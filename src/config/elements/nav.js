import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Nav — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (label+url+icona), visibilità icone, target link, nofollow
 *   styleFields[] → preset, bg, typo, direzione, allineamento, stile, indicatore attivo, hover, colori, separatori, padding, gap
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'nav',
  name: t('Nav'),
  icon: 'dashicons-menu',
  category: 'navigation',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    items: [
      { id: 'n-1', title: t('Home'), content: '#', tag: 'home' },
      { id: 'n-2', title: t('Chi siamo'), content: '#', tag: 'users' },
      { id: 'n-3', title: t('Servizi'), content: '#', tag: 'settings' },
      { id: 'n-4', title: t('Contatti'), content: '#', tag: 'mail' },
    ],
    style: 'default',
    direction: 'vertical',
    alignment: 'left',
    show_icons: true,
    icon_position: 'left',
    icon_size: '16',
    font_size: '14',
    font_weight: '400',
    text_transform: 'none',
    letter_spacing: '0',
    link_color: '',
    link_hover_color: '',
    active_color: '',
    icon_color: '',
    separator: false,
    separator_color: '',
    gap: '4',
    tile_padding: { top: 8, right: 12, bottom: 8, left: 12 },
    border_radius: '6',
    active_style: 'left-border',
    active_bg: '',
    hover_bg: '',
    hover_effect: 'none',
    open_in_new_tab: false,
    nofollow: false,
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'items', label: t('Elementi'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: t('Etichetta'), type: 'text' },
        { key: 'content', label: t('URL'), type: 'link', placeholder: t('https://...') },
        { key: 'tag', label: t('Icona'), type: 'icon' },
      ],
      newItemDefaults: { title: t('Nuovo link'), content: '#', tag: '' },
      itemLabel: 'Link',
    },

    { type: 'separator', label: t('Icone') },
    { key: 'show_icons', label: t('Mostra icone'), type: 'toggle' },

    { type: 'separator', label: t('Comportamento link') },
    { key: 'open_in_new_tab', label: t('Apri in nuova scheda'), type: 'toggle' },
    { key: 'nofollow', label: 'rel="nofollow"', type: 'toggle' },
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

    { type: 'separator', label: t('Layout') },
    { key: 'direction', label: t('Direzione'), type: 'select', options: [
      { value: 'vertical', label: t('Verticale') },
      { value: 'horizontal', label: t('Orizzontale') },
    ]},
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'stretch', label: t('Espandi') },
    ]},
    { key: 'gap', label: t('Gap tra elementi (px)'), type: 'range', min: 0, max: 24, step: 2 },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 32 },

    { type: 'separator', label: t('Stile voci') },
    { key: 'style', label: t('Stile predefinito'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'pill', label: t('Pill') },
      { value: 'underline', label: t('Sottolineato') },
      { value: 'boxed', label: t('Riquadro') },
      { value: 'minimal', label: t('Minimale') },
    ]},
    { key: 'active_style', label: t('Indicatore attivo'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'left-border', label: t('Bordo sinistro') },
      { value: 'bottom-border', label: t('Bordo inferiore') },
      { value: 'background', label: t('Sfondo') },
      { value: 'bold', label: t('Grassetto') },
      { value: 'dot', label: t('Punto') },
    ]},
    withHover({ key: 'border_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'slide-bg', label: t('Sfondo slide') },
      { value: 'underline', label: t('Sottolineatura') },
      { value: 'lift', label: t('Solleva') },
    ]},

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Voci'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:          'font_size',
        weight:        'font_weight',
        transform:     'text_transform',
        letterSpacing: 'letter_spacing',
        color:         'link_color',
        colorHover:    'link_hover_color',
      },
      sizeMin: 11, sizeMax: 24, sizeStep: 1,
    },

    { type: 'separator', label: t('Stile icone') },
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ], show: s => s.show_icons },
    { key: 'icon_size', label: t('Dim. icona (px)'), type: 'range', min: 12, max: 28, step: 2, show: s => s.show_icons },
    { key: 'icon_color', label: t('Colore icona'), type: 'color', show: s => s.show_icons },

    { type: 'separator', label: t('Colori') },
    { key: 'active_color', label: t('Colore attivo'), type: 'color' },
    withHover({ key: 'active_bg',  label: t('Sfondo attivo'), type: 'color' }, { hoverKey: 'hover_bg' }),
    { key: 'separator', label: t('Mostra separatori'), type: 'toggle' },
    { key: 'separator_color', label: t('Colore separatore'), type: 'color', show: s => s.separator },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),
    ...shadowField,
    ...borderFields(),
  ],
};
