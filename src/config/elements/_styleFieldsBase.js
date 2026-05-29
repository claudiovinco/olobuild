import { withHover, borderFields } from './_shared.js';
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
  const isStructural = STRUCTURAL_TYPES.has(tileType);
  const layoutFields = isStructural
    ? [
        // Le tile strutturali hanno controlli larghezza dedicati nel proprio config.
        // Qui esponiamo solo altezza minima e overflow (utili anche su section/row/column).
        { key: 'tile_min_height', label: t('Altezza minima'),  type: 'text', responsive: true, placeholder: t('auto (es. 300px, 50vh)') },
        { key: 'overflow',       label: t('Overflow'),         type: 'select', options: [
          { value: 'visible', label: t('Visibile') },
          { value: 'hidden',  label: t('Nascosto') },
          { value: 'auto',    label: t('Auto (scroll)') },
        ]},
      ]
    : [
        { key: 'full_width',     label: t('Larghezza piena'),  type: 'toggle' },
        { key: 'tile_width',     label: t('Larghezza'),        type: 'text', responsive: true, placeholder: t('auto (es. 25%, 200px)') },
        { key: 'tile_max_width', label: t('Larghezza massima'), type: 'text', responsive: true, placeholder: t('none (es. 600px, 80%)') },
        { key: 'tile_min_height', label: t('Altezza minima'),  type: 'text', responsive: true, placeholder: t('auto (es. 300px, 50vh)') },
        { key: 'overflow',       label: t('Overflow'),         type: 'select', options: [
          { value: 'visible', label: t('Visibile') },
          { value: 'hidden',  label: t('Nascosto') },
          { value: 'auto',    label: t('Auto (scroll)') },
        ]},
      ];

  return [
    // ─── LAYOUT ─────────────────────────────────────────────────
    { type: 'separator', label: t('Layout') },
    ...layoutFields,

    // ─── SPAZI & BORDI (pannello unico compatto: margine/padding/raggio) ───
    // Sostituisce le sezioni separate "Spaziatura" e "Border radius" con un solo
    // pannello impilato (design handoff boxcontrol). Stesse chiavi salvate:
    // margin_*/padding_* (per-breakpoint) e border_radius (+ style.hover.border_radius).
    { type: 'separator', label: t('Spazi & Bordi') },
    { type: 'box-stack' },

    // ─── SFONDO ─────────────────────────────────────────────────
    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo'), type: 'background', showParallax: true },

    // ─── BORDO completo ─────────────────────────────────────────
    // Sistema FieldBorder: 4 lati indipendenti con link/unlink + stile + colore + hover + 4 effetti.
    // Salva in style.border (oggetto), style.border_hover, style.border_effect_*.
    // Il PHP frontend renderer (build_wrapper_border_css) lo legge con priorità sul vecchio
    // sistema legacy (border_width/style/color), che resta come fallback per template salvati prima.
    // borderFields() inserisce i propri separator "Bordo" e "Effetti bordo" → 2 sezioni.
    ...borderFields(),

    // ─── EFFETTI ────────────────────────────────────────────────
    { type: 'separator', label: t('Effetti') },
    withHover({ key: 'shadow_block',     label: t('Ombra'),     type: 'shadow-block' }),
    withHover({ key: 'opacity',          label: t('Opacità'),   type: 'range', min: 0, max: 100, step: 5, default: 100, unit: '%' }),
    withHover({ key: 'transform',        label: t('Trasformazione'), type: 'transform' }),
    withHover({ key: 'text_shadow',      label: t('Ombra testo'),     type: 'text-shadow' }),
    withHover({ key: 'backdrop_filter',  label: t('Filtro sfondo (glassmorphism)'), type: 'backdrop-filter' }),
    { key: 'mask', label: t('Maschera'), type: 'select', options: [
      { value: 'none',     label: t('Nessuna') },
      { value: 'circle',   label: t('Cerchio') },
      { value: 'triangle', label: t('Triangolo') },
      { value: 'diamond',  label: t('Diamante') },
      { value: 'hexagon',  label: t('Esagono') },
      { value: 'star',     label: t('Stella') },
      { value: 'blob',     label: t('Blob') },
      { value: 'wave',     label: t('Onda') },
    ]},

    // ─── TRANSIZIONE GLOBALE (fallback se i singoli withHover non hanno duration) ──
    { type: 'separator', label: t('Transizione hover (globale)') },
    { key: 'transition.duration', label: t('Durata (ms)'), type: 'range', min: 0, max: 2000, step: 50, default: 300 },
    { key: 'transition.easing',   label: t('Easing'), type: 'select', options: [
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
