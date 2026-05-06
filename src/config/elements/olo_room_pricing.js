
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'olo_room_pricing',
  name: 'Sala - Tariffa',
  icon: 'dashicons-money-alt',
  category: 'olo-space',
  defaults: {
    style: 'card',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Aspetto' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'card', label: 'Card con sfondo' },
      { value: 'flat', label: 'Piatto (senza sfondo)' },
    ]},
    ...borderFields(),
  ],
};
