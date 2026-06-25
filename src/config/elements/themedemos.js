import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';

/**
 * Demo temi — rail orizzontale di card con mini-preview PARAMETRICA di un tema:
 * il riquadro anteprima è disegnato (logo quadratino, headline nel font del tema,
 * bottone pill, badge zona blur) coi colori bg/ink/accent dell'item — niente
 * screenshot. I colori e il font degli item sono CONTENUTO (rappresentano il tema
 * mostrato), il guscio (card, footer, bordi) segue i token del sito. Scroll-snap
 * orizzontale, hover lift, nessun JS runtime. Render Vue == PHP
 * (ThemeDemosTile.vue). Estratta dal blueprint "Clod — Evoluzione v2" (.rs__demos).
 */
export default {
  type: 'themedemos',
  name: t('Demo temi (mini anteprime)'),
  icon: 'dashicons-welcome-view-site',
  category: 'media',

  defaults: {
    items: [
      { name: 'Forge', category: 'Software & Tech', zone_label: 'Contrast', bg: 'var(--olo-color-dark, #16263d)', ink: '#f4f4f4', accent: '#ff6a2b', font_label: 'Big Shoulders Display', light: false, link: '' },
      { name: 'Prisma', category: 'Creative', zone_label: 'Palette', bg: '#160a24', ink: '#f1e9f7', accent: '#c14bff', font_label: 'Big Shoulders Display', light: false, link: '' },
      { name: 'Saffron', category: 'Food & Drink', zone_label: 'Floor plan', bg: '#f6efe2', ink: '#241a16', accent: '#c75d3a', font_label: 'Big Shoulders Display', light: true, link: '' },
      { name: 'Soundwave', category: 'Artist', zone_label: 'Sequencer', bg: '#0c0c10', ink: '#ffffff', accent: '#27e0a3', font_label: 'Big Shoulders Display', light: false, link: '' },
    ],
    accent: '',
    card_bg: '',
    card_border_color: '',
    card_border_hover_color: '',
    preview_height: 168,
    gap: 16,

    // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Temi') },
    { key: 'items', label: t('Card demo'), type: 'content-items',
      itemLabel: t('Tema'),
      newItemDefaults: { name: 'Nuovo tema', category: 'Categoria', zone_label: 'Zona', bg: '#121212', ink: '#f4f4f4', accent: '#C6F24E', font_label: 'Big Shoulders Display', light: false, link: '' },
      itemFields: [
        { key: 'name', label: t('Nome tema'), type: 'text' },
        { key: 'category', label: t('Categoria'), type: 'text' },
        { key: 'zone_label', label: t('Badge zona (in anteprima)'), type: 'text' },
        { key: 'bg', label: t('Sfondo anteprima'), type: 'color' },
        { key: 'ink', label: t('Colore titolo anteprima'), type: 'color' },
        { key: 'accent', label: t('Accento anteprima (logo + bottone)'), type: 'color' },
        { key: 'font_label', label: t('Font del tema (nome esatto)'), type: 'text',
          description: t('Font dell\'anteprima: rappresenta il tema mostrato, non segue i ruoli del sito.') },
        { key: 'light', label: t('Anteprima chiara (badge scuro)'), type: 'toggle' },
        { key: 'link', label: t('Link'), type: 'link' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Riga') },
    { key: 'gap', label: t('Spazio tra card (px)'), type: 'range', min: 8, max: 32, step: 2 },
    { key: 'preview_height', label: t('Altezza anteprima (px)'), type: 'number', min: 100, max: 320 },

    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (anello focus)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color',
      description: t('Vuoto = superficie attenuata del tema.') },
    withHover({ key: 'card_border_color', label: t('Bordo card'), type: 'color',
      description: t('Vuoto = bordo del tema.') }, { hoverKey: 'card_border_hover_color' }),

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
