<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Grid_Tile extends Olo_Tile_Base {

    protected $type     = 'grid';
    protected $name     = 'Griglia';
    protected $icon     = 'dashicons-grid-view';
    protected $category = 'layout';
    protected $defaults = [
        'items'              => [
            [ 'title' => 'Elemento 1', 'content' => 'Descrizione del primo elemento.', 'image' => '', 'tag' => 'all' ],
            [ 'title' => 'Elemento 2', 'content' => 'Descrizione del secondo elemento.', 'image' => '', 'tag' => 'all' ],
            [ 'title' => 'Elemento 3', 'content' => 'Descrizione del terzo elemento.', 'image' => '', 'tag' => 'all' ],
        ],
        'columns'            => '3',
        'gap'                => 'default',
        'show_filter'        => false,
        'filter_style'       => 'pills',
        'filter_align'       => 'left',
        'masonry'            => false,
        'card_style'         => 'default',
        'shadow'             => 'none',
        'card_hover'         => 'none',
        'image_ratio'        => 'auto',
        'image_height'       => '',
        'image_fit'          => 'cover',
        'image_zoom'         => false,
        'card_radius'        => '8',
        'card_padding'       => '16',
        'card_bg_color'      => '',
        'card_border_color'  => '',
        'equal_height'       => false,
        'overlay_text'       => false,
        'overlay_position'   => 'bottom',
        'title_size'         => '',
        'content_size'       => '',
        'title_color'        => '',
        'content_color'      => '',
        'image_animation'    => 'none',
        'image_animation_speed' => '3',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'items',       'type' => 'custom', 'label' => 'Items' ],
            [ 'key' => 'columns',     'type' => 'select', 'label' => 'Columns' ],
            [ 'key' => 'gap',         'type' => 'select', 'label' => 'Gap' ],
            [ 'key' => 'show_filter', 'type' => 'toggle', 'label' => 'Show Filter' ],
            [ 'key' => 'masonry',     'type' => 'toggle', 'label' => 'Masonry' ],
        ];
    }

    public function render( $settings ) {
        $s     = wp_parse_args( $settings, $this->defaults );
        $items = $this->parse_items( $s['items'] );
        $count = count( $items );

        if ( $count === 0 ) {
            return '';
        }

        $columns = absint( $s['columns'] ) ?: 3;
        if ( $columns > 6 ) {
            $columns = 6;
        }

        // Gap mapping
        $gap_map = [
            'collapse' => 'uk-grid-collapse',
            'small'    => 'uk-grid-small',
            'default'  => '',
            'medium'   => 'uk-grid-medium',
            'large'    => 'uk-grid-large',
        ];
        $gap_class = isset( $gap_map[ $s['gap'] ] ) ? $gap_map[ $s['gap'] ] : '';

        $masonry_attr = ! empty( $s['masonry'] ) ? 'masonry: true' : '';
        $uid          = 'mgrid-' . wp_rand( 10000, 99999 );

        // Collect unique tags for filter (support comma-separated)
        $tags = [];
        $show_filter = ! empty( $s['show_filter'] );
        if ( $show_filter ) {
            foreach ( $items as $item ) {
                $raw_tag = ! empty( $item['tag'] ) ? $item['tag'] : 'all';
                $parts = array_map( 'trim', explode( ',', $raw_tag ) );
                foreach ( $parts as $part ) {
                    $slug = sanitize_title( $part );
                    if ( $slug && $slug !== 'all' && ! in_array( $slug, $tags, true ) ) {
                        $tags[] = $slug;
                    }
                }
            }
        }
        $has_tags = ! empty( $tags );

        // Build scoped CSS
        $scoped_css = $this->build_scoped_css( $uid, $s );

        ob_start();

        echo '<style>' . $scoped_css . '</style>';
        ?>
        <div class="olo-grid <?php echo esc_attr( $uid ); ?>"<?php if ( $show_filter && $has_tags ) : ?> uk-filter="target: .js-filter; animation: fade"<?php endif; ?>>

            <?php if ( $show_filter && $has_tags ) :
                $fa = $s['filter_align'] ?? 'left';
                $fa_cls = $fa === 'center' ? ' olo-filter-center' : ( $fa === 'right' ? ' olo-filter-right' : '' );
                $filter_style = $s['filter_style'] ?? 'pills';
                $all_label = ! empty( $s['filter_all_label'] ) ? esc_html( $s['filter_all_label'] ) : 'All';
            ?>
                <?php if ( $filter_style === 'minimal' ) : ?>
                <div class="olo-filter-minimal<?php echo $fa_cls; ?>">
                    <button class="olo-filter-minimal__btn" uk-filter-control><?php echo $all_label; ?></button>
                    <?php foreach ( $tags as $tag ) : ?>
                    <button class="olo-filter-minimal__btn" uk-filter-control=".tag-<?php echo esc_attr( $tag ); ?>"><?php echo esc_html( ucfirst( str_replace( '-', ' ', $tag ) ) ); ?></button>
                    <?php endforeach; ?>
                </div>
                <?php elseif ( $filter_style === 'buttons' ) : ?>
                <div class="olo-filter-buttons<?php echo $fa_cls; ?>">
                    <button class="olo-filter-btn" uk-filter-control><?php echo $all_label; ?></button>
                    <?php foreach ( $tags as $tag ) : ?>
                    <button class="olo-filter-btn" uk-filter-control=".tag-<?php echo esc_attr( $tag ); ?>"><?php echo esc_html( ucfirst( str_replace( '-', ' ', $tag ) ) ); ?></button>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <ul class="uk-subnav uk-subnav-pill<?php echo $fa_cls; ?>">
                    <li class="uk-active" uk-filter-control><a href="#"><?php echo $all_label; ?></a></li>
                    <?php foreach ( $tags as $tag ) : ?>
                    <li uk-filter-control=".tag-<?php echo esc_attr( $tag ); ?>"><a href="#"><?php echo esc_html( ucfirst( str_replace( '-', ' ', $tag ) ) ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            <?php endif; ?>

            <div class="js-filter uk-child-width-1-<?php echo esc_attr( $columns ); ?>@m <?php echo esc_attr( $gap_class ); ?><?php echo ! empty( $s['equal_height'] ) ? ' uk-grid-match' : ''; ?>" uk-grid<?php if ( $masonry_attr ) : ?>="<?php echo esc_attr( $masonry_attr ); ?>"<?php endif; ?>>
                <?php foreach ( $items as $item ) :
                    // Build tag classes (support comma-separated)
                    $tag_classes = [];
                    if ( $show_filter && $has_tags && ! empty( $item['tag'] ) && $item['tag'] !== 'all' ) {
                        $parts = array_map( 'trim', explode( ',', $item['tag'] ) );
                        foreach ( $parts as $part ) {
                            $slug = sanitize_title( $part );
                            if ( $slug && $slug !== 'all' ) {
                                $tag_classes[] = 'tag-' . $slug;
                            }
                        }
                    }
                    $tag_class_str = implode( ' ', $tag_classes );

                    $has_link   = ! empty( $item['link'] );
                    $link_tag   = $has_link ? 'a' : 'div';
                    $link_attrs = '';
                    if ( $has_link ) {
                        $link_attrs .= ' href="' . esc_url( $item['link'] ) . '"';
                        if ( ! empty( $item['link_target'] ) ) {
                            $link_attrs .= ' target="_blank" rel="noopener noreferrer"';
                        }
                        $link_attrs .= ' style="text-decoration:none;color:inherit;display:block;"';
                    }

                    $card_hover = $s['card_hover'] ?? 'none';
                    $card_style = $s['card_style'] ?? 'default';
                ?>
                <div<?php if ( $tag_class_str ) : ?> class="<?php echo esc_attr( $tag_class_str ); ?>"<?php endif; ?>>
                    <<?php echo $link_tag; ?> class="olo-grid-card olo-grid-card--<?php echo esc_attr( $card_style ); ?><?php echo $card_hover !== 'none' ? ' olo-grid-hover--' . esc_attr( $card_hover ) : ''; ?>"<?php echo $link_attrs; ?>>
                        <?php
                        // Badge
                        if ( ! empty( $item['badge'] ) ) {
                            $bc = ! empty( $item['badge_color'] ) ? esc_attr( $item['badge_color'] ) : 'var(--olo-color-primary, #e1474f)';
                            echo '<span class="olo-grid-badge" style="background:' . $bc . ';">' . esc_html( $item['badge'] ) . '</span>';
                        }

                        // Image
                        if ( ! empty( $item['image'] ) ) {
                            echo '<div class="olo-grid-media">';
                            $img_cls = 'olo-grid-img';
                            $img_anim = $s['image_animation'] ?? 'none';
                            if ( $img_anim && $img_anim !== 'none' ) {
                                $img_cls .= ' olo-grid-imganim--' . esc_attr( $img_anim );
                            }
                            $img_html = '<img class="' . esc_attr( $img_cls ) . '" src="' . esc_url( $item['image'] ) . '" alt="' . esc_attr( $item['title'] ) . '" loading="lazy">';
                            echo $this->render_hover_wrap( $img_html, $item['hover_image'] ?? '', $item['hover_video'] ?? '' );

                            // Overlay text on image
                            if ( ! empty( $s['overlay_text'] ) ) {
                                $ov_pos = $s['overlay_position'] ?? 'bottom';
                                echo '<div class="olo-grid-overlay olo-grid-overlay--' . esc_attr( $ov_pos ) . '">';
                                list( $gt_cls, $gt_data ) = $this->tfx_attrs( $s, 'title', wp_strip_all_tags( $item['title'] ) );
                                list( $gc_cls, $gc_data ) = $this->tfx_attrs( $s, 'content', wp_strip_all_tags( $item['content'] ?? '' ) );
                                echo '<h3 class="olo-grid-title' . $gt_cls . '"' . $gt_data . '>' . esc_html( wp_strip_all_tags( $item['title'] ) ) . '</h3>';
                                if ( ! empty( $item['content'] ) ) {
                                    echo '<p class="olo-grid-text' . $gc_cls . '"' . $gc_data . '>' . nl2br( esc_html( wp_strip_all_tags( $item['content'] ) ) ) . '</p>';
                                }
                                echo '</div>';
                            }

                            echo '</div>';
                        }

                        // Content area (skip if overlay_text + has image)
                        if ( empty( $s['overlay_text'] ) || empty( $item['image'] ) ) {
                            echo '<div class="olo-grid-body">';
                            if ( ! empty( $item['icon'] ) ) {
                                echo '<span class="olo-grid-icon ' . esc_attr( $item['icon'] ) . '"></span>';
                            }
                            echo '<h3 class="olo-grid-title">' . esc_html( wp_strip_all_tags( $item['title'] ) ) . '</h3>';
                            if ( ! empty( $item['content'] ) ) {
                                echo '<p class="olo-grid-text">' . nl2br( esc_html( wp_strip_all_tags( $item['content'] ) ) ) . '</p>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </<?php echo $link_tag; ?>>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }

    /**
     * Build scoped CSS for this grid instance.
     */
    private function build_scoped_css( $uid, $s ) {
        $sel = '.' . $uid;
        $css = '';

        $radius  = $this->build_border_radius_css( $s["card_radius"] ?? 8 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['card_radius_hover'] ?? null );
        $padding = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['card_padding'] ?? 16, 16 );
        $card_style   = $s['card_style'] ?? 'default';
        $border_color = $s['card_border_color'] ?? '';

        // Shadow on cards
        $shadow_css = '';
        $shadow_val = $s['shadow'] ?? 'none';
        $shadow_map = [
            'sm' => '0 1px 3px 0 rgba(0,0,0,0.12),0 1px 2px -1px rgba(0,0,0,0.1)',
            'md' => '0 4px 6px -1px rgba(0,0,0,0.15),0 2px 4px -2px rgba(0,0,0,0.12)',
            'lg' => '0 10px 15px -3px rgba(0,0,0,0.18),0 4px 6px -4px rgba(0,0,0,0.12)',
            'xl' => '0 20px 25px -5px rgba(0,0,0,0.2),0 8px 10px -6px rgba(0,0,0,0.15)',
        ];
        if ( isset( $shadow_map[ $shadow_val ] ) ) {
            $shadow_css = 'box-shadow:' . $shadow_map[ $shadow_val ] . '!important;';
        } elseif ( $shadow_val === 'custom' ) {
            $sh = intval( $s['shadow_h'] ?? 0 );
            $sv = intval( $s['shadow_v'] ?? 4 );
            $sb = intval( $s['shadow_blur'] ?? 10 );
            $ss = intval( $s['shadow_spread'] ?? 0 );
            $sc = $s['shadow_color'] ?? 'rgba(0,0,0,0.15)';
            $si = ! empty( $s['shadow_inset'] ) ? 'inset ' : '';
            $shadow_css = 'box-shadow:' . $si . $sh . 'px ' . $sv . 'px ' . $sb . 'px ' . $ss . 'px ' . esc_attr( $sc ) . '!important;';
        }

        // Grid item wrappers: allow shadow to overflow
        if ( $shadow_css ) {
            $css .= $sel . ' [uk-grid]>*{overflow:visible!important;}';
        }

        // Card base
        $css .= $sel . ' .olo-grid-card{position:relative;border-radius:' . $radius . 'px;' . $shadow_css . 'transition:transform 0.35s cubic-bezier(.4,0,.2,1),box-shadow 0.35s ease,border-color 0.3s,border-radius 400ms cubic-bezier(.4,0,.2,1);}';
        if ( $radius_hover_css !== '' ) {
            $css .= $sel . ' .olo-grid-card:hover{border-radius:' . $radius_hover_css . ' !important;}';
        }

        // Card styles
        if ( $card_style === 'default' ) {
            $bc = $border_color ? esc_attr( $border_color ) : '#e5e7eb';
            $css .= $sel . ' .olo-grid-card--default{background:#fff;border:1px solid ' . $bc . ';}';
        } elseif ( $card_style === 'minimal' ) {
            $css .= $sel . ' .olo-grid-card--minimal{background:none;border:none;}';
            if ( $border_color ) {
                $css .= $sel . ' .olo-grid-card--minimal{border:1px solid ' . esc_attr( $border_color ) . ';}';
            }
        } elseif ( $card_style === 'outlined' ) {
            $bc = $border_color ? esc_attr( $border_color ) : '#d1d5db';
            $css .= $sel . ' .olo-grid-card--outlined{background:transparent;border:2px solid ' . $bc . ';}';
        } elseif ( $card_style === 'elevated' ) {
            $css .= $sel . ' .olo-grid-card--elevated{background:#fff;border:none;box-shadow:0 4px 20px rgba(0,0,0,0.08);}';
            if ( $border_color ) {
                $css .= $sel . ' .olo-grid-card--elevated{border:1px solid ' . esc_attr( $border_color ) . ';}';
            }
        } elseif ( $card_style === 'glass' ) {
            $bc = $border_color ? esc_attr( $border_color ) : 'rgba(255,255,255,0.15)';
            $css .= $sel . ' .olo-grid-card--glass{background:rgba(255,255,255,0.08);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid ' . $bc . ';}';
        } elseif ( $card_style === 'gradient' ) {
            if ( $border_color ) {
                $css .= $sel . ' .olo-grid-card--gradient{background:#fff;border:2px solid ' . esc_attr( $border_color ) . ';}';
            } else {
                $css .= $sel . ' .olo-grid-card--gradient{background:#fff;border:2px solid transparent;background-image:linear-gradient(#fff,#fff),linear-gradient(135deg,var(--olo-color-primary, #e1474f),var(--olo-color-accent, #f4a23b),var(--olo-color-secondary, #16263d));background-origin:border-box;background-clip:padding-box,border-box;}';
            }
        } elseif ( $card_style === 'flat' ) {
            $css .= $sel . ' .olo-grid-card--flat{background:#f9fafb;border:none;}';
            if ( $border_color ) {
                $css .= $sel . ' .olo-grid-card--flat{border:1px solid ' . esc_attr( $border_color ) . ';}';
            }
        }

        // Custom background color overrides card style
        $bg_color = $s['card_bg_color'] ?? '';
        if ( $bg_color ) {
            $css .= $sel . ' .olo-grid-card{background:' . esc_attr( $bg_color ) . '!important;}';
        }

        // Hover effects
        $hover = $s['card_hover'] ?? 'none';
        if ( $hover === 'lift' ) {
            $css .= $sel . ' .olo-grid-hover--lift:hover{transform:translateY(-6px);box-shadow:0 12px 32px rgba(0,0,0,0.12);}';
        } elseif ( $hover === 'scale' ) {
            $css .= $sel . ' .olo-grid-hover--scale:hover{transform:scale(1.03);}';
        } elseif ( $hover === 'glow' ) {
            $css .= $sel . ' .olo-grid-hover--glow:hover{box-shadow:0 0 20px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);}';
        } elseif ( $hover === 'border-glow' ) {
            $css .= $sel . ' .olo-grid-hover--border-glow{transition:border-color 0.3s,transform 0.35s cubic-bezier(.4,0,.2,1),box-shadow 0.35s;}';
            $css .= $sel . ' .olo-grid-hover--border-glow:hover{border-color:var(--olo-color-primary, #e1474f)!important;box-shadow:0 0 15px color-mix(in srgb, var(--olo-color-primary, #e1474f) 20%, transparent);}';
        } elseif ( $hover === 'tilt' ) {
            $css .= $sel . ' .olo-grid-hover--tilt:hover{transform:perspective(800px) rotateY(4deg) rotateX(2deg);box-shadow:4px 8px 24px rgba(0,0,0,0.12);}';
        }

        // Image
        $img_ratio  = $s['image_ratio'] ?? 'auto';
        $img_height = absint( $s['image_height'] ?? 0 );
        $img_fit    = $s['image_fit'] ?? 'cover';

        $css .= $sel . ' .olo-grid-media{position:relative;overflow:hidden;border-radius:' . $radius . 'px ' . $radius . 'px 0 0;}';
        if ( $img_ratio && $img_ratio !== 'auto' ) {
            $css .= $sel . ' .olo-grid-media{aspect-ratio:' . $img_ratio . ';}';
        } elseif ( $img_height > 0 ) {
            $css .= $sel . ' .olo-grid-media{height:' . $img_height . 'px;}';
        }
        $css .= $sel . ' .olo-grid-img{width:100%;height:100%;object-fit:' . esc_attr( $img_fit ) . ';display:block;transition:transform 0.5s cubic-bezier(.4,0,.2,1);}';

        // Image zoom on hover
        if ( ! empty( $s['image_zoom'] ) ) {
            $css .= $sel . ' .olo-grid-card:hover .olo-grid-img{transform:scale(1.08);}';
        }

        // Image continuous animations
        $img_anim  = $s['image_animation'] ?? 'none';
        $img_speed = absint( $s['image_animation_speed'] ?? 3 );
        if ( $img_speed < 2 ) $img_speed = 2;

        if ( $img_anim && $img_anim !== 'none' ) {
            $anim_name = 'olo-grid-' . $img_anim;

            if ( $img_anim === 'ken-burns' ) {
                $css .= '@keyframes ' . $anim_name . '{0%{transform:scale(1) translate(0,0)}100%{transform:scale(1.15) translate(-2%,-2%)}}';
                $css .= $sel . ' .olo-grid-imganim--ken-burns{animation:' . $anim_name . ' ' . $img_speed . 's ease-in-out infinite alternate;}';
            } elseif ( $img_anim === 'pan-left' ) {
                $css .= '@keyframes ' . $anim_name . '{0%{transform:translateX(0) scale(1.15)}100%{transform:translateX(-10%) scale(1.15)}}';
                $css .= $sel . ' .olo-grid-imganim--pan-left{animation:' . $anim_name . ' ' . $img_speed . 's linear infinite alternate;}';
            } elseif ( $img_anim === 'pan-right' ) {
                $css .= '@keyframes ' . $anim_name . '{0%{transform:translateX(-10%) scale(1.15)}100%{transform:translateX(0) scale(1.15)}}';
                $css .= $sel . ' .olo-grid-imganim--pan-right{animation:' . $anim_name . ' ' . $img_speed . 's linear infinite alternate;}';
            } elseif ( $img_anim === 'pan-up' ) {
                $css .= '@keyframes ' . $anim_name . '{0%{transform:translateY(0) scale(1.15)}100%{transform:translateY(-10%) scale(1.15)}}';
                $css .= $sel . ' .olo-grid-imganim--pan-up{animation:' . $anim_name . ' ' . $img_speed . 's linear infinite alternate;}';
            } elseif ( $img_anim === 'pan-down' ) {
                $css .= '@keyframes ' . $anim_name . '{0%{transform:translateY(-10%) scale(1.15)}100%{transform:translateY(0) scale(1.15)}}';
                $css .= $sel . ' .olo-grid-imganim--pan-down{animation:' . $anim_name . ' ' . $img_speed . 's linear infinite alternate;}';
            } elseif ( $img_anim === 'pulse' ) {
                $css .= '@keyframes ' . $anim_name . '{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}';
                $css .= $sel . ' .olo-grid-imganim--pulse{animation:' . $anim_name . ' ' . $img_speed . 's ease-in-out infinite;}';
            } elseif ( $img_anim === 'float' ) {
                $css .= '@keyframes ' . $anim_name . '{0%,100%{transform:scale(1.06) translateY(0)}50%{transform:scale(1.06) translateY(-6px)}}';
                $css .= $sel . ' .olo-grid-imganim--float{animation:' . $anim_name . ' ' . $img_speed . 's ease-in-out infinite;}';
            } elseif ( $img_anim === 'rotate' ) {
                $css .= '@keyframes ' . $anim_name . '{from{transform:rotate(0deg) scale(1.3)}to{transform:rotate(360deg) scale(1.3)}}';
                $css .= $sel . ' .olo-grid-imganim--rotate{animation:' . $anim_name . ' ' . ( $img_speed * 4 ) . 's linear infinite;}';
            } elseif ( $img_anim === 'shimmer' ) {
                $css .= '@keyframes ' . $anim_name . '{0%,100%{filter:brightness(1)}50%{filter:brightness(1.2)}}';
                $css .= $sel . ' .olo-grid-imganim--shimmer{animation:' . $anim_name . ' ' . $img_speed . 's ease-in-out infinite;}';
            }
        }

        // Body padding
        if ( $card_style === 'minimal' ) {
            $css .= $sel . ' .olo-grid-body{padding:' . max( 4, intval( $padding / 4 ) ) . 'px 0 0;}';
        } else {
            $css .= $sel . ' .olo-grid-body{padding:' . $padding . ';}';
        }

        // Icon
        $css .= $sel . ' .olo-grid-icon{font-size:1.5em;margin-bottom:8px;display:inline-block;color:var(--olo-color-primary, #e1474f);}';

        // Title
        $title_size = absint( $s['title_size'] ?? 0 );
        $title_col  = $s['title_color'] ?? '';
        $css .= $sel . ' .olo-grid-title{margin:0 0 6px;line-height:1.3;font-weight:700;';
        if ( $title_size > 0 ) {
            $css .= 'font-size:' . $title_size . 'px;';
        } else {
            $css .= 'font-size:1.05em;';
        }
        if ( $title_col ) {
            $css .= 'color:' . esc_attr( $title_col ) . ';';
        }
        $css .= '}';

        // Content text
        $content_size = absint( $s['content_size'] ?? 0 );
        $content_col  = $s['content_color'] ?? '';
        $css .= $sel . ' .olo-grid-text{margin:0;line-height:1.55;';
        if ( $content_size > 0 ) {
            $css .= 'font-size:' . $content_size . 'px;';
        } else {
            $css .= 'font-size:0.9em;';
        }
        if ( $content_col ) {
            $css .= 'color:' . esc_attr( $content_col ) . ';';
        } else {
            $css .= 'color:#666;';
        }
        $css .= '}';

        // Badge
        $css .= $sel . ' .olo-grid-badge{position:absolute;top:10px;right:10px;z-index:3;padding:3px 10px;border-radius:4px;font-size:0.72em;font-weight:700;color:#fff;letter-spacing:0.3px;text-transform:uppercase;}';

        // Overlay on image
        if ( ! empty( $s['overlay_text'] ) ) {
            $ov_pos = $s['overlay_position'] ?? 'bottom';
            $css .= $sel . ' .olo-grid-overlay{position:absolute;left:0;right:0;z-index:2;padding:16px;}';
            $css .= $sel . ' .olo-grid-overlay .olo-grid-title{color:#fff;}';
            $css .= $sel . ' .olo-grid-overlay .olo-grid-text{color:rgba(255,255,255,0.85);}';

            if ( $ov_pos === 'bottom' ) {
                $css .= $sel . ' .olo-grid-overlay--bottom{bottom:0;background:linear-gradient(transparent,rgba(0,0,0,0.65));}';
            } elseif ( $ov_pos === 'top' ) {
                $css .= $sel . ' .olo-grid-overlay--top{top:0;background:linear-gradient(rgba(0,0,0,0.65),transparent);}';
            } elseif ( $ov_pos === 'center' ) {
                $css .= $sel . ' .olo-grid-overlay--center{top:0;bottom:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;background:rgba(0,0,0,0.45);opacity:0;transition:opacity 0.35s;}';
                $css .= $sel . ' .olo-grid-card:hover .olo-grid-overlay--center{opacity:1;}';
            }
        }

        // Equal height
        if ( ! empty( $s['equal_height'] ) ) {
            $css .= $sel . ' .olo-grid-card{display:flex;flex-direction:column;height:100%;}';
            $css .= $sel . ' .olo-grid-body{flex:1;}';
        }

        return $css;
    }

    /**
     * Parse items from array or legacy format.
     */
    private function parse_items( $raw ) {
        if ( is_array( $raw ) ) {
            $items = [];
            foreach ( $raw as $item ) {
                if ( is_array( $item ) && ! empty( $item['title'] ) ) {
                    $items[] = [
                        'title'       => $item['title'],
                        'content'     => $item['content'] ?? '',
                        'image'       => $item['image'] ?? '',
                        'hover_image' => $item['hover_image'] ?? '',
                        'hover_video' => $item['hover_video'] ?? '',
                        'tag'         => $item['tag'] ?? 'all',
                        'link'        => $item['link'] ?? '',
                        'link_target' => $item['link_target'] ?? false,
                        'badge'       => $item['badge'] ?? '',
                        'badge_color' => $item['badge_color'] ?? '',
                        'icon'        => $item['icon'] ?? '',
                    ];
                }
            }
            return $items;
        }
        return [];
    }
}
