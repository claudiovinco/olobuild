# -*- coding: utf-8 -*-
"""Genera il tema «Zolla» (id zolla): un’associazione di orti urbani condivisi a Bologna, in un ex scalo ferroviario:
sessanta parcelle, l’orto dei bambini, i laboratori del sabato, la festa del raccolto e il compost di quartiere.

Tema chiaro, la terra e l’orto: paglia, verde orto, rosso terra e il giallo dei girasoli. Titoli in Outfit, testo in
Figtree, dati in Spline Sans Mono.

Apertura: «la semina». Lo slider resta fermo in cima mentre si scorre: sul video delle mani nella terra, a destra, tre
solchi. Scorrendo i semi cadono dall’alto nei solchi, uno dopo l’altro, e dove toccano terra spunta un germoglio che
cresce e ondeggia piano alla base; alla fine l’orto è verde e compare il raccolto. Cursore: la pioggerella.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'zolla'
NAME = 'Zolla'

HEX = {
    'primary': '#2F6B3A', 'primary_contrast': '#FFFFFF', 'secondary': '#F3C969', 'secondary_contrast': '#1F1A14',
    'accent': '#B5532E', 'dark': '#1F1A14', 'light': '#FFFFFF', 'muted': '#F1EEDF', 'muted_contrast': '#1F1A14',
    'text': '#1F1A14', 'text_muted': '#5C574B', 'background': '#FAF8EF', 'border': '#DDD8C4', 'link': '#2F6B3A',
}

HOME, ORTI, INSIEME, PARTECIPA = '{{PAGINA:home}}', '{{PAGINA:gli-orti}}', '{{PAGINA:insieme}}', '{{PAGINA:partecipa}}'
MAIL, PHONE, PHONE_TEL = 'orto@zolla.example', '+39 051 000 6123', 'tel:+390510006123'
LAT, LNG = '44.5120', '11.3700'

V_SEMI, V_MANI, V_COPPIA, V_FILARI, V_ACQUA, V_ANNAFFIA, V_ZUCCHE, V_CESTO, V_RACCOLTO = (
    'pexels-video-6509243', 'pexels-video-13244342', 'pexels-video-7655277', 'pexels-video-9737858', 'pexels-video-20521970',
    'pexels-video-11960789', 'pexels-video-5602272', 'pexels-video-5527775', 'pexels-video-7655581')
PHOTOS = [
    'pexels-27742358.jpg', 'pexels-37553146.jpg', 'pexels-37553164.jpg', 'pexels-35319756.jpg', 'pexels-7658795.jpg',
    'pexels-15768099.jpg', 'pexels-12969371.jpg', 'pexels-7728874.jpg', 'pexels-4258044.jpg', 'pexels-7728921.jpg',
    'pexels-421999.jpg', 'pexels-6913455.jpg', 'pexels-8540212.jpg', 'pexels-5425893.jpg', 'pexels-7658807.jpg',
    'pexels-7658824.jpg', 'pexels-39516843.jpg', 'pexels-16723207.jpg', 'pexels-35913668.jpg', 'pexels-40447235.jpg',
    'pexels-6187904.jpg', 'pexels-7388513.jpg', 'pexels-37113938.jpg', 'pexels-38279065.jpg', 'pexels-39574868.jpg',
    'pexels-15429028.jpg', 'pexels-18566999.jpg', 'pexels-19214653.jpg', 'pexels-6913442.jpg', 'pexels-6508421.jpg',
    'pexels-11286045.jpg', 'pexels-5624271.jpg', 'pexels-11286030.jpg', 'pexels-11286028.jpg', 'pexels-7782200.jpg',
    'pexels-7782091.jpg', 'pexels-29219216.jpg', 'pexels-4750400.jpg', 'pexels-30119143.jpg', 'pexels-7728636.jpg',
    'pexels-5029776.jpg', 'pexels-4920247.jpg', 'pexels-9722002.jpg', 'pexels-7658828.jpg', 'pexels-29085775.jpg',
    'pexels-5029773.jpg', 'pexels-29085795.jpg', 'pexels-8784683.jpg',
]
VIDEOS = [V_SEMI, V_MANI, V_COPPIA, V_FILARI, V_ACQUA, V_ANNAFFIA, V_ZUCCHE, V_CESTO, V_RACCOLTO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=16, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
GIRASOLE, TERRA = C['sec'], C['acc2']
MONO = 'Spline Sans Mono'
VOCI = [('map-pin', 'Via dello Scalo 40 · Bologna', PARTECIPA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Zolla', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Zolla', 'Orti condivisi nell’ex scalo ferroviario di Bologna, dal 2015. Sessanta parcelle, l’orto dei '
                   'bambini, il compost del quartiere: la terra è di tutti.',
                   [('Gli orti', ORTI), ('Insieme', INSIEME), ('Partecipa', PARTECIPA), ('Privacy', '/privacy-policy/')],
                   'Il cancello', 'Aperto tutti i giorni dall’alba al tramonto<br>Il sabato mattina c’è sempre qualcuno', VOCI,
                   '© 2026 Zolla · Bologna · Associazione di promozione sociale · C.F. 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=170, fondo=C['sala'])


# ── L'apertura: la semina ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
ORTO = (54.0, 52.0, 40.0, 34.0)       # x, y, larghezza, altezza dell'orto sul computer (% della tela)
ORTOM = (8.0, 50.0, 84.0, 30.0)       # e sul telefono
FILE, PER_FILA = 3, 5


def _dove(fx, fy, fw, fh, mob=False):
    ox, oy, ow, oh = ORTOM if mob else ORTO
    return ox + fx * ow, oy + fy * oh, fw * ow, fh * oh


def apertura(t):
    m = K.mob
    livelli = []
    # i solchi
    for r in range(FILE):
        fy = 0.18 + r * 0.32
        x, y, w, h = _dove(0.0, fy, 1.0, 0.13)
        xm, ym, wm, hm = _dove(0.0, fy, 1.0, 0.13, True)
        livelli.append(K.livello('zo-solco%d' % r, tipo='shape', x=round(x, 2), y=round(y, 2), w=round(w, 2), h=round(h, 2),
                                 shapeGradient={'angle': 180, 'from': '#4A3424', 'to': '#2E2016', 'fromStop': 30},
                                 borderRadius=999, entra='fadeIn', ritardo=300 + r * 150, durata=900,
                                 boxShadow={'x': 0, 'y': 6, 'blur': 14, 'spread': -4, 'color': mix(C['nero'], 60)},
                                 responsive={'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                        'height': round(hm, 2)}}))
    # i semi che cadono e i germogli che crescono, fila per fila
    n = 0
    for r in range(FILE):
        for c in range(PER_FILA):
            fx = 0.09 + c * 0.2 + (0.04 if r == 1 else 0)
            fy = 0.18 + r * 0.32
            a = 6 + n * 4.6                          # quando cade questo seme
            # il seme
            sw, sh = 0.026, 0.05
            x, y, w, h = _dove(fx - sw / 2, fy + 0.03, sw, sh)
            xm, ym, wm, hm = _dove(fx - sw / 2, fy + 0.03, sw, sh, True)
            livelli.append(K.livello('zo-seme%d' % n, tipo='shape', x=round(x, 2), y=round(y, 2), w=round(w, 2),
                                     h=round(h, 2), bgColor=GIRASOLE, borderRadius=999, entra='fadeIn', ritardo=0, durata=300,
                                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [
                                         {'p': 0, 'y': -420, 'o': 0}, {'p': a - 0.5, 'y': -420, 'o': 0},
                                         {'p': a, 'y': -420, 'o': 100}, {'p': a + 5, 'y': 0, 'o': 100},
                                         {'p': a + 8, 'y': 0, 'o': 0}, {'p': 100, 'y': 0, 'o': 0}]},
                                     responsive={'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                            'height': round(hm, 2)}}))
            # il germoglio: cresce dal basso (scala), poi ondeggia attorno alla base; il riquadro è quadrato e alto quanto
            # il disegno, con la base dentro il solco (un riquadro più alto lo lasciava galleggiare)
            gpx, gpxm = 66.0, 104.0                       # lato in px della tela, computer e telefono
            ox_, oy_, ow_, oh_ = ORTO
            oxm, oym, owm, ohm = ORTOM
            cx_, base = ox_ + fx * ow_, oy_ + (fy + 0.085) * oh_
            cxm, basem = oxm + fx * owm, oym + (fy + 0.085) * ohm
            w, h = gpx / W * 100, gpx / H * 100
            wm, hm = gpxm / WM * 100, gpxm / HM * 100
            x, y, xm, ym = cx_ - w / 2, base - h, cxm - wm / 2, basem - hm
            icona = 'sprout' if (n + r) % 3 else 'leaf'
            L = K.livello('zo-germe%d' % n, tipo='icon', iconName=icona, colore=C['acc'] if n % 2 else '#4E9A5A',
                          iconFillColor=mix('#7BC47F', 45), iconStrokeWidth=1.8, x=round(x, 2), y=round(y, 2), w=round(w, 2),
                          h=round(h, 2), size=int(gpx), entra='fadeIn', ritardo=0, durata=300,
                          customCSS='transform-origin:50% 100%;',
                          scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 0.01}, {'p': a + 5, 's': 0.01},
                                                                         {'p': a + 13, 's': 1}, {'p': 100, 's': 1.08}]},
                          responsive={'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                 'height': round(hm, 2), 'fontSize': int(gpxm)}})
            dur = 2400 + (n * 370) % 1400
            L['timeline'] = K.tl(round(x, 2), round(y, 2), [(0, {'rotation': -5, 'originX': 50, 'originY': 100}, 'sine.inOut'),
                                                            (dur // 2, {'rotation': 5, 'originX': 50, 'originY': 100},
                                                             'sine.inOut'),
                                                            (dur, {'rotation': -5, 'originX': 50, 'originY': 100})], dur)
            livelli.append(L)
            n += 1
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 20}, {'p': 86, 'o': 0, 'y': 20},
                                                {'p': 96, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    livelli.append(K.livello('zo-raccolto', contenuto='A luglio: 1.800 kg di verdura, divisa fra sessanta famiglie.', x=54,
                             y=89, w=40, size=14, peso='600', colore=C['nero'], bgColor=GIRASOLE, padding=10, borderRadius=999,
                             align='center', fontFamily=MONO, entra='fadeIn', ritardo=0, durata=300, scrollMove=fine,
                             responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 83, 'width': 88}}))
    testi = [
        K.livello('zo-o', contenuto='Zolla · orti condivisi · Bologna', x=6, y=22, w=40, size=12, peso='600',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('zo-t1', contenuto='Un pezzo di terra,', tag='h1', x=6, y=28, w=48, size=82, peso='600', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(34), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('zo-t2', fontFamily='Outfit', contenuto='tanti vicini.', tag='p', x=6, y=39, w=48, size=82, peso='600',
                  colore=GIRASOLE, lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(34), 'y': 14.5, 'x': 7, 'width': 90}}),
        K.livello('zo-s', contenuto='Sessanta parcelle nell’ex scalo ferroviario, l’orto dei bambini e il compost del '
                                    'quartiere. Non serve saper zappare: si impara.', x=6, y=51, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 19, 'x': 7, 'width': 86}}),
        K.livello('zo-b', tipo='button', contenuto='Prendi una parcella', x=6, y=66, w='auto', size=15, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=16, entra='fadeInUp', ritardo=1000,
                  buttonUrl=PARTECIPA, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 26, 'x': 7, 'fontSize': m(12)}}),
        K.livello('zo-l', tipo='button', contenuto='Gli orti', x=22.5, y=66, w='auto', size=15, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=17, borderRadius=16, borderWidth=1, borderColor=mix(C['chiaro'], 70),
                  entra='fadeInUp', ritardo=1100, buttonUrl=ORTI,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 28}, 'mobile': {'y': 26, 'x': 62, 'fontSize': m(12)}}),
        K.livello('zo-hint', contenuto='Scorrete: si semina ↓', x=6, y=82, w=30, size=12, peso='600',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 15, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 31.5, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('zo-velo', 'sinistra', 36, 62, colore=C['nero'], forza=84, mobile_alto=34)
    sl = [K.diapositiva('zo-1', K.sfondo_video(V_MANI, overlay=0.2, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=2000, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Zolla · Bologna, dal 2015', ['Dove passavano', 'i treni merci.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Dieci anni fa qui c’erano binari e ortiche. Adesso ci sono sessanta parcelle, un frutteto, '
                                'le arnie, una cucina all’aperto e l’orto dei bambini. Ognuno coltiva il suo pezzo, e il '
                                'sabato mattina si lavora insieme agli spazi comuni.', size=17, color=C['testo']),
                      K.button(t, 'Gli orti', ORTI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([P.schede_icone(K, t, [('01', 'parcelle', '60', '', 'Da 30 m², con l’acqua e il capanno degli attrezzi.',
                                      'shovel'),
                                     ('02', 'famiglie', '140', '', 'Pensionati, studenti, famiglie con bambini.', 'users'),
                                     ('03', 'di compost', '9', ' t', 'All’anno, dagli scarti del quartiere.', 'recycle'),
                                     ('04', 'alberi', '48', '', 'Il frutteto piantato nel 2018: meli, peri, fichi.', 'trees')],
                              fondo=C['sala'])], style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'L’anno nell’orto', ['Quattro stagioni,', 'sempre qualcosa da fare.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('Primavera', 'marzo — maggio', 'pexels-7728874.jpg', 'Si comincia', 'La semina', 'e i trapianti',
             'Le piantine dal semenzaio comune, i pomodori a fine aprile, i fagioli quando fa caldo.', '1.200', 'piantine'),
            ('Estate', 'giugno — agosto', 'pexels-35913668.jpg', 'Si raccoglie', 'Pomodori', 'e zucchine',
             'L’acqua la sera, i turni d’agosto per chi parte, la festa del raccolto a luglio.', '1.800', 'kg di verdura'),
            ('Autunno', 'settembre — novembre', 'pexels-16723207.jpg', 'Si prepara', 'Le zucche', 'e il compost',
             'Le zucche, i cavoli, il compost rivoltato e la legna per l’inverno.', '9', 't di compost')], icona='sprout')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_ANNAFFIA, 'La sera', 'L’acqua <em>quando il sole cala.</em>',
                             'Alle sette di sera l’orto si riempie: si annaffia, si chiacchiera, ci si scambia le zucchine.',
                             overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Insieme', ['Non solo', 'verdura.'], size=56)]),
        P.carte(K, t, [
            ('pexels-11286045.jpg', 'L’orto dei bambini', 'Il mercoledì pomeriggio', 'Dodici aiuole per le scuole del '
             'quartiere: si semina, si guarda crescere, si mangia.'),
            ('pexels-9722002.jpg', 'I laboratori', 'Il sabato mattina', 'Potatura, compost, semi da conservare: due ore con '
             'chi ne sa di più.'),
            ('pexels-7658828.jpg', 'La festa del raccolto', 'A metà luglio', 'Una tavolata lunga cento metri sotto il '
             'pergolato, aperta a tutto il quartiere.'),
            ('pexels-29219216.jpg', 'La banca dei semi', 'Tutto l’anno', 'Semi antichi conservati da noi: si prendono e '
             'si restituiscono a fine stagione.'),
        ]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [P.testimonianza(
        K, t, 'Sono andata in pensione a marzo e a maggio avevo i primi pomodori. Ma soprattutto avevo sessanta vicini: '
              'prima conoscevo solo quella del piano di sopra.', 'Rosanna', 'Parcella 23, dal 2022', size=34)])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_CESTO, 'Partecipa', 'Ci sono ancora <em>sei parcelle.</em>',
                          'Si assegnano a febbraio: la quota è di sessanta euro all’anno, acqua compresa.', 'Partecipa',
                          PARTECIPA, overlay=18, video=True))
    return out


def build_orti():
    t = Tpl(ID, 'gli-orti')
    out = [K.intro(t, 'Gli orti', ['Sessanta parcelle,', 'un regolamento corto.'],
                   'Niente pesticidi, niente concimi chimici, l’acqua con giudizio, un sabato al mese per gli spazi comuni. '
                   'Il resto lo decidete voi.', bg=K.video_bg(V_FILARI), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le quote', ['All’anno,', 'acqua compresa.'], size=56)]),
        t.row([t.col([P.listino(K, t, [
            ('Parcella piccola', '30 m², per chi comincia', '60 €', 'pexels-7658795.jpg', ''),
            ('Parcella grande', '60 m², per le famiglie', '100 €', 'pexels-27742358.jpg', 'la più chiesta'),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Mezza parcella', '15 m², in due', '35 €', 'pexels-37113938.jpg', ''),
            ('La tessera', 'Per i laboratori e la festa', '10 €', 'pexels-6913442.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Nell’orto', ['Cosa cresce', 'qui.'], size=56)]),
        P.fila_foto(K, t, [('pexels-40447235.jpg', 'I pomodori sulla pianta'), ('pexels-38279065.jpg', 'Le file '
                                                                                                     'dell’insalata'),
                           ('pexels-6187904.jpg', 'I pomodori verdi'), ('pexels-15429028.jpg', 'Le foglie di insalata')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_ZUCCHE, 'L’autunno', 'Le zucche <em>arrivano tutte insieme.</em>',
                             'A ottobre ce ne sono così tante che le regaliamo alla mensa del quartiere.', overlay=22))
    return out


def build_insieme():
    t = Tpl(ID, 'insieme')
    out = [K.intro(t, 'Insieme', ['Il sabato mattina', 'si lavora per tutti.'],
                   'I laboratori sono aperti anche a chi non ha una parcella: basta la tessera da dieci euro.',
                   bg=K.photo_bg('pexels-5029776.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il calendario', ['I prossimi', 'appuntamenti.'], size=56)]),
        t.one([P.righe(K, t, [('15 feb', 'L’assegnazione delle parcelle', 'In sede, alle 10', 'aperto a tutti'),
                              ('8 mar', 'Il semenzaio comune', 'Si seminano i pomodori sotto la serra', 'laboratorio'),
                              ('12 apr', 'La potatura del frutteto', 'Con un agronomo, guanti e forbici', 'laboratorio'),
                              ('17 mag', 'Il compost, spiegato bene', 'Due ore al cumulo', 'laboratorio'),
                              ('12 lug', 'La festa del raccolto', 'Sotto il pergolato, dalle 18', 'festa')], PARTECIPA,
                       titolo=32)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi coordina', ['Tre volontarie,', 'e un trattore piccolo.'], size=56)]),
        K.squadra(t, [
            ('pexels-29085775.jpg', 'Elena Tassinari', 'Presidente', 'Agronoma, ha disegnato l’orto nel 2015.', 'center 25%'),
            ('pexels-5029773.jpg', 'Marta Galli', 'L’orto dei bambini', 'Maestra elementare, il mercoledì con le scuole.',
             'center 25%'),
            ('pexels-8784683.jpg', 'Giulia Ferri', 'Laboratori e festa', 'Cuoca, organizza la tavolata di luglio.',
             'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    return out


def build_partecipa():
    t = Tpl(ID, 'partecipa')
    out = [K.intro(t, 'Partecipa', ['Una parcella,', 'o anche solo una mano.'],
                   'Le parcelle si assegnano a febbraio, in ordine di iscrizione. Per dare una mano negli spazi comuni '
                   'basta venire un sabato mattina.', bg=K.photo_bg('pexels-12969371.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Vorreste', 'cosa', opzioni='Una parcella piccola\nUna parcella grande\nMezza parcella\nDare '
                     'una mano il sabato\nL’orto per la mia classe'),
             K.campo('select', 'Abitate', 'dove', opzioni='Nel quartiere\nA Bologna\nFuori Bologna'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'iscriviti', 'Iscriviti', ['Il nome in lista,', 'a febbraio la terra.'],
                                'Vi scriviamo a gennaio con la data dell’assegnazione.', VOCI, campi, 'Zolla — iscrizione',
                                'Iscrivimi', 'Grazie! Vi scriviamo a gennaio.'))
    out.append(K.domande(t, 'Prima di iscriversi', ['Le domande', 'di chi comincia.'], [
        ('Non ho mai coltivato niente.', 'Benissimo: il primo anno vi affianchiamo a un ortolano esperto.'),
        ('Gli attrezzi?', 'Ci sono, nel capanno comune. Portate solo guanti e stivali.'),
        ('E ad agosto, se parto?', 'Ci sono i turni: qualcuno annaffia per voi, voi annaffiate per lui a luglio.'),
        ('Possono venire i bambini?', 'Certo, e c’è anche l’orto apposta per loro.'),
    ], 'zo-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['All’ex scalo,', 'il cancello verde.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Aperto', 'tutti i giorni, dall’alba al tramonto'), ('In bici', 'la ciclabile della '
                                                                                                    'Bolognina'),
                                  ('In autobus', 'linea 27, fermata Scalo')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Zolla · orti', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · terreno incolto', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui non è <em>ancora spuntato niente.</em>', size=80, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La pagina non c’è. Torniamo al cancello.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SEMI, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Associazioni e cause', 'La semina',
    ['orti urbani', 'orti condivisi', 'associazione', 'quartiere', 'bologna', 'chiaro', 'slider pro', 'scorrimento'],
    'Orti condivisi in un ex scalo ferroviario, terra e verde orto: in apertura tre solchi sul video delle mani nella '
    'terra, e scorrendo i semi cadono uno alla volta e diventano germogli che ondeggiano; le stagioni dell’orto, i '
    'laboratori, le quote delle parcelle e l’iscrizione.',
    [('Gli orti', ORTI), ('Insieme', INSIEME), ('Partecipa', PARTECIPA)],
    [('home', 'Home', None), ('gli-orti', 'Gli orti', 'gli-orti'), ('insieme', 'Insieme', 'insieme'),
     ('partecipa', 'Partecipa', 'partecipa')],
    HEX, '\'Figtree\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Outfit\', system-ui, sans-serif',
    '\'Spline Sans Mono\', ui-monospace, Menlo, monospace', ['Figtree', 'Outfit', 'Spline Sans Mono'], corsivo_di=[],
    peso_titoli='600', raggio=16, grana=0, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'pioggerella'
THEME['cursor']['ring_color'] = '#E9EFE6'
THEME['cursor']['dot_color'] = '#4C9BD6'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'gli-orti.json': build_orti(), 'insieme.json': build_insieme(), 'partecipa.json': build_partecipa(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
