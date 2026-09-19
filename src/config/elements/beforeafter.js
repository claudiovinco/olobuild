import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField } from './_shared.js';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Before / After — griglia di card "prova" (risultati): coppia di media affiancati
 * con etichette Prima/Dopo + didascalia (titolo + testo). Estratta dai blueprint
 * OLOthemes (BeforeAfter: cadence "The proof"). Per il confronto a slider singolo
 * usare invece la tile `imgcompare`. Render Vue == PHP (BeforeAfterTile.vue).
 */
export default {
  type: 'beforeafter',
  name: t('Before / After (prova)'),
  icon: 'dashicons-images-alt2',
  category: 'media',

  defaults: {
    items: [
      { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Marcus · 16 weeks', text: 'Down 11kg, first-ever pull-up, and a deadlift PB he never thought he’d hit.' },
      { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Priya · 6 months', text: 'Built real strength postpartum, pain-free and back to running.' },
      { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Sam · 1 year', text: 'From couch to first powerlifting meet — and stayed for the community.' },
    ],
    columns: 3,
    gap: 24,
    media_bg: '',
    media_aspect: '1/1',
    // 'cover' = il background-size che i due renderer avevano cablato fino a ieri:
    // cambiarlo qui sposterebbe ogni card già pubblicata.
    media_fit: 'cover',
    object_position: 'center center',
    accent: '',
    before_label_color: 'var(--olo-color-light, #f8f9fa)',
    after_label_color: 'var(--olo-color-light, #f8f9fa)',
    title_color: '',
    text_color: '',
    card_bg: '',
    radius: 12,

    // Spaziatura / Forma — additivi e no-op coi default (parità PHP)
    cap_padding: { top: 16, right: 4, bottom: 4, left: 4 },
    card_radius: { tl: 12, tr: 12, br: 12, bl: 12 },
    label_radius: { tl: 999, tr: 999, br: 999, bl: 999 },

    // Kit standard OLObuild — sfondo completo + ombra + bordo (no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Card prima/dopo') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { before_image: '', after_image: '', before_label: 'Before', after_label: 'After', title: 'Nome · durata', text: 'Risultato ottenuto.' },
      itemFields: [
        { key: 'before_image', label: t('Immagine "Prima"'), type: 'image' },
        { key: 'after_image', label: t('Immagine "Dopo"'), type: 'image' },
        { key: 'before_label', label: t('Etichetta Prima'), type: 'text' },
        { key: 'after_label', label: t('Etichetta Dopo'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'text', label: t('Testo risultato'), type: 'textarea' },
      ],
    },
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 4, step: 1, responsive: true },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (etichetta "Dopo")'), type: 'color',
      description: t('Sfondo dell’etichetta Dopo; vuoto = primario del tema.') },
    { key: 'before_label_color', label: t('Testo etichetta Prima'), type: 'color' },
    { key: 'after_label_color', label: t('Testo etichetta Dopo'), type: 'color' },
    { key: 'media_bg', label: t('Sfondo media'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },

    { type: 'separator', label: t('Forma') },
    // Elenco canonico: alle cinque voci di prima si aggiungono 16:9, 21:9, 9:16 e 2:3.
    // Il default resta '1/1', che e' il quadrato con cui rendono tutte le card già
    // pubblicate. Niente voce automatica: le due foto sono background-image di due
    // <div> vuoti, quindi senza `aspect-ratio` il confronto collasserebbe a zero.
    // La proporzione e' UNA per tutta la tile — prima e dopo, tutte le card: e' il
    // confronto stesso a richiederlo, due maschere diverse non si confrontano.
    { key: 'media_aspect', label: t('Proporzioni media'), type: 'select',
      options: ratioOptions({ auto: false }) },
    // L'adattamento mancava del tutto: il `background-size` era cablato su `cover`,
    // che resta il default. Le foto sono sfondi, non <img>, quindi i valori si
    // traducono (fill → '100% 100%', none → 'auto'); «Riduci se necessario» non ha
    // un equivalente nel background e non viene offerto, per non mettere in elenco
    // una voce che il renderer non saprebbe disegnare.
    { key: 'media_fit', label: t('Adattamento media'), type: 'select',
      options: ADATTAMENTI.filter((o) => o.value !== 'scale-down') },
    // Il focale passa dall'helper condiviso invece di essere riscritto a mano: la
    // chiave salvata resta `object_position` (storica, via `key`) e `src: ''` tiene il
    // pad neutro, perché le foto stanno negli item e il focale vale per tutte.
    // Scritto a mano l'oggetto era identico — ma «identico oggi» e' quello che si perde
    // al primo ritocco dell'helper: la tile ora eredita, non copia.
    focalField('', {
      key: 'object_position',
      src: '',
      ratio: 'media_aspect',
      fit: 'media_fit',
      description: t('Punto focale globale di tutte le immagini (prima + dopo).'),
      // Con «Deforma per riempire» la foto viene stirata sui due assi: non c'e'
      // nessun ritaglio da spostare e il controllo non farebbe niente.
      condition: { field: 'media_fit', op: 'neq', value: 'fill' },
    }),
    { key: 'radius', label: t('Raggio'), type: 'border-radius' },
    { key: 'gap', label: t('Gap card'), type: 'range', min: 8, max: 48, step: 2 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'cap_padding', label: t('Padding didascalia'), type: 'spacing', max: 64,
      description: t('Padding del blocco titolo + testo sotto le immagini.') },

    { type: 'separator', label: t('Raggio') },
    { key: 'card_radius', label: t('Raggio card'), type: 'border-radius',
      description: t('Arrotondamento dei 4 angoli della card. Default = raggio base.') },
    { key: 'label_radius', label: t('Raggio etichette'), type: 'border-radius',
      description: t('Arrotondamento delle pillole "Prima"/"Dopo".') },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },
    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
