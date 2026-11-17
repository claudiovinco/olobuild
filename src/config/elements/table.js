import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Tabella — righe di celle di testo (table_data), la prima può essere l'intestazione.
 *   fields[]      → Dati (finestra a griglia: celle, righe, colonne, allineamento) · Struttura (intestazione, prima
 *                   colonna come titolo di riga)
 *   styleFields[] → Aspetto (stile, sfondi, righe alternate, linee, passaggio, ombra) · Testo (intestazione, celle) ·
 *                   Forma (righe compatte) · Disposizione (sul telefono) · contenitore
 * Renderer: includes/tiles/class-table-tile.php.
 */
export default {
  type: 'table',
  name: t('Tabella'),
  icon: 'dashicons-editor-table',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    table_data: [
      ['Funzionalità', 'Base', 'Pro'],
      ['Spazio', '5 GB', '50 GB'],
      ['Utenti', '1', '10'],
      ['Supporto', 'Email', 'Prioritario'],
    ],
    has_header: true,
    striped: true,
    bordered: true,
    hover_effect: true,
    compact: false,
    first_col_bold: false,
    tb_head_font_family: '', tb_head_font_size: '', tb_head_font_weight: '', tb_head_text_transform: '', tb_head_letter_spacing: '',
    tb_cell_font_family: '', tb_cell_font_size: '', tb_cell_font_weight: '', tb_cell_line_height: '',
    col_alignments: [],
    responsive_mode: 'scroll',
    header_bg: '',
    header_text_color: '',
    text_color: '',
    border_color: '',
    even_row_bg: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: il listino di un'attività — intestazione nel colore secondario con il testo
  // che ci contrasta, prima colonna in grassetto, righe alternate e prezzi in €.
  partenza: {
    table_data: [
      [t('Servizio'), t('Durata'), t('Prezzo')],
      [t('Prima consulenza'), t('30 minuti'), t('Gratuita')],
      [t('Incontro singolo'), t('60 minuti'), '70 €'],
      [t('Pacchetto da 5 incontri'), t('5 × 60 minuti'), '320 €'],
      [t('Pacchetto da 10 incontri'), t('10 × 60 minuti'), '600 €'],
    ],
    first_col_bold: true,
    header_bg: 'var(--olo-color-secondary)',
    header_text_color: 'var(--olo-color-secondary-contrast)',
  },

  fields: [
    // I dati si scrivono in una finestra a griglia (FieldTabella): prima si modificavano solo nel canvas Vue, che non si
    // monta più dal 25 set 2026, e una nota in linea mandava a cliccare celle che non rispondevano (16 nov 2026).
    { key: 'table_data', label: t('Dati'), type: 'tabella' },

    { type: 'separator', label: t('Struttura') },
    { key: 'has_header', label: t('Riga intestazione'), type: 'toggle',
      description: t('La prima riga dei Dati fa da intestazione: ha il suo sfondo e il suo carattere, e sul telefono dà il nome alle celle.') },
  ],

  // Zone dello standard (16 nov 2026, episodio 27): Aspetto · Testo · Forma · Disposizione. Prima: «Preset stilistico»
  // coi dodici stili inglesi a colori fissi (che cambiavano anche la Riga intestazione, un contenuto), «Header» e
  // «Celle» col solo colore, righe alternate, bordi, compattezza e comportamento sul telefono nel Contenuto.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'chiara',     label: t('Chiara') },
      { value: 'scura',      label: t('Scura') },
      { value: 'primario',   label: t('Primario') },
      { value: 'righe',      label: t('A righe') },
      { value: 'essenziale', label: t('Essenziale') },
      { value: 'compatta',   label: t('Compatta') },
      { value: 'custom',     label: t('Personalizzato') },
    ], description: t('Cambia insieme intestazione, righe, linee e caratteri, coi colori del sito. Poi ogni controllo qui sotto si regola da solo.') },
    { key: 'header_bg', label: t('Sfondo intestazione'), type: 'color',
      condition: { field: 'has_header', op: 'neq', value: false } },
    { key: 'striped', label: t('Righe alternate'), type: 'toggle' },
    { key: 'even_row_bg', label: t('Sfondo righe alternate'), type: 'color',
      condition: { field: 'striped', op: 'neq', value: false } },
    { key: 'bordered', label: t('Linee fra le righe'), type: 'toggle' },
    { key: 'border_color', label: t('Colore delle linee'), type: 'color',
      description: t('Le linee fra le righe e, sul telefono, il bordo delle schede.') },
    { key: 'hover_effect', label: t('Evidenzia la riga al passaggio'), type: 'toggle' },
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Intestazione'), responsiveKeys: [],
      keys: { family: 'tb_head_font_family', size: 'tb_head_font_size', weight: 'tb_head_font_weight',
              transform: 'tb_head_text_transform', letterSpacing: 'tb_head_letter_spacing', color: 'header_text_color' },
      sizeMin: 10, sizeMax: 40, sizeStep: 1,
      condition: { field: 'has_header', op: 'neq', value: false } },
    { type: 'typography', label: t('Celle'), responsiveKeys: [],
      keys: { family: 'tb_cell_font_family', size: 'tb_cell_font_size', weight: 'tb_cell_font_weight',
              lineHeight: 'tb_cell_line_height', color: 'text_color' },
      sizeMin: 10, sizeMax: 40, sizeStep: 1 },
    { key: 'first_col_bold', label: t('Prima colonna come titolo di riga'), type: 'toggle',
      description: t('La prima cella di ogni riga diventa il titolo della riga, in grassetto; nelle schede del telefono è il titolo della scheda.') },

    { type: 'separator', label: t('Forma') },
    { key: 'compact', label: t('Righe compatte'), type: 'toggle',
      description: t('Meno spazio attorno al testo delle celle e un carattere un po’ più piccolo: per tabelle lunghe.') },

    { type: 'separator', label: t('Disposizione') },
    { key: 'responsive_mode', label: t('Sul telefono'), type: 'select', options: [
      { value: 'scroll', label: t('Scorre di lato') },
      { value: 'stack', label: t('Una scheda per riga') },
    ], description: t('Scorre di lato: la tabella resta com’è e si scorre col dito. Una scheda per riga: ogni riga diventa una scheda, coi nomi dell’intestazione accanto ai valori.') },

    ...borderFields(),
  ],
};
