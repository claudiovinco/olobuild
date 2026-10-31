# -*- coding: utf-8 -*-
"""Genera il tema «Orbita Bassa» (id orbita-bassa): una giovane azienda spaziale romana che costruisce microsatelliti
per l'osservazione della Terra e ne vende i dati: campi, ghiacciai, coste, città.

Tema scuro: il nero dello spazio, il bianco ghiaccio e l'arancio di segnalazione delle tute e dei razzi. Titoli in Radio
Canada Big, testo in Atkinson Hyperlegible Next, dati in Ubuntu Sans Mono.

Apertura: Slider Pro «la costellazione», alto 16:9 perché i cerchi restino cerchi. La Terra che gira davvero (un video
in una finestra rotonda), due orbite ellittiche inclinate e i satelliti che ci corrono sopra a velocità diverse
(percorsi di movimento); a ogni diapositiva una scheda di vetro col dato raccolto (campi, ghiacciai, coste), con la
foto dall'alto e il numero. La Terra, le orbite e i satelliti restano fra una diapositiva e l'altra.
"""
import math
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'orbita-bassa'
NAME = 'Orbita Bassa'

HEX = {
    'primary': '#FF6A3D', 'primary_contrast': '#0A0D14', 'secondary': '#4C6FFF', 'secondary_contrast': '#05070C',
    'accent': '#9FD8FF', 'dark': '#020308', 'light': '#F1F4F8', 'muted': '#0D1220', 'muted_contrast': '#E6EBF2',
    'text': '#E6EBF2', 'text_muted': '#93A0B3', 'background': '#05070C', 'border': '#1E2638', 'link': '#FF6A3D',
}

HOME, COSTELLAZIONE, DATI, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:la-costellazione}}', '{{PAGINA:i-dati}}',
                                       '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'missione@orbitabassa.example', '+39 06 0000 0550', 'tel:+390600000550'
LAT, LNG = '41.9268', '12.6031'

V_TERRA, V_FALCE, V_LANCIO, V_SATELLITE, V_ANTENNA = ('pexels-video-854518', 'pexels-video-5921369', 'pexels-video-854232',
                                                      'pexels-video-15164036', 'pexels-video-2954002')
PHOTOS = [
    'pexels-36084879.jpg', 'pexels-12545525.jpg', 'pexels-36050294.jpg', 'pexels-2724241.jpg', 'pexels-11624913.jpg',
    'pexels-35441333.jpg', 'pexels-12545536.jpg', 'pexels-957626.jpg', 'pexels-7327336.jpg', 'pexels-9574465.jpg',
    'pexels-5118462.jpg', 'pexels-5506058.jpg', 'pexels-4031690.jpg', 'pexels-8851780.jpg', 'pexels-8539523.jpg',
    'pexels-7565176.jpg', 'pexels-14776246.jpg', 'pexels-20286950.jpg', 'pexels-20545300.jpg', 'pexels-13760469.jpg',
    'pexels-20286952.jpg', 'pexels-30622825.jpg', 'pexels-33803466.jpg', 'pexels-33803484.jpg', 'pexels-34432856.jpg',
    'pexels-37894518.jpg', 'pexels-20548937.jpg', 'pexels-9349554.jpg', 'pexels-36043872.jpg', 'pexels-39448138.jpg',
    'pexels-28852824.jpg', 'pexels-2899221.jpg', 'pexels-32395648.jpg', 'pexels-32395646.jpg', 'pexels-17661614.jpg',
    'pexels-39635044.jpg', 'pexels-34303714.jpg', 'pexels-36169769.jpg', 'pexels-36169772.jpg', 'pexels-34924856.jpg',
    'pexels-6325003.jpg', 'pexels-30226726.jpg', 'pexels-18833053.jpg', 'pexels-11858855.jpg', 'pexels-1229042.jpg',
]
VIDEOS = [V_TERRA, V_FALCE, V_LANCIO, V_SATELLITE, V_ANTENNA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='500', corsivo=False, maiuscole_pulsanti=True)
C = K.c
GHIACCIO = C['acc2']
TITOLI, MONO = 'Radio Canada Big', 'Ubuntu Sans Mono'
VOCI = [('map-pin', 'Via Tiburtina 1020 · Roma', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Orbita Bassa', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Orbita Bassa', 'Microsatelliti per l’osservazione della Terra, costruiti a Roma. Ventiquattro '
                   'satelliti a 550 chilometri, una foto di ogni campo d’Europa ogni giorno, i dati pronti in un’ora.',
                   [('La costellazione', COSTELLAZIONE), ('I dati', DATI), ('Contatti', CONTATTI),
                    ('Lavora con noi', CONTATTI + '#squadra'), ('Privacy', '/privacy-policy/')],
                   'Il centro di controllo', 'Aperto tutto l’anno, giorno e notte<br>Visite per le scuole il venerdì', VOCI,
                   '© 2026 Orbita Bassa spa · Roma · P.IVA 00000000000',
                   [('linkedin', 'https://linkedin.com'), ('youtube', 'https://youtube.com')], marchio_size=120)


DATI_SLIDE = [  # linguetta, occhiello, riga 1, riga 2, testo, foto della scheda, etichetta, valore, luogo
    ('Costellazione', '01 · la costellazione', 'Ventiquattro satelliti', 'a 550 km da qui.', 'Grandi come un forno a '
     'microonde, girano attorno alla Terra in novanta minuti. Insieme passano sopra ogni punto d’Europa una volta al giorno.',
     'pexels-36084879.jpg', 'Passaggio sopra Roma', 'ogni 90′', 'OB-07 · 09:42 UTC'),
    ('Campi', '02 · l’agricoltura', 'Ogni campo,', 'ogni mattina.', 'L’indice di vegetazione dice dove la coltura soffre '
     'prima che si veda da terra: l’acqua si dà solo dove serve.', 'pexels-20286950.jpg', 'Indice di vegetazione',
     '0,72', 'Campo 1184 · Vercelli'),
    ('Ghiacci', '03 · il clima', 'I ghiacciai,', 'misurati dall’alto.', 'Un’immagine ogni settimana dello stesso '
     'ghiacciaio, per vedere quanto si ritira d’estate e quanto torna d’inverno.', 'pexels-9349554.jpg',
     'Fronte del ghiacciaio', '−1,4 m', 'Monte Rosa · luglio 2026'),
    ('Coste', '04 · le coste', 'Dove finisce', 'la terra.', 'Le linee di costa e i delta cambiano a ogni mareggiata: le '
     'misuriamo per i comuni e la protezione civile.', 'pexels-32395648.jpg', 'Linea di costa', '−12 m',
     'Delta del Po · in un anno'),
]


def apertura(t):
    m = K.mob
    W, H = 1440.0, 810.0
    ex, ey, ew, eh = 27.78, 59.0, 44.44, 79.01         # la Terra: 640 px, sorge dal bordo in basso
    cx, cy = (ex + ew / 2) * W / 100, (ey + eh / 2) * H / 100
    sl = []
    for i, (tab, occ, r1, r2, testo, foto, etichetta, valore, luogo) in enumerate(DATI_SLIDE):
        n = i + 1
        sl.append(K.diapositiva('ob-%d' % n, K.sfondo_video([V_ANTENNA, V_LANCIO, V_SATELLITE, V_ANTENNA][i], overlay=0.74,
                                                            colore=C['fondo']), [
            K.livello('ob-o%d' % n, contenuto=occ, x=20, y=11.5, w=60, size=12, peso='500', colore=C['acc'], letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, align='center', ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 8.6, 'x': 7, 'width': 86}, 'tablet': {'y': 13}}),
            K.livello('ob-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=10, y=15.5, w=80, size=70, peso='500',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, letterSpacing=-1, align='center', entra='fadeIn',
                      ritardo=350, durata=1100, charAnim={'stagger': 22, 'offsetY': -30, 'blur': 6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(31), 'y': 11, 'x': 5, 'width': 90}, 'tablet': {'y': 17.5}}),
            K.livello('ob-i%d' % n, contenuto=r2, tag='p', x=10, y=24.5, w=80, size=70, peso='500', colore=GHIACCIO,
                      fontFamily=TITOLI, lineHeight=1.04, letterSpacing=-1, align='center', entra='fadeIn', ritardo=650,
                      durata=1100, charAnim={'stagger': 22, 'offsetY': -30, 'blur': 6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(31), 'y': 15.5, 'x': 5, 'width': 90}, 'tablet': {'y': 26.5}}),
            K.livello('ob-s%d' % n, contenuto=testo, x=29, y=35.5, w=42, size=17, peso='400', colore=mix(C['chiaro'], 80),
                      lineHeight=1.6, align='center', ritardo=1000,
                      responsive={'mobile': {'fontSize': m(14), 'y': 21.3, 'x': 7, 'width': 86},
                                  'tablet': {'y': 37, 'x': 18, 'width': 64}}),
            K.livello('ob-b%d' % n, tipo='button', contenuto='Vedi i dati', x=44.3, y=48, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=10, ritardo=1300, buttonUrl=DATI, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 30, 'x': 33, 'fontSize': m(11)}, 'tablet': {'x': 43.3, 'y': 50}}),
            # la scheda di vetro col dato della diapositiva, in basso a sinistra
            K.livello('ob-cs%d' % n, tipo='shape', x=4, y=60, w=21, h=27, bgColor=mix(C['sala'], 55), borderRadius=16,
                      borderWidth=1, borderColor=mix(GHIACCIO, 30), backdropBlur=14, entra='fadeInLeft', ritardo=1500,
                      durata=900, esce='fadeOut',
                      parallax={'type': 'mouse', 'depth': 8, 'tilt3d': True, 'scrollSpeed': 0.5},
                      responsive={'mobile': {'x': 7, 'y': 36.5, 'width': 86, 'height': 10}}),
            K.livello('ob-cf%d' % n, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=5.2, y=62, w=6.9, h=12.3,
                      borderRadius=10, entra='fadeIn', ritardo=1800, durata=700,
                      responsive={'mobile': {'x': 9, 'y': 37.6, 'width': 17.2, 'height': 7.86}}),
            K.livello('ob-ce%d' % n, contenuto=etichetta, x=13, y=62.5, w=11, size=11, peso='500', colore=mix(C['chiaro'], 70),
                      fontFamily=MONO, letterSpacing=1, textTransform='uppercase', lineHeight=1.4, entra='fadeIn',
                      ritardo=1900, responsive={'mobile': {'x': 30, 'y': 38, 'width': 60, 'fontSize': m(10)}}),
            K.livello('ob-cv%d' % n, contenuto=valore, x=5.2, y=75.5, w=18, size=40, peso='500', colore=C['acc'],
                      fontFamily=TITOLI, lineHeight=1.0, entra='fadeInUp', ritardo=2100, durata=700,
                      responsive={'mobile': {'x': 30, 'y': 40.1, 'width': 60, 'fontSize': m(24)}}),
            K.livello('ob-cl%d' % n, contenuto=luogo, x=13, y=67.5, w=11, size=11, peso='400', colore=mix(C['chiaro'], 62),
                      fontFamily=MONO, lineHeight=1.4, entra='fadeIn', ritardo=2300,
                      responsive={'mobile': {'x': 30, 'y': 43.9, 'width': 60, 'fontSize': m(10)}}),
        ], durata=8000, tab=tab))
    # la Terra che sorge dal bordo, le orbite inclinate e i satelliti che ci corrono sopra
    globali = [K.livello('ob-terra', tipo='video', videoSrc=K.img(V_TERRA + '.mp4'), videoAutoplay=True, videoMuted=True,
                         videoLoop=True, x=ex, y=ey, w=ew, h=eh, borderRadius=999, entra='fadeInUp', ritardo=0, durata=1800,
                         boxShadow={'x': 0, 'y': 0, 'blur': 140, 'spread': 10, 'color': mix(GHIACCIO, 30)},
                         responsive={'mobile': {'x': 10, 'y': 58.5, 'width': 80, 'height': 36.57}})]
    for k, (rx, ry, ang, dur, sz) in enumerate([(470, 92, -7, 14000, 12), (590, 150, 5, 22000, 10)]):
        ox, oy = (cx - rx) / W * 100, (cy - ry) / H * 100
        globali.append(K.livello('ob-orbita%d' % k, tipo='shape', x=round(ox, 2), y=round(oy, 2), w=round(2 * rx / W * 100, 2),
                                 h=round(2 * ry / H * 100, 2), bgColor='transparent', borderRadius=999, borderWidth=1,
                                 borderStyle='dashed', borderColor=mix(GHIACCIO, 45),
                                 timeline=K.tl(round(ox, 2), round(oy, 2), [(0, {'rotation': ang, 'opacity': 0}),
                                                                            (1500, {'rotation': ang, 'opacity': 100})],
                                               1500, loop=False),
                                 responsive={'mobile': {'visible': False}}))
        a = math.radians(ang)
        dx, dy = rx * math.cos(a), rx * math.sin(a)
        sx, sy = (cx - dx - sz / 2) / W * 100, (cy - dy - sz / 2) / H * 100
        percorso = 'M 0 0 a %d %d %d 1 1 %.1f %.1f a %d %d %d 1 1 %.1f %.1f' % (rx, ry, ang, 2 * dx, 2 * dy, rx, ry, ang,
                                                                               -2 * dx, -2 * dy)
        globali.append(K.livello('ob-sat%d' % k, tipo='shape', x=round(sx, 2), y=round(sy, 2), w=round(sz / W * 100, 3),
                                 h=round(sz / H * 100, 3), bgColor=C['acc'] if k == 0 else GHIACCIO, borderRadius=3,
                                 entra='fadeIn', ritardo=1600 + 400 * k, durata=800,
                                 boxShadow={'x': 0, 'y': 0, 'blur': 16, 'spread': 3,
                                            'color': mix(C['acc'] if k == 0 else GHIACCIO, 70)},
                                 motionPath={'path': percorso, 'duration': dur, 'autoRotate': True, 'loop': True,
                                             'easing': 'linear'},
                                 responsive={'mobile': {'visible': False}}))
    globali.append(K.livello('ob-quota', contenuto='Quota 550 km · inclinazione 97,6° · 15 giri al giorno', x=74, y=80, w=22,
                             size=11, peso='400', colore=mix(C['chiaro'], 62), letterSpacing=2, textTransform='uppercase',
                             fontFamily=MONO, align='right', lineHeight=1.6, entra='fadeIn', ritardo=1800,
                             responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='blur',
                       durata_trans=1100, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=C['acc'], navColor=C['chiaro'])

USI = [  # titolo, sottotitolo, foto, ancora
    ('Agricoltura', 'Acqua e concime solo dove servono', 'pexels-20286952.jpg', 'agricoltura'),
    ('Ghiacciai', 'Il ritiro, settimana dopo settimana', 'pexels-36043872.jpg', 'clima'),
    ('Coste e fiumi', 'Erosione, piene, delta', 'pexels-39635044.jpg', 'coste'),
    ('Città di notte', 'Luce, consumi, crescita', 'pexels-33803466.jpg', 'citta'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Orbita Bassa · Roma', ['Guardiamo la Terra', 'per chi la coltiva.'], size=60,
                                layout='stack', size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Costruiamo i satelliti a Roma, li lanciamo in gruppi da sei e li guidiamo da qui. Ogni '
                                'giorno fotografano tutta l’Europa: consorzi agricoli, comuni e ricercatori ricevono i dati '
                                'un’ora dopo il passaggio, già pronti da leggere.', size=17, color=C['testo']),
                      K.button(t, 'La costellazione', COSTELLAZIONE, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('24', 'Satelliti in orbita'), ('550 km', 'La quota'), ('90′', 'Un giro della Terra'),
                            ('3 m', 'Di risoluzione a terra')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    griglia = t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': su, 'title': ti, 'link': DATI + '#' + a, 'span': 6, 'aspect': '4/3'}
                  for ti, su, f, a in USI],
        'columns': 2, 'gap': 16, 'object_position': 'center center', 'object_fit': 'cover', 'radius': 10,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 22), 'arrow_color': C['chiaro'], 'arrow_hover_bg': C['acc'],
        'arrow_hover_color': C['on_acc'], 'show_arrow': True, 'title_size': 38, 'title_weight': '500', 'kicker_size': 11,
        'card_radius': radius(10)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'A che cosa servono', ['Quattro modi', 'di guardare giù.'], size=60,
                         tagline='Le stesse foto, lette in modi diversi: la clorofilla, il ghiaccio, l’acqua, la luce.')]),
        t.one([griglia]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_LANCIO, 'Il lancio', 'Undici minuti <em>per lo spazio.</em>',
                             'Dal momento in cui si accendono i motori a quando i satelliti si staccano dal razzo passano '
                             'undici minuti. Poi tre giorni per aprire i pannelli e salutare la stazione di terra.',
                             overlay=50))
    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2027-03-14T09:20', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': True, 'expired_message': 'Sono in orbita!', 'label_days': 'giorni',
        'label_hours': 'ore', 'label_minutes': 'minuti', 'label_seconds': 'secondi', 'text_color': C['chiaro'],
        'accent_color': C['acc'], 'number_font_size': 76, 'number_font_weight': '500', 'label_font_size': '12',
        'item_min_width': 150, 'item_bg_color': C['sala'], 'item_radius': radius(10),
        'item_padding': {'top': 30, 'right': 20, 'bottom': 22, 'left': 20}}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il prossimo lancio', ['Altri sei,', 'il 14 marzo.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Dalla base di Kourou, nella Guyana francese, alle 9:20 di Roma. Lo trasmettiamo in diretta '
                                'dal centro di controllo, e le scuole possono venire a vederlo con noi.')],
                     wm='2-5', ws='1-1'),
               t.col([conto], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', html_id='lancio', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Prima ce ne accorgevamo quando il riso ingialliva. Adesso la mappa arriva alle otto di mattina e sappiamo '
                 'quale campo bagnare per primo: quest’estate abbiamo usato un quinto d’acqua in meno.',
        'author_name': 'Paolo Viretti', 'author_role': 'Consorzio irriguo della Baraggia', 'avatar': '', 'rating': '5',
        'text_color': C['testo'], 'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'],
        'quote_font': 'heading', 'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False,
        'layout': 'editorial', 'show_line': True, 'line_color': C['acc']}, style={})])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_ANTENNA, 'La stazione di terra', 'Sei minuti <em>per parlarsi.</em>',
                          'Ogni satellite passa sopra la nostra antenna per sei minuti: in quel tempo scarica le foto del '
                          'giro e riceve i comandi per il prossimo.', 'La costellazione', COSTELLAZIONE, overlay=58,
                          video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Orbita Bassa',
                page_intro_caption='T − 0 · Roma', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def build_costellazione():
    t = Tpl(ID, 'la-costellazione')
    out = [K.intro(t, 'La costellazione', ['Ventiquattro satelliti,', 'quattro piani.'],
                   'Sei satelliti per ogni piano d’orbita, sfasati in modo che sopra l’Europa ce ne sia sempre uno. Li '
                   'costruiamo, li proviamo e li guidiamo noi.', bg=K.photo_bg('pexels-5118462.jpg', overlay=66),
                   overlay_text=True)]
    scheda = t.classic('schedatecnica', {
        'codice': 'OB-16U', 'nome': 'Il satellite OB-16U', 'etichetta': 'Serie 2026',
        'descrizione': 'Un cubo da sedici unità con due pannelli che si aprono come ali, una fotocamera multispettrale e un '
                       'motore a gas freddo per tenere la quota. Dura cinque anni, poi rientra e brucia in atmosfera.',
        'immagine': K.img('pexels-9574465.jpg'), 'titolo_specifiche': 'Le misure',
        'specifiche': [{'id': 'os-%d' % k, 'gruppo': g, 'nome': a, 'valore': b, 'unita': u} for k, (g, a, b, u) in enumerate([
            ('Corpo', 'Dimensioni', '45 × 23 × 23', 'cm'), ('Corpo', 'Massa', '24', 'kg'), ('Corpo', 'Potenza', '90', 'W'),
            ('Fotocamera', 'Risoluzione', '3', 'm'), ('Fotocamera', 'Bande', '8', ''), ('Fotocamera', 'Striscia', '24', 'km'),
            ('Orbita', 'Quota', '550', 'km'), ('Orbita', 'Durata', '5', 'anni'), ('Orbita', 'Rientro', 'controllato', '')])],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'Costruito e provato a Roma · rientro a fine vita',
        'cta_testo': 'Chiedi la scheda completa', 'cta_url': CONTATTI + '#scrivici', 'cta_codice': True,
        'cta_parametro': 'satellite', 'dati_strutturati': False, 'accento': C['acc'], 'sfondo_scheda': C['sala'],
        'colore_linee': C['filo'], 'righe_alternate': False, 'nome_font_size': 40, 'nome_font_weight': '500',
        'nome_color': C['testo'], 'descrizione_color': C['tenue'], 'gruppo_color': C['acc'], 'voce_color': C['tenue'],
        'valore_color': C['testo'], 'unita_color': C['tenue'], 'codice_color': GHIACCIO, 'raggio': radius(10),
        'raggio_pulsante': radius(10), 'padding_scheda': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40},
        'img_ratio': '4/5', 'img_fit': 'cover', 'foto_posizione': 'sinistra', 'larghezza_foto': 42, 'gap': 48,
        'colonne_specifiche': 1}, style={})
    out.append(t.section([t.one([scheda])], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    missione = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('T−90', 'Roma', 'pexels-5506058.jpg', 'L’integrazione', 'Si monta', 'in camera bianca', 'Fotocamera, pannelli, '
             'radio: ogni pezzo si prova al caldo, al freddo e sotto vuoto.', '90', 'giorni prima'),
            ('T−0', 'Kourou', 'pexels-7327336.jpg', 'Il lancio', 'Sei alla volta', 'sul razzo', 'Partono insieme, si '
             'staccano uno alla volta a undici minuti dal decollo.', '11′', 'al distacco'),
            ('T+3', 'In orbita', 'pexels-30226726.jpg', 'Il primo contatto', 'Si aprono', 'i pannelli', 'Tre giorni dopo '
             'il primo saluto all’antenna; la prima foto arriva dopo una settimana.', '7', 'giorni alla prima foto')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': C['acc'],
        'counter_font_family': 'heading', 'counter_size': 72, 'counter_color': C['acc'], 'counter_italic': False,
        'counter_weight': '500', 'tag_color': C['chiaro'], 'pre_title_color': GHIACCIO, 'title_font_family': 'heading',
        'title_size': 30, 'title_weight': '500', 'title_color': C['chiaro'], 'title_accent_color': C['acc'],
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'clock', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(10)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Una missione', ['Dalla camera bianca', 'alla prima foto.'], size=56)]),
        t.one([missione]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    grafico = t.classic('chart', {
        'chart_type': 'doughnut',
        'items': [{'label': l, 'value': v, 'color': c} for l, v, c in [
            ('Agricoltura', '46', C['acc']), ('Clima e ghiacciai', '22', GHIACCIO), ('Coste e fiumi', '18', C['sec']),
            ('Città', '14', mix(C['acc'], 45, C['chiaro']))]],
        'chart_height': '360', 'show_legend': True, 'legend_position': 'bottom', 'legend_align': 'center',
        'legend_color': C['testo'], 'legend_font_size': '13', 'show_title': False}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Le foto di un anno', ['Due milioni di immagini,', 'quattro usi.'], size=52,
                                layout='stack', size_mobile=34),
                      K.text(t, 'Nel 2026 la costellazione ha scattato due milioni di immagini. Quasi la metà è servita ai '
                                'campi: irrigazione, concime, raccolto. Il resto a chi studia il clima, le coste e le città.')],
                     wm='1-2', ws='1-1'),
               t.col([grafico], wm='1-2', ws='1-1')], layout='50-50', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SATELLITE, 'In orbita', 'Quindici giri <em>al giorno.</em>',
                             'A 27.000 chilometri all’ora, ogni satellite vede sedici albe e sedici tramonti in ventiquattro '
                             'ore. Le foto si scattano solo dal lato del sole.', overlay=45))
    out.append(K.domande(t, 'La', 'costellazione.', [
        ('Che cosa succede ai satelliti a fine vita?', 'Accendono il motore all’indietro e scendono finché l’atmosfera li '
                                                       'brucia, entro due anni. Non lasciamo detriti in orbita.'),
        ('Si vedono dalla Terra?', 'Al tramonto, con un buon binocolo: sono puntini che passano in quattro minuti.'),
        ('Fotografano le persone?', 'No: tre metri di risoluzione bastano per un campo, non per una persona o una targa.'),
        ('Chi li costruisce?', 'Noi, a Roma, con fornitori italiani per l’ottica e la struttura.'),
    ], 'co', bg=True))
    return out


def build_dati():
    t = Tpl(ID, 'i-dati')
    out = [K.intro(t, 'I dati', ['Una foto al giorno,', 'già letta.'],
                   'Non vendiamo immagini da interpretare: vendiamo mappe pronte, con il numero che vi serve e un avviso '
                   'quando qualcosa cambia.', bg=K.photo_bg('pexels-14776246.jpg', overlay=62), overlay_text=True)]
    out.append(t.section([
        t.one([K.sechead(t, 'Dall’archivio', ['L’Europa,', 'vista da 550 km.'], size=56,
                         tagline='Trascinate o lasciate scorrere: sono immagini vere della costellazione, di questo autunno.')]),
        t.one([K.coverflow(t, [
            ('pexels-20286950.jpg', 'Campi coltivati visti dall’alto', 'Campi a cerchio', 'Irrigazione a perno'),
            ('pexels-36050294.jpg', 'Il disegno di un fiume visto dall’alto', 'Un fiume', 'I rami di un delta'),
            ('pexels-9349554.jpg', 'Un ghiacciaio innevato dall’alto', 'Un ghiacciaio', 'Monte Rosa'),
            ('pexels-33803466.jpg', 'Le strade illuminate di una città di notte', 'Una città', 'Le luci di notte'),
            ('pexels-30622825.jpg', 'Risaie verdi dall’alto', 'Le risaie', 'Vercelli, luglio'),
            ('pexels-12545536.jpg', 'Una costa frastagliata con le onde', 'Una costa', 'Dopo la mareggiata'),
            ('pexels-28852824.jpg', 'Iceberg nel mare scuro', 'Il ghiaccio marino', 'Mare del Nord'),
            ('pexels-34303714.jpg', 'Mangrovie verdi e canali', 'Le zone umide', 'Delta e lagune')],
            altezza=420, larghezza=360)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    carte = []
    for nome, sigla, testo, foto, anc in [
        ('Vegetazione', 'NDVI', 'Quanta clorofilla c’è: dice dove la coltura sta bene e dove soffre, prima che si veda.',
         'pexels-13760469.jpg', 'agricoltura'),
        ('Umidità del suolo', 'SMI', 'Quanta acqua c’è nei primi centimetri di terra: si bagna solo dove serve.',
         'pexels-20545300.jpg', ''),
        ('Ghiaccio e neve', 'NDSI', 'Dove c’è neve o ghiaccio e quanto è esteso: la misura del ritiro, ogni settimana.',
         'pexels-20548937.jpg', 'clima'),
        ('Acqua', 'NDWI', 'Il confine fra terra e acqua: piene dei fiumi, delta, linee di costa.', 'pexels-32395646.jpg',
         'coste'),
        ('Luci notturne', 'VIIRS', 'Quanta luce fa una città di notte: dice dove cresce e quanta energia consuma.',
         'pexels-34432856.jpg', 'citta'),
        ('Temperatura', 'LST', 'Quanto scotta il suolo: le isole di calore in città e i campi sotto stress.',
         'pexels-11624913.jpg', '')]:
        carte.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 58}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': sigla + ' · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': sigla, 'back_description': testo,
            'back_bg': C['sala'], 'back_text_color': C['testo'], 'back_valign': 'center', 'back_cta_text': '',
            'back_cta_url': '', 'flip_direction': 'vertical', 'flip_trigger': 'hover', 'card_height': '340',
            'card_border_radius': '10', 'card_shadow': 'md', 'title_size': '26', 'title_weight': '500', 'desc_size': '15'},
            style={}, advanced={'html_id': anc} if anc else None)], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli indici', ['Sei numeri', 'dalla stessa foto.'], size=56,
                         tagline='Ogni carta è un modo di leggere la luce che torna dalla Terra. Giratela.')]),
        t.row(carte[:3], layout='33-33-33', gap=20),
        t.row(carte[3:], layout='33-33-33', gap=20, style={'margin_top': 20}),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli abbonamenti', ['Dal campo', 'al continente.'], size=56,
                         tagline='Il primo mese è di prova, senza impegno. Le mappe arrivano per mail e nel vostro gestionale.')]),
        K.piani(t, [('Campo', '29', 'al mese · fino a 50 ettari', 'Una mappa al giorno\nVegetazione e umidità\nAvviso '
                     'quando cambia qualcosa', False, ''),
                    ('Consorzio', '390', 'al mese · fino a 5.000 ettari', 'Tutti gli indici\nStorico di cinque anni\n'
                     'Collegamento al vostro gestionale\nUn analista dedicato', True, 'Il più scelto'),
                    ('Su misura', '—', 'per enti e ricerca', 'Aree grandi quanto volete\nImmagini a richiesta\nI dati '
                     'grezzi, per chi li sa leggere', False, '')],
                'Chiedi la prova', CONTATTI + '#scrivici', nota='IVA esclusa · primo mese gratis'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Scriveteci', 'da qualsiasi orbita.'],
                   'Per una prova dei dati, una visita al centro di controllo con la classe o per lavorare con noi.',
                   bg=K.photo_bg('pexels-30226726.jpg', overlay=60), overlay_text=True, size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'cosa', True,
                     opzioni='Una prova dei dati\nUn abbonamento per un consorzio\nUna visita con la scuola\nLavorare con noi\n'
                             'Altro'),
             K.campo('text', 'Ente o azienda', 'ente'), K.campo('text', 'Satellite', 'satellite', param='satellite'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Il vostro progetto', 'messaggio', larghezza='1-1',
                     segnaposto='Dove, quanto grande, che cosa vi serve sapere.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Un’area,', 'una domanda.'],
                                'Vi rispondiamo entro due giorni lavorativi, con una mappa di prova della vostra zona.', VOCI,
                                campi, 'Orbita Bassa — contatto', 'Invia', 'Ricevuto. Vi scriviamo entro due giorni.'))
    out.append(t.section([
        t.one([K.sechead(t, 'La squadra', ['Quaranta persone,', 'ventiquattro satelliti.'], size=56,
                         tagline='Ingegneri, analisti, agronomi. Cerchiamo sempre chi sa scrivere software di volo.')]),
        K.squadra(t, [
            ('pexels-4031690.jpg', 'Giulia Ferraro', 'Ingegnera dei sistemi', 'Ha disegnato l’OB-16U: dal primo schizzo alla '
             'prova sotto vuoto.', '50% 25%'),
            ('pexels-7565176.jpg', 'Andrea Costa', 'Operazioni di volo', 'Guida la costellazione dal centro di controllo. '
             'Fa il turno di notte quando c’è un lancio.', '50% 30%'),
            ('pexels-8851780.jpg', 'Sara Lombardi', 'Dati e immagini', 'Agronoma e analista: trasforma le foto in mappe che '
             'un agricoltore legge in un minuto.', '50% 30%')]),
    ], padding='large', width='large', html_id='squadra', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il centro di controllo', ['Al Tecnopolo,', 'sulla Tiburtina.'], size=52,
                                layout='stack', size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via Tiburtina 1020, Roma'), ('Treno', 'stazione Guidonia, poi navetta'),
                                  ('Visite', 'il venerdì, su prenotazione'), ('Lanci', 'in diretta, aperti al pubblico')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Orbita Bassa · centro di controllo', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · segnale perso', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>è fuori orbita.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro']),
                       K.text(t, 'Il prossimo passaggio sopra la home è adesso.', size=17, align='center',
                              color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna a terra', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_FALCE, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Tecnologia', 'I satelliti in orbita',
    ['spazio', 'satelliti', 'aerospazio', 'dati', 'roma', 'scuro', 'slider pro', '3d'],
    'Una giovane azienda spaziale, nero dello spazio e arancio di segnalazione: in apertura, sopra il video delle '
    'antenne e del lancio, la Terra che sorge dal bordo, due orbite inclinate coi satelliti che ci corrono sopra e a '
    'ogni diapositiva la scheda di vetro col dato raccolto; il conto alla rovescia del lancio, i dati sulla mappa, la'
    ' costellazione e le missioni.',
    [('La costellazione', COSTELLAZIONE), ('I dati', DATI), ('Il lancio', HOME + '#lancio'), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('la-costellazione', 'La costellazione', 'la-costellazione'), ('i-dati', 'I dati', 'i-dati'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Atkinson Hyperlegible Next\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Radio Canada Big\', system-ui, sans-serif', '\'Ubuntu Sans Mono\', ui-monospace, Menlo, monospace',
    ['Radio Canada Big', 'Atkinson Hyperlegible Next', 'Ubuntu Sans Mono'], peso_titoli='500', raggio=10, grana=4,
    titoli_line='1.04', titoli_spazio='-0.02em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # apertura rifatta il 31 ottobre 2026
THEME['cursor']['style'] = 'mirino'  # cursore: il mirino con le coordinate (31 ott 2026)


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-costellazione.json': build_costellazione(), 'i-dati.json': build_dati(),
                        'contatti.json': build_contatti(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
