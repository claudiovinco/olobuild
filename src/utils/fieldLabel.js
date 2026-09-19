/**
 * fieldLabel — l'unità di misura sta NEL controllo, non nel nome del campo.
 *
 * PERCHÉ
 * ------
 * Censendo i 5.922 campi dell'inspector sono saltate fuori 880 etichette che si
 * portano dietro l'unità scritta a mano: «Padding (px)», «Durata (ms)»,
 * «Larghezza (%)». Scritta a mano vuol dire scritta in modi diversi — con la
 * parentesi o senza, attaccata o staccata, a volte tradotta e a volte no — e
 * soprattutto vuol dire che la stessa informazione sta in due posti quando il
 * controllo sa già mostrarla accanto al numero (NumberScrubber ha `unit`).
 *
 * COSA FA
 * -------
 * Stacca l'unità dalla coda dell'etichetta GIÀ TRADOTTA e la restituisce a
 * parte, così chi rende il campo può metterla dove va: dentro la valbox.
 *
 * REGOLA DI PRUDENZA
 * ------------------
 * Si stacca SOLO per i controlli che l'unità la sanno mostrare. Per gli altri
 * l'etichetta resta l'unico posto in cui l'utente legge «px», e toglierla sarebbe
 * una perdita, non una pulizia: prima di aggiungere un tipo a questo elenco,
 * verificare che il suo controllo l'unità la scriva davvero.
 */

// Le unità che riconosciamo. Chiuso di proposito: una coda come «(opzionale)»
// o «(secondi)» non è un'unità e non va toccata.
const UNITA = /\s*\((px|%|ms|s|vh|vw|vmin|vmax|em|rem|ch|deg|fr|pt)\)\s*$/i;

// I controlli che l'unità la sanno mostrare da sé.
// `spacing` e `border-radius` sono entrati in questo elenco quando sono passati a
// FieldBox (lo stesso controllo del pannello «Spazi & Bordi»), che ha il selettore
// d'unità accanto ai quattro riquadri: prima avevano solo caselle nude e l'unità
// poteva stare unicamente nell'etichetta.
const SANNO_MOSTRARLA = new Set(['range', 'number', 'unit', 'spacing', 'border-radius']);

/**
 * @param {string} etichetta  il testo GIÀ passato da t()
 * @param {string} tipo       field.type
 * @returns {{ testo: string, unita: string }}
 */
export function staccaUnita(etichetta, tipo) {
  const testo = String(etichetta == null ? '' : etichetta);
  if (!SANNO_MOSTRARLA.has(tipo)) return { testo, unita: '' };
  const m = testo.match(UNITA);
  if (!m) return { testo, unita: '' };
  return { testo: testo.slice(0, testo.length - m[0].length).trim(), unita: m[1] };
}

/** Solo il testo, senza unità — per i title/aria dove l'unità non serve. */
export function testoEtichetta(etichetta, tipo) {
  return staccaUnita(etichetta, tipo).testo;
}
