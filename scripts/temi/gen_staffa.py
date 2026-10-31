# -*- coding: utf-8 -*-
"""Genera il tema «Staffa» (id staffa): un centro equestre in Maremma, sulle colline di Scansano: lezioni per adulti e
bambini, passeggiate a cavallo fra le sugherete e fino al mare, il pony club e la pensione per i cavalli.

Tema chiaro, paglia e cuoio: crema di fieno, rosso cuoio, verde macchia e oro. Titoli in Young Serif, testo in Instrument
Sans, dati in Martian Mono.

Apertura: «il ventaglio». Lo slider resta fermo in cima mentre si scorre, sul video dei cavalli che corrono nella
polvere al tramonto: cinque foto sono impilate al centro come un mazzo di carte e scorrendo si aprono a ventaglio (ognuna
ruota attorno allo stesso perno, sotto), poi compaiono le quattro cose che si fanno al maneggio e i pulsanti. Il mouse
inclina le foto. Cursore: le impronte.
"""
import math
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'staffa'
NAME = 'Staffa'

HEX = {
    'primary': '#8A3B12', 'primary_contrast': '#FFFFFF', 'secondary': '#3E5A3A', 'secondary_contrast': '#FFFFFF',
    'accent': '#D9A441', 'dark': '#1B140E', 'light': '#FFFFFF', 'muted': '#ECE3D3', 'muted_contrast': '#2B2118',
    'text': '#2B2118', 'text_muted': '#665848', 'background': '#F6F0E4', 'border': '#DED2BD', 'link': '#8A3B12',
}

HOME, LEZIONI, PASSEGGIATE, CAVALLI, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:lezioni}}', '{{PAGINA:passeggiate}}',
                                                 '{{PAGINA:i-cavalli}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'maneggio@staffa.example', '+39 0564 000 733', 'tel:+390564000733'
LAT, LNG = '42.6772', '11.3317'

V_TRAMONTO, V_CRINIERA, V_MANEGGIO, V_SENTIERO, V_CAMPI, V_OCCHIO = ('pexels-video-27979821', 'pexels-video-6876680',
                                                                    'pexels-video-30170705', 'pexels-video-8624895',
                                                                    'pexels-video-18319293', 'pexels-video-19877526')
PHOTOS = [
    'pexels-17637176.jpg', 'pexels-33687425.jpg', 'pexels-12314846.jpg', 'pexels-28538730.jpg', 'pexels-4894993.jpg',
    'pexels-7882500.jpg', 'pexels-4894938.jpg', 'pexels-33894147.jpg', 'pexels-13133680.jpg', 'pexels-3149048.jpg',
    'pexels-5036596.jpg', 'pexels-7882504.jpg', 'pexels-37106917.jpg', 'pexels-32413354.jpg',
]
VIDEOS = [V_TRAMONTO, V_CRINIERA, V_MANEGGIO, V_SENTIERO, V_CAMPI, V_OCCHIO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='400', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
ORO, MACCHIA = C['acc2'], C['sec']
TITOLI, MONO = 'Young Serif', 'Martian Mono'
VOCI = [('map-pin', 'Strada delle Sugherete 12 · Scansano (GR)', CONTATTI + '#maneggio'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Staffa', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Staffa', 'Centro equestre sulle colline di Scansano: lezioni, passeggiate fra le sugherete e fino al '
                   'mare, il pony club per i bambini e la pensione. Ventidue cavalli, quattro istruttori.',
                   [('Le lezioni', LEZIONI), ('Le passeggiate', PASSEGGIATE), ('I cavalli', CAVALLI), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'Il maneggio', 'Da martedì a domenica 8 — 19<br>Il lunedì i cavalli riposano', VOCI,
                   '© 2026 Staffa · Associazione sportiva dilettantistica · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=140,
                   fondo=C['sala'])


# ── L'apertura: il ventaglio ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
CARTE = ['pexels-4894993.jpg', 'pexels-33687425.jpg', 'pexels-37106917.jpg', 'pexels-5036596.jpg', 'pexels-13133680.jpg']
ANGOLI = [-28, -14, 0, 14, 28]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    cw, ch = 235.0, 300.0                      # la carta sul computer
    cx, cy, perno = 720.0, 420.0, 560.0        # il centro del mazzo e la distanza dal perno, sotto
    cwm, chm, cym, pernom = 220.0, 282.0, 760.0, 470.0
    livelli = [
        K.livello('st-cielo', tipo='shape', x=0, y=0, w=100, h=40, entra='fadeIn', ritardo=0, durata=600,
                  shapeGradient={'angle': 180, 'from': mix(C['nero'], 85), 'to': mix(C['nero'], 0), 'fromStop': 40},
                  responsive={'mobile': {'height': 36}}),
        K.livello('st-fondo', tipo='shape', x=0, y=72, w=100, h=28, entra='fadeIn', ritardo=0, durata=600,
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 35},
                  responsive={'mobile': {'y': 76, 'height': 24}}),
        K.livello('st-o', contenuto='Staffa · centro equestre in Maremma', x=20, y=8, w=60, size=12, peso='500', colore=ORO,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, align='center', ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 6, 'width': 88}}),
        K.livello('st-t', contenuto='Si parte al passo,<br><span style="color:var(--olo-color-accent)">si torna al galoppo.</span>',
                  tag='h1', x=10, y=12, w=80, size=66, peso='400', colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.06,
                  align='center', entra='maskFromBottom', ritardo=300, durata=1200,
                  responsive={'mobile': {'fontSize': m(31), 'y': 10.5, 'x': 5, 'width': 90}}),
    ]
    # le carte: impilate al centro, si aprono a ventaglio attorno al perno sotto il mazzo
    for k, (foto, ang) in enumerate(zip(CARTE, ANGOLI)):
        a = _pc(cx - cw / 2, cy - ch / 2, cw, ch, W, H)
        b = _pc(320 - cwm / 2, cym - chm / 2, cwm, chm, WM, HM)
        th = math.radians(ang)
        dx, dy = perno * math.sin(th), perno * (1 - math.cos(th))
        sbando = (k - 2) * 2.5          # nel mazzo le carte sono appena storte
        livelli.append(K.livello('st-carta%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a[0], y=a[1],
                                 w=a[2], h=a[3], borderRadius=10, borderWidth=6, borderColor=C['chiaro'], entra='fadeInUp',
                                 ritardo=200 + 120 * k, durata=900,
                                 boxShadow={'x': 0, 'y': 24, 'blur': 50, 'spread': -12, 'color': mix(C['nero'], 70)},
                                 parallax={'type': 'mouse', 'depth': 4 + k, 'tilt3d': True, 'scrollSpeed': 0.5},
                                 scrollMove={'inizio': 0, 'fine': 70, 'da': {'r': sbando},
                                             'a': {'x': round(dx), 'y': round(dy), 'r': ang}},
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    fine = lambda i, f: {'inizio': i, 'fine': f, 'da': {'y': 40, 'o': 0}, 'a': {}}
    livelli += [
        K.livello('st-b', tipo='button', contenuto='Prenota una lezione', x=36.5, y=86.5, w='auto', size=14, peso='600',
                  colore=C['nero'], bgColor=ORO, padding=16, borderRadius=999, entra='fadeIn', ritardo=0, buttonUrl=LEZIONI,
                  hover={'bgColor': C['chiaro'], 'duration': 250}, scrollMove=fine(62, 82),
                  responsive={'tablet': {'x': 30}, 'mobile': {'y': 88, 'x': 7, 'fontSize': m(12)}}),
        K.livello('st-l', tipo='button', contenuto='Le passeggiate', x=52, y=86.5, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), entra='fadeIn', ritardo=0, buttonUrl=PASSEGGIATE,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  scrollMove=fine(66, 86), responsive={'tablet': {'x': 52}, 'mobile': {'y': 88, 'x': 53, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('st-1', K.sfondo_video(V_TRAMONTO, overlay=0.25, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=True,
                       parallaxType='mouse', parallaxIntensity=6, scrollTimeline=True, scrollTimelineDistance=1600,
                       scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def cose(t):
    voci = [('pexels-4894993.jpg', 'Marta in sella nel maneggio coperto', 'Le lezioni', 'Dal primo passo al salto',
             'Lezioni in piccoli gruppi nel maneggio coperto e in quello all’aperto, per adulti e ragazzi.', LEZIONI),
            ('pexels-32413354.jpg', 'Tre cavalli in un prato fiorito', 'Le passeggiate', 'Fra le sugherete, fino al mare',
             'Da un’ora a una giornata intera, coi cavalli più tranquilli e una guida sempre davanti.', PASSEGGIATE),
            ('pexels-5036596.jpg', 'Un ragazzo col suo pony sulla sabbia', 'Il pony club', 'Dai sei anni',
             'Si impara a curare il pony prima di salirci: spazzola, zoccoli, sella. Poi si va.', LEZIONI + '#pony')]
    cols = []
    for foto, alt, occ, tit, testo, link in voci:
        cols.append(t.col([K.image(t, foto, alt, ratio='4/3', tilt=True), K.label(t, occ, color=C['acc'], pad_top=22),
                           K.headline(t, tit, size=28, pad_bottom=10), K.text(t, testo, size=15),
                           K.button(t, 'Scopri', link, pad_top=10)], wm='1-3', ws='1-1'))
    return t.row(cols, layout='33-33-33', gap=32)


ITINERARI = [  # durata, nome, descrizione, prezzo
    ('1 ora', 'Il giro delle sughere', 'Il bosco dietro il maneggio, al passo · per tutti', '35 €'),
    ('2 ore', 'Le vigne del Morellino', 'Fra i filari fino alla cantina, con l’assaggio · dai 12 anni', '60 €'),
    ('Mezza giornata', 'Il fiume Albegna', 'Il guado e il pranzo al sacco · chi sa trottare', '90 €'),
    ('Una giornata', 'Fino al mare', 'Le colline, la pineta e il galoppo sulla spiaggia · chi sa galoppare', '160 €'),
    ('Al tramonto', 'Il crinale', 'Due ore col sole basso e la cena in aia · per tutti', '75 €'),
]


def itinerari(t, voci=ITINERARI):
    return t.classic('worklist', {
        'items': [{'number': d, 'title': n, 'category': c, 'year': p, 'link_url': CONTATTI + '#scrivici'} for d, n, c, p in voci],
        'divider_color': C['filo'], 'row_padding_y': 24, 'row_hover_bg': mix(C['acc'], 6), 'hover_indent': 28,
        'number_color': C['acc'], 'number_size': 13, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 38, 'title_weight': '400', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': C['acc'], 'year_size': 18, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': MONO}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': ORO}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Staffa · Scansano, dal 1996', ['Ventidue cavalli,', 'quattrocento ettari di colline.'],
                                size=56, layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Il maneggio sta in un vecchio podere fra le sugherete, a mezz’ora dal mare. I cavalli vivono '
                                'al pascolo, in branco, e la sera tornano in scuderia. Si impara a cavalcare come si faceva '
                                'qui: prima si conosce il cavallo, poi ci si sale.', size=17, color=C['testo']),
                      K.button(t, 'I cavalli', CAVALLI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('22', 'Cavalli'), ('4', 'Istruttori'), ('30 km', 'Di sentieri'), ('6 anni', 'Per il pony club')])],
              style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Al maneggio', ['Tre modi', 'di stare a cavallo.'], size=56,
                         tagline='Passate sulle foto: si inclinano verso il puntatore.')]),
        t.one([cose(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_CRINIERA, 'Il branco', 'Al pascolo <em>tutto il giorno.</em>',
                             'Un cavallo che vive all’aperto, coi suoi, è un cavallo tranquillo: lo sentite appena salite.',
                             overlay=40))
    out.append(t.section([
        t.one([K.sechead(t, 'Le passeggiate', ['Dal bosco', 'al mare.'], size=56,
                         tagline='Passate su un itinerario: la freccia porta alla richiesta.')]),
        t.one([itinerari(t, ITINERARI[:4])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Non salivo a cavallo da trent’anni. Marta mi ha fatto spazzolare Brina per mezz’ora prima di montare. Al ritorno, '
           'sulla spiaggia, abbiamo galoppato: non lo dimentico più.', 'Lorenzo P.', 'Passeggiata fino al mare')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_SENTIERO, 'Le passeggiate', 'Il sentiero <em>fra le sughere.</em>',
                          'Ogni giorno alle nove e alle cinque, coi cavalli più tranquilli del branco.', 'Prenota la passeggiata',
                          PASSEGGIATE, overlay=40, video=True))
    return out


def build_lezioni():
    t = Tpl(ID, 'lezioni')
    out = [K.intro(t, 'Le lezioni', ['Prima il cavallo,', 'poi la sella.'],
                   'Lezioni in gruppi di quattro per livello, nel maneggio coperto e in quello all’aperto. La prima lezione è '
                   'di prova e si paga metà.', bg=K.video_bg(V_MANEGGIO), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Una lezione,', 'dieci, o il pony club.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Lezione', '30', 'un’ora, in gruppo', 'Quattro allievi per istruttore\nCasco e paraschiena compresi\nLa prima a '
             'metà prezzo', False, ''),
            ('Dieci lezioni', '260', 'da usare in tre mesi', 'Lo stesso istruttore\nIl passaggio di livello\nUna passeggiata '
             'in omaggio', True, 'la più scelta'),
            ('Pony club', '120', 'al mese, due volte a settimana', 'Dai sei ai dodici anni\nCurare il pony, poi montare\nLa '
             'gara di fine anno', False, ''),
        ], 'Prenota la prova', CONTATTI + '#scrivici', nota='Si monta dai sei anni; per il galoppo in passeggiata serve il '
           'secondo livello.'),
    ], padding='large', width='large', html_id='prezzi', style={'padding_top': 120, 'padding_bottom': 110}))
    corsi = [('base', 'Primo livello', C['acc'], 'Passo, trotto, la posizione.', 60),
             ('medio', 'Secondo livello', MACCHIA, 'Il galoppo e i primi salti.', 60),
             ('pony', 'Pony club', mix(ORO, 85, C['testo']), 'Dai sei ai dodici anni.', 60),
             ('salto', 'Salto ostacoli', mix(C['acc'], 55, C['testo']), 'Per chi galoppa sicuro.', 75)]
    piano = [(2, '17:00', '18:00', 'pony'), (2, '18:00', '19:00', 'base'), (3, '17:30', '18:30', 'medio'),
             (4, '17:00', '18:00', 'pony'), (4, '18:00', '19:15', 'salto'), (5, '18:00', '19:00', 'base'),
             (6, '09:30', '10:30', 'pony'), (6, '10:30', '11:30', 'base'), (6, '11:30', '12:30', 'medio')]
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'st-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c,
                     'istruttore': 'Marta Lenzi' if c in ('base', 'medio', 'salto') else 'Giada Corsi', 'sala': 'Maneggio',
                     'livello': 'tutti', 'posti': 4, 'nota': ''} for k, (g, a, b, c) in enumerate(piano)],
        'giorni': 'Martedì,Mercoledì,Giovedì,Venerdì,Sabato,Domenica', 'ora_inizio': '09:00', 'ora_fine': '20:00',
        'mostra_filtri': True, 'etichetta_tutti': 'Tutti', 'filtro_livello': False, 'filtro_istruttore': True,
        'testo_vuoto': 'Nessuna lezione con questi filtri.', 'testo_prenota': 'Prenota',
        'link_prenota': CONTATTI + '?corso={corso}&giorno={giorno}&ora={ora}#scrivici', 'colore_oggi': C['acc'],
        'colore_adesso': ORO, 'colore_tenue': C['testo'], 'giorno_font_family': 'heading', 'giorno_font_size': 20,
        'giorno_font_weight': '400', 'giorno_uppercase': False, 'corso_font_family': 'heading', 'corso_font_size': 17,
        'corso_font_weight': '400', 'ora_font_family': 'var(--olo-font-family-mono)', 'raggio_lezione': radius(10),
        'altezza_ora': 44, 'gap': 4, 'forma_filtri': 'pillola'}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'L’orario', ['Una settimana', 'di lezioni.'], size=56,
                         tagline='Filtrate per istruttrice; toccate una lezione per prenotarla.')]),
        t.one([orario]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='pony', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'della prima volta.', [
        ('Che cosa porto?', 'Pantaloni lunghi e scarpe con un po’ di tacco. Casco e paraschiena li diamo noi.'),
        ('Non sono mai salito a cavallo.', 'Si comincia da terra: come ci si avvicina, come si spazzola, come si sale.'),
        ('C’è un limite di peso?', 'Sì, per il bene dei cavalli: 95 chili. Scriveteci se siete vicini.'),
    ], 'st-faq'))
    return out


def build_passeggiate():
    t = Tpl(ID, 'passeggiate')
    out = [K.intro(t, 'Le passeggiate', ['Dal bosco,', 'fino al mare.'],
                   'Ogni giorno alle nove e alle cinque, con una guida davanti e i cavalli più tranquilli del branco. D’estate '
                   'anche al tramonto.', bg=K.video_bg(V_CAMPI), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Gli itinerari', ['Cinque giri,', 'cinque paesaggi.'], size=56,
                         tagline='Il livello serve per il trotto e il galoppo: al passo vengono tutti.')]),
        t.one([itinerari(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Lungo la strada', ['Quello che', 'si vede.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-37106917.jpg', 'Cavalli al pascolo in collina', 'Il pascolo', 'al mattino'),
                               ('pexels-32413354.jpg', 'Tre cavalli in un prato fiorito', 'I prati', 'in primavera'),
                               ('pexels-7882500.jpg', 'Un’amazzone col casco fra gli alberi', 'Il bosco', 'le sughere'),
                               ('pexels-3149048.jpg', 'La scuderia controluce al tramonto', 'Il ritorno', 'alla sera'),
                               ('pexels-4894938.jpg', 'Un’amazzone in sella nel recinto', 'Il recinto', 'prima di partire')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_OCCHIO, 'Il cavallo', 'Vi guarda <em>prima che saliate.</em>',
                             'Per questo si comincia sempre da terra: due minuti di spazzola e il cavallo vi conosce.', overlay=40))
    return out


def build_cavalli():
    t = Tpl(ID, 'i-cavalli')
    out = [K.intro(t, 'I cavalli', ['Ventidue cavalli,', 'ventidue caratteri.'],
                   'Maremmani quasi tutti, qualche arabo e tre pony shetland. Vivono al pascolo in branco e lavorano al '
                   'massimo due ore al giorno.', bg=K.photo_bg('pexels-12314846.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Alcuni del branco', ['Chi vi porterà', 'in giro.'], size=56)]),
        K.squadra(t, [
            ('pexels-33687425.jpg', 'Brina', 'Maremmana, 14 anni', 'La più tranquilla: porta chi sale per la prima volta.', 'center 30%'),
            ('pexels-17637176.jpg', 'Libeccio', 'Arabo, 11 anni', 'Veloce e curioso, solo per chi galoppa sicuro.', 'center 30%'),
            ('pexels-28538730.jpg', 'Tartufo', 'Maremmano, 9 anni', 'Il più goloso: in passeggiata si ferma a ogni cespuglio.', 'center 40%'),
        ], ratio='4/5', size='300', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-4894993.jpg', 'Marta Lenzi in sella nel maneggio coperto', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Le istruttrici', ['Marta e Giada,', 'nate in sella.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Marta Lenzi insegna qui da vent’anni e ha corso il palio di Scansano. Giada Corsi segue il '
                                'pony club: con lei i bambini imparano a curare il pony prima di salirci.', size=17),
                      K.specs(t, [('Istruttori', 'quattro, federali'), ('La pensione', 'box, paddock e pascolo'),
                                  ('Il veterinario', 'una volta a settimana')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Mia figlia aveva paura dei cavalli. Dopo un mese di pony club passa i sabati a spazzolare Pepe, e il cavallo la '
           'segue per il paddock.', 'Silvia T.', 'Mamma di una bambina del pony club')])], padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Venite a trovarci,', 'i cavalli aspettano.'],
                   'Per una lezione di prova, una passeggiata, il pony club o la pensione del vostro cavallo.',
                   bg=K.photo_bg('pexels-33894147.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa vi interessa', 'corso', True, param='corso',
                     opzioni='Una lezione di prova\nUna passeggiata\nIl pony club\nLa pensione'),
             K.campo('date', 'Il giorno', 'giorno'),
             K.campo('select', 'Il vostro livello', 'livello', opzioni='Mai salito a cavallo\nPasso e trotto\nAnche il galoppo'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1', segnaposto='Quanti siete, le età, il vostro peso '
                                                                                         'se superate i 90 chili.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Il giorno e il livello,', 'al cavallo pensiamo noi.'],
                                'Rispondiamo in giornata. Le passeggiate del sabato finiscono presto.', VOCI, campi,
                                'Staffa — richiesta', 'Invia', 'Ricevuto. Vi rispondiamo in giornata.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il maneggio', ['Sulle colline,', 'fra le sughere.'], size=52, layout='stack', size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Strada delle Sugherete 12, Scansano (GR)'),
                                  ('Orari', 'martedì — domenica 8 — 19'), ('Dal mare', 'mezz’ora da Marina di Grosseto')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Staffa · il maneggio', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='maneggio', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori dal recinto', color=K.acc_media, align='center'),
                       K.headline(t, 'Questo cavallo <em>è scappato.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Torna sempre alla scuderia. Anche voi.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_TRAMONTO, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'Il ventaglio',
    ['maneggio', 'equitazione', 'cavalli', 'passeggiate a cavallo', 'pony club', 'maremma', 'chiaro', 'slider pro'],
    'Un centro equestre in Maremma, paglia e cuoio: in apertura cinque foto impilate sul video dei cavalli al tramonto che '
    'scorrendo si aprono a ventaglio e si inclinano col mouse; le lezioni col calendario, le passeggiate fino al mare, i '
    'cavalli col loro carattere e il pony club.',
    [('Le lezioni', LEZIONI), ('Le passeggiate', PASSEGGIATE), ('I cavalli', CAVALLI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('lezioni', 'Le lezioni', 'lezioni'), ('passeggiate', 'Le passeggiate', 'passeggiate'),
     ('i-cavalli', 'I cavalli', 'i-cavalli'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Instrument Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Young Serif\', \'Georgia\', serif',
    '\'Martian Mono\', ui-monospace, Menlo, monospace', ['Young Serif', 'Instrument Sans', 'Martian Mono'], corsivo_di=[],
    peso_titoli='400', raggio=10, grana=3, titoli_line='1.06', titoli_spazio='0')
THEME['cursor']['style'] = 'impronte'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'lezioni.json': build_lezioni(), 'passeggiate.json': build_passeggiate(),
                        'i-cavalli.json': build_cavalli(), 'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
