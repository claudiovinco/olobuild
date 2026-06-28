<?php
/**
 * Olobuild_Cursor_Hud — HUD "mirino da sala di regia": crosshair full-viewport che segue
 * il puntatore + tag con coordinate (X · Y assoluta) e label della sezione corrente.
 *
 * NON è un tile in pagina: è una FEATURE GLOBALE DI TEMA (stessa famiglia di
 * Olobuild_Magnetic_Cursor). Reference: blueprint "Clod — Evoluzione v2",
 * design-clod/…/clod/evo-fx.js [2] + evo-fx.css [2] (.fx-hud).
 *
 * Contratto §2 (tile-speciali):
 *   - ogni numero/colore/selettore = impostazione con default (niente hardcode);
 *   - markup + CSS prefissati con un UID per-istanza;
 *   - runtime INLINE idempotente (guard su window flag), multi-init safe;
 *   - prefers-reduced-motion / (pointer:coarse) → HUD non emesso o nascosto;
 *   - pointer-events:none ovunque: zero interferenza con l'interazione.
 *
 * La label di sezione viene letta da [data-screen-label] se presente, altrimenti
 * dall'id delle sezioni (`.olo-section[id], section[id]`) in maiuscolo — nel tema
 * le sezioni portano html_id (top/servizi/lavori/…) che fungono anche da anchor.
 *
 * Storage: option `olo_cursor_hud` (array). Chiavi additive: non rompono nulla.
 *
 * ── WIRING (coordinatore, NON questo file) ────────────────────────────────────
 *   require_once OLOBUILD_PATH . 'includes/class-cursor-hud.php';
 *   Olobuild_Cursor_Hud::init();
 * Feature dormiente: si attiva solo con option `enabled => true` (import tema,
 * REST o wp-cli).
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Cursor_Hud {

    /** Option key (wp_options). */
    const OPT = 'olo_cursor_hud';

    /**
     * Registra gli hook frontend. Idempotente: safe se chiamato più volte.
     */
    public static function init() {
        static $done = false;
        if ( $done ) {
            return;
        }
        $done = true;

        $opts = self::get_settings();
        if ( empty( $opts['enabled'] ) ) {
            return;
        }

        // Non emettere dentro l'iframe di editing del builder.
        if ( defined( 'OLOBUILD_BUILDER_PREVIEW' ) && OLOBUILD_BUILDER_PREVIEW ) {
            return;
        }

        add_action( 'wp_footer', [ __CLASS__, 'render_frontend' ], 61 );
    }

    /* ═══════════════════════════════════════════════════
     * SETTINGS
     * ═══════════════════════════════════════════════════ */

    /**
     * Default + merge con i valori salvati.
     *
     * @return array
     */
    public static function get_settings() {
        $defaults = [
            'enabled'          => false,                  // master switch
            'show_coords'      => true,                   // riga "0000 · 0000"
            'show_label'       => true,                   // riga sezione corrente
            'line_color'       => 'rgba(236,234,227,.09)',// crosshair h/v
            'coords_color'     => '#C6F24E',              // numeri (tabular-nums)
            'label_color'      => '#7c7e74',              // label sezione
            'font_size'        => 10,                     // px — tag mono
            'tag_offset'       => 16,                     // px — distanza tag dal puntatore
            'z_index'          => 75,                     // sotto il cursore custom (100000)
            'section_selector' => '[data-screen-label], .olo-section[id], section[id]',
            'empty_label'      => '—',                    // label quando nessuna sezione
        ];

        $saved = get_option( self::OPT, [] );
        if ( ! is_array( $saved ) ) {
            $saved = [];
        }

        return wp_parse_args( $saved, $defaults );
    }

    /**
     * Sanitizza l'input (riusabile da importer / endpoint REST / wp-cli).
     *
     * @param array $input
     * @return array
     */
    public static function sanitize( $input ) {
        $d     = self::get_settings();
        $clean = [];

        $clean['enabled']     = ! empty( $input['enabled'] );
        $clean['show_coords'] = array_key_exists( 'show_coords', (array) $input ) ? ! empty( $input['show_coords'] ) : $d['show_coords'];
        $clean['show_label']  = array_key_exists( 'show_label', (array) $input ) ? ! empty( $input['show_label'] ) : $d['show_label'];

        $clean['font_size']  = max( 7, min( 24, intval( $input['font_size'] ?? $d['font_size'] ) ) );
        $clean['tag_offset'] = max( 0, min( 80, intval( $input['tag_offset'] ?? $d['tag_offset'] ) ) );
        $clean['z_index']    = max( 1, min( 2147483000, intval( $input['z_index'] ?? $d['z_index'] ) ) );

        foreach ( [ 'line_color', 'coords_color', 'label_color' ] as $ck ) {
            $clean[ $ck ] = sanitize_text_field( $input[ $ck ] ?? $d[ $ck ] );
        }

        // Selettore: niente virgolette/angolari (non rompere querySelectorAll / HTML).
        $sel = sanitize_text_field( $input['section_selector'] ?? $d['section_selector'] );
        $sel = str_replace( [ '"', "'", '<', '>' ], '', $sel );
        $clean['section_selector'] = $sel;

        $clean['empty_label'] = sanitize_text_field( $input['empty_label'] ?? $d['empty_label'] );

        return $clean;
    }

    /* ═══════════════════════════════════════════════════
     * FRONTEND
     * ═══════════════════════════════════════════════════ */

    /**
     * Emette CSS + runtime inline. SSR-safe: i nodi dell'HUD sono creati dal runtime,
     * non stampati lato server — senza JS non resta nulla in pagina.
     */
    public static function render_frontend() {
        $o = self::get_settings();

        $uid = 'olo-hud-' . wp_rand( 10000, 99999 );

        $fs     = (int) $o['font_size'];
        $off    = (int) $o['tag_offset'];
        $zi     = (int) $o['z_index'];
        $line   = $o['line_color'];
        $coords = $o['coords_color'];
        $label  = $o['label_color'];

        $cfg = [
            'showCoords' => ! empty( $o['show_coords'] ),
            'showLabel'  => ! empty( $o['show_label'] ),
            'sel'        => $o['section_selector'],
            'empty'      => $o['empty_label'],
        ];
        $cfg_json = wp_json_encode( $cfg );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS/JS: numerici clampati con cast (int), colori esc_attr()'d, config via wp_json_encode(); $uid interno.
        ?>
<style id="<?php echo esc_attr( $uid ); ?>-css">
.<?php echo $uid; ?>{position:fixed;inset:0;z-index:<?php echo $zi; ?>;pointer-events:none;opacity:0;transition:opacity .35s;}
.<?php echo $uid; ?>.on{opacity:1;}
.<?php echo $uid; ?> .h,.<?php echo $uid; ?> .v{position:absolute;background:<?php echo esc_attr( $line ); ?>;}
.<?php echo $uid; ?> .h{left:0;right:0;height:1px;top:var(--my,50vh);}
.<?php echo $uid; ?> .v{top:0;bottom:0;width:1px;left:var(--mx,50vw);}
.<?php echo $uid; ?> .tag{position:absolute;left:var(--mx,50vw);top:var(--my,50vh);transform:translate(<?php echo $off; ?>px,<?php echo $off; ?>px);
  display:flex;flex-direction:column;gap:3px;font-family:'Space Mono','SFMono-Regular',Consolas,monospace;font-size:<?php echo $fs; ?>px;
  letter-spacing:.1em;text-transform:uppercase;white-space:nowrap;}
.<?php echo $uid; ?> .tag b{color:<?php echo esc_attr( $coords ); ?>;font-weight:700;font-variant-numeric:tabular-nums;}
.<?php echo $uid; ?> .tag em{font-style:normal;color:<?php echo esc_attr( $label ); ?>;}
@media(pointer:coarse){.<?php echo $uid; ?>{display:none;}}
</style>
<script>
(function(){
  /* ===== <?php echo $uid; ?> — cursor HUD mirino (idempotente, multi-init safe) ===== */
  var CFG = <?php echo $cfg_json; ?>;
  var UID = <?php echo wp_json_encode( $uid ); ?>;

  if (!(window.matchMedia && window.matchMedia('(pointer: fine)').matches)) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (window.__oloHud && window.__oloHud.active) return;
  window.__oloHud = { active: true, uid: UID };

  function pad4(n){ n = Math.max(0, Math.round(n)); var s = String(n); while (s.length < 4) s = '0' + s; return s; }

  var hud = document.createElement('div');
  hud.className = UID;
  hud.setAttribute('aria-hidden', 'true');
  hud.innerHTML = '<i class="h"></i><i class="v"></i><span class="tag">' +
    (CFG.showCoords ? '<b>0000 · 0000</b>' : '') +
    (CFG.showLabel ? '<em>' + CFG.empty.replace(/&/g,'&amp;').replace(/</g,'&lt;') + '</em>' : '') +
    '</span>';
  function mount(){ if (document.body) document.body.appendChild(hud); }
  if (document.body) { mount(); } else { document.addEventListener('DOMContentLoaded', mount); }

  var tagB = hud.querySelector('b'), tagE = hud.querySelector('em');
  var mx = 0, my = 0, tk = false;

  function sections(){
    try { return [].slice.call(document.querySelectorAll(CFG.sel)); }
    catch (e) { return []; }
  }
  function labelOf(el){
    var l = el.getAttribute('data-screen-label');
    if (l) return l;
    return (el.id || '').toUpperCase();
  }
  function upd(){
    tk = false;
    hud.style.setProperty('--mx', mx + 'px');
    hud.style.setProperty('--my', my + 'px');
    if (tagB) tagB.textContent = pad4(mx) + ' · ' + pad4(my + (window.scrollY || 0));
    if (tagE) {
      var lab = CFG.empty, secs = sections();
      for (var i = 0; i < secs.length; i++) {
        var r = secs[i].getBoundingClientRect();
        if (my >= r.top && my <= r.bottom) { lab = labelOf(secs[i]); break; }
      }
      tagE.textContent = lab;
    }
  }
  window.addEventListener('pointermove', function(e){
    if (e.pointerType === 'touch') return;
    mx = e.clientX; my = e.clientY;
    hud.classList.add('on');
    if (!tk) { tk = true; requestAnimationFrame(upd); }
  }, { passive: true });
  document.documentElement.addEventListener('pointerleave', function(){ hud.classList.remove('on'); });
})();
</script>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- buffered inline asset, ogni valore dinamico sanitizzato/escapato sopra
        echo ob_get_clean();
    }
}
