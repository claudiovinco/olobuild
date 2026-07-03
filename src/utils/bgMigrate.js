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
