import { t } from '@/i18n';

/**
 * Tile Dark Mode Toggle — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → icone scelte (lette dal PHP: «sun»/«moon» = disegni storici), testi pulsante,
 *                   comportamento (salva preferenza, rispetta sistema)
 *   styleFields[] → Aspetto (stile toggle/icona/pulsante, colori, durata), Forma (dimensione icona)
 * La preferenza si riapplica in testa alla pagina in ogni tema: Olobuild_Darkmode_Tile::stampa_script_testa().
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
  // Colori vuoti = token nel renderer PHP: testo del sito in chiaro, «warning» (oro) in scuro.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'toggle', label: t('Toggle switch') },
      { value: 'icon', label: t('Icona singola') },
      { value: 'button', label: t('Pulsante con testo') },
    ]},
    { key: 'toggle_color', label: t('Colore in modalità chiara'), type: 'color' },
    { key: 'toggle_active_color', label: t('Colore in modalità scura'), type: 'color' },
    { key: 'transition_duration', label: t('Durata transizione'), type: 'range', min: 0, max: 1000, step: 50, unit: 'ms' },

    { type: 'separator', label: t('Forma') },
    { key: 'icon_size', label: t('Dimensione icona'), type: 'range', min: 16, max: 48, step: 2, unit: 'px' },
  ],
};
