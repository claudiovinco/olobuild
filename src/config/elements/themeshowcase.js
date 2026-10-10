import { t } from '@/i18n';
import { ratioOptions } from './_imageFrame.js';

/**
 * Vetrina temi — i temi inclusi nel plugin come siti da visitare: una scheda per tema con
 * l'anteprima che scorre, i filtri per categoria e il visore a tutto schermo con la demo dal vivo
 * (computer, tablet, telefono, temi vicini). Renderer: includes/tiles/class-themeshowcase-tile.php.
 * Esiste solo dove le demo dei temi sono accese (Olobuild_Demo_Temi): l'elenco viene dai temi del
 * plugin, non si compila a mano.
 *   fields[]      → filtri e loro etichetta, cosa mostra la scheda, pulsante del visore
 *   styleFields[] → colore e testo sul colore, scheda (sfondo, bordo, raggio, proporzione),
 *                   testi (nome, categoria), disposizione (colonne, gap, filtri)
 */
export default {
  type: 'themeshowcase',
  name: t('Vetrina temi'),
  icon: 'dashicons-admin-appearance',
  category: 'dynamic',

  defaults: {
    filter_bar: true,
    filter_all_label: t('Tutti'),
    show_category: true,
    show_zone: true,
    card_label: t('Guarda la demo'),
    cta_text: t('Usa questo tema'),
    cta_url: '',
    accent: '',
    accent_on: '',
    filter_color: '',
    card_bg: '',
    card_border: '',
    card_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
    preview_ratio: '4:3',
    name_font_family: '',
    name_font_size: '20',
    name_font_weight: '600',
    name_text_transform: 'none',
    name_letter_spacing: '0',
    name_color: '',
    meta_font_family: '',
    meta_font_size: '12',
    meta_font_weight: '600',
    meta_text_transform: 'uppercase',
    meta_letter_spacing: '1',
    meta_color: '',
    columns: 3,
    gap: 24,
    filter_align: 'center',
  },

  fields: [
    { type: 'separator', label: t('Filtri') },
    { key: 'filter_bar', label: t('Filtri per categoria'), type: 'toggle' },
    { key: 'filter_all_label', label: t('Etichetta di «tutti»'), type: 'text',
      condition: { field: 'filter_bar', op: 'eq', value: true } },

    { type: 'separator', label: t('Schede') },
    { key: 'show_category', label: t('Mostra la categoria'), type: 'toggle' },
    { key: 'show_zone', label: t("Mostra l'effetto in evidenza"), type: 'toggle',
      description: t("L'effetto che caratterizza il tema, scritto sull'anteprima.") },
    { key: 'card_label', label: t('Invito sulla scheda'), type: 'text' },

    { type: 'separator', label: t('Visore') },
    { type: 'description', text: t('Una scheda apre la demo del tema a tutto schermo: si sfoglia come un sito vero, in vista computer, tablet o telefono, e si passa ai temi vicini.') },
    { key: 'cta_text', label: t('Testo del pulsante'), type: 'text' },
    { key: 'cta_url', label: t('Collegamento del pulsante'), type: 'link',
      description: t('Vuoto: il pulsante non compare.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'accent', label: t('Colore'), type: 'color',
      description: t('Filtro attivo, invito, comandi e pulsante del visore. Vuoto: il primario del tema.') },
    { key: 'accent_on', label: t('Testo sul colore'), type: 'color' },
    { key: 'filter_color', label: t('Colore filtri'), type: 'color',
      description: t('Testo e bordo dei filtri non attivi. Vuoto: il colore del testo intorno.') },

    { type: 'separator', label: t('Scheda') },
    { key: 'card_bg', label: t('Sfondo scheda'), type: 'color' },
    { key: 'card_border', label: t('Bordo scheda'), type: 'border', legacyWidth: 1 },
    { key: 'card_radius', label: t('Raggio scheda'), type: 'border-radius' },
    { key: 'preview_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions({ auto: false, sep: ':' }) },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Nome del tema'),
      responsiveKeys: [],
      keys: {
        family:        'name_font_family',
        size:          'name_font_size',
        weight:        'name_font_weight',
        transform:     'name_text_transform',
        letterSpacing: 'name_letter_spacing',
        color:         'name_color',
      },
      sizeMin: 12, sizeMax: 40,
    },
    { type: 'typography', label: t('Categoria'),
      responsiveKeys: [],
      keys: {
        family:        'meta_font_family',
        size:          'meta_font_size',
        weight:        'meta_font_weight',
        transform:     'meta_text_transform',
        letterSpacing: 'meta_letter_spacing',
        color:         'meta_color',
      },
      sizeMin: 9, sizeMax: 20,
    },

    { type: 'separator', label: t('Disposizione') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 4, step: 1, responsive: true },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 64, step: 4 },
    { key: 'filter_align', label: t('Allineamento filtri'), type: 'select',
      condition: { field: 'filter_bar', op: 'eq', value: true },
      options: [
        { value: 'left', label: t('Sinistra') },
        { value: 'center', label: t('Centro') },
        { value: 'right', label: t('Destra') },
      ]},
  ],
};
