# -*- coding: utf-8 -*-
"""Tagli non voluti (regola dell'utente, 8 nov 2026: «porre sempre attenzione ai tagli non voluti»).

Percorre ogni pagina dall'alto in basso (passi di mezza finestra, così si vedono anche gli stati delle aperture che
scorrono) e a ogni passo guarda il testo VISIBILE nella finestra:
  - testo tagliato: l'inchiostro delle lettere (misurato con measureText: ascendenti, discendenti, sporgenze del
    corsivo) esce da un antenato che ritaglia (overflow ≠ visible, clip-path inset) e il riquadro lo interseca
    in parte: è un taglio, non un elemento nascosto apposta (quelli stanno tutti fuori);
  - schermate ritagliate: <img> in object-fit cover il cui src corrisponde a --schermate e che perde più del 4%
    per lato.
uso: python tagli.py <base> <percorsi,separati> [--lingue it,en,de,fr] [--larghezze 390,1440] [--schermate regex]
"""
import argparse
import json
import sys
from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')
ap = argparse.ArgumentParser()
ap.add_argument('base')
ap.add_argument('percorsi')
ap.add_argument('--lingue', default='it,en,de,fr')
ap.add_argument('--larghezze', default='390,1440')
ap.add_argument('--schermate', default=r'otr-(studente|regia|docente|scuola|portale|gioco)-')
ap.add_argument('--param', default='lang')
ap.add_argument('--ignora', default=r'ticker|olo-mq|olo-pg-cap')
a = ap.parse_args()

JS = r"""(rx) => {
  const out = [], vh = innerHeight, vw = innerWidth;
  const cv = document.createElement('canvas').getContext('2d');
  const visibile = el => { for (let e = el; e && e !== document.documentElement; e = e.parentElement) {
      const s = getComputedStyle(e); if (s.visibility === 'hidden' || s.display === 'none' || parseFloat(s.opacity) < 0.08) return false; }
    return true; };
  const ritaglio = (a) => {   // riquadro che ritaglia, o null
    const s = getComputedStyle(a), b = a.getBoundingClientRect();
    const ox = s.overflowX !== 'visible', oy = s.overflowY !== 'visible';
    let box = null;
    if (ox || oy) {
      const bl = parseFloat(s.borderLeftWidth) || 0, br = parseFloat(s.borderRightWidth) || 0,
            bt = parseFloat(s.borderTopWidth) || 0, bb = parseFloat(s.borderBottomWidth) || 0;
      box = {l: ox ? b.left + bl : -1e9, r: ox ? b.right - br : 1e9, t: oy ? b.top + bt : -1e9, b: oy ? b.bottom - bb : 1e9, perche: 'overflow'};
    }
    const cp = s.clipPath;
    if (cp && cp !== 'none') {
      const m = cp.match(/^inset\(([^)]*)\)/);
      if (m) {
        const v = m[1].split(/\s+round\s+/)[0].trim().split(/\s+/);
        const q = [v[0], v[1] ?? v[0], v[2] ?? v[0], v[3] ?? v[1] ?? v[0]];
        const val = (x, base) => x.endsWith('%') ? parseFloat(x) / 100 * base : parseFloat(x) || 0;
        const c = {t: b.top + val(q[0], b.height), r: b.right - val(q[1], b.width), b: b.bottom - val(q[2], b.height), l: b.left + val(q[3], b.width), perche: 'clip-path ' + cp};
        box = box ? {l: Math.max(box.l, c.l), r: Math.min(box.r, c.r), t: Math.max(box.t, c.t), b: Math.min(box.b, c.b), perche: box.perche + '+' + c.perche} : c;
      }
    }
    return box;
  };
  const nome = e => (e.className && typeof e.className === 'string' ? '.' + e.className.trim().split(/\s+/).slice(0, 2).join('.') : e.tagName.toLowerCase());
  const w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {acceptNode: n => n.nodeValue.trim().length > 1 ? 1 : 2});
  let n;
  while ((n = w.nextNode())) {
    const el = n.parentElement;
    if (!el || el.closest('script,style,noscript,[aria-hidden="true"],.screen-reader-text,.sr-only')) continue;
    const r = document.createRange(); r.selectNodeContents(n);
    const righe = [...r.getClientRects()].filter(q => q.width > 1 && q.height > 1 && q.bottom > 0 && q.top < vh && q.right > 0 && q.left < vw);
    if (!righe.length || !visibile(el)) continue;
    const s = getComputedStyle(el);
    cv.font = `${s.fontStyle} ${s.fontWeight} ${s.fontSize} ${s.fontFamily}`;
    const testo = n.nodeValue.trim(), mt = cv.measureText(s.textTransform === 'uppercase' ? testo.toUpperCase() : testo);
    const asc = mt.fontBoundingBoxAscent, inkA = mt.actualBoundingBoxAscent, inkD = mt.actualBoundingBoxDescent;
    const sporge = Math.max(0, mt.actualBoundingBoxRight - mt.width);
    for (let a = el; a && a !== document.body; a = a.parentElement) {
      const box = ritaglio(a);
      if (!box) continue;
      for (const q of righe) {
        const base = q.top + asc * (q.height / (mt.fontBoundingBoxAscent + mt.fontBoundingBoxDescent));
        const ink = {t: base - inkA, b: base + inkD, l: q.left, r: q.right + sporge};
        const tocca = ink.r > box.l && ink.l < box.r && ink.b > box.t && ink.t < box.b;
        const dentro = ink.l >= box.l - 1 && ink.r <= box.r + 1 && ink.t >= box.t - 1 && ink.b <= box.b + 1;
        if (tocca && !dentro) {
          out.push({tipo: 'testo', testo: testo.slice(0, 60), dove: nome(a) + ' < ' + nome(el), perche: box.perche,
                    px: Math.round(Math.max(box.t - ink.t, ink.b - box.b, box.l - ink.l, ink.r - box.r))});
        }
      }
    }
  }
  if (rx) {
    const re = new RegExp(rx);
    document.querySelectorAll('img').forEach(im => {
      const b = im.getBoundingClientRect();
      if (b.width < 20 || b.bottom < 0 || b.top > vh || !re.test(im.currentSrc || im.src) || !visibile(im)) return;
      if (getComputedStyle(im).objectFit !== 'cover' || !im.naturalWidth) return;
      const ri = im.naturalWidth / im.naturalHeight, rb = b.width / b.height;
      const perso = ri > rb ? 1 - rb / ri : 1 - ri / rb;   // frazione persa (sui due lati)
      if (perso / 2 > 0.04) out.push({tipo: 'schermata', testo: (im.currentSrc || im.src).split('/').pop(), dove: nome(im.parentElement),
                                      perche: (ri > rb ? 'ai lati ' : 'sopra/sotto ') + Math.round(perso * 50) + '% per lato', px: 0});
    });
  }
  return out;
}"""

trovati = {}
with sync_playwright() as p:
    br = p.chromium.launch(channel='chrome')
    for larg in [int(x) for x in a.larghezze.split(',')]:
        for perc in a.percorsi.split(','):
            for l in a.lingue.split(','):
                pg = br.new_page(viewport={'width': larg, 'height': 900 if larg > 600 else 844})
                url = a.base.rstrip('/') + perc + ('' if l == 'it' else ('&' if '?' in perc else '?') + a.param + '=' + l)
                pg.goto(url, wait_until='load')
                pg.wait_for_timeout(1500)
                alto = pg.evaluate('document.documentElement.scrollHeight')
                y = 0
                while True:
                    pg.wait_for_timeout(450)
                    for x in pg.evaluate(JS, a.schermate):
                        if a.ignora and __import__('re').search(a.ignora, x['dove']):
                            continue
                        k = (larg, perc, l, x['tipo'], x['testo'], x['dove'])
                        if k not in trovati or x['px'] > trovati[k]['px']:
                            trovati[k] = dict(x, y=y)
                    if y + 10 >= pg.evaluate('document.documentElement.scrollHeight') - pg.viewport_size['height']:
                        break
                    y += pg.viewport_size['height'] // 2
                    pg.evaluate('window.scrollTo(0, %d)' % y)
                print('fatto', larg, perc, l, '·', sum(1 for k in trovati if k[:3] == (larg, perc, l)), 'segnalazioni', flush=True)
                pg.close()
    br.close()
for k, x in sorted(trovati.items(), key=lambda kv: (kv[0][3], -kv[1]['px'])):
    print(k[0], k[1], k[2], '|', x['tipo'], '|', x['px'], 'px |', x['testo'], '|', x['dove'], '|', x['perche'][:60], '| y', x['y'])
json.dump([dict(zip(('larg', 'pagina', 'lingua', 'tipo', 'testo', 'dove'), k), **v) for k, v in trovati.items()],
          open('tagli.json', 'w', encoding='utf-8'), ensure_ascii=False, indent=0)
