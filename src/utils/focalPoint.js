/**
 * focalPos — risolve il valore del punto focale (object-position / background-position)
 * per un campo immagine, con fallback a 'center center'.
 *
 * Gemello runtime JS dell'helper config `focalField` (_shared.js) e del PHP
 * `Olobuild_Tile_Utils::focal_pos`. Usare nei componenti Tile *.vue:
 *
 *   :style="{ backgroundPosition: focalPos(s, 'bg_image') }"
 *   :style="{ objectPosition: focalPos(s, 'cover_image') }"
 *
 * Chiave letta: `<imageKey>_object_position` (override con il 3° argomento).
 * Il valore è una stringa CSS valida sia per object-position sia per background-position.
 *
 * @param {object} settings  tile.settings
 * @param {string} imageKey  chiave del campo immagine (es. 'bg_image')
 * @param {string} [key]     override della chiave salvata
 * @returns {string} es. 'center center' | '34% 23%'
 */
export function focalPos(settings, imageKey, key) {
  const k = key || (imageKey + '_object_position');
  const v = settings && settings[k];
  return (v && String(v).trim()) ? v : 'center center';
}
