export default {
  type: 'olo_room_related',
  name: 'Sale simili',
  icon: 'dashicons-grid-view',
  category: 'olo-space',
  defaults: {
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
  },
  fields: [
    // ── Contenuto ──
    { type: 'separator', label: 'Contenuto' },
    {
      key: 'criteria', label: 'Criterio selezione', type: 'select', options: [
        { value: 'district', label: 'Stessa zona' },
        { value: 'type', label: 'Stesso tipo sala' },
        { value: 'sede', label: 'Stessa sede' },
        { value: 'nearest', label: 'Pi\u00f9 vicine (GPS)' },
        { value: 'random', label: 'Casuali' },
      ],
    },
    { key: 'count', label: 'Numero sale', type: 'range', min: 2, max: 8, step: 1 },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    {
      key: 'layout', label: 'Layout', type: 'select', options: [
        { value: 'grid', label: 'Griglia' },
        { value: 'slider', label: 'Slider (frecce)' },
        { value: 'marquee', label: 'Nastro scorrevole' },
      ],
    },
    { key: 'columns', label: 'Colonne', type: 'range', min: 2, max: 5, step: 1, condition: { field: 'layout', value: 'grid' } },
    { key: 'gap', label: 'Spazio tra card (px)', type: 'range', min: 8, max: 40, step: 2 },

    // Slider options
    { key: 'autoplay', label: 'Autoplay', type: 'toggle', condition: { field: 'layout', value: 'slider' } },
    { key: 'autoplay_speed', label: 'Velocit\u00e0 autoplay (sec)', type: 'range', min: 2, max: 10, step: 1, condition: { field: 'layout', value: 'slider' } },
    { key: 'pause_hover', label: 'Pausa al passaggio mouse', type: 'toggle', condition: { field: 'layout', value: 'slider' } },

    // Marquee options
    { key: 'marquee_speed', label: 'Velocit\u00e0 scorrimento (sec)', type: 'range', min: 10, max: 60, step: 5, condition: { field: 'layout', value: 'marquee' } },
    {
      key: 'marquee_direction', label: 'Direzione', type: 'select', options: [
        { value: 'left', label: 'Sinistra' },
        { value: 'right', label: 'Destra' },
      ], condition: { field: 'layout', value: 'marquee' },
    },
    { key: 'marquee_pause', label: 'Pausa al passaggio mouse', type: 'toggle', condition: { field: 'layout', value: 'marquee' } },

    // ── Intestazione ──
    { type: 'separator', label: 'Intestazione' },
    {
      key: 'heading', label: 'Titolo sezione', type: 'select', options: [
        { value: 'Sale nella stessa zona', label: 'Sale nella stessa zona' },
        { value: 'Sale simili', label: 'Sale simili' },
        { value: 'Potrebbe interessarti', label: 'Potrebbe interessarti' },
        { value: 'Altre sale disponibili', label: 'Altre sale disponibili' },
        { value: '', label: 'Nessun titolo' },
      ],
    },
    { key: 'heading_size', label: 'Dimensione titolo (px)', type: 'range', min: 14, max: 36, step: 1 },
    { key: 'heading_color', label: 'Colore titolo', type: 'color' },
    {
      key: 'heading_align', label: 'Allineamento titolo', type: 'select', options: [
        { value: 'left', label: 'Sinistra' },
        { value: 'center', label: 'Centro' },
        { value: 'right', label: 'Destra' },
      ],
    },

    // ── Contenuto card ──
    { type: 'separator', label: 'Contenuto card' },
    { key: 'show_image', label: 'Mostra immagine', type: 'toggle' },
    { key: 'image_height', label: 'Altezza immagine (px)', type: 'range', min: 100, max: 300, step: 10 },
    { key: 'show_type', label: 'Mostra tipo sala', type: 'toggle' },
    { key: 'show_district', label: 'Mostra zona', type: 'toggle' },
    { key: 'show_capacity', label: 'Mostra capienza', type: 'toggle' },
    { key: 'show_price', label: 'Mostra tariffa oraria', type: 'toggle' },
    { key: 'show_equipment_count', label: 'Mostra conteggio dotazioni', type: 'toggle' },
    { key: 'show_link', label: 'Mostra pulsante', type: 'toggle' },
    {
      key: 'link_text', label: 'Testo pulsante', type: 'select', options: [
        { value: 'Dettagli', label: 'Dettagli' },
        { value: 'Scopri', label: 'Scopri' },
        { value: 'Prenota', label: 'Prenota' },
        { value: 'Vedi sala', label: 'Vedi sala' },
      ],
    },

    // ── Stile card ──
    { type: 'separator', label: 'Stile card' },
    { key: 'card_bg', label: 'Sfondo card', type: 'color' },
    { key: 'card_radius', label: 'Raggio angoli (px)', type: 'border-radius' },
    { key: 'card_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    {
      key: 'card_shadow', label: 'Ombra', type: 'select', options: [
        { value: 'none', label: 'Nessuna' },
        { value: 'sm', label: 'Leggera' },
        { value: 'md', label: 'Media' },
        { value: 'lg', label: 'Forte' },
        { value: 'custom', label: 'Personalizzata' },
      ],
    },
    { key: 'card_shadow_h', label: 'Offset H (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_v', label: 'Offset V (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_spread', label: 'Espansione (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_color', label: 'Colore ombra', type: 'color',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_inset', label: 'Ombra interna', type: 'toggle',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    {
      key: 'card_hover_effect', label: 'Effetto hover', type: 'select', options: [
        { value: 'none', label: 'Nessuno' },
        { value: 'lift', label: 'Sollevamento' },
        { value: 'scale', label: 'Ingrandimento' },
        { value: 'glow', label: 'Bagliore' },
      ],
    },
    { key: 'title_size', label: 'Dimensione titolo card (px)', type: 'range', min: 14, max: 24, step: 1 },
    { key: 'title_color', label: 'Colore titolo card', type: 'color' },
    { key: 'meta_color', label: 'Colore meta', type: 'color' },
    { key: 'price_color', label: 'Colore prezzo', type: 'color' },
    { key: 'btn_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'btn_color', label: 'Colore testo pulsante', type: 'color' },
    { key: 'btn_radius', label: 'Raggio pulsante (px)', type: 'border-radius' },
    { key: 'btn_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
  ],
};
