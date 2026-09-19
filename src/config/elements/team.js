import { textEffectsFields, textEffectsDefaults, filterFields, filterDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, focalField } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Tile Team — split CONTENUTO/STILE.
 *   fields[]      → photo + hover_image/video, nome, ruolo, bio, link (text+url)
 *   styleFields[] → preset, bg, typo, text-effects, foto aspetto, contenitore info, tipografia, tile aspect, filtri
 */
export default {
  type: 'team',
  name: t('Membro del team'),
  icon: 'dashicons-businessperson',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    ...filterDefaults,
    photo: '',
    hover_image: '',
    hover_video: '',
    name: 'Jane Smith',
    role: 'Lead Designer',
    bio: 'Appassionata di creare esperienze utente eccellenti.',
    link_text: 'Profilo',
    link_url: '',
    photo_size: '120',
    // CORNICE della foto. '1/1' riproduce il riquadro quadrato di sempre
    // (width = height = photo_size px): photo_size resta la sorgente della
    // LARGHEZZA, la proporzione decide solo l'altezza derivata. 'cover' era
    // cablato nei due renderer (class-team-tile.php e TeamTile.vue).
    photo_ratio: '1/1',
    photo_fit: 'cover',
    photo_object_position: 'center center',
    photo_shape: 'circle',
    photo_radius: '12',
    photo_border_width: '3',
    photo_border_color: '',
    photo_shadow: 'md',
    photo_gap: '12',
    info_bg_color: '',
    info_text_color: '',
    info_padding: { top: 24, right: 24, bottom: 24, left: 24 },
    info_width: '100',
    info_margin: '0',
    info_radius: '16',
    info_border_width: '0',
    info_border_color: '',
    info_align: 'center',
    name_size: '20',
    name_weight: '600',
    role_size: '14',
    role_color: '',
    bio_size: '14',
    bg_color: '',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    border_radius: '16',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'name',
  },

  fields: [
    { type: 'separator', label: t('Media') },
    { key: 'photo', label: t('Foto / Video'), type: 'media' },
    { key: 'hover_image', label: t('Immagine hover'), type: 'image' },
    { key: 'hover_video', label: t('Video hover'), type: 'media' },
    { type: 'separator', label: t('Contenuto') },
    { key: 'name', label: t('Nome'), type: 'text' },
    { key: 'role', label: t('Ruolo'), type: 'text' },
    { key: 'bio', label: t('Biografia'), type: 'textarea' },
    { type: 'separator', label: t('Link') },
    { key: 'link_text', label: t('Testo link'), type: 'text' },
    { key: 'link_url', label: t('URL link'), type: 'link' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'corporate-card',   label: t('Corporate Card') },
      { value: 'magazine-row',     label: t('Magazine Row') },
      { value: 'minimal-clean',    label: t('Minimal Clean') },
      { value: 'editorial-bold',   label: t('Editorial Bold') },
      { value: 'photographer',     label: t('Photographer (B&N)') },
      { value: 'glass-portrait',   label: t('Glass Portrait') },
      { value: 'neon-frame',       label: t('Neon Frame') },
      { value: 'brutalist-stamp',  label: t('Brutalist Stamp') },
      { value: 'polaroid-photo',   label: t('Polaroid') },
      { value: 'sticker-portrait', label: t('Sticker Portrait') },
      { value: 'retro-yearbook',   label: t('Retro Yearbook') },
      { value: 'tilt-3d',          label: t('3D Tilt') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'name', label: t('Solo Nome') } ]),

    { type: 'separator', label: t('Foto') },
    { key: 'photo_size', label: t('Dimensione foto'), type: 'range', min: 60, max: 250, step: 5 },
    // Niente voce automatica: il riquadro della foto non ha altro da cui prendere
    // l'altezza (e' un box di dimensione fissa), quindi senza proporzione collasserebbe.
    { key: 'photo_ratio', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ auto: false }),
      description: t('La larghezza resta «Dimensione foto»: la proporzione decide quanto è alta.') },
    { key: 'photo_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    // Il punto focale si nasconde SOLO con 'fill': con 'contain' lavora eccome (decide
    // da che parte la foto si appoggia nelle bande vuote, e i due renderer lo
    // emettono), e una scheda salvata prima che l'adattamento esistesse non ha affatto
    // la chiave `photo_fit` — l'inspector valuta le condizioni sui settings GREZZI,
    // senza fondere i default, quindi con un elenco `in` il valore assente non
    // combacerebbe con niente e il controllo sparirebbe per sempre.
    focalField('photo', { ratio: 'photo_ratio', fit: 'photo_fit',
      condition: { field: 'photo_fit', op: 'neq', value: 'fill' } }),
    { key: 'photo_shape', label: t('Maschera foto'), type: 'select', options: [
      { value: 'circle', label: t('Tonda') },
      { value: 'square', label: t('Quadrata') },
      { value: 'rounded', label: t('Arrotondata') },
      { value: 'hexagon', label: t('Esagonale') },
    ]},
    withHover({ key: 'photo_radius', label: t('Raggio foto'), type: 'border-radius',
      condition: { field: 'photo_shape', value: 'rounded' } }),
    { key: 'photo_border', label: t('Bordo foto'), type: 'border',
      legacyKeys: { width: 'photo_border_width', color: 'photo_border_color' } },
    { key: 'photo_shadow', label: t('Ombra foto'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'photo_shadow_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'photo_shadow_h', v: 'photo_shadow_v', blur: 'photo_shadow_blur', spread: 'photo_shadow_spread', color: 'photo_shadow_color', inset: 'photo_shadow_inset' },
      condition: { field: 'photo_shadow', op: 'eq', value: 'custom' } },
    { key: 'photo_gap', label: t('Gap foto-contenitore'), type: 'range', min: -40, max: 40, step: 4 },

    { type: 'separator', label: t('Contenitore info') },
    { key: 'info_bg_color', label: t('Sfondo contenitore'), type: 'color' },
    { key: 'info_width', label: t('Larghezza contenitore'), type: 'range', min: 50, max: 100, step: 5 },
    { key: 'info_margin', label: t('Margine dal tile'), type: 'spacing', max: 40 },
    { key: 'info_padding', label: t('Padding contenitore'), type: 'spacing', max: 48 },
    withHover({ key: 'info_radius', label: t('Raggio'), type: 'border-radius' }),
    { key: 'info_border', label: t('Bordo scheda'), type: 'border',
      legacyKeys: { width: 'info_border_width', color: 'info_border_color' } },
    { key: 'info_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Nome'),
      responsiveKeys: ['size'],
      keys: {
        size:   'name_size',
        weight: 'name_weight',
        color:  'info_text_color',
      },
      sizeMin: 14, sizeMax: 36, sizeStep: 1,
    },
    { type: 'typography', label: t('Ruolo'),
      responsiveKeys: ['size'],
      keys: {
        size:  'role_size',
        color: 'role_color',
      },
      sizeMin: 10, sizeMax: 24, sizeStep: 1,
    },
    { type: 'typography', label: t('Biografia'),
      responsiveKeys: ['size'],
      keys: {
        size:  'bio_size',
        color: 'info_text_color',
      },
      sizeMin: 10, sizeMax: 20, sizeStep: 1,
    },

    { type: 'separator', label: t('Aspetto tile') },
    { key: 'bg_color', label: t('Colore sfondo tile'), type: 'color' },
    { key: 'tile_padding', label: t('Padding tile'), type: 'spacing', max: 40 },
    withHover({ key: 'border_radius', label: t('Raggio tile'), type: 'border-radius' }),

    ...borderFields(),
    ...filterFields,
  ],
};
