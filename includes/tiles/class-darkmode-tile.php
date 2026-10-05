<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Darkmode_Tile extends Olobuild_Tile_Base {

    protected $type     = 'darkmode';
    protected $name     = 'Dark Mode Toggle';
    protected $icon     = 'dashicons-admin-appearance';
    protected $category = 'interactive';
    protected $defaults = [
        'style'               => 'toggle',
        'light_icon'          => 'sun',
        'dark_icon'           => 'moon',
        'icon_size'           => 24,
        'button_text_light'   => 'Modalità scura',
        'button_text_dark'    => 'Modalità chiara',
        // Vuoti = token (render): testo del sito in chiaro, «warning» (oro) in scuro.
        'toggle_color'        => '',
        'toggle_active_color' => '',
        'save_preference'     => true,
        'respect_system'      => true,
        'transition_duration' => 300,
    ];

    /**
     * Segnale «il sito usa la tile» per lo script in testa: [ 'sistema' => 0|1, 'visto' => timestamp ].
     * Opzione in autoload: dopo la prima scrittura il wp_head non fa query.
     */
    const OPZIONE = 'olobuild_darkmode_tile';

    /** Il hook wp_head è registrato una sola volta per richiesta. */
    private static $hook_registrato = false;

    /** Il segnale si scrive al più una volta per richiesta (la prima tile resa decide). */
    private static $segnale_visto = false;

    /**
     * La tile viene istanziata a plugins_loaded (registrazione delle tile), quindi PRIMA di
     * wp_head in qualunque tema. Da qui si aggancia lo script che riapplica la preferenza:
     * agganciarlo dentro render() funzionava solo nei temi a blocchi, dove il contenuto si
     * rende prima di wp_head; nei temi classici al ricaricamento si tornava in chiaro.
     */
    public function __construct() {
        if ( ! self::$hook_registrato ) {
            self::$hook_registrato = true;
            add_action( 'wp_head', [ __CLASS__, 'stampa_script_testa' ], 1 );
        }
    }

    public function get_controls() {
        return [];
    }

    /**
     * Script minimo in testa (wp_head, priorità 1): riapplica la preferenza salvata o il tema
     * di sistema PRIMA che la pagina si disegni, quindi senza lampeggio.
     *
     * Si stampa solo se il sito usa la tile: render() aggiorna l'opzione al più una volta al
     * giorno, e se per 30 giorni nessuna pagina con la tile viene resa lo script si spegne da
     * solo (tile tolta dal sito). Non si stampa nel canvas del builder: lì la pagina resta in
     * chiaro e la preferenza dell'autore come visitatore non deve colorarla.
     * Niente && nello script (WordPress lo trasformerebbe in &#038;): if annidati.
     */
    public static function stampa_script_testa() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura del flag di routing dell'iframe del builder; nessuna modifica di stato.
        if ( ! empty( $_GET['olo_builder_iframe'] ) ) {
            return;
        }
        $segnale = get_option( self::OPZIONE );
        if ( ! is_array( $segnale ) || empty( $segnale['visto'] ) ) {
            return;
        }
        if ( time() - (int) $segnale['visto'] > 30 * DAY_IN_SECONDS ) {
            return;
        }
        $sistema = '';
        if ( ! empty( $segnale['sistema'] ) ) {
            $sistema = 'else if(v!=="light"){if(window.matchMedia){if(window.matchMedia("(prefers-color-scheme: dark)").matches){h.classList.add("olo-dark-mode");}}}';
        }
        echo '<script id="olo-darkmode-init">(function(){var h=document.documentElement,v=null;try{v=localStorage.getItem("olo-dark-mode");}catch(e){}if(v==="dark"){h.classList.add("olo-dark-mode");}' . $sistema . '})();</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- script statico: l'unica parte variabile è uno di due letterali fissi scelti sopra.
    }

    /**
     * Aggiorna il segnale letto da stampa_script_testa(). Solo dalle viste del sito (mai dal
     * canvas, dall'admin, da REST o AJAX): una tile appena trascinata nel builder e poi tolta
     * non deve accendere la modalità scura di sistema su un sito senza interruttore.
     * Scrive se il segnale manca, se è di più di un giorno fa, o se «Rispetta tema di sistema»
     * è cambiato (non prima di un minuto dall'ultima scrittura: due tile con impostazioni
     * diverse non riscrivono l'opzione a ogni vista).
     */
    private function segnala_uso( $respect ) {
        if ( self::$segnale_visto ) {
            return;
        }
        self::$segnale_visto = true;
        if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
            return;
        }
        $ora     = time();
        $sistema = $respect ? 1 : 0;
        $attuale = get_option( self::OPZIONE );
        $visto   = is_array( $attuale ) ? (int) ( $attuale['visto'] ?? 0 ) : 0;
        $diverso = ! is_array( $attuale ) || (int) ( $attuale['sistema'] ?? -1 ) !== $sistema;
        $eta     = $ora - $visto;
        if ( $eta > DAY_IN_SECONDS || ( $diverso && $eta > MINUTE_IN_SECONDS ) ) {
            update_option( self::OPZIONE, [ 'sistema' => $sistema, 'visto' => $ora ], true );
        }
    }

    /**
     * L'icona scelta nell'inspector (type:'icon') alla misura $px. «sun» e «moon», i
     * predefiniti, restano i disegni storici della tile: chi non le ha cambiate vede la stessa
     * resa. Le altre passano da render_icon_html() come in tutte le tile (UIkit, Lucide, custom).
     * Vuota = nessuna icona, salvo dove l'icona È il controllo ($obbligatoria: stile «Icona singola»).
     */
    private function icona( $nome, $predefinita, $px, $obbligatoria ) {
        $nome = trim( (string) $nome );
        if ( $nome === '' ) {
            if ( ! $obbligatoria ) {
                return '';
            }
            $nome = $predefinita;
        }
        $px  = max( 8, (int) $px );
        $svg = '<svg width="' . $px . '" height="' . $px . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';
        if ( $nome === 'sun' ) {
            return $svg . '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
        }
        if ( $nome === 'moon' ) {
            return $svg . '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
        }
        return $this->render_icon_html( $nome, round( $px / 20, 2 ), 'aria-hidden="true"' );
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        $uid       = 'olo-dm-' . wp_rand( 10000, 99999 );
        $style     = in_array( $s['style'], [ 'toggle', 'icon', 'button' ], true ) ? $s['style'] : 'toggle';
        $icon_size = max( 16, intval( $s['icon_size'] ) );
        $color     = $this->safe_color_css( $s['toggle_color'] ) ?: 'var(--olo-color-text, #333333)';
        $active    = $this->safe_color_css( $s['toggle_active_color'] ) ?: 'var(--olo-color-warning, #ffd700)';
        $duration  = max( 0, intval( $s['transition_duration'] ) );
        $save_pref = ! empty( $s['save_preference'] );
        $respect   = ! empty( $s['respect_system'] );
        // Canvas del builder: sia la tile in modifica (_builder_mode) sia una tile dell'header o
        // del footer resa nell'iframe dell'anteprima. Lì si mostra lo stato e basta.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura del flag di routing dell'iframe del builder; nessuna modifica di stato.
        $in_canvas = ! empty( $s['_builder_mode'] ) || ! empty( $_GET['olo_builder_iframe'] );

        if ( ! $in_canvas ) {
            $this->segnala_uso( $respect );
        }

        // Icone scelte nell'inspector (prima sole e luna erano cablate e la scelta non agiva).
        $thumb_size = max( 12, round( $icon_size * 0.6 ) );
        $icona_px   = $style === 'toggle' ? $thumb_size : $icon_size;
        $obbligata  = $style === 'icon';
        $icona_luce = $this->icona( $s['light_icon'], 'sun', $icona_px, $obbligata );
        $icona_buio = $this->icona( $s['dark_icon'], 'moon', $icona_px, $obbligata );
        $etichetta  = olobuild_t( 'Modalità scura' );

        ob_start();

        // Track dimensions for toggle
        $track_w    = max( 44, round( $icon_size * 2.2 ) );
        $track_h    = max( 24, round( $icon_size * 1.2 ) );
        $thumb_d    = $track_h - 6;
        $travel     = $track_w - $thumb_d - 6;
        // Pulsante «Icona singola»: l'area cliccabile e l'anello del focus abbracciano l'icona
        // (prima le icone erano assolute in un pulsante di soli 16px di padding).
        $icon_btn   = $icon_size + 16;

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: safe_color_css() whitelist colors (with token fallbacks), intval()/max()/round() integer dimensions and the internally generated $uid.
        ?>
        <style>
            <?php if ( ! $in_canvas ) : ?>html { transition: background-color <?php echo (int) $duration; ?>ms ease, color <?php echo (int) $duration; ?>ms ease; }<?php endif; ?>

            .<?php echo $uid; ?> { display: flex; align-items: center; justify-content: center; }

            /* Toggle style */
            .<?php echo $uid; ?> .olo-dm-track {
                display: inline-flex;
                align-items: center;
                width: <?php echo (int) $track_w; ?>px;
                height: <?php echo (int) $track_h; ?>px;
                border-radius: <?php echo (int) $track_h; ?>px;
                background: <?php echo $color; ?>;
                padding: 3px;
                cursor: pointer;
                transition: background <?php echo (int) $duration; ?>ms ease;
                border: none;
                outline: none;
                position: relative;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-track {
                background: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-thumb {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: <?php echo (int) $thumb_d; ?>px;
                height: <?php echo (int) $thumb_d; ?>px;
                border-radius: 50%;
                background: var(--olo-color-background, #ffffff);
                transform: translateX(0);
                transition: transform <?php echo (int) $duration; ?>ms ease;
                color: <?php echo $color; ?>;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-thumb {
                transform: translateX(<?php echo (int) $travel; ?>px);
                color: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-sun,
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-moon {
                display: inline-flex;
                transition: opacity <?php echo (int) $duration; ?>ms ease, transform <?php echo (int) $duration; ?>ms ease;
                position: absolute;
            }
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-sun {
                opacity: 1;
                transform: rotate(0deg);
            }
            .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-moon {
                opacity: 0;
                transform: rotate(-90deg);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-sun {
                opacity: 0;
                transform: rotate(90deg);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-thumb .olo-dm-icon-moon {
                opacity: 1;
                transform: rotate(0deg);
            }

            /* Icon style */
            .<?php echo $uid; ?> .olo-dm-icon-btn {
                position: relative;
                width: <?php echo (int) $icon_btn; ?>px;
                height: <?php echo (int) $icon_btn; ?>px;
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: <?php echo $color; ?>;
                transition: color <?php echo (int) $duration; ?>ms ease, transform <?php echo (int) $duration; ?>ms ease;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-icon-btn {
                color: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn:hover {
                transform: scale(1.1);
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-sun,
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-moon {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: opacity <?php echo (int) $duration; ?>ms ease, transform <?php echo (int) $duration; ?>ms ease;
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-sun {
                opacity: 1; transform: rotate(0deg) scale(1);
            }
            .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-moon {
                opacity: 0; transform: rotate(-90deg) scale(0.5);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-sun {
                opacity: 0; transform: rotate(90deg) scale(0.5);
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-icon-btn .olo-dm-icon-moon {
                opacity: 1; transform: rotate(0deg) scale(1);
            }

            /* Button style */
            .<?php echo $uid; ?> .olo-dm-button {
                display: inline-flex;
                align-items: center;
                padding: 10px 20px;
                border-radius: 8px;
                border: 2px solid <?php echo $color; ?>;
                background: transparent;
                color: <?php echo $color; ?>;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all <?php echo (int) $duration; ?>ms ease;
                font-family: inherit;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button {
                border-color: <?php echo $active; ?>;
                color: <?php echo $active; ?>;
            }
            .<?php echo $uid; ?> .olo-dm-button:hover {
                transform: scale(1.03);
            }
            .<?php echo $uid; ?> .olo-dm-track:focus-visible,
            .<?php echo $uid; ?> .olo-dm-icon-btn:focus-visible,
            .<?php echo $uid; ?> .olo-dm-button:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-sun,
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-moon {
                transition: opacity <?php echo (int) $duration; ?>ms ease, transform <?php echo (int) $duration; ?>ms ease;
                margin-right: 8px;
                flex-shrink: 0;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-sun {
                display: inline-flex;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-moon {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-sun {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-icon-moon {
                display: inline-flex;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-light {
                display: inline;
            }
            .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-dark {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-light {
                display: none;
            }
            html.olo-dark-mode .<?php echo $uid; ?> .olo-dm-button .olo-dm-text-dark {
                display: inline;
            }
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div class="olo-darkmode <?php echo esc_attr( $uid ); ?>" data-olo-darkmode="1">

            <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $icona_luce/$icona_buio: SVG statici costruiti in icona() da percorsi fissi e misure intere, oppure render_icon_html() (esc_attr / olobuild_sanitize_svg al suo interno). ?>
            <?php if ( $style === 'toggle' ) : ?>
                <?php // Interruttore: role="switch" + aria-checked, allineato dallo script allo stato reale. ?>
                <button type="button" class="olo-dm-track olo-dm-btn" role="switch" aria-checked="false" aria-label="<?php echo esc_attr( $etichetta ); ?>">
                    <span class="olo-dm-thumb" style="position:relative;" aria-hidden="true">
                        <span class="olo-dm-icon-sun"><?php echo $icona_luce; ?></span>
                        <span class="olo-dm-icon-moon"><?php echo $icona_buio; ?></span>
                    </span>
                </button>

            <?php elseif ( $style === 'icon' ) : ?>
                <button type="button" class="olo-dm-icon-btn olo-dm-btn" aria-pressed="false" aria-label="<?php echo esc_attr( $etichetta ); ?>">
                    <span class="olo-dm-icon-sun" aria-hidden="true"><?php echo $icona_luce; ?></span>
                    <span class="olo-dm-icon-moon" aria-hidden="true"><?php echo $icona_buio; ?></span>
                </button>

            <?php else : ?>
                <?php // Pulsante con testo: il testo visibile dice l'azione e cambia con lo stato, quindi è
                      // lui il nome accessibile (niente aria-pressed: con un nome che cambia sarebbe ambiguo). ?>
                <button type="button" class="olo-dm-button olo-dm-btn">
                    <?php if ( $icona_luce !== '' ) : ?><span class="olo-dm-icon-sun" aria-hidden="true"><?php echo $icona_luce; ?></span><?php endif; ?>
                    <?php if ( $icona_buio !== '' ) : ?><span class="olo-dm-icon-moon" aria-hidden="true"><?php echo $icona_buio; ?></span><?php endif; ?>
                    <span class="olo-dm-text-light"><?php echo esc_html( $s['button_text_light'] ); ?></span>
                    <span class="olo-dm-text-dark"><?php echo esc_html( $s['button_text_dark'] ); ?></span>
                </button>
            <?php endif; ?>
            <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>

        <script>
        (function(){
            var wrap = document.querySelector('.<?php echo esc_js( $uid ); ?>');
            if (!wrap) { return; }
            var btn = wrap.querySelector('.olo-dm-btn');
            if (!btn) { return; }
            var html = document.documentElement;

            function sincronizza() {
                var scuro = html.classList.contains('olo-dark-mode') ? 'true' : 'false';
                if (btn.getAttribute('role') === 'switch') {
                    btn.setAttribute('aria-checked', scuro);
                } else if (btn.hasAttribute('aria-pressed')) {
                    btn.setAttribute('aria-pressed', scuro);
                }
            }
            function annuncia() {
                try { document.dispatchEvent(new CustomEvent('olo:darkmode')); } catch (e) { sincronizza(); }
            }
            document.addEventListener('olo:darkmode', sincronizza);

            <?php if ( $in_canvas ) : ?>
            // Canvas del builder: solo lo stato visivo, la pagina resta in chiaro.
            sincronizza();
            return;
            <?php endif; ?>

            var salva = <?php echo $save_pref ? 'true' : 'false'; ?>;
            var sistema = <?php echo $respect ? 'true' : 'false'; ?>;
            var scelto = false;
            var mq = null;
            if (window.matchMedia) { mq = window.matchMedia('(prefers-color-scheme: dark)'); }

            function memorizzata() {
                var v = null;
                try { v = localStorage.getItem('olo-dark-mode'); } catch (e) {}
                return v;
            }
            function applica(scuro) {
                if (scuro) { html.classList.add('olo-dark-mode'); } else { html.classList.remove('olo-dark-mode'); }
            }

            // Riallineamento: lo script in testa può mancare (prima vista dopo l'inserimento della
            // tile, pagina servita da una cache precedente) o venire da un'altra tile.
            var v = memorizzata();
            if (v === 'dark') {
                applica(true);
            } else if (v === 'light') {
                applica(false);
            } else if (sistema) {
                applica(mq ? mq.matches : false);
            } else {
                applica(false);
            }
            annuncia();

            btn.addEventListener('click', function () {
                var scuro = !html.classList.contains('olo-dark-mode');
                scelto = true;
                applica(scuro);
                if (salva) {
                    try { localStorage.setItem('olo-dark-mode', scuro ? 'dark' : 'light'); } catch (e) {}
                }
                annuncia();
            });

            // Il tema di sistema cambia a pagina aperta: lo segue solo chi non ha ancora scelto.
            if (sistema) {
                if (mq) {
                    var segui = function (e) {
                        if (scelto) { return; }
                        if (memorizzata() !== null) { return; }
                        applica(e.matches);
                        annuncia();
                    };
                    if (mq.addEventListener) { mq.addEventListener('change', segui); } else if (mq.addListener) { mq.addListener(segui); }
                }
            }
        })();
        </script>
        <?php

        return ob_get_clean();
    }

}
