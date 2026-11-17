import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile IconList — split CONTENUTO/STILE.
 *   fields[]      → items (icon+text+link+color), layout (vertical/horizontal), divider toggle
 *   styleFields[] → preset, bg, typo, text-effects, icona aspetto (color/size/shape/bg), testo aspetto, gap, divider color, shadow, border
 */
export default {
  type: 'iconlist',
  name: t('Lista icone'),
  icon: 'dashicons-list-view',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    items: [
      { id: 'il-1', icon: 'check', text: t('Prima voce della lista'), color: '' },
      { id: 'il-2', icon: 'check', text: t('Seconda voce della lista'), color: '' },
      { id: 'il-3', icon: 'check', text: t('Terza voce della lista'), color: '' },
    ],
    icon_color: '',
    icon_size: '20',
    text_color: '',
    text_size: '16',
    text_align: 'left',
    gap: '12',
    icon_shape: 'none',
    icon_bg_color: '',
    divider: false,
    divider_color: '',
    layout: 'vertical',
    // Padding dell'elenco: era 16 px fissi in linea nel PHP; 16 su tutti i lati = com'era.
    list_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'text',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: i recapiti di un'attività — un'icona diversa per voce, nel colore primario
  // dentro un cerchio tinto, con il filo fra le righe.
  partenza: {
    items: [
      { id: 'il-1', icon: 'location', text: t('Via Roma 24, 40121 Bologna'), color: '', link: '' },
      { id: 'il-2', icon: 'phone', text: t('051 123 4567'), color: '', link: '' },
      { id: 'il-3', icon: 'mail', text: t('info@tuosito.it'), color: '', link: '' },
      { id: 'il-4', icon: 'clock', text: t('Lunedì – venerdì, 9:00 – 18:00'), color: '', link: '' },
    ],
    icon_color: 'var(--olo-color-primary)',
    icon_shape: 'circle',
    icon_bg_color: 'color-mix(in srgb, var(--olo-color-primary) 12%, transparent)',
    divider: true,
  },

  fields: [
    { key: 'items', label: t('Voci'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'icon', label: t('Icona'), type: 'icon' },
        { key: 'text', label: t('Testo'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link', placeholder: t('https://...') },
      ],
      newItemDefaults: { icon: 'check', text: t('Nuova voce'), color: '', link: '' },
    },

  ],

  // Zone standard (16 nov 2026): Aspetto · Testo · Effetti testo · Icone · Forma · Disposizione · Voci. Prima «Preset
  // stilistico», «Tipografia», «Stile icone», «Spaziatura & separatore» e il separatore fra le voci nel Contenuto.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'clean', label: t('Pulita') },
      { value: 'essential', label: t('Essenziale') },
      { value: 'dots-full', label: t('Bollini pieni') },
      { value: 'dots-soft', label: t('Bollini tenui') },
      { value: 'squares', label: t('Quadratini') },
      { value: 'row', label: t('In riga') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Voci'),
      responsiveKeys: [],
      keys: {
        size:  'text_size',
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 28,
    },
    ...textEffectsFields([ { value: 'text', label: t('Solo il testo') } ]),

    { type: 'separator', label: t('Icone') },
    { key: 'icon_color', label: t('Colore icone'), type: 'color' },
    { key: 'icon_size', label: t('Dimensione icone'), type: 'range', unit: 'px', min: 12, max: 48, step: 2 },
    { key: 'icon_shape', label: t('Bollino'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'rounded', label: t('Arrotondato') },
    ]},
    { key: 'icon_bg_color', label: t('Colore bollino'), type: 'color',
      condition: { field: 'icon_shape', operator: '!=', value: 'none' } },

    { type: 'separator', label: t('Forma') },
    { key: 'list_padding', label: t('Padding'), type: 'spacing', min: 0, max: 80 },
    ...borderFields(),

    { type: 'separator', label: t('Disposizione') },
    { key: 'layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'vertical', label: t('Una sotto l’altra') },
      { value: 'horizontal', label: t('In riga') },
    ]},
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},
    { key: 'gap', label: t('Gap voci'), type: 'range', unit: 'px', min: 4, max: 32, step: 2 },
    { key: 'divider', label: t('Filo fra le voci'), type: 'toggle' },
    { key: 'divider_color', label: t('Colore filo'), type: 'color',
      condition: { field: 'divider', operator: '==', value: true } },

    { type: 'separator', label: t('Voci') },
    { key: 'items', type: 'content-items', label: t('Voci'), etichettaDa: 'text', itemFields: [
        { key: 'color', label: t('Colore icona'), type: 'color' },
    ] },
  ],
};
