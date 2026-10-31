# -*- coding: utf-8 -*-
"""Il contrasto di ogni testo dell'apertura Slider Pro, diapositiva per diapositiva, misurato sui pixel (video e foto
compresi): per ogni livello di testo visibile si legge il colore, si nasconde il livello, si fotografa e si prende nel suo
riquadro il punto più chiaro (90° percentile) per un testo chiaro o il più scuro (10°) per un testo scuro. I pulsanti
con un fondo pieno si misurano sul loro fondo. Soglie WCAG AA: 4,5, oppure 3 dai 24 px (18,66 in grassetto).
uso: python aperture_contrasti.py larghezza tema [tema …]"""
import io
import re
import sys

from PIL import Image
from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')
larg, temi = int(sys.argv[1]), sys.argv[2:]


def lum(c):
    f = lambda v: v / 12.92 if v <= 0.03928 else ((v + 0.055) / 1.055) ** 2.4
    r, g, b = [x / 255 for x in c[:3]]
    return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b)


def rapporto(a, b):
    return (max(lum(a), lum(b)) + 0.05) / (min(lum(a), lum(b)) + 0.05)


def colore(s):
    m = [float(x) for x in re.findall(r'[\d.]+', s)[:4]]
    if s.startswith('color('):
        return [v * 255 for v in m[:3]] + m[3:]
    return m


LIVELLI = r"""() => {
  document.querySelectorAll('[data-ce]').forEach(e => e.removeAttribute('data-ce'));
  const c = document.querySelector('.olo-proslider');
  const att = c.querySelector('.olo-proslider-slide.mps-active');
  const els = [...(att ? att.querySelectorAll('.olo-proslider-layer') : []),
               ...document.querySelectorAll('.olo-proslider-global-layers .olo-proslider-layer')];
  const out = [];
  els.forEach((L, i) => {
    const t = (L.innerText || '').replace(/\s+/g, ' ').trim();
    if (!t) return;
    const r0 = L.getBoundingClientRect();
    const cs = getComputedStyle(L);
    if (r0.width < 4 || r0.height < 4 || cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity) < 0.3) return;
    if (r0.bottom < 0 || r0.top > innerHeight) return;
    let el = L.querySelector('h1,h2,h3,h4,h5,h6,p,a,button,span,div') || L;
    while (el.firstElementChild && !el.childNodes[0].nodeValue && el.firstElementChild.tagName !== 'BR') el = el.firstElementChild;
    const s = getComputedStyle(el);
    if (s.color.includes('0, 0, 0, 0') || s.color === 'transparent') return;
    // il riquadro delle righe di testo (non tutto il livello, che può essere più largo della scritta)
    // l'unione dei riquadri delle righe di testo (i nodi di testo, non i blocchi che li contengono)
    const rr = { left: 1e9, top: 1e9, right: -1e9, bottom: -1e9, width: 0 };
    const tw = document.createTreeWalker(L, NodeFilter.SHOW_TEXT);
    let tn;
    while ((tn = tw.nextNode())) {
      if (!tn.nodeValue.trim()) continue;
      const rg = document.createRange(); rg.selectNodeContents(tn);
      for (const q of rg.getClientRects()) {
        if (q.width < 1) continue;
        rr.left = Math.min(rr.left, q.left); rr.top = Math.min(rr.top, q.top);
        rr.right = Math.max(rr.right, q.right); rr.bottom = Math.max(rr.bottom, q.bottom);
      }
    }
    rr.width = rr.right - rr.left;
    const r = rr.width > 2 ? { left: Math.max(r0.left, rr.left), top: Math.max(r0.top, rr.top), right: Math.min(r0.right, rr.right), bottom: Math.min(r0.bottom, rr.bottom) } : r0;
    // il fondo del pulsante o del bollino, se c'è (anche velato: si fonde con quello che c'è dietro)
    let fondo = '';
    for (let e = el; e && e !== L.parentElement; e = e.parentElement) {
      const b = getComputedStyle(e).backgroundColor;
      const m = b.match(/[\d.]+/g);
      if (m && (m.length < 4 || parseFloat(m[3]) >= 0.3)) { fondo = b; break; }
    }
    L.setAttribute('data-ce', i);
    out.push({ i, t: t.slice(0, 34), color: s.color, fs: parseFloat(s.fontSize), fw: parseInt(s.fontWeight) || 400, fondo,
               box: [r.left, r.top, r.right, r.bottom] });
  });
  return out;
}"""

sotto_tot = 0
with sync_playwright() as p:
    b = p.chromium.launch()
    for tema in temi:
        pg = b.new_page(viewport={'width': larg, 'height': {390: 844, 820: 1180}.get(larg, 900)}, is_mobile=larg < 700,
                        has_touch=larg < 700)
        pg.goto('https://olotheme.com/olobuild/demo/%s/' % tema, wait_until='load', timeout=120000)
        pg.wait_for_timeout(3500)
        n = pg.evaluate("() => document.querySelectorAll('.olo-proslider .olo-proslider-slide').length")
        for k in range(n):
            if k:
                pg.evaluate("(k) => { const t = document.querySelectorAll('.olo-proslider-tab'), d = document.querySelectorAll('.olo-proslider-dot'); const e = t[k] || d[k] || document.querySelector('.olo-proslider-next'); if (e) e.click(); }", k)
                pg.wait_for_timeout(4800)
            # ferma lo scorrimento automatico mentre si misura
            pg.evaluate("() => document.querySelector('.olo-proslider').dispatchEvent(new MouseEvent('mouseenter'))")
            for L in pg.evaluate(LIVELLI):
                fg = colore(L['color'])
                cf = colore(L['fondo']) if L['fondo'] else []
                alfa = cf[3] if len(cf) > 3 else 1.0
                if cf and alfa >= 0.95:
                    fondo = cf[:3]
                    via = 'pieno'
                else:
                    pg.evaluate("(i) => document.querySelector('[data-ce=\"' + i + '\"]').style.setProperty('opacity', '0', 'important')", L['i'])
                    pg.wait_for_timeout(60)
                    im = Image.open(io.BytesIO(pg.screenshot(type='png'))).convert('RGB')
                    pg.evaluate("(i) => document.querySelector('[data-ce=\"' + i + '\"]').style.removeProperty('opacity')", L['i'])
                    x0, y0, x1, y1 = [int(v) for v in L['box']]
                    box = (max(0, x0), max(0, y0), min(im.width, x1), min(im.height, y1))
                    if box[2] <= box[0] or box[3] <= box[1]:
                        continue
                    px = sorted(list(im.crop(box).getdata()), key=lum)
                    fondo = px[int(len(px) * 0.9)] if lum(fg) > 0.4 else px[int(len(px) * 0.1)]
                    via = 'pixel'
                    if cf:
                        fondo = [alfa * cf[i] + (1 - alfa) * fondo[i] for i in range(3)]
                        via = 'velato %d%%' % round(alfa * 100)
                r = rapporto(fg, fondo)
                grande = L['fs'] >= 24 or (L['fs'] >= 18.66 and L['fw'] >= 700)
                soglia = 3 if grande else 4.5
                if r < soglia:
                    sotto_tot += 1
                    print('%-15s s%d SOTTO %5.2f/%s  %-34s %s su %s (%s, %dpx)' % (tema, k + 1, r, soglia, L['t'],
                          [round(x) for x in fg[:3]], [round(x) for x in fondo[:3]], via, L['fs']))
        print('%-15s fatto (%d diapositive)' % (tema, n))
        pg.close()
    b.close()
print(sotto_tot, 'testi sotto soglia')
