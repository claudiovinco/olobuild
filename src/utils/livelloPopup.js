/**
 * Il livello (z-index) di un popup portato nel <body> (menu, selettore dei caratteri, cursore dei numeri, spiegazioni
 * (i)): sopra la finestra che contiene il suo campo (1.4.1435).
 *
 * Coi livelli fissi del builder (100000–100200) i popup aperti dentro l'editor del Pro Slider (z-index 999999)
 * finivano DIETRO l'editor: nessun menu si poteva usare e le (i) non si vedevano. Qui si guarda il livello più alto fra
 * gli antenati del campo e, se supera quello di base, il popup va subito sopra.
 *
 * @param {Element} el   Il campo (trigger) da cui il popup si apre.
 * @param {number}  base Il livello di sempre del popup.
 * @returns {number}
 */
export function livelloPopup(el, base) {
  let zMax = 0;
  for (let n = el?.parentElement; n && n !== document.body; n = n.parentElement) {
    const z = parseInt(getComputedStyle(n).zIndex, 10);
    if (!Number.isNaN(z) && z > zMax) zMax = z;
  }
  return zMax >= base - 100 ? zMax + 20 : base;
}
