import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Tile Hotspot — split CONTENUTO/STILE.
 *   fields[]      → immagine, markers (pos_x+pos_y+title+description+icon+tooltip_position)
 *   styleFields[] → preset, bg, typo, image height + radius, text-effects, marker color/size/pulse, tooltip colori/width, border
 */
export default {
  type: 'hotspot',
  name: t('Hotspot'),
  icon: 'dashicons-location-alt',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    image: '',
    image_height: '400',
    // 'auto' = la resa di sempre: il contenitore tiene l'altezza fissa di
    // `image_height` (400px) e il ritaglio dipende dalla larghezza della colonna.
    // Un rapporto cablato qui (16/9 o altro) cambierebbe l'inquadratura di ogni
    // hotspot già pubblicato, perché i marker sono in percentuale sul riquadro.
    aspect_ratio: 'auto',
    aspect_ratio_custom: '16/9',
    // Era cablato nel CSS di entrambi i renderer: esposto, ma con lo stesso valore.
    object_fit: 'cover',
    object_position: 'center center',
    markers: [
      { id: 'hs-1', pos_x: '30', pos_y: '40', title: t('Punto di interesse'), description: t('Descrizione del primo hotspot.'), icon: 'pin', tooltip_position: 'top' },
      { id: 'hs-2', pos_x: '65', pos_y: '55', title: t('Secondo punto'), description: t('Descrizione del secondo hotspot.'), icon: 'pin', tooltip_position: 'bottom' },
    ],
    border_radius: '0',
    marker_color: '',
    marker_size: '24',
    pulse_animation: true,
    tooltip_bg: '',
    tooltip_color: '',
    tooltip_width: '220',
    ...textEffectsDefaults,
    text_effect_target: 'all',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'image', label: t('Immagine'), type: 'image' },
    { key: 'markers', label: t('Marker'), type: 'content-items',
      itemFields: [
        { key: 'pos_x', label: t('Posizione X'), type: 'range', min: 0, max: 100, step: 1 },
        { key: 'pos_y', label: t('Posizione Y'), type: 'range', min: 0, max: 100, step: 1 },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'text' },
        { key: 'icon', label: t('Icona'), type: 'icon' },
      ],
      newItemDefaults: { pos_x: '50', pos_y: '50', title: t('Nuovo punto'), description: t('Descrizione.'), icon: 'pin', tooltip_position: 'top' },
      itemLabel: 'Marker',
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-pin',      label: t('Modern Pin') },
      { value: 'minimal-dot',     label: t('Minimal Dot') },
      { value: 'magazine-numbered', label: t('Magazine Numbered') },
      { value: 'pulse-ring',      label: t('Pulse Ring') },
      { value: 'tooltip-card',    label: t('Tooltip Card') },
      { value: 'glass-pin',       label: t('Glass Pin') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-pulse',  label: t('Gradient Pulse') },
      { value: 'sticker-pin',     label: t('Sticker Pin') },
      { value: 'retro-marker',    label: t('Retro Marker') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Immagine') },
    withHover({ key: 'border_radius', label: t('Raggio immagine'), type: 'border-radius' }),
    { key: 'aspect_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions({ custom: true }),
      description: t('«Auto» tiene l’altezza fissa qui sotto, com’è sempre stato.') },
    { key: 'aspect_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4'),
      condition: { field: 'aspect_ratio', op: 'eq', value: 'custom' } },
    // Con un rapporto scelto l'altezza la calcola il rapporto: lasciare il cursore
    // visibile darebbe un controllo che non muove niente.
    // Scritto come `show` e non come `condition: eq 'auto'` perché l'inspector valuta
    // sui settings GREZZI, senza fondere i default: un hotspot salvato PRIMA di questo
    // sprint non ha affatto la chiave `aspect_ratio`, e con l'uguaglianza il cursore
    // dell'altezza sarebbe sparito proprio dalle tile già pubblicate — quelle che
    // l'altezza ce l'hanno davvero. Chiave assente = 'auto' = cursore visibile.
    { key: 'image_height', label: t('Altezza immagine'), type: 'range', min: 200, max: 800, step: 10,
      show: (s) => (s.aspect_ratio || 'auto') === 'auto' },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    { key: 'object_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'image', height: 'image_height', ratio: 'aspect_ratio', ratioCustom: 'aspect_ratio_custom', fit: 'object_fit' },
      condition: { field: 'object_fit', op: 'neq', value: 'fill' } },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Stile marker') },
    { key: 'marker_color', label: t('Colore marker'), type: 'color' },
    { key: 'marker_size', label: t('Dimensione marker'), type: 'range', min: 16, max: 40, step: 2 },
    { key: 'pulse_animation', label: t('Animazione pulse'), type: 'toggle' },

    { key: 'markers', type: 'content-items', label: t('Marker'), itemLabel: 'Marker', etichettaDa: 'title', itemFields: [
        { key: 'tooltip_position', label: t('Posizione tooltip'), type: 'select', options: [
          { value: 'top', label: t('Sopra') },
          { value: 'bottom', label: t('Sotto') },
          { value: 'left', label: t('Sinistra') },
          { value: 'right', label: t('Destra') },
        ]},
    ] },
    { type: 'separator', label: t('Stile tooltip') },
    { key: 'tooltip_bg', label: t('Sfondo tooltip'), type: 'color' },
    { key: 'tooltip_color', label: t('Colore testo tooltip'), type: 'color' },
    { key: 'tooltip_width', label: t('Larghezza tooltip'), type: 'range', min: 150, max: 350, step: 10 },

    ...borderFields(),
  ],
};
