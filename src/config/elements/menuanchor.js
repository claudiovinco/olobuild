import { textEffectsFields, textEffectsDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Menu Anchor — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → ID ancora, etichetta builder
 *   styleFields[] → offset (px), text-effects, typography preset
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'menuanchor',
  name: t('Ancora Menu'),
  icon: 'dashicons-admin-links',
  category: 'navigation',
  defaults: {
    typography_preset: '',
    anchor_id: '',
    offset: '0',
    label: '',
    ...textEffectsDefaults,
    text_effect_target: 'label',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'anchor_id', label: t('ID ancora (senza #)'), type: 'text' },
    { key: 'label', label: t('Etichetta (solo builder)'), type: 'text',
      description: t('Visibile solo nel builder per riconoscere l\'ancora, non viene renderizzata.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Comportamento') },
    { key: 'offset', label: t('Offset per header fisso (px)'), type: 'range', min: 0, max: 200, step: 5 },

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'label', label: t('Solo Etichetta') } ]),
  ],
};
