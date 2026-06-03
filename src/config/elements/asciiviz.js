import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ASCIIViz — visualizer audio in caratteri ASCII (famiglia C, bucket C).
 * Riferimento visivo/runtime: handoff-tile-speciali/temi/67-tema-radio-notturna.html.
 *
 * Una griglia di caratteri monospaziati (<pre> aggiornato via textContent → resta
 * TESTO selezionabile/accessibile, NO canvas) che "danza" come un equalizzatore.
 *   - reactTo: simulated  → somma di sinusoidi + rumore (porta lo snippet del tema)
 *   - reactTo: real-audio → AnalyserNode.getByteFrequencyData su un <audio> sorgente
 * Mappa ampiezza→altezza colonna→carattere del `ramp`.
 *
 * Contratto §2: ogni numero/colore/testo è un campo con default; UID scoped per
 * istanza (CSS, @keyframes); SSR già visibile; runtime IIFE inline idempotente,
 * multi-istanza, IntersectionObserver, prefers-reduced-motion, fallback no-WebAudio.
 * Chiavi salvate additive: il <pre> è aria-hidden, ma titolo/stato è testo reale.
 */
export default {
  type: 'asciiviz',
  name: t('Visualizer ASCII'),
  icon: 'dashicons-format-audio',
  category: 'media',
  defaults: {
    // ── Contenuto / player ──
    show_player: true,
    track_label: 'Ora in onda',
    track_name: 'Velluto Blu — Måni',
    audio_url: '',
    autoplay: false,
    show_progress: true,
    show_listeners: true,
    listeners_label: 'in ascolto',
    listeners_count: 1204,
    listeners_drift: true,

    // ── Visualizer ──
    react_to: 'simulated',
    cols: 64,
    rows: 12,
    ramp: ' ·:-=+*o%#@',
    char_top: '█',
    idle_amplitude: 0.06,
    react_speed: 1.6,

    // ── Aspetto ──
    color: '',
    bg_color: '',
    glow: 8,
    font_size: 13,
    line_height: 1.02,
    letter_spacing: 1,
    radius: 18,
    padding: 24,

    // ── Bordo (sistema condiviso) ──
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Sorgente') },
    { key: 'react_to', label: t('Reagisce a'), type: 'select', options: [
      { value: 'simulated', label: t('Simulato (sinusoidi + rumore)') },
      { value: 'real-audio', label: t('Audio reale (analisi frequenze)') },
    ], description: t('"Simulato" anima sempre, senza file. "Audio reale" usa Web Audio sul brano: serve un URL audio e il consenso a riprodurre. Senza Web Audio o con prefers-reduced-motion → onda statica/lenta.') },
    { key: 'audio_url', label: t('File audio (mp3/ogg)'), type: 'media', accept: 'audio',
      description: t('Sorgente del player (Media Library). In modalità "Audio reale" è la traccia analizzata.'),
      condition: { field: 'react_to', op: 'eq', value: 'real-audio' } },
    { key: 'autoplay', label: t('Riproduci automaticamente'), type: 'toggle',
      description: t('I browser bloccano l\'autoplay con audio finché non c\'è interazione: spesso parte solo dopo il primo click.'),
      condition: { field: 'react_to', op: 'eq', value: 'real-audio' } },

    { type: 'separator', label: t('Player') },
    { key: 'show_player', label: t('Mostra barra player'), type: 'toggle' },
    { key: 'track_label', label: t('Etichetta (sopra il titolo)'), type: 'text',
      condition: { field: 'show_player', op: 'eq', value: true } },
    { key: 'track_name', label: t('Titolo brano'), type: 'text',
      condition: { field: 'show_player', op: 'eq', value: true } },
    { key: 'show_progress', label: t('Mostra barra di avanzamento'), type: 'toggle',
      condition: { field: 'show_player', op: 'eq', value: true } },
    { key: 'show_listeners', label: t('Mostra contatore ascoltatori'), type: 'toggle',
      condition: { field: 'show_player', op: 'eq', value: true } },
    { key: 'listeners_count', label: t('Ascoltatori (numero base)'), type: 'number', min: 0, max: 9999999,
      condition: { field: 'show_listeners', op: 'eq', value: true } },
    { key: 'listeners_label', label: t('Testo dopo il numero'), type: 'text',
      condition: { field: 'show_listeners', op: 'eq', value: true } },
    { key: 'listeners_drift', label: t('Numero fluttuante (demo live)'), type: 'toggle',
      description: t('Fa oscillare leggermente il contatore come uno stream reale. Fermo con prefers-reduced-motion.'),
      condition: { field: 'show_listeners', op: 'eq', value: true } },

    { type: 'separator', label: t('Griglia ASCII') },
    { key: 'cols', label: t('Colonne'), type: 'range', min: 16, max: 160, step: 1,
      description: t('Larghezza della griglia in caratteri.') },
    { key: 'rows', label: t('Righe'), type: 'range', min: 4, max: 32, step: 1,
      description: t('Altezza dell\'equalizzatore in righe.') },
    { key: 'ramp', label: t('Set di caratteri (dal vuoto al pieno)'), type: 'text',
      description: t('Rampa di densità: primo carattere = silenzio, ultimo = quasi pieno. Es. " ·:-=+*o%#@".') },
    { key: 'char_top', label: t('Carattere di colonna piena'), type: 'text',
      description: t('Usato quando la colonna raggiunge il massimo (sopra la rampa). Default "█".') },
    { key: 'idle_amplitude', label: t('Ampiezza a riposo'), type: 'range', min: 0, max: 0.5, step: 0.01,
      description: t('Quanto ondeggia quando è in pausa (0 = piatto).') },
    { key: 'react_speed', label: t('Reattività (velocità onda)'), type: 'range', min: 0.3, max: 4, step: 0.1,
      condition: { field: 'react_to', op: 'eq', value: 'simulated' } },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'color', label: t('Colore caratteri'), type: 'color',
      description: t('Vuoto = colore primario del tema.') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color',
      description: t('Vuoto = sfondo scuro predefinito.') },
    { key: 'glow', label: t('Bagliore (glow px)'), type: 'range', min: 0, max: 30, step: 1 },

    { type: 'separator', label: t('Tipografia ASCII') },
    { key: 'font_size', label: t('Dimensione carattere (px)'), type: 'range', min: 6, max: 24, step: 1 },
    { key: 'line_height', label: t('Interlinea'), type: 'range', min: 0.8, max: 1.6, step: 0.02 },
    { key: 'letter_spacing', label: t('Spaziatura lettere (px)'), type: 'range', min: 0, max: 6, step: 0.5 },

    { type: 'separator', label: t('Contenitore') },
    { key: 'radius', label: t('Raggio angoli (px)'), type: 'range', min: 0, max: 40, step: 1 },
    { key: 'padding', label: t('Padding interno (px)'), type: 'range', min: 0, max: 60, step: 2 },

    ...shadowField,
    ...borderFields(),
  ],
};
