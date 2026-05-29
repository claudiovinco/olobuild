
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Divider — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → testo/emoji centrale
 *   styleFields[] → stile linea, larghezza, spessore, colori, allineamento, spaziatura, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'divider',
  name: t('Divisore'),
  icon: 'dashicons-minus',
  category: 'essential',
  defaults: {
    style: 'solid',
    width: '100',
    thickness: '1',
    color: '',
    alignment: 'center',
    spacing: '16',
    text: '',
    text_color: '',
    text_size: '14',
    icon_emoji: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'text', label: t('Testo centrale'), type: 'text' },
    { key: 'icon_emoji', label: t('Icona / emoji centrale'), type: 'icon' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Linea') },
    { key: 'style', label: t('Stile linea'), type: 'select', options: [
      { value: 'solid', label: t('Continua') },
      { value: 'dashed', label: t('Tratteggiata') },
      { value: 'dotted', label: t('Puntinata') },
      { value: 'double', label: t('Doppia') },
      { value: 'gradient', label: t('Gradiente') },
      { value: 'fade', label: t('Sfumata') },
      { value: 'shadow', label: t('Con ombra') },
      { value: 'wave', label: t('Onda') },
      { value: 'zigzag', label: t('Zigzag') },
      { value: 'dots', label: t('Puntini decorativi') },
      { value: 'diamonds', label: t('Diamanti') },
    ]},
    { key: 'width', label: t('Larghezza (%)'), type: 'range', min: 10, max: 100, step: 5 },
    { key: 'thickness', label: t('Spessore (px)'), type: 'range', min: 1, max: 10, step: 1 },
    { key: 'color', label: t('Colore linea'), type: 'color' },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'spacing', label: t('Spaziatura (px)'), type: 'range', min: 0, max: 80, step: 4 },

    { type: 'separator', label: t('Testo centrale') },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'text_size', label: t('Dimensione testo (px)'), type: 'range', min: 10, max: 32, step: 1 },

    ...borderFields(),
  ],
};
