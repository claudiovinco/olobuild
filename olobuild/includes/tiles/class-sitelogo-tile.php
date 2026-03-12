<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_SiteLogo_Tile extends Olo_Tile_Base {

    protected $type     = 'sitelogo';
    protected $name     = 'Logo sito';
    protected $icon     = 'dashicons-admin-home';
    protected $category = 'navigation';
    protected $defaults = [
        'source'       => 'auto',
        'custom_image' => '',
        'max_height'   => 50,
        'link_home'    => true,
        'show_tagline' => false,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'source', 'type' => 'select', 'label' => 'Source', 'options' => [
                'auto'         => 'WP Site Logo / Title',
                'custom_image' => 'Custom Image',
            ]],
            [ 'key' => 'custom_image', 'type' => 'image', 'label' => 'Logo Image' ],
            [ 'key' => 'max_height', 'type' => 'range', 'label' => 'Max Height (px)', 'min' => 20, 'max' => 120 ],
            [ 'key' => 'link_home', 'type' => 'toggle', 'label' => 'Link to Homepage' ],
            [ 'key' => 'show_tagline', 'type' => 'toggle', 'label' => 'Show Tagline' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $max_height = absint( $s['max_height'] );
        $link_home  = ! empty( $s['link_home'] );
        $home_url   = esc_url( home_url( '/' ) );

        ob_start();
        ?>
        <div class="olo-sitelogo">
            <?php
            if ( $s['source'] === 'custom_image' && ! empty( $s['custom_image'] ) ) {
                // Custom image logo
                $img = '<img src="' . esc_url( $s['custom_image'] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="max-height: ' . $max_height . 'px; width: auto; display: block;" />';
                if ( $link_home ) {
                    echo '<a href="' . $home_url . '" class="olo-sitelogo-link">' . $img . '</a>';
                } else {
                    echo $img;
                }
            } else {
                // Auto: WP custom logo or site title
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                if ( $custom_logo_id ) {
                    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                    if ( $logo_url ) {
                        $img = '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="max-height: ' . $max_height . 'px; width: auto; display: block;" />';
                        if ( $link_home ) {
                            echo '<a href="' . $home_url . '" class="olo-sitelogo-link">' . $img . '</a>';
                        } else {
                            echo $img;
                        }
                    }
                } else {
                    // Text fallback
                    $site_name = esc_html( get_bloginfo( 'name' ) );
                    if ( $link_home ) {
                        echo '<a href="' . $home_url . '" class="olo-sitelogo-link olo-sitelogo-text uk-h3 uk-margin-remove">' . $site_name . '</a>';
                    } else {
                        echo '<span class="olo-sitelogo-text uk-h3 uk-margin-remove">' . $site_name . '</span>';
                    }
                }
            }
            ?>
            <?php if ( ! empty( $s['show_tagline'] ) ) : ?>
                <p class="olo-sitelogo-tagline uk-text-small uk-text-muted uk-margin-remove-top"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
