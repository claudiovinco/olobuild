<?php
/**
 * Il passaggio fra le pagine (1.4.1218): una scelta di tutto il sito, salvata nell'opzione
 * olobuild_transizioni, che si cambiano da Impostazioni pagina › Effetti di pagina (si salvano da sole, come il Cursore
 * del sito).
 *
 * - Passaggio fra le pagine: le View Transitions fra documenti del browser (Chrome ed Edge dalla 126, Safari dalla 18.2;
 *   gli altri cambiano pagina come sempre): dissolvenza, tendina che sale, zoom, cerchio che si apre dal punto del clic.
 * La schermata di caricamento c'è già: è l'Introduzione di ogni pagina (Olobuild_Page_Intro, Effetti di pagina).
 *
 * Niente per chi ha chiesto meno movimento, niente nel canvas del builder. Gli script non contengono «&&»: WordPress
 * li trasformerebbe in entità.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Olobuild_Transizioni {

	const OPT       = 'olobuild_transizioni';
	const PASSAGGI  = [ '', 'dissolvenza', 'tendina', 'zoom', 'cerchio' ];

	public static function init() {
		add_action( 'rest_api_init', [ __CLASS__, 'rotte' ] );
		if ( defined( 'OLOBUILD_BUILDER_PREVIEW' ) && OLOBUILD_BUILDER_PREVIEW ) {
			return;
		}
		add_action( 'wp_head', [ __CLASS__, 'testa' ], 5 );
	}

	public static function get() {
		$d = [ 'passaggio' => '', 'durata' => 500 ];
		$s = get_option( self::OPT, [] );
		return wp_parse_args( is_array( $s ) ? $s : [], $d );
	}

	public static function pulisci( $in ) {
		$p = sanitize_key( (string) ( $in['passaggio'] ?? '' ) );
		return [
			'passaggio'         => in_array( $p, self::PASSAGGI, true ) ? $p : '',
			'durata'            => max( 200, min( 1500, (int) ( $in['durata'] ?? 500 ) ) ),
		];
	}

	public static function rotte() {
		register_rest_route( 'olobuild/v1', '/transizioni', [
			[
				'methods'             => 'GET',
				'callback'            => function () {
					return rest_ensure_response( self::get() );
				},
				'permission_callback' => function () { return current_user_can( 'manage_options' ); },
			],
			[
				'methods'             => 'PUT',
				'callback'            => function ( $request ) {
					$b = $request->get_json_params();
					if ( ! is_array( $b ) ) {
						return new WP_Error( 'invalid_data', __( 'Dati non validi.', 'olobuild' ), [ 'status' => 400 ] );
					}
					$clean = self::pulisci( array_merge( self::get(), $b ) );
					update_option( self::OPT, $clean, false );
					return rest_ensure_response( $clean );
				},
				'permission_callback' => function () { return current_user_can( 'manage_options' ); },
			],
		] );
	}

	/** Il canvas del builder e le anteprime non cambiano pagina: lì niente. */
	private static function nel_builder() {
		return ! empty( $_GET['olo_builder_iframe'] ) || is_admin(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- solo lettura
	}

	/** Il passaggio fra le pagine: la regola @view-transition e le animazioni della scelta. */
	public static function testa() {
		$o = self::get();
		if ( '' === $o['passaggio'] || self::nel_builder() ) {
			return;
		}
		$ms   = (int) $o['durata'];
		$curva = 'cubic-bezier(.65,0,.35,1)';
		$css  = '@view-transition{navigation:auto}'
			. '::view-transition-group(root){animation-duration:' . $ms . 'ms;animation-timing-function:' . $curva . '}';
		switch ( $o['passaggio'] ) {
			case 'tendina':
				$css .= '::view-transition-old(root){animation:' . $ms . 'ms ' . $curva . ' both olo-vt-via}'
					. '::view-transition-new(root){animation:' . $ms . 'ms ' . $curva . ' both olo-vt-sale}'
					. '@keyframes olo-vt-via{to{transform:translateY(-12%);opacity:.4}}'
					. '@keyframes olo-vt-sale{from{transform:translateY(100%)}to{transform:none}}';
				break;
			case 'zoom':
				$css .= '::view-transition-old(root){animation:' . $ms . 'ms ' . $curva . ' both olo-vt-indietro}'
					. '::view-transition-new(root){animation:' . $ms . 'ms ' . $curva . ' both olo-vt-avanti}'
					. '@keyframes olo-vt-indietro{to{transform:scale(.92);opacity:0}}'
					. '@keyframes olo-vt-avanti{from{transform:scale(1.06);opacity:0}}';
				break;
			case 'cerchio':
				// la pagina nuova si apre in un cerchio che parte dal punto del clic (lo script qui sotto)
				$css .= '::view-transition-old(root){animation:none}::view-transition-new(root){animation:none;z-index:1}';
				break;
			default: // dissolvenza: quella del browser, con la durata scelta
				$css .= '::view-transition-old(root),::view-transition-new(root){animation-duration:' . $ms . 'ms}';
		}
		echo '<style id="olo-transizioni">@media (prefers-reduced-motion:no-preference){' . $css . '}</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS fisso, durata intera
		if ( 'cerchio' === $o['passaggio'] ) {
			echo '<script>(function(){try{addEventListener("pointerdown",function(e){sessionStorage.setItem("olo-vt-xy",e.clientX+","+e.clientY);},true);'
				. 'addEventListener("pagereveal",function(e){if(!e.viewTransition){return;}var xy=(sessionStorage.getItem("olo-vt-xy")||"").split(",");'
				. 'var x=parseFloat(xy[0]),y=parseFloat(xy[1]);if(isNaN(x)){x=innerWidth/2;y=innerHeight/2;}'
				. 'var r=Math.hypot(Math.max(x,innerWidth-x),Math.max(y,innerHeight-y));'
				. 'e.viewTransition.ready.then(function(){document.documentElement.animate({clipPath:["circle(0px at "+x+"px "+y+"px)","circle("+r+"px at "+x+"px "+y+"px)"]},'
				. '{duration:' . $ms . ',easing:"cubic-bezier(.65,0,.35,1)",pseudoElement:"::view-transition-new(root)"});});});}catch(err){}})();</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- script fisso
		}
	}
}
