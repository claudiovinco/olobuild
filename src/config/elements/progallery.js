import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Pro Gallery — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → media items (gallery), preview video, layout schema/family,
 *                   colonne/righe responsive, autoplay, lightbox toggles/animation,
 *                   visibilità frecce/dots/caption
 *   styleFields[] → preset, sfondo, tipografia, gap, dimensioni visive, raggio,
 *                   colori frecce/dots/overlay, durate animazioni, hover effects,
 *                   filtri immagine, cornice, bordi animati, ombra, bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'progallery',
  name: t('Pro Gallery'),
  icon: 'dashicons-images-alt2',
  category: 'media',

  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    images: [],
    video_preview: 'poster',
    // Layout
    layout: 'grid',
    layout_family: 'classic',
    puzzle_style: 'classic',
    columns: '3',
    gap: '8',
    img_height: '250px',
    object_fit: 'cover',
    thumb_radius: '8',
    rows: '0',
    mobile_columns: '2',
    expand_ratio: '4',
    expand_shrink: '0.5',
    expand_speed: '500',
    parallax_height: '1500',
    parallax_intensity: '50',
    drift_height: '1200',
    drift_intensity: '60',
    drift_rotation: '12',
    cascade_spread: '60',
    cascade_overlap: '40',
    cascade_rotation: '8',
    metro_cell_height: '200',
    filmstrip_item_width: '280',
    filmstrip_center_zoom: '1.15',
    filmstrip_side_tilt: '35',
    filmstrip_autoplay: false,
    filmstrip_speed: '4',
    filmstrip_dots: 'dots',
    filmstrip_dots_color: '',
    // Strip (nastro)
    strip_arrows: false,
    strip_arrows_style: 'chevron',
    strip_arrows_size: '36',
    strip_arrows_color: '',
    strip_arrows_bg: '',
    strip_height: '280',
    strip_item_width: '300',
    strip_rows: '2',
    strip_speed: '30',
    strip_pause_hover: true,
    strip_direction: 'left',
    strip_fade_edges: true,
    // Entrance
    entrance: 'none',
    entrance_stagger: '120',
    entrance_duration: '600',
    // Hover
    hover_effect: 'zoom',
    hover_zoom_scale: '1.08',
    hover_tilt_angle: '10',
    hover_magnetic_strength: '24',
    hover_glow_color: '',
    hover_glow_spread: '20',
    hover_frame_in: false,
    hover_caption: 'none',
    hover_caption_bg: 'rgba(0,0,0,0.6)',
    hover_caption_color: '',
    hover_caption_weight: '700',
    hover_frame_inset: '10',
    // Continuous
    continuous: '',
    continuous_speed: '20',
    // Filter
    filter: 'none',
    filter_hover_restore: false,
    duotone_dark: '',
    duotone_light: '',
    duotone_intensity: '80',
    // Frame
    frame: 'none',
    frame_color: '',
    frame_inset_padding: '10',
    // Animated borders
    anim_border: 'none',
    anim_border_color: '',
    anim_border_thickness: '2',
    anim_border_inset: '20',
    anim_border_speed: '3',
    // Lightbox
    lightbox: true,
    lightbox_animation: 'slide',
    lightbox_thumbs: 'none',
    lightbox_thumbs_rows: '1',
    show_caption: false,
    // "+N" overlay
    more_bg: 'rgba(0,0,0,0.55)',
    more_color: '',
    more_size: '28',
    // Avanzato
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ─── Media ───
    { key: 'images', label: t('Media'), type: 'gallery' },
    { type: 'separator', label: t('Video'), show: s => s.images?.some(i => i.type === 'video') },
    { key: 'video_preview', label: t('Preview video'), type: 'select', options: [
      { value: 'poster', label: t('Poster statico') },
      { value: 'autoplay', label: t('Autoplay muted') },
    ], show: s => s.images?.some(i => i.type === 'video') },

    // ─── Layout (struttura schema) ───
    { type: 'separator', label: t('Layout') },
    { key: 'layout_family', label: t('Famiglia'), type: 'select', options: [
      { value: 'classic', label: t('Classica') },
      { value: 'strip', label: t('Nastro') },
    ]},
    { key: 'layout', label: t('Schema'), type: 'select', optionsFn: s => {
      let family = s.layout_family || 'classic';
      if (family === 'classic') {
        const lay = s.layout || 'grid';
        if (lay.startsWith('strip') || lay === 'filmstrip') family = 'strip';
      }
      if (family === 'strip') return [
        { value: 'strip', label: t('Nastro') },
        { value: 'strip_collage', label: t('Nastro collage') },
        { value: 'strip_multi', label: t('Nastro multi-riga') },
        { value: 'strip_marquee', label: t('Nastro automatico') },
        { value: 'strip_split', label: t('Nastro doppio') },
        { value: 'strip_coverflow', label: t('Coverflow 3D') },
      ];
      return [
        { value: 'grid', label: t('Griglia') },
        { value: 'justified', label: t('Giustificato') },
        { value: 'masonry', label: t('Masonry') },
        { value: 'scattered', label: t('Sparso') },
        { value: 'collage', label: t('Collage') },
        { value: 'mosaic', label: t('Mosaico') },
        { value: 'honeycomb', label: t('Esagoni') },
        { value: 'hexgrid', label: t('Esagoni incastro') },
        { value: 'puzzle', label: t('Puzzle') },
        { value: 'diagonal', label: t('Diagonale') },
        { value: 'parallax', label: t('Parallasse') },
        { value: 'drift', label: t('Deriva (multi-dir)') },
        { value: 'cascade', label: t('Cascata (sovrapposti)') },
        { value: 'metro', label: t('Metro (dimensioni miste)') },
        { value: 'expand', label: t('Espandi (spotlight)') },
      ];
    }},
    { key: 'puzzle_style', label: t('Stile puzzle'), type: 'select', options: [
      { value: 'classic', label: t('Classico') },
      { value: 'zigzag', label: t('Zigzag') },
      { value: 'wave', label: t('Onda') },
      { value: 'castle', label: t('Castello') },
      { value: 'fir', label: t('Abeti') },
    ], show: s => s.layout === 'puzzle' },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 2, max: 6, step: 1,
      show: s => !(s.layout && s.layout.startsWith('strip')) },
    { key: 'rows', label: t('Righe visibili (0 = tutte)'), type: 'range', min: 0, max: 5, step: 1,
      show: s => !(s.layout && s.layout.startsWith('strip')) },
    { key: 'mobile_columns', label: t('Colonne mobile'), type: 'range', min: 1, max: 4, step: 1,
      show: s => !(s.layout && s.layout.startsWith('strip')) },

    // ─── Coverflow / Filmstrip behavior ───
    { type: 'separator', label: t('Coverflow 3D'),
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_autoplay', label: t('Auto-avanzamento'), type: 'toggle',
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_speed', label: t('Intervallo (s)'), type: 'range', min: 2, max: 8, step: 0.5,
      show: s => (s.layout === 'strip_coverflow' || s.layout === 'filmstrip') && !!s.filmstrip_autoplay },
    { key: 'filmstrip_dots', label: t('Indicatore posizione'), type: 'select', options: [
      { value: 'dots', label: t('Pallini') },
      { value: 'lines', label: t('Linee') },
      { value: 'progress', label: t('Barra progresso') },
      { value: 'fraction', label: t('Frazione (3/12)') },
      { value: 'none', label: t('Nessuno') },
    ], show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },

    // ─── Nastro behavior ───
    { type: 'separator', label: t('Nastro'),
      show: s => s.layout && s.layout.startsWith('strip') },
    { key: 'strip_pause_hover', label: t('Pausa al passaggio mouse'), type: 'toggle',
      show: s => s.layout === 'strip_marquee' || s.layout === 'strip_split' },
    { key: 'strip_direction', label: t('Direzione'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ], show: s => s.layout === 'strip_marquee' },
    { key: 'strip_arrows', label: t('Frecce navigazione'), type: 'toggle',
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' },

    // ─── Lightbox (comportamento) ───
    { type: 'separator', label: t('Lightbox') },
    { key: 'lightbox', label: t('Attiva lightbox'), type: 'toggle' },
    { key: 'lightbox_animation', label: t('Animazione'), type: 'select', options: [
      { value: 'slide', label: t('Scorrimento') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'scale', label: t('Scala') },
    ], show: s => !!s.lightbox && s.lightbox_thumbs === 'none' },
    { key: 'lightbox_thumbs', label: t('Miniature lightbox'), type: 'select', options: [
      { value: 'none', label: t('Nessuna (UIKit)') },
      { value: 'bottom', label: t('Sotto') },
      { value: 'right', label: t('Destra') },
      { value: 'left', label: t('Sinistra') },
    ], show: s => !!s.lightbox },
    { key: 'lightbox_thumbs_rows', label: t('Righe/Colonne miniature'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
    ], show: s => !!s.lightbox && s.lightbox_thumbs && s.lightbox_thumbs !== 'none' },
    { key: 'show_caption', label: t('Mostra didascalie'), type: 'toggle',
      show: s => !!s.lightbox },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    // ─── Preset ───
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-grid',     label: t('Modern Grid') },
      { value: 'editorial-mosaic', label: t('Editorial Mosaic') },
      { value: 'cinema-strip',    label: t('Cinema Strip') },
      { value: 'bento-pro',       label: t('Bento Pro') },
      { value: 'spotlight-expand', label: t('Spotlight Expand') },
      { value: 'glass-collage',   label: t('Glass Collage') },
      { value: 'neon-honeycomb',  label: t('Neon Honeycomb') },
      { value: 'brutalist-mosaic', label: t('Brutalist Mosaic') },
      { value: 'parallax-drift',  label: t('Parallax Drift') },
      { value: 'sticker-cascade', label: t('Sticker Cascade') },
      { value: 'vhs-coverflow',   label: t('VHS Coverflow') },
      { value: 'tilt-puzzle',     label: t('3D Tilt Puzzle') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    // ─── Dimensioni visive ───
    { type: 'separator', label: t('Dimensioni') },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 24, step: 2 },
    // type 'unit': stessa stringa CSS salvata del vecchio text ('250px', 'auto' resta editabile raw)
    { key: 'img_height', label: t('Altezza immagine'), type: 'unit', units: ['px', 'vh'], min: 0,
      show: s => !(s.layout && s.layout.startsWith('strip')) },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover', label: t('Riempi') },
      { value: 'contain', label: t('Contieni') },
    ]},
    withHover({ key: 'thumb_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),

    // ─── Espandi ───
    { type: 'separator', label: t('Layout — Espandi (spotlight)'), show: s => s.layout === 'expand' },
    { key: 'expand_ratio', label: t('Rapporto espansione'), type: 'range', min: 2, max: 6, step: 0.5,
      show: s => s.layout === 'expand' },
    { key: 'expand_shrink', label: t('Compressione altri'), type: 'range', min: 0.2, max: 1, step: 0.1,
      show: s => s.layout === 'expand' },
    { key: 'expand_speed', label: t('Velocità (ms)'), type: 'range', min: 200, max: 1000, step: 50,
      show: s => s.layout === 'expand' },

    // ─── Parallasse ───
    { type: 'separator', label: t('Layout — Parallasse'), show: s => s.layout === 'parallax' },
    { key: 'parallax_height', label: t('Altezza area (px)'), type: 'range', min: 800, max: 3000, step: 100,
      show: s => s.layout === 'parallax' },
    { key: 'parallax_intensity', label: t('Intensità parallasse'), type: 'range', min: 10, max: 100, step: 5,
      show: s => s.layout === 'parallax' },

    // ─── Deriva ───
    { type: 'separator', label: t('Layout — Deriva'), show: s => s.layout === 'drift' },
    { key: 'drift_height', label: t('Altezza area (px)'), type: 'range', min: 600, max: 2500, step: 100,
      show: s => s.layout === 'drift' },
    { key: 'drift_intensity', label: t('Intensità movimento'), type: 'range', min: 10, max: 100, step: 5,
      show: s => s.layout === 'drift' },
    { key: 'drift_rotation', label: t('Rotazione max (deg)'), type: 'range', min: 0, max: 25, step: 1,
      show: s => s.layout === 'drift' },

    // ─── Cascata ───
    { type: 'separator', label: t('Layout — Cascata'), show: s => s.layout === 'cascade' },
    { key: 'cascade_spread', label: t('Distanza separazione'), type: 'range', min: 20, max: 100, step: 5,
      show: s => s.layout === 'cascade' },
    { key: 'cascade_overlap', label: t('Sovrapposizione iniziale (%)'), type: 'range', min: 10, max: 80, step: 5,
      show: s => s.layout === 'cascade' },
    { key: 'cascade_rotation', label: t('Rotazione carte (deg)'), type: 'range', min: 0, max: 20, step: 1,
      show: s => s.layout === 'cascade' },

    // ─── Metro ───
    { type: 'separator', label: t('Layout — Metro'), show: s => s.layout === 'metro' },
    { key: 'metro_cell_height', label: t('Altezza cella (px)'), type: 'range', min: 100, max: 400, step: 10,
      show: s => s.layout === 'metro' },

    // ─── Coverflow 3D visivo ───
    { type: 'separator', label: t('Coverflow 3D — aspetto'),
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_item_width', label: t('Larghezza foto (px)'), type: 'range', min: 180, max: 450, step: 10,
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_center_zoom', label: t('Zoom centro'), type: 'range', min: 1.0, max: 1.5, step: 0.05,
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_side_tilt', label: t('Rotazione 3D laterali (deg)'), type: 'range', min: 0, max: 60, step: 1,
      show: s => s.layout === 'strip_coverflow' || s.layout === 'filmstrip' },
    { key: 'filmstrip_dots_color', label: t('Colore indicatore'), type: 'color',
      show: s => (s.layout === 'strip_coverflow' || s.layout === 'filmstrip') && s.filmstrip_dots && s.filmstrip_dots !== 'none' },

    // ─── Nastro aspetto ───
    { type: 'separator', label: t('Nastro — aspetto'),
      show: s => s.layout && s.layout.startsWith('strip') },
    { key: 'strip_height', label: t('Altezza nastro (px)'), type: 'range', min: 150, max: 500, step: 10,
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' },
    { key: 'strip_item_width', label: t('Larghezza foto (px)'), type: 'range', min: 150, max: 500, step: 10,
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_multi' && s.layout !== 'strip_coverflow' },
    { key: 'strip_rows', label: t('Righe'), type: 'range', min: 2, max: 3, step: 1,
      show: s => s.layout === 'strip_multi' },
    { key: 'strip_fade_edges', label: t('Sfumatura bordi'), type: 'toggle',
      show: s => s.layout && s.layout.startsWith('strip') },
    { key: 'strip_speed', label: t('Durata ciclo (s)'), type: 'range', min: 10, max: 60, step: 2,
      show: s => s.layout === 'strip_marquee' || s.layout === 'strip_split' },
    { key: 'strip_arrows_style', label: t('Stile frecce'), type: 'select', options: [
      { value: 'chevron', label: t('Chevron') },
      { value: 'arrow', label: t('Freccia') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'pill', label: t('Pillola') },
      { value: 'minimal', label: t('Minimale') },
    ], show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },
    { key: 'strip_arrows_size', label: t('Dimensione frecce (px)'), type: 'range', min: 24, max: 60, step: 2,
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },
    { key: 'strip_arrows_color', label: t('Colore frecce'), type: 'color',
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },
    { key: 'strip_arrows_bg', label: t('Sfondo frecce'), type: 'color',
      show: s => s.layout && s.layout.startsWith('strip') && s.layout !== 'strip_coverflow' && !!s.strip_arrows },

    // ─── Entrance ───
    { type: 'separator', label: t('Animazione ingresso (solo frontend)') },
    { key: 'entrance', label: t('Effetto'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'fade-up', label: t('Fade su') },
      { value: 'fade-scale', label: t('Fade + Scala') },
      { value: 'flip', label: t('Flip') },
      { value: 'slide-in', label: t('Scorrimento') },
      { value: 'blur-in', label: t('Sfocatura') },
      { value: 'split-sides', label: t('Lati alterni') },
      { value: 'fall', label: t('Caduta (bounce)') },
      { value: 'wind', label: t('Soffio di vento') },
      { value: 'zoom-center', label: t('Zoom dal centro') },
      { value: 'land', label: t('Atterraggio 3D') },
    ]},
    { key: 'entrance_stagger', label: t('Stagger (ms)'), type: 'range', min: 80, max: 400, step: 20,
      show: s => s.entrance && s.entrance !== 'none' },
    { key: 'entrance_duration', label: t('Durata (ms)'), type: 'range', min: 300, max: 1200, step: 50,
      show: s => s.entrance && s.entrance !== 'none' },

    // ─── Hover ───
    { type: 'separator', label: t('Effetti hover') },
    { key: 'hover_effect', label: t('Effetto'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'lift', label: t('Sollevamento') },
      { value: 'tilt3d', label: t('Tilt 3D') },
      { value: 'glow', label: t('Bagliore') },
      { value: 'blur-peers', label: t('Sfoca gli altri') },
      { value: 'magnetic', label: t('Magnetico') },
    ]},
    { key: 'hover_zoom_scale', label: t('Intensità zoom'), type: 'range', min: 1.05, max: 1.30, step: 0.01,
      show: s => s.hover_effect === 'zoom' },
    { key: 'hover_tilt_angle', label: t('Angolo tilt (deg)'), type: 'range', min: 5, max: 20, step: 1,
      show: s => s.hover_effect === 'tilt3d' },
    { key: 'hover_magnetic_strength', label: t('Intensità magnetismo'), type: 'range', min: 8, max: 60, step: 2,
      show: s => s.hover_effect === 'magnetic' },
    { key: 'hover_glow_color', label: t('Colore bagliore'), type: 'color',
      show: s => s.hover_effect === 'glow' },
    { key: 'hover_glow_spread', label: t('Intensità bagliore (px)'), type: 'range', min: 8, max: 50, step: 2,
      show: s => s.hover_effect === 'glow' },
    { key: 'hover_caption', label: t('Didascalia hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'slide-up', label: t('Scorrimento dal basso') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'overlay', label: t('Overlay pieno') },
      { value: 'frame', label: t('Cornice elegante') },
      { value: 'centered', label: t('Testo centrato') },
    ]},
    { key: 'hover_caption_bg', label: t('Sfondo didascalia'), type: 'color',
      show: s => s.hover_caption && s.hover_caption !== 'none' && s.hover_caption !== 'centered' },
    { key: 'hover_caption_color', label: t('Colore testo didascalia'), type: 'color',
      show: s => s.hover_caption && s.hover_caption !== 'none' },
    { key: 'hover_caption_weight', label: t('Peso testo'), type: 'select', options: [
      { value: '400', label: t('Normale') },
      { value: '600', label: t('Semi-bold') },
      { value: '700', label: t('Bold') },
      { value: '900', label: t('Extra bold') },
    ], show: s => s.hover_caption === 'centered' },
    { key: 'hover_frame_inset', label: t('Padding cornice (px)'), type: 'spacing', max: 40,
      show: s => s.hover_caption === 'frame' },

    // ─── Animazione continua ───
    { type: 'separator', label: t('Animazione continua') },
    { key: 'continuous', label: t('Effetti'), type: 'multi_pills', options: [
      { value: 'float', label: t('Galleggiamento') },
      { value: 'drift', label: t('Deriva') },
      { value: 'breathe', label: t('Respiro') },
      { value: 'rotate-slow', label: t('Rotazione lenta') },
      { value: 'kenburns', label: t('Ken Burns') },
      { value: 'shimmer', label: t('Shimmer') },
    ]},
    { key: 'continuous_speed', label: t('Durata ciclo (s)'), type: 'range', min: 10, max: 40, step: 1,
      show: s => !!s.continuous && s.continuous !== 'none' },

    // ─── Filtro ───
    { type: 'separator', label: t('Filtro immagine') },
    { key: 'filter', label: t('Filtro'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'grayscale', label: t('Bianco e nero') },
      { value: 'sepia', label: t('Seppia') },
      { value: 'high-contrast', label: t('Alto contrasto') },
      { value: 'warm', label: t('Caldo') },
      { value: 'cool', label: t('Freddo') },
      { value: 'vintage', label: t('Vintage') },
      { value: 'duotone', label: t('Duotono') },
    ]},
    { key: 'filter_hover_restore', label: t('Rimuovi filtro al hover'), type: 'toggle',
      show: s => s.filter && s.filter !== 'none' },
    { key: 'duotone_dark', label: t('Colore scuro'), type: 'color',
      show: s => s.filter === 'duotone' },
    { key: 'duotone_light', label: t('Colore chiaro'), type: 'color',
      show: s => s.filter === 'duotone' },
    { key: 'duotone_intensity', label: t('Intensità duotone (%)'), type: 'range', min: 0, max: 100, step: 5,
      show: s => s.filter === 'duotone' },

    // ─── Cornice ───
    { type: 'separator', label: t('Cornice') },
    { key: 'frame', label: t('Stile cornice'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'polaroid', label: t('Polaroid') },
      { value: 'rounded', label: t('Arrotondata') },
      { value: 'shadow-box', label: t('Riquadro ombra') },
      { value: 'torn', label: t('Strappata') },
      { value: 'tape', label: t('Nastro adesivo') },
      { value: 'inset', label: t('Interna') },
    ]},
    { key: 'frame_color', label: t('Colore cornice'), type: 'color',
      show: s => s.frame === 'polaroid' || s.frame === 'shadow-box' || s.frame === 'inset' },
    { key: 'frame_inset_padding', label: t('Distanza dal bordo (px)'), type: 'spacing', max: 40 },

    // ─── Bordi animati ───
    { type: 'separator', label: t('Bordi animati') },
    { key: 'anim_border', label: t('Tipo bordo animato'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'frame-in', label: t('Cornice entrante') },
      { value: 'neon', label: t('Neon') },
      { value: 'ants', label: t('Formiche') },
      { value: 'corners', label: t('Angoli') },
      { value: 'pulse', label: t('Pulsazione') },
      { value: 'radar', label: t('Radar') },
    ]},
    { key: 'anim_border_color', label: t('Colore'), type: 'color',
      show: s => s.anim_border && s.anim_border !== 'none' },
    { key: 'anim_border_thickness', label: t('Spessore (px)'), type: 'range', min: 1, max: 6, step: 1,
      show: s => s.anim_border && s.anim_border !== 'none' && s.anim_border !== 'frame-in' },
    { key: 'anim_border_inset', label: t('Distanza dal bordo (px)'), type: 'range', min: 4, max: 50, step: 2,
      show: s => s.anim_border === 'frame-in' },
    { key: 'anim_border_speed', label: t('Velocità (s)'), type: 'range', min: 1, max: 10, step: 0.5,
      show: s => s.anim_border && s.anim_border !== 'none' && s.anim_border !== 'frame-in' && s.anim_border !== 'corners' },

    // ─── +N overlay ───
    { type: 'separator', label: 'Indicatore "+N"' },
    { key: 'more_bg', label: t('Sfondo overlay'), type: 'color' },
    { key: 'more_color', label: t('Colore testo'), type: 'color' },
    { key: 'more_size', label: t('Dimensione testo (px)'), type: 'range', min: 16, max: 48, step: 2 },

    // ─── Ombra & bordi ───
    ...shadowField,
    ...borderFields(),
  ],
};
