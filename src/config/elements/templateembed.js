import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile TemplateEmbed — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → sorgente dati (template_id)
 *   styleFields[] → ombra
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'templateembed',
  name: t('Includi template'),
  icon: 'dashicons-layout',
  category: 'layout',
  defaults: {
    template_id: 0,
    shadow: 'none',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'template_id', label: t('Template'), type: 'select', optionsSource: 'templates' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    ...shadowField,
  ],
};
