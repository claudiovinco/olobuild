import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile HiddenPop — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → modalità, testi, immagine, CTA, trigger, frequenza, display rules
 *   styleFields[] → aspetto modale (sfondo, colori, ombra, radius, overlay, animazione, padding, bordo)
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'hiddenpop',
  name: t('Popup Nascosto'),
  icon: 'dashicons-flag',
  category: 'interactive',
  defaults: {
    // Contenuto
    mode: 'simple',
    title: t('Titolo popup'),
    subtitle: '',
    image: '',
    image_position: 'top',
    cta_text: '',
    cta_url: '#',
    cta_style: 'primary',
    cta_target: '_self',
    template_id: 0,
    // Modale
    modal_max_width: 560,
    modal_bg_color: '#ffffff',
    modal_shadow: 'lg',
    modal_radius: '16',
    modal_border_width: '0',
    modal_border_color: '',
    modal_overlay: '60',
    modal_close_button: true,
    popup_close_overlay: true,
    popup_overlay_blur: 0,
    popup_animation: 'slide-up',
    // Stile testo
    title_color: '#111827',
    title_size: '24',
    text_color: '#4b5563',
    // Trigger
    trigger_threshold: 50,
    trigger_direction: 'down',
    exit_intent: false,
    retrigger: false,
    // Trigger sequenza-tasti (Konami) — additivo, default OFF: gli altri trigger restano invariati
    key_sequence: false,
    key_sequence_keys: '↑↑↓↓←→←→ba',
    key_sequence_confetti: false,
    // Colori coriandoli: 3 slot color picker (vuoti = palette brand).
    // key_sequence_confetti_colors (CSV legacy) resta letta dal PHP come fallback.
    key_sequence_confetti_colors: '',
    confetti_color_1: '',
    confetti_color_2: '',
    confetti_color_3: '',
    popup_frequency: 'always',
    show_max_times: 0,
    // Display rules
    display_device: '',
    display_logged: '',
    display_date_from: '',
    display_date_to: '',
    display_referrer: '',
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
    border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'mode', label: t('Modalità contenuto'), type: 'select', options: [
      { value: 'simple', label: t('Semplice') },
      { value: 'template', label: t('Template Olobuild') },
    ]},
    { key: 'title', label: t('Titolo'), type: 'text',
      condition: { field: 'mode', op: 'eq', value: 'simple' } },
    { key: 'subtitle', label: t('Testo / Descrizione'), type: 'textarea',
      condition: { field: 'mode', op: 'eq', value: 'simple' } },
    { key: 'image', label: t('Immagine'), type: 'image',
      condition: { field: 'mode', op: 'eq', value: 'simple' } },
    { key: 'image_position', label: t('Posizione immagine'), type: 'select', options: [
      { value: 'top', label: t('Sopra') },
      { value: 'bottom', label: t('Sotto') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ], condition: { field: 'mode', op: 'eq', value: 'simple' } },
    { key: 'template_id', label: t('Template'), type: 'select', optionsSource: 'templates',
      condition: { field: 'mode', op: 'eq', value: 'template' } },

    { type: 'separator', label: t('Pulsante CTA') },
    { key: 'cta_text', label: t('Testo pulsante'), type: 'text', placeholder: t('Lascia vuoto per nascondere'),
      condition: { field: 'mode', op: 'eq', value: 'simple' } },
    { key: 'cta_url', label: t('URL pulsante'), type: 'link',
      condition: { field: 'mode', op: 'eq', value: 'simple' } },
    { key: 'cta_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova finestra') },
    ], condition: { field: 'mode', op: 'eq', value: 'simple' } },

    { type: 'separator', label: t('Trigger') },
    { key: 'exit_intent', label: t('Exit Intent (mouse verso top)'), type: 'toggle' },
    { key: 'trigger_threshold', label: t('Punto di attivazione (% viewport)'), type: 'range', min: 10, max: 90, step: 5,
      condition: { field: 'exit_intent', op: 'eq', value: false } },
    { key: 'trigger_direction', label: t('Direzione scroll'), type: 'select', options: [
      { value: 'down', label: t('Solo verso il basso') },
      { value: 'up', label: t('Solo verso l\'alto') },
      { value: 'both', label: t('Entrambe le direzioni') },
    ], condition: { field: 'exit_intent', op: 'eq', value: false } },
    { key: 'retrigger', label: t('Ri-attiva a ogni passaggio'), type: 'toggle',
      condition: { field: 'exit_intent', op: 'eq', value: false } },

    { type: 'separator', label: t('Sequenza di tasti (Konami)') },
    { key: 'key_sequence', label: t('Apri con sequenza di tasti'), type: 'toggle',
      description: t('Mostra il popup quando il visitatore digita una sequenza segreta (es. codice Konami). Funziona insieme agli altri trigger.') },
    { key: 'key_sequence_keys', label: t('Sequenza'), type: 'text',
      placeholder: '↑↑↓↓←→←→ba',
      description: t('Usa le frecce ↑ ↓ ← → e le lettere/numeri (es. “ba”). Spazi e maiuscole ignorati.'),
      condition: { field: 'key_sequence', op: 'eq', value: true } },
    { key: 'key_sequence_confetti', label: t('Coriandoli alla scoperta'), type: 'toggle',
      description: t('Lancia un effetto coriandoli quando la sequenza viene completata.'),
      condition: { field: 'key_sequence', op: 'eq', value: true } },
    // Palette coriandoli: 3 slot color picker (pattern goo.js color_1..color_5).
    // Il PHP raccoglie i non-vuoti; se tutti vuoti fallback alla CSV legacy
    // key_sequence_confetti_colors e poi alla palette brand.
    { key: 'confetti_color_1', label: t('Colore coriandoli 1'), type: 'color',
      description: t('Lascia vuoti gli slot per usare la palette del brand.'),
      condition: { field: 'key_sequence_confetti', op: 'eq', value: true } },
    { key: 'confetti_color_2', label: t('Colore coriandoli 2'), type: 'color',
      condition: { field: 'key_sequence_confetti', op: 'eq', value: true } },
    { key: 'confetti_color_3', label: t('Colore coriandoli 3'), type: 'color',
      condition: { field: 'key_sequence_confetti', op: 'eq', value: true } },

    { type: 'separator', label: t('Frequenza') },
    { key: 'popup_frequency', label: t('Frequenza'), type: 'select', options: [
      { value: 'always', label: t('Sempre') },
      { value: 'once_session', label: t('Una volta per sessione') },
      { value: 'once_day', label: t('Una volta al giorno') },
      { value: 'once_week', label: t('Una volta a settimana') },
      { value: 'once_ever', label: t('Solo una volta') },
    ]},
    { key: 'show_max_times', label: t('Max visualizzazioni (0=illimitato)'), type: 'range', min: 0, max: 50 },

    { type: 'separator', label: t('Regole visualizzazione') },
    { key: 'display_device', label: t('Dispositivo'), type: 'select', options: [
      { value: '', label: t('Tutti') },
      { value: 'desktop', label: t('Solo desktop') },
      { value: 'mobile', label: t('Solo mobile') },
    ]},
    { key: 'display_logged', label: t('Stato utente'), type: 'select', options: [
      { value: '', label: t('Tutti') },
      { value: 'logged_in', label: t('Solo loggati') },
      { value: 'logged_out', label: t('Solo visitatori') },
    ]},
    { key: 'display_date_from', label: t('Mostra dal'), type: 'date' },
    { key: 'display_date_to', label: t('Mostra fino al'), type: 'date' },
    { key: 'display_referrer', label: t('Solo da referrer (contiene)'), type: 'text', placeholder: t('google.com') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Stile pulsante CTA') },
    { key: 'cta_style', label: t('Stile pulsante'), type: 'select', options: [
      { value: 'primary', label: t('Primario') },
      { value: 'secondary', label: t('Secondario') },
      { value: 'danger', label: t('Pericolo') },
      { value: 'text', label: t('Testo') },
    ], condition: { field: 'mode', op: 'eq', value: 'simple' } },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      responsiveKeys: ['size'],
      keys: {
        size:  'title_size',
        color: 'title_color',
      },
      sizeMin: 14, sizeMax: 48,
      condition: { field: 'mode', op: 'eq', value: 'simple' },
    },
    { type: 'typography', label: t('Testo'),
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
      condition: { field: 'mode', op: 'eq', value: 'simple' },
    },

    { type: 'separator', label: t('Aspetto modale') },
    { key: 'modal_max_width', label: t('Larghezza max (px)'), type: 'range', min: 300, max: 900, step: 10 },
    { key: 'modal_bg_color', label: t('Sfondo card'), type: 'color' },
    { key: 'modal_shadow', label: t('Ombra'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
    ]},
    withHover({ key: 'modal_radius', label: t('Bordo arrotondato'), type: 'border-radius' }),
    { key: 'modal_border_width', label: t('Spessore bordo'), type: 'range', min: 0, max: 10, step: 1 },
    { key: 'modal_border_color', label: t('Colore bordo'), type: 'color' },

    { type: 'separator', label: t('Overlay e comportamento') },
    { key: 'modal_overlay', label: t('Oscuramento sfondo'), type: 'range', min: 0, max: 100, step: 5 },
    { key: 'modal_close_button', label: t('Pulsante chiudi (X)'), type: 'toggle' },
    { key: 'popup_close_overlay', label: t('Chiudi su click overlay'), type: 'toggle' },
    { key: 'popup_overlay_blur', label: t('Sfocatura overlay (px)'), type: 'range', min: 0, max: 20, step: 1 },
    { key: 'popup_animation', label: t('Animazione apertura'), type: 'select', options: [
      { value: 'fade', label: t('Fade') },
      { value: 'slide-up', label: t('Scorrimento su') },
      { value: 'slide-down', label: t('Scorrimento giù') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'flip', label: t('Flip') },
    ]},

    { type: 'separator', label: t('Contenitore') },
    { key: 'tile_padding', type: 'spacing', label: t('Spaziatura interna') },
    withHover({ key: 'border_radius', type: 'border-radius', label: t('Raggio bordo') }),
    ...borderFields(),
  ],
};
