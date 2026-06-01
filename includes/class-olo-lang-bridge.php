<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Bridge OLObuild ↔ OLOlang.
 *
 * Le pagine costruite con OLObuild non hanno il contenuto in `post_content` (vuoto):
 * il contenuto vive in un TEMPLATE olo collegato via post meta `_olo_template_id`.
 * Quando OLOlang crea la traduzione di una pagina (clona il post e lancia l'action
 * `olo_lang_post_duplicated`), il meta `_olo_template_id` NON viene propagato → la
 * pagina tradotta risulta vuota.
 *
 * Questo bridge clona il template olo dell'originale e lo assegna alla copia tradotta:
 * così la versione tradotta nasce con lo stesso contenuto, ed è editabile/traducibile
 * indipendentemente nel builder (un template per lingua).
 *
 * Registrazione automatica: l'hook scatta solo se OLOlang è attivo e lancia l'azione,
 * altrimenti nessun side effect.
 */
class Olo_Lang_Bridge {

    public static function init() {
        add_action( 'olo_lang_post_duplicated', [ __CLASS__, 'on_post_duplicated' ], 10, 3 );
    }

    /**
     * @param int    $post_id ID del post ORIGINALE
     * @param int    $copy_id ID della COPIA tradotta appena creata da OLOlang
     * @param string $lang    Codice lingua della traduzione (es. "en", "de")
     */
    public static function on_post_duplicated( $post_id, $copy_id, $lang ) {
        $src_tpl = (int) get_post_meta( $post_id, '_olo_template_id', true );
        if ( ! $src_tpl ) {
            return; // la pagina non usa un template OLObuild → niente da fare
        }
        // Evita doppioni se l'hook scatta più volte o se il meta è già stato copiato.
        if ( get_post_meta( $copy_id, '_olo_template_id', true ) ) {
            return;
        }
        if ( ! class_exists( 'Olo_Database' ) ) {
            return;
        }

        $db  = new Olo_Database();
        $src = $db->get_template( $src_tpl );
        if ( ! $src ) {
            return;
        }

        $lang_up = strtoupper( (string) $lang );
        $new_id  = $db->create_template( [
            'title'    => ( $src['title'] ?? 'Template' ) . ' [' . $lang_up . ']',
            'type'     => $src['type'] ?? 'page',
            'content'  => ( isset( $src['content'] ) && is_array( $src['content'] ) ) ? $src['content'] : [],
            'settings' => ! empty( $src['settings'] ) ? $src['settings'] : [],
            'status'   => $src['status'] ?? 'publish',
        ] );

        if ( $new_id ) {
            update_post_meta( $copy_id, '_olo_template_id', $new_id );
        }
    }
}
