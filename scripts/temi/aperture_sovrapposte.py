# -*- coding: utf-8 -*-
"""Controlla l'apertura Slider Pro della home di ogni demo, diapositiva per diapositiva, alle larghezze date: testi di
livelli diversi che si coprono (anche con la testata sopra lo slider) e testi che escono dallo slider. Per ogni caso
salva il ritaglio dello slider. uso: python aperture_sovrapposte.py uscita.json 390,820,1024,1440 [tema …]"""
import asyncio
import glob
import json
import os
import sys

from playwright.async_api import async_playwright

BASE = 'https://olotheme.com/olobuild/demo/'
PRIVATI = {'clod-evoluzione', 'tutor-clod'}
OUT, LARGHE = sys.argv[1], [int(x) for x in sys.argv[2].split(',')]
SOLO = set(sys.argv[3:])
CARTELLA = os.path.splitext(OUT)[0]
os.makedirs(CARTELLA, exist_ok=True)

TEMI = []
for radice in ['T:/TECNICA/olobuild/assets/data/themes', 'T:/TECNICA/olo-booking/themes']:
    for f in sorted(glob.glob(radice + '/*/theme.json')):
        d = json.load(open(f, encoding='utf-8'))
        tid = d.get('id') or os.path.basename(os.path.dirname(f))
        if tid in PRIVATI or (SOLO and tid not in SOLO) or tid in TEMI:
            continue
        TEMI.append(tid)

INFO = r"""() => {
  const c = document.querySelector('.olo-proslider');
  if (!c) return null;
  const r = c.getBoundingClientRect();
  if (r.top + scrollY > 400) return null;
  let dur = 800; try { dur = JSON.parse(c.getAttribute('data-proslider')).transDuration || 800; } catch (e) {}
  return { n: c.querySelectorAll('.olo-proslider-slide').length, dur,
           tabs: document.querySelectorAll('.olo-proslider-tab').length, dots: c.querySelectorAll('.olo-proslider-dot').length };
}"""

VAI = r"""(k) => {
  const c = document.querySelector('.olo-proslider');
  const t = document.querySelectorAll('.olo-proslider-tab'), d = c.querySelectorAll('.olo-proslider-dot');
  if (t[k]) { t[k].click(); return true; }
  if (d[k]) { d[k].click(); return true; }
  return false;
}"""

JS = r"""() => {
  const c = document.querySelector('.olo-proslider');
  const R = c.getBoundingClientRect();
  const nascosto = el => {
    for (let e = el; e && e !== document.documentElement; e = e.parentElement) {
      const cs = getComputedStyle(e);
      if (cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity) < 0.08) return true;
      if (e.getAttribute && e.getAttribute('aria-hidden') === 'true') return true;
      if (e.classList && (e.classList.contains('uk-offcanvas') || e.classList.contains('uk-dropdown') || e.classList.contains('uk-navbar-dropdown'))) return true;
      if (e.classList && e.classList.contains('olo-proslider-slide') && !e.classList.contains('mps-active')) return true;
    }
    return false;
  };
  const items = [];
  const w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT,
    { acceptNode: n => n.nodeValue.trim().length > 0 ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT });
  let n;
  while ((n = w.nextNode())) {
    const el = n.parentElement;
    if (!el || ['SCRIPT', 'STYLE', 'NOSCRIPT', 'OPTION', 'TEXTAREA', 'TITLE'].includes(el.tagName)) continue;
    const rg = document.createRange(); rg.selectNodeContents(n);
    const rects = [...rg.getClientRects()].filter(x => x.width > 2 && x.height > 5 &&
      x.bottom > R.top && x.top < R.bottom && x.right > R.left && x.left < R.right);
    if (!rects.length || nascosto(el)) continue;
    // le scritte decorative contornate (riempimento trasparente) stanno dietro di proposito
    if (/rgba\([^)]*,\s*0\)$/.test(getComputedStyle(el).color) || getComputedStyle(el).color === 'transparent') continue;
    const livello = el.closest('.olo-proslider-layer') || el.closest('a,button,li,p,h1,h2,h3,h4,h5,h6,span,div') || el;
    const dentro = c.contains(el);
    items.push({ livello, dentro, rects, t: n.nodeValue.trim().replace(/\s+/g, ' ').slice(0, 40) });
  }
  // un livello = un testo: unisce i nodi (le lettere animate una per una sono nodi diversi)
  const gruppi = new Map();
  for (const it of items) {
    if (!gruppi.has(it.livello)) gruppi.set(it.livello, { el: it.livello, dentro: it.dentro, rects: [], t: '' });
    const g = gruppi.get(it.livello);
    g.rects.push(...it.rects);
    if (g.t.length < 40) g.t = (g.t + (g.t && it.t.length > 1 ? ' ' : '') + it.t).slice(0, 40);
  }
  const G = [...gruppi.values()];
  const out = [];
  for (let i = 0; i < G.length; i++) for (let j = i + 1; j < G.length; j++) {
    const a = G[i], b = G[j];
    if (!a.dentro && !b.dentro) continue;
    if (a.el.contains(b.el) || b.el.contains(a.el)) continue;
    let best = 0;
    for (const p of a.rects) for (const q of b.rects) {
      const ix = Math.min(p.right, q.right) - Math.max(p.left, q.left);
      const iy = Math.min(p.bottom, q.bottom) - Math.max(p.top, q.top);
      if (ix <= 2 || iy <= 2) continue;
      if (iy < 0.42 * Math.min(p.height, q.height) || ix < 0.25 * Math.min(p.width, q.width, 40)) continue;
      const fr = (ix / Math.min(p.width, q.width)) * (iy / Math.min(p.height, q.height));
      if (fr > best) best = fr;
    }
    if (best > 0.3) out.push({ tipo: 'sovrapposti', a: a.t, b: b.t, f: Math.round(best * 100) });
  }
  // testi dello slider tagliati dal bordo
  for (const g of G) {
    if (!g.dentro) continue;
    for (const p of g.rects) {
      if (p.right > R.right + 3 || p.left < R.left - 3 || p.bottom > R.bottom + 3) {
        out.push({ tipo: 'fuori', a: g.t, b: '', f: Math.round(Math.max(p.right - R.right, R.left - p.left, p.bottom - R.bottom)) });
        break;
      }
    }
  }
  return { casi: out, box: { x: R.left + scrollX, y: R.top + scrollY, w: R.width, h: R.height } };
}"""


async def uno(browser, sem, tid, w, risultati):
    async with sem:
        h = {390: 844, 820: 1180, 1024: 768}.get(w, 900)
        ctx = await browser.new_context(viewport={'width': w, 'height': h}, is_mobile=w < 700, has_touch=w < 700)
        pg = await ctx.new_page()
        try:
            await pg.goto(BASE + tid + '/', wait_until='load', timeout=120000)
            await pg.wait_for_timeout(4500)
            info = await pg.evaluate(INFO)
            if not info:
                risultati.append({'tema': tid, 'w': w, 'senza_slider': True})
                await ctx.close()
                return
            for k in range(info['n']):
                if k > 0:
                    if not await pg.evaluate(VAI, k):
                        break
                    await pg.wait_for_timeout(info['dur'] + 3400)
                r = await pg.evaluate(JS)
                casi = r['casi']
                if casi:
                    b = r['box']
                    nome = '%s-%d-s%d.jpg' % (tid, w, k + 1)
                    try:
                        await pg.screenshot(path=os.path.join(CARTELLA, nome), type='jpeg', quality=70, full_page=True,
                                            clip={'x': 0, 'y': max(0, b['y']), 'width': w, 'height': min(1400, b['h'] + 20)})
                    except Exception:  # noqa: BLE001
                        nome = ''
                    risultati.append({'tema': tid, 'w': w, 'slide': k + 1, 'casi': casi, 'img': nome})
            risultati.append({'tema': tid, 'w': w, 'fatto': info['n']})
        except Exception as e:  # noqa: BLE001
            risultati.append({'tema': tid, 'w': w, 'errore': str(e)[:200]})
        await ctx.close()


async def main():
    risultati = []
    sem = asyncio.Semaphore(6)
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        await asyncio.gather(*[uno(browser, sem, tid, w, risultati) for tid in TEMI for w in LARGHE])
        await browser.close()
    json.dump(risultati, open(OUT, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
    con = [r for r in risultati if r.get('casi')]
    print(len(TEMI), 'temi ·', sum(1 for r in risultati if r.get('senza_slider')) // max(1, len(LARGHE)), 'senza slider ·',
          len(con), 'diapositive con casi ·', sum(1 for r in risultati if 'errore' in r), 'errori')
    for r in sorted(con, key=lambda r: (r['tema'], r['w'], r['slide'])):
        for c in r['casi']:
            print('%-18s %4d s%d  %-11s %3d  %s  ×  %s' % (r['tema'], r['w'], r['slide'], c['tipo'], c['f'], c['a'], c['b']))


asyncio.run(main())
