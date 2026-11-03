# -*- coding: utf-8 -*-
"""Genera il tema «Raspa» (id raspa): un laboratorio di Vicenza che lava e graffia a mano il denim, un paio alla volta.

La trovata: «gratta il crudo». In apertura lo schermo è denim crudo, scuro; scorrendo la raspa passa: i baffi chiari dei
jeans lavati si tirano uno dopo l'altro sul tessuto (livelli dello Slider Pro che si allungano con lo scorrimento) e al
centro il denim sbiadisce in una macchia di candeggio (un livello che schiarisce ciò che ha dietro). Più giù i lavaggi si
scoprono grattando: tre «gratta e scopri» col denim crudo sopra e il jeans lavato sotto.

Tema chiaro: crema, indaco e il rame delle cuciture. Titoli in Archivo Black, testo in Archivo, dati in IBM Plex Mono.
Logo: RASPA mezzo crudo e mezzo lavato, con la doppia cucitura. Cursore: raspa (graffia dove passa).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'raspa'
NAME = 'Raspa'

HEX = {
    'primary': '#2B3F7A', 'primary_contrast': '#FFFFFF', 'secondary': '#8FA9C9', 'secondary_contrast': '#1B2338',
    'accent': '#B8661C', 'dark': '#141B2E', 'light': '#FFFFFF', 'muted': '#ECE6DA', 'muted_contrast': '#1B2338',
    'text': '#1B2338', 'text_muted': '#535D74', 'background': '#F6F2EA', 'border': '#D8D0C0', 'link': '#2B3F7A',
}
SBIADITO, RAME = '#C9D7EA', '#D9822B'

HOME, CAPI, LAV, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:i-lavaggi}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'banco@raspa.example', '+39 0444 000 517', 'tel:+390444000517'
LAT, LNG = '45.5470', '11.5460'

V_CRUDO, V_CHIARO, V_SFRANGIATO, V_STRADA, V_STUDIO, V_CUCIRE, V_FORBICI, V_TELA, V_PILA = (
    'pexels-video-7792881', 'pexels-video-3094382', 'pexels-video-6764150', 'pexels-video-8929220', 'pexels-video-6778846',
    'pexels-video-36066869', 'pexels-video-7776739', 'pexels-video-7776742', 'pexels-video-6764156')
VIDEOS = [V_CRUDO, V_CHIARO, V_SFRANGIATO, V_STRADA, V_STUDIO, V_CUCIRE, V_FORBICI, V_TELA, V_PILA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
JEAN = f('20485548', '6764727', '19215826', '32438644', '10004179', '15433419')
GIAC = f('6769347', '30765819', '21274098', '2157079', '37712015', '31112900')
TRAMA = f('13368318', '4049757', '8817549', '20900065', '10133275', '34851014', '19203176')
STRAP = f('3081404', '13187713', '1346187', '10744589', '36718240', '8929775')
CUCI = f('4621891', '4622203', '11482148', '6461091', '27893037')
PILA = f('6764128', '4109797', '19903579', '2129970', '4210860')
INDACO = f('31666019', '6850864', '6851142', '32808531', '6850460', '6850452')
DETT = f('16603754', '6276001', '34564985', '1482180', '6843241', '4109757')
STRADA = f('15564290', '19238449', '38244126', '15114415', '38502619', '4676602')
CHIARO = f('17492486', '36123560', '17751889', '11930218', '16982878', '16851882')
LOGO_S, LOGO_C = 'logo-raspa-scuro.png', 'logo-raspa-chiaro.png'
PHOTOS = JEAN + GIAC + TRAMA + STRAP + CUCI + PILA + INDACO + DETT + STRADA + CHIARO
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media=SBIADITO)
C = K.c
MONO = 'IBM Plex Mono'
VOCI = [('map-pin', 'Contrà Porti 21 · Vicenza', NEGOZI + '#banco'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Raspa', LOGO_S, 132, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '796/272', 'Raspa lava e graffia a mano il denim, a Vicenza: pomice, raspa e acqua di '
                   'pozzo, un paio alla volta. Ogni jeans esce diverso dall’altro.',
                   [('La collezione', CAPI), ('I lavaggi', LAV), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'Il banco', 'Contrà Porti 21, Vicenza<br>Martedì — sabato 10 — 19', VOCI,
                   '© 2026 Raspa · Vicenza · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=330, fondo=C['sala'])


# ── L'apertura: la raspa sul crudo ──
def apertura(t):
    m = K.mob
    livelli = K.veli('ra-velo', 'sinistra', 34, 62, colore=C['nero'], forza=72, mobile_da=0, mobile_alto=40) + [
        K.livello('ra-o', contenuto='Raspa · denim lavato a mano · Vicenza', x=6, y=16, w=48, size=13, peso='500',
                  colore=mix('#FFFFFF', 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('ra-t1', contenuto='Il crudo,', tag='h1', x=6, y=21, w=62, size=128, peso='400', colore='#FFFFFF',
                  lineHeight=0.92, entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'fontSize': m(58), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('ra-t2', contenuto='graffiato a mano.', tag='p', x=6, y=39, w=50, size=60, peso='400', colore=SBIADITO,
                  lineHeight=1.0, entra='maskFromBottom', ritardo=500, durata=1000,
                  responsive={'mobile': {'fontSize': m(30), 'x': 6, 'y': 17, 'width': 88}}),
        K.livello('ra-s', contenuto='Scorrete: la raspa passa sul denim, e il denim schiarisce dove la mano ha insistito.', x=6, y=54,
                  w=34, size=17, peso='400', colore=mix('#FFFFFF', 90), lineHeight=1.55, ritardo=800,
                  scrollMove={'inizio': 0, 'fine': 30, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 22, 'width': 80}}),
    ]
    # i baffi: strisce chiare che la raspa tira una dopo l'altra (si allungano da sinistra con lo scorrimento)
    baffi = [(52, 30, 30, -7), (55, 36, 34, -4), (58, 42, 30, -2), (54, 48, 36, 1), (57, 54, 28, 3), (53, 60, 32, 5), (60, 66, 24, 7)]
    baffi_tel = [(8, 52, 70, -6), (12, 55, 76, -3), (6, 58, 72, -1), (10, 61, 80, 2), (8, 64, 70, 4), (14, 67, 66, 6), (9, 70, 60, 8)]
    for k, ((x, y, w, r), (xm, ym, wm, _)) in enumerate(zip(baffi, baffi_tel)):
        da = 6 + k * 7
        livelli.append(K.livello(
            'ra-baffo%d' % k, tipo='shape', x=x, y=y, w=w, h=0.9, bgColor='transparent', borderRadius=999, entra='fadeIn',
            ritardo=0, durata=300, customCSS='transform-origin:0% 50%;',
            shapeGradient={'angle': 90, 'from': mix(SBIADITO, 92), 'to': mix(SBIADITO, 0), 'fromStop': 35},
            scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'sx': 0, 'r': r, 'o': 0}, {'p': da, 'sx': 0, 'r': r, 'o': 0},
                                                            {'p': da + 14, 'sx': 1, 'r': r, 'o': 95}, {'p': 100, 'sx': 1, 'r': r, 'o': 95}]},
            responsive={'mobile': {'x': xm, 'y': ym, 'width': wm, 'height': 0.5}}))
    # la macchia di candeggio: un tondo che schiarisce e scolora il denim che ha dietro, e si allarga
    livelli.append(K.livello(
        'ra-macchia', tipo='shape', x=58, y=14, w=34, h=72, bgColor='transparent', borderRadius=999, entra='fadeIn', ritardo=0,
        durata=300, backdropBrightness=185, backdropSaturate=45, backdropContrast=90,
        customCSS='-webkit-mask-image:radial-gradient(closest-side,#000 55%,transparent 100%);mask-image:radial-gradient(closest-side,#000 55%,transparent 100%);',
        scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 0.15, 'o': 0}, {'p': 48, 's': 0.15, 'o': 0}, {'p': 78, 's': 1, 'o': 100},
                                                        {'p': 100, 's': 1.08, 'o': 100}]},
        responsive={'mobile': {'x': 4, 'y': 44, 'width': 92, 'height': 42}}))
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 78, 'o': 0}, {'p': 92, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('ra-f1', contenuto='Mai due uguali.', tag='p', x=6, y=70, w=44, size=64, peso='400', colore='#FFFFFF', lineHeight=1.0,
                  scrollMove=fine, entra='fadeIn', ritardo=0, responsive={'mobile': {'fontSize': m(30), 'x': 6, 'y': 30, 'width': 88}}),
        K.livello('ra-b', tipo='button', contenuto='La collezione', x=6, y=84, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=2, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 36, 'fontSize': m(11)}}),
        K.livello('ra-l', tipo='button', contenuto='I lavaggi', x=20, y=84, w='auto', size=12, peso='600', colore='#FFFFFF',
                  bgColor='transparent', padding=16, borderRadius=2, borderWidth=1, borderColor=mix('#FFFFFF', 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=LAV, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix('#FFFFFF', 14), 'borderColor': '#FFFFFF', 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'x': 55, 'y': 36, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    sl = [K.diapositiva('ra-1', K.sfondo_video(V_CRUDO, overlay=0.1, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def gratta(t, voci, altezza_tel=None):
    """Le carte da grattare: il denim crudo sopra, il lavaggio sotto. voci = [(crudo, lavato, numero, nome, testo)]."""
    cols = []
    for crudo, lavato, num, nome, testo in voci:
        cols.append(t.col([t.classic('scratchfx', {
            'image': K.img(lavato), 'object_fit': 'cover', 'object_position': 'center center', 'prize_eyebrow': num,
            'prize_title': nome, 'prize_text': testo, 'coupon_code': '', 'remember': False, 'text_color': '#FFFFFF',
            'under_bg': C['nero'], 'hint': 'Grattate il crudo', 'show_button': True, 'reveal_label': 'Scopri senza grattare',
            'cover_type': 'image', 'cover_image': K.img(crudo), 'cover_color': C['nero'], 'cover_color2': C['acc'],
            'cover_text': '', 'cover_text_color': mix('#FFFFFF', 85), 'brush_size': 38, 'reveal_threshold': 55,
            'reset_on_leave': False, 'aspect': '4/5', 'height_mode': 'aspect', 'height': 460, 'align': 'center', 'max_width': 460,
            'border_radius': radius(2), 'shadow': 'none',
            'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}}, style={})],
            wm='1-3', ws='1-1'))
    return t.row(cols, layout='33-33-33', gap=24)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Raspa · Vicenza, contrà Porti 21', ['Il denim', 'si consuma a mano.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'I jeans nascono scuri, rigidi, tutti uguali. Da noi passano per le mani: la pomice nella '
                                'lavatrice di rame, la raspa sulle cosce dove si siedono i baffi, l’acqua del pozzo per il '
                                'risciacquo. Ogni paio esce con le sue pieghe, e un numero scritto a mano.', size=17),
                      K.button(t, 'I lavaggi', LAV, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I tre lavaggi', ['Grattate il crudo:', 'sotto c’è il lavato.'], size=60)]),
        gratta(t, [(TRAMA[2], STRAP[1], 'Lavaggio n. 1', 'Pietra', 'Pomice per due ore: il blu si apre, i bordi si consumano.'),
                   (TRAMA[5], CHIARO[1], 'Lavaggio n. 2', 'Neve', 'Pomice e candeggio a macchie: il denim d’inverno.'),
                   (TRAMA[3], JEAN[2], 'Lavaggio n. 3', 'Sole', 'Raspa sulle cosce e sole sul balcone: i baffi chiari.')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro jeans,', 'nessuno uguale.'], size=60)]),
        t.one([M.capi(K, t, [
            (JEAN[0], 'Jeans', 'Pietra', '', 'Dritto, il blu aperto dalla pomice.', '190 €', 'nuovo'),
            (STRAP[3], 'Jeans', 'Strappo', '', 'Le ginocchia aperte con la lama, una per una.', '210 €', ''),
            (GIAC[1], 'Giacche', 'Baffo', '', 'Il giubbotto di jeans lavato a sole.', '240 €', ''),
            (CHIARO[3], 'Jeans', 'Neve', '', 'Candeggio a macchie, mai due uguali.', '200 €', '')], CAPI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Lavato a mano', 'Graffiato a raspa', 'Mai due uguali', 'Acqua di pozzo'],
                                          fondo=C['acc'], colore='#FFFFFF', size=58)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'Crudo e lavato', ['Trascinate:', 'è lo stesso paio.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (PILA[0], PILA[3], 'Il Pietra', 'Uscito dal telaio è scuro e rigido; dopo due ore di pomice si apre.'),
            (TRAMA[2], TRAMA[1], 'La tela', 'Lo stesso denim, prima e dopo la raspa.'),
            (JEAN[3], STRAP[0], 'Lo Strappo', 'Le ginocchia aperte a mano, con la lama e la pazienza.')],
            prima='Crudo', dopo='Lavato')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_CUCIRE, 'Il banco', 'Un paio <em>alla volta.</em>',
                             'Quattro persone, due lavatrici di rame e una raspa per ognuno. Ottanta jeans alla settimana.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Per strada', ['Lavati,', 'e poi vissuti.'], size=60)]),
        P.fila_foto(K, t, [(STRADA[0], 'Il Pietra in città'), (STRADA[2], 'La giacca Baffo'), (STRADA[3], 'Lo Strappo in centro'),
                           (STRADA[5], 'Il Neve d’inverno')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Un lavaggio nuovo', 'ogni stagione.'],
                         'I lavaggi che proviamo, i jeans numerati che escono, i negozi dove arrivano. Quattro volte l’anno.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Otto capi,', 'otto lavaggi.'],
                   'Denim giapponese da 14 once, cotone cimosato, bottoni di rame. Ogni capo ha il numero del suo lavaggio '
                   'scritto a mano sulla tasca.', bg=K.video_bg(V_TELA, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Jeans', ['Dritti, larghi,', 'consumati.'], size=60)]),
        t.one([M.capi(K, t, [
            (JEAN[0], 'Jeans', 'Pietra', '', 'Dritto, pomice per due ore.', '190 €', 'nuovo'),
            (STRAP[3], 'Jeans', 'Strappo', '', 'Le ginocchia aperte a mano.', '210 €', ''),
            (CHIARO[3], 'Jeans', 'Neve', '', 'Candeggio a macchie.', '200 €', ''),
            (JEAN[5], 'Jeans', 'Crudo', '', 'Non lavato: lo consumate voi.', '170 €', '')], NEGOZI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Giacche e camicie', ['Il denim', 'sopra.'], size=60)]),
        t.one([M.capi(K, t, [
            (GIAC[1], 'Giacche', 'Baffo', '', 'Lavata a sole sul balcone.', '240 €', ''),
            (GIAC[3], 'Giacche', 'Notte', '', 'Il crudo più scuro, appena sfiorato.', '230 €', ''),
            (GIAC[4], 'Camicie', 'Polvere', '', 'Chambray lavato tre volte.', '140 €', ''),
            (GIAC[0], 'Giacche', 'Due Blu', '', 'Metà crudo e metà lavato.', '260 €', 'ultimi')], NEGOZI, tinta=C['acc'])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Prima e dopo', ['Lo stesso capo,', 'due vite.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (PILA[0], PILA[3], 'Il Pietra', 'Prima e dopo due ore di pomice.'),
            (DETT[0], DETT[1], 'La tasca', 'Il rivetto di rame resta, il blu se ne va.'),
            (JEAN[3], STRAP[0], 'Lo Strappo', 'Le ginocchia aperte a mano.')], prima='Crudo', dopo='Lavato')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_lavaggi():
    t = Tpl(ID, 'i-lavaggi')
    out = [K.intro(t, 'I lavaggi', ['Pomice, raspa,', 'acqua di pozzo.'],
                   'Niente laser, niente spruzzi chimici: il denim lo consumiamo come si consumerebbe in dieci anni, ma in un '
                   'pomeriggio.', bg=K.video_bg(V_FORBICI, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si lava', ['Tre passaggi,', 'tre mani.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'la pomice', PILA[1], 'Lavare', 'La pomice', 'nel rame', 'Due ore nel cestello di rame con la pietra pomice: il '
             'blu si apre e le cuciture si consumano.', '2', 'ore'),
            ('2', 'la raspa', CUCI[0], 'Graffiare', 'La raspa', 'sulle cosce', 'I baffi si tirano a mano, sulla forma di legno, '
             'dove il jeans si piega quando ci si siede.', '7', 'baffi'),
            ('3', 'il sole', INDACO[3], 'Asciugare', 'Il sole', 'sul balcone', 'Steso al sole un giorno intero: il blu si scalda e '
             'si schiarisce dove prende luce.', '1', 'giorno')], icona='sun')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Provate voi', ['Il lavaggio', 'del mese.'], size=60)]),
        gratta(t, [(TRAMA[5], INDACO[1], 'Di novembre', 'Indaco', 'Il blu ripassato in tintura, più scuro del crudo.'),
                   (TRAMA[2], CHIARO[5], 'Di dicembre', 'Ghiaccio', 'Candeggio pieno: un azzurro quasi bianco.'),
                   (TRAMA[3], STRAP[4], 'Di gennaio', 'Graffio', 'Raspa grossa: il denim che sembra vissuto da sempre.')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Al banco', ['Chi tiene', 'la raspa.'], size=60)]),
        K.squadra(t, [(STRADA[1], 'Ines Dal Zotto', 'Fondatrice', 'Lavava i jeans dei fratelli in vasca da bagno, con la pomice '
                       'presa al mare.', 'center 25%'),
                      (STRADA[4], 'Tommaso Rigon', 'Raspa', 'Tira i baffi a mano: sette per gamba, mai nello stesso punto.',
                       'center 25%'),
                      (JEAN[1], 'Aiko Tanaka', 'Tintura', 'Ripassa l’indaco dei lavaggi scuri.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, CUCI[1], 'Il banco della raspa', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Mi sono seduto sul banco di Raspa con i miei jeans di dieci anni fa: li hanno guardati e '
                                        'hanno detto che erano un buon lavaggio. Era il complimento più bello.', 'Davide, Padova',
                                  size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarli', 'addosso.'],
                   'Un jeans lavato a mano va provato: ogni paio veste diverso. Ecco dove trovarli, e il nostro banco.',
                   bg=K.photo_bg(PILA[4]), size=96)]
    out.append(M.negozi(K, t, [
        ('Vicenza', 'Banco Raspa', 'contrà Porti 21'), ('Milano', 'Cimosa', 'via Tortona 12'), ('Torino', 'Indaco', 'via Po 33'),
        ('Bologna', 'Pomice', 'via del Pratello 8'), ('Firenze', 'Baffo', 'borgo San Frediano 41'), ('Roma', 'Telaio', 'via del Boschetto 5'),
        ('Amsterdam', 'Denim Huis', 'Haarlemmerstraat 70'), ('Osaka', 'Raspa-ya', 'Minamihorie 1-chome')],
        nota='Al banco di Vicenza si possono portare i propri jeans: ve li laviamo a mano, in una settimana.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un lavaggio dei miei jeans\nUna riparazione\nVendere Raspa\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Jeans da lavare?', 'Portateli.'],
                                'Per un lavaggio a mano dei vostri jeans, una riparazione, o per venderci nel vostro negozio.', VOCI,
                                campi, 'Raspa — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il banco', ['In contrà Porti,', 'dietro il teatro.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('Lavaggi su richiesta', 'una settimana, da 40 €'),
                                  ('A piedi', 'dal Teatro Olimpico, cinque minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Raspa · il banco', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='banco', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sul denim.'], [
        ('Il jeans stinge ancora?', 'Un poco, i primi lavaggi: lavatelo al rovescio, a trenta gradi, da solo.'),
        ('Perché ogni paio è diverso?', 'Perché i baffi si tirano a mano e la pomice non lavora mai due volte uguale.'),
        ('Fate l’orlo?', 'Sì, in negozio o al banco, col punto a catena.'),
        ('Spedite?', 'No: un jeans così si prova. Il negozio più vicino si fa mandare la taglia.'),
    ], 'ra-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pagina stinta', color=SBIADITO, align='center'),
                       K.headline(t, 'Questa pagina <em>è stinta.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento=SBIADITO),
                       K.text(t, 'L’abbiamo lavata troppo: non si legge più. Torniamo all’inizio.', size=17, align='center',
                              color=mix('#FFFFFF', 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CHIARO, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Gratta il crudo',
    ['moda', 'abbigliamento', 'denim', 'jeans', 'vicenza', 'chiaro', 'slider pro', 'gratta e scopri'],
    'Un laboratorio che lava e graffia il denim a mano: in apertura la raspa passa sul denim crudo, i baffi chiari si tirano '
    'uno dopo l’altro e al centro il blu sbiadisce; più giù i lavaggi si scoprono grattando il crudo, poi la collezione, '
    'crudo e lavato da trascinare, i lavaggi e i negozi.',
    [('La collezione', CAPI), ('I lavaggi', LAV), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('i-lavaggi', 'I lavaggi', 'i-lavaggi'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Archivo\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Archivo Black\', \'Archivo\', sans-serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Archivo', 'Archivo Black', 'IBM Plex Mono'], corsivo_di=[],
    peso_titoli='400', raggio=2, grana=3, titoli_line='0.95', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'raspa'
THEME['cursor']['ring_color'] = '#8FA9C9'
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'i-lavaggi.json': build_lavaggi(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
