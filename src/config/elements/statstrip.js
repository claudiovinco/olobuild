import { t } from '@/i18n';

/**
 * Stat Strip — banda orizzontale di statistiche/metriche (valore grande + etichetta),
 * con divisori tra le celle. Token-first: valore dal font heading del tema, etichetta in mono.
 * I valori sono testo libero (es. "500+", "92%", "40M", "14x").
 */
export default {
  type: 'statstrip',
  name: t('Stat Strip'),
  icon: 'dashicons-chart-bar',
  category: 'layout',

  defaults: {
    typography_preset: '',
    items: [
      { value: '500+', label: 'Progetti consegnati' },
      { value: '12', label: 'Anni di attività' },
      { value: '98%', label: 'Clienti soddisfatti' },
      { value: '40M', label: 'Utenti raggiunti' },
    ],
    columns: 4,
    items_gap: 0,
    band_padding_y: 40,

    show_dividers: true,
    divider_color: 'var(--olo-color-border, #e5e7eb)',
    band_border: true,

    value_font_family: 'heading',
    value_color: 'var(--olo-color-text, #1f2937)',
    value_size: 48,
    value_weight: '600',

    label_color: 'var(--olo-color-text-soft, #6b7280)',
    label_size: 13,
    label_uppercase: false,
    label_weight: '',

    align: 'left',
    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Statistiche') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Statistica'),
      defaults: { value: '00', label: 'Etichetta' },
      itemFields: [
        { key: 'value', label: t('Valore (es. 500+, 92%)'), type: 'text' },
        { key: 'label', label: t('Etichetta'), type: 'text' },
      ],
    },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Banda') },
    { key: 'columns',        label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1, responsive: true },
    { key: 'band_padding', label: t('Padding banda'), type: 'spacing', min: 0, max: 120,
      legacyKeys: { y: 'band_padding_y' } },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},

    { type: 'separator', label: t('Divisori') },
    { key: 'show_dividers', label: t('Divisori tra le celle'), type: 'toggle' },
    { key: 'band_border',   label: t('Bordo sopra e sotto'),   type: 'toggle' },
    { key: 'divider_color', label: t('Colore linee'),          type: 'color' },

    { type: 'separator', label: t('Valore') },
    // Lo stile tipografico vale per il SOLO valore (l'etichetta resta in mono):
    // a stile collegato il controllo sotto nasconde famiglia e peso.
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Valore'), responsiveKeys: [], linkedPresetKey: 'typography_preset',
      keys: { family: 'value_font_family', size: 'value_size', weight: 'value_weight', color: 'value_color' }, sizeMin: 20, sizeMax: 96 },

    { type: 'separator', label: t('Etichetta') },
    // La famiglia dell'etichetta stava in un campo a parte («Font etichetta»):
    // ora è nel controllo tipografia, chiave INVARIATA (mono_font_family, vuoto =
    // mono del tema).
    { type: 'typography', label: t('Etichetta'), responsiveKeys: [],
      keys: { family: 'mono_font_family', size: 'label_size', weight: 'label_weight', color: 'label_color', uppercase: 'label_uppercase' }, sizeMin: 10, sizeMax: 22 },
  ],
};
