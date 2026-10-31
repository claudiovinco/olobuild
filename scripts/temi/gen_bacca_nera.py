# -*- coding: utf-8 -*-
"""Genera il tema «Bacca Nera» (id bacca-nera): una distilleria di gin a Bormio, in alta Valtellina: ginepro raccolto a
duemila metri, quattordici botaniche, tre distillazioni in un alambicco di rame, visite e degustazioni.

Tema scuro: verde bosco, ottone, il blu viola delle bacche di ginepro e la salvia. Titoli in Brygada 1918 (anche
corsivo), testo in Afacad, dati in Sometype Mono.

Apertura: Slider Pro «il giro delle botaniche», alto 16:9 perché i cerchi restino cerchi. Il bicchiere in una finestra
ad arco e, attorno, cinque botaniche (ginepro, anice stellato, cardamomo, limone, cannella) in tondi che girano su
un'orbita ellittica inclinata, ognuna partita da un punto diverso del giro (percorsi di movimento). Una diapositiva per
gin, a linguette: cambia il bicchiere, le botaniche continuano a girare.
"""
import math
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'bacca-nera'
NAME = 'Bacca Nera'

HEX = {
    'primary': '#D4A35A', 'primary_contrast': '#0B1512', 'secondary': '#5B6FA8', 'secondary_contrast': '#FFFFFF',
    'accent': '#9CC5A1', 'dark': '#060C0A', 'light': '#F1F0E8', 'muted': '#12201B', 'muted_contrast': '#E8EDE6',
    'text': '#E8EDE6', 'text_muted': '#9DAEA5', 'background': '#0B1512', 'border': '#22352E', 'link': '#D4A35A',
}

HOME, GIN, DISTILLERIA, VISITE = '{{PAGINA:home}}', '{{PAGINA:i-gin}}', '{{PAGINA:la-distilleria}}', '{{PAGINA:visite}}'
MAIL, PHONE, PHONE_TEL = 'alambicco@baccanera.example', '+39 0342 000 220', 'tel:+390342000220'
LAT, LNG = '46.4669', '10.3707'

V_ROSMARINO, V_SCORZA, V_GHIACCIO, V_BOTTE, V_SALA = ('pexels-video-5816530', 'pexels-video-7219891', 'pexels-video-7593544',
                                                      'pexels-video-7192640', 'pexels-video-38503948')
PHOTOS = [
    'pexels-3039671.jpg', 'pexels-12958451.jpg', 'pexels-6988421.jpg', 'pexels-37109031.jpg', 'pexels-22882572.jpg',
    'pexels-30688213.jpg', 'pexels-5667551.jpg', 'pexels-9557794.jpg', 'pexels-11216107.jpg', 'pexels-32606563.jpg',
    'pexels-6202411.jpg', 'pexels-36745315.jpg', 'pexels-37568646.jpg', 'pexels-30705538.jpg', 'pexels-15347180.jpg',
    'pexels-9974499.jpg', 'pexels-39150949.jpg', 'pexels-8450505.jpg', 'pexels-28792582.jpg', 'pexels-29732300.jpg',
    'pexels-32075440.jpg', 'pexels-35105786.jpg', 'pexels-1267311.jpg', 'pexels-9130342.jpg', 'pexels-31893699.jpg',
    'pexels-9566432.jpg', 'pexels-30006382.jpg', 'pexels-1405667.jpg', 'pexels-13373185.jpg', 'pexels-1002543.jpg',
    'pexels-33459702.jpg', 'pexels-38270189.jpg', 'pexels-9199091.jpg', 'pexels-19118707.jpg', 'pexels-18117614.jpg',
    'pexels-27953366.jpg', 'pexels-17151136.jpg', 'pexels-8985443.jpg', 'pexels-20676810.jpg',
]
VIDEOS = [V_ROSMARINO, V_SCORZA, V_GHIACCIO, V_BOTTE, V_SALA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True)
C = K.c
OTTONE, SALVIA = C['acc'], C['acc2']
TITOLI, MONO = 'Brygada 1918', 'Sometype Mono'
VOCI = [('map-pin', 'Via dei Masi 12 · Bormio', VISITE + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Bacca Nera', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Bacca Nera', 'Distilleria di gin in alta Valtellina dal 2018: ginepro raccolto a mano sopra i duemila '
                   'metri, quattordici botaniche, tre distillazioni nell’alambicco di rame. Visite e degustazioni su prenotazione.',
                   [('I gin', GIN), ('La distilleria', DISTILLERIA), ('Visite', VISITE), ('Le ricette', HOME + '#ricette'),
                    ('Privacy', '/privacy-policy/')],
                   'La distilleria', 'Mercoledì — domenica 10 — 19<br>Visite alle 11 e alle 16', VOCI,
                   '© 2026 Bacca Nera srl · Bormio · P.IVA 00000000000 · Bevete responsabilmente',
                   [('instagram', 'https://instagram.com')], marchio_size=120)


GINS = [  # linguetta, occhiello, riga 1, riga 2, testo, gradi, finestra (foto|video)
    ('Alpino', 'N° 1 · London dry', 'Il ginepro', 'di duemila metri.', 'Il nostro gin di sempre: ginepro selvatico, '
     'pino mugo, genziana e scorza di limone. Secco, resinoso, fresco come l’aria del passo.', '43% vol',
     ('foto', 'pexels-6988421.jpg')),
    ('Agrumato', 'N° 2 · d’estate', 'Limone, bergamotto', 'e un rametto di timo.', 'Le scorze grattugiate a mano la '
     'mattina della distillazione. Nel gin tonic con una fetta di pompelmo rosa.', '41% vol', ('foto', 'pexels-3039671.jpg')),
    ('Rosso', 'N° 3 · per il Negroni', 'Rosmarino', 'e bacche rosse.', 'Rosa canina, mirtillo rosso e rosmarino dell’orto: '
     'un gin rosato che regge il bitter e il vermut senza sparire.', '42% vol', ('video', V_ROSMARINO)),
    ('Riserva', 'N° 4 · in botte', 'Sei mesi', 'nel legno di larice.', 'L’Alpino che riposa in piccole botti di larice: '
     'diventa ambrato, morbido, profuma di resina e miele. Da bere liscio.', '45% vol', ('foto', 'pexels-12958451.jpg')),
]
BOTANICHE = [('pexels-30688213.jpg', 'Ginepro'), ('pexels-36745315.jpg', 'Anice stellato'), ('pexels-37568646.jpg', 'Cardamomo'),
             ('pexels-1002543.jpg', 'Limone'), ('pexels-30705538.jpg', 'Cannella')]


def apertura(t):
    m = K.mob
    W, H = 1440.0, 810.0
    ax, ay, aw, ah = 19.0, 14.0, 20.83, 66.67           # l'arco: 300 × 540 px, a sinistra
    sl = []
    for i, (tab, occ, r1, r2, testo, gradi, media) in enumerate(GINS):
        n = i + 1
        arco = dict(x=ax, y=ay, w=aw, h=ah, borderRadiusLinked=False, borderRadiusTL=150, borderRadiusTR=150,
                    borderRadiusBR=6, borderRadiusBL=6, entra='maskFromBottom', ritardo=100, durata=1300, esce='fadeOut',
                    boxShadow={'x': 0, 'y': 40, 'blur': 90, 'spread': -20, 'color': mix(OTTONE, 22)},
                    responsive={'mobile': {'x': 26, 'y': 5, 'width': 48, 'height': 30}})
        if media[0] == 'video':
            finestra = K.livello('bn-a%d' % n, tipo='video', videoSrc=K.img(media[1] + '.mp4'), videoAutoplay=True,
                                 videoMuted=True, videoLoop=True, **arco)
        else:
            finestra = K.livello('bn-a%d' % n, tipo='image', imageSrc=K.img(media[1]), objectFit='cover',
                                 kenBurns={'type': 'zoomIn', 'intensity': 8, 'duration': 9000}, **arco)
        sl.append(K.diapositiva('bn-%d' % n, K.sfondo_video([V_SALA, V_SCORZA, V_GHIACCIO, V_BOTTE][i], overlay=0.8,
                                                            colore=C['fondo']), [
            finestra,
            K.livello('bn-o%d' % n, contenuto=occ, x=53.5, y=18, w=40, size=12, peso='400', colore=OTTONE, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 41, 'x': 7, 'width': 86}}),
            K.livello('bn-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=53.5, y=22.5, w=43, size=62, peso='500',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='fadeIn', ritardo=350, durata=1200,
                      charAnim={'stagger': 26, 'offsetY': 30, 'blur': 8, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(35), 'y': 43.5, 'x': 7, 'width': 88}}),
            K.livello('bn-i%d' % n, contenuto=r2, tag='p', x=53.5, y=32, w=43, size=62, peso='500', fontStyle='italic',
                      colore=SALVIA, fontFamily=TITOLI, lineHeight=1.04, entra='fadeIn', ritardo=650, durata=1200,
                      charAnim={'stagger': 26, 'offsetY': 30, 'blur': 8, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(35), 'y': 47.8, 'x': 7, 'width': 88}}),
            K.livello('bn-s%d' % n, contenuto=testo, x=53.5, y=46, w=34, size=17, peso='400', colore=mix(C['chiaro'], 80),
                      lineHeight=1.65, ritardo=1000, responsive={'mobile': {'fontSize': m(14), 'y': 53.5, 'x': 7, 'width': 86}}),
            K.livello('bn-b%d' % n, tipo='button', contenuto='Prenota una visita', x=53.5, y=67, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=OTTONE, padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=4, ritardo=1300, buttonUrl=VISITE, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 68, 'x': 7, 'fontSize': m(11)}, 'tablet': {'y': 69}}),
            K.livello('bn-g%d' % n, contenuto=gradi, x=70, y=68, w='auto', size=12, peso='400', colore=C['chiaro'],
                      fontFamily=MONO, letterSpacing=1, padding=12, borderRadius=999, borderWidth=1,
                      borderColor=mix(C['chiaro'], 40), entra='popUpBack', ritardo=1550,
                      responsive={'mobile': {'y': 68.6, 'x': 60, 'fontSize': m(11)}, 'tablet': {'x': 74.5, 'y': 70}}),
        ], durata=8000, tab=tab))
    # l'orbita inclinata delle botaniche attorno all'arco
    cx, cy, rx, ry, th, sz = (ax + aw / 2) * W / 100, 52 * H / 100, 330.0, 105.0, -8.0, 84.0
    globali = [K.livello('bn-orbita', tipo='shape', x=round((cx - rx) / W * 100, 2), y=round((cy - ry) / H * 100, 2),
                         w=round(2 * rx / W * 100, 2), h=round(2 * ry / H * 100, 2), bgColor='transparent', borderRadius=999,
                         borderWidth=1, borderStyle='dashed', borderColor=mix(OTTONE, 45),
                         timeline=K.tl(round((cx - rx) / W * 100, 2), round((cy - ry) / H * 100, 2),
                                       [(0, {'rotation': th, 'opacity': 0}), (1600, {'rotation': th, 'opacity': 100})], 1600,
                                       loop=False),
                         responsive={'mobile': {'visible': False}})]
    t_ = math.radians(th)
    for k, (foto, nome) in enumerate(BOTANICHE):
        phi = math.radians(90 + 72 * k)
        xe, ye = rx * math.cos(phi), ry * math.sin(phi)
        X, Y = xe * math.cos(t_) - ye * math.sin(t_), xe * math.sin(t_) + ye * math.cos(t_)
        px_, py_ = (cx + X - sz / 2) / W * 100, (cy + Y - sz / 2) / H * 100
        percorso = 'M 0 0 a %d %d %d 1 1 %.1f %.1f a %d %d %d 1 1 %.1f %.1f' % (rx, ry, th, -2 * X, -2 * Y, rx, ry, th,
                                                                               2 * X, 2 * Y)
        globali.append(K.livello('bn-bot%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=round(px_, 2),
                                 y=round(py_, 2), w=round(sz / W * 100, 2), h=round(sz / H * 100, 2), borderRadius=999,
                                 borderWidth=3, borderColor=mix(OTTONE, 70), entra='zoomIn', ritardo=900 + 160 * k, durata=700,
                                 boxShadow={'x': 0, 'y': 14, 'blur': 30, 'spread': -8, 'color': mix(C['nero'], 70)},
                                 motionPath={'path': percorso, 'duration': 26000, 'autoRotate': False, 'loop': True,
                                             'easing': 'linear'},
                                 responsive={'mobile': {'visible': False}}))
    globali.append(K.livello('bn-quattordici', contenuto='Quattordici botaniche · tre distillazioni · lotti da 600 bottiglie',
                             x=53.5, y=86, w=44, size=11, peso='400', colore=mix(C['chiaro'], 60), letterSpacing=2,
                             textTransform='uppercase', fontFamily=MONO, align='left', entra='fadeIn', ritardo=1800,
                             responsive={'mobile': {'y': 76, 'x': 7, 'width': 86, 'fontSize': m(10)}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='slideUp',
                       durata_trans=1100, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=OTTONE, navColor=C['chiaro'])


def botaniche(t):
    voci = [('Ginepro', '2.200 m · Valle dello Zebrù', 'Raccolto a mano a settembre, una bacca su tre: le altre restano agli '
             'uccelli e alla pianta.', 'pexels-30688213.jpg'),
            ('Pino mugo', 'Gemme di maggio', 'Le gemme verdi del mugo, colte in primavera: danno il fresco resinoso.',
             'pexels-6202411.jpg'),
            ('Genziana', 'Radice · Passo dello Stelvio', 'L’amaro di montagna, un grammo per litro: basta per tenere insieme '
             'tutto il resto.', 'pexels-8450505.jpg'),
            ('Agrumi', 'Limone e bergamotto', 'Scorze grattugiate a mano la mattina della distillazione, mai essiccate.',
             'pexels-38270189.jpg'),
            ('Spezie', 'Cardamomo e anice stellato', 'Pochissime, dal mercante di Milano da cui comprava il nonno.',
             'pexels-39150949.jpg'),
            ('Bacche nere', 'Mirtillo e camarina', 'Le bacche scure del sottobosco che danno il nome alla distilleria.',
             'pexels-9557794.jpg')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '%02d' % (k + 1), 'image': K.img(f), 'link_url': GIN + '#botaniche',
                   'color': OTTONE} for k, (n, s, d, f) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': C['tenue'],
        'number_hover_color': OTTONE, 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': OTTONE,
        'name_size': 48, 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': SALVIA, 'sub_size': 12,
        'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 24, 'hover_indent': 28,
        'hover_bg': mix(OTTONE, 7), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image', 'peek_width': 240,
        'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def ricette(t):
    cols = []
    for nome, dose, foto in [
        ('Gin tonic alpino', '5 cl di Alpino, 15 cl di acqua tonica secca, ghiaccio pieno, una scorza di limone e tre bacche '
                             'di ginepro schiacciate.', 'pexels-6988421.jpg'),
        ('Negroni rosso', '3 cl di Rosso, 3 cl di bitter, 3 cl di vermut rosso. Mescolato sul ghiaccio, scorza d’arancia.',
         'pexels-3039671.jpg'),
        ('Martini di montagna', '6 cl di Alpino, 1 cl di vermut secco, mescolato a lungo. Una gemma di pino mugo.',
         'pexels-12958451.jpg'),
        ('Collins al bergamotto', '5 cl di Agrumato, 2 cl di limone, 1,5 cl di sciroppo di miele, soda fredda.',
         'pexels-22882572.jpg')]:
        cols.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 58}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': 'La ricetta · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': nome, 'back_description': dose,
            'back_bg': C['sala'], 'back_text_color': C['testo'], 'back_valign': 'center', 'back_cta_text': '',
            'back_cta_url': '', 'flip_direction': 'horizontal', 'flip_trigger': 'hover', 'card_height': '380',
            'card_border_radius': '4', 'card_shadow': 'md', 'title_size': '26', 'title_weight': '500', 'desc_size': '15'},
            style={})], wm='1-4', ws='1-1'))
    return t.row(cols, layout='25-25-25-25', gap=18)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Bacca Nera · Bormio', ['Un alambicco di rame,', 'quattordici botaniche.'], size=60,
                                layout='stack', size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Il ginepro lo raccogliamo a mano a settembre, sopra i duemila metri; le altre botaniche '
                                'vengono dai boschi della valle o da un mercante di spezie di Milano. Tre distillazioni '
                                'lente nell’alambicco di rame, poi un mese di riposo in vetro prima dell’imbottigliamento.',
                             size=17, color=C['testo']),
                      K.button(t, 'La distilleria', DISTILLERIA, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('14', 'Botaniche'), ('2.200 m', 'Dove nasce il ginepro'), ('3', 'Distillazioni lente'),
                            ('600', 'Bottiglie a lotto')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le botaniche', ['Sei famiglie,', 'una valle.'], size=60,
                         tagline='Passate su una riga: la botanica si affaccia. Da dove viene, quando si raccoglie, che cosa dà al gin.')]),
        t.one([botaniche(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_ROSMARINO, 'Il Rosso', 'Il rosmarino, <em>appena bruciato.</em>',
                             'Un rametto acceso col cerino e spento nel bicchiere: il fumo resta sopra il Negroni, il profumo '
                             'arriva prima del primo sorso.', overlay=48))
    out.append(t.section([
        t.one([K.sechead(t, 'Le ricette', ['Quattro bicchieri', 'da girare.'], size=60,
                         tagline='Passate sopra un cocktail, o toccatelo: dietro ci sono le dosi del nostro bancone.')]),
        ricette(t),
    ], padding='large', width='large', html_id='ricette', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Siamo saliti per la visita e siamo rimasti tutto il pomeriggio. Il gin tonic con l’Alpino sa di bosco dopo '
                 'la pioggia: adesso a casa non beviamo altro.', 'author_name': 'Chiara e Andrea',
        'author_role': 'Visita in distilleria, agosto 2026', 'avatar': '', 'rating': '5', 'text_color': C['testo'],
        'star_color': OTTONE, 'author_color': C['tenue'], 'quote_accent_color': OTTONE, 'quote_font': 'heading',
        'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': OTTONE}, style={})])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_SALA, 'La distilleria', 'Tre distillazioni, <em>nessuna fretta.</em>',
                          'L’alambicco lavora otto ore per ogni passaggio, a fuoco basso: teniamo solo il cuore del '
                          'distillato, la testa e la coda tornano alla terra.', 'Prenota una visita', VISITE, overlay=60,
                          video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Bacca Nera',
                page_intro_caption='Distilleria · Bormio', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def build_gin():
    t = Tpl(ID, 'i-gin')
    out = [K.intro(t, 'I gin', ['Quattro bottiglie,', 'una montagna.'],
                   'Si comprano in distilleria o ve le spediamo in tutta Italia, nella cassetta di larice. Lotti numerati a mano.',
                   bg=K.photo_bg('pexels-29732300.jpg', overlay=62), overlay_text=True)]
    listino = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in [
                      ('Alpino · 70 cl', 'London dry, 43% · ginepro, mugo, genziana, limone', '42 €', 'pexels-6988421.jpg', 'Il primo'),
                      ('Agrumato · 70 cl', '41% · limone, bergamotto, timo serpillo', '44 €', 'pexels-3039671.jpg', ''),
                      ('Rosso · 70 cl', '42% · rosa canina, mirtillo rosso, rosmarino', '46 €', 'pexels-22882572.jpg', ''),
                      ('Riserva · 50 cl', '45% · sei mesi in botti di larice', '58 €', 'pexels-12958451.jpg', 'Lotto 07'),
                      ('Cassetta da tre · 3 × 20 cl', 'Alpino, Agrumato e Rosso, da provare', '39 €', 'pexels-37109031.jpg', '')]],
        'separator_style': 'dotted', 'separator_color': mix(OTTONE, 40), 'title_color': C['testo'], 'price_color': OTTONE,
        'description_color': C['tenue'], 'image_size': 76, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': SALVIA, 'badge_color': C['nero'], 'badge_border_radius': '4',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['In distilleria', 'o a casa vostra.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Spedizione gratuita da due bottiglie. Per i ristoranti e i bar c’è il listino professionale: '
                                'scriveteci.')], wm='2-5', ws='1-1'),
               t.col([listino], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    vicino = t.classic('provamateriale', {
        'voci': [{'id': 'bn-p%d' % k, 'nome': n, 'codice': c, 'testo': te, 'immagine': K.img(f), 'miniatura': '',
                  'colore': '', 'alt': n} for k, (n, c, te, f) in enumerate([
                      ('Ginepro', 'Juniperus communis', 'La bacca matura al secondo anno, quando diventa blu notte: è lì che '
                       'ha tutto l’olio essenziale.', 'pexels-30688213.jpg'),
                      ('Anice stellato', 'Illicium verum', 'Una stella sola ogni dieci litri: dà la dolcezza che arrotonda il '
                       'ginepro.', 'pexels-36745315.jpg'),
                      ('Cardamomo', 'Elettaria cardamomum', 'I semi dentro il baccello verde: fresco, quasi balsamico.',
                       'pexels-37568646.jpg'),
                      ('Cannella', 'Cinnamomum verum', 'La corteccia sottile di Ceylon, non la cassia: calda ma leggera.',
                       'pexels-30705538.jpg'),
                      ('Pimento', 'Pimenta dioica', 'Il pepe della Giamaica, solo nella Riserva: si sente nel finale.',
                       'pexels-9974499.jpg')])],
        'iniziale': 0, 'lente': True, 'lente_zoom': 2.5, 'lente_misura': 200, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passate sopra la foto: la lente mostra la botanica da vicino',
        'testo_suggerimento_touch': 'Toccate la foto per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'La botanica', 'accento': OTTONE, 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        'sfondo_pannello': '', 'etichetta_color': C['tenue'], 'nome_font_family': 'heading', 'nome_font_size': 48,
        'nome_font_weight': '500', 'nome_color': C['testo'], 'codice_font_family': 'var(--olo-font-family-mono)',
        'codice_color': SALVIA, 'testo_color': C['tenue'], 'raggio_immagine': radius(4), 'proporzione': '3/2',
        'campione_forma': 'cerchio', 'campione_misura': 52, 'disposizione': 'lato', 'lato': 'destra',
        'larghezza_immagine': 58, 'gap': 56}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Da vicino', ['Le botaniche', 'sotto la lente.'], size=56,
                         tagline='Scegliete una botanica: la lente la ingrandisce come la vediamo noi prima di metterla nel tino.')]),
        t.one([vicino]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='botaniche', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SCORZA, 'Il servizio', 'La scorza, <em>non il succo.</em>',
                             'Nel gin tonic si spreme la scorza sopra il bicchiere, gli oli cadono sul ghiaccio. Il succo '
                             'copre il ginepro: lasciatelo nel limone.', overlay=55))
    out.append(K.domande(t, 'Prima', 'di ordinare.', [
        ('Spedite in tutta Italia?', 'Sì, in due giorni lavorativi, nella cassetta di larice. Gratis da due bottiglie.'),
        ('Fate gin per i matrimoni?', 'Sì: l’etichetta personalizzata e le bottigliette da 5 cl, da duecento pezzi in su.'),
        ('Che acqua tonica usate?', 'Una secca, poco zuccherata. Il rapporto è uno a tre, col ghiaccio pieno.'),
        ('Il gin si conserva aperto?', 'Anni, al buio e in piedi. Gli agrumi perdono un po’ di freschezza dopo sei mesi.'),
    ], 'gi', bg=False))
    return out


def build_distilleria():
    t = Tpl(ID, 'la-distilleria')
    out = [K.intro(t, 'La distilleria', ['Una stalla del 1890,', 'un alambicco di rame.'],
                   'L’abbiamo ricavata nella stalla del maso di famiglia, a Bormio. L’alambicco viene da un ramaio di '
                   'Arezzo, a mano, in trecento ore.', bg=K.photo_bg('pexels-28792582.jpg', overlay=60), overlay_text=True)]
    fasi = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': OTTONE, 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': OTTONE, 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': True, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('01', 'Settembre', 'pexels-20676810.jpg', 'La raccolta', 'Il ginepro', 'a mano', 'Sopra i duemila metri, coi '
             'guanti e il cesto: una bacca su tre, quelle blu notte.', '2.200 m', 'di quota'),
            ('02', 'Due giorni', 'pexels-8450505.jpg', 'La macerazione', 'Le botaniche', 'nell’alcol', 'Quarantotto ore in '
             'infusione a freddo, al buio, prima di accendere il fuoco.', '48 h', 'al buio'),
            ('03', 'Otto ore', 'pexels-29732300.jpg', 'La distillazione', 'Il cuore', 'del distillato', 'Tre passaggi a fuoco '
             'basso; si tiene solo la parte centrale, la più pulita.', '3', 'passaggi')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': OTTONE,
        'counter_font_family': 'heading', 'counter_size': 84, 'counter_color': OTTONE, 'counter_italic': True,
        'counter_weight': '500', 'tag_color': C['chiaro'], 'pre_title_color': SALVIA, 'title_font_family': 'heading',
        'title_size': 32, 'title_weight': '500', 'title_color': C['chiaro'], 'title_accent_color': OTTONE,
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'clock', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(4)}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Come nasce', ['Dal bosco', 'alla bottiglia.'], size=56)]), t.one([fasi])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Tre persone,', 'un alambicco.'], size=56)]),
        K.squadra(t, [
            ('pexels-27953366.jpg', 'Marta Pedranzini', 'Distillatrice', 'Chimica, nata a Bormio. Ha scritto la ricetta '
             'dell’Alpino in quaranta prove.', '50% 25%'),
            ('pexels-9199091.jpg', 'Luca Compagnoni', 'Bancone e degustazioni', 'Bartender per dieci anni a Londra, è tornato '
             'per il ginepro. Guida le degustazioni.', '50% 25%'),
            ('pexels-17151136.jpg', 'Giulia Rinaldi', 'Raccolta e botaniche', 'Botanica, conosce ogni cespuglio di ginepro '
             'della valle. Decide quando raccogliere.', '50% 30%')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([t.classic('scrubtext', {
            'text': 'Il gin non si fa con la ricetta: <em>si fa con la montagna</em> che c’è quell’anno.',
            'show_lead': True, 'lead': 'Un’estate secca dà un ginepro più resinoso, una piovosa più fresco. Assaggiamo ogni '
                                       'raccolto e aggiustiamo le altre botaniche.',
            'scroll_reveal': True, 'dim_opacity': 18, 'accent': OTTONE, 'text_color': C['testo'], 'lead_color': C['tenue'],
            'size_min': 32, 'size_max': 62, 'max_width_ch': 22, 'lead_size': 18, 'text_font_weight': '500',
            'text_align': 'center'}, style={})]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_BOTTE, 'La Riserva', 'Sei mesi <em>nel larice.</em>',
                          'Le botti le fa un bottaio di Livigno con il larice della valle. Il gin entra trasparente ed esce '
                          'color paglia, con il profumo del bosco.', 'I gin', GIN, overlay=58, video=True))
    return out


def build_visite():
    t = Tpl(ID, 'visite')
    out = [K.intro(t, 'Visite', ['Un’ora in distilleria,', 'tre bicchieri.'],
                   'Dal mercoledì alla domenica, alle 11 e alle 16. Si vede l’alambicco, si annusano le botaniche, si '
                   'assaggia al bancone.', bg=K.photo_bg('pexels-9130342.jpg', overlay=62), overlay_text=True, size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Tre modi', ['Di venirci', 'a trovare.'], size=56,
                         tagline='Tutte su prenotazione. Per chi guida c’è la degustazione senza alcol, con i distillati '
                                 'di botaniche e l’acqua tonica fatta da noi.')]),
        K.piani(t, [('La visita', '18', 'a persona · un’ora', 'L’alambicco e la stalla del 1890\nLe botaniche da annusare\n'
                     'Un gin tonic al bancone', False, ''),
                    ('La degustazione', '35', 'a persona · un’ora e mezza', 'La visita intera\nI quattro gin, lisci e in '
                     'miscelazione\nTaglieri della valle', True, 'La più scelta'),
                    ('Il laboratorio', '110', 'a persona · tre ore', 'Scegliete le vostre botaniche\nDistillate con noi nel '
                     'piccolo alambicco\nVi portate a casa la vostra bottiglia', False, '')],
                'Prenota', VISITE + '#prenota', nota='Dai 18 anni · bambini gratis alla visita, con lo sciroppo di mugo'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa', 'cosa', True, opzioni='La visita · 18 €\nLa degustazione · 35 €\nIl laboratorio · 110 €'),
             K.campo('date', 'Giorno', 'giorno', True), K.campo('select', 'Ora', 'ora', True, opzioni='11:00\n16:00'),
             K.campo('select', 'Quanti siete', 'persone', True, opzioni='1\n2\n3\n4\n5\n6\n7 — 12 (gruppo)'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Allergie, chi guida, una festa da fare…')]
    out.append(K.sezione_modulo(t, 'prenota', 'La prenotazione', ['Scegliete', 'il giorno.'],
                                'Vi confermiamo con una mail. Se nevica forte vi scriviamo noi la mattina, e spostiamo senza '
                                'problemi.', VOCI, campi, 'Bacca Nera — visita', 'Prenota', 'Ricevuta. Vi confermiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In alta valle,', 'sotto lo Stelvio.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via dei Masi 12, Bormio'), ('Da Milano', 'tre ore, per la statale 38'),
                                  ('Parcheggio', 'davanti al maso'), ('D’inverno', 'catene o gomme da neve')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Bacca Nera · distilleria', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · bottiglia vuota', color=OTTONE, align='center'),
                       K.headline(t, 'Questa pagina <em>è evaporata.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=SALVIA),
                       K.text(t, 'Succede anche alla testa del distillato. Torniamo al cuore.', size=17, align='center',
                              color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna alla distilleria', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_GHIACCIO, overlay=66), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'Le botaniche in orbita',
    ['gin', 'distilleria', 'cocktail', 'valtellina', 'degustazioni', 'scuro', 'slider pro'],
    'Una distilleria di gin in alta montagna, verde bosco e ottone: in apertura il bicchiere nell’arco e cinque botaniche '
    'che gli girano attorno su un’orbita inclinata, un gin per diapositiva; le botaniche che si affacciano, le ricette '
    'su carte da girare, le spezie sotto la lente, il listino e la prenotazione delle visite.',
    [('I gin', GIN), ('La distilleria', DISTILLERIA), ('Le ricette', HOME + '#ricette'), ('Visite', VISITE)],
    [('home', 'Home', None), ('i-gin', 'I gin', 'i-gin'), ('la-distilleria', 'La distilleria', 'la-distilleria'),
     ('visite', 'Visite', 'visite')],
    HEX, '\'Afacad\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Brygada 1918\', \'Georgia\', serif',
    '\'Sometype Mono\', ui-monospace, Menlo, monospace', ['Brygada 1918', 'Afacad', 'Sometype Mono'],
    corsivo_di=['Brygada 1918'], peso_titoli='500', raggio=4, grana=6, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # apertura rifatta il 31 ottobre 2026


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-gin.json': build_gin(), 'la-distilleria.json': build_distilleria(), 'visite.json': build_visite(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
