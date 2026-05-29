
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Hero — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle badge/indirizzo, testo CTA, toggle overlay
 *   styleFields[] → sfondo, tipografia, altezza, opacità overlay, bordo
 */
export default {
  type: 'olo_room_hero',
  name: t('Sala - Hero'),
  icon: 'dashicons-format-image',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    height: 420,
    overlay: true,
    overlay_opacity: 45,
    show_badge: true,
    show_address: true,
    cta_text: 'Prenota questa sala',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Contenuto') },
    { key: 'show_badge', label: t('Mostra badge tipologia'), type: 'toggle' },
    { key: 'show_address', label: t('Mostra indirizzo'), type: 'toggle' },
    { key: 'cta_text', label: t('Testo pulsante CTA'), type: 'text' },
    { key: 'overlay', label: t('Mostra overlay scuro'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'separator', label: t('Layout') },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 200, max: 800, step: 10 },

    { type: 'separator', label: t('Overlay') },
    { key: 'overlay_opacity', label: t('Opacità overlay (%)'), type: 'range', min: 0, max: 100, step: 5 },
    ...borderFields(),
  ],
};
