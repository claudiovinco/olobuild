import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { ratioOptions } from './_imageFrame.js';
import { demoObj } from '../demoMedia.js';
import { t } from '@/i18n';

// Gli schemi che reggono i testi sulle foto e i filtri: quelli a griglia o a righe, dove ogni foto è un
// riquadro nel flusso della pagina. Nastri, coverflow e schemi a posizioni libere (sparso, deriva, cascata,
// esagoni, puzzle) no: i testi finirebbero tagliati e il riordino non avrebbe dove andare.
const SCHEMI_SCHEDE = ['grid', 'justified', 'masonry', 'metro', 'mosaic', 'collage', 'diagonal', 'expand', 'elenco', 'strip'];
const SCHEMI_FILTRI = ['grid', 'justified', 'masonry', 'metro', 'mosaic', 'collage', 'diagonal', 'elenco', 'strip'];
const schema = (s) => s.layout || 'grid';
const conSchede = (s) => SCHEMI_SCHEDE.includes(schema(s));
const filtrabile = (s) => SCHEMI_FILTRI.includes(schema(s));
const schedaAccesa = (s) => conSchede(s) && (!!s.scheda || schema(s) === 'elenco');
// «Sotto la foto» c'è solo in Griglia e Masonry: negli altri schemi i testi vanno sulla foto.
const schedaSopra = (s) => ['sopra', 'riquadro'].includes(s.scheda)
  || (s.scheda === 'sotto' && !['grid', 'masonry', 'strip'].includes(schema(s)))
  || (s.scheda === 'biglietto' && !['grid', 'masonry'].includes(schema(s)));
// Le zone in cui può andare ogni parte della card (Posizione libera, solo coi testi sulla foto)
const ZONE = [
  { value: '', label: t('Come lo schema') },
  { value: 'as', label: t('In alto a sinistra') },
  { value: 'ac', label: t('In alto al centro') },
  { value: 'ad', label: t('In alto a destra') },
  { value: 'cs', label: t('Al centro a sinistra') },
  { value: 'cc', label: t('Al centro') },
  { value: 'cd', label: t('Al centro a destra') },
  { value: 'bs', label: t('In basso a sinistra') },
  { value: 'bc', label: t('In basso al centro') },
  { value: 'bd', label: t('In basso a destra') },
  { value: 'ps', label: t('Nel piede, a sinistra') },
  { value: 'pd', label: t('Nel piede, a destra') },
  { value: 'fas', label: t('Sulla foto, in alto a sinistra') },
  { value: 'fad', label: t('Sulla foto, in alto a destra') },
  { value: 'fbs', label: t('Sulla foto, in basso a sinistra') },
  { value: 'fbd', label: t('Sulla foto, in basso a destra') },
];
// Le parti piccole (categoria, numero, nota, icona…) possono stare anche attaccate al titolo
const ZONE_PICCOLE = [
  ...ZONE,
  { value: 'pt', label: t('Davanti al titolo') },
  { value: 'tt', label: t('Dopo il titolo, nella stessa riga') },
  { value: 'dt', label: t('Sotto il titolo') },
];
const conZone = (s) => schedaAccesa(s) && schema(s) !== 'elenco'
  && (['sopra', 'riquadro'].includes(s.scheda) || (s.scheda === 'sotto' && ['grid', 'masonry', 'strip'].includes(schema(s))));
// La galleria ha card di solo testo (citazioni, annunci)?
const haTesti = (s) => Array.isArray(s.images) && s.images.some(i => i && i.type === 'testo');
// La Vista rapida dei prodotti: solo dove c'è WooCommerce (i Prodotti fra i tipi pubblici) e negli schemi che la disegnano
// (non nel nastro che scorre da solo né nell'alveare: hanno una resa loro, senza l'icona della card)
const conNegozio = (s) => (window.oloData?.postTypes || []).some(p => p && p.value === 'product')
  && !['strip_marquee', 'strip_split', 'hexgrid'].includes(schema(s));

/**
 * Tile Pro Gallery — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → media items (gallery), preview video, layout schema/family,
 *                   colonne/righe responsive, autoplay, lightbox toggles/animation,
 *                   visibilità frecce/dots/caption
 *   styleFields[] → preset, sfondo, tipografia, gap, dimensioni visive, raggio,
 *                   colori frecce/dots/overlay, durate animazioni, hover effects,
 *                   filtri immagine, cornice, bordi animati, ombra, bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'progallery',
  name: t('Pro Gallery'),
  icon: 'dashicons-images-alt2',
  category: 'media',

  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    images: [],
    video_preview: 'poster',
    video_pausa: false,
    // Layout
    layout: 'grid',
    layout_family: 'classic',
    puzzle_style: 'classic',
    columns: '3',
    gap: '8',
    img_height: '250px',
    // 'auto' = nessuna proporzione: l'altezza dell'item resta quella di `img_height`,
    // esattamente come prima che questo campo esistesse. Qualunque altro default
    // cambierebbe l'altezza di TUTTE le gallerie già pubblicate.
    aspect_ratio: 'auto',
    aspect_ratio_custom: '16/9',
    object_fit: 'cover',
    object_position: 'center center',
    thumb_radius: '8',
    rows: '0',
    mobile_columns: '2',
    expand_ratio: '4',
    expand_shrink: '0.5',
    expand_speed: '500',
    parallax_height: '1500',
    parallax_intensity: '50',
    drift_height: '1200',
    drift_intensity: '60',
    drift_rotation: '12',
    cascade_spread: '60',
    cascade_overlap: '40',
    cascade_rotation: '8',
    metro_cell_height: '200',
    filmstrip_item_width: '280',
    filmstrip_center_zoom: '1.15',
    filmstrip_side_tilt: '35',
    filmstrip_autoplay: false,
    filmstrip_speed: '4',
    filmstrip_dots: 'dots',
    filmstrip_dots_color: '',
    // Strip (nastro)
    strip_arrows: false,
    strip_arrows_style: 'chevron',
    strip_arrows_size: '36',
    strip_arrows_color: '',
    strip_arrows_bg: '',
    strip_height: '280',
    strip_item_width: '300',
    strip_rows: '2',
    strip_speed: '30',
    strip_pause_hover: true,
    strip_direction: 'left',
    strip_fade_edges: true,
    // Entrance
    entrance: 'none',
    entrance_stagger: '120',
    entrance_duration: '600',
    // Hover
    hover_effect: 'zoom',
    hover_zoom_scale: '1.08',
    hover_tilt_angle: '10',
    hover_magnetic_strength: '24',
    hover_glow_color: '',
    hover_glow_spread: '20',
    hover_frame_in: false,
    hover_caption: 'none',
    hover_caption_bg: 'rgba(0,0,0,0.6)',
    hover_caption_color: '',
    hover_caption_weight: '700',
    hover_frame_inset: '10',
    // Continuous
    continuous: '',
    continuous_speed: '20',
    // Filter
    filter: 'none',
    filter_hover_restore: false,
    duotone_dark: '',
    duotone_light: '',
    duotone_intensity: '80',
    // Frame
    frame: 'none',
    frame_color: '',
    frame_inset_padding: '10',
    // Animated borders
    anim_border: 'none',
    anim_border_color: '',
    anim_border_thickness: '2',
    anim_border_inset: '20',
    anim_border_speed: '3',
    // Lightbox
    lightbox: true,
    lightbox_animation: 'slide',
    lightbox_thumbs: 'none',
    lightbox_thumbs_rows: '1',
    show_caption: false,
    // Testi sulle foto (schede, 23 nov 2026): '' = come prima, solo la didascalia al passaggio
    scheda: '',
    scheda_quando: 'sempre',
    scheda_categoria: true,
    scheda_numero: false,
    scheda_titolo: true,
    scheda_sottotitolo: true,
    scheda_testo: false,
    scheda_icona: 'piu',
    vista_rapida: false,
    scheda_titolo_size: '',
    scheda_colore: '',
    scheda_font: '',
    scheda_peso: '',
    scheda_accento: '',
    scheda_velo: 55,
    scheda_sfondo: '',
    scheda_padding: 18,
    // Elenco a righe
    elenco_miniatura: true,
    // Filtri per categoria
    filtri: false,
    filtri_tutti: '',
    filtri_conteggi: true,
    filtri_stile: 'pillole',
    filtri_allinea: 'left',
    filtri_colore: '',
    filtri_link: false,
    filtri_multipli: false,
    mescola: false,
    cerca: false,
    cerca_testo: '',
    ordina: false,
    piu_foto: '',
    per_pagina: 8,
    scheda_pulsante: '',
    tablet_columns: '',
    large_columns: '',
    small_columns: '',
    scheda_anima: '',
    scheda_numero_formato: '',
    scheda_numero_grande: false,
    scheda_colore_sottotitolo: '',
    scheda_colore_testo: '',
    scheda_stile_categoria: '',
    scheda_stile_numero: '',
    scheda_sottotitolo_maiuscolo: false,
    scheda_durata: false,
    zona_categoria: '',
    zona_numero: '',
    zona_titolo: '',
    zona_sottotitolo: '',
    zona_testo: '',
    zona_pulsante: '',
    zona_icona: '',
    zona_nota: '',
    zona_durata: '',
    scheda_nota_stile: '',
    scheda_nota_colore: '',
    scheda_etichetta_angolo: 'as',
    scheda_separatore: '',
    scheda_numero_prefisso: '',
    scheda_numero_suffisso: '',
    scheda_numero_rovescio: false,
    scheda_numero_icona: '',
    scheda_cornice_meta: false,
    scheda_icona_stile: '',
    scheda_icona_colore: '',
    scheda_corsivo_colore: '',
    scheda_linea_piede: false,
    scheda_linea_sotto: false,
    scheda_riquadro_grandi: false,
    scheda_riquadro_stretto: false,
    scheda_campione: false,
    scheda_sfondo_voce: false,
    scheda_pulsante_stile: '',
    scheda_barra: false,
    scheda_durata_stile: '',
    scheda_titolo_adatta: false,
    scheda_punto_testo: false,
    scheda_categoria_punto: false,
    scheda_categoria_gruppo: '',
    scheda_sot_grandi: false,
    filtri_forma: '',
    filtri_conteggi_stile: '',
    filtri_colore_testo: '',
    filtri_colore_bordo: '',
    filtri_punti: false,
    filtri_linea: false,
    filtri_riga_sopra: false,
    cerca_posizione: '',
    frame_spessore: '',
    pellicola_scritte: true,
    pellicola_rullino: 'R1',
    pellicola_iso: '400',
    pellicola_primo: 1,
    pellicola_passo: 1,
    pellicola_colore_scritte: '',
    strip_avanzamento: false,
    griglia_linee: false,
    griglia_linee_colore: '',
    testo_sfondo: '',
    social: '',
    social_fonte: '',
    social_quante: 12,
    social_link: false,
    // "+N" overlay
    more_bg: 'rgba(0,0,0,0.55)',
    more_color: '',
    more_size: '28',
    // Avanzato
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena trascinata: undici foto nello schema «Metro», celle di misure miste (2×2, 2×1, 1×2) che
  // si incastrano senza buchi in quattro righe; angoli morbidi, zoom leggero e didascalia che sale
  // dal basso al passaggio, lightbox con le didascalie.
  partenza: {
    images: [
      demoObj('montagna-lago', t('Il lago in quota')),
      demoObj('caffe', t('La pausa caffè')),
      demoObj('vaso', t('Ceramica bianca')),
      demoObj('spiaggia-alto', t('La spiaggia dall\'alto')),
      demoObj('foglie', t('Dopo la pioggia')),
      demoObj('facciata-vetro', t('Vetro e acciaio')),
      demoObj('insalata', t('Pranzo leggero')),
      demoObj('citta-tetti', t('I tetti del centro')),
      demoObj('ciotola', t('Porcellana blu')),
      demoObj('bosco-luce', t('Il sentiero nel bosco')),
      demoObj('strada-colline', t('La strada nella valle')),
    ],
    layout: 'metro',
    columns: '5',
    metro_cell_height: '150',
    gap: '10',
    thumb_radius: '12',
    hover_zoom_scale: '1.05',
    hover_caption: 'slide-up',
    show_caption: true,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ─── Media ───
    // `righeExtra`: ogni immagine porta anche sottotitolo e testo, con gli stessi
    // nomi di campo degli item dello ScrollScrub (`subtitle`, `text`), così lo
    // stesso contenuto si sposta da un tile all'altro senza riscriverlo.
    // `puntoFocale` accende l'inquadratura PER SINGOLA FOTO dentro la lista.
    // I contextKeys dicono al campo con che ritaglio disegnare l'anteprima e da
    // quale valore partire: senza, il pad mostrerebbe un ritaglio che non e'
    // quello vero e la scelta si farebbe su un'immagine sbagliata.
    { key: 'images', label: t('Media'), type: 'gallery', righeExtra: true, puntoFocale: true,
      categoria: true, linkVoce: true, misuraVoce: true, cardTesto: true, durataVoce: true, notaVoce: true,
      contextKeys: { fit: 'object_fit', pos: 'object_position' }, supportsDynamic: true,
      dynamicItemFields: [
        { key: 'url', label: t('Immagine'), type: 'image' },
        { key: 'caption', label: t('Titolo'), type: 'text' },
        { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },
        { key: 'text', label: t('Testo'), type: 'text' },
        { key: 'nota', label: t('Nota'), type: 'text' },
        { key: 'category', label: t('Categoria'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link' },
        { key: 'alt', label: t('Testo alternativo'), type: 'text' },
      ] },
    // ─── Foto da un social (al posto di quelle di Media) ───
    { type: 'separator', label: t('Foto da un social') },
    { key: 'social', label: t('Fonte'), type: 'select', options: [
      { value: '', label: t('Le foto di «Media»') },
      { value: 'youtube', label: t('YouTube (canale, @nome o playlist)') },
      { value: 'vimeo', label: t('Vimeo (utente o canale)') },
      { value: 'flickr', label: t('Flickr (utente o tag)') },
      { value: 'pinterest', label: t('Pinterest (profilo o bacheca)') },
      { value: 'instagram', label: t('Instagram (account di OLOsocial)') },
      { value: 'facebook', label: t('Facebook (pagina di OLOsocial)') },
      { value: 'rss', label: t('Feed RSS') },
    ], description: t('Le foto o i video arrivano dal social e si aggiornano da soli ogni ora; quelli di «Media» restano salvati ma non si mostrano. YouTube, Vimeo, Flickr, Pinterest e i feed RSS non chiedono chiavi; Instagram e Facebook usano gli account collegati in OLOsocial.') },
    { key: 'social_fonte', label: t('Canale, account o indirizzo'), type: 'text', placeholder: '@nome', show: s => !!s.social,
      description: t('YouTube: @nome, indirizzo del canale o della playlist. Vimeo: nome utente o channels/nome. Flickr: l’id utente (12345678@N00) o dei tag. Pinterest: nome o nome/bacheca. Instagram e Facebook: @nome o nome della pagina (vuoto = il primo account collegato in OLOsocial). RSS: l’indirizzo del feed.') },
    { key: 'social_quante', label: t('Quante'), type: 'number', min: 1, max: 50, step: 1, show: s => !!s.social },
    { key: 'social_link', label: t('Apri sul social'), type: 'toggle', show: s => !!s.social,
      description: t('Il clic porta al post sul social invece di aprire la lightbox.') },
    { type: 'separator', label: t('Video'), show: s => s.images?.some(i => i.type === 'video') },
    { key: 'video_preview', label: t('Preview video'), type: 'select', options: [
      { value: 'poster', label: t('Poster statico') },
      { value: 'autoplay', label: t('Autoplay muted') },
    ], show: s => s.images?.some(i => i.type === 'video') },
    { key: 'video_pausa', label: t('Pulsante di pausa'), type: 'toggle', show: s => s.video_preview === 'autoplay' && s.images?.some(i => i.type === 'video'),
      description: t('Sui video che partono da soli un pulsante tondo in alto a sinistra per fermarli e farli ripartire. Con «riduci movimento» del sistema i video partono già fermi.') },

    // ─── Layout (struttura schema) ───
    { type: 'separator', label: t('Layout') },
    { key: 'layout_family', label: t('Famiglia'), type: 'select', options: [
      { value: 'classic', label: t('Classica') },
      { value: 'strip', label: t('Nastro') },
    ]},
    { key: 'rows', label: t('Righe visibili (0 = tutte)'), type: 'range', min: 0, max: 5, step: 1,
      show: s => !(s.layout && s.layout.startsWith('strip')) },

    // ─── Coverflow / Filmstrip behavior ───
    { type: 'separator', label: t('Coverflow 3D'),
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_autoplay', label: t('Auto-avanzamento'), type: 'toggle',
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_speed', label: t('Intervallo'), type: 'range', min: 2, max: 8, step: 0.5,
      show: s => (s.layout === 'strip_coverflow' || s.layout === 'filmstrip') && !!s.filmstrip_autoplay },
    { key: 'filmstrip_dots', label: t('Indicatore posizione'), type: 'select', options: [
      { value: 'dots', label: t('Pallini') },
      { value: 'lines', label: t('Linee') },
      { value: 'progress', label: t('Barra progresso') },
      { value: 'fraction', label: t('Frazione (3/12)') },
      { value: 'none', label: t('Nessuno') },
    ], show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },

    // ─── Nastro behavior ───
    { type: 'separator', label: t('Nastro'),
      show: s => s.layout && s.layout.startsWith('strip') },
    { key: 'strip_pause_hover', label: t('Pausa al passaggio mouse'), type: 'toggle',
      show: s => s.layout === 'strip_marquee' || s.layout === 'strip_split' },
    { key: 'strip_direction', label: t('Direzione'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ], show: s => s.layout === 'strip_marquee' },
    { key: 'strip_arrows', label: t('Frecce navigazione'), type: 'toggle',
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' },

    // ─── Testi sulle foto (schede) ───
    // Titolo, sottotitolo e testo vengono dalle righe di ogni foto in «Media», la categoria dalla sua riga
    // «Categoria», il numero dall'ordine. Sui nastri e sugli schemi a posizioni libere i testi non ci stanno.
    { type: 'separator', label: t('Testi sulle foto'), show: s => conSchede(s) },
    { key: 'scheda', label: t('Testi'), type: 'select', options: [
      { value: '', label: t('Solo la didascalia al passaggio') },
      { value: 'sopra', label: t('Sulla foto') },
      { value: 'sotto', label: t('Sotto la foto') },
      { value: 'riquadro', label: t('Sulla foto, in un riquadro') },
      { value: 'biglietto', label: t('Accanto alla foto (biglietto)') },
    ], show: s => conSchede(s) && s.layout !== 'elenco',
      description: t('«Sotto la foto» vale per Griglia, Masonry e Nastro, «Accanto alla foto» per Griglia e Masonry; negli altri schemi i testi vanno sulla foto. Nel biglietto il testo della foto diventa la scritta grande (per esempio «20 mag sab»): accendi «Mostra il testo».') },
    { key: 'scheda_quando', label: t('Quando si vedono'), type: 'select', options: [
      { value: 'sempre', label: t('Sempre') },
      { value: 'passaggio', label: t('Al passaggio del mouse') },
    ], show: s => conSchede(s) && s.layout !== 'elenco' && schedaSopra(s) },
    { key: 'scheda_categoria', label: t('Mostra la categoria'), type: 'toggle', show: s => schedaAccesa(s) },
    { key: 'scheda_numero', label: t('Mostra il numero'), type: 'toggle', show: s => schedaAccesa(s) },
    { key: 'scheda_durata', label: t('Mostra la durata dei video'), type: 'toggle', show: s => schedaAccesa(s) && s.layout !== 'elenco',
      description: t('Accanto alla categoria, per esempio «7:40». Per i video caricati nella libreria la durata si legge da sola; per YouTube e Vimeo aggiunti a mano si scrive nel campo «Durata» del video in «Media».') },
    { key: 'scheda_titolo', label: t('Mostra il titolo'), type: 'toggle', show: s => schedaAccesa(s) },
    { key: 'scheda_sottotitolo', label: t('Mostra il sottotitolo'), type: 'toggle', show: s => schedaAccesa(s) },
    { key: 'scheda_testo', label: t('Mostra il testo'), type: 'toggle', show: s => schedaAccesa(s) },
    { key: 'scheda_icona', label: t('Icona'), type: 'select', options: [
      { value: '', label: t('Nessuna') },
      { value: 'piu', label: t('Più') },
      { value: 'freccia', label: t('Freccia') },
      { value: 'lente', label: t('Lente') },
      { value: 'play', label: t('Play') },
      { value: 'destra', label: t('Freccia a destra') },
      { value: 'linea', label: t('Freccia lunga') },
    ], show: s => schedaAccesa(s) && s.layout !== 'elenco' },
    { key: 'scheda_pulsante', label: t('Pulsante'), type: 'text', placeholder: t('Scopri'),
      show: s => schedaAccesa(s) && s.layout !== 'elenco',
      description: t('Il testo di un pulsante sotto i testi della foto (vuoto = niente pulsante). Porta dove porta la foto: il suo link o la lightbox.') },
    { key: 'elenco_miniatura', label: t('Mostra la miniatura'), type: 'toggle', show: s => s.layout === 'elenco' },

    // ─── Filtri per categoria ───
    { type: 'separator', label: t('Filtri, ricerca e pagine'), show: s => filtrabile(s) },
    { key: 'filtri', label: t('Filtri per categoria'), type: 'toggle', show: s => filtrabile(s),
      description: t('Sopra la galleria un pulsante per ogni categoria e «Tutti»: cliccando, restano le foto di quella categoria e la galleria si riordina. La categoria si scrive sotto ogni foto in «Media»; più categorie si separano con la virgola. Per più gruppi che si combinano scrivi «Gruppo: valore», per esempio «Tipo: Guida, Livello: Base»: sopra la galleria compare una fila per gruppo.') },
    { key: 'filtri_tutti', label: t('Testo di «Tutti»'), type: 'text', placeholder: t('Tutti'),
      show: s => filtrabile(s) && !!s.filtri },
    { key: 'filtri_conteggi', label: t('Mostra quante foto'), type: 'toggle', show: s => filtrabile(s) && !!s.filtri },
    { key: 'filtri_link', label: t('Link ai filtri'), type: 'toggle', show: s => filtrabile(s) && !!s.filtri,
      description: t('L’indirizzo della pagina ricorda il filtro scelto (…#filtro=categoria): copiandolo, o mettendolo in un pulsante o nel menu, la galleria si apre già filtrata.') },
    { key: 'filtri_multipli', label: t('Più valori insieme'), type: 'toggle', show: s => filtrabile(s) && !!s.filtri,
      description: t('In ogni gruppo si possono accendere più valori insieme: restano le foto che ne hanno almeno uno (Guida o Modello). Gruppi diversi restano combinati (Guida o Modello, e di livello Base). Vale coi filtri a pulsanti.') },
    { key: 'cerca', label: t('Ricerca'), type: 'toggle', show: s => filtrabile(s),
      description: t('Una casella che tiene solo le foto col testo cercato nel titolo, nel sottotitolo, nel testo o nella categoria.') },
    { key: 'cerca_testo', label: t('Testo della casella'), type: 'text', placeholder: t('Cerca…'), show: s => filtrabile(s) && !!s.cerca,
      description: t('«%d» diventa il numero delle foto: «Cerca fra %d risorse».') },
    { key: 'ordina', label: t('Ordinamento'), type: 'toggle', show: s => filtrabile(s),
      description: t('Un menu per mettere le foto in ordine di titolo o di testo (anni, prezzi), crescente o decrescente.') },
    { key: 'mescola', label: t('Mescola a ogni visita'), type: 'toggle', show: s => ['grid', 'masonry', 'justified', 'diagonal', 'elenco'].includes(schema(s)),
      description: t('Le foto escono in un ordine diverso a ogni visita. Con l’«Ordinamento» acceso, chi visita può rimetterle in ordine.') },
    { key: 'piu_foto', label: t('Foto da mostrare'), type: 'select', options: [
      { value: '', label: t('Tutte subito') },
      { value: 'carica', label: t('Un gruppo, poi «Mostra altre»') },
      { value: 'infinito', label: t('Un gruppo, poi le altre scorrendo') },
      { value: 'pagine', label: t('A pagine') },
    ], show: s => filtrabile(s) },
    { key: 'per_pagina', label: t('Foto per gruppo o pagina'), type: 'number', min: 2, max: 60, step: 1,
      show: s => filtrabile(s) && !!s.piu_foto },

    // ─── Negozio (comportamento): la Vista rapida dei prodotti di WooCommerce (23 nov 2026) ───
    { type: 'separator', label: t('Negozio'), show: s => conNegozio(s) },
    { key: 'vista_rapida', label: t('Vista rapida dei prodotti'), type: 'toggle', show: s => conNegozio(s),
      description: t('Sulle voci che sono prodotti di WooCommerce (dalla Sorgente dinamica coi Prodotti, o con il link a un prodotto) l’icona della card apre una finestra col prodotto: foto, prezzo, varianti, quantità e «Aggiungi al carrello». Il resto della card porta alla scheda del prodotto. Senza testi sulle foto l’icona compare nell’angolo della foto. Nel builder l’icona si vede ma non apre la finestra.') },

    // ─── Lightbox (comportamento) ───
    { type: 'separator', label: t('Lightbox') },
    { key: 'lightbox', label: t('Attiva lightbox'), type: 'toggle' },
    { key: 'lightbox_animation', label: t('Animazione'), type: 'select', options: [
      { value: 'slide', label: t('Scorrimento') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'scale', label: t('Scala') },
    ], show: s => !!s.lightbox && s.lightbox_thumbs === 'none' },
    { key: 'lightbox_thumbs', label: t('Miniature lightbox'), type: 'select', options: [
      { value: 'none', label: t('Nessuna (UIKit)') },
      { value: 'bottom', label: t('Sotto') },
      { value: 'right', label: t('Destra') },
      { value: 'left', label: t('Sinistra') },
    ], show: s => !!s.lightbox },
    { key: 'lightbox_thumbs_rows', label: t('Righe/Colonne miniature'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
    ], show: s => !!s.lightbox && s.lightbox_thumbs && s.lightbox_thumbs !== 'none' },
    { key: 'show_caption', label: t('Mostra didascalie'), type: 'toggle',
      show: s => !!s.lightbox },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    // ─── Preset ───
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-grid',     label: t('Modern Grid') },
      { value: 'editorial-mosaic', label: t('Editorial Mosaic') },
      { value: 'cinema-strip',    label: t('Cinema Strip') },
      { value: 'bento-pro',       label: t('Bento Pro') },
      { value: 'spotlight-expand', label: t('Spotlight Expand') },
      { value: 'glass-collage',   label: t('Glass Collage') },
      { value: 'neon-honeycomb',  label: t('Neon Honeycomb') },
      { value: 'brutalist-mosaic', label: t('Brutalist Mosaic') },
      { value: 'parallax-drift',  label: t('Parallax Drift') },
      { value: 'sticker-cascade', label: t('Sticker Cascade') },
      { value: 'vhs-coverflow',   label: t('VHS Coverflow') },
      { value: 'tilt-puzzle',     label: t('3D Tilt Puzzle') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { key: 'puzzle_style', label: t('Stile puzzle'), type: 'select', options: [
      { value: 'classic', label: t('Classico') },
      { value: 'zigzag', label: t('Zigzag') },
      { value: 'wave', label: t('Onda') },
      { value: 'castle', label: t('Castello') },
      { value: 'fir', label: t('Abeti') },
    ], show: s => s.layout === 'puzzle' },
    // ─── Dimensioni visive ───
    { type: 'separator', label: t('Dimensioni') },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 24, step: 2 },
    // type 'unit': stessa stringa CSS salvata del vecchio text ('250px', 'auto' resta editabile raw)
    // Sparisce dove non produce CSS: sui nastri (hanno «Altezza nastro») e — da quando
    // esistono le Proporzioni — su Griglia e Diagonale quando una proporzione è attiva,
    // perché lì il renderer scrive la proporzione AL POSTO dell'altezza
    // (class-progallery-tile.php:163 `$item_box`, gemello `itemBox` in ProgalleryTile.vue)
    // e l'altezza non viene più letta da nessun'altra parte. Negli altri schemi (collage,
    // mosaico, espandi, giustificato, puzzle…) l'altezza comanda ancora, e resta.
    // `s.aspect_ratio || 'auto'`: le condizioni si valutano sui settings GREZZI, e una
    // galleria salvata prima di questo sprint la chiave non ce l'ha proprio — il caso
    // «chiave assente» deve cadere dalla parte del campo VISIBILE.
    { key: 'img_height', label: t('Altezza immagine'), type: 'unit', units: ['px', 'vh'], min: 0,
      show: s => !(s.layout && s.layout.startsWith('strip')) && s.layout !== 'elenco'
        && !(['grid', 'diagonal'].includes(s.layout || 'grid') && (s.aspect_ratio || 'auto') !== 'auto') },
    // Proporzioni: il pezzo di cornice che mancava (adattamento e punto focale c'erano
    // già). Visibile solo su Griglia e Diagonale, gli unici schemi in cui l'item e' un
    // rettangolo largo quanto la colonna e la proporzione può davvero dargli la forma:
    // negli altri l'altezza viene dalle righe della griglia, da posizioni assolute o
    // dall'altezza del nastro, e un controllo mostrato li' non farebbe niente.
    { key: 'aspect_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions({ custom: true }),
      description: t('Con una proporzione attiva comanda lei, e «Altezza immagine» sparisce.'),
      show: s => ['grid', 'diagonal'].includes(s.layout || 'grid') },
    { key: 'aspect_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4, 1.618'),
      show: s => ['grid', 'diagonal'].includes(s.layout || 'grid'),
      condition: { field: 'aspect_ratio', op: 'eq', value: 'custom' } },
    // La galleria è mista: gli item possono essere foto, video in autoplay (<video>,
    // che object-fit lo segue) oppure embed YouTube/Vimeo, resi come <iframe>
    // (class-progallery-tile.php, ramo `use_embed_autoplay`). Su un iframe object-fit e
    // object-position non hanno alcun effetto: lì comanda solo la proporzione del
    // contenitore. Non si può togliere il controllo — per foto e video serve — quindi
    // lo si dichiara, invece di lasciar credere che agisca su tutto.
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover', label: t('Riempi') },
      { value: 'contain', label: t('Contieni') },
    ], description: t('Vale per foto e video in autoplay; gli embed YouTube/Vimeo non lo seguono.') },
    // Punto focale della GALLERIA: vale per ogni foto che non ne ha uno suo, ed e'
    // quindi il valore di ripiego, non più l'unico. Quello per singola foto sta
    // nella lista dei media, in CONTENUTO, dove si vede la foto a cui si applica.
    // URL per-item → niente src nei contextKeys (qui il pad non ha un'immagine da
    // mostrare: non saprebbe quale delle tante).
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { fit: 'object_fit', ratio: 'aspect_ratio', ratioCustom: 'aspect_ratio_custom' } },
    withHover({ key: 'thumb_radius', label: t('Raggio'), type: 'border-radius' }),

    // ─── Espandi ───
    { type: 'separator', label: t('Layout — Espandi (spotlight)'), show: s => s.layout === 'expand' },
    { key: 'expand_ratio', label: t('Rapporto espansione'), type: 'range', min: 2, max: 6, step: 0.5,
      show: s => s.layout === 'expand' },
    { key: 'expand_shrink', label: t('Compressione altri'), type: 'range', min: 0.2, max: 1, step: 0.1,
      show: s => s.layout === 'expand' },
    { key: 'expand_speed', label: t('Velocità'), type: 'range', min: 200, max: 1000, step: 50,
      show: s => s.layout === 'expand' },

    // ─── Parallasse ───
    { type: 'separator', label: t('Layout — Parallasse'), show: s => s.layout === 'parallax' },
    { key: 'parallax_height', label: t('Altezza area'), type: 'range', min: 800, max: 3000, step: 100,
      show: s => s.layout === 'parallax' },
    { key: 'parallax_intensity', label: t('Intensità parallasse'), type: 'range', min: 10, max: 100, step: 5,
      show: s => s.layout === 'parallax' },

    // ─── Deriva ───
    { type: 'separator', label: t('Layout — Deriva'), show: s => s.layout === 'drift' },
    { key: 'drift_height', label: t('Altezza area'), type: 'range', min: 600, max: 2500, step: 100,
      show: s => s.layout === 'drift' },
    { key: 'drift_intensity', label: t('Intensità movimento'), type: 'range', min: 10, max: 100, step: 5,
      show: s => s.layout === 'drift' },
    { key: 'drift_rotation', label: t('Rotazione max'), type: 'range', min: 0, max: 25, step: 1,
      show: s => s.layout === 'drift' },

    // ─── Cascata ───
    { type: 'separator', label: t('Layout — Cascata'), show: s => s.layout === 'cascade' },
    { key: 'cascade_spread', label: t('Distanza separazione'), type: 'range', min: 20, max: 100, step: 5,
      show: s => s.layout === 'cascade' },
    { key: 'cascade_overlap', label: t('Sovrapposizione iniziale'), type: 'range', min: 10, max: 80, step: 5,
      show: s => s.layout === 'cascade' },
    { key: 'cascade_rotation', label: t('Rotazione carte'), type: 'range', min: 0, max: 20, step: 1,
      show: s => s.layout === 'cascade' },

    // ─── Metro ───
    { type: 'separator', label: t('Layout — Metro'), show: s => s.layout === 'metro' },
    { key: 'metro_cell_height', label: t('Altezza cella'), type: 'range', min: 100, max: 400, step: 10,
      show: s => s.layout === 'metro' },

    // ─── Coverflow 3D visivo ───
    { type: 'separator', label: t('Coverflow 3D — aspetto'),
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_item_width', label: t('Larghezza foto'), type: 'range', min: 180, max: 450, step: 10,
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_center_zoom', label: t('Zoom centro'), type: 'range', min: 1.0, max: 1.5, step: 0.05,
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_side_tilt', label: t('Rotazione 3D laterali'), type: 'range', min: 0, max: 60, step: 1,
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_dots_color', label: t('Colore indicatore'), type: 'color',
      show: s => (s.layout === 'strip_coverflow' || s.layout === 'filmstrip') && s.filmstrip_dots && s.filmstrip_dots !== 'none' },

    // ─── Nastro aspetto ───
    { type: 'separator', label: t('Nastro — aspetto'),
      show: s => s.layout && s.layout.startsWith('strip') },
    { key: 'strip_height', label: t('Altezza nastro'), type: 'range', min: 150, max: 500, step: 10,
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' },
    { key: 'strip_item_width', label: t('Larghezza foto'), type: 'range', min: 150, max: 500, step: 10,
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_multi' && s.layout !== 'strip_coverflow' },
    { key: 'strip_rows', label: t('Righe'), type: 'range', min: 2, max: 3, step: 1,
      show: s => s.layout === 'strip_multi' },
    { key: 'strip_fade_edges', label: t('Sfumatura bordi'), type: 'toggle',
      show: s => s.layout && s.layout.startsWith('strip') },
    { key: 'strip_avanzamento', label: t('Barretta di avanzamento'), type: 'toggle',
      show: s => ['strip', 'strip_collage', 'strip_multi'].includes(s.layout),
      description: t('Sotto il nastro una barretta corta che dice a che punto si è.') },
    { key: 'strip_speed', label: t('Durata ciclo'), type: 'range', min: 10, max: 60, step: 2,
      show: s => s.layout === 'strip_marquee' || s.layout === 'strip_split' },
    { key: 'strip_arrows_style', label: t('Stile frecce'), type: 'select', options: [
      { value: 'chevron', label: t('Chevron') },
      { value: 'arrow', label: t('Freccia') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'pill', label: t('Pillola') },
      { value: 'minimal', label: t('Minimale') },
    ], show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },
    { key: 'strip_arrows_size', label: t('Dimensione frecce'), type: 'range', min: 24, max: 60, step: 2,
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },
    { key: 'strip_arrows_color', label: t('Colore frecce'), type: 'color',
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },
    { key: 'strip_arrows_bg', label: t('Sfondo frecce'), type: 'color',
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },

    // ─── Entrance ───
    { type: 'separator', label: t('Animazione ingresso (solo frontend)') },
    { key: 'entrance', label: t('Effetto'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'fade-up', label: t('Fade su') },
      { value: 'fade-scale', label: t('Fade + Scala') },
      { value: 'flip', label: t('Flip') },
      { value: 'slide-in', label: t('Scorrimento') },
      { value: 'blur-in', label: t('Sfocatura') },
      { value: 'split-sides', label: t('Lati alterni') },
      { value: 'fall', label: t('Caduta (bounce)') },
      { value: 'wind', label: t('Soffio di vento') },
      { value: 'zoom-center', label: t('Zoom dal centro') },
      { value: 'land', label: t('Atterraggio 3D') },
    ]},
    { key: 'entrance_stagger', label: t('Ritardo fra le foto'), type: 'range', min: 80, max: 400, step: 20,
      show: s => s.entrance && s.entrance !== 'none' },
    { key: 'entrance_duration', label: t('Durata'), type: 'range', min: 300, max: 1200, step: 50,
      show: s => s.entrance && s.entrance !== 'none' },

    // ─── Hover ───
    { type: 'separator', label: t('Effetti hover') },
    { key: 'hover_effect', label: t('Effetto'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'lift', label: t('Sollevamento') },
      { value: 'tilt3d', label: t('Tilt 3D') },
      { value: 'glow', label: t('Bagliore') },
      { value: 'blur-peers', label: t('Sfoca gli altri') },
      { value: 'magnetic', label: t('Magnetico') },
    ]},
    { key: 'hover_zoom_scale', label: t('Intensità zoom'), type: 'range', min: 1.05, max: 1.30, step: 0.01,
      show: s => s.hover_effect === 'zoom' },
    { key: 'hover_tilt_angle', label: t('Angolo tilt'), type: 'range', min: 5, max: 20, step: 1,
      show: s => s.hover_effect === 'tilt3d' },
    { key: 'hover_magnetic_strength', label: t('Intensità magnetismo'), type: 'range', min: 8, max: 60, step: 2,
      show: s => s.hover_effect === 'magnetic' },
    { key: 'hover_glow_color', label: t('Colore bagliore'), type: 'color',
      show: s => s.hover_effect === 'glow' },
    { key: 'hover_glow_spread', label: t('Intensità bagliore'), type: 'range', min: 8, max: 50, step: 2,
      show: s => s.hover_effect === 'glow' },
    { key: 'hover_caption', label: t('Didascalia hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'slide-up', label: t('Scorrimento dal basso') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'overlay', label: t('Overlay pieno') },
      { value: 'frame', label: t('Cornice elegante') },
      { value: 'centered', label: t('Testo centrato') },
    // Coi «Testi sulle foto» accesi la didascalia al passaggio non c'è: i testi li disegna la scheda.
    ], show: s => !schedaAccesa(s) },
    { key: 'hover_caption_bg', label: t('Sfondo didascalia'), type: 'color',
      show: s => !schedaAccesa(s) && s.hover_caption && s.hover_caption !== 'none' && s.hover_caption !== 'centered' },
    { type: 'typography', label: t('Didascalia (hover)'), responsiveKeys: [], keys: { weight: 'hover_caption_weight', color: 'hover_caption_color' },
      show: s => !schedaAccesa(s) },
    { key: 'hover_frame_inset', label: t('Padding cornice'), type: 'spacing', max: 40,
      show: s => s.hover_caption === 'frame' },

    // ─── Animazione continua ───
    { type: 'separator', label: t('Animazione continua') },
    { key: 'continuous', label: t('Effetti'), type: 'multi_pills', options: [
      { value: 'float', label: t('Galleggiamento') },
      { value: 'drift', label: t('Deriva') },
      { value: 'breathe', label: t('Respiro') },
      { value: 'rotate-slow', label: t('Rotazione lenta') },
      { value: 'kenburns', label: t('Ken Burns') },
      { value: 'shimmer', label: t('Shimmer') },
    ]},
    { key: 'continuous_speed', label: t('Durata ciclo'), type: 'range', min: 10, max: 40, step: 1,
      show: s => !!s.continuous && s.continuous !== 'none' },

    // ─── Filtro ───
    { type: 'separator', label: t('Filtro immagine') },
    { key: 'filter', label: t('Filtro'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'grayscale', label: t('Bianco e nero') },
      { value: 'sepia', label: t('Seppia') },
      { value: 'high-contrast', label: t('Alto contrasto') },
      { value: 'warm', label: t('Caldo') },
      { value: 'cool', label: t('Freddo') },
      { value: 'vintage', label: t('Vintage') },
      { value: 'duotone', label: t('Duotono') },
    ]},
    { key: 'filter_hover_restore', label: t('Rimuovi filtro al hover'), type: 'toggle',
      show: s => s.filter && s.filter !== 'none' },
    { key: 'duotone_dark', label: t('Colore scuro'), type: 'color',
      show: s => s.filter === 'duotone' },
    { key: 'duotone_light', label: t('Colore chiaro'), type: 'color',
      show: s => s.filter === 'duotone' },
    { key: 'duotone_intensity', label: t('Intensità duotone'), type: 'range', min: 0, max: 100, step: 5,
      show: s => s.filter === 'duotone' },

    // ─── Cornice ───
    { type: 'separator', label: t('Cornice') },
    { key: 'frame', label: t('Stile cornice'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'polaroid', label: t('Polaroid') },
      { value: 'rounded', label: t('Arrotondata') },
      { value: 'shadow-box', label: t('Riquadro ombra') },
      { value: 'torn', label: t('Strappata') },
      { value: 'tape', label: t('Nastro adesivo') },
      { value: 'inset', label: t('Interna') },
      { value: 'pellicola', label: t('Pellicola') },
      { value: 'filo', label: t('Filo interno') },
      { value: 'bordo', label: t('Bordo pieno') },
    ]},
    { key: 'frame_color', label: t('Colore cornice'), type: 'color',
      show: s => ['polaroid', 'shadow-box', 'inset', 'pellicola', 'filo', 'bordo'].includes(s.frame),
      description: t('Con un colore per ogni voce (Colore e segno delle voci) filo, bordo e pellicola prendono quello della voce.') },
    { key: 'frame_spessore', label: t('Spessore'), type: 'range', min: 1, max: 16, step: 1, unit: 'px', show: s => ['filo', 'bordo'].includes(s.frame) },
    { key: 'pellicola_scritte', label: t('Scritte sul bordo'), type: 'toggle', show: s => s.frame === 'pellicola',
      description: t('Le scritte che la pellicola porta stampate sul bordo, come su un provino a contatto: «▸ 03» è il numero del fotogramma (la freccia lo indica), «03A» il mezzo fotogramma che segue, «R1» il rullino e «400» la sensibilità ISO della pellicola.') },
    { key: 'pellicola_rullino', label: t('Rullino'), type: 'text', placeholder: 'R1', incollaStile: false, show: s => s.frame === 'pellicola' && s.pellicola_scritte !== false },
    { key: 'pellicola_iso', label: t('Sensibilità ISO'), type: 'text', placeholder: '400', incollaStile: false, show: s => s.frame === 'pellicola' && s.pellicola_scritte !== false },
    { key: 'pellicola_primo', label: t('Primo fotogramma'), type: 'number', min: 0, max: 99, step: 1, show: s => s.frame === 'pellicola' && s.pellicola_scritte !== false },
    { key: 'pellicola_passo', label: t('Ogni quanti fotogrammi'), type: 'number', min: 1, max: 9, step: 1, show: s => s.frame === 'pellicola' && s.pellicola_scritte !== false,
      description: t('Sul provino il fotografo sceglie i fotogrammi migliori: con 3 e 4 i numeri vanno 03, 07, 11…') },
    { key: 'pellicola_colore_scritte', label: t('Colore delle scritte'), type: 'color', show: s => s.frame === 'pellicola' && s.pellicola_scritte !== false },
    { key: 'frame_inset_padding', label: t('Padding dal bordo'), type: 'spacing', max: 40 },

    // ─── Bordi animati ───
    { type: 'separator', label: t('Bordi animati') },
    { key: 'anim_border', label: t('Tipo bordo animato'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'frame-in', label: t('Cornice entrante') },
      { value: 'neon', label: t('Neon') },
      { value: 'ants', label: t('Formiche') },
      { value: 'corners', label: t('Angoli') },
      { value: 'pulse', label: t('Pulsazione') },
      { value: 'radar', label: t('Radar') },
    ]},
    { key: 'anim_border_color', label: t('Colore'), type: 'color',
      show: s => s.anim_border && s.anim_border !== 'none' },
    { key: 'anim_border_thickness', label: t('Spessore'), type: 'range', min: 1, max: 6, step: 1,
      show: s => s.anim_border && s.anim_border !== 'none' && s.anim_border !== 'frame-in' },
    { key: 'anim_border_inset', label: t('Distanza dal bordo'), type: 'range', min: 4, max: 50, step: 2,
      show: s => s.anim_border === 'frame-in' },
    { key: 'anim_border_speed', label: t('Velocità'), type: 'range', min: 1, max: 10, step: 0.5,
      show: s => s.anim_border && s.anim_border !== 'none' && s.anim_border !== 'frame-in' && s.anim_border !== 'corners' },

    // ─── +N overlay ───
    { type: 'separator', label: 'Indicatore "+N"' },
    { key: 'more_bg', label: t('Sfondo overlay'), type: 'color' },
    { type: 'typography', label: t('Mostra altro'), responsiveKeys: [], keys: { size: 'more_size', color: 'more_color' }, sizeMin: 16, sizeMax: 48, sizeStep: 2 },

    // ─── Testi sulle foto ───
    { type: 'separator', label: t('Testi sulle foto'), show: s => schedaAccesa(s) || haTesti(s) },
    { key: 'scheda_accento', label: t('Colore di categoria e numero'), type: 'color', show: s => schedaAccesa(s) },
    { key: 'scheda_velo', label: t('Velo sotto i testi'), type: 'range', min: 0, max: 100, step: 5, unit: '%',
      show: s => schedaAccesa(s) && schedaSopra(s) && schema(s) !== 'elenco' },
    { key: 'scheda_velo_alto', label: t('Velo dietro i testi in alto'), type: 'range', min: 0, max: 100, step: 5, unit: '%',
      description: t('Scurisce la foto dietro le parti in alto (numero, categoria, nota) fino al bordo, qualunque sia l’altezza della loro riga. Vuoto: metà del velo, sul primo quarto della foto.'),
      show: s => schedaAccesa(s) && schedaSopra(s) && schema(s) !== 'elenco' },
    { key: 'scheda_sfondo', label: t('Sfondo dei testi'), type: 'color',
      show: s => schedaAccesa(s) && (s.scheda === 'riquadro' || (s.scheda === 'sotto' && ['grid', 'masonry', 'strip'].includes(schema(s))) || (s.scheda === 'biglietto' && ['grid', 'masonry'].includes(schema(s)))) },
    { key: 'scheda_sfondo_voce', label: t('Fondo nel colore della voce'), type: 'toggle',
      show: s => schedaAccesa(s) && s.scheda === 'sotto' && ['grid', 'masonry', 'strip'].includes(schema(s)),
      description: t('La card prende il colore scelto per ogni voce (Colore e segno delle voci), e il testo si fa chiaro o scuro da sé: le card colorate di un negozio.') },
    { key: 'scheda_riquadro_grandi', label: t('Riquadro solo sulle foto grandi'), type: 'toggle',
      show: s => schedaAccesa(s) && s.scheda === 'riquadro' && ['metro', 'mosaic', 'collage'].includes(schema(s)),
      description: t('Il riquadro coi testi resta sulle foto grandi (almeno due colonne per due righe); sulle piccole i testi stanno sulla foto, senza sottotitolo e senza piede.') },
    { key: 'scheda_riquadro_stretto', label: t('Riquadro stretto'), type: 'toggle', show: s => schedaAccesa(s) && s.scheda === 'riquadro',
      description: t('Il riquadro largo quanto i suoi testi, in un angolo, invece che da un lato all’altro.') },
    { key: 'scheda_padding', label: t('Padding testi'), type: 'spacing', max: 64, show: s => schedaAccesa(s) },
    { key: 'scheda_anima', label: t('Entrata dei testi'), type: 'select', options: [
      { value: '', label: t('Tutti insieme') },
      { value: 'sale', label: t('Uno alla volta, dal basso') },
      { value: 'lato', label: t('Uno alla volta, da sinistra') },
      { value: 'maschera', label: t('Uno alla volta, si svelano') },
      { value: 'scala', label: t('Uno alla volta, si ingrandiscono') },
      { value: 'sfuma', label: t('Uno alla volta, sfumano') },
    ], show: s => (schedaAccesa(s) && schema(s) !== 'elenco') || haTesti(s),
      description: t('Categoria, numero, titolo, sottotitolo, testo e pulsante arrivano uno dopo l’altro quando la foto entra nello schermo, o al passaggio se i testi compaiono così. Con «riduci movimento» del sistema non si muove niente.') },

    // ─── Titolo ───
    { type: 'separator', label: t('Titolo'), show: s => schedaAccesa(s) || haTesti(s) },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [],
      keys: { family: 'scheda_font', size: 'scheda_titolo_size', weight: 'scheda_peso', color: 'scheda_colore',
        italic: 'scheda_titolo_italic', transform: 'scheda_titolo_text_transform', letterSpacing: 'scheda_titolo_letter_spacing', lineHeight: 'scheda_titolo_line_height' },
      sizeMin: 12, sizeMax: 72, show: s => schedaAccesa(s) },
    { key: 'scheda_corsivo_colore', label: t('Colore del corsivo'), type: 'color', show: s => schedaAccesa(s) || haTesti(s),
      description: t('Nel titolo il pezzo fra due asterischi va in corsivo: «Vestiti per *la luce lunga*». Qui il suo colore; vuoto = come il titolo.') },
    { key: 'scheda_titolo_adatta', label: t('Titolo grande quanto la foto'), type: 'toggle', show: s => schedaAccesa(s) && schema(s) !== 'elenco',
      description: t('Il titolo cresce con la foto: grande sulle foto grandi, piccolo sulle piccole (negli schemi a misure miste).') },

    // ─── Sottotitolo e testo ───
    { type: 'separator', label: t('Sottotitolo e testo'), show: s => schedaAccesa(s) && (s.scheda_sottotitolo !== false || !!s.scheda_testo) },
    { type: 'typography', label: t('Sottotitolo'), responsiveKeys: [],
      keys: { family: 'scheda_sot_font_family', size: 'scheda_sot_font_size', weight: 'scheda_sot_font_weight', color: 'scheda_colore_sottotitolo',
        italic: 'scheda_sot_italic', transform: 'scheda_sot_text_transform', letterSpacing: 'scheda_sot_letter_spacing' },
      sizeMin: 8, sizeMax: 32, show: s => schedaAccesa(s) && s.scheda_sottotitolo !== false },
    { key: 'scheda_sottotitolo_maiuscolo', label: t('Sottotitolo in maiuscolo piccolo'), type: 'toggle', show: s => schedaAccesa(s) && s.scheda_sottotitolo !== false && schema(s) !== 'elenco' },
    { key: 'scheda_sot_grandi', label: t('Sottotitolo solo sulle foto grandi'), type: 'toggle',
      show: s => schedaAccesa(s) && s.scheda_sottotitolo !== false && ['metro', 'mosaic', 'collage'].includes(schema(s)) },
    { type: 'typography', label: t('Testo'), responsiveKeys: [],
      keys: { family: 'scheda_txt_font_family', size: 'scheda_txt_font_size', weight: 'scheda_txt_font_weight', color: 'scheda_colore_testo',
        italic: 'scheda_txt_italic', transform: 'scheda_txt_text_transform', letterSpacing: 'scheda_txt_letter_spacing' },
      sizeMin: 8, sizeMax: 32, show: s => schedaAccesa(s) && !!s.scheda_testo },
    { key: 'scheda_punto_testo', label: t('Pallino davanti al testo'), type: 'toggle', show: s => schedaAccesa(s) && !!s.scheda_testo && schema(s) !== 'elenco',
      description: t('Un pallino nel colore della voce davanti al testo, come la luce di una lampada accanto alle sue misure.') },

    // ─── Categoria e durata ───
    { type: 'separator', label: t('Categoria e durata'), show: s => schedaAccesa(s) && (s.scheda_categoria !== false || !!s.scheda_durata || !!s.scheda_numero) },
    { type: 'typography', label: t('Categoria, numero e durata'), responsiveKeys: [],
      keys: { family: 'scheda_meta_font_family', size: 'scheda_meta_font_size', weight: 'scheda_meta_font_weight', color: 'scheda_meta_color',
        transform: 'scheda_meta_text_transform', letterSpacing: 'scheda_meta_letter_spacing' },
      sizeMin: 8, sizeMax: 24, show: s => schedaAccesa(s) },
    { key: 'scheda_stile_categoria', label: t('Stile della categoria'), type: 'select', options: [
      { value: '', label: t('Riquadro') },
      { value: 'testo', label: t('Solo testo') },
      { value: 'pieno', label: t('Etichetta piena') },
      { value: 'chiaro', label: t('Etichetta chiara') },
      { value: 'scuro', label: t('Etichetta scura') },
    ], show: s => schedaAccesa(s) && s.scheda_categoria !== false && schema(s) !== 'elenco',
      description: t('L’etichetta piena prende il colore della voce (Colore e segno delle voci) o, se manca, quello di categoria e numero: ogni categoria può avere il suo. Chiara e scura stanno bene sulle foto.') },
    { key: 'scheda_categoria_punto', label: t('Pallino davanti alla categoria'), type: 'toggle', show: s => schedaAccesa(s) && s.scheda_categoria !== false && schema(s) !== 'elenco' },
    { key: 'scheda_categoria_gruppo', label: t('Gruppo da mostrare'), type: 'text', placeholder: t('Tutti'),
      show: s => schedaAccesa(s) && s.scheda_categoria !== false,
      description: t('Con le categorie a gruppi («Tipo: Guida, Livello: Base») la card mostra solo quelle del gruppo scritto qui, per esempio «Tipo». Vuoto = tutte.') },
    { key: 'scheda_durata_stile', label: t('Stile della durata'), type: 'select', options: [
      { value: '', label: t('Solo testo') },
      { value: 'etichetta', label: t('Etichetta col play') },
    ], show: s => schedaAccesa(s) && !!s.scheda_durata },
    { key: 'scheda_separatore', label: t('Separatore'), type: 'select', options: [
      { value: '', label: t('Nessuno') },
      { value: 'punto', label: '·' },
      { value: 'barra', label: '/' },
      { value: 'trattino', label: '—' },
      { value: 'riga', label: '|' },
    ], show: s => schedaAccesa(s) && schema(s) !== 'elenco',
      description: t('Fra le parti piccole che stanno nella stessa zona: «Interni · 2026», «Regia / 2026», e fra categoria e durata sotto il numero grande.') },

    // ─── Numero ───
    { type: 'separator', label: t('Numero'), show: s => schedaAccesa(s) && !!s.scheda_numero },
    { key: 'scheda_numero_grande', label: t('Numero grande'), type: 'toggle', show: s => schedaAccesa(s) && !!s.scheda_numero && ['sopra', 'riquadro'].includes(s.scheda),
      description: t('Il numero in alto a sinistra, grande, nel carattere dei titoli; sotto, categoria e durata. Come nelle pareti di video.') },
    { key: 'scheda_numero_formato', label: t('Formato del numero'), type: 'select', options: [
      { value: '', label: '01' },
      { value: 'su', label: '01 / 12' },
      { value: 'n', label: t('N. 01 / 12') },
      { value: 'semplice', label: '1' },
      { value: 'tre', label: '001' },
    ], show: s => schedaAccesa(s) && !!s.scheda_numero },
    { key: 'scheda_numero_prefisso', label: t('Prima del numero'), type: 'text', placeholder: 'Q', show: s => schedaAccesa(s) && !!s.scheda_numero,
      description: t('Lettere o segni davanti e dietro al numero: «Q» dà «Q01», «(» e «)» danno «(01)», «No. » dà «No. 01».') },
    { key: 'scheda_numero_suffisso', label: t('Dopo il numero'), type: 'text', placeholder: ')', show: s => schedaAccesa(s) && !!s.scheda_numero },
    { key: 'scheda_numero_rovescio', label: t('Conta all’indietro'), type: 'toggle', show: s => schedaAccesa(s) && !!s.scheda_numero,
      description: t('La prima voce ha il numero più alto: 016, 015, 014… come gli indici dei progetti, dal più recente.') },
    { key: 'scheda_numero_icona', label: t('Icona del numero'), type: 'icon', show: s => schedaAccesa(s) && !!s.scheda_numero && schema(s) !== 'elenco' },
    { key: 'scheda_stile_numero', label: t('Stile del numero'), type: 'select', options: [
      { value: '', label: t('Solo testo') },
      { value: 'pieno', label: t('Etichetta piena') },
    ], show: s => schedaAccesa(s) && !!s.scheda_numero && !s.scheda_numero_grande && schema(s) !== 'elenco' },
    { key: 'scheda_cornice_meta', label: t('Numero e categoria in un riquadro'), type: 'toggle',
      show: s => schedaAccesa(s) && !!s.scheda_numero && s.scheda_categoria !== false && schema(s) !== 'elenco',
      description: t('Quando stanno nella stessa zona, numero e categoria vanno in un riquadro sottile, col numero pieno nel colore della voce.') },

    // ─── Nota ───
    // Anche senza voci scritte a mano: con la «Sorgente dinamica» le voci (e le note, il prezzo dei prodotti) arrivano
    // solo nel sito, e la sezione spariva proprio dove serviva (23 nov 2026).
    { type: 'separator', label: t('Nota'), show: s => schedaAccesa(s) && (!Array.isArray(s.images) || !s.images.length || s.images.some(i => i && i.nota)) },
    { key: 'scheda_nota_stile', label: t('Stile della nota'), type: 'select', options: [
      { value: '', label: t('Solo testo') },
      { value: 'grande', label: t('Grande (un prezzo)') },
      { value: 'etichetta', label: t('Etichetta') },
      { value: 'linea', label: t('Con la lineetta') },
    ], show: s => schedaAccesa(s),
      description: t('La nota di ogni voce (in Media): un prezzo, un anno, un codice. «Grande» fa grande la prima parola e piccolo il resto («€3.40 330 ml»).') },
    { key: 'scheda_nota_colore', label: t('Colore dell’etichetta'), type: 'color', show: s => schedaAccesa(s) && s.scheda_nota_stile === 'etichetta',
      description: t('Vuoto = il colore di ogni voce.') },
    { type: 'typography', label: t('Nota'), responsiveKeys: [],
      keys: { family: 'scheda_nota_font_family', size: 'scheda_nota_font_size', weight: 'scheda_nota_font_weight', color: 'scheda_nota_color',
        transform: 'scheda_nota_text_transform', letterSpacing: 'scheda_nota_letter_spacing' },
      sizeMin: 8, sizeMax: 32, show: s => schedaAccesa(s) },

    // ─── Icona e pulsante ───
    { type: 'separator', label: t('Icona e pulsante'), show: s => schedaAccesa(s) && schema(s) !== 'elenco' && (!!s.scheda_icona || !!s.scheda_pulsante) },
    { key: 'scheda_icona_stile', label: t('Stile dell’icona'), type: 'select', options: [
      { value: '', label: t('Nel cerchio') },
      { value: 'quadrato', label: t('Nel quadrato') },
      { value: 'pieno', label: t('Nel cerchio pieno') },
      { value: 'nudo', label: t('Sola') },
    ], show: s => schedaAccesa(s) && !!s.scheda_icona },
    { key: 'scheda_icona_colore', label: t('Colore dell’icona'), type: 'color', show: s => schedaAccesa(s) && !!s.scheda_icona },
    { key: 'scheda_pulsante_stile', label: t('Stile del pulsante'), type: 'select', options: [
      { value: '', label: t('Pieno') },
      { value: 'contorno', label: t('A contorno') },
      { value: 'testo', label: t('Scritta sottolineata') },
    ], show: s => schedaAccesa(s) && !!s.scheda_pulsante },

    // ─── Linee e colore ───
    { type: 'separator', label: t('Linee e colore'), show: s => schedaAccesa(s) && schema(s) !== 'elenco' },
    { key: 'scheda_linea_piede', label: t('Linea sopra il piede'), type: 'toggle', show: s => conZone(s),
      description: t('Una linea sottile sopra il piede della card (le parti messe «Nel piede»).') },
    { key: 'scheda_linea_sotto', label: t('Linea sotto ogni card'), type: 'toggle', show: s => schedaAccesa(s) && s.scheda === 'sotto' },
    { key: 'scheda_barra', label: t('Barretta nel colore della voce'), type: 'toggle', show: s => schedaAccesa(s) && schema(s) !== 'elenco',
      description: t('Una barretta in fondo alla foto, nel colore scelto per ogni voce.') },
    { key: 'scheda_campione', label: t('Quadratino del colore'), type: 'toggle', show: s => schedaAccesa(s) && schema(s) !== 'elenco',
      description: t('Un quadratino nel colore della voce in alto a destra, come il campione di un materiale.') },

    // ─── Posizione delle parti ───
    { type: 'separator', label: t('Posizione delle parti'), show: s => conZone(s) },
    { key: 'zona_titolo', label: t('Posizione del titolo'), type: 'select', options: ZONE, show: s => conZone(s),
      description: t('Ogni parte della card può andare in una delle nove zone, nel piede (una riga in fondo) o, coi testi sotto la foto, sulla foto. Le parti piccole possono stare anche davanti, dopo o sotto il titolo. «Come lo schema» la lascia dov’è.') },
    { key: 'zona_sottotitolo', label: t('Posizione del sottotitolo'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) },
    { key: 'zona_testo', label: t('Posizione del testo'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) },
    { key: 'zona_categoria', label: t('Posizione della categoria'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) },
    { key: 'zona_durata', label: t('Posizione della durata'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) && !!s.scheda_durata },
    { key: 'zona_numero', label: t('Posizione del numero'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) },
    { key: 'zona_nota', label: t('Posizione della nota'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) },
    { key: 'zona_pulsante', label: t('Posizione del pulsante'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) },
    { key: 'zona_icona', label: t('Posizione dell’icona'), type: 'select', options: ZONE_PICCOLE, show: s => conZone(s) },
    { key: 'scheda_etichetta_angolo', label: t('Posizione dell’etichetta'), type: 'select', options: [
      { value: 'as', label: t('In alto a sinistra') },
      { value: 'ad', label: t('In alto a destra') },
      { value: 'bs', label: t('In basso a sinistra') },
      { value: 'bd', label: t('In basso a destra') },
    ], show: s => Array.isArray(s.images) && s.images.some(i => i && i.etichetta) },

    // ─── Card di testo ───
    { type: 'separator', label: t('Card di testo'), show: s => haTesti(s) },
    { key: 'testo_sfondo', label: t('Sfondo delle card di testo'), type: 'color', show: s => haTesti(s) },

    // ─── Colore di ogni voce (specchio di Media: stesse voci, solo il colore) ───
    { type: 'separator', label: t('Colore e segno delle voci'), show: s => Array.isArray(s.images) && s.images.length > 0 && !s.social },
    { key: 'images', type: 'content-items', label: t('Voci'), itemLabel: t('Voce'), etichettaDa: 'caption', miniaturaDa: 'url',
      show: s => Array.isArray(s.images) && s.images.length > 0 && !s.social,
      description: t('Un colore per ogni foto o card: tinge la cornice, la categoria, il numero, il pulsante, il biglietto, il fondo delle card di testo e il segno a matita. Vuoto = il colore della galleria. Il segno a matita cerchia una foto, come sul provino di un fotografo.'),
      itemFields: [
        { key: 'colore', label: t('Colore'), type: 'color' },
        { key: 'segno', label: t('Segno a matita'), type: 'select', options: [
          { value: '', label: t('Nessuno') },
          { value: 'riquadro', label: t('Riquadro a pennarello') },
          { value: 'matita', label: t('Riquadro a matita') },
          { value: 'cerchio', label: t('Cerchio a matita') },
        ] },
        { key: 'icona', label: t('Icona della categoria'), type: 'icon' },
        { key: 'riflesso', label: t('Riflesso olografico'), type: 'toggle',
          description: t('L’etichetta della nota (e il filo interno, se c’è) prende i colori cangianti di una carta olografica, che scorrono piano.') },
      ] },

    // ─── Filtri ───
    { type: 'separator', label: t('Filtri'), show: s => filtrabile(s) && !!s.filtri },
    { key: 'filtri_stile', label: t('Stile dei filtri'), type: 'select', options: [
      { value: 'pillole', label: t('Pillole') },
      { value: 'contorno', label: t('Contorno') },
      { value: 'sottolineato', label: t('Sottolineato') },
      { value: 'bordate', label: t('Bordate') },
      { value: 'semplice', label: t('Semplici') },
      { value: 'caselle', label: t('Caselle da spuntare') },
      { value: 'menu', label: t('Menu a tendina') },
    ], show: s => filtrabile(s) && !!s.filtri,
      description: t('Con le caselle si possono spuntare più valori insieme.') },
    { key: 'filtri_forma', label: t('Forma'), type: 'select', options: [
      { value: '', label: t('Dello stile') },
      { value: 'pillola', label: t('Pillola') },
      { value: 'arrotondata', label: t('Arrotondata') },
      { value: 'squadrata', label: t('Squadrata') },
    ], show: s => filtrabile(s) && !!s.filtri && !['menu', 'sottolineato'].includes(s.filtri_stile) },
    { key: 'filtri_conteggi_stile', label: t('Conteggi'), type: 'select', options: [
      { value: '', label: t('In alto, piccoli') },
      { value: 'accanto', label: t('Accanto al nome') },
      { value: 'punto', label: t('Accanto, col punto') },
    ], show: s => filtrabile(s) && !!s.filtri && s.filtri_conteggi !== false && s.filtri_stile !== 'menu' },
    { key: 'filtri_riga_sopra', label: t('Riga spessa sopra'), type: 'toggle', show: s => filtrabile(s) && !!s.filtri && s.filtri_stile === 'sottolineato' },
    { key: 'filtri_punti', label: t('Pallino nel colore della categoria'), type: 'toggle', show: s => filtrabile(s) && !!s.filtri && s.filtri_stile !== 'menu',
      description: t('Il colore è quello della prima voce della categoria (Colore e segno delle voci).') },
    { key: 'filtri_linea', label: t('Gruppi in una riga'), type: 'toggle', show: s => filtrabile(s) && !!s.filtri && s.filtri_stile !== 'menu',
      description: t('Con più gruppi («Tipo: Guida, Livello: Base») tutti i gruppi stanno in una riga, col loro nome piccolo, e un solo «Tutti» davanti che li azzera.') },
    { type: 'typography', label: t('Testo dei filtri'), responsiveKeys: [],
      keys: { family: 'filtri_font_family', size: 'filtri_font_size', weight: 'filtri_font_weight', transform: 'filtri_text_transform', letterSpacing: 'filtri_letter_spacing' },
      sizeMin: 9, sizeMax: 22, show: s => filtrabile(s) && !!s.filtri && s.filtri_stile !== 'menu' },
    { key: 'filtri_posizione', label: t('Posizione dei filtri'), type: 'select', options: [
      { value: '', label: t('Sopra la galleria') },
      { value: 'sinistra', label: t('A sinistra, in colonna') },
    ], show: s => filtrabile(s) && !!s.filtri && !String(s.layout || '').startsWith('strip'),
      description: t('In colonna i gruppi di filtri, la ricerca e l’ordinamento stanno uno sotto l’altro a sinistra della galleria e restano fermi mentre scorri. Sotto i 960 px tornano sopra la galleria.') },
    { key: 'filtri_allinea', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ], show: s => filtrabile(s) && !!s.filtri && s.filtri_posizione !== 'sinistra' },
    { key: 'filtri_colore', label: t('Colore del filtro attivo'), type: 'color', show: s => filtrabile(s) && !!s.filtri },
    { key: 'filtri_colore_testo', label: t('Testo del filtro attivo'), type: 'color', show: s => filtrabile(s) && !!s.filtri,
      description: t('Vuoto = chiaro o scuro da sé, secondo il colore del filtro attivo.') },
    { key: 'filtri_colore_bordo', label: t('Bordo dei filtri'), type: 'color', show: s => filtrabile(s) && !!s.filtri && ['bordate', 'contorno', 'caselle'].includes(s.filtri_stile),
      description: t('Vuoto = una linea leggera nel colore del testo.') },
    { key: 'cerca_posizione', label: t('Posizione della ricerca'), type: 'select', options: [
      { value: '', label: t('Accanto ai filtri') },
      { value: 'sopra', label: t('Sopra, larga, con la lente') },
    ], show: s => filtrabile(s) && !!s.cerca },

    // ─── Ombra & bordi ───
    ...shadowField,
    ...borderFields(),
    { type: 'separator', label: t('Disposizione') },
    { key: 'layout', label: t('Schema'), type: 'select', optionsFn: s => {
      let family = s.layout_family || 'classic';
      if (family === 'classic') {
        const lay = s.layout || 'grid';
        if (lay.startsWith('strip') || lay === 'filmstrip') family = 'strip';
      }
      if (family === 'strip') return [
        { value: 'strip', label: t('Nastro') },
        { value: 'strip_collage', label: t('Nastro collage') },
        { value: 'strip_multi', label: t('Nastro multi-riga') },
        { value: 'strip_marquee', label: t('Nastro automatico') },
        { value: 'strip_split', label: t('Nastro doppio') },
        { value: 'strip_coverflow', label: t('Coverflow 3D') },
      ];
      return [
        { value: 'grid', label: t('Griglia') },
        { value: 'justified', label: t('Giustificato') },
        { value: 'masonry', label: t('Masonry') },
        { value: 'scattered', label: t('Sparso') },
        { value: 'collage', label: t('Collage') },
        { value: 'mosaic', label: t('Mosaico') },
        { value: 'honeycomb', label: t('Esagoni') },
        { value: 'hexgrid', label: t('Esagoni incastro') },
        { value: 'puzzle', label: t('Puzzle') },
        { value: 'diagonal', label: t('Diagonale') },
        { value: 'parallax', label: t('Parallasse') },
        { value: 'drift', label: t('Deriva (multi-dir)') },
        { value: 'cascade', label: t('Cascata (sovrapposti)') },
        { value: 'metro', label: t('Metro (dimensioni miste)') },
        { value: 'expand', label: t('Espandi (spotlight)') },
        { value: 'elenco', label: t('Elenco a righe') },
      ];
    }},
    { key: 'columns', label: t('Colonne'), type: 'range', min: 2, max: 6, step: 1,
      show: s => !(s.layout && s.layout.startsWith('strip')) && s.layout !== 'elenco' },
    { key: 'large_columns', label: t('Colonne schermi grandi'), type: 'range', min: 1, max: 8, step: 1,
      show: s => ['grid', 'diagonal', 'masonry'].includes(s.layout || 'grid'),
      description: t('Da 1400 px in su (monitor grandi); vuoto = come al computer.') },
    { key: 'tablet_columns', label: t('Colonne tablet'), type: 'range', min: 1, max: 6, step: 1,
      show: s => ['grid', 'diagonal', 'masonry'].includes(s.layout || 'grid'),
      description: t('Fra il telefono e il computer (da 640 a 1199 px); vuoto = come al computer.') },
    { key: 'mobile_columns', label: t('Colonne mobile'), type: 'range', min: 1, max: 4, step: 1,
      show: s => !(s.layout && s.layout.startsWith('strip')) && s.layout !== 'elenco' },
    { key: 'small_columns', label: t('Colonne telefoni piccoli'), type: 'range', min: 1, max: 4, step: 1,
      show: s => ['grid', 'diagonal', 'masonry'].includes(s.layout || 'grid'),
      description: t('Sotto i 480 px; vuoto = come le colonne mobile.') },
    { key: 'griglia_linee', label: t('Linee fra le celle'), type: 'toggle', show: s => ['grid', 'metro', 'mosaic'].includes(s.layout || 'grid'),
      description: t('Le celle si toccano e le divide una linea sottile, dentro un riquadro: le pareti di loghi.') },
    { key: 'griglia_linee_colore', label: t('Colore delle linee'), type: 'color', show: s => !!s.griglia_linee },
    { key: 'video_pausa_angolo', label: t('Posizione della pausa'), type: 'select', options: [
      { value: '', label: t('In alto a sinistra') },
      { value: 'ad', label: t('In alto a destra') },
      { value: 'bs', label: t('In basso a sinistra') },
      { value: 'bd', label: t('In basso a destra') },
    ], show: s => !!s.video_pausa && s.video_preview === 'autoplay' && !!s.images?.some(i => i.type === 'video'),
      description: t('L’angolo del video in cui sta il pulsante di pausa: spostalo dove non copre il numero, la categoria o i testi.') },
  ],
};
