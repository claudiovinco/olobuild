import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Search — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → placeholder, placeholder animato + parole, icona/pulsante visibilità + testo pulsante
 *   styleFields[] → preset, bg, typo, layout, posizione icona, stile pulsante, colori, bordi, effetti
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'search',
  name: t('Ricerca'),
  icon: 'dashicons-search',
  category: 'navigation',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    placeholder: t('Cerca...'),
    style: 'default',
    size: 'medium',
    show_icon: true,
    icon_position: 'left',
    show_button: false,
    button_text: 'Cerca',
    button_style: 'filled',
    full_width: true,
    max_width: '',
    alignment: 'center',
    bg_color: '#FFFFFF',
    text_color: '#374151',
    placeholder_color: '#9CA3AF',
    icon_color: '#6B7280',
    border_color: '#E5E7EB',
    border_width: '1',
    border_radius: '8',
    focus_border_color: '',   // '' ⇒ primary (era #e1474f off-brand)
    button_bg: '',            // '' ⇒ primary (era #e1474f off-brand)
    button_color: '#FFFFFF',
    button_radius: '8',
    input_shadow: false,
    focus_shadow: true,
    animated_placeholder: false,
    placeholder_words: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'placeholder', label: t('Placeholder'), type: 'text' },
    { key: 'animated_placeholder', label: t('Placeholder animato'), type: 'toggle' },
    { key: 'placeholder_words', label: t('Parole animate (una per riga)'), type: 'textarea',
      show: s => s.animated_placeholder },

    { type: 'separator', label: t('Icona') },
    { key: 'show_icon', label: t('Mostra icona'), type: 'toggle' },

    { type: 'separator', label: t('Pulsante') },
    { key: 'show_button', label: t('Mostra pulsante'), type: 'toggle' },
    { key: 'button_text', label: t('Testo pulsante'), type: 'text', show: s => s.show_button },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'pill-rounded',    label: t('Pill Rounded') },
      { value: 'magazine-bar',    label: t('Magazine Bar') },
      { value: 'with-button',     label: t('With Button') },
      { value: 'glass-floating',  label: t('Glass Floating') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-border', label: t('Gradient Border') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Input'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Layout') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'minimal', label: t('Minimale') },
      { value: 'pill', label: t('Pillola') },
      { value: 'underline', label: t('Sottolineato') },
      { value: 'hero', label: t('Hero (grande)') },
      { value: 'floating', label: t('Flottante') },
    ]},
    { key: 'size', label: t('Dimensione'), type: 'select', options: [
      { value: 'small', label: t('Piccola') },
      { value: 'medium', label: t('Media') },
      { value: 'large', label: t('Grande') },
    ]},
    { key: 'full_width', label: t('Larghezza piena'), type: 'toggle' },
    { key: 'max_width', label: t('Larghezza massima (px)'), type: 'text',
      show: s => !s.full_width },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ], show: s => !s.full_width },

    { type: 'separator', label: t('Posizione icona') },
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ], show: s => s.show_icon },

    { type: 'separator', label: t('Stile pulsante') },
    { key: 'button_style', label: t('Stile pulsante'), type: 'select', options: [
      { value: 'filled', label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'icon-only', label: t('Solo icona') },
    ], show: s => s.show_button },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo input'), type: 'color' },
    { key: 'placeholder_color', label: t('Colore placeholder'), type: 'color' },
    { key: 'icon_color', label: t('Colore icona'), type: 'color' },
    { key: 'border_color', label: t('Colore bordo'), type: 'color' },
    { key: 'focus_border_color', label: t('Colore bordo focus'), type: 'color' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color', show: s => s.show_button },
    { key: 'button_color', label: t('Colore testo pulsante'), type: 'color', show: s => s.show_button },

    { type: 'separator', label: t('Bordi') },
    { key: 'border_width', label: t('Spessore bordo (px)'), type: 'range', min: 0, max: 4 },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    withHover({ key: 'button_radius', label: t('Arrotondamento pulsante (px)'), type: 'border-radius',
      show: s => s.show_button }),

    { type: 'separator', label: t('Effetti') },
    { key: 'input_shadow', label: t('Ombra input'), type: 'toggle' },
    { key: 'focus_shadow', label: t('Ombra su focus'), type: 'toggle' },

    ...shadowField,
    ...borderFields(),
  ],
};
