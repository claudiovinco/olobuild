<?php
/**
 * rinormalizza.php — riapplica normalizza.json a uno snapshot GIÀ reso da render-golden.php,
 * quando si aggiunge una regola: la baseline resta confrontabile senza reinstallare la versione
 * con cui era stata presa. Le regole sono idempotenti, quindi riapplicarle tutte è sicuro.
 *
 * Aggiorna in posto <chiave>.html, _md5.txt, la colonna md5 di _summary.tsv e di _righe/*.tsv,
 * e porta gli a capo di _style-system.css a LF. Non tocca md5_grezzo né _grezzo/.
 * Non serve WordPress: php rinormalizza.php <cartella dello snapshot>
 */

if ( PHP_SAPI !== 'cli' || empty( $argv[1] ) || ! is_dir( $argv[1] ) ) {
	fwrite( STDERR, "Uso: php rinormalizza.php <cartella dello snapshot>\n" );
	exit( 2 );
}
$dir  = rtrim( $argv[1], '/' );
$json = json_decode( (string) file_get_contents( __DIR__ . '/normalizza.json' ), true );
if ( ! is_array( $json ) || empty( $json['regole'] ) ) {
	fwrite( STDERR, "normalizza.json mancante o illeggibile accanto a rinormalizza.php\n" );
	exit( 2 );
}
$regole = [];
foreach ( $json['regole'] as $r ) {
	// La versione è già diventata VERSIONE al render: un segnaposto che non trova niente.
	$cerca    = str_replace( '{VERSIONE}', preg_quote( '0.0.0-rinormalizza', '~' ), (string) $r['cerca'] );
	$regole[] = [ '~' . str_replace( '~', '\~', $cerca ) . '~', (string) $r['sostituisci'] ];
}
$normalizza = function ( $html ) use ( $regole ) {
	foreach ( $regole as $r ) {
		$html = preg_replace( $r[0], $r[1], $html );
	}
	return $html;
};

$nuovi   = [];
$cambiati = 0;
foreach ( glob( $dir . '/*.html' ) as $f ) {
	$k    = basename( $f, '.html' );
	$prima = (string) file_get_contents( $f );
	$dopo  = $normalizza( $prima );
	if ( $dopo !== $prima ) {
		file_put_contents( $f, $dopo );
		$cambiati++;
	}
	$nuovi[ $k ] = md5( $dopo );
}

// _summary.tsv e _righe/*.tsv: la colonna «md5», cercata per nome nell'intestazione.
$sommario = $dir . '/_summary.tsv';
$righe    = file( $sommario, FILE_IGNORE_NEW_LINES );
$testa    = explode( "\t", (string) array_shift( $righe ) );
$c_k      = array_search( 'chiave', $testa, true );
$c_md5    = array_search( 'md5', $testa, true );
$c_stato  = array_search( 'stato', $testa, true );
if ( false === $c_k || false === $c_md5 ) {
	fwrite( STDERR, "_summary.tsv senza le colonne chiave/md5\n" );
	exit( 2 );
}
$aggiorna = function ( $riga ) use ( $nuovi, $c_k, $c_md5, $c_stato ) {
	$c = explode( "\t", $riga );
	$k = $c[ $c_k ] ?? '';
	// Solo le chiavi rese: un fatale ha l'md5 vuoto e resta vuoto.
	if ( isset( $nuovi[ $k ] ) && '' !== ( $c[ $c_md5 ] ?? '' ) && ( false === $c_stato || 'fatale' !== ( $c[ $c_stato ] ?? '' ) ) ) {
		$c[ $c_md5 ] = $nuovi[ $k ];
	}
	return implode( "\t", $c );
};
file_put_contents( $sommario, implode( "\t", $testa ) . "\n" . implode( "\n", array_map( $aggiorna, $righe ) ) . "\n" );
foreach ( glob( $dir . '/_righe/*.tsv' ) as $f ) {
	file_put_contents( $f, $aggiorna( rtrim( (string) file_get_contents( $f ), "\n" ) ) . "\n" );
}

// _md5.txt «md5  chiave», rifatto dal sommario aggiornato (stesso ordine).
$md5 = [];
foreach ( array_map( $aggiorna, $righe ) as $riga ) {
	$c = explode( "\t", $riga );
	if ( '' !== ( $c[ $c_md5 ] ?? '' ) ) {
		$md5[] = $c[ $c_md5 ] . '  ' . $c[ $c_k ];
	}
}
file_put_contents( $dir . '/_md5.txt', implode( "\n", $md5 ) . "\n" );

if ( is_file( $dir . '/_style-system.css' ) ) {
	file_put_contents( $dir . '/_style-system.css', preg_replace( '/\r\n?/', "\n", (string) file_get_contents( $dir . '/_style-system.css' ) ) );
}
echo "rinormalizzato $dir: " . count( $nuovi ) . " pagine, $cambiati cambiate\n";
