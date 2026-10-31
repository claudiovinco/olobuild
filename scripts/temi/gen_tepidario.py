# -*- coding: utf-8 -*-
"""Genera il tema «Tepidario» (id tepidario): un hammam alla Kalsa, a Palermo, in un palazzo arabo-normanno: il percorso
delle sale calde, il gommage col guanto di kessa, i massaggi, il tè alla menta nella sala del riposo.

Tema scuro e caldo: pietra bruna, rame, verde delle maioliche e oro delle candele. Titoli in Marcellus, testo in Jost,
dati in Spline Sans Mono.

Apertura: Slider Pro «la cupola», alta 16:9, una sala del percorso per diapositiva col suo video pieno (il vapore e i
secchi di legno, l'acqua sulla pietra, il massaggio, il tè versato) e un velo scuro solo dal lato del testo. A destra la
cupola vista dal basso: i lucernari a stella brillano piano, tre raggi di luce scendono nel vapore e al centro c'è la
temperatura della sala. Le sale si danno il cambio sfumando (fade). Cursore: la cometa.
"""
import os
import sys
import math

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'tepidario'
NAME = 'Tepidario'

HEX = {
    'primary': '#C9894A', 'primary_contrast': '#140E0A', 'secondary': '#6FA39A', 'secondary_contrast': '#0E1513',
    'accent': '#E9C79A', 'dark': '#0A0706', 'light': '#FFF8EE', 'muted': '#1F1714', 'muted_contrast': '#F1E6D6',
    'text': '#F1E6D6', 'text_muted': '#B5A592', 'background': '#15100D', 'border': '#362820', 'link': '#C9894A',
}

HOME, RITUALE, TRATTAMENTI, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:il-rituale}}', '{{PAGINA:trattamenti}}',
                                        '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'benvenuti@tepidario.example', '+39 091 000 6262', 'tel:+390910006262'
LAT, LNG = '38.1157', '13.3713'

V_VAPORE, V_PIETRA, V_MASSAGGIO, V_CANDELE, V_TE, V_LUMI = ('pexels-video-8284417', 'pexels-video-852306',
                                                          'pexels-video-9335886', 'pexels-video-17115886',
                                                          'pexels-video-5977443', 'pexels-video-4707807')
PHOTOS = [
    'pexels-7598373.jpg', 'pexels-7391598.jpg', 'pexels-25785233.jpg', 'pexels-38911905.jpg', 'pexels-38911900.jpg',
    'pexels-3079492.jpg', 'pexels-6628646.jpg', 'pexels-37719548.jpg', 'pexels-4177714.jpg', 'pexels-30906049.jpg',
    'pexels-31497982.jpg',
]
VIDEOS = [V_VAPORE, V_PIETRA, V_MASSAGGIO, V_CANDELE, V_TE, V_LUMI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=8, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=False)
C = K.c
RAME, MAIOLICA, ORO = C['acc'], C['sec'], C['acc2']
TITOLI, MONO = 'Marcellus', 'Spline Sans Mono'
VOCI = [('map-pin', 'Via della Vetreria 21 · Palermo', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Tepidario', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Tepidario', 'Hammam alla Kalsa, in un palazzo del Quattrocento. Il percorso delle sale calde, il '
                   'gommage col guanto di kessa, i massaggi e il tè alla menta. Su prenotazione, dodici persone alla volta.',
                   [('Il rituale', RITUALE), ('I trattamenti', TRATTAMENTI), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'Gli orari', 'Da martedì a domenica 11 — 22<br>Il mercoledì solo donne', VOCI,
                   '© 2026 Tepidario · Palermo · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=140)


# ── L'apertura: la cupola ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
SALE = [  # linguetta, occhiello, riga 1, riga 2, testo, video, nome della sala, temperatura, dato
    ('Tepidario', 'Prima sala · il tepidario', 'Il caldo che entra', 'piano, dalla pelle.',
     'Si comincia nella sala tiepida, sdraiati sulla pietra: il corpo si abitua, il respiro rallenta, i pensieri anche.',
     V_VAPORE, 'Tepidario', '38°', 'umidità 60%'),
    ('Calidario', 'Seconda sala · il calidario', 'Vapore, pietra,', 'acqua fredda.',
     'La sala più calda, piena di vapore all’eucalipto. Poi l’acqua fredda della fontana, versata con la ciotola di rame.',
     V_PIETRA, 'Calidario', '45°', 'vapore 100%'),
    ('Gommage', 'Terza sala · il gommage', 'Il guanto di kessa', 'e il sapone nero.',
     'Sulla pietra calda, col sapone nero all’oliva e il guanto ruvido: la pelle vecchia va via, quella nuova respira.',
     V_MASSAGGIO, 'Gommage', '40°', 'sapone nero'),
    ('Riposo', 'Quarta sala · il riposo', 'Il tè alla menta,', 'senza orologio.',
     'Nella sala dei cuscini, col tè alla menta e i datteri: si resta finché si vuole. Nessuno vi chiede di andare.',
     V_TE, 'Sala del riposo', '24°', 'tè e datteri'),
]


def _box(cx, cy, r, tw, th):
    return round((cx - r) / tw * 100, 2), round((cy - r) / th * 100, 2), round(2 * r / tw * 100, 2), round(2 * r / th * 100, 2)


def cupola(n, sala, gradi, dato):
    """Sotto la cupola: un disco scuro più grande dello slider, al centro, coi lucernari a stella che brillano e due
    raggi di luce; scorrendo i lucernari girano attorno al centro della cupola (l'origine della rotazione è il centro)
    e la cupola si avvicina, come alzando gli occhi mentre si cammina."""
    cx, cy, R, mx, my, sm = 720.0, 430.0, 560.0, 320.0, 700.0, 1.0
    out = []
    a, b = _box(cx, cy, R, W, H), _box(mx, my, R * 0.62, WM, HM)
    avvicina = {'inizio': 0, 'fine': 100, 'da': {}, 'a': {'s': 1.18}}
    out.append(K.livello('tp-cupola%d' % n, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                         bgColor=mix(C['nero'], 88), borderWidth=2, borderColor=mix(ORO, 30), entra='zoomIn', ritardo=0,
                         durata=1200, esce='fadeOut', scrollMove=avvicina,
                         boxShadow={'x': 0, 'y': 0, 'blur': 120, 'spread': 0, 'color': mix(RAME, 40)},
                         responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    for k, rr in enumerate((470.0, 380.0, 290.0)):
        a, b = _box(cx, cy, rr, W, H), _box(mx, my, rr * 0.62, WM, HM)
        out.append(K.livello('tp-anello%d-%d' % (n, k), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                             bgColor='transparent', borderWidth=1, borderColor=mix(ORO, 16), entra='fadeIn',
                             ritardo=200 + 100 * k, durata=800, esce='fadeOut', scrollMove=avvicina,
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # i lucernari: tre giri di punti; ognuno gira attorno al centro della cupola con lo scorrimento
    j = 0
    for giro, (rr, quanti, rp) in enumerate(((520.0, 22, 8.0), (425.0, 18, 7.0), (335.0, 14, 6.0))):
        for k in range(quanti):
            ang = math.radians(-90 + 360.0 * k / quanti + (8 if giro == 1 else 0))
            px, py = cx + rr * math.cos(ang), cy + rr * math.sin(ang)
            pmx, pmy = mx + rr * 0.62 * math.cos(ang), my + rr * 0.62 * math.sin(ang)
            a = _box(px, py, rp, W, H)
            b = _box(pmx, pmy, rp, WM, HM)
            ox = round((cx - (px - rp)) / (2 * rp) * 100, 1)
            oy = round((cy - (py - rp)) / (2 * rp) * 100, 1)
            dur = 2600 + (j * 370) % 1700
            gira = {'inizio': 0, 'fine': 100, 'da': {}, 'a': {'r': 26 if giro % 2 == 0 else -18}}
            out.append(K.livello('tp-luce%d-%d' % (n, j), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                                 bgColor=ORO, esce='fadeOut', scrollMove=gira,
                                 timeline=K.tl(a[0], a[1], [(0, {'opacity': 25, 'originX': ox, 'originY': oy}),
                                                            (round(dur / 2), {'opacity': 100, 'originX': ox, 'originY': oy},
                                                             'sine.inOut'),
                                                            (dur, {'opacity': 25, 'originX': ox, 'originY': oy})], dur,
                                               ritardo=300 + (j * 113) % 900),
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
            j += 1
    return out


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, video, sala, gradi, dato) in enumerate(SALE):
        n = i + 1
        livelli = cupola(n, sala, gradi, dato)
        livelli += [
            K.livello('tp-o%d' % n, contenuto=occ, x=20, y=22, w=60, size=12, peso='500', colore=ORO, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, align='center', ritardo=200,
                      responsive={'mobile': {'fontSize': m(11), 'y': 33, 'x': 8, 'width': 84}}),
            K.livello('tp-t%d' % n, contenuto=r1 + '<br><span style="color:var(--olo-color-accent)">' + r2 + '</span>',
                      tag='h1' if n == 1 else 'h2', x=15, y=26.5, w=70, size=72, peso='400', colore=C['chiaro'],
                      fontFamily=TITOLI, lineHeight=1.08, align='center', entra='maskFromBottom', ritardo=300, durata=1200,
                      responsive={'mobile': {'fontSize': m(30), 'y': 36.5, 'x': 8, 'width': 84}}),
            K.livello('tp-g%d' % n, contenuto=sala + ' · ' + gradi + ' · ' + dato, x=35, y=48.5, w=30, size=13, peso='600',
                      colore=C['nero'], bgColor=ORO, padding=10, borderRadius=999, align='center', fontFamily=MONO,
                      letterSpacing=1, entra='zoomIn', ritardo=700,
                      responsive={'mobile': {'fontSize': m(10), 'y': 50, 'x': 14, 'width': 72}}),
            K.livello('tp-s%d' % n, contenuto=testo, x=31, y=56, w=38, size=17, peso='400', colore=mix(C['chiaro'], 86),
                      lineHeight=1.65, align='center', ritardo=800,
                      responsive={'mobile': {'fontSize': m(14), 'y': 55.5, 'x': 10, 'width': 80}}),
            K.livello('tp-b%d' % n, tipo='button', contenuto='Prenota il rituale', x=36.5, y=71, w='auto', size=12, peso='600',
                      colore=C['nero'], bgColor=ORO, padding=16, letterSpacing=2, textTransform='uppercase', borderRadius=8,
                      ritardo=1100, buttonUrl=CONTATTI + '#prenota', hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'tablet': {'x': 30}, 'mobile': {'y': 68, 'x': 14, 'fontSize': m(11)}}),
            K.livello('tp-l%d' % n, tipo='button', contenuto='I trattamenti', x=52, y=71, w='auto', size=12, peso='600',
                      colore=C['chiaro'], bgColor='transparent', padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=8, borderWidth=1, borderColor=mix(C['chiaro'], 50), ritardo=1250, buttonUrl=TRATTAMENTI,
                      hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                      responsive={'tablet': {'x': 52}, 'mobile': {'y': 73, 'x': 14, 'fontSize': m(11)}}),
        ]
        sl.append(K.diapositiva('tp-%d' % n, K.sfondo_video(video, overlay=0.2, colore=C['nero']), livelli, durata=9000,
                                tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', durata_trans=1400,
                       showArrows=False, showDots=False, showTabs=True, tabPosition='bottom', parallax=False,
                       autoplaySpeed=9000, progressBarColor=ORO, navColor=C['chiaro'],
                       heightMobile={'mode': 'px', 'value': 1400})


def percorso(t):
    fasi = [('01', '38°', 'Il tepidario', 'La sala tiepida, sdraiati sulla pietra: il corpo si abitua.', 'pexels-7598373.jpg'),
            ('02', '45°', 'Il calidario', 'Il vapore all’eucalipto, poi l’acqua fredda della fontana.', 'pexels-3079492.jpg'),
            ('03', '40°', 'Il gommage', 'Il sapone nero e il guanto di kessa, sulla pietra calda.', 'pexels-38911900.jpg'),
            ('04', '24°', 'Il riposo', 'Il tè alla menta e i datteri, nella sala dei cuscini.', 'pexels-7391598.jpg')]
    return t.classic('step-timeline', {
        'items': [{'counter': n, 'tag_text': g, 'tag_dot_color': ORO, 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['sala'], 'media_color': ORO, 'pre_title': '',
                   'title': ti, 'title_accent': '', 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': '', 'footer_label': '', 'separator_text': ''}
                  for n, g, ti, d, f in fasi],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': ORO, 'timeline_dot_size': 10,
        'timeline_height': 1, 'timeline_margin_bottom': 40, 'counter_font_family': 'heading', 'counter_size': 40,
        'counter_color': ORO, 'counter_italic': False, 'counter_weight': '400', 'tag_size': 11, 'tag_color': C['tenue'],
        'media_aspect_ratio': '4/5', 'media_object_position': 'center center', 'media_radius': radius(8),
        'media_radius_hover': radius(8), 'media_shadow': 'none', 'show_media_label': False, 'pre_title_size': 11,
        'pre_title_color': ORO, 'title_font_family': 'heading', 'title_size': 24, 'title_weight': '400',
        'title_color': C['testo'], 'description_color': C['tenue']}, style={})


def trattamenti(t):
    voci = [('Il rituale', 'Le quattro sale, il gommage e il tè · due ore', '65 €', 'pexels-38911900.jpg', 'Il più scelto'),
            ('Rituale e massaggio', 'Il rituale e cinquanta minuti di massaggio all’olio d’argan', '120 €', 'pexels-6628646.jpg', ''),
            ('Il rituale per due', 'In coppia, con la sala del riposo riservata', '140 €', 'pexels-7391598.jpg', ''),
            ('Massaggio ai piedi', 'Trenta minuti, con l’olio caldo', '45 €', 'pexels-37719548.jpg', ''),
            ('Il tè e i dolci', 'Per chi aspetta, nella sala del riposo', '9 €', 'pexels-30906049.jpg', '')]
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in voci],
        'separator_style': 'dotted', 'separator_color': mix(ORO, 35), 'title_color': C['testo'], 'price_color': ORO,
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': ORO, 'badge_color': C['nero'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': ORO, 'author_color': C['tenue'], 'quote_accent_color': ORO, 'quote_font': 'heading',
        'quote_size': 36, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': ORO}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Tepidario · la Kalsa, Palermo', ['Un palazzo del Quattrocento,', 'quattro sale di vapore.'],
                                size=56, layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Le sale erano le cantine di un palazzo della Kalsa. Le abbiamo rivestite di pietra e '
                                'maiolica, abbiamo acceso il vapore e lasciato le volte com’erano. Si entra in dodici alla '
                                'volta, si esce quando si vuole.', size=17, color=C['testo']),
                      K.button(t, 'Il rituale', RITUALE, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('4', 'Sale'), ('12', 'Persone alla volta'), ('2 ore', 'Il rituale'), ('45°', 'La sala più calda')])],
              style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il percorso', ['Quattro sale,', 'dal tiepido al caldo.'], size=56)]),
        t.one([percorso(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_LUMI, 'Le candele', 'Qui il tempo <em>non si guarda.</em>',
                             'Niente orologi alle pareti e il telefono resta nell’armadietto: due ore che sembrano un '
                             'pomeriggio intero.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'I trattamenti', ['Il rituale,', 'e quello che si aggiunge.'], size=52, layout='stack',
                                size_mobile=34),
                      K.button(t, 'Tutti i trattamenti', TRATTAMENTI, pad_top=14)], wm='2-5', ws='1-1'),
               t.col([trattamenti(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Sono entrata stanca morta dopo una settimana di lavoro. Dopo il gommage ero un’altra persona, dopo il tè non '
           'volevo più uscire. Mi hanno lasciata stare: è questo il lusso.', 'Giulia M.', 'Rituale e massaggio')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_CANDELE, 'Prenota', 'Il vostro posto <em>nel vapore.</em>',
                          'Dodici persone alla volta: conviene prenotare, soprattutto il fine settimana.',
                          'Prenota il rituale', CONTATTI + '#prenota', overlay=40, video=True))
    return out


def build_rituale():
    t = Tpl(ID, 'il-rituale')
    out = [K.intro(t, 'Il rituale', ['Due ore,', 'quattro sale, nessuna fretta.'],
                   'Il rituale dell’hammam come si faceva a Palermo mille anni fa, quando qui c’erano i bagni della città '
                   'araba.', bg=K.photo_bg('pexels-3079492.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Sala per sala', ['Dal tiepido', 'al tè.'], size=56)]),
        t.one([percorso(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-38911900.jpg', 'La sala con la volta e i lucernari a stella', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'La volta', ['I lucernari', 'a stella.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'La luce entra dalle stelle scavate nella volta, come negli hammam di Damasco e di Granada: '
                                'di giorno disegna dei raggi nel vapore, di sera ci sono le candele.', size=17),
                      K.specs(t, [('Le volte', 'del Quattrocento'), ('La pietra', 'di Billiemi, scaldata da sotto'),
                                  ('Le maioliche', 'di Santo Stefano di Camastra')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di venire.', [
        ('Che cosa porto?', 'Niente: accappatoio, telo, ciabatte e il costume monouso li diamo noi.'),
        ('Si può venire in coppia?', 'Sì, sempre; il mercoledì l’hammam è solo per le donne.'),
        ('E se sono incinta?', 'Il calidario no. Il tepidario e il riposo sì, con un massaggio dolce: diteci tutto.'),
    ], 'tp-faq'))
    return out


def build_trattamenti():
    t = Tpl(ID, 'trattamenti')
    out = [K.intro(t, 'I trattamenti', ['Il rituale,', 'il massaggio, il riposo.'],
                   'Tutti i trattamenti comprendono il percorso delle quattro sale e il tè alla menta.',
                   bg=K.video_bg(V_MASSAGGIO), size=92)]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['Per uno,', 'per due.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'I massaggi li fanno Samira e Rosalia, che hanno imparato a Fès. L’olio d’argan arriva da una '
                                'cooperativa di donne della valle del Souss.', size=16)], wm='2-5', ws='1-1'),
               t.col([trattamenti(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le formule', ['Per uno,', 'per due.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Il rituale', '65', 'due ore', 'Le quattro sale\nIl gommage col sapone nero\nIl tè alla menta', False, ''),
            ('Rituale e massaggio', '120', 'tre ore', 'Tutto il rituale\nCinquanta minuti di massaggio\nI datteri e i dolci di '
             'mandorla', True, 'il più regalato'),
            ('Per due', '140', 'due ore, in coppia', 'Il rituale insieme\nLa sala del riposo riservata\nUna bottiglia di '
             'acqua di rose', False, ''),
        ], 'Regala un rituale', CONTATTI + '#prenota', nota='Il buono regalo vale sei mesi e si spedisce in una scatola di '
           'cedro.'),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_TE, 'Il tè', 'La menta, <em>versata dall’alto.</em>',
                             'Si versa da lontano perché faccia la schiuma: il primo bicchiere è amaro, il terzo dolce.',
                             overlay=40))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Prenotate,', 'il vapore è pronto.'],
                   'Per il rituale, un massaggio, un buono regalo o l’hammam per un gruppo.',
                   bg=K.photo_bg('pexels-7598373.jpg'), size=92)]
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Il trattamento', 'trattamento', True, opzioni='Il rituale\nRituale e massaggio\nPer due\nUn buono '
                                                                                'regalo'),
             K.campo('date', 'Il giorno', 'giorno', True), K.campo('select', 'L’ora', 'ora', opzioni='11:00\n13:30\n16:00\n18:30\n20:00'),
             K.campo('textarea', 'Una nota', 'note', larghezza='1-1', segnaposto='Un’allergia, una gravidanza, un regalo.')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Il giorno e l’ora,', 'vi confermiamo subito.'],
                                'Confermiamo con un messaggio entro un’ora. Si può disdire fino al giorno prima.', VOCI, campi,
                                'Tepidario — prenotazione', 'Prenota', 'Ricevuto. Vi confermiamo entro un’ora.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Alla Kalsa,', 'dietro la Magione.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Via della Vetreria 21, Palermo'), ('Orari', 'martedì — domenica 11 — 22'),
                                  ('Il mercoledì', 'solo donne')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Tepidario · hammam', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · sala vuota', color=ORO, align='center'),
                       K.headline(t, 'Qui c’è <em>solo vapore.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=ORO),
                       K.text(t, 'La pagina si è sciolta nel caldo. Torniamo all’ingresso.', size=17, align='center',
                              color=mix(C['chiaro'], 84)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CANDELE, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'Sotto la cupola',
    ['hammam', 'spa', 'benessere', 'massaggi', 'gommage', 'palermo', 'scuro', 'slider pro'],
    'Un hammam in un palazzo del Quattrocento, pietra bruna e oro delle candele: in apertura la cupola vista dal '
    'basso, grande quanto la pagina, coi lucernari a stella che brillano e girano mentre si scorre e il titolo al '
    'centro; il percorso delle quattro sale, i trattamenti e la prenotazione.',
    [('Il rituale', RITUALE), ('I trattamenti', TRATTAMENTI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('il-rituale', 'Il rituale', 'il-rituale'), ('trattamenti', 'I trattamenti', 'trattamenti'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Jost\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Marcellus\', \'Georgia\', serif',
    '\'Spline Sans Mono\', ui-monospace, Menlo, monospace', ['Marcellus', 'Jost', 'Spline Sans Mono'], corsivo_di=[],
    peso_titoli='400', raggio=8, grana=5, titoli_line='1.1', titoli_spazio='0')
THEME['cursor']['style'] = 'vapore'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-rituale.json': build_rituale(), 'trattamenti.json': build_trattamenti(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
