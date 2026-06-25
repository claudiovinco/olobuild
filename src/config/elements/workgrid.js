import { t } from '@/i18n';

/**
 * Work Grid — griglia di lavori/progetti con immagine (placeholder a strisce se vuota),
 * titolo + meta e descrizione, con zoom dell'immagine al passaggio del mouse.
 * Token-first: titolo dal font heading del tema; meta in monospace.
 */
export default {
  type: 'workgrid',
  name: t('Work Grid'),
  icon: 'dashicons-screenoptions',
  category: 'layout',

  defaults: {
    items: [
      { image: '', media_label: 'Marisol — identity system', title: 'Marisol',      meta: "'26 — Brand",     description: 'A coastal hotel group, rebuilt around one mark and a lot of restraint.', link_url: '', tall: false },
      { image: '', media_label: 'Atlas Press — book covers', title: 'Atlas Press',   meta: "'25 — Editorial", description: "An independent publisher's new look, from spine to site.", link_url: '', tall: true },
      { image: '', media_label: 'Field Museum — wayfinding', title: 'Field Museum',  meta: "'25 — Wayfinding", description: 'A signage and type system that quietly tells you where you are.', link_url: '', tall: true },
      { image: '', media_label: 'Cobalt — product UI',       title: 'Cobalt',        meta: "'24 — Product",   description: 'Brand and interface for a developer tool that hates noise.', link_url: '', tall: false },
    ],

    columns:   2,
    items_gap: 32,

    media_aspect:      '4/3',
    media_tall_aspect: '4/5',
    media_bg:          'var(--olo-color-surface-alt, #f6f7f9)',
    media_label_color: 'var(--olo-color-text, #1f2937)',
    hover_zoom:        true,
    object_position:   'center center',

    title_font_family: 'heading',
    title_color: 'var(--olo-color-text, #1f2937)',
    title_size:  22,
    title_weight: '500',

    meta_color: 'var(--olo-color-text-soft, #6b7280)',
    meta_size:  12,

    show_desc:  true,
    desc_color: 'var(--olo-color-text-soft, #6b7280)',
    desc_size:  15,

    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Lavori') },
    { key: 'items', label: t('Card'), type: 'content-items',
      itemLabel: t('Lavoro'),
      defaults: { image: '', media_label: 'Nuovo lavoro', title: 'Titolo', meta: "'26 — Categoria", description: 'Breve descrizione del progetto.', link_url: '', tall: false },
      itemFields: [
        { key: 'image',       label: t('Immagine'),                 type: 'image' },
        { key: 'media_label', label: t('Label placeholder (se vuota)'), type: 'text' },
        { key: 'title',       label: t('Titolo'),                   type: 'text' },
        { key: 'meta',        label: t('Meta (es. \'26 — Brand)'),  type: 'text' },
        { key: 'description', label: t('Descrizione'),              type: 'editor', mode: 'inline' },
        { key: 'tall',        label: t('Formato alto (4/5)'),       type: 'toggle' },
        { key: 'link_url',    label: t('Link'),                     type: 'link' },
      ],
    },

    { type: 'separator', label: t('Visibilità') },
    { key: 'show_desc', label: t('Mostra descrizione'), type: 'toggle' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Layout griglia') },
    { key: 'columns',   label: t('Numero colonne'),  type: 'range', min: 1, max: 4, step: 1, responsive: true },
    { key: 'items_gap', label: t('Gap (px)'),        type: 'range', min: 8, max: 60, step: 2, responsive: true },

    { type: 'separator', label: t('Media') },
    { key: 'media_aspect', label: t('Aspect ratio'), type: 'select', options: [
      { value: '16/9', label: '16 / 9' },
      { value: '16/10', label: '16 / 10' },
      { value: '4/3',  label: '4 / 3' },
      { value: '3/2',  label: '3 / 2' },
      { value: '1/1',  label: t('1 / 1 (quadrato)') },
    ]},
    { key: 'media_tall_aspect', label: t('Aspect ratio "alto"'), type: 'select', options: [
      { value: '4/5', label: '4 / 5' },
      { value: '3/4', label: '3 / 4' },
      { value: '2/3', label: '2 / 3' },
    ]},
    { key: 'media_bg',          label: t('Sfondo placeholder'),  type: 'color' },
    { key: 'media_label_color', label: t('Colore label/strisce'), type: 'color' },
    { key: 'hover_zoom',        label: t('Zoom immagine al hover'), type: 'toggle' },
    { key: 'object_position',   label: t('Posizione contenuto'), type: 'object-position', reveal: true, contextKeys: { ratio: 'media_aspect' } },

    { type: 'separator', label: t('Titolo') },
    { key: 'title_font_family', label: t('Famiglia titolo'), type: 'font-family' },
    { key: 'title_color',  label: t('Colore'),         type: 'color' },
    { key: 'title_size',   label: t('Dimensione (px)'), type: 'range', min: 14, max: 48, step: 1 },
    { key: 'title_weight', label: t('Peso'), type: 'select', options: [
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
    ]},

    { type: 'separator', label: t('Meta e descrizione') },
    { key: 'mono_font_family', label: t('Font meta (vuoto = mono del tema)'), type: 'font-family' },
    { key: 'meta_color', label: t('Colore meta'), type: 'color' },
    { key: 'meta_size',  label: t('Meta (px)'),   type: 'range', min: 10, max: 18, step: 1 },
    { key: 'desc_color', label: t('Colore descrizione'), type: 'color' },
    { key: 'desc_size',  label: t('Descrizione (px)'),   type: 'range', min: 12, max: 20, step: 1 },
  ],
};
