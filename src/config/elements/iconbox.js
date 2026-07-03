import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile IconBox — split CONTENUTO/STILE.
 *   fields[]      → icona, title, description, link (URL+testo)
 *   styleFields[] → preset, bg, typo, text-effects, icona aspetto, tipografia titolo, spaziature, colori, sfondo interno box, padding, radius, shadow, border
 */
export default {
  type: 'iconbox',
  name: t('Icon Box'),
  icon: 'dashicons-star-filled',
  category: 'content',

  // Unificazione sfondo box icona: i campi legacy bg_type/bg_color/bg_image (+size/focal)
  // confluiscono nel pannello unico media_bg (immagine/video/gradiente/colore…).
  // Non distruttivo: le chiavi vecchie restano come fallback nei renderer. NB: il gradiente
  // legacy (bg_type==='gradient' → bg_gradient) NON è mappabile 1:1 nel media_bg → resta
  // come fallback nel renderer (non migrato).
  bgMigrate: { typeKey: 'bg_type', colorKey: 'bg_color', imageKey: 'bg_image', imageSizeKey: 'bg_image_size', imagePosKey: 'bg_image_position' },

  defaults: {
    media_bg: { type: 'none' },
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    icon_emoji: 'star',
    title: t('Titolo Provvisorio'),
    description: t('Una breve descrizione.'),
    link_url: '',
    link_text: 'Scopri di più',
    alignment: 'center',
    text_color: '',
    icon_size: '3',
    icon_position: 'top',
    icon_bg_color: '',
    icon_bg_shape: 'circle',
    icon_color: '',
    title_font_size: '20',
    title_font_weight: '600',
    title_color: '',
    link_color: '',
    shadow: 'none',
    icon_gap: '16',
    title_gap: '8',
    desc_gap: '16',
    bg_type: 'none',
    bg_color: '',
    bg_gradient: null,
    bg_image: '',
    bg_image_size: 'cover',
    bg_image_position: 'center center',
    tile_padding: { top: 24, right: 24, bottom: 24, left: 24 },
    border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'all',
  },

  fields: [
    { key: 'icon_emoji', label: t('Icona / Emoji'), type: 'icon' },
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'description', label: t('Descrizione'), type: 'textarea' },
    { key: 'link_url', label: t('URL link'), type: 'link' },
    { key: 'link_text', label: t('Testo link'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-card',     label: t('Modern Card') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'centered-pill',   label: t('Centered Pill') },
      { value: 'horizontal-row',  label: t('Horizontal Row') },
      { value: 'glass-tile',      label: t('Glass Tile') },
      { value: 'neon-icon',       label: t('Neon Icon') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-circle', label: t('Gradient Circle') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-badge',     label: t('Retro Badge') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Allineamento') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Icona') },
    { key: 'icon_size', label: t('Dimensione icona (em)'), type: 'range', min: 1, max: 8, step: 0.5 },
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'top', label: t('Sopra') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'icon_color', label: t('Colore icona'), type: 'color' },
    { key: 'icon_bg_color', label: t('Sfondo icona'), type: 'color' },
    { key: 'icon_bg_shape', label: t('Forma sfondo icona'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'rounded', label: t('Arrotondato') },
    ], condition: { field: 'icon_bg_color', operator: '!=', value: '' } },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'title_font_size',
        weight: 'title_font_weight',
        color:  'title_color',
      },
      sizeMin: 14, sizeMax: 48, sizeStep: 1,
    },
    { type: 'typography', label: t('Descrizione'),
      presetKey: 'typography_preset',
      keys: {
        color: 'text_color',
      },
    },
    { type: 'typography', label: t('Link'),
      presetKey: 'typography_preset',
      keys: {
        color: 'link_color',
      },
    },

    { type: 'separator', label: t('Spaziatura elementi') },
    { key: 'icon_gap', label: t('Distanza icona-titolo (px)'), type: 'range', min: 0, max: 48, step: 2 },
    { key: 'title_gap', label: t('Distanza titolo-testo (px)'), type: 'range', min: 0, max: 32, step: 2 },
    { key: 'desc_gap', label: t('Distanza testo-link (px)'), type: 'range', min: 0, max: 48, step: 2 },

    { type: 'separator', label: t('Sfondo box icona') },
    { key: '_bg_hint', type: 'description', label: '', description: t('Sfondo specifico del box icona (interno). Per lo sfondo del wrapper esterno usa il tab Stile → Sfondo.') },
    { key: 'media_bg', type: 'background', showParallax: false, label: t('Sfondo box (immagine, video, gradiente, colore…)') },

    { type: 'separator', label: t('Bordo e spaziatura') },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 60 },
    withHover({ key: 'border_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),

    ...shadowField,
    ...borderFields(),
  ],
};
