import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, widgetTemplateField, wowEffectsFields, wowEffectsDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile IconTabs — split CONTENUTO/STILE.
 *   fields[]      → schede (icon+label+heading+title+content+link), default_index
 *   styleFields[] → preset, bg, typo, effetti, text-effects, pill/card colori, bordo card, border
 */
export default {
  type: 'icontabs',
  name: t('Tab a Icone'),
  icon: 'dashicons-menu-alt',
  category: 'interactive',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    items: [
      { id: 'it-1', icon: 'location', label: t('Viaggi'), heading: 'HeyConad Viaggi', title: t('Catalogo vacanze di primavera ed estate'), content: 'Destinazioni da sogno? Prenota subito la tua vacanza ideale.', link_text: 'Scopri tutte le offerte', link_url: '#' },
      { id: 'it-2', icon: 'tablet',   label: t('Mobile'), heading: 'HeyConad Mobile',  title: t('Tariffe mobile esclusive'),            content: 'Naviga e chiama senza pensieri a prezzi vantaggiosi.', link_text: 'Scopri le tariffe',       link_url: '#' },
      { id: 'it-3', icon: 'lock',     label: t('Tutela'), heading: 'HeyConad Tutela',  title: t('Assicurazioni pensate per te'),        content: 'Protezione per casa, persona e animali.',             link_text: 'Scopri i prodotti',       link_url: '#' },
    ],
    preset: 'pill-default',
    pill_bg: '#F5F2EB',
    active_bg: '',            // '' ⇒ primary (era #e1474f off-brand)
    active_color: '',         // '' ⇒ on-primary
    inactive_color: '',       // '' ⇒ text
    card_bg: '#F9D7D7',
    card_radius: '16',
    card_border: { ...borderDefault },
    card_border_hover: { ...borderHoverDefault },
    card_border_hover_duration: 300,
    heading_color: '',        // '' ⇒ primary (era #e1474f off-brand)
    title_color: '',          // '' ⇒ text
    text_color: '',           // '' ⇒ text-soft
    link_color: '',           // '' ⇒ token link
    default_index: '0',
    effect_color: '',
    effect_intensity: 'medium',
    effect_speed: 0,
    ...textEffectsDefaults,
    text_effect_target: 'label',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...wowEffectsDefaults,
  },

  fields: [
    { key: 'items', label: t('Schede'), type: 'content-items',
      itemFields: [
        { key: 'icon', label: t('Icona'), type: 'icon' },
        { key: 'label', label: t('Etichetta attiva'), type: 'text' },
        { type: 'separator', label: t('Contenuto scheda') },
        widgetTemplateField,
        { key: 'heading', label: t('Occhiello'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'content', label: t('Testo'), type: 'textarea' },
        { key: 'link_text', label: t('Testo link'), type: 'text' },
        { key: 'link_url', label: t('URL link'), type: 'link' },
      ],
      newItemDefaults: { icon: 'star', label: t('Nuova'), heading: '', title: '', content: '', link_text: '', link_url: '#', widget_template_id: 0 },
    },

    { type: 'separator', label: t('Comportamento') },
    { key: 'default_index', label: t('Scheda attiva iniziale (0-based)'), type: 'number', min: 0, step: 1 },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stile') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'pill-default',     label: t('Pill Default (default)') },
      { value: 'pill-subtle',      label: t('Pill Subtle (bianco)') },
      { value: 'underline-bar',    label: t('Underline Bar (no pill)') },
      { value: 'brand-sharp',      label: t('Brand Sharp (squared)') },
      { value: 'card-centered',    label: t('Card Centered (full width)') },
      { value: 'liquid-glass',     label: t('Liquid Glass (Vision Pro)') },
      { value: 'neon-cyber',       label: t('Neon Cyberpunk (Tron)') },
      { value: 'brutalist-block',  label: t('Brutalist Block (neo-brutalist)') },
      { value: 'magnetic-liquid',  label: t('Magnetic Liquid (next-gen)') },
      { value: 'sticker',          label: t('Sticker / Scrapbook') },
      { value: 'retro-terminal',   label: t('Retro Terminal (CRT)') },
      { value: '3d-tilt',          label: t('3D Card Tilt') },
      { value: 'custom',           label: t('Personalizzato (usa controlli sotto)') },
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
    { key: 'effect_speed', label: t('Velocità animazioni (ms)'), type: 'range',
      min: 0, max: 4000, step: 100,
      condition: { field: 'preset', op: 'in', value: ['neon-cyber','magnetic-liquid','retro-terminal','3d-tilt'] } },

    ...textEffectsFields([
      { value: 'label', label: t('Solo Etichetta') },
      { value: 'heading', label: t('Solo Occhiello') },
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Tab'),
      presetKey: 'typography_preset',
      keys: {
        color: 'active_color',
      },
    },
    { type: 'typography', label: t('Occhiello'),
      presetKey: 'typography_preset',
      keys: {
        color: 'heading_color',
      },
    },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      keys: {
        color: 'title_color',
      },
    },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      keys: {
        color: 'text_color',
      },
    },
    { type: 'typography', label: t('Link'),
      presetKey: 'typography_preset',
      keys: {
        color: 'link_color',
      },
    },

    { type: 'separator', label: t('Stile pill') },
    { key: 'pill_bg', label: t('Sfondo pill'), type: 'color' },
    { key: 'active_bg', label: t('Sfondo attivo'), type: 'color' },
    { key: 'inactive_color', label: t('Icone inattive'), type: 'color' },

    { type: 'separator', label: t('Stile card') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    withHover({ key: 'card_radius', label: t('Raggio card'), type: 'border-radius'}),
    withHover(
      { key: 'card_border', label: t('Bordo card'), type: 'border' },
      { hoverKey: 'card_border_hover', hoverDurationKey: 'card_border_hover_duration' }
    ),

    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
