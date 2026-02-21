<?php
/**
 * Popola wp_olo_translations con le voci dei menu WordPress.
 * Scansiona tutti i menu attivi e inserisce titoli e label come stringhe globali.
 *
 * Eseguire con: wp eval-file populate-menu-strings.php --path=/var/www/wordpress --allow-root
 * Sicuro da rieseguire: usa upsert.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$db = new Olo_Lang_Database();

// Traduzioni menu: IT → EN, DE
$menu_translations = [
    // === Menu "primo" (header principale) ===
    'Homepage'          => [ 'en' => 'Homepage', 'de' => 'Startseite' ],
    'Mappa Interattiva' => [ 'en' => 'Interactive Map', 'de' => 'Interaktive Karte' ],
    'Sand'              => [ 'en' => 'Sand', 'de' => 'Sand' ],
    'I componenti'      => [ 'en' => 'Components', 'de' => 'Komponenten' ],
    'Overlay Slider'    => [ 'en' => 'Overlay Slider', 'de' => 'Overlay Slider' ],
    'Accesso'           => [ 'en' => 'Login', 'de' => 'Anmeldung' ],

    // === Menu "mega-demo" ===
    'Home'              => [ 'en' => 'Home', 'de' => 'Home' ],
    'Servizi'           => [ 'en' => 'Services', 'de' => 'Dienstleistungen' ],
    'Baite'             => [ 'en' => 'Chalets', 'de' => 'Hütten' ],
    'Web Design'        => [ 'en' => 'Web Design', 'de' => 'Webdesign' ],
    'Siti Vetrina'      => [ 'en' => 'Showcase Sites', 'de' => 'Präsentationsseiten' ],
    'E-commerce'        => [ 'en' => 'E-commerce', 'de' => 'E-Commerce' ],
    'Landing Page'      => [ 'en' => 'Landing Page', 'de' => 'Landing Page' ],
    'Restyling'         => [ 'en' => 'Restyling', 'de' => 'Neugestaltung' ],
    'Marketing'         => [ 'en' => 'Marketing', 'de' => 'Marketing' ],
    'SEO'               => [ 'en' => 'SEO', 'de' => 'SEO' ],
    'Social Media'      => [ 'en' => 'Social Media', 'de' => 'Social Media' ],
    'Email Marketing'   => [ 'en' => 'Email Marketing', 'de' => 'E-Mail-Marketing' ],
    'Content Strategy'  => [ 'en' => 'Content Strategy', 'de' => 'Content-Strategie' ],
    'Sviluppo'          => [ 'en' => 'Development', 'de' => 'Entwicklung' ],
    'App Mobile'        => [ 'en' => 'Mobile Apps', 'de' => 'Mobile Apps' ],
    'Software Custom'   => [ 'en' => 'Custom Software', 'de' => 'Individuelle Software' ],
    'API e Integrazioni' => [ 'en' => 'APIs & Integrations', 'de' => 'APIs & Integrationen' ],
    'Portfolio'         => [ 'en' => 'Portfolio', 'de' => 'Portfolio' ],
    'Roby'              => [ 'en' => 'Roby', 'de' => 'Roby' ],
    'Calendario Eventi' => [ 'en' => 'Events Calendar', 'de' => 'Veranstaltungskalender' ],
    'Chi Siamo'         => [ 'en' => 'About Us', 'de' => 'Über uns' ],
    'Il Team'           => [ 'en' => 'The Team', 'de' => 'Das Team' ],
    'La Nostra Storia'  => [ 'en' => 'Our Story', 'de' => 'Unsere Geschichte' ],
    'Lavora con Noi'    => [ 'en' => 'Careers', 'de' => 'Karriere' ],
    'Contatti'          => [ 'en' => 'Contact', 'de' => 'Kontakt' ],
    'Esplora'           => [ 'en' => 'Explore', 'de' => 'Entdecken' ],
    'Griglia con Filtri' => [ 'en' => 'Grid with Filters', 'de' => 'Raster mit Filtern' ],
    'Tutte le Baite'    => [ 'en' => 'All Chalets', 'de' => 'Alle Hütten' ],
    'Le Valli'          => [ 'en' => 'The Valleys', 'de' => 'Die Täler' ],
    'Val di Fassa'      => [ 'en' => 'Val di Fassa', 'de' => 'Val di Fassa' ],
    'Val di Non'        => [ 'en' => 'Val di Non', 'de' => 'Val di Non' ],
    'Val di Sole'       => [ 'en' => 'Val di Sole', 'de' => 'Val di Sole' ],
    'Val Rendena'       => [ 'en' => 'Val Rendena', 'de' => 'Val Rendena' ],
    'Passo Rolle'       => [ 'en' => 'Passo Rolle', 'de' => 'Passo Rolle' ],
    'Prenota'           => [ 'en' => 'Book', 'de' => 'Buchen' ],
    'Prenotazioni Online' => [ 'en' => 'Online Booking', 'de' => 'Online-Buchung' ],
    'Contattaci'        => [ 'en' => 'Contact Us', 'de' => 'Kontaktieren Sie uns' ],

    // === Titoli pagine WordPress (per l'output buffer) ===
    'Le nostre Baite'           => [ 'en' => 'Our Chalets', 'de' => 'Unsere Hütten' ],
    'Servizi e Prenotazioni'    => [ 'en' => 'Services & Booking', 'de' => 'Dienstleistungen & Buchung' ],
    'Prenota Consulenza'        => [ 'en' => 'Book a Consultation', 'de' => 'Beratung buchen' ],
    'Home Baite'                => [ 'en' => 'Chalets Home', 'de' => 'Hütten Startseite' ],
];

$inserted = 0;
$idx = 0;

foreach ( $menu_translations as $it => $langs ) {
    $idx++;
    foreach ( $langs as $lang => $translation ) {
        // Non inserire se la traduzione è identica all'originale
        if ( $translation === $it ) {
            continue;
        }
        $result = $db->save_translation( [
            'template_id' => 0,
            'tile_id'     => 'wp-menu',
            'field_path'  => 'menu_item_' . $idx,
            'lang'        => $lang,
            'original'    => $it,
            'translation' => $translation,
            'status'      => 'tradotto',
        ] );
        if ( $result ) {
            $inserted++;
        }
    }
}

echo "\n=== Populate Menu Strings ===\n";
echo "Voci menu processate: " . count( $menu_translations ) . "\n";
echo "Record inseriti/aggiornati: {$inserted}\n";
echo "Fatto!\n";
