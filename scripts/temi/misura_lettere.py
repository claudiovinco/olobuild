# -*- coding: utf-8 -*-
"""Le caselle delle lettere di un'apertura fatta di lettere separate (Abbaglio, Girandola): la larghezza vera di ogni lettera
nel carattere del tema, misurata in Chrome, in % della tela dello Slider Pro (computer 1440, telefono 640), con la parola
centrata. Stampa le righe da incollare nel generatore.
uso: python misura_lettere.py "Famiglia" peso PAROLA corpo_computer corpo_telefono [spaziatura_em]
"""
import json
import sys

from playwright.sync_api import sync_playwright

fam, peso, parola = sys.argv[1], int(sys.argv[2]), sys.argv[3]
corpo_c, corpo_t = float(sys.argv[4]), float(sys.argv[5])
spazio = float(sys.argv[6]) if len(sys.argv) > 6 else 0.0

with sync_playwright() as p:
    b = p.chromium.launch()
    pg = b.new_page()
    pg.set_content('<html><head><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=%s:wght@%d&display=block">'
                   '</head><body><span style="font-family:\'%s\';font-weight:%d">%s</span></body></html>'
                   % (fam.replace(' ', '+'), peso, fam, peso, parola))
    pg.wait_for_timeout(1500)
    pg.evaluate('document.fonts.ready')
    larghe = pg.evaluate("""([fam, peso, parola]) => { const c = document.createElement('canvas').getContext('2d');
      c.font = peso + ' 100px "' + fam + '"'; return [...parola].map(l => c.measureText(l).width / 100); }""", [fam, peso, parola])
    b.close()


def caselle(corpo, tela):
    em = corpo / tela * 100  # un em in % della tela
    w = [(l + spazio) * em for l in larghe]
    x = (100 - sum(w)) / 2
    out = []
    for k, lw in enumerate(w):
        out.append((parola[k], round(x, 2), round(lw, 2)))
        x += lw
    return out


print('em:', [round(l, 3) for l in larghe])
print('COMPUTER =', json.dumps(caselle(corpo_c, 1440), ensure_ascii=False))
print('TELEFONO =', json.dumps(caselle(corpo_t, 640), ensure_ascii=False))
