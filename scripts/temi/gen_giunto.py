# -*- coding: utf-8 -*-
"""Genera il tema «Giunto» (id giunto): robotica collaborativa per le piccole fabbriche a Reggio Emilia: robot che lavorano
accanto alle persone, per pallettizzare, servire le macchine, saldare e avvitare; il sopralluogo, la simulazione,
l’installazione e la formazione.

Tema chiaro, l’officina pulita: bianco, grafite e il giallo sicurezza. Titoli in Rubik, testo in Lexend, dati in Overpass
Mono.

Apertura: «il braccio». Lo slider resta fermo in cima mentre si scorre: sul video di una cella di saldatura, a destra, un
braccio robotico a due segmenti sulla sua base. Scorrendo il braccio si allunga verso il nastro, afferra il pezzo, lo
porta in alto e lo posa sul bancale, poi torna dritto: ogni segmento gira attorno al suo giunto (gli angoli calcolati con
la cinematica inversa), le articolazioni seguono. Cursore: passo passo.
"""
import math
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'giunto'
NAME = 'Giunto'

HEX = {
    'primary': '#2B2F36', 'primary_contrast': '#FFFFFF', 'secondary': '#F5C518', 'secondary_contrast': '#1B1E22',
    'accent': '#F5C518', 'dark': '#16181C', 'light': '#FFFFFF', 'muted': '#EEF0F2', 'muted_contrast': '#1B1E22',
    'text': '#1B1E22', 'text_muted': '#5A6069', 'background': '#F7F8F9', 'border': '#D7DADE', 'link': '#2B2F36',
}

HOME, SOLUZIONI, METODO, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:soluzioni}}', '{{PAGINA:come-funziona}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'progetti@giunto.example', '+39 0522 000 471', 'tel:+390522000471'
LAT, LNG = '44.6980', '10.6310'

V_SALDA, V_GIALLO, V_GIALLO2, V_LINEA, V_LINEA2, V_COBOT, V_SALDA2, V_BRACCIO = (
    'pexels-video-10641848', 'pexels-video-32386523', 'pexels-video-32386519', 'pexels-video-32386590',
    'pexels-video-32386532', 'pexels-video-8327784', 'pexels-video-11800598', 'pexels-video-32386569')
PHOTOS = [
    'pexels-35280311.jpg', 'pexels-34222005.jpg', 'pexels-8294612.jpg', 'pexels-6153351.jpg', 'pexels-30335372.jpg',
    'pexels-30335395.jpg', 'pexels-32305004.jpg', 'pexels-30335445.jpg', 'pexels-29988963.jpg', 'pexels-12499181.jpg',
    'pexels-7869034.jpg', 'pexels-7869048.jpg', 'pexels-6153356.jpg', 'pexels-34194565.jpg', 'pexels-8386369.jpg',
    'pexels-8386426.jpg', 'pexels-7869033.jpg', 'pexels-34194580.jpg', 'pexels-32845663.jpg', 'pexels-33427061.jpg',
    'pexels-31336017.jpg', 'pexels-32845701.jpg', 'pexels-633850.jpg', 'pexels-48799.jpg', 'pexels-8865189.jpg',
    'pexels-51320.jpg', 'pexels-7937658.jpg', 'pexels-6474447.jpg', 'pexels-7565176.jpg', 'pexels-34718930.jpg',
    'pexels-36423819.jpg', 'pexels-34718922.jpg', 'pexels-4170172.jpg',
]
VIDEOS = [V_SALDA, V_GIALLO, V_GIALLO2, V_LINEA, V_LINEA2, V_COBOT, V_SALDA2, V_BRACCIO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=8, peso_titoli='600', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
GIALLO = C['sec']
MONO = 'Overpass Mono'
VOCI = [('map-pin', 'Via dell’Industria 22 · Reggio Emilia', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Giunto', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Giunto', 'Robotica collaborativa per le piccole e medie fabbriche, a Reggio Emilia dal 2016. Robot che '
                   'lavorano accanto alle persone, senza gabbie.',
                   [('Le soluzioni', SOLUZIONI), ('Come funziona', METODO), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Lo showroom', 'Lunedì — venerdì 8:30 — 18<br>Le prove su appuntamento', VOCI,
                   '© 2026 Giunto · Reggio Emilia · P.IVA 00000000000', [('linkedin', 'https://linkedin.com'),
                                                                           ('youtube', 'https://youtube.com')],
                   marchio_size=170, fondo=C['sala'])


# ── L'apertura: il braccio ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
POSE = [  # (p, punta del braccio, il pezzo è in presa)
    (0, 'riposo', False), (16, 'presa', False), (22, 'presa', True), (44, 'alto', True), (62, 'posa', True),
    (68, 'posa', False), (86, 'riposo', False), (100, 'riposo', False)]


def _ik(B, T, L1, L2):
    """Gli angoli assoluti (gradi, y verso il basso) dei due segmenti per portare la punta in T, gomito in alto."""
    dx, dy = T[0] - B[0], T[1] - B[1]
    d = min(L1 + L2 - 1, math.hypot(dx, dy))
    c2 = max(-1.0, min(1.0, (d * d - L1 * L1 - L2 * L2) / (2 * L1 * L2)))
    t2 = math.acos(c2)                                    # gomito in alto (y verso il basso)
    phi = math.atan2(dy, dx)
    a1 = phi - math.atan2(L2 * math.sin(t2), L1 + L2 * math.cos(t2))
    return math.degrees(a1), math.degrees(a1 + t2)


def _braccio(g, mob):
    """Tutti i livelli del braccio per un formato. g: geometria in px della sua tela. Il braccio del telefono sta fuori
    tela sugli altri formati e i suoi spostamenti sono moltiplicati per 1440/640 (il JS li riduce con la larghezza)."""
    Wc, Hc = (WM, HM) if mob else (W, H)
    k = W / WM if mob else 1.0
    suf = 'm' if mob else ''
    B, L1, L2, t1, t2 = g['B'], g['L1'], g['L2'], g['t1'], g['t2']
    punte = {n: g[n] for n in ('riposo', 'presa', 'alto', 'posa')}
    ang = {n: _ik(B, T, L1, L2) for n, T in punte.items()}
    gomito = lambda a1: (B[0] + L1 * math.cos(math.radians(a1)), B[1] + L1 * math.sin(math.radians(a1)))
    E0 = gomito(ang['riposo'][0])
    T0 = punte['riposo']
    pct = lambda x, y, w, h: dict(x=round(x / Wc * 100, 2), y=round(y / Hc * 100, 2), w=round(w / Wc * 100, 2),
                                  h=round(h / Hc * 100, 2))

    def dove(x, y, w, h):
        d = pct(x, y, w, h)
        if mob:
            return dict(x=-60, y=d['y'], w=d['w'], h=d['h'], responsive={'mobile': {'x': d['x'], 'y': d['y'], 'width': d['w'],
                                                                                    'height': d['h']}})
        return dict(d, responsive={'mobile': {'visible': False}})

    def mosse(fn):
        return {'inizio': 0, 'fine': 100, 'tappe': [dict({'p': p}, **fn(n, presa)) for p, n, presa in POSE]}

    out = []
    # il nastro a destra e il bancale a sinistra
    nx, ny = punte['presa'][0] - g['nastro'] / 2, punte['presa'][1] + g['pezzo']
    out.append(K.livello('gi-nastro' + suf, tipo='shape', bgColor='#2E3238', borderRadius=8, entra='fadeIn', ritardo=200,
                         durata=800, customCSS='background:repeating-linear-gradient(90deg,#2E3238 0 22px,#3A3F46 22px 30px);',
                         **dove(nx, ny, g['nastro'], g['pezzo'] * 0.32)))
    bx, by = punte['posa'][0] - g['pezzo'] * 0.9, punte['posa'][1] + g['pezzo']
    out.append(K.livello('gi-bancale' + suf, tipo='shape', bgColor='#B88A4E', borderRadius=4, entra='fadeIn', ritardo=200,
                         durata=800, customCSS='background:repeating-linear-gradient(90deg,#B88A4E 0 26px,#8F6A39 26px 32px);',
                         **dove(bx, by, g['pezzo'] * 1.8, g['pezzo'] * 0.28)))
    # il pezzo: sul nastro, poi segue la punta mentre è in presa, poi resta sul bancale
    P0 = punte['presa']
    stato = {'pos': (0.0, 0.0)}

    def pezzo_mossa(n, presa):
        if presa:
            stato['pos'] = (punte[n][0] - P0[0], punte[n][1] - P0[1])
        return {'x': round(stato['pos'][0] * k, 1), 'y': round(stato['pos'][1] * k, 1)}
    out.append(K.livello('gi-pezzo' + suf, contenuto='1204', tipo='text', size=int(g['pezzo'] * 0.2 / (k if mob else 1) *
                                                                                            (k if mob else 1)),
                         peso='700', colore=C['nero'], bgColor=GIALLO, borderRadius=6, align='center', textValign='center',
                         fontFamily=MONO, entra='fadeIn', ritardo=300, durata=800,
                         boxShadow={'x': 0, 'y': 10, 'blur': 18, 'spread': -6, 'color': mix(C['nero'], 50)},
                         scrollMove=mosse(pezzo_mossa), **dove(P0[0] - g['pezzo'] / 2, P0[1], g['pezzo'], g['pezzo'])))
    if mob:
        out[-1]['responsive']['mobile']['fontSize'] = 22
    # la base
    out.append(K.livello('gi-base' + suf, tipo='shape', bgColor='#2B2F36', borderRadiusLinked=False, borderRadiusTL=18,
                         borderRadiusTR=18, borderRadiusBR=4, borderRadiusBL=4, entra='fadeIn', ritardo=200, durata=800,
                         **dove(B[0] - g['base'] / 2, B[1], g['base'], g['base'] * 0.55)))
    # primo segmento: col cardine nella base (bordo sinistro), gira e basta
    out.append(K.livello('gi-s1' + suf, tipo='shape', bgColor=GIALLO, borderRadius=999, entra='fadeIn', ritardo=300, durata=800,
                         customCSS='transform-origin:%.2f%% 50%%;' % (t1 / 2 / (L1 + t1) * 100),
                         boxShadow={'x': 0, 'y': 8, 'blur': 18, 'spread': -6, 'color': mix(C['nero'], 45)},
                         scrollMove=mosse(lambda n, presa: {'r': round(ang[n][0], 2)}),
                         **dove(B[0] - t1 / 2, B[1] - t1 / 2, L1 + t1, t1)))
    # secondo segmento: posato sul gomito della posa di riposo; si sposta col gomito e gira sul suo cardine
    def s2(n, presa):
        E = gomito(ang[n][0])
        return {'x': round((E[0] - E0[0]) * k, 1), 'y': round((E[1] - E0[1]) * k, 1), 'r': round(ang[n][1], 2)}
    out.append(K.livello('gi-s2' + suf, tipo='shape', bgColor=mix(GIALLO, 88, '#FFFFFF'), borderRadius=999, entra='fadeIn',
                         ritardo=350, durata=800, customCSS='transform-origin:%.2f%% 50%%;' % (t2 / 2 / (L2 + t2) * 100),
                         boxShadow={'x': 0, 'y': 8, 'blur': 18, 'spread': -6, 'color': mix(C['nero'], 45)},
                         scrollMove=mosse(s2), **dove(E0[0] - t2 / 2, E0[1] - t2 / 2, L2 + t2, t2)))
    # le articolazioni: base, gomito, polso (e la pinza al polso)
    j = g['giunto']
    out.append(K.livello('gi-j0' + suf, tipo='shape', bgColor='#2B2F36', borderRadius=999, borderWidth=4, borderColor=GIALLO,
                         entra='fadeIn', ritardo=400, durata=800, **dove(B[0] - j / 2, B[1] - j / 2, j, j)))
    out.append(K.livello('gi-j1' + suf, tipo='shape', bgColor='#2B2F36', borderRadius=999, borderWidth=4, borderColor=GIALLO,
                         entra='fadeIn', ritardo=400, durata=800,
                         scrollMove=mosse(lambda n, presa: {'x': round((gomito(ang[n][0])[0] - E0[0]) * k, 1),
                                                            'y': round((gomito(ang[n][0])[1] - E0[1]) * k, 1)}),
                         **dove(E0[0] - j / 2, E0[1] - j / 2, j, j)))
    pj = j * 0.8
    out.append(K.livello('gi-j2' + suf, tipo='shape', bgColor='#2B2F36', borderRadius=6, entra='fadeIn', ritardo=400,
                         durata=800,
                         scrollMove=mosse(lambda n, presa: {'x': round((punte[n][0] - T0[0]) * k, 1),
                                                            'y': round((punte[n][1] - T0[1]) * k, 1)}),
                         **dove(T0[0] - pj / 2, T0[1] - pj / 2, pj, pj)))
    return out


def apertura(t):
    m = K.mob
    D = dict(B=(1120.0, 640.0), L1=230.0, L2=200.0, t1=46.0, t2=38.0, giunto=50.0, base=130.0, pezzo=70.0, nastro=230.0,
             riposo=(1150.0, 250.0), presa=(1330.0, 560.0), alto=(1060.0, 300.0), posa=(880.0, 560.0))
    M = dict(B=(330.0, 1150.0), L1=170.0, L2=150.0, t1=36.0, t2=30.0, giunto=40.0, base=104.0, pezzo=56.0, nastro=150.0,
             riposo=(350.0, 860.0), presa=(520.0, 1090.0), alto=(290.0, 900.0), posa=(140.0, 1090.0))
    livelli = _braccio(D, False) + _braccio(M, True)
    testi = [
        K.livello('gi-o', contenuto='Giunto · robotica collaborativa · Reggio Emilia', x=6, y=22, w=44, size=12, peso='500',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('gi-t1', contenuto='Un collega', tag='h1', x=6, y=28, w=46, size=96, peso='600', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(40), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('gi-t2', fontFamily='Rubik', contenuto='che non si stanca.', tag='p', x=6, y=40, w=48, size=72, peso='600',
                  colore=GIALLO, lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(32), 'y': 15, 'x': 7, 'width': 90}}),
        K.livello('gi-s', contenuto='Robot che lavorano accanto alle persone, senza gabbie: pallettizzano, servono le '
                                    'macchine, saldano. Per le fabbriche da dieci a cento persone.', x=6, y=51, w=36, size=17,
                  peso='400', colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 19, 'x': 7, 'width': 86}}),
        K.livello('gi-b', tipo='button', contenuto='Le soluzioni', x=6, y=67, w='auto', size=13, peso='700', colore=C['nero'],
                  bgColor=GIALLO, padding=17, borderRadius=8, letterSpacing=1.5, textTransform='uppercase', entra='fadeInUp',
                  ritardo=1000, buttonUrl=SOLUZIONI, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'y': 26, 'x': 7, 'fontSize': m(11)}}),
        K.livello('gi-l', tipo='button', contenuto='Un sopralluogo', x=20, y=67, w='auto', size=13, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=8, borderWidth=1,
                  borderColor=mix(C['chiaro'], 70), letterSpacing=1.5, textTransform='uppercase', entra='fadeInUp',
                  ritardo=1100, buttonUrl=CONTATTI, hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'],
                                                           'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'y': 26, 'x': 46, 'fontSize': m(11)}}),
        K.livello('gi-hint', contenuto='Scorrete: il braccio si mette al lavoro ↓', x=6, y=82, w=34, size=12, peso='500',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 15, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 31.5, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('gi-velo', 'sinistra', 38, 62, colore=C['nero'], forza=86, mobile_alto=34)
    sl = [K.diapositiva('gi-1', K.sfondo_video(V_SALDA, overlay=0.24, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Giunto · Reggio Emilia, dal 2016', ['Il robot giusto', 'per la vostra fabbrica.'],
                                size=56, layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Non vendiamo robot: risolviamo un lavoro. Veniamo in reparto, guardiamo la postazione, la '
                                'simuliamo al computer e solo dopo scegliamo il robot. Poi lo installiamo, formiamo chi lo '
                                'userà e restiamo al telefono.', size=17, color=C['testo']),
                      K.button(t, 'Come funziona', METODO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([P.schede_icone(K, t, [('01', 'installazioni', '140', '', 'In fabbriche da dieci a cento persone.', 'factory'),
                                     ('02', 'mesi', '14', '', 'Il ritorno medio dell’investimento.', 'trending-up'),
                                     ('03', 'giorni', '5', '', 'Dall’arrivo del robot alla produzione.', 'calendar'),
                                     ('04', 'gabbie', '0', '', 'I robot collaborativi lavorano accanto alle persone.',
                                      'shield-check')], fondo=C['sala'])], style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le soluzioni', ['Quattro lavori', 'che un robot fa bene.'], size=56,
                         tagline='Passate su una soluzione: si affaccia la cella.')]),
        t.one([P.elenco_hover(K, t, [
            ('Pallettizzazione', 'Fine linea · fino a 15 kg', 'Le scatole sul bancale, strato dopo strato, anche di notte.',
             'pexels-34718930.jpg'),
            ('Asservimento macchine', 'Torni e centri di lavoro', 'Carica e scarica i pezzi mentre l’operatore controlla '
             'la qualità.', 'pexels-30335395.jpg'),
            ('Saldatura', 'Piccole serie', 'Il saldatore gli insegna il cordone a mano, il robot lo ripete identico.',
             'pexels-35280311.jpg'),
            ('Avvitatura e montaggio', 'Banchi di lavoro', 'Le viti tutte uguali, alla coppia giusta, accanto alle persone.',
             'pexels-6153356.jpg'),
        ], SOLUZIONI, size=48)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_COBOT, 'Accanto', 'Si ferma <em>se lo toccate.</em>',
                             'I robot collaborativi sentono l’urto e si fermano: niente gabbie, niente recinti.', overlay=24))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-7869033.jpg', 'Un tecnico che programma il robot dal tablet', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Avevamo paura di toglier lavoro a qualcuno. Invece Mauro, che caricava il tornio, '
                                          'adesso programma il robot e controlla tre macchine.', 'Meccanica Bertani, '
                                          'Scandiano', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_LINEA, 'Il sopralluogo', 'Veniamo in reparto, <em>gratis.</em>',
                          'Un’ora con voi alla postazione: vi diciamo se un robot ha senso, e quale.', 'Prenota', CONTATTI,
                          overlay=20, video=True))
    return out


def build_soluzioni():
    t = Tpl(ID, 'soluzioni')
    out = [K.intro(t, 'Le soluzioni', ['Un lavoro,', 'una cella.'],
                   'Ogni cella la progettiamo per la vostra postazione: le misure, i pezzi, il ritmo. Qui le più chieste, '
                   'coi prezzi indicativi chiavi in mano.', bg=K.video_bg(V_GIALLO), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Fine linea · da 48.000 €', 'Pallettizzare', 'senza schiena',
         '<p>Il robot prende le scatole dal nastro e le dispone sul bancale secondo lo schema scelto.</p><ul><li>Fino a 15 '
         'kg a presa</li><li>Due bancali, si cambia senza fermarsi</li></ul>', 'pexels-36423819.jpg'),
        ('Reparto macchine · da 42.000 €', 'Servire il tornio', 'anche di notte',
         '<p>Carica il grezzo, scarica il finito, soffia i trucioli: il tornio non si ferma mai.</p><ul><li>Cassetti per otto '
         'ore di autonomia</li></ul>', 'pexels-30335372.jpg'),
        ('Saldatura · da 55.000 €', 'Il cordone', 'sempre uguale',
         '<p>Si insegna a mano, guidando la torcia: il robot memorizza e ripete.</p><ul><li>Per le piccole serie</li><li>'
         'Il tavolo di posizionamento a due stazioni</li></ul>', 'pexels-8294612.jpg'),
    ])], anim=None)], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le pinze', ['Una presa', 'per ogni pezzo.'], size=56)]),
        P.fila_foto(K, t, [('pexels-34194565.jpg', 'La pinza del robot'), ('pexels-8386369.jpg', 'Una mano robotica'),
                           ('pexels-8386426.jpg', 'La pinza che prende un oggetto'), ('pexels-6153351.jpg', 'Il braccio '
                                                                                                         'robotico')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_metodo():
    t = Tpl(ID, 'come-funziona')
    out = [K.intro(t, 'Come funziona', ['Prima il lavoro,', 'poi il robot.'],
                   'Dal sopralluogo alla produzione passano in media dieci settimane. Il robot arriva solo quando la cella '
                   'ha già funzionato al computer.', bg=K.photo_bg('pexels-32305004.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le tappe', ['Dieci settimane,', 'tre tappe.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'settimana', 'pexels-12499181.jpg', 'Il sopralluogo', 'In reparto', 'con voi',
             'Guardiamo la postazione, misuriamo, contiamo i pezzi all’ora: gratis.', '1', 'ora'),
            ('2', 'settimane', 'pexels-34194580.jpg', 'La simulazione', 'La cella', 'al computer',
             'Il gemello digitale della cella: vedete il robot lavorare prima di comprarlo.', '3', 'revisioni'),
            ('3', 'settimane', 'pexels-7869048.jpg', 'L’installazione', 'In produzione', 'in cinque giorni',
             'Montaggio, collaudo, e due giorni di formazione per chi lo userà.', '5', 'giorni')], icona='wrench')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_LINEA2, 'Dopo', 'Il robot è vostro, <em>noi restiamo.</em>',
                             'Assistenza da remoto in giornata, il tecnico in reparto entro due giorni.', overlay=24))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Un’ora in reparto,', 'senza impegno.'],
                   'Scriveteci cosa fa oggi la persona alla postazione: vi diciamo se un robot ha senso prima ancora di '
                   'venire.', bg=K.photo_bg('pexels-29988963.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi viene', 'in reparto.'], size=56)]),
        K.squadra(t, [
            ('pexels-7937658.jpg', 'Elena Ferretti', 'Ingegnera, fondatrice', 'Le celle di saldatura e di asservimento.',
             'center 25%'),
            ('pexels-6474447.jpg', 'Matteo Bonacini', 'Programmatore robot', 'Le simulazioni e i collaudi.', 'center 25%'),
            ('pexels-7565176.jpg', 'Luca Iori', 'Tecnico di assistenza', 'In reparto entro due giorni, ovunque in Emilia.',
             'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome e azienda', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Il lavoro da automatizzare', 'lavoro', opzioni='Pallettizzazione\nAsservimento macchine\n'
                     'Saldatura\nAvvitatura e montaggio\nAltro'),
             K.campo('text', 'Quanti pezzi al giorno', 'pezzi'), K.campo('textarea', 'La postazione, in due righe', 'messaggio',
                                                                        larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'sopralluogo', 'Il sopralluogo', ['Due righe sulla postazione,', 'veniamo noi.'],
                                'Vi richiamiamo entro due giorni per fissare la visita.', VOCI, campi, 'Giunto — sopralluogo',
                                'Prenota il sopralluogo', 'Ricevuto. Vi richiamiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lo showroom', ['Tre celle', 'da provare.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — venerdì 8:30 — 18'), ('Dall’autostrada', 'uscita Reggio Emilia, '
                                                                                                '6 minuti'),
                                  ('Le prove', 'portate i vostri pezzi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Giunto · showroom', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori corsa', color=K.acc_media, align='center'),
                       K.headline(t, 'Il braccio <em>non ci arriva.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Questa pagina è fuori dal raggio di lavoro. Torniamo alla posizione di riposo.', size=17,
                              align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SALDA2, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Tecnologia', 'Il braccio robotico',
    ['robotica', 'cobot', 'automazione', 'industria 4.0', 'reggio emilia', 'chiaro', 'slider pro', 'scorrimento'],
    'Robotica collaborativa per le piccole fabbriche, grafite e giallo sicurezza: in apertura un braccio robotico sul video '
    'di una cella di saldatura, che scorrendo prende il pezzo dal nastro e lo posa sul bancale, ogni segmento sul suo '
    'giunto; le soluzioni coi prezzi, il metodo in tre tappe, le persone e il sopralluogo.',
    [('Le soluzioni', SOLUZIONI), ('Come funziona', METODO), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('soluzioni', 'Le soluzioni', 'soluzioni'), ('come-funziona', 'Come funziona', 'come-funziona'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Lexend\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Rubik\', system-ui, sans-serif',
    '\'Overpass Mono\', ui-monospace, Menlo, monospace', ['Lexend', 'Rubik', 'Overpass Mono'], corsivo_di=[],
    peso_titoli='600', raggio=8, grana=0, titoli_line='1.0', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'passopasso'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['secondary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'soluzioni.json': build_soluzioni(), 'come-funziona.json': build_metodo(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
