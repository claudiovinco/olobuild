# -*- coding: utf-8 -*-
"""La tenda dell'apertura di Capitonné, ridisegnata (21 nov 2026, utente: «rendi la tenda di questo demo più realistica e
morbida, lavora anche sull'aggancio giallo finale (il cordino)»).

Prima la tenda erano 14 strisce uguali di sfumature CSS: raccolte facevano righe regolari, e il cordone era una barra
d'oro dritta con una nappa piccola. Ora:

- capitonne-tenda-velluto.webp (2560 × 1440, con trasparenza): il telo intero, a pieghe irregolari che si allargano
  verso il fondo e ondeggiano appena, col lucido del velluto sui fianchi delle pieghe, le pieghe strette sotto il bastone,
  l'orlo ondulato col gallone d'oro e il gallone sul bordo libero. Le 14 pieghe del Pro Slider se lo dividono
  (background-position): chiusa è un telo solo, raccolta si stringe piega per piega.
- capitonne-tenda-legata.webp (computer) e capitonne-tenda-legata-tel.webp (telefono): la tenda raccolta e legata, a
  clessidra: sopra il cordone il velluto si gonfia e ricade, sotto si allarga fino a terra; le pieghe seguono il profilo.
  La legatura sta a metà altezza (computer) o al 55 % (telefono): metà altezza è l'unico punto in cui un livello
  allungato a tutta scena (questo) e uno della composizione 16:9 (il cordone) coincidono a ogni formato di finestra.
- capitonne-cordone.svg / capitonne-cordone-tel.svg: il cordone ritorto a due capi che avvolge la tenda, col nodo davanti.
- capitonne-nappa-2.svg: la nappa vera: cappio ritorto, testa fasciata di filo d'oro con la collana di riccioli, gonna di
  fili. Dondola piano da sola (CSS dentro l'SVG; ferma con «riduci il movimento»).

Le misure (in % della tela 1440 × 810 del computer) sono quelle di gen_capitonne.py: la tenda raccolta va dall'85 % al
muro, legata si stringe al 91 %; i livelli legati occupano dall'80 % al 100 %.
Uso: python disegni_capitonne_tenda.py <cartella dei media>
I nomi sono nuovi: mai sovrascrivere un file già nella libreria (la cache lo terrebbe vecchio).
"""
import math
import os
import sys

import numpy as np
from PIL import Image

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'
os.makedirs(OUT, exist_ok=True)

# ── utilità ──
def sfoca1(a, r, asse):
    """Sfocatura a scatola ripetuta tre volte (≈ gaussiana) lungo un asse; r in pixel."""
    r = int(r)
    if r < 1:
        return a
    n = a.shape[asse]
    for _ in range(3):
        pad = [(0, 0)] * a.ndim
        pad[asse] = (r + 1, r)
        c = np.cumsum(np.pad(a, pad, mode='edge'), axis=asse)
        a = (np.take(c, np.arange(2 * r + 1, 2 * r + 1 + n), axis=asse) - np.take(c, np.arange(n), axis=asse)) / (2 * r + 1)
    return a


def sfoca(a, rx, ry):
    return sfoca1(sfoca1(a, rx, 1), ry, 0)


def rumore(rng, shape, rx, ry):
    n = sfoca(rng.standard_normal(shape).astype(np.float32), rx, ry)
    return n / (np.abs(n).max() + 1e-6)


def liscia(e0, e1, x):
    t = np.clip((x - e0) / (e1 - e0), 0, 1)
    return t * t * (3 - 2 * t)


def scala(v, tappe):
    """Mappa un valore 0–1+ su una scala di colori [(punto, (r, g, b)), …]."""
    pts = np.array([p for p, _ in tappe])
    out = np.zeros(v.shape + (3,), np.float32)
    for k in range(3):
        out[..., k] = np.interp(v, pts, [c[k] for _, c in tappe])
    return out


VELLUTO = [(0.00, (13, 2, 6)), (0.14, (38, 6, 15)), (0.32, (76, 17, 33)), (0.52, (116, 31, 49)), (0.72, (156, 50, 70)),
           (0.88, (186, 76, 96)), (1.08, (214, 122, 138))]
ORO = [(0.0, (52, 34, 8)), (0.3, (128, 94, 34)), (0.55, (196, 156, 70)), (0.8, (238, 210, 134)), (1.0, (255, 241, 196))]
LUCE = np.array([-0.55, -0.32, 0.77])
LUCE = LUCE / np.linalg.norm(LUCE)


def luce(z, zn):
    """Chiaroscuro del velluto dalla profondità z (px) e dalla profondità relativa zn (0 = incavo, 1 = colmo)."""
    gy, gx = np.gradient(z)
    inv = 1.0 / np.sqrt(gx * gx + gy * gy + 1.0)
    nx, ny, nz = -gx * inv, -gy * inv, inv
    nl = nx * LUCE[0] + ny * LUCE[1] + nz * LUCE[2]
    dif = np.clip(nl, 0, 1)
    fianco = (1 - nz) ** 1.6                      # il lucido del velluto: i fianchi delle pieghe, visti di sbieco
    b = 0.09 + 0.60 * dif ** 1.45 + 0.46 * fianco * np.clip(nl + 0.35, 0, 1)
    ao = 0.20 + 0.80 * np.clip(zn, 0, 1) ** 0.55  # gli incavi profondi restano al buio
    return b * ao, dif


def trama(rng, shape, fine=1.0):
    """Il pelo del velluto: grana fine, righe verticali del pelo, chiazze larghe del velluto schiacciato."""
    h, w = shape
    g = rumore(rng, shape, max(1, int(fine)), max(1, int(fine)))
    pelo = rumore(rng, shape, 1, int(28 * fine))
    chiazze = rumore(rng, shape, int(70 * fine), int(90 * fine))
    return 1 + 0.035 * g + 0.04 * pelo + 0.07 * chiazze, chiazze


def gallone(xx, yy, base):
    """Il gallone d'oro intrecciato (spighetta a diagonali), con la luce della piega sotto (base 0–1)."""
    tw = 0.5 + 0.5 * np.sin(xx * 0.85 + yy * 1.7)
    tw2 = 0.5 + 0.5 * np.sin(xx * 0.85 - yy * 1.7 + 1.3)
    v = 0.18 + 0.72 * base + 0.16 * (np.maximum(tw, tw2) - 0.5)
    return scala(np.clip(v, 0, 1), ORO)


def salva_webp(rgb, alfa, nome, q=84):
    a = np.dstack([np.clip(rgb, 0, 255), np.clip(alfa * 255, 0, 255)]).astype(np.uint8)
    Image.fromarray(a, 'RGBA').save(os.path.join(OUT, nome), 'WEBP', quality=q, method=6)
    print(nome, os.path.getsize(os.path.join(OUT, nome)) // 1024, 'kB')


def onda(riga, r):
    """La riga di profondità dell'ultima fila, ammorbidita e riportata fra 0 e 1: l'orlo ondeggia senza punte."""
    s = sfoca1(riga[None, :].astype(np.float32), r, 1)[0]
    return (s - s.min()) / (s.max() - s.min() + 1e-6)


def pieghe_di(rng, da, a, passo):
    """I confini (incavi) delle pieghe, irregolari, e per ogni piega profondità e asimmetria."""
    b, x = [], da
    while x < a:
        b.append(x)
        x += rng.uniform(*passo)
    b = np.array(b, np.float32)
    return b, rng.uniform(0.40, 0.58, len(b)).astype(np.float32), rng.uniform(-1, 1, len(b)).astype(np.float32)


def profilo(u, bordi, amp, storta):
    """Profondità (relativa 0–1 e in unità di u) delle pieghe nei punti u."""
    i = np.clip(np.searchsorted(bordi, u) - 1, 0, len(bordi) - 2)
    a, b = bordi[i], bordi[i + 1]
    t = np.clip((u - a) / (b - a), 0, 1)
    t = t + 0.16 * storta[i] * np.sin(2 * np.pi * t) / (2 * np.pi) * 2
    zn = np.maximum(np.sin(np.pi * np.clip(t, 0, 1)), 0) ** 0.8
    return zn, zn * amp[i] * (b - a)


# ───────────────────────── il telo intero ─────────────────────────
def telo():
    W, H = 2560, 1440
    rng = np.random.default_rng(1958)
    bordi, amp, storta = pieghe_di(rng, -70, W + 400, (108, 178))
    fase = rng.uniform(0, 2 * np.pi, len(bordi)).astype(np.float32)
    xs = np.arange(W, dtype=np.float32)
    z = np.zeros((H, W), np.float32)
    zn = np.zeros((H, W), np.float32)
    for y in range(H):
        yn = y / H
        # le pieghe si allargano verso il fondo e ondeggiano appena; sotto il bastone sono strette e basse
        by = bordi + (bordi - W * 0.45) * 0.045 * yn + 8 * np.sin(2.2 * np.pi * yn + fase) * yn ** 1.3
        n, zz = profilo(xs, by, amp, storta)
        alto = 0.5 + 0.5 * liscia(0.0, 0.13, yn)
        zn[y], z[y] = n, zz * alto
    b, dif = luce(z, zn)
    tr, chiazze = trama(rng, (H, W))
    b = b * tr * (1 + 0.10 * chiazze * dif)
    yy, xx = np.mgrid[0:H, 0:W].astype(np.float32)
    # l'ombra del bastone e della fettuccia in cima, il fondo appena più scuro
    b *= 0.42 + 0.58 * liscia(0, 120, yy)
    b *= 1 - 0.16 * liscia(H - 260, H, yy)
    # l'orlo: i colmi delle pieghe (più vicini) scendono un po' più in basso degli incavi, in un'onda morbida
    orlo = H - 30 - 18 * onda(zn[-1], 18)[None, :]
    b = np.where((yy > orlo - 74) & (yy < orlo - 71), b * 0.78, b)          # la cucitura dell'orlo
    b = np.where(yy > orlo - 72, b * 0.95, b)
    rgb = scala(np.clip(b, 0, 1.08), VELLUTO)
    base = np.clip(0.25 + 0.85 * dif * (0.5 + 0.5 * zn), 0, 1)
    g_orlo = (yy > orlo - 22) & (yy < orlo - 3)
    rgb = np.where(g_orlo[..., None], gallone(xx, yy, base), rgb)
    # il bordo libero (a sinistra): l'orlo girato, più scuro, e il gallone verticale
    rgb = np.where((xx < 8)[..., None], rgb * 0.6, rgb)
    g_lato = (xx >= 9) & (xx < 25) & (yy < orlo - 3)
    rgb = np.where(g_lato[..., None], gallone(yy, xx, np.clip(0.45 + 0.4 * liscia(0, 300, yy), 0, 1)), rgb)
    alfa = np.clip(orlo - yy + 0.5, 0, 1)
    salva_webp(rgb, alfa, 'capitonne-tenda-velluto.webp')


# ───────────────────────── la tenda legata ─────────────────────────
def legata(nome, W, H, ty, pieghe_n, seme):
    """La tenda raccolta contro il muro (a destra) e legata a ty (frazione dell'altezza). Il livello va dall'80 % al
    100 % della tela: in coordinate del livello la tenda raccolta parte da 0,25 (85 %), legata si stringe a 0,50 (90 %)."""
    rng = np.random.default_rng(seme)
    yy, xx = np.mgrid[0:H, 0:W].astype(np.float32)
    yn = yy[:, 0] / H
    # il profilo del bordo davanti: dritto dal bastone, poi la pancia del velluto che si gonfia e ricade sopra il
    # cordone, stretto al cordone, poi una linea lunga che si allarga fino a terra
    pts = [(0.00, 0.25), (ty - 0.19, 0.25), (ty - 0.115, 0.236), (ty - 0.062, 0.203), (ty - 0.036, 0.207),
           (ty - 0.016, 0.29), (ty, 0.50), (ty + 0.014, 0.48), (ty + 0.08, 0.37), (ty + 0.20, 0.235), (ty + 0.34, 0.15),
           (1.00, 0.075)]
    L = np.interp(yn, [p for p, _ in pts], [v for _, v in pts]).astype(np.float32)
    L = sfoca1(L[:, None], int(H * 0.009), 0)[:, 0] * W
    R = float(W)
    larg = np.maximum(R - L, 4)
    u = (xx - L[:, None]) / larg[:, None]                 # 0 al bordo davanti, 1 al muro
    bordi, amp, storta = pieghe_di(rng, -0.08, 1.15, (0.075, 0.15))
    bordi = bordi.astype(np.float32)
    zn, zu = profilo(u, bordi, amp, storta)
    z = zu * larg[:, None] * 1.1
    b, dif = luce(z, zn)
    tr, chiazze = trama(rng, (H, W), fine=0.5)
    b = b * tr * (1 + 0.10 * chiazze * dif)
    # il fascio è un cilindro: davanti a sinistra prende la luce, verso il muro va in ombra; il bordo davanti gira via
    b *= (0.80 + 0.30 * np.cos(np.clip(u, 0, 1) * np.pi / 2) ** 0.7) * (1 - 0.38 * liscia(0.82, 1.0, u))
    b *= 0.62 + 0.38 * liscia(0.0, 0.07, u)
    # sopra il cordone il velluto si gonfia verso di noi, sotto c'è l'ombra del cordone e della strozzatura
    yy_n = yy / H
    b *= 1 + 0.14 * np.exp(-((yy_n - (ty - 0.05)) / 0.03) ** 2)
    b *= 1 - 0.50 * np.exp(-((yy_n - (ty + 0.012)) / 0.012) ** 2)
    b *= 0.42 + 0.58 * liscia(0, H * 0.085, yy)
    b *= 1 - 0.16 * liscia(H * 0.82, H, yy)
    orlo = H - H * 0.021 - H * 0.012 * onda(zn[-1], max(2, W // 60))[None, :]
    b = np.where(yy > orlo - H * 0.05, b * 0.95, b)
    rgb = scala(np.clip(b, 0, 1.08), VELLUTO)
    base = np.clip(0.25 + 0.85 * dif * (0.5 + 0.5 * zn), 0, 1)
    s = W / 576.0                                          # le misure dei galloni sul computer, in proporzione
    g_orlo = (yy > orlo - H * 0.0145) & (yy < orlo - H * 0.002)
    rgb = np.where(g_orlo[..., None], gallone(xx / s, yy / s, base), rgb)
    # il gallone del bordo libero segue il profilo della tenda legata
    dx = xx - L[:, None]
    g_lato = (dx >= 2 * s) & (dx < 9 * s) & (yy < orlo - 2)
    rgb = np.where(g_lato[..., None], gallone(yy / s, dx / s, np.clip(0.45 + 0.4 * liscia(0, H * 0.2, yy), 0, 1)), rgb)
    alfa = np.clip(dx + 0.5, 0, 1) * np.clip(orlo - yy + 0.5, 0, 1)
    salva_webp(rgb, alfa, nome)


# ───────────────────────── il cordone ─────────────────────────
def bezier(p0, p1, p2, p3, n):
    t = np.linspace(0, 1, n)[:, None]
    p0, p1, p2, p3 = (np.array(p, float) for p in (p0, p1, p2, p3))
    pts = (1 - t) ** 3 * p0 + 3 * (1 - t) ** 2 * t * p1 + 3 * (1 - t) * t ** 2 * p2 + t ** 3 * p3
    d = 3 * (1 - t) ** 2 * (p1 - p0) + 6 * (1 - t) * t * (p2 - p1) + 3 * t ** 2 * (p3 - p2)
    return pts, np.degrees(np.arctan2(d[:, 1], d[:, 0]))


def cordone(nome, vw, vh, curva, spes, nodo):
    """Il cordone ritorto: un tubo d'oro scuro e sopra i capi in diagonale, uno dopo l'altro, con la luce in alto; il
    nodo davanti, da cui pende la nappa. curva: i quattro punti della Bézier (dal nodo verso il muro)."""
    d = 'M%.1f %.1f C%.1f %.1f %.1f %.1f %.1f %.1f' % tuple(v for p in curva for v in p)
    pts, ang = bezier(*curva, n=400)
    lung = float(np.sum(np.hypot(*np.diff(pts, axis=0).T)))
    n = int(lung / (spes * 0.46))
    idx = np.linspace(0, len(pts) - 1, n).astype(int)
    capi = []
    for j, i in enumerate(idx):
        x, y = pts[i]
        capi.append('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" transform="rotate(%.1f %.1f %.1f)" fill="url(#%s)"/>'
                    % (x, y, spes * 0.64, spes * 0.27, ang[i] + 52, x, y, 'ca' if j % 2 else 'cb'))
    nx, ny = nodo
    r = spes * 0.95
    nodo_svg = ('<circle cx="%.1f" cy="%.1f" r="%.1f" fill="url(#nd)"/>' % (nx, ny, r) +
                ''.join('<path d="M%.1f %.1f Q%.1f %.1f %.1f %.1f" stroke="#5A4012" stroke-width="%.1f" fill="none" '
                        'opacity=".55"/>' % (nx - r * 0.8, ny + k * r * 0.42, nx, ny + k * r * 0.42 - r * 0.5, nx + r * 0.8,
                                             ny + k * r * 0.42, spes * 0.07) for k in (-1, 0, 1)) +
                '<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="#FFF3C8" opacity=".55"/>'
                % (nx - r * 0.3, ny - r * 0.45, r * 0.32, r * 0.18))
    svg = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d">
<defs>
<linearGradient id="ca" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFF0BC"/><stop offset=".35" stop-color="#E2BF6A"/><stop offset=".75" stop-color="#9C7428"/><stop offset="1" stop-color="#4E360C"/></linearGradient>
<linearGradient id="cb" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F6DE97"/><stop offset=".4" stop-color="#C9A24A"/><stop offset=".8" stop-color="#7E5A1C"/><stop offset="1" stop-color="#3E2A08"/></linearGradient>
<radialGradient id="nd" cx=".38" cy=".32" r=".75"><stop offset="0" stop-color="#FCEBB0"/><stop offset=".45" stop-color="#CFA650"/><stop offset="1" stop-color="#5A4012"/></radialGradient>
<filter id="om" x="-20%%" y="-50%%" width="140%%" height="220%%"><feGaussianBlur stdDeviation="%.1f"/></filter>
<mask id="tubo" maskUnits="userSpaceOnUse"><path d="%s" stroke="#fff" stroke-width="%.1f" fill="none" stroke-linecap="round"/></mask>
</defs>
<path d="%s" stroke="#000" stroke-opacity=".5" stroke-width="%.1f" fill="none" stroke-linecap="round" transform="translate(0 %.1f)" filter="url(#om)"/>
<path d="%s" stroke="#4A330B" stroke-width="%.1f" fill="none" stroke-linecap="round"/>
<g mask="url(#tubo)">%s</g>
<path d="%s" stroke="#FFF6D8" stroke-opacity=".28" stroke-width="%.1f" fill="none" stroke-linecap="round" transform="translate(0 %.1f)"/>
%s
</svg>''' % (vw, vh, spes * 0.28, d, spes * 0.94, d, spes * 1.1, spes * 0.42, d, spes * 1.02, ''.join(capi), d,
             spes * 0.12, -spes * 0.24, nodo_svg)
    with open(os.path.join(OUT, nome), 'w', encoding='utf-8', newline='\n') as f:
        f.write(svg)
    print(nome, len(svg) // 1024, 'kB')


# ───────────────────────── la nappa ─────────────────────────
def nappa():
    rng = np.random.default_rng(7)
    oro = ['#FBE7A6', '#EBCB78', '#D9B45C', '#C9A24A', '#B48C38', '#9A7428', '#7E5C1C']
    # il cappio: due capi ritorti dal nodo alla testa
    cappio = []
    for k in range(13):
        y = 4 + k * 5.4
        cappio.append('<ellipse cx="80" cy="%.1f" rx="6.4" ry="3.9" transform="rotate(%d 80 %.1f)" fill="url(#%s)"/>'
                      % (y, 30 if k % 2 else -30, y, 'ca' if k % 2 else 'cb'))
    # la testa fasciata di filo: righe che seguono la curva della testa
    fasce = []
    def mezza(y):          # la mezza larghezza della testa all'altezza y
        return float(np.interp(y, [72, 84, 104, 126, 146, 162, 172, 186], [8, 15, 30, 33, 26, 20, 23, 31]))
    for y in np.arange(74, 186, 2.3):
        hw = mezza(y)
        fasce.append('<path d="M%.1f %.1f Q80 %.1f %.1f %.1f" stroke="%s" stroke-width="1.5" fill="none" opacity=".55"/>'
                     % (80 - hw, y, y + hw * 0.16, 80 + hw, y, ['#6E4E14', '#FFF0C0', '#A88232'][int(y) % 3]))
    sagoma = ' '.join('%.1f %.1f' % (80 - mezza(y), y) for y in np.arange(72, 187, 4))
    sagoma2 = ' '.join('%.1f %.1f' % (80 + mezza(y), y) for y in np.arange(186, 71, -4))
    testa = 'M80 70 L%s L%s Z' % (sagoma, sagoma2)
    # la collana di riccioli sulla vita della testa
    riccioli = ''.join('<circle cx="%.1f" cy="%.1f" r="3.6" fill="url(#ri)" stroke="#5E4212" stroke-width=".6"/>'
                       % (80 + 21 * math.sin(a), 166 + 2.2 * math.cos(a), ) for a in np.linspace(-1.35, 1.35, 11))
    # la gonna: fili dalla corona alla frangia, quelli dietro più scuri, davanti più chiari
    fili = []
    ciuffi = np.sort(rng.uniform(0.03, 0.97, 11))      # i fili vanno a ciuffi, non a pettine
    for strato, (n, lum) in enumerate(((70, 0.2), (80, 0.55), (60, 0.85))):
        for k in range(n):
            f = float(np.clip(rng.choice(ciuffi) + rng.normal(0, 0.04), 0, 1))
            x0 = 80 + (f - 0.5) * 58
            x1 = 80 + (f - 0.5) * 82 + rng.normal(0, 1.6)
            y1 = 452 + rng.uniform(-7, 9)
            cx = (x0 + x1) / 2 + rng.normal(0, 2.2)
            lato = abs(f - 0.42) * 2                       # la luce viene da sinistra: il bordo destro più scuro
            v = np.clip(lum - 0.45 * lato ** 1.5 + rng.normal(0, 0.1), 0, 1)
            col = oro[int(round((1 - v) * (len(oro) - 1)))]
            fili.append('<path d="M%.1f 190 Q%.1f %.1f %.1f %.1f" stroke="%s" stroke-width="%.2f" fill="none" '
                        'stroke-linecap="round"/>' % (x0, cx, (190 + y1) / 2, x1, y1, col, rng.uniform(1.5, 2.4)))
    svg = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 470">
<style>
.dondola{transform-origin:80px 2px;animation:d 6s ease-in-out infinite}
.gonna{transform-origin:80px 190px;animation:g 6s ease-in-out infinite;animation-delay:-.8s}
@keyframes d{0%%,100%%{transform:rotate(-1.3deg)}50%%{transform:rotate(1.3deg)}}
@keyframes g{0%%,100%%{transform:rotate(-1.1deg) skewX(.8deg)}50%%{transform:rotate(1.1deg) skewX(-.8deg)}}
@media (prefers-reduced-motion:reduce){.dondola,.gonna{animation:none}}
</style>
<defs>
<linearGradient id="ca" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFF0BC"/><stop offset=".45" stop-color="#D9B45C"/><stop offset="1" stop-color="#5A4012"/></linearGradient>
<linearGradient id="cb" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F3D88E"/><stop offset=".5" stop-color="#B48C38"/><stop offset="1" stop-color="#3E2A08"/></linearGradient>
<linearGradient id="cil" x1="0" x2="1"><stop offset="0" stop-color="#4A330B"/><stop offset=".2" stop-color="#B48C38"/><stop offset=".38" stop-color="#FCE9AE"/><stop offset=".55" stop-color="#D2AC56"/><stop offset=".85" stop-color="#7A561A"/><stop offset="1" stop-color="#3A2706"/></linearGradient>
<linearGradient id="omb" x1="0" x2="1"><stop offset="0" stop-color="#000" stop-opacity=".45"/><stop offset=".22" stop-color="#000" stop-opacity="0"/><stop offset=".5" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".6"/></linearGradient>
<linearGradient id="giu" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#000" stop-opacity=".5"/><stop offset=".12" stop-color="#000" stop-opacity="0"/></linearGradient>
<linearGradient id="sfm" x1="0" y1="0" x2="0" y2="1"><stop offset=".8" stop-color="#fff"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>
<mask id="sf" maskUnits="userSpaceOnUse" x="0" y="180" width="160" height="270"><rect x="0" y="186" width="160" height="260" fill="url(#sfm)"/></mask>
<radialGradient id="ri" cx=".35" cy=".3" r=".8"><stop offset="0" stop-color="#FFF1C4"/><stop offset=".5" stop-color="#C9A24A"/><stop offset="1" stop-color="#5E4212"/></radialGradient>
<clipPath id="gc"><path d="M50 188 L110 188 L120 444 L40 444 Z"/></clipPath>
</defs>
<g class="dondola">
%s
<g class="gonna">
%s
<path d="M50 188 L110 188 L120 444 L40 444 Z" fill="url(#omb)" mask="url(#sf)"/>
<path d="M50 188 L110 188 L120 444 L40 444 Z" fill="url(#giu)"/>
</g>
<path d="%s" fill="url(#cil)"/>
<g clip-path="url(#tc)">%s</g>
<clipPath id="tc"><path d="%s"/></clipPath>
%s
<rect x="47" y="182" width="66" height="9" rx="3" fill="url(#cil)"/>
<path d="M47 186.5 H113" stroke="#5A4012" stroke-width=".8" opacity=".6"/>
<ellipse cx="66" cy="112" rx="6" ry="15" fill="#FFF6D8" opacity=".35"/>
</g>
</svg>''' % (''.join(cappio), ''.join(fili), testa, ''.join(fasce), testa, riccioli)
    with open(os.path.join(OUT, 'capitonne-nappa-2.svg'), 'w', encoding='utf-8', newline='\n') as f:
        f.write(svg)
    print('capitonne-nappa-2.svg', len(svg) // 1024, 'kB')


if __name__ == '__main__':
    quali = sys.argv[2:] or ['telo', 'legata', 'cordone', 'nappa']
    if 'telo' in quali:
        telo()
    if 'legata' in quali:
        legata('capitonne-tenda-legata.webp', 576, 1620, 0.50, 9, 11)
        legata('capitonne-tenda-legata-tel.webp', 192, 2100, 0.55, 9, 12)
    if 'cordone' in quali:
        # computer: livello dall'88,5 % al 100 % della tela, alto 6 % (viewBox 660 × 195); la tenda legata ha il bordo
        # davanti al 90 % (86 nel viewBox): il nodo sta lì, il cordone gira davanti e sparisce verso il muro
        cordone('capitonne-cordone.svg', 660, 195, [(86, 97), (220, 134), (460, 130), (660, 88)], 40, (86, 97))
        # telefono: stesse proporzioni orizzontali, cordone più grosso (viewBox 330 × 120, bordo davanti a 43)
        cordone('capitonne-cordone-tel.svg', 330, 120, [(43, 60), (115, 84), (235, 82), (330, 52)], 36, (43, 60))
    if 'nappa' in quali:
        nappa()
