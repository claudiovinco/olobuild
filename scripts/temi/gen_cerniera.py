# -*- coding: utf-8 -*-
"""Genera il tema «Cerniera» (id cerniera): un laboratorio di Firenze che fa giubbotti di pelle, bomber e borse dove la
zip è il disegno: zip di ottone a vista, doppie, di traverso, che si aprono per cambiare la forma del capo.

Apertura: «la zip che si apre». Lo slider resta fermo in cima mentre si scorre: lo schermo è chiuso da due lembi di pelle
nera uniti al centro da una cerniera coi denti di metallo. Scorrendo il cursore della zip scende dall'alto al fondo, poi i
due lembi si spalancano a V intorno al fondo, come una giacca che si apre, e se ne vanno ai lati: sotto c'è il video.
Più giù i capi «chiusi e aperti» col cursore da trascinare.

Tema scuro: nero, acciaio e l'arancio della linguetta. Titoli in Big Shoulders Display, testo in Chivo, dati in Roboto
Mono. Logo: CERNIERA con la fila dei denti e la linguetta arancio. Cursore: zip (il cursore della cerniera, che lascia i
denti chiusi dove passa).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'cerniera'
NAME = 'Cerniera'

HEX = {
    'primary': '#FF5A1F', 'primary_contrast': '#111214', 'secondary': '#9AA1AB', 'secondary_contrast': '#111214',
    'accent': '#FFB000', 'dark': '#0A0B0C', 'light': '#FFFFFF', 'muted': '#1A1C20', 'muted_contrast': '#ECEEF1',
    'text': '#ECEEF1', 'text_muted': '#A9B0B9', 'background': '#111214', 'border': '#2C3036', 'link': '#FF5A1F',
}
PELLE, ACCIAIO = '#16171A', '#C9CCD1'

HOME, CAPI, ZIP, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:la-zip}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'lab@cerniera.example', '+39 055 000 3381', 'tel:+390550003381'
LAT, LNG = '43.7680', '11.2620'

V_ARANCIO, V_LAVORO, V_BIANCA, V_NERA, V_VERDE, V_ZAINO, V_POLSO, V_NEON, V_DOPPIA = (
    'pexels-video-9222684', 'pexels-video-34740346', 'pexels-video-4201601', 'pexels-video-6862418', 'pexels-video-27095041',
    'pexels-video-7055179', 'pexels-video-16016833', 'pexels-video-10179643', 'pexels-video-6371945')
VIDEOS = [V_ARANCIO, V_LAVORO, V_BIANCA, V_NERA, V_VERDE, V_ZAINO, V_POLSO, V_NEON, V_DOPPIA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
ZIPF = f('4109774', '4109757', '4109761', '6296726', '9286992', '5335403')
PEL = f('32836908', '36484175', '32310029', '29305191', '28451072', '31896868')
ZAI = f('4638311', '14936657', '16474198', '17862336', '31637024', '1170184')
BOM = f('19781191', '19824500', '33235658', '15909155', '15880475', '15988333')
MET = f('16137829', '17924253', '5036874', '39007243')
BOR = f('6167276', '16359294', '37923465', '7869710', '37923459')
ANT = f('13085468', '9502482', '28758136', '37171523', '18476548', '32179687')
STR = f('17894643', '9242085', '18496160', '18838718', '7415156')
LAB = f('37443512', '4452610', '4452619', '4452611', '40080052')
LOGO = 'logo-cerniera-chiaro.png'
PHOTOS = ZIPF + PEL + ZAI + BOM + MET + BOR + ANT + STR + LAB
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO, 'logo-cerniera-scuro.png'}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True)
C = K.c
MONO = 'Roboto Mono'
VOCI = [('map-pin', 'Via dei Conciatori 8 · Firenze', NEGOZI + '#laboratorio'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Cerniera', LOGO, 178, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO, '1196/296', 'Cerniera fa a Firenze giubbotti, bomber e borse dove la zip non si nasconde: '
                   'si apre, si sposta, cambia la forma del capo. Ottone fuso, pelle conciata in Toscana.',
                   [('La collezione', CAPI), ('La zip', ZIP), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'Il laboratorio', 'Via dei Conciatori 8, Firenze<br>Martedì — sabato 10 — 19', VOCI,
                   '© 2026 Cerniera · Firenze · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=440)


# ── L'apertura: la zip che si apre ──
def apertura(t):
    m = K.mob
    # i due lembi: chiusi finché il cursore scende (fino al 40% del tratto), poi si aprono a V attorno al fondo e se ne vanno
    def lembo(lato):
        segno = -1 if lato == 's' else 1
        return {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': 0, 'x': 0}, {'p': 42, 'r': 0, 'x': 0},
                                                    {'p': 62, 'r': segno * 14, 'x': 0}, {'p': 86, 'r': segno * 22, 'x': segno * 1100},
                                                    {'p': 100, 'r': segno * 22, 'x': segno * 1100}]}
    pelle = ('background-image:radial-gradient(circle at 30% 20%,rgba(255,255,255,.05),transparent 60%),'
             'repeating-radial-gradient(circle at 50% 50%,rgba(255,255,255,.018) 0 1px,transparent 1px 4px);')
    denti = 'background-image:repeating-linear-gradient(180deg,%s 0 10px,transparent 10px 20px);' % ACCIAIO
    livelli = [
        K.livello('zi-sx', tipo='shape', x=0, y=0, w=50.06, h=100, bgColor=PELLE, entra='fadeIn', ritardo=0, durata=400,
                  customCSS=pelle + 'transform-origin:100% 100%;', scrollMove=lembo('s'),
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 50.1, 'height': 100}}),
        K.livello('zi-dx', tipo='shape', x=49.94, y=0, w=50.06, h=100, bgColor=PELLE, entra='fadeIn', ritardo=0, durata=400,
                  customCSS=pelle + 'transform-origin:0% 100%;', scrollMove=lembo('d'),
                  responsive={'mobile': {'x': 49.9, 'y': 0, 'width': 50.1, 'height': 100}}),
        # i denti: due file sfalsate sul bordo di ogni lembo, che girano attorno allo stesso perno del loro lembo
        K.livello('zi-ds', tipo='shape', x=49.2, y=0, w=0.86, h=100, bgColor='transparent', entra='fadeIn', ritardo=0, durata=400,
                  customCSS=denti + 'transform-origin:100% 100%;', scrollMove=lembo('s'),
                  responsive={'mobile': {'x': 48.4, 'y': 0, 'width': 1.7, 'height': 100}}),
        K.livello('zi-dd', tipo='shape', x=49.94, y=0.6, w=0.86, h=99.4, bgColor='transparent', entra='fadeIn', ritardo=0,
                  durata=400, customCSS=denti + 'transform-origin:0% 100%;', scrollMove=lembo('d'),
                  responsive={'mobile': {'x': 49.9, 'y': 0.3, 'width': 1.7, 'height': 99.7}}),
    ]
    # il cursore della zip: il corpo d'acciaio e la linguetta arancio, che scendono dall'alto al fondo
    giu = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0, 'o': 100}, {'p': 42, 'y': 690, 'o': 100}, {'p': 52, 'y': 690, 'o': 0},
                                               {'p': 100, 'y': 690, 'o': 0}]}
    livelli += [
        K.livello('zi-corpo', tipo='shape', x=48.75, y=1.5, w=2.5, h=7, bgColor=ACCIAIO, borderRadius=6, entra='fadeInDown',
                  ritardo=300, durata=700, scrollMove=giu, responsive={'mobile': {'x': 47.2, 'y': 1, 'width': 5.6, 'height': 3}}),
        K.livello('zi-linguetta', tipo='shape', x=49.3, y=8.2, w=1.4, h=9, bgColor=C['acc'], borderRadius=4, entra='fadeInDown',
                  ritardo=450, durata=700, scrollMove=giu, customCSS='box-shadow:0 6px 18px rgba(0,0,0,.45);',
                  responsive={'mobile': {'x': 48.5, 'y': 3.9, 'width': 3, 'height': 3.6}}),
    ]
    # i titoli stanno sui lembi e se ne vanno con loro
    def testo_lembo(lid, lato, contenuto, x, y, w, h, size, colore, mob, **kw):
        pivot_x = (50 - x) / w * 100
        pivot_y = (100 - y) / h * 100
        return K.livello(lid, contenuto=contenuto, x=x, y=y, w=w, h=h, size=size, colore=colore,
                         customCSS='transform-origin:%.1f%% %.1f%%;' % (pivot_x, pivot_y), scrollMove=lembo(lato),
                         responsive={'mobile': mob}, **kw)
    livelli += [
        testo_lembo('zi-o', 's', 'Cerniera · giubbotti e borse · Firenze', 6, 18, 40, 4, 13, mix(ACCIAIO, 90),
                    {'fontSize': m(10), 'y': 12, 'x': 6, 'width': 40, 'height': 4}, peso='500', letterSpacing=3,
                    textTransform='uppercase', fontFamily=MONO, ritardo=200),
        testo_lembo('zi-t1', 's', 'Aprite', 6, 26, 40, 22, 150, '#FFFFFF', {'fontSize': m(52), 'y': 22, 'x': 6, 'width': 42, 'height': 8},
                    tag='h1', peso='800', lineHeight=0.9, entra='maskFromBottom', ritardo=350, durata=1000),
        testo_lembo('zi-t2', 'd', 'la zip.', 56, 26, 40, 22, 150, C['acc'], {'fontSize': m(52), 'y': 30, 'x': 54, 'width': 42, 'height': 8},
                    fontFamily='Big Shoulders Display', tag='p', peso='800', lineHeight=0.9, entra='maskFromBottom', ritardo=550,
                    durata=1000),
        testo_lembo('zi-s', 'd', 'Giubbotti, bomber e borse dove la cerniera è il disegno: si apre, si sposta, cambia la forma.',
                    56, 56, 34, 12, 17, mix('#FFFFFF', 88), {'fontSize': m(13), 'y': 40, 'x': 54, 'width': 42, 'height': 12},
                    peso='400', lineHeight=1.55, ritardo=800),
        K.livello('zi-hint', contenuto='Scorrete: si apre ↓', x=56, y=88, w=30, size=13, peso='500', colore=mix('#FFFFFF', 80),
                  fontFamily=MONO, ritardo=1200, scrollMove={'inizio': 0, 'fine': 12, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 70, 'x': 54, 'width': 42}}),
    ]
    # quando i lembi se ne sono andati: il video e una riga sola, coi pulsanti
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 80, 'o': 0}, {'p': 94, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += K.veli('zi-velo', 'basso', 30, 70, colore=C['nero'], forza=86, mobile_da=60)
    for L in livelli[-2:]:
        L['scrollMove'] = fine
    livelli += [
        K.livello('zi-f1', contenuto='La zip è il disegno.', tag='p', x=6, y=70, w=60, size=72, peso='800', colore='#FFFFFF',
                  fontFamily='Big Shoulders Display', lineHeight=1.0, scrollMove=fine, entra='fadeIn', ritardo=0,
                  responsive={'mobile': {'fontSize': m(34), 'y': 78, 'x': 6, 'width': 88}}),
        K.livello('zi-b', tipo='button', contenuto='La collezione', x=6, y=84, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=4, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'y': 86, 'x': 6, 'fontSize': m(11)}}),
        K.livello('zi-l', tipo='button', contenuto='La zip', x=20, y=84, w='auto', size=12, peso='600', colore='#FFFFFF',
                  bgColor='transparent', padding=16, borderRadius=4, borderWidth=1, borderColor=mix('#FFFFFF', 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=ZIP, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix('#FFFFFF', 14), 'borderColor': '#FFFFFF', 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'y': 86, 'x': 55, 'fontSize': m(11)}}),
    ]
    sl = [K.diapositiva('zi-1', K.sfondo_video(V_NEON, overlay=0.2, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2000, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Cerniera · Firenze, via dei Conciatori 8', ['Una zip', 'non si nasconde.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Nei giubbotti di sempre la cerniera sta sotto una patta. Nei nostri sta davanti, di ottone '
                                'fuso, e fa qualcosa: apre una tasca nascosta, trasforma il bomber in gilet, la borsa in zaino. '
                                'La pelle la conciamo a Santa Croce sull’Arno.', size=17),
                      K.button(t, 'La zip', ZIP, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Chiusa e aperta', ['Trascinate:', 'è lo stesso capo.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (PEL[2], PEL[4], 'Giubbotto Doppio', 'Chiuso è un giubbotto, con la zip di traverso aperta diventa un gilet.'),
            (ZAI[1], BOR[2], 'Borsa Due Vite', 'Una zip lungo il fianco: borsa a mano o zaino.'),
            (BOM[0], BOM[3], 'Bomber Fodera', 'Si apre la zip della schiena e la fodera arancio prende aria.')],
            prima='Chiusa', dopo='Aperta')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro capi,', 'undici zip.'], size=60)]),
        t.one([M.capi(K, t, [
            (PEL[0], 'Giubbotti', 'Doppio', '', 'Pelle nera, due zip incrociate.', '590 €', 'nuovo'),
            (BOM[1], 'Bomber', 'Fodera', '', 'Nylon e la fodera arancio da scoprire.', '320 €', ''),
            (ZAI[0], 'Zaini', 'Due Vite', '', 'Una zip lungo il fianco: borsa o zaino.', '280 €', ''),
            (ANT[0], 'Antivento', 'Spiffero', '', 'Zip ascellari che si aprono in salita.', '210 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Ottone fuso', 'Pelle toscana', 'Zip a vista', 'Si apre tutto'],
                                          fondo=C['acc'], colore=C['nero'], size=60)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(K.video_frase(t, V_ARANCIO, 'Il gesto', 'Zzzip: <em>e cambia.</em>',
                             'Una zip che apre una tasca, un’altra che allarga il collo. Ogni capo ne ha almeno due.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Aperti,', 'per strada.'], size=60)]),
        P.fila_foto(K, t, [(STR[0], 'Il giubbotto aperto'), (PEL[5], 'La pelle nera e la zip d’ottone'),
                           (ANT[2], 'L’antivento con le zip ascellari'), (STR[3], 'Il bomber con la fodera')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Una zip nuova', 'ogni stagione.'],
                         'I capi nuovi, le zip che fondiamo, i negozi dove arrivano. Quattro volte l’anno.', bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Otto capi,', 'ventitré zip.'],
                   'Pelle conciata al vegetale, nylon riciclato, ottone fuso a Firenze. I prezzi sono quelli consigliati.',
                   bg=K.photo_bg(LAB[1]), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Giubbotti e bomber', ['La zip', 'davanti.'], size=60)]),
        t.one([M.capi(K, t, [
            (PEL[0], 'Giubbotti', 'Doppio', '', 'Due zip incrociate, pelle nera.', '590 €', 'nuovo'),
            (PEL[3], 'Giubbotti', 'Biker', '', 'La zip di traverso, il collo che si allarga.', '620 €', ''),
            (BOM[1], 'Bomber', 'Fodera', '', 'Nylon, la fodera arancio da scoprire.', '320 €', ''),
            (BOM[4], 'Bomber', 'Verde', '', 'Le maniche si staccano con la zip.', '340 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Borse e antivento', ['La zip', 'che cambia forma.'], size=60)]),
        t.one([M.capi(K, t, [
            (ZAI[0], 'Zaini', 'Due Vite', '', 'Borsa a mano o zaino, con una zip.', '280 €', ''),
            (BOR[0], 'Borse', 'Bocca', '', 'Si apre tutta, come una bocca.', '240 €', ''),
            (ANT[0], 'Antivento', 'Spiffero', '', 'Zip ascellari per la salita.', '210 €', ''),
            (ANT[5], 'Antivento', 'Notte', '', 'Riflettente, la zip fluorescente.', '230 €', 'ultimi')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Chiuse e aperte', ['Provate', 'ad aprirle.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (PEL[1], PEL[3], 'Giubbotto Biker', 'Chiuso dritto, aperto di traverso.'),
            (ZIPF[1], ZIPF[2], 'La zip d’ottone', 'Fusa a mano: i denti grossi, il cursore pesante.'),
            (ANT[1], ANT[3], 'Antivento Spiffero', 'Le zip sotto le braccia si aprono in salita.')], prima='Chiusa', dopo='Aperta')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_zip():
    t = Tpl(ID, 'la-zip')
    out = [K.intro(t, 'La zip', ['Fusa, cucita,', 'messa davanti.'],
                   'Le zip le facciamo noi: i denti d’ottone fusi in una fonderia di Prato, cuciti su nastro di cotone. Pesano '
                   'il doppio di una zip normale, e si sentono.', bg=K.video_bg(V_LAVORO), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come nasce', ['Tre passaggi,', 'mille denti.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'la fonderia', MET[0], 'Fondere', 'I denti', 'd’ottone', 'Ogni dente si fonde in uno stampo: una zip lunga ne '
             'ha centoventi.', '120', 'denti'),
            ('2', 'il nastro', ZIPF[3], 'Cucire', 'Il nastro', 'di cotone', 'I denti si fissano sul nastro uno a uno, a mano.', '2',
             'ore'),
            ('3', 'il capo', LAB[3], 'Montare', 'La zip', 'davanti', 'La zip si cuce sulla pelle con un punto doppio, che non si '
             'apre mai.', '1', 'giubbotto')], icona='wrench')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In laboratorio', ['Chi chiude', 'le zip.'], size=60)]),
        K.squadra(t, [(PEL[1], 'Dario Benvenuti', 'Fondatore', 'Pellettiere, figlio di pellettieri: la prima zip l’ha messa di '
                       'traverso per sbaglio.', 'center 25%'),
                      (STR[2], 'Nadia Rossi', 'Modellista', 'Disegna dove va ogni zip, e cosa apre.', 'center 25%'),
                      (STR[4], 'Kofi Mensah', 'Pellettiere', 'Cuce le zip col punto doppio.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, LAB[0], 'Il banco del laboratorio', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho aperto la zip della schiena del bomber in un concerto: si è scoperta la fodera arancio '
                                        'e mi hanno chiesto se era un pezzo unico.', 'Matteo, Prato', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove aprirle', 'con le mani.'],
                   'Le zip si provano: pesano, fanno rumore, si sentono. Ecco dove trovarle, e il laboratorio.',
                   bg=K.photo_bg(LAB[2]), size=96)]
    out.append(M.negozi(K, t, [
        ('Firenze', 'Laboratorio Cerniera', 'via dei Conciatori 8'), ('Firenze', 'Lampo', 'via Maggio 30'),
        ('Milano', 'Dente', 'corso Garibaldi 71'), ('Roma', 'Tiretto', 'via dei Coronari 40'),
        ('Bologna', 'Ottone', 'via Santo Stefano 18'), ('Napoli', 'Linguetta', 'via Chiaia 140'),
        ('Londra', 'Zip & Co', 'Redchurch Street 12'), ('Seul', 'Jipeo', 'Seongsu-dong 2-ga')],
        nota='Il laboratorio ripara le zip di qualsiasi giubbotto, anche non nostro.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una zip da riparare\nUn capo su misura\nVendere Cerniera\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una zip rotta?', 'La rifacciamo.'],
                                'Per una riparazione, un capo su misura, o per venderci nel vostro negozio.', VOCI, campi,
                                'Cerniera — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il laboratorio', ['A Santa Croce,', 'dietro la piazza.'], size=54, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('Riparazioni', 'zip di ogni marca, in un giorno'),
                                  ('A piedi', 'da piazza Santa Croce, tre minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Cerniera · laboratorio', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='laboratorio', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sulle zip.'], [
        ('Le zip d’ottone si rompono?', 'Quasi mai: se succede le rifacciamo gratis, per sempre.'),
        ('La pelle si bagna?', 'La conciatura al vegetale regge la pioggia: si asciuga lontano dai termosifoni.'),
        ('Fate capi su misura?', 'Sì: scegliete dove volete le zip, e cosa aprono.'),
        ('Spedite?', 'No: si provano nei negozi. Il negozio più vicino si fa mandare la taglia.'),
    ], 'zi-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · zip inceppata', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>si è inceppata.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Non si apre più. Torniamo all’inizio e riproviamo.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_NERA, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La zip che si apre',
    ['moda', 'abbigliamento', 'pelle', 'giubbotti', 'borse', 'firenze', 'scuro', 'slider pro', '3d'],
    'Un laboratorio di giubbotti e borse dove la zip è il disegno: in apertura lo schermo è chiuso da due lembi di pelle '
    'uniti da una cerniera, scorrendo il cursore della zip scende e i lembi si spalancano a V scoprendo il video; poi i capi '
    'chiusi e aperti da trascinare, la collezione e il laboratorio.',
    [('La collezione', CAPI), ('La zip', ZIP), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('la-zip', 'La zip', 'la-zip'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Chivo\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Big Shoulders Display\', Impact, sans-serif',
    '\'Roboto Mono\', ui-monospace, Menlo, monospace', ['Chivo', 'Big Shoulders Display', 'Roboto Mono'], corsivo_di=[],
    peso_titoli='800', raggio=4, grana=4, titoli_line='0.95', titoli_spazio='0.01em')
THEME['cursor']['style'] = 'zip'
THEME['cursor']['ring_color'] = ACCIAIO
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'la-zip.json': build_zip(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
