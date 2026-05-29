import { t } from '@/i18n';

/**
 * Tile Dark Mode Toggle — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → stile UI (toggle/icon/button), icone scelte, testi pulsante, comportamento (salva preferenza, rispetta sistema)
 *   styleFields[] → dimensione icona, colori toggle, durata transizione
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'darkmode',
  name: t('Dark Mode Toggle'),
  icon: 'dashicons-admin-appearance',
  category: 'interactive',
  defaults: {
    style: 'toggle',
    light_icon: 'sun',
    dark_icon: 'moon',
    icon_size: 24,
    button_text_light: 'Modalità scura',
    button_text_dark: 'Modalità chiara',
    toggle_color: '',
    toggle_active_color: '',
    save_preference: true,
    respect_system: true,
    transition_duration: 300,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Stile') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'toggle', label: t('Toggle switch') },
      { value: 'icon', label: t('Icona singola') },
      { value: 'button', label: t('Pulsante con testo') },
    ]},

    { type: 'separator', label: t('Icone') },
    { key: 'light_icon', label: t('Icona luce'), type: 'icon' },
    { key: 'dark_icon', label: t('Icona scuro'), type: 'icon' },

    { type: 'separator', label: t('Testo (solo button)') },
    { key: 'button_text_light', label: t('Testo (modalità chiara)'), type: 'text',
      condition: { field: 'style', operator: '==', value: 'button' } },
    { key: 'button_text_dark', label: t('Testo (modalità scura)'), type: 'text',
      condition: { field: 'style', operator: '==', value: 'button' } },

    { type: 'separator', label: t('Comportamento') },
    { key: 'save_preference', label: t('Salva preferenza'), type: 'toggle' },
    { key: 'respect_system', label: t('Rispetta tema di sistema'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Dimensioni') },
    { key: 'icon_size', label: t('Dimensione icona (px)'), type: 'range', min: 16, max: 48, step: 2 },

    { type: 'separator', label: t('Colori') },
    { key: 'toggle_color', label: t('Colore toggle'), type: 'color' },
    { key: 'toggle_active_color', label: t('Colore toggle attivo'), type: 'color' },

    { type: 'separator', label: t('Transizione') },
    { key: 'transition_duration', label: t('Durata transizione (ms)'), type: 'range', min: 0, max: 1000, step: 50 },
  ],
};
