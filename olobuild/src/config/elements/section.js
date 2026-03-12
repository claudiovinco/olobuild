import { flexContainerFields, flexContainerDefaults, cssGridFields, cssGridDefaults } from './_shared.js';

export default {
  type: 'section',
  name: 'Sezione',
  icon: 'dashicons-align-center',
  category: 'structure',
  defaults: {
    style: 'default',
    width: 'default',
    padding: 'default',
    sticky_effect: 'none',
    sticky_top: '',
    scroll_snap: false,
    snap_dots: false,
    snap_dot_color: '',
    snap_dot_active_color: '',
    snap_dot_position: 'right',
    ...flexContainerDefaults,
    ...cssGridDefaults,
  },
  fields: [
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'muted', label: 'Attenuato' },
      { value: 'primary', label: 'Primario' },
      { value: 'secondary', label: 'Secondario' },
    ]},
    { key: 'width', label: 'Larghezza max', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'small', label: 'Piccolo' },
      { value: 'large', label: 'Grande' },
      { value: 'xlarge', label: 'Extra grande' },
      { value: 'expand', label: 'Larghezza piena' },
      { value: 'fullbleed', label: 'Bordo a bordo' },
    ]},
    { key: 'padding', label: 'Padding', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'small', label: 'Piccolo' },
      { value: 'large', label: 'Grande' },
      { value: 'xlarge', label: 'Extra grande' },
      { value: 'remove-vertical', label: 'Nessuno' },
    ]},
    { key: 'sticky_effect', label: 'Effetto sticky', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'cover', label: 'Cover' },
      { value: 'reveal', label: 'Reveal' },
      { value: 'cover-h', label: 'Cover orizzontale' },
      { value: 'reveal-h', label: 'Reveal orizzontale' },
    ]},
    { key: 'sticky_top', label: 'Offset dall\'alto (px)', type: 'text', placeholder: '0', condition: { field: 'sticky_effect', operator: '!=', value: 'none' } },

    { type: 'separator', label: 'Scroll Snap' },
    { key: 'scroll_snap', label: 'Sezione full-screen con snap', type: 'toggle' },
    { key: 'snap_dots', label: 'Navigazione pallini', type: 'toggle',
      condition: { field: 'scroll_snap', op: 'eq', value: true } },
    { key: 'snap_dot_color', label: 'Colore pallini', type: 'color',
      condition: { field: 'snap_dots', op: 'eq', value: true } },
    { key: 'snap_dot_active_color', label: 'Colore pallino attivo', type: 'color',
      condition: { field: 'snap_dots', op: 'eq', value: true } },
    { key: 'snap_dot_position', label: 'Posizione pallini', type: 'select', options: [
      { value: 'right', label: 'Destra' },
      { value: 'left', label: 'Sinistra' },
    ], condition: { field: 'snap_dots', op: 'eq', value: true } },

    ...flexContainerFields,
    ...cssGridFields,
  ],
};
