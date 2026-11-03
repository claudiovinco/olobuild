# -*- coding: utf-8 -*-
"""Genera il tema «Sbieco» (id sbieco): un atelier di Como che fa abiti di seta tagliati di sbieco, a quarantacinque gradi
sul filo del tessuto, perché cadano addosso come acqua.

La trovata: «il cartamodello». In apertura i pezzi del cartamodello (corpetto e gonna, carta da modelli con la linea di
cucitura e la freccia dello sbieco) sono sparsi e storti sul tavolo, sopra le forbici che tagliano; scorrendo volano al
loro posto e compongono l'abito, poi compare la cucitura al centro. Più giù il cartamodello si disegna da solo (SVG
Animator) e la frase della sarta corre lungo un'onda (TextPath).

Tema chiaro: carta da modelli, inchiostro, il rosso del gessetto e il blu del filo. Titoli in Playfair Display corsivo,
testo in Libre Franklin, dati in Courier Prime. Logo: Sbieco sulla diagonale tratteggiata, con le forbici. Cursore:
forbici (tagliano lungo il tratteggio).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'sbieco'
NAME = 'Sbieco'

HEX = {
    'primary': '#A3322B', 'primary_contrast': '#FFFFFF', 'secondary': '#3E6A94', 'secondary_contrast': '#FFFFFF',
    'accent': '#C98A1E', 'dark': '#1E1712', 'light': '#FFFFFF', 'muted': '#E6DBC6', 'muted_contrast': '#2A2018',
    'text': '#2A2018', 'text_muted': '#65564A', 'background': '#F1E9DA', 'border': '#D1C2A6', 'link': '#A3322B',
}

HOME, CAPI, ATELIER, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:atelier}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'atelier@sbieco.example', '+39 031 000 5528', 'tel:+390310005528'
LAT, LNG = '45.8110', '9.0850'

V_TAGLIO, V_FORBICI, V_TAVOLO, V_PRATO, V_ABITO, V_CAPELLI, V_CARTA, V_SPILLI, V_MANICHINO = (
    'pexels-video-4621690', 'pexels-video-6459918', 'pexels-video-4621701', 'pexels-video-8343757', 'pexels-video-8343756',
    'pexels-video-6967861', 'pexels-video-8516411', 'pexels-video-6424075', 'pexels-video-8516029')
VIDEOS = [V_TAGLIO, V_FORBICI, V_TAVOLO, V_PRATO, V_ABITO, V_CAPELLI, V_CARTA, V_SPILLI, V_MANICHINO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
CART = f('37471995', '4614113', '4621567', '4614111', '4614253', '4614228', '6766239')
ABIT = f('13301470', '5478819', '15383677', '19771924', '15921814', '8244709', '37728713')
FORB = f('35009367', '35009340', '5830641', '8527312', '7776108')
MANI = f('7147465', '2974113', '7148050', '35258130', '35278926', '7432219')
METR = f('7778196', '37588632', '31144012', '3639034')
GESS = f('7775460', '7775480', '8483986', '4614252', '6461113')
ATEL = f('4362304', '9850083', '36731337', '7147454')
MODE = f('17471951', '31046828', '37960541', '31636425', '2285484')
ROTO = f('4614231', '4614236', '4614202', '20531147')
SPIL = f('6850593', '6851139', '38835523', '35166553')
PEZZI = ['cartamodello-corpetto-sx.svg', 'cartamodello-corpetto-dx.svg', 'cartamodello-gonna-sx.svg', 'cartamodello-gonna-dx.svg']
LOGO_S, LOGO_C = 'logo-sbieco-scuro.png', 'logo-sbieco-chiaro.png'
PHOTOS = CART + ABIT + FORB + MANI + METR + GESS + ATEL + MODE + ROTO + SPIL + PEZZI
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='700', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#F0B8A8')
C = K.c
MONO = 'Courier Prime'
VOCI = [('map-pin', 'Via Borgovico 64 · Como', NEGOZI + '#atelier'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]

# il cartamodello disegnato tratto per tratto: i quattro pezzi stesi sulla carta, la linea di cucitura e le frecce del filo
CARTAMODELLO = (
    '<svg viewBox="0 0 900 520" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2.4" '
    'stroke-linecap="round" stroke-linejoin="round">'
    '<path d="M40 76 L144 42 C152 80 176 104 212 116 L218 276 L40 282 Z"/>'
    '<path d="M54 87 L136 60 C144 92 168 116 198 128 L204 264 L54 269 Z" stroke-dasharray="8 6"/>'
    '<path d="M104 132 V236 M96 144 L104 132 L112 144 M96 224 L104 236 L112 224"/>'
    '<path d="M412 76 L308 42 C300 80 276 104 240 116 L234 276 L412 282 Z"/>'
    '<path d="M398 87 L316 60 C308 92 284 116 254 128 L248 264 L398 269 Z" stroke-dasharray="8 6"/>'
    '<path d="M348 132 V236 M340 144 L348 132 L356 144 M340 224 L348 236 L356 224"/>'
    '<path d="M470 40 L646 40 L726 456 L470 462 Z"/>'
    '<path d="M484 54 L634 54 L708 444 L484 448 Z" stroke-dasharray="8 6"/>'
    '<path d="M526 390 L652 180 M638 182 L652 180 L650 194"/>'
    '<path d="M880 40 L756 40 L716 270"/>'
    '<path d="M80 340 H420 M80 330 V350 M420 330 V350 M250 330 V350"/>'
    '<path d="M90 420 C150 380 210 460 270 420 S390 380 420 420" stroke-dasharray="4 7"/>'
    '</svg>')


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Sbieco', LOGO_S, 128, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '774/292', 'Sbieco taglia a Como abiti di seta di sbieco, a quarantacinque gradi sul filo: è il '
                   'taglio che fa cadere la seta addosso come acqua. Ogni abito parte da un cartamodello di carta.',
                   [('La collezione', CAPI), ('L’atelier', ATELIER), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'L’atelier', 'Via Borgovico 64, Como<br>Martedì — sabato 10 — 19', VOCI,
                   '© 2026 Sbieco · Como · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')],
                   larghezza=300, fondo=C['sala'])


# ── L'apertura: i pezzi del cartamodello che si compongono ──
def apertura(t):
    m = K.mob
    # (file, x, y, larghezza, altezza, sparso: dx, dy, rotazione) a tela del computer; poi la posizione sul telefono
    pezzi = [('cartamodello-corpetto-dx.svg', 61.9, 11.1, 11.7, 27.2, -300, 330, -32, (30, 36, 20, 11.9)),
             ('cartamodello-corpetto-sx.svg', 73.6, 11.1, 11.7, 27.2, 170, 10, 41, (50, 36, 20, 11.9)),
             ('cartamodello-gonna-dx.svg', 56.0, 37.0, 17.6, 49.4, -200, 60, 24, (20, 47.3, 30, 21.6)),
             ('cartamodello-gonna-sx.svg', 73.6, 37.0, 17.6, 49.4, 110, 10, -28, (50, 47.3, 30, 21.6))]
    livelli = K.veli('sb-velo', 'sinistra', 34, 58, colore=C['nero'], forza=80, mobile_da=0, mobile_alto=30)
    for k, (file, x, y, w, h, dx, dy, r, (xm, ym, wm, hm)) in enumerate(pezzi):
        livelli.append(K.livello(
            'sb-p%d' % k, tipo='image', imageSrc=K.img(file), objectFit='contain', x=x, y=y, w=w, h=h, entra='fadeIn',
            ritardo=200 + k * 120, durata=700, customCSS='filter:drop-shadow(0 14px 18px rgba(0,0,0,.45));',
            scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': dx, 'y': dy, 'r': r}, {'p': 10, 'x': dx, 'y': dy, 'r': r},
                                                            {'p': 40 + k * 5, 'x': dx * 0.2, 'y': dy * 0.2, 'r': r * 0.15},
                                                            {'p': 62, 'x': 0, 'y': 0, 'r': 0}, {'p': 100, 'x': 0, 'y': 0, 'r': 0}]},
            responsive={'mobile': {'x': xm, 'y': ym, 'width': wm, 'height': hm}}))
    # la cucitura al centro, quando i pezzi si sono uniti
    livelli.append(K.livello('sb-cucitura', tipo='shape', x=73.5, y=12, w=0.12, h=73, bgColor='transparent', entra='fadeIn', ritardo=0,
                             durata=300, customCSS='border-left:2px dashed var(--olo-color-primary);',
                             scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 64, 'o': 0}, {'p': 74, 'o': 100},
                                                                             {'p': 100, 'o': 100}]},
                             responsive={'mobile': {'x': 49.9, 'y': 36.5, 'width': 0.3, 'height': 32}}))
    livelli += [
        K.livello('sb-o', contenuto='Sbieco · abiti di seta · Como', x=6, y=16, w=44, size=13, peso='700', colore=mix('#FFFFFF', 88),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('sb-t1', contenuto='Tagliato', tag='h1', x=6, y=22, w=48, size=120, peso='700', fontStyle='italic', colore='#FFFFFF',
                  lineHeight=0.95, entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'fontSize': m(48), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('sb-t2', contenuto='di sbieco.', tag='p', x=6, y=37, w=48, size=120, peso='700', fontStyle='italic',
                  fontFamily='Playfair Display', colore='#F0B8A8', lineHeight=0.95, entra='maskFromBottom', ritardo=500, durata=1000,
                  responsive={'mobile': {'fontSize': m(48), 'x': 6, 'y': 15.5, 'width': 88}}),
        K.livello('sb-s', contenuto='Scorrete: i pezzi del cartamodello vanno al loro posto.', x=6, y=55, w=34, size=17, peso='400',
                  colore=mix('#FFFFFF', 92), lineHeight=1.55, ritardo=800,
                  scrollMove={'inizio': 0, 'fine': 40, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 21, 'width': 86}}),
    ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 72, 'o': 0}, {'p': 86, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('sb-f1', contenuto='Quattro pezzi di carta, un abito di seta.', tag='p', x=6, y=55, w=36, size=26, peso='400',
                  colore='#FFFFFF', lineHeight=1.3, scrollMove=fine, entra='fadeIn', ritardo=0,
                  responsive={'mobile': {'fontSize': m(16), 'x': 6, 'y': 21, 'width': 86}}),
        K.livello('sb-b', tipo='button', contenuto='La collezione', x=6, y=70, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=0, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 26, 'fontSize': m(11)}}),
        K.livello('sb-l', tipo='button', contenuto='L’atelier', x=20, y=70, w='auto', size=12, peso='600', colore='#FFFFFF',
                  bgColor='transparent', padding=16, borderRadius=0, borderWidth=1, borderColor=mix('#FFFFFF', 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=ATELIER, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix('#FFFFFF', 14), 'borderColor': '#FFFFFF', 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'x': 55, 'y': 26, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    sl = [K.diapositiva('sb-1', K.sfondo_video(V_TAGLIO, overlay=0.15, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def disegno(t):
    """Il cartamodello che si disegna da solo, tratto per tratto, quando arriva sullo schermo."""
    return t.classic('svganimator', {
        'source_type': 'code', 'svg_url': '', 'svg_code': CARTAMODELLO, 'anim_type': 'draw', 'anim_sequence': 'one-by-one',
        'trigger': 'viewport', 'duration': 4200, 'delay': 0, 'easing': 'ease-in-out', 'stagger_delay': 120, 'stroke_width': '',
        'stroke_color': C['acc'], 'show_fill': False, 'reverse': False, 'erase_on_leave': False, 'loop': False,
        'replay_button': True, 'replay_button_label': 'Ridisegna', 'max_width': '900px', 'alignment': 'center'}, style={})


def onda(t, testo):
    """La frase della sarta che corre lungo un'onda, come il filo di un'imbastitura."""
    return t.classic('textpath', {
        'text': testo, 'path_preset': 'wave', 'custom_path': '', 'font_size': '15', 'text_color': C['acc'], 'letter_spacing': '3',
        'animation': 'continuous', 'animation_speed': '14', 'spin': False, 'typography_preset': ''}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Sbieco · Como, via Borgovico 64', ['La seta', 'cade di sbieco.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Tagliata dritta la seta sta ferma; tagliata a quarantacinque gradi sul filo si allunga, '
                                'segue il corpo, si muove quando vi muovete voi. Si spreca un terzo di tessuto in più, e ogni '
                                'pezzo va tenuto appeso due giorni prima di cucirlo. Ne vale la pena.', size=17),
                      K.button(t, 'L’atelier', ATELIER, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il cartamodello', ['Prima la carta,', 'poi la seta.'], size=60, layout='stack', size_mobile=40),
                      K.text(t, 'Ogni abito comincia così: il corpetto, la gonna, la freccia dello sbieco che dice come appoggiare '
                                'la carta sulla seta. Il resto è forbici e pazienza.', size=17),
                      onda(t, 'Imbastire a un centimetro dal bordo · lasciare riposare due giorni · ')], wm='1-3', ws='1-1'),
               t.col([disegno(t)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro abiti,', 'tutti di sbieco.'], size=60)]),
        t.one([M.capi(K, t, [
            (ABIT[0], 'Abiti', 'Acqua', '', 'La sottoveste lunga, il raso che scivola.', '380 €', 'nuovo'),
            (ABIT[3], 'Abiti', 'Lago', '', 'Il collo a cappuccio, la seta lavata.', '420 €', ''),
            (ABIT[2], 'Abiti', 'Notte', '', 'Corto, le spalline sottili.', '320 €', ''),
            (ABIT[6], 'Gonne', 'Onda', '', 'La gonna a ruota, di sbieco.', '260 €', '')], CAPI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Carta e seta', ['Trascinate:', 'dal modello all’abito.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (CART[0], ABIT[1], 'L’Acqua', 'Il cartamodello del corpetto e l’abito finito.'),
            (MANI[0], MANI[4], 'Il manichino', 'La seta appuntata con gli spilli, e cucita.'),
            (CART[3], MODE[1], 'La Notte', 'I pezzi tagliati sul tavolo, e l’abito indossato.')], prima='Carta', dopo='Seta')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_PRATO, 'Addosso', 'Cade <em>come acqua.</em>',
                             'La seta tagliata di sbieco si muove a ogni passo, e torna al suo posto.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Indossati', ['Quattro abiti,', 'quattro sere.'], size=60)]),
        P.fila_foto(K, t, [(MODE[0], 'L’Acqua, lunga'), (MODE[2], 'La Lago, al ginocchio'), (MODE[3], 'La Notte, corta'),
                           (MODE[4], 'L’Onda, con la camicia')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Un cartamodello', 'ogni stagione.'],
                         'Gli abiti nuovi, le sete che arrivano da Como, i pomeriggi in atelier. Quattro volte l’anno.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Otto abiti,', 'tutti di sbieco.'],
                   'Seta di Como, raso, crêpe e chiffon. Ogni abito è tagliato a mano sul cartamodello e lasciato riposare due '
                   'giorni prima di cucirlo.', bg=K.video_bg(V_ABITO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Abiti lunghi', ['Fino', 'alla caviglia.'], size=60)]),
        t.one([M.capi(K, t, [
            (ABIT[0], 'Abiti', 'Acqua', '', 'Raso, la sottoveste lunga.', '380 €', 'nuovo'),
            (ABIT[3], 'Abiti', 'Lago', '', 'Il collo a cappuccio.', '420 €', ''),
            (ABIT[5], 'Abiti', 'Riva', '', 'Crêpe, le maniche a kimono.', '440 €', ''),
            (ABIT[1], 'Abiti', 'Cipria', '', 'Il rosa del raso, lungo.', '400 €', '')], NEGOZI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Corti e gonne', ['Sopra', 'il ginocchio.'], size=60)]),
        t.one([M.capi(K, t, [
            (ABIT[2], 'Abiti', 'Notte', '', 'Corto, spalline sottili.', '320 €', ''),
            (ABIT[4], 'Abiti', 'Aperitivo', '', 'Chiffon, a portafoglio.', '340 €', ''),
            (ABIT[6], 'Gonne', 'Onda', '', 'La gonna a ruota.', '260 €', ''),
            (MODE[4], 'Gonne', 'Sottana', '', 'Lunga e leggera, sotto il maglione.', '240 €', 'ultime')], NEGOZI, tinta=C['acc'])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_atelier():
    t = Tpl(ID, 'atelier')
    out = [K.intro(t, 'L’atelier', ['Carta, forbici,', 'due giorni d’attesa.'],
                   'Un abito di sbieco non si fa in fretta: la seta tagliata va lasciata appesa perché si allunghi da sola, '
                   'prima di cucirla.', bg=K.video_bg(V_MANICHINO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come nasce', ['Tre giorni,', 'un abito.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'il cartamodello', CART[2], 'Disegnare', 'La carta', 'su misura', 'Il cartamodello si disegna sulla carta da modelli, '
             'con la freccia dello sbieco a quarantacinque gradi.', '4', 'pezzi'),
            ('2', 'il taglio', FORB[1], 'Tagliare', 'Le forbici', 'sulla seta', 'La carta si appunta sulla seta e si taglia a mano, '
             'un pezzo alla volta.', '1', 'giorno'),
            ('3', 'il riposo', MANI[2], 'Aspettare', 'Due giorni', 'appesa', 'I pezzi restano appesi due giorni: la seta si allunga '
             'da sola, e solo dopo si cuce.', '2', 'giorni')], icona='scissors')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In atelier', ['Chi taglia', 'di sbieco.'], size=60)]),
        K.squadra(t, [(ATEL[0], 'Ornella Cattaneo', 'Fondatrice', 'Ha imparato lo sbieco dai figurini degli anni trenta.',
                       'center 30%'),
                      (MODE[1], 'Yuki Mori', 'Modellista', 'Disegna i cartamodelli e decide dove va la freccia.', 'center 25%'),
                      (ATEL[3], 'Gianni Ponti', 'Taglio', 'Taglia la seta a mano, senza mai fermare le forbici.', 'center 30%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, SPIL[1], 'Gli spilli dell’atelier', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Me l’hanno fatto provare tre volte, e ogni volta l’abito era cambiato da solo: la seta si '
                                        'era allungata. Alla quarta cadeva come se mi conoscesse.', 'Livia, Lugano', size='38')],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarli', 'in movimento.'],
                   'Un abito di sbieco si prova camminando: si vede solo quando si muove. Ecco dove, e l’atelier.',
                   bg=K.photo_bg(ROTO[3]), size=96)]
    out.append(M.negozi(K, t, [
        ('Como', 'Atelier Sbieco', 'via Borgovico 64'), ('Milano', 'Filo', 'via della Spiga 30'), ('Torino', 'Taglio', 'via Roma 101'),
        ('Venezia', 'Seta', 'calle dei Fabbri 12'), ('Firenze', 'Raso', 'via de’ Tornabuoni 8'), ('Roma', 'Crêpe', 'via Borgognona 21'),
        ('Lugano', 'Biais', 'via Nassa 40'), ('Londra', 'Bias Cut', 'Marylebone High Street 77')],
        nota='In atelier gli abiti si fanno anche su misura: tre prove, un mese.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un abito su misura\nUna prova in atelier\nVendere Sbieco\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Un abito', 'su misura?'],
                                'Per un abito su misura, una prova in atelier, o per vendere Sbieco nel vostro negozio.', VOCI, campi,
                                'Sbieco — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’atelier', ['Sul lago,', 'in Borgovico.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('Su misura', 'su appuntamento'),
                                  ('A piedi', 'dal Duomo, dieci minuti lungo il lago')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Sbieco · l’atelier', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='atelier', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sulla seta.'], [
        ('Perché costa di più?', 'Perché lo sbieco spreca un terzo di tessuto, e ogni pezzo aspetta due giorni prima di essere cucito.'),
        ('Si lava?', 'A mano, in acqua fredda, e si stende piano, senza strizzare. Oppure in lavanderia.'),
        ('Fate le taglie forti?', 'Sì: lo sbieco segue il corpo, e il cartamodello si fa su misura.'),
        ('Spedite?', 'Sì, appeso in una custodia di cotone: arriva senza una piega.'),
    ], 'sb-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pezzo mancante', color='#F0B8A8', align='center'),
                       K.headline(t, 'Manca un pezzo <em>del cartamodello.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento='#F0B8A8'),
                       K.text(t, 'Questa pagina non si trova sul tavolo. Torniamo all’inizio.', size=17, align='center',
                              color=mix('#FFFFFF', 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CARTA, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il cartamodello che si compone',
    ['moda', 'abbigliamento', 'abiti', 'seta', 'sartoria', 'como', 'chiaro', 'slider pro', 'svg'],
    'Un atelier di abiti di seta tagliati di sbieco: in apertura i pezzi del cartamodello sono sparsi e storti sul tavolo e '
    'scorrendo volano al loro posto componendo l’abito; poi il cartamodello che si disegna da solo, la frase della sarta '
    'lungo un’onda, carta e seta da trascinare, l’atelier e i negozi.',
    [('La collezione', CAPI), ('L’atelier', ATELIER), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('atelier', 'L’atelier', 'atelier'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Libre Franklin\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Playfair Display\', Georgia, serif',
    '\'Courier Prime\', ui-monospace, Menlo, monospace', ['Libre Franklin', 'Playfair Display', 'Courier Prime'],
    corsivo_di=['Playfair Display'], peso_titoli='700', raggio=0, grana=5, titoli_line='0.95', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'forbici'
THEME['cursor']['ring_color'] = HEX['text']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'atelier.json': build_atelier(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
