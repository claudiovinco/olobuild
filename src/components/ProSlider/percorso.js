/**
 * Il percorso di movimento di un livello (1.4.1473): disegnarlo sulla tela, vederlo, provarlo.
 *
 * Il tracciato si salva come sempre in `motionPath.path` (un «d» SVG in px della tela, dal punto in cui il livello è
 * posato: conta solo lo spostamento dal primo punto). I punti disegnati restano in `motionPath.punti` ([[x, y], …], il
 * primo è [0, 0], il livello) per poterli trascinare dopo; `motionPath.dritte` = segmenti dritti invece della curva.
 * La grandezza: `motionPath.scala` (%, 10–400, di serie 100) e `motionPath.adatta` (il percorso si allarga e si stringe
 * con la larghezza dello slider, rispetto alla tela del Computer di 1440 px). Gemello del motore: avviaPercorso() in
 * assets/js/olo-proslider.js e render_layer() in class-proslider-tile.php (data-mp: k, a, w).
 */

export const TELA_COMPUTER = 1440;

// Quanto il tracciato salvato si ingrandisce sulla tela che si guarda (larghezza del formato: 1440, 1240, 1024, 640)
export function fattorePercorso(mp, larghezzaFormato) {
  const s = Number(mp?.scala);
  const k = Number.isFinite(s) && s > 0 ? Math.max(10, Math.min(400, s)) / 100 : 1;
  return k * (mp?.adatta ? (Number(larghezzaFormato) || TELA_COMPUTER) / TELA_COMPUTER : 1);
}

// Il primo punto del tracciato (il «M» iniziale): da lì parte lo spostamento
export function puntoIniziale(d) {
  const m = String(d || '').match(/^\s*[Mm]\s*(-?[\d.]+(?:e[-+]?\d+)?)[\s,]+(-?[\d.]+(?:e[-+]?\d+)?)/i);
  return m ? [Number(m[1]), Number(m[2])] : [0, 0];
}

const r1 = (v) => Math.round(v * 10) / 10;
const fmt = (p) => r1(p[0]) + ',' + r1(p[1]);

// Il «d» che passa per i punti: dritto (L) o curvo morbido, una curva di Catmull-Rom scritta con le Bézier (C) che
// passa proprio per ogni punto
export function dDaPunti(punti, dritte = false) {
  const p = (punti || []).filter((x) => Array.isArray(x) && x.length >= 2 && Number.isFinite(+x[0]) && Number.isFinite(+x[1])).map((x) => [+x[0], +x[1]]);
  if (p.length < 2) return '';
  if (dritte || p.length === 2) return 'M' + fmt(p[0]) + p.slice(1).map((x) => ' L' + fmt(x)).join('');
  let d = 'M' + fmt(p[0]);
  for (let i = 0; i < p.length - 1; i++) {
    const p0 = p[i - 1] || p[i], p1 = p[i], p2 = p[i + 1], p3 = p[i + 2] || p2;
    const c1 = [p1[0] + (p2[0] - p0[0]) / 6, p1[1] + (p2[1] - p0[1]) / 6];
    const c2 = [p2[0] - (p3[0] - p1[0]) / 6, p2[1] - (p3[1] - p1[1]) / 6];
    d += ' C' + fmt(c1) + ' ' + fmt(c2) + ' ' + fmt(p2);
  }
  return d;
}

// I fotogrammi del movimento come li fa il sito: 64 punti lungo il tracciato, spostamento dal primo (× fattore) e, se
// chiesta, la rotazione lungo il percorso
export function fotogrammiPercorso(d, fattore = 1, ruota = false) {
  if (typeof document === 'undefined' || !d) return [];
  const NS = 'http://www.w3.org/2000/svg';
  const svg = document.createElementNS(NS, 'svg');
  const path = document.createElementNS(NS, 'path');
  svg.setAttribute('width', '0');
  svg.setAttribute('height', '0');
  svg.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden';
  path.setAttribute('d', d);
  svg.appendChild(path);
  document.body.appendChild(svg);
  const frames = [];
  try {
    const len = path.getTotalLength();
    if (len > 0) {
      const p0 = path.getPointAtLength(0);
      const N = 64;
      for (let i = 0; i <= N; i++) {
        const l = (len * i) / N;
        const p = path.getPointAtLength(l);
        let tr = `translate(${((p.x - p0.x) * fattore).toFixed(2)}px,${((p.y - p0.y) * fattore).toFixed(2)}px)`;
        if (ruota) {
          const a = path.getPointAtLength(Math.max(0, l - 0.5));
          const b = path.getPointAtLength(Math.min(len, l + 0.5));
          tr += ` rotate(${((Math.atan2(b.y - a.y, b.x - a.x) * 180) / Math.PI).toFixed(1)}deg)`;
        }
        frames.push({ transform: tr, offset: i / N });
      }
    }
  } catch (_) { /* tracciato scritto male: niente movimento */ }
  svg.remove();
  return frames;
}
