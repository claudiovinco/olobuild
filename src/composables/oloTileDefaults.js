/**
 * oloTileDefaults — default ESTETICI e centrati sul brand per le tile.
 *
 * Adattamento allo stack reale Olobuild del prototipo in
 * `regoletiles1/prototype/oloTileDefaults.js`. Vedi:
 *   - regoletiles1/DESIGN_LANGUAGE.md   (le 10 regole)
 *   - regoletiles1/TOKEN_MAPPING.md     (come i token si legano ai colori globali)
 *
 * DUE problemi che affronta
 * -------------------------
 * 1. Default off-brand: ButtonTile/IconTile nascevano #6366F1 (indaco), Alert con
 *    palette fisse, Divider grigi fissi. Il token --olo-color-primary esiste ma i
 *    fallback NON lo rispettavano → una tile appena inserita non sembrava "del brand".
 * 2. Default duplicati e divergenti tra `config/elements/<type>.js` e `<Type>Tile.vue`.
 *
 * STRATEGIA
 * - Tutti i colori partono dai token globali del sito (con fallback brand sensati).
 * - Spaziature/raggi da una scala coerente (SPACE 8pt / RADIUS).
 * - Un'unica fonte per i default delle tile (`buildDefaults`).
 *
 * ⚠️ NON inventare nomi di variabile che il GlobalColorsPanel non produce: legati
 *    SOLO ai 6 ruoli globali (GLOBAL) + ai 4 semantici globali + ai token di SYSTEM.
 *
 * Riuso: l'ombra è gestita da `useShadowMap` (SHADOW_MAP) e il radius da
 *        `useRadius`/`useBoxModel` — qui NON si ridefiniscono.
 */

/* ════════════════════════════════════════════════════════════════════
   TOKEN GLOBALI — legati alla palette del cliente (GlobalColorsPanel).
   Il pannello emette var(--olo-color-<id>) dai 6 ruoli seed: primary,
   secondary, accent, dark, light, text. I fallback qui = SEED del pannello
   (decisione cliente: primario = ROSSO BRAND #e1474f).
   ════════════════════════════════════════════════════════════════════ */
export const GLOBAL = {
  primary:   'var(--olo-color-primary, #e1474f)',    // rosso brand (decisione cliente)
  secondary: 'var(--olo-color-secondary, #16263d)',  // navy profondo — armonico col rosso
  accent:    'var(--olo-color-accent, #f4a23b)',     // ambra calda — armonica col rosso
  text:      'var(--olo-color-text, #1f2937)',
  dark:      'var(--olo-color-dark, #16263d)',
  light:     'var(--olo-color-light, #f8f9fa)',
};

/* ════════════════════════════════════════════════════════════════════
   TOKEN DI SISTEMA. Neutri fini = costanti (non personalizzabili).
   Semantici = ruoli globali personalizzabili (il cliente sceglie i 4 `fg`;
   la tinta `bg` soft è DERIVATA via color-mix). on-primary = calcolato dallo
   store per contrasto sul primario corrente (vedi contrastOn) con fallback bianco.
   ════════════════════════════════════════════════════════════════════ */
const soft = (v, hex) => `color-mix(in srgb, var(${v}, ${hex}) 12%, #fff)`;
export const SYSTEM = {
  onPrimary: 'var(--olo-color-on-primary, #ffffff)',
  textSoft:  'var(--olo-color-text-soft, #6b7280)',
  textFaint: 'var(--olo-color-text-faint, #94a3b8)',
  surface:   'var(--olo-color-surface, #ffffff)',
  surfaceAlt:'var(--olo-color-surface-alt, #f6f7f9)',
  border:    'var(--olo-color-border, #e5e7eb)',
  info:    { fg: 'var(--olo-color-info, #2563eb)',    bg: soft('--olo-color-info', '#2563eb') },
  success: { fg: 'var(--olo-color-success, #15803d)', bg: soft('--olo-color-success', '#15803d') },
  warning: { fg: 'var(--olo-color-warning, #b45309)', bg: soft('--olo-color-warning', '#b45309') },
  error:   { fg: 'var(--olo-color-error, #b42318)',   bg: soft('--olo-color-error', '#b42318') },
};

/**
 * contrastOn(hex) — testo leggibile su uno sfondo dato (luminanza relativa sRGB).
 * Lo STORE lo chiama quando il cliente cambia il primario e scrive il risultato
 * in `--olo-color-on-primary` (rosso→bianco, giallo chiaro→testo scuro).
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
  dark:      GLOBAL.dark,
  light:     GLOBAL.light,
};

/* Scala spaziature (8pt) e raggi — coerenti per tutte le tile. */
export const SPACE = { xs: 4, sm: 8, md: 12, lg: 16, xl: 24, '2xl': 32, '3xl': 48 };
export const RADIUS = { none: 0, sm: 6, md: 10, lg: 14, pill: 999 };

/* Lingua d'ombra unica "claude design" — gemella JS di Olo_Tile_Utils::SHADOW_MAP
   (PHP). Due strati navy-tinted (contact stretto + ambient morbido). I valori sm/md
   sono quelli esatti dei REFERENCE_*.html; lg/xl ne sono l'estensione coerente.
   Le tile Vue devono usare QUESTA scala (no box-shadow nero piatto inline) così la
   canvas combacia col render PHP. hover di una card = passare da `sm` a `md`. */
export const SHADOW = {
  none: 'none',
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};

/**
 * resolveColor — risolve un valore colore in modo TOKEN-FIRST:
 * se l'utente non ha scelto un colore, usa il token; mai un hex hardcoded.
 */
export function resolveColor(userValue, token) {
  return userValue && userValue !== '' ? userValue : token;
}

/* ─── Famiglia font — ruoli del tema + retrocompatibilità ───────────────────
   Gemello JS di Olo_Tile_Base::resolve_font_family (PHP): UNICA mappa dei
   valori-ruolo legacy salvati dalle vecchie select per-tile. Il formato NUOVO
   (FieldFontFamily / type 'font') salva CSS pronto: `var(--olo-font-family-…)`
   per i ruoli, `'Poppins', sans-serif` per font specifici, '' = eredita. */
export const FONT_ROLE_VARS = {
  body:         "var(--olo-font-family, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif)",
  sans:         "var(--olo-font-family, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif)",
  'sans-serif': "var(--olo-font-family, 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif)",
  heading:      "var(--olo-font-family-heading, Georgia, 'Times New Roman', serif)",
  serif:        "var(--olo-font-family-heading, 'Playfair Display', Georgia, serif)",
  mono:         "var(--olo-font-family-mono, ui-monospace, 'SF Mono', Menlo, Consolas, monospace)",
};

/**
 * resolveFontFamily — risolve il valore di un campo famiglia in CSS pronto.
 *
 * @param {string} value      Valore salvato: '' (eredita) | ruolo legacy
 *                            ('serif','sans','mono','heading','body') | CSS pronto.
 * @param {Object} legacyMap  Mappa per-tile opzionale che sovrascrive i ruoli
 *                            legacy con gli stack storici della tile (così le
 *                            tile convertite mantengono il loro fallback font).
 * @returns {string} font-family CSS, '' se da ereditare.
 */
export function resolveFontFamily(value, legacyMap = null) {
  const v = (value == null ? '' : String(value)).trim();
  if (v === '' || v === 'inherit') return v === 'inherit' ? 'inherit' : '';
  if (legacyMap && legacyMap[v]) return legacyMap[v];
  if (FONT_ROLE_VARS[v]) return FONT_ROLE_VARS[v];
  return v; // CSS pronto (var(...), stack, nome font)
}

/**
 * Default curati per tipo di tile. Pensati per essere "belli appena inseriti":
 * colore brand, testo leggibile, raggio/padding dalla scala condivisa, micro-ombra.
 *
 * ⚠️ Le CHIAVI qui sono identiche a quelle salvate dal config `elements/<type>.js`
 *    (guardrail: non cambiare le chiavi). Cambia solo il VALORE di default per le
 *    tile NUOVE; le tile esistenti mantengono i valori già salvati.
 *
 * I colori sono `''` ⇒ verranno risolti a runtime con resolveColor(userVal, TOKEN).
 */
export const TILE_DEFAULTS = {
  button: {
    text: 'Clicca qui',
    bg_color: '',                 // '' ⇒ TOKENS.primary
    text_color: '',               // '' ⇒ TOKENS.onPrimary
    border_radius: RADIUS.md,     // 10 (era 6)
    tile_padding: { top: SPACE.md, right: SPACE.xl, bottom: SPACE.md, left: SPACE.xl },
    font_size: 16,
    font_weight: 600,
    shadow: 'sm',                 // micro-ombra elegante invece di 'none' piatto
    hover_effect: 'lift',
  },
  divider: {
    style: 'solid',
    width: 100,
    thickness: 1,
    color: '',                    // '' ⇒ TOKENS.border
    alignment: 'center',
    spacing: SPACE.lg,            // 16
    text: '',
    text_color: '',               // '' ⇒ TOKENS.textSoft
    text_size: 14,
    icon_emoji: '',
  },
  alert: {
    alert_type: 'info',
    title: 'Attenzione!',
    message: 'Questo è un avviso informativo.',
    show_icon: true,
    custom_icon: '',
    dismissible: false,
    custom_bg_color: '',          // '' ⇒ tinta soft del semantico
    custom_text_color: '',        // '' ⇒ TOKENS.text
    text_align: 'left',
    shadow: 'none',
    // bg/border/icona risolti a runtime da TOKENS[alert_type]
  },
  headline: {
    heading: 'Nuovo Titolo',
    subtitle: '',
    tag: 'h2',
    alignment: 'center',
    heading_size: 'lg',
    heading_color: '',            // '' ⇒ ereditato (TOKENS.text via cascade)
    subtitle_color: '',           // '' ⇒ TOKENS.textSoft
    heading_italic: false,
    heading_uppercase: false,
    decoration: 'line',
    decoration_color: '',         // '' ⇒ TOKENS.primary
    decoration_count: 3,
    decoration_spacing: 6,
    text_stroke: 0,
    text_stroke_color: '',        // '' ⇒ TOKENS.text
    text_shadow: '',
    gradient_text: false,
    gradient_from: '',            // '' ⇒ TOKENS.primary
    gradient_to: '',              // '' ⇒ TOKENS.accent
    gradient_angle: 90,
    blend_mode: 'normal',
    shadow: 'none',
  },
  icon: {
    icon: 'star',
    size: 40,
    color: '',                    // '' ⇒ TOKENS.primary (o onPrimary su sfondo pieno)
    view: 'default',
    bg_color: '',                 // '' ⇒ TOKENS.primary
    bg_shape: 'circle',
    tile_padding: { top: SPACE.lg, right: SPACE.lg, bottom: SPACE.lg, left: SPACE.lg },
    hover_animation: 'none',
    rotation: 0,
  },
  hero: {
    title: 'Benvenuto nel nostro sito',
    subtitle: 'Scopri qualcosa di straordinario',
    text_color: '',               // '' ⇒ TOKENS.onPrimary (sovrapposto a bg)
    min_height: '500px',
    tile_padding: { top: SPACE['3xl'], right: SPACE.xl, bottom: SPACE['3xl'], left: SPACE.xl },
  },
};

/**
 * buildDefaults(type) — unica fonte dei default curati. Il Tile (render) e, dove
 * applicabile, il config registry (editor) leggono da qui, senza ridichiararli.
 */
export function buildDefaults(type) {
  return { ...(TILE_DEFAULTS[type] || {}) };
}
