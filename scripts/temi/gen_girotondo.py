# -*- coding: utf-8 -*-
"""Genera il tema «Girotondo» (id girotondo): un nido d'infanzia a metodo Montessori a Reggio Emilia, da 12 a 36 mesi,
con il giardino, l'orto, l'atelier e la cucina interna.

Tema chiaro e morbido: latte, terracotta, salvia e il giallo sole. Titoli in Parkinsans, testo in Inclusive Sans (fatto
per leggere bene), dati in Overpass Mono.

Apertura: Slider Pro «i giochi di legno», alto 16:9. La foto in una cornice dalle curve morbide; davanti, l'arcobaleno
di legno a cinque archi che dondola piano sul tavolo (tutti gli archi con lo stesso punto d'appoggio); in alto tre
cubi di legno che cadono uno dopo l'altro e rimbalzano a ogni diapositiva. Una diapositiva per momento del nido.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'girotondo'
NAME = 'Girotondo'

HEX = {
    'primary': '#A8462A', 'primary_contrast': '#FFFFFF', 'secondary': '#4D7C61', 'secondary_contrast': '#FFFFFF',
    'accent': '#F2B84B', 'dark': '#2B2A33', 'light': '#FFFFFF', 'muted': '#F4EBDD', 'muted_contrast': '#2B2A33',
    'text': '#2B2A33', 'text_muted': '#635C57', 'background': '#FBF6EE', 'border': '#E6D9C5', 'link': '#A8462A',
}

HOME, GIORNATA, SPAZI, ISCRIZIONI = '{{PAGINA:home}}', '{{PAGINA:la-giornata}}', '{{PAGINA:gli-spazi}}', '{{PAGINA:iscrizioni}}'
MAIL, PHONE, PHONE_TEL = 'ciao@girotondo.example', '+39 0522 000 345', 'tel:+390522000345'
LAT, LNG = '44.6990', '10.6312'

V_BLOCCHI, V_ARCO, V_PITTURA, V_PALLONCINI, V_ORTO = ('pexels-video-3678329', 'pexels-video-8426407', 'pexels-video-6965665',
                                                     'pexels-video-8384703', 'pexels-video-8297931')
PHOTOS = [
    'pexels-8435791.jpg', 'pexels-8435758.jpg', 'pexels-5275850.jpg', 'pexels-8501700.jpg', 'pexels-40472861.jpg',
    'pexels-7296528.jpg', 'pexels-7269674.jpg', 'pexels-7296764.jpg', 'pexels-7269670.jpg', 'pexels-7269619.jpg',
    'pexels-6692962.jpg', 'pexels-6692878.jpg', 'pexels-9667023.jpg', 'pexels-33971311.jpg', 'pexels-14730002.jpg',
    'pexels-34817532.jpg', 'pexels-5997590.jpg', 'pexels-13059844.jpg', 'pexels-18991702.jpg', 'pexels-4966468.jpg',
    'pexels-8384415.jpg', 'pexels-5997674.jpg', 'pexels-7898618.jpg', 'pexels-8466792.jpg', 'pexels-6978759.jpg',
    'pexels-7898451.jpg', 'pexels-7025973.jpg', 'pexels-8613089.jpg', 'pexels-8922396.jpg', 'pexels-8922393.jpg',
    'pexels-8923073.jpg', 'pexels-8538362.jpg', 'pexels-8612965.jpg', 'pexels-8467268.jpg', 'pexels-8612970.jpg',
    'pexels-8613106.jpg', 'pexels-8613105.jpg', 'pexels-8923967.jpg', 'pexels-8923553.jpg', 'pexels-8617542.jpg',
    'pexels-8617558.jpg', 'pexels-5728175.jpg', 'pexels-5471923.jpg', 'pexels-35842244.jpg', 'pexels-8290210.jpg',
    'pexels-8014314.jpg', 'pexels-5274622.jpg',
]
VIDEOS = [V_BLOCCHI, V_ARCO, V_PITTURA, V_PALLONCINI, V_ORTO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=22, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
SOLE, SALVIA = C['acc2'], C['sec']
TITOLI, MONO = 'Parkinsans', 'Overpass Mono'
VOCI = [('map-pin', 'Via dei Gelsi 4 · Reggio Emilia', ISCRIZIONI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Girotondo', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Girotondo', 'Nido d’infanzia a metodo Montessori a Reggio Emilia: trenta bambini da 12 a 36 mesi, sei '
                   'educatrici, il giardino con l’orto, l’atelier e la cucina interna. Convenzionato con il Comune.',
                   [('La giornata', GIORNATA), ('Gli spazi', SPAZI), ('Iscrizioni', ISCRIZIONI), ('Open day', ISCRIZIONI + '#openday'),
                    ('Privacy', '/privacy-policy/')],
                   'Il nido', 'Lunedì — venerdì 7:30 — 16:30<br>Prolungamento fino alle 18', VOCI,
                   '© 2026 Girotondo società cooperativa sociale · Reggio Emilia · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=140,
                   fondo=C['sala'])


MOMENTI = [  # linguetta, occhiello, riga 1, riga 2, testo, foto, fondo
    ('Il nido', '12 — 36 mesi', 'Fare da soli,', 'insieme agli altri.', 'Materiali di legno a misura di mano, scaffali '
     'bassi, il tempo per provare e riprovare. Le educatrici osservano, aiutano quando serve.', 'pexels-7296764.jpg',
     mix(C['acc2'], 18, C['fondo'])),
    ('Il giardino', 'Fuori, con ogni tempo', 'Il giardino', 'è un’aula.', 'Tremila metri quadri con l’orto, la sabbiera e '
     'gli alberi da frutto. Si esce ogni giorno, anche con la pioggia: gli stivali li abbiamo noi.', 'pexels-5997590.jpg',
     mix(C['sec'], 12, C['fondo'])),
    ('L’atelier', 'Le mani nel colore', 'Pennelli, argilla', 'e farina.', 'L’atelier ha il pavimento che si lava e i muri '
     'da dipingere. Pietro, l’atelierista, prepara i materiali; i bambini decidono che cosa farne.', 'pexels-7898618.jpg',
     mix(C['acc'], 9, C['fondo'])),
    ('La cucina', 'Il pranzo, cucinato qui', 'Si apparecchia', 'tutti insieme.', 'La cuoca cucina ogni mattina con le '
     'verdure dell’orto e del mercato. I bambini apparecchiano, si servono, sparecchiano.', 'pexels-8617542.jpg',
     mix(C['acc2'], 12, mix(C['acc'], 5, C['fondo']))),
]


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, foto, fondo) in enumerate(MOMENTI):
        n = i + 1
        sl.append(K.diapositiva('gi-%d' % n, K.sfondo_video([V_BLOCCHI, V_PITTURA, V_ORTO, V_PALLONCINI][i], overlay=0.6,
                                                            colore=C['fondo']),
                                K.veli('gi-velo%d' % n, 'alto', 58, 72) + [
            K.livello('gi-f%d' % n, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=5, y=46, w=24, h=42,
                      borderRadiusLinked=False, borderRadiusTL=220, borderRadiusTR=60, borderRadiusBR=220, borderRadiusBL=60,
                      kenBurns={'type': 'zoomIn', 'intensity': 6, 'duration': 9000}, entra='zoomIn', ritardo=500, durata=1100,
                      esce='fadeOut', boxShadow={'x': 0, 'y': 30, 'blur': 60, 'spread': -20, 'color': mix(C['nero'], 25)},
                      parallax={'type': 'mouse', 'depth': 6, 'tilt3d': True, 'scrollSpeed': 0.5},
                      responsive={'mobile': {'x': 10, 'y': 4, 'width': 80, 'height': 28}}),
            # il testo al centro, sopra l'arcobaleno
            K.livello('gi-o%d' % n, contenuto=occ, x=20, y=12.5, w=60, size=13, peso='500', colore=C['acc'], letterSpacing=1,
                      textTransform='uppercase', fontFamily=MONO, align='center', ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 42, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello('gi-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=12, y=16.5, w=76, size=64, peso='600',
                      colore=C['testo'], fontFamily=TITOLI, lineHeight=1.04, letterSpacing=-1, align='center', entra='fadeIn',
                      ritardo=350, durata=1000, charAnim={'stagger': 24, 'offsetY': 40, 'rotation': 0, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(36), 'y': 44.5, 'x': 7, 'width': 88, 'textAlign': 'left'}}),
            K.livello('gi-i%d' % n, contenuto=r2, tag='p', x=12, y=25.5, w=76, size=64, peso='600', colore=C['acc'],
                      fontFamily=TITOLI, lineHeight=1.04, letterSpacing=-1, align='center', entra='fadeIn', ritardo=650,
                      durata=1000, charAnim={'stagger': 24, 'offsetY': 40, 'rotation': 0, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(36), 'y': 48.8, 'x': 7, 'width': 88, 'textAlign': 'left'}}),
            K.livello('gi-s%d' % n, contenuto=testo, x=29, y=37.5, w=42, size=18, peso='400', colore=C['tenue'],
                      lineHeight=1.6, align='center', ritardo=950,
                      responsive={'mobile': {'fontSize': m(15), 'y': 55, 'x': 7, 'width': 86, 'textAlign': 'left'},
                                  'tablet': {'x': 20, 'width': 60}}),
            K.livello('gi-b%d' % n, tipo='button', contenuto='Vieni all’open day', x=43.1, y=52, w='auto', size=15, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, ritardo=1250,
                      buttonUrl=ISCRIZIONI + '#openday', hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 70, 'x': 7, 'fontSize': m(13)}, 'tablet': {'x': 40.7, 'y': 54}}),
        ], durata=7500, tab=tab))
    # l'arcobaleno di legno in basso al centro: cinque mezzi dischi, dal più grande al più piccolo, che dondolano
    W, H, MW, MH = 1440.0, 810.0, 640.0, 1400.0
    cx, cy, mcx, mcy = 720.0, 806.0, 520.0, 468.0       # il punto d'appoggio, sul computer e sul telefono
    colori = [C['acc'], SOLE, SALVIA, mix(C['acc'], 45, C['chiaro']), mix(SALVIA, 40, C['chiaro']), C['fondo']]
    globali = []
    for k, col in enumerate(colori):
        d, md = 300 - 50 * k, 200 - 33 * k
        x, y = (cx - d / 2) / W * 100, (cy - d / 2) / H * 100
        globali.append(K.livello('gi-arco%d' % k, tipo='shape', x=round(x, 2), y=round(y, 2), w=round(d / W * 100, 2),
                                 h=round(d / 2 / H * 100, 2), bgColor=col, borderRadiusLinked=False, borderRadiusTL=999,
                                 borderRadiusTR=999, borderRadiusBR=0, borderRadiusBL=0,
                                 timeline=K.tl(round(x, 2), round(y, 2), [(ms, {'rotation': r, 'originX': 50, 'originY': 100})
                                                                          for ms, r in [(0, -5), (1600, 5), (3200, -5)]],
                                               3200, curva='sine.inOut'),
                                 responsive={'mobile': {'x': round((mcx - md / 2) / MW * 100, 2),
                                                        'y': round((mcy - md / 2) / MH * 100, 2),
                                                        'width': round(md / MW * 100, 2), 'height': round(md / 2 / MH * 100, 2)}}))
    # i cubetti di legno che cadono uno sull'altro, a destra
    for k, (x, y_fine, col, giro) in enumerate([(80.2, 80.0, SOLE, 4), (84.0, 80.0, SALVIA, -3), (82.1, 73.6, C['acc'], 8)]):
        globali.append(K.livello('gi-cubo%d' % k, tipo='shape', x=x, y=y_fine, w=3.6, h=6.4, bgColor=col, borderRadius=8,
                                 boxShadow={'x': 0, 'y': 8, 'blur': 16, 'spread': -6, 'color': mix(C['nero'], 30)},
                                 timeline=K.tl(x, y_fine, [(0, {'y': 40, 'rotation': giro * 3, 'opacity': 0}, 'bounce.out'),
                                                           (300 + 350 * k, {'y': 40, 'rotation': giro * 3, 'opacity': 100}, 'bounce.out'),
                                                           (1300 + 350 * k, {'rotation': giro, 'opacity': 100})],
                                               1300 + 350 * k, loop=False, fine_con_slide=True),
                                 responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=globali,
                       altezza={'mode': 'ratio', 'value': '16:9'}, transizione='slideDown', durata_trans=1100,
                       showArrows=False, showDots=False, showTabs=True, tabPosition='bottom', parallax=False,
                       autoplaySpeed=7500, progressBarColor=C['acc'], navColor=C['testo'],
                       globalBackground={'type': 'color', 'color': C['fondo']})


def giornata(t):
    voci = [('7:30', 'L’accoglienza', 'Un saluto alla porta', 'Si arriva quando si vuole fino alle 9:30. Il saluto è breve e '
             'sempre uguale: aiuta a lasciarsi.', 'pexels-8613105.jpg', 'sun'),
            ('9:30', 'Lo spuntino', 'Frutta a tavola', 'Frutta di stagione tagliata dai bambini, con i coltellini di legno.',
             'pexels-8617558.jpg', 'apple'),
            ('10:00', 'Le attività', 'Atelier, giardino, sezione', 'Ognuno sceglie: i materiali Montessori, la pittura, l’orto. '
             'Le educatrici osservano e accompagnano.', 'pexels-7269674.jpg', 'puzzle'),
            ('11:30', 'Il pranzo', 'Cucinato qui', 'Si apparecchia insieme, ci si serve da soli dalle ciotole, si sparecchia.',
             'pexels-5728175.jpg', 'utensils'),
            ('13:00', 'La nanna', 'Nella stanza del sonno', 'Lettini bassi, il proprio cuscino, una canzone sempre uguale.',
             'pexels-8923073.jpg', 'moon'),
            ('15:30', 'La merenda e i saluti', 'Fino alle 16:30', 'Merenda, un libro, i genitori che arrivano. Prolungamento '
             'fino alle 18 per chi serve.', 'pexels-8922393.jpg', 'home')]
    return t.classic('timeline', {
        'items': [{'date': d, 'title': ti, 'tag': tag, 'description': de, 'image': K.img(f), 'video': '', 'icon': ic,
                   'category': 'primary'} for d, ti, tag, de, f, ic in voci],
        'tl_layout': 'alt', 'tl_reveal': 'bloom', 'tl_theme': 'paper', 'tl_card': 'bubble', 'tl_thread': 'dash',
        'tl_node': 'year', 'tl_color': 'cat', 'tl_media': 'on', 'tl_density': 'comfy', 'tl_transparent': True,
        'tl_title_size': 24, 'tl_title_weight': '600', 'tl_title_family': 'heading', 'tl_show_tag': True}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Girotondo · Reggio Emilia', ['Aiutami', 'a fare da solo.'], size=64, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'È la frase di Maria Montessori che abbiamo scritto sopra la porta. Al nido i bambini si '
                                'vestono, apparecchiano, travasano l’acqua, scelgono che cosa fare: con calma, con i '
                                'materiali giusti e un adulto che guarda senza sostituirsi.', size=18, color=C['testo']),
                      K.button(t, 'La giornata', GIORNATA, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('30', 'Bambini, in tre sezioni'), ('1 : 5', 'Un’educatrice ogni cinque'),
                            ('3.000 m²', 'Di giardino e orto'), ('0 km', 'Le verdure della cucina')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    spazi = t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': k, 'title': ti, 'link': SPAZI + '#' + a, 'span': 6, 'aspect': '4/3'}
                  for f, k, ti, a in [('pexels-8612970.jpg', 'Scaffali bassi · materiali di legno', 'Le sezioni', 'sezioni'),
                                      ('pexels-4966468.jpg', 'Orto, sabbiera, alberi da frutto', 'Il giardino', 'giardino'),
                                      ('pexels-7025973.jpg', 'Colori, argilla, farina', 'L’atelier', 'atelier'),
                                      ('pexels-5728175.jpg', 'La cuoca, ogni mattina', 'La cucina', 'cucina')]],
        'columns': 2, 'gap': 16, 'object_position': 'center center', 'object_fit': 'cover', 'radius': 22,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 30), 'arrow_color': C['chiaro'], 'arrow_hover_bg': C['acc'],
        'arrow_hover_color': C['on_acc'], 'show_arrow': True, 'title_size': 40, 'title_weight': '600', 'kicker_size': 12,
        'card_radius': radius(22)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Gli spazi', ['Quattro posti', 'per crescere.'], size=60,
                         tagline='Tutto a misura di bambino: le maniglie, i lavandini, gli specchi, gli scaffali.')]),
        t.one([spazi]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_BLOCCHI, 'Il gioco', 'Un cubo <em>sopra l’altro.</em>',
                             'Una torre che cade e si rifà dieci volte è un pomeriggio di matematica, di pazienza e di '
                             'mani. Noi non la rifacciamo al posto loro.', overlay=62))
    out.append(t.section([
        t.one([K.sechead(t, 'La giornata', ['Dalle sette e mezza', 'alla merenda.'], size=60,
                         tagline='Sempre gli stessi momenti, nello stesso ordine: i bambini sanno che cosa viene dopo, e si fidano.')]),
        t.one([giornata(t)]),
    ], padding='large', width='large', html_id='giornata', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'A due anni Pietro apparecchia la tavola da solo e si mette le scarpe. Ce l’hanno insegnato loro: prima '
                 'facevamo tutto noi, per fare prima.', 'author_name': 'Elena e Marco', 'author_role': 'Genitori di Pietro',
        'avatar': '', 'rating': '5', 'text_color': C['testo'], 'star_color': SOLE, 'author_color': C['tenue'],
        'quote_accent_color': C['acc'], 'quote_font': 'heading', 'quote_size': 36, 'author_uppercase': True,
        'quote_uppercase': False, 'layout': 'editorial', 'show_line': True, 'line_color': SOLE}, style={})])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PALLONCINI, 'Fuori', 'Si esce <em>tutti i giorni.</em>',
                          'Col sole, col vento, con la pioggia: stivali e tute impermeabili li teniamo noi, per tutti. '
                          'I bambini che stanno fuori dormono meglio e mangiano di più.', 'Gli spazi', SPAZI, overlay=62,
                          video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Girotondo',
                page_intro_caption='Nido Montessori · Reggio Emilia', page_intro_bg=HEX['background'],
                page_intro_color=HEX['primary'], page_intro_duration=1500, page_intro_once=True)


def build_giornata():
    t = Tpl(ID, 'la-giornata')
    out = [K.intro(t, 'La giornata', ['Ritmi lenti,', 'mani occupate.'],
                   'Il metodo Montessori al nido è soprattutto un ambiente preparato e un adulto che sa aspettare. Ecco come '
                   'passano le giornate.', bg=K.photo_bg('pexels-8613089.jpg', overlay=80, color=C['fondo']))]
    carte = []
    for nome, cosa, foto in [
        ('Il travaso', 'Acqua da una brocca all’altra, poi lenticchie col cucchiaio: la mano impara la precisione, la mente '
                       'la concentrazione.', 'pexels-7296764.jpg'),
        ('Gli incastri', 'Forme di legno da mettere al loro posto: il bambino si accorge da solo dell’errore e riprova.',
         'pexels-6692962.jpg'),
        ('La torre rosa', 'Dieci cubi dal più grande al più piccolo: si costruisce, si guarda, si rifà.', 'pexels-7269670.jpg'),
        ('La vita pratica', 'Vestirsi, lavarsi le mani, versare l’acqua, apparecchiare: i gesti di tutti i giorni.',
         'pexels-8290210.jpg')]:
        carte.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 58}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': 'I materiali · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': nome, 'back_description': cosa,
            'back_bg': SALVIA, 'back_text_color': C['chiaro'], 'back_valign': 'center', 'back_cta_text': '',
            'back_cta_url': '', 'flip_direction': 'horizontal', 'flip_trigger': 'hover', 'card_height': '380',
            'card_border_radius': '22', 'card_shadow': 'md', 'title_size': '28', 'title_weight': '600', 'desc_size': '16'},
            style={})], wm='1-4', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'I materiali', ['Quattro attività', 'da girare.'], size=56,
                         tagline='Passate sopra una carta, o toccatela: dietro c’è che cosa impara il bambino.')]),
        t.row(carte, layout='25-25-25-25', gap=18),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_ARCO, 'Il movimento', 'Salire, scendere, <em>cadere e rialzarsi.</em>',
                             'L’arco di legno e le pedane Pikler stanno in ogni sezione: i bambini si arrampicano quando '
                             'sono pronti, mai prima. Nessuno li mette in piedi.', overlay=62))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi passa', 'la giornata con loro.'], size=56)]),
        K.squadra(t, [
            ('pexels-8538362.jpg', 'Anna Rinaldi', 'Coordinatrice', 'Diplomata Montessori a Bergamo, guida il nido dal 2015. '
             'Fa i colloqui con le famiglie, uno per uno.', '50% 30%'),
            ('pexels-8612965.jpg', 'Sofia Marchetti', 'Educatrice', 'Nella sezione dei più grandi, ama l’orto e le canzoni in '
             'dialetto della nonna.', '50% 30%'),
            ('pexels-8467268.jpg', 'Pietro Galli', 'Atelierista', 'Pittore, prepara l’atelier ogni mattina: colori, argilla, '
             'luce. Il resto lo fanno i bambini.', '50% 30%')], sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Le domande', 'dei genitori.', [
        ('Come si fa l’ambientamento?', 'Due settimane, con un genitore presente all’inizio. Si allunga piano il tempo al '
                                        'nido, seguendo il bambino.'),
        ('E se il bambino non mangia?', 'Non si forza mai. Si assaggia, si riprova il giorno dopo: col tempo si mangia tutto.'),
        ('Come sappiamo come è andata?', 'Ogni sera due righe e una foto nell’app del nido, e un colloquio ogni tre mesi.'),
        ('Se mio figlio ha un’allergia?', 'La cuoca prepara un menu a parte, con il certificato del pediatra.'),
    ], 'gi', bg=False))
    return out


def build_spazi():
    t = Tpl(ID, 'gli-spazi')
    out = [K.intro(t, 'Gli spazi', ['Una casa', 'a misura di bambino.'],
                   'Una villa degli anni Venti col giardino, restaurata per il nido: tre sezioni, l’atelier, la cucina, la '
                   'stanza del sonno e tremila metri quadri fuori.', bg=K.photo_bg('pexels-13059844.jpg', overlay=80,
                                                                                   color=C['fondo']))]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8612970.jpg', 'Una sezione con gli scaffali bassi e i giochi di legno', ratio='4/3')],
                     wm='1-2', ws='1-1'),
               t.col([K.sechead(t, 'Le sezioni', ['Tutto', 'all’altezza giusta.'], size=48, layout='stack', size_mobile=32),
                      K.text(t, 'Gli scaffali arrivano alla vita dei bambini, gli specchi partono da terra, i lavandini sono '
                                'bassi. Ogni materiale ha il suo posto: si prende, si usa, si rimette dov’era.'),
                      K.specs(t, [('Piccoli', '12 — 18 mesi · 8 bambini'), ('Medi', '18 — 24 mesi · 10 bambini'),
                                  ('Grandi', '24 — 36 mesi · 12 bambini')])], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], padding='large', width='large', html_id='sezioni', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_ORTO, 'L’orto', 'Le zucchine <em>le hanno piantate loro.</em>',
                             'Da marzo a ottobre ogni sezione ha la sua aiuola: si semina, si annaffia, si raccoglie e si '
                             'porta in cucina. Le zucchine del nido sono le più buone di Reggio.', overlay=62))
    out.append(t.section([
        t.one([K.sechead(t, 'L’atelier', ['Colori, argilla,', 'farina e acqua.'], size=56,
                         tagline='Trascinate o lasciate scorrere: sono i lavori di questo mese.')]),
        t.one([K.coverflow(t, [
            ('pexels-7898618.jpg', 'Un bambino dipinge un’anguria', 'L’anguria', 'Tempere · 3 anni'),
            ('pexels-8466792.jpg', 'Un bambino con gli acquerelli', 'Il mare', 'Acquerello · 2 anni'),
            ('pexels-6978759.jpg', 'Una bambina dipinge il muro', 'Il muro dell’atelier', 'Tutti insieme'),
            ('pexels-35842244.jpg', 'Mani che modellano la pasta colorata', 'La pasta di sale', 'Colorata con le verdure'),
            ('pexels-5471923.jpg', 'Biscotti a forma di zucca', 'I biscotti', 'Con la cuoca'),
            ('pexels-5274622.jpg', 'Un sole disegnato coi gessetti', 'I gessetti', 'In cortile')], altezza=400, larghezza=320)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='atelier', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La cucina', ['Si cucina qui,', 'ogni mattina.'], size=48, layout='stack', size_mobile=32),
                      K.text(t, 'Menu di stagione approvato dalla pediatra dell’Ausl: verdure dell’orto e del mercato, '
                                'pasta fresca, pochissimo sale. Le diete speciali si cucinano a parte.')], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-8617542.jpg', 'Bambini a pranzo, seduti a un tavolo basso', ratio='4/3')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=64, valign='center'),
    ], padding='large', width='large', html_id='cucina', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PITTURA, 'Il giardino', 'Fuori, <em>con ogni tempo.</em>',
                          'Sabbiera coperta, casetta di legno, alberi da frutto e un prato per correre. D’estate il nido '
                          'si sposta quasi tutto sotto i tigli.', 'Vieni all’open day', ISCRIZIONI + '#openday', overlay=62,
                          video=True))
    return out


def build_iscrizioni():
    t = Tpl(ID, 'iscrizioni')
    out = [K.intro(t, 'Iscrizioni', ['Trenta posti,', 'un colloquio.'],
                   'Le iscrizioni per settembre si aprono a gennaio. Prima venite a vederci all’open day, poi facciamo un '
                   'colloquio con calma.', bg=K.photo_bg('pexels-8922396.jpg', overlay=80, color=C['fondo']), size=96)]
    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2027-01-16T10:00', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': False, 'expired_message': 'Le porte sono aperte!', 'label_days': 'giorni',
        'label_hours': 'ore', 'label_minutes': 'minuti', 'text_color': C['testo'], 'accent_color': C['acc'],
        'number_font_size': 72, 'number_font_weight': '600', 'label_font_size': '13', 'item_min_width': 150,
        'item_bg_color': C['chiaro'], 'item_radius': radius(22),
        'item_padding': {'top': 30, 'right': 20, 'bottom': 22, 'left': 20}}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’open day', ['Sabato 16 gennaio,', 'porte aperte.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Dalle 10 alle 13: si visitano le sezioni, l’atelier e il giardino, si conoscono le educatrici. '
                                'I bambini possono giocare mentre voi fate domande.')], wm='2-5', ws='1-1'),
               t.col([conto], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', html_id='openday', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le rette', ['Tre orari,', 'pasti compresi.'], size=56,
                         tagline='Il nido è convenzionato: col contributo del Comune la retta scende in base all’ISEE.')]),
        K.piani(t, [('Part time', '420', 'al mese · 7:30 — 13', 'Pranzo compreso\nSpuntino e pannolini\nAmbientamento di due '
                     'settimane', False, ''),
                    ('Tempo pieno', '560', 'al mese · 7:30 — 16:30', 'Pranzo e merenda\nLa nanna al nido\nPannolini e corredo',
                     True, 'Il più scelto'),
                    ('Prolungato', '640', 'al mese · fino alle 18', 'Tutto il tempo pieno\nUna seconda merenda\nUn’educatrice '
                     'dedicata', False, '')],
                'Chiedi un colloquio', ISCRIZIONI + '#colloquio', nota='Retta ridotta con l’ISEE · fratelli −10%', sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome del genitore', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('text', 'Nome del bambino', 'bambino', True), K.campo('date', 'Data di nascita', 'nascita', True),
             K.campo('select', 'Da quando', 'quando', opzioni='Settembre\nGennaio\nAppena possibile'),
             K.campo('select', 'Orario', 'orario', opzioni='Part time\nTempo pieno\nProlungato'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Qualcosa su di lui o di lei', 'messaggio', larghezza='1-1',
                     segnaposto='Abitudini, sonno, allergie, quello che vi sembra importante.')]
    out.append(K.sezione_modulo(t, 'colloquio', 'Il colloquio', ['Un’ora,', 'con calma.'],
                                'Vi richiamiamo entro tre giorni per fissare il colloquio con la coordinatrice. Venite con '
                                'il bambino, se volete.', VOCI, campi, 'Girotondo — colloquio', 'Chiedi il colloquio',
                                'Grazie! Vi richiamiamo entro tre giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Nella villa', 'coi tigli.'], size=52, layout='stack', size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via dei Gelsi 4, Reggio Emilia'), ('Parcheggio', 'davanti al cancello, dieci posti'),
                                  ('Passeggini', 'il portico coperto'), ('In bici', 'la ciclabile del parco')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Girotondo · nido Montessori', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · giro a vuoto', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è andata a nanna.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Facciamo un altro girotondo e torniamo all’inizio.', size=18, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna al nido', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_PALLONCINI, overlay=62), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Cultura e formazione', 'L’arcobaleno che dondola',
    ['nido', 'asilo', 'montessori', 'infanzia', 'reggio emilia', 'chiaro', 'slider pro'],
    'Un nido Montessori, latte, terracotta e salvia: in apertura il titolo al centro sopra il video del nido, '
    'l’arcobaleno di legno a cinque archi che dondola, i cubi che cadono e rimbalzano e la foto dalle curve morbide; '
    'la giornata in sei momenti, i materiali su carte da girare, gli spazi e le iscrizioni.',
    [('La giornata', GIORNATA), ('Gli spazi', SPAZI), ('Iscrizioni', ISCRIZIONI), ('Open day', ISCRIZIONI + '#openday')],
    [('home', 'Home', None), ('la-giornata', 'La giornata', 'la-giornata'), ('gli-spazi', 'Gli spazi', 'gli-spazi'),
     ('iscrizioni', 'Iscrizioni', 'iscrizioni')],
    HEX, '\'Inclusive Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Parkinsans\', system-ui, sans-serif',
    '\'Overpass Mono\', ui-monospace, Menlo, monospace', ['Parkinsans', 'Inclusive Sans', 'Overpass Mono'], peso_titoli='600',
    raggio=22, grana=0, titoli_line='1.04', titoli_spazio='-0.015em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '3'  # apertura scura sul video pieno, 31 ottobre 2026


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-giornata.json': build_giornata(), 'gli-spazi.json': build_spazi(), 'iscrizioni.json': build_iscrizioni(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
