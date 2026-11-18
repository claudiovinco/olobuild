import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, wowEffectsFields, wowEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { demo } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile Overlay Grid — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (content-items), colonne (desktop/mobile),
 *                   posizione overlay, altezza, dimensione titolo,
 *                   visibilità CTA + testo CTA, posizione ribbon, hover_effect/hover_overlay (comportamento)
 *   styleFields[] → preset, sfondo, tipografia, tweak effetto preset, gap, allineamento testo,
 *                   stile overlay (primary/default), padding overlay, raggio elementi,
 *                   colore overlay/gradiente, colori/peso/spacing titolo+sottotitolo, stile CTA,
 *                   colori ribbon, text effects, ombra, bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'overlaygrid',
  name: t('Overlay Grid'),
  icon: 'dashicons-grid-view',
  // Griglia di immagini con didascalia: con galleria e showcase in «Media».
  category: 'media',
  defaults: {
    typography_preset: '',
    bg: { type: 'none' },
    // Testi neutri e nessuna foto esterna (stessi default del PHP): senza immagine
    // la card mostra il fondo scuro della palette e il testo resta leggibile.
    items: [
      { id: 'og-1', image: '', title: t('Servizi'), subtitle: t('Cosa possiamo fare per te'), link: '' },
      { id: 'og-2', image: '', title: t('Progetti'), subtitle: t('Alcuni lavori recenti'), link: '' },
      { id: 'og-3', image: '', title: t('Chi siamo'), subtitle: t('Persone, metodo, valori'), link: '' },
    ],
    columns: '3',
    columns_laptop: '',
    columns_tablet: '',
    columns_mobile: '1',
    gap: 'medium',
    height: '320',
    // 'auto' = nessun ritaglio imposto, resta l'altezza fissa di sempre; 'cover' e'
    // l'object-fit che i due renderer avevano cablato. Entrambi riproducono la resa
    // che le pagine pubblicate hanno oggi.
    image_ratio: 'auto',
    image_fit: 'cover',
    object_position: 'center center',
    match_height: true,
    layout_mode: 'uniform',
    overlay_position: 'bottom',
    overlay_horizontal: 'left',
    overlay_style: 'overlay-primary',
    overlay_padding: 'medium',
    title_size: 'h3',
    hover_effect: 'zoom',
    hover_overlay: 'always',
    ribbon_position: 'top-right',
    ribbon_bg: '',
    ribbon_color: '',
    shadow: 'sm',

    // ── Preset & granular controls (V3.26.0) ──
    preset: 'custom',
    item_radius: 12,
    overlay_color: 'rgba(0,0,0,0.45)',
    overlay_gradient: true,
    title_color: 'var(--olo-color-light, #f8f9fa)',
    title_weight: '700',
    title_letter_spacing: 0,
    title_uppercase: false,
    subtitle_color: 'rgba(255,255,255,0.85)',
    subtitle_size: 14,
    show_cta: false,
    cta_text: 'Scopri',
    cta_style: 'arrow',

    effect_color: '',
    effect_intensity: 'medium',
    effect_speed: 0,

    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...wowEffectsDefaults,
  },

  // Appena trascinata: le tre proposte di un locale in card verticali con foto, velo scuro
  // sfumato, titolo e sottotitolo chiari, un nastrino «Nuovo menù» e il link «Scopri il menù».
  partenza: {
    items: [
      { id: 'og-1', card_type: 'image', image: demo('caffe'), title: t('Colazione'), subtitle: t('Dalle 7:30, con i cornetti sfornati da noi'), link: '', ribbon: '' },
      { id: 'og-2', card_type: 'image', image: demo('insalata'), title: t('Pranzo'), subtitle: t('Il piatto del giorno a 14 €, anche da asporto'), link: '', ribbon: '' },
      { id: 'og-3', card_type: 'image', image: demo('tavola'), title: t('Cena'), subtitle: t('Su prenotazione, dal giovedì alla domenica'), link: '', ribbon: t('Nuovo menù') },
    ],
    image_ratio: '4/5',
    item_radius: 16,
    overlay_color: 'color-mix(in srgb, var(--olo-color-dark, #16263d) 85%, transparent)',
    title_color: 'var(--olo-color-light, #f8f9fa)',
    show_cta: true,
    cta_text: t('Scopri il menù'),
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  // 16 nov 2026 (episodio 31): etichette in italiano senza spiegazioni tra parentesi (nelle (i)), lo Stile nelle zone
  // dello standard, sei stili onesti coi colori del sito (prima dodici nomi inglesi a colori fissi che scrivevano anche
  // il testo del pulsante: «Read more», «ACCESS», «EXEC»), tolti tre controlli che non agivano («Tweak effetto
  // preset»: colore, intensità, velocità) e «Stile overlay» (Primary/Predefinito: il velo lo decide il suo colore).
  fields: [
    { type: 'separator', label: t('Elementi') },
    { key: 'items', label: t('Elementi'), type: 'content-items', supportsDynamic: true,
      itemLabel: t('Elemento'), etichettaDa: 'title', miniaturaDa: 'image',
      itemFields: [
        { key: 'card_type', label: t('Tipo'), type: 'select', options: [
          { value: 'image',   label: t('Immagine') },
          { value: 'text',    label: t('Testo') },
          { value: 'icon',    label: t('Icona') },
          { value: 'graphic', label: t('Grafica') },
        ], description: t('Immagine: la foto col testo sopra. Testo, Icona e Grafica: una card piena col suo colore, senza foto.') },
        { key: 'image', label: t('Immagine'), type: 'image',
          condition: { field: 'card_type', op: 'eq', value: 'image' } },
        { key: 'hover_image', label: t('Seconda immagine al passaggio'), type: 'image',
          condition: { field: 'card_type', op: 'eq', value: 'image' } },
        { key: 'hover_video', label: t('Video al passaggio'), type: 'media',
          condition: { field: 'card_type', op: 'eq', value: 'image' } },
        { key: 'icon', label: t('Icona'), type: 'icon',
          condition: { field: 'card_type', op: 'eq', value: 'icon' } },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },
        { key: 'body', label: t('Testo'), type: 'textarea',
          condition: { field: 'card_type', op: 'in', value: ['text', 'icon', 'graphic'] },
          description: t('Il paragrafo sotto il titolo, nelle card senza foto.') },
        { key: 'link', label: t('Link'), type: 'link', placeholder: t('https://...') },
        { key: 'ribbon', label: t('Nastro'), type: 'text',
          description: t('Una scritta nell’angolo della card: «Nuovo», «Esaurito», «Ultimi posti».') },
        { key: 'tall', label: t('Cella alta'), type: 'toggle', condizioneTile: { field: 'layout_mode', op: 'eq', value: 'masonry' },
          description: t('Nel mosaico occupa due righe.') },
        { key: 'wide', label: t('Cella larga'), type: 'toggle', condizioneTile: { field: 'layout_mode', op: 'eq', value: 'masonry' },
          description: t('Nel mosaico occupa due colonne.') },
      ],
      newItemDefaults: { card_type: 'image', image: '', hover_image: '', hover_video: '', icon: '', icon_color: '', card_bg: '', body: '', title: t('Nuovo elemento'), subtitle: '', link: '', ribbon: '', item_title_color: '', item_subtitle_color: '', tall: false, wide: false },
    },

    { type: 'separator', label: t('Al passaggio') },
    { key: 'hover_overlay', label: t('Il testo'), type: 'select', options: [
      { value: 'always', label: t('Sempre visibile') },
      { value: 'fade', label: t('Compare') },
      { value: 'slide-bottom', label: t('Sale dal basso') },
      { value: 'slide-top', label: t('Scende dall’alto') },
      { value: 'slide-left', label: t('Entra da sinistra') },
      { value: 'slide-right', label: t('Entra da destra') },
    ], description: t('Sempre visibile, o solo quando il puntatore passa sulla card. Sul telefono, dove non si passa sopra, il testo è sempre visibile.') },

    { type: 'separator', label: t('Pulsante') },
    { key: 'show_cta', label: t('Mostra il pulsante'), type: 'toggle',
      description: t('Lo stesso pulsante su ogni card, che porta al suo link.') },
    { key: 'cta_text', label: t('Testo del pulsante'), type: 'text',
      condition: { field: 'show_cta', op: 'eq', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // Zone: Aspetto · Testo · Forma · Disposizione · Pulsante · Nastro · Ogni elemento (+ effetti speciali, bordo).
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'editoriale', label: t('Editoriale') },
      { value: 'minimo',     label: t('Minimo') },
      { value: 'rivista',    label: t('Rivista') },
      { value: 'schede',     label: t('Schede') },
      { value: 'vetro',      label: t('Vetro') },
      { value: 'tinta',      label: t('Tinta') },
      { value: 'custom',     label: t('Personalizzato') },
    ], description: t('Cambia insieme velo, testo, raggio, ombra ed effetto al passaggio, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.') },
    { key: 'overlay_color', label: t('Colore del velo'), type: 'color',
      description: t('Il fondo sotto il testo delle card con la foto.') },
    { key: 'overlay_gradient', label: t('Velo sfumato'), type: 'toggle',
      description: t('Trasparente in alto e pieno in basso, dove sta il testo: la foto resta libera.') },
    { key: 'hover_effect', label: t('Effetto foto'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'zoom-rotate', label: t('Zoom e rotazione') },
      { value: 'brightness', label: t('Più luminosa') },
      { value: 'desaturate', label: t('Dal grigio al colore') },
      { value: 'blur-in', label: t('Da sfocata a nitida') },
    ], description: t('Al passaggio del puntatore sulla card.') },
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { key: 'title_size', label: t('Grandezza del titolo'), type: 'select', options: [
      { value: 'h1', label: t('Grandissimo (H1)') },
      { value: 'h2', label: t('Grande (H2)') },
      { value: 'h3', label: t('Medio (H3)') },
      { value: 'h4', label: t('Piccolo (H4)') },
    ]},
    { type: 'typography', label: t('Titolo'),
      responsiveKeys: [],
      letterSpacingUnit: 'em',
      keys: {
        weight:        'title_weight',
        letterSpacing: 'title_letter_spacing',
        color:         'title_color',
      },
    },
    { key: 'title_uppercase', label: t('Titolo maiuscolo'), type: 'toggle' },
    { type: 'typography', label: t('Sottotitolo'),
      responsiveKeys: [],
      keys: {
        size:  'subtitle_size',
        color: 'subtitle_color',
      },
      sizeMin: 11, sizeMax: 20, sizeStep: 1,
    },
    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'subtitle', label: t('Solo Sottotitolo') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Forma') },
    { key: 'item_radius', label: t('Raggio elementi'), type: 'border-radius' },
    { key: 'height', label: t('Altezza'), type: 'range', min: 150, max: 800, step: 25, unit: 'px',
      description: t('Usata quando le proporzioni sono automatiche.') },
    // La cornice mancava: si poteva scegliere solo l'altezza in pixel, uguale per ogni
    // schermo. Scelta una proporzione, prende il posto dell'altezza; con 'auto' non si
    // emette niente e resta l'altezza fissa, cioè la resa di oggi.
    // In Masonry l'altezza della cella la decide la griglia (`grid-auto-rows` +
    // `height:100%` sull'immagine), quindi la proporzione non avrebbe presa: il
    // controllo si nasconde per non promettere un effetto che non c'e'.
    { key: 'image_ratio', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ autoLabel: 'Auto (usa Altezza)' }),
      condition: { field: 'layout_mode', op: 'neq', value: 'masonry' },
      description: t('La maschera di ritaglio delle immagini: sostituisce l’altezza fissa.') },
    { key: 'image_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    // La chiave resta `object_position` (globale, non per-item): e' quella salvata nei
    // template pubblicati e non si tocca, anche se le sorelle nuove si chiamano image_*.
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { fit: 'image_fit', ratio: 'image_ratio' },
      description: t('Punto focale delle immagini (uguale per tutte). Predefinito: il centro.'),
      condition: { field: 'image_fit', op: 'neq', value: 'fill' } },
    { key: 'overlay_padding', label: t('Padding del testo'), type: 'select', options: [
      { value: 'small', label: t('Piccolo') },
      { value: 'medium', label: t('Medio') },
      { value: 'large', label: t('Grande') },
    ]},

    { type: 'separator', label: t('Disposizione') },
    { key: 'layout_mode', label: t('Disposizione'), type: 'select', options: [
      { value: 'uniform', label: t('Griglia') },
      { value: 'masonry', label: t('Mosaico (bento)') },
    ], description: t('Nel mosaico le card con «Cella alta» o «Cella larga» occupano due righe o due colonne; l’altezza diventa quella di una riga.') },
    { key: 'match_height', label: t('Altezza uniforme'), type: 'toggle',
      condition: { field: 'layout_mode', op: 'eq', value: 'uniform' } },
    { key: 'columns', label: t('Colonne'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
      { value: '5', label: '5' },
    ]},
    // Fra 960 e 1199 px (tablet orizzontale, portatili piccoli); vuoto = come il desktop (com'era
    // prima che ci fosse): quattro schede da 200 px a 960 erano troppo strette per le didascalie.
    { key: 'columns_laptop', label: t('Colonne portatile'), type: 'select', options: [
      { value: '', label: t('Come il desktop') },
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
    ], description: t('Da 960 a 1199 px di larghezza della finestra: portatili piccoli e tablet in orizzontale. In mezza pagina la tile toglie da sola le colonne finché ogni card ha almeno 220 px.') },
    // Fra 640 e 959 px; vuoto = come il mobile (com'era prima che ci fosse)
    { key: 'columns_tablet', label: t('Colonne tablet'), type: 'select', options: [
      { value: '', label: t('Come il telefono') },
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
    ]},
    { key: 'columns_mobile', label: t('Colonne telefono'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
    ]},
    { key: 'gap', label: t('Gap'), type: 'select', options: [
      { value: 'collapse', label: t('Nessuno') },
      { value: 'small', label: t('Piccolo') },
      { value: 'medium', label: t('Medio') },
      { value: 'large', label: t('Grande') },
    ]},
    { key: 'overlay_position', label: t('Posizione del testo'), type: 'select', options: [
      { value: 'bottom', label: t('In basso') },
      { value: 'top', label: t('In alto') },
      { value: 'center', label: t('Centro') },
      { value: 'cover', label: t('Su tutta la card') },
      { value: 'bottom-left', label: t('Basso-sinistra') },
      { value: 'bottom-center', label: t('Basso-centro') },
      { value: 'bottom-right', label: t('Basso-destra') },
      { value: 'top-left', label: t('Alto-sinistra') },
      { value: 'top-center', label: t('Alto-centro') },
      { value: 'top-right', label: t('Alto-destra') },
      { value: 'center-left', label: t('Centro-sinistra') },
      { value: 'center-right', label: t('Centro-destra') },
    ]},
    { key: 'overlay_horizontal', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Pulsante'), condition: { field: 'show_cta', op: 'eq', value: true } },
    { key: 'cta_style', label: t('Stile del pulsante'), type: 'select', options: [
      { value: 'underline', label: t('Sottolineato') },
      { value: 'arrow',     label: t('Con freccia') },
      { value: 'pill',      label: t('Pillola') },
      { value: 'text',      label: t('Solo testo') },
    ]},

    { type: 'separator', label: t('Nastro') },
    { key: 'ribbon_bg', label: t('Sfondo nastro'), type: 'color',
      description: t('Vuoto: il colore primario del sito.') },
    { key: 'ribbon_color', label: t('Testo nastro'), type: 'color',
      description: t('Vuoto: il colore che contrasta col primario.') },
    { key: 'ribbon_position', label: t('Posizione nastro'), type: 'select', options: [
      { value: 'top-left', label: t('Alto sinistra') },
      { value: 'top-right', label: t('Alto destra') },
    ]},

    { type: 'separator', label: t('Ogni elemento') },
    { key: 'items', type: 'content-items', label: t('Elementi'), itemLabel: t('Elemento'), etichettaDa: 'title', miniaturaDa: 'image', itemFields: [
        { key: 'icon_color', label: t('Colore icona'), type: 'color',
          condition: { field: 'card_type', op: 'eq', value: 'icon' } },
        { key: 'card_bg', label: t('Sfondo card'), type: 'color',
          condition: { field: 'card_type', op: 'in', value: ['text', 'icon', 'graphic'] },
          description: t('Lo sfondo della card senza foto. Vuoto: il colore scuro del sito.') },
        { key: 'item_title_color', label: t('Colore titolo'), type: 'color',
          description: t('Solo per questa card. Vuoto: quello scelto in Testo.') },
        { key: 'item_subtitle_color', label: t('Colore sottotitolo'), type: 'color',
          description: t('Solo per questa card. Vuoto: quello scelto in Testo.') },
    ] },

    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
