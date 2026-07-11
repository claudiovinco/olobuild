<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Base condivisa della famiglia OLOX (replica pixel-perfect olotheme.com).
 * Enqueue degli asset comuni (olox.css + olox.js + Google Fonts Fraunces/Inter/
 * JetBrains Mono) e helper di render condivisi (wrapper .oloxp, rich text
 * limitato per i titoli con <em>, colore prodotto).
 */
abstract class Olobuild_Olox_Base_Tile extends Olobuild_Tile_Base {

    /** Colori prodotto OLOtheme (palette del design). */
    const OLOX_COLORS = [
        'olo'     => '#E8453D',
        'build'   => '#E8453D',
        'booking' => '#3D8BFF',
        'lang'    => '#E8409A',
        'tour'    => '#F5A623',
        'tutor'   => '#38C172',
        'secur'   => '#26B8E8',
    ];

    public function get_controls() { return []; }

    /** Enqueue asset condivisi della famiglia (idempotente per handle). */
    protected function olox_assets() {
        wp_enqueue_style(
            'olox-fonts',
            'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap',
            [],
            null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts URL versionato dal provider.
        );
        wp_enqueue_style( 'olox-css', OLOBUILD_URL . 'assets/css/olox.css', [], OLOBUILD_VERSION );
        wp_enqueue_script( 'olox-js', OLOBUILD_URL . 'assets/js/olox.js', [], OLOBUILD_VERSION, true );
    }

    /**
     * Rich text limitato per titoli/paragrafi del design (em/strong/b/i/br/span/code/a).
     * I contenuti arrivano dai settings del builder (utenti trusted con capability
     * di edit), il kses tiene comunque il markup nel perimetro del design.
     */
    protected function olox_rich( $text ) {
        return wp_kses( (string) $text, [
            'em'     => [ 'style' => true, 'class' => true ],
            'strong' => [],
            'b'      => [ 'class' => true ],
            'i'      => [ 'class' => true, 'style' => true ],
            'br'     => [],
            'code'   => [],
            'span'   => [ 'class' => true, 'style' => true ],
            'a'      => [ 'href' => true, 'target' => true, 'rel' => true, 'class' => true, 'style' => true ],
        ] );
    }

    /** Colore prodotto: chiave palette (build/booking/…) o colore custom. */
    protected function olox_color( $key ) {
        $key = (string) $key;
        if ( isset( self::OLOX_COLORS[ $key ] ) ) {
            return 'var(--' . $key . ')';
        }
        $safe = $this->safe_color_css( $key );
        return $safe ? $safe : 'var(--olo)';
    }

    /** Apre il wrapper famiglia: .oloxp (+ .oloxp-live nel frontend reale). */
    protected function olox_open( $extra = '', $style = '' ) {
        $cls = 'oloxp oloxp-live' . ( $extra ? ' ' . $extra : '' );
        $st  = $style ? ' style="' . esc_attr( $style ) . '"' : '';
        return '<div class="' . esc_attr( $cls ) . '"' . $st . '>';
    }

    protected function olox_close() {
        return '</div>';
    }

    /** Items array-safe. */
    protected function olox_items( $s, $key ) {
        $items = $s[ $key ] ?? [];
        return is_array( $items ) ? $items : [];
    }

    /** Default lingue del sito (IT attiva; le altre si collegano con OLOlang). */
    public static function olox_default_langs() {
        return [
            [ 'code' => 'IT', 'url' => '/', 'active' => true ],
            [ 'code' => 'EN', 'url' => '#', 'active' => false ],
            [ 'code' => 'FR', 'url' => '#', 'active' => false ],
            [ 'code' => 'DE', 'url' => '#', 'active' => false ],
            [ 'code' => 'ES', 'url' => '#', 'active' => false ],
        ];
    }

    /** Markup lang switcher (lista inline desktop + tendina mobile), design .langsw. */
    protected function olox_langsw( $langs ) {
        $langs = is_array( $langs ) && $langs ? $langs : self::olox_default_langs();
        $cur   = 'IT';
        foreach ( $langs as $l ) {
            if ( ! empty( $l['active'] ) ) { $cur = (string) ( $l['code'] ?? 'IT' ); }
        }
        $out = '<div class="langsw" data-olox="langsw"><button class="lsw-t" type="button">' . esc_html( $cur ) . ' ▾</button><div class="lsw-list">';
        foreach ( $langs as $l ) {
            $on   = ! empty( $l['active'] ) ? ' class="on"' : '';
            $out .= '<a' . $on . ' href="' . esc_url( $l['url'] ?? '#' ) . '">' . esc_html( $l['code'] ?? '' ) . '</a>';
        }
        return $out . '</div></div>';
    }
}
