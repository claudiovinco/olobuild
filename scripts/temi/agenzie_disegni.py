# -*- coding: utf-8 -*-
"""I disegni delle aperture dei cinque temi delle agenzie (20 nov 2026).

- Affisso: il cartellone a prismi (trivision) — la struttura (computer e telefono, SVG) e le tre facce disegnate con
  Chrome nei caratteri del tema, tagliate in otto strisce ciascuna (affisso-faccia-<a|b|c>-<0..7>.jpg): nell'apertura
  ogni striscia è un livello che gira su sé stessa.
- Diaframma: le sette lamelle con il foro al centro (SVG): nell'apertura il livello si ingrandisce e gira, il foro
  si allarga e scopre il video.
- Pollice: il telefono col vetro vuoto (SVG): sotto, il livello video verticale.
- Strappo: tre manifesti disegnati con Chrome e strappati in due metà col bordo frastagliato e l'anima bianca della
  carta (strappo-<1|2|3>-<s|d>.png, e -t- per il telefono).
- Torchio: i due rulli inchiostrati, nero e rosso (SVG verticali).

Uso: python agenzie_disegni.py <cartella dei media>
"""
import asyncio
import base64
import io
import math
import os
import random
import sys

from PIL import Image, ImageDraw, ImageFilter
from playwright.async_api import async_playwright

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'
MEDIA = OUT


def scrivi(nome, testo):
    with open(os.path.join(OUT, nome), 'w', encoding='utf-8') as f:
        f.write(testo)


def foto64(nome, lato=1400):
    im = Image.open(os.path.join(MEDIA, nome)).convert('RGB')
    im.thumbnail((lato, lato))
    b = io.BytesIO()
    im.save(b, 'JPEG', quality=86)
    return 'data:image/jpeg;base64,' + base64.b64encode(b.getvalue()).decode()


# ───────────────────────── Affisso ─────────────────────────
# Il pannello è 2:1 (un 6 × 3 metri). Struttura del computer: viewBox 703 × 717, pannello a (20, 40) 662 × 331, le
# lampade sopra, le due gambe fino in fondo. Del telefono: viewBox 603 × 468, pannello a (20, 40) 563 × 281.
def cartellone(w, h, pw, ph):
    x0, y0 = 20, 40
    gx = (int(w * 0.24), int(w * 0.76))
    lamp = ''.join('<g transform="translate(%d 0)"><rect x="-2" y="6" width="4" height="30" fill="#2A2A2A"/>'
                   '<path d="M-16 6 h32 l-6 10 h-20 Z" fill="#1E1E1E"/><rect x="-10" y="15" width="20" height="3" fill="#FFF3C4"/>'
                   '</g>' % int(x0 + pw * f) for f in (0.18, 0.5, 0.82))
    gambe = ''.join('<rect x="%d" y="%d" width="22" height="%d" fill="url(#acc)"/>'
                    '<rect x="%d" y="%d" width="22" height="6" fill="#0A0A0A" opacity=".6"/>' % (g - 11, y0 + ph + 14, h - y0 - ph - 14,
                                                                                         g - 11, y0 + ph + 14) for g in gx)
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}">'
            '<defs><linearGradient id="acc" x1="0" x2="1"><stop offset="0" stop-color="#2B2B2B"/><stop offset=".5" stop-color="#4A4A48"/>'
            '<stop offset="1" stop-color="#1C1C1C"/></linearGradient>'
            '<linearGradient id="cor" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3A3A38"/><stop offset="1" stop-color="#161616"/>'
            '</linearGradient></defs>'
            '{gambe}{lamp}'
            '<path fill-rule="evenodd" fill="url(#cor)" d="M{a} {b} h{c} v{d} h-{c} Z M{x0} {y0} h{pw} v{ph} h-{pw} Z"/>'
            '<rect x="{a}" y="{b}" width="{c}" height="{d}" fill="none" stroke="#555" stroke-width="1.5"/>'
            '<rect x="{a2}" y="{e}" width="{c2}" height="8" fill="#101010"/>'
            '</svg>').format(w=w, h=h, gambe=gambe, lamp=lamp, a=x0 - 14, b=y0 - 12, c=pw + 28, d=ph + 26, x0=x0, y0=y0, pw=pw, ph=ph,
                             a2=x0 - 6, e=y0 + ph + 14, c2=pw + 12)


GRANA = ('<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'300\' height=\'300\'><filter id=\'n\'><feTurbulence type=\'fractalNoise\' '
         'baseFrequency=\'.85\' numOctaves=\'2\' seed=\'4\'/><feColorMatrix values=\'0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 .16 0\'/>'
         '</filter><rect width=\'300\' height=\'300\' filter=\'url(%23n)\'/></svg>')


def faccia_html(corpo, fondo, w=1324, h=662):
    return ('<html><head><link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Schibsted+Grotesk:wght@500;700'
            '&display=block" rel="stylesheet"><style>*{margin:0;box-sizing:border-box}body{width:%dpx;height:%dpx;overflow:hidden}'
            '#f{position:relative;width:%dpx;height:%dpx;%s;overflow:hidden}'
            '#f:after{content:"";position:absolute;inset:0;background:url("data:image/svg+xml;utf8,%s")}'
            '.big{font-family:"Archivo Black";line-height:.86;letter-spacing:-.03em}'
            '.sm{font-family:"Schibsted Grotesk";font-weight:700;letter-spacing:.08em;text-transform:uppercase}'
            '</style></head><body><div id="f">%s</div></body></html>') % (w, h, w, h, fondo, GRANA, corpo)


FACCE = {
    'a': ('background:#FFD21F;color:#111',
          '<div class="sm" style="position:absolute;left:56px;top:52px;font-size:30px">Qui ci sta</div>'
          '<div class="big" style="position:absolute;left:50px;top:104px;font-size:228px">il tuo<br>nome.</div>'
          '<div class="sm" style="position:absolute;left:58px;bottom:44px;font-size:22px">6 × 3 metri · Via Marina · uscita 47</div>'
          '<svg style="position:absolute;right:60px;bottom:52px" width="190" height="190" viewBox="0 0 100 100">'
          '<path d="M10 50 H78 M54 24 L82 50 L54 76" fill="none" stroke="#111" stroke-width="14" stroke-linecap="square"/></svg>'),
    'b': ('background:#111;color:#fff',
          '<img src="FOTO" style="position:absolute;right:0;top:0;width:48%;height:100%;object-fit:cover;object-position:50% 60%">'
          '<div style="position:absolute;left:0;top:0;width:52%;height:100%;background:#111"></div>'
          '<div class="sm" style="position:absolute;left:56px;top:56px;font-size:24px;color:#FFD21F">Tangenziale · Fuorigrotta</div>'
          '<div class="big" style="position:absolute;left:50px;top:130px;font-size:108px">Ti vedono<br><span style="color:#FFD21F">tutti.</span></div>'
          '<div class="sm" style="position:absolute;left:58px;bottom:48px;font-size:22px;opacity:.86">Novantamila auto al giorno</div>'),
    'c': ('background:#E5322D;color:#fff',
          '<div class="big" style="position:absolute;left:46px;top:70px;font-size:290px">Affisso.</div>'
          '<div class="sm" style="position:absolute;left:56px;bottom:58px;font-size:30px;line-height:1.35">Dal 1987 i muri di Napoli<br>'
          'parlano con la nostra colla.</div>'
          '<div style="position:absolute;right:56px;bottom:52px;width:150px;height:150px;border-radius:50%;background:#FFD21F;'
          'color:#111;display:flex;align-items:center;justify-content:center;text-align:center" class="sm">'
          '<span style="font-size:22px;line-height:1.2">Prenota<br>la tua<br>uscita</span></div>'),
}


# ───────────────────────── Diaframma ─────────────────────────
def iride():
    cx, cy, r0, D = 800, 450, 64, 5000
    v = [(cx + r0 * math.cos(math.radians(-90 + k * 360 / 7)), cy + r0 * math.sin(math.radians(-90 + k * 360 / 7))) for k in range(7)]
    u = []
    for k in range(7):
        a, b = v[k], v[(k + 1) % 7]
        ln = math.hypot(b[0] - a[0], b[1] - a[1])
        u.append(((b[0] - a[0]) / ln, (b[1] - a[1]) / ln))
    lame = []
    for k in range(7):
        a, b = v[k], v[(k + 1) % 7]
        pb = (b[0] + D * u[k][0], b[1] + D * u[k][1])
        pa = (a[0] + D * u[k - 1][0], a[1] + D * u[k - 1][1])
        lame.append('<path d="M%.1f %.1f L%.1f %.1f L%.1f %.1f L%.1f %.1f Z" fill="url(#l%d)" stroke="#4A4038" stroke-width="2.2"/>'
                    % (a[0], a[1], b[0], b[1], pb[0], pb[1], pa[0], pa[1], k % 2))
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 900">'
            '<defs><radialGradient id="l0" cx="800" cy="450" r="900" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#2A241F"/>'
            '<stop offset=".25" stop-color="#16120F"/><stop offset="1" stop-color="#070605"/></radialGradient>'
            '<radialGradient id="l1" cx="800" cy="450" r="900" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#332B24"/>'
            '<stop offset=".25" stop-color="#1B1612"/><stop offset="1" stop-color="#090807"/></radialGradient></defs>'
            + ''.join(lame) +
            '<circle cx="800" cy="450" r="430" fill="none" stroke="#FF9F2E" stroke-opacity=".22" stroke-width="1.5"/>'
            '<circle cx="800" cy="450" r="436" fill="none" stroke="#FFF8EE" stroke-opacity=".08" stroke-width="8"/>'
            '</svg>')


# ───────────────────────── Pollice ─────────────────────────
# Il telefono: viewBox 400 × 820, vetro (buco) da (14, 14) a (386, 806), raggio 44.
def telefono():
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 820">'
            '<rect x="-3" y="170" width="6" height="64" rx="3" fill="#2B2B2B"/><rect x="-3" y="250" width="6" height="64" rx="3" fill="#2B2B2B"/>'
            '<rect x="397" y="210" width="6" height="96" rx="3" fill="#2B2B2B"/>'
            '<path fill-rule="evenodd" fill="#121212" d="M58 0 H342 A58 58 0 0 1 400 58 V762 A58 58 0 0 1 342 820 H58 A58 58 0 0 1 0 762 V58 '
            'A58 58 0 0 1 58 0 Z M58 14 A44 44 0 0 0 14 58 V762 A44 44 0 0 0 58 806 H342 A44 44 0 0 0 386 762 V58 A44 44 0 0 0 342 14 Z"/>'
            '<path fill="none" stroke="#3A3A3A" stroke-width="2" d="M58 1 H342 A57 57 0 0 1 399 58 V762 A57 57 0 0 1 342 819 H58 A57 57 0 0 1 1 762 V58 A57 57 0 0 1 58 1 Z"/>'
            '<rect x="148" y="28" width="104" height="30" rx="15" fill="#050505"/>'
            '<rect x="150" y="788" width="100" height="5" rx="2.5" fill="#FFFFFF" opacity=".75"/>'
            '</svg>')


# ───────────────────────── Strappo ─────────────────────────
def manifesto_html(corpo, fondo, w, h):
    return ('<html><head><link href="https://fonts.googleapis.com/css2?family=Libre+Caslon+Display&family=Onest:wght@500;700'
            '&display=block" rel="stylesheet"><style>*{margin:0;box-sizing:border-box}body{width:%dpx;height:%dpx;overflow:hidden}'
            '#f{position:relative;width:%dpx;height:%dpx;%s;overflow:hidden}'
            '#f:after{content:"";position:absolute;inset:0;background:url("data:image/svg+xml;utf8,%s")}'
            '.big{font-family:"Libre Caslon Display";line-height:.9;letter-spacing:-.02em}'
            '.sm{font-family:"Onest";font-weight:700;letter-spacing:.14em;text-transform:uppercase}'
            '</style></head><body><div id="f">%s</div></body></html>') % (w, h, w, h, fondo, GRANA, corpo)


def manifesti(t):
    """I tre manifesti, per il computer (t False, 1440 × 810) o per il telefono (640 × 1400)."""
    if not t:
        return [
            ('background:#EFEBE3;color:#141414',
             '<div class="sm" style="position:absolute;left:84px;top:150px;font-size:18px;color:#C8145F">Art direction · Milano · dal 2011</div>'
             '<div class="big" style="position:absolute;left:70px;top:200px;font-size:330px">Strappo</div>'
             '<div style="position:absolute;left:84px;bottom:120px;width:520px;height:2px;background:#141414"></div>'
             '<div class="sm" style="position:absolute;left:84px;bottom:76px;font-size:16px">Campagne · identità · fotografia · set</div>'
             '<div class="big" style="position:absolute;right:90px;bottom:70px;font-size:120px;color:#C8145F">N.° 1</div>'),
            ('background:#C8145F;color:#fff',
             '<img src="FOTO2" style="position:absolute;right:120px;top:120px;width:470px;height:570px;object-fit:cover;transform:rotate(3deg)">'
             '<div class="sm" style="position:absolute;left:84px;top:150px;font-size:18px">Le regole</div>'
             '<div class="big" style="position:absolute;left:76px;top:210px;font-size:190px">Prima<br>si imparano,</div>'
             '<div class="sm" style="position:absolute;left:84px;bottom:90px;font-size:16px">N.° 2 · Via Tortona 31</div>'),
            ('background:#141414;color:#EFEBE3',
             '<div class="big" style="position:absolute;left:76px;top:210px;font-size:250px">poi si<br>strappano<span style="color:#F0287A">.</span></div>'
             '<div class="sm" style="position:absolute;right:90px;top:150px;font-size:18px;color:#F7C948">N.° 3</div>'),
        ]
    return [
        ('background:#EFEBE3;color:#141414',
         '<div class="sm" style="position:absolute;left:48px;top:250px;font-size:20px;color:#C8145F">Art direction · Milano</div>'
         '<div class="big" style="position:absolute;left:40px;top:310px;font-size:196px">Strap<br>po</div>'
         '<div style="position:absolute;left:48px;top:760px;width:300px;height:2px;background:#141414"></div>'
         '<div class="sm" style="position:absolute;left:48px;top:790px;font-size:17px;line-height:1.6">Campagne · identità<br>fotografia · set</div>'
         '<div class="big" style="position:absolute;right:52px;top:1010px;font-size:110px;color:#C8145F">N.° 1</div>'),
        ('background:#C8145F;color:#fff',
         '<div class="sm" style="position:absolute;left:48px;top:250px;font-size:20px">Le regole</div>'
         '<div class="big" style="position:absolute;left:42px;top:310px;font-size:130px">Prima<br>si impa<br>rano,</div>'
         '<img src="FOTO2" style="position:absolute;left:150px;top:760px;width:400px;height:440px;object-fit:cover;transform:rotate(3deg)">'),
        ('background:#141414;color:#EFEBE3',
         '<div class="sm" style="position:absolute;right:52px;top:250px;font-size:20px;color:#F7C948">N.° 3</div>'
         '<div class="big" style="position:absolute;left:42px;top:330px;font-size:150px">poi si<br>strap<br>pano<span style="color:#F0287A">.</span></div>'),
    ]


def strappa(im, seme, verticale=True):
    """Divide il manifesto in due metà con lo strappo frastagliato e l'anima bianca della carta lungo il bordo."""
    rnd = random.Random(seme)
    w, h = im.size
    mid = w * (0.5 + rnd.uniform(-0.04, 0.04))
    punti = []
    ph = [rnd.uniform(0, 6.28) for _ in range(3)]
    for y in range(0, h + 7, 7):
        x = mid + 34 * math.sin(y / 170 + ph[0]) + 14 * math.sin(y / 53 + ph[1]) + 6 * math.sin(y / 19 + ph[2]) + rnd.uniform(-6, 6)
        punti.append((x, min(y, h)))
    sin_ = Image.new('L', (w, h), 0)
    ImageDraw.Draw(sin_).polygon([(0, 0)] + punti + [(0, h)], fill=255)
    des = Image.new('L', (w, h), 0)
    ImageDraw.Draw(des).polygon([(w, 0)] + punti + [(w, h)], fill=255)
    # l'anima bianca: una fascia di 3–11 px lungo lo strappo, più larga sulla metà destra
    anima_s = Image.new('L', (w, h), 0)
    anima_d = Image.new('L', (w, h), 0)
    fs = [(x - rnd.uniform(2, 6), y) for x, y in punti]
    fd = [(x + rnd.uniform(4, 11), y) for x, y in punti]
    ImageDraw.Draw(anima_s).polygon(punti + fs[::-1], fill=255)
    ImageDraw.Draw(anima_d).polygon(punti + fd[::-1], fill=255)
    carta = Image.new('RGB', (w, h), (246, 243, 235))
    rumore = Image.effect_noise((w, h), 26).convert('RGB')
    carta = Image.blend(carta, rumore, 0.12)
    meta = []
    for m, an in ((sin_, anima_s), (des, anima_d)):
        rgba = im.convert('RGBA')
        rgba = Image.composite(carta.convert('RGBA'), rgba, an.filter(ImageFilter.GaussianBlur(0.6)))
        a = m.filter(ImageFilter.GaussianBlur(0.7))
        rgba.putalpha(a)
        bbox = a.getbbox()
        meta.append((rgba.crop(bbox), bbox))
    return meta


# ───────────────────────── Torchio ─────────────────────────
def rullo(inchiostro, riflesso):
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 1000">'
            '<defs><linearGradient id="g" x1="0" x2="1"><stop offset="0" stop-color="{i}"/><stop offset=".28" stop-color="{r}"/>'
            '<stop offset=".42" stop-color="{i}"/><stop offset="1" stop-color="#050505"/></linearGradient>'
            '<linearGradient id="m" x1="0" x2="1"><stop offset="0" stop-color="#6B6B6B"/><stop offset=".4" stop-color="#D9D9D6"/>'
            '<stop offset="1" stop-color="#3A3A3A"/></linearGradient></defs>'
            '<rect x="70" y="0" width="20" height="1000" fill="url(#m)"/>'
            '<rect x="22" y="40" width="116" height="920" rx="14" fill="url(#g)"/>'
            '<rect x="22" y="40" width="116" height="920" rx="14" fill="none" stroke="#000" stroke-opacity=".5" stroke-width="2"/>'
            '<ellipse cx="80" cy="44" rx="58" ry="10" fill="url(#m)"/><ellipse cx="80" cy="956" rx="58" ry="10" fill="url(#m)"/>'
            '<circle cx="80" cy="44" r="7" fill="#222"/><circle cx="80" cy="956" r="7" fill="#222"/>'
            '</svg>').format(i=inchiostro, r=riflesso)


async def disegna(pg, html, w, h, dest, png=False):
    await pg.set_viewport_size({'width': w, 'height': h})
    await pg.set_content(html)
    await pg.wait_for_timeout(1500)
    await pg.screenshot(path=dest, clip={'x': 0, 'y': 0, 'width': w, 'height': h}, type='png')
    return Image.open(dest).convert('RGB')


async def main():
    scrivi('affisso-cartellone.svg', cartellone(703, 717, 662, 331))
    scrivi('affisso-cartellone-t.svg', cartellone(603, 468, 563, 281))
    scrivi('diaframma-iride.svg', iride())
    scrivi('pollice-telefono.svg', telefono())
    scrivi('torchio-rullo.svg', rullo('#151515', '#5A5A5A'))
    scrivi('torchio-rullo-rosso.svg', rullo('#8E1A14', '#E0645C'))
    tmp = os.path.join(OUT, '_tmp.png')
    async with async_playwright() as p:
        b = await p.chromium.launch()
        pg = await b.new_page(device_scale_factor=1)
        napoli = foto64('pexels-10045295.jpg')
        for k, (fondo, corpo) in FACCE.items():
            im = await disegna(pg, faccia_html(corpo.replace('FOTO', napoli), fondo), 1324, 662, tmp)
            im.save(os.path.join(OUT, 'affisso-faccia-%s.jpg' % k), quality=88)
            for s in range(8):
                x0, x1 = round(s * 1324 / 8), round((s + 1) * 1324 / 8)
                im.crop((x0, 0, x1, 662)).save(os.path.join(OUT, 'affisso-faccia-%s-%d.jpg' % (k, s)), quality=88)
        collage = foto64('pexels-19780748.jpg')
        for t, (w, h), suf in ((False, (1440, 810), ''), (True, (640, 1400), '-t')):
            for n, (fondo, corpo) in enumerate(manifesti(t), 1):
                im = await disegna(pg, manifesto_html(corpo.replace('FOTO2', collage), fondo, w, h), w, h, tmp)
                im.save(os.path.join(OUT, '_strappo-%d%s.jpg' % (n, suf)), quality=90)
                for (metà, bbox), lato in zip(strappa(im, n * 7 + (3 if t else 0)), 'sd'):
                    metà.save(os.path.join(OUT, 'strappo-%d%s-%s.png' % (n, suf, lato)), optimize=True)
                    print('strappo-%d%s-%s.png' % (n, suf, lato), bbox, '%.2f%%' % (bbox[0] / w * 100), '%.2f%%' % ((bbox[2] - bbox[0]) / w * 100))
        await b.close()
    os.remove(tmp)


if __name__ == '__main__':
    asyncio.run(main())
