import { t } from '@/i18n';
import { withHover, focalField } from './_shared.js';
import { ratioOptions } from './_imageFrame.js';
import { demo } from '../demoMedia.js';

/**
 * Hover List — lista a righe con pastiglia colore (swatch), nome e sotto-etichetta,
 * con indentazione al passaggio del mouse e un'anteprima flottante (peek) che segue il cursore.
 * Pensata per shade finder / liste curate. Token-first.
 *
 * Estensioni "sala di regia" (blueprint Clod Evoluzione):
 *   lead_mode 'number'  → numero progressivo mono (01, 02…) al posto della pastiglia,
 *                         riga a griglia 64px 1fr auto con descrizione a destra.
 *   peek_mode 'monitor' → il box che segue il cursore diventa un monitor di regia
 *                         (viewfinder + ● STILL + barra label) invece dell'immagine.
 *
 * Hover delle voci — standard bilaterale `withHover()` (qui) ↔ `build_hover_css()` (PHP):
 *   ogni proprietà della riga che cambia al passaggio del mouse è UN solo controllo con
 *   toggle Normale | Hover + durata, mai field `hover_*` separati. Chiavi salvate
 *   INVARIATE (`hover_indent`, `hover_bg`, `number_hover_color`); varianti nuove:
 *   `name_hover_color`, `sub_hover_color`, `desc_hover_color`, `line_hover_color`,
 *   `swatch_hover_size` ('' = proprietà invariata al hover). `row_indent` '' = automatico
 *   (8px con pastiglia, 4px numerato = resa storica). Vale per ENTRAMBI i layout.
 */
export default {
  type: 'hoverlist',
  name: t('Hover List'),
  icon: 'dashicons-art',
  category: 'layout',

  defaults: {
    typography_preset: '',
    items: [
      { color: '#9a3b52', name: 'Rosewood',   sub: 'Cool · matte',  link_url: '' },
      { color: '#c77a6a', name: 'Terracotta',  sub: 'Warm · matte',  link_url: '' },
      { color: '#e79aa6', name: 'Peony',       sub: 'Cool · blush',  link_url: '' },
      { color: '#e6a17e', name: 'Apricot',     sub: 'Warm · blush',  link_url: '' },
      { color: '#7d2e3e', name: 'Merlot',      sub: 'Deep · matte',  link_url: '' },
    ],

    lead_mode: 'swatch',
    swatch_size: 26,
    swatch_hover_size: '',
    swatch_size_hover_duration: 200,
    swatch_shape: 'circle',
    number_color: '',
    number_hover_color: '',
    number_color_hover_duration: 200,

    name_font_family: 'heading',
    name_color: 'var(--olo-color-light, #f8f9fa)',
    name_hover_color: '',
    name_color_hover_duration: 200,
    name_size: 22,
    name_uppercase: false,
    name_weight: '',

    sub_color: 'var(--olo-color-text-soft, #6b7280)',
    sub_hover_color: '',
    sub_color_hover_duration: 200,
    sub_size: 12,
    sub_uppercase: true,

    desc_color: '',
    desc_hover_color: '',
    desc_color_hover_duration: 200,
    desc_size: 14,

    row_padding_y: 20,
    row_indent: '',
    hover_indent: 20,
    hover_indent_duration: 250,
    row_bg_color: '',
    hover_bg: 'var(--olo-color-dark, #16263d)',
    hover_bg_duration: 200,
    line_color: 'color-mix(in srgb, var(--olo-color-light, #f8f9fa) 13%, transparent)',
    line_hover_color: '',
    line_color_hover_duration: 200,

    peek: true,
    peek_mode: 'image',
    peek_width: 170,
    peek_ratio: '4/5',
    object_position: 'center center',
    mono_font_family: '',
  },

  // Appena nata: l'elenco dei servizi di uno studio nel layout numerato — numero, nome grande
  // e descrizione a destra, nei colori del tema su fondo chiaro; al passaggio la riga si tinge
  // del primario e accanto al cursore compare la foto del servizio.
  partenza: {
    items: [
      { name: t('Consulenza'), sub: '', desc: t('Un primo incontro per capire obiettivi, tempi e budget.'), number: '', image: demo('ufficio'), link_url: '' },
      { name: t('Progettazione'), sub: '', desc: t('Una proposta chiara, con fasi e costi definiti fin dall\'inizio.'), number: '', image: demo('scrivania'), link_url: '' },
      { name: t('Realizzazione'), sub: '', desc: t('Seguiamo ogni dettaglio fino alla consegna, senza sorprese.'), number: '', image: demo('architettura'), link_url: '' },
      { name: t('Assistenza'), sub: '', desc: t('Restiamo al tuo fianco anche dopo, ogni volta che serve.'), number: '', image: demo('caffe'), link_url: '' },
    ],
    lead_mode: 'number',
    number_color: 'var(--olo-color-text-muted, #6b7280)',
    name_color: 'var(--olo-color-dark, #16263d)',
    name_size: 40,
    sub_color: 'var(--olo-color-text-soft, #6b7280)',
    desc_color: 'var(--olo-color-text-muted, #6b7280)',
    desc_size: 15,
    hover_bg: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 7%, transparent)',
    line_color: 'var(--olo-color-border, #e5e7eb)',
    peek_width: 200,
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Righe') },
    { key: 'items', label: t('Voci'), type: 'content-items', supportsDynamic: true,
      itemLabel: t('Voce'),
      newItemDefaults: { color: '#e79aa6', name: 'Nuova voce', sub: 'Etichetta', desc: '', number: '', image: '', link_url: '', row_bg: { type: 'none' } },
      itemFields: [
        // 16 nov 2026: descrizione e numero solo col numero a sinistra, l'immagine solo con l'anteprima accesa.
        { key: 'name',     label: t('Nome'),             type: 'text' },
        { key: 'sub',      label: t('Etichetta'),        type: 'text' },
        { key: 'desc',     label: t('Descrizione'),      type: 'text',
          condizioneTile: { field: 'lead_mode', operator: '==', value: 'number' } },
        { key: 'number',   label: t('Numero'),           type: 'text', placeholder: t('Automatico: 01, 02…'),
          condizioneTile: { field: 'lead_mode', operator: '==', value: 'number' } },
        { key: 'image',    label: t('Immagine dell\'anteprima'), type: 'image',
          condizioneTile: { field: 'peek', operator: '!=', value: false } },
        { key: 'link_url', label: t('Link'),             type: 'link' },
      ],
    },

    { type: 'separator', label: t('Anteprima al passaggio') },
    { key: 'peek', label: t('Mostra l\'anteprima che segue il puntatore'), type: 'toggle' },
    { key: 'peek_mode', label: t('Contenuto'), type: 'select',
      condition: { field: 'peek', operator: '!=', value: false }, options: [
        { value: 'image', label: t('Immagine') },
        { value: 'monitor', label: t('Schermo di regia') },
      ]},
    // Il focale e' UNO per tutta la lista (src:'' → pad neutro): le anteprime devono
    // essere inquadrate allo stesso modo, altrimenti la lista "balla" al passaggio.
    focalField('image', { key: 'object_position', src: '', reveal: true,
      ratio: 'peek_ratio', label: t('Punto focale immagini') }),
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  // Ogni proprietà "hover-abile" delle voci è un controllo withHover: toggle Normale | Hover
  // + durata della transizione, stesso schema del Button. Il render (Vue + PHP) tratta un
  // valore hover vuoto come "invariato".
  styleFields: [
    { type: 'separator', label: t('Segno a sinistra') },
    { key: 'lead_mode', label: t('Elemento a sinistra'), type: 'select', options: [
      { value: 'swatch', label: t('Pastiglia') },
      { value: 'number', label: t('Numero') },
    ]},
    withHover({ key: 'swatch_size', label: t('Dimensione'), type: 'range', unit: 'px', min: 14, max: 44, step: 1,
      condition: { field: 'lead_mode', value: 'swatch' } },
      { hoverKey: 'swatch_hover_size', hoverDurationKey: 'swatch_size_hover_duration', defaultDuration: 200 }),
    { key: 'swatch_shape', label: t('Forma'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato arrotondato') },
    ], condition: { field: 'lead_mode', value: 'swatch' } },
    withHover({ key: 'number_color', label: t('Colore numero'), type: 'color',
      condition: { field: 'lead_mode', value: 'number' } },
      { hoverKey: 'number_hover_color', hoverDurationKey: 'number_color_hover_duration', defaultDuration: 200 }),

    { type: 'separator', label: t('Nome') },
    withHover({ key: 'name_color', label: t('Colore'), type: 'color' },
      { hoverKey: 'name_hover_color', hoverDurationKey: 'name_color_hover_duration', defaultDuration: 200 }),
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Nome'), responsiveKeys: [], keys: { size: 'name_size', weight: 'name_weight', uppercase: 'name_uppercase', family: 'name_font_family' }, sizeMin: 14, sizeMax: 56 },

    { type: 'separator', label: t('Etichetta') },
    withHover({ key: 'sub_color', label: t('Colore'), type: 'color' },
      { hoverKey: 'sub_hover_color', hoverDurationKey: 'sub_color_hover_duration', defaultDuration: 200 }),
    // Il font dell'etichetta stava in un campo a parte: ora è nel controllo tipografia (chiave INVARIATA).
    { type: 'typography', label: t('Etichetta'), responsiveKeys: [], keys: { family: 'mono_font_family', size: 'sub_size', uppercase: 'sub_uppercase' }, sizeMin: 10, sizeMax: 18,
      description: t('Font vuoto: il monospazio del tema.') },

    { type: 'separator', label: t('Descrizione'), condition: { field: 'lead_mode', value: 'number' } },
    withHover({ key: 'desc_color', label: t('Colore'), type: 'color',
      condition: { field: 'lead_mode', value: 'number' } },
      { hoverKey: 'desc_hover_color', hoverDurationKey: 'desc_color_hover_duration', defaultDuration: 200 }),
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [], keys: { size: 'desc_size' }, sizeMin: 10, sizeMax: 24, condition: { field: 'lead_mode', value: 'number' } },

    { type: 'separator', label: t('Righe') },
    // Nel layout numerato l'altezza della riga segue la scala fluida del blueprint
    // (clamp 20-32px): il controllo resta nascosto lì invece di fingere di agire.
    { key: 'row_padding', label: t('Padding riga'), type: 'spacing', min: 0, max: 80,
      legacyKeys: { y: 'row_padding_y' },
      condition: { field: 'lead_mode', value: 'swatch' } },
    withHover({ key: 'row_indent', label: t('Rientro riga'), type: 'range', unit: 'px', min: 0, max: 60, step: 2,
      placeholder: t('auto') },
      { hoverKey: 'hover_indent', hoverDurationKey: 'hover_indent_duration', defaultDuration: 250 }),
    withHover({ key: 'row_bg_color', label: t('Sfondo riga'), type: 'color' },
      { hoverKey: 'hover_bg', hoverDurationKey: 'hover_bg_duration', defaultDuration: 200 }),
    withHover({ key: 'line_color', label: t('Colore linee'), type: 'color' },
      { hoverKey: 'line_hover_color', hoverDurationKey: 'line_color_hover_duration', defaultDuration: 200 }),
    { type: 'description', description: t('Ogni controllo con il toggle Normale | Hover imposta anche la resa al passaggio del mouse, con la sua durata; lasciare vuoto lo stato Hover significa «invariato». Rientro vuoto = automatico (8 px con pastiglia, 4 px nel layout numerato, dove la riga segue una scala fluida di altezza). Una voce con sfondo proprio lo mantiene anche in hover.') },
    { key: 'items', type: 'content-items', label: t('Voci'), itemLabel: t('Voce'), etichettaDa: 'name', miniaturaDa: 'image', itemFields: [
        { key: 'color',    label: t('Colore pastiglia'), type: 'color',
          condizioneTile: { field: 'lead_mode', operator: '!=', value: 'number' } },
        { key: 'row_bg',   label: t('Sfondo voce'),      type: 'background',
          description: t('Sfondo della riga: colore, gradiente o immagine. Vuoto = trasparente con evidenziazione standard al passaggio del mouse; una voce con sfondo proprio lo mantiene anche in hover.') },
    ] },
    // «!= false»: peek vale di serie vero, e una tile salvata prima della chiave non ce l'ha (le condizioni leggono i
    // valori salvati, senza i predefiniti): con «== true» la zona spariva.
    { type: 'separator', label: t('Anteprima'), condition: { field: 'peek', operator: '!=', value: false } },
    { key: 'peek_width', label: t('Larghezza'), type: 'range', unit: 'px', min: 100, max: 320, step: 10 },
    // Elenco canonico. Niente voce automatica: il peek è uno <span> vuoto con la foto
    // messa come background-image, quindi senza `aspect-ratio` il riquadro avrebbe
    // altezza zero e l'anteprima sparirebbe. Il 16:11 non sta nel set canonico ma era
    // una delle quattro voci offerte finora: resta selezionabile via `extra` per chi
    // l'ha già salvato.
    // Il monitor di regia ha le sue proporzioni (quelle di uno schermo): qui agisce solo sull'immagine.
    { key: 'peek_ratio', label: t('Proporzioni'), type: 'select',
      condition: { field: 'peek_mode', op: 'neq', value: 'monitor' },
      options: ratioOptions({ auto: false, extra: ['16/11'] }) },
  ],
};
