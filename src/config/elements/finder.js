import {
  textEffectsFields, textEffectsDefaults,
  borderFields, borderDefault, borderHoverDefault, borderEffectDefaults,
  wowEffectsFields, wowEffectsDefaults,
} from './_shared';
import { t } from '@/i18n';

/**
 * Finder — zona interattiva "one-tap recommender": chip opzione → result card.
 * Estratto dai demo OLOthemes (setupFinder in fx.js, zona `data-finder`, presente
 * in 18 temi). Token-first: un solo colore `zone_accent`.
 *
 * Modello dati: UN array `items[]` (ogni riga = chip + il suo risultato), 1:1.
 *
 * ⚠️ CHIAVI SALVATE INVARIATE: il tile è già usato in 18 temi. Le chiavi storiche
 * (eyebrow/heading/intro/zone_accent/zone_on/card_bg/card_border/align + item
 * option/title/text/meta/cta_text/cta_url/icon) NON cambiano mai. Tutte le chiavi
 * aggiunte dall'upgrade "tile perfetta" hanno default che riproducono l'aspetto
 * storico (preset 'custom', shadow 'none', card_radius 16, border 0…), così i temi
 * esistenti rendono identici. Render Vue == PHP (FinderTile.vue).
 */
export default {
  type: 'finder',
  name: t('Finder (chip → risultato)'),
  icon: 'dashicons-search',
  category: 'interactive',

  defaults: {
    // preset apply-once — 'custom' = nessuna firma (i temi esistenti restano invariati)
    preset: 'custom',
    // ─── storiche (INVARIATE) ───
    eyebrow: t('Trova il tuo'),
    heading: t('Da dove vuoi partire?'),
    intro: '',
    items: [
      { option: 'Opzione A', title: 'Risultato A', text: 'Descrizione del risultato consigliato.', meta: '', cta_text: '', cta_url: '#', icon: '' },
      { option: 'Opzione B', title: 'Risultato B', text: 'Descrizione del risultato consigliato.', meta: '', cta_text: '', cta_url: '#', icon: '' },
      { option: 'Opzione C', title: 'Risultato C', text: 'Descrizione del risultato consigliato.', meta: '', cta_text: '', cta_url: '#', icon: '' },
    ],
    zone_accent: '',
    zone_on: 'var(--olo-color-surface, #ffffff)',
    card_bg: '',
    card_border: '',
    media_bg: '',
    object_position: 'center center',
    align: 'center',
    // ─── nuove (default = aspetto storico) ───
    default_index: '0',
    typography_preset: '',
    chip_bg: '',           // '' ⇒ trasparente (chip outline)
    chip_radius: '999',
    card_radius: '16',
    card_padding: { top: 34, right: 38, bottom: 34, left: 38 },
    card_max_width: '680',
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'heading',
    ...wowEffectsDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Domanda / titolo'), type: 'text',
      description: t('Usa <em>parola</em> per evidenziarla in corsivo con il colore accento.') },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },

    { type: 'separator', label: t('Opzioni e risultati') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Opzione'),
      defaults: { option: 'Nuova opzione', kicker: '', title: 'Risultato', text: 'Descrizione.', meta: '', cta_text: '', cta_url: '#', icon: '', image: '', media_bg: { type: 'none' }, media_label: '' },
      itemFields: [
        { key: 'option', label: t('Etichetta chip'), type: 'text' },
        { key: 'icon', label: t('Icona (opzionale)'), type: 'icon' },
        { type: 'separator', label: t('Card risultato') },
        { key: 'kicker', label: t('Kicker (es. The edit)'), type: 'text' },
        { key: 'title', label: t('Titolo risultato'), type: 'text' },
        { key: 'text', label: t('Testo risultato'), type: 'textarea' },
        { key: 'meta', label: t('Meta in basso (prezzo, durata…)'), type: 'text' },
        { key: 'cta_text', label: t('Testo CTA (opzionale)'), type: 'text' },
        { key: 'cta_url', label: t('URL CTA'), type: 'link' },
        { type: 'separator', label: t('Media card (opzionale)') },
        { key: 'image', label: t('Immagine (vuoto = placeholder)'), type: 'image' },
        { key: 'media_bg', label: t('Sfondo / media (ogni tipo)'), type: 'background', showParallax: false },
        { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('Comportamento') },
    { key: 'default_index', label: t('Opzione attiva iniziale (0-based)'), type: 'number', min: 0, step: 1 },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stile') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      // 5 sicuri ◆
      { value: 'soft-card',       label: t('◆ Soft Card') },
      { value: 'minimal-line',    label: t('◆ Minimal Line') },
      { value: 'pill-solid',      label: t('◆ Pill Solid') },
      { value: 'editorial-serif', label: t('◆ Editorial Serif') },
      { value: 'compact',         label: t('◆ Compact') },
      // 7 audaci
      { value: 'glass',           label: t('✨ Glass') },
      { value: 'neon',            label: t('⚡ Neon Cyber') },
      { value: 'brutalist',       label: t('⬛ Brutalist') },
      { value: 'gradient',        label: t('🌊 Vivid Duotone') },
      { value: 'sticker',         label: t('🏷 Sticker') },
      { value: 'retro-terminal',  label: t('▌ Retro Terminal') },
      { value: 'tilt-3d',         label: t('🃏 3D Tilt') },
      // libertà totale
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([
      { value: 'heading', label: t('Solo titolo sezione') },
      { value: 'title',   label: t('Solo titoli risultato') },
      { value: 'all',     label: t('Titolo sezione + risultati') },
    ]),

    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore zona (accento)'), type: 'color',
      description: t('Chip attivo, kicker e bordi derivano da questo colore.') },
    { key: 'zone_on', label: t('Testo su accento'), type: 'color' },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},

    { type: 'separator', label: t('Chip') },
    { key: 'chip_bg', label: t('Sfondo chip (inattiva)'), type: 'color' },
    { key: 'chip_radius', label: t('Raggio chip (px)'), type: 'border-radius' },

    { type: 'separator', label: t('Card risultato') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'card_border', label: t('Colore bordo card (semplice)'), type: 'color',
      description: t('Bordo 1px rapido. Per bordi avanzati (spessore, lati, effetti) usa la sezione Bordo.') },
    { key: 'media_bg', label: t('Sfondo media (card con immagine)'), type: 'color' },
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true,
      contextKeys: { ratio: '190/240', fit: 'cover' },
      description: t('Punto focale globale dell’immagine in tutte le card risultato.') },
    { key: 'card_max_width', label: t('Larghezza max card (px)'), type: 'range', min: 480, max: 1000, step: 10 },
    { key: 'card_radius', label: t('Raggio card'), type: 'border-radius' },
    { key: 'card_padding', label: t('Padding card (px)'), type: 'spacing', max: 80 },
    { key: 'shadow', label: t('Ombra card'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Piccola') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
    ]},

    { type: 'separator', label: t('Aspetto tile') },
    { key: 'tile_padding', label: t('Padding tile (px)'), type: 'spacing', min: 0, max: 96 },

    ...borderFields(),
    ...wowEffectsFields(),
  ],
};
