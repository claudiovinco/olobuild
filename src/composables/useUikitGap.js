/**
 * useUikitGap — quanto vale, in pixel, un gap espresso con le parole di UIkit.
 *
 * PERCHÉ ESISTE
 * -------------
 * Le tile a griglia salvano il gap come parola ('small', 'medium', …) e sul sito
 * la traducono in una classe UIkit (`uk-grid-small`, …). Il canvas del builder,
 * che non carica quel CSS, se la ricavava a mano — e OGNI tile aveva inventato
 * numeri diversi dai suoi:
 *
 *   griglia      small 8px  · default 16px · medium 24px · large 40px
 *   post         small 8px  · default 12px · medium 16px · large 24px
 *   overlay      small 8px  ·                medium 16px · large 24px
 *   UIkit (vero) small 15px · default  0px  · medium 30px · large 40px (70 ≥1200)
 *
 * Risultato: il blocco nel builder era distanziato in un modo e sulla pagina in
 * un altro, e la differenza cresceva con la larghezza. Qui i valori sono presi
 * da `assets/vendor/uikit/css/uikit.min.css`, cioè dalla stessa fonte che decide
 * la resa sul sito: se un giorno si aggiorna UIkit, si aggiorna questo file.
 *
 * ⚠️ `default` vale 0: è l'assenza di classe, non una via di mezzo. Sembra strano
 * ma è ciò che il sito fa davvero, e il canvas deve dire la verità, non essere
 * più carino.
 */

// padding-left applicato ai figli da ciascuna classe (= distanza fra le colonne).
export const UIKIT_GAP = {
  collapse: 0,
  small: 15,
  default: 0,
  medium: 30,
  large: 40,
};

// Da 1200px in su `uk-grid-large` passa a 70px.
export const UIKIT_GAP_XL = { ...UIKIT_GAP, large: 70 };

/**
 * @param {string} parola  valore salvato ('small' | 'medium' | …)
 * @param {string} [ripiego='default'] cosa usare se la parola non è fra quelle note
 * @returns {string} il gap in CSS, es. '30px'
 */
export function uikitGap(parola, ripiego = 'default') {
  const k = String(parola || '').trim() || ripiego;
  const v = UIKIT_GAP[k] !== undefined ? UIKIT_GAP[k] : UIKIT_GAP[ripiego];
  return (v === undefined ? 0 : v) + 'px';
}
