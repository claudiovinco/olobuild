
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { ratioOptions } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Tile Content — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → testi (heading, text), tag titolo, immagine + posizione, sorgente hover, URL link, target,
 *                   target effetti testo (heading/text/both), comportamento loop effetto, frasi typewriter
 *   styleFields[] → preset, sfondo creativo, typography preset, dimensioni/colori/allineamento,
 *                   stile immagine (fit/radius/border/shadow), effetti hover visuali, durata/colori effetti testo, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'content',
  name: t('Contenuto'),
  icon: 'dashicons-text-page',
  category: 'essential',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    heading: t('Titolo Provvisorio'),
    heading_tag: 'h2',
    heading_size: 'md',
    heading_line_height: 1.2,
    heading_align: '',
    heading_color: '',
    heading_font_family: '',
    heading_font_weight: '',
    text: t('Aggiungi il tuo contenuto qui.'),
    text_color: '',
    text_font_family: '',
    text_font_size: '',
    text_font_weight: '',
    image: '',
    image_position: 'top',
    image_width: '40',
    image_height: 'auto',
    // 'auto' = nessun aspect-ratio nel CSS: l'altezza continua a essere quella di
    // `image_height` (che a sua volta vale 'auto'). È il default OBBLIGATO, perché
    // qualunque proporzione ritaglierebbe le immagini delle pagine già pubblicate.
    aspect_ratio: 'auto',
    aspect_ratio_custom: '16/9',
    image_fit: 'cover',
    object_position: 'center center',
    image_radius: '0',
    image_border_width: '0',
    image_border_color: '',
    image_shadow: 'none',
    heading_gap: '8',
    image_gap: '16',
    hover_effect: 'none',
    hover_image: '',
    hover_video: '',
    link_url: '',
    link_target: '_self',
    // Text effects
    text_effect_target: 'heading',
    text_effect: 'none',
    text_effect_speed: '50',
    text_effect_delay: '0',
    text_effect_loop: false,
    text_effect_cursor: true,
    text_effect_cursor_char: '|',
    text_effect_color: '',
    text_effect_color_to: '',
    text_effect_phrases: '',
    text_effect_pause: '1500',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'text', label: t('Testo'), type: 'editor', mode: 'block' },

    { type: 'separator', label: t('Immagine') },
    { key: 'image', label: t('Immagine'), type: 'image' },
    { key: 'image_position', label: t('Posizione immagine'), type: 'select', responsive: true, options: [
      { value: 'top', label: t('Sopra') },
      { value: 'bottom', label: t('Sotto') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ]},
    // L'avviso sta QUI, sul campo che diventa inerte, non solo su «Proporzioni»:
    // le due cose vivono in schede diverse (questa è CONTENUTO, la proporzione è in
    // STILE) e chi sta muovendo l'altezza non ha modo di leggere la descrizione
    // dell'altro campo. Il renderer forza `height:auto` quando c'è una proporzione
    // (class-content-tile.php, gemello in ContentTile.vue `imgStyle`).
    { key: 'image_height', label: t('Altezza immagine (px o auto)'), type: 'text',
      description: t('Ignorata quando in Stile è attiva una proporzione.') },
    { key: 'hover_image', label: t('Immagine hover'), type: 'image' },
    { key: 'hover_video', label: t('Video hover (mp4)'), type: 'media' },

    { type: 'separator', label: t('Link immagine') },
    { key: 'link_url', label: t('URL collegamento'), type: 'link' },
    { key: 'link_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova finestra') },
    ]},

    { type: 'separator', label: t('Effetti testo (contenuto)') },
    { key: 'text_effect_target', label: t('Applica a'), type: 'select', options: [
      { value: 'heading', label: t('Solo titolo') },
      { value: 'text', label: t('Solo testo') },
      { value: 'both', label: t('Titolo e testo') },
    ], condition: { field: 'text_effect', op: 'neq', value: 'none' } },
    { key: 'text_effect_cursor', label: t('Mostra cursore lampeggiante'), type: 'toggle',
      condition: { field: 'text_effect', op: 'eq', value: 'typewriter' } },
    { key: 'text_effect_cursor_char', label: t('Carattere cursore'), type: 'text',
      condition: { field: 'text_effect_cursor', op: 'eq', value: true } },
    { key: 'text_effect_phrases', label: t('Frasi (una per riga)'), type: 'textarea',
      description: t('Verranno mostrate in loop con effetto typewriter'),
      condition: { field: 'text_effect', op: 'eq', value: 'typewriter-loop' } },
    { key: 'text_effect_loop', label: t('Riproduci in loop'), type: 'toggle',
      condition: { field: 'text_effect', value: ['typewriter', 'reveal-letter', 'reveal-word', 'wave', 'glitch', 'scramble'] } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'),
      responsiveKeys: ['lineHeight'],
      keys: {
        tag:        'heading_tag',
        family:     'heading_font_family',
        weight:     'heading_font_weight',
        lineHeight: 'heading_line_height',
        color:      'heading_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Testo'),
      responsiveKeys: [],
      keys: {
        family: 'text_font_family',
        size:   'text_font_size',
        weight: 'text_font_weight',
        color:  'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Titolo') },
    { key: 'heading_size', label: t('Dim. titolo'), type: 'select', responsive: true, options: [
      { value: 'sm', label: t('Piccolo') },
      { value: 'md', label: t('Medio') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
    ]},
    { key: 'heading_align', label: t('Allineamento titolo'), type: 'select', responsive: true, options: [
      { value: '', label: t('Predefinito') },
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustifica') },
    ]},
    { key: 'heading_gap', label: t('Gap titolo-testo'), type: 'range', min: 0, max: 60, step: 2 },

    { type: 'separator', label: t('Immagine') },
    { key: 'image_width', label: t('Larghezza immagine'), type: 'range', min: 20, max: 80, step: 5,
      condition: { field: 'image_position', value: ['left', 'right'] } },
    // La cornice mancante: la tile sapeva già come riempire (fit) e dove inquadrare
    // (punto focale), ma non che FORMA dare al ritaglio. Chiavi `aspect_ratio` /
    // `aspect_ratio_custom` come nella tile Immagine — qui il prefisso `image_` non si
    // usa per i campi della cornice (vedi `object_position` qui sotto).
    { key: 'aspect_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions({ custom: true }),
      description: t('Con una proporzione attiva comanda lei: «Altezza immagine» viene ignorata.') },
    { key: 'aspect_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4, 1.618'),
      condition: { field: 'aspect_ratio', op: 'eq', value: 'custom' } },
    { key: 'image_fit', label: t('Adattamento immagine'), type: 'select', options: [
      { value: 'cover', label: t('Cover') },
      { value: 'contain', label: t('Contain') },
      { value: 'fill', label: t('Fill') },
    ]},
    { key: 'object_position', label: t('Punto focale'), type: 'object-position',
      contextKeys: { src: 'image', fit: 'image_fit', ratio: 'aspect_ratio', ratioCustom: 'aspect_ratio_custom' } },
    withHover({ key: 'image_radius', label: t('Raggio'), type: 'border-radius' }),
    { key: 'image_border', label: t('Bordo immagine'), type: 'border',
      legacyKeys: { width: 'image_border_width', color: 'image_border_color' } },
    { key: 'image_shadow', label: t('Ombra'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Piccola') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'image_shadow_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'image_shadow_h', v: 'image_shadow_v', blur: 'image_shadow_blur', spread: 'image_shadow_spread', color: 'image_shadow_color', inset: 'image_shadow_inset' },
      condition: { field: 'image_shadow', op: 'eq', value: 'custom' } },
    { key: 'image_gap', label: t('Gap immagine-testo'), type: 'range', min: 0, max: 60, step: 4 },

    { type: 'separator', label: t('Effetti hover') },
    { key: 'hover_effect', label: t('Effetto immagine'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'zoom-rotate', label: t('Zoom + rotazione') },
      { value: 'brightness', label: t('Luminosità') },
      { value: 'desaturate', label: t('Desatura → colore') },
      { value: 'blur-in', label: t('Sfocatura → nitido') },
    ]},

    { type: 'separator', label: t('Effetti testo') },
    { key: 'text_effect', label: t('Effetto'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'typewriter', label: t('Macchina da scrivere') },
      { value: 'typewriter-loop', label: t('Macchina da scrivere — loop frasi') },
      { value: 'reveal-letter', label: t('Reveal lettera per lettera') },
      { value: 'reveal-word', label: t('Reveal parola per parola') },
      { value: 'gradient-anim', label: t('Gradient animato') },
      { value: 'glitch', label: t('Glitch RGB') },
      { value: 'wave', label: t('Wave (lettere ondulanti)') },
      { value: 'underline-grow', label: t('Underline grow') },
      { value: 'highlight-grow', label: t('Highlight grow') },
      { value: 'scramble', label: t('Split scramble') },
    ]},
    { key: 'text_effect_speed', label: t('Velocità (ms per char/parola)'), type: 'range', min: 10, max: 300, step: 5,
      condition: { field: 'text_effect', op: 'neq', value: 'none' } },
    { key: 'text_effect_delay', label: t('Ritardo iniziale'), type: 'range', min: 0, max: 3000, step: 100,
      condition: { field: 'text_effect', op: 'neq', value: 'none' } },
    { key: 'text_effect_pause', label: t('Pausa tra frasi'), type: 'range', min: 500, max: 5000, step: 100,
      condition: { field: 'text_effect', op: 'eq', value: 'typewriter-loop' } },
    { key: 'text_effect_color', label: t('Colore primario'), type: 'color',
      description: t('Per gradient/highlight/underline'),
      condition: { field: 'text_effect', value: ['gradient-anim', 'highlight-grow', 'underline-grow'] } },
    { key: 'text_effect_color_to', label: t('Colore secondario (gradient)'), type: 'color',
      condition: { field: 'text_effect', op: 'eq', value: 'gradient-anim' } },

    ...borderFields(),
  ],
};
