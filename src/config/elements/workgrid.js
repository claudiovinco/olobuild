import { ratioOptions } from './_imageFrame.js';
import { demo } from '../demoMedia.js';
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
    typography_preset: '',
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

    card_style:  'none',
    card_radius: { tl: 0, tr: 0, br: 0, bl: 0, linked: true },
    card_bg:     '',
  },

  // Appena nata: i lavori scelti di uno studio, quattro foto vere su due colonne — una fila in
  // formato alto e una orizzontale (le alte stanno nella stessa fila: la griglia non lascia buchi)
  // — con titolo, anno e categoria, descrizione e zoom al passaggio.
  partenza: {
    items: [
      { image: demo('ufficio'), media_label: '', title: t('Studio Ferri'), meta: t('\'25 — Interni'), description: t('Uffici luminosi per quaranta persone, con sale riunioni e spazi comuni.'), link_url: '', tall: true },
      { image: demo('citta-tetti'), media_label: '', title: t('Palazzo Ricci'), meta: t('\'25 — Restauro'), description: t('Un palazzo dell\'Ottocento restituito al centro storico, tetti compresi.'), link_url: '', tall: true },
      { image: demo('architettura'), media_label: '', title: t('Residenza Aurora'), meta: t('\'26 — Architettura'), description: t('Dodici appartamenti affacciati sul verde, disegnati attorno alla luce naturale.'), link_url: '', tall: false },
      { image: demo('facciata-vetro'), media_label: '', title: t('Torre Levante'), meta: t('\'24 — Concorso'), description: t('La proposta per una sede direzionale: vetro, acciaio e un giardino in quota.'), link_url: '', tall: false },
    ],
    media_bg: 'var(--olo-color-surface-alt, #f6f7f9)',
    media_label_color: 'var(--olo-color-text, #1f2937)',
    title_color: 'var(--olo-color-text, #1f2937)',
    meta_color: 'var(--olo-color-text-soft, #6b7280)',
    desc_color: 'var(--olo-color-text-soft, #6b7280)',
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
    { key: 'items_gap', label: t('Gap'),        type: 'range', min: 8, max: 60, step: 2, responsive: true },

    { type: 'separator', label: t('Card') },
    // «Nessuno» = immagine e testo sul fondo della sezione (com'era); «Ombra» e «Bordo»
    // chiudono immagine e testo in un riquadro. Il raggio va sull'immagine o sul riquadro.
    { key: 'card_style', label: t('Stile card'), type: 'select', options: [
      { value: 'none',   label: t('Nessuno') },
      { value: 'shadow', label: t('Ombra') },
      { value: 'border', label: t('Bordo') },
    ]},
    // Solo col riquadro: con «Nessuno» la card non ha un fondo suo.
    { key: 'card_bg', label: t('Sfondo card'), type: 'color',
      description: t('Vuoto: il colore della superficie del tema con «Ombra», nessun fondo con «Bordo». Su una sezione scura scegli un fondo che faccia leggere i testi.'),
      condition: { field: 'card_style', operator: 'in', value: ['shadow', 'border'] } },
    { key: 'card_radius', label: t('Raggio card'), type: 'border-radius' },

    { type: 'separator', label: t('Media') },
    // Niente voce «Auto» in queste due tendine: il contenitore del media non ha
    // un'altezza propria, vive solo dell'aspect-ratio. Toglierlo farebbe collassare
    // a zero le card senza immagine (il placeholder a strisce). '16/10' non sta nel
    // set canonico ma resta selezionabile: è uno dei rapporti storici di questa tile.
    { key: 'media_aspect', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ auto: false, extra: ['16/10'] }) },
    { key: 'media_tall_aspect', label: t('Proporzioni card alta'), type: 'select',
      options: ratioOptions({ auto: false }) },
    { key: 'media_bg',          label: t('Sfondo placeholder'),  type: 'color' },
    { key: 'media_label_color', label: t('Colore label/strisce'), type: 'color' },
    { key: 'hover_zoom',        label: t('Zoom immagine al hover'), type: 'toggle' },
    // Un punto focale solo per tutte le immagini della griglia: se fosse per card,
    // le foto si muoverebbero una diversa dall'altra dentro la stessa fila.
    { key: 'object_position',   label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { ratio: 'media_aspect', fit: '(cover)' } },

    { type: 'separator', label: t('Titolo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'title_size', weight: 'title_weight', color: 'title_color', family: 'title_font_family' }, sizeMin: 14, sizeMax: 48 },

    { type: 'separator', label: t('Meta e descrizione') },
    { key: 'mono_font_family', label: t('Font meta (vuoto = mono del tema)'), type: 'font-family' },

    { type: 'typography', label: t('Meta'), responsiveKeys: [], keys: { size: 'meta_size', color: 'meta_color' }, sizeMin: 10, sizeMax: 18 },

    { type: 'typography', label: t('Descrizione'), responsiveKeys: [], keys: { size: 'desc_size', color: 'desc_color' }, sizeMin: 12, sizeMax: 20 },
  ],
};
