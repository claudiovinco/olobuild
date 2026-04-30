import { shadowField } from './_shared.js';

export default {
  type: 'shatteredimage',
  name: 'Shattered Image',
  icon: 'dashicons-shield',
  category: 'media',

  defaults: {
    image_url: '',
    // Maschera
    preset: 'shards',
    gap: 4,
    height: '400px',
    image_position: 'center center',
    gap_color: 'transparent',
    // Zoom frammenti
    zoom_variation: false,
    zoom_min: 100,
    zoom_max: 180,
    zoom_random: false,
    // Effetti scroll
    scroll_parallax: false,
    scroll_parallax_intensity: 30,
    scroll_reveal: false,
    scroll_reveal_stagger: 150,
    scroll_reveal_duration: 600,
    // Ken Burns
    kenburns: true,
    kenburns_style: 'mixed',
    kenburns_duration: 20,
    kenburns_intensity: 1.25,
    // Overlay
    overlay: false,
    overlay_color: '#000000',
    overlay_opacity: 30,
    // Avanzato
    border_radius_outer: 0,
    shadow: 'none',
  },

  fields: [
    // --- Immagine ---
    { key: 'image_url', label: 'Immagine', type: 'image' },

    // --- Maschera ---
    { type: 'separator', label: 'Maschera' },
    {
      key: 'preset',
      label: 'Preset',
      type: 'select',
      options: [
        { value: 'shards', label: 'Frantumi (simmetrico 5)' },
        { value: 'radial_center', label: 'Radiale centro (8)' },
        { value: 'shards_left', label: 'Radiale sinistra (6)' },
        { value: 'shards_right', label: 'Radiale destra (6)' },
        { value: 'shards_top', label: 'Radiale alto (6)' },
        { value: 'shards_bottom', label: 'Radiale basso (6)' },
        { value: 'columns', label: 'Colonne (3)' },
        { value: 'columns_4', label: 'Colonne (4)' },
        { value: 'columns_5', label: 'Colonne (5)' },
        { value: 'columns_6', label: 'Colonne (6)' },
        { value: 'mosaic', label: 'Mosaico (3+2)' },
        { value: 'mosaic_4', label: 'Mosaico (2×2)' },
        { value: 'mosaic_6', label: 'Mosaico (3×2)' },
        { value: 'diagonal', label: 'Diagonali (3)' },
        { value: 'diagonal_4', label: 'Diagonali (4)' },
        { value: 'diagonal_5', label: 'Diagonali (5)' },
        { value: 'diagonal_reverse', label: 'Diagonali inverse (3)' },
        { value: 'honeycomb', label: 'Esagoni (4)' },
        { value: 'honeycomb_6', label: 'Esagoni (6)' },
        { value: 'honeycomb_8', label: 'Esagoni (8)' },
        { value: 'circles_3', label: 'Cerchi (3)' },
        { value: 'circles_4', label: 'Cerchi (2×2)' },
        { value: 'circles_5', label: 'Cerchi (3+2)' },
        { value: 'circles_6', label: 'Cerchi (3×2)' },
        { value: 'circles_7', label: 'Cerchi (7 hex)' },
        { value: 'circles_scattered', label: 'Cerchi sparsi (9)' },
      ],
    },
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 0, max: 16, step: 1 },
    { key: 'height', label: 'Altezza', type: 'text' },
    {
      key: 'image_position',
      label: 'Posizione immagine',
      type: 'select',
      options: [
        { value: 'center center', label: 'Centro' },
        { value: 'top center', label: 'Alto' },
        { value: 'bottom center', label: 'Basso' },
        { value: 'left center', label: 'Sinistra' },
        { value: 'right center', label: 'Destra' },
      ],
    },
    { key: 'gap_color', label: 'Colore sfondo gap', type: 'color' },

    // --- Zoom frammenti ---
    { type: 'separator', label: 'Zoom frammenti' },
    { key: 'zoom_variation', label: 'Zoom variato per frammento', type: 'toggle' },
    { key: 'zoom_min', label: 'Zoom minimo (%)', type: 'range', min: 100, max: 250, step: 5,
      show: s => !!s.zoom_variation },
    { key: 'zoom_max', label: 'Zoom massimo (%)', type: 'range', min: 120, max: 350, step: 5,
      show: s => !!s.zoom_variation },
    { key: 'zoom_random', label: 'Ordine casuale', type: 'toggle',
      show: s => !!s.zoom_variation },

    // --- Ken Burns ---
    { type: 'separator', label: 'Ken Burns' },
    { key: 'kenburns', label: 'Attiva Ken Burns', type: 'toggle' },
    {
      key: 'kenburns_style',
      label: 'Stile movimento',
      type: 'select',
      show: s => !!s.kenburns,
      options: [
        { value: 'mixed', label: 'Misto (ogni frammento diverso)' },
        { value: 'horizontal', label: 'Orizzontale' },
        { value: 'vertical', label: 'Verticale' },
        { value: 'diagonal', label: 'Diagonale' },
        { value: 'radial', label: 'Radiale (dal centro)' },
        { value: 'zoom', label: 'Solo zoom (nessuno spostamento)' },
        { value: 'rotation', label: 'Rotazione (zoom + leggera rotazione)' },
        { value: 'chaotic', label: 'Caotico (massima variazione)' },
      ],
    },
    { key: 'kenburns_duration', label: 'Durata ciclo (s)', type: 'range', min: 10, max: 40, step: 1,
      show: s => !!s.kenburns },
    { key: 'kenburns_intensity', label: 'Intensità zoom', type: 'range', min: 1.10, max: 1.40, step: 0.01,
      show: s => !!s.kenburns },

    // --- Effetti scroll ---
    { type: 'separator', label: 'Effetti scroll (solo frontend)' },
    { key: 'scroll_parallax', label: 'Parallax per frammento', type: 'toggle' },
    { key: 'scroll_parallax_intensity', label: 'Intensità (px)', type: 'range', min: 10, max: 80, step: 5,
      show: s => !!s.scroll_parallax },
    { key: 'scroll_reveal', label: 'Reveal sequenziale', type: 'toggle' },
    { key: 'scroll_reveal_stagger', label: 'Stagger (ms)', type: 'range', min: 50, max: 400, step: 25,
      show: s => !!s.scroll_reveal },
    { key: 'scroll_reveal_duration', label: 'Durata (ms)', type: 'range', min: 200, max: 1200, step: 50,
      show: s => !!s.scroll_reveal },

    // --- Overlay ---
    { type: 'separator', label: 'Overlay' },
    { key: 'overlay', label: 'Attiva overlay', type: 'toggle' },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color',
      show: s => !!s.overlay },
    { key: 'overlay_opacity', label: 'Opacità (%)', type: 'range', min: 5, max: 90, step: 1,
      show: s => !!s.overlay },

    // --- Avanzato ---
    { type: 'separator', label: 'Avanzato' },
    { key: 'border_radius_outer', label: 'Border radius', type: 'border-radius' },
    { key: 'border_radius_outer_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    ...shadowField,
  ],
};
