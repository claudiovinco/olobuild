import { t } from '@/i18n';

/**
 * Tile Luce di pagina — atmosfera: layer fisso dietro al contenuto con un
 * alone radiale il cui colore segue la sezione visibile (campo "Colore luce"
 * nelle impostazioni di sezione, o l'evento olo:hgroup dei gruppi Cover
 * orizzontale). Il cross-fade è CSS nativo (transition su background-color
 * di un layer mascherato). Decoratore a dimensione zero: una per pagina.
 *   fields[]      → colore di default, colore fondo pagina (opzionale)
 *   styleFields[] → posizione/ampiezza alone, intensità, velocità transizione
 */
export default {
  type: 'pagelight',
  name: t('Luce di pagina'),
  icon: 'dashicons-lightbulb',
  category: 'atmosphere',
  defaults: {
    light_color: '',
    base_color: '',
    position: 'center',
    size: 90,
    intensity: 26,
    transition_ms: 800,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'light_color', label: t('Colore luce di partenza'), type: 'color',
      description: t('Vuoto = primario del tema. Le sezioni con "Colore luce" lo cambiano allo scroll.') },
    { key: 'base_color', label: t('Fondo pagina (opzionale)'), type: 'color',
      description: t('Colore pieno dietro la luce, per pagine con sezioni trasparenti.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'position', label: t('Posizione alone'), type: 'select', options: [
      { value: 'center', label: t('Centro') },
      { value: 'top', label: t('Alto') },
      { value: 'top-left', label: t('Alto a sinistra') },
      { value: 'top-right', label: t('Alto a destra') },
      { value: 'spread', label: t('Diffusa (due aloni)') },
    ] },
    { key: 'size', label: t('Ampiezza alone (%)'), type: 'range', min: 40, max: 140, step: 5 },
    { key: 'intensity', label: t('Intensità (%)'), type: 'range', min: 5, max: 70, step: 1 },
    { key: 'transition_ms', label: t('Velocità transizione (ms)'), type: 'range', min: 100, max: 2500, step: 100 },
  ],
};
