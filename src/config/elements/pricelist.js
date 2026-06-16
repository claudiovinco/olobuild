import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile PriceList — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (content-items), toggle mostra immagine, posizione prezzo (layout)
 *   styleFields[] → preset, sfondo creativo, typography preset, effetti testo,
 *                   card (sfondo/bordo/radius/hover), separatore, dimensione immagine, radius immagine,
 *                   gap, padding, colori (titolo/prezzo/descrizione/highlighted), badge (sfondo/colore/bordo/radius),
 *                   ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'pricelist',
  name: t('Lista prezzi'),
  icon: 'dashicons-list-view',
  category: 'content',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    items: [
      { id: 'pl-1', title: t('Bruschetta'), description: t('Pomodoro fresco, basilico e olio EVO'), price: '€8', image_url: '', highlighted: false, badge: '' },
      { id: 'pl-2', title: t('Risotto ai funghi porcini'), description: t('Riso Carnaroli mantecato con porcini freschi'), price: '€14', image_url: '', highlighted: false, badge: '' },
      { id: 'pl-3', title: t('Tiramisù'), description: t('Mascarpone, savoiardi e caffè espresso'), price: '€7', image_url: '', highlighted: false, badge: 'Consigliato' },
    ],
    separator_style: 'dotted',
    separator_color: '',
    title_color: '',
    price_color: '',
    description_color: '',
    image_size: '60',
    image_border_radius: '8',
    image_object_position: 'center center',
    show_image: true,
    price_position: 'right',
    highlighted_bg: '',
    badge_bg: '',
    badge_color: '',
    badge_border_color: '',
    badge_border_width: '0',
    badge_border_style: 'solid',
    badge_border_radius: '6',
    gap: '12',
    tile_padding: { top: 14, right: 14, bottom: 14, left: 14 },
    card_bg: '',
    card_border_radius: '12',
    card_border_color: '',
    hover_lift: true,
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
    // ── Items ──
    { key: 'items', label: t('Elementi'), type: 'content-items',
      itemFields: [
        { key: 'title', label: t('Nome'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'text' },
        { key: 'price', label: t('Prezzo'), type: 'text' },
        { key: 'image_url', label: t('Immagine'), type: 'image' },
        { key: 'highlighted', label: t('In evidenza'), type: 'toggle' },
        { key: 'badge', label: t('Badge'), type: 'text' },
      ],
      newItemDefaults: { title: t('Nuovo piatto'), description: t('Descrizione del piatto'), price: '€0', image_url: '', highlighted: false, badge: '' },
      itemLabel: 'Piatto',
    },

    // ── Immagine ──
    { type: 'separator', label: t('Immagine') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },

    // ── Layout ──
    { type: 'separator', label: t('Layout') },
    { key: 'price_position', label: t('Posizione prezzo'), type: 'select', options: [
      { value: 'right', label: t('A destra') },
      { value: 'below', label: t('Sotto il titolo') },
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
    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'title_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Descrizione'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'description_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Prezzo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'price_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Badge'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'badge_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    // ── Card ──
    { type: 'separator', label: t('Card') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'card_border_color', label: t('Bordo card'), type: 'color' },
    withHover({ key: 'card_border_radius', label: t('Arrotondamento card (px)'), type: 'border-radius'}),
    { key: 'hover_lift', label: t('Effetto hover'), type: 'toggle' },

    // ── Separatore ──
    { type: 'separator', label: t('Separatore') },
    { key: 'separator_style', label: t('Stile separatore'), type: 'select', options: [
      { value: 'dotted', label: t('Puntinato') },
      { value: 'dashed', label: t('Tratteggiato') },
      { value: 'solid', label: t('Continuo') },
      { value: 'none', label: t('Nessuno') },
    ]},
    { key: 'separator_color', label: t('Colore separatore'), type: 'color',
      condition: { field: 'separator_style', operator: '!=', value: 'none' } },

    // ── Immagine ──
    { type: 'separator', label: t('Immagine') },
    { key: 'image_size', label: t('Dimensione immagine (px)'), type: 'range', min: 30, max: 120, step: 5,
      condition: { field: 'show_image', value: true } },
    withHover({ key: 'image_border_radius', label: t('Arrotondamento immagine (px)'), type: 'border-radius',
      condition: { field: 'show_image', value: true } }),
    // Punto focale GLOBALE: le immagini sono per-item ma il frame è un quadrato 1:1 fisso
    // (dimensione unica image_size). Nessuna chiave tile-level per src/fit/ratio → contextKeys
    // vuoto: il pad resta neutro (cover) senza far trapelare l'immagine di una singola voce.
    { key: 'image_object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true,
      contextKeys: {},
      condition: { field: 'show_image', value: true } },

    // ── Spaziatura ──
    { type: 'separator', label: t('Spaziatura') },
    { key: 'gap', label: t('Gap tra elementi (px)'), type: 'range', min: 0, max: 32, step: 2 },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 32 },

    // ── Colori extra (non gestiti dai popover Tipografia) ──
    { type: 'separator', label: t('Colori extra') },
    { key: 'highlighted_bg', label: t('Sfondo evidenziato'), type: 'color' },

    // ── Badge ──
    { type: 'separator', label: t('Badge') },
    { key: 'badge_bg', label: t('Sfondo'), type: 'color' },
    { key: 'badge_border_color', label: t('Colore bordo'), type: 'color' },
    { key: 'badge_border_width', label: t('Spessore bordo (px)'), type: 'range', min: 0, max: 5, step: 1 },
    { key: 'badge_border_style', label: t('Stile bordo'), type: 'select', options: [
      { value: 'solid', label: t('Continuo') },
      { value: 'dashed', label: t('Tratteggiato') },
      { value: 'dotted', label: t('Puntinato') },
    ], condition: { field: 'badge_border_width', operator: '!=', value: '0' } },
    withHover({ key: 'badge_border_radius', label: t('Arrotondamento (px)'), type: 'border-radius'}),

    ...shadowField,
    ...borderFields(),
  ],
};
