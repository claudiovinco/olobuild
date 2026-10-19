// Testi di partenza delle tile appena inserite (createTileFromType in useDragDrop.js) nella lingua del builder.
import { tPartenza } from '@/i18n';

// Tipi di campo che portano testo da leggere: varianti, modi e fonti (select) hanno valori che il
// catalogo conosce come parole («voci», «schede», «chiari») e non vanno toccati.
const TESTO = new Set(['text', 'textarea', 'editor']);

// Campi di testo con valori tecnici: nomi dei campi delle integrazioni («email»), ancore, slug,
// codici, parametri dell'indirizzo, proporzioni e misure, identificativi che altri campi richiamano
// (scene del Tour 360, gruppi e indicatori del Configuratore), tipi di contenuto, tasti.
const CHIAVE_TECNICA = /(_field$|^anchor$|^slug$|code$|^codice|parametro|^param$|^lang|language|ratio|height|width|^id$|_id$|^gruppo$|^punti$|^scena|destinazione|campo_modulo|campi_modulo|post_type|taxonomy|chips|_keys$|mailto)/i;

// Valori che sembrano tecnici anche in un campo di testo: il catalogo li conosce come parole
// («auto» = Car, «A» = To). Una lettera sola, auto/none, colori, indirizzi, numeri e misure.
const VALORE_TECNICO = /^(auto|none|inherit|initial|unset|\S|#[\w-]*|https?:\/\/\S*|var\(.*|url\(.*|calc\(.*|[\d.,\s/:%+-]+(px|em|rem|vh|vw|fr|s|ms|deg)?)$/i;

function traduci(v, chiave) {
  if (typeof v !== 'string' || !v.trim() || CHIAVE_TECNICA.test(chiave) || VALORE_TECNICO.test(v.trim())) return v;
  return tPartenza(v);
}

/**
 * I testi con cui una tile nasce (i default della classe PHP via REST, le voci d'esempio e la
 * `partenza` del config) sono scritti in italiano: nel builder in un'altra lingua la tile nasceva
 * in italiano. I campi di testo, e quelli delle voci dei ripetitori, passano dal catalogo della
 * lingua del builder; senza traduzione resta il testo com'è (in italiano non cambia niente).
 */
export function traduciTestiDiPartenza(settings, def) {
  if (!settings || !def) return;
  for (const f of [...(def.fields || []), ...(def.styleFields || [])]) {
    // `partenzaFissa`: testi che mostrano più lingue apposta (le righe «it → en» di oloxlist, il
    // «Benvenuto / Welcome / Willkommen» di oloxhero): tradotti, la dimostrazione perde senso.
    if (!f || !f.key || f.partenzaFissa) continue;
    const v = settings[f.key];
    if (TESTO.has(f.type)) {
      settings[f.key] = traduci(v, f.key);
    } else if (f.type === 'content-items' && Array.isArray(v) && Array.isArray(f.itemFields)) {
      const campi = f.itemFields.filter((x) => x && x.key && TESTO.has(x.type) && !x.partenzaFissa);
      for (const voce of v) {
        if (!voce || typeof voce !== 'object') continue;
        for (const c of campi) voce[c.key] = traduci(voce[c.key], c.key);
      }
    }
  }
}
