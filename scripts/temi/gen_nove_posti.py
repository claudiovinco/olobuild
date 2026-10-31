# -*- coding: utf-8 -*-
"""Genera il tema «Nove Posti» (id nove-posti): un omakase a Milano, nove sgabelli attorno a un banco di hinoki, lo
chef che decide il percorso della sera e lo serve un pezzo alla volta.

Tema scuro e silenzioso: nero d'inchiostro, avorio, il rosso lacca e l'oro opaco delle ceramiche. Titoli in Cormorant
(anche corsivo), testo in Instrument Sans, dati in Xanh Mono.

Apertura: Slider Pro «il banco», alto 16:9 perché i cerchi restino cerchi. Una diapositiva per momento del percorso
(Sakizuke, Sashimi, Nigiri, Aburi, Wagashi) con le linguette in basso: il piatto visto dall'alto in una finestra
rotonda che si inclina in 3D verso il puntatore, un anello tratteggiato d'oro che gira piano attorno e un punto che
ci corre sopra (percorso di movimento), il nome del momento enorme e contornato, il titolo che entra lettera per
lettera. Nell'aburi la finestra è un video: la fiamma sul nigiri.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'nove-posti'
NAME = 'Nove Posti'

HEX = {
    'primary': '#E0644A', 'primary_contrast': '#120D0A', 'secondary': '#3E5A7A', 'secondary_contrast': '#FFFFFF',
    'accent': '#C8A869', 'dark': '#0A0908', 'light': '#F3EDE3', 'muted': '#191715', 'muted_contrast': '#EDE6DA',
    'text': '#EDE6DA', 'text_muted': '#A39A8E', 'background': '#0F0E0D', 'border': '#2E2A26', 'link': '#E0644A',
}

HOME, PERCORSO, BANCO, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:il-percorso}}', '{{PAGINA:il-banco}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'banco@noveposti.example', '+39 02 0000 0909', 'tel:+390200000909'
LAT, LNG = '45.4544', '9.1738'

V_TONNO, V_RISO, V_FUOCO, V_MATCHA, V_TE = ('pexels-video-10432045', 'pexels-video-8901942', 'pexels-video-5789855',
                                           'pexels-video-8507724', 'pexels-video-8507751')
PHOTOS = [
    'pexels-12878171.jpg', 'pexels-28574963.jpg', 'pexels-8672034.jpg', 'pexels-28574964.jpg', 'pexels-32482533.jpg',
    'pexels-8951134.jpg', 'pexels-17597136.jpg', 'pexels-8951246.jpg', 'pexels-8951090.jpg', 'pexels-8951040.jpg',
    'pexels-9424910.jpg', 'pexels-8951048.jpg', 'pexels-359993.jpg', 'pexels-39691941.jpg', 'pexels-39691958.jpg',
    'pexels-33432672.jpg', 'pexels-33432671.jpg', 'pexels-28701153.jpg', 'pexels-8672038.jpg', 'pexels-29934980.jpg',
    'pexels-23436801.jpg', 'pexels-31313540.jpg', 'pexels-31871322.jpg', 'pexels-5975980.jpg', 'pexels-7451969.jpg',
    'pexels-10432642.jpg', 'pexels-18324427.jpg', 'pexels-8136881.jpg', 'pexels-7378470.jpg', 'pexels-31346219.jpg',
    'pexels-31317040.jpg', 'pexels-39691961.jpg', 'pexels-39692090.jpg', 'pexels-39691946.jpg', 'pexels-39691939.jpg',
    'pexels-8093408.jpg', 'pexels-39692081.jpg', 'pexels-39692086.jpg', 'pexels-39692079.jpg', 'pexels-39692077.jpg',
    'pexels-33037261.jpg', 'pexels-8952818.jpg', 'pexels-1576405.jpg', 'pexels-7300469.jpg', 'pexels-23436810.jpg',
    'pexels-37400025.jpg', 'pexels-23436814.jpg', 'pexels-8092347.jpg',
]
VIDEOS = [V_TONNO, V_RISO, V_FUOCO, V_MATCHA, V_TE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True)
C = K.c
ORO = C['acc2']
TITOLI, MONO = 'Cormorant', 'Xanh Mono'
VOCI = [('map-pin', 'Via Vigevano 9 · Milano', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Nove Posti', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Nove Posti', 'Un banco di hinoki, nove sgabelli, uno chef. Omakase sui Navigli dal 2019: il pesce '
                   'del mattino, il riso a temperatura di mano, il sake scelto per ogni momento.',
                   [('Il percorso', PERCORSO), ('Il banco', BANCO), ('Prenota', PRENOTA), ('Come arrivare', PRENOTA + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Il banco', 'Martedì — sabato<br>Due turni: 19:00 e 21:30<br>Domenica e lunedì chiuso', VOCI,
                   '© 2026 Nove Posti srl · Milano · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=130)


MOMENTI = [  # linguetta, occhiello, riga 1, riga 2, testo, foto o video, prezzo/nota
    ('Sakizuke', '01 · l’apertura', 'Si comincia', 'dal silenzio.', 'Un piccolo piatto freddo e un sorso d’acqua di '
     'sorgente: lo chef guarda come mangiate, e decide il resto della sera.', ('foto', 'pexels-31317040.jpg')),
    ('Sashimi', '02 · il taglio', 'Il tonno', 'riposa quattro giorni.', 'Lo tagliamo davanti a voi, contro fibra, alla '
     'temperatura giusta. Tre pesci, tre spessori, una salsa di soia fatta in casa.', ('foto', 'pexels-33432671.jpg')),
    ('Nigiri', '03 · il cuore', 'Un boccone,', 'con le mani.', 'Il riso è a trentasei gradi, come la mano che lo stringe. '
     'Arriva uno alla volta: si mangia subito, in un morso solo.', ('foto', 'pexels-39691941.jpg')),
    ('Aburi', '04 · il fuoco', 'Tre secondi', 'di fiamma.', 'La ricciola e il salmone passano sotto il cannello: il '
     'grasso si scioglie, la superficie profuma di brace. Dentro resta crudo.', ('video', V_FUOCO)),
    ('Wagashi', '05 · la chiusa', 'Il dolce', 'che sa di stagione.', 'Mochi di riso, gelatina di yuzu, il tè verde '
     'battuto al banco. Poi l’ultimo sorso di sake, se vi va.', ('foto', 'pexels-39692086.jpg')),
]


def apertura(t):
    m = K.mob
    sl = []
    # la finestra rotonda: 560 px su una tela di 1440 × 810 (16:9)
    px, py, pw, ph = 52.0, 15.0, 38.9, 69.1
    for i, (tab, occ, r1, r2, testo, media) in enumerate(MOMENTI):
        n = i + 1
        finestra = dict(x=px, y=py, w=pw, h=ph, borderRadius=999, entra='zoomIn', ritardo=150, durata=1400, esce='fadeOut',
                        boxShadow={'x': 0, 'y': 40, 'blur': 120, 'spread': -10, 'color': mix(C['acc'], 26)},
                        parallax={'type': 'mouse', 'depth': 7, 'tilt3d': True, 'scrollSpeed': 0.5},
                        responsive={'mobile': {'x': 17.2, 'y': 6, 'width': 65.6, 'height': 30}})
        if media[0] == 'video':
            piatto = K.livello('np-p%d' % n, tipo='video', videoSrc=K.img(media[1] + '.mp4'), videoAutoplay=True,
                               videoMuted=True, videoLoop=True, **finestra)
        else:
            piatto = K.livello('np-p%d' % n, tipo='image', imageSrc=K.img(media[1]), objectFit='cover',
                               kenBurns={'type': 'zoomIn', 'intensity': 6, 'duration': 9000}, **finestra)
        sfondo = K.sfondo_video([V_TE, V_TONNO, V_RISO, V_TONNO, V_MATCHA][i], overlay=0.8, colore=C['fondo'])
        sl.append(K.diapositiva('np-%d' % n, sfondo, [
            # il nome del momento, enorme e contornato, dietro al piatto
            K.livello('np-n%d' % n, contenuto=tab, x=30, y=66, w=66, size=230, peso='500', fontStyle='italic',
                      colore='transparent', align='right', fontFamily=TITOLI, lineHeight=0.9, textStrokeWidth=1,
                      textStrokeColor=mix(ORO, 55), entra='maskFromLeft', ritardo=300, durata=1800, esce='fadeOut',
                      responsive={'mobile': {'x': 4, 'y': 34, 'width': 92, 'fontSize': m(76)}, 'tablet': {'visible': False}}),
            piatto,
            K.livello('np-o%d' % n, contenuto=occ, x=6, y=19, w=40, size=12, peso='400', colore=ORO, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 43, 'x': 7, 'width': 86}}),
            K.livello('np-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=24, w=46, size=70, peso='500',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.0, entra='fadeIn', ritardo=380, durata=1300,
                      charAnim={'stagger': 30, 'offsetY': 28, 'blur': 10, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(33), 'y': 45.6, 'x': 7, 'width': 88}}),
            K.livello('np-i%d' % n, contenuto=r2, tag='p', x=6, y=34, w=46, size=70, peso='500', fontStyle='italic',
                      colore=C['acc'], fontFamily=TITOLI, lineHeight=1.0, entra='fadeIn', ritardo=700, durata=1300,
                      charAnim={'stagger': 30, 'offsetY': 28, 'blur': 10, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(33), 'y': 50.6, 'x': 7, 'width': 88}}),
            K.livello('np-s%d' % n, contenuto=testo, x=6, y=48, w=32, size=17, peso='400', colore=mix(C['chiaro'], 80),
                      lineHeight=1.65, ritardo=1100, responsive={'mobile': {'fontSize': m(14), 'y': 57.5, 'x': 7, 'width': 86}}),
            K.livello('np-b%d' % n, tipo='button', contenuto='Prenota un posto', x=6, y=73, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1400, buttonUrl=PRENOTA, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 70, 'x': 7, 'fontSize': m(11)}}),
            K.livello('np-l%d' % n, tipo='button', contenuto='Il percorso', x=20.5, y=73, w='auto', size=12, peso='600',
                      colore=C['chiaro'], bgColor='transparent', padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, borderWidth=1, borderColor=mix(C['chiaro'], 45), ritardo=1550, buttonUrl=PERCORSO,
                      hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 70, 'x': 50, 'fontSize': m(11)}}),
        ], durata=8000, tab=tab))
    # l'anello d'oro tratteggiato che gira piano attorno alla finestra, e il punto che ci corre sopra
    ax, ay, aw, ah = 49.23, 10.0, 44.44, 79.0
    globali = [
        K.livello('np-anello', tipo='shape', x=ax, y=ay, w=aw, h=ah, bgColor='transparent', borderRadius=999,
                  borderWidth=2, borderStyle='dashed', borderColor=mix(ORO, 75),
                  timeline=K.tl(ax, ay, [(0, {'rotation': 0}), (120000, {'rotation': 360})], 120000),
                  responsive={'mobile': {'x': 12.5, 'y': 3.85, 'width': 75, 'height': 34.3}}),
        K.livello('np-punto', tipo='shape', x=71.1, y=9.4, w=0.7, h=1.24, bgColor=C['acc'], borderRadius=999,
                  entra='fadeIn', ritardo=1200, durata=800,
                  boxShadow={'x': 0, 'y': 0, 'blur': 14, 'spread': 2, 'color': mix(C['acc'], 70)},
                  motionPath={'path': 'M 0 0 A 320 320 0 1 1 0 640 A 320 320 0 1 1 0 0', 'duration': 16000,
                              'autoRotate': False, 'loop': True, 'easing': 'linear'},
                  responsive={'mobile': {'visible': False}}),
        K.livello('np-turni', contenuto='Nove posti · due turni, 19:00 e 21:30', x=6, y=86, w=40, size=11, peso='400',
                  colore=mix(C['chiaro'], 62), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, entra='fadeIn',
                  ritardo=1800, responsive={'mobile': {'y': 77, 'x': 7, 'width': 86, 'fontSize': m(10)}}),
    ]
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='cubeV',
                       durata_trans=1200, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=C['acc'], navColor=C['chiaro'])


def percorso(t):
    voci = [('Sakizuke', 'L’apertura · freddo', 'Un assaggio di stagione, l’acqua di sorgente.', 'pexels-39691946.jpg', 'apertura'),
            ('Sashimi', 'Il taglio · tre pesci', 'Tonno maturato, ricciola, gambero rosso.', 'pexels-33432671.jpg', 'taglio'),
            ('Nigiri', 'Il cuore · dodici pezzi', 'Riso a 36 °C, uno alla volta, con le mani.', 'pexels-12878171.jpg', 'nigiri'),
            ('Aburi', 'Il fuoco · tre secondi', 'Il cannello sul grasso del pesce, dentro crudo.', 'pexels-28574963.jpg', 'nigiri'),
            ('Wagashi', 'La chiusa · tè battuto', 'Mochi, yuzu e matcha al banco.', 'pexels-39692081.jpg', 'chiusa')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': PERCORSO + '#' + a,
                   'color': C['acc']} for k, (n, s, d, f, a) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': C['tenue'],
        'number_hover_color': C['acc'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 56, 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': ORO, 'sub_size': 12,
        'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 24, 'hover_indent': 28,
        'hover_bg': mix(C['acc'], 7), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image', 'peek_width': 260,
        'peek_ratio': '1/1', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Nove Posti · Milano', ['Nove sgabelli,', 'un banco di hinoki.'], size=64, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Non c’è un menu da scegliere: c’è lo chef, il pesce arrivato la mattina e due ore davanti a '
                                'voi. Diciotto passaggi, uno alla volta, nel ritmo che date voi. Il banco è di legno di '
                                'hinoki, profuma ancora: l’ha montato un falegname di Kyoto in tre settimane.', size=17,
                             color=C['testo']),
                      K.button(t, 'Il percorso', PERCORSO, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('9', 'Posti al banco'), ('18', 'Passaggi in due ore'), ('2', 'Turni: 19:00 e 21:30'),
                            ('4', 'Giorni di riposo del tonno')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il percorso della sera', ['Cinque momenti,', 'un solo ritmo.'], size=60,
                         tagline='Passate sopra un momento: il piatto si affaccia. Ogni sera cambia qualcosa, il ritmo resta.')]),
        t.one([percorso(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_FUOCO, 'Aburi', 'Il fuoco, <em>per tre secondi.</em>',
                             'Il cannello passa sul nigiri e si spegne: il grasso del salmone si scioglie appena, il riso resta '
                             'tiepido. Si mangia prima che la fiamma smetta di profumare.', overlay=58))
    out.append(t.section([
        t.one([K.sechead(t, 'Dal banco', ['I piatti', 'di questa stagione.'], size=60,
                         tagline='Trascinate, o lasciate scorrere: la ceramica è fatta a mano a Shigaraki e a Faenza.')]),
        t.one([K.coverflow(t, [
            ('pexels-33432671.jpg', 'Sashimi di tonno con caviale e fiori', 'Tonno e caviale', 'Sashimi · ottobre'),
            ('pexels-39691958.jpg', 'Sashimi su un piatto decorato', 'Ricciola', 'Sashimi · il taglio'),
            ('pexels-12878171.jpg', 'Tre nigiri di salmone e tonno', 'Tre nigiri', 'Il cuore'),
            ('pexels-29934980.jpg', 'Un temaki su un piatto scuro', 'Temaki', 'A metà sera'),
            ('pexels-39691946.jpg', 'Una zuppa verde in una ciotola di ceramica', 'Zuppa di alghe', 'Sakizuke'),
            ('pexels-28574963.jpg', 'Un nigiri di salmone scottato', 'Salmone aburi', 'Il fuoco'),
            ('pexels-39692079.jpg', 'Un dolce giapponese su ceramica scura', 'Kaki e kuzu', 'Wagashi'),
            ('pexels-39692077.jpg', 'Una gelatina di yuzu', 'Gelatina di yuzu', 'Wagashi')], altezza=440, larghezza=340)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Ci siamo seduti alle sette e mezza e alzati alle dieci senza guardare l’ora. Il nigiri di ricciola con lo '
                 'yuzu me lo ricordo ancora adesso.', 'author_name': 'Francesca R.', 'author_role': 'Al banco a settembre',
        'avatar': '', 'rating': '5', 'text_color': C['testo'], 'star_color': ORO, 'author_color': C['tenue'],
        'quote_accent_color': C['acc'], 'quote_font': 'heading', 'quote_size': 42, 'author_uppercase': True,
        'quote_uppercase': False, 'layout': 'editorial', 'show_line': True, 'line_color': ORO}, style={})])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_TE, 'Il sake e il tè', 'Dodici etichette, <em>a temperatura.</em>',
                          'Il sake lo scegliamo per ogni momento, freddo, a temperatura di stanza o tiepido. Chi non beve '
                          'alcol ha i tè di Uji, infusi al banco.', 'Il percorso del sake', PERCORSO + '#sake', overlay=60,
                          video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Nove Posti',
                page_intro_caption='Omakase · Milano', page_intro_bg=HEX['background'], page_intro_color=HEX['accent'],
                page_intro_duration=1800, page_intro_once=True)


def build_percorso():
    t = Tpl(ID, 'il-percorso')
    out = [K.intro(t, 'Il percorso', ['Diciotto passaggi,', 'una sola sera.'],
                   'Non si sceglie: si decide quanto, e lo chef fa il resto. Diteci prima allergie e cose che non amate, al '
                   'banco cambiamo al volo.', bg=K.photo_bg('pexels-9424910.jpg', overlay=62), overlay_text=True)]
    out.append(t.section([
        t.one([K.sechead(t, 'Tre percorsi', ['Quanto volete', 'restare al banco.'], size=56,
                         tagline='Il prezzo è a persona, acqua di sorgente e tè compresi. Il turno è di due ore.')]),
        K.piani(t, [('Kaiseki breve', '120', 'a persona · 12 passaggi', 'Sakizuke e sashimi\nOtto nigiri\nWagashi e tè '
                     'battuto\nUn’ora e mezza', False, ''),
                    ('Omakase', '160', 'a persona · 18 passaggi', 'Sakizuke, sashimi e aburi\nDodici nigiri, il tonno in tre '
                     'tagli\nWagashi e tè battuto\nDue ore al banco', True, 'Il percorso intero'),
                    ('Omakase e sake', '220', 'a persona · 18 + 6 calici', 'Il percorso intero\nSei sake scelti per i '
                     'momenti\nLo racconta Aiko al banco\nAnche senza alcol: 190', False, '')],
                'Prenota', PRENOTA, nota='Coperto e servizio compresi'),
    ], padding='large', width='large', html_id='apertura', style={'padding_top': 110, 'padding_bottom': 110}))
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Arrivate puntuali', 'description': 'Il turno parte per tutti insieme: il riso '
                   'è pronto a quell’ora, e il primo pezzo esce per i nove.'},
                  {'number': '02', 'title': 'Il nigiri con le mani', 'description': 'Si può, anzi si deve: si gira e si '
                   'intinge dal lato del pesce. Le bacchette vanno bene lo stesso.'},
                  {'number': '03', 'title': 'Lo chef condisce', 'description': 'Soia, sale, agrumi: ogni pezzo arriva già '
                   'pronto. La ciotolina di soia serve per lo zenzero.'},
                  {'number': '04', 'title': 'Il ritmo lo date voi', 'description': 'Lo chef guarda il piatto vuoto: se '
                   'rallentate, rallenta anche lui. Nessuno vi porta via niente.'}],
        'columns': 4, 'gap': 18, 'number_style': 'plain', 'number_color': ORO, 'number_size': 40, 'number_font': 'heading',
        'number_weight': '500', 'title_color': C['testo'], 'title_size': 24, 'title_weight': '500', 'title_font': 'heading',
        'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left', 'card_bg': C['fondo'], 'card_border': '',
        'card_radius': radius(2), 'card_padding': 28, 'columns_tablet': 2, 'columns_mobile': 1}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Al banco', ['Quattro cose', 'da sapere.'], size=56)]),
        t.one([passi]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='nigiri', style={'padding_top': 120, 'padding_bottom': 120}))
    sake = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in [
                      ('Junmai daiginjo · Niigata', 'Freddo, 8 °C. Pulito, quasi d’acqua: per il sashimi.', '14 €', 'pexels-31871322.jpg', ''),
                      ('Junmai · Hyogo', 'A temperatura di stanza. Riso e frutta secca: per il tonno grasso.', '11 €', 'pexels-31313540.jpg', 'Con il toro'),
                      ('Kimoto · Fukushima', 'Tiepido, 45 °C. Profondo, lattico: per l’aburi.', '12 €', 'pexels-5975980.jpg', ''),
                      ('Tè verde di Uji', 'Gyokuro infuso a 60 °C, senza alcol, tutta la sera.', '8 €', 'pexels-33037261.jpg', '')]],
        'separator_style': 'dotted', 'separator_color': mix(ORO, 40), 'title_color': C['testo'], 'price_color': ORO,
        'description_color': C['tenue'], 'image_size': 76, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': C['acc'], 'badge_color': C['on_acc'], 'badge_border_radius': '2',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il sake', ['Al calice,', 'a temperatura.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Aiko Mori sceglie dodici etichette a stagione, da piccole cantine. Al banco ve ne racconta '
                                'una per momento: freddo per il crudo, tiepido per il fuoco.'),
                      K.image(t, 'pexels-8952818.jpg', 'Il tè battuto al banco', ratio='4/3')], wm='2-5', ws='1-1'),
               t.col([sake], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', html_id='sake', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MATCHA, 'Wagashi', 'Il tè, <em>battuto al banco.</em>',
                             'L’ultimo gesto della sera: il frustino di bambù nella ciotola, venti secondi, la schiuma fine. '
                             'Con il dolce di stagione.', overlay=60))
    out.append(K.domande(t, 'Prima', 'di sedervi al banco.', [
        ('Avete un menu vegetariano?', 'Sì, va chiesto alla prenotazione: diciotto passaggi di verdure, alghe, tofu fatto '
                                       'in casa e riso. Stesso prezzo.'),
        ('E se ho un’allergia?', 'Scrivetela quando prenotate. Al banco lo chef la ricorda pezzo per pezzo: crostacei, '
                                 'glutine e sesamo li togliamo senza problemi.'),
        ('Si può venire in uno?', 'Sì, e capita spesso: il banco è il posto migliore per mangiare da soli.'),
        ('Quanto dura?', 'Due ore, dal primo all’ultimo passaggio. Il secondo turno inizia alle 21:30.'),
    ], 'np', bg=True, nota='Per tutto il resto scriveteci: rispondiamo dalle 14 alle 18.'))
    return out


def build_banco():
    t = Tpl(ID, 'il-banco')
    out = [K.intro(t, 'Il banco', ['Uno chef,', 'nove ospiti.'],
                   'Kenji Arai ha imparato a Tokyo, dieci anni da un maestro di Ginza. A Milano ha voluto un banco solo, '
                   'perché il sushi si serve a mano e si mangia subito.', bg=K.photo_bg('pexels-8951246.jpg', overlay=64),
                   overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-23436810.jpg', 'Lo chef Kenji Arai con i suoi coltelli', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Kenji Arai · chef', color=ORO),
                      t.classic('quotation', {
                          'content': 'Il riso è la metà del sushi. L’altra metà è il momento in cui lo mangiate: un minuto '
                                     'dopo è già un altro piatto.', 'author': 'Kenji Arai, al banco',
                          'style': 'default', 'alignment': 'left', 'content_font_family': 'heading', 'content_font_size': '44',
                          'content_font_weight': '500', 'content_font_style': 'italic', 'content_color': C['testo'],
                          'author_color': C['tenue'], 'author_font_size': '15', 'text_effect': 'none'}, style={}),
                      K.text(t, 'Il riso è un koshihikari di Niigata, lavato tre volte e condito con due aceti di riso '
                                'invecchiati. Il pesce arriva ogni mattina: il tonno lo lasciamo riposare quattro giorni, '
                                'avvolto nella carta, perché diventi più dolce.', pad=(20, 0, 0, 0))],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    giornata = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': True, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('05', 'Il mattino', 'pexels-18324427.jpg', 'Il mercato', 'Il pesce', 'del giorno', 'Kenji sceglie al mercato '
             'ittico: occhi, branchie, il suono del pesce quando lo tocca.', '05:30', 'il mercato'),
            ('12', 'Il pomeriggio', 'pexels-31346219.jpg', 'Il riso', 'Lavato', 'tre volte', 'Si cuoce nella pentola di ghisa, '
             'si condisce col ventaglio e si tiene a 36 gradi nella ciotola di legno.', '36 °C', 'nella ciotola'),
            ('19', 'La sera', 'pexels-8951040.jpg', 'Il banco', 'Il primo', 'taglio', 'Il pesce esce dalla carta un momento '
             'prima, si taglia davanti a voi, si serve a mano.', '19:00', 'il primo turno')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': C['acc'],
        'counter_font_family': 'heading', 'counter_size': 88, 'counter_color': ORO, 'counter_italic': True,
        'counter_weight': '500', 'tag_color': C['chiaro'], 'pre_title_color': C['acc'], 'title_font_family': 'heading',
        'title_size': 34, 'title_weight': '500', 'title_color': C['chiaro'], 'title_accent_color': C['acc'],
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'clock', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(2)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Una giornata', ['Dal mercato', 'al primo taglio.'], size=56)]),
        t.one([giornata]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Tre persone,', 'nove ospiti.'], size=56)]),
        K.squadra(t, [
            ('pexels-23436810.jpg', 'Kenji Arai', 'Chef al banco', 'Dieci anni a Ginza, poi Milano. Taglia, condisce e serve '
             'ogni pezzo: al banco non c’è nessun altro.', '50% 25%'),
            ('pexels-8092347.jpg', 'Aiko Mori', 'Sake e cucina calda', 'Sommelier di sake, cura la zuppa, i piatti caldi e il '
             'tè. Sceglie le dodici etichette della stagione.', '50% 30%'),
            ('pexels-23436814.jpg', 'Daichi Ono', 'Il taglio del mattino', 'Pulisce e fa riposare il pesce, prepara il riso. '
             'Da due anni impara il banco.', '50% 30%')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([t.classic('scrubtext', {
            'text': 'Al banco non si aspetta il piatto: <em>il piatto aspetta voi.</em>',
            'show_lead': True, 'lead': 'Ogni nigiri arriva quando avete finito il precedente. Per questo i posti sono nove: '
                                       'di più, non ci si riuscirebbe.',
            'scroll_reveal': True, 'dim_opacity': 18, 'accent': C['acc'], 'text_color': C['testo'], 'lead_color': C['tenue'],
            'size_min': 34, 'size_max': 68, 'max_width_ch': 22, 'lead_size': 18, 'text_font_weight': '500',
            'text_align': 'center'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_RISO, 'Il riso', 'Trentasei gradi, <em>come la mano.</em>',
                          'Il riso non si tiene in frigo e non si scalda: resta nella ciotola di legno coperta da un telo, '
                          'alla temperatura del palmo.', 'Prenota un posto', PRENOTA, overlay=60, video=True))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Due turni,', 'nove posti.'],
                   'Le prenotazioni aprono il primo del mese per il mese dopo. Per gruppi da sei a nove il banco è vostro: '
                   'scriveteci.', bg=K.photo_bg('pexels-1576405.jpg', overlay=60), overlay_text=True, size=96)]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Martedì — sabato', 'time': '19:00', 'note': 'Primo turno, due ore'},
                  {'day': 'Martedì — sabato', 'time': '21:30', 'note': 'Secondo turno, due ore'},
                  {'day': 'Domenica e lunedì', 'time': 'Chiuso', 'note': 'Il lunedì Kenji va al mercato di Chioggia'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': ORO, 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 48,
        'time_weight': '500', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    out.append(t.section([t.one([orari])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 60}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('date', 'Giorno', 'giorno', True), K.campo('select', 'Turno', 'turno', True, opzioni='19:00\n21:30'),
             K.campo('select', 'Quanti siete', 'persone', True, opzioni='1\n2\n3\n4\n5\n6 — 9 (il banco intero)'),
             K.campo('select', 'Percorso', 'percorso', True, opzioni='Kaiseki breve · 120 €\nOmakase · 160 €\nOmakase e sake · 220 €'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Allergie e cose che non amate', 'messaggio', larghezza='1-1',
                     segnaposto='Crostacei, glutine, sesamo, il riccio di mare… lo chef lo ricorda pezzo per pezzo.')]
    out.append(K.sezione_modulo(t, 'posto', 'La prenotazione', ['Scegliete', 'la sera.'],
                                'Vi confermiamo il posto entro un giorno. La caparra è di 30 € a persona, si scala dal conto; '
                                'si recupera se disdite due giorni prima.', VOCI, campi, 'Nove Posti — prenotazione',
                                'Chiedi il posto', 'Ricevuta. Vi scriviamo entro un giorno per confermare.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Sui Navigli,', 'dietro il cortile.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via Vigevano 9, Milano'), ('Metro', 'Porta Genova, M2, a cinque minuti'),
                                  ('Il campanello', 'nel cortile, la porta di legno chiaro'), ('Parcheggio', 'in via Tortona')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Nove Posti · via Vigevano 9', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'La', 'prenotazione.', [
        ('Si può spostare?', 'Sì, fino a due giorni prima, senza perdere la caparra. Dopo, cerchiamo di riempire il posto.'),
        ('Fate buoni regalo?', 'Sì, per uno dei tre percorsi: arrivano per mail, valgono un anno.'),
        ('C’è un abbigliamento richiesto?', 'No. Solo una cosa: niente profumi forti, al banco coprono il pesce.'),
        ('Si possono portare bambini?', 'Dagli otto anni, se amano il pesce crudo: per loro il percorso breve costa la metà.'),
    ], 'pr', bg=True))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · posto vuoto', color=ORO, align='center'),
                       K.headline(t, 'Questo sgabello <em>è libero.</em>', size=88, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro']),
                       K.text(t, 'La pagina non c’è, ma il banco sì: torniamo all’inizio.', size=17, align='center',
                              color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna al banco', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_TONNO, overlay=70), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'Il piatto da inclinare',
    ['sushi', 'omakase', 'ristorante giapponese', 'milano', 'scuro', 'slider pro', '3d'],
    'Un omakase di nove posti, nero d’inchiostro e oro opaco: in apertura il piatto in una finestra rotonda che si inclina '
    'in 3D verso il puntatore, l’anello d’oro che gira, i cinque momenti del percorso a linguette; il percorso coi piatti '
    'che si affacciano, la galleria in coverflow 3D, il sake al calice e la prenotazione per turno.',
    [('Il percorso', PERCORSO), ('Il banco', BANCO), ('Il sake', PERCORSO + '#sake'), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('il-percorso', 'Il percorso', 'il-percorso'), ('il-banco', 'Il banco', 'il-banco'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Instrument Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cormorant\', \'Georgia\', serif',
    '\'Xanh Mono\', ui-monospace, Menlo, monospace', ['Cormorant', 'Instrument Sans', 'Xanh Mono'],
    corsivo_di=['Cormorant'], peso_titoli='500', raggio=2, grana=5, titoli_line='1.0', titoli_spazio='-0.005em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # apertura rifatta il 31 ottobre 2026


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-percorso.json': build_percorso(), 'il-banco.json': build_banco(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
