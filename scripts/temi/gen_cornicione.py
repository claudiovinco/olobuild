# -*- coding: utf-8 -*-
"""Genera il tema «Cornicione» (id cornicione): una pizzeria contemporanea a Bologna, impasto di due giorni, forno a
legna a 450 gradi, farine macinate a pietra; si mangia al tavolo o si porta via.

Tema chiaro e caldo: farina, pomodoro, basilico e la crosta dorata. Titoli in Hedvig Letters Serif, testo in Familjen
Grotesk, dati in Victor Mono.

Apertura: Slider Pro «la pizza della sera», alto 16:9 perché i cerchi restino cerchi. Una pizza per diapositiva, vista
dall'alto in una finestra rotonda che gira piano come sulla pala; gli ingredienti (pomodoro, fiordilatte, farina)
cadono nella diapositiva e rimbalzano a ogni pizza nuova; il prezzo in un bollino, le linguette coi nomi delle pizze.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'cornicione'
NAME = 'Cornicione'

HEX = {
    'primary': '#B8321C', 'primary_contrast': '#FFFFFF', 'secondary': '#2F6B3A', 'secondary_contrast': '#FFFFFF',
    'accent': '#E9A83B', 'dark': '#1F1A17', 'light': '#FFFBF5', 'muted': '#EDE2D1', 'muted_contrast': '#211C19',
    'text': '#211C19', 'text_muted': '#6B6058', 'background': '#F6EFE4', 'border': '#DDCDB7', 'link': '#B8321C',
}

HOME, MENU, IMPASTO, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:il-menu}}', '{{PAGINA:l-impasto}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'forno@cornicione.example', '+39 051 000 4848', 'tel:+390510004848'
LAT, LNG = '44.4955', '11.3314'

PHOTOS = [
    'pexels-17518462.jpg', 'pexels-19260730.jpg', 'pexels-33593001.jpg', 'pexels-19260835.jpg', 'pexels-4001871.jpg',
    'pexels-35017347.jpg', 'pexels-17932142.jpg', 'pexels-13736876.jpg', 'pexels-905847.jpg', 'pexels-1878346.jpg',
    'pexels-36430040.jpg', 'pexels-28509915.jpg', 'pexels-38539266.jpg', 'pexels-29793494.jpg', 'pexels-32293385.jpg',
    'pexels-28907096.jpg', 'pexels-36912598.jpg', 'pexels-5903441.jpg', 'pexels-9685268.jpg', 'pexels-5953554.jpg',
    'pexels-5953523.jpg', 'pexels-5056618.jpg', 'pexels-8290125.jpg', 'pexels-36430056.jpg', 'pexels-32570609.jpg',
    'pexels-34421471.jpg', 'pexels-28841126.jpg', 'pexels-3669501.jpg', 'pexels-34247760.jpg', 'pexels-8471577.jpg',
    'pexels-38597849.jpg', 'pexels-32934453.jpg', 'pexels-38455659.jpg', 'pexels-5947611.jpg', 'pexels-10009357.jpg',
    'pexels-32293375.jpg', 'pexels-5953500.jpg', 'pexels-5987500.jpg', 'pexels-39886102.jpg', 'pexels-29460805.jpg',
    'pexels-26838689.jpg', 'pexels-365459.jpg', 'pexels-34667359.jpg',
]
VIDEOS = ['pexels-video-39235627', 'pexels-video-5899154', 'pexels-video-5898377', 'pexels-video-5897984']
V_FORNO, V_STESURA, V_CONDIRE, V_TAGLIO = VIDEOS
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='400', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media=mix('var(--olo-color-accent)', 70, 'var(--olo-color-light)'))
C = K.c
TITOLI, MONO = 'Hedvig Letters Serif', 'Victor Mono'
VOCI = [('map-pin', 'Via del Pratello 48 · Bologna', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Cornicione', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Cornicione', 'Pizzeria in via del Pratello dal 2017: impasto di due giorni con farine macinate a '
                   'pietra, forno a legna a 450 gradi, pomodoro San Marzano e fiordilatte di Agerola. Al tavolo o da asporto.',
                   [('Il menu', MENU), ('L’impasto', IMPASTO), ('Prenota', PRENOTA), ('Asporto', PRENOTA + '#asporto'),
                    ('Privacy', '/privacy-policy/')],
                   'Il forno', 'Tutte le sere 19 — 23:30<br>Sabato e domenica anche 12:30 — 14:30<br>Chiuso il martedì', VOCI,
                   '© 2026 Cornicione srl · Bologna · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=130,
                   fondo=C['sala'])


PIZZE = [  # linguetta, occhiello, nome, sottotitolo, testo, prezzo, foto, fondo
    ('Margherita', 'N° 1 · la classica', 'Margherita', 'come la prima volta.', 'San Marzano schiacciato a mano, fiordilatte '
     'di Agerola, basilico dell’orto, olio di Sorrento. La prima che impariamo, l’ultima che smettiamo di rifare.', '8 €',
     'pexels-17932142.jpg', mix(C['acc'], 10, C['fondo'])),
    ('Pomodorino', 'N° 2 · d’estate', 'Pomodorino', 'rosso e giallo.', 'Pomodorini del piennolo e datterini gialli '
     'confit, fiordilatte a fine cottura, basilico fritto. Esce dal forno più chiara, si scioglie in bocca.', '10 €',
     'pexels-33593001.jpg', mix(C['acc2'], 22, C['fondo'])),
    ('Capricciosa', 'N° 3 · la ricca', 'Capricciosa', 'un po’ di tutto.', 'Pomodoro, fiordilatte, funghi chiodini, '
     'carciofi sott’olio, olive di Gaeta, prosciutto cotto: la pizza delle domeniche in famiglia.', '12 €',
     'pexels-19260835.jpg', mix(C['sec'], 12, C['fondo'])),
    ('Datterino', 'N° 4 · dal forno', 'Datterino', 'bruciacchiato.', 'Datterini rossi che scoppiano a 450 gradi, '
     'fiordilatte, pecorino romano e foglie di basilico messe dopo, per non perdere il profumo.', '10 €',
     'pexels-35017347.jpg', mix(C['acc'], 6, mix(C['acc2'], 18, C['fondo']))),
]


def apertura(t):
    m = K.mob
    sl = []
    # la pizza: 580 px nel piatto di 640 su una tela di 1440 × 810 (16:9); il piatto parte sotto la testata
    px, py, pw, ph = 61.08, 16.2, 40.28, 71.6
    cadute = [  # foto, x, y, x e y sul telefono
        ('pexels-8471577.jpg', 64.3, 12.2, 4, 22), ('pexels-3669501.jpg', 57.85, 64.3, 79, 21.5),
        ('pexels-5947611.jpg', 73.2, 83.5, 72, 31.5)]
    for i, (tab, occ, nome, sotto, testo, prezzo, foto, fondo) in enumerate(PIZZE):
        n = i + 1
        livelli = K.veli('co-velo%d' % n, 'sinistra', 46, 62) + [
            K.livello('co-piatto%d' % n, tipo='shape', x=59, y=12.5, w=44.44, h=79.01, bgColor=C['chiaro'],
                      borderRadius=999, entra='zoomIn', ritardo=0, durata=900, esce='fadeOut',
                      boxShadow={'x': 0, 'y': 34, 'blur': 70, 'spread': -18, 'color': mix(C['nero'], 30)},
                      responsive={'mobile': {'x': 13.3, 'y': 7, 'width': 73.4, 'height': 33.6}, 'tablet': {'y': 15.5}}),
            K.livello('co-p%d' % n, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=px, y=py, w=pw, h=ph,
                      borderRadius=999,
                      timeline=K.tl(px, py, [(0, {'rotation': -30, 'scale': 0.86, 'opacity': 0}, 'power3.out'),
                                             (1300, {'rotation': 0, 'scale': 1, 'opacity': 100}),
                                             (61300, {'rotation': 360, 'scale': 1, 'opacity': 100})], 61300),
                      responsive={'mobile': {'x': 15.6, 'y': 8, 'width': 68.75, 'height': 31.43}, 'tablet': {'y': py + 3}}),
        ]
        for k, (f, x, y, mx, my) in enumerate(cadute):
            livelli.append(K.livello('co-c%d%d' % (n, k), tipo='image', imageSrc=K.img(f), objectFit='cover', x=x, y=y,
                                     w=8.33, h=14.81, borderRadius=999, borderWidth=5, borderColor=C['chiaro'],
                                     boxShadow={'x': 0, 'y': 14, 'blur': 30, 'spread': -10, 'color': mix(C['nero'], 35)},
                                     timeline=K.tl(x, y, [(0, {'y': -25, 'rotation': -40, 'opacity': 0}, 'bounce.out'),
                                                          (500 + 220 * k, {'y': -25, 'rotation': -40, 'opacity': 0}, 'bounce.out'),
                                                          (1500 + 220 * k, {'rotation': 0, 'opacity': 100})],
                                                   1500 + 220 * k, loop=False),
                                     responsive={'mobile': {'x': mx, 'y': my, 'width': 14.06, 'height': 6.43},
                                                 'tablet': {'y': y + 3 if k < 2 else 85}}))
        livelli += [
            K.livello('co-o%d' % n, contenuto=occ, x=6, y=20, w=40, size=12, peso='500', colore=mix(C['acc'], 78, C['testo']), letterSpacing=2,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 41, 'x': 7, 'width': 86}}),
            K.livello('co-t%d' % n, contenuto=nome, tag='h1' if n == 1 else 'h2', x=6, y=24.5, w=46, size=104, peso='400',
                      colore=C['testo'], fontFamily=TITOLI, lineHeight=1.0, entra='fadeIn', ritardo=350, durata=1100,
                      charAnim={'stagger': 28, 'offsetY': 50, 'rotation': 8, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(41), 'y': 43.5, 'x': 7, 'width': 88}}),
            K.livello('co-i%d' % n, contenuto=sotto, tag='p', x=6, y=39, w=46, size=44, peso='400', colore=C['acc'],
                      fontFamily=TITOLI, lineHeight=1.1, entra='fadeInUp', ritardo=750, durata=900,
                      responsive={'mobile': {'fontSize': m(24), 'y': 48.6, 'x': 7, 'width': 88}}),
            K.livello('co-s%d' % n, contenuto=testo, x=6, y=50, w=33, size=17, peso='400', colore=C['tenue'],
                      lineHeight=1.65, ritardo=1000, responsive={'mobile': {'fontSize': m(14), 'y': 53, 'x': 7, 'width': 86}}),
            K.livello('co-b%d' % n, tipo='button', contenuto='Prenota un tavolo', x=6, y=72, w='auto', size=14, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, ritardo=1300,
                      buttonUrl=PRENOTA, hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 67, 'x': 7, 'fontSize': m(13)}}),
            K.livello('co-a%d' % n, tipo='button', contenuto='Da asporto', x=21, y=72, w='auto', size=14, peso='600',
                      colore=C['testo'], bgColor='transparent', padding=17, borderRadius=999, borderWidth=1,
                      borderColor=mix(C['testo'], 40), ritardo=1450, buttonUrl=PRENOTA + '#asporto',
                      hover={'bgColor': C['chiaro'], 'borderColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 67, 'x': 54, 'fontSize': m(13)}}),
            # il bollino del prezzo sul bordo della pizza
            K.livello('co-z%d' % n, contenuto=prezzo, x=55.56, y=38.3, w=7.64, h=13.58, size=30, peso='400', colore=C['testo'],
                      bgColor=C['acc2'], borderRadius=999, align='center', fontFamily=TITOLI, lineHeight=3.65,
                      entra='popUpBack', ritardo=1700, durata=800,
                      boxShadow={'x': 0, 'y': 10, 'blur': 24, 'spread': -8, 'color': mix(C['nero'], 30)},
                      responsive={'mobile': {'x': 14, 'y': 30.5, 'width': 17.2, 'height': 7.86, 'fontSize': 30},
                                  'tablet': {'y': 41.3}}),
        ]
        sfondo = K.sfondo_video([V_STESURA, V_CONDIRE, V_FORNO, V_TAGLIO][i], overlay=0.6, colore=C['fondo'])
        sl.append(K.diapositiva('co-%d' % n, sfondo, livelli, durata=7500, tab=tab))
    globali = [K.livello('co-forno', contenuto='Forno a legna · 450 °C · novanta secondi', x=6, y=86, w=40, size=11,
                         peso='500', colore=C['tenue'], letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                         entra='fadeIn', ritardo=1800, responsive={'mobile': {'y': 74, 'x': 7, 'width': 86, 'fontSize': m(10)}})]
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='pushRight',
                       durata_trans=1000, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=7500, progressBarColor=C['acc'], navColor=C['testo'],
                       globalBackground={'type': 'color', 'color': C['fondo']})


CARTE = [  # nome, ingredienti, prezzo, foto
    ('Margherita', 'San Marzano, fiordilatte di Agerola, basilico, olio di Sorrento.', '8 €', 'pexels-33593001.jpg'),
    ('Marinara', 'Pomodoro, aglio rosso, origano di montagna, olio. Senza formaggio.', '7 €', 'pexels-19260730.jpg'),
    ('Rucola', 'Fiordilatte, poi a crudo rucola, grana di trenta mesi, olio al limone.', '11 €', 'pexels-28509915.jpg'),
    ('Datterino', 'Datterini rossi scoppiati nel forno, fiordilatte, pecorino, basilico.', '10 €', 'pexels-17932142.jpg'),
    ('Bufala', 'Pomodoro, mozzarella di bufala aggiunta a crudo, basilico.', '12 €', 'pexels-35017347.jpg'),
    ('Ripieno', 'Il cornicione ripieno di ricotta di bufala e pepe nero.', '12 €', 'pexels-17518462.jpg'),
]


def carte(t):
    cols = []
    for nome, ingr, prezzo, foto in CARTE:
        cols.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 58}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': prezzo + ' · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': nome,
            'back_description': ingr + ' ' + prezzo, 'back_bg': C['acc'], 'back_text_color': C['on_acc'],
            'back_valign': 'center', 'back_cta_text': 'Prenota', 'back_cta_url': PRENOTA, 'flip_direction': 'horizontal',
            'flip_trigger': 'hover', 'card_height': '400', 'card_border_radius': '14', 'card_shadow': 'md', 'title_size': '30',
            'title_weight': '400', 'desc_size': '16'}, style={})], wm='1-3', ws='1-1'))
    return cols


def componi(t):
    def opz(i, g, nome, desc, prezzo, pred=False, badge=''):
        return {'id': i, 'gruppo': g, 'nome': nome, 'descrizione': desc, 'prezzo': prezzo, 'immagine': '', 'badge': badge,
                'predefinita': pred, 'punti': '', 'min': 0, 'max': 1}
    return t.classic('configuratore', {
        'gruppi': [{'id': 'base', 'titolo': 'La base', 'tipo': 'uno', 'obbligatorio': True,
                    'aiuto': 'Tutte con l’impasto di 48 ore. La integrale è con farina di tipo 1 e germe di grano.'},
                   {'id': 'formaggio', 'titolo': 'Il formaggio', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'sopra', 'titolo': 'Sopra', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': [opz('rossa', 'base', 'Rossa', 'San Marzano e basilico.', 6, True), opz('bianca', 'base', 'Bianca',
                    'Olio, sale di Cervia, rosmarino.', 6), opz('integrale', 'base', 'Rossa integrale', 'Tipo 1 e germe di grano.', 7),
                    opz('fiordilatte', 'formaggio', 'Fiordilatte di Agerola', 'Il nostro di ogni giorno.', 2, True),
                    opz('bufala', 'formaggio', 'Bufala a crudo', 'Messa dopo il forno.', 4, badge='La più chiesta'),
                    opz('senza', 'formaggio', 'Senza formaggio', 'Come la marinara.', 0),
                    opz('salame', 'sopra', 'Salame piccante', 'Di Calabria, a fette sottili.', 3),
                    opz('friarielli', 'sopra', 'Friarielli', 'Saltati in padella con aglio.', 3),
                    opz('nduja', 'sopra', '’Nduja', 'Un cucchiaio, si scioglie nel forno.', 3),
                    opz('acciughe', 'sopra', 'Acciughe di Cetara', 'Con capperi e olive.', 3),
                    opz('pomodorini', 'sopra', 'Pomodorini gialli', 'Del piennolo, confit.', 2)],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'La vostra pizza', 'nota_prezzo': 'Senza glutine: +3 €',
        'periodo': '', 'riepilogo': True, 'titolo_riepilogo': 'La vostra pizza', 'testo_azzera': 'Ricomincia',
        'testo_pulsante': 'Ordina da asporto', 'link_azione': PRENOTA + '?pizza={configurazione}#asporto',
        'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': C['chiaro'], 'colore_scelta': C['acc'],
        'colore_testo': C['testo'], 'colore_tenue': C['tenue'], 'colore_prezzo': C['acc'], 'sfondo_riepilogo': C['chiaro'],
        'sfondo_badge': C['acc2'], 'colore_badge': C['testo'], 'raggio_scheda': radius(14), 'gruppo_color': C['testo'],
        'gruppo_font_weight': '400', 'totale_color': C['acc'], 'totale_font_weight': '400'}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Cornicione · Bologna', ['Due giorni d’impasto,', 'novanta secondi di forno.'], size=60,
                                layout='stack', size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'La pizza la impastiamo due giorni prima, con tre farine macinate a pietra in Romagna. Riposa '
                                'quarantotto ore al fresco, si stende a mano, entra nel forno a legna a 450 gradi ed esce '
                                'dopo novanta secondi, col cornicione alto, morbido e maculato.', size=17, color=C['testo']),
                      K.button(t, 'L’impasto', IMPASTO, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('48 h', 'Di lievitazione'), ('450 °C', 'Il forno a legna'), ('90 s', 'In forno'),
                            ('3', 'Farine macinate a pietra')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([t.one([t.classic('marquee', {
        'content_type': 'text', 'text_items': 'San Marzano | Fiordilatte di Agerola | Basilico dell’orto | Olio di Sorrento | '
                                              'Pecorino romano | Bufala campana | Acciughe di Cetara',
        'separator': ' ● ', 'speed': '34', 'direction': 'left', 'pause_hover': True, 'gap': 34, 'velocity_skew': True,
        'bg_color': C['acc'], 'text_color': C['on_acc'], 'font_size': 40, 'font_weight': '400', 'letter_spacing': 0,
        'text_transform': 'none', 'font_family': 'heading', 'separator_color': C['acc2'], 'height': 84, 'full_width': True})],
        anim=None)], width='fullbleed', padding='remove-vertical'))
    cc = carte(t)
    out.append(t.section([
        t.one([K.sechead(t, 'Il menu della sera', ['Sei pizze', 'da girare.'], size=60,
                         tagline='Passate sopra una pizza, o toccatela: dietro ci sono gli ingredienti e il prezzo.')]),
        t.row(cc[:3], layout='33-33-33', gap=20),
        t.row(cc[3:], layout='33-33-33', gap=20, style={'margin_top': 20}),
        t.one([K.button(t, 'Tutto il menu', MENU, kind='pieno', pad_top=36)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_FORNO, 'Il forno a legna', 'Novanta secondi, <em>a 450 gradi.</em>',
                             'Legna di faggio e quercia, accesa alle cinque. La pizza si gira due volte con la pala: il '
                             'cornicione si gonfia e si macchia di nero, il centro resta morbido.', overlay=55))
    out.append(t.section([
        t.one([K.sechead(t, 'Componi la tua', ['Base, formaggio,', 'tutto il resto.'], size=60,
                         tagline='Sceglietela pezzo per pezzo: il prezzo si aggiorna e la ordinate da asporto.')]),
        t.one([componi(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='componi',
        style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Il cornicione è di quelli che ti ricordi: alto, leggero, con le bolle bruciate. Siamo tornati tre volte '
                 'in una settimana, la terza solo per la marinara.', 'author_name': 'Luca e Giada',
        'author_role': 'Dal Pratello, ottobre 2026', 'avatar': '', 'rating': '5', 'text_color': C['testo'],
        'star_color': C['acc2'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc']}, style={})])], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_STESURA, 'La stesura', 'Con le mani, <em>mai col mattarello.</em>',
                          'Si allarga dal centro verso fuori: l’aria scappa nel bordo e lì resta, a fare il cornicione. '
                          'Il mattarello la schiaccerebbe tutta.', 'L’impasto', IMPASTO, overlay=55, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Cornicione',
                page_intro_caption='Pizzeria · Bologna', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def listino(t, voci, prefisso):
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f) if f else '', 'highlighted': bool(h),
                   'badge': h} for a, b, c, f, h in voci],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 30), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': C['acc2'], 'badge_color': C['testo'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})


def build_menu():
    t = Tpl(ID, 'il-menu')
    out = [K.intro(t, 'Il menu', ['Rosse, bianche,', 'fritte e dolci.'],
                   'Le pizze sono quattordici, cambiano con le stagioni del mercato. Tutte si fanno anche senza glutine, '
                   'cotte su una pietra a parte.', bg=K.photo_bg('pexels-19260835.jpg', overlay=82, color=C['fondo']))]
    rosse = listino(t, [('Margherita', 'San Marzano, fiordilatte, basilico, olio', '8 €', 'pexels-33593001.jpg', ''),
                        ('Marinara', 'Pomodoro, aglio rosso, origano, olio', '7 €', 'pexels-19260730.jpg', ''),
                        ('Datterino', 'Datterini rossi, fiordilatte, pecorino, basilico', '10 €', 'pexels-17932142.jpg', ''),
                        ('Cosacca', 'Pomodoro, pecorino romano, basilico: la più antica', '8 €', '', ''),
                        ('Bufala', 'Pomodoro, bufala a crudo, basilico', '12 €', 'pexels-35017347.jpg', 'La più chiesta'),
                        ('Diavola', 'Pomodoro, fiordilatte, salame piccante di Calabria', '10 €', 'pexels-365459.jpg', '')], 'r')
    bianche = listino(t, [('Rucola', 'Fiordilatte, rucola, grana di 30 mesi', '11 €', 'pexels-28509915.jpg', ''),
                          ('Friarielli', 'Fiordilatte, friarielli, salsiccia di Bra', '12 €', 'pexels-34667359.jpg', ''),
                          ('Ripieno', 'Cornicione ripieno di ricotta di bufala e pepe', '12 €', 'pexels-17518462.jpg', 'Nuova'),
                          ('Acciughe', 'Fiordilatte, acciughe di Cetara, capperi, olive', '11 €', 'pexels-905847.jpg', '')], 'b')
    out.append(t.section([
        t.row([t.col([K.label(t, 'Le rosse', color=C['acc']), rosse], wm='1-2', ws='1-1'),
               t.col([K.label(t, 'Le bianche', color=C['acc']), bianche], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 90}))
    fritti = listino(t, [('Arancini', 'Al ragù o al burro, due pezzi', '6 €', 'pexels-5987500.jpg', ''),
                         ('Montanarine', 'Pizzette fritte col pomodoro e il grana', '5 €', 'pexels-39886102.jpg', ''),
                         ('Caprese', 'Pomodoro cuore di bue e bufala', '9 €', 'pexels-32570609.jpg', '')], 'f')
    dolci = listino(t, [('Tiramisù', 'Col caffè della moka, al cucchiaio', '6 €', 'pexels-29460805.jpg', ''),
                        ('Tiramisù al pistacchio', 'Una volta a settimana, finché c’è', '7 €', 'pexels-26838689.jpg', ''),
                        ('Pizza alla Nutella', 'Da dividere in quattro, con le fragole', '9 €', '', '')], 'd')
    out.append(t.section([
        t.row([t.col([K.label(t, 'I fritti e gli antipasti', color=C['acc']), fritti], wm='1-2', ws='1-1'),
               t.col([K.label(t, 'I dolci', color=C['acc']), dolci], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([t.classic('scrubtext', {
            'text': 'Una pizza buona si riconosce dal bordo: <em>se lo lasci, non era buona.</em>',
            'show_lead': True, 'lead': 'Al Pratello nessuno lascia il cornicione: è la parte che impastiamo con più cura.',
            'scroll_reveal': True, 'dim_opacity': 18, 'accent': C['acc'], 'text_color': C['testo'], 'lead_color': C['tenue'],
            'size_min': 32, 'size_max': 64, 'max_width_ch': 22, 'lead_size': 18, 'text_font_weight': '400',
            'text_align': 'center'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'di ordinare.', [
        ('Fate la pizza senza glutine?', 'Sì, tutte e quattordici, con un impasto a parte cotto su una pietra dedicata. '
                                         'Costa 3 € in più.'),
        ('E vegana?', 'La marinara lo è già; le altre le facciamo con una crema di anacardi al posto del fiordilatte.'),
        ('Si può dividere?', 'Certo: la tagliamo in otto, anche a metà fra due pizze diverse.'),
        ('Avete il menu per bambini?', 'La margherita piccola a 5 €, e un pezzo di impasto da stendere al tavolo.'),
    ], 'mn', bg=True))
    return out


def build_impasto():
    t = Tpl(ID, 'l-impasto')
    out = [K.intro(t, 'L’impasto', ['Quarantotto ore', 'prima della pizza.'],
                   'Acqua, farina, sale, pochissimo lievito e il tempo. Il resto lo fa il forno, in novanta secondi.',
                   bg=K.photo_bg('pexels-38455659.jpg', overlay=80, color=C['fondo']))]
    giorni = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('01', 'Lunedì sera', 'pexels-5947611.jpg', 'L’impasto', 'Tre farine', 'e acqua fredda', 'Tipo 0, tipo 1 e '
             'semola, macinate a pietra a Faenza. Pochissimo lievito: due grammi per chilo.', '2 g', 'di lievito al kg'),
            ('02', 'Martedì', 'pexels-36912598.jpg', 'I panetti', 'Si staglia', 'a mano', 'Panetti da 260 grammi, uno per '
             'pizza, a riposare nelle cassette di legno al fresco.', '260 g', 'a panetto'),
            ('03', 'Mercoledì sera', 'pexels-13736876.jpg', 'Il forno', 'Si stende', 'e si cuoce', 'Dopo quarantotto ore '
             'l’impasto è pronto: si stende a mano, si condisce, novanta secondi di fuoco.', '48 h', 'di riposo')]],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': C['acc'],
        'counter_font_family': 'heading', 'counter_size': 88, 'counter_color': C['acc'], 'counter_italic': False,
        'counter_weight': '400', 'tag_color': C['testo'], 'pre_title_color': C['acc'], 'title_font_family': 'heading',
        'title_size': 34, 'title_weight': '400', 'title_color': C['testo'], 'title_accent_color': C['acc'],
        'description_color': C['tenue'], 'footer_icon': 'timer', 'footer_value_color': C['testo'],
        'footer_label_color': C['tenue'], 'columns': 3, 'gap': 48, 'media_radius': radius(14)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Tre giorni', ['Dal sacco di farina', 'alla pala.'], size=56)]),
        t.one([giorni]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_CONDIRE, 'Il condimento', 'Il pomodoro <em>a crudo.</em>',
                             'Il San Marzano non si cuoce prima: si schiaccia a mano con un pizzico di sale e va sulla pizza '
                             'così. Cuoce solo nel forno, per novanta secondi.', overlay=55))
    out.append(t.section([
        t.one([K.sechead(t, 'Al forno', ['Chi stende', 'e chi inforna.'], size=56)]),
        K.squadra(t, [
            ('pexels-5056618.jpg', 'Gennaro Esposito', 'Pizzaiolo', 'Da Napoli al Pratello: vent’anni di forno, la pala in '
             'mano dalle sette di sera.', '50% 30%'),
            ('pexels-8290125.jpg', 'Marta Ricci', 'Impasti e lievito madre', 'Fa gli impasti il lunedì e il giovedì. Ha '
             'cercato per due anni le tre farine giuste.', '50% 30%'),
            ('pexels-36430056.jpg', 'Samir Haddad', 'Forno e fritti', 'Accende il forno alle cinque e lo tiene a 450 gradi: '
             'sa dove mettere ogni ciocco.', '50% 30%')], sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_TAGLIO, 'Al tavolo', 'Si taglia <em>in otto.</em>',
                          'La pizza arriva intera, con la rotella: tagliatela voi, così il centro non si raffredda.',
                          'Prenota', PRENOTA, overlay=55, video=True))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Un tavolo', 'o una pizza da portare.'],
                   'Al tavolo si prenota fino alle 21:30; da asporto si ordina qui e si ritira all’ora che scegliete.',
                   bg=K.photo_bg('pexels-32293375.jpg', overlay=80, color=C['fondo']), size=92)]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Tutte le sere', 'time': '19 — 23:30', 'note': 'L’ultimo tavolo alle 22:15'},
                  {'day': 'Sabato e domenica', 'time': '12:30 — 14:30', 'note': 'Anche a pranzo, col forno acceso'},
                  {'day': 'Martedì', 'time': 'Chiuso', 'note': 'Si impasta per il resto della settimana'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': C['acc'], 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 44,
        'time_weight': '400', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    out.append(t.section([t.one([orari])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 60}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('select', 'Al tavolo o da asporto', 'come', True, opzioni='Al tavolo\nDa asporto'),
             K.campo('date', 'Giorno', 'giorno', True), K.campo('select', 'Ora', 'ora', True,
                                                               opzioni='19:00\n19:30\n20:00\n20:30\n21:00\n21:30\n22:00'),
             K.campo('select', 'Quanti siete', 'persone', opzioni='1\n2\n3\n4\n5\n6\n7 o più'),
             K.campo('text', 'La vostra pizza', 'pizza', param='pizza', larghezza='1-1',
                     segnaposto='Se l’avete composta, la trovate già qui'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1',
                     segnaposto='Senza glutine, un seggiolone, un compleanno…')]
    out.append(K.sezione_modulo(t, 'asporto', 'Prenota o ordina', ['Al tavolo', 'o da portare via.'],
                                'Vi confermiamo con un messaggio. Da asporto la pizza è pronta all’ora scelta, nella scatola '
                                'forata che non la fa sudare.', VOCI, campi, 'Cornicione — prenotazione',
                                'Manda', 'Ricevuto! Vi confermiamo con un messaggio entro mezz’ora.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Al Pratello,', 'dove fa angolo.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via del Pratello 48, Bologna'), ('Bus', '13 e 19, fermata Pratello'),
                                  ('In bici', 'rastrelliera davanti al forno'), ('Posti', '64 dentro, 20 sotto il portico')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Cornicione · via del Pratello 48', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · tavolo non trovato', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è finita nel forno.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Novanta secondi di troppo. Torniamo al menu.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla pizza', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_FORNO, overlay=66), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'La pizza che gira',
    ['pizzeria', 'pizza', 'forno a legna', 'bologna', 'asporto', 'chiaro', 'slider pro'],
    'Una pizzeria contemporanea, farina, pomodoro e basilico: in apertura la pizza che gira nella finestra rotonda e gli '
    'ingredienti che cadono e rimbalzano a ogni pizza, i nomi a linguette; il menu su carte da girare, il configuratore '
    'per comporre la propria pizza col prezzo, i tre giorni dell’impasto e la prenotazione al tavolo o da asporto.',
    [('Il menu', MENU), ('L’impasto', IMPASTO), ('Componi', HOME + '#componi'), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('il-menu', 'Il menu', 'il-menu'), ('l-impasto', 'L’impasto', 'l-impasto'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Familjen Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Hedvig Letters Serif\', \'Georgia\', serif', '\'Victor Mono\', ui-monospace, Menlo, monospace',
    ['Hedvig Letters Serif', 'Familjen Grotesk', 'Victor Mono'], peso_titoli='400', raggio=14, grana=4,
    titoli_line='1.02', titoli_spazio='-0.015em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '3'  # apertura scura sul video pieno, 31 ottobre 2026


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-menu.json': build_menu(), 'l-impasto.json': build_impasto(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
