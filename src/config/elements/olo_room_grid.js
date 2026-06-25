
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Grid — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → filtri attivi, configurazione mappa (layout/zoom/auto-fit), paginazione,
 *                   toggle contenuto card (image/gallery/video/excerpt/equipment/price)
 *   styleFields[] → sfondo tile, tipografia, griglia (colonne/gap), card (sfondo/bordo/ombra/hover),
 *                   immagine (altezza/ratio/raggio/effetti), overlay, testo (colori/padding), bordo
 */
export default {
  type: 'olo_room_grid',
  name: t('Sale - Mappa e Lista'),
  icon: 'dashicons-building',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    // Filtri
    visible_filters: 'type,district,capacity,equipment',
    equipment_filter_list: '',
    // Mappa
    layout: 'map-left',
    map_height: '500',
    map_sticky: true,
    tile_layer: 'positron',
    default_zoom: '13',
    fit_bounds: true,
    marker_color: '',
    // Griglia
    columns: '1',
    columns_mobile: '1',
    gap: 'default',
    items_per_page: '8',
    pagination_style: 'numbers',
    // Card contenuto
    card_image: true,
    card_gallery: false,
    card_video: false,
    card_excerpt: true,
    card_equipment: true,
    card_price: true,
    gallery_counter: false,
    gallery_swipe: true,
    // Card sfondo
    card_bg_type: 'color',
    card_bg: '',
    card_bg_gradient_from: '',
    card_bg_gradient_to: '',
    card_bg_gradient_angle: '135',
    card_hover_bg: '',
    // Card bordo
    card_border_width: '1',
    card_border_style: 'solid',
    card_border_color: '',
    card_border_radius: '',
    card_radius: '10',
    card_border_hover: '',
    card_hover_border_width: '',
    card_shadow: 'none',
    card_hover_shadow: '',
    card_hover_lift: true,
    // Immagine
    image_height: '160',
    image_aspect_ratio: '',
    image_object_fit: 'cover',
    image_radius: '0',
    hover_effect: 'none',
    fx_kenburns: false,
    fx_kenburns_speed: '20',
    fx_kenburns_scale: '1.12',
    // Filtri immagine
    fx_vignette: false,
    fx_vignette_strength: '40',
    fx_grain: false,
    fx_grain_opacity: '6',
    fx_tint: false,
    fx_tint_color: '',
    fx_tint_opacity: '10',
    fx_tint_blend: 'multiply',
    // Overlay
    overlay_gradient: false,
    overlay_color: 'var(--olo-color-dark, #16263d)',
    overlay_opacity: '50',
    overlay_direction: 'bottom',
    overlay_height: '50',
    // Testo
    tile_padding: { top: 14, right: 14, bottom: 14, left: 14 },
    title_size: '1',
    title_color: '',
    excerpt_size: '0.85',
    excerpt_color: '',
    meta_color: '',
    tag_bg: '',
    tag_color: '',
    body_bg: '',
    body_bg_opacity: '100',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ═══════════════════════════════════════════
    // 1. Filtri
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Filtri') },
    {
      key: 'visible_filters',
      label: t('Filtri visibili'),
      type: 'multi_pills',
      options: [
        { value: 'type', label: t('Tipo sala'), icon: 'home' },
        { value: 'district', label: t('Zona'), icon: 'location' },
        { value: 'capacity', label: t('Capienza'), icon: 'users' },
        { value: 'equipment', label: t('Dotazioni'), icon: 'star' },
      ],
    },
    {
      key: 'equipment_filter_list',
      label: t('Dotazioni nel filtro'),
      type: 'multi_pills',
      options: [
        { value: 'wifi', label: t('WiFi') },
        { value: 'climatizzazione', label: t('Climatizzazione') },
        { value: 'proiettore', label: t('Proiettore') },
        { value: 'proiettore-hd', label: t('Proiettore HD') },
        { value: 'proiettore-led', label: t('Proiettore LED') },
        { value: 'proiettore-cinematografico', label: t('Proiettore cinematografico') },
        { value: 'schermo-motorizzato', label: t('Schermo motorizzato') },
        { value: 'schermo-a-parete', label: t('Schermo a parete') },
        { value: 'schermo-avvolgibile-grande-formato', label: t('Schermo grande formato') },
        { value: 'lavagna', label: t('Lavagna') },
        { value: 'lavagna-a-fogli-mobili', label: t('Lavagna a fogli mobili') },
        { value: 'lavagna-interattiva', label: t('Lavagna interattiva') },
        { value: 'lavagna-interattiva-multimediale-lim', label: t('LIM') },
        { value: 'microfono-fisso', label: t('Microfono fisso') },
        { value: 'microfono-wireless', label: t('Microfono wireless') },
        { value: 'impianto-audio', label: t('Impianto Audio') },
        { value: 'impianto-audio-con-bluetooth', label: t('Audio Bluetooth') },
        { value: 'impianto-audio-professionale', label: t('Audio professionale') },
        { value: 'amplificazione', label: t('Amplificazione') },
        { value: 'mixer-audio-24-canali', label: t('Mixer audio 24ch') },
        { value: 'registrazione-audio', label: t('Registrazione audio') },
        { value: 'videoconferenza', label: t('Videoconferenza') },
        { value: 'webcam-per-videoconferenze', label: t('Webcam videoconferenze') },
        { value: 'collegamento-streaming', label: t('Streaming') },
        { value: 'postazioni-pc', label: t('Postazioni PC') },
        { value: 'stampante-multifunzione', label: t('Stampante') },
        { value: 'palco', label: t('Palco') },
        { value: 'predisposizione-palco-mobile', label: t('Palco mobile') },
        { value: 'illuminazione-scenografica', label: t('Luci scenografiche') },
        { value: 'luci-palcoscenico-dmx', label: t('Luci palcoscenico DMX') },
        { value: 'illuminazione-museale-led', label: t('Illuminazione museale LED') },
        { value: 'illuminazione-pubblica', label: t('Illuminazione pubblica') },
        { value: 'cabina-regia', label: t('Cabina regia') },
        { value: 'camerini', label: t('Camerini') },
        { value: 'leggio-relatori', label: t('Leggio relatori') },
        { value: 'tavolo-conferenze-modulare', label: t('Tavolo conferenze') },
        { value: 'pianoforte-a-coda', label: t('Pianoforte a coda') },
        { value: 'pianoforte-verticale', label: t('Pianoforte verticale') },
        { value: 'cucina', label: t('Cucina') },
        { value: 'cucina-di-servizio', label: t('Cucina di servizio') },
        { value: 'punto-acqua', label: t('Punto acqua') },
        { value: 'distributore-bevande-calde', label: t('Distributore bevande') },
        { value: 'guardaroba', label: t('Guardaroba') },
        { value: 'anticamera-di-accoglienza', label: t('Anticamera accoglienza') },
        { value: 'loop-induttivo-per-ipoudenti', label: t('Loop ipoudenti') },
        { value: 'allacci-elettrici', label: t('Allacci elettrici') },
        { value: 'prese-elettriche', label: t('Prese elettriche') },
        { value: 'prese-di-corrente-ai-posti', label: t('Prese ai posti') },
        { value: 'ciabatte-multipresa-ai-banchi', label: t('Multipresa ai banchi') },
        { value: 'controllo-umidita', label: t('Controllo umidita') },
        { value: 'videosorveglianza', label: t('Videosorveglianza') },
        { value: 'basi-espositive', label: t('Basi espositive') },
        { value: 'binari-appendimento-quadri', label: t('Binari quadri') },
        { value: 'teche-vetro', label: t('Teche vetro') },
        { value: 'transenne-e-delimitazioni', label: t('Transenne') },
        { value: 'pavimento-sportivo-antiurto', label: t('Pavimento sportivo') },
        { value: 'attrezzi-fitness-base', label: t('Attrezzi fitness') },
        { value: 'specchi-a-parete', label: t('Specchi a parete') },
        { value: 'spogliatoi-con-docce', label: t('Spogliatoi con docce') },
        { value: 'tappetini', label: t('Tappetini') },
      ],
    },
    { type: 'description', description: t('Vuoto = mostra tutte le dotazioni nel filtro') },

    // ═══════════════════════════════════════════
    // 2. Mappa
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Mappa') },
    { key: 'layout', label: t('Posizione mappa'), type: 'select', options: [
      { value: 'map-left', label: t('Mappa a sinistra') },
      { value: 'map-right', label: t('Mappa a destra') },
      { value: 'map-top', label: t('Mappa in alto') },
      { value: 'cards-only', label: t('Nessuna mappa (solo card)') },
    ]},
    { key: 'map_sticky', label: t('Mappa fissa durante lo scroll'), type: 'toggle',
      condition: { field: 'layout', operator: '!=', value: 'cards-only' } },
    { key: 'tile_layer', label: t('Stile mappa'), type: 'select', options: [
      { value: 'osm', label: t('OpenStreetMap') },
      { value: 'positron', label: t('Positron (chiaro)') },
      { value: 'dark', label: t('Dark') },
    ], condition: { field: 'layout', operator: '!=', value: 'cards-only' } },
    { key: 'default_zoom', label: t('Zoom iniziale'), type: 'range', min: 5, max: 18, step: 1,
      condition: { field: 'layout', operator: '!=', value: 'cards-only' } },
    { key: 'fit_bounds', label: t('Auto-zoom sui risultati'), type: 'toggle',
      condition: { field: 'layout', operator: '!=', value: 'cards-only' } },

    // ═══════════════════════════════════════════
    // 3. Paginazione
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Paginazione') },
    { key: 'items_per_page', label: t('Sale per pagina'), type: 'select', options: [
      { value: '4', label: '4' }, { value: '6', label: '6' },
      { value: '8', label: '8' }, { value: '12', label: '12' },
      { value: '16', label: '16' }, { value: '24', label: '24' },
    ]},
    { key: 'pagination_style', label: t('Stile navigazione'), type: 'select', options: [
      { value: 'numbers', label: t('Numeri pagina') },
      { value: 'arrows', label: t('Frecce avanti/indietro') },
      { value: 'loadmore', label: t('Pulsante Carica altro') },
    ]},

    // ═══════════════════════════════════════════
    // 4. Card contenuto
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Card contenuto') },
    { key: 'card_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'card_gallery', label: t('Galleria sfogliabile'), type: 'toggle',
      condition: { field: 'card_image', value: true } },
    { key: 'card_video', label: t('Video nelle card'), type: 'toggle',
      condition: { field: 'card_image', value: true } },
    { key: 'card_excerpt', label: t('Mostra descrizione breve'), type: 'toggle' },
    { key: 'card_equipment', label: t('Mostra dotazioni'), type: 'toggle' },
    { key: 'card_price', label: t('Mostra tariffa'), type: 'toggle' },
    { key: 'gallery_counter', label: t('Contatore foto (2/5)'), type: 'toggle',
      condition: { field: 'card_gallery', value: true } },
    { key: 'gallery_swipe', label: t('Swipe touch su mobile'), type: 'toggle',
      condition: { field: 'card_gallery', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'title_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Estratto'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'excerpt_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Meta'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'meta_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Mappa - aspetto') },
    { key: 'map_height', label: t('Altezza mappa (px)'), type: 'range', min: 300, max: 800, step: 10,
      condition: { field: 'layout', operator: '!=', value: 'cards-only' } },
    { key: 'marker_color', label: t('Colore pin'), type: 'color',
      condition: { field: 'layout', operator: '!=', value: 'cards-only' } },

    // ═══════════════════════════════════════════
    // Griglia
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Griglia') },
    { key: 'columns', label: t('Colonne'), type: 'select', options: [
      { value: '1', label: '1' }, { value: '2', label: '2' }, { value: '3', label: '3' },
    ]},
    { key: 'columns_mobile', label: t('Colonne su mobile'), type: 'select', options: [
      { value: '1', label: '1' }, { value: '2', label: '2' },
    ]},
    { key: 'gap', label: t('Spaziatura tra card'), type: 'select', options: [
      { value: 'collapse', label: t('Nessuna') },
      { value: 'small', label: t('Piccola') },
      { value: 'default', label: t('Standard') },
      { value: 'medium', label: t('Media') },
      { value: 'large', label: t('Grande') },
    ]},

    // ═══════════════════════════════════════════
    // Card sfondo
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Card sfondo') },
    { key: 'card_bg_type', label: t('Tipo sfondo'), type: 'select', options: [
      { value: 'color', label: t('Colore piatto') },
      { value: 'gradient', label: t('Gradiente') },
    ]},
    { key: 'card_bg', label: t('Colore sfondo card'), type: 'color',
      condition: { field: 'card_bg_type', value: 'color' } },
    { key: 'card_bg_gradient_from', label: t('Gradiente da'), type: 'color',
      condition: { field: 'card_bg_type', value: 'gradient' } },
    { key: 'card_bg_gradient_to', label: t('Gradiente a'), type: 'color',
      condition: { field: 'card_bg_type', value: 'gradient' } },
    { key: 'card_bg_gradient_angle', label: t('Angolo gradiente'), type: 'range', min: 0, max: 360, step: 5,
      condition: { field: 'card_bg_type', value: 'gradient' } },
    { key: 'card_hover_bg', label: t('Sfondo card su hover'), type: 'color' },

    // ═══════════════════════════════════════════
    // Card bordo
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Card — Bordo') },
    withHover({ key: 'card_border_width', label: t('Spessore bordo (px)'), type: 'range', min: 0, max: 5, step: 1 }, { hoverKey: 'card_hover_border_width' }),
    { key: 'card_border_style', label: t('Stile bordo'), type: 'select', options: [
      { value: 'solid', label: t('Continuo') },
      { value: 'dashed', label: t('Tratteggiato') },
      { value: 'dotted', label: t('Puntinato') },
      { value: 'none', label: t('Nessuno') },
    ]},
    withHover({ key: 'card_border_color', label: t('Colore bordo'), type: 'color' }, { hoverKey: 'card_border_hover' }),
    withHover({ key: 'card_border_radius', label: t('Raggio angoli (4 valori)'), type: 'border-radius' }),

    { type: 'separator', label: t('Card — Ombra normale') },
    { key: 'card_shadow', label: t('Ombra card'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Forte') },
      { value: 'xl', label: t('Molto forte') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'card_shadow_h', label: t('Offset H (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_v', label: t('Offset V (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_blur', label: t('Sfocatura (px)'), type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_spread', label: t('Espansione (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_color', label: t('Colore ombra'), type: 'color',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_inset', label: t('Ombra interna'), type: 'toggle',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },

    { type: 'separator', label: t('Card — Ombra hover') },
    { key: 'card_hover_shadow', label: t('Ombra card su hover'), type: 'select', options: [
      { value: '', label: t('Predefinita') },
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Forte') },
      { value: 'xl', label: t('Molto forte') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'card_hover_shadow_h', label: t('Offset H hover (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_hover_shadow_v', label: t('Offset V hover (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_hover_shadow_blur', label: t('Sfocatura hover (px)'), type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'card_hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_hover_shadow_spread', label: t('Espansione hover (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_hover_shadow_color', label: t('Colore ombra hover'), type: 'color',
      condition: { field: 'card_hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_hover_shadow_inset', label: t('Ombra interna hover'), type: 'toggle',
      condition: { field: 'card_hover_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_hover_lift', label: t('Sollevamento su hover'), type: 'toggle' },

    // ═══════════════════════════════════════════
    // Immagine
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Immagine') },
    { key: 'image_height', label: t('Altezza immagine (px)'), type: 'range', min: 80, max: 350, step: 10,
      condition: { field: 'image_aspect_ratio', value: '' } },
    { key: 'image_aspect_ratio', label: t('Proporzioni'), type: 'select', options: [
      { value: '', label: t('Altezza fissa (px)') },
      { value: '1/1', label: t('1:1 Quadrato') },
      { value: '4/3', label: t('4:3 Classico') },
      { value: '3/2', label: t('3:2 Foto') },
      { value: '16/9', label: t('16:9 Panoramico') },
      { value: '2/1', label: t('2:1 Ultra-wide') },
    ]},
    { key: 'image_object_fit', label: t('Adattamento immagine'), type: 'select', options: [
      { value: 'cover', label: t('Riempi (cover)') },
      { value: 'contain', label: t('Adatta (contain)') },
      { value: 'fill', label: t('Distorci (fill)') },
    ]},
    withHover({ key: 'image_radius', label: t('Raggio bordo immagine (px)'), type: 'border-radius' }),
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'zoom-rotate', label: t('Zoom + rotazione') },
      { value: 'brightness', label: t('Luminosita') },
      { value: 'desaturate', label: t('Desatura → colore') },
      { value: 'blur-in', label: t('Sfocatura → nitido') },
      { value: 'slide-up', label: t('Scorrimento in alto') },
      { value: 'glow', label: t('Bagliore') },
      { value: 'tilt', label: t('Tilt 3D') },
    ]},
    { key: 'fx_kenburns', label: t('Ken Burns (zoom cinematico)'), type: 'toggle' },
    { key: 'fx_kenburns_speed', label: t('Velocita Ken Burns (s)'), type: 'range', min: 10, max: 40, step: 1,
      condition: { field: 'fx_kenburns', value: true } },
    { key: 'fx_kenburns_scale', label: t('Intensita zoom'), type: 'range', min: 1.05, max: 1.25, step: 0.01,
      condition: { field: 'fx_kenburns', value: true } },

    // ═══════════════════════════════════════════
    // Filtri immagine
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Filtri immagine') },
    { key: 'fx_vignette', label: t('Vignettatura'), type: 'toggle' },
    { key: 'fx_vignette_strength', label: t('Intensita vignettatura (%)'), type: 'range', min: 10, max: 80, step: 5,
      condition: { field: 'fx_vignette', value: true } },
    { key: 'fx_grain', label: t('Grana pellicola'), type: 'toggle' },
    { key: 'fx_grain_opacity', label: t('Opacita grana (%)'), type: 'range', min: 2, max: 20, step: 1,
      condition: { field: 'fx_grain', value: true } },
    { key: 'fx_tint', label: t('Tinta colore'), type: 'toggle' },
    { key: 'fx_tint_color', label: t('Colore tinta'), type: 'color',
      condition: { field: 'fx_tint', value: true } },
    { key: 'fx_tint_opacity', label: t('Opacita tinta (%)'), type: 'range', min: 5, max: 50, step: 5,
      condition: { field: 'fx_tint', value: true } },
    { key: 'fx_tint_blend', label: t('Modo fusione'), type: 'select', options: [
      { value: 'multiply', label: t('Moltiplica') },
      { value: 'overlay', label: t('Sovrapposizione') },
      { value: 'color', label: t('Colore') },
      { value: 'soft-light', label: t('Luce soffusa') },
      { value: 'hard-light', label: t('Luce forte') },
    ], condition: { field: 'fx_tint', value: true } },

    // ═══════════════════════════════════════════
    // Overlay
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Overlay immagine') },
    { key: 'overlay_gradient', label: t('Overlay sfumato'), type: 'toggle' },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color',
      condition: { field: 'overlay_gradient', value: true } },
    { key: 'overlay_opacity', label: t('Opacita overlay (%)'), type: 'range', min: 10, max: 90, step: 5,
      condition: { field: 'overlay_gradient', value: true } },
    { key: 'overlay_direction', label: t('Direzione sfumatura'), type: 'select', options: [
      { value: 'bottom', label: t('Dal basso') },
      { value: 'top', label: "Dall'alto" },
      { value: 'left', label: t('Da sinistra') },
      { value: 'right', label: t('Da destra') },
    ], condition: { field: 'overlay_gradient', value: true } },
    { key: 'overlay_height', label: t('Altezza gradiente (%)'), type: 'range', min: 20, max: 100, step: 5,
      condition: { field: 'overlay_gradient', value: true } },

    // ═══════════════════════════════════════════
    // Testo
    // ═══════════════════════════════════════════
    { type: 'separator', label: t('Stile testo') },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 40 },
    { key: 'title_size', label: t('Dimensione titolo (em)'), type: 'range', min: 0.7, max: 2.5, step: 0.05 },
    { key: 'excerpt_size', label: t('Dimensione estratto (em)'), type: 'range', min: 0.7, max: 1.5, step: 0.05 },
    { key: 'tag_bg', label: t('Sfondo pill dotazioni'), type: 'color' },
    { key: 'tag_color', label: t('Testo pill dotazioni'), type: 'color' },
    { key: 'body_bg', label: t('Sfondo area testo'), type: 'color' },
    { key: 'body_bg_opacity', label: t('Opacita sfondo (%)'), type: 'range', min: 0, max: 100, step: 5,
      condition: { field: 'body_bg', operator: '!=', value: '' } },
    ...borderFields(),
  ],
};
