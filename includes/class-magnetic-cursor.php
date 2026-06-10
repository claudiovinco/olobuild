<?php
/**
 * Olo_Magnetic_Cursor — cursore custom neon (anello + dot) con "pull" magnetico.
 *
 * NON è un tile in pagina: è una FEATURE GLOBALE DI TEMA/HEADER (bucket C, scheda §4
 * famiglia A del doc tile-speciali). Sostituisce il cursore di sistema con un anello +
 * dot al neon; sugli elementi che matchano `magnetic_selector` l'anello si ingrandisce e
 * l'elemento viene "tirato" verso il puntatore.
 *
 * Reference funzionante: handoff-tile-speciali/temi/60-tema-community-gamer.html
 * (blocco <script> "magnetic neon cursor"). Qui lo snippet è reso parametrico, scoped per
 * istanza (UID), SSR-safe e conforme al contratto §2:
 *   - ogni numero/colore/selettore = impostazione con default (niente hardcode);
 *   - markup + CSS + @keyframes prefissati con un UID per-istanza (N emissioni non si calpestano);
 *   - runtime INLINE idempotente (guard su window flag), multi-init safe;
 *   - prefers-reduced-motion → cursore statico (nessun easing rAF, nessun pull);
 *   - (hover:none)/(pointer:coarse) → cursore di sistema, nessun pull, niente `cursor:none`;
 *   - il focus da tastiera NON viene MAI nascosto (cursor:none solo sul movimento mouse/pen);
 *   - Pointer Events; will-change mirato; rAF singolo con auto-stop a riposo.
 *
 * Storage: option `olo_magnetic_cursor` (array). Chiavi additive: non rompono nulla.
 *
 * ── WIRING (lo fa il coordinatore, NON questo file) ───────────────────────────────────
 *   require_once OLO_PATH . 'includes/class-magnetic-cursor.php';
 *   Olo_Magnetic_Cursor::init();
 * Le impostazioni vanno esposte nel pannello "Configurazione" (tab tema/header) leggendo
 * Olo_Magnetic_Cursor::get_settings() e salvando in `olo_magnetic_cursor` via
 * Olo_Magnetic_Cursor::sanitize(). Il banner frontend funziona già senza UI: appena
 * `enabled` è true, il runtime viene emesso nel footer.
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Magnetic_Cursor {

    /** Option key (wp_options). */
    const OPT = 'olo_magnetic_cursor';

    /** Blend mode ammessi per l'anello (whitelist anti-injection). */
    const BLEND_MODES = [ 'normal', 'screen', 'difference', 'exclusion', 'overlay', 'lighten', 'multiply' ];

    /**
     * Registra gli hook frontend. Idempotente: safe se chiamato più volte.
     */
    public static function init() {
        static $done = false;
        if ( $done ) {
            return;
        }
        $done = true;

        // Pagina impostazioni admin nativa (WP Settings API): registrata SEMPRE — anche se
        // disabilitato — così la si può attivare dall'admin senza dipendere dalla Vue app.
        if ( is_admin() ) {
            add_action( 'admin_menu', [ __CLASS__, 'admin_menu' ] );
            add_action( 'admin_init', [ __CLASS__, 'admin_register' ] );
        }

        $opts = self::get_settings();
        if ( empty( $opts['enabled'] ) ) {
            return;
        }

        // Non emettere dentro l'iframe di editing del builder (cursore di sistema in canvas).
        if ( defined( 'OLO_BUILDER_PREVIEW' ) && OLO_BUILDER_PREVIEW ) {
            return;
        }

        add_action( 'wp_footer', [ __CLASS__, 'render_frontend' ], 60 );
    }

    /* ═══════════════════════════════════════════════════
     * SETTINGS
     * ═══════════════════════════════════════════════════ */

    /**
     * Default + merge con i valori salvati. Ogni parametro del demo è qui un campo.
     *
     * @return array
     */
    public static function get_settings() {
        $defaults = [
            'enabled'           => false,        // master switch
            'ring_size'         => 38,           // px — diametro anello a riposo
            'ring_color'        => '#22D3EE',    // colore bordo anello
            'ring_width'        => 1.5,          // px — spessore bordo anello
            'dot_size'          => 6,            // px — diametro dot centrale
            'dot_color'         => '#B6FF3D',    // colore dot
            'hot_scale'         => 1.6,          // moltiplicatore anello su elemento magnetico
            'hot_color'         => '#B6FF3D',    // colore anello quando "hot" (vuoto = ring_color)
            'hot_fill'          => 'rgba(139,92,246,.18)', // fill anello "hot" (vuoto = trasparente)
            'blend_mode'        => 'screen',     // mix-blend-mode anello
            'magnetic_selector' => 'button, a.btn', // chi attrae il cursore
            'pull_strength'     => 0.3,          // 0..1 — quanto l'elemento è tirato
            'follow_easing'     => 0.18,         // 0..1 — easing inseguimento anello (1 = istantaneo)
            'hide_system'       => true,         // cursor:none mentre il cursore custom è attivo
            'z_index'           => 100000,       // stacking del cursore
        ];

        $saved = get_option( self::OPT, [] );
        if ( ! is_array( $saved ) ) {
            $saved = [];
        }

        return wp_parse_args( $saved, $defaults );
    }

    /**
     * Sanitizza l'input (riusabile da una futura pagina admin / endpoint REST).
     *
     * @param array $input
     * @return array
     */
    public static function sanitize( $input ) {
        $d     = self::get_settings();
        $clean = [];

        $clean['enabled']     = ! empty( $input['enabled'] );
        $clean['hide_system'] = ! empty( $input['hide_system'] );

        // Interi con clamp
        $clean['ring_size'] = max( 8,  min( 200, intval( $input['ring_size'] ?? $d['ring_size'] ) ) );
        $clean['dot_size']  = max( 0,  min( 60,  intval( $input['dot_size'] ?? $d['dot_size'] ) ) );
        $clean['z_index']   = max( 1,  min( 2147483000, intval( $input['z_index'] ?? $d['z_index'] ) ) );

        // Float con clamp
        $clean['ring_width']    = max( 0,   min( 12, floatval( $input['ring_width'] ?? $d['ring_width'] ) ) );
        $clean['hot_scale']     = max( 1,   min( 5,  floatval( $input['hot_scale'] ?? $d['hot_scale'] ) ) );
        $clean['pull_strength'] = max( 0,   min( 1,  floatval( $input['pull_strength'] ?? $d['pull_strength'] ) ) );
        $clean['follow_easing'] = max( 0.02, min( 1, floatval( $input['follow_easing'] ?? $d['follow_easing'] ) ) );

        // Colori (color picker → esadecimale o rgba; sanitize_text_field tiene rgba()/var())
        foreach ( [ 'ring_color', 'dot_color', 'hot_color', 'hot_fill' ] as $ck ) {
            $clean[ $ck ] = sanitize_text_field( $input[ $ck ] ?? $d[ $ck ] );
        }

        // Blend mode whitelisted
        $bm = sanitize_text_field( $input['blend_mode'] ?? $d['blend_mode'] );
        $clean['blend_mode'] = in_array( $bm, self::BLEND_MODES, true ) ? $bm : 'screen';

        // Selettore: niente virgolette/angolari (evita di rompere querySelectorAll / HTML).
        // Stringa vuota = legittima: nessun pull magnetico (l'anello "hot" resta sui
        // soli elementi interattivi base).
        $sel = sanitize_text_field( $input['magnetic_selector'] ?? $d['magnetic_selector'] );
        $sel = str_replace( [ '"', "'", '<', '>' ], '', $sel );
        $clean['magnetic_selector'] = $sel;

        return $clean;
    }

    /* ═══════════════════════════════════════════════════
     * FRONTEND
     * ═══════════════════════════════════════════════════ */

    /**
     * Emette CSS + runtime inline. SSR-safe: senza JS resta il cursore di sistema
     * (i nodi del cursore custom sono creati dal runtime, non stampati lato server),
     * quindi nessuna regressione su no-JS / stampa / screen reader.
     */
    public static function render_frontend() {
        $o = self::get_settings();

        // UID per-istanza: prefissa classi, id e @keyframes. Se per qualunque motivo
        // il footer emettesse due volte, le due copie non si calpestano.
        $uid = 'olo-magcur-' . wp_rand( 10000, 99999 );

        // Valori numerici → stringhe compatte e localizzazione-safe.
        $ring     = (float) $o['ring_size'];
        $ringW    = (float) $o['ring_width'];
        $dot      = (float) $o['dot_size'];
        $hotScale = (float) $o['hot_scale'];
        $pull     = (float) $o['pull_strength'];
        $ease     = (float) $o['follow_easing'];
        $zi       = (int) $o['z_index'];

        $ringColor = $o['ring_color'];
        $dotColor  = $o['dot_color'];
        $hotColor  = $o['hot_color'] !== '' ? $o['hot_color'] : $o['ring_color'];
        $hotFill   = $o['hot_fill'] !== '' ? $o['hot_fill'] : 'transparent';
        $blend     = $o['blend_mode'];
        $hideSys   = ! empty( $o['hide_system'] );

        $ringHot = $ring * $hotScale;

        // Config passata al runtime come JSON (numeri/selettore → niente concatenazioni fragili).
        $cfg = [
            'ring'     => $ring,
            'ringHot'  => $ringHot,
            'pull'     => $pull,
            'ease'     => $ease,
            'sel'      => $o['magnetic_selector'],
            'hideSys'  => $hideSys,
        ];
        $cfg_json = wp_json_encode( $cfg );

        // CSS scoped sull'UID. Anello + dot sono position:fixed, pointer-events:none.
        // - (hover:none),(pointer:coarse): nascondi cursore custom, NON forzare cursor:none.
        // - prefers-reduced-motion: nessuna animazione di transizione sull'anello.
        ob_start();
        ?>
<style id="<?php echo esc_attr( $uid ); ?>-css">
.<?php echo $uid; ?>-ring,
.<?php echo $uid; ?>-dot{
  position:fixed;top:0;left:0;
  z-index:<?php echo $zi; ?>;
  pointer-events:none;
  border-radius:50%;
  will-change:transform;
  contain:layout style;
}
.<?php echo $uid; ?>-ring{
  width:<?php echo $ring; ?>px;height:<?php echo $ring; ?>px;
  margin:<?php echo ( -$ring / 2 ); ?>px 0 0 <?php echo ( -$ring / 2 ); ?>px;
  border:<?php echo $ringW; ?>px solid <?php echo esc_attr( $ringColor ); ?>;
  background:transparent;
  mix-blend-mode:<?php echo esc_attr( $blend ); ?>;
  transition:width .18s ease,height .18s ease,margin .18s ease,background .18s ease,border-color .18s ease;
}
.<?php echo $uid; ?>-dot{
  width:<?php echo $dot; ?>px;height:<?php echo $dot; ?>px;
  margin:<?php echo ( -$dot / 2 ); ?>px 0 0 <?php echo ( -$dot / 2 ); ?>px;
  background:<?php echo esc_attr( $dotColor ); ?>;
}
.<?php echo $uid; ?>-ring.is-hot{
  width:<?php echo $ringHot; ?>px;height:<?php echo $ringHot; ?>px;
  margin:<?php echo ( -$ringHot / 2 ); ?>px 0 0 <?php echo ( -$ringHot / 2 ); ?>px;
  border-color:<?php echo esc_attr( $hotColor ); ?>;
  background:<?php echo esc_attr( $hotFill ); ?>;
}
<?php if ( $hideSys ) : ?>
/* cursor:none SOLO quando il cursore custom è attivo (flag sul <html>); mai su touch.
   Non tocca :focus-visible: la navigazione da tastiera resta sempre visibile. */
html.<?php echo $uid; ?>-on,
html.<?php echo $uid; ?>-on a,
html.<?php echo $uid; ?>-on button,
html.<?php echo $uid; ?>-on [role="button"]{cursor:none;}
<?php endif; ?>
@media (hover:none),(pointer:coarse){
  .<?php echo $uid; ?>-ring,.<?php echo $uid; ?>-dot{display:none!important;}
  html.<?php echo $uid; ?>-on,
  html.<?php echo $uid; ?>-on a,
  html.<?php echo $uid; ?>-on button,
  html.<?php echo $uid; ?>-on [role="button"]{cursor:auto;}
}
@media (prefers-reduced-motion:reduce){
  .<?php echo $uid; ?>-ring{transition:none;}
}
</style>
<script>
(function(){
  /* ===== <?php echo $uid; ?> — magnetic neon cursor (idempotente, multi-init safe) ===== */
  var CFG = <?php echo $cfg_json; ?>;
  var UID = <?php echo wp_json_encode( $uid ); ?>;

  /* Off su touch/coarse: cursore di sistema, nessun pull. Fallback no-JS già coperto
     (i nodi non esistono finché non li creiamo qui). */
  var coarse = window.matchMedia && (window.matchMedia('(hover:none)').matches || window.matchMedia('(pointer:coarse)').matches);
  if (coarse) return;

  /* Guard idempotenza: una sola istanza attiva per pagina, anche se il footer
     emettesse due volte o lo script venisse rieseguito. */
  if (window.__oloMagCur && window.__oloMagCur.active) return;
  window.__oloMagCur = { active: true, uid: UID };

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var html = document.documentElement;
  var ring = document.createElement('div'); ring.className = UID + '-ring'; ring.setAttribute('aria-hidden','true');
  var dot  = document.createElement('div'); dot.className  = UID + '-dot';  dot.setAttribute('aria-hidden','true');

  function mount(){
    if (!document.body) { return; }
    document.body.appendChild(ring);
    document.body.appendChild(dot);
    if (CFG.hideSys) html.classList.add(UID + '-on');
  }
  if (document.body) { mount(); } else { document.addEventListener('DOMContentLoaded', mount); }

  var mx = window.innerWidth/2, my = window.innerHeight/2;   // target (dot, immediato)
  var cx = mx, cy = my;                                       // posizione anello (easing)
  var running = false;

  function place(el,x,y){ el.style.transform = 'translate(' + x + 'px,' + y + 'px)'; }

  /* rAF singolo con auto-stop quando l'anello ha raggiunto il target (no loop a vuoto). */
  function loop(){
    cx += (mx - cx) * CFG.ease;
    cy += (my - cy) * CFG.ease;
    place(ring, cx, cy);
    if (Math.abs(mx - cx) > 0.1 || Math.abs(my - cy) > 0.1) {
      requestAnimationFrame(loop);
    } else {
      running = false;
    }
  }
  function kick(){ if (!running){ running = true; requestAnimationFrame(loop); } }

  window.addEventListener('pointermove', function(e){
    if (e.pointerType === 'touch') return;     // penna/mouse sì, dito no
    mx = e.clientX; my = e.clientY;
    place(dot, mx, my);                         // dot segue 1:1
    if (reduce) { place(ring, mx, my); }        // reduced-motion: anello senza easing
    else { kick(); }
  }, { passive: true });

  /* Stato "hot": anello ingrandito su qualsiasi elemento interattivo. Delegato → regge
     anche nodi aggiunti dopo (no bind per-elemento che si perde su contenuto dinamico). */
  var HOT_SEL = 'a, button, [role="button"], input, select, textarea, label, summary' + (CFG.sel ? ', ' + CFG.sel : '');
  function isHot(t){ return t && t.closest && t.closest(HOT_SEL); }
  document.addEventListener('pointerover', function(e){ if (isHot(e.target)) ring.classList.add('is-hot'); }, { passive: true });
  document.addEventListener('pointerout',  function(e){ if (isHot(e.target)) ring.classList.remove('is-hot'); }, { passive: true });

  /* Pull magnetico sugli elementi che matchano il selettore. Delegato, con cleanup
     quando il puntatore lascia il target. Reduced-motion → niente pull. */
  if (!reduce && CFG.pull > 0 && CFG.sel) {
    var pulled = null;
    function clearPull(){ if (pulled){ pulled.style.transform = ''; pulled.style.willChange = ''; pulled = null; } }
    document.addEventListener('pointermove', function(e){
      if (e.pointerType === 'touch') return;
      var el = e.target && e.target.closest ? e.target.closest(CFG.sel) : null;
      if (!el){ clearPull(); return; }
      if (pulled && pulled !== el) clearPull();
      pulled = el; el.style.willChange = 'transform';
      var r = el.getBoundingClientRect();
      var dx = (e.clientX - r.left - r.width/2)  * CFG.pull;
      var dy = (e.clientY - r.top  - r.height/2) * CFG.pull;
      el.style.transform = 'translate(' + dx + 'px,' + dy + 'px)';
    }, { passive: true });
    document.addEventListener('pointerout', function(e){
      var el = e.target && e.target.closest ? e.target.closest(CFG.sel) : null;
      if (el && el === pulled) clearPull();
    }, { passive: true });
    window.addEventListener('blur', clearPull);
  }

  /* Nascondi il cursore custom quando il puntatore esce dalla finestra (estetica). */
  document.addEventListener('mouseleave', function(){ ring.style.opacity = '0'; dot.style.opacity = '0'; });
  document.addEventListener('mouseenter', function(){ ring.style.opacity = '';  dot.style.opacity = '';  });
})();
</script>
        <?php
        // Output raw: CSS/JS generati interamente da valori sanitizzati sopra.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline asset, valori già sanitizzati/escaped a monte
        echo ob_get_clean();
    }

    /* ═══════════════════════════════════════════════════
     * ADMIN UI — WP Settings API nativa (non tocca la Vue app del builder)
     * ═══════════════════════════════════════════════════ */

    public static function admin_menu() {
        add_options_page(
            __( 'Cursore magnetico', 'olobuild' ),
            __( 'OLO Cursore magnetico', 'olobuild' ),
            'manage_options',
            'olo-magnetic-cursor',
            [ __CLASS__, 'render_settings_page' ]
        );
    }

    public static function admin_register() {
        register_setting( 'olo_magnetic_cursor_group', self::OPT, [ __CLASS__, 'sanitize' ] );
    }

    public static function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $o   = self::get_settings();
        $opt = esc_attr( self::OPT );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Cursore magnetico (MagneticCursor)', 'olobuild' ); ?></h1>
            <p style="max-width:640px">
                <?php esc_html_e( 'Cursore custom al neon (anello + dot) con "pull" magnetico sugli elementi interattivi. Si disattiva automaticamente su touch / (pointer:coarse) e con prefers-reduced-motion; non nasconde mai il focus da tastiera.', 'olobuild' ); ?>
            </p>
            <form method="post" action="options.php">
                <?php settings_fields( 'olo_magnetic_cursor_group' ); ?>
                <table class="form-table" role="presentation">
                    <tr><th scope="row"><?php esc_html_e( 'Attivo', 'olobuild' ); ?></th>
                        <td><label><input type="checkbox" name="<?php echo $opt; ?>[enabled]" value="1" <?php checked( ! empty( $o['enabled'] ) ); ?>> <?php esc_html_e( 'Abilita il cursore magnetico sul frontend', 'olobuild' ); ?></label></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Dimensione anello (px)', 'olobuild' ); ?></th>
                        <td><input type="number" min="8" max="200" name="<?php echo $opt; ?>[ring_size]" value="<?php echo esc_attr( $o['ring_size'] ); ?>"></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Spessore anello (px)', 'olobuild' ); ?></th>
                        <td><input type="number" step="0.5" min="0" max="12" name="<?php echo $opt; ?>[ring_width]" value="<?php echo esc_attr( $o['ring_width'] ); ?>"></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Colore anello', 'olobuild' ); ?></th>
                        <td><input type="text" name="<?php echo $opt; ?>[ring_color]" value="<?php echo esc_attr( $o['ring_color'] ); ?>" class="regular-text" placeholder="#22D3EE"></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Colore dot', 'olobuild' ); ?></th>
                        <td><input type="text" name="<?php echo $opt; ?>[dot_color]" value="<?php echo esc_attr( $o['dot_color'] ); ?>" class="regular-text" placeholder="#B6FF3D"></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Blend mode anello', 'olobuild' ); ?></th>
                        <td><select name="<?php echo $opt; ?>[blend_mode]"><?php foreach ( self::BLEND_MODES as $bm ) { echo '<option value="' . esc_attr( $bm ) . '" ' . selected( $o['blend_mode'], $bm, false ) . '>' . esc_html( $bm ) . '</option>'; } ?></select></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Selettore magnetico', 'olobuild' ); ?></th>
                        <td><input type="text" name="<?php echo $opt; ?>[magnetic_selector]" value="<?php echo esc_attr( $o['magnetic_selector'] ); ?>" class="regular-text" placeholder="button, a.btn">
                            <p class="description"><?php esc_html_e( 'CSS selector degli elementi "tirati" verso il cursore.', 'olobuild' ); ?></p></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Forza pull (0–1)', 'olobuild' ); ?></th>
                        <td><input type="number" step="0.05" min="0" max="1" name="<?php echo $opt; ?>[pull_strength]" value="<?php echo esc_attr( $o['pull_strength'] ); ?>"></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Inseguimento (0–1)', 'olobuild' ); ?></th>
                        <td><input type="number" step="0.02" min="0.02" max="1" name="<?php echo $opt; ?>[follow_easing]" value="<?php echo esc_attr( $o['follow_easing'] ); ?>"></td></tr>
                    <tr><th scope="row"><?php esc_html_e( 'Nascondi cursore di sistema', 'olobuild' ); ?></th>
                        <td><label><input type="checkbox" name="<?php echo $opt; ?>[hide_system]" value="1" <?php checked( ! empty( $o['hide_system'] ) ); ?>> <?php esc_html_e( 'cursor:none mentre il cursore custom è attivo (mai su touch)', 'olobuild' ); ?></label></td></tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
