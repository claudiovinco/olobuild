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
  name: t('Manifesto — Scrub testo'),
  icon: 'dashicons-editor-textcolor',
  category: 'text',

  defaults: {
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

    // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'text', label: t('Testo manifesto'), type: 'textarea',
      description: t('HTML consentito: gli <em> diventano parole accento (colore primario), <br/> va a capo.') },

    { type: 'separator', label: t('Lead') },
    { key: 'show_lead', label: t('Mostra paragrafo lead'), type: 'toggle' },
    { key: 'lead', label: t('Paragrafo lead'), type: 'textarea',
      condition: { field: 'show_lead', op: 'eq', value: true } },

    { type: 'separator', label: t('Scrub allo scroll') },
    { key: 'scroll_reveal', label: t('Accendi le parole allo scroll'), type: 'toggle',
      description: t('Le parole partono attenuate e si accendono con lo scorrimento. Disattivato automaticamente con prefers-reduced-motion.') },
    { key: 'dim_opacity', label: t('Opacità parole spente (%)'), type: 'number', min: 0, max: 100,
      condition: { field: 'scroll_reveal', op: 'eq', value: true } },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (parole <em>)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'text_color', label: t('Colore testo'), type: 'color',
      description: t('Vuoto = colore testo del tema.') },
    { key: 'lead_color', label: t('Colore lead'), type: 'color',
      description: t('Vuoto = testo attenuato del tema.') },

    { type: 'separator', label: t('Tipografia') },
    { key: 'size_min', label: t('Dimensione minima (px)'), type: 'number', min: 12, max: 120 },
    { key: 'size_max', label: t('Dimensione massima (px)'), type: 'number', min: 16, max: 200 },
    { key: 'max_width_ch', label: t('Larghezza massima (ch)'), type: 'number', min: 6, max: 60 },
    { key: 'lead_size', label: t('Dimensione lead (px)'), type: 'number', min: 10, max: 32, step: 0.5 },
    { key: 'lead_max_width_ch', label: t('Larghezza massima lead (ch)'), type: 'number', min: 20, max: 100 },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
