# -*- coding: utf-8 -*-
"""Genera il tema «Fornace» (id fornace): una vetreria artistica di Murano dal 1921, vetro soffiato, murrine, calici e
lampadari; la fornace si visita e si può provare a lume a fare una perla.

Tema scuro: il nero della fornace, l'arancio del vetro fuso, il blu di Murano e l'acquamarina. Titoli in Fraunces (anche
corsivo), testo in Spline Sans, dati in Spline Sans Mono.

Apertura: Slider Pro «la fornace», alto 16:9. Il vetro fuso in una finestra rotonda (un video a lume); attorno, tre
dischi di vetro liquido (sfocatura di quello che c'è dietro, riflesso, bordo di luce) che girano su sé stessi in 3D a
velocità diverse, e due murrine che ruotano piano. Una diapositiva per lavoro della vetreria, a linguette.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'fornace'
NAME = 'Fornace'

HEX = {
    'primary': '#FF8A3D', 'primary_contrast': '#140A05', 'secondary': '#3E7BD1', 'secondary_contrast': '#FFFFFF',
    'accent': '#7FD3C8', 'dark': '#05060A', 'light': '#F7F4EE', 'muted': '#141725', 'muted_contrast': '#ECEAF2',
    'text': '#ECEAF2', 'text_muted': '#A3A0B4', 'background': '#0B0D14', 'border': '#262A3D', 'link': '#FF8A3D',
}

HOME, OPERE, FORNACE_P, VISITA = '{{PAGINA:home}}', '{{PAGINA:le-opere}}', '{{PAGINA:la-fornace}}', '{{PAGINA:visita}}'
MAIL, PHONE, PHONE_TEL = 'fornace@fornace.example', '+39 041 000 1921', 'tel:+390410001921'
LAT, LNG = '45.4580', '12.3527'

V_PERLA, V_LUME, V_FORNO, V_BOCCA, V_CANALE, V_TUBO = (
    'pexels-video-7520871', 'pexels-video-7519304', 'pexels-video-5846301', 'pexels-video-5846589', 'pexels-video-28552996',
    'pexels-video-852406')
PHOTOS = [
    'pexels-31280744.jpg', 'pexels-36639186.jpg', 'pexels-6069713.jpg', 'pexels-6069748.jpg', 'pexels-12377479.jpg',
    'pexels-10693829.jpg', 'pexels-34665311.jpg', 'pexels-34519429.jpg', 'pexels-5845914.jpg', 'pexels-5846085.jpg',
    'pexels-7519891.jpg', 'pexels-26832096.jpg', 'pexels-38037228.jpg', 'pexels-7518812.jpg', 'pexels-38037230.jpg',
    'pexels-7518815.jpg', 'pexels-20992753.jpg', 'pexels-8777649.jpg', 'pexels-8777639.jpg', 'pexels-9989659.jpg',
    'pexels-936801.jpg', 'pexels-30650533.jpg', 'pexels-8391474.jpg', 'pexels-15260244.jpg', 'pexels-20531141.jpg',
    'pexels-34400995.jpg', 'pexels-35687707.jpg', 'pexels-37669255.jpg', 'pexels-36050147.jpg', 'pexels-18843417.jpg',
    'pexels-18781088.jpg', 'pexels-31397005.jpg', 'pexels-31206523.jpg', 'pexels-18847929.jpg', 'pexels-35778903.jpg',
    'pexels-5114970.jpg', 'pexels-37358133.jpg', 'pexels-34382783.jpg', 'pexels-37358134.jpg', 'pexels-38774907.jpg',
    'pexels-7585700.jpg', 'pexels-5378420.jpg', 'pexels-7518808.jpg', 'pexels-10166994.jpg',
]
VIDEOS = [V_PERLA, V_LUME, V_FORNO, V_BOCCA, V_CANALE, V_TUBO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=12, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
FUSO, BLU, ACQUA = C['acc'], C['sec'], C['acc2']
TITOLI, MONO = 'Fraunces', 'Spline Sans Mono'
VOCI = [('map-pin', 'Fondamenta dei Vetrai 21 · Murano', VISITA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Fornace', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Fornace', 'Vetreria artistica sulla Fondamenta dei Vetrai dal 1921: vetro soffiato, murrine, calici e '
                   'lampadari su misura, fatti a mano da tre generazioni di maestri. La fornace si visita.',
                   [('Le opere', OPERE), ('La fornace', FORNACE_P), ('Visita', VISITA), ('Come arrivare', VISITA + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'La fornace', 'Lunedì — sabato 9 — 17<br>Le dimostrazioni alle 10, alle 12 e alle 15', VOCI,
                   '© 2026 Fornace Dal Moro snc · Murano · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=140)


LAVORI = [  # linguetta, occhiello, riga 1, riga 2, testo, finestra (foto|video), sfondo
    ('Soffiato', '01 · il soffiato', 'Un respiro', 'a mille gradi.', 'Il maestro raccoglie il vetro fuso sulla canna, soffia, '
     'gira, taglia con le pinze. Un vaso si fa in venti minuti; ci vogliono trent’anni per farlo bene.',
     ('video', V_LUME), 'pexels-34665311.jpg'),
    ('Murrine', '02 · le murrine', 'Un fiore di vetro', 'in ogni fetta.', 'Bacchette di vetro colorato fuse a strati, tirate '
     'fino a un dito e tagliate a fette sottili: ogni fetta è un fiore, sempre uguale e sempre un po’ diverso.',
     ('foto', 'pexels-5114970.jpg'), 'pexels-34382783.jpg'),
    ('Lampadari', '03 · i lampadari', 'Trecento pezzi,', 'una sola luce.', 'Bracci, foglie, fiori e pendenti soffiati uno per '
     'uno e montati a mano. I nostri lampadari stanno in tre teatri e in molte case di Venezia.',
     ('foto', 'pexels-35687707.jpg'), 'pexels-36050147.jpg'),
    ('Visita', '04 · la visita', 'La fornace', 'è aperta.', 'Tre dimostrazioni al giorno davanti alla bocca del forno, e il '
     'laboratorio a lume dove fate la vostra perla. Si prenota, perché la sala è piccola.',
     ('video', V_PERLA), 'pexels-5846085.jpg'),
]


def apertura(t):
    m = K.mob
    sl = []
    fx, fy, fw, fh = 59.0, 15.0, 33.33, 59.26        # la finestra: 480 px
    for i, (tab, occ, r1, r2, testo, media, sfondo) in enumerate(LAVORI):
        n = i + 1
        finestra = dict(x=fx, y=fy, w=fw, h=fh, borderRadius=999, entra='zoomIn', ritardo=100, durata=1300, esce='fadeOut',
                        boxShadow={'x': 0, 'y': 0, 'blur': 120, 'spread': 6, 'color': mix(FUSO, 30)},
                        responsive={'mobile': {'x': 17.2, 'y': 6, 'width': 65.6, 'height': 30}})
        if media[0] == 'video':
            vetro = K.livello('fn-v%d' % n, tipo='video', videoSrc=K.img(media[1] + '.mp4'), videoAutoplay=True, videoMuted=True,
                              videoLoop=True, **finestra)
        else:
            vetro = K.livello('fn-v%d' % n, tipo='image', imageSrc=K.img(media[1]), objectFit='cover',
                              kenBurns={'type': 'zoomIn', 'intensity': 8, 'duration': 9000}, **finestra)
        sl.append(K.diapositiva('fn-%d' % n, K.sfondo_foto(sfondo, overlay=0.88, scala=1.08, durata=10000,
                                                           verso='in' if n % 2 else 'out', colore=C['fondo']), [
            vetro,
            K.livello('fn-o%d' % n, contenuto=occ, x=6, y=18, w=40, size=12, peso='400', colore=FUSO, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 42, 'x': 7, 'width': 86}}),
            K.livello('fn-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=22.5, w=48, size=84, peso='400',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, entra='fadeIn', ritardo=350, durata=1200,
                      charAnim={'stagger': 28, 'offsetY': 26, 'blur': 10, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(40), 'y': 44.5, 'x': 7, 'width': 88}}),
            K.livello('fn-i%d' % n, contenuto=r2, tag='p', x=6, y=34, w=48, size=84, peso='400', fontStyle='italic',
                      colore=FUSO, fontFamily=TITOLI, lineHeight=1.02, entra='fadeIn', ritardo=650, durata=1200,
                      charAnim={'stagger': 28, 'offsetY': 26, 'blur': 10, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(40), 'y': 49, 'x': 7, 'width': 88}}),
            K.livello('fn-s%d' % n, contenuto=testo, x=6, y=49.5, w=32, size=17, peso='400', colore=mix(C['chiaro'], 80),
                      lineHeight=1.65, ritardo=1000, responsive={'mobile': {'fontSize': m(14), 'y': 55.5, 'x': 7, 'width': 86}}),
            K.livello('fn-b%d' % n, tipo='button', contenuto='Prenota la visita', x=6, y=71, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=FUSO, padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=12, ritardo=1300, buttonUrl=VISITA, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 71, 'x': 7, 'fontSize': m(11)}}),
        ], durata=8000, tab=tab))
    # i dischi di vetro liquido che girano in 3D e le murrine che ruotano
    globali = []
    for k, (x, y, d, dur, col) in enumerate([(55.0, 10.0, 9.0, 7000, ACQUA), (86.5, 22.0, 6.5, 9000, BLU),
                                             (83.0, 66.0, 10.0, 11000, FUSO)]):
        h = round(d * 1440 / 810, 2)
        globali.append(K.livello('fn-disco%d' % k, tipo='shape', x=x, y=y, w=d, h=h, borderRadius=999, borderWidth=1,
                                 borderColor=mix(C['chiaro'], 55), backdropBlur=12, backdropBrightness=125,
                                 shapeGradient={'angle': 135, 'from': mix(C['chiaro'], 30), 'to': mix(col, 22)},
                                 boxShadow={'x': 0, 'y': 20, 'blur': 40, 'spread': -10, 'color': mix(col, 35)},
                                 timeline=K.tl(x, y, [(0, {'rotationY': 0.01, 'y': y}, 'sine.inOut'),
                                                      (dur // 2, {'rotationY': 180, 'y': y - 2}, 'sine.inOut'),
                                                      (dur, {'rotationY': 360, 'y': y})], dur),
                                 responsive={'mobile': {'x': [8, 80, 74][k], 'y': [4, 14, 30][k], 'width': [16, 12, 18][k],
                                                        'height': [7.3, 5.5, 8.2][k]}}))
    for k, (x, y, d, foto, dur) in enumerate([(52.5, 62.0, 7.0, 'pexels-5114970.jpg', 40000),
                                              (90.5, 47.0, 5.0, 'pexels-38774907.jpg', 30000)]):
        h = round(d * 1440 / 810, 2)
        globali.append(K.livello('fn-murrina%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=x, y=y, w=d, h=h,
                                 borderRadius=999, borderWidth=3, borderColor=mix(C['chiaro'], 40),
                                 boxShadow={'x': 0, 'y': 14, 'blur': 30, 'spread': -8, 'color': mix(C['nero'], 80)},
                                 timeline=K.tl(x, y, [(0, {'rotation': 0}), (dur, {'rotation': 360 if k == 0 else -360})], dur),
                                 responsive={'mobile': {'visible': False}}))
    globali.append(K.livello('fn-gradi', contenuto='Fornace a 1.100 °C · vetro sodico-calcico · dal 1921', x=50, y=88, w=44,
                             size=11, peso='400', colore=mix(C['chiaro'], 60), fontFamily=MONO, letterSpacing=2, align='right',
                             textTransform='uppercase', entra='fadeIn', ritardo=1600,
                             responsive={'mobile': {'y': 78, 'x': 7, 'width': 86, 'fontSize': m(10)}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='crossFade',
                       durata_trans=1300, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=FUSO, navColor=C['chiaro'])


COLORI = [('Blu Murano', 'Cobalto · 1921', 'Il blu della prima fornace del nonno, col cobalto. Sotto la luce diventa '
           'viola ai bordi.', 'pexels-8777649.jpg'),
          ('Rosso rubino', 'Oro colloidale', 'Il rosso più caro: si colora con l’oro. Lo facciamo una volta al mese.',
           'pexels-31280744.jpg'),
          ('Acquamarina', 'Rame · trasparente', 'Il colore della laguna d’estate, il più chiesto per i calici.',
           'pexels-8777639.jpg'),
          ('Ambra', 'Ferro e zolfo', 'Il colore del miele: caldo, si vede bene anche alla luce di una candela.',
           'pexels-6069748.jpg'),
          ('Millefiori', 'Murrine a strati', 'Decine di colori in una fetta: lo usiamo per le perle e i fermacarte.',
           'pexels-5114970.jpg')]


def colori(t):
    return t.classic('provamateriale', {
        'voci': [{'id': 'fn-c%d' % k, 'nome': n, 'codice': c, 'testo': te, 'immagine': K.img(f), 'miniatura': '',
                  'colore': '', 'alt': 'Vetro %s' % n.lower()} for k, (n, c, te, f) in enumerate(COLORI)],
        'iniziale': 0, 'lente': True, 'lente_zoom': 2.6, 'lente_misura': 210, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passate sopra la foto: la lente mostra il vetro da vicino',
        'testo_suggerimento_touch': 'Toccate la foto per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'Il colore', 'accento': FUSO, 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        'sfondo_pannello': '', 'etichetta_color': C['tenue'], 'nome_font_family': 'heading', 'nome_font_size': 50,
        'nome_font_weight': '400', 'nome_color': C['testo'], 'codice_font_family': 'var(--olo-font-family-mono)',
        'codice_color': ACQUA, 'testo_color': C['tenue'], 'raggio_immagine': radius(12), 'proporzione': '3/2',
        'campione_forma': 'cerchio', 'campione_misura': 52, 'disposizione': 'lato', 'lato': 'sinistra',
        'larghezza_immagine': 58, 'gap': 56}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Fornace · Murano', ['Tre generazioni,', 'una bocca di fuoco.'], size=60, layout='stack',
                                size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Il nonno Giovanni accese il forno nel 1921, sulla Fondamenta dei Vetrai. Il forno non si '
                                'spegne mai: costa troppo riaccenderlo. Oggi ci lavorano suo nipote Bepi, maestro vetraio, e '
                                'la bisnipote Lucia, che fa le murrine e insegna a lume.', size=17, color=C['testo']),
                      K.button(t, 'La fornace', FORNACE_P, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('1921', 'Il forno acceso'), ('1.100 °C', 'Il vetro fuso'), ('3', 'Generazioni di maestri'),
                            ('0', 'Giorni di forno spento')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I colori', ['Cinque colori,', 'cinque ricette.'], size=60,
                         tagline='Scegliete un colore: la lente mostra il vetro da vicino. Le ricette sono del nonno, scritte '
                                 'in un quaderno che non esce dalla fornace.')]),
        t.one([colori(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='colori', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_BOCCA, 'La bocca del forno', 'Il forno <em>non si spegne mai.</em>',
                             'Milleecento gradi giorno e notte, da più di cent’anni. A Natale e a Ferragosto si abbassa, '
                             'ma non si spegne: riaccenderlo costerebbe una settimana.', overlay=40))
    out.append(t.section([
        t.one([K.sechead(t, 'Le opere', ['Dalla fornace,', 'questa stagione.'], size=60,
                         tagline='Trascinate o lasciate scorrere. Sono pezzi unici: quando uno è venduto, se ne fa un altro, mai uguale.')]),
        t.one([K.coverflow(t, [
            ('pexels-31280744.jpg', 'Bottiglie di vetro rosso intrecciate', 'Le bottiglie rubino', 'Oro colloidale'),
            ('pexels-6069713.jpg', 'Un vaso rosso con l’ombra sul muro', 'Il vaso Fiamma', 'Soffiato a mano'),
            ('pexels-6069748.jpg', 'Una coppa arancio vista dall’alto', 'La coppa Sole', 'Ambra e arancio'),
            ('pexels-10693829.jpg', 'Vasi blu con piante secche', 'I vasi Laguna', 'Blu Murano'),
            ('pexels-35687707.jpg', 'Lampadari colorati di Murano', 'Il lampadario Festa', 'Trecento pezzi'),
            ('pexels-20531141.jpg', 'Calici di cristallo su un tavolo', 'I calici', 'Su ordinazione'),
            ('pexels-36639186.jpg', 'Un vaso verde con una gerbera rossa', 'Il vaso Bolla', 'Verde bottiglia')],
            altezza=430, larghezza=330)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Abbiamo visto nascere il nostro lampadario: Bepi ci ha fatto tenere in mano la canna per un secondo, il vetro '
                 'era arancione come il sole. Adesso è appeso in salotto, e lo guardiamo ogni sera.',
        'author_name': 'Margherita e Hans', 'author_role': 'Lampadario su misura, 2026', 'avatar': '', 'rating': '5',
        'text_color': C['testo'], 'star_color': FUSO, 'author_color': C['tenue'], 'quote_accent_color': FUSO,
        'quote_font': 'heading', 'quote_size': 36, 'author_uppercase': True, 'quote_uppercase': False,
        'layout': 'editorial', 'show_line': True, 'line_color': ACQUA}, style={})])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PERLA, 'A lume', 'La vostra perla, <em>in mezz’ora.</em>',
                          'Al banco a lume Lucia vi insegna a scaldare la bacchetta, a girarla sul mandrino, a chiudere la '
                          'perla. Ve la portate a casa, nel suo sacchetto di velluto.', 'Prenota il laboratorio',
                          VISITA + '#prenota', overlay=50, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Fornace',
                page_intro_caption='Murano · dal 1921', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


OPERE_ = [  # titolo, foto, categoria, descrizione, anno
    ('Vaso Fiamma', 'pexels-6069713.jpg', 'Vasi', 'Rosso e arancio, soffiato, h 32 cm · 680 €', '2026'),
    ('Vasi Laguna', 'pexels-10693829.jpg', 'Vasi', 'Blu Murano, tre misure · da 240 €', '2026'),
    ('Coppa Sole', 'pexels-6069748.jpg', 'Coppe', 'Ambra e arancio, ø 28 cm · 420 €', '2025'),
    ('Bottiglie rubino', 'pexels-31280744.jpg', 'Vasi', 'Oro colloidale, coppia · 960 €', '2026'),
    ('Calici Acqua', 'pexels-20531141.jpg', 'Calici', 'Sei calici, gambo ritorto · 540 €', '2026'),
    ('Lampadario Festa', 'pexels-35687707.jpg', 'Lampadari', 'Trecento pezzi, su misura · su preventivo', '2025'),
    ('Lampade Lanterna', 'pexels-18843417.jpg', 'Lampadari', 'Vetro verde e ottone · 380 €', '2026'),
    ('Perle millefiori', 'pexels-38774907.jpg', 'Murrine e perle', 'Filo da 42 cm · 160 €', '2026'),
]


def build_opere():
    t = Tpl(ID, 'le-opere')
    out = [K.intro(t, 'Le opere', ['Pezzi unici,', 'firmati sul fondo.'],
                   'Ogni pezzo porta inciso il nome del maestro e l’anno. Cercate per tipo o colore; spediamo in tutto il '
                   'mondo, imballati in cassa di legno.', bg=K.photo_bg('pexels-9989659.jpg', overlay=70), overlay_text=True)]
    griglia = t.classic('portfolio', {
        'source': 'manual', 'layout': 'grid', 'columns': 4, 'gap': 24, 'image_ratio': '4:5', 'image_fit': 'cover',
        'hover_effect': 'zoom', 'animation': 'fade', 'caption_position': 'below', 'enable_search': True,
        'search_placeholder': 'Cerca: vaso, blu, lampadario…', 'show_title': True, 'show_category': True,
        'show_excerpt': True, 'font_family': 'heading', 'font_weight': '400', 'title_color': C['testo'],
        'text_color': C['tenue'], 'accent_color': FUSO, 'border_radius': 12, 'year_stamp': True, 'index_numbering': False,
        'items': [{'title': ti, 'image_url': K.img(f), 'category': cat, 'description': d, 'year': a,
                   'link_url': VISITA + '#prenota'} for ti, f, cat, d, a in OPERE_],
        'columns_tablet': '2', 'columns_mobile_landscape': '2'}, style={})
    out.append(t.section([t.one([griglia])], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_TUBO, 'Il lampadario', 'Ogni foglia <em>è soffiata a parte.</em>',
                             'Un lampadario di trecento pezzi richiede due mesi: bracci, foglie, fiori, pendenti. Si montano '
                             'su un’anima di ferro, a mano, il giorno prima della consegna.', overlay=50))
    vetrata = t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': k, 'title': ti, 'link': VISITA + '#prenota', 'span': 6, 'aspect': '4/3'}
                  for f, k, ti in [('pexels-8391474.jpg', 'Vetro colorato a piombo', 'Le vetrate'),
                                   ('pexels-936801.jpg', 'Esagoni di vetro soffiato', 'I pannelli'),
                                   ('pexels-30650533.jpg', 'Lastre a fasce di colore', 'Le lastre'),
                                   ('pexels-9989659.jpg', 'Un soffitto di fiori di vetro', 'Le installazioni')]],
        'columns': 2, 'gap': 16, 'object_position': 'center center', 'object_fit': 'cover', 'radius': 12,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 22), 'arrow_color': C['chiaro'], 'arrow_hover_bg': FUSO,
        'arrow_hover_color': C['on_acc'], 'show_arrow': True, 'title_size': 38, 'title_weight': '400', 'kicker_size': 11,
        'card_radius': radius(12)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Su misura', ['Per una casa,', 'un teatro, una chiesa.'], size=56,
                         tagline='Disegniamo con voi, facciamo un campione, poi lo realizziamo. Dai tre mesi in su.')]),
        t.one([vetrata]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di comprare.', [
        ('Come so che è vero vetro di Murano?', 'Ogni pezzo ha il marchio di origine col numero della fornace e la firma '
                                                 'incisa del maestro. Diffidate dei prezzi troppo bassi.'),
        ('Spedite all’estero?', 'Sì, in cassa di legno su misura, assicurato. Se arriva rotto, lo rifacciamo.'),
        ('Si può lavare in lavastoviglie?', 'I calici no: acqua tiepida e un panno morbido. I vasi tutti a mano.'),
        ('Fate pezzi su disegno?', 'Sì: un colloquio, un bozzetto, un campione. Il preventivo è gratuito.'),
    ], 'op', bg=False))
    return out


def build_fornace():
    t = Tpl(ID, 'la-fornace')
    out = [K.intro(t, 'La fornace', ['Sabbia, fuoco', 'e fiato.'],
                   'Il vetro è sabbia di silice, soda e calce, fuse insieme a milleecento gradi. Il resto lo fanno le mani e '
                   'il respiro del maestro.', bg=K.photo_bg('pexels-5846085.jpg', overlay=60), overlay_text=True)]
    fasi = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': FUSO, 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': FUSO, 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': True, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('I', 'La levata', 'pexels-34519429.jpg', 'Il bolo', 'Il vetro', 'sulla canna', 'Il maestro raccoglie il vetro fuso '
             'dal crogiolo girando la canna, come il miele sul cucchiaio.', '1.100 °C', 'nel crogiolo'),
            ('II', 'Il soffio', 'pexels-26832096.jpg', 'La forma', 'Un respiro', 'lungo', 'Si soffia piano, si gira, si scalda di '
             'nuovo. Le pinze e il legno bagnato danno la forma.', '20′', 'per un vaso'),
            ('III', 'La tempera', 'pexels-7518815.jpg', 'Il riposo', 'Una notte', 'a raffreddare', 'Il pezzo passa nella tempera, '
             'un forno che scende piano fino a temperatura di stanza.', '12 h', 'di raffreddamento')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': FUSO,
        'counter_font_family': 'heading', 'counter_size': 84, 'counter_color': FUSO, 'counter_italic': True,
        'counter_weight': '400', 'tag_color': C['chiaro'], 'pre_title_color': ACQUA, 'title_font_family': 'heading',
        'title_size': 32, 'title_weight': '400', 'title_color': C['chiaro'], 'title_accent_color': FUSO,
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'flame', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(12)}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Come nasce un vaso', ['Tre tempi,', 'una notte di riposo.'], size=56)]),
                          t.one([fasi])], bg=solid(C['sala']), padding='large', width='large',
                         style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-38037230.jpg', 'Il maestro Bepi al lavoro col vetro e la fiamma', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Bepi Dal Moro · maestro vetraio', color=FUSO),
                      t.classic('quotation', {
                          'content': 'Il vetro ti dice lui quando è pronto: cambia colore, cambia peso. Bisogna solo stare '
                                     'zitti e guardarlo.', 'author': 'Bepi Dal Moro, alla bocca del forno', 'style': 'default',
                          'alignment': 'left', 'content_font_family': 'heading', 'content_font_size': '44',
                          'content_font_weight': '400', 'content_font_style': 'italic', 'content_color': C['testo'],
                          'author_color': C['tenue'], 'author_font_size': '15', 'text_effect': 'none'}, style={})],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Tre mani', 'davanti al fuoco.'], size=56)]),
        K.squadra(t, [
            ('pexels-38037230.jpg', 'Bepi Dal Moro', 'Maestro vetraio', 'Nipote del fondatore, alla bocca del forno da quando '
             'aveva quattordici anni. I lampadari sono suoi.', '50% 30%'),
            ('pexels-37358134.jpg', 'Graziella Dal Moro', 'Murrine e perle', 'Sorella di Bepi, fa le murrine come le faceva la '
             'nonna. Ogni bacchetta richiede una settimana.', '50% 30%'),
            ('pexels-7518808.jpg', 'Marco Busetto', 'Servente e allievo', 'Da sei anni accanto al maestro: porge le canne, '
             'scalda, impara. Il primo vaso firmato è del 2025.', '50% 30%')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_FORNO, 'Il crogiolo', 'Milleecento gradi, <em>giorno e notte.</em>',
                          'Il crogiolo di argilla dura sei mesi, poi si rompe e si cambia. Il giorno del cambio la fornace '
                          'si ferma: è l’unico.', 'Prenota la visita', VISITA, overlay=55, video=True))
    return out


def build_visita():
    t = Tpl(ID, 'visita')
    out = [K.intro(t, 'Visita', ['Davanti al fuoco,', 'per un’ora.'],
                   'La dimostrazione alla bocca del forno, la sala delle opere e il laboratorio a lume. Si prenota: la '
                   'fornace è piccola e fa caldo.', bg=K.photo_bg('pexels-31206523.jpg', overlay=60), overlay_text=True, size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Tre modi', ['Di entrare', 'in fornace.'], size=56,
                         tagline='I bambini dagli otto anni. D’estate venite la mattina: vicino al forno fa davvero caldo.')]),
        K.piani(t, [('La dimostrazione', '15', 'a persona · 30 minuti', 'Il maestro alla bocca del forno\nUn vaso soffiato '
                     'davanti a voi\nLa sala delle opere', False, ''),
                    ('A lume', '65', 'a persona · un’ora e mezza', 'La dimostrazione\nLa vostra perla al banco a lume\nIl '
                     'sacchetto di velluto', True, 'La più scelta'),
                    ('Un giorno da vetraio', '290', 'a persona · sei ore', 'Al fianco del maestro\nIl vostro primo bicchiere '
                     'soffiato\nIl pranzo alla trattoria del rio', False, '')],
                'Prenota', VISITA + '#prenota', nota='Gruppi fino a dodici persone · gratis sotto gli otto anni'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa', 'cosa', True,
                     opzioni='La dimostrazione · 15 €\nA lume · 65 €\nUn giorno da vetraio · 290 €\nUn pezzo su misura'),
             K.campo('date', 'Giorno', 'giorno', True), K.campo('select', 'Ora', 'ora', True, opzioni='10:00\n12:00\n15:00'),
             K.campo('select', 'Quanti siete', 'persone', True, opzioni='1\n2\n3\n4\n5 — 8\n9 — 12'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Bambini, lingua della visita, un pezzo che cercate.')]
    out.append(K.sezione_modulo(t, 'prenota', 'La prenotazione', ['Scegliete', 'l’ora del fuoco.'],
                                'Vi confermiamo entro un giorno. Le visite si fanno in italiano, inglese e tedesco.', VOCI, campi,
                                'Fornace — visita', 'Prenota', 'Ricevuto! Vi confermiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Il vaporetto,', 'fermata Colonna.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Fondamenta dei Vetrai 21, Murano'), ('Vaporetto', 'linee 4.1 e 4.2, fermata Colonna'),
                                  ('Da Venezia', 'quindici minuti da Fondamente Nove'), ('Orari', 'lunedì — sabato 9 — 17')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fornace · Fondamenta dei Vetrai 21', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_CANALE, 'Murano', 'Un’isola, <em>settecento anni di vetro.</em>',
                          'Le fornaci vennero spostate a Murano nel 1291, per paura degli incendi a Venezia. Da allora '
                          'l’isola vive del fuoco: fate un giro sulle fondamenta, dopo la visita.', 'Le opere', OPERE,
                          overlay=50, video=True))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pezzo rotto', color=FUSO, align='center'),
                       K.headline(t, 'Questa pagina <em>è tornata nel crogiolo.</em>', size=80, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=FUSO),
                       K.text(t, 'Il vetro rotto si rifonde: torniamo all’inizio.', size=17, align='center',
                              color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna alla fornace', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LUME, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Industria e manifattura', 'Il vetro che gira',
    ['vetro', 'murano', 'vetro soffiato', 'artigianato', 'venezia', 'scuro', 'slider pro', '3d'],
    'Una vetreria di Murano, nero di fornace e arancio di vetro fuso: in apertura il vetro a lume nella finestra rotonda, '
    'tre dischi di vetro liquido che girano su sé stessi in 3D e le murrine che ruotano; i colori sotto la lente, le '
    'opere in coverflow 3D e con la ricerca, i tre tempi del soffiato e la visita in fornace.',
    [('Le opere', OPERE), ('La fornace', FORNACE_P), ('I colori', HOME + '#colori'), ('Visita', VISITA)],
    [('home', 'Home', None), ('le-opere', 'Le opere', 'le-opere'), ('la-fornace', 'La fornace', 'la-fornace'),
     ('visita', 'Visita', 'visita')],
    HEX, '\'Spline Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fraunces\', \'Georgia\', serif',
    '\'Spline Sans Mono\', ui-monospace, Menlo, monospace', ['Fraunces', 'Spline Sans', 'Spline Sans Mono'],
    corsivo_di=['Fraunces'], peso_titoli='400', raggio=12, grana=5, titoli_line='1.02', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-opere.json': build_opere(), 'la-fornace.json': build_fornace(), 'visita.json': build_visita(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
