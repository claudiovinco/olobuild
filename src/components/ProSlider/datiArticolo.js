// Slide dagli articoli (1.4.1457): la slide modello si ripete una volta per ogni contenuto scelto nell'inspector
// («Slide dagli articoli»), e i segnaposto dei suoi livelli prendono i valori di quel contenuto. Il renderer PHP li
// riempie (Olobuild_ProSlider_Tile::slide_dai_contenuti); qui ci sono i nomi proposti nell'editor e l'esempio che la
// tela mostra al posto delle foto.
import { inject, computed } from 'vue';

// Fornito da ProSliderEditor: { attivi, modello (indice), esempio ({ segnaposto: valore } del primo contenuto) }
export const CHIAVE_CONTENUTI = 'oloContenutiSlider';

// Le parole che un testo può prendere dal contenuto (le etichette passano da t() dove si mostrano)
export const DATI_TESTO = [
  { token: '{post_title}', label: 'Titolo' },
  { token: '{post_excerpt}', label: 'Riassunto' },
  { token: '{post_date}', label: 'Data' },
  { token: '{post_category}', label: 'Categoria' },
  { token: '{post_author}', label: 'Autore' },
];
export const FOTO_ARTICOLO = '{featured_image}';
export const LINK_ARTICOLO = '{post_url}';

const SEGNAPOSTO = /\{(post_title|post_excerpt|post_date|post_category|post_author|post_url|featured_image)\}/g;

/** Lo stato delle slide dagli articoli per un componente dell'editor (spento fuori dall'editor). */
export function useContenutiSlider() {
  const c = inject(CHIAVE_CONTENUTI, null);
  return {
    attivi: computed(() => !!c?.attivi?.value),
    modello: computed(() => (c ? c.modello.value : -1)),
    esempio: computed(() => c?.esempio?.value || null),
  };
}

/** Un indirizzo con segnaposto riempito col primo contenuto (per le foto della tela); senza esempio resta com'è. */
export function conEsempio(v, esempio) {
  if (typeof v !== 'string' || !v.includes('{') || !esempio) return v;
  return v.replace(SEGNAPOSTO, (m, k) => (esempio[k] != null && esempio[k] !== '' ? String(esempio[k]) : m));
}

/** Lo stesso per un testo che va in v-html: il valore del contenuto entra escapato. */
const esc = (x) => String(x).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
export function conEsempioTesto(html, esempio) {
  if (typeof html !== 'string' || !html.includes('{') || !esempio) return html;
  return html.replace(SEGNAPOSTO, (m, k) => (esempio[k] != null && esempio[k] !== '' ? esc(esempio[k]) : m));
}

/** Vero se il valore ha un segnaposto del contenuto. */
export const haSegnaposto = (v) => typeof v === 'string' && new RegExp(SEGNAPOSTO.source).test(v);
