# -*- coding: utf-8 -*-
"""Genera il tema «Abbaglio» (id abbaglio): un museo delle illusioni al Porto Antico di Genova — nove sale, trentasei
illusioni, la stanza di Ames, gli specchi infiniti, la scala che non finisce; i laboratori per le scuole.

La trovata: «l'anamorfosi». Nell'apertura le otto lettere del nome sono sparse a grandezze e angoli diversi sopra una spirale
che gira; scorrendo, solo in un punto si incastrano e la parola si legge, poi si sparpagliano in un altro modo. Più giù una
seconda scena: due griglie di righe ferme, una ruota appena e nascono onde che non ci sono (il moiré). Titoli e foto vanno
a fuoco solo a metà schermo.

Tema scuro: nero, giallo acido, magenta, viola. Titoli in Lexend Zetta (larghissimo), testo in Lexend Deca. Cursore: ipnosi.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'abbaglio'
NAME = 'Abbaglio'
CATEGORIA = 'Cultura e formazione'

HEX = {
    'primary': '#E8FF3A', 'primary_contrast': '#0B0B0D', 'secondary': '#E0218A', 'secondary_contrast': '#FFFFFF',
    'accent': '#9B85FF', 'dark': '#0B0B0D', 'light': '#FFFFFF', 'muted': '#17171C', 'muted_contrast': '#EDEDF0',
    'text': '#EDEDF0', 'text_muted': '#A3A3AE', 'background': '#0F0F12', 'border': '#2B2B33', 'link': '#E8FF3A',
}
HOME, SALE, VISITA, SCUOLE = '{{PAGINA:home}}', '{{PAGINA:le-sale}}', '{{PAGINA:la-visita}}', '{{PAGINA:scuole}}'
MAIL, PHONE, PHONE_TEL = 'biglietteria@abbaglio.example', '010 000 3636', 'tel:+390100003636'
LAT, LNG = '44.4093', '8.9245'

V_SPIRALE, V_MOIRE, V_ONDE, V_TUNNEL, V_SPECCHI, V_NEON, V_SCALA = (
    'pexels-video-35666409', 'pexels-video-5968482', 'pexels-video-31710277', 'pexels-video-35667980', 'pexels-video-8111746',
    'pexels-video-34677875', 'pexels-video-9614020')
VIDEOS = [V_SPIRALE, V_MOIRE, V_ONDE, V_TUNNEL, V_SPECCHI, V_NEON, V_SCALA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(VORTICE, ROMBI, ONDE_LUCE, ONDE_BN, LAMELLE, SPECCHI_PARETE, RAGAZZI_SPECCHI, STANZA_ROSA, STELLE, CORNICI, FRATTALE,
 CORRIDOIO_BLU, SPECCHIO_TONDO, ESAGONI, SFERE, SPIRALE_UOMO, SCALA_LEGNO, SCALA_FERRO, MUSEO_BN, SALA_MUSEO, DISEGNO,
 POLTRONE, RAGGI) = f(27404269, 4243357, 36643476, 7523946, 6810936, 39240990, 6208392, 30144056, 2889489, 7724506, 12891172,
                      36040448, 1502800, 2157888, 33797649, 19898100, 69947, 18403797, 36770103, 30435051, 28648089, 16011285,
                      15062110)
PHOTOS = [VORTICE, ROMBI, ONDE_LUCE, ONDE_BN, LAMELLE, SPECCHI_PARETE, RAGAZZI_SPECCHI, STANZA_ROSA, STELLE, CORNICI, FRATTALE,
          CORRIDOIO_BLU, SPECCHIO_TONDO, ESAGONI, SFERE, SPIRALE_UOMO, SCALA_LEGNO, SCALA_FERRO, MUSEO_BN, SALA_MUSEO, DISEGNO,
          POLTRONE, RAGGI]
DISEGNI = ['abbaglio-righe-a.svg', 'abbaglio-righe-b.svg']
LOGHI = ['abbaglio-logo-chiaro.png', 'abbaglio-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
FONT_T = 'Lexend Zetta'
VOCI = [('map-pin', 'Calata Mandraccio · Porto Antico, Genova', VISITA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


# ── gli effetti a scorrimento delle pagine ──
def a_fuoco(tile, massimo=14):
    """Messo a fuoco solo a metà schermo: entra sfocato, è nitido al centro, esce sfocato (parallax delle Avanzate)."""
    tile.setdefault('advanced', {})['parallax'] = {'blur': [{'value': massimo, 'position': 0}, {'value': 0, 'position': 45},
                                                            {'value': 0, 'position': 60}, {'value': massimo, 'position': 100}]}
    return tile


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '869/111', 'Abbaglio è un museo delle illusioni al Porto Antico di Genova: nove sale, '
                   'trentasei illusioni da toccare e fotografare, i laboratori per le scuole.',
                   [('Le sale', SALE), ('La visita', VISITA), ('Scuole', SCUOLE), ('Privacy', '/privacy-policy/')],
                   'Gli orari', 'Tutti i giorni 10 — 19<br>Sabato fino alle 22', VOCI,
                   '© 2026 Abbaglio srl · Genova · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                     ('tiktok', 'https://tiktok.com')], scuro=True, larghezza=400)


# ── L'apertura: l'anamorfosi ──
# Le lettere nelle loro caselle (x e larghezza in % della tela, computer e telefono), larghe come le lettere di Lexend Zetta
# misurate in Chrome (misura_lettere.py, a 140 px su 1440 e a 56 su 640); sparse a p 0, al loro posto fra p 46 e 58, poi
# sparse in un altro modo. Gli spostamenti sono in px della tela.
LETTERE = [('A', 10.77, 9.59, 14.69, 8.63), ('B', 20.35, 9.87, 23.32, 8.88), ('B', 30.22, 9.87, 32.2, 8.88),
           ('A', 40.09, 9.59, 41.08, 8.63), ('G', 49.67, 10.68, 49.71, 9.61), ('L', 60.35, 8.83, 59.31, 7.95),
           ('I', 69.18, 9.05, 67.26, 8.15), ('O', 78.23, 11.01, 75.41, 9.9)]
SPARSE = [(-160, -120, 1.7, -24), (90, 150, 0.55, 18), (-60, -170, 1.25, 40), (140, 110, 0.7, -12), (-120, 180, 1.9, 22),
          (170, -140, 0.6, -35), (-40, 120, 2.3, 60), (110, -90, 0.8, -18)]
DOPO = [(-210, -190, 0.6, 30), (-110, -230, 1.4, -20), (80, -250, 0.7, -42), (-260, 210, 1.2, 14), (120, -260, 0.5, -28),
        (250, 220, 1.3, 36), (190, -200, 0.4, -64), (270, 180, 1.5, 22)]


def apertura(t):
    m = K.mob
    L = [K.livello('ab-velo', tipo='shape', x=0, y=0, w=100, h=100, bgColor=mix(C['nero'], 38), entra='fadeIn', ritardo=0,
                   durata=600, esce='fadeOut')]
    for k, ((lettera, x, w, xm, wm), a, b) in enumerate(zip(LETTERE, SPARSE, DOPO)):
        tappa = lambda p, v, o=100: {'p': p, 'x': v[0], 'y': v[1], 's': v[2], 'r': v[3], 'o': o}
        moto = {'inizio': 0, 'fine': 100, 'tappe': [tappa(0, a), tappa(12, a), tappa(46, (0, 0, 1, 0)), tappa(58, (0, 0, 1, 0)),
                                                    tappa(88, b, 16), tappa(100, b, 16)]}
        L.append(K.livello('ab-l%d' % k, contenuto=lettera, x=x, y=35, w=w, size=140, peso='700', colore=C['chiaro'],
                           align='center', fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=200 + k * 90, durata=700,
                           customCSS='transform-origin:50% 50%;', scrollMove=moto,
                           responsive={'mobile': {'fontSize': m(34), 'x': xm, 'y': 39.5, 'width': wm}}))
    lampo = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 44, 'o': 0}, {'p': 50, 'o': 100}, {'p': 58, 'o': 100},
                                                 {'p': 66, 'o': 0}, {'p': 100, 'o': 0}]}
    poi = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 3}, {'p': da, 'o': 0, 'y': 3},
                                                             {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    L += [
        K.livello('ab-o', contenuto='Abbaglio · museo delle illusioni · Genova', tag='h1', x=6, y=12, w=88, size=13, peso='500',
                  colore=mix(C['chiaro'], 86), align='center', letterSpacing=3, textTransform='uppercase', ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 4, 'y': 9.5, 'width': 92}}),
        K.livello('ab-ok', contenuto='Si legge solo da qui.', x=10, y=60, w=80, size=26, peso='500', colore=C['acc'],
                  align='center', entra='fadeIn', ritardo=0, scrollMove=lampo,
                  responsive={'mobile': {'fontSize': m(17), 'x': 6, 'y': 47, 'width': 88}}),
        K.livello('ab-f', contenuto='Al museo ogni stanza ha il suo punto giusto.', x=10, y=62, w=80, size=30, peso='500',
                  colore=C['chiaro'], align='center', lineHeight=1.2, entra='fadeIn', ritardo=0, scrollMove=poi(74, 84),
                  responsive={'mobile': {'fontSize': m(18), 'x': 6, 'y': 49, 'width': 88}}),
        K.livello('ab-b1', tipo='button', contenuto='La visita', x=38, y=74, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=18, borderRadius=4, letterSpacing=2, textTransform='uppercase', buttonUrl=VISITA,
                  entra='fadeIn', ritardo=0, scrollMove=poi(82, 90), hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 8, 'y': 57, 'fontSize': m(12)}}),
        K.livello('ab-b2', tipo='button', contenuto='Le sale', x=52.5, y=74, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=4, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  letterSpacing=2, textTransform='uppercase', buttonUrl=SALE, entra='fadeIn', ritardo=0, scrollMove=poi(82, 90),
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250}, responsive={'mobile': {'x': 56, 'y': 57, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('ab-1', K.sfondo_video(V_SPIRALE, overlay=0.35, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── La seconda scena: il moiré ──
def moire(t):
    m = K.mob
    piano = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': -7, 'x': -60}, {'p': 50, 'r': 0, 'x': 0}, {'p': 100, 'r': 7, 'x': 60}]}
    poi = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 3}, {'p': da, 'o': 0, 'y': 3},
                                                             {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    L = [
        K.livello('mo-a', tipo='image', imageSrc=K.img(DISEGNI[0]), imageAlt='', objectFit='cover', x=-20, y=-30, w=140, h=160,
                  entra='fadeIn', ritardo=0, durata=400, responsive={'mobile': {'x': -60, 'y': -10, 'width': 220, 'height': 120}}),
        K.livello('mo-b', tipo='image', imageSrc=K.img(DISEGNI[1]), imageAlt='', objectFit='cover', x=-20, y=-30, w=140, h=160,
                  entra='fadeIn', ritardo=0, durata=400, customCSS='transform-origin:50% 50%;', scrollMove=piano,
                  responsive={'mobile': {'x': -60, 'y': -10, 'width': 220, 'height': 120}}),
        K.livello('mo-box', tipo='shape', x=28, y=34, w=44, h=32, bgColor=C['nero'], borderRadius=4, entra='fadeIn', ritardo=0,
                  scrollMove=poi(18, 30), responsive={'mobile': {'x': 6, 'y': 38, 'width': 88, 'height': 22}}),
        K.livello('mo-t', contenuto='Due griglie ferme.', tag='h2', x=30, y=40, w=40, size=34, peso='700', colore=C['chiaro'],
                  align='center', fontFamily=FONT_T, lineHeight=1.1, entra='fadeIn', ritardo=0, scrollMove=poi(22, 34),
                  responsive={'mobile': {'fontSize': m(20), 'x': 8, 'y': 41, 'width': 84}}),
        K.livello('mo-s', contenuto='Le onde che vedi passare non ci sono.', x=30, y=52, w=40, size=19, peso='400', colore=C['acc'],
                  align='center', lineHeight=1.4, entra='fadeIn', ritardo=0, scrollMove=poi(30, 42),
                  responsive={'mobile': {'fontSize': m(14), 'x': 8, 'y': 50, 'width': 84}}),
    ]
    fondo = {'type': 'color', 'image': '', 'video': '', 'color': '#F4F1EA', 'overlay': '', 'overlayOpacity': 0, 'kenBurns': False}
    sl = [K.diapositiva('mo-1', fondo, L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1100})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([a_fuoco(K.sechead(t, 'Abbaglio · Genova, Porto Antico', ['Non credere', 'ai tuoi occhi.'], size=60,
                                        layout='stack', size_mobile=32))], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Nove sale, trentasei illusioni e un’ora e mezza in cui il cervello sbaglia sempre. Qui si '
                                'tocca tutto e si fotografa tutto: è il solo museo dove ti chiediamo di farlo.', size=17),
                      K.button(t, 'La visita', VISITA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le sale', ['Nove stanze', 'che mentono.'], size=56)]),
        t.one([P.elenco_hover(K, t, [
            ('La stanza di Ames', 'sala 1', 'Due persone agli angoli: una sembra un gigante, l’altra un bambino. Poi si scambiano.',
             POLTRONE),
            ('Gli specchi infiniti', 'sala 3', 'Mille luci rosa e una sola lampadina.', STANZA_ROSA),
            ('Il tunnel', 'sala 5', 'Il pavimento è fermo. Le gambe non ci credono.', CORRIDOIO_BLU),
            ('La scala', 'sala 7', 'Scende per sempre e finisce al piano di sopra.', SCALA_LEGNO),
            ('L’anamorfosi', 'sala 9', 'Un muro di macchie: dal punto segnato a terra diventa una stanza.', DISEGNO),
        ], SALE, size=44, nome_corpo=True)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([moire(t)], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(K.video_frase(t, V_SPECCHI, 'Gli specchi', 'Quante persone <em>ci sono?</em>',
                             'Contale, poi chiedi a chi è con te. Non vi tornerà il numero.', overlay=36))
    out.append(t.section([
        t.one([a_fuoco(K.sechead(t, 'A fuoco', ['Nitide', 'solo a metà strada.'], size=52))]),
        t.row([t.col([a_fuoco(K.image(t, foto, alt, ratio='3/4'))], wm='1-3', ws='1-1')
               for foto, alt in ((VORTICE, 'Un vortice di righe bianche e nere'), (ESAGONI, 'Esagoni di colore uno dentro l’altro'),
                                 (SFERE, 'Sfere a righe sopra un’onda di righe'))], layout='33-33-33', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([K.numeri(t, [('9', 'sale'), ('36', 'illusioni'), ('90′', 'di visita'), ('0', 'cose da non toccare')],
                                          colonne=4, size=58)])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(X.recensioni(K, t, [
        ('Nella stanza di Ames mia figlia è diventata più alta di me. Ha preteso di restarci.', 'Francesca', 'Genova'),
        ('Ho fotografato il tunnel venti volte per capire il trucco. Non l’ho capito.', 'Andrea', 'Savona'),
        ('Venuti con la classe: i ragazzi hanno smesso di guardare il telefono. Per un’ora e mezza.', 'prof. Luisa', 'Chiavari')],
        'Chi è passato', ['Tre visite,', 'tre teste girate.']))
    out.append(K.pannello(t, SPIRALE_UOMO, 'Il biglietto', 'Entra, <em>e non fidarti.</em>',
                          'Tutti i giorni dalle 10 alle 19, il sabato fino alle 22. I bambini sotto i sei anni entrano gratis.',
                          'La visita', VISITA))
    return out


def build_sale():
    t = Tpl(ID, 'le-sale')
    out = [K.intro(t, 'Le sale', ['Nove stanze,', 'trentasei bugie.'],
                   'Un percorso di un’ora e mezza: si entra dalla stanza di Ames e si esce dalla scala che non finisce.',
                   bg=K.video_bg(V_TUNNEL, overlay=40), size=72)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Ames', 'La stanza di Ames', 'sala 1', 'Il pavimento sale, il soffitto scende, le finestre mentono: chi sta a destra '
         'sembra un gigante.', POLTRONE),
        ('Specchi', 'Gli specchi infiniti', 'sala 3', 'Due specchi uno davanti all’altro e mille lampadine che sono una.', STANZA_ROSA),
        ('Tunnel', 'Il tunnel che gira', 'sala 5', 'Il pavimento è dritto, il tunnel gira: le gambe credono agli occhi.', CORRIDOIO_BLU),
        ('Scala', 'La scala', 'sala 7', 'La scala di Penrose, costruita in legno: si scende sempre, si torna su.', SCALA_LEGNO),
        ('Op art', 'La sala op art', 'sala 8', 'Righe ferme che si muovono, cerchi che pulsano.', ONDE_BN)], altezza=420)])],
        padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([a_fuoco(K.sechead(t, 'Dentro', ['Le sale,', 'una per una.'], size=52))]),
        P.fila_foto(K, t, [(SPECCHI_PARETE, 'Una parete di specchi antichi'), (STELLE, 'Una stanza buia piena di luci'),
                           (CORNICI, 'Cornici una dentro l’altra'), (SPECCHIO_TONDO, 'Uno specchio tondo dentro un altro')],
                    ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_NEON, 'Sala 6', 'La stanza <em>di luce.</em>',
                          'Linee di luce che corrono verso un punto che non c’è: dopo trenta secondi il pavimento ondeggia.',
                          'Prenota la visita', VISITA + '#scrivi', video=True))
    return out


def build_visita():
    t = Tpl(ID, 'la-visita')
    out = [K.intro(t, 'La visita', ['Un’ora e mezza', 'di dubbi.'],
                   'Scegli il biglietto e le aggiunte; per i gruppi oltre le quindici persone scrivici.',
                   bg=K.video_bg(V_MOIRE, overlay=44), size=72)]
    conf = X.preventivo(K, t, [
        ('biglietto', 'Il biglietto', 'uno', True, 'Sotto i sei anni si entra gratis.'),
        ('aggiunte', 'Le aggiunte', 'piu', False, '')], [
        ('intero', 'biglietto', 'Intero', 'Dai 14 anni.', 14, '', True),
        ('ridotto', 'biglietto', 'Ridotto', 'Dai 6 ai 13 anni, over 65, studenti.', 10, '', False),
        ('famiglia', 'biglietto', 'Famiglia', 'Due adulti e due ragazzi.', 38, 'il più scelto', False),
        ('guida', 'aggiunte', 'Visita con la guida', 'Quaranta minuti, i trucchi spiegati.', 4, '', False),
        ('foto', 'aggiunte', 'La foto nella stanza di Ames', 'Stampata all’uscita.', 5, '', False)],
        'La tua visita', VISITA + '?visita={configurazione}#scrivi', 'IVA compresa · si entra a ogni mezz’ora',
        testo_pulsante='Prenota la visita')
    out.append(X.sezione_preventivo(K, t, 'Il biglietto', ['Componi,', 'il totale si fa da sé.'], '', conf))
    out.append(t.section([t.one([X.orari(K, t, [('Lunedì — venerdì', '10 — 19', 'Ultimo ingresso alle 17:30'),
                                                ('Sabato', '10 — 22', 'Ultimo ingresso alle 20:30'),
                                                ('Domenica e festivi', '10 — 19', 'Meglio prenotare')])])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(K.domande(t, 'Prima di entrare,', 'le domande di tutti.', [
        ('Si possono fare foto?', 'Sì, anzi: ogni sala ha il punto segnato a terra da cui la foto viene meglio.'),
        ('È adatto ai bambini?', 'Dai quattro anni in su si divertono; sotto i sei anni entrano gratis.'),
        ('C’è l’ascensore?', 'Sì, tutto il percorso è accessibile; la scala della sala 7 ha anche la rampa.'),
    ], 'ab-faq'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('date', 'Il giorno', 'giorno', True), K.campo('text', 'Quante persone', 'persone', True),
             K.campo('text', 'La visita', 'visita', larghezza='1-1'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Ti teniamo', 'il posto.'],
                                'Ti rispondiamo in giornata con l’orario d’ingresso.', VOCI, campi, 'Abbaglio — prenotazione',
                                'Prenota la visita', 'Grazie! Ti rispondiamo in giornata.'))
    out.append(X.zone(K, t, 'Il museo', ['Al Porto Antico,', 'davanti all’acquario.'], 'Cinque minuti a piedi dalla metro San Giorgio.',
                      [('Metro', 'San Giorgio, 5 minuti'), ('Treno', 'Genova Piazza Principe, 15 minuti'),
                       ('Parcheggio', 'Porto Antico, convenzionato'), ('Telefono', PHONE)], LAT, LNG, 'Abbaglio · il museo', True,
                      HEX['primary']))
    return out


def build_scuole():
    t = Tpl(ID, 'scuole')
    out = [K.intro(t, 'Scuole', ['Perché il cervello', 'sbaglia.'],
                   'Laboratori di un’ora per le classi, dalla quarta elementare alla quinta superiore: si capisce un’illusione '
                   'costruendola.', bg=K.photo_bg(MUSEO_BN, overlay=50), size=72)]
    out.append(t.section([
        t.one([a_fuoco(K.sechead(t, 'I laboratori', ['Si guarda,', 'poi si costruisce.'], size=52))]),
        t.one([P.passi(K, t, [('1', 'Gli occhi', 'Cosa vede l’occhio e cosa decide il cervello: dieci minuti di prove.'),
                              ('2', 'La stanza', 'Si entra nella stanza di Ames e si misura chi è più alto.'),
                              ('3', 'Il trucco', 'Ogni gruppo costruisce un’illusione di cartone da portare a scuola.')],
                       colonne=3)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Le quote', ['Per classe,', 'tutto compreso.'], size=52, layout='stack', size_mobile=32),
                      K.text(t, 'Gli insegnanti entrano gratis. Le schede del laboratorio arrivano per email una settimana prima.')],
                     wm='1-3', ws='1-1'),
               t.col([P.listino(K, t, [
                   ('Visita libera', 'Un’ora e mezza, per alunno', '7 €', '', ''),
                   ('Visita e laboratorio', 'Due ore e mezza, per alunno', '11 €', '', 'la più scelta'),
                   ('Laboratorio a scuola', 'Veniamo noi, per classe', '180 €', '', '')], immagini=False)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Scuola', 'scuola', True), K.campo('text', 'Classe', 'classe', True),
             K.campo('email', 'Email', 'email', True), K.campo('date', 'Il giorno che preferite', 'giorno'),
             K.campo('textarea', 'Note (alunni, esigenze)', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scuole', ['Prenota', 'la classe.'], 'Ti confermiamo data e laboratorio entro due giorni.',
                                VOCI, campi, 'Abbaglio — scuole', 'Prenota', 'Grazie! Vi confermiamo entro due giorni.'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · illusione', color=C['acc'], align='center'),
                       a_fuoco(K.headline(t, 'Questa pagina <em>non c’è mai stata.</em>', size=70, tag='h1', align='center',
                                          pad_bottom=20), massimo=10),
                       K.text(t, 'O forse c’era, e l’hai vista solo da un punto. Torniamo all’ingresso.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ONDE, overlay=55), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Si legge solo da qui',
    ['cultura', 'museo', 'illusioni ottiche', 'genova', 'scuole', 'scuro', 'slider pro', 'scorrimento'],
    'Un museo delle illusioni al Porto Antico di Genova: nell’apertura le lettere del nome sono sparse sopra una spirale e '
    'solo in un punto dello scorrimento si incastrano; più giù due griglie ferme fanno nascere onde che non ci sono. Titoli '
    'e foto vanno a fuoco solo a metà schermo; poi le sale, il biglietto da comporre, gli orari e i laboratori per le scuole.',
    [('Le sale', SALE), ('La visita', VISITA), ('Scuole', SCUOLE)],
    [('home', 'Home', None), ('le-sale', 'Le sale', 'le-sale'), ('la-visita', 'La visita', 'la-visita'), ('scuole', 'Scuole', 'scuole')],
    HEX, '\'Lexend Deca\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Lexend Zetta\', system-ui, sans-serif',
    'ui-monospace, Menlo, monospace', ['Lexend Deca', 'Lexend Zetta'], corsivo_di=[],
    peso_titoli='700', raggio=4, grana=3, titoli_line='1.08', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'ipnosi'
THEME['cursor']['ring_color'] = '#FFFFFF'
THEME['cursor']['dot_color'] = '#0B0B0D'
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'senza'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-sale.json': build_sale(), 'la-visita.json': build_visita(), 'scuole.json': build_scuole(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
