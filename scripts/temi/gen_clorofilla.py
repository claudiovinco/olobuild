# -*- coding: utf-8 -*-
"""Genera il tema «Clorofilla» (id clorofilla): un negozio di piante da appartamento a Torino, in San Salvario: piante
scelte una per una, il dottore delle piante a domicilio, i laboratori di rinvaso e talee.

Tema chiaro, verde foglia e terracotta. Titoli in Bricolage Grotesque, testo in Be Vietnam Pro, dati in Victor Mono.

Apertura: «la pianta che cresce». Lo slider resta fermo in cima mentre si scorre: in un vaso di terracotta, davanti a un
tondo di video con la foglia bagnata, il fusto sale a pezzi e le foglie si aprono una dopo l'altra, a destra e a
sinistra; alla fine compare il cartellino della pianta. Il titolo e i pulsanti restano a sinistra, scuri sulla carta.
Cursore: le foglie che cadono.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'clorofilla'
NAME = 'Clorofilla'

HEX = {
    'primary': '#2F6B3B', 'primary_contrast': '#FFFFFF', 'secondary': '#B85A33', 'secondary_contrast': '#FFFFFF',
    'accent': '#A7C957', 'dark': '#102016', 'light': '#FFFFFF', 'muted': '#E3EADB', 'muted_contrast': '#1B2A1E',
    'text': '#1B2A1E', 'text_muted': '#556353', 'background': '#F1F4EC', 'border': '#CFDAC6', 'link': '#2F6B3B',
}

HOME, PIANTE, CURA, LABORATORI, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:piante}}', '{{PAGINA:cura}}', '{{PAGINA:laboratori}}',
                                            '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'ciao@clorofilla.example', '+39 011 000 4482', 'tel:+390110004482'
LAT, LNG = '45.0566', '7.6797'

V_FOGLIA, V_ANNAFFIA, V_BANCO, V_NEGOZIO, V_SERRA, V_SPRUZZO = ('pexels-video-7013838', 'pexels-video-6628035',
                                                               'pexels-video-9707289', 'pexels-video-9465801',
                                                               'pexels-video-6508742', 'pexels-video-7013839')
PHOTOS = [
    'pexels-9414406.jpg', 'pexels-9710609.jpg', 'pexels-5942503.jpg', 'pexels-6144357.jpg', 'pexels-1624732.jpg',
    'pexels-37047504.jpg', 'pexels-14723067.jpg', 'pexels-6280451.jpg', 'pexels-13984652.jpg', 'pexels-7424239.jpg',
    'pexels-31578568.jpg', 'pexels-28961744.jpg', 'pexels-16808428.jpg', 'pexels-18864976.jpg', 'pexels-5830878.jpg',
    'pexels-5830881.jpg', 'pexels-38681769.jpg', 'pexels-8737425.jpg', 'pexels-32633628.jpg',
]
VIDEOS = [V_FOGLIA, V_ANNAFFIA, V_BANCO, V_NEGOZIO, V_SERRA, V_SPRUZZO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=16, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
GERMOGLIO, TERRACOTTA = C['acc2'], C['sec']
MONO = 'Victor Mono'
VOCI = [('map-pin', 'Via Madama Cristina 52 · Torino', CONTATTI + '#negozio'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Clorofilla', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Clorofilla', 'Piante da appartamento in San Salvario, scelte una per una. Il dottore delle piante a '
                   'domicilio, i laboratori di rinvaso e talee, i vasi di terracotta di Impruneta.',
                   [('Le piante', PIANTE), ('Il dottore delle piante', CURA), ('I laboratori', LABORATORI),
                    ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Il negozio', 'Da martedì a sabato 10 — 19:30<br>La domenica mattina in primavera', VOCI,
                   '© 2026 Clorofilla · Torino · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')], marchio_size=140, fondo=C['sala'])


# ── L'apertura: la pianta che cresce ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
SEG, NSEG = 62.0, 6                          # i pezzi del fusto (px) e quanti sono
PIANTA = (1000.0, 610.0)                     # la base del fusto sul computer (sul bordo del vaso)
PIANTA_M = (320.0, 1080.0)                   # sul telefono
SM = 0.95                                    # la pianta sul telefono, in scala


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    bx, by = PIANTA
    bmx, bmy = PIANTA_M

    def pezzo(lid, dx, dy, w, h, scala=1.0, **extra):
        """Un pezzo disegnato attorno alla base del fusto: (dx, dy) è l'angolo in alto a sinistra rispetto alla base."""
        a = _pc(bx + dx, by + dy, w, h, W, H)
        b = _pc(bmx + dx * SM * scala, bmy + dy * SM * scala, w * SM * scala, h * SM * scala, WM, HM)
        return K.livello(lid, x=a[0], y=a[1], w=a[2], h=a[3], responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2],
                                                                                     'height': b[3]}}, **extra)
    livelli = [
        # il tondo di video dietro la pianta
        pezzo('cl-sole', -290, -540, 580, 580, tipo='video', videoSrc=K.img(V_FOGLIA + '.mp4'), videoAutoplay=True,
              videoMuted=True, videoLoop=True, borderRadius=999, entra='zoomIn', ritardo=0, durata=1100,
              scrollMove={'inizio': 0, 'fine': 100, 'da': {}, 'a': {'s': 1.1}}),
    ]
    # il fusto: sei pezzi che compaiono uno dopo l'altro, dal basso
    for k in range(NSEG):
        livelli.append(pezzo('cl-fusto%d' % k, -5, -SEG * (k + 1) - 2, 10, SEG + 4, tipo='shape', borderRadius=99,
                             bgColor=C['acc'], entra='fadeIn', ritardo=0, durata=300,
                             scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 30}, {'p': 9 * k, 'o': 0, 'y': 30},
                                                                             {'p': 9 * k + 9, 'o': 100, 'y': 0},
                                                                             {'p': 100, 'o': 100}]}))
    # le foglie: a destra e a sinistra, si aprono dopo il loro pezzo di fusto
    foglie = [(1, -1, 150, 64, 28), (2, 1, 170, 72, -26), (3, -1, 180, 76, 24), (4, 1, 190, 80, -22), (5, -1, 160, 70, 34),
              (5, 1, 150, 66, -38)]
    for j, (k, lato, lw, lh, ang) in enumerate(foglie):
        ya = -SEG * k - lh / 2
        dx = 4 if lato > 0 else -lw - 4
        verde = C['acc'] if j % 2 else mix(C['acc'], 75, GERMOGLIO)
        p0 = 9 * k + 4
        livelli.append(pezzo('cl-foglia%d' % j, dx, ya - 16 * (1 if lato > 0 else 1), lw, lh, tipo='shape',
                             borderRadiusLinked=False, borderRadiusTL=0, borderRadiusTR=lh, borderRadiusBR=0,
                             borderRadiusBL=lh, shapeGradient={'angle': 135 if lato > 0 else 45, 'from': GERMOGLIO,
                                                               'to': verde, 'fromStop': 5},
                             boxShadow={'x': 0, 'y': 10, 'blur': 22, 'spread': -8, 'color': mix(C['nero'], 35)},
                             entra='fadeIn', ritardo=0, durata=300,
                             scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.1, 'r': ang},
                                                                             {'p': p0, 'o': 0, 's': 0.1, 'r': ang},
                                                                             {'p': p0 + 12, 'o': 100, 's': 1, 'r': ang},
                                                                             {'p': 100, 'o': 100, 's': 1, 'r': ang}]}))
    # il vaso di terracotta (davanti al fusto) e il bordo
    livelli += [
        pezzo('cl-vaso', -100, 14, 200, 240, tipo='shape', borderRadiusLinked=False, borderRadiusTL=4, borderRadiusTR=4,
              borderRadiusBR=40, borderRadiusBL=40, shapeGradient={'angle': 90, 'from': mix(TERRACOTTA, 85, C['nero']),
                                                                   'to': TERRACOTTA, 'fromStop': 10},
              entra='fadeInUp', ritardo=200, durata=900),
        pezzo('cl-bordo', -118, -4, 236, 32, tipo='shape', borderRadius=6, bgColor=mix(TERRACOTTA, 90, C['chiaro']),
              boxShadow={'x': 0, 'y': 10, 'blur': 18, 'spread': -6, 'color': mix(C['nero'], 40)}, entra='fadeInUp', ritardo=300,
              durata=900),
    ]
    a = _pc(bx + 130, by - 120, 230, 30, W, H)
    b = _pc(bmx - 120, bmy - 600, 240, 34, WM, HM)
    livelli.append(K.livello('cl-cartellino', contenuto='Monstera · un anno dopo · 32 €', x=a[0], y=a[1], w='auto', size=13,
                             peso='600', colore=C['chiaro'], bgColor=C['acc'], padding=10, borderRadius=999, fontFamily=MONO,
                             entra='fadeIn', ritardo=0,
                             scrollMove={'inizio': 62, 'fine': 80, 'da': {'y': 30, 'o': 0}, 'a': {}},
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(11)}}))
    # il titolo e i pulsanti, a sinistra sulla carta
    livelli += [
        K.livello('cl-o', contenuto='Clorofilla · piante in San Salvario', x=6, y=24, w=44, size=12, peso='600', colore=C['acc'],
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('cl-t', contenuto='Una pianta,<br><span style="color:var(--olo-color-primary)">ci vuole pazienza.</span>',
                  tag='h1', x=6, y=28.5, w=46, size=70, peso='700', colore=C['testo'], lineHeight=1.02, letterSpacing=-2,
                  entra='maskFromBottom', ritardo=300, durata=1100,
                  responsive={'mobile': {'fontSize': m(34), 'y': 10.5, 'x': 7, 'width': 86}}),
        K.livello('cl-s', contenuto='Le scegliamo una per una dai vivai del Piemonte e della Liguria, ve le portiamo a casa e '
                  'vi diciamo come tenerle. Scorrete: cresce.', x=6, y=52, w=33, size=17, peso='400', colore=C['tenue'],
                  lineHeight=1.6, ritardo=800, responsive={'mobile': {'fontSize': m(14), 'y': 20.5, 'x': 7, 'width': 86}}),
        K.livello('cl-b', tipo='button', contenuto='Le piante', x=6, y=69, w='auto', size=14, peso='600', colore=C['chiaro'],
                  bgColor=C['acc'], padding=16, borderRadius=999, ritardo=1100, buttonUrl=PIANTE,
                  hover={'bgColor': C['testo'], 'duration': 250}, responsive={'mobile': {'y': 29, 'x': 7, 'fontSize': m(12)}}),
        K.livello('cl-l', tipo='button', contenuto='Il dottore delle piante', x=15.5, y=69, w='auto', size=14, peso='600',
                  colore=C['testo'], bgColor='transparent', padding=16, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['testo'], 40), ritardo=1250, buttonUrl=CURA,
                  hover={'bgColor': mix(C['testo'], 8), 'borderColor': C['testo'], 'duration': 250},
                  responsive={'tablet': {'x': 19}, 'mobile': {'y': 29, 'x': 37, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('cl-1', {'type': 'color', 'image': '', 'video': '', 'color': C['fondo'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1700, scrollStep=False,
                       globalBackground={'type': 'color', 'color': C['fondo']}, heightMobile={'mode': 'px', 'value': 1400})


PIANTE_LISTA = [  # nome, luce e acqua, descrizione, prezzo, foto
    ('Monstera deliciosa', 'Luce indiretta · acqua ogni 10 giorni', 'Le foglie si bucano crescendo: più luce, più buchi.',
     'da 32 €', 'pexels-6144357.jpg'),
    ('Ficus lyrata', 'Tanta luce · acqua quando la terra è asciutta', 'Non ama essere spostata: trovatele un posto e lasciatela lì.',
     'da 45 €', 'pexels-5942503.jpg'),
    ('Sansevieria', 'Poca luce va bene · acqua una volta al mese', 'La pianta di chi parte spesso. Quasi impossibile da far morire.',
     'da 18 €', 'pexels-7424239.jpg'),
    ('Cactus e succulente', 'Sole pieno · quasi niente acqua', 'Per il davanzale a sud. In vasi di terracotta piccoli.',
     'da 8 €', 'pexels-31578568.jpg'),
    ('Pothos', 'Ovunque · acqua ogni settimana', 'Ricade dalle mensole e si moltiplica con una talea in un bicchiere.',
     'da 14 €', 'pexels-9710609.jpg'),
]


def piante(t):
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': c + ' · ' + p, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': PIANTE,
                   'color': C['acc']} for k, (n, c, d, p, f) in enumerate(PIANTE_LISTA)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': C['acc'],
        'number_hover_color': TERRACOTTA, 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 48, 'name_weight': '700', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['acc'],
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(C['acc'], 6), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 260, 'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': TERRACOTTA}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Clorofilla · dal 2017', ['Trecento piante,', 'nessuna di plastica.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Lin ha aperto il negozio in un ex laboratorio di biciclette, con le vetrine a sud. Le piante '
                                'arrivano ogni martedì dai vivai; quelle che non stanno bene restano in serra finché non si '
                                'riprendono. Ogni pianta esce col suo cartellino: luce, acqua, cosa la fa soffrire.',
                             size=17, color=C['testo']),
                      K.button(t, 'Le piante', PIANTE, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('300', 'Piante in negozio'), ('40', 'Specie'), ('1', 'Dottore delle piante'),
                            ('2', 'Laboratori al mese')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le piante', ['Cinque da cui', 'cominciare.'], size=56,
                         tagline='Passate su una pianta: la foto si affaccia. Sotto, la luce e l’acqua che le servono.')]),
        t.one([piante(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_ANNAFFIA, 'L’acqua', 'Più piante muoiono <em>d’acqua che di sete.</em>',
                             'Il dito nella terra, due centimetri: se è umida, si aspetta. È tutto qui.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-9414406.jpg', 'Lin sistema le piante sullo scaffale del negozio', ratio='4/5',
                              tilt=True)], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Il dottore delle piante', ['Una pianta che sta male', 'si cura.'], size=50,
                                layout='stack', size_mobile=32),
                      K.text(t, 'Foglie gialle, macchie, insetti, una pianta che non cresce più: Lin viene a casa, guarda la '
                                'luce e la terra, rinvasa se serve e vi lascia un foglio con quello che c’è da fare.', size=17),
                      K.specs(t, [('La visita', '35 € a Torino'), ('Il rinvaso', 'da 12 € a pianta'),
                                  ('Le vacanze', 'le annaffiamo noi, 10 € a visita')]),
                      K.button(t, 'Chiama il dottore', CURA, kind='pieno', pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'La mia monstera aveva tre foglie gialle e io ero pronta a buttarla. Lin l’ha rinvasata, spostata di un metro, e mi ha '
           'detto di lasciarla in pace. Adesso arriva al soffitto.', 'Elisa M.', 'Una visita del dottore delle piante')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_SERRA, 'I laboratori', 'Talee, rinvasi, <em>terra sulle mani.</em>',
                          'Due sabati al mese, otto persone, una pianta da portare a casa.', 'I laboratori', LABORATORI,
                          overlay=40, video=True))
    return out


def build_piante():
    t = Tpl(ID, 'piante')
    out = [K.intro(t, 'Le piante', ['Quaranta specie,', 'ognuna col suo cartellino.'],
                   'Luce, acqua, temperatura e quello che la fa soffrire: lo scriviamo su ogni pianta, così sapete dove '
                   'metterla prima di portarla a casa.', bg=K.photo_bg('pexels-14723067.jpg'), size=90)]
    out.append(t.section([
        t.one([K.sechead(t, 'Per cominciare', ['Le più facili', 'da tenere.'], size=56)]),
        t.one([piante(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    vasi = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in [
                      ('Vaso di Impruneta · 14 cm', 'Terracotta cotta a legna, col sottovaso', '12 €', 'pexels-7424239.jpg', ''),
                      ('Vaso di Impruneta · 24 cm', 'Per monstera e ficus giovani', '28 €', 'pexels-5830878.jpg', 'Il più venduto'),
                      ('Terriccio del negozio · 5 litri', 'Torba, fibra di cocco, pomice, un po’ di humus', '9 €',
                       'pexels-18864976.jpg', ''),
                      ('Concime liquido · 250 ml', 'Da aprile a settembre, una volta al mese', '11 €', 'pexels-5830881.jpg', '')]],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 35), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '16', 'show_image': True,
        'price_position': 'right', 'badge_bg': TERRACOTTA, 'badge_color': C['chiaro'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Vasi e terra', ['Quello che serve', 'per rinvasare.'], size=50, layout='stack',
                                size_mobile=32),
                      K.text(t, 'Il terriccio lo mescoliamo noi: drena bene e non si compatta. I vasi arrivano da una fornace '
                                'di Impruneta.', size=16)], wm='2-5', ws='1-1'),
               t.col([vasi], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Da vicino', ['Le foglie,', 'una per una.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-38681769.jpg', 'Le venature di una foglia verde', 'Le venature', 'controluce'),
                               ('pexels-8737425.jpg', 'Una foglia di ficus lucida', 'Il ficus', 'lyrata'),
                               ('pexels-32633628.jpg', 'Una foglia tonda contro la luce', 'La pilea', 'tonda'),
                               ('pexels-1624732.jpg', 'Piante sul davanzale', 'Il davanzale', 'a est'),
                               ('pexels-6280451.jpg', 'Mani coi guanti su una pianta grassa', 'Le grasse', 'coi guanti')])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    return out


def build_cura():
    t = Tpl(ID, 'cura')
    out = [K.intro(t, 'Il dottore delle piante', ['Foglie gialle?', 'Si cura.'],
                   'Lin viene a casa vostra o in ufficio, guarda le piante una per una e vi lascia un foglio con quello che '
                   'c’è da fare.', bg=K.video_bg(V_SPRUZZO), size=92)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Ci scrivete', 'description': 'Una foto della pianta e del posto dove sta.'},
                  {'number': '02', 'title': 'La visita', 'description': 'Luce, terra, radici, insetti: un’ora per guardare tutto.'},
                  {'number': '03', 'title': 'La cura', 'description': 'Rinvaso, potatura, trattamento se serve: si fa lì.'},
                  {'number': '04', 'title': 'Il foglio', 'description': 'Per ogni pianta: dove tenerla, quanta acqua, cosa '
                   'guardare.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '600', 'title_color': C['testo'], 'title_size': 26,
        'title_weight': '700', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Quattro passi,', 'una pianta salva.'], size=56)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['A casa,', 'in ufficio, in vacanza.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('La visita', '35', 'a Torino', 'Un’ora per tutte le piante\nIl foglio delle cure\nI rinvasi a parte', False, ''),
            ('L’ufficio', '60', 'al mese', 'Una visita ogni due settimane\nAcqua, pulizia, concime\nLe piante malate sostituite',
             True, 'per le aziende'),
            ('Le vacanze', '10', 'a visita', 'Annaffiamo noi mentre siete via\nUna foto a ogni visita\nLe chiavi in custodia',
             False, ''),
        ], 'Chiama il dottore', CONTATTI + '#scrivici', nota='Fuori Torino si aggiunge il viaggio.'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='prezzi', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Le domande', 'di tutti.', [
        ('Le foglie diventano gialle.', 'Quasi sempre è troppa acqua. Lasciate asciugare la terra per due dita prima di bagnare.'),
        ('Le punte sono secche.', 'Aria secca: allontanate la pianta dal termosifone e spruzzate le foglie.'),
        ('Non cresce più.', 'Forse il vaso è piccolo: se le radici escono dal fondo, è ora di rinvasare.'),
    ], 'cl-faq'))
    return out


def build_laboratori():
    t = Tpl(ID, 'laboratori')
    out = [K.intro(t, 'I laboratori', ['Terra sulle mani,', 'una pianta a casa.'],
                   'Due sabati al mese, otto persone al tavolo grande del negozio. Grembiule, terra e vasi li diamo noi.',
                   bg=K.photo_bg('pexels-5830881.jpg'), size=90)]
    corsi = [('rinvaso', 'Rinvasare bene', C['acc'], 'Terra, vaso, radici: la pianta da portare a casa.', 120),
             ('talee', 'Talee e moltiplicazioni', TERRACOTTA, 'Pothos, pilea, monstera: dieci piante da una.', 120),
             ('terrario', 'Il terrario', mix(C['acc'], 60, C['testo']), 'Un piccolo bosco in un barattolo di vetro.', 150)]
    piano = [(6, '10:00', '12:00', 'rinvaso'), (6, '15:00', '17:00', 'talee'), (4, '18:30', '21:00', 'terrario')]
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'cl-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': 'Lin Chen',
                     'sala': 'Il tavolo grande', 'livello': 'tutti', 'posti': 8, 'nota': ''} for k, (g, a, b, c) in enumerate(piano)],
        'giorni': 'Martedì,Mercoledì,Giovedì,Venerdì,Sabato', 'ora_inizio': '09:00', 'ora_fine': '22:00',
        'mostra_filtri': False, 'etichetta_tutti': 'Tutti', 'filtro_livello': False, 'filtro_istruttore': False,
        'testo_vuoto': 'Nessun laboratorio in questo giorno.', 'testo_prenota': 'Prenota il posto',
        'link_prenota': CONTATTI + '?corso={corso}&giorno={giorno}&ora={ora}#scrivici', 'colore_oggi': C['acc'],
        'colore_adesso': TERRACOTTA, 'colore_tenue': C['testo'], 'giorno_font_family': 'heading', 'giorno_font_size': 20,
        'giorno_font_weight': '700', 'giorno_uppercase': False, 'corso_font_family': 'heading', 'corso_font_size': 17,
        'corso_font_weight': '700', 'ora_font_family': 'var(--olo-font-family-mono)', 'raggio_lezione': radius(12),
        'altezza_ora': 40, 'gap': 4, 'forma_filtri': 'pillola'}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Il calendario', ['Tre laboratori,', 'otto posti.'], size=56,
                         tagline='35 € a persona, la pianta compresa. Toccate un laboratorio per prenotarlo.')]),
        t.one([orario]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Al tavolo grande', ['Com’è', 'un sabato mattina.'], size=56)]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5', lightbox=True)], wm='1-4', ws='1-2') for f, alt in (
            ('pexels-18864976.jpg', 'Una mano che rinvasa una piantina'), ('pexels-5830878.jpg', 'Vasi di terracotta e terra'),
            ('pexels-28961744.jpg', 'La serra con le felci'), ('pexels-16808428.jpg', 'Le piante grandi della serra'))],
              layout='25-25-25-25', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_BANCO, 'Il bancone', 'Ogni pianta <em>esce bagnata.</em>',
                             'Prima di partire la annaffiamo noi: per una settimana non dovete fare niente.', overlay=40))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Passate in negozio,', 'o mandateci una foto.'],
                   'Per una pianta, il dottore delle piante, un laboratorio o le piante dell’ufficio.',
                   bg=K.photo_bg('pexels-37047504.jpg'), size=90)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'corso', True, param='corso',
                     opzioni='Una pianta\nIl dottore delle piante\nUn laboratorio\nLe piante dell’ufficio'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1',
                     segnaposto='Che pianta, dove sta, che cosa vi preoccupa.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Una riga e una foto,', 'vi diciamo cosa fare.'],
                                'Rispondiamo entro un giorno, anche solo per un consiglio.', VOCI, campi,
                                'Clorofilla — contatto', 'Invia', 'Ricevuto. Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il negozio', ['In San Salvario,', 'le vetrine a sud.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Indirizzo', 'Via Madama Cristina 52, Torino'), ('Orari', 'martedì — sabato 10 — 19:30'),
                                  ('Le consegne', 'in bici, a Torino')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Clorofilla · il negozio', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='negozio', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · senza radici', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>non ha attecchito.</em>', size=80, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Capita anche alle talee. Riproviamo dalla home.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_FOGLIA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Negozi online', 'La pianta che cresce',
    ['piante', 'negozio di piante', 'vivaio', 'laboratori', 'rinvaso', 'torino', 'chiaro', 'slider pro'],
    'Un negozio di piante in San Salvario, verde foglia e terracotta: in apertura una pianta che cresce nel vaso mentre si '
    'scorre, il fusto che sale e le foglie che si aprono una dopo l’altra davanti a un tondo di video; le piante col loro '
    'cartellino, il dottore delle piante e i laboratori di rinvaso.',
    [('Le piante', PIANTE), ('Il dottore', CURA), ('I laboratori', LABORATORI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('piante', 'Le piante', 'piante'), ('cura', 'Il dottore delle piante', 'cura'),
     ('laboratori', 'I laboratori', 'laboratori'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Be Vietnam Pro\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Bricolage Grotesque\', system-ui, sans-serif', '\'Victor Mono\', ui-monospace, Menlo, monospace',
    ['Bricolage Grotesque', 'Be Vietnam Pro', 'Victor Mono'], corsivo_di=[], peso_titoli='700', raggio=16, grana=0,
    titoli_line='1.02', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'foglie'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'piante.json': build_piante(), 'cura.json': build_cura(), 'laboratori.json': build_laboratori(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
