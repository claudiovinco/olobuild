# -*- coding: utf-8 -*-
"""Genera il tema «Rugiada» (id rugiada): un laboratorio di cosmesi a Parma, sieri, creme e oli con pochi ingredienti,
formulati da una chimica e venduti online e in venti erboristerie.

Tema chiaro e morbido: pesca chiara, rosa legno, salvia. Titoli in Ibarra Real Nova (anche corsivo), testo in Hanken
Grotesk, dati in Red Hat Mono.

Apertura: Slider Pro «la goccia», alto 16:9. Il prodotto in una finestra ad arco; dall'alto cade una goccia che tocca il
prodotto e si allarga in un'onda (timeline in loop); attorno galleggiano bolle di vetro liquido (sfocatura di quello che
c'è dietro, bordo di luce), lente e lievi. Una diapositiva per prodotto a linguette: siero, crema, olio, il rituale.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'rugiada'
NAME = 'Rugiada'

HEX = {
    'primary': '#A8475B', 'primary_contrast': '#FFFFFF', 'secondary': '#5E7D6E', 'secondary_contrast': '#FFFFFF',
    'accent': '#E8B9A6', 'dark': '#2A1F1D', 'light': '#FFFFFF', 'muted': '#EFE4DC', 'muted_contrast': '#2B2220',
    'text': '#2B2220', 'text_muted': '#6F625D', 'background': '#F7F1EC', 'border': '#E2D3C8', 'link': '#A8475B',
}

HOME, PRODOTTI, LAB, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:prodotti}}', '{{PAGINA:il-laboratorio}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'ciao@rugiada.example', '+39 0521 000 777', 'tel:+390521000777'
LAT, LNG = '44.8015', '10.3279'

V_GOCCIA, V_MANO, V_CREMA, V_ACQUA, V_VISO = ('pexels-video-13662047', 'pexels-video-4561105', 'pexels-video-7234930',
                                             'pexels-video-16478020', 'pexels-video-9697675')
PHOTOS = [
    'pexels-7019497.jpg', 'pexels-7321710.jpg', 'pexels-7321654.jpg', 'pexels-7321723.jpg', 'pexels-5240625.jpg',
    'pexels-7321706.jpg', 'pexels-7691107.jpg', 'pexels-8015480.jpg', 'pexels-9774602.jpg', 'pexels-9774609.jpg',
    'pexels-6167445.jpg', 'pexels-6167444.jpg', 'pexels-40097335.jpg', 'pexels-4194852.jpg', 'pexels-1871289.jpg',
    'pexels-37213312.jpg', 'pexels-11126099.jpg', 'pexels-8129916.jpg', 'pexels-3762758.jpg', 'pexels-3762765.jpg',
    'pexels-8128677.jpg', 'pexels-3762897.jpg', 'pexels-5253959.jpg', 'pexels-9774655.jpg', 'pexels-37711449.jpg',
    'pexels-31403898.jpg', 'pexels-6763795.jpg', 'pexels-8160467.jpg', 'pexels-15699693.jpg', 'pexels-4857799.jpg',
    'pexels-8015898.jpg', 'pexels-12336389.jpg', 'pexels-6682999.jpg', 'pexels-4888814.jpg', 'pexels-8450510.jpg',
    'pexels-8450222.jpg', 'pexels-8450396.jpg', 'pexels-8450116.jpg', 'pexels-8465992.jpg', 'pexels-9227978.jpg',
    'pexels-7321496.jpg', 'pexels-6690233.jpg', 'pexels-18253735.jpg', 'pexels-4041187.jpg', 'pexels-16233548.jpg',
    'pexels-3528076.jpg', 'pexels-18253732.jpg', 'pexels-4196795.jpg', 'pexels-34644210.jpg', 'pexels-9768437.jpg',
    'pexels-8531365.jpg', 'pexels-4031526.jpg', 'pexels-8657380.jpg', 'pexels-8148743.jpg', 'pexels-6693972.jpg',
    'pexels-9775175.jpg',
]
VIDEOS = [V_GOCCIA, V_MANO, V_CREMA, V_ACQUA, V_VISO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=18, peso_titoli='500', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
PESCA, SALVIA = C['acc2'], C['sec']
TITOLI, MONO = 'Ibarra Real Nova', 'Red Hat Mono'
VOCI = [('map-pin', 'Borgo delle Colonne 21 · Parma', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Rugiada', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Rugiada', 'Cosmesi formulata e prodotta a Parma: pochi ingredienti, scritti tutti in etichetta, vetro '
                   'ambrato e ricariche. Spedizione in 48 ore, venti erboristerie in Italia.',
                   [('I prodotti', PRODOTTI), ('Il laboratorio', LAB), ('La tua routine', PRODOTTI + '#routine'),
                    ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Il laboratorio', 'Lunedì — venerdì 9 — 18<br>Il negozio del laboratorio: sabato 10 — 13', VOCI,
                   '© 2026 Rugiada srl · Parma · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')], marchio_size=150, fondo=C['sala'])


PRODOTTI_SLIDE = [  # linguetta, occhiello, riga 1, riga 2, testo, foto, prezzo, fondo
    ('Siero', 'N° 1 · il siero', 'Sette ingredienti,', 'una goccia al mattino.', 'Acido ialuronico in tre pesi, niacinamide '
     'e acqua di rose di Damasco. Si assorbe in un respiro, non lascia niente sulla pelle.', 'pexels-6693972.jpg', '34 € · 30 ml',
     mix(C['acc2'], 30, C['fondo'])),
    ('Crema', 'N° 2 · la crema', 'Morbida', 'come la seta.', 'Burro di karité e squalano d’oliva '
     'in un’emulsione che si scioglie al contatto. Per tutto il giorno, anche sotto il trucco.', 'pexels-9774602.jpg',
     '29 € · 50 ml', mix(C['acc'], 9, C['fondo'])),
    ('Olio', 'N° 3 · l’olio', 'Tre oli', 'per la notte.', 'Jojoba, rosa mosqueta e camelia, spremuti a freddo. Quattro gocce '
     'sul viso umido, prima di dormire: al mattino la pelle è un’altra.', 'pexels-7321710.jpg', '31 € · 20 ml',
     mix(C['sec'], 14, C['fondo'])),
    ('Rituale', 'N° 4 · il rituale', 'Tre passi,', 'due minuti.', 'Detergere, una goccia di siero, la crema. La sera '
     'l’olio al posto della crema. Non serve altro: lo dice la nostra chimica, non il marketing.', 'pexels-3762758.jpg',
     '84 € · il cofanetto', mix(C['acc2'], 18, mix(C['acc'], 6, C['fondo']))),
]


def apertura(t):
    m = K.mob
    sl = []
    ax, ay, aw, ah = 37.5, 13.0, 25.0, 74.0              # l'arco al centro: 360 × 600 px
    for i, (tab, occ, r1, r2, testo, foto, prezzo, fondo) in enumerate(PRODOTTI_SLIDE):
        n = i + 1
        sl.append(K.diapositiva('ru-%d' % n, K.sfondo_video([V_ACQUA, V_CREMA, V_MANO, V_VISO][i], overlay=0.7,
                                                            colore=C['fondo']),
                                # un velo per lato: il titolo diviso sta ai due lati dell'arco
                                K.veli('ru-velo%d' % n, 'sinistra', 35, 40) + K.veli('ru-vela%d' % n, 'destra', 35, 39)[:1] + [
            K.livello('ru-a%d' % n, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=ax, y=ay, w=aw, h=ah,
                      borderRadiusLinked=False, borderRadiusTL=180, borderRadiusTR=180, borderRadiusBR=18, borderRadiusBL=18,
                      kenBurns={'type': 'zoomIn', 'intensity': 8, 'duration': 9000}, entra='maskFromBottom', ritardo=100,
                      durata=1300, esce='fadeOut',
                      boxShadow={'x': 0, 'y': 40, 'blur': 80, 'spread': -24, 'color': mix(C['nero'], 25)},
                      responsive={'mobile': {'x': 24, 'y': 6, 'width': 52, 'height': 31}}),
            # il titolo si legge attraverso l'arco: la prima riga a sinistra, la seconda a destra
            K.livello('ru-o%d' % n, contenuto=occ, x=3, y=22, w=31.5, size=12, peso='500', colore=C['acc'], letterSpacing=2,
                      textTransform='uppercase', fontFamily=MONO, align='right', ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 41, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello('ru-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=3, y=26, w=31.5, size=58, peso='500',
                      colore=C['testo'], fontFamily=TITOLI, lineHeight=1.04, align='right', entra='fadeIn', ritardo=350,
                      durata=1300, charAnim={'stagger': 30, 'offsetY': 0, 'blur': 12, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 43.5, 'x': 7, 'width': 88, 'textAlign': 'left'}}),
            K.livello('ru-i%d' % n, contenuto=r2, tag='p', x=65.5, y=40, w=31.5, size=58, peso='500', fontStyle='italic',
                      colore=C['acc'], fontFamily=TITOLI, lineHeight=1.04, entra='fadeIn', ritardo=700, durata=1300,
                      charAnim={'stagger': 30, 'offsetY': 0, 'blur': 12, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 47.8, 'x': 7, 'width': 88}}),
            K.livello('ru-s%d' % n, contenuto=testo, x=65.5, y=57, w=28, size=17, peso='400', colore=C['tenue'],
                      lineHeight=1.65, ritardo=1000,
                      responsive={'mobile': {'fontSize': m(14), 'y': 54.5, 'x': 7, 'width': 86}, 'tablet': {'width': 31}}),
            K.livello('ru-b%d' % n, tipo='button', contenuto='Scopri il prodotto', x=65.5, y=76, w='auto', size=15, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, ritardo=1300, buttonUrl=PRODOTTI,
                      hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 69.5, 'x': 7, 'fontSize': m(13)}, 'tablet': {'y': 77}}),
            K.livello('ru-p%d' % n, contenuto=prezzo, x=81, y=77.2, w='auto', size=13, peso='500', colore=C['testo'],
                      fontFamily=MONO, bgColor=C['chiaro'], padding=12, borderRadius=999, entra='popUpBack', ritardo=1550,
                      responsive={'mobile': {'y': 70.1, 'x': 58, 'fontSize': m(12)}, 'tablet': {'x': 84, 'y': 78}}),
        ], durata=8000, tab=tab))
    # la goccia che cade sulla cima dell'arco e l'onda che si allarga; le bolle di vetro liquido che galleggiano
    gx, gy = 49.3, -6.0
    globali = [
        K.livello('ru-goccia', tipo='shape', x=gx, y=gy, w=1.39, h=3.1, bgColor=C['chiaro'], borderRadiusLinked=False,
                  borderRadiusTL=0, borderRadiusTR=999, borderRadiusBR=999, borderRadiusBL=999,
                  borderWidth=1, borderColor=mix(C['acc'], 40), backdropBlur=4,
                  boxShadow={'x': 0, 'y': 6, 'blur': 14, 'spread': 0, 'color': mix(C['acc'], 30)},
                  timeline=K.tl(gx, gy, [(0, {'y': -6, 'rotation': -45, 'opacity': 0}, 'power2.in'),
                                         (300, {'y': -4, 'rotation': -45, 'opacity': 100}, 'power2.in'),
                                         (1300, {'y': 10, 'rotation': -45, 'opacity': 100}),
                                         (1450, {'y': 11, 'rotation': -45, 'opacity': 0, 'scale': 0.4}),
                                         (3600, {'y': 11, 'rotation': -45, 'opacity': 0, 'scale': 0.4})], 3600),
                  responsive={'mobile': {'visible': False}}),
    ]
    for k, ritardo in enumerate([0, 450]):
        globali.append(K.livello('ru-onda%d' % k, tipo='shape', x=43.75, y=1.9, w=12.5, h=22.2, bgColor='transparent',
                                 borderRadius=999, borderWidth=2, borderColor=mix(C['acc'], 60),
                                 timeline=K.tl(43.75, 1.9, [(0, {'scale': 0.05, 'opacity': 0}),
                                                            (1400 + ritardo, {'scale': 0.05, 'opacity': 0}, 'power2.out'),
                                                            (1450 + ritardo, {'scale': 0.1, 'opacity': 100}, 'power2.out'),
                                                            (2900 + ritardo, {'scale': 1.0, 'opacity': 0}),
                                                            (3600, {'scale': 1.0, 'opacity': 0})], 3600),
                                 responsive={'mobile': {'visible': False}}))
    for k, (x, y, d, dur, dy) in enumerate([(31.5, 58, 7.0, 7000, 2.5), (59, 15, 5.5, 8200, 3), (57, 76, 6.0, 9000, 2),
                                            (34, 12, 4.2, 6400, 3.5)]):
        h = round(d * 1440 / 810, 2)
        globali.append(K.livello('ru-bolla%d' % k, tipo='shape', x=x, y=y, w=d, h=h, bgColor=mix(C['chiaro'], 22),
                                 borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 75), backdropBlur=10,
                                 backdropBrightness=108,
                                 shapeGradient={'angle': 135, 'from': mix(C['chiaro'], 70), 'to': mix(PESCA, 30)},
                                 boxShadow={'x': 0, 'y': 18, 'blur': 40, 'spread': -14, 'color': mix(C['acc'], 22)},
                                 timeline=K.tl(x, y, [(0, {'y': y, 'opacity': 0}, 'sine.inOut'),
                                                      (900, {'y': y, 'opacity': 100}, 'sine.inOut'),
                                                      (dur // 2, {'y': y - dy, 'opacity': 100}, 'sine.inOut'),
                                                      (dur, {'y': y, 'opacity': 100})], dur),
                                 responsive={'mobile': {'x': [18, 76, 70, 14][k], 'y': [5, 9, 30, 32][k],
                                                        'width': [12, 8, 15, 7][k], 'height': [5.5, 3.6, 6.8, 3.2][k]}}))
    globali.append(K.livello('ru-nota', contenuto='Formule corte · fatte a Parma · senza profumi aggiunti', x=3, y=80,
                             w=31.5, size=11, peso='500', colore=C['tenue'], letterSpacing=2, textTransform='uppercase',
                             fontFamily=MONO, align='right', lineHeight=1.6, entra='fadeIn', ritardo=1800,
                             responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='crossFade',
                       durata_trans=1300, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=C['acc'], navColor=C['testo'],
                       globalBackground={'type': 'color', 'color': C['fondo']})


def texture(t):
    voci = [('Il siero', 'Gel fluido · 30 ml', 'Trasparente, quasi acqua: si stende con due dita e sparisce in pochi secondi.',
             'pexels-7321496.jpg'),
            ('La crema', 'Emulsione · 50 ml', 'Bianca e soffice, si scioglie a contatto con la pelle. Non unge, non tira.',
             'pexels-9227978.jpg'),
            ('L’olio', 'Tre oli · 20 ml', 'Dorato, profuma appena di rosa. Quattro gocce bastano per il viso e il collo.',
             'pexels-7321710.jpg'),
            ('La maschera', 'Argilla rosa · 75 ml', 'Densa e vellutata: dieci minuti la domenica, poi acqua tiepida.',
             'pexels-4857799.jpg'),
            ('Il balsamo', 'Labbra e mani · 15 ml', 'Cera d’api e burro di cacao, solido nel vasetto, morbido sulle dita.',
             'pexels-6690233.jpg')]
    return t.classic('provamateriale', {
        'voci': [{'id': 'ru-t%d' % k, 'nome': n, 'codice': c, 'testo': te, 'immagine': K.img(f), 'miniatura': '',
                  'colore': '', 'alt': 'La texture: %s' % n.lower()} for k, (n, c, te, f) in enumerate(voci)],
        'iniziale': 1, 'lente': True, 'lente_zoom': 2.6, 'lente_misura': 210, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passate sopra la foto: la lente mostra la texture da vicino',
        'testo_suggerimento_touch': 'Toccate la foto per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'La texture', 'accento': C['acc'], 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        'sfondo_pannello': '', 'etichetta_color': C['tenue'], 'nome_font_family': 'heading', 'nome_font_size': 48,
        'nome_font_weight': '500', 'nome_color': C['testo'], 'codice_font_family': 'var(--olo-font-family-mono)',
        'codice_color': C['acc'], 'testo_color': C['tenue'], 'raggio_immagine': radius(18), 'proporzione': '3/2',
        'campione_forma': 'cerchio', 'campione_misura': 52, 'disposizione': 'lato', 'lato': 'sinistra',
        'larghezza_immagine': 58, 'gap': 56}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Rugiada · Parma', ['Pochi ingredienti,', 'tutti scritti.'], size=64, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Ogni prodotto ha al massimo dodici ingredienti, scritti in etichetta in italiano, con a cosa '
                                'servono. Li formula Elena Rossi, chimica cosmetologa, in un laboratorio dietro Borgo delle '
                                'Colonne. Vetro ambrato, tappi in alluminio, ricariche in busta.', size=17, color=C['testo']),
                      K.button(t, 'Il laboratorio', LAB, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('12', 'Ingredienti al massimo'), ('0', 'Profumi aggiunti'), ('48 h', 'La spedizione'),
                            ('20', 'Erboristerie in Italia')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le texture', ['Da vicino,', 'come sulla pelle.'], size=60,
                         tagline='Scegliete un prodotto: la foto cambia e la lente mostra la texture, com’è davvero.')]),
        t.one([texture(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_GOCCIA, 'Il siero', 'Una goccia <em>basta.</em>',
                             'Il contagocce è tarato: una goccia è la dose per tutto il viso. Un flacone dura tre mesi, e si '
                             'ricarica con la busta.', overlay=64))
    prodotti = t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': k, 'title': ti, 'link': PRODOTTI + '#' + a, 'span': s, 'aspect': asp}
                  for f, k, ti, a, s, asp in [
                      ('pexels-8657380.jpg', 'Sieri · da 34 €', 'I sieri', 'sieri', 6, '4/3'),
                      ('pexels-9774609.jpg', 'Creme · da 29 €', 'Le creme', 'creme', 6, '4/3'),
                      ('pexels-6167445.jpg', 'Oli e balsami · da 18 €', 'Oli e balsami', 'oli', 6, '4/3'),
                      ('pexels-6682999.jpg', 'Corpo e bagno · da 14 €', 'Il corpo', 'corpo', 6, '4/3')]],
        'columns': 2, 'gap': 16, 'object_position': 'center center', 'object_fit': 'cover', 'radius': 18,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 30), 'arrow_color': C['chiaro'], 'arrow_hover_bg': C['acc'],
        'arrow_hover_color': C['on_acc'], 'show_arrow': True, 'title_size': 40, 'title_weight': '500', 'kicker_size': 11,
        'card_radius': radius(18)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'I prodotti', ['Quattro famiglie,', 'una pelle.'], size=60,
                         tagline='Tutto senza profumi aggiunti, adatto anche alle pelli sensibili. Ogni prodotto ha la sua ricarica.')]),
        t.one([prodotti]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Avevo un cassetto pieno di creme che pizzicavano. Adesso uso tre prodotti, leggo tutto quello che c’è dentro '
                 'e la pelle non mi è mai stata così tranquilla.', 'author_name': 'Martina V.',
        'author_role': 'Pelle sensibile · il rituale da un anno', 'avatar': '', 'rating': '5', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc2']}, style={})])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_VISO, 'Il rituale', 'Due minuti, <em>mattina e sera.</em>',
                          'Detergere, il siero, la crema. La sera l’olio. Ve lo spieghiamo in una videochiamata gratuita '
                          'di dieci minuti, se volete.', 'Componi la tua routine', PRODOTTI + '#routine', overlay=55,
                          video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Rugiada',
                page_intro_caption='Cosmesi · Parma', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1500, page_intro_once=True)


def routine(t):
    def opz(i, g, nome, desc, prezzo, pred=False, badge=''):
        return {'id': i, 'gruppo': g, 'nome': nome, 'descrizione': desc, 'prezzo': prezzo, 'immagine': '', 'badge': badge,
                'predefinita': pred, 'punti': '', 'min': 0, 'max': 1}
    return t.classic('configuratore', {
        'gruppi': [{'id': 'pelle', 'titolo': 'La vostra pelle', 'tipo': 'uno', 'obbligatorio': True,
                    'aiuto': 'Se non lo sapete, scegliete «normale»: la routine si aggiusta dopo due settimane.'},
                   {'id': 'base', 'titolo': 'La base', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'piu', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': [opz('normale', 'pelle', 'Normale', 'Né lucida né che tira.', 0, True),
                    opz('secca', 'pelle', 'Secca', 'Tira dopo il lavaggio.', 0),
                    opz('mista', 'pelle', 'Mista', 'Lucida su fronte e naso.', 0),
                    opz('sensibile', 'pelle', 'Sensibile', 'Si arrossa facilmente.', 0),
                    opz('duo', 'base', 'Siero e crema', 'Il minimo che serve, mattina e sera.', 58, True, 'La più scelta'),
                    opz('trio', 'base', 'Siero, crema e olio', 'Con l’olio per la notte.', 84),
                    opz('detergente', 'piu', 'Detergente in gel', 'Delicato, senza schiuma.', 18),
                    opz('maschera', 'piu', 'Maschera all’argilla rosa', 'La domenica, dieci minuti.', 24),
                    opz('balsamo', 'piu', 'Balsamo labbra e mani', 'In borsa, tutto l’anno.', 12)],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'La vostra routine', 'nota_prezzo': 'Spedizione gratuita da 50 €',
        'periodo': '', 'riepilogo': True, 'titolo_riepilogo': 'La vostra routine', 'testo_azzera': 'Ricomincia',
        'testo_pulsante': 'Chiedi la routine', 'link_azione': CONTATTI + '?routine={configurazione}#scrivici',
        'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': C['chiaro'], 'colore_scelta': C['acc'],
        'colore_testo': C['testo'], 'colore_tenue': C['tenue'], 'colore_prezzo': C['acc'], 'sfondo_riepilogo': C['chiaro'],
        'sfondo_badge': C['acc2'], 'colore_badge': C['testo'], 'raggio_scheda': radius(18), 'gruppo_color': C['testo'],
        'gruppo_font_weight': '500', 'totale_color': C['acc'], 'totale_font_weight': '500'}, style={})


def build_prodotti():
    t = Tpl(ID, 'prodotti')
    out = [K.intro(t, 'I prodotti', ['Quattordici prodotti,', 'nessuno di troppo.'],
                   'Ogni formula ha al massimo dodici ingredienti. Li trovate scritti qui sotto, con a cosa servono.',
                   bg=K.photo_bg('pexels-8148743.jpg', overlay=80, color=C['fondo']))]
    listino = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in [
                      ('Siero idratante · 30 ml', 'Ialuronico in tre pesi, niacinamide, acqua di rose', '34 €', 'pexels-6693972.jpg', 'Il primo'),
                      ('Crema leggera · 50 ml', 'Karité, squalano d’oliva, ceramidi', '29 €', 'pexels-9774602.jpg', ''),
                      ('Olio della notte · 20 ml', 'Jojoba, rosa mosqueta, camelia', '31 €', 'pexels-7321710.jpg', ''),
                      ('Maschera all’argilla rosa · 75 ml', 'Caolino, argilla rosa, aloe', '24 €', 'pexels-4857799.jpg', ''),
                      ('Balsamo labbra e mani · 15 ml', 'Cera d’api, burro di cacao, calendula', '12 €', 'pexels-6690233.jpg', ''),
                      ('Ricarica del siero · 30 ml', 'In busta compostabile', '26 €', 'pexels-8657380.jpg', '−25%')]],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 30), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 76, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': C['acc2'], 'badge_color': C['testo'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['Vetro ambrato,', 'ricariche in busta.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Spedizione gratuita da 50 €, in 48 ore. Il vetro vuoto potete rimandarcelo: lo laviamo e '
                                'lo riempiamo di nuovo.')], wm='2-5', ws='1-1'),
               t.col([listino], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', html_id='sieri', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'La vostra routine', ['Componetela', 'in un minuto.'], size=56,
                         tagline='Scegliete pelle e prodotti: il prezzo si aggiorna, e la nostra chimica vi scrive se qualcosa '
                                 'non torna.')]),
        t.one([routine(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='routine', style={'padding_top': 120, 'padding_bottom': 120}))
    scheda = t.classic('schedatecnica', {
        'codice': 'RG-S01', 'nome': 'Siero idratante', 'etichetta': 'Gli ingredienti',
        'descrizione': 'Sette ingredienti in tutto, nell’ordine dell’etichetta. Il pH è 5,5, come quello della pelle; senza '
                       'profumi aggiunti, senza alcol.',
        'immagine': K.img('pexels-6693972.jpg'), 'titolo_specifiche': 'Che cosa c’è dentro',
        'specifiche': [{'id': 'rs-%d' % k, 'gruppo': g, 'nome': a, 'valore': b, 'unita': u} for k, (g, a, b, u) in enumerate([
            ('Base', 'Acqua di rose di Damasco', 'tonifica', ''), ('Base', 'Glicerina vegetale', 'trattiene l’acqua', ''),
            ('Attivi', 'Acido ialuronico', '1,5', '%'), ('Attivi', 'Niacinamide', '4', '%'), ('Attivi', 'Pantenolo', '1', '%'),
            ('Conservanti', 'Acido benzoico e sorbico', 'i più delicati', ''), ('Il flacone', 'Durata', '3', 'mesi')])],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'Testato su pelli sensibili · nickel tested · vegano',
        'cta_testo': 'Chiedi alla chimica', 'cta_url': CONTATTI + '#scrivici', 'cta_codice': False, 'cta_parametro': '',
        'dati_strutturati': True, 'accento': C['acc'], 'sfondo_scheda': C['chiaro'], 'colore_linee': C['filo'],
        'righe_alternate': False, 'nome_font_size': 40, 'nome_font_weight': '500', 'nome_color': C['testo'],
        'raggio': radius(18), 'raggio_pulsante': radius(999), 'padding_scheda': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40},
        'img_ratio': '4/5', 'img_fit': 'cover', 'foto_posizione': 'destra', 'larghezza_foto': 42, 'gap': 48,
        'colonne_specifiche': 1}, style={})
    out.append(t.section([t.one([scheda])], padding='large', width='large', html_id='creme',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'di ordinare.', [
        ('Vanno bene per la pelle sensibile?', 'Sì: niente profumi aggiunti né alcol, e ogni formula è testata su pelli '
                                               'sensibili. Il primo giorno provate su un polso.'),
        ('Quanto durano aperti?', 'Sei mesi dall’apertura: c’è il barattolino aperto disegnato sull’etichetta.'),
        ('Fate i campioncini?', 'Sì: il kit di prova con quattro mini da 5 ml costa 12 €, e li scalate dal primo ordine.'),
        ('Testate sugli animali?', 'No, mai: è vietato in Europa e non lo faremmo comunque. Tutti i prodotti sono vegani '
                                   'tranne il balsamo, con cera d’api.'),
    ], 'pd', bg=True))
    return out


def build_lab():
    t = Tpl(ID, 'il-laboratorio')
    out = [K.intro(t, 'Il laboratorio', ['Una chimica,', 'cento prove.'],
                   'Ogni formula nasce in laboratorio e cambia finché non funziona: il siero ha richiesto centodue versioni.',
                   bg=K.photo_bg('pexels-8450510.jpg', overlay=80, color=C['fondo']))]
    ingr = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '%02d' % (k + 1), 'image': K.img(f), 'link_url': PRODOTTI,
                   'color': C['acc']} for k, (n, s, d, f) in enumerate([
                       ('Acqua di rose', 'Rosa di Damasco · Bulgaria', 'La base del siero, al posto dell’acqua.', 'pexels-3528076.jpg'),
                       ('Ialuronico', 'Tre pesi molecolari', 'Idrata in superficie e un po’ più in fondo.', 'pexels-40097335.jpg'),
                       ('Squalano d’oliva', 'Frantoi umbri', 'Ammorbidisce senza ungere, come il sebo della pelle.', 'pexels-15699693.jpg'),
                       ('Rosa mosqueta', 'Spremuta a freddo', 'L’olio della notte: ricco, si assorbe lento.', 'pexels-18253735.jpg'),
                       ('Argilla rosa', 'Caolino e illite', 'Pulisce senza seccare: nella maschera della domenica.', 'pexels-4041187.jpg')])],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': C['tenue'],
        'number_hover_color': C['acc'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 48, 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['sec'], 'sub_size': 12,
        'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 24, 'hover_indent': 28,
        'hover_bg': mix(C['acc'], 6), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image', 'peek_width': 240,
        'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Gli ingredienti', ['Cinque che', 'tornano sempre.'], size=56,
                         tagline='Passate su un ingrediente: si affaccia da dove viene.')]),
        t.one([ingr]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_CREMA, 'La crema', 'Si scioglie <em>al contatto.</em>',
                             'L’emulsione è fatta a freddo, così resta leggera: si stende con un dito e la pelle la beve in '
                             'pochi secondi.', overlay=62))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi formula,', 'chi prova.'], size=56)]),
        K.squadra(t, [
            ('pexels-9768437.jpg', 'Elena Rossi', 'Chimica cosmetologa', 'Quindici anni nell’industria, poi il suo laboratorio. '
             'Scrive ogni formula e la riscrive finché non funziona.', '50% 25%'),
            ('pexels-8531365.jpg', 'Matilde Corsi', 'Controllo qualità', 'Prova ogni lotto: stabilità, pH, conservazione. Se '
             'non torna, il lotto non esce.', '50% 25%'),
            ('pexels-8450396.jpg', 'Ugo Ferrari', 'Gli oli e le essenze', 'Erborista da quarant’anni, sceglie gli oli dai '
             'frantoi e le rose dalla Bulgaria.', '50% 30%')], sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([t.classic('scrubtext', {
            'text': 'Una crema non deve promettere: <em>deve far stare bene la pelle</em>, ogni mattina.',
            'show_lead': True, 'lead': 'Per questo sulle nostre etichette non trovate «miracoli», ma la percentuale di ogni '
                                       'attivo e a cosa serve.',
            'scroll_reveal': True, 'dim_opacity': 18, 'accent': C['acc'], 'text_color': C['testo'], 'lead_color': C['tenue'],
            'size_min': 32, 'size_max': 62, 'max_width_ch': 22, 'lead_size': 18, 'text_font_weight': '500',
            'text_align': 'center'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Scriveteci,', 'risponde la chimica.'],
                   'Per una domanda sulla vostra pelle, un ordine, o per diventare una delle nostre erboristerie.',
                   bg=K.photo_bg('pexels-16233548.jpg', overlay=80, color=C['fondo']), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'cosa', True,
                     opzioni='Una domanda sulla mia pelle\nUn ordine\nLa videochiamata del rituale\nDiventare rivenditore'),
             K.campo('text', 'La vostra routine', 'routine', param='routine', larghezza='1-1',
                     segnaposto='Se l’avete composta, la trovate già qui'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1',
                     segnaposto='Che pelle avete, che cosa usate adesso, che cosa vi dà fastidio.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Una domanda,', 'una risposta vera.'],
                                'Vi risponde Elena, la nostra chimica, entro due giorni lavorativi.', VOCI, campi,
                                'Rugiada — contatto', 'Invia', 'Ricevuto. Elena vi risponde entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il laboratorio', ['A Parma,', 'in Borgo delle Colonne.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Borgo delle Colonne 21, Parma'), ('Il negozio', 'sabato 10 — 13'),
                                  ('Le erboristerie', 'venti, in tutta Italia'), ('Spedizioni', '48 ore, gratis da 50 €')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Rugiada · il laboratorio', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · evaporata', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>si è asciugata.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Basta una goccia per tornare all’inizio.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ACQUA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Negozi online', 'La goccia e il vetro liquido',
    ['cosmesi', 'skincare', 'siero', 'creme', 'parma', 'e-commerce', 'chiaro', 'slider pro'],
    'Un laboratorio di cosmesi, pesca chiara e rosa legno: in apertura il prodotto nell’arco al centro e il titolo '
    'diviso ai due lati, la goccia che cade sulla cima e si apre in un’onda, fra bolle di vetro liquido, sul video '
    'dell’acqua e delle creme; le texture sotto la lente, il configuratore della routine col prezzo, gli ingredienti '
    'e il laboratorio.',
    [('I prodotti', PRODOTTI), ('Il laboratorio', LAB), ('La routine', PRODOTTI + '#routine'), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('prodotti', 'I prodotti', 'prodotti'), ('il-laboratorio', 'Il laboratorio', 'il-laboratorio'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Hanken Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Ibarra Real Nova\', \'Georgia\', serif',
    '\'Red Hat Mono\', ui-monospace, Menlo, monospace', ['Ibarra Real Nova', 'Hanken Grotesk', 'Red Hat Mono'],
    corsivo_di=['Ibarra Real Nova'], peso_titoli='500', raggio=18, grana=3, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '3'  # apertura scura sul video pieno, 31 ottobre 2026


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'prodotti.json': build_prodotti(), 'il-laboratorio.json': build_lab(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
