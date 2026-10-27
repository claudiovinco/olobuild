import { t } from '@/i18n';

/**
 * Montaggio — una timeline di montaggio vera: monitor col video, barra con play e timecode, righello, traccia video
 * con le clip in fila (lunghe quanto durano) e traccia audio con la forma d'onda. La testina si trascina, un clic su
 * una clip salta al suo inizio, e accanto al monitor cambiano tipo, titolo, cliente e pulsante. Tasti come in un
 * programma di montaggio (frecce, J K L, Spazio). Nata per il tema di una casa di produzione video (27 ott 2026).
 * Renderer: includes/tiles/class-montaggio-tile.php.
 *   fields[]      → clip · sequenza (nome, fotogrammi, partenza) · cosa mostrare
 *   styleFields[] → Aspetto (colori) · Testo · Forma (proporzione, raggio, tracce) · Disposizione
 */
export default {
  type: 'montaggio',
  name: t('Montaggio'),
  icon: 'dashicons-video-alt2',
  category: 'media',

  defaults: {
    clip: [
      { titolo: t('Bianco'), cliente: 'Atelier Neve', anno: '2026', tipo: t('Fashion film'), video: '', immagine: '', durata: 8, link: '', testo_link: t('Guarda il film'), colore: '' },
      { titolo: t('Strada bianca'), cliente: 'Auto Ferri', anno: '2026', tipo: t('Spot'), video: '', immagine: '', durata: 6, link: '', testo_link: t('Guarda lo spot'), colore: '' },
      { titolo: t('Rosso'), cliente: 'I Fari', anno: '2025', tipo: t('Videoclip'), video: '', immagine: '', durata: 7, link: '', testo_link: t('Guarda il video'), colore: '' },
    ],
    nome_sequenza: 'SHOWREEL_2026_v12',
    fps: 24,
    parti_da_sola: true,
    mostra_audio: true,
    mostra_info: true,

    colore_fondo: '',
    colore_testo: '',
    colore_testina: '',
    colore_clip: '',
    colore_audio: '',
    titolo_font_family: '',
    titolo_font_size: 34,
    titolo_font_size_mobile: 24,
    titolo_font_weight: '',
    titolo_color: '',
    dati_font_family: '',
    raggio: { tl: 10, tr: 10, br: 10, bl: 10 },
    proporzione: '16/9',
    altezza_traccia: 56,
    posizione_info: 'lato',
  },

  fields: [
    { type: 'separator', label: t('Clip') },
    { key: 'clip', label: t('Clip'), type: 'content-items', itemLabel: t('Clip'), etichettaDa: 'titolo', miniaturaDa: 'immagine',
      description: t('Una per clip, nell\'ordine della timeline: si mettono in fila e ognuna è lunga quanto dura. Il video va nel monitor quando la testina ci passa; la foto fa da copertina e da striscia di fotogrammi sulla traccia.'),
      newItemDefaults: { titolo: '', cliente: '', anno: '', tipo: '', video: '', immagine: '', durata: 8, link: '', testo_link: t('Guarda il progetto'), colore: '' },
      itemFields: [
        { key: 'titolo', label: t('Titolo'), type: 'text' },
        { key: 'tipo', label: t('Tipo'), type: 'text', placeholder: t('Spot, Videoclip, Documentario…') },
        { key: 'cliente', label: t('Cliente'), type: 'text' },
        { key: 'anno', label: t('Anno'), type: 'text' },
        { key: 'video', label: t('Video'), type: 'media', accept: 'video',
          description: t('Senza audio e corto: si vede solo il tratto lungo quanto la durata qui sotto.') },
        { key: 'immagine', label: t('Copertina'), type: 'image' },
        { key: 'durata', label: t('Durata'), type: 'range', min: 1, max: 60, step: 0.5, unit: 's',
          description: t('Quanto resta in onda la clip, e quanto è lunga sulla traccia.') },
        { key: 'link', label: t('Collegamento'), type: 'link' },
        { key: 'testo_link', label: t('Testo del collegamento'), type: 'text' },
      ] },

    { type: 'separator', label: t('Sequenza') },
    { key: 'nome_sequenza', label: t('Nome della sequenza'), type: 'text',
      description: t('Il nome che compare nella barra, come in un programma di montaggio. Vuoto: niente nome.') },
    { key: 'fps', label: t('Fotogrammi al secondo'), type: 'select', options: [
      { value: 24, label: '24' }, { value: 25, label: '25' }, { value: 30, label: '30' }, { value: 50, label: '50' }, { value: 60, label: '60' },
    ], description: t('Solo per il timecode (ore:minuti:secondi:fotogrammi) e per il passo delle frecce.') },
    { key: 'parti_da_sola', label: t('Parte da sola'), type: 'toggle',
      description: t('Senza audio, quando la timeline entra nello schermo; si ferma quando esce. Mai nel builder, mai con la riduzione del movimento.') },
    { key: 'mostra_audio', label: t('Mostra la traccia audio'), type: 'toggle' },
    { key: 'mostra_info', label: t('Mostra il riquadro della clip'), type: 'toggle',
      description: t('Accanto o sotto il monitor: tipo, titolo, cliente, anno e collegamento della clip in onda.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'colore_fondo', label: t('Sfondo'), type: 'color', description: t('Vuoto: lo scuro del tema.') },
    { key: 'colore_testo', label: t('Colore del testo'), type: 'color', description: t('Vuoto: quello che si legge sullo sfondo.') },
    { key: 'colore_testina', label: t('Colore accento'), type: 'color',
      description: t('La testina, il timecode, il tipo della clip e la clip in onda. Vuoto: il primario del tema.') },
    { key: 'colore_clip', label: t('Colore delle clip'), type: 'color', description: t('Sotto la striscia di fotogrammi. Vuoto: il secondario del tema.') },
    { key: 'colore_audio', label: t('Colore della forma d\'onda'), type: 'color' },
    { key: 'clip', label: t('Clip'), type: 'content-items', etichettaDa: 'titolo', miniaturaDa: 'immagine',
      itemFields: [
        { key: 'colore', label: t('Colore del bordo'), type: 'color', description: t('Il filo a sinistra della clip sulla traccia. Vuoto: il colore accento.') },
      ] },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Titolo della clip'), responsiveKeys: ['size'],
      keys: { family: 'titolo_font_family', size: 'titolo_font_size', weight: 'titolo_font_weight', color: 'titolo_color' },
      sizeMin: 16, sizeMax: 72 },
    { key: 'dati_font_family', label: t('Font del timecode'), type: 'font-family', description: t('Vuoto: il monospaziato del tema.') },

    { type: 'separator', label: t('Forma') },
    { key: 'proporzione', label: t('Proporzione del monitor'), type: 'select', options: [
      { value: '16/9', label: '16:9' }, { value: '21/9', label: '21:9' }, { value: '4/3', label: '4:3' }, { value: '1/1', label: '1:1' },
    ] },
    { key: 'raggio', label: t('Raggio'), type: 'border-radius' },
    { key: 'altezza_traccia', label: t('Altezza delle tracce'), type: 'range', min: 36, max: 120, step: 2, unit: 'px' },

    { type: 'separator', label: t('Disposizione') },
    { key: 'posizione_info', label: t('Riquadro della clip'), type: 'select', options: [
      { value: 'lato', label: t('Accanto al monitor') },
      { value: 'sotto', label: t('Sotto il monitor') },
    ], description: t('Quando la colonna è stretta va comunque sotto.') },
  ],
};
