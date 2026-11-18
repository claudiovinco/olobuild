# -*- coding: utf-8 -*-
"""Genera il tema «Tornanti» (id tornanti): noleggio di e-bike a Bormio, per salire allo Stelvio, al Gavia e al Mortirolo.

La trovata (18 nov 2026, utente: «un sito che si muove in orizzontale ma un po a destra e un po a sinistra… e che magari
sembri procedere a scala, un po in giu e un po in orizzontale cambiando spesso la direzione»): il sito va a tornanti.
Nell'apertura una bici sale i tornanti disegnati sopra il video della valle mentre la quota passa da 1225 a 2757 m; poi la
salita è un gruppo di sezioni a percorso (Sezioni → Effetto sticky → «Arriva da»): a destra, a destra, giù, a sinistra,
a sinistra, giù, a destra, come la strada. Nei giri la discesa del Gavia va al contrario: a sinistra e in SU mentre scorri
in giù. In testata i Pallini Cover contano i tornanti.

Tile poco usate messe alla prova: Pallini Cover (mai usata), Cime (layerscape), Counter Circle, Barra Scroll, Step Timeline.
Tema chiaro: carta di neve, rosso dei paracarri, blu dei laghi, giallo dei cartelli. Titoli in Big Shoulders Display,
testo in Barlow. Cursore: tornante (lascia dietro di sé una strada a zigzag).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'tornanti'
NAME = 'Tornanti'
CATEGORIA = 'Noleggio'

HEX = {
    'primary': '#E5322D', 'primary_contrast': '#FFFFFF', 'secondary': '#1F6FB2', 'secondary_contrast': '#FFFFFF',
    'accent': '#FFC400', 'dark': '#161B1F', 'light': '#FFFFFF', 'muted': '#E7E6DF', 'muted_contrast': '#1C2226',
    'text': '#1C2226', 'text_muted': '#5A646B', 'background': '#F4F3EE', 'border': '#D8D6CC', 'link': '#B8231F',
}
HOME, BICI, GIRI, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:le-bici}}', '{{PAGINA:i-giri}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'ciao@tornanti.example', '0342 000 4848', 'tel:+3903420004848'
LAT, LNG = '46.4669', '10.3705'

V_TORNANTI, V_NEVE, V_CICLISTA, V_EBIKE, V_VALLE, V_NUVOLE = (
    'pexels-video-13526128', 'pexels-video-28650445', 'pexels-video-29887183', 'pexels-video-6420515', 'pexels-video-39927521',
    'pexels-video-32730916')
VIDEOS = [V_TORNANTI, V_NEVE, V_CICLISTA, V_EBIKE, V_VALLE, V_NUVOLE]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(STELVIO_LARGO, STELVIO_ROSSA, STELVIO_CURVE, STELVIO_AEREO, VERDE, SERPENTINA, CURVA_NUVOLE, BUIO, COPPIA, EBIKE_PRATO,
 EBIKE_DUE, ALBERGO_PASSO, CICLISTA_GHIAIA, BICI_FILA, NOLEGGIO_NEON, BICI_NEGOZIO, LAGO, LAGO_VERDE, BAITA, GIADA, GRUPPO,
 ELENA) = f(28607405, 39504408, 29065651, 39504409, 35755654, 31204618, 39853415, 11163071, 19411352, 8724665, 36440179,
            38745614, 25961180, 2870243, 19138765, 33865627, 27786306, 138903, 4406319, 12781856, 5807940, 31510086)
PHOTOS = [STELVIO_LARGO, STELVIO_ROSSA, STELVIO_CURVE, STELVIO_AEREO, VERDE, SERPENTINA, CURVA_NUVOLE, BUIO, COPPIA,
          EBIKE_PRATO, EBIKE_DUE, ALBERGO_PASSO, CICLISTA_GHIAIA, BICI_FILA, NOLEGGIO_NEON, BICI_NEGOZIO, LAGO, LAGO_VERDE,
          BAITA, GIADA, GRUPPO, ELENA]
DISEGNI = ['tornanti-strada.svg', 'tornanti-strada-tel.svg', 'tornanti-bici.svg']
LOGHI = ['tornanti-logo-chiaro.png', 'tornanti-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#FFC400')
C = K.c
FONT_T = 'Big Shoulders Display'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via Roma 48 · Bormio', PRENOTA + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    sez = X.testata(K, Tpl(ID, 'header'), ID, NAME, 168, scuro=False, telefono=PHONE, tel=PHONE_TEL)
    # i Pallini Cover contano i tornanti del percorso (si vedono solo nelle pagine che hanno un gruppo a percorso)
    t = Tpl(ID, 'header-pallini')
    sez[0]['children'].append(t.row([t.col([t.bare('coverdots', {
        # senza nomi: lo stesso pallino conta i tornanti della home e i tratti della discesa nei giri
        'items': [],
        'hide_without_group': True, 'dot_size': 26, 'dot_gap': 3, 'dot_inner': 8, 'border_color': mix(C['testo'], 30),
        'dot_bg': 'transparent', 'dot_color': C['acc'], 'active_glow': True})], flex_justify='center')], anim=None))
    return sez


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '530/119',
                   'Tornanti noleggia e-bike a Bormio per salire allo Stelvio, al Gavia e al Mortirolo: la bici spinge, '
                   'tu pedali, la batteria basta per andare e tornare.',
                   [('Le bici', BICI), ('I giri', GIRI), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'Il noleggio', 'Da maggio a ottobre · tutti i giorni 8 — 19<br>Il passo apre a fine maggio', VOCI,
                   '© 2026 Tornanti snc · Bormio · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                       ('youtube', 'https://youtube.com')], scuro=False)


# ── L'apertura: la bici che sale i tornanti ──
# Tela del computer 1440 × 810: la strada sta nel riquadro x 50, y 12, 46 × 84 % (662 × 680 px, come il disegno), la
# bici parte dall'inizio della strada (60, 640 del disegno) e ne segue i tratti; le tappe sono in px della tela del
# computer (sulle tele più strette si riducono da sole). Telefono (tela 640 × 1400): strada nel riquadro x 6, y 30,
# 88 × 48 % (563 × 672 px); le tappe del telefono sono le sue misure × 2,25 (1440 / 640).
GIRO_DESK = [(0, 0), (480, 0), (540, -60), (480, -120), (60, -120), (0, -180), (60, -240), (480, -240), (540, -300),
             (480, -360), (60, -360), (0, -420), (60, -480), (540, -480)]
GIRO_TEL = [(0, 0), (400, 0), (460, -60), (400, -120), (60, -120), (0, -180), (60, -240), (400, -240), (460, -300),
            (400, -360), (60, -360), (0, -420), (60, -480), (460, -480)]


def salita(punti, scala=1.0, da=8, a=58):
    """Le tappe della bici: i punti del tracciato distribuiti fra `da` e `a` % dello scorrimento."""
    passo = (a - da) / float(len(punti) - 1)
    return {'inizio': 0, 'fine': 100, 'andamento': 'costante',
            'tappe': [{'p': 0, 'x': 0, 'y': 0}] + [{'p': round(da + k * passo, 2), 'x': round(x * scala, 1),
                                                        'y': round(y * scala, 1)} for k, (x, y) in enumerate(punti)]
                     + [{'p': 100, 'x': round(punti[-1][0] * scala, 1), 'y': round(punti[-1][1] * scala, 1)}]}


def apertura(t):
    m = K.mob
    poi = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 4}, {'p': da, 'o': 0, 'y': 4},
                                                             {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    via = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': da, 'o': 100}, {'p': a, 'o': 0},
                                                             {'p': 100, 'o': 0}]}
    # i titoli ondeggiano di lato come la strada: uno a destra, l'altro a sinistra
    zig = lambda s: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 20, 'x': 36 * s}, {'p': 40, 'x': -18 * s},
                                                         {'p': 60, 'x': 24 * s}, {'p': 100, 'x': 0}]}
    L = K.veli('tr-velo', 'sinistra', 30, 60, colore=C['nero'], forza=86, mobile_alto=28)
    L += [
        K.livello('tr-strada', tipo='image', imageSrc=K.img('tornanti-strada.svg'), imageAlt='', objectFit='contain',
                  x=50, y=12, w=46, h=84, entra='fadeIn', ritardo=300, durata=900, responsive={'mobile': {'visible': False}}),
        K.livello('tr-strada-t', tipo='image', imageSrc=K.img('tornanti-strada-tel.svg'), imageAlt='', objectFit='contain',
                  x=0, y=0, w=0.1, h=0.1, entra='fadeIn', ritardo=300, durata=900,
                  responsive={'mobile': {'x': 6, 'y': 30, 'width': 88, 'height': 48}}),
        K.livello('tr-bici', tipo='image', imageSrc=K.img('tornanti-bici.svg'), imageAlt='La bici che sale i tornanti',
                  objectFit='contain', x=51.94, y=87.06, w=4.44, h=7.9, entra='zoomIn', ritardo=700, durata=600,
                  scrollMove=salita(GIRO_DESK), responsive={'mobile': {'visible': False}}),
        K.livello('tr-bici-t', tipo='image', imageSrc=K.img('tornanti-bici.svg'), imageAlt='', objectFit='contain',
                  x=0, y=0, w=0.1, h=0.1, entra='zoomIn', ritardo=700, durata=600, scrollMove=salita(GIRO_TEL, 2.25),
                  responsive={'mobile': {'x': 8.81, 'y': 72.71, 'width': 10, 'height': 4.57}}),
        K.livello('tr-o', contenuto='Tornanti · noleggio e-bike · Bormio', tag='p', x=6, y=17, w=40, size=13, peso='600',
                  colore=mix(C['chiaro'], 88), letterSpacing=3, textTransform='uppercase', ritardo=150, fontFamily=MONO,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 5.2, 'width': 88}}),
        K.livello('tr-t1', contenuto='Quarantotto', tag='h1', x=6, y=23, w=44, size=118, peso='800', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=0.92, textTransform='uppercase', entra='fadeInLeft', ritardo=300, durata=900,
                  scrollMove=zig(1), responsive={'mobile': {'fontSize': m(60), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('tr-t2', contenuto='tornanti.', x=6, y=37, w=44, size=118, peso='800', colore=C['acc2'], fontFamily=FONT_T,
                  lineHeight=0.92, textTransform='uppercase', entra='fadeInRight', ritardo=450, durata=900, scrollMove=zig(-1),
                  responsive={'mobile': {'fontSize': m(60), 'x': 6, 'y': 14.8, 'width': 88}}),
        K.livello('tr-sub', contenuto='Uno alla volta, con una bici che spinge per te.', x=6, y=53, w=36, size=22,
                  peso='500', colore=mix(C['chiaro'], 90), lineHeight=1.35, ritardo=600,
                  responsive={'mobile': {'fontSize': m(17), 'x': 6, 'y': 21.8, 'width': 84}}),
        K.livello('tr-ql', contenuto='Quota', x=6, y=64, w=20, size=12, peso='600', colore=mix(C['chiaro'], 80),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=700,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 80.8, 'width': 40}}),
        K.livello('tr-q1', contenuto='1225 m', x=6, y=67, w=30, size=58, peso='800', colore=C['chiaro'], fontFamily=FONT_T,
                  ritardo=800, scrollMove=via(46, 54), responsive={'mobile': {'fontSize': m(34), 'x': 6, 'y': 82, 'width': 60}}),
        K.livello('tr-q2', contenuto='2757 m', x=6, y=67, w=30, size=58, peso='800', colore=C['acc2'], fontFamily=FONT_T,
                  entra='fadeIn', ritardo=0, scrollMove=poi(52, 58),
                  responsive={'mobile': {'fontSize': m(34), 'x': 6, 'y': 82, 'width': 60}}),
        K.livello('tr-f', contenuto='Sei sul Passo dello Stelvio.', x=6, y=76, w=40, size=26, peso='700', colore=C['chiaro'],
                  fontFamily=FONT_T, textTransform='uppercase', letterSpacing=1, entra='fadeIn', ritardo=0,
                  scrollMove=poi(58, 66), responsive={'mobile': {'fontSize': m(19), 'x': 6, 'y': 86.2, 'width': 88}}),
        K.livello('tr-b1', tipo='button', contenuto='Prenota la bici', x=6, y=86, w='auto', size=13, peso='700',
                  colore=C['chiaro'], bgColor=C['acc'], padding=18, borderRadius=6, letterSpacing=2, textTransform='uppercase',
                  buttonUrl=PRENOTA, entra='fadeIn', ritardo=0, scrollMove=poi(66, 76),
                  hover={'bgColor': C['nero'], 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 91, 'fontSize': m(12)}}),
        K.livello('tr-b2', tipo='button', contenuto='I giri', x=21, y=86, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=6, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  letterSpacing=2, textTransform='uppercase', buttonUrl=GIRI, entra='fadeIn', ritardo=0, scrollMove=poi(66, 76),
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250}, responsive={'mobile': {'x': 58, 'y': 91, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('tr-1', K.sfondo_video(V_VALLE, overlay=0.22, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── la salita: un gruppo di sezioni a percorso ──
def tornante(t, foto, occhiello, titolo, testo, dove, da_dove, link=None, pos='center center'):
    """Un tornante della salita: la foto piena, la scheda scura col testo; `da_dove`: da dove arriva rispetto al
    precedente (destra, sinistra, sotto, sopra). La scheda sta dalla parte opposta a quella da cui arriva la strada."""
    blocco = [K.label(t, occhiello, color=C['acc2']),
              K.headline(t, titolo, size=64, tag='h2', pad_bottom=14, color=C['chiaro'], accento=C['acc2'], sizes=(48, 38)),
              K.text(t, testo, size=16, color=mix(C['chiaro'], 88))]
    if link:
        blocco.append(K.button(t, link[0], link[1], kind='pieno', pad_top=12))
    scheda = {'bg': solid(mix(C['nero'], 86)), 'padding_top': 38, 'padding_right': 42, 'padding_bottom': 36,
              'padding_left': 42, 'border_radius': '6px'}
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=scheda), t.col([], wm='1-2', ws='1-1')]
    if dove == 'destra':
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='46,54' if dove == 'sinistra' else '54,46', valign='end', anim=None)],
                     bg=dict(K.photo_bg(foto, overlay=10, position=pos)), padding='large', width='large',
                     sticky_effect='cover-h', sticky_h_dir=da_dove, sticky_h_align='bottom' if dove != 'centro' else 'center',
                     style={'padding_top': 120, 'padding_bottom': 70})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    # la Barra Scroll in cima: quanto manca al passo
    out.append(t.section([
        t.one([t.classic('scrollprogress', {'position': 'top', 'bar_color': C['acc'], 'bar_bg': 'transparent',
                                            'bar_height': '4', 'show_percentage': False, 'z_index': '9998'})], anim=None),
        t.row([t.col([K.sechead(t, 'Bormio · Passo dello Stelvio', ['Si sale piano,', 'si scende ridendo.'], size=76,
                                layout='stack', size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Ventuno chilometri e mezzo, millecinquecento metri di dislivello, quarantotto tornanti '
                                'contati con la vernice sull’asfalto. Con una e-bike li fai senza allenamento: tu pedali, '
                                'il motore aggiunge quello che manca.', size=17),
                      K.button(t, 'Scegli la bici', BICI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 90}))
    # la salita a tornanti: destra, destra, giù, sinistra, sinistra, giù, destra
    out += [
        tornante(t, BICI_NEGOZIO, 'Bormio · 1225 m', 'Si parte <em>da qui.</em>', 'Ritiri la bici in via Roma, regoli la '
                 'sella, scegli quanto aiuto vuoi. Il primo tornante è dopo il ponte sull’Adda.', 'sinistra', 'destra'),
        tornante(t, VERDE, 'Tornante 40 · 1650 m', 'Le <em>gallerie.</em>', 'Sei gallerie corte e illuminate. La luce '
                 'davanti è già accesa: le nostre bici partono così.', 'sinistra', 'destra'),
        tornante(t, LAGO, 'Tornante 32 · 1850 m', 'Il primo <em>lago.</em>', 'Qui si fermano tutti. C’è la fontana per la '
                 'borraccia e la foto è obbligatoria.', 'destra', 'sotto'),
        tornante(t, BAITA, 'Tornante 24 · 2100 m', 'La <em>baita.</em>', 'Strudel e caffè. La batteria è a metà: va bene '
                 'così, la discesa la ricarica un poco.', 'destra', 'sinistra'),
        tornante(t, CURVA_NUVOLE, 'Tornante 16 · 2350 m', 'Sopra le <em>nuvole.</em>', 'Gli alberi finiscono e il vento '
                 'cambia. Metti la giacca che ti abbiamo dato con la bici.', 'destra', 'sinistra'),
        tornante(t, STELVIO_CURVE, 'Tornante 8 · 2550 m', 'Guarda <em>giù.</em>', 'La strada che hai fatto sembra un nastro '
                 'buttato sul prato. Mancano otto curve.', 'sinistra', 'sotto'),
        tornante(t, STELVIO_LARGO, 'Tornante 1 · 2757 m', 'Il <em>Passo.</em>', 'Il cartello, la foto, il panino. Al ritorno '
                 'la bici frena per te.', 'sinistra', 'destra', link=('Prenota la salita', PRENOTA)),
    ]
    # i numeri della salita (Counter Circle)
    cerchio = lambda v, m_, suf, tit, col: t.col([t.classic('countercircle', {
        'preset': 'custom', 'value': v, 'max_value': m_, 'suffix': suf, 'prefix': '', 'title': tit, 'size': '190',
        'stroke_width': '12', 'stroke_color': col, 'track_color': mix(C['chiaro'], 16), 'text_color': C['chiaro'],
        'title_color': mix(C['chiaro'], 82), 'duration': '1800', 'title_position': 'below'}, style={})], wm='1-3', ws='1-1')
    out.append(t.section([
        t.one([K.sechead(t, 'La salita in tre numeri', ['Quanto è dura?', 'Meno di quanto pensi.'], size=60, align='center',
                         chiaro=True)]),
        t.row([cerchio('48', '48', '', 'tornanti, numerati', C['acc']), cerchio('21', '25', ' km', 'da Bormio al passo', C['acc2']),
               cerchio('1532', '2000', ' m', 'di dislivello', '#6FB7FF')], layout='33-33-33', gap=24),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    # le cime che si vedono dal passo (Cime / layerscape)
    out.append(t.section([t.one([t.classic('layerscape', {
        'strati': [
            {'id': 'tr-ls-1', 'forma': 'cime', 'variante': 4, 'dettaglio': 70, 'immagine': '', 'profondita': 90, 'altezza': 74,
             'colore': mix(C['acc'], 22, C['nero']), 'opacita': 100, 'sfocatura': 0},
            {'id': 'tr-ls-2', 'forma': 'montagne', 'variante': 6, 'dettaglio': 55, 'immagine': '', 'profondita': 60,
             'altezza': 58, 'colore': mix('#1F6FB2', 55, C['nero']), 'opacita': 100, 'sfocatura': 0},
            {'id': 'tr-ls-3', 'forma': 'colline', 'variante': 3, 'dettaglio': 50, 'immagine': '', 'profondita': 30,
             'altezza': 38, 'colore': mix('#1F6FB2', 30, C['nero']), 'opacita': 100, 'sfocatura': 0},
            {'id': 'tr-ls-4', 'forma': 'bosco', 'variante': 5, 'dettaglio': 60, 'immagine': '', 'profondita': 0, 'altezza': 26,
             'colore': C['nero'], 'opacita': 100, 'sfocatura': 0}],
        'cielo': 'sfumato', 'stelle': True, 'stelle_numero': 70, 'stelle_comparsa': 'scorrendo', 'stelle_scintillio': True,
        'astro': 'sole', 'astro_x': 72, 'astro_y': 46, 'astro_moto': 'tramonta', 'nebbia': True, 'nebbia_scorre': True,
        'occhiello': 'Dal passo, al tramonto', 'titolo': 'Ortles, Cevedale, Gran Zebrù.', 'titolo_tag': 'h2',
        'testo': 'Tre cime sopra i tremila metri. Le vedi dal cartello del passo mentre il sole scende sulla Svizzera.',
        'pulsante_testo': 'I giri', 'pulsante_link': GIRI, 'testo_dietro': 1, 'testo_profondita': 45, 'movimento': True,
        'intensita': 60, 'mouse': True, 'altezza': 640,
        'cielo_alto': mix(C['nero'], 70, '#1F6FB2'), 'cielo_basso': mix(C['acc'], 55, C['acc2']), 'astro_colore': C['acc2'],
        'stelle_colore': C['chiaro'], 'nebbia_colore': mix(C['chiaro'], 60, C['acc2'])})], anim=None)],
        padding='remove-vertical', width='fullbleed'))
    out.append(X.recensioni(K, t, [
        ('Non andavo in bici da vent’anni. Sono arrivata al passo prima di mio marito, che aveva la sua da corsa.', 'Laura',
         'Brescia'),
        ('La bici era pronta all’ora giusta, la sella già alla mia altezza. In cima ho trovato ancora mezza batteria.', 'Tommaso',
         'Verona'),
        ('Abbiamo fatto lo Stelvio e il giorno dopo il Gavia. Il secondo giorno è stato il più bello.', 'Ilaria', 'Monza')],
        'Chi è salito', ['Tre salite,', 'nessuna fatica di troppo.']))
    out.append(K.domande(t, 'Prima di partire', ['Le domande', 'di chi non è mai salito.'], [
        ('Serve essere allenati?', 'No. Con l’aiuto al massimo la salita si fa al ritmo di una passeggiata in pianura: '
                                   'il motore compensa la pendenza.'),
        ('La batteria basta?', 'Sì: la batteria da 750 Wh fa andata e ritorno con un terzo di carica in avanzo. Al rifugio '
                               'del tornante 24 c’è comunque una presa.'),
        ('Che cosa mi date con la bici?', 'Casco, luci, lucchetto, una giacca antivento e la borsa sotto la sella con la '
                                          'camera d’aria.'),
        ('E se piove?', 'Spostiamo la prenotazione senza costi fino alla sera prima. Se cambia il tempo per strada, ci chiami '
                        'e veniamo a prenderti col furgone.')], 'tr'))
    out.append(K.pannello(t, STELVIO_ROSSA, 'Maggio — ottobre', 'La bici è <em>pronta alle otto.</em>',
                          'Prenoti oggi, ritiri domattina in via Roma. Il casco è già della tua misura.', 'Prenota',
                          PRENOTA + '#scrivi', effetto=''))
    return out


def build_bici():
    t = Tpl(ID, 'le-bici')
    out = [K.intro(t, 'Le bici', ['Spingono loro,', 'pedali tu.'], 'Tre modelli, tutti con la batteria grande e il cambio '
                   'a dodici rapporti. Te li prepariamo sulla tua altezza prima che arrivi.', bg=K.video_bg(V_EBIKE, overlay=20),
                   size=110)]
    out.append(t.section([
        t.one([K.sechead(t, 'Tre modelli', ['Una bici', 'per ogni salita.'], size=60)]),
        P.carte(K, t, [(EBIKE_PRATO, 'Da strada', 'La trekking', 'Il manubrio dritto, le ruote larghe: la più facile. Va bene '
                        'per lo Stelvio e per la ciclabile della valle.'),
                       (EBIKE_DUE, 'Da sterrato', 'La mountain', 'Le sospensioni davanti e dietro: per il Mortirolo e le '
                        'strade bianche dei pascoli.'),
                       (CICLISTA_GHIAIA, 'Da corsa', 'La gravel', 'Leggera, il manubrio curvo, l’aiuto discreto: per chi la '
                        'salita la vuole sentire.')], altezza='420', colonne=3),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    # Step Timeline: come funziona il noleggio
    passo = lambda n, tag, foto, pre, tit, acc, dopo, testo, val, lab, sep: {
        'counter': n, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_label': '', 'media_type': 'image', 'media_content': '',
        'media_image': K.img(foto), 'media_bg': C['sala'], 'media_color': C['acc'], 'pre_title': pre, 'title': tit,
        'title_accent': acc, 'title_accent_italic': False, 'title_after': dopo, 'title_after_italic': False,
        'description': testo, 'footer_value': val, 'footer_label': lab, 'separator_text': sep}
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Tre passi', 'e sei in sella.'], size=60)]),
        t.one([t.classic('step-timeline', {
            'items': [passo('01', 'IN UN MINUTO', NOLEGGIO_NEON, 'PRENOTA', 'Scegli la bici e', 'il giorno', '.',
                            'Dal sito o al telefono. Ci dici quanto sei alto e che giro vuoi fare.', '1′', 'PER PRENOTARE',
                            '→ POI'),
                      passo('02', 'ALLE OTTO', BICI_FILA, 'RITIRA', 'La trovi', 'pronta', 'in via Roma.',
                            'Sella alla tua altezza, batteria carica, casco della tua misura. Ti spieghiamo i tre livelli di aiuto.',
                            '10′', 'PER PARTIRE', '→ POI'),
                      passo('03', 'FINO ALLE 19', STELVIO_AEREO, 'SALI', 'Quarantotto tornanti,', 'uno alla volta', '.',
                            'Se qualcosa non va ci chiami: abbiamo un furgone e una bici di scorta.', '48', 'TORNANTI', '')],
            'columns': 3, 'columns_tablet': 1, 'media_aspect_ratio': '4/3',
            'counter_font_family': 'heading', 'counter_color': C['acc'], 'counter_italic': False, 'counter_weight': '800',
            'timeline_line_color': C['filo'], 'timeline_dot_color': C['acc'], 'tag_color': C['testo'],
            'title_font_family': 'heading', 'title_color': C['testo'], 'title_accent_color': C['acc'],
            'pre_title_color': C['tenue']}, style={})], anim=None),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Il noleggio,', 'casco compreso.'], size=60, align='center')]),
        K.piani(t, [('Mezza giornata', '39', '8 — 13 o 14 — 19', 'Bici a scelta\nCasco e luci\nGiacca antivento', False, ''),
                    ('Giornata', '59', '8 — 19', 'Bici a scelta\nCasco e luci\nGiacca antivento\nRecupero col furgone',
                     True, 'La più scelta'),
                    ('Tre giorni', '149', 'Stelvio, Gavia, Mortirolo', 'Bici a scelta\nCasco e luci\nGiacca antivento\n'
                     'Recupero col furgone\nMappa dei tre passi', False, '')], 'Prenota', PRENOTA + '#scrivi',
                nota='IVA compresa · cauzione 200 € sulla carta'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Sulle bici', ['Batterie, taglie', 'e lucchetti.'], [
        ('Che taglie avete?', 'Dalla S alla XL per ogni modello, e due bici per ragazzi dai 140 cm.'),
        ('Posso portare il mio pedale?', 'Sì, li montiamo in due minuti: pedali flat o a sgancio.'),
        ('E se buco?', 'Nella borsa c’è la camera d’aria e la pompa. Se non te la senti, ci chiami e arriviamo.')], 'trb'))
    return out


def build_giri():
    t = Tpl(ID, 'i-giri')
    out = [K.intro(t, 'I giri', ['Tre passi,', 'un giorno ciascuno.'], 'Lo Stelvio da Bormio, il Gavia verso Ponte di Legno, '
                   'il Mortirolo per chi ama le pendenze. Ognuno ha il suo carattere, e la sua discesa.',
                   bg=K.video_bg(V_TORNANTI, overlay=18), size=110)]
    out.append(t.section([
        t.one([K.sechead(t, 'I tre passi', ['Scegli', 'il tuo.'], size=60)]),
        P.righe(K, t, [('01', 'Stelvio', '48 tornanti · le gallerie, i laghi, la neve di luglio', '2757 m'),
                       ('02', 'Gavia', 'strada stretta · i pascoli e il lago Bianco', '2621 m'),
                       ('03', 'Mortirolo', 'pendenze al 18% · con l’aiuto al massimo è una gita nel bosco', '1852 m')], GIRI),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    # la discesa del Gavia, al contrario: a sinistra e in su mentre scorri in giù
    out.append(t.section([t.one([K.sechead(t, 'La discesa del Gavia', ['Scorri in giù,', 'la strada va in su.'], size=60,
                                            tagline='Dal lago Bianco a Ponte di Legno la vista va al contrario: a sinistra '
                                                    'e in alto. È il bello della discesa: guardi indietro.')])],
                         padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 40}))
    out += [
        tornante(t, LAGO_VERDE, 'Gavia · 2621 m', 'Il lago <em>Bianco.</em>', 'In cima al Gavia il lago è bianco solo di nome: '
                 'a luglio è verde come una bottiglia.', 'destra', 'destra'),
        tornante(t, SERPENTINA, 'Gavia · 2300 m', 'Le marmotte <em>fischiano.</em>', 'Rallenta: attraversano senza guardare. '
                 'La bici frena da sola quando tiri la leva a metà.', 'sinistra', 'sinistra'),
        tornante(t, ALBERGO_PASSO, 'Gavia · 1900 m', 'Il rifugio <em>dei ciclisti.</em>', 'Le foto dei Giri d’Italia sulle '
                 'pareti, la polenta sul fuoco.', 'destra', 'sopra'),
        tornante(t, BUIO, 'Ponte di Legno · 1258 m', 'Giù <em>fino in fondo.</em>', 'Ti veniamo a prendere col furgone, '
                 'con la bici e tutto. Si torna a Bormio dal Tonale.', 'sinistra', 'sinistra',
                 link=('Prenota il Gavia', PRENOTA)),
    ]
    out.append(X.zone(K, t, 'Da dove si parte', ['Da Bormio,', 'in via Roma.'], 'A cento metri dalla piazza, col parcheggio '
                      'per l’auto e il deposito per le valigie.', [('Stelvio', 'si parte dal negozio'),
                      ('Gavia', 'si parte dal negozio'), ('Mortirolo', 'ti portiamo a Mazzo col furgone'),
                      ('Telefono', PHONE)], LAT, LNG, 'Tornanti · via Roma 48', False, HEX['primary']))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Domani alle otto', 'la bici è tua.'], 'Scegli la bici, il giorno e il giro. Se hai dubbi '
                   'sulla taglia scrivici quanto sei alto: la sella la regoliamo noi.', bg=K.video_bg(V_CICLISTA, overlay=20),
                   size=104)]
    conf = X.preventivo(K, t, [
        ('bici', 'La bici', 'uno', True, 'Tutte con batteria da 750 Wh e casco compreso.'),
        ('giorni', 'Quanto', 'uno', True, ''),
        ('extra', 'In più', 'molti', False, '')], [
        ('trekking', 'bici', 'Trekking', 'Da strada.', 0, 'la più scelta', True),
        ('mountain', 'bici', 'Mountain', 'Da sterrato.', 10, '', False),
        ('gravel', 'bici', 'Gravel', 'Da corsa.', 10, '', False),
        ('mezza', 'giorni', 'Mezza giornata', '8 — 13 o 14 — 19.', 39, '', False),
        ('giornata', 'giorni', 'Giornata', '8 — 19.', 59, '', True),
        ('tre', 'giorni', 'Tre giorni', 'I tre passi.', 149, '', False),
        ('seggiolino', 'extra', 'Seggiolino bimbo', 'Fino a 22 kg.', 8, '', False),
        ('borsa', 'extra', 'Borse laterali', 'Due da 20 litri.', 6, '', False)],
        'Il tuo noleggio', PRENOTA + '?noleggio={configurazione}#scrivi', 'IVA compresa · cauzione 200 €',
        testo_pulsante='Prenota')
    out.append(X.sezione_preventivo(K, t, 'Componi', ['Bici, giorni', 'e quello che serve.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('date', 'Il giorno', 'giorno', True),
             K.campo('text', 'Quanto sei alto', 'altezza'), K.campo('text', 'Il noleggio', 'noleggio'),
             K.campo('textarea', 'Qualcosa da dirci', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Ti confermiamo', 'entro un’ora.'],
                                'Dalle 8 alle 19, anche la domenica.', VOCI, campi, 'Tornanti — prenotazione', 'Prenota',
                                'Grazie! Ti confermiamo la bici entro un’ora.'))
    out.append(t.section([t.one([X.orari(K, t, [('Maggio — giugno', '8 — 19', 'Lo Stelvio apre a fine maggio'),
                                                ('Luglio — agosto', '7:30 — 19:30', 'Si parte prima, col fresco'),
                                                ('Settembre — ottobre', '8:30 — 18', 'Il Gavia chiude alle prime nevi')])])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 110}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · strada sbagliata', color=C['acc2'], align='center'),
                       K.headline(t, 'Hai sbagliato <em>tornante.</em>', size=88, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc2']),
                       K.text(t, 'Capita anche ai migliori, di solito al trentaduesimo. Torna giù alla home e riparti.',
                              size=17, align='center', color=mix(C['chiaro'], 88)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_NEVE, overlay=30), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito a tornanti',
    ['noleggio', 'e-bike', 'bici', 'montagna', 'stelvio', 'bormio', 'chiaro', 'slider pro', 'percorso', 'scorrimento'],
    'Un noleggio di e-bike a Bormio: nell’apertura una bici sale i tornanti disegnati sopra la valle mentre la quota passa '
    'da 1225 a 2757 metri; poi il sito stesso va a tornanti, un po’ a destra, un po’ giù, un po’ a sinistra, come la strada '
    'dello Stelvio. Nei giri la discesa del Gavia va al contrario, in su mentre scorri in giù; poi le bici, i prezzi, il '
    'noleggio da comporre e la prenotazione.',
    [('Le bici', BICI), ('I giri', GIRI), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('le-bici', 'Le bici', 'le-bici'), ('i-giri', 'I giri', 'i-giri'), ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Barlow\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Big Shoulders Display\', system-ui, sans-serif',
    'ui-monospace, Menlo, monospace', ['Barlow', 'Big Shoulders Display'], corsivo_di=[],
    peso_titoli='800', raggio=6, grana=3, titoli_line='0.95', titoli_spazio='0')
THEME['cursor']['style'] = 'tornante'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['accent']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'senza'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-bici.json': build_bici(), 'i-giri.json': build_giri(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
