import { t } from '@/i18n';

/**
 * Tile Fragment — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → placeholder (la tile non ha contenuto né stile, solo meta tecnico)
 *   styleFields[] → vuoto (nessuna proprietà visiva)
 *   AVANZATE      → ID HTML, classi CSS
 */
export default {
  type: 'fragment',
  name: t('Frammento'),
  icon: 'dashicons-screenoptions',
  category: 'layout',
  defaults: {
    html_id: '',
    css_classes: '',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'description', description: t('Frammento di markup wrapper. Configura ID HTML e classi CSS dal tab Avanzate.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [],
};
