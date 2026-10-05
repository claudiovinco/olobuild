import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, widgetTemplateField, wowEffectsFields, wowEffectsDefaults } from './_shared';
import { shadowField, shadowDefaults } from './_shared.js';
import { demo } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Tile Switcher — split CONTENUTO/STILE.
 *   fields[]      → items (title+content), vertical, animation+duration
 *   styleFields[] → preset, bg, typo, effetti, tab typo & spacing, container, colori tab, indicator, contenuto pannello, text-effects, shadow, border
 *   (nav_style resta nei defaults: lo stile della barra lo decidono preset e «Tipo indicatore»)
 */
export default {
  type: 'switcher',
  name: t('Switcher'),
  icon: 'dashicons-welcome-widgets-menus',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    items: [
      { id: 'sw-1', title: t('Panoramica'),  content: 'Una panoramica chiara e sintetica di ciò che offri. Aggiungi qui il tuo testo introduttivo.' },
      { id: 'sw-2', title: t('Caratteristiche'), content: 'Le funzionalità principali del prodotto o servizio. Usa questo spazio per evidenziare i vantaggi.' },
      { id: 'sw-3', title: t('FAQ'),         content: 'Risposte alle domande più frequenti dei tuoi utenti. Sintetiche, dirette, utili.' },
    ],
    preset: 'pill-slide',
    nav_style: 'tab',
    animation: 'fade',
    animation_duration: '250',
    vertical: false,
    tab_padding_y: '10',
    tab_padding_x: '18',
    tab_font_size: '14',
    tab_font_weight: '500',
    tab_gap: '4',
    tab_radius: '8',
    container_bg: 'var(--olo-color-surface, #ffffff)',
    container_padding: '4',
    container_radius: '10',
    active_bg: 'var(--olo-color-surface, #ffffff)',
    active_color: 'var(--olo-color-text, #1f2937)',
    inactive_color: 'var(--olo-color-text-soft, #6b7280)',
    hover_bg: '',
    indicator_type: 'none',
    indicator_color: '',
    content_bg: '',
    content_color: 'var(--olo-color-text, #1f2937)',
    content_padding_y: '20',
    content_padding_x: '0',
    ...shadowDefaults,
    effect_color: '',
    effect_intensity: 'medium',
    effect_speed: 0,
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...wowEffectsDefaults,
  },

  // Come nasce dalla palette: tre schede con icona in una barra a pillola tinta col primario (la
  // scheda attiva si stacca con la sua ombra, come dice il preset «Pill Sliding»); ogni pannello
  // ha il testo e la foto accanto.
  partenza: {
    items: [
      { id: 'sw-1', title: t('Chi siamo'), icon: 'users', content: t('<p><strong>Una squadra piccola, una cura grande.</strong> Lavoriamo insieme dal 2015 e seguiamo ogni cliente dall\'inizio alla fine, con un referente unico.</p>'), image: demo('ufficio'), link_text: '', link_url: '', widget_template_id: 0 },
      { id: 'sw-2', title: t('Cosa facciamo'), icon: 'lightbulb', content: t('<p><strong>Progetti su misura.</strong> Ascoltiamo le tue esigenze, proponiamo una soluzione chiara e la realizziamo nei tempi concordati.</p>'), image: demo('scrivania'), link_text: '', link_url: '', widget_template_id: 0 },
      { id: 'sw-3', title: t('Dove trovarci'), icon: 'location', content: t('<p><strong>In centro, a due passi dalla stazione.</strong> Riceviamo su appuntamento dal lunedì al venerdì, dalle 9 alle 18.</p>'), image: demo('citta-tetti'), link_text: '', link_url: '', widget_template_id: 0 },
    ],
    indicator_type: 'pill',
    tab_padding_x: '22',
    tab_font_size: '15',
    tab_font_weight: '600',
    tab_radius: '999',
    container_padding: '6',
    container_radius: '999',
    active_bg: 'var(--olo-color-background, #ffffff)',
    hover_bg: 'color-mix(in srgb, var(--olo-color-primary) 8%, transparent)',
    content_padding_y: '28',
    // Conflitti PHP/config, risolti coi token veri (surface e text-soft la Palette non li genera).
    container_bg: 'color-mix(in srgb, var(--olo-color-primary) 10%, transparent)',
    active_color: 'var(--olo-color-primary)',
    inactive_color: 'var(--olo-color-text-muted)',
    content_color: 'var(--olo-color-text)',
  },

  fields: [
    { key: 'items', label: t('Elementi'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'icon', label: t('Icona'), type: 'icon',
          description: t('Facoltativa: compare prima del titolo della scheda.') },
        widgetTemplateField,
        { key: 'content', label: t('Contenuto'), type: 'editor', mode: 'block' },
        { key: 'image', label: t('Immagine'), type: 'image',
          description: t('Facoltativa: compare accanto al testo del pannello (sotto, sui telefoni).') },
        { key: 'link_text', label: t('Testo del pulsante'), type: 'text' },
        { key: 'link_url', label: t('Collegamento del pulsante'), type: 'link',
          description: t('Il pulsante compare sotto il testo quando ci sono sia il testo sia il collegamento.') },
      ],
      newItemDefaults: { title: t('Nuova scheda'), content: 'Contenuto della scheda.', widget_template_id: 0, icon: '', image: '', link_text: '', link_url: '' },
      itemLabel: 'Scheda',
    },

    { type: 'separator', label: t('Layout') },
    { key: 'vertical', label: t('Verticale'), type: 'toggle' },

    { type: 'separator', label: t('Animazione') },
    { key: 'animation', label: t('Animazione'), type: 'select', options: [
      { value: '', label: t('Nessuna') },
      { value: 'fade', label: t('Fade') },
      { value: 'slide-left', label: t('Slide sinistra') },
      { value: 'slide-right', label: t('Slide destra') },
      { value: 'slide-top', label: t('Slide alto') },
      { value: 'slide-bottom', label: t('Slide basso') },
    ]},
    { key: 'animation_duration', label: t('Durata transizioni'), type: 'range', min: 100, max: 800, step: 50 },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stile') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'pill-slide',         label: t('Pill Sliding (Linear/Apple)') },
      { value: 'underline-animated', label: t('Underline Animated (Stripe)') },
      { value: 'card-tabs',          label: t('Card Tabs') },
      { value: 'minimal-text',       label: t('Minimal Text') },
      { value: 'vertical-sidebar',   label: t('Vertical Sidebar (Spotify/Notion)') },
      { value: 'liquid-glass',       label: t('Liquid Glass (Vision Pro)') },
      { value: 'neon-cyber',         label: t('Neon Cyberpunk (Tron)') },
      { value: 'brutalist-block',    label: t('Brutalist Block (neo-brutalist)') },
      { value: 'magnetic-liquid',    label: t('Magnetic Liquid (next-gen)') },
      { value: 'sticker',            label: t('Sticker / Scrapbook') },
      { value: 'retro-terminal',     label: t('Retro Terminal (CRT)') },
      { value: '3d-tilt',            label: t('3D Card Tilt') },
      { value: 'custom',             label: t('Personalizzato (usa controlli sotto)') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Tweak effetto preset'),
      condition: { field: 'preset', op: 'in', value: ['liquid-glass','neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal','3d-tilt'] } },
    { key: 'effect_color', label: t('Colore effetto'), type: 'color',
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal'] } },
    { key: 'effect_intensity', label: t('Intensità effetto'), type: 'select',
      options: [
        { value: 'low',    label: t('Bassa') },
        { value: 'medium', label: t('Media (default)') },
        { value: 'high',   label: t('Alta') },
      ],
      condition: { field: 'preset', op: 'in', value: ['liquid-glass','neon-cyber','brutalist-block','magnetic-liquid','sticker','retro-terminal','3d-tilt'] } },
    { key: 'effect_speed', label: t('Velocità animazioni'), type: 'range',
      min: 0, max: 4000, step: 100,
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','magnetic-liquid','retro-terminal','3d-tilt'] } },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Tab'),
      responsiveKeys: [],
      keys: {
        size:       'tab_font_size',
        weight:     'tab_font_weight',
        color:      'active_color',
      },
      sizeMin: 11, sizeMax: 20, sizeStep: 1,
    },
    { type: 'typography', label: t('Contenuto'),
      keys: {
        color: 'content_color',
      },
    },

    { type: 'separator', label: t('Tab — Spaziatura') },
    { key: 'tab_padding', label: t('Padding tab'), type: 'spacing', min: 0, max: 60,
      legacyKeys: { y: 'tab_padding_y', x: 'tab_padding_x' } },
    { key: 'tab_gap', label: t('Gap tab'), type: 'range', min: 0, max: 24, step: 1 },
    { key: 'tab_radius', label: t('Raggio tab'), type: 'border-radius' },

    { type: 'separator', label: t('Container nav') },
    { key: 'container_bg', label: t('Sfondo container'), type: 'color' },
    { key: 'container_padding', label: t('Padding container'), type: 'spacing', min: 0, max: 60 },
    { key: 'container_radius', label: t('Raggio container'), type: 'border-radius' },

    { type: 'separator', label: t('Colori tab') },
    { key: 'active_bg', label: t('Sfondo tab attiva'), type: 'color' },
    { key: 'inactive_color', label: t('Colore testo inattivo'), type: 'color' },
    { key: 'hover_bg', label: t('Sfondo tab hover'), type: 'color' },

    { type: 'separator', label: t('Indicatore') },
    { key: 'indicator_type', label: t('Tipo indicatore'), type: 'select', options: [
      { value: 'none',      label: t('Nessuno (solo bg attivo)') },
      { value: 'underline', label: t('Sottolineatura') },
      { value: 'overline',  label: t('Linea sopra') },
      { value: 'pill',      label: t('Pill di sfondo (default Pill Slide)') },
      { value: 'left-bar',  label: t('Barra sinistra (per Vertical)') },
    ]},
    { key: 'indicator_color', label: t('Colore indicatore'), type: 'color' },

    { type: 'separator', label: t('Contenuto pannello') },
    { key: 'content_bg', label: t('Sfondo contenuto'), type: 'color' },
    { key: 'content_padding', label: t('Padding contenuto'), type: 'spacing', min: 0, max: 80,
      legacyKeys: { y: 'content_padding_y', x: 'content_padding_x' } },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),
    ...shadowField,
    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
