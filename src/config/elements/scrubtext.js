import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Manifesto — Scrub testo : dichiarazione editoriale in display (Big Shoulders) con
 * parole-accento via <em> (colore primario, niente corsivo) e paragrafo lead sans.
 * Meccanica firma: allo scroll le parole si "accendono" progressivamente — partono
 * attenuate (dim_opacity) e passano a piena opacità in base al progresso della
 * sezione nel viewport (p = clamp((vh*0.9 - rect.top) / (vh*0.6), 0, 1)).
 * Rispetta prefers-reduced-motion (tutto a opacità piena). Render Vue == PHP
 * (ScrubTextTile.vue). Estratta dal blueprint "Clod — Evoluzione v2" (.manifesto).
 */
export default {
  type: 'scrubtext',
  name: t('Manifesto'),
  icon: 'dashicons-editor-textcolor',
  category: 'text',

  defaults: {
    typography_preset: '',
    text: 'Idee che si <em>vedono.</em><br/>Progetti che <em>funzionano.</em>',
    show_lead: true,
    lead: 'La mia consulenza parte da un\'analisi della situazione reale dell\'azienda — sfide e opportunità — per identificare le soluzioni più adatte. Poi le rendo visibili: strategia, web e media originali, in un unico filo conduttore.',
    scroll_reveal: true,
    dim_opacity: 13,
    accent: '',
    text_color: '',
    lead_color: '',
    size_min: 26,
    size_max: 56,
    max_width_ch: 20,
    lead_size: 16.5,
    lead_max_width_ch: 52,
    text_font_family: '',
    text_font_weight: '',
    lead_font_family: '',
    lead_font_weight: '',
    text_align: '',

    // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nato: il manifesto con le due parole d'accento e un paragrafo lead adatto a
  // qualunque attività (quello di prima parlava in prima persona di una consulenza).
  partenza: {
    lead: t('Ogni progetto parte dall\'ascolto: capiamo cosa ti serve davvero, poi lo realizziamo con cura, un passo alla volta, con tempi e costi chiari fin dal primo giorno.'),
  },

  // 16 nov 2026 (episodio 34): il nome «Manifesto» (era «Manifesto — Scrub testo»); il testo con l'editor, dove il
  // corsivo fa le parole d'accento (prima si scriveva HTML a mano: <em>, <br/>); «Paragrafo» al posto di «Lead», «Allo
  // scorrimento» al posto di «Scrub allo scroll»; lo Stile nelle zone Aspetto · Testo · Disposizione.
  fields: [
    { key: 'text', label: t('Testo'), type: 'editor', mode: 'inline', strumenti: ['corsivo'], aCapo: true,
      description: t('Le parole in corsivo prendono il colore d’accento e restano dritte. Per andare a capo: Maiusc + Invio.') },

    { type: 'separator', label: t('Paragrafo') },
    { key: 'show_lead', label: t('Mostra il paragrafo'), type: 'toggle' },
    { key: 'lead', label: t('Paragrafo'), type: 'textarea',
      condition: { field: 'show_lead', op: 'eq', value: true } },

    { type: 'separator', label: t('Allo scorrimento') },
    { key: 'scroll_reveal', label: t('Le parole si accendono'), type: 'toggle',
      description: t('Le parole partono spente e si accendono una alla volta mentre la pagina scorre. Chi nel sistema ha chiesto meno movimento le vede tutte accese.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'accent', label: t('Colore accento'), type: 'color',
      description: t('Le parole in corsivo del testo. Vuoto: il primario del sito.') },
    { key: 'dim_opacity', label: t('Parole spente'), type: 'range', min: 0, max: 100, step: 1, unit: '%',
      condition: { field: 'scroll_reveal', op: 'eq', value: true },
      description: t('Quanto si vedono le parole non ancora accese.') },
    { key: 'bg', label: t('Sfondo'), type: 'background', showParallax: false },
    ...shadowField,

    // Tipografia nel controllo unico: la dimensione del manifesto è FLUIDA
    // (scala da sé fra i due estremi) e la riga si misura in caratteri.
    // Chiavi invariate — il renderer continua a leggere le stesse.
    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    // Lo stile tipografico collegato governa ENTRAMBI i testi (famiglia, peso,
    // interlinea…): a stile scelto i due controlli quelle righe non le mostrano.
    { type: 'typography', label: t('Testo'), responsiveKeys: [],
      linkedPresetKey: 'typography_preset',
      keys: { family: 'text_font_family', fluidMin: 'size_min', fluidMax: 'size_max', weight: 'text_font_weight', maxWidth: 'max_width_ch', color: 'text_color' },
      sizeMin: 12, sizeMax: 200, maxWidthMin: 6, maxWidthMax: 60 },
    { type: 'typography', label: t('Paragrafo'), responsiveKeys: [],
      linkedPresetKey: 'typography_preset',
      keys: { family: 'lead_font_family', size: 'lead_size', weight: 'lead_font_weight', maxWidth: 'lead_max_width_ch', color: 'lead_color' },
      sizeMin: 10, sizeMax: 32, sizeStep: 0.5, maxWidthMin: 20, maxWidthMax: 100,
      condition: { field: 'show_lead', op: 'eq', value: true } },

    ...borderFields(),

    // Manifesto e paragrafo stavano sempre a sinistra. «Predefinito» = a sinistra, come prima;
    // centrate o a destra le due righe (misurate in caratteri) si spostano col margine.
    { type: 'separator', label: t('Disposizione') },
    { key: 'text_align', label: t('Allineamento'), type: 'select', responsive: true, options: [
      { value: '', label: t('Predefinito') },
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
  ],
};
