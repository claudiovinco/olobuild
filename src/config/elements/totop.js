import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ToTop — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → comportamento: scroll fluido, «Compare dopo» (solo fisso)
 *   styleFields[] → stile pulsante (default/primary), ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'totop',
  name: t('Torna su'),
  icon: 'dashicons-arrow-up-alt',
  category: 'navigation',
  defaults: {
    alignment: 'right',
    style: 'default',
    smooth: true,
    // «Fisso nell'angolo» (14 ott 2026): il pulsante resta nell'angolo dello schermo e compare dopo
    // «Compare dopo» px di scorrimento. Va nel piè di pagina del tema (lì i position:fixed funzionano).
    posizione: 'in_linea',
    compare_dopo: 600,
    distanza_bordo: 24,
    distanza_basso: 24,
    button_size: 44,
    icon_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'smooth', label: t('Scorrimento fluido'), type: 'toggle' },
    { key: 'compare_dopo', label: t('Compare dopo'), type: 'range', min: 0, max: 2000, step: 50, unit: 'px',
      description: t('Quanto si scorre la pagina prima che il pulsante compaia. 0 = sempre visibile.'),
      condition: { field: 'posizione', op: 'eq', value: 'fisso' } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // «Stile» prima non era letto dal PHP e la freccia era l'icona UIkit di 18×10 px, senza misura
  // né colore: ora il renderer disegna un pulsante tondo (Predefinito leggero, Pieno nel colore del
  // sito) con la misura e il colore della freccia scelti qui.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'primary', label: t('Pieno') },
    ]},
    { key: 'icon_color', label: t('Colore freccia'), type: 'color',
      description: t('Vuoto = il colore dello stile: quello del testo nel Predefinito, il contrasto del colore del sito nel Pieno.') },
    ...shadowField,

    { type: 'separator', label: t('Forma') },
    { key: 'button_size', label: t('Dimensione'), type: 'range', min: 28, max: 96, step: 2, unit: 'px' },

    ...borderFields(),
    { type: 'separator', label: t('Disposizione') },
    { key: 'posizione', label: t('Posizione'), type: 'select', options: [
      { value: 'in_linea', label: t('Nel punto della pagina') },
      { value: 'fisso', label: t('Fisso nell’angolo dello schermo') },
    ]},
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'distanza_bordo', label: t('Distanza dal bordo'), type: 'range', min: 8, max: 80, step: 2, unit: 'px',
      condition: { field: 'posizione', op: 'eq', value: 'fisso' } },
    { key: 'distanza_basso', label: t('Distanza dal basso'), type: 'range', min: 8, max: 160, step: 2, unit: 'px',
      condition: { field: 'posizione', op: 'eq', value: 'fisso' } },
  ],
};
