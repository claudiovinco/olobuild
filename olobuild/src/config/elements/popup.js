export default {
  type: 'popup',
  name: 'Popup',
  icon: 'dashicons-external',
  category: 'content',
  defaults: {
    // Modalità
    mode: 'simple',
    // Pulsante
    button_text: 'Apri',
    button_style: 'default',
    button_size: '',
    button_icon: '',
    button_fullwidth: false,
    // Modal
    modal_size: '',
    modal_close_button: true,
    modal_title: '',
    modal_shadow: 'lg',
    modal_overlay: '60',
    modal_radius: '12',
    modal_border_width: '0',
    modal_border_color: '#374151',
    // Modo simple
    content: '<p>Contenuto del popup...</p>',
    image: '',
    image_position: 'top',
    // Modo template
    template_id: 0,
  },
  fields: [
    { type: 'separator', label: 'Pulsante' },
    { key: 'button_text', label: 'Testo pulsante', type: 'text' },
    { key: 'button_style', label: 'Stile pulsante', type: 'select', options: [
      { value: 'default', label: 'Default' },
      { value: 'primary', label: 'Primary' },
      { value: 'secondary', label: 'Secondary' },
      { value: 'danger', label: 'Danger' },
      { value: 'text', label: 'Text' },
      { value: 'link', label: 'Link' },
    ]},
    { key: 'button_size', label: 'Dimensione', type: 'select', options: [
      { value: '', label: 'Auto' },
      { value: 'small', label: 'Small' },
      { value: 'large', label: 'Large' },
    ]},
    { key: 'button_icon', label: 'Icona pulsante', type: 'icon' },
    { key: 'button_fullwidth', label: 'Larghezza piena', type: 'toggle' },

    { type: 'separator', label: 'Contenuto Popup' },
    { key: 'mode', label: 'Modalità contenuto', type: 'select', options: [
      { value: 'simple', label: 'Semplice' },
      { value: 'template', label: 'Template Olobuild' },
    ]},
    { key: 'modal_title', label: 'Titolo modale', type: 'text' },
    { key: 'content', label: 'Contenuto', type: 'editor', mode: 'block', supportsDynamic: true },
    { key: 'image', label: 'Immagine', type: 'image', supportsDynamic: true },
    { key: 'image_position', label: 'Posizione immagine', type: 'select', options: [
      { value: 'top', label: 'Sopra' },
      { value: 'bottom', label: 'Sotto' },
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'template_id', label: 'Template', type: 'select', optionsSource: 'templates' },

    { type: 'separator', label: 'Modale' },
    { key: 'modal_size', label: 'Dimensione modale', type: 'select', options: [
      { value: '', label: 'Auto' },
      { value: 'container', label: 'Container' },
      { value: 'full', label: 'Full screen' },
    ]},
    { key: 'modal_shadow', label: 'Ombra modale', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Leggera' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Grande' },
      { value: 'xl', label: 'Extra grande' },
    ]},
    { key: 'modal_radius', label: 'Bordo arrotondato', type: 'range', min: 0, max: 40, step: 1 },
    { key: 'modal_border_width', label: 'Spessore bordo', type: 'range', min: 0, max: 10, step: 1 },
    { key: 'modal_border_color', label: 'Colore bordo', type: 'color' },
    { key: 'modal_overlay', label: 'Oscuramento sfondo', type: 'range', min: 0, max: 100, step: 5 },
    { key: 'modal_close_button', label: 'Pulsante chiudi', type: 'toggle' },
  ],
};
