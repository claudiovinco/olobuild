import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Projector — "zona interattiva": uno slider guida un valore calcolato live
 * (budget, costo, ore, montante composto). Estratta dai demo OLOthemes (setupProjector
 * in fx.js). Token-first: un solo controllo colore `zone_accent` (gli altri derivati).
 * Formula: fv = rate===0 ? value*years : value*((1+rate)^years − 1)/rate.
 *   fields[]      → eyebrow/heading/intro, min/max/step/value, rate/years/currency, label/caption/note, mostra input
 *   styleFields[] → colore zona, allineamento, padding, raggio, ombra, bordo
 * Chiavi salvate stabili (vedi ZONE_TILES_SPEC.md). Render Vue == PHP.
 */
export default {
  type: 'projector',
  name: t('Projector (slider → valore)'),
  icon: 'dashicons-chart-line',
  category: 'interactive',
  defaults: {
    eyebrow: t('Una stima'),
    heading: t('La pazienza <em>compone</em>'),
    intro: t('Imposta quanto metti da parte ogni anno. Ecco cosa potrebbe diventare nel tempo.'),
    min: '2000',
    max: '50000',
    step: '1000',
    value: '12000',
    rate: '0.06',
    years: '20',
    currency: '€',
    input_label: t('Investito ogni anno'),
    out_caption: t('Proiezione finale'),
    note: t('Solo illustrativo. Capitale a rischio; le performance passate non garantiscono risultati futuri.'),
    show_contrib: true,
    zone_accent: '',
    align: 'left',
    tile_padding: { top: 48, right: 48, bottom: 48, left: 48 },
    border_radius: '16',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    shadow: 'sm',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },

    { type: 'separator', label: t('Slider e calcolo') },
    { key: 'min', label: t('Valore minimo'), type: 'text' },
    { key: 'max', label: t('Valore massimo'), type: 'text' },
    { key: 'step', label: t('Incremento (step)'), type: 'text' },
    { key: 'value', label: t('Valore iniziale'), type: 'text' },
    { key: 'rate', label: t('Tasso annuo (0 = lineare)'), type: 'text',
      description: t('0 → valore × anni (costo/ore/budget). >0 → montante composto (es. 0.06 = 6%).') },
    { key: 'years', label: t('Anni / moltiplicatore'), type: 'text' },
    { key: 'currency', label: t('Valuta (vuoto = nessun simbolo)'), type: 'text' },

    { type: 'separator', label: t('Etichette') },
    { key: 'input_label', label: t('Etichetta slider'), type: 'text' },
    { key: 'out_caption', label: t('Didascalia risultato'), type: 'text' },
    { key: 'note', label: t('Nota a piè'), type: 'textarea' },
    { key: 'show_contrib', label: t('Mostra valore corrente sotto lo slider'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore zona (accento)'), type: 'color',
      description: t('Un solo colore: testo-su-accento, bordi e tinta soft sono derivati automaticamente.') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},

    { type: 'separator', label: t('Aspetto tile') },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 96 },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    { key: 'shadow', label: t('Ombra'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Piccola') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
    ]},
    ...borderFields(),
  ],
};
