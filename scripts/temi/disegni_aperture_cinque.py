# -*- coding: utf-8 -*-
"""I disegni delle aperture ritoccate dei primi cinque temi del calendario social (15 nov 2026).

- Capitonné: la nappa del cordone che lega la tenda raccolta (SVG).
- Rastrello: il tosaerba a cilindro visto di lato, che va verso destra col manico DIETRO (prima il manico stava davanti,
  come se lo si tirasse), in quattro pezzi: il corpo fermo, la ruota, il cilindro delle lame e il rullo, che lo Slider
  Pro fa girare solo mentre il tosaerba avanza (rotazione legata alla strada fatta).
- Scheggia: le crepe del primo e del secondo colpo su una tela doppia, per spostarle dove si colpisce il vetro.
- Ventaglio: le pieghe di un ventaglio vero (PNG): la stoffa stampata (Pexels 14918088) tagliata a spicchi già ruotati
  al contrario, così che aperto il ventaglio la stoffa si ricompone intera; ogni spicchio ha l'ombra della sua piega
  (a fisarmonica: colmo chiaro, incavo scuro) e, sotto gli incavi, la stecca di bambù che si vede fra il perno e la stoffa.

Uso: python disegni_aperture_cinque.py <cartella dei media> <stoffa.jpg>
I nomi sono nuovi (mai sovrascrivere un file già nella libreria: la cache lo terrebbe vecchio).
"""
import math
import os
import sys

import numpy as np
from PIL import Image

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'
STOFFA = sys.argv[2] if len(sys.argv) > 2 else ''
os.makedirs(OUT, exist_ok=True)


def salva(nome, corpo):
    with open(os.path.join(OUT, nome), 'w', encoding='utf-8', newline='\n') as f:
        f.write(corpo.strip() + '\n')


# ───────────────────────── CAPITONNÉ: la nappa ─────────────────────────
def nappa():
    fili = []
    for k in range(34):
        f = k / 33.0
        x0 = 38 + f * 44
        x1 = 22 + f * 76 + math.sin(k * 1.7) * 2.5
        y1 = 318 + math.sin(k * 2.3) * 7
        tono = ['#E9CC7E', '#C9A24A', '#F3DD98', '#A9822F', '#D8B661'][k % 5]
        fili.append('<path d="M%.1f 118 Q%.1f 220 %.1f %.1f" stroke="%s" stroke-width="3.2" fill="none" stroke-linecap="round"/>'
                    % (x0, (x0 + x1) / 2, x1, y1, tono))
    salva('capitonne-nappa.svg', '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 340">
<defs>
<linearGradient id="o" x1="0" x2="1"><stop offset="0" stop-color="#5C4416"/><stop offset=".35" stop-color="#F1D88E"/>
<stop offset=".6" stop-color="#C9A24A"/><stop offset="1" stop-color="#4E3A12"/></linearGradient>
<linearGradient id="g" x1="0" x2="1"><stop offset="0" stop-color="#000" stop-opacity=".35"/><stop offset=".4" stop-color="#000" stop-opacity="0"/>
<stop offset=".75" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".45"/></linearGradient>
</defs>
<path d="M60 2 C50 2 46 10 46 18 C46 26 52 30 60 30 C68 30 74 26 74 18 C74 10 70 2 60 2 Z M60 9 C65 9 67 13 67 18 C67 22 64 24 60 24 C56 24 53 22 53 18 C53 13 55 9 60 9 Z" fill="url(#o)"/>
<path d="M60 28 C40 30 30 52 32 78 C33 92 40 102 46 106 L74 106 C80 102 87 92 88 78 C90 52 80 30 60 28 Z" fill="url(#o)"/>
<path d="M36 58 Q60 64 84 58 M33 74 Q60 81 87 74 M36 90 Q60 96 84 90" stroke="#5C4416" stroke-width="2" fill="none" opacity=".55"/>
<rect x="40" y="104" width="40" height="16" rx="5" fill="url(#o)"/>
<path d="M40 109 H80 M40 115 H80" stroke="#5C4416" stroke-width="1.6" opacity=".6"/>
%s
<path d="M38 118 Q60 120 82 118 L100 318 Q60 330 20 318 Z" fill="url(#g)"/>
</svg>''' % ''.join(fili))


# ───────────────────────── RASTRELLO: il tosaerba a cilindro ─────────────────────────
# Riferimenti nel viewBox del corpo (600 × 420, terreno a y = 400): ruota (330, 300) r 100, cilindro (395, 338) r 58,
# rullo (130, 370) r 30. gen_rastrello.py usa gli stessi numeri per mettere i pezzi che girano.
def tosaerba():
    salva('rastrello-tosaerba2.svg', '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 420">
<defs>
<linearGradient id="v" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3F7A4C"/><stop offset="1" stop-color="#1E4A2A"/></linearGradient>
<linearGradient id="l" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#B07A44"/><stop offset=".5" stop-color="#8A5A2E"/><stop offset="1" stop-color="#5E3B1C"/></linearGradient>
<linearGradient id="b" x1="0" x2="1"><stop offset="0" stop-color="#7A5B22"/><stop offset=".45" stop-color="#F1D88E"/><stop offset="1" stop-color="#8E6E2A"/></linearGradient>
</defs>
<!-- il manico: dal telaio sale dietro, verso sinistra, fino all'impugnatura a T (si spinge da dietro) -->
<path d="M300 262 L86 52" stroke="#3A2614" stroke-width="15" stroke-linecap="round"/>
<path d="M300 262 L86 52" stroke="url(#l)" stroke-width="11" stroke-linecap="round"/>
<path d="M286 248 L262 225" stroke="url(#b)" stroke-width="15" stroke-linecap="round"/>
<rect x="56" y="36" width="44" height="26" rx="12" transform="rotate(-44 78 49)" fill="#2B2B2B"/>
<rect x="60" y="40" width="36" height="18" rx="9" transform="rotate(-44 78 49)" fill="#4A4A4A"/>
<!-- il telaio di ghisa, traforato: dal rullo dietro al cilindro davanti -->
<path d="M118 352 C150 318 210 300 262 284 L332 262 C372 252 418 268 440 300 C452 318 452 346 440 362 L412 372 C400 340 376 322 346 322 C320 322 300 336 292 356 L176 378 C150 384 126 376 118 352 Z" fill="url(#v)" stroke="#16351E" stroke-width="3"/>
<path d="M170 346 C200 330 236 320 266 314" stroke="#9FD3A8" stroke-width="4" fill="none" opacity=".45" stroke-linecap="round"/>
<circle cx="214" cy="336" r="15" fill="none" stroke="#16351E" stroke-width="3" opacity=".7"/>
<!-- la lama fissa sotto il cilindro e il bullone del cilindro -->
<path d="M352 398 L452 392" stroke="#9AA3A8" stroke-width="6" stroke-linecap="round"/>
<path d="M352 398 L452 392" stroke="#E3E8EB" stroke-width="2" stroke-linecap="round"/>
<circle cx="130" cy="370" r="9" fill="url(#b)" stroke="#5C4416" stroke-width="2"/>
<rect x="150" y="352" width="54" height="14" rx="3" transform="rotate(-12 177 359)" fill="url(#b)" stroke="#5C4416" stroke-width="1.5"/>
</svg>''')
    # la ruota: copertone con la battitura (si vede girare), cerchione di terracotta, dieci raggi, mozzo d'ottone; fra i
    # raggi è vuota: dietro si vedono il telaio e il cilindro delle lame che gira
    tacche = ''.join('<rect x="96" y="1" width="8" height="10" rx="2" fill="#0E120F" transform="rotate(%d 100 100)"/>' % (k * 15)
                     for k in range(24))
    raggi = ''.join('<rect x="97" y="18" width="6" height="66" rx="3" fill="#2A2F2B" transform="rotate(%d 100 100)"/>' % (k * 36)
                    for k in range(10))
    salva('rastrello-ruota.svg', '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
<defs><radialGradient id="m"><stop offset="0" stop-color="#F1D88E"/><stop offset="1" stop-color="#8E6E2A"/></radialGradient></defs>
<circle cx="100" cy="100" r="93" fill="none" stroke="#1F2620" stroke-width="12"/>%s
<circle cx="100" cy="100" r="86" fill="none" stroke="#A94F25" stroke-width="8"/>
<circle cx="100" cy="100" r="81" fill="none" stroke="#6E2F12" stroke-width="2"/>
%s
<circle cx="100" cy="100" r="19" fill="url(#m)" stroke="#5C4416" stroke-width="2"/>
<circle cx="100" cy="100" r="7" fill="#3A2C10"/>
</svg>''' % (tacche, raggi))
    # il cilindro visto di testa: cinque lame elicoidali (curve), l'albero al centro
    lame = ''.join('<path d="M60 60 Q78 40 104 30" stroke="#C7CED2" stroke-width="7" fill="none" stroke-linecap="round" '
                   'transform="rotate(%d 60 60)"/><path d="M60 60 Q78 40 104 30" stroke="#5D676D" stroke-width="2" fill="none" '
                   'stroke-linecap="round" transform="rotate(%d 60 60) translate(1.5 3)"/>' % (k * 72, k * 72) for k in range(5))
    salva('rastrello-cilindro.svg', '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
<circle cx="60" cy="60" r="56" fill="none" stroke="#5D676D" stroke-width="2" opacity=".5"/>%s
<circle cx="60" cy="60" r="11" fill="#3B4347"/><circle cx="60" cy="60" r="4" fill="#C7CED2"/>
</svg>''' % lame)
    # il rullo: legno con le scanalature (si vede girare) e il perno
    righe = ''.join('<rect x="28" y="2" width="4" height="56" fill="#4A3620" transform="rotate(%d 30 30)"/>' % (k * 30) for k in range(6))
    salva('rastrello-rullo.svg', '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60">
<circle cx="30" cy="30" r="29" fill="#7A5A34" stroke="#3A2614" stroke-width="2"/>%s
<circle cx="30" cy="30" r="22" fill="none" stroke="#3A2614" stroke-width="1.5" opacity=".6"/>
<circle cx="30" cy="30" r="7" fill="#C9A24A" stroke="#5C4416" stroke-width="1.5"/>
</svg>''' % righe)


# ───────────────────────── VENTAGLIO: le pieghe ─────────────────────────
PIEGHE = 14           # spicchi della stoffa
APERTURA = 140.0      # gradi del ventaglio aperto
R = 1000              # raggio in px del disegno (sulla tela del computer diventa ~450 px)
R_IN = 0.40           # dove comincia la stoffa (dentro si vedono solo le stecche)
SOTTO = 14            # px sotto il perno (la testa arrotondata delle stecche)


def angoli():
    th = APERTURA / PIEGHE
    return th, [-APERTURA / 2 + th * (k + 0.5) for k in range(PIEGHE)]


def pieghe():
    th, fin = angoli()
    stoffa = Image.open(STOFFA).convert('RGB')
    # la stoffa distesa sotto il ventaglio aperto: un riquadro 2R × R (il perno in basso al centro), coperto
    tw, tht = 2 * R, R
    sc = max(tw / stoffa.width, tht / stoffa.height) * 1.02
    st = stoffa.resize((int(stoffa.width * sc) + 1, int(stoffa.height * sc) + 1), Image.LANCZOS)
    ox, oy = (st.width - tw) // 2, (st.height - tht) // 2
    S = np.asarray(st, dtype=np.float32)[oy:oy + tht, ox:ox + tw]
    mezza = R * math.sin(math.radians(th / 2)) + 16
    W = int(math.ceil(mezza)) * 2
    H = R + SOTTO
    cx, cy = W / 2.0, float(R)            # il perno
    ys, xs = np.mgrid[0:H, 0:W].astype(np.float32)
    dx, dy = xs + 0.5 - cx, cy - (ys + 0.5)
    r = np.hypot(dx, dy)
    phi = np.degrees(np.arctan2(dx, dy))  # 0 = in alto, positivo a destra
    for k in range(PIEGHE):
        a = fin[k]
        img = np.zeros((H, W, 4), np.float32)
        # la stoffa: dove sta questo spicchio quando il ventaglio è aperto
        foglia = (r >= R * R_IN) & (r <= R - 1) & (np.abs(phi) <= th / 2 + 0.06)
        ga = np.radians(a + phi)
        sx = np.clip(R + r * np.sin(ga), 0, tw - 1).astype(np.int32)
        sy = np.clip(R - r * np.cos(ga), 0, tht - 1).astype(np.int32)
        col = S[sy, sx]
        # la piega a fisarmonica: gli spicchi pari salgono dall'incavo (sinistra) al colmo, i dispari scendono
        f = np.clip(phi / th + 0.5, 0, 1)
        f = f if k % 2 == 0 else 1 - f
        luce = 0.62 + 0.5 * f ** 0.85
        luce = luce * (0.92 + 0.08 * np.clip((r / R - R_IN) / (1 - R_IN), 0, 1))   # verso il perno un filo più scuro
        orlo = (r > R - 9) | (r < R * R_IN + 5)                                     # il bordo cucito, fuori e dentro
        luce = np.where(orlo, luce * 0.72, luce)
        c = np.clip(col * luce[..., None], 0, 255)
        img[foglia, :3] = c[foglia]
        img[foglia, 3] = 255
        # le stecche: sotto ogni incavo (bordo sinistro degli spicchi pari) e la guardia sul bordo destro dell'ultimo
        def stecca(ang_loc, sopra_la_stoffa):
            # distanza dalla retta che esce dal perno con l'angolo ang_loc (nello spicchio)
            u = np.radians(ang_loc)
            lungo = dx * np.sin(u) + dy * np.cos(u)
            lato = dx * np.cos(u) - dy * np.sin(u)
            fino = R - 4 if sopra_la_stoffa else R * R_IN + 30
            largh = 13 - 4 * np.clip(lungo / R, 0, 1)
            m = (np.abs(lato) <= largh / 2) & (lungo >= -SOTTO + 2) & (lungo <= fino)
            m |= (np.hypot(dx, dy) <= 9)          # la testa attorno al perno
            vis = m if sopra_la_stoffa else (m & ~foglia)
            t = np.clip(lato / np.maximum(largh / 2, 1), -1, 1)
            legno = np.stack([233 - 40 * np.abs(t), 206 - 52 * np.abs(t), 152 - 62 * np.abs(t)], -1)
            img[vis, :3] = legno[vis]
            img[vis, 3] = 255
        if k % 2 == 0:
            stecca(-th / 2, k == 0)
        if k == PIEGHE - 1:
            stecca(th / 2, True)
        Image.fromarray(img.astype(np.uint8), 'RGBA').save(os.path.join(OUT, 'ventaglio-piega-%02d.png' % k), optimize=True)
    print('pieghe: %d spicchi %d × %d px, perno a %.2f%% dell\'altezza' % (PIEGHE, W, H, R / H * 100))


# ───────────────────────── SCHEGGIA: le crepe dove si colpisce ─────────────────────────
# Le crepe del primo e del secondo colpo, ognuna con la sua stella al centro di una tela doppia (3200 × 1800, gli stessi
# px di prima su 1600 × 900): il livello è grande il doppio dello slider, centrato, e si sposta dove si è cliccato
# (--mps-clic-x/y del Pro Slider). La seconda crepa è solo quella nuova: la prima resta dov'era.
def crepe_colpi():
    import random
    VB = '0 0 3200 1800'

    def crepe(cx, cy, n, lungo, seme, anelli):
        rng = random.Random(seme)
        p = ''
        for k in range(n):
            a = k * 2 * math.pi / n + rng.uniform(-.25, .25)
            x, y = cx, cy
            pts = ['M%.0f %.0f' % (x, y)]
            L = rng.uniform(.5, 1) * lungo
            for _ in range(7):
                a += rng.uniform(-.22, .22)
                x += math.cos(a) * L / 7
                y += math.sin(a) * L / 7
                pts.append('L%.0f %.0f' % (x, y))
                if rng.random() < .35:
                    b = a + rng.choice([-1, 1]) * rng.uniform(.5, 1)
                    p += '<path d="M%.0f %.0f L%.0f %.0f" stroke="#fff" stroke-width="1.6" opacity=".7"/>' % (
                        x, y, x + math.cos(b) * L / 5, y + math.sin(b) * L / 5)
            p += '<path d="%s" stroke="#fff" stroke-width="2.4" fill="none" opacity=".9"/>' % ' '.join(pts)
        for r in anelli:
            pts = []
            for k in range(n * 2):
                a = k * math.pi / n
                rr = r * rng.uniform(.85, 1.15)
                pts.append('%s%.0f %.0f' % ('M' if not k else 'L', cx + math.cos(a) * rr, cy + math.sin(a) * rr))
            p += '<path d="%s Z" stroke="#fff" stroke-width="1.4" fill="none" opacity=".55"/>' % ' '.join(pts)
        return p
    ombra = '<defs><filter id="o"><feDropShadow dx="0" dy="0" stdDeviation="1.5" flood-color="#000" flood-opacity=".6"/></filter></defs>'
    for nome, corpo in (('scheggia-colpo-1.svg', crepe(1600, 900, 9, 300, 1, [40]) ),
                        ('scheggia-colpo-2.svg', crepe(1600, 900, 14, 760, 2, [90, 170, 280]))):
        salva(nome, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="%s" preserveAspectRatio="xMidYMid slice">%s<g filter="url(#o)">%s</g>'
                    '<circle cx="1600" cy="900" r="10" fill="#fff" opacity=".8"/></svg>' % (VB, ombra, corpo))


# ───────────────────────── CHIAVISTELLO: l'insegna a bandiera ─────────────────────────
# Chiesta più elegante il 15 nov 2026 (era fatta di forme piatte: un rettangolo, una riga nera, due fili). Il braccio di
# ferro battuto entra dal bordo destro (la facciata è appena fuori dallo schermo), con la piastra al muro e le volute
# vicino al muro; le catene a maglie hanno in cima l'occhiello attorno alla barra (così si appendono dove serve, sul
# computer e sul telefono); la tavola ha la cornice d'ottone a sbalzo, lo smalto con un filo di grana, la doppia
# filettatura dorata, i fiori agli angoli e gli anelli in cima. Le scritte restano livelli di testo sopra la tavola.
CH_TAVOLA = (640, 520)      # viewBox della tavola; gli anelli in (51, 62) e (589, 62), raggio 14
CH_BRACCIO = (1000, 112)    # viewBox del braccio; la barra a y 20–32


def chiavistello():
    ferro = ('<linearGradient id="f" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#4A423B"/>'
             '<stop offset=".45" stop-color="#26201C"/><stop offset="1" stop-color="#110E0C"/></linearGradient>')

    def voluta(cx, cy, r, giri=1.6, verso=1):
        pts = []
        n = 40
        for k in range(n + 1):
            a = verso * k / n * giri * 2 * math.pi
            rr = r * (1 - 0.82 * k / n)
            pts.append('%s%.1f %.1f' % ('M' if not k else 'L', cx + rr * math.cos(a), cy + rr * math.sin(a)))
        return '<path d="%s" fill="none" stroke="url(#f)" stroke-width="6" stroke-linecap="round"/>' % ' '.join(pts)
    salva('chiavistello-braccio.svg', '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 112">
<defs>%s
<linearGradient id="p" x1="0" x2="1"><stop offset="0" stop-color="#2E2722"/><stop offset="1" stop-color="#0E0B09"/></linearGradient>
<filter id="o" x="-5%%" y="-30%%" width="110%%" height="170%%"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#000" flood-opacity=".45"/></filter>
</defs>
<g filter="url(#o)">
<path d="M968 104 C 900 104 820 92 770 32" fill="none" stroke="url(#f)" stroke-width="8" stroke-linecap="round"/>
%s%s%s
<rect x="36" y="20" width="936" height="12" rx="6" fill="url(#f)"/>
<rect x="44" y="21.5" width="920" height="2.5" rx="1.2" fill="#8A7D70" opacity=".55"/>
<circle cx="30" cy="26" r="13" fill="url(#f)"/><circle cx="26" cy="22" r="4" fill="#8A7D70" opacity=".5"/>
<rect x="962" y="2" width="38" height="108" rx="4" fill="url(#p)"/>
<circle cx="981" cy="14" r="4.5" fill="#6B5F55"/><circle cx="981" cy="98" r="4.5" fill="#6B5F55"/>
</g>
</svg>''' % (ferro, voluta(880, 66, 26, 1.7, 1), voluta(800, 52, 15, 1.5, -1), voluta(930, 86, 12, 1.4, -1)))

    maglie = ''
    for k in range(18):
        y = 22 + k * 20
        if k % 2 == 0:
            maglie += ('<ellipse cx="12" cy="%d" rx="6.5" ry="11.5" fill="none" stroke="url(#c)" stroke-width="3.6"/>'
                       '<ellipse cx="10.5" cy="%d" rx="3" ry="8" fill="none" stroke="#9C8A6A" stroke-width="1" opacity=".6"/>' % (y, y))
        else:
            maglie += '<rect x="10" y="%d" width="4" height="23" rx="2" fill="url(#c)"/>' % (y - 11)
    salva('chiavistello-catena.svg', '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 380" preserveAspectRatio="xMidYMin slice">
<defs><linearGradient id="c" x1="0" x2="1"><stop offset="0" stop-color="#5A4A3A"/><stop offset=".5" stop-color="#2A221C"/>
<stop offset="1" stop-color="#15110E"/></linearGradient></defs>
<circle cx="12" cy="8" r="7.5" fill="none" stroke="url(#c)" stroke-width="3.6"/>%s
</svg>''' % maglie)

    def tavola(nome, chiaro, scuro):
        bordo = 'M16 86 Q320 4 624 86 L624 498 Q624 504 618 504 L22 504 Q16 504 16 498 Z'
        dentro = 'M38 102 Q320 30 602 102 L602 478 Q602 482 598 482 L42 482 Q38 482 38 478 Z'
        filo1 = 'M56 116 Q320 52 584 116 L584 464 L56 464 Z'
        filo2 = 'M64 122 Q320 60 576 122 L576 456 L64 456 Z'
        fiore = lambda x, y: ('<g transform="translate(%d %d)"><path d="M0 -9 L4 0 L0 9 L-4 0 Z" fill="#E9C46A"/>'
                              '<path d="M-9 0 L0 4 L9 0 L0 -4 Z" fill="#E9C46A" opacity=".75"/><circle r="2" fill="#FFF3C4"/></g>' % (x, y))
        salva(nome, '''
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 520">
<defs>
<linearGradient id="o" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F6E3A1"/><stop offset=".35" stop-color="#C9A24A"/>
<stop offset=".7" stop-color="#9C7A2E"/><stop offset="1" stop-color="#6E521C"/></linearGradient>
<linearGradient id="s" x1="0" y1="0" x2=".3" y2="1"><stop offset="0" stop-color="%s"/><stop offset="1" stop-color="%s"/></linearGradient>
<radialGradient id="l" cx=".3" cy=".2" r=".9"><stop offset="0" stop-color="#fff" stop-opacity=".09"/><stop offset=".6" stop-color="#fff" stop-opacity="0"/></radialGradient>
<filter id="g" x="0" y="0" width="100%%" height="100%%"><feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" seed="7"/>
<feColorMatrix values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 .07 0"/><feComposite in2="SourceGraphic" operator="in"/></filter>
<filter id="d" x="-10%%" y="-10%%" width="120%%" height="125%%"><feDropShadow dx="0" dy="9" stdDeviation="9" flood-color="#000" flood-opacity=".42"/></filter>
</defs>
<g filter="url(#d)">
<path d="%s" fill="url(#o)"/>
<path d="%s" fill="none" stroke="#FFF1C2" stroke-width="2" opacity=".55" transform="translate(0 2)"/>
<path d="%s" fill="url(#s)"/>
<path d="%s" fill="#000" filter="url(#g)"/>
<path d="%s" fill="url(#l)"/>
<path d="%s" fill="none" stroke="#5A3E10" stroke-width="2.5" opacity=".55"/>
</g>
<path d="%s" fill="none" stroke="#E9C46A" stroke-width="2.2" opacity=".95"/>
<path d="%s" fill="none" stroke="#E9C46A" stroke-width="1" opacity=".8"/>
%s%s%s%s
<circle cx="51" cy="62" r="14" fill="none" stroke="url(#o)" stroke-width="6"/>
<circle cx="589" cy="62" r="14" fill="none" stroke="url(#o)" stroke-width="6"/>
</svg>''' % (chiaro, scuro, bordo, bordo, dentro, dentro, dentro, dentro, filo1, filo2,
             fiore(64, 122), fiore(576, 122), fiore(64, 456), fiore(576, 456)))
    tavola('chiavistello-insegna-vendesi-2.svg', '#B9563C', '#7E3220')
    tavola('chiavistello-insegna-venduto-2.svg', '#3A5A6E', '#1C3140')


if __name__ == '__main__':
    chiavistello()
    crepe_colpi()
    nappa()
    tosaerba()
    if STOFFA:
        pieghe()
