export default {
  type: 'servicevideo',
  name: 'Video Servizio',
  icon: 'dashicons-video-alt3',
  category: 'booking',
  defaults: {
    meta_prefix: '_olo_service_',
    video_slot: '1',
    // Player
    autoplay: false,
    loop: true,
    muted: true,
    controls: true,
    playsinline: true,
    // Layout
    aspect_ratio: '16/9',
    max_width: '',
    border_radius: '12',
    // Poster
    poster_from_thumb: true,
    // Play button
    show_play_btn: true,
    play_btn_size: '64',
    play_btn_bg: 'rgba(0,0,0,0.5)',
    play_btn_color: '#FFFFFF',
  },
  fields: [
    { type: 'separator', label: 'Sorgente dati' },
    { key: 'meta_prefix', label: 'Tipo servizio', type: 'select', optionsSource: 'metaPrefixes' },
    { key: 'video_slot', label: 'Video da mostrare', type: 'select', options: [
      { value: '1', label: 'Video 1' },
      { value: '2', label: 'Video 2' },
      { value: '3', label: 'Video 3' },
    ]},

    { type: 'separator', label: 'Riproduzione' },
    { key: 'autoplay', label: 'Avvio automatico', type: 'toggle' },
    { key: 'loop', label: 'Ripeti in loop', type: 'toggle' },
    { key: 'muted', label: 'Audio disattivato', type: 'toggle' },
    { key: 'controls', label: 'Mostra comandi', type: 'toggle' },
    { key: 'playsinline', label: 'Riproduci inline (mobile)', type: 'toggle' },

    { type: 'separator', label: 'Layout' },
    { key: 'aspect_ratio', label: 'Proporzioni', type: 'select', options: [
      { value: '16/9', label: '16:9 (widescreen)' },
      { value: '4/3', label: '4:3 (classico)' },
      { value: '21/9', label: '21:9 (ultrawide)' },
      { value: '1/1', label: '1:1 (quadrato)' },
      { value: '9/16', label: '9:16 (verticale)' },
    ]},
    { key: 'max_width', label: 'Larghezza max (px, vuoto = 100%)', type: 'text' },
    { key: 'border_radius', label: 'Raggio bordi (px)', type: 'range', min: 0, max: 32, step: 2 },

    { type: 'separator', label: 'Poster' },
    { key: 'poster_from_thumb', label: 'Usa immagine in evidenza come poster', type: 'toggle' },

    { type: 'separator', label: 'Pulsante play' },
    { key: 'show_play_btn', label: 'Mostra pulsante play', type: 'toggle' },
    { key: 'play_btn_size', label: 'Dimensione (px)', type: 'range', min: 32, max: 96, step: 4,
      condition: { field: 'show_play_btn', value: true } },
    { key: 'play_btn_bg', label: 'Sfondo pulsante', type: 'color',
      condition: { field: 'show_play_btn', value: true } },
    { key: 'play_btn_color', label: 'Colore icona', type: 'color',
      condition: { field: 'show_play_btn', value: true } },
  ],
};
