# -*- coding: utf-8 -*-
"""Genera il tema «Fiori d’Arancio» (id fiori-d-arancio): una wedding planner di Como con un fiorista, che organizza
matrimoni sul lago, fra le vigne e gli ulivi. Dal primo caffè all’ultimo ballo, e il giorno dopo il brunch.

Tema chiaro, avorio e carta, col verde salvia profondo, l’albicocca e lo champagne. Titoli in Bodoni Moda (anche
corsivo), testo in Tenor Sans, date in Fragment Mono.

Apertura: Slider Pro come un album di inviti. Ogni diapositiva è un matrimonio vero: la foto a tutto schermo che si
muove piano, sopra un cartoncino che entra da sinistra coi nomi degli sposi, il luogo e la data, e il sigillo di
ceralacca con le iniziali che si posa sull’angolo. Si passa all’altro matrimonio con un taglio di carta in diagonale;
sotto, le miniature dei quattro matrimoni; sopra tutto cadono piano petali di fiori d’arancio.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'fiori-d-arancio'
NAME = 'Fiori d’Arancio'

HEX = {
    'primary': '#545E3D', 'primary_contrast': '#FFFFFF', 'secondary': '#C98463', 'secondary_contrast': '#24211D',
    'accent': '#D9C08C', 'dark': '#24211D', 'light': '#FFFFFF', 'muted': '#F2ECE1', 'muted_contrast': '#24211D',
    'text': '#24211D', 'text_muted': '#675F53', 'background': '#FBF8F2', 'border': '#E4DACB', 'link': '#545E3D',
}

HOME, MATRIMONI, METODO, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:matrimoni}}', '{{PAGINA:il-metodo}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'ciao@fioridarancio.example', '+39 031 000 1406', 'tel:+390310001406'
LAT, LNG = '45.8139', '9.0806'

V_RICEVIMENTO, V_TAVOLA, V_GRANO, V_FIORI, V_LUCI, V_CALICI, V_CANDELE = (
    'pexels-video-20203976', 'pexels-video-28572592', 'pexels-video-32346559', 'pexels-video-9737873',
    'pexels-video-34602730', 'pexels-video-5703614', 'pexels-video-12350305')
PHOTOS = [
    'pexels-29205725.jpg', 'pexels-39532908.jpg', 'pexels-2909569.jpg', 'pexels-32552764.jpg', 'pexels-35349365.jpg',
    'pexels-39352696.jpg', 'pexels-17001833.jpg', 'pexels-17206102.jpg', 'pexels-29040917.jpg', 'pexels-9644360.jpg',
    'pexels-35985203.jpg', 'pexels-30312512.jpg', 'pexels-17034946.jpg', 'pexels-30191032.jpg', 'pexels-32716815.jpg',
    'pexels-28267040.jpg', 'pexels-14682511.jpg', 'pexels-36610234.jpg', 'pexels-34803738.jpg', 'pexels-9820926.jpg',
    'pexels-27115145.jpg', 'pexels-14515700.jpg', 'pexels-30206324.jpg', 'pexels-38598635.jpg', 'pexels-8459379.jpg',
    'pexels-13456843.jpg', 'pexels-34597474.jpg', 'pexels-6032061.jpg', 'pexels-13235218.jpg', 'pexels-37279680.jpg',
    'pexels-29569636.jpg', 'pexels-11712500.jpg', 'pexels-29388921.jpg', 'pexels-11650472.jpg', 'pexels-31039958.jpg',
    'pexels-8459367.jpg', 'pexels-4968707.jpg', 'pexels-8450496.jpg',
]
VIDEOS = [V_RICEVIMENTO, V_TAVOLA, V_GRANO, V_FIORI, V_LUCI, V_CALICI, V_CANDELE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media=mix('var(--olo-color-accent)', 55, 'var(--olo-color-light)'))
C = K.c
TITOLI, MONO = 'Bodoni Moda', 'Fragment Mono'
VOCI = [('map-pin', 'Via Borgovico 12 · Como', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Fiori d’Arancio', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Fiori d’Arancio', 'Matrimoni sul lago, fra le vigne e gli ulivi, dal 2012. Ginevra organizza, '
                   'Tommaso fa i fiori; per il resto abbiamo i nostri fornitori, gli stessi da dieci anni.',
                   [('Matrimoni', MATRIMONI), ('Il metodo', METODO), ('Contatti', CONTATTI), ('Dove siamo', CONTATTI + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Lo studio', 'Su appuntamento, dal martedì al sabato.<br>D’estate ci trovate ai matrimoni.', VOCI,
                   '© 2026 Fiori d’Arancio di Ginevra Riva · Como · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')], marchio_size=110,
                   fondo=C['sala'])


def apertura(t):
    m = K.mob
    album = [
        ('Villa sul lago · Como', 'Giulia', 'e Marco', '14 · 06 · 2026', 'Il rito in giardino sotto i cipressi, la cena '
         'sul pontile, il ballo con la barca ferma davanti. Centoventi ospiti da sei paesi.', 'pexels-14682511.jpg',
         'G·M', 'villa'),
        ('Uliveto · Val d’Orcia', 'Sara', 'e Daniel', '06 · 09 · 2025', 'Una tavola lunga quaranta metri fra gli ulivi, '
         'le luci appese ai rami, la pizza nel forno a legna a mezzanotte.', 'pexels-9644360.jpg', 'S·D', 'uliveto'),
        ('Vigna · Langhe', 'Chiara', 'e Luca', '18 · 10 · 2025', 'Il sì fra i filari in autunno, la cena nella cantina '
         'di botti, il Barolo dell’anno in cui sono nati.', 'pexels-29205725.jpg', 'C·L', 'vigna'),
        ('Giardino · Lago Maggiore', 'Elena', 'e Paolo', '24 · 05 · 2025', 'Venti ospiti, un pergolato di glicine, un '
         'pranzo che è finito alle sei di sera. I matrimoni piccoli ci piacciono tanto.', 'pexels-35349365.jpg',
         'E·P', 'giardino'),
    ]
    sl = []
    for i, (luogo, n1, n2, data, testo, foto, sigla, aid) in enumerate(album):
        n = i + 1
        sl.append(K.diapositiva('fa-%d' % n, K.sfondo_foto(foto, overlay=0.12, scala=1.12, durata=10000,
                                                          verso='in' if n % 2 else 'out'), [
            K.livello('fa-c%d' % n, tipo='shape', x=5.5, y=17, w=34, h=66, bgColor=mix(C['chiaro'], 95), entra='maskFromLeft',
                      ritardo=150, durata=1000, esce='fadeOut', borderWidth=1, borderColor=mix(C['acc2'], 60),
                      boxShadow={'x': 0, 'y': 30, 'blur': 70, 'spread': -18, 'color': mix(C['nero'], 45)},
                      responsive={'mobile': {'x': 5, 'y': 44, 'width': 90, 'height': 46}}),
            K.livello('fa-o%d' % n, contenuto=luogo, x=9, y=23.5, w=28, size=11, peso='400', colore=C['acc'], ritardo=700,
                      letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn',
                      responsive={'mobile': {'fontSize': m(10), 'y': 48.6, 'x': 11, 'width': 70}}),
            K.livello('fa-t%d' % n, contenuto=n1, tag='h1' if n == 1 else 'h2', x=9, y=27.5, w=29, size=88, peso='400',
                      colore=C['testo'], ritardo=850, durata=1200, lineHeight=1.0, entra='fadeIn', fontFamily=TITOLI,
                      charAnim={'stagger': 45, 'offsetY': 20, 'blur': 6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(40), 'y': 51, 'x': 11, 'width': 80}}),
            K.livello('fa-i%d' % n, contenuto=n2, tag='p', x=9, y=38, w=29, size=88, peso='400', fontStyle='italic',
                      colore=C['acc'], ritardo=1150, durata=1200, lineHeight=1.0, entra='fadeIn', fontFamily=TITOLI,
                      charAnim={'stagger': 45, 'offsetY': 20, 'blur': 6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(40), 'y': 55.6, 'x': 11, 'width': 80}}),
            K.livello('fa-d%d' % n, contenuto=data, x=9, y=50.5, w=28, size=14, peso='400', colore=C['testo'], ritardo=1500,
                      letterSpacing=4, fontFamily=MONO, entra='fadeIn',
                      responsive={'mobile': {'fontSize': m(12), 'y': 61.2, 'x': 11, 'width': 70}}),
            K.livello('fa-s%d' % n, contenuto=testo, x=9, y=55.5, w=26.5, size=16, peso='400', colore=C['tenue'],
                      ritardo=1650, lineHeight=1.65, entra='fadeIn',
                      responsive={'mobile': {'fontSize': m(13.5), 'y': 64.6, 'x': 11, 'width': 78}}),
            K.livello('fa-b%d' % n, tipo='button', contenuto='Il loro matrimonio', x=9, y=72.5, w='auto', size=11,
                      peso='500', colore=C['testo'], bgColor='transparent', padding=14, borderWidth=1,
                      borderColor=C['testo'], letterSpacing=3, textTransform='uppercase', ritardo=1850,
                      buttonUrl=MATRIMONI + '#' + aid, hover={'bgColor': C['acc2'], 'borderColor': C['acc2'], 'duration': 250},
                      responsive={'mobile': {'y': 82, 'x': 11, 'fontSize': m(10)}}),
            # il sigillo di ceralacca con le iniziali
            K.livello('fa-z%d' % n, tipo='shape', x=34.6, y=12.5, w=7, h=11.2, bgColor=C['acc'], borderRadius=999,
                      borderWidth=4, borderColor=mix(C['acc'], 70, C['chiaro']), entra='popUpBack', ritardo=2100, durata=800,
                      esce='fadeOut', boxShadow={'x': 0, 'y': 10, 'blur': 22, 'spread': -6, 'color': mix(C['nero'], 45)},
                      responsive={'mobile': {'x': 74, 'y': 40.6, 'width': 18, 'height': 8.2}}),
            K.livello('fa-zi%d' % n, contenuto=sigla, x=34.6, y=16.3, w=7, size=20, peso='400', fontStyle='italic',
                      colore=C['chiaro'], align='center', fontFamily=TITOLI, entra='popUpBack', ritardo=2100, durata=800,
                      responsive={'mobile': {'x': 74, 'y': 43.3, 'width': 18, 'fontSize': m(17)}}),
        ], durata=7500))
    petali = []
    for k, (x, y, sz, anim, dur) in enumerate([(52, 10, 0.9, 'drift', 9000), (63, 26, 0.7, 'float', 6400),
                                              (72, 8, 1.0, 'wander', 11000), (81, 34, 0.8, 'drift', 8200),
                                              (90, 18, 0.7, 'float', 7000), (58, 62, 0.8, 'wander', 10400),
                                              (76, 70, 0.9, 'drift', 9400), (93, 56, 0.6, 'float', 6800)]):
        petali.append(K.livello('fa-p%d' % k, tipo='shape', x=x, y=y, w=sz, h=sz * 1.9, bgColor=C['chiaro'], opacity=92,
                                borderRadiusLinked=False, borderRadiusTL=18, borderRadiusTR=2, borderRadiusBR=18,
                                borderRadiusBL=2, entra='fadeIn', ritardo=600 + 180 * k, durata=1600, animLoop=anim,
                                animLoopDuration=dur, parallaxDepth=4 + k % 4,
                                boxShadow={'x': 0, 'y': 2, 'blur': 8, 'spread': 0, 'color': mix(C['nero'], 25)},
                                responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=petali, altezza={'mode': 'vh', 'value': 90}, transizione='paperCut',
                       durata_trans=1300, showArrows=True, arrowStyle='outline', showDots=False, showThumbs=True,
                       thumbPosition='bottom', parallax=True, parallaxIntensity=4, autoplaySpeed=7500,
                       progressBarColor=C['acc2'])


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    numeri = []
    for valore, massimo, suffisso, titolo in [('128', '128', '', 'matrimoni dal 2012'), ('9', '20', '', 'regioni, dalle Alpi alla Puglia'),
                                              ('98', '100', '%', 'delle coppie ci consiglierebbe')]:
        numeri.append(t.col([t.classic('countercircle', {
            'value': valore, 'max_value': massimo, 'suffix': suffisso, 'prefix': '', 'title': titolo, 'size': '220',
            'stroke_width': '2', 'stroke_color': C['acc'], 'track_color': C['filo'], 'text_color': C['testo'],
            'title_color': C['tenue'], 'duration': '2600', 'title_position': 'below'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-4968707.jpg', 'Ginevra Riva scrive il programma di un matrimonio', ratio='4/5',
                              position='50% 30%', drift=True)], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Ginevra e Tommaso', ['Un matrimonio', 'non si improvvisa.'], size=60, layout='stack',
                                size_mobile=38),
                      K.text(t, 'Ginevra organizza dal 2012: il luogo, i fornitori, il programma minuto per minuto, la '
                                'pioggia. Tommaso fa i fiori, dal bouquet ai pergolati. Prendiamo al massimo quindici '
                                'matrimoni l’anno, per esserci davvero a ognuno.', size=17, color=C['testo']),
                      K.button(t, 'Il metodo', METODO, pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
        t.row(numeri, layout='33-33-33', gap=24, style={'margin_top': 72}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 120}))
    giorno = t.classic('timeline', {
        'items': [{'date': o, 'title': ti, 'tag': tag, 'description': d, 'image': K.img(f), 'video': '', 'icon': ic,
                   'category': 'primary'} for o, ti, tag, d, f, ic in [
            ('10:00', 'Il caffè con la sposa', 'Mattina', 'Arriviamo prima di tutti: fiori, trucco, il vestito appeso '
             'alla finestra. Il telefono lo teniamo noi.', 'pexels-8459367.jpg', 'coffee'),
            ('15:30', 'Il sì', 'Rito', 'Le sedie bianche, il violino, i fazzoletti nel cesto per chi piange. Contiamo '
             'gli ospiti due volte.', 'pexels-35349365.jpg', 'heart'),
            ('17:00', 'Le bollicine', 'Aperitivo', 'Il fotografo porta via gli sposi per mezz’ora: agli ospiti pensiamo '
             'noi, con le bollicine e i fiori di zucca.', 'pexels-17001833.jpg', 'wine'),
            ('20:00', 'La tavola', 'Cena', 'Il tableau scritto a mano, i segnaposto, i menù per chi non mangia glutine. '
             'Tutto al suo posto, anche col vento.', 'pexels-35985203.jpg', 'utensils'),
            ('23:00', 'La torta e il ballo', 'Festa', 'La torta tagliata sotto le luci, il primo ballo, la pista che si '
             'riempie. Noi andiamo via per ultimi.', 'pexels-29569636.jpg', 'music')]],
        'tl_layout': 'alt', 'tl_reveal': 'bloom', 'tl_theme': 'paper', 'tl_card': 'polaroid', 'tl_thread': 'dash',
        'tl_node': 'year', 'tl_color': 'cat', 'tl_media': 'on', 'tl_density': 'comfy', 'tl_transparent': True,
        'tl_title_size': 24, 'tl_title_weight': '400', 'tl_title_family': 'heading', 'tl_show_tag': True}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Il vostro giorno', ['Dal caffè del mattino', 'all’ultimo ballo.'], size=60,
                         tagline='Ogni matrimonio ha il suo programma, scritto minuto per minuto e stampato per tutti i '
                                 'fornitori. Questo è uno dei nostri.')]),
        t.one([giorno]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    luoghi = t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': k, 'title': ti, 'link': MATRIMONI + '#' + a, 'span': s, 'aspect': asp}
                  for f, k, ti, a, s, asp in [
                      ('pexels-36610234.jpg', 'Lago di Como · fino a 200 ospiti', 'Le ville sul lago', 'villa', 6, '4/3'),
                      ('pexels-34803738.jpg', 'Toscana e Umbria · fino a 150', 'Gli uliveti', 'uliveto', 6, '4/3'),
                      ('pexels-2909569.jpg', 'Langhe e Roero · fino a 120', 'Le vigne', 'vigna', 6, '4/3'),
                      ('pexels-32552764.jpg', 'Lago Maggiore · fino a 40', 'I giardini segreti', 'giardino', 6, '4/3')]],
        'columns': 2, 'gap': 16, 'object_position': 'center center', 'object_fit': 'cover', 'radius': 0,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 22), 'arrow_color': C['chiaro'], 'arrow_hover_bg': C['acc2'],
        'arrow_hover_color': C['testo'], 'show_arrow': True, 'title_size': 38, 'title_weight': '400', 'kicker_size': 11,
        'card_radius': radius(0)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'I luoghi', ['Quattro paesaggi,', 'cento posti scelti.'], size=60,
                         tagline='Li abbiamo visti tutti, con la pioggia e col sole. Vi portiamo a vederne tre, poi '
                                 'scegliete voi.')]),
        t.one([luoghi]),
    ], padding='large', width='large', html_id='luoghi', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_FIORI, 'I fiori di Tommaso', 'Ogni fiore <em>scelto a mano.</em>',
                             'Al mercato di Sanremo il martedì prima, nei campi del lago il giorno stesso. Niente fiori '
                             'di plastica, niente fiori che non sono di stagione.', overlay=66))
    out.append(t.section([t.one([t.classic('testimonial', {
        'quote': 'Ginevra ha pensato a tutto, anche a quello che non sapevamo di dover pensare. Il giorno del matrimonio '
                 'non abbiamo guardato il telefono una volta.', 'author_name': 'Giulia e Marco',
        'author_role': 'Villa sul lago · giugno 2026', 'avatar': '', 'rating': '5', 'text_color': C['testo'],
        'star_color': C['acc2'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc2']}, style={})])], bg=solid(C['sala']), padding='large', width='large',
        style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_RICEVIMENTO, 'La tavola', 'Quaranta metri <em>di tovaglia.</em>',
                          'Le tavole lunghe fra gli ulivi sono la nostra cosa preferita: tutti si guardano, tutti si '
                          'parlano. Le apparecchiamo il pomeriggio, una forchetta alla volta.', 'I matrimoni', MATRIMONI,
                          overlay=66, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Fiori d’Arancio',
                page_intro_caption='Matrimoni · dal 2012', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_matrimoni():
    t = Tpl(ID, 'matrimoni')
    out = [K.intro(t, 'I matrimoni', ['Ogni matrimonio', 'è una storia sua.'],
                   'Quattro dei quindici dell’anno, raccontati da chi li ha vissuti. Il resto lo raccontiamo a voce, '
                   'davanti a un caffè.', bg=K.photo_bg('pexels-34597474.jpg', overlay=45), overlay_text=True)]
    storie = [
        ('villa', 'Villa sul lago · Como · giugno 2026', 'Giulia <em>e Marco</em>', 'Centoventi ospiti da sei paesi, un '
         'traduttore per i discorsi, la cena sul pontile e il ballo con la barca ferma davanti. Il temporale è arrivato '
         'alle undici: eravamo pronti, col tendone già montato dietro la villa.',
         [('Ospiti', '120'), ('Fiori', 'peonie bianche e ortensie'), ('Il momento', 'il ballo sul pontile')],
         ['pexels-14682511.jpg', 'pexels-17206102.jpg', 'pexels-6032061.jpg']),
        ('uliveto', 'Uliveto · Val d’Orcia · settembre 2025', 'Sara <em>e Daniel</em>', 'Lei di Siena, lui di '
         'Melbourne. Una tavola lunga quaranta metri fra gli ulivi, le luci appese ai rami, il forno a legna acceso a '
         'mezzanotte per la pizza. Nessuno è andato a dormire prima delle tre.',
         [('Ospiti', '90'), ('Fiori', 'rami d’ulivo e rosmarino'), ('Il momento', 'la pizza a mezzanotte')],
         ['pexels-9644360.jpg', 'pexels-35985203.jpg', 'pexels-34597474.jpg']),
        ('vigna', 'Vigna · Langhe · ottobre 2025', 'Chiara <em>e Luca</em>', 'Il sì fra i filari dopo la vendemmia, '
         'con le foglie già rosse. La cena nella cantina delle botti, a lume di candela, e il Barolo del 1994, l’anno in '
         'cui sono nati tutti e due.',
         [('Ospiti', '70'), ('Fiori', 'dalie, grano, foglie di vite'), ('Il momento', 'il brindisi in cantina')],
         ['pexels-29205725.jpg', 'pexels-17001833.jpg', 'pexels-37279680.jpg']),
        ('giardino', 'Giardino · Lago Maggiore · maggio 2025', 'Elena <em>e Paolo</em>', 'Venti ospiti, un pergolato '
         'di glicine, un pranzo di sei portate che è finito alle sei di sera. La torta l’ha fatta la nonna di Paolo, '
         'noi abbiamo pensato ai fiori e alle sedie.',
         [('Ospiti', '20'), ('Fiori', 'glicine e rose antiche'), ('Il momento', 'la torta della nonna')],
         ['pexels-35349365.jpg', 'pexels-11712500.jpg', 'pexels-32552764.jpg']),
    ]
    for k, (aid, occ, nomi, testo, dati, foto) in enumerate(storie):
        out.append(t.section([
            t.row([t.col([K.label(t, occ, color=C['acc']), K.headline(t, nomi, size=64, tag='h2', pad_bottom=16),
                          K.text(t, testo, size=16), K.specs(t, dati)], wm='2-5', ws='1-1'),
                   t.col([t.row([t.col([K.image(t, foto[0], 'Il matrimonio di ' + nomi.split(' <em>')[0], ratio='4/5')],
                                       wm='1-2', ws='1-1'),
                                 t.col([K.image(t, foto[1], 'La tavola', ratio='1/1', lightbox=True),
                                        K.image(t, foto[2], 'La festa', ratio='16/10', lightbox=True)], wm='1-2', ws='1-1')],
                                layout='50-50', gap=14, anim=None)], wm='3-5', ws='1-1')],
                  layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 else None,
            style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_LUCI, 'Dopo mezzanotte', 'Le luci <em>restano accese.</em>',
                             'Fino all’ultimo ospite, fino all’ultimo ballo. Poi spegniamo noi, e il giorno dopo c’è il '
                             'brunch.', overlay=68,
                             extra=[K.button(t, 'Raccontateci il vostro', CONTATTI + '#scrivici', kind='pieno', align='center',
                                             pad_top=18)]))
    return out


def build_metodo():
    t = Tpl(ID, 'il-metodo')
    out = [K.intro(t, 'Il metodo', ['Un anno prima,', 'un giorno solo.'],
                   'In media lavoriamo dodici mesi a un matrimonio: tre sopralluoghi, venti fornitori, un programma che '
                   'cambia fino all’ultima settimana. Il giorno stesso, voi non dovete fare niente.',
                   bg=K.photo_bg('pexels-11650472.jpg', overlay=45), overlay_text=True)]
    piani = [('Il giorno', '2.400', 'per il coordinamento', 'Il programma minuto per minuto\nIl coordinamento dei '
              'fornitori\nNoi due presenti dalle 10 alla fine', False, ''),
             ('Dal sì al ballo', '6.800', 'organizzazione completa', 'Ricerca e sopralluoghi delle location\nScelta e '
              'contratti dei fornitori\nFiori e allestimenti di Tommaso\nIl giorno, con quattro assistenti', True,
              'La più scelta'),
             ('Matrimonio piccolo', '1.900', 'fino a trenta ospiti', 'Un luogo segreto scelto per voi\nFiori e tavola\nIl '
              'pranzo o la cena, senza fretta', False, '')]
    cols = []
    for nome, prezzo, periodo, voci, pop, badge in piani:
        cols.append(t.col([t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': periodo,
            'features': voci, 'check_style': 'circle-check', 'cta_text': 'Parliamone', 'cta_url': CONTATTI + '#scrivici',
            'cta_bg_color': C['acc'] if pop else 'transparent', 'cta_text_color': C['on_acc'] if pop else C['testo'],
            'cta_border_width': '0' if pop else '1', 'cta_border_color': C['testo'], 'cta_radius': '0',
            'cta_hover_bg_color': C['testo'], 'additional_info': 'Più IVA · i fornitori si pagano a parte',
            'is_popular': pop, 'badge_text': badge, 'badge_bg_color': C['acc2'], 'badge_text_color': C['testo'],
            'bg_color': C['chiaro'], 'accent_color': C['acc'], 'text_color': C['testo'], 'price_color': C['testo'],
            'border_radius': '0', 'shadow': 'md' if pop else 'none'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le tre formule', ['Scegliete quanto', 'volete fare voi.'], size=56,
                         tagline='Il primo incontro è gratuito e senza impegno. Il preventivo arriva scritto, con ogni voce.')]),
        t.row(cols, layout='33-33-33', gap=24, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8450496.jpg', 'Tommaso compone un mazzo di peonie bianche', ratio='4/5',
                              position='50% 35%')], wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Tommaso Neri · fiorista', color=C['acc']),
                      K.headline(t, 'I fiori arrivano <em>la mattina stessa.</em>', size=52, tag='h2', pad_bottom=14),
                      K.text(t, 'Tommaso fa i bouquet la notte prima e allestisce il giorno stesso, così i fiori sono '
                                'freschi fino al ballo. Usa solo fiori di stagione, e a fine festa li regaliamo agli ospiti.'),
                      K.specs(t, [('Il bouquet', 'consegnato alle 11'), ('Gli allestimenti', 'dalle 8 del mattino'),
                                  ('Dopo la festa', 'i centrotavola agli ospiti')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'del primo incontro.', [
        ('Con quanto anticipo dobbiamo cercarvi?', 'Dodici mesi è l’ideale, sei mesi bastano. Per i matrimoni piccoli '
                                                   'anche tre.'),
        ('Lavorate fuori dalla Lombardia?', 'Sì, in nove regioni: soprattutto laghi, Langhe, Toscana e Puglia.'),
        ('E se piove?', 'C’è sempre un piano B scritto, con il tendone o la sala già prenotati. Lo decidiamo due giorni '
                        'prima, insieme.'),
        ('Possiamo scegliere noi i fornitori?', 'Certo. Vi proponiamo i nostri, ma coordiniamo volentieri anche quelli '
                                                'che avete già.'),
    ], 'me', bg=True))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Raccontateci', 'il vostro giorno.'],
                   'Ci bastano la data, il numero di ospiti e un posto che vi piace, anche solo per aria. Vi rispondiamo '
                   'entro due giorni con una proposta di incontro.', bg=K.photo_bg('pexels-31039958.jpg', overlay=45),
                   overlay_text=True)]
    campi = [K.campo('text', 'I vostri nomi', 'nomi', True, segnaposto='Giulia e Marco'), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'), K.campo('date', 'La data (anche indicativa)', 'data'),
             K.campo('select', 'Quanti ospiti', 'ospiti', opzioni='Fino a 30\nDa 30 a 80\nDa 80 a 150\nPiù di 150'),
             K.campo('select', 'Dove vi piacerebbe', 'dove', opzioni='Sul lago\nFra le vigne\nFra gli ulivi\nIn un giardino\nNon lo sappiamo ancora'),
             K.campo('textarea', 'Raccontateci', 'messaggio', larghezza='1-1',
                     segnaposto='Come vi siete conosciuti, cosa vi piace, cosa non volete assolutamente.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Il primo incontro', ['Un caffè,', 'senza impegno.'],
                                'Ci vediamo in studio a Como, in videochiamata o nel posto che avete in mente.', VOCI, campi,
                                'Fiori d’Arancio — primo incontro', 'Mandate', 'Grazie! Vi scriviamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Lo studio,', 'a due passi dal lago.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via Borgovico 12, Como'), ('Dalla stazione', 'Como San Giovanni, cinque minuti'),
                                  ('Ricevimento', 'su appuntamento, martedì — sabato')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fiori d’Arancio · wedding planner', scuro=False, hex_marker=HEX['primary'],
                              zoom='16')], wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · invito smarrito', color=C['acc2'], align='center'),
                       K.headline(t, 'Questa pagina <em>non è stata invitata.</em>', size=88, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=C['acc2']),
                       K.text(t, 'Torniamo alla festa: è di qua.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CANDELE, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Eventi e musica', 'Dal sì all’ultimo ballo',
    ['matrimoni', 'wedding planner', 'fiori', 'como', 'chiaro', 'eleganti', 'slider pro'],
    'Una wedding planner sul lago di Como con il suo fiorista, avorio e salvia: in apertura un album di matrimoni veri '
    'col cartoncino dei nomi e il sigillo di ceralacca, il taglio di carta fra uno e l’altro e i petali che cadono; il '
    'giorno minuto per minuto, i luoghi, i numeri e le tre formule.',
    [('Matrimoni', MATRIMONI), ('Il metodo', METODO), ('I luoghi', HOME + '#luoghi'), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('matrimoni', 'Matrimoni', 'matrimoni'), ('il-metodo', 'Il metodo', 'il-metodo'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Tenor Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bodoni Moda\', \'Didot\', serif',
    '\'Fragment Mono\', ui-monospace, Menlo, monospace', ['Bodoni Moda', 'Tenor Sans', 'Fragment Mono'],
    corsivo_di=['Bodoni Moda'], peso_titoli='400', raggio=0, grana=0, titoli_line='1.0', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'matrimoni.json': build_matrimoni(), 'il-metodo.json': build_metodo(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
