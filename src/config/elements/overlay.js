import { textEffectsFields, textEffectsDefaults, filterFields, filterDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

// L'elenco delle proporzioni, e in separata sede i soli valori che un ritaglio lo
// accendono davvero (tutti tranne «Auto»). Il secondo serve alla condizione
// sull'altezza: derivarlo dal primo evita che aggiungendo un rapporto ci si
// dimentichi di aggiornare la condizione.
const PROPORZIONI = ratioOptions({ custom: true });
// I rapporti che danno la forma al contenitore, e che quindi rendono inutile
// l'«Altezza». 'custom' NON e' fra questi: finché il campo «Proporzioni
// personalizzate» e' vuoto o scritto male, image_frame() non lo valida e il
// contenitore torna all'altezza fissa — che a quel punto deve restare
// raggiungibile, o il riquadro resta bloccato sul valore salvato.
const RITAGLI = PROPORZIONI.filter((o) => o.value !== 'auto' && o.value !== 'custom').map((o) => o.value);

/**
 * Tile Overlay — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → immagine, titolo, descrizione, URL link + target, effetto hover (comportamento)
 *   styleFields[] → preset, sfondo, tipografia, border-radius, colore overlay/testo,
 *                   opacità overlay, altezza, text effects, ombra, filtri immagine, bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'overlay',
  name: t('Overlay'),
  icon: 'dashicons-format-image',
  category: 'media',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    bg: { type: 'none' },
    image_url: '',
    // La cornice dell'immagine, con le stesse chiavi (senza prefisso) della tile
    // Immagine, che è la convenzione già in casa qui: `object_position` c'era da
    // sempre. 'auto' = nessun aspect-ratio, il contenitore resta alto `height`;
    // 'cover' = quello che [uk-cover] applica già all'immagine. Così una pagina
    // pubblicata rende esattamente come prima.
    aspect_ratio: 'auto',
    aspect_ratio_custom: '16/9',
    object_fit: 'cover',
    object_position: 'center center',
    title: t('Titolo progetto'),
    description: t('Descrizione.'),
    link_url: '',
    link_target: '_self',
    overlay_color: 'var(--olo-color-dark, #16263d)',
    text_color: '',
    hover_effect: 'fade',
    overlay_opacity: '70',
    height: '300',
    shadow: 'none',
    border_radius: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'title',
    ...filterDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'image_url', label: t('Immagine'), type: 'image' },
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'description', label: t('Descrizione'), type: 'textarea' },
    { key: 'link_url', label: t('URL link'), type: 'link' },
    { key: 'link_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'fade', label: t('Fade') },
      { value: 'slide-up', label: t('Slide Up') },
      { value: 'zoom', label: t('Zoom') },
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
    { type: 'separator', label: t('Aspetto') },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),
    // L'altezza fissa e le proporzioni sono alternative: con un aspect-ratio addosso
    // il contenitore non può anche avere un'altezza in px, la vincerebbe e il
    // ritaglio non si vedrebbe. Si mostra quella che comanda davvero.
    //
    // SCRITTA AL CONTRARIO, e non per vezzo: «non è uno dei ritagli» invece di
    // «vale auto». `aspect_ratio` è una chiave NUOVA, e l'inspector valuta le
    // condizioni sui settings GREZZI, senza fondere i default: su ogni overlay già
    // in pagina il valore è `undefined`, che con `eq 'auto'` non combacia. Il
    // controllo dell'altezza — l'unico che quelle pagine usano davvero — sarebbe
    // sparito dall'inspector per tutte.
    { key: 'height', label: t('Altezza'), type: 'range', min: 10, max: 600, step: 5,
      condition: { field: 'aspect_ratio', op: 'notIn', value: RITAGLI } },
    { key: 'aspect_ratio', label: t('Proporzioni'), type: 'select',
      options: PROPORZIONI },
    { key: 'aspect_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4, 1.618'),
      condition: { field: 'aspect_ratio', op: 'eq', value: 'custom' } },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    // Il pad del focale leggeva `ratio: 'height'`: '300' passava per una proporzione
    // e disegnava un'anteprima 300:1. Ora legge la cornice vera.
    { key: 'object_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'image_url', ratio: 'aspect_ratio', ratioCustom: 'aspect_ratio_custom', fit: 'object_fit' },
      condition: { field: 'object_fit', op: 'neq', value: 'fill' } },
    { type: 'separator', label: t('Overlay') },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'overlay_opacity', label: t('Opacità overlay'), type: 'range', min: 0, max: 100, step: 5 },
    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),
    ...shadowField,
    ...filterFields,
    ...borderFields(),
  ],
};
