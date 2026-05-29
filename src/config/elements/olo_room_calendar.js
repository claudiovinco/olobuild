
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Calendar — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo, vista iniziale, toggle viste/conteggio, orari visibili
 *   styleFields[] → sfondo, tipografia, altezza, stile barra, colori sfondo giorni, bordo
 */
export default {
  type: 'olo_room_calendar',
  name: t('Sala - Calendario'),
  icon: 'dashicons-calendar-alt',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    title: '',
    default_view: 'dayGridMonth',
    show_week_view: true,
    show_day_view: true,
    calendar_height: 'auto',
    slot_min_time: '07:00',
    slot_max_time: '22:00',
    color_available: '',
    color_partial: '',
    color_closed: '',
    show_slot_count: true,
    header_style: 'full',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Contenuto') },
    { key: 'title', label: t('Titolo sezione'), type: 'select', options: [
      { value: '', label: t('Nessun titolo') },
      { value: 'Prenota questa sala', label: t('Prenota questa sala') },
      { value: 'Calendario prenotazioni', label: t('Calendario prenotazioni') },
      { value: 'Disponibilità e prenotazione', label: t('Disponibilità e prenotazione') },
    ]},
    { key: 'default_view', label: t('Vista iniziale'), type: 'select', options: [
      { value: 'dayGridMonth', label: t('Mese') },
      { value: 'timeGridWeek', label: t('Settimana') },
      { value: 'timeGridDay', label: t('Giorno') },
    ]},
    { key: 'show_week_view', label: t('Mostra pulsante vista settimana'), type: 'toggle' },
    { key: 'show_day_view', label: t('Mostra pulsante vista giorno'), type: 'toggle' },
    { key: 'show_slot_count', label: t('Mostra conteggio slot disponibili'), type: 'toggle' },

    { type: 'separator', label: t('Orari') },
    { key: 'slot_min_time', label: t('Orario inizio visibile'), type: 'select', options: [
      { value: '06:00', label: '06:00' },
      { value: '07:00', label: '07:00' },
      { value: '08:00', label: '08:00' },
      { value: '09:00', label: '09:00' },
    ]},
    { key: 'slot_max_time', label: t('Orario fine visibile'), type: 'select', options: [
      { value: '18:00', label: '18:00' },
      { value: '20:00', label: '20:00' },
      { value: '22:00', label: '22:00' },
      { value: '23:00', label: '23:00' },
      { value: '24:00', label: '24:00' },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Aspetto') },
    { key: 'calendar_height', label: t('Altezza calendario'), type: 'select', options: [
      { value: 'auto', label: t('Automatica') },
      { value: '500', label: t('500px') },
      { value: '600', label: t('600px') },
      { value: '700', label: t('700px') },
      { value: '800', label: t('800px') },
    ]},
    { key: 'header_style', label: t('Barra strumenti'), type: 'select', options: [
      { value: 'full', label: t('Completa (navigazione + viste)') },
      { value: 'simple', label: t('Semplice (solo navigazione)') },
      { value: 'none', label: t('Nascosta') },
    ]},

    { type: 'separator', label: t('Colori sfondo giorni') },
    { key: 'color_available', label: t('Disponibile'), type: 'color' },
    { key: 'color_partial', label: t('Parzialmente occupato'), type: 'color' },
    { key: 'color_closed', label: t('Chiuso'), type: 'color' },
    ...borderFields(),
  ],
};
