/**
 * fieldLegacyBridge — ponte fra i controlli COMPOSITI dell'inspector
 * (spacing / border-radius / border) e le vecchie chiavi PIATTE che molte tile
 * hanno ancora nei loro settings (padding_y + padding_x, border_width +
 * border_style + border_color, ...).
 *
 * PERCHÉ ESISTE
 * -------------
 * L'obiettivo di uniformità è: «il padding si gestisce in UN SOLO modo in tutto
 * OloBuild» — cioè il controllo a 4 lati (FieldSpacing), ovunque. Ma le tile
 * storiche salvano due scalari (`tab_padding_y`, `tab_padding_x`) letti dai
 * renderer Vue/PHP. Migrare a mano dati + renderer di 250 tile in un colpo solo
 * sarebbe una rottura enorme.
 *
 * Il ponte risolve il problema in modo strutturale:
 *
 *   LETTURA  (seed)   il campo composito, se non ha ancora un valore proprio,
 *                     si INIZIALIZZA dalle chiavi legacy → l'utente apre
 *                     l'inspector e vede i suoi valori reali, non uno zero.
 *   SCRITTURA (mirror) ogni modifica scrive il valore composito sulla chiave
 *                     nuova E ricopia la sintesi sulle chiavi legacy → il
 *                     renderer NON ancora migrato continua a rendere corretto.
 *
 * Conseguenza: il config di una tile può passare al controllo standard SUBITO,
 * e il renderer può essere portato a 4 lati veri quando gli tocca, senza
 * finestre di rottura e senza migrazioni del DB.
 *
 * CONTRATTI DATI (invariati)
 *   spacing        { top, right, bottom, left, linked }  |  numero
 *   border-radius  { tl, tr, br, bl }                    |  numero
 *   border         { top, right, bottom, left, linked, style, color }
 *
 * USO NEL CONFIG
 *   { key: 'tab_padding', label: t('Padding (px)'), type: 'spacing',
 *     legacyKeys: { y: 'tab_padding_y', x: 'tab_padding_x' } }
 *
 *   { key: 'card_border', label: t('Bordo'), type: 'border',
 *     legacyKeys: { width: 'card_border_width', style: 'card_border_style',
 *                   color: 'card_border_color' } }
 *
 * Chiavi legacy riconosciute per tipo:
 *   spacing        all | y | x | top | right | bottom | left
 *   border-radius  all | tl | tr | br | bl
 *   border         width | style | color  (+ top|right|bottom|left per spessori per-lato)
 *   box-shadow     h | v | blur | spread | color | inset
 */

const int = (v, fb = 0) => {
  const n = parseInt(v, 10);
  return Number.isFinite(n) ? n : fb;
};

/** true se la chiave legacy esiste nei settings con un valore utilizzabile */
const filled = (settings, key) =>
  !!key && settings && settings[key] !== undefined && settings[key] !== null && settings[key] !== '';

/** true se ALMENO una delle chiavi legacy mappate è valorizzata */
export function hasLegacyValue(map, settings) {
  if (!map || !settings) return false;
  return Object.values(map).some((k) => filled(settings, k));
}

// ─── SPACING ────────────────────────────────────────────────────────────────

function spacingSeed(map, s) {
  const all = filled(s, map.all) ? int(s[map.all]) : null;
  const y = filled(s, map.y) ? int(s[map.y]) : all;
  const x = filled(s, map.x) ? int(s[map.x]) : all;
  const top = filled(s, map.top) ? int(s[map.top]) : (y ?? 0);
  const right = filled(s, map.right) ? int(s[map.right]) : (x ?? 0);
  const bottom = filled(s, map.bottom) ? int(s[map.bottom]) : (y ?? 0);
  const left = filled(s, map.left) ? int(s[map.left]) : (x ?? 0);
  return { top, right, bottom, left, linked: top === right && right === bottom && bottom === left };
}

function spacingMirror(map, v) {
  // Il composito può arrivare come numero (stato "collegato" di FieldBox) o oggetto.
  const uni = v && typeof v === 'object' ? null : int(v);
  const top = uni !== null ? uni : int(v?.top);
  const right = uni !== null ? uni : int(v?.right);
  const bottom = uni !== null ? uni : int(v?.bottom);
  const left = uni !== null ? uni : int(v?.left);
  const out = [];
  // Lo scalare legacy non sa esprimere l'asimmetria: prende il valore più grande,
  // così il renderer non migrato non "perde" il bordo/spazio impostato.
  if (map.all) out.push({ key: map.all, value: Math.max(top, right, bottom, left) });
  if (map.y) out.push({ key: map.y, value: top === bottom ? top : Math.max(top, bottom) });
  if (map.x) out.push({ key: map.x, value: left === right ? left : Math.max(left, right) });
  if (map.top) out.push({ key: map.top, value: top });
  if (map.right) out.push({ key: map.right, value: right });
  if (map.bottom) out.push({ key: map.bottom, value: bottom });
  if (map.left) out.push({ key: map.left, value: left });
  return out;
}

// ─── BORDER-RADIUS ──────────────────────────────────────────────────────────

function radiusSeed(map, s) {
  const all = filled(s, map.all) ? int(s[map.all]) : null;
  const tl = filled(s, map.tl) ? int(s[map.tl]) : (all ?? 0);
  const tr = filled(s, map.tr) ? int(s[map.tr]) : (all ?? 0);
  const br = filled(s, map.br) ? int(s[map.br]) : (all ?? 0);
  const bl = filled(s, map.bl) ? int(s[map.bl]) : (all ?? 0);
  // FieldBox usa il NUMERO quando i 4 angoli coincidono (stato collegato).
  if (tl === tr && tr === br && br === bl) return tl;
  return { tl, tr, br, bl };
}

function radiusMirror(map, v) {
  const uni = v && typeof v === 'object' ? null : int(v);
  const tl = uni !== null ? uni : int(v?.tl);
  const tr = uni !== null ? uni : int(v?.tr);
  const br = uni !== null ? uni : int(v?.br);
  const bl = uni !== null ? uni : int(v?.bl);
  const out = [];
  if (map.all) out.push({ key: map.all, value: Math.max(tl, tr, br, bl) });
  if (map.tl) out.push({ key: map.tl, value: tl });
  if (map.tr) out.push({ key: map.tr, value: tr });
  if (map.br) out.push({ key: map.br, value: br });
  if (map.bl) out.push({ key: map.bl, value: bl });
  return out;
}

// ─── BORDER ─────────────────────────────────────────────────────────────────

function borderSeed(map, s) {
  const w = filled(s, map.width) ? int(s[map.width]) : null;
  const top = filled(s, map.top) ? int(s[map.top]) : (w ?? 0);
  const right = filled(s, map.right) ? int(s[map.right]) : (w ?? 0);
  const bottom = filled(s, map.bottom) ? int(s[map.bottom]) : (w ?? 0);
  const left = filled(s, map.left) ? int(s[map.left]) : (w ?? 0);
  return {
    top, right, bottom, left,
    linked: top === right && right === bottom && bottom === left,
    style: filled(s, map.style) ? String(s[map.style]) : 'solid',
    color: filled(s, map.color) ? String(s[map.color]) : '',
  };
}

function borderMirror(map, v) {
  const top = int(v?.top);
  const right = int(v?.right);
  const bottom = int(v?.bottom);
  const left = int(v?.left);
  const out = [];
  if (map.width) out.push({ key: map.width, value: Math.max(top, right, bottom, left) });
  if (map.top) out.push({ key: map.top, value: top });
  if (map.right) out.push({ key: map.right, value: right });
  if (map.bottom) out.push({ key: map.bottom, value: bottom });
  if (map.left) out.push({ key: map.left, value: left });
  if (map.style) out.push({ key: map.style, value: v?.style || 'solid' });
  if (map.color) out.push({ key: map.color, value: v?.color || '' });
  return out;
}

// ─── BOX-SHADOW ─────────────────────────────────────────────────────────────
// Il gruppo `shadowField` di _shared.js (importato da 109 tile) salva l'ombra
// personalizzata su SEI chiavi piatte — shadow_h, shadow_v, shadow_blur,
// shadow_spread, shadow_color, shadow_inset — mentre FieldBoxShadow lavora su un
// oggetto con ESATTAMENTE gli stessi nomi: { h, v, blur, spread, color, inset }.
// Il ponte è quindi una corrispondenza uno a uno, e nessun renderer si accorge
// del cambio: PHP continua a leggere le sei chiavi con shadow_value().
const SH_NUM = ['h', 'v', 'blur', 'spread'];

function shadowSeed(map, s) {
  const out = {};
  for (const k of SH_NUM) out[k] = filled(s, map[k]) ? int(s[map[k]]) : undefined;
  out.color = filled(s, map.color) ? String(s[map.color]) : undefined;
  // L'interruttore può essere salvato come booleano o come '1'/'0'/'true'.
  out.inset = filled(s, map.inset) ? (s[map.inset] === true || s[map.inset] === 1 || s[map.inset] === '1' || s[map.inset] === 'true') : undefined;
  // I campi che il legacy non ha valorizzato restano fuori: ci pensa il default
  // di FieldBoxShadow, che è la stessa terna di shadowDefaults.
  for (const k of Object.keys(out)) if (out[k] === undefined) delete out[k];
  return out;
}

function shadowMirror(map, v) {
  const out = [];
  for (const k of SH_NUM) if (map[k]) out.push({ key: map[k], value: int(v?.[k]) });
  if (map.color) out.push({ key: map.color, value: v?.color || '' });
  if (map.inset) out.push({ key: map.inset, value: !!v?.inset });
  return out;
}

// ─── API ────────────────────────────────────────────────────────────────────

// `text-shadow` usa le stesse funzioni: FieldTextShadow emette { h, v, blur,
// color } — la stessa forma senza spread né inset, che in un'ombra del testo
// non esistono. Le funzioni saltano da sole le sotto-chiavi non mappate.
const SEED = { spacing: spacingSeed, 'border-radius': radiusSeed, border: borderSeed, 'box-shadow': shadowSeed, 'text-shadow': shadowSeed };
const MIRROR = { spacing: spacingMirror, 'border-radius': radiusMirror, border: borderMirror, 'box-shadow': shadowMirror, 'text-shadow': shadowMirror };

/**
 * Valore iniziale del controllo composito ricavato dalle chiavi legacy.
 * Restituisce `undefined` quando non c'è nulla da ereditare (→ il campo usa il
 * suo default normale).
 */
export function seedFromLegacy(field, settings) {
  const map = field?.legacyKeys;
  const fn = SEED[field?.type];
  if (!map || !fn || !settings) return undefined;
  if (!hasLegacyValue(map, settings)) return undefined;
  return fn(map, settings);
}

/**
 * Bordo salvato nel FORMATO STORICO: la chiave conteneva solo la stringa colore
 * (`card_border: '#e5e7eb'`) e lo spessore era cablato nel renderer (di norma 1px).
 * Il controllo completo si inizializza da lì, così l'utente ritrova il suo bordo
 * invece di un controllo azzerato.
 *
 * @param {*} value           valore corrente della chiave (stringa = formato storico)
 * @param {number} [width=1]  spessore che il renderer usava quando il colore c'era
 */
export function seedBorderFromColor(value, width = 1) {
  if (typeof value !== 'string' || value.trim() === '') return undefined;
  const w = Number.isFinite(parseInt(width, 10)) ? parseInt(width, 10) : 1;
  return { top: w, right: w, bottom: w, left: w, linked: true, style: 'solid', color: value };
}

/**
 * Elenco `{ key, value }` da riversare sulle chiavi legacy dopo una modifica del
 * controllo composito. Vuoto se la tile non dichiara `legacyKeys`.
 */
export function legacyMirror(field, value) {
  const map = field?.legacyKeys;
  const fn = MIRROR[field?.type];
  if (!map || !fn) return [];
  return fn(map, value);
}
