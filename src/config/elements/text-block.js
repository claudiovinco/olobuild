import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Text-block — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → contenuto rich text
 *   styleFields[] → preset, tipografia, layout, padding, bordo, effetti
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'text-block',
  name: t('Testo'),
  icon: 'dashicons-editor-paragraph',
  category: 'essential',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    content: '<p>Scrivi qui il tuo testo. Puoi formattare con <strong>grassetto</strong>, <em>corsivo</em>, elenchi, titoli e molto altro.</p>',
    typography_preset: '',
    text_color: '',
    font_size: '',
    line_height: '',
    text_align: 'left',
    max_width: '',
    columns: 1,
    column_gap: '30',
    padding: '16',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    tile_margin: { top: 0, right: 0, bottom: 0, left: 0 },
    border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
    hover_border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
    hover_radius_duration: 400,
    ...textEffectsDefaults,
    text_effect_target: 'content',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'content', label: t('Contenuto'), type: 'editor', mode: 'block' },
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
    ...textEffectsFields([ { value: 'content', label: t('Solo Contenuto') } ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:       'font_size',
        lineHeight: 'line_height',
        color:      'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Layout') },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centrato') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},
    { key: 'max_width', label: t('Larghezza max (px)'), type: 'range', min: 0, max: 1200, step: 10,
      description: t('0 = nessun limite') },
    { key: 'columns', label: t('Numero colonne'), type: 'range', min: 1, max: 4, step: 1 },
    { key: 'column_gap', label: t('Spazio tra colonne (px)'), type: 'range', min: 0, max: 80, step: 2,
      condition: { field: 'columns', operator: '>', value: 1 } },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 100 },
    { key: 'tile_margin', label: t('Margine (px)'), type: 'spacing', min: -50, max: 100 },
    withHover(
      { key: 'border_radius', label: t('Border Radius'), type: 'border-radius' },
      { hoverKey: 'hover_border_radius', hoverDurationKey: 'hover_radius_duration' }
    ),
    ...borderFields(),
  ],
};
