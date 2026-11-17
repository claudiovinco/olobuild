import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, focalField, focalDefault } from './_shared.js';
import { t } from '@/i18n';

// Ore intere per «Cresce dalle / Fino alle» del conteggio dal vivo: 00:00 … 24:00.
const ORE = Array.from({ length: 25 }, (_, h) => ({ value: String(h), label: String(h).padStart(2, '0') + ':00' }));

/**
 * Tile Counter — split CONTENUTO/STILE.
 *   fields[]      → number, label, prefix, suffix, icon, conteggio dal vivo, sfondo (image/video)
 *   styleFields[] → preset, bg, typo, text-effects, icon size, tipografia, colori, overlay, padding, radius, shadow, border
 */
export default {
  type: 'counter',
  name: t('Contatore'),
  icon: 'dashicons-performance',
  category: 'marketing',

  // Unificazione sfondo: i campi bg_type/bg_color/bg_image/bg_video legacy confluiscono
  // nel pannello unico media_bg (immagine/video/gradiente/colore…). Non distruttivo:
  // le chiavi vecchie restano come fallback nei renderer.
  bgMigrate: { typeKey: 'bg_type', colorKey: 'bg_color', imageKey: 'bg_image', imagePosKey: 'bg_image_object_position', videoKey: 'bg_video' },

  defaults: {
    media_bg: { type: 'none' },
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    number: '1250',
    label: t('Clienti soddisfatti'),
    prefix: '',
    suffix: '+',
    icon_emoji: 'bolt',
    // «Dal vivo» (14 ott 2026): il numero cresce da solo col tempo (kWh prodotti oggi, pallet usciti
    // quest'anno). «Numero» è il valore all'inizio del periodo; spento = il contatore di sempre.
    vivo: false,
    vivo_ritmo: '',
    vivo_unita: 'giorno',
    vivo_da: 'giorno',
    vivo_data: '',
    vivo_ora_inizio: '0',
    vivo_ora_fine: '24',
    vivo_decimali: 0,
    icon_size: '40',
    text_color: '',
    number_font_size: '48',
    number_font_weight: '700',
    label_color: '',
    label_font_size: '14',
    label_font_weight: '400',
    bg_type: 'color',
    bg_color: '',
    bg_image: '',
    ...focalDefault('bg_image'),
    bg_video: '',
    overlay: false,
    overlay_color: 'var(--olo-color-dark, #16263d)',
    overlay_opacity: '50',
    tile_padding: { top: 32, right: 32, bottom: 32, left: 32 },
    border_radius: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'label',
  },

  // Appena nata: un numero da vetrina in un riquadro tenue nel colore primario, con icona,
  // cifra grande col suffisso colorato ed etichetta; raggio morbido.
  partenza: {
    number: '1.250',
    icon_emoji: 'users',
    icon_size: '32',
    text_color: 'var(--olo-color-text)',
    number_font_size: '56',
    number_font_weight: '800',
    label_color: 'var(--olo-color-text-muted)',
    label_font_size: '15',
    label_font_weight: '500',
    media_bg: { type: 'solid', color: 'color-mix(in srgb, var(--olo-color-primary) 8%, transparent)' },
    overlay_color: 'var(--olo-color-dark, #16263d)',
    tile_padding: { top: 36, right: 28, bottom: 36, left: 28 },
    border_radius: '20',
  },

  fields: [
    { key: 'number', label: t('Numero'), type: 'text' },
    { key: 'label', label: t('Etichetta'), type: 'text' },
    { key: 'prefix', label: t('Prefisso'), type: 'text' },
    { key: 'suffix', label: t('Suffisso'), type: 'text' },
    { key: 'icon_emoji', label: t('Icona'), type: 'icon' },

    { type: 'separator', label: t('Conteggio') },
    { key: 'vivo', label: t('Dal vivo'), type: 'toggle',
      description: t('Il numero cresce da solo al ritmo scelto, anche mentre la pagina è aperta: i kWh prodotti oggi, i pallet usciti quest’anno. «Numero» è il valore all’inizio del periodo.') },
    { key: 'vivo_ritmo', label: t('Quanto cresce'), type: 'number', min: 0, step: 0.1,
      condition: { field: 'vivo', value: true } },
    { key: 'vivo_unita', label: t('Ritmo'), type: 'select', options: [
      { value: 'minuto', label: t('Al minuto') },
      { value: 'ora',    label: t('All’ora') },
      { value: 'giorno', label: t('Al giorno') },
    ], condition: { field: 'vivo', value: true } },
    { key: 'vivo_da', label: t('Conta da'), type: 'select', options: [
      { value: 'giorno', label: t('Mezzanotte (oggi)') },
      { value: 'anno',   label: t('1° gennaio (quest’anno)') },
      { value: 'data',   label: t('Una data') },
    ], condition: { field: 'vivo', value: true } },
    { key: 'vivo_data', label: t('Data di partenza'), type: 'date',
      condition: [ { field: 'vivo', value: true }, { field: 'vivo_da', op: 'eq', value: 'data' } ] },
    { key: 'vivo_ora_inizio', label: t('Cresce dalle'), type: 'select', options: ORE.slice(0, 24),
      description: t('Fuori da queste ore il numero resta fermo: le ore di sole per il fotovoltaico, i turni per un magazzino. Un ritmo al giorno si distribuisce in queste ore.'),
      condition: { field: 'vivo', value: true } },
    { key: 'vivo_ora_fine', label: t('Fino alle'), type: 'select', options: ORE.slice(1),
      condition: { field: 'vivo', value: true } },
    { key: 'vivo_decimali', label: t('Decimali'), type: 'range', min: 0, max: 2, step: 1,
      condition: { field: 'vivo', value: true } },

  ],

  // 17 nov 2026 (episodio 44): lo Stile nelle zone Aspetto · Testo · Forma; il velo sul media dal Contenuto allo Stile,
  // accanto allo Sfondo; stili onesti coi colori del sito (erano dodici nomi inglesi a colori fissi che scrivevano la
  // vecchia chiave bg_color invece dello Sfondo); le unità nei cursori.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'semplice', label: t('Semplice') },
      { value: 'riquadro', label: t('Riquadro tenue') },
      { value: 'scheda',   label: t('Scheda') },
      { value: 'pieno',    label: t('Pieno') },
      { value: 'scuro',    label: t('Scuro') },
      { value: 'custom',   label: t('Personalizzato') },
    ], description: t('Cambia insieme sfondo, testi, raggio e spazi, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.') },
    { key: 'media_bg', label: t('Sfondo'), type: 'background', showParallax: false,
      description: t('Immagine, video, gradiente o colore dietro il numero.') },
    { key: 'overlay', label: t('Velo scuro sul media'), type: 'toggle' },
    { key: 'overlay_color', label: t('Colore del velo'), type: 'color',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_opacity', label: t('Opacità del velo'), type: 'range', min: 10, max: 100, step: 5, unit: '%',
      condition: { field: 'overlay', value: true } },
    ...shadowField,
    ...borderFields(),

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Numero'),
      responsiveKeys: [],
      keys: {
        size:   'number_font_size',
        weight: 'number_font_weight',
        color:  'text_color',
      },
      sizeMin: 20, sizeMax: 120, sizeStep: 2,
    },
    // Il PHP colorava il suffisso con `number_color`, che i temi salvano ma l'inspector
    // non offriva: vuoto = primario, come prima.
    { key: 'number_color', label: t('Colore suffisso'), type: 'color',
      condition: { field: 'suffix', op: 'neq', value: '' } },
    { type: 'typography', label: t('Etichetta'),
      responsiveKeys: [],
      keys: {
        size:   'label_font_size',
        weight: 'label_font_weight',
        color:  'label_color',
      },
      sizeMin: 10, sizeMax: 32, sizeStep: 1,
    },
    // Gli effetti del testo hanno un loro separatore: dopo le tipografie, se no Numero ed Etichetta finivano nella
    // sezione «Effetti testo».
    ...textEffectsFields([ { value: 'label', label: t('Solo Etichetta') } ]),

    { type: 'separator', label: t('Forma') },
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 80 },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),
    { key: 'icon_size', label: t('Dimensione icona'), type: 'range', min: 16, max: 80, step: 2, unit: 'px' },
  ],
};
