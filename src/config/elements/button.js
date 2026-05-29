import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Button — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → testo, icona, URL, target, allineamento (semantica)
 *   styleFields[] → preset, colori, tipografia, padding, bordo, ombra, hover
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'button',
  name: t('Pulsante'),
  icon: 'dashicons-button',
  category: 'essential',
  defaults: {
    preset: 'custom',
    text: t('Clicca qui'),
    url: '#',
    target: '_self',
    alignment: 'center',
    full_width: false,

    typography_preset: '',
    bg: { type: 'none' },
    bg_color: '',
    text_color: '',
    border_radius: '6',
    tile_padding: { top: 14, right: 32, bottom: 14, left: 32 },
    font_size: '16',
    font_weight: '600',
    letter_spacing: '0',
    text_transform: 'none',
    icon: '',
    icon_position: 'before',
    icon_spacing: '8',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,

    hover_bg_color: '',
    hover_text_color: '',
    hover_shadow: '',
    hover_effect: 'lift',
    hover_image: '',
    hover_video: '',
    ...textEffectsDefaults,
    text_effect_target: 'text',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'text', label: t('Testo pulsante'), type: 'text' },
    { key: 'icon', label: t('Icona'), type: 'icon' },
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'before', label: t('Prima del testo') },
      { value: 'after', label: t('Dopo il testo') },
    ]},
    { key: 'url', label: t('URL'), type: 'link' },
    { key: 'target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},
    { key: 'full_width', label: t('Larghezza piena'), type: 'toggle' },
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
      { value: 'glass-pill',      label: t('Glass Pill') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},

    { type: 'separator', label: t('Layout') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', responsive: true, options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'icon_spacing', label: t('Spazio icona (px)'), type: 'range', min: 0, max: 24, step: 2 },

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
      sizeMin: 12, sizeMax: 32,
      letterSpacingUnit: 'px',
    },

    { type: 'separator', label: t('Sfondo & colori') },
    withHover({ key: 'bg_color', label: t('Colore sfondo (semplice)'), type: 'color' }, { hoverKey: 'hover_bg_color' }),

    { type: 'separator', label: t('Forma') },
    withHover({ key: 'border_radius', label: t('Border Radius'), type: 'border-radius' }),
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 80 },
    ...shadowField,
    ...borderFields(),

    { type: 'separator', label: t('Stato hover') },
    { key: 'hover_text_color', label: t('Colore testo hover'), type: 'color' },
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'lift', label: t('Solleva (↑)') },
      { value: 'grow', label: t('Ingrandisci') },
      { value: 'shrink', label: t('Rimpicciolisci') },
      { value: 'glow', label: t('Bagliore') },
      { value: 'pulse', label: t('Pulsazione') },
    ]},
    { key: 'hover_shadow', label: t('Ombra hover'), type: 'select', options: [
      { value: '', label: t('Invariata') },
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Piccola') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'hover_shadow_h', label: t('Offset H hover (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'hover_shadow_v', label: t('Offset V hover (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'hover_shadow_blur', label: t('Sfocatura hover (px)'), type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'hover_shadow_spread', label: t('Espansione hover (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'hover_shadow_color', label: t('Colore ombra hover'), type: 'color',
      condition: { field: 'hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'hover_shadow_inset', label: t('Ombra interna hover'), type: 'toggle',
      condition: { field: 'hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'hover_image', label: t('Immagine hover'), type: 'image' },
    { key: 'hover_video', label: t('Video hover (mp4)'), type: 'media' },
  ],
};
