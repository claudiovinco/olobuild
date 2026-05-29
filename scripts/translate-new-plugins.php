<?php
/**
 * Batch translate i 3 nuovi plugin in tutte le lingue attive di olo-lang.
 * Esegui con:
 *   wp eval-file translate-new-plugins.php --path=/var/www/wordpress --allow-root
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$plugins = [ 'olo-calendar', 'olo-vtour', 'oloele-property-map' ];
$langs   = [ 'cs', 'de', 'en', 'es', 'fr', 'hu', 'ja', 'nl', 'pl', 'pt', 'ru' ];

if ( ! class_exists( 'Olo_Lang_AI_Translator' ) ) {
    echo "ERR: classe Olo_Lang_AI_Translator non trovata\n";
    exit( 1 );
}

if ( ! Olo_Lang_AI_Translator::has_api_key() ) {
    echo "ERR: API key non configurata\n";
    exit( 1 );
}

foreach ( $plugins as $plugin ) {
    $tile_id = '__plugin:' . $plugin;
    foreach ( $langs as $lang ) {
        echo str_pad( "$plugin -> $lang ", 50 );
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
