import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Progress — split CONTENUTO/STILE.
 *   fields[]      → bars (etichetta|valore), show_percentage, inner_text, layout, animation toggles
 *   styleFields[] → preset, bg, typo, colori, dimensioni (altezza/cerchio), animation duration, shadow, border
 */
export default {
  type: 'progress',
  name: t('Barra progresso'),
  icon: 'dashicons-chart-bar',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    bars: 'HTML|90\nJavaScript|80\nVue.js|75',
    bar_color: '',
    bar_bg: '',
    text_color: '',
    height: '20',
    show_percentage: true,
    animated: true,
    layout: 'bar',
    circle_size: '120',
    circle_width: '8',
    inner_text: '',
    animate_counter: true,
    animation_duration: '1500',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: quattro indicatori di un'attività qualunque in barre sottili e arrotondate
  // nel colore primario, su una traccia della stessa tinta; le barre si riempiono entrando.
  partenza: {
    bars: t('Clienti soddisfatti|98\nProgetti consegnati nei tempi|95\nClienti che tornano|87\nRisposte entro 24 ore|92'),
    bar_color: 'var(--olo-color-primary)',
    bar_bg: 'color-mix(in srgb, var(--olo-color-primary) 14%, transparent)',
    text_color: 'var(--olo-color-text)',
    height: '10',
    show_percentage: false,
  },

  fields: [
    { key: 'bars', label: t('Barre (etichetta|valore per riga)'), type: 'textarea' },
    { key: 'show_percentage', label: t('Mostra percentuale'), type: 'toggle' },
    // L'etichetta diceva «vuoto = percentuale»: nella barra non è più vero (la percentuale
    // compariva due volte), la regola completa sta nella (i).
    { key: 'inner_text', label: t('Testo interno'), type: 'text',
      description: t('Il testo al centro della barra o del cerchio. Se è vuoto, nel cerchio compare la percentuale; nella barra la percentuale sta in testa con «Mostra percentuale», e dentro la barra solo coi preset Thick Bold, Gradient Bar e Gradient Aurora e un colore del testo chiaro.') },


    { type: 'separator', label: t('Animazione') },
    { key: 'animated', label: t('Animata'), type: 'toggle' },
    { key: 'animate_counter', label: t('Anima contatore'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-thin',    label: t('Minimal Thin') },
      { value: 'thick-bold',      label: t('Thick Bold') },
      { value: 'gradient-bar',    label: t('Gradient Bar') },
      { value: 'segmented',       label: t('Segmented') },
      { value: 'glass-bar',       label: t('Glass Bar') },
      { value: 'neon-pulse',      label: t('Neon Pulse') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fill',    label: t('Sticker Fill') },
      { value: 'retro-segments',  label: t('Retro Segments') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Colori') },
    { key: 'bar_color', label: t('Colore barra'), type: 'color' },
    { key: 'bar_bg', label: t('Sfondo barra'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'height', label: t('Altezza'), type: 'range', min: 10, max: 600, step: 5 },
    { key: 'circle_size', label: t('Dimensione cerchio'), type: 'range', min: 60, max: 200, step: 10,
      condition: { field: 'layout', operator: '==', value: 'circle' } },
    { key: 'circle_width', label: t('Spessore cerchio'), type: 'range', min: 2, max: 20, step: 1,
      condition: { field: 'layout', operator: '==', value: 'circle' } },

    { type: 'separator', label: t('Velocità animazione') },
    { key: 'animation_duration', label: t('Durata animazione'), type: 'range', min: 500, max: 3000, step: 100 },

    ...shadowField,
    ...borderFields(),
    { type: 'separator', label: t('Disposizione') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'bar', label: t('Barra') },
      { value: 'circle', label: t('Cerchio') },
    ]},
  ],
};
