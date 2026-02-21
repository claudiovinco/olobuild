<?php
/**
 * Traduce TUTTI i meta delle copie baite EN/DE + aggiunge stringhe globali mancanti.
 * Copre: rules, directions, seasons, opening, valley, club, locality
 * + stringhe comuni olo-booking per l'output buffer.
 */
error_reporting(0);
require_once "/var/www/wordpress/wp-load.php";
global $wpdb;
$db = new Olo_Lang_Database();

// =============================================================================
// PARTE 1: Stringhe globali — frasi comuni che appaiono nell'HTML
// =============================================================================
echo "=== STRINGHE GLOBALI — FRASI COMUNI ===\n";
$common = [
    // --- Regole comuni ---
    ['it' => 'Check-in dalle 15:00, check-out entro le 10:00.', 'en' => 'Check-in from 3:00 PM, check-out by 10:00 AM.', 'de' => 'Check-in ab 15:00 Uhr, Check-out bis 10:00 Uhr.'],
    ['it' => 'Check-in dalle 14:00, check-out entro le 10:00.', 'en' => 'Check-in from 2:00 PM, check-out by 10:00 AM.', 'de' => 'Check-in ab 14:00 Uhr, Check-out bis 10:00 Uhr.'],
    ['it' => 'Check-in dalle 16:00, check-out entro le 10:00.', 'en' => 'Check-in from 4:00 PM, check-out by 10:00 AM.', 'de' => 'Check-in ab 16:00 Uhr, Check-out bis 10:00 Uhr.'],
    ['it' => "Vietato fumare all'interno.", 'en' => 'No smoking indoors.', 'de' => 'Rauchen im Gebäude verboten.'],
    ['it' => "Vietato fumare all'interno della struttura.", 'en' => 'No smoking inside the property.', 'de' => 'Rauchen in der Unterkunft verboten.'],
    ['it' => 'Vietato fumare in tutti gli ambienti interni.', 'en' => 'No smoking in all indoor areas.', 'de' => 'Rauchen in allen Innenräumen verboten.'],
    ['it' => 'Vietato fumare in tutti gli ambienti.', 'en' => 'No smoking in all areas.', 'de' => 'Rauchen in allen Bereichen verboten.'],
    ['it' => "Non fumare all'interno della struttura.", 'en' => 'Do not smoke inside the property.', 'de' => 'Bitte nicht in der Unterkunft rauchen.'],
    ['it' => 'Animali ammessi previo accordo.', 'en' => 'Pets allowed by prior arrangement.', 'de' => 'Haustiere nach Absprache erlaubt.'],
    ['it' => 'Animali non ammessi.', 'en' => 'No pets allowed.', 'de' => 'Keine Haustiere erlaubt.'],
    ['it' => 'Animali di piccola taglia ammessi su richiesta.', 'en' => 'Small pets allowed on request.', 'de' => 'Kleine Haustiere auf Anfrage erlaubt.'],
    ['it' => 'Animali ammessi (max 1 per appartamento, supplemento giornaliero).', 'en' => 'Pets allowed (max 1 per apartment, daily supplement).', 'de' => 'Haustiere erlaubt (max. 1 pro Wohnung, täglicher Zuschlag).'],
    ['it' => 'Animali ammessi solo di piccola taglia e su richiesta.', 'en' => 'Only small pets allowed, on request.', 'de' => 'Nur kleine Haustiere auf Anfrage erlaubt.'],
    ['it' => 'Animali di piccola taglia ammessi previo accordo.', 'en' => 'Small pets allowed by prior arrangement.', 'de' => 'Kleine Haustiere nach Absprache erlaubt.'],
    ['it' => 'Animali ammessi su richiesta (supplemento applicabile).', 'en' => 'Pets allowed on request (supplement may apply).', 'de' => 'Haustiere auf Anfrage erlaubt (Zuschlag möglich).'],
    ['it' => "Animali non ammessi per rispetto dell'ambiente naturale.", 'en' => 'No pets allowed, to protect the natural environment.', 'de' => 'Keine Haustiere erlaubt, zum Schutz der natürlichen Umgebung.'],
    ['it' => 'Animali ammessi previo accordo con la gestione.', 'en' => 'Pets allowed by prior arrangement with management.', 'de' => 'Haustiere nach Absprache mit der Verwaltung erlaubt.'],
    ['it' => 'Animali ammessi di piccola taglia.', 'en' => 'Small pets allowed.', 'de' => 'Kleine Haustiere erlaubt.'],
    ['it' => 'Silenzio dalle 22:00 alle 8:00.', 'en' => 'Quiet hours from 10:00 PM to 8:00 AM.', 'de' => 'Nachtruhe von 22:00 bis 8:00 Uhr.'],
    ['it' => 'Silenzio dalle 22:00 alle 7:30.', 'en' => 'Quiet hours from 10:00 PM to 7:30 AM.', 'de' => 'Nachtruhe von 22:00 bis 7:30 Uhr.'],
    ['it' => 'Biancheria da letto e asciugamani forniti.', 'en' => 'Bed linen and towels provided.', 'de' => 'Bettwäsche und Handtücher werden gestellt.'],
    ['it' => 'Lasciare la baita in ordine al momento della partenza.', 'en' => 'Please leave the chalet tidy upon departure.', 'de' => 'Bitte hinterlassen Sie die Hütte ordentlich bei der Abreise.'],
    ['it' => 'Rispettare la quiete del vicinato.', 'en' => 'Please respect the neighbours\' peace.', 'de' => 'Bitte die Nachbarschaftsruhe respektieren.'],
    ['it' => 'Smaltimento rifiuti secondo calendario comunale.', 'en' => 'Waste disposal according to local schedule.', 'de' => 'Müllentsorgung gemäß kommunalem Kalender.'],
    ['it' => 'Deposito sci disponibile al piano interrato.', 'en' => 'Ski storage available in the basement.', 'de' => 'Skiaufbewahrung im Untergeschoss verfügbar.'],
    ['it' => 'In caso di nevicata, liberare il proprio posto auto.', 'en' => 'In case of snowfall, clear your parking space.', 'de' => 'Bei Schneefall bitte den eigenen Parkplatz räumen.'],
    ['it' => 'Ski room disponibile al piano terra.', 'en' => 'Ski room available on the ground floor.', 'de' => 'Skiraum im Erdgeschoss verfügbar.'],
    ['it' => 'Non stendere biancheria sui balconi.', 'en' => 'Do not hang laundry on the balconies.', 'de' => 'Bitte keine Wäsche auf den Balkonen aufhängen.'],
    ['it' => 'Acqua calda disponibile con boiler: uso responsabile.', 'en' => 'Hot water available via boiler: please use responsibly.', 'de' => 'Warmwasser über Boiler verfügbar: bitte verantwortungsvoll nutzen.'],
    ['it' => 'Legna per la stufa fornita gratuitamente.', 'en' => 'Firewood for the stove provided free of charge.', 'de' => 'Brennholz für den Ofen wird kostenlos bereitgestellt.'],
    ['it' => 'Non abbandonare rifiuti lungo i sentieri.', 'en' => 'Do not leave rubbish along the trails.', 'de' => 'Bitte keinen Müll auf den Wanderwegen hinterlassen.'],
    ['it' => 'Obbligo di scarpe da trekking per i percorsi limitrofi.', 'en' => 'Hiking boots required for surrounding trails.', 'de' => 'Wanderschuhe für die umliegenden Wege erforderlich.'],
    ['it' => 'La biancheria viene cambiata ogni 3 giorni.', 'en' => 'Linen is changed every 3 days.', 'de' => 'Die Bettwäsche wird alle 3 Tage gewechselt.'],
    ['it' => 'Rispettare le norme antincendio.', 'en' => 'Please observe fire safety regulations.', 'de' => 'Bitte die Brandschutzvorschriften beachten.'],
    ['it' => 'Non lasciare cibo all\'esterno (zona con fauna selvatica).', 'en' => 'Do not leave food outside (wildlife area).', 'de' => 'Bitte keine Lebensmittel draußen lassen (Wildtiergebiet).'],
    ['it' => 'Raccolta differenziata obbligatoria.', 'en' => 'Waste sorting is mandatory.', 'de' => 'Mülltrennung ist Pflicht.'],
    ['it' => 'Portare sacchi per la spazzatura: non ci sono cassonetti in zona.', 'en' => 'Bring rubbish bags: there are no bins in the area.', 'de' => 'Bitte Müllsäcke mitbringen: es gibt keine Container in der Gegend.'],
    ['it' => 'La struttura non dispone di connessione Wi-Fi.', 'en' => 'The property does not have Wi-Fi.', 'de' => 'Die Unterkunft verfügt über kein WLAN.'],
    ['it' => 'La legna per il camino viene fornita gratuitamente.', 'en' => 'Firewood for the fireplace is provided free of charge.', 'de' => 'Brennholz für den Kamin wird kostenlos bereitgestellt.'],
    ['it' => 'Mantenere puliti gli spazi comuni.', 'en' => 'Please keep common areas clean.', 'de' => 'Bitte die Gemeinschaftsbereiche sauber halten.'],
    ['it' => 'La biancheria da letto e da bagno viene fornita.', 'en' => 'Bed and bath linen is provided.', 'de' => 'Bett- und Badwäsche wird gestellt.'],
    ['it' => 'Rispettare la raccolta differenziata.', 'en' => 'Please follow waste sorting rules.', 'de' => 'Bitte die Mülltrennung beachten.'],

    // --- Apertura ---
    ['it' => 'Apertura annuale', 'en' => 'Open all year', 'de' => 'Ganzjährig geöffnet'],
    ['it' => 'Apertura stagionale', 'en' => 'Seasonal opening', 'de' => 'Saisonöffnung'],

    // --- Stagioni ---
    ['it' => 'Bassa stagione', 'en' => 'Low season', 'de' => 'Nebensaison'],
    ['it' => 'Alta stagione', 'en' => 'High season', 'de' => 'Hochsaison'],
    ['it' => 'Tariffa base', 'en' => 'Base rate', 'de' => 'Grundpreis'],

    // --- Valli (nomi propri, ma utili per l'output buffer) ---
    ['it' => 'Val di Pejo', 'en' => 'Val di Pejo', 'de' => 'Val di Pejo'],
    ['it' => 'Val Rendena', 'en' => 'Val Rendena', 'de' => 'Val Rendena'],
    ['it' => 'Val di Non', 'en' => 'Val di Non', 'de' => 'Val di Non'],
    ['it' => 'Val di Sole', 'en' => 'Val di Sole', 'de' => 'Val di Sole'],
    ['it' => 'Val di Fiemme', 'en' => 'Val di Fiemme', 'de' => 'Val di Fiemme'],
    ['it' => 'Val di Fassa', 'en' => 'Val di Fassa', 'de' => 'Val di Fassa'],
    ['it' => 'Passo Rolle', 'en' => 'Passo Rolle', 'de' => 'Passo Rolle'],
    ['it' => 'Paganella', 'en' => 'Paganella', 'de' => 'Paganella'],
    ['it' => 'Lagorai', 'en' => 'Lagorai', 'de' => 'Lagorai'],
    ['it' => "Valle dell'Adige", 'en' => 'Adige Valley', 'de' => 'Etschtal'],

    // --- Label olo-booking frontend ---
    ['it' => 'Prenota il tuo soggiorno', 'en' => 'Book your stay', 'de' => 'Buchen Sie Ihren Aufenthalt'],
    ['it' => 'Servizio non trovato.', 'en' => 'Service not found.', 'de' => 'Dienst nicht gefunden.'],
    ['it' => 'Prenotazione non trovata.', 'en' => 'Booking not found.', 'de' => 'Buchung nicht gefunden.'],
    ['it' => 'Errore nella creazione.', 'en' => 'Error during creation.', 'de' => 'Fehler bei der Erstellung.'],
    ['it' => 'Stato mancante.', 'en' => 'Missing status.', 'de' => 'Status fehlt.'],
    ['it' => 'Non autorizzato.', 'en' => 'Unauthorized.', 'de' => 'Nicht autorisiert.'],
    ['it' => 'Descrizione breve', 'en' => 'Short description', 'de' => 'Kurzbeschreibung'],
    ['it' => 'Descrizione completa', 'en' => 'Full description', 'de' => 'Vollständige Beschreibung'],
    ['it' => 'Struttura e capienza', 'en' => 'Structure & capacity', 'de' => 'Struktur & Kapazität'],
    ['it' => 'Posti letto', 'en' => 'Beds', 'de' => 'Schlafplätze'],
    ['it' => 'Come arrivare', 'en' => 'How to get there', 'de' => 'Anfahrt'],
    ['it' => 'Nuova stagione', 'en' => 'New season', 'de' => 'Neue Saison'],
    ['it' => 'Rimuovi stagione', 'en' => 'Remove season', 'de' => 'Saison entfernen'],
    ['it' => 'Trascina per riordinare', 'en' => 'Drag to reorder', 'de' => 'Zum Sortieren ziehen'],
    ['it' => 'Categorie attive', 'en' => 'Active categories', 'de' => 'Aktive Kategorien'],
    ['it' => 'Nessun limite', 'en' => 'No limit', 'de' => 'Keine Begrenzung'],
    ['it' => 'Caratteristiche massime selezionabili', 'en' => 'Maximum selectable features', 'de' => 'Maximal auswählbare Merkmale'],
    ['it' => 'Vista lista', 'en' => 'List view', 'de' => 'Listenansicht'],
    ['it' => 'Vista calendario', 'en' => 'Calendar view', 'de' => 'Kalenderansicht'],
    ['it' => 'Tutte le strutture', 'en' => 'All properties', 'de' => 'Alle Unterkünfte'],
    ['it' => 'Mese precedente', 'en' => 'Previous month', 'de' => 'Vorheriger Monat'],
    ['it' => 'Mese successivo', 'en' => 'Next month', 'de' => 'Nächster Monat'],
    ['it' => 'Apri dettagli', 'en' => 'Open details', 'de' => 'Details öffnen'],
    ['it' => 'Arrivi oggi', 'en' => 'Arriving today', 'de' => 'Anreise heute'],
    ['it' => 'Dettagli soggiorno', 'en' => 'Stay details', 'de' => 'Aufenthaltsdetails'],
    ['it' => 'Dati ospite', 'en' => 'Guest details', 'de' => 'Gästedaten'],
    ['it' => 'Storico modifiche', 'en' => 'Change history', 'de' => 'Änderungsverlauf'],
    ['it' => 'Nome e cognome', 'en' => 'Full name', 'de' => 'Vollständiger Name'],
    ['it' => 'Note opzionali', 'en' => 'Optional notes', 'de' => 'Optionale Anmerkungen'],
    ['it' => 'Crea prenotazione', 'en' => 'Create booking', 'de' => 'Buchung erstellen'],
    ['it' => 'Nuova prenotazione', 'en' => 'New booking', 'de' => 'Neue Buchung'],
    ['it' => 'Modifica prenotazione', 'en' => 'Edit booking', 'de' => 'Buchung bearbeiten'],
    ['it' => 'Prenotazione aggiornata', 'en' => 'Booking updated', 'de' => 'Buchung aktualisiert'],
    ['it' => 'Prenotazione creata', 'en' => 'Booking created', 'de' => 'Buchung erstellt'],
    ['it' => 'Prenotazione eliminata', 'en' => 'Booking deleted', 'de' => 'Buchung gelöscht'],
    ['it' => 'Prenotazione spostata', 'en' => 'Booking moved', 'de' => 'Buchung verschoben'],
    ['it' => 'Nessuna prenotazione trovata.', 'en' => 'No bookings found.', 'de' => 'Keine Buchungen gefunden.'],
    ['it' => 'Eliminare questa prenotazione?', 'en' => 'Delete this booking?', 'de' => 'Diese Buchung löschen?'],
    ['it' => 'Salva servizi', 'en' => 'Save services', 'de' => 'Dienste speichern'],
    ['it' => 'Salva stagioni', 'en' => 'Save seasons', 'de' => 'Saisons speichern'],
    ['it' => 'Salva chiusure', 'en' => 'Save closures', 'de' => 'Schließungen speichern'],
    ['it' => 'Salva galleria', 'en' => 'Save gallery', 'de' => 'Galerie speichern'],
    ['it' => 'Le tue strutture', 'en' => 'Your properties', 'de' => 'Ihre Unterkünfte'],
    ['it' => 'Nuova struttura', 'en' => 'New property', 'de' => 'Neue Unterkunft'],
    ['it' => 'Crea struttura', 'en' => 'Create property', 'de' => 'Unterkunft erstellen'],
    ['it' => 'Colore calendario', 'en' => 'Calendar colour', 'de' => 'Kalenderfarbe'],
    ['it' => "Il nome della struttura è obbligatorio.", 'en' => 'Property name is required.', 'de' => 'Der Name der Unterkunft ist erforderlich.'],
    ['it' => 'Sito web', 'en' => 'Website', 'de' => 'Webseite'],
    ['it' => 'Note interne', 'en' => 'Internal notes', 'de' => 'Interne Anmerkungen'],
    ['it' => "Note visibili all'ospite", 'en' => 'Notes visible to guest', 'de' => 'Für den Gast sichtbare Anmerkungen'],
    ['it' => 'Solo per il gestore', 'en' => 'Manager only', 'de' => 'Nur für den Verwalter'],
    ['it' => 'Prezzo base se vuoto', 'en' => 'Base price if empty', 'de' => 'Grundpreis wenn leer'],
    ['it' => 'Min notti', 'en' => 'Min nights', 'de' => 'Min. Nächte'],
    ['it' => 'Servizio generico', 'en' => 'Generic service', 'de' => 'Allgemeiner Dienst'],
    ['it' => "Clicca sulla mappa per posizionare la struttura.", 'en' => 'Click on the map to position the property.', 'de' => 'Klicken Sie auf die Karte, um die Unterkunft zu positionieren.'],
    ['it' => 'Foto profilo', 'en' => 'Profile photo', 'de' => 'Profilfoto'],
    ['it' => 'Carica foto', 'en' => 'Upload photo', 'de' => 'Foto hochladen'],
    ['it' => 'Lingue parlate', 'en' => 'Languages spoken', 'de' => 'Gesprochene Sprachen'],
    ['it' => 'Profilo pubblico', 'en' => 'Public profile', 'de' => 'Öffentliches Profil'],
    ['it' => 'Nome visualizzato', 'en' => 'Display name', 'de' => 'Anzeigename'],
    ['it' => 'Seleziona foto per la galleria', 'en' => 'Select photos for the gallery', 'de' => 'Fotos für die Galerie auswählen'],
    ['it' => 'Aggiungi alla galleria', 'en' => 'Add to gallery', 'de' => 'Zur Galerie hinzufügen'],
    ['it' => 'Nessuna immagine trovata.', 'en' => 'No images found.', 'de' => 'Keine Bilder gefunden.'],
    ['it' => 'Risultato importazione', 'en' => 'Import result', 'de' => 'Importergebnis'],
    ['it' => "Il nome è obbligatorio", 'en' => 'Name is required', 'de' => 'Name ist erforderlich'],
    ['it' => 'Seleziona un servizio', 'en' => 'Select a service', 'de' => 'Wählen Sie einen Dienst'],
    ['it' => 'Errore del server', 'en' => 'Server error', 'de' => 'Serverfehler'],
    ['it' => 'Compila tutti i campi obbligatori.', 'en' => 'Please fill in all required fields.', 'de' => 'Bitte alle Pflichtfelder ausfüllen.'],
    ['it' => 'Compila nome ospite e date.', 'en' => 'Please fill in guest name and dates.', 'de' => 'Bitte Gastname und Daten ausfüllen.'],
    ['it' => 'Conferma eliminazione', 'en' => 'Confirm deletion', 'de' => 'Löschung bestätigen'],
    ['it' => "Questa azione non puo essere annullata.", 'en' => 'This action cannot be undone.', 'de' => 'Diese Aktion kann nicht rückgängig gemacht werden.'],
    ['it' => 'Libreria media', 'en' => 'Media library', 'de' => 'Medienbibliothek'],
    ['it' => 'Libreria video', 'en' => 'Video library', 'de' => 'Videobibliothek'],
    ['it' => 'Nessun video trovato.', 'en' => 'No videos found.', 'de' => 'Keine Videos gefunden.'],
    ['it' => 'Ripristina default', 'en' => 'Restore defaults', 'de' => 'Standardeinstellungen wiederherstellen'],
    ['it' => 'Salva tema', 'en' => 'Save theme', 'de' => 'Design speichern'],
    ['it' => 'Personalizza colori', 'en' => 'Customise colours', 'de' => 'Farben anpassen'],
    ['it' => 'Salva permessi', 'en' => 'Save permissions', 'de' => 'Berechtigungen speichern'],
    ['it' => 'Trentino Marketing', 'en' => 'Trentino Marketing', 'de' => 'Trentino Marketing'],
    ['it' => 'Family', 'en' => 'Family', 'de' => 'Familie'],
    ['it' => 'Vista montagna', 'en' => 'Mountain view', 'de' => 'Bergblick'],
];

$g_count = 0;
foreach ($common as $s) {
    foreach (['en', 'de'] as $lang) {
        $db->save_translation([
            'template_id' => 0,
            'tile_id'     => 'olo-booking',
            'field_path'  => md5($s['it']) . '_common',
            'lang'        => $lang,
            'original'    => $s['it'],
            'translation' => $s[$lang],
            'status'      => 'tradotto',
        ]);
        $g_count++;
    }
}
echo "  $g_count stringhe globali aggiunte\n";

// =============================================================================
// PARTE 2: Aggiorna meta delle copie EN/DE con regole e direzioni tradotte
// =============================================================================
echo "\n=== AGGIORNAMENTO META COPIE ===\n";

// Mappa traduzioni direzioni per baita
$directions_map = [
    323 => [ // Baita Alpina Marmolada
        'en' => '',
        'de' => '',
    ],
    324 => ['en' => "From Trento: SS43 towards Val di Non, exit at Ronzone and follow signs for Cercena (approx. 1h 10min).\nFrom Bolzano: A22 exit Mezzocorona, SS43 to Ronzone (approx. 1h).\nPrivate parking included in the rate.", 'de' => "Von Trient: SS43 Richtung Val di Non, Ausfahrt Ronzone und Beschilderung nach Cercena folgen (ca. 1 Std. 10 Min.).\nVon Bozen: A22 Ausfahrt Mezzocorona, SS43 bis Ronzone (ca. 1 Std.).\nPrivatparkplatz im Preis inbegriffen."],
    328 => ['en' => "From Trento: SS43 towards Val di Sole to Vermiglio, then continue towards Passo del Tonale (approx. 2h).\nFrom Brescia: SS42 Val Camonica to Ponte di Legno, then Passo del Tonale (approx. 2h 15min).\nFree parking adjacent to the property. Ski lifts 300 metres away.", 'de' => "Von Trient: SS43 Richtung Val di Sole bis Vermiglio, dann weiter Richtung Tonalepass (ca. 2 Std.).\nVon Brescia: SS42 Val Camonica bis Ponte di Legno, dann Tonalepass (ca. 2 Std. 15 Min.).\nKostenloser Parkplatz neben der Unterkunft. Skilifte 300 Meter entfernt."],
    333 => ['en' => "From Trento: SS43 towards Val di Sole to Vermiglio, then road to Passo del Tonale and turn off for Presanella (approx. 1h 45min).\nFrom Bolzano: A22 exit San Michele, SS43 to Vermiglio (approx. 2h).\nDirt road for the last 3 km. Vehicle with good ground clearance recommended.", 'de' => "Von Trient: SS43 Richtung Val di Sole bis Vermiglio, dann Straße zum Tonalepass und Abzweigung Presanella (ca. 1 Std. 45 Min.).\nVon Bozen: A22 Ausfahrt San Michele, SS43 bis Vermiglio (ca. 2 Std.).\nSchotterstraße auf den letzten 3 km. Fahrzeug mit guter Bodenfreiheit empfohlen."],
    339 => ['en' => "From Trento: SS43 towards Val di Sole, exit at Dimaro and follow signs for Loc. Vegaia (approx. 1h 15min).\nFrom Madonna di Campiglio: SP239 to Dimaro, then signs for Vegaia (approx. 30min).\nPrivate parking included.", 'de' => "Von Trient: SS43 Richtung Val di Sole, Ausfahrt Dimaro und Beschilderung Richtung Loc. Vegaia folgen (ca. 1 Std. 15 Min.).\nVon Madonna di Campiglio: SP239 bis Dimaro, dann Beschilderung nach Vegaia (ca. 30 Min.).\nPrivatparkplatz inklusive."],
    345 => ['en' => "From Trento: SS43 towards Val di Sole, turn off for Val di Rabbi at Male, continue to Loc. Malga Rossa (approx. 1h 40min).\nFrom Bolzano: A22 exit San Michele, then SS43 to Male and turn off for Rabbi (approx. 2h).\nLast 2 km on dirt road. Parking at the farmstead.", 'de' => "Von Trient: SS43 Richtung Val di Sole, Abzweigung Val di Rabbi bei Male, weiter bis Loc. Malga Rossa (ca. 1 Std. 40 Min.).\nVon Bozen: A22 Ausfahrt San Michele, SS43 bis Male und Abzweigung nach Rabbi (ca. 2 Std.).\nLetzte 2 km Schotterstraße. Parkplatz bei der Hütte."],
    350 => ['en' => "From Trento: SS43 to Dimaro, then follow signs for Folgarida (approx. 1h 20min).\nFrom Madonna di Campiglio: SP239 towards Dimaro, then climb to Folgarida (approx. 25min).\nCable car and ski lifts 500 metres away. Covered parking available.", 'de' => "Von Trient: SS43 bis Dimaro, dann Beschilderung nach Folgarida folgen (ca. 1 Std. 20 Min.).\nVon Madonna di Campiglio: SP239 Richtung Dimaro, dann Auffahrt nach Folgarida (ca. 25 Min.).\nSeilbahn und Skilifte 500 Meter entfernt. Überdachter Parkplatz verfügbar."],
    354 => ['en' => "From Trento: SS45bis to Tione, then SS239 to Madonna di Campiglio, follow signs for Vallesinella (approx. 1h 30min).\nFrom Bolzano: A22 exit San Michele, SS43 to Dimaro, then SP239 to Madonna di Campiglio (approx. 2h).\nFree parking 200 metres from the property.", 'de' => "Von Trient: SS45bis bis Tione, dann SS239 nach Madonna di Campiglio, Beschilderung Vallesinella (ca. 1 Std. 30 Min.).\nVon Bozen: A22 Ausfahrt San Michele, SS43 bis Dimaro, dann SP239 nach Madonna di Campiglio (ca. 2 Std.).\nKostenloser Parkplatz 200 Meter von der Unterkunft."],
    360 => ['en' => "From Trento: SS43 to Cles, then provincial road to Passo Peller (approx. 1h 20min).\nFrom Bolzano: A22 exit San Michele, SS43 to Cles, then signs for Peller (approx. 1h 30min).\nPaved road to the property. Free parking.", 'de' => "Von Trient: SS43 bis Cles, dann Provinzstraße zum Passo Peller (ca. 1 Std. 20 Min.).\nVon Bozen: A22 Ausfahrt San Michele, SS43 bis Cles, dann Beschilderung Peller (ca. 1 Std. 30 Min.).\nAsphaltierte Straße bis zur Unterkunft. Kostenloser Parkplatz."],
    366 => ['en' => "From Trento: SS43 towards Val di Non, exit at Ronzone and follow signs for Cercena (approx. 1h 10min).\nFrom Bolzano: A22 exit Mezzocorona, SS43 to Ronzone (approx. 1h).\nPrivate parking included in the rate.", 'de' => "Von Trient: SS43 Richtung Val di Non, Ausfahrt Ronzone, Beschilderung nach Cercena (ca. 1 Std. 10 Min.).\nVon Bozen: A22 Ausfahrt Mezzocorona, SS43 bis Ronzone (ca. 1 Std.).\nPrivatparkplatz im Preis inbegriffen."],
    371 => ['en' => "From Trento: take the SS43 towards Val di Sole, continue on SS42 to Cogolo, then follow signs for Pejo Fonti (approx. 1h 30min).\nFrom Bolzano: take the A22 to San Michele, then SS43 towards Val di Sole to Pejo Fonti (approx. 1h 45min).\nFree parking available at the property.", 'de' => "Von Trient: SS43 Richtung Val di Sole, weiter auf SS42 bis Cogolo, dann Beschilderung Pejo Fonti folgen (ca. 1 Std. 30 Min.).\nVon Bozen: A22 bis San Michele, dann SS43 Richtung Val di Sole bis Pejo Fonti (ca. 1 Std. 45 Min.).\nKostenloser Parkplatz bei der Unterkunft."],
];

// Per ogni baita originale, aggiorna i meta delle copie
$baite = get_posts(['post_type' => 'olo_service', 'numberposts' => -1, 'post_status' => 'publish']);
$updated = 0;
foreach ($baite as $b) {
    $lang_meta = get_post_meta($b->ID, '_olo_lang_lang', true);
    if ($lang_meta) continue; // è una copia, skip

    $translations = get_post_meta($b->ID, '_olo_lang_translations', true);
    if (!is_array($translations)) continue;

    $directions_it = get_post_meta($b->ID, '_olo_service_directions', true);

    foreach (['en', 'de'] as $lang) {
        $copy_id = $translations[$lang] ?? 0;
        if (!$copy_id) continue;

        // Aggiorna direzioni se disponibili
        if (isset($directions_map[$b->ID][$lang]) && $directions_map[$b->ID][$lang]) {
            update_post_meta($copy_id, '_olo_service_directions', $directions_map[$b->ID][$lang]);
            $updated++;
        }

        // Aggiorna opening
        $opening = get_post_meta($b->ID, '_olo_service_opening', true);
        if ($opening) {
            $opening_map = [
                'Apertura annuale' => ($lang === 'en' ? 'Open all year' : 'Ganzjährig geöffnet'),
                'Apertura stagionale' => ($lang === 'en' ? 'Seasonal opening' : 'Saisonöffnung'),
            ];
            $translated_opening = $opening_map[$opening] ?? $opening;
            update_post_meta($copy_id, '_olo_service_opening', $translated_opening);
        }

        // Aggiorna valley
        $valley = get_post_meta($b->ID, '_olo_service_valley', true);
        if ($valley && $lang === 'de') {
            $valley_map = ["Valle dell'Adige" => 'Etschtal'];
            if (isset($valley_map[$valley])) {
                update_post_meta($copy_id, '_olo_service_valley', $valley_map[$valley]);
            }
        }
    }
}
echo "  $updated meta aggiornati sulle copie\n";

// Riepilogo
$total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}olo_translations");
$global = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}olo_translations WHERE template_id = 0");
echo "\n=== RIEPILOGO ===\n";
echo "  Stringhe globali: $global\n";
echo "  Totale traduzioni: $total\n";
echo "FATTO!\n";
