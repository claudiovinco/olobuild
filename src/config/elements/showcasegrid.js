import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Showcase Grid — griglia di card-immagine grandi e linkate: media (slot/strisce) + velo,
 * kicker + titolo grande in basso, freccia circolare in alto a destra che si ANIMA all'hover
 * (cerchio → accento, freccia che ruota). Estratta dal blueprint OLOthemes "CardGrid/teams"
 * (verdano .teams-grid/.team). Render Vue == PHP (ShowcaseGridTile.vue). Nessun JS.
 */
export default {
  type: 'showcasegrid',
  name: t('Showcase Grid (card + freccia)'),
  icon: 'dashicons-grid-view',
  category: 'media',

  defaults: {
    items: [
      { image: '', media_label: "Men's squad", kicker: '3 squads', title: 'Men', link: '#' },
      { image: '', media_label: "Women's squad", kicker: '1 squad', title: 'Women', link: '#' },
      { image: '', media_label: 'Youth squad', kicker: '4 squads · U14–U21', title: 'Youth', link: '#' },
    ],
    columns: 3,
    gap: 18,
    aspect: '3/3.5',
    object_position: 'center center',
    radius: 20,
    media_bg: '#0f3a2a',
    veil_color: '#0a2a1e',
    kicker_color: '',
    title_color: '#ffffff',
    arrow_bg: 'rgba(255,255,255,0.14)',
    arrow_color: '#ffffff',
    arrow_hover_bg: '',
    arrow_hover_color: '#0a2a1e',
    show_arrow: true,
    title_size: 34,
    title_weight: '900',
    title_uppercase: true,
    kicker_size: 12,

    // Spaziatura interna card — default = ESATTAMENTE il padding attuale (26px) → no-op.
    card_padding: { top: 26, right: 26, bottom: 26, left: 26 },
    // Raggio card a 4 angoli (OVERRIDE gated): default {0,0,0,0} → si usa il legacy `radius` → no-op.
    card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

    // KIT standard OLObuild (contenitore) — sfondo completo + ombra + bordo.
    // Default no-op: con i default il render resta identico a prima.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Card') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { image: '', media_bg: { type: 'none' }, media_label: 'Etichetta media', kicker: 'Kicker', title: 'Titolo', link: '#', span: 0, aspect: '' },
      itemFields: [
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'media_bg', label: t('Sfondo / media (ogni tipo)'), type: 'background', showParallax: false },
        { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
        { key: 'kicker', label: t('Kicker (sopra il titolo)'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link' },
        { key: 'span', label: t('Colonne occupate (0 = uniforme; 1-12 = editoriale)'), type: 'range', min: 0, max: 12, step: 1 },
        { key: 'aspect', label: t('Proporzioni (es. 4/5, vuoto = generale)'), type: 'text' },
      ],
    },
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 4, step: 1, responsive: true },
  ],

  styleFields: [
    { type: 'separator', label: t('Forma') },
    { key: 'aspect', label: t('Proporzioni'), type: 'select', options: [
      { value: '3/3.5', label: '3:3.5 (alto)' },
      { value: '1/1', label: '1:1' },
      { value: '4/5', label: '4:5' },
      { value: '3/4', label: '3:4' },
    ]},
    { key: 'gap', label: t('Spazio tra card (px)'), type: 'range', min: 8, max: 32, step: 2 },
    // Punto focale GLOBALE applicato a TUTTE le card (object-position). Default 'center center' → no-op.
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true,
      contextKeys: { ratio: 'aspect' } },

    { type: 'separator', label: t('Raggio') },
    { key: 'radius', label: t('Raggio (px)'), type: 'range', min: 0, max: 40, step: 1 },
    // 4 angoli indipendenti. Default {0,0,0,0} → si usa il raggio legacy sopra (no-op).
    { key: 'card_radius', label: t('Raggio card a 4 angoli (0 = usa raggio sopra)'), type: 'border-radius' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'card_padding', label: t('Padding interno card (px)'), type: 'spacing', max: 80 },

    { type: 'separator', label: t('Colori') },
    { key: 'media_bg', label: t('Sfondo media'), type: 'color' },
    { key: 'veil_color', label: t('Velo (gradiente)'), type: 'color' },
    { key: 'kicker_color', label: t('Kicker (vuoto = accento tema)'), type: 'color' },
    { key: 'kicker_size', label: t('Dim. kicker (px)'), type: 'range', min: 8, max: 16, step: 1 },
    { key: 'title_color', label: t('Titolo'), type: 'color' },
    { key: 'title_size', label: t('Dim. titolo (px)'), type: 'range', min: 14, max: 56, step: 1 },
    { key: 'title_weight', label: t('Peso titolo'), type: 'select', options: [
      { value: '400', label: '400' }, { value: '500', label: '500' }, { value: '600', label: '600' }, { value: '700', label: '700' }, { value: '900', label: '900' },
    ]},
    { key: 'title_uppercase', label: t('Titolo maiuscolo'), type: 'toggle' },

    { type: 'separator', label: t('Freccia') },
    { key: 'show_arrow', label: t('Mostra freccia'), type: 'toggle' },
    withHover({ key: 'arrow_bg', label: t('Cerchio (hover vuoto = accento)'), type: 'color' }, { hoverKey: 'arrow_hover_bg' }),
    withHover({ key: 'arrow_color', label: t('Freccia'), type: 'color' }, { hoverKey: 'arrow_hover_color' }),

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
