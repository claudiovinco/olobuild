export default {
  type: 'langswitcher',
  name: 'Selettore lingua',
  icon: 'dashicons-translation',
  category: 'navigation',
  defaults: {
    style: 'flags',
    flag_shape: 'circle',
    flag_size: 24,
    show_label: false,
    label_format: 'name',
    layout: 'inline',
    floating_pos: 'bottom-right',
    gap: 8,
    active_bg: '',
    active_color: '',
    bg: '',
    color: '',
    border_color: '',
    border_radius: 8,
    show_dropdown_arrow: true,
    // Tabs (linguette)
    tabs_edge: 'top',
    tabs_offset: 20,
    tabs_size: 'normal',
    // Circle badge
    circle_bg: '',
    circle_border: '',
    circle_size: 36,
    // Compact
    compact: false,
  },
  fields: [
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'flags', label: 'Bandiere' },
      { value: 'flags_circle', label: 'Bandiere in cerchietto' },
      { value: 'codes', label: 'Codici (IT/EN/DE)' },
      { value: 'names', label: 'Nomi (Italiano/English)' },
      { value: 'flags_text', label: 'Bandiere + codice' },
    ]},
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'inline', label: 'Inline (dentro header/footer)' },
      { value: 'dropdown', label: 'Dropdown' },
      { value: 'tabs', label: 'Linguette fisse (bordo pagina)' },
      { value: 'floating', label: 'Fluttuante (fisso su schermo)' },
    ]},
    { key: 'compact', label: 'Compatto (extra piccolo)', type: 'toggle' },

    // Tabs options
    { key: 'tabs_edge', label: 'Bordo', type: 'select',
      show: s => s.layout === 'tabs',
      options: [
        { value: 'top', label: 'Alto (linguette che scendono)' },
        { value: 'right', label: 'Destra (linguette laterali)' },
        { value: 'left', label: 'Sinistra (linguette laterali)' },
      ]},
    { key: 'tabs_offset', label: 'Distanza dal bordo (px)', type: 'range', min: 0, max: 200, step: 5,
      show: s => s.layout === 'tabs' },
    { key: 'tabs_size', label: 'Dimensione linguette', type: 'select',
      show: s => s.layout === 'tabs',
      options: [
        { value: 'tiny', label: 'Minima' },
        { value: 'small', label: 'Piccola' },
        { value: 'normal', label: 'Normale' },
      ]},

    // Floating position
    { key: 'floating_pos', label: 'Posizione fluttuante', type: 'select', options: [
      { value: 'bottom-right', label: 'Basso destra' },
      { value: 'bottom-left', label: 'Basso sinistra' },
      { value: 'top-right', label: 'Alto destra' },
      { value: 'top-left', label: 'Alto sinistra' },
      { value: 'middle-right', label: 'Centro destra' },
      { value: 'middle-left', label: 'Centro sinistra' },
    ], show: s => s.layout === 'floating' },

    // Flag options
    { key: 'flag_shape', label: 'Forma bandiere', type: 'select', options: [
      { value: 'circle', label: 'Cerchio' },
      { value: 'rounded', label: 'Rettangolo arrotondato' },
    ], show: s => s.style === 'flags' || s.style === 'flags_text' },
    { key: 'flag_size', label: 'Dimensione bandiere (px)', type: 'range', min: 14, max: 48,
      show: s => s.style === 'flags' || s.style === 'flags_text' || s.style === 'flags_circle' },

    // Circle badge options
    { key: 'circle_size', label: 'Diametro cerchietto (px)', type: 'range', min: 24, max: 56, step: 2,
      show: s => s.style === 'flags_circle' },
    { key: 'circle_bg', label: 'Sfondo cerchietto', type: 'color',
      show: s => s.style === 'flags_circle' },
    { key: 'circle_border', label: 'Bordo cerchietto', type: 'color',
      show: s => s.style === 'flags_circle' },

    { key: 'show_label', label: 'Mostra etichetta sotto', type: 'toggle',
      show: s => s.style === 'flags' || s.style === 'flags_circle' },
    { key: 'label_format', label: 'Formato etichetta', type: 'select', options: [
      { value: 'name', label: 'Nome completo' },
      { value: 'code', label: 'Codice (IT/EN)' },
    ], show: s => s.show_label },
    { key: 'gap', label: 'Spazio tra elementi (px)', type: 'range', min: 0, max: 24 },
    { key: 'show_dropdown_arrow', label: 'Freccia dropdown', type: 'toggle',
      show: s => s.layout === 'dropdown' },

    // Colori
    { key: '_sep_colors', label: 'Colori', type: 'separator' },
    { key: 'active_bg', label: 'Sfondo lingua attiva', type: 'color' },
    { key: 'active_color', label: 'Testo lingua attiva', type: 'color' },
    { key: 'bg', label: 'Sfondo', type: 'color' },
    { key: 'color', label: 'Testo', type: 'color' },
    { key: 'border_color', label: 'Bordo', type: 'color' },
    { key: 'border_radius', label: 'Raggio bordo (px)', type: 'border-radius' },
  ],
};
