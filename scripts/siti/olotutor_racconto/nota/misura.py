# -*- coding: utf-8 -*-
"""Altezza di ogni blocco della nota (mm), per decidere dove andare a pagina nuova."""
import os, sys
from playwright.sync_api import sync_playwright
sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
with sync_playwright() as p:
    b = p.chromium.launch(channel='chrome')
    pg = b.new_page()
    pg.goto('file:///' + os.path.join(QUI, 'nota-novembre-2026.html').replace(os.sep, '/'), wait_until='networkidle')
    pg.wait_for_function('window.__impaginato > 0', timeout=30000)
    r = pg.evaluate("""() => {
      const out = [];
      document.querySelectorAll('.blocco').forEach((b, i) => {
        const st = getComputedStyle(b);
        const t = b.querySelector('h2, h3, p, td');
        out.push([i, Math.round((b.getBoundingClientRect().height + parseFloat(st.marginTop)) / 3.7795 * 10) / 10,
                  b.closest('.pagina') ? [...document.querySelectorAll('.pagina')].indexOf(b.closest('.pagina')) + 1 : 0,
                  (t ? t.innerText : '').slice(0, 50)]);
      });
      const p1 = document.querySelector('.pagina.prima .seguito').getBoundingClientRect();
      const c1 = document.querySelector('.pagina.prima .corpo').getBoundingClientRect();
      return {blocchi: out, libero_p1: Math.round((c1.bottom - p1.top) / 3.7795), altre: Math.round(document.querySelectorAll('.corpo')[1].getBoundingClientRect().height / 3.7795)};
    }""")
    for x in r['blocchi']:
        print(*x)
    print('spazio p1:', r['libero_p1'], 'mm · altre pagine:', r['altre'], 'mm')
    b.close()
