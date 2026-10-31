# -*- coding: utf-8 -*-
"""Genera il tema «Fuorigiri» (id fuorigiri): un'officina di moto su misura a Modena: cafe racer, scrambler, bobber e
tracker costruiti su motori usati, una moto alla volta, nove settimane per moto.

Tema scuro, nero officina, ambra delle spie e verde del via. Titoli e testo in Archivo, dati in Chivo Mono.

Apertura: Slider Pro «il semaforo», alto 16:9, una moto per diapositiva col suo video pieno (l'officina, il meccanico al
lavoro, la curva in montagna, la notte in città) e un velo scuro solo dal lato del testo. A destra il semaforo delle gare
di accelerazione: si accendono le luci bianche di preparazione, poi le tre gialle una dopo l'altra, poi il verde, e sotto
compare il tempo di reazione. Le diapositive si spingono di lato (push). Cursore: il mirino.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'fuorigiri'
NAME = 'Fuorigiri'

HEX = {
    'primary': '#E8A317', 'primary_contrast': '#0D0D0D', 'secondary': '#46B05A', 'secondary_contrast': '#0D0D0D',
    'accent': '#F3D27A', 'dark': '#050505', 'light': '#F7F4EE', 'muted': '#181715', 'muted_contrast': '#ECE8E1',
    'text': '#ECE8E1', 'text_muted': '#9E978C', 'background': '#0D0D0D', 'border': '#2B2925', 'link': '#E8A317',
}

HOME, MOTO, SU_MISURA, OFFICINA, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:le-moto}}', '{{PAGINA:su-misura}}',
                                             '{{PAGINA:officina}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'officina@fuorigiri.example', '+39 059 000 4500', 'tel:+390590004500'
LAT, LNG = '44.6471', '10.9252'

V_BANCO, V_CHIAVE, V_CURVA, V_NOTTE, V_COLLE, V_MOTORE = ('pexels-video-7170229', 'pexels-video-7170232', 'pexels-video-4565877',
                                                        'pexels-video-4922672', 'pexels-video-4594259', 'pexels-video-5198913')
PHOTOS = [
    'pexels-9924837.jpg', 'pexels-34590640.jpg', 'pexels-34294046.jpg', 'pexels-27957826.jpg', 'pexels-3817843.jpg',
    'pexels-3823200.jpg', 'pexels-4489762.jpg', 'pexels-7564867.jpg', 'pexels-15693823.jpg', 'pexels-9580301.jpg',
    'pexels-7019377.jpg', 'pexels-16243260.jpg', 'pexels-40161877.jpg', 'pexels-35769636.jpg',
]
VIDEOS = [V_BANCO, V_CHIAVE, V_CURVA, V_NOTTE, V_COLLE, V_MOTORE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=False)
C = K.c
AMBRA, VIA = C['acc'], C['sec']
MONO = 'Chivo Mono'
VOCI = [('map-pin', 'Via Emilia Est 1460 · Modena', CONTATTI + '#officina'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Fuorigiri', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Fuorigiri', 'Moto su misura a Modena: cafe racer, scrambler, bobber e tracker costruiti su motori '
                   'usati, una alla volta. Nove settimane per moto, otto moto all’anno.',
                   [('Le moto', MOTO), ('Su misura', SU_MISURA), ('L’officina', OFFICINA), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'L’officina', 'Lunedì — venerdì 8:30 — 18:30<br>Il sabato su appuntamento', VOCI,
                   '© 2026 Fuorigiri · Modena · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=140)


# ── L'apertura: il semaforo ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
MOTO_SLIDE = [  # linguetta, occhiello, riga 1, riga 2, testo, scheda, video, tempo di reazione
    ('Cafe racer', 'N° 31 · cafe racer', 'Sella corta,', 'manubrio basso.',
     'Un bicilindrico degli anni Ottanta, rifatto da capo: telaietto nuovo, serbatoio battuto a mano, niente di superfluo.',
     'Bicilindrico 650 · 168 kg · 9 settimane', V_BANCO, '0,412'),
    ('Scrambler', 'N° 32 · scrambler', 'Sterrato o asfalto,', 'non fa differenza.',
     'Gomme tassellate, scarico alto, sospensioni lunghe: la moto per uscire dalla strada quando la strada finisce.',
     'Monocilindrico 500 · 151 kg · 8 settimane', V_CURVA, '0,388'),
    ('Bobber', 'N° 33 · bobber', 'Basso, nero,', 'senza fretta.',
     'Parafanghi tagliati, sella singola, il motore in vista: per andare piano e farsi guardare.',
     'Bicilindrico a V 750 · 212 kg · 10 settimane', V_NOTTE, '0,455'),
    ('Tracker', 'N° 34 · tracker', 'Nata in pista,', 'cresciuta in città.',
     'Le tabelle col numero, il manubrio largo, la ruota da diciannove: la moto più divertente che facciamo.',
     'Monocilindrico 600 · 146 kg · 9 settimane', V_CHIAVE, '0,401'),
]
CASSA = (650.0, 140.0, 140.0, 540.0)           # il semaforo sul computer, al centro: x, y, larghezza, altezza
CASSA_M = (236.0, 150.0, 168.0, 520.0)         # sul telefono, sotto la testata
COLONNE, COLONNE_M = (685.0, 755.0), (278.0, 362.0)
CENTRO = (720.0, 410.0)                         # il centro del semaforo: scorrendo tutto «parte» da qui
SCATTO = 1.45


def parte(px, py, fine=60):
    """Scorrendo, il semaforo viene incontro: ogni pezzo si ingrandisce attorno al centro del semaforo (si sposta
    lontano dal centro di quanto cresce) e sparisce."""
    return {'inizio': 0, 'fine': fine, 'da': {}, 'a': {'x': round((px - CENTRO[0]) * (SCATTO - 1)),
                                                      'y': round((py - CENTRO[1]) * (SCATTO - 1)), 's': SCATTO, 'o': 0}}
LUCI = [  # nome, y, raggio, y e raggio sul telefono, colore acceso, quando si accende (ms)
    ('pre1', 192, 12, 192, 14, 'chiaro', 250), ('pre2', 230, 12, 234, 14, 'chiaro', 450),
    ('g1', 300, 26, 306, 30, 'ambra', 900), ('g2', 380, 26, 384, 30, 'ambra', 1300), ('g3', 460, 26, 462, 30, 'ambra', 1700),
    ('via', 572, 30, 570, 34, 'via', 2150),
]


def _box(cx, cy, r, tw, th):
    return round((cx - r) / tw * 100, 2), round((cy - r) / th * 100, 2), round(2 * r / tw * 100, 2), round(2 * r / th * 100, 2)


def semaforo(n, reazione):
    m = K.mob
    out = []
    x, y, w, h = round(CASSA[0] / W * 100, 2), round(CASSA[1] / H * 100, 2), round(CASSA[2] / W * 100, 2), round(CASSA[3] / H * 100, 2)
    xm, ym, wm, hm = (round(CASSA_M[0] / WM * 100, 2), round(CASSA_M[1] / HM * 100, 2), round(CASSA_M[2] / WM * 100, 2),
                      round(CASSA_M[3] / HM * 100, 2))
    out.append(K.livello('fg-cassa%d' % n, tipo='shape', x=x, y=y, w=w, h=h, bgColor=mix(C['nero'], 92), borderRadius=22,
                         borderWidth=2, borderColor=mix(C['chiaro'], 14), entra='fadeInUp', ritardo=0, durata=700,
                         esce='fadeOut', boxShadow={'x': 0, 'y': 30, 'blur': 70, 'spread': -18, 'color': mix(C['nero'], 90)},
                         scrollMove=parte(CASSA[0] + CASSA[2] / 2, CASSA[1] + CASSA[3] / 2),
                         responsive={'mobile': {'x': xm, 'y': ym, 'width': wm, 'height': hm}}))
    accesi = {'chiaro': C['chiaro'], 'ambra': AMBRA, 'via': VIA}
    for c, (cx, cxm) in enumerate(zip(COLONNE, COLONNE_M)):
        for nome, cy, r, cym, rm, col, quando in LUCI:
            a = _box(cx, cy, r, W, H)
            b = _box(cxm, cym, rm, WM, HM)
            resp = {'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}
            # la lampada spenta
            out.append(K.livello('fg-%s-%d-%d' % (nome, n, c), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                                 bgColor=mix(C['chiaro'], 8), borderWidth=2, borderColor=mix(C['chiaro'], 12), entra='fadeIn',
                                 ritardo=100, durata=400, esce='fadeOut', scrollMove=parte(cx, cy), responsive=resp))
            # la lampada accesa: si accende al suo turno e resta accesa (ingresso ritardato, non la timeline: la timeline
            # ritaglia il livello al suo riquadro e l'alone diventava quadrato)
            out.append(K.livello('fg-%sx-%d-%d' % (nome, n, c), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                                 bgColor=accesi[col], entra='fadeIn', ritardo=quando, durata=90, esce='fadeOut',
                                 boxShadow={'x': 0, 'y': 0, 'blur': 28 if r > 20 else 14, 'spread': 2,
                                            'color': mix(accesi[col], 80)},
                                 scrollMove=parte(cx, cy), responsive=resp))
    # il tempo di reazione, quando scatta il verde
    x, _, w, _ = _box(CASSA[0] + CASSA[2] / 2, 0, 120, W, H)
    y = round((CASSA[1] + CASSA[3] + 16) / H * 100, 2)
    xm, _, wm, _ = _box(CASSA_M[0] + CASSA_M[2] / 2, 0, 150, WM, HM)
    ym = round((CASSA_M[1] + CASSA_M[3] + 18) / HM * 100, 2)
    out.append(K.livello('fg-reazione%d' % n, contenuto='Reazione ' + reazione + ' s', x=x, y=y, w=w, size=13, peso='600',
                         colore=C['nero'], bgColor=VIA, padding=9, borderRadius=4, align='center', fontFamily=MONO,
                         letterSpacing=1, entra='zoomIn', ritardo=2400, durata=500,
                         scrollMove=parte(CASSA[0] + CASSA[2] / 2, CASSA[1] + CASSA[3] + 30),
                         responsive={'mobile': {'x': xm, 'y': ym, 'width': wm, 'fontSize': m(11)}}))
    return out


def apertura(t):
    """Il semaforo al centro e il titolo diviso ai due lati: la prima riga a sinistra, la seconda a destra; il video si
    vede pieno al centro, i veli scuri salgono solo dai due bordi. Scorrendo, il semaforo viene incontro e sparisce e le
    due metà del titolo si allontanano: la moto è partita."""
    m = K.mob
    sl = []
    via = lambda dx: {'inizio': 0, 'fine': 80, 'da': {}, 'a': {'x': dx, 'o': 0}}
    for i, (tab, occ, r1, r2, testo, scheda, video, reazione) in enumerate(MOTO_SLIDE):
        n = i + 1
        g = lambda ang: {'angle': ang, 'from': mix(C['nero'], 90), 'to': mix(C['nero'], 0), 'fromStop': 45}
        livelli = [
            K.livello('fg-vs%d' % n, tipo='shape', x=0, y=0, w=46, h=100, shapeGradient=g(90), entra='fadeIn', ritardo=0,
                      durata=600, esce='fadeOut', responsive={'mobile': {'visible': False}}),
            K.livello('fg-vd%d' % n, tipo='shape', x=54, y=0, w=46, h=100, shapeGradient=g(270), entra='fadeIn', ritardo=0,
                      durata=600, esce='fadeOut', responsive={'mobile': {'visible': False}}),
            K.livello('fg-vm%d' % n, tipo='shape', x=0, y=0, w=0.1, h=0.1, shapeGradient=g(0), entra='fadeIn', ritardo=0,
                      durata=600, esce='fadeOut', responsive={'mobile': {'x': 0, 'y': 46, 'width': 100, 'height': 54}}),
            K.livello('fg-cielo%d' % n, tipo='shape', x=0, y=0, w=100, h=18, entra='fadeIn', ritardo=0, durata=600,
                      esce='fadeOut', shapeGradient={'angle': 180, 'from': mix(C['nero'], 75), 'to': mix(C['nero'], 0),
                                                     'fromStop': 25},
                      responsive={'mobile': {'height': 6}}),
        ]
        livelli += semaforo(n, reazione)
        livelli += [
            K.livello('fg-o%d' % n, contenuto=occ, x=4, y=24, w=38, size=12, peso='500', colore=C['chiaro'], letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, align='right', ritardo=200, scrollMove=via(-260),
                      responsive={'mobile': {'fontSize': m(11), 'y': 54.5, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello('fg-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=2, y=28, w=40, size=70, peso='800',
                      colore=C['chiaro'], lineHeight=1.02, letterSpacing=-2, align='right', entra='maskFromBottom', ritardo=300,
                      durata=1000, scrollMove=via(-320),
                      responsive={'mobile': {'fontSize': m(34), 'y': 58, 'x': 7, 'width': 88, 'textAlign': 'left'}}),
            K.livello('fg-r%d' % n, contenuto=r2, tag='p', x=58, y=40, w=40, size=70, peso='800', colore=AMBRA,
                      lineHeight=1.02, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000, scrollMove=via(320),
                      responsive={'mobile': {'fontSize': m(34), 'y': 62.2, 'x': 7, 'width': 88}}),
            K.livello('fg-k%d' % n, contenuto=scheda, x=4, y=50, w=38, size=12, peso='500', colore=mix(C['chiaro'], 76),
                      letterSpacing=1, fontFamily=MONO, align='right', ritardo=1000, scrollMove=via(-260),
                      responsive={'mobile': {'fontSize': m(10), 'y': 76.5, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello('fg-s%d' % n, contenuto=testo, x=58, y=60, w=33, size=17, peso='400', colore=mix(C['chiaro'], 84),
                      lineHeight=1.6, ritardo=800, scrollMove=via(260),
                      responsive={'mobile': {'fontSize': m(14), 'y': 68, 'x': 7, 'width': 86}}),
            K.livello('fg-b%d' % n, tipo='button', contenuto='Costruiamo la tua', x=58, y=76, w='auto', size=12, peso='800',
                      colore=C['nero'], bgColor=AMBRA, padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=4, ritardo=1100, buttonUrl=SU_MISURA, hover={'bgColor': C['chiaro'], 'duration': 250},
                      scrollMove=via(260), responsive={'mobile': {'y': 81, 'x': 7, 'fontSize': m(11)}}),
            K.livello('fg-l%d' % n, tipo='button', contenuto='Le moto', x=34, y=60, w='auto', size=12, peso='800',
                      colore=C['chiaro'], bgColor='transparent', padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=4, borderWidth=1, borderColor=mix(C['chiaro'], 50), ritardo=1250, buttonUrl=MOTO,
                      hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                      scrollMove=via(-260), responsive={'mobile': {'y': 85.5, 'x': 7, 'fontSize': m(11)}}),
        ]
        sl.append(K.diapositiva('fg-%d' % n, K.sfondo_video(video, overlay=0.18, colore=C['nero']), livelli, durata=8000,
                                tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='push', durata_trans=900,
                       showArrows=False, showDots=False, showTabs=True, tabPosition='bottom', parallax=False,
                       autoplaySpeed=8000, progressBarColor=AMBRA, navColor=C['chiaro'],
                       heightMobile={'mode': 'px', 'value': 1400})


MOTO_LISTA = [  # numero, nome, sottotitolo, anno, foto
    ('31', 'Cafe racer «Grigia»', 'Bicilindrico 650 · sella in pelle cucita a mano', '2026', 'pexels-9924837.jpg'),
    ('30', 'Scrambler «Polvere»', 'Monocilindrico 500 · gomme tassellate', '2026', 'pexels-15693823.jpg'),
    ('29', 'Bobber «Notte»', 'Bicilindrico a V 750 · tutto nero', '2025', 'pexels-35769636.jpg'),
    ('28', 'Tracker «Verde»', 'Monocilindrico 600 · tabelle col 28', '2025', 'pexels-34294046.jpg'),
    ('27', 'Cafe racer «Coppia»', 'Due moto gemelle per due fratelli', '2025', 'pexels-27957826.jpg'),
]


def moto_lista(t):
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': 'N° ' + nu + ' · ' + a, 'desc': d, 'number': nu, 'image': K.img(f), 'link_url': MOTO,
                   'color': AMBRA} for nu, n, d, a, f in MOTO_LISTA],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': AMBRA,
        'number_hover_color': VIA, 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': AMBRA,
        'name_size': 48, 'name_weight': '800', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': AMBRA,
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(AMBRA, 6), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 300, 'peek_ratio': '4/3', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': AMBRA, 'author_color': C['tenue'], 'quote_accent_color': AMBRA, 'quote_font': 'heading',
        'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': AMBRA}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Fuorigiri · Modena, dal 2013', ['Moto usate,', 'rifatte da capo.'], size=64,
                                layout='stack', size_mobile=38)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Partiamo da un motore che ha già fatto strada: lo apriamo, lo rifacciamo, e gli costruiamo '
                                'attorno una moto nuova, sulle tue misure e sul modo in cui guidi. Otto moto all’anno, ognuna '
                                'col suo numero sul telaio.', size=17, color=C['testo']),
                      K.button(t, 'L’officina', OFFICINA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('34', 'Moto costruite'), ('8', 'All’anno'), ('9', 'Settimane, in media'),
                            ('2', 'Meccanici')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le ultime', ['Cinque moto,', 'cinque padroni.'], size=60,
                         tagline='Passate su una moto: la foto si affaccia.')]),
        t.one([moto_lista(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_MOTORE, 'Il motore', 'Si apre, si misura, <em>si rifà.</em>',
                             'Pistoni, valvole, cuscinetti: quello che è consumato si cambia, il resto si pulisce a mano. Il '
                             'motore esce come nuovo, con la sua storia.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-4489762.jpg', 'Bruno al banco con la salopette e il berretto', ratio='4/5',
                              tilt=True)], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Bruno Ferretti', ['Trent’anni', 'di motori.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Bruno ha cominciato a quindici anni nell’officina di suo zio, sulla via Emilia. Ha fatto il '
                                'meccanico in pista per dieci stagioni, poi nel 2013 ha aperto Fuorigiri con Ivan, che salda e '
                                'vernicia.', size=17),
                      K.specs(t, [('In officina dal', '2013'), ('Moto costruite', '34'), ('In pista', 'dieci stagioni')]),
                      K.button(t, 'L’officina', OFFICINA, kind='pieno', pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Ho portato la moto di mio padre, ferma da vent’anni in garage. Me l’hanno ridata che sembrava uscita adesso, ma col '
           'suo graffio sul serbatoio. Non l’hanno tolto: avevano capito.', 'Davide S.', 'N° 24, cafe racer')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_COLLE, 'Su misura', 'La tua, <em>da capo.</em>',
                          'Si comincia con un caffè e la moto che hai, o quella che vorresti.', 'Costruiamo la tua',
                          SU_MISURA, overlay=40, video=True))
    return out


def build_moto():
    t = Tpl(ID, 'le-moto')
    out = [K.intro(t, 'Le moto', ['Trentaquattro moto,', 'nessuna uguale.'],
                   'Ogni moto ha il suo numero sul telaio e il nome che le dà chi la guida. Queste sono le ultime.',
                   bg=K.photo_bg('pexels-3817843.jpg'), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le ultime cinque', ['Dal banco', 'alla strada.'], size=56)]),
        t.one([moto_lista(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le quattro famiglie', ['Cafe racer, scrambler,', 'bobber, tracker.'], size=56)]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5', tilt=True), K.label(t, quando, color=AMBRA, pad_top=18),
                      K.headline(t, nome, size=26, pad_bottom=8), K.text(t, desc, size=15)], wm='1-4', ws='1-2')
               for f, alt, quando, nome, desc in (
                   ('pexels-9924837.jpg', 'Una cafe racer grigia di profilo', 'da 8.900 €', 'Cafe racer',
                    'Sella corta, manubrio basso, niente di superfluo.'),
                   ('pexels-15693823.jpg', 'Una moto su una strada sterrata', 'da 8.400 €', 'Scrambler',
                    'Gomme tassellate e scarico alto.'),
                   ('pexels-35769636.jpg', 'Un uomo accanto a una moto nera nel bosco', 'da 9.800 €', 'Bobber',
                    'Basso, nero, per andare piano.'),
                   ('pexels-34294046.jpg', 'Un motociclista su una moto verde davanti a un portone', 'da 8.900 €', 'Tracker',
                    'Ruota da diciannove e manubrio largo.'))],
              layout='25-25-25-25', gap=24),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Sulla strada', ['Dove vanno', 'a finire.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-34590640.jpg', 'Due moto in curva su una strada nel bosco', 'In due', 'Appennino'),
                               ('pexels-9580301.jpg', 'Due moto nella nebbia', 'La nebbia', 'Bassa modenese'),
                               ('pexels-40161877.jpg', 'Un motociclista col casco in mano nel bosco', 'Il padrone', 'N° 26'),
                               ('pexels-27957826.jpg', 'Due motociclisti affiancati in curva', 'Le gemelle', 'N° 27'),
                               ('pexels-15693823.jpg', 'Una moto sulla strada sterrata', 'La polvere', 'N° 30')])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    return out


def build_su_misura():
    t = Tpl(ID, 'su-misura')
    out = [K.intro(t, 'Su misura', ['Nove settimane,', 'dal caffè al primo giro.'],
                   'Si parte dalla moto che hai o da un motore che troviamo noi. Ogni settimana ti mandiamo le foto dal banco.',
                   bg=K.video_bg(V_CHIAVE), size=96)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Il caffè', 'description': 'Parliamo di come guidi, dove vai, che moto hai in '
                   'mente. Poi un disegno.'},
                  {'number': '02', 'title': 'Il motore', 'description': 'Il tuo o uno che troviamo noi: si apre, si misura, si '
                   'rifà da capo.'},
                  {'number': '03', 'title': 'Telaio e serbatoio', 'description': 'Telaietto nuovo, serbatoio battuto a mano, '
                   'la sella cucita su di te.'},
                  {'number': '04', 'title': 'Il primo giro', 'description': 'Lo facciamo insieme, sui colli. Poi la moto è '
                   'tua.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': AMBRA, 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '500', 'title_color': C['testo'], 'title_size': 26,
        'title_weight': '800', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Quattro passi,', 'una moto sola.'], size=56)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Da dove', 'si parte.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Sulla tua moto', '6.500', 'da', 'Il tuo motore rifatto\nTelaietto e serbatoio nuovi\nNove settimane', False, ''),
            ('Chiavi in mano', '8.900', 'da', 'Il motore lo troviamo noi\nTutto il lavoro su misura\nImmatricolazione e collaudo',
             True, 'il più scelto'),
            ('Restauro', '4.200', 'da', 'La moto com’era\nPezzi originali dove si trovano\nSei settimane', False, ''),
        ], 'Chiedi il preventivo', CONTATTI + '#scrivici', nota='Il preventivo esatto arriva dopo il caffè e un’occhiata alla moto.',
            sfondo=C['sala']),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di cominciare.', [
        ('Si può immatricolare?', 'Sì: ogni moto esce collaudata e con i documenti in regola.'),
        ('E la garanzia?', 'Due anni sul motore e sul lavoro, come su una moto nuova.'),
        ('Posso seguire i lavori?', 'Ogni settimana ti mandiamo le foto dal banco. E il sabato si può passare.'),
    ], 'fg-faq'))
    return out


def build_officina():
    t = Tpl(ID, 'officina')
    out = [K.intro(t, 'L’officina', ['Sulla via Emilia,', 'dove si fanno i motori.'],
                   'Un capannone degli anni Sessanta, due banchi, un tornio, la cabina di verniciatura in fondo.',
                   bg=K.photo_bg('pexels-3823200.jpg'), size=96)]
    out.append(t.section([
        t.row([t.col([K.image(t, f, alt, ratio='4/5', lightbox=True)], wm='1-4', ws='1-2') for f, alt in (
            ('pexels-7564867.jpg', 'Ivan al lavoro su un motore'), ('pexels-7019377.jpg', 'Le chiavi nel cassetto'),
            ('pexels-16243260.jpg', 'Le chiavi appese alla parete'), ('pexels-3817843.jpg', 'Le moto sul soppalco'))],
              layout='25-25-25-25', gap=18),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_BANCO, 'Il banco', 'Una moto <em>alla volta.</em>',
                             'Non ne teniamo due sul banco: la moto che stiamo facendo è l’unica a cui pensiamo.', overlay=40))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Passa in officina,', 'il caffè è pronto.'],
                   'Per una moto su misura, un restauro o un motore da rifare.', bg=K.photo_bg('pexels-16243260.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa ti serve', 'cosa', True, opzioni='Una moto su misura\nLavorare sulla mia moto\nUn '
                                                                            'restauro\nUn motore da rifare'),
             K.campo('text', 'La moto che hai (se ce l’hai)', 'moto'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scrivici', ['Due righe,', 'poi un caffè.'],
                                'Rispondiamo la sera, quando le mani sono pulite.', VOCI, campi, 'Fuorigiri — contatto',
                                'Invia', 'Ricevuto. Ti rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’officina', ['Via Emilia Est,', 'al 1460.'], size=52, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'lunedì — venerdì 8:30 — 18:30'), ('Sabato', 'su appuntamento'),
                                  ('Parcheggio', 'nel cortile')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fuorigiri · l’officina', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='officina', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · falsa partenza', color=AMBRA, align='center'),
                       K.headline(t, 'Partito <em>prima del verde.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=AMBRA),
                       K.text(t, 'Capita. Si torna alla linea di partenza.', size=17, align='center', color=mix(C['chiaro'], 84)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_NOTTE, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Industria e manifattura', 'Il semaforo',
    ['moto', 'moto custom', 'cafe racer', 'officina', 'restauro', 'modena', 'scuro', 'slider pro'],
    'Un’officina di moto su misura, nero officina e ambra delle spie: in apertura il semaforo delle gare di '
    'accelerazione al centro, fra le due metà del titolo, con le luci che si accendono fino al verde; scorrendo il '
    'semaforo viene incontro e le metà si allontanano; le moto col loro numero, le quattro famiglie e l’officina.',
    [('Le moto', MOTO), ('Su misura', SU_MISURA), ('L’officina', OFFICINA), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('le-moto', 'Le moto', 'le-moto'), ('su-misura', 'Su misura', 'su-misura'),
     ('officina', 'L’officina', 'officina'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Archivo\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Archivo\', system-ui, sans-serif',
    '\'Chivo Mono\', ui-monospace, Menlo, monospace', ['Archivo', 'Chivo Mono'], corsivo_di=[],
    peso_titoli='800', raggio=4, grana=5, titoli_line='1.02', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'calamita'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-moto.json': build_moto(), 'su-misura.json': build_su_misura(), 'officina.json': build_officina(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
