# -*- coding: utf-8 -*-
"""I disegni delle aperture dei dieci temi dei mestieri (10 nov 2026): SVG (alcuni animati dentro, con CSS: partono
quando il livello si mostra, perché lo Slider Pro li fa ripartire cambiando stato) e qualche PNG/JPG fatto a mano con
PIL (mattoni, erba, fuliggine, lastra di legno).

Uso: python mestieri_disegni.py <cartella dei media>
Tutti i file hanno il prefisso del tema (morsetto-…, sifone-…): sono pezzi della libreria come le foto, e nell'editor
si cambiano come ogni immagine.
"""
import math
import os
import random
import sys

import numpy as np
from PIL import Image, ImageDraw, ImageFilter, ImageFont

OUT = sys.argv[1] if len(sys.argv) > 1 else '.'
os.makedirs(OUT, exist_ok=True)


def salva(nome, svg):
    with open(os.path.join(OUT, nome), 'w', encoding='utf-8', newline='\n') as f:
        f.write(svg.strip() + '\n')


def svg(vb, corpo, stile='', extra=''):
    return ('<svg xmlns="http://www.w3.org/2000/svg" viewBox="%s"%s>' % (vb, extra)
            + ('<style>%s</style>' % stile if stile else '') + corpo + '</svg>')


# ───────────────────────── MORSETTO (elettricista) ─────────────────────────
def morsetto():
    # il salvavita: un interruttore modulare da quadro, la leva giù (spento) o su (acceso)
    def interruttore(su):
        leva_y = 112 if su else 188
        spia = '#3DDC84' if su else '#E5484D'
        testo = 'I · ON' if su else '0 · OFF'
        alone = ('<circle cx="100" cy="262" r="20" fill="#3DDC84" opacity=".35" filter="url(#b)"/>' if su else '')
        return svg('0 0 200 360', '''
<defs>
 <linearGradient id="c" x1="0" x2="1"><stop offset="0" stop-color="#D6DADF"/><stop offset=".45" stop-color="#F4F6F8"/><stop offset="1" stop-color="#C5CAD0"/></linearGradient>
 <linearGradient id="l" x1="0" x2="1"><stop offset="0" stop-color="#20242A"/><stop offset=".5" stop-color="#3A4049"/><stop offset="1" stop-color="#1A1D22"/></linearGradient>
 <linearGradient id="r" y1="0" y2="1"><stop offset="0" stop-color="#9AA1A9"/><stop offset=".5" stop-color="#E3E6EA"/><stop offset="1" stop-color="#7C838B"/></linearGradient>
 <filter id="b"><feGaussianBlur stdDeviation="6"/></filter>
 <filter id="o" x="-20%%" y="-20%%" width="140%%" height="140%%"><feDropShadow dx="0" dy="10" stdDeviation="10" flood-opacity=".55"/></filter>
</defs>
<rect x="-40" y="158" width="280" height="44" fill="url(#r)"/>
<g filter="url(#o)">
 <rect x="20" y="10" width="160" height="340" rx="14" fill="url(#c)"/>
 <rect x="20" y="10" width="160" height="340" rx="14" fill="none" stroke="#9AA0A7" stroke-width="2"/>
 <circle cx="100" cy="34" r="13" fill="#B8BEC5" stroke="#8A9199" stroke-width="2"/><rect x="89" y="31" width="22" height="6" rx="2" fill="#6E757D" transform="rotate(35 100 34)"/>
 <circle cx="100" cy="318" r="15" fill="#B8BEC5" stroke="#8A9199" stroke-width="2"/><rect x="88" y="315" width="24" height="6" rx="2" fill="#6E757D" transform="rotate(-20 100 318)"/>
 <rect x="52" y="78" width="96" height="152" rx="10" fill="#2B3037"/>
 <rect x="58" y="84" width="84" height="140" rx="7" fill="#15181C"/>
 <rect x="66" y="%d" width="68" height="44" rx="8" fill="url(#l)" stroke="#000" stroke-opacity=".5"/>
 <rect x="72" y="%d" width="56" height="6" rx="3" fill="#59616B"/>
 %s
 <rect x="70" y="248" width="60" height="28" rx="5" fill="#101215"/>
 <rect x="76" y="254" width="48" height="16" rx="3" fill="%s"/>
 <text x="100" y="296" font-family="Arial, sans-serif" font-size="13" font-weight="700" fill="#2D3238" text-anchor="middle">%s</text>
 <text x="100" y="68" font-family="Arial, sans-serif" font-size="12" font-weight="700" fill="#3A4047" text-anchor="middle" letter-spacing="1">MORSETTO</text>
 <text x="100" y="342" font-family="Arial, sans-serif" font-size="10" fill="#5A6068" text-anchor="middle">C16 · 6000 · 3</text>
</g>''' % (leva_y, leva_y + 8, alone, spia, testo))
    salva('morsetto-salvavita-giu.svg', interruttore(False))
    salva('morsetto-salvavita-su.svg', interruttore(True))
    # la corrente che sale nei fili, dal salvavita verso l'alto
    fili = [('#6B4A2E', 'M150 400 C150 300 60 260 70 0'), ('#1E1E1E', 'M170 400 C172 290 170 200 172 0'),
            ('#2F66C8', 'M190 400 C190 300 290 250 280 0')]
    corpo = ''.join('<path d="%s" stroke="%s" stroke-width="13" fill="none" stroke-linecap="round"/>' % (d, c) for c, d in fili)
    corpo += ''.join('<path class="f%d" d="%s" stroke="#FFE27A" stroke-width="5" fill="none" stroke-linecap="round" '
                     'stroke-dasharray="18 46" filter="url(#g)"/>' % (i, d) for i, (c, d) in enumerate(fili))
    salva('morsetto-corrente.svg', svg('0 0 340 400', '<defs><filter id="g" x="-50%" y="-50%" width="200%" height="200%">'
                                       '<feGaussianBlur stdDeviation="3" result="s"/><feMerge><feMergeNode in="s"/>'
                                       '<feMergeNode in="SourceGraphic"/></feMerge></filter></defs>' + corpo,
                                       stile='@keyframes c{to{stroke-dashoffset:-640}}.f0,.f1,.f2{animation:c 1.6s linear infinite}'
                                             '.f1{animation-duration:1.3s}.f2{animation-duration:1.9s}'))
    # il lampo all'accensione: raggi che si allargano e svaniscono (una volta)
    raggi = ''.join('<line x1="200" y1="200" x2="%.1f" y2="%.1f" stroke="#FFF3B0" stroke-width="4" stroke-linecap="round"/>' % (
        200 + math.cos(a) * 190, 200 + math.sin(a) * 190) for a in [k * math.pi / 8 for k in range(16)])
    salva('morsetto-lampo.svg', svg('0 0 400 400', '<g class="r">' + raggi + '</g><circle class="n" cx="200" cy="200" r="60" fill="#FFF7D1"/>',
                                    stile='@keyframes r{0%{opacity:1;transform:scale(.2)}100%{opacity:0;transform:scale(1.15)}}'
                                          '@keyframes n{0%{opacity:.95;transform:scale(.3)}100%{opacity:0;transform:scale(2.2)}}'
                                          '.r,.n{transform-origin:200px 200px;transform-box:view-box;opacity:0}'
                                          '.r{animation:r .9s cubic-bezier(.2,.8,.3,1) .05s forwards}.n{animation:n .7s ease-out forwards}'))
    # il buio con la torcia: nero quasi pieno, e un alone morbido attorno al salvavita (al centro)
    salva('morsetto-buio.svg', svg('0 0 2000 2000', '<defs><radialGradient id="t" cx="1000" cy="1000" r="560" gradientUnits="userSpaceOnUse">'
                                   '<stop offset="0" stop-color="#05070B" stop-opacity=".35"/><stop offset=".5" stop-color="#05070B" stop-opacity=".86"/>'
                                   '<stop offset="1" stop-color="#05070B" stop-opacity=".97"/></radialGradient></defs>'
                                   '<rect width="2000" height="2000" fill="url(#t)"/>'))
    # la luce calda della stanza accesa: un alone giallo in alto, sopra il video
    salva('morsetto-luce.svg', svg('0 0 1600 900', '<defs><radialGradient id="l" cx="800" cy="180" r="900" gradientUnits="userSpaceOnUse">'
                                   '<stop offset="0" stop-color="#FFC873" stop-opacity=".34"/><stop offset=".6" stop-color="#FF9F45" stop-opacity=".1"/>'
                                   '<stop offset="1" stop-color="#FF9F45" stop-opacity="0"/></radialGradient></defs><rect width="1600" height="900" fill="url(#l)"/>',
                                   extra=' preserveAspectRatio="none"'))


# ───────────────────────── SIFONE (idraulico) ─────────────────────────
def sifone():
    VB = '0 0 1600 420'
    rame = ('<linearGradient id="cu" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7A3A1A"/><stop offset=".3" stop-color="#E9A06A"/>'
            '<stop offset=".55" stop-color="#C46A35"/><stop offset="1" stop-color="#6B2F12"/></linearGradient>'
            '<linearGradient id="cv" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#7A3A1A"/><stop offset=".3" stop-color="#E9A06A"/>'
            '<stop offset=".55" stop-color="#C46A35"/><stop offset="1" stop-color="#6B2F12"/></linearGradient>'
            '<linearGradient id="ot" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8C6B22"/><stop offset=".35" stop-color="#F2D47B"/>'
            '<stop offset="1" stop-color="#7A5A16"/></linearGradient>')
    # i tubi: da sinistra al raccordo che perde (x 420), alla valvola (x 760), al T col manometro (x 1040), al rubinetto
    tubi = ('<rect x="-20" y="232" width="1380" height="40" fill="url(#cu)"/>'
            '<rect x="1020" y="120" width="40" height="120" fill="url(#cv)"/>'
            '<path d="M1340 232 h60 a40 40 0 0 1 40 40 v58 h-40 v-58 h-60 z" fill="url(#cu)"/>'
            '<rect x="1394" y="318" width="52" height="34" rx="6" fill="url(#ot)"/>'
            '<rect x="404" y="224" width="40" height="56" rx="6" fill="url(#ot)"/>'
            '<rect x="1004" y="224" width="72" height="56" rx="6" fill="url(#ot)"/>'
            '<rect x="200" y="226" width="22" height="52" rx="4" fill="#8A3C18"/><rect x="620" y="226" width="22" height="52" rx="4" fill="#8A3C18"/>'
            # corpo della valvola
            '<rect x="722" y="214" width="76" height="76" rx="12" fill="url(#ot)"/><rect x="752" y="150" width="16" height="70" fill="#9A7A2C"/>'
            # il manometro: cassa, quadrante, tacche
            '<circle cx="1040" cy="92" r="74" fill="#1F2A30"/><circle cx="1040" cy="92" r="64" fill="#F6F3EC"/>'
            '<path d="M1000 130 A56 56 0 0 1 990 70" stroke="#E5484D" stroke-width="9" fill="none"/>'
            '<path d="M1060 40 A56 56 0 0 1 1092 120" stroke="#3DBE7B" stroke-width="9" fill="none"/>'
            + ''.join('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="#1F2A30" stroke-width="3"/>' % (
                1040 + math.cos(a) * 50, 92 + math.sin(a) * 50, 1040 + math.cos(a) * 60, 92 + math.sin(a) * 60)
                for a in [math.radians(135 + k * 27) for k in range(11)])
            + '<text x="1040" y="118" font-family="Arial" font-size="15" font-weight="700" fill="#1F2A30" text-anchor="middle">bar</text>')
    salva('sifone-tubi.svg', svg(VB, '<defs>' + rame + '</defs>' + tubi))

    # il volantino rosso della valvola (sopra la valvola), fermo o che gira di mezzo giro
    def volantino(gira):
        raggi = ''.join('<rect x="755" y="88" width="10" height="64" rx="5" fill="#B0262B" transform="rotate(%d 760 150)"/>' % (k * 72) for k in range(5))
        return svg(VB, '<defs><radialGradient id="v"><stop offset=".6" stop-color="#F0525A"/><stop offset="1" stop-color="#B0262B"/></radialGradient></defs>'
                   '<g class="w">' + raggi + '<circle cx="760" cy="150" r="66" fill="none" stroke="url(#v)" stroke-width="18"/>'
                   '<circle cx="760" cy="150" r="20" fill="#C8333A"/><circle cx="760" cy="150" r="8" fill="#7E1A1E"/></g>',
                   stile=('@keyframes w{to{transform:rotate(-200deg)}}.w{transform-origin:760px 150px;transform-box:view-box;'
                          'animation:w 1.2s cubic-bezier(.3,.7,.3,1) forwards}') if gira else '')
    salva('sifone-volantino.svg', volantino(False))
    salva('sifone-volantino-gira.svg', volantino(True))
    # la perdita: gocce che si staccano dal raccordo e uno spruzzo fine
    gocce = ''.join('<path class="g g%d" d="M424 300 q-7 12 0 18 q7 -6 0 -18z" fill="#7FC4F0"/>' % k for k in range(4))
    spruzzo = ''.join('<circle class="s s%d" cx="%d" cy="%d" r="3" fill="#BFE3FA"/>' % (k, 430 + k * 9, 236 - k * 5) for k in range(5))
    salva('sifone-perdita.svg', svg(VB, gocce + spruzzo,
                                    stile='@keyframes g{0%{transform:translateY(0);opacity:0}10%{opacity:1}100%{transform:translateY(110px);opacity:1}}'
                                          '@keyframes s{0%{transform:translate(0,0);opacity:.9}100%{transform:translate(40px,-30px);opacity:0}}'
                                          '.g{animation:g 1.1s cubic-bezier(.5,0,1,1) infinite}.g1{animation-delay:.28s}.g2{animation-delay:.55s}.g3{animation-delay:.83s}'
                                          '.s{animation:s .9s ease-out infinite}.s1{animation-delay:.18s}.s2{animation-delay:.36s}.s3{animation-delay:.54s}.s4{animation-delay:.72s}'))
    # la pozza che si allarga piano sotto il raccordo
    salva('sifone-pozza.svg', svg(VB, '<ellipse class="p" cx="424" cy="410" rx="120" ry="12" fill="#7FC4F0" opacity=".55"/>',
                                  stile='@keyframes p{from{transform:scaleX(.15)}to{transform:scaleX(1.6)}}'
                                        '.p{transform-origin:424px 410px;transform-box:view-box;animation:p 22s ease-out forwards}'))
    # l'acqua che corre nel tubo (riparato) e scende dal rubinetto
    salva('sifone-acqua.svg', svg(VB, '<path class="a" d="M-20 252 H1380 q40 0 40 40 V420" stroke="#9FD8FF" stroke-width="8" fill="none" '
                                      'stroke-dasharray="26 40" stroke-linecap="round" opacity=".9"/>'
                                      '<rect class="c" x="1408" y="352" width="24" height="70" fill="#9FD8FF" opacity=".75"/>',
                                  stile='@keyframes a{to{stroke-dashoffset:-660}}.a{animation:a 1.8s linear infinite}'
                                        '@keyframes c{0%,100%{opacity:.55}50%{opacity:.9}}.c{animation:c .6s ease-in-out infinite}'))

    # l'ago del manometro: giù nel rosso, o che sale al verde
    def ago(sale):
        return svg(VB, '<g class="n"><path d="M1040 92 L1036 96 L1006 128 L1044 96 Z" fill="#1F2A30"/><path d="M1040 92 L1000 132" stroke="#E5484D" stroke-width="4" stroke-linecap="round"/></g>'
                       '<circle cx="1040" cy="92" r="8" fill="#1F2A30"/>',
                   stile=('@keyframes n{0%{transform:rotate(0)}70%{transform:rotate(196deg)}85%{transform:rotate(186deg)}100%{transform:rotate(190deg)}}'
                          '.n{transform-origin:1040px 92px;transform-box:view-box;animation:n 1.6s ease-out .4s forwards}') if sale else
                   '@keyframes n{0%,100%{transform:rotate(0)}50%{transform:rotate(-6deg)}}.n{transform-origin:1040px 92px;transform-box:view-box;animation:n .5s ease-in-out infinite}')
    salva('sifone-ago-giu.svg', ago(False))
    salva('sifone-ago-su.svg', ago(True))
    sifone_chiave()


def sifone_chiave():
    """La chiave inglese che stringe il raccordo che perde (x 424, y 252): arriva da sotto, afferra il dado e gira due
    volte di un terzo di giro. Prima la perdita spariva girando il volantino della valvola, e non aveva senso (l'utente,
    13 nov 2026: «una perdita che scompare girando un controllo»)."""
    VB = '0 0 1600 420'
    acciaio = ('<linearGradient id="k" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#7D8590"/>'
               '<stop offset=".45" stop-color="#E3E7EC"/><stop offset="1" stop-color="#6B727C"/></linearGradient>'
               '<linearGradient id="kt" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#B7BEC7"/>'
               '<stop offset="1" stop-color="#6B727C"/></linearGradient>')
    # in coordinate locali: il dado al centro (0, 0), le ganasce ai suoi fianchi (±22), il manico in giù
    chiave = ('<g class="ch"><g transform="translate(424 252)"><g class="kr">'
              '<path d="M-38 -30 h14 v46 h48 v-46 h14 v56 a10 10 0 0 1 -10 10 h-56 a10 10 0 0 1 -10 -10 z" fill="url(#kt)"/>'
              '<rect x="-15" y="34" width="30" height="190" rx="12" fill="url(#k)"/>'
              '<circle cx="0" cy="206" r="7" fill="#4A5059"/>'
              '<rect x="-9" y="40" width="18" height="10" rx="3" fill="#59606A"/></g></g></g>')
    stile = ('@keyframes ch{0%{opacity:0;transform:translateY(60px)}18%{opacity:1;transform:translateY(0)}100%{opacity:1;transform:translateY(0)}}'
             '@keyframes kr{0%,22%{transform:rotate(-38deg)}44%{transform:rotate(4deg)}54%{transform:rotate(-30deg)}'
             '74%{transform:rotate(8deg)}82%{transform:rotate(4deg)}100%{transform:rotate(6deg)}}'
             '.ch{animation:ch 1.8s ease-out forwards}'
             '.kr{transform-box:view-box;transform-origin:0 0;animation:kr 1.8s cubic-bezier(.4,0,.3,1) forwards}')
    salva('sifone-chiave.svg', svg(VB, '<defs>' + acciaio + '</defs>' + chiave, stile=stile))


# ───────────────────────── DUE MANI (imbianchino) ─────────────────────────
def due_mani():
    # il rullo grande: il manico e la staffa (il cilindro è un livello colorato a parte); la trama del pelo, chiara
    # la staffa e il manico, attaccati in fondo al cilindro: l'asse in (20, 10) del disegno
    salva('duemani-staffa.svg', svg('0 0 400 400',
        '<path d="M20 4 V60 Q20 100 70 104 L160 112 Q190 116 196 146 L214 240" stroke="#B8BEC6" stroke-width="12" fill="none" '
        'stroke-linecap="round" stroke-linejoin="round"/><rect x="8" y="0" width="24" height="22" rx="6" fill="#6E757E"/>'
        '<path d="M214 236 L300 400" stroke="#E2B34A" stroke-width="30" stroke-linecap="round"/>'
        '<path d="M206 240 L288 400" stroke="#F3D27E" stroke-width="8" stroke-linecap="round"/>'
        '<rect x="196" y="226" width="34" height="30" rx="8" fill="#6E757E" transform="rotate(-28 213 241)"/>'))
    salva('duemani-manico.svg', svg('0 0 300 1000',
        '<path d="M60 40 H180 Q210 40 210 70 V470 Q210 500 180 500 H150" stroke="#B8BEC6" stroke-width="16" fill="none" stroke-linejoin="round"/>'
        '<rect x="128" y="490" width="44" height="80" rx="10" fill="#6E757E"/>'
        '<rect x="134" y="560" width="32" height="420" rx="14" fill="#E2B34A"/><rect x="134" y="560" width="10" height="420" rx="5" fill="#F3D27E"/>'
        '<rect x="56" y="20" width="10" height="960" rx="5" fill="#8E959D"/>'))
    salva('duemani-pelo.svg', svg('0 0 120 1000', ''.join(
        '<line x1="%d" y1="0" x2="%d" y2="1000" stroke="%s" stroke-width="%d" opacity="%.2f"/>' % (x, x + random.randint(-3, 3), c, w, o)
        for x, c, w, o in [(random.randint(4, 116), random.choice(['#ffffff', '#000000']), random.randint(1, 3), random.uniform(.05, .18))
                           for _ in range(60)])
        + '<rect width="120" height="1000" fill="url(#s)"/>'
        '<defs><linearGradient id="s" x1="0" x2="1"><stop offset="0" stop-color="#000" stop-opacity=".35"/><stop offset=".35" stop-color="#fff" stop-opacity=".18"/>'
        '<stop offset=".6" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".4"/></linearGradient></defs>',
        extra=' preserveAspectRatio="none"'))
    # la trama della vernice stesa: righe verticali appena visibili, sopra la tinta
    random.seed(7)
    righe = ''.join('<rect x="%d" y="0" width="%d" height="900" fill="%s" opacity="%.3f"/>' % (
        x, random.randint(2, 9), random.choice(['#fff', '#000']), random.uniform(.02, .07)) for x in range(0, 1600, 7))
    salva('duemani-trama.svg', svg('0 0 1600 900', righe, extra=' preserveAspectRatio="none"'))


# ───────────────────────── FILO A PIOMBO (muratore) ─────────────────────────
def filo_a_piombo():
    # le file di mattoni (PNG, 1600 × 100): mattoni pieni a mano, colori che cambiano appena, malta grigia
    rng = random.Random(11)
    palette = [(168, 72, 46), (152, 62, 40), (181, 86, 55), (140, 58, 38), (190, 98, 64), (160, 78, 52), (122, 52, 36)]
    for n in range(7):
        W, H = 1600, 100
        im = Image.new('RGBA', (W, H), (196, 188, 174, 255))
        px = np.array(im).astype(np.float32)
        rumore = np.random.default_rng(n).normal(0, 7, (H, W))
        for c in range(3):
            px[:, :, c] += rumore
        im = Image.fromarray(np.clip(px, 0, 255).astype(np.uint8))
        d = ImageDraw.Draw(im)
        L, G = 230, 12
        x = -(L + G) // 2 if n % 2 else 0
        while x < W:
            col = rng.choice(palette)
            col = tuple(max(0, min(255, v + rng.randint(-14, 14))) for v in col)
            d.rounded_rectangle([x + G // 2, 6, x + L + G // 2, H - 6], radius=5, fill=col + (255,))
            x += L + G
        a = np.array(im).astype(np.float32)
        grana = np.random.default_rng(100 + n).normal(0, 12, (H, W))
        mattone = (a[:, :, 0] - a[:, :, 2]) > 40
        for c in range(3):
            a[:, :, c] = np.where(mattone, a[:, :, c] + grana, a[:, :, c])
        # luce dall'alto: il filo alto del mattone più chiaro, quello basso più scuro
        ombra = np.linspace(14, -22, H)[:, None]
        for c in range(3):
            a[:, :, c] = np.where(mattone, a[:, :, c] + ombra, a[:, :, c])
        Image.fromarray(np.clip(a, 0, 255).astype(np.uint8)).save(os.path.join(OUT, 'filo-mattoni-%d.png' % (n + 1)), optimize=True)
    # la livella e la bolla (la bolla è un livello a parte, che va in centro scorrendo)
    salva('filo-livella.svg', svg('0 0 1000 120',
        '<defs><linearGradient id="a" y1="0" y2="1"><stop offset="0" stop-color="#F6CB3C"/><stop offset=".6" stop-color="#E8B923"/><stop offset="1" stop-color="#B8890F"/></linearGradient>'
        '<linearGradient id="v" y1="0" y2="1"><stop offset="0" stop-color="#E9F7C9"/><stop offset="1" stop-color="#A9D46A"/></linearGradient></defs>'
        '<rect x="0" y="14" width="1000" height="92" rx="10" fill="url(#a)"/><rect x="0" y="86" width="1000" height="20" rx="8" fill="#000" opacity=".15"/>'
        '<rect x="20" y="30" width="40" height="60" rx="6" fill="#2A2A2A"/><rect x="940" y="30" width="40" height="60" rx="6" fill="#2A2A2A"/>'
        '<rect x="390" y="36" width="220" height="48" rx="24" fill="url(#v)" stroke="#6E8F2E" stroke-width="3"/>'
        '<line x1="470" y1="38" x2="470" y2="82" stroke="#2A2A2A" stroke-width="3"/><line x1="530" y1="38" x2="530" y2="82" stroke="#2A2A2A" stroke-width="3"/>'
        '<text x="200" y="74" font-family="Arial" font-size="24" font-weight="700" fill="#5C4708" letter-spacing="3">FILO A PIOMBO</text>'
        '<text x="800" y="74" font-family="Arial" font-size="22" fill="#5C4708" text-anchor="middle">± 0,5 mm/m</text>'))
    salva('filo-bolla.svg', svg('0 0 60 40', '<ellipse cx="30" cy="20" rx="26" ry="15" fill="#FFFFFF" opacity=".92"/>'
                                '<ellipse cx="24" cy="15" rx="9" ry="4" fill="#FFFFFF"/><ellipse cx="30" cy="20" rx="26" ry="15" fill="none" stroke="#6E8F2E" stroke-width="2" opacity=".6"/>'))
    # il filo a piombo che dondola appeso
    salva('filo-piombo.svg', svg('0 0 200 1000',
        '<g class="p"><line x1="100" y1="0" x2="100" y2="820" stroke="#F2EEE6" stroke-width="2.5"/>'
        '<path d="M100 820 L128 860 L100 980 L72 860 Z" fill="#B8BEC6"/><path d="M100 820 L128 860 L100 980 Z" fill="#8E959D"/>'
        '<rect x="88" y="806" width="24" height="18" rx="4" fill="#6E757E"/></g>',
        stile='@keyframes p{0%,100%{transform:rotate(1.6deg)}50%{transform:rotate(-1.6deg)}}'
              '.p{transform-origin:100px 0;transform-box:view-box;animation:p 3.2s ease-in-out infinite}'))


# ───────────────────────── TRUCIOLO (falegname) ─────────────────────────
def truciolo(media):
    # la tavola: dalla foto delle assi, una lastra larga 5,2 : 1 divisa in due metà (sinistra e destra del taglio)
    src = os.path.join(media, 'pexels-139306.jpg')
    if os.path.exists(src):
        im = Image.open(src).convert('RGB')
        w, h = im.size
        th = int(w / 5.2)
        y0 = max(0, (h - th) // 2)
        lastra = im.crop((0, y0, w, y0 + th)).resize((2600, 500), Image.LANCZOS)
        # i bordi della tavola: un filo scuro sopra e sotto, come uno spigolo
        d = ImageDraw.Draw(lastra)
        d.rectangle([0, 0, 2600, 7], fill=(92, 62, 34))
        d.rectangle([0, 492, 2600, 500], fill=(70, 46, 24))
        lastra.crop((0, 0, 1300, 500)).save(os.path.join(OUT, 'truciolo-tavola-sx.jpg'), quality=86, progressive=True)
        lastra.crop((1300, 0, 2600, 500)).save(os.path.join(OUT, 'truciolo-tavola-dx.jpg'), quality=86, progressive=True)
    # la lama circolare che gira
    denti = ''.join('<path d="M%.1f %.1f L%.1f %.1f L%.1f %.1f Z" fill="#C9CED6"/>' % (
        200 + math.cos(a) * 168, 200 + math.sin(a) * 168, 200 + math.cos(a + .09) * 196, 200 + math.sin(a + .09) * 196,
        200 + math.cos(a + .2) * 168, 200 + math.sin(a + .2) * 168) for a in [k * 2 * math.pi / 40 for k in range(40)])
    salva('truciolo-lama.svg', svg('0 0 400 400',
        '<defs><radialGradient id="m"><stop offset="0" stop-color="#F1F3F6"/><stop offset=".7" stop-color="#B9BFC7"/><stop offset="1" stop-color="#8E959D"/></radialGradient></defs>'
        '<g class="l">' + denti + '<circle cx="200" cy="200" r="172" fill="url(#m)"/>'
        '<path d="M200 60 A140 140 0 0 1 330 160" stroke="#fff" stroke-width="10" fill="none" opacity=".5"/>'
        + ''.join('<rect x="196" y="70" width="8" height="40" rx="4" fill="#9AA1A9" transform="rotate(%d 200 200)"/>' % (k * 60) for k in range(6))
        + '<circle cx="200" cy="200" r="46" fill="#2A2E33"/><circle cx="200" cy="200" r="16" fill="#8E959D"/></g>',
        stile='@keyframes l{to{transform:rotate(360deg)}}.l{transform-origin:200px 200px;transform-box:view-box;animation:l .35s linear infinite}'))
    # la segatura che vola via dalla lama
    random.seed(3)
    grani = ''.join('<circle class="s s%d" cx="200" cy="200" r="%.1f" fill="%s"/>' % (k % 6, random.uniform(2, 5), random.choice(['#E8C38A', '#D9A86A', '#F2D9AE']))
                    for k in range(36))
    stile = '.s{opacity:0}' + ''.join('@keyframes s%d{0%%{transform:translate(0,0);opacity:1}100%%{transform:translate(%dpx,%dpx);opacity:0}}' % (
        k, random.randint(-180, -40), random.randint(-160, 160)) + '.s%d{animation:s%d .7s ease-out %0.2fs infinite}' % (k, k, k * .11) for k in range(6))
    salva('truciolo-segatura.svg', svg('0 0 400 400', grani, stile=stile))


# ───────────────────────── INCUDINE (fabbro) ─────────────────────────
def incudine():
    # filtro e sfumatura in unità della tela: su una barra dritta il riquadro è alto zero e il disegno spariva
    alone = ('<filter id="g" filterUnits="userSpaceOnUse" x="0" y="0" width="1000" height="400"><feGaussianBlur stdDeviation="10" result="b"/>'
             '<feMerge><feMergeNode in="b"/><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>')

    def ferro(colori, d, w=34):
        return svg('0 0 1000 400', '<defs>' + alone + '<linearGradient id="c" gradientUnits="userSpaceOnUse" x1="80" y1="0" x2="920" y2="0"><stop offset="0" stop-color="%s"/>'
                   '<stop offset=".5" stop-color="%s"/><stop offset="1" stop-color="%s"/></linearGradient></defs>'
                   '<path d="%s" stroke="url(#c)" stroke-width="%d" fill="none" stroke-linecap="round" stroke-linejoin="round" filter="url(#g)"/>'
                   % (colori[0], colori[1], colori[2], d, w), stile='@keyframes t{0%,100%{opacity:1}50%{opacity:.86}}svg{animation:t 1.2s ease-in-out infinite}')
    # 1: la barra dritta, gialla di fuoco; 2: la punta tirata, arancio; 3: piegata a gancio, rossa; 4: il ricciolo, quasi freddo
    salva('incudine-ferro-1.svg', ferro(('#FF8C1A', '#FFF1A8', '#FF9F2E'), 'M80 200 H920'))
    salva('incudine-ferro-2.svg', ferro(('#E8561A', '#FFC24D', '#FF7A1A'), 'M80 200 H760 L920 200', 30))
    salva('incudine-ferro-3.svg', ferro(('#7A2A12', '#E5481A', '#FF8C33'), 'M80 200 H640 C760 200 800 120 760 70 C730 40 690 60 700 100', 30))
    salva('incudine-ferro-4.svg', svg('0 0 1000 400', '<defs><linearGradient id="c" gradientUnits="userSpaceOnUse" x1="80" y1="0" x2="820" y2="0"><stop offset="0" stop-color="#3A3F45"/>'
                                      '<stop offset=".7" stop-color="#5A6068"/><stop offset="1" stop-color="#C2391A"/></linearGradient></defs>'
                                      '<path d="M80 200 H600 C760 200 820 120 790 60 C760 10 670 20 660 80 C652 130 710 150 740 120 C760 100 745 70 720 76" '
                                      'stroke="url(#c)" stroke-width="28" fill="none" stroke-linecap="round"/>'
                                      '<path d="M80 192 H600" stroke="#8E959D" stroke-width="4" opacity=".6"/>'))
    # le scintille dei colpi (a raffiche, sempre)
    random.seed(5)
    sc = ''
    stile = '.s{opacity:0}'
    for k in range(28):
        a = random.uniform(-math.pi, 0)
        r = random.uniform(160, 420)
        dx, dy = math.cos(a) * r, math.sin(a) * r * .8
        sc += '<line class="s s%d" x1="500" y1="200" x2="%.0f" y2="%.0f" stroke="%s" stroke-width="%.1f" stroke-linecap="round"/>' % (
            k, 500 + dx * .06, 200 + dy * .06, random.choice(['#FFF3B0', '#FFC24D', '#FF8C1A']), random.uniform(2, 4))
        stile += ('@keyframes s%d{0%%{transform:translate(0,0);opacity:0}4%%{opacity:1}40%%{transform:translate(%.0fpx,%.0fpx);opacity:0}100%%{opacity:0}}'
                  '.s%d{animation:s%d 1.6s cubic-bezier(.1,.7,.3,1) %.2fs infinite}') % (k, dx, dy + 60, k, k, random.uniform(0, .25))
    salva('incudine-scintille.svg', svg('0 0 1000 400', sc, stile=stile))


# ───────────────────────── CRIC (meccanico) ─────────────────────────
def cric():
    # Il cruscotto 3 (18 nov 2026, utente: «il cruscotto dovrebbe rimanere più basso (e anche più piccolo) per fare spazio
    # alla strada che scorre… più reale e definito»). Una plancia larga quanto lo schermo (2200 × 640) con la palpebra del
    # quadro al centro; dentro due quadranti con la ghiera cromata, i numeri retroilluminati, la zona rossa, l'ago affusolato
    # col perno e il riflesso del vetro, i piccoli display (orologio, contachilometri) e lo schermo centrale dove lo Slider
    # Pro scrive le spie. Il livello va a tutta larghezza e poggia sul fondo: il quadro sta nella metà bassa.
    def arco(cx, cy, r, a0, a1):
        x0, y0 = cx + math.cos(math.radians(a0)) * r, cy + math.sin(math.radians(a0)) * r
        x1, y1 = cx + math.cos(math.radians(a1)) * r, cy + math.sin(math.radians(a1)) * r
        return 'M%.1f %.1f A%d %d 0 %d 1 %.1f %.1f' % (x0, y0, r, r, 1 if a1 - a0 > 180 else 0, x1, y1)

    def quadrante(cx, cy, r, numeri, rosso_da, ago_cls, unita, lcd):
        n = len(numeri) - 1
        ang = lambda v: 135 + v * 270 / n
        t = ('<circle cx="%d" cy="%d" r="%d" fill="#000" opacity=".55" filter="url(#sf)"/>' % (cx, cy + 10, r + 22)
             + '<circle cx="%d" cy="%d" r="%d" fill="url(#cr)"/>' % (cx, cy, r + 13)
             + '<circle cx="%d" cy="%d" r="%d" fill="#030405"/>' % (cx, cy, r + 4)
             + '<circle cx="%d" cy="%d" r="%d" fill="url(#fa)"/>' % (cx, cy, r)
             + '<circle cx="%d" cy="%d" r="%d" fill="none" stroke="#2EC4B6" stroke-opacity=".28" stroke-width="2" filter="url(#lu)"/>' % (cx, cy, r - 3))
        # la zona rossa, sul bordo
        t += '<path d="%s" stroke="#FF3B2F" stroke-width="9" fill="none" opacity=".9" filter="url(#lu)"/>' % arco(cx, cy, r - 9, ang(rosso_da), ang(n))
        tacche, nm = '', ''
        for k in range(n + 1):
            a = math.radians(ang(k))
            tacche += '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="%s" stroke-width="4.5" stroke-linecap="round"/>' % (
                cx + math.cos(a) * (r - 30), cy + math.sin(a) * (r - 30), cx + math.cos(a) * (r - 8), cy + math.sin(a) * (r - 8),
                '#FF5A4A' if k >= rosso_da else '#F4F7FA')
            nm += ('<text x="%.1f" y="%.1f" font-family="Helvetica, Arial, sans-serif" font-size="%d" font-weight="700" fill="#F4F7FA" '
                   'text-anchor="middle" dominant-baseline="central">%s</text>') % (
                cx + math.cos(a) * (r - (54 if len(numeri[k]) < 3 else 58)), cy + math.sin(a) * (r - (54 if len(numeri[k]) < 3 else 58)), 21 if len(numeri[k]) < 3 else 16, numeri[k])
            if k < n:
                for j in range(1, 5):
                    b = math.radians(ang(k + j / 5))
                    lung = 18 if j == 2 or j == 3 else 14
                    tacche += '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f" stroke="%s" stroke-width="2" stroke-linecap="round"/>' % (
                        cx + math.cos(b) * (r - 8 - lung + (4 if j != 2.5 else 0)), cy + math.sin(b) * (r - 8 - lung + 4),
                        cx + math.cos(b) * (r - 8), cy + math.sin(b) * (r - 8), '#FF8A80' if k >= rosso_da else '#B7C6D1')
        t += '<g filter="url(#lu)">' + tacche + nm + '</g>'
        t += ('<text x="%d" y="%d" font-family="Helvetica, Arial, sans-serif" font-size="15" letter-spacing="1.5" fill="#8FA4B3" '
              'text-anchor="middle">%s</text>') % (cx, cy + 46, unita)
        # il piccolo display in basso nel quadrante
        t += ('<rect x="%d" y="%d" width="124" height="34" rx="6" fill="#06100F" stroke="#1D2A2C" stroke-width="2"/>'
              '<text x="%d" y="%d" font-family="Consolas, Menlo, monospace" font-size="17" fill="#7FF0E2" text-anchor="middle" '
              'dominant-baseline="central" filter="url(#lu)">%s</text>') % (cx - 62, cy + 78, cx, cy + 95, lcd)
        # l'ago affusolato (disegnato verso sinistra, a 180°: lo ruota la classe) col contrappeso, e il perno
        t += ('<g class="%s"><polygon points="%d,%d %d,%d %d,%d %d,%d %d,%d" fill="url(#ag)" filter="url(#la)"/>'
              '<polygon points="%d,%d %d,%d %d,%d" fill="#FFD1B8" opacity=".55"/></g>') % (
            ago_cls, cx - r + 22, cy, cx + 4, cy - 6, cx + 30, cy - 4, cx + 30, cy + 4, cx + 4, cy + 6,
            cx - r + 22, cy, cx + 4, cy - 2, cx + 4, cy)
        t += ('<circle cx="%d" cy="%d" r="19" fill="url(#mo)" stroke="#06080A" stroke-width="2"/>'
              '<circle cx="%d" cy="%d" r="6" fill="#0B0E11"/><circle cx="%d" cy="%d" r="3" fill="#C8D0D7" opacity=".7"/>') % (
            cx, cy, cx, cy, cx - 6, cy - 6)
        # il riflesso del vetro, in alto a sinistra
        t += ('<path d="M%.1f %.1f A%d %d 0 0 1 %.1f %.1f Q%.1f %.1f %.1f %.1f Z" fill="url(#ve)"/>') % (
            cx - r * .93, cy - r * .1, r, r, cx + r * .45, cy - r * .88, cx - r * .2, cy - r * .55, cx - r * .93, cy - r * .1)
        return t

    SIL = ('M0 342 C260 320 360 302 410 294 C470 150 700 64 1100 58 C1500 64 1730 150 1790 294 '
           'C1840 302 1940 320 2200 342 V640 H0 Z')
    POZZO = ('M478 332 C520 196 740 124 1100 120 C1460 124 1680 196 1722 332 L1722 528 C1722 560 1700 578 1668 578 '
             'L532 578 C500 578 478 560 478 528 Z')
    defs = ('<defs>'
            '<linearGradient id="pl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1D2127"/><stop offset=".35" stop-color="#121519"/>'
            '<stop offset="1" stop-color="#07080A"/></linearGradient>'
            '<linearGradient id="po" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#020203"/><stop offset=".55" stop-color="#07090B"/>'
            '<stop offset="1" stop-color="#0C0F12"/></linearGradient>'
            '<linearGradient id="om" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#000" stop-opacity=".85"/>'
            '<stop offset=".32" stop-color="#000" stop-opacity="0"/></linearGradient>'
            '<linearGradient id="cr" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#E6EAEE"/><stop offset=".28" stop-color="#7B848D"/>'
            '<stop offset=".52" stop-color="#2B3036"/><stop offset=".78" stop-color="#8D959D"/><stop offset="1" stop-color="#3A4046"/></linearGradient>'
            '<radialGradient id="fa" cx=".5" cy=".42" r=".62"><stop offset="0" stop-color="#151B21"/><stop offset=".75" stop-color="#0A0D10"/>'
            '<stop offset="1" stop-color="#040506"/></radialGradient>'
            '<linearGradient id="ag" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#FF3B1F"/><stop offset=".7" stop-color="#FF7A2E"/>'
            '<stop offset="1" stop-color="#B8261A"/></linearGradient>'
            '<radialGradient id="mo" cx=".35" cy=".3" r=".8"><stop offset="0" stop-color="#9AA3AC"/><stop offset=".45" stop-color="#3B434B"/>'
            '<stop offset="1" stop-color="#11151A"/></radialGradient>'
            '<linearGradient id="ve" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".13"/>'
            '<stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>'
            '<linearGradient id="tf" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0D1C24"/><stop offset="1" stop-color="#050B0F"/></linearGradient>'
            '<linearGradient id="tv" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".07"/>'
            '<stop offset=".45" stop-color="#fff" stop-opacity="0"/></linearGradient>'
            '<filter id="lu" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="2.4" result="b"/>'
            '<feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>'
            '<filter id="la" x="-20%" y="-60%" width="140%" height="220%"><feGaussianBlur stdDeviation="4" result="b"/>'
            '<feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>'
            '<filter id="sf" x="-30%" y="-30%" width="160%" height="160%"><feGaussianBlur stdDeviation="14"/></filter>'
            '<filter id="gr" filterUnits="userSpaceOnUse" x="0" y="0" width="2200" height="640">'
            '<feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" seed="7" stitchTiles="stitch"/>'
            '<feColorMatrix values="0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  0 0 0 .06 0"/></filter>'
            '<clipPath id="cs"><path d="' + SIL + '"/></clipPath>'
            '</defs>')
    corpo = (defs
             # la plancia, la grana della plastica, lo spigolo illuminato e la cucitura della palpebra
             + '<path d="' + SIL + '" fill="url(#pl)"/>'
             + '<rect width="2200" height="640" filter="url(#gr)" clip-path="url(#cs)"/>'
             + '<path d="M0 342 C260 320 360 302 410 294 C470 150 700 64 1100 58 C1500 64 1730 150 1790 294 C1840 302 1940 320 2200 342" '
               'stroke="#4A535C" stroke-width="2.5" fill="none" opacity=".85"/>'
             + '<path d="M432 300 C492 166 714 84 1100 78 C1486 84 1708 166 1768 300" stroke="#2C333A" stroke-width="2.5" '
               'stroke-dasharray="9 7" fill="none"/>'
             # il pozzo del quadro, con l'ombra della palpebra sopra
             + '<path d="' + POZZO + '" fill="url(#po)" stroke="#252B32" stroke-width="3"/>'
             + '<path d="' + POZZO + '" fill="url(#om)"/>'
             + '<path d="M532 578 L1668 578" stroke="#3A424B" stroke-width="2" opacity=".7"/>'
             + quadrante(660, 365, 150, ['0', '1', '2', '3', '4', '5', '6', '7', '8'], 6.5, 'giri', '× 1000 giri/min', '12:40')
             + quadrante(1540, 365, 150, ['0', '20', '40', '60', '80', '100', '120', '140', '160', '180', '200'], 10, 'vel', 'km/h',
                         '048312 km')
             # lo schermo centrale: la cornice cromata sottile, il vetro, il riflesso
             + '<rect x="838" y="193" width="524" height="324" rx="22" fill="#020304" stroke="url(#cr)" stroke-width="2.5"/>'
             + '<rect x="850" y="205" width="500" height="300" rx="14" fill="url(#tf)" stroke="#16242B" stroke-width="2"/>'
             + '<rect x="850" y="205" width="500" height="300" rx="14" fill="url(#tv)"/>'
             + '<path d="M870 486 H1330" stroke="#2EC4B6" stroke-opacity=".18" stroke-width="2"/>')
    salva('cric-cruscotto-3.svg', svg('0 0 2200 640', corpo,
                                    stile='@keyframes g{0%,100%{transform:rotate(36deg)}50%{transform:rotate(44deg)}}'
                                          '@keyframes v{0%,100%{transform:rotate(16deg)}50%{transform:rotate(22deg)}}'
                                          '.giri{transform-origin:660px 365px;transform-box:view-box;animation:g 1.6s ease-in-out infinite}'
                                          '.vel{transform-origin:1540px 365px;transform-box:view-box;animation:v 7s ease-in-out infinite}'
                                          '@media (prefers-reduced-motion: reduce){.giri,.vel{animation:none}.giri{transform:rotate(40deg)}.vel{transform:rotate(18deg)}}'))
    # le quattro spie: accese (lampeggiano) o spente col segno di fatto
    icone = {
        'olio': '<path d="M12 36 h12 l6 -8 h18 l4 6 l30 -10 l4 6 l-26 22 h-48 z" fill="none" stroke="C" stroke-width="6" stroke-linejoin="round"/><rect x="32" y="20" width="12" height="8" fill="C"/><path d="M88 46 q5 8 0 12 q-5 -4 0 -12z" fill="C"/>',
        'freni': '<circle cx="50" cy="40" r="20" fill="none" stroke="C" stroke-width="6"/><path d="M22 20 q-12 20 0 40 M78 20 q12 20 0 40" stroke="C" stroke-width="6" fill="none"/><rect x="47" y="28" width="6" height="16" fill="C"/><circle cx="50" cy="50" r="3.5" fill="C"/>',
        'batteria': '<rect x="16" y="24" width="68" height="38" rx="4" fill="none" stroke="C" stroke-width="6"/><rect x="26" y="16" width="12" height="8" fill="C"/><rect x="62" y="16" width="12" height="8" fill="C"/><path d="M28 43 h14 M35 36 v14 M58 43 h14" stroke="C" stroke-width="5"/>',
        'motore': '<path d="M20 30 h12 v-8 h30 v8 h10 l8 8 h8 v18 h-8 l-8 8 h-50 v-12 h-6 v-14 h6 z" fill="none" stroke="C" stroke-width="6" stroke-linejoin="round"/>',
    }
    for nome, d in icone.items():
        salva('cric-spia-%s.svg' % nome, svg('0 0 100 80', '<g class="b">' + d.replace('"C"', '"#FFB020"') + '</g>',
                                             stile='@keyframes b{0%,100%{opacity:1}50%{opacity:.35}}.b{animation:b 1.1s ease-in-out infinite;'
                                                   'filter:drop-shadow(0 0 6px #FF8A00)}'))
        salva('cric-spia-%s-ok.svg' % nome, svg('0 0 100 80', '<g opacity=".35">' + d.replace('"C"', '"#7FA3B8"') + '</g>'
                                                '<circle cx="78" cy="60" r="15" fill="#2EC4B6"/><path d="M71 60 l5 5 l10 -11" stroke="#06161A" stroke-width="4" fill="none"/>'))


# ───────────────────────── FULIGGINE (spazzacamino) ─────────────────────────
def fuliggine():
    def lastra(W, H, nome, scritta_size):
        rng = np.random.default_rng(42 + W)

        def macchie(sigma, forza):
            n = Image.fromarray(np.clip(rng.normal(128, 50, (H, W)), 0, 255).astype(np.uint8)).filter(ImageFilter.GaussianBlur(sigma))
            a = np.array(n).astype(np.float32) - 128
            return a / (np.abs(a).max() + 1e-6) * forza
        # nero fumo quasi uniforme, con macchie morbide grandi e medie, qualche colatura verticale e la grana fine
        base = np.full((H, W), 24.0, np.float32) + macchie(90, 9) + macchie(26, 5) + rng.normal(0, 2.6, (H, W))
        colature = np.zeros((H, W), np.float32)
        for _ in range(40):
            x = int(rng.integers(0, W))
            y0 = int(rng.integers(0, H // 2))
            colature[y0:, max(0, x - 1):x + 2] += rng.uniform(-5, 4)
        base += np.array(Image.fromarray(np.clip(colature + 128, 0, 255).astype(np.uint8)).filter(ImageFilter.GaussianBlur(2))).astype(np.float32) - 128
        im = Image.fromarray(np.clip(base, 0, 255).astype(np.uint8)).convert('RGB')
        # la scritta col dito nella fuliggine: «PULISCIMI», un po' più chiara e sbavata
        d = ImageDraw.Draw(im)
        try:
            f = ImageFont.truetype('C:/Windows/Fonts/segoeuib.ttf', scritta_size)
        except Exception:
            f = ImageFont.load_default()
        testo = 'PULISCIMI'
        tw = d.textlength(testo, font=f)
        m = Image.new('L', (W, H), 0)
        dm = ImageDraw.Draw(m)
        dm.text(((W - tw) / 2, H * 0.40), testo, font=f, fill=255)
        m = m.rotate(-4, resample=Image.BICUBIC, center=(W / 2, H / 2)).filter(ImageFilter.GaussianBlur(3))
        chiaro = Image.new('RGB', (W, H), (92, 86, 80))
        im = Image.composite(chiaro, im, m.point(lambda v: int(v * 0.75)))
        # un ditata lunga sotto, come una firma
        d = ImageDraw.Draw(im)
        yb = H * 0.40 + scritta_size * 1.25 + 30
        d.line([(W * 0.33, yb + 8), (W * 0.47, yb - 8), (W * 0.62, yb + 12)], fill=(70, 66, 61), width=max(6, scritta_size // 12))
        im = im.filter(ImageFilter.GaussianBlur(0.6))
        im.save(os.path.join(OUT, nome), quality=84, progressive=True)
    lastra(1920, 1080, 'fuliggine-lastra.jpg', 230)
    lastra(1080, 1800, 'fuliggine-lastra-telefono.jpg', 150)


# ───────────────────────── RASTRELLO (giardiniere) ─────────────────────────
def rastrello():
    # l'erba alta (PNG trasparente, 3200 × 700, in dieci fette da 320) e il prato rasato a righe (3200 × 200)
    rng = random.Random(21)
    W, H = 3200, 700
    im = Image.new('RGBA', (W, H), (0, 0, 0, 0))
    d = ImageDraw.Draw(im)
    verdi = [(46, 92, 40), (62, 118, 48), (84, 140, 58), (104, 156, 70), (38, 78, 34), (120, 168, 80)]
    for _ in range(5200):
        x = rng.uniform(-40, W + 40)
        alto = rng.uniform(260, 680)
        piega = rng.uniform(-90, 90)
        c = rng.choice(verdi) + (255,)
        larg = rng.uniform(7, 15)
        pts_l, pts_r = [], []
        for s in range(13):
            f = s / 12
            px = x + piega * f * f
            py = H - alto * f
            w = larg * (1 - f) ** 0.9
            pts_l.append((px - w / 2, py))
            pts_r.append((px + w / 2, py))
        d.polygon(pts_l + pts_r[::-1], fill=c)
    for k in range(10):
        im.crop((k * 320, 0, (k + 1) * 320, H)).save(os.path.join(OUT, 'rastrello-erba-%d.png' % (k + 1)), optimize=True)
    # il prato rasato a righe chiare e scure
    W2, H2 = 3200, 200
    pr = Image.new('RGBA', (W2, H2), (0, 0, 0, 0))
    a = np.zeros((H2, W2, 4), np.float32)
    righe = (np.arange(W2) // 320) % 2
    for c, (v1, v2) in enumerate([(70, 96), (128, 156), (52, 64)]):
        a[:, :, c] = np.where(righe[None, :] == 0, v1, v2)
    a[:, :, :3] += np.random.default_rng(3).normal(0, 9, (H2, W2, 1))
    a[:, :, 3] = 255
    pr = Image.fromarray(np.clip(a, 0, 255).astype(np.uint8))
    dr = ImageDraw.Draw(pr)
    for _ in range(9000):
        x = rng.uniform(0, W2)
        y = rng.uniform(8, H2)
        dr.line([(x, y), (x + rng.uniform(-3, 3), y - rng.uniform(6, 16))], fill=rng.choice(verdi) + (255,), width=2)
    pr.save(os.path.join(OUT, 'rastrello-prato.png'), optimize=True)
    # il tosaerba a spinta, visto di lato, e l'erba tagliata che vola
    salva('rastrello-tosaerba.svg', svg('0 0 600 420',
        '<defs><linearGradient id="s" y1="0" y2="1"><stop offset="0" stop-color="#3E7D4A"/><stop offset="1" stop-color="#1F4A2B"/></linearGradient></defs>'
        '<path d="M420 230 L560 20" stroke="#2A2A2A" stroke-width="14" stroke-linecap="round"/><path d="M520 70 L590 40" stroke="#2A2A2A" stroke-width="16" stroke-linecap="round"/>'
        '<path d="M110 200 Q120 140 200 130 H430 Q470 130 480 190 L490 300 H90 Z" fill="url(#s)"/>'
        '<path d="M150 150 H420" stroke="#9FD3A8" stroke-width="8" stroke-linecap="round" opacity=".5"/>'
        '<rect x="20" y="150" width="120" height="120" rx="22" fill="#E9E3CF"/><rect x="34" y="166" width="92" height="88" rx="14" fill="#D3CBB2"/>'
        '<text x="290" y="235" font-family="Arial" font-size="40" font-weight="700" fill="#F4F3EC" text-anchor="middle" letter-spacing="2">RASTRELLO</text>'
        '<g class="r"><circle cx="160" cy="320" r="70" fill="#1B1B1B"/><circle cx="160" cy="320" r="34" fill="#C9CED6"/><rect x="155" y="260" width="10" height="120" fill="#8E959D"/></g>'
        '<g class="r2"><circle cx="420" cy="320" r="70" fill="#1B1B1B"/><circle cx="420" cy="320" r="34" fill="#C9CED6"/><rect x="415" y="260" width="10" height="120" fill="#8E959D"/></g>',
        stile='@keyframes r{to{transform:rotate(360deg)}}.r{transform-origin:160px 320px;transform-box:view-box;animation:r .9s linear infinite}'
              '.r2{transform-origin:420px 320px;transform-box:view-box;animation:r .9s linear infinite}'))
    random.seed(9)
    fili = ''.join('<line class="f f%d" x1="60" y1="300" x2="%d" y2="%d" stroke="%s" stroke-width="5" stroke-linecap="round"/>' % (
        k % 6, 60 + random.randint(-14, 14), 300 - random.randint(8, 22), random.choice(['#5C9A4A', '#7DB35E', '#3F7A36'])) for k in range(30))
    stile = '.f{opacity:0}' + ''.join('@keyframes f%d{0%%{transform:translate(0,0) rotate(0);opacity:1}100%%{transform:translate(%dpx,%dpx) rotate(%ddeg);opacity:0}}'
                                      '.f%d{animation:f%d .9s ease-out %.2fs infinite}' % (k, random.randint(-260, -80), random.randint(-220, -60),
                                                                                          random.randint(-200, 200), k, k, k * .14) for k in range(6))
    salva('rastrello-sfalcio.svg', svg('0 0 400 400', fili, stile=stile))


# ───────────────────────── SCHEGGIA (vetraio) ─────────────────────────
def scheggia():
    VB = '0 0 1600 900'
    # i riflessi della lastra: due fasci diagonali chiari
    salva('scheggia-riflessi.svg', svg(VB, '<defs><linearGradient id="r" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff" stop-opacity="0"/>'
                                           '<stop offset=".5" stop-color="#fff" stop-opacity=".22"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient></defs>'
                                           '<path d="M980 -40 L1180 -40 L560 940 L360 940 Z" fill="url(#r)"/><path d="M1260 -40 L1320 -40 L700 940 L640 940 Z" fill="url(#r)"/>',
                                       extra=' preserveAspectRatio="xMidYMid slice"'))
    # le crepe: raggi spezzati da un punto, coi rami, e qualche anello
    def crepe(cx, cy, n, lungo, seme, anelli):
        rng = random.Random(seme)
        p = ''
        for k in range(n):
            a = k * 2 * math.pi / n + rng.uniform(-.25, .25)
            x, y = cx, cy
            pts = ['M%.0f %.0f' % (x, y)]
            L = rng.uniform(.5, 1) * lungo
            passi = 7
            for s in range(passi):
                a += rng.uniform(-.22, .22)
                x += math.cos(a) * L / passi
                y += math.sin(a) * L / passi
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
    C1 = crepe(800, 380, 9, 300, 1, [40])
    salva('scheggia-crepa-1.svg', svg(VB, '<g filter="url(#o)">' + C1 + '</g><circle cx="800" cy="380" r="10" fill="#fff" opacity=".8"/>'
                                      '<defs><filter id="o"><feDropShadow dx="0" dy="0" stdDeviation="1.5" flood-color="#000" flood-opacity=".6"/></filter></defs>',
                                      extra=' preserveAspectRatio="xMidYMid slice"'))
    C2 = C1 + crepe(800, 380, 14, 760, 2, [90, 170, 280]) + crepe(980, 640, 8, 300, 3, [50])
    salva('scheggia-crepa-2.svg', svg(VB, '<g filter="url(#o)">' + C2 + '</g>'
                                      '<defs><filter id="o"><feDropShadow dx="0" dy="0" stdDeviation="1.5" flood-color="#000" flood-opacity=".6"/></filter></defs>',
                                      extra=' preserveAspectRatio="xMidYMid slice"'))
    # il lampo del colpo: il vetro si sbianca un attimo (una volta)
    salva('scheggia-lampo.svg', svg(VB, '<rect class="l" width="1600" height="900" fill="#fff"/>',
                                    stile='@keyframes l{0%{opacity:.55}100%{opacity:0}}.l{opacity:0;animation:l .35s ease-out forwards}',
                                    extra=' preserveAspectRatio="none"'))
    # i frantumi: la lastra in pezzi che cadono girando e svaniscono (una volta)
    rng = random.Random(4)
    pezzi = ''
    stile = ''
    k = 0
    for gx in range(0, 1600, 200):
        for gy in range(0, 900, 180):
            x0, y0 = gx + rng.randint(-30, 30), gy + rng.randint(-30, 30)
            pts = ' '.join('%d,%d' % (x0 + rng.randint(-20, 230), y0 + rng.randint(-20, 210)) for _ in range(4))
            pezzi += '<polygon class="q q%d" points="%s" fill="#E6F4F7" fill-opacity=".22" stroke="#fff" stroke-opacity=".7" stroke-width="2"/>' % (k, pts)
            stile += ('@keyframes q%d{0%%{transform:translate(0,0) rotate(0);opacity:1}100%%{transform:translate(%dpx,%dpx) rotate(%ddeg);opacity:0}}'
                      '.q%d{transform-box:fill-box;transform-origin:center;animation:q%d %.2fs cubic-bezier(.5,0,.9,.6) %.2fs forwards}') % (
                k, rng.randint(-120, 120), rng.randint(700, 1200), rng.randint(-160, 160), k, k, rng.uniform(.9, 1.5), rng.uniform(0, .25))
            k += 1
    salva('scheggia-frantumi.svg', svg(VB, pezzi, stile=stile, extra=' preserveAspectRatio="xMidYMid slice"'))


if __name__ == '__main__':
    random.seed(1)
    morsetto()
    sifone()
    due_mani()
    filo_a_piombo()
    truciolo(OUT)
    incudine()
    cric()
    fuliggine()
    rastrello()
    scheggia()
    print('disegni fatti in', OUT)
