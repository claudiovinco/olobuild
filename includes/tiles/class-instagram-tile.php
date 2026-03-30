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
        $radius       = Olo_Tile_Utils::border_radius( $s['border_radius'] ?? 0 );
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

        ob_start();
        ?>
        <div class="olo-instagram" style="<?php echo esc_attr( $wrapper_style ); ?>">
            <?php if ( empty( $url ) ) : ?>
                <div style="padding:40px 20px;text-align:center;color:var(--olo-color-text-muted, #9CA3AF);background:var(--olo-color-muted, #F3F4F6);border-radius:<?php echo $radius; ?>;">
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
                    echo $oembed_html;
                else :
                    // Fallback: direct blockquote embed
                    $w = $this->parse_width( $width );
                ?>
                    <blockquote
                        class="instagram-media"
                        data-instgrm-captioned="<?php echo $show_caption ? 'true' : 'false'; ?>"
                        style="max-width:540px;width:<?php echo esc_attr( $w ); ?>;margin:0 auto;border:1px solid #dbdbdb;border-radius:<?php echo $radius; ?>;background:#FFF;"
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
