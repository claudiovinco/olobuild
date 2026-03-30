export default {
  type: 'olo_room_hero',
  name: 'Sala - Hero',
  icon: 'dashicons-format-image',
  category: 'olo-space',
  defaults: {
    height: 420,
    overlay: true,
    overlay_opacity: 45,
    show_badge: true,
    show_address: true,
    cta_text: 'Prenota questa sala',
  },
  fields: [
    { type: 'separator', label: 'Layout' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 200, max: 800, step: 10 },

    { type: 'separator', label: 'Overlay' },
    { key: 'overlay', label: 'Mostra overlay scuro', type: 'toggle' },
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 0, max: 100, step: 5 },

    { type: 'separator', label: 'Contenuto' },
    { key: 'show_badge', label: 'Mostra badge tipologia', type: 'toggle' },
    { key: 'show_address', label: 'Mostra indirizzo', type: 'toggle' },
    { key: 'cta_text', label: 'Testo pulsante CTA', type: 'text' },
  ],
};
