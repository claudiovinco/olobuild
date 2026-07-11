import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Sticky — sezioni sticky scroll-driven OLOtheme: assembler ("il sito si
 * monta da solo", build) e day ("una giornata col motore", booking).
 * Render Vue == PHP (OloxStickyTile.vue / class-oloxsticky-tile.php).
 */
export default {
  type: 'oloxsticky',
  name: t('OLOX — Sezione sticky (assembler/day)'),
  icon: 'dashicons-editor-insertmore',
  category: 'marketing',

  defaults: {
    accent: 'build',
    variant: 'assembler',
    anchor: 'cantiere',
    kicker: 'Il cantiere',
    browser_url: 'https://il-tuo-sito.it, costruito con OLObuild',
    asm_hint: '▼ continua a scorrere',
    asm_blocks: [
      { text: 'header + menu' }, { text: 'hero animato' }, { text: 'galleria media' },
      { text: 'form builder' }, { text: 'footer' },
    ],
    asm_steps: [
      { text: 'Scrolla: il sito si <em>monta da solo</em>.' },
      { text: 'Fase 1 · <em>header</em> e menu al loro posto.' },
      { text: 'Fase 2, l’<em>hero</em> animato entra in scena.' },
      { text: 'Fase 3, la <em>galleria</em> aggancia i media.' },
      { text: 'Fase 4, il <em>form</em> raccoglie contatti.' },
      { text: 'Fase 5 · <em>footer</em>: sito consegnato. ~1h30.' },
    ],
    day_label: 'agenda riempita',
    day_hint: 'scrolla per far passare le ore',
    day_stamp: 'Confermato',
    day_slots: [
      { hh: '09:00', what: 'Visita immobile, via Verdi 8', who: 'real estate' },
      { hh: '10:30', what: 'Consulenza fiscale, Studio B.', who: 'appuntamenti' },
    ],
  },

  fields: [
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'assembler', label: t('Assembler — il sito si monta (build)') },
      { value: 'day', label: t('Giornata — agenda che si riempie (booking)') },
    ] },
    { key: 'kicker', label: t('Kicker'), type: 'text' },
    { key: 'anchor', label: t('Ancora (id)'), type: 'text' },
    { type: 'separator', label: t('Assembler') },
    { key: 'browser_url', label: t('URL nel browser mockup'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'assembler' } },
    { key: 'asm_hint', label: t('Hint sotto i passi'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'assembler' } },
    { key: 'asm_blocks', label: t('Blocchi tile'), type: 'content-items', itemLabel: t('Blocco'),
      defaults: { text: 'blocco' },
      itemFields: [ { key: 'text', label: t('Nome blocco'), type: 'text' } ],
      condition: { field: 'variant', op: 'eq', value: 'assembler' } },
    { key: 'asm_steps', label: t('Testi fase (HTML, il 1° è lo stato zero)'), type: 'content-items', itemLabel: t('Fase'),
      defaults: { text: 'Fase…' },
      itemFields: [ { key: 'text', label: t('Testo'), type: 'text' } ],
      condition: { field: 'variant', op: 'eq', value: 'assembler' } },
    { type: 'separator', label: t('Giornata') },
    { key: 'day_label', label: t('Etichetta occupazione'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'day' } },
    { key: 'day_hint', label: t('Hint scroll'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'day' } },
    { key: 'day_stamp', label: t('Testo timbro'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'day' } },
    { key: 'day_slots', label: t('Slot della giornata'), type: 'content-items', itemLabel: t('Slot'),
      defaults: { hh: '09:00', what: 'Appuntamento', who: 'verticale' },
      itemFields: [
        { key: 'hh', label: t('Ora'), type: 'text' },
        { key: 'what', label: t('Cosa'), type: 'text' },
        { key: 'who', label: t('Verticale'), type: 'text' },
      ],
      condition: { field: 'variant', op: 'eq', value: 'day' } },
  ],

  styleFields: [ oloxAccentField() ],
};
