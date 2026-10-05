import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Ricerca con filtri (tipo `tripfinder`) — barra di ricerca: N campi + pulsante.
 * Nata dai blueprint OLOthemes (TripFinder/BookingBar: fjordline, wander, pasaje)
 * come solo disegno; dalla 1.4.505 è un form GET vero: ogni campo dice cosa filtra
 * (testo, categoria, tag, tassonomia, tipo di contenuto o un parametro), le opzioni
 * si scrivono a mano o vengono dal sito. Renderer: class-tripfinder-tile.php.
 */
const FILTRI_CON_TERMINI = ['category', 'tag', 'taxonomy', 'post_type'];

export default {
  type: 'tripfinder',
  name: t('Ricerca con filtri'),
  icon: 'dashicons-search',
  category: 'interactive',

  defaults: {
    fields: [
      { label: 'Cosa cerchi', filter: 'text', value: 'Scrivi una parola', options: '' },
      { label: 'Categoria', filter: 'category', auto_options: true, value: 'Tutte le categorie', options: '' },
      { label: 'Tipo', filter: 'post_type', value: 'Tutto', options: 'Tutto\nArticoli|post\nPagine|page' },
    ],
    button_text: 'Cerca',
    button_url: '',
    accent: '',
    accent_on: 'var(--olo-color-surface, #ffffff)',
    bar_bg: '',
    field_bg: '',
    field_border: '',
    label_color: '',
    value_color: '',
    radius: 14,

    // SPAZIATURA additiva — default = padding storico della barra (8px su 4 lati)
    // e dei campi (10px verticale / 16px orizzontale). Render invariato coi default.
    bar_padding: { top: 8, right: 8, bottom: 8, left: 8 },
    field_padding: { top: 10, right: 16, bottom: 10, left: 16 },

    // FORMA additiva — raggio per-angolo della barra/bottone. Default tutto 0 →
    // fallback al raggio uniforme storico `radius` (no-op).
    radius_corners: { tl: 0, tr: 0, br: 0, bl: 0 },

    // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
    // Default no-op: bg none / shadow none / border 0 → render invariato.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: la ricerca vera nel sito dei default (testo libero, categorie del
  // sito, tipo di contenuto) in una barra a pillola con ombra, etichette nel primario e il
  // pulsante pieno con il testo in contrasto.
  partenza: {
    accent_on: 'var(--olo-color-primary-contrast, #ffffff)',
    label_color: 'var(--olo-color-primary)',
    radius_corners: { tl: 999, tr: 999, br: 999, bl: 999 },
    field_padding: { top: 10, right: 22, bottom: 10, left: 22 },
    shadow: 'lg',
  },

  fields: [
    { type: 'separator', label: t('Campi') },
    { key: 'fields', label: t('Campi della barra'), type: 'content-items',
      itemLabel: t('Campo'),
      defaults: { label: 'Categoria', filter: 'category', auto_options: true, value: 'Tutte', options: '' },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'filter', label: t('Cosa filtra'), type: 'select',
          options: [
            { value: 'text', label: t('Testo libero (cerca nel sito)') },
            { value: 'category', label: t('Categoria') },
            { value: 'tag', label: t('Tag') },
            { value: 'taxonomy', label: t('Altra tassonomia') },
            { value: 'post_type', label: t('Tipo di contenuto') },
            { value: '', label: t("Parametro nell'indirizzo") },
          ],
          description: t("Testo libero, categoria, tag, tassonomia e tipo di contenuto filtrano i risultati della ricerca del sito. «Parametro nell'indirizzo» passa la scelta alla pagina indicata in «Dove porta la ricerca», che la legge con i contenuti dinamici o con le condizioni di visibilità.") },
        { key: 'taxonomy', label: t('Tassonomia'), type: 'text', placeholder: 'product_cat',
          condition: { key: 'filter', op: 'eq', value: 'taxonomy' },
          description: t('Il nome interno della tassonomia, per esempio product_cat per le categorie dei prodotti WooCommerce.') },
        { key: 'param', label: t('Nome del parametro'), type: 'text', placeholder: t('es. sede'),
          condition: { key: 'filter', op: 'empty' },
          description: t("Il nome con cui la scelta arriva nell'indirizzo (?sede=milano). Vuoto: l'etichetta in minuscolo.") },
        { key: 'value', label: t('Voce iniziale'), type: 'text',
          description: t("La voce che si vede all'inizio e che non filtra niente («Tutte le categorie»). Nel testo libero è il testo d'esempio.") },
        { key: 'auto_options', label: t('Opzioni dal sito'), type: 'toggle',
          show: (v) => FILTRI_CON_TERMINI.includes(v.filter),
          description: t('Le opzioni sono le categorie, i tag, i termini o i tipi di contenuto del sito, sempre aggiornati.') },
        { key: 'options', label: t('Opzioni (una per riga)'), type: 'textarea',
          show: (v) => v.filter !== 'text' && !(v.auto_options && FILTRI_CON_TERMINI.includes(v.filter)),
          description: t("«Etichetta|valore» invia un valore diverso da quello che si legge (Pagine|page). Senza valore, per categorie e tag si invia il nome scritto come nell'indirizzo (Scienze della terra → scienze-della-terra).") },
      ],
    },
    { type: 'separator', label: t('Bottone') },
    { key: 'button_text', label: t('Testo bottone'), type: 'text' },
    { key: 'button_url', label: t('Dove porta la ricerca'), type: 'link', placeholder: t('Risultati della ricerca del sito'),
      description: t("Vuoto: i risultati della ricerca del sito. Una pagina o un indirizzo: riceve le scelte nell'indirizzo, per esempio un motore di prenotazione.") },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (bottone)'), type: 'color',
      description: t('Sfondo del bottone; vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento'), type: 'color' },
    { key: 'bar_bg', label: t('Sfondo barra'), type: 'color' },
    { key: 'field_bg', label: t('Sfondo campo'), type: 'color' },
    { key: 'field_border', label: t('Bordo / divisori'), type: 'border', legacyWidth: 1 },
    { key: 'label_color', label: t('Colore etichette'), type: 'color' },
    { key: 'value_color', label: t('Colore valori'), type: 'color' },

    { type: 'separator', label: t('Forma') },
    { key: 'radius_corners', label: t('Raggio'), type: 'border-radius',
      legacyKeys: { all: 'radius' },
      description: t('Tutti gli angoli a 0: il raggio di base (14 px).') },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'bar_padding', label: t('Padding barra'), type: 'spacing', min: 0, max: 64 },
    { key: 'field_padding', label: t('Padding campi'), type: 'spacing', min: 0, max: 64 },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
