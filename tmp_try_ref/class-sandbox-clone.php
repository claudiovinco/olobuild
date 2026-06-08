<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Clona il template "master" in un nuovo template dedicato al SID del visitatore.
 * Il clone vive in `wp_olo_templates` come template normale, con titolo
 * `[SANDBOX abc12345] Demo Page`. Verrà cancellato al cleanup cron.
 */
class Olo_Sandbox_Clone {

    /**
     * Garantisce che esista un clone valido per il SID dato.
     * Se sessione esiste e template clone esiste → restituisce il suo ID.
     * Altrimenti: clona master, registra sessione, restituisce nuovo ID.
     *
     * @return int|null template_id del clone, o null se master non configurato.
     */
    public static function ensure_for_sid( $sid ) {
        // 1. Verifica master configurato
        $master_id = (int) get_option( Olo_Sandbox_Config::MASTER_OPTION, 0 );
        if ( ! $master_id ) {
            return null;
        }
        if ( ! class_exists( 'Olo_Database' ) ) {
            return null;
        }

        $db = Olo_Database::instance();
        $master = $db->get_template( $master_id );
        if ( ! $master ) {
            return null;
        }

        // 2. Se sessione esiste e clone esiste → riusa
        $existing = Olo_Sandbox_Session::fetch( $sid );
        if ( $existing ) {
            $clone = $db->get_template( (int) $existing['template_id'] );
            if ( $clone ) {
                return (int) $existing['template_id'];
            }
            // Clone fantasma: la sessione punta a un template eliminato → ricloniamo
        }

        // 3. Clone fresh
        $new_id = self::clone_template( $master, $sid );
        if ( ! $new_id ) {
            return null;
        }

        // 4. Registra sessione
        Olo_Sandbox_Session::save( $sid, $new_id );

        return $new_id;
    }

    /**
     * Ricrea il clone (reset esplicito dell'utente).
     * Cancella il clone esistente e ne crea uno nuovo dal master.
     */
    public static function reset_for_sid( $sid ) {
        // Drop esistente
        $existing = Olo_Sandbox_Session::fetch( $sid );
        if ( $existing && class_exists( 'Olo_Database' ) ) {
            $db = Olo_Database::instance();
            $db->delete_template( (int) $existing['template_id'] );
        }

        // Forza ricreazione
        return self::ensure_for_sid( $sid );
    }

    /**
     * Esegue il clone fisico in tabella `wp_olo_templates`.
     */
    private static function clone_template( $master, $sid ) {
        $db = Olo_Database::instance();

        $short_sid = substr( $sid, 0, 8 );
        $title = sprintf( '[SANDBOX %s] %s', $short_sid, $master['title'] );

        // `content` e `settings` possono già essere array (decodificati da Olo_Database::get_template)
        $content  = is_array( $master['content'] )  ? $master['content']  : json_decode( $master['content'], true );
        $settings = is_array( $master['settings'] ) ? $master['settings'] : json_decode( $master['settings'], true );

        $data = [
            'title'     => $title,
            'type'      => $master['type'] ?? 'page',
            'content'   => $content ?: [],
            'settings'  => $settings ?: [],
            'status'    => 'published',
            'author_id' => get_current_user_id() ?: 1,
        ];

        $new_id = $db->create_template( $data );
        return $new_id ?: null;
    }

    /**
     * Restituisce l'ID del clone associato al visitatore corrente.
     * Helper rapido per filtri di rendering.
     */
    public static function current_clone_id() {
        $session = Olo_Sandbox_Session::current();
        return $session ? (int) $session['template_id'] : null;
    }
}
