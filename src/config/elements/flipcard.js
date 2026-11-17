import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';
import { demo } from '../demoMedia.js';

/**
 * Tile FlipCard — split CONTENUTO/STILE.
 *   fields[]      → fronte (image/video/icon/title/description), retro (image/video/icon/title/description/cta), animazione flip
 *   styleFields[] → preset, bg, typo, text-effects, fronte aspetto (icon size/color, bg, overlay, fit/padding/radius, text color/align/valign),
 *                   retro aspetto identico + cta colori/radius, card (height/radius/border/shadow/padding), tipografia
 */
export default {
  type: 'flipcard',
  name: t('FlipCard'),
  icon: 'dashicons-image-flip-horizontal',
  category: 'marketing',

  // Unificazione sfondo: ogni faccia ha il suo pannello unico media (immagine/video/
  // gradiente/colore…). I campi legacy front_image/front_video e back_image/back_video
  // confluiscono rispettivamente in front_media e back_media. Non distruttivo: le chiavi
  // vecchie restano come fallback nei renderer (Vue+PHP) e nei defaults; front_media/
  // back_media hanno la precedenza. Migrazione al load in src/utils/bgMigrate.js.
  bgMigrate: [
    { imageKey: 'front_image', imagePosKey: 'front_image_position', videoKey: 'front_video', target: 'front_media' },
    { imageKey: 'back_image',  imagePosKey: 'back_image_position',  videoKey: 'back_video',  target: 'back_media' },
  ],

  defaults: {
    front_media: { type: 'none' },
    back_media: { type: 'none' },
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    front_image: '',
    front_video: '',
    front_icon: 'star',
    front_icon_size: '40',
    front_icon_color: '',
    front_title: 'Titolo fronte',
    front_description: 'Descrizione della card visibile.',
    front_bg: '',
    front_overlay: '',
    front_image_fit: 'cover',
    front_image_position: 'center center',
    front_image_padding: '0',
    front_image_radius: '0',
    front_text_color: '',
    front_text_align: 'center',
    front_valign: 'center',
    back_image: '',
    back_video: '',
    back_icon: '',
    back_icon_size: '40',
    back_icon_color: '',
    back_title: 'Titolo retro',
    back_description: 'Contenuto retro con dettagli.',
    back_bg: '',
    back_overlay: '',
    back_image_fit: 'cover',
    back_image_position: 'center center',
    back_image_padding: '0',
    back_image_radius: '0',
    back_text_color: '',
    back_text_align: 'center',
    back_valign: 'center',
    back_cta_text: 'Scopri di più',
    back_cta_url: '',
    back_cta_target: false,
    back_cta_bg: '',
    back_cta_color: '',
    back_cta_radius: '6',
    flip_direction: 'horizontal',
    flip_duration: '600',
    flip_trigger: 'hover',
    flip_easing: 'ease-in-out',
    card_height: '350',
    card_border_radius: '12',
    card_border: { ...borderDefault },
    card_border_hover: { ...borderHoverDefault },
    card_border_hover_duration: 300,
    card_shadow: 'md',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    tile_padding: { top: 24, right: 24, bottom: 24, left: 24 },
    title_size: '22',
    title_weight: '600',
    desc_size: '14',
    ...textEffectsDefaults,
    text_effect_target: 'all',
  },

  // Appena nata: davanti una foto con velo scuro e il nome del servizio in chiaro in basso;
  // al passaggio la card gira e mostra, sul colore primario, cosa comprende e il pulsante.
  partenza: {
    // La foto resta piena: il velo scuro sale solo dal basso, dove sta il testo (mai media velati).
    front_media: { type: 'image', image_url: demo('scrivania'), image_size: 'cover', image_position: 'center center', overlay_color: 'var(--olo-color-dark, #16263d)', overlay_opacity: 78, overlay_fade: 'bottom', overlay_fade_reach: 75 },
    front_icon: '',
    front_title: t('Consulenza su misura'),
    front_description: t('Un percorso pensato attorno a te, dal primo incontro al risultato.'),
    front_text_color: 'var(--olo-color-light, #ffffff)',
    front_text_align: 'left',
    front_valign: 'bottom',
    back_title: t('Consulenza su misura'),
    back_description: t('Un primo incontro gratuito per capire le tue esigenze, poi un piano chiaro con tempi e costi definiti per iscritto.'),
    back_bg: 'var(--olo-color-primary)',
    back_text_color: 'var(--olo-color-primary-contrast)',
    back_text_align: 'left',
    back_cta_text: t('Prenota un incontro'),
    back_cta_url: '#',
    back_cta_bg: 'var(--olo-color-primary-contrast)',
    back_cta_color: 'var(--olo-color-primary)',
    back_cta_radius: '999',
    card_height: '360',
    card_border_radius: '16',
    title_size: '24',
    desc_size: '15',
  },

  // Lo sfondo di ogni faccia (foto, video, colore) è il suo media principale: sta nel Contenuto, accanto ai testi
  // della faccia (16 nov 2026; prima era in fondo allo Stile, dopo gli allineamenti).
  fields: [
    { type: 'separator', label: t('Fronte') },
    { key: 'front_icon', label: t('Icona'), type: 'icon' },
    { key: 'front_title', label: t('Titolo'), type: 'text' },
    { key: 'front_description', label: t('Descrizione'), type: 'textarea' },
    { key: 'front_media', label: t('Sfondo / media'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Retro') },
    { key: 'back_icon', label: t('Icona'), type: 'icon' },
    { key: 'back_title', label: t('Titolo'), type: 'text' },
    { key: 'back_description', label: t('Descrizione'), type: 'textarea' },
    { key: 'back_media', label: t('Sfondo / media'), type: 'background', showParallax: false },
    { key: 'back_cta_text', label: t('Testo pulsante'), type: 'text' },
    { key: 'back_cta_url', label: t('Link pulsante'), type: 'link',
      condition: { field: 'back_cta_text', op: 'notEmpty' } },
    { key: 'back_cta_target', label: t('Apri in una nuova scheda'), type: 'toggle',
      condition: { field: 'back_cta_text', op: 'notEmpty' } },

    { type: 'separator', label: t('Comportamento') },
    { key: 'flip_trigger', label: t('Si gira'), type: 'select', options: [
      { value: 'hover', label: t('Al passaggio del mouse') },
      { value: 'click', label: t('Al clic') },
    ], description: t('Sul telefono si gira sempre col tocco.') },
    { key: 'flip_direction', label: t('Movimento'), type: 'select', options: [
      { value: 'horizontal', label: t('Si gira di lato') },
      { value: 'vertical', label: t('Si gira dall’alto') },
      { value: 'diagonal', label: t('In diagonale') },
      { value: 'cube', label: t('Cubo') },
      { value: 'slide-flip', label: t('Scivola e si gira') },
      { value: 'zoom-flip', label: t('Si avvicina e si gira') },
    ]},
    { key: 'flip_duration', label: t('Durata'), type: 'range', unit: 'ms', min: 400, max: 1200, step: 50 },
    { key: 'flip_easing', label: t('Andamento'), type: 'select', options: [
      { value: 'ease', label: t('Morbido') },
      { value: 'ease-in-out', label: t('Morbido ai due capi') },
      { value: 'cubic-bezier(0.68,-0.55,0.265,1.55)', label: t('Con rimbalzo') },
    ]},
  ],

  // Zone standard (16 nov 2026): Aspetto · Testo · Fronte · Retro · Pulsante · Forma.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-card',     label: t('Scheda') },
      { value: 'photo-overlay',   label: t('Foto con velo') },
      { value: 'magazine-flip',   label: t('Rivista') },
      { value: 'minimal-clean',   label: t('Essenziale') },
      { value: 'icon-driven',     label: t('Icona grande') },
      { value: 'glass-flip',      label: t('Vetro') },
      { value: 'neon-frame',      label: t('Neon su scuro') },
      { value: 'brutalist-card',  label: t('Blocco netto') },
      { value: 'gradient-aurora', label: t('Sfumato') },
      { value: 'sticker-flip',    label: t('Adesivo') },
      { value: 'retro-poster',    label: t('Manifesto') },
      { value: 'tilt-3d',         label: t('Inclinata') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
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

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titoli'),
      responsiveKeys: [],
      keys: {
        size:   'title_size',
        weight: 'title_weight',
      },
      sizeMin: 16, sizeMax: 40, sizeStep: 1,
    },
    { type: 'typography', label: t('Descrizioni'),
      responsiveKeys: [],
      keys: {
        size:  'desc_size',
      },
      sizeMin: 12, sizeMax: 20, sizeStep: 1,
    },
    ...textEffectsFields([
      { value: 'front_title', label: t('Titolo del fronte') },
      { value: 'front_description', label: t('Descrizione del fronte') },
      { value: 'back_title', label: t('Titolo del retro') },
      { value: 'back_description', label: t('Descrizione del retro') },
      { value: 'all', label: t('Tutto il testo') },
    ]),

    { type: 'separator', label: t('Fronte') },
    { key: 'front_bg', label: t('Colore sfondo'), type: 'color' },
    // Il colore del testo vale per TUTTA la faccia (titolo, descrizione, icona senza colore):
    // stava in Tipografia come «Titolo», e chi lo cambiava si trovava colorato il fronte intero.
    { key: 'front_text_color', label: t('Colore testo'), type: 'color' },
    { key: 'front_icon_color', label: t('Colore icona'), type: 'color', condition: { field: 'front_icon', op: 'notEmpty' } },
    { key: 'front_icon_size', label: t('Dimensione icona'), type: 'range', unit: 'px', min: 24, max: 80, condition: { field: 'front_icon', op: 'notEmpty' } },
    { key: 'front_image_fit', label: t('Adattamento immagine'), type: 'select', options: [
      { value: 'cover', label: t('Copri (taglia)') },
      { value: 'contain', label: t('Contieni (intera)') },
      { value: 'fill', label: t('Riempi (deforma)') },
    ], condition: { field: 'front_image', op: 'notEmpty' } },
    { key: 'front_image_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'front_image', fit: 'front_image_fit' },
      condition: { field: 'front_image', op: 'notEmpty' } },
    { key: 'front_image_padding', label: t('Padding immagine'), type: 'spacing', min: 0, max: 60,
      condition: { field: 'front_image', op: 'notEmpty' } },
    withHover({ key: 'front_image_radius', label: t('Raggio immagine'), type: 'border-radius',
      condition: { field: 'front_image', op: 'notEmpty' } }),
    { key: 'front_overlay', label: t('Velo sull’immagine'), type: 'color',
      condition: { field: 'front_image', op: 'notEmpty' } },
    { key: 'front_text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'front_valign', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'top', label: t('Alto') },
      { value: 'center', label: t('Centro') },
      { value: 'bottom', label: t('Basso') },
    ]},

    { type: 'separator', label: t('Retro') },
    { key: 'back_bg', label: t('Colore sfondo'), type: 'color' },
    // Come sul fronte: stava in Tipografia come «Descrizione» e colorava tutto il retro.
    { key: 'back_text_color', label: t('Colore testo'), type: 'color' },
    { key: 'back_icon_color', label: t('Colore icona'), type: 'color', condition: { field: 'back_icon', op: 'notEmpty' } },
    { key: 'back_icon_size', label: t('Dimensione icona'), type: 'range', unit: 'px', min: 24, max: 80, condition: { field: 'back_icon', op: 'notEmpty' } },
    { key: 'back_image_fit', label: t('Adattamento immagine'), type: 'select', options: [
      { value: 'cover', label: t('Copri (taglia)') },
      { value: 'contain', label: t('Contieni (intera)') },
      { value: 'fill', label: t('Riempi (deforma)') },
    ], condition: { field: 'back_image', op: 'notEmpty' } },
    { key: 'back_image_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'back_image', fit: 'back_image_fit' },
      condition: { field: 'back_image', op: 'notEmpty' } },
    { key: 'back_image_padding', label: t('Padding immagine'), type: 'spacing', min: 0, max: 60,
      condition: { field: 'back_image', op: 'notEmpty' } },
    withHover({ key: 'back_image_radius', label: t('Raggio immagine'), type: 'border-radius',
      condition: { field: 'back_image', op: 'notEmpty' } }),
    { key: 'back_overlay', label: t('Velo sull’immagine'), type: 'color',
      condition: { field: 'back_image', op: 'notEmpty' } },
    { key: 'back_text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'back_valign', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'top', label: t('Alto') },
      { value: 'center', label: t('Centro') },
      { value: 'bottom', label: t('Basso') },
    ]},

    { type: 'separator', label: t('Pulsante'), condition: { field: 'back_cta_text', op: 'notEmpty' } },
    { key: 'back_cta_bg', label: t('Sfondo pulsante'), type: 'color', condition: { field: 'back_cta_text', op: 'notEmpty' } },
    { key: 'back_cta_color', label: t('Colore testo pulsante'), type: 'color', condition: { field: 'back_cta_text', op: 'notEmpty' } },
    withHover({ key: 'back_cta_radius', label: t('Raggio pulsante'), type: 'border-radius', condition: { field: 'back_cta_text', op: 'notEmpty' } }),

    { type: 'separator', label: t('Forma') },
    { key: 'card_height', label: t('Altezza'), type: 'range', unit: 'px', min: 200, max: 600, step: 10 },
    withHover({ key: 'card_border_radius', label: t('Raggio'), type: 'border-radius' }),
    withHover(
      { key: 'card_border', label: t('Bordo scheda'), type: 'border' },
      { hoverKey: 'card_border_hover', hoverDurationKey: 'card_border_hover_duration' }
    ),
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 40 },
    ...borderFields(),
  ],
};
