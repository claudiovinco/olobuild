<?php
/**
 * Set tipografici globali del sito OLOX (olox-serif / olox-mono / olox-body).
 * Merge NON distruttivo: i set esistenti con altri id restano intatti,
 * i tre set olox-* vengono aggiunti o aggiornati.
 * Uso: wp eval-file olox-typography.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$olox_sets = [
    [ 'id' => 'olox-serif', 'label' => 'OLOX Serif (Fraunces)',   'family' => 'Fraunces',       'weight' => '400', 'transform' => 'none', 'line_height' => '1.12', 'letter_spacing' => '0' ],
    [ 'id' => 'olox-mono',  'label' => 'OLOX Mono (JetBrains)',   'family' => 'JetBrains Mono', 'weight' => '600', 'transform' => 'none', 'line_height' => '1.4',  'letter_spacing' => '0' ],
    [ 'id' => 'olox-body',  'label' => 'OLOX Body (Inter)',       'family' => 'Inter',          'weight' => '400', 'transform' => 'none', 'line_height' => '1.6',  'letter_spacing' => '0' ],
];

$current = get_option( 'olobuild_global_typography', [] );
if ( ! is_array( $current ) ) $current = [];

$by_id = [];
foreach ( $current as $set ) {
    if ( is_array( $set ) && ! empty( $set['id'] ) ) $by_id[ $set['id'] ] = $set;
}
foreach ( $olox_sets as $set ) {
    $by_id[ $set['id'] ] = $set;
}
update_option( 'olobuild_global_typography', array_values( $by_id ) );
echo 'Typography: ' . count( $by_id ) . " set totali (olox-serif/mono/body ok)\n";
