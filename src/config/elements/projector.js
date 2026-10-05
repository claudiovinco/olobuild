import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Projector — «Calcolatore a slider»: uno slider guida un valore calcolato live
 * (budget, costo, ore, montante composto) o una lista di quantità scalate (ex Scaler).
 * Estratta dai demo OLOthemes (setupProjector in fx.js). Token-first: `zone_accent`.
 * Formula: fv = rate===0 ? value*years : value*((1+rate)^years − 1)/rate.
 * Lista: ogni quantità × valore / list_base.
 *   fields[]      → eyebrow/heading/intro, min/max/step/value, rate/years/currency, label/caption/note, mostra input
 *   styleFields[] → colore zona, allineamento, padding, raggio, ombra, bordo
 * Chiavi salvate stabili (vedi ZONE_TILES_SPEC.md). Render Vue == PHP.
 */
export default {
  type: 'projector',
  name: t('Calcolatore a slider'),
  icon: 'dashicons-chart-line',
  category: 'interactive',
  defaults: {
    eyebrow: t('Calcola'),
    heading: t('Quanto costa <em>il tuo evento</em>'),
    intro: t('Sposta il cursore sul numero di ospiti.'),
    min: '10',
    max: '200',
    step: '5',
    value: '40',
    rate: '0',
    years: '35',
    currency: '€',
    input_label: t('Ospiti'),
    input_unit: t('ospiti'),
    out_caption: t('Totale stimato'),
    result_suffix: '',
    decimals: 0,
    note: t('Prezzo indicativo a persona, IVA inclusa.'),
    show_contrib: true,
    list_base: 1,
    list_items: [],
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

    { type: 'separator', label: t('Cursore') },
    { key: 'input_label', label: t('Etichetta cursore'), type: 'text' },
    { key: 'min', label: t('Valore minimo'), type: 'number', step: 0.01 },
    { key: 'max', label: t('Valore massimo'), type: 'number', step: 0.01 },
    { key: 'step', label: t('Passo'), type: 'number', step: 0.01 },
    { key: 'value', label: t('Valore iniziale'), type: 'number', step: 0.01 },
    { key: 'input_unit', label: t('Unità del cursore'), type: 'text', placeholder: t('es. ospiti, notti, m²'),
      description: t('Si vede accanto al valore scelto («40 ospiti»). Vuota: il valore si mostra con la valuta.') },
    { key: 'show_contrib', label: t('Mostra il valore scelto sotto il cursore'), type: 'toggle' },

    { type: 'separator', label: t('Calcolo') },
    { key: 'years', label: t('Fattore (o anni)'), type: 'number', step: 0.01,
      description: t('Con crescita 0 il risultato è valore × fattore: prezzo a persona, costo a notte, ore per unità. Con una crescita è il numero di anni.') },
    { key: 'rate', label: t('Crescita annua'), type: 'number', min: 0, max: 1, step: 0.01,
      description: t('0 = moltiplica soltanto. Maggiore di 0: versamento annuo che cresce per gli anni indicati (0.06 = 6%).') },

    { type: 'separator', label: t('Risultato') },
    { key: 'out_caption', label: t('Didascalia'), type: 'text' },
    { key: 'currency', label: t('Prima del numero'), type: 'text', placeholder: '€',
      description: t('Il simbolo prima del risultato, di solito la valuta. Vuoto: nessuno.') },
    { key: 'result_suffix', label: t('Dopo il numero'), type: 'text', placeholder: t('es. kg, ore, al mese') },
    { key: 'decimals', label: t('Decimali'), type: 'number', min: 0, max: 4, step: 1 },
    { key: 'note', label: t('Nota a piè'), type: 'textarea' },

    { type: 'separator', label: t('Lista scalata (facoltativa)') },
    { key: 'list_items', label: t('Voci'), type: 'content-items', itemLabel: t('Voce'),
      defaults: { name: 'Nuova voce', amount: 100, unit: 'g' },
      itemFields: [
        { key: 'name', label: t('Nome'), type: 'text' },
        { key: 'amount', label: t('Quantità'), type: 'number', step: 0.01 },
        { key: 'unit', label: t('Unità'), type: 'text' },
      ],
      description: t('Con almeno una voce, al posto del risultato si vede la lista: ogni quantità scala con il cursore, come gli ingredienti di una ricetta.') },
    { key: 'list_base', label: t('Le quantità valgono per'), type: 'number', step: 0.01,
      description: t('Il valore del cursore a cui si riferiscono le quantità scritte (es. 4 porzioni).') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore zona (accento)'), type: 'color',
      description: t("Il colore di cursore, occhiello e risultato.") },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},

    { type: 'separator', label: t('Aspetto tile') },
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 96 },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),
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
