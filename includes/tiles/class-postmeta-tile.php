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
        'text_color'          => '#9CA3AF',
        'link_color'          => '#6366F1',
        'icon_color'          => '#6B7280',
        'font_size'           => '14',
        'author_link'         => true,
        'category_link'       => true,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $post_id = get_the_ID();
        $post    = get_post( $post_id );

        $text_color = $this->safe_color_css( $s['text_color'] );
        $link_color = $this->safe_color_css( $s['link_color'] );
        $icon_color = $this->safe_color_css( $s['icon_color'] );
        $font_size  = max( 10, min( 24, absint( $s['font_size'] ) ) );
        $separator  = esc_html( $s['separator'] );
        $layout     = $s['layout'] === 'stacked' ? 'stacked' : 'inline';
        $show_icons = $s['icon_style'] === 'before';

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

        // Date
        if ( ! empty( $s['show_date'] ) ) {
            $date_text = '';
            if ( $post ) {
                $date_text = get_the_date( $s['date_format'], $post );
            }
            if ( ! $date_text ) {
                $date_text = wp_date( $s['date_format'] );
            }
            $items[] = $this->build_meta_item(
                $date_text,
                '',
                $show_icons ? $icon_svg['date'] : '',
                $text_color,
                $icon_color
            );
        }

        // Author
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
            $items[] = $this->build_meta_item(
                $author_name,
                $author_url,
                $show_icons ? $icon_svg['author'] : '',
                $author_url ? $link_color : $text_color,
                $icon_color
            );
        }

        // Categories
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
            $items[] = $this->build_meta_item(
                implode( ', ', $cats ),
                '',
                $show_icons ? $icon_svg['category'] : '',
                $text_color,
                $icon_color,
                true
            );
        }

        // Tags
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
                $items[] = $this->build_meta_item(
                    implode( ', ', $tag_list ),
                    '',
                    $show_icons ? $icon_svg['tag'] : '',
                    $text_color,
                    $icon_color,
                    true
                );
            }
        }

        // Comments count
        if ( ! empty( $s['show_comments_count'] ) ) {
            $count = 0;
            if ( $post ) {
                $count = (int) get_comments_number( $post->ID );
            }
            $label = $count . ' ' . olo_t( $count === 1 ? 'commento' : 'commenti' );
            $items[] = $this->build_meta_item(
                $label,
                '',
                $show_icons ? $icon_svg['comment'] : '',
                $text_color,
                $icon_color
            );
        }

        // Reading time
        if ( ! empty( $s['show_reading_time'] ) ) {
            $minutes = 1;
            if ( $post ) {
                $content    = get_the_content( null, false, $post );
                $word_count = str_word_count( wp_strip_all_tags( $content ) );
                $minutes    = max( 1, (int) ceil( $word_count / 200 ) );
            }
            $label = $minutes . ' ' . olo_t( 'min di lettura' );
            $items[] = $this->build_meta_item(
                $label,
                '',
                $show_icons ? $icon_svg['clock'] : '',
                $text_color,
                $icon_color
            );
        }

        if ( empty( $items ) ) {
            return '';
        }

        // Build separator
        $sep_html = '';
        if ( $layout === 'inline' ) {
            $sep_html = '<span class="olo-postmeta-sep" style="color:' . $text_color . ';">' . $separator . '</span>';
        }

        // Layout styles
        $wrap_style = 'font-size:' . $font_size . 'px;line-height:1.6;';
        if ( $layout === 'stacked' ) {
            $wrap_style .= 'display:flex;flex-direction:column;gap:4px;';
        } else {
            $wrap_style .= 'display:flex;flex-wrap:wrap;align-items:center;gap:0;';
        }

        ob_start();
        ?>
        <div class="olo-postmeta" style="<?php echo esc_attr( $wrap_style ); ?>">
            <?php
            $total = count( $items );
            foreach ( $items as $i => $item_html ) {
                if ( $i > 0 ) {
                    if ( $layout === 'inline' ) {
                        echo $sep_html;
                    }
                }
                echo $item_html;
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Build a single meta item HTML.
     *
     * @param string $text       The text or HTML content.
     * @param string $url        Optional link URL.
     * @param string $icon_svg   Optional SVG icon HTML.
     * @param string $color      Text/link color.
     * @param string $icon_color Icon color.
     * @param bool   $raw_html   If true, $text is already HTML (contains links).
     * @return string
     */
    private function build_meta_item( $text, $url = '', $icon_svg = '', $color = '', $icon_color = '', $raw_html = false ) {
        $html = '<span class="olo-postmeta-item" style="display:inline-flex;align-items:center;gap:5px;color:' . $color . ';">';

        if ( $icon_svg ) {
            $html .= '<span style="color:' . $icon_color . ';display:inline-flex;flex-shrink:0;">' . $icon_svg . '</span>';
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
