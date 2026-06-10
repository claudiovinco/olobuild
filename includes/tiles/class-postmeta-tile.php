<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_PostMeta_Tile extends Olo_Tile_Base {

    protected $type     = 'postmeta';
    protected $name     = 'Post Meta';
    protected $icon     = 'dashicons-media-text';
    protected $category = 'dynamic';
    protected $defaults = [
        'preset'              => 'editorial-classic',
        'show_date'           => true,
        'show_author'         => true,
        'show_categories'     => true,
        'show_tags'           => false,
        'show_comments_count' => false,
        'show_reading_time'   => false,
        'date_format'         => 'd/m/Y',
        'layout'              => 'inline',
        'separator'           => ' · ',
        'icon_style'          => 'none',
        'text_color'          => '',
        'link_color'          => '',
        'icon_color'          => '',
        'bg_color'            => '',
        'font_size'           => '14',
        'font_family'         => 'inherit',
        'font_weight'         => '400',
        'text_transform'      => 'none',
        'letter_spacing'      => 0,
        'item_gap'            => 0,
        'chip_style'          => 'none',
        'chip_bg'             => '',
        'chip_padding_x'      => 0,
        'chip_padding_y'      => 0,
        'chip_radius'         => 0,
        'container_padding'   => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'container_radius'    => [],
        'effect_color'        => '',
        'effect_intensity'    => 'medium',
        'effect_speed'        => 0,
        'wow_disable'           => false,
        'wow_backdrop_blur'     => 0,
        'wow_backdrop_saturate' => 100,
        'wow_border_style'      => 'solid',
        'wow_font_family'       => 'inherit',
        'wow_rotation'          => 0,
        'wow_perspective'       => 0,
        'wow_tilt_x'            => 0,
        'wow_glow_pulse'        => false,
        'wow_title_glow'        => false,
        'wow_scanlines'         => false,

        'wow_terminal_prompt' => false,
        'author_link'         => true,
        'category_link'       => true,
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
    ];

    public function get_controls() {
        return [];
    }

    private function font_family_css( $val ) {
        switch ( $val ) {
            case 'sans':  return 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
            case 'serif': return 'Georgia, "Times New Roman", Times, serif';
            case 'mono':  return 'ui-monospace, "SF Mono", Menlo, Consolas, monospace';
            default:      return 'inherit';
        }
    }

    /**
     * Extra CSS per preset audaci (effetti che non si mappano a singoli field).
     */
    private function get_preset_extra_css( $preset_id, $uid, $s ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprieta personalizzabile.
        return '';
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-pm-' . wp_rand( 10000, 99999 );

        $post_id = get_the_ID();
        $post    = get_post( $post_id );

        // TOKEN-FIRST: neutri → token tema; link = primario brand (era #e1474f indaco off-brand)
        $preset_id  = sanitize_key( $s['preset'] ?? 'custom' );
        $text_color = $this->safe_color_css( $s['text_color'] ) ?: 'var(--olo-color-text-faint, #9CA3AF)';
        $link_color = $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $icon_color = $this->safe_color_css( $s['icon_color'] ) ?: 'var(--olo-color-text-soft, #6B7280)';
        $bg_color   = $this->safe_color_css( $s['bg_color'] );
        $font_size  = max( 10, min( 24, absint( $s['font_size'] ) ) );
        $separator  = esc_html( $s['separator'] );
        $layout     = $s['layout'] === 'stacked' ? 'stacked' : 'inline';
        $show_icons = $s['icon_style'] === 'before';
        $item_gap   = max( 0, min( 40, absint( $s['item_gap'] ) ) );

        $font_family = $this->font_family_css( $s['font_family'] ?? 'inherit' );
        $font_weight = in_array( $s['font_weight'], [ '300','400','500','600','700' ], true ) ? $s['font_weight'] : '400';
        $tt          = in_array( $s['text_transform'], [ 'none','uppercase','lowercase','capitalize' ], true ) ? $s['text_transform'] : 'none';
        $ls          = floatval( $s['letter_spacing'] ?? 0 );

        $chip_style  = in_array( $s['chip_style'], [ 'none','pill','tag','sticker','chip-3d' ], true ) ? $s['chip_style'] : 'none';
        $chip_bg     = $this->safe_color_css( $s['chip_bg'] ?? '' );
        $chip_px     = max( 0, min( 24, absint( $s['chip_padding_x'] ) ) );
        $chip_py     = max( 0, min( 16, absint( $s['chip_padding_y'] ) ) );
        // Dual-format: numero legacy O oggetto {tl,tr,br,bl}; '' se zero/vuoto (storico: nessuna regola).
        $chip_radius = $this->build_border_radius_css( $s['chip_radius'] ?? 0 );

        // Container padding/radius
        $cp = $s['container_padding'] ?? [];
        $cpt = is_array( $cp ) ? absint( $cp['top']    ?? 0 ) : 0;
        $cpr = is_array( $cp ) ? absint( $cp['right']  ?? 0 ) : 0;
        $cpb = is_array( $cp ) ? absint( $cp['bottom'] ?? 0 ) : 0;
        $cpl = is_array( $cp ) ? absint( $cp['left']   ?? 0 ) : 0;
        $container_radius_css = $this->build_border_radius_css( $s['container_radius'] ?? [] );

        // SVG icons (14x14)
        $icon_svg = [
            'date'     => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
            'author'   => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
            'category' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>',
            'tag'      => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
            'comment'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
            'clock'    => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        ];

        // Collect meta items
        $items = [];

        if ( ! empty( $s['show_date'] ) ) {
            $date_text = '';
            if ( $post ) {
                $date_text = get_the_date( $s['date_format'], $post );
            }
            if ( ! $date_text ) {
                $date_text = wp_date( $s['date_format'] );
            }
            $items[] = $this->build_meta_item( $date_text, '', $show_icons ? $icon_svg['date'] : '', $text_color, $icon_color );
        }

        if ( ! empty( $s['show_author'] ) ) {
            $author_name = '';
            $author_url  = '';
            if ( $post ) {
                $author_name = get_the_author_meta( 'display_name', $post->post_author );
                if ( ! empty( $s['author_link'] ) ) {
                    $author_url = get_author_posts_url( $post->post_author );
                }
            }
            if ( ! $author_name ) {
                $author_name = 'Autore';
            }
            $items[] = $this->build_meta_item( $author_name, $author_url, $show_icons ? $icon_svg['author'] : '', $author_url ? $link_color : $text_color, $icon_color );
        }

        if ( ! empty( $s['show_categories'] ) ) {
            $cats = [];
            if ( $post ) {
                $categories = get_the_category( $post->ID );
                if ( $categories ) {
                    foreach ( $categories as $cat ) {
                        if ( ! empty( $s['category_link'] ) ) {
                            $cats[] = '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '" style="color:' . $link_color . ';text-decoration:none;">' . esc_html( $cat->name ) . '</a>';
                        } else {
                            $cats[] = esc_html( $cat->name );
                        }
                    }
                }
            }
            if ( empty( $cats ) ) {
                $cats[] = 'Senza categoria';
            }
            $items[] = $this->build_meta_item( implode( ', ', $cats ), '', $show_icons ? $icon_svg['category'] : '', $text_color, $icon_color, true );
        }

        if ( ! empty( $s['show_tags'] ) ) {
            $tag_list = [];
            if ( $post ) {
                $tags = get_the_tags( $post->ID );
                if ( $tags ) {
                    foreach ( $tags as $tag ) {
                        $tag_list[] = '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" style="color:' . $link_color . ';text-decoration:none;">' . esc_html( $tag->name ) . '</a>';
                    }
                }
            }
            if ( ! empty( $tag_list ) ) {
                $items[] = $this->build_meta_item( implode( ', ', $tag_list ), '', $show_icons ? $icon_svg['tag'] : '', $text_color, $icon_color, true );
            }
        }

        if ( ! empty( $s['show_comments_count'] ) ) {
            $count = 0;
            if ( $post ) {
                $count = (int) get_comments_number( $post->ID );
            }
            $label = $count . ' ' . olo_t( $count === 1 ? 'commento' : 'commenti' );
            $items[] = $this->build_meta_item( $label, '', $show_icons ? $icon_svg['comment'] : '', $text_color, $icon_color );
        }

        if ( ! empty( $s['show_reading_time'] ) ) {
            $minutes = 1;
            if ( $post ) {
                $content    = get_the_content( null, false, $post );
                $word_count = str_word_count( wp_strip_all_tags( $content ) );
                $minutes    = max( 1, (int) ceil( $word_count / 200 ) );
            }
            $label = $minutes . ' ' . olo_t( 'min di lettura' );
            $items[] = $this->build_meta_item( $label, '', $show_icons ? $icon_svg['clock'] : '', $text_color, $icon_color );
        }

        if ( empty( $items ) ) {
            return '';
        }

        // Decide se mostrare separatori (chip style nasconde i separatori)
        $show_sep = ( $layout === 'inline' && $separator !== '' && $chip_style === 'none' );
        $sep_html = $show_sep ? '<span class="olo-postmeta-sep" style="color:' . $text_color . ';">' . $separator . '</span>' : '';

        // Container styles
        $wrap_style  = 'font-size:' . $font_size . 'px;line-height:1.6;';
        $wrap_style .= 'font-family:' . $font_family . ';';
        $wrap_style .= 'font-weight:' . $font_weight . ';';
        $wrap_style .= 'text-transform:' . $tt . ';';
        if ( $ls > 0 ) $wrap_style .= 'letter-spacing:' . $ls . 'px;';
        if ( $bg_color ) $wrap_style .= 'background:' . $bg_color . ';';
        if ( $cpt || $cpr || $cpb || $cpl ) $wrap_style .= "padding:{$cpt}px {$cpr}px {$cpb}px {$cpl}px;";
        if ( $container_radius_css ) $wrap_style .= "border-radius:{$container_radius_css};";

        if ( $layout === 'stacked' ) {
            $wrap_style .= 'display:flex;flex-direction:column;gap:' . max( 4, $item_gap ) . 'px;';
        } else {
            $gap_css = $item_gap > 0 ? "gap:{$item_gap}px;" : 'gap:0;';
            $wrap_style .= 'display:flex;flex-wrap:wrap;align-items:center;' . $gap_css;
        }

        ob_start();
        ?>
        <div class="olo-postmeta <?php echo esc_attr( $uid ); ?> olo-pm-preset-<?php echo esc_attr( $preset_id ); ?>" style="<?php echo esc_attr( $wrap_style ); ?>">
            <?php
            foreach ( $items as $i => $item_html ) {
                if ( $i > 0 && $sep_html ) {
                    echo $sep_html;
                }
                echo $this->wrap_meta_item( $item_html, $chip_style, $chip_bg, $chip_px, $chip_py, $chip_radius );
            }
            ?>
        </div>
        <?php

        // Hover link sotto preset (text-shadow su a)
        if ( $link_color && $preset_id !== 'neon-cyber' && $preset_id !== 'gradient-glow' ) {
            // hover non standardizzato qui, lascio i preset gestire
        }

        // a11y tastiera: anello di focus visibile sui link meta (autore/categorie/tag)
        echo '<style>.' . esc_attr( $uid ) . ' a:focus-visible{outline:none;box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);border-radius:3px;}</style>';

        // Preset extra CSS
        $extra = $this->get_preset_extra_css( $preset_id, $uid, $s );
        $extra .= $this->build_wow_effects_css( $s, '.' . $uid, '' );
        if ( $extra ) {
            echo '<style>' . $extra . '</style>';
        }

        // Border system standard
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}";
            echo $border_hover_css . $border_effect_css . '</style>';
        }
        return ob_get_clean();
    }

    /**
     * Wrap meta item with chip styling (if applicable).
     */
    private function wrap_meta_item( $item_html, $chip_style, $chip_bg, $chip_px, $chip_py, $chip_radius ) {
        if ( $chip_style === 'none' ) {
            return $item_html;
        }
        // Strip outer span e ricostruisci con stili chip
        $extra_style = '';
        if ( $chip_bg ) $extra_style .= 'background:' . $chip_bg . ';';
        if ( $chip_px || $chip_py ) $extra_style .= "padding:{$chip_py}px {$chip_px}px;";
        if ( $chip_radius !== '' ) $extra_style .= "border-radius:{$chip_radius};";

        // Inietta stili nello span esistente
        return preg_replace(
            '/^<span class="olo-postmeta-item" style="([^"]*)"/',
            '<span class="olo-postmeta-item olo-pm-chip-' . esc_attr( $chip_style ) . '" style="$1' . $extra_style . '"',
            $item_html,
            1
        );
    }

    /**
     * Build a single meta item HTML.
     */
    private function build_meta_item( $text, $url = '', $icon_svg = '', $color = '', $icon_color = '', $raw_html = false ) {
        $html = '<span class="olo-postmeta-item" style="display:inline-flex;align-items:center;gap:5px;color:' . $color . ';">';

        if ( $icon_svg ) {
            $html .= '<span class="olo-postmeta-icon" style="color:' . $icon_color . ';display:inline-flex;flex-shrink:0;">' . $icon_svg . '</span>';
        }

        if ( $url ) {
            $html .= '<a href="' . esc_url( $url ) . '" style="color:' . $color . ';text-decoration:none;">' . esc_html( $text ) . '</a>';
        } elseif ( $raw_html ) {
            $html .= '<span>' . $text . '</span>';
        } else {
            $html .= '<span>' . esc_html( $text ) . '</span>';
        }

        $html .= '</span>';
        return $html;
    }
}
