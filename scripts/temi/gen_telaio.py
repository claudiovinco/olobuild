# -*- coding: utf-8 -*-
"""Genera il tema «Telaio» (id telaio): una piccola officina di Torino che costruisce telai da bici in acciaio su misura,
uno alla volta, saldati a mano: strada, gravel, randonnée e pista, con la misurazione del ciclista e il disegno tecnico.

Tema scuro, nero officina, arancio del primo strato di vernice e blu acciaio. Titoli in Hanken Grotesk, dati in Space Mono.

Apertura: Slider Pro «la tavola», alta 16:9, un modello per diapositiva col suo video pieno (la strada fra i pini, il
gravel dal drone, la saldatura, l'officina) e un velo scuro solo dal lato del testo. A destra la tavola del disegno
tecnico: i tubi del telaio si disegnano uno dopo l'altro (la maschera della timeline), le ruote girano, e le quote del
modello (orizzontale, sterzo, carro, verticale) cambiano a ogni diapositiva. Le diapositive si spingono verso destra
(pushRight). Cursore: «Su misura · in acciaio» che gira attorno al puntatore.
"""
import os
import sys
import math

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'telaio'
NAME = 'Telaio'

HEX = {
    'primary': '#FF4D2E', 'primary_contrast': '#0B0C0E', 'secondary': '#A9C4D6', 'secondary_contrast': '#0B0C0E',
    'accent': '#FF8A6B', 'dark': '#060708', 'light': '#F2F4F5', 'muted': '#16181B', 'muted_contrast': '#E6E9EB',
    'text': '#E6E9EB', 'text_muted': '#9AA3AA', 'background': '#0B0C0E', 'border': '#262A2F', 'link': '#FF4D2E',
}

HOME, TELAI, SU_MISURA, OFFICINA, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:telai}}', '{{PAGINA:su-misura}}',
                                              '{{PAGINA:officina}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'officina@telaio.example', '+39 011 000 3141', 'tel:+390110003141'
LAT, LNG = '45.0716', '7.6407'

V_STRADA, V_GRAVEL, V_SALDA, V_OFFICINA, V_SCINTILLE = ('pexels-video-5789980', 'pexels-video-13963645', 'pexels-video-855137',
                                                       'pexels-video-6333025', 'pexels-video-34771866')
PHOTOS = [
    'pexels-40002193.jpg', 'pexels-15211612.jpg', 'pexels-4828817.jpg', 'pexels-6496532.jpg', 'pexels-15211611.jpg',
    'pexels-31628832.jpg', 'pexels-132682.jpg', 'pexels-13122870.jpg', 'pexels-6334127.jpg', 'pexels-17363454.jpg',
    'pexels-17363462.jpg', 'pexels-8841821.jpg', 'pexels-8934524.jpg', 'pexels-36398681.jpg', 'pexels-6502511.jpg',
    'pexels-7936509.jpg', 'pexels-15822454.jpg',
]
VIDEOS = [V_STRADA, V_GRAVEL, V_SALDA, V_OFFICINA, V_SCINTILLE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=False)
C = K.c
ARANCIO, ACCIAIO = C['acc'], C['sec']
MONO = 'Space Mono'
VOCI = [('map-pin', 'Via Frejus 31 · Torino', CONTATTI + '#officina'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Telaio', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Telaio', 'Telai in acciaio su misura, saldati a mano a Torino da Matteo Gilardi. Strada, gravel, '
                   'randonnée e pista: una bici alla volta, ventidue all’anno.',
                   [('I telai', TELAI), ('Su misura', SU_MISURA), ('L’officina', OFFICINA), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'L’officina', 'Martedì — sabato 9 — 18<br>La misurazione su appuntamento', VOCI,
                   '© 2026 Telaio · Matteo Gilardi · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150)


# ── L'apertura: la tavola del disegno ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
# il disegno in px (y verso l'alto), con l'asse posteriore nell'origine
R0, F0, MC, ST, FC, HT = (0, 0), (315, 0), (128, -22), (79.7, 141), (280, 110.6), (266, 155.4)
SELLA, ATTACCO = (62, 198.5), (298, 162)
TUBI = [  # nome, da, a, spessore, colore (None = arancio)
    ('carro', R0, MC, 4, None), ('obliquo-post', R0, (76, 130), 3, None), ('verticale', MC, ST, 5, None),
    ('orizzontale', ST, HT, 5, None), ('obliquo', MC, FC, 6, None), ('sterzo', FC, HT, 7, None), ('forcella', F0, FC, 4, None),
    ('reggisella', ST, SELLA, 3, 'chiaro'), ('attacco', HT, ATTACCO, 3, 'chiaro'),
]
MODELLI = [  # linguetta, occhiello, riga 1, riga 2, testo, video, tavola, orizzontale, sterzo, carro, verticale, scheda
    ('Strada', 'Telaio N° 214 · strada', 'Su misura,', 'al millimetro.',
     'Ti misuriamo in officina, disegniamo il telaio sulle tue misure e lo saldiamo a mano, tubo per tubo.',
     V_STRADA, 'Tavola 01 · strada', '545', '73°', '405', '540', 'Cromo-molibdeno · 1,8 kg · da 2.400 €'),
    ('Gravel', 'Telaio N° 215 · gravel', 'Per le strade', 'che non ci sono.',
     'Carro lungo, sterzo calmo, gomme fino a 50 mm: il telaio per perdersi senza fretta.',
     V_GRAVEL, 'Tavola 02 · gravel', '565', '71,5°', '430', '520', 'Cromo-molibdeno · 2,0 kg · da 2.600 €'),
    ('Randonnée', 'Telaio N° 216 · randonnée', 'Ogni giunto,', 'a mano.',
     'Parafanghi, portapacchi e luci integrate: la bici per i viaggi lunghi, saldata a TIG e brasata dove serve.',
     V_SALDA, 'Tavola 03 · randonnée', '560', '72°', '445', '560', 'Cromo-molibdeno · 2,2 kg · da 2.800 €'),
    ('Pista', 'Telaio N° 217 · pista', 'Un telaista,', 'una bici alla volta.',
     'Scatto fisso, sterzo rapido, nessun cavo: il telaio più semplice è quello in cui non si può sbagliare niente.',
     V_OFFICINA, 'Tavola 04 · pista', '535', '74°', '390', '530', 'Cromo-molibdeno · 1,7 kg · da 2.200 €'),
]
CARTA = (812.0, 104.0, 568.0, 602.0)      # la tavola sul computer: x, y, larghezza, altezza in px
CARTA_M = (24.0, 150.0, 592.0, 560.0)    # sul telefono, sotto la testata
ORIG = (942.0, 434.0)                     # l'asse posteriore sul computer
ORIG_M = (162.5, 460.0)                   # sul telefono


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def _seg(p, q, spessore, o, tw, th):
    """Un tubo da p a q (px del disegno) come rettangolo ruotato: x, y, w, h in % della tela e l'angolo."""
    x1, y1 = o[0] + p[0], o[1] - p[1]
    x2, y2 = o[0] + q[0], o[1] - q[1]
    L = math.hypot(x2 - x1, y2 - y1)
    cx, cy = (x1 + x2) / 2, (y1 + y2) / 2
    ang = math.degrees(math.atan2(y2 - y1, x2 - x1))
    return _pc(cx - L / 2, cy - spessore / 2, L, spessore, tw, th) + (round(ang, 2),)


def _cerchio(c, r, o, tw, th):
    x, y = o[0] + c[0], o[1] - c[1]
    return _pc(x - r, y - r, 2 * r, 2 * r, tw, th)


def tavola(n, tav, quote, scheda):
    m = K.mob
    out = []
    x, y, w, h = _pc(*CARTA, W, H)
    xm, ym, wm, hm = _pc(*CARTA_M, WM, HM)
    out.append(K.livello('tl-carta%d' % n, tipo='shape', x=x, y=y, w=w, h=h, bgColor=mix(C['nero'], 88), borderRadius=4,
                         borderWidth=1, borderColor=mix(C['chiaro'], 16), entra='fadeIn', ritardo=0, durata=700,
                         esce='fadeOut', boxShadow={'x': 0, 'y': 30, 'blur': 70, 'spread': -20, 'color': mix(C['nero'], 80)},
                         responsive={'mobile': {'x': xm, 'y': ym, 'width': wm, 'height': hm}}))
    tx, ty, tw_, _ = _pc(CARTA[0] + 28, CARTA[1] + 24, CARTA[2] - 56, 20, W, H)
    tmx, tmy, tmw, _ = _pc(CARTA_M[0] + 28, CARTA_M[1] + 24, CARTA_M[2] - 56, 20, WM, HM)
    out.append(K.livello('tl-tav%d' % n, contenuto=tav, x=tx, y=ty, w=tw_, size=12, peso='400', colore=ACCIAIO, letterSpacing=2,
                         textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=150,
                         responsive={'mobile': {'x': tmx, 'y': tmy, 'width': tmw, 'fontSize': m(10)}}))
    # le ruote: copertone, cerchio, quattro aste di raggi che girano, il mozzo
    for k, centro in enumerate((R0, F0)):
        for nome, r, bordo, col in (('gomma', 110, 7, mix(C['chiaro'], 30)), ('cerchio', 100, 2, mix(C['chiaro'], 70))):
            a = _cerchio(centro, r, ORIG, W, H)
            b = _cerchio(centro, r, ORIG_M, WM, HM)
            out.append(K.livello('tl-%s%d-%d' % (nome, n, k), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='transparent',
                                 borderRadius=999, borderWidth=bordo, borderColor=col, entra='zoomIn', ritardo=200 + 120 * k,
                                 durata=800, esce='fadeOut',
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        for j in range(4):
            base = 45 * j
            a = _seg((centro[0] - 100, centro[1]), (centro[0] + 100, centro[1]), 1, ORIG, W, H)
            b = _seg((centro[0] - 100, centro[1]), (centro[0] + 100, centro[1]), 1, ORIG_M, WM, HM)
            out.append(K.livello('tl-raggi%d-%d-%d' % (n, k, j), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3],
                                 bgColor=mix(C['chiaro'], 34), esce='fadeOut',
                                 timeline=K.tl(a[0], a[1], [(0, {'rotation': base, 'opacity': 0}),
                                                            (600, {'rotation': base + 36, 'opacity': 100}),
                                                            (6600, {'rotation': base + 396, 'opacity': 100})], 6600,
                                               ritardo=300),
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a = _cerchio(centro, 9, ORIG, W, H)
        b = _cerchio(centro, 9, ORIG_M, WM, HM)
        out.append(K.livello('tl-mozzo%d-%d' % (n, k), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['chiaro'],
                             borderRadius=999, entra='zoomIn', ritardo=500, durata=500, esce='fadeOut',
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # i tubi si disegnano uno dopo l'altro: la maschera di destra scende da 100 a 0 lungo il tubo
    for k, (nome, p, q, sp, col) in enumerate(TUBI):
        a = _seg(p, q, sp, ORIG, W, H)
        b = _seg(p, q, sp, ORIG_M, WM, HM)
        rit = 500 + 140 * k
        out.append(K.livello('tl-%s%d' % (nome, n), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=99,
                             bgColor=C['chiaro'] if col else ARANCIO, esce='fadeOut',
                             timeline=K.tl(a[0], a[1], [(0, {'rotation': a[4], 'maskRight': 100}, 'power2.out'),
                                                        (rit, {'rotation': a[4], 'maskRight': 100}, 'power2.out'),
                                                        (rit + 500, {'rotation': a[4], 'maskRight': 0})], rit + 500, loop=False),
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # la sella, il manubrio, il movimento centrale
    for nome, p, q, sp in (('sella', (SELLA[0] - 26, SELLA[1] + 2), (SELLA[0] + 22, SELLA[1] + 2), 7),
                           ('piega', (ATTACCO[0], ATTACCO[1]), (ATTACCO[0] + 16, ATTACCO[1] - 26), 4)):
        a = _seg(p, q, sp, ORIG, W, H)
        b = _seg(p, q, sp, ORIG_M, WM, HM)
        out.append(K.livello('tl-%s%d' % (nome, n), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=99,
                             bgColor=C['chiaro'], esce='fadeOut',
                             timeline=K.tl(a[0], a[1], [(0, {'rotation': a[4], 'opacity': 0}), (1800, {'rotation': a[4], 'opacity': 0}),
                                                        (2200, {'rotation': a[4], 'opacity': 100})], 2200, loop=False),
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a = _cerchio(MC, 13, ORIG, W, H)
    b = _cerchio(MC, 13, ORIG_M, WM, HM)
    out.append(K.livello('tl-mc%d' % n, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['nero'], 100),
                         borderRadius=999, borderWidth=3, borderColor=ARANCIO, entra='zoomIn', ritardo=900, durata=500,
                         esce='fadeOut', responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # le quote del modello
    oriz, sterzo, carro, vert = quote
    for k, (testo, p) in enumerate(((oriz + ' mm', (133, 186)), (sterzo, (300, 128)), (carro + ' mm', (24, -46)),
                                    (vert + ' mm', (-8, 72)))):
        a = _pc(ORIG[0] + p[0], ORIG[1] - p[1], 92, 26, W, H)
        b = _pc(ORIG_M[0] + p[0], ORIG_M[1] - p[1], 92, 26, WM, HM)
        out.append(K.livello('tl-q%d-%d' % (n, k), contenuto=testo, x=a[0], y=a[1], w='auto', size=12, peso='700',
                             colore=C['nero'], bgColor=ACCIAIO, padding=7, borderRadius=2, letterSpacing=1, fontFamily=MONO,
                             entra='popUpBack', ritardo=1900 + 160 * k, durata=600,
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(10)}}))
    sx, sy, sw, _ = _pc(CARTA[0] + 28, CARTA[1] + CARTA[2] - 0 + 0, CARTA[2] - 56, 20, W, H)
    sy = round((CARTA[1] + CARTA[3] - 56) / H * 100, 2)
    smx, _, smw, _ = _pc(CARTA_M[0] + 28, 0, CARTA_M[2] - 56, 20, WM, HM)
    smy = round((CARTA_M[1] + CARTA_M[3] - 52) / HM * 100, 2)
    out.append(K.livello('tl-scheda%d' % n, contenuto=scheda, x=sx, y=sy, w=sw, size=12, peso='400',
                         colore=mix(C['chiaro'], 80), letterSpacing=1, fontFamily=MONO, entra='fadeIn', ritardo=2400,
                         responsive={'mobile': {'x': smx, 'y': smy, 'width': smw, 'fontSize': m(10)}}))
    return out


NOMI = {'carro': 'Foderi bassi', 'obliquo-post': 'Foderi alti', 'verticale': 'Tubo verticale', 'orizzontale': 'Tubo orizzontale',
        'obliquo': 'Tubo obliquo', 'sterzo': 'Tubo sterzo', 'forcella': 'Forcella'}
CENTRO_TELAIO = (165.0, 70.0)          # il centro del telaio nel disegno: l'esploso parte da qui


def esploso_di(dx, dy, inizio=0, fine=100):
    """Il pezzo fermo, poi lontano dal centro di (dx, dy) a metà dello scorrimento, poi di nuovo al suo posto."""
    return {'inizio': inizio, 'fine': fine, 'tappe': [{'p': 0}, {'p': 18}, {'p': 52, 'x': dx, 'y': dy}, {'p': 72, 'x': dx, 'y': dy},
                                                      {'p': 96}, {'p': 100}]}


def apertura(t):
    """Il telaio esploso: una sola tavola grande al centro, sul video della saldatura. Lo slider resta fermo in cima
    mentre si scorre: il telaio si smonta (tubi, forcella e ruote si allontanano dal centro, ognuno nella sua direzione,
    e compaiono i loro nomi), resta aperto un momento e si rimonta; alla fine entrano il prezzo e i pulsanti."""
    import math
    m = K.mob
    S, SM = 1.4, 0.92
    cxa, cya = 720.0, 470.0                  # centro della tavola sul computer
    cxm, cym = 320.0, 560.0                   # sul telefono
    # il disegno: coordinate in px (y verso l'alto) moltiplicate per la scala, attorno al centro del telaio
    o = (cxa - CENTRO_TELAIO[0] * S, cya + CENTRO_TELAIO[1] * S)
    om = (cxm - CENTRO_TELAIO[0] * SM, cym + CENTRO_TELAIO[1] * SM)
    sc = lambda pt, k: (pt[0] * k, pt[1] * k)
    livelli = [
        K.livello('tl-carta', tipo='shape', x=14, y=8, w=72, h=86, bgColor=mix(C['nero'], 90), borderRadius=6, borderWidth=1,
                  borderColor=mix(C['chiaro'], 14), entra='fadeIn', ritardo=0, durata=700, esce='fadeOut',
                  boxShadow={'x': 0, 'y': 30, 'blur': 80, 'spread': -20, 'color': mix(C['nero'], 85)},
                  responsive={'mobile': {'x': 3, 'y': 7, 'width': 94, 'height': 86}}),
        K.livello('tl-o', contenuto='Tavola 01 · telaio da strada N° 214', x=17, y=12, w=40, size=12, peso='400',
                  colore=ACCIAIO, letterSpacing=2, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=150,
                  responsive={'mobile': {'x': 7, 'y': 10, 'width': 86, 'fontSize': m(10)}}),
        K.livello('tl-t', contenuto='Ogni tubo,<br><span style="color:var(--olo-color-primary)">al suo posto.</span>', tag='h1',
                  x=17, y=16, w=40, size=58, peso='700', colore=C['chiaro'], lineHeight=1.02, letterSpacing=-2,
                  entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'x': 7, 'y': 12.5, 'width': 86, 'fontSize': m(30)}}),
        K.livello('tl-scorri', contenuto='Scorrete: il telaio si smonta', x=60, y=14, w=23, size=12, peso='400',
                  colore=mix(C['chiaro'], 70), letterSpacing=1, fontFamily=MONO, align='right', entra='fadeIn', ritardo=900,
                  scrollMove={'inizio': 0, 'fine': 20, 'da': {}, 'a': {'o': 0}},
                  responsive={'mobile': {'x': 7, 'y': 22, 'width': 86, 'fontSize': m(10), 'textAlign': 'left'}}),
    ]
    # le ruote: copertone, cerchio, raggi che girano, mozzo; si allontanano ai lati
    for k, centro in enumerate((R0, F0)):
        dx = -180 if k == 0 else 180
        cen, cenm = sc(centro, S), sc(centro, SM)
        mov = esploso_di(dx, 30)
        for nome, r, bordo, col in (('gomma', 110, 7, mix(C['chiaro'], 30)), ('cerchio', 100, 2, mix(C['chiaro'], 70))):
            a_ = _cerchio(cen, r * S, o, W, H)
            b_ = _cerchio(cenm, r * SM, om, WM, HM)
            livelli.append(K.livello('tl-%s-%d' % (nome, k), tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3],
                                     bgColor='transparent', borderRadius=999, borderWidth=bordo, borderColor=col, entra='zoomIn',
                                     ritardo=200 + 120 * k, durata=800, esce='fadeOut', scrollMove=mov,
                                     responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
        for j in range(4):
            base = 45 * j
            a_ = _seg((cen[0] - 100 * S, cen[1]), (cen[0] + 100 * S, cen[1]), 1, o, W, H)
            b_ = _seg((cenm[0] - 100 * SM, cenm[1]), (cenm[0] + 100 * SM, cenm[1]), 1, om, WM, HM)
            livelli.append(K.livello('tl-raggi-%d-%d' % (k, j), tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3],
                                     bgColor=mix(C['chiaro'], 34), esce='fadeOut', scrollMove=mov,
                                     timeline=K.tl(a_[0], a_[1], [(0, {'rotation': base, 'opacity': 0}),
                                                                  (600, {'rotation': base + 36, 'opacity': 100}),
                                                                  (6600, {'rotation': base + 396, 'opacity': 100})], 6600,
                                                   ritardo=300),
                                     responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
        a_ = _cerchio(cen, 9 * S, o, W, H)
        b_ = _cerchio(cenm, 9 * SM, om, WM, HM)
        livelli.append(K.livello('tl-mozzo-%d' % k, tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], bgColor=C['chiaro'],
                                 borderRadius=999, entra='zoomIn', ritardo=500, durata=500, esce='fadeOut', scrollMove=mov,
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
        etichetta = 'Ruota posteriore' if k == 0 else 'Ruota anteriore'
        a_ = _pc(o[0] + cen[0] - 80 + dx, o[1] - cen[1] + 120 * S + 6, 160, 24, W, H)
        b_ = _pc(om[0] + cenm[0] - 80 + dx * 0.44, om[1] - cenm[1] + 120 * SM + 6, 160, 24, WM, HM)
        livelli.append(K.livello('tl-nr-%d' % k, contenuto=etichetta, x=a_[0], y=a_[1], w=a_[2], size=12, peso='700',
                                 colore=ACCIAIO, align='center', fontFamily=MONO, letterSpacing=1, entra='fadeIn', ritardo=0,
                                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 42, 'o': 0},
                                                                                {'p': 54, 'o': 100}, {'p': 72, 'o': 100},
                                                                                {'p': 84, 'o': 0}, {'p': 100, 'o': 0}]},
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'fontSize': m(9)}}))
    # i tubi: ognuno si allontana dal centro del telaio nella sua direzione, col suo nome accanto
    for k, (nome, p0, q0, sp, col) in enumerate(TUBI):
        p, q = sc(p0, S), sc(q0, S)
        pm, qm = sc(p0, SM), sc(q0, SM)
        a_ = _seg(p, q, sp * 1.2, o, W, H)
        b_ = _seg(pm, qm, sp, om, WM, HM)
        mx_, my_ = (p0[0] + q0[0]) / 2 - CENTRO_TELAIO[0], (p0[1] + q0[1]) / 2 - CENTRO_TELAIO[1]
        d = math.hypot(mx_, my_) or 1
        dx, dy = round(mx_ / d * 95), round(-my_ / d * 95)
        rit = 500 + 140 * k
        livelli.append(K.livello('tl-%s' % nome, tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], borderRadius=99,
                                 bgColor=C['chiaro'] if col else ARANCIO, esce='fadeOut', scrollMove=esploso_di(dx, dy),
                                 timeline=K.tl(a_[0], a_[1], [(0, {'rotation': a_[4], 'maskRight': 100}, 'power2.out'),
                                                              (rit, {'rotation': a_[4], 'maskRight': 100}, 'power2.out'),
                                                              (rit + 500, {'rotation': a_[4], 'maskRight': 0})], rit + 500,
                                               loop=False),
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
        if nome in NOMI:
            cxl = o[0] + (p[0] + q[0]) / 2 + dx * 1.9
            cyl = o[1] - (p[1] + q[1]) / 2 + dy * 1.9
            cxlm = om[0] + (pm[0] + qm[0]) / 2 + dx * 1.9 * 0.44
            cylm = om[1] - (pm[1] + qm[1]) / 2 + dy * 1.9 * 0.44
            a_ = _pc(cxl - 80, cyl - 12, 160, 24, W, H)
            b_ = _pc(cxlm - 80, cylm - 12, 160, 24, WM, HM)
            livelli.append(K.livello('tl-n-%s' % nome, contenuto=NOMI[nome], x=a_[0], y=a_[1], w=a_[2], size=12, peso='700',
                                     colore=C['nero'], bgColor=ACCIAIO, padding=6, borderRadius=2, align='center',
                                     fontFamily=MONO, letterSpacing=1, entra='fadeIn', ritardo=0,
                                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 44, 'o': 0},
                                                                                    {'p': 56, 'o': 100}, {'p': 72, 'o': 100},
                                                                                    {'p': 84, 'o': 0}, {'p': 100, 'o': 0}]},
                                     responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'fontSize': m(9)}}))
    # il movimento centrale
    a_ = _cerchio(sc(MC, S), 13 * S, o, W, H)
    b_ = _cerchio(sc(MC, SM), 13 * SM, om, WM, HM)
    livelli.append(K.livello('tl-mc', tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], bgColor=mix(C['nero'], 100),
                             borderRadius=999, borderWidth=3, borderColor=ARANCIO, entra='zoomIn', ritardo=900, durata=500,
                             esce='fadeOut', responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
    # alla fine: il prezzo e i pulsanti
    fine = lambda i, f: {'inizio': i, 'fine': f, 'da': {'y': 40, 'o': 0}, 'a': {}}
    livelli += [
        K.livello('tl-k', contenuto='Cromo-molibdeno · 1,8 kg · sette settimane · da 2.400 €', x=17, y=84, w=42, size=13,
                  peso='400', colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, entra='fadeIn', ritardo=0,
                  scrollMove=fine(80, 94), responsive={'mobile': {'x': 7, 'y': 79, 'width': 86, 'fontSize': m(10)}}),
        K.livello('tl-b', tipo='button', contenuto='Prenota la misurazione', x=58, y=82, w='auto', size=12, peso='700',
                  colore=C['nero'], bgColor=ARANCIO, padding=16, letterSpacing=2, textTransform='uppercase', borderRadius=2,
                  entra='fadeIn', ritardo=0, buttonUrl=SU_MISURA + '#misurazione',
                  hover={'bgColor': C['chiaro'], 'duration': 250}, scrollMove=fine(84, 98),
                  responsive={'mobile': {'x': 7, 'y': 84, 'fontSize': m(11)}}),
    ]
    sl = [K.diapositiva('tl-1', K.sfondo_video(V_SALDA, overlay=0.25, colore=C['nero']), livelli, durata=60000)]
    # 16:9 e non a tutto schermo: la tela resta in proporzione e le ruote restano tonde
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2000, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def modelli(t):
    voci = [('pexels-15211611.jpg', 'Strada', 'Per andare forte e lontano', '545 · 73° · 405', 'da 2.400 €'),
            ('pexels-8934524.jpg', 'Gravel', 'Sterrati, strade bianche, bikepacking', '565 · 71,5° · 430', 'da 2.600 €'),
            ('pexels-17363462.jpg', 'Randonnée', 'Viaggi lunghi con le borse', '560 · 72° · 445', 'da 2.800 €'),
            ('pexels-4828817.jpg', 'Pista', 'Scatto fisso, velodromo e città', '535 · 74° · 390', 'da 2.200 €')]
    return t.classic('worklist', {
        'items': [{'number': '0%d' % (k + 1), 'title': ti, 'category': sub + ' · ' + geo, 'year': pr, 'link_url': TELAI}
                  for k, (f, ti, sub, geo, pr) in enumerate(voci)],
        'divider_color': C['filo'], 'row_padding_y': 28, 'row_hover_bg': mix(ARANCIO, 7), 'hover_indent': 28,
        'number_color': ARANCIO, 'number_size': 14, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 46, 'title_weight': '700', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': ARANCIO, 'year_size': 18, 'show_arrow': True, 'arrow_color': ARANCIO,
        'mono_font_family': MONO}, style={})


def dettagli(t):
    voci = [('pexels-36398681.jpg', 'La forcella dritta', 'Steli tondi, testa saldata a TIG, i forcellini tagliati a mano.'),
            ('pexels-6502511.jpg', 'Il carro', 'Foderi curvati a freddo: la ruota dietro entra giusta, la catena pure.'),
            ('pexels-6496532.jpg', 'I freni a disco', 'Attacchi flat mount saldati sul fodero, il passaggio dei cavi interno.'),
            ('pexels-15822454.jpg', 'Il cambio', 'Meccanico o elettronico: i passaggi li decidiamo insieme sul disegno.')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': 'Dettaglio 0%d' % (k + 1), 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f),
                   'link_url': SU_MISURA, 'color': ARANCIO} for k, (f, n, d) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': ARANCIO,
        'number_hover_color': C['chiaro'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': ARANCIO,
        'name_size': 50, 'name_weight': '700', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': ACCIAIO,
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(ARANCIO, 6), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 280, 'peek_ratio': '4/3', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': ARANCIO, 'author_color': C['tenue'], 'quote_accent_color': ARANCIO, 'quote_font': 'heading',
        'quote_size': 36, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': ARANCIO}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Telaio · Torino, dal 2011', ['Acciaio, fuoco,', 'le tue misure.'], size=64,
                                layout='stack', size_mobile=38)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Matteo costruisce telai da quindici anni, prima per gli altri, poi per conto suo in una '
                                'vecchia officina di Borgo San Paolo. Un telaio richiede sette settimane: una per la '
                                'misurazione e il disegno, due per tagliare e saldare, il resto per la vernice e il '
                                'montaggio.', size=17, color=C['testo']),
                      K.button(t, 'L’officina', OFFICINA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('412', 'Telai consegnati'), ('22', 'All’anno, non di più'), ('7', 'Settimane per un telaio'),
                            ('∞', 'Garanzia sulle saldature')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I telai', ['Quattro famiglie,', 'nessun telaio uguale.'], size=60,
                         tagline='Le quote sono quelle di partenza: orizzontale, sterzo, carro. Poi si cambiano sulle tue misure.')]),
        t.one([modelli(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SCINTILLE, 'La saldatura', 'Ogni giunto <em>lo fa una persona.</em>',
                             'Saldatura a TIG, argento dove serve brasare. Nessun robot, nessuna serie: le saldature si '
                             'vedono, e sono garantite per sempre.', overlay=40))
    out.append(t.section([
        t.one([K.sechead(t, 'I dettagli', ['Le cose piccole', 'che si scelgono.'], size=60,
                         tagline='Passate su una riga: il pezzo si affaccia.')]),
        t.one([dettagli(t)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Avevo una schiena che con le bici di serie dopo cento chilometri smetteva di collaborare. Matteo mi ha misurato '
           'per due ore. Adesso faccio i duecento e scendo dalla bici solo perché ho fame.', 'Silvia Martinetto',
        'Randonnée N° 188, 2025')])], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_GRAVEL, 'La misurazione', 'Due ore <em>sulla bici di prova.</em>',
                          'Si comincia sempre così: la bici regolabile, le tue misure, il modo in cui pedali. Poi il disegno.',
                          'Prenota la misurazione', SU_MISURA + '#misurazione', overlay=40, video=True))
    return out


def build_telai():
    t = Tpl(ID, 'telai')
    out = [K.intro(t, 'I telai', ['Quattro famiglie,', 'tutte su misura.'],
                   'Partiamo da una geometria che conosciamo bene e la cambiamo sulle tue misure e sul modo in cui usi la bici.',
                   bg=K.photo_bg('pexels-31628832.jpg'), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le famiglie', ['Strada, gravel,', 'randonnée, pista.'], size=56)]),
        t.one([modelli(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Telaio, bici,', 'o viaggio.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Solo telaio', '2.200', 'telaio e forcella', 'Misurazione e disegno\nTelaio e forcella in acciaio\nVerniciatura '
             'in un colore', False, ''),
            ('Bici completa', '4.200', 'pronta da pedalare', 'Tutto il telaio\nGruppo, ruote e componenti scelti insieme\n'
             'Montaggio e prima regolazione', True, 'la più scelta'),
            ('Viaggio', '5.400', 'randonnée attrezzata', 'Tutta la bici completa\nParafanghi e portapacchi su misura\nLuci a '
             'dinamo e borse', False, ''),
        ], 'Prenota la misurazione', SU_MISURA + '#misurazione', nota='I prezzi sono di partenza: il preventivo esatto arriva '
           'col disegno.'),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Sulla strada', ['Dove vanno', 'a finire.'], size=56,
                         tagline='Trascinate o lasciate scorrere: le foto le mandano i ciclisti.')]),
        t.one([K.coverflow(t, [('pexels-17363454.jpg', 'Un ciclista in salita fra le case di montagna', 'Strada', 'Valle di Susa'),
                               ('pexels-8841821.jpg', 'Una strada che attraversa una valle verde', 'Randonnée', 'Norvegia'),
                               ('pexels-8934524.jpg', 'Un ciclista su uno sterrato fra i cespugli', 'Gravel', 'Langhe'),
                               ('pexels-17363462.jpg', 'Un ciclista sulla strada nei prati', 'Strada', 'Val Chisone'),
                               ('pexels-15211612.jpg', 'Una bici nera parcheggiata in città', 'Pista', 'Torino')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di ordinare.', [
        ('Perché l’acciaio?', 'Perché dura una vita, si ripara, assorbe le vibrazioni della strada e si può modificare anche '
         'dopo anni.'),
        ('Pesa di più del carbonio?', 'Un telaio pesa da 1,7 a 2,2 kg: qualche etto in più, che sulla strada non si sente.'),
        ('Si può ritoccare dopo?', 'Sì: un attacco in più, un portaborraccia, una verniciatura nuova. Lo facciamo in officina.'),
    ], 'tl-faq'))
    return out


def configuratore(t):
    def opz(i, g, nome, desc, prezzo, pred=False, badge=''):
        return {'id': i, 'gruppo': g, 'nome': nome, 'descrizione': desc, 'prezzo': prezzo, 'canone': 0, 'immagine': '',
                'badge': badge, 'predefinita': pred, 'punti': '', 'min': 0, 'max': 1}

    def gruppo(i, titolo, tipo='uno', obbl=True, aiuto=''):
        return {'id': i, 'titolo': titolo, 'blocco': '', 'tipo': tipo, 'obbligatorio': obbl, 'aiuto': aiuto}
    return t.classic('configuratore', {
        'gruppi': [gruppo('famiglia', 'La famiglia'), gruppo('tubi', 'I tubi', aiuto='Più leggeri = più vivaci, più spessi '
                                                                                     '= più comodi coi pesi.'),
                   gruppo('vernice', 'La vernice'), gruppo('extra', 'Da aggiungere', tipo='piu', obbl=False)],
        'opzioni': [
            opz('strada', 'famiglia', 'Strada', 'Per andare forte e lontano.', 2400, pred=True),
            opz('gravel', 'famiglia', 'Gravel', 'Gomme fino a 50 mm, carro lungo.', 2600, badge='Il più chiesto'),
            opz('randonnee', 'famiglia', 'Randonnée', 'Per i viaggi lunghi con le borse.', 2800),
            opz('pista', 'famiglia', 'Pista', 'Scatto fisso, niente cavi.', 2200),
            opz('tubi-std', 'tubi', 'Cromo-molibdeno classico', 'Il tubo di tutti i giorni, robusto.', 0, pred=True),
            opz('tubi-leggeri', 'tubi', 'Tubi a spessore variabile', 'Più sottili al centro, circa 300 g in meno.', 380),
            opz('tubi-inox', 'tubi', 'Acciaio inossidabile', 'Si lascia anche senza vernice, non arrugginisce.', 900),
            opz('vernice-1', 'vernice', 'Un colore', 'Polvere, opaca o lucida.', 0, pred=True),
            opz('vernice-2', 'vernice', 'Due colori e filetti', 'Le congiunzioni in un altro colore.', 260),
            opz('vernice-arte', 'vernice', 'A mano, come lo disegnate', 'Sfumature, scritte, il vostro nome sul tubo.', 650),
            opz('portapacchi', 'extra', 'Portapacchi su misura', 'Saldato per quel telaio, in acciaio.', 340),
            opz('parafanghi', 'extra', 'Attacchi per parafanghi', 'Invisibili quando non servono.', 60),
            opz('dinamo', 'extra', 'Cavi della dinamo interni', 'Le luci senza fili a vista.', 120),
            opz('borraccia', 'extra', 'Terzo portaborraccia', 'Sotto l’obliquo, per i viaggi lunghi.', 40),
        ],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'Il tuo telaio', 'testo_canone': '',
        'nota_prezzo': 'IVA compresa. Il prezzo esatto arriva col disegno, dopo la misurazione.', 'periodo': '',
        'mostra_differenze': True, 'riepilogo': True, 'titolo_riepilogo': 'Il tuo telaio', 'a_passi': False,
        'testo_azzera': 'Ricomincia', 'testo_pulsante': 'Prenota la misurazione', 'link_azione': '#misurazione',
        'campo_modulo': 'telaio', 'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': C['sala'],
        'colore_scelta': ARANCIO, 'colore_testo': C['testo'], 'colore_tenue': C['tenue'], 'colore_prezzo': ARANCIO,
        'sfondo_riepilogo': C['sala'], 'sfondo_badge': ACCIAIO, 'colore_badge': C['nero'], 'raggio_scheda': radius(2),
        'gruppo_color': C['testo'], 'gruppo_font_weight': '700', 'totale_color': ARANCIO, 'totale_font_weight': '700'},
        style={})


def build_su_misura():
    t = Tpl(ID, 'su-misura')
    out = [K.intro(t, 'Su misura', ['Sette settimane,', 'dalle misure alla strada.'],
                   'La misurazione, il disegno, il taglio e la saldatura, la vernice, il montaggio. Ogni passo lo vedi: ti '
                   'mandiamo le foto dal banco.', bg=K.video_bg(V_SALDA), size=96)]
    fasi = [('01', 'Settimana 1', 'La misurazione', 'Due ore sulla bici di prova: misure, flessibilità, il modo in cui '
             'pedali.', 'pexels-13122870.jpg'),
            ('02', 'Settimana 1', 'Il disegno', 'La tavola del telaio con le quote: la firmi tu prima che si tagli un tubo.',
             'pexels-40002193.jpg'),
            ('03', 'Settimane 2 — 4', 'Taglio e saldatura', 'Tubo per tubo, sulla dima. Le saldature si vedono e restano.',
             'pexels-31628832.jpg'),
            ('04', 'Settimane 5 — 7', 'Vernice e montaggio', 'Sabbiatura, polvere, decalcomanie. Poi il montaggio e la '
             'prima regolazione.', 'pexels-132682.jpg')]
    tappe = t.classic('step-timeline', {
        'items': [{'counter': n, 'tag_text': quando, 'tag_dot_color': ARANCIO, 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['sala'], 'media_color': ARANCIO, 'pre_title': '',
                   'title': titolo, 'title_accent': '', 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': '', 'footer_label': '', 'separator_text': ''}
                  for n, quando, titolo, d, f in fasi],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': ARANCIO, 'timeline_dot_size': 10,
        'timeline_height': 1, 'timeline_margin_bottom': 40, 'counter_font_family': 'heading', 'counter_size': 40,
        'counter_color': ARANCIO, 'counter_italic': False, 'counter_weight': '700', 'tag_size': 11, 'tag_color': C['tenue'],
        'media_aspect_ratio': '4/3', 'media_object_position': 'center center', 'media_radius': radius(2),
        'media_radius_hover': radius(2), 'media_shadow': 'none', 'show_media_label': False, 'pre_title_size': 11,
        'pre_title_color': ARANCIO, 'title_font_family': 'heading', 'title_size': 22, 'title_weight': '700',
        'title_color': C['testo'], 'description_color': C['tenue']})
    out.append(t.section([
        t.one([K.sechead(t, 'Il percorso', ['Quattro passi,', 'una bici sola.'], size=56)]),
        t.one([tappe]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Componi il telaio', ['Scegli, il prezzo', 'si aggiorna.'], size=56,
                         tagline='La famiglia, i tubi, la vernice e quello che vuoi aggiungere: il telaio composto arriva nel '
                                 'modulo della misurazione.')]),
        t.one([configuratore(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='componi', style={'padding_top': 130, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('text', 'Altezza e cavallo (cm)', 'misure', segnaposto='Per esempio 178 e 84'),
             K.campo('select', 'Come usi la bici', 'uso', True,
                     opzioni='Strada e granfondo\nSterrati e bikepacking\nViaggi con le borse\nCittà e pista'),
             K.campo('text', 'Il telaio composto', 'telaio', param='telaio', larghezza='1-1',
                     segnaposto='Se l’hai composto sopra, lo trovi già qui'),
             K.campo('textarea', 'Cosa vorresti, cosa ti dà fastidio adesso', 'note', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'misurazione', 'La misurazione', ['Due ore,', 'in officina.'],
                                'La misurazione costa 120 €, che si scalano dal telaio se lo ordini.', VOCI, campi,
                                'Telaio — misurazione', 'Prenota la misurazione', 'Ricevuta. Ti chiamo per fissare il giorno.'))
    return out


def build_officina():
    t = Tpl(ID, 'officina')
    out = [K.intro(t, 'L’officina', ['Un banco, una dima,', 'una persona.'],
                   'Borgo San Paolo, dove a Torino si facevano le automobili. L’officina era di un tornitore: il tornio è '
                   'rimasto.', bg=K.video_bg(V_OFFICINA), size=96)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-6334127.jpg', 'Matteo controlla il manubrio di una bici col telaio rosso',
                              ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Matteo Gilardi', ['Prima meccanico,', 'poi telaista.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Ho cominciato riparando bici in un negozio di San Salvario. Poi un corso di saldatura, tre anni '
                                'da un telaista di Bergamo e nel 2011 l’officina. Faccio ventidue telai all’anno: di più, non '
                                'riuscirei a guardarli tutti come si deve.', size=17),
                      K.specs(t, [('In officina dal', '2011'), ('Telai consegnati', '412'), ('Il più lungo', '64 cm, '
                                  'randonnée'), ('Il più piccolo', '44 cm, pista')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    foto = [('pexels-31628832.jpg', 'Telai colorati appesi alle pareti dell’officina'),
            ('pexels-132682.jpg', 'Una bici sul cavalletto da lavoro'),
            ('pexels-13122870.jpg', 'Mani che montano la catena'),
            ('pexels-7936509.jpg', 'La forcella e i raggi da vicino')]
    out.append(t.section([
        t.one([K.sechead(t, 'Il banco', ['Dove nasce', 'il telaio.'], size=56)]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5', lightbox=True)], wm='1-4', ws='1-2') for f, alt in foto],
              layout='25-25-25-25', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SALDA, 'Il TIG', 'Due millimetri <em>di arco.</em>',
                             'La saldatura a TIG è lenta, e va bene così: il calore resta dove serve, il tubo non si deforma.',
                             overlay=40))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Passa in officina,', 'c’è sempre il caffè.'],
                   'Per una misurazione, un preventivo, una riparazione su un telaio in acciaio, anche non nostro.',
                   bg=K.photo_bg('pexels-132682.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'cosa', True,
                     opzioni='Un telaio nuovo\nUna misurazione\nUna riparazione\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivi', ['Una riga,', 'poi parliamo.'],
                                'Rispondo la sera, quando la saldatrice è spenta.', VOCI, campi, 'Telaio — contatto', 'Invia',
                                'Ricevuto. Ti rispondo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’officina', ['Via Frejus 31,', 'Borgo San Paolo.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 9 — 18'), ('Misurazioni', 'su appuntamento'),
                                  ('Metro', 'Racconigi, cinque minuti a piedi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Telaio · l’officina', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='officina', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori quota', color=ARANCIO, align='center'),
                       K.headline(t, 'Questa pagina <em>non è in tavola.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=ARANCIO),
                       K.text(t, 'Le misure non tornano. Ripartiamo dall’inizio.', size=17, align='center',
                              color=mix(C['chiaro'], 84)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SCINTILLE, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Industria e manifattura', 'Il telaio esploso',
    ['bici', 'telai', 'su misura', 'acciaio', 'artigiano', 'officina', 'ciclismo', 'scuro', 'slider pro'],
    'Un’officina che costruisce telai da bici in acciaio su misura, nero officina e arancio di fondo: in apertura la '
    'tavola del telaio sul video della saldatura, che scorrendo si smonta nei suoi tubi, coi loro nomi, e si '
    'rimonta; le famiglie dei telai, il percorso in sette settimane, il configuratore col prezzo e la misurazione.',
    [('I telai', TELAI), ('Su misura', SU_MISURA), ('L’officina', OFFICINA), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('telai', 'I telai', 'telai'), ('su-misura', 'Su misura', 'su-misura'),
     ('officina', 'L’officina', 'officina'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Hanken Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Hanken Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Space Mono\', ui-monospace, Menlo, monospace', ['Hanken Grotesk', 'Space Mono'], corsivo_di=[],
    peso_titoli='700', raggio=2, grana=4, titoli_line='1.02', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'testo'
THEME['cursor']['label_text'] = 'Su misura · in acciaio · '


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'telai.json': build_telai(), 'su-misura.json': build_su_misura(), 'officina.json': build_officina(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
