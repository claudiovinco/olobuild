import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Alert — split CONTENUTO/STILE.
 *   fields[]      → tipo, title, message, show_icon, custom_icon, dismissible
 *   styleFields[] → preset, bg, typo, text-effects, allineamento, colori custom, shadow, border
 */
export default {
  type: 'alert',
  name: t('Avviso'),
  icon: 'dashicons-warning',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    alert_type: 'info',
    title: t('Attenzione!'),
    message: t('Questo è un avviso informativo.'),
    show_icon: true,
    custom_icon: '',
    dismissible: false,
    custom_bg_color: '',
    custom_text_color: '',
    text_align: 'left',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'all',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  // Solo opzioni strutturali del messaggio (tipo, icona, chiudibilità).
  // I testi (titolo + messaggio) si modificano nella zona STILE via popup
  // dedicato, per evitare doppi controlli e dare immediato accesso visivo.
  fields: [
    { key: 'alert_type', label: t('Tipo'), type: 'select', options: [
      { value: 'info', label: t('Info') },
      { value: 'success', label: t('Successo') },
      { value: 'warning', label: t('Attenzione') },
      { value: 'error', label: t('Errore') },
    ]},
    { key: 'show_icon', label: t('Mostra icona'), type: 'toggle' },
    { key: 'custom_icon', label: t('Icona personalizzata'), type: 'icon' },
    { key: 'dismissible', label: t('Chiudibile'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    // PRIMARIO: editing testi via popup (titolo + messaggio separati).
    { type: 'content-popup', label: t('Testi'), fields: [
      { key: 'title',   label: t('Titolo'),    type: 'text' },
      { key: 'message', label: t('Messaggio'), type: 'textarea' },
    ]},

    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-pill',     label: t('Modern Pill') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'magazine-bar',    label: t('Magazine Bar') },
      { value: 'banner-bold',     label: t('Banner Bold') },
      { value: 'centered-card',   label: t('Centered Card') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-strip',      label: t('Neon Strip') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-soft',   label: t('Gradient Soft') },
      { value: 'sticker-tape',    label: t('Sticker Tape') },
      { value: 'retro-banner',    label: t('Retro Banner') },
      { value: 'tilt-card',       label: t('Tilt Card') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'message', label: t('Solo Messaggio') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'custom_text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centrato') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},

    { type: 'separator', label: t('Sfondo personalizzato') },
    { key: 'custom_bg_color', label: t('Colore sfondo (sovrascrive tipo)'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
