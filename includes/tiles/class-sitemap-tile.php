<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Sitemap_Tile extends Olo_Tile_Base {

    protected $type     = 'sitemap';
    protected $name     = 'Sitemap HTML';
    protected $icon     = 'dashicons-networking';
    protected $category = 'dynamic';
    protected $defaults = [
        'show_pages'      => true,
        'show_posts'      => true,
        'show_cpt'        => false,
        'cpt_names'       => '',
        'show_categories' => true,
        'columns'         => '2',
        'title_tag'       => 'h3',
        'title_color'     => '#1e293b',
        'link_color'      => '#2563eb',
        'hover_color'     => '#1d4ed8',
        'list_style'      => 'disc',
        'indent'          => '20',
        'exclude_ids'     => '',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'show_pages',      'type' => 'toggle',   'label' => olo_t( 'Mostra pagine' ) ],
            [ 'key' => 'show_posts',       'type' => 'toggle',   'label' => olo_t( 'Mostra articoli' ) ],
            [ 'key' => 'show_categories',  'type' => 'toggle',   'label' => olo_t( 'Mostra categorie' ) ],
            [ 'key' => 'show_cpt',         'type' => 'toggle',   'label' => olo_t( 'Mostra CPT' ) ],
            [ 'key' => 'cpt_names',        'type' => 'text',     'label' => olo_t( 'Nomi CPT (virgola)' ) ],
            [ 'key' => 'exclude_ids',      'type' => 'text',     'label' => olo_t( 'Escludi ID (virgola)' ) ],
            [ 'key' => 'columns',          'type' => 'select',   'label' => olo_t( 'Colonne' ) ],
            [ 'key' => 'title_tag',        'type' => 'select',   'label' => olo_t( 'Tag titolo' ) ],
            [ 'key' => 'list_style',       'type' => 'select',   'label' => olo_t( 'Stile lista' ) ],
            [ 'key' => 'indent',           'type' => 'range',    'label' => olo_t( 'Indentazione (px)' ), 'min' => 0, 'max' => 60 ],
            [ 'key' => 'title_color',      'type' => 'color',    'label' => olo_t( 'Colore titolo' ) ],
            [ 'key' => 'link_color',       'type' => 'color',    'label' => olo_t( 'Colore link' ) ],
            [ 'key' => 'hover_color',      'type' => 'color',    'label' => olo_t( 'Colore hover' ) ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $columns    = max( 1, min( 4, absint( $s['columns'] ) ) );
        $title_tag  = in_array( $s['title_tag'], [ 'h2', 'h3', 'h4', 'h5' ], true ) ? $s['title_tag'] : 'h3';
        $list_style = in_array( $s['list_style'], [ 'disc', 'circle', 'none' ], true ) ? $s['list_style'] : 'disc';
        $indent     = max( 0, min( 60, absint( $s['indent'] ) ) );

        $title_color = $this->safe_color_css( $s['title_color'] ) ?: '#1e293b';
        $link_color  = $this->safe_color_css( $s['link_color'] )  ?: '#2563eb';
        $hover_color = $this->safe_color_css( $s['hover_color'] ) ?: '#1d4ed8';

        // Parse exclude IDs
        $exclude_ids = [];
        if ( ! empty( $s['exclude_ids'] ) ) {
            $exclude_ids = array_map( 'absint', array_filter( explode( ',', $s['exclude_ids'] ) ) );
        }

        $uid = 'olo-sitemap-' . wp_unique_id();

        // Collect sections
        $sections = [];

        // Pages
        if ( ! empty( $s['show_pages'] ) ) {
            $pages_args = [
                'sort_column' => 'menu_order,post_title',
                'sort_order'  => 'ASC',
                'post_status' => 'publish',
            ];
            if ( ! empty( $exclude_ids ) ) {
                $pages_args['exclude'] = $exclude_ids;
            }
            $pages = get_pages( $pages_args );
            if ( $pages ) {
                $items = [];
                foreach ( $pages as $page ) {
                    $items[] = [
                        'title' => get_the_title( $page ),
                        'url'   => get_permalink( $page->ID ),
                    ];
                }
                $sections[] = [ 'heading' => olo_t( 'Pagine' ), 'items' => $items ];
            }
        }

        // Posts
        if ( ! empty( $s['show_posts'] ) ) {
            $posts_args = [
                'post_type'      => 'post',
                'posts_per_page' => 100,
                'post_status'    => 'publish',
                'orderby'        => 'title',
                'order'          => 'ASC',
            ];
            if ( ! empty( $exclude_ids ) ) {
                $posts_args['post__not_in'] = $exclude_ids;
            }
            $posts = get_posts( $posts_args );
            if ( $posts ) {
                $items = [];
                foreach ( $posts as $p ) {
                    $items[] = [
                        'title' => get_the_title( $p ),
                        'url'   => get_permalink( $p->ID ),
                    ];
                }
                $sections[] = [ 'heading' => olo_t( 'Articoli' ), 'items' => $items ];
            }
        }

        // Categories
        if ( ! empty( $s['show_categories'] ) ) {
            $cats = get_categories( [ 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ] );
            if ( $cats ) {
                $items = [];
                foreach ( $cats as $cat ) {
                    if ( in_array( $cat->term_id, $exclude_ids, true ) ) {
                        continue;
                    }
                    $items[] = [
                        'title' => $cat->name,
                        'url'   => get_category_link( $cat->term_id ),
                    ];
                }
                if ( $items ) {
                    $sections[] = [ 'heading' => olo_t( 'Categorie' ), 'items' => $items ];
                }
            }
        }

        // Custom Post Types
        if ( ! empty( $s['show_cpt'] ) && ! empty( $s['cpt_names'] ) ) {
            $cpt_list = array_map( 'sanitize_key', array_filter( array_map( 'trim', explode( ',', $s['cpt_names'] ) ) ) );
            foreach ( $cpt_list as $cpt ) {
                if ( ! post_type_exists( $cpt ) ) {
                    continue;
                }
                $cpt_obj = get_post_type_object( $cpt );
                $cpt_posts = get_posts( [
                    'post_type'      => $cpt,
                    'posts_per_page' => 100,
                    'post_status'    => 'publish',
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'post__not_in'   => $exclude_ids ?: [],
                ] );
                if ( $cpt_posts ) {
                    $items = [];
                    foreach ( $cpt_posts as $cp ) {
                        $items[] = [
                            'title' => get_the_title( $cp ),
                            'url'   => get_permalink( $cp->ID ),
                        ];
                    }
                    $heading = $cpt_obj ? $cpt_obj->labels->name : ucfirst( $cpt );
                    $sections[] = [ 'heading' => $heading, 'items' => $items ];
                }
            }
        }

        if ( empty( $sections ) ) {
            return '<div class="olo-sitemap" style="text-align:center;padding:20px;color:var(--olo-color-text-muted, #9CA3AF);">' . olo_t( 'Nessun contenuto trovato per la sitemap.' ) . '</div>';
        }

        ob_start();
        ?>
        <style>
            #<?php echo $uid; ?> { display: grid; grid-template-columns: repeat(<?php echo $columns; ?>, 1fr); gap: 24px; padding: 16px; }
            #<?php echo $uid; ?> .olo-sitemap-title { color: <?php echo $title_color; ?>; margin: 0 0 8px; font-size: 1.1em; font-weight: 600; }
            #<?php echo $uid; ?> ul { list-style-type: <?php echo $list_style; ?>; padding-left: <?php echo $indent; ?>px; margin: 0; line-height: 1.8; }
            #<?php echo $uid; ?> a { color: <?php echo $link_color; ?>; text-decoration: none; transition: color 0.2s; }
            #<?php echo $uid; ?> a:hover { color: <?php echo $hover_color; ?>; text-decoration: underline; }
            @media (max-width: 640px) { #<?php echo $uid; ?> { grid-template-columns: 1fr; } }
        </style>
        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-sitemap">
            <?php foreach ( $sections as $section ) : ?>
            <div class="olo-sitemap-section">
                <<?php echo $title_tag; ?> class="olo-sitemap-title"><?php echo esc_html( $section['heading'] ); ?></<?php echo $title_tag; ?>>
                <ul>
                    <?php foreach ( $section['items'] as $item ) : ?>
                    <li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
