# -*- coding: utf-8 -*-
"""I disegni delle aperture dei tre temi «pazzi» (18 nov 2026): il cerchio aereo di Capriola (acciaio col nastro dove si
tiene), gli scoppi di Girandola (oro, rosa, azzurro: raggi di faville con le perle in punta). SVG nella libreria come le foto.

Uso: python tre_disegni.py <cartella dei media>
"""
import math
import os
import random
import sys

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'
os.makedirs(OUT, exist_ok=True)


def salva(nome, svg):
    with open(os.path.join(OUT, nome), 'w', encoding='utf-8', newline='\n') as f:
        f.write(svg.strip() + '\n')


def svg(vb, corpo, stile=''):
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="%s">' % vb + ('<style>%s</style>' % stile if stile else '') + corpo + '</svg>')


def cerchio():
    # l'anello d'acciaio: sfumatura metallica, il bordo chiaro, il nastro bianco nei due punti di presa
    defs = ('<defs><linearGradient id="a" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F4F6F8"/>'
            '<stop offset=".35" stop-color="#9AA3AC"/><stop offset=".55" stop-color="#5D6670"/><stop offset=".8" stop-color="#C9D0D6"/>'
            '<stop offset="1" stop-color="#6E7780"/></linearGradient></defs>')
    corpo = defs + '<circle cx="200" cy="200" r="184" fill="none" stroke="url(#a)" stroke-width="15"/>'
    corpo += '<circle cx="200" cy="200" r="191" fill="none" stroke="#ffffff" stroke-opacity=".35" stroke-width="1.5"/>'
    # il nastro: tratti obliqui bianchi sull'anello, in alto (dove si appende) e in basso
    for centro in (-90, 90):
        for k in range(-6, 7):
            a = math.radians(centro + k * 3.2)
            x1, y1 = 200 + math.cos(a) * 176, 200 + math.sin(a) * 176
            x2, y2 = 200 + math.cos(a + 0.03) * 192, 200 + math.sin(a + 0.03) * 192
            corpo += '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#FBF7F0" stroke-width="5" stroke-linecap="round"/>' % (x1, y1, x2, y2)
    # il moschettone dove la corda prende l'anello
    corpo += '<rect x="190" y="2" width="20" height="18" rx="8" fill="none" stroke="#C9D0D6" stroke-width="5"/>'
    salva('capriola-cerchio.svg', svg('0 0 400 400', corpo))


def scoppio(nome, colore, perla, seme):
    random.seed(seme)
    corpo = ('<defs><radialGradient id="c"><stop offset="0" stop-color="#FFFBEA" stop-opacity=".95"/>'
             '<stop offset=".25" stop-color="%s" stop-opacity=".5"/><stop offset="1" stop-color="%s" stop-opacity="0"/></radialGradient>'
             '<linearGradient id="r" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="%s" stop-opacity="0"/>'
             '<stop offset="1" stop-color="%s"/></linearGradient></defs>') % (colore, colore, colore, perla)
    corpo += '<circle cx="200" cy="200" r="120" fill="url(#c)"/>'
    n = 34
    for k in range(n):
        a = k / n * 2 * math.pi + random.uniform(-0.05, 0.05)
        r0, r1 = random.uniform(18, 40), random.uniform(130, 190)
        x0, y0 = 200 + math.cos(a) * r0, 200 + math.sin(a) * r0
        x1, y1 = 200 + math.cos(a) * r1, 200 + math.sin(a) * r1 + (r1 / 190) ** 2 * 14  # le faville cadono un poco
        corpo += ('<path d="M%.1f %.1f Q%.1f %.1f %.1f %.1f" stroke="%s" stroke-width="%.1f" fill="none" stroke-linecap="round" '
                  'opacity="%.2f"/>') % (x0, y0, (x0 + x1) / 2, (y0 + y1) / 2 - 4, x1, y1, colore, random.uniform(2.2, 4), random.uniform(.6, 1))
        corpo += '<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s"/>' % (x1, y1, random.uniform(3, 5.5), perla)
    for k in range(40):
        a, r = random.uniform(0, 2 * math.pi), random.uniform(40, 175)
        corpo += '<circle cx="%.1f" cy="%.1f" r="%.1f" fill="%s" opacity="%.2f"/>' % (
            200 + math.cos(a) * r, 200 + math.sin(a) * r, random.uniform(1, 2.4), perla, random.uniform(.4, .9))
    salva(nome, svg('0 0 400 400', corpo))


def righe(nome, colore, passo, spessore):
    # una griglia di righe verticali su fondo trasparente, larga abbastanza da coprire la tela anche ruotata
    corpo = ''.join('<rect x="%.1f" y="0" width="%.1f" height="1200" fill="%s"/>' % (x, spessore, colore)
                    for x in [k * passo for k in range(int(2000 / passo) + 1)])
    salva(nome, svg('0 0 2000 1200', corpo))


if __name__ == '__main__':
    righe('abbaglio-righe-a.svg', '#0B0B0D', 14, 6.5)
    righe('abbaglio-righe-b.svg', '#0B0B0D', 14.6, 6.5)
    cerchio()
    scoppio('girandola-scoppio-oro.svg', '#FFC94A', '#FFF3C4', 3)
    scoppio('girandola-scoppio-rosa.svg', '#FF3D7F', '#FFD0E1', 5)
    scoppio('girandola-scoppio-azzurro.svg', '#3DDCFF', '#D7F8FF', 7)
    print('disegni fatti in', OUT)
