<?php
/**
 * Olo Lang — Gestione database traduzioni.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Database {

    private function table_translations() {
        global $wpdb;
        return $wpdb->prefix . 'olo_translations';
    }

    public function create_tables() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_translations()} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            template_id BIGINT(20) UNSIGNED NOT NULL,
            tile_id VARCHAR(100) NOT NULL DEFAULT '',
            field_path VARCHAR(300) NOT NULL,
            lang VARCHAR(10) NOT NULL,
            original LONGTEXT NOT NULL,
            translation LONGTEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'bozza',
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uk_tile_field_lang (template_id, tile_id, field_path(191), lang),
            KEY idx_template_lang (template_id, lang),
            KEY idx_status (status)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Tutte le traduzioni di un template per una lingua.
     */
    public function get_template_translations( $template_id, $lang ) {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_translations()} WHERE template_id = %d AND lang = %s",
                $template_id,
                $lang
            ),
            ARRAY_A
        );
    }

    /**
     * Mappa traduzioni indicizzata per tile_id::field_path (lookup veloce nel rendering).
     */
    public function get_translation_map( $template_id, $lang ) {
        $rows = $this->get_template_translations( $template_id, $lang );
        $map  = [];
        foreach ( $rows as $row ) {
            $key         = $row['tile_id'] . '::' . $row['field_path'];
            $map[ $key ] = $row;
        }
        return $map;
    }

    /**
     * Salva o aggiorna una singola traduzione (upsert).
     */
    public function save_translation( $data ) {
        global $wpdb;

        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT id FROM {$this->table_translations()} WHERE template_id = %d AND tile_id = %s AND field_path = %s AND lang = %s",
            $data['template_id'],
            $data['tile_id'],
            $data['field_path'],
            $data['lang']
        ) );

        if ( $existing ) {
            return $wpdb->update(
                $this->table_translations(),
                [
                    'original'    => $data['original'],
                    'translation' => $data['translation'],
                    'status'      => $data['status'] ?? 'bozza',
                ],
                [ 'id' => $existing->id ],
                [ '%s', '%s', '%s' ],
                [ '%d' ]
            );
        }

        $wpdb->insert( $this->table_translations(), [
            'template_id' => absint( $data['template_id'] ),
            'tile_id'     => sanitize_text_field( $data['tile_id'] ),
            'field_path'  => sanitize_text_field( $data['field_path'] ),
            'lang'        => sanitize_text_field( $data['lang'] ),
            'original'    => $data['original'],
            'translation' => $data['translation'],
            'status'      => sanitize_text_field( $data['status'] ?? 'bozza' ),
        ], [ '%d', '%s', '%s', '%s', '%s', '%s', '%s' ] );

        return $wpdb->insert_id;
    }

    /**
     * Salvataggio massivo traduzioni per un template + lingua.
     */
    public function save_translations_bulk( $template_id, $lang, $translations ) {
        $saved = 0;
        foreach ( $translations as $t ) {
            $result = $this->save_translation( [
                'template_id' => $template_id,
                'tile_id'     => $t['tile_id'],
                'field_path'  => $t['field_path'],
                'lang'        => $lang,
                'original'    => $t['original'],
                'translation' => $t['translation'],
                'status'      => $t['status'] ?? 'tradotto',
            ] );
            if ( $result ) {
                $saved++;
            }
        }
        return $saved;
    }

    /**
     * Elimina traduzioni di un template (opzionalmente per lingua).
     */
    public function delete_template_translations( $template_id, $lang = '' ) {
        global $wpdb;
        if ( $lang ) {
            return $wpdb->delete( $this->table_translations(), [
                'template_id' => $template_id,
                'lang'        => $lang,
            ], [ '%d', '%s' ] );
        }
        return $wpdb->delete( $this->table_translations(), [
            'template_id' => $template_id,
        ], [ '%d' ] );
    }

    /**
     * Statistiche traduzioni per un template, raggruppate per lingua.
     */
    public function get_template_stats( $template_id ) {
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT lang,
                    COUNT(*) as totale,
                    SUM(CASE WHEN status = 'tradotto' THEN 1 ELSE 0 END) as tradotte,
                    SUM(CASE WHEN status = 'bozza' THEN 1 ELSE 0 END) as bozze,
                    SUM(CASE WHEN status = 'obsoleta' THEN 1 ELSE 0 END) as obsolete
             FROM {$this->table_translations()}
             WHERE template_id = %d
             GROUP BY lang",
            $template_id
        ), ARRAY_A );
    }

    /**
     * Statistiche globali per lingua.
     */
    public function get_global_stats() {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT lang,
                    COUNT(DISTINCT template_id) as templates,
                    COUNT(*) as stringhe_totali,
                    SUM(CASE WHEN status = 'tradotto' THEN 1 ELSE 0 END) as tradotte
             FROM {$this->table_translations()}
             GROUP BY lang",
            ARRAY_A
        );
    }

    // =========================================================================
    // Stringhe globali (template_id = 0)
    // =========================================================================

    /**
     * Tutte le stringhe globali per una lingua, opzionalmente filtrate per tile_id.
     */
    public function get_global_strings( $lang, $tile_id = '' ) {
        global $wpdb;

        if ( $tile_id ) {
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$this->table_translations()} WHERE template_id = 0 AND lang = %s AND tile_id = %s ORDER BY id ASC",
                $lang,
                $tile_id
            ), ARRAY_A );
        }

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table_translations()} WHERE template_id = 0 AND lang = %s ORDER BY tile_id, id ASC",
            $lang
        ), ARRAY_A );
    }

    /**
     * Statistiche stringhe globali: count per tile_id e lingua.
     */
    public function get_global_strings_stats() {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT tile_id, lang,
                    COUNT(*) as totale,
                    SUM(CASE WHEN status = 'tradotto' THEN 1 ELSE 0 END) as tradotte
             FROM {$this->table_translations()}
             WHERE template_id = 0
             GROUP BY tile_id, lang",
            ARRAY_A
        );
    }

    /**
     * Elimina singola stringa globale per ID.
     */
    public function delete_global_string( $id ) {
        global $wpdb;
        return $wpdb->delete( $this->table_translations(), [
            'id'          => absint( $id ),
            'template_id' => 0,
        ], [ '%d', '%d' ] );
    }

    /**
     * Lista distinta di tile_id per template_id=0.
     */
    public function get_global_tile_ids() {
        global $wpdb;
        return $wpdb->get_col(
            "SELECT DISTINCT tile_id FROM {$this->table_translations()} WHERE template_id = 0 ORDER BY tile_id"
        );
    }

    /**
     * Segna traduzioni come obsolete quando il contenuto originale cambia.
     */
    public function mark_outdated( $template_id, $tile_id, $field_path ) {
        global $wpdb;
        return $wpdb->update(
            $this->table_translations(),
            [ 'status' => 'obsoleta' ],
            [
                'template_id' => $template_id,
                'tile_id'     => $tile_id,
                'field_path'  => $field_path,
            ],
            [ '%s' ],
            [ '%d', '%s', '%s' ]
        );
    }
}
