import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Quiz — quiz a risposta singola con confetti e bonus XP (pagina tutor).
 * Render Vue == PHP (OloxQuizTile.vue / class-oloxquiz-tile.php).
 */
export default {
  type: 'oloxquiz',
  name: t('OLOX — Quiz verifica'),
  icon: 'dashicons-editor-help',
  category: 'marketing',

  defaults: {
    accent: 'tutor',
    anchor: 'quiz',
    kicker: 'Verifica finale',
    title_html: 'Un quiz <em>vero</em>, provalo',
    question_html: 'Dove vivono i tuoi corsi con <em>OLOtutor</em>?',
    answers: [
      { text: 'Su un marketplace, in fila coi concorrenti', ok: false },
      { text: 'Sul mio WordPress, con i miei allievi e i miei dati', ok: true },
      { text: 'In un cloud di terzi, a canone mensile', ok: false },
    ],
    hint: 'rispondi per guadagnare +90 xp',
    ok_html: 'esatto · <b>+90 xp</b> · badge sbloccato',
    ko_text: 'mmh, riprova: la risposta è nel nome della suite…',
    bonus: 90,
  },

  fields: [
    { key: 'kicker', label: t('Kicker'), type: 'text' },
    { key: 'title_html', label: t('Titolo (HTML)'), type: 'textarea' },
    { key: 'question_html', label: t('Domanda (HTML)'), type: 'textarea' },
    { key: 'answers', label: t('Risposte'), type: 'content-items', itemLabel: t('Risposta'),
      defaults: { text: 'Risposta', ok: false },
      itemFields: [
        { key: 'text', label: t('Testo'), type: 'text' },
        { key: 'ok', label: t('Risposta giusta'), type: 'toggle' },
      ] },
    { key: 'hint', label: t('Testo iniziale verdetto'), type: 'text' },
    { key: 'ok_html', label: t('Verdetto giusto (HTML)'), type: 'text' },
    { key: 'ko_text', label: t('Verdetto sbagliato'), type: 'text' },
    { key: 'bonus', label: t('Bonus XP'), type: 'number' },
    { key: 'anchor', label: t('Ancora (id)'), type: 'text' },
  ],

  styleFields: [ oloxAccentField() ],
};
