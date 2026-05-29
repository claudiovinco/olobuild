
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Description — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → placeholder (tile a contenuto dinamico, descrizione dal CPT sala)
 *   styleFields[] → sfondo, tipografia, bordo
 */
export default {
  type: 'olo_room_description',
  name: t('Sala - Descrizione'),
  icon: 'dashicons-editor-paragraph',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'description', description: t('La descrizione è caricata automaticamente dal post sala corrente.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    ...borderFields(),
  ],
};
