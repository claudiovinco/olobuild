
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Info — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → placeholder (tile a contenuto dinamico, info dal CPT sala)
 *   styleFields[] → sfondo, tipografia, stile (card/flat), bordo
 */
export default {
  type: 'olo_room_info',
  name: t('Sala - Informazioni'),
  icon: 'dashicons-info-outline',
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
    { type: 'description', description: t('Le informazioni sono caricate automaticamente dal post sala corrente.') },
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
