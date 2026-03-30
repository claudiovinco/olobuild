import { shadowField } from './_shared.js';

export default {
  type: 'loginform',
  name: 'Login / Registrazione',
  icon: 'dashicons-lock',
  category: 'marketing',
  defaults: {
    mode: 'login',
    redirect_url: '',
    show_remember_me: true,
    show_lost_password: true,
    show_avatar: true,
    logged_in_message: 'Bentornato!',
    logged_out_redirect: '',

    // Titoli
    login_title: 'Bentornato',
    login_subtitle: 'Accedi al tuo account',
    register_title: 'Crea un account',
    register_subtitle: 'Registrati in pochi secondi',

    // Testi pulsanti
    login_button_text: 'Accedi',
    register_button_text: 'Registrati',

    // Visual
    show_input_icons: true,
    show_password_toggle: true,
    show_password_strength: true,
    password_min_length: 8,
    password_require_uppercase: false,
    password_require_number: false,
    password_require_special: false,
    password_min_strength: 0,
    tab_style: 'underline',
    tile_padding: { top: 32, right: 32, bottom: 32, left: 32 },

    // Termini e condizioni
    show_terms: false,
    terms_text: 'Accetto i Termini e le Condizioni',
    terms_url: '',

    // Divider social (visuale)
    show_social_divider: false,
    social_divider_text: 'oppure',
    social_google: false,
    social_facebook: false,
    social_apple: false,
    social_google_url: '#',
    social_facebook_url: '#',
    social_apple_url: '#',

    // Campi registrazione (built-in + personalizzati, riordinabili)
    register_fields: [
      { id: 'rf-username', label: 'Nome utente', field_type: 'username', placeholder: 'Scegli un nome utente', required: true, width: '100', meta_key: '', options: '' },
      { id: 'rf-email', label: 'Email', field_type: 'user_email', placeholder: 'nome@esempio.it', required: true, width: '100', meta_key: '', options: '' },
      { id: 'rf-password', label: 'Password', field_type: 'user_password', placeholder: 'Min. 8 caratteri', required: true, width: '100', meta_key: '', options: '' },
    ],

    // Stile
    form_bg: '',
    text_color: '',
    label_color: '',
    input_bg: '',
    input_color: '',
    input_border_color: '',
    input_padding: '11',
    input_radius: '8',
    input_focus_color: '',
    submit_bg: '',
    submit_color: '',
    submit_radius: '8',
    submit_hover_bg: '',
    link_color: '',
    icon_color: '',
    border_radius: '12',
    border_width: '0',
    border_color: '',
    shadow: 'none',
  },
  fields: [
    // ═══════════════════════════════════════
    //  MODALITÀ
    // ═══════════════════════════════════════
    { key: 'mode', label: 'Modalità', type: 'select', options: [
      { value: 'login', label: 'Solo Login' },
      { value: 'register', label: 'Solo Registrazione' },
      { value: 'both', label: 'Login + Registrazione (tab)' },
    ]},
    { key: 'redirect_url', label: 'Redirect dopo login', type: 'text', placeholder: 'URL (vuoto = pagina corrente)' },
    { key: 'show_remember_me', label: 'Mostra "Ricordami"', type: 'toggle' },
    { key: 'show_lost_password', label: 'Mostra "Password dimenticata"', type: 'toggle' },
    { key: 'show_avatar', label: 'Mostra avatar se loggato', type: 'toggle' },
    { key: 'logged_in_message', label: 'Messaggio utente loggato', type: 'text' },
    { key: 'logged_out_redirect', label: 'Redirect dopo logout', type: 'text', placeholder: 'URL (vuoto = pagina corrente)' },

    // ═══════════════════════════════════════
    //  TITOLI E TESTI
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Titoli e testi' },
    { key: 'login_title', label: 'Titolo login', type: 'text' },
    { key: 'login_subtitle', label: 'Sottotitolo login', type: 'text' },
    { key: 'register_title', label: 'Titolo registrazione', type: 'text',
      condition: { field: 'mode', operator: '!=', value: 'login' } },
    { key: 'register_subtitle', label: 'Sottotitolo registrazione', type: 'text',
      condition: { field: 'mode', operator: '!=', value: 'login' } },
    { key: 'login_button_text', label: 'Testo pulsante login', type: 'text' },
    { key: 'register_button_text', label: 'Testo pulsante registrazione', type: 'text',
      condition: { field: 'mode', operator: '!=', value: 'login' } },

    // ═══════════════════════════════════════
    //  FUNZIONALITÀ VISIVE
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Funzionalità visive' },
    { key: 'show_input_icons', label: 'Icone negli input', type: 'toggle' },
    { key: 'show_password_toggle', label: 'Mostra/nascondi password', type: 'toggle' },
    { key: 'show_password_strength', label: 'Indicatore forza password', type: 'toggle',
      condition: { field: 'mode', operator: '!=', value: 'login' } },
    { key: 'password_min_length', label: 'Lunghezza minima password', type: 'range', min: 4, max: 24, step: 1,
      condition: { field: 'mode', operator: '!=', value: 'login' } },
    { key: 'password_require_uppercase', label: 'Richiedi maiuscola', type: 'toggle',
      condition: { field: 'mode', operator: '!=', value: 'login' } },
    { key: 'password_require_number', label: 'Richiedi numero', type: 'toggle',
      condition: { field: 'mode', operator: '!=', value: 'login' } },
    { key: 'password_require_special', label: 'Richiedi carattere speciale', type: 'toggle',
      condition: { field: 'mode', operator: '!=', value: 'login' } },
    { key: 'password_min_strength', label: 'Forza minima per registrarsi', type: 'select',
      condition: { field: 'mode', operator: '!=', value: 'login' },
      options: [
        { value: 0, label: 'Nessun requisito' },
        { value: 1, label: 'Debole (1/4)' },
        { value: 2, label: 'Media (2/4)' },
        { value: 3, label: 'Buona (3/4)' },
        { value: 4, label: 'Forte (4/4)' },
      ]},
    { key: 'tab_style', label: 'Stile tab', type: 'select', options: [
      { value: 'underline', label: 'Sottolineato' },
      { value: 'pill', label: 'Pillola' },
      { value: 'classic', label: 'Classico' },
    ], condition: { field: 'mode', value: 'both' } },

    // ═══════════════════════════════════════
    //  TERMINI E CONDIZIONI
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Termini e condizioni' },
    { key: 'show_terms', label: 'Mostra checkbox termini', type: 'toggle' },
    { key: 'terms_text', label: 'Testo', type: 'text',
      condition: { field: 'show_terms', value: true } },
    { key: 'terms_url', label: 'URL pagina termini', type: 'text',
      condition: { field: 'show_terms', value: true } },

    // ═══════════════════════════════════════
    //  SOCIAL LOGIN
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Social login' },
    { key: 'show_social_divider', label: 'Mostra sezione social', type: 'toggle' },
    { key: 'social_divider_text', label: 'Testo divisore', type: 'text',
      condition: { field: 'show_social_divider', value: true } },
    { key: 'social_google', label: 'Google', type: 'toggle',
      condition: { field: 'show_social_divider', value: true } },
    { key: 'social_google_url', label: 'URL Google', type: 'text',
      condition: { field: 'social_google', value: true } },
    { key: 'social_facebook', label: 'Facebook', type: 'toggle',
      condition: { field: 'show_social_divider', value: true } },
    { key: 'social_facebook_url', label: 'URL Facebook', type: 'text',
      condition: { field: 'social_facebook', value: true } },
    { key: 'social_apple', label: 'Apple', type: 'toggle',
      condition: { field: 'show_social_divider', value: true } },
    { key: 'social_apple_url', label: 'URL Apple', type: 'text',
      condition: { field: 'social_apple', value: true } },

    // ═══════════════════════════════════════
    //  CAMPI REGISTRAZIONE
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Campi registrazione' },
    {
      key: 'register_fields',
      label: 'Campi del form',
      type: 'content-items',
      condition: { field: 'mode', operator: '!=', value: 'login' },
      itemFields: [
        { key: 'label', label: 'Etichetta', type: 'text' },
        { key: 'field_type', label: 'Tipo', type: 'select', options: [
          { value: 'username', label: 'Nome utente (built-in)' },
          { value: 'user_email', label: 'Email (built-in)' },
          { value: 'user_password', label: 'Password (built-in)' },
          { value: 'confirm_password', label: 'Conferma password (built-in)' },
          { value: 'text', label: 'Testo' },
          { value: 'email', label: 'Email personalizzata' },
          { value: 'tel', label: 'Telefono' },
          { value: 'number', label: 'Numero' },
          { value: 'date', label: 'Data' },
          { value: 'url', label: 'URL' },
          { value: 'textarea', label: 'Area testo' },
          { value: 'select', label: 'Dropdown' },
          { value: 'checkbox', label: 'Checkbox' },
          { value: 'radio', label: 'Radio' },
        ]},
        { key: 'placeholder', label: 'Placeholder', type: 'text' },
        { key: 'meta_key', label: 'Chiave user_meta', type: 'text' },
        { key: 'required', label: 'Obbligatorio', type: 'toggle' },
        { key: 'width', label: 'Larghezza', type: 'select', options: [
          { value: '100', label: '100%' },
          { value: '50', label: '50%' },
          { value: '33', label: '33%' },
        ]},
        { key: 'options', label: 'Opzioni (una per riga)', type: 'textarea',
          condition: { field: 'field_type', operator: 'in', value: ['select', 'radio'] } },
      ],
      newItemDefaults: {
        label: 'Nuovo campo',
        field_type: 'text',
        placeholder: '',
        meta_key: '',
        required: false,
        width: '100',
        options: '',
      },
      itemLabel: 'Campo',
    },

    // ═══════════════════════════════════════
    //  COLORI
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Colori' },
    { key: 'form_bg', label: 'Sfondo form', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'label_color', label: 'Colore label', type: 'color' },
    { key: 'input_bg', label: 'Sfondo input', type: 'color' },
    { key: 'input_color', label: 'Colore testo input', type: 'color' },
    { key: 'input_border_color', label: 'Colore bordo input', type: 'color' },
    { key: 'input_focus_color', label: 'Colore bordo focus', type: 'color' },
    { key: 'icon_color', label: 'Colore icone input', type: 'color',
      condition: { field: 'show_input_icons', value: true } },
    { key: 'link_color', label: 'Colore link', type: 'color' },
    { key: 'submit_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'submit_color', label: 'Colore testo pulsante', type: 'color' },
    { key: 'submit_hover_bg', label: 'Sfondo pulsante hover', type: 'color' },

    // ═══════════════════════════════════════
    //  ASPETTO FORM
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Aspetto form' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 64 },
    { key: 'input_padding', label: 'Padding input (px)', type: 'spacing', max: 24 },
    { key: 'input_radius', label: 'Raggio bordo input (px)', type: 'border-radius' },
    { key: 'submit_radius', label: 'Raggio bordo pulsante (px)', type: 'border-radius' },
    { key: 'border_radius', label: 'Raggio bordo form (px)', type: 'border-radius' },
    { key: 'border_width', label: 'Bordo form (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'border_color', label: 'Colore bordo form', type: 'color',
      condition: { field: 'border_width', operator: '>', value: '0' } },
    ...shadowField,
  ],
};
