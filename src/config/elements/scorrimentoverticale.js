import { t } from '@/i18n';

/**
 * Tile Scorrimento verticale — colonne di voci che scorrono senza fine, ognuna verso l'alto o verso il
 * basso (il «Vertical Marquee» di pixfort: testi, immagini e icone insieme, separatori, bordi sfumati).
 *   fields[]      → voci (tipo, contenuto, link, colonna), colonne per dispositivo, movimento
 *                   (durata del ciclo, direzione di ogni colonna, pausa al passaggio del mouse)
 *   styleFields[] → Voci (specchio: colore e sfondo della singola voce) · Testi · Icone · Immagini ·
 *                   Separatore · Riquadro (altezza per dispositivo, bordi sfumati) · Disposizione
 */

const TIPI = [
  { value: 'immagine', label: t('Immagine') },
  { value: 'icona', label: t('Icona') },
  { value: 'testo', label: t('Testo') },
  { value: 'separatore', label: t('Separatore') },
];
const DIREZIONI = [
  { value: 'giu', label: t('Verso il basso') },
  { value: 'su', label: t('Verso l\'alto') },
];
const tipo = (value) => ({ field: 'tipo', op: 'eq', value });
const bianco = 'var(--olo-color-light, #ffffff)';

export default {
  type: 'scorrimentoverticale',
  name: t('Scorrimento verticale'),
  icon: 'dashicons-sort',
  category: 'media',
  defaults: {
    voci: [
      { tipo: 'testo', testo: 'Scorrimento verticale', colonna: 'auto' },
      { tipo: 'separatore', colonna: 'auto' },
      { tipo: 'testo', testo: 'Novità', colonna: 'auto' },
    ],
    colonne: '3',
    durata: 24,
    direzione_1: 'giu',
    direzione_2: 'su',
    direzione_3: 'giu',
    direzione_4: 'su',
    pausa_hover: true,
    altezza: 420,
    gap: 40,
    gap_colonne: 24,
    sfumatura_bordi: 18,
    allineamento: 'center',
    testo_font_family: '',
    testo_font_size: 40,
    testo_font_size_tablet: '',
    testo_font_size_mobile: '',
    testo_font_weight: '700',
    testo_colore: '',
    testo_sfumatura: false,
    testo_colore_fine: '',
    icona_dimensione: 44,
    icona_riquadro: 104,
    icona_sfondo: '',
    icona_colore: '',
    icona_raggio: 24,
    immagine_altezza: 104,
    immagine_raggio: 0,
    separatore_colore: '',
  },

  // Come nasce dalla palette: l'esempio di pixfort con le icone del set — a sinistra tre icone
  // d'app che scendono, al centro due scritte sfumate fra i puntini che salgono, a destra altre tre
  // icone che salgono. Colori dai token del tema.
  partenza: {
    voci: [
      { tipo: 'icona', icona: 'gem', colonna: '1', colore: bianco, sfondo: 'var(--olo-color-warning, #f59e0b)' },
      { tipo: 'icona', icona: 'music', colonna: '1', colore: bianco, sfondo: 'var(--olo-color-success, #10b981)' },
      { tipo: 'icona', icona: 'rocket', colonna: '1', colore: bianco, sfondo: 'var(--olo-color-dark, #16263d)' },
      { tipo: 'separatore', colonna: '2' },
      { tipo: 'testo', testo: t('Scorrimento verticale'), colonna: '2' },
      { tipo: 'separatore', colonna: '2' },
      { tipo: 'testo', testo: t('Novità'), colonna: '2' },
      { tipo: 'icona', icona: 'play', colonna: '3', colore: bianco, sfondo: 'var(--olo-color-danger, #ef4444)' },
      { tipo: 'icona', icona: 'layout-grid', colonna: '3', colore: bianco, sfondo: 'var(--olo-color-info, #2563eb)' },
      { tipo: 'icona', icona: 'code-xml', colonna: '3', colore: bianco, sfondo: 'var(--olo-color-primary, #e1474f)' },
    ],
    direzione_3: 'su',
    // sul telefono le icone a sinistra e le scritte, che in un terzo di schermo andrebbero a capo a ogni sillaba
    colonne_mobile: '2',
    testo_font_size_tablet: 30,
    testo_font_size_mobile: 24,
    testo_sfumatura: true,
    testo_colore: 'var(--olo-color-warning, #f59e0b)',
    testo_colore_fine: 'var(--olo-color-primary, #e1474f)',
    icona_raggio: 28,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Voci') },
    { key: 'voci', label: t('Voci'), type: 'content-items', itemLabel: t('Voce'), etichettaDa: 'testo|tipo', miniaturaDa: 'immagine',
      newItemDefaults: { tipo: 'testo', testo: t('Nuova voce'), immagine: '', alt: '', icona: 'star', link: '', colonna: 'auto', colore: '', sfondo: '' },
      itemFields: [
        { key: 'tipo', label: t('Tipo'), type: 'select', options: TIPI },
        { key: 'immagine', label: t('Immagine'), type: 'image', condition: tipo('immagine') },
        { key: 'alt', label: t('Testo alternativo'), type: 'text', condition: tipo('immagine') },
        { key: 'icona', label: t('Icona'), type: 'icon', condition: tipo('icona') },
        { key: 'testo', label: t('Testo'), type: 'text', condition: tipo('testo') },
        { key: 'link', label: t('Link'), type: 'link', condition: { field: 'tipo', op: 'neq', value: 'separatore' } },
        { key: 'colonna', label: t('Colonna'), type: 'select', options: [
          { value: 'auto', label: t('Automatica') },
          { value: '1', label: '1' },
          { value: '2', label: '2' },
          { value: '3', label: '3' },
          { value: '4', label: '4' },
        ], description: t('Automatica = le voci si distribuiscono a turno fra le colonne.') },
      ] },
    { key: 'colonne', label: t('Colonne'), type: 'select', responsive: true, options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
    ], description: t('Su tablet e telefono puoi mostrarne meno: le colonne in più si nascondono con le loro voci.') },

    { type: 'separator', label: t('Movimento') },
    { key: 'durata', label: t('Durata del ciclo'), type: 'range', min: 3, max: 120, step: 1, unit: 's',
      description: t('Il tempo in cui una colonna fa scorrere tutte le sue voci: più è lungo, più va piano.') },
    { key: 'direzione_1', label: t('Colonna 1'), type: 'select', options: DIREZIONI },
    { key: 'direzione_2', label: t('Colonna 2'), type: 'select', options: DIREZIONI, condition: { field: 'colonne', op: 'in', value: ['2', '3', '4'] } },
    { key: 'direzione_3', label: t('Colonna 3'), type: 'select', options: DIREZIONI, condition: { field: 'colonne', op: 'in', value: ['3', '4'] } },
    { key: 'direzione_4', label: t('Colonna 4'), type: 'select', options: DIREZIONI, condition: { field: 'colonne', op: 'eq', value: '4' } },
    { key: 'pausa_hover', label: t('Pausa al passaggio del mouse'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Voci') },
    { key: 'voci', type: 'content-items', etichettaDa: 'testo|tipo', etichettaOpzioni: TIPI, miniaturaDa: 'immagine', itemFields: [
      { key: 'colore', label: t('Colore'), type: 'color', condition: { field: 'tipo', op: 'in', value: ['testo', 'icona'] },
        description: t('Del testo o dell\'icona di questa voce. Vuoto = quello di tutte le voci; con la sfumatura è il colore da cui parte.') },
      { key: 'sfondo', label: t('Sfondo icona'), type: 'color', condition: tipo('icona'),
        description: t('Il riquadro dietro questa icona. Vuoto = quello di tutte le icone.') },
    ] },

    { type: 'separator', label: t('Testi') },
    { type: 'typography', label: t('Testo'), responsiveKeys: ['size'],
      keys: { family: 'testo_font_family', size: 'testo_font_size', weight: 'testo_font_weight', color: 'testo_colore' },
      sizeMin: 12, sizeMax: 160 },
    { key: 'testo_sfumatura', label: t('Sfumatura'), type: 'toggle' },
    { key: 'testo_colore_fine', label: t('Colore finale'), type: 'color', condition: { field: 'testo_sfumatura', op: 'eq', value: true },
      description: t('Il testo sfuma dal suo colore a questo. Vuoto = il primario del tema.') },

    { type: 'separator', label: t('Icone') },
    { key: 'icona_colore', label: t('Colore icone'), type: 'color' },
    { key: 'icona_sfondo', label: t('Sfondo icone'), type: 'color',
      description: t('Il riquadro dietro le icone. Vuoto = nessun riquadro, solo l\'icona.') },
    { key: 'icona_dimensione', label: t('Dimensione icone'), type: 'range', min: 16, max: 160, step: 2, unit: 'px' },
    { key: 'icona_riquadro', label: t('Dimensione riquadro'), type: 'range', min: 24, max: 240, step: 2, unit: 'px' },
    { key: 'icona_raggio', label: t('Raggio riquadro'), type: 'border-radius' },

    { type: 'separator', label: t('Immagini') },
    { key: 'immagine_altezza', label: t('Altezza immagini'), type: 'range', min: 24, max: 320, step: 2, unit: 'px' },
    { key: 'immagine_raggio', label: t('Raggio immagini'), type: 'border-radius' },

    { type: 'separator', label: t('Separatore') },
    { key: 'separatore_colore', label: t('Colore separatore'), type: 'color' },

    { type: 'separator', label: t('Riquadro') },
    { key: 'altezza', label: t('Altezza'), type: 'range', min: 120, max: 1200, step: 10, unit: 'px', responsive: true },
    { key: 'sfumatura_bordi', label: t('Sfumatura ai bordi'), type: 'range', min: 0, max: 45, step: 1, unit: '%',
      description: t('In alto e in basso le voci svaniscono nel fondo della sezione. 0 = bordi netti.') },

    { type: 'separator', label: t('Disposizione') },
    { key: 'allineamento', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'gap', label: t('Gap voci'), type: 'range', min: 0, max: 160, step: 2, unit: 'px' },
    { key: 'gap_colonne', label: t('Gap colonne'), type: 'range', min: 0, max: 160, step: 2, unit: 'px' },
  ],
};
