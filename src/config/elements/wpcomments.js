
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WpComments — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle visibilità (titolo, avatar, data, reply link, form), testo titolo, tag titolo,
 *                   commenti per pagina, ordine
 *   styleFields[] → preset, sfondo creativo, typography preset, dimensione avatar, raggio avatar,
 *                   colori (titolo, testo, autore, data, link, form bg, border), bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'wpcomments',
  name: t('Commenti'),
  icon: 'dashicons-admin-comments',
  category: 'dynamic',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_title: true,
    title_text: 'Commenti',
    title_tag: 'h3',
    show_avatar: true,
    avatar_size: '48',
    show_date: true,
    show_reply_link: true,
    show_form: true,
    comments_per_page: '10',
    order: 'desc',
    title_color: '',
    text_color: '',
    author_color: '',
    date_color: '',
    link_color: '',
    form_background: '',
    border_color: '',
    avatar_border_radius: '50',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Titolo') },
    { key: 'show_title', label: t('Mostra titolo'), type: 'toggle' },
    { key: 'title_text', label: t('Testo titolo'), type: 'text',
      condition: { field: 'show_title', operator: '==', value: true } },
    { key: 'title_tag', label: t('Tag titolo'), type: 'select', options: [
      { value: 'h2', label: t('H2') },
      { value: 'h3', label: t('H3') },
      { value: 'h4', label: t('H4') },
      { value: 'h5', label: t('H5') },
    ], condition: { field: 'show_title', operator: '==', value: true } },

    { type: 'separator', label: t('Commenti') },
    { key: 'show_avatar', label: t('Mostra avatar'), type: 'toggle' },
    { key: 'show_date', label: t('Mostra data'), type: 'toggle' },
    { key: 'show_reply_link', label: t('Mostra link rispondi'), type: 'toggle' },
    { key: 'comments_per_page', label: t('Commenti per pagina'), type: 'range', min: 5, max: 50, step: 5 },
    { key: 'order', label: t('Ordine'), type: 'select', options: [
      { value: 'desc', label: t('Più recenti prima') },
      { value: 'asc', label: t('Più vecchi prima') },
    ]},
    { key: 'show_form', label: t('Mostra modulo commento'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Avatar') },
    { key: 'avatar_size', label: t('Dimensione avatar (px)'), type: 'range', min: 24, max: 96, step: 4,
      condition: { field: 'show_avatar', operator: '==', value: true } },
    withHover({ key: 'avatar_border_radius', label: t('Raggio avatar (%)'), type: 'border-radius',
      condition: { field: 'show_avatar', operator: '==', value: true } }),

    { type: 'separator', label: t('Colori') },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'author_color', label: t('Colore autore'), type: 'color' },
    { key: 'date_color', label: t('Colore data'), type: 'color' },
    { key: 'link_color', label: t('Colore link'), type: 'color' },
    { key: 'form_background', label: t('Sfondo modulo'), type: 'color' },
    { key: 'border_color', label: t('Colore bordo'), type: 'color' },
    ...borderFields(),
  ],
};
