# -*- coding: utf-8 -*-
"""Genera il tema «Persiana» (id persiana): un'agenzia immobiliare di Lucca che le case le racconta da dentro.

La trovata (18 nov 2026, utente: «un video che si perde sullo sfondo, svelando qualcos'altro»): nell'apertura il video
della facciata, a tutto schermo, scorrendo si allontana: rimpicciolisce, sale di lato e si perde sullo sfondo, e dietro
c'era il salotto. «Da fuori è una facciata. Da dentro è casa.» Lo Slider Pro fermo durante lo scorrimento, col livello
video che passa da scala 1 a 0,05.

Tile poco usate messe alla prova: Hotspots (mai usata; senza foto era un riquadro di prova: aggiunta la foto), Mixer (mai
usata) per la tinta delle persiane, Switcher Panel (mai usata) per i quartieri, Masked Video Hero (mai usata), Pannello
flottante, Slideshow.
Tema chiaro: intonaco, verde persiana, terracotta, ocra. Titoli in Bodoni Moda (col corsivo), testo in Manrope. Cursore:
persiana.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'persiana'
NAME = 'Persiana'
CATEGORIA = 'Immobiliare'

HEX = {
    'primary': '#2F5D50', 'primary_contrast': '#FFFFFF', 'secondary': '#C8643B', 'secondary_contrast': '#FFFFFF',
    'accent': '#E0B04A', 'dark': '#1C2421', 'light': '#FFFFFF', 'muted': '#EDE6DA', 'muted_contrast': '#25302C',
    'text': '#25302C', 'text_muted': '#5E6A64', 'background': '#F7F2E9', 'border': '#DCD2C2', 'link': '#2F5D50',
}
HOME, CASE, VENDI, AGENZIA = '{{PAGINA:home}}', '{{PAGINA:le-case}}', '{{PAGINA:vendi}}', '{{PAGINA:l-agenzia}}'
MAIL, PHONE, PHONE_TEL = 'ciao@persiana.example', '0583 000 112', 'tel:+390583000112'
LAT, LNG = '43.8430', '10.5050'

V_FACCIATA, V_SALOTTO, V_STRADA, V_FINESTRA, V_CUCINA, V_PRANZO = (
    'pexels-video-39272229', 'pexels-video-7118332', 'pexels-video-29808195', 'pexels-video-10238036', 'pexels-video-7614417',
    'pexels-video-5823595')
VIDEOS = [V_FACCIATA, V_SALOTTO, V_STRADA, V_FINESTRA, V_CUCINA, V_PRANZO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(FACCIATE, GIALLO, PERSIANE_MATTONE, PERSIANE_COLORI, PERSIANE_ROSSE, VUOTO_LEGNO, LUMINOSO, VUOTO_BIANCO, LUSSO,
 CUCINA_BIANCA, CUCINA_SCURA, CAMERA, SCALA_BIANCA, SCALA, BALCONE_FIORI, BALCONE_ARCHI, CHIAVI, CHIAVI_CASETTA, LOFT_VERDE,
 LOFT, COPPIA_SCATOLE, COPPIA) = f(6062808, 19466870, 14059296, 30475946, 17373417, 3284980, 6077368, 9826456, 5998120,
                                   7166645, 9757618, 6903157, 28272314, 27313203, 14965658, 19981282, 29094497, 7599735,
                                   7045921, 4703, 4554377, 7203720)
PHOTOS = [FACCIATE, GIALLO, PERSIANE_MATTONE, PERSIANE_COLORI, PERSIANE_ROSSE, VUOTO_LEGNO, LUMINOSO, VUOTO_BIANCO, LUSSO,
          CUCINA_BIANCA, CUCINA_SCURA, CAMERA, SCALA_BIANCA, SCALA, BALCONE_FIORI, BALCONE_ARCHI, CHIAVI, CHIAVI_CASETTA,
          LOFT_VERDE, LOFT, COPPIA_SCATOLE, COPPIA]
LOGHI = ['persiana-logo-chiaro.png', 'persiana-logo-scuro.png']
MEDIA = set(PHOTOS) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='600', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#E0B04A')
C = K.c
FONT_T = 'Bodoni Moda'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via Fillungo 112 · Lucca', AGENZIA + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 170, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '570/116',
                   'Persiana vende e affitta case a Lucca e nella Piana dal 1987. Le guardiamo da dentro: la luce, i '
                   'rumori, i metri veri.',
                   [('Le case', CASE), ('Vendi', VENDI), ('L’agenzia', AGENZIA), ('Privacy', '/privacy-policy/')],
                   'L’agenzia', 'Lunedì — venerdì 9 — 13 e 15 — 19<br>Sabato 9:30 — 12:30, solo su appuntamento', VOCI,
                   '© 2026 Persiana srl · Lucca · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                     ('facebook', 'https://facebook.com')], scuro=False)


# ── L'apertura: la facciata che si perde sullo sfondo ──
# Il livello video della facciata copre la tela; scorrendo rimpicciolisce (da 1 a 0,05), sale a destra e si spegne. Sotto
# c'è il video del salotto, sfondo della diapositiva. Gli spostamenti sono in px della tela del computer (1440 × 810).
def apertura(t):
    m = K.mob
    perdi = {'inizio': 0, 'fine': 100, 'tappe': [
        {'p': 0, 's': 1, 'x': 0, 'y': 0, 'r': 0, 'o': 100}, {'p': 12, 's': 1, 'x': 0, 'y': 0, 'r': 0, 'o': 100},
        {'p': 50, 's': 0.24, 'x': 360, 'y': -170, 'r': -5, 'o': 100}, {'p': 70, 's': 0.12, 'x': 430, 'y': -230, 'r': -8, 'o': 70},
        {'p': 88, 's': 0.05, 'x': 470, 'y': -270, 'r': -10, 'o': 0}, {'p': 100, 's': 0.05, 'x': 470, 'y': -270, 'r': -10, 'o': 0}]}
    poi = lambda da, a, via_da=None, via_a=None: {'inizio': 0, 'fine': 100, 'tappe': (
        [{'p': 0, 'o': 0, 'y': 14}, {'p': da, 'o': 0, 'y': 14}, {'p': a, 'o': 100, 'y': 0}]
        + ([{'p': via_da, 'o': 100, 'y': 0}, {'p': via_a, 'o': 0, 'y': -14}, {'p': 100, 'o': 0, 'y': -14}] if via_da
           else [{'p': 100, 'o': 100, 'y': 0}]))}
    via = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100, 'y': 0}, {'p': da, 'o': 100, 'y': 0},
                                                             {'p': a, 'o': 0, 'y': -16}, {'p': 100, 'o': 0, 'y': -16}]}
    L = [K.livello('ps-fuori', tipo='video', videoSrc=K.img(V_FACCIATA + '.mp4'), videoAutoplay=True, videoMuted=True,
                   videoLoop=True, x=0, y=0, w=100, h=100, entra='fadeIn', ritardo=0, durata=600, scrollMove=perdi,
                   customCSS='transform-origin:50% 50%;box-shadow:0 30px 80px rgba(0,0,0,.45);',
                   responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}})]
    L += K.veli('ps-velo', 'sinistra', 32, 62, colore=C['nero'], forza=84, mobile_alto=40)
    L += [
        K.livello('ps-o', contenuto='Persiana · agenzia immobiliare · Lucca', x=6, y=14, w=40, size=13, peso='600',
                  colore=mix(C['chiaro'], 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 5.4, 'width': 88}}),
        # fuori
        K.livello('ps-a1', contenuto='Da fuori', tag='h1', x=6, y=24, w=60, size=104, peso='600', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=1.0, entra='fadeInUp', ritardo=300, durata=900, scrollMove=via(16, 26),
                  responsive={'mobile': {'fontSize': m(54), 'x': 6, 'y': 9, 'width': 88}}),
        K.livello('ps-a2', contenuto='è una facciata.', x=6, y=38, w=60, size=104, peso='600', fontStyle='italic',
                  colore=C['acc2'], fontFamily=FONT_T, lineHeight=1.0, entra='fadeInUp', ritardo=450, durata=900,
                  scrollMove=via(16, 26), responsive={'mobile': {'fontSize': m(54), 'x': 6, 'y': 14.6, 'width': 88}}),
        K.livello('ps-as', contenuto='Scorri: ti facciamo entrare.', x=6, y=56, w=34, size=20, peso='500',
                  colore=mix(C['chiaro'], 90), lineHeight=1.4, ritardo=600, scrollMove=via(16, 26),
                  responsive={'mobile': {'fontSize': m(16), 'x': 6, 'y': 21.4, 'width': 84}}),
        # la facciata che se ne va: la sua etichetta
        K.livello('ps-tag', contenuto='La facciata · via Fillungo', x=62, y=10, w=30, size=12, peso='600', align='right',
                  colore=mix(C['chiaro'], 86), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, entra='fadeIn',
                  ritardo=0, scrollMove=poi(44, 52, 74, 84), responsive={'mobile': {'fontSize': m(10), 'x': 40, 'y': 33, 'width': 54}}),
        # dentro
        K.livello('ps-b1', contenuto='Da dentro', x=6, y=24, w=60, size=104, peso='600', colore=C['chiaro'], fontFamily=FONT_T,
                  lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=poi(32, 44),
                  responsive={'mobile': {'fontSize': m(54), 'x': 6, 'y': 9, 'width': 88}}),
        K.livello('ps-b2', contenuto='è casa.', x=6, y=38, w=60, size=104, peso='600', fontStyle='italic', colore=C['acc2'],
                  fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=poi(36, 48),
                  responsive={'mobile': {'fontSize': m(54), 'x': 6, 'y': 14.6, 'width': 88}}),
        K.livello('ps-bs', contenuto='Novantadue case a Lucca e nella Piana, raccontate da dentro: la luce alle quattro del '
                                     'pomeriggio, il rumore della strada, i metri veri.', x=6, y=56, w=34, size=19,
                  peso='500', colore=mix(C['chiaro'], 90), lineHeight=1.45, entra='fadeIn', ritardo=0, scrollMove=poi(46, 58),
                  responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 21.4, 'width': 86}}),
        K.livello('ps-b', tipo='button', contenuto='Le case', x=6, y=74, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=18, borderRadius=2, letterSpacing=2, textTransform='uppercase', buttonUrl=CASE,
                  entra='fadeIn', ritardo=0, scrollMove=poi(60, 70), hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 30.5, 'fontSize': m(12)}}),
        K.livello('ps-b2b', tipo='button', contenuto='Vendi con noi', x=17.5, y=74, w='auto', size=13, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=18, borderRadius=2, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), letterSpacing=2, textTransform='uppercase', buttonUrl=VENDI, entra='fadeIn',
                  ritardo=0, scrollMove=poi(60, 70), hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 48, 'y': 30.5, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('ps-1', K.sfondo_video(V_SALOTTO, overlay=0.16, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


CASE_VOCI = [
    (LUMINOSO, 'Centro storico · 4 vani', 'Il terzo piano con le travi', 'Via del Battistero, 112 m². Due finestre sulla '
     'piazza, il sole del pomeriggio in salotto. 480.000 €'),
    (CUCINA_BIANCA, 'Sant’Anna · 3 vani', 'La cucina che guarda il giardino', 'Villetta a schiera, 96 m² e un orto di '
     '60 m². Silenziosa la sera. 315.000 €'),
    (LOFT_VERDE, 'San Concordio · loft', 'L’ex magazzino del tabacco', 'Un unico ambiente alto cinque metri, 140 m², '
     'il soppalco in ferro. 390.000 €'),
    (BALCONE_FIORI, 'Arancio · 2 vani', 'Il balcone dei gerani', 'Secondo piano senza ascensore, 64 m², la vista sulle Mura. '
     'In affitto, 850 € al mese'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lucca · dal 1987', ['Le case', 'le guardiamo da dentro.'], size=76, layout='stack',
                                size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Una facciata ti dice poco: è la stessa per sei appartamenti. Noi entriamo, misuriamo, '
                                'apriamo le persiane alle quattro del pomeriggio e ti diciamo dove arriva il sole. Poi la '
                                'casa la vedi tu, con noi accanto.', size=17),
                      K.button(t, 'Le case in vendita', CASE, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'In vendita e in affitto', ['Quattro case', 'questa settimana.'], size=60)]),
        P.carte(K, t, CASE_VOCI, altezza='440', colonne=4),
    ], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 110}))
    # Hotspots: entra nel soggiorno (la foto sotto i segnaposto)
    out.append(t.section([t.one([t.bare('hotspots', {
        'eyebrow': 'Via del Battistero, terzo piano', 'heading': 'Entra nel soggiorno.',
        'intro': 'Tocca i punti: quello che una foto non dice, e che ti diciamo noi prima della visita.',
        'panel_label': '', 'image': K.img(LUSSO), 'image_alt': 'Il soggiorno chiaro con il lampadario e il divano bianco',
        'object_position': 'center center', 'aspect_ratio': '16/9',
        'items': [{'x': 30, 'y': 30, 'title': 'Le travi', 'text': 'Castagno, restaurate nel 2019. Il soffitto è a 3,40 m.',
                   'meta': 'Struttura'},
                  {'x': 62, 'y': 44, 'title': 'Le finestre', 'text': 'A ovest: il sole entra dalle 15 alle 19, d’estate fino '
                   'alle 20.', 'meta': 'Luce'},
                  {'x': 47, 'y': 74, 'title': 'Il pavimento', 'text': 'Cotto originale sotto il parquet, nella stanza accanto '
                   'lo abbiamo già scoperto.', 'meta': 'Sorpresa'},
                  {'x': 82, 'y': 66, 'title': 'Il rumore', 'text': 'La piazza la sera: la notte è quieta, il sabato c’è il '
                   'mercato fino alle 13.', 'meta': 'Vicini'}],
        'zone_accent': C['acc'], 'zone_on': C['chiaro'], 'card_bg': C['fondo'], 'align': 'left'})], anim=None)],
        bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    # Mixer: la tinta delle persiane
    out.append(t.section([
        t.row([t.col([t.bare('mixer', {
            'eyebrow': 'Il colore delle persiane', 'heading': 'Componi la tua tinta.',
            'intro': 'A Lucca le persiane si dipingono coi colori del centro storico: fondi due campioni e guarda che '
                     'verde viene. Il Comune approva quasi tutti quelli della tavolozza.',
            'max': 3, 'empty_label': 'Tocca due o tre campioni',
            'items': [{'name': 'Verde Lucca', 'color': '#2F5D50'}, {'name': 'Verde salvia', 'color': '#8BA888'},
                      {'name': 'Grigio tortora', 'color': '#A69E92'}, {'name': 'Ocra delle Mura', 'color': '#C9A04A'},
                      {'name': 'Rosso pompeiano', 'color': '#A3412F'}, {'name': 'Marrone castagna', 'color': '#5A3F2E'}],
            'zone_accent': C['acc'], 'card_bg': C['sala'], 'align': 'left'})], wm='1-2', ws='1-1'),
               t.col([K.image(t, PERSIANE_COLORI, 'Una facciata con le persiane di sei colori diversi', ratio='4/5')],
                     wm='1-2', ws='1-1')],
              layout='custom', custom='50,50', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    # Switcher Panel: i quartieri
    voce = lambda n, nav, tit, testo, foto: {'id': 'ps-q-%d' % n, 'nav_label': nav, 'title': tit, 'text': testo,
                                             'button_text': 'Le case in ' + nav.lower(), 'button_url': CASE, 'image': K.img(foto)}
    out.append(t.section([
        t.one([K.sechead(t, 'I quartieri', ['Dentro le Mura', 'e appena fuori.'], size=60)]),
        t.one([t.bare('switcherpanel', {
            'items': [voce(1, 'Centro', 'Dentro le Mura', 'Vicoli, torri, il mercato del sabato. Case alte, scale strette, '
                       'soffitti affrescati: si vive a piedi.', GIALLO),
                      voce(2, 'Sant’Anna', 'Le villette col giardino', 'Fuori Porta San Donato: scuole, campi da calcio, il '
                       'rumore delle biciclette.', CUCINA_BIANCA),
                      voce(3, 'Arancio', 'Le palazzine del dopoguerra', 'Balconi, condomini piccoli, i prezzi più bassi a un '
                       'chilometro dalle Mura.', BALCONE_ARCHI),
                      voce(4, 'San Concordio', 'I magazzini diventati case', 'L’ex Manifattura e i capannoni del tabacco: loft '
                       'alti, ferro e mattoni.', LOFT)],
            'hero_image': K.img(FACCIATE), 'hero_height': 420, 'panel_bg': C['fondo'], 'image_position': 'right', 'nav_style': 'minimal',
            'animation': 'fade', 'preset': 'editorial-overlay', 'nav_position': 'overlay', 'layout_mode': 'split',
            'button_style': 'primary'})], anim=None),
    ], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('Ci hanno detto loro che la cucina a nord d’inverno era buia. Abbiamo comprato quella accanto, ed è giusta.', 'Elisa e '
         'Marco', 'Sant’Anna'),
        ('Ho venduto la casa di mia madre in tre settimane, senza fare sessanta visite inutili.', 'Roberto', 'Centro'),
        ('Mi hanno mandato un video alle quattro del pomeriggio per farmi vedere la luce. Ero a Milano.', 'Giulia', 'Arancio')],
        'Chi è entrato', ['Tre case,', 'viste da dentro.']))
    pannello = K.pannello(t, CHIAVI_CASETTA, 'Le chiavi', 'La prossima casa <em>la apriamo insieme.</em>',
                          'Dicci che cosa cerchi: ti scriviamo quando entriamo in una casa che ti somiglia.', 'Scrivici',
                          AGENZIA + '#scrivi', effetto='')
    # Pannello flottante: prenota una visita (si apre dal pulsante in basso a destra)
    fp = t.bare('floatingpanel', {'position': 'fixed', 'placement': 'bottom-right', 'offset_x': '24', 'offset_y': '24',
                                  'width': '300', 'bg_color': C['fondo'], 'border_radius': '4', 'shadow': True,
                                  'trigger_mode': 'button', 'trigger_icon': 'key-round', 'trigger_size': '54',
                                  'trigger_bg': C['acc'], 'trigger_color': C['chiaro'], 'trigger_radius': '50',
                                  'show_close': True})
    fp['children'] = [K.label(t, 'Una visita', color=C['acc']),
                      K.text(t, 'Scegli il giorno: ti richiamiamo per l’ora, anche il sabato.', size=15),
                      K.button(t, 'Prenota una visita', AGENZIA + '#scrivi', kind='pieno', pad_top=10)]
    pannello['children'][0]['children'][0]['children'].append(fp)
    out.append(pannello)
    return out


def build_case():
    t = Tpl(ID, 'le-case')
    out = [K.intro(t, 'Le case', ['Novantadue case,', 'raccontate da dentro.'], 'Ogni scheda ha le misure vere, la luce ora '
                   'per ora e le cose che non vanno. Le visite le facciamo noi, mai con le chiavi lasciate in portineria.',
                   bg=K.video_bg(V_PRANZO, overlay=20), size=104)]
    out.append(t.section([
        t.one([K.sechead(t, 'In vendita', ['Le case', 'di questa settimana.'], size=60)]),
        P.carte(K, t, CASE_VOCI + [
            (CAMERA, 'Pescaglia · 5 vani', 'La casa in collina', 'Pietra e castagno, 180 m², un ettaro di bosco. Venti minuti '
             'da Lucca. 520.000 €'),
            (CUCINA_SCURA, 'Viareggio · 3 vani', 'Il bilocale vicino al mare', 'Ristrutturato nel 2024, 70 m², a 300 metri '
             'dalla spiaggia. 345.000 €'),
            (VUOTO_LEGNO, 'Capannori · 4 vani', 'Da finire a modo tuo', 'Al grezzo, 120 m², il giardino davanti e dietro. '
             '210.000 €'),
            (SCALA_BIANCA, 'Centro · duplex', 'La scala bianca', 'Ultimo piano e mansarda, 98 m², la terrazza sui tetti. '
             '455.000 €')], altezza='420', colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    # Slideshow: le persiane di Lucca
    out.append(t.section([
        t.one([K.sechead(t, 'Guardare in su', ['Le persiane', 'di Lucca.'], size=60)]),
        t.one([t.classic('slideshow', {
            'preset': 'custom', 'slides': [
                {'id': 'ps-s1', 'image': K.img(PERSIANE_MATTONE), 'title': 'Verde Lucca', 'subtitle': 'Via Santa Croce', 'link': ''},
                {'id': 'ps-s2', 'image': K.img(PERSIANE_ROSSE), 'title': 'Rosso pompeiano', 'subtitle': 'Borgo Giannotti', 'link': ''},
                {'id': 'ps-s3', 'image': K.img(FACCIATE), 'title': 'Tutti i colori', 'subtitle': 'Piazza dell’Anfiteatro', 'link': ''},
                {'id': 'ps-s4', 'image': K.img(PERSIANE_COLORI), 'title': 'Sei tinte, una casa', 'subtitle': 'Via Fillungo', 'link': ''}],
            'autoplay': True, 'autoplay_speed': '4500', 'show_arrows': True, 'show_dots': True, 'aspect_ratio': '21/9',
            'object_fit': 'cover', 'overlay_color': C['nero'], 'overlay_opacity': '22', 'text_color': C['chiaro'],
            'transition': 'fade'}, style={})], anim=None),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima della visita', ['Quello che', 'ci chiedete sempre.'], [
        ('Le misure sono vere?', 'Sì: le prendiamo noi col distanziometro, stanza per stanza. Le planimetrie del catasto le '
                                 'mettiamo accanto, quando non coincidono lo scriviamo.'),
        ('Posso vedere una casa la sera?', 'Certo: la luce e il rumore cambiano. Fissiamo la visita all’ora che ti serve.'),
        ('Fate anche affitti?', 'Sì, con contratti 3+2 e a canone concordato. Per gli affitti brevi no.')], 'psc'))
    return out


def build_vendi():
    t = Tpl(ID, 'vendi')
    out = [K.intro(t, 'Vendi con noi', ['La tua casa', 'vista da chi la compra.'], 'Prima di metterla in vendita la guardiamo '
                   'come la guarderà chi la cerca: la luce, i difetti da dire subito, la foto giusta della stanza giusta.',
                   bg=K.video_bg(V_FINESTRA, overlay=20), size=104)]
    out.append(t.section([t.one([t.bare('maskedvideohero', {
        'media_bg': {'type': 'video', 'video_url': K.img(V_STRADA + '.mp4'), 'video_poster': K.img(V_STRADA + '.jpg'),
                     'video_fit': 'cover', 'overlay_color': C['nero'], 'overlay_opacity': 30},
        'tag_text': 'Vendere a Lucca · 2026', 'headline_text': 'Trenta giorni,', 'accent_text': 'non trecento.',
        'uppercase': False, 'subhead': 'In media vendiamo in trentuno giorni: la casa giusta alla persona giusta, con meno visite '
                                       'e nessuna sorpresa al rogito.',
        'cta1_text': 'Chiedi una stima', 'cta1_url': VENDI + '#stima', 'cta2_text': 'Come lavoriamo', 'cta2_url': AGENZIA,
        'bg_color': C['nero'], 'overlay_color': C['nero'], 'overlay_strength': 0.35, 'watermark_text': 'LUCCA',
        'watermark_color': mix(C['chiaro'], 6), 'accent': C['acc2'], 'accent_on': C['nero'], 'text_color': C['chiaro'],
        'sub_color': mix(C['chiaro'], 82), 'arch': True, 'transparent_bg': False, 'min_height': 78,
        'media_label': ''})], anim=None)], padding='remove-vertical', width='fullbleed'))
    conf = X.preventivo(K, t, [
        ('dove', 'Dove', 'uno', True, 'Il prezzo al metro quadro di Lucca, del primo semestre 2026.'),
        ('quanto', 'Quanto è grande', 'uno', True, ''),
        ('come', 'Com’è', 'molti', False, '')], [
        ('centro', 'dove', 'Dentro le Mura', '3.400 € al m².', 0, '', True),
        ('fuori', 'dove', 'Appena fuori', '2.600 € al m².', 0, '', False),
        ('piana', 'dove', 'Nella Piana', '1.900 € al m².', 0, '', False),
        ('piccola', 'quanto', 'Fino a 70 m²', 'Bilocali e trilocali.', 210000, '', False),
        ('media', 'quanto', 'Da 70 a 120 m²', 'La misura più cercata.', 320000, '', True),
        ('grande', 'quanto', 'Oltre 120 m²', 'Case e ville.', 480000, '', False),
        ('giardino', 'come', 'Con giardino', '+8%.', 25000, '', False),
        ('ascensore', 'come', 'Con ascensore', '+5%.', 15000, '', False),
        ('rifare', 'come', 'Da ristrutturare', '−15%.', -45000, '', False)],
        'La tua stima', VENDI + '?stima={configurazione}#scrivi', 'Una cifra indicativa: la stima vera la facciamo in casa, '
        'gratis.', testo_pulsante='Chiedi la stima vera')
    out.append(X.sezione_preventivo(K, t, 'Una prima idea', ['Quanto vale', 'la tua casa?'], '', conf, html_id='stima'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Indirizzo della casa', 'indirizzo'),
             K.campo('text', 'La stima', 'stima'), K.campo('textarea', 'Raccontaci la casa', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Una stima in casa', ['Veniamo noi,', 'con il metro.'],
                                'Gratis, entro una settimana. Lasciaci un numero.', VOCI, campi, 'Persiana — stima', 'Invia',
                                'Grazie! Ti chiamiamo per fissare il giorno.'))
    return out


def build_agenzia():
    t = Tpl(ID, 'l-agenzia')
    out = [K.intro(t, 'L’agenzia', ['Dal 1987', 'in via Fillungo.'], 'Siamo in quattro, tutti di Lucca. La nostra vetrina è '
                   'la più piccola della via, e le case non le mettiamo in vetrina: te le raccontiamo seduti.',
                   bg=K.video_bg(V_CUCINA, overlay=22), size=104)]
    out.append(t.section([t.one([K.numeri(t, [('92', 'case in agenzia'), ('31', 'giorni per vendere, in media'),
                                              ('1987', 'l’anno della prima'), ('4', 'persone, tutte di Lucca')], colonne=4,
                                         size=58)])],
                         padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 90}))
    out.append(t.section([t.one([P.citazione(K, t, 'Una casa non si vende con la foto della facciata. Si vende con la luce '
                                                   'delle quattro del pomeriggio.', 'Anna Del Prete, fondatrice')])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Cerchi', 'cerchi', True, opzioni='Una casa da comprare\nUna casa in affitto\nDi vendere\nAltro'),
             K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivici', ['Ti richiamiamo', 'entro domani.'], 'Dal lunedì al sabato.', VOCI,
                                campi, 'Persiana — contatto', 'Invia', 'Grazie! Ti richiamiamo entro domani.'))
    out.append(X.zone(K, t, 'Dove siamo', ['In via Fillungo,', 'la vetrina piccola.'], 'Fra la Torre delle Ore e la chiesa di '
                      'San Cristoforo, al numero 112.', [('A piedi', 'da piazza San Michele, 4 minuti'),
                      ('In auto', 'parcheggio Mazzini, poi 6 minuti'), ('In treno', 'dalla stazione, 15 minuti'),
                      ('Telefono', PHONE)], LAT, LNG, 'Persiana · via Fillungo 112', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · persiana chiusa', color=C['acc2'], align='center'),
                       K.headline(t, 'Dietro questa <em>non c’è niente.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=C['acc2']),
                       K.text(t, 'La pagina che cercavi non c’è più: forse l’abbiamo venduta. Torna alla home e apri '
                                 'un’altra finestra.', size=17, align='center', color=mix(C['chiaro'], 88)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_FACCIATA, overlay=30), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'La facciata che si perde',
    ['immobiliare', 'agenzia', 'case', 'lucca', 'chiaro', 'slider pro', 'video', 'scorrimento'],
    'Un’agenzia immobiliare di Lucca: nell’apertura il video della facciata, a tutto schermo, scorrendo si allontana e si '
    'perde sullo sfondo, e dietro c’è il salotto. Poi le case con le misure vere, il soggiorno da esplorare coi segnaposto, '
    'la tinta delle persiane da comporre, i quartieri, la stima da comporre e le visite.',
    [('Le case', CASE), ('Vendi', VENDI), ('L’agenzia', AGENZIA)],
    [('home', 'Home', None), ('le-case', 'Le case', 'le-case'), ('vendi', 'Vendi', 'vendi'), ('l-agenzia', 'L’agenzia', 'l-agenzia')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bodoni Moda\', Georgia, serif',
    'ui-monospace, Menlo, monospace', ['Manrope', 'Bodoni Moda'], corsivo_di=['Bodoni Moda'],
    peso_titoli='600', raggio=2, grana=3, titoli_line='1.02', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'persiana'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['accent']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'grazie'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-case.json': build_case(), 'vendi.json': build_vendi(), 'l-agenzia.json': build_agenzia(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
