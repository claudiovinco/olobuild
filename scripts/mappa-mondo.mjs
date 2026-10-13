// Dati della tile «Mappa del mondo» (assets/data/mappa-mondo.json) da Natural Earth 1:110m, nel TopoJSON del
// pacchetto world-atlas 2.0.2 (countries-110m.json, https://cdn.jsdelivr.net/npm/world-atlas@2.0.2/countries-110m.json):
// un tracciato SVG per stato (proiezione Natural Earth I, larghezza 1000, Antartide esclusa), codice ISO a due lettere
// (dal numerico, con le regole di CLDR), nome in it/en/fr/de/es (CLDR via Intl.DisplayNames di Node); gli stati troppo
// piccoli per la scala 1:110m sono puntini sulla capitale. Semplificazione sugli archi (Douglas-Peucker sul proiettato):
// i confini in comune restano identici fra i due stati. Anelli che attraversano l'antimeridiano tagliati a ±180°.
// uso: node scripts/mappa-mondo.mjs <countries-110m.json> assets/data/mappa-mondo.json [tolleranza=0.5] [decimali=1]
import fs from 'node:fs';
const [IN, OUT, TOLL = '0.5', DEC = '1'] = process.argv.slice(2);
const tolleranza = Number(TOLL), decimali = Number(DEC);
const topo = JSON.parse(fs.readFileSync(IN, 'utf8'));
const { scale, translate } = topo.transform;
const LINGUE = ['it', 'en', 'fr', 'de', 'es'];
const nomi = Object.fromEntries(LINGUE.map((l) => [l, new Intl.DisplayNames([l], { type: 'region' })]));

// Natural Earth I (stesse costanti di d3-geo, naturalEarth1Raw)
const proietta = ([lon, lat]) => {
  const l = (lon * Math.PI) / 180, p = (lat * Math.PI) / 180, p2 = p * p, p4 = p2 * p2;
  return [
    l * (0.8707 - 0.131979 * p2 + p4 * (-0.013791 + p4 * (0.003971 * p2 - 0.001529 * p4))),
    p * (1.007226 + p2 * (0.015085 + p4 * (-0.044475 + 0.028874 * p2 - 0.005916 * p4))),
  ];
};
// archi in gradi
const archiGradi = topo.arcs.map((arco) => { let x = 0, y = 0; return arco.map(([dx, dy]) => { x += dx; y += dy; return [x * scale[0] + translate[0], y * scale[1] + translate[1]]; }); });

// codice a due lettere: dal numerico con CLDR; i tre senza codice ISO nei dati di Natural Earth per nome
const PER_NOME = { Kosovo: 'XK', 'N. Cyprus': 'CY', Somaliland: 'SO' };
const codice = (g) => {
  const nome = g.properties && g.properties.name;
  if (PER_NOME[nome]) return PER_NOME[nome];
  if (!g.id || !/^\d{3}$/.test(g.id)) return null;
  try { const r = Intl.getCanonicalLocales('und-' + g.id)[0].split('-')[1]; return /^[A-Z]{2}$/.test(r) ? r : null; } catch { return null; }
};
const geometrie = topo.objects.countries.geometries.filter((g) => g.id !== '010'); // senza Antartide
const senzaCodice = geometrie.filter((g) => !codice(g)).map((g) => `${g.id} ${g.properties && g.properties.name}`);
if (senzaCodice.length) { console.error('senza codice:', senzaCodice.join(' | ')); process.exit(1); }

// archi usati (fuori dall'Antartide) → proiezione, limiti, semplificazione
const usati = new Set();
const anelli = (g) => (g.type === 'Polygon' ? [g.arcs] : g.type === 'MultiPolygon' ? g.arcs : []);
for (const g of geometrie) for (const poli of anelli(g)) for (const anello of poli) for (const a of anello) usati.add(a < 0 ? ~a : a);
// Anelli che attraversano l'antimeridiano (Figi, Čukotka): longitudini rese continue, taglio a ±180° (Sutherland-Hodgman
// sulle due metà), la metà oltre il bordo torna dall'altra parte; poi proiezione e semplificazione dell'anello intero.
const salta = (pts) => pts.some((q, i) => i && Math.abs(q[0] - pts[i - 1][0]) > 180);
const taglia = (ring) => {
  const u = [[...ring[0]]];
  for (let i = 1; i < ring.length; i++) { let d = ring[i][0] - ring[i - 1][0]; if (d > 180) d -= 360; if (d < -180) d += 360; u.push([u[i - 1][0] + d, ring[i][1]]); }
  if (u.length > 1 && u[0][0] === u[u.length - 1][0] && u[0][1] === u[u.length - 1][1]) u.pop();
  const lons = u.map((q) => q[0]), c = Math.max(...lons) > 180 ? 180 : -180;
  const clip = (dentro) => { const out = []; for (let i = 0; i < u.length; i++) { const a = u[(i - 1 + u.length) % u.length], b = u[i];
    const x = () => { const t = (c - a[0]) / (b[0] - a[0]); return [c, a[1] + t * (b[1] - a[1])]; };
    if (dentro(b)) { if (!dentro(a)) out.push(x()); out.push(b); } else if (dentro(a)) out.push(x()); } return out; };
  const ovest = clip((q) => q[0] <= c), est = clip((q) => q[0] >= c);
  // la metà oltre il bordo si sposta TUTTA, anche i punti sul taglio (+180 → -180): spostando solo quelli oltre, il
  // punto sul taglio restava a destra e il tracciato attraversava la cartina
  const dentro = c > 0 ? ovest : est, oltre = c > 0 ? est : ovest, dlon = c > 0 ? -360 : 360;
  return [dentro, oltre.map(([lon, lat]) => [lon + dlon, lat])].filter((q) => q.length >= 3);
};
const archiP = new Map();
let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
for (const i of usati) { const p = archiGradi[i].map(proietta); archiP.set(i, p); for (const [x, y] of p) { minX = Math.min(minX, x); maxX = Math.max(maxX, x); minY = Math.min(minY, y); maxY = Math.max(maxY, y); } }
const L = 1000, k = L / (maxX - minX), H = Math.ceil((maxY - minY) * k);
const inScala = ([x, y]) => [(x - minX) * k, (maxY - y) * k];
const dp = (pts, eps) => { // Douglas-Peucker, estremi fissi
  if (pts.length < 3) return pts;
  const tieni = new Uint8Array(pts.length); tieni[0] = tieni[pts.length - 1] = 1;
  const pila = [[0, pts.length - 1]];
  while (pila.length) {
    const [a, b] = pila.pop(); const [ax, ay] = pts[a], [bx, by] = pts[b]; const dx = bx - ax, dy = by - ay, d2 = dx * dx + dy * dy;
    let max = -1, idx = -1;
    for (let i = a + 1; i < b; i++) { const [px, py] = pts[i]; let d; if (!d2) d = Math.hypot(px - ax, py - ay); else { const tt = Math.max(0, Math.min(1, ((px - ax) * dx + (py - ay) * dy) / d2)); d = Math.hypot(px - (ax + tt * dx), py - (ay + tt * dy)); } if (d > max) { max = d; idx = i; } }
    if (max > eps) { tieni[idx] = 1; pila.push([a, idx], [idx, b]); }
  }
  return pts.filter((_, i) => tieni[i]);
};
const archiS = new Map();
let puntiPrima = 0, puntiDopo = 0;
for (const [i, p] of archiP) { const s = dp(p.map(inScala), tolleranza); archiS.set(i, s); puntiPrima += p.length; puntiDopo += s.length; }

const f = 10 ** decimali;
const r = (v) => Math.round(v * f) / f;
const num = (v) => { const s = String(r(v)); return s.replace(/^(-?)0\./, '$1.'); };
const coppia = (dx, dy) => { const a = num(dx), b = num(dy); return a + (b.startsWith('-') ? '' : ',') + b; };
const tracciato = (g) => {
  let d = '';
  for (const poli of anelli(g)) for (const anello of poli) {
    const gradi = [];
    for (const a of anello) { const s = a < 0 ? [...archiGradi[~a]].reverse() : archiGradi[a]; gradi.push(...(gradi.length ? s.slice(1) : s)); }
    const pezzi = [];
    if (salta(gradi)) { for (const parte of taglia(gradi)) pezzi.push(dp(parte.map((q) => inScala(proietta(q))), tolleranza)); }
    else { const pts = []; for (const a of anello) { const s = a < 0 ? [...archiS.get(~a)].reverse() : archiS.get(a); pts.push(...(pts.length ? s.slice(1) : s)); } pezzi.push(pts); }
    for (const pts of pezzi) {
      // arrotondati, senza doppioni; un anello ridotto a meno di 3 punti si salta
      const q = []; for (const [x, y] of pts) { const p = [r(x), r(y)]; const u = q[q.length - 1]; if (!u || u[0] !== p[0] || u[1] !== p[1]) q.push(p); }
      if (q.length > 1 && q[0][0] === q[q.length - 1][0] && q[0][1] === q[q.length - 1][1]) q.pop();
      if (q.length < 3) continue;
      d += 'M' + num(q[0][0]) + ',' + num(q[0][1]);
      for (let i = 1; i < q.length; i++) d += 'l' + coppia(q[i][0] - q[i - 1][0], q[i][1] - q[i - 1][1]);
      d += 'z';
    }
  }
  return d;
};
const nomiDi = (cc) => Object.fromEntries(LINGUE.map((l) => [l, nomi[l].of(cc)]));
const perCodice = new Map();
for (const g of geometrie) { const cc = codice(g); perCodice.set(cc, (perCodice.get(cc) || '') + tracciato(g)); }
const stati = [...perCodice].map(([c, d]) => ({ c, d, n: nomiDi(c) })).sort((a, b) => a.c.localeCompare(b.c));

// Stati che a 1:110m non hanno contorno: un puntino sulla capitale (coordinate approssimate, ± qualche km)
const PICCOLI = { AD: [42.51, 1.52], AG: [17.12, -61.85], BH: [26.07, 50.56], BB: [13.19, -59.54], CV: [15.12, -23.61], KM: [-11.7, 43.26],
  DM: [15.41, -61.37], GD: [12.12, -61.68], KI: [1.45, 172.98], LI: [47.14, 9.52], MV: [3.2, 73.22], MT: [35.9, 14.45], MH: [7.09, 171.38],
  MU: [-20.2, 57.5], FM: [6.92, 158.16], MC: [43.73, 7.42], NR: [-0.53, 166.92], PW: [7.5, 134.62], KN: [17.3, -62.72], LC: [13.91, -60.98],
  VC: [13.25, -61.2], WS: [-13.76, -172.1], SM: [43.94, 12.46], ST: [0.34, 6.73], SC: [-4.68, 55.49], SG: [1.35, 103.82], TO: [-21.18, -175.2],
  TV: [-8.52, 179.2], VA: [41.9, 12.45] };
const puntini = Object.entries(PICCOLI).filter(([c]) => !perCodice.has(c)).map(([c, [lat, lon]]) => { const [x, y] = inScala(proietta([lon, lat])); return { c, x: r(x), y: r(y), n: nomiDi(c) }; }).sort((a, b) => a.c.localeCompare(b.c));

const dati = {
  viewBox: `0 0 ${L} ${H}`,
  fonte: 'Natural Earth 1:110m (pubblico dominio, naturalearthdata.com), via world-atlas 2.0.2; proiezione Natural Earth I; nomi CLDR (Unicode). Generato da scripts/mappa-mondo.mjs, tolleranza ' + tolleranza,
  licenza: 'world-atlas: Copyright 2013-2019 Michael Bostock. Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee is hereby granted, provided that the above copyright notice and this permission notice appear in all copies. THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.',
  stati, puntini,
};
fs.writeFileSync(OUT, JSON.stringify(dati));
const byte = fs.statSync(OUT).size;
const soloTracciati = stati.reduce((s, x) => s + x.d.length, 0);
console.log(`stati ${stati.length} · puntini ${puntini.length} (${puntini.map((p) => p.c).join(' ')}) · viewBox ${dati.viewBox}`);
console.log(`punti ${puntiPrima} → ${puntiDopo} · tracciati ${(soloTracciati / 1024).toFixed(1)} KB · file ${(byte / 1024).toFixed(1)} KB`);
const assenti = Object.keys(PICCOLI).filter((c) => perCodice.has(c));
if (assenti.length) console.log('già col contorno (niente puntino):', assenti.join(' '));
