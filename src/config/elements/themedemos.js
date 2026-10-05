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

  // Come nasce dalla palette: quattro modelli di sito per attività diverse, ognuno con la sua
  // mini-anteprima disegnata (colori presi dalla palette del sito in combinazioni diverse, un
  // carattere di sistema diverso, il badge della sezione più importante) e nome + categoria sotto.
  partenza: {
    items: [
      { name: t('Bottega'), category: t('Negozi e artigiani'), zone_label: t('Catalogo'), bg: 'var(--olo-color-dark, #16263d)', ink: 'var(--olo-color-light, #f8f9fa)', accent: 'var(--olo-color-primary, #e1474f)', font_label: 'Georgia', light: false, link: '' },
      { name: t('Studio'), category: t('Professionisti'), zone_label: t('Prenota'), bg: 'var(--olo-color-primary, #e1474f)', ink: 'var(--olo-color-primary-contrast, #ffffff)', accent: 'var(--olo-color-dark, #16263d)', font_label: 'Trebuchet MS', light: false, link: '' },
      { name: t('Trattoria'), category: t('Ristoranti'), zone_label: t('Menù'), bg: 'color-mix(in srgb, var(--olo-color-accent, #f4a23b) 22%, var(--olo-color-light, #fdfcfa))', ink: 'var(--olo-color-dark, #16263d)', accent: 'var(--olo-color-primary, #e1474f)', font_label: 'Palatino Linotype', light: true, link: '' },
      { name: t('Atelier'), category: t('Moda e design'), zone_label: t('Lookbook'), bg: 'var(--olo-color-secondary, #3d5a80)', ink: 'var(--olo-color-secondary-contrast, #ffffff)', accent: 'var(--olo-color-accent, #f4a23b)', font_label: 'Impact', light: false, link: '' },
    ],
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
        { key: 'font_label', label: t('Font del tema (nome esatto)'), type: 'text',
          description: t('Font dell\'anteprima: rappresenta il tema mostrato, non segue i ruoli del sito.') },
        { key: 'light', label: t('Anteprima chiara (badge scuro)'), type: 'toggle' },
        { key: 'link', label: t('Link'), type: 'link' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Riga') },
    { key: 'gap', label: t('Gap card'), type: 'range', min: 8, max: 32, step: 2 },
    { key: 'preview_height', label: t('Altezza anteprima'), type: 'number', min: 100, max: 320 },

    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (anello focus)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color',
      description: t('Vuoto = superficie attenuata del tema.') },
    withHover({ key: 'card_border_color', label: t('Bordo card'), type: 'border', legacyWidth: 1,
      description: t('Vuoto = bordo del tema.') }, { hoverKey: 'card_border_hover_color', defaultDuration: 180 }),

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
    { type: 'separator', label: t('Temi') },
    { key: 'items', type: 'content-items', label: t('Card demo'), itemLabel: t('Tema'), etichettaDa: 'name', itemFields: [
        { key: 'bg', label: t('Sfondo anteprima'), type: 'color' },
        { key: 'ink', label: t('Colore titolo anteprima'), type: 'color' },
        { key: 'accent', label: t('Accento anteprima (logo + bottone)'), type: 'color' },
    ] },
  ],
};
