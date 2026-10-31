# -*- coding: utf-8 -*-
"""Genera il tema «Arnia» (id arnia): un'apicoltura di montagna sull'Appennino modenese, cento arnie in sei postazioni,
mieli monoflora e l'adozione di un alveare (si adotta un'arnia, si ricevono le foto e il suo miele a settembre).

Tema chiaro, cera e miele: crema di cera, ambra, giallo polline e verde bosco. Titoli in Besley (anche corsivo), testo in
Work Sans, dati in Azeret Mono.

Apertura: Slider Pro «il favo», alto 16:9, una diapositiva per miele col suo video pieno (l'ape sui fiori, gli apicoltori
fra le arnie, i telaini del favo, il miele che cola nel vasetto) e un velo scuro solo dal lato del testo. A destra un favo
di sette celle tonde: al centro la foto del favo di quel miele, attorno le sei tinte della scala Pfund, dal bianco
all'ambra scura; la tinta del miele della diapositiva si accende e pulsa piano. Le diapositive scendono dall'alto
(slideDown). Cursore: l'orbita.
"""
import os
import sys
import math

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'arnia'
NAME = 'Arnia'

HEX = {
    'primary': '#A86A00', 'primary_contrast': '#FFFFFF', 'secondary': '#4E6B2F', 'secondary_contrast': '#FFFFFF',
    'accent': '#F2B705', 'dark': '#1E160A', 'light': '#FFFFFF', 'muted': '#F3EAD3', 'muted_contrast': '#2A1F0E',
    'text': '#2A1F0E', 'text_muted': '#6B5B3E', 'background': '#FBF6E9', 'border': '#E6D9B8', 'link': '#8F5A00',
}

HOME, MIELI, ADOTTA, APIARIO, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:mieli}}', '{{PAGINA:adotta-un-alveare}}',
                                          '{{PAGINA:apiario}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'api@arnia.example', '+39 0536 000 512', 'tel:+390536000512'
LAT, LNG = '44.3363', '10.8338'

V_FIORI, V_ARNIE, V_TELAINI, V_COLA, V_ORO = ('pexels-video-1310117', 'pexels-video-9144906', 'pexels-video-10516160',
                                              'pexels-video-31185864', 'pexels-video-31656449')
PHOTOS = [
    'pexels-4921856.jpg', 'pexels-10117717.jpg', 'pexels-30666799.jpg', 'pexels-20012002.jpg', 'pexels-3194327.jpg',
    'pexels-17682675.jpg', 'pexels-10725589.jpg', 'pexels-4247092.jpg', 'pexels-18925406.jpg', 'pexels-13625981.jpg',
    'pexels-4247163.jpg', 'pexels-38027227.jpg', 'pexels-38524703.jpg', 'pexels-32977983.jpg', 'pexels-14022964.jpg',
    'pexels-14509312.jpg', 'pexels-11334351.jpg', 'pexels-35382037.jpg',
]
VIDEOS = [V_FIORI, V_ARNIE, V_TELAINI, V_COLA, V_ORO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
POLLINE, BOSCO = C['acc2'], C['sec']
TITOLI, MONO = 'Besley', 'Azeret Mono'
VOCI = [('map-pin', 'Via dei Castagni 4 · Pavullo nel Frignano', CONTATTI + '#apiario'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Arnia', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Arnia', 'Cento arnie in sei postazioni sull’Appennino modenese, dai castagni ai prati d’alta quota. '
                   'Mieli monoflora estratti a freddo, l’adozione di un alveare, le visite all’apiario.',
                   [('I mieli', MIELI), ('Adotta un alveare', ADOTTA), ('L’apiario', APIARIO), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'Lo spaccio', 'Sabato 9 — 12:30<br>Da aprile a ottobre anche la domenica', VOCI,
                   '© 2026 Arnia · Società agricola · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=150,
                   fondo=C['sala'])


# ── L'apertura: la parola che si apre ──
# le sei tinte della scala Pfund, dal bianco all'ambra scura: (etichetta, colore di fondo, testo chiaro?)
TINTE = [('0 — 17 mm', mix(POLLINE, 18, C['chiaro']), False), ('18 — 34', mix(POLLINE, 42, C['chiaro']), False),
         ('35 — 50', mix(POLLINE, 75, C['chiaro']), False), ('51 — 85', POLLINE, False),
         ('86 — 114', mix(C['acc'], 80, C['nero']), True), ('oltre 114', mix(C['acc'], 62, C['nero']), True)]
def apertura(t):
    """La parola che si apre: la pagina color cera con «MIELE» enorme, e il miele che cola si vede solo dentro le lettere
    (livello di testo con la fusione «schiarisci»: il fondo bianco copre il video, le lettere scure lo lasciano vedere;
    le due scritte piccole hanno la fusione «moltiplica» per stare sopra la parola).
    Lo slider resta fermo in cima mentre si scorre: la parola si ingrandisce finché una lettera riempie lo schermo e il
    miele occupa tutto; poi entrano il titolo, il testo e i pulsanti."""
    m = K.mob
    entra = lambda inizio, fine: {'inizio': inizio, 'fine': fine, 'da': {'y': 50, 'o': 0}, 'a': {'y': 0, 'o': 100}}
    livelli = [
        # la parola: il fondo color cera copre il video, il miele si vede nelle lettere
        K.livello('ar-parola', contenuto='MIELE', x=0, y=0, w=100, h=100, size=330, peso='800', colore=C['nero'],
                  bgColor=C['chiaro'], align='center', fontFamily=TITOLI, lineHeight=1, letterSpacing=-6, textValign='center',
                  blendMode='screen', entra='fadeIn', ritardo=0, durata=700, esce='fadeOut',
                  scrollMove={'inizio': 0, 'fine': 72, 'tappe': [{'p': 0, 's': 1}, {'p': 50, 's': 6}, {'p': 76, 's': 24, 'o': 100},
                                                                  {'p': 90, 's': 34, 'o': 0}, {'p': 100, 's': 40, 'o': 0}]},
                  responsive={'mobile': {'fontSize': 150}}),
        K.livello('ar-sopra', contenuto='Arnia · miele di montagna dell’Appennino', x=0, y=20, w=100, size=13, peso='600',
                  colore=C['testo'], align='center', letterSpacing=4, textTransform='uppercase', fontFamily=MONO, entra='fadeIn',
                  blendMode='multiply', ritardo=300, scrollMove={'inizio': 0, 'fine': 25, 'da': {}, 'a': {'y': -60, 'o': 0}},
                  responsive={'mobile': {'y': 30, 'x': 6, 'width': 88, 'fontSize': m(10)}}),
        K.livello('ar-scorri', contenuto='Scorrete: si entra nel miele', x=0, y=80, w=100, size=13, peso='500',
                  colore=C['tenue'], align='center', letterSpacing=2, textTransform='uppercase', fontFamily=MONO, entra='fadeIn',
                  blendMode='multiply', ritardo=900, scrollMove={'inizio': 0, 'fine': 20, 'da': {}, 'a': {'y': 40, 'o': 0}},
                  responsive={'mobile': {'y': 66, 'x': 6, 'width': 88, 'fontSize': m(10)}}),
        # dopo: il velo scuro dal basso e i testi sul miele
        K.livello('ar-velo', tipo='shape', x=0, y=40, w=100, h=60, entra='fadeIn', ritardo=0, durata=400, esce='fadeOut',
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 35},
                  scrollMove={'inizio': 62, 'fine': 82, 'da': {'o': 0}, 'a': {'o': 100}},
                  responsive={'mobile': {'y': 35, 'height': 65}}),
        K.livello('ar-o', contenuto='Cinque mieli · cento arnie · Appennino modenese', x=6, y=54, w=60, size=12, peso='600',
                  colore=POLLINE, letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=0,
                  scrollMove=entra(70, 84), responsive={'mobile': {'fontSize': m(11), 'y': 50, 'x': 7, 'width': 86}}),
        K.livello('ar-t', contenuto='Il miele,<br><em>da vicinissimo.</em>', tag='h1', x=6, y=59, w=60, size=78, peso='600',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, letterSpacing=-1, entra='fadeIn', ritardo=0,
                  scrollMove=entra(72, 88), responsive={'mobile': {'fontSize': m(36), 'y': 54, 'x': 7, 'width': 88}}),
        K.livello('ar-s', contenuto='Estratto a freddo, lasciato decantare un mese e messo nel vasetto così com’è: acacia, '
                  'tiglio, millefiori di montagna, castagno e melata.', x=6, y=80, w=40, size=17, peso='400',
                  colore=mix(C['chiaro'], 88), lineHeight=1.6, entra='fadeIn', ritardo=0, scrollMove=entra(76, 92),
                  responsive={'mobile': {'fontSize': m(14), 'y': 67, 'x': 7, 'width': 86}}),
        K.livello('ar-b', tipo='button', contenuto='I mieli', x=60, y=82, w='auto', size=14, peso='600', colore=C['nero'],
                  bgColor=POLLINE, padding=16, borderRadius=999, entra='fadeIn', ritardo=0, buttonUrl=MIELI,
                  hover={'bgColor': C['chiaro'], 'duration': 250}, scrollMove=entra(80, 95),
                  responsive={'mobile': {'y': 79, 'x': 7, 'fontSize': m(12)}}),
        K.livello('ar-l', tipo='button', contenuto='Adotta un alveare', x=70, y=82, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), entra='fadeIn', ritardo=0, buttonUrl=ADOTTA,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250}, scrollMove=entra(82, 97),
                  responsive={'tablet': {'x': 74}, 'mobile': {'y': 84, 'x': 7, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('ar-1', K.sfondo_video(V_COLA, overlay=0.05, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'vh', 'value': 100}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})



MIELI_LISTA = [  # nome, sottotitolo, descrizione, foto, tinta
    ('Acacia', 'Maggio · sotto i 700 metri', 'Chiaro, liquido per mesi, dolce di vaniglia. Il miele dei bambini.',
     'pexels-10725589.jpg', 0),
    ('Tiglio', 'Giugno · viali e parchi', 'Fresco e mentolato, con un fondo amarognolo. Nel tè, la sera.',
     'pexels-4921856.jpg', 1),
    ('Millefiori di montagna', 'Luglio · prati sopra i 1.200 metri', 'Trifoglio, timo serpillo, rododendro: ogni annata '
     'ha il suo sapore.', 'pexels-3194327.jpg', 3),
    ('Castagno', 'Agosto · castagneti secolari', 'Scuro, tannico, quasi salato. Coi formaggi stagionati.',
     'pexels-17682675.jpg', 5),
    ('Melata di bosco', 'Settembre · abeti bianchi', 'Non dai fiori ma dagli alberi: scura, minerale, poco dolce.',
     'pexels-20012002.jpg', 5),
]


def mieli(t):
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': MIELI,
                   'color': TINTE[tn][1]} for k, (n, s, d, f, tn) in enumerate(MIELI_LISTA)],
        'lead_mode': 'swatch', 'swatch_size': 22, 'swatch_shape': 'circle', 'number_color': C['acc'],
        'number_hover_color': C['acc'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 50, 'name_weight': '600', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['acc'],
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(POLLINE, 10), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 260, 'peek_ratio': '1/1', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def listino(t):
    voci = [('Acacia · 500 g', 'Maggio, sotto i 700 metri', '9 €', 'pexels-10725589.jpg', ''),
            ('Tiglio · 500 g', 'Giugno, viali e parchi', '9 €', 'pexels-4921856.jpg', ''),
            ('Millefiori di montagna · 500 g', 'Luglio, prati d’alta quota', '10 €', 'pexels-3194327.jpg', 'Il più amato'),
            ('Castagno · 500 g', 'Agosto, castagneti secolari', '10 €', 'pexels-17682675.jpg', ''),
            ('Favo intero · 400 g', 'Il telaino tagliato a mano, con la cera', '16 €', 'pexels-20012002.jpg', 'Solo a settembre'),
            ('Cassetta degustazione · 4 × 125 g', 'Acacia, tiglio, millefiori, castagno', '18 €', 'pexels-30666799.jpg', '')]
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in voci],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 40), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': BOSCO, 'badge_color': C['chiaro'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 36, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc']}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Arnia · Appennino modenese', ['Cento arnie,', 'sei postazioni, una montagna.'],
                                size=58, layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Le arnie si spostano con le fioriture: a maggio sotto i castagni bassi per l’acacia, a '
                                'luglio sui prati sopra i milleduecento metri. Il miele si estrae a freddo, si lascia '
                                'decantare un mese e si mette nel vasetto così com’è.', size=17, color=C['testo']),
                      K.button(t, 'L’apiario', APIARIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('100', 'Arnie'), ('6', 'Postazioni'), ('1.450 m', 'La più alta'), ('5', 'Mieli all’anno')])],
              style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I mieli', ['Cinque mieli,', 'dal più chiaro al più scuro.'], size=58,
                         tagline='Passate su un miele: il favo si affaccia. Il tondo ha il colore del miele.')]),
        t.one([mieli(t)]),
        t.one([K.button(t, 'Il listino', MIELI + '#listino', pad_top=26)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_ORO, 'L’estrazione', 'Il miele <em>si estrae a freddo.</em>',
                             'Niente calore e niente filtri fini: resta il polline, restano i profumi. Se cristallizza è '
                             'buon segno.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-18925406.jpg', 'Due apicoltori con la tuta gialla aprono un’arnia', ratio='4/5',
                              tilt=True)], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Adotta un alveare', ['Un’arnia', 'tutta vostra.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Scegliete l’arnia, le date un nome. Vi mandiamo le foto delle visite, la mappa di dove sta e a '
                                'settembre il suo miele, col vostro nome sull’etichetta.', size=17),
                      K.specs(t, [('Da', '59 € all’anno'), ('Il miele', 'da 2 a 6 kg'), ('Le visite', 'due all’anno, '
                                  'con la tuta')]),
                      K.button(t, 'Adotta un alveare', ADOTTA, kind='pieno', pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Abbiamo adottato un’arnia per il matrimonio e regalato il suo miele agli ospiti. La nostra si chiama Ottavia: a '
           'settembre ci ha dato cinque chili di millefiori.', 'Anna e Pietro', 'Adottano Ottavia dal 2024')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_ARNIE, 'Le visite', 'Una mattina <em>fra le arnie.</em>',
                          'Da maggio a settembre, il sabato: la tuta, l’affumicatore, l’arnia aperta e l’assaggio dal favo.',
                          'Prenota la visita', APIARIO + '#visite', overlay=40, video=True))
    return out


def build_mieli():
    t = Tpl(ID, 'mieli')
    out = [K.intro(t, 'I mieli', ['Cinque fioriture,', 'cinque colori.'],
                   'Ogni miele ha la sua postazione e il suo mese. Li estraiamo a freddo e li vendiamo nell’annata: quando '
                   'finiscono, finiscono.', bg=K.video_bg(V_COLA), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Dal chiaro allo scuro', ['La scala', 'del colore.'], size=56,
                         tagline='Il colore del miele si misura in millimetri sulla scala Pfund: più alto il numero, più '
                                 'scuro e più forte il sapore.')]),
        t.one([mieli(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['In spaccio', 'o a casa vostra.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Spedizione gratuita da sei vasetti. Per i negozi e i ristoranti c’è il listino professionale.',
                             size=16),
                      K.button(t, 'Scriveteci per ordinare', CONTATTI + '#scrivici', kind='pieno', pad_top=14)],
                     wm='2-5', ws='1-1'),
               t.col([listino(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='listino',
        style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_TELAINI, 'Il favo', 'La cera <em>la fanno loro.</em>',
                             'Ogni telaino è pieno di cellette che le api sigillano quando il miele è pronto. Noi aspettiamo '
                             'che lo siano tutte.', overlay=40))
    out.append(K.domande(t, 'Sul', 'miele.', [
        ('Perché si cristallizza?', 'Perché è vero: il glucosio forma cristalli col freddo. Bastano dieci minuti a '
         'bagnomaria, sotto i 40 gradi.'),
        ('Quanto dura?', 'Due anni in un posto fresco e buio. Cambia colore e consistenza, non si guasta.'),
        ('Si può dare ai bambini?', 'Dopo il primo anno di età, sì. Prima no, come tutti i mieli.'),
    ], 'ar-faq'))
    return out


def build_adotta():
    t = Tpl(ID, 'adotta-un-alveare')
    out = [K.intro(t, 'Adotta un alveare', ['Un’arnia,', 'il vostro nome, il suo miele.'],
                   'Un regalo che dura un anno: le foto delle visite, la mappa della postazione, il miele di settembre '
                   'con l’etichetta col nome che le avete dato.', bg=K.photo_bg('pexels-14509312.jpg'), size=92)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Scegliete l’arnia', 'description': 'Mezza, intera o un apiario per l’azienda. '
                   'Le date un nome.'},
                  {'number': '02', 'title': 'Arriva il certificato', 'description': 'Con la foto dell’arnia, la sua postazione '
                   'e la famiglia che ci vive.'},
                  {'number': '03', 'title': 'Le foto delle visite', 'description': 'Una volta al mese, da aprile a settembre: '
                   'come cresce, cosa raccoglie.'},
                  {'number': '04', 'title': 'Il miele, a settembre', 'description': 'A casa vostra, con l’etichetta col suo '
                   'nome. Il primo vasetto lo assaggiamo insieme, se venite.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '600', 'title_color': C['testo'], 'title_size': 26,
        'title_weight': '600', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Quattro passi,', 'un anno di api.'], size=56)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le adozioni', ['Mezza, intera,', 'o un apiario.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Mezza arnia', '59', 'all’anno', 'Il certificato col nome\nLe foto delle visite\n2 kg di miele a settembre',
             False, ''),
            ('Un’arnia', '99', 'all’anno', 'Il certificato col nome\nLe foto delle visite\n5 kg di miele, in vasetti col nome\n'
             'Una visita con la tuta', True, 'la più scelta'),
            ('Apiario per l’azienda', '690', 'all’anno', 'Dieci arnie col vostro marchio\n150 vasetti da regalare\nUna mattina in '
             'apiario per la squadra', False, ''),
        ], 'Adotta adesso', ADOTTA + '#adotta', nota='Si rinnova solo se lo chiedete. Il miele dipende dall’annata: se le api ne '
           'fanno di meno, lo diciamo.'),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa adottate', 'adozione', True,
                     opzioni='Mezza arnia · 59 €\nUn’arnia · 99 €\nApiario per l’azienda · 690 €'),
             K.campo('text', 'Il nome dell’arnia', 'nome_arnia', segnaposto='Per esempio Ottavia'),
             K.campo('checkbox', 'Regalo', 'regalo', opzioni='È un regalo: il certificato va a un’altra persona'),
             K.campo('textarea', 'Il messaggio sul certificato, se è un regalo', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'adotta', 'Adotta', ['Scegliete l’arnia,', 'pensiamo al resto.'],
                                'Vi mandiamo il link per il pagamento e, appena arriva, il certificato.', VOCI, campi,
                                'Arnia — adozione', 'Adotta', 'Ricevuto. Vi scriviamo oggi con il certificato.'))
    return out


def build_apiario():
    t = Tpl(ID, 'apiario')
    out = [K.intro(t, 'L’apiario', ['Sei postazioni,', 'dai castagni ai prati.'],
                   'Siamo Lucia e Giacomo: apicoltori dal 2012, prima con dieci arnie nell’orto di casa.',
                   bg=K.video_bg(V_ARNIE), size=92)]
    stagioni = [('01', 'Aprile', 'Il risveglio', 'Si aprono le arnie dopo l’inverno, si conta la covata, si aggiungono i '
                 'melari.', 'pexels-4247092.jpg'),
                ('02', 'Maggio — giugno', 'Acacia e tiglio', 'Le arnie scendono in collina, sotto i boschi di robinia e lungo '
                 'i viali.', 'pexels-14022964.jpg'),
                ('03', 'Luglio — agosto', 'In quota', 'Si sale ai prati sopra i 1.200 metri e ai castagneti.',
                 'pexels-11334351.jpg'),
                ('04', 'Settembre', 'L’estrazione', 'Si tolgono i melari, si estrae a freddo, si lascia decantare.',
                 'pexels-4247163.jpg')]
    tappe = t.classic('step-timeline', {
        'items': [{'counter': n, 'tag_text': quando, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['sala'], 'media_color': C['acc'], 'pre_title': '',
                   'title': titolo, 'title_accent': '', 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': '', 'footer_label': '', 'separator_text': ''}
                  for n, quando, titolo, d, f in stagioni],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': C['acc'], 'timeline_dot_size': 10,
        'timeline_height': 1, 'timeline_margin_bottom': 40, 'counter_font_family': 'heading', 'counter_size': 40,
        'counter_color': C['acc'], 'counter_italic': True, 'counter_weight': '600', 'tag_size': 11, 'tag_color': C['tenue'],
        'media_aspect_ratio': '4/3', 'media_object_position': 'center center', 'media_radius': radius(10),
        'media_radius_hover': radius(10), 'media_shadow': 'none', 'show_media_label': False, 'pre_title_size': 11,
        'pre_title_color': C['acc'], 'title_font_family': 'heading', 'title_size': 22, 'title_weight': '600',
        'title_color': C['testo'], 'description_color': C['tenue']})
    out.append(t.section([
        t.one([K.sechead(t, 'L’anno delle api', ['Da aprile', 'a settembre.'], size=56)]),
        t.one([tappe]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-13625981.jpg', 'Giacomo con l’affumicatore fra le arnie blu', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Le visite', ['Una mattina', 'con la tuta.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Da maggio a settembre, il sabato alle nove. Vi diamo la tuta e i guanti, apriamo un’arnia '
                                'insieme e alla fine si assaggia il miele direttamente dal favo.', size=17),
                      K.specs(t, [('Quando', 'sabato alle 9, da maggio a settembre'), ('Durata', 'due ore'),
                                  ('Prezzo', '25 €, bambini dai 6 anni 15 €')]),
                      K.button(t, 'Prenota la visita', CONTATTI + '#scrivici', kind='pieno', pad_top=18)],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='visite',
        style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le postazioni', ['Dove stanno', 'le arnie.'], size=56,
                         tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-38027227.jpg', 'Papaveri e fiordalisi in un prato', 'I prati', '1.200 metri'),
                               ('pexels-14509312.jpg', 'Arnie colorate nel bosco', 'Il bosco', '900 metri'),
                               ('pexels-38524703.jpg', 'Un campo di fiori gialli sotto il cielo', 'La piana', '400 metri'),
                               ('pexels-11334351.jpg', 'Una fila di arnie lungo un sentiero di pini', 'La pineta', '700 metri'),
                               ('pexels-32977983.jpg', 'Papaveri fra le spighe', 'I campi', '500 metri')])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Venite allo spaccio,', 'o scriveteci.'],
                   'Per un ordine, un’adozione, una visita all’apiario o il listino per il vostro negozio.',
                   bg=K.photo_bg('pexels-35382037.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'cosa', True,
                     opzioni='Un ordine\nUn’adozione\nUna visita all’apiario\nIl listino per i negozi'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Una riga,', 'rispondiamo la sera.'],
                                'Di giorno siamo fra le arnie: rispondiamo entro un giorno.', VOCI, campi, 'Arnia — contatto',
                                'Invia', 'Ricevuto. Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lo spaccio', ['A Pavullo,', 'sotto i castagni.'], size=52, layout='stack', size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Via dei Castagni 4, Pavullo nel Frignano'), ('Aperto', 'sabato 9 — 12:30'),
                                  ('Da aprile a ottobre', 'anche la domenica')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Arnia · lo spaccio', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='apiario', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · arnia vuota', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui le api <em>non ci sono.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Sono tutte sui fiori. Torniamo all’inizio.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_FIORI, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Negozi online', 'La parola che si apre',
    ['miele', 'apicoltura', 'api', 'adotta un alveare', 'montagna', 'prodotti agricoli', 'chiaro', 'slider pro'],
    'Un’apicoltura di montagna, crema di cera e ambra: in apertura la parola MIELE col video del miele dentro le '
    'lettere, che scorrendo si ingrandisce finché si entra nel miele; i cinque mieli dal più chiaro al più scuro, il '
    'listino, l’adozione di un alveare e le visite.',
    [('I mieli', MIELI), ('Adotta un alveare', ADOTTA), ('L’apiario', APIARIO), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('mieli', 'I mieli', 'mieli'), ('adotta-un-alveare', 'Adotta un alveare', 'adotta-un-alveare'),
     ('apiario', 'L’apiario', 'apiario'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Besley\', \'Georgia\', serif',
    '\'Azeret Mono\', ui-monospace, Menlo, monospace', ['Besley', 'Work Sans', 'Azeret Mono'], corsivo_di=['Besley'],
    peso_titoli='600', raggio=10, grana=3, titoli_line='1.05', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'orbita'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'mieli.json': build_mieli(), 'adotta-un-alveare.json': build_adotta(), 'apiario.json': build_apiario(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
