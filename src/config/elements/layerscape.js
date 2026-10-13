import { t } from '@/i18n';

/**
 * Paesaggio a strati — un paesaggio disegnato a strati (cime, montagne, colline, bosco, mare di nuvole, onde, dune,
 * città o un'immagine propria) che scorrono a velocità diverse mentre si scorre la pagina, con cielo, stelle che si
 * accendono, sole o luna che tramonta dietro gli strati, nebbia nelle valli e un titolo che può stare DIETRO gli
 * strati vicini. Nata per il tema Rifugio Larice (13 ott 2026, «usa parallassi particolari»).
 * Renderer: includes/tiles/class-layerscape-tile.php (le sagome si generano da forma + variante + dettaglio).
 *   fields[]      → strati (forma, variante, dettaglio, immagine, profondità) · stelle · sole o luna · nebbia · testo ·
 *                   movimento
 *   styleFields[] → cielo · strati (specchio: colore, altezza, opacità, sfocatura) · sole o luna · stelle · nebbia ·
 *                   testo (tipografia) · pulsante · forma (altezza, raggio) · disposizione del testo
 */

const FORME = [
  { value: 'cime', label: t('Cime (guglie)') },
  { value: 'montagne', label: t('Montagne') },
  { value: 'colline', label: t('Colline') },
  { value: 'bosco', label: t('Bosco di conifere') },
  { value: 'nuvole', label: t('Mare di nuvole') },
  { value: 'onde', label: t('Onde') },
  { value: 'dune', label: t('Dune') },
  { value: 'citta', label: t('Città') },
  { value: 'immagine', label: t('Immagine') },
];
const disegnata = { field: 'forma', operator: '!=', value: 'immagine' };
const conAstro = (s) => ['sole', 'luna', 'immagine'].includes(s?.astro);
const conTesto = (s) => ['occhiello', 'titolo', 'testo', 'pulsante_testo'].some((k) => String(s?.[k] || '').trim() !== '');

export default {
  type: 'layerscape',
  name: t('Paesaggio a strati'),
  icon: 'dashicons-image-filter',
  category: 'atmosphere',

  defaults: {
    strati: [
      { id: 'ls-1', forma: 'cime', variante: 3, dettaglio: 60, immagine: '', profondita: 90, altezza: 72, colore: 'color-mix(in srgb, var(--olo-color-primary) 40%, var(--olo-color-dark))', opacita: 100, sfocatura: 0 },
      { id: 'ls-2', forma: 'montagne', variante: 7, dettaglio: 50, immagine: '', profondita: 62, altezza: 56, colore: 'color-mix(in srgb, var(--olo-color-secondary) 70%, var(--olo-color-dark))', opacita: 100, sfocatura: 0 },
      { id: 'ls-3', forma: 'colline', variante: 2, dettaglio: 50, immagine: '', profondita: 34, altezza: 40, colore: 'color-mix(in srgb, var(--olo-color-secondary) 40%, var(--olo-color-dark))', opacita: 100, sfocatura: 0 },
      { id: 'ls-4', forma: 'bosco', variante: 5, dettaglio: 55, immagine: '', profondita: 0, altezza: 30, colore: 'var(--olo-color-dark)', opacita: 100, sfocatura: 0 },
    ],
    cielo: 'sfumato',
    stelle: true,
    stelle_numero: 90,
    stelle_comparsa: 'scorrendo',
    stelle_scintillio: true,
    astro: 'sole',
    astro_immagine: '',
    astro_x: 74,
    astro_y: 50,
    astro_moto: 'tramonta',
    nebbia: true,
    nebbia_scorre: true,
    occhiello: '',
    titolo: t('Dove finisce il sentiero'),
    titolo_tag: 'h2',
    testo: '',
    pulsante_testo: '',
    pulsante_link: '',
    testo_dietro: 1,
    testo_profondita: 45,
    movimento: true,
    intensita: 60,
    mouse: true,
    altezza: 640,
    strati_scala: 100,
    cielo_alto: 'color-mix(in srgb, var(--olo-color-dark) 72%, var(--olo-color-secondary))',
    cielo_basso: 'color-mix(in srgb, var(--olo-color-primary) 55%, var(--olo-color-accent))',
    astro_colore: 'var(--olo-color-accent)',
    astro_misura: 110,
    astro_alone: 60,
    stelle_colore: 'var(--olo-color-light)',
    nebbia_colore: 'color-mix(in srgb, var(--olo-color-primary) 25%, var(--olo-color-light))',
    nebbia_opacita: 34,
    occhiello_font_family: '',
    occhiello_font_size: 13,
    occhiello_font_weight: '700',
    occhiello_uppercase: true,
    occhiello_letter_spacing: 2,
    occhiello_color: 'var(--olo-color-accent)',
    titolo_font_family: 'var(--olo-font-family-heading)',
    titolo_font_size: 72,
    titolo_font_size_tablet: 56,
    titolo_font_size_mobile: 40,
    titolo_font_weight: '800',
    titolo_line_height: 0.95,
    titolo_letter_spacing: '',
    titolo_color: 'var(--olo-color-light)',
    testo_font_family: '',
    testo_font_size: 18,
    testo_font_weight: '',
    testo_color: 'color-mix(in srgb, var(--olo-color-light) 86%, transparent)',
    pulsante_fondo: 'var(--olo-color-primary)',
    pulsante_colore: 'var(--olo-color-primary-contrast)',
    testo_allineamento: 'center',
    testo_posizione: 'alto',
    testo_larghezza: 760,
    testo_padding: { top: 72, right: 24, bottom: 72, left: 24 },
    raggio: { tl: 0, tr: 0, br: 0, bl: 0 },
  },

  fields: [
    { type: 'separator', label: t('Strati') },
    { key: 'strati', label: t('Strati del paesaggio'), type: 'content-items', itemLabel: t('Strato'),
      description: t('Dal più lontano (in alto nell\'elenco) al più vicino. Ogni strato scorre alla sua velocità: i lontani piano, i vicini insieme alla pagina. Colore, altezza, opacità e sfocatura stanno nello Stile.'),
      newItemDefaults: { forma: 'montagne', variante: 1, dettaglio: 50, immagine: '', profondita: 50, altezza: 50, colore: 'var(--olo-color-dark)', opacita: 100, sfocatura: 0 },
      itemFields: [
        { key: 'forma', label: t('Forma'), type: 'select', options: FORME },
        { key: 'immagine', label: t('Immagine'), type: 'image', condition: { field: 'forma', value: 'immagine' },
          description: t('Meglio un PNG o un WebP con lo sfondo trasparente: la parte bassa dell\'immagine poggia sul fondo della scena.') },
        { key: 'variante', label: t('Variante'), type: 'range', min: 1, max: 99, step: 1, condition: disegnata,
          description: t('Ogni numero disegna un\'altra sagoma della stessa forma.') },
        { key: 'dettaglio', label: t('Dettaglio'), type: 'range', min: 0, max: 100, step: 5, unit: '%', condition: disegnata,
          description: t('Quante punte, alberi, onde o palazzi: più alto, più fitto e frastagliato.') },
        { key: 'profondita', label: t('Profondità'), type: 'range', min: 0, max: 100, step: 1, unit: '%',
          description: t('0: si muove con la pagina, come una cosa vicina. 100: quasi fermo, come l\'orizzonte.') },
      ],
    },

    { type: 'separator', label: t('Stelle') },
    { key: 'stelle', label: t('Mostra le stelle'), type: 'toggle' },
    { key: 'stelle_numero', label: t('Quante stelle'), type: 'range', min: 10, max: 300, step: 10, condition: { field: 'stelle', value: true } },
    { key: 'stelle_comparsa', label: t('Comparsa'), type: 'select', condition: { field: 'stelle', value: true }, options: [
      { value: 'scorrendo', label: t('Si accendono scorrendo') },
      { value: 'sempre', label: t('Sempre accese') },
    ]},
    { key: 'stelle_scintillio', label: t('Scintillio'), type: 'toggle', condition: { field: 'stelle', value: true } },

    { type: 'separator', label: t('Sole o luna') },
    { key: 'astro', label: t('In cielo'), type: 'select', options: [
      { value: 'nessuno', label: t('Niente') },
      { value: 'sole', label: t('Sole') },
      { value: 'luna', label: t('Luna') },
      { value: 'immagine', label: t('Immagine') },
    ]},
    { key: 'astro_immagine', label: t('Immagine'), type: 'image', condition: { field: 'astro', value: 'immagine' } },
    { key: 'astro_moto', label: t('Scorrendo'), type: 'select', show: conAstro, options: [
      { value: 'tramonta', label: t('Tramonta dietro gli strati') },
      { value: 'sorge', label: t('Sorge da dietro gli strati') },
      { value: 'fermo', label: t('Resta lontano') },
    ]},

    { type: 'separator', label: t('Nebbia') },
    { key: 'nebbia', label: t('Mostra la nebbia'), type: 'toggle',
      description: t('Banchi di nebbia nelle valli, fra uno strato e quello davanti.') },
    { key: 'nebbia_scorre', label: t('La nebbia scorre'), type: 'toggle', condition: { field: 'nebbia', value: true } },

    { type: 'separator', label: t('Testo') },
    { key: 'occhiello', label: t('Occhiello'), type: 'text' },
    { key: 'titolo', label: t('Titolo'), type: 'textarea', rows: 2 },
    { key: 'titolo_tag', label: t('Tag del titolo'), type: 'select', options: [
      { value: 'h1', label: 'H1' }, { value: 'h2', label: 'H2' }, { value: 'h3', label: 'H3' }, { value: 'h4', label: 'H4' }, { value: 'p', label: t('Paragrafo') },
    ]},
    { key: 'testo', label: t('Testo'), type: 'textarea', rows: 3 },
    { key: 'pulsante_testo', label: t('Testo del pulsante'), type: 'text' },
    { key: 'pulsante_link', label: t('Link del pulsante'), type: 'link', show: (s) => String(s?.pulsante_testo || '').trim() !== '' },

    { type: 'separator', label: t('Movimento') },
    { key: 'movimento', label: t('Parallasse scorrendo'), type: 'toggle',
      description: t('Gli strati scorrono a velocità diverse. Si ferma per chi ha chiesto al sistema di ridurre il movimento.') },
    { key: 'intensita', label: t('Intensità'), type: 'range', min: 0, max: 100, step: 5, unit: '%', condition: { field: 'movimento', value: true } },
    { key: 'testo_profondita', label: t('Profondità del testo'), type: 'range', min: 0, max: 100, step: 1, unit: '%', condition: { field: 'movimento', value: true },
      description: t('Come per gli strati: 0 scorre con la pagina, 100 resta quasi fermo. A metà, gli strati vicini salendo lo coprono.') },
    { key: 'mouse', label: t('Segue il mouse'), type: 'toggle',
      description: t('Col mouse gli strati vicini si spostano di lato più dei lontani. Sui telefoni e sui tablet non c\'è il mouse: resta la parallasse.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'cielo', label: t('Cielo'), type: 'select', options: [
      { value: 'sfumato', label: t('Sfumato') },
      { value: 'tinta', label: t('Tinta unita') },
      { value: 'nessuno', label: t('Trasparente') },
    ]},
    { key: 'cielo_alto', label: t('Colore cielo in alto'), type: 'color', condition: { field: 'cielo', operator: 'in', value: ['sfumato', 'tinta'] } },
    { key: 'cielo_basso', label: t('Colore cielo all\'orizzonte'), type: 'color', condition: { field: 'cielo', value: 'sfumato' } },

    { type: 'separator', label: t('Strati') },
    { key: 'strati', type: 'content-items', label: t('Strati del paesaggio'), itemLabel: t('Strato'), etichettaDa: 'forma', etichettaOpzioni: FORME, miniaturaDa: 'immagine', itemFields: [
        { key: 'colore', label: t('Colore'), type: 'color' },
        { key: 'altezza', label: t('Altezza'), type: 'range', min: 5, max: 100, step: 1, unit: '%',
          description: t('Quanta parte della scena occupa, dal basso.') },
        { key: 'opacita', label: t('Opacità'), type: 'range', min: 0, max: 100, step: 5, unit: '%' },
        { key: 'sfocatura', label: t('Sfocatura'), type: 'range', min: 0, max: 12, step: 0.5, unit: 'px' },
    ] },

    { type: 'separator', label: t('Sole o luna'), show: conAstro },
    { key: 'astro_colore', label: t('Colore'), type: 'color', show: (s) => conAstro(s) && s.astro !== 'immagine' },
    { key: 'astro_misura', label: t('Dimensione'), type: 'range', min: 24, max: 400, step: 2, unit: 'px', responsive: true, show: conAstro },
    { key: 'astro_alone', label: t('Alone'), type: 'range', min: 0, max: 100, step: 5, unit: '%', show: (s) => conAstro(s) && s.astro !== 'immagine' },
    { key: 'astro_x', label: t('Posizione orizzontale'), type: 'range', min: 0, max: 100, step: 1, unit: '%', responsive: true, show: conAstro },
    { key: 'astro_y', label: t('Posizione verticale'), type: 'range', min: 0, max: 100, step: 1, unit: '%', responsive: true, show: conAstro },

    { type: 'separator', label: t('Stelle'), condition: { field: 'stelle', value: true } },
    { key: 'stelle_colore', label: t('Colore'), type: 'color' },

    { type: 'separator', label: t('Nebbia'), condition: { field: 'nebbia', value: true } },
    { key: 'nebbia_colore', label: t('Colore'), type: 'color' },
    { key: 'nebbia_opacita', label: t('Opacità'), type: 'range', min: 0, max: 100, step: 2, unit: '%' },

    { type: 'separator', label: t('Testo'), show: conTesto },
    { type: 'typography', label: t('Occhiello'), responsiveKeys: [],
      show: (s) => String(s?.occhiello || '').trim() !== '',
      keys: {
        family: 'occhiello_font_family',
        size: 'occhiello_font_size',
        weight: 'occhiello_font_weight',
        uppercase: 'occhiello_uppercase',
        letterSpacing: 'occhiello_letter_spacing',
        color: 'occhiello_color',
      },
      sizeMin: 9, sizeMax: 28,
    },
    { type: 'typography', label: t('Titolo'), responsiveKeys: ['size'],
      show: (s) => String(s?.titolo || '').trim() !== '',
      keys: {
        family: 'titolo_font_family',
        size: 'titolo_font_size',
        weight: 'titolo_font_weight',
        lineHeight: 'titolo_line_height',
        letterSpacing: 'titolo_letter_spacing',
        color: 'titolo_color',
      },
      sizeMin: 16, sizeMax: 200, sizeStep: 2,
    },
    { type: 'typography', label: t('Testo'), responsiveKeys: [],
      show: (s) => String(s?.testo || '').trim() !== '',
      keys: {
        family: 'testo_font_family',
        size: 'testo_font_size',
        weight: 'testo_font_weight',
        color: 'testo_color',
      },
      sizeMin: 12, sizeMax: 32,
    },

    { type: 'separator', label: t('Pulsante'), show: (s) => String(s?.pulsante_testo || '').trim() !== '' },
    { key: 'pulsante_fondo', label: t('Colore sfondo'), type: 'color' },
    { key: 'pulsante_colore', label: t('Colore testo'), type: 'color' },

    { type: 'separator', label: t('Forma') },
    { key: 'altezza', label: t('Altezza'), type: 'range', min: 240, max: 1400, step: 10, unit: 'px', responsive: true },
    { key: 'strati_scala', label: t('Altezza degli strati'), type: 'range', min: 40, max: 150, step: 5, unit: '%', responsive: true,
      description: t('Alza o abbassa tutti gli strati insieme. Sul telefono, più stretto e alto, abbassarli lascia più cielo al sole e al testo.') },
    { key: 'raggio', label: t('Raggio'), type: 'border-radius' },

    { type: 'separator', label: t('Disposizione'), show: conTesto },
    { key: 'testo_posizione', label: t('Posizione del testo'), type: 'select', responsive: true, options: [
      { value: 'alto', label: t('In alto') },
      { value: 'centro', label: t('Al centro') },
      { value: 'basso', label: t('In basso') },
    ]},
    { key: 'testo_allineamento', label: t('Allineamento'), type: 'select', responsive: true, options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'testo_larghezza', label: t('Larghezza del testo'), type: 'range', min: 320, max: 1400, step: 10, unit: 'px' },
    { key: 'testo_padding', label: t('Padding testo'), type: 'spacing', responsive: true },
    { key: 'testo_dietro', label: t('Strati davanti al testo'), type: 'range', min: 0, max: 8, step: 1,
      description: t('Quanti strati, contando dal più vicino, passano davanti al testo. 0: il testo sta sopra tutto.') },
  ],
};
