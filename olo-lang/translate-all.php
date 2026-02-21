<?php
/**
 * Script di traduzione massiva IT → EN + DE per tutti i template Olobuild.
 * Eseguire con: wp eval-file translate-all.php --path=/var/www/wordpress --allow-root
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$lang_db = new Olo_Lang_Database();
$olo_db  = new Olo_Database();
$list    = $olo_db->list_templates( [ 'per_page' => 100 ] );

// Mappa traduzioni: originale → [ 'en' => ..., 'de' => ... ]
$translations = [

    // =====================================================================
    // Template #37 — Baita Singola
    // =====================================================================
    'Codice CIPAT'                          => [ 'en' => 'CIPAT Code', 'de' => 'CIPAT-Code' ],
    'Classificazione comfort'               => [ 'en' => 'Comfort Rating', 'de' => 'Komfortklassifizierung' ],
    'Servizi e comfort'                     => [ 'en' => 'Services & Amenities', 'de' => 'Service & Komfort' ],
    'Regole della struttura'                => [ 'en' => 'House Rules', 'de' => 'Hausregeln' ],
    'Apri in Google Maps'                   => [ 'en' => 'Open in Google Maps', 'de' => 'In Google Maps öffnen' ],
    'La struttura'                          => [ 'en' => 'The Property', 'de' => 'Die Unterkunft' ],
    'Come arrivare'                         => [ 'en' => 'How to Get Here', 'de' => 'Anfahrt' ],
    'Scopri'                                => [ 'en' => 'Discover', 'de' => 'Entdecken' ],
    'Altre strutture'                       => [ 'en' => 'Other Properties', 'de' => 'Weitere Unterkünfte' ],
    '<p>Per prenotare il tuo soggiorno</p>' => [ 'en' => '<p>To book your stay</p>', 'de' => '<p>So buchen Sie Ihren Aufenthalt</p>' ],

    // Istruzioni prenotazione (contenuto lungo)
    '<ol><li><p>Scorri la pagina della baita fino alla sezione "Prenota il tuo soggiorno"</p></li><li><p>Nel calendario, i giorni in colore sono disponibili, quelli grigi sono occupati</p></li><li><p>Clicca sul giorno di arrivo e poi sul giorno di partenza</p></li><li><p>Seleziona il numero di ospiti</p></li><li><p>Il prezzo totale si calcola automaticamente in base alle notti selezionate</p></li><li><p>Clicca "Prenota ora" per confermare e riceverai un\'email con il riepilogo della tua prenotazione</p></li></ol><p></p>'
        => [
            'en' => '<ol><li><p>Scroll down the cabin page to the "Book Your Stay" section</p></li><li><p>On the calendar, colored days are available, grey days are booked</p></li><li><p>Click the arrival date and then the departure date</p></li><li><p>Select the number of guests</p></li><li><p>The total price is calculated automatically based on the selected nights</p></li><li><p>Click "Book Now" to confirm and you will receive an email with your booking summary</p></li></ol><p></p>',
            'de' => '<ol><li><p>Scrollen Sie auf der Hüttenseite bis zum Abschnitt "Buchen Sie Ihren Aufenthalt"</p></li><li><p>Im Kalender sind farbige Tage verfügbar, graue Tage sind belegt</p></li><li><p>Klicken Sie auf das Anreisedatum und dann auf das Abreisedatum</p></li><li><p>Wählen Sie die Anzahl der Gäste</p></li><li><p>Der Gesamtpreis wird automatisch anhand der ausgewählten Nächte berechnet</p></li><li><p>Klicken Sie auf "Jetzt buchen" zur Bestätigung und Sie erhalten eine E-Mail mit Ihrer Buchungsübersicht</p></li></ol><p></p>',
        ],

    // =====================================================================
    // Template #40 — Home Baite
    // =====================================================================
    'Le nostre Baite in Trentino'                               => [ 'en' => 'Our Cabins in Trentino', 'de' => 'Unsere Hütten im Trentino' ],
    'Scopri le nostre baite e chalet nelle valli del Trentino'  => [ 'en' => 'Discover our cabins and chalets in the Trentino valleys', 'de' => 'Entdecken Sie unsere Hütten und Chalets in den Tälern des Trentino' ],

    // =====================================================================
    // Template #26 — Sand (demo)
    // =====================================================================
    '<p>Lunga e diritta correva la strada, l\'auto veloce correva</p>'
        => [ 'en' => '<p>Long and straight the road ran, the fast car was speeding</p>', 'de' => '<p>Lang und gerade verlief die Straße, das schnelle Auto fuhr</p>' ],

    'Elemento 1'                   => [ 'en' => 'Element 1', 'de' => 'Element 1' ],
    'Elemento 2'                   => [ 'en' => 'Element 2', 'de' => 'Element 2' ],
    'Elemento 3'                   => [ 'en' => 'Element 3', 'de' => 'Element 3' ],
    'Event has started!'           => [ 'en' => 'Event has started!', 'de' => 'Die Veranstaltung hat begonnen!' ],
    'Accordion Item 1'             => [ 'en' => 'Accordion Item 1', 'de' => 'Akkordeon Element 1' ],
    'Content for the first accordion item.'  => [ 'en' => 'Content for the first accordion item.', 'de' => 'Inhalt des ersten Akkordeon-Elements.' ],
    'Accordion Item 2'             => [ 'en' => 'Accordion Item 2', 'de' => 'Akkordeon Element 2' ],
    'Content for the second accordion item.' => [ 'en' => 'Content for the second accordion item.', 'de' => 'Inhalt des zweiten Akkordeon-Elements.' ],
    'Accordion Item 3'             => [ 'en' => 'Accordion Item 3', 'de' => 'Akkordeon Element 3' ],
    'Content for the third accordion item.'  => [ 'en' => 'Content for the third accordion item.', 'de' => 'Inhalt des dritten Akkordeon-Elements.' ],
    'Nuovo termine'                => [ 'en' => 'New term', 'de' => 'Neuer Begriff' ],
    'APRI MAPPA'                   => [ 'en' => 'OPEN MAP', 'de' => 'KARTE ÖFFNEN' ],
    '<p>Contenuto del popup...</p>'          => [ 'en' => '<p>Popup content...</p>', 'de' => '<p>Popup-Inhalt...</p>' ],
    '<p>Contenuto del popup...</p><p></p>'   => [ 'en' => '<p>Popup content...</p><p></p>', 'de' => '<p>Popup-Inhalt...</p><p></p>' ],
    'Section Headline'             => [ 'en' => 'Section Headline', 'de' => 'Abschnittsüberschrift' ],
    'A brief subtitle goes here'   => [ 'en' => 'A brief subtitle goes here', 'de' => 'Hier steht ein kurzer Untertitel' ],
    'Section Title'                => [ 'en' => 'Section Title', 'de' => 'Abschnittstitel' ],
    'Add your content here. This is a simple text block that you can customize.'
        => [ 'en' => 'Add your content here. This is a simple text block that you can customize.', 'de' => 'Fügen Sie hier Ihren Inhalt ein. Dies ist ein einfacher Textblock, den Sie anpassen können.' ],
    'CLICCA QUI'                   => [ 'en' => 'CLICK HERE', 'de' => 'HIER KLICKEN' ],
    'ciao'                         => [ 'en' => 'hello', 'de' => 'hallo' ],
    'VISUALIZZA I COMMENTI'        => [ 'en' => 'SHOW COMMENTS', 'de' => 'KOMMENTARE ANZEIGEN' ],
    'NASCONDI I COMMENTI'          => [ 'en' => 'HIDE COMMENTS', 'de' => 'KOMMENTARE VERBERGEN' ],
    'VISUALIZZA I PREZZI'          => [ 'en' => 'SHOW PRICES', 'de' => 'PREISE ANZEIGEN' ],
    'NASCONDI I PREZZI'            => [ 'en' => 'HIDE PRICES', 'de' => 'PREISE VERBERGEN' ],
    '<p>Questo team di Claudio e i suoi 5 amici programmatori AI è fantastico</p>'
        => [ 'en' => '<p>This team of Claudio and his 5 AI programmer friends is fantastic</p>', 'de' => '<p>Dieses Team von Claudio und seinen 5 KI-Programmiererfreunden ist fantastisch</p>' ],
    '<p>Sara</p>'                  => [ 'en' => '<p>Sara</p>', 'de' => '<p>Sara</p>' ],
    'CEO, Company'                 => [ 'en' => 'CEO, Company', 'de' => 'Geschäftsführer, Unternehmen' ],
    'Pro Plan'                     => [ 'en' => 'Pro Plan', 'de' => 'Pro-Tarif' ],
    '<p><span style="font-size: 20px;">/mese</span></p>'
        => [ 'en' => '<p><span style="font-size: 20px;">/month</span></p>', 'de' => '<p><span style="font-size: 20px;">/Monat</span></p>' ],
    "Griglia drag & drop libera\nPreview in tempo reale\n50+ tile personalizzabili\nLeggero e veloce\nTemplate header/footer/single"
        => [ 'en' => "Free drag & drop grid\nReal-time preview\n50+ customizable tiles\nLightweight and fast\nHeader/footer/single templates", 'de' => "Freies Drag & Drop Raster\nEchtzeitvorschau\n50+ anpassbare Kacheln\nLeicht und schnell\nHeader/Footer/Single Templates" ],
    '<p>COMPRO QUESTO</p>'         => [ 'en' => '<p>BUY THIS</p>', 'de' => '<p>JETZT KAUFEN</p>' ],
    '<p>Clienti Felici</p>'        => [ 'en' => '<p>Happy Clients</p>', 'de' => '<p>Zufriedene Kunden</p>' ],
    'Abbiamo '                     => [ 'en' => 'We have ', 'de' => 'Wir haben ' ],
    'Jane Smith'                   => [ 'en' => 'Jane Smith', 'de' => 'Jane Smith' ],
    'Lead Designer'                => [ 'en' => 'Lead Designer', 'de' => 'Leitende Designerin' ],
    'Passionate about creating beautiful user experiences.'
        => [ 'en' => 'Passionate about creating beautiful user experiences.', 'de' => 'Leidenschaftlich daran interessiert, schöne Benutzererfahrungen zu schaffen.' ],

    // Timeline
    'Primo evento'                 => [ 'en' => 'First event', 'de' => 'Erstes Ereignis' ],
    'Descrizione del primo evento.'  => [ 'en' => 'Description of the first event.', 'de' => 'Beschreibung des ersten Ereignisses.' ],
    'Secondo evento'               => [ 'en' => 'Second event', 'de' => 'Zweites Ereignis' ],
    'Descrizione del secondo evento.' => [ 'en' => 'Description of the second event.', 'de' => 'Beschreibung des zweiten Ereignisses.' ],
    'Terzo evento'                 => [ 'en' => 'Third event', 'de' => 'Drittes Ereignis' ],
    'Descrizione del terzo evento.'  => [ 'en' => 'Description of the third event.', 'de' => 'Beschreibung des dritten Ereignisses.' ],
    'Nuovo evento'                 => [ 'en' => 'New event', 'de' => 'Neues Ereignis' ],
    '<p>Quarto</p>'                => [ 'en' => '<p>Fourth</p>', 'de' => '<p>Viertes</p>' ],

    // FlipCard
    'Titolo fronte'                => [ 'en' => 'Front Title', 'de' => 'Vorderseite Titel' ],
    'Descrizione della card visibile.' => [ 'en' => 'Visible card description.', 'de' => 'Beschreibung der sichtbaren Karte.' ],
    'Titolo retro'                 => [ 'en' => 'Back Title', 'de' => 'Rückseite Titel' ],
    'Contenuto retro con dettagli.' => [ 'en' => 'Back content with details.', 'de' => 'Rückseiteninhalt mit Details.' ],
    'Scopri di più'                => [ 'en' => 'Learn more', 'de' => 'Mehr erfahren' ],

    // Popup
    '<p>PASSIONE POPUP</p>'        => [ 'en' => '<p>POPUP PASSION</p>', 'de' => '<p>POPUP LEIDENSCHAFT</p>' ],
    '<p><span style="font-size: 18px;">alcune impostazioni, non tutte</span></p>'
        => [ 'en' => '<p><span style="font-size: 18px;">some settings, not all</span></p>', 'de' => '<p><span style="font-size: 18px;">einige Einstellungen, nicht alle</span></p>' ],
    'Versione 1'                   => [ 'en' => 'Version 1', 'de' => 'Version 1' ],
    'Versione 2'                   => [ 'en' => 'Version 2', 'de' => 'Version 2' ],
    'Versione 3'                   => [ 'en' => 'Version 3', 'de' => 'Version 3' ],
    'Versione 4'                   => [ 'en' => 'Version 4', 'de' => 'Version 4' ],
    'Versione 5'                   => [ 'en' => 'Version 5', 'de' => 'Version 5' ],

    // Form
    'Nuovo messaggio dal sito - olobuild test'  => [ 'en' => 'New message from website - olobuild test', 'de' => 'Neue Nachricht von der Website - olobuild test' ],
    'Messaggio inviato con successo! Ti risponderemo al più presto.'
        => [ 'en' => 'Message sent successfully! We will get back to you as soon as possible.', 'de' => 'Nachricht erfolgreich gesendet! Wir werden uns so schnell wie möglich bei Ihnen melden.' ],
    'Si è verificato un errore. Riprova più tardi.'
        => [ 'en' => 'An error occurred. Please try again later.', 'de' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.' ],
    'Invia messaggio'              => [ 'en' => 'Send message', 'de' => 'Nachricht senden' ],
    'Accetto il trattamento dei dati personali secondo la <a href="/privacy-policy">Privacy Policy</a>'
        => [ 'en' => 'I accept the processing of personal data according to the <a href="/privacy-policy">Privacy Policy</a>', 'de' => 'Ich akzeptiere die Verarbeitung personenbezogener Daten gemäß der <a href="/privacy-policy">Datenschutzerklärung</a>' ],
    'Claudio Vinco'                => [ 'en' => 'Claudio Vinco', 'de' => 'Claudio Vinco' ],

    // =====================================================================
    // Template #35 — Calendario
    // =====================================================================
    'I Nostri Servizi'             => [ 'en' => 'Our Services', 'de' => 'Unsere Dienstleistungen' ],
    '<p style="text-align:center;color:#C7D2FE;font-size:18px;max-width:600px;margin:0 auto">Prenota il servizio che preferisci, scegli data e orario disponibili e conferma in pochi click.</p>'
        => [ 'en' => '<p style="text-align:center;color:#C7D2FE;font-size:18px;max-width:600px;margin:0 auto">Book the service you prefer, choose an available date and time, and confirm in just a few clicks.</p>',
             'de' => '<p style="text-align:center;color:#C7D2FE;font-size:18px;max-width:600px;margin:0 auto">Buchen Sie den gewünschten Service, wählen Sie ein verfügbares Datum und eine Uhrzeit und bestätigen Sie mit wenigen Klicks.</p>' ],
    'Esplora i servizi disponibili' => [ 'en' => 'Explore available services', 'de' => 'Verfügbare Dienstleistungen erkunden' ],
    'Prenota ora'                  => [ 'en' => 'Book now', 'de' => 'Jetzt buchen' ],
    'Calendario Eventi'            => [ 'en' => 'Events Calendar', 'de' => 'Veranstaltungskalender' ],
    'Prenota subito'               => [ 'en' => 'Book now', 'de' => 'Jetzt buchen' ],
    'Hai bisogno di aiuto?'        => [ 'en' => 'Need help?', 'de' => 'Brauchen Sie Hilfe?' ],
    '<p style="text-align:center;color:#4338CA;font-size:16px;max-width:500px;margin:0 auto 24px">Contattaci per qualsiasi domanda sui nostri servizi o per prenotazioni personalizzate.</p>'
        => [ 'en' => '<p style="text-align:center;color:#4338CA;font-size:16px;max-width:500px;margin:0 auto 24px">Contact us for any questions about our services or for custom bookings.</p>',
             'de' => '<p style="text-align:center;color:#4338CA;font-size:16px;max-width:500px;margin:0 auto 24px">Kontaktieren Sie uns bei Fragen zu unseren Dienstleistungen oder für individuelle Buchungen.</p>' ],
    'Contattaci'                   => [ 'en' => 'Contact us', 'de' => 'Kontaktieren Sie uns' ],

    // =====================================================================
    // Template #22 — Proprietà singole (già in inglese, serve solo DE)
    // =====================================================================
    'Photo Gallery'                => [ 'en' => 'Photo Gallery', 'de' => 'Fotogalerie' ],
    'Location'                     => [ 'en' => 'Location', 'de' => 'Standort' ],
    'Contact'                      => [ 'en' => 'Contact', 'de' => 'Kontakt' ],
    'Rating'                       => [ 'en' => 'Rating', 'de' => 'Bewertung' ],
    'Book Now'                     => [ 'en' => 'Book Now', 'de' => 'Jetzt buchen' ],
    'Bedrooms'                     => [ 'en' => 'Bedrooms', 'de' => 'Schlafzimmer' ],
    'Bathrooms'                    => [ 'en' => 'Bathrooms', 'de' => 'Badezimmer' ],
    'Guests'                       => [ 'en' => 'Guests', 'de' => 'Gäste' ],
    'Sqm'                          => [ 'en' => 'Sqm', 'de' => 'Qm' ],
    ' m²'                          => [ 'en' => ' m²', 'de' => ' m²' ],
    '<p>About This Property</p>'   => [ 'en' => '<p>About This Property</p>', 'de' => '<p>Über diese Unterkunft</p>' ],
    'Virtual Tour'                 => [ 'en' => 'Virtual Tour', 'de' => 'Virtuelle Tour' ],
    'Explore the property'         => [ 'en' => 'Explore the property', 'de' => 'Die Unterkunft erkunden' ],
    '<p>Pricing</p>'               => [ 'en' => '<p>Pricing</p>', 'de' => '<p>Preise</p>' ],
    '<p>Flexible rates for every stay</p>' => [ 'en' => '<p>Flexible rates for every stay</p>', 'de' => '<p>Flexible Preise für jeden Aufenthalt</p>' ],
    '€/night'                      => [ 'en' => '€/night', 'de' => '€/Nacht' ],
    "Min 1 night\nCleaning fee included\nSelf check-in"
        => [ 'en' => "Min 1 night\nCleaning fee included\nSelf check-in", 'de' => "Min. 1 Nacht\nReinigungsgebühr inklusive\nSelbst-Check-in" ],
    '€/week'                       => [ 'en' => '€/week', 'de' => '€/Woche' ],
    "7+ nights discount\nCleaning included\nFlexible cancellation"
        => [ 'en' => "7+ nights discount\nCleaning included\nFlexible cancellation", 'de' => "7+ Nächte Rabatt\nReinigung inklusive\nFlexible Stornierung" ],
    '€/month'                      => [ 'en' => '€/month', 'de' => '€/Monat' ],
    "30+ nights\nAll utilities included\nBest value"
        => [ 'en' => "30+ nights\nAll utilities included\nBest value", 'de' => "30+ Nächte\nAlle Nebenkosten inklusive\nBestes Preis-Leistungs-Verhältnis" ],
    'Specifications'               => [ 'en' => 'Specifications', 'de' => 'Spezifikationen' ],
    'Highlights'                   => [ 'en' => 'Highlights', 'de' => 'Highlights' ],
    'Amenities'                    => [ 'en' => 'Amenities', 'de' => 'Ausstattung' ],
    'Wi-Fi, Air Conditioning, Heating, Kitchen, Washing Machine, TV, Iron, Hair Dryer, Coffee Machine, Workspace'
        => [ 'en' => 'Wi-Fi, Air Conditioning, Heating, Kitchen, Washing Machine, TV, Iron, Hair Dryer, Coffee Machine, Workspace',
             'de' => 'WLAN, Klimaanlage, Heizung, Küche, Waschmaschine, TV, Bügeleisen, Haartrockner, Kaffeemaschine, Arbeitsplatz' ],
    'House Rules'                  => [ 'en' => 'House Rules', 'de' => 'Hausregeln' ],
    'No smoking, No pets, No parties, Quiet hours (22:00 - 08:00)'
        => [ 'en' => 'No smoking, No pets, No parties, Quiet hours (22:00 - 08:00)',
             'de' => 'Nichtraucher, Keine Haustiere, Keine Partys, Ruhezeiten (22:00 - 08:00)' ],
    'Check-in / Check-out'         => [ 'en' => 'Check-in / Check-out', 'de' => 'Check-in / Check-out' ],
    'Self check-in available. Check-in: 14:00, Check-out: 11:00. Late check-out upon request.'
        => [ 'en' => 'Self check-in available. Check-in: 14:00, Check-out: 11:00. Late check-out upon request.',
             'de' => 'Selbst-Check-in möglich. Check-in: 14:00, Check-out: 11:00. Später Check-out auf Anfrage.' ],
    'Cancellation Policy'          => [ 'en' => 'Cancellation Policy', 'de' => 'Stornierungsbedingungen' ],
    'Free cancellation up to 7 days before arrival. 50% refund for cancellations within 7 days.'
        => [ 'en' => 'Free cancellation up to 7 days before arrival. 50% refund for cancellations within 7 days.',
             'de' => 'Kostenlose Stornierung bis 7 Tage vor Anreise. 50% Rückerstattung bei Stornierung innerhalb von 7 Tagen.' ],
    'The Neighborhood'             => [ 'en' => 'The Neighborhood', 'de' => 'Die Nachbarschaft' ],
    'Discover what\'s around'      => [ 'en' => 'Discover what\'s around', 'de' => 'Entdecken Sie die Umgebung' ],
    'Availability'                 => [ 'en' => 'Availability', 'de' => 'Verfügbarkeit' ],
    'Updated daily'                => [ 'en' => 'Updated daily', 'de' => 'Täglich aktualisiert' ],
    'This property is currently available for booking. Contact us for exact dates and special requests.'
        => [ 'en' => 'This property is currently available for booking. Contact us for exact dates and special requests.',
             'de' => 'Diese Unterkunft ist derzeit zur Buchung verfügbar. Kontaktieren Sie uns für genaue Daten und Sonderwünsche.' ],
    'View on map'                  => [ 'en' => 'View on map', 'de' => 'Auf der Karte ansehen' ],
    'Guest Ratings'                => [ 'en' => 'Guest Ratings', 'de' => 'Gästebewertungen' ],
    'Based on recent reviews'      => [ 'en' => 'Based on recent reviews', 'de' => 'Basierend auf aktuellen Bewertungen' ],
    'Amazing stay! The apartment was exactly as described. Clean, well-equipped and in a perfect location. Would definitely come back!'
        => [ 'en' => 'Amazing stay! The apartment was exactly as described. Clean, well-equipped and in a perfect location. Would definitely come back!',
             'de' => 'Fantastischer Aufenthalt! Die Wohnung war genau wie beschrieben. Sauber, gut ausgestattet und in perfekter Lage. Würde definitiv wiederkommen!' ],
    'Marco R.'                     => [ 'en' => 'Marco R.', 'de' => 'Marco R.' ],
    'Guest — March 2025'           => [ 'en' => 'Guest — March 2025', 'de' => 'Gast — März 2025' ],
    'We put our heart into making this space feel like home. Every detail has been thought of to ensure your comfort during your stay.'
        => [ 'en' => 'We put our heart into making this space feel like home. Every detail has been thought of to ensure your comfort during your stay.',
             'de' => 'Wir haben unser Herz darin investiert, diesen Raum wie ein Zuhause zu gestalten. Jedes Detail wurde durchdacht, um Ihren Komfort während Ihres Aufenthalts zu gewährleisten.' ],
    'Your Host'                    => [ 'en' => 'Your Host', 'de' => 'Ihr Gastgeber' ],
    'Superhost'                    => [ 'en' => 'Superhost', 'de' => 'Superhost' ],
    'Passionate about hospitality. Always available to help make your stay unforgettable.'
        => [ 'en' => 'Passionate about hospitality. Always available to help make your stay unforgettable.',
             'de' => 'Leidenschaftlich für Gastfreundschaft. Immer verfügbar, um Ihren Aufenthalt unvergesslich zu machen.' ],
    'Living Area'                  => [ 'en' => 'Living Area', 'de' => 'Wohnbereich' ],
    'Bright and spacious'          => [ 'en' => 'Bright and spacious', 'de' => 'Hell und geräumig' ],
    'Kitchen'                      => [ 'en' => 'Kitchen', 'de' => 'Küche' ],
    'Fully equipped'               => [ 'en' => 'Fully equipped', 'de' => 'Voll ausgestattet' ],
    'Bedroom'                      => [ 'en' => 'Bedroom', 'de' => 'Schlafzimmer' ],
    'Comfortable and quiet'        => [ 'en' => 'Comfortable and quiet', 'de' => 'Komfortabel und ruhig' ],
    'Here to help'                 => [ 'en' => 'Here to help', 'de' => 'Hier um zu helfen' ],
    'Response Time'                => [ 'en' => 'Response Time', 'de' => 'Antwortzeit' ],
    'Your host typically responds within 1 hour. Feel free to reach out with any questions before or during your stay.'
        => [ 'en' => 'Your host typically responds within 1 hour. Feel free to reach out with any questions before or during your stay.',
             'de' => 'Ihr Gastgeber antwortet normalerweise innerhalb einer Stunde. Kontaktieren Sie uns gerne bei Fragen vor oder während Ihres Aufenthalts.' ],
    'Find us on the map'           => [ 'en' => 'Find us on the map', 'de' => 'Finden Sie uns auf der Karte' ],
    'Rome, Italy'                  => [ 'en' => 'Rome, Italy', 'de' => 'Rom, Italien' ],
    'Ready to Book?'               => [ 'en' => 'Ready to Book?', 'de' => 'Bereit zu buchen?' ],
    'Secure your dates now — limited availability'
        => [ 'en' => 'Secure your dates now — limited availability', 'de' => 'Sichern Sie sich jetzt Ihre Daten — begrenzte Verfügbarkeit' ],

    // =====================================================================
    // Template #24 — Homepage
    // =====================================================================
    'View Details'                 => [ 'en' => 'View Details', 'de' => 'Details ansehen' ],
    'Explore'                      => [ 'en' => 'Explore', 'de' => 'Erkunden' ],
    'View'                         => [ 'en' => 'View', 'de' => 'Ansehen' ],
    'Cielo'                        => [ 'en' => 'Sky', 'de' => 'Himmel' ],
    'Colorato'                     => [ 'en' => 'Colorful', 'de' => 'Bunt' ],
    'Angeli'                       => [ 'en' => 'Angels', 'de' => 'Engel' ],
    'Rilassati al sole'            => [ 'en' => 'Relax in the sun', 'de' => 'Entspannen in der Sonne' ],
    'Prima TAB'                    => [ 'en' => 'First TAB', 'de' => 'Erster TAB' ],
    '<p>Qui ci va del testo come sempre</p>'
        => [ 'en' => '<p>Here goes some text as always</p>', 'de' => '<p>Hier kommt wie immer etwas Text</p>' ],
    '<p>e in questo spazio ci andrebbe ancora più testo</p>'
        => [ 'en' => '<p>and in this space there would be even more text</p>', 'de' => '<p>und in diesem Bereich gäbe es noch mehr Text</p>' ],
    'LEGGI'                        => [ 'en' => 'READ', 'de' => 'LESEN' ],
    'Bar & Cocktails'              => [ 'en' => 'Bar & Cocktails', 'de' => 'Bar & Cocktails' ],
    'Ut enim ad minim veniam.'     => [ 'en' => 'Ut enim ad minim veniam.', 'de' => 'Ut enim ad minim veniam.' ],
    'altra cosa'                   => [ 'en' => 'something else', 'de' => 'anderes' ],
    'Nuovo pannello'               => [ 'en' => 'New panel', 'de' => 'Neues Panel' ],
    'Contenuto del pannello.'      => [ 'en' => 'Panel content.', 'de' => 'Panel-Inhalt.' ],

    // =====================================================================
    // Template #31 — Roby
    // =====================================================================
    '<p>Ciao</p>'                  => [ 'en' => '<p>Hello</p>', 'de' => '<p>Hallo</p>' ],
    'Welcome to Our Site'          => [ 'en' => 'Welcome to Our Site', 'de' => 'Willkommen auf unserer Seite' ],
    'Discover something amazing'   => [ 'en' => 'Discover something amazing', 'de' => 'Entdecken Sie etwas Erstaunliches' ],
    'This is an amazing product! Highly recommended.'
        => [ 'en' => 'This is an amazing product! Highly recommended.', 'de' => 'Dies ist ein erstaunliches Produkt! Sehr empfehlenswert.' ],
    'John Doe'                     => [ 'en' => 'John Doe', 'de' => 'John Doe' ],
    'Piano Pro'                    => [ 'en' => 'Pro Plan', 'de' => 'Pro-Tarif' ],
    '/mese'                        => [ 'en' => '/month', 'de' => '/Monat' ],
    "Progetti illimitati\n10 GB di spazio\nSupporto prioritario\nDominio personalizzato"
        => [ 'en' => "Unlimited projects\n10 GB storage\nPriority support\nCustom domain", 'de' => "Unbegrenzte Projekte\n10 GB Speicher\nPriorität-Support\nBenutzerdefinierte Domain" ],
    'Inizia ora'                   => [ 'en' => 'Get started', 'de' => 'Jetzt starten' ],
    'Slide One'                    => [ 'en' => 'Slide One', 'de' => 'Folie Eins' ],
    'First slide description'      => [ 'en' => 'First slide description', 'de' => 'Beschreibung der ersten Folie' ],
    'Slide Two'                    => [ 'en' => 'Slide Two', 'de' => 'Folie Zwei' ],
    'Second slide description'     => [ 'en' => 'Second slide description', 'de' => 'Beschreibung der zweiten Folie' ],
    'Slide Three'                  => [ 'en' => 'Slide Three', 'de' => 'Folie Drei' ],
    'Third slide description'      => [ 'en' => 'Third slide description', 'de' => 'Beschreibung der dritten Folie' ],
    'Project Title'                => [ 'en' => 'Project Title', 'de' => 'Projekttitel' ],
    'A brief description of this project.'
        => [ 'en' => 'A brief description of this project.', 'de' => 'Eine kurze Beschreibung dieses Projekts.' ],
    'Roma, Italia'                 => [ 'en' => 'Rome, Italy', 'de' => 'Rom, Italien' ],

    // =====================================================================
    // Template #27 — Overlay Slider
    // =====================================================================
    'Slide 1'                      => [ 'en' => 'Slide 1', 'de' => 'Folie 1' ],
    'Slide 2'                      => [ 'en' => 'Slide 2', 'de' => 'Folie 2' ],
];

// Valori da SALTARE (falsi positivi: colori rgba, coordinate, numeri puri)
$skip_patterns = [
    '/^rgba\(/',
    '/^\d+\.\d+,\s*\d+\.\d+$/',  // coordinate
    '/^\d+$/',                     // numeri puri (prezzi)
    '/^\+$/',                      // suffisso +
    '/^<p><\/p>$/',                // paragrafi vuoti
];

$total_saved = 0;
$total_skipped = 0;

foreach ( $list['items'] as $template ) {
    $strings = Olo_Lang_Scanner::scan_template( $template['content'] );
    if ( empty( $strings ) ) continue;

    $tpl_saved = 0;

    foreach ( $strings as $s ) {
        $val = $s['value'];

        // Salta falsi positivi
        $skip = false;
        foreach ( $skip_patterns as $pat ) {
            if ( preg_match( $pat, $val ) ) {
                $skip = true;
                break;
            }
        }
        if ( $skip ) {
            $total_skipped++;
            continue;
        }

        // Cerca traduzione nella mappa
        if ( isset( $translations[ $val ] ) ) {
            $trans = $translations[ $val ];

            foreach ( [ 'en', 'de' ] as $lang ) {
                if ( ! isset( $trans[ $lang ] ) || $trans[ $lang ] === '' ) continue;

                $lang_db->save_translation( [
                    'template_id' => $template['id'],
                    'tile_id'     => $s['tile_id'],
                    'field_path'  => $s['field_path'],
                    'lang'        => $lang,
                    'original'    => $val,
                    'translation' => $trans[ $lang ],
                    'status'      => 'tradotto',
                ] );
                $tpl_saved++;
                $total_saved++;
            }
        }
    }

    if ( $tpl_saved > 0 ) {
        echo "Template #{$template['id']} [{$template['title']}]: {$tpl_saved} traduzioni salvate\n";
    }
}

echo "\n=== RISULTATO ===\n";
echo "Traduzioni salvate: {$total_saved}\n";
echo "Stringhe saltate (falsi positivi): {$total_skipped}\n";
echo "Fatto!\n";
