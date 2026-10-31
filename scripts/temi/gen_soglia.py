# -*- coding: utf-8 -*-
"""Genera il tema «Soglia» (id soglia): una galleria d'arte contemporanea all'Isola, a Milano: quattro mostre all'anno,
gli artisti della galleria, le visite guidate e le aperture serali.

Tema chiaro, il bianco delle pareti: carta, nero, blu oltremare, arancio segnale. Titoli e testo in Host Grotesk, dati in
Red Hat Mono.

Apertura: «la porta». Lo slider resta fermo in cima mentre si scorre: due pareti bianche coprono la sala, con una fessura
in mezzo da cui si vede il video dell'installazione; sulle pareti il titolo, diviso. Scorrendo le pareti si aprono come
una porta scorrevole e la sala appare intera, poi entrano la mostra in corso e i pulsanti. Cursore: la lente.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'soglia'
NAME = 'Soglia'

HEX = {
    'primary': '#1F3FFF', 'primary_contrast': '#FFFFFF', 'secondary': '#E8462A', 'secondary_contrast': '#FFFFFF',
    'accent': '#F2C14E', 'dark': '#0A0A0A', 'light': '#FFFFFF', 'muted': '#E8E6E1', 'muted_contrast': '#141414',
    'text': '#141414', 'text_muted': '#5C5A55', 'background': '#F5F4F1', 'border': '#D6D3CC', 'link': '#1F3FFF',
}

HOME, MOSTRE, ARTISTI, VISITA = '{{PAGINA:home}}', '{{PAGINA:mostre}}', '{{PAGINA:artisti}}', '{{PAGINA:visita}}'
MAIL, PHONE, PHONE_TEL = 'galleria@soglia.example', '+39 02 0000 8812', 'tel:+390200008812'
LAT, LNG = '45.4890', '9.1873'

V_STRISCE, V_LAMPADINE, V_ARGILLA, V_ROSA, V_SALA, V_VERDE = ('pexels-video-6492173', 'pexels-video-3051336',
                                                             'pexels-video-6719630', 'pexels-video-6214486',
                                                             'pexels-video-36229200', 'pexels-video-9738008')
PHOTOS = [
    'pexels-6727760.jpg', 'pexels-20313879.jpg', 'pexels-7859044.jpg', 'pexels-11843513.jpg', 'pexels-36470786.jpg',
    'pexels-7859047.jpg', 'pexels-8947357.jpg', 'pexels-30925664.jpg', 'pexels-13382069.jpg', 'pexels-7325760.jpg',
    'pexels-30874918.jpg', 'pexels-6714312.jpg', 'pexels-27799708.jpg', 'pexels-33317327.jpg', 'pexels-8381961.jpg',
    'pexels-6732467.jpg', 'pexels-39518605.jpg', 'pexels-8123018.jpg', 'pexels-9756574.jpg', 'pexels-16207593.jpg',
    'pexels-25859708.jpg',
]
VIDEOS = [V_STRISCE, V_LAMPADINE, V_ARGILLA, V_ROSA, V_SALA, V_VERDE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='600', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
SEGNALE, GIALLO = C['sec'], C['acc2']
MONO = 'Red Hat Mono'
VOCI = [('map-pin', 'Via Pastrengo 14 · Milano Isola', VISITA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Soglia', logo_size='22', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Soglia', 'Galleria d’arte contemporanea all’Isola, dal 2009. Quattro mostre all’anno, otto artisti, '
                   'l’apertura del giovedì sera fino alle 22. L’ingresso è libero.',
                   [('Le mostre', MOSTRE), ('Gli artisti', ARTISTI), ('La visita', VISITA), ('Privacy', '/privacy-policy/')],
                   'La galleria', 'Da martedì a sabato 11 — 19<br>Il giovedì fino alle 22', VOCI,
                   '© 2026 Soglia · Milano · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=150, fondo=C['sala'])


# ── L'apertura: la porta ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0


def apertura(t):
    m = K.mob
    sx = {'inizio': 8, 'fine': 68, 'da': {}, 'a': {'x': -760}}
    dx = {'inizio': 8, 'fine': 68, 'da': {}, 'a': {'x': 760}}
    fine = lambda i, f: {'inizio': i, 'fine': f, 'da': {'y': 40, 'o': 0}, 'a': {}}
    livelli = [
        # dopo: il velo dal basso sotto i testi della mostra
        K.livello('so-velo', tipo='shape', x=0, y=34, w=100, h=66, entra='fadeIn', ritardo=0,
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 35},
                  scrollMove={'inizio': 60, 'fine': 80, 'da': {'o': 0}, 'a': {'o': 100}},
                  responsive={'mobile': {'y': 52, 'height': 48}}),
        K.livello('so-in', contenuto='In mostra fino al 21 dicembre', x=6, y=46, w='auto', size=13, peso='600',
                  colore=C['nero'], bgColor=GIALLO, padding=9, letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                  entra='fadeIn', ritardo=0, scrollMove=fine(66, 80),
                  responsive={'mobile': {'x': 7, 'y': 60, 'fontSize': m(10)}}),
        K.livello('so-mostra', contenuto='Marta Bellini<br><span style="color:var(--olo-color-accent)">Luce ferma</span>',
                  tag='p', x=6, y=51.5, w=60, size=82, peso='600', colore=C['chiaro'], lineHeight=1.0, letterSpacing=-3,
                  entra='fadeIn', ritardo=0, scrollMove=fine(68, 84),
                  responsive={'mobile': {'fontSize': m(38), 'y': 63.5, 'x': 7, 'width': 86}}),
        K.livello('so-b', tipo='button', contenuto='La mostra', x=66, y=70, w='auto', size=12, peso='700', colore=C['chiaro'],
                  bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase', borderRadius=0, entra='fadeIn',
                  ritardo=0, buttonUrl=MOSTRE, hover={'bgColor': SEGNALE, 'duration': 250}, scrollMove=fine(74, 90),
                  responsive={'tablet': {'x': 58}, 'mobile': {'y': 80, 'x': 7, 'fontSize': m(11)}}),
        K.livello('so-l', tipo='button', contenuto='Prenota la visita', x=77.5, y=70, w='auto', size=12, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=16, letterSpacing=2, textTransform='uppercase',
                  borderRadius=0, borderWidth=1, borderColor=mix(C['chiaro'], 70), entra='fadeIn', ritardo=0,
                  buttonUrl=VISITA + '#prenota', hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'],
                                                         'duration': 250},
                  scrollMove=fine(76, 92), responsive={'tablet': {'x': 75}, 'mobile': {'y': 85, 'x': 7, 'fontSize': m(11)}}),
        # le due pareti, con la fessura in mezzo
        K.livello('so-parete-s', tipo='shape', x=0, y=0, w=48.6, h=100, bgColor=C['fondo'], entra='fadeIn', ritardo=0,
                  durata=300, boxShadow={'x': 14, 'y': 0, 'blur': 40, 'spread': -10, 'color': mix(C['nero'], 45)},
                  scrollMove=sx, responsive={'mobile': {'width': 47}}),
        K.livello('so-parete-d', tipo='shape', x=51.4, y=0, w=48.6, h=100, bgColor=C['fondo'], entra='fadeIn', ritardo=0,
                  durata=300, boxShadow={'x': -14, 'y': 0, 'blur': 40, 'spread': -10, 'color': mix(C['nero'], 45)},
                  scrollMove=dx, responsive={'mobile': {'x': 53, 'width': 47}}),
        # sulle pareti: il titolo diviso, e due righe
        K.livello('so-o', contenuto='Soglia · galleria d’arte contemporanea', x=4, y=24, w=42, size=12, peso='600',
                  colore=C['acc'], letterSpacing=3, textTransform='uppercase', fontFamily=MONO, align='right', ritardo=200,
                  scrollMove=sx, responsive={'mobile': {'fontSize': m(9), 'y': 30, 'x': 4, 'width': 40}}),
        K.livello('so-t1', contenuto='La soglia', tag='h1', x=2, y=29, w=44, size=104, peso='600', colore=C['testo'],
                  lineHeight=1.0, letterSpacing=-4, align='right', entra='maskFromBottom', ritardo=300, durata=1100,
                  scrollMove=sx, responsive={'mobile': {'fontSize': m(34), 'y': 33, 'x': 2, 'width': 42}}),
        K.livello('so-t2', contenuto='si attraversa.', tag='p', x=54, y=43, w=44, size=104, peso='600', colore=C['acc'],
                  lineHeight=1.0, letterSpacing=-4, entra='maskFromBottom', ritardo=550, durata=1100,
                  scrollMove=dx, responsive={'mobile': {'fontSize': m(34), 'y': 37, 'x': 56, 'width': 42}}),
        K.livello('so-s', contenuto='Quattro mostre all’anno, ingresso libero. Il giovedì aperti fino alle 22.', x=8, y=46,
                  w=38, size=16, peso='400', colore=C['tenue'], lineHeight=1.6, align='right', ritardo=800, scrollMove=sx,
                  responsive={'mobile': {'fontSize': m(12), 'y': 42, 'x': 4, 'width': 40}}),
        K.livello('so-scorri', contenuto='Scorrete: la porta si apre →', x=54, y=60, w=30, size=12, peso='500',
                  colore=C['tenue'], letterSpacing=1, fontFamily=MONO, ritardo=1000, scrollMove=dx,
                  responsive={'mobile': {'fontSize': m(10), 'y': 46, 'x': 56, 'width': 40}}),
    ]
    sl = [K.diapositiva('so-1', K.sfondo_video(V_STRISCE, overlay=0.1, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


MOSTRE_LISTA = [  # date, artista e titolo, descrizione, stato
    ('24.10 — 21.12', 'Marta Bellini · Luce ferma', 'Pittura · quattordici tele sulla luce di Milano d’inverno', 'In corso'),
    ('16.01 — 14.03', 'Davide Orsi · Le cose lente', 'Pittura e disegno · nature morte del tempo presente', 'Prossima'),
    ('27.03 — 23.05', 'Collettiva · Argilla', 'Scultura · cinque artisti e la terra cruda', 'Prossima'),
    ('12.09 — 18.10', 'Ines Varga · Strisce', 'Installazione · la luce che attraversa la sala', '2026'),
    ('05.06 — 26.07', 'Collettiva · Il bianco', 'Pittura · quindici modi di non dipingere', '2026'),
]


def mostre(t, voci=MOSTRE_LISTA):
    return t.classic('worklist', {
        'items': [{'number': d, 'title': ti, 'category': c, 'year': s, 'link_url': MOSTRE} for d, ti, c, s in voci],
        'divider_color': C['testo'], 'row_padding_y': 26, 'row_hover_bg': mix(C['acc'], 6), 'hover_indent': 28,
        'number_color': C['acc'], 'number_size': 14, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 40, 'title_weight': '600', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': SEGNALE, 'year_size': 15, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': MONO}, style={})


def opere(t):
    voci = [('Luce ferma n. 3', 'Olio su tela · 120 × 160 cm', 'Marta Bellini, 2026', 'pexels-7325760.jpg'),
            ('Inverno, mattina', 'Olio e smalto · 100 × 140 cm', 'Marta Bellini, 2026', 'pexels-30874918.jpg'),
            ('Il sole basso', 'Acrilico su tela · 90 × 120 cm', 'Marta Bellini, 2025', 'pexels-13382069.jpg'),
            ('Rumore', 'Tecnica mista · 80 × 100 cm', 'Davide Orsi, 2025', 'pexels-30925664.jpg')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': a, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': MOSTRE,
                   'color': C['acc']} for k, (n, d, a, f) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'square', 'number_color': C['acc'],
        'number_hover_color': SEGNALE, 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 52, 'name_weight': '600', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['acc'],
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(C['acc'], 5), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 300, 'peek_ratio': '4/3', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': SEGNALE}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Soglia · Isola, dal 2009', ['Una sala bianca,', 'quattro mostre all’anno.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'La galleria era una tipografia: una sala sola, alta sei metri, la luce dai lucernari. Ci '
                                'lavorano otto artisti; ogni mostra dura due mesi e si inaugura il giovedì sera, con la porta '
                                'aperta a chi passa.', size=17, color=C['testo']),
                      K.button(t, 'Gli artisti', ARTISTI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('62', 'Mostre'), ('8', 'Artisti'), ('6 m', 'Di soffitto'), ('0 €', 'L’ingresso')])],
              style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In mostra', ['Luce ferma,', 'quattordici tele.'], size=56,
                         tagline='Passate su un’opera: si affaccia. Le opere si possono acquistare.')]),
        t.one([opere(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_VERDE, 'Lo studio', 'Una tela <em>comincia così.</em>',
                             'Prima un colore solo, steso largo. Il resto viene dopo, a volte dopo mesi.', overlay=40))
    out.append(t.section([
        t.one([K.sechead(t, 'Il calendario', ['Le prossime', 'mostre.'], size=56,
                         tagline='Ogni mostra si inaugura il giovedì alle 19. Non serve invito.')]),
        t.one([mostre(t, MOSTRE_LISTA[:3])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Sono entrata per ripararmi dalla pioggia. Sono rimasta un’ora davanti a una tela bianca con una riga gialla. '
           'Adesso vengo a ogni inaugurazione.', 'Chiara D.', 'Visitatrice del giovedì')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_LAMPADINE, 'Il giovedì', 'Aperti <em>fino alle 22.</em>',
                          'Il giovedì sera la galleria resta aperta, con un bicchiere e l’artista in sala.', 'La visita', VISITA,
                          overlay=40, video=True))
    return out


def build_mostre():
    t = Tpl(ID, 'mostre')
    out = [K.intro(t, 'Le mostre', ['Due mesi,', 'una sala, un artista.'],
                   'Ogni mostra nasce per la sala: le opere sono pensate per i sei metri di soffitto e la luce dei '
                   'lucernari.', bg=K.photo_bg('pexels-20313879.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il calendario', ['Adesso,', 'prossimamente, prima.'], size=56)]),
        t.one([mostre(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La sala', ['Com’era,', 'mostra dopo mostra.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-6727760.jpg', 'Un’installazione di luce bianca nella sala vuota', 'Ines Varga', 'Strisce'),
                               ('pexels-11843513.jpg', 'Una sala grigia con le fotografie e una panca', 'Collettiva', 'Il bianco'),
                               ('pexels-36470786.jpg', 'Una stanza rossa con una porta luminosa', 'Installazione', 'Rosso'),
                               ('pexels-33317327.jpg', 'Una scultura di rami davanti ai quadri', 'Collettiva', 'Argilla'),
                               ('pexels-8123018.jpg', 'Fotografie appese in griglia e uno sgabello', 'Il progetto', 'Archivio')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SALA, 'La visita', 'Si entra, <em>si guarda, si esce diversi.</em>',
                             'Nessun percorso obbligato: la sala è una, le opere si guardano nell’ordine che volete.',
                             overlay=40))
    return out


def build_artisti():
    t = Tpl(ID, 'artisti')
    out = [K.intro(t, 'Gli artisti', ['Otto artisti,', 'seguiti per anni.'],
                   'Lavoriamo con pochi artisti e li seguiamo a lungo: una mostra ogni due anni, le fiere, i libri.',
                   bg=K.video_bg(V_ROSA), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'In galleria', ['Due di loro,', 'al lavoro.'], size=56)]),
        K.squadra(t, [
            ('pexels-8381961.jpg', 'Marta Bellini', 'Pittrice, 1984', 'Dipinge la luce delle città d’inverno. In mostra '
             'fino al 21 dicembre con «Luce ferma».', 'center 30%'),
            ('pexels-6732467.jpg', 'Davide Orsi', 'Pittore, 1979', 'Nature morte del tempo presente: le cose che teniamo sul '
             'tavolo. A gennaio con «Le cose lente».', 'center 30%'),
        ], ratio='4/5', size='340', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Da vicino', ['La materia', 'delle opere.'], size=56)]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5', lightbox=True)], wm='1-4', ws='1-2') for f, alt in (
            ('pexels-9756574.jpg', 'Il colore spesso, giallo e blu'), ('pexels-16207593.jpg', 'La tavolozza piena di colori'),
            ('pexels-25859708.jpg', 'Bianco e azzurro graffiati'), ('pexels-39518605.jpg', 'I pennelli in un vaso'))],
              layout='25-25-25-25', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_ARGILLA, 'L’argilla', 'La scultura <em>nasce bagnata.</em>',
                             'In primavera la collettiva «Argilla»: cinque artisti e la terra cruda.', overlay=40))
    return out


def build_visita():
    t = Tpl(ID, 'visita')
    out = [K.intro(t, 'La visita', ['L’ingresso è libero,', 'la porta è aperta.'],
                   'Da martedì a sabato, il giovedì fino alle 22. Per i gruppi e le scuole, la visita guidata con chi ha '
                   'curato la mostra.', bg=K.photo_bg('pexels-7859044.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le visite guidate', ['Con chi ha curato', 'la mostra.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Il giovedì', '0', 'alle 19, senza prenotare', 'Il giro della mostra in mezz’ora\nL’artista in sala\nUn bicchiere',
             False, ''),
            ('Per i gruppi', '120', 'fino a quindici persone', 'Un’ora con la curatrice\nIl giorno che volete\nIl catalogo '
             'in regalo', True, 'la più chiesta'),
            ('Per le scuole', '60', 'una classe', 'Un’ora, adatta all’età\nUn laboratorio con i materiali\nDal martedì al '
             'venerdì mattina', False, ''),
        ], 'Prenota', VISITA + '#prenota', nota='Le opere in mostra si possono acquistare: chiedete il listino in sala.'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='prezzi', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'La visita', 'visita', True, opzioni='Per i gruppi\nPer le scuole\nUna domanda su un’opera'),
             K.campo('date', 'Il giorno', 'giorno'), K.campo('text', 'Quanti siete', 'persone'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Il giorno e quanti siete,', 'vi aspettiamo.'],
                                'Vi rispondiamo entro un giorno con la conferma.', VOCI, campi, 'Soglia — visita', 'Prenota',
                                'Ricevuto. Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['All’Isola,', 'via Pastrengo 14.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Orari', 'martedì — sabato 11 — 19'), ('Il giovedì', 'fino alle 22'),
                                  ('Metro', 'Isola (M5), tre minuti a piedi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Soglia · galleria', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · sala vuota', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui c’era <em>un’opera.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'È stata venduta, o forse non c’è mai stata. Torniamo all’ingresso.', size=17,
                              align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_STRISCE, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Cultura e formazione', 'La porta che si apre',
    ['galleria d’arte', 'arte contemporanea', 'mostre', 'artisti', 'milano', 'chiaro', 'slider pro'],
    'Una galleria d’arte all’Isola, il bianco delle pareti e il blu oltremare: in apertura due pareti bianche col titolo '
    'diviso e una fessura sul video dell’installazione, che scorrendo si aprono come una porta e mostrano la sala e la '
    'mostra in corso; le opere, il calendario, gli artisti e le visite guidate.',
    [('Le mostre', MOSTRE), ('Gli artisti', ARTISTI), ('La visita', VISITA)],
    [('home', 'Home', None), ('mostre', 'Le mostre', 'mostre'), ('artisti', 'Gli artisti', 'artisti'),
     ('visita', 'La visita', 'visita')],
    HEX, '\'Host Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Host Grotesk\', system-ui, sans-serif',
    '\'Red Hat Mono\', ui-monospace, Menlo, monospace', ['Host Grotesk', 'Red Hat Mono'], corsivo_di=[],
    peso_titoli='600', raggio=0, grana=0, titoli_line='1.0', titoli_spazio='-0.04em')
THEME['cursor']['style'] = 'lente'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'mostre.json': build_mostre(), 'artisti.json': build_artisti(), 'visita.json': build_visita(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
