import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'marquee',
  name: 'Nastro Scorrevole',
  icon: 'dashicons-slides',
  category: 'content',
  defaults: {
    // Contenuto
    content_type: 'text',
    text_items: 'Testo scorrevole di esempio',
    separator: ' — ',
    images: [],
    image_height: '40',

    // Movimento
    speed: '30',
    direction: 'left',
    pause_hover: true,
    gap: '60',

    // Aspetto
    bg_color: '#111827',
    text_color: '#FFFFFF',
    font_size: '16',
    font_weight: '500',
    letter_spacing: '1',
    text_transform: 'uppercase',
    height: '50',
    full_width: true,
    border_top: '0',
    border_bottom: '0',
    border_color: '#374151',
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    // ── Contenuto ──
    { key: 'content_type', label: 'Tipo contenuto', type: 'select', options: [
      { value: 'text', label: 'Testo' },
      { value: 'images', label: 'Immagini' },
    ]},
    { key: 'text_items', label: 'Testo', type: 'editor', mode: 'inline',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'separator', label: 'Separatore', type: 'text',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'images', label: 'Immagini', type: 'gallery',
      condition: { field: 'content_type', value: 'images' } },
    { key: 'image_height', label: 'Altezza immagini (px)', type: 'range', min: 20, max: 120,
      condition: { field: 'content_type', value: 'images' } },

    // ── Movimento ──
    { type: 'separator', label: 'Movimento' },
    { key: 'speed', label: 'Durata ciclo (sec)', type: 'range', min: 5, max: 80, step: 5 },
    { key: 'direction', label: 'Direzione', type: 'select', options: [
      { value: 'left', label: 'Verso sinistra' },
      { value: 'right', label: 'Verso destra' },
    ]},
    { key: 'pause_hover', label: 'Pausa al passaggio mouse', type: 'toggle' },
    { key: 'gap', label: 'Spazio tra elementi (px)', type: 'range', min: 20, max: 120, step: 10 },

    // ── Aspetto ──
    { type: 'separator', label: 'Aspetto' },
    { key: 'bg_color', label: 'Colore sfondo', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 12, max: 48,
      condition: { field: 'content_type', value: 'text' } },
    { key: 'font_weight', label: 'Peso testo', type: 'select', options: [
      { value: '400', label: 'Normal' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
      { value: '900', label: 'Black' },
    ], condition: { field: 'content_type', value: 'text' } },
    { key: 'letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: 0, max: 8,
      condition: { field: 'content_type', value: 'text' } },
    { key: 'text_transform', label: 'Trasformazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'MAIUSCOLO' },
      { value: 'lowercase', label: 'minuscolo' },
    ], condition: { field: 'content_type', value: 'text' } },
    { key: 'height', label: 'Altezza nastro (px)', type: 'range', min: 30, max: 120 },
    { key: 'full_width', label: 'Larghezza piena (100vw)', type: 'toggle' },
    { key: 'border_top', label: 'Bordo superiore (px)', type: 'range', min: 0, max: 4 },
    { key: 'border_bottom', label: 'Bordo inferiore (px)', type: 'range', min: 0, max: 4 },
    { key: 'border_color', label: 'Colore bordo', type: 'color' },
    shadowField,
    ...borderFields,
  ],
};
