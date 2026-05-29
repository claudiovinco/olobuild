/**
 * Wheel handler per input[type=number] focusati.
 *
 * Comportamento browser di default: quando l'input ha focus, la rotella
 * incrementa il valore MA scrolla anche la pagina sottostante. UX rotta:
 * basta provare a regolare un padding e la sidebar scrolla via.
 *
 * Questo handler:
 *  - se l'input ha focus → previene lo scroll della pagina (preventDefault) e
 *    incrementa/decrementa manualmente di `step` (rispettando min/max)
 *  - se l'input NON ha focus → lascia passare l'evento (rotella = scroll pagina)
 *
 * Uso: `<input type="number" @wheel="handleNumberWheel" ... />`
 *
 * Nota: il listener emette `input` + `change` sintetici così l'handler @input
 * del template Vue riceve il nuovo valore senza modifiche al codice del componente.
 */
export function handleNumberWheel(e) {
  const el = e.currentTarget;
  if (document.activeElement !== el) return;
  e.preventDefault();
  const step = parseFloat(el.step || '1') || 1;
  const min = el.min !== '' ? parseFloat(el.min) : -Infinity;
  const max = el.max !== '' ? parseFloat(el.max) : Infinity;
  const delta = e.deltaY < 0 ? step : -step;
  const current = parseFloat(el.value || '0') || 0;
  const newVal = Math.min(max, Math.max(min, current + delta));
  el.value = String(newVal);
  el.dispatchEvent(new Event('input', { bubbles: true }));
  el.dispatchEvent(new Event('change', { bubbles: true }));
}
