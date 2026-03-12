<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_ServiceRelated_Tile extends Olo_Tile_Base {

    protected $type     = 'servicerelated';
    protected $name     = 'Strutture Correlate';
    protected $icon     = 'dashicons-networking';
    protected $category = 'booking';
    protected $defaults = [
        'mode'              => 'same_valley',
        'count'             => 3,
        'layout'            => 'grid',
        'columns'           => 3,
        'gap'               => 20,
        // Card
        'show_image'        => true,
        'image_height'      => 180,
        'show_valley'       => true,
        'show_altitude'     => true,
        'show_price'        => true,
        'show_capacity'     => true,
        'show_mushrooms'    => false,
        'show_link'         => true,
        'link_text'         => '',
        // Style
        'card_bg'           => '#FFFFFF',
        'card_radius'       => 12,
        'card_shadow'       => 'sm',
        'card_hover_effect' => 'lift',
        'title_size'        => 17,
        'title_color'       => '',
        'meta_color'        => '#6B7280',
        'price_color'       => '#6366F1',
        'btn_bg'            => '#6366F1',
        'btn_color'         => '#FFFFFF',
        'btn_radius'        => 8,
        // Heading
        'heading'           => 'Altre strutture',
        'heading_size'      => 22,
        'heading_color'     => '',
        'heading_align'     => 'left',
        // Slider
        'autoplay'          => true,
        'autoplay_speed'    => 4,
        'pause_hover'       => true,
        // Marquee
        'marquee_speed'     => 25,
        'marquee_direction' => 'left',
        'marquee_pause'     => true,
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        if ( ! post_type_exists( 'olo_service' ) ) {
            return '<div style="padding:24px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:8px">'
                 . '<p style="margin:0">Plugin <strong>Olo Booking</strong> non attivo.</p></div>';
        }

        global $post;
        $current_id = ( $post && $post->post_type === 'olo_service' ) ? $post->ID : 0;

        $services = $this->query_related( $s, $current_id );

        if ( empty( $services ) ) {
            return '';
        }

        $uid = 'olo-sr-' . wp_rand( 10000, 99999 );
        $layout = $s['layout'];

        ob_start();

        $this->render_styles( $uid, $s );

        echo '<div class="olo-sr-wrap" style="width:100%;max-width:100%;overflow:hidden">';

        // Heading
        $heading = trim( $s['heading'] );
        if ( $heading ) {
            $h_size  = absint( $s['heading_size'] ) ?: 22;
            $h_color = $this->safe_color_css( $s['heading_color'] ) ?: 'var(--olo-color-text, #374151)';
            $h_align = in_array( $s['heading_align'], [ 'left', 'center', 'right' ] ) ? $s['heading_align'] : 'left';
            echo '<h3 class="olo-sr-heading ' . esc_attr( $uid ) . '-h" style="font-size:' . $h_size . 'px;color:' . $h_color . ';text-align:' . $h_align . ';margin:0 0 16px">' . esc_html( $heading ) . '</h3>';
        }

        if ( $layout === 'marquee' ) {
            $this->render_marquee( $uid, $s, $services );
        } elseif ( $layout === 'slider' ) {
            $this->render_slider( $uid, $s, $services );
        } else {
            $this->render_grid( $uid, $s, $services );
        }

        echo '</div>';

        return ob_get_clean();
    }

    /* ─── Query ─── */

    private function query_related( $s, $current_id ) {
        $mode  = $s['mode'];
        $count = max( 2, min( 8, absint( $s['count'] ) ) );

        // Get all published services except current
        $args = [
            'post_type'      => 'olo_service',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'no_found_rows'  => true,
            'post__not_in'   => $current_id ? [ $current_id ] : [],
        ];

        if ( $mode === 'random' ) {
            $args['orderby']        = 'rand';
            $args['posts_per_page'] = $count;
            $posts = get_posts( $args );
            return $this->format_services( $posts );
        }

        $posts = get_posts( $args );
        if ( empty( $posts ) ) return [];

        $services = $this->format_services( $posts );

        switch ( $mode ) {
            case 'same_valley':
                $my_valley = $current_id ? get_post_meta( $current_id, '_olo_service_valley', true ) : '';
                if ( $my_valley ) {
                    $matched = array_values( array_filter( $services, fn( $sv ) => $sv['valley'] === $my_valley ) );
                    if ( ! empty( $matched ) ) {
                        shuffle( $matched );
                        $services = $matched;
                        break;
                    }
                }
                shuffle( $services );
                break;

            case 'nearest':
                if ( $current_id ) {
                    $my_lat = floatval( get_post_meta( $current_id, '_olo_service_latitude', true ) );
                    $my_lng = floatval( get_post_meta( $current_id, '_olo_service_longitude', true ) );
                    if ( $my_lat && $my_lng ) {
                        foreach ( $services as &$svc ) {
                            $dlat = $svc['lat'] - $my_lat;
                            $dlng = $svc['lng'] - $my_lng;
                            $svc['_dist'] = sqrt( $dlat * $dlat + $dlng * $dlng );
                        }
                        unset( $svc );
                        usort( $services, fn( $a, $b ) => ( $a['_dist'] ?? 999 ) <=> ( $b['_dist'] ?? 999 ) );
                    }
                }
                break;

            case 'similar_altitude':
                $my_alt = $current_id ? intval( get_post_meta( $current_id, '_olo_service_altitude', true ) ) : 0;
                foreach ( $services as &$svc ) {
                    $svc['_alt_diff'] = abs( ( $svc['altitude'] ?? 0 ) - $my_alt );
                }
                unset( $svc );
                usort( $services, fn( $a, $b ) => $a['_alt_diff'] <=> $b['_alt_diff'] );
                break;

            case 'same_club':
                $my_group = $current_id ? get_post_meta( $current_id, '_olo_service_club_group', true ) : '';
                if ( $my_group ) {
                    $matched = array_values( array_filter( $services, fn( $sv ) => $sv['club_group'] === $my_group ) );
                    if ( ! empty( $matched ) ) {
                        shuffle( $matched );
                        $services = $matched;
                        break;
                    }
                }
                shuffle( $services );
                break;

            default:
                shuffle( $services );
                break;
        }

        return array_slice( $services, 0, $count );
    }

    private function format_services( $posts ) {
        $result = [];
        foreach ( $posts as $p ) {
            $pid = $p->ID;
            $result[] = [
                'id'         => $pid,
                'title'      => get_the_title( $p ),
                'url'        => get_permalink( $pid ),
                'image'      => get_the_post_thumbnail_url( $pid, 'medium_large' ) ?: '',
                'valley'     => get_post_meta( $pid, '_olo_service_valley', true ) ?: '',
                'altitude'   => intval( get_post_meta( $pid, '_olo_service_altitude', true ) ),
                'price'      => get_post_meta( $pid, '_olo_service_price', true ) ?: '',
                'capacity'   => intval( get_post_meta( $pid, '_olo_service_capacity', true ) ),
                'mushrooms'  => intval( get_post_meta( $pid, '_olo_service_mushrooms', true ) ),
                'club_group' => get_post_meta( $pid, '_olo_service_club_group', true ) ?: '',
                'color'      => get_post_meta( $pid, '_olo_service_color', true ) ?: '#6366F1',
                'lat'        => floatval( get_post_meta( $pid, '_olo_service_latitude', true ) ),
                'lng'        => floatval( get_post_meta( $pid, '_olo_service_longitude', true ) ),
            ];
        }
        return $result;
    }

    /* ─── Card HTML ─── */

    private function render_card( $uid, $s, $svc ) {
        $img_h = absint( $s['image_height'] ) ?: 180;
        ?>
        <a href="<?php echo esc_url( $svc['url'] ); ?>" class="olo-sr-card <?php echo esc_attr( $uid ); ?>-card">
            <?php if ( ! empty( $s['show_image'] ) ) : ?>
                <div class="olo-sr-img" style="height:<?php echo $img_h; ?>px">
                    <?php if ( $svc['image'] ) : ?>
                        <img src="<?php echo esc_url( $svc['image'] ); ?>" alt="<?php echo esc_attr( $svc['title'] ); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="olo-sr-img-ph" style="background:<?php echo $this->safe_color_css( $svc['color'] ); ?>20">&#127968;</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="olo-sr-body">
                <h4 class="olo-sr-title"><?php echo esc_html( $svc['title'] ); ?></h4>
                <div class="olo-sr-meta">
                    <?php if ( ! empty( $s['show_valley'] ) && $svc['valley'] ) : ?>
                        <span class="olo-sr-valley">&#128205; <?php echo esc_html( $svc['valley'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_altitude'] ) && $svc['altitude'] ) : ?>
                        <span class="olo-sr-alt">&#9968; <?php echo number_format( $svc['altitude'], 0, ',', '.' ); ?> m</span>
                    <?php endif; ?>
                    <?php if ( ! empty( $s['show_capacity'] ) && $svc['capacity'] ) : ?>
                        <span class="olo-sr-cap">&#128101; <?php echo $svc['capacity']; ?></span>
                    <?php endif; ?>
                </div>
                <?php if ( ! empty( $s['show_price'] ) && $svc['price'] ) : ?>
                    <div class="olo-sr-price">&euro; <?php echo esc_html( number_format( (float) $svc['price'], 0, ',', '.' ) ); ?> <small><?php echo esc_html( olo_t( '/notte' ) ); ?></small></div>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_mushrooms'] ) && $svc['mushrooms'] ) : ?>
                    <div class="olo-sr-mush"><?php echo str_repeat( '&#127812;', $svc['mushrooms'] ); ?></div>
                <?php endif; ?>
                <?php if ( ! empty( $s['show_link'] ) ) : ?>
                    <span class="olo-sr-btn"><?php echo esc_html( $s['link_text'] ?: olo_t( 'Scopri' ) ); ?> &rarr;</span>
                <?php endif; ?>
            </div>
        </a>
        <?php
    }

    /* ─── Grid Layout ─── */

    private function render_grid( $uid, $s, $services ) {
        $cols = absint( $s['columns'] ) ?: 3;
        $gap  = absint( $s['gap'] ) ?: 20;
        ?>
        <div class="olo-sr-grid <?php echo esc_attr( $uid ); ?>" style="display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,1fr);gap:<?php echo $gap; ?>px">
            <?php foreach ( $services as $svc ) {
                $this->render_card( $uid, $s, $svc );
            } ?>
        </div>
        <style>
            @media(max-width:767px){.<?php echo $uid; ?>{grid-template-columns:1fr !important}}
        </style>
        <?php
    }

    /* ─── Slider Layout ─── */

    private function render_slider( $uid, $s, $services ) {
        $gap   = absint( $s['gap'] ) ?: 20;
        $auto  = ! empty( $s['autoplay'] );
        $speed = max( 2, absint( $s['autoplay_speed'] ) ) * 1000;
        $pause = ! empty( $s['pause_hover'] );
        ?>
        <div class="olo-sr-slider <?php echo esc_attr( $uid ); ?>" data-auto="<?php echo $auto ? '1' : '0'; ?>" data-speed="<?php echo $speed; ?>" data-pause="<?php echo $pause ? '1' : '0'; ?>">
            <div class="olo-sr-track" style="gap:<?php echo $gap; ?>px">
                <?php foreach ( $services as $svc ) {
                    $this->render_card( $uid, $s, $svc );
                } ?>
            </div>
            <button class="olo-sr-arrow olo-sr-prev" aria-label="Precedente">&#8249;</button>
            <button class="olo-sr-arrow olo-sr-next" aria-label="Successivo">&#8250;</button>
        </div>
        <style>
            .<?php echo $uid; ?>{position:relative;overflow:hidden}
            .<?php echo $uid; ?> .olo-sr-track{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;-ms-overflow-style:none;scrollbar-width:none}
            .<?php echo $uid; ?> .olo-sr-track::-webkit-scrollbar{display:none}
            .<?php echo $uid; ?> .olo-sr-card{scroll-snap-align:start;flex:0 0 calc(33.333% - <?php echo round( $gap * 2 / 3 ); ?>px);min-width:260px}
            .<?php echo $uid; ?> .olo-sr-arrow{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.9);border:1px solid var(--olo-color-border, #E5E7EB);box-shadow:0 2px 8px rgba(0,0,0,.1);cursor:pointer;font-size:22px;line-height:1;display:flex;align-items:center;justify-content:center;transition:opacity .2s;z-index:2}
            .<?php echo $uid; ?> .olo-sr-arrow:hover{background:#fff;box-shadow:0 4px 12px rgba(0,0,0,.15)}
            .<?php echo $uid; ?> .olo-sr-prev{left:8px}
            .<?php echo $uid; ?> .olo-sr-next{right:8px}
            @media(max-width:767px){.<?php echo $uid; ?> .olo-sr-card{flex:0 0 85%}}
        </style>
        <script>
        (function(){
            var el = document.querySelector('.<?php echo $uid; ?>');
            if(!el) return;
            var track = el.querySelector('.olo-sr-track');
            var prev  = el.querySelector('.olo-sr-prev');
            var next  = el.querySelector('.olo-sr-next');
            var card  = track.querySelector('.olo-sr-card');
            if(!card) return;
            var step = function(){ return card.offsetWidth + <?php echo $gap; ?>; };
            prev.addEventListener('click', function(){ track.scrollLeft -= step(); });
            next.addEventListener('click', function(){ track.scrollLeft += step(); });
            <?php if ( $auto ) : ?>
            var iv = setInterval(function(){ track.scrollLeft += step(); if(track.scrollLeft >= track.scrollWidth - track.clientWidth - 10) track.scrollLeft = 0; }, <?php echo $speed; ?>);
            <?php if ( $pause ) : ?>
            el.addEventListener('mouseenter', function(){ clearInterval(iv); });
            el.addEventListener('mouseleave', function(){ iv = setInterval(function(){ track.scrollLeft += step(); if(track.scrollLeft >= track.scrollWidth - track.clientWidth - 10) track.scrollLeft = 0; }, <?php echo $speed; ?>); });
            <?php endif; ?>
            <?php endif; ?>
        })();
        </script>
        <?php
    }

    /* ─── Marquee Layout ─── */

    private function render_marquee( $uid, $s, $services ) {
        $speed = max( 10, absint( $s['marquee_speed'] ) );
        $dir   = $s['marquee_direction'] === 'right' ? 'reverse' : 'normal';
        $pause = ! empty( $s['marquee_pause'] );
        $gap   = absint( $s['gap'] ) ?: 20;

        // Double the cards for seamless loop
        $doubled = array_merge( $services, $services );
        ?>
        <div class="olo-sr-marquee <?php echo esc_attr( $uid ); ?>">
            <div class="olo-sr-mq-track">
                <?php foreach ( $doubled as $svc ) {
                    $this->render_card( $uid, $s, $svc );
                } ?>
            </div>
        </div>
        <style>
            @keyframes <?php echo $uid; ?>-scroll {
                0%   { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }
            .<?php echo $uid; ?>{overflow:hidden}
            .<?php echo $uid; ?> .olo-sr-mq-track{display:flex;gap:<?php echo $gap; ?>px;width:max-content;animation:<?php echo $uid; ?>-scroll <?php echo $speed; ?>s linear infinite;animation-direction:<?php echo $dir; ?>}
            .<?php echo $uid; ?> .olo-sr-card{flex:0 0 280px}
            <?php if ( $pause ) : ?>
            .<?php echo $uid; ?>:hover .olo-sr-mq-track{animation-play-state:paused}
            <?php endif; ?>
        </style>
        <?php
    }

    /* ─── Shared Styles ─── */

    private function render_styles( $uid, $s ) {
        $radius  = Olo_Tile_Utils::border_radius( $s['card_radius'] ?? 0 );
        $bg      = $this->safe_color_css( $s['card_bg'] ) ?: 'var(--olo-color-background, #FFFFFF)';
        $shadow  = Olo_Tile_Utils::shadow( $s['card_shadow'] ?? 'none' );
        $hover   = $s['card_hover_effect'];
        $t_size  = absint( $s['title_size'] ) ?: 17;
        $t_color = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #374151)';
        $m_color = $this->safe_color_css( $s['meta_color'] ) ?: 'var(--olo-color-text-muted, #9CA3AF)';
        $p_color = $this->safe_color_css( $s['price_color'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $btn_bg  = $this->safe_color_css( $s['btn_bg'] ) ?: 'var(--olo-color-primary, #6366F1)';
        $btn_c   = $this->safe_color_css( $s['btn_color'] ) ?: 'var(--olo-color-primary-contrast, #FFFFFF)';
        $btn_r   = Olo_Tile_Utils::border_radius( $s['btn_radius'] ?? 0 );
        ?>
        <style>
            .<?php echo $uid; ?>-card{display:block;text-decoration:none;color:inherit;background:<?php echo $bg; ?>;border-radius:<?php echo $radius; ?>;box-shadow:<?php echo $shadow; ?>;overflow:hidden;transition:transform .3s ease,box-shadow .3s ease}
            <?php if ( $hover === 'lift' ) : ?>
            .<?php echo $uid; ?>-card:hover{transform:translateY(-6px);box-shadow:0 12px 28px rgba(0,0,0,.15)}
            <?php elseif ( $hover === 'scale' ) : ?>
            .<?php echo $uid; ?>-card:hover{transform:scale(1.03);box-shadow:0 8px 20px rgba(0,0,0,.12)}
            <?php elseif ( $hover === 'glow' ) : ?>
            .<?php echo $uid; ?>-card:hover{box-shadow:0 0 0 3px <?php echo $btn_bg; ?>44,0 8px 20px rgba(0,0,0,.1)}
            <?php endif; ?>
            .<?php echo $uid; ?>-card .olo-sr-img{overflow:hidden;position:relative;background:var(--olo-color-muted, #F3F4F6)}
            .<?php echo $uid; ?>-card .olo-sr-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
            .<?php echo $uid; ?>-card:hover .olo-sr-img img{transform:scale(1.06)}
            .<?php echo $uid; ?>-card .olo-sr-img-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--olo-color-text-muted, #9CA3AF)}
            .<?php echo $uid; ?>-card .olo-sr-body{padding:16px}
            .<?php echo $uid; ?>-card .olo-sr-title{font-size:<?php echo $t_size; ?>px;font-weight:600;color:<?php echo $t_color; ?>;margin:0 0 6px;line-height:1.3}
            .<?php echo $uid; ?>-card .olo-sr-meta{display:flex;flex-wrap:wrap;gap:8px;font-size:13px;color:<?php echo $m_color; ?>;margin-bottom:8px}
            .<?php echo $uid; ?>-card .olo-sr-price{font-size:16px;font-weight:700;color:<?php echo $p_color; ?>;margin-bottom:8px}
            .<?php echo $uid; ?>-card .olo-sr-price small{font-weight:400;font-size:12px;opacity:.7}
            .<?php echo $uid; ?>-card .olo-sr-mush{font-size:14px;margin-bottom:6px}
            .<?php echo $uid; ?>-card .olo-sr-btn{display:inline-block;padding:8px 18px;background:<?php echo $btn_bg; ?>;color:<?php echo $btn_c; ?>;border-radius:<?php echo $btn_r; ?>;font-size:13px;font-weight:600;transition:opacity .2s}
            .<?php echo $uid; ?>-card:hover .olo-sr-btn{opacity:.85}
        </style>
        <?php
    }
}
