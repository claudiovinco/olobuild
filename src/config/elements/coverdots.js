import { t } from '@/i18n';

/**
 * Tile CoverDots — pallini di navigazione per i gruppi di sezioni
 * "Sticky → Cover orizzontale" (motore nativo cover-h).
 * Un pallino per sezione del gruppo: attivo segue lo scroll (evento
 * 'olo:hgroup' del renderer), click = salto alla fermata. Pensata per
 * i template header: se la pagina non ha gruppi cover-h si nasconde.
 *   fields[]      → items (etichetta+colore per pallino), auto-hide
 *   styleFields[] → misure cerchio/pallino, bordo, glow attivo
 */
export default {
  type: 'coverdots',
  name: t('Pallini Cover'),
  icon: 'dashicons-ellipsis',
  category: 'interactive',
  defaults: {
    items: [],
    hide_without_group: true,
    dot_size: 34,
    dot_gap: 4,
    dot_inner: 9,
    border_color: '',
    dot_bg: '',
    dot_color: '',
    active_glow: true,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'items', label: t('Fermate'), type: 'content-items', itemLabel: t('Fermata'),
      hint: t('Facoltative: etichetta e colore per ogni pallino. Se vuote, i pallini vengono creati dal gruppo Cover orizzontale della pagina.'),
      newItemDefaults: { label: '', color: '' },
      itemFields: [
        { key: 'label', label: t('Etichetta (tooltip)'), type: 'text' },
        { key: 'color', label: t('Colore'), type: 'color' },
      ] },
    { key: 'hide_without_group', label: t('Nascondi se la pagina non ha un gruppo Cover orizzontale'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'dot_size', label: t('Diametro cerchio (px)'), type: 'range', min: 20, max: 48, step: 1 },
    { key: 'dot_gap', label: t('Distanza fra cerchi (px)'), type: 'range', min: 0, max: 16, step: 1 },
    { key: 'dot_inner', label: t('Diametro pallino (px)'), type: 'range', min: 5, max: 16, step: 1 },
    { key: 'border_color', label: t('Colore bordo cerchio'), type: 'color' },
    { key: 'dot_bg', label: t('Sfondo cerchio'), type: 'color' },
    { key: 'dot_color', label: t('Colore pallino (senza colore per-fermata)'), type: 'color' },
    { key: 'active_glow', label: t('Bagliore sul pallino attivo'), type: 'toggle' },
  ],
};
