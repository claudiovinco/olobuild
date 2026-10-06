import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, focalField } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';
import { demo } from '../demoMedia.js';
import { PIATTAFORME_SOCIAL } from './social.js';

/**
 * Tile Link in Bio — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (link), dati profilo (immagine, nome, bio), toggle show_social_icons
 *   styleFields[] → preset, bg, typography_preset, textEffectsFields, layout (max_width, gap, text_align),
 *                   colori profilo e bottoni, padding tile, border-radius bottoni, sfondo,
 *                   shadow, borderFields
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'linkinbio',
  name: t('Link in Bio'),
  icon: 'dashicons-smartphone',
  category: 'marketing',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    items: [
      { id: 'lib-1', title: t('Sito web'),    url: 'https://example.com', icon: 'globe',     image_url: '', style: 'filled' },
      { id: 'lib-2', title: t('Portfolio'),    url: 'https://example.com', icon: 'briefcase', image_url: '', style: 'filled' },
      { id: 'lib-3', title: t('Instagram'),    url: 'https://instagram.com', icon: 'instagram', image_url: '', style: 'filled' },
      { id: 'lib-4', title: t('Contattami'),   url: 'mailto:info@example.com', icon: 'mail', image_url: '', style: 'outline' },
    ],
    profile_image: '',
    // 'center center' = quel che il browser applica già da sé (object-position
    // iniziale 50% 50%): il controllo nasce senza spostare nessun avatar.
    profile_image_object_position: 'center center',
    profile_name: 'Il tuo nome',
    profile_bio: 'Una breve descrizione qui',
    max_width: '420',
    link_color: '',
    link_bg: '',
    link_hover_bg: '',
    link_border_radius: '12',
    tile_padding: { top: 14, right: 14, bottom: 14, left: 14 },
    gap: '12',
    text_align: 'center',
    profile_name_color: '',
    bio_color: '',
    background_color: '',
    background_gradient: '',
    show_social_icons: false,
    social_links: [],
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: la pagina «link in bio» di un locale — foto profilo, nome e una riga di
  // presentazione, poi i link a pillola con le loro icone (menu, prenotazione, mappa,
  // Instagram) e l'ultimo a contorno, su un fondo tenue nel colore primario.
  partenza: {
    items: [
      { id: 'lib-1', title: t('Il nostro menu'), url: '#', icon: 'file-text', image_url: '', style: 'filled' },
      { id: 'lib-2', title: t('Prenota un tavolo'), url: '#', icon: 'calendar', image_url: '', style: 'filled' },
      { id: 'lib-3', title: t('Come arrivare'), url: '#', icon: 'location', image_url: '', style: 'filled' },
      { id: 'lib-4', title: t('Seguici su Instagram'), url: 'https://www.instagram.com/', icon: 'instagram', image_url: '', style: 'filled' },
      { id: 'lib-5', title: t('Scrivici'), url: 'mailto:info@example.com', icon: 'mail', image_url: '', style: 'outline' },
    ],
    profile_image: demo('caffe'),
    profile_name: t('Caffè Centrale'),
    profile_bio: t('Colazioni, pranzi veloci e aperitivi in centro'),
    link_bg: 'var(--olo-color-background)',
    link_hover_bg: 'color-mix(in srgb, var(--olo-color-primary) 12%, var(--olo-color-background))',
    link_border_radius: '999',
    bio_color: 'var(--olo-color-text-muted)',
    background_color: 'color-mix(in srgb, var(--olo-color-primary) 9%, var(--olo-color-background))',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'items', label: t('Link'), type: 'content-items',
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'url', label: t('URL'), type: 'link' },
        { key: 'icon', label: t('Icona (nome)'), type: 'icon' },
        { key: 'image_url', label: t('Immagine icona'), type: 'image' },
      ],
    },
    { type: 'separator', label: t('Profilo') },
    { key: 'profile_image', label: t('Foto profilo'), type: 'image' },
    { key: 'profile_name', label: t('Nome'), type: 'text' },
    { key: 'profile_bio', label: t('Bio'), type: 'text' },
    { type: 'separator', label: t('Social') },
    { key: 'show_social_icons', label: t('Mostra icone social'), type: 'toggle' },
    // La riga di icone sotto la bio: prima il toggle non agiva (non c'era dove scrivere i
    // profili). Stesse piattaforme, icone e nomi della tile Link social.
    { key: 'social_links', label: t('Profili social'), type: 'content-items',
      condition: { field: 'show_social_icons', operator: '==', value: true },
      itemLabel: t('Profilo'), newItemDefaults: { platform: 'instagram', url: '' },
      itemFields: [
        { key: 'platform', label: t('Piattaforma'), type: 'select', options: PIATTAFORME_SOCIAL },
        { key: 'url', label: t('URL'), type: 'link', placeholder: t('https://...') },
      ],
    },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    ...textEffectsFields([ { value: 'title', label: t('Solo Titolo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Profilo'),
      responsiveKeys: [],
      keys: {
        color: 'profile_name_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Link'),
      responsiveKeys: [],
      keys: {
        color: 'link_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    // La cornice dell'avatar è un cerchio di 80px a lato fisso: proporzioni e
    // adattamento non hanno senso (16:9 su un avatar non vuol dire niente, il fit
    // resta 'cover'). Serve invece il punto focale: oggi un viso decentrato viene
    // tagliato male e non c'è alcun modo di correggerlo.
    { type: 'separator', label: t('Foto profilo') },
    focalField('profile_image', { fit: 'cover', ratio: '1/1',
      condition: { field: 'profile_image', op: 'notEmpty' } }),

    { type: 'separator', label: t('Colore bio') },
    { key: 'bio_color', label: t('Colore bio'), type: 'color' },

    { type: 'separator', label: t('Layout') },
    { key: 'max_width', label: t('Larghezza max'), type: 'range', min: 300, max: 600, step: 10 },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'gap', label: t('Gap link'), type: 'range', min: 4, max: 24, step: 2 },

    { type: 'separator', label: t('Stile bottoni') },
    { key: 'link_bg', label: t('Sfondo link'), type: 'color' },
    { key: 'link_hover_bg', label: t('Sfondo hover'), type: 'color' },
    withHover({ key: 'link_border_radius', label: t('Raggio'), type: 'border-radius' }),
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 24 },

    { type: 'separator', label: t('Sfondo') },
    { key: 'background_color', label: t('Colore sfondo'), type: 'color' },
    { key: 'background_gradient', label: t('Gradiente CSS'), type: 'text', placeholder: t('linear-gradient(135deg, #667eea, #764ba2)') },

    ...shadowField,
    ...borderFields(),
    { type: 'separator', label: t('Link') },
    { key: 'items', type: 'content-items', label: t('Link'), etichettaDa: 'title', miniaturaDa: 'image_url', itemFields: [
        { key: 'style', label: t('Stile'), type: 'select', options: [
          { value: 'filled', label: t('Pieno') },
          { value: 'outline', label: t('Contorno') },
          { value: 'minimal', label: t('Minimale') },
        ]},
    ] },
  ],
};
