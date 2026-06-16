import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Timeline "SUPER" — redesign (handoff "OLObuild - Tile Timeline SUPER").
 *
 * Riscrittura del vecchio EventList in una timeline ricca e combinabile su
 * 10 dimensioni: Layout · Ingresso · Tema · Card · Filo · Nodo · Colore · Media ·
 * Densità · Stato. Stesse sorgenti dati (items), chiavi tl_* additive.
 *
 * Migrazione (resa PHP/Vue): legge le vecchie chiavi come fallback —
 *   layout(vertical-center→alt, vertical-left/right→one, horizontal→horizontal),
 *   marker_type(dot→dot, icon→icon, number→num), line_style/line_progress→thread/line.
 * Gli items vecchi (title/description/date/image/video/icon/icon_color) restano validi;
 * `tag` e `category` sono additivi (default sensati).
 *
 * Colori categoria dai ruoli globali del cliente:
 *   primary · secondary · accent · success (con override per-item via icon_color).
 *   Modalità "mono" forza tutto sul primario.
 */
export default {
  type: 'timeline',
  name: t('Timeline'),
  icon: 'dashicons-backup',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',

    // ── Items ──
    items: [
      { id: 'tl-1', title: t('Prima riga di codice'), tag: t('Fondazione'), description: t('Il prototipo del builder: drag-and-drop nativo in WordPress, senza shortcode.'), date: '2019', image: '', video: '', icon: 'star', category: 'primary', icon_color: '' },
      { id: 'tl-2', title: t('Libreria tile v1'), tag: t('Prodotto'), description: t('40 tile native e il sistema di colori globali. Pubblicati i primi mille siti.'), date: '2021', image: '', video: '', icon: 'grid', category: 'accent', icon_color: '' },
      { id: 'tl-3', title: t('Aurora, bagliori, animazioni'), tag: t('Effetti'), description: t('Sfondi generativi e transizioni native. La libreria supera le 150 tile.'), date: '2023', image: '', video: '', icon: 'star', category: 'success', icon_color: '' },
      { id: 'tl-4', title: t('Linguaggio "bello & coerente"'), tag: t('Sistema'), description: t('Token globali, controlli inspector e tile allineati a un\'unica grammatica.'), date: '2025', image: '', video: '', icon: 'settings', category: 'secondary', icon_color: '' },
      { id: 'tl-5', title: t('240 tile, un solo standard'), tag: t('Futuro'), description: t('Ogni categoria curata, ogni controllo coerente. La libreria completa.'), date: '2026', image: '', video: '', icon: 'flag', category: 'primary', icon_color: '' },
    ],

    // ── 10 dimensioni SUPER ──
    tl_layout: 'alt',       // alt · one · horizontal · navigator
    tl_reveal: 'sides',     // sides · bloom · unroll · slot
    tl_theme: 'paper',      // paper · night · neon · blue
    tl_card: 'bubble',      // bubble · glass · polaroid · ticket
    tl_thread: 'solid2',    // solid2 · dash · dot · comet
    tl_node: 'icon',        // icon · dot · num · year
    tl_color: 'cat',        // cat · mono
    tl_media: 'on',         // on · off
    tl_density: 'comfy',    // comfy · compact
    tl_line: 'scroll',      // scroll (roadmap) · solid (statico)
    tl_transparent: false,  // sfondo blocco trasparente (ignora il bg del tema)

    // ── Personalizzazione (override; '' o 0 = usa il default della variante/tema) ──
    tl_rail_color: '', tl_rail_w: 0, tl_fill_from: '', tl_fill_to: '',
    tl_node_size: 0, tl_node_border: 0,
    tl_card_bg: '', tl_card_radius: 0, tl_card_maxw: 0, tl_card_pad: 0,
    tl_media_ratio: 'auto', tl_media_h: 0, tl_media_fit: 'cover', object_position: 'center center', tl_media_radius: 0, tl_media_bar: true,
    tl_title_size: 0, tl_title_weight: '', tl_title_color: '', tl_title_family: '',
    tl_text_size: 0, tl_text_color: '', tl_text_lh: 0, tl_text_align: 'left', tl_text_family: '',
    tl_yr_size: 0, tl_yr_color: '', tl_yr_family: '',
    tl_show_tag: true, tl_tag_color: '',

    // Opzioni orizzontale
    h_card_width: '268',

    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── Eventi ──
    { type: 'separator', label: t('Eventi') },
    { key: 'items', label: t('Eventi'), type: 'content-items',
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'tag', label: t('Etichetta / fase'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
        { key: 'date', label: t('Data / anno'), type: 'text' },
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'video', label: t('Video'), type: 'media' },
        { key: 'icon', label: t('Icona nodo'), type: 'icon' },
        { key: 'category', label: t('Categoria (colore)'), type: 'select', options: [
          { value: 'primary',   label: t('Primario') },
          { value: 'secondary', label: t('Secondario') },
          { value: 'accent',    label: t('Accento') },
          { value: 'success',   label: t('Successo') },
          { value: 'warning',   label: t('Avviso') },
          { value: 'info',      label: t('Info') },
        ]},
        { key: 'icon_color', label: t('Colore override'), type: 'color' },
      ],
      newItemDefaults: { title: t('Nuovo evento'), tag: t('Tappa'), description: t('Descrizione evento.'), date: '', image: '', video: '', icon: 'star', category: 'primary', icon_color: '' },
      itemLabel: 'Evento',
    },

    // ── Layout ──
    { type: 'separator', label: t('Layout') },
    { key: 'tl_layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'alt',        label: t('Alternato') },
      { value: 'one',        label: t('Una colonna') },
      { value: 'schedule',   label: t('Scaletta (orario a sinistra)') },
      { value: 'horizontal', label: t('Orizzontale') },
      { value: 'navigator',  label: t('Navigatore (asse date)') },
    ]},

    // ── Nodo / Colore ──
    { type: 'separator', label: t('Nodo') },
    { key: 'tl_node', label: t('Nodo'), type: 'select', options: [
      { value: 'icon', label: t('Icona') },
      { value: 'dot',  label: t('Punto') },
      { value: 'num',  label: t('Numero') },
      { value: 'year', label: t('Anno') },
    ]},
    { key: 'tl_color', label: t('Colore'), type: 'select', options: [
      { value: 'cat',  label: t('Per categoria') },
      { value: 'mono', label: t('Mono (primario)') },
    ]},

    // ── Media / Densità ──
    { type: 'separator', label: t('Contenuto') },
    { key: 'tl_media', label: t('Media nelle card'), type: 'select', options: [
      { value: 'on',  label: t('Immagini') },
      { value: 'off', label: t('Solo testo') },
    ]},
    { key: 'tl_density', label: t('Densità'), type: 'select', options: [
      { value: 'comfy',   label: t('Comoda') },
      { value: 'compact', label: t('Compatta') },
    ]},

    // ── Stato linea / Ingresso ──
    { type: 'separator', label: t('Animazione') },
    { key: 'tl_line', label: t('Filo + stato'), type: 'select', options: [
      { value: 'scroll', label: t('Scroll + roadmap (Fatto/In corso/In arrivo)') },
      { value: 'solid',  label: t('Statico') },
    ]},
    { key: 'tl_reveal', label: t('Ingresso card'), type: 'select', options: [
      { value: 'sides',   label: t('Lati') },
      { value: 'bloom',   label: t('Sboccia') },
      { value: 'unroll',  label: t('Srotola') },
      { value: 'slot',    label: t('Scatto') },
      { value: 'flip',    label: t('Flip') },
      { value: 'zoom',    label: t('Zoom') },
      { value: 'tendina', label: t('Tendina') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    // ── Tema ──
    { type: 'separator', label: t('Tema') },
    { key: 'tl_theme', label: t('Tema'), type: 'select', options: [
      { value: 'paper', label: t('Carta (chiaro)') },
      { value: 'night', label: t('Notte') },
      { value: 'neon',  label: t('Neon') },
      { value: 'blue',  label: t('Blueprint') },
    ]},
    { key: 'tl_transparent', label: t('Sfondo blocco trasparente'), type: 'toggle' },

    // ── Card ──
    { type: 'separator', label: t('Card') },
    { key: 'tl_card', label: t('Stile card'), type: 'select', options: [
      { value: 'bubble',   label: t('Fumetto') },
      { value: 'glass',    label: t('Vetro') },
      { value: 'polaroid', label: t('Polaroid') },
      { value: 'ticket',   label: t('Ticket') },
    ]},

    // ── Filo ──
    { type: 'separator', label: t('Filo') },
    { key: 'tl_thread', label: t('Filo'), type: 'select', options: [
      { value: 'solid2', label: t('Pieno') },
      { value: 'dash',   label: t('Tratteggio') },
      { value: 'dot',    label: t('Punti') },
      { value: 'comet',  label: t('Comet') },
    ]},

    // ── Orizzontale ──
    { type: 'separator', label: t('Opzioni orizzontale'),
      condition: { field: 'tl_layout', value: 'horizontal' } },
    { key: 'h_card_width', label: t('Larghezza card (px)'), type: 'range', min: 200, max: 420, step: 4,
      condition: { field: 'tl_layout', value: 'horizontal' } },

    // ═══════════ PERSONALIZZAZIONE (override fine; 0/vuoto = default variante) ═══════════
    // ── Filo ──
    { type: 'separator', label: t('Personalizza · Filo') },
    { key: 'tl_rail_color', label: t('Colore filo (statico)'), type: 'color' },
    { key: 'tl_rail_w', label: t('Spessore filo (px · 0 = auto)'), type: 'range', min: 0, max: 12, step: 1 },
    { key: 'tl_fill_from', label: t('Riempimento — da'), type: 'color' },
    { key: 'tl_fill_to', label: t('Riempimento — a'), type: 'color' },

    // ── Nodo ──
    { type: 'separator', label: t('Personalizza · Nodo') },
    { key: 'tl_node_size', label: t('Dimensione nodo (px · 0 = auto)'), type: 'range', min: 0, max: 64, step: 2 },
    { key: 'tl_node_border', label: t('Spessore bordo nodo (px · 0 = auto)'), type: 'range', min: 0, max: 6, step: 1 },

    // ── Card ──
    { type: 'separator', label: t('Personalizza · Card') },
    { key: 'tl_card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'tl_card_radius', label: t('Arrotondamento card (px · 0 = auto)'), type: 'border-radius' },
    { key: 'tl_card_maxw', label: t('Larghezza max card (px · 0 = auto)'), type: 'range', min: 0, max: 600, step: 10 },
    { key: 'tl_card_pad', label: t('Padding card (px · 0 = auto)'), type: 'range', min: 0, max: 32, step: 1 },

    // ── Immagine ──
    { type: 'separator', label: t('Personalizza · Immagine') },
    { key: 'tl_media_ratio', label: t('Proporzioni'), type: 'select', options: [
      { value: 'auto',  label: t('Auto (usa altezza)') },
      { value: '16/9',  label: '16:9' },
      { value: '4/3',   label: '4:3' },
      { value: '3/2',   label: '3:2' },
      { value: '1/1',   label: '1:1 (quadrata)' },
      { value: '21/9',  label: '21:9 (panoramica)' },
    ]},
    { key: 'tl_media_h', label: t('Altezza immagine (px · 0 = auto)'), type: 'range', min: 0, max: 420, step: 4,
      condition: { field: 'tl_media_ratio', value: 'auto' } },
    { key: 'tl_media_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover',   label: t('Riempi (cover)') },
      { value: 'contain', label: t('Contieni (contain)') },
    ]},
    // Punto focale globale: applicato a OGNI immagine/video della timeline. L'immagine è
    // per-item (items[].image) → niente `src` nei contextKeys (il pad degrada a neutro);
    // fit/ratio sono chiavi tile-level reali.
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true,
      contextKeys: { fit: 'tl_media_fit', ratio: 'tl_media_ratio' } },
    { key: 'tl_media_radius', label: t('Arrotondamento immagine (px · 0 = auto)'), type: 'border-radius' },
    { key: 'tl_media_bar', label: t('Barra colore sopra immagine'), type: 'toggle' },

    // ── Testi (popup tipografia — convenzione OLObuild) ──
    { type: 'separator', label: t('Personalizza · Testi') },
    { type: 'typography', label: t('Titolo'),
      keys: { family: 'tl_title_family', size: 'tl_title_size', weight: 'tl_title_weight', color: 'tl_title_color' },
      sizeMin: 10, sizeMax: 48, sizeStep: 1 },
    { type: 'typography', label: t('Descrizione'),
      keys: { family: 'tl_text_family', size: 'tl_text_size', color: 'tl_text_color', lineHeight: 'tl_text_lh' },
      sizeMin: 10, sizeMax: 28, sizeStep: 1 },
    { type: 'typography', label: t('Data / anno'),
      keys: { family: 'tl_yr_family', size: 'tl_yr_size', color: 'tl_yr_color' },
      sizeMin: 12, sizeMax: 64, sizeStep: 1 },
    { key: 'tl_text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},
    { key: 'tl_show_tag', label: t('Mostra etichetta (pill)'), type: 'toggle' },
    { key: 'tl_tag_color', label: t('Colore etichetta'), type: 'color' },

    ...borderFields(),
  ],
};
