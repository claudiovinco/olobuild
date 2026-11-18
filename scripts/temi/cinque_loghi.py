# -*- coding: utf-8 -*-
"""I loghi dei cinque temi «pazzi» (18 nov 2026), come mestieri_loghi.py: il segno e il nome nel carattere dei titoli, in PNG
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

# id: (nome, sotto, famiglia, peso, maiuscolo, spaziatura, colore del segno sul chiaro, sul scuro, segno SVG 0 0 100 100
# con «S» = segno, «C» = testo)
LOGHI = {
    'tornanti': ('Tornanti', 'noleggio e-bike · Bormio', 'Big Shoulders Display', 800, True, '0.02em', '#FF4A44', '#E5322D',
                 '<path d="M10 88 H78 A12 12 0 0 0 78 64 H24 A12 12 0 0 1 24 40 H78 A12 12 0 0 0 78 16 H58" fill="none" '
                 'stroke="S" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>'
                 '<circle cx="58" cy="16" r="7" fill="C"/>'),
    'persiana': ('Persiana', 'agenzia immobiliare · Lucca', 'Bodoni Moda', 700, False, '-0.01em', '#7FB5A1', '#2F5D50',
                 '<rect x="20" y="8" width="60" height="84" rx="4" fill="none" stroke="C" stroke-width="6"/>'
                 + ''.join('<line x1="28" y1="%d" x2="72" y2="%d" stroke="S" stroke-width="5" stroke-linecap="round"/>' % (y, y - 4)
                           for y in range(22, 86, 10))),
    'ruota-libera': ('Ruota Libera', 'ciclofficina popolare · Padova', 'Bricolage Grotesque', 800, False, '-0.02em', '#FF7A2E',
                     '#E85A0C', '<circle cx="50" cy="50" r="40" fill="none" stroke="C" stroke-width="8"/>'
                     + ''.join('<line x1="50" y1="50" x2="%.1f" y2="%.1f" stroke="S" stroke-width="3.5"/>'
                               % (50 + 36 * __import__('math').cos(a * 3.14159 / 6), 50 + 36 * __import__('math').sin(a * 3.14159 / 6))
                               for a in range(12))
                     + '<circle cx="50" cy="50" r="8" fill="S"/>'),
    'vetrino': ('Vetrino', 'laboratorio analisi · Parma', 'Sora', 700, False, '-0.02em', '#B49CFF', '#7B3FE4',
                '<rect x="6" y="32" width="88" height="36" rx="5" fill="none" stroke="C" stroke-width="6"/>'
                '<circle cx="56" cy="50" r="12" fill="S"/><circle cx="56" cy="50" r="5" fill="C"/>'
                '<rect x="14" y="40" width="16" height="20" rx="2" fill="S" opacity=".55"/>'),
    'doppio-senso': ('Doppio Senso', 'traduzioni e interpreti · Trieste', 'Cormorant Garamond', 700, False, '0', '#E8B04B',
                     '#B8862A', '<path d="M8 34 H80 M64 18 L82 34 L64 50" fill="none" stroke="S" stroke-width="8" '
                     'stroke-linecap="round" stroke-linejoin="round"/>'
                     '<path d="M92 66 H20 M36 50 L18 66 L36 82" fill="none" stroke="C" stroke-width="8" stroke-linecap="round" '
                     'stroke-linejoin="round"/>'),
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
        for tid, (nome, sotto, fam, peso, maiusc, sp, col_chiaro, col_scuro, segno) in LOGHI.items():
            for variante, testo in (('chiaro', '#FFFFFF'), ('scuro', '#14121A')):
                segno_col = col_chiaro if variante == 'chiaro' else col_scuro
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
