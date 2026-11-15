# -*- coding: utf-8 -*-
"""Apertura della home: spazio fra i testi impilati (in px, dal testo vero), per lingua e larghezza, in ogni stato.
Segnala le coppie con meno di 6 px (o sovrapposte) e i testi che escono dalla finestra.
uso: python sovrapposti.py <url> [larghezze] [lingue]"""
import sys
from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')
URL = sys.argv[1]
LARG = [int(x) for x in (sys.argv[2] if len(sys.argv) > 2 else '390,768,1024,1440').split(',')]
LING = (sys.argv[3] if len(sys.argv) > 3 else 'it,en,de,fr').split(',')
STATI = [('apertura', 0.0, ['h-occ', 'h-t1', 'h-t2', 'h-t3', 'h-s', 'h-bollini', 'h-b1'])]
for k in range(6):
    STATI.append(('fatto%d' % (k + 1), (22 + 11.5 * k + 5.5) / 100, ['h-f%d-occ' % k, 'h-f%d-t1' % k, 'h-f%d-t2' % k, 'h-f%d-s' % k]))
STATI.append(('finale', 0.985, ['h-voto', 'h-voto2', 'h-fine-b']))
JS = r"""(ids) => ids.map(id => { const el = document.querySelector('.mps-layer-' + id); if (!el) return [id, null];
  const a = el.querySelector('a');
  let rr;
  if (a) { const b = a.getBoundingClientRect(); rr = [b]; }
  else { const r = document.createRange(); r.selectNodeContents(el); rr = [...r.getClientRects()].filter(x => x.width > 1); }
  if (!rr.length) return [id, null];
  return [id, {t: Math.min(...rr.map(x => x.top)), b: Math.max(...rr.map(x => x.bottom)), r: Math.max(...rr.map(x => x.right)),
               o: +getComputedStyle(el).opacity, testo: (el.innerText || '').trim().slice(0, 30)}]; })"""
with sync_playwright() as p:
    br = p.chromium.launch(channel='chrome')
    for w in LARG:
        for l in LING:
            pg = br.new_page(viewport={'width': w, 'height': 900 if w > 600 else 844})
            pg.goto(URL + ('' if l == 'it' else '?lang=' + l), wait_until='load'); pg.wait_for_timeout(1500)
            # la lunghezza dello scorrimento dell'apertura: dalla sezione che la contiene
            corsa = pg.evaluate("""() => { const s = document.querySelector('.olo-proslider'); const sec = s.closest('section') || s.parentElement;
                return sec.offsetHeight - innerHeight; }""")
            for nome, f, ids in STATI:
                y = int(corsa * f)
                pg.evaluate('window.scrollTo(0,%d)' % y); pg.wait_for_timeout(900 if nome != 'apertura' else 2200)
                v = [x for x in pg.evaluate(JS, ids) if x[1] and x[1]['o'] > 0.5]
                for (i1, a), (i2, b) in zip(v, v[1:]):
                    gap = round(b['t'] - a['b'])
                    if gap < 6:
                        print(w, l, nome, '| %s → %s: %d px' % (i1, i2, gap), '|', a['testo'], '→', b['testo'])
                for i, a in v:
                    if a['r'] > w - 4:
                        print(w, l, nome, '| %s esce a destra (%d)' % (i, a['r']), '|', a['testo'])
            print('fatto', w, l, '(corsa %d)' % corsa, flush=True)
            pg.close()
    br.close()
