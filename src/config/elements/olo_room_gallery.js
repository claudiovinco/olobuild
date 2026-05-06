
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'olo_room_gallery',
  name: 'Sala - Galleria',
  icon: 'dashicons-format-gallery',
  category: 'olo-space',
  defaults: {
    columns: 5,
    lightbox: true,
    main_height: 450,
    kenburns: true,
    autoplay: true,
    show_counter: true,
    show_arrows: true,
    show_dots: true,
    thumb_height: 80,
    transition: 'kenburns',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Layout' },
    { key: 'columns', label: 'Colonne miniature', type: 'range', min: 2, max: 8, step: 1 },
    { key: 'main_height', label: 'Altezza immagine principale (px)', type: 'range', min: 200, max: 800, step: 10 },
    { key: 'thumb_height', label: 'Altezza miniature (px)', type: 'range', min: 40, max: 150, step: 5 },

    { type: 'separator', label: 'Comportamento' },
    { key: 'lightbox', label: 'Apri in lightbox al click', type: 'toggle' },
    { key: 'autoplay', label: 'Avanzamento automatico', type: 'toggle' },
    { key: 'kenburns', label: 'Effetto Ken Burns', type: 'toggle' },
    { key: 'transition', label: 'Transizione', type: 'select', options: [
      { value: 'kenburns', label: 'Ken Burns' },
      { value: 'slide', label: 'Slide' },
      { value: 'fade', label: 'Dissolvenza' },
    ]},

    { type: 'separator', label: 'Controlli' },
    { key: 'show_arrows', label: 'Mostra frecce navigazione', type: 'toggle' },
    { key: 'show_dots', label: 'Mostra indicatori (dots)', type: 'toggle' },
    { key: 'show_counter', label: 'Mostra contatore immagini', type: 'toggle' },
    ...borderFields(),
  ],
};
