import { shadowField } from './_shared.js';

export default {
  type: 'lottie',
  name: 'Lottie Animation',
  icon: 'dashicons-format-video',
  category: 'media',
  defaults: {
    json_url: '',
    width: '300',
    height: '300',
    loop: true,
    autoplay: true,
    speed: '1',
    trigger: 'autoplay',
    hover_action: 'none',
    alignment: 'center',
    shadow: 'none',
  },
  fields: [
    { key: 'json_url', label: 'File Lottie (.json)', type: 'lottie_picker' },
    { key: 'width', label: 'Larghezza (px)', type: 'range', min: 50, max: 800, step: 10 },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 50, max: 800, step: 10 },
    { key: 'loop', label: 'Ripeti', type: 'toggle' },
    { key: 'speed', label: 'Velocita', type: 'range', min: 0.1, max: 3, step: 0.1 },
    { key: 'trigger', label: 'Trigger', type: 'select', options: [
      { value: 'autoplay', label: 'Automatico' },
      { value: 'viewport', label: 'Quando visibile' },
      { value: 'hover', label: 'Al passaggio mouse' },
      { value: 'click', label: 'Al click' },
    ]},
    { key: 'hover_action', label: 'Azione hover', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'pause', label: 'Pausa' },
      { value: 'reverse', label: 'Inverti direzione' },
      { value: 'speed-up', label: 'Accelera (2x)' },
    ]},
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    ...shadowField,
  ],
};
