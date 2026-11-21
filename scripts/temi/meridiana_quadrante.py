# -*- coding: utf-8 -*-
"""Il quadrante di Meridiana (21 nov 2026, utente: «non capisco cosa fa questa grafica statica nel sito meridiana»).

Prima: un cerchio fatto con un livello forma (al tablet diventava una capsula), una lancetta che attraversava tutto il
cerchio e girava una volta al minuto (sembrava ferma) e non indicava l'ora scritta sopra.
Ora: una meridiana orizzontale vista dall'alto, con lo gnomone al CENTRO del disegno, così il perno resta al centro del
livello a ogni proporzione dello schermo (le immagini stanno in «contain»). Le ore da V a VIII di sera, a 15° l'una
dall'altra come al sole (XII in alto, il mattino a sinistra). L'ombra è un secondo disegno con lo stesso riquadro: nel
tema ogni diapositiva la fa scorrere fino alla sua ora (IX, XII, IV, VII).
Uso: python meridiana_quadrante.py <cartella d'uscita>   → meridiana-quadrante.svg, meridiana-ombra.svg
"""
import math
import os
import sys

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'
OTTONE = '#E9D8A6'
ROMANI = {5: 'V', 6: 'VI', 7: 'VII', 8: 'VIII', 9: 'IX', 10: 'X', 11: 'XI', 12: 'XII', 13: 'I', 14: 'II', 15: 'III', 16: 'IV',
          17: 'V', 18: 'VI', 19: 'VII', 20: 'VIII'}
C = 200


def punto(r, ang):
    a = math.radians(ang)
    return C + r * math.sin(a), C - r * math.cos(a)


def quadrante():
    p = ['<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400">',
         '<g fill="none" stroke="%s">' % OTTONE,
         '<circle cx="200" cy="200" r="190" stroke-opacity=".5" stroke-width="1.2"/>',
         '<circle cx="200" cy="200" r="148" stroke-opacity=".22" stroke-width="1"/>',
         '<circle cx="200" cy="200" r="28" stroke-opacity=".35" stroke-width="1"/>']
    for h in range(5, 21):
        ang = (h - 12) * 15
        x1, y1 = punto(34, ang)
        x2, y2 = punto(148, ang)
        p.append('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke-opacity="%s" stroke-width="%s"/>'
                 % (x1, y1, x2, y2, '.4' if h == 12 else '.2', '1.2' if h == 12 else '.9'))
        if h < 20:
            for q in (1, 2, 3):           # i quarti
                xa, ya = punto(141, ang + q * 3.75)
                xb, yb = punto(148, ang + q * 3.75)
                p.append('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke-opacity=".3" stroke-width=".8"/>' % (xa, ya, xb, yb))
    p.append('</g>')
    p.append('<g fill="%s" font-family="Georgia, \'Times New Roman\', serif" text-anchor="middle" dominant-baseline="central">'
             % OTTONE)
    for h, nome in ROMANI.items():
        x, y = punto(169, (h - 12) * 15)
        p.append('<text x="%.1f" y="%.1f" font-size="%d" fill-opacity="%s" letter-spacing="1">%s</text>'
                 % (x, y, 17 if h == 12 else 14, '.95' if h == 12 else '.75', nome))
    p.append('</g>')
    # lo gnomone visto dall'alto: un perno d'ottone al centro
    p.append('<circle cx="200" cy="200" r="7" fill="%s"/><circle cx="200" cy="200" r="12" fill="none" stroke="%s" '
             'stroke-opacity=".6"/>' % (OTTONE, OTTONE))
    p.append('</svg>')
    return ''.join(p)


def ombra():
    """L'ombra dello gnomone, verso XII (in alto): nel tema il livello gira attorno al centro fino all'ora."""
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400">'
            '<defs><linearGradient id="g" x1="0" y1="1" x2="0" y2="0"><stop offset="0" stop-color="%s" stop-opacity=".95"/>'
            '<stop offset="1" stop-color="%s" stop-opacity=".35"/></linearGradient></defs>'
            '<polygon points="194,204 206,204 201.2,58 198.8,58" fill="url(#g)"/>'
            '<polygon points="190,204 210,204 202,62 198,62" fill="%s" fill-opacity=".08"/>'
            '</svg>') % (OTTONE, OTTONE, OTTONE)


if __name__ == '__main__':
    os.makedirs(OUT, exist_ok=True)
    for nome, svg in (('meridiana-quadrante.svg', quadrante()), ('meridiana-ombra.svg', ombra())):
        with open(os.path.join(OUT, nome), 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(svg)
        print(nome, len(svg))
