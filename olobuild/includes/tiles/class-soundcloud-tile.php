<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Soundcloud_Tile extends Olo_Tile_Base {

    protected $type     = 'soundcloud';
    protected $name     = 'SoundCloud';
    protected $icon     = 'dashicons-format-audio';
    protected $category = 'media';
    protected $defaults = [
        'url'           => '',
        'auto_play'     => false,
        'show_artwork'  => true,
        'show_user'     => true,
        'color'         => '#ff5500',
        'visual'        => true,
        'height'        => '166',
        'alignment'     => 'center',
        'border_radius' => '8',
    ];

    public function get_controls() {
        return [];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $url    = trim( $s['url'] );
        $radius = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $height = absint( $s['height'] ) ?: 166;

        // Nessun URL — mostra placeholder
        if ( empty( $url ) ) {
            ob_start();
            ?>
            <div class="olo-soundcloud" style="text-align: center; padding: 40px 20px; background: linear-gradient(135deg, rgba(255,85,0,0.08) 0%, rgba(255,136,0,0.08) 100%); border: 2px dashed rgba(255,85,0,0.3); border-radius: <?php echo $radius; ?>;">
                <div style="color: #ff5500; opacity: .8; font-size: 14px;">
                    <?php echo esc_html( olo_t( 'Inserisci URL SoundCloud' ) ); ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        // Sanitizza colore: rimuovi il # e accetta solo hex
        $raw_color = ltrim( $s['color'], '#' );
        if ( ! preg_match( '/^[0-9a-fA-F]{3,6}$/', $raw_color ) ) {
            $raw_color = 'ff5500';
        }

        // Prova oEmbed di WordPress
        $oembed_html = wp_oembed_get( $url, [ 'height' => $height ] );

        if ( $oembed_html ) {
            ob_start();
            ?>
            <div class="olo-soundcloud" style="border-radius: <?php echo $radius; ?>; overflow: hidden;">
                <?php echo $oembed_html; ?>
            </div>
            <?php
            return ob_get_clean();
        }

        // Fallback: iframe manuale
        $auto_play    = ! empty( $s['auto_play'] )    ? 'true' : 'false';
        $show_artwork = ! empty( $s['show_artwork'] )  ? 'true' : 'false';
        $show_user    = ! empty( $s['show_user'] )     ? 'true' : 'false';
        $visual       = ! empty( $s['visual'] )        ? 'true' : 'false';

        $iframe_url = 'https://w.soundcloud.com/player/?url=' . rawurlencode( $url )
            . '&color=%23' . $raw_color
            . '&auto_play=' . $auto_play
            . '&show_artwork=' . $show_artwork
            . '&show_user=' . $show_user
            . '&visual=' . $visual;

        ob_start();
        ?>
        <div class="olo-soundcloud" style="border-radius: <?php echo $radius; ?>; overflow: hidden;">
            <iframe
                src="<?php echo esc_url( $iframe_url ); ?>"
                width="100%"
                height="<?php echo $height; ?>"
                scrolling="no"
                frameborder="no"
                allow="autoplay"
                title="SoundCloud Player"
                loading="lazy"
            ></iframe>
        </div>
        <?php
        return ob_get_clean();
    }
}
