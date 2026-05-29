import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile HTML — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → codice HTML, opzione sandbox
 *   styleFields[] → ombra
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'html',
  name: t('HTML / Codice'),
  icon: 'dashicons-editor-code',
  category: 'text',
  defaults: {
    html_content: '<div style="padding:20px;text-align:center;color:var(--olo-color-text-faint, #9ca3af);">HTML personalizzato</div>',
    sandbox: false,
    shadow: 'none',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'html_content', label: t('Contenuto HTML'), type: 'textarea' },
    { key: 'sandbox', label: t('Sandbox (iframe)'), type: 'toggle',
      description: t('Esegue l\'HTML in un iframe isolato (più sicuro per codice esterno).') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    ...shadowField,
  ],
};
