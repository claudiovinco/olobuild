
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'olo_room_calendar',
  name: 'Sala - Calendario',
  icon: 'dashicons-calendar-alt',
  category: 'olo-space',
  defaults: {
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
  fields: [
    { type: 'separator', label: 'Contenuto' },
    { key: 'title', label: 'Titolo sezione', type: 'select', options: [
      { value: '', label: 'Nessun titolo' },
      { value: 'Prenota questa sala', label: 'Prenota questa sala' },
      { value: 'Calendario prenotazioni', label: 'Calendario prenotazioni' },
      { value: 'Disponibilità e prenotazione', label: 'Disponibilità e prenotazione' },
    ]},
    { key: 'default_view', label: 'Vista iniziale', type: 'select', options: [
      { value: 'dayGridMonth', label: 'Mese' },
      { value: 'timeGridWeek', label: 'Settimana' },
      { value: 'timeGridDay', label: 'Giorno' },
    ]},
    { key: 'show_week_view', label: 'Mostra pulsante vista settimana', type: 'toggle' },
    { key: 'show_day_view', label: 'Mostra pulsante vista giorno', type: 'toggle' },

    { type: 'separator', label: 'Orari' },
    { key: 'slot_min_time', label: 'Orario inizio visibile', type: 'select', options: [
      { value: '06:00', label: '06:00' },
      { value: '07:00', label: '07:00' },
      { value: '08:00', label: '08:00' },
      { value: '09:00', label: '09:00' },
    ]},
    { key: 'slot_max_time', label: 'Orario fine visibile', type: 'select', options: [
      { value: '18:00', label: '18:00' },
      { value: '20:00', label: '20:00' },
      { value: '22:00', label: '22:00' },
      { value: '23:00', label: '23:00' },
      { value: '24:00', label: '24:00' },
    ]},

    { type: 'separator', label: 'Aspetto' },
    { key: 'calendar_height', label: 'Altezza calendario', type: 'select', options: [
      { value: 'auto', label: 'Automatica' },
      { value: '500', label: '500px' },
      { value: '600', label: '600px' },
      { value: '700', label: '700px' },
      { value: '800', label: '800px' },
    ]},
    { key: 'header_style', label: 'Barra strumenti', type: 'select', options: [
      { value: 'full', label: 'Completa (navigazione + viste)' },
      { value: 'simple', label: 'Semplice (solo navigazione)' },
      { value: 'none', label: 'Nascosta' },
    ]},
    { key: 'show_slot_count', label: 'Mostra conteggio slot disponibili', type: 'toggle' },

    { type: 'separator', label: 'Colori sfondo giorni' },
    { key: 'color_available', label: 'Disponibile', type: 'color' },
    { key: 'color_partial', label: 'Parzialmente occupato', type: 'color' },
    { key: 'color_closed', label: 'Chiuso', type: 'color' },
    ...borderFields(),
  ],
};
