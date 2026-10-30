# -*- coding: utf-8 -*-
"""Genera il tema «Ca' Notturna» (id ca-notturna): un albergo di undici stanze in un palazzo del Quattrocento a Castello,
Venezia, pensato per chi vive di notte (la reception non chiude, la colazione arriva fino all'una). Scuro come il velluto.

Nero vinaccia, oro delle candele, bordeaux e il verde acqua della laguna di notte. Titoli in Bodoni Moda (corsivo per
l'accento), testo in Jost, dati in DM Mono.

Apertura: Slider Pro con la transizione a sipario (curtain3D) e le miniature delle quattro ore della notte; il titolo si
scopre dietro una lama d'oro, e accanto a ogni ora una «cartolina» incorniciata col dettaglio che si muove col mouse. Poi
il nome che scorre sull'onda del canale (Text path), le stanze in una lista con l'anteprima che segue il cursore
(Hover list), la colazione, il bagno e la porta d'acqua in riquadri che si svelano (Reveal box), il libro degli ospiti
(North quote slider) e un pannello fisso col bar di stanotte (Floating panel).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'ca-notturna'
NAME = 'Ca’ Notturna'

HEX = {
    'primary': '#D9B26F', 'primary_contrast': '#160D0B', 'secondary': '#8A2433', 'secondary_contrast': '#FBF4EA',
    'accent': '#5FBFB2', 'dark': '#080405', 'light': '#FBF4EA', 'muted': '#1A1012', 'muted_contrast': '#F1E6DA',
    'text': '#F1E6DA', 'text_muted': '#B6A396', 'background': '#0F0809', 'border': '#33232A', 'link': '#D9B26F',
}

HOME, STANZE, BAR, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:le-stanze}}', '{{PAGINA:il-bar}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'notte@canotturna.example', '+39 041 000 4011', 'tel:+390410004011'
LAT, LNG = '45.4343', '12.3446'

V_CANDELE, V_CANDELABRO, V_FILA = 'pexels-video-5766230', 'pexels-video-6032576', 'pexels-video-6802671'
V_PIOGGIA, V_LAMPIONE = 'pexels-video-15161525', 'pexels-video-4458845'
PHOTOS = [
    'pexels-39428241.jpg', 'pexels-14447176.jpg', 'pexels-34057921.jpg', 'pexels-35794820.jpg', 'pexels-36422261.jpg',
    'pexels-36799851.jpg', 'pexels-37789117.jpg', 'pexels-37789118.jpg', 'pexels-39241528.jpg', 'pexels-6466236.jpg',
    'pexels-40411933.jpg', 'pexels-3923362.jpg', 'pexels-39643624.jpg', 'pexels-39966133.jpg', 'pexels-6394610.jpg',
    'pexels-34119214.jpg', 'pexels-7195723.jpg', 'pexels-34051671.jpg', 'pexels-38783853.jpg', 'pexels-24604833.jpg',
    'pexels-14897611.jpg', 'pexels-36107885.jpg', 'pexels-36189463.jpg', 'pexels-4485391.jpg', 'pexels-9198187.jpg',
    'pexels-31397070.jpg', 'pexels-36393803.jpg', 'pexels-36041760.jpg', 'pexels-1816529.jpg', 'pexels-36299919.jpg',
    'pexels-37793335.jpg', 'pexels-36437452.jpg', 'pexels-36437455.jpg', 'pexels-36437447.jpg', 'pexels-6466288.jpg',
    'pexels-6466283.jpg', 'pexels-6466289.jpg',
]
VIDEOS = [V_CANDELE, V_CANDELABRO, V_FILA, V_PIOGGIA, V_LAMPIONE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Castello 4011, Rio de la Pietà · Venezia', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]

STANZE_DATI = [
    ('luna', 'Camera della Luna', '24 m² · sul canale', 'Il letto a baldacchino e due finestre gotiche sul rio.', 'pexels-6466236.jpg', '290 €'),
    ('lume', 'Camera del Lume', '20 m² · sulla corte', 'La lampada da lettura del nonno e il silenzio della corte.', 'pexels-40411933.jpg', '230 €'),
    ('nera', 'Camera Nera', '28 m² · tutta buia', 'Velluto nero, poltrone intagliate: per chi dorme di giorno.', 'pexels-39966133.jpg', '320 €'),
    ('doge', 'Suite del Doge', '46 m² · salotto', 'Il salotto blu col camino e il bagno di marmo di Carrara.', 'pexels-3923362.jpg', '540 €'),
    ('mezzanino', 'Il Mezzanino', '18 m² · soppalco', 'Sotto le travi, la poltrona di velluto e il tè a ogni ora.', 'pexels-39643624.jpg', '190 €'),
    ('altana', 'La stanza con l’altana', '30 m² · terrazza', 'Una scala di legno porta all’altana privata sopra i tetti.', 'pexels-36437455.jpg', '380 €'),
]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Ca’ Notturna', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Ca’ Notturna', 'Undici stanze in un palazzo del Quattrocento a Castello. La reception non chiude, la '
                   'colazione arriva fino all’una e il bar apre quando chiudono gli altri.',
                   [('Le stanze', STANZE), ('Il bar', BAR), ('Prenota', PRENOTA), ('Come arrivare', PRENOTA + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'La porta d’acqua', 'Dal motoscafo si scende sulla riva di pietra d’Istria, dentro il palazzo. È aperta '
                   'tutta la notte: suonate la campanella.', VOCI, '© 2026 Ca’ Notturna srl · Venezia · CIN IT027042A1000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=130)


def apertura(t):
    m = K.mob
    ore = [
        ('22:00 · si arriva dall’acqua', 'Venezia,\ndopo le dieci.', 'Un palazzo del Quattrocento a Castello, aperto per chi '
         'vive di notte. Il motoscafo vi lascia alla porta d’acqua.', 'pexels-34057921.jpg', 'pexels-37789117.jpg',
         'Il gondoliere Toni'),
        ('23:30 · il salone', 'Sotto la cupola,\nil pianoforte.', 'Il salone del piano nobile ha la cupola d’oro del '
         'Seicento. Elena suona dalle undici, finché resta qualcuno.', 'pexels-24604833.jpg', 'pexels-36299919.jpg',
         'Vetro di Murano'),
        ('01:00 · la stanza', 'Undici stanze,\nnessuna sveglia.', 'Tende di velluto che fanno buio vero e la colazione '
         'servita fino all’una del pomeriggio.', 'pexels-6466236.jpg', 'pexels-39966133.jpg', 'La Camera Nera'),
        ('03:00 · fuori piove', 'La città\nè tutta vostra.', 'Le calli vuote e l’acqua che riflette i lampioni. Alla '
         'reception: l’ombrello e la mappa dei bacari aperti.', 'pexels-1816529.jpg', 'pexels-36041760.jpg',
         'Il rio sotto casa'),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo, cartolina, didascalia) in enumerate(ore):
        n = i + 1
        sl.append(K.diapositiva('cn-%d' % n, K.sfondo_foto(sfondo, overlay=0.62 if n == 1 else 0.5, scala=1.1, durata=11000,
                                                            verso='out' if n % 2 else 'in'), [
            K.livello('cn-r%d' % n, tipo='shape', x=6, y=25, w=6, h=0.3, entra='fadeIn', ritardo=200, durata=900,
                      bgColor=C['acc'], responsive={'mobile': {'y': 11, 'width': 14}}),
            K.livello('cn-o%d' % n, contenuto=occ, x=13, y=23.6, w=40, size=13, peso='400', colore=C['acc'], ritardo=150,
                      letterSpacing=3, textTransform='uppercase', fontFamily='DM Mono',
                      responsive={'mobile': {'fontSize': m(11), 'x': 23, 'y': 10.2, 'width': 72}}),
            K.livello('cn-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=30, w=54, size=92, peso='400',
                      colore=C['chiaro'], entra='fadeIn', ritardo=300, durata=700, lineHeight=1.0, letterSpacing=-1,
                      fontStyle='italic' if n % 2 == 0 else 'normal',
                      sfx={'effect': 'blockRight', 'color': C['acc'], 'duration': 1100},
                      textShadow={'x': 0, 'y': 2, 'blur': 24, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(40), 'y': 14, 'width': 88}}),
            K.livello('cn-s%d' % n, contenuto=testo, x=6, y=62, w=34, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1300, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 27, 'width': 88}}),
            K.livello('cn-b%d' % n, tipo='button', contenuto='Prenota una notte', x=6, y=77, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1600, buttonUrl=PRENOTA,
                      hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 40, 'fontSize': m(12)}}),
            K.livello('cn-c%d' % n, tipo='image', imageSrc=K.img(cartolina), x=68, y=20, w=22, h=56, entra='zoomIn',
                      ritardo=700, durata=1200, padding=10, bgColor=C['nero'], borderWidth=1, borderColor=C['acc'],
                      objectFit='cover', parallaxDepth=5, kenBurns={'type': 'zoomIn', 'intensity': 12, 'duration': 9000},
                      boxShadow={'x': 0, 'y': 30, 'blur': 60, 'spread': 0, 'color': mix(C['nero'], 70)},
                      hover={'scale': 1.03, 'duration': 400},
                      responsive={'mobile': {'x': 24, 'y': 50, 'width': 52, 'height': 30}}),
            K.livello('cn-d%d' % n, contenuto=didascalia, x=68, y=78, w=22, size=12, peso='400',
                      colore=mix(C['chiaro'], 80), align='right', entra='fadeIn', ritardo=1400, letterSpacing=2,
                      textTransform='uppercase', fontFamily='DM Mono',
                      responsive={'mobile': {'x': 24, 'y': 81.5, 'width': 52, 'fontSize': m(10)}}),
        ], durata=7000))
    globali = [K.livello('cn-g', contenuto='Marea stanotte +68 cm · 9 °C · la porta d’acqua è aperta', x=6, y=90, w=36, size=11,
                         peso='400', colore=mix(C['chiaro'], 75), entra='fadeIn', ritardo=600, letterSpacing=2,
                         textTransform='uppercase', fontFamily='DM Mono', responsive={'mobile': {'visible': False}})]
    return K.proslider(t, sl, globali=globali, transizione='curtain3D', durata_trans=1300, showThumbs=True,
                       thumbPosition='bottom', showDots=False, showArrows=True, parallaxIntensity=4)


def stanze_lista(t):
    return t.classic('hoverlist', {
        'items': [{'name': nome, 'sub': sotto + ' · da ' + prezzo, 'desc': desc, 'number': '%02d' % (i + 1),
                   'image': K.img(foto), 'link_url': STANZE + '#' + sid, 'color': C['acc']}
                  for i, (sid, nome, sotto, desc, foto, prezzo) in enumerate(STANZE_DATI)],
        'lead_mode': 'number', 'number_color': C['acc'], 'number_hover_color': C['chiaro'], 'name_font_family': 'heading',
        'name_color': C['testo'], 'name_hover_color': C['acc'], 'name_size': 40, 'name_uppercase': False,
        'sub_color': C['tenue'], 'sub_hover_color': C['acc2'], 'sub_size': 12, 'sub_uppercase': True,
        'desc_color': C['tenue'], 'desc_hover_color': C['testo'], 'desc_size': 15, 'row_padding_y': 26, 'hover_indent': 24,
        'hover_bg': mix(C['acc'], 6), 'line_color': C['filo'], 'line_hover_color': C['acc'], 'peek': True,
        'peek_mode': 'image', 'peek_width': 240, 'peek_ratio': '4/5', 'mono_font_family': 'DM Mono',
    })


def svela(t, foto, titolo, testo):
    return t.classic('revealbox', {
        'media': {'type': 'none'},
        'top_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center'},
        'bottom_media': {'type': 'solid', 'color': C['sala']}, 'visible_height': 460,
        'top_content': '<p><strong>%s</strong></p>' % titolo,
        'bottom_content': '<p><strong>%s</strong></p><p>%s</p>' % (titolo, testo),
        'top_text_color': C['chiaro'], 'top_font_size': '22', 'bottom_text_color': C['testo'], 'bottom_font_size': '16',
        'text_color': C['chiaro'], 'overlay_color': C['nero'], 'overlay_opacity': 40, 'reveal_effect': 'slide-up',
        'top_padding': '28', 'bottom_padding': '32',
    })


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    onda = t.classic('textpath', {
        'typography_preset': '', 'text': 'Ca’ Notturna · Castello 4011 · la porta d’acqua sul Rio de la Pietà · aperto dalle '
                                         '19 alle 13 · Ca’ Notturna · Castello 4011 · ',
        'path_preset': 'custom', 'custom_path': 'M 0 50 Q 37.5 12 75 50 T 150 50 T 225 50 T 300 50',
        'font_size': '6.5', 'text_color': C['acc'], 'letter_spacing': '0.6', 'animation': 'continuous', 'animation_speed': '24',
        'spin': False,
    }, style={'margin_top': -40, 'margin_bottom': -60})
    out.append(t.section([
        t.one([K.sechead(t, 'Un albergo al contrario', ['Si dorme di giorno,', 'si vive di notte.'], size=64, size_mobile=40,
                         tagline='Abbiamo aperto Ca’ Notturna nel 2019 per chi arriva a Venezia col treno delle undici, per i '
                                 'musicisti della Fenice, per chi vuole vedere San Marco vuota alle quattro. Le regole '
                                 'dell’albergo, qui, sono girate di dodici ore.')]),
        t.one([onda], anim=None),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 60}))

    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Le stanze', ['Undici,', 'e nessuna uguale.'], size=60, layout='stack', size_mobile=38),
                      K.text(t, 'Passa sopra una stanza: la vedi. Tutte hanno le tende che fanno buio vero e il campanello '
                                'per la colazione, a qualsiasi ora.'),
                      K.button(t, 'Tutte le stanze', STANZE, pad_top=20)], wm='1-3', ws='1-1'),
               t.col([stanze_lista(t)], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))

    out.append(t.section([
        t.one([K.sechead(t, 'Tre cose che non si vedono', ['Tocca,', 'e si apre.'], size=56,
                         tagline='I dettagli che i nostri ospiti ci raccontano quando ripartono.')]),
        t.row([t.col([svela(t, 'pexels-6466288.jpg', 'Colazione fino all’una', 'Portata in camera su un carrello d’argento: '
                            'brioche del Rosa Salva, uova come le volete, spremuta. Anche alle dodici e mezza.')],
                     wm='1-3', ws='1-1'),
               t.col([svela(t, 'pexels-7195723.jpg', 'Il marmo nero', 'Ogni bagno ha una vasca o una doccia di marmo '
                            'Marquinia, l’acqua sempre calda e la luce bassa che non sveglia.')], wm='1-3', ws='1-1'),
               t.col([svela(t, 'pexels-37789118.jpg', 'La porta d’acqua', 'Il gondoliere Toni vi aspetta alla riva di casa: '
                            'un giro di quaranta minuti nei rii di Castello, a lume di lanterna.')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=18, valign='start'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))

    out.append(K.video_frase(t, V_CANDELE, 'Il bar · dalle 22 alle 4', 'Apre quando <em>chiudono gli altri.</em>',
                             'Duecento candele, il bancone di noce e Marco che fa lo spritz col Select come lo faceva '
                             'suo padre. Aperto anche a chi non dorme qui.', overlay=45,
                             extra=[K.button(t, 'Il bar', BAR, kind='pieno', align='center', pad_top=18)]))

    ospiti = t.classic('northquoteslider', {
        'heading': 'Dal libro degli ospiti',
        'items': [{'quote': 'Siamo usciti a mezzanotte e siamo tornati alle cinque: la luce del portiere era ancora accesa, '
                            'e c’era il tè caldo.', 'author_name': 'Sofia e Hugo', 'author_role': 'Camera della Luna · '
                   'febbraio 2026', 'logo_text': ''},
                  {'quote': 'Suono alla Fenice e qui finalmente ho trovato un albergo che non mi sveglia alle sette per '
                            'rifare la stanza.', 'author_name': 'Pietro', 'author_role': 'violoncello · ospite fisso',
                   'logo_text': ''},
                  {'quote': 'La colazione all’una, sul letto, con le campane di San Zaccaria. Torneremo per il Redentore.',
                   'author_name': 'Margaux', 'author_role': 'Suite del Doge · luglio 2026', 'logo_text': ''}],
        'slant': True, 'autoplay': True, 'autoplay_speed': 7, 'graphic_label': 'Ca’ Notturna · libro degli ospiti',
        'bg_color': 'transparent', 'heading_color': C['testo'], 'quote_color': C['testo'], 'author_color': C['acc'],
        'role_color': C['tenue'], 'logo_color': C['testo'], 'arrow_color': C['testo'], 'graphic_color': C['sec'],
        'graphic_line_color': C['fondo'], 'quote_size': 40,
    })
    out.append(t.section([t.one([ospiti])], padding='large', width='large',
                         style={'padding_top': 130, 'padding_bottom': 130}))

    pannello = t.classic('floatingpanel', {
        'placement': 'bottom-right', 'offset_y': '96', 'width': '290', 'bg_color': C['sala'],
        'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': mix(C['acc'], 50)},
        'border_radius': 2, 'shadow': True, 'shadow_color': mix(C['nero'], 60), 'trigger_icon': 'star',
        'trigger_bg': C['acc'], 'trigger_color': C['on_acc'], 'trigger_radius': '50', 'close_color': C['tenue'],
    })
    pannello['children'] = [
        K.text(t, '<p><strong>Stanotte al bar</strong><br>Elena al pianoforte dalle 23. Restano quattro tavoli vicino '
                  'alle candele.</p>', size=15, color=C['testo']),
        K.button(t, 'Tieni un tavolo', BAR + '#tavolo', kind='pieno', pad_top=4),
    ]
    out.append(t.section([t.one([pannello], anim=None)], padding='remove-vertical'))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Ca’ Notturna',
                page_intro_caption='Venezia · dalle 19 alle 13', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_stanze():
    t = Tpl(ID, 'le-stanze')
    out = [K.intro(t, 'Le stanze', ['Il buio', 'fatto bene.'],
                   'Undici stanze su tre piani, tutte con le tende di velluto doppio, i letti di Rubelli e il campanello per '
                   'la colazione. Nessuna pulizia prima delle due, se non la chiedete voi.',
                   bg=K.photo_bg('pexels-34051671.jpg', overlay=60), overlay_text=True)]
    griglia = t.classic('overlaygrid', {
        'items': [{'image': K.img(foto), 'title': nome, 'subtitle': desc, 'link': '#' + sid,
                   'ribbon': 'da ' + prezzo} for sid, nome, sotto, desc, foto, prezzo in STANZE_DATI],
        'columns': '3', 'columns_mobile': '1', 'gap': 'medium', 'height': '460', 'image_ratio': '4/5', 'image_fit': 'cover',
        'overlay_position': 'bottom', 'overlay_horizontal': 'left', 'overlay_style': 'overlay-default',
        'overlay_padding': 'medium', 'title_size': 'h3', 'hover_effect': 'zoom', 'hover_overlay': 'always',
        'ribbon_position': 'top-right', 'ribbon_bg': C['acc'], 'ribbon_color': C['on_acc'],
    })
    out.append(t.section([t.one([griglia])], padding='large', width='large', style={'padding_top': 70}))
    tabella = t.classic('table', {
        'table_data': [['Stanza', 'Metri', 'Vista', 'Da']] +
                      [[nome, sotto.split(' · ')[0], sotto.split(' · ')[1], prezzo] for sid, nome, sotto, desc, foto, prezzo
                       in STANZE_DATI],
        'has_header': True, 'striped': True, 'bordered': False, 'hover_effect': True, 'first_col_bold': True,
        'col_alignments': ['left', 'center', 'left', 'right'], 'responsive_mode': 'scroll', 'header_bg': C['acc'],
        'header_text_color': C['on_acc'], 'text_color': C['testo'], 'even_row_bg': mix(C['acc'], 6),
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'A confronto', ['Una per', 'ogni notte.'], size=52, layout='stack', size_mobile=36),
                      K.text(t, 'Il prezzo è per due persone con la colazione. Dalla terza notte, la cena al bar è nostra.')],
                     wm='1-3', ws='1-1'),
               t.col([tabella], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di arrivare.', [
        ('A che ora si entra in camera?', 'Quando volete: la reception non chiude. Se arrivate alle quattro del mattino, la '
                                         'stanza è pronta e la luce del corridoio accesa.'),
        ('E a che ora si lascia?', 'Alle sedici. Chi prende il treno della sera può restare al bar fino alla partenza.'),
        ('Si sente il rumore?', 'Poco: i muri sono di mattoni del Quattrocento, le finestre doppie. Le campane di San '
                                'Zaccaria sì, alle sette.'),
        ('Ci sono stanze per chi viaggia da solo?', 'Il Mezzanino, a 190 €. E il bar è il posto più facile di Venezia per '
                                                   'fare conversazione alle due di notte.'),
    ], 'st', bg=False))
    return out


def build_bar():
    t = Tpl(ID, 'il-bar')
    out = [K.intro(t, 'Il bar', ['Duecento candele', 'e un pianoforte.'],
                   'Il bar del piano terra apre alle dieci di sera e chiude quando esce l’ultimo. Ci vengono gli ospiti, i '
                   'musicisti dopo il concerto e i veneziani che non hanno sonno.', bg=K.video_bg(V_FILA, overlay=50),
                   overlay_text=True)]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Mar — Gio', 'time': '22 — 2', 'note': 'Il piano dalle 23'},
                  {'day': 'Ven — Sab', 'time': '22 — 4', 'note': 'Jazz in trio a mezzanotte'},
                  {'day': 'Domenica', 'time': '19 — 1', 'note': 'Cicchetti e vino della laguna'}],
        'columns': 3, 'band_padding_y': 36, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': C['acc'], 'day_size': 13, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 52,
        'time_weight': '400', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': 'DM Mono',
    })
    out.append(t.section([t.one([orari])], padding='large', style={'padding_top': 50, 'padding_bottom': 60}))
    lista = t.classic('pricelist', {
        'items': [{'title': ti, 'description': de, 'price': pr, 'image_url': K.img(fo), 'highlighted': hi, 'badge': ba}
                  for ti, de, pr, fo, hi, ba in [
                      ('Spritz al Select', 'Come lo faceva il padre di Marco: Select, prosecco, seltz e un’oliva grande.',
                       '9 €', 'pexels-36189463.jpg', False, ''),
                      ('Notte a Castello', 'Rum agricolo, amaro di carciofo, scorza d’arancia bruciata.', '14 €',
                       'pexels-4485391.jpg', True, 'Della casa'),
                      ('Bellini fuori stagione', 'Pesca bianca sciroppata in estate, prosecco di Valdobbiadene.', '12 €',
                       'pexels-9198187.jpg', False, ''),
                      ('Il bicchiere del pianista', 'Un whisky torbato scelto ogni sera da Elena, con un cubo solo.', '16 €',
                       'pexels-36107885.jpg', False, ''),
                      ('Dorona al bicchiere', 'Il vino d’oro di Mazzorbo, nel calice soffiato a Murano. Coi cicchetti.',
                       '15 €', 'pexels-37793335.jpg', False, '')]],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 35), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 76, 'image_border_radius': '2', 'show_image': True,
        'price_position': 'right', 'highlighted_bg': mix(C['acc'], 8), 'badge_bg': C['acc'], 'badge_color': C['on_acc'],
        'badge_border_radius': '2', 'card_bg': 'transparent',
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La carta', ['Da bere,', 'e da piluccare.'], size=56, layout='stack', size_mobile=38),
                      K.image(t, 'pexels-36299919.jpg', 'I lampadari di vetro di Murano sopra il bancone', ratio='4/5',
                              drift=True)], wm='2-5', ws='1-1'),
               t.col([lista], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PIOGGIA, 'Quando piove', 'Il bar resta aperto fino all’alba.', 'Con l’acqua alta o col '
                          'temporale nessuno ha voglia di uscire: si accende il camino, Elena suona ancora, e alle sei '
                          'arrivano le brioche calde.', overlay=50, video=True))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('date', 'Sera', 'sera', True), K.campo('select', 'Persone', 'persone', True, opzioni='1\n2\n3\n4\n5\n6'),
             K.campo('select', 'Dove', 'dove', opzioni='Vicino al pianoforte\nAl bancone\nNella saletta col camino'),
             K.campo('textarea', 'Note', 'note', larghezza='1-1', segnaposto='Un compleanno, una richiesta per Elena…')]
    out.append(K.sezione_modulo(t, 'tavolo', 'Un tavolo', ['Tienicelo', 'per stasera.'],
                                'I tavoli vicino alle candele sono dodici. Il bancone non si prenota: lì c’è sempre posto.',
                                VOCI, campi, 'Ca’ Notturna — tavolo al bar', 'Tieni il tavolo',
                                'Fatto: il tavolo vi aspetta fino a mezzanotte.'))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Una notte,', 'o dieci.'],
                   'Scriveteci le date e la stanza che vi piace: rispondiamo entro un’ora, anche di notte. Si paga '
                   'all’arrivo, la cancellazione è gratuita fino a tre giorni prima.',
                   bg=K.photo_bg('pexels-35794820.jpg', overlay=55), overlay_text=True)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('date', 'Arrivo', 'arrivo', True), K.campo('date', 'Partenza', 'partenza', True),
             K.campo('select', 'La stanza', 'stanza', True, opzioni='\n'.join(s[1] for s in STANZE_DATI) + '\nConsigliatemi voi'),
             K.campo('select', 'Arrivo previsto', 'ora', opzioni='Di sera\nDopo mezzanotte\nAll’alba\nDi giorno'),
             K.campo('textarea', 'Raccontateci', 'messaggio', larghezza='1-1',
                     segnaposto='Il motoscafo dall’aeroporto, un anniversario, il violoncello da custodire…')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Le date', ['Vi rispondiamo', 'entro un’ora.'],
                                'Con la conferma vi mandiamo il numero del motoscafo e la mappa per arrivare a piedi dalla '
                                'stazione o da San Zaccaria.', VOCI, campi, 'Ca’ Notturna — richiesta', 'Chiedi le date',
                                'Ricevuto: entro un’ora vi scriviamo con la conferma.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Dall’acqua,', 'o a piedi.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Dall’aeroporto', 'motoscafo fino alla porta d’acqua, 35 minuti'),
                                  ('Dalla stazione', 'vaporetto 1 o 2, fermata San Zaccaria'),
                                  ('A piedi', 'da San Marco, otto minuti'), ('La reception', 'aperta sempre, anche alle 4')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Ca’ Notturna · porta d’acqua', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · calle senza uscita', color=C['acc'], align='center'),
                       K.headline(t, 'Anche i veneziani <em>si perdono.</em>', size=88, tag='h1', align='center',
                                  pad_bottom=20),
                       K.text(t, 'Tornate verso la luce: la porta d’acqua è aperta.', size=17, align='center',
                              color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LAMPIONE, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ospitalità e viaggi', 'Sfoglia la notte',
    ['hotel', 'venezia', 'boutique hotel', 'bar', 'notte', 'velluto', 'scuro', 'slider pro'],
    'Un albergo di undici stanze in un palazzo veneziano per chi vive di notte, velluto nero e oro delle candele: '
    'l’apertura a sipario con le miniature delle ore della notte e una cartolina incorniciata che si muove col mouse, il '
    'nome che scorre sull’onda del canale, le stanze con l’anteprima che segue il cursore, il bar con la carta e gli orari '
    'e un pannello fisso con la musica di stanotte.',
    [('Le stanze', STANZE), ('Il bar', BAR), ('Come arrivare', PRENOTA + '#arrivare'), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('le-stanze', 'Le stanze', 'le-stanze'), ('il-bar', 'Il bar', 'il-bar'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Jost\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Bodoni Moda\', \'Didot\', Georgia, serif', '\'DM Mono\', ui-monospace, Menlo, monospace',
    ['Bodoni Moda', 'Jost', 'DM Mono'], corsivo_di=['Bodoni Moda'], peso_titoli='400', raggio=2, grana=5,
    titoli_line='1.02', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-stanze.json': build_stanze(), 'il-bar.json': build_bar(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
