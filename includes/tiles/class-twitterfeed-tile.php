<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Twitterfeed_Tile extends Olo_Tile_Base {

    protected $type     = 'twitterfeed';
    protected $name     = 'X Timeline';
    protected $icon     = 'dashicons-twitter';
    protected $category = 'marketing';
    protected $defaults = [
        'url'           => '',
        'embed_type'    => 'timeline',
        'theme'         => 'light',
        'width'         => '',
        'height'        => '600',
        'chrome'        => 'noheader,nofooter,noborders,noscrollbar',
        'tweet_limit'   => '5',
        'language'      => 'it',
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
        return [
            [ 'key' => 'url',           'type' => 'text',   'label' => 'URL profilo X / Tweet' ],
            [ 'key' => 'embed_type',    'type' => 'select', 'label' => 'Tipo embed', 'options' => [ 'timeline' => 'Timeline', 'tweet' => 'Tweet singolo' ] ],
            [ 'key' => 'theme',         'type' => 'select', 'label' => 'Tema', 'options' => [ 'light' => 'Chiaro', 'dark' => 'Scuro' ] ],
            [ 'key' => 'width',         'type' => 'text',   'label' => 'Larghezza (px)' ],
            [ 'key' => 'height',        'type' => 'range',  'label' => 'Altezza (px)', 'min' => 200, 'max' => 1200, 'step' => 50 ],
            [ 'key' => 'chrome',        'type' => 'text',   'label' => 'Chrome' ],
            [ 'key' => 'tweet_limit',   'type' => 'range',  'label' => 'Limite tweet', 'min' => 1, 'max' => 20 ],
            [ 'key' => 'language',      'type' => 'select', 'label' => 'Lingua', 'options' => [ 'it' => 'Italiano', 'en' => 'English', 'de' => 'Deutsch', 'fr' => 'Francais', 'es' => 'Espanol' ] ],
            [ 'key' => 'alignment',     'type' => 'select', 'label' => 'Allineamento', 'options' => [ 'left' => 'Sinistra', 'center' => 'Centro', 'right' => 'Destra' ] ],
            [ 'key' => 'border_radius', 'type' => 'range',  'label' => 'Arrotondamento (px)', 'min' => 0, 'max' => 30 ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $url          = trim( $s['url'] );
        $embed_type   = $s['embed_type'] ?: 'timeline';
        $theme        = in_array( $s['theme'], [ 'light', 'dark' ], true ) ? $s['theme'] : 'light';
        $width        = $s['width'] ? absint( $s['width'] ) : '';
        $height       = absint( $s['height'] ) ?: 600;
        $chrome       = sanitize_text_field( $s['chrome'] );
        $tweet_limit  = absint( $s['tweet_limit'] ) ?: 5;
        $language     = sanitize_text_field( $s['language'] ?: 'it' );
        $alignment    = $s['alignment'] ?: 'center';
        $radius       = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );

        $flex_map  = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
        $flex_just = $flex_map[ $alignment ] ?? 'center';

        $wrapper_style = 'display:flex;justify-content:' . esc_attr( $flex_just ) . ';';
        if ( $radius && $radius !== '0px' ) {
            $wrapper_style .= 'border-radius:' . $radius . ';overflow:hidden;';
        }
        if ( $radius_hover_css !== '' ) {
            $wrapper_style .= 'transition:border-radius 400ms cubic-bezier(.4,0,.2,1);overflow:hidden;';
        }
        $tw_uid = 'olo-tw-' . wp_unique_id();

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>.<?php echo $tw_uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tw_uid is internally generated; hover radius CSS built by Olo_Tile_Utils::radius_force_css() (absint-forced px values) ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}</style>
        <?php endif; ?>
        <div class="olo-twitterfeed <?php echo esc_attr( $tw_uid ); ?>" style="<?php echo esc_attr( $wrapper_style ); ?>">
            <?php if ( empty( $url ) ) : ?>
                <div style="padding:40px 20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:<?php echo esc_attr( $theme === 'dark' ? '#15202b' : 'var(--olo-color-muted, #F3F4F6)' ); ?>;border-radius:<?php echo esc_attr( $radius ); ?>;width:100%;max-width:550px;">
                    <?php echo esc_html( function_exists( 'olo_t' ) ? olo_t( 'Inserisci URL profilo X/Twitter' ) : 'Inserisci URL profilo X/Twitter' ); ?>
                </div>
            <?php elseif ( $embed_type === 'tweet' ) : ?>
                <?php
                // Single tweet via oEmbed
                $oembed_html = wp_oembed_get( esc_url( $url ), [
                    'theme' => $theme,
                    'lang'  => $language,
                ] );
                if ( $oembed_html ) :
                    $container_style = '';
                    if ( $width ) {
                        $container_style .= 'max-width:' . $width . 'px;';
                    }
                    ?>
                    <div style="<?php echo esc_attr( $container_style ); ?>">
                        <?php echo $oembed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- embed HTML returned by wp_oembed_get() (WordPress core oEmbed, whitelisted providers) ?>
                    </div>
                <?php else : ?>
                    <div style="padding:40px 20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">
                        <?php echo esc_html( function_exists( 'olo_t' ) ? olo_t( 'Impossibile caricare il tweet' ) : 'Impossibile caricare il tweet' ); ?>
                    </div>
                <?php endif; ?>
                <?php $this->enqueue_twitter_js(); ?>
            <?php else : ?>
                <?php
                // Timeline embed
                $data_attrs = '';
                $data_attrs .= ' data-height="' . esc_attr( $height ) . '"';
                $data_attrs .= ' data-theme="' . esc_attr( $theme ) . '"';
                if ( $chrome ) {
                    $data_attrs .= ' data-chrome="' . esc_attr( str_replace( ',', ' ', $chrome ) ) . '"';
                }
                $data_attrs .= ' data-tweet-limit="' . esc_attr( $tweet_limit ) . '"';
                $data_attrs .= ' data-lang="' . esc_attr( $language ) . '"';
                if ( $width ) {
                    $data_attrs .= ' data-width="' . esc_attr( $width ) . '"';
                }
                ?>
                <a
                    class="twitter-timeline"
                    <?php echo $data_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string assembled above with esc_attr() on every value ?>
                    href="<?php echo esc_url( $url ); ?>"
                ><?php echo esc_html( olo_t( 'Tweets' ) ); ?></a>
                <?php $this->enqueue_twitter_js(); ?>
            <?php endif; ?>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$tw_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$tw_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$tw_uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized settings; $tw_uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Enqueue Twitter widgets.js once per page via wp_enqueue_script.
     */
    private function enqueue_twitter_js() {
        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- SDK/embed di terze parti da URL remoto: versione controllata dal provider, non si versiona un asset remoto
        wp_enqueue_script( 'twitter-widgets', 'https://platform.twitter.com/widgets.js', [], null, true );
    }
}
