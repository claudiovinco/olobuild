
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'olo_room_description',
  name: 'Sala - Descrizione',
  icon: 'dashicons-editor-paragraph',
  category: 'olo-space',
  defaults: {
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    ...borderFields(),
  ],
};
