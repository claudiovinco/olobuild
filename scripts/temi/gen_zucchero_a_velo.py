# -*- coding: utf-8 -*-
"""Genera il tema «Zucchero a Velo» (id zucchero-a-velo): una pasticceria in Vanchiglia, a Torino. Il laboratorio
accende il forno alle quattro, la vetrina è piena alle sette; bignè, crostate, croissant e macaron, e le torte su
ordinazione.

Tema chiaro, panna e lampone, col verde pistacchio e il giallo del burro. Titoli in Castoro (anche corsivo), testo in
Outfit, prezzi in Azeret Mono.

Apertura: Slider Pro a vetrina che cambia colore: ogni dolce ha il suo fondo pastello, la foto in una cornice ad arco
che si svela dal basso (nella prima il video dello zucchero a velo che cade dal setaccio), il particolare in un
tondo che galleggia, il prezzo su un cartellino. In basso, fissi, i quattro tondi della vetrina: toccandoli si passa a
quel dolce. Sopra, granelli di zucchero che scendono piano.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'zucchero-a-velo'
NAME = 'Zucchero a Velo'

HEX = {
    'primary': '#A8325F', 'primary_contrast': '#FFFFFF', 'secondary': '#6F8A4E', 'secondary_contrast': '#FFFFFF',
    'accent': '#F2C45F', 'dark': '#2B1B16', 'light': '#FFFCF7', 'muted': '#F5E9DC', 'muted_contrast': '#2B1B16',
    'text': '#2B1B16', 'text_muted': '#6B564A', 'background': '#FBF5EC', 'border': '#E8D7C5', 'link': '#A8325F',
}

HOME, VETRINA, TORTE, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:la-vetrina}}', '{{PAGINA:le-torte}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'buongiorno@zuccheroavelo.example', '+39 011 000 0418', 'tel:+390110000418'
LAT, LNG = '45.0712', '7.6985'

V_ZUCCHERO, V_SAC_A_POCHE, V_CREMA, V_FORNO, V_CIOCCOLATO, V_FARINA = (
    'pexels-video-6138114', 'pexels-video-7525329', 'pexels-video-7964890', 'pexels-video-6516822',
    'pexels-video-4458588', 'pexels-video-8626676')
PHOTOS = [
    'pexels-19498995.jpg', 'pexels-19499004.jpg', 'pexels-13218486.jpg', 'pexels-30233156.jpg', 'pexels-25916370.jpg',
    'pexels-11522869.jpg', 'pexels-39240989.jpg', 'pexels-19859271.jpg', 'pexels-31228824.jpg', 'pexels-29380149.jpg',
    'pexels-35985224.jpg', 'pexels-31101247.jpg', 'pexels-30919066.jpg', 'pexels-20264758.jpg', 'pexels-38935420.jpg',
    'pexels-6346240.jpg', 'pexels-20272490.jpg', 'pexels-4129384.jpg', 'pexels-3850347.jpg', 'pexels-20002837.jpg',
    'pexels-8629101.jpg', 'pexels-5964529.jpg', 'pexels-28146801.jpg', 'pexels-5662087.jpg', 'pexels-973238.jpg',
    'pexels-33106880.jpg', 'pexels-7474117.jpg', 'pexels-8625940.jpg', 'pexels-18532492.jpg', 'pexels-16402082.jpg',
    'pexels-30919086.jpg', 'pexels-38981540.jpg', 'pexels-30667454.jpg', 'pexels-40408086.jpg', 'pexels-835740.jpg',
    'pexels-12125085.jpg', 'pexels-25947327.jpg', 'pexels-21558659.jpg', 'pexels-40076591.jpg',
]
VIDEOS = [V_ZUCCHERO, V_SAC_A_POCHE, V_CREMA, V_FORNO, V_CIOCCOLATO, V_FARINA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=18, peso_titoli='400', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media=mix('var(--olo-color-accent)', 55, 'var(--olo-color-light)'))
C = K.c
TITOLI, MONO = 'Castoro', 'Azeret Mono'
VOCI = [('map-pin', 'Via Vanchiglia 18 · Torino', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Zucchero a Velo', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Zucchero a Velo', 'Pasticceria in Vanchiglia dal 1987. Il forno si accende alle quattro, la '
                   'vetrina è piena alle sette, le torte si ordinano entro le sei del giorno prima.',
                   [('La vetrina', VETRINA), ('Le torte', TORTE), ('Contatti', CONTATTI), ('Dove siamo', CONTATTI + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Gli orari', 'Martedì — sabato 7 — 19:30<br>Domenica 7:30 — 13<br>Lunedì il forno riposa', VOCI,
                   '© 2026 Zucchero a Velo di Ferro &amp; Gilli snc · Torino · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=120,
                   fondo=C['sala'])


def apertura(t):
    m = K.mob
    # (occhiello, riga 1, riga 2 in corsivo, testo, prezzo, fondo da, fondo a, arco foto|video, tondo)
    vetrina = [
        ('N° 1 · Lo zucchero a velo', 'Leggero', 'come un velo.', 'Bignè alla chantilly, crostate di frutta, croissant al '
         'burro: tutto fatto stanotte, nel laboratorio dietro la vetrina.', 'Dalle 7 · ogni mattina',
         mix(C['sec'], 22, C['fondo']), C['fondo'], ('video', V_ZUCCHERO), 'pexels-8625940.jpg'),
        ('N° 2 · Il bignè', 'Cento bignè,', 'uno per uno.', 'Pasta choux cotta due volte e chantilly alla vaniglia del '
         'Madagascar, montata alle sei. Si mangia entro sera.', '2,40 € · al pezzo',
         mix(C['acc'], 12, C['fondo']), C['fondo'], ('foto', 'pexels-38981540.jpg'), 'pexels-16402082.jpg'),
        ('N° 3 · La crostata', 'Frutta', 'di stamattina.', 'Frolla al burro, crema pasticcera e la frutta del mercato di '
         'Porta Palazzo, scelta alle cinque. Cambia con la stagione.', '28 € · per sei',
         mix(C['acc2'], 34, C['fondo']), C['fondo'], ('foto', 'pexels-835740.jpg'), 'pexels-12125085.jpg'),
        ('N° 4 · Il croissant', 'Ventisette', 'strati.', 'Burro francese piegato a mano ventisette volte, tre giorni '
         'di lievitazione. Esce dal forno alle sei e quaranta.', '1,60 € · al pezzo',
         mix(C['acc'], 8, mix(C['acc2'], 30, C['fondo'])), C['fondo'], ('foto', 'pexels-20002837.jpg'), 'pexels-4129384.jpg'),
    ]
    sl = []
    for i, (occ, r1, r2, testo, prezzo, da, a, arco, tondo) in enumerate(vetrina):
        n = i + 1
        cornice = {'x': 55, 'y': 11, 'w': 30, 'h': 78, 'borderRadiusLinked': False, 'borderRadiusTL': 216,
                   'borderRadiusTR': 216, 'borderRadiusBR': 22, 'borderRadiusBL': 22, 'entra': 'maskFromBottom',
                   'ritardo': 150, 'durata': 1300, 'esce': 'fadeOut', 'parallaxDepth': 2,
                   'boxShadow': {'x': 0, 'y': 30, 'blur': 60, 'spread': -20, 'color': mix(C['nero'], 30)},
                   'responsive': {'mobile': {'x': 12, 'y': 8.5, 'width': 76, 'height': 39}}}
        if arco[0] == 'video':
            foto_arco = K.livello('zv-a%d' % n, tipo='video', videoSrc=K.img(arco[1] + '.mp4'), videoAutoplay=True,
                                  videoMuted=True, videoLoop=True, **cornice)
        else:
            foto_arco = K.livello('zv-a%d' % n, tipo='image', imageSrc=K.img(arco[1]), objectFit='cover',
                                  kenBurns={'type': 'zoomIn', 'intensity': 8, 'duration': 9000}, **cornice)
        sl.append(K.diapositiva('zv-%d' % n, {'type': 'gradient', 'gradientFrom': da, 'gradientTo': a, 'gradientAngle': 125,
                                              'image': '', 'video': '', 'color': '', 'overlay': '', 'overlayOpacity': 0,
                                              'kenBurns': False}, [
            foto_arco,
            K.livello('zv-d%d' % n, tipo='image', imageSrc=K.img(tondo), objectFit='cover', x=48, y=60, w=12, h=19,
                      borderRadius=999, borderWidth=7, borderColor=C['chiaro'], entra='popUpBack', ritardo=900, durata=900,
                      esce='fadeOut', animLoop='float', animLoopDuration=5200, parallaxDepth=6,
                      boxShadow={'x': 0, 'y': 18, 'blur': 40, 'spread': -12, 'color': mix(C['nero'], 35)},
                      responsive={'mobile': {'x': 6, 'y': 35, 'width': 28, 'height': 13}}),
            K.livello('zv-o%d' % n, contenuto=occ, x=7, y=24, w=40, size=12, peso='500', colore=C['acc'], ritardo=200,
                      letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                      responsive={'mobile': {'fontSize': m(11), 'y': 51, 'x': 8, 'width': 84}}),
            K.livello('zv-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=7, y=28.5, w=47, size=100, peso='400',
                      colore=C['testo'], ritardo=320, durata=1200, lineHeight=1.0, entra='fadeIn', fontFamily=TITOLI,
                      charAnim={'stagger': 32, 'offsetY': 36, 'blur': 8, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(46), 'y': 53.6, 'x': 8, 'width': 86}}),
            K.livello('zv-i%d' % n, contenuto=r2, tag='p', x=7, y=40, w=47, size=100, peso='400', fontStyle='italic',
                      colore=C['acc'], ritardo=620, durata=1200, lineHeight=1.0, entra='fadeIn', fontFamily=TITOLI,
                      charAnim={'stagger': 32, 'offsetY': 36, 'blur': 8, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(46), 'y': 58.4, 'x': 8, 'width': 86}}),
            K.livello('zv-s%d' % n, contenuto=testo, x=7, y=56, w=33, size=18, peso='400', colore=C['tenue'],
                      ritardo=1150, lineHeight=1.6, responsive={'mobile': {'fontSize': m(15), 'y': 64.5, 'x': 8, 'width': 84}}),
            K.livello('zv-b%d' % n, tipo='button', contenuto='Prenota per domani', x=7, y=71, w='auto', size=14, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, borderRadius=999, ritardo=1400,
                      buttonUrl=CONTATTI + '#ordina', hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 75, 'x': 8, 'fontSize': m(13)}}),
            K.livello('zv-p%d' % n, contenuto=prezzo, x=23.5, y=72.3, w='auto', size=13, peso='500', colore=C['testo'],
                      bgColor=C['chiaro'], padding=11, borderRadius=999, fontFamily=MONO, entra='popUpBack', ritardo=1650,
                      durata=700, boxShadow={'x': 0, 'y': 8, 'blur': 22, 'spread': -10, 'color': mix(C['nero'], 30)},
                      responsive={'mobile': {'y': 81.2, 'x': 8, 'fontSize': m(12)}}),
        ], durata=7000))
    globali = []
    # granelli di zucchero che scendono piano davanti all'arco
    for k, (x, y, sz, anim, dur) in enumerate([(52, 14, 0.42, 'drift', 7400), (60, 9, 0.3, 'float', 5200),
                                              (68, 22, 0.5, 'drift', 8800), (77, 12, 0.36, 'float', 6100),
                                              (86, 30, 0.44, 'wander', 9600), (90, 52, 0.3, 'drift', 7000),
                                              (58, 46, 0.34, 'float', 5600), (83, 70, 0.4, 'wander', 8200),
                                              (64, 84, 0.3, 'float', 6400), (73, 58, 0.26, 'drift', 7800)]):
        globali.append(K.livello('zv-g%d' % k, tipo='shape', x=x, y=y, w=sz, h=sz * 1.6, bgColor=C['chiaro'],
                                 borderRadius=999, entra='fadeIn', ritardo=500 + 140 * k, durata=1600, opacity=95,
                                 animLoop=anim, animLoopDuration=dur, parallaxDepth=5 + k % 4,
                                 boxShadow={'x': 0, 'y': 1, 'blur': 6, 'spread': 0, 'color': mix(C['nero'], 22)},
                                 responsive={'mobile': {'visible': False}}))
    # la vetrina: i quattro tondi che portano al dolce
    globali.append(K.livello('zv-vl', contenuto='La vetrina · tocca un dolce', x=7, y=83, w=30, size=11, peso='500',
                             colore=C['tenue'], letterSpacing=2, textTransform='uppercase', fontFamily=MONO, entra='fadeIn',
                             ritardo=1800, responsive={'mobile': {'y': 86.6, 'x': 8, 'width': 84, 'fontSize': m(10)}}))
    for k, foto in enumerate(['pexels-8625940.jpg', 'pexels-38981540.jpg', 'pexels-835740.jpg', 'pexels-20002837.jpg']):
        globali.append(K.livello('zv-v%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=7 + k * 4.4, y=86.5,
                                 w=3.6, h=5.8, borderRadius=999, borderWidth=3, borderColor=C['chiaro'], entra='popUpBack',
                                 ritardo=1900 + 120 * k, durata=700, cursor='pointer',
                                 action={'type': 'goToSlide', 'target': str(k)}, hover={'scale': 1.14, 'duration': 220},
                                 boxShadow={'x': 0, 'y': 6, 'blur': 16, 'spread': -6, 'color': mix(C['nero'], 35)},
                                 responsive={'mobile': {'x': 8 + k * 14, 'y': 88.4, 'width': 11, 'height': 5}}))
    return K.proslider(t, sl, globali=globali, transizione='zoomFade', durata_trans=1100, showArrows=False, showDots=False,
                       parallax=True, parallaxIntensity=5, navColor=C['testo'], autoplaySpeed=7000,
                       globalBackground={'type': 'color', 'color': C['fondo']})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Martedì — sabato', 'time': '7 — 19:30', 'note': 'I croissant escono alle 6:40'},
                  {'day': 'Domenica', 'time': '7:30 — 13', 'note': 'Le paste della domenica: ordinatele il venerdì'},
                  {'day': 'Lunedì', 'time': 'Chiuso', 'note': 'Il forno riposa, noi andiamo al mercato'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': C['acc'], 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 44,
        'time_weight': '400', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Ogni mattina alle sei', ['Il forno si accende', 'quando la città dorme.'], size=60,
                                layout='stack', size_mobile=38),
                      K.text(t, 'Davide entra in laboratorio alle quattro: sfoglia, choux, frolla e lievitati, nell’ordine in '
                                'cui finiranno in vetrina. Alle sette apriamo, alle dieci i bignè sono già la metà.',
                             size=17, color=C['testo'])], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-8629101.jpg', 'Il pasticcere stende la pasta sul banco infarinato', ratio='4/5',
                              tilt=True)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
        t.one([orari], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    vetrina = t.classic('carousel', {
        'slides': [{'image_url': K.img(f), 'image_alt': a, 'link_url': VETRINA, 'caption': c} for f, a, c in [
            ('pexels-18532492.jpg', 'Bignè dorati su un piatto', 'Bignè alla chantilly · 2,40 €'),
            ('pexels-31101247.jpg', 'Pasticceria mignon assortita', 'Il vassoio mignon · 38 € al chilo'),
            ('pexels-35985224.jpg', 'Canelé al cioccolato su un’alzata', 'Canelé al rum · 2,20 €'),
            ('pexels-12125085.jpg', 'Crostatine di frutta', 'Crostatine di frutta · 3 €'),
            ('pexels-30919086.jpg', 'Éclair in vetrina', 'Éclair al caffè · 3,20 €'),
            ('pexels-30919066.jpg', 'Croissant al pistacchio e al cioccolato', 'Croissant ripieni · 1,90 €'),
            ('pexels-7474117.jpg', 'Cannoli con la crema', 'Cannoli alla ricotta · 2,80 €'),
            ('pexels-16402082.jpg', 'Paris-Brest alla crema di nocciola', 'Paris-Brest · 4,50 €')]],
        'slides_to_show': 3, 'gap': 22, 'autoplay': True, 'autoplay_speed': 3400, 'show_arrows': True, 'show_dots': False,
        'loop': True, 'pause_on_hover': True, 'slide_height': 'fixed', 'fixed_height': 460,
        'border_radius': {'tl': 240, 'tr': 240, 'br': 22, 'bl': 22}, 'border_radius_hover': {'tl': 22, 'tr': 22, 'br': 22, 'bl': 22},
        'arrow_color': C['on_acc'], 'arrow_bg': C['acc'], 'show_caption': True, 'caption_color': C['testo'],
        'caption_bg': mix(C['chiaro'], 88), 'object_fit': 'cover', 'mobile_slides': 1}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'La vetrina di oggi', ['Quello che è uscito', 'dal forno stamattina.'], size=60,
                         tagline='Passateci sopra: la cornice si apre. I prezzi sono quelli al banco, il vassoio si incarta '
                                 'col nastro lampone.')]),
        t.one([vetrina]),
        t.one([K.button(t, 'Tutta la vetrina', VETRINA, kind='pieno', align='center', pad_top=40)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_CREMA, 'Il sac à poche', 'Fatti a mano, <em>uno per uno.</em>',
                             'Nessuna macchina riempie i nostri bignè: la chantilly la mette Giulia, col sac à poche, '
                             'trecento volte ogni mattina.', overlay=66))
    occasioni = t.classic('finder', {
        'eyebrow': 'Per quale occasione', 'heading': 'Che dolce <em>portiamo</em>?',
        'intro': 'Scegliete il momento: vi diciamo cosa prendere e quanto ordinarne.',
        'items': [
            {'option': 'Colazione', 'icon': 'croissant', 'kicker': 'Dalle 7', 'title': 'Croissant e cappuccino',
             'text': 'Il croissant al burro esce alle 6:40. Al banco con il caffè della torrefazione di via Po, o nel '
                     'sacchetto di carta per l’ufficio.', 'meta': '1,60 € al pezzo', 'cta_text': 'La colazione',
             'cta_url': VETRINA + '#colazione', 'image': K.img('pexels-21558659.jpg'), 'media_label': ''},
            {'option': 'Pranzo della domenica', 'icon': 'cake-slice', 'kicker': 'Su ordinazione',
             'title': 'Il vassoio della domenica', 'text': 'Bignè, cannoncini, crostatine e meringhe: un vassoio da otto '
                     'persone, incartato col nastro. Ordinatelo entro il venerdì.', 'meta': 'Da 32 €',
             'cta_text': 'Ordina il vassoio', 'cta_url': CONTATTI + '#ordina', 'image': K.img('pexels-31101247.jpg'),
             'media_label': ''},
            {'option': 'Compleanno', 'icon': 'party-popper', 'kicker': 'Tre giorni prima', 'title': 'Una torta su misura',
             'text': 'Pan di Spagna, crema, frutta o cioccolato: la scegliamo insieme, la scriviamo come volete voi.',
             'meta': 'Da 32 € al chilo', 'cta_text': 'Le torte', 'cta_url': TORTE, 'image': K.img('pexels-19498995.jpg'),
             'media_label': ''},
            {'option': 'Un regalo', 'icon': 'gift', 'kicker': 'Pronta in vetrina', 'title': 'La scatola dei macaron',
             'text': 'Dodici macaron in sei gusti, nella scatola color panna. Si conservano tre giorni in frigorifero.',
             'meta': '18 € la scatola', 'cta_text': 'La vetrina', 'cta_url': VETRINA + '#macaron',
             'image': K.img('pexels-38935420.jpg'), 'media_label': ''},
            {'option': 'Senza glutine', 'icon': 'wheat-off', 'kicker': 'Il giovedì', 'title': 'Il forno senza glutine',
             'text': 'Il giovedì il laboratorio lavora solo farine senza glutine: crostate, amaretti e la torta di nocciole.',
             'meta': 'Da prenotare entro il martedì', 'cta_text': 'Scrivici', 'cta_url': CONTATTI + '#ordina',
             'image': K.img('pexels-5662087.jpg'), 'media_label': ''}],
        'zone_accent': C['acc'], 'zone_on': C['on_acc'], 'card_bg': C['chiaro'], 'chip_bg': C['chiaro'], 'chip_radius': 999,
        'card_radius': 26, 'media_ratio': '4/5', 'align': 'center', 'default_index': '0', 'shadow': 'lg'}, style={})
    out.append(t.section([t.one([occasioni])], padding='large', width='large', html_id='occasioni',
                         style={'padding_top': 130, 'padding_bottom': 130}))
    clienti = t.classic('northquoteslider', {
        'heading': 'Dal quaderno al banco',
        'items': [
            {'quote': 'Vengo qui da quando ero bambina: la crostata della domenica ha ancora il sapore di quella di allora.',
             'author_name': 'Margherita', 'author_role': 'Vanchiglia · dal 1991', 'logo_text': ''},
            {'quote': 'Abbiamo ordinato la torta del matrimonio senza sapere niente di torte. Davide ci ha fatto assaggiare '
                      'tutto, due volte.', 'author_name': 'Luca e Anna', 'author_role': 'Torta nuziale · giugno 2026',
             'logo_text': ''},
            {'quote': 'Il croissant delle sette è il motivo per cui prendo il tram una fermata prima.',
             'author_name': 'Pietro', 'author_role': 'Cliente del mattino', 'logo_text': ''}],
        'slant': True, 'autoplay': True, 'autoplay_speed': 7, 'graphic_label': 'Zucchero a Velo · il quaderno dei clienti',
        'bg_color': 'transparent', 'heading_color': C['testo'], 'quote_color': C['testo'], 'author_color': C['acc'],
        'role_color': C['tenue'], 'logo_color': C['testo'], 'arrow_color': C['testo'], 'graphic_color': C['sec'],
        'graphic_line_color': C['fondo'], 'quote_size': 38}, style={})
    out.append(t.section([t.one([clienti])], bg=solid(C['sala']), padding='large', width='large',
                         style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_FORNO, 'Alle 6:40', 'Il croissant <em>esce adesso.</em>',
                          'Ventisette strati di burro, tre giorni di lievitazione. Se arrivate alle sette lo trovate ancora '
                          'tiepido, e il profumo arriva fino in corso San Maurizio.', 'Prenota per domani',
                          CONTATTI + '#ordina', overlay=66, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Zucchero a Velo',
                page_intro_caption='Pasticceria · Torino', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def lista(t, voci):
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in voci],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 30), 'title_color': C['testo'],
        'price_color': C['acc'], 'description_color': C['tenue'], 'image_size': 76, 'image_border_radius': '999',
        'show_image': True, 'price_position': 'right', 'badge_bg': C['acc2'], 'badge_color': C['testo'],
        'badge_border_radius': '999', 'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'},
        style={})


def build_vetrina():
    t = Tpl(ID, 'la-vetrina')
    out = [K.intro(t, 'La vetrina', ['Tutto quello', 'che c’è al banco.'],
                   'I prezzi sono quelli di oggi. Quello che finisce non si rifà: il pomeriggio la vetrina è più corta, '
                   'ed è giusto così.', bg=K.photo_bg('pexels-30667454.jpg', overlay=80, color=C['fondo']))]
    gruppi = [
        ('colazione', 'Dalle 7', 'La colazione', [
            ('Croissant al burro', 'Burro francese, tre giorni di lievitazione', '1,60 €', 'pexels-20002837.jpg', ''),
            ('Croissant ripieno', 'Pistacchio, crema o cioccolato fondente', '1,90 €', 'pexels-30919066.jpg', ''),
            ('Girella all’uvetta', 'Crema pasticcera e uvetta sultanina', '1,80 €', 'pexels-29380149.jpg', ''),
            ('Fagottino zuccherato', 'Sfoglia e zucchero a velo, come una volta', '1,50 €', 'pexels-4129384.jpg', 'Il più chiesto')]),
        ('pasticceria', 'Al pezzo e al chilo', 'La pasticceria', [
            ('Bignè alla chantilly', 'Choux cotta due volte, vaniglia del Madagascar', '2,40 €', 'pexels-18532492.jpg', 'Della casa'),
            ('Éclair al caffè', 'Crema al caffè della torrefazione di via Po', '3,20 €', 'pexels-30919086.jpg', ''),
            ('Paris-Brest', 'Crema mousseline alla nocciola del Piemonte', '4,50 €', 'pexels-16402082.jpg', ''),
            ('Cannolo alla ricotta', 'Ricotta di pecora e scorza d’arancia candita', '2,80 €', 'pexels-7474117.jpg', ''),
            ('Mignon assortiti', 'Il vassoio da incartare col nastro', '38 € al kg', 'pexels-31101247.jpg', '')]),
        ('macaron', 'In scatola', 'I macaron', [
            ('Scatola da sei', 'Lampone, pistacchio, vaniglia, cioccolato, limone, caffè', '9 €', 'pexels-38935420.jpg', ''),
            ('Scatola da dodici', 'Due per gusto, nella scatola color panna', '18 €', 'pexels-20272490.jpg', 'Da regalare')]),
    ]
    for k, (aid, occ, titolo, voci) in enumerate(gruppi):
        out.append(t.section([
            t.row([t.col([K.sechead(t, occ, [titolo], size=64, layout='stack', size_mobile=40),
                          K.text(t, 'Prezzi al banco, IVA compresa. Per più di venti pezzi chiamateci il giorno prima.')],
                         wm='1-3', ws='1-1'),
                   t.col([lista(t, voci)], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 else None,
            style={'padding_top': 110, 'padding_bottom': 110}))
    carte = []
    for foto, nome, sotto, retro in [
        ('pexels-35985224.jpg', 'Canelé', 'Bordeaux, via Torino', 'Rum agricolo e vaniglia, cotti negli stampi di rame. '
         'Fuori croccanti, dentro morbidi: si fanno solo il sabato.'),
        ('pexels-973238.jpg', 'Bundt della nonna', 'La ricetta del 1987', 'Burro, limone e zucchero a velo. La torta che '
         'la signora Gilli faceva per il caffè delle quattro.'),
        ('pexels-33106880.jpg', 'Sfoglia ai frutti di bosco', 'Solo d’estate', 'Sfoglia caramellata, crema leggera e i '
         'frutti di bosco della Val Pellice.')]:
        carte.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 58}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': sotto, 'front_text_color': C['chiaro'], 'front_valign': 'bottom',
            'front_overlay': '', 'back_title': nome, 'back_description': retro, 'back_bg': C['chiaro'],
            'back_text_color': C['testo'], 'back_valign': 'center', 'back_cta_text': 'Prenota', 'back_cta_url': CONTATTI + '#ordina',
            'back_cta_bg': C['acc'], 'back_cta_color': C['on_acc'], 'back_cta_radius': '999', 'flip_direction': 'horizontal',
            'flip_trigger': 'hover', 'card_height': '440', 'card_border_radius': '32', 'card_shadow': 'md',
            'title_size': '26', 'title_weight': '400', 'desc_size': '14'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le specialità', ['Tre dolci', 'che facciamo solo noi.'], size=60,
                         tagline='Passate sopra la cornice: dietro c’è la storia.')]),
        t.row(carte, layout='33-33-33', gap=24),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Al banco', 'e al telefono.', [
        ('Avete dolci senza glutine?', 'Il giovedì il laboratorio lavora solo farine senza glutine. Gli altri giorni non '
                                       'possiamo garantire l’assenza di tracce.'),
        ('Si può prenotare la colazione?', 'Sì: scriveteci entro le sei del giorno prima e la trovate incartata col '
                                           'vostro nome.'),
        ('Gli allergeni?', 'Sono scritti sul cartellino di ogni dolce e nel quaderno al banco. Chiedeteci pure.'),
        ('Fate consegne?', 'In Vanchiglia, Borgo Po e centro, per ordini da 30 €, il sabato e la domenica mattina.'),
    ], 'vt', bg=True))
    return out


def build_torte():
    t = Tpl(ID, 'le-torte')
    out = [K.intro(t, 'Le torte', ['Su misura,', 'da assaggiare prima.'],
                   'Per un compleanno, un battesimo, un matrimonio: la torta la decidiamo insieme, al tavolino accanto '
                   'alla vetrina, con tre assaggi e un caffè.', bg=K.photo_bg('pexels-30233156.jpg', overlay=80, color=C['fondo']))]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Ci raccontate la festa', 'description': 'Quante persone, che giorno, cosa vi '
                   'piace. Al telefono o al banco, cinque minuti.'},
                  {'number': '02', 'title': 'Assaggiate tre basi', 'description': 'Pan di Spagna, frolla o sfoglia, con le '
                   'creme e la frutta di stagione. L’assaggio è offerto.'},
                  {'number': '03', 'title': 'La disegniamo', 'description': 'Un bozzetto a matita con la scritta e le '
                   'decorazioni. Si cambia fino a quando vi piace.'},
                  {'number': '04', 'title': 'La ritirate fresca', 'description': 'Pronta la mattina della festa, nella '
                   'scatola rigida. Per le torte a piani la portiamo noi.'}],
        'columns': 4, 'gap': 18, 'number_style': 'plain', 'number_color': C['acc'], 'number_size': 40, 'number_font': 'heading',
        'number_weight': '400', 'title_color': C['testo'], 'title_size': 22, 'title_weight': '400', 'title_font': 'heading',
        'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left', 'card_bg': C['chiaro'], 'card_border': '',
        'card_radius': {'tl': 22, 'tr': 22, 'br': 22, 'bl': 22}, 'card_padding': 28, 'columns_tablet': 2, 'columns_mobile': 1},
        style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Come si ordina', ['Quattro passi,', 'almeno tre giorni prima.'], size=60)]),
        t.one([passi]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    preventivo = t.classic('builder', {
        'eyebrow': 'La vostra torta', 'heading': 'Quanto viene?',
        'intro': 'Mettete le persone e le aggiunte: il conto si fa da sé. Il prezzo vero lo scriviamo dopo l’assaggio.',
        'currency': '€ ', 'cap': 0,
        'items': [{'name': 'Fetta di torta classica', 'price': '4', 'note': 'Pan di Spagna, crema e frutta · a persona', 'start': 12},
                  {'name': 'Piano in più', 'price': '35', 'note': 'Per le torte da oltre trenta persone', 'start': 0},
                  {'name': 'Scritta in pasta di zucchero', 'price': '8', 'note': 'Nome, data, una frase breve', 'start': 1},
                  {'name': 'Fiori freschi edibili', 'price': '15', 'note': 'Violette, rose e fiori di sambuco', 'start': 0},
                  {'name': 'Vassoio di mignon', 'price': '32', 'note': 'Per otto persone, accanto alla torta', 'start': 0}],
        'total_label': 'Totale indicativo', 'count_label': 'voci', 'count_label_one': 'voce', 'cta_text': 'Mandaci la torta',
        'cta_url': '', 'send_mode': 'mail', 'send_to': MAIL, 'send_subject': 'Una torta su misura',
        'send_intro': 'Vorremmo una torta con:', 'zone_accent': C['acc'], 'zone_on': C['on_acc'], 'card_bg': C['chiaro'],
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']}, 'align': 'left',
        'layout': 'panel'}, style={})
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-25916370.jpg', 'Una torta bianca a piani con i fiori rosa', ratio='4/5', tilt=True)],
                     wm='2-5', ws='1-1'),
               t.col([preventivo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', html_id='quanto', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Fatte da noi', ['Qualche torta', 'dell’ultimo anno.'], size=56)]),
        t.row([t.col([K.image(t, f, a, ratio='3/4', lightbox=True)], wm='1-4', ws='1-2', width_default='1-2') for f, a in [
            ('pexels-19498995.jpg', 'Torte glassate in vetrina'), ('pexels-13218486.jpg', 'Un tavolo di dolci per una festa'),
            ('pexels-11522869.jpg', 'Piccole torte colorate'), ('pexels-39240989.jpg', 'Fette di torta al pistacchio e cioccolato')]],
              layout='25-25-25-25', gap=16),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SAC_A_POCHE, 'L’ultimo tocco', 'La crema <em>la mettiamo all’ultimo.</em>',
                             'Così il pan di Spagna resta soffice e la panna non si siede. Per questo la torta si ritira la '
                             'mattina della festa.', overlay=66,
                             extra=[K.button(t, 'Prenota l’assaggio', CONTATTI + '#ordina', kind='pieno', align='center',
                                             pad_top=18)]))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Ordinate entro le sei,', 'ritirate domattina.'],
                   'Torte, vassoi, colazioni da portar via: scriveteci qui o chiamateci. Rispondiamo tra un’infornata e '
                   'l’altra, di solito entro un’ora.', bg=K.photo_bg('pexels-40076591.jpg', overlay=80, color=C['fondo']))]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('date', 'Per quando', 'data', True),
             K.campo('select', 'Cosa vi serve', 'cosa', opzioni='Torta su misura\nVassoio della domenica\nColazione da '
                                                                'portar via\nMacaron in scatola\nAltro'),
             K.campo('text', 'Per quante persone', 'persone', segnaposto='Es. 12'),
             K.campo('textarea', 'Raccontateci', 'messaggio', larghezza='1-1',
                     segnaposto='Gusti, allergie, la scritta sulla torta.')]
    out.append(K.sezione_modulo(t, 'ordina', 'Ordina', ['Ditecelo,', 'lo prepariamo.'],
                                'Per le torte su misura servono almeno tre giorni. Per i vassoi basta il giorno prima.',
                                VOCI, campi, 'Zucchero a Velo — ordine', 'Manda l’ordine',
                                'Grazie! Vi richiamiamo per confermare il ritiro.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['In Vanchiglia,', 'sotto i portici.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via Vanchiglia 18, Torino'), ('Tram', '15 e 55, fermata Vanchiglia'),
                                  ('Martedì — sabato', '7 — 19:30'), ('Domenica', '7:30 — 13'), ('Lunedì', 'chiuso')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Zucchero a Velo · pasticceria', scuro=False, hex_marker=HEX['primary'],
                              zoom='16')], wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · vetrina vuota', color=K.acc_media, align='center'),
                       K.headline(t, 'Finita, come i bignè <em>della domenica.</em>', size=88, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Questa pagina non c’è più. In vetrina invece qualcosa è rimasto.', size=17,
                              align='center', color=K.soft_media),
                       K.button(t, 'Torna alla vetrina', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ZUCCHERO, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'Leggero come un velo',
    ['pasticceria', 'torte', 'colazione', 'torino', 'chiaro', 'pastello', 'slider pro'],
    'Una pasticceria in Vanchiglia, panna e lampone col verde pistacchio: la vetrina in apertura che cambia colore a '
    'ogni dolce, con la foto nell’arco, il particolare che galleggia e i tondi da toccare; il dolce per ogni occasione, '
    'il carosello del banco, le specialità che si girano e il conto della torta su misura.',
    [('La vetrina', VETRINA), ('Le torte', TORTE), ('Occasioni', HOME + '#occasioni'), ('Ordina', CONTATTI)],
    [('home', 'Home', None), ('la-vetrina', 'La vetrina', 'la-vetrina'), ('le-torte', 'Le torte', 'le-torte'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Outfit\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Castoro\', \'Georgia\', serif',
    '\'Azeret Mono\', ui-monospace, Menlo, monospace', ['Castoro', 'Outfit', 'Azeret Mono'], corsivo_di=['Castoro'],
    peso_titoli='400', raggio=18, grana=0, titoli_line='1.0', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-vetrina.json': build_vetrina(), 'le-torte.json': build_torte(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
