<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$res = Olo_Lang_AI_Translator::translate_for_lang( '__plugin:olo-calendar', 'en', [
    'limit' => 3,
] );

echo "=== Result ===\n";
echo "processed: " . ( $res['processed'] ?? 'n/a' ) . "\n";
echo "written:   " . ( $res['written'] ?? 'n/a' ) . "\n";
echo "skipped:   " . ( $res['skipped'] ?? 'n/a' ) . "\n";
echo "batches:   " . ( $res['batches'] ?? 'n/a' ) . "\n";
echo "duration:  " . ( $res['duration_ms'] ?? 'n/a' ) . " ms\n";
echo "message:   " . ( $res['message'] ?? '(none)' ) . "\n";

if ( ! empty( $res['errors'] ) ) {
    echo "errors:\n";
    foreach ( $res['errors'] as $i => $e ) {
        echo "  [$i] $e\n";
    }
}
