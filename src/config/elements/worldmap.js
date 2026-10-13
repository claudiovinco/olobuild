import { t } from '@/i18n';

/**
 * Mappa del mondo — solo i confini degli stati: ognuno trasparente, nel primo colore («In arrivo») o nel
 * secondo («Coperto»). Si sceglie cliccando sulla cartina nel controllo «Stati»: un clic il primo colore, due
 * il secondo, tre di nuovo trasparente. Nata per mostrare gli stati coperti da OLOfounder (13 ott 2026).
 * Renderer: includes/tiles/class-worldmap-tile.php · dati: assets/data/mappa-mondo.json (scripts/mappa-mondo.mjs).
 *   fields[]      → stati (cartina cliccabile), legenda (mostra, nomi dei due colori, numeri)
 *   styleFields[] → i due colori, stati vuoti, confini · testo della legenda · spessore · posizione della legenda
 */
export default {
  type: 'worldmap',
  name: t('Mappa del mondo'),
  icon: 'dashicons-admin-site-alt3',
  category: 'media',

  defaults: {
    stati: {},
    mostra_legenda: true,
    mostra_numeri: true,
    etichetta_1: t('In arrivo'),
    etichetta_2: t('Coperto'),
    colore_1: 'var(--olo-color-warning, #F59E0B)',
    colore_2: 'var(--olo-color-success, #10B981)',
    colore_vuoto: '',
    colore_confini: '',
    spessore_confini: 0.6,
    legenda_posizione: 'sotto',
    legenda_allineamento: 'center',
    legenda_font_family: '',
    legenda_font_size: '',
    legenda_font_weight: '',
    legenda_color: '',
  },

  fields: [
    { key: 'stati', label: t('Stati'), type: 'mappa-stati',
      description: t('Clicca uno stato: un clic gli dà il primo colore, due il secondo, tre lo riportano trasparente. «Apri in grande» mostra la cartina più grande, con la ricerca per nome e gli elenchi: serve per gli stati piccoli, che sulla cartina sono puntini.') },

    { type: 'separator', label: t('Legenda') },
    { key: 'mostra_legenda', label: t('Mostra la legenda'), type: 'toggle' },
    { key: 'etichetta_1', label: t('Primo colore (un clic)'), type: 'text', condition: { field: 'mostra_legenda', value: true } },
    { key: 'etichetta_2', label: t('Secondo colore (due clic)'), type: 'text', condition: { field: 'mostra_legenda', value: true } },
    { key: 'mostra_numeri', label: t('Mostra quanti sono'), type: 'toggle', condition: { field: 'mostra_legenda', value: true } },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'colore_1', label: t('Primo colore'), type: 'color' },
    { key: 'colore_2', label: t('Secondo colore'), type: 'color' },
    { key: 'colore_vuoto', label: t('Stati senza colore'), type: 'color',
      description: t('Vuoto: trasparenti, si vede lo sfondo.') },
    { key: 'colore_confini', label: t('Confini'), type: 'color',
      description: t('Vuoto: il colore del testo, velato. Chiari su uno sfondo scuro, scuri su uno chiaro.') },

    { type: 'separator', label: t('Testo'), condition: { field: 'mostra_legenda', value: true } },
    { type: 'typography', label: t('Legenda'),
      responsiveKeys: [],
      keys: {
        family: 'legenda_font_family',
        size:   'legenda_font_size',
        weight: 'legenda_font_weight',
        color:  'legenda_color',
      },
      sizeMin: 10, sizeMax: 28, sizeStep: 1,
    },

    { type: 'separator', label: t('Forma') },
    { key: 'spessore_confini', label: t('Spessore dei confini'), type: 'range', min: 0, max: 3, step: 0.1, unit: 'px' },

    { type: 'separator', label: t('Disposizione'), condition: { field: 'mostra_legenda', value: true } },
    { key: 'legenda_posizione', label: t('Posizione della legenda'), type: 'select', options: [
      { value: 'sotto', label: t('Sotto la cartina') },
      { value: 'sopra', label: t('Sopra la cartina') },
    ]},
    { key: 'legenda_allineamento', label: t('Allineamento della legenda'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
  ],
};
