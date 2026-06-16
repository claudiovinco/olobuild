import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Before / After — griglia di card "prova" (risultati): coppia di media affiancati
 * con etichette Prima/Dopo + didascalia (titolo + testo). Estratta dai blueprint
 * OLOthemes (BeforeAfter: cadence "The proof"). Per il confronto a slider singolo
 * usare invece la tile `imgcompare`. Render Vue == PHP (BeforeAfterTile.vue).
 */
export default {
  type: 'beforeafter',
  name: t('Before / After (prova)'),
  icon: 'dashicons-images-alt2',
  category: 'media',

  defaults: {
    items: [
      { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Marcus · 16 weeks', text: 'Down 11kg, first-ever pull-up, and a deadlift PB he never thought he’d hit.' },
      { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Priya · 6 months', text: 'Built real strength postpartum, pain-free and back to running.' },
      { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Sam · 1 year', text: 'From couch to first powerlifting meet — and stayed for the community.' },
    ],
    columns: 3,
    gap: 24,
    media_bg: '',
    media_aspect: '1/1',
    object_position: 'center center',
    accent: '',
    before_label_color: '#ffffff',
    after_label_color: '#ffffff',
    title_color: '',
    text_color: '',
    card_bg: '',
    radius: 12,

    // Spaziatura / Forma — additivi e no-op coi default (parità PHP)
    cap_padding: { top: 16, right: 4, bottom: 4, left: 4 },
    card_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
    label_radius: { tl: 999, tr: 999, br: 999, bl: 999 },

    // Kit standard OLObuild — sfondo completo + ombra + bordo (no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Card prima/dopo') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Nome · durata', text: 'Risultato ottenuto.' },
      itemFields: [
        { key: 'before_image', label: t('Immagine "Prima"'), type: 'image' },
        { key: 'after_image', label: t('Immagine "Dopo"'), type: 'image' },
        { key: 'before_label', label: t('Etichetta Prima'), type: 'text' },
        { key: 'after_label', label: t('Etichetta Dopo'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'text', label: t('Testo risultato'), type: 'textarea' },
      ],
    },
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 4, step: 1, responsive: true },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (etichetta "Dopo")'), type: 'color',
      description: t('Sfondo dell’etichetta Dopo; vuoto = primario del tema.') },
    { key: 'before_label_color', label: t('Testo etichetta Prima'), type: 'color' },
    { key: 'after_label_color', label: t('Testo etichetta Dopo'), type: 'color' },
    { key: 'media_bg', label: t('Sfondo media'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },

    { type: 'separator', label: t('Forma') },
    { key: 'media_aspect', label: t('Proporzioni media'), type: 'select', options: [
      { value: '1/1', label: '1:1' },
      { value: '4/5', label: '4:5' },
      { value: '3/4', label: '3:4' },
      { value: '4/3', label: '4:3' },
      { value: '3/2', label: '3:2' },
    ]},
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true,
      contextKeys: { ratio: 'media_aspect', fit: 'cover' },
      description: t('Punto focale globale di tutte le immagini (prima + dopo).') },
    { key: 'radius', label: t('Raggio bordo (px)'), type: 'border-radius' },
    { key: 'gap', label: t('Spazio tra card (px)'), type: 'range', min: 8, max: 48, step: 2 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'cap_padding', label: t('Padding didascalia (px)'), type: 'spacing', max: 64,
      description: t('Padding del blocco titolo + testo sotto le immagini.') },

    { type: 'separator', label: t('Raggio') },
    { key: 'card_radius', label: t('Raggio card (px)'), type: 'border-radius',
      description: t('Arrotondamento dei 4 angoli della card. Default = raggio base.') },
    { key: 'label_radius', label: t('Raggio etichette (px)'), type: 'border-radius',
      description: t('Arrotondamento delle pillole "Prima"/"Dopo".') },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },
    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
