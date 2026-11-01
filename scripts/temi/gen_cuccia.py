# -*- coding: utf-8 -*-
"""Genera il tema «Cuccia» (id cuccia): un canile e gattile gestito da volontari a Parma: le adozioni, gli animali anziani,
le passeggiate del sabato, gli affidi a distanza e le donazioni.

Tema chiaro e caldo: crema, marrone nocciola, senape e verde salvia. Titoli in Fredoka, testo in Nunito, dati in Roboto
Mono.

Apertura: «il mazzo». Lo slider resta fermo in cima mentre si scorre: sul video di un cane che corre sul prato, a destra,
un mazzo di schede con le foto degli ospiti, un po’ storte, ognuna col cartellino del nome. Scorrendo la scheda in cima
vola via ruotando e scopre quella sotto, una alla volta: Briciola, Tobia, Zenzero, Pepe; l’ultima è il nonno Gino, che
resta, col pulsante per conoscerlo. Cursore: lo scodinzolo.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'cuccia'
NAME = 'Cuccia'

HEX = {
    'primary': '#8A4B1F', 'primary_contrast': '#FFFFFF', 'secondary': '#F5C04A', 'secondary_contrast': '#2A1D14',
    'accent': '#8DB38F', 'dark': '#2A1D14', 'light': '#FFFFFF', 'muted': '#FBF1E1', 'muted_contrast': '#2A1D14',
    'text': '#2A1D14', 'text_muted': '#6B5A4C', 'background': '#FFF9F0', 'border': '#EBDCC6', 'link': '#8A4B1F',
}

HOME, ADOTTA, AIUTA, VISITA = '{{PAGINA:home}}', '{{PAGINA:adotta}}', '{{PAGINA:aiutaci}}', '{{PAGINA:vieni-a-trovarci}}'
MAIL, PHONE, PHONE_TEL = 'adozioni@cuccia.example', '+39 0521 000 773', 'tel:+390521000773'
LAT, LNG = '44.7780', '10.3530'

V_CORSA, V_PRATO, V_SALTO, V_BOSCO, V_ABBRACCIO, V_CUCCIOLI, V_GIOCO, V_GATTO, V_MICIO = (
    'pexels-video-12363704', 'pexels-video-35282483', 'pexels-video-16573252', 'pexels-video-10363702',
    'pexels-video-7474315', 'pexels-video-11534028', 'pexels-video-8129587', 'pexels-video-35087753', 'pexels-video-13925467')
PHOTOS = [
    'pexels-33229631.jpg', 'pexels-29836295.jpg', 'pexels-33229625.jpg', 'pexels-6291571.jpg', 'pexels-15183362.jpg',
    'pexels-36372421.jpg', 'pexels-14789916.jpg', 'pexels-35710849.jpg', 'pexels-34093718.jpg', 'pexels-14488468.jpg',
    'pexels-12510164.jpg', 'pexels-35445398.jpg', 'pexels-30136926.jpg', 'pexels-6291572.jpg', 'pexels-8564868.jpg',
    'pexels-11605151.jpg', 'pexels-16614830.jpg', 'pexels-16652386.jpg', 'pexels-28795606.jpg', 'pexels-6900232.jpg',
    'pexels-37220239.jpg', 'pexels-38189498.jpg', 'pexels-38189496.jpg', 'pexels-16465605.jpg', 'pexels-10804575.jpg',
    'pexels-34951098.jpg', 'pexels-16652369.jpg', 'pexels-30577798.jpg', 'pexels-8434723.jpg', 'pexels-30577793.jpg',
    'pexels-5420821.jpg', 'pexels-1375722.jpg', 'pexels-30400274.jpg', 'pexels-19027991.jpg', 'pexels-1383813.jpg',
    'pexels-21086801.jpg', 'pexels-31024733.jpg', 'pexels-9376756.jpg', 'pexels-25695963.jpg', 'pexels-32910493.jpg',
    'pexels-23021510.jpg', 'pexels-31545055.jpg', 'pexels-32588777.jpg', 'pexels-28914761.jpg', 'pexels-16621946.jpg',
]
VIDEOS = [V_CORSA, V_PRATO, V_SALTO, V_BOSCO, V_ABBRACCIO, V_CUCCIOLI, V_GIOCO, V_GATTO, V_MICIO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=20, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
SENAPE, SALVIA = C['sec'], C['acc2']
MONO = 'Roboto Mono'
VOCI = [('map-pin', 'Strada del Canile 3 · Parma', VISITA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Cuccia', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Cuccia', 'Canile e gattile a Parma, gestito da volontari dal 1992. Novanta cani, sessanta gatti, una '
                   'cuccia per ognuno finché non trova casa.',
                   [('Adotta', ADOTTA), ('Aiutaci', AIUTA), ('Vieni a trovarci', VISITA), ('Privacy', '/privacy-policy/')],
                   'Le visite', 'Martedì — domenica 10 — 12:30 e 15 — 18<br>Lunedì chiuso', VOCI,
                   '© 2026 Cuccia · Parma · Organizzazione di volontariato · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=170,
                   fondo=C['sala'])


# ── L'apertura: il mazzo ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
MAZZO = [  # foto, nome, riga, inclinazione (dalla scheda in fondo a quella in cima)
    ('pexels-23021510.jpg', 'Nonno Gino', '11 anni · cerca un divano', 2),
    ('pexels-30136926.jpg', 'Pepe', '4 mesi · cucciolo', -5),
    ('pexels-14789916.jpg', 'Zenzero', '2 anni · gatto rosso', 6),
    ('pexels-29836295.jpg', 'Tobia', '5 anni · meticcio', -4),
    ('pexels-33229631.jpg', 'Briciola', '3 anni · meticcia', 3),
]


def apertura(t):
    m = K.mob
    # la scheda: centro e misure (computer e telefono), proporzione 4:5
    CX, CY, CW, CH = 73.0, 52.0, 23.0, 52.0
    CXM, CYM, CWM, CHM = 50.0, 63.0, 66.0, 37.0
    n = len(MAZZO)
    livelli = []
    for k, (foto, nome, riga, inc) in enumerate(MAZZO):
        ordine = n - 1 - k                      # 0 = in cima, vola via per prima
        a = 8 + ordine * 18
        resta = k == 0
        via = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0, 'y': 0, 'r': inc, 'o': 100}]}
        if resta:
            via['tappe'] += [{'p': 100, 'x': 0, 'y': 0, 'r': inc, 'o': 100}]
        else:
            lato = 1 if ordine % 2 == 0 else -1
            via['tappe'] += [{'p': a, 'x': 0, 'y': 0, 'r': inc, 'o': 100},
                             {'p': a + 14, 'x': 980 * lato, 'y': -140, 'r': inc + 30 * lato, 'o': 0},
                             {'p': 100, 'x': 980 * lato, 'y': -140, 'r': inc + 30 * lato, 'o': 0}]
        x, y = CX - CW / 2, CY - CH / 2
        xm, ym = CXM - CWM / 2, CYM - CHM / 2
        livelli.append(K.livello('cu-f%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover',
                                 objectPosition='center 30%', x=round(x, 2), y=round(y, 2), w=CW, h=CH, borderRadius=22,
                                 borderWidth=10, borderColor=C['chiaro'], entra='fadeInUp', ritardo=200 + k * 120, durata=900,
                                 boxShadow={'x': 0, 'y': 22, 'blur': 46, 'spread': -10, 'color': mix(C['nero'], 55)},
                                 scrollMove=via,
                                 responsive={'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': CWM, 'height': CHM}}))
        # il cartellino: in basso sulla scheda; ruota attorno al centro della scheda (transform-origin in % della sua scatola)
        lw, lh, ly = 0.78, 0.17, 0.76
        lx = (1 - lw) / 2
        ox, oy = (0.5 - lx) / lw * 100, (0.5 - ly) / lh * 100
        livelli.append(K.livello('cu-n%d' % k, contenuto='<strong>%s</strong><br><span style="font-size:.78em">%s</span>' % (nome, riga),
                                 x=round(x + lx * CW, 2), y=round(y + ly * CH, 2), w=round(lw * CW, 2), h=round(lh * CH, 2),
                                 size=20, peso='500', colore=C['nero'], lineHeight=1.3, bgColor=mix(C['chiaro'], 94),
                                 padding=10, borderRadius=14, align='center', textValign='center', fontFamily='Fredoka',
                                 entra='fadeInUp', ritardo=200 + k * 120, durata=900, scrollMove=dict(via, tappe=list(via['tappe'])),
                                 customCSS='transform-origin:%.1f%% %.1f%%;' % (ox, oy),
                                 responsive={'mobile': {'x': round(xm + lx * CWM, 2), 'y': round(ym + ly * CHM, 2),
                                                        'width': round(lw * CWM, 2), 'height': round(lh * CHM, 2),
                                                        'fontSize': m(15)}}))
        livelli[-2]['customCSS'] = 'transform-origin:50% 50%;'
    testi = [
        K.livello('cu-o', contenuto='Cuccia · canile e gattile · Parma', x=6, y=22, w=40, size=12, peso='600',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('cu-t1', contenuto='Novanta cani,', tag='h1', x=6, y=28, w=48, size=90, peso='600', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(42), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('cu-t2', fontFamily='Fredoka', contenuto='sessanta gatti.', tag='p', x=6, y=40, w=48, size=90, peso='600',
                  colore=SENAPE, lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(42), 'y': 15.5, 'x': 7, 'width': 90}}),
        K.livello('cu-s', contenuto='Ognuno con un nome, una storia e una cuccia che aspetta di lasciare. Venite a '
                                    'conoscerli: la domenica si passeggia insieme.', x=6, y=53, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 20.5, 'x': 7, 'width': 86}}),
        K.livello('cu-b', tipo='button', contenuto='Adotta', x=6, y=68, w='auto', size=15, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=17, borderRadius=999, entra='fadeInUp', ritardo=1000, buttonUrl=ADOTTA,
                  hover={'bgColor': C['nero'], 'duration': 250}, responsive={'mobile': {'y': 27.5, 'x': 7, 'fontSize': m(12)}}),
        K.livello('cu-l', tipo='button', contenuto='Aiutaci', x=15, y=68, w='auto', size=15, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=17, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 70),
                  entra='fadeInUp', ritardo=1100, buttonUrl=AIUTA,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 19}, 'mobile': {'y': 27.5, 'x': 38, 'fontSize': m(12)}}),
        K.livello('cu-hint', contenuto='Scorrete: vi presentiamo gli ospiti ↓', x=6, y=84, w=32, size=12, peso='600',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 20, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 33, 'x': 7, 'width': 86}}),
        K.livello('cu-fine', tipo='button', contenuto='Conosci Gino →', x=CX - 7, y=CY + CH / 2 + 4, w='auto', size=14,
                  peso='700', colore=C['nero'], bgColor=SENAPE, padding=14, borderRadius=999, entra='fadeIn', ritardo=0,
                  durata=300, buttonUrl=ADOTTA, hover={'bgColor': C['chiaro'], 'duration': 250},
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 20}, {'p': 86, 'o': 0, 'y': 20},
                                                                 {'p': 96, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]},
                  responsive={'mobile': {'x': CXM - 18, 'y': CYM + CHM / 2 + 2, 'fontSize': m(12)}}),
    ]
    veli = K.veli('cu-velo', 'sinistra', 36, 62, colore=C['nero'], forza=84, mobile_alto=36)
    sl = [K.diapositiva('cu-1', K.sfondo_video(V_CORSA, overlay=0.18, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=2000, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Cuccia · Parma, dal 1992', ['Una cuccia,', 'finché serve.'], size=56, layout='stack',
                                size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo un canile e un gattile gestiti da ottanta volontari. Ogni animale ha il suo box, la '
                                'sua scheda, il veterinario e qualcuno che lo porta a spasso. Lo scorso anno ne sono andati a '
                                'casa duecentoquattordici.', size=17, color=C['testo']),
                      K.button(t, 'Adotta', ADOTTA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('214', 'Adozioni nel 2025'), ('90', 'Cani'), ('60', 'Gatti'), ('80', 'Volontari')])],
              style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cercano casa', ['Girate le schede:', 'dietro c’è il carattere.'], size=56)]),
        P.carte(K, t, [
            ('pexels-33229625.jpg', 'Luna', 'Cagnolina · 6 anni', 'Tranquilla, va d’accordo coi gatti. Cerca una casa con '
             'un giardino piccolo e qualcuno che resti spesso.'),
            ('pexels-35710849.jpg', 'Oliva', 'Gatta · 1 anno', 'Curiosa, gioca con tutto. Va bene anche in appartamento, '
             'meglio con un altro gatto.'),
            ('pexels-15183362.jpg', 'Rocco', 'Cane · 4 anni', 'Grande, buonissimo, tira al guinzaglio: lo stiamo '
             'educando. Per chi ha già avuto cani.'),
            ('pexels-32588777.jpg', 'Nina', 'Cagnolina · 12 anni', 'Anziana e dolcissima: dorme tanto, chiede poco. Per lei '
             'il cibo lo paghiamo noi.'),
        ], altezza='420'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SALTO, 'La domenica', 'Una passeggiata <em>cambia la settimana.</em>',
                             'Ogni domenica mattina portiamo i cani al parco: venite anche voi, il guinzaglio ve lo diamo noi.',
                             overlay=24))
    out.append(t.section([
        t.one([K.sechead(t, 'Come si adotta', ['Quattro passi,', 'senza fretta.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'La visita', 'Venite al canile, conoscete chi vi piace.'),
                              ('02', 'Il colloquio', 'Mezz’ora per capire se siete fatti l’uno per l’altro.'),
                              ('03', 'Le passeggiate', 'Due o tre volte insieme, prima di decidere.'),
                              ('04', 'A casa', 'Con microchip, vaccini, e il nostro numero per ogni dubbio.')],
                       numero_font=MONO)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-16652369.jpg', 'Una famiglia col cane adottato', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Siamo venuti per un cucciolo. Siamo usciti con Ugo, nove anni e un orecchio storto. '
                                          'Dorme sui piedi di nostro figlio da tre anni.', 'Famiglia Ferrari, Langhirano',
                                  size='38')], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72,
              valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_ABBRACCIO, 'Affido a distanza', 'Non potete adottare? <em>Adottate a distanza.</em>',
                          'Con dieci euro al mese pagate cibo e cure di un ospite, e ricevete le sue foto.', 'Aiutaci', AIUTA,
                          overlay=18, video=True))
    return out


def build_adotta():
    t = Tpl(ID, 'adotta')
    out = [K.intro(t, 'Adotta', ['Cento e cinquanta', 'storie che aspettano.'],
                   'Qui ce ne sono alcuni. Gli altri li conoscete al canile: venite, anche solo per una passeggiata.',
                   bg=K.video_bg(V_CUCCIOLI), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Gli anziani', ['I più bravi,', 'i meno scelti.'], size=56,
                         tagline='Per chi adotta un ospite sopra i dieci anni, le cure veterinarie le paghiamo noi.')]),
        t.one([P.elenco_hover(K, t, [
            ('Gino', '11 anni · meticcio', 'Ama il divano e le carezze sulla pancia. Cammina piano, ma cammina.',
             'pexels-23021510.jpg'),
            ('Nina', '12 anni · piccola taglia', 'Dorme tanto, mangia poco, segue chi le vuole bene come un’ombra.',
             'pexels-32588777.jpg'),
            ('Brando', '10 anni · pastore', 'Grande e calmo, ha vissuto sempre in cortile: vorrebbe provare una casa.',
             'pexels-31545055.jpg'),
            ('Mimì', '13 anni · gatta', 'Fa le fusa a chiunque entri nella stanza. Va bene in appartamento.',
             'pexels-14488468.jpg'),
        ], ADOTTA, size=48)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I gatti', ['Il gattile,', 'al primo piano.'], size=56)]),
        P.fila_foto(K, t, [('pexels-6900232.jpg', 'Un gattino sul cuscino'), ('pexels-37220239.jpg', 'Due gattini che '
                                                                                                       'giocano'),
                           ('pexels-34093718.jpg', 'Un gatto tigrato'), ('pexels-12510164.jpg', 'Un gatto rosso')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_GATTO, 'Il gattile', 'Sessanta gatti, <em>sessanta caratteri.</em>',
                             'Ci sono i timidi, i chiacchieroni, quelli che vi scelgono loro.', overlay=24))
    return out


def build_aiuta():
    t = Tpl(ID, 'aiutaci')
    out = [K.intro(t, 'Aiutaci', ['Tempo, cibo,', 'o dieci euro al mese.'],
                   'Il canile vive di volontari e di donazioni. Ogni aiuto conta, anche una coperta vecchia: quelle le '
                   'usiamo tutte.', bg=K.photo_bg('pexels-16614830.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Dona', ['Quello che potete,', 'quando potete.'], size=56,
                         tagline='Le donazioni si detraggono: la ricevuta arriva per email.')]),
        K.piani(t, [
            ('Una ciotola', '15', 'una volta', 'Il cibo di un cane per una settimana', False, ''),
            ('Affido a distanza', '10', 'al mese', 'Cibo e cure di un ospite\nLe sue foto ogni mese\nUna passeggiata con lui',
             True, 'il più scelto'),
            ('Una visita', '60', 'una volta', 'Le vaccinazioni di un cucciolo\nIl microchip', False, ''),
        ], 'Dona', AIUTA + '#dona', nota='Il 5×1000: nella dichiarazione dei redditi, il codice fiscale 00000000000.'),
    ], padding='large', width='large', html_id='dona', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Volontari', ['Due ore', 'a settimana.'], size=56)]),
        t.one([P.righe(K, t, [('sab · dom', 'Le passeggiate', 'Portare i cani al parco, la mattina', '2 ore'),
                              ('ogni giorno', 'Le pulizie dei box', 'La mattina presto, in squadra', '2 ore'),
                              ('su turno', 'Il gattile', 'Pappe, lettiere e coccole', '2 ore'),
                              ('quando volete', 'Le foto', 'Per le schede degli ospiti', '1 ora')], VISITA + '#scrivi',
                       titolo=32)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_visita():
    t = Tpl(ID, 'vieni-a-trovarci')
    out = [K.intro(t, 'Vieni a trovarci', ['Il canile è aperto,', 'anche la domenica.'],
                   'Non serve prenotare per una visita. Per adottare scriveteci prima: vi presentiamo chi potrebbe fare al '
                   'caso vostro.', bg=K.photo_bg('pexels-16465605.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Vorreste', 'cosa', opzioni='Adottare un cane\nAdottare un gatto\nFare volontariato\nUn affido a '
                     'distanza'),
             K.campo('select', 'A casa avete', 'casa', opzioni='Un appartamento\nUna casa con giardino\nAltri animali'),
             K.campo('textarea', 'Raccontateci di voi', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Due righe su di voi,', 'al resto pensiamo noi.'],
                                'Vi rispondiamo entro due giorni.', VOCI, campi, 'Cuccia — adozione', 'Invia',
                                'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(K.domande(t, 'Prima di adottare', ['Le domande', 'di tutti.'], [
        ('Quanto costa adottare?', 'L’adozione è gratuita: chiediamo solo un contributo libero per le cure.'),
        ('Posso adottare se lavoro tutto il giorno?', 'Dipende dall’animale: alcuni stanno bene da soli, altri no. Ne '
                                                      'parliamo al colloquio.'),
        ('E se non va bene?', 'Ci chiamate: l’animale torna da noi, senza giudizi, e lo aiutiamo a trovare un’altra casa.'),
        ('Fate adozioni fuori Parma?', 'Sì, in tutta l’Emilia, con una visita a casa prima.'),
    ], 'cu-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In campagna,', 'a dieci minuti.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Visite', 'martedì — domenica 10 — 12:30 e 15 — 18'),
                                  ('In auto', 'dalla tangenziale, uscita 14'),
                                  ('In bici', 'la ciclabile lungo il torrente')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Cuccia · canile', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · scappato', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è scappata.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La stiamo cercando nel prato. Intanto, torniamo a casa.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_PRATO, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Associazioni e cause', 'Il mazzo delle adozioni',
    ['canile', 'gattile', 'adozioni', 'volontariato', 'parma', 'chiaro', 'slider pro', 'scorrimento'],
    'Un canile e gattile di volontari, crema e nocciola: in apertura un mazzo di schede con le foto degli ospiti sul video '
    'di un cane che corre, e scorrendo le schede volano via una alla volta fino al nonno Gino; gli animali da girare, '
    'come si adotta, l’affido a distanza, le donazioni e le visite.',
    [('Adotta', ADOTTA), ('Aiutaci', AIUTA), ('Vieni a trovarci', VISITA)],
    [('home', 'Home', None), ('adotta', 'Adotta', 'adotta'), ('aiutaci', 'Aiutaci', 'aiutaci'),
     ('vieni-a-trovarci', 'Vieni a trovarci', 'vieni-a-trovarci')],
    HEX, '\'Nunito\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fredoka\', system-ui, sans-serif',
    '\'Roboto Mono\', ui-monospace, Menlo, monospace', ['Nunito', 'Fredoka', 'Roboto Mono'], corsivo_di=[],
    peso_titoli='600', raggio=20, grana=0, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'scodinzolo'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['dark']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'adotta.json': build_adotta(), 'aiutaci.json': build_aiuta(), 'vieni-a-trovarci.json': build_visita(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
