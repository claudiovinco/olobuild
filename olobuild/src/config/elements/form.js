export default {
  type: 'form',
  name: 'Form Contatti',
  icon: 'dashicons-email-alt',
  category: 'content',
  defaults: {
    fields: [
      { id: 'f-1', field_type: 'text', label: 'Nome', placeholder: 'Il tuo nome', name: 'nome', required: true, width: '1-2', options: '', icon: 'user' },
      { id: 'f-2', field_type: 'email', label: 'Email', placeholder: 'La tua email', name: 'email', required: true, width: '1-2', options: '', icon: 'mail' },
      { id: 'f-3', field_type: 'text', label: 'Oggetto', placeholder: 'Oggetto del messaggio', name: 'oggetto', required: false, width: '1-1', options: '', icon: '' },
      { id: 'f-4', field_type: 'textarea', label: 'Messaggio', placeholder: 'Scrivi il tuo messaggio...', name: 'messaggio', required: true, width: '1-1', options: '', icon: '' },
    ],

    // Invio
    email_to: '',
    email_cc: '',
    email_from_name: '',
    email_subject: 'Nuovo messaggio dal sito',
    success_message: 'Messaggio inviato con successo! Ti risponderemo al più presto.',
    error_message: 'Si è verificato un errore. Riprova più tardi.',
    redirect_url: '',
    success_animation: 'slide-top-small',
    error_animation: 'shake',

    // Auto-reply
    auto_reply: false,
    auto_reply_subject: 'Grazie per averci contattato',
    auto_reply_message: 'Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.',

    // Pulsante invio
    submit_text: 'Invia messaggio',
    submit_icon: '',
    submit_icon_pos: 'left',
    submit_alignment: 'left',
    submit_full_width: false,

    // Contenitore form
    form_max_width: '0',
    form_align: 'left',

    // Stile form
    form_layout: 'stacked',
    label_color: '#D1D5DB',
    label_size: '14',
    label_weight: '500',
    input_bg: '#1F2937',
    input_color: '#F3F4F6',
    input_border_color: '#374151',
    input_border_width: '1',
    input_radius: '6',
    input_size: 'default',
    input_focus_border: '',
    input_focus_shadow: true,
    input_placeholder_opacity: '0.4',
    gap: '16',

    // Stile pulsante
    submit_bg: '',
    submit_color: '#FFFFFF',
    submit_radius: '6',
    submit_padding_x: '32',
    submit_padding_y: '14',
    submit_font_size: '16',
    submit_font_weight: '600',
    submit_hover_bg: '',
    submit_border_width: '0',
    submit_border_color: '',
    submit_hover_border_color: '',
    submit_letter_spacing: '0.3',
    submit_text_transform: 'none',

    // Stile checkbox / radio
    check_accent_color: '',
    check_bg: '',
    check_border_color: '',
    check_size: '18',
    check_label_gap: '8',

    // Anti-spam
    honeypot: true,
    rate_limit: true,
    rate_limit_max: '5',
    rate_limit_window: '60',

    // Consenso privacy
    privacy_checkbox: false,
    privacy_text: 'Accetto il trattamento dei dati personali secondo la <a href="/privacy-policy">Privacy Policy</a>',
  },
  fields: [
    // ── Campi ──
    { key: 'fields', label: 'Campi del form', type: 'content-items',
      itemFields: [
        { key: 'field_type', label: 'Tipo campo', type: 'select', options: [
          { value: 'text', label: 'Testo' },
          { value: 'email', label: 'Email' },
          { value: 'tel', label: 'Telefono' },
          { value: 'url', label: 'URL' },
          { value: 'number', label: 'Numero' },
          { value: 'date', label: 'Data' },
          { value: 'time', label: 'Ora' },
          { value: 'textarea', label: 'Area di testo' },
          { value: 'select', label: 'Seleziona' },
          { value: 'radio', label: 'Scelta singola' },
          { value: 'checkbox', label: 'Scelta multipla' },
          { value: 'hidden', label: 'Nascosto' },
        ]},
        { key: 'label', label: 'Etichetta', type: 'text' },
        { key: 'name', label: 'Nome campo (email)', type: 'text' },
        { key: 'placeholder', label: 'Placeholder', type: 'text' },
        { key: 'icon', label: 'Icona UIkit (user, mail, phone...)', type: 'text',
          condition: { field: 'field_type', op: 'in', value: ['text', 'email', 'tel', 'url', 'number', 'date', 'time'] } },
        { key: 'required', label: 'Obbligatorio', type: 'toggle' },
        { key: 'width', label: 'Larghezza', type: 'select', options: [
          { value: '1-1', label: 'Intera (100%)' },
          { value: '1-2', label: 'Metà (50%)' },
          { value: '1-3', label: 'Un terzo (33%)' },
          { value: '2-3', label: 'Due terzi (66%)' },
          { value: '1-4', label: 'Un quarto (25%)' },
          { value: '3-4', label: 'Tre quarti (75%)' },
        ]},
        { key: 'options', label: 'Opzioni (una per riga)', type: 'textarea',
          condition: { field: 'field_type', op: 'in', value: ['select', 'radio', 'checkbox'] } },
      ],
      newItemDefaults: { field_type: 'text', label: 'Nuovo campo', placeholder: '', name: '', required: false, width: '1-1', options: '', icon: '' },
      itemLabel: 'Campo',
    },

    // ── Contenitore form ──
    { type: 'separator', label: 'Contenitore' },
    { key: 'form_max_width', label: 'Larghezza massima (px)', type: 'range', min: 0, max: 900, step: 10 },
    { key: 'form_align', label: 'Allineamento form', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ],
      condition: { field: 'form_max_width', operator: '>', value: 0 } },

    // ── Impostazioni invio ──
    { type: 'separator', label: 'Impostazioni invio' },
    { key: 'email_to', label: 'Email destinatario', type: 'text', placeholder: 'admin@tuosito.it (vuoto = admin)' },
    { key: 'email_cc', label: 'Email CC (copia)', type: 'text', placeholder: 'copia@tuosito.it' },
    { key: 'email_from_name', label: 'Nome mittente', type: 'text', placeholder: 'Nome Sito (vuoto = nome sito)' },
    { key: 'email_subject', label: 'Oggetto email', type: 'text' },
    { key: 'success_message', label: 'Messaggio di successo', type: 'text' },
    { key: 'error_message', label: 'Messaggio di errore', type: 'text' },
    { key: 'redirect_url', label: 'Redirect dopo invio (URL)', type: 'text', placeholder: 'Lascia vuoto per mostrare messaggio' },
    { key: 'success_animation', label: 'Animazione successo', type: 'select', options: [
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'slide-top-small', label: 'Scorrimento dall\'alto' },
      { value: 'slide-bottom-small', label: 'Scorrimento dal basso' },
      { value: 'scale-up', label: 'Scala' },
    ]},
    { key: 'error_animation', label: 'Animazione errore', type: 'select', options: [
      { value: 'shake', label: 'Vibrazione' },
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'slide-top-small', label: 'Scorrimento dall\'alto' },
    ]},

    // ── Auto-reply ──
    { type: 'separator', label: 'Risposta automatica' },
    { key: 'auto_reply', label: 'Invia conferma al compilatore', type: 'toggle' },
    { key: 'auto_reply_subject', label: 'Oggetto risposta', type: 'text',
      condition: { field: 'auto_reply', op: 'eq', value: true } },
    { key: 'auto_reply_message', label: 'Testo risposta', type: 'text',
      condition: { field: 'auto_reply', op: 'eq', value: true } },

    // ── Pulsante invio ──
    { type: 'separator', label: 'Pulsante invio' },
    { key: 'submit_text', label: 'Testo pulsante', type: 'text' },
    { key: 'submit_icon', label: 'Icona UIkit (arrow-right, check...)', type: 'text' },
    { key: 'submit_icon_pos', label: 'Posizione icona', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ],
      condition: { field: 'submit_icon', op: 'notEmpty' } },
    { key: 'submit_alignment', label: 'Allineamento pulsante', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'submit_full_width', label: 'Pulsante larghezza piena', type: 'toggle' },

    // ── Stile campi ──
    { type: 'separator', label: 'Stile campi' },
    { key: 'form_layout', label: 'Layout form', type: 'select', options: [
      { value: 'stacked', label: 'Label sopra (stacked)' },
      { value: 'floating', label: 'Label fluttuante' },
    ]},
    { key: 'label_color', label: 'Colore etichette', type: 'color' },
    { key: 'label_size', label: 'Dimensione etichette (px)', type: 'range', min: 11, max: 18 },
    { key: 'label_weight', label: 'Peso etichette', type: 'select', options: [
      { value: '400', label: 'Normal' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
    ]},
    { key: 'input_bg', label: 'Sfondo input', type: 'color' },
    { key: 'input_color', label: 'Colore testo input', type: 'color' },
    { key: 'input_border_color', label: 'Colore bordo input', type: 'color' },
    { key: 'input_border_width', label: 'Spessore bordo (px)', type: 'range', min: 0, max: 3 },
    { key: 'input_radius', label: 'Raggio bordo input (px)', type: 'range', min: 0, max: 20 },
    { key: 'input_size', label: 'Dimensione input', type: 'select', options: [
      { value: 'small', label: 'Piccolo' },
      { value: 'default', label: 'Normale' },
      { value: 'large', label: 'Grande' },
    ]},
    { key: 'input_focus_border', label: 'Colore bordo focus', type: 'color' },
    { key: 'input_focus_shadow', label: 'Ombra focus', type: 'toggle' },
    { key: 'input_placeholder_opacity', label: 'Opacità placeholder', type: 'range', min: 0.2, max: 0.8, step: 0.05 },
    { key: 'gap', label: 'Gap tra campi (px)', type: 'range', min: 8, max: 32, step: 4 },

    // ── Stile pulsante ──
    { type: 'separator', label: 'Stile pulsante' },
    { key: 'submit_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'submit_color', label: 'Colore testo pulsante', type: 'color' },
    { key: 'submit_hover_bg', label: 'Sfondo pulsante hover', type: 'color' },
    { key: 'submit_radius', label: 'Raggio bordo (px)', type: 'range', min: 0, max: 30 },
    { key: 'submit_padding_x', label: 'Padding orizzontale (px)', type: 'range', min: 16, max: 48 },
    { key: 'submit_padding_y', label: 'Padding verticale (px)', type: 'range', min: 8, max: 24 },
    { key: 'submit_font_size', label: 'Dimensione testo (px)', type: 'range', min: 12, max: 22 },
    { key: 'submit_font_weight', label: 'Peso testo', type: 'select', options: [
      { value: '400', label: 'Normal' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'submit_letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: 0, max: 3, step: 0.1 },
    { key: 'submit_text_transform', label: 'Trasformazione testo', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'MAIUSCOLO' },
      { value: 'lowercase', label: 'minuscolo' },
      { value: 'capitalize', label: 'Iniziale Maiuscola' },
    ]},
    { key: 'submit_border_width', label: 'Bordo pulsante (px)', type: 'range', min: 0, max: 4 },
    { key: 'submit_border_color', label: 'Colore bordo pulsante', type: 'color',
      condition: { field: 'submit_border_width', operator: '>', value: 0 } },
    { key: 'submit_hover_border_color', label: 'Bordo pulsante hover', type: 'color',
      condition: { field: 'submit_border_width', operator: '>', value: 0 } },

    // ── Stile checkbox / radio ──
    { type: 'separator', label: 'Checkbox e Radio' },
    { key: 'check_accent_color', label: 'Colore accento (checked)', type: 'color' },
    { key: 'check_bg', label: 'Sfondo', type: 'color' },
    { key: 'check_border_color', label: 'Colore bordo', type: 'color' },
    { key: 'check_size', label: 'Dimensione (px)', type: 'range', min: 14, max: 24 },
    { key: 'check_label_gap', label: 'Gap etichetta (px)', type: 'range', min: 4, max: 16 },

    // ── Anti-spam ──
    { type: 'separator', label: 'Anti-spam' },
    { key: 'honeypot', label: 'Honeypot (consigliato)', type: 'toggle' },
    { key: 'rate_limit', label: 'Rate limiting', type: 'toggle' },
    { key: 'rate_limit_max', label: 'Max invii per finestra', type: 'range', min: 1, max: 20,
      condition: { field: 'rate_limit', op: 'eq', value: true } },
    { key: 'rate_limit_window', label: 'Finestra (minuti)', type: 'range', min: 5, max: 120, step: 5,
      condition: { field: 'rate_limit', op: 'eq', value: true } },

    // ── Privacy ──
    { type: 'separator', label: 'Privacy' },
    { key: 'privacy_checkbox', label: 'Checkbox consenso privacy', type: 'toggle' },
    { key: 'privacy_text', label: 'Testo consenso', type: 'text',
      condition: { field: 'privacy_checkbox', op: 'eq', value: true } },
  ],
};
