
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Pricing — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → placeholder (tile a contenuto dinamico, dati tariffa dal CPT)
 *   styleFields[] → sfondo, tipografia, stile (card/flat), bordo
 */
export default {
  type: 'olo_room_pricing',
  name: t('Sala - Tariffa'),
  icon: 'dashicons-money-alt',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    style: 'card',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'description', description: t('La tariffa è caricata automaticamente dal post sala corrente.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'separator', label: t('Aspetto') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'card', label: t('Card con sfondo') },
      { value: 'flat', label: t('Piatto (senza sfondo)') },
    ]},
    ...borderFields(),
  ],
};
