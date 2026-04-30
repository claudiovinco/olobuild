<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Portfolio_Tile extends Olo_Tile_Base {

    protected $type     = 'portfolio';
    protected $name     = 'Portfolio';
    protected $icon     = 'dashicons-portfolio';
    protected $category = 'dynamic';
    protected $defaults = [
        'source'              => 'manual',
        'post_type'           => 'post',
        'taxonomy'            => 'category',
        'posts_per_page'      => 12,
        'columns'             => 3,
        'gap'                 => 20,
        'filter_bar'          => true,
        'filter_style'        => 'buttons',
        'filter_all_label'    => 'Tutti',
        'filter_color'        => '#9CA3AF',
        'filter_active_color' => '#6366F1',
        'layout'              => 'grid',
        'hover_effect'        => 'fade',
        'show_title'          => true,
        'show_category'       => true,
        'show_excerpt'        => false,
        'title_color'         => '#F3F4F6',
        'text_color'          => '#9CA3AF',
        'overlay_color'       => '#000000',
        'overlay_opacity'     => 80,
        'image_ratio'         => '4:3',
        'border_radius'       => 8,
        'animation'           => 'fade',
        'items'               => [],
        'shadow'              => 'none',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid      = 'olo-pf-' . wp_rand( 10000, 99999 );
        $source   = in_array( $s['source'], [ 'manual', 'posts', 'custom_taxonomy' ], true ) ? $s['source'] : 'manual';
        $cols     = max( 1, min( 6, absint( $s['columns'] ) ) );
        $gap      = absint( $s['gap'] );
        $radius   = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $layout   = in_array( $s['layout'], [ 'grid', 'masonry' ], true ) ? $s['layout'] : 'grid';
        $fx       = in_array( $s['hover_effect'], [ 'none', 'zoom', 'fade', 'slide-up', 'overlay' ], true ) ? $s['hover_effect'] : 'fade';
        $anim     = in_array( $s['animation'], [ 'fade', 'scale', 'slide' ], true ) ? $s['animation'] : 'fade';
        $filter_style = in_array( $s['filter_style'], [ 'buttons', 'pills', 'underline', 'dropdown' ], true ) ? $s['filter_style'] : 'buttons';
        $ov_color = $this->safe_color_css( $s['overlay_color'] ) ?: '#000000';
        $ov_opa   = max( 0, min( 100, absint( $s['overlay_opacity'] ) ) );
        $title_c  = $this->safe_color_css( $s['title_color'] ) ?: '#F3F4F6';
        $text_c   = $this->safe_color_css( $s['text_color'] ) ?: '#9CA3AF';
        $flt_c    = $this->safe_color_css( $s['filter_color'] ) ?: '#9CA3AF';
        $flt_ac   = $this->safe_color_css( $s['filter_active_color'] ) ?: '#6366F1';
        $all_label = esc_html( $s['filter_all_label'] ?: olo_t( 'Tutti' ) );

        $ratio_map = [
            '1:1'  => '100%',
            '4:3'  => '75%',
            '16:9' => '56.25%',
            '3:2'  => '66.67%',
            'auto' => '0',
        ];
        $ratio = $s['image_ratio'];
        $ratio_css = isset( $ratio_map[ $ratio ] ) ? $ratio_map[ $ratio ] : '75%';

        // ── Gather items ─────────────────────────────────────
        $items      = [];
        $categories = [];

        if ( $source === 'manual' ) {
            $raw_items = is_array( $s['items'] ) ? $s['items'] : [];
            foreach ( $raw_items as $item ) {
                if ( ! is_array( $item ) ) continue;
                $cat = trim( $item['category'] ?? '' );
                $items[] = [
                    'title'       => $item['title'] ?? '',
                    'image'       => $item['image_url'] ?? '',
                    'category'    => $cat,
                    'description' => $item['description'] ?? '',
                    'link'        => $item['link_url'] ?? '',
                ];
                if ( $cat !== '' ) {
                    $categories[ $cat ] = true;
                }
            }
        } else {
            // Posts / Custom taxonomy source
            $post_type = sanitize_key( $s['post_type'] ?: 'post' );
            $taxonomy  = sanitize_key( $s['taxonomy'] ?: 'category' );
            $per_page  = max( 1, min( 50, absint( $s['posts_per_page'] ) ) );

            $query_args = [
                'post_type'      => $post_type,
                'posts_per_page' => $per_page,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ];

            $query = new WP_Query( $query_args );
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $terms = get_the_terms( get_the_ID(), $taxonomy );
                    $cats  = [];
                    if ( is_array( $terms ) ) {
                        foreach ( $terms as $term ) {
                            $cats[] = $term->name;
                            $categories[ $term->name ] = true;
                        }
                    }
                    $items[] = [
                        'title'       => get_the_title(),
                        'image'       => get_the_post_thumbnail_url( get_the_ID(), 'medium_large' ) ?: '',
                        'category'    => implode( ',', $cats ),
                        'description' => get_the_excerpt(),
                        'link'        => get_permalink(),
                    ];
                }
                wp_reset_postdata();
            }
        }

        $categories = array_keys( $categories );

        if ( empty( $items ) ) {
            return '<div style="padding:40px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">' . esc_html( olo_t( 'Nessun elemento nel portfolio' ) ) . '</div>';
        }

        ob_start();
        ?>
        <style>
            /* ── Grid / Masonry ── */
            <?php if ( $layout === 'masonry' ) : ?>
            .<?php echo $uid; ?>-grid {
                column-count: <?php echo $cols; ?>;
                column-gap: <?php echo $gap; ?>px;
            }
            .<?php echo $uid; ?>-grid .olo-pf-item {
                break-inside: avoid;
                margin-bottom: <?php echo $gap; ?>px;
            }
            <?php else : ?>
            .<?php echo $uid; ?>-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo $cols; ?>, 1fr);
                gap: <?php echo $gap; ?>px;
            }
            <?php endif; ?>

            /* ── Card ── */
            .<?php echo $uid; ?>-grid .olo-pf-item {
                position: relative;
                border-radius: <?php echo $radius; ?>;
                overflow: hidden;
                transition: opacity 0.4s ease, transform 0.4s ease;
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?>-grid .olo-pf-item{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>-grid .olo-pf-item:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?>-grid .olo-pf-item.olo-pf-hidden {
                <?php if ( $anim === 'scale' ) : ?>
                opacity: 0;
                transform: scale(0.8);
                position: absolute;
                width: 0;
                height: 0;
                overflow: hidden;
                margin: 0;
                padding: 0;
                <?php elseif ( $anim === 'slide' ) : ?>
                opacity: 0;
                transform: translateY(20px);
                position: absolute;
                width: 0;
                height: 0;
                overflow: hidden;
                margin: 0;
                padding: 0;
                <?php else : ?>
                opacity: 0;
                position: absolute;
                width: 0;
                height: 0;
                overflow: hidden;
                margin: 0;
                padding: 0;
                <?php endif; ?>
            }

            /* ── Image area ── */
            .<?php echo $uid; ?>-grid .olo-pf-img-wrap {
                position: relative;
                overflow: hidden;
                border-radius: <?php echo $radius; ?>;
                <?php if ( $ratio !== 'auto' ) : ?>
                padding-top: <?php echo $ratio_css; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-grid .olo-pf-img-wrap img {
                <?php if ( $ratio !== 'auto' ) : ?>
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                <?php else : ?>
                width: 100%;
                height: auto;
                <?php endif; ?>
                display: block;
                transition: transform 0.5s cubic-bezier(.25,.46,.45,.94);
            }

            /* ── Hover effects ── */
            <?php if ( $fx === 'zoom' ) : ?>
            .<?php echo $uid; ?>-grid .olo-pf-item:hover .olo-pf-img-wrap img {
                transform: scale(1.1);
            }
            <?php endif; ?>

            .<?php echo $uid; ?>-grid .olo-pf-overlay {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 16px;
                background: <?php echo $ov_color; ?>;
                color: var(--olo-color-primary-contrast, #FFFFFF);
                pointer-events: none;
                transition: opacity 0.4s ease, transform 0.4s ease;
                <?php if ( $fx === 'fade' || $fx === 'overlay' ) : ?>
                opacity: 0;
                <?php elseif ( $fx === 'slide-up' ) : ?>
                opacity: 0;
                transform: translateY(100%);
                <?php else : ?>
                display: none;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-grid .olo-pf-item:hover .olo-pf-overlay {
                <?php if ( $fx === 'fade' || $fx === 'overlay' ) : ?>
                opacity: <?php echo $ov_opa / 100; ?>;
                <?php elseif ( $fx === 'slide-up' ) : ?>
                opacity: <?php echo $ov_opa / 100; ?>;
                transform: translateY(0);
                <?php endif; ?>
            }

            /* ── Filter bar ── */
            .<?php echo $uid; ?>-filter {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 16px;
                align-items: center;
            }
            .<?php echo $uid; ?>-filter button {
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.25s ease;
                border: none;
                outline: none;
                color: <?php echo $flt_c; ?>;
                background: transparent;
                <?php if ( $filter_style === 'buttons' ) : ?>
                padding: 6px 16px;
                border-radius: 4px;
                background: rgba(255,255,255,0.06);
                <?php elseif ( $filter_style === 'pills' ) : ?>
                padding: 6px 18px;
                border-radius: 50px;
                background: rgba(255,255,255,0.06);
                <?php elseif ( $filter_style === 'underline' ) : ?>
                padding: 6px 10px;
                border-bottom: 2px solid transparent;
                <?php elseif ( $filter_style === 'dropdown' ) : ?>
                padding: 8px 16px;
                <?php endif; ?>
            }
            .<?php echo $uid; ?>-filter button:hover,
            .<?php echo $uid; ?>-filter button.active {
                color: var(--olo-color-primary-contrast, #FFFFFF);
                <?php if ( $filter_style === 'buttons' ) : ?>
                background: <?php echo $flt_ac; ?>;
                <?php elseif ( $filter_style === 'pills' ) : ?>
                background: <?php echo $flt_ac; ?>;
                <?php elseif ( $filter_style === 'underline' ) : ?>
                color: <?php echo $flt_ac; ?>;
                border-bottom-color: <?php echo $flt_ac; ?>;
                <?php elseif ( $filter_style === 'dropdown' ) : ?>
                background: <?php echo $flt_ac; ?>;
                <?php endif; ?>
            }

            /* ── Text below image ── */
            .<?php echo $uid; ?>-grid .olo-pf-text {
                padding: 12px 4px 4px;
            }
            .<?php echo $uid; ?>-grid .olo-pf-cat {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: <?php echo $flt_ac; ?>;
                margin-bottom: 2px;
            }
            .<?php echo $uid; ?>-grid .olo-pf-title {
                font-size: 15px;
                font-weight: 600;
                color: <?php echo $title_c; ?>;
                line-height: 1.3;
            }
            .<?php echo $uid; ?>-grid .olo-pf-desc {
                font-size: 13px;
                color: <?php echo $text_c; ?>;
                margin-top: 4px;
                line-height: 1.5;
            }

            /* ── Mobile ── */
            @media (max-width: 640px) {
                <?php if ( $layout === 'masonry' ) : ?>
                .<?php echo $uid; ?>-grid {
                    column-count: 1;
                }
                <?php else : ?>
                .<?php echo $uid; ?>-grid {
                    grid-template-columns: 1fr;
                }
                <?php endif; ?>
            }
            @media (min-width: 641px) and (max-width: 1024px) {
                <?php if ( $layout === 'masonry' ) : ?>
                .<?php echo $uid; ?>-grid {
                    column-count: 2;
                }
                <?php else : ?>
                .<?php echo $uid; ?>-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                <?php endif; ?>
            }
        </style>

        <?php if ( ! empty( $s['filter_bar'] ) ) : ?>
        <div class="<?php echo esc_attr( $uid ); ?>-filter" id="<?php echo esc_attr( $uid ); ?>-filter">
            <button class="active" data-filter="*"><?php echo $all_label; ?></button>
            <?php foreach ( $categories as $cat ) : ?>
            <button data-filter="<?php echo esc_attr( sanitize_title( $cat ) ); ?>"><?php echo esc_html( $cat ); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="<?php echo esc_attr( $uid ); ?>-grid" id="<?php echo esc_attr( $uid ); ?>-grid">
            <?php foreach ( $items as $item ) :
                $cat_slugs = [];
                $cat_names = array_map( 'trim', explode( ',', $item['category'] ) );
                foreach ( $cat_names as $cn ) {
                    if ( $cn !== '' ) {
                        $cat_slugs[] = sanitize_title( $cn );
                    }
                }
                $cat_data = implode( ',', $cat_slugs );
                $has_link = ! empty( $item['link'] );
                $tag_open = $has_link ? '<a href="' . esc_url( $item['link'] ) . '" class="olo-pf-item" data-categories="' . esc_attr( $cat_data ) . '" style="display:block;text-decoration:none;color:inherit;">' : '<div class="olo-pf-item" data-categories="' . esc_attr( $cat_data ) . '">';
                $tag_close = $has_link ? '</a>' : '</div>';
            ?>
                <?php echo $tag_open; ?>
                    <div class="olo-pf-img-wrap">
                        <?php if ( ! empty( $item['image'] ) ) : ?>
                        <img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" />
                        <?php else : ?>
                        <div style="<?php if ( $ratio !== 'auto' ) : ?>position:absolute;inset:0;<?php endif; ?>display:flex;align-items:center;justify-content:center;background:var(--olo-color-secondary, #1F2937);min-height:120px;">
                            <span style="font-size:32px;opacity:0.3;"><?php echo esc_html( olo_t( '&#x1F5BC;' ) ); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ( $fx !== 'none' ) : ?>
                        <div class="olo-pf-overlay">
                            <?php if ( ! empty( $s['show_title'] ) ) : ?>
                            <span style="font-weight:600;font-size:14px;"><?php echo esc_html( $item['title'] ); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ( $fx !== 'overlay' ) : ?>
                    <?php if ( ! empty( $s['show_title'] ) || ! empty( $s['show_category'] ) || ! empty( $s['show_excerpt'] ) ) : ?>
                    <div class="olo-pf-text">
                        <?php if ( ! empty( $s['show_category'] ) ) : ?>
                            <?php $first_cat = ! empty( $cat_names[0] ) ? $cat_names[0] : ''; ?>
                            <?php if ( $first_cat ) : ?>
                            <div class="olo-pf-cat"><?php echo esc_html( $first_cat ); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_title'] ) ) : ?>
                        <?php list( $pft_cls, $pft_data ) = $this->tfx_attrs( $s, "title", $item["title"] ); ?><div class="olo-pf-title<?php echo $pft_cls; ?>"<?php echo $pft_data; ?>><?php echo esc_html( $item["title"] ); ?></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $s['show_excerpt'] ) ) : ?>
                            <?php if ( ! empty( $item['description'] ) ) : ?>
                            <?php list( $pfd_cls, $pfd_data ) = $this->tfx_attrs( $s, "description", wp_strip_all_tags( $item["description"] ?? "" ) ); ?><div class="olo-pf-desc<?php echo $pfd_cls; ?>"<?php echo $pfd_data; ?>><?php echo wp_kses_post( $item["description"] ); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                <?php echo $tag_close; ?>
            <?php endforeach; ?>
        </div>

        <?php if ( ! empty( $s['filter_bar'] ) ) : ?>
        <script>
        (function(){
            var filterBar = document.getElementById('<?php echo $uid; ?>-filter');
            var grid = document.getElementById('<?php echo $uid; ?>-grid');
            if(!filterBar){return}
            if(!grid){return}
            var buttons = filterBar.querySelectorAll('button');
            buttons.forEach(function(btn){
                btn.addEventListener('click', function(){
                    buttons.forEach(function(b){ b.classList.remove('active'); });
                    btn.classList.add('active');
                    var f = btn.getAttribute('data-filter');
                    var items = grid.querySelectorAll('.olo-pf-item');
                    items.forEach(function(item){
                        var cats = (item.getAttribute('data-categories') || '').split(',');
                        if(f === '*'){
                            item.classList.remove('olo-pf-hidden');
                        } else {
                            var match = false;
                            var i;
                            for(i = 0; i < cats.length; i++){
                                if(cats[i] === f){
                                    match = true;
                                }
                            }
                            if(match){
                                item.classList.remove('olo-pf-hidden');
                            } else {
                                item.classList.add('olo-pf-hidden');
                            }
                        }
                    });
                });
            });
        })();
        </script>
        <?php endif; ?>
        <?php
        $tfx_css = $this->tfx_css( $s, '.' . $uid );
        if ( $tfx_css ) echo '<style>' . $tfx_css . '</style>';
        $this->tfx_print_script();
        return ob_get_clean();
    }
}
