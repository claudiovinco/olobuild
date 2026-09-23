import { shadowField, borderDefault } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Badge / Etichetta — pill compatta con testo + icona opzionale.
 * Feature dedicata "Stato live": pallino con onda che pulsa (verde o primario),
 * pensato per indicatori "Online / In diretta / Novità" (vedi Try home).
 *
 *   fields[]      → testo, icona, posizione icona, Stato live (toggle + colore onda)
 *   styleFields[] → zone standard dell'elemento: Aspetto · Testo · Forma · Disposizione
 *
 * Ogni controllo è letto da class-badge-tile.php e dal gemello BadgeTile.vue.
 * Tolti perché non facevano niente: «Stile» (nessun preset registrato per il
 * badge in tilePresets.js), «Effetti testo» (mai resi), «Effetti bordo» e gli
 * hover di raggio e bordo (mai letti). Le chiavi già salvate restano nei
 * template: semplicemente nessuno le mostra più. «Bordo» invece è rimasto e ora
 * è disegnato: sostituisce quello della variante (la home di try ne ha bisogno).
 *
 * Chiavi nuove additive: badge_live (bool), badge_live_color ('success'|'primary').
 */
export default {
  type: 'badge',
  name: t('Badge / Etichetta'),
  icon: 'dashicons-tag',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    text: t('Online'),
    icon: '',
    icon_position: 'before',
    // Stato live (additive)
    badge_live: false,
    badge_live_color: 'success',
    // Badge aggiuntivi (additive): pill gemelle in fila, colore per-item
    extra_items: [],
    // Stile
    variant: 'soft',
    bg_color: '',
    text_color: '',
    font_family: '',
    font_size: '13',
    font_weight: '600',
    text_transform: 'none',
    letter_spacing: '0',
    badge_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
    padding_y: 7,
    padding_x: 13,
    alignment: 'left',
    shadow: 'none',
    border: { ...borderDefault },
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'text', label: t('Testo'), type: 'text' },
    { key: 'icon', label: t('Icona (opzionale)'), type: 'icon' },

    { type: 'separator', label: t('Badge aggiuntivi') },
    { key: 'extra_items', label: t('Altre etichette'), type: 'content-items', itemLabel: t('Etichetta'),
      hint: t('Pill gemelle affiancate alla prima: stesso stile, colore per-etichetta (vuoto = neutro).'),
      newItemDefaults: { text: '', color: '', text_color: '' },
      itemFields: [
        { key: 'text', label: t('Testo'), type: 'text' },
      ] },

    { type: 'separator', label: t('Stato live') },
    { key: 'badge_live', label: t('Mostra pallino live'), type: 'toggle',
      description: t('Aggiunge un pallino con onda che pulsa, per indicatori "Online / In diretta".') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // Zone standard dell'elemento, sempre in quest'ordine: Aspetto · Testo · Forma ·
  // Disposizione. Sotto, il tab mostra il «Contenitore» (solo spazi ed effetti:
  // il badge è una tile atomica).
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    // Ogni voce dice cosa fa del «Colore» qui sotto: la spiegazione sta dove si
    // sceglie (la descrizione di un campo in linea l'inspector non la mostra).
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'soft',    label: t('Soft — tinta tenue del colore') },
      { value: 'solid',   label: t('Pieno — sfondo del colore') },
      { value: 'outline', label: t('Contorno — bordo e testo del colore') },
      { value: 'light',   label: t('Chiaro — bianco, bordo neutro') },
    ]},
    // Il colore da cui la variante ricava la pillola, NON lo sfondo: con Soft lo
    // sfondo è il 12% di questo colore e il bordo il 22%. Chiamato «Colore sfondo»
    // traeva in inganno (sulla home di try 14 badge lo usavano come sfondo finale).
    // Con «Chiaro» la variante non lo usa: il campo sparisce.
    { key: 'bg_color', label: t('Colore'), type: 'color',
      condition: { field: 'variant', op: 'neq', value: 'light' } },
    // Bordo proprio: se impostato prende il posto di quello della variante. Con
    // «Pieno» si ottiene un design esatto (sfondo = Colore, bordo = questo).
    { key: 'border', label: t('Bordo'), type: 'border',
      description: t('A zero resta il bordo della variante.') },
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    // Dallo stile tipografico il badge prende la sola FAMIGLIA: peso, maiuscolo
    // e spaziatura restano suoi (i badge OLOX: mono dallo stile, maiuscolo
    // spaziato dal badge). A stile collegato si nasconde quindi solo «Famiglia».
    { type: 'typography', label: t('Testo'),
      linkedPresetKey: 'typography_preset',
      linkedPresetGoverns: ['family'],
      responsiveKeys: [],
      keys: {
        family:        'font_family',
        size:          'font_size',
        weight:        'font_weight',
        transform:     'text_transform',
        letterSpacing: 'letter_spacing',
        color:         'text_color',
      },
      sizeMin: 10, sizeMax: 28,
    },

    { type: 'separator', label: t('Forma') },
    { key: 'badge_radius', label: t('Raggio'), type: 'border-radius' },
    { key: 'padding', label: t('Padding'), type: 'spacing', min: 0, max: 60,
      legacyKeys: { y: 'padding_y', x: 'padding_x' } },

    { type: 'separator', label: t('Badge aggiuntivi') },
    { key: 'extra_items', type: 'content-items', label: t('Altre etichette'), itemLabel: t('Etichetta'), etichettaDa: 'text', itemFields: [
        { key: 'color', label: t('Colore'), type: 'color' },
        { key: 'text_color', label: t('Colore testo (vuoto = come il colore)'), type: 'color' },
    ] },
    { type: 'separator', label: t('Stato live') },
    { key: 'badge_live_color', label: t('Colore onda'), type: 'select', options: [
      { value: 'success', label: t('Verde (online)') },
      { value: 'primary', label: t('Primario (brand)') },
    ], condition: { field: 'badge_live', op: 'eq', value: true } },
    { type: 'separator', label: t('Disposizione') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', responsive: true, options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},
    // Senza icona la posizione non ha niente da spostare.
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'before', label: t('Prima del testo') },
      { value: 'after', label: t('Dopo il testo') },
    ], condition: { field: 'icon', op: 'notEmpty' } },
  ],
};
