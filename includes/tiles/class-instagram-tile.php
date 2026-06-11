<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Instagram_Tile extends Olo_Tile_Base {

    protected $type     = 'instagram';
    protected $name     = 'Instagram';
    protected $icon     = 'dashicons-instagram';
    protected $category = 'marketing';
    protected $defaults = [
        'url'              => '',
        'embed_type'       => 'post',
        'width'            => '100%',
        'caption'          => true,
        'background_color' => '',
        'border_radius'    => '8',
        'alignment'        => 'center',
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
            [ 'key' => 'url',              'type' => 'text',   'label' => 'URL Instagram' ],
            [ 'key' => 'embed_type',       'type' => 'select', 'label' => 'Tipo embed', 'options' => [ 'post' => 'Post / Reel', 'profile' => 'Profilo' ] ],
            [ 'key' => 'width',            'type' => 'text',   'label' => 'Larghezza' ],
            [ 'key' => 'caption',          'type' => 'toggle', 'label' => 'Mostra didascalia' ],
            [ 'key' => 'background_color', 'type' => 'color',  'label' => 'Colore sfondo' ],
            [ 'key' => 'border_radius',    'type' => 'range',  'label' => 'Arrotondamento (px)', 'min' => 0, 'max' => 30 ],
            [ 'key' => 'alignment',        'type' => 'select', 'label' => 'Allineamento', 'options' => [ 'left' => 'Sinistra', 'center' => 'Centro', 'right' => 'Destra' ] ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $url          = trim( $s['url'] );
        $embed_type   = $s['embed_type'] ?: 'post';
        $width        = $s['width'] ?: '100%';
        $show_caption = ! empty( $s['caption'] );
        $bg_color     = $this->safe_color_css( $s['background_color'] );
        // border_radius ora è un oggetto {tl,tr,br,bl} (type:'border-radius' + withHover);
        // build_border_radius_css gestisce sia l'oggetto sia il vecchio scalare ('8').
        $radius       = $this->build_border_radius_css( $s['border_radius'] ?? 0 );
        $radius_hover_css = Olo_Tile_Utils::radius_force_css( $s['border_radius_hover'] ?? null );
        $alignment    = $s['alignment'] ?: 'center';

        $align_map = [ 'left' => 'left', 'center' => 'center', 'right' => 'right' ];
        $text_align = $align_map[ $alignment ] ?? 'center';

        $wrapper_style = 'text-align:' . $text_align . ';';
        if ( $bg_color ) {
            $wrapper_style .= 'background-color:' . $bg_color . ';';
        }
        if ( $radius && $radius !== '0px' ) {
            $wrapper_style .= 'border-radius:' . $radius . ';overflow:hidden;';
        }
        $ig_uid = 'olo-ig-' . wp_unique_id();
        if ( $radius_hover_css !== '' ) {
            $radius_dur = absint( $s['border_radius_hover_duration'] ?? 300 ) ?: 300;
            $wrapper_style .= 'transition:border-radius ' . $radius_dur . 'ms cubic-bezier(.4,0,.2,1);';
            if ( ! ( $radius && $radius !== '0px' ) ) {
                // ensure overflow:hidden so transition clips correctly even when base is 0
                $wrapper_style .= 'overflow:hidden;';
            }
        }

        ob_start();
        ?>
        <?php if ( $radius_hover_css !== '' ) : ?>
        <style>.<?php echo $ig_uid; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $ig_uid is internally generated; radius built by Olo_Tile_Utils::radius_force_css() from absint() values ?>:hover{border-radius:<?php echo $radius_hover_css; ?> !important}</style>
        <?php endif; ?>
        <div class="olo-instagram <?php echo esc_attr( $ig_uid ); ?>" style="<?php echo esc_attr( $wrapper_style ); ?>">
            <?php if ( empty( $url ) ) : ?>
                <div style="padding:40px 20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:<?php echo esc_attr( $radius ); ?>;">
                    <?php echo esc_html( function_exists( 'olo_t' ) ? olo_t( 'Inserisci un URL Instagram' ) : 'Inserisci un URL Instagram' ); ?>
                </div>
            <?php elseif ( $embed_type === 'profile' ) : ?>
                <?php
                // Extract username from URL
                $username = $this->extract_username( $url );
                if ( $username ) :
                    $embed_url = 'https://www.instagram.com/' . $username . '/embed/';
                    $w = $this->parse_width( $width );
                ?>
                    <iframe
                        src="<?php echo esc_url( $embed_url ); ?>"
                        style="width:<?php echo esc_attr( $w ); ?>;max-width:540px;min-height:500px;border:none;overflow:hidden;display:inline-block;"
                        frameborder="0"
                        scrolling="no"
                        allowtransparency="true"
                        loading="lazy"
                    ></iframe>
                <?php else : ?>
                    <div style="padding:40px 20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);">
                        <?php echo esc_html( function_exists( 'olo_t' ) ? olo_t( 'URL profilo Instagram non valido' ) : 'URL profilo Instagram non valido' ); ?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <?php
                // Post/Reel embed via oEmbed
                $oembed_html = wp_oembed_get( esc_url( $url ), [
                    'width'      => 540,
                    'hidecaption' => $show_caption ? 'false' : 'true',
                ] );

                if ( $oembed_html ) :
                    echo $oembed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- embed HTML returned by WordPress core wp_oembed_get() (provider-whitelisted oEmbed)
                else :
                    // Fallback: direct blockquote embed
                    $w = $this->parse_width( $width );
                ?>
                    <blockquote
                        class="instagram-media"
                        data-instgrm-captioned="<?php echo $show_caption ? 'true' : 'false'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'true'/'false' literals ?>"
                        style="max-width:540px;width:<?php echo esc_attr( $w ); ?>;margin:0 auto;border:1px solid #dbdbdb;border-radius:<?php echo esc_attr( $radius ); ?>;background:#FFF;"
                    >
                        <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html( function_exists( 'olo_t' ) ? olo_t( 'Visualizza su Instagram' ) : 'Visualizza su Instagram' ); ?>
                        </a>
                    </blockquote>
                <?php endif; ?>
                <?php $this->enqueue_instagram_js(); ?>
            <?php endif; ?>
        </div>
        <?php
                // Border system
        $border_css        = $this->build_border_css( $s['border'] ?? [] );
        $border_hover_css  = $this->build_border_hover_css( ".{$ig_uid}", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );
        $border_effect_css = $this->build_border_effect_css( ".{$ig_uid}", $s['border'] ?? [], $s );
        if ( $border_css || $border_hover_css || $border_effect_css ) {
            echo '<style>';
            if ( $border_css ) echo ".{$ig_uid}{{$border_css}}"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_css() from sanitized settings; $ig_uid is internally generated
            echo $border_hover_css . $border_effect_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated by Olo_Tile_Base::build_border_hover_css()/build_border_effect_css() from sanitized settings
        }
        return ob_get_clean();
    }

    /**
     * Extract Instagram username from a URL.
     */
    private function extract_username( $url ) {
        if ( preg_match( '#instagram\.com/([a-zA-Z0-9_.]+)#', $url, $m ) ) {
            $user = $m[1];
            // Exclude known path segments
            $excluded = [ 'p', 'reel', 'stories', 'explore', 'accounts', 'about', 'developer', 'legal' ];
            if ( ! in_array( strtolower( $user ), $excluded, true ) ) {
                return sanitize_text_field( $user );
            }
        }
        return '';
    }

    /**
     * Parse width value — if numeric, append 'px'; otherwise return as-is.
     */
    private function parse_width( $width ) {
        $w = trim( $width );
        if ( is_numeric( $w ) ) {
            return $w . 'px';
        }
        return $w ?: '100%';
    }

    /**
     * Enqueue Instagram embed.js once per page via wp_enqueue_script.
     */
    private function enqueue_instagram_js() {
        wp_enqueue_script( 'instagram-embed', 'https://www.instagram.com/embed.js', [], null, true );
    }
}
