import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Subnav — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → source (manuale/wp_menu), items (manuali), menu_id/menu_depth/parent_item (WP),
 *                   toggle highlight_current, toggle divider, active_style (indicatore attivo)
 *   styleFields[] → preset, bg, typography_preset, layout style (default/pill/underline/boxed), alignment,
 *                   gap, tile_padding, border_radius, font_size/weight/transform, colori (link/hover/active/bg),
 *                   textEffectsFields, shadow, borderFields
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'subnav',
  name: t('Subnav'),
  icon: 'dashicons-ellipsis',
  category: 'navigation',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    source: 'manual',
    items: [
      { id: 'sn-1', title: t('Elemento 1'), content: '#' },
      { id: 'sn-2', title: t('Elemento 2'), content: '#' },
      { id: 'sn-3', title: t('Elemento 3'), content: '#' },
    ],
    menu_id: 0,
    menu_depth: 'top',
    parent_item: '0',
    style: 'default',
    divider: false,
    alignment: 'left',
    gap: '8',
    font_size: '14',
    font_weight: '400',
    text_transform: 'none',
    link_color: '',
    hover_color: '',
    active_color: '',
    active_style: 'none',
    bg_color: '',
    hover_bg: '',
    active_bg: '',
    border_radius: '4',
    tile_padding: { top: 8, right: 12, bottom: 8, left: 12 },
    highlight_current: true,
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
    { type: 'separator', label: t('Sorgente') },
    { key: 'source', label: t('Sorgente'), type: 'select', options: [
      { value: 'manual', label: t('Manuale') },
      { value: 'wp_menu', label: t('Menu WordPress') },
    ]},

    // Manuale
    { key: 'items', label: t('Elementi'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: t('Etichetta'), type: 'text' },
        { key: 'content', label: t('URL'), type: 'link', placeholder: t('https://...') },
      ],
      newItemDefaults: { title: t('Nuovo elemento'), content: '#' },
      show: s => s.source === 'manual',
    },

    // Menu WP
    { key: 'menu_id', label: t('Menu WordPress'), type: 'select', optionsSource: 'wpMenus',
      show: s => s.source === 'wp_menu' },
    { key: 'menu_depth', label: t('Livello da mostrare'), type: 'select', options: [
      { value: 'top', label: t('Voci principali (1 livello)') },
      { value: 'children', label: t('Figli di una voce specifica') },
      { value: 'auto', label: t('Figli della pagina corrente') },
    ], show: s => s.source === 'wp_menu' },
    { key: 'parent_item', label: t('Voce genitore'), type: 'select',
      optionsSource: 'wpMenuItems', optionsDependOn: 'menu_id',
      show: s => s.source === 'wp_menu' && s.menu_depth === 'children' },

    { type: 'separator', label: t('Comportamento') },
    { key: 'divider', label: t('Separatore'), type: 'toggle' },
    { key: 'highlight_current', label: t('Evidenzia pagina corrente'), type: 'toggle' },
    { key: 'active_style', label: t('Indicatore attivo'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'underline', label: t('Sottolineatura') },
      { value: 'background', label: t('Sfondo') },
      { value: 'bold', label: t('Grassetto') },
    ]},
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
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'pill', label: t('Pill') },
      { value: 'underline', label: t('Sottolineato') },
      { value: 'boxed', label: t('Riquadro') },
    ]},
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'stretch', label: t('Distribuito') },
    ]},
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 32, step: 2 },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 24 },
    withHover({ key: 'border_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Voci'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:      'font_size',
        weight:    'font_weight',
        transform: 'text_transform',
        color:     'link_color',
        colorHover: 'hover_color',
      },
      sizeMin: 11, sizeMax: 20, sizeStep: 1,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'active_color', label: t('Colore attivo'), type: 'color' },
    withHover({ key: 'bg_color',   label: t('Sfondo'),     type: 'color' }, { hoverKey: 'hover_bg' }),
    { key: 'active_bg', label: t('Sfondo attivo'), type: 'color' },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),
    ...shadowField,
    ...borderFields(),
  ],
};
