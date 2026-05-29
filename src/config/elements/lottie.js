import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Lottie — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → file Lottie (.json), loop, trigger, hover action
 *   styleFields[] → preset, typo preset, dimensioni, speed, allineamento, ombra, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'lottie',
  name: t('Lottie Animation'),
  icon: 'dashicons-format-video',
  category: 'media',
  defaults: {
    typography_preset: '',
    preset: 'custom',
    json_url: '',
    width: '300',
    height: '300',
    loop: true,
    autoplay: true,
    speed: '1',
    trigger: 'autoplay',
    hover_action: 'none',
    alignment: 'center',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'json_url', label: t('File Lottie (.json)'), type: 'lottie_picker' },

    { type: 'separator', label: t('Riproduzione') },
    { key: 'loop', label: t('Ripeti'), type: 'toggle' },
    { key: 'trigger', label: t('Trigger'), type: 'select', options: [
      { value: 'autoplay', label: t('Automatico') },
      { value: 'viewport', label: t('Quando visibile') },
      { value: 'hover', label: t('Al passaggio mouse') },
      { value: 'click', label: t('Al click') },
    ]},
    { key: 'hover_action', label: t('Azione hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'pause', label: t('Pausa') },
      { value: 'reverse', label: t('Inverti direzione') },
      { value: 'speed-up', label: t('Accelera (2x)') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean', label: t('Modern Clean') },
      { value: 'minimal-frame', label: t('Minimal Frame') },
      { value: 'magazine-bold', label: t('Magazine Bold') },
      { value: 'cinema-wide', label: t('Cinema Wide') },
      { value: 'centered-large', label: t('Centered Large') },
      { value: 'glass-frame', label: t('Glass Frame') },
      { value: 'neon-glow', label: t('Neon Glow') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun', label: t('Sticker Fun') },
      { value: 'retro-vhs', label: t('Retro VHS') },
      { value: 'tilt-3d', label: t('3D Tilt') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Dimensioni & velocità') },
    { key: 'width', label: t('Larghezza (px)'), type: 'range', min: 50, max: 800, step: 10 },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 50, max: 800, step: 10 },
    { key: 'speed', label: t('Velocità'), type: 'range', min: 0.1, max: 3, step: 0.1 },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    ...shadowField,
    ...borderFields(),
  ],
};
