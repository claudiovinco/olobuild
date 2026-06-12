import { withHover, borderEffectFields } from './_shared.js';
import { t } from '@/i18n';

/**
 * Definizione dichiarativa dei field del tab "Stile" del wrapper di un tile.
 * Sostituisce il template hard-coded in BuilderInspector.vue:227-889.
 *
 * SCOPE: questi field scrivono su `tile.style.*` (NON `tile.settings.*`).
 * L'InspectorField va invocato con `:scope="'style'"`.
 *
 * Hover: i field marcati con withHover() salvano l'override in `tile.style.hover.X`
 * (schema legacy preservato per backward-compat). NON in `tile.style.X_hover`.
 *
 * BREAKPOINT-AWARE: i field marcati con `responsive: true` usano la chiave
 * `key_${breakpoint}` (es. `tile_width_tablet`) — già supportato da InspectorField.
 *
 * SPACING BREAKPOINT: margin/padding hanno un breakpoint dedicato (`spacingBp`)
 * gestito separatamente nel renderer perché lavora su 4 sub-keys (top/right/bottom/left)
 * non su un singolo key responsive.
 */
/**
 * tileType opzionale: per tile strutturali (section/row/column/inner-columns) i field
 * di larghezza/padding del wrapper sono nascosti perché hanno controlli semantici dedicati
 * (es. section.width = small/large/fullbleed, column.width_default = 1-1/1-2/...). Lasciarli
 * tutti visibili creava 3 controlli paralleli per la larghezza, di cui solo uno funzionava.
 */
const STRUCTURAL_TYPES = new Set(['section', 'row', 'column', 'inner-columns']);

export function styleFieldsBase(tileType) {
  return [
    // ─── LAYOUT ─────────────────────────────────────────────────
    // Pannello unico compatto (StyleLayoutStack): larghezza piena (+descrizione), larghezza,
    // larghezza massima, altezza minima (con SELETTORE UNITÀ), overflow + anteprima vincoli.
    // Niente switch device qui: il breakpoint si cambia dalla barra in alto (viewMode) e i
    // campi dimensionali (responsive) lo seguono. Chiavi salvate INVARIATE: full_width,
    // tile_width[_bp], tile_max_width[_bp], tile_min_height[_bp], overflow. Le tile strutturali
    // (section/row/column) sono gestite dentro il componente (solo altezza minima + overflow).
    // searchTerms: i pannelli compatti non hanno label/key propria — senza questi
    // alias la ricerca impostazioni ("Cerca impostazione...") non li troverebbe mai.
    { type: 'separator', label: t('Layout') },
    { type: 'layout-stack', searchTerms: ['layout', 'larghezza', 'width', 'altezza', 'height', 'overflow', 'dimensioni', 'larghezza piena', 'full width', 'minima', 'massima'] },

    // ─── SPAZI & BORDI (pannello unico compatto: margine/padding/raggio) ───
    // Sostituisce le sezioni separate "Spaziatura" e "Border radius" con un solo
    // pannello impilato (design handoff boxcontrol). Stesse chiavi salvate:
    // margin_*/padding_* (per-breakpoint) e border_radius (+ style.hover.border_radius).
    { type: 'separator', label: t('Spazi & Bordi') },
    { type: 'box-stack', searchTerms: ['margine', 'margin', 'padding', 'spaziatura', 'spacing', 'raggio', 'radius', 'bordo', 'border', 'angoli', 'arrotonda'] },

    // ─── SFONDO ─────────────────────────────────────────────────
    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo'), type: 'background', showParallax: true, searchTerms: ['background', 'sfondo', 'colore', 'immagine', 'gradiente', 'video', 'parallax'] },

    // ─── EFFETTI BORDO ──────────────────────────────────────────
    // Il CONTROLLO bordo (style.border / border_<bp> per-device / border_hover) ora vive
    // DENTRO il pannello "Spazi & Bordi" (box-stack), accanto a margine/padding/raggio.
    // Qui resta solo la sezione "Effetti bordo" (neon/gradiente), che opera su border_effect_*
    // e legge style.border come colore base.
    // PHP frontend: build_wrapper_border_css (desktop, inline) + collect_responsive_css (border_<bp>).
    ...borderEffectFields(),

    // ─── EFFETTI ────────────────────────────────────────────────
    // Pannello unico compatto (StyleEffectsStack): ombra, opacità, trasformazione, ombra
    // testo, filtro sfondo (glassmorphism), maschera + toggle Normale/Hover globale e
    // anteprima effetti dal vivo. Chiavi salvate INVARIATE e allineate al renderer PHP
    // (collect_hover_css): shadow/shadow_custom, opacity, transform (oggetto; hover su chiavi
    // piatte transform_*), text_shadow_*, backdrop_*, mask (non hoverable).
    { type: 'separator', label: t('Effetti') },
    { type: 'effects-stack', searchTerms: ['ombra', 'shadow', 'opacità', 'opacity', 'trasformazione', 'transform', 'scala', 'scale', 'rotazione', 'rotate', 'filtro', 'filter', 'backdrop', 'blur', 'glassmorphism', 'maschera', 'mask', 'effetti'] },

    // ─── BLEND MODE WRAPPER ─────────────────────────────────────
    // mix-blend-mode sul wrapper del tile/contenitore: utile per nav/heading che
    // si invertono sopra hero chiari/scuri (es. 'difference'). Chiave salvata:
    // style.blend_mode. PHP frontend: apply_common_box_styles → 'mix-blend-mode: …'.
    { key: 'blend_mode', label: t('Fusione (blend mode)'), type: 'select', default: 'normal', options: [
      { value: 'normal',      label: t('Normale') },
      { value: 'multiply',    label: t('Moltiplica') },
      { value: 'screen',      label: t('Schermo') },
      { value: 'overlay',     label: t('Sovrapposizione') },
      { value: 'darken',      label: t('Scurisci') },
      { value: 'lighten',     label: t('Schiarisci') },
      { value: 'color-dodge', label: t('Color Dodge') },
      { value: 'color-burn',  label: t('Color Burn') },
      { value: 'hard-light',  label: t('Hard Light') },
      { value: 'soft-light',  label: t('Soft Light') },
      { value: 'difference',  label: t('Differenza') },
      { value: 'exclusion',   label: t('Esclusione') },
      { value: 'hue',         label: t('Tonalità') },
      { value: 'saturation',  label: t('Saturazione') },
      { value: 'color',       label: t('Colore') },
      { value: 'luminosity',  label: t('Luminosità') },
    ]},

    // ─── TRANSIZIONE GLOBALE (fallback se i singoli withHover non hanno duration) ──
    { type: 'separator', label: t('Transizione hover (globale)') },
    { key: 'transition.duration', label: t('Durata'), type: 'range', min: 0, max: 2000, step: 50, default: 300, unit: 'ms' },
    { key: 'transition.easing',   label: t('Easing'), type: 'select', default: 'ease', options: [
      { value: 'ease',        label: t('Ease (default)') },
      { value: 'ease-in',     label: t('Ease in') },
      { value: 'ease-out',    label: t('Ease out') },
      { value: 'ease-in-out', label: t('Ease in-out') },
      { value: 'linear',      label: t('Linear') },
    ]},
  ];
}

/**
 * Field "compositi" che leggono/scrivono su MULTIPLE chiavi del tile.style.
 * Sono pseudo-field: il renderer custom espande in N controlli reali.
 *
 * Mapping per il PHP renderer (per backward-compat al rendering):
 *
 *   border_legacy:    style.border_width, style.border_style, style.border_color
 *   border_legacy_hover: style.hover.border_color (solo color, le size legacy non hanno hover)
 *
 *   shadow_block:     style.shadow (preset 'none|sm|md|lg|xl|custom') + style.shadow_custom (oggetto FieldBoxShadow)
 *   shadow_block_hover: style.hover.shadow + style.hover.shadow_custom
 *
 *   text_shadow:      style.text_shadow_h, style.text_shadow_v, style.text_shadow_blur, style.text_shadow_color
 *   text_shadow_hover: style.hover.text_shadow_h, style.hover.text_shadow_v, style.hover.text_shadow_blur, style.hover.text_shadow_color
 *
 *   backdrop_filter:  style.backdrop_blur, style.backdrop_brightness, style.backdrop_saturate
 *   backdrop_filter_hover: style.hover.backdrop_blur (solo blur è in hover oggi; brightness/saturate sono dichiarati ma non in hover)
 *
 *   bg:               style.bg (oggetto unificato gestito da BackgroundControls)
 *   bg_hover:         style.hover.bg_color (legacy semplice color)
 *
 *   transform:        style.transform (oggetto)
 *   transform_hover:  style.hover.transform_scale, transform_translateY, transform_translateX, transform_rotate, transform_skewX, transform_skewY (5 chiavi piatte)
 *
 *   opacity:          style.opacity
 *   opacity_hover:    style.hover.opacity
 *
 *   border_radius:    style.border_radius
 *   border_radius_hover: style.hover.border_radius
 *
 * Le chiavi salvate NON cambiano. Solo la UI di editing si pulisce.
 */
