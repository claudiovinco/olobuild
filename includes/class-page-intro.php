<?php
/**
 * Olobuild_Page_Intro — l'apertura della pagina (Impostazioni pagina › Effetti di pagina › Apertura).
 *
 * Un velo a tutto schermo col nome del sito (o una scritta scelta) che dopo un attimo si apre e
 * scopre la pagina: la soglia che hanno quasi tutti i siti premiati del 2026 (Awwwards, siti del
 * giorno di ERA Residence, Son Daven, LPAS, Tengile, Belgrade Arbor). Tre modi:
 *   - «sipario»   il nome sale dentro la sua riga, poi il velo sale e scopre la pagina;
 *   - «contatore» un numero corre da 0 a 100 nell'angolo con una linea sotto, poi il velo sale;
 *   - «linea»     una riga sottile si disegna sotto il nome, poi il velo si apre in due.
 *
 * Dove esce: con un tema a blocchi la pagina si disegna prima della testata
 * (get_the_block_template_html() gira prima di wp_head), quindi il velo aspetta wp_body_open ed
 * esce subito dopo <body>: copre anche la testata dal primo fotogramma. Con un tema classico la
 * testata è già uscita e il velo esce in testa al template principale. Nel builder mai: coprirebbe
 * il canvas a ogni modifica (il canvas usa render_tiles_array, che non lo chiama).
 *
 * L'animazione è solo CSS: senza JavaScript il velo si apre lo stesso. Lo script (poche righe, senza
 * «&&», vedi wptexturize) fa il resto: una volta per sessione, clic o tasto per saltarla, toglie il
 * nodo alla fine. Con «riduci il movimento» il velo non c'è.
 *
 * Chiavi (page settings, tutte facoltative tranne l'interruttore):
 *   page_intro_enabled  bool
 *   page_intro_style    'sipario' | 'contatore' | 'linea'           (predefinito sipario)
 *   page_intro_text     la scritta grande                           (vuota = nome del sito)
 *   page_intro_caption  la riga piccola sotto                       (vuota = nessuna)
 *   page_intro_bg       colore del velo                             (vuoto = sfondo del tema)
 *   page_intro_color    colore della scritta                        (vuoto = testo del tema)
 *   page_intro_duration durata in ms, 1000-4000                     (predefinito 2000)
 *   page_intro_once     una volta per sessione                      (chiave assente = sì)
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Page_Intro {

    const STILI = [ 'sipario', 'contatore', 'linea' ];

    /** HTML pronto che aspetta wp_body_open (tema a blocchi). */
    private static $in_attesa = '';

    /** Un solo velo per richiesta, anche se il template principale si disegna due volte. */
    private static $fatto = false;

    public static function init() {
        add_action( 'wp_body_open', [ __CLASS__, 'stampa_in_attesa' ], 2 );
    }

    /**
     * Dal renderer, per il template principale. Ritorna l'HTML da mettere lì, oppure '' quando il
     * velo uscirà subito dopo <body> (o quando la pagina non ne ha).
     *
     * @param array $settings Page settings del template.
     * @return string
     */
    public static function per_template( $settings ) {
        if ( self::$fatto || ! is_array( $settings ) || empty( $settings['page_intro_enabled'] ) ) {
            return '';
        }
        if ( defined( 'OLOBUILD_BUILDER_PREVIEW' ) && OLOBUILD_BUILDER_PREVIEW ) {
            return '';
        }
        $html = self::markup( $settings );
        if ( '' === $html ) {
            return '';
        }
        self::$fatto = true;
        if ( did_action( 'wp_body_open' ) ) {
            return $html;
        }
        self::$in_attesa = $html;
        return '';
    }

    public static function stampa_in_attesa() {
        if ( '' === self::$in_attesa ) {
            return;
        }
        echo self::$in_attesa; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup assembled by markup() from escaped values
        self::$in_attesa = '';
    }

    /**
     * @param array $s Page settings.
     * @return string
     */
    private static function markup( $s ) {
        $stile = in_array( $s['page_intro_style'] ?? '', self::STILI, true ) ? $s['page_intro_style'] : 'sipario';
        $testo = trim( (string) ( $s['page_intro_text'] ?? '' ) );
        if ( '' === $testo ) {
            $testo = (string) get_bloginfo( 'name' );
        }
        $riga  = trim( (string) ( $s['page_intro_caption'] ?? '' ) );
        $bg    = Olobuild_Tile_Utils::safe_color( $s['page_intro_bg'] ?? '' );
        $fg    = Olobuild_Tile_Utils::safe_color( $s['page_intro_color'] ?? '' );
        $durata = isset( $s['page_intro_duration'] ) && is_numeric( $s['page_intro_duration'] )
            ? max( 1000, min( 4000, (int) $s['page_intro_duration'] ) )
            : 2000;
        $una_volta = array_key_exists( 'page_intro_once', $s ) ? ! empty( $s['page_intro_once'] ) : true;

        $vars = '--oi-d:' . $durata . 'ms';
        if ( '' !== $bg ) {
            $vars .= ';--oi-bg:' . $bg;
        }
        if ( '' !== $fg ) {
            $vars .= ';--oi-fg:' . $fg;
        }

        // «olo-tokens»: il velo sta fuori dal template, e quella classe gli dà gli stessi colori e font del tema
        // (anche quelli della sera), senza la geometria di .olo-template (Olobuild_Style_System::generate_css()).
        $out  = '<div id="olo-intro" class="olo-tokens olo-intro olo-intro--' . esc_attr( $stile ) . '" style="' . esc_attr( $vars ) . '" aria-hidden="true">';
        if ( 'linea' === $stile ) {
            $out .= '<span class="olo-intro__meta olo-intro__meta--su"></span><span class="olo-intro__meta olo-intro__meta--giu"></span>';
        }
        $out .= '<div class="olo-intro__in">';
        $out .= '<span class="olo-intro__nome"><span>' . esc_html( $testo ) . '</span></span>';
        if ( 'linea' === $stile ) {
            $out .= '<span class="olo-intro__linea"></span>';
        }
        if ( '' !== $riga ) {
            $out .= '<span class="olo-intro__riga">' . esc_html( $riga ) . '</span>';
        }
        $out .= '</div>';
        if ( 'contatore' === $stile ) {
            $out .= '<span class="olo-intro__conta"></span><span class="olo-intro__barra"></span>';
        }
        $out .= '</div>';
        $out .= '<style id="olo-intro-css">' . self::css() . '</style>';
        // La chiave della sessione cambia con la scritta e lo stile: un'altra apertura (un altro tema
        // nella Vetrina delle demo, un'altra sezione del sito) si vede comunque la prima volta.
        $out .= '<script>' . self::script( $una_volta, $durata, substr( md5( $stile . '|' . $testo ), 0, 8 ) ) . '</script>';
        return $out;
    }

    /** CSS fisso: colori, durata e scritte arrivano dalle variabili sull'elemento. */
    private static function css() {
        $in  = 'cubic-bezier(.22,1,.36,1)';
        $esc = 'cubic-bezier(.76,0,.24,1)';
        return '@property --oi-n{syntax:"<integer>";inherits:false;initial-value:0}'
            . '.olo-intro{position:fixed;inset:0;z-index:2147483000;display:grid;place-items:center;background:var(--oi-bg,var(--olo-color-background,#fff));color:var(--oi-fg,var(--olo-color-text,#111));cursor:pointer;animation:olo-intro-su var(--oi-d) ' . $esc . ' both;transition:opacity .45s ease}'
            . '.olo-intro--linea{background:none;animation:olo-intro-fine var(--oi-d) linear both}'
            . '.olo-intro__meta{position:absolute;left:0;right:0;height:50.5%;background:var(--oi-bg,var(--olo-color-background,#fff))}'
            . '.olo-intro__meta--su{top:0;animation:olo-intro-meta-su var(--oi-d) ' . $esc . ' both}'
            . '.olo-intro__meta--giu{bottom:0;animation:olo-intro-meta-giu var(--oi-d) ' . $esc . ' both}'
            . '.olo-intro__in{position:relative;display:flex;flex-direction:column;align-items:center;gap:18px;padding:0 24px;text-align:center;animation:olo-intro-via var(--oi-d) ease both}'
            . '.olo-intro__nome{display:block;overflow:hidden;padding:.08em 0;font-family:var(--olo-font-family-heading,inherit);font-weight:var(--olo-font-weight-heading,400);font-size:clamp(40px,7.5vw,112px);line-height:1;letter-spacing:-.02em}'
            . '.olo-intro__nome>span{display:block;animation:olo-intro-riga var(--oi-d) ' . $in . ' both}'
            . '.olo-intro__riga{font-family:var(--olo-font-family,inherit);font-size:12px;letter-spacing:.24em;text-transform:uppercase;opacity:.7;animation:olo-intro-didascalia var(--oi-d) ease both}'
            . '.olo-intro__linea{display:block;width:min(56vw,480px);height:1px;background:currentColor;transform-origin:left center;animation:olo-intro-traccia var(--oi-d) ' . $esc . ' both}'
            . '.olo-intro--contatore .olo-intro__in{position:absolute;left:clamp(20px,4vw,56px);top:clamp(20px,4vw,48px);align-items:flex-start;padding:0;text-align:left;gap:10px}'
            . '.olo-intro--contatore .olo-intro__nome{font-size:clamp(20px,2.2vw,30px);letter-spacing:0}'
            . '.olo-intro__conta{position:absolute;right:clamp(20px,4vw,56px);bottom:clamp(28px,5vw,64px);font-family:var(--olo-font-family-heading,inherit);font-weight:var(--olo-font-weight-heading,400);font-size:clamp(72px,15vw,220px);line-height:.8;font-variant-numeric:tabular-nums;letter-spacing:-.04em;counter-reset:oi var(--oi-n);animation:olo-intro-conta var(--oi-d) ' . $in . ' both}'
            . '.olo-intro__conta::after{content:counter(oi)}'
            . '.olo-intro__barra{position:absolute;left:0;bottom:0;width:100%;height:2px;background:currentColor;transform-origin:left center;animation:olo-intro-barra var(--oi-d) ' . $in . ' both}'
            . '.olo-intro--via{opacity:0;pointer-events:none}'
            . '@keyframes olo-intro-su{0%,70%{transform:translateY(0)}100%{transform:translateY(-100%);visibility:hidden}}'
            . '@keyframes olo-intro-fine{0%,99%{visibility:visible}100%{visibility:hidden}}'
            . '@keyframes olo-intro-meta-su{0%,68%{transform:translateY(0)}100%{transform:translateY(-100%)}}'
            . '@keyframes olo-intro-meta-giu{0%,68%{transform:translateY(0)}100%{transform:translateY(100%)}}'
            . '@keyframes olo-intro-riga{0%{transform:translateY(105%)}32%,58%{transform:translateY(0)}72%,100%{transform:translateY(-105%)}}'
            . '@keyframes olo-intro-didascalia{0%,20%{opacity:0}38%,58%{opacity:.7}70%,100%{opacity:0}}'
            . '@keyframes olo-intro-traccia{0%,14%{transform:scaleX(0)}48%,62%{transform:scaleX(1);opacity:1}72%,100%{transform:scaleX(1);opacity:0}}'
            . '@keyframes olo-intro-via{0%,64%{opacity:1}74%,100%{opacity:0}}'
            . '@keyframes olo-intro-conta{0%{--oi-n:0;opacity:1}62%{--oi-n:100;opacity:1}72%,100%{--oi-n:100;opacity:0}}'
            . '@keyframes olo-intro-barra{0%{transform:scaleX(0)}62%{transform:scaleX(1);opacity:1}72%,100%{transform:scaleX(1);opacity:0}}'
            . '@media (prefers-reduced-motion:reduce){.olo-intro{display:none}}';
    }

    /**
     * Una volta per sessione (sessionStorage, una chiave per scritta e stile), clic o tasto per
     * saltare, nodo tolto alla fine. Gira subito dopo il velo, prima che la pagina si veda.
     */
    private static function script( $una_volta, $durata, $chiave ) {
        return '(function(){var v=document.getElementById("olo-intro");if(!v){return;}'
            . 'function togli(){if(v.parentNode){v.parentNode.removeChild(v);}var c=document.getElementById("olo-intro-css");if(c){if(c.parentNode){c.parentNode.removeChild(c);}}}'
            . ( $una_volta
                ? 'try{if(sessionStorage.getItem("olo-intro-' . $chiave . '")){togli();return;}sessionStorage.setItem("olo-intro-' . $chiave . '","1");}catch(e){}'
                : '' )
            . 'function salta(){v.classList.add("olo-intro--via");setTimeout(togli,500);document.removeEventListener("keydown",salta);}'
            . 'v.addEventListener("click",salta);document.addEventListener("keydown",salta);'
            . 'setTimeout(function(){document.removeEventListener("keydown",salta);togli();},' . ( (int) $durata + 150 ) . ');})();';
    }
}
