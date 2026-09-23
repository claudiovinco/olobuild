import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField } from './_shared.js';
import { ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Tile Marquee — split CONTENUTO/STILE.
 *   fields[]      → content_type (text/images), text_items, separator, images, image_height, movimento (speed/direction/pause_hover/gap)
 *   styleFields[] → preset, bg, typo, colori (bg/text), tipografia (size/weight/spacing/transform), altezza, full_width, bordi top/bottom + color, shadow, border
 */
export default {
  type: 'marquee',
  name: t('Nastro Scorrevole'),
  icon: 'dashicons-slides',
  category: 'media',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    content_type: 'text',
    text_items: 'Testo scorrevole di esempio',
    separator: ' — ',
    images: [],
    image_height: '40',
    // CORNICE dei loghi, SENZA maschera di ritaglio. Qui `images` e' una galleria
    // (type:'gallery'), non un repeater: un solo selettore di proporzioni imporrebbe
    // la stessa forma a TUTTI i marchi insieme, ed e' esattamente cio' che il
    // `width:auto` del nastro evita — ogni logo tiene la sua sagoma. Resta il solo
    // ADATTAMENTO, che nasce 'contain' (era cablato nella regola .olo-mq-img) e NON
    // 'cover' come nella tile Immagine: su un logo 'cover' taglierebbe via i bordi.
    image_fit: 'contain',
    image_object_position: 'center center',
    speed: '30',
    direction: 'left',
    pause_hover: true,
    drag_scroll: false,          // scorrimento libero: nastro trascinabile (mouse/touch)
    gap: '60',
    // VelocitySkew (reattivo allo scroll) — default OFF: i Marquee esistenti restano invariati
    velocity_skew: false,
    vskew_base_speed: 0.6,
    vskew_scroll_boost: 0.6,
    vskew_max_skew: 14,
    vskew_damping: 0.86,
    bg_color: 'var(--olo-color-dark, #16263d)',
    text_color: '',
    font_size: '16',
    font_weight: '500',
    letter_spacing: '1',
    text_transform: 'uppercase',
    font_family: '',
    font_style: 'normal',
    separator_color: '',
    separator_size: '',
    height: '50',
    full_width: false,
    border_top: '0',
    border_bottom: '0',
    border_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'content_type', label: t('Tipo contenuto'), type: 'select', options: [
      { value: 'text', label: t('Testo') },
      { value: 'images', label: t('Immagini') },
    ]},
    { key: 'text_items', label: t('Testo'), type: 'text',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'separator', label: t('Separatore'), type: 'text',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'images', label: t('Immagini'), type: 'gallery',
      condition: { field: 'content_type', value: 'images' } },

    { type: 'separator', label: t('Movimento') },
    { key: 'speed', label: t('Durata ciclo (sec)'), type: 'range', min: 5, max: 80, step: 5 },
    { key: 'direction', label: t('Direzione'), type: 'select', options: [
      { value: 'left', label: t('Verso sinistra') },
      { value: 'right', label: t('Verso destra') },
    ]},
    { key: 'pause_hover', label: t('Pausa al passaggio mouse'), type: 'toggle' },
    { key: 'drag_scroll', label: t('Trascinabile (scorrimento libero)'), type: 'toggle',
      description: t('Il nastro si può trascinare con mouse o dito; al rilascio riprende a scorrere da solo. Il loop resta continuo.') },
    { key: 'gap', label: t('Gap elementi'), type: 'range', min: 20, max: 120, step: 10 },

    { type: 'separator', label: t('Velocity Skew (reattivo allo scroll)') },
    { key: 'velocity_skew', label: t('Inclina con la velocità di scroll'), type: 'toggle',
      description: t('Lo scroll aggiunge spinta e inclinazione al nastro, che si smorza al fermarsi. Rispetta prefers-reduced-motion (solo drift base).') },
    { key: 'vskew_base_speed', label: t('Velocità base (drift)'), type: 'range', min: 0, max: 3, step: 0.1,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
    { key: 'vskew_scroll_boost', label: t('Spinta da scroll'), type: 'range', min: 0, max: 2, step: 0.05,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
    { key: 'vskew_max_skew', label: t('Inclinazione massima (°)'), type: 'range', min: 0, max: 30, step: 1,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
    { key: 'vskew_damping', label: t('Smorzamento'), type: 'range', min: 0.5, max: 0.98, step: 0.01,
      condition: { field: 'velocity_skew', op: 'eq', value: true } },
  ],

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

    { type: 'separator', label: t('Aspetto immagini') },
    { key: 'image_height', label: t('Altezza immagini'), type: 'range', min: 20, max: 120,
      condition: { field: 'content_type', value: 'images' } },
    // NIENTE selettore di proporzioni: il nastro e' fatto di loghi di sagoma diversa
    // e l'altezza e' l'unica dimensione imposta (width:auto). Una maschera comune
    // riempirebbe di bande vuote ogni marchio che non ha la forma dello slot.
    { key: 'image_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI,
      condition: { field: 'content_type', value: 'images' } },
    focalField('image', { src: '', fit: 'image_fit',
      // Senza maschera di ritaglio il focale morde solo con «Dimensione originale»,
      // dove il logo a grandezza naturale viene tagliato dall'altezza del nastro:
      // decide quale parte resta in vista.
      // La guardia e' `neq 'fill'`, come nel resto della famiglia, e NON un elenco:
      // un nastro salvato prima che il campo esistesse non ha affatto la chiave
      // `image_fit`, e l'inspector valuta le condizioni sui settings GREZZI, senza
      // fondere i default — con un `in` il valore assente non combacerebbe con niente
      // e il controllo sparirebbe per sempre.
      // L'AND si scrive come ARRAY: e' la forma che evaluateCondition() valuta davvero.
      condition: [
        { field: 'content_type', value: 'images' },
        { field: 'image_fit', op: 'neq', value: 'fill' },
      ] }),

    { type: 'separator', label: t('Tipografia'),
      condition: { field: 'content_type', value: 'text' } },
    { type: 'typography', label: t('Testo'),
      condition: { field: 'content_type', value: 'text' },
      responsiveKeys: [],
      keys: {
        size:          'font_size',
        weight:        'font_weight',
        letterSpacing: 'letter_spacing',
        transform:     'text_transform',
        color:         'text_color',
      },
      sizeMin: 12, sizeMax: 48, sizeStep: 1,
    },

    { type: 'separator', label: t('Font & separatore'),
      condition: { field: 'content_type', value: 'text' } },
    { key: 'font_family', label: t('Famiglia font'), type: 'font-family',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'font_style', label: t('Corsivo'), type: 'select',
      condition: { field: 'content_type', value: 'text' }, options: [
      { value: 'normal', label: t('Normale') },
      { value: 'italic', label: t('Corsivo') },
    ]},
    { key: 'separator_color', label: t('Colore separatore (vuoto = come testo)'), type: 'color',
      condition: { field: 'content_type', value: 'text' } },
    { key: 'separator_size', label: t('Dim. separatore (px, 0 = come testo)'), type: 'range', min: 0, max: 48, step: 1,
      condition: { field: 'content_type', value: 'text' } },

    { type: 'separator', label: t('Aspetto') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    { key: 'height', label: t('Altezza nastro'), type: 'range', min: 30, max: 120 },
    { key: 'full_width', label: t('Larghezza piena (100vw)'), type: 'toggle' },
    { key: 'border_top', label: t('Bordo superiore'), type: 'range', min: 0, max: 4 },
    { key: 'border_bottom', label: t('Bordo inferiore'), type: 'range', min: 0, max: 4 },
    { key: 'border_color', label: t('Colore bordo'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
