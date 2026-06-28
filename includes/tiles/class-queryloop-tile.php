<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Queryloop_Tile extends Olobuild_Tile_Base {

    protected $type     = 'queryloop';
    protected $name     = 'Query Loop';
    protected $icon     = 'dashicons-database';
    protected $category = 'dynamic';
    protected $defaults = [
        'preset'          => 'magazine-trio',
        'post_type'       => 'post',
        'posts_per_page'  => '6',
        'orderby'         => 'date',
        'order'           => 'DESC',
        'taxonomy'        => '',
        'terms'           => '',
        'offset'          => '0',
        'exclude_current' => true,
        'layout'          => 'grid',
        'columns'         => '3',
        'gap'             => '30',
        'show_image'      => true,
        'show_title'      => true,
        'show_excerpt'    => true,
        'show_date'       => true,
        'show_author'     => false,
        'show_category'   => true,
        'show_read_more'  => true,
        'read_more_text'  => 'Leggi tutto',
        'excerpt_length'  => '20',
        'image_ratio'     => '16:9',
        'pagination_type' => 'none',
        'title_tag'       => 'h3',
        'title_color'     => '',
        'text_color'      => '',
        'meta_color'      => '',
        'link_color'      => '',
        'bg_color'        => '',
        'hover_bg'        => '',
        'accent_color'    => '',
        'overlay_color'   => '#000000',
        'overlay_opacity' => 60,
        'card_style'      => 'none',
        'loop_template_id' => '',
        // Magic features
        'show_reading_time'      => false,
        'new_badge'              => false,
        'new_badge_days'         => 7,
        'new_badge_text'         => 'Nuovo',
        'trending_badge'         => false,
        'show_comment_count'     => false,
        'enable_search'          => false,
        'search_placeholder'     => 'Cerca…',
        'enable_sort_ui'         => false,
        'enable_taxonomy_tabs'   => false,
        'taxonomy_tabs_taxonomy' => 'category',
        'timeline_group_by'      => 'month',
        'sticky_filter_rail'     => false,
        'skeleton_loading'       => false,
        // Hover
        'hover_effect'    => 'none',
        // Tipografia
        'font_family'     => 'inherit',
        'title_weight'    => '700',
        'text_transform'  => 'none',
        'letter_spacing'  => 0,
        // Container
        'container_padding' => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
        'container_radius'  => [],
        'card_radius'       => [ 'tl' => 8, 'tr' => 8, 'br' => 8, 'bl' => 8 ],
        // Effetti preset
        'effect_color'      => '',
        'effect_intensity'  => 'medium',
        'effect_speed'      => 0,
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
    ];

    /** Track whether the AJAX handler has been registered. */
    private static $ajax_registered = false;

    public function __construct() {
        if ( ! self::$ajax_registered ) {
            add_action( 'wp_ajax_olo_queryloop_page', [ $this, 'ajax_load_page' ] );
            add_action( 'wp_ajax_nopriv_olo_queryloop_page', [ $this, 'ajax_load_page' ] );
            self::$ajax_registered = true;
        }
    }

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

    private function is_post_new( $post, $days ) {
        if ( ! $post || ! $days ) return false;
        $time = get_post_time( 'U', true, $post );
        return $time && ( time() - $time ) < $days * DAY_IN_SECONDS;
    }

    private function get_reading_time( $post ) {
        $words = str_word_count( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
        return max( 1, (int) ceil( $words / 200 ) );
    }

    /**
     * Trending IDs cache: top 3 by comment count from current loop.
     */
    private $trending_ids = [];
    private function compute_trending_ids( $posts ) {
        $list = [];
        foreach ( $posts as $p ) {
            $list[ $p->ID ] = (int) get_comments_number( $p->ID );
        }
        arsort( $list );
        $this->trending_ids = array_slice( array_keys( $list ), 0, 3, true );
    }

    /**
     * CSS specifico per layout speciali (oltre grid/list/masonry/carousel).
     */
    private function get_layout_extra_css( $layout, $uid, $columns, $gap, $accent ) {
        switch ( $layout ) {
            case 'magazine-trio':
                return "#{$uid} .olo-ql-container{display:grid;grid-template-columns:2fr 1fr;grid-template-rows:auto auto;gap:{$gap}px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child{grid-row:span 2;position:relative;min-height:380px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-img{padding-bottom:0 !important;height:100%;position:absolute;inset:0}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-body{position:absolute;left:0;right:0;bottom:0;padding:24px;background:linear-gradient(180deg,transparent 0%,rgba(0,0,0,0.85) 100%);color:#fff}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-title,#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-title a,#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-excerpt{color:#fff !important}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-title{font-size:1.8em;line-height:1.15}"
                     . "@media(max-width:768px){#{$uid} .olo-ql-container{grid-template-columns:1fr}#{$uid} .olo-ql-container > .olo-ql-card:first-child{grid-row:auto;min-height:auto}#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-body{position:static;background:transparent;color:inherit;padding:16px}}";
            case 'magazine-hero':
                return "#{$uid} .olo-ql-container{display:grid;grid-template-columns:repeat({$columns},1fr);gap:{$gap}px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child{grid-column:1/-1;position:relative;min-height:420px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-img{padding-bottom:0 !important;height:100%;position:absolute;inset:0}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-body{position:absolute;left:0;right:0;bottom:0;padding:32px;background:linear-gradient(180deg,transparent 0%,rgba(0,0,0,0.85) 100%);color:#fff}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-title,#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-title a{color:#fff !important;font-size:2.2em;line-height:1.1}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:first-child .olo-ql-excerpt{color:rgba(255,255,255,0.85) !important;font-size:1.05em;max-width:680px}";
            case 'alternating':
                return "#{$uid} .olo-ql-container{display:flex;flex-direction:column;gap:{$gap}px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card{display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:center}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:nth-child(even){direction:rtl}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:nth-child(even) > *{direction:ltr}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-body{padding:0 !important}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-title{font-size:1.6em;line-height:1.2}"
                     . "@media(max-width:768px){#{$uid} .olo-ql-container > .olo-ql-card{grid-template-columns:1fr;direction:ltr !important}}";
            case 'list-rich':
                return "#{$uid} .olo-ql-container{display:flex;flex-direction:column;gap:{$gap}px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card{display:grid;grid-template-columns:240px 1fr;gap:24px;align-items:start;padding:0 !important;background:transparent !important;border:0 !important;box-shadow:none !important}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-img{padding-bottom:0 !important;height:160px;border-radius:8px;background-position:center}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-img-link{display:block;height:100%}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-body{padding:0 !important}"
                     . "@media(max-width:640px){#{$uid} .olo-ql-container > .olo-ql-card{grid-template-columns:1fr}#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-img{height:200px}}";
            case 'bento':
                return "#{$uid} .olo-ql-container{display:grid;grid-template-columns:repeat({$columns},1fr);grid-auto-rows:minmax(180px,auto);gap:{$gap}px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:nth-child(7n+1){grid-column:span 2;grid-row:span 2;position:relative}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:nth-child(7n+1) .olo-ql-img{padding-bottom:0 !important;height:100%;position:absolute;inset:0}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:nth-child(7n+1) .olo-ql-body{position:absolute;left:0;right:0;bottom:0;padding:20px;background:linear-gradient(180deg,transparent,rgba(0,0,0,0.8));color:#fff}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:nth-child(7n+1) .olo-ql-title,#{$uid} .olo-ql-container > .olo-ql-card:nth-child(7n+1) .olo-ql-title a{color:#fff !important;font-size:1.5em}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card:nth-child(7n+4){grid-column:span 2}";
            case 'newspaper':
                return "#{$uid} .olo-ql-container{column-count:{$columns};column-gap:{$gap}px;font-family:Georgia,serif}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card{break-inside:avoid;margin-bottom:{$gap}px;background:transparent;border:0;padding:0 0 14px;border-bottom:1px solid rgba(0,0,0,0.12);box-shadow:none}"
                     . "#{$uid} .olo-ql-title{font-family:Georgia,serif;font-size:1.25em;line-height:1.25}"
                     . "#{$uid} .olo-ql-img{margin-bottom:8px}";
            case 'timeline':
                return "#{$uid} .olo-ql-container{display:flex;flex-direction:column;gap:0;position:relative}"
                     . "#{$uid} .olo-ql-container::before{content:'';position:absolute;left:120px;top:0;bottom:0;width:2px;background:rgba(0,0,0,0.08)}"
                     . "#{$uid} .olo-ql-tl-group{display:grid;grid-template-columns:140px 1fr;gap:0;align-items:start;padding:8px 0}"
                     . "#{$uid} .olo-ql-tl-date{font-family:ui-monospace,monospace;font-size:11px;letter-spacing:2px;color:{$accent};text-transform:uppercase;font-weight:600;position:relative;padding-top:18px}"
                     . "#{$uid} .olo-ql-tl-date::after{content:'';position:absolute;left:118px;top:18px;width:8px;height:8px;border-radius:50%;background:{$accent};box-shadow:0 0 0 4px #fff,0 0 0 5px {$accent}}"
                     . "#{$uid} .olo-ql-tl-items{display:flex;flex-direction:column;gap:14px;padding:14px 0 14px 30px}"
                     . "#{$uid} .olo-ql-tl-items > .olo-ql-card{margin:0}";
            case 'ticker-strip':
                $speed = max( 15, min( 120, absint( $columns ) * 8 ) );
                return "#{$uid} .olo-ql-container{display:flex;overflow:hidden;width:100%;mask-image:linear-gradient(90deg,transparent,#000 5%,#000 95%,transparent)}"
                     . "#{$uid} .olo-ql-track{display:flex;gap:{$gap}px;animation:olo-ql-tape-{$uid} {$speed}s linear infinite;will-change:transform}"
                     . "#{$uid} .olo-ql-track > .olo-ql-card{flex-shrink:0;width:280px}"
                     . "@keyframes olo-ql-tape-{$uid}{from{transform:translateX(0)}to{transform:translateX(calc(-50% - {$gap}px / 2))}}";
            case 'stacked':
                return "#{$uid} .olo-ql-container{display:flex;flex-direction:column;gap:{$gap}px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card{position:relative;overflow:hidden;min-height:320px}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-img{padding-bottom:0 !important;height:100%;position:absolute;inset:0}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-body{position:absolute;left:0;right:0;bottom:0;padding:32px;background:linear-gradient(180deg,transparent,rgba(0,0,0,0.85));color:#fff}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-title,#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-title a{color:#fff !important;font-size:2em}"
                     . "#{$uid} .olo-ql-container > .olo-ql-card .olo-ql-excerpt{color:rgba(255,255,255,0.85) !important;max-width:640px}";
        }
        return '';
    }

    private function get_hover_effect_css( $fx, $uid, $accent ) {
        $sel = "#{$uid} .olo-ql-card";
        switch ( $fx ) {
            case 'lift':
                return "{$sel}{transition:transform 280ms cubic-bezier(0.34,1.56,0.64,1),box-shadow 280ms ease}"
                     . "{$sel}:hover{transform:translateY(-6px);box-shadow:0 16px 32px rgba(15,23,42,0.12)}";
            case 'image-zoom':
                return "{$sel} .olo-ql-img{transition:transform 600ms ease}{$sel}:hover .olo-ql-img{transform:scale(1.06)}"
                     . "{$sel} .olo-ql-img-auto{transition:transform 600ms ease}{$sel}:hover .olo-ql-img-auto{transform:scale(1.06)}";
            case 'title-underline':
                return "{$sel} .olo-ql-title a{position:relative;background-image:linear-gradient({$accent},{$accent});background-size:0 2px;background-repeat:no-repeat;background-position:left bottom;transition:background-size 300ms ease;padding-bottom:2px}"
                     . "{$sel}:hover .olo-ql-title a{background-size:100% 2px}";
            case 'arrow-slide':
                return "{$sel} .olo-ql-readmore{transition:transform 250ms ease;display:inline-block}"
                     . "{$sel}:hover .olo-ql-readmore{transform:translateX(8px)}";
            case 'color-tint':
                return "{$sel} .olo-ql-img{transition:filter 400ms ease}"
                     . "{$sel}:hover .olo-ql-img{filter:saturate(1.4) contrast(1.05)}"
                     . "{$sel}:hover{box-shadow:0 0 0 2px {$accent}}";
            case 'border-grow':
                return "{$sel}{position:relative}{$sel}::after{content:'';position:absolute;left:0;bottom:0;width:0;height:3px;background:{$accent};transition:width 300ms ease;z-index:2}"
                     . "{$sel}:hover::after{width:100%}";
            case 'glassy-border':
                return "{$sel}{transition:box-shadow 300ms ease}"
                     . "{$sel}:hover{box-shadow:0 0 0 1px rgba(255,255,255,0.6) inset, 0 0 0 1px {$accent}, 0 12px 32px rgba(0,0,0,0.1)}";
        }
        return '';
    }

    private function get_magic_css( $s, $uid, $accent ) {
        $css = '';
        if ( ! empty( $s['new_badge'] ) ) {
            $css .= "#{$uid} .olo-ql-card .olo-ql-new-badge{position:absolute;top:12px;left:12px;background:{$accent};color:#fff;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;z-index:3;box-shadow:0 4px 8px rgba(0,0,0,0.15)}";
        }
        if ( ! empty( $s['trending_badge'] ) ) {
            $css .= "#{$uid} .olo-ql-card .olo-ql-trend-badge{position:absolute;top:12px;right:12px;background:#0f172a;color:#fff;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;z-index:3;display:inline-flex;align-items:center;gap:4px}";
            $css .= "#{$uid} .olo-ql-card .olo-ql-trend-badge::before{content:'🔥';font-size:10px}";
        }
        if ( ! empty( $s['show_reading_time'] ) ) {
            $css .= "#{$uid} .olo-ql-rt{display:inline-flex;align-items:center;gap:4px;font-size:0.8em;color:rgba(0,0,0,0.55);margin-left:8px}";
            $css .= "#{$uid} .olo-ql-rt::before{content:'⏱';font-size:11px}";
        }
        if ( ! empty( $s['show_comment_count'] ) ) {
            $css .= "#{$uid} .olo-ql-cc{display:inline-flex;align-items:center;gap:4px;font-size:0.8em;color:rgba(0,0,0,0.55);margin-left:8px}";
            $css .= "#{$uid} .olo-ql-cc::before{content:'💬';font-size:11px}";
        }
        // Card relative positioning per badges
        $css .= "#{$uid} .olo-ql-card{position:relative}";
        return $css;
    }

    private function get_preset_extra_css( $preset_id, $uid, $s, $accent ) {
        // @deprecated v1.0.73 — refactor profondo: i preset audaci ora settano direttamente
        // i field standard tramite TILE_PRESETS in BuilderInspector.vue, e i field wow_* via
        // build_wow_effects_css(). Nessun !important, ogni proprietà personalizzabile.
        return '';
    }

    /**
     * Build WP_Query args from settings.
     */
    private function build_query_args( $s, $paged = 1 ) {
        // Post type: support custom post types
        $post_type = sanitize_key( $s['post_type'] ?? 'post' );
        if ( $post_type === 'custom' ) {
            $cpt = sanitize_key( $s['custom_post_type'] ?? '' );
            $post_type = $cpt !== '' ? $cpt : 'post';
        }

        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => absint( $s['posts_per_page'] ),
            'orderby'        => sanitize_key( $s['orderby'] ),
            'order'          => strtoupper( $s['order'] ) === 'ASC' ? 'ASC' : 'DESC',
            'post_status'    => 'publish',
            'paged'          => $paged,
        ];

        // Offset
        $offset = absint( $s['offset'] ?? 0 );
        if ( $offset > 0 ) {
            $args['offset'] = $offset + ( ( $paged - 1 ) * absint( $s['posts_per_page'] ) );
        }

        // Exclude current post
        $not_in = [];
        if ( ! empty( $s['exclude_current'] ) ) {
            $current_id = get_the_ID();
            if ( $current_id ) {
                $not_in[] = $current_id;
            }
        }

        // Exclude specific IDs
        $exclude_ids = trim( $s['exclude_ids'] ?? '' );
        if ( $exclude_ids !== '' ) {
            foreach ( explode( ',', $exclude_ids ) as $eid ) {
                $eid = absint( trim( $eid ) );
                if ( $eid > 0 ) $not_in[] = $eid;
            }
        }
        if ( ! empty( $not_in ) ) {
            // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- esclusione post necessaria alla funzione del tile; query a volume limitato
            $args['post__not_in'] = array_unique( $not_in );
        }

        // Include specific IDs only
        $include_ids = trim( $s['include_ids'] ?? '' );
        if ( $include_ids !== '' ) {
            $ids = array_filter( array_map( function( $v ) { return absint( trim( $v ) ); }, explode( ',', $include_ids ) ) );
            if ( ! empty( $ids ) ) {
                $args['post__in'] = $ids;
            }
        }

        // Author filter
        $author_ids = trim( $s['author_ids'] ?? '' );
        if ( $author_ids !== '' ) {
            $aids = array_filter( array_map( function( $v ) { return absint( trim( $v ) ); }, explode( ',', $author_ids ) ) );
            if ( ! empty( $aids ) ) {
                $args['author__in'] = $aids;
            }
        }

        // Sticky posts handling
        $sticky = $s['sticky_posts'] ?? 'include';
        if ( $sticky === 'exclude' ) {
            $args['ignore_sticky_posts'] = true;
            $sticky_ids = get_option( 'sticky_posts', [] );
            if ( ! empty( $sticky_ids ) ) {
                // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- esclusione post necessaria alla funzione del tile; query a volume limitato
                $args['post__not_in'] = array_unique( array_merge( $args['post__not_in'] ?? [], $sticky_ids ) );
            }
        } elseif ( $sticky === 'only' ) {
            $sticky_ids = get_option( 'sticky_posts', [] );
            if ( ! empty( $sticky_ids ) ) {
                $args['post__in'] = $sticky_ids;
            }
        }

        // Taxonomy filter (primary)
        $taxonomy  = sanitize_text_field( $s['taxonomy'] ?? '' );
        $terms_str = sanitize_text_field( $s['terms'] ?? '' );
        $tax_query = [];
        if ( $taxonomy !== '' && $terms_str !== '' ) {
            $term_slugs = array_map( 'trim', explode( ',', $terms_str ) );
            $operator   = $s['tax_relation'] ?? 'IN';
            $valid_ops  = [ 'IN', 'AND', 'NOT IN' ];
            if ( ! in_array( $operator, $valid_ops, true ) ) $operator = 'IN';
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $term_slugs,
                'operator' => $operator,
            ];
        }

        // Second taxonomy filter
        $tax2      = sanitize_text_field( $s['second_taxonomy'] ?? '' );
        $terms2    = sanitize_text_field( $s['second_terms'] ?? '' );
        if ( $tax2 !== '' && $terms2 !== '' ) {
            $tax_query[] = [
                'taxonomy' => $tax2,
                'field'    => 'slug',
                'terms'    => array_map( 'trim', explode( ',', $terms2 ) ),
            ];
        }
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        if ( ! empty( $tax_query ) ) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- filtro tassonomia necessario alla funzione del tile Query Loop; volume limitato (loop di archivio impaginato).
            $args['tax_query'] = $tax_query;
        }

        // Date range filter
        $date_after  = sanitize_text_field( $s['date_after'] ?? '' );
        $date_before = sanitize_text_field( $s['date_before'] ?? '' );
        if ( $date_after !== '' || $date_before !== '' ) {
            $date_query = [];
            if ( $date_after !== '' )  $date_query['after']  = $date_after;
            if ( $date_before !== '' ) $date_query['before'] = $date_before;
            $date_query['inclusive'] = true;
            $args['date_query'] = [ $date_query ];
        }

        // Meta query (ACF / custom field filter)
        $meta_key = sanitize_text_field( $s['meta_key'] ?? '' );
        if ( $meta_key !== '' ) {
            $meta_value   = sanitize_text_field( $s['meta_value'] ?? '' );
            $meta_compare = $s['meta_compare'] ?? '=';
            $meta_type    = $s['meta_type'] ?? 'CHAR';
            $valid_cmp    = [ '=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS' ];
            if ( ! in_array( $meta_compare, $valid_cmp, true ) ) $meta_compare = '=';
            $valid_types  = [ 'CHAR', 'NUMERIC', 'DATE', 'DECIMAL' ];
            if ( ! in_array( $meta_type, $valid_types, true ) ) $meta_type = 'CHAR';

            $mq = [
                'key'     => $meta_key,
                'compare' => $meta_compare,
                'type'    => $meta_type,
            ];
            if ( ! in_array( $meta_compare, [ 'EXISTS', 'NOT EXISTS' ], true ) ) {
                $mq['value'] = $meta_value;
            }
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- filtro campo personalizzato (ACF/meta) necessario alla funzione del tile Query Loop; volume limitato (loop di archivio impaginato).
            $args['meta_query'] = [ $mq ];

            // Support orderby meta_value
            $orderby = $s['orderby'] ?? 'date';
            if ( in_array( $orderby, [ 'meta_value', 'meta_value_num' ], true ) ) {
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- ordinamento per meta_value necessario alla funzione del tile Query Loop; volume limitato (loop di archivio impaginato).
                $args['meta_key'] = $meta_key;
            }
        }

        // Search keyword
        $search = sanitize_text_field( $s['search_keyword'] ?? '' );
        if ( $search !== '' ) {
            $args['s'] = $search;
        }

        return $args;
    }

    /**
     * Get aspect ratio padding from setting.
     */
    private function get_ratio_padding( $ratio ) {
        $map = [
            '16:9' => '56.25%',
            '4:3'  => '75%',
            '1:1'  => '100%',
            '3:2'  => '66.67%',
            'auto' => '0',
        ];
        return $map[ $ratio ] ?? '56.25%';
    }

    /**
     * Render a single card.
     */
    private function render_card( $post_obj, $s ) {
        $permalink  = get_permalink( $post_obj );
        $title      = get_the_title( $post_obj );
        $title_tag  = in_array( $s['title_tag'], [ 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ? $s['title_tag'] : 'h3';
        $excerpt_length = absint( $s['excerpt_length'] );

        // Card classes
        $card_classes = [ 'olo-ql-card' ];
        $card_style_type = $s['card_style'];
        if ( $card_style_type === 'shadow' ) {
            $card_classes[] = 'olo-ql-card--shadow';
        } elseif ( $card_style_type === 'border' ) {
            $card_classes[] = 'olo-ql-card--border';
        } elseif ( $card_style_type === 'filled' ) {
            $card_classes[] = 'olo-ql-card--filled';
        }

        // Card inline styles
        $card_styles = [];
        if ( ! empty( $s['bg_color'] ) ) {
            $card_styles[] = 'background-color:' . esc_attr( $s['bg_color'] );
        }

        $hover_attr = '';
        if ( ! empty( $s['hover_bg'] ) ) {
            $hover_attr = ' data-olo-ql-hover="' . esc_attr( $s['hover_bg'] ) . '"';
        }

        // Magic badges
        $is_new       = ! empty( $s['new_badge'] ) && $this->is_post_new( $post_obj, absint( $s['new_badge_days'] ) );
        $is_trending  = ! empty( $s['trending_badge'] ) && in_array( $post_obj->ID, $this->trending_ids, true );
        $reading_time = ! empty( $s['show_reading_time'] ) ? $this->get_reading_time( $post_obj ) : 0;
        $comment_cnt  = ! empty( $s['show_comment_count'] ) ? (int) get_comments_number( $post_obj->ID ) : 0;
        $new_text     = esc_html( $s['new_badge_text'] ?? 'New' );

        ob_start();
        ?>
        <article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>"<?php if ( ! empty( $card_styles ) ) echo ' style="' . esc_attr( implode( ';', $card_styles ) ) . '"'; ?><?php echo $hover_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- data-olo-ql-hover attribute string built above with esc_attr() around the value ?>>
            <?php if ( $is_new ) : ?><span class="olo-ql-new-badge"><?php echo $new_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_html() at assignment above ?></span><?php endif; ?>
            <?php if ( $is_trending ) : ?><span class="olo-ql-trend-badge">Trending</span><?php endif; ?>
            <?php if ( ! empty( $s['show_image'] ) ) :
                $thumb_url = get_the_post_thumbnail_url( $post_obj, 'large' );
                $ratio_padding = $this->get_ratio_padding( $s['image_ratio'] );
                if ( $thumb_url ) :
            ?>
                <a href="<?php echo esc_url( $permalink ); ?>" class="olo-ql-img-link">
                    <?php if ( $s['image_ratio'] === 'auto' ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="olo-ql-img-auto" loading="lazy">
                    <?php else : ?>
                        <div class="olo-ql-img" style="padding-bottom:<?php echo esc_attr( $ratio_padding ); ?>;background-image:url(<?php echo esc_url( $thumb_url ); ?>)"></div>
                    <?php endif; ?>
                </a>
            <?php endif; endif; ?>

            <div class="olo-ql-body">
                <?php if ( ! empty( $s['show_category'] ) ) :
                    $cats = get_the_category( $post_obj->ID );
                    if ( ! empty( $cats ) ) :
                ?>
                    <span class="olo-ql-cat"<?php if ( ! empty( $s['link_color'] ) ) echo ' style="color:' . esc_attr( $s['link_color'] ) . '"'; ?>><?php echo esc_html( $cats[0]->name ); ?></span>
                <?php endif; endif; ?>

                <?php if ( ! empty( $s['show_title'] ) ) : ?>
                    <<?php echo tag_escape( $title_tag ); ?> class="olo-ql-title"<?php if ( ! empty( $s['title_color'] ) ) echo ' style="color:' . esc_attr( $s['title_color'] ) . '"'; ?>>
                        <a href="<?php echo esc_url( $permalink ); ?>"<?php if ( ! empty( $s['title_color'] ) ) echo ' style="color:inherit"'; ?>><?php echo esc_html( $title ); ?></a>
                    </<?php echo tag_escape( $title_tag ); ?>>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_date'] ) || ! empty( $s['show_author'] ) || $reading_time || $comment_cnt > 0 ) : ?>
                    <div class="olo-ql-meta"<?php if ( ! empty( $s['meta_color'] ) ) echo ' style="color:' . esc_attr( $s['meta_color'] ) . '"'; ?>>
                        <?php if ( ! empty( $s['show_date'] ) ) : ?>
                            <span class="olo-ql-date"><?php echo esc_html( get_the_date( '', $post_obj ) ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_author'] ) ) : ?>
                            <span class="olo-ql-author"><?php echo esc_html( get_the_author_meta( 'display_name', $post_obj->post_author ) ); ?></span>
                        <?php endif; ?>
                        <?php if ( $reading_time ) : ?>
                            <span class="olo-ql-rt"><?php echo (int) $reading_time; ?> min</span>
                        <?php endif; ?>
                        <?php if ( $comment_cnt > 0 ) : ?>
                            <span class="olo-ql-cc"><?php echo (int) $comment_cnt; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_excerpt'] ) ) :
                    $raw_excerpt = $post_obj->post_excerpt;
                    if ( empty( $raw_excerpt ) ) {
                        $raw_excerpt = $post_obj->post_content;
                    }
                    $excerpt_text = wp_trim_words( wp_strip_all_tags( $raw_excerpt ), $excerpt_length, '...' );
                ?>
                    <p class="olo-ql-excerpt"<?php if ( ! empty( $s['text_color'] ) ) echo ' style="color:' . esc_attr( $s['text_color'] ) . '"'; ?>><?php echo esc_html( $excerpt_text ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $s['show_read_more'] ) ) : ?>
                    <a href="<?php echo esc_url( $permalink ); ?>" class="olo-ql-readmore"<?php if ( ! empty( $s['link_color'] ) ) echo ' style="color:' . esc_attr( $s['link_color'] ) . '"'; ?>><?php echo esc_html( $s['read_more_text'] ); ?> &rarr;</a>
                <?php endif; ?>
            </div>
        </article>
        <?php
        return ob_get_clean();
    }

    /**
     * Render cards HTML for a set of posts.
     */
    private function render_cards( $posts, $s ) {
        $loop_tpl_id = absint( $s['loop_template_id'] ?? 0 );
        $html = '';

        foreach ( $posts as $post_obj ) {
            setup_postdata( $post_obj );

            if ( $loop_tpl_id > 0 ) {
                // Render using custom Olobuild template for each item
                $html .= $this->render_loop_item( $post_obj, $loop_tpl_id );
            } else {
                $html .= $this->render_card( $post_obj, $s );
            }
        }
        wp_reset_postdata();
        return $html;
    }

    /**
     * Render a single loop item using an Olobuild template.
     * Sets the global $post so dynamic content resolves to the loop item.
     */
    private function render_loop_item( $post_obj, $template_id ) {
        global $post;
        $old_post = $post;
        $post = $post_obj;
        setup_postdata( $post );

        $renderer = new Olobuild_Frontend_Renderer();
        $output = $renderer->render_template( $template_id );

        $post = $old_post;
        if ( $old_post ) {
            setup_postdata( $old_post );
        }

        return '<div class="olo-ql-loop-item">' . $output . '</div>';
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        if ( ! post_type_exists( sanitize_key( $s['post_type'] ) ) ) {
            return '<p style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;">Tipo di contenuto "' . esc_html( $s['post_type'] ) . '" non trovato.</p>';
        }

        $paged = 1;
        if ( $s['pagination_type'] === 'numbers' ) {
            $paged = max( 1, absint( get_query_var( 'paged', 1 ) ) );
        }

        $query_args = $this->build_query_args( $s, $paged );
        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            return '<p class="olo-ql-empty" style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;">Nessun risultato trovato.</p>';
        }

        $layout  = $s['layout'];
        $columns = absint( $s['columns'] );
        $gap     = absint( $s['gap'] );

        $preset_id = sanitize_key( $s['preset'] ?? 'custom' );
        $allowed_layouts = [ 'grid','list','list-rich','carousel','masonry','magazine-trio','magazine-hero','alternating','bento','newspaper','timeline','ticker-strip','stacked' ];
        if ( ! in_array( $layout, $allowed_layouts, true ) ) $layout = 'grid';

        $allowed_fx = [ 'none','lift','image-zoom','title-underline','arrow-slide','color-tint','border-grow','glassy-border' ];
        $hover_fx   = in_array( $s['hover_effect'], $allowed_fx, true ) ? $s['hover_effect'] : 'none';

        // TOKEN-FIRST: accento = primario brand (era #e1474f indaco off-brand)
        $accent_c   = $this->safe_color_css( $s['accent_color'] ) ?: ( $this->safe_color_css( $s['effect_color'] ) ?: ( $this->safe_color_css( $s['link_color'] ) ?: 'var(--olo-color-primary, #e1474f)' ) );
        $bg_c       = $this->safe_color_css( $s['bg_color'] ?? '' );
        $font_family = $this->font_family_css( $s['font_family'] ?? 'inherit' );
        $title_weight = in_array( $s['title_weight'], [ '400','500','600','700','800' ], true ) ? $s['title_weight'] : '700';
        $tt           = in_array( $s['text_transform'], [ 'none','uppercase','lowercase','capitalize' ], true ) ? $s['text_transform'] : 'none';
        $ls           = floatval( $s['letter_spacing'] ?? 0 );

        $cp = $s['container_padding'] ?? [];
        $cpt = is_array( $cp ) ? absint( $cp['top']    ?? 0 ) : 0;
        $cpr = is_array( $cp ) ? absint( $cp['right']  ?? 0 ) : 0;
        $cpb = is_array( $cp ) ? absint( $cp['bottom'] ?? 0 ) : 0;
        $cpl = is_array( $cp ) ? absint( $cp['left']   ?? 0 ) : 0;
        $container_radius_css = $this->build_border_radius_css( $s['container_radius'] ?? [] );
        $card_radius_css      = $this->build_border_radius_css( $s['card_radius'] ?? [] );

        // Unique instance ID
        $instance_id = 'olo-ql-' . wp_unique_id();

        // Compute trending IDs if enabled
        if ( ! empty( $s['trending_badge'] ) ) {
            $this->compute_trending_ids( $query->posts );
        }

        // Container style — usato solo per layout base; per layout speciali si usa get_layout_extra_css
        $container_style = '';
        if ( $layout === 'grid' ) {
            $container_style = "display:grid;grid-template-columns:repeat({$columns},1fr);gap:{$gap}px";
        } elseif ( $layout === 'list' ) {
            $container_style = "display:flex;flex-direction:column;gap:{$gap}px";
        } elseif ( $layout === 'masonry' ) {
            $container_style = "column-count:{$columns};column-gap:{$gap}px";
        }

        // Wrap container styles
        $wrap_styles = "font-family:{$font_family};";
        if ( $tt !== 'none' ) $wrap_styles .= "text-transform:{$tt};";
        if ( $ls > 0 ) $wrap_styles .= "letter-spacing:{$ls}px;";
        if ( $bg_c ) $wrap_styles .= "background:{$bg_c};";
        $wrap_styles .= "padding:{$cpt}px {$cpr}px {$cpb}px {$cpl}px;";
        if ( $container_radius_css ) $wrap_styles .= "border-radius:{$container_radius_css};";

        ob_start();
        ?>
        <div class="olo-queryloop olo-ql-preset-<?php echo esc_attr( $preset_id ); ?> olo-ql-layout-<?php echo esc_attr( $layout ); ?> olo-ql-hover-<?php echo esc_attr( $hover_fx ); ?>" id="<?php echo esc_attr( $instance_id ); ?>" style="<?php echo esc_attr( $wrap_styles ); ?>">

            <?php if ( ! empty( $s['enable_search'] ) ) : ?>
                <input type="text" class="olo-ql-search" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" />
            <?php endif; ?>

            <?php if ( ! empty( $s['enable_sort_ui'] ) ) : ?>
                <div class="olo-ql-sort-wrap">
                    <label><?php esc_html_e( 'Ordina:', 'olobuild' ); ?>
                    <select class="olo-ql-sort">
                        <option value="date-desc"><?php esc_html_e( 'Più recenti', 'olobuild' ); ?></option>
                        <option value="date-asc"><?php esc_html_e( 'Più vecchi', 'olobuild' ); ?></option>
                        <option value="title-asc"><?php esc_html_e( 'Titolo (A-Z)', 'olobuild' ); ?></option>
                        <option value="title-desc"><?php esc_html_e( 'Titolo (Z-A)', 'olobuild' ); ?></option>
                        <option value="rand"><?php esc_html_e( 'Casuale', 'olobuild' ); ?></option>
                    </select>
                    </label>
                </div>
            <?php endif; ?>

            <?php
            // Taxonomy tabs UI
            if ( ! empty( $s['enable_taxonomy_tabs'] ) ) {
                $tax = sanitize_key( $s['taxonomy_tabs_taxonomy'] ?? 'category' );
                if ( taxonomy_exists( $tax ) ) {
                    $tax_terms = get_terms( [ 'taxonomy' => $tax, 'hide_empty' => true ] );
                    if ( ! is_wp_error( $tax_terms ) && ! empty( $tax_terms ) ) {
                        echo '<div class="olo-ql-tabs"><button class="active" data-tax="*">' . esc_html__( 'Tutti', 'olobuild' ) . '</button>';
                        foreach ( $tax_terms as $term ) {
                            echo '<button data-tax="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</button>';
                        }
                        echo '</div>';
                    }
                }
            }
            ?>

            <?php if ( $layout === 'carousel' ) : ?>
                <div uk-slider="autoplay: false; sets: true">
                    <div class="uk-position-relative">
                        <div class="uk-slider-container">
                            <ul class="uk-slider-items uk-grid" style="gap:<?php echo (int) $gap; ?>px">
                                <?php foreach ( $query->posts as $post_obj ) :
                                    setup_postdata( $post_obj );
                                ?>
                                    <li style="min-width:<?php echo esc_attr( round( 100 / max( 1, $columns ), 2 ) ); ?>%;max-width:<?php echo esc_attr( round( 100 / max( 1, $columns ), 2 ) ); ?>%">
                                        <?php echo $this->render_card( $post_obj, $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- card markup generated by render_card() which escapes every value internally (esc_html/esc_attr/esc_url/tag_escape) ?>
                                    </li>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </ul>
                        </div>
                        <a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                        <a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
                    </div>
                    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-small-top"></ul>
                </div>
            <?php elseif ( $layout === 'timeline' ) : ?>
                <div class="olo-ql-container">
                    <?php
                    $group_by = in_array( $s['timeline_group_by'], [ 'day','month','year' ], true ) ? $s['timeline_group_by'] : 'month';
                    $groups = [];
                    foreach ( $query->posts as $post_obj ) {
                        if ( $group_by === 'year' )       $key = get_the_date( 'Y', $post_obj );
                        elseif ( $group_by === 'day' )    $key = get_the_date( 'd M Y', $post_obj );
                        else                              $key = get_the_date( 'F Y', $post_obj );
                        if ( ! isset( $groups[ $key ] ) ) $groups[ $key ] = [];
                        $groups[ $key ][] = $post_obj;
                    }
                    foreach ( $groups as $label => $posts ) :
                    ?>
                        <div class="olo-ql-tl-group">
                            <div class="olo-ql-tl-date"><?php echo esc_html( $label ); ?></div>
                            <div class="olo-ql-tl-items">
                                <?php echo $this->render_cards( $posts, $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cards markup generated by render_card()/render_template() which escape every value internally ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ( $layout === 'ticker-strip' ) : ?>
                <div class="olo-ql-container">
                    <div class="olo-ql-track">
                        <?php for ( $rep = 0; $rep < 2; $rep++ ) : ?>
                            <?php echo $this->render_cards( $query->posts, $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cards markup generated by render_card()/render_template() which escape every value internally ?>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="olo-ql-container"<?php if ( $container_style ) echo ' style="' . esc_attr( $container_style ) . '"'; ?>>
                    <?php echo $this->render_cards( $query->posts, $s ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cards markup generated by render_card()/render_template() which escape every value internally ?>
                </div>
            <?php endif; ?>

            <?php
            // Pagination
            if ( $s['pagination_type'] === 'numbers' ) {
                $total_pages = $query->max_num_pages;
                if ( $total_pages > 1 ) {
                    echo '<nav class="olo-ql-pagination">';
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() returns pagination markup generated by WordPress core
                    echo paginate_links( [
                        'total'   => $total_pages,
                        'current' => $paged,
                        'type'    => 'list',
                    ] );
                    echo '</nav>';
                }
            } elseif ( $s['pagination_type'] === 'loadmore' ) {
                if ( $query->max_num_pages > 1 ) {
                    echo '<div class="olo-ql-loadmore-wrap" style="text-align:center;margin-top:' . (int) $gap . 'px">';
                    echo '<button class="olo-ql-loadmore-btn" data-ql-id="' . esc_attr( $instance_id ) . '" data-ql-page="1" data-ql-max="' . esc_attr( $query->max_num_pages ) . '">Carica altro</button>';
                    echo '</div>';
                    $this->enqueue_ajax_script( $instance_id, $s, $query->max_num_pages );
                }
            } elseif ( $s['pagination_type'] === 'infinite' ) {
                if ( $query->max_num_pages > 1 ) {
                    echo '<div class="olo-ql-sentinel" data-ql-id="' . esc_attr( $instance_id ) . '" data-ql-page="1" data-ql-max="' . esc_attr( $query->max_num_pages ) . '" style="height:1px"></div>';
                    $this->enqueue_infinite_script( $instance_id, $s, $query->max_num_pages );
                }
            }
            ?>
        </div>

        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: absint() for gap/columns, safe_color_css() whitelist for the accent colour, in_array() whitelists for layout/hover-effect/title-weight, build_border_radius_css() (integer-forced), the internally generated instance id, and CSS produced by the internal builders get_layout_extra_css()/get_hover_effect_css()/get_magic_css()/build_wow_effects_css() which only interpolate those same sanitized values. ?>
        <style>
            .olo-queryloop { width: 100%; }
            .olo-ql-card { overflow: hidden; <?php echo $card_radius_css ? 'border-radius:' . $card_radius_css . ';' : 'border-radius: 6px;'; ?> }
            .olo-ql-card--shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
            .olo-ql-card--border { border: 1px solid var(--olo-color-border, #E5E7EB); }
            .olo-ql-card--filled { background: var(--olo-color-muted, #F3F4F6); }
            .olo-ql-img { width: 100%; background-size: cover; background-position: center; }
            .olo-ql-img-auto { width: 100%; height: auto; display: block; }
            .olo-ql-img-link { display: block; text-decoration: none; }
            .olo-ql-body { padding: 16px; }
            .olo-ql-cat { display: inline-block; font-size: 11px; text-transform: uppercase; font-weight: 600; color: <?php echo $accent_c; ?>; margin-bottom: 4px; }
            .olo-ql-title { font-size: 1.1em; font-weight: <?php echo $title_weight; ?>; margin: 0 0 6px; line-height: 1.3; }
            .olo-ql-title a { text-decoration: none; color: inherit; }
            .olo-ql-title a:hover { text-decoration: underline; }
            .olo-ql-meta { font-size: 0.82em; color: var(--olo-color-text-muted, #9CA3AF); margin-bottom: 8px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
            .olo-ql-excerpt { font-size: 0.92em; color: var(--olo-color-text-muted, #9CA3AF); line-height: 1.6; margin: 0 0 10px; }
            .olo-ql-readmore { font-size: 0.88em; font-weight: 500; text-decoration: none; color: <?php echo $accent_c; ?>; }
            .olo-ql-readmore:hover { text-decoration: underline; }
            /* a11y tastiera: anello di focus visibile su link card, read-more, paginazione e load-more */
            .olo-ql-title a:focus-visible,
            .olo-ql-readmore:focus-visible,
            .olo-ql-img-link:focus-visible,
            .olo-ql-pagination .page-numbers li a:focus-visible,
            .olo-ql-loadmore-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); border-radius: 4px; }
            .olo-ql-pagination { margin-top: 24px; text-align: center; }
            .olo-ql-pagination .page-numbers { display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 4px; }
            .olo-ql-pagination .page-numbers li a,
            .olo-ql-pagination .page-numbers li span { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 8px; border-radius: 4px; font-size: 14px; text-decoration: none; background: var(--olo-color-muted, #F3F4F6); color: var(--olo-color-text, #374151); }
            .olo-ql-pagination .page-numbers li .current { background: <?php echo $accent_c; ?>; color: #fff; }
            .olo-ql-loadmore-btn { padding: 10px 28px; font-size: 14px; border: 1px solid var(--olo-color-border, #E5E7EB); border-radius: 6px; background: var(--olo-color-background, #FFFFFF); color: var(--olo-color-text, #374151); cursor: pointer; transition: background 0.2s; }
            .olo-ql-loadmore-btn:hover { background: var(--olo-color-muted, #F3F4F6); }
            .olo-ql-loadmore-btn:disabled { opacity: 0.5; cursor: not-allowed; }

            /* UI controls */
            #<?php echo esc_attr( $instance_id ); ?> .olo-ql-search { width: 100%; padding: 10px 14px; border: 1px solid rgba(0,0,0,0.15); border-radius: 8px; font-size: 14px; box-sizing: border-box; margin-bottom: 14px; background: rgba(255,255,255,0.6); }
            #<?php echo esc_attr( $instance_id ); ?> .olo-ql-search:focus { outline: none; border-color: <?php echo $accent_c; ?>; }
            #<?php echo esc_attr( $instance_id ); ?> .olo-ql-sort-wrap { margin-bottom: 14px; font-size: 13px; }
            #<?php echo esc_attr( $instance_id ); ?> .olo-ql-sort { padding: 6px 10px; border: 1px solid rgba(0,0,0,0.15); border-radius: 6px; background: #fff; margin-left: 6px; }
            #<?php echo esc_attr( $instance_id ); ?> .olo-ql-card.olo-ql-hidden { display: none !important; }

            <?php if ( $layout === 'masonry' ) : ?>
            #<?php echo esc_attr( $instance_id ); ?> .olo-ql-container .olo-ql-card { break-inside: avoid; margin-bottom: <?php echo $gap; ?>px; }
            <?php endif; ?>

            <?php // Layout speciali ?>
            <?php $layout_extra = $this->get_layout_extra_css( $layout, $instance_id, $columns, $gap, $accent_c ); ?>
            <?php if ( $layout_extra ) echo $layout_extra; ?>

            <?php // Hover effect ?>
            <?php $hover_extra = $this->get_hover_effect_css( $hover_fx, $instance_id, $accent_c ); ?>
            <?php if ( $hover_extra ) echo $hover_extra; ?>

            <?php // Magic features ?>
            <?php $magic_extra = $this->get_magic_css( $s, $instance_id, $accent_c ); ?>
            <?php if ( $magic_extra ) echo $magic_extra; ?>

            <?php // v1.0.73 — refactor profondo: get_preset_extra_css svuotato, ora i preset audaci
            // settano i field standard tramite TILE_PRESETS.queryloop + helper wow_*. ?>
            <?php $preset_extra = $this->build_wow_effects_css( $s, '#' . $instance_id, '.olo-loop-item-title' ); ?>
            <?php if ( $preset_extra ) echo $preset_extra; ?>

            @media (max-width: 640px) {
                <?php if ( $layout === 'grid' || $layout === 'bento' ) : ?>
                #<?php echo esc_attr( $instance_id ); ?> .olo-ql-container { grid-template-columns: 1fr !important; }
                <?php elseif ( $layout === 'masonry' || $layout === 'newspaper' ) : ?>
                #<?php echo esc_attr( $instance_id ); ?> .olo-ql-container { column-count: 1 !important; }
                <?php endif; ?>
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php // ── JS interactions per UI controls ── ?>
        <?php if ( ! empty( $s['enable_search'] ) || ! empty( $s['enable_sort_ui'] ) || ! empty( $s['enable_taxonomy_tabs'] ) || $layout === 'ticker-strip' ) : ?>
        <script>
        (function(){
            var root = document.getElementById('<?php echo esc_js( $instance_id ); ?>');
            if(!root) return;

            <?php if ( ! empty( $s['enable_search'] ) ) : ?>
            var search = root.querySelector('.olo-ql-search');
            if(search){
                search.addEventListener('input', function(){
                    var q = (search.value || '').toLowerCase().trim();
                    var cards = root.querySelectorAll('.olo-ql-card');
                    cards.forEach(function(c){
                        var t = (c.querySelector('.olo-ql-title') || {}).textContent || '';
                        var e = (c.querySelector('.olo-ql-excerpt') || {}).textContent || '';
                        var hay = (t + ' ' + e).toLowerCase();
                        if(q === ''){ c.classList.remove('olo-ql-hidden'); }
                        else if(hay.indexOf(q) === -1){ c.classList.add('olo-ql-hidden'); }
                        else { c.classList.remove('olo-ql-hidden'); }
                    });
                });
            }
            <?php endif; ?>

            <?php if ( ! empty( $s['enable_sort_ui'] ) ) : ?>
            var sortSel = root.querySelector('.olo-ql-sort');
            if(sortSel){
                sortSel.addEventListener('change', function(){
                    var mode = sortSel.value;
                    var container = root.querySelector('.olo-ql-container');
                    if(!container) return;
                    var cards = Array.prototype.slice.call(container.querySelectorAll(':scope > .olo-ql-card'));
                    cards.sort(function(a,b){
                        if(mode === 'rand'){ return Math.random() - 0.5; }
                        var aT = (a.querySelector('.olo-ql-title') || {}).textContent || '';
                        var bT = (b.querySelector('.olo-ql-title') || {}).textContent || '';
                        if(mode === 'title-asc'){ return aT.localeCompare(bT); }
                        if(mode === 'title-desc'){ return bT.localeCompare(aT); }
                        var aD = (a.querySelector('.olo-ql-date') || {}).textContent || '';
                        var bD = (b.querySelector('.olo-ql-date') || {}).textContent || '';
                        if(mode === 'date-asc'){ return aD.localeCompare(bD); }
                        return bD.localeCompare(aD);
                    });
                    cards.forEach(function(c){ container.appendChild(c); });
                });
            }
            <?php endif; ?>

            <?php if ( ! empty( $s['enable_taxonomy_tabs'] ) ) : ?>
            var tabs = root.querySelector('.olo-ql-tabs');
            if(tabs){
                var tabBtns = tabs.querySelectorAll('button');
                tabBtns.forEach(function(btn){
                    btn.addEventListener('click', function(){
                        tabBtns.forEach(function(b){ b.classList.remove('active'); });
                        btn.classList.add('active');
                        var slug = btn.getAttribute('data-tax');
                        var cards = root.querySelectorAll('.olo-ql-card');
                        cards.forEach(function(c){
                            if(slug === '*'){ c.classList.remove('olo-ql-hidden'); return; }
                            var catEl = c.querySelector('.olo-ql-cat');
                            var catText = catEl ? catEl.textContent.trim().toLowerCase() : '';
                            var bSlug = slug.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                            var bText = catText.replace(/[^a-z0-9]+/g, '-');
                            if(bText.indexOf(bSlug) !== -1){ c.classList.remove('olo-ql-hidden'); }
                            else { c.classList.add('olo-ql-hidden'); }
                        });
                    });
                });
            }
            <?php endif; ?>

            <?php if ( $layout === 'ticker-strip' ) : ?>
            var track = root.querySelector('.olo-ql-track');
            if(track){
                root.addEventListener('mouseenter', function(){ track.style.animationPlayState = 'paused'; });
                root.addEventListener('mouseleave', function(){ track.style.animationPlayState = 'running'; });
            }
            <?php endif; ?>
        })();
        </script>
        <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue inline AJAX script for load-more button.
     * NOTA: NON usare && negli script inline — WordPress converte in entità HTML.
     */
    private function enqueue_ajax_script( $instance_id, $s, $max_pages ) {
        $ajax_url = admin_url( 'admin-ajax.php' );
        $settings_json = wp_json_encode( $s );
        // NOTA CRITICA: usare if annidati al posto di && per evitare che WordPress rompa lo script
        ?>
        <script>
        (function(){
            var container = document.querySelector('#<?php echo esc_js( $instance_id ); ?> .olo-ql-container');
            var btn = document.querySelector('[data-ql-id="<?php echo esc_js( $instance_id ); ?>"].olo-ql-loadmore-btn');
            if(btn){
                btn.addEventListener('click', function(){
                    var page = parseInt(btn.getAttribute('data-ql-page')) + 1;
                    var maxP = parseInt(btn.getAttribute('data-ql-max'));
                    if(page > maxP) return;
                    btn.disabled = true;
                    btn.textContent = 'Caricamento...';
                    var fd = new FormData();
                    fd.append('action', 'olo_queryloop_page');
                    fd.append('page', page);
                    fd.append('settings', '<?php echo esc_js( $settings_json ); ?>');
                    fd.append('nonce', '<?php echo esc_js( wp_create_nonce( 'olo_ql_nonce' ) ); ?>');
                    fetch('<?php echo esc_js( $ajax_url ); ?>', { method: 'POST', body: fd })
                        .then(function(r){ return r.json(); })
                        .then(function(data){
                            if(data.success){
                                if(container){
                                    var tmp = document.createElement('div');
                                    tmp.innerHTML = data.data.html;
                                    while(tmp.firstChild){
                                        container.appendChild(tmp.firstChild);
                                    }
                                }
                                btn.setAttribute('data-ql-page', page);
                                if(page >= maxP){
                                    btn.parentNode.style.display = 'none';
                                } else {
                                    btn.disabled = false;
                                    btn.textContent = 'Carica altro';
                                }
                            }
                        });
                });
            }
        })();
        </script>
        <?php
    }

    /**
     * Enqueue inline script for infinite scroll.
     * NOTA: NON usare && negli script inline — WordPress converte in entità HTML.
     */
    private function enqueue_infinite_script( $instance_id, $s, $max_pages ) {
        $ajax_url = admin_url( 'admin-ajax.php' );
        $settings_json = wp_json_encode( $s );
        // NOTA CRITICA: usare if annidati al posto di && per evitare che WordPress rompa lo script
        ?>
        <script>
        (function(){
            var sentinel = document.querySelector('[data-ql-id="<?php echo esc_js( $instance_id ); ?>"].olo-ql-sentinel');
            var container = document.querySelector('#<?php echo esc_js( $instance_id ); ?> .olo-ql-container');
            if(sentinel){
                var loading = false;
                var observer = new IntersectionObserver(function(entries){
                    var entry = entries[0];
                    if(entry.isIntersecting){
                        if(loading) return;
                        var page = parseInt(sentinel.getAttribute('data-ql-page')) + 1;
                        var maxP = parseInt(sentinel.getAttribute('data-ql-max'));
                        if(page > maxP){
                            observer.disconnect();
                            sentinel.style.display = 'none';
                            return;
                        }
                        loading = true;
                        var fd = new FormData();
                        fd.append('action', 'olo_queryloop_page');
                        fd.append('page', page);
                        fd.append('settings', '<?php echo esc_js( $settings_json ); ?>');
                        fd.append('nonce', '<?php echo esc_js( wp_create_nonce( 'olo_ql_nonce' ) ); ?>');
                        fetch('<?php echo esc_js( $ajax_url ); ?>', { method: 'POST', body: fd })
                            .then(function(r){ return r.json(); })
                            .then(function(data){
                                if(data.success){
                                    if(container){
                                        var tmp = document.createElement('div');
                                        tmp.innerHTML = data.data.html;
                                        while(tmp.firstChild){
                                            container.appendChild(tmp.firstChild);
                                        }
                                    }
                                    sentinel.setAttribute('data-ql-page', page);
                                    if(page >= maxP){
                                        observer.disconnect();
                                        sentinel.style.display = 'none';
                                    }
                                }
                                loading = false;
                            });
                    }
                }, { rootMargin: '200px' });
                observer.observe(sentinel);
            }
        })();
        </script>
        <?php
    }

    /**
     * AJAX handler for loading additional pages.
     */
    public function ajax_load_page() {
        check_ajax_referer( 'olo_ql_nonce', 'nonce' );

        $page = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;
        // Settings arrivano come blob JSON prodotto da wp_json_encode() (singola riga,
        // nessun newline/tab): sanitize_text_field lo preserva ma rimuove eventuali
        // caratteri di controllo. Il payload decodificato viene comunque ri-sanitizzato
        // campo-per-campo a valle (wp_parse_args + build_query_args con sanitize_key/
        // sanitize_text_field/absint).
        $settings_raw = isset( $_POST['settings'] ) ? sanitize_text_field( wp_unslash( $_POST['settings'] ) ) : '';
        $s = json_decode( $settings_raw, true );
        if ( ! is_array( $s ) ) {
            wp_send_json_error( [ 'message' => 'Invalid settings' ] );
        }

        $s = wp_parse_args( $s, $this->defaults );

        $query_args = $this->build_query_args( $s, $page );
        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            wp_send_json_success( [ 'html' => '', 'has_more' => false ] );
        }

        $html = $this->render_cards( $query->posts, $s );
        $has_more = ( $page < $query->max_num_pages );

        wp_send_json_success( [
            'html'     => $html,
            'has_more' => $has_more,
        ] );
    }
}
