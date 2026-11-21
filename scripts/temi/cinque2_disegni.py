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

def televisore():
    """Il televisore di Zapping rifatto (20 nov 2026, utente: «una tivu retrò decisamente più curata e realistica»): mobile
    d'arancio laccato con la grana, i fianchi in ombra e il lucido in alto, la mascherina scura con la cornice cromata, il
    vetro del tubo (vignetta, profondità, riflesso, righe di scansione leggere), il pannello d'alluminio spazzolato col
    selettore dei canali numerato, il volume, i tasti, la griglia dell'altoparlante e la spia; antenna a stilo cromata,
    gambe di noce con i puntali d'ottone. Stesso viewBox (1000 × 760) e stesso buco del primo: i livelli non si spostano."""
    buco = 'M150 122 Q95 122 95 180 V580 Q95 640 150 640 H650 Q705 640 705 580 V180 Q705 122 650 122 Z'
    corpo = 'M62 92 H938 Q986 92 986 140 V656 Q986 704 938 704 H62 Q14 704 14 656 V140 Q14 92 62 92 Z'
    cornice = 'M120 104 H680 Q738 104 738 162 V600 Q738 658 680 658 H120 Q62 658 62 600 V162 Q62 104 120 104 Z'
    import math

    def stilo(x0, y0, x1, y1):
        """Un'antenna a tre pezzi che si assottigliano, coi giunti e la pallina in cima."""
        out, pezzi, larg = [], [(0, .46), (.46, .76), (.76, 1)], [9, 6.5, 4.6]
        for (a, b), w in zip(pezzi, larg):
            xa, ya = x0 + (x1 - x0) * a, y0 + (y1 - y0) * a
            xb, yb = x0 + (x1 - x0) * b, y0 + (y1 - y0) * b
            out.append('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#7d8286" stroke-width="%.1f" stroke-linecap="round"/>'
                       % (xa, ya, xb, yb, w))
            out.append('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#f3f5f6" stroke-width="%.1f" stroke-linecap="round"/>'
                       % (xa, ya - w * .22, xb, yb - w * .22, w * .32))
            out.append('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="url(#cromo)" stroke="#5d6266" stroke-width="1"/>' % (xa, ya, w * .72))
        out.append('<circle cx="%.1f" cy="%.1f" r="7" fill="url(#pallina)"/>' % (x1, y1))
        return ''.join(out)

    def manopola(cx, cy, r, colore, segno):
        """Manopola con le righe per la presa, l'anello cromato e la tacca."""
        ang = math.radians(segno)
        tx, ty = cx + math.sin(ang) * r * .78, cy - math.cos(ang) * r * .78
        return ('<circle cx="{cx}" cy="{cy2}" r="{r1}" fill="#000" opacity=".45" filter="url(#sfuma3)"/>'
                '<circle cx="{cx}" cy="{cy}" r="{r1}" fill="none" stroke="url(#cromo)" stroke-width="3.5"/>'
                '<circle cx="{cx}" cy="{cy}" r="{r}" fill="url(#{col})"/>'
                '<circle cx="{cx}" cy="{cy}" r="{r2}" fill="none" stroke="#000" stroke-opacity=".38" stroke-width="{rw}" '
                'stroke-dasharray="2.2 3.1"/>'
                '<circle cx="{cx}" cy="{cy}" r="{r3}" fill="url(#{col})" stroke="#fff" stroke-opacity=".18" stroke-width="1.5"/>'
                '<line x1="{cx}" y1="{cy}" x2="{tx:.1f}" y2="{ty:.1f}" stroke="{tacca}" stroke-width="{tw}" stroke-linecap="round"/>'
                '<ellipse cx="{hx:.1f}" cy="{hy:.1f}" rx="{hr:.1f}" ry="{hr2:.1f}" fill="#fff" opacity=".22"/>').format(
            cx=cx, cy=cy, cy2=cy + 4, r=r, r1=r + 3, r2=r - 2.5, rw=5, r3=r * .72, col=colore, tx=tx, ty=ty,
            tacca='#E8892B' if colore == 'bachelite' else '#3a2a1c', tw=max(3, r * .1),
            hx=cx - r * .3, hy=cy - r * .38, hr=r * .32, hr2=r * .18)

    numeri = ''.join('<text x="%.1f" y="%.1f" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700" '
                     'fill="#EFE7D6" text-anchor="middle" dominant-baseline="central">%d</text>'
                     % (859 + math.sin(math.radians(k * 30)) * 60, 236 - math.cos(math.radians(k * 30)) * 60, k or 12)
                     for k in range(12))
    tacche = ''.join('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#EFE7D6" stroke-opacity=".55" stroke-width="1.6"/>'
                     % (859 + math.sin(math.radians(k * 30 + 15)) * 68, 236 - math.cos(math.radians(k * 30 + 15)) * 68,
                        859 + math.sin(math.radians(k * 30 + 15)) * 74, 236 - math.cos(math.radians(k * 30 + 15)) * 74)
                     for k in range(12))
    tasti = ''.join(
        '<rect x="{x}" y="{y}" width="36" height="{h}" rx="5" fill="url(#avorio)" stroke="#6d6458" stroke-width="1"/>'
        '<rect x="{x2}" y="{y2}" width="30" height="3" rx="1.5" fill="#fff" opacity=".55"/>'.format(
            x=x, y=455 if i else 459, h=26 if i else 22, x2=x + 3, y2=458 if i else 462)
        for i, x in enumerate((778, 822, 866, 910)))
    return (
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 760">'
        '<defs>'
        '<linearGradient id="corpo" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F7B653"/>'
        '<stop offset=".42" stop-color="#E98C2D"/><stop offset="1" stop-color="#B2561A"/></linearGradient>'
        '<linearGradient id="lati" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#3a1600" stop-opacity=".42"/>'
        '<stop offset=".06" stop-color="#3a1600" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".05"/>'
        '<stop offset=".94" stop-color="#3a1600" stop-opacity="0"/><stop offset="1" stop-color="#3a1600" stop-opacity=".42"/>'
        '</linearGradient>'
        '<linearGradient id="lucido" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".6"/>'
        '<stop offset=".035" stop-color="#fff" stop-opacity=".16"/><stop offset=".13" stop-color="#fff" stop-opacity="0"/>'
        '<stop offset=".9" stop-color="#2a0f00" stop-opacity="0"/><stop offset="1" stop-color="#2a0f00" stop-opacity=".3"/>'
        '</linearGradient>'
        '<radialGradient id="mascherina" cx="50%" cy="42%" r="70%"><stop offset="0" stop-color="#2d2621"/>'
        '<stop offset="1" stop-color="#0c0907"/></radialGradient>'
        '<linearGradient id="cromo" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f6f6f3"/>'
        '<stop offset=".22" stop-color="#a5a6a3"/><stop offset=".48" stop-color="#ececea"/><stop offset=".74" stop-color="#868784"/>'
        '<stop offset="1" stop-color="#d9d9d6"/></linearGradient>'
        '<linearGradient id="cromo2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#dcdcd8"/>'
        '<stop offset=".25" stop-color="#7c7d7a"/><stop offset=".5" stop-color="#c9c9c5"/><stop offset=".75" stop-color="#5f605d"/>'
        '<stop offset="1" stop-color="#b3b3af"/></linearGradient>'
        '<linearGradient id="alluminio" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#a9a8a2"/>'
        '<stop offset=".45" stop-color="#e6e5df"/><stop offset=".6" stop-color="#d6d5cf"/><stop offset="1" stop-color="#9c9b95"/>'
        '</linearGradient>'
        '<radialGradient id="bachelite" cx="38%" cy="32%" r="75%"><stop offset="0" stop-color="#5b5550"/>'
        '<stop offset=".5" stop-color="#1e1b19"/><stop offset="1" stop-color="#080707"/></radialGradient>'
        '<radialGradient id="crema" cx="38%" cy="32%" r="75%"><stop offset="0" stop-color="#fffbf2"/>'
        '<stop offset=".6" stop-color="#e6dcc7"/><stop offset="1" stop-color="#ad9f84"/></radialGradient>'
        '<radialGradient id="quadrante" cx="50%" cy="45%" r="60%"><stop offset="0" stop-color="#34302c"/>'
        '<stop offset="1" stop-color="#121010"/></radialGradient>'
        '<radialGradient id="pallina" cx="35%" cy="30%" r="70%"><stop offset="0" stop-color="#fff"/>'
        '<stop offset=".5" stop-color="#b9bdc0"/><stop offset="1" stop-color="#5e6366"/></radialGradient>'
        '<linearGradient id="avorio" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#f7f1e3"/>'
        '<stop offset="1" stop-color="#cbbfa6"/></linearGradient>'
        '<linearGradient id="gamba" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#2c190c"/>'
        '<stop offset=".45" stop-color="#6e4627"/><stop offset="1" stop-color="#24140a"/></linearGradient>'
        '<linearGradient id="ottone" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#7d5f24"/>'
        '<stop offset=".5" stop-color="#ecd07a"/><stop offset="1" stop-color="#6f531c"/></linearGradient>'
        '<linearGradient id="cupola" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#4a4541"/>'
        '<stop offset="1" stop-color="#141211"/></linearGradient>'
        '<radialGradient id="vignetta" cx="50%" cy="48%" r="66%"><stop offset=".66" stop-color="#000" stop-opacity="0"/>'
        '<stop offset="1" stop-color="#000" stop-opacity=".42"/></radialGradient>'
        '<linearGradient id="riflesso" x1="0" y1="0" x2=".7" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".2"/>'
        '<stop offset=".45" stop-color="#fff" stop-opacity=".05"/><stop offset="1" stop-color="#fff" stop-opacity="0"/>'
        '</linearGradient>'
        '<pattern id="righe" width="4" height="4" patternUnits="userSpaceOnUse"><rect width="4" height="1.5" fill="#000"/></pattern>'
        '<pattern id="fori" x="2" y="2" width="11" height="11" patternUnits="userSpaceOnUse">'
        '<circle cx="5.5" cy="5.5" r="2.5" fill="#1d1916"/><circle cx="5.5" cy="6.4" r="2.5" fill="none" stroke="#fff" '
        'stroke-opacity=".35" stroke-width=".8" stroke-dasharray="0 4 4 8"/></pattern>'
        '<filter id="grana" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".9" '
        'numOctaves="2" seed="4"/><feColorMatrix values="0 0 0 0 .35  0 0 0 0 .18  0 0 0 0 .05  0 0 0 .5 -.18"/>'
        '<feComposite in2="SourceGraphic" operator="in"/></filter>'
        '<filter id="spazzolato" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" '
        'baseFrequency=".004 .9" numOctaves="2" seed="9"/><feColorMatrix values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  0 0 0 .55 -.2"/>'
        '<feComposite in2="SourceGraphic" operator="in"/></filter>'
        '<filter id="sfuma3" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="3"/></filter>'
        '<filter id="sfuma8" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="8"/></filter>'
        '<filter id="sfuma12" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="12"/></filter>'
        '<clipPath id="nelbuco"><path d="' + buco + '"/></clipPath>'
        '<clipPath id="nelcorpo"><path d="' + corpo + '"/></clipPath>'
        '<mask id="foro" maskUnits="userSpaceOnUse" x="0" y="0" width="1000" height="760"><rect width="1000" height="760" '
        'fill="#fff"/><path d="' + buco + '" fill="#000"/></mask>'
        '</defs>'
        # l'ombra sul pavimento e le gambe
        '<ellipse cx="500" cy="748" rx="450" ry="9" fill="#000" opacity=".6" filter="url(#sfuma8)"/>'
        '<path d="M168 696 H210 L170 750 H146 Z" fill="url(#gamba)"/><path d="M790 696 H832 L854 750 H830 Z" fill="url(#gamba)"/>'
        '<path d="M150 740 H172 L169 752 H146 Z" fill="url(#ottone)"/><path d="M828 740 H851 L855 752 H831 Z" fill="url(#ottone)"/>'
        # le antenne, dietro la cupola
        + stilo(492, 70, 316, 8) + stilo(510, 70, 688, 6) +
        '<g mask="url(#foro)">'
        # il mobile
        '<path d="' + corpo + '" fill="url(#corpo)"/>'
        '<path d="' + corpo + '" fill="url(#lati)"/>'
        '<path d="' + corpo + '" fill="#fff" filter="url(#grana)"/>'
        '<path d="' + corpo + '" fill="url(#lucido)"/>'
        '<path d="' + corpo + '" fill="none" stroke="#7a3a0c" stroke-width="2.5" stroke-opacity=".7"/>'
        '<path d="M64 96 H936 Q980 96 981 138" fill="none" stroke="#fff" stroke-opacity=".55" stroke-width="2"/>'
        # la giunta del frontale
        '<rect x="30" y="106" width="940" height="584" rx="36" fill="none" stroke="#5a2600" stroke-opacity=".22" stroke-width="2"/>'
        '<rect x="31" y="107.5" width="940" height="584" rx="36" fill="none" stroke="#fff" stroke-opacity=".16" stroke-width="1.2"/>'
        # la mascherina col suo incavo e la cornice cromata
        '<path d="' + cornice + '" fill="#000" opacity=".5" filter="url(#sfuma8)" transform="translate(0 6)"/>'
        '<path d="' + cornice + '" fill="url(#mascherina)"/>'
        '<path d="' + cornice + '" fill="none" stroke="url(#cromo2)" stroke-width="4.5"/>'
        '<path d="' + cornice + '" fill="none" stroke="#000" stroke-opacity=".35" stroke-width="1" transform="translate(0 4.5)"/>'
        # il pannello d'alluminio
        '<rect x="752" y="116" width="214" height="566" rx="26" fill="#4a1f02" opacity=".45"/>'
        '<rect x="760" y="124" width="198" height="550" rx="22" fill="url(#alluminio)"/>'
        '<rect x="760" y="124" width="198" height="550" rx="22" fill="#fff" filter="url(#spazzolato)"/>'
        '<rect x="760" y="124" width="198" height="550" rx="22" fill="none" stroke="#6c6b66" stroke-width="2"/>'
        '<rect x="762" y="126" width="194" height="546" rx="20" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="1.2"/>'
        # il selettore dei canali
        '<circle cx="859" cy="240" r="78" fill="#000" opacity=".35" filter="url(#sfuma3)"/>'
        '<circle cx="859" cy="236" r="78" fill="url(#quadrante)" stroke="url(#cromo)" stroke-width="3"/>'
        + tacche + numeri + manopola(859, 236, 38, 'bachelite', 60) +
        # il volume, i tasti, la griglia, la spia
        manopola(859, 382, 30, 'crema', -40) + tasti +
        '<rect x="780" y="502" width="158" height="128" rx="10" fill="#cfcec8"/>'
        '<rect x="780" y="502" width="158" height="128" rx="10" fill="url(#fori)"/>'
        '<rect x="780" y="502" width="158" height="128" rx="10" fill="none" stroke="#77766f" stroke-width="2"/>'
        '<circle cx="800" cy="652" r="10" fill="url(#crema)" stroke="#6d6458" stroke-width="1"/>'
        '<circle cx="918" cy="652" r="11" fill="#ff3b2f" opacity=".55" filter="url(#sfuma3)"/>'
        '<circle cx="918" cy="652" r="5" fill="#ff5a45" stroke="#7a0f08" stroke-width="1"/>'
        '<circle cx="916.5" cy="650.5" r="1.6" fill="#fff" opacity=".8"/>'
        # la targhetta sotto lo schermo
        '<rect x="356" y="670" width="88" height="18" rx="4" fill="url(#cromo)" stroke="#6b6c69" stroke-width="1"/>'
        '<text x="400" y="679.5" font-family="Arial, Helvetica, sans-serif" font-size="10.5" font-weight="700" letter-spacing="2.5" '
        'fill="#3b3b3a" text-anchor="middle" dominant-baseline="central">ZAPPING</text>'
        '</g>'
        # la cupola delle antenne
        '<path d="M432 94 Q434 56 501 54 Q568 56 570 94 Z" fill="url(#cupola)"/>'
        '<path d="M446 84 Q452 64 500 62" fill="none" stroke="#fff" stroke-opacity=".25" stroke-width="3" stroke-linecap="round"/>'
        '<rect x="488" y="62" width="26" height="12" rx="4" fill="url(#cromo)"/>'
        # il bordo del tubo e il vetro
        '<path d="' + buco + '" fill="none" stroke="#050403" stroke-width="7"/>'
        '<g clip-path="url(#nelbuco)">'
        '<path d="' + buco + '" fill="none" stroke="#000" stroke-width="38" opacity=".55" filter="url(#sfuma12)"/>'
        '<rect x="90" y="118" width="620" height="526" fill="url(#vignetta)"/>'
        '<rect x="90" y="118" width="620" height="526" fill="url(#righe)" opacity=".07"/>'
        '<path d="M112 134 Q380 116 600 132 Q360 176 112 430 Z" fill="url(#riflesso)"/>'
        '<path d="M150 140 Q300 130 420 134" fill="none" stroke="#fff" stroke-opacity=".22" stroke-width="3" stroke-linecap="round"/>'
        '</g>'
        '</svg>')


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
    'zapping-tv-2.svg': televisore(),
    'torre-vano.svg': VANO,
    'torre-cabina.svg': CABINA,
    'scontrino-stampante.svg': STAMPANTE,
    'scontrino-rotolo.svg': rotolo(460, 1200),
}


if __name__ == '__main__':
    for nome, svg in DISEGNI.items():
        open(os.path.join(OUT, nome), 'w', encoding='utf-8').write(svg)
        print(nome)
