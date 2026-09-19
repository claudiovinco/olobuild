/**
 * useImageFrame — gemello JS di `Olobuild_Tile_Utils::image_frame()`.
 *
 * La «cornice» di un'immagine sono tre cose che vanno sempre insieme:
 *   la PROPORZIONE (la maschera di ritaglio: 1:1, 16:9, …) sul contenitore,
 *   l'ADATTAMENTO (come la foto riempie quella maschera) sull'immagine,
 *   il PUNTO FOCALE (quale parte resta inquadrata quando si ritaglia).
 * Separarle è ciò che produceva tile in cui puoi scegliere il ritaglio ma non
 * dove cade, o viceversa.
 *
 * Il canvas del builder e il sito devono disegnare la stessa cornice: questo file
 * e l'helper PHP leggono le STESSE chiavi e applicano le STESSE regole, compresa
 * la whitelist sul valore della proporzione.
 *
 *   const { contenitore, immagine } = imageFrame(s.value, 'cover_image', { fit: 'cover' });
 *   <div :style="contenitore"><img :style="immagine" …></div>
 */

const ADATTAMENTI = ['cover', 'contain', 'fill', 'none', 'scale-down'];

// "W/H" oppure un numero: niente altro finisce nel CSS.
const PROPORZIONE = /^\d+(?:\.\d+)?(?:\s*\/\s*\d+(?:\.\d+)?)?$/;

/**
 * @param {object} settings  settings della tile
 * @param {string} key       chiave del campo immagine (es. 'cover_image')
 * @param {object} [def]     default che riproducono la resa ATTUALE della tile:
 *                           { ratio: 'auto', fit: '', pos: 'center center' }
 * @returns {{contenitore: object, immagine: object}} oggetti di stile Vue (vuoti = non emettere nulla)
 */
export function imageFrame(settings, key, def = {}) {
  const s = settings || {};
  const contenitore = {};
  const immagine = {};

  let ratio = String(s[`${key}_ratio`] ?? def.ratio ?? 'auto').trim();
  if (ratio === 'custom') ratio = String(s[`${key}_ratio_custom`] ?? '').trim();
  // Cinque tile storiche salvano '16:9' invece di '16/9', che in CSS non vale:
  // si normalizza qui, come fa il gemello PHP.
  ratio = ratio.replace(/:/g, '/');
  if (ratio && ratio !== 'auto' && PROPORZIONE.test(ratio)) {
    contenitore.aspectRatio = ratio.replace(/\s+/g, '');
  }

  const fit = String(s[`${key}_fit`] ?? def.fit ?? '');
  if (ADATTAMENTI.includes(fit)) {
    immagine.objectFit = fit;
    // Con `fill` l'immagine viene deformata per riempire: il punto focale non
    // sposta niente, e scriverlo darebbe l'idea che serva a qualcosa.
    if (fit !== 'fill') {
      const pos = String(s[`${key}_object_position`] ?? def.pos ?? 'center center').trim();
      // Difesa breakout, come nel gemello PHP: niente parentesi o punti e virgola.
      if (pos && !/[;{}()]/.test(pos)) immagine.objectPosition = pos;
    }
  }

  return { contenitore, immagine };
}
