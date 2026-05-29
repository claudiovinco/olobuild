<?php
/**
 * Scansiona i 3 nuovi plugin, importa le stringhe in wp_olo_translations,
 * poi richiama l'AI translator per popolare le traduzioni in tutte le lingue.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$plugins = [ 'olo-calendar', 'olo-vtour', 'oloele-property-map' ];
$langs   = [ 'cs', 'de', 'en', 'es', 'fr', 'hu', 'ja', 'nl', 'pl', 'pt', 'ru' ];

if ( ! class_exists( 'Olo_Lang_Plugin_Scanner_Core' ) ) {
    echo "ERR: Olo_Lang_Plugin_Scanner_Core non trovato\n";
    exit( 1 );
}
if ( ! class_exists( 'Olo_Lang_AI_Translator' ) ) {
    echo "ERR: Olo_Lang_AI_Translator non trovato\n";
    exit( 1 );
}
if ( ! Olo_Lang_AI_Translator::has_api_key() ) {
    echo "ERR: API key non configurata\n";
    exit( 1 );
}

// ── 1. Scan + Import ──
echo "[1/2] Scan + import stringhe per i 3 plugin\n";
foreach ( $plugins as $plugin ) {
    $plugin_dir = WP_PLUGIN_DIR . '/' . $plugin;
    if ( ! is_dir( $plugin_dir ) ) {
        echo "  SKIP $plugin: directory mancante ($plugin_dir)\n";
        continue;
    }
    $scan = Olo_Lang_Plugin_Scanner_Core::scan( $plugin_dir, [
        'filter_domain' => $plugin,
    ] );
    $items = $scan['items'] ?? [];
    echo "  $plugin: scansionate " . count( $items ) . " stringhe uniche\n";

    if ( ! empty( $items ) ) {
        $res = Olo_Lang_Plugin_Scanner_Core::import( $items, $langs, false );
        echo "    new: {$res['new']}, existing: {$res['existing']}, wrote: {$res['wrote']}\n";
    }
}

// ── 2. AI translate ──
echo "\n[2/2] AI translate per ogni plugin × lingua\n";
foreach ( $plugins as $plugin ) {
    $tile_id = '__plugin:' . $plugin;
    foreach ( $langs as $lang ) {
        echo "  " . str_pad( "$plugin → $lang", 45 );
        $t0 = microtime( true );
        try {
            $res = Olo_Lang_AI_Translator::translate_for_lang( $tile_id, $lang, [
                'only_missing' => true,
                'batch'        => 30,
            ] );
            $dt = round( microtime( true ) - $t0, 1 );
            $translated = isset( $res['translated'] ) ? (int) $res['translated'] : ( isset( $res['count'] ) ? (int) $res['count'] : 0 );
            $skipped    = isset( $res['skipped'] ) ? (int) $res['skipped'] : 0;
            $err        = $res['error'] ?? '';
            if ( $err ) {
                echo "ERR ($err) [{$dt}s]\n";
            } else {
                echo "tradotte: $translated, saltate: $skipped [{$dt}s]\n";
            }
        } catch ( Throwable $e ) {
            $dt = round( microtime( true ) - $t0, 1 );
            echo "EXCEPTION: " . $e->getMessage() . " [{$dt}s]\n";
        }
    }
}

echo "\nDONE\n";
