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

  // Appena nata: un frammento HTML sobrio e leggibile (gli orari di apertura in un riquadro
  // con filo e raggio), scritto coi colori del sito: si vede subito che qui va del markup proprio.
  partenza: {
    html_content: '<div style="padding:24px 28px;border:1px solid var(--olo-color-border);border-radius:12px;color:var(--olo-color-text);">\n  <p style="margin:0 0 8px;font-weight:600;">' + t('Orari di apertura') + '</p>\n  <p style="margin:0;color:var(--olo-color-text-muted);">' + t('Lunedì – venerdì: 9:00 – 18:00') + '<br>' + t('Sabato: 9:00 – 13:00 · Domenica chiuso') + '</p>\n</div>',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'html_content', label: t('Contenuto HTML'), type: 'code' },
    { key: 'sandbox', label: t('Sandbox (iframe)'), type: 'toggle',
      description: t('Esegue l\'HTML in un iframe isolato (più sicuro per codice esterno). L\'iframe prende l\'altezza del contenuto e dentro valgono caratteri, colori e token del tema.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    ...shadowField,
  ],
};
