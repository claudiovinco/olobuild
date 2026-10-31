# -*- coding: utf-8 -*-
"""Genera il tema «Bolina» (id bolina): una scuola di vela all'Argentario: corsi su derive e cabinati, perfezionamento,
crociere di una settimana, uscite al tramonto e noleggio.

Tema chiaro, bianco vela e blu mare, con l'arancio delle cerate e il verde acqua. Titoli in Outfit, testo in DM Sans, dati
in JetBrains Mono.

Apertura: Slider Pro «la bolina», alta 16:9, una diapositiva per corso col suo video pieno (la barca a vela, la prua che
sbanda, il drone sul mare scuro, le vele al tramonto) e un velo scuro solo dal lato del testo. A destra la rotta di
bolina: dall'alto scendono le strisce del vento, la rotta a zig-zag si disegna lato dopo lato e la barca la risale
virata dopo virata fino alla boa; in alto il vento del giorno, che cambia a ogni diapositiva. Le diapositive si
impilano (stack). Cursore: le onde al clic.
"""
import os
import sys
import math

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'bolina'
NAME = 'Bolina'

HEX = {
    'primary': '#0B5FA5', 'primary_contrast': '#FFFFFF', 'secondary': '#0FA3A3', 'secondary_contrast': '#06222F',
    'accent': '#F25C3B', 'dark': '#06192A', 'light': '#FFFFFF', 'muted': '#E4EDF2', 'muted_contrast': '#0E2233',
    'text': '#0E2233', 'text_muted': '#4D6273', 'background': '#F2F6F8', 'border': '#D3E0E8', 'link': '#0B5FA5',
}

HOME, CORSI, NOLEGGIO, SCUOLA, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:corsi}}', '{{PAGINA:noleggio}}',
                                           '{{PAGINA:la-scuola}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'vela@bolina.example', '+39 0564 000 818', 'tel:+390564000818'
LAT, LNG = '42.4378', '11.1199'

V_VELA, V_SBANDA, V_DRONE, V_TRAMONTO, V_WINCH = ('pexels-video-12084811', 'pexels-video-8906211', 'pexels-video-15360330',
                                                  'pexels-video-30593077', 'pexels-video-8515865')
PHOTOS = [
    'pexels-38926131.jpg', 'pexels-12157648.jpg', 'pexels-2574037.jpg', 'pexels-8918062.jpg', 'pexels-40229498.jpg',
    'pexels-11195818.jpg', 'pexels-16829946.jpg', 'pexels-30903100.jpg', 'pexels-13122991.jpg', 'pexels-16313846.jpg',
    'pexels-5416402.jpg', 'pexels-5416486.jpg', 'pexels-5370838.jpg', 'pexels-4253990.jpg', 'pexels-275637.jpg',
]
VIDEOS = [V_VELA, V_SBANDA, V_DRONE, V_TRAMONTO, V_WINCH]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=12, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
CERATA, ACQUA = C['acc2'], C['sec']
MONO = 'JetBrains Mono'
VOCI = [('map-pin', 'Lungomare dei Navigatori 12 · Porto Santo Stefano', CONTATTI + '#base'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Bolina', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Bolina', 'Scuola di vela all’Argentario dal 1998. Corsi su derive e cabinati, perfezionamento, '
                   'crociere di una settimana fra le isole, uscite al tramonto e noleggio.',
                   [('I corsi', CORSI), ('Noleggio', NOLEGGIO), ('La scuola', SCUOLA), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'La base', 'Da aprile a ottobre, tutti i giorni 8:30 — 19<br>D’inverno il sabato mattina', VOCI,
                   '© 2026 Bolina · Associazione sportiva · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150,
                   fondo=C['sala'])


# ── L'apertura: la rotta di bolina ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
ROTTA = [(1180, 708), (985, 580), (1325, 446), (985, 312), (1236, 182)]       # px sulla tela del computer
ROTTA_M = [(370, 610), (130, 500), (500, 382), (130, 262), (400, 140)]        # sul telefono
CORSI_SLIDE = [  # linguetta, occhiello, riga 1, riga 2, testo, video, vento
    ('Primo corso', 'Primo corso · cinque giorni', 'Il vento contro,', 'e si va avanti.',
     'Cinque mattine sulle derive, con l’istruttore a bordo: alla fine del corso la barca la portate voi, controvento.',
     V_VELA, 'Maestrale · 10 nodi'),
    ('Perfezionamento', 'Perfezionamento · sui cabinati', 'Stringere il vento,', 'sentire la barca.',
     'Regolare le vele, virare in equipaggio, prendere una boa col mare formato: il corso per chi sa già andare.',
     V_SBANDA, 'Libeccio · 16 nodi'),
    ('Crociera', 'Crociera · una settimana', 'Sette giorni,', 'sei isole.',
     'Giglio, Giannutri, Montecristo e ritorno: si vive a bordo, si fa la guardia, si cucina in rada.',
     V_DRONE, 'Grecale · 12 nodi'),
    ('Tramonto', 'Uscita al tramonto · due ore', 'Due ore al largo,', 'col sole basso.',
     'Per chi non ha mai messo piede su una barca: si esce alle sette, si torna col buio e la cena è pronta in base.',
     V_TRAMONTO, 'Brezza di terra · 6 nodi'),
]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def _seg(p, q, sp, tw, th):
    L = math.hypot(q[0] - p[0], q[1] - p[1])
    cx, cy = (p[0] + q[0]) / 2, (p[1] + q[1]) / 2
    return _pc(cx - L / 2, cy - sp / 2, L, sp, tw, th) + (round(math.degrees(math.atan2(q[1] - p[1], q[0] - p[0])), 2),)


CARTA_R = [(760, 760), (600, 630), (1250, 510), (760, 380), (1270, 240)]     # la rotta sulla carta (px del computer)
FM = 640.0 / 1440.0                                                           # sul telefono: la stessa rotta in scala


def apertura(t):
    """La carta nautica: il mare dal drone, la griglia della carta, la rosa dei venti e il cartiglio col titolo. Lo slider
    resta fermo in cima mentre si scorre e la barca risale il vento lungo la rotta di bolina, virata dopo virata, fino
    alla boa; alla fine sale il velo scuro dal basso con l'invito e i pulsanti."""
    m = K.mob
    cm = lambda pt: (pt[0] * FM, pt[1] * FM + 150)          # un punto della carta sul telefono
    livelli = []
    # la griglia della carta
    for k in range(1, 9):
        livelli.append(K.livello('bo-mer%d' % k, tipo='shape', x=k * 11.11, y=0, w=0.07, h=100, bgColor=mix(C['chiaro'], 14),
                                 entra='fadeIn', ritardo=60 * k, durata=800, responsive={'mobile': {'width': 0.16}}))
    for k in range(1, 5):
        livelli.append(K.livello('bo-par%d' % k, tipo='shape', x=0, y=k * 20, w=100, h=0.12, bgColor=mix(C['chiaro'], 14),
                                 entra='fadeIn', ritardo=60 * k, durata=800, responsive={'mobile': {'height': 0.07}}))
    # la rotta (lati tratteggiati) e la boa
    for k in range(len(CARTA_R) - 1):
        a_ = _seg(CARTA_R[k], CARTA_R[k + 1], 3, W, H)
        b_ = _seg(cm(CARTA_R[k]), cm(CARTA_R[k + 1]), 3, WM, HM)
        livelli.append(K.livello('bo-lato%d' % k, tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], bgColor='transparent',
                                 borderWidth=1, borderStyle='dashed', borderColor=mix(C['chiaro'], 70), esce='fadeOut',
                                 timeline=K.tl(a_[0], a_[1], [(0, {'rotation': a_[4], 'maskRight': 100}),
                                                              (400 + 350 * k, {'rotation': a_[4], 'maskRight': 100}),
                                                              (750 + 350 * k, {'rotation': a_[4], 'maskRight': 0})],
                                               750 + 350 * k, loop=False),
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
    for nome, pt, r_, testo, dx in (('boa', CARTA_R[-1], 15, 'Boa di bolina', 26), ('via', CARTA_R[0], 11, 'Partenza', 22)):
        a_ = _pc(pt[0] - r_, pt[1] - r_, 2 * r_, 2 * r_, W, H)
        q = cm(pt)
        b_ = _pc(q[0] - r_, q[1] - r_, 2 * r_, 2 * r_, WM, HM)
        livelli.append(K.livello('bo-%s' % nome, tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], borderRadius=999,
                                 bgColor=CERATA, borderWidth=3, borderColor=C['chiaro'], entra='zoomIn', ritardo=1800,
                                 durata=600, esce='fadeOut',
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
        a_ = _pc(pt[0] + dx, pt[1] - 13, 200, 26, W, H)
        b_ = _pc(q[0] + dx if nome == 'via' else q[0] - 190, q[1] - 15, 220, 30, WM, HM)
        livelli.append(K.livello('bo-%st' % nome, contenuto=testo, x=a_[0], y=a_[1], w='auto', size=12, peso='600',
                                 colore=C['chiaro'], bgColor=mix(C['nero'], 72), padding=7, borderRadius=6, letterSpacing=1,
                                 textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=2000, durata=500,
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'fontSize': m(10)}}))
    # la barca: risale la rotta con lo scorrimento
    s0 = 26
    tappe = [{'p': 0}]
    for k, (pt, pp) in enumerate(zip(CARTA_R[1:], (24, 48, 70, 92))):
        tappe.append({'p': pp, 'x': pt[0] - CARTA_R[0][0], 'y': pt[1] - CARTA_R[0][1]})
    tappe.append({'p': 100, 'x': CARTA_R[-1][0] - CARTA_R[0][0], 'y': CARTA_R[-1][1] - CARTA_R[0][1]})
    a_ = _pc(CARTA_R[0][0] - s0 / 2, CARTA_R[0][1] - s0 / 2, s0, s0, W, H)
    q = cm(CARTA_R[0])
    b_ = _pc(q[0] - s0 / 2, q[1] - s0 / 2, s0, s0, WM, HM)
    livelli.append(K.livello('bo-barca', tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], borderRadius=999, bgColor=C['chiaro'],
                             borderWidth=6, borderColor=ACQUA, entra='zoomIn', ritardo=1200, durata=600, esce='fadeOut',
                             scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe},
                             responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
    # la rosa dei venti e il vento del giorno
    livelli += [
        K.livello('bo-rosa', tipo='shape', x=89, y=6, w=6.94, h=12.35, borderRadius=999, bgColor='transparent', borderWidth=2,
                  borderColor=mix(C['chiaro'], 70), entra='zoomIn', ritardo=400,
                  responsive={'mobile': {'x': 78, 'y': 6, 'width': 15.6, 'height': 7.14}}),
        K.livello('bo-nord', contenuto='N', x=89, y=7.4, w=6.94, size=22, peso='700', colore=C['chiaro'], align='center',
                  entra='fadeIn', ritardo=600, responsive={'mobile': {'x': 78, 'y': 7.6, 'width': 15.6, 'fontSize': m(16)}}),
        K.livello('bo-vento', contenuto='↓ Maestrale · 12 nodi', x=78, y=20, w='auto', size=12, peso='600', colore=C['nero'],
                  bgColor=C['chiaro'], padding=9, borderRadius=999, fontFamily=MONO, letterSpacing=1, entra='fadeInDown',
                  ritardo=700, responsive={'mobile': {'x': 52, 'y': 14.5, 'fontSize': m(10)}}),
    ]
    # il cartiglio col titolo, in alto a sinistra
    livelli += [
        K.livello('bo-cartiglio', tipo='shape', x=5, y=9, w=38, h=42, bgColor=mix(C['nero'], 82), borderRadius=4,
                  borderWidth=1, borderColor=mix(C['chiaro'], 45), entra='fadeIn', ritardo=0, durata=700,
                  responsive={'mobile': {'x': 4, 'y': 52, 'width': 92, 'height': 30}}),
        K.livello('bo-o', contenuto='Carta della scuola · Argentario', x=7.5, y=13, w=34, size=12, peso='600', colore=ACQUA,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(11), 'y': 54.5, 'x': 9, 'width': 82}}),
        K.livello('bo-t', contenuto='Il vento contro,<br><span style="color:var(--olo-color-secondary)">e si va avanti.</span>',
                  tag='h1', x=7.5, y=17.5, w=34, size=58, peso='600', colore=C['chiaro'], lineHeight=1.04, letterSpacing=-2,
                  entra='maskFromBottom', ritardo=300, durata=1100,
                  responsive={'mobile': {'fontSize': m(31), 'y': 57.5, 'x': 9, 'width': 82}}),
        K.livello('bo-s', contenuto='La bolina è l’andatura più difficile: si risale il vento a zig-zag. Scorrete e la barca va '
                  'alla boa.', x=7.5, y=36, w=31, size=15, peso='400', colore=mix(C['chiaro'], 88), lineHeight=1.6,
                  ritardo=800, responsive={'mobile': {'fontSize': m(13), 'y': 68.5, 'x': 9, 'width': 82}}),
    ]
    # alla fine: l'invito
    fine = lambda i, f: {'inizio': i, 'fine': f, 'da': {'y': 40, 'o': 0}, 'a': {}}
    livelli += [
        K.livello('bo-velo', tipo='shape', x=0, y=55, w=100, h=45, entra='fadeIn', ritardo=0,
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 90), 'to': mix(C['nero'], 0), 'fromStop': 40},
                  scrollMove={'inizio': 78, 'fine': 92, 'da': {'o': 0}, 'a': {'o': 100}},
                  responsive={'mobile': {'y': 70, 'height': 30}}),
        K.livello('bo-b', tipo='button', contenuto='Scegli il corso', x=5, y=84, w='auto', size=14, peso='600',
                  colore=C['nero'], bgColor=CERATA, padding=16, borderRadius=999, entra='fadeIn', ritardo=0, buttonUrl=CORSI,
                  hover={'bgColor': C['chiaro'], 'duration': 250}, scrollMove=fine(84, 96),
                  responsive={'mobile': {'y': 87, 'x': 7, 'fontSize': m(12)}}),
        K.livello('bo-l', tipo='button', contenuto='Noleggio', x=17.5, y=84, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), entra='fadeIn', ritardo=0, buttonUrl=NOLEGGIO,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250}, scrollMove=fine(86, 98),
                  responsive={'tablet': {'x': 21}, 'mobile': {'y': 87, 'x': 52, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('bo-1', K.sfondo_video(V_DRONE, overlay=0.2, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'vh', 'value': 100}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def orario(t):
    corsi = [('deriva', 'Primo corso su deriva', C['acc'], 'Cinque mattine, dai 14 anni.', 180),
             ('cabinato', 'Perfezionamento', ACQUA, 'Sui cabinati da 9 metri.', 240),
             ('ragazzi', 'Vela per ragazzi', CERATA, 'Dagli 8 ai 13 anni, sugli Optimist.', 150),
             ('tramonto', 'Uscita al tramonto', mix(C['acc'], 55, C['testo']), 'Due ore, per tutti.', 120)]
    piano = [(1, '09:00', '12:00', 'deriva'), (1, '15:00', '17:30', 'ragazzi'), (2, '09:00', '13:00', 'cabinato'),
             (2, '19:00', '21:00', 'tramonto'), (3, '09:00', '12:00', 'deriva'), (3, '15:00', '17:30', 'ragazzi'),
             (4, '09:00', '13:00', 'cabinato'), (4, '19:00', '21:00', 'tramonto'), (5, '09:00', '12:00', 'deriva'),
             (5, '15:00', '17:30', 'ragazzi'), (6, '09:00', '13:00', 'cabinato'), (6, '19:00', '21:00', 'tramonto')]
    return t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'bo-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': 'Franco Rossetti'
                     if c in ('cabinato', 'tramonto') else 'Giulia Neri', 'sala': 'Pontile della base', 'livello': 'tutti',
                     'posti': 6, 'nota': ''} for k, (g, a, b, c) in enumerate(piano)],
        'giorni': 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato', 'ora_inizio': '08:00', 'ora_fine': '22:00',
        'mostra_filtri': True, 'etichetta_tutti': 'Tutti', 'filtro_livello': False, 'filtro_istruttore': True,
        'testo_vuoto': 'Nessun corso con questi filtri.', 'testo_prenota': 'Prenota',
        'link_prenota': CONTATTI + '?corso={corso}&giorno={giorno}&ora={ora}#scrivici', 'colore_oggi': C['acc'],
        'colore_adesso': CERATA, 'colore_tenue': C['testo'], 'giorno_font_family': 'heading', 'giorno_font_size': 20,
        'giorno_font_weight': '600', 'giorno_uppercase': False, 'corso_font_family': 'heading', 'corso_font_size': 17,
        'corso_font_weight': '600', 'ora_font_family': 'var(--olo-font-family-mono)', 'raggio_lezione': radius(10),
        'altezza_ora': 40, 'gap': 4, 'forma_filtri': 'pillola'}, style={})


def corsi_lista(t):
    voci = [('Primo corso su deriva', 'Cinque mattine · dai 14 anni', '290 €'),
            ('Perfezionamento sui cabinati', 'Quattro giorni · per chi sa già andare', '420 €'),
            ('Crociera fra le isole', 'Sette giorni a bordo · cabina doppia', '890 €'),
            ('Vela per ragazzi', 'Due settimane · dagli 8 ai 13 anni', '360 €'),
            ('Uscita al tramonto', 'Due ore · per tutti', '45 €')]
    return t.classic('worklist', {
        'items': [{'number': '0%d' % (k + 1), 'title': ti, 'category': ca, 'year': pr, 'link_url': CORSI}
                  for k, (ti, ca, pr) in enumerate(voci)],
        'divider_color': C['filo'], 'row_padding_y': 26, 'row_hover_bg': mix(C['acc'], 6), 'hover_indent': 28,
        'number_color': C['acc'], 'number_size': 14, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 40, 'title_weight': '600', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': C['acc'], 'year_size': 20, 'show_arrow': True, 'arrow_color': CERATA,
        'mono_font_family': MONO}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 36, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': CERATA}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Bolina · Argentario, dal 1998', ['Andare controvento', 'si impara.'], size=60,
                                layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'La bolina è l’andatura più difficile: la barca risale il vento a zig-zag, una virata dopo '
                                'l’altra. È anche quella che insegna di più. Da ventotto anni la insegniamo a chi non è mai '
                                'salito su una barca e a chi vuole fare il giro del mondo.', size=17, color=C['testo']),
                      K.button(t, 'La scuola', SCUOLA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('28', 'Stagioni'), ('14', 'Barche'), ('9', 'Istruttori federali'), ('6.400', 'Allievi')])],
              style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I corsi', ['Dalla prima uscita', 'alla traversata.'], size=58,
                         tagline='Passate su un corso: la freccia porta al calendario.')]),
        t.one([corsi_lista(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_WINCH, 'Il winch', 'Cazzare la scotta, <em>sentire il vento.</em>',
                             'Il primo giorno si impara a tirare una cima. Il quinto, a capire quando lasciarla.',
                             overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8918062.jpg', 'Franco spiega una manovra a un ragazzo vicino alla vela', ratio='4/5',
                              tilt=True)], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Gli istruttori', ['Nove istruttori,', 'tutti federali.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Franco ha fatto quattro traversate atlantiche e insegna qui dal primo giorno. Giulia ha corso '
                                'in 470 e segue i ragazzi. Nessuno di loro alza la voce in barca.', size=17),
                      K.specs(t, [('In barca', 'massimo quattro allievi per istruttore'), ('Lingue', 'italiano, inglese, '
                                  'tedesco'), ('Sicurezza', 'gommone d’appoggio sempre in acqua')]),
                      K.button(t, 'La scuola', SCUOLA, kind='pieno', pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Avevo paura dell’acqua alta. Il terzo giorno Franco mi ha passato il timone e si è seduto a prua a guardare il '
           'mare. Il quinto ho preso la boa da sola, controvento.', 'Elena C.', 'Primo corso, giugno 2025')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_TRAMONTO, 'Al tramonto', 'Due ore <em>col sole basso.</em>',
                          'L’uscita per chi non è mai salito su una barca: si esce alle sette, si torna col buio.',
                          'Prenota l’uscita', CONTATTI + '#scrivici', overlay=40, video=True))
    return out


def build_corsi():
    t = Tpl(ID, 'corsi')
    out = [K.intro(t, 'I corsi', ['Dalla deriva', 'alla crociera.'],
                   'Gruppi piccoli, istruttori federali, barche controllate ogni mattina. Il primo giorno si impara a nuotare '
                   'col giubbotto, l’ultimo a prendere una boa.', bg=K.video_bg(V_SBANDA), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Tre corsi,', 'tre mari diversi.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Primo corso', '290', 'cinque mattine', 'Deriva con l’istruttore a bordo\nTeoria al pontile\nTessera federale '
             'compresa', False, ''),
            ('Perfezionamento', '420', 'quattro giorni', 'Cabinato da 9 metri\nManovre in equipaggio\nUna navigazione '
             'notturna', True, 'il più scelto'),
            ('Crociera', '890', 'sette giorni a bordo', 'Cabina doppia, cambusa compresa\nGiglio, Giannutri, Montecristo\n'
             'Lo skipper è un istruttore', False, ''),
        ], 'Prenota il corso', CONTATTI + '#scrivici', nota='Si parte da aprile a ottobre. Se il vento è troppo forte la lezione '
           'si recupera.'),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il calendario', ['Una settimana', 'di corsi.'], size=56,
                         tagline='Filtrate per istruttore; toccate una lezione per prenotarla.')]),
        t.one([orario(t)]),
    ], padding='large', width='large', html_id='calendario', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'di partire.', [
        ('Devo saper nuotare?', 'Sì, almeno cinquanta metri. Il giubbotto si porta sempre.'),
        ('Che cosa porto?', 'Scarpe con la suola chiara, una felpa, la crema. Cerate e giubbotti li diamo noi.'),
        ('E se c’è troppo vento?', 'La lezione si sposta: con più di 20 nodi i principianti fanno teoria e nodi al pontile.'),
    ], 'bo-faq'))
    return out


def build_noleggio():
    t = Tpl(ID, 'noleggio')
    out = [K.intro(t, 'Noleggio', ['Una barca,', 'per un giorno o una settimana.'],
                   'Derive, catamarani e cabinati, con o senza skipper. Per noleggiare senza skipper serve la patente '
                   'nautica o un nostro corso.', bg=K.photo_bg('pexels-16829946.jpg'), size=92)]
    barche = [('Deriva Laser · 4 m', 'Una persona, per allenarsi', '60 € / mezza giornata', 'pexels-12157648.jpg', ''),
              ('Catamarano · 5 m', 'Due persone, veloce e stabile', '120 € / giorno', 'pexels-2574037.jpg', ''),
              ('Cabinato · 9 m', 'Sei persone, per le isole vicine', '280 € / giorno', 'pexels-38926131.jpg', 'Con skipper'),
              ('Cabinato · 12 m', 'Otto persone, quattro cabine', '2.400 € / settimana', 'pexels-40229498.jpg', ''),
              ('Gommone d’appoggio', 'Per le foto e i ragazzi', '90 € / mezza giornata', 'pexels-11195818.jpg', '')]
    listino = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in barche],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 35), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '12', 'show_image': True,
        'price_position': 'right', 'badge_bg': CERATA, 'badge_color': C['nero'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La flotta', ['Quattordici barche,', 'tutte controllate.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Ogni mattina si controllano vele, cime e motore. Le barche escono solo se il bollettino lo '
                                'permette.', size=16),
                      K.button(t, 'Chiedi la barca', CONTATTI + '#scrivici', kind='pieno', pad_top=14)], wm='2-5', ws='1-1'),
               t.col([listino], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I nodi', ['Cinque nodi', 'da sapere prima.'], size=56,
                         tagline='Passate su una carta: dietro c’è a che cosa serve.')]),
        t.row([t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(f), 'image_size': 'cover', 'image_position': 'center center',
                            'overlay_color': C['nero'], 'overlay_opacity': 45}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': 'Girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': nome, 'back_description': uso,
            'back_bg': C['sala'], 'back_text_color': C['testo'], 'back_valign': 'center', 'back_cta_text': '',
            'back_cta_url': '', 'flip_direction': 'horizontal', 'flip_trigger': 'hover', 'card_height': '360',
            'card_border_radius': '12', 'card_shadow': 'md', 'title_size': '24', 'title_weight': '600', 'desc_size': '15'},
            style={})], wm='1-3', ws='1-1') for f, nome, uso in (
            ('pexels-275637.jpg', 'Il nodo di bitta', 'Per legare la barca alla bitta del pontile: tiene e si scioglie in '
             'un attimo.'),
            ('pexels-4253990.jpg', 'La gassa d’amante', 'Un occhio che non scorre: per le scotte, per i parabordi, per '
             'tirare su qualcuno.'),
            ('pexels-5370838.jpg', 'Il nodo Savoia', 'In fondo alle scotte, perché non sfilino dal bozzello.'))],
              layout='33-33-33', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_DRONE, 'Le isole', 'Giglio, Giannutri, <em>Montecristo.</em>',
                             'In due ore di bolina il Giglio, in tre Giannutri. Montecristo si guarda da fuori: si entra solo '
                             'col permesso.', overlay=40))
    return out


def build_scuola():
    t = Tpl(ID, 'la-scuola')
    out = [K.intro(t, 'La scuola', ['Un pontile,', 'ventotto stagioni.'],
                   'Nata nel 1998 con due derive e un gommone. Oggi quattordici barche, nove istruttori e lo stesso pontile.',
                   bg=K.photo_bg('pexels-40229498.jpg'), size=92)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-5416486.jpg', 'Due istruttori sistemano le cime a prua', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Il metodo', ['Prima il vento,', 'poi la barca.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Si comincia sempre a terra, a guardare il mare: da dove viene il vento, come cambia, cosa '
                                'fanno le altre barche. Poi in acqua, con l’istruttore a bordo, e solo quando siete pronti '
                                'da soli, col gommone vicino.', size=17),
                      K.specs(t, [('Istruttori', 'nove, tutti federali'), ('Allievi per barca', 'quattro al massimo'),
                                  ('Barche', 'quattordici, dai Laser ai 12 metri'), ('Base', 'Porto Santo Stefano')])],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il mare di casa', ['Dove usciamo', 'ogni giorno.'], size=56,
                         tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-30903100.jpg', 'Il mare blu con le creste bianche', 'Il canale', 'col maestrale'),
                               ('pexels-38926131.jpg', 'Due barche a vela sul mare calmo', 'La baia', 'di mattina'),
                               ('pexels-16313846.jpg', 'Un’onda verde acqua dall’alto', 'Le secche', 'col libeccio'),
                               ('pexels-13122991.jpg', 'Il mare increspato al sole', 'Il largo', 'nel pomeriggio'),
                               ('pexels-12157648.jpg', 'Una vela bianca sul mare blu', 'Verso il Giglio', 'in bolina')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Mio figlio ha fatto due estati di Optimist con Giulia. Adesso ci porta lui in barca, e ci spiega perché stiamo '
           'sbagliando tutto.', 'Roberto M.', 'Papà di un allievo')])], padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Venite al pontile,', 'o scriveteci.'],
                   'Per un corso, un noleggio, un’uscita al tramonto o un regalo.', bg=K.photo_bg('pexels-11195818.jpg'),
                   size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa vi interessa', 'corso', True,
                     opzioni='Primo corso su deriva\nPerfezionamento\nCrociera fra le isole\nVela per ragazzi\nUscita al '
                             'tramonto\nNoleggio', param='corso'),
             K.campo('date', 'Dal giorno', 'dal'), K.campo('date', 'Al giorno', 'al'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1',
                     segnaposto='Quanti siete, che esperienza avete, cosa vi piacerebbe fare.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Una riga,', 'vi rispondiamo dal pontile.'],
                                'Rispondiamo entro un giorno. Per le uscite di domani, meglio telefonare.', VOCI, campi,
                                'Bolina — contatto', 'Invia', 'Ricevuto. Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La base', ['Porto Santo Stefano,', 'il pontile in fondo.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Lungomare dei Navigatori 12'), ('Stagione', 'aprile — ottobre, tutti i '
                                  'giorni'), ('Parcheggio', 'al porto, gratis per gli allievi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Bolina · la base', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='base', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori rotta', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è andata alla deriva.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Virata, e si torna in porto.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_VELA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'La carta nautica',
    ['vela', 'scuola di vela', 'corsi', 'noleggio barche', 'mare', 'argentario', 'chiaro', 'slider pro'],
    'Una scuola di vela all’Argentario, bianco vela e blu mare: in apertura la carta nautica sul mare visto dal '
    'drone, con la rosa dei venti e la barca che risale la rotta di bolina mentre si scorre, fino alla boa; i corsi '
    'coi prezzi, il calendario della settimana, la flotta da noleggiare e i nodi.',
    [('I corsi', CORSI), ('Noleggio', NOLEGGIO), ('La scuola', SCUOLA), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('corsi', 'I corsi', 'corsi'), ('noleggio', 'Noleggio', 'noleggio'),
     ('la-scuola', 'La scuola', 'la-scuola'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'DM Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Outfit\', system-ui, sans-serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Outfit', 'DM Sans', 'JetBrains Mono'], corsivo_di=[],
    peso_titoli='600', raggio=12, grana=0, titoli_line='1.04', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'onde'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'corsi.json': build_corsi(), 'noleggio.json': build_noleggio(), 'la-scuola.json': build_scuola(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
