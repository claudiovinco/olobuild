# -*- coding: utf-8 -*-
"""Genera il tema «Strada Bianca» (id strada-bianca): un noleggio di camper e van a Firenze: sei mezzi attrezzati, gli
itinerari per la Toscana (Chianti, Val d’Orcia, la costa), il ritiro in sede e la consegna a domicilio.

Tema chiaro, la Toscana d’estate: sabbia, verde cipresso, ocra e terracotta. Titoli in Bricolage Grotesque, testo in
Albert Sans, dati in JetBrains Mono.

Apertura: «il viaggio». Lo slider resta fermo in cima mentre si scorre: sul video della strada fra i cipressi, a destra,
una carta della Toscana col percorso tratteggiato da Firenze al mare. Scorrendo il segnalino del navigatore percorre la
strada tappa dopo tappa (Greve, Siena, Pienza, Montalcino, Bolgheri), girandosi nelle curve; il tratto fatto si
accende, i paesi spuntano col loro nome e il contachilometri sale fino a 214. Sul telefono la carta sta sotto il
titolo, con un percorso tutto suo. Cursore: il volante.
"""
import math
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'strada-bianca'
NAME = 'Strada Bianca'

HEX = {
    'primary': '#2E5E4E', 'primary_contrast': '#FFFFFF', 'secondary': '#E8A15C', 'secondary_contrast': '#1A1F1B',
    'accent': '#C8553D', 'dark': '#1A1F1B', 'light': '#FFFFFF', 'muted': '#F1EADF', 'muted_contrast': '#1F241F',
    'text': '#1F241F', 'text_muted': '#5B5F57', 'background': '#FBF8F2', 'border': '#E2D8C7', 'link': '#2E5E4E',
}

HOME, FLOTTA, ITINERARI, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:flotta}}', '{{PAGINA:itinerari}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'partenze@stradabianca.example', '+39 055 000 7781', 'tel:+390550007781'
LAT, LNG = '43.7930', '11.2050'

V_CIPRESSI, V_CURVA, V_DRONE, V_CAMPER, V_NOTTE, V_COLLINE, V_PODERE, V_SALITA, V_TRAMONTO = (
    'pexels-video-33589364', 'pexels-video-8230615', 'pexels-video-26263168', 'pexels-video-8152842', 'pexels-video-9354170',
    'pexels-video-30779748', 'pexels-video-36730009', 'pexels-video-31580581', 'pexels-video-36431824')
PHOTOS = [
    'pexels-28897042.jpg', 'pexels-14816413.jpg', 'pexels-15346246.jpg', 'pexels-5093241.jpg', 'pexels-6946075.jpg',
    'pexels-15240614.jpg', 'pexels-16746356.jpg', 'pexels-12709207.jpg', 'pexels-8231196.jpg', 'pexels-19478842.jpg',
    'pexels-35451821.jpg', 'pexels-4484242.jpg', 'pexels-8975590.jpg', 'pexels-5502746.jpg', 'pexels-12514699.jpg',
    'pexels-30018173.jpg', 'pexels-33250842.jpg', 'pexels-36719404.jpg', 'pexels-6946140.jpg', 'pexels-8231014.jpg',
    'pexels-8231139.jpg', 'pexels-9143492.jpg', 'pexels-18014082.jpg', 'pexels-5078352.jpg', 'pexels-8231163.jpg',
    'pexels-4592018.jpg', 'pexels-30131061.jpg', 'pexels-27623983.jpg', 'pexels-21624458.jpg', 'pexels-17539759.jpg',
    'pexels-6271698.jpg', 'pexels-9143468.jpg', 'pexels-6945994.jpg', 'pexels-3935702.jpg', 'pexels-7009483.jpg',
    'pexels-8033781.jpg', 'pexels-7368300.jpg', 'pexels-10375957.jpg', 'pexels-7937959.jpg',
]
VIDEOS = [V_CIPRESSI, V_CURVA, V_DRONE, V_CAMPER, V_NOTTE, V_COLLINE, V_PODERE, V_SALITA, V_TRAMONTO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=12, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
OCRA, COTTO = C['sec'], C['acc2']
MONO = 'JetBrains Mono'
VOCI = [('map-pin', 'Via Pistoiese 210 · Firenze', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Strada Bianca', logo_size='22', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Strada Bianca', 'Camper e van a noleggio a Firenze, dal 2018. Sei mezzi attrezzati, gli itinerari '
                   'scritti da noi, il ritiro in sede o la consegna dove volete.',
                   [('La flotta', FLOTTA), ('Gli itinerari', ITINERARI), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'La sede', 'Ritiri e riconsegne<br>tutti i giorni 8 — 19', VOCI,
                   '© 2026 Strada Bianca · Firenze · P.IVA 00000000000', [('instagram', 'https://instagram.com')],
                   marchio_size=130, fondo=C['sala'])


# ── L'apertura: il viaggio sulla carta ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
# le tappe: nome, km, punto sul computer (%), punto sul telefono (%)
TAPPE = [('Firenze', 0, (61, 22), (24, 44)), ('Greve', 38, (66, 33), (40, 51)), ('Siena', 72, (72.5, 47), (58, 58)),
         ('Pienza', 120, (86, 57), (82, 65)), ('Montalcino', 168, (78, 70), (66, 75)), ('Bolgheri, il mare', 214, (60, 72),
                                                                                     (26, 79))]
N = len(TAPPE)
PASSO = 100.0 / (N - 1)


def _px(p, mob):
    return (p[0] / 100 * (WM if mob else W), p[1] / 100 * (HM if mob else H))


def _rotta(mob):
    """Il percorso, il segnalino e i paesi per un formato. Su computer e tablet valgono i livelli normali; quelli del
    telefono stanno fuori dalla tela sugli altri formati e i loro spostamenti sono moltiplicati per 1440/640 (il JS li
    riduce in proporzione alla tela)."""
    m = K.mob
    suf = 'm' if mob else ''
    scala = W / WM if mob else 1.0
    pts = [tp[3] if mob else tp[2] for tp in TAPPE]
    px = [_px(p, mob) for p in pts]
    out = []

    def tonda(v):
        return round(v, 2) if isinstance(v, (int, float)) else v

    def dove(x, y, w, h):
        if mob:
            return dict(x=-60, y=tonda(y), w=w, h=h, responsive={'mobile': {'x': tonda(x), 'y': tonda(y), 'width': tonda(w),
                                                                            'height': tonda(h)}})
        return dict(x=tonda(x), y=tonda(y), w=tonda(w), h=tonda(h), responsive={'mobile': {'visible': False}})

    spess = 5.0
    for i in range(N - 1):
        (x1, y1), (x2, y2) = px[i], px[i + 1]
        L = math.hypot(x2 - x1, y2 - y1)
        ang = math.degrees(math.atan2(y2 - y1, x2 - x1))
        cx, cy = (x1 + x2) / 2, (y1 + y2) / 2
        Wc, Hc = (WM, HM) if mob else (W, H)
        geo = dove((cx - L / 2) / Wc * 100, (cy - spess / 2) / Hc * 100, L / Wc * 100, spess / Hc * 100)
        a, b = i * PASSO, (i + 1) * PASSO
        out.append(K.livello('sb-r%d%s' % (i, suf), tipo='shape', bgColor=C['acc'], borderRadius=999, entra='fadeIn',
                             ritardo=600, durata=600,
                             scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': round(ang, 2), 'o': 22},
                                                                            {'p': a, 'r': round(ang, 2), 'o': 22},
                                                                            {'p': b, 'r': round(ang, 2), 'o': 100},
                                                                            {'p': 100, 'r': round(ang, 2), 'o': 100}]},
                             **geo))
    # i paesi: il punto e il nome
    for i, (nome, km, pd, pm) in enumerate(TAPPE):
        x, y = px[i]
        Wc, Hc = (WM, HM) if mob else (W, H)
        d = 16.0
        arrivo = i * PASSO
        pop = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 0.4, 'o': 0 if i else 100},
                                                    {'p': max(0, arrivo - 4), 's': 0.4, 'o': 0 if i else 100},
                                                    {'p': arrivo, 's': 1, 'o': 100}, {'p': 100, 's': 1, 'o': 100}]}
        if i == 0:
            pop = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 1, 'o': 100}, {'p': 100, 's': 1, 'o': 100}]}
        out.append(K.livello('sb-p%d%s' % (i, suf), tipo='shape', bgColor=C['chiaro'], borderRadius=999, borderWidth=4,
                             borderColor=C['acc'] if i else C['nero'], entra='fadeIn', ritardo=500, durata=500, scrollMove=pop,
                             **dove((x - d / 2) / Wc * 100, (y - d / 2) / Hc * 100, d / Wc * 100, d / Hc * 100)))
        sotto = i in (0, 3)
        lx, ly = x + 14, y - 9
        lab = K.livello('sb-n%d%s' % (i, suf), contenuto=nome, size=14 if not mob else m(12), peso='700', colore=C['nero'],
                        fontFamily=MONO, letterSpacing=1, textTransform='uppercase', bgColor=mix(C['chiaro'], 92),
                        padding=6, borderRadius=6, entra='fadeIn', ritardo=0, durata=400,
                        scrollMove=dict(pop), **dove(lx / Wc * 100, (ly + (22 if sotto else -26)) / Hc * 100, 'auto', 'auto'))
        lab['width'] = 'auto'
        if mob:
            lab['responsive']['mobile']['width'] = 'auto'
            lab['responsive']['mobile']['fontSize'] = m(10)
        out.append(lab)
    # il segnalino del navigatore: parte da Firenze e segue le tappe, girato nel verso della strada
    x0, y0 = px[0]
    tappe = []
    for i in range(N):
        x, y = px[i]
        j = min(i, N - 2)
        (ax, ay), (bx, by) = px[j], px[j + 1]
        ang = math.degrees(math.atan2(by - ay, bx - ax)) + 90
        tappe.append({'p': round(i * PASSO, 2), 'x': round((x - x0) * scala, 1), 'y': round((y - y0) * scala, 1),
                      'r': round(ang, 1)})
    Wc, Hc = (WM, HM) if mob else (W, H)
    d = 40.0
    nav = K.livello('sb-nav%s' % suf, tipo='icon', iconName='navigation-2', iconFillColor=COTTO, color=C['nero'],
                    iconStrokeWidth=1.6, size=d if not mob else 34, entra='fadeIn', ritardo=700, durata=500,
                    scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe},
                    **dove((x0 - d / 2) / Wc * 100, (y0 - d / 2) / Hc * 100, d / Wc * 100, d / Hc * 100))
    out.append(nav)
    return out


def apertura(t):
    m = K.mob
    carta = [
        K.livello('sb-carta', tipo='shape', x=53, y=12, w=42, h=76, bgColor='#F6EFDF', borderRadius=18, entra='fadeInUp',
                  ritardo=200, durata=1000, boxShadow={'x': 0, 'y': 24, 'blur': 60, 'spread': -16, 'color': mix(C['nero'], 60)},
                  responsive={'mobile': {'x': 6, 'y': 39, 'width': 88, 'height': 46}}),
        # il mare, appena accennato sulla carta
        K.livello('sb-mare', tipo='shape', x=53, y=64, w=7, h=24, bgColor=mix('#4C8FB0', 30),
                  borderRadiusLinked=False, borderRadiusTL=0, borderRadiusTR=60, borderRadiusBR=0, borderRadiusBL=18,
                  entra='fadeIn', ritardo=400, durata=1000,
                  responsive={'mobile': {'x': 6, 'y': 74, 'width': 14, 'height': 11}}),
        K.livello('sb-bussola', tipo='icon', iconName='compass', color=mix(C['nero'], 55), iconStrokeWidth=1.2, x=89.5, y=15,
                  w=3.4, h=6, size=42, entra='fadeIn', ritardo=900, durata=800, animLoop='pendulum', animLoopDuration=5200,
                  responsive={'mobile': {'x': 82, 'y': 40.5, 'width': 8, 'height': 3.6, 'fontSize': 40}}),
    ]
    km = []
    for i, (nome, chilometri, pd, pm) in enumerate(TAPPE):
        a = i * PASSO
        tp = [{'p': 0, 'o': 100 if i == 0 else 0}]
        if i:
            tp += [{'p': max(0, a - 1), 'o': 0}, {'p': a + 3, 'o': 100}]
        if i < N - 1:
            tp += [{'p': a + PASSO - 6, 'o': 100}, {'p': a + PASSO - 1, 'o': 0}, {'p': 100, 'o': 0}]
        else:
            tp += [{'p': 100, 'o': 100}]
        km.append(K.livello('sb-km%d' % i, contenuto='km %d' % chilometri, x=55.5, y=79.5, w=20, size=30, peso='700',
                            colore=C['acc'], fontFamily=MONO, entra='fadeIn', ritardo=800, durata=400,
                            scrollMove={'inizio': 0, 'fine': 100, 'tappe': tp},
                            responsive={'mobile': {'fontSize': m(22), 'x': 58, 'y': 80.6, 'width': 34, 'textAlign': 'right'}}))
    testi = [
        K.livello('sb-o', contenuto='Strada Bianca · camper e van · Firenze', x=6, y=22, w=40, size=12, peso='600',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 7.5, 'x': 7, 'width': 86}}),
        K.livello('sb-t1', contenuto='Le chiavi,', tag='h1', x=6, y=28, w=46, size=92, peso='700', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(44), 'y': 10.5, 'x': 7, 'width': 86}}),
        K.livello('sb-t2', fontFamily='Bricolage Grotesque', contenuto='poi la strada.', tag='p', x=6, y=40, w=46, size=92, peso='700', colore=OCRA,
                  lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(44), 'y': 15, 'x': 7, 'width': 86}}),
        K.livello('sb-s', contenuto='Sei camper e van attrezzati, gli itinerari scritti da noi e il ritiro a Firenze. Si '
                                    'parte anche per un weekend.', x=6, y=54, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 21, 'x': 7, 'width': 86}}),
        K.livello('sb-b', tipo='button', contenuto='Scegli il mezzo', x=6, y=69, w='auto', size=14, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=12, entra='fadeInUp', ritardo=1000,
                  buttonUrl=FLOTTA, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 28, 'x': 7, 'fontSize': m(12)}}),
        K.livello('sb-l', tipo='button', contenuto='Gli itinerari', x=19.5, y=69, w='auto', size=14, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=12, borderWidth=1,
                  borderColor=mix(C['chiaro'], 70), entra='fadeInUp', ritardo=1100, buttonUrl=ITINERARI,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 24.5}, 'mobile': {'y': 28, 'x': 50, 'fontSize': m(12)}}),
        K.livello('sb-hint', contenuto='Scorrete: si parte da Firenze ↓', x=6, y=84, w=30, size=12, peso='600',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 18, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 33.5, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('sb-velo', 'sinistra', 34, 62, colore=C['nero'], forza=84, mobile_alto=35)
    livelli = veli + carta + _rotta(False) + _rotta(True) + km + testi
    sl = [K.diapositiva('sb-1', K.sfondo_video(V_CIPRESSI, overlay=0.2, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=2000, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


MEZZI = [  # foto, nome, davanti, dietro
    ('pexels-28897042.jpg', 'Il Cipresso', 'Van per due · da 95 € al giorno', 'Letto matrimoniale, cucina, frigo, '
     'pannello solare. Si guida con la patente B, si parcheggia ovunque.'),
    ('pexels-15346246.jpg', 'La Pieve', 'Van per quattro · da 120 €', 'Tetto che si alza, due letti, cucina e doccia '
     'esterna. Il preferito delle famiglie.'),
    ('pexels-5093241.jpg', 'Il Podere', 'Camper per quattro · da 145 €', 'Bagno con doccia, riscaldamento, il letto '
     'sopra la cabina. Per l’autunno e l’inverno.'),
    ('pexels-14816413.jpg', 'La Vespa', 'Van d’epoca per due · da 130 €', 'Un van degli anni Settanta rimesso a nuovo: '
     'lento, bellissimo, solo d’estate.'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Strada Bianca · Firenze, dal 2018', ['Un mezzo pronto,', 'un itinerario scritto.'],
                                size=56, layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Ve lo consegniamo pulito, col pieno, le lenzuola, i piatti e la carta degli itinerari. '
                                'Mezz’ora di spiegazioni nel piazzale, e poi partite: noi restiamo al telefono per tutto il '
                                'viaggio.', size=17, color=C['testo']),
                      K.button(t, 'La flotta', FLOTTA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([P.schede_icone(K, t, [('01', 'mezzi', '6', ' pronti', 'Van e camper, da due a quattro posti letto.', 'truck'),
                                     ('02', 'itinerari', '12', ' scritti', 'Le strade bianche, le soste, dove mangiare.',
                                      'map'),
                                     ('03', 'patente', 'B', '', 'Tutti si guidano con la patente dell’auto.', 'id-card'),
                                     ('04', 'assistenza', '24', ' ore', 'Un numero che risponde, anche ad agosto.', 'phone')],
                              fondo=C['sala'])], style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La flotta', ['Quattro dei sei,', 'girateli.'], size=56,
                         tagline='Passate su un mezzo: dietro ci sono i letti e la cucina.')]),
        P.carte(K, t, MEZZI, altezza='400'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_DRONE, 'La strada', 'Le strade bianche <em>non hanno fretta.</em>',
                             'Sterrati fra le vigne, cipressi in fila, un podere dove fermarsi per la notte.', overlay=28))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli itinerari', ['Dodici viaggi,', 'da due a dieci giorni.'], size=56)]),
        t.one([P.righe(K, t, [('2 gg', 'Il Chianti in un weekend', 'Greve, Panzano, Radda · le cantine', '140 km'),
                              ('4 gg', 'La Val d’Orcia', 'Pienza, Bagno Vignoni, Montalcino', '260 km'),
                              ('5 gg', 'Dalle colline al mare', 'Siena, Massa Marittima, Bolgheri', '320 km'),
                              ('7 gg', 'La Toscana minore', 'Lunigiana e Garfagnana', '410 km')], ITINERARI, titolo=34)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [P.testimonianza(
        K, t, 'Siamo partiti il venerdì sera con due bambini e un cane. La carta degli itinerari aveva anche il forno dove '
              'comprare il pane la mattina: l’abbiamo trovato, ed era buonissimo.', 'Famiglia Bertolini', 'Val d’Orcia, '
              'quattro giorni')])], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_NOTTE, 'La sera', 'Dormire <em>dove vi pare.</em>',
                          'Nelle aree di sosta, negli agriturismi amici, sotto le stelle della Maremma.', 'Prenota', PRENOTA,
                          overlay=20, video=True))
    return out


def build_flotta():
    t = Tpl(ID, 'flotta')
    out = [K.intro(t, 'La flotta', ['Sei mezzi,', 'tutti diversi.'],
                   'Ogni mezzo ha un nome e un posto nel piazzale. Li puliamo, li controlliamo e li prepariamo noi, prima di '
                   'ogni partenza.', bg=K.photo_bg('pexels-12709207.jpg'), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Van · 2 posti · da 95 € al giorno', 'Il Cipresso', 'per due',
         '<p>Il più agile: si parcheggia nei borghi e passa sulle strade bianche.</p><ul><li>Letto 140 × 195</li><li>'
         'Cucina a due fuochi e frigo</li><li>Pannello solare</li></ul>', 'pexels-6946075.jpg'),
        ('Van · 4 posti · da 120 €', 'La Pieve', 'con il tetto',
         '<p>Il tetto si alza e sopra c’è il secondo letto.</p><ul><li>Doccia esterna calda</li><li>Tavolo per quattro'
         '</li><li>Seggiolino per bambini</li></ul>', 'pexels-8231196.jpg'),
        ('Camper · 4 posti · da 145 €', 'Il Podere', 'tutto l’anno',
         '<p>Bagno, riscaldamento e l’acqua calda: il mezzo per l’inverno.</p><ul><li>Bagno con doccia</li><li>'
         'Riscaldamento a gasolio</li><li>Garage per le bici</li></ul>', 'pexels-15240614.jpg'),
    ])], anim=None)], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Al giorno,', 'tutto compreso.'], size=56,
                         tagline='Lenzuola, piatti, assicurazione kasko e assistenza sono già nel prezzo.')]),
        t.row([t.col([P.listino(K, t, [
            ('Il Cipresso', 'Van per due', '95 €', 'pexels-28897042.jpg', ''),
            ('La Pieve', 'Van per quattro', '120 €', 'pexels-15346246.jpg', 'il più scelto'),
            ('La Vespa', 'Van d’epoca per due', '130 €', 'pexels-14816413.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Il Podere', 'Camper per quattro', '145 €', 'pexels-5093241.jpg', ''),
            ('Consegna a domicilio', 'Firenze e provincia', '40 €', 'pexels-10375957.jpg', ''),
            ('Kit bici', 'Due biciclette e il portabici', '25 €', 'pexels-36719404.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dentro', ['Tutto al suo posto,', 'anche il caffè.'], size=56)]),
        P.fila_foto(K, t, [('pexels-16746356.jpg', 'Il letto del van con la luce calda'),
                           ('pexels-19478842.jpg', 'La cucina del van'), ('pexels-6946140.jpg', 'La colazione nel van'),
                           ('pexels-9143492.jpg', 'Due tazze di caffè fuori dal van')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_itinerari():
    t = Tpl(ID, 'itinerari')
    out = [K.intro(t, 'Gli itinerari', ['Le strade', 'che sceglieremmo noi.'],
                   'Li abbiamo fatti tutti, più volte. Sulla carta ci sono le soste, le salite da evitare col camper, i '
                   'forni e le osterie aperte la sera.', bg=K.video_bg(V_COLLINE), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Tre viaggi', ['Dal Chianti', 'al mare.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('2', 'giorni', 'pexels-5502746.jpg', 'Il weekend', 'Il Chianti', 'in due giorni',
             'Greve, Panzano e Radda: le cantine, la cena in fattoria, la notte fra le vigne.', '140', 'km'),
            ('4', 'giorni', 'pexels-30018173.jpg', 'Il classico', 'La Val', 'd’Orcia',
             'Pienza, le terme di Bagno Vignoni, il Brunello a Montalcino, i cipressi di San Quirico.', '260', 'km'),
            ('5', 'giorni', 'pexels-4592018.jpg', 'Il lungo', 'Dalle colline', 'al mare',
             'Siena, Massa Marittima e giù fino a Bolgheri, per finire col tramonto sulla spiaggia.', '320', 'km')],
            icona='map-pin')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_TRAMONTO, 'Il tramonto', 'L’ultima sera <em>sul mare.</em>',
                             'Il van aperto verso l’acqua, la cena fuori, le sedie pieghevoli nella sabbia.', overlay=28))
    out.append(t.section([
        t.one([K.sechead(t, 'In viaggio', ['Le foto', 'che ci mandate.'], size=56)]),
        P.fila_foto(K, t, [('pexels-30131061.jpg', 'Il camper in montagna'), ('pexels-8231163.jpg', 'Il van davanti al mare'),
                           ('pexels-35451821.jpg', 'Il campo con le luci la sera'), ('pexels-6271698.jpg', 'Una coppia '
                                                                                   'col van')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Le date,', 'il mezzo, si parte.'],
                   'Ditemi quando e quanti siete: vi rispondiamo in giornata con la disponibilità e il prezzo finale.',
                   bg=K.photo_bg('pexels-7937959.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('date', 'Partenza', 'partenza', True), K.campo('date', 'Ritorno', 'ritorno', True),
             K.campo('select', 'Il mezzo', 'mezzo', opzioni='Fate voi\nIl Cipresso\nLa Pieve\nIl Podere\nLa Vespa'),
             K.campo('select', 'Quanti siete', 'persone', opzioni='2\n3\n4'),
             K.campo('textarea', 'Dove vorreste andare', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Le date e quanti siete,', 'al resto pensiamo noi.'],
                                'Vi rispondiamo in giornata. La caparra è del 30%, il resto al ritiro.', VOCI, campi,
                                'Strada Bianca — prenotazione', 'Chiedi la disponibilità',
                                'Ricevuto. Vi rispondiamo in giornata con la disponibilità.'))
    out.append(K.domande(t, 'Prima di partire', ['Le domande', 'di chi parte la prima volta.'], [
        ('Serve una patente speciale?', 'No, tutti i nostri mezzi si guidano con la patente B.'),
        ('Si può portare il cane?', 'Sì, nella Pieve e nel Podere, con un supplemento di 40 € per la pulizia.'),
        ('Quanti chilometri sono compresi?', 'Duecento al giorno; oltre, 0,25 € al chilometro.'),
        ('E se si rompe qualcosa?', 'Chiamate: c’è l’assistenza stradale ventiquattro ore e un meccanico amico in ogni '
                                    'provincia.'),
    ], 'sb-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il piazzale', ['A Firenze,', 'via Pistoiese.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Ritiri', 'tutti i giorni 8 — 19'), ('Dall’autostrada', 'uscita Firenze Nord, 6 minuti'),
                                  ('L’auto', 'la lasciate da noi, al coperto')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Strada Bianca', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · strada chiusa', color=K.acc_media, align='center'),
                       K.headline(t, 'Ricalcolo <em>del percorso.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Questa strada non porta da nessuna parte. Torniamo al piazzale.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CURVA, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Noleggio', 'Il viaggio sulla carta',
    ['noleggio camper', 'van', 'viaggi', 'toscana', 'firenze', 'chiaro', 'slider pro', 'scorrimento'],
    'Un noleggio di camper e van a Firenze, sabbia, cipresso e terracotta: in apertura la carta della Toscana sul video '
    'della strada fra i cipressi, e scorrendo il segnalino del navigatore fa il viaggio da Firenze al mare, coi paesi '
    'che spuntano e il contachilometri che sale; la flotta da girare, gli itinerari, i prezzi e la prenotazione.',
    [('La flotta', FLOTTA), ('Gli itinerari', ITINERARI), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('flotta', 'La flotta', 'flotta'), ('itinerari', 'Gli itinerari', 'itinerari'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Albert Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bricolage Grotesque\', system-ui, sans-serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Albert Sans', 'Bricolage Grotesque', 'JetBrains Mono'],
    corsivo_di=[], peso_titoli='700', raggio=12, grana=0, titoli_line='1.0', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'volante'
THEME['cursor']['ring_color'] = HEX['dark']
THEME['cursor']['dot_color'] = HEX['secondary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'flotta.json': build_flotta(), 'itinerari.json': build_itinerari(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
