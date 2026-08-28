<?php
/**
 * Olobuild_Renderer_Structure_Trait — render dei nodi struttura: sezioni, righe (+loop), colonne, colonne interne, floating panel.
 *
 * Estratto verbatim da class-frontend-renderer.php (dieta monoliti v1.4.390):
 * stessi metodi, stessa visibilita', zero cambi alle chiamate ($this/self
 * risolvono nella classe che usa il trait).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Olobuild_Renderer_Structure_Trait {
    /**
     * Render a Section container using UIkit classes.
     */
    private function render_section_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        // Floating panel bypass: if section contains only a floatingpanel (inside row>column),
        // render the floatingpanel directly without section/row/column wrappers to avoid empty gap.
        // Skipped in builder mode: bypass would lose data-olo-tile-id for the floatingpanel
        // (it would inherit the section's id instead), breaking drop-target hit-testing.
        if ( ! $this->builder_mode && $this->section_has_only_floatingpanel( $node ) ) {
            return $this->extract_and_render_floatingpanel( $node, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $s = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        // Sticky effect
        $sticky_effect = $s['sticky_effect'] ?? 'none';
        $is_sticky_v = in_array( $sticky_effect, [ 'cover', 'reveal' ], true );
        $is_sticky_h = in_array( $sticky_effect, [ 'cover-h', 'reveal-h' ], true );
        $is_sticky = $is_sticky_v || $is_sticky_h;
        $sticky_top = intval( $s['sticky_top'] ?? 0 );

        // Scroll snap
        $scroll_snap = ! empty( $s['scroll_snap'] );
        $snap_dots   = $scroll_snap && ! empty( $s['snap_dots'] );

        // Section classes — position-relative needed for absolute-positioned children
        // Sticky sections use position:sticky instead (handled via CSS class)
        $classes = [ 'uk-section' ];
        if ( $is_sticky_v ) {
            $classes[] = 'olo-sticky-' . $sticky_effect;
        } elseif ( $is_sticky_h ) {
            $classes[] = 'olo-sticky-' . $sticky_effect;
        } else {
            $classes[] = 'uk-position-relative';
        }
        $section_style = $s['style'] ?? 'default';
        $style_map = [
            'muted'     => 'uk-section-muted',
            'primary'   => 'uk-section-primary',
            'secondary' => 'uk-section-secondary',
        ];
        if ( isset( $style_map[ $section_style ] ) ) {
            $classes[] = $style_map[ $section_style ];
        } else {
            $classes[] = 'uk-section-default';
        }

        // Padding
        $padding = $s['padding'] ?? 'default';
        $padding_map = [
            'small'           => 'uk-section-small',
            'large'           => 'uk-section-large',
            'xlarge'          => 'uk-section-xlarge',
            'remove-vertical' => 'uk-padding-remove-vertical',
        ];
        if ( isset( $padding_map[ $padding ] ) ) {
            $classes[] = $padding_map[ $padding ];
        }

        // Custom CSS classes
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // Sticky top offset (inline overrides CSS top:0) — only for vertical sticky
        $inline_styles = [];
        if ( $is_sticky_v && $sticky_top > 0 ) {
            $inline_styles[] = "top: {$sticky_top}px";
        }

        // Padding verticale "Personalizzato (px)": valori espliciti sopra/sotto,
        // vincono sul default di .uk-section via inline style.
        if ( 'custom' === $padding ) {
            $pt = max( 0, intval( $s['padding_top_custom'] ?? 70 ) );
            $pb = max( 0, intval( $s['padding_bottom_custom'] ?? 70 ) );
            $inline_styles[] = 'padding-top: ' . $pt . 'px';
            $inline_styles[] = 'padding-bottom: ' . $pb . 'px';
        }

        // Background handling
        // Il field `bg` (type=background) di section è dichiarato in `fields[]` (settings),
        // quindi BuilderInspector lo salva via updateSetting → finisce in $s['bg'], NON in
        // $style['bg']. Il render storicamente leggeva solo da $style: l'utente impostava
        // un colore alla section ma non lo vedeva mai applicato. Fallback su settings.
        $tile_bg = $this->css->get_effective_bg( $style );
        if ( ( $tile_bg['type'] ?? 'none' ) === 'none' && ! empty( $s['bg']['type'] ) && $s['bg']['type'] !== 'none' ) {
            $tile_bg = $this->css->get_effective_bg( [ 'bg' => $s['bg'] ] );
        }
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        if ( $has_bg_image || $has_bg_video || $has_bg_gallery ) {
            $classes[] = 'uk-position-relative';
            $inline_styles[] = 'overflow: clip';
        } elseif ( $tile_bg['type'] !== 'none' ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Marker class: preserva il padding-top della prima section quando ha un
        // background, altrimenti la regola "classic header gap collapse" in frontend.css
        // azzera lo spazio sopra il contenuto e taglia il riquadro colorato.
        if ( $has_bg_any ) {
            $classes[] = 'olo-section-has-bg';
        }

        // Video cover height
        if ( $has_bg_video && ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) {
            $inline_styles[] = 'min-height: ' . intval( $tile_bg['cover_height'] ) . 'px';
        }

        // Shadow CLASS — section applica sempre (no branch has_bg_any come element).
        if ( ! empty( $style['shadow'] ) ) {
            $uk_shadow_map = [
                'sm' => 'uk-box-shadow-small',
                'md' => 'uk-box-shadow-medium',
                'lg' => 'uk-box-shadow-large',
                'xl' => 'uk-box-shadow-xlarge',
            ];
            if ( isset( $uk_shadow_map[ $style['shadow'] ] ) ) {
                $classes[] = $uk_shadow_map[ $style['shadow'] ];
            }
        }

        // Helper unificato: margin/padding/border-radius/border/opacity/flex/transform/
        // box-shadow inline/text-shadow/backdrop/overflow/dimensions/mask/custom_css/position.
        $this->apply_common_box_styles( $inline_styles, $style, $s, $advanced );

        // CSS Grid layout (overrides flex se layout_mode=grid) — section-specific.
        $grid_css = $this->css->build_css_grid_css( $s );
        foreach ( $grid_css as $decl ) {
            $inline_styles[] = $decl;
        }

        // overflow:clip per border-radius clipping — section/row hanno questa forzatura
        // perché altrimenti il bg overflow esce dal rounded corner. (clip preserva sticky)
        if ( ! empty( $style['border_radius'] ) ) {
            $inline_styles[] = 'overflow: clip';
        }

        // HTML ID (always generate for hover CSS support)
        $tile_counter++;
        $css_id  = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'ms-' . $template_id . '-' . $tile_counter;
        $id_attr = ' id="' . esc_attr( $css_id ) . '"';

        // Hover CSS rules
        $this->collect_hover_css( $style, $css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $css_id, $advanced );

        // Infinite animation
        $inf_anim_css = $this->css->build_infinite_animation_css( $s, $css_id );
        if ( $inf_anim_css ) {
            $hover_css_rules[] = $inf_anim_css;
        }

        // Custom CSS per sezione (campo settings.custom_css)
        $this->collect_custom_css( $s, $css_id, $hover_css_rules );

        // Scroll snap: add full-screen height + snap alignment
        if ( $scroll_snap ) {
            $inline_styles[] = 'height: 100vh';
            $inline_styles[] = 'scroll-snap-align: start';
            $inline_styles[] = 'box-sizing: border-box';
        }

        // Entrance animation (olo-entrance-*)
        $entrance = $s['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            $classes[] = 'olo-visible'; // applicata subito: l'animation parte al page-load (no IntersectionObserver dependency)
            // Override CSS variables per durata/delay/easing custom (via field inspector)
            $e_dur = intval( $s['entrance_duration'] ?? 0 );
            if ( $e_dur > 0 ) $inline_styles[] = '--olo-e-dur: ' . max( 50, min( 5000, $e_dur ) ) . 'ms';
            $e_delay = intval( $s['entrance_delay'] ?? 0 );
            if ( $e_delay > 0 ) $inline_styles[] = '--olo-e-delay: ' . min( 5000, $e_delay ) . 'ms';
            $e_ease = $s['entrance_easing'] ?? 'auto';
            if ( $e_ease && $e_ease !== 'auto' ) {
                // Whitelist: keyword o cubic-bezier
                if ( preg_match( '/^(linear|ease|ease-in|ease-out|ease-in-out|cubic-bezier\([0-9.,\s\-]+\))$/', $e_ease ) ) {
                    $inline_styles[] = '--olo-e-ease: ' . $e_ease;
                }
            }
            $e_int = floatval( $s['entrance_intensity'] ?? 1 );
            if ( $e_int > 0 && abs( $e_int - 1 ) > 0.01 ) {
                $e_int = max( 0.1, min( 5, $e_int ) );
                $inline_styles[] = '--olo-e-int: ' . $e_int;
            }
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $classes[] = 'olo-stagger-parent';
                $inline_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes
        $scrollspy_attr = $this->anim->build_scrollspy_attr( $advanced );
        $el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );
        $mouse_attrs = $this->anim->build_mouse_attrs( $advanced );

        // Infinite animation & mask (inline style for elements)
        $inf_anim_css = $this->anim->build_inline_animation_css( $advanced );
        if ( $inf_anim_css ) $inline_styles[] = $inf_anim_css;
        $mask_css = $this->anim->build_inline_mask_css( $advanced );
        if ( $mask_css ) $inline_styles[] = $mask_css;

        // Snap dots data attributes
        $snap_data_attr = '';
        if ( $snap_dots ) {
            $dot_color        = sanitize_hex_color( $s['snap_dot_color'] ?? '' ) ?: '#ffffff';
            $dot_active_color = sanitize_hex_color( $s['snap_dot_active_color'] ?? '' );
            $dot_position     = ( $s['snap_dot_position'] ?? 'right' ) === 'left' ? 'left' : 'right';
            $snap_data_attr   = ' data-olo-snap-section';
            $snap_data_attr  .= ' data-snap-dot-color="' . esc_attr( $dot_color ) . '"';
            if ( $dot_active_color ) {
                $snap_data_attr .= ' data-snap-dot-active="' . esc_attr( $dot_active_color ) . '"';
            }
            $snap_data_attr .= ' data-snap-dot-pos="' . esc_attr( $dot_position ) . '"';
        }

        // Decide where to place bg/overlay layers: full section (default) or inside container.
        // bg_scope='container' keeps the bg/overlay limited to the container max-width
        // (useful when 'width' = default/small/etc. and the user doesn't want edge-to-edge bg).
        // v1.0.78 — default 'container' (Centrata): la sezione rispetta la larghezza contenuto
        // scelta dall'utente. Dati legacy senza bg_scope vengono trattati come Centrata.
        $bg_scope = ( $s['bg_scope'] ?? 'container' ) === 'section' ? 'section' : 'container';
        $has_any_bg = ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay );

        // ─── Section outer max-width ─────────────────────────────────────
        // Quando bg_scope='container' E width != fullbleed/expand, anche la `<section>`
        // esterna viene limitata in larghezza con `max-width` + `margin: 0 auto`.
        // Senza questo, il colore/gradiente di sfondo era sempre bordo-a-bordo perché
        // applicato come inline style sull'outer `<section>`, mentre solo il container
        // interno seguiva il width semantico. Risultato per l'utente: scegliendo
        // "Piccolo" o "Grande" la section sembrava sempre uguale (full viewport).
        $width_for_outer = $s['width'] ?? $s['section_width'] ?? 'default';
        $outer_max_width_map = [
            'small'   => 900,
            'default' => 1200,
            'large'   => 1400,
            'xlarge'  => 1600,
        ];
        if ( $bg_scope === 'container' && isset( $outer_max_width_map[ $width_for_outer ] ) ) {
            $inline_styles[] = 'max-width: ' . $outer_max_width_map[ $width_for_outer ] . 'px';
            $inline_styles[] = 'margin-left: auto';
            $inline_styles[] = 'margin-right: auto';
        }

        $html = '<section role="region" class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $id_attr;
        if ( $inline_styles ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        // Colore luce per la tile "Luce di pagina" (atmosfera): la sezione
        // dichiara il colore, il layer fisso della tile lo segue allo scroll.
        $light_color = $this->sanitize_light_color( $s['light_color'] ?? '' );
        if ( $light_color ) {
            $html .= ' data-olo-light="' . esc_attr( $light_color ) . '"';
        }
        $html .= $scrollspy_attr . $el_parallax_attr . $snap_data_attr . $mouse_attrs . $this->anim->build_spotlight_attr( $advanced ) . '>';

        $bg_layers_html = '';
        if ( $has_bg_image ) {
            $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
            $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );
            $bg_layers_html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $tile_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
            $bg_layers_html .= $this->anim->build_uk_parallax_attr( $tile_bg );
            $bg_layers_html .= '></div>';
        }
        if ( $has_bg_video ) {
            $vid_url    = esc_url( $tile_bg['video_url'] );
            $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
            $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
            $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
            $vid_cover  = ( ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) ? intval( $tile_bg['cover_height'] ) : 0;
            $vid_scale  = ( ! empty( $tile_bg['video_scale'] ) && intval( $tile_bg['video_scale'] ) > 100 ) ? intval( $tile_bg['video_scale'] ) / 100 : 0;
            $scale_css  = $vid_scale ? '; transform: scale(' . $vid_scale . '); transform-origin: ' . $vid_pos : '';
            if ( $vid_cover ) {
                $bg_layers_html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: ' . $vid_cover . 'px; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            } else {
                $bg_layers_html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
            }
            if ( $vid_poster ) $bg_layers_html .= ' poster="' . $vid_poster . '"';
            // Parallax di SOLO sfondo anche per il VIDEO (scale/blur/opacity): trasforma il
            // layer video senza toccare il contenuto della sezione — a differenza del parallax
            // di sezione (element parallax) che trasforma l'intero <section> figli inclusi.
            // bgx/bgy sono no-op su <video> (non hanno background-position).
            $bg_layers_html .= $this->anim->build_uk_parallax_attr( $tile_bg );
            $bg_layers_html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
        }
        if ( $has_bg_gallery ) {
            $bg_layers_html .= $this->render_bg_gallery( $tile_bg );
        }
        if ( $has_overlay ) {
            $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
            $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
            $bg_layers_html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none" aria-hidden="true"></div>';
        }

        // bg_scope=section: emit bg layers as siblings of the container (full edge-to-edge bg)
        if ( $has_any_bg && $bg_scope === 'section' ) {
            $html .= $bg_layers_html;
        }

        // Container width wrapper (relative for z-index above bg/overlay)
        $width = $s['width'] ?? $s['section_width'] ?? 'default';

        if ( $width === 'fullbleed' ) {
            // Edge-to-edge: no uk-container, no padding
            $container_class = 'olo-section-fullbleed';
        } else {
            $container_class = 'uk-container';
            $width_map = [
                'small'  => 'uk-container-small',
                'large'  => 'uk-container-large',
                'xlarge' => 'uk-container-xlarge',
                'expand' => 'uk-container-expand',
            ];
            if ( isset( $width_map[ $width ] ) ) {
                $container_class .= ' ' . $width_map[ $width ];
            }
        }

        if ( $has_any_bg ) {
            $container_class .= ' uk-position-relative';
            $html .= '<div class="' . esc_attr( $container_class ) . '" style="z-index: 1">';
        } else {
            $html .= '<div class="' . esc_attr( $container_class ) . '">';
        }

        // bg_scope=container: emit bg layers INSIDE the container (limited to container width).
        // Wrap them so they align with the content-box (excluding container padding) — otherwise
        // uk-position-cover would extend through the container padding and look wider than uk-grid content.
        if ( $has_any_bg && $bg_scope === 'container' ) {
            $html .= '<div class="olo-bg-in-container">' . $bg_layers_html . '</div>';
        }

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div></section>';

        // Reveal sections: wrap in a container that limits the sticky range.
        // JS will set wrapper height = 2×section height and margin-top = -section height
        // so the section sits behind the previous one and unsticks once fully revealed.
        if ( $sticky_effect === 'reveal' ) {
            $html = '<div class="olo-reveal-wrapper" data-sticky-top="' . $sticky_top . '">' . $html . '</div>';
        }

        // Horizontal sticky: add data attribute for JS grouping
        if ( $is_sticky_h ) {
            // Mark section with data for JS to build the horizontal scroll group
            $html = '<div class="olo-h-marker" data-sticky-h="' . esc_attr( $sticky_effect ) . '" data-sticky-top="' . $sticky_top . '" style="display:contents">' . $html . '</div>';
        }

        return $html;
    }

    /**
     * Colore CSS "sicuro" per data-olo-light: hex, rgb(a), hsl(a) o var(--…).
     */
    private function sanitize_light_color( $value ) {
        $v = trim( (string) $value );
        if ( $v === '' ) return '';
        if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $v ) ) return $v;
        if ( preg_match( '/^(rgb|rgba|hsl|hsla)\([\d\s.,%\/]+\)$/', $v ) ) return $v;
        if ( preg_match( '/^var\(\s*--[\w-]+(?:\s*,\s*[^;{}<>]+)?\)$/', $v ) ) return $v;
        return '';
    }

    /**
     * Render a Row using UIkit grid.
     */
    private function render_row_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];
        $gap    = absint( $s['gap'] ?? 16 );
        $valign = $s['vertical_align'] ?? 'stretch';
        $stack        = ! empty( $s['stack_mobile'] );
        $stack_tablet = ! empty( $s['stack_tablet'] );

        // Background handling — vedi commento in render_section_node: fallback su $s['bg'].
        $tile_bg      = $this->css->get_effective_bg( $style );
        if ( ( $tile_bg['type'] ?? 'none' ) === 'none' && ! empty( $s['bg']['type'] ) && $s['bg']['type'] !== 'none' ) {
            $tile_bg = $this->css->get_effective_bg( [ 'bg' => $s['bg'] ] );
        }
        $has_bg_image   = ( $tile_bg['type'] === 'image' && ! empty( $tile_bg['image_url'] ) );
        $has_bg_video   = ( $tile_bg['type'] === 'video' && ! empty( $tile_bg['video_url'] ) );
        $has_bg_gallery = ( $tile_bg['type'] === 'gallery' && ! empty( $tile_bg['gallery_images'] ) && is_array( $tile_bg['gallery_images'] ) );
        $has_bg_any     = ( $tile_bg['type'] !== 'none' );
        $has_overlay    = ( $has_bg_any && ! empty( $tile_bg['overlay_opacity'] ) && intval( $tile_bg['overlay_opacity'] ) > 0 );

        // Row spacing/decorations — apply_flex=false: il flex va sul <div uk-grid>
        // interno (vedi $row_flex_styles più sotto), non sul wrapper esterno.
        // Pre-calc $pos_mode: ci serve per $has_positioning (riga successiva).
        $pos_mode = $advanced['position_mode'] ?? 'static';
        $row_spacing_styles = [];
        $this->apply_common_box_styles( $row_spacing_styles, $style, $s, $advanced, [ 'apply_flex' => false ] );

        // Video cover height — row/section-specific (l'helper non gestisce bg layers).
        if ( $has_bg_video && ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) {
            $row_spacing_styles[] = 'min-height: ' . intval( $tile_bg['cover_height'] ) . 'px';
        }

        // Wrapper for row background or spacing
        $has_border_radius = ! empty( $style['border_radius'] );
        $has_border = $this->wrapper_has_border( $style );
        $has_opacity = ! empty( $style['opacity'] ) && intval( $style['opacity'] ) < 100;
        $has_shadow = ! empty( $style['shadow'] );
        $has_spacing = ! empty( $row_spacing_styles );
        $has_positioning = $pos_mode && $pos_mode !== 'static';
        $has_hover   = ! empty( $style['hover'] ) && is_array( $style['hover'] ) && array_filter( $style['hover'], function( $v ) { return $v !== null && $v !== '' && $v !== false; } );
        $needs_wrapper = $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay || ( $tile_bg['type'] !== 'none' ) || $has_spacing || $has_border_radius || $has_border || $has_opacity || $has_shadow || $has_hover || $has_positioning;

        // ID for hover CSS support
        $tile_counter++;
        $row_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mr-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $row_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $row_css_id, $advanced );

        // Custom CSS per riga (campo settings.custom_css)
        $this->collect_custom_css( $s, $row_css_id, $hover_css_rules );

        $wrapper_styles = [];
        $wrapper_classes = [];

        if ( $needs_wrapper ) {
            if ( $has_bg_image || $has_bg_video || $has_bg_gallery ) {
                $wrapper_classes[] = 'uk-position-relative';
                $wrapper_styles[] = 'overflow: clip';
            } elseif ( $tile_bg['type'] !== 'none' ) {
                $bg_css = $this->css->get_bg_inline_css( $tile_bg );
                if ( $bg_css ) $wrapper_styles[] = $bg_css;
            }
            if ( $has_spacing ) {
                $wrapper_styles = array_merge( $wrapper_styles, $row_spacing_styles );
            }
            // Shadow class on wrapper
            if ( $has_shadow ) {
                $uk_shadow_map = [
                    'sm' => 'uk-box-shadow-small',
                    'md' => 'uk-box-shadow-medium',
                    'lg' => 'uk-box-shadow-large',
                    'xl' => 'uk-box-shadow-xlarge',
                ];
                if ( isset( $uk_shadow_map[ $style['shadow'] ] ) ) {
                    $wrapper_classes[] = $uk_shadow_map[ $style['shadow'] ];
                }
            }
            // Overflow clip for border-radius clipping (clip instead of hidden to preserve sticky)
            if ( $has_border_radius ) {
                $wrapper_styles[] = 'overflow: clip';
            }
            if ( ! empty( $advanced['custom_css'] ) ) {
                $wrapper_styles[] = $this->safe_inline_css( $advanced['custom_css'] );
            }
        }

        // UIkit grid classes
        $classes = [];

        // Gap mapping to UIkit column-gap
        $gap_map = [
            0  => 'uk-grid-collapse',
            4  => 'uk-grid-small',
            8  => 'uk-grid-small',
            16 => '', // default gap
            24 => 'uk-grid-medium',
            32 => 'uk-grid-medium',
            48 => 'uk-grid-large',
        ];
        // Find closest gap
        $closest_gap = '';
        $min_diff = PHP_INT_MAX;
        foreach ( $gap_map as $g => $cls ) {
            $diff = abs( $gap - $g );
            if ( $diff < $min_diff ) {
                $min_diff = $diff;
                $closest_gap = $cls;
            }
        }
        if ( $closest_gap ) $classes[] = $closest_gap;

        // Vertical alignment
        $valign_map = [
            'start'  => 'uk-flex-top',
            'center' => 'uk-flex-middle',
            'end'    => 'uk-flex-bottom',
        ];
        if ( isset( $valign_map[ $valign ] ) ) {
            $classes[] = $valign_map[ $valign ];
        }

        // Custom class will be added after we know it
        $pre_class_attr_classes = $classes;

        // No-stack class for mobile
        $nostack_class = '';
        if ( ! $stack ) {
            $nostack_class = 'olo-nostack-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
            $classes[] = $nostack_class;
            $pre_class_attr_classes[] = $nostack_class;
        }

        // uk-grid attribute with options
        $grid_opts = [];
        if ( $stack ) {
            $grid_opts[] = 'margin: uk-margin-small-top';
        }
        $uk_grid = 'uk-grid';

        $html = '';

        // No-stack CSS: prevent columns from stacking on mobile
        if ( ! $stack && $nostack_class ) {
            $html .= '<style>';
            $html .= '.' . $nostack_class . '{flex-wrap:nowrap!important}';
            $html .= '.' . $nostack_class . '>*{flex:1 1 auto}';
            $html .= '.' . $nostack_class . '>[class*="uk-width-expand"]{flex:1 1 0%}';
            $html .= '</style>';
        }

        // Stack on tablet: force columns to 100% width between 960px and 1200px
        if ( $stack_tablet ) {
            $stack_tab_class = $nostack_class ?: ( 'olo-nostack-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 ) );
            if ( ! $nostack_class ) {
                $classes[] = $stack_tab_class;
                $pre_class_attr_classes[] = $stack_tab_class;
            }
            $html .= '<style>';
            $html .= '@container olo-tpl (max-width:1199px){';
            $html .= '.' . $stack_tab_class . '{flex-wrap:wrap!important}';
            $html .= '.' . $stack_tab_class . '>*{width:100%!important;flex:0 0 100%!important}';
            $html .= '}';
            $html .= '</style>';
        }

        // Custom widths: generate scoped <style> block
        $is_custom_layout = ( ( $s['layout'] ?? '' ) === 'custom' && ! empty( $s['custom_widths'] ) );
        $custom_class = '';
        if ( $is_custom_layout ) {
            $custom_id = substr( md5( ( $node['id'] ?? '' ) . $s['custom_widths'] ), 0, 8 );
            $custom_class = 'olo-cw-' . $custom_id;
            $widths = array_filter( array_map( 'floatval', explode( ',', $s['custom_widths'] ) ), function( $v ) { return $v > 0; } );
            if ( ! empty( $widths ) ) {
                $html .= '<style>';
                // When nostack is active, apply custom widths at ALL breakpoints
                if ( ! $stack ) {
                    foreach ( $widths as $i => $w ) {
                        $nth = $i + 1;
                        $html .= '.' . $custom_class . '>:nth-child(' . $nth . '){width:' . $w . '%!important}';
                    }
                } else {
                    $html .= '@container olo-tpl (min-width:960px){';
                    foreach ( $widths as $i => $w ) {
                        $nth = $i + 1;
                        $html .= '.' . $custom_class . '>:nth-child(' . $nth . '){width:' . $w . '%!important}';
                    }
                    $html .= '}';
                }
                $html .= '</style>';
            }
        }

        // Build class attribute for grid div (after custom class is known)
        if ( $custom_class ) {
            $pre_class_attr_classes[] = $custom_class;
        }
        $class_attr = ! empty( $pre_class_attr_classes ) ? ' class="' . esc_attr( implode( ' ', $pre_class_attr_classes ) ) . '"' : '';

        // Entrance animation (olo-entrance-*) for row
        $entrance = $s['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $wrapper_classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            $wrapper_classes[] = 'olo-visible'; // applicata subito (no IntersectionObserver dependency)
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $wrapper_classes[] = 'olo-stagger-parent';
                $wrapper_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes for row
        $row_scrollspy_attr = $this->anim->build_scrollspy_attr( $advanced );
        $row_el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );
        $row_mouse_attrs = $this->anim->build_mouse_attrs( $advanced );
        $row_spotlight_attr = $this->anim->build_spotlight_attr( $advanced );
        // Tutti gli attributi-effetto della row in un'unica stringa: vanno sul
        // wrapper quando esiste, altrimenti direttamente sul nodo griglia
        // (prima i mouse attrs venivano calcolati ma mai stampati, e le row
        // senza wrapper perdevano anche lo spotlight).
        $row_fx_attrs = $row_scrollspy_attr . $row_el_parallax_attr . $row_mouse_attrs . $row_spotlight_attr;

        // Open row wrapper (for background)
        if ( $needs_wrapper ) {
            $html .= '<div id="' . esc_attr( $row_css_id ) . '" class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '"';
            if ( $wrapper_styles ) {
                $html .= ' style="' . esc_attr( implode( '; ', $wrapper_styles ) ) . '"';
            }
            $html .= $row_fx_attrs . '>';

            // Background image layer (with optional UIkit parallax)
            if ( $has_bg_image ) {
                $bg_size = esc_attr( $tile_bg['image_size'] ?? 'cover' );
                $bg_pos  = esc_attr( $tile_bg['image_position'] ?? 'center center' );

                $html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $tile_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
                $html .= $this->anim->build_uk_parallax_attr( $tile_bg );
                $html .= '></div>';
            }

            // Video background layer
            if ( $has_bg_video ) {
                $vid_url    = esc_url( $tile_bg['video_url'] );
                $vid_poster = ! empty( $tile_bg['video_poster'] ) ? esc_url( $tile_bg['video_poster'] ) : '';
                $vid_pos    = esc_attr( $tile_bg['image_position'] ?? 'center center' );
                $vid_fit    = esc_attr( $tile_bg['video_fit'] ?? 'cover' );
                $vid_cover  = ( ! empty( $tile_bg['cover_height'] ) && intval( $tile_bg['cover_height'] ) > 0 ) ? intval( $tile_bg['cover_height'] ) : 0;
                $vid_scale  = ( ! empty( $tile_bg['video_scale'] ) && intval( $tile_bg['video_scale'] ) > 100 ) ? intval( $tile_bg['video_scale'] ) / 100 : 0;
                $scale_css  = $vid_scale ? '; transform: scale(' . $vid_scale . '); transform-origin: ' . $vid_pos : '';
                if ( $vid_cover ) {
                    $html .= '<video style="position: absolute; top: 0; left: 0; width: 100%; height: ' . $vid_cover . 'px; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
                } else {
                    $html .= '<video style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none' . $scale_css . '" autoplay muted loop playsinline';
                }
                if ( $vid_poster ) $html .= ' poster="' . $vid_poster . '"';
                $html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
            }

            // Gallery background slideshow (row)
            if ( $has_bg_gallery ) {
                $html .= $this->render_bg_gallery( $tile_bg );
            }

            // Overlay layer
            if ( $has_overlay ) {
                $ov_color   = esc_attr( $tile_bg['overlay_color'] ?? '#000000' );
                $ov_opacity = intval( $tile_bg['overlay_opacity'] ) / 100;
                $html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none"></div>';
            }
        }

        // Flex container overrides for the row grid (direction, justify, align, wrap, gap).
        // Helper unificato — un eventuale `display: flex` aggiuntivo è no-op perché
        // .uk-grid ce l'ha già; gli altri decls (flex-direction/justify-content/...)
        // sono i veri override.
        $row_flex_styles = $this->css->build_flex_container_css( $s );

        // Grid — if no wrapper, put scrollspy/parallax on the grid div itself
        $grid_extra_attrs = $needs_wrapper ? '' : $row_fx_attrs;
        $grid_style_parts = $row_flex_styles;
        if ( $needs_wrapper && ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) ) {
            $grid_style_parts[] = 'position: relative';
            $grid_style_parts[] = 'z-index: 1';
        }
        // === CSS Grid mode ===
        $is_css_grid = ( ( $s['layout_mode'] ?? '' ) === 'grid' );
        if ( $is_css_grid ) {
            $grid_css_parts = [];
            $grid_css_parts[] = 'display: grid';
            if ( ! empty( $s['grid_columns'] ) ) {
                $grid_css_parts[] = 'grid-template-columns: ' . esc_attr( $s['grid_columns'] );
            }
            if ( ! empty( $s['grid_rows'] ) ) {
                $grid_css_parts[] = 'grid-template-rows: ' . esc_attr( $s['grid_rows'] );
            }
            // Separate column/row gaps or unified gap
            $g_col_gap = $s['grid_column_gap'] ?? '';
            $g_row_gap = $s['grid_row_gap'] ?? '';
            if ( $g_col_gap !== '' && $g_row_gap !== '' ) {
                $grid_css_parts[] = 'column-gap: ' . intval( $g_col_gap ) . 'px';
                $grid_css_parts[] = 'row-gap: ' . intval( $g_row_gap ) . 'px';
            } elseif ( $g_col_gap !== '' ) {
                $grid_css_parts[] = 'column-gap: ' . intval( $g_col_gap ) . 'px';
                $grid_css_parts[] = 'row-gap: ' . $gap . 'px';
            } elseif ( $g_row_gap !== '' ) {
                $grid_css_parts[] = 'column-gap: ' . $gap . 'px';
                $grid_css_parts[] = 'row-gap: ' . intval( $g_row_gap ) . 'px';
            } else {
                $grid_css_parts[] = 'gap: ' . $gap . 'px';
            }
            // Grid auto-flow (direction + density)
            $g_auto_flow = $s['grid_auto_flow'] ?? 'row';
            if ( ! empty( $s['grid_auto_flow_dense'] ) ) {
                $g_auto_flow .= ' dense';
            }
            if ( $g_auto_flow !== 'row' ) {
                $grid_css_parts[] = 'grid-auto-flow: ' . esc_attr( $g_auto_flow );
            }
            // Justify content
            $g_jc = $s['grid_justify_content'] ?? '';
            if ( $g_jc && $g_jc !== 'stretch' ) {
                $grid_css_parts[] = 'justify-content: ' . esc_attr( $g_jc );
            }
            // Align items
            $g_ai = $s['grid_align_items'] ?? $valign;
            if ( $g_ai && $g_ai !== 'stretch' ) {
                $grid_css_parts[] = 'align-items: ' . esc_attr( $g_ai );
            }
            // Align content
            $g_ac = $s['grid_align_content'] ?? '';
            if ( $g_ac && $g_ac !== 'stretch' ) {
                $grid_css_parts[] = 'align-content: ' . esc_attr( $g_ac );
            }
            if ( $needs_wrapper && ( $has_bg_image || $has_bg_video || $has_bg_gallery || $has_overlay ) ) {
                $grid_css_parts[] = 'position: relative';
                $grid_css_parts[] = 'z-index: 1';
            }
            $grid_extra_attrs = $needs_wrapper ? '' : $row_fx_attrs;
            $grid_class_list = [];
            if ( $stack ) $grid_class_list[] = 'olo-grid-stack';
            $grid_class_attr = ! empty( $grid_class_list ) ? ' class="' . esc_attr( implode( ' ', $grid_class_list ) ) . '"' : '';
            $html .= '<div' . $grid_class_attr . ' style="' . esc_attr( implode( '; ', $grid_css_parts ) ) . '"' . $grid_extra_attrs . '>';

            // Loop mode: repeat children for each post from WP_Query
            $loop_enabled = ! empty( $s['loop_enabled'] );
            $loop_pagination_html = '';
            if ( $loop_enabled ) {
                $row_id_short = substr( md5( $node['id'] ?? wp_rand() ), 0, 8 );
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per paginazione del Row Loop; nessuna modifica di stato; valore forzato a intero.
                $current_page = isset( $_GET[ 'olo_p_' . $row_id_short ] ) ? max( 1, intval( wp_unslash( $_GET[ 'olo_p_' . $row_id_short ] ) ) ) : 1;
                $loop_query = $this->run_row_loop_query( $s, $current_page, true );
                $html .= $this->render_row_loop_children( $node['children'] ?? [], $loop_query->posts, $manager, $template_id, $hover_css_rules, $tile_counter, true );
                $loop_pagination_html = $this->render_row_loop_pagination( $s, $current_page, intval( $loop_query->max_num_pages ), $row_id_short );
                // Marca il container della row con data-olo-loop-row così il JS Load More
                // sa dove appendere i nuovi children (li appende al wrapper interno).
                if ( ( $s['loop_pagination'] ?? 'none' ) === 'load_more' ) {
                    $html = preg_replace(
                        '/<div(' . preg_quote( $grid_class_attr, '/' ) . ')/',
                        '<div data-olo-loop-row-container="' . esc_attr( $row_id_short ) . '" data-olo-loop-template-id="' . intval( $template_id ) . '"$1',
                        $html, 1
                    );
                }
            } else {
                foreach ( $node['children'] ?? [] as $child ) {
                    $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter, true );
                }
            }

            $html .= '</div>';
            $html .= $loop_pagination_html;

            // Stack on mobile: override grid to 1 column
            if ( $stack ) {
                $grid_id = 'olo-g-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
                $html = str_replace( '<div' . $grid_class_attr, '<div class="' . esc_attr( trim( implode( ' ', $grid_class_list ) . ' ' . $grid_id ) ) . '"', $html );
                $bp_mobile = intval( $this->breakpoints['tablet'] ?? 960 );
                $html .= '<style>@media(max-width:' . $bp_mobile . 'px){.' . $grid_id . '{grid-template-columns:1fr!important;grid-template-rows:auto!important}.' . $grid_id . '>*{grid-column:auto!important;grid-row:auto!important}}</style>';
            }
        } else {
            // === Classic Flexbox mode ===
            $grid_style_attr = ! empty( $grid_style_parts ) ? ' style="' . esc_attr( implode( '; ', $grid_style_parts ) ) . '"' : '';
            $grid_extra_attrs = $needs_wrapper ? '' : $row_fx_attrs;
            $html .= '<div' . $class_attr . ' ' . $uk_grid . $grid_style_attr . $grid_extra_attrs . '>';

            // Loop mode: repeat children for each post from WP_Query
            $loop_enabled_flex = ! empty( $s['loop_enabled'] );
            $loop_pagination_html_flex = '';
            if ( $loop_enabled_flex ) {
                $row_id_short_flex = substr( md5( $node['id'] ?? wp_rand() ), 0, 8 );
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only per paginazione del Row Loop; nessuna modifica di stato; valore forzato a intero.
                $current_page_flex = isset( $_GET[ 'olo_p_' . $row_id_short_flex ] ) ? max( 1, intval( wp_unslash( $_GET[ 'olo_p_' . $row_id_short_flex ] ) ) ) : 1;
                $loop_query_flex   = $this->run_row_loop_query( $s, $current_page_flex, true );
                $html .= $this->render_row_loop_children( $node['children'] ?? [], $loop_query_flex->posts, $manager, $template_id, $hover_css_rules, $tile_counter, false );
                $loop_pagination_html_flex = $this->render_row_loop_pagination( $s, $current_page_flex, intval( $loop_query_flex->max_num_pages ), $row_id_short_flex );
                // Marca il container per il Load More JS
                if ( ( $s['loop_pagination'] ?? 'none' ) === 'load_more' ) {
                    $html = preg_replace(
                        '/<div(' . preg_quote( $class_attr, '/' ) . ' ' . preg_quote( $uk_grid, '/' ) . ')/',
                        '<div data-olo-loop-row-container="' . esc_attr( $row_id_short_flex ) . '" data-olo-loop-template-id="' . intval( $template_id ) . '"$1',
                        $html, 1
                    );
                }
            } else {
                foreach ( $node['children'] ?? [] as $child ) {
                    $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
                }
            }

            $html .= '</div>';
            $html .= $loop_pagination_html_flex;
        }

        // Close row wrapper
        if ( $needs_wrapper ) {
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Build and run a WP_Query for row loop mode.
     *
     * Reso PUBBLICO per consentire al REST endpoint Load More di riusare
     * la stessa logica di costruzione args.
     *
     * @param array $s            Row settings containing loop_* keys.
     * @param int   $current_page Pagina corrente (1-based) per la paginazione.
     * @param bool  $return_query Se true ritorna l'oggetto WP_Query invece dei soli posts.
     * @return WP_Post[]|WP_Query  Array di post objects (default) oppure l'intero WP_Query.
     */
    public function run_row_loop_query( $s, $current_page = 1, $return_query = false ) {
        $post_type = sanitize_key( $s['loop_post_type'] ?? 'post' );
        if ( ! post_type_exists( $post_type ) ) {
            $post_type = 'post';
        }

        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => absint( $s['loop_posts_per_page'] ?? 6 ),
            'orderby'        => sanitize_key( $s['loop_orderby'] ?? 'date' ),
            'order'          => strtoupper( $s['loop_order'] ?? 'DESC' ) === 'ASC' ? 'ASC' : 'DESC',
            'post_status'    => 'publish',
            'paged'          => max( 1, intval( $current_page ) ),
        ];

        // Offset
        $offset = absint( $s['loop_offset'] ?? 0 );
        if ( $offset > 0 ) {
            $args['offset'] = $offset;
        }

        // Exclude current post
        if ( ! empty( $s['loop_exclude_current'] ) ) {
            $current_id = get_the_ID();
            if ( $current_id ) {
                // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- esclusione post necessaria alla funzione del tile; query a volume limitato
                $args['post__not_in'] = [ $current_id ];
            }
        }

        // Taxonomy include filter
        $taxonomy  = sanitize_text_field( $s['loop_taxonomy'] ?? '' );
        $terms_str = sanitize_text_field( $s['loop_terms'] ?? '' );
        $tax_query = [];
        if ( $taxonomy !== '' ) {
            if ( $terms_str !== '' ) {
                $term_slugs = array_map( 'trim', explode( ',', $terms_str ) );
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $term_slugs,
                    'operator' => 'IN',
                ];
            }
            // Taxonomy exclude filter
            $terms_exclude = sanitize_text_field( $s['loop_terms_exclude'] ?? '' );
            if ( $terms_exclude !== '' ) {
                $exclude_slugs = array_map( 'trim', explode( ',', $terms_exclude ) );
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $exclude_slugs,
                    'operator' => 'NOT IN',
                ];
            }
            if ( count( $tax_query ) > 1 ) {
                $tax_query['relation'] = 'AND';
            }
        }
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- query per il Row Loop del tile (filtro per tassonomia/termini scelti dall'utente); tax query necessaria alla funzione, volume limitato da posts_per_page.
        }

        // Meta query
        $meta_key = sanitize_text_field( $s['loop_meta_key'] ?? '' );
        if ( $meta_key !== '' ) {
            $meta_value   = sanitize_text_field( $s['loop_meta_value'] ?? '' );
            $meta_compare = $s['loop_meta_compare'] ?? '=';
            $valid_cmp    = [ '=', '!=', '>', '<', 'LIKE', 'EXISTS', 'NOT EXISTS' ];
            if ( ! in_array( $meta_compare, $valid_cmp, true ) ) $meta_compare = '=';

            $mq = [
                'key'     => $meta_key,
                'compare' => $meta_compare,
            ];
            if ( ! in_array( $meta_compare, [ 'EXISTS', 'NOT EXISTS' ], true ) ) {
                $mq['value'] = $meta_value;
            }
            $args['meta_query'] = [ $mq ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- query per il Row Loop del tile (filtro per meta scelto dall'utente); meta query necessaria alla funzione, volume limitato da posts_per_page.

            // Orderby meta
            $orderby = $s['loop_orderby'] ?? 'date';
            if ( in_array( $orderby, [ 'meta_value', 'meta_value_num' ], true ) ) {
                $args['meta_key'] = $meta_key; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- query per il Row Loop del tile (ordinamento per valore meta scelto dall'utente); meta key necessaria all'orderby, volume limitato da posts_per_page.
            }
        }

        $query = new WP_Query( $args );
        return $return_query ? $query : $query->posts;
    }

    /**
     * Renderizza la paginazione del Row Loop (numerica o bottone Load More).
     * Riusa la classe `.olo-btn-link` del tile button per coerenza visiva del bottone.
     *
     * @param array  $s            Settings della Row.
     * @param int    $current_page Pagina corrente.
     * @param int    $max_pages    Numero totale di pagine.
     * @param string $row_id       Identificatore univoco della Row (per query var + data attr).
     * @return string  HTML della paginazione (vuoto se non applicabile).
     */
    private function render_row_loop_pagination( $s, $current_page, $max_pages, $row_id ) {
        $mode = $s['loop_pagination'] ?? 'none';
        if ( $mode === 'none' || $max_pages <= 1 ) return '';

        $align = in_array( $s['loop_pagination_align'] ?? 'center', [ 'left', 'center', 'right' ], true )
            ? $s['loop_pagination_align'] : 'center';
        $align_css = $align === 'left' ? 'flex-start' : ( $align === 'right' ? 'flex-end' : 'center' );

        $wrapper_style = 'display:flex;justify-content:' . $align_css . ';margin-top:24px;';

        if ( $mode === 'numbers' ) {
            $qvar = 'olo_p_' . $row_id;
            $links = paginate_links( [
                'base'      => add_query_arg( $qvar, '%#%' ),
                'format'    => '',
                'current'   => $current_page,
                'total'     => $max_pages,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
                'type'      => 'array',
            ] );
            if ( empty( $links ) ) return '';
            $items = '';
            foreach ( $links as $lnk ) {
                $items .= '<span class="olo-loop-page-item">' . $lnk . '</span>';
            }
            return '<nav class="olo-loop-pagination olo-loop-pagination--numbers" style="' . esc_attr( $wrapper_style ) . '">'
                . $items . '</nav>';
        }

        if ( $mode === 'load_more' ) {
            // Mostra il bottone solo se ci sono altre pagine da caricare
            if ( $current_page >= $max_pages ) return '';
            $label = sanitize_text_field( $s['loop_load_more_label'] ?? '' ) ?: __( 'Carica altri', 'olobuild' );
            // Riusa la classe `.olo-btn-link` del tile button per coerenza visiva.
            // Wrapper `.olo-button` applica gli stili di centratura/padding del button.
            $btn = '<a href="#" role="button"'
                . ' class="olo-btn-link olo-loop-load-more"'
                . ' data-olo-loop-row="' . esc_attr( $row_id ) . '"'
                . ' data-olo-loop-page="' . intval( $current_page ) . '"'
                . ' data-olo-loop-max="' . intval( $max_pages ) . '"'
                . ' style="display:inline-block;padding:14px 32px;background-color:var(--olo-color-primary,#6366F1);color:var(--olo-color-primary-contrast,#FFFFFF);border-radius:6px;text-decoration:none;font-weight:600;cursor:pointer;transition:opacity .2s ease;">'
                . '<span class="olo-loop-load-more-label">' . esc_html( $label ) . '</span>'
                . '</a>';
            return '<div class="olo-loop-pagination olo-loop-pagination--load-more" style="' . esc_attr( $wrapper_style ) . '">'
                . $btn . '</div>';
        }

        return '';
    }

    /**
     * Renderizza il template del Loop una volta per ogni post.
     *
     * IMPORTANTE: il "template" del Loop è la PRIMA colonna della Row.
     * Le altre colonne eventualmente presenti vengono ignorate quando il loop è
     * attivo. Questo modello (Elementor-style):
     *   - Coerente con come l'utente pensa al loop ("una card si ripete N volte")
     *   - Layout della disposizione gestito dalla Row (es. 33-33-33 + 6 post = 2 righe da 3)
     *   - Coerente col modello mentale "Loop Item = la prima colonna"
     *
     * Usato sia dal render normale che dal REST Load More.
     *
     * @return string  HTML concatenato del template renderizzato per ogni post.
     */
    public function render_row_loop_children( $children, $loop_posts, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        if ( empty( $loop_posts ) || empty( $children ) ) return '';
        // Solo il primo child viene usato come template del singolo card del loop.
        $template_child = $children[0];
        global $post;
        $old_post = $post;
        $html = '';
        foreach ( $loop_posts as $loop_post ) {
            $post = $loop_post;
            setup_postdata( $post );
            $html .= $this->render_node( $template_child, $manager, $template_id, $hover_css_rules, $tile_counter, $parent_is_grid );
        }
        $post = $old_post;
        if ( $old_post ) { setup_postdata( $old_post ); } else { wp_reset_postdata(); }
        return $html;
    }

    /**
     * Render a Column using UIkit width classes.
     */
    private function render_column_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter, $parent_is_grid = false ) {
        $s        = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        $classes = [];
        $inline_styles = [];

        if ( $parent_is_grid ) {
            // === CSS Grid cell: use grid-column / grid-row placement ===
            if ( ! empty( $s['grid_column'] ) ) {
                $inline_styles[] = 'grid-column: ' . esc_attr( $s['grid_column'] );
            }
            if ( ! empty( $s['grid_row'] ) ) {
                $inline_styles[] = 'grid-row: ' . esc_attr( $s['grid_row'] );
            }
            $inline_styles[] = 'min-width: 0';
        } else {
            // === Classic Flexbox: UIkit width classes ===
            $width_custom  = $s['width_custom'] ?? '';
            $width_default = $s['width_default'] ?? '';
            $width_small   = $s['width_small'] ?? '';
            $width_medium  = $s['width_medium'] ?? '';
            $width_large   = $s['width_large'] ?? '';

            if ( $width_custom !== '' && floatval( $width_custom ) > 0 ) {
                $classes[] = 'uk-width-1-1';
            } else {
                if ( $width_default && isset( $this->fraction_map[ $width_default ] ) ) {
                    $classes[] = 'uk-width-' . $width_default;
                }
                if ( $width_small && isset( $this->fraction_map[ $width_small ] ) ) {
                    $classes[] = 'uk-width-' . $width_small . '@s';
                }
                if ( $width_medium && isset( $this->fraction_map[ $width_medium ] ) ) {
                    $classes[] = 'uk-width-' . $width_medium . '@m';
                }
                if ( $width_large && isset( $this->fraction_map[ $width_large ] ) ) {
                    $classes[] = 'uk-width-' . $width_large . '@l';
                }

                if ( empty( $classes ) ) {
                    $classes[] = 'uk-width-expand';
                }
            }
        }

        // Shadow CLASS — column applica sempre (no branch has_bg_any come element).
        if ( ! empty( $style['shadow'] ) ) {
            $uk_shadow_map = [
                'sm' => 'uk-box-shadow-small',
                'md' => 'uk-box-shadow-medium',
                'lg' => 'uk-box-shadow-large',
                'xl' => 'uk-box-shadow-xlarge',
            ];
            if ( isset( $uk_shadow_map[ $style['shadow'] ] ) ) {
                $classes[] = $uk_shadow_map[ $style['shadow'] ];
            }
        }

        // Helper unificato: margin/padding/border-radius/border/opacity/flex/transform/
        // box-shadow inline/text-shadow/backdrop/overflow/dimensions/mask/custom_css/position.
        $this->apply_common_box_styles( $inline_styles, $style, $s, $advanced );

        // Background handling for column (post-helper: gestione layer image/video/overlay).
        // Vedi commento in render_section_node: fallback su $s['bg'].
        $col_bg      = $this->css->get_effective_bg( $style );
        if ( ( $col_bg['type'] ?? 'none' ) === 'none' && ! empty( $s['bg']['type'] ) && $s['bg']['type'] !== 'none' ) {
            $col_bg = $this->css->get_effective_bg( [ 'bg' => $s['bg'] ] );
        }
        $has_col_bg_image = ( $col_bg['type'] === 'image' && ! empty( $col_bg['image_url'] ) );
        $has_col_bg_video = ( $col_bg['type'] === 'video' && ! empty( $col_bg['video_url'] ) );
        $has_col_bg_any   = ( $col_bg['type'] !== 'none' );
        $has_col_overlay  = ( $has_col_bg_any && ! empty( $col_bg['overlay_opacity'] ) && intval( $col_bg['overlay_opacity'] ) > 0 );

        if ( ! $has_col_bg_image && ! $has_col_bg_video && $col_bg['type'] !== 'none' ) {
            $bg_css = $this->css->get_bg_inline_css( $col_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }
        if ( $has_col_bg_image || $has_col_bg_video ) {
            $classes[] = 'uk-position-relative';
            $inline_styles[] = 'overflow: clip';
        }

        // v3.55.48 — sticky column ri-attivata. Necessaria perché lo sticky della
        // tile element (Avanzate → Sticky) raramente funziona per layout immagine
        // + testo: il parent immediato della tile è la column wrapper, che spesso
        // ha overflow:clip (per bg image) o altezza non stretched. La column invece
        // è child diretto della row (uk-grid) che è sempre flex container con
        // height = max child height. position:sticky sulla column funziona quindi
        // come atteso: si blocca all'offset, si sblocca quando la row termina.
        if ( ! empty( $s['sticky'] ) ) {
            $sticky_offset = max( 0, intval( $s['sticky_offset'] ?? 50 ) );
            $inline_styles[] = 'position: sticky';
            // Top dinamico: --olo-sticky-top-offset viene aggiornata da
            // print_sticky_offset_script() in base all'altezza dell'header sticky.
            // Nel builder la var resta 0 (header forzato a position:relative).
            $inline_styles[] = 'top: calc(var(--olo-sticky-top-offset, 0px) + ' . $sticky_offset . 'px)';
            $inline_styles[] = 'align-self: start';
            $inline_styles[] = 'z-index: 5';
            self::$needs_sticky_offset_script = true;
        }

        // ID for hover CSS support
        $tile_counter++;
        $col_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mc-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $col_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $col_css_id, $advanced );

        // Custom CSS per colonna (campo settings.custom_css)
        $this->collect_custom_css( $s, $col_css_id, $hover_css_rules );

        // Entrance animation (olo-entrance-*) for column
        $entrance = $s['entrance_animation'] ?? 'none';
        if ( $entrance && $entrance !== 'none' ) {
            $classes[] = 'olo-entrance-' . sanitize_html_class( $entrance );
            $classes[] = 'olo-visible'; // applicata subito: l'animation parte al page-load (no IntersectionObserver dependency)
            if ( ! empty( $s['entrance_stagger'] ) ) {
                $stagger_delay = intval( $s['entrance_stagger_delay'] ?? 100 );
                $stagger_delay = max( 25, min( 500, $stagger_delay ) );
                $classes[] = 'olo-stagger-parent';
                $inline_styles[] = '--olo-stagger-delay: ' . $stagger_delay . 'ms';
            }
        }

        // Scrollspy & element parallax attributes for column
        $col_scrollspy_attr = $this->anim->build_scrollspy_attr( $advanced );
        $col_el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );
        $col_mouse_attrs = $this->anim->build_mouse_attrs( $advanced );

        $html = '<div id="' . esc_attr( $col_css_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '"';
        if ( ! empty( $inline_styles ) ) {
            $html .= ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"';
        }
        $html .= $col_scrollspy_attr . $col_el_parallax_attr . $col_mouse_attrs . $this->anim->build_spotlight_attr( $advanced ) . '>';

        // Background image cover for column
        if ( $has_col_bg_image ) {
            $bg_size = esc_attr( $col_bg['image_size'] ?? 'cover' );
            $bg_pos  = esc_attr( $col_bg['image_position'] ?? 'center center' );
            $html .= '<div class="uk-position-cover" style="background-image: url(' . esc_url( $col_bg['image_url'] ) . '); background-size: ' . $bg_size . '; background-position: ' . $bg_pos . '; background-repeat: no-repeat"';
            $html .= $this->anim->build_uk_parallax_attr( $col_bg );
            $html .= '></div>';
        }
        // Background video cover for column
        if ( $has_col_bg_video ) {
            $vid_url    = esc_url( $col_bg['video_url'] );
            $vid_poster = ! empty( $col_bg['video_poster'] ) ? esc_url( $col_bg['video_poster'] ) : '';
            $vid_fit    = esc_attr( $col_bg['video_fit'] ?? 'cover' );
            $vid_pos    = esc_attr( $col_bg['image_position'] ?? 'center center' );
            $html .= '<video aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: ' . $vid_fit . '; object-position: ' . $vid_pos . '; pointer-events: none" autoplay muted loop playsinline';
            if ( $vid_poster ) $html .= ' poster="' . $vid_poster . '"';
            $html .= '><source src="' . $vid_url . '" type="' . $this->get_video_mime( $vid_url ) . '"></video>';
        }
        // Overlay for column
        if ( $has_col_overlay ) {
            $ov_color   = esc_attr( $col_bg['overlay_color'] ?? '#000000' );
            $ov_opacity = intval( $col_bg['overlay_opacity'] ) / 100;
            $html .= '<div class="uk-position-cover" style="background-color: ' . $ov_color . '; opacity: ' . $ov_opacity . '; pointer-events: none" aria-hidden="true"></div>';
        }

        // Column children content (z-index above bg if needed)
        if ( $has_col_bg_image || $has_col_bg_video || $has_col_overlay ) {
            $html .= '<div style="position: relative; z-index: 1">';
        }
        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }
        if ( $has_col_bg_image || $has_col_bg_video || $has_col_overlay ) {
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render an inner-columns container (flex row with sub-columns).
     */
    private function render_inner_columns_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s        = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];
        $gap      = absint( $s['gap'] ?? 16 );
        $valign   = $s['vertical_align'] ?? 'stretch';
        $stack    = ! empty( $s['stack_mobile'] );

        $align_css = $this->align_map[ $valign ] ?? 'stretch';

        $inline_styles = [
            'display: flex',
            'gap: ' . $gap . 'px',
            'align-items: ' . $align_css,
        ];

        if ( ! $stack ) {
            $inline_styles[] = 'flex-wrap: nowrap';
        } else {
            $inline_styles[] = 'flex-wrap: wrap';
        }

        // Margin & Padding from style tab
        // intval() previene CSS injection via tile settings (es. "10;background:url(...)").
        // I valori margin/padding sono SEMPRE numeri interi (px) — qualsiasi cosa diversa
        // viene troncata a 0.
        if ( ! empty( $style['margin_top'] ) )     $inline_styles[] = 'margin-top: ' . intval( $style['margin_top'] ) . 'px';
        if ( ! empty( $style['margin_right'] ) )   $inline_styles[] = 'margin-right: ' . intval( $style['margin_right'] ) . 'px';
        if ( ! empty( $style['margin_bottom'] ) )  $inline_styles[] = 'margin-bottom: ' . intval( $style['margin_bottom'] ) . 'px';
        if ( ! empty( $style['margin_left'] ) )    $inline_styles[] = 'margin-left: ' . intval( $style['margin_left'] ) . 'px';
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = 'padding-top: ' . intval( $style['padding_top'] ) . 'px';
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = 'padding-right: ' . intval( $style['padding_right'] ) . 'px';
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = 'padding-bottom: ' . intval( $style['padding_bottom'] ) . 'px';
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = 'padding-left: ' . intval( $style['padding_left'] ) . 'px';

        // Background
        $tile_bg = $this->css->get_effective_bg( $style );
        if ( $tile_bg['type'] !== 'none' && $tile_bg['type'] !== 'image' && $tile_bg['type'] !== 'video' ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Border radius
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = $this->css->build_border_radius_css( $style['border_radius'] );

        // Border (sistema unificato: oggetto 4-side + fallback legacy 3-key)
        $border_css = $this->build_wrapper_border_css( $style );
        if ( $border_css ) $inline_styles[] = $border_css;

        $classes = [ 'olo-inner-columns' ];
        if ( ! empty( $advanced['css_classes'] ) ) {
            $classes[] = esc_attr( $advanced['css_classes'] );
        }

        // ID for hover CSS support
        $tile_counter++;
        $ic_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mic-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $ic_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $ic_css_id );

        $html = '';

        // Stack on mobile: responsive CSS
        if ( $stack ) {
            $ic_class = 'olo-ic-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
            $classes[] = $ic_class;
            $html .= '<style>@container olo-tpl (max-width:640px){.' . $ic_class . '{flex-direction:column}.' . $ic_class . '>*{width:100%!important}}</style>';
        }

        $html .= '<div id="' . esc_attr( $ic_css_id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . esc_attr( implode( '; ', $inline_styles ) ) . '">';

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render an inner-column (single sub-column within inner-columns).
     */
    private function render_inner_column_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $s        = $node['settings'] ?? [];
        $style    = $node['style'] ?? [];
        $advanced = $node['advanced'] ?? [];

        $width = floatval( $s['width'] ?? 50 );

        $inline_styles = [
            'width: ' . $width . '%',
            'min-width: 0',
            'box-sizing: border-box',
        ];

        // Margin & Padding
        // intval() previene CSS injection via tile settings (es. "10;background:url(...)").
        // I valori margin/padding sono SEMPRE numeri interi (px) — qualsiasi cosa diversa
        // viene troncata a 0.
        if ( ! empty( $style['margin_top'] ) )     $inline_styles[] = 'margin-top: ' . intval( $style['margin_top'] ) . 'px';
        if ( ! empty( $style['margin_right'] ) )   $inline_styles[] = 'margin-right: ' . intval( $style['margin_right'] ) . 'px';
        if ( ! empty( $style['margin_bottom'] ) )  $inline_styles[] = 'margin-bottom: ' . intval( $style['margin_bottom'] ) . 'px';
        if ( ! empty( $style['margin_left'] ) )    $inline_styles[] = 'margin-left: ' . intval( $style['margin_left'] ) . 'px';
        if ( ! empty( $style['padding_top'] ) )    $inline_styles[] = 'padding-top: ' . intval( $style['padding_top'] ) . 'px';
        if ( ! empty( $style['padding_right'] ) )  $inline_styles[] = 'padding-right: ' . intval( $style['padding_right'] ) . 'px';
        if ( ! empty( $style['padding_bottom'] ) ) $inline_styles[] = 'padding-bottom: ' . intval( $style['padding_bottom'] ) . 'px';
        if ( ! empty( $style['padding_left'] ) )   $inline_styles[] = 'padding-left: ' . intval( $style['padding_left'] ) . 'px';

        // Background
        $tile_bg = $this->css->get_effective_bg( $style );
        if ( $tile_bg['type'] !== 'none' && $tile_bg['type'] !== 'image' && $tile_bg['type'] !== 'video' ) {
            $bg_css = $this->css->get_bg_inline_css( $tile_bg );
            if ( $bg_css ) $inline_styles[] = $bg_css;
        }

        // Border radius
        if ( ! empty( $style['border_radius'] ) ) $inline_styles[] = $this->css->build_border_radius_css( $style['border_radius'] );

        // Border (sistema unificato: oggetto 4-side + fallback legacy 3-key)
        $border_css = $this->build_wrapper_border_css( $style );
        if ( $border_css ) $inline_styles[] = $border_css;

        // Sticky column support
        if ( ! empty( $s['sticky'] ) ) {
            $sticky_offset = intval( $s['sticky_offset'] ?? 20 );
            $inline_styles[] = 'position: sticky';
            $inline_styles[] = 'top: calc(var(--olo-sticky-top-offset, 0px) + ' . $sticky_offset . 'px)';
            $inline_styles[] = 'align-self: flex-start';
            self::$needs_sticky_offset_script = true;
        }

        // ID for hover CSS support
        $tile_counter++;
        $icol_css_id = ! empty( $advanced['html_id'] ) ? $advanced['html_id'] : 'mci-' . $template_id . '-' . $tile_counter;

        // Hover CSS rules
        $this->collect_hover_css( $style, $icol_css_id, false, $hover_css_rules );
        $this->collect_responsive_css( $style, $icol_css_id );

        $html = '<div id="' . esc_attr( $icol_css_id ) . '" class="olo-inner-column" style="' . esc_attr( implode( '; ', $inline_styles ) ) . '">';

        foreach ( $node['children'] ?? [] as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Render a floating panel container node.
     * Uses the tile's render() for the opening wrapper, then injects children, then render_closing().
     */
    private function render_floatingpanel_node( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $tile_instance = $manager->get_tile( 'floatingpanel' );
        if ( ! $tile_instance ) return '';

        $settings = $node['settings'] ?? [];

        // In builder mode, force panel always visible, in normal flow, so users can edit it.
        // Also clear placement positioning (top/left/etc.) to keep panel inline.
        if ( $this->builder_mode ) {
            $settings = array_merge( $settings, [
                'trigger_mode'  => 'always',
                'position'      => 'relative',
                'placement'     => 'top-left',
                'offset_x'      => '0',
                'offset_y'      => '0',
                'custom_top'    => '',
                'custom_left'   => '',
                'custom_bottom' => '',
                'custom_right'  => '',
                'width'         => '100%',
                'height'        => '',
                'z_index'       => '0',
                '_builder_mode' => true,
            ] );
        }

        // Render opening wrapper (panel div with styles, trigger button, close button)
        $html = Olobuild_Tile_Utils::process_dynamic_tags( $tile_instance->render( $settings, $node['style'] ?? [] ) );

        $children = $node['children'] ?? [];

        // Builder mode: identifying banner so users know this is a floating panel
        // (in frontend it would be positioned/floating; in editor it's shown inline).
        if ( $this->builder_mode ) {
            $orig_placement = $node['settings']['placement'] ?? 'bottom-right';
            $orig_position  = $node['settings']['position'] ?? 'fixed';
            $pos_label = ucfirst( str_replace( '-', ' ', $orig_placement ) );
            $mode_label = ucfirst( $orig_position );
            $html .= '<div class="olo-fp-builder-banner" style="display:flex;align-items:center;gap:8px;padding:6px 10px;margin:-8px -8px 12px -8px;background:rgba(232,98,42,0.12);border-radius:6px;font-size:11px;font-weight:600;color:#c2410c;text-transform:uppercase;letter-spacing:0.5px;">'
                   . '<span>📌 ' . esc_html__( 'Pannello flottante', 'olobuild' ) . '</span>'
                   . '<span style="opacity:0.6;font-weight:400;text-transform:none;letter-spacing:0;">→ ' . esc_html( $mode_label ) . ' · ' . esc_html( $pos_label ) . '</span>'
                   . '</div>';
        }

        // Builder mode: when empty, inject a visible drop-zone placeholder so users can
        // see where to drop tiles (the panel is otherwise an empty box).
        if ( $this->builder_mode && empty( $children ) ) {
            $fp_id = esc_attr( $node['id'] ?? '' );
            $html .= '<div class="olo-fp-builder-empty" data-olo-fp-empty="' . $fp_id . '" style="min-height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;border:2px dashed rgba(232,98,42,0.6);border-radius:8px;padding:20px;background:rgba(232,98,42,0.06);color:#e8622a;font-size:13px;font-weight:500;text-align:center;cursor:pointer;">'
                   . '<span style="font-size:32px;font-weight:300;line-height:1;pointer-events:none;">+</span>'
                   . '<span style="pointer-events:none;">' . esc_html__( 'Trascina qui contenuti del pannello', 'olobuild' ) . '</span>'
                   . '<span style="font-size:10px;opacity:0.7;text-transform:uppercase;letter-spacing:0.5px;pointer-events:none;">' . esc_html__( 'O clicca per aprire il finder', 'olobuild' ) . '</span>'
                   . '</div>';
        }

        // Render children inside the panel
        foreach ( $children as $child ) {
            $html .= $this->render_node( $child, $manager, $template_id, $hover_css_rules, $tile_counter );
        }

        // Render closing wrapper + JS
        $html .= $tile_instance->render_closing( $settings );

        return $html;
    }

    /**
     * Check if a section node contains only a single floatingpanel tile
     * (inside row > column), with no other content.
     */
    private function section_has_only_floatingpanel( $node ) {
        $rows = $node['children'] ?? [];
        if ( count( $rows ) !== 1 ) return false;

        $row = $rows[0];
        if ( ( $row['type'] ?? '' ) !== 'row' ) return false;

        $cols = $row['children'] ?? [];
        if ( count( $cols ) !== 1 ) return false;

        $col = $cols[0];
        if ( ( $col['type'] ?? '' ) !== 'column' ) return false;

        $tiles = $col['children'] ?? [];
        if ( count( $tiles ) !== 1 ) return false;

        return ( $tiles[0]['type'] ?? '' ) === 'floatingpanel';
    }

    /**
     * Extract and render only the floatingpanel from a section>row>column structure,
     * skipping all parent wrappers to avoid empty section gap.
     */
    private function extract_and_render_floatingpanel( $node, $manager, $template_id, &$hover_css_rules, &$tile_counter ) {
        $fp_node = $node['children'][0]['children'][0]['children'][0];
        return $this->render_floatingpanel_node( $fp_node, $manager, $template_id, $hover_css_rules, $tile_counter );
    }

    /**
     * Render an element (leaf tile) with full wrapper (bg, margin, padding, hover).
     * Uses UIkit utility classes where possible.
     */
    /**
     * Map element type to its items key in settings.
     */
    private function get_items_key( $type ) {
        $map = [
            'accordion'     => 'panels',
            'panelslider'   => 'panels',
            'slideshow'     => 'slides',
            'overlayslider' => 'slides',
            'popover'       => 'markers',
        ];
        return $map[ $type ] ?? 'items';
    }
}
