// Categorie della palette degli elementi: UNA fonte per il rail della sidebar, il
// pannello Inserisci (il «+» fra le sezioni) e il Finder (Ctrl+K). Prima erano quattro
// elenchi con voci ed etichette diverse: 11 tile con una categoria fuori elenco
// (content, header, general, creative) non stavano in nessuna voce del rail e si
// trovavano solo cercandole per nome.
//
// La categoria la dichiara la tile (`$category` nel PHP, `category` nel config JS) e
// deve essere una di queste chiavi: lo controlla `node scripts/check-tile-registries.cjs`
// (categoria-fuori-elenco = 0). Le chiavi storiche passano dagli alias; ogni altra
// (per esempio di un plugin esterno) finisce in «Altro», che è sempre in elenco:
// nessuna tile resta fuori dall'inseritore.
//
// «Atmosfera» resta col suo nome: «Effetti» è già un gruppo del tab Stile.

const svg = (d) => `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">${d}</svg>`;

export const PALETTE_CATEGORIES = [
  { key: 'essential', label: 'Essenziale', color: 'var(--olo-ui-accent, #e8622a)',
    icon: svg('<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>') },
  { key: 'layout', label: 'Layout', color: '#3B82F6',
    icon: svg('<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>') },
  { key: 'text', label: 'Testo', color: '#22C55E',
    icon: svg('<path d="M4 7V4h16v3M9 20h6M12 4v16"/>') },
  { key: 'media', label: 'Media', color: '#A855F7',
    icon: svg('<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>') },
  { key: 'marketing', label: 'Marketing', color: '#F59E0B',
    icon: svg('<path d="M3 11l18-8v18L3 13z"/><path d="M11.6 16.8a3 3 0 11-5.8-1.6"/>') },
  { key: 'interactive', label: 'Interattivo', color: '#06B6D4',
    icon: svg('<path d="M12 3l1.9 5.8H20l-4.9 3.6 1.9 5.8L12 14.6 7 18.2l1.9-5.8L4 8.8h6.1z"/>') },
  { key: 'atmosphere', label: 'Atmosfera', color: '#38BDF8',
    icon: svg('<path d="M12 3l1.4 4.1L17.5 8.5 13.4 9.9 12 14l-1.4-4.1L6.5 8.5l4.1-1.4z"/><path d="M5 14l.8 2.2L8 17l-2.2.8L5 20l-.8-2.2L2 17l2.2-.8z"/><path d="M18.5 13l.6 1.6 1.6.6-1.6.6-.6 1.6-.6-1.6-1.6-.6 1.6-.6z"/>') },
  { key: 'navigation', label: 'Navigazione', color: '#F43F5E',
    icon: svg('<path d="M3 12h18M3 6h18M3 18h18"/>') },
  { key: 'dynamic', label: 'Dinamico', color: '#F97316',
    icon: svg('<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>') },
  { key: 'woocommerce', label: 'WooCommerce', color: '#7F54B3',
    icon: svg('<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>') },
  { key: 'booking', label: 'Olo Booking', color: '#EAB308',
    icon: svg('<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18M8 14l3 3 5-5"/>') },
  { key: 'olo-space', label: 'Olo Space', color: '#14B8A6',
    icon: svg('<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path d="M9 22V12h6v10"/>') },
  { key: 'olo-tutor', label: 'Olo Tutor', color: '#84CC16',
    icon: svg('<path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 2 9 2 12 0v-5"/>') },
  { key: 'olotour', label: 'Olo Tour', color: '#0EA5E9',
    icon: svg('<circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="9" ry="3.5"/><path d="M12 3v18"/>') },
  { key: 'other', label: 'Altro', color: '#6B7280',
    icon: svg('<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="16" cy="12" r="1"/>') },
];

// Categorie dichiarate altrove → voce della palette: le storiche (una tile di un plugin
// esterno può ancora usarle) e i moduli di OLObooking, che dichiarano ognuno la propria
// (le «Ac —» di accommodation, gli immobili…) e nella palette stanno sotto il prodotto.
// Prima le 41 tile di OLObooking/OLOtutor/OLOtour su mosaic non stavano nel rail.
export const CATEGORY_ALIASES = {
  header: 'navigation',
  general: 'interactive',
  creative: 'atmosphere',
  accommodation: 'booking',
  'real-estate': 'booking',
  restaurants: 'booking',
  rentals: 'booking',
  events: 'booking',
  appointments: 'booking',
};

// Gli elementi strutturali (sezione, riga, colonna) non stanno nella palette.
export const STRUCTURE_CATEGORY = 'structure';

const PER_CHIAVE = Object.fromEntries(PALETTE_CATEGORIES.map((c) => [c.key, c]));

/** Chiave della palette per la categoria dichiarata da una tile (mai fuori elenco). */
export function paletteCategoryOf(category) {
  const c = CATEGORY_ALIASES[category] || category;
  return PER_CHIAVE[c] ? c : 'other';
}

/** Voce della palette ({ key, label, color, icon }) per la categoria di una tile. */
export function paletteCategory(category) {
  return PER_CHIAVE[paletteCategoryOf(category)];
}
