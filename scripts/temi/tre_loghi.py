# -*- coding: utf-8 -*-
"""I loghi dei tre temi «pazzi» (18 nov 2026), come mestieri_loghi.py: il segno e il nome nel carattere dei titoli, in PNG
trasparente con Chrome — <id>-logo-chiaro.png (scritte chiare, per i fondi scuri) e <id>-logo-scuro.png. Stampa le
proporzioni (larghezza/altezza) che i generatori usano.

Uso: python tre_loghi.py <cartella dei media>
"""
import asyncio
import os
import sys

from PIL import Image
from playwright.async_api import async_playwright

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'

PALE = ''.join('<path d="M50 50 L50 6 A22 22 0 0 1 72 28 Z" fill="%s" transform="rotate(%d 50 50)"/>' % (c, a)
               for a, c in ((0, 'S'), (90, '#FF3D7F'), (180, 'S'), (270, '#FF3D7F')))
SCINTILLE = ''.join('<circle cx="%d" cy="%d" r="%s" fill="S"/>' % (x, y, r) for x, y, r in ((90, 14, '3.5'), (12, 88, '3'), (92, 84, '2.5'), (8, 16, '2.5')))

# id: (nome, sotto, famiglia, peso, maiuscolo, spaziatura, colore del segno, segno SVG 0 0 100 100 con «S» = segno, «C» = testo)
LOGHI = {
    'capriola': ('Capriola', 'scuola di acrobatica aerea · Bologna', 'Unbounded', 700, False, '-0.02em', '#FF5A3C',
                 '<line x1="50" y1="0" x2="50" y2="10" stroke="C" stroke-width="5"/>'
                 '<circle cx="50" cy="52" r="40" fill="none" stroke="S" stroke-width="8"/>'
                 '<path d="M50 14 C40 30 60 40 50 60" stroke="C" stroke-width="7" fill="none" stroke-linecap="round"/>'
                 '<path d="M50 30 L34 22 M50 30 L66 22" stroke="C" stroke-width="5" stroke-linecap="round"/>'
                 '<circle cx="50" cy="70" r="8" fill="S"/>'),
    'abbaglio': ('Abbaglio', 'museo delle illusioni · Genova', 'Lexend Zetta', 700, True, '0', '#E8FF3A',
                 '<path d="M4 50 Q50 4 96 50 Q50 96 4 50 Z" fill="none" stroke="C" stroke-width="6"/>'
                 '<circle cx="50" cy="50" r="24" fill="S"/><circle cx="50" cy="50" r="17" fill="C"/>'
                 '<circle cx="50" cy="50" r="10" fill="S"/><circle cx="50" cy="50" r="4" fill="C"/>'),
    'girandola': ('Girandola', 'fuochi d’artificio · Napoli dal 1958', 'Epilogue', 700, False, '-0.02em', '#FFC94A',
                  PALE + '<circle cx="50" cy="50" r="5" fill="C"/>' + SCINTILLE),
}


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


async def main():
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page(viewport={'width': 1900, 'height': 400}, device_scale_factor=1)
        for tid, (nome, sotto, fam, peso, maiusc, sp, col, segno) in LOGHI.items():
            for variante, testo in (('chiaro', '#FFFFFF'), ('scuro', '#14121A')):
                # il giallo acido di Abbaglio sul chiaro non si vede: lì il segno diventa magenta
                segno_col = '#E0218A' if (tid == 'abbaglio' and variante == 'scuro') else ('#E8A400' if (tid == 'girandola' and variante == 'scuro') else col)
                await pg.set_content(html(nome, sotto, fam, peso, maiusc, sp, segno_col, segno, testo))
                await pg.wait_for_timeout(1200)
                el = await pg.query_selector('#l')
                dest = os.path.join(OUT, '%s-logo-%s.png' % (tid, variante))
                await el.screenshot(path=dest, omit_background=True)
                im = Image.open(dest)
                im = im.crop(im.getbbox())
                im.save(dest, optimize=True)
                if variante == 'chiaro':
                    print(tid, '%d/%d' % im.size)
        await b.close()


asyncio.run(main())
