# -*- coding: utf-8 -*-
"""I disegni dei cinque temi «pazzi» della sera del 18 nov 2026: la copertina di cuoio di Dorso (computer e telefono), il
fascio del faro di Lanterna, il televisore di Zapping (lo schermo è un buco: sotto si vede il livello video), il vano
dell'ascensore e la cabina di Torre, la stampante e il rotolo di Scontrino. SVG da caricare nella libreria dei media.

Uso: python cinque2_disegni.py <cartella dei media>
"""
import os
import sys

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'


def copertina(w, h):
    """Cuoio bordeaux con la grana, la doppia cornice d'oro e i fregi agli angoli; il titolo lo scrivono i livelli."""
    m = int(min(w, h) * 0.055)
    fregio = lambda x, y, sx, sy: ('<g transform="translate(%d %d) scale(%d %d)" fill="none" stroke="#D9B45A" stroke-width="2.4">'
                                   '<path d="M0 0 C 30 0 46 10 52 34 M0 0 C 0 30 10 46 34 52"/><circle cx="18" cy="18" r="6"/>'
                                   '<path d="M38 14 l8 -8 M14 38 l-8 8"/></g>') % (x, y, sx, sy)
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" preserveAspectRatio="none">'
            '<defs><radialGradient id="c" cx="45%" cy="40%" r="75%"><stop offset="0" stop-color="#8E2232"/>'
            '<stop offset=".6" stop-color="#6E1724"/><stop offset="1" stop-color="#3F0B14"/></radialGradient>'
            '<filter id="g"><feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" seed="7"/>'
            '<feColorMatrix values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 .22 0"/></filter>'
            '<linearGradient id="d" x1="0" x2="1"><stop offset="0" stop-color="#000" stop-opacity=".55"/>'
            '<stop offset=".035" stop-color="#000" stop-opacity=".12"/><stop offset=".06" stop-color="#fff" stop-opacity=".08"/>'
            '<stop offset=".09" stop-color="#000" stop-opacity="0"/></linearGradient></defs>'
            '<rect width="{w}" height="{h}" fill="url(#c)"/><rect width="{w}" height="{h}" filter="url(#g)"/>'
            '<rect width="{w}" height="{h}" fill="url(#d)"/>'
            '<rect x="{m}" y="{m}" width="{w1}" height="{h1}" fill="none" stroke="#D9B45A" stroke-width="3"/>'
            '<rect x="{m2}" y="{m2}" width="{w2}" height="{h2}" fill="none" stroke="#D9B45A" stroke-width="1.2" opacity=".8"/>'
            '{f}</svg>').format(w=w, h=h, m=m, w1=w - 2 * m, h1=h - 2 * m, m2=m + 14, w2=w - 2 * m - 28, h2=h - 2 * m - 28,
                                f=fregio(m + 24, m + 24, 1, 1) + fregio(w - m - 24, m + 24, -1, 1)
                                + fregio(m + 24, h - m - 24, 1, -1) + fregio(w - m - 24, h - m - 24, -1, -1))


FASCIO = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 420">'
          '<defs><linearGradient id="f" x1="0" x2="1"><stop offset="0" stop-color="#FFD58A" stop-opacity=".95"/>'
          '<stop offset=".25" stop-color="#FFC266" stop-opacity=".42"/><stop offset="1" stop-color="#FFB547" stop-opacity="0"/>'
          '</linearGradient><filter id="s"><feGaussianBlur stdDeviation="10"/></filter></defs>'
          '<path d="M0 210 L1600 0 L1600 420 Z" fill="url(#f)" filter="url(#s)"/>'
          '<path d="M0 210 L1600 120 L1600 300 Z" fill="url(#f)" opacity=".6"/></svg>')

# Il televisore: mobile di legno arancio, schermo bombato (buco), manopole, antenna. Il buco va da 9,5 a 70 % in
# larghezza e da 16 a 84 % in altezza (viewBox 1000 × 760): lì sotto il livello video.
TV = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 760">'
      '<defs><linearGradient id="l" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F2A33A"/>'
      '<stop offset="1" stop-color="#C46E1C"/></linearGradient>'
      '<radialGradient id="v" cx="40%" cy="38%" r="70%"><stop offset=".7" stop-color="#000" stop-opacity="0"/>'
      '<stop offset="1" stop-color="#000" stop-opacity=".55"/></radialGradient></defs>'
      '<path d="M420 120 L300 10 M560 120 L700 6" stroke="#222" stroke-width="10" stroke-linecap="round"/>'
      '<path fill-rule="evenodd" fill="url(#l)" d="M40 110 H960 Q990 110 990 140 V700 Q990 730 960 730 H40 Q10 730 10 700 V140 '
      'Q10 110 40 110 Z M150 122 Q95 122 95 180 V580 Q95 640 150 640 H650 Q705 640 705 580 V180 Q705 122 650 122 Z"/>'
      '<path fill="url(#v)" d="M150 122 Q95 122 95 180 V580 Q95 640 150 640 H650 Q705 640 705 580 V180 Q705 122 650 122 Z"/>'
      '<path fill="none" stroke="#7A3F0C" stroke-width="10" d="M150 122 Q95 122 95 180 V580 Q95 640 150 640 H650 Q705 640 705 580 '
      'V180 Q705 122 650 122 Z"/>'
      '<rect x="760" y="160" width="190" height="460" rx="26" fill="#2A1A0E"/>'
      '<circle cx="855" cy="260" r="52" fill="#E9E2D3"/><circle cx="855" cy="260" r="40" fill="#C9BFAA"/>'
      '<rect x="850" y="214" width="10" height="36" rx="5" fill="#2A1A0E"/>'
      '<circle cx="855" cy="410" r="40" fill="#E9E2D3"/><rect x="851" y="376" width="8" height="28" rx="4" fill="#2A1A0E"/>'
      + ''.join('<rect x="790" y="%d" width="130" height="8" rx="4" fill="#4A3220"/>' % y for y in range(490, 600, 22))
      + '<rect x="80" y="730" width="70" height="26" rx="6" fill="#2A1A0E"/><rect x="850" y="730" width="70" height="26" rx="6" '
      'fill="#2A1A0E"/></svg>')

# Il vano dell'ascensore (stretto e alto) con le tacche dei piani, e la cabina d'ottone con la finestrella.
VANO = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 1000">'
        '<rect x="20" y="0" width="80" height="1000" fill="#1E1A15" fill-opacity=".55"/>'
        '<rect x="20" y="0" width="80" height="1000" fill="none" stroke="#C9A45C" stroke-width="3"/>'
        '<line x1="44" y1="0" x2="44" y2="1000" stroke="#C9A45C" stroke-width="1.5" opacity=".6"/>'
        '<line x1="76" y1="0" x2="76" y2="1000" stroke="#C9A45C" stroke-width="1.5" opacity=".6"/>'
        + ''.join('<line x1="0" y1="%d" x2="120" y2="%d" stroke="#C9A45C" stroke-width="3"/>' % (y, y) for y in range(70, 1000, 143))
        + '</svg>')
CABINA = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 130">'
          '<line x1="50" y1="0" x2="50" y2="14" stroke="#C9A45C" stroke-width="3"/>'
          '<rect x="6" y="14" width="88" height="112" rx="6" fill="#C9A45C"/>'
          '<rect x="16" y="26" width="68" height="44" rx="3" fill="#FFE7A8"/>'
          '<line x1="50" y1="74" x2="50" y2="126" stroke="#7A5A22" stroke-width="3"/>'
          '<rect x="6" y="14" width="88" height="112" rx="6" fill="none" stroke="#7A5A22" stroke-width="3"/></svg>')

# La stampante dello scontrino (una barra scura con la fessura) e il rotolo di carta termica col bordo strappato.
STAMPANTE = ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 120">'
             '<defs><linearGradient id="p" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3A3633"/>'
             '<stop offset="1" stop-color="#1C1A18"/></linearGradient></defs>'
             '<rect x="0" y="0" width="600" height="96" rx="22" fill="url(#p)"/>'
             '<rect x="70" y="78" width="460" height="10" rx="5" fill="#0A0908"/>'
             '<circle cx="540" cy="36" r="9" fill="#7CFF6B"/><rect x="40" y="26" width="90" height="20" rx="6" fill="#55504A"/></svg>')


def rotolo(w, h):
    denti = ' '.join('L%.1f %d' % (w - (k + 0.5) * w / 18, h - (0 if k % 2 else 14)) for k in range(18))
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" preserveAspectRatio="none">'
            '<defs><linearGradient id="r" x1="0" x2="1"><stop offset="0" stop-color="#ECE6D6"/><stop offset=".08" stop-color="#FBF8EF"/>'
            '<stop offset=".92" stop-color="#FBF8EF"/><stop offset="1" stop-color="#E6DFCC"/></linearGradient></defs>'
            '<path d="M0 0 H{w} V{h1} {d} L0 {h1} Z" fill="url(#r)"/></svg>').format(w=w, h=h, h1=h - 14, d=denti)


DISEGNI = {
    'dorso-copertina.svg': copertina(1440, 810),
    'dorso-copertina-tel.svg': copertina(640, 1400),
    'lanterna-fascio.svg': FASCIO,
    'zapping-tv.svg': TV,
    'torre-vano.svg': VANO,
    'torre-cabina.svg': CABINA,
    'scontrino-stampante.svg': STAMPANTE,
    'scontrino-rotolo.svg': rotolo(460, 1200),
}


if __name__ == '__main__':
    for nome, svg in DISEGNI.items():
        open(os.path.join(OUT, nome), 'w', encoding='utf-8').write(svg)
        print(nome)
