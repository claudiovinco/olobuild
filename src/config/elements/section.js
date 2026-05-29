import { flexContainerFields, flexContainerDefaults, cssGridFields, cssGridDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Section — container puro: nessun contenuto editabile.
 *   fields[]      → placeholder informativo (la section è solo un wrapper di struttura)
 *   styleFields[] → sfondo, larghezza, padding, sticky, scroll-snap, flex/grid
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'section',
  name: t('Sezione'),
  icon: 'dashicons-align-center',
  category: 'structure',
  defaults: {
    bg: { type: 'none' },
    style: 'default',
    width: 'default',
    bg_scope: 'container',
    sticky_effect: 'none',
    sticky_top: '',
    scroll_snap: false,
    snap_dots: false,
    snap_dot_color: '',
    snap_dot_active_color: '',
    snap_dot_position: 'right',
    ...flexContainerDefaults,
    ...cssGridDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'description', description: t('La Sezione è un contenitore strutturale. Trascina al suo interno righe e tile per popolarla, oppure passa al tab Stile per configurare sfondo, larghezza, padding e layout.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Dimensioni') },
    { key: 'width', label: t('Larghezza contenuto'), type: 'select', options: [
      { value: 'small',     label: t('Piccola — max 900px') },
      { value: 'default',   label: t('Standard — max 1200px') },
      { value: 'large',     label: t('Grande — max 1400px') },
      { value: 'xlarge',    label: t('Extra grande — max 1600px') },
      { value: 'expand',    label: t('Tutta la larghezza (con padding laterale)') },
      { value: 'fullbleed', label: t('Bordo a bordo (senza padding)') },
    ], description: t('La larghezza massima del contenuto interno alla sezione. Lo sfondo si estende sempre a tutta la sezione, a meno che tu non cambi "Estensione sfondo".') },
    { key: 'bg_scope', label: t('Larghezza sezione'), type: 'select', options: [
      { value: 'container', label: t('Centrata (segue la larghezza scelta sopra)') },
      { value: 'section',   label: t('Edge-to-edge (sfondo a tutto il viewport)') },
    ], description: t('Scegli se la sezione (incluso lo sfondo colore/immagine/video) rimane centrata entro la larghezza impostata o si estende fino ai bordi del viewport. La modalità edge-to-edge è utile per band visive con sfondi a tutta pagina ma contenuto centrato.') },

    { type: 'separator', label: t('Aspetto preset') },
    { key: 'style', label: t('Variante colore'), type: 'select', options: [
      { value: 'default',   label: t('Predefinito') },
      { value: 'muted',     label: t('Attenuato') },
      { value: 'primary',   label: t('Primario') },
      { value: 'secondary', label: t('Secondario') },
    ], description: t('Variante rapida basata sui colori globali del tema. Per uno sfondo personalizzato usa "Sfondo" nelle sezioni sotto.') },

    { type: 'separator', label: t('Sticky') },
    { key: 'sticky_effect', label: t('Effetto sticky'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'cover', label: t('Cover') },
      { value: 'reveal', label: t('Reveal') },
      { value: 'cover-h', label: t('Cover orizzontale') },
      { value: 'reveal-h', label: t('Reveal orizzontale') },
    ]},
    { key: 'sticky_top', label: t('Offset dall\'alto (px)'), type: 'text', placeholder: '0', condition: { field: 'sticky_effect', operator: '!=', value: 'none' } },

    { type: 'separator', label: t('Scroll Snap') },
    { key: 'scroll_snap', label: t('Sezione full-screen con snap'), type: 'toggle' },
    { key: 'snap_dots', label: t('Navigazione pallini'), type: 'toggle',
      condition: { field: 'scroll_snap', op: 'eq', value: true } },
    { key: 'snap_dot_color', label: t('Colore pallini'), type: 'color',
      condition: { field: 'snap_dots', op: 'eq', value: true } },
    { key: 'snap_dot_active_color', label: t('Colore pallino attivo'), type: 'color',
      condition: { field: 'snap_dots', op: 'eq', value: true } },
    { key: 'snap_dot_position', label: t('Posizione pallini'), type: 'select', options: [
      { value: 'right', label: t('Destra') },
      { value: 'left', label: t('Sinistra') },
    ], condition: { field: 'snap_dots', op: 'eq', value: true } },

    ...flexContainerFields,
    ...cssGridFields,
  ],
};
