export default {
  type: 'togglebtn',
  name: 'Pulsante Toggle',
  icon: 'dashicons-hidden',
  category: 'content',
  defaults: {
    // Contenuto
    text_show: 'Mostra di più',
    text_hide: 'Mostra di meno',
    icon_show: 'chevron-down',
    icon_hide: 'chevron-up',
    icon_position: 'right',

    // Target
    target_id: '',
    initial_state: 'hidden',
    animation: 'collapse',
    duration: '400',

    // Stile pulsante
    btn_bg: 'transparent',
    btn_color: '#6366F1',
    btn_hover_bg: 'rgba(99,102,241,0.1)',
    btn_border_width: '2',
    btn_border_color: '#6366F1',
    btn_border_radius: '8',
    btn_padding_x: '24',
    btn_padding_y: '12',
    btn_font_size: '15',
    btn_font_weight: '600',
    btn_align: 'center',
    btn_full_width: false,
  },
  fields: [
    // ── Contenuto ──
    { key: 'text_show', label: 'Testo (quando nascosto)', type: 'text' },
    { key: 'text_hide', label: 'Testo (quando visibile)', type: 'text' },
    { key: 'icon_show', label: 'Icona mostra', type: 'select', options: [
      { value: '', label: 'Nessuna' },
      { value: 'chevron-down', label: '▼ Chevron giù' },
      { value: 'plus', label: '＋ Più' },
      { value: 'arrow-down', label: '↓ Freccia giù' },
      { value: 'eye', label: '👁 Occhio' },
    ]},
    { key: 'icon_hide', label: 'Icona nascondi', type: 'select', options: [
      { value: '', label: 'Nessuna' },
      { value: 'chevron-up', label: '▲ Chevron su' },
      { value: 'minus', label: '— Meno' },
      { value: 'arrow-up', label: '↑ Freccia su' },
      { value: 'eye-off', label: '🚫 Occhio chiuso' },
    ]},
    { key: 'icon_position', label: 'Posizione icona', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ]},

    // ── Target ──
    { type: 'separator', label: 'Sezione target' },
    { key: 'target_id', label: 'ID sezione (html_id in Avanzate)', type: 'text' },
    { key: 'initial_state', label: 'Stato iniziale sezione', type: 'select', options: [
      { value: 'hidden', label: 'Nascosta' },
      { value: 'visible', label: 'Visibile' },
    ]},
    { key: 'animation', label: 'Animazione', type: 'select', options: [
      { value: 'collapse', label: 'Collassa (altezza)' },
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'slide', label: 'Scorrimento + dissolvenza' },
    ]},
    { key: 'duration', label: 'Durata (ms)', type: 'range', min: 100, max: 800, step: 50 },

    // ── Stile pulsante ──
    { type: 'separator', label: 'Stile pulsante' },
    { key: 'btn_bg', label: 'Sfondo', type: 'color' },
    { key: 'btn_color', label: 'Colore testo', type: 'color' },
    { key: 'btn_hover_bg', label: 'Sfondo hover', type: 'color' },
    { key: 'btn_border_width', label: 'Bordo (px)', type: 'range', min: 0, max: 4 },
    { key: 'btn_border_color', label: 'Colore bordo', type: 'color' },
    { key: 'btn_border_radius', label: 'Raggio bordo (px)', type: 'range', min: 0, max: 30 },
    { key: 'btn_padding_x', label: 'Padding orizzontale (px)', type: 'range', min: 8, max: 48 },
    { key: 'btn_padding_y', label: 'Padding verticale (px)', type: 'range', min: 4, max: 24 },
    { key: 'btn_font_size', label: 'Dimensione testo (px)', type: 'range', min: 12, max: 24 },
    { key: 'btn_font_weight', label: 'Peso testo', type: 'select', options: [
      { value: '400', label: 'Normal' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'btn_align', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'btn_full_width', label: 'Larghezza piena', type: 'toggle' },
  ],
};
