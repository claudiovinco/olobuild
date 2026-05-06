import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'newsletter',
  name: 'Newsletter',
  icon: 'dashicons-email-alt',
  category: 'marketing',
  defaults: {
    // Layout
    layout: 'horizontal',
    preset: 'default',
    // Content
    title: 'Iscriviti alla newsletter',
    subtitle: 'Ricevi aggiornamenti e contenuti esclusivi direttamente nella tua casella email.',
    icon_type: 'none',
    icon_name: 'mail',
    icon_image: '',
    show_name: false,
    name_placeholder: 'Il tuo nome',
    email_placeholder: 'La tua email',
    button_text: 'Iscriviti',
    button_icon: true,
    privacy_text: '',
    privacy_required: false,
    // Success
    success_message: 'Iscrizione completata! Controlla la tua email.',
    success_animation: 'fade',
    redirect_url: '',
    // Content Lock
    content_lock: false,
    lock_message: 'Iscriviti alla newsletter per sbloccare questo contenuto',
    lock_blur: 8,
    lock_height: 200,
    // Integration (reuses form handler)
    integration: 'none',
    mailchimp_api: '',
    mailchimp_list: '',
    brevo_api: '',
    brevo_list: '',
    activecampaign_url: '',
    activecampaign_api: '',
    activecampaign_list: '',
    convertkit_api: '',
    convertkit_form: '',
    hubspot_portal: '',
    hubspot_form: '',
    webhook_url: '',
    webhook_method: 'POST',
    // Anti-spam
    honeypot: true,
    recaptcha: false,
    // Style — Container
    max_width: '600',
    alignment: 'center',
    bg_color: '',
    border_radius: 12,
    tile_padding: { top: 32, right: 32, bottom: 32, left: 32 },
    // Style — Title
    title_size: '24',
    title_weight: '700',
    title_color: '',
    subtitle_size: '14',
    subtitle_color: '',
    // Style — Icon
    icon_size: '48',
    icon_color: '',
    // Style — Input
    input_bg: '#ffffff',
    input_color: '#1F2937',
    input_border: '#D1D5DB',
    input_focus_border: '',
    input_radius: 8,
    input_height: '44',
    // Style — Button
    btn_bg: '',
    btn_color: '#ffffff',
    btn_hover_bg: '',
    btn_radius: 8,
    btn_font_size: '14',
    btn_font_weight: '600',
    // Shadow
    shadow: 'none',
    ...textEffectsDefaults,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    // ── CONTENUTO ──
    { type: 'separator', label: 'Contenuto' },
    { key: 'title', label: 'Titolo', type: 'text' },
    { key: 'subtitle', label: 'Sottotitolo', type: 'textarea', rows: 2 },
    { key: 'icon_type', label: 'Icona', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'emoji', label: 'Emoji / Testo' },
      { value: 'image', label: 'Immagine' },
    ]},
    { key: 'icon_name', label: 'Emoji o testo', type: 'text', placeholder: '📧',
      condition: { field: 'icon_type', value: 'emoji' } },
    { key: 'icon_image', label: 'Immagine icona', type: 'image',
      condition: { field: 'icon_type', value: 'image' } },

    // ── FORM ──
    { type: 'separator', label: 'Form' },
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'horizontal', label: 'Orizzontale (inline)' },
      { value: 'vertical', label: 'Verticale (stacked)' },
      { value: 'minimal', label: 'Minimal (solo email)' },
    ]},
    { key: 'show_name', label: 'Mostra campo nome', type: 'toggle' },
    { key: 'name_placeholder', label: 'Placeholder nome', type: 'text',
      condition: { field: 'show_name', value: true } },
    { key: 'email_placeholder', label: 'Placeholder email', type: 'text' },
    { key: 'button_text', label: 'Testo pulsante', type: 'text' },
    { key: 'button_icon', label: 'Icona pulsante (freccia)', type: 'toggle' },
    { key: 'privacy_text', label: 'Testo privacy (HTML)', type: 'textarea', rows: 2, placeholder: 'Accetto la <a href="/privacy">privacy policy</a>' },
    { key: 'privacy_required', label: 'Privacy obbligatoria', type: 'toggle',
      condition: { field: 'privacy_text', operator: '!=', value: '' } },

    // ── SUCCESSO ──
    { type: 'separator', label: 'Successo' },
    { key: 'success_message', label: 'Messaggio successo', type: 'textarea', rows: 2 },
    { key: 'success_animation', label: 'Animazione successo', type: 'select', options: [
      { value: 'fade', label: 'Fade' },
      { value: 'slide-up', label: 'Scivola su' },
      { value: 'checkmark', label: 'Checkmark animato' },
    ]},
    { key: 'redirect_url', label: 'Redirect dopo iscrizione', type: 'text', placeholder: 'https://...' },

    // ── CONTENT LOCK ──
    { type: 'separator', label: 'Content Lock' },
    { key: 'content_lock', label: 'Blocca contenuto successivo', type: 'toggle' },
    { key: 'lock_message', label: 'Messaggio blocco', type: 'text',
      condition: { field: 'content_lock', value: true } },
    { key: 'lock_blur', label: 'Sfocatura contenuto (px)', type: 'range', min: 0, max: 20, step: 1,
      condition: { field: 'content_lock', value: true } },
    { key: 'lock_height', label: 'Altezza anteprima (px)', type: 'range', min: 50, max: 500, step: 10,
      condition: { field: 'content_lock', value: true } },

    // ── INTEGRAZIONE ──
    { type: 'separator', label: 'Integrazione Email' },
    { key: 'integration', label: 'Provider', type: 'select', options: [
      { value: 'none', label: 'Solo email (wp_mail)' },
      { value: 'mailchimp', label: 'Mailchimp' },
      { value: 'brevo', label: 'Brevo (ex Sendinblue)' },
      { value: 'activecampaign', label: 'ActiveCampaign' },
      { value: 'convertkit', label: 'ConvertKit' },
      { value: 'hubspot', label: 'HubSpot' },
      { value: 'webhook', label: 'Webhook personalizzato' },
    ]},
    { key: 'mailchimp_api', label: 'API Key', type: 'text', condition: { field: 'integration', value: 'mailchimp' } },
    { key: 'mailchimp_list', label: 'List ID', type: 'text', condition: { field: 'integration', value: 'mailchimp' } },
    { key: 'brevo_api', label: 'API Key', type: 'text', condition: { field: 'integration', value: 'brevo' } },
    { key: 'brevo_list', label: 'List IDs (virgola)', type: 'text', condition: { field: 'integration', value: 'brevo' } },
    { key: 'activecampaign_url', label: 'Account URL', type: 'text', placeholder: 'https://account.api-us1.com', condition: { field: 'integration', value: 'activecampaign' } },
    { key: 'activecampaign_api', label: 'API Key', type: 'text', condition: { field: 'integration', value: 'activecampaign' } },
    { key: 'activecampaign_list', label: 'List ID', type: 'text', condition: { field: 'integration', value: 'activecampaign' } },
    { key: 'convertkit_api', label: 'API Key', type: 'text', condition: { field: 'integration', value: 'convertkit' } },
    { key: 'convertkit_form', label: 'Form ID', type: 'text', condition: { field: 'integration', value: 'convertkit' } },
    { key: 'hubspot_portal', label: 'Portal ID', type: 'text', condition: { field: 'integration', value: 'hubspot' } },
    { key: 'hubspot_form', label: 'Form GUID', type: 'text', condition: { field: 'integration', value: 'hubspot' } },
    { key: 'webhook_url', label: 'URL Webhook', type: 'text', condition: { field: 'integration', value: 'webhook' } },
    { key: 'webhook_method', label: 'Metodo', type: 'select', options: [
      { value: 'POST', label: 'POST' },
      { value: 'PUT', label: 'PUT' },
    ], condition: { field: 'integration', value: 'webhook' } },

    // ── ANTI-SPAM ──
    { type: 'separator', label: 'Anti-spam' },
    { key: 'honeypot', label: 'Honeypot (consigliato)', type: 'toggle' },
    { key: 'recaptcha', label: 'reCAPTCHA v3', type: 'toggle' },

    // ── STILE ──
    { type: 'separator', label: 'Stile' },
    { key: 'max_width', label: 'Larghezza max (px)', type: 'text' },
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'bg_color', label: 'Sfondo', type: 'color' },
    { key: 'border_radius', label: 'Raggio bordi', type: 'border-radius'},
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 60 },

    // Testo
    { type: 'separator', label: 'Stile testo' },
    { key: 'title_size', label: 'Dimensione titolo (px)', type: 'range', min: 14, max: 48, step: 1 },
    { key: 'title_weight', label: 'Peso titolo', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
      { value: '800', label: 'Extra Bold' },
    ]},
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'subtitle_size', label: 'Dimensione sottotitolo (px)', type: 'range', min: 11, max: 20, step: 1 },
    { key: 'subtitle_color', label: 'Colore sottotitolo', type: 'color' },

    // Input
    { type: 'separator', label: 'Stile input' },
    { key: 'input_bg', label: 'Sfondo input', type: 'color' },
    { key: 'input_color', label: 'Colore testo input', type: 'color' },
    { key: 'input_border', label: 'Bordo input', type: 'color' },
    { key: 'input_focus_border', label: 'Bordo focus', type: 'color' },
    { key: 'input_radius', label: 'Raggio input', type: 'border-radius'},
    { key: 'input_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'input_height', label: 'Altezza input (px)', type: 'range', min: 32, max: 56, step: 2 },

    // Button
    { type: 'separator', label: 'Stile pulsante' },
    { key: 'btn_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'btn_color', label: 'Colore testo pulsante', type: 'color' },
    { key: 'btn_hover_bg', label: 'Sfondo hover', type: 'color' },
    { key: 'btn_radius', label: 'Raggio pulsante', type: 'border-radius'},
    { key: 'btn_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'btn_font_size', label: 'Dimensione testo (px)', type: 'range', min: 11, max: 20, step: 1 },
    { key: 'btn_font_weight', label: 'Peso testo', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
    ]},
    ...shadowField,
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'subtitle', label: 'Solo Sottotitolo' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
    ...borderFields(),
  ],
};
