# -*- coding: utf-8 -*-
"""I disegni SVG dei cinque temi «pazzi» del 18 nov 2026. Le misure dei disegni dell'apertura coincidono col riquadro del
livello del Pro Slider che li mostra (stessa proporzione): così le tappe del movimento a scorrimento, scritte in px della
tela, cadono sopra il disegno.
uso: python cinque_disegni.py <cartella dei media>"""
import os
import sys

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'


def strada(w, h, x0, x1, ys, r=60):
    """La strada a tornanti: tratti orizzontali alle quote `ys` (dal basso), tornanti a mezzo cerchio alternati a destra e
    a sinistra. Restituisce il tracciato SVG."""
    d = 'M %d %d' % (x0, ys[0])
    destra = True
    for k in range(len(ys) - 1):
        xa = x1 - r if destra else x0 + r
        d += ' L %d %d A %d %d 0 0 %d %d %d' % (xa, ys[k], r, r, 0 if destra else 1, xa, ys[k + 1])
        destra = not destra
    d += ' L %d %d' % (x1, ys[-1])
    return d


def strada_svg(w, h, x0, x1, ys):
    d = strada(w, h, x0, x1, ys)
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d">'
            '<path d="%s" fill="none" stroke="#000" stroke-opacity=".28" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>'
            '<path d="%s" fill="none" stroke="#fff" stroke-width="5" stroke-dasharray="16 12" stroke-linecap="round"/>'
            '<circle cx="%d" cy="%d" r="11" fill="#fff"/><circle cx="%d" cy="%d" r="5" fill="#E5322D"/>'
            '<path d="M %d %d l 0 -46 l 34 12 l -34 12" fill="#E5322D" stroke="#fff" stroke-width="3" stroke-linejoin="round"/>'
            '</svg>') % (w, h, w, h, d, d, x0, ys[0], x0, ys[0], x1, ys[-1])


BICI = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64">'
        '<circle cx="32" cy="32" r="30" fill="#E5322D" stroke="#fff" stroke-width="3"/>'
        '<g fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">'
        '<circle cx="20" cy="38" r="8"/><circle cx="44" cy="38" r="8"/>'
        '<path d="M20 38 L28 24 L40 24 L44 38 M28 24 L33 38 L20 38 M33 38 L40 24 M26 20 L31 20 M38 18 L42 18 L40 24"/>'
        '</g><path d="M30 9 l-4 7 h5 l-3 6 l8 -9 h-5 l3 -4 z" fill="#FFC400"/></svg>')

import math


def ruota_grande():
    """La ruota di bici dell'apertura di Ruota Libera, 1000 × 1000: copertone col battistrada, cerchio, 32 raggi incrociati
    (a coppie dal mozzo, come una ruota vera), mozzo menta e valvola."""
    g = ['<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" width="1000" height="1000">',
         '<circle cx="500" cy="500" r="478" fill="none" stroke="#151515" stroke-width="40"/>',
         '<circle cx="500" cy="500" r="478" fill="none" stroke="#2a2a2a" stroke-width="40" stroke-dasharray="6 14"/>',
         '<circle cx="500" cy="500" r="452" fill="none" stroke="#d8d4cc" stroke-width="12"/>',
         '<circle cx="500" cy="500" r="446" fill="none" stroke="#FF6A1A" stroke-width="3"/>']
    for k in range(32):
        a = k / 32.0 * 2 * math.pi
        off = 0.22 if k % 2 else -0.22  # i raggi partono dal mozzo un po' girati: si incrociano
        hx, hy = 500 + 34 * math.cos(a + off), 500 + 34 * math.sin(a + off)
        rx, ry = 500 + 446 * math.cos(a), 500 + 446 * math.sin(a)
        g.append('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#ffffff" stroke-opacity=".9" stroke-width="3.2"/>'
                 % (hx, hy, rx, ry))
    g += ['<circle cx="500" cy="500" r="46" fill="#2CD3A8"/><circle cx="500" cy="500" r="18" fill="#0b0b0b"/>',
          '<rect x="492" y="40" width="16" height="30" rx="3" fill="#c9c4bb"/>',
          '<rect x="470" y="880" width="60" height="22" rx="5" fill="#FF6A1A" transform="rotate(8 500 891)"/>', '</svg>']
    return ''.join(g)


def pezzo(colore, disegno):
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
            '<circle cx="50" cy="50" r="50" fill="%s"/>%s</svg>') % (colore, disegno)


def ingranaggio():
    d = []
    for k in range(12):
        a = k / 12.0 * 2 * math.pi
        d.append('<rect x="45" y="12" width="10" height="14" rx="2" fill="#fff" transform="rotate(%.1f 50 50)"/>' % math.degrees(a))
    return ''.join(d) + '<circle cx="50" cy="50" r="27" fill="#fff"/><circle cx="50" cy="50" r="10" fill="#FF6A1A"/>'


PEZZI = {
    'ruota-libera-pezzo-ingranaggio.svg': pezzo('#FF6A1A', ingranaggio()),
    'ruota-libera-pezzo-dado.svg': pezzo('#2CD3A8', '<polygon points="50,18 78,34 78,66 50,82 22,66 22,34" fill="#fff"/>'
                                                     '<circle cx="50" cy="50" r="13" fill="#2CD3A8"/>'),
    'ruota-libera-pezzo-campanello.svg': pezzo('#F7E733', '<path d="M24 64 Q24 30 50 30 Q76 30 76 64 Z" fill="#141413"/>'
                                                          '<rect x="20" y="64" width="60" height="7" rx="3" fill="#141413"/>'
                                                          '<circle cx="50" cy="26" r="5" fill="#141413"/>'
                                                          '<path d="M80 40 L88 34 M82 50 L91 50" stroke="#141413" stroke-width="4" stroke-linecap="round"/>'),
    'ruota-libera-pezzo-maglia.svg': pezzo('#8C8780', '<rect x="16" y="34" width="68" height="32" rx="16" fill="#fff"/>'
                                                      '<circle cx="33" cy="50" r="7" fill="#8C8780"/><circle cx="67" cy="50" r="7" fill="#8C8780"/>'),
    'ruota-libera-pezzo-pedale.svg': pezzo('#141413', '<rect x="22" y="36" width="56" height="28" rx="6" fill="#FF6A1A"/>'
                                                      + ''.join('<rect x="%d" y="40" width="4" height="20" rx="2" fill="#141413"/>' % x
                                                                for x in (30, 40, 50, 60, 70))
                                                      + '<rect x="46" y="64" width="8" height="18" rx="3" fill="#fff"/>'),
    'ruota-libera-pezzo-valvola.svg': pezzo('#2CD3A8', '<rect x="42" y="18" width="16" height="22" rx="3" fill="#141413"/>'
                                                       '<rect x="45" y="40" width="10" height="34" fill="#fff"/>'
                                                       '<rect x="38" y="72" width="24" height="8" rx="3" fill="#fff"/>'),
}

DISEGNI = {
    # computer: riquadro 46 × 84 % della tela 1440 × 810 = 662 × 680
    'tornanti-strada.svg': strada_svg(662, 680, 60, 600, [640, 520, 400, 280, 160]),
    # telefono: riquadro 88 × 48 % della tela 640 × 1400 = 563 × 672
    'tornanti-strada-tel.svg': strada_svg(563, 672, 50, 510, [630, 510, 390, 270, 150]),
    'tornanti-bici.svg': BICI,
    'ruota-libera-ruota.svg': ruota_grande(),
}
DISEGNI.update(PEZZI)


if __name__ == '__main__':
    for nome, svg in DISEGNI.items():
        open(os.path.join(OUT, nome), 'w', encoding='utf-8').write(svg)
        print(nome)
