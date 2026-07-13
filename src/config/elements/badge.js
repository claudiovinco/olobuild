import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, shadowField, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Badge / Etichetta — pill compatta con testo + icona opzionale.
 * Feature dedicata "Stato live": pallino con onda che pulsa (verde o primario),
 * pensato per indicatori "Online / In diretta / Novità" (vedi Try home).
 *
 *   fields[]      → testo, icona, posizione icona, Stato live (toggle + colore onda)
 *   styleFields[] → preset, variante, colori, tipografia, forma (radius), padding, ombra, bordo
 *
 * Chiavi nuove additive: badge_live (bool), badge_live_color ('success'|'primary').
 */
export default {
  type: 'badge',
  name: t('Badge / Etichetta'),
  icon: 'dashicons-tag',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    text: t('Online'),
    icon: '',
    icon_position: 'before',
    // Stato live (additive)
    badge_live: false,
    badge_live_color: 'success',
    // Badge aggiuntivi (additive): pill gemelle in fila, colore per-item
    extra_items: [],
    // Stile
    variant: 'soft',
    bg_color: '',
    text_color: '',
    font_size: '13',
    font_weight: '600',
    text_transform: 'none',
    letter_spacing: '0',
    badge_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
    padding_y: 7,
    padding_x: 13,
    alignment: 'left',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'text',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'text', label: t('Testo'), type: 'text' },
    { key: 'icon', label: t('Icona (opzionale)'), type: 'icon' },
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'before', label: t('Prima del testo') },
      { value: 'after', label: t('Dopo il testo') },
    ]},

    { type: 'separator', label: t('Badge aggiuntivi') },
    { key: 'extra_items', label: t('Altre etichette'), type: 'content-items', itemLabel: t('Etichetta'),
      hint: t('Pill gemelle affiancate alla prima: stesso stile, colore per-etichetta (vuoto = neutro).'),
      newItemDefaults: { text: '', color: '', text_color: '' },
      itemFields: [
        { key: 'text', label: t('Testo'), type: 'text' },
        { key: 'color', label: t('Colore'), type: 'color' },
        { key: 'text_color', label: t('Colore testo (vuoto = come il colore)'), type: 'color' },
      ] },

    { type: 'separator', label: t('Stato live') },
    { key: 'badge_live', label: t('Mostra pallino live'), type: 'toggle',
      description: t('Aggiunge un pallino con onda che pulsa, per indicatori "Online / In diretta".') },
    { key: 'badge_live_color', label: t('Colore onda'), type: 'select', options: [
      { value: 'success', label: t('Verde (online)') },
      { value: 'primary', label: t('Primario (brand)') },
    ], condition: { field: 'badge_live', op: 'eq', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-soft',     label: t('Modern Soft') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'solid-pill',      label: t('Solid Pill') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'soft',    label: t('Soft (tinta tenue)') },
      { value: 'solid',   label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'light',   label: t('Chiaro') },
    ]},
    { key: 'alignment', label: t('Allineamento'), type: 'select', responsive: true, options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},

    ...textEffectsFields([ { value: 'text', label: t('Solo Testo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:          'font_size',
        weight:        'font_weight',
        transform:     'text_transform',
        letterSpacing: 'letter_spacing',
        color:         'text_color',
      },
      sizeMin: 10, sizeMax: 28,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },

    { type: 'separator', label: t('Forma') },
    withHover({ key: 'badge_radius', label: t('Raggio bordo'), type: 'border-radius' }),
    { key: 'padding_y', label: t('Padding verticale (px)'), type: 'range', min: 0, max: 30, step: 1 },
    { key: 'padding_x', label: t('Padding orizzontale (px)'), type: 'range', min: 0, max: 48, step: 1 },

    ...shadowField,
    ...borderFields(),
  ],
};
