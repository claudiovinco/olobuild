import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Mockup Builder — illustrazione animata dell'editor OLObuild (browser bar +
 * rail categorie + griglia tile + canvas + inspector) con una "tile fantasma" che
 * viene trascinata sul canvas in loop (effetto wow, solo CSS). Pensato per landing
 * "prova il builder" / hero marketing.
 *
 * Tutto è renderizzato lato server (SSR) con CSS scoped per istanza (UID): nessun
 * JS, l'animazione del drag è @keyframes; rispetta prefers-reduced-motion.
 * Colori dai ruoli globali del cliente (var(--olo-color-primary)) di default.
 */
export default {
  type: 'buildermock',
  name: t('Mockup Builder'),
  icon: 'dashicons-laptop',
  category: 'marketing',
  defaults: {
    accent: 'var(--olo-color-primary)',
    url_text: 'olobuild.it/editor',
    cat_active: 'Essenziale',
    canvas_title: 'Benvenuto al Resort delle Ville',
    canvas_sub: 'Una struttura immersa nel verde, a 10 minuti dal mare.',
    selected_tile: 'Titolo',
    drag_label: 'Titolo',
    animate_drag: true,
    tilt: 13,
    width: 840,

    shadow: 'xl',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Contenuto mockup') },
    { key: 'url_text', label: t('Barra indirizzo'), type: 'text' },
    { key: 'canvas_title', label: t('Titolo nel canvas'), type: 'text' },
    { key: 'canvas_sub', label: t('Sottotitolo nel canvas'), type: 'text' },
    { key: 'cat_active', label: t('Categoria attiva (sidebar)'), type: 'text' },
    { key: 'selected_tile', label: t('Tile selezionato'), type: 'text' },

    { type: 'separator', label: t('Drag chip (tile trascinato)') },
    { key: 'drag_label', label: t('Etichetta chip'), type: 'text' },
    { key: 'animate_drag', label: t('Anima il trascinamento'), type: 'toggle',
      description: t('La tile fantasma viene trascinata sul canvas in loop (solo CSS). Ferma con prefers-reduced-motion.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'accent', label: t('Colore accento'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'tilt', label: t('Inclinazione 3D (gradi)'), type: 'range', min: 0, max: 22, step: 1 },
    { key: 'width', label: t('Larghezza (px)'), type: 'range', min: 480, max: 1100, step: 20 },

    ...shadowField,
    ...borderFields(),
  ],
};
