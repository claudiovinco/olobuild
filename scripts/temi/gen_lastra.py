# -*- coding: utf-8 -*-
"""Genera il tema «Lastra» (id lastra): un marchio di Torino di giacche tecniche e piumini che si fa guardare dentro.

La trovata: «lo scanner dell'aeroporto». In apertura la giacca passa sotto lo scanner: scorrendo una riga di luce scende
sullo schermo e tutto quello che ha già passato diventa una lastra ai raggi X (un livello dello Slider Pro che rovescia
e scolora quello che ha dietro e si allunga dall'alto), e sulla lastra compaiono le scritte di quello che c'è dentro.
Più giù i punti da toccare sulla giacca (Hotspot) e la foto con la sua lastra da trascinare (Image Compare).

Tema scuro: blu notte, ghiaccio e l'arancio dello scanner. Titoli in Chakra Petch, testo in IBM Plex Sans, dati in
JetBrains Mono. Logo: LASTRA tra gli angoli del mirino, con la riga di luce. Cursore: scanner (la riga che sale e scende).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'lastra'
NAME = 'Lastra'

HEX = {
    'primary': '#45D1FF', 'primary_contrast': '#05121A', 'secondary': '#7FA7BA', 'secondary_contrast': '#05121A',
    'accent': '#FF8A3D', 'dark': '#03080C', 'light': '#FFFFFF', 'muted': '#0F1C25', 'muted_contrast': '#E2F2FA',
    'text': '#E2F2FA', 'text_muted': '#93AEBD', 'background': '#081118', 'border': '#1F3341', 'link': '#45D1FF',
}
CIANO = HEX['primary']

HOME, CAPI, DENTRO, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:dentro}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'officina@lastra.example', '+39 011 000 7740', 'tel:+390110007740'
LAT, LNG = '45.0700', '7.6860'

V_PONTE, V_SCHIENA, V_CITTA, V_GHIACCIO, V_TRE, V_NEVE, V_NASTRO, V_BAGAGLI, V_SCANNER = (
    'pexels-video-10378647', 'pexels-video-16407855', 'pexels-video-16016833', 'pexels-video-7037952', 'pexels-video-7026832',
    'pexels-video-7038139', 'pexels-video-3701065', 'pexels-video-27778466', 'pexels-video-3696007')
VIDEOS = [V_PONTE, V_SCHIENA, V_CITTA, V_GHIACCIO, V_TRE, V_NEVE, V_NASTRO, V_BAGAGLI, V_SCANNER]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
PIUM = f('20645811', '7147889', '35862260', '35027803', '20645804', '36018377', '7147891')
TECN = f('21820213', '12517408', '37171596', '33126357', '37171563', '9845909')
RADI = f('5723884', '4989186', '5723874', '7579820')
TASC = f('9286989', '9965135', '9286987', '9286992', '5036874')
MONT = f('13441250', '26614328', '5779251', '11971020', '4751943', '40463190')
AERO = f('17068518', '18090300', '34558328')
PIUME = f('13717229', '5840689', '10526884', '84506')
PIOG = f('16797043', '16797042', '2252340', '9788920', '8499431')
STUD = f('16684688', '33350352', '19338831', '33772487', '29900469')
CUCI = f('6765650', '6766368', '6766350', '6765529', '6764951')
LASTRE = ['pexels-7147889-lastra.jpg', 'pexels-20645811-lastra.jpg', 'pexels-21820213-lastra.jpg', 'pexels-35862260-lastra.jpg']
LOGO_S, LOGO_C = 'logo-lastra-scuro.png', 'logo-lastra-chiaro.png'
PHOTOS = PIUM + TECN + RADI + TASC + MONT + AERO + PIUME + PIOG + STUD + CUCI + LASTRE
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
MONO = 'JetBrains Mono'
VOCI = [('map-pin', 'Via Bologna 220 · Torino', NEGOZI + '#officina'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Lastra', LOGO_C, 160, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_C, '1008/260', 'Lastra fa a Torino giacche tecniche e piumini che si possono guardare dentro: '
                   'piuma tracciata, cuciture nastrate, ogni strato dichiarato.',
                   [('La collezione', CAPI), ('Dentro', DENTRO), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'L’officina', 'Via Bologna 220, Torino<br>Martedì — sabato 10 — 19', VOCI,
                   '© 2026 Lastra · Torino · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=380)


# ── L'apertura: lo scanner ──
def apertura(t):
    m = K.mob
    giu = lambda alto: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0, 'o': 0}, {'p': 10, 'y': 0, 'o': 100}, {'p': 78, 'y': alto, 'o': 100},
                                                             {'p': 86, 'y': alto, 'o': 0}, {'p': 100, 'y': alto, 'o': 0}]}
    livelli = K.veli('la-velo', 'sinistra', 34, 58, colore=C['nero'], forza=78, mobile_da=0, mobile_alto=30) + [
        # la lastra: rovescia, scolora e indurisce quello che ha dietro, e si allunga dall'alto man mano che la riga scende
        K.livello('la-lastra', tipo='shape', x=0, y=0, w=100, h=100, bgColor=mix(CIANO, 14), entra='fadeIn', ritardo=0, durata=300,
                  backdropGrayscale=100, backdropContrast=175, backdropBrightness=150, backdropBlur=1,
                  customCSS='transform-origin:50% 0%;',
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'sy': 0}, {'p': 10, 'sy': 0}, {'p': 78, 'sy': 1}, {'p': 100, 'sy': 1}]}),
        # la riga di luce: una per il computer e il tablet, una per il telefono (la tela del telefono è più alta)
        K.livello('la-riga', tipo='shape', x=0, y=0, w=100, h=0.45, bgColor=CIANO, entra='fadeIn', ritardo=0, durata=300,
                  customCSS='box-shadow:0 0 18px 4px rgba(69,209,255,.75);', scrollMove=giu(806),
                  responsive={'mobile': {'visible': False}}),
        K.livello('la-rigam', tipo='shape', x=-60, y=0, w=10, h=0.25, bgColor=CIANO, entra='fadeIn', ritardo=0, durata=300,
                  customCSS='box-shadow:0 0 18px 4px rgba(69,209,255,.75);', scrollMove=giu(3146),
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 0.25}}),
        K.livello('la-o', contenuto='Lastra · giacche tecniche · Torino', x=6, y=16, w=44, size=13, peso='500', colore=mix(C['testo'], 86),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('la-t1', contenuto='Guardatela', tag='h1', x=6, y=22, w=62, size=104, peso='700', colore=C['testo'], lineHeight=0.95,
                  textTransform='uppercase', entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('la-t2', contenuto='dentro.', tag='p', x=6, y=35, w=62, size=104, fontFamily='Chakra Petch', peso='700', colore=CIANO, lineHeight=0.95,
                  textTransform='uppercase', entra='maskFromBottom', ritardo=500, durata=1000,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 15, 'width': 88}}),
        K.livello('la-s', contenuto='Scorrete: la giacca passa sotto lo scanner, e vedete cosa la tiene calda.', x=6, y=52, w=34, size=17,
                  peso='400', colore=mix(C['testo'], 92), lineHeight=1.55, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 20, 'width': 86}}),
    ]
    # le scritte sulla lastra: compaiono quando la riga di luce passa sulla loro altezza
    for k, (testo, x, y, xm, ym) in enumerate([('Cappuccio · piuma 90/10', 60, 22, 46, 40), ('Tasca stagna · zip nastrata', 66, 42, 46, 52),
                                                ('800 cuin · 220 g di piuma', 58, 58, 46, 64), ('Orlo · coulisse a una mano', 64, 76, 46, 76)]):
        p = 10 + y * 0.68
        livelli.append(K.livello(
            'la-n%d' % k, contenuto='+ ' + testo, x=x, y=y, w=24, size=14, peso='600', colore=C['acc'], letterSpacing=1,
            textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=0,
            scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': p, 'o': 0}, {'p': p + 4, 'o': 100}, {'p': 100, 'o': 100}]},
            responsive={'mobile': {'fontSize': m(10), 'x': xm, 'y': ym, 'width': 50}}))
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 80, 'o': 0}, {'p': 92, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('la-b', tipo='button', contenuto='La collezione', x=6, y=84, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=CIANO, padding=16, borderRadius=4, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 26, 'fontSize': m(11)}}),
        K.livello('la-l', tipo='button', contenuto='Dentro', x=20.4, y=84, w='auto', size=12, peso='600', colore=C['testo'],
                  bgColor='transparent', padding=16, borderRadius=4, borderWidth=1, borderColor=mix(C['testo'], 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=DENTRO, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix(C['testo'], 14), 'borderColor': C['testo'], 'duration': 250},
                  responsive={'mobile': {'x': 55, 'y': 26, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    M.scendi_tel(livelli)
    sl = [K.diapositiva('la-1', K.sfondo_video(V_SCHIENA, overlay=0.1, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def punti(t, foto, alt, segni):
    """La giacca coi punti da toccare: segni = [(x, y, titolo, testo)]."""
    return t.classic('hotspot', {
        'image': K.img(foto), 'image_alt': alt, 'image_height': '640', 'aspect_ratio': '4/5', 'object_fit': 'cover',
        'object_position': 'center center',
        'markers': [{'id': 'la-h%d' % k, 'pos_x': str(x), 'pos_y': str(y), 'title': ti, 'description': tx, 'icon': '', 'link': '',
                     'link_label': ''} for k, (x, y, ti, tx) in enumerate(segni)],
        'border_radius': 4, 'marker_style': 'number', 'marker_color': CIANO, 'marker_text_color': C['nero'], 'marker_size': 34,
        'pulse_animation': True, 'tooltip_bg': C['sala'], 'tooltip_color': C['testo'], 'tooltip_width': 280}, style={})


def lastra(t, foto, raggi, etichetta=('La giacca', 'La lastra')):
    """La foto e la sua lastra ai raggi X, col divisore da trascinare."""
    return t.classic('imgcompare', {
        'before_image': K.img(foto), 'after_image': K.img(raggi), 'before_label': etichetta[0], 'after_label': etichetta[1],
        'show_labels': True, 'start_position': 50, 'orientation': 'horizontal', 'handle_color': CIANO, 'handle_size': 44,
        'handle_border': '2', 'line_width': 2, 'height': '640', 'aspect_ratio': '4/5', 'border_radius': 4, 'object_fit': 'cover',
        'object_position': 'center center', 'autoplay': True, 'autoplay_delay': '2', 'autoplay_speed': '3'}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lastra · Torino, via Bologna 220', ['Una giacca', 'non ha segreti.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Su ogni nostra giacca c’è scritto cosa c’è dentro: quanta piuma e da dove viene, quante '
                                'cuciture sono nastrate, dove sta la tasca stagna. E la mettiamo sotto i raggi X, perché '
                                'possiate vederlo da voi.', size=17),
                      K.button(t, 'Dentro', DENTRO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il piumino Corso', ['Toccate', 'i punti.'], size=60, layout='stack', size_mobile=40),
                      K.text(t, 'Ogni numero è una scelta: la piuma, la zip, la tasca, l’orlo. Toccatelo per leggerla.', size=17)],
                     wm='1-3', ws='1-1'),
               t.col([punti(t, PIUM[1], 'Una donna col piumino nero sul molo di pietra', [
                   (52, 26, 'Collo alto', 'Foderato in pile riciclato, si chiude fino al naso.'),
                   (40, 50, 'Piuma 90/10', '800 cuin, tracciata dall’allevamento: il certificato è nella tasca.'),
                   (66, 62, 'Tasca scaldamani', 'Foderata in pile, con la zip nascosta.'),
                   (55, 86, 'Orlo', 'Coulisse a una mano, per chiudere il vento.')])], wm='1-3', ws='1-1'),
               t.col([lastra(t, PIUM[1], LASTRE[0])], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=40, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro giacche,', 'tutte trasparenti.'], size=60)]),
        t.one([M.capi(K, t, [
            (PIUM[1], 'Piumini', 'Corso', '', 'Corto, lucido, 800 cuin.', '420 €', 'nuovo'),
            (TECN[0], 'Gusci', 'Valico', '', 'Tre strati, cuciture nastrate.', '380 €', ''),
            (PIUM[3], 'Piumini', 'Dora', '', 'Lungo fino al ginocchio, il cappuccio staccabile.', '480 €', ''),
            (TECN[2], 'Gusci', 'Stura', '', 'Leggero, sta in una tasca.', '260 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Piuma tracciata', 'Cuciture nastrate', 'Tutto dichiarato', 'Raggi X'],
                                          fondo=CIANO, colore=C['nero'], size=56)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(K.video_frase(t, V_GHIACCIO, 'Sul ghiaccio', 'Meno trenta, <em>dentro venti.</em>',
                             'Il Dora l’abbiamo provato sul lago ghiacciato: dentro si sta come in casa.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'In città', ['Fuori è una giacca,', 'dentro un’officina.'], size=60)]),
        P.fila_foto(K, t, [(STUD[1], 'Il Valico in studio'), (PIOG[2], 'Lo Stura sotto la pioggia'), (PIUM[0], 'Il Corso giallo'),
                           (STUD[4], 'Il Dora in città')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Una giacca nuova', 'ogni inverno.'],
                         'Le giacche che escono, le prove sul campo, le lastre di quello che c’è dentro. Due volte l’anno.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Otto giacche,', 'ogni strato scritto.'],
                   'Piuma tracciata, nylon riciclato, membrane senza PFC. Sull’etichetta di ogni giacca c’è il suo spaccato.',
                   bg=K.video_bg(V_PONTE, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Piumini', ['Caldi', 'e leggeri.'], size=60)]),
        t.one([M.capi(K, t, [
            (PIUM[1], 'Piumini', 'Corso', '', 'Corto, lucido.', '420 €', 'nuovo'),
            (PIUM[3], 'Piumini', 'Dora', '', 'Lungo, il cappuccio staccabile.', '480 €', ''),
            (PIUM[0], 'Piumini', 'Sole', '', 'Giallo, per farsi vedere in montagna.', '420 €', ''),
            (PIUM[5], 'Piumini', 'Crema', '', 'Il bianco latte, sfoderabile.', '440 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([lastra(t, PIUM[0], LASTRE[1])], wm='1-2', ws='1-1'), t.col([lastra(t, TECN[0], LASTRE[2])], wm='1-2', ws='1-1')],
              layout='50-50', gap=32),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Gusci', ['Contro il vento', 'e la pioggia.'], size=60)]),
        t.one([M.capi(K, t, [
            (TECN[0], 'Gusci', 'Valico', '', 'Tre strati, nastrato.', '380 €', ''),
            (TECN[2], 'Gusci', 'Stura', '', 'Sta in una tasca.', '260 €', ''),
            (TECN[4], 'Gusci', 'Rocciamelone', '', 'Per l’alta quota.', '460 €', ''),
            (PIOG[0], 'Gusci', 'Pioggia', '', 'La cerata tecnica.', '300 €', 'ultimi')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_dentro():
    t = Tpl(ID, 'dentro')
    out = [K.intro(t, 'Dentro', ['Strato', 'per strato.'],
                   'Una giacca è fatta di strati: fuori il guscio, poi la piuma, poi la fodera. Su ognuno c’è una scelta, e ve '
                   'la diciamo tutta.', bg=K.video_bg(V_NASTRO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Gli strati', ['Tre strati,', 'tutti scritti.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'il guscio', TASC[0], 'Fuori', 'Il guscio', 'nastrato', 'Nylon riciclato e membrana senza PFC: ogni cucitura è '
             'chiusa da un nastro.', '20 000', 'mm di colonna d’acqua'),
            ('2', 'la piuma', PIUME[0], 'In mezzo', 'La piuma', 'tracciata', 'Piuma d’oca 90/10 da allevamenti controllati: il '
             'numero di lotto è nella tasca.', '800', 'cuin'),
            ('3', 'la fodera', CUCI[2], 'Dentro', 'La fodera', 'che respira', 'Nylon leggerissimo, morbido sulla pelle, che lascia '
             'uscire il vapore.', '20', 'grammi')], icona='layers')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([lastra(t, PIUM[2], LASTRE[3])], wm='1-2', ws='1-1'),
               t.col([punti(t, TECN[0], 'Il guscio Valico appeso nel bagagliaio', [
                   (50, 22, 'Cappuccio', 'Regolabile da dietro, si chiude col casco.'),
                   (50, 42, 'Zip stagna', 'Nastrata, con la patta interna.'),
                   (34, 56, 'Tasca al petto', 'Ci sta un telefono, con la zip stagna.'),
                   (52, 78, 'Orlo', 'Coulisse a una mano.')])], wm='1-2', ws='1-1')],
              layout='50-50', gap=40, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'In officina', ['Chi riempie', 'le giacche.'], size=60)]),
        K.squadra(t, [(STUD[0], 'Paola Gribaudo', 'Fondatrice', 'Ingegnera dei materiali: le giacche le smonta prima di disegnarle.',
                       'center 25%'),
                      (STUD[3], 'Kwame Asante', 'Modellista', 'Disegna dove va ogni cucitura, e quante ne servono.', 'center 25%'),
                      (STUD[2], 'Irene Bosio', 'Piuma', 'Pesa la piuma di ogni scomparto, al grammo.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, RADI[0], 'Una lastra ai raggi X', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Al controllo dell’aeroporto l’addetto ha guardato lo schermo e ha detto: questa giacca '
                                        'la conosco, l’ho vista sul vostro sito. Era vero.', 'Federico, Cuneo', size='38')],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarle,', 'e aprirle.'],
                   'Nei negozi c’è sempre una giacca aperta a metà, per vedere gli strati. E c’è la nostra officina.',
                   bg=K.photo_bg(MONT[3]), size=96)]
    out.append(M.negozi(K, t, [
        ('Torino', 'Officina Lastra', 'via Bologna 220'), ('Torino', 'Valico', 'via Garibaldi 31'), ('Milano', 'Strato', 'via Tortona 27'),
        ('Courmayeur', 'Quota', 'via Roma 66'), ('Bolzano', 'Piuma', 'via dei Portici 12'), ('Cortina', 'Ghiaccio', 'corso Italia 44'),
        ('Chamonix', 'Radiographie', 'rue du Docteur Paccard 9'), ('Oslo', 'Røntgen', 'Grünerløkka 18')],
        nota='All’officina si riparano le giacche, anche dopo dieci anni: si cambia la piuma, si rifà la zip.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una riparazione\nIl lotto della mia piuma\nVendere Lastra\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una giacca', 'da riparare?'],
                                'Per una riparazione, sapere da dove viene la vostra piuma, o vendere Lastra nel vostro negozio.',
                                VOCI, campi, 'Lastra — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’officina', ['In Barriera,', 'nel vecchio capannone.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('Riparazioni', 'in due settimane'),
                                  ('In tram', 'linea 4, fermata Bologna')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Lastra · l’officina', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='officina', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sulle giacche.'], [
        ('Da dove viene la piuma?', 'Da allevamenti controllati: col numero di lotto della tasca trovate l’allevamento.'),
        ('Si lavano?', 'Sì, in lavatrice a trenta gradi con tre palline da tennis, per rigonfiare la piuma.'),
        ('Le riparate?', 'Sempre, anche dopo dieci anni: si cambia la piuma, si rifà la zip, si rinastrano le cuciture.'),
        ('Spedite?', 'Sì, in tutta Europa, in una scatola che si rimanda indietro per i resi.'),
    ], 'la-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · lastra vuota', color=C['acc'], align='center'),
                       K.headline(t, 'Sotto lo scanner <em>non c’è niente.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Questa pagina è passata dal controllo vuota. Torniamo all’inizio.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SCANNER, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Lo scanner ai raggi X',
    ['moda', 'abbigliamento', 'giacche', 'piumini', 'torino', 'scuro', 'slider pro', 'hotspot'],
    'Un marchio di giacche tecniche che si fa guardare dentro: in apertura la giacca passa sotto lo scanner e scorrendo '
    'la riga di luce la trasforma in una lastra ai raggi X con le scritte di quello che c’è dentro; poi i punti da toccare '
    'sul piumino, la foto e la lastra da trascinare, gli strati e i negozi.',
    [('La collezione', CAPI), ('Dentro', DENTRO), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('dentro', 'Dentro', 'dentro'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'IBM Plex Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Chakra Petch\', \'IBM Plex Sans\', sans-serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['IBM Plex Sans', 'Chakra Petch', 'JetBrains Mono'], corsivo_di=[],
    peso_titoli='700', raggio=4, grana=4, titoli_line='0.95', titoli_spazio='0.01em')
THEME['cursor']['style'] = 'scanner'
THEME['cursor']['ring_color'] = HEX['text']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'dentro.json': build_dentro(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
