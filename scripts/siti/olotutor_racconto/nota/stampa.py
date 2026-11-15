# -*- coding: utf-8 -*-
"""Stampa la nota di sintesi in PDF con Chrome e controlla che nessuna pagina trabocchi.
uso: python stampa.py [nota.html] [uscita.pdf]   (di serie: nota-novembre-2026.html → nota-di-sintesi.pdf)"""
import os
import sys
from playwright.sync_api import sync_playwright

QUI = os.path.dirname(os.path.abspath(__file__))
html = os.path.join(QUI, sys.argv[1] if len(sys.argv) > 1 else 'nota-novembre-2026.html')
pdf = os.path.join(QUI, sys.argv[2] if len(sys.argv) > 2 else 'nota-di-sintesi.pdf')
with sync_playwright() as p:
    b = p.chromium.launch(channel='chrome')
    pg = b.new_page()
    pg.goto('file:///' + html.replace('\\', '/'), wait_until='networkidle')
    pg.wait_for_function('window.__impaginato > 0', timeout=30000)
    pg.emulate_media(media='print')
    pieni = pg.evaluate("""() => [...document.querySelectorAll('.pagina')].map((s, i) => {
        const c = s.querySelector('.corpo');
        const el = [...c.querySelectorAll('p, table, ul, h2, h3, .chiusa')];
        const fine = Math.max(...el.map(e => e.getBoundingClientRect().bottom));
        return {pagina: i + 1, libero_mm: Math.round((c.getBoundingClientRect().bottom - fine) / 3.7795 * 10) / 10};
    })""")
    for x in pieni:
        print('pagina %(pagina)d: %(libero_mm)s mm liberi' % x, '  ⚠️ TRABOCCA' if x['libero_mm'] < 0 else '')
    pg.pdf(path=pdf, format='A4', print_background=True, margin={'top': '0', 'right': '0', 'bottom': '0', 'left': '0'},
           prefer_css_page_size=True)
    b.close()
print('scritto', pdf)
