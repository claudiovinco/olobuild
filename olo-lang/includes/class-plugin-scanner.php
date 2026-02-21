<?php
/**
 * Olo Lang — Scanner per stringhe di plugin (PHP e JS).
 * Scansiona i file sorgente di un plugin e trova le stringhe
 * in italiano hardcoded, pronte per la traduzione.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Plugin_Scanner {

    /**
     * Catalogo stringhe note per plugin — organizzato per plugin slug.
     * Ogni entry ha: stringa originale (IT), contesto, tipo (html/js/email).
     */
    public static function get_known_strings() {
        return [
            'olo-booking' => self::get_olo_booking_strings(),
        ];
    }

    /**
     * Stringhe Olo Booking — PHP frontend + JS + Email.
     */
    private static function get_olo_booking_strings() {
        return [
            // --- PHP Frontend (class-frontend.php) ---
            [ 'string' => 'Caricamento servizi...', 'context' => 'loading', 'type' => 'html' ],
            [ 'string' => 'Lun', 'context' => 'weekday_short', 'type' => 'html' ],
            [ 'string' => 'Mar', 'context' => 'weekday_short', 'type' => 'html' ],
            [ 'string' => 'Mer', 'context' => 'weekday_short', 'type' => 'html' ],
            [ 'string' => 'Gio', 'context' => 'weekday_short', 'type' => 'html' ],
            [ 'string' => 'Ven', 'context' => 'weekday_short', 'type' => 'html' ],
            [ 'string' => 'Sab', 'context' => 'weekday_short', 'type' => 'html' ],
            [ 'string' => 'Dom', 'context' => 'weekday_short', 'type' => 'html' ],
            [ 'string' => 'Check-in:', 'context' => 'label', 'type' => 'html' ],
            [ 'string' => 'Check-out:', 'context' => 'label', 'type' => 'html' ],
            [ 'string' => 'Notti:', 'context' => 'label', 'type' => 'html' ],
            [ 'string' => 'Il tuo nome *', 'context' => 'placeholder', 'type' => 'html' ],
            [ 'string' => 'La tua email *', 'context' => 'placeholder', 'type' => 'html' ],
            [ 'string' => 'Telefono (opzionale)', 'context' => 'placeholder', 'type' => 'html' ],
            [ 'string' => 'Numero ospiti', 'context' => 'placeholder', 'type' => 'html' ],
            [ 'string' => 'Note (opzionale)', 'context' => 'placeholder', 'type' => 'html' ],
            [ 'string' => '← Indietro', 'context' => 'button', 'type' => 'html' ],
            [ 'string' => 'Conferma prenotazione', 'context' => 'button', 'type' => 'html' ],
            [ 'string' => 'Prenotazione inviata!', 'context' => 'success_title', 'type' => 'html' ],
            [ 'string' => 'Riceverai una conferma via email.', 'context' => 'success_msg', 'type' => 'html' ],
            [ 'string' => 'Nuova prenotazione', 'context' => 'button', 'type' => 'html' ],

            // --- JS (booking-front.js) ---
            [ 'string' => 'Gennaio', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Febbraio', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Marzo', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Aprile', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Maggio', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Giugno', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Luglio', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Agosto', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Settembre', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Ottobre', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Novembre', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Dicembre', 'context' => 'month', 'type' => 'js' ],
            [ 'string' => 'Nessun servizio disponibile.', 'context' => 'error', 'type' => 'js' ],
            [ 'string' => '/notte', 'context' => 'price_unit', 'type' => 'js' ],
            [ 'string' => ' notte', 'context' => 'night_singular', 'type' => 'js' ],
            [ 'string' => ' notti', 'context' => 'night_plural', 'type' => 'js' ],
            [ 'string' => 'Seleziona data uscita', 'context' => 'placeholder', 'type' => 'js' ],
            [ 'string' => 'Invio in corso...', 'context' => 'loading', 'type' => 'js' ],
            [ 'string' => 'Errore nella prenotazione', 'context' => 'error', 'type' => 'js' ],

            // --- Email (class-emails.php) ---
            [ 'string' => 'Struttura', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Ospite', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Telefono', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Ospiti', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Totale', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Stagione', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Note', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Indirizzo', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Stato', 'context' => 'email_label', 'type' => 'email' ],
            [ 'string' => 'Confermata', 'context' => 'status', 'type' => 'email' ],
            [ 'string' => 'Annullata', 'context' => 'status', 'type' => 'email' ],
            [ 'string' => 'In attesa', 'context' => 'status', 'type' => 'email' ],
            [ 'string' => 'Completata', 'context' => 'status', 'type' => 'email' ],
            [ 'string' => 'La tua prenotazione è stata aggiornata', 'context' => 'email_heading', 'type' => 'email' ],
            [ 'string' => 'La tua prenotazione è stata confermata! Ti aspettiamo.', 'context' => 'email_body', 'type' => 'email' ],
            [ 'string' => 'La tua prenotazione è stata annullata.', 'context' => 'email_body', 'type' => 'email' ],
            [ 'string' => 'Nuova prenotazione ricevuta', 'context' => 'email_heading', 'type' => 'email' ],
            [ 'string' => 'Vai al pannello di gestione', 'context' => 'email_link', 'type' => 'email' ],
            [ 'string' => 'Prenotazione ricevuta', 'context' => 'email_heading', 'type' => 'email' ],
            [ 'string' => 'la tua prenotazione è stata ricevuta con successo.', 'context' => 'email_body', 'type' => 'email' ],
            [ 'string' => 'Riceverai una conferma definitiva a breve.', 'context' => 'email_body', 'type' => 'email' ],
            [ 'string' => 'Grazie,', 'context' => 'email_closing', 'type' => 'email' ],

            // --- Giorni completi (class-price-calculator.php) ---
            [ 'string' => 'Lunedì', 'context' => 'weekday_full', 'type' => 'html' ],
            [ 'string' => 'Martedì', 'context' => 'weekday_full', 'type' => 'html' ],
            [ 'string' => 'Mercoledì', 'context' => 'weekday_full', 'type' => 'html' ],
            [ 'string' => 'Giovedì', 'context' => 'weekday_full', 'type' => 'html' ],
            [ 'string' => 'Venerdì', 'context' => 'weekday_full', 'type' => 'html' ],
            [ 'string' => 'Sabato', 'context' => 'weekday_full', 'type' => 'html' ],
            [ 'string' => 'Domenica', 'context' => 'weekday_full', 'type' => 'html' ],

            // --- Validazione (class-price-calculator.php) ---
            [ 'string' => 'La data di check-out deve essere successiva al check-in.', 'context' => 'validation', 'type' => 'html' ],

            // --- Amenities — Categorie ---
            [ 'string' => 'Generali', 'context' => 'amenity_cat', 'type' => 'html' ],
            [ 'string' => 'Cucina', 'context' => 'amenity_cat', 'type' => 'html' ],
            [ 'string' => 'Bagno e Lavanderia', 'context' => 'amenity_cat', 'type' => 'html' ],
            [ 'string' => 'Esterno', 'context' => 'amenity_cat', 'type' => 'html' ],
            [ 'string' => 'Sport e Attivita', 'context' => 'amenity_cat', 'type' => 'html' ],
            [ 'string' => 'Servizi extra', 'context' => 'amenity_cat', 'type' => 'html' ],

            // --- Amenities — Generali ---
            [ 'string' => 'Wi-Fi', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Riscaldamento', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Aria condizionata', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Camino / Stufa', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'TV', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Animali ammessi', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Fumatori', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Ascensore', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Accesso disabili', 'context' => 'amenity', 'type' => 'html' ],

            // --- Amenities — Cucina ---
            [ 'string' => 'Cucina attrezzata', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Forno', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Microonde', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Lavastoviglie', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Frigorifero', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Macchina caffe', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Bollitore', 'context' => 'amenity', 'type' => 'html' ],

            // --- Amenities — Bagno/Lavanderia ---
            [ 'string' => 'Lavatrice', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Asciugatrice', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Ferro da stiro', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Asciugacapelli', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Vasca da bagno', 'context' => 'amenity', 'type' => 'html' ],

            // --- Amenities — Esterno ---
            [ 'string' => 'Parcheggio', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Garage', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Giardino', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Terrazza / Balcone', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Barbecue', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Piscina', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Vasca idromassaggio', 'context' => 'amenity', 'type' => 'html' ],

            // --- Amenities — Sport ---
            [ 'string' => 'Vicino piste da sci', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Biciclette disponibili', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Area giochi bambini', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Sauna', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Sentieri escursionistici', 'context' => 'amenity', 'type' => 'html' ],

            // --- Amenities — Extra ---
            [ 'string' => 'Biancheria inclusa', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Asciugamani inclusi', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Pulizia finale inclusa', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Culla disponibile', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Seggiolone', 'context' => 'amenity', 'type' => 'html' ],
            [ 'string' => 'Cassaforte', 'context' => 'amenity', 'type' => 'html' ],
        ];
    }

    /**
     * Restituisce tutte le stringhe (di tutti i plugin) come array piatto.
     * Ogni entry ha: string, context, type, plugin.
     */
    public static function get_all_strings() {
        $all    = [];
        $known  = self::get_known_strings();

        foreach ( $known as $plugin => $strings ) {
            foreach ( $strings as $s ) {
                $s['plugin'] = $plugin;
                $all[]       = $s;
            }
        }

        return $all;
    }
}
