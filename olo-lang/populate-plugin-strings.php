<?php
/**
 * Popola wp_olo_translations con le stringhe globali dei plugin (template_id=0).
 * Per ogni stringa catalogata in Olo_Lang_Plugin_Scanner, inserisce:
 *   - record EN con traduzione inglese
 *   - record DE con traduzione tedesca
 *
 * Eseguire con: wp eval-file populate-plugin-strings.php --path=/var/www/wordpress --allow-root
 * Sicuro da rieseguire: usa upsert (aggiorna se esiste).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$db = new Olo_Lang_Database();

// Stringhe olo-booking con traduzioni EN e DE
$translations = [

    // =====================================================================
    // PHP Frontend (class-frontend.php) — tipo html
    // =====================================================================
    [ 'it' => 'Caricamento servizi...', 'en' => 'Loading services...', 'de' => 'Dienste werden geladen...', 'ctx' => 'loading', 'type' => 'html' ],
    [ 'it' => 'Lun', 'en' => 'Mon', 'de' => 'Mo', 'ctx' => 'weekday_lun', 'type' => 'html' ],
    [ 'it' => 'Mar', 'en' => 'Tue', 'de' => 'Di', 'ctx' => 'weekday_mar', 'type' => 'html' ],
    [ 'it' => 'Mer', 'en' => 'Wed', 'de' => 'Mi', 'ctx' => 'weekday_mer', 'type' => 'html' ],
    [ 'it' => 'Gio', 'en' => 'Thu', 'de' => 'Do', 'ctx' => 'weekday_gio', 'type' => 'html' ],
    [ 'it' => 'Ven', 'en' => 'Fri', 'de' => 'Fr', 'ctx' => 'weekday_ven', 'type' => 'html' ],
    [ 'it' => 'Sab', 'en' => 'Sat', 'de' => 'Sa', 'ctx' => 'weekday_sab', 'type' => 'html' ],
    [ 'it' => 'Dom', 'en' => 'Sun', 'de' => 'So', 'ctx' => 'weekday_dom', 'type' => 'html' ],
    [ 'it' => 'Check-in:', 'en' => 'Check-in:', 'de' => 'Check-in:', 'ctx' => 'label_checkin', 'type' => 'html' ],
    [ 'it' => 'Check-out:', 'en' => 'Check-out:', 'de' => 'Check-out:', 'ctx' => 'label_checkout', 'type' => 'html' ],
    [ 'it' => 'Notti:', 'en' => 'Nights:', 'de' => 'Nächte:', 'ctx' => 'label_nights', 'type' => 'html' ],
    [ 'it' => 'Il tuo nome *', 'en' => 'Your name *', 'de' => 'Ihr Name *', 'ctx' => 'placeholder_name', 'type' => 'html' ],
    [ 'it' => 'La tua email *', 'en' => 'Your email *', 'de' => 'Ihre E-Mail *', 'ctx' => 'placeholder_email', 'type' => 'html' ],
    [ 'it' => 'Telefono (opzionale)', 'en' => 'Phone (optional)', 'de' => 'Telefon (optional)', 'ctx' => 'placeholder_phone', 'type' => 'html' ],
    [ 'it' => 'Numero ospiti', 'en' => 'Number of guests', 'de' => 'Anzahl der Gäste', 'ctx' => 'placeholder_guests', 'type' => 'html' ],
    [ 'it' => 'Note (opzionale)', 'en' => 'Notes (optional)', 'de' => 'Anmerkungen (optional)', 'ctx' => 'placeholder_notes', 'type' => 'html' ],
    [ 'it' => '← Indietro', 'en' => '← Back', 'de' => '← Zurück', 'ctx' => 'button_back', 'type' => 'html' ],
    [ 'it' => 'Conferma prenotazione', 'en' => 'Confirm booking', 'de' => 'Buchung bestätigen', 'ctx' => 'button_confirm', 'type' => 'html' ],
    [ 'it' => 'Prenotazione inviata!', 'en' => 'Booking submitted!', 'de' => 'Buchung gesendet!', 'ctx' => 'success_title', 'type' => 'html' ],
    [ 'it' => 'Riceverai una conferma via email.', 'en' => 'You will receive a confirmation email.', 'de' => 'Sie erhalten eine Bestätigungs-E-Mail.', 'ctx' => 'success_msg', 'type' => 'html' ],
    [ 'it' => 'Nuova prenotazione', 'en' => 'New booking', 'de' => 'Neue Buchung', 'ctx' => 'button_new', 'type' => 'html' ],

    // =====================================================================
    // JS (booking-front.js) — tipo js (tradotto via oloLangStrings)
    // =====================================================================
    [ 'it' => 'Gennaio', 'en' => 'January', 'de' => 'Januar', 'ctx' => 'month_01', 'type' => 'js' ],
    [ 'it' => 'Febbraio', 'en' => 'February', 'de' => 'Februar', 'ctx' => 'month_02', 'type' => 'js' ],
    [ 'it' => 'Marzo', 'en' => 'March', 'de' => 'März', 'ctx' => 'month_03', 'type' => 'js' ],
    [ 'it' => 'Aprile', 'en' => 'April', 'de' => 'April', 'ctx' => 'month_04', 'type' => 'js' ],
    [ 'it' => 'Maggio', 'en' => 'May', 'de' => 'Mai', 'ctx' => 'month_05', 'type' => 'js' ],
    [ 'it' => 'Giugno', 'en' => 'June', 'de' => 'Juni', 'ctx' => 'month_06', 'type' => 'js' ],
    [ 'it' => 'Luglio', 'en' => 'July', 'de' => 'Juli', 'ctx' => 'month_07', 'type' => 'js' ],
    [ 'it' => 'Agosto', 'en' => 'August', 'de' => 'August', 'ctx' => 'month_08', 'type' => 'js' ],
    [ 'it' => 'Settembre', 'en' => 'September', 'de' => 'September', 'ctx' => 'month_09', 'type' => 'js' ],
    [ 'it' => 'Ottobre', 'en' => 'October', 'de' => 'Oktober', 'ctx' => 'month_10', 'type' => 'js' ],
    [ 'it' => 'Novembre', 'en' => 'November', 'de' => 'November', 'ctx' => 'month_11', 'type' => 'js' ],
    [ 'it' => 'Dicembre', 'en' => 'December', 'de' => 'Dezember', 'ctx' => 'month_12', 'type' => 'js' ],
    [ 'it' => 'Nessun servizio disponibile.', 'en' => 'No services available.', 'de' => 'Keine Dienste verfügbar.', 'ctx' => 'error_no_services', 'type' => 'js' ],
    [ 'it' => '/notte', 'en' => '/night', 'de' => '/Nacht', 'ctx' => 'price_per_night', 'type' => 'js' ],
    [ 'it' => ' notte', 'en' => ' night', 'de' => ' Nacht', 'ctx' => 'night_singular', 'type' => 'js' ],
    [ 'it' => ' notti', 'en' => ' nights', 'de' => ' Nächte', 'ctx' => 'night_plural', 'type' => 'js' ],
    [ 'it' => 'Seleziona data uscita', 'en' => 'Select checkout date', 'de' => 'Abreisedatum wählen', 'ctx' => 'placeholder_checkout', 'type' => 'js' ],
    [ 'it' => 'Invio in corso...', 'en' => 'Submitting...', 'de' => 'Wird gesendet...', 'ctx' => 'loading_submit', 'type' => 'js' ],
    [ 'it' => 'Errore nella prenotazione', 'en' => 'Booking error', 'de' => 'Buchungsfehler', 'ctx' => 'error_booking', 'type' => 'js' ],

    // JS: nomi giorni usati in formatDateIt()
    [ 'it' => 'Dom', 'en' => 'Sun', 'de' => 'So', 'ctx' => 'js_weekday_dom', 'type' => 'js' ],
    [ 'it' => 'Lun', 'en' => 'Mon', 'de' => 'Mo', 'ctx' => 'js_weekday_lun', 'type' => 'js' ],
    [ 'it' => 'Mar', 'en' => 'Tue', 'de' => 'Di', 'ctx' => 'js_weekday_mar', 'type' => 'js' ],
    [ 'it' => 'Mer', 'en' => 'Wed', 'de' => 'Mi', 'ctx' => 'js_weekday_mer', 'type' => 'js' ],
    [ 'it' => 'Gio', 'en' => 'Thu', 'de' => 'Do', 'ctx' => 'js_weekday_gio', 'type' => 'js' ],
    [ 'it' => 'Ven', 'en' => 'Fri', 'de' => 'Fr', 'ctx' => 'js_weekday_ven', 'type' => 'js' ],
    [ 'it' => 'Sab', 'en' => 'Sat', 'de' => 'Sa', 'ctx' => 'js_weekday_sab', 'type' => 'js' ],

    // =====================================================================
    // Email (class-emails.php) — tipo email (tradotto via output buffer)
    // =====================================================================
    [ 'it' => 'Struttura', 'en' => 'Property', 'de' => 'Unterkunft', 'ctx' => 'email_struttura', 'type' => 'email' ],
    [ 'it' => 'Ospite', 'en' => 'Guest', 'de' => 'Gast', 'ctx' => 'email_ospite', 'type' => 'email' ],
    [ 'it' => 'Telefono', 'en' => 'Phone', 'de' => 'Telefon', 'ctx' => 'email_telefono', 'type' => 'email' ],
    [ 'it' => 'Ospiti', 'en' => 'Guests', 'de' => 'Gäste', 'ctx' => 'email_ospiti', 'type' => 'email' ],
    [ 'it' => 'Totale', 'en' => 'Total', 'de' => 'Gesamt', 'ctx' => 'email_totale', 'type' => 'email' ],
    [ 'it' => 'Stagione', 'en' => 'Season', 'de' => 'Saison', 'ctx' => 'email_stagione', 'type' => 'email' ],
    [ 'it' => 'Note', 'en' => 'Notes', 'de' => 'Anmerkungen', 'ctx' => 'email_note', 'type' => 'email' ],
    [ 'it' => 'Indirizzo', 'en' => 'Address', 'de' => 'Adresse', 'ctx' => 'email_indirizzo', 'type' => 'email' ],
    [ 'it' => 'Stato', 'en' => 'Status', 'de' => 'Status', 'ctx' => 'email_stato', 'type' => 'email' ],
    [ 'it' => 'Confermata', 'en' => 'Confirmed', 'de' => 'Bestätigt', 'ctx' => 'status_confermata', 'type' => 'email' ],
    [ 'it' => 'Annullata', 'en' => 'Cancelled', 'de' => 'Storniert', 'ctx' => 'status_annullata', 'type' => 'email' ],
    [ 'it' => 'In attesa', 'en' => 'Pending', 'de' => 'Ausstehend', 'ctx' => 'status_attesa', 'type' => 'email' ],
    [ 'it' => 'Completata', 'en' => 'Completed', 'de' => 'Abgeschlossen', 'ctx' => 'status_completata', 'type' => 'email' ],
    [ 'it' => 'La tua prenotazione è stata aggiornata', 'en' => 'Your booking has been updated', 'de' => 'Ihre Buchung wurde aktualisiert', 'ctx' => 'email_updated_heading', 'type' => 'email' ],
    [ 'it' => 'La tua prenotazione è stata confermata! Ti aspettiamo.', 'en' => 'Your booking has been confirmed! We look forward to seeing you.', 'de' => 'Ihre Buchung wurde bestätigt! Wir freuen uns auf Sie.', 'ctx' => 'email_confirmed_body', 'type' => 'email' ],
    [ 'it' => 'La tua prenotazione è stata annullata.', 'en' => 'Your booking has been cancelled.', 'de' => 'Ihre Buchung wurde storniert.', 'ctx' => 'email_cancelled_body', 'type' => 'email' ],
    [ 'it' => 'Nuova prenotazione ricevuta', 'en' => 'New booking received', 'de' => 'Neue Buchung eingegangen', 'ctx' => 'email_new_heading', 'type' => 'email' ],
    [ 'it' => 'Vai al pannello di gestione', 'en' => 'Go to management panel', 'de' => 'Zum Verwaltungspanel', 'ctx' => 'email_panel_link', 'type' => 'email' ],
    [ 'it' => 'Prenotazione ricevuta', 'en' => 'Booking received', 'de' => 'Buchung eingegangen', 'ctx' => 'email_received_heading', 'type' => 'email' ],
    [ 'it' => 'la tua prenotazione è stata ricevuta con successo.', 'en' => 'your booking has been successfully received.', 'de' => 'Ihre Buchung wurde erfolgreich empfangen.', 'ctx' => 'email_received_body', 'type' => 'email' ],
    [ 'it' => 'Riceverai una conferma definitiva a breve.', 'en' => 'You will receive a final confirmation shortly.', 'de' => 'Sie erhalten in Kürze eine endgültige Bestätigung.', 'ctx' => 'email_confirm_soon', 'type' => 'email' ],
    [ 'it' => 'Grazie,', 'en' => 'Thank you,', 'de' => 'Vielen Dank,', 'ctx' => 'email_closing', 'type' => 'email' ],
];

$inserted = 0;

foreach ( $translations as $t ) {
    foreach ( [ 'en', 'de' ] as $lang ) {
        $result = $db->save_translation( [
            'template_id' => 0,                       // globale (non legato a template)
            'tile_id'     => 'olo-booking',            // identificativo plugin
            'field_path'  => $t['type'] . '.' . $t['ctx'],  // es. "html.loading", "js.month_01"
            'lang'        => $lang,
            'original'    => $t['it'],
            'translation' => $t[ $lang ],
            'status'      => 'tradotto',
        ] );
        if ( $result ) {
            $inserted++;
        }
    }
}

echo "\n=== Populate Plugin Strings ===\n";
echo "Stringhe processate: " . count( $translations ) . "\n";
echo "Record inseriti/aggiornati: {$inserted}\n";
echo "Fatto!\n";
