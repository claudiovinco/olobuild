# -*- coding: utf-8 -*-
"""Provini delle testate dei temi sul sottosito: la barra del computer (1440) o il menu mobile aperto (390), con il
contrasto dei testi del pannello aperto. Scrive le foto in una cartella e un provino unico per gruppi di temi.
uso: python testate_foto.py pc|tel cartella [tema ...]     senza temi: tutti quelli con header.json
"""
import json
import os
import sys

from PIL import Image, ImageDraw
from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import testate  # noqa: E402
# Le misure del contrasto sono quelle di contrasti.py (che si esegue appena importato: se ne legge solo lo script).
_src = open(os.path.join(os.path.dirname(os.path.abspath(__file__)), 'contrasti.py'), encoding='utf-8').read()
JS_UTIL = _src.split('JS_UTIL = r"""', 1)[1].split('"""', 1)[0]

BASE = 'https://olotheme.com/olobuild/demo/%s/'

PANNELLO = """(() => {
  const p = [...document.querySelectorAll('.olo-mm-offcanvas, .olo-mm-fullscreen, .olo-mm-dropdown-panel')]
    .find(e => { const r = e.getBoundingClientRect(); const cs = getComputedStyle(e);
                 return cs.display !== 'none' && cs.visibility !== 'hidden' && r.width > 100 && r.height > 60 && r.right > 10; });
  if(!p){ return {aperto: false, bassi: []}; }
  const out = [];
  p.querySelectorAll('a, p, span, button').forEach(e => {
    if(!window.__ctr.visibile(e) || e.closest('[aria-hidden="true"]')){ return; }
    const proprio = [...e.childNodes].some(n => n.nodeType === 3 && n.textContent.trim().length > 1);
    if(!proprio){ return; }
    const r = e.getBoundingClientRect(); if(r.bottom < 0 || r.top > innerHeight){ return; }
    const m = window.__ctr.misura(e); m.testo = e.textContent.trim().slice(0, 30);
    if(m.r < m.soglia && m.op > .6){ out.push(m); }
  });
  return {aperto: true, bassi: out, cls: p.className};
})()"""


def apri(pg):
    return pg.evaluate("""(() => {
      const h = [...document.querySelectorAll('.olo-mm-hamburger')].find(e => e.offsetParent !== null && e.getBoundingClientRect().width > 0);
      if(!h){ return false; } h.click(); return true; })()""")


def provino(cartella, nomi, lato, colonne, nome):
    w, h = lato
    righe = (len(nomi) + colonne - 1) // colonne
    foglio = Image.new('RGB', (colonne * (w + 12) + 12, righe * (h + 34) + 12), (235, 235, 235))
    d = ImageDraw.Draw(foglio)
    for i, t in enumerate(nomi):
        f = os.path.join(cartella, t + '.png')
        if not os.path.exists(f):
            continue
        im = Image.open(f).convert('RGB')
        im.thumbnail((w, h))
        x, y = 12 + (i % colonne) * (w + 12), 12 + (i // colonne) * (h + 34)
        foglio.paste(im, (x, y + 22))
        d.text((x, y + 4), t, fill=(20, 20, 20))
    foglio.save(os.path.join(cartella, nome), quality=82)


def main():
    modo, cartella = sys.argv[1], sys.argv[2]
    temi = sys.argv[3:] or sorted(d for d in os.listdir(testate.TEMI) if os.path.exists(os.path.join(testate.TEMI, d, 'header.json')))
    os.makedirs(cartella, exist_ok=True)
    tel = modo == 'tel'
    vw, vh = (390, 844) if tel else (1440, 900)
    rapporto = {}
    with sync_playwright() as p:
        b = p.chromium.launch()
        ctx = b.new_context(viewport={'width': vw, 'height': vh}, is_mobile=tel, has_touch=tel, device_scale_factor=1)
        pg = ctx.new_page()
        for t in temi:
            try:
                pg.goto(BASE % t, wait_until='networkidle', timeout=90000)
            except Exception:
                pg.goto(BASE % t, wait_until='load', timeout=90000)
            pg.wait_for_timeout(1800)
            if tel:
                pg.screenshot(path=os.path.join(cartella, t + '-barra.png'), clip={'x': 0, 'y': 0, 'width': vw, 'height': 150})
                ok = apri(pg)
                pg.wait_for_timeout(1300)
                pg.screenshot(path=os.path.join(cartella, t + '.png'))
                pg.add_script_tag(content=JS_UTIL)
                m = pg.evaluate(PANNELLO) if ok else {'aperto': False, 'bassi': []}
                rapporto[t] = m
                print(t, 'aperto' if m['aperto'] else 'NON APERTO', ' '.join('%.2f«%s»' % (x['r'], x['testo']) for x in m['bassi'])[:300])
            else:
                pg.screenshot(path=os.path.join(cartella, t + '.png'), clip={'x': 0, 'y': 0, 'width': vw, 'height': 190})
                print(t)
        b.close()
    json.dump(rapporto, open(os.path.join(cartella, 'rapporto.json'), 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
    passo = 24 if tel else 14
    for k in range(0, len(temi), passo):
        gruppo = temi[k:k + passo]
        if tel:
            provino(cartella, gruppo, (195, 422), 8, 'provino-%02d.jpg' % (k // passo + 1))
        else:
            provino(cartella, gruppo, (1080, 143), 2, 'provino-%02d.jpg' % (k // passo + 1))


if __name__ == '__main__':
    main()
