# -*- coding: utf-8 -*-
"""Genera il tema «Pista Nera» (id pista-nera): una scuola di sci al Passo del Tonale: corsi collettivi e privati, i
bambini dai quattro anni, il fuoripista con la guida e le piste del comprensorio dalla blu alla nera.

Tema chiaro, bianco neve e azzurro ghiaccio, coi colori delle piste: blu, rosso, nero e l'ambra del fuoripista. Titoli in
Sora, testo in Plus Jakarta Sans, dati in Roboto Mono.

Apertura: Slider Pro «la pendenza», alta 16:9, un livello per diapositiva col suo video pieno (la pista dolce con la
seggiovia, le curve fra gli abeti, la discesa ripida, la neve fresca) e un velo scuro solo dal lato del testo. A destra
il cartello della pista col suo colore e la pendenza disegnata: la linea della pista si inclina a ogni livello, dalla
blu alla nera, e lo sciatore la scende; accanto la pendenza in percentuale. Cursore: la neve.
"""
import os
import sys
import math

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'pista-nera'
NAME = 'Pista Nera'

HEX = {
    'primary': '#1F5FBF', 'primary_contrast': '#FFFFFF', 'secondary': '#E8A100', 'secondary_contrast': '#0B0F14',
    'accent': '#D7263D', 'dark': '#0B0F14', 'light': '#FFFFFF', 'muted': '#E6EDF3', 'muted_contrast': '#0D1B2A',
    'text': '#0D1B2A', 'text_muted': '#4B5B6B', 'background': '#F4F7FA', 'border': '#D0DBE5', 'link': '#1F5FBF',
}

HOME, CORSI, MAESTRI, PISTE, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:corsi}}', '{{PAGINA:maestri}}', '{{PAGINA:le-piste}}',
                                         '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'scuola@pistanera.example', '+39 0364 000 911', 'tel:+390364000911'
LAT, LNG = '46.2611', '10.5845'

V_DOLCE, V_ABETI, V_RIPIDA, V_POLVERE, V_SEGGIOVIA, V_VETTE = ('pexels-video-33065444', 'pexels-video-20179609',
                                                               'pexels-video-35547882', 'pexels-video-854877',
                                                               'pexels-video-20659486', 'pexels-video-28638515')
PHOTOS = [
    'pexels-36638158.jpg', 'pexels-15318901.jpg', 'pexels-36583078.jpg', 'pexels-15577708.jpg', 'pexels-3800412.jpg',
    'pexels-8627419.jpg', 'pexels-10828626.jpg', 'pexels-35497201.jpg', 'pexels-11938974.jpg', 'pexels-1054201.jpg',
    'pexels-20256853.jpg', 'pexels-34804813.jpg', 'pexels-36206791.jpg', 'pexels-36873465.jpg', 'pexels-16706208.jpg',
    'pexels-15919805.jpg', 'pexels-36259159.jpg', 'pexels-15324806.jpg', 'pexels-30786149.jpg',
]
VIDEOS = [V_DOLCE, V_ABETI, V_RIPIDA, V_POLVERE, V_SEGGIOVIA, V_VETTE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-light)')
C = K.c
BLU, AMBRA, ROSSO = C['acc'], C['sec'], C['acc2']
MONO = 'Roboto Mono'
VOCI = [('map-pin', 'Piazzale delle Funivie 2 · Passo del Tonale', CONTATTI + '#scuola'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Pista Nera', logo_size='22', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Pista Nera', 'Scuola di sci al Passo del Tonale dal 1979. Corsi collettivi e privati, i bambini dai '
                   'quattro anni, il fuoripista con la guida. Ventotto maestri, tutti del posto.',
                   [('I corsi', CORSI), ('I maestri', MAESTRI), ('Le piste', PISTE), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'La scuola', 'Da dicembre ad aprile, tutti i giorni 8:30 — 16:30<br>Il ritrovo alla partenza della '
                   'cabinovia', VOCI, '© 2026 Scuola di sci Pista Nera · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=140,
                   fondo=C['sala'])


# ── L'apertura: la pendenza ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
LIVELLI = [  # linguetta, occhiello, riga 1, riga 2, testo, video, colore, cartello, pendenza, angolo disegnato
    ('Principianti', 'Primo livello · pista blu', 'Il primo giorno,', 'la pista blu.',
     'Tre mattine col maestro sul campo scuola, poi la prima pista vera: larga, dolce, con la seggiovia accanto.',
     V_DOLCE, BLU, 'Pista blu', 18, 14),
    ('Intermedi', 'Secondo livello · pista rossa', 'Le curve strette,', 'la pista rossa.',
     'Si lavora sul ritmo e sulle curve corte, fra gli abeti: qui si comincia a sciare davvero.',
     V_ABETI, ROSSO, 'Pista rossa', 32, 26),
    ('Esperti', 'Terzo livello · pista nera', 'La pendenza vera,', 'la pista nera.',
     'Il Paradiso e la Bleis: le nere del Tonale, col maestro davanti che vi fa vedere dove mettere gli sci.',
     V_RIPIDA, C['nero'], 'Pista nera', 48, 37),
    ('Fuoripista', 'Fuori dalle piste · con la guida', 'La neve fresca,', 'con chi la conosce.',
     'ARTVA, pala e sonda, e una guida alpina che sa dove la neve tiene: il Presena in giornate come questa.',
     V_POLVERE, AMBRA, 'Fuoripista', 60, 45),
]
def apertura(t):
    """Il cubo dei livelli: un cubo verticale che gira con lo scorrimento (lo slider resta fermo in cima), una faccia per
    livello, dalla pista blu alla nera e al fuoripista. Su ogni faccia il cartello della pista grande al centro, col suo
    colore e il numero, poi il titolo e i pulsanti; il video si vede pieno in alto, il velo scuro sale solo dal basso."""
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, video, colore, cartello, perc, ang) in enumerate(LIVELLI):
        n = i + 1
        livelli = [
            K.livello('pn-velo%d' % n, tipo='shape', x=0, y=28, w=100, h=72, entra='fadeIn', ritardo=0, durata=500,
                      esce='fadeOut', shapeGradient={'angle': 0, 'from': mix(C['nero'], 90), 'to': mix(C['nero'], 0),
                                                     'fromStop': 50},
                      responsive={'mobile': {'y': 34, 'height': 66}}),
            K.livello('pn-disco%d' % n, tipo='shape', x=45, y=10, w=10, h=17.78, borderRadius=999, bgColor=colore,
                      borderWidth=8, borderColor=C['chiaro'], entra='zoomIn', ritardo=150, durata=700, esce='fadeOut',
                      boxShadow={'x': 0, 'y': 18, 'blur': 40, 'spread': -10, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'x': 35, 'y': 10, 'width': 30, 'height': 13.71}}),
            K.livello('pn-num%d' % n, contenuto='%d' % n, x=45, y=10, w=10, h=17.78, size=56, peso='800',
                      colore=C['nero'] if colore == AMBRA else C['chiaro'], align='center', lineHeight=1, textValign='center',
                      entra='zoomIn', ritardo=350, durata=600, esce='fadeOut',
                      responsive={'mobile': {'x': 35, 'y': 10, 'width': 30, 'height': 13.71, 'fontSize': m(40)}}),
            K.livello('pn-nome%d' % n, contenuto=cartello + ' · pendenza fino al %d%%' % perc, x=35, y=30, w=30, size=12,
                      peso='700', colore=C['chiaro'], bgColor=mix(C['nero'], 62), padding=8, borderRadius=6, align='center',
                      letterSpacing=2, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=500,
                      responsive={'mobile': {'x': 10, 'y': 25.5, 'width': 80, 'fontSize': m(10)}}),
            K.livello('pn-o%d' % n, contenuto=occ, x=20, y=43, w=60, size=12, peso='600', colore=mix(C['chiaro'], 86),
                      letterSpacing=3, textTransform='uppercase', fontFamily=MONO, align='center', ritardo=200,
                      responsive={'mobile': {'fontSize': m(11), 'y': 50, 'x': 6, 'width': 88}}),
            K.livello('pn-t%d' % n, contenuto=r1 + '<br><span style="color:var(--olo-color-secondary)">' + r2 + '</span>',
                      tag='h1' if n == 1 else 'h2', x=10, y=47, w=80, size=72, peso='700', colore=C['chiaro'],
                      lineHeight=1.04, letterSpacing=-2, align='center', entra='maskFromBottom', ritardo=300, durata=1100,
                      responsive={'mobile': {'fontSize': m(33), 'y': 53.5, 'x': 5, 'width': 90}}),
            K.livello('pn-s%d' % n, contenuto=testo, x=29, y=66, w=42, size=16, peso='400', colore=mix(C['chiaro'], 88),
                      lineHeight=1.6, align='center', ritardo=800,
                      responsive={'mobile': {'fontSize': m(14), 'y': 65, 'x': 7, 'width': 86}}),
            K.livello('pn-b%d' % n, tipo='button', contenuto='Prenota la lezione', x=37, y=77, w='auto', size=14, peso='700',
                      colore=C['chiaro'], bgColor=BLU, padding=16, borderRadius=10, ritardo=1100, buttonUrl=CORSI,
                      hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                      responsive={'tablet': {'x': 30}, 'mobile': {'y': 78, 'x': 7, 'fontSize': m(12)}}),
            K.livello('pn-l%d' % n, tipo='button', contenuto='Le piste', x=53.5, y=77, w='auto', size=14, peso='700',
                      colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=10, borderWidth=1,
                      borderColor=mix(C['chiaro'], 55), ritardo=1250, buttonUrl=PISTE,
                      hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                      responsive={'tablet': {'x': 52}, 'mobile': {'y': 78, 'x': 56, 'fontSize': m(12)}}),
        ]
        sl.append(K.diapositiva('pn-%d' % n, K.sfondo_video(video, overlay=0.16, colore=C['nero']), livelli, durata=8000,
                                tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'vh', 'value': 100}, transizione='cubeV', durata_trans=900,
                       heightNotebook={'mode': 'vh', 'value': 100}, heightTablet={'mode': 'ratio', 'value': '16:9'},
                       heightMobile={'mode': 'vh', 'value': 100}, autoplay=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, scrollScrub=True, showArrows=False, showDots=True, dotStyle='dash',
                       showTabs=False, showProgressBar=False, parallax=False, swipe=False, navColor=C['chiaro'],
                       globalBackground={'type': 'color', 'color': C['nero']})


PISTE_LISTA = [  # numero, nome, difficoltà e lunghezza, quota
    ('3', 'Valbiolo', 'Blu · 1,8 km · larga e dolce', '1.880 m'),
    ('7', 'Alpino', 'Blu · 2,4 km · col campo scuola', '1.900 m'),
    ('12', 'Bleis', 'Rossa · 2,1 km · fra gli abeti', '2.120 m'),
    ('15', 'Contrabbandiere', 'Rossa · 3,2 km · dal Presena', '2.585 m'),
    ('21', 'Paradiso', 'Nera · 1,6 km · la più ripida', '2.580 m'),
    ('24', 'Presena', 'Nera · 1,2 km · sul ghiacciaio', '3.000 m'),
]


def piste(t, voci=PISTE_LISTA):
    return t.classic('worklist', {
        'items': [{'number': nu, 'title': ti, 'category': ca, 'year': q, 'link_url': PISTE} for nu, ti, ca, q in voci],
        'divider_color': C['filo'], 'row_padding_y': 24, 'row_hover_bg': mix(BLU, 6), 'hover_indent': 28,
        'number_color': BLU, 'number_size': 16, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 40, 'title_weight': '700', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': BLU, 'year_size': 18, 'show_arrow': True, 'arrow_color': BLU,
        'mono_font_family': MONO}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': BLU, 'author_color': C['tenue'], 'quote_accent_color': BLU, 'quote_font': 'heading',
        'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': BLU}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Pista Nera · Passo del Tonale, dal 1979', ['Dal campo scuola', 'al Presena.'], size=60,
                                layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Ventotto maestri, tutti nati qui sotto. Insegniamo a chi non ha mai messo gli sci e a chi '
                                'vuole scendere la Paradiso senza pensarci. Il ritrovo è alla partenza della cabinovia, alle '
                                'nove: gli sci a noleggio li trovate già pronti.', size=17, color=C['testo']),
                      K.button(t, 'I maestri', MAESTRI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('28', 'Maestri'), ('100 km', 'Di piste'), ('3.000 m', 'Il ghiacciaio'),
                            ('4 anni', 'L’età per cominciare')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le piste', ['Dalla blu', 'alla nera.'], size=58,
                         tagline='Passate su una pista: la freccia porta alla mappa. Il numero è quello del cartello.')]),
        t.one([piste(t, PISTE_LISTA[:5])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SEGGIOVIA, 'La seggiovia', 'Il tempo <em>per guardare.</em>',
                             'Otto minuti fino in cima: il maestro vi spiega la prossima discesa, voi guardate le montagne.',
                             overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-35497201.jpg', 'Una maestra in tuta arancione coi bambini sulla pista', ratio='4/5',
                              tilt=True)], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'I bambini', ['Dai quattro anni,', 'giocando.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Il campo scuola col tappeto, gli archi colorati, i pupazzi da aggirare: la prima settimana si '
                                'gioca e basta. La seconda si prende la seggiovia con la maestra.', size=17),
                      K.specs(t, [('Età', 'dai 4 ai 12 anni'), ('Gruppi', 'sei bambini per maestro'),
                                  ('Merenda', 'alle 11, al rifugio')]),
                      K.button(t, 'I corsi per i bambini', CORSI, kind='pieno', pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Mia figlia aveva paura della seggiovia. Il terzo giorno Marta le ha fatto fare la fila davanti a tutti. Il sesto '
           'ci ha superati sulla rossa.', 'Federica P.', 'Corso bambini, febbraio 2026')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_VETTE, 'Fuoripista', 'La neve fresca, <em>con la guida.</em>',
                          'Mezze giornate sul Presena con una guida alpina: ARTVA, pala e sonda li diamo noi.',
                          'Il fuoripista', CORSI + '#fuoripista', overlay=40, video=True))
    return out


def build_corsi():
    t = Tpl(ID, 'corsi')
    out = [K.intro(t, 'I corsi', ['Collettivi, privati,', 'per i bambini.'],
                   'Il ritrovo è alle nove alla partenza della cabinovia. Gli sci e gli scarponi a noleggio sono già pronti '
                   'col vostro nome.', bg=K.video_bg(V_ABETI), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Collettivo, privato,', 'per i bambini.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Collettivo', '210', 'sei mattine', 'Gruppi di sei per livello\nDue ore e mezza al giorno\nLa gara del sabato con la '
             'medaglia', False, ''),
            ('Privato', '65', 'l’ora', 'Il maestro solo per voi\nFino a tre persone dello stesso livello\nAnche il pomeriggio',
             True, 'il più scelto'),
            ('Bambini', '240', 'sei mattine', 'Dai quattro ai dodici anni\nSei bambini per maestro\nLa merenda al rifugio',
             False, ''),
        ], 'Prenota il corso', CONTATTI + '#scrivici', nota='Il noleggio degli sci si aggiunge: 18 € al giorno, casco compreso.',
            sfondo=C['sala']),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-15318901.jpg', 'Uno sciatore nella neve fresca', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Il fuoripista', ['Mezza giornata,', 'con la guida alpina.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Sul Presena e nei canali del Tonale, solo con il bollettino buono. Prima un’ora di ricerca con '
                                'l’ARTVA, poi la neve fresca: la guida sceglie dove, voi pensate a sciare.', size=17),
                      K.specs(t, [('Prezzo', '120 € a persona, mezza giornata'), ('Gruppi', 'quattro persone al massimo'),
                                  ('Attrezzatura', 'ARTVA, pala e sonda comprese')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', html_id='fuoripista', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'di arrivare.', [
        ('Non ho mai sciato: da dove comincio?', 'Dal collettivo principianti: tre mattine sul campo scuola, poi la pista blu.'),
        ('Il casco è obbligatorio?', 'Per i bambini sì, per gli adulti lo consigliamo: è compreso nel noleggio.'),
        ('E se c’è brutto tempo?', 'Si scia lo stesso; se gli impianti chiudono, la lezione si recupera.'),
    ], 'pn-faq'))
    return out


def build_maestri():
    t = Tpl(ID, 'maestri')
    out = [K.intro(t, 'I maestri', ['Ventotto maestri,', 'tutti del posto.'],
                   'Sono nati in valle e hanno imparato a sciare prima che a camminare. Parlano italiano, inglese, tedesco e '
                   'polacco.', bg=K.photo_bg('pexels-1054201.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Alcuni di loro', ['Chi vi aspetta', 'alla cabinovia.'], size=56)]),
        K.squadra(t, [
            ('pexels-36259159.jpg', 'Luca Bertolini', 'Direttore della scuola', 'Maestro dal 1998, ex discesista in Coppa '
             'Europa. Segue gli esperti e il fuoripista.', 'center 25%'),
            ('pexels-15919805.jpg', 'Sara Pedrotti', 'Maestra e allenatrice', 'Allena i ragazzi dello sci club. Sulla Paradiso '
             'fa sembrare tutto facile.', 'center 30%'),
            ('pexels-15324806.jpg', 'Giorgio Mosconi', 'Maestro dei principianti', 'Trent’anni di primi giorni: nessuno '
             'ha più pazienza di lui.', 'center 30%'),
        ], ratio='4/5', size='300', sfondo=None),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Luca mi ha guardato scendere una volta e mi ha detto: troppo indietro. Dopo una settimana scendevo la Paradiso. '
           'Non so come, ma aveva ragione.', 'Tomasz K.', 'Lezioni private, gennaio 2026')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_POLVERE, 'Il Presena', 'Tremila metri, <em>neve fino a maggio.</em>',
                             'Quando giù comincia a mancare, si sale sul ghiacciaio: la stagione qui finisce dopo.', overlay=40))
    return out


def build_piste():
    t = Tpl(ID, 'le-piste')
    out = [K.intro(t, 'Le piste', ['Cento chilometri,', 'dai 1.880 ai 3.000 metri.'],
                   'Il Tonale, il Presena e la discesa verso Ponte di Legno: blu larghe per cominciare, rosse fra gli abeti, '
                   'nere sul ghiacciaio.', bg=K.photo_bg('pexels-20256853.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le nostre preferite', ['Sei piste,', 'una per livello e mezzo.'], size=56,
                         tagline='Il numero è quello del cartello sulla pista; a destra la quota della partenza.')]),
        t.one([piste(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In quota', ['Le montagne', 'attorno.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-11938974.jpg', 'Una cresta innevata fra le nuvole', 'Il Presena', '3.069 m'),
                               ('pexels-34804813.jpg', 'Una vetta al sole del tramonto', 'Il tramonto', 'dalla Bleis'),
                               ('pexels-36206791.jpg', 'Una baita bianca fra gli abeti innevati', 'La baita', 'del Valbiolo'),
                               ('pexels-36873465.jpg', 'Un rifugio rosso illuminato di notte', 'Il rifugio', 'la sera'),
                               ('pexels-16706208.jpg', 'Baite di pietra coperte di neve', 'Le malghe', 'sotto il Castellaccio'),
                               ('pexels-30786149.jpg', 'Una seggiovia arancione nel cielo blu', 'La seggiovia', 'Valbiolo')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Scriveteci,', 'vi teniamo il maestro.'],
                   'Per un corso collettivo, le lezioni private, i bambini o il fuoripista.',
                   bg=K.photo_bg('pexels-36583078.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Il corso', 'corso', True, opzioni='Collettivo\nPrivato\nBambini\nFuoripista con la guida'),
             K.campo('select', 'Il livello', 'livello', True, opzioni='Mai sciato\nPista blu\nPista rossa\nPista nera'),
             K.campo('date', 'Dal giorno', 'dal'), K.campo('date', 'Al giorno', 'al'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1', segnaposto='Quanti siete, le età dei bambini, '
                                                                                         'se serve il noleggio.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Le date e il livello,', 'al resto pensiamo noi.'],
                                'Rispondiamo in giornata. In alta stagione i privati finiscono presto.', VOCI, campi,
                                'Pista Nera — richiesta', 'Invia', 'Ricevuto. Vi rispondiamo in giornata.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La scuola', ['Alla cabinovia,', 'in piazzale.'], size=52, layout='stack', size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Piazzale delle Funivie 2, Passo del Tonale'),
                                  ('Orari', 'dicembre — aprile, 8:30 — 16:30'), ('Parcheggio', 'gratuito, al piazzale')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Pista Nera · la scuola', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='scuola', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pista chiusa', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pista <em>oggi è chiusa.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Si torna alla cabinovia e si riparte.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_VETTE, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'Il cubo dei livelli',
    ['sci', 'scuola di sci', 'maestri', 'montagna', 'neve', 'bambini', 'chiaro', 'slider pro'],
    'Una scuola di sci, bianco neve coi colori delle piste: in apertura un cubo che gira con lo scorrimento, una '
    'faccia per livello, dalla pista blu alla nera e al fuoripista, col cartello della pista al centro; i corsi coi '
    'prezzi, i maestri, le piste col loro numero e i bambini.',
    [('I corsi', CORSI), ('I maestri', MAESTRI), ('Le piste', PISTE), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('corsi', 'I corsi', 'corsi'), ('maestri', 'I maestri', 'maestri'),
     ('le-piste', 'Le piste', 'le-piste'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Plus Jakarta Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Sora\', system-ui, sans-serif',
    '\'Roboto Mono\', ui-monospace, Menlo, monospace', ['Sora', 'Plus Jakarta Sans', 'Roboto Mono'], corsivo_di=[],
    peso_titoli='700', raggio=10, grana=0, titoli_line='1.04', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'neve'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'corsi.json': build_corsi(), 'maestri.json': build_maestri(), 'le-piste.json': build_piste(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
