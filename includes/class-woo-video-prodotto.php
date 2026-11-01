<?php
/**
 * Il video del prodotto: un campo nella scheda di WooCommerce («Video del prodotto»: un video della libreria o un link
 * YouTube o Vimeo, con la sua copertina facoltativa) che la Gallery Prodotto WC mette fra le foto, con il triangolo
 * sulla miniatura, e fa partire con l'audio e i comandi nella finestra che si apre (lightbox).
 *
 * Il dato è del prodotto (meta _olo_video, _olo_video_poster): ogni prodotto ha il suo video e la galleria della
 * scheda, che è un template dinamico, lo legge da sé. Il filtro olobuild_woo_video_prodotto lascia che un altro
 * plugin dei video prodotto dia il suo.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Woo_Video_Prodotto {

    const META        = '_olo_video';
    const META_POSTER = '_olo_video_poster';

    public static function init() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        add_action( 'woocommerce_product_options_general_product_data', [ __CLASS__, 'campo' ] );
        add_action( 'woocommerce_admin_process_product_object', [ __CLASS__, 'salva' ] );
    }

    /** I due campi nella scheda Generale dei dati del prodotto, ognuno col pulsante della libreria. */
    public static function campo() {
        global $post;
        if ( ! $post ) {
            return;
        }
        echo '<div class="options_group olo-video-prodotto">';
        woocommerce_wp_text_input( [
            'id'          => self::META,
            'label'       => olobuild_t( 'Video del prodotto' ),
            'placeholder' => 'https://',
            'desc_tip'    => true,
            'description' => olobuild_t( 'Un video della libreria (mp4) o un link YouTube o Vimeo. La Gallery Prodotto WC di OLObuild lo mette fra le foto e lo apre con l’audio nella finestra della galleria.' ),
            'value'       => (string) get_post_meta( $post->ID, self::META, true ),
        ] );
        echo '<p class="form-field"><label></label><button type="button" class="button olo-video-scegli" data-campo="' . esc_attr( self::META ) . '" data-tipo="video">'
            . esc_html( olobuild_t( 'Scegli dalla libreria' ) ) . '</button></p>';
        woocommerce_wp_text_input( [
            'id'          => self::META_POSTER,
            'label'       => olobuild_t( 'Copertina del video' ),
            'placeholder' => 'https://',
            'desc_tip'    => true,
            'description' => olobuild_t( 'L’immagine della miniatura del video. Vuoto = la foto principale del prodotto.' ),
            'value'       => (string) get_post_meta( $post->ID, self::META_POSTER, true ),
        ] );
        echo '<p class="form-field"><label></label><button type="button" class="button olo-video-scegli" data-campo="' . esc_attr( self::META_POSTER ) . '" data-tipo="image">'
            . esc_html( olobuild_t( 'Scegli dalla libreria' ) ) . '</button></p>';
        echo '</div>';
        ?>
        <script>
        jQuery(function($){
            if(!window.wp){return}
            if(!wp.media){return}
            $(document).on('click', '.olo-video-scegli', function(e){
                e.preventDefault();
                var b = $(this);
                var finestra = wp.media({ library: { type: b.data('tipo') }, multiple: false });
                finestra.on('select', function(){
                    var scelto = finestra.state().get('selection').first().toJSON();
                    $('#' + b.data('campo')).val(scelto.url).trigger('change');
                });
                finestra.open();
            });
        });
        </script>
        <?php
    }

    /**
     * Salva i due campi. Il nonce dei dati del prodotto l'ha già controllato WooCommerce prima di questa azione.
     *
     * @param WC_Product $product Il prodotto che si salva.
     */
    public static function salva( $product ) {
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- verificato da WooCommerce (woocommerce_meta_nonce) prima di woocommerce_admin_process_product_object
        foreach ( [ self::META, self::META_POSTER ] as $chiave ) {
            $valore = isset( $_POST[ $chiave ] ) ? esc_url_raw( trim( wp_unslash( (string) $_POST[ $chiave ] ) ) ) : '';
            if ( '' === $valore ) {
                $product->delete_meta_data( $chiave );
            } else {
                $product->update_meta_data( $chiave, $valore );
            }
        }
        // phpcs:enable WordPress.Security.NonceVerification.Missing
    }

    /**
     * Il video di un prodotto, pronto per la galleria: il file (src) o l'indirizzo da incorporare (embed), e la copertina.
     *
     * @param WC_Product $prod Il prodotto.
     * @return array|null [ 'src', 'embed', 'poster' ] o null se il prodotto non ha un video.
     */
    public static function del_prodotto( $prod ) {
        if ( ! is_a( $prod, 'WC_Product' ) ) {
            return null;
        }
        $url    = trim( (string) $prod->get_meta( self::META ) );
        $poster = trim( (string) $prod->get_meta( self::META_POSTER ) );
        $video  = null;
        if ( '' !== $url ) {
            $embed = self::embed( $url );
            $video = [ 'src' => $embed ? '' : $url, 'embed' => $embed, 'poster' => $poster ];
            if ( '' === $poster ) {
                $foto = $prod->get_image_id();
                $video['poster'] = $foto ? (string) wp_get_attachment_image_url( $foto, 'large' ) : '';
                if ( '' === $video['poster'] && preg_match( '~youtube\.com/embed/([\w-]+)~', $embed, $m ) ) {
                    $video['poster'] = 'https://i.ytimg.com/vi/' . $m[1] . '/hqdefault.jpg';
                }
            }
        }
        $video = apply_filters( 'olobuild_woo_video_prodotto', $video, $prod );
        if ( ! is_array( $video ) || ( empty( $video['src'] ) && empty( $video['embed'] ) ) ) {
            return null;
        }
        return [
            'src'    => esc_url_raw( (string) ( $video['src'] ?? '' ) ),
            'embed'  => esc_url_raw( (string) ( $video['embed'] ?? '' ) ),
            'poster' => esc_url_raw( (string) ( $video['poster'] ?? '' ) ),
        ];
    }

    /**
     * L'indirizzo da incorporare per un link YouTube o Vimeo (con l'avvio e l'audio), '' per un file.
     *
     * @param string $url Il link scritto nel campo.
     */
    public static function embed( $url ) {
        if ( preg_match( '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([\w-]{6,})~', $url, $m ) ) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0&playsinline=1';
        }
        if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $url, $m ) ) {
            return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1';
        }
        return '';
    }
}
