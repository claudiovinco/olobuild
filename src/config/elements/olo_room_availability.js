
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Availability — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo, toggle legenda/navigazione, mesi navigabili
 *   styleFields[] → sfondo, tipografia, stile card, raggio celle (con hover), colori stato, bordo
 */
export default {
  type: 'olo_room_availability',
  name: t('Sala - Disponibilità'),
  icon: 'dashicons-calendar-alt',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    title: t('Disponibilità'),
    show_legend: true,
    show_navigation: true,
    months_ahead: '3',
    color_free: '',
    color_partial: '',
    color_full: '',
    color_closed: '',
    card_style: true,
    day_radius: '4',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Contenuto') },
    { key: 'title', label: t('Titolo sezione'), type: 'select', options: [
      { value: 'Disponibilità', label: t('Disponibilità') },
      { value: 'Calendario disponibilità', label: t('Calendario disponibilità') },
      { value: 'Verifica disponibilità', label: t('Verifica disponibilità') },
      { value: '', label: t('Nessun titolo') },
    ]},
    { key: 'show_legend', label: t('Mostra legenda colori'), type: 'toggle' },
    { key: 'show_navigation', label: t('Navigazione tra mesi'), type: 'toggle' },
    { key: 'months_ahead', label: t('Mesi navigabili in avanti'), type: 'select', options: [
      { value: '1', label: t('1 mese') },
      { value: '2', label: t('2 mesi') },
      { value: '3', label: t('3 mesi') },
      { value: '6', label: t('6 mesi') },
      { value: '12', label: t('12 mesi') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Aspetto') },
    { key: 'card_style', label: t('Stile card con sfondo'), type: 'toggle' },
    withHover({ key: 'day_radius', label: t('Raggio celle giorno (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Colori stato') },
    { key: 'color_free', label: t('Libero'), type: 'color' },
    { key: 'color_partial', label: t('Parzialmente occupato'), type: 'color' },
    { key: 'color_full', label: t('Occupato'), type: 'color' },
    { key: 'color_closed', label: t('Chiuso'), type: 'color' },
    ...borderFields(),
  ],
};
