import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Lessons — percorso lezioni che si sbloccano scendendo (pagina tutor).
 * Render Vue == PHP (OloxLessonsTile.vue / class-oloxlessons-tile.php).
 */
export default {
  type: 'oloxlessons',
  name: t('OLOX — Percorso lezioni'),
  icon: 'dashicons-welcome-learn-more',
  category: 'marketing',

  defaults: {
    accent: 'tutor',
    anchor: 'lezioni',
    kicker: 'Il percorso',
    title_html: 'Le lezioni si <em>sbloccano</em> scendendo',
    lock_text: 'scendi per sbloccare',
    items: [
      { xp: '+120 xp', title: 'Corsi & lezioni', text_html: 'Strutture di corso, lezioni ordinate, area allievi.' },
      { xp: '+180 xp', title: 'Quiz & gamification', text_html: 'Quiz, mini-giochi, punti e badge.' },
    ],
  },

  // Come nasce dalla palette: il programma di un corso in quattro lezioni lungo il percorso
  // tratteggiato, coi nodi numerati, i punti di ogni lezione e il lucchetto che si apre scendendo.
  partenza: {
    kicker: t('Corso base · 4 lezioni'),
    title_html: t('Si impara <em>una lezione</em> alla volta'),
    items: [
      { xp: '+80 xp', title: t('Le basi'), text_html: t('Gli strumenti, le parole del mestiere e un primo esercizio guidato da fare subito.') },
      { xp: '+120 xp', title: t('La pratica'), text_html: t('Tre esercizi con le soluzioni commentate passo per passo.') },
      { xp: '+150 xp', title: t('Il progetto'), text_html: t('Metti insieme quello che hai imparato in un lavoro tutto tuo.') },
      { xp: '+200 xp', title: t('La verifica finale'), text_html: t('Dieci domande e l\'attestato da scaricare.') },
    ],
  },

  fields: [
    { key: 'kicker', label: t('Kicker'), type: 'text' },
    { key: 'title_html', label: t('Titolo (HTML)'), type: 'textarea' },
    { key: 'lock_text', label: t('Testo lucchetto'), type: 'text' },
    { key: 'anchor', label: t('Ancora (id)'), type: 'text' },
    { key: 'items', label: t('Lezioni'), type: 'content-items', itemLabel: t('Lezione'),
      defaults: { xp: '+100 xp', title: 'Lezione', text_html: 'Testo…' },
      itemFields: [
        { key: 'xp', label: t('Badge XP'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'text_html', label: t('Testo (HTML)'), type: 'textarea' },
      ] },
  ],

  styleFields: [ oloxAccentField() ],
};
