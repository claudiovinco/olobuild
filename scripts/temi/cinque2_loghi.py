# -*- coding: utf-8 -*-
"""I loghi dei cinque temi «pazzi» della sera del 18 nov 2026 (Dorso, Lanterna, Zapping, Torre, Scontrino), come
cinque_loghi.py: il segno e il nome nel carattere dei titoli, in PNG trasparente con Chrome — <id>-logo-chiaro.png
(scritte chiare, per i fondi scuri) e <id>-logo-scuro.png. Stampa le proporzioni (larghezza/altezza) che i generatori usano.

Uso: python cinque2_loghi.py <cartella dei media>
"""
import asyncio
import math
import os
import sys

from PIL import Image
from playwright.async_api import async_playwright

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'


# (copiata da cinque_loghi.py, che non si può importare: alla fine rifà i suoi loghi)
def html(nome, sotto, fam, peso, maiusc, sp, segno_col, segno, testo_col):
    s = segno.replace('"S"', '"%s"' % segno_col).replace('"C"', '"%s"' % testo_col)
    return ('<html><head><link href="https://fonts.googleapis.com/css2?family=%s:wght@%d&display=block" rel="stylesheet">'
            '<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@500&display=block" rel="stylesheet"></head>'
            '<body style="margin:0;background:transparent"><div id="l" style="display:inline-flex;align-items:center;gap:22px;padding:6px">'
            '<svg viewBox="0 0 100 100" width="118" height="118">%s</svg>'
            '<div><div style="font-family:\'%s\';font-weight:%d;font-size:92px;line-height:.95;letter-spacing:%s;color:%s;%s">%s</div>'
            '<div style="font-family:\'Figtree\';font-weight:500;font-size:24px;letter-spacing:.14em;text-transform:uppercase;color:%s;opacity:.82;margin-top:12px">%s</div></div>'
            '</div></body></html>') % (fam.replace(' ', '+'), peso, s, fam, peso, sp, testo_col,
                                      'text-transform:uppercase;' if maiusc else '', nome, testo_col, sotto)

# id: (nome, sotto, famiglia, peso, maiuscolo, spaziatura, colore del segno sul chiaro, sul scuro, segno SVG 0 0 100 100
# con «S» = segno, «C» = testo)
LOGHI = {
    'dorso': ('Dorso', 'legatoria · Firenze', 'Fraunces', 700, False, '-0.01em', '#D9B45A', '#8C1C2B',
              '<rect x="30" y="6" width="40" height="88" rx="5" fill="S"/>'
              + ''.join('<rect x="27" y="%d" width="46" height="5" rx="2.5" fill="C"/>' % y for y in (20, 36, 60, 76))
              + '<rect x="38" y="44" width="24" height="10" rx="1.5" fill="none" stroke="C" stroke-width="2.5"/>'),
    'lanterna': ('Lanterna', 'escape room · Genova', 'Cinzel', 700, True, '0.04em', '#FFB547', '#C77A10',
                 '<path d="M50 30 L96 12 L96 48 Z" fill="S" opacity=".55"/><path d="M50 30 L4 12 L4 48 Z" fill="S" opacity=".3"/>'
                 '<rect x="40" y="20" width="20" height="20" rx="2" fill="S"/>'
                 '<path d="M36 20 H64 L58 12 H42 Z" fill="C"/><path d="M42 40 H58 L64 94 H36 Z" fill="C"/>'
                 '<rect x="44" y="56" width="12" height="5" fill="S"/><rect x="44" y="72" width="12" height="5" fill="S"/>'),
    'zapping': ('Zapping', 'cineclub · Torino', 'Bungee', 400, True, '0.02em', '#7CFF6B', '#E0312B',
                '<path d="M34 6 L50 22 L66 6" fill="none" stroke="C" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>'
                '<rect x="6" y="22" width="88" height="66" rx="12" fill="none" stroke="C" stroke-width="7"/>'
                '<rect x="16" y="32" width="56" height="46" rx="9" fill="S"/>'
                '<circle cx="83" cy="42" r="4.5" fill="C"/><circle cx="83" cy="58" r="4.5" fill="C"/>'),
    'torre': ('Torre', 'B&B in una torre · San Gimignano', 'Marcellus', 400, True, '0.06em', '#C9A45C', '#9A6B2E',
              '<path d="M30 94 V16 L36 8 H64 L70 16 V94 Z" fill="none" stroke="C" stroke-width="6" stroke-linejoin="round"/>'
              + ''.join('<rect x="46" y="%d" width="8" height="10" rx="1" fill="S"/>' % y for y in (22, 40, 58))
              + '<path d="M50 74 L58 84 H42 Z" fill="S"/>'),
    'scontrino': ('Scontrino', 'salumeria · Bologna · dal 1958', 'Courier Prime', 700, True, '0.02em', '#F0EADB', '#D8342B',
                  '<path d="M18 6 H82 V82 ' + ' '.join('L%d %d' % (82 - k * 8, 94 if k % 2 else 82) for k in range(1, 9))
                  + ' Z" fill="S"/>'
                  + ''.join('<rect x="28" y="%d" width="%d" height="5" rx="2" fill="C"/>' % (y, w)
                            for y, w in ((18, 44), (30, 30), (42, 40), (54, 22), (66, 44)))),
}


async def main():
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page(viewport={'width': 1900, 'height': 400}, device_scale_factor=1)
        for tid, (nome, sotto, fam, peso, maiusc, sp, col_chiaro, col_scuro, segno) in LOGHI.items():
            for variante, testo in (('chiaro', '#FFFFFF'), ('scuro', '#14121A')):
                segno_col = col_chiaro if variante == 'chiaro' else col_scuro
                await pg.set_content(html(nome, sotto, fam, peso, maiusc, sp, segno_col, segno, testo))
                await pg.wait_for_timeout(1400)
                el = await pg.query_selector('#l')
                dest = os.path.join(OUT, '%s-logo-%s.png' % (tid, variante))
                await el.screenshot(path=dest, omit_background=True)
                im = Image.open(dest)
                im = im.crop(im.getbbox())
                im.save(dest, optimize=True)
                if variante == 'chiaro':
                    print(tid, '%d/%d' % im.size)
        await b.close()


if __name__ == '__main__':
    asyncio.run(main())
