import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Showcase Grid — griglia di card-immagine grandi e linkate: media (slot/strisce) + velo,
 * kicker + titolo grande in basso, freccia circolare in alto a destra che si ANIMA all'hover
 * (cerchio → accento, freccia che ruota). Estratta dal blueprint OLOthemes "CardGrid/teams"
 * (verdano .teams-grid/.team). Render Vue == PHP (ShowcaseGridTile.vue). Nessun JS.
 */
export default {
  type: 'showcasegrid',
  name: t('Showcase Grid (card + freccia)'),
  icon: 'dashicons-grid-view',
  category: 'media',

  defaults: {
    items: [
      { image: '', media_label: "Men's squad", kicker: '3 squads', title: 'Men', link: '#' },
      { image: '', media_label: "Women's squad", kicker: '1 squad', title: 'Women', link: '#' },
      { image: '', media_label: 'Youth squad', kicker: '4 squads · U14–U21', title: 'Youth', link: '#' },
    ],
    columns: 3,
    gap: 18,
    aspect: '3/3.5',
    object_position: 'center center',
    // Il media della card e' un background-image con `background-size:cover`
    // cablato da sempre (PHP .ocg-media, twin in ShowcaseGridTile.vue): il
    // controllo nuovo nasce su quel valore → nessuna card pubblicata si muove.
    object_fit: 'cover',
    radius: 20,
    media_bg: 'var(--olo-color-dark, #16263d)',
    veil_color: 'var(--olo-color-dark, #16263d)',
    kicker_color: '',
    title_color: 'var(--olo-color-light, #f8f9fa)',
    arrow_bg: 'rgba(255,255,255,0.14)',
    arrow_color: 'var(--olo-color-light, #f8f9fa)',
    arrow_hover_bg: '',
    arrow_hover_color: 'var(--olo-color-dark, #16263d)',
    show_arrow: true,
    title_size: 34,
    title_weight: '900',
    title_uppercase: true,
    kicker_size: 12,

    // Spaziatura interna card — default = ESATTAMENTE il padding attuale (26px) → no-op.
    card_padding: { top: 26, right: 26, bottom: 26, left: 26 },
    // Raggio card a 4 angoli (OVERRIDE gated): default {0,0,0,0} → si usa il legacy `radius` → no-op.
    card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

    // KIT standard OLObuild (contenitore) — sfondo completo + ombra + bordo.
    // Default no-op: con i default il render resta identico a prima.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Card') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { image: '', media_bg: { type: 'none' }, media_label: 'Etichetta media', kicker: 'Kicker', title: 'Titolo', link: '#', span: 0, aspect: '' },
      itemFields: [
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'media_bg', label: t('Sfondo / media (ogni tipo)'), type: 'background', showParallax: false },
        { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
        { key: 'kicker', label: t('Kicker (sopra il titolo)'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link' },
        { key: 'span', label: t('Colonne occupate (0 = uniforme; 1-12 = editoriale)'), type: 'range', min: 0, max: 12, step: 1 },
        // Stesso concetto del select «Proporzioni» della tile, quindi stesso controllo:
        // finché era un campo di testo libero si poteva scrivere «16:9» coi due punti e
        // la regex dei renderer (`[^0-9.\/]`) lo riduceva a «169», card larghissima e
        // nessun avviso. Nel select quell'errore non è possibile.
        // ⚠ `autoValue: ''` è obbligatorio: la stringa vuota è il valore STORICO che
        // significa «eredita dalla griglia» (ed è il default dell'item), e non si tocca.
        // Niente condizione di visibilità: l'override per-item si accende quando ALMENO
        // UNA card ha uno span (`$has_spans` in PHP, `hasSpans` nel canvas), non quando
        // ce l'ha questa — una condizione su `span` di questo item lo nasconderebbe
        // proprio nelle card a span 0 di una griglia editoriale, dove invece funziona.
        // Lo dice la descrizione, che è la sede giusta per un cancello che sta altrove.
        { key: 'aspect', label: t('Proporzioni'), type: 'select',
          options: ratioOptions({ auto: true, autoValue: '', autoLabel: 'Come la griglia', extra: ['3/3.5'] }),
          description: t('Vale solo in modalità editoriale, cioè quando almeno una card ha «Colonne occupate» maggiore di 0.') },
      ],
    },
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 4, step: 1, responsive: true },
  ],

  styleFields: [
    { type: 'separator', label: t('Forma') },
    // Elenco canonico delle proporzioni. `3/3.5` e' il rapporto storico di questa
    // tile (e il suo default): sta fuori dal set comune e va tenuto in `extra`,
    // altrimenti sparirebbe dal select di chi ce l'ha già salvato.
    // Niente voce 'Auto': la card e' un background-image senza altezza propria —
    // togliere l'aspect-ratio la farebbe collassare sul solo testo. E il renderer
    // PHP ripulisce il valore da tutto cio' che non e' cifra o barra: 'auto'
    // diventerebbe '' e ricadrebbe comunque su 3/3.5.
    { key: 'aspect', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ auto: false, extra: ['3/3.5'] }) },
    // L'adattamento qui pilota `background-size`, non `object-fit`: 'Riduci se
    // necessario' (scale-down) non ha equivalente e non si offre — un controllo
    // che il renderer non sa leggere e' peggio di un controllo che manca.
    { key: 'object_fit', label: t('Adattamento'), type: 'select',
      options: ADATTAMENTI.filter((o) => o.value !== 'scale-down'),
      description: t('Come la foto riempie la maschera: «Riempi» ritaglia, «Contieni» la mostra tutta.') },
    { key: 'gap', label: t('Gap card'), type: 'range', min: 8, max: 32, step: 2 },
    // Punto focale GLOBALE applicato a TUTTE le card (background-position). Default 'center center' → no-op.
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { ratio: 'aspect', fit: 'object_fit' } },

    { type: 'separator', label: t('Raggio') },

    // 4 angoli indipendenti. Default {0,0,0,0} → si usa il raggio legacy sopra (no-op).
    { key: 'card_radius', label: t('Raggio card'), type: 'border-radius', legacyKeys: { all: 'radius' } },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'card_padding', label: t('Padding interno card'), type: 'spacing', max: 80 },

    { type: 'separator', label: t('Colori') },
    { key: 'media_bg', label: t('Sfondo media'), type: 'color' },
    { key: 'veil_color', label: t('Velo (gradiente)'), type: 'color' },
    { type: 'typography', label: t('Occhiello'), responsiveKeys: [], keys: { size: 'kicker_size', color: 'kicker_color' }, sizeMin: 8, sizeMax: 16 },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'title_size', weight: 'title_weight', color: 'title_color', uppercase: 'title_uppercase' }, sizeMin: 14, sizeMax: 56 },

    { type: 'separator', label: t('Freccia') },
    { key: 'show_arrow', label: t('Mostra freccia'), type: 'toggle' },
    withHover({ key: 'arrow_bg', label: t('Cerchio (hover vuoto = accento)'), type: 'color' }, { hoverKey: 'arrow_hover_bg' }),
    withHover({ key: 'arrow_color', label: t('Freccia'), type: 'color' }, { hoverKey: 'arrow_hover_color' }),

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
