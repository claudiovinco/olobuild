<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Soundcloud_Tile extends Olobuild_Tile_Base {

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
        $s = wp_parse_args( $settings, $this->defaults );

        $url    = trim( $s['url'] );
        $radius = Olobuild_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olobuild_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $height = absint( $s['height'] ) ?: 166;
        // Per TUTTI i rami (prima nasceva solo nel segnaposto: con un URL la classe restava vuota)
        $uid    = 'olo-sc-' . wp_rand( 10000, 99999 );

        // Nessun URL — mostra placeholder
        if ( empty( $url ) ) {
            ob_start();
            ?>
            <div class="olo-soundcloud <?php echo esc_attr( $uid ); ?>" style="text-align: center; padding: 40px 20px; background: linear-gradient(135deg, rgba(255,85,0,0.08) 0%, rgba(255,136,0,0.08) 100%); border: 2px dashed rgba(255,85,0,0.3); border-radius: <?php echo esc_attr( $radius ); ?>;">
                <div style="color: #ff5500; opacity: .8; font-size: 14px;">
                    <?php echo esc_html( olobuild_t( 'Inserisci URL SoundCloud' ) ); ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        // Colore del player: il parametro dell'iframe vuole un esadecimale. Un colore della Palette
        // (var(--olo-color-…)) si risolve nel suo hex (prima ricadeva sempre sull'arancio SoundCloud).
        $raw_color = ltrim( Olobuild_Tile_Utils::colore_hex( $s['color'] ), '#' );
        if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $raw_color ) ) {
            $raw_color = 'ff5500';
        }

        // L'oEmbed di WordPress serve SOLO a risolvere l'indirizzo canonico del brano
        // (api.soundcloud.com/tracks/…, anche dai link brevi on.soundcloud.com) e il titolo. Prima si
        // stampava il suo iframe: largo 500 px fissi e con i parametri di SoundCloud, così colore,
        // riproduzione automatica, copertina, autore e «Player visuale» venivano ignorati.
        $player_url = $url;
        $title      = olobuild_t( 'SoundCloud Player' );
        $oembed_html = wp_oembed_get( $url, [ 'height' => $height ] );
        if ( $oembed_html && preg_match( '/\ssrc="([^"]+)"/', $oembed_html, $src_m ) ) {
            $query = wp_parse_url( html_entity_decode( $src_m[1] ), PHP_URL_QUERY );
            parse_str( (string) $query, $src_args );
            if ( ! empty( $src_args['url'] ) && is_string( $src_args['url'] ) ) {
                $player_url = $src_args['url'];
            }
            if ( preg_match( '/\stitle="([^"]+)"/', $oembed_html, $title_m ) ) {
                $title = html_entity_decode( $title_m[1], ENT_QUOTES );
            }
        }

        $auto_play    = ! empty( $s['auto_play'] )    ? 'true' : 'false';
        $show_artwork = ! empty( $s['show_artwork'] )  ? 'true' : 'false';
        $show_user    = ! empty( $s['show_user'] )     ? 'true' : 'false';
        $visual       = ! empty( $s['visual'] )        ? 'true' : 'false';

        $iframe_url = 'https://w.soundcloud.com/player/?url=' . rawurlencode( $player_url )
            . '&color=%23' . $raw_color
            . '&auto_play=' . $auto_play
            . '&show_artwork=' . $show_artwork
            . '&show_user=' . $show_user
            . '&visual=' . $visual;

        ob_start();
        $sc_hash = $radius_hover_css !== '' ? substr( md5( $radius_hover_css . $url ), 0, 6 ) : '';
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>.olo-sc-hr-<?php echo $sc_hash; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $sc_hash is an internally generated md5 fragment; hover radius CSS built by Olobuild_Tile_Utils::radius_force_css() (absint-forced px values) ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}</style>
        <?php endif; ?>
        <div class="olo-soundcloud <?php echo esc_attr( $uid ); ?><?php echo esc_attr( $radius_hover_css !== '' ? ' olo-sc-hr-' . $sc_hash : '' ); ?>" style="border-radius: <?php echo esc_attr( $radius ); ?>; overflow: hidden;<?php if ( $radius_hover_css !== '' ) echo 'transition:border-radius 400ms cubic-bezier(.4,0,.2,1);'; ?>">
            <iframe
                src="<?php echo esc_url( $iframe_url ); ?>"
                width="100%"
                height="<?php echo (int) $height; ?>"
                scrolling="no"
                frameborder="no"
                allow="autoplay"
                title="<?php echo esc_attr( $title ); ?>"
                loading="lazy"
            ></iframe>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_css() from sanitized settings; $uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olobuild_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }
}
