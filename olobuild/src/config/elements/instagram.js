export default {
  type: 'instagram',
  name: 'Instagram',
  icon: 'dashicons-instagram',
  category: 'marketing',
  defaults: {
    url: '',
    embed_type: 'post',
    width: '100%',
    caption: true,
    background_color: '',
    border_radius: '8',
    alignment: 'center',
  },
  fields: [
    { key: 'url', label: 'URL Instagram', type: 'text', placeholder: 'https://www.instagram.com/p/...' },
    { key: 'embed_type', label: 'Tipo embed', type: 'select', options: [
      { value: 'post', label: 'Post / Reel' },
      { value: 'profile', label: 'Profilo' },
    ]},
    { key: 'width', label: 'Larghezza', type: 'text', placeholder: '100%' },
    { key: 'caption', label: 'Mostra didascalia', type: 'toggle' },
    { key: 'background_color', label: 'Colore sfondo', type: 'color' },
    { key: 'border_radius', label: 'Arrotondamento (px)', type: 'border-radius' },
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
  ],
};
