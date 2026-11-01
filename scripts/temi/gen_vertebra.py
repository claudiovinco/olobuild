# -*- coding: utf-8 -*-
"""Genera il tema «Vertebra» (id vertebra): uno studio di fisioterapia e osteopatia a Padova: le visite, i trattamenti
per schiena, collo, spalle e ginocchia, la riabilitazione dopo gli interventi e la ginnastica in piccoli gruppi.

Tema chiaro, il verde salvia dello studio: bianco, verde profondo, corallo e un giallo caldo. Titoli in Sora, testo in
Inter Tight, dati in Red Hat Mono.

Apertura: «la schiena si raddrizza». Lo slider resta fermo in cima mentre si scorre: sul video di una seduta, a destra,
sette vertebre in colonna, storte come una schiena con la scoliosi (spostate di lato e inclinate a S). Scorrendo tornano
in fila una alla volta, dal basso verso l’alto; quando sono allineate compaiono i dischi fra l’una e l’altra e la linea
della colonna. Le vertebre seguono il puntatore inclinandosi in 3D. Cursore: l’elastico.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'vertebra'
NAME = 'Vertebra'

HEX = {
    'primary': '#2F6B5E', 'primary_contrast': '#FFFFFF', 'secondary': '#F08A6E', 'secondary_contrast': '#14231F',
    'accent': '#F4B860', 'dark': '#13221E', 'light': '#FFFFFF', 'muted': '#E8EFEB', 'muted_contrast': '#16211E',
    'text': '#16211E', 'text_muted': '#52605B', 'background': '#F7F9F7', 'border': '#D2DCD6', 'link': '#2F6B5E',
}

HOME, TRATT, STUDIO, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:trattamenti}}', '{{PAGINA:studio}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'studio@vertebra.example', '+39 049 000 4417', 'tel:+390490004417'
LAT, LNG = '45.4064', '11.8768'

V_SEDUTA, V_PIEDI, V_BRACCIO, V_SDRAIO, V_MANI, V_ALLUNGO = ('pexels-video-5793304', 'pexels-video-5793440',
                                                           'pexels-video-5793444', 'pexels-video-6111095',
                                                           'pexels-video-5894175', 'pexels-video-8520044')
PHOTOS = [
    'pexels-5793895.jpg', 'pexels-5793788.jpg', 'pexels-5793910.jpg', 'pexels-5793653.jpg', 'pexels-5793904.jpg',
    'pexels-5793651.jpg', 'pexels-5793684.jpg', 'pexels-4506072.jpg', 'pexels-31234755.jpg', 'pexels-4506160.jpg',
    'pexels-4506109.jpg', 'pexels-4506106.jpg', 'pexels-6111610.jpg', 'pexels-6111586.jpg', 'pexels-6111588.jpg',
    'pexels-8846512.jpg', 'pexels-4376713.jpg', 'pexels-4506166.jpg', 'pexels-6933320.jpg', 'pexels-6453579.jpg',
    'pexels-4587682.jpg', 'pexels-6454028.jpg', 'pexels-4506112.jpg', 'pexels-5793683.jpg', 'pexels-7722859.jpg',
    'pexels-5793918.jpg', 'pexels-6076134.jpg', 'pexels-8923161.jpg', 'pexels-4270364.jpg', 'pexels-4270363.jpg',
    'pexels-5037324.jpg', 'pexels-5036892.jpg', 'pexels-5262738.jpg', 'pexels-5037285.jpg',
]
VIDEOS = [V_SEDUTA, V_PIEDI, V_BRACCIO, V_SDRAIO, V_MANI, V_ALLUNGO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
CORALLO, GIALLO = C['sec'], C['acc2']
MONO = 'Red Hat Mono'
VOCI = [('map-pin', 'Via Belzoni 41 · Padova', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Vertebra', logo_size='22', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Vertebra', 'Studio di fisioterapia e osteopatia a Padova, dal 2011. Quattro fisioterapisti, una '
                   'palestra piccola, le visite in quaranta minuti veri.',
                   [('I trattamenti', TRATT), ('Lo studio', STUDIO), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'Gli orari', 'Lunedì — venerdì 8 — 20<br>Sabato 8 — 13', VOCI,
                   '© 2026 Vertebra · Padova · P.IVA 00000000000', [('instagram', 'https://instagram.com')],
                   marchio_size=150, fondo=C['sala'])


# ── L'apertura: la schiena si raddrizza ──
KM = 640 / 390.0                       # il telefono: la tela è larga 640 e si scala sui 390 px veri
VERT = 7
DX = [40, 110, 150, 115, 30, -60, -105]  # lo spostamento di ogni vertebra, a S (px della tela 1440)
RZ = [-6, -11, -5, 6, 12, 8, 2]          # e l’inclinazione


def _colonna(desk, mob):
    """Le vertebre, i dischi e la linea. desk/mob: (centro x %, primo y %, ultimo y %, larghezza corpo %, altezza corpo %)
    della colonna su computer e telefono. Le ali (i processi trasversi) ruotano attorno al centro del corpo: il loro
    transform-origin sta lì, in percentuale della loro scatola, uguale sui due formati perché le proporzioni lo sono."""
    out, dischi = [], []
    ali_w, sovra = 0.6, 0.06                     # ala = 60% del corpo, sovrapposta al corpo per il 6%
    o_sx = (0.5 + ali_w - sovra) / ali_w * 100   # il centro del corpo visto dall’ala sinistra
    o_dx = (-0.5 + sovra) / ali_w * 100
    for i in range(VERT):
        ini = 6 + (VERT - 1 - i) * 7             # dal basso verso l’alto
        mosse = lambda extra=None: {'inizio': 0, 'fine': 100, 'tappe': [
            {'p': 0, 'x': DX[i], 'r': RZ[i]}, {'p': ini, 'x': DX[i], 'r': RZ[i]}, {'p': ini + 26, 'x': 0, 'r': 0},
            {'p': 100, 'x': 0, 'r': 0}]}

        def geo(g, parte):
            cx, y0, y1, bw, bh = g
            cy = y0 + (y1 - y0) * i / (VERT - 1)
            bw_i = bw * (0.86 + 0.14 * i / (VERT - 1))   # più larghe verso il basso, come le lombari
            if parte == 'corpo':
                return cx - bw_i / 2, cy - bh / 2, bw_i, bh
            aw = bw_i * ali_w
            x = cx - bw_i / 2 - aw + bw_i * sovra if parte == 'sx' else cx + bw_i / 2 - bw_i * sovra
            return x, cy - bh * 0.22, aw, bh * 0.44

        tilt = {'type': 'mouse', 'depth': 3 + i % 3, 'tilt3d': True}
        for parte, origine in (('sx', '%.1f%% 50%%' % o_sx), ('dx', '%.1f%% 50%%' % o_dx), ('corpo', '50% 50%')):
            x, y, w, h = geo(desk, parte)
            xm, ym, wm, hm = geo(mob, parte)
            corpo = parte == 'corpo'
            out.append(K.livello(
                've-v%d%s' % (i, parte), tipo='shape', x=round(x, 2), y=round(y, 2), w=round(w, 2), h=round(h, 2),
                shapeGradient={'angle': 180, 'from': mix(C['chiaro'], 98) if corpo else mix(C['chiaro'], 90),
                               'to': mix(C['sala'], 96) if corpo else mix(C['sala'], 86), 'fromStop': 30},
                borderRadius=999 if not corpo else 22, entra='fadeIn', ritardo=200 + i * 90, durata=700,
                boxShadow={'x': 0, 'y': 10, 'blur': 26, 'spread': -8, 'color': mix(C['nero'], 55)} if corpo else None,
                parallax=tilt, scrollMove=mosse(), customCSS='transform-origin:%s;' % origine,
                responsive={'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2), 'height': round(hm, 2)}}))
        if i < VERT - 1:
            def disco(g):
                cx, y0, y1, bw, bh = g
                cy = y0 + (y1 - y0) * (i + 0.5) / (VERT - 1)
                return cx - bw * 0.36, cy - bh * 0.17, bw * 0.72, bh * 0.34
            x, y, w, h = disco(desk)
            xm, ym, wm, hm = disco(mob)
            dischi.append(K.livello('ve-d%d' % i, tipo='shape', x=round(x, 2), y=round(y, 2), w=round(w, 2), h=round(h, 2),
                                    bgColor=GIALLO, borderRadius=999, entra='fadeIn', ritardo=0, durata=300,
                                    scrollMove={'inizio': 78, 'fine': 94, 'tappe': [{'p': 0, 's': 0.3, 'o': 0},
                                                                                    {'p': 100, 's': 1, 'o': 100}]},
                                    responsive={'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                           'height': round(hm, 2)}}))
    for L in out:
        if L.get('boxShadow') is None:
            L.pop('boxShadow', None)
    cx, y0, y1, bw, bh = desk
    cxm, y0m, y1m, bwm, bhm = mob
    linea = K.livello('ve-linea', tipo='shape', x=cx - 0.12, y=y0 - 4, w=0.24, h=y1 - y0 + 8, bgColor=GIALLO, entra='fadeIn',
                      ritardo=0, durata=300, scrollMove={'inizio': 74, 'fine': 92, 'tappe': [{'p': 0, 'o': 0}, {'p': 100, 'o': 80}]},
                      responsive={'mobile': {'x': cxm - 0.3, 'y': y0m - 2, 'width': 0.6, 'height': y1m - y0m + 4}})
    return [linea] + dischi + out


def apertura(t):
    m = K.mob
    ferma = lambda i, f: {'inizio': i, 'fine': f, 'tappe': [{'p': 0, 'o': 0, 'y': 30}, {'p': 100, 'o': 100, 'y': 0}]}
    testi = [
        K.livello('ve-o', contenuto='Vertebra · fisioterapia e osteopatia · Padova', x=6, y=21, w=46, size=12, peso='500',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 9, 'x': 7, 'width': 86}}),
        K.livello('ve-t1', contenuto='La schiena', tag='h1', x=6, y=27, w=52, size=96, peso='600', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(46), 'y': 12, 'x': 7, 'width': 86}}),
        K.livello('ve-t2', fontFamily='Sora', contenuto='torna in fila.', tag='p', x=6, y=39, w=52, size=96, peso='600', colore=GIALLO,
                  lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(46), 'y': 16.5, 'x': 7, 'width': 86}}),
        K.livello('ve-s', contenuto='Visite di quaranta minuti, trattamenti a mano e in palestra, il programma da fare a casa. '
                                    'Per la schiena, il collo, le spalle e le ginocchia.', x=6, y=53, w=38, size=17,
                  peso='400', colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 22.5, 'x': 7, 'width': 86}}),
        K.livello('ve-b', tipo='button', contenuto='Prenota una visita', x=6, y=68, w='auto', size=14, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=14, entra='fadeInUp', ritardo=1000,
                  buttonUrl=PRENOTA, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 30, 'x': 7, 'fontSize': m(12)}}),
        K.livello('ve-l', tipo='button', contenuto='I trattamenti', x=20, y=68, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=14, borderWidth=1,
                  borderColor=mix(C['chiaro'], 70), entra='fadeInUp', ritardo=1100, buttonUrl=TRATT,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'y': 30, 'x': 50, 'fontSize': m(12)}}),
        K.livello('ve-hint', contenuto='Scorrete: la schiena si raddrizza ↓', x=6, y=86, w=34, size=12, peso='500',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 30, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 35, 'x': 7, 'width': 86}}),
        K.livello('ve-fatto', contenuto='Sette vertebre, una sopra l’altra.', x=6, y=86, w=40, size=13, peso='500',
                  colore=GIALLO, letterSpacing=1, fontFamily=MONO, entra='fadeIn', ritardo=0, scrollMove=ferma(80, 95),
                  responsive={'mobile': {'fontSize': m(10), 'y': 35, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('ve-velo', 'sinistra', 38, 70, colore=C['nero'], forza=86, mobile_alto=37)
    livelli = veli + _colonna((76, 17, 83, 7.6, 6.4), (50, 46, 90, 17, 3.2)) + testi
    sl = [K.diapositiva('ve-1', K.sfondo_video(V_SEDUTA, overlay=0.22, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=True,
                       parallaxType='mouse', parallaxIntensity=5, scrollTimeline=True, scrollTimelineDistance=1500,
                       scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


TRATTAMENTI = [  # nome, sotto, descrizione, foto
    ('La schiena', 'Lombalgia · sciatica · cervicale', 'Terapia manuale, esercizi e la postura sul lavoro: in media sei '
     'sedute, poi il programma a casa.', 'pexels-4506072.jpg'),
    ('Il ginocchio', 'Dopo il menisco o il crociato', 'La riabilitazione dal giorno dopo l’intervento al ritorno in '
     'campo, con le prove di forza misurate.', 'pexels-6111586.jpg'),
    ('La spalla', 'Cuffia dei rotatori · spalla rigida', 'Il movimento che torna piano: terapia manuale, elastici e '
     'pesi leggeri, una seduta alla settimana.', 'pexels-4506112.jpg'),
    ('Osteopatia', 'Il corpo intero', 'Un trattamento dolce, a mani nude: per il mal di testa, la mandibola, il '
     'respiro e le schiene che non stanno mai bene.', 'pexels-4506160.jpg'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lo studio · Padova, dal 2011', ['Quaranta minuti,', 'tutti per voi.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'La prima visita è lunga apposta: vi ascoltiamo, vi guardiamo camminare, piegarvi, alzare '
                                'le braccia. Poi vi diciamo cosa pensiamo, quante sedute servono e cosa potete fare da '
                                'soli, a casa, fra una seduta e l’altra.', size=17, color=C['testo']),
                      K.button(t, 'Lo studio', STUDIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('40′', 'La prima visita'), ('4', 'Fisioterapisti'), ('6', 'Sedute in media'),
                            ('1.800', 'Schiene all’anno')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I trattamenti', ['Dove fa male,', 'e perché.'], size=56,
                         tagline='Passate su un trattamento: si affaccia la sala.')]),
        t.one([P.elenco_hover(K, t, TRATTAMENTI, TRATT)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Dalla prima visita', 'all’ultima seduta.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'La visita', 'Quaranta minuti: la storia, i movimenti, le prove. Portate gli esami che '
                                                  'avete.'),
                              ('02', 'Il piano', 'Quante sedute, ogni quanto, cosa aspettarsi. Scritto, da portare a casa.'),
                              ('03', 'Le sedute', 'Mani e palestra, con lo stesso fisioterapista dall’inizio alla fine.'),
                              ('04', 'Da soli', 'Gli esercizi in un video sul telefono, e un controllo dopo un mese.')],
                       numero_font=MONO)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_MANI, 'La terapia manuale', 'Le mani <em>sentono prima.</em>',
                             'Dove il muscolo è teso, dove l’articolazione non scorre: lo si sente prima di vederlo.',
                             overlay=35))
    out.append(t.section([K.centered(t, [P.testimonianza(
        K, t, 'Sono arrivata piegata in due dopo un trasloco. Alla terza seduta mi allacciavo le scarpe da sola, alla sesta '
              'sono tornata in bici al lavoro.', 'Marta R.', 'Insegnante, 46 anni')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_SDRAIO, 'Il sabato mattina', 'La ginnastica <em>in sei.</em>',
                          'Un’ora per la schiena, in piccoli gruppi, con un fisioterapista: per non ricominciare da capo.',
                          'Prenota', PRENOTA, overlay=30, video=True))
    return out


def build_trattamenti():
    t = Tpl(ID, 'trattamenti')
    out = [K.intro(t, 'I trattamenti', ['A mano,', 'e in palestra.'],
                   'La terapia manuale scioglie, l’esercizio tiene. Usiamo le due cose insieme, quasi sempre: le macchine '
                   'poco, solo quando servono davvero.', bg=K.photo_bg('pexels-5793788.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le sedute', ['Quanto durano,', 'quanto costano.'], size=56,
                         tagline='Le sedute si pagano una alla volta. Fattura per la detrazione del 19%.')]),
        t.row([t.col([P.listino(K, t, [
            ('Prima visita', 'Quaranta minuti, il piano scritto', '70 €', 'pexels-5793895.jpg', ''),
            ('Seduta di fisioterapia', 'Cinquanta minuti, mani ed esercizi', '55 €', 'pexels-4506109.jpg', ''),
            ('Osteopatia', 'Cinquanta minuti, il corpo intero', '65 €', 'pexels-4506160.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Riabilitazione', 'Dopo un intervento, un’ora', '60 €', 'pexels-6111588.jpg', ''),
            ('Ginnastica in sei', 'Il sabato, un’ora, dieci incontri', '180 €', 'pexels-4587682.jpg', 'piccoli gruppi'),
            ('Pacchetto schiena', 'Visita e cinque sedute', '320 €', 'pexels-31234755.jpg', 'il più chiesto'),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Per cosa venite', ['Le cose che vediamo', 'più spesso.'], size=56)]),
        t.one([P.righe(K, t, [('01', 'Mal di schiena', 'Lombalgia, sciatica, ernia', '6 sedute'),
                              ('02', 'Collo e testa', 'Cervicale, mal di testa, mandibola', '5 sedute'),
                              ('03', 'Dopo un intervento', 'Ginocchio, anca, spalla', '12 sedute'),
                              ('04', 'Sportivi', 'Distorsioni, stiramenti, ritorno in campo', '4 sedute'),
                              ('05', 'Postura', 'Chi sta seduto otto ore', '4 sedute')], TRATT, titolo=34)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'In palestra', ['L’esercizio', 'giusto, misurato.'], size=56)]),
        P.fila_foto(K, t, [('pexels-8846512.jpg', 'Esercizio con l’elastico alle spalle'),
                           ('pexels-6453579.jpg', 'La palla piccola fra le mani'),
                           ('pexels-6933320.jpg', 'L’elastico alle gambe'),
                           ('pexels-6454028.jpg', 'Esercizio con la palla grande')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_BRACCIO, 'Il movimento', 'Prima piano, <em>poi tutto.</em>',
                             'Ogni esercizio parte dal movimento che fa male, preso alla larga.', overlay=35))
    return out


def build_studio():
    t = Tpl(ID, 'studio')
    out = [K.intro(t, 'Lo studio', ['Quattro fisioterapisti,', 'una palestra piccola.'],
                   'Siamo al piano terra, con la porta sul cortile: niente scale, il parcheggio davanti. Ognuno di noi segue '
                   'i suoi pazienti dall’inizio alla fine.', bg=K.video_bg(V_PIEDI), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Chi vi segue', ['Le persone', 'dello studio.'], size=56)]),
        K.squadra(t, [
            ('pexels-5793918.jpg', 'Chiara Bonetto', 'Fisioterapista, fondatrice', 'Schiena e collo. Ha aperto lo studio nel '
             '2011 dopo dieci anni in ospedale.', 'center 30%'),
            ('pexels-8923161.jpg', 'Luca Ferrin', 'Fisioterapista sportivo', 'Ginocchia e caviglie, il ritorno in campo. '
             'Segue due squadre di pallavolo.', 'center 25%'),
            ('pexels-6076134.jpg', 'Giulia Rampazzo', 'Osteopata', 'Mal di testa, mandibola, respiro. Il sabato tiene la '
             'ginnastica in sei.', 'center 30%'),
            ('pexels-4270364.jpg', 'Davide Mason', 'Fisioterapista', 'Spalle e riabilitazione dopo gli interventi.',
             'center 25%'),
        ], ratio='4/5', size='300', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le stanze', ['Tre sale', 'e la palestra.'], size=56)]),
        P.fila_foto(K, t, [('pexels-5793910.jpg', 'La sala con il lettino e la finestra'),
                           ('pexels-5793653.jpg', 'La palestra con gli attrezzi'),
                           ('pexels-5793651.jpg', 'Un esercizio vicino alla finestra')], ratio='4/3'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_ALLUNGO, 'Dopo', 'Correre di nuovo, <em>senza pensarci.</em>',
                             'L’ultima seduta la facciamo fuori, al parco: una corsa leggera insieme.', overlay=35))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Una visita,', 'anche questa settimana.'],
                   'Scriveteci il problema in due righe e quando potete venire: vi richiamiamo in giornata con l’orario.',
                   bg=K.photo_bg('pexels-5793684.jpg'), size=92)]
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email'),
             K.campo('select', 'Per cosa', 'motivo', True, opzioni='Schiena\nCollo e testa\nSpalla\nGinocchio\nDopo un '
                     'intervento\nOsteopatia\nGinnastica in sei'),
             K.campo('select', 'Quando preferite', 'quando', opzioni='Mattina\nPausa pranzo\nPomeriggio\nSabato'),
             K.campo('textarea', 'Due righe sul problema', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Il problema e l’orario,', 'al resto pensiamo noi.'],
                                'Vi richiamiamo in giornata. Se avete esami o referti, portateli alla visita.', VOCI, campi,
                                'Vertebra — prenotazione', 'Prenota', 'Ricevuto. Vi richiamiamo in giornata.'))
    out.append(K.domande(t, 'Prima di venire', ['Le domande', 'che ci fanno tutti.'], [
        ('Serve la prescrizione del medico?', 'No, per la fisioterapia privata non serve. Se l’avete, portatela.'),
        ('Come mi vesto?', 'Comodi: pantaloncini o leggings e una maglietta. Per la spalla, una canottiera.'),
        ('Si può detrarre?', 'Sì, il 19% con la fattura e il pagamento tracciabile.'),
        ('Fate visite a domicilio?', 'Per chi non può muoversi, a Padova e nei comuni vicini: chiedeteci.'),
    ], 've-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In via Belzoni,', 'piano terra.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — venerdì 8 — 20 · sabato 8 — 13'), ('In auto', 'il parcheggio davanti'),
                                  ('In autobus', 'linea 3, fermata Belzoni')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Vertebra · studio', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori asse', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è storta.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Non c’è, o l’abbiamo spostata. Torniamo dritti alla home.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SEDUTA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Salute e cura', 'La schiena che si raddrizza',
    ['fisioterapia', 'osteopatia', 'riabilitazione', 'studio medico', 'padova', 'chiaro', 'slider pro', '3d'],
    'Uno studio di fisioterapia e osteopatia, il verde salvia e il corallo: in apertura sette vertebre storte accanto al '
    'video di una seduta, che scorrendo tornano in fila una alla volta e seguono il puntatore in 3D; i trattamenti, i '
    'prezzi, le persone dello studio e la prenotazione.',
    [('I trattamenti', TRATT), ('Lo studio', STUDIO), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('trattamenti', 'I trattamenti', 'trattamenti'), ('studio', 'Lo studio', 'studio'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Inter Tight\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Sora\', system-ui, sans-serif',
    '\'Red Hat Mono\', ui-monospace, Menlo, monospace', ['Inter Tight', 'Sora', 'Red Hat Mono'], corsivo_di=[],
    peso_titoli='600', raggio=14, grana=0, titoli_line='1.02', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'elastico'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['secondary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'trattamenti.json': build_trattamenti(), 'studio.json': build_studio(),
                        'prenota.json': build_prenota(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
