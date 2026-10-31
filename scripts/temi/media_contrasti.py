# -*- coding: utf-8 -*-
"""Il contrasto dei testi che stanno sopra una foto o un video (sezioni, righe, colonne e tile con lo Sfondo a media),
misurato sui pixel: dopo la regola del 31 ott 2026 (mai media velati) i veli sono leggeri o sfumati da un lato, e il
testo deve leggersi lo stesso.

Per ogni pagina dei temi indicati che ha uno Sfondo a media con del testo sopra: si scorre la pagina (le sezioni pigre
arrivano), poi per ogni contenitore a media si porta in vista, si nascondono i suoi testi, si fotografa e per ogni testo si
prende, nel suo riquadro, il punto più chiaro (90° percentile) per un testo chiaro o il più scuro (10°) per uno scuro.
Soglie WCAG AA: 4,5, o 3 per i testi grandi (24 px, o 18,66 in grassetto). Lo Slider Pro ha il suo strumento
(aperture_contrasti.py).

uso: python media_contrasti.py larghezza uscita.json tema [tema …]
"""
import io
import json
import re
import os
import sys

from PIL import Image
from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')
DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes')
BASE = 'https://olotheme.com/olobuild/demo/%s/'


def lum(c):
    f = lambda v: v / 12.92 if v <= 0.03928 else ((v + 0.055) / 1.055) ** 2.4
    r, g, b = [x / 255 for x in c[:3]]
    return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b)


def ha_media(o):
    """Il file del template ha uno Sfondo a foto o video (fuori dallo Slider Pro)?"""
    if isinstance(o, dict):
        if o.get('type') in ('image', 'video') and (o.get('image_url') or o.get('video_url')) and 'overlay_opacity' in o:
            return True
        return any(ha_media(v) for k, v in o.items() if k != 'slides')
    if isinstance(o, list):
        return any(ha_media(v) for v in o)
    return False


def pagine(tema):
    th = json.load(open(os.path.join(DIR, tema, 'theme.json'), encoding='utf-8'))
    out = []
    for k, pg in (th.get('pages') or {}).items():
        f = os.path.join(DIR, tema, (pg.get('template') or k) + '.json')
        try:
            j = json.load(open(f, encoding='utf-8'))
        except (OSError, ValueError):
            continue
        if ha_media(j):
            out.append(BASE % tema + ('' if pg.get('set_as_homepage') else (pg.get('slug') or k) + '/'))
    return out


JS_TROVA = r"""() => {
  // I contenitori con un media di sfondo: un <video> o un'immagine di sfondo come livello assoluto, o background-image.
  const fuori = e => e.closest('.olo-proslider, header, .olo-site-header, footer');
  const cont = new Set();
  document.querySelectorAll('video').forEach(v => {
    const cs = getComputedStyle(v); if (cs.position !== 'absolute' || fuori(v)) return;
    let p = v.parentElement; while (p && p.offsetHeight < 80) p = p.parentElement; if (p) cont.add(p); });
  document.querySelectorAll('section, .uk-section, [class*="olo-"]').forEach(e => {
    const bi = getComputedStyle(e).backgroundImage; if (bi && /url\(/.test(bi) && e.offsetHeight > 80 && !fuori(e)) cont.add(e); });
  const lista = [...cont].filter(c => ![...cont].some(d => d !== c && d.contains(c) && false));
  window.__mc = lista;
  return lista.map((c, i) => {
    const testi = [];
    c.querySelectorAll('*').forEach(e => {
      if (!e.offsetParent && getComputedStyle(e).position !== 'fixed') return;
      if (e.closest('.olo-proslider')) return;
      const proprio = [...e.childNodes].some(n => n.nodeType === 3 && n.textContent.trim().length > 1);
      if (!proprio) return;
      // un fondo pieno fra il testo e il contenitore: il testo sta su una scheda, non sul media
      let n = e, scheda = false;
      while (n && n !== c) { const bg = getComputedStyle(n).backgroundColor; const m = bg.match(/rgba?\(([^)]+)\)/);
        if (m) { const p = m[1].split(/[ ,\/]+/).filter(Boolean).map(Number); if ((p.length < 4 || p[3] > 0.85) && !(p[0] === 0 && p[1] === 0 && p[2] === 0 && p.length > 3 && p[3] === 0)) { scheda = true; break; } }
        n = n.parentElement; }
      if (scheda) return;
      const cs = getComputedStyle(e);
      if (cs.visibility === 'hidden' || +cs.opacity < 0.5) return;
      testi.push(e);
    });
    window['__mt' + i] = testi;
    return testi.length;
  });
}"""


def misura(pg, i):
    n = pg.evaluate('i => window["__mt" + i].length', i)
    if not n:
        return []
    pg.evaluate('i => window.__mc[i].scrollIntoView({block: "center"})', i)
    pg.wait_for_timeout(1500)
    info = pg.evaluate("""i => window['__mt' + i].map(e => { const r = e.getBoundingClientRect(); const s = getComputedStyle(e);
        return {t: e.textContent.trim().slice(0, 50), x: r.x, y: r.y, w: r.width, h: r.height, c: s.webkitTextFillColor || s.color,
                f: parseFloat(s.fontSize), p: parseInt(s.fontWeight) || 400,
                clip: /text/.test(s.webkitBackgroundClip || s.backgroundClip || '')}; })""", i)
    # opacità e non visibilità: le lettere animate (ingresso a lettere, effetti testo) hanno la loro visibilità e
    # restavano lì, e il testo si misurava contro sé stesso
    pg.evaluate("i => window['__mt' + i].forEach(e => e.style.setProperty('opacity', '0', 'important'))", i)
    pg.wait_for_timeout(120)
    im = Image.open(io.BytesIO(pg.screenshot(type='png'))).convert('RGB')
    pg.evaluate("i => window['__mt' + i].forEach(e => e.style.removeProperty('opacity'))", i)
    out = []
    for d in info:
        # testo colorato da un gradiente (riempimento trasparente): il colore non si legge, si salta
        if d.get('clip') or re.search(r'rgba\([^)]*,\s*0\)$', d['c']):
            continue
        box = (max(0, int(d['x'])), max(0, int(d['y'])), min(im.width, int(d['x'] + d['w'])), min(im.height, int(d['y'] + d['h'])))
        if box[2] - box[0] < 3 or box[3] - box[1] < 3:
            continue
        nums = [float(x) for x in d['c'].replace('rgba(', '').replace('rgb(', '').replace(')', '').replace('color(srgb', '').split(',' if ',' in d['c'] else None)[:3]]
        fg = [v * 255 for v in nums] if d['c'].startswith('color(') else nums
        px = sorted(im.crop(box).getdata(), key=lum)
        fondo = px[int(len(px) * 0.9)] if lum(fg) > 0.4 else px[int(len(px) * 0.1)]
        r = (max(lum(fg), lum(fondo)) + 0.05) / (min(lum(fg), lum(fondo)) + 0.05)
        grande = d['f'] >= 24 or (d['f'] >= 18.66 and d['p'] >= 700)
        out.append({'testo': d['t'], 'r': round(r, 2), 'soglia': 3 if grande else 4.5, 'px': d['f'], 'fondo': list(fondo)})
    return out


def main():
    w, uscita, temi = int(sys.argv[1]), sys.argv[2], sys.argv[3:]
    ris = json.load(open(uscita, encoding='utf-8')) if os.path.exists(uscita) else {}
    with sync_playwright() as p:
        b = p.chromium.launch()
        pg = b.new_page(viewport={'width': w, 'height': 900 if w > 600 else 844})
        for tema in temi:
            for url in pagine(tema):
                if url in ris:
                    continue
                try:
                    pg.goto(url, wait_until='load', timeout=90000)
                    pg.wait_for_timeout(2000)
                    h = pg.evaluate('document.documentElement.scrollHeight')
                    for y in range(0, h, 600):
                        pg.evaluate('scrollTo(0,%d)' % y)
                        pg.wait_for_timeout(110)
                    conti = pg.evaluate(JS_TROVA)
                    righe = []
                    for i, n in enumerate(conti):
                        righe += misura(pg, i)
                except Exception as e:  # una pagina che non risponde non ferma le altre
                    righe = [{'errore': str(e)[:160]}]
                ris[url] = righe
                sotto = [r for r in righe if 'r' in r and r['r'] < r['soglia']]
                print('%-70s %3d testi, %d sotto' % (url[len('https://olotheme.com/olobuild/demo/'):], len(righe), len(sotto)), flush=True)
                for r in sotto:
                    print('      %5.2f/%s %4.0fpx %s' % (r['r'], r['soglia'], r['px'], r['testo']), flush=True)
                json.dump(ris, open(uscita, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
        b.close()


if __name__ == '__main__':
    main()
