<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Social_Tile extends Olo_Tile_Base {

    protected $type     = 'social';
    protected $name     = 'Link social';
    protected $icon     = 'dashicons-share';
    protected $category = 'content';
    protected $defaults = [
        'links'     => "facebook|https://facebook.com\ntwitter|https://twitter.com\ninstagram|https://instagram.com\nlinkedin|https://linkedin.com",
        'size'      => '32',
        'alignment' => 'center',
        'gap'       => '12',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'links',     'type' => 'textarea', 'label' => 'Links (platform|url per line)' ],
            [ 'key' => 'size',      'type' => 'range', 'label' => 'Icon Size (px)', 'min' => 20, 'max' => 60, 'step' => 4 ],
            [ 'key' => 'alignment', 'type' => 'select', 'label' => 'Alignment', 'options' => [ 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ] ],
            [ 'key' => 'gap',       'type' => 'range', 'label' => 'Gap (px)', 'min' => 4, 'max' => 32, 'step' => 4 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $platform_icons = [
            'facebook'  => '&#x1F1EB;',
            'twitter'   => '&#x1F426;',
            'instagram' => '&#x1F4F7;',
            'linkedin'  => '&#x1F4BC;',
            'youtube'   => '&#x25B6;&#xFE0F;',
            'tiktok'    => '&#x1F3B5;',
            'github'    => '&#x1F4BB;',
            'email'     => '&#x2709;&#xFE0F;',
            'website'   => '&#x1F310;',
        ];

        $platform_colors = [
            'facebook'  => '#1877F2',
            'twitter'   => '#1DA1F2',
            'instagram' => '#E4405F',
            'linkedin'  => '#0A66C2',
            'youtube'   => '#FF0000',
            'tiktok'    => '#000000',
            'github'    => '#333333',
            'email'     => '#6366F1',
            'website'   => '#6366F1',
        ];

        $links = $this->parse_links( $s['links'] );
        $size = absint( $s['size'] );
        $gap  = absint( $s['gap'] );
        $align_map = [ 'left' => 'uk-flex-left', 'center' => 'uk-flex-center', 'right' => 'uk-flex-right' ];
        $align_class = $align_map[ $s['alignment'] ] ?? 'uk-flex-center';

        ob_start();
        ?>
        <div class="olo-social uk-flex uk-flex-wrap <?php echo esc_attr( $align_class ); ?>" style="gap: <?php echo $gap; ?>px; padding: 16px;">
            <?php foreach ( $links as $link ) :
                $icon = $platform_icons[ $link['platform'] ] ?? '&#x1F517;';
                $color = $platform_colors[ $link['platform'] ] ?? '#6366F1';
            ?>
                <a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="uk-flex uk-flex-center uk-flex-middle" style="width: <?php echo $size; ?>px; height: <?php echo $size; ?>px; background: <?php echo esc_attr( $color ); ?>; border-radius: 50%; text-decoration: none; font-size: <?php echo intval( $size * 0.5 ); ?>px; line-height: 1;" title="<?php echo esc_attr( ucfirst( $link['platform'] ) ); ?>">
                    <?php echo $icon; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function parse_links( $text ) {
        $links = [];
        $lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
        foreach ( $lines as $line ) {
            $parts = explode( '|', $line, 2 );
            if ( count( $parts ) === 2 ) {
                $links[] = [ 'platform' => strtolower( trim( $parts[0] ) ), 'url' => trim( $parts[1] ) ];
            }
        }
        return $links;
    }
}
