import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ToTop — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → allineamento (posizione), comportamento scroll fluido
 *   styleFields[] → stile pulsante (default/primary), ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'totop',
  name: t('Torna su'),
  icon: 'dashicons-arrow-up-alt',
  category: 'navigation',
  defaults: {
    alignment: 'right',
    style: 'default',
    smooth: true,
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'smooth', label: t('Scorrimento fluido'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'primary', label: t('Primary') },
    ]},
    ...shadowField,
    ...borderFields(),
  ],
};
