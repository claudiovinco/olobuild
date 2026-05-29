
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Room Related — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → criterio selezione, numero, layout, autoplay/marquee behavior, titolo,
 *                   toggle elementi card (image/type/district/capacity/price/link), testo pulsante
 *   styleFields[] → sfondo, tipografia, dimensioni heading, colonne, gap, durata animazioni,
 *                   stile card (bg/raggio/ombra/hover effect), colori, raggio pulsante, bordo
 */
export default {
  type: 'olo_room_related',
  name: t('Sale simili'),
  icon: 'dashicons-grid-view',
  category: 'olo-space',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    criteria: 'district',
    count: 3,
    layout: 'grid',
    columns: 3,
    gap: 20,
    // Heading
    heading: 'Sale nella stessa zona',
    heading_size: 22,
    heading_color: '',
    heading_align: 'left',
    // Card content
    show_image: true,
    image_height: 180,
    show_type: true,
    show_district: false,
    show_capacity: true,
    show_price: true,
    show_equipment_count: false,
    show_link: true,
    link_text: 'Dettagli',
    // Card style
    card_bg: '',
    card_radius: 12,
    card_shadow: 'sm',
    card_hover_effect: 'lift',
    title_size: 17,
    title_color: '',
    meta_color: '',
    price_color: '',
    btn_bg: '',
    btn_color: '',
    btn_radius: 8,
    // Slider
    autoplay: true,
    autoplay_speed: 4,
    pause_hover: true,
    // Marquee
    marquee_speed: 25,
    marquee_direction: 'left',
    marquee_pause: true,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── Contenuto ──
    { type: 'separator', label: t('Contenuto') },
    {
      key: 'criteria', label: t('Criterio selezione'), type: 'select', options: [
        { value: 'district', label: t('Stessa zona') },
        { value: 'type', label: t('Stesso tipo sala') },
        { value: 'sede', label: t('Stessa sede') },
        { value: 'nearest', label: t('Più vicine (GPS)') },
        { value: 'random', label: t('Casuali') },
      ],
    },
    { key: 'count', label: t('Numero sale'), type: 'range', min: 2, max: 8, step: 1 },

    // ── Layout (selezione + behavior) ──
    { type: 'separator', label: t('Layout') },
    {
      key: 'layout', label: t('Layout'), type: 'select', options: [
        { value: 'grid', label: t('Griglia') },
        { value: 'slider', label: t('Slider (frecce)') },
        { value: 'marquee', label: t('Nastro scorrevole') },
      ],
    },

    // Slider options (behavior)
    { key: 'autoplay', label: t('Autoplay'), type: 'toggle', condition: { field: 'layout', value: 'slider' } },
    { key: 'pause_hover', label: t('Pausa al passaggio mouse'), type: 'toggle', condition: { field: 'layout', value: 'slider' } },

    // Marquee options (behavior)
    {
      key: 'marquee_direction', label: t('Direzione'), type: 'select', options: [
        { value: 'left', label: t('Sinistra') },
        { value: 'right', label: t('Destra') },
      ], condition: { field: 'layout', value: 'marquee' },
    },
    { key: 'marquee_pause', label: t('Pausa al passaggio mouse'), type: 'toggle', condition: { field: 'layout', value: 'marquee' } },

    // ── Intestazione (titolo) ──
    { type: 'separator', label: t('Intestazione') },
    {
      key: 'heading', label: t('Titolo sezione'), type: 'select', options: [
        { value: 'Sale nella stessa zona', label: t('Sale nella stessa zona') },
        { value: 'Sale simili', label: t('Sale simili') },
        { value: 'Potrebbe interessarti', label: t('Potrebbe interessarti') },
        { value: 'Altre sale disponibili', label: t('Altre sale disponibili') },
        { value: '', label: t('Nessun titolo') },
      ],
    },

    // ── Contenuto card ──
    { type: 'separator', label: t('Contenuto card') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'show_type', label: t('Mostra tipo sala'), type: 'toggle' },
    { key: 'show_district', label: t('Mostra zona'), type: 'toggle' },
    { key: 'show_capacity', label: t('Mostra capienza'), type: 'toggle' },
    { key: 'show_price', label: t('Mostra tariffa oraria'), type: 'toggle' },
    { key: 'show_equipment_count', label: t('Mostra conteggio dotazioni'), type: 'toggle' },
    { key: 'show_link', label: t('Mostra pulsante'), type: 'toggle' },
    {
      key: 'link_text', label: t('Testo pulsante'), type: 'select', options: [
        { value: 'Dettagli', label: t('Dettagli') },
        { value: 'Scopri', label: t('Scopri') },
        { value: 'Prenota', label: t('Prenota') },
        { value: 'Vedi sala', label: t('Vedi sala') },
      ],
    },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Intestazione'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'heading_size',
        color: 'heading_color',
      },
      sizeMin: 14, sizeMax: 36,
    },
    { type: 'typography', label: t('Titolo card'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'title_size',
        color: 'title_color',
      },
      sizeMin: 14, sizeMax: 24,
    },
    { type: 'typography', label: t('Meta'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'meta_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Prezzo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'price_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Layout - dimensioni') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 2, max: 5, step: 1, condition: { field: 'layout', value: 'grid' } },
    { key: 'gap', label: t('Spazio tra card (px)'), type: 'range', min: 8, max: 40, step: 2 },

    // Slider speed (durata in sec — categorizzata come stile per coerenza con animation duration)
    { key: 'autoplay_speed', label: t('Velocità autoplay (sec)'), type: 'range', min: 2, max: 10, step: 1, condition: { field: 'layout', value: 'slider' } },
    // Marquee speed
    { key: 'marquee_speed', label: t('Velocità scorrimento (sec)'), type: 'range', min: 10, max: 60, step: 5, condition: { field: 'layout', value: 'marquee' } },

    { type: 'separator', label: t('Intestazione') },
    {
      key: 'heading_align', label: t('Allineamento titolo'), type: 'select', options: [
        { value: 'left', label: t('Sinistra') },
        { value: 'center', label: t('Centro') },
        { value: 'right', label: t('Destra') },
      ],
    },

    { type: 'separator', label: t('Immagine card') },
    { key: 'image_height', label: t('Altezza immagine (px)'), type: 'range', min: 100, max: 300, step: 10 },

    // ── Stile card ──
    { type: 'separator', label: t('Stile card') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    withHover({ key: 'card_radius', label: t('Raggio angoli (px)'), type: 'border-radius' }),
    {
      key: 'card_shadow', label: t('Ombra'), type: 'select', options: [
        { value: 'none', label: t('Nessuna') },
        { value: 'sm', label: t('Leggera') },
        { value: 'md', label: t('Media') },
        { value: 'lg', label: t('Forte') },
        { value: 'custom', label: t('Personalizzata') },
      ],
    },
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
    {
      key: 'card_hover_effect', label: t('Effetto hover'), type: 'select', options: [
        { value: 'none', label: t('Nessuno') },
        { value: 'lift', label: t('Sollevamento') },
        { value: 'scale', label: t('Ingrandimento') },
        { value: 'glow', label: t('Bagliore') },
      ],
    },
    { key: 'btn_bg', label: t('Sfondo pulsante'), type: 'color' },
    { key: 'btn_color', label: t('Colore testo pulsante'), type: 'color' },
    withHover({ key: 'btn_radius', label: t('Raggio pulsante (px)'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
