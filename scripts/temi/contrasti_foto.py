# -*- coding: utf-8 -*-
"""Il contrasto dei testi sopra foto e video, misurato sui pixel: quello che contrasti.py non sa leggere (sfondi
d'immagine, video, sezioni che si svelano di lato, sezioni pigre).

Per ogni testo: si scorre tutta la pagina (le sezioni lontane arrivano solo così), poi a passi finché il testo è davvero
in primo piano (anche nei gruppi orizzontali, dove lo diventa a metà corsa); si nasconde, si fotografa lo sfondo e si
prende, dentro il suo riquadro, il punto più chiaro (90° percentile) per un testo chiaro o il più scuro (10°) per un
testo scuro. Soglie WCAG AA: 4,5 per i testi normali, 3 per quelli grandi (dai 24 px).

uso: python contrasti_foto.py larghezza URL "testo" [URL "testo" …]
es.: python contrasti_foto.py 390 https://olotheme.com/olobuild/demo/lunga-posa/ "Guardo a lungo." \\
         https://olotheme.com/olobuild/demo/lunga-posa/studio/ "Una stanza, una finestra"
"""
import io
import re
import sys

from PIL import Image
from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')


def lum(c):
    f = lambda v: v / 12.92 if v <= 0.03928 else ((v + 0.055) / 1.055) ** 2.4
    r, g, b = [x / 255 for x in c[:3]]
    return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b)


def colore(s):
    m = [float(x) for x in re.findall(r'[\d.]+', s)[:3]]
    return [v * 255 for v in m] if s.startswith('color(') else m


def in_primo_piano(pg, loc):
    return pg.evaluate("""e => { const r = e.getBoundingClientRect(); if (r.bottom < 0 || r.top > innerHeight || r.width < 2) return false;
        const x = Math.min(innerWidth - 2, Math.max(1, r.left + Math.min(r.width / 2, 30))), y = r.top + r.height / 2;
        const h = document.elementFromPoint(x, y); return !!h && (h === e || e.contains(h) || h.contains(e)); }""", loc.element_handle())


def scorri_tutta(pg):
    h = pg.evaluate('document.documentElement.scrollHeight')
    y = 0
    while y < h:
        pg.evaluate('scrollTo(0,%d)' % y)
        pg.wait_for_timeout(150)
        y += 500
        h = pg.evaluate('document.documentElement.scrollHeight')


def main():
    if len(sys.argv) < 4 or len(sys.argv) % 2:
        sys.exit(__doc__)
    w = int(sys.argv[1])
    casi = list(zip(sys.argv[2::2], sys.argv[3::2]))
    sotto = 0
    with sync_playwright() as p:
        b = p.chromium.launch()
        pg = b.new_page(viewport={'width': w, 'height': 900 if w > 600 else 844})
        pagina = None
        for url, testo in casi:
            if url != pagina:
                pg.goto(url, wait_until='networkidle', timeout=90000)
                pg.wait_for_timeout(2600)
                scorri_tutta(pg)
                pagina = url
            loc = pg.get_by_text(testo).first
            if not loc.count():
                print('%-45s non trovato' % testo[:45])
                continue
            pg.evaluate('scrollTo(0,0)')
            pg.wait_for_timeout(300)
            h = pg.evaluate('document.documentElement.scrollHeight')
            y, trovato = 0, False
            while y < h:
                pg.evaluate('scrollTo(0,%d)' % y)
                pg.wait_for_timeout(260)
                if in_primo_piano(pg, loc):
                    pg.wait_for_timeout(1400)
                    if in_primo_piano(pg, loc):
                        trovato = True
                        break
                y += 140
                h = pg.evaluate('document.documentElement.scrollHeight')
            if not trovato:
                print('%-45s mai in primo piano' % testo[:45])
                continue
            bb = loc.bounding_box()
            st = pg.evaluate('e => { const s = getComputedStyle(e); return [s.color, s.fontSize, s.fontWeight]; }', loc.element_handle())
            fg = colore(st[0])
            pg.evaluate('e => e.style.visibility = "hidden"', loc.element_handle())
            im = Image.open(io.BytesIO(pg.screenshot(type='png'))).convert('RGB')
            pg.evaluate('e => e.style.visibility = ""', loc.element_handle())
            box = (max(0, int(bb['x'])), max(0, int(bb['y'])), min(im.width, int(bb['x'] + bb['width'])), min(im.height, int(bb['y'] + bb['height'])))
            if box[2] <= box[0] or box[3] <= box[1]:
                print('%-45s fuori schermo' % testo[:45])
                continue
            px = sorted(im.crop(box).getdata(), key=lum)
            fondo = px[int(len(px) * 0.9)] if lum(fg) > 0.4 else px[int(len(px) * 0.1)]
            r = (max(lum(fg), lum(fondo)) + 0.05) / (min(lum(fg), lum(fondo)) + 0.05)
            grande = float(st[1].replace('px', '') or 0) >= 24 or (float(st[1].replace('px', '') or 0) >= 18.66 and int(st[2] or 400) >= 700)
            soglia = 3 if grande else 4.5
            esito = 'ok' if r >= soglia else 'SOTTO'
            sotto += esito == 'SOTTO'
            print('%-45s %5.2f/%s %s  testo %s · fondo %s · %s' % (testo[:45], r, soglia, esito, [round(x) for x in fg], fondo, st[1]))
        b.close()
    print('%d testi sotto soglia' % sotto)


if __name__ == '__main__':
    main()
