import { textEffectsFields, textEffectsDefaults, withHover } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Hero — applica la regola universale Olobuild:
 *
 *   tab CONTENUTO  → fields[]       solo dati testuali (titolo, testi CTA, URL, tag HTML)
 *   tab STILE      → styleFields[]  tutto l'aspetto (colori, tipografia, layout, effetti, CTA stile)
 *                  + styleFieldsBase (sfondo, padding/margin esterno, bordo, ombra, opacità del wrapper)
 *   tab AVANZATE   → meta tecnico (HTML id, CSS class, condizioni, dynamic data)
 *
 * Tutti i campi (sia fields che styleFields) salvano in tile.settings.* — il PHP renderer
 * resta invariato. Solo l'inspector è riorganizzato in due tab logici.
 */
export default {
  type: 'hero',
  name: t('Hero'),
  icon: 'dashicons-cover-image',
  category: 'layout',
  defaults: {
    typography_preset: '',
    preset: 'custom',

    // Contenuto
    title: t('Titolo Provvisorio'),
    subtitle: t('Sottotitolo provvisorio'),

    // ── Scena (unificazione hero, Fase 1) — tutti default no-op: i template
    //    esistenti restano identici finché l'utente non attiva qualcosa. ──
    eyebrow_text: '',
    eyebrow_dot: false,
    media_bg: { type: 'none' },
    overlay_color: '',
    overlay_top: 0,
    overlay_bottom: 0,
    overlay_sides: false,
    glow_on: false,
    glow_color: '',
    glow_w: 760,
    glow_h: 560,
    glow_blur: 100,
    glow_x: 50,
    glow_y: 20,
    arch: false,
    frame_on: false,
    frame_inset: 24,
    watermark_text: '',
    watermark_color: '',
    accent: '',
    meta_text: '',
    scroll_hint: '',

    // ── Modulo sotto il contenuto (unificazione hero, Fase 1) — default '' = no-op ──
    module: '',
    strip_items: [
      { image: '', caption: 'dettaglio — 01' },
      { image: '', caption: 'dettaglio — 02' },
      { image: '', caption: 'dettaglio — 03' },
    ],
    strip_offset: 28,
    strip_radius: 200,
    search_placeholder: t('Cerca…'),
    search_button: t('Cerca'),
    search_url: '',
    search_chips: '',
    mock_mode: 'media',
    mock_media: { type: 'none' },
    mock_label: 'screenshot prodotto — 16/8.5',
    mock_url: 'app.tuoprodotto.com',
    mock_kpis: [
      { label: 'MRR', value: '€48.2k', delta: '+12% sul mese', down: false },
      { label: 'Utenti attivi', value: '3.204', delta: '+8%', down: false },
      { label: 'Churn', value: '1,9%', delta: '−0,4%', down: true },
    ],
    mock_chart_title: 'Revenue',
    mock_chart_meta: 'ultimi 12 mesi',
    mock_bars: [
      { h: 34, label: 'G', alt: false }, { h: 52, label: 'F', alt: false },
      { h: 44, label: 'M', alt: true },  { h: 68, label: 'A', alt: false },
      { h: 58, label: 'M', alt: false }, { h: 80, label: 'G', alt: true },
      { h: 72, label: 'L', alt: false }, { h: 92, label: 'A', alt: false },
    ],
    chat_label: 'workspace',
    chat_messages: [
      { side: 'you', text: 'Riassumi le chiamate della settimana e segnala i temi sul prezzo.' },
      { side: 'ai', text: 'Su 9 chiamate: 3 citano il prezzo — due chiedono la fatturazione annuale. Ho preparato una bozza di follow-up per ciascuna.' },
      { side: 'you', text: 'Perfetto, aggiungile al CRM.' },
    ],

    // Colori
    text_color: '',
    title_color: '',
    subtitle_color: '',

    // Titolo tipografia
    title_tag: 'h1',
    title_font_family: '',
    title_font_size: '',
    title_font_weight: '700',
    title_letter_spacing: '0',
    title_line_height: '1.2',
    title_text_transform: 'none',
    title_text_shadow: '',

    // Sottotitolo tipografia
    subtitle_font_size: '',
    subtitle_font_weight: '400',
    subtitle_letter_spacing: '0',
    subtitle_max_width: '',

    // Layout
    min_height: '500px',
    content_max_width: '700',
    vertical_align: 'center',
    horizontal_align: 'center',
    text_align: 'center',
    tile_padding: { top: 60, right: 20, bottom: 60, left: 20 },

    // CTA Primario
    cta_text: t('Inizia ora'),
    cta_url: '#',
    cta_target: '_self',
    cta_bg_color: '',
    cta_text_color: '',
    cta_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
    cta_size: '15',
    cta_style: 'filled',

    // CTA Secondario
    cta2_text: '',
    cta2_url: '#',
    cta2_target: '_self',
    cta2_bg_color: '',
    cta2_text_color: '',
    cta2_style: 'outline',

    // Effetti testo
    ...textEffectsDefaults,
    text_effect_target: 'all',
  },

  // ─────────────────────────────────────────────────────────────────
  // TAB CONTENUTO — dati testuali, semantica HTML, link
  // ─────────────────────────────────────────────────────────────────
  fields: [
    { key: 'eyebrow_text', label: t('Eyebrow (sopratitolo, vuoto = nascosto)'), type: 'text' },
    { key: 'eyebrow_dot', label: t('Dot prima dell\'eyebrow'), type: 'toggle',
      condition: { field: 'eyebrow_text', op: 'neq', value: '' } },
    { key: 'title', label: t('Titolo'), type: 'text',
      description: t('Le parti in corsivo (em) diventano "parola accento" se imposti il Colore accento nel tab Stile.') },
    { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },

    { type: 'separator', label: t('Scena / media di sfondo') },
    { key: 'media_bg', label: t('Media di scena (immagine, video, gradiente, colore…)'), type: 'background', showParallax: false },

    { type: 'separator', label: t('CTA Primario') },
    { key: 'cta_text',   label: t('Testo pulsante'), type: 'text' },
    { key: 'cta_url',    label: t('URL pulsante'),   type: 'link' },
    { key: 'cta_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self',  label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},

    { type: 'separator', label: t('CTA Secondario') },
    { key: 'cta2_text',   label: t('Testo (vuoto = nascosto)'), type: 'text' },
    { key: 'cta2_url',    label: t('URL'), type: 'link' },
    { key: 'cta2_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self',  label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},

    { type: 'separator', label: t('Modulo sotto il contenuto') },
    { key: 'module', label: t('Modulo'), type: 'select', options: [
      { value: '',       label: t('Nessuno') },
      { value: 'strip',  label: t('Striscia media (tessere)') },
      { value: 'search', label: t('Barra di ricerca + chip') },
      { value: 'mockup', label: t('Mockup prodotto (cornice browser)') },
      { value: 'chat',   label: t('Finestra chat (bolle conversazione)') },
    ], description: t('Un blocco opzionale sotto il messaggio: i campi compaiono solo per il modulo scelto.') },
    { key: 'strip_items', label: t('Tessere media'), type: 'content-items',
      itemLabel: t('Tessera'),
      defaults: { image: '', caption: '' },
      itemFields: [
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'caption', label: t('Didascalia (opzionale)'), type: 'text' },
      ],
      condition: { field: 'module', op: 'eq', value: 'strip' } },
    { key: 'search_placeholder', label: t('Placeholder campo ricerca'), type: 'text',
      condition: { field: 'module', op: 'eq', value: 'search' } },
    { key: 'search_button', label: t('Testo bottone'), type: 'text',
      condition: { field: 'module', op: 'eq', value: 'search' } },
    { key: 'search_url', label: t('URL di ricerca (vuoto = ricerca del sito)'), type: 'link',
      condition: { field: 'module', op: 'eq', value: 'search' } },
    { key: 'search_chips', label: t('Chip categorie (separate da virgola)'), type: 'text',
      condition: { field: 'module', op: 'eq', value: 'search' } },
    { key: 'mock_mode', label: t('Contenuto della cornice'), type: 'select', options: [
      { value: 'media',     label: t('Media (screenshot / video)') },
      { value: 'dashboard', label: t('Dashboard (KPI + grafico)') },
    ], condition: { field: 'module', op: 'eq', value: 'mockup' } },
    { key: 'mock_url', label: t('URL nella barra browser'), type: 'text',
      condition: { field: 'module', op: 'eq', value: 'mockup' } },
    { key: 'mock_media', label: t('Media del mockup (screenshot, video…)'), type: 'background', showParallax: false,
      show: (s) => s.module === 'mockup' && (s.mock_mode || 'media') === 'media' },
    { key: 'mock_label', label: t('Etichetta placeholder'), type: 'text',
      show: (s) => s.module === 'mockup' && (s.mock_mode || 'media') === 'media' },
    { key: 'mock_kpis', label: t('KPI cards'), type: 'content-items',
      itemLabel: t('KPI'),
      defaults: { label: 'KPI', value: '0', delta: '', down: false },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'value', label: t('Valore'), type: 'text' },
        { key: 'delta', label: t('Variazione (es. +12%)'), type: 'text' },
        { key: 'down', label: t('Variazione negativa'), type: 'toggle' },
      ],
      show: (s) => s.module === 'mockup' && s.mock_mode === 'dashboard' },
    { key: 'mock_chart_title', label: t('Titolo grafico'), type: 'text',
      show: (s) => s.module === 'mockup' && s.mock_mode === 'dashboard' },
    { key: 'mock_chart_meta', label: t('Meta grafico (a destra)'), type: 'text',
      show: (s) => s.module === 'mockup' && s.mock_mode === 'dashboard' },
    { key: 'mock_bars', label: t('Barre del grafico'), type: 'content-items',
      itemLabel: t('Barra'),
      defaults: { h: 50, label: '', alt: false },
      itemFields: [
        { key: 'h', label: t('Altezza (%)'), type: 'range', min: 0, max: 100, step: 2 },
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'alt', label: t('Colore alternativo'), type: 'toggle' },
      ],
      show: (s) => s.module === 'mockup' && s.mock_mode === 'dashboard' },
    { key: 'chat_label', label: t('Etichetta finestra'), type: 'text',
      condition: { field: 'module', op: 'eq', value: 'chat' } },
    { key: 'chat_messages', label: t('Messaggi'), type: 'content-items',
      itemLabel: t('Messaggio'),
      defaults: { side: 'you', text: '' },
      itemFields: [
        { key: 'side', label: t('Mittente'), type: 'select', options: [
          { value: 'you', label: t('Tu (a destra, accento)') },
          { value: 'ai',  label: t('Risposta (a sinistra)') },
        ]},
        { key: 'text', label: t('Testo'), type: 'textarea' },
      ],
      condition: { field: 'module', op: 'eq', value: 'chat' } },

    { type: 'separator', label: t('Righe extra (opzionali)') },
    { key: 'meta_text', label: t('Riga meta / data'), type: 'text',
      description: t('Es. "12 September 2026 · Lago di Como". Vuoto = nascosta.') },
    { key: 'scroll_hint', label: t('Hint di scroll'), type: 'text',
      description: t('Es. "Scroll". Vuoto = nascosto.') },
    { key: 'watermark_text', label: t('Watermark ghost (testo gigante)'), type: 'text',
      description: t('Sigla in filigrana dietro il contenuto, stile Verdano. Vuoto = nessuno.') },
  ],

  // ─────────────────────────────────────────────────────────────────
  // TAB STILE — aspetto visivo specifico della hero. Mostrato dal
  // StyleFieldsRenderer PRIMA dei campi style universali del wrapper.
  // ─────────────────────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-centered',   label: t('Modern Centered') },
      { value: 'split-image',       label: t('Split Image') },
      { value: 'minimal-editorial', label: t('Minimal Editorial') },
      { value: 'bold-statement',    label: t('Bold Statement') },
      { value: 'video-cinema',      label: t('Video Cinema') },
      { value: 'glass-overlay',     label: t('Glass Overlay') },
      { value: 'neon-cyberpunk',    label: t('Neon Cyberpunk') },
      { value: 'brutalist-mega',    label: t('Brutalist Mega') },
      { value: 'gradient-aurora',   label: t('Gradient Aurora') },
      { value: 'sticker-collage',   label: t('Sticker Collage') },
      { value: 'retro-poster',      label: t('Retro Poster') },
      { value: 'tilt-parallax',     label: t('Tilt Parallax') },
      // ── Blueprint scena (unificazione hero, Fase 1) ──
      { value: 'editorial-overlay',  label: t('Editorial Overlay (Atelier)') },
      { value: 'masked-arch',        label: t('Masked Arch (Verdano)') },
      { value: 'photo-frame',        label: t('Photo Frame (cornice)') },
      { value: 'glow-statement',     label: t('Glow Statement') },
      { value: 'centered-statement', label: t('Centered Statement') },
      { value: 'custom',            label: t('Personalizzato') },
    ]},

    // ── Scena: velo sopra il media (gradiente 3 zone, stile Atelier/Loft) ──
    { type: 'separator', label: t('Scena — velo (overlay)') },
    { key: 'overlay_top', label: t('Intensità velo in alto'), type: 'range', min: 0, max: 1, step: 0.05,
      description: t('0 su entrambe le intensità = nessun velo.') },
    { key: 'overlay_bottom', label: t('Intensità velo in basso'), type: 'range', min: 0, max: 1, step: 0.05 },
    { key: 'overlay_color', label: t('Colore velo (vuoto = scuro del tema)'), type: 'color' },
    { key: 'overlay_sides', label: t('Velo laterale (stile Atelier)'), type: 'toggle' },

    // ── Scena: glow radiale (stesse chiavi della famiglia glow) ──
    { type: 'separator', label: t('Scena — glow radiale') },
    { key: 'glow_on', label: t('Glow radiale dietro il contenuto'), type: 'toggle' },
    { key: 'glow_color', label: t('Colore glow (vuoto = accento)'), type: 'color',
      condition: { field: 'glow_on', op: 'eq', value: true } },
    { key: 'glow_w', label: t('Larghezza (px)'), type: 'range', min: 100, max: 1600, step: 20,
      condition: { field: 'glow_on', op: 'eq', value: true } },
    { key: 'glow_h', label: t('Altezza (px)'), type: 'range', min: 100, max: 1200, step: 20,
      condition: { field: 'glow_on', op: 'eq', value: true } },
    { key: 'glow_blur', label: t('Sfocatura (px)'), type: 'range', min: 0, max: 200, step: 5,
      condition: { field: 'glow_on', op: 'eq', value: true } },
    { key: 'glow_x', label: t('Posizione X (%)'), type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'glow_on', op: 'eq', value: true } },
    { key: 'glow_y', label: t('Posizione Y (%)'), type: 'range', min: -50, max: 100, step: 1,
      condition: { field: 'glow_on', op: 'eq', value: true } },

    // ── Modulo: regolazioni striscia ──
    { type: 'separator', label: t('Modulo — striscia media'), condition: { field: 'module', op: 'eq', value: 'strip' } },
    { key: 'strip_offset', label: t('Offset verticale 2ª tessera (px)'), type: 'range', min: 0, max: 80, step: 2,
      condition: { field: 'module', op: 'eq', value: 'strip' } },
    { key: 'strip_radius', label: t('Raggio superiore tessere (px)'), type: 'range', min: 0, max: 260, step: 4,
      condition: { field: 'module', op: 'eq', value: 'strip' } },

    // ── Finiture di scena ──
    { type: 'separator', label: t('Finiture') },
    { key: 'arch', label: t('Bordo inferiore ad arco (maschera)'), type: 'toggle' },
    { key: 'frame_on', label: t('Cornice fotografica (media in frame)'), type: 'toggle' },
    { key: 'frame_inset', label: t('Spessore cornice (px)'), type: 'range', min: 8, max: 80, step: 2,
      condition: { field: 'frame_on', op: 'eq', value: true } },
    { key: 'watermark_color', label: t('Colore watermark (vuoto = bianco 6%)'), type: 'color',
      condition: { field: 'watermark_text', op: 'neq', value: '' } },
    // ── Tipografia (unica sezione: titolo, sottotitolo, default + effetti) ──
    { type: 'separator', label: t('Tipografia') },
    { key: 'text_color', label: t('Colore testo (default)'), type: 'color',
      description: t('Default per titolo + sottotitolo, sovrascritto dai colori specifici nei popover qui sotto.') },
    { key: 'accent', label: t('Colore accento'), type: 'color',
      description: t('Colora le parole in corsivo (em) del titolo, il dot eyebrow e il glow di default. Vuoto = nessun accento.') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'lineHeight', 'letterSpacing'],
      keys: {
        tag:           'title_tag',
        family:        'title_font_family',
        size:          'title_font_size',
        weight:        'title_font_weight',
        transform:     'title_text_transform',
        lineHeight:    'title_line_height',
        letterSpacing: 'title_letter_spacing',
        color:         'title_color',
        shadow:        'title_text_shadow',
      },
      sizeMin: 14, sizeMax: 120, sizeStep: 1,
    },
    { type: 'typography', label: t('Sottotitolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'letterSpacing'],
      keys: {
        size:          'subtitle_font_size',
        weight:        'subtitle_font_weight',
        letterSpacing: 'subtitle_letter_spacing',
        color:         'subtitle_color',
      },
      sizeMin: 12, sizeMax: 48, sizeStep: 1,
    },
    { key: 'subtitle_max_width', label: t('Larghezza max sottotitolo (px)'), type: 'range', min: 200, max: 1000, step: 10 },

    // ── Effetti testo (gradient/neon/typewriter/...) ──
    ...textEffectsFields([
      { value: 'title',    label: t('Solo Titolo') },
      { value: 'subtitle', label: t('Solo Sottotitolo') },
      { value: 'all',      label: t('Tutti gli elementi testuali') },
    ]),

    // ── Layout interno ──
    { type: 'separator', label: t('Layout interno') },
    { key: 'min_height',         label: t('Altezza minima'), type: 'unit', units: ['px', 'vh', '%'], placeholder: 'auto' },
    { key: 'content_max_width',  label: t('Larghezza max contenuto (px)'), type: 'range', min: 200, max: 1200, step: 50 },
    { key: 'vertical_align',     label: t('Posizione verticale del blocco'), type: 'select', options: [
      { value: 'top',    label: t('In alto') },
      { value: 'center', label: t('Al centro') },
      { value: 'bottom', label: t('In basso') },
    ]},
    { key: 'horizontal_align',   label: t('Posizione orizzontale del blocco'), type: 'select', options: [
      { value: 'left',   label: t('A sinistra') },
      { value: 'center', label: t('Al centro') },
      { value: 'right',  label: t('A destra') },
    ]},
    { key: 'text_align',         label: t('Allineamento del testo'), type: 'select', options: [
      { value: 'left',   label: t('A sinistra') },
      { value: 'center', label: t('Al centro') },
      { value: 'right',  label: t('A destra') },
    ]},
    { key: 'tile_padding', label: t('Padding del contenuto interno (px)'), type: 'spacing', max: 200 },

    // ── Stile pulsante CTA Primario ──
    { type: 'separator', label: t('Stile CTA Primario') },
    { key: 'cta_style', label: t('Tipo'), type: 'select', options: [
      { value: 'filled',  label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'ghost',   label: t('Trasparente') },
    ]},
    { key: 'cta_size',       label: t('Dimensione testo (px)'), type: 'range', min: 12, max: 24, step: 1 },
    { key: 'cta_bg_color',   label: t('Colore sfondo'), type: 'color' },
    { key: 'cta_text_color', label: t('Colore testo'),  type: 'color' },
    withHover({ key: 'cta_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),

    // ── Stile pulsante CTA Secondario ──
    { type: 'separator', label: t('Stile CTA Secondario') },
    { key: 'cta2_style', label: t('Tipo'), type: 'select', options: [
      { value: 'filled',  label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'ghost',   label: t('Trasparente') },
    ]},
    { key: 'cta2_bg_color',   label: t('Colore sfondo'), type: 'color' },
    { key: 'cta2_text_color', label: t('Colore testo'),  type: 'color' },
  ],
};
