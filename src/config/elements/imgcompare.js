
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { demo } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile Image Compare — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → 2 immagini before/after, etichette + show_labels, posizione iniziale, orientamento, autoplay+timing
 *   styleFields[] → preset, typo, slider aspect (colore/dimensione maniglia/spessore linea), altezza card, radius, fit, ombra card, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'imgcompare',
  name: t('Confronto Immagini'),
  icon: 'dashicons-image-flip-horizontal',
  category: 'media',
  defaults: {
    typography_preset: '',
    preset: 'custom',
    before_image: '',
    after_image: '',
    before_label: 'Prima',
    after_label: 'Dopo',
    show_labels: true,
    start_position: '50',
    orientation: 'horizontal',
    handle_color: '',
    handle_size: '40',
    handle_border: '3',
    line_width: '3',
    height: '400',
    // 'auto' = nessun ritaglio: resta in vigore l'Altezza in px (400), che è come
    // questa tile ha sempre reso. Qualunque altro default cambierebbe l'altezza di
    // ogni confronto già pubblicato.
    aspect_ratio: 'auto',
    aspect_ratio_custom: '16/9',
    border_radius: '8',
    object_fit: 'cover',
    object_position: 'center center',
    card_border_width: '0',
    card_border_color: '',
    card_shadow: 'none',
    autoplay: false,
    autoplay_delay: '3',
    autoplay_speed: '2',
    // Etichette (16 nov 2026): vuote = la pillola scura di sempre (12 px, maiuscolo, bianco su nero al 75%).
    label_bg: '',
    label_font_family: '',
    label_font_size: '',
    label_font_weight: '',
    label_text_transform: '',
    label_letter_spacing: '',
    label_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: il prima e dopo di uno spazio di lavoro, una foto sopra l'altra in
  // formato panoramico con la maniglia al centro da trascinare, le etichette agli angoli, raggio
  // morbido e ombra.
  partenza: {
    before_image: demo('scrivania'),
    after_image: demo('ufficio'),
    aspect_ratio: '21/9',
    handle_color: 'var(--olo-color-light, #ffffff)',
    handle_size: '46',
    border_radius: '16',
    card_shadow: 'md',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'before_image', label: t('Immagine «Prima»'), type: 'image' },
    { key: 'after_image', label: t('Immagine «Dopo»'), type: 'image' },
    { key: 'before_label', label: t('Etichetta Prima'), type: 'text' },
    { key: 'after_label', label: t('Etichetta Dopo'), type: 'text' },
    { key: 'show_labels', label: t('Mostra etichette'), type: 'toggle' },

    { type: 'separator', label: t('Comportamento slider') },
    { key: 'start_position', label: t('Posizione iniziale'), type: 'range', min: 0, max: 100 },

    { type: 'separator', label: t('Autoplay') },
    { key: 'autoplay', label: t('Passaggio automatico'), type: 'toggle' },
    { key: 'autoplay_delay', label: t('Attesa inattività (sec)'), type: 'range', min: 1, max: 10,
      condition: { field: 'autoplay', value: true } },
    { key: 'autoplay_speed', label: t('Durata ciclo (sec)'), type: 'range', min: 1, max: 8,
      condition: { field: 'autoplay', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // Zone standard (16 nov 2026, rifinita per «Guide in un minuto» N. 02): Aspetto · Maniglia · Testo · Forma · Immagini
  // · Disposizione. Prima: «Preset stilistico», «Slider — Aspetto», «Card», con dodici stili dai nomi che promettevano
  // effetti mai disegnati (3D Tilt, Retro VHS, Neon Divider cambiavano solo il colore della maniglia, in esadecimale).
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    // Gli stili (tilePresets.js) toccano solo la maniglia e le etichette, coi colori della palette.
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'chiaro',  label: t('Chiaro') },
      { value: 'scuro',   label: t('Scuro') },
      { value: 'brand',   label: t('Primario') },
      { value: 'vetro',   label: t('Vetro') },
      { value: 'minimo',  label: t('Minimo') },
      { value: 'grande',  label: t('Grande') },
      { value: 'custom',  label: t('Personalizzato') },
    ]},
    { key: 'label_bg', label: t('Sfondo etichette'), type: 'color',
      condition: { field: 'show_labels', op: 'neq', value: false } },
    { key: 'card_shadow', label: t('Ombra'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'card_shadow_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'card_shadow_h', v: 'card_shadow_v', blur: 'card_shadow_blur', spread: 'card_shadow_spread', color: 'card_shadow_color', inset: 'card_shadow_inset' },
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },

    { type: 'separator', label: t('Maniglia') },
    { key: 'handle_color', label: t('Colore maniglia'), type: 'color' },
    { key: 'handle_size', label: t('Dimensione maniglia'), type: 'range', min: 24, max: 60 },
    { key: 'handle_border', label: t('Spessore bordo'), type: 'range', min: 1, max: 6 },
    { key: 'line_width', label: t('Spessore linea'), type: 'range', min: 1, max: 6 },

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Etichette'), responsiveKeys: [], condition: { field: 'show_labels', op: 'neq', value: false },
      keys: { family: 'label_font_family', size: 'label_font_size', weight: 'label_font_weight', transform: 'label_text_transform',
              letterSpacing: 'label_letter_spacing', color: 'label_color' },
      sizeMin: 9, sizeMax: 28 },

    { type: 'separator', label: t('Forma') },
    // Con un rapporto scelto l'Altezza non fa più niente, quindi sparisce. L'elenco
    // comprende vuoto/null/undefined perché le tile salvate PRIMA di questo campo non
    // hanno la chiave: leggerle come «non auto» nasconderebbe l'Altezza a tutti loro.
    { key: 'height', label: t('Altezza'), type: 'range', min: 200, max: 800, step: 10,
      condition: { field: 'aspect_ratio', op: 'in', value: ['auto', '', null, undefined] } },
    // La cornice è UNA per tutte e due le immagini: prima e dopo devono per forza
    // stare nella stessa maschera, altrimenti lo slider confronta due ritagli diversi.
    // Con un rapporto scelto l'altezza la detta il rapporto e l'Altezza in px sparisce
    // (i due renderer emettono aspect-ratio AL POSTO di height, mai insieme).
    { key: 'aspect_ratio', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ custom: true }),
      description: t('«Auto» mantiene l\'altezza fissa qui sopra.') },
    { key: 'aspect_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4'),
      condition: { field: 'aspect_ratio', op: 'eq', value: 'custom' } },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),
    { key: 'card_border', label: t('Bordo card'), type: 'border',
      legacyKeys: { width: 'card_border_width', color: 'card_border_color' } },

    { type: 'separator', label: t('Immagini') },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    { key: 'object_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'before_image', fit: 'object_fit', height: 'height', ratio: 'aspect_ratio', ratioCustom: 'aspect_ratio_custom' } },

    { type: 'separator', label: t('Disposizione') },
    { key: 'orientation', label: t('Orientamento'), type: 'select', options: [
      { value: 'horizontal', label: t('Orizzontale') },
      { value: 'vertical', label: t('Verticale') },
    ]},

    ...borderFields(),
  ],
};
