import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { t } from '@/i18n';

/**
 * Tile PresenceGrid — griglia membri con stato online live (famiglia E · bucket C).
 * Riferimento: handoff-tile-speciali/temi/60-tema-community-gamer.html (#wall).
 *
 *   fields[]      → source (manual/query/endpoint), members[] (repeater: name+avatar+role+online),
 *                   pollInterval, endpoint_url, show_ranks, ticker (show+text), columns (responsive)
 *   styleFields[] → preset, card bg/colore, pallino online/offline + colori, avatar dimensione/forma,
 *                   tipografia (nome/ruolo), raggio card + hover, ticker aspetto, bordi
 *
 * Contratto §2:
 * - tutto parametrico (zero hardcode); colori via token / color picker
 * - SSR: la griglia è renderizzata server-side già visibile; il JS arricchisce (poll/flip)
 * - lo stato online è comunicato ANCHE a testo ("Online"/"Offline") → a11y, non solo col pallino
 * - chiavi salvate additive
 */
export default {
  type: 'presencegrid',
  name: t('Griglia Presenze'),
  icon: 'dashicons-groups',
  category: 'dynamic',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },

    // ── Origine dati ──
    source: 'manual',
    endpoint_url: '',
    poll_interval: 4000,

    members: [
      { id: 'pg-1', name: 'KiraByte',   avatar: '', role: 'Diamante', online: true,  color: '' },
      { id: 'pg-2', name: 'nott2late',  avatar: '', role: 'Platino',  online: true,  color: '' },
      { id: 'pg-3', name: 'pixelmom',   avatar: '', role: 'Oro',      online: true,  color: '' },
      { id: 'pg-4', name: 'grumpyTank', avatar: '', role: 'Argento',  online: false, color: '' },
      { id: 'pg-5', name: 'luna404',    avatar: '', role: 'Maestro',  online: true,  color: '' },
      { id: 'pg-6', name: 'frostbyte',  avatar: '', role: 'Bronzo',   online: false, color: '' },
      { id: 'pg-7', name: 'wasd_',      avatar: '', role: 'Oro',      online: true,  color: '' },
      { id: 'pg-8', name: 'glhf',       avatar: '', role: 'Platino',  online: true,  color: '' },
    ],

    // ── Layout ──
    columns: 6,
    columns_tablet: 4,
    columns_mobile: 2,
    gap: 14,

    show_ranks: true,

    // ── Etichette stato (testuali, a11y) ──
    online_label: 'Online',
    offline_label: 'Offline',

    // ── Ticker attività (opzionale) ──
    show_ticker: false,
    ticker_text: '@KiraByte ha sbloccato Diamante I · torneo FIFA Cup domenica 21:00 · @pixelmom ha vinto MVP · nuovo record clan: +18.000 XP',
    ticker_speed: 26,
    ticker_bg: '',
    ticker_color: '',

    // ── Aspetto card ──
    card_bg: '',
    card_color: '',
    role_color: '',
    avatar_size: 54,
    avatar_shape: 'circle',
    online_color: '',
    offline_color: '',
    dot_size: 13,

    name_size: 14,
    name_weight: '600',
    role_size: 10,

    card_radius: { tl: 14, tr: 14, br: 14, bl: 14, linked: true },
    card_radius_hover: null,
    card_radius_hover_duration: 300,

    card_hover_effect: 'lift',

    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ═══ CONTENUTO ═══════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Origine dati') },
    { key: 'source', label: t('Sorgente'), type: 'select', options: [
      { value: 'manual',   label: t('Manuale (elenco sotto)') },
      { value: 'query',    label: t('Query utenti del sito') },
      { value: 'endpoint', label: t('Endpoint REST (poll live)') },
    ], description: t('Manuale = elenco curato qui sotto. Query/Endpoint aggiornano lo stato in tempo reale; se non rispondono, resta lo stato demo qui sotto.') },
    { key: 'endpoint_url', label: t('URL endpoint'), type: 'text',
      placeholder: t('es. /wp-json/olobuild/v1/presence'),
      description: t('Deve restituire JSON: array di { name, avatar, role, online } o { members: [...] }.'),
      condition: { field: 'source', op: 'eq', value: 'endpoint' } },
    { key: 'poll_interval', label: t('Intervallo aggiornamento (ms)'), type: 'range', min: 2000, max: 60000, step: 1000,
      description: t('Ogni quanto ricontrollare lo stato. Debounced; si ferma fuori dal viewport.'),
      condition: { field: 'source', op: 'in', value: ['query', 'endpoint'] } },

    { type: 'separator', label: t('Membri') },
    { key: 'members', label: t('Membri'), type: 'content-items',
      itemLabel: t('Membro'),
      defaults: { name: t('Nuovo membro'), avatar: '', role: '', online: true, color: '' },
      newItemDefaults: { name: t('Nuovo membro'), avatar: '', role: '', online: true, color: '' },
      itemFields: [
        { key: 'name',   label: t('Nome / username'), type: 'text' },
        { key: 'avatar', label: t('Avatar'), type: 'image' },
        { key: 'role',   label: t('Ruolo / rango (opzionale)'), type: 'text', placeholder: t('es. Diamante') },
        { key: 'online', label: t('Online'), type: 'toggle' },
        { key: 'color',  label: t('Colore avatar (override)'), type: 'color' },
      ],
      description: t('Usato come elenco (sorgente Manuale) oppure come stato demo/placeholder quando query/endpoint non rispondono.'),
    },

    { type: 'separator', label: t('Stato (testo accessibile)') },
    { key: 'online_label',  label: t('Etichetta "online"'),  type: 'text',
      description: t('Lo stato è comunicato anche a parole, non solo col colore del pallino.') },
    { key: 'offline_label', label: t('Etichetta "offline"'), type: 'text' },

    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 10, step: 1, responsive: true },
    { key: 'columns_tablet', label: t('Colonne (tablet)'), type: 'range', min: 1, max: 8, step: 1 },
    { key: 'columns_mobile', label: t('Colonne (mobile)'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'gap', label: t('Spazio tra card (px)'), type: 'range', min: 0, max: 40, step: 2 },
    { key: 'show_ranks', label: t('Mostra ruolo / rango'), type: 'toggle' },

    { type: 'separator', label: t('Ticker attività (opzionale)') },
    { key: 'show_ticker', label: t('Mostra ticker sopra la griglia'), type: 'toggle' },
    { key: 'ticker_text', label: t('Testo ticker'), type: 'textarea',
      condition: { field: 'show_ticker', op: 'eq', value: true } },
    { key: 'ticker_speed', label: t('Durata ciclo ticker (s)'), type: 'range', min: 8, max: 80, step: 2,
      condition: { field: 'show_ticker', op: 'eq', value: true } },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
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

    { type: 'separator', label: t('Card') },
    { key: 'card_bg',    label: t('Sfondo card'),  type: 'color' },
    { key: 'card_color', label: t('Colore nome'),  type: 'color' },
    { key: 'role_color', label: t('Colore ruolo'), type: 'color' },
    withHover({ key: 'card_radius', label: t('Raggio bordi card (px)'), type: 'border-radius' },
      { hoverKey: 'card_radius_hover', hoverDurationKey: 'card_radius_hover_duration' }),
    { key: 'card_hover_effect', label: t('Effetto hover card'), type: 'select', options: [
      { value: 'none',  label: t('Nessuno') },
      { value: 'lift',  label: t('Sollevamento') },
      { value: 'scale', label: t('Scala') },
      { value: 'glow',  label: t('Bagliore bordo') },
    ]},

    { type: 'separator', label: t('Avatar') },
    { key: 'avatar_size', label: t('Dimensione avatar (px)'), type: 'range', min: 32, max: 96, step: 2 },
    { key: 'avatar_shape', label: t('Forma avatar'), type: 'select', options: [
      { value: 'circle',  label: t('Cerchio') },
      { value: 'rounded', label: t('Angoli arrotondati') },
      { value: 'square',  label: t('Quadrato') },
    ]},

    { type: 'separator', label: t('Pallino stato') },
    { key: 'online_color',  label: t('Colore online'),  type: 'color' },
    { key: 'offline_color', label: t('Colore offline'), type: 'color' },
    { key: 'dot_size', label: t('Dimensione pallino (px)'), type: 'range', min: 6, max: 22, step: 1 },

    { type: 'separator', label: t('Tipografia') },
    { key: 'name_size', label: t('Dimensione nome (px)'), type: 'range', min: 10, max: 24, step: 1 },
    { key: 'name_weight', label: t('Peso nome'), type: 'select', options: [
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
    ]},
    { key: 'role_size', label: t('Dimensione ruolo (px)'), type: 'range', min: 8, max: 16, step: 1 },

    { type: 'separator', label: t('Ticker — Aspetto'),
      condition: { field: 'show_ticker', op: 'eq', value: true } },
    { key: 'ticker_bg', label: t('Sfondo ticker'), type: 'color',
      condition: { field: 'show_ticker', op: 'eq', value: true } },
    { key: 'ticker_color', label: t('Colore testo ticker'), type: 'color',
      condition: { field: 'show_ticker', op: 'eq', value: true } },

    ...borderFields(),
  ],
};
