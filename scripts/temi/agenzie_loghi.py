# -*- coding: utf-8 -*-
"""I loghi dei cinque temi delle agenzie di comunicazione (20 nov 2026): Affisso, Diaframma, Pollice, Strappo, Torchio.
Come cinque2_loghi.py: il segno e il nome nel carattere dei titoli, in PNG trasparente con Chrome —
<id>-logo-chiaro.png (scritte chiare, per i fondi scuri) e <id>-logo-scuro.png. Stampa le proporzioni (larghezza/altezza)
che i generatori usano nel piede.

Uso: python agenzie_loghi.py <cartella dei media>
"""
import asyncio
import os
import sys

from PIL import Image
from playwright.async_api import async_playwright

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'


def html(nome, sotto, fam, peso, maiusc, sp, segno_col, segno, testo_col, corpo):
    s = segno.replace('"S"', '"%s"' % segno_col).replace('"C"', '"%s"' % testo_col)
    return ('<html><head><link href="https://fonts.googleapis.com/css2?family=%s:wght@%d&display=block" rel="stylesheet">'
            '<link href="https://fonts.googleapis.com/css2?family=%s:wght@500&display=block" rel="stylesheet"></head>'
            '<body style="margin:0;background:transparent"><div id="l" style="display:inline-flex;align-items:center;gap:22px;padding:6px">'
            '<svg viewBox="0 0 100 100" width="118" height="118">%s</svg>'
            '<div><div style="font-family:\'%s\';font-weight:%d;font-size:92px;line-height:.95;letter-spacing:%s;color:%s;%s">%s</div>'
            '<div style="font-family:\'%s\';font-weight:500;font-size:24px;letter-spacing:.14em;text-transform:uppercase;color:%s;opacity:.82;margin-top:12px">%s</div></div>'
            '</div></body></html>') % (fam.replace(' ', '+'), peso, corpo.replace(' ', '+'), s, fam, peso, sp, testo_col,
                                      'text-transform:uppercase;' if maiusc else '', nome, corpo, testo_col, sotto)


def _lamelle():
    """Sette lamelle attorno al foro a sette lati: ogni lamella va da un lato del foro al bordo, girata di 50 gradi."""
    import math
    foro = [(50 + 15 * math.cos(math.radians(-90 + k * 360 / 7)), 50 + 15 * math.sin(math.radians(-90 + k * 360 / 7)))
            for k in range(7)]
    out = []
    for k in range(7):
        a, b = foro[k], foro[(k + 1) % 7]
        ang = math.radians(-90 + k * 360 / 7 + 50)
        e1 = (50 + 41 * math.cos(ang), 50 + 41 * math.sin(ang))
        ang2 = ang + math.radians(360 / 7)
        e2 = (50 + 41 * math.cos(ang2), 50 + 41 * math.sin(ang2))
        out.append('<path d="M%.1f %.1f L%.1f %.1f L%.1f %.1f A41 41 0 0 0 %.1f %.1f Z" fill="S" stroke="C" stroke-width="2.4" '
                   'stroke-linejoin="round"/>' % (a[0], a[1], e1[0], e1[1], e2[0], e2[1], b[0], b[1]))
    return ''.join(out)


LAMELLE = _lamelle()

# id: (nome, sotto, famiglia, peso, maiuscolo, spaziatura, colore del segno sul chiaro, sul scuro, segno SVG 0 0 100 100
# con «S» = segno, «C» = testo, carattere del sottotitolo)
LOGHI = {
    # il cartellone a tre prismi (trivision) con le due gambe
    'affisso': ('Affisso', 'affissioni · Napoli', 'Archivo Black', 400, False, '-0.02em', '#FFD21F', '#111111',
                '<rect x="6" y="10" width="88" height="56" rx="3" fill="none" stroke="C" stroke-width="6"/>'
                + ''.join('<path d="M%d 16 h%d l-5 44 h-%d Z" fill="S"/>' % (13 + k * 25, 22, 22) for k in range(3))
                + '<rect x="26" y="66" width="7" height="28" fill="C"/><rect x="67" y="66" width="7" height="28" fill="C"/>',
                'Schibsted Grotesk'),
    # sette lamelle attorno al foro
    'diaframma': ('Diaframma', 'produzioni video · Roma', 'Gloock', 400, False, '0', '#FF9F2E', '#B8590A',
                  '<circle cx="50" cy="50" r="45" fill="C"/>' + LAMELLE, 'Inter Tight'),
    # il pollice in su dentro la nuvoletta
    'pollice': ('Pollice', 'agenzia social · Bologna', 'Gabarito', 800, False, '-0.03em', '#FF5436', '#3A26E0',
                '<path d="M14 10 H86 a8 8 0 0 1 8 8 V66 a8 8 0 0 1 -8 8 H44 L24 92 V74 H14 a8 8 0 0 1 -8 -8 V18 a8 8 0 0 1 8 -8 Z" fill="S"/>'
                '<path d="M30 62 V40 H38 L48 22 c6 0 8 4 7 9 l-2 7 H70 c5 0 7 4 6 8 l-4 13 c-1 3 -3 5 -7 5 Z" fill="C"/>'
                '<rect x="22" y="40" width="6" height="22" rx="2" fill="C"/>',
                'DM Sans'),
    # il foglio strappato in due
    'strappo': ('Strappo', 'art direction · Milano', 'Libre Caslon Display', 400, False, '0', '#C8145F', '#C8145F',
                '<path d="M10 8 H52 L46 22 L54 34 L44 48 L53 60 L45 74 L51 92 H10 Z" fill="C"/>'
                '<path d="M60 12 H90 V96 H58 L64 80 L56 66 L66 52 L57 40 L66 26 Z" fill="S" transform="rotate(6 74 54)"/>',
                'Onest'),
    # la lettera di piombo con l'occhio inciso
    'torchio': ('Torchio', 'stampa e design editoriale · Verona', 'IM Fell English', 400, False, '0', '#E26B5E', '#B3261E',
                '<rect x="12" y="8" width="76" height="84" rx="4" fill="C"/>'
                '<rect x="12" y="74" width="76" height="6" fill="S"/>'
                '<path d="M34 64 L50 22 L66 64 M40 50 H60" fill="none" stroke="S" stroke-width="7" stroke-linejoin="round"/>',
                'Literata'),
}


async def main():
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page(viewport={'width': 2200, 'height': 400}, device_scale_factor=1)
        for tid, (nome, sotto, fam, peso, maiusc, sp, col_chiaro, col_scuro, segno, corpo) in LOGHI.items():
            for variante, testo in (('chiaro', '#FFFFFF'), ('scuro', '#14121A')):
                segno_col = col_chiaro if variante == 'chiaro' else col_scuro
                await pg.set_content(html(nome, sotto, fam, peso, maiusc, sp, segno_col, segno, testo, corpo))
                await pg.wait_for_timeout(1600)
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
