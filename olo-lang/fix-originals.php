<?php
/**
 * Fix originals: trasforma i contenuti inglesi dei template in italiano.
 * Per ogni stringa inglese trovata nel template:
 *   1. Sostituisce il valore nel JSON del template → italiano
 *   2. Aggiorna il campo 'original' nella tabella traduzioni → italiano
 *   3. Assicura che la traduzione EN = vecchio testo inglese
 *
 * Eseguire con: wp eval-file fix-originals.php --path=/var/www/wordpress --allow-root
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Mappa: inglese → italiano (per ogni stringa trovata nei template che è in EN)
$en_to_it = [

    // =====================================================================
    // Template #22 — Proprietà singole
    // =====================================================================
    'Photo Gallery'                => 'Galleria Fotografica',
    'Location'                     => 'Posizione',
    'Contact'                      => 'Contatto',
    'Rating'                       => 'Valutazione',
    'Book Now'                     => 'Prenota ora',
    'Bedrooms'                     => 'Camere da letto',
    'Bathrooms'                    => 'Bagni',
    'Guests'                       => 'Ospiti',
    'Sqm'                          => 'Mq',
    ' m²'                          => ' m²',
    '<p>About This Property</p>'   => '<p>Informazioni sulla proprietà</p>',
    'Virtual Tour'                 => 'Tour Virtuale',
    'Explore the property'         => 'Esplora la proprietà',
    '<p>Pricing</p>'               => '<p>Prezzi</p>',
    '<p>Flexible rates for every stay</p>' => '<p>Tariffe flessibili per ogni soggiorno</p>',
    '€/night'                      => '€/notte',
    "Min 1 night\nCleaning fee included\nSelf check-in"
        => "Min 1 notte\nPulizia inclusa\nSelf check-in",
    '€/week'                       => '€/settimana',
    "7+ nights discount\nCleaning included\nFlexible cancellation"
        => "Sconto 7+ notti\nPulizia inclusa\nCancellazione flessibile",
    '€/month'                      => '€/mese',
    "30+ nights\nAll utilities included\nBest value"
        => "30+ notti\nUtenze incluse\nMiglior rapporto qualità-prezzo",
    'Specifications'               => 'Specifiche',
    'Highlights'                   => 'Punti di forza',
    'Amenities'                    => 'Dotazioni',
    'Wi-Fi, Air Conditioning, Heating, Kitchen, Washing Machine, TV, Iron, Hair Dryer, Coffee Machine, Workspace'
        => 'Wi-Fi, Aria condizionata, Riscaldamento, Cucina, Lavatrice, TV, Ferro da stiro, Asciugacapelli, Macchina caffè, Postazione lavoro',
    'House Rules'                  => 'Regole della casa',
    'No smoking, No pets, No parties, Quiet hours (22:00 - 08:00)'
        => 'Vietato fumare, Animali non ammessi, Vietate feste, Orari di silenzio (22:00 - 08:00)',
    'Check-in / Check-out'         => 'Check-in / Check-out',
    'Self check-in available. Check-in: 14:00, Check-out: 11:00. Late check-out upon request.'
        => 'Self check-in disponibile. Check-in: 14:00, Check-out: 11:00. Late check-out su richiesta.',
    'Cancellation Policy'          => 'Politica di cancellazione',
    'Free cancellation up to 7 days before arrival. 50% refund for cancellations within 7 days.'
        => 'Cancellazione gratuita fino a 7 giorni prima dell\'arrivo. Rimborso del 50% per cancellazioni entro 7 giorni.',
    'The Neighborhood'             => 'Il Quartiere',
    'Discover what\'s around'      => 'Scopri cosa c\'è intorno',
    'Availability'                 => 'Disponibilità',
    'Updated daily'                => 'Aggiornato quotidianamente',
    'This property is currently available for booking. Contact us for exact dates and special requests.'
        => 'Questa proprietà è attualmente disponibile. Contattaci per date precise e richieste speciali.',
    'View on map'                  => 'Vedi sulla mappa',
    'Guest Ratings'                => 'Valutazioni ospiti',
    'Based on recent reviews'      => 'Basato su recensioni recenti',
    'Amazing stay! The apartment was exactly as described. Clean, well-equipped and in a perfect location. Would definitely come back!'
        => 'Soggiorno fantastico! L\'appartamento era esattamente come descritto. Pulito, ben attrezzato e in posizione perfetta. Torneremo sicuramente!',
    'Marco R.'                     => 'Marco R.',
    'Guest — March 2025'           => 'Ospite — Marzo 2025',
    'We put our heart into making this space feel like home. Every detail has been thought of to ensure your comfort during your stay.'
        => 'Abbiamo messo il cuore per far sentire questo spazio come casa. Ogni dettaglio è stato pensato per garantire il vostro comfort durante il soggiorno.',
    'Your Host'                    => 'Il tuo Host',
    'Superhost'                    => 'Superhost',
    'Passionate about hospitality. Always available to help make your stay unforgettable.'
        => 'Appassionato di ospitalità. Sempre disponibile per rendere il vostro soggiorno indimenticabile.',
    'Living Area'                  => 'Zona Giorno',
    'Bright and spacious'          => 'Luminoso e spazioso',
    'Kitchen'                      => 'Cucina',
    'Fully equipped'               => 'Completamente attrezzata',
    'Bedroom'                      => 'Camera da letto',
    'Comfortable and quiet'        => 'Confortevole e tranquilla',
    'Here to help'                 => 'Qui per aiutarti',
    'Response Time'                => 'Tempo di risposta',
    'Your host typically responds within 1 hour. Feel free to reach out with any questions before or during your stay.'
        => 'Il tuo host risponde solitamente entro 1 ora. Non esitare a contattarci per qualsiasi domanda prima o durante il soggiorno.',
    'Find us on the map'           => 'Trovaci sulla mappa',
    'Rome, Italy'                  => 'Roma, Italia',
    'Ready to Book?'               => 'Pronto a prenotare?',
    'Secure your dates now — limited availability'
        => 'Prenota le tue date ora — disponibilità limitata',
    'View Details'                 => 'Vedi dettagli',
    'Explore'                      => 'Esplora',
    'View'                         => 'Visualizza',

    // =====================================================================
    // Template #26 — Sand (stringhe demo in inglese)
    // =====================================================================
    'Event has started!'           => 'L\'evento è iniziato!',
    'Accordion Item 1'             => 'Elemento Accordion 1',
    'Content for the first accordion item.'  => 'Contenuto del primo elemento accordion.',
    'Accordion Item 2'             => 'Elemento Accordion 2',
    'Content for the second accordion item.' => 'Contenuto del secondo elemento accordion.',
    'Accordion Item 3'             => 'Elemento Accordion 3',
    'Content for the third accordion item.'  => 'Contenuto del terzo elemento accordion.',
    'Section Headline'             => 'Titolo Sezione',
    'A brief subtitle goes here'   => 'Qui va un breve sottotitolo',
    'Section Title'                => 'Titolo Sezione',
    'Add your content here. This is a simple text block that you can customize.'
        => 'Aggiungi il tuo contenuto qui. Questo è un semplice blocco di testo che puoi personalizzare.',
    'CEO, Company'                 => 'CEO, Azienda',
    'Pro Plan'                     => 'Piano Pro',
    'Jane Smith'                   => 'Jane Smith',
    'Lead Designer'                => 'Designer Principale',
    'Passionate about creating beautiful user experiences.'
        => 'Appassionata nella creazione di esperienze utente straordinarie.',

    // =====================================================================
    // Template #31 — Roby (stringhe in inglese)
    // =====================================================================
    'Welcome to Our Site'          => 'Benvenuto nel nostro sito',
    'Discover something amazing'   => 'Scopri qualcosa di straordinario',
    'This is an amazing product! Highly recommended.'
        => 'Questo è un prodotto straordinario! Altamente consigliato.',
    'John Doe'                     => 'John Doe',
    'Slide One'                    => 'Slide Uno',
    'First slide description'      => 'Descrizione prima slide',
    'Slide Two'                    => 'Slide Due',
    'Second slide description'     => 'Descrizione seconda slide',
    'Slide Three'                  => 'Slide Tre',
    'Third slide description'      => 'Descrizione terza slide',
    'Project Title'                => 'Titolo Progetto',
    'A brief description of this project.'
        => 'Una breve descrizione di questo progetto.',

    // =====================================================================
    // Stringhe condivise tra più template
    // =====================================================================
    'Slide 1'                      => 'Slide 1',
    'Slide 2'                      => 'Slide 2',
];

$olo_db  = new Olo_Database();
$lang_db = new Olo_Lang_Database();
$list    = $olo_db->list_templates( [ 'per_page' => 100 ] );

$total_content_fixes = 0;
$total_trans_fixes   = 0;

foreach ( $list['items'] as $template ) {
    $tpl_id  = $template['id'];
    $content = $template['content'];

    if ( empty( $content ) || ! is_array( $content ) ) {
        continue;
    }

    $content_json_before = json_encode( $content, JSON_UNESCAPED_UNICODE );
    $fixes_in_tpl = 0;

    // Funzione ricorsiva per sostituire stringhe nel tile tree
    $replace_in_tree = function( &$nodes ) use ( $en_to_it, &$replace_in_tree, &$fixes_in_tpl ) {
        foreach ( $nodes as &$node ) {
            if ( ! empty( $node['settings'] ) && is_array( $node['settings'] ) ) {
                replace_in_settings( $node['settings'], $en_to_it, $fixes_in_tpl );
            }
            if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
                $replace_in_tree( $node['children'] );
            }
        }
    };

    $replace_in_tree( $content );

    // Se qualcosa è cambiato, aggiorna il template nel DB
    $content_json_after = json_encode( $content, JSON_UNESCAPED_UNICODE );
    if ( $content_json_before !== $content_json_after ) {
        global $wpdb;
        $table = $wpdb->prefix . 'olo_templates';
        $wpdb->update(
            $table,
            [ 'content' => $content_json_after ],
            [ 'id' => $tpl_id ],
            [ '%s' ],
            [ '%d' ]
        );
        $total_content_fixes += $fixes_in_tpl;
        echo "Template #{$tpl_id} [{$template['title']}]: {$fixes_in_tpl} stringhe sostituite nel contenuto\n";
    }

    // Aggiorna anche i record nella tabella traduzioni
    $trans_fixes = fix_translation_originals( $tpl_id, $en_to_it, $lang_db );
    if ( $trans_fixes > 0 ) {
        $total_trans_fixes += $trans_fixes;
        echo "  → {$trans_fixes} record traduzioni aggiornati (original → italiano)\n";
    }
}

echo "\n=== RISULTATO ===\n";
echo "Stringhe sostituite nei template: {$total_content_fixes}\n";
echo "Record traduzioni aggiornati: {$total_trans_fixes}\n";
echo "Fatto!\n";

// =========================================================================
// Funzioni helper
// =========================================================================

function replace_in_settings( &$settings, $en_to_it, &$count ) {
    $items_keys = [ 'items', 'panels', 'slides', 'markers', 'cells', 'features', 'steps', 'members', 'tabs', 'options', 'links', 'buttons' ];

    foreach ( $settings as $key => &$value ) {
        if ( is_array( $value ) && in_array( $key, $items_keys, true ) ) {
            foreach ( $value as &$item ) {
                if ( is_array( $item ) ) {
                    replace_in_settings( $item, $en_to_it, $count );
                }
            }
            continue;
        }

        if ( is_string( $value ) && isset( $en_to_it[ $value ] ) && $en_to_it[ $value ] !== $value ) {
            $value = $en_to_it[ $value ];
            $count++;
        }
    }
}

function fix_translation_originals( $template_id, $en_to_it, $lang_db ) {
    global $wpdb;
    $table = $wpdb->prefix . 'olo_translations';
    $count = 0;

    foreach ( [ 'en', 'de' ] as $lang ) {
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, original, translation FROM {$table} WHERE template_id = %d AND lang = %s",
            $template_id, $lang
        ), ARRAY_A );

        foreach ( $rows as $row ) {
            $old_original = $row['original'];
            if ( isset( $en_to_it[ $old_original ] ) && $en_to_it[ $old_original ] !== $old_original ) {
                $new_original = $en_to_it[ $old_original ];

                // Per EN: se la traduzione è uguale all'originale inglese, mantienila
                // Per DE: mantieni la traduzione tedesca esistente
                $update_data = [ 'original' => $new_original ];

                // Se EN e la traduzione era = originale (inglese), aggiorna traduzione = vecchio originale
                if ( $lang === 'en' && $row['translation'] === $old_original ) {
                    $update_data['translation'] = $old_original; // la traduzione EN resta l'inglese originale
                }

                $wpdb->update( $table, $update_data, [ 'id' => $row['id'] ] );
                $count++;
            }
        }
    }

    return $count;
}
