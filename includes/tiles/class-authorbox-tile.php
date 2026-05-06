<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Authorbox_Tile extends Olo_Tile_Base {

    protected $type     = 'authorbox';
    protected $name     = 'Author Box';
    protected $icon     = 'dashicons-admin-users';
    protected $category = 'dynamic';
    protected $defaults = [
        'layout'               => 'horizontal',
        'avatar_size'          => '80',
        'show_avatar'          => true,
        'show_name'            => true,
        'show_bio'             => true,
        'show_role'            => false,
        'show_post_count'      => false,
        'show_website'         => false,
        'name_tag'             => 'h3',
        'name_color'           => '#F3F4F6',
        'name_size'            => '20',
        'name_weight'          => '700',
        'bio_color'            => '#9CA3AF',
        'bio_size'             => '14',
        'role_color'           => '#818CF8',
        'role_size'            => '13',
        'link_color'           => '#6366F1',
        'count_color'          => '#9CA3AF',
        'background_color'     => '',
        'border_radius'        => '8',
        'padding'              => '20',
        'avatar_border_radius' => '50',
        'avatar_border_width'  => '0',
        'avatar_border_color'  => '#6366F1',
        'border_width'            => '0',
        'border_color'            => '#374151',
        'gap'                     => '16',
        'text_align'              => 'left',
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

    public function render( $settings ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-ab-' . wp_rand( 10000, 99999 );

        // Determine author
        $author_id = 0;

        // Try from current post
        if ( is_singular() ) {
            $post = get_post();
            if ( $post ) {
                $author_id = (int) $post->post_author;
            }
        }

        // Fallback: author archive
        if ( ! $author_id ) {
            $queried = get_queried_object();
            if ( $queried instanceof WP_User ) {
                $author_id = $queried->ID;
            }
        }

        // No author found — placeholder
        if ( ! $author_id ) {
            $bg      = $this->safe_color_css( $s['background_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
            $fg      = $this->safe_color_css( $s['name_color'] ) ?: '#F3F4F6';
            $rad     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
            $rad_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
            $pad     = absint( $s['padding'] );
            return '<div class="olo-authorbox" style="background:' . $bg . ';color:' . $fg . ';border-radius:' . $rad . ';padding:' . $pad . 'px;text-align:center;font-size:14px;">' . olo_t( 'Autore non disponibile in questo contesto' ) . '</div>';
        }

        // Gather author data
        $author_name = get_the_author_meta( 'display_name', $author_id );
        $author_bio  = get_the_author_meta( 'description', $author_id );
        $author_url  = get_the_author_meta( 'url', $author_id );
        $author_email = get_the_author_meta( 'user_email', $author_id );
        $user_obj    = get_userdata( $author_id );

        // Avatar
        $av_size   = max( 40, min( 160, absint( $s['avatar_size'] ) ) );
        $avatar_url = get_avatar_url( $author_id, [ 'size' => $av_size * 2 ] ); // 2x for retina

        // Role
        $role_label = '';
        if ( $user_obj ) {
            $roles = $user_obj->roles;
            if ( ! empty( $roles ) ) {
                $role_slug = $roles[0];
                $wp_roles  = wp_roles();
                $role_label = isset( $wp_roles->role_names[ $role_slug ] )
                    ? translate_user_role( $wp_roles->role_names[ $role_slug ] )
                    : ucfirst( $role_slug );
            }
        }

        // Post count
        $post_count = count_user_posts( $author_id, 'post', true );

        // Settings
        $layout     = $s['layout'] === 'vertical' ? 'vertical' : 'horizontal';
        $show_avatar = filter_var( $s['show_avatar'], FILTER_VALIDATE_BOOLEAN );
        $show_name   = filter_var( $s['show_name'], FILTER_VALIDATE_BOOLEAN );
        $show_bio    = filter_var( $s['show_bio'], FILTER_VALIDATE_BOOLEAN );
        $show_role   = filter_var( $s['show_role'], FILTER_VALIDATE_BOOLEAN );
        $show_count  = filter_var( $s['show_post_count'], FILTER_VALIDATE_BOOLEAN );
        $show_web    = filter_var( $s['show_website'], FILTER_VALIDATE_BOOLEAN );
        $name_tag    = in_array( $s['name_tag'], [ 'h3', 'h4', 'h5', 'div' ], true ) ? $s['name_tag'] : 'h3';

        // Colors
        $bg_col     = $this->safe_color_css( $s['background_color'] ) ?: 'var(--olo-color-muted, #F3F4F6)';
        $name_col   = $this->safe_color_css( $s['name_color'] ) ?: '#F3F4F6';
        $bio_col    = $this->safe_color_css( $s['bio_color'] ) ?: '#9CA3AF';
        $role_col   = $this->safe_color_css( $s['role_color'] ) ?: '#818CF8';
        $link_col   = $this->safe_color_css( $s['link_color'] ) ?: '#6366F1';
        $count_col  = $this->safe_color_css( $s['count_color'] ) ?: '#9CA3AF';

        // Sizes
        $name_size  = max( 14, min( 32, absint( $s['name_size'] ) ) );
        $name_wt    = in_array( $s['name_weight'], [ '400', '500', '600', '700' ], true ) ? $s['name_weight'] : '700';
        $bio_size   = max( 11, min( 18, absint( $s['bio_size'] ) ) );
        $role_size  = max( 10, min( 18, absint( $s['role_size'] ) ) );
        $gap        = absint( $s['gap'] );
        $padding = Olo_Tile_Utils::spacing_css( $s['tile_padding'] ?? $s['padding'] ?? 20, 20 );
        $radius     = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $av_radius  = max( 0, min( 50, Olo_Tile_Utils::radius_int( $s['avatar_border_radius'] ) ) );
        $text_align = in_array( $s['text_align'], [ 'left', 'center', 'right' ], true ) ? $s['text_align'] : 'left';

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> {
                background: <?php echo $bg_col; ?>;
                border-radius: <?php echo $radius; ?>;
                padding: <?php echo $padding; ?>;
                <?php
                $bw = absint( $s['border_width'] );
                if ( $bw > 0 ) :
                    $bc = $this->safe_color_css( $s['border_color'] ) ?: '#374151';
                ?>
                border: <?php echo $bw; ?>px solid <?php echo $bc; ?>;
                <?php endif; ?>
            }
            <?php if ( $radius_hover_css !== '' ) : ?>.<?php echo $uid; ?>{transition:border-radius 400ms cubic-bezier(.4,0,.2,1)}.<?php echo $uid; ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}<?php endif; ?>
            .<?php echo $uid; ?> .olo-ab-layout {
                display: flex;
                gap: <?php echo $gap; ?>px;
                <?php if ( $layout === 'vertical' ) : ?>
                flex-direction: column;
                align-items: center;
                text-align: center;
                <?php else : ?>
                align-items: flex-start;
                text-align: <?php echo $text_align; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ab-avatar {
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-ab-avatar img {
                width: <?php echo $av_size; ?>px;
                height: <?php echo $av_size; ?>px;
                border-radius: <?php echo $av_radius; ?>%;
                object-fit: cover;
                display: block;
                <?php
                $abw = absint( $s['avatar_border_width'] );
                if ( $abw > 0 ) :
                    $abc = $this->safe_color_css( $s['avatar_border_color'] ) ?: '#6366F1';
                ?>
                border: <?php echo $abw; ?>px solid <?php echo $abc; ?>;
                <?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-ab-info {
                flex: 1;
                min-width: 0;
            }
            .<?php echo $uid; ?> .olo-ab-name {
                margin: 0 0 4px 0;
                padding: 0;
                font-size: <?php echo $name_size; ?>px;
                font-weight: <?php echo $name_wt; ?>;
                color: <?php echo $name_col; ?>;
                line-height: 1.3;
            }
            .<?php echo $uid; ?> .olo-ab-role {
                font-size: <?php echo $role_size; ?>px;
                color: <?php echo $role_col; ?>;
                font-weight: 500;
                margin-bottom: 8px;
            }
            .<?php echo $uid; ?> .olo-ab-bio {
                font-size: <?php echo $bio_size; ?>px;
                color: <?php echo $bio_col; ?>;
                line-height: 1.55;
                margin: 8px 0;
            }
            .<?php echo $uid; ?> .olo-ab-count {
                font-size: 13px;
                color: <?php echo $count_col; ?>;
                margin-top: 6px;
            }
            .<?php echo $uid; ?> .olo-ab-website a {
                font-size: 13px;
                color: <?php echo $link_col; ?>;
                text-decoration: underline;
                transition: opacity 0.2s;
            }
            .<?php echo $uid; ?> .olo-ab-website a:hover {
                opacity: 0.8;
            }
        </style>
        <div class="olo-authorbox <?php echo esc_attr( $uid ); ?>">
            <div class="olo-ab-layout">
                <?php if ( $show_avatar ) : ?>
                <div class="olo-ab-avatar">
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" loading="lazy" />
                </div>
                <?php endif; ?>

                <div class="olo-ab-info">
                    <?php if ( $show_name ) : ?>
                    <<?php echo $name_tag; ?> class="olo-ab-name"><?php echo esc_html( $author_name ); ?></<?php echo $name_tag; ?>>
                    <?php endif; ?>

                    <?php if ( $show_role && $role_label ) : ?>
                    <div class="olo-ab-role"><?php echo esc_html( $role_label ); ?></div>
                    <?php endif; ?>

                    <?php if ( $show_bio && $author_bio ) : ?>
                    <div class="olo-ab-bio"><?php echo wp_kses_post( wpautop( $author_bio ) ); ?></div>
                    <?php endif; ?>

                    <?php if ( $show_count ) : ?>
                    <div class="olo-ab-count"><?php echo esc_html( $post_count . ' ' . olo_t( 'articoli pubblicati' ) ); ?></div>
                    <?php endif; ?>

                    <?php if ( $show_web && $author_url ) : ?>
                    <div class="olo-ab-website">
                        <a href="<?php echo esc_url( $author_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( wp_parse_url( $author_url, PHP_URL_HOST ) ?: $author_url ); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        // Border system
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
}
