import { t } from '@/i18n';

/**
 * Scaler — zona interattiva: un input base (porzioni o peso) scala live una lista
 * di quantità. mode 'scale' = qty × (val/base); mode 'percent' = val × (qty/100)
 * (baker's %). Estratto dai demo OLOthemes (RecipeScaler/BakersCalc). Token-first.
 * Render Vue == PHP (ScalerTile.vue). Runtime senza '&&' né '<'/'>'.
 */
export default {
  type: 'scaler',
  name: t('Scaler (porzioni/peso → quantità)'),
  icon: 'dashicons-calculator',
  category: 'interactive',

  defaults: {
    eyebrow: '',
    heading: t('Scala la ricetta'),
    intro: '',
    mode: 'scale',
    base_label: t('Porzioni'),
    base_value: 4,
    base_min: 1,
    base_max: 12,
    base_step: 1,
    base_suffix: '',
    items: [
      { name: 'Ingrediente A', amount: 200, unit: 'g' },
      { name: 'Ingrediente B', amount: 2, unit: '' },
      { name: 'Ingrediente C', amount: 50, unit: 'ml' },
    ],
    show_total: false,
    total_label: t('Totale'),
    total_unit: 'g',
    zone_accent: '',
    card_bg: '',
    card_border: '',
    align: 'left',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },

    { type: 'separator', label: t('Modalità e base') },
    { key: 'mode', label: t('Modalità'), type: 'select', options: [
      { value: 'scale', label: t('Scala (qty × base) — porzioni') },
      { value: 'percent', label: t('Percentuale (baker\'s %)') },
    ]},
    { key: 'base_label', label: t('Etichetta base'), type: 'text' },
    { key: 'base_value', label: t('Valore base (riferimento)'), type: 'number' },
    { key: 'base_min', label: t('Min'), type: 'number' },
    { key: 'base_max', label: t('Max'), type: 'number' },
    { key: 'base_step', label: t('Step'), type: 'number' },
    { key: 'base_suffix', label: t('Suffisso base (es. g)'), type: 'text' },

    { type: 'separator', label: t('Ingredienti') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Ingrediente'),
      defaults: { name: 'Nuovo', amount: 100, unit: 'g' },
      itemFields: [
        { key: 'name', label: t('Nome'), type: 'text' },
        { key: 'amount', label: t('Quantità base (o % in modalità percentuale)'), type: 'number' },
        { key: 'unit', label: t('Unità'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('Totale') },
    { key: 'show_total', label: t('Mostra totale'), type: 'toggle' },
    { key: 'total_label', label: t('Etichetta totale'), type: 'text' },
    { key: 'total_unit', label: t('Unità totale'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore zona (accento)'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo pannello'), type: 'color' },
    { key: 'card_border', label: t('Bordo'), type: 'color' },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
