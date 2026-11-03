# -*- coding: utf-8 -*-
"""Genera il tema «Trentagradi» (id trentagradi): un marchio di Prato di capi basic in cotone pesante, magliette, felpe,
pantaloni, garantiti cento lavaggi a trenta gradi. Ogni capo ha in etichetta il contalavaggi.

Apertura: «l'oblò che si apre». Lo slider resta fermo in cima mentre si scorre: la tela è il fronte bianco di una
lavatrice con l'oblò, e dentro l'oblò gira il cestello (il video). Scorrendo il testo esce di lato e l'oblò si allarga,
col bordo tratteggiato che gira come la centrifuga, finché il cestello riempie lo schermo; alla fine una riga sul video.
Più giù il capo nuovo e lo stesso capo dopo cento lavaggi, col cursore da trascinare.

Tema chiaro, bianco lavatrice, blu detersivo e un corallo. Titoli in Rubik, testo in Hanken Grotesk, dati in IBM Plex
Mono. Logo: la vaschetta del simbolo di lavaggio col 30°. Cursore: la centrifuga (un oblò col calzino che gira più forte
quanto più veloce si va).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'trentagradi'
NAME = 'Trentagradi'

HEX = {
    'primary': '#1A5FD0', 'primary_contrast': '#FFFFFF', 'secondary': '#7BB6F0', 'secondary_contrast': '#0F1E2E',
    'accent': '#E8574A', 'dark': '#0F1E2E', 'light': '#FFFFFF', 'muted': '#E4EDF5', 'muted_contrast': '#0F1E2E',
    'text': '#0F1E2E', 'text_muted': '#4B5D70', 'background': '#F3F7FA', 'border': '#CDDAE6', 'link': '#1A5FD0',
}
BIANCO, BORDO = '#F3F7FA', '#C9D3DD'

HOME, CAPI, LAVAGGIO, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:i-capi}}', '{{PAGINA:il-lavaggio}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@trentagradi.example', '+39 0574 000 300', 'tel:+390574000300'
LAT, LNG = '43.8810', '11.0820'

V_CESTELLO, V_LUCE, V_ROSSO, V_BLU, V_LENZUOLA, V_FILO, V_CAMICIA, V_PILA, V_PIEGA = (
    'pexels-video-4119968', 'pexels-video-6482495', 'pexels-video-15432350', 'pexels-video-8756891', 'pexels-video-4245059',
    'pexels-video-20136155', 'pexels-video-9309690', 'pexels-video-6961718', 'pexels-video-5116403')
VIDEOS = [V_CESTELLO, V_LUCE, V_ROSSO, V_BLU, V_LENZUOLA, V_FILO, V_CAMICIA, V_PILA, V_PIEGA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
LAV = f('27376258', '6492065', '34397245', '18357976', '7086375')
LAN = f('8774451', '14556070', '39381820', '27666740', '2254065')
STE = f('32938974', '32938975', '30348933', '10190477', '6624093')
MAG = f('8346218', '6994856', '8346226', '7857500', '30940601')
COT = f('26601192', '8532611', '38337242', '8532616', '9558567')
MOD = f('8217291', '8217366', '9775825', '7665783', '9775824', '1304647', '4319139')
CES = f('8580730', '8889715')
CAM = f('13924870', '19131290', '37802648', '13924868')
STI = f('7705328', '5202923', '5901631')
PER = f('39915457', '32428179', '11515489', '39809934', '20420890')
LOGO_S = 'logo-trentagradi-scuro.png'
PHOTOS = LAV + LAN + STE + MAG + COT + MOD + CES + CAM + STI + PER
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, 'logo-trentagradi-chiaro.png'}

K = Kit(ID, MEDIA, raggio=16, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#8CC2FF')
C = K.c
MONO = 'IBM Plex Mono'
VOCI = [('map-pin', 'Via Pistoiese 120 · Prato', NEGOZI + '#lavanderia'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Trentagradi', LOGO_S, 200, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '1276/194', 'Trentagradi fa a Prato magliette, felpe e pantaloni di cotone pesante, garantiti '
                   'cento lavaggi a trenta gradi. Sull’etichetta c’è il contalavaggi: segnate voi.',
                   [('I capi', CAPI), ('Il lavaggio', LAVAGGIO), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La lavanderia', 'Via Pistoiese 120, Prato<br>Lunedì — sabato 9 — 19', VOCI,
                   '© 2026 Trentagradi · Prato · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=460, fondo=C['sala'])


# ── L'apertura: l'oblò che si apre ──
def apertura(t):
    m = K.mob
    # l'oblò: sul computer al 64% / 50% della tela 16:9, sul telefono al 50% / 38% della tela 640 × 1400; il fronte bianco è
    # un livello pieno con un buco tondo (un'ellisse in % della tela che sulla tela è un cerchio) e cresce attorno al buco
    apre = lambda cx, cy: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 1}, {'p': 12, 's': 1}, {'p': 88, 's': 7.5},
                                                               {'p': 100, 's': 7.5}]}
    fronte = lambda ex, ey, cx, cy: ('background:radial-gradient(ellipse %s%% %s%% at %s%% %s%%,transparent 98.5%%,%s 100%%);'
                                     'transform-origin:%s%% %s%%;') % (ex, ey, cx, cy, BIANCO, cx, cy)
    livelli = [
        K.livello('tg-fronte', tipo='shape', x=0, y=0, w=100, h=100, bgColor='transparent', entra='fadeIn', ritardo=0, durata=300,
                  customCSS=fronte(15, 26.67, 64, 50), scrollMove=apre(64, 50), responsive={'mobile': {'visible': False}}),
        K.livello('tg-fronte-m', tipo='shape', x=-60, y=0, w=0.1, h=0.1, bgColor='transparent', entra='fadeIn', ritardo=0, durata=300,
                  customCSS=fronte(34, 15.55, 50, 38), scrollMove=apre(50, 38),
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}}),
        # il bordo dell'oblò: un anello pieno e uno tratteggiato che gira come la centrifuga
        K.livello('tg-bordo', tipo='shape', x=48.5, y=22.4, w=31, h=55.1, bgColor='transparent', borderRadius=999,
                  borderWidth=16, borderColor=BORDO, borderStyle='solid', entra='zoomIn', ritardo=200, durata=900,
                  customCSS='box-shadow:inset 0 0 0 4px #FFFFFF,0 14px 40px rgba(15,30,46,.18);',
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 1, 'o': 100}, {'p': 12, 's': 1, 'o': 100},
                                                                 {'p': 70, 's': 6, 'o': 100}, {'p': 85, 's': 7.5, 'o': 0},
                                                                 {'p': 100, 's': 7.5, 'o': 0}]},
                  responsive={'mobile': {'x': 14.5, 'y': 21.7, 'width': 71, 'height': 32.6}}),
        K.livello('tg-giri', tipo='shape', x=50.2, y=25.4, w=27.6, h=49.1, bgColor='transparent', borderRadius=999, borderWidth=3,
                  borderColor=mix('#FFFFFF', 85), borderStyle='dashed', entra='fadeIn', ritardo=600, durata=600,
                  # la centrifuga gira con lo scorrimento: due giri mentre l'oblò si apre
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 1, 'o': 100, 'r': 0},
                                                                 {'p': 12, 's': 1, 'o': 100, 'r': 90},
                                                                 {'p': 70, 's': 6, 'o': 100, 'r': 720},
                                                                 {'p': 80, 's': 7, 'o': 0, 'r': 800},
                                                                 {'p': 100, 's': 7, 'o': 0, 'r': 800}]},
                  responsive={'mobile': {'x': 18.4, 'y': 23.4, 'width': 63.2, 'height': 29.1}}),
    ]
    via = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0, 'o': 100}, {'p': 10, 'x': 0, 'o': 100}, {'p': 35, 'x': -500, 'o': 0},
                                               {'p': 100, 'x': -500, 'o': 0}]}
    testi = [
        K.livello('tg-o', contenuto='Trentagradi · capi che durano · Prato', x=6, y=18, w=40, size=13, peso='500',
                  colore=C['acc'], letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(10), 'y': 58, 'x': 7, 'width': 86}}),
        K.livello('tg-t1', contenuto='Lavare', tag='h1', x=6, y=24, w=42, size=110, peso='700', colore=C['testo'], lineHeight=1.0,
                  letterSpacing=-3, entra='maskFromBottom', ritardo=350, durata=1000, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(50), 'y': 60.5, 'x': 7, 'width': 90}}),
        K.livello('tg-t2', fontFamily='Rubik', contenuto='a trenta gradi.', tag='p', x=6, y=38, w=42, size=64, peso='700',
                  colore=C['acc'], lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(34), 'y': 65.5, 'x': 7, 'width': 90}}),
        K.livello('tg-s', contenuto='Magliette, felpe e pantaloni di cotone pesante, garantiti cento lavaggi. Sull’etichetta c’è il '
                                    'contalavaggi: segnate voi.', x=6, y=50, w=36, size=17, peso='400', colore=C['tenue'],
                  lineHeight=1.6, ritardo=800, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(13), 'y': 70, 'x': 7, 'width': 86}}),
        K.livello('tg-b', tipo='button', contenuto='I capi', x=6, y=66, w='auto', size=15, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=17, borderRadius=999, entra='fadeInUp', ritardo=1000, buttonUrl=CAPI, scrollMove=via,
                  hover={'bgColor': C['testo'], 'duration': 250}, responsive={'mobile': {'y': 77, 'x': 7, 'fontSize': m(12)}}),
        K.livello('tg-l', tipo='button', contenuto='Il programma', x=14.5, y=66, w='auto', size=15, peso='600', colore=C['testo'],
                  bgColor='transparent', padding=17, borderRadius=999, borderWidth=1, borderColor=mix(C['testo'], 40),
                  entra='fadeInUp', ritardo=1100, buttonUrl=LAVAGGIO, scrollMove=via,
                  hover={'bgColor': C['sala'], 'duration': 250},
                  responsive={'tablet': {'x': 17.5}, 'mobile': {'y': 77, 'x': 35, 'fontSize': m(12)}}),
        # la manopola dei programmi, in basso a sinistra
        K.livello('tg-prog', contenuto='Cotone · 30° · 1200 giri · 54 min', x=6, y=82, w=36, size=13, peso='500', colore=C['tenue'],
                  fontFamily=MONO, letterSpacing=1, ritardo=1300, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(10), 'y': 84, 'x': 7, 'width': 86}}),
    ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 86, 'o': 0}, {'p': 96, 'o': 100}, {'p': 100, 'o': 100}]}
    veli = K.veli('tg-velo', 'basso', 30, 70, colore=C['nero'], forza=84, mobile_da=62)
    for L in veli:
        L['scrollMove'] = fine
    finali = [
        K.livello('tg-f1', contenuto='Cento giri così, e la maglietta è uguale.', tag='p', x=6, y=74, w=70, size=54, peso='700',
                  colore='#FFFFFF', fontFamily='Rubik', lineHeight=1.05, scrollMove=fine, entra='fadeIn', ritardo=0,
                  responsive={'mobile': {'fontSize': m(28), 'y': 76, 'x': 7, 'width': 86}}),
        K.livello('tg-hint', contenuto='Scorrete: si apre l’oblò ↓', x=56, y=90, w=38, size=13, peso='500', colore=C['tenue'],
                  fontFamily=MONO, ritardo=1400, textAlign='right',
                  scrollMove={'inizio': 0, 'fine': 10, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 92, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
    ]
    sl = [K.diapositiva('tg-1', K.sfondo_video(V_CESTELLO, overlay=0.08, colore=C['nero']), veli + livelli + testi + finali,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400},
                       globalBackground={'type': 'color', 'color': BIANCO})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Trentagradi · Prato, via Pistoiese 120', ['Cento lavaggi,', 'garantiti.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Una maglietta si giudica al cinquantesimo lavaggio, non in negozio. Le nostre sono di cotone '
                                'da 240 grammi, cucite doppie, tinte in capo: dopo cento lavaggi a trenta gradi hanno la stessa '
                                'misura e lo stesso colore. Se non è così, ve la cambiamo.', size=17),
                      K.button(t, 'Il lavaggio', LAVAGGIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Nuova e dopo cento lavaggi', ['Trascinate:', 'non si vede la differenza.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (MAG[0], MAG[2], 'Maglietta Cento', 'Il colore e la misura del primo giorno.'),
            (MOD[2], MOD[4], 'Felpa Pesante', 'Il collo non si allarga, i polsini tengono.'),
            (COT[1], COT[3], 'Pantalone Giro', 'Le cuciture doppie non si torcono.')], prima='Nuova', dopo='Lavaggio 100')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I capi', ['Quattro basi,', 'per sempre.'], size=60)]),
        t.one([M.capi(K, t, [
            (MOD[0], 'Magliette', 'Cento', '', 'Cotone 240 g, girocollo, sei colori.', '29 €', ''),
            (MOD[3], 'Felpe', 'Pesante', '', 'Garzata dentro, il collo che non si allarga.', '69 €', 'nuova'),
            (COT[0], 'Pantaloni', 'Giro', '', 'Tela di cotone, cuciture doppie.', '79 €', ''),
            (MAG[3], 'Camicie', 'Stendi', '', 'Si asciuga senza ferro da stiro.', '59 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il programma', ['Quattro numeri', 'sull’etichetta.'], size=60)]),
        t.one([P.schede_icone(K, t, [('30', '°C', 'Trenta', ' gradi', 'Bastano: il cotone pesante si lava bene anche così, e '
                                      'non si restringe.', 'thermometer'),
                                     ('1200', 'giri', 'Milleduecento', '', 'La centrifuga giusta: oltre, il cotone si sfibra.', 'refresh-cw'),
                                     ('0', 'asciugatrici', 'Zero', '', 'Si stende all’ombra: il sole sbiadisce, l’asciugatrice '
                                      'restringe.', 'wind'),
                                     ('100', 'lavaggi', 'Cento', '', 'Garantiti: segnateli sul contalavaggi.', 'badge-check')])]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['30°', '1200 giri', 'Stendere all’ombra', 'Cento lavaggi', 'Niente asciugatrice'],
                                          fondo=C['acc'], colore='#FFFFFF', size=54)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(K.video_frase(t, V_LENZUOLA, 'All’ombra', 'Il sole <em>sbiadisce.</em>',
                             'Stendete all’ombra e al vento: il colore dura il doppio.', overlay=20))
    out.append(M.lettera(K, t, 'La lettera', ['Ogni cento lavaggi,', 'una lettera.'],
                         'Scherziamo: una ogni due mesi. I capi nuovi, i consigli per lavarli, i negozi dove trovarli.',
                         bg=solid(C['sala'])))
    return out


def build_capi():
    t = Tpl(ID, 'i-capi')
    out = [K.intro(t, 'I capi', ['Otto basi,', 'cento lavaggi l’una.'],
                   'Cotone pesante tinto in capo, cuciture doppie, il contalavaggi sull’etichetta. I prezzi sono quelli '
                   'consigliati.', bg=K.photo_bg(MAG[1]), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Sopra', ['Magliette,', 'felpe, camicie.'], size=60)]),
        t.one([M.capi(K, t, [
            (MOD[0], 'Magliette', 'Cento', '', 'Cotone 240 g, girocollo.', '29 €', ''),
            (MOD[5], 'Magliette', 'Cento', ' lunga', 'Le maniche lunghe, polsini doppi.', '35 €', ''),
            (MOD[3], 'Felpe', 'Pesante', '', 'Garzata dentro, collo che tiene.', '69 €', 'nuova'),
            (MAG[3], 'Camicie', 'Stendi', '', 'Senza ferro da stiro.', '59 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Sotto', ['Pantaloni', 'e il resto.'], size=60)]),
        t.one([M.capi(K, t, [
            (COT[0], 'Pantaloni', 'Giro', '', 'Tela di cotone, cuciture doppie.', '79 €', ''),
            (COT[2], 'Pantaloni', 'Giro', ' corto', 'Al ginocchio, per l’estate.', '59 €', ''),
            (COT[4], 'Calze', 'Cento', '', 'Tre paia, rinforzate al tallone.', '19 €', ''),
            (MAG[4], 'Tute', 'Domenica', '', 'Felpa e pantalone, da casa e da fuori.', '119 €', 'ultime')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Lavati', 'cento volte.'], size=60)]),
        P.fila_foto(K, t, [(PER[0], 'La maglietta nera dopo un anno'), (MOD[1], 'La maglietta bianca'), (PER[3], 'La felpa grigia'),
                           (MOD[6], 'La maglietta verde')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_lavaggio():
    t = Tpl(ID, 'il-lavaggio')
    out = [K.intro(t, 'Il lavaggio', ['Le istruzioni', 'sono corte.'],
                   'Trenta gradi, milleduecento giri, all’ombra. Il resto lo fa il cotone, se è quello giusto. Ecco perché il '
                   'nostro dura cento lavaggi.', bg=K.video_bg(V_BLU), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il capo', ['Tre cose', 'che lo fanno durare.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'il cotone', CAM[1], 'Coltivare', 'Fibra', 'lunga', 'Cotone a fibra lunga, che non fa pallini e non si sfibra.',
             '240', 'grammi al metro'),
            ('2', 'la tintura', LAN[0], 'Tingere', 'In capo', 'non in pezza', 'Il capo si tinge già cucito: il colore entra anche '
             'nelle cuciture.', '1', 'bagno di colore'),
            ('3', 'la cucitura', STI[0], 'Cucire', 'Punto', 'doppio', 'Ogni cucitura è doppia: non si torce e non si apre.', '2',
             'fili')], icona='shirt')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'A Prato', ['Chi lava', 'e rilava.'], size=60)]),
        K.squadra(t, [(PER[2], 'Lucia Bertini', 'Fondatrice', 'Ha lavato la stessa maglietta trecento volte per provarla.',
                       'center 25%'),
                      (PER[4], 'Wei Chen', 'Tintoria', 'Tinge i capi già cuciti, a bagno.', 'center 25%'),
                      (PER[1], 'Sara Gori', 'Il laboratorio prove', 'Conta i lavaggi: il record è a 412.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Lavarli bene', ['Le domande', 'del cesto della biancheria.'], [
        ('Posso lavarli a quaranta gradi?', 'Sì, ogni tanto: ma a trenta durano di più e si risparmia corrente.'),
        ('E l’ammorbidente?', 'Non serve: il cotone pesante resta morbido da solo. Rovina le fibre.'),
        ('Si stirano?', 'Le camicie Stendi no. Le magliette, se le stendete tirate, nemmeno.'),
        ('E se dopo cento lavaggi è rovinata?', 'Portatecela: ve la cambiamo, e la vecchia diventa stracci per la tintoria.'),
    ], 'tg-faq', bg=False))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove trovarli,', 'ancora piegati.'],
                   'Nei negozi che ci vendono e nella nostra lavanderia di Prato, dove si provano e si lavano.',
                   bg=K.photo_bg(LAN[1]), size=88)]
    out.append(M.negozi(K, t, [
        ('Prato', 'Lavanderia Trentagradi', 'via Pistoiese 120'), ('Firenze', 'Centrifuga', 'via Gioberti 44'),
        ('Milano', 'Basico', 'via Vigevano 9'), ('Torino', 'Bucato', 'via Nizza 70'),
        ('Bologna', 'Stendino', 'via Mascarella 26'), ('Roma', 'Mollette', 'via del Pigneto 30'),
        ('Palermo', 'Ammollo', 'via Maqueda 210'), ('Amsterdam', 'Dertig Graden', 'Haarlemmerstraat 54')],
        nota='Nella lavanderia di Prato si lavano gratis i capi Trentagradi, anche vecchi.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('number', 'A che lavaggio siete', 'lavaggi'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['A che lavaggio', 'siete arrivati?'],
                                'Mandateci il numero del contalavaggi: il cliente col numero più alto vince una felpa.', VOCI, campi,
                                'Trentagradi — messaggio', 'Invia', 'Grazie! Segniamo il vostro numero.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La lavanderia', ['Sulla Pistoiese,', 'con le lavatrici in vetrina.'], size=54,
                                layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'lunedì — sabato 9 — 19'), ('Lavatrici', 'gratis per i capi Trentagradi'),
                                  ('In treno', 'Prato Centrale, poi il 2')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Trentagradi · lavanderia', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='lavanderia', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · persa in lavatrice', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è rimasta nel cestello.</em>', size=72, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Come un calzino: c’era, adesso non c’è. Torniamo all’inizio.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ROSSO, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'L’oblò che si apre',
    ['moda', 'abbigliamento', 'basic', 'cotone', 'marchio', 'prato', 'chiaro', 'slider pro', 'scorrimento'],
    'Un marchio di magliette, felpe e pantaloni garantiti cento lavaggi: in apertura il fronte bianco di una lavatrice, e '
    'scorrendo l’oblò si allarga, col bordo che gira come la centrifuga, finché il cestello riempie lo schermo; poi il capo '
    'nuovo e dopo cento lavaggi da trascinare, il programma, i capi.',
    [('I capi', CAPI), ('Il lavaggio', LAVAGGIO), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('i-capi', 'I capi', 'i-capi'), ('il-lavaggio', 'Il lavaggio', 'il-lavaggio'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Hanken Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Rubik\', system-ui, sans-serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Hanken Grotesk', 'Rubik', 'IBM Plex Mono'], corsivo_di=[],
    peso_titoli='700', raggio=16, grana=0, titoli_line='1.0', titoli_spazio='-0.025em')
THEME['cursor']['style'] = 'centrifuga'
THEME['cursor']['ring_color'] = BORDO
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-capi.json': build_capi(), 'il-lavaggio.json': build_lavaggio(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
