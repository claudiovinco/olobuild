# -*- coding: utf-8 -*-
"""Le aperture (Pro Slider della home) di tutte le demo del sottosito a una larghezza da telefono: per ogni slide, i
livelli di testo e i pulsanti visibili con il carattere reale (calcolato × scala della tela, letta dalla sua trasformazione: una diapositiva a metà animazione non conta), la posizione rispetto alla
testata che sta sopra lo slider e la distanza dai bordi. Segnala: testo sotto la testata, paragrafi sotto i 14 px reali
(altri testi e scritte in maiuscolo sotto gli 11, come il minimo del Pro Slider), pulsanti o testi a meno di 6 px dal bordo.
uso: python aperture_leggibili.py uscita.json [larghezza] [tema …]"""
import glob
import json
import os
import sys
from concurrent.futures import ThreadPoolExecutor

from playwright.sync_api import sync_playwright

sys.path.insert(0, r'T:\TECNICA\SOCIAL\guide-in-un-minuto\sorgente')
import regia_ot01 as U  # noqa: E402

OUT = sys.argv[1]
W = int(sys.argv[2]) if len(sys.argv) > 2 and sys.argv[2].isdigit() else 390
SOLO = set(a for a in sys.argv[2:] if not a.isdigit())
BASE = 'https://olotheme.com/olobuild/demo/'
PRIVATI = {'clod-evoluzione', 'tutor-clod'}
TEMI = []
for radice in ['T:/TECNICA/olobuild/assets/data/themes', 'T:/TECNICA/olo-booking/themes']:
    for f in sorted(glob.glob(radice + '/*/theme.json')):
        d = json.load(open(f, encoding='utf-8'))
        tid = d.get('id') or os.path.basename(os.path.dirname(f))
        if tid in PRIVATI or (SOLO and tid not in SOLO) or tid in TEMI:
            continue
        TEMI.append(tid)

JS = r"""() => { const c = document.querySelector('.olo-proslider'); if (!c) return {nessuno: true};
  scrollTo(0, 0); const cr = c.getBoundingClientRect(); const out = {cw: Math.round(cr.width), ch: Math.round(cr.height), slides: []};
  let hb = 0; for (const h of document.querySelectorAll('header, .olo-header, [data-olo-zone="header"], .olo-site-header')) {
    const r = h.getBoundingClientRect(); if (r.height > 20 && r.top <= cr.top + 2 && r.bottom > cr.top) hb = Math.max(hb, r.bottom - cr.top); }
  out.testata = Math.round(hb);
  c.querySelectorAll('.olo-proslider-slide').forEach((sl, si) => { const sr = sl.getBoundingClientRect(); const liv = [];
    sl.querySelectorAll('.olo-proslider-layer').forEach(L => { const cs = getComputedStyle(L); if (cs.display === 'none' || cs.visibility === 'hidden') return;
      const t = (L.textContent || '').trim().replace(/\s+/g, ' '); if (!t) return;
      const btn = !!L.querySelector('a, button') && t.length < 40; const el = L.querySelector('h1,h2,h3,h4,h5,h6,p,a,button,span,div') || L;
      const wrap = L.closest('.mps-layers-wrap'); const mt = wrap ? getComputedStyle(wrap).transform : 'none'; const sc = mt && mt.startsWith('matrix(') ? parseFloat(mt.slice(7)) : 1;
      const r = (btn ? (L.querySelector('a, button') || L) : el).getBoundingClientRect(); if (!r.width) return;
      const fs = parseFloat(getComputedStyle(el).fontSize) * sc;
      const id = ([...L.classList].find(c => c.startsWith('mps-layer-')) || '').slice(10);
      liv.push({id: id, sc: Math.round(sc * 1000) / 1000, fsd: parseFloat(getComputedStyle(el).fontSize), t: t.slice(0, 40), btn: btn, fs: Math.round(fs * 10) / 10, y: Math.round(r.top - sr.top), b: Math.round(r.bottom - sr.top),
                sx: Math.round(r.left - sr.left), dx: Math.round(sr.right - r.right), lung: t.length, maiu: getComputedStyle(el).textTransform === 'uppercase'}); });
    out.slides.push(liv); });
  return out }"""


def problemi(m):
    out = []
    if m.get('nessuno'):
        return out
    for si, liv in enumerate(m['slides']):
        for l in liv:
            if l['b'] <= 0 or l['y'] >= m['ch']:
                continue
            if m['testata'] and l['y'] < m['testata'] - 2:
                out.append(['sotto la testata', si + 1, l['t'], l['y'], m['testata']])
            if not l['btn'] and l['lung'] >= 40 and not l.get('maiu') and l['fs'] < 14:
                out.append(['paragrafo piccolo', si + 1, l['t'], l['fs']])
            elif l['fs'] < 11:
                out.append(['testo piccolo', si + 1, l['t'], l['fs']])
            if l['sx'] < 6 or l['dx'] < 6:
                out.append(['sul bordo', si + 1, l['t'], l['sx'], l['dx']])
    return out


def misura(temi):
    ris = {}
    with sync_playwright() as pw:
        b = pw.chromium.launch(channel='chrome', headless=True)
        mob = W < 800
        ctx = b.new_context(viewport={'width': W, 'height': 844 if mob else 1180}, is_mobile=mob, has_touch=mob,
                            device_scale_factor=1, locale='it-IT')
        ctx.add_init_script(U.PRIVACY)
        p = ctx.new_page()
        for t in temi:
            try:
                p.goto(BASE + t + '/', wait_until='networkidle', timeout=60000)
                p.wait_for_timeout(3500)
                m = p.evaluate(JS)
                ris[t] = {'misure': m, 'problemi': problemi(m)}
            except Exception as e:
                ris[t] = {'errore': str(e)[:200]}
            print(t, len(ris[t].get('problemi', [])) if 'problemi' in ris[t] else ris[t].get('errore'), flush=True)
        b.close()
    return ris


gruppi = [TEMI[i::4] for i in range(4)]
tutto = {}
with ThreadPoolExecutor(4) as ex:
    for r in ex.map(misura, gruppi):
        tutto.update(r)
json.dump(tutto, open(OUT, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
con = {t: v['problemi'] for t, v in tutto.items() if v.get('problemi')}
print('temi', len(tutto), 'con problemi', len(con))
