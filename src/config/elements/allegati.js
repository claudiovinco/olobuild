import { t } from '@/i18n';

/**
 * Allegati — i file da scaricare di una notizia, di un comunicato, di una pratica: icona del tipo di file, nome,
 * formato e peso. Le voci vengono dal campo «Allegati» del contenuto che la pagina mostra (Impostazioni › Tipi di
 * contenuto) oppure si scrivono qui. Senza allegati sul sito non compare. Renderer: includes/tiles/class-allegati-tile.php.
 *   fields[]      → titolo · origine (campo del contenuto o elenco) · voci · informazioni
 *   styleFields[] → Aspetto · Testo · Forma · Disposizione
 */
const dallaTile = { field: 'campo', op: 'empty' };

export default {
  type: 'allegati',
  name: t('Allegati'),
  icon: 'dashicons-paperclip',
  category: 'dynamic',

  defaults: {
    titolo: t('Allegati'),
    campo: '',
    voci: [
      { id: 'al-1', nome: t('Comunicato stampa'), file: '', formato: 'pdf', peso: '240 KB' },
      { id: 'al-2', nome: t('Foto in alta risoluzione'), file: '', formato: 'zip', peso: '18 MB' },
      { id: 'al-3', nome: t('Dati e tabelle'), file: '', formato: 'xls', peso: '96 KB' },
    ],
    mostra_info: true,
    accento: '',
    colore_linee: '',
    sfondo_voce: '',
    bordo_voce: [],
    titolo_tag: 'h3',
    titolo_font_family: '',
    titolo_font_size: 13,
    titolo_font_weight: '600',
    titolo_uppercase: true,
    titolo_letter_spacing: 1.5,
    titolo_color: '',
    voce_font_family: '',
    voce_font_size: 16,
    voce_font_weight: '500',
    voce_color: '',
    info_font_size: 13,
    info_color: '',
    raggio: '',
    padding_voce: [],
    colonne: 1,
    gap: 0,
  },

  fields: [
    { type: 'separator', label: t('Allegati') },
    { key: 'titolo', label: t('Titolo'), type: 'text' },
    { key: 'campo', label: t('Dal contenuto'), type: 'select', optionsSource: 'campiAllegati',
      description: t('Nel template di una notizia (o di un altro tipo con un campo Allegati) la tile mostra i file di quel contenuto, e senza file non compare. Con «Elenco qui sotto» si scrivono le voci a mano. Nel builder, fuori da un contenuto, si vedono le voci d\'esempio.') },
    { key: 'voci', label: t('File'), type: 'content-items', itemLabel: t('File'), etichettaDa: 'nome', condition: dallaTile,
      newItemDefaults: { nome: '', file: '', formato: 'pdf', peso: '' },
      itemFields: [
        { key: 'nome', label: t('Nome'), type: 'text' },
        { key: 'file', label: t('File'), type: 'media', accept: 'all' },
        { key: 'formato', label: t('Formato'), type: 'select', options: [
          { value: 'pdf', label: 'PDF' },
          { value: 'doc', label: t('Documento') },
          { value: 'xls', label: t('Foglio di calcolo') },
          { value: 'ppt', label: t('Presentazione') },
          { value: 'zip', label: 'ZIP' },
          { value: 'immagine', label: t('Immagine') },
          { value: 'audio', label: t('Audio') },
          { value: 'video', label: t('Video') },
          { value: 'altro', label: t('Altro') },
        ] },
        { key: 'peso', label: t('Peso'), type: 'text', placeholder: '1,2 MB' },
      ] },
    { key: 'mostra_info', label: t('Mostra formato e peso'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'accento', label: t('Colore accento'), type: 'color',
      description: t('Le icone e il nome al passaggio del mouse. Vuoto: il primario del tema.') },
    { key: 'colore_linee', label: t('Colore delle linee'), type: 'color' },
    { key: 'sfondo_voce', label: t('Sfondo voce'), type: 'color' },
    { key: 'bordo_voce', label: t('Bordo voce'), type: 'border' },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [],
      keys: { tag: 'titolo_tag', family: 'titolo_font_family', size: 'titolo_font_size', weight: 'titolo_font_weight', uppercase: 'titolo_uppercase', letterSpacing: 'titolo_letter_spacing', color: 'titolo_color' },
      sizeMin: 10, sizeMax: 40 },
    { type: 'typography', label: t('Nome del file'), responsiveKeys: [],
      keys: { family: 'voce_font_family', size: 'voce_font_size', weight: 'voce_font_weight', color: 'voce_color' }, sizeMin: 11, sizeMax: 28 },
    { type: 'typography', label: t('Formato e peso'), responsiveKeys: [], condition: { field: 'mostra_info', op: 'eq', value: true },
      keys: { size: 'info_font_size', color: 'info_color' }, sizeMin: 9, sizeMax: 20 },

    { type: 'separator', label: t('Forma') },
    { key: 'raggio', label: t('Raggio voce'), type: 'border-radius',
      description: t('Si vede quando la voce ha uno sfondo o un bordo.') },
    { key: 'padding_voce', label: t('Padding voce'), type: 'spacing', max: 60 },

    { type: 'separator', label: t('Disposizione') },
    { key: 'colonne', label: t('Colonne'), type: 'range', min: 1, max: 3, step: 1 },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 80, step: 2, unit: 'px' },
  ],
};
