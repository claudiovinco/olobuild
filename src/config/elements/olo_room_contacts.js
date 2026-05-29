import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Room Contacts — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo sezione
 *   styleFields[] → sfondo, tipografia, effetti testo, stile (card/flat), bordo
 */
export default {
  type: 'olo_room_contacts',
  name: t('Sala - Contatti'),
  icon: 'dashicons-phone',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    style: 'card',
    title: t('Contatti'),
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Contenuto') },
    { key: 'title', label: t('Titolo sezione'), type: 'text' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    ...textEffectsFields([ { value: 'title', label: t('Solo Titolo') } ]),

    { type: 'separator', label: t('Aspetto') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'card', label: t('Card con sfondo') },
      { value: 'flat', label: t('Piatto (senza sfondo)') },
    ]},
    ...borderFields(),
  ],
};
