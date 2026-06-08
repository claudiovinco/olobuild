/**
 * oloTileDefaults — default ESTETICI e centrati sul brand per le tile.
 *
 * DUE problemi che affronta
 * -------------------------
 * 1. I default oggi sono hardcoded e OFF-BRAND: ButtonTile nasce #6366F1 (indigo),
 *    Alert ha palette fisse, Divider grigi fissi. Il token --olo-color-primary
 *    esiste ma i fallback NON lo rispettano → una tile appena inserita non sembra
 *    "del brand" finché non la ritocchi.
 * 2. I default sono "tecnicamente validi" ma non CURATI esteticamente. L'utente
 *    vuole che inserendo una tile il risultato sia già visivamente piacevole.
 *
 * STRATEGIA
 * - Tutti i colori partono dai token globali del sito (con fallback sensati).
 * - Spaziature/raggi da una scala coerente (non numeri arbitrari per tile).
 * - Un'unica fonte per i default condivisi, così tutte le tile sono armoniche.
 *
 * ⚠️ Riferimento per Claude Code: vedi TOKEN_MAPPING.md per come il
 *    GlobalColorsPanel genera realmente le variabili. NON inventare nomi di
 *    variabile che il pannello non produce: legati SOLO ai 6 ruoli globali.
 */

/* ════════════════════════════════════════════════════════════════════
   TOKEN GLOBALI — legati alla palette del cliente (GlobalColorsPanel).
   Il pannello emette var(--olo-color-<id>) dai 6 ruoli seed: primary,
   secondary, accent, dark, light, text. I fallback qui = SEED ATTUALE del
   pannello (NON il brand rosso di OLObuild, che è un'altra cosa).
   ⚠️ l'id è derivato dalla label → instabile: vedi TOKEN_MAPPING.md §fragilità.
   ════════════════════════════════════════════════════════════════════ */
export const GLOBAL = {
  primary:   'var(--olo-color-primary, #e1474f)',    // rosso brand (decisione cliente)
  secondary: 'var(--olo-color-secondary, #16263d)',  // navy profondo — ancora il rosso
  accent:    'var(--olo-color-accent, #f4a23b)',      // ambra calda — armonica col rosso
  text:      'var(--olo-color-text, #1f2937)',
  dark:      'var(--olo-color-dark, #16263d)',
  light:     'var(--olo-color-light, #f8f9fa)',
};

/* ════════════════════════════════════════════════════════════════════
   TOKEN DI SISTEMA. Neutri fini = costanti (non personalizzabili).
   Semantici = ORA ruoli globali personalizzabili dal cliente (decisione):
   il cliente sceglie i 4 `fg`; la tinta `bg` (soft) è DERIVATA via color-mix,
   così resta coerente senza chiedere 8 colori. on-primary = gestito dallo
   store per contrasto (vedi contrastOn) con fallback bianco.
   ════════════════════════════════════════════════════════════════════ */
const soft = (v, hex) => `color-mix(in srgb, var(${v}, ${hex}) 12%, #fff)`;
export const SYSTEM = {
  onPrimary: 'var(--olo-color-on-primary, #ffffff)',
  textSoft:  '#6b7280',
  textFaint: '#94a3b8',
  surface:   '#ffffff',
  surfaceAlt:'#f6f7f9',
  border:    '#e5e7eb',
  info:    { fg: 'var(--olo-color-info, #2563eb)',    bg: soft('--olo-color-info', '#2563eb') },
  success: { fg: 'var(--olo-color-success, #15803d)', bg: soft('--olo-color-success', '#15803d') },
  warning: { fg: 'var(--olo-color-warning, #b45309)', bg: soft('--olo-color-warning', '#b45309') },
  error:   { fg: 'var(--olo-color-error, #b42318)',   bg: soft('--olo-color-error', '#b42318') },
};

/**
 * contrastOn(hex) — testo leggibile su uno sfondo dato (luminanza relativa sRGB).
 * Lo STORE dovrebbe chiamarlo quando il cliente cambia il primario e scrivere
 * il risultato in `--olo-color-on-primary`, così il testo dei Button resta
 * leggibile su QUALSIASI primario (rosso→bianco, giallo chiaro→testo scuro).
 */
export function contrastOn(hex, dark = '#1f2937', light = '#ffffff') {
  const c = String(hex || '').replace('#', '');
  if (c.length < 6) return light;
  const ch = (i) => parseInt(c.slice(i, i + 2), 16) / 255;
  const lin = (v) => (v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4));
  const L = 0.2126 * lin(ch(0)) + 0.7152 * lin(ch(2)) + 0.0722 * lin(ch(4));
  return L > 0.5 ? dark : light;
}

/* TOKENS — vista unificata per i consumer. `primary`/`text`/`secondary`/
   `accent` arrivano dai GLOBALI del cliente; il resto è di SISTEMA. */
export const TOKENS = {
  ...SYSTEM,
  primary:   GLOBAL.primary,
  secondary: GLOBAL.secondary,
  accent:    GLOBAL.accent,
  text:      GLOBAL.text,
};

/* Scala spaziature (8pt) e raggi — coerenti per tutte le tile. */
export const SPACE = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24, '2xl': 32, '3xl': 48 };
export const RADIUS = { none: 0, sm: 6, md: 10, lg: 14, pill: 999 };

/**
 * resolveColor — risolve un valore colore in modo TOKEN-FIRST:
 * se l'utente non ha scelto un colore, usa il token; mai un hex hardcoded.
 */
export function resolveColor(userValue, token) {
  return userValue && userValue !== '' ? userValue : token;
}

/**
 * Default curati per tipo di tile. Pensati per essere "belli appena inseriti":
 * primario del brand, testo leggibile, raggio e padding dalla scala condivisa.
 * Da usare come UNICA fonte (il componente NON deve ri-dichiarare i default).
 */
export const TILE_DEFAULTS = {
  button: {
    text: 'Clicca qui',
    bg_color: '',                 // '' ⇒ resolveColor() userà TOKENS.primary
    text_color: '',               // '' ⇒ TOKENS.onPrimary
    border_radius: RADIUS.md,     // 10, non 6
    tile_padding: { top: SPACE.md, right: SPACE.xl, bottom: SPACE.md, left: SPACE.xl },
    font_size: 16,
    font_weight: 600,
    shadow: 'sm',                 // micro-ombra elegante invece di 'none' piatto
    hover_effect: 'lift',
  },
  alert: {
    alert_type: 'info',
    title: 'Attenzione!',
    message: 'Questo è un avviso informativo.',
    show_icon: true,
    icon: 'info',                 // icona SVG dal set, NON emoji
    border_radius: RADIUS.md,
    // colori risolti a runtime da TOKENS[alert_type]
  },
  divider: {
    style: 'solid',
    width: 100,
    thickness: 1,
    color: '',                    // '' ⇒ TOKENS.border
    spacing: SPACE.lg,
  },
  hero: {
    title: 'Benvenuto nel nostro sito',
    subtitle: 'Scopri qualcosa di straordinario',
    text_color: '',               // '' ⇒ TOKENS.onPrimary (sovrapposto a bg)
    min_height: '500px',
    tile_padding: { top: SPACE['3xl'], right: SPACE.xl, bottom: SPACE['3xl'], left: SPACE.xl },
    cta_radius: { tl: RADIUS.md, tr: RADIUS.md, br: RADIUS.md, bl: RADIUS.md },
  },
};

/**
 * buildDefaults(type) — unica fonte dei default. Il Tile e il config registry
 * dovrebbero entrambi leggere da qui, NON ridichiararli (vedi nota DRY nel README).
 */
export function buildDefaults(type) {
  return { ...(TILE_DEFAULTS[type] || {}) };
}
