/**
 * bgMigrate — migrazione NON distruttiva dei campi sfondo "flat" legacy verso
 * l'oggetto unico `media_bg` (type:'background', il pannello universale che
 * inserisce colore/gradiente/immagine/video/galleria + focal).
 *
 * Filosofia (sicurezza prima di tutto):
 *  - Gira al CARICAMENTO del builder, popolando `media_bg` SOLO se non è già
 *    impostato → il pannello unico mostra i dati vecchi e l'utente li edita da lì.
 *  - NON cancella le chiavi legacy: i renderer (Vue+PHP) le tengono come FALLBACK,
 *    quindi i template non ancora ri-salvati continuano a rendere identici.
 *  - Al ri-salvataggio `media_bg` ha la precedenza (i renderer preferiscono media_bg).
 *
 * Ogni tile dichiara nel suo config una `bgMigrate` (o array di spec per tile
 * multi-zona), letta qui via elementRegistry:
 *   bgMigrate: {
 *     target: 'media_bg',        // chiave oggetto destinazione (default 'media_bg')
 *     typeKey: 'bg_type',        // opz. select legacy color|image|video
 *     colorKey: 'bg_color',
 *     imageKey: 'bg_image',
 *     imageSizeKey: 'bg_image_size',
 *     imagePosKey: 'bg_image_object_position',
 *     videoKey: 'bg_video',
 *     posterKey: 'video_poster',
 *   }
 */
import { getElementDef } from '@/config/elementRegistry';

/**
 * Costruisce l'oggetto media_bg da una singola spec, senza sovrascrivere se già presente.
 * @returns {boolean} true se ha scritto qualcosa.
 */
export function applyBgMigration(settings, spec) {
  if (!settings || !spec) return false;
  const target = spec.target || 'media_bg';
  const cur = settings[target];
  // Già un background reale → non toccare (l'utente/una migrazione precedente l'ha impostato).
  if (cur && typeof cur === 'object' && cur.type && cur.type !== 'none') return false;

  const t   = spec.typeKey   ? settings[spec.typeKey]   : null;
  const img = spec.imageKey  ? settings[spec.imageKey]  : '';
  const vid = spec.videoKey  ? settings[spec.videoKey]  : '';
  const col = spec.colorKey  ? settings[spec.colorKey]  : '';

  let bg = null;
  if (((t === 'video') || (!t && vid)) && vid) {
    bg = { type: 'video', video_url: vid };
    if (spec.posterKey && settings[spec.posterKey])   bg.video_poster  = settings[spec.posterKey];
    if (spec.imagePosKey && settings[spec.imagePosKey]) bg.image_position = settings[spec.imagePosKey];
  } else if (((t === 'image') || (!t && img)) && img) {
    bg = { type: 'image', image_url: img };
    if (spec.imageSizeKey && settings[spec.imageSizeKey]) bg.image_size    = settings[spec.imageSizeKey];
    if (spec.imagePosKey && settings[spec.imagePosKey])  bg.image_position = settings[spec.imagePosKey];
  } else if (((t === 'color') || (!t && col)) && col) {
    bg = { type: 'solid', color: col };
  }

  if (!bg) return false;
  settings[target] = bg;
  return true;
}

/**
 * Migra il background di un singolo nodo leggendo la/le spec dal suo config.
 */
export function migrateNodeBg(node) {
  if (!node || !node.type || !node.settings) return;
  const def = getElementDef(node.type);
  const spec = def && def.bgMigrate;
  if (!spec) return;
  if (Array.isArray(spec)) {
    for (const s of spec) applyBgMigration(node.settings, s);
  } else {
    applyBgMigration(node.settings, spec);
  }
}

/**
 * Walk ricorsivo dell'albero: migra ogni nodo. Chiamato dai set*Tiles dello store.
 */
export function migrateTreeBackgrounds(nodes, _depth = 0) {
  if (!Array.isArray(nodes) || _depth > 20) return;
  for (const node of nodes) {
    migrateNodeBg(node);
    if (Array.isArray(node.children)) migrateTreeBackgrounds(node.children, _depth + 1);
  }
}

/** Tutte le chiavi che un campo dell'inspector della tile mostra (anche dentro gruppi e varianti a chiavi). */
function chiaviDeiCampi(def) {
  const set = new Set();
  const add = (f) => {
    if (!f || typeof f !== 'object') return;
    if (typeof f.key === 'string' && f.key) set.add(f.key);
    if (f.keys && typeof f.keys === 'object') Object.values(f.keys).forEach((k) => typeof k === 'string' && set.add(k));
    for (const sub of ['fields', 'children', 'controls']) if (Array.isArray(f[sub])) f[sub].forEach(add);
  };
  [...(def.fields || []), ...(def.styleFields || [])].forEach(add);
  return set;
}

const DA_SVUOTARE = ['imageKey', 'imagePosKey', 'imageSizeKey', 'videoKey', 'posterKey'];

/**
 * Le chiavi vecchie da svuotare quando l'utente cambia il pannello che le ha prese al caricamento.
 * Il renderer le usa come ripiego quando il pannello è vuoto: senza svuotarle, un'immagine tolta
 * dal pannello ricompariva da sola sulla pagina (e tornava nel pannello al caricamento seguente).
 * Si svuotano solo media e posizione, e solo quelle che nessun campo della tile mostra.
 * @param {string} type  tipo della tile
 * @param {string[]} cambiate  chiavi che l'utente sta scrivendo
 * @returns {string[]}
 */
export function chiaviVecchieDaSvuotare(type, cambiate) {
  const def = getElementDef(type);
  const spec = def && def.bgMigrate;
  if (!spec || !Array.isArray(cambiate) || !cambiate.length) return [];
  const specs = Array.isArray(spec) ? spec : [spec];
  const mostrate = chiaviDeiCampi(def);
  const out = [];
  for (const s of specs) {
    if (!cambiate.includes(s.target || 'media_bg')) continue;
    for (const prop of DA_SVUOTARE) {
      const k = s[prop];
      if (typeof k === 'string' && k && !mostrate.has(k) && !cambiate.includes(k)) out.push(k);
    }
  }
  return out;
}
