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
     * Una voce per ogni riga a griglia in corso di render (righe annidate): true se
     * le sue colonne devono portare la classe olo-gc-* della «Larghezza responsive».
     *
     * @var bool[]
     */
    private $griglia_pila = [];

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
            // Controllo unico a 4 lati (`padding_custom`), con ripiego sulle due
            // chiavi storiche sopra/sotto. Prima i lati orizzontali non esistevano.
            $sec_pad = Olobuild_Tile_Utils::spacing_sides(
                $s['padding_custom'] ?? null,
                [ 'top' => $s['padding_top_custom'] ?? null, 'bottom' => $s['padding_bottom_custom'] ?? null ],
                [ 70, 0, 70, 0 ]
            );
            $inline_styles[] = 'padding: ' . Olobuild_Tile_Utils::sides_css( $sec_pad );
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

        // Animazione continua — REGOLA con selettore, mai una dichiarazione inline:
        // lo sfondo Aurora/Bagliori animato scrive a sua volta `animation:` inline
        // (vedi $bg_css sopra), e due `animation` nello stesso attributo style non
        // convivono: l'ultima cancella l'altra, in silenzio. È lo stesso motivo per
        // cui l'element la emette come regola (class-frontend-renderer.php).
        // Qui la chiave arriva da due posti: `settings` nelle sezioni storiche,
        // `advanced` in quelle salvate dal pannello Avanzate.
        $inf_anim_css = $this->css->build_infinite_animation_css( $s, $css_id );
        if ( $inf_anim_css ) {
            $hover_css_rules[] = $inf_anim_css;
        }
        // La variante da `advanced` conserva il suo costruttore: là `infinite_speed`
        // è in secondi (com'è etichettato nell'inspector), non sulla scala 1-10 —
        // riusare l'altro cambierebbe la velocità delle sezioni già pubblicate.
        $adv_anim_decl = $this->anim->build_inline_animation_css( $advanced );
        if ( $adv_anim_decl ) {
            // Stesso identico selettore della gemella build_infinite_animation_css()
            // (class-css-builder.php): l'attributo id viene scritto GREZZO poche righe
            // sopra, quindi ripulirlo solo qui faceva mancare il bersaglio a ogni
            // sezione con un id che contiene uno spazio, un punto o un accento — e
            // l'animazione spariva in silenzio.
            $hover_css_rules[] = '#' . esc_attr( $css_id ) . '{' . $adv_anim_decl . '}'
                . '@media(prefers-reduced-motion:reduce){#' . esc_attr( $css_id ) . '{animation:none}}';
        }

        // Custom CSS per sezione (campo settings.custom_css)
        $this->collect_custom_css( $s, $css_id, $hover_css_rules );

        // Scroll snap: add full-screen height + snap alignment
        if ( $scroll_snap ) {
            $inline_styles[] = 'height: 100vh';
            $inline_styles[] = 'scroll-snap-align: start';
            $inline_styles[] = 'box-sizing: border-box';
        }

        // Entrance animation — classi + variabili CSS (blocco unico, vedi trait css).
        $this->apply_entrance_animation( $s, $classes, $inline_styles );

        // Scrollspy & element parallax attributes
        $scrollspy_attr = $this->anim->build_scrollspy_attr( $advanced );
        $el_parallax_attr = $this->anim->build_element_parallax_attr( $advanced );
        $mouse_attrs = $this->anim->build_mouse_attrs( $advanced );

        // Mask (inline style). L'animazione continua NON sta qui: è una regola con
        // selettore, emessa sopra insieme alla gemella di `settings`.
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

        // Entrance animation — stesso blocco della sezione: prima la riga si fermava
        // alla classe e ignorava durata, ritardo, curva e intensita'.
        $this->apply_entrance_animation( $s, $wrapper_classes, $wrapper_styles );

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
            // «Larghezza responsive» delle colonne (grid_width_*): con almeno una
            // larghezza la classe olo-g-* va sul div PRIMA di stamparlo (il
            // preg_replace del Load More cerca questo stesso attributo). Senza
            // larghezze $grid_colonne e' vuoto e la riga resta quella di sempre.
            $grid_id      = '';
            $grid_colonne = $this->griglia_colonne_con_larghezze( $node, ! empty( $s['loop_enabled'] ) );
            if ( $grid_colonne ) {
                $grid_id = 'olo-g-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
                $grid_class_list[] = $grid_id;
            }
            $grid_class_attr = ! empty( $grid_class_list ) ? ' class="' . esc_attr( implode( ' ', $grid_class_list ) ) . '"' : '';
            $html .= '<div' . $grid_class_attr . ' style="' . esc_attr( implode( '; ', $grid_css_parts ) ) . '"' . $grid_extra_attrs . '>';

            // Loop mode: repeat children for each post from WP_Query
            $loop_enabled = ! empty( $s['loop_enabled'] );
            $loop_pagination_html = '';
            // Le colonne di QUESTA riga prendono la classe olo-gc-* (pila: una riga
            // a griglia dentro una colonna ha la sua). In loop no: le copie hanno lo
            // stesso id e quelle del Load More arrivano senza, vale la regola sulla riga.
            $this->griglia_pila[] = ( ! empty( $grid_colonne ) && ! $loop_enabled );
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
            array_pop( $this->griglia_pila );

            $html .= '</div>';
            $html .= $loop_pagination_html;

            // Stack on mobile: override grid to 1 column
            if ( $stack ) {
                // Con le larghezze la classe e' gia' sul div: niente secondo str_replace.
                if ( $grid_id === '' ) {
                    $grid_id = 'olo-g-' . substr( md5( $node['id'] ?? wp_rand() ), 0, 6 );
                    $html = str_replace( '<div' . $grid_class_attr, '<div class="' . esc_attr( trim( implode( ' ', $grid_class_list ) . ' ' . $grid_id ) ) . '"', $html );
                }
                $bp_mobile = intval( $this->breakpoints['tablet'] ?? 960 );
                $html .= '<style>@media(max-width:' . $bp_mobile . 'px){.' . $grid_id . '{grid-template-columns:1fr!important;grid-template-rows:auto!important}.' . $grid_id . '>*{grid-column:auto!important;grid-row:auto!important}}</style>';
            }
            // Dopo il div e dopo lo stack, mai in testa: render_node mette
            // data-olo-tile-id sul PRIMO tag dell'HTML della riga.
            if ( $grid_colonne ) {
                $html .= $this->css_larghezze_griglia( $s, $grid_colonne, $grid_id, $stack, $gap, $loop_enabled );
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

    // =========================================================================
    // «Larghezza responsive» delle colonne in una riga a griglia (grid_width_*)
    // =========================================================================

    /**
     * Le colonne che contano per la «Larghezza responsive» di una riga a griglia:
     * quelle che verranno rese davvero (in loop solo la prima, il modello che si
     * ripete), se almeno una ha una grid_width_* valida. Altrimenti [] e la riga
     * resta quella di sempre, byte per byte.
     *
     * I width_* delle colonne (misure della riga Flex) qui non si leggono: righe a
     * griglia salvate con width_* vecchi (Footer repeat(4, 1fr)...) non cambiano.
     *
     * @param array $node Nodo riga.
     * @param bool  $loop Riga in modalita' loop.
     * @return array Figli in ordine di resa (indici 0..n-1).
     */
    private function griglia_colonne_con_larghezze( $node, $loop ) {
        $figli = ( isset( $node['children'] ) && is_array( $node['children'] ) ) ? $node['children'] : [];
        if ( $loop ) {
            $figli = ( isset( $figli[0] ) && is_array( $figli[0] ) ) ? [ $figli[0] ] : [];
        }
        $ha_larghezze = false;
        foreach ( $figli as $figlio ) {
            if ( is_array( $figlio ) && $this->griglia_frazioni_colonna( $figlio ) ) {
                $ha_larghezze = true;
                break;
            }
        }
        if ( ! $ha_larghezze ) {
            return [];
        }
        if ( $loop ) {
            return $figli;
        }
        // Solo i figli che render_node rendera' (ruolo, date, post...): uno nascosto
        // non deve spostare posizione e quota degli altri.
        $resi = [];
        foreach ( $figli as $figlio ) {
            if ( is_array( $figlio ) && $this->should_render_node( $figlio ) ) {
                $resi[] = $figlio;
            }
        }
        foreach ( $resi as $figlio ) {
            if ( $this->griglia_frazioni_colonna( $figlio ) ) {
                return $resi;
            }
        }
        return [];
    }

    /**
     * Le grid_width_* valide di una colonna, per misura: [ 'small' => [ 1, 2 ] ].
     * Frazione esatta dalla chiave 'n-d': fraction_map fa solo da elenco dei
     * valori ammessi (i suoi 33.33 e 66.66 sono arrotondati).
     */
    private function griglia_frazioni_colonna( $col ) {
        $s   = ( isset( $col['settings'] ) && is_array( $col['settings'] ) ) ? $col['settings'] : [];
        $out = [];
        foreach ( [ 'default', 'small', 'medium', 'large' ] as $misura ) {
            $v = $s[ 'grid_width_' . $misura ] ?? '';
            if ( is_string( $v ) && $v !== '' && isset( $this->fraction_map[ $v ] ) ) {
                $p = explode( '-', $v );
                $out[ $misura ] = [ intval( $p[0] ), intval( $p[1] ) ];
            }
        }
        return $out;
    }

    /** Classe di una colonna nel <style> della sua riga a griglia. */
    private function griglia_classe_colonna( $id ) {
        return 'olo-gc-' . substr( md5( (string) $id ), 0, 6 );
    }

    /**
     * Margine sinistro + destro di una colonna in px, nella fascia che comincia a
     * $min px. In griglia il margine sta DENTRO la cella; in flex si somma alla
     * base: va tolto da li'. Per lato, come lo rende la pagina:
     *   - in linea (apply_common_box_styles(): intval, solo se non vuoto) vince
     *     sulle regole #id, in ogni fascia;
     *   - se no quello per dispositivo, margin_<lato>_<bp> (stesso test di
     *     collect_responsive_css(), che lo scrive in @media(max-width:X) senza
     *     !important): delle soglie che valgono nella fascia (X >= $min; le fasce
     *     si spezzano subito oltre ogni X) vince l'ultima stampata;
     *   - se no 0.
     *
     * @param array     $col    Colonna.
     * @param int|float $min    Inizio della fascia in px.
     * @param int[]     $soglie bp => X, in ordine di stampa (griglia_soglie_margini()).
     * @return int
     */
    private function griglia_margini_colonna( $col, $min = 0, $soglie = [] ) {
        $st  = ( isset( $col['style'] ) && is_array( $col['style'] ) ) ? $col['style'] : [];
        $tot = 0;
        foreach ( [ 'left', 'right' ] as $lato ) {
            if ( ! empty( $st[ 'margin_' . $lato ] ) ) {
                $tot += intval( $st[ 'margin_' . $lato ] );
                continue;
            }
            $m = 0;
            foreach ( $soglie as $bp => $x ) {
                $k = 'margin_' . $lato . '_' . $bp;
                if ( $min <= $x && isset( $st[ $k ] ) && $st[ $k ] !== '' && $st[ $k ] !== null ) {
                    $m = intval( $st[ $k ] );
                }
            }
            $tot += $m;
        }
        return $tot;
    }

    /**
     * Le soglie dei margini per dispositivo, bp => X px, nell'ordine in cui la
     * pagina ne stampa i blocchi @media(max-width:X) (collect_responsive_css()):
     * quello delle chiavi di responsive_css_rules, cioe' della prima comparsa di
     * ogni soglia (di solito dalla piu' larga), gia' deciso per le colonne di una
     * riga quando la riga scrive il suo CSS (le rende prima); a pari X, l'ordine
     * di collect_responsive_css(). Dove valgono piu' soglie vince l'ultima.
     *
     * @return int[]
     */
    private function griglia_soglie_margini() {
        $pos    = array_flip( array_keys( $this->responsive_css_rules ) );
        $soglie = [];
        $ordine = [];
        $i      = 0;
        foreach ( [ 'tablet_landscape' => 1200, 'tablet' => 960, 'mobile_landscape' => 640, 'mobile' => 480 ] as $bp => $def ) {
            $x             = intval( $this->breakpoints[ $bp ] ?? $def );
            $soglie[ $bp ] = $x;
            $ordine[ $bp ] = [ $pos[ $x . 'px' ] ?? PHP_INT_MAX, $i++ ];
        }
        uksort(
            $soglie,
            function ( $a, $b ) use ( $ordine ) {
                return $ordine[ $a ] <=> $ordine[ $b ];
            }
        );
        return $soglie;
    }

    /**
     * Il <style> della «Larghezza responsive» di una riga a griglia.
     *
     * La griglia NON si ridisegna con piu' tracce: il gap della riga sta fra ogni
     * coppia di tracce, e con 60 tracce 59 gap facevano sfondare la riga. Nelle
     * fasce di schermo in cui almeno una colonna ha una larghezza il div diventa
     * un flex che va a capo (gap invariato) e ogni colonna prende
     *     flex: 0 1 f·100% − G·(1−f) − M
     * con G il gap fra le colonne e M i suoi margini sinistro + destro: una fila
     * di frazioni che somma 1 si riempie esattamente, come le uk-width-* di una
     * riga Flex, e il margine resta dentro la parte della colonna come nella
     * cella della griglia (sommato alla base farebbe andare a capo la fila e,
     * dove la riga si impila, sfondare la pagina). M e' quello della fascia,
     * margini per dispositivo compresi (margin_*_tablet/_mobile...,
     * griglia_margini_colonna()): per loro le fasce si spezzano anche subito
     * oltre ogni soglia @media(max-width:X). box-sizing:border-box tiene
     * dentro padding e bordo; l'altezza minima della colonna, li', comprende il
     * padding come in una riga Flex. flex-shrink 1 (in linea la colonna ha gia'
     * min-width:0) resta come rete per cio' che la base non conosce
     * (margin_*_widescreen, in @media min-width, che l'inspector non scrive):
     * stringe la colonna che supera DA SOLA la fila, come la cella della
     * griglia, invece di far scorrere la pagina. Gli a capo li decide la base
     * (flex-wrap), quindi le file sono le stesse di flex-shrink 0. f e':
     *   - la larghezza della colonna, a cascata dalla misura piu' piccola
     *     (telefono → tablet → desktop → schermo grande: una misura vuota prende
     *     la piu' vicina impostata fra quelle piu' piccole), sulle soglie fisse di
     *     UIkit 0/640/960/1200 delle classi uk-width-*@s/@m/@l;
     *   - senza larghezza, 1 se la fascia e' impilata («Impila su mobile»);
     *   - se no la sua quota della griglia della riga: i pesi fr della sua
     *     cella, quella di grid_column o, per una colonna senza (incollata o
     *     duplicata), quella in cui la mette la griglia (griglia_celle()).
     * In flex grid-column, grid-row e le altezze di grid-template-rows non valgono:
     * niente sovrapposizioni, le colonne vanno in ordine. Nelle fasce senza nessuna
     * larghezza la griglia resta quella di sempre.
     *
     * Confine dello stack: una riga con larghezze e' impilata ESATTAMENTE dove lo
     * e' lo <style> dello stack, @media(max-width:bp) con bp = breakpoints[tablet]
     * (960 di serie), estremo incluso. Fino a bp compreso le colonne ad «Auto»
     * restano al 100%; da oltre bp prendono la loro quota, con la media
     * complementare «not all and (max-width:bp)», non con min-width bp+1: con un
     * viewport frazionario (zoom, scala di Windows), fra bp e bp+1, la riga con
     * larghezze resterebbe impilata e le altre no. Le
     * colonne con una larghezza seguono invece sempre le soglie UIkit: a 960 px
     * esatti prendono la misura desktop (uk-width-*@m, min-width 960) dentro una
     * riga ancora impilata. Conta perche' l'anteprima «Tablet» del builder e' un
     * iframe largo proprio breakpoints[tablet] (iframeStyle di BuilderCanvas.vue):
     * li' la riga si impila come le righe senza larghezze e le altre colonne non
     * si spostano quando se ne tocca una; la colonna toccata mostra la misura
     * desktop, come le uk-width-*@m delle righe Flex nella stessa anteprima. Una
     * soglia tablet diversa (es. 1024) da' una fascia 960-1024 con la misura
     * desktop e la riga impilata.
     *
     * Nel calc() solo il segno meno; numeri col punto (number_format: con la
     * virgola di una locale, in PHP 7.4, la dichiarazione verrebbe scartata); la
     * percentuale arrotondata per difetto e i px per eccesso, perche' una fila
     * piena non vada a capo per un decimillesimo.
     *
     * @param array  $s       Impostazioni della riga.
     * @param array  $colonne Figli resi (griglia_colonne_con_larghezze()).
     * @param string $gid     Classe olo-g-* del div della griglia.
     * @param bool   $stack   «Impila su mobile».
     * @param int    $gap     Gap della riga in px.
     * @param bool   $loop    Riga in loop: una regola sola per tutte le copie.
     * @return string
     */
    private function css_larghezze_griglia( $s, $colonne, $gid, $stack, $gap, $loop ) {
        $bande  = [ 'default' => 0, 'small' => 640, 'medium' => 960, 'large' => 1200 ];
        $g_col  = $s['grid_column_gap'] ?? '';
        $g      = ( $g_col !== '' ) ? max( 0, intval( $g_col ) ) : intval( $gap );
        $bp     = $stack ? intval( $this->breakpoints['tablet'] ?? 960 ) : 0;
        $soglie = $this->griglia_soglie_margini();
        // Fasce: [ inizio, media query ], per inizio. Oltre alle soglie di UIkit,
        // una fascia subito oltre ogni soglia X delle @media(max-width:X) della
        // pagina (margini per dispositivo; con X = bp la fine dello stack): inizio
        // X + 0.5, solo per ordinarla e per le cascate, e la media complementare
        // «not all and (max-width:X)». Una fascia uguale alla precedente non si
        // stampa: senza margini per dispositivo restano le fasce di sempre.
        $fasce = [];
        foreach ( $bande as $da ) {
            $fasce[ (string) $da ] = [ $da, $da > 0 ? '(min-width:' . $da . 'px)' : '' ];
        }
        foreach ( $soglie as $x ) {
            if ( $x > 0 ) {
                $fasce[ (string) ( $x + 0.5 ) ] = [ $x + 0.5, 'not all and (max-width:' . $x . 'px)' ];
            }
        }
        usort(
            $fasce,
            function ( $x, $y ) {
                return $x[0] <=> $y[0];
            }
        );
        $frazioni = [];
        foreach ( $colonne as $i => $col ) {
            $frazioni[ $i ] = $this->griglia_frazioni_colonna( $col );
        }
        $quote = $this->griglia_quote_base( $s, $colonne );
        $n     = count( $colonne );
        $sel   = '.' . $gid;
        $css   = '';
        $prima = true;
        $prec  = '';
        foreach ( $fasce as $fascia ) {
            list( $min, $media ) = $fascia;
            $impila   = ( $bp > 0 && $min <= $bp );
            $per_w    = []; // flex-basis => selettori
            $attiva   = false;
            $senza_cl = false;
            foreach ( $colonne as $i => $col ) {
                $f = null;
                foreach ( $bande as $misura => $da ) {
                    if ( $da <= $min && isset( $frazioni[ $i ][ $misura ] ) ) {
                        $f = $frazioni[ $i ][ $misura ];
                    }
                }
                // In loop la colonna e' il modello: le copie hanno gli stessi margini.
                $m = $this->griglia_margini_colonna( $col, $min, $soglie );
                if ( $f !== null ) {
                    $attiva = true;
                    $w = $this->griglia_flex_basis( $f[0] / $f[1], $g, $m );
                } else {
                    $w = $this->griglia_flex_basis( $impila ? 1 : $quote[ $i ], $g, $m );
                }
                if ( $loop ) {
                    $per_w[ $w ][] = $sel . '>*';
                } elseif ( ( $col['type'] ?? '' ) === 'column' && ! empty( $col['id'] ) ) {
                    $per_w[ $w ][] = $sel . '>.' . $this->griglia_classe_colonna( $col['id'] );
                } else {
                    $senza_cl = true;
                }
            }
            if ( ! $attiva ) {
                continue;
            }
            $regole = '';
            if ( $senza_cl ) {
                // Figli senza classe (senza id, o non colonne): parti uguali.
                $regole .= $sel . '>*{flex:0 1 ' . $this->griglia_flex_basis( $impila ? 1 : 1 / max( 1, $n ), $g ) . '!important}';
            }
            foreach ( $per_w as $w => $selettori ) {
                $regole .= implode( ',', $selettori ) . '{flex:0 1 ' . $w . '!important}';
            }
            // Media a cascata (in ordine di inizio: vince l'ultima che vale): una
            // fascia uguale alla precedente non serve.
            if ( ! $prima && $regole === $prec ) {
                continue;
            }
            $blocco = $prima
                ? $sel . '{display:flex!important;flex-wrap:wrap!important}' . $sel . '>*{box-sizing:border-box!important}' . $regole
                : $regole;
            $css  .= $media !== '' ? '@media ' . $media . '{' . $blocco . '}' : $blocco;
            $prima = false;
            $prec  = $regole;
        }
        return $css === '' ? '' : '<style>' . $css . '</style>';
    }

    /**
     * flex-basis di una colonna larga $f (0..1] in una fila con gap $g px, con
     * $m px di margini sinistro + destro: f·100% − g·(1−f) − m, scritto col solo
     * segno meno (margini negativi: «- -Npx»). Con $m = 0 l'uscita e' quella di
     * sempre: '100%', 'P%' o 'calc(P% - Npx)'.
     */
    private function griglia_flex_basis( $f, $g, $m = 0 ) {
        if ( $f >= 1 ) {
            $pct = '100';
            $px  = $m;
        } else {
            $pct = rtrim( rtrim( number_format( floor( $f * 1000000 + 0.000001 ) / 10000, 4, '.', '' ), '0' ), '.' );
            $px  = $g * ( 1 - $f ) + $m;
        }
        // Per eccesso (verso lo zero se negativo): la base resta per difetto.
        $px = ceil( $px * 10000 - 0.000001 ) / 10000;
        if ( $px == 0 ) {
            return $pct . '%';
        }
        $num = rtrim( rtrim( number_format( abs( $px ), 4, '.', '' ), '0' ), '.' );
        return 'calc(' . $pct . '% - ' . ( $px < 0 ? '-' : '' ) . $num . 'px)';
    }

    /**
     * La quota (0..1] di ogni colonna nella griglia della riga: i pesi fr delle
     * tracce della sua cella (griglia_celle()). Tracce implicite, oltre quelle
     * del template, non hanno un peso: conta la traccia del bordo. Template non
     * calcolabile (px, auto, auto-fill...): parti uguali fra le colonne rese,
     * tranne quelle da «1 / -1», che coprono tutta la griglia esplicita con
     * qualunque template: fila intera.
     *
     * @param array $s       Impostazioni della riga.
     * @param array $colonne Figli resi.
     * @return float[]
     */
    private function griglia_quote_base( $s, $colonne ) {
        $quote    = [];
        $template = $s['grid_columns'] ?? '';
        $pesi     = $this->griglia_pesi_tracce( is_string( $template ) ? $template : '' );
        if ( ! $pesi ) {
            $parte = 1 / max( 1, count( $colonne ) );
            foreach ( $colonne as $i => $col ) {
                $gc          = $col['settings']['grid_column'] ?? '';
                $quote[ $i ] = $this->griglia_da_prima_a_ultima( is_scalar( $gc ) ? (string) $gc : '' ) ? 1 : $parte;
            }
            return $quote;
        }
        $n      = count( $pesi );
        $totale = array_sum( $pesi );
        $celle  = $this->griglia_celle( $s, $colonne, $n );
        foreach ( $colonne as $i => $col ) {
            $da    = max( 1, min( $celle[ $i ][0], $n ) );
            $a     = max( $da + 1, min( $celle[ $i ][1], $n + 1 ) );
            $somma = 0;
            for ( $t = $da; $t < $a; $t++ ) {
                $somma += $pesi[ $t - 1 ];
            }
            $quote[ $i ] = $somma / $totale;
        }
        return $quote;
    }

    /**
     * La cella di ogni colonna come la mette la griglia CSS (auto-posizionamento,
     * css-grid-1 §8.5), con la direzione della riga (grid_auto_flow row/column) e
     * «dense»: 1. le colonne con riga e colonna definite; 2. quelle con la sola
     * posizione della fila (la riga, nel flusso per righe), nel primo posto libero
     * di quella fila (sparse: dopo quelle messe li' da questo passo); 3. le altre
     * in ordine DOM col cursore: il primo posto libero dopo la precedente (dense:
     * dall'inizio), a capo quando la fila non basta. Una colonna senza grid_column
     * (incollata o duplicata: il posto della cella d'origine non viaggia con la
     * copia) prende cosi' la cella in cui la mette la griglia, non la traccia che
     * segue la sorella precedente nel DOM. Linee prima della prima: la prima (li'
     * la griglia aggiungerebbe tracce in testa); linee e span oltre 1000: 1000.
     *
     * @param array $s       Impostazioni della riga.
     * @param array $colonne Figli resi.
     * @param int   $n       Tracce di grid-template-columns.
     * @return array i => [ inizio, fine ]: linee di colonna, fine esclusa.
     */
    private function griglia_celle( $s, $colonne, $n ) {
        $flusso = ( isset( $s['grid_auto_flow'] ) && is_string( $s['grid_auto_flow'] ) ) ? strtolower( $s['grid_auto_flow'] ) : '';
        $percol = ( strpos( $flusso, 'column' ) !== false );
        $dense  = ! empty( $s['grid_auto_flow_dense'] ) || ( strpos( $flusso, 'dense' ) !== false );
        $righe  = $this->griglia_conta_tracce( $s['grid_rows'] ?? '' );
        // Ogni voce: [ p, q ], p lungo la fila (le colonne nel flusso per righe,
        // le righe in quello per colonne) e q fra le file; ciascuna [ inizio, fine ]
        // se definita, [ null, span ] se automatica.
        $voci = [];
        foreach ( $colonne as $i => $col ) {
            $st         = ( isset( $col['settings'] ) && is_array( $col['settings'] ) ) ? $col['settings'] : [];
            $gc         = $st['grid_column'] ?? '';
            $gr         = $st['grid_row'] ?? '';
            $c          = $this->griglia_posizione( is_scalar( $gc ) ? (string) $gc : '', $n );
            $r          = $this->griglia_posizione( is_scalar( $gr ) ? (string) $gr : '', $righe );
            $voci[ $i ] = $percol ? [ $r, $c ] : [ $c, $r ];
        }
        $posto  = []; // i => [ p0, p1, q0, q1 ]
        $libera = function ( $p0, $p1, $q0, $q1 ) use ( &$posto ) {
            foreach ( $posto as $x ) {
                if ( $p0 < $x[1] && $x[0] < $p1 && $q0 < $x[3] && $x[2] < $q1 ) {
                    return false;
                }
            }
            return true;
        };
        // 1. Definite su entrambi gli assi.
        foreach ( $voci as $i => $v ) {
            if ( $v[0][0] !== null && $v[1][0] !== null ) {
                $posto[ $i ] = [ $v[0][0], $v[0][1], $v[1][0], $v[1][1] ];
            }
        }
        // 2. Bloccate a una fila.
        $dopo = [];
        foreach ( $voci as $i => $v ) {
            if ( $v[0][0] === null && $v[1][0] !== null ) {
                $p = $dense ? 1 : ( $dopo[ $v[1][0] ] ?? 1 );
                while ( ! $libera( $p, $p + $v[0][1], $v[1][0], $v[1][1] ) ) {
                    $p++;
                }
                $posto[ $i ]       = [ $p, $p + $v[0][1], $v[1][0], $v[1][1] ];
                $dopo[ $v[1][0] ] = $p + $v[0][1];
            }
        }
        // 3. Posti della fila: quelli del template, piu' quelli impliciti che
        //    servono alle colonne con posizione e allo span piu' lungo.
        $pn = $percol ? $righe : $n;
        foreach ( $voci as $i => $v ) {
            if ( isset( $posto[ $i ] ) ) {
                $pn = max( $pn, $posto[ $i ][1] - 1 );
            } else {
                $pn = max( $pn, $v[0][0] !== null ? $v[0][1] - 1 : $v[0][1] );
            }
        }
        // 4. Le altre, in ordine DOM, col cursore [ fila $cq, posto $cp ].
        $cq = 1;
        $cp = 1;
        foreach ( $voci as $i => $v ) {
            if ( isset( $posto[ $i ] ) ) {
                continue;
            }
            $alta = $v[1][1]; // span fra le file: qui la posizione e' automatica
            if ( $v[0][0] !== null ) {
                if ( $dense ) {
                    $cq = 1;
                } elseif ( $v[0][0] < $cp ) {
                    $cq++;
                }
                $cp = $v[0][0];
                while ( ! $libera( $v[0][0], $v[0][1], $cq, $cq + $alta ) ) {
                    $cq++;
                }
                $posto[ $i ] = [ $v[0][0], $v[0][1], $cq, $cq + $alta ];
            } else {
                $span = $v[0][1];
                if ( $dense ) {
                    $cq = 1;
                    $cp = 1;
                }
                while ( true ) {
                    if ( $cp + $span - 1 > $pn ) {
                        $cq++;
                        $cp = 1;
                    } elseif ( $libera( $cp, $cp + $span, $cq, $cq + $alta ) ) {
                        break;
                    } else {
                        $cp++;
                    }
                }
                $posto[ $i ] = [ $cp, $cp + $span, $cq, $cq + $alta ];
            }
        }
        $celle = [];
        foreach ( $posto as $i => $x ) {
            $celle[ $i ] = $percol ? [ $x[2], $x[3] ] : [ $x[0], $x[1] ];
        }
        return $celle;
    }

    /**
     * Quante tracce ha una lista di tracce (grid_rows): repeat(N, …) espanso,
     * repeat(auto-fill/auto-fit, …) una volta, vuoto o 'none' 0.
     */
    private function griglia_conta_tracce( $template ) {
        $template = is_string( $template ) ? trim( $template ) : '';
        if ( $template === '' || strtolower( $template ) === 'none' ) {
            return 0;
        }
        $tot = 0;
        foreach ( $this->griglia_token( $template ) as $tok ) {
            if ( $tok[0] === '[' ) {
                continue; // nomi di linea
            }
            if ( preg_match( '/^repeat\((.*)\)$/is', $tok, $m ) ) {
                $parti = $this->griglia_prima_virgola( $m[1] );
                $volte = trim( $parti[0] );
                $k     = 0;
                if ( count( $parti ) === 2 ) {
                    foreach ( $this->griglia_token( $parti[1] ) as $t2 ) {
                        if ( $t2[0] !== '[' ) {
                            $k++;
                        }
                    }
                }
                $tot += ( preg_match( '/^\d+$/', $volte ) ? min( intval( $volte ), 1000 ) : 1 ) * $k;
                continue;
            }
            $tot++;
        }
        return min( $tot, 1000 );
    }

    /**
     * I pesi delle tracce di grid-template-columns ('1fr 2fr 1fr' → [1, 2, 1];
     * repeat(N, …) espanso; minmax(x, Nfr) → N). Vuoto = una traccia sola, come
     * la griglia senza template. null se una traccia non e' in fr (px, auto,
     * auto-fill/auto-fit...): la quota non si puo' calcolare.
     */
    private function griglia_pesi_tracce( $template ) {
        $template = trim( $template );
        if ( $template === '' ) {
            return [ 1 ];
        }
        $pesi = [];
        foreach ( $this->griglia_token( $template ) as $tok ) {
            if ( $tok[0] === '[' ) {
                continue; // nomi di linea
            }
            if ( preg_match( '/^repeat\((.*)\)$/is', $tok, $m ) ) {
                $parti = $this->griglia_prima_virgola( $m[1] );
                $volte = trim( $parti[0] );
                if ( count( $parti ) !== 2 || ! preg_match( '/^\d+$/', $volte ) || intval( $volte ) < 1 || intval( $volte ) > 60 ) {
                    return null;
                }
                $interni = [];
                foreach ( $this->griglia_token( $parti[1] ) as $t2 ) {
                    if ( $t2[0] === '[' ) {
                        continue;
                    }
                    $w = $this->griglia_peso_traccia( $t2 );
                    if ( $w === null ) {
                        return null;
                    }
                    $interni[] = $w;
                }
                if ( ! $interni ) {
                    return null;
                }
                for ( $k = 0; $k < intval( $volte ); $k++ ) {
                    foreach ( $interni as $w ) {
                        $pesi[] = $w;
                    }
                }
                continue;
            }
            $w = $this->griglia_peso_traccia( $tok );
            if ( $w === null ) {
                return null;
            }
            $pesi[] = $w;
        }
        return ( $pesi && count( $pesi ) <= 240 ) ? $pesi : null;
    }

    /** Peso di una traccia: '2fr' → 2, 'minmax(0, 1fr)' → 1; altrimenti null. */
    private function griglia_peso_traccia( $tok ) {
        if ( preg_match( '/^minmax\((.*)\)$/is', $tok, $m ) ) {
            $parti = $this->griglia_prima_virgola( $m[1] );
            if ( count( $parti ) !== 2 ) {
                return null;
            }
            $tok = trim( $parti[1] );
        }
        if ( preg_match( '/^(\d+(?:\.\d+)?|\.\d+)fr$/i', $tok, $m ) && floatval( $m[1] ) > 0 ) {
            return floatval( $m[1] );
        }
        return null;
    }

    /** Divide una lista di tracce sugli spazi di primo livello (fuori da () e []). */
    private function griglia_token( $str ) {
        $out = [];
        $cur = '';
        $liv = 0;
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i++ ) {
            $c = $str[ $i ];
            if ( $c === '(' || $c === '[' ) {
                $liv++;
            } elseif ( ( $c === ')' || $c === ']' ) && $liv > 0 ) {
                $liv--;
            }
            if ( $liv === 0 && in_array( $c, [ ' ', "\t", "\n", "\r" ], true ) ) {
                if ( $cur !== '' ) {
                    $out[] = $cur;
                    $cur   = '';
                }
                continue;
            }
            $cur .= $c;
        }
        if ( $cur !== '' ) {
            $out[] = $cur;
        }
        return $out;
    }

    /** Divide sulla prima virgola di primo livello: [ prima, resto ] oppure [ tutto ]. */
    private function griglia_prima_virgola( $str ) {
        $liv = 0;
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i++ ) {
            $c = $str[ $i ];
            if ( $c === '(' ) {
                $liv++;
            } elseif ( $c === ')' && $liv > 0 ) {
                $liv--;
            } elseif ( $c === ',' && $liv === 0 ) {
                return [ substr( $str, 0, $i ), substr( $str, $i + 1 ) ];
            }
        }
        return [ $str ];
    }

    /**
     * La posizione di una colonna su un asse di $n tracce esplicite, da
     * grid_column o grid_row: 'a', 'a / b' (negativi contati dalla fine: -1 =
     * ultima linea), 'span k', 'a / span k', 'span k / b'. Definita: [ inizio,
     * fine ] (linee, fine esclusa); vuota, 'auto' o non leggibile: [ null, span ],
     * la mette l'auto-posizionamento (griglia_celle()).
     */
    private function griglia_posizione( $valore, $n ) {
        $parti = explode( '/', strtolower( trim( $valore ) ), 2 );
        $a     = $this->griglia_linea( $parti[0] );
        $b     = isset( $parti[1] ) ? $this->griglia_linea( $parti[1] ) : [ 'auto', 0 ];
        if ( $a[0] === 'linea' ) {
            $inizio = $this->griglia_linea_n( $a[1], $n );
            if ( $b[0] === 'linea' ) {
                $fine = $this->griglia_linea_n( $b[1], $n );
            } elseif ( $b[0] === 'span' ) {
                $fine = $inizio + $b[1];
            } else {
                $fine = $inizio + 1;
            }
        } elseif ( $b[0] === 'linea' ) {
            $fine   = $this->griglia_linea_n( $b[1], $n );
            $inizio = $fine - ( $a[0] === 'span' ? $a[1] : 1 );
        } else {
            $span = ( $a[0] === 'span' ) ? $a[1] : ( ( $b[0] === 'span' ) ? $b[1] : 1 );
            return [ null, min( $span, 1000 ) ];
        }
        if ( $fine < $inizio ) {
            list( $inizio, $fine ) = [ $fine, $inizio ];
        }
        $inizio = min( max( 1, $inizio ), 1000 );
        $fine   = min( max( $inizio + 1, $fine ), 1001 );
        return [ $inizio, $fine ];
    }

    /** Una meta' di grid-column o grid-row: [ 'linea', a ] | [ 'span', k ] | [ 'auto', 0 ]. */
    private function griglia_linea( $parte ) {
        $parte = trim( $parte );
        if ( preg_match( '/^-?\d+$/', $parte ) && intval( $parte ) !== 0 ) {
            return [ 'linea', intval( $parte ) ];
        }
        if ( preg_match( '/^span\s+(\d+)$/', $parte, $m ) && intval( $m[1] ) > 0 ) {
            return [ 'span', intval( $m[1] ) ];
        }
        return [ 'auto', 0 ];
    }

    /** Numero di linea: i negativi contano dalla fine (-1 = linea n+1). */
    private function griglia_linea_n( $linea, $n ) {
        return $linea < 0 ? $n + 2 + $linea : $linea;
    }

    /**
     * True se grid_column va dalla prima all'ultima linea esplicita («1 / -1», o
     * «-1 / 1», che il CSS rigira): la colonna copre tutta la griglia anche quando
     * il numero di tracce non si conosce (auto-fill, px...).
     */
    private function griglia_da_prima_a_ultima( $valore ) {
        $parti = explode( '/', strtolower( trim( $valore ) ), 2 );
        if ( count( $parti ) !== 2 ) {
            return false;
        }
        $a = $this->griglia_linea( $parti[0] );
        $b = $this->griglia_linea( $parti[1] );
        if ( $a[0] !== 'linea' || $b[0] !== 'linea' ) {
            return false;
        }
        $linee = [ $a[1], $b[1] ];
        sort( $linee );
        return $linee === [ -1, 1 ];
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
            // La riga ha colonne con «Larghezza responsive»: il suo <style> la trova
            // da questa classe (css_larghezze_griglia). I width_* restano spenti.
            if ( end( $this->griglia_pila ) === true && ! empty( $node['id'] ) ) {
                $classes[] = $this->griglia_classe_colonna( $node['id'] );
            }
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

        // Entrance animation — stesso blocco della sezione: prima la colonna si
        // fermava alla classe e ignorava durata, ritardo, curva e intensita'.
        $this->apply_entrance_animation( $s, $classes, $inline_styles );

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
