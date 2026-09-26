/**
 * Incolla stile: che cosa passa da una tile all'altra.
 *
 * Passa SOLO lo stile. Fra tile dello stesso tipo sono le chiavi che i campi del tab
 * Stile (`styleFields`) scrivono in tile.settings, con le loro varianti: dispositivi
 * (`_tablet`, `_mobile`…), hover (`_hover`, `_hover_duration`), chiavi storiche del
 * ponte legacy, chiavi della tipografia e stile tipografico collegato. Una chiave che
 * anche un campo del Contenuto scrive resta com'è; delle voci dei ripetitori passa
 * solo ciò che lo specchio nello Stile mostra, voce per voce (per posizione).
 *
 * Prima passava tutto tranne un elenco fisso di chiavi di contenuto (text, title…):
 * l'occhiello e le righe del titolo di un Section Header, che si chiamano in un altro
 * modo, venivano sovrascritti (utente, 26 set 2026).
 *
 * Una chiave dello stile che la sorgente non ha viene tolta anche alla destinazione:
 * lì vale il predefinito, come nella sorgente.
 *
 * Un campo che sta nello Stile ma porta le parole della tile (i testi dell'avviso, le
 * frasi degli effetti testo…) si marca `incollaStile: false`.
 */
import { getElementDef } from '@/config/elementRegistry';

const DISPOSITIVI = ['widescreen', 'tablet_landscape', 'tablet', 'mobile_landscape', 'mobile'];

// Mai, qualunque campo le scriva: nomi che portano sempre contenuto, e il posto
// della colonna nella griglia di ORIGINE (incollato su un'altra colonna ne metteva
// due nella stessa cella).
const MAI = new Set([
  'images', 'items', 'slides', 'content', 'text', 'title', 'subtitle', 'message',
  'description', 'html', 'url', 'link', 'href', 'video_url', 'file_url',
  'embed', 'shortcode', 'icon', 'label', 'caption', 'alt',
  'post_type', 'posts_per_page', 'query', 'taxonomy', 'terms',
  'service_id', 'source_type', 'wp_menu_id',
  'grid_column', 'grid_row',
]);

// Chiavi scritte da un elenco di campi. `voci`: ripetitore → chiavi delle sue voci.
function chiaviDi(fields, perStile, acc = { chiavi: new Set(), voci: new Map() }) {
  for (const f of fields || []) {
    if (!f || typeof f !== 'object' || (perStile && f.incollaStile === false)) continue;
    if (f.key && Array.isArray(f.itemFields)) {
      acc.voci.set(f.key, chiaviDi(f.itemFields, perStile).chiavi);
      continue;
    }
    const base = [f.key, f.linkedPresetKey, ...Object.values(f.keys || {}), ...Object.values(f.legacyKeys || {})];
    for (const k of base) {
      if (typeof k !== 'string' || !k) continue;
      acc.chiavi.add(k);
      for (const d of DISPOSITIVI) acc.chiavi.add(k + '_' + d);
    }
    if (f.key) {
      acc.chiavi.add(f.hoverKey || f.key + '_hover');
      acc.chiavi.add(f.hoverDurationKey || f.key + '_hover_duration');
    }
    if (Array.isArray(f.fields)) chiaviDi(f.fields, perStile, acc);
  }
  return acc;
}

const cache = new Map();

/** { chiavi, voci } dello Stile di un tipo di tile, o null se il tipo non ha config. */
export function chiaviDelloStile(type) {
  if (cache.has(type)) return cache.get(type);
  const def = getElementDef(type);
  let esito = null;
  if (def) {
    const stile = chiaviDi(def.styleFields, true);
    const contenuto = chiaviDi(def.fields, false);
    const libera = (k) => !MAI.has(k) && !contenuto.chiavi.has(k) && !contenuto.voci.has(k);
    const voci = new Map();
    for (const [k, campi] of stile.voci) {
      const delContenuto = contenuto.voci.get(k) || new Set();
      const solo = [...campi].filter(c => !MAI.has(c) && !delContenuto.has(c));
      if (solo.length) voci.set(k, solo);
    }
    esito = { chiavi: [...stile.chiavi].filter(libera), voci };
  }
  cache.set(type, esito);
  return esito;
}

const oggetto = (v) => (v && typeof v === 'object' && !Array.isArray(v) ? v : {});
const ha = (o, k) => Object.prototype.hasOwnProperty.call(o, k);

// La chiave `k` di `a` diventa quella di `da`; se `da` non ce l'ha, la toglie.
function passa(da, a, k) {
  if (ha(da, k)) a[k] = JSON.parse(JSON.stringify(da[k]));
  else delete a[k];
}

/**
 * Impostazioni della destinazione con lo stile della sorgente (stesso tipo di tile).
 * `salta(k)`: chiavi dello stile che il chiamante gestisce da sé (le larghezze della
 * colonna). Restituisce un oggetto nuovo.
 */
export function incollaImpostazioni(destinazione, sorgente, type, salta = () => false) {
  const out = { ...oggetto(destinazione) };
  const regole = chiaviDelloStile(type);
  const src = oggetto(sorgente);
  if (!regole) return out;
  for (const k of regole.chiavi) if (!salta(k)) passa(src, out, k);
  for (const [k, campi] of regole.voci) {
    if (!Array.isArray(src[k]) || !Array.isArray(out[k])) continue;
    out[k] = out[k].map((voce, i) => {
      const da = src[k][i];
      if (!voce || typeof voce !== 'object' || !da || typeof da !== 'object') return voce;
      const nuova = { ...voce };
      for (const c of campi) passa(da, nuova, c);
      return nuova;
    });
  }
  return out;
}
