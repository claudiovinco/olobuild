# -*- coding: utf-8 -*-
"""Genera il tema «Tavola Errante» (id tavola-errante): la pagina personale di uno chef indipendente che non ha un
ristorante: cucina ogni volta in un posto diverso (una vigna, una spiaggia, una radura, un cortile), diciotto ospiti a
tavola, un menu che nasce dal luogo. Il calendario delle tappe, il menu della stagione, lo chef, la prenotazione.

Tema scuro, il nero della brace, la luce delle candele (ambra) e il verderame; titoli in Vollkorn col corsivo, testo in
Karla, dati in DM Mono.

Apertura: Slider Pro «le tappe», alto 16:9, una diapositiva per tappa col suo video pieno (la vigna, la spiaggia al
tramonto, il fuoco nel bosco, le luci nel cortile) e un velo scuro solo a sinistra. A destra il biglietto della cena,
col bordo tratteggiato: la data enorme, il luogo, i posti rimasti, il prezzo; si inclina verso il puntatore. Le tappe si
girano come le pagine di un taccuino (flipH). Cursore: l'etichetta «Prenota» sui link.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'tavola-errante'
NAME = 'Tavola Errante'

HEX = {
    'primary': '#F0A24A', 'primary_contrast': '#160F0A', 'secondary': '#5FB8AD', 'secondary_contrast': '#0E1514',
    'accent': '#F6C77E', 'dark': '#0A0705', 'light': '#FBF3E6', 'muted': '#1B1410', 'muted_contrast': '#F3E9DA',
    'text': '#F3E9DA', 'text_muted': '#B9A994', 'background': '#120D0A', 'border': '#33271E', 'link': '#F0A24A',
}

HOME, CENE, MENU, CHEF, PRENOTA = ('{{PAGINA:home}}', '{{PAGINA:le-cene}}', '{{PAGINA:menu}}', '{{PAGINA:chi-sono}}',
                                   '{{PAGINA:prenota}}')
MAIL, PHONE, PHONE_TEL = 'tavola@tavolaerrante.example', '+39 347 000 2614', 'tel:+393470002614'

V_FUOCO, V_BRACE, V_LUCI, V_SPIAGGIA, V_PIATTO, V_VIGNA = (
    'pexels-video-5032257', 'pexels-video-854077', 'pexels-video-5617254', 'pexels-video-5189828', 'pexels-video-39453091',
    'pexels-video-20203976')
PHOTOS = [
    'pexels-32179374.jpg', 'pexels-5638699.jpg', 'pexels-27389956.jpg', 'pexels-16120148.jpg', 'pexels-5638677.jpg',
    'pexels-32224391.jpg', 'pexels-17248503.jpg', 'pexels-6111928.jpg', 'pexels-29101361.jpg', 'pexels-24186303.jpg',
    'pexels-29259652.jpg', 'pexels-36799182.jpg', 'pexels-18426522.jpg', 'pexels-8365312.jpg', 'pexels-5779781.jpg',
    'pexels-17086146.jpg', 'pexels-19987294.jpg', 'pexels-36989618.jpg', 'pexels-37155669.jpg', 'pexels-12932171.jpg',
    'pexels-5638816.jpg', 'pexels-5638820.jpg',
]
VIDEOS = [V_FUOCO, V_BRACE, V_LUCI, V_SPIAGGIA, V_PIATTO, V_VIGNA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=False)
C = K.c
CANDELA, VERDERAME = C['acc2'], C['sec']
TITOLI, MONO = 'Vollkorn', 'DM Mono'
VOCI = [('map-pin', 'Ogni volta in un posto diverso', CENE), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Tavola Errante', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Tavola Errante', 'Elia Mancuso, cuoco senza ristorante. Ogni mese una tavola lunga in un posto che '
                   'non è un ristorante: una vigna, una spiaggia, una radura, un cortile. Diciotto posti, un menu che nasce lì.',
                   [('Le cene', CENE), ('Il menu', MENU), ('Chi sono', CHEF), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'Come funziona', 'Si prenota il posto, non il tavolo<br>Il luogo esatto arriva tre giorni prima', VOCI,
                   '© 2026 Tavola Errante · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=120)


# ── L'apertura: la tavola che si apparecchia ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
PIATTI = [  # foto, centro sul computer, centro sul telefono, da dove arriva (sopra o sotto)
    ('pexels-24186303.jpg', (300, 340), (112, 600), -1), ('pexels-6111928.jpg', (720, 340), (320, 600), -1),
    ('pexels-29101361.jpg', (1140, 340), (528, 600), -1), ('pexels-18426522.jpg', (510, 488), (112, 800), 1),
    ('pexels-29259652.jpg', (930, 488), (320, 800), 1), ('pexels-36799182.jpg', (1350, 488), (528, 800), 1),
]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    """La tavola vista dall'alto: una tavola lunga di legno attraversa l'apertura sul video della vigna. Lo slider resta
    fermo in cima mentre si scorre: arrivano i piatti, uno dopo l'altro, dall'alto e dal basso, si accendono le candele e
    i bicchieri; il titolo esce, entrano la data della prossima tappa e l'invito."""
    m = K.mob
    livelli = [
        # la tavola e la tovaglia
        K.livello('te-tavola', tipo='shape', x=-2, y=31, w=104, h=40, borderRadius=10,
                  shapeGradient={'angle': 180, 'from': mix(C['acc'], 38, C['nero']), 'to': mix(C['acc'], 24, C['nero'])},
                  boxShadow={'x': 0, 'y': 30, 'blur': 60, 'spread': -10, 'color': mix(C['nero'], 80)}, entra='fadeIn', ritardo=0,
                  durata=900, responsive={'mobile': {'x': -2, 'y': 37, 'width': 104, 'height': 28}}),
        K.livello('te-runner', tipo='shape', x=-2, y=48.6, w=104, h=4.8, bgColor=mix(C['chiaro'], 82), entra='fadeIn', ritardo=300,
                  durata=900, responsive={'mobile': {'y': 49.8, 'height': 3.2}}),
    ]
    # i piatti: arrivano uno dopo l'altro
    for k, (foto, (cx, cy), (mx, my), lato) in enumerate(PIATTI):
        a_ = _pc(cx - 86, cy - 86, 172, 172, W, H)
        b_ = _pc(mx - 82, my - 82, 164, 164, WM, HM)
        i0 = 6 + 9 * k
        livelli.append(K.livello('te-piatto%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a_[0], y=a_[1],
                                 w=a_[2], h=a_[3], borderRadius=999, borderWidth=7, borderColor=C['chiaro'], entra='fadeIn',
                                 ritardo=0, durata=300,
                                 boxShadow={'x': 0, 'y': 14, 'blur': 26, 'spread': -6, 'color': mix(C['nero'], 75)},
                                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': [
                                     {'p': 0, 'y': 620 * lato, 'r': -40 * lato, 'o': 0}, {'p': i0, 'y': 620 * lato, 'r': -40 * lato, 'o': 0},
                                     {'p': i0 + 14, 'y': 0, 'r': 0, 'o': 100}, {'p': 100, 'o': 100}]},
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
        # il bicchiere accanto al piatto
        gx, gy = cx + 100, cy - 66 * lato
        a_ = _pc(gx - 15, gy - 15, 30, 30, W, H)
        b_ = _pc(mx + 66 - 13, my - 74 * lato - 13, 26, 26, WM, HM)
        livelli.append(K.livello('te-calice%d' % k, tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], borderRadius=999,
                                 bgColor=mix(C['chiaro'], 14), borderWidth=2, borderColor=mix(C['chiaro'], 70), entra='fadeIn',
                                 ritardo=0, scrollMove={'inizio': 0, 'fine': 100, 'tappe': [
                                     {'p': 0, 's': 0.2, 'o': 0}, {'p': i0 + 16, 's': 0.2, 'o': 0}, {'p': i0 + 22, 's': 1, 'o': 100},
                                     {'p': 100, 'o': 100}]},
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
    # le candele sulla tovaglia: si accendono verso la fine
    for k, cx in enumerate((200, 620, 1040)):
        a_ = _pc(cx - 13, 411 - 13, 26, 26, W, H)
        b_ = _pc(110 + 210 * k - 12, 700 - 12, 24, 24, WM, HM)
        livelli.append(K.livello('te-candela%d' % k, tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], borderRadius=999,
                                 bgColor=CANDELA, boxShadow={'x': 0, 'y': 0, 'blur': 26, 'spread': 6, 'color': mix(CANDELA, 70)},
                                 entra='fadeIn', ritardo=0, scrollMove={'inizio': 0, 'fine': 100, 'tappe': [
                                     {'p': 0, 'o': 0, 's': 0.3}, {'p': 64 + 4 * k, 'o': 0, 's': 0.3}, {'p': 72 + 4 * k, 'o': 100, 's': 1},
                                     {'p': 100, 'o': 100}]},
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}))
    via = {'inizio': 0, 'fine': 45, 'da': {}, 'a': {'y': -80, 'o': 0}}
    fine = lambda i, f: {'inizio': i, 'fine': f, 'da': {'y': 40, 'o': 0}, 'a': {}}
    livelli += [
        K.livello('te-cielo', tipo='shape', x=0, y=0, w=100, h=34, entra='fadeIn', ritardo=0, durata=600,
                  shapeGradient={'angle': 180, 'from': mix(C['nero'], 85), 'to': mix(C['nero'], 0), 'fromStop': 40},
                  responsive={'mobile': {'height': 38}}),
        K.livello('te-fondo', tipo='shape', x=0, y=68, w=100, h=32, entra='fadeIn', ritardo=0, durata=600,
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 35},
                  responsive={'mobile': {'y': 64, 'height': 36}}),
        K.livello('te-o', contenuto='Elia Mancuso · cuoco senza ristorante', x=6, y=9, w=50, size=12, peso='500', colore=CANDELA,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(11), 'y': 9, 'x': 7, 'width': 86}, 'tablet': {'visible': False}}),
        K.livello('te-t', contenuto='Diciotto posti,<br><em>una tavola sola.</em>', tag='h1', x=6, y=12.5, w=60, size=72,
                  peso='500', colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-1, entra='maskFromBottom',
                  ritardo=300, durata=1200, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(34), 'y': 12, 'x': 7, 'width': 86}, 'tablet': {'y': 15, 'fontSize': 40}}),
        K.livello('te-scorri', contenuto='Scorrete: si apparecchia', x=74, y=12, w=20, size=12, peso='500',
                  colore=mix(C['chiaro'], 75), letterSpacing=1, fontFamily=MONO, align='right', ritardo=900,
                  scrollMove={'inizio': 0, 'fine': 15, 'da': {}, 'a': {'o': 0}},
                  responsive={'mobile': {'x': 7, 'y': 24, 'width': 86, 'fontSize': m(10), 'textAlign': 'left'},
                              'tablet': {'visible': False}}),
        K.livello('te-data', contenuto='Prossima tappa · 14 · 06 · una villa sui Colli Euganei · 4 posti liberi', x=6, y=10,
                  w='auto', size=13, peso='600', colore=C['nero'], bgColor=VERDERAME, padding=10, borderRadius=999,
                  fontFamily=MONO, letterSpacing=1, entra='fadeIn', ritardo=0, scrollMove=fine(62, 78),
                  responsive={'mobile': {'x': 7, 'y': 69, 'fontSize': m(10)}}),
        K.livello('te-t2', contenuto='Una cena, <em>dove capita.</em>', tag='p', x=6, y=15.5, w=50, size=46, peso='500',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.05, entra='fadeIn', ritardo=0, scrollMove=fine(68, 84),
                  responsive={'mobile': {'fontSize': m(28), 'y': 73.5, 'x': 7, 'width': 86}}),
        K.livello('te-b', tipo='button', contenuto='Prenota un posto', x=62, y=15, w='auto', size=12, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase', borderRadius=4,
                  entra='fadeIn', ritardo=0, buttonUrl=PRENOTA, hover={'bgColor': C['chiaro'], 'duration': 250},
                  scrollMove=fine(76, 92), responsive={'tablet': {'x': 58}, 'mobile': {'y': 82, 'x': 7, 'fontSize': m(11)}}),
        K.livello('te-l', tipo='button', contenuto='Tutte le date', x=78, y=15, w='auto', size=12, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=16, letterSpacing=2, textTransform='uppercase',
                  borderRadius=4, borderWidth=1, borderColor=mix(C['chiaro'], 50), entra='fadeIn', ritardo=0, buttonUrl=CENE,
                  hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  scrollMove=fine(78, 94), responsive={'tablet': {'x': 78}, 'mobile': {'y': 87, 'x': 7, 'fontSize': m(11)}}),
    ]
    sl = [K.diapositiva('te-1', K.sfondo_video(V_VIGNA, overlay=0.2, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


DATE = [  # numero, luogo, categoria, prezzo
    ('14 · 06', 'Una villa sui colli · Colli Euganei', 'Vigna · menu della vendemmia · 18 posti', '70 €'),
    ('28 · 06', 'Spiaggia di Mezzavalle · Conero', 'Mare · pesce del giorno sulla brace · 16 posti', '75 €'),
    ('12 · 07', 'Una radura sul lago · Sila', 'Bosco · fuoco vivo, funghi e patate · 20 posti', '65 €'),
    ('26 · 07', 'Una cascina in collina · Langhe', 'Cortile · tajarin e luci fino a mezzanotte · 18 posti', '80 €'),
    ('09 · 08', 'Un uliveto · Val d’Orcia', 'Campagna · olio nuovo e pane cotto a legna · 22 posti', '70 €'),
    ('23 · 08', 'Una tonnara · Favignana', 'Isola · il tonno in sei modi · 14 posti', '90 €'),
    ('13 · 09', 'Un rifugio · Dolomiti', 'Montagna · polenta, formaggi d’alpe · 16 posti', '75 €'),
    ('04 · 10', 'Una corte di mattoni · Bologna', 'Città · la sfoglia e il ragù di tre ore · 24 posti', '65 €'),
]


def calendario(t, voci=DATE):
    return t.classic('worklist', {
        'items': [{'number': nu.replace(' · ', '.'), 'title': ti, 'category': ca, 'year': pr, 'link_url': PRENOTA} for nu, ti, ca, pr in voci],
        'divider_color': C['filo'], 'row_padding_y': 26, 'row_hover_bg': mix(C['acc'], 7), 'hover_indent': 28,
        'number_color': CANDELA, 'number_size': 15, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 34, 'title_weight': '500', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': C['acc'], 'year_size': 20, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': MONO}, style={})


def portate(t):
    voci = [('Gli assaggi', 'Pane di lievito madre e piccole cose', 'Il pane lo impasto la notte prima nel posto dove si '
             'mangia; con lui arrivano tre bocconi di quello che c’è intorno.', 'pexels-24186303.jpg'),
            ('Dalla terra', 'Verdure dell’orto vicino', 'La prima portata la decide il campo più vicino: quello che è maturo '
             'quella settimana, crudo o appena passato sul fuoco.', 'pexels-29101361.jpg'),
            ('Dal luogo', 'Un piatto che si fa solo lì', 'Ogni tappa ha un piatto che non ripeto altrove: il tonno a '
             'Favignana, i tajarin nelle Langhe.', 'pexels-6111928.jpg'),
            ('Dal fuoco', 'Brace di legna del posto', 'La portata grande cuoce sulla brace tutto il pomeriggio: la vedete '
             'arrivando, la sentite prima di sedervi.', 'pexels-18426522.jpg'),
            ('La fine', 'Dolce e brindisi, a lume di candela', 'Il dolce arriva quando è buio, con le candele: frutta della '
             'zona, qualcosa di cotto nella cenere, l’ultimo bicchiere.', 'pexels-5638820.jpg')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': MENU,
                   'color': C['acc']} for k, (n, s, d, f) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': CANDELA,
        'number_hover_color': C['acc'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 54, 'name_weight': '500', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': CANDELA,
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 24,
        'hover_indent': 28, 'hover_bg': mix(C['acc'], 6), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 260, 'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': CANDELA, 'quote_font': 'heading',
        'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': CANDELA}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Elia Mancuso · cuoco errante', ['Niente ristorante.', 'Una tavola lunga, ogni volta altrove.'],
                                size=60, layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Dopo dodici anni nelle cucine degli altri ho venduto il furgone del catering e ho comprato '
                                'una tavola pieghevole da diciotto posti. Ogni mese la monto in un posto che non è un '
                                'ristorante e cucino quello che quel posto mi dà. Si prenota il posto, il luogo esatto '
                                'arriva tre giorni prima.', size=17, color=C['testo']),
                      K.button(t, 'Chi sono', CHEF, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('38', 'Cene in un anno'), ('14', 'Regioni'), ('18', 'Posti a tavola'),
                            ('1', 'Menu per ogni luogo')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le prossime tappe', ['Otto date,', 'otto posti diversi.'], size=60,
                         tagline='Passate su una tappa: la freccia porta alla prenotazione. Il prezzo comprende i vini.')]),
        t.one([calendario(t, DATE[:5])]),
        t.one([K.button(t, 'Il calendario intero', CENE, pad_top=26)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='tappe', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_BRACE, 'Il fuoco', 'La brace si accende <em>alle tre del pomeriggio.</em>',
                             'Quando arrivate la carne cuoce da quattro ore e il pane è già sfornato: la cena comincia molto '
                             'prima che vi sediate.', overlay=40))
    out.append(t.section([
        t.one([K.sechead(t, 'Il menu della stagione', ['Cinque portate,', 'una per ogni cosa del posto.'], size=60,
                         tagline='Passate su una portata: il piatto si affaccia. Cambia a ogni tappa, l’ordine resta.')]),
        t.one([portate(t)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Ci siamo seduti con sedici sconosciuti in mezzo a una vigna. Alle dieci di sera eravamo amici, alla fine '
           'qualcuno cantava. Il tonno l’ho ancora in bocca.', 'Chiara e Marco', 'Tappa di Favignana, 2025')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_VIGNA, 'Prenota', 'Un posto <em>alla prossima tavola.</em>',
                          'Si prenota il posto, si paga un acconto, il luogo esatto arriva tre giorni prima con le '
                          'indicazioni per arrivare.', 'Prenota un posto', PRENOTA, overlay=40, video=True))
    return out


def build_cene():
    t = Tpl(ID, 'le-cene')
    out = [K.intro(t, 'Le cene', ['Il calendario', 'delle tappe.'],
                   'Da giugno a ottobre, due cene al mese. Ogni tappa ha i suoi posti, il suo menu e il suo prezzo, vini compresi.',
                   bg=K.video_bg(V_LUCI), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, '2026', ['Otto tavole,', 'da giugno a ottobre.'], size=56,
                         tagline='Le date con pochi posti si chiudono prima: conviene scrivere appena si sa di esserci.')]),
        t.one([calendario(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    luoghi = [('pexels-17086146.jpg', 'Il giardino sotto la pergola'), ('pexels-19987294.jpg', 'Il cortile della cascina'),
              ('pexels-36989618.jpg', 'L’uliveto'), ('pexels-32179374.jpg', 'La tavola lunga in giardino')]
    out.append(t.section([
        t.one([K.sechead(t, 'I luoghi', ['Posti che non sono', 'ristoranti.'], size=56,
                         tagline='Aie, cortili, uliveti, spiagge: chi ci ospita mangia con noi.')]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5')], wm='1-4', ws='1-2') for f, alt in luoghi], layout='25-25-25-25', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Come', 'funziona.', [
        ('Dove si mangia esattamente?', 'Il luogo esatto arriva per email tre giorni prima, con la mappa: è casa di '
         'qualcuno, e così resta tranquilla fino alla sera della cena.'),
        ('E se piove?', 'Ogni posto ha un piano coperto: un fienile, una veranda, una cantina. Non abbiamo mai annullato.'),
        ('Si può venire da soli?', 'Sì, la metà degli ospiti arriva da sola: la tavola è unica, ci si siede vicini.'),
        ('Allergie e intolleranze?', 'Scrivetele nella prenotazione: il menu si cambia per voi, senza un piatto diverso.'),
    ], 'te-faq'))
    out.append(K.pannello(t, 'pexels-12932171.jpg', 'Prenota', 'Scegliete la tappa, <em>al resto pensiamo noi.</em>',
                          'Acconto di 20 € a persona, il resto la sera della cena.', 'Prenota un posto', PRENOTA, overlay=40))
    return out


def build_menu():
    t = Tpl(ID, 'menu')
    out = [K.intro(t, 'Il menu', ['Cinque portate,', 'un luogo.'],
                   'Il menu cambia a ogni tappa: lo scrivo dopo il sopralluogo, con quello che quel posto e i suoi vicini mi '
                   'danno quella settimana.', bg=K.video_bg(V_PIATTO), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'L’ordine', ['Sempre lo stesso ordine,', 'mai gli stessi piatti.'], size=56)]),
        t.one([portate(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dalla brace', ['Piatti di', 'tappe passate.'], size=56,
                         tagline='Trascinate o lasciate scorrere: ogni piatto è stato fatto una sera sola.')]),
        t.one([K.coverflow(t, [('pexels-18426522.jpg', 'Carne sulla brace con le fiamme', 'La costata', 'Sila'),
                               ('pexels-6111928.jpg', 'Carne bianca con funghi ed erbe', 'Carne e funghi', 'Langhe'),
                               ('pexels-29101361.jpg', 'Verdure in un piatto bianco', 'L’orto', 'Colli Euganei'),
                               ('pexels-24186303.jpg', 'Piccoli assaggi gialli con erbe', 'Gli assaggi', 'Conero'),
                               ('pexels-29259652.jpg', 'Un piatto con la salsa scura su marmo', 'Il fondo bruno', 'Val d’Orcia'),
                               ('pexels-5779781.jpg', 'Le fiamme sulla griglia', 'Il fuoco', 'Dolomiti')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_FUOCO, 'Il sopralluogo', 'Il menu lo scrive <em>il posto.</em>',
                             'Una settimana prima vado sul posto: guardo l’orto, parlo col pescatore, assaggio il vino. Il '
                             'menu lo scrivo in macchina, tornando.', overlay=40))
    return out


def build_chef():
    t = Tpl(ID, 'chi-sono')
    out = [K.intro(t, 'Chi sono', ['Elia Mancuso,', 'cuoco senza indirizzo.'],
                   'Dodici anni nelle cucine degli altri, da Milano a Copenaghen. Poi la voglia di cucinare dove le cose nascono.',
                   bg=K.photo_bg('pexels-5779781.jpg'), size=96)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-32224391.jpg', 'Elia Mancuso con la giacca da cuoco nera', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'La storia', ['Una tavola pieghevole', 'e un furgone.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Ho cominciato lavando pentole a sedici anni. Poi le brigate, i ristoranti con le stelle, il '
                                'catering. Nel 2021 ho capito che la parte che mi piaceva di più era il momento in cui la '
                                'gente si siede insieme: ho tolto tutto il resto. Adesso ho una tavola da diciotto, due '
                                'casse di attrezzi e un calendario.', size=17),
                      K.specs(t, [('Cucino dal', '2009'), ('Brigate', 'Milano, Lione, Copenaghen'),
                                  ('Tavola errante dal', '2021'), ('Tappe fatte', '112')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Elia è arrivato una settimana prima, ha guardato il nostro orto e ha detto: faccio tutto con questo. La sera '
           'della cena il nostro cortile non sembrava più casa nostra, ma era più nostra di prima.', 'Paola e Gianni',
        'Hanno ospitato la tappa delle Langhe')])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_SPIAGGIA, 'Ospitate una tappa', 'Avete un posto <em>speciale?</em>',
                          'Una vigna, un cortile, una spiaggia: se volete ospitare una tavola scrivetemi. Chi ospita mangia gratis.',
                          'Scrivetemi', PRENOTA + '#richiesta', overlay=40, video=True))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Un posto', 'alla prossima tavola.'],
                   'Scegliete la tappa e quanti siete. Vi rispondo entro un giorno con la conferma e il link per l’acconto.',
                   bg=K.photo_bg('pexels-5638816.jpg'), size=96)]
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'La tappa', 'tappa', True, opzioni='\n'.join('%s · %s' % (d, l) for d, l, c, p in DATE)),
             K.campo('select', 'Quanti siete', 'persone', True, opzioni='1\n2\n3\n4\n5\n6'),
             K.campo('textarea', 'Allergie, intolleranze, cose da sapere', 'note', larghezza='1-1',
                     segnaposto='Anche se è una cosa piccola: il menu si cambia per voi.')]
    out.append(K.sezione_modulo(t, 'richiesta', 'La richiesta', ['Scrivetemi,', 'vi tengo il posto.'],
                                'I posti si tengono per 48 ore: poi arriva il link per l’acconto di 20 € a persona.', VOCI,
                                campi, 'Tavola Errante — prenotazione', 'Tenetemi il posto',
                                'Ricevuta. Vi scrivo entro un giorno con la conferma.'))
    out.append(K.domande(t, 'Prima', 'di prenotare.', [
        ('L’acconto si restituisce?', 'Sì, fino a sette giorni prima. Dopo si può passare il posto a qualcun altro.'),
        ('I bambini?', 'Sono benvenuti alle tappe in campagna: pagano metà e hanno il loro menu.'),
        ('Si può regalare un posto?', 'Sì: un buono per una tappa a scelta, valido un anno.'),
    ], 'te-faq-p'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · tappa sbagliata', color=CANDELA, align='center'),
                       K.headline(t, 'Qui la tavola <em>non c’è.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc']),
                       K.text(t, 'La strada era giusta, il posto no. Torniamo al calendario.', size=17, align='center',
                              color=mix(C['chiaro'], 84)),
                       K.button(t, 'Le prossime tappe', CENE, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BRACE, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'La tavola che si apparecchia',
    ['chef', 'chef privato', 'cene', 'pop-up', 'supper club', 'eventi', 'scuro', 'slider pro'],
    'La pagina di un cuoco senza ristorante, nero di brace e luce di candela: in apertura la tavola lunga vista '
    'dall’alto sul video della vigna, che si apparecchia mentre si scorre, coi piatti che arrivano uno dopo l’altro '
    'e le candele che si accendono; il calendario delle tappe, il menu in cinque portate, lo chef e la prenotazione.',
    [('Le cene', CENE), ('Il menu', MENU), ('Chi sono', CHEF), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('le-cene', 'Le cene', 'le-cene'), ('menu', 'Il menu', 'menu'), ('chi-sono', 'Chi sono', 'chi-sono'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Karla\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Vollkorn\', \'Georgia\', serif',
    '\'DM Mono\', ui-monospace, Menlo, monospace', ['Vollkorn', 'Karla', 'DM Mono'], corsivo_di=['Vollkorn'],
    peso_titoli='500', raggio=4, grana=4, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'brace'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-cene.json': build_cene(), 'menu.json': build_menu(), 'chi-sono.json': build_chef(),
                        'prenota.json': build_prenota(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
