import { t } from '@/i18n';

/**
 * Preventivo (tipo `builder`) — righe con stepper +/− e totale live; la scelta parte
 * per email, WhatsApp, verso una pagina o nel carrello WooCommerce (`send_mode`).
 * Estratto dai demo OLOthemes (setupBuilder in fx.js). Token-first: `zone_accent`.
 * Renderer: class-builder-tile.php. Opzionale `cap` = max quantità totale.
 * Le tile salvate prima della 1.4.508 non hanno `send_mode`: il renderer tiene il
 * collegamento di sempre a `cta_url` (opzione «Solo collegamento»).
 */
export default {
  type: 'builder',
  name: t('Preventivo'),
  icon: 'dashicons-cart',
  category: 'interactive',

  defaults: {
    eyebrow: t('Preventivo'),
    heading: t('Componi la tua richiesta'),
    intro: '',
    currency: '€',
    cap: 0,
    items: [
      { name: t('Servizio base'), price: '120', note: '', start: 1 },
      { name: t('Ora aggiuntiva'), price: '45', note: '', start: 0 },
      { name: t('Trasferta'), price: '30', note: '', start: 0 },
    ],
    total_label: t('Totale'),
    count_label: t('voci'),
    count_label_one: '',
    cta_text: t('Richiedi il preventivo'),
    cta_url: '',
    send_mode: 'mail',
    send_to: '',
    send_subject: t('Richiesta di preventivo'),
    send_intro: t('Vorrei un preventivo per:'),
    zone_accent: '',
    zone_on: 'var(--olo-color-surface, #ffffff)',
    card_bg: '',
    card_border: '',
    align: 'left',
    layout: 'panel',
    heading_accent: '',
    heading_color: '',
    tally_bg: '',
    item_name_color: '',
    item_price_color: '',
  },

  // Come nasce dalla palette: il preventivo di un servizio professionale con quattro voci con la
  // nota, due già scelte (il totale parte da 150 €), i più e meno e il pulsante che apre un'email
  // già scritta (compare sul sito quando si scrive l'indirizzo che la riceve).
  partenza: {
    intro: t('Scegli le voci che ti servono: il totale si aggiorna da solo e la richiesta ci arriva già compilata.'),
    items: [
      { name: t('Sopralluogo e consulenza'), price: '60', note: t('Un\'ora, nella tua sede o online'), start: 1 },
      { name: t('Ore di lavoro'), price: '45', note: t('Il prezzo è per ora'), start: 2 },
      { name: t('Materiali'), price: '25', note: t('Forfait per ogni intervento'), start: 0 },
      { name: t('Trasferta fuori città'), price: '30', note: t('Oltre i 20 km dalla sede'), start: 0 },
    ],
    currency: '€ ',
    zone_on: 'var(--olo-color-primary-contrast, #ffffff)',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'heading_accent', label: t('Parola accento titolo'), type: 'text',
      condition: { field: 'layout', value: 'split' } },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },

    { type: 'separator', label: t('Articoli') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Voce'),
      defaults: { name: 'Nuova voce', price: '10', note: '', start: 0 },
      itemFields: [
        { key: 'name', label: t('Nome'), type: 'text' },
        { key: 'price', label: t('Prezzo'), type: 'number', min: 0, step: 0.01 },
        { key: 'note', label: t('Nota (opzionale)'), type: 'text' },
        { key: 'start', label: t('Quantità iniziale'), type: 'number' },
        { key: 'product_id', label: t('Prodotto WooCommerce (ID)'), type: 'number', min: 0,
          description: t("Serve solo con l'invio al carrello: la quantità scelta di questa voce va nel carrello come questo prodotto.") },
      ],
    },

    { type: 'separator', label: t('Calcolo') },
    { key: 'currency', label: t('Valuta'), type: 'text' },
    { key: 'cap', label: t('Limite quantità totale (0 = nessuno)'), type: 'number' },
    { key: 'total_label', label: t('Etichetta totale'), type: 'text' },
    { key: 'count_label', label: t('Etichetta conteggio'), type: 'text' },
    // Prima con una sola voce scelta usciva «1 voci».
    { key: 'count_label_one', label: t('Etichetta conteggio, singolare'), type: 'text', placeholder: t('voce'),
      description: t('Quando la quantità scelta è 1 («1 voce»). Vuota: per le etichette comuni (voci, scelte, ospiti, notti…) il singolare si ricava da solo, per le altre resta quella al plurale.') },

    { type: 'separator', label: t('Invio della scelta') },
    { key: 'send_mode', label: t('Dove va la scelta'), type: 'select', options: [
      { value: 'mail', label: t('Email') },
      { value: 'whatsapp', label: t('WhatsApp') },
      { value: 'page', label: t('Una pagina (es. il modulo contatti)') },
      { value: 'woocommerce', label: t('Carrello WooCommerce') },
      { value: '', label: t('Solo collegamento (non invia la scelta)') },
    ],
      description: t("Email e WhatsApp aprono un messaggio già scritto con l'elenco e il totale. La pagina riceve ?scelta=…&totale=… nell'indirizzo. Il carrello aggiunge le voci che hanno un prodotto WooCommerce.") },
    { key: 'send_to', label: t('Destinazione'), type: 'text', placeholder: t('email, numero o indirizzo della pagina'),
      condition: { field: 'send_mode', op: 'in', value: ['mail', 'whatsapp', 'page'] },
      description: t("Email: l'indirizzo che riceve. WhatsApp: il numero con il prefisso internazionale (39…). Pagina: il suo indirizzo. Finché manca, il pulsante non compare sul sito.") },
    { key: 'send_subject', label: t("Oggetto dell'email"), type: 'text',
      condition: { field: 'send_mode', value: 'mail' } },
    { key: 'send_intro', label: t('Frase iniziale del messaggio'), type: 'text',
      condition: { field: 'send_mode', op: 'in', value: ['mail', 'whatsapp'] } },
    { key: 'cta_text', label: t('Testo del pulsante'), type: 'text' },
    { key: 'cta_url', label: t('Collegamento del pulsante'), type: 'link',
      condition: { field: 'send_mode', op: 'empty' } },
  ],

  styleFields: [
    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore zona (accento)'), type: 'color' },
    { key: 'zone_on', label: t('Testo su accento'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo pannello'), type: 'color' },
    { key: 'card_border', label: t('Bordo pannello'), type: 'border', legacyWidth: 1 },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'panel', label: t('Pannello (lista + footer)') },
      { value: 'split', label: t('Split (header + griglia card)') },
    ]},
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ], condition: { field: 'layout', op: 'neq', value: 'split' } },

    { type: 'separator', label: t('Colori (split)'), condition: { field: 'layout', value: 'split' } },
    { key: 'heading_color', label: t('Colore titolo (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
    { key: 'tally_bg', label: t('Sfondo pill totale (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
    { key: 'item_name_color', label: t('Colore nome voce (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
    { key: 'item_price_color', label: t('Colore prezzo (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
  ],
};
