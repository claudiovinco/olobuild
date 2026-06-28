<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Sitemap_Tile extends Olobuild_Tile_Base {

    protected $type     = 'sitemap';
    protected $name     = 'Sitemap HTML';
    protected $icon     = 'dashicons-networking';
    protected $category = 'dynamic';
    protected $defaults = [
        'preset'                  => 'classic-columns',
        'show_pages'              => true,
        'show_posts'              => true,
        'show_categories'         => true,
        'show_tags'               => false,
        'show_authors'            => false,
        'show_archives'           => false,
        'show_cpt'                => false,
        'cpt_names'               => '',
        'page_tree'               => true,
        'limit_per_section'       => 100,
        'order_by'                => 'title',
        'layout_mode'             => 'columns',
        'columns'                 => '2',
        'title_tag'               => 'h3',
        'list_style'              => 'disc',
        'indent'                  => '20',
        'gap'                     => 24,
        'item_gap'                => 6,
        'show_counter'            => false,
        'show_icons'              => false,
        'show_excerpt'            => false,
        'excerpt_length'          => 80,
        'show_date'               => false,
        'show_thumb'              => false,
        'enable_search'           => false,
        'search_placeholder'      => 'Cerca…',
        'font_family'             => 'inherit',
        'font_weight'             => '400',
        'text_transform'          => 'none',
        'letter_spacing'          => 0,
        'title_color'             => '',
        'link_color'              => '',
        'hover_color'             => '',
        'text_color'              => '',
        'bg_color'                => '',
        'accent_color'            => '',
        'container_padding'       => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16 ],
        'container_radius'        => [],
        'effect_color'            => '',
        'effect_intensity'        => 'medium',
        'effect_speed'            => 0,
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
        'exclude_ids'             => '',
        'shadow'                  => 'none',
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

    private function svg_icon( $type ) {
        $stroke = 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
        switch ( $type ) {
            case 'page':    return '<svg width="14" height="14" viewBox="0 0 24 24" ' . $stroke . '><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
            case 'post':    return '<svg width="14" height="14" viewBox="0 0 24 24" ' . $stroke . '><path d="M19 4H5a2 2 0 0 0-2 2v14l4-4h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="14" y2="13"/></svg>';
            case 'cat':     return '<svg width="14" height="14" viewBox="0 0 24 24" ' . $stroke . '><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>';
            case 'tag':     return '<svg width="14" height="14" viewBox="0 0 24 24" ' . $stroke . '><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>';
            case 'author':  return '<svg width="14" height="14" viewBox="0 0 24 24" ' . $stroke . '><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
            case 'archive': return '<svg width="14" height="14" viewBox="0 0 24 24" ' . $stroke . '><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
            case 'cpt':     return '<svg width="14" height="14" viewBox="0 0 24 24" ' . $stroke . '><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6v6H9z"/></svg>';
        }
        return '';
    }

    /**
     * Extra CSS per preset audaci.
     */
    private function get_preset_extra_css( $preset_id, $uid, $s ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprieta personalizzabile.
        return '';
    }

    private function get_excerpt( $post, $length ) {
        $raw = '';
        if ( ! empty( $post->post_excerpt ) ) {
            $raw = $post->post_excerpt;
        } else {
            $raw = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
        }
        $raw = trim( preg_replace( '/\s+/', ' ', $raw ) );
        if ( mb_strlen( $raw ) > $length ) {
            $raw = mb_substr( $raw, 0, $length ) . '…';
        }
        return $raw;
    }

    /**
     * Build a flat list of pages preserving parent->child hierarchy.
     */
    private function build_page_tree( $pages, $parent = 0, $depth = 0 ) {
        $out = [];
        foreach ( $pages as $page ) {
            if ( (int) $page->post_parent !== (int) $parent ) continue;
            $out[] = [
                'page'  => $page,
                'depth' => $depth,
            ];
            $children = $this->build_page_tree( $pages, $page->ID, $depth + 1 );
            if ( $children ) $out = array_merge( $out, $children );
        }
        return $out;
    }

    private function order_args( $order_by ) {
        switch ( $order_by ) {
            case 'date':       return [ 'orderby' => 'date',       'order' => 'DESC' ];
            case 'date_asc':   return [ 'orderby' => 'date',       'order' => 'ASC'  ];
            case 'menu_order': return [ 'orderby' => 'menu_order', 'order' => 'ASC'  ];
            default:           return [ 'orderby' => 'title',      'order' => 'ASC'  ];
        }
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $preset_id   = sanitize_key( $s['preset'] ?? 'custom' );
        $layout_mode = sanitize_key( $s['layout_mode'] ?? 'columns' );
        if ( ! in_array( $layout_mode, [ 'columns','cards','tree','index-az','cloud','compact','honeycomb','mindmap','terminal' ], true ) ) {
            $layout_mode = 'columns';
        }
        $columns    = max( 1, min( 4, absint( $s['columns'] ) ) );
        $title_tag  = in_array( $s['title_tag'], [ 'h2','h3','h4','h5' ], true ) ? $s['title_tag'] : 'h3';
        $list_style = in_array( $s['list_style'], [ 'disc','circle','arrow','check','none' ], true ) ? $s['list_style'] : 'disc';
        $indent     = max( 0, min( 60, absint( $s['indent'] ) ) );
        $gap        = max( 0, min( 80, absint( $s['gap'] ) ) );
        $item_gap   = max( 0, min( 24, absint( $s['item_gap'] ) ) );
        $limit      = max( 5, min( 500, absint( $s['limit_per_section'] ) ) );

        // TOKEN-FIRST: link/hover = primario brand (era #2563eb/#1d4ed8 blu off-brand); neutri → token
        $title_color = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #1e293b)';
        $link_color  = $this->safe_color_css( $s['link_color'] )  ?: 'var(--olo-color-primary, #e1474f)';
        $hover_color = $this->safe_color_css( $s['hover_color'] ) ?: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 80%, #000)';
        $text_color  = $this->safe_color_css( $s['text_color'] )  ?: 'var(--olo-color-text-soft, #64748b)';
        $bg_color    = $this->safe_color_css( $s['bg_color'] );
        $accent      = $this->safe_color_css( $s['accent_color'] ) ?: $link_color;

        $font_family    = $this->font_family_css( $s['font_family'] ?? 'inherit' );
        $font_weight    = in_array( $s['font_weight'], [ '300','400','500','600','700' ], true ) ? $s['font_weight'] : '400';
        $tt             = in_array( $s['text_transform'], [ 'none','uppercase','lowercase','capitalize' ], true ) ? $s['text_transform'] : 'none';
        $ls             = floatval( $s['letter_spacing'] ?? 0 );

        $show_counter = ! empty( $s['show_counter'] );
        $show_icons   = ! empty( $s['show_icons'] );
        $show_date    = ! empty( $s['show_date'] );
        $show_excerpt = ! empty( $s['show_excerpt'] );
        $show_thumb   = ! empty( $s['show_thumb'] );
        $excerpt_len  = max( 30, min( 200, absint( $s['excerpt_length'] ) ) );
        $page_tree    = ! empty( $s['page_tree'] );

        // Container padding/radius
        $cp = $s['container_padding'] ?? [];
        $cpt = is_array( $cp ) ? absint( $cp['top']    ?? 0 ) : 0;
        $cpr = is_array( $cp ) ? absint( $cp['right']  ?? 0 ) : 0;
        $cpb = is_array( $cp ) ? absint( $cp['bottom'] ?? 0 ) : 0;
        $cpl = is_array( $cp ) ? absint( $cp['left']   ?? 0 ) : 0;
        $container_radius_css = $this->build_border_radius_css( $s['container_radius'] ?? [] );

        // Parse exclude IDs
        $exclude_ids = [];
        if ( ! empty( $s['exclude_ids'] ) ) {
            $exclude_ids = array_map( 'absint', array_filter( explode( ',', $s['exclude_ids'] ) ) );
        }

        $uid = 'olo-sitemap-' . wp_unique_id();
        $order_args = $this->order_args( $s['order_by'] ?? 'title' );

        // ─── Collect sections ──────────────────────────────────────────
        $sections = [];

        // Pages
        if ( ! empty( $s['show_pages'] ) ) {
            $pages_args = [
                'post_type'      => 'page',
                'posts_per_page' => $limit,
                'post_status'    => 'publish',
                'orderby'        => $page_tree ? 'menu_order title' : $order_args['orderby'],
                'order'          => $page_tree ? 'ASC' : $order_args['order'],
            ];
            // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- esclusione post necessaria alla funzione del tile; query a volume limitato
            if ( ! empty( $exclude_ids ) ) $pages_args['post__not_in'] = $exclude_ids;
            $pages = get_posts( $pages_args );
            if ( $pages ) {
                $items = [];
                if ( $page_tree ) {
                    $tree = $this->build_page_tree( $pages, 0, 0 );
                    foreach ( $tree as $node ) {
                        $p = $node['page'];
                        $items[] = $this->build_item( $p, 'page', $node['depth'], $show_icons, $show_date, $show_excerpt, $excerpt_len, $show_thumb );
                    }
                } else {
                    foreach ( $pages as $p ) {
                        $items[] = $this->build_item( $p, 'page', 0, $show_icons, $show_date, $show_excerpt, $excerpt_len, $show_thumb );
                    }
                }
                $sections[] = [ 'heading' => olobuild_t( 'Pagine' ), 'type' => 'page', 'items' => $items ];
            }
        }

        // Posts
        if ( ! empty( $s['show_posts'] ) ) {
            $posts = get_posts( [
                'post_type'      => 'post',
                'posts_per_page' => $limit,
                'post_status'    => 'publish',
                'orderby'        => $order_args['orderby'],
                'order'          => $order_args['order'],
                // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- esclusione post necessaria alla funzione del tile; query a volume limitato
                'post__not_in'   => $exclude_ids ?: [],
            ] );
            if ( $posts ) {
                $items = [];
                foreach ( $posts as $p ) {
                    $items[] = $this->build_item( $p, 'post', 0, $show_icons, $show_date, $show_excerpt, $excerpt_len, $show_thumb );
                }
                $sections[] = [ 'heading' => olobuild_t( 'Articoli' ), 'type' => 'post', 'items' => $items ];
            }
        }

        // Categories
        if ( ! empty( $s['show_categories'] ) ) {
            $cats = get_categories( [ 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC', 'number' => $limit ] );
            if ( $cats ) {
                $items = [];
                foreach ( $cats as $cat ) {
                    if ( in_array( $cat->term_id, $exclude_ids, true ) ) continue;
                    $items[] = [
                        'title'  => $cat->name,
                        'url'    => get_category_link( $cat->term_id ),
                        'count'  => (int) $cat->count,
                        'icon'   => $show_icons ? 'cat' : '',
                        'depth'  => 0,
                        'meta'   => '',
                        'thumb'  => '',
                    ];
                }
                if ( $items ) $sections[] = [ 'heading' => olobuild_t( 'Categorie' ), 'type' => 'cat', 'items' => $items ];
            }
        }

        // Tags
        if ( ! empty( $s['show_tags'] ) ) {
            $tags = get_tags( [ 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC', 'number' => $limit ] );
            if ( $tags ) {
                $items = [];
                foreach ( $tags as $tg ) {
                    $items[] = [
                        'title'  => $tg->name,
                        'url'    => get_tag_link( $tg->term_id ),
                        'count'  => (int) $tg->count,
                        'icon'   => $show_icons ? 'tag' : '',
                        'depth'  => 0,
                        'meta'   => '',
                        'thumb'  => '',
                    ];
                }
                if ( $items ) $sections[] = [ 'heading' => olobuild_t( 'Tag' ), 'type' => 'tag', 'items' => $items ];
            }
        }

        // Authors
        if ( ! empty( $s['show_authors'] ) ) {
            $authors = get_users( [ 'has_published_posts' => [ 'post' ], 'fields' => [ 'ID', 'display_name' ], 'number' => $limit ] );
            if ( $authors ) {
                $items = [];
                foreach ( $authors as $au ) {
                    $count = count_user_posts( $au->ID, 'post', true );
                    $items[] = [
                        'title'  => $au->display_name,
                        'url'    => get_author_posts_url( $au->ID ),
                        'count'  => (int) $count,
                        'icon'   => $show_icons ? 'author' : '',
                        'depth'  => 0,
                        'meta'   => '',
                        'thumb'  => '',
                    ];
                }
                if ( $items ) $sections[] = [ 'heading' => olobuild_t( 'Autori' ), 'type' => 'author', 'items' => $items ];
            }
        }

        // Archives mensili
        if ( ! empty( $s['show_archives'] ) ) {
            $archives_obj = wp_get_archives( [ 'type' => 'monthly', 'echo' => 0, 'format' => 'custom', 'before' => '###', 'after' => '|||', 'limit' => $limit ] );
            if ( $archives_obj ) {
                $items = [];
                if ( preg_match_all( '/###<a href=\'([^\']+)\'>([^<]+)<\/a>\|\|\|/', $archives_obj, $matches ) ) {
                    foreach ( $matches[1] as $i => $url ) {
                        $items[] = [
                            'title'  => $matches[2][ $i ],
                            'url'    => $url,
                            'count'  => 0,
                            'icon'   => $show_icons ? 'archive' : '',
                            'depth'  => 0,
                            'meta'   => '',
                            'thumb'  => '',
                        ];
                    }
                }
                if ( $items ) $sections[] = [ 'heading' => olobuild_t( 'Archivi' ), 'type' => 'archive', 'items' => $items ];
            }
        }

        // CPT
        if ( ! empty( $s['show_cpt'] ) && ! empty( $s['cpt_names'] ) ) {
            $cpt_list = array_map( 'sanitize_key', array_filter( array_map( 'trim', explode( ',', $s['cpt_names'] ) ) ) );
            foreach ( $cpt_list as $cpt ) {
                if ( ! post_type_exists( $cpt ) ) continue;
                $cpt_obj = get_post_type_object( $cpt );
                $cpt_posts = get_posts( [
                    'post_type'      => $cpt,
                    'posts_per_page' => $limit,
                    'post_status'    => 'publish',
                    'orderby'        => $order_args['orderby'],
                    'order'          => $order_args['order'],
                    // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- esclusione post necessaria alla funzione del tile; query a volume limitato
                    'post__not_in'   => $exclude_ids ?: [],
                ] );
                if ( $cpt_posts ) {
                    $items = [];
                    foreach ( $cpt_posts as $cp ) {
                        $items[] = $this->build_item( $cp, 'cpt', 0, $show_icons, $show_date, $show_excerpt, $excerpt_len, $show_thumb );
                    }
                    $heading = $cpt_obj ? $cpt_obj->labels->name : ucfirst( $cpt );
                    $sections[] = [ 'heading' => $heading, 'type' => 'cpt', 'items' => $items ];
                }
            }
        }

        if ( empty( $sections ) ) {
            return '<div class="olo-sitemap" style="text-align:center;padding:20px;color:#9CA3AF;">' . olobuild_t( 'Nessun contenuto trovato per la sitemap.' ) . '</div>';
        }

        // ─── Build markup ──────────────────────────────────────────────
        $bullet_chars = [ 'arrow' => '→', 'check' => '✓' ];
        $real_list_style = in_array( $list_style, [ 'disc','circle','none' ], true ) ? $list_style : 'none';

        // Wrap container styles
        $wrap_styles  = "font-family:{$font_family};font-weight:{$font_weight};text-transform:{$tt};";
        if ( $ls > 0 ) $wrap_styles .= "letter-spacing:{$ls}px;";
        if ( $bg_color ) $wrap_styles .= "background:{$bg_color};";
        $wrap_styles .= "padding:{$cpt}px {$cpr}px {$cpb}px {$cpl}px;";
        if ( $container_radius_css ) $wrap_styles .= "border-radius:{$container_radius_css};";

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colors, absint()/clamped integers, in_array() whitelists and the internally generated $uid.
        ?>
        <style>
            .<?php echo $uid; ?> { position:relative; gap:<?php echo (int) $gap; ?>px; }
            <?php // Default layout = columns ?>
            .<?php echo $uid; ?>.olo-sm-mode-columns,
            .<?php echo $uid; ?>.olo-sm-mode-cards { display:grid; grid-template-columns:repeat(<?php echo (int) $columns; ?>, 1fr); }
            .<?php echo $uid; ?>.olo-sm-mode-compact { display:grid; grid-template-columns:repeat(<?php echo (int) $columns; ?>, 1fr); }
            .<?php echo $uid; ?>.olo-sm-mode-tree,
            .<?php echo $uid; ?>.olo-sm-mode-index-az,
            .<?php echo $uid; ?>.olo-sm-mode-cloud,
            .<?php echo $uid; ?>.olo-sm-mode-terminal { display:flex; flex-direction:column; }
            @media (max-width:640px) {
                .<?php echo $uid; ?>.olo-sm-mode-columns,
                .<?php echo $uid; ?>.olo-sm-mode-cards,
                .<?php echo $uid; ?>.olo-sm-mode-compact { grid-template-columns:1fr; }
            }

            .<?php echo $uid; ?> .olo-sm-title { color:<?php echo $title_color; ?>; margin:0 0 8px; font-size:1.1em; font-weight:600; display:flex; align-items:center; gap:6px; }
            .<?php echo $uid; ?> .olo-sm-counter { display:inline-block; background:rgba(0,0,0,0.06); color:<?php echo $text_color; ?>; padding:1px 7px; border-radius:999px; font-size:0.7em; font-weight:600; margin-left:4px; vertical-align:middle; }
            .<?php echo $uid; ?> ul { list-style:none; padding:0; margin:0; line-height:1.6; display:flex; flex-direction:column; gap:<?php echo (int) $item_gap; ?>px; }
            .<?php echo $uid; ?> ul.is-bulleted { list-style-type:<?php echo esc_attr( $real_list_style ); ?>; padding-left:<?php echo (int) $indent; ?>px; }
            .<?php echo $uid; ?> a { color:<?php echo $link_color; ?>; text-decoration:none; transition:color 0.2s; display:inline-flex; align-items:center; gap:5px; }
            .<?php echo $uid; ?> a:hover { color:<?php echo $hover_color; ?>; text-decoration:underline; }
            .<?php echo $uid; ?> a:focus-visible { outline:none; box-shadow:0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); border-radius:3px; }
            .<?php echo $uid; ?> .olo-sm-icon { color:<?php echo $accent; ?>; flex-shrink:0; display:inline-flex; align-items:center; }
            .<?php echo $uid; ?> .olo-sm-meta { font-size:0.8em; color:<?php echo $text_color; ?>; margin-left:8px; opacity:0.75; }
            .<?php echo $uid; ?> .olo-sm-excerpt { display:block; font-size:0.85em; color:<?php echo $text_color; ?>; margin-top:2px; opacity:0.8; line-height:1.4; }
            .<?php echo $uid; ?> .olo-sm-thumb { width:32px; height:32px; object-fit:cover; border-radius:4px; margin-right:6px; vertical-align:middle; flex-shrink:0; }
            .<?php echo $uid; ?> .olo-sm-bullet-arrow li::marker, .<?php echo $uid; ?> .olo-sm-bullet-check li::marker { content:''; }
            .<?php echo $uid; ?> .olo-sm-bullet-arrow li::before { content:'→  '; color:<?php echo $accent; ?>; font-weight:700; }
            .<?php echo $uid; ?> .olo-sm-bullet-check li::before { content:'✓  '; color:<?php echo $accent; ?>; font-weight:700; }

            <?php // Cards mode ?>
            .<?php echo $uid; ?>.olo-sm-mode-cards .olo-sm-section { background:rgba(255,255,255,0.95); padding:18px 20px; border-radius:10px; box-shadow:0 2px 12px rgba(15,23,42,0.06); }

            <?php // Tree mode ?>
            .<?php echo $uid; ?>.olo-sm-mode-tree .olo-sm-section ul { padding-left:0; }
            .<?php echo $uid; ?>.olo-sm-mode-tree li { position:relative; }
            .<?php echo $uid; ?>.olo-sm-mode-tree li[data-depth="1"] { padding-left:18px; }
            .<?php echo $uid; ?>.olo-sm-mode-tree li[data-depth="2"] { padding-left:36px; }
            .<?php echo $uid; ?>.olo-sm-mode-tree li[data-depth="3"] { padding-left:54px; }
            .<?php echo $uid; ?>.olo-sm-mode-tree li[data-depth="4"] { padding-left:72px; }
            .<?php echo $uid; ?>.olo-sm-mode-tree li[data-depth]:not([data-depth="0"])::before {
                content:''; position:absolute; left:8px; top:0; bottom:50%; width:1px; background:rgba(0,0,0,0.15);
            }
            .<?php echo $uid; ?>.olo-sm-mode-tree li[data-depth]:not([data-depth="0"])::after {
                content:''; position:absolute; left:8px; top:50%; width:10px; height:1px; background:rgba(0,0,0,0.15);
            }

            <?php // Index A-Z: handled inline (renders flat list with letter headers) ?>

            <?php // Cloud mode ?>
            .<?php echo $uid; ?>.olo-sm-mode-cloud .olo-sm-section ul {
                flex-direction:row; flex-wrap:wrap; align-items:center; gap:6px 14px;
            }
            .<?php echo $uid; ?>.olo-sm-mode-cloud li { padding:0; display:inline; }

            <?php // Compact mode ?>
            .<?php echo $uid; ?>.olo-sm-mode-compact .olo-sm-section ul { flex-direction:row; flex-wrap:wrap; gap:6px; }
            .<?php echo $uid; ?>.olo-sm-mode-compact li { padding:0; }
            .<?php echo $uid; ?>.olo-sm-mode-compact li a {
                background:rgba(0,0,0,0.04); padding:5px 12px; border-radius:999px; font-size:0.92em;
            }
            .<?php echo $uid; ?>.olo-sm-mode-compact li a:hover { background:<?php echo $accent; ?>; color:#fff; text-decoration:none; }

            <?php // Search filter ?>
            .<?php echo $uid; ?> .olo-sm-search-wrap { margin-bottom:16px; }
            .<?php echo $uid; ?> .olo-sm-search {
                width:100%; padding:10px 14px; border:1px solid rgba(0,0,0,0.15); border-radius:8px;
                font-size:14px; box-sizing:border-box; background:rgba(255,255,255,0.6);
            }
            .<?php echo $uid; ?> .olo-sm-search:focus { outline:none; border-color:<?php echo $accent; ?>; box-shadow:0 0 0 3px rgba(0,0,0,0.04); }
            .<?php echo $uid; ?> .olo-sm-hidden { display:none !important; }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php $extra = $this->get_preset_extra_css( $preset_id, $uid, $s ); ?>
        <?php $extra .= $this->build_wow_effects_css( $s, '.' . $uid, '.olo-sitemap-title' ); ?>
        <?php if ( $extra ) echo '<style>' . $extra . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_wow_effects_css() from clamped numbers, whitelisted enums and safe_color_css() colors (get_preset_extra_css() returns '') ?>

        <?php // Mind-map angular positioning (post-render via inline style) ?>
        <?php
        $sections_count = count( $sections );
        $is_mindmap = ( $preset_id === 'mind-map' ) || ( $layout_mode === 'mindmap' );
        $mm_styles = '';
        if ( $is_mindmap && $sections_count > 0 ) {
            for ( $i = 0; $i < $sections_count; $i++ ) {
                $angle = ( 360 / $sections_count ) * $i - 90;
                $rad   = $angle * M_PI / 180;
                $tx    = round( cos( $rad ) * 180 );
                $ty    = round( sin( $rad ) * 180 );
                $idx   = $i + 1;
                $mm_styles .= ".{$uid} .olo-sm-section:nth-child({$idx}){--olo-mm-tx:{$tx}px;--olo-mm-ty:{$ty}px;transform:translate(calc(-50% + {$tx}px),calc(-50% + {$ty}px))}";
            }
            echo '<style>' . $mm_styles . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS built above only from the internally generated $uid and round()'d numeric offsets
        }
        ?>

        <div class="olo-sitemap olo-sm-preset-<?php echo esc_attr( $preset_id ); ?> olo-sm-mode-<?php echo esc_attr( $layout_mode ); ?> <?php echo esc_attr( $uid ); ?>" style="<?php echo esc_attr( $wrap_styles ); ?>">
            <?php if ( ! empty( $s['enable_search'] ) ) : ?>
                <div class="olo-sm-search-wrap">
                    <input type="text" class="olo-sm-search" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" />
                </div>
            <?php endif; ?>

            <?php foreach ( $sections as $section ) :
                $bullet_class = '';
                if ( $list_style === 'arrow' ) $bullet_class = 'olo-sm-bullet-arrow';
                elseif ( $list_style === 'check' ) $bullet_class = 'olo-sm-bullet-check';
                $ul_class = 'olo-sm-list ' . $bullet_class;
                if ( in_array( $list_style, [ 'disc','circle' ], true ) && in_array( $layout_mode, [ 'columns','cards' ], true ) ) {
                    $ul_class .= ' is-bulleted';
                }
            ?>
            <div class="olo-sm-section">
                <<?php echo tag_escape( $title_tag ); ?> class="olo-sm-title">
                    <?php if ( $show_icons && $section['type'] ) : ?>
                        <span class="olo-sm-icon"><?php echo $this->svg_icon( $section['type'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded svg_icon() map ?></span>
                    <?php endif; ?>
                    <?php echo esc_html( $section['heading'] ); ?>
                    <?php if ( $show_counter ) : ?>
                        <span class="olo-sm-counter"><?php echo (int) count( $section['items'] ); ?></span>
                    <?php endif; ?>
                </<?php echo tag_escape( $title_tag ); ?>>

                <?php if ( $layout_mode === 'index-az' ) : ?>
                    <?php $this->render_index_az( $section['items'], $accent ); ?>
                <?php elseif ( $layout_mode === 'cloud' ) : ?>
                    <?php $this->render_cloud( $section['items'], $accent, $link_color ); ?>
                <?php else : ?>
                    <ul class="<?php echo esc_attr( $ul_class ); ?>">
                        <?php foreach ( $section['items'] as $item ) :
                            $depth = isset( $item['depth'] ) ? intval( $item['depth'] ) : 0;
                        ?>
                        <li data-depth="<?php echo esc_attr( $depth ); ?>" data-search="<?php echo esc_attr( strtolower( $item['title'] ) ); ?>">
                            <?php if ( $show_thumb && ! empty( $item['thumb'] ) ) : ?>
                                <img class="olo-sm-thumb" src="<?php echo esc_url( $item['thumb'] ); ?>" alt="" />
                            <?php endif; ?>
                            <a href="<?php echo esc_url( $item['url'] ); ?>">
                                <?php if ( $show_icons && ! empty( $item['icon'] ) && $layout_mode !== 'tree' ) : ?>
                                    <span class="olo-sm-icon"><?php echo $this->svg_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG markup from the hardcoded svg_icon() map ?></span>
                                <?php endif; ?>
                                <span><?php echo esc_html( $item['title'] ); ?></span>
                                <?php if ( ! empty( $item['count'] ) ) : ?>
                                    <span class="olo-sm-counter"><?php echo intval( $item['count'] ); ?></span>
                                <?php endif; ?>
                            </a>
                            <?php if ( $show_date && ! empty( $item['meta'] ) ) : ?>
                                <span class="olo-sm-meta"><?php echo esc_html( $item['meta'] ); ?></span>
                            <?php endif; ?>
                            <?php if ( $show_excerpt && ! empty( $item['excerpt'] ) ) : ?>
                                <span class="olo-sm-excerpt"><?php echo esc_html( $item['excerpt'] ); ?></span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ( ! empty( $s['enable_search'] ) ) : ?>
        <script>
        (function(){
            var root = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if (!root) return;
            var input = root.querySelector('.olo-sm-search');
            if (!input) return;
            input.addEventListener('input', function(){
                var q = (input.value || '').toLowerCase().trim();
                var lis = root.querySelectorAll('li[data-search]');
                for (var i = 0; i < lis.length; i++) {
                    var s = lis[i].getAttribute('data-search') || '';
                    if (q === '') { lis[i].classList.remove('olo-sm-hidden'); }
                    else if (s.indexOf(q) === -1) { lis[i].classList.add('olo-sm-hidden'); }
                    else { lis[i].classList.remove('olo-sm-hidden'); }
                }
                var sections = root.querySelectorAll('.olo-sm-section');
                for (var j = 0; j < sections.length; j++) {
                    var visible = sections[j].querySelectorAll('li:not(.olo-sm-hidden)').length;
                    var total   = sections[j].querySelectorAll('li').length;
                    if (q !== '') { if (visible === 0) { sections[j].classList.add('olo-sm-hidden'); } else { sections[j].classList.remove('olo-sm-hidden'); } }
                    else { sections[j].classList.remove('olo-sm-hidden'); }
                }
            });
        })();
        </script>
        <?php endif; ?>

        <?php
        // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by build_border_hover_css()/build_border_effect_css() base-class helpers
        }
        return ob_get_clean();
    }

    private function build_item( $post, $type, $depth, $show_icons, $show_date, $show_excerpt, $excerpt_len, $show_thumb ) {
        $thumb = '';
        if ( $show_thumb ) {
            $tid = get_post_thumbnail_id( $post->ID );
            if ( $tid ) {
                $img = wp_get_attachment_image_src( $tid, 'thumbnail' );
                if ( $img ) $thumb = $img[0];
            }
        }
        $meta = '';
        if ( $show_date ) {
            $meta = get_the_date( get_option( 'date_format' ), $post );
        }
        $excerpt = '';
        if ( $show_excerpt ) {
            $excerpt = $this->get_excerpt( $post, $excerpt_len );
        }
        return [
            'title'   => get_the_title( $post ),
            'url'     => get_permalink( $post->ID ),
            'count'   => 0,
            'icon'    => $type,
            'depth'   => $depth,
            'meta'    => $meta,
            'excerpt' => $excerpt,
            'thumb'   => $thumb,
        ];
    }

    private function render_index_az( $items, $accent ) {
        $groups = [];
        foreach ( $items as $it ) {
            $first = mb_strtoupper( mb_substr( $it['title'], 0, 1 ) );
            if ( ! preg_match( '/[A-Z]/u', $first ) ) $first = '#';
            if ( ! isset( $groups[ $first ] ) ) $groups[ $first ] = [];
            $groups[ $first ][] = $it;
        }
        ksort( $groups );
        echo '<div class="olo-sm-index-az">';
        foreach ( $groups as $letter => $group ) {
            echo '<div class="olo-sm-az-group">';
            echo '<div class="olo-sm-az-letter" style="font-size:1.4em;font-weight:700;color:' . esc_attr( $accent ) . ';margin:14px 0 6px;border-bottom:2px solid ' . esc_attr( $accent ) . ';padding-bottom:4px;">' . esc_html( $letter ) . '</div>';
            echo '<ul class="olo-sm-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:4px 16px;">';
            foreach ( $group as $it ) {
                echo '<li data-search="' . esc_attr( strtolower( $it['title'] ) ) . '"><a href="' . esc_url( $it['url'] ) . '">' . esc_html( $it['title'] ) . '</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        echo '</div>';
    }

    private function render_cloud( $items, $accent, $link ) {
        $max_count = 1;
        foreach ( $items as $it ) {
            if ( ! empty( $it['count'] ) && $it['count'] > $max_count ) $max_count = (int) $it['count'];
        }
        echo '<ul class="olo-sm-list olo-sm-cloud" style="display:flex;flex-wrap:wrap;align-items:center;gap:6px 14px;">';
        foreach ( $items as $it ) {
            $w = isset( $it['count'] ) ? (int) $it['count'] : 1;
            if ( $max_count > 0 ) {
                $size = 0.8 + ( $w / max( 1, $max_count ) ) * 0.9;
            } else {
                $size = 1;
            }
            $size = round( $size, 2 );
            echo '<li data-search="' . esc_attr( strtolower( $it['title'] ) ) . '" style="display:inline">';
            echo '<a href="' . esc_url( $it['url'] ) . '" style="font-size:' . (float) $size . 'em;font-weight:' . ( $size > 1.3 ? 700 : ( $size > 1 ? 600 : 400 ) ) . ';color:' . esc_attr( $link ) . ';">' . esc_html( $it['title'] ) . '</a>';
            echo '</li>';
        }
        echo '</ul>';
    }
}
