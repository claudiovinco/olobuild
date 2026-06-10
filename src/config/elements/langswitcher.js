
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Lang Switcher — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → stile contenutistico (bandiere/codici/nomi), layout (inline/dropdown/tabs/floating), posizione fluttuante,
 *                   bordo tabs, formato etichetta, toggle visibilità (show_label, show_dropdown_arrow), compatto
 *   styleFields[] → preset, bg, typography_preset, dimensioni (flag_size, circle_size, tabs_offset, tabs_size, gap),
 *                   forma bandiere, colori, border-radius, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'langswitcher',
  name: t('Selettore lingua'),
  icon: 'dashicons-translation',
  category: 'navigation',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    style: 'flags',
    flag_shape: 'circle',
    flag_size: 24,
    show_label: false,
    label_format: 'name',
    layout: 'inline',
    floating_pos: 'bottom-right',
    gap: 8,
    active_bg: '',
    active_color: '',
    bg: '',
    color: '',
    border_color: '',
    border_radius: 8,
    show_dropdown_arrow: true,
    // Tabs (linguette)
    tabs_edge: 'top',
    tabs_offset: 20,
    tabs_size: 'normal',
    // Circle badge
    circle_bg: '',
    circle_border: '',
    circle_size: 36,
    // Compact
    compact: false,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Stile contenuto') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'flags', label: t('Bandiere') },
      { value: 'flags_circle', label: t('Bandiere in cerchietto') },
      { value: 'codes', label: t('Codici (IT/EN/DE)') },
      { value: 'names', label: t('Nomi (Italiano/English)') },
      { value: 'flags_text', label: t('Bandiere + codice') },
    ]},
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'inline', label: t('Inline (dentro header/footer)') },
      { value: 'dropdown', label: t('Dropdown') },
      { value: 'tabs', label: t('Linguette fisse (bordo pagina)') },
      { value: 'floating', label: t('Fluttuante (fisso su schermo)') },
    ]},
    { key: 'compact', label: t('Compatto (extra piccolo)'), type: 'toggle' },

    { type: 'separator', label: t('Linguette / Fluttuante'), show: s => s.layout === 'tabs' || s.layout === 'floating' },
    { key: 'tabs_edge', label: t('Bordo'), type: 'select',
      show: s => s.layout === 'tabs',
      options: [
        { value: 'top', label: t('Alto (linguette che scendono)') },
        { value: 'right', label: t('Destra (linguette laterali)') },
        { value: 'left', label: t('Sinistra (linguette laterali)') },
      ]},
    { key: 'floating_pos', label: t('Posizione fluttuante'), type: 'select', options: [
      { value: 'bottom-right', label: t('Basso destra') },
      { value: 'bottom-left', label: t('Basso sinistra') },
      { value: 'top-right', label: t('Alto destra') },
      { value: 'top-left', label: t('Alto sinistra') },
      { value: 'middle-right', label: t('Centro destra') },
      { value: 'middle-left', label: t('Centro sinistra') },
    ], show: s => s.layout === 'floating' },

    { type: 'separator', label: t('Etichetta') },
    { key: 'show_label', label: t('Mostra etichetta sotto'), type: 'toggle',
      show: s => s.style === 'flags' || s.style === 'flags_circle' },
    { key: 'label_format', label: t('Formato etichetta'), type: 'select', options: [
      { value: 'name', label: t('Nome completo') },
      { value: 'code', label: t('Codice (IT/EN)') },
    ], show: s => s.show_label },
    { key: 'show_dropdown_arrow', label: t('Freccia dropdown'), type: 'toggle',
      show: s => s.layout === 'dropdown' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Dimensioni linguette'), show: s => s.layout === 'tabs' },
    { key: 'tabs_offset', label: t('Distanza dal bordo (px)'), type: 'range', min: 0, max: 200, step: 5,
      show: s => s.layout === 'tabs' },
    { key: 'tabs_size', label: t('Dimensione linguette'), type: 'select',
      show: s => s.layout === 'tabs',
      options: [
        { value: 'tiny', label: t('Minima') },
        { value: 'small', label: t('Piccola') },
        { value: 'normal', label: t('Normale') },
      ]},

    { type: 'separator', label: t('Bandiere'),
      show: s => s.style === 'flags' || s.style === 'flags_text' || s.style === 'flags_circle' },
    { key: 'flag_shape', label: t('Forma bandiere'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'rounded', label: t('Rettangolo arrotondato') },
    ], show: s => s.style === 'flags' || s.style === 'flags_text' },
    { key: 'flag_size', label: t('Dimensione bandiere (px)'), type: 'range', min: 14, max: 48,
      show: s => s.style === 'flags' || s.style === 'flags_text' || s.style === 'flags_circle' },

    { key: 'circle_size', label: t('Diametro cerchietto (px)'), type: 'range', min: 24, max: 56, step: 2,
      show: s => s.style === 'flags_circle' },
    { key: 'circle_bg', label: t('Sfondo cerchietto'), type: 'color',
      show: s => s.style === 'flags_circle' },
    { key: 'circle_border', label: t('Bordo cerchietto'), type: 'color',
      show: s => s.style === 'flags_circle' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'gap', label: t('Spazio tra elementi (px)'), type: 'range', min: 0, max: 24 },

    { type: 'separator', label: t('Colori') },
    { key: 'active_bg', label: t('Sfondo lingua attiva'), type: 'color' },
    { key: 'active_color', label: t('Testo lingua attiva'), type: 'color' },
    { key: 'bg', label: t('Sfondo'), type: 'color' },
    { key: 'color', label: t('Testo'), type: 'color' },
    { key: 'border_color', label: t('Bordo'), type: 'color' },
    withHover({ key: 'border_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
