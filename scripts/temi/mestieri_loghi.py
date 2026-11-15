# -*- coding: utf-8 -*-
"""I loghi dei dieci temi dei mestieri (10 nov 2026): il segno del mestiere e il nome nel carattere dei titoli del tema,
resi in PNG trasparente con Chrome (Playwright) — <id>-logo-chiaro.png (scritte chiare, per i fondi scuri) e
<id>-logo-scuro.png. Stampa le proporzioni (larghezza/altezza) che i generatori usano.

Uso: python mestieri_loghi.py <cartella dei media>
"""
import asyncio
import os
import sys

from PIL import Image
from playwright.async_api import async_playwright

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'

# id: (nome, sotto, famiglia, peso, maiuscolo, spaziatura, colore del segno, segno SVG 0 0 100 100 con «C» = colore del testo)
LOGHI = {
    'morsetto': ('Morsetto', 'impianti elettrici · Brescia', 'Red Hat Display', 800, True, '0.08em', '#FFD23F',
                 '<circle cx="50" cy="50" r="42" fill="none" stroke="S" stroke-width="9"/><path d="M56 14 L28 56 H48 L42 86 L72 42 H52 Z" fill="S"/>'),
    'sifone': ('Sifone', 'idraulica e caldaie · Verona', 'Bricolage Grotesque', 700, False, '-0.01em', '#B5562A',
               '<path d="M18 10 V52 a32 32 0 0 0 64 0 V36" fill="none" stroke="S" stroke-width="13" stroke-linecap="round"/><path d="M82 30 V12" stroke="C" stroke-width="13" stroke-linecap="round"/><path d="M50 72 q6 10 0 16 q-6 -6 0 -16z" fill="S"/>'),
    'due-mani': ('Due Mani', 'imbianchini · Bologna', 'Fraunces', 600, False, '-0.01em', '#A84A28',
                 '<rect x="8" y="16" width="56" height="44" rx="8" fill="S" transform="rotate(-8 36 38)"/><rect x="36" y="42" width="56" height="44" rx="8" fill="#5E7A62" opacity=".92" transform="rotate(6 64 64)"/>'),
    'filo-a-piombo': ('Filo a Piombo', 'costruzioni e restauri · Bergamo', 'Epilogue', 700, False, '-0.02em', '#A9432B',
                      '<line x1="50" y1="2" x2="50" y2="44" stroke="C" stroke-width="5"/><path d="M50 40 L72 60 L50 98 L28 60 Z" fill="S"/><rect x="42" y="36" width="16" height="10" rx="2" fill="C"/>'),
    'truciolo': ('Truciolo', 'falegnameria su misura · Udine', 'Spectral', 600, False, '0', '#E0A458',
                 '<path d="M10 70 C40 70 86 66 86 38 C86 16 58 10 46 26 C36 40 52 56 64 44 C72 36 64 26 58 30" fill="none" stroke="S" stroke-width="9" stroke-linecap="round"/>'),
    'incudine': ('Incudine', 'fabbro · Bienno', 'Zilla Slab', 700, True, '0.06em', '#FF7A1A',
                 '<path d="M4 22 H74 C86 22 96 30 96 36 H62 C56 44 56 52 64 58 V66 H70 V78 H18 V66 H26 V58 C34 52 34 44 28 36 H14 Z" fill="S"/>'),
    'cric': ('Cric', 'officina meccanica · Torino', 'Space Grotesk', 700, True, '0.12em', '#FFB020',
             '<path d="M50 12 L86 50 L50 88 L14 50 Z" fill="none" stroke="S" stroke-width="8" stroke-linejoin="round"/><line x1="6" y1="50" x2="94" y2="50" stroke="C" stroke-width="7" stroke-linecap="round"/><circle cx="50" cy="12" r="7" fill="S"/><circle cx="50" cy="88" r="7" fill="S"/>'),
    'fuliggine': ('Fuliggine', 'spazzacamini · Val Vigezzo', 'Crimson Pro', 700, False, '0', '#1F1C1A',
                  '<rect x="26" y="8" width="40" height="40" rx="3" fill="S"/><rect x="12" y="44" width="68" height="9" rx="4" fill="S"/><circle cx="74" cy="76" r="16" fill="none" stroke="#C4471F" stroke-width="5" stroke-dasharray="3 3"/><line x1="46" y1="54" x2="66" y2="66" stroke="#C4471F" stroke-width="5"/>'),
    'rastrello': ('Rastrello', 'giardinieri · Lucca', 'Cormorant', 700, False, '0.01em', '#2F5D3A',
                  '<line x1="50" y1="96" x2="50" y2="40" stroke="S" stroke-width="8" stroke-linecap="round"/>'
                  + ''.join('<line x1="50" y1="40" x2="%d" y2="%d" stroke="S" stroke-width="6" stroke-linecap="round"/>' % (x, y) for x, y in [(10, 16), (30, 8), (50, 4), (70, 8), (90, 16)])),
    'scheggia': ('Scheggia', 'vetreria e serramenti · Treviso', 'Sora', 700, False, '-0.02em', '#0F6E74',
                 '<path d="M12 88 L44 8 L58 54 Z" fill="S"/><path d="M58 54 L44 8 L90 70 Z" fill="S" opacity=".55"/><path d="M12 88 L58 54 L90 70 Z" fill="S" opacity=".8"/>'),
}


def html(nome, sotto, fam, peso, maiusc, sp, segno_col, segno, testo_col):
    s = segno.replace('"S"', '"%s"' % segno_col).replace('"C"', '"%s"' % testo_col)
    return ('<html><head><link href="https://fonts.googleapis.com/css2?family=%s:wght@400;%d&display=block" rel="stylesheet"></head>'
            '<body style="margin:0;background:transparent"><div id="l" style="display:inline-flex;align-items:center;gap:22px;padding:6px">'
            '<svg viewBox="0 0 100 100" width="118" height="118">%s</svg>'
            '<div><div style="font-family:\'%s\';font-weight:%d;font-size:96px;line-height:.95;letter-spacing:%s;color:%s;%s">%s</div>'
            '<div style="font-family:\'%s\';font-weight:400;font-size:25px;letter-spacing:.14em;text-transform:uppercase;color:%s;opacity:.8;margin-top:10px">%s</div></div>'
            '</div></body></html>') % (fam.replace(' ', '+'), peso, s, fam, peso, sp, testo_col,
                                      'text-transform:uppercase;' if maiusc else '', nome, fam, testo_col, sotto)


async def main():
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page(viewport={'width': 1800, 'height': 400}, device_scale_factor=1)
        for tid, (nome, sotto, fam, peso, maiusc, sp, col, segno) in LOGHI.items():
            for variante, testo in (('chiaro', '#FFFFFF' if tid != 'fuliggine' else '#F6F2EA'), ('scuro', '#14171A')):
                # sui fondi scuri i segni scuri (verde bosso, petrolio, nero fuliggine) si schiariscono
                segno_col = {'fuliggine': '#F6F2EA', 'rastrello': '#A9C98F', 'scheggia': '#6CC3C8'}.get(tid, col) if variante == 'chiaro' else col
                await pg.set_content(html(nome, sotto, fam, peso, maiusc, sp, segno_col, segno, testo))
                await pg.wait_for_timeout(900)
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
