// Interi dai campi numerici (FieldRange / NumberScrubber) per i valori che si sono
// sempre salvati come NUMERI: i cursori nativi di prima scrivevano parseInt(...).
// FieldRange emette invece STRINGHE e non limita il numero digitato.

// Intero entro [min, max], oppure null = «non scrivere». Vuoto, non numerico o sotto
// il minimo non si scrive: è una digitazione a metà («1» verso «120»), e scrivere lì
// il minimo farebbe saltare il numero sotto le dita. Sopra il massimo si ferma al massimo.
export function interoNelRange(raw, min, max) {
  const n = parseInt(raw, 10);
  if (Number.isNaN(n) || n < min) return null;
  return Math.min(max, n);
}

// Da agganciare a @focusout del campo: uscendo, il numero mostrato torna quello
// salvato, così una digitazione scartata (campo svuotato, sotto il minimo) non
// resta a schermo come se valesse.
export function risincronizzaNumero(e, valore) {
  const el = e && e.target;
  if (el && el.type === 'number' && el.value !== String(valore)) el.value = String(valore);
}
