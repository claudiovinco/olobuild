# -*- coding: utf-8 -*-
"""Genera il tema «Fondente» (id fondente): una cioccolateria «dalla fava alla tavoletta» a Modica e Torino, tavolette
monorigine, praline, cioccolata calda e scatole regalo da comporre, con il negozio online.

Tema scuro e caldo: cacao, caramello, il rosa della polvere di cacao e la crema. Titoli in Gloock, testo in Readex
Pro, dati in DM Mono.

Apertura: Slider Pro «la vetrina», alto 16:9. Il prodotto in una finestra ad arco che si inclina in 3D verso il
puntatore; sotto, cinque praline in fila che girano su sé stesse in 3D una dopo l'altra, come un'onda, e ogni tanto
mostrano il rovescio. Una diapositiva per famiglia, a linguette: tavolette, praline, cacao, cioccolata calda.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'fondente'
NAME = 'Fondente'

HEX = {
    'primary': '#D6A066', 'primary_contrast': '#150C08', 'secondary': '#8A4B2C', 'secondary_contrast': '#FFFFFF',
    'accent': '#E8B8A4', 'dark': '#0B0605', 'light': '#F6EDE3', 'muted': '#1D120D', 'muted_contrast': '#F1E6DA',
    'text': '#F1E6DA', 'text_muted': '#B49E8D', 'background': '#130B08', 'border': '#33231A', 'link': '#D6A066',
}

HOME, TAVOLETTE, LAB, REGALI = '{{PAGINA:home}}', '{{PAGINA:le-tavolette}}', '{{PAGINA:il-laboratorio}}', '{{PAGINA:regali}}'
MAIL, PHONE, PHONE_TEL = 'bottega@fondente.example', '+39 0932 000 718', 'tel:+390932000718'
LAT, LNG = '36.8586', '14.7609'

V_STAMPI, V_COLATA, V_TEMPERA, V_COLTELLO, V_CABOSSIDE, V_GOCCE = (
    'pexels-video-4458588', 'pexels-video-4458591', 'pexels-video-4458596', 'pexels-video-5152004', 'pexels-video-8505961',
    'pexels-video-4061791')
PHOTOS = [
    'pexels-4113365.jpg', 'pexels-6167345.jpg', 'pexels-918328.jpg', 'pexels-65882.jpg', 'pexels-6167336.jpg',
    'pexels-66234.jpg', 'pexels-13278153.jpg', 'pexels-15550424.jpg', 'pexels-18958634.jpg', 'pexels-16120253.jpg',
    'pexels-32597040.jpg', 'pexels-8900912.jpg', 'pexels-37062650.jpg', 'pexels-3635143.jpg', 'pexels-30741485.jpg',
    'pexels-4051127.jpg', 'pexels-5060464.jpg', 'pexels-35583855.jpg', 'pexels-36572429.jpg', 'pexels-10313341.jpg',
    'pexels-352785.jpg', 'pexels-20856302.jpg', 'pexels-39850334.jpg', 'pexels-39850255.jpg', 'pexels-28869126.jpg',
    'pexels-8902301.jpg', 'pexels-8902220.jpg', 'pexels-18092596.jpg', 'pexels-26598636.jpg', 'pexels-8093382.jpg',
    'pexels-34567208.jpg', 'pexels-14356266.jpg', 'pexels-22703386.jpg', 'pexels-34567226.jpg', 'pexels-37439237.jpg',
    'pexels-28697846.jpg', 'pexels-28146830.jpg',
]
VIDEOS = [V_STAMPI, V_COLATA, V_TEMPERA, V_COLTELLO, V_CABOSSIDE, V_GOCCE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True)
C = K.c
CARAMELLO, ROSA = C['acc'], C['acc2']
TITOLI, MONO = 'Gloock', 'DM Mono'
VOCI = [('map-pin', 'Corso Umberto 140 · Modica', REGALI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Fondente', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Fondente', 'Cioccolato dalla fava alla tavoletta a Modica dal 2014: cacao comprato direttamente da '
                   'sei cooperative, tostato e raffinato da noi, solo zucchero di canna. Spediamo in tutta Europa, al fresco.',
                   [('Le tavolette', TAVOLETTE), ('Il laboratorio', LAB), ('Regali', REGALI), ('Il club', REGALI + '#club'),
                    ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Martedì — domenica 10 — 13 e 16 — 20<br>Le spedizioni partono il lunedì e il mercoledì',
                   VOCI, '© 2026 Fondente srl · Modica · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=150)


FAMIGLIE = [  # linguetta, occhiello, riga 1, riga 2, testo, foto dell'arco, foto di sfondo, prezzo
    ('Tavolette', '01 · le tavolette', 'Sei origini,', 'due ingredienti.', 'Cacao e zucchero di canna, nient’altro. Ogni '
     'tavoletta viene da una sola piantagione: il Perù sa di frutti rossi, il Madagascar di agrumi.', 'pexels-65882.jpg',
     'pexels-4113365.jpg', 'da 6,50 €'),
    ('Praline', '02 · le praline', 'Ventiquattro praline,', 'fatte stamattina.', 'Ganache al mandarino tardivo di Ciaculli, '
     'gianduia alla nocciola tonda, caramello al sale di Trapani. Si mangiano entro tre settimane.', 'pexels-15550424.jpg',
     'pexels-36572429.jpg', '1,80 € · al pezzo'),
    ('Cacao', '03 · il cacao', 'Dalla fava', 'alla tavoletta.', 'Compriamo il cacao direttamente dalle cooperative, lo '
     'tostiamo, lo sgusciamo e lo raffiniamo per settantadue ore. Ogni passaggio, qui.', 'pexels-30741485.jpg',
     'pexels-32597040.jpg', '6 cooperative'),
    ('Cioccolata', '04 · d’inverno', 'La cioccolata calda,', 'densa come una volta.', 'Settanta per cento, latte intero e '
     'un pizzico di cannella. Da bere al banco o da portare a casa nella busta da sciogliere.', 'pexels-22703386.jpg',
     'pexels-28869126.jpg', '4 € · la tazza'),
]
PRALINE = ['pexels-66234.jpg', 'pexels-10313341.jpg', 'pexels-18958634.jpg', 'pexels-28697846.jpg', 'pexels-37439237.jpg']


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, arco, sfondo, prezzo) in enumerate(FAMIGLIE):
        n = i + 1
        sl.append(K.diapositiva('fo-%d' % n, K.sfondo_video([V_COLATA, V_TEMPERA, V_STAMPI, V_CABOSSIDE][i], overlay=0.42,
                                                            colore=C['fondo']), [
            # il pannello di vetro scuro a destra, col testo dentro: il video del cioccolato resta tutto in vista
            K.livello('fo-v%d' % n, tipo='shape', x=53, y=11, w=41, h=60, bgColor=mix(C['sala'], 74), borderRadius=24,
                      borderWidth=1, borderColor=mix(CARAMELLO, 30), backdropBlur=16, entra='fadeInRight', ritardo=150,
                      durata=1000, esce='fadeOut',
                      boxShadow={'x': 0, 'y': 30, 'blur': 70, 'spread': -20, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'x': 4, 'y': 8.5, 'width': 92, 'height': 45}, 'tablet': {'y': 13.5}}),
            K.livello('fo-o%d' % n, contenuto=occ, x=56.5, y=16.5, w=35, size=12, peso='400', colore=CARAMELLO, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=400,
                      responsive={'mobile': {'fontSize': m(11), 'y': 11.5, 'x': 9, 'width': 82}, 'tablet': {'y': 19}}),
            K.livello('fo-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=56.5, y=20.5, w=35, size=45, peso='400',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='fadeIn', ritardo=500, durata=1200,
                      charAnim={'stagger': 26, 'offsetY': 30, 'blur': 8, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(28), 'y': 14, 'x': 9, 'width': 82}, 'tablet': {'y': 23}}),
            K.livello('fo-i%d' % n, contenuto=r2, tag='p', x=56.5, y=27.5, w=35, size=45, peso='400', colore=CARAMELLO,
                      fontFamily=TITOLI, lineHeight=1.04, entra='fadeIn', ritardo=800, durata=1200,
                      charAnim={'stagger': 26, 'offsetY': 30, 'blur': 8, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(28), 'y': 18.3, 'x': 9, 'width': 82}, 'tablet': {'y': 31.3}}),
            K.livello('fo-s%d' % n, contenuto=testo, x=56.5, y=38, w=33, size=16, peso='400', colore=mix(C['chiaro'], 82),
                      lineHeight=1.65, ritardo=1100,
                      responsive={'mobile': {'fontSize': m(14), 'y': 24.5, 'x': 9, 'width': 82}, 'tablet': {'width': 35, 'y': 40.5}}),
            K.livello('fo-b%d' % n, tipo='button', contenuto='Al negozio', x=56.5, y=59.5, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=CARAMELLO, padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=10, ritardo=1400, buttonUrl=TAVOLETTE, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 45.5, 'x': 9, 'fontSize': m(11)}, 'tablet': {'y': 62}}),
            K.livello('fo-p%d' % n, contenuto=prezzo, x=68, y=60.2, w='auto', size=12, peso='400', colore=C['chiaro'],
                      fontFamily=MONO, padding=12, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 40),
                      entra='popUpBack', ritardo=1650,
                      responsive={'mobile': {'y': 46.1, 'x': 48, 'fontSize': m(11)}, 'tablet': {'x': 71, 'y': 62.7}}),
        ], durata=8000, tab=tab))
    # la vetrina in fondo: cinque praline che girano in 3D una dopo l'altra, come un'onda
    globali = [K.livello('fo-vassoio', tipo='shape', x=6, y=76, w=88, h=15, bgColor=mix(C['sala'], 62), borderRadius=999,
                         borderWidth=1, borderColor=mix(CARAMELLO, 30), backdropBlur=10, entra='fadeInUp', ritardo=600,
                         responsive={'mobile': {'x': 6, 'y': 58.5, 'width': 88, 'height': 7}, 'tablet': {'y': 77}})]
    for k, foto in enumerate(PRALINE):
        x = 14 + k * 16.5
        globali.append(K.livello('fo-pr%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=x, y=78.7, w=5.4,
                                 h=9.6, borderRadius=999, borderWidth=2, borderColor=mix(CARAMELLO, 60),
                                 boxShadow={'x': 0, 'y': 10, 'blur': 20, 'spread': -6, 'color': mix(C['nero'], 80)},
                                 timeline=K.tl(x, 78.7, [(0, {'rotationY': 0.01, 'opacity': 0, 'scale': 0.6}, 'back.out'),
                                                         (700 + 150 * k, {'rotationY': 0.01, 'opacity': 100, 'scale': 1}, 'power2.inOut'),
                                                         (2600 + 300 * k, {'rotationY': 0.01, 'opacity': 100, 'scale': 1}, 'power2.inOut'),
                                                         (3600 + 300 * k, {'rotationY': 360, 'opacity': 100, 'scale': 1.08}, 'power2.inOut'),
                                                         (6400, {'rotationY': 360, 'opacity': 100, 'scale': 1})], 6400),
                                 responsive={'mobile': {'x': 9.5 + k * 17.5, 'y': 59.1, 'width': 13, 'height': 5.9}, 'tablet': {'y': 79.7}}))
    globali.append(K.livello('fo-vl', contenuto='La vetrina di oggi · cinque praline', x=6, y=69.5, w='auto', size=10,
                             peso='400', colore=C['chiaro'], fontFamily=MONO, letterSpacing=2,
                             bgColor=mix(C['sala'], 94), padding=9, borderRadius=999,
                             textTransform='uppercase', entra='fadeIn', ritardo=1500,
                             responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='stack',
                       durata_trans=1100, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=CARAMELLO, navColor=C['chiaro'])


ORIGINI = [  # nome, sotto, testo, foto
    ('Perù · Piura', '70% · bianco criollo', 'Frutti rossi e un fondo di miele. Il cacao bianco di Piura è fra i più rari al '
     'mondo: la cooperativa ne raccoglie quattro tonnellate l’anno.', 'pexels-65882.jpg'),
    ('Madagascar · Sambirano', '72% · trinitario', 'Agrumi, lampone, una punta acida che pulisce la bocca. La tavoletta più '
     'vivace della vetrina.', 'pexels-918328.jpg'),
    ('Ecuador · Esmeraldas', '75% · nacional', 'Fiori e frutta secca: è il cacao più antico, quello che i Maya bevevano.',
     'pexels-6167345.jpg'),
    ('Ghana · Ashanti', '80% · forastero', 'Profondo, terroso, poco zucchero: per chi vuole sentire il cacao e basta.',
     'pexels-6167336.jpg'),
    ('Modica · al latte', '45% · col latte di Ragusa', 'Il nostro al latte: caramello e nocciola, senza vaniglia. Il preferito '
     'dei bambini.', 'pexels-4113365.jpg')]


def origini(t):
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '%02d' % (k + 1), 'image': K.img(f), 'link_url': TAVOLETTE,
                   'color': CARAMELLO} for k, (n, s, d, f) in enumerate(ORIGINI)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': C['tenue'],
        'number_hover_color': CARAMELLO, 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': CARAMELLO,
        'name_size': 46, 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': ROSA, 'sub_size': 12,
        'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 24, 'hover_indent': 28,
        'hover_bg': mix(CARAMELLO, 7), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image', 'peek_width': 240,
        'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Fondente · Modica', ['Dalla fava', 'alla tavoletta.'], size=64, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Il cacao arriva in sacchi di juta da sei cooperative che conosciamo per nome. Lo tostiamo a '
                                'bassa temperatura, lo sgusciamo, lo raffiniamo per tre giorni con lo zucchero di canna. Poi '
                                'lo temperiamo a mano sul marmo, come si fa a Modica da cinquecento anni.', size=17,
                             color=C['testo']),
                      K.button(t, 'Il laboratorio', LAB, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('2', 'Ingredienti in una tavoletta'), ('72 h', 'Di raffinazione'), ('6', 'Cooperative di cacao'),
                            ('24', 'Praline in vetrina')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le origini', ['Cinque tavolette,', 'cinque paesi.'], size=60,
                         tagline='Passate su un’origine: la tavoletta si affaccia. Ognuna sa di un posto diverso.')]),
        t.one([origini(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_COLATA, 'La temperatura', 'Trentuno gradi, <em>e lucida.</em>',
                             'Il cioccolato si scalda, si raffredda sul marmo e si riscalda di un grado: solo così i '
                             'cristalli del burro di cacao si mettono in fila e la tavoletta fa «crac».', overlay=50))
    out.append(t.section([
        t.one([K.sechead(t, 'In vetrina', ['Le praline', 'di questa settimana.'], size=60,
                         tagline='Trascinate o lasciate scorrere. Cambiano col mercato: questa settimana mandarino e fichi.')]),
        t.one([K.coverflow(t, [
            ('pexels-15550424.jpg', 'Praline in pirottini scuri', 'Ganache al mandarino', 'Ciaculli · 1,80 €'),
            ('pexels-18958634.jpg', 'Tartufi al cioccolato in fila', 'Tartufo al rum', 'Rum agricolo · 1,80 €'),
            ('pexels-37439237.jpg', 'Praline decorate a mano', 'Caramello e sale', 'Sale di Trapani · 1,80 €'),
            ('pexels-10313341.jpg', 'Tartufi in una ciotola', 'Tartufo al cacao', 'Il classico · 1,60 €'),
            ('pexels-28697846.jpg', 'Tartufi su un vassoio', 'Gianduia', 'Nocciola tonda · 1,80 €'),
            ('pexels-35583855.jpg', 'Dolci al cioccolato con decorazioni', 'Mignon al pistacchio', 'Bronte · 2,20 €'),
            ('pexels-16120253.jpg', 'Cioccolatini su un vassoio di cristallo', 'La scatola da festa', '24 praline · 39 €')],
            altezza=420, larghezza=330)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'La tavoletta di Piura l’ho assaggiata alla cieca accanto a tre cioccolati famosi. Ho riconosciuto la sua dal '
                 'profumo, prima ancora di metterla in bocca.', 'author_name': 'Alessandra P.',
        'author_role': 'Club della tavoletta dal 2024', 'avatar': '', 'rating': '5', 'text_color': C['testo'],
        'star_color': CARAMELLO, 'author_color': C['tenue'], 'quote_accent_color': CARAMELLO, 'quote_font': 'heading',
        'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': ROSA}, style={})])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_STAMPI, 'Regali', 'La scatola <em>la componete voi.</em>',
                          'Da sei a ventiquattro praline, scelte una per una, nella scatola di cartone grezzo col nastro. '
                          'Ci mettiamo anche il biglietto scritto a mano.', 'Componi la scatola', REGALI + '#scatola',
                          overlay=58, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Fondente',
                page_intro_caption='Cioccolato · Modica', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


TAV = [  # titolo, foto, categoria, descrizione, percentuale
    ('Perù · Piura', 'pexels-65882.jpg', 'Fondenti', 'Bianco criollo, frutti rossi · 8,50 €', '70%'),
    ('Madagascar', 'pexels-918328.jpg', 'Fondenti', 'Agrumi e lampone · 7,50 €', '72%'),
    ('Ecuador', 'pexels-6167345.jpg', 'Fondenti', 'Fiori e frutta secca · 7,50 €', '75%'),
    ('Ghana', 'pexels-6167336.jpg', 'Fondenti', 'Terroso, intenso · 6,50 €', '80%'),
    ('Modica al latte', 'pexels-4113365.jpg', 'Al latte', 'Latte di Ragusa, caramello · 6,50 €', '45%'),
    ('Arancia di Ribera', 'pexels-918328.jpg', 'Con aggiunte', 'Scorze candite da noi · 7,50 €', '70%'),
    ('Mandorla di Noto', 'pexels-4051127.jpg', 'Con aggiunte', 'Mandorle tostate intere · 7,50 €', '70%'),
    ('Cacao puro', 'pexels-39850255.jpg', 'Fondenti', 'Senza zucchero, per i coraggiosi · 6,50 €', '100%'),
]


def build_tavolette():
    t = Tpl(ID, 'le-tavolette')
    out = [K.intro(t, 'Le tavolette', ['Otto tavolette,', 'lavorate a freddo.'],
                   'Tutte da 75 grammi, incartate a mano nella carta di riso. Cercate per origine, percentuale o gusto.',
                   bg=K.photo_bg('pexels-4113365.jpg', overlay=66), overlay_text=True)]
    griglia = t.classic('portfolio', {
        'source': 'manual', 'layout': 'grid', 'columns': 4, 'gap': 24, 'image_ratio': '4:5', 'image_fit': 'cover',
        'hover_effect': 'zoom', 'animation': 'fade', 'caption_position': 'below', 'enable_search': True,
        'search_placeholder': 'Cerca: Perù, 80%, arancia…', 'show_title': True, 'show_category': True, 'show_excerpt': True,
        'font_family': 'heading', 'font_weight': '400', 'title_color': C['testo'], 'text_color': C['tenue'],
        'accent_color': CARAMELLO, 'border_radius': 10, 'year_stamp': True, 'index_numbering': False,
        'items': [{'title': ti, 'image_url': K.img(f), 'category': cat, 'description': d, 'year': pc,
                   'link_url': REGALI + '#ordina'} for ti, f, cat, d, pc in TAV],
        'columns_tablet': '2', 'columns_mobile_landscape': '2'}, style={})
    out.append(t.section([t.one([griglia])], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    scheda = t.classic('schedatecnica', {
        'codice': 'FD-PIU-70', 'nome': 'Perù · Piura 70%', 'etichetta': 'La più premiata',
        'descrizione': 'Cacao bianco criollo della cooperativa Norandino, a Piura. Tostato a 118 gradi per diciotto minuti, '
                       'raffinato settantadue ore. Profumo di frutti rossi e miele, finale lungo.',
        'immagine': K.img('pexels-65882.jpg'), 'titolo_specifiche': 'La carta d’identità',
        'specifiche': [{'id': 'fs-%d' % k, 'gruppo': g, 'nome': a, 'valore': b, 'unita': u} for k, (g, a, b, u) in enumerate([
            ('Cacao', 'Varietà', 'Criollo bianco', ''), ('Cacao', 'Origine', 'Piura, Perù', ''), ('Cacao', 'Percentuale', '70', '%'),
            ('Lavorazione', 'Tostatura', '118 °C · 18 min', ''), ('Lavorazione', 'Raffinazione', '72', 'ore'),
            ('Tavoletta', 'Peso', '75', 'g'), ('Tavoletta', 'Ingredienti', 'cacao, zucchero di canna', ''),
            ('Tavoletta', 'Prezzo', '8,50', '€')])],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'Commercio diretto · senza glutine · vegano',
        'cta_testo': 'Ordina', 'cta_url': REGALI + '#ordina', 'cta_codice': False, 'cta_parametro': '',
        'dati_strutturati': True, 'accento': CARAMELLO, 'sfondo_scheda': C['sala'], 'colore_linee': C['filo'],
        'righe_alternate': False, 'nome_font_size': 44, 'nome_font_weight': '400', 'nome_color': C['testo'],
        'descrizione_color': C['tenue'], 'gruppo_color': CARAMELLO, 'voce_color': C['tenue'], 'valore_color': C['testo'],
        'unita_color': C['tenue'], 'codice_color': ROSA, 'raggio': radius(10), 'raggio_pulsante': radius(10),
        'padding_scheda': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40}, 'img_ratio': '4/5', 'img_fit': 'cover',
        'foto_posizione': 'sinistra', 'larghezza_foto': 42, 'gap': 48, 'colonne_specifiche': 1}, style={})
    out.append(t.section([t.one([scheda])], bg=solid(C['sala']), padding='large', width='large',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_COLTELLO, 'L’assaggio', 'Spezzate, <em>annusate, aspettate.</em>',
                             'Si assaggia a temperatura di stanza: si spezza, si annusa, si lascia sciogliere sulla lingua '
                             'senza masticare. I profumi arrivano dopo dieci secondi.', overlay=55))
    out.append(K.domande(t, 'Prima', 'di ordinare.', [
        ('Spedite d’estate?', 'Sì, in scatola termica con il ghiaccio secco, da giugno a settembre. Il lunedì e il mercoledì, '
                              'per non farlo restare in magazzino il fine settimana.'),
        ('C’è latte nei fondenti?', 'No, ma li lavoriamo sulle stesse macchine del cioccolato al latte: tracce possibili.'),
        ('Quanto si conserva?', 'Le tavolette un anno, al fresco e al buio. Le praline tre settimane.'),
        ('Fate ingrosso per negozi e ristoranti?', 'Sì, con il listino professionale: scriveteci.'),
    ], 'ta', bg=False))
    return out


def build_lab():
    t = Tpl(ID, 'il-laboratorio')
    out = [K.intro(t, 'Il laboratorio', ['Sei passaggi,', 'tutti qui.'],
                   'Dalla fava alla tavoletta, senza comprare cioccolato già fatto. Dietro la vetrina c’è la tostatrice, la '
                   'raffinatrice e il marmo per temperare.', bg=K.photo_bg('pexels-32597040.jpg', overlay=64), overlay_text=True)]
    fasi = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': CARAMELLO, 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': CARAMELLO, 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('01', 'La fava', 'pexels-8900912.jpg', 'La scelta', 'Fave', 'fermentate', 'Le cooperative fermentano il cacao sei '
             'giorni sotto le foglie di banano: lì nasce il profumo.', '6', 'giorni di fermentazione'),
            ('02', 'Il fuoco', 'pexels-32597040.jpg', 'La tostatura', 'Piano', 'e a lungo', 'Diciotto minuti a 118 gradi, poi '
             'si sgusciano le fave e resta la granella.', '118 °C', 'per 18 minuti'),
            ('03', 'Il marmo', 'pexels-5060464.jpg', 'La temperatura', 'A mano', 'sul marmo', 'Tre giorni di raffinazione, poi '
             'il cioccolato si tempera sul marmo e si cola negli stampi.', '72 h', 'di raffinazione')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': CARAMELLO,
        'counter_font_family': 'heading', 'counter_size': 84, 'counter_color': CARAMELLO, 'counter_italic': False,
        'counter_weight': '400', 'tag_color': C['chiaro'], 'pre_title_color': ROSA, 'title_font_family': 'heading',
        'title_size': 32, 'title_weight': '400', 'title_color': C['chiaro'], 'title_accent_color': CARAMELLO,
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'thermometer', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(10)}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Come nasce', ['Dalla cabossa', 'allo stampo.'], size=56)]), t.one([fasi])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_TEMPERA, 'La temperatrice', 'Il cioccolato <em>gira tutto il giorno.</em>',
                             'Nella temperatrice il cioccolato resta a trentuno gradi, sempre in movimento: si prende con '
                             'il mestolo e si cola negli stampi delle praline.', overlay=55))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Tre mani', 'sul marmo.'], size=56)]),
        K.squadra(t, [
            ('pexels-26598636.jpg', 'Davide Neri', 'Cioccolatiere', 'Ha imparato a Modica e a Torino. Sceglie il cacao '
             'andando dalle cooperative, una volta all’anno.', '50% 25%'),
            ('pexels-8902220.jpg', 'Mei Lin Zhou', 'Praline e ganache', 'Pasticciera, inventa le praline della settimana con '
             'la frutta del mercato.', '50% 25%'),
            ('pexels-18092596.jpg', 'Franco Ragusa', 'La bottega', 'Sta al banco da quando abbiamo aperto. Vi fa assaggiare '
             'tutto, anche quello che non volete comprare.', '50% 30%')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_CABOSSIDE, 'Le cooperative', 'Il cacao <em>comprato per nome.</em>',
                          'Paghiamo il cacao il doppio del prezzo di borsa, direttamente a chi lo coltiva. Ogni tavoletta '
                          'porta scritto il nome della cooperativa.', 'Le tavolette', TAVOLETTE, overlay=58, video=True))
    return out


def scatola(t):
    def opz(i, g, nome, desc, prezzo, pred=False, badge='', mx=1):
        return {'id': i, 'gruppo': g, 'nome': nome, 'descrizione': desc, 'prezzo': prezzo, 'immagine': '', 'badge': badge,
                'predefinita': pred, 'punti': '', 'min': 0, 'max': mx}
    return t.classic('configuratore', {
        'gruppi': [{'id': 'misura', 'titolo': 'La scatola', 'tipo': 'uno', 'obbligatorio': True,
                    'aiuto': 'Il prezzo comprende la scatola, il nastro e il biglietto scritto a mano.'},
                   {'id': 'praline', 'titolo': 'Le praline', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''},
                   {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': [opz('sei', 'misura', 'Da sei', 'Un pensiero.', 12, True), opz('dodici', 'misura', 'Da dodici', 'Per due.', 22,
                    badge='La più regalata'), opz('ventiquattro', 'misura', 'Da ventiquattro', 'Per una festa.', 39),
                    opz('mandarino', 'praline', 'Ganache al mandarino', 'Tardivo di Ciaculli.', 0),
                    opz('caramello', 'praline', 'Caramello e sale', 'Sale di Trapani.', 0),
                    opz('gianduia', 'praline', 'Gianduia', 'Nocciola tonda del Piemonte.', 0),
                    opz('rum', 'praline', 'Tartufo al rum', 'Rum agricolo.', 0),
                    opz('pistacchio', 'praline', 'Pistacchio di Bronte', 'Il più chiesto.', 0),
                    opz('tavoletta', 'extra', 'Una tavoletta Piura', 'Nella scatola, sotto il nastro.', 8.5),
                    opz('cioccolata', 'extra', 'Busta di cioccolata calda', 'Per quattro tazze.', 9)],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'La vostra scatola', 'nota_prezzo': 'Spedizione 7 € · gratis da 50 €',
        'periodo': '', 'riepilogo': True, 'titolo_riepilogo': 'La vostra scatola', 'testo_azzera': 'Ricomincia',
        'testo_pulsante': 'Ordina la scatola', 'link_azione': REGALI + '?scatola={configurazione}#ordina',
        'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': C['sala'], 'colore_scelta': CARAMELLO,
        'colore_testo': C['testo'], 'colore_tenue': C['tenue'], 'colore_prezzo': CARAMELLO, 'sfondo_riepilogo': C['sala'],
        'sfondo_badge': ROSA, 'colore_badge': C['nero'], 'raggio_scheda': radius(10), 'gruppo_color': C['testo'],
        'gruppo_font_weight': '400', 'totale_color': CARAMELLO, 'totale_font_weight': '400'}, style={})


def build_regali():
    t = Tpl(ID, 'regali')
    out = [K.intro(t, 'Regali', ['Una scatola,', 'un biglietto a mano.'],
                   'Componete la scatola, o scegliete il club: una tavoletta nuova ogni mese, con la storia della '
                   'cooperativa.', bg=K.photo_bg('pexels-16120253.jpg', overlay=64), overlay_text=True, size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Componi la scatola', ['Scegliete', 'pralina per pralina.'], size=56,
                         tagline='La misura, i gusti, qualcosa in più: il prezzo si aggiorna e la ordinate subito.')]),
        t.one([scatola(t)]),
    ], padding='large', width='large', html_id='scatola', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il club della tavoletta', ['Una tavoletta', 'al mese.'], size=56,
                         tagline='Ogni mese un’origine diversa, con la scheda di degustazione. Si sospende quando volete.')]),
        K.piani(t, [('Assaggio', '9', 'al mese', 'Una tavoletta da 75 g\nLa scheda dell’origine\nSpedizione compresa', False, ''),
                    ('Intenditore', '19', 'al mese', 'Due tavolette, una in anteprima\nLa scheda e la mappa\nUna pralina in '
                     'regalo\nSpedizione compresa', True, 'La più scelta'),
                    ('Regalo d’un anno', '199', 'una volta sola', 'Dodici mesi di Intenditore\nIl biglietto per chi lo riceve\n'
                     'La prima scatola a Natale', False, '')],
                'Iscriviti', REGALI + '#ordina', nota='Si sospende o si disdice quando volete'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='club', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('text', 'La vostra scatola', 'scatola', param='scatola', larghezza='1-1',
                     segnaposto='Se l’avete composta, la trovate già qui'),
             K.campo('text', 'A chi la mandiamo', 'destinatario', segnaposto='Nome e città'),
             K.campo('date', 'Per quando', 'quando'),
             K.campo('textarea', 'Il biglietto', 'messaggio', larghezza='1-1', segnaposto='Lo scriviamo a mano, con la stilografica.')]
    out.append(K.sezione_modulo(t, 'ordina', 'L’ordine', ['Una scatola,', 'due righe.'],
                                'Vi mandiamo il link per pagare e la data di consegna. Le scatole partono il lunedì e il '
                                'mercoledì.', VOCI, campi, 'Fondente — ordine', 'Ordina', 'Ricevuto! Vi scriviamo entro oggi.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['Sul corso,', 'sotto il duomo.'], size=52, layout='stack', size_mobile=34),
                      K.specs(t, [('Indirizzo', 'corso Umberto 140, Modica'), ('Orari', 'mar — dom 10 — 13 · 16 — 20'),
                                  ('Assaggi', 'sempre, al banco'), ('Visite al laboratorio', 'il sabato alle 11')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fondente · corso Umberto 140', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · vetrina vuota', color=CARAMELLO, align='center'),
                       K.headline(t, 'Questa pagina <em>si è sciolta.</em>', size=88, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=CARAMELLO),
                       K.text(t, 'Troppo vicino al termosifone. Torniamo in bottega.', size=17, align='center',
                              color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna alla vetrina', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_GOCCE, overlay=58), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Negozi online', 'Le praline che girano',
    ['cioccolato', 'cioccolateria', 'praline', 'modica', 'regali', 'e-commerce', 'scuro', 'slider pro', '3d'],
    'Una cioccolateria dalla fava alla tavoletta, cacao e caramello: in apertura il video del cioccolato che cola a '
    'tutta larghezza, il pannello di vetro col racconto e in fondo la vetrina di cinque praline che girano su sé '
    'stesse a onda; le origini che si affacciano, le praline in coverflow 3D, le tavolette con la ricerca e la carta '
    'd’identità, la scatola regalo da comporre e il club della tavoletta.',
    [('Le tavolette', TAVOLETTE), ('Il laboratorio', LAB), ('Regali', REGALI), ('Il club', REGALI + '#club')],
    [('home', 'Home', None), ('le-tavolette', 'Le tavolette', 'le-tavolette'), ('il-laboratorio', 'Il laboratorio', 'il-laboratorio'),
     ('regali', 'Regali', 'regali')],
    HEX, '\'Readex Pro\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Gloock\', \'Georgia\', serif',
    '\'DM Mono\', ui-monospace, Menlo, monospace', ['Gloock', 'Readex Pro', 'DM Mono'], peso_titoli='400', raggio=10,
    grana=6, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # apertura rifatta il 31 ottobre 2026
THEME['cursor']['style'] = 'etichetta'  # cursore: la parola sui link della bottega (31 ott 2026)


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-tavolette.json': build_tavolette(), 'il-laboratorio.json': build_lab(), 'regali.json': build_regali(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
