/**
 * Icone per le scelte brevi dell'inspector (FieldSelect a pulsanti): al posto di «Sini… Cen… Des…», tagliate nella
 * colonna stretta, un'icona che si riconosce in ogni lingua; il nome compare nel popup al passaggio del mouse
 * (utente, 2 nov 2026). Un gruppo prende le icone solo se TUTTE le sue opzioni ne hanno una: metà icone e metà
 * scritte non si leggono. Un'opzione può portare la sua (`icona`: il contenuto di un <svg viewBox="0 0 24 24">,
 * o `sigla`: due o tre lettere); senza, si guarda il valore e il campo (chiave ed etichetta) per capire il senso
 * («start» in un allineamento verticale è «in alto»).
 */

const L = (d) => `<path d="${d}"/>`;
const PIENO = (d) => `<path d="${d}" fill="currentColor" stroke="none"/>`;

// Allineamento del testo / orizzontale
const RIGHE = {
  left: L('M4 6h16M4 10h10M4 14h16M4 18h10'),
  center: L('M4 6h16M7 10h10M4 14h16M7 18h10'),
  right: L('M4 6h16M10 10h10M4 14h16M10 18h10'),
  justify: L('M4 6h16M4 10h16M4 14h16M4 18h16'),
};
// Allineamento verticale: la linea è il bordo, il blocco l'oggetto
const VERTICALE = {
  top: L('M4 4h16') + L('M9 7h6v9H9z'),
  middle: L('M4 12h4M16 12h4') + L('M8 7h8v10H8z'),
  bottom: L('M4 20h16') + L('M9 8h6v9H9z'),
  stretch: L('M4 4h16M4 20h16') + L('M9 7h6v10H9z'),
};
// Allineamento orizzontale di oggetti (flex): la linea è il bordo
const ORIZZONTALE = {
  left: L('M4 4v16') + L('M7 9h9v6H7z'),
  center: L('M12 4v4M12 16v4') + L('M7 8h10v8H7z'),
  right: L('M20 4v16') + L('M8 9h9v6H8z'),
  stretch: L('M4 4v16M20 4v16') + L('M7 9h10v6H7z'),
};
// Posizione su un lato (icona sopra, legenda a destra…): il riquadro col lato pieno
const LATO = {
  top: L('M5 5h14v14H5z') + PIENO('M5 5h14v4H5z'),
  bottom: L('M5 5h14v14H5z') + PIENO('M5 15h14v4H5z'),
  left: L('M5 5h14v14H5z') + PIENO('M5 5h4v14H5z'),
  right: L('M5 5h14v14H5z') + PIENO('M15 5h4v14h-4z'),
};
const ANGOLO = {
  'top-left': L('M5 5h14v14H5z') + PIENO('M7 7h5v5H7z'),
  'top-right': L('M5 5h14v14H5z') + PIENO('M12 7h5v5h-5z'),
  'bottom-left': L('M5 5h14v14H5z') + PIENO('M7 12h5v5H7z'),
  'bottom-right': L('M5 5h14v14H5z') + PIENO('M12 12h5v5h-5z'),
};
const NESSUNO = '<circle cx="12" cy="12" r="8"/>' + L('M6.5 17.5l11-11');
const VARIE = {
  horizontal: L('M4 12h16M8 8l-4 4 4 4M16 8l4 4-4 4'),
  vertical: L('M12 4v16M8 8l4-4 4 4M8 16l4 4 4-4'),
  row: L('M4 12h16M8 8l-4 4 4 4M16 8l4 4-4 4'),
  column: L('M12 4v16M8 8l4-4 4 4M8 16l4 4 4-4'),
  _self: L('M3 6h18v13H3zM3 10h18'),
  _blank: L('M14 4h6v6M20 4l-9 9M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5'),
  asc: L('M7 19V5M3 9l4-4 4 4M14 7h3M14 12h5M14 17h7'),
  desc: L('M7 5v14M3 15l4 4 4-4M14 7h7M14 12h5M14 17h3'),
  circle: '<circle cx="12" cy="12" r="7.5"/>',
  square: L('M5 5h14v14H5z'),
  rounded: '<rect x="5" y="5" width="14" height="14" rx="4"/>',
  pill: '<rect x="3" y="8" width="18" height="8" rx="4"/>',
  list: L('M9 6h11M9 12h11M9 18h11') + PIENO('M4 5h2v2H4zM4 11h2v2H4zM4 17h2v2H4z'),
  grid: L('M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z'),
  light: '<circle cx="12" cy="12" r="4"/>' + L('M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4'),
  dark: L('M20 14.5A8 8 0 0 1 9.5 4 8 8 0 1 0 20 14.5z'),
  none: NESSUNO,
  low: PIENO('M4 16h4v4H4z') + L('M10 12h4v8h-4zM16 8h4v12h-4z'),
  medium: PIENO('M4 16h4v4H4zM10 12h4v8h-4z') + L('M16 8h4v12h-4z'),
  high: PIENO('M4 16h4v4H4zM10 12h4v8h-4zM16 8h4v12h-4z'),
};
// Misure: le sigle si leggono in ogni lingua
const SIGLE = { xs: 'XS', sm: 'S', small: 'S', s: 'S', md: 'M', medium: 'M', m: 'M', lg: 'L', large: 'L', l: 'L', xl: 'XL', xxl: 'XXL' };

const SINONIMI = {
  sinistra: 'left', destra: 'right', centro: 'center', centrato: 'center', giustificato: 'justify',
  'flex-start': 'start', 'flex-end': 'end', 'space-between': 'justify', alto: 'top', basso: 'bottom', sopra: 'top', sotto: 'bottom',
  orizzontale: 'horizontal', verticale: 'vertical', chiaro: 'light', scuro: 'dark', nessuno: 'none', nessuna: 'none',
  cerchio: 'circle', quadrato: 'square', arrotondato: 'rounded', elenco: 'list', griglia: 'grid', lista: 'list',
};

function norm(v) {
  const s = String(v == null ? '' : v).trim().toLowerCase();
  return SINONIMI[s] || s;
}

/**
 * @param {Array} opzioni  le opzioni piatte { value, label, icona?, sigla? }
 * @param {string} campo   chiave ed etichetta del campo, per capire se un allineamento è verticale
 * @returns {Array|null}   per ogni opzione { svg } o { sigla }, oppure null se il gruppo resta a scritte
 */
export function iconeOpzioni(opzioni, campo = '') {
  const opts = (opzioni || []).filter(Boolean);
  if (opts.length < 2 || opts.length > 5) return null;
  const ctx = String(campo || '').toLowerCase();
  const valori = opts.map(o => norm(o.value));
  const verticale = /vertical|valign|v_align|align_items|align-items|verticale|vert/.test(ctx)
    || valori.includes('top') || valori.includes('bottom') || valori.includes('middle');
  const allinea = /align|allinea|justify|giustific/.test(ctx);
  const soloLati = valori.every(v => ['top', 'bottom', 'left', 'right', 'none'].includes(v));
  const flex = /justify|align_items|align-items|content|distribu/.test(ctx);

  const una = (o, v) => {
    if (o.icona) return { svg: o.icona };
    if (o.sigla) return { sigla: o.sigla };
    if (SIGLE[v] && valori.some(x => SIGLE[x] && x !== v)) return { sigla: SIGLE[v] };
    if (ANGOLO[v]) return { svg: ANGOLO[v] };
    if (soloLati && !allinea && LATO[v]) return { svg: LATO[v] };
    if (verticale) {
      const k = v === 'start' ? 'top' : v === 'end' ? 'bottom' : v === 'center' ? 'middle' : v;
      if (VERTICALE[k]) return { svg: VERTICALE[k] };
    }
    const k = v === 'start' ? 'left' : v === 'end' ? 'right' : v;
    if (flex && ORIZZONTALE[k]) return { svg: ORIZZONTALE[k] };
    if (RIGHE[k]) return { svg: RIGHE[k] };
    if (k === 'stretch' && ORIZZONTALE.stretch) return { svg: ORIZZONTALE.stretch };
    if (VARIE[v]) return { svg: VARIE[v] };
    return null;
  };
  const risultato = opts.map(o => una(o, norm(o.value)));
  return risultato.every(Boolean) ? risultato : null;
}
