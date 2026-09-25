import { columnWidthOptions, flexContainerFields } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Column — container puro: nessun contenuto editabile.
 *   fields[]      → comportamento sticky (sticky è funzionale, non puro aspetto)
 *   styleFields[] → sfondo, larghezza responsive, flex
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'column',
  name: t('Colonna'),
  icon: 'dashicons-editor-insertmore',
  category: 'structure',
  defaults: {
    bg: { type: 'none' },
    width_default: '',
    width_small: '',
    width_medium: '',
    width_large: '',
    flex_direction: '',
    flex_justify: '',
    flex_align: '',
    flex_wrap: '',
    flex_column_gap: '',
    flex_row_gap: '',
    sticky: false,
    sticky_offset: 50,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'description', description: t('La Colonna è un contenitore. Trascina al suo interno le tile, oppure passa al tab Stile per configurare sfondo, larghezza e allineamento.') },

    { type: 'separator', label: t('Scroll fisso (sticky)') },
    { key: '_sticky_hint', type: 'description', label: '',
      description: t('Mantiene questa colonna ferma mentre le altre colonne della stessa riga scorrono. Per layout immagine + testo: attiva sulla colonna che contiene l\'immagine.') },
    { key: 'sticky', label: t('Attiva scroll fisso'), type: 'toggle' },
    { key: 'sticky_offset', label: t('Distanza dal bordo superiore'), type: 'range', min: 0, max: 300, step: 5,
      condition: { field: 'sticky', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // «Larghezza responsive» c'è per OGNI colonna. Due gruppi con le stesse
  // etichette, e l'inspector (campiStileElemento) ne mostra uno solo secondo la
  // riga genitore: riga Flex → width_* (classi uk-width-*); riga a griglia →
  // grid_width_* (+ la spiegazione), rese dal PHP nel <style> della riga
  // (css_larghezze_griglia). Chiavi separate: i width_* salvati in righe a
  // griglia non hanno mai agito e non devono cominciare adesso.
  styleFields: [
    { type: 'separator', label: t('Larghezza responsive') },
    { key: '_grid_width_hint', type: 'description', label: '', soloGriglia: true,
      description: t('Riga a griglia: una misura vuota prende la larghezza della prima misura impostata sopra di lei nell’elenco, cioè per uno schermo più piccolo (desktop vuoto → quella del tablet, se no del telefono). Nelle misure in cui almeno una colonna della riga ha una larghezza, le colonne vanno in fila, in ordine: quelle ad «Auto» prendono come larghezza la loro parte della griglia (il 100% dove la riga si impila) e, se la fila non basta, vanno a capo. Lì le posizioni della griglia e le colonne alte più righe non valgono, l’altezza minima della colonna comprende il padding e, con colonne del modello di larghezze diverse (1fr 2fr 1fr…) e un gap, le larghezze possono scostarsi di qualche pixel da quelle della griglia (meno di un gap con i modelli del pannello). Nelle altre misure vale la griglia della riga.') },
    { key: 'width_default', label: t('Larghezza telefono'), type: 'select', options: columnWidthOptions, soloFlex: true },
    { key: 'width_small', label: t('Larghezza tablet'), type: 'select', options: columnWidthOptions, soloFlex: true },
    { key: 'width_medium', label: t('Larghezza desktop'), type: 'select', options: columnWidthOptions, soloFlex: true },
    { key: 'width_large', label: t('Larghezza schermo grande'), type: 'select', options: columnWidthOptions, soloFlex: true },
    { key: 'grid_width_default', label: t('Larghezza telefono'), type: 'select', options: columnWidthOptions, soloGriglia: true },
    { key: 'grid_width_small', label: t('Larghezza tablet'), type: 'select', options: columnWidthOptions, soloGriglia: true },
    { key: 'grid_width_medium', label: t('Larghezza desktop'), type: 'select', options: columnWidthOptions, soloGriglia: true },
    { key: 'grid_width_large', label: t('Larghezza schermo grande'), type: 'select', options: columnWidthOptions, soloGriglia: true },

    ...flexContainerFields,
  ],
};
