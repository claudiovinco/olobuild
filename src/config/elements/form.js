import { textEffectsFields, textEffectsDefaults } from './_shared';
export default {
  type: 'form',
  name: 'Form Contatti',
  icon: 'dashicons-email-alt',
  category: 'marketing',
  defaults: {
    fields: [
      { id: 'f-1', field_type: 'text', label: 'Nome', placeholder: 'Il tuo nome', name: 'nome', required: true, width: '1-2', options: '', icon: 'user', condition_field: '', condition_operator: 'equals', condition_value: '' },
      { id: 'f-2', field_type: 'email', label: 'Email', placeholder: 'La tua email', name: 'email', required: true, width: '1-2', options: '', icon: 'mail', condition_field: '', condition_operator: 'equals', condition_value: '' },
      { id: 'f-3', field_type: 'text', label: 'Oggetto', placeholder: 'Oggetto del messaggio', name: 'oggetto', required: false, width: '1-1', options: '', icon: '', condition_field: '', condition_operator: 'equals', condition_value: '' },
      { id: 'f-4', field_type: 'textarea', label: 'Messaggio', placeholder: 'Scrivi il tuo messaggio...', name: 'messaggio', required: true, width: '1-1', options: '', icon: '', condition_field: '', condition_operator: 'equals', condition_value: '' },
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
    label_color: '',
    label_size: '14',
    label_weight: '500',
    input_bg: '',
    input_color: '',
    input_border_color: '',
    input_border_width: '1',
    input_radius: '6',
    input_size: 'default',
    input_focus_border: '',
    input_focus_shadow: true,
    input_placeholder_opacity: '0.4',
    gap: '16',

    // Stile pulsante
    submit_bg: '',
    submit_color: '',
    submit_radius: '6',
    tile_padding: { top: 12, right: 24, bottom: 12, left: 24 },
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

    // Multi-step
    enable_multistep: false,
    step_style: 'progress',
    step_labels: '',

    // Conditional logic
    enable_conditions: false,

    // reCAPTCHA v3
    recaptcha_enabled: false,

    // Integrations
    mailchimp_enabled: false,
    mailchimp_list_id: '',
    mailchimp_email_field: 'email',
    mailchimp_merge_fields: '',
    webhook_enabled: false,
    webhook_url: '',
    webhook_method: 'POST',

    // HubSpot
    hubspot_enabled: false,
    hubspot_portal_id: '',
    hubspot_form_guid: '',

    // ActiveCampaign
    activecampaign_enabled: false,
    activecampaign_list_id: '',
    activecampaign_email_field: 'email',

    // ConvertKit
    convertkit_enabled: false,
    convertkit_form_id: '',
    convertkit_email_field: 'email',

    // Brevo (ex Sendinblue)
    brevo_enabled: false,
    brevo_list_id: '',
    brevo_email_field: 'email',

    // File upload
    file_max_size: '5',
    file_types: '.pdf,.doc,.docx,.jpg,.png',

    // Submissions storage
    store_submissions: false,

    // Consenso privacy
    privacy_checkbox: false,
    privacy_text: 'Accetto il trattamento dei dati personali secondo la <a href="/privacy-policy">Privacy Policy</a>',
    ...textEffectsDefaults,
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
          { value: 'file', label: 'File upload' },
          { value: 'datetime', label: 'Data e ora' },
          { value: 'range', label: 'Range slider' },
          { value: 'star_rating', label: 'Valutazione stelle' },
          { value: 'password', label: 'Password' },
          { value: 'password_confirm', label: 'Conferma password' },
          { value: 'color', label: 'Selettore colore' },
          { value: 'calculation', label: 'Campo calcolato' },
          { value: 'step', label: '── Separatore step ──' },
        ]},
        { key: 'label', label: 'Etichetta', type: 'text' },
        { key: 'name', label: 'Nome campo (email)', type: 'text' },
        { key: 'placeholder', label: 'Placeholder', type: 'text' },
        { key: 'icon', label: 'Icona campo', type: 'icon',
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
        { key: 'file_allowed_types', label: 'Tipi file ammessi', type: 'text',
          condition: { field: 'field_type', op: 'in', value: ['file'] } },
        { key: 'file_max_size', label: 'Max dimensione (MB)', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['file'] } },
        { key: 'file_max_files', label: 'Max file', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['file'] } },
        { key: 'file_button_text', label: 'Testo pulsante', type: 'text',
          condition: { field: 'field_type', op: 'in', value: ['file'] } },
        { key: 'range_min', label: 'Valore minimo', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['range'] } },
        { key: 'range_max', label: 'Valore massimo', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['range'] } },
        { key: 'range_step', label: 'Step', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['range'] } },
        { key: 'range_default', label: 'Valore default', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['range'] } },
        { key: 'star_count', label: 'Numero stelle', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['star_rating'] } },
        { key: 'default_value', label: 'Valore default', type: 'text',
          condition: { field: 'field_type', op: 'in', value: ['hidden'] } },
        { key: 'calc_formula', label: 'Formula (es. {quantita} * {prezzo})', type: 'text',
          condition: { field: 'field_type', op: 'in', value: ['calculation'] } },
        { key: 'calc_prefix', label: 'Prefisso (es. €)', type: 'text',
          condition: { field: 'field_type', op: 'in', value: ['calculation'] } },
        { key: 'calc_suffix', label: 'Suffisso', type: 'text',
          condition: { field: 'field_type', op: 'in', value: ['calculation'] } },
        { key: 'calc_decimals', label: 'Decimali', type: 'number',
          condition: { field: 'field_type', op: 'in', value: ['calculation'] } },
        { key: 'condition_field', label: 'Condizione: campo', type: 'text', placeholder: 'Nome campo (es. nome)' },
        { key: 'condition_operator', label: 'Condizione: operatore', type: 'select', options: [
          { value: 'equals', label: 'Uguale a' },
          { value: 'not_equals', label: 'Diverso da' },
          { value: 'contains', label: 'Contiene' },
          { value: 'not_empty', label: 'Non vuoto' },
          { value: 'empty', label: 'Vuoto' },
        ], condition: { field: 'condition_field', op: 'notEmpty' } },
        { key: 'condition_value', label: 'Condizione: valore', type: 'text',
          condition: { field: 'condition_field', op: 'notEmpty' } },
      ],
      newItemDefaults: { field_type: 'text', label: 'Nuovo campo', placeholder: '', name: '', required: false, width: '1-1', options: '', icon: '', file_allowed_types: '.pdf,.doc,.docx,.jpg,.png', file_max_size: 5, file_max_files: 1, file_button_text: 'Scegli file', range_min: 0, range_max: 100, range_step: 1, range_default: 50, star_count: 5, default_value: '', calc_formula: '', calc_prefix: '', calc_suffix: '', calc_decimals: 2, condition_field: '', condition_operator: 'equals', condition_value: '' },
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
    { key: 'submit_icon', label: 'Icona pulsante', type: 'icon' },
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
    { key: 'input_radius', label: 'Raggio bordo input (px)', type: 'border-radius' },
    { key: 'input_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
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
    { key: 'submit_radius', label: 'Raggio bordo (px)', type: 'border-radius' },
    { key: 'submit_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 48 },
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

    // ── Multi-step ──
    { type: 'separator', label: 'Multi-step' },
    { key: 'enable_multistep', label: 'Form multi-step', type: 'toggle' },
    { key: 'step_style', label: 'Stile step', type: 'select', options: [
      { value: 'progress', label: 'Barra progresso' },
      { value: 'numbers', label: 'Numeri' },
      { value: 'dots', label: 'Pallini' },
    ], condition: { field: 'enable_multistep', operator: '==', value: true } },
    { key: 'step_labels', label: 'Etichette step (una per riga)', type: 'textarea',
      condition: { field: 'enable_multistep', operator: '==', value: true } },

    // ── Logica condizionale ──
    { type: 'separator', label: 'Logica condizionale' },
    { key: 'enable_conditions', label: 'Abilita condizioni', type: 'toggle' },

    // ── File upload ──
    { type: 'separator', label: 'File upload' },
    { key: 'file_max_size', label: 'Dimensione max file (MB)', type: 'range', min: 1, max: 25, step: 1 },
    { key: 'file_types', label: 'Tipi file ammessi', type: 'text' },

    // ── Archiviazione ──
    { type: 'separator', label: 'Archiviazione' },
    { key: 'store_submissions', label: 'Salva invii nel database', type: 'toggle' },

    // ── Anti-spam ──
    { type: 'separator', label: 'Anti-spam' },
    { key: 'honeypot', label: 'Honeypot (consigliato)', type: 'toggle' },
    { key: 'rate_limit', label: 'Rate limiting', type: 'toggle' },
    { key: 'rate_limit_max', label: 'Max invii per finestra', type: 'range', min: 1, max: 20,
      condition: { field: 'rate_limit', op: 'eq', value: true } },
    { key: 'rate_limit_window', label: 'Finestra (minuti)', type: 'range', min: 5, max: 120, step: 5,
      condition: { field: 'rate_limit', op: 'eq', value: true } },
    { key: 'recaptcha_enabled', label: 'reCAPTCHA v3', type: 'toggle' },

    // ── Integrazioni ──
    { type: 'separator', label: 'Integrazioni' },
    { key: 'mailchimp_enabled', label: 'Mailchimp', type: 'toggle' },
    { key: 'mailchimp_list_id', label: 'List/Audience ID', type: 'text', placeholder: 'abc1234def',
      condition: { field: 'mailchimp_enabled', op: 'eq', value: true } },
    { key: 'mailchimp_email_field', label: 'Nome campo email', type: 'text', placeholder: 'email',
      condition: { field: 'mailchimp_enabled', op: 'eq', value: true } },
    { key: 'mailchimp_merge_fields', label: 'Merge fields (campo=MERGE)', type: 'textarea', placeholder: 'nome=FNAME\ncognome=LNAME',
      condition: { field: 'mailchimp_enabled', op: 'eq', value: true } },
    { key: 'webhook_enabled', label: 'Webhook', type: 'toggle' },
    { key: 'webhook_url', label: 'Webhook URL', type: 'text', placeholder: 'https://...',
      condition: { field: 'webhook_enabled', op: 'eq', value: true } },
    { key: 'webhook_method', label: 'Metodo HTTP', type: 'select', options: [
      { value: 'POST', label: 'POST' },
      { value: 'PUT', label: 'PUT' },
    ], condition: { field: 'webhook_enabled', op: 'eq', value: true } },
    { key: 'hubspot_enabled', label: 'HubSpot', type: 'toggle' },
    { key: 'hubspot_portal_id', label: 'HubSpot Portal ID', type: 'text', placeholder: '12345678',
      condition: { field: 'hubspot_enabled', op: 'eq', value: true } },
    { key: 'hubspot_form_guid', label: 'HubSpot Form GUID', type: 'text', placeholder: 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
      condition: { field: 'hubspot_enabled', op: 'eq', value: true } },
    { key: 'activecampaign_enabled', label: 'ActiveCampaign', type: 'toggle' },
    { key: 'activecampaign_list_id', label: 'List ID', type: 'text', placeholder: '1',
      condition: { field: 'activecampaign_enabled', op: 'eq', value: true } },
    { key: 'activecampaign_email_field', label: 'Nome campo email', type: 'text', placeholder: 'email',
      condition: { field: 'activecampaign_enabled', op: 'eq', value: true } },
    { key: 'convertkit_enabled', label: 'ConvertKit', type: 'toggle' },
    { key: 'convertkit_form_id', label: 'Form ID', type: 'text', placeholder: '1234567',
      condition: { field: 'convertkit_enabled', op: 'eq', value: true } },
    { key: 'convertkit_email_field', label: 'Nome campo email', type: 'text', placeholder: 'email',
      condition: { field: 'convertkit_enabled', op: 'eq', value: true } },
    { key: 'brevo_enabled', label: 'Brevo (Sendinblue)', type: 'toggle' },
    { key: 'brevo_list_id', label: 'List ID', type: 'text', placeholder: '3',
      condition: { field: 'brevo_enabled', op: 'eq', value: true } },
    { key: 'brevo_email_field', label: 'Nome campo email', type: 'text', placeholder: 'email',
      condition: { field: 'brevo_enabled', op: 'eq', value: true } },

    // ── Privacy ──
    { type: 'separator', label: 'Privacy' },
    { key: 'privacy_checkbox', label: 'Checkbox consenso privacy', type: 'toggle' },
    { key: 'privacy_text', label: 'Testo consenso', type: 'text',
      condition: { field: 'privacy_checkbox', op: 'eq', value: true } },
    ...textEffectsFields([
      { value: 'label', label: 'Solo Etichetta' },
      { value: 'name', label: 'Solo Nome' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
  ],
};
