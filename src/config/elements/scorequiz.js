import { t } from '@/i18n';

/**
 * Quiz a punteggio — una domanda alla volta, ogni risposta vale dei punti, alla fine
 * l'esito della fascia raggiunta con il suo testo e il suo link.
 * Renderer: includes/tiles/class-scorequiz-tile.php. Nato nell'ondata I4 (1.4.508)
 * al posto di Availability, il cui verdetto non portava da nessuna parte.
 *   fields[]      → testi, domande (risposte «Testo|punti»), esiti (da N punti, link)
 *   styleFields[] → colore, testo sul colore, sfondo e bordo della scheda, allineamento
 */
export default {
  type: 'scorequiz',
  name: t('Quiz a punteggio'),
  icon: 'dashicons-forms',
  category: 'interactive',

  defaults: {
    eyebrow: t('Trova la soluzione giusta'),
    heading: t('Quale pacchetto fa per te?'),
    intro: t('Tre domande, meno di un minuto.'),
    questions: [
      { text: t('Quanto tempo puoi dedicarci?'), options: t('Poco') + '|1\n' + t('Abbastanza') + '|2\n' + t('Tutto quello che serve') + '|3' },
      { text: t('Hai già esperienza?'), options: t('No, parto da zero') + '|1\n' + t("Un po'") + '|2\n' + t('Sì, molta') + '|3' },
      { text: t('Cosa ti interessa di più?'), options: t('Un risultato veloce') + '|1\n' + t('Un percorso guidato') + '|2\n' + t('Approfondire tutto') + '|3' },
    ],
    results: [
      { min: 0, title: 'Base', text: t('Il pacchetto Base è il punto di partenza giusto: essenziale e veloce.'), link_text: '', link_url: '' },
      { min: 5, title: 'Plus', text: t('Il pacchetto Plus ti accompagna passo per passo.'), link_text: '', link_url: '' },
      { min: 8, title: t('Completo'), text: t('Il pacchetto Completo ti dà tutto, senza limiti.'), link_text: '', link_url: '' },
    ],
    result_label: t('Il tuo risultato'),
    back_label: t('Indietro'),
    restart_label: t('Ricomincia'),
    show_score: false,
    zone_accent: '',
    zone_on: 'var(--olo-color-primary-contrast, #ffffff)',
    card_bg: '',
    card_border: '',
    align: 'left',
  },

  // Come nasce dalla palette: un quiz vero per scegliere fra tre pacchetti di assistenza, con
  // domande concrete, risposte che valgono 1-3 punti e tre esiti con il loro prezzo al mese.
  partenza: {
    intro: t('Tre domande, meno di un minuto: ti consigliamo da dove partire.'),
    questions: [
      { text: t('Quanto spesso ti serve il nostro aiuto?'), options: t('Una volta ogni tanto') + '|1\n' + t('Qualche volta al mese') + '|2\n' + t('Ogni settimana') + '|3' },
      { text: t('Quante persone lavorano con te?'), options: t('Lavoro da solo') + '|1\n' + t('Da 2 a 10') + '|2\n' + t('Più di 10') + '|3' },
      { text: t('Cosa conta di più per te?'), options: t('Spendere il giusto') + '|1\n' + t('Un referente fisso') + '|2\n' + t('Risposte in giornata') + '|3' },
    ],
    results: [
      { min: 0, title: 'Base', text: t('Due interventi al mese e assistenza via email entro 48 ore. Da 29 € al mese.'), link_text: '', link_url: '' },
      { min: 5, title: 'Plus', text: t('Un referente dedicato, interventi senza limiti e risposta in giornata. Da 59 € al mese.'), link_text: '', link_url: '' },
      { min: 8, title: t('Completo'), text: t('Tutto il Plus, con una revisione ogni mese e assistenza anche il sabato. Da 99 € al mese.'), link_text: '', link_url: '' },
    ],
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },

    { type: 'separator', label: t('Domande') },
    { key: 'questions', label: t('Domande'), type: 'content-items', itemLabel: t('Domanda'), etichettaDa: 'text',
      defaults: { text: t('Nuova domanda'), options: t('Risposta A') + '|1\n' + t('Risposta B') + '|2' },
      itemFields: [
        { key: 'text', label: t('Domanda'), type: 'text' },
        { key: 'options', label: t('Risposte (una per riga)'), type: 'textarea',
          description: t('Scrivi «Risposta|punti», per esempio «Spesso|3». Senza punti la risposta vale 0.') },
      ],
    },

    { type: 'separator', label: t('Esiti') },
    { key: 'results', label: t('Esiti'), type: 'content-items', itemLabel: t('Esito'), etichettaDa: 'title',
      defaults: { min: 0, title: t('Nuovo esito'), text: '', link_text: '', link_url: '' },
      itemFields: [
        { key: 'min', label: t('Da punti'), type: 'number', min: 0, step: 0.5,
          description: t("L'esito vale da questo punteggio in su; vince il più alto raggiunto.") },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'text', label: t('Testo'), type: 'textarea' },
        { key: 'link_text', label: t('Testo del pulsante'), type: 'text' },
        { key: 'link_url', label: t('Collegamento'), type: 'link' },
      ],
    },
    { key: 'show_score', label: t('Mostra il punteggio'), type: 'toggle' },

    { type: 'separator', label: t('Etichette') },
    { key: 'result_label', label: t('Sopra l\'esito'), type: 'text' },
    { key: 'back_label', label: t('Pulsante indietro'), type: 'text' },
    { key: 'restart_label', label: t('Pulsante ricomincia'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'zone_accent', label: t('Colore'), type: 'color',
      description: t('Barra di avanzamento, occhiello, titolo dell\'esito e pulsante. Vuoto: il primario del tema.') },
    { key: 'zone_on', label: t('Testo sul colore'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo scheda'), type: 'color' },
    { key: 'card_border', label: t('Bordo scheda'), type: 'border', legacyWidth: 1 },

    { type: 'separator', label: t('Disposizione') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
