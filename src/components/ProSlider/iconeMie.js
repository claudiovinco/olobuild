/**
 * Le icone caricate dall'utente («Le mie icone», `custom:<nome>`) per la tela e il pannello del Pro Slider (1.4.1475).
 * Prima la tela conosceva solo le icone del set (Lucide e UIkit) e al posto di quelle dell'utente disegnava una stella.
 * Si leggono una volta da olobuild/v1/custom-icons (le stesse che il sito disegna con render_icon_html()).
 */
import { reactive } from 'vue';

export const iconeMie = reactive({});
let stato = 'da-caricare';

export function caricaIconeMie() {
  if (stato !== 'da-caricare' || typeof fetch === 'undefined') return;
  stato = 'in-corso';
  const base = (typeof window !== 'undefined' && window.oloData?.restUrl) || '/wp-json/olobuild/v1/';
  fetch(base + 'custom-icons', { headers: { 'X-WP-Nonce': window.oloData?.nonce || '' }, credentials: 'same-origin' })
    .then((r) => (r.ok ? r.json() : null))
    .then((o) => {
      if (o && typeof o === 'object' && !Array.isArray(o)) Object.assign(iconeMie, o);
      stato = 'fatto';
    })
    .catch(() => { stato = 'da-caricare'; });
}

// Aggiunge (o aggiorna) un'icona appena caricata dal selettore, senza rileggere tutto
export function aggiungiIconaMia(nome, svg) {
  if (nome && svg) iconeMie[nome] = svg;
}

/**
 * Il disegno SVG di un'icona: quelle dell'utente da «Le mie icone», le altre dal set (`set` = nome → svg).
 * '' se non c'è (ancora): chi chiama decide il ripiego.
 */
export function svgIcona(nome, set) {
  const n = String(nome || '');
  if (n.startsWith('custom:')) {
    caricaIconeMie();
    return iconeMie[n.slice(7)] || '';
  }
  return (set && set[n]) || '';
}
