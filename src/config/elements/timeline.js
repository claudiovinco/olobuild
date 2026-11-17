import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { ratioOptions } from './_imageFrame.js';
import { demo } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile Timeline "SUPER" — redesign (handoff "OLObuild - Tile Timeline SUPER").
 *
 * Riscrittura del vecchio EventList in una timeline ricca e combinabile su
 * 10 dimensioni: Layout · Ingresso · Tema · Card · Filo · Nodo · Colore · Media ·
 * Densità · Stato. Stesse sorgenti dati (items), chiavi tl_* additive.
 *
 * Migrazione (resa PHP/Vue): legge le vecchie chiavi come fallback —
 *   layout(vertical-center→alt, vertical-left/right→one, horizontal→horizontal),
 *   marker_type(dot→dot, icon→icon, number→num), line_style/line_progress→thread/line.
 * Gli items vecchi (title/description/date/image/video/icon/icon_color) restano validi;
 * `tag` e `category` sono additivi (default sensati).
 *
 * Colori categoria dai ruoli globali del cliente:
 *   primary · secondary · accent · success (con override per-item via icon_color).
 *   Modalità "mono" forza tutto sul primario.
 */
export default {
  type: 'timeline',
  name: t('Timeline'),
  icon: 'dashicons-backup',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',

    // ── Items ──
    // Tappe neutre, uguali ai default del PHP. Nessuno stato: lo dichiara la tappa.
    items: [
      { id: 'tl-1', title: t('La fondazione'), tag: t('Inizio'), description: t('Il progetto prende forma: un\'idea, poche persone e tanta voglia di fare.'), date: '2015', image: '', video: '', icon: 'star', category: 'primary', icon_color: '', status: '' },
      { id: 'tl-2', title: t('La prima sede'), tag: t('Crescita'), description: t('Uno spazio tutto nostro per accogliere clienti e collaboratori.'), date: '2018', image: '', video: '', icon: 'location', category: 'accent', icon_color: '', status: '' },
      { id: 'tl-3', title: t('Un nuovo servizio'), tag: t('Novità'), description: t('L\'offerta si amplia per rispondere a nuove esigenze.'), date: '2021', image: '', video: '', icon: 'bolt', category: 'success', icon_color: '', status: '' },
      { id: 'tl-4', title: t('Oggi'), tag: t('Presente'), description: t('Un gruppo affiatato e lo sguardo rivolto ai prossimi traguardi.'), date: '2024', image: '', video: '', icon: 'flag', category: 'secondary', icon_color: '', status: '' },
    ],

    // ── 10 dimensioni SUPER ──
    tl_layout: 'alt',       // alt · one · horizontal · navigator
    tl_reveal: 'sides',     // sides · bloom · unroll · slot
    tl_theme: 'paper',      // paper · night · neon · blue
    tl_card: 'bubble',      // bubble · glass · polaroid · ticket
    tl_thread: 'solid2',    // solid2 · dash · dot · comet
    tl_node: 'icon',        // icon · dot · num · year
    tl_color: 'cat',        // cat · mono
    tl_media: 'on',         // on · off
    tl_density: 'comfy',    // comfy · compact
    tl_line: 'solid',       // solid (statico) · scroll (si riempie scorrendo, senza stati)
    tl_transparent: false,  // sfondo blocco trasparente (ignora il bg del tema)

    // ── Personalizzazione (override; '' o 0 = usa il default della variante/tema) ──
    tl_rail_color: '', tl_rail_w: 0, tl_fill_from: '', tl_fill_to: '',
    tl_node_size: 0, tl_node_border: 0,
    tl_card_bg: '', tl_card_radius: 0, tl_card_maxw: 0, tl_card_pad: 0,
    tl_media_ratio: 'auto', tl_media_h: 0, tl_media_fit: 'cover', object_position: 'center center', tl_media_radius: 0, tl_media_bar: true,
    tl_title_size: 0, tl_title_weight: '', tl_title_color: '', tl_title_family: '',
    tl_text_size: 0, tl_text_color: '', tl_text_lh: 0, tl_text_align: 'left', tl_text_family: '',
    tl_yr_size: 0, tl_yr_color: '', tl_yr_family: '',
    tl_show_tag: true, tl_tag_color: '',

    // Opzioni orizzontale
    h_card_width: '268',

    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: la storia di un'attività in quattro tappe alternate, ognuna con la
  // sua foto 16:9 nella card, l'icona nel nodo, l'etichetta colorata per categoria e l'ultima
  // tappa segnata «In corso».
  partenza: {
    items: [
      { id: 'tl-1', title: t('La fondazione'), tag: t('Inizio'), description: t('Il progetto prende forma: un\'idea, poche persone e tanta voglia di fare.'), date: '2015', image: demo('scrivania'), video: '', icon: 'star', category: 'primary', icon_color: '', status: '' },
      { id: 'tl-2', title: t('La prima sede'), tag: t('Crescita'), description: t('Uno spazio tutto nostro per accogliere clienti e collaboratori.'), date: '2018', image: demo('architettura'), video: '', icon: 'location', category: 'accent', icon_color: '', status: '' },
      { id: 'tl-3', title: t('Un nuovo servizio'), tag: t('Novità'), description: t('L\'offerta si amplia per rispondere a nuove esigenze.'), date: '2021', image: demo('ufficio'), video: '', icon: 'bolt', category: 'success', icon_color: '', status: '' },
      { id: 'tl-4', title: t('Oggi'), tag: t('Presente'), description: t('Un gruppo affiatato e lo sguardo rivolto ai prossimi traguardi.'), date: '2024', image: demo('citta-tetti'), video: '', icon: 'flag', category: 'secondary', icon_color: '', status: 'current' },
    ],
    tl_media_ratio: '16/9',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  // 16 nov 2026 (episodio 22 di «Una tile in un minuto»): inspector rimesso nello standard, chiavi INVARIATE. Nel
  // Contenuto ciò che la timeline dice e come si muove; la disposizione, il nodo e la densità sono passati nello Stile;
  // «Mostra le etichette» è venuto qui. Niente più unità né «0 = auto» nelle etichette: le spiegazioni stanno nelle (i).
  fields: [
    // ── Eventi ──
    { type: 'separator', label: t('Eventi') },
    { key: 'items', label: t('Eventi'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'tag', label: t('Etichetta'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
        { key: 'date', label: t('Data'), type: 'text' },
        // Lo stato lo dichiara la tappa: prima «Fatto / In corso / In arrivo» si
        // calcolava dalla posizione di scorrimento della pagina.
        { key: 'status', label: t('Stato'), type: 'select', options: [
          { value: '',        label: t('Nessuno') },
          { value: 'done',    label: t('Fatto') },
          { value: 'current', label: t('In corso') },
          { value: 'next',    label: t('In arrivo') },
        ], description: t('Etichetta accanto alla data, in ogni disposizione. «Nessuno» non mostra niente.') },
        { key: 'image', label: t('Immagine'), type: 'image',
          description: t('Compare in ogni disposizione tranne la Scaletta; in Alternato, Una colonna e Orizzontale solo con «Media nelle card» su Immagini.') },
        { key: 'video', label: t('Video'), type: 'media',
          description: t('YouTube, Vimeo o file video: prende il posto dell\'immagine. Non compare nella Scaletta.') },
        { key: 'icon', label: t('Icona'), type: 'icon',
          description: t('Nel nodo, quando lo Stile mostra le icone.') },
        { key: 'category', label: t('Categoria'), type: 'select', options: [
          { value: 'primary',   label: t('Primario'),   colore: 'var(--olo-color-primary)',   testo: 'var(--olo-color-primary-contrast)' },
          { value: 'secondary', label: t('Secondario'), colore: 'var(--olo-color-secondary)', testo: 'var(--olo-color-secondary-contrast)' },
          { value: 'accent',    label: t('Accento'),    colore: 'var(--olo-color-accent)',    testo: 'var(--olo-color-dark)' },
          { value: 'success',   label: t('Successo'),   colore: 'var(--olo-color-success)',   testo: 'var(--olo-color-light)' },
          { value: 'warning',   label: t('Avviso'),     colore: 'var(--olo-color-warning)',   testo: 'var(--olo-color-dark)' },
          { value: 'info',      label: t('Info'),       colore: 'var(--olo-color-info)',      testo: 'var(--olo-color-light)' },
        ], description: t('Il colore della tappa: nodo, etichetta e barra sopra l’immagine.') },
      ],
      newItemDefaults: { title: t('Nuovo evento'), tag: t('Tappa'), description: t('Descrizione evento.'), date: '', image: '', video: '', icon: 'star', category: 'primary', icon_color: '', status: '' },
      itemLabel: 'Evento',
    },

    // ── Cosa mostrano le card ──
    { type: 'separator', label: t('Nelle card') },
    { key: 'tl_media', label: t('Media nelle card'), type: 'select', options: [
      { value: 'on',  label: t('Immagini') },
      { value: 'off', label: t('Solo testo') },
    ]},
    { key: 'tl_show_tag', label: t('Mostra le etichette'), type: 'toggle' },

    // ── Movimento ──
    { type: 'separator', label: t('Animazione') },
    { key: 'tl_line', label: t('Filo'), type: 'select', options: [
      { value: 'solid',  label: t('Statico') },
      { value: 'scroll', label: t('Si riempie scorrendo') },
    ], description: t('«Si riempie scorrendo» colora filo e nodi man mano che la pagina scorre (solo Alternato e Una colonna). Gli stati delle tappe si scelgono in ogni voce.'),
      condition: { field: 'tl_layout', op: 'in', value: ['alt', 'one', '', null, undefined] } },
    { key: 'tl_reveal', label: t('Ingresso delle card'), type: 'select', options: [
      { value: 'sides',   label: t('Lati') },
      { value: 'bloom',   label: t('Sboccia') },
      { value: 'unroll',  label: t('Srotola') },
      { value: 'slot',    label: t('Scatto') },
      { value: 'flip',    label: t('Ribalta') },
      { value: 'zoom',    label: t('Zoom') },
      { value: 'tendina', label: t('Tendina') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // Una zona per parte (Filo, Nodo, Card, Immagine, Etichetta) fra Aspetto, Testo e Disposizione.
  styleFields: [
    // ── Aspetto ──
    { type: 'separator', label: t('Aspetto') },
    { key: 'tl_theme', label: t('Tema'), type: 'select', options: [
      { value: 'paper', label: t('Carta') },
      { value: 'night', label: t('Notte') },
      { value: 'neon',  label: t('Neon') },
      { value: 'blue',  label: t('Cianografia') },
    ]},
    { key: 'tl_transparent', label: t('Senza lo sfondo del tema'), type: 'toggle',
      description: t('La timeline sta sullo sfondo della sezione invece che sul fondo del tema scelto.') },
    { key: 'tl_color', label: t('Colori'), type: 'select', options: [
      { value: 'cat',  label: t('Per categoria') },
      { value: 'mono', label: t('Solo il primario') },
    ]},

    // ── Filo ──
    { type: 'separator', label: t('Filo') },
    { key: 'tl_thread', label: t('Tratto'), type: 'select', options: [
      { value: 'solid2', label: t('Pieno') },
      { value: 'dash',   label: t('Tratteggio') },
      { value: 'dot',    label: t('Punti') },
      { value: 'comet',  label: t('Cometa') },
    ]},
    { key: 'tl_rail_color', label: t('Colore filo'), type: 'color' },
    { key: 'tl_rail_w', label: t('Spessore filo'), type: 'range', unit: 'px', min: 0, max: 12, step: 1,
      description: t('0: lo spessore del tratto scelto.') },
    { key: 'tl_fill_from', label: t('Riempimento, primo colore'), type: 'color', condition: { field: 'tl_line', value: 'scroll' } },
    { key: 'tl_fill_to', label: t('Riempimento, secondo colore'), type: 'color', condition: { field: 'tl_line', value: 'scroll' } },

    // ── Nodo ──
    { type: 'separator', label: t('Nodo') },
    { key: 'tl_node', label: t('Nel nodo'), type: 'select', options: [
      { value: 'icon', label: t('Icona') },
      { value: 'dot',  label: t('Punto') },
      { value: 'num',  label: t('Numero') },
      { value: 'year', label: t('Anno') },
    ]},
    { key: 'tl_node_size', label: t('Dimensione nodo'), type: 'range', unit: 'px', min: 0, max: 64, step: 2,
      description: t('0: la dimensione dello stile scelto.') },
    { key: 'tl_node_border', label: t('Spessore bordo nodo'), type: 'range', unit: 'px', min: 0, max: 6, step: 1,
      description: t('0: lo spessore dello stile scelto.') },

    // ── Card ──
    { type: 'separator', label: t('Card') },
    { key: 'tl_card', label: t('Forma card'), type: 'select', options: [
      { value: 'bubble',   label: t('Fumetto') },
      { value: 'glass',    label: t('Vetro') },
      { value: 'polaroid', label: t('Polaroid') },
      { value: 'ticket',   label: t('Biglietto') },
    ]},
    { key: 'tl_card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'tl_card_radius', label: t('Raggio card'), type: 'border-radius',
      description: t('Tutti a 0: il raggio della forma scelta.') },
    { key: 'tl_card_pad', label: t('Padding card'), type: 'spacing', min: 0, max: 80,
      description: t('Tutti a 0: il padding della forma scelta.') },
    { key: 'tl_card_maxw', label: t('Larghezza massima card'), type: 'range', unit: 'px', min: 0, max: 600, step: 10,
      description: t('0: la larghezza della disposizione scelta.') },
    { key: 'h_card_width', label: t('Larghezza card'), type: 'range', unit: 'px', min: 200, max: 420, step: 4,
      condition: { field: 'tl_layout', value: 'horizontal' } },

    // ── Immagine ── (solo con le immagini nelle card)
    { type: 'separator', label: t('Immagine'), condition: { field: 'tl_media', op: 'neq', value: 'off' } },
    // Elenco canonico (_imageFrame): i cinque rapporti di prima erano un sottoinsieme,
    // i nuovi sono tutti della forma "N/N" e passano quindi la whitelist `^\d+/\d+$` che
    // PHP e canvas applicano prima di scrivere --tl-media-ar. 'auto' resta la voce che
    // lascia comandare `tl_media_h`, ed è il default: le timeline pubblicate non si muovono.
    { key: 'tl_media_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions() },
    // L'altezza sparisce solo quando comanda la proporzione. Scritto come `show` e non
    // come `condition: { field:'tl_media_ratio', value:'auto' }`: le condizioni si
    // valutano sui settings GREZZI, e una timeline salvata prima che `tl_media_ratio`
    // esistesse (v1.4.9) non ha la chiave — `undefined === 'auto'` è falso e il cursore
    // dell'altezza sarebbe sparito per sempre proprio dalle timeline più vecchie.
    { key: 'tl_media_h', label: t('Altezza immagine'), type: 'range', unit: 'px', min: 0, max: 420, step: 4,
      show: s => (s.tl_media_ratio || 'auto') === 'auto', description: t('0: l’altezza dello stile scelto.') },
    { key: 'tl_media_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover',   label: t('Riempi') },
      { value: 'contain', label: t('Contieni') },
    ]},
    // Punto focale globale: applicato a OGNI immagine/video della timeline. L'immagine è
    // per-item (items[].image) → niente `src` nei contextKeys (il pad degrada a neutro);
    // fit/ratio sono chiavi tile-level reali.
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { fit: 'tl_media_fit', ratio: 'tl_media_ratio' } },
    { key: 'tl_media_radius', label: t('Raggio immagine'), type: 'border-radius',
      description: t('Tutti a 0: il raggio dello stile scelto.') },
    { key: 'tl_media_bar', label: t('Barra colorata sopra'), type: 'toggle',
      description: t('Una barra nel colore della tappa sopra l’immagine.') },

    // ── Testo ──
    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'),
      keys: { family: 'tl_title_family', size: 'tl_title_size', weight: 'tl_title_weight', color: 'tl_title_color' },
      sizeMin: 10, sizeMax: 48, sizeStep: 1 },
    { type: 'typography', label: t('Descrizione'),
      keys: { family: 'tl_text_family', size: 'tl_text_size', color: 'tl_text_color', lineHeight: 'tl_text_lh' },
      sizeMin: 10, sizeMax: 28, sizeStep: 1 },
    { type: 'typography', label: t('Data'),
      keys: { family: 'tl_yr_family', size: 'tl_yr_size', color: 'tl_yr_color' },
      sizeMin: 12, sizeMax: 64, sizeStep: 1 },

    // ── Etichetta ──
    { type: 'separator', label: t('Etichetta'), condition: { field: 'tl_show_tag', op: 'neq', value: false } },
    { key: 'tl_tag_color', label: t('Colore etichetta'), type: 'color',
      description: t('Vuoto: il colore della categoria di ogni tappa.') },

    ...borderFields(),

    // ── Disposizione ──
    { type: 'separator', label: t('Disposizione') },
    { key: 'tl_layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'alt',        label: t('Alternato') },
      { value: 'one',        label: t('Una colonna') },
      { value: 'schedule',   label: t('Scaletta') },
      { value: 'horizontal', label: t('Orizzontale') },
      { value: 'navigator',  label: t('Navigatore') },
    ], description: t('Scaletta: la data a sinistra e il testo accanto, senza card. Navigatore: un asse di date e una tappa alla volta.') },
    { key: 'tl_density', label: t('Densità'), type: 'select', options: [
      { value: 'comfy',   label: t('Comoda') },
      { value: 'compact', label: t('Compatta') },
    ]},
    { key: 'tl_text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},

    // ── Lo stile delle tappe (specchio delle voci) ──
    { type: 'separator', label: t('Eventi') },
    { key: 'items', type: 'content-items', label: t('Eventi'), itemLabel: 'Evento', etichettaDa: 'title', miniaturaDa: 'image', itemFields: [
        { key: 'icon_color', label: t('Colore della tappa'), type: 'color',
          description: t('Vuoto: il colore della categoria.') },
    ] },
  ],
};
