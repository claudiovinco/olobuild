# -*- coding: utf-8 -*-
"""Genera il tema «Nove Lune» (id nove-lune): un’ostetrica libera professionista a Torino, con due colleghe: le visite in
gravidanza, i corsi di accompagnamento alla nascita, l’allattamento e le visite a casa dopo il parto.

Tema chiaro, caldo: cipria, rosa prugna, blu notte e l’oro della luna. Titoli in Noto Serif Display, testo in Nunito Sans, dati in
Martian Mono.

Apertura: «la luna cresce». Lo slider resta fermo in cima mentre si scorre: sul video delle mani sulla pancia, a destra,
una luna grande e sottile, appena una falce. Scorrendo la luna cresce davvero: il terminatore (un disco che ruota in 3D
sull’asse verticale, prima scuro poi chiaro) scivola sulla faccia fino al primo quarto e poi alla luna piena, mentre
sotto si contano i mesi, da uno a nove; a luna piena si accende l’alone. Le stelle in alto pulsano piano. Cursore: la
luna.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'nove-lune'
NAME = 'Nove Lune'

HEX = {
    'primary': '#A8465A', 'primary_contrast': '#FFFFFF', 'secondary': '#F2C6B4', 'secondary_contrast': '#1E1B2E',
    'accent': '#E9B949', 'dark': '#141A33', 'light': '#FFFFFF', 'muted': '#F7ECE6', 'muted_contrast': '#1E1B2E',
    'text': '#1E1B2E', 'text_muted': '#5E596E', 'background': '#FFF9F5', 'border': '#EAD9CF', 'link': '#A8465A',
}

HOME, ACCOMP, CORSI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:accompagnamento}}', '{{PAGINA:corsi}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'ciao@novelune.example', '+39 011 000 6630', 'tel:+390110006630'
LAT, LNG = '45.0628', '7.6782'

V_PANCIA, V_PANCIA2, V_PANCIA3, V_MANINE, V_DORME, V_LUNA, V_LUNA2, V_MAMMA, V_PRATO = (
    'pexels-video-9215610', 'pexels-video-5853841', 'pexels-video-5853201', 'pexels-video-7918430', 'pexels-video-7918433',
    'pexels-video-854739', 'pexels-video-7615707', 'pexels-video-6849012', 'pexels-video-6181568')
PHOTOS = [
    'pexels-1556672.jpg', 'pexels-5853821.jpg', 'pexels-5425050.jpg', 'pexels-9214887.jpg', 'pexels-5424634.jpg',
    'pexels-16181658.jpg', 'pexels-10600032.jpg', 'pexels-28193071.jpg', 'pexels-11295339.jpg', 'pexels-37298557.jpg',
    'pexels-12713447.jpg', 'pexels-7282837.jpg', 'pexels-7282843.jpg', 'pexels-3270224.jpg', 'pexels-34566653.jpg',
    'pexels-7155527.jpg', 'pexels-7524702.jpg', 'pexels-7155354.jpg', 'pexels-7155394.jpg', 'pexels-21073558.jpg',
    'pexels-6392855.jpg', 'pexels-32830536.jpg', 'pexels-7990097.jpg', 'pexels-7990111.jpg', 'pexels-7990112.jpg',
    'pexels-7990146.jpg', 'pexels-40068650.jpg', 'pexels-20376386.jpg', 'pexels-34605845.jpg', 'pexels-4964488.jpg',
    'pexels-4964280.jpg', 'pexels-34784660.jpg', 'pexels-15915916.jpg', 'pexels-37888904.jpg', 'pexels-5424711.jpg',
    'pexels-9267509.jpg', 'pexels-19357674.jpg', 'pexels-19357670.jpg', 'pexels-19357676.jpg', 'pexels-19357668.jpg',
]
VIDEOS = [V_PANCIA, V_PANCIA2, V_PANCIA3, V_MANINE, V_DORME, V_LUNA, V_LUNA2, V_MAMMA, V_PRATO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=22, peso_titoli='400', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
CIPRIA, ORO = C['sec'], C['acc2']
MONO = 'Martian Mono'
VOCI = [('map-pin', 'Corso Re Umberto 54 · Torino', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Nove Lune', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Nove Lune', 'Ostetriche a Torino, dal 2016. Le visite in gravidanza, i corsi, l’allattamento e le '
                   'visite a casa nei primi giorni: la stessa voce dall’inizio alla fine.',
                   [('L’accompagnamento', ACCOMP), ('I corsi', CORSI), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Gli orari', 'Lunedì — venerdì 9 — 19<br>Per le urgenze dopo il parto, sempre', VOCI,
                   '© 2026 Nove Lune · Torino · Ordine della professione di ostetrica', [('instagram', 'https://instagram.com')],
                   marchio_size=140, fondo=C['sala'])


# ── L'apertura: la luna cresce ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
CX, CY, D = 73.0, 47.0, 520.0          # la luna sul computer (centro in %, diametro in px)
CXM, CYM, DM = 50.0, 61.0, 470.0       # e sul telefono


def _disco(lid, parte, **kw):
    """Un disco della luna (parte='tutto') o la sua metà destra (parte='destra'), computer e telefono."""
    w, h, wm, hm = D / W * 100, D / H * 100, DM / WM * 100, DM / HM * 100
    x, y, xm, ym = CX - w / 2, CY - h / 2, CXM - wm / 2, CYM - hm / 2
    if parte == 'destra':
        x, xm, w, wm = CX, CXM, w / 2, wm / 2
        kw.update(borderRadiusLinked=False, borderRadiusTL=0, borderRadiusBL=0, borderRadiusTR=999, borderRadiusBR=999)
    else:
        kw['borderRadius'] = 999
    resp = {'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2), 'height': round(hm, 2)}}
    return K.livello(lid, tipo='shape', x=round(x, 2), y=round(y, 2), w=round(w, 2), h=round(h, 2), responsive=resp, **kw)


def apertura(t):
    m = K.mob
    faccia = {'angle': 120, 'from': '#FBF3DF', 'to': '#E9D7AE', 'fromStop': 20}
    buio = mix(C['nero'], 80, '#3A4064')
    fine_luce = 50
    livelli = [
        _disco('nl-alone', 'tutto', bgColor=mix(ORO, 40), filterBlur=60, entra='fadeIn', ritardo=0, durata=400,
               scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.9}, {'p': 88, 'o': 0, 's': 0.9},
                                                              {'p': 100, 'o': 80, 's': 1.12}]}),
        _disco('nl-buio', 'tutto', bgColor=buio, entra='fadeIn', ritardo=200, durata=1200,
               boxShadow={'x': 0, 'y': 0, 'blur': 0, 'spread': 1, 'color': mix(C['chiaro'], 14)}),
        _disco('nl-meta', 'destra', shapeGradient=faccia, entra='fadeIn', ritardo=300, durata=1200),
        _disco('nl-term-buio', 'tutto', bgColor=buio, entra='fadeIn', ritardo=300, durata=1200,
               scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': 18}, {'p': 6, 'ry': 18},
                                                              {'p': fine_luce, 'ry': 90, 'o': 100},
                                                              {'p': fine_luce + 0.5, 'ry': 90, 'o': 0},
                                                              {'p': 100, 'ry': 90, 'o': 0}]}),
        _disco('nl-term-luce', 'tutto', shapeGradient=faccia, entra='fadeIn', ritardo=0, durata=300,
               scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': 90, 'o': 0}, {'p': fine_luce, 'ry': 90, 'o': 0},
                                                              {'p': fine_luce + 0.5, 'ry': 90, 'o': 100},
                                                              {'p': 92, 'ry': 0.01, 'o': 100}, {'p': 100, 'ry': 0.01}]}),
    ]
    # i mesi, sotto la luna: uno alla volta
    for k in range(9):
        a = 4 + k * 10
        tappe = [{'p': 0, 'o': 0}, {'p': max(0, a - 3), 'o': 0}, {'p': a, 'o': 100}]
        if k < 8:
            tappe += [{'p': a + 7, 'o': 100}, {'p': a + 10, 'o': 0}, {'p': 100, 'o': 0}]
        else:
            tappe += [{'p': 100, 'o': 100}]
        testo = '%d° mese' % (k + 1) if k < 8 else '9° mese · eccoci'
        livelli.append(K.livello('nl-mese%d' % k, contenuto=testo, x=CX - 15, y=CY + D / H * 50 + 4, w=30, size=15,
                                 peso='700', colore=ORO, align='center', letterSpacing=3, textTransform='uppercase',
                                 fontFamily=MONO, entra='fadeIn', ritardo=0, durata=300,
                                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe},
                                 responsive={'mobile': {'fontSize': m(12), 'x': 20, 'y': CYM + DM / HM * 50 + 2, 'width': 60}}))
    # le stelle
    for k, (sx, sy, sxm, sym) in enumerate([(56, 12, 12, 40), (62, 22, 84, 43), (88, 14, 30, 44.5), (93, 30, 90, 52),
                                            (58, 80, 8, 78), (91, 76, 70, 86), (66, 6, 55, 41.5), (96, 56, 16, 66)]):
        d = 3 + k % 3
        livelli.append(K.livello('nl-s%d' % k, tipo='shape', x=sx, y=sy, w=round(d / W * 100, 3), h=round(d / H * 100, 3),
                                 bgColor=C['chiaro'], borderRadius=999, entra='fadeIn', ritardo=800 + k * 120, durata=800,
                                 animLoop='blinkLoop', animLoopDuration=2600 + k * 530,
                                 responsive={'mobile': {'x': sxm, 'y': sym, 'width': round(d * 1.4 / WM * 100, 3),
                                                        'height': round(d * 1.4 / HM * 100, 3)}}))
    testi = [
        K.livello('nl-o', contenuto='Nove Lune · ostetriche · Torino', x=6, y=24, w=40, size=12, peso='500',
                  colore=mix(C['chiaro'], 88), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('nl-t1', contenuto='Nove lune,', tag='h1', x=6, y=30, w=50, size=100, peso='400', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=400, durata=1100,
                  responsive={'mobile': {'fontSize': m(46), 'y': 11, 'x': 7, 'width': 86}}),
        K.livello('nl-t2', fontFamily='Noto Serif Display', contenuto='una alla volta.', tag='p', x=6, y=43, w=50, size=100, peso='400', colore=CIPRIA,
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=600, durata=1100,
                  responsive={'mobile': {'fontSize': m(46), 'y': 15.5, 'x': 7, 'width': 86}}),
        K.livello('nl-s', contenuto='Dalla prima visita al primo bagnetto, la stessa ostetrica: i controlli, il corso, '
                                    'l’allattamento e le visite a casa.', x=6, y=58, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 88), lineHeight=1.6, ritardo=850,
                  responsive={'mobile': {'fontSize': m(13), 'y': 21, 'x': 7, 'width': 86}}),
        K.livello('nl-b', tipo='button', contenuto='I corsi preparto', x=6, y=72, w='auto', size=14, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, entra='fadeInUp', ritardo=1050,
                  buttonUrl=CORSI, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 28, 'x': 7, 'fontSize': m(12)}}),
        K.livello('nl-l', tipo='button', contenuto='Scrivici', x=20.5, y=72, w='auto', size=14, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 70), entra='fadeInUp', ritardo=1150, buttonUrl=CONTATTI,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'y': 28, 'x': 52, 'fontSize': m(12)}}),
        K.livello('nl-hint', contenuto='Scorrete: la luna cresce ↓', x=6, y=86, w=30, size=12, peso='500',
                  colore=mix(C['chiaro'], 82), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 20, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 33.5, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('nl-velo', 'sinistra', 36, 66, colore=C['nero'], forza=86, mobile_alto=34)
    sl = [K.diapositiva('nl-1', K.sfondo_video(V_PANCIA, overlay=0.24, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Nove Lune · Torino, dal 2016', ['Una voce sola,', 'per tutto il viaggio.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo tre ostetriche. Vi seguiamo dalla prima visita in gravidanza fino ai primi mesi con il '
                                'bambino, e chi vi segue resta la stessa: conosce la vostra storia, risponde al telefono, '
                                'viene a casa quando serve.', size=17, color=C['testo']),
                      K.button(t, 'L’accompagnamento', ACCOMP, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'I tre tempi', ['Tre trimestri,', 'tre domande diverse.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('I', 'Dal primo al terzo mese', 'pexels-5853821.jpg', 'L’inizio', 'Le prime', 'visite',
             'Gli esami, la nausea, le domande che non si osa fare. Una visita lunga per cominciare.', '60′', 'la prima visita'),
            ('II', 'Dal quarto al sesto', 'pexels-7155527.jpg', 'Il corpo cambia', 'Movimento', 'e respiro',
             'La pancia cresce: la postura, il sonno, lo yoga in gravidanza il martedì.', '8', 'incontri di yoga'),
            ('III', 'Dal settimo al nono', 'pexels-5424711.jpg', 'Prepararsi', 'Il corso', 'e la borsa',
             'Il travaglio, il parto, i primi giorni: il corso in coppia e la visita a casa prima della nascita.', '10',
             'incontri del corso')], icona='calendar')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Passateci sopra:', 'dietro c’è il come.'], size=56)]),
        P.carte(K, t, [
            ('pexels-7990111.jpg', 'Le visite', 'In studio o a casa', 'Controlli, misure, il battito: un’ora, con le '
             'domande che volete.'),
            ('pexels-4964488.jpg', 'Il corso', 'In coppia, dieci incontri', 'Il travaglio, il parto, i primi giorni. Il '
             'giovedì sera.'),
            ('pexels-6392855.jpg', 'L’allattamento', 'Dal primo giorno', 'Le posizioni, il seno, il latte che non arriva: '
             'anche al telefono.'),
            ('pexels-10600032.jpg', 'A casa, dopo', 'Le prime settimane', 'Due visite a casa: il bambino, il peso, il '
             'sonno, e come state voi.'),
        ]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-7282843.jpg', 'Una manina stretta attorno a un dito', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Il giorno dopo la nascita Elena era qui alle nove, con le brioche. Sapeva già '
                                          'tutto di noi: non abbiamo dovuto spiegare niente.', 'Giulia e Paolo, genitori di Bianca')],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MANINE, 'I primi giorni', 'Le mani <em>imparano da sole.</em>',
                             'Il bagnetto, la fascia, il ruttino: ve lo mostriamo una volta, poi siete voi.', overlay=30))
    out.append(K.pannello(t, V_MAMMA, 'Il corso preparto', 'Dieci giovedì, <em>in coppia.</em>',
                          'Il prossimo comincia il 15 gennaio: otto coppie, una sala con i cuscini.', 'I corsi', CORSI,
                          overlay=25, video=True))
    return out


def build_accompagnamento():
    t = Tpl(ID, 'accompagnamento')
    out = [K.intro(t, 'L’accompagnamento', ['Dal primo test', 'al primo dentino.'],
                   'Potete scegliere un servizio solo, o il percorso intero. In ogni caso, la vostra ostetrica ha un nome e '
                   'un numero di telefono.', bg=K.photo_bg('pexels-1556672.jpg'), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('In gravidanza · da 70 €', 'Le visite', 'a casa o in studio',
         '<p>Un’ora per visita: le misure, il battito, gli esami da fare, e il tempo per le domande.</p><ul><li>Gravidanze '
         'fisiologiche, insieme al vostro ginecologo</li><li>Una visita a casa nell’ultimo mese</li></ul>',
         'pexels-7990097.jpg'),
        ('Dal settimo mese · 220 € a coppia', 'Il corso', 'dieci incontri',
         '<p>Il travaglio e il parto, il respiro, le posizioni, i primi giorni. Con un incontro sull’allattamento e uno con '
         'la pediatra.</p><ul><li>Il giovedì alle 19</li><li>Otto coppie al massimo</li></ul>', 'pexels-5424711.jpg'),
        ('Dopo il parto · 150 €', 'A casa', 'le prime settimane',
         '<p>Due visite a casa nei primi dieci giorni e il telefono acceso per un mese. Il peso, l’allattamento, il '
         'sonno.</p><ul><li>Anche il sabato e la domenica</li><li>A Torino e nella prima cintura</li></ul>',
         'pexels-37298557.jpg'),
    ])], anim=None)], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Tutto scritto,', 'prima.'], size=56,
                         tagline='Rilasciamo fattura: le prestazioni dell’ostetrica si detraggono al 19%.')]),
        t.row([t.col([P.listino(K, t, [
            ('Prima visita', 'Un’ora, in studio', '70 €', 'pexels-7990112.jpg', ''),
            ('Visita a casa', 'Un’ora, Torino e cintura', '90 €', 'pexels-4964280.jpg', ''),
            ('Consulenza allattamento', 'Un’ora, anche a casa', '75 €', 'pexels-21073558.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Corso preparto', 'Dieci incontri, a coppia', '220 €', 'pexels-15915916.jpg', 'il più scelto'),
            ('Yoga in gravidanza', 'Otto incontri', '110 €', 'pexels-7524702.jpg', ''),
            ('Il percorso intero', 'Dalle visite al dopo parto', '590 €', 'pexels-12713447.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_DORME, 'Il sonno', 'Dormirà. <em>Anche voi.</em>',
                             'Le prime settimane sono così: vi aiutiamo a trovare un ritmo, il vostro.', overlay=30))
    return out


CORSI_L = [
    ('15 gen', 'Corso preparto · inverno', 'Dieci giovedì alle 19, in coppia', '6 posti'),
    ('20 gen', 'Yoga in gravidanza', 'Otto martedì alle 18', '4 posti'),
    ('7 feb', 'Allattamento, prima di cominciare', 'Un sabato mattina', '10 posti'),
    ('12 mar', 'Corso preparto · primavera', 'Dieci giovedì alle 19, in coppia', '8 posti'),
    ('4 apr', 'Il massaggio del neonato', 'Quattro sabati, con il bambino', '6 posti'),
]


def build_corsi():
    t = Tpl(ID, 'corsi')
    out = [K.intro(t, 'I corsi', ['In piccoli gruppi,', 'con i cuscini per terra.'],
                   'La sala è al piano terra, con il tappeto e i cuscini. Si viene comodi, con una bottiglia d’acqua e '
                   'tutte le domande.', bg=K.video_bg(V_PANCIA2), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il calendario', ['I prossimi', 'corsi.'], size=56,
                         tagline='Per iscrivervi scriveteci: il posto si tiene per una settimana.')]),
        t.one([P.righe(K, t, CORSI_L, CONTATTI + '#scrivi', titolo=34)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Com’è', ['La sala,', 'il giovedì sera.'], size=56)]),
        P.fila_foto(K, t, [('pexels-7155354.jpg', 'Lo yoga in gravidanza, sul tappetino'),
                           ('pexels-9267509.jpg', 'Una coppia che aspetta, abbracciata'),
                           ('pexels-7990146.jpg', 'Una visita in studio'),
                           ('pexels-7155394.jpg', 'Un esercizio di respiro')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_PANCIA3, 'Il respiro', 'Respirare <em>fino in fondo.</em>',
                             'Il respiro è la prima cosa che si impara al corso: serve in travaglio, e anche dopo.', overlay=30))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Scriveteci', 'anche solo per chiedere.'],
                   'Rispondiamo entro un giorno. Dopo il parto, per le nostre mamme, il telefono è acceso sempre.',
                   bg=K.photo_bg('pexels-16181658.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le ostetriche', ['Chi vi seguirà,', 'per nome.'], size=56)]),
        K.squadra(t, [
            ('pexels-19357674.jpg', 'Elena Ferrero', 'Ostetrica, fondatrice', 'Visite in gravidanza e a casa. Ha seguito '
             'più di mille nascite.', 'center 25%'),
            ('pexels-19357670.jpg', 'Marta Gallo', 'Ostetrica', 'Il corso preparto e lo yoga in gravidanza.', 'center 25%'),
            ('pexels-19357676.jpg', 'Sofia Rinaldi', 'Ostetrica, consulente per l’allattamento', 'L’allattamento e il '
             'massaggio del neonato.', 'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'), K.campo('date', 'Data presunta del parto', 'data'),
             K.campo('select', 'Vi interessa', 'interesse', opzioni='Le visite\nIl corso preparto\nL’allattamento\nLe visite '
                     'a casa\nIl percorso intero'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['La data presunta', 'e cosa vi serve.'],
                                'Vi rispondiamo entro un giorno con una proposta.', VOCI, campi, 'Nove Lune — contatto',
                                'Invia', 'Grazie! Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In corso Re Umberto,', 'al piano terra.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — venerdì 9 — 19'), ('Il passeggino', 'entra: niente gradini'),
                                  ('In tram', 'linea 15, fermata Re Umberto')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Nove Lune', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · luna nuova', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui non c’è <em>ancora niente.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Come la luna nuova: tornerà. Intanto, la home.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LUNA, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Salute e cura', 'La luna che cresce',
    ['ostetrica', 'gravidanza', 'corso preparto', 'allattamento', 'torino', 'chiaro', 'slider pro', '3d'],
    'Le ostetriche di un piccolo studio, cipria e blu notte: in apertura una luna che cresce davvero mentre si scorre, dalla '
    'falce alla luna piena, e sotto si contano i mesi da uno a nove; le visite, il corso preparto, l’allattamento e le '
    'visite a casa.',
    [('L’accompagnamento', ACCOMP), ('I corsi', CORSI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('accompagnamento', 'L’accompagnamento', 'accompagnamento'), ('corsi', 'I corsi', 'corsi'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Nunito Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Noto Serif Display\', Georgia, serif',
    '\'Martian Mono\', ui-monospace, Menlo, monospace', ['Nunito Sans', 'Noto Serif Display', 'Martian Mono'], corsivo_di=[],
    peso_titoli='400', raggio=22, grana=0, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'luna'
THEME['cursor']['ring_color'] = HEX['dark']
THEME['cursor']['dot_color'] = '#F4EAD2'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'accompagnamento.json': build_accompagnamento(), 'corsi.json': build_corsi(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
