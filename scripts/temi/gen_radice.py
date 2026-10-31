# -*- coding: utf-8 -*-
"""Genera il tema «Radice» (id radice): la pagina personale di Irene Valli, cuoca vegetale indipendente a Bologna:
cene a domicilio, consulenze per i ristoranti che vogliono un menu vegetale vero, corsi e ricette di stagione.

Tema chiaro, carta di riso, verde foglia, rosso barbabietola e giallo tuorlo. Titoli in Literata (anche corsivo), testo in
Albert Sans, dati in IBM Plex Mono.

Apertura: Slider Pro «l'orologio delle stagioni», alto 16:9, una diapositiva per stagione col suo video pieno (l'orto,
il raccolto, il mercato, la padella). A destra un quadrante scuro coi dodici mesi attorno al piatto della stagione: i tre
mesi della stagione si accendono e la lancetta avanza di un quarto a ogni diapositiva, sempre in senso orario. Le stagioni
si danno il cambio con lo zoom (zoomFade). Cursore: il pennello.
"""
import os
import sys
import math

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'radice'
NAME = 'Radice'

HEX = {
    'primary': '#8C3A5E', 'primary_contrast': '#FFFFFF', 'secondary': '#3F6B45', 'secondary_contrast': '#FFFFFF',
    'accent': '#E2B33C', 'dark': '#16201A', 'light': '#FFFFFF', 'muted': '#EAE5D8', 'muted_contrast': '#1E2A1F',
    'text': '#1E2A1F', 'text_muted': '#57614F', 'background': '#F5F2EA', 'border': '#DCD5C4', 'link': '#8C3A5E',
}

HOME, DOMICILIO, CONSULENZE, RICETTE, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:a-domicilio}}', '{{PAGINA:consulenze}}',
                                                 '{{PAGINA:ricette}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'irene@radice.example', '+39 051 000 4417', 'tel:+390510004417'
LAT, LNG = '44.4949', '11.3426'

V_ORTO, V_RACCOLTO, V_MERCATO, V_PADELLA, V_TAGLIO = ('pexels-video-7655573', 'pexels-video-7782528', 'pexels-video-3209759',
                                                      'pexels-video-7963455', 'pexels-video-856437')
PHOTOS = [
    'pexels-5425893.jpg', 'pexels-5677717.jpg', 'pexels-7890188.jpg', 'pexels-5084088.jpg', 'pexels-5737836.jpg',
    'pexels-5737830.jpg', 'pexels-15671324.jpg', 'pexels-15059687.jpg', 'pexels-1143754.jpg', 'pexels-7499323.jpg',
    'pexels-1833333.jpg', 'pexels-6811354.jpg', 'pexels-33368317.jpg', 'pexels-7964654.jpg', 'pexels-7214336.jpg',
    'pexels-7061664.jpg', 'pexels-5737461.jpg', 'pexels-8902124.jpg',
]
VIDEOS = [V_ORTO, V_RACCOLTO, V_MERCATO, V_PADELLA, V_TAGLIO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='500', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
TUORLO, FOGLIA = C['acc2'], C['sec']
TITOLI, MONO = 'Literata', 'IBM Plex Mono'
VOCI = [('map-pin', 'Via del Pratello 40 · Bologna', CONTATTI + '#cucina'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Radice', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Radice', 'Irene Valli, cuoca vegetale a Bologna. Cene a domicilio, consulenze per i ristoranti, '
                   'corsi nella cucina del Pratello. Tutto quello che cucino viene da tre orti e da un mercato.',
                   [('A domicilio', DOMICILIO), ('Consulenze', CONSULENZE), ('Ricette', RICETTE), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'La cucina', 'Corsi il martedì e il giovedì sera<br>Su appuntamento il resto della settimana', VOCI,
                   '© 2026 Radice · Irene Valli · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150,
                   fondo=C['sala'])


# ── L'apertura: l'orologio delle stagioni ──
MESI = ['GEN', 'FEB', 'MAR', 'APR', 'MAG', 'GIU', 'LUG', 'AGO', 'SET', 'OTT', 'NOV', 'DIC']
STAGIONI = [  # linguetta, occhiello, riga 1, riga 2, testo, ingredienti, video, piatto, mesi accesi, angolo della lancetta
    ('Primavera', 'Primavera · marzo — maggio', 'Il primo verde,', 'appena tagliato.',
     'Asparagi, piselli e le prime erbe dell’orto: piatti leggeri, crudi o appena passati sul fuoco.',
     'Asparagi · piselli · fave · ortica', V_ORTO, 'pexels-7499323.jpg', (2, 3, 4), 0),
    ('Estate', 'Estate · giugno — agosto', 'Pomodori caldi', 'di sole.',
     'L’orto dà più di quanto si riesca a mangiare: lo metto in barattolo, in forno, a crudo con l’olio nuovo.',
     'Pomodori · zucchine · mais · basilico', V_RACCOLTO, 'pexels-1143754.jpg', (5, 6, 7), 90),
    ('Autunno', 'Autunno · settembre — novembre', 'Zucche, funghi,', 'pentole lente.',
     'Le cotture si allungano, i colori si scaldano: la stagione dei brodi, delle zucche e delle castagne.',
     'Zucca · funghi · castagne · cavolo nero', V_MERCATO, 'pexels-15671324.jpg', (8, 9, 10), 180),
    ('Inverno', 'Inverno · dicembre — febbraio', 'Radici e brodo,', 'la cucina al caldo.',
     'Rape, sedano rapa, agrumi e legumi: l’inverno vegetale è il più goloso, se si ha pazienza.',
     'Rape · sedano rapa · agrumi · ceci', V_PADELLA, 'pexels-15059687.jpg', (11, 0, 1), 270),
]
W, H = 1440.0, 810.0          # la tela del computer
WM, HM = 640.0, 1400.0         # la tela del telefono


def apertura(t):
    """Le quattro stagioni in un carosello 3D: ogni carta è una stagione col suo video, il piatto tondo in alto e il
    titolo in basso; la carta al centro è dritta e grande, le vicine si girano ai lati. Il mouse inclina il piatto.
    (Le misure del piatto sono in % della carta, larga il 66% della tela: 300 px = 31,6% x 37%.)"""
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, ingr, video, piatto, accesi, angolo) in enumerate(STAGIONI):
        n = i + 1
        livelli = [
            K.livello('ra-velo%d' % n, tipo='shape', x=0, y=30, w=100, h=70, entra='fadeIn', ritardo=0, durata=600,
                      esce='fadeOut', shapeGradient={'angle': 0, 'from': mix(C['nero'], 90), 'to': mix(C['nero'], 0),
                                                     'fromStop': 62},
                      responsive={'mobile': {'y': 26, 'height': 74}}),
            K.livello('ra-piatto%d' % n, tipo='image', imageSrc=K.img(piatto), objectFit='cover', x=64, y=9, w=31.6, h=37,
                      borderRadius=999, borderWidth=6, borderColor=C['chiaro'], entra='zoomIn', ritardo=300, durata=1000,
                      esce='fadeOut', kenBurns={'type': 'zoomIn', 'intensity': 6, 'duration': 9000},
                      boxShadow={'x': 0, 'y': 24, 'blur': 50, 'spread': -14, 'color': mix(C['nero'], 70)},
                      parallax={'type': 'mouse', 'depth': 8, 'tilt3d': True, 'scrollSpeed': 0.5},
                      responsive={'mobile': {'x': 24.6, 'y': 6, 'width': 50.8, 'height': 18.6}}),
            K.livello('ra-o%d' % n, contenuto=occ, x=6, y=50, w=60, size=12, peso='600', colore=TUORLO, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=200,
                      responsive={'mobile': {'fontSize': m(11), 'y': 49, 'x': 7, 'width': 86}}),
            K.livello('ra-t%d' % n, contenuto=r1 + '<br><em>' + r2 + '</em>', tag='h1' if n == 1 else 'h2', x=6, y=55, w=62,
                      size=60, peso='500', colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, letterSpacing=-1,
                      entra='maskFromBottom', ritardo=300, durata=1200,
                      responsive={'mobile': {'fontSize': m(31), 'y': 52.5, 'x': 7, 'width': 88}}),
            K.livello('ra-s%d' % n, contenuto=testo, x=6, y=74, w=48, size=16, peso='400', colore=mix(C['chiaro'], 86),
                      lineHeight=1.6, ritardo=800, responsive={'mobile': {'fontSize': m(14), 'y': 63, 'x': 7, 'width': 86}}),
            K.livello('ra-i%d' % n, contenuto=ingr, x=6, y=88, w=50, size=12, peso='500', colore=mix(C['chiaro'], 76),
                      letterSpacing=1, fontFamily=MONO, ritardo=1000,
                      responsive={'mobile': {'fontSize': m(10), 'y': 74, 'x': 7, 'width': 86}}),
            K.livello('ra-b%d' % n, tipo='button', contenuto='Una cena a casa vostra', x=66, y=84, w='auto', size=14,
                      peso='600', colore=C['nero'], bgColor=TUORLO, padding=16, borderRadius=999, ritardo=1150,
                      buttonUrl=DOMICILIO, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 80, 'x': 7, 'fontSize': m(12)}}),
        ]
        sl.append(K.diapositiva('ra-%d' % n, K.sfondo_video(video, overlay=0.12, colore=C['nero']), livelli, durata=7000,
                                tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='slide', durata_trans=1000,
                       carousel=True, carouselWidth=66, carouselGap=18, carouselSideScale=0.84, carousel3D=True,
                       showArrows=True, arrowStyle='minimal', showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=True, parallaxType='mouse', parallaxIntensity=6, autoplaySpeed=7000,
                       progressBarColor=TUORLO, navColor=C['chiaro'],
                       globalBackground={'type': 'color', 'color': C['fondo']},
                       heightMobile={'mode': 'px', 'value': 1400})


def tre_modi(t):
    voci = [('pexels-7964654.jpg', 'Due ragazze in cucina che preparano le verdure', 'A domicilio',
             'Una cena a casa vostra', 'Porto la spesa, le pentole e i coltelli; cucino da voi, servo a tavola e lascio la '
             'cucina in ordine. Da due a trenta persone.', 'Come funziona', DOMICILIO),
            ('pexels-6811354.jpg', 'Due cuochi al banco con le verdure di stagione', 'Per i ristoranti',
             'Un menu vegetale vero', 'Non il piatto per chi non mangia carne: una parte del menu pensata da capo, con '
             'i costi, le schede e la brigata formata.', 'Le consulenze', CONSULENZE),
            ('pexels-33368317.jpg', 'Un gruppo di donne che cucina insieme ai fornelli', 'I corsi',
             'Il martedì e il giovedì', 'Otto persone, quattro ore, una stagione: si cucina tutti, si mangia insieme '
             'quello che si è fatto.', 'Le ricette e i corsi', RICETTE)]
    cols = []
    for foto, alt, occ, tit, testo, link_t, link_u in voci:
        cols.append(t.col([K.image(t, foto, alt, ratio='4/3', tilt=True),
                           K.label(t, occ, color=C['acc'], pad_top=22),
                           K.headline(t, tit, size=30, pad_bottom=10),
                           K.text(t, testo, size=15),
                           K.button(t, link_t, link_u, pad_top=10)], wm='1-3', ws='1-1'))
    return t.row(cols, layout='33-33-33', gap=32)


def stagione_menu(t):
    voci = [('Crudo di rape e agrumi', 'Rapa bianca, arancia rossa, finocchietto, olio di Brisighella', '1'),
            ('Ceci, cavolo nero e pane', 'Zuppa di ceci di Cicerale, cavolo nero, crostini al rosmarino', '2'),
            ('Tortelli di zucca, burro nocciola', 'La sfoglia tirata a mano, zucca violina, salvia croccante', '3'),
            ('Sedano rapa al sale', 'Cotto intero sotto la crosta di sale, servito con la salsa verde', '4'),
            ('Mela, nocciola, rosmarino', 'Mela cotogna al forno, crema di nocciole, gelato al rosmarino', '5')]
    return t.classic('pricelist', {
        'items': [{'title': ti, 'description': de, 'price': '0' + nu, 'image_url': '', 'highlighted': nu == '3',
                   'badge': 'il piatto della casa' if nu == '3' else ''} for ti, de, nu in voci],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 35), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'show_image': False, 'price_position': 'right', 'badge_bg': TUORLO,
        'badge_color': C['nero'], 'badge_border_radius': '999',
        'tile_padding': {'top': 16, 'right': 0, 'bottom': 16, 'left': 0}, 'card_bg': 'transparent'}, style={})


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
        t.row([t.col([K.sechead(t, 'Irene Valli · cuoca vegetale', ['La verdura al centro,', 'non al posto di qualcosa.'],
                                size=58, layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Cucino solo verdure, legumi e cereali, da quindici anni. Non per togliere qualcosa, ma '
                                'perché un sedano rapa cotto sotto il sale per tre ore è più interessante di molte '
                                'bistecche. Compro da tre orti dei colli e dal mercato del Pratello: quello che c’è, '
                                'quando c’è.', size=17, color=C['testo']),
                      K.button(t, 'Cosa faccio', DOMICILIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('15', 'Anni di cucina vegetale'), ('3', 'Orti dei colli'), ('22', 'Ristoranti seguiti'),
                            ('4', 'Stagioni, quattro menu')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Tre modi', ['A casa vostra, nel vostro', 'ristorante, nella mia cucina.'], size=56,
                         tagline='Passate sulle foto: si inclinano verso il puntatore.')]),
        t.one([tre_modi(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='tre-modi',
        style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_TAGLIO, 'Il coltello', 'Le erbe si tagliano <em>all’ultimo.</em>',
                             'Prezzemolo, erba cipollina, maggiorana: un minuto prima di servire, mai prima. Il profumo è '
                             'tutto lì.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il menu d’inverno', ['Cinque portate,', 'nessuna sostituzione.'], size=52,
                                layout='stack', size_mobile=34),
                      K.text(t, 'Il menu delle cene a domicilio cambia a ogni stagione. Questo è quello di dicembre, '
                                'gennaio e febbraio: 55 € a persona, i vini li scegliamo insieme.', size=16),
                      K.button(t, 'Prenota una cena', DOMICILIO + '#prenota', pad_top=14)], wm='2-5', ws='1-1'),
               t.col([stagione_menu(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Avevamo dodici ospiti e tre di loro mangiavano solo carne. Alla fine erano loro a chiedere la ricetta del '
           'sedano rapa. Irene ha lasciato la cucina più pulita di come l’aveva trovata.', 'Giovanna R.',
        'Cena a domicilio, Bologna')])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_RACCOLTO, 'Le cene', 'Una cena <em>a casa vostra.</em>',
                          'Da due a trenta persone, il menu della stagione, la spesa la porto io.', 'Come funziona', DOMICILIO,
                          overlay=40, video=True))
    return out


def build_domicilio():
    t = Tpl(ID, 'a-domicilio')
    out = [K.intro(t, 'A domicilio', ['Il ristorante,', 'per una sera, è casa vostra.'],
                   'Porto la spesa del mattino, le pentole e i coltelli. Cucino da voi, servo a tavola, racconto i piatti e '
                   'quando vado via la cucina è in ordine.', bg=K.video_bg(V_PADELLA), size=92)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Mi scrivete', 'description': 'Il giorno, quanti siete, che cosa non '
                   'mangiate. Vi rispondo in giornata con il menu della stagione.'},
                  {'number': '02', 'title': 'Vado al mercato', 'description': 'La mattina della cena, dagli orti dei colli '
                   'e dal mercato del Pratello. Niente arriva da lontano.'},
                  {'number': '03', 'title': 'Cucino da voi', 'description': 'Arrivo tre ore prima con tutto quello che '
                   'serve. Voi apparecchiate, io accendo i fuochi.'},
                  {'number': '04', 'title': 'Lascio la ricetta', 'description': 'A fine cena lascio sul tavolo le ricette '
                   'dei piatti: la verdura, la sapete già comprare.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '500', 'title_color': C['testo'], 'title_size': 28,
        'title_weight': '500', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Quattro passi,', 'voi vi sedete.'], size=56)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Due persone,', 'o trenta.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Cena per due', '220', 'tutto compreso', 'Cinque portate della stagione\nLa spesa e il servizio\nLe ricette della '
             'sera', False, ''),
            ('La tavola', '55', 'a persona, da 6 a 14', 'Cinque portate della stagione\nIl pane fatto da me\nI vini scelti '
             'insieme, a parte\nLe ricette della sera', True, 'la più scelta'),
            ('La festa', '40', 'a persona, da 15 a 30', 'Un buffet di dodici piatti\nUna persona in più per il servizio\nI '
             'piatti e i bicchieri, se servono', False, ''),
        ], 'Prenota la data', DOMICILIO + '#prenota', nota='Bologna e trenta chilometri attorno: fuori, il viaggio si aggiunge.',
            sfondo=C['sala']),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('date', 'La data', 'data', True), K.campo('select', 'Quanti siete', 'persone', True,
                                                            opzioni='2\nDa 6 a 14\nDa 15 a 30'),
             K.campo('textarea', 'Cosa non mangiate, la cucina che avete', 'note', larghezza='1-1',
                     segnaposto='Allergie, intolleranze, quanti fuochi e se c’è il forno.')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Scrivetemi la data,', 'vi mando il menu.'],
                                'Rispondo in giornata. La data è vostra con un acconto del 30%.', VOCI, campi,
                                'Radice — cena a domicilio', 'Chiedi la data', 'Ricevuta. Vi scrivo oggi con il menu.'))
    out.append(K.domande(t, 'Prima', 'di prenotare.', [
        ('Serve una cucina grande?', 'No: due fuochi e un forno bastano per dodici persone. Se manca qualcosa, lo porto io.'),
        ('E se qualcuno è celiaco?', 'Lo scrivete nella richiesta e il menu è senza glutine per tutti, non un piatto a parte.'),
        ('I vini?', 'Li scegliamo insieme: posso portarli io da una cantina dei colli, oppure apriamo i vostri.'),
    ], 'ra-faq-d'))
    return out


def build_consulenze():
    t = Tpl(ID, 'consulenze')
    out = [K.intro(t, 'Per i ristoranti', ['Un menu vegetale', 'che si vende.'],
                   'Non il piatto per chi non mangia carne: una parte del menu pensata da capo, con i costi, le schede '
                   'tecniche e la brigata che la sa fare.', bg=K.photo_bg('pexels-6811354.jpg'), size=92)]
    fasi = [('01', 'Settimana 1', 'Il sopralluogo', 'Una giornata in cucina con voi: i fornitori, le attrezzature, il '
             'servizio, i piatti che vendete di più.', 'pexels-7061664.jpg'),
            ('02', 'Settimane 2 — 4', 'Il menu', 'Otto piatti vegetali scritti per la vostra cucina, provati con la brigata '
             'e corretti finché non escono uguali.', 'pexels-15059687.jpg'),
            ('03', 'Settimana 5', 'Schede e costi', 'Una scheda per piatto: grammature, passaggi, allergeni e costo del '
             'piatto a quel prezzo di vendita.', 'pexels-8902124.jpg'),
            ('04', 'Settimana 6', 'L’apertura', 'Sono in cucina le prime tre sere, poi una volta al mese per la stagione '
             'successiva.', 'pexels-7964654.jpg')]
    tappe = t.classic('step-timeline', {
        'items': [{'counter': n, 'tag_text': quando, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['sala'], 'media_color': C['acc'], 'pre_title': '',
                   'title': titolo, 'title_accent': '', 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': '', 'footer_label': '', 'separator_text': ''}
                  for n, quando, titolo, d, f in fasi],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': C['acc'], 'timeline_dot_size': 10,
        'timeline_height': 1, 'timeline_margin_bottom': 40, 'counter_font_family': 'heading', 'counter_size': 40,
        'counter_color': C['acc'], 'counter_italic': True, 'counter_weight': '500', 'tag_size': 11, 'tag_color': C['tenue'],
        'media_aspect_ratio': '4/3', 'media_object_position': 'center center', 'media_radius': radius(14),
        'media_radius_hover': radius(14), 'media_shadow': 'none', 'show_media_label': False, 'pre_title_size': 11,
        'pre_title_color': C['acc'], 'title_font_family': 'heading', 'title_size': 22, 'title_weight': '500',
        'title_color': C['testo'], 'description_color': C['tenue']})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il metodo', ['Sei settimane,', 'poi camminate da soli.'], size=52, layout='stack',
                                size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Lavoro con un ristorante alla volta. Il prezzo è a progetto: dipende da quanti piatti e da '
                                'quante cucine.', size=16),
                      K.specs(t, [('Durata', 'sei settimane'), ('Piatti', 'da sei a dodici'),
                                  ('Da', '3.800 € a progetto'), ('Dopo', 'una visita al mese, se volete')])],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([tappe], style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.numeri(t, [('22', 'Ristoranti seguiti'), ('31%', 'Piatti vegetali venduti, in media'),
                            ('−18%', 'Costo medio del piatto'), ('6', 'Settimane')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Il nostro piatto vegetariano era un risotto alle verdure che nessuno ordinava. Adesso il sedano rapa al sale è il '
           'secondo piatto più venduto, e costa meno della metà del filetto.', 'Luca Ferri', 'Osteria in via Saragozza')])],
        padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('text', 'Il ristorante', 'ristorante', True),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Telefono', 'telefono'),
             K.campo('textarea', 'Il vostro menu oggi, e cosa vorreste', 'note', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'richiesta', 'Parliamone', ['Un caffè', 'nella vostra cucina.'],
                                'Il primo incontro è gratis: vengo a vedere la cucina e vi dico se posso esservi utile.',
                                VOCI, campi, 'Radice — consulenza', 'Chiedi l’incontro',
                                'Ricevuto. Vi chiamo entro due giorni per fissare l’incontro.'))
    return out


def ricette(t):
    voci = [('Asparagi, uovo e nocciole', 'Asparagi verdi alla brace, uovo in camicia, nocciole tostate, olio al limone. '
             '20 minuti.', 'pexels-7499323.jpg', 'Primavera'),
            ('Pomodori, mais e avocado', 'Piadina sottile, pomodori cuore di bue, mais arrostito, avocado, coriandolo. '
             '15 minuti.', 'pexels-1143754.jpg', 'Estate'),
            ('Insalata di zucchine e lamponi', 'Zucchine a nastro, ravanelli, lamponi, ricotta di pecora, menta. 10 '
             'minuti.', 'pexels-1833333.jpg', 'Estate'),
            ('Zucca, lenticchie e nasturzio', 'Zucca al forno, lenticchie di Castelluccio, foglie di nasturzio. 40 '
             'minuti.', 'pexels-15671324.jpg', 'Autunno')]
    cols = []
    for nome, dose, foto, stag in voci:
        cols.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center',
                            'overlay_color': C['nero'], 'overlay_opacity': 50}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': stag + ' · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': nome, 'back_description': dose,
            'back_bg': C['sala'], 'back_text_color': C['testo'], 'back_valign': 'center', 'back_cta_text': '',
            'back_cta_url': '', 'flip_direction': 'vertical', 'flip_trigger': 'hover', 'card_height': '400',
            'card_border_radius': '14', 'card_shadow': 'md', 'title_size': '25', 'title_weight': '500', 'desc_size': '15'},
            style={})], wm='1-4', ws='1-1'))
    return t.row(cols, layout='25-25-25-25', gap=18)


def orario(t):
    corsi = [('basi', 'Le basi vegetali', C['sec'], 'Brodi, fondi, salse e le cotture di ogni verdura.', 240),
             ('pasta', 'Pasta fresca senza uova', C['acc'], 'Sfoglia, tortelli, tagliatelle con le farine giuste.', 240),
             ('legumi', 'Legumi e cereali', mix(TUORLO, 70, C['testo']), 'Ammollo, cotture e dieci piatti con un barattolo di ceci.', 240)]
    piano = [(2, '18:30', '22:30', 'basi'), (4, '18:30', '22:30', 'pasta'), (6, '10:00', '14:00', 'legumi')]
    return t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'ra-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': 'Irene Valli',
                     'sala': 'La cucina del Pratello', 'livello': 'tutti', 'posti': 8, 'nota': ''}
                    for k, (g, a, b, c) in enumerate(piano)],
        'giorni': 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato', 'ora_inizio': '09:00', 'ora_fine': '23:00',
        'mostra_filtri': False, 'etichetta_tutti': 'Tutti', 'filtro_livello': False, 'filtro_istruttore': False,
        'testo_vuoto': 'Nessun corso in questo giorno.', 'testo_prenota': 'Prenota il posto',
        'link_prenota': CONTATTI + '?corso={corso}&giorno={giorno}&ora={ora}#scrivimi', 'colore_oggi': C['acc'],
        'colore_adesso': TUORLO, 'colore_tenue': C['testo'], 'giorno_font_family': 'heading', 'giorno_font_size': 20,
        'giorno_font_weight': '500', 'giorno_uppercase': False, 'corso_font_family': 'heading', 'corso_font_size': 17,
        'corso_font_weight': '500', 'ora_font_family': 'var(--olo-font-family-mono)', 'raggio_lezione': radius(10),
        'altezza_ora': 44, 'gap': 4, 'forma_filtri': 'pillola'}, style={})


def build_ricette():
    t = Tpl(ID, 'ricette')
    out = [K.intro(t, 'Ricette e corsi', ['Quello che cucino,', 'scritto per voi.'],
                   'Una ricetta a settimana, sempre di stagione, e due corsi a settimana nella cucina del Pratello.',
                   bg=K.photo_bg('pexels-5677717.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le ricette', ['Quattro carte,', 'quattro piatti veri.'], size=56,
                         tagline='Passate su una carta: dietro ci sono gli ingredienti e il tempo che serve.')]),
        t.one([ricette(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I corsi', ['Otto persone,', 'una stagione.'], size=56,
                         tagline='Si cucina tutti, si mangia insieme alla fine. Grembiule e coltelli ve li do io.')]),
        t.one([orario(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-5737461.jpg', 'Irene sfoglia il suo quaderno di ricette', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Il libro', ['Radice,', 'quattro stagioni.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Sessanta ricette, quindici per stagione, con la lista della spesa del mercato e quello che '
                                'si può preparare in anticipo. 240 pagine, carta riciclata.', size=17),
                      K.specs(t, [('Pagine', '240'), ('Ricette', '60'), ('Prezzo', '32 €, con la dedica')]),
                      K.button(t, 'Lo voglio con la dedica', CONTATTI + '#scrivimi', kind='pieno', pad_top=18)],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_MERCATO, 'Il mercato', 'La ricetta comincia <em>dal banco.</em>',
                             'Prima si guarda cosa c’è, poi si decide cosa cucinare. Mai il contrario.', overlay=40))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Scrivetemi,', 'rispondo io.'],
                   'Per una cena, una consulenza, un corso o il libro con la dedica.',
                   bg=K.photo_bg('pexels-5084088.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'cosa', True,
                     opzioni='Una cena a domicilio\nUna consulenza per il ristorante\nUn corso\nIl libro con la dedica'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivimi', 'Scrivetemi', ['Una riga basta,', 'poi ci sentiamo.'],
                                'Rispondo entro un giorno, di solito la sera dopo il servizio.', VOCI, campi,
                                'Radice — contatto', 'Invia', 'Ricevuto. Vi rispondo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La cucina', ['Al Pratello,', 'a due passi dal mercato.'], size=52, layout='stack',
                                size_mobile=34),
                      K.image(t, 'pexels-7214336.jpg', 'La cucina luminosa dove si tengono i corsi', ratio='16/10'),
                      K.specs(t, [('Indirizzo', 'Via del Pratello 40, Bologna'), ('Corsi', 'martedì e giovedì sera'),
                                  ('Visite', 'su appuntamento')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Radice · la cucina', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='cucina', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori stagione', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>non è di stagione.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Tornerà, come gli asparagi. Intanto, la home.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ORTO, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'Il carosello delle stagioni',
    ['chef', 'cuoca', 'vegetale', 'vegetariano', 'cene a domicilio', 'consulenze', 'corsi di cucina', 'chiaro', 'slider pro'],
    'La pagina di una cuoca vegetale indipendente, carta di riso e verde foglia: in apertura le quattro stagioni in '
    'un carosello 3D, ognuna col suo video e il piatto tondo che si inclina col mouse; le cene a domicilio coi '
    'prezzi, le consulenze per i ristoranti, le ricette da girare e i corsi.',
    [('A domicilio', DOMICILIO), ('Consulenze', CONSULENZE), ('Ricette', RICETTE), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('a-domicilio', 'A domicilio', 'a-domicilio'), ('consulenze', 'Consulenze', 'consulenze'),
     ('ricette', 'Ricette e corsi', 'ricette'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Albert Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Literata\', \'Georgia\', serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Literata', 'Albert Sans', 'IBM Plex Mono'],
    corsivo_di=['Literata'], peso_titoli='500', raggio=14, grana=3, titoli_line='1.06', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'pennello'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'a-domicilio.json': build_domicilio(), 'consulenze.json': build_consulenze(),
                        'ricette.json': build_ricette(), 'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
