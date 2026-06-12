import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Film Reel — reel orizzontale cinematografico di progetti ("Lavori").
 * Estratta pixel-perfect dal blueprint "Clod — Evoluzione v2" (section.reel):
 * scroller drag + rotella + snap, fotogrammi a tre altezze, overlay REC con
 * viewfinder + timecode all'hover, skew dei fotogrammi proporzionale alla
 * velocità di scroll, barra di progresso. Render Vue == PHP (FilmReelTile.vue
 * / Olo_FilmReel_Tile). Runtime inline scoped per istanza (no '&&').
 */
export default {
  type: 'filmreel',
  name: t('Film Reel (Lavori)'),
  icon: 'dashicons-format-video',
  category: 'media',

  defaults: {
    title: 'Lavori',
    show_title: true,
    hint_text: 'Trascina · rotella · scorri in orizzontale',
    show_hint: true,
    intro_eyebrow: 'Selezione · photograph & video',
    intro_text: 'Nove progetti tra industria, retail, ritratto ed eventi. Trascina i tuoi fotogrammi nelle cornici.',
    show_intro: true,
    items: [
      { image: '', media_label: 'Comifo — still', name: 'Comifo', tag: 'Industriale · Video', size: 'tall', link: '' },
      { image: '', media_label: 'Valorizza', name: 'Valorizza', tag: 'Retail · Video', size: 'short', link: '' },
      { image: '', media_label: 'Confesercenti', name: 'Confesercenti', tag: 'Istituzionale', size: 'normal', link: '' },
      { image: '', media_label: 'Foto tecniche', name: 'Foto tecniche', tag: 'Industria · Foto', size: 'short', link: '' },
      { image: '', media_label: 'Wedding', name: 'Wedding', tag: 'Event · Video', size: 'tall', link: '' },
      { image: '', media_label: 'Darja Wilson', name: 'Darja Wilson', tag: 'Ritratto', size: 'normal', link: '' },
      { image: '', media_label: 'Antibrina', name: 'Antibrina', tag: 'Industriale', size: 'short', link: '' },
      { image: '', media_label: 'Industry', name: 'Industry', tag: 'Industria · Foto', size: 'normal', link: '' },
      { image: '', media_label: 'Event', name: 'Event', tag: 'Evento · Video', size: 'tall', link: '' },
    ],
    rec_overlay: true,
    velocity_skew: true,
    skew_max: '7',
    progress_bar: true,
    scroll_mode: 'free',
    progress_color: '',
    accent: '',
    bg_color: '',
    border_color: '',

    // Spaziatura (gated): padding verticale di base clamp(42px,6vw,78px) 0.
    // Override attivo SOLO se pad_custom=true → no-op coi default.
    pad_custom: false,
    padding: { top: 78, right: 0, bottom: 78, left: 0 },

    // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
    // Default no-op: bg none / shadow none / border 0 → render invariato.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Intestazione') },
    { key: 'show_title', label: t('Mostra titolo'), type: 'toggle' },
    { key: 'title', label: t('Titolo'), type: 'text',
      condition: { field: 'show_title', op: 'eq', value: true } },
    { key: 'show_hint', label: t('Mostra suggerimento'), type: 'toggle' },
    { key: 'hint_text', label: t('Testo suggerimento'), type: 'text',
      condition: { field: 'show_hint', op: 'eq', value: true } },

    { type: 'separator', label: t('Introduzione') },
    { key: 'show_intro', label: t('Mostra introduzione'), type: 'toggle' },
    { key: 'intro_eyebrow', label: t('Occhiello'), type: 'text',
      condition: { field: 'show_intro', op: 'eq', value: true } },
    { key: 'intro_text', label: t('Testo introduzione'), type: 'textarea',
      condition: { field: 'show_intro', op: 'eq', value: true } },

    { type: 'separator', label: t('Fotogrammi') },
    { key: 'items', label: t('Progetti'), type: 'content-items',
      itemLabel: t('Fotogramma'),
      newItemDefaults: { image: '', media_bg: { type: 'none' }, media_label: 'Progetto', name: 'Progetto', tag: '', size: 'normal', link: '' },
      itemFields: [
        { key: 'media_bg', label: t('Media (video, immagine, sfondo…)'), type: 'background',
          description: t('Riempimento del fotogramma: video, immagine, colore o gradiente. Ha precedenza sull\'Immagine qui sotto.') },
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'media_label', label: t('Etichetta placeholder'), type: 'text',
          description: t('Mostrata al centro del fotogramma quando manca il media.') },
        { key: 'name', label: t('Nome progetto'), type: 'text' },
        { key: 'tag', label: t('Tag (categoria)'), type: 'text' },
        { key: 'size', label: t('Altezza'), type: 'select', options: [
          { value: 'normal', label: t('Normale') },
          { value: 'tall', label: t('Alto') },
          { value: 'short', label: t('Basso') },
        ]},
        { key: 'link', label: t('Link (opzionale)'), type: 'link' },
      ],
    },

    { type: 'separator', label: t('Scorrimento') },
    { key: 'scroll_mode', label: t('Modalità'), type: 'select', options: [
      { value: 'free', label: t('Libero') },
      { value: 'pin', label: t('Bloccato') },
    ], description: t('Libero: drag + rotella sul reel. Bloccato: la sezione resta a schermo e lo scroll di pagina guida il reel fino in fondo, senza poterlo saltare (con riduzione del movimento torna libero).') },

    { type: 'separator', label: t('Effetti cinema') },
    { key: 'rec_overlay', label: t('Overlay REC all\'hover'), type: 'toggle',
      description: t('Cornici viewfinder, scanline e timecode che conta sui fotogrammi.') },
    { key: 'velocity_skew', label: t('Inclinazione da velocità'), type: 'toggle',
      description: t('I fotogrammi si inclinano in proporzione alla velocità di scorrimento.') },
    { key: 'skew_max', label: t('Inclinazione max (°)'), type: 'number', min: 0, max: 20, step: 1,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
    { key: 'progress_bar', label: t('Barra di progresso'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Colore accento'), type: 'color' },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    { key: 'border_color', label: t('Colore linee'), type: 'color' },
    { key: 'progress_color', label: t('Colore barra progresso'), type: 'color',
      condition: { field: 'progress_bar', op: 'eq', value: true } },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato'), type: 'toggle',
      description: t('Di base il padding verticale è responsivo: clamp(42px,6vw,78px).') },
    { key: 'padding', label: t('Padding (px)'), type: 'spacing', max: 200,
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
