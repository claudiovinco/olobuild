# -*- coding: utf-8 -*-
"""Il campionario di Torchio (20 nov 2026): otto pagine A5 in PDF, disegnate con Chrome nei caratteri del tema (IM Fell
English e Literata), da sfogliare nel sito con la tile PDF Pro (torchio-campionario.pdf, nella libreria dei media).

Uso: python agenzie_pdf.py <cartella dei media>
"""
import asyncio
import base64
import io
import os
import sys

from PIL import Image
from playwright.async_api import async_playwright

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'


def foto64(nome, lato=1200):
    im = Image.open(os.path.join(OUT, nome)).convert('RGB')
    im.thumbnail((lato, lato))
    b = io.BytesIO()
    im.save(b, 'JPEG', quality=84)
    return 'data:image/jpeg;base64,' + base64.b64encode(b.getvalue()).decode()


ROSSO, NERO, CARTA, BLU = '#B3261E', '#1C1A17', '#F5F0E6', '#1F3A5F'
CARTE = [('Cotone Rag', '600 g · bianco naturale', '#F7F3EA'), ('Gesso', '400 g · avorio', '#EFE6D2'),
         ('Kraft', '350 g · avana', '#C9A97A'), ('Notte', '450 g · blu di Prussia', '#1F3A5F'),
         ('Ardesia', '400 g · grigio', '#6B6A66'), ('Riso', '90 g · velina', '#FBF8F1')]


def pagine(img):
    p = []
    p.append('<div class="pg" style="background:%s;color:%s;justify-content:space-between">'
             '<div class="sm" style="color:%s">Stamperia e design editoriale · Verona</div>'
             '<div><div class="big" style="font-size:96px">Torchio</div>'
             '<div class="big it" style="font-size:40px;color:%s;margin-top:6px">campionario 2027</div></div>'
             '<div class="sm">Vicolo San Marco 4 · dal 1961</div></div>' % (CARTA, NERO, ROSSO, ROSSO))
    p.append('<div class="pg"><div class="sm" style="color:%s">Le carte</div><div class="big" style="font-size:44px;margin:8px 0 22px">'
             'Sei carte, <span class="it" style="color:%s">sei mani diverse.</span></div><div class="griglia">%s</div></div>'
             % (ROSSO, ROSSO, ''.join('<div><div class="cam" style="background:%s"></div><b>%s</b><br><span>%s</span></div>'
                                      % (c, n, d) for n, d, c in CARTE)))
    p.append('<div class="pg"><div class="sm" style="color:%s">I caratteri</div>'
             '<div class="big" style="font-size:150px;line-height:1">Aa <span class="it" style="color:%s">Rr</span></div>'
             '<div class="big" style="font-size:22px;letter-spacing:.04em;margin-top:18px">ABCDEFGHIJKLMNOPQRSTUVWXYZ<br>'
             'abcdefghijklmnopqrstuvwxyz<br>0123456789 &amp; ? ! « »</div>'
             '<p style="margin-top:22px">Fell, Garamond, Bodoni e un bastoncino del 1930: in cassetta ne abbiamo quaranta, in '
             'legno e in piombo, dal corpo 6 al corpo 120.</p></div>' % (ROSSO, ROSSO))
    p.append('<div class="pg" style="padding:0"><img src="%s" style="width:100%%;height:82%%;object-fit:cover">'
             '<p style="padding:16px 34px 0;font-size:12px">La cassettiera dei caratteri di legno, in fondo al laboratorio. Ogni '
             'cassetto è un corpo, ogni scomparto una lettera.</p></div>' % img['cassetti'])
    p.append('<div class="pg"><div class="sm" style="color:%s">Biglietti da visita</div><div class="big" style="font-size:44px;'
             'margin:8px 0 24px">Si sentono <span class="it" style="color:%s">al tatto.</span></div>'
             '<div class="bv"><div class="big" style="font-size:24px">Elena Bassi</div><div class="sm" style="font-size:8px;'
             'color:%s;margin-top:4px">Restauro del legno</div><div style="font-size:9px;margin-top:26px">Via Mazzini 12 · Verona'
             '</div></div><div class="bv" style="background:%s;color:%s;margin-top:16px"><div class="big it" style="font-size:24px">'
             'Osteria del Ponte</div><div style="font-size:9px;margin-top:30px">Dal martedì alla domenica</div></div>'
             '<p style="margin-top:22px">Due colori su cotone da 600 g, con la stampa che affonda nella carta. Cento biglietti da '
             '120 €.</p></div>' % (ROSSO, ROSSO, ROSSO, BLU, CARTA))
    p.append('<div class="pg"><div class="sm" style="color:%s">Inviti e partecipazioni</div>'
             '<div class="inv"><div class="sm" style="color:%s;font-size:8px">Sabato 12 giugno 2027</div>'
             '<div class="big it" style="font-size:34px;margin:14px 0">Giulia &amp; Pietro</div>'
             '<div style="font-size:11px">si sposano nella chiesa di San Zeno<br>alle undici del mattino</div></div>'
             '<p style="margin-top:22px">Ogni invito lo componiamo a mano, lettera per lettera. Busta foderata compresa.</p></div>'
             % (ROSSO, ROSSO))
    p.append('<div class="pg"><div class="sm" style="color:%s">I prezzi</div><div class="big" style="font-size:44px;margin:8px 0 20px">'
             'Per cominciare.</div><table>%s</table><p style="margin-top:18px;font-size:11px">IVA inclusa. Le prove di stampa sono '
             'gratis: le vedi e le tocchi prima di dire sì.</p></div>' % (ROSSO, ''.join(
                 '<tr><td>%s</td><td>%s</td></tr>' % r for r in (('100 biglietti da visita, 2 colori', '120 €'),
                                                                ('50 inviti con busta foderata', '290 €'),
                                                                ('200 cartoline, 1 colore', '160 €'),
                                                                ('Carta da lettere, 100 fogli', '180 €'),
                                                                ('Libro d’artista, 30 copie', 'da 1.400 €')))))
    p.append('<div class="pg" style="background:%s;color:%s;justify-content:flex-end"><div class="big" style="font-size:56px">'
             'Passa a<br><span class="it">toccare la carta.</span></div><div class="sm" style="margin-top:26px;color:%s">'
             'Vicolo San Marco 4 · Verona · 045 000 1961</div></div>' % (NERO, CARTA, '#F2A39C'))
    return p


def html(pp):
    return ('<html><head><link href="https://fonts.googleapis.com/css2?family=IM+Fell+English:ital@0;1&family=Literata:wght@400;600'
            '&display=block" rel="stylesheet"><style>@page{size:148mm 210mm;margin:0}*{margin:0;box-sizing:border-box}'
            'body{font-family:Literata;color:%s;font-size:13px;line-height:1.55}'
            '.pg{width:148mm;height:210mm;padding:34px;display:flex;flex-direction:column;background:%s;page-break-after:always;'
            'overflow:hidden}.big{font-family:"IM Fell English";line-height:1.02}.it{font-style:italic}'
            '.sm{font-family:Literata;font-weight:600;font-size:9px;letter-spacing:.18em;text-transform:uppercase}'
            '.griglia{display:grid;grid-template-columns:1fr 1fr;gap:16px 18px;font-size:11px}'
            '.cam{height:70px;border:1px solid rgba(0,0,0,.12);margin-bottom:6px}'
            '.bv{width:85mm;height:55mm;background:#F7F3EA;border:1px solid rgba(0,0,0,.12);padding:18px;box-shadow:0 8px 20px rgba(0,0,0,.12)}'
            '.inv{border:1px solid %s;padding:28px;text-align:center;margin-top:18px;background:#FBF8F1}'
            'table{border-collapse:collapse;width:100%%}td{border-bottom:1px solid rgba(0,0,0,.15);padding:9px 0}'
            'td:last-child{text-align:right;font-weight:600}</style></head><body>%s</body></html>') % (
        NERO, CARTA, ROSSO, ''.join(pp))


async def main():
    img = {'cassetti': foto64('pexels-4140925.jpg')}
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page()
        await pg.set_content(html(pagine(img)))
        await pg.wait_for_timeout(2500)
        dest = os.path.join(OUT, 'torchio-campionario.pdf')
        await pg.pdf(path=dest, width='148mm', height='210mm', print_background=True)
        print(dest, os.path.getsize(dest) // 1024, 'kB')
        await b.close()


if __name__ == '__main__':
    asyncio.run(main())
