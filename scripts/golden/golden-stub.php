<?php
/**
 * golden-stub.php — il caso reso ripetibile, per il banco L1 (passo 0.1 del sistema compatto).
 *
 * Si carica SOLO con l'opzione `--require` di WP-CLI, che lo include PRIMA di WordPress:
 *
 *   wp --require=/root/olo-golden/golden-stub.php eval-file /root/olo-golden/render-golden.php …
 *
 * Perché prima: wp_rand() e wp_create_nonce() sono funzioni «pluggable» (wp-includes/
 * pluggable.php le definisce solo se nessuno l'ha già fatto). Con il solo eval-file
 * WordPress è già carico e le sue versioni non si possono più sostituire.
 *
 * Fuori dalla finestra di render (il flag $GLOBALS['olo_golden_det'], acceso e spento da
 * render-golden.php attorno a render_shortcode) le due funzioni fanno ESATTAMENTE quello
 * che fa il core: casualità vera, nonce veri. Dentro la finestra:
 *
 *  - wp_rand(): un numero ricavato da md5( chiave del template | file chiamante | n-esima
 *    chiamata da quel file ). Le 168 tile che nascono con wp_rand(10000, 99999) ottengono
 *    lo stesso uid a ogni esecuzione; e siccome il contatore è per FILE, una tile che
 *    cambia non sposta gli uid delle altre.
 *  - wp_create_nonce(): un valore fisso per azione. I nonce veri cambiano ogni 12 ore
 *    (wp_nonce_tick) e renderebbero diverso l'HTML fra la baseline e il «dopo».
 *
 * wp_unique_id() NON è pluggable: è un contatore statico del core. È deterministico perché
 * render-golden.php rende ogni template in un PROCESSO suo.
 *
 * Non va MAI caricato dal plugin né copiato in wp-content: vive in /root/olo-golden.
 * Nessuna chiamata a WordPress al caricamento (WordPress non c'è ancora).
 */

if ( defined( 'OLO_GOLDEN_STUB' ) ) {
	return;
}
define( 'OLO_GOLDEN_STUB', __FILE__ );

$GLOBALS['olo_golden_det']   = false; // finestra deterministica (render-golden.php)
$GLOBALS['olo_golden_seme']  = '';    // la chiave del template in render
$GLOBALS['olo_golden_conta'] = [];    // chiamate per etichetta (file chiamante)

if ( ! function_exists( 'olo_golden_numero' ) ) {
	/**
	 * Numero ripetibile in [$lo, $hi] per la n-esima chiamata con questa etichetta.
	 */
	function olo_golden_numero( $etichetta, $lo, $hi ) {
		$n = isset( $GLOBALS['olo_golden_conta'][ $etichetta ] ) ? $GLOBALS['olo_golden_conta'][ $etichetta ] : 0;
		$GLOBALS['olo_golden_conta'][ $etichetta ] = $n + 1;
		$span = $hi - $lo + 1;
		if ( ! is_int( $span ) || $span <= 1 ) {
			return $lo;
		}
		// 48 bit dell'md5: un intero esatto anche su PHP a 64 bit, uniforme sul range.
		$h = hexdec( substr( md5( (string) $GLOBALS['olo_golden_seme'] . '|' . $etichetta . '|' . $n ), 0, 12 ) );
		return $lo + ( $h % $span );
	}
}

if ( function_exists( 'wp_rand' ) ) {
	// Arrivato tardi (WordPress già carico): render-golden.php se ne accorge e si ferma.
	define( 'OLO_GOLDEN_STUB_TARDI', true );
} else {
	/**
	 * wp_rand() del core, con in più la finestra deterministica.
	 * Stessa firma e stesso contratto: interi in [min, max], argomenti in qualunque ordine.
	 */
	function wp_rand( $min = null, $max = null ) {
		$min = ( null === $min ) ? 0 : (int) $min;
		$max = ( null === $max ) ? 4294967295 : (int) $max;
		$lo  = min( $min, $max );
		$hi  = max( $min, $max );
		if ( empty( $GLOBALS['olo_golden_det'] ) ) {
			return abs( (int) random_int( $lo, $hi ) );
		}
		$bt    = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 );
		$file  = isset( $bt[0]['file'] ) ? basename( $bt[0]['file'] ) : '?';
		return abs( (int) olo_golden_numero( 'rand|' . $file, $lo, $hi ) );
	}
}

if ( ! function_exists( 'wp_create_nonce' ) ) {
	/**
	 * wp_create_nonce() del core (wp-includes/pluggable.php), con un valore fisso per
	 * azione dentro la finestra. Fuori è la stessa formula del core.
	 */
	function wp_create_nonce( $action = -1 ) {
		if ( ! empty( $GLOBALS['olo_golden_det'] ) ) {
			return substr( md5( 'olo-golden-nonce|' . $action ), 0, 10 );
		}
		$user = wp_get_current_user();
		$uid  = (int) $user->ID;
		if ( ! $uid ) {
			/** This filter is documented in wp-includes/pluggable.php */
			$uid = apply_filters( 'nonce_user_logged_out', $uid, $action );
		}
		$token = wp_get_session_token();
		$i     = wp_nonce_tick( $action );
		return substr( wp_hash( $i . '|' . $action . '|' . $uid . '|' . $token, 'nonce' ), -12, 10 );
	}
}
