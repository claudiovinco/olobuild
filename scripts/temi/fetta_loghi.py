# -*- coding: utf-8 -*-
"""Il logo di Fetta (21 nov 2026, era Scontrino): la lama tonda dell'affettatrice col perno e una fetta che se ne stacca,
e il nome in DM Serif Display, come cinque2_loghi.py: fetta-logo-chiaro.png (scritte chiare) e fetta-logo-scuro.png.
Uso: python fetta_loghi.py <cartella dei media>
"""
import asyncio
import os
import sys

from PIL import Image
from playwright.async_api import async_playwright

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import cinque2_loghi as L  # noqa: E402

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'
SEGNO = ('<circle cx="46" cy="50" r="38" fill="S"/>'
         '<circle cx="46" cy="50" r="27" fill="none" stroke="C" stroke-width="3.5" stroke-opacity=".9"/>'
         '<circle cx="46" cy="50" r="7" fill="C"/>'
         # la fetta che si stacca dal bordo della lama
         '<path d="M78 22 A40 40 0 0 1 90 58 L84 60 A34 34 0 0 0 73 27 Z" fill="C"/>')


async def main():
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page(viewport={'width': 1900, 'height': 400}, device_scale_factor=1)
        for variante, testo, segno_col in (('chiaro', '#FFFFFF', '#E9545A'), ('scuro', '#14121A', '#BE2720')):
            await pg.set_content(L.html('Fetta', 'salumeria · Bologna · dal 1958', 'DM Serif Display', 400, False, '0',
                                        segno_col, SEGNO, testo))
            await pg.wait_for_timeout(1500)
            el = await pg.query_selector('#l')
            dest = os.path.join(OUT, 'fetta-logo-%s.png' % variante)
            await el.screenshot(path=dest, omit_background=True)
            im = Image.open(dest)
            im = im.crop(im.getbbox())
            im.save(dest, optimize=True)
            print(dest, '%d/%d' % im.size)
        await b.close()


if __name__ == '__main__':
    asyncio.run(main())
