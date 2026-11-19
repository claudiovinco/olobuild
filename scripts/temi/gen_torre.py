# -*- coding: utf-8 -*-
"""Genera il tema «Torre» (id torre): un B&B in una torre del Duecento a San Gimignano, una camera per piano, la colazione
al quinto e la terrazza in cima. Dentro la torre c'è un ascensore.

La trovata (18 nov 2026, sera; utente: «cerca di farne altri 5»): il sito è un ascensore. Nell'apertura la cabina sale nel
vano accanto alle torri e il piano cambia in grande; poi le sezioni della home sono i piani (Sezioni → Effetto sticky →
«Ascensore»): fra un piano e l'altro le porte d'ottone si chiudono, la fila dei piani in basso si accende sul successivo e
le porte si riaprono.

Tile poco usate messe alla prova: Soggiorni (il calendario coi prezzi e le notti minime), Image Hero (mai usata) per la
terrazza, Numeri, Passi.
Tema chiaro: pietra, cotto, ottone, salvia. Titoli in Marcellus (maiuscole incise, niente corsivo), testo in Nunito Sans.
Cursore: pulsante dell'ascensore.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'torre'
NAME = 'Torre'
CATEGORIA = 'Ospitalità e viaggi'

HEX = {
    'primary': '#9A4A2E', 'primary_contrast': '#FFFFFF', 'secondary': '#5E6B4F', 'secondary_contrast': '#FFFFFF',
    'accent': '#C9A45C', 'dark': '#1E1A15', 'light': '#FFFFFF', 'muted': '#EFE8DC', 'muted_contrast': '#2B241D',
    'text': '#2B241D', 'text_muted': '#685D52', 'background': '#F7F2EA', 'border': '#E0D6C6', 'link': '#9A4A2E',
}
HOME, CAMERE, STORIA, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:le-camere}}', '{{PAGINA:la-torre}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'piano@torre.example', '0577 000 270', 'tel:+390577000270'
LAT, LNG = '43.4677', '11.0430'

V_TORRI, V_TORRI2, V_MURA, V_TRAMONTO, V_CIPRESSI, V_CAMERA = (
    'pexels-video-26921525', 'pexels-video-26921522', 'pexels-video-12999690', 'pexels-video-36746030',
    'pexels-video-10156960', 'pexels-video-7507815')
VIDEOS = [V_TORRI, V_TORRI2, V_MURA, V_TRAMONTO, V_CIPRESSI, V_CAMERA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(TORRI_ALTO, SG_COLLINE, VICOLO, SG_NOTTE, TORRI_SOTTO, TORRI, TORRE_PORTICO, SG_TETTI, TORRE_1, TORRE_2, CAMERA_ARCO,
 CAMERA_DONNA, GIACOMO, COLAZIONE_LETTO, CAMERA_LAMPADA, CAMERA_PIETRA, CAMERA_MURO, CAMERA_TRAVI, CAMERA_BLU,
 CAMERA_VISTA, TERRAZZA, TERRAZZA_TAVOLI, TERRAZZA_VISTA, COLAZIONE, COLAZIONE_TAVOLA, CORNETTI, DONNA_COLAZIONE,
 PULSANTI, PULSANTI_NERI, SCALA_PIETRA, SCALA_CHIOCCIOLA, SCALA_ORO, SCALINI, CIPRESSI, VIGNA_TORRI, COLLINE, VINO_SERA,
 VINO_TAVOLA, PORTA, PORTA_ARCO, VASCA_PIETRA, VASCA) = f(
    13000472, 36483990, 14100906, 28572715, 16829822, 14939069, 16829835, 38487588, 13255066, 33040051, 38127108, 24877216,
    3770158, 24877214, 31296628, 15699339, 38183822, 7163651, 15692775, 28054852, 11888487, 28962508, 12604872, 14098680,
    36782825, 34307858, 3771067, 23319072, 16026075, 36714675, 39656, 27348400, 19064311, 15111268, 14515698, 4773222,
    9476486, 13809245, 30538865, 13013514, 2134224, 31387487)
PHOTOS = [TORRI_ALTO, SG_COLLINE, VICOLO, SG_NOTTE, TORRI_SOTTO, TORRI, TORRE_PORTICO, SG_TETTI, TORRE_1, TORRE_2, CAMERA_ARCO,
          CAMERA_DONNA, GIACOMO, COLAZIONE_LETTO, CAMERA_LAMPADA, CAMERA_PIETRA, CAMERA_MURO, CAMERA_TRAVI, CAMERA_BLU,
          CAMERA_VISTA, TERRAZZA, TERRAZZA_TAVOLI, TERRAZZA_VISTA, COLAZIONE, COLAZIONE_TAVOLA, CORNETTI, DONNA_COLAZIONE,
          PULSANTI, PULSANTI_NERI, SCALA_PIETRA, SCALA_CHIOCCIOLA, SCALA_ORO, SCALINI, CIPRESSI, VIGNA_TORRI, COLLINE,
          VINO_SERA, VINO_TAVOLA, PORTA, PORTA_ARCO, VASCA_PIETRA, VASCA]
DISEGNI = ['torre-vano.svg', 'torre-cabina.svg']
LOGHI = ['torre-logo-chiaro.png', 'torre-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#E2C27F')
C = K.c
FONT_T = 'Marcellus'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via San Matteo 70 · San Gimignano', PRENOTA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]
PIANI = ['T', '1', '2', '3', '4', '5', '6']


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 210, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '644/114',
                   'Torre è un bed and breakfast in una torre del Duecento a San Gimignano: sei camere, una per piano, la '
                   'colazione al quinto e la terrazza in cima. E un ascensore, per chi non vuole contare i gradini.',
                   [('Le camere', CAMERE), ('La torre', STORIA), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'Reception', 'Tutti i giorni 8 — 20<br>Arrivi dalle 14, partenze entro le 11', VOCI,
                   '© 2026 Torre B&B · San Gimignano · CIN IT052028C1000000', [('instagram', 'https://instagram.com'),
                                                                               ('facebook', 'https://facebook.com')], scuro=False)


# ── L'apertura: la cabina che sale ──
# Computer (tela 1440 × 810): il vano a x 86, y 6, 5 × 88 %; la cabina (4 × 11 %) parte da y 82 e sale fino a y 8: -74 %
# della tela = -600 px. Telefono (tela 640 × 1400): il vano a x 84, y 13, 9 × 30 %, la cabina (7 × 4,4 %) da y 38 a 14:
# -24 % = -336 unità della tela del telefono, cioè -756 px della tela del computer (× 2,25): una cabina solo per il
# telefono, nascosta al computer. A sinistra il piano in grande cambia mentre la cabina sale.
def apertura(t):
    m = K.mob
    sale = lambda dy: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0}, {'p': 8, 'y': 0}, {'p': 86, 'y': dy},
                                                          {'p': 100, 'y': dy}]}

    def piano(k, testo):
        a, b = 8 + k * 11, 8 + (k + 1) * 11
        tappe = [{'p': 0, 'o': 0}, {'p': max(0.1, a - 1), 'o': 0}, {'p': a, 'o': 100}, {'p': b - 1, 'o': 100}, {'p': b, 'o': 0},
                 {'p': 100, 'o': 0}]
        if k == 0:
            tappe = [{'p': 0, 'o': 100}, {'p': b - 1, 'o': 100}, {'p': b, 'o': 0}, {'p': 100, 'o': 0}]
        if k == len(PIANI) - 1:
            tappe = [{'p': 0, 'o': 0}, {'p': a - 1, 'o': 0}, {'p': a, 'o': 100}, {'p': 100, 'o': 100}]
        return K.livello('to-p%d' % k, contenuto=testo, x=60, y=14, w=22, size=150, peso='400', colore=mix(C['chiaro'], 92),
                         align='right', fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0, durata=300,
                         scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe},
                         responsive={'mobile': {'x': 52, 'y': 15, 'width': 30, 'fontSize': m(70)}})

    poi = lambda da, a, via_da=None, via_a=None: {'inizio': 0, 'fine': 100, 'tappe': (
        [{'p': 0, 'o': 0, 'y': 16}, {'p': da, 'o': 0, 'y': 16}, {'p': a, 'o': 100, 'y': 0}]
        + ([{'p': via_da, 'o': 100, 'y': 0}, {'p': via_a, 'o': 0, 'y': -16}, {'p': 100, 'o': 0, 'y': -16}] if via_da
           else [{'p': 100, 'o': 100, 'y': 0}]))}
    L = K.veli('to-velo', 'sinistra', 34, 66, colore=C['nero'], forza=84, mobile_alto=46)
    L += [
        K.livello('to-vano', tipo='image', imageSrc=K.img('torre-vano.svg'), imageAlt='', objectFit='fill', x=86, y=6, w=5,
                  h=88, entra='fadeIn', ritardo=200, durata=900,
                  responsive={'mobile': {'x': 84, 'y': 13, 'width': 9, 'height': 30}}),
        K.livello('to-cabina', tipo='image', imageSrc=K.img('torre-cabina.svg'), imageAlt='La cabina dell’ascensore che sale',
                  objectFit='contain', x=86.5, y=82, w=4, h=11, entra='fadeIn', ritardo=400, durata=600, scrollMove=sale(-600),
                  responsive={'mobile': {'visible': False}}),
        K.livello('to-cabina-t', tipo='image', imageSrc=K.img('torre-cabina.svg'), imageAlt='', objectFit='contain', x=85,
                  y=38, w=7, h=4.4, visible=False, entra='fadeIn', ritardo=400, durata=600, scrollMove=sale(-756),
                  responsive={'mobile': {'visible': True, 'x': 85, 'y': 38, 'width': 7, 'height': 4.4}}),
    ]
    L += [piano(k, p if p != 'T' else 'T') for k, p in enumerate(PIANI)]
    L += [
        K.livello('to-pl', contenuto='piano', x=60, y=10, w=22, size=14, peso='600', colore=mix(C['chiaro'], 80), align='right',
                  letterSpacing=5, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'x': 52, 'y': 13, 'width': 30, 'fontSize': m(10)}}),
        K.livello('to-o', contenuto='Bed & breakfast · San Gimignano', x=6, y=44, w=44, size=13, peso='600',
                  colore=mix(C['chiaro'], 86), letterSpacing=4, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  scrollMove=poi(0, 1, 40, 46), responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 48, 'width': 88}}),
        K.livello('to-t1', contenuto='Sette piani,<br>una camera per piano.', tag='h1', x=6, y=48, w=62, size=68, peso='400',
                  colore=C['chiaro'], fontFamily=FONT_T, lineHeight=1.04, entra='fadeInUp', ritardo=300, durata=900,
                  scrollMove=poi(0, 1, 40, 46), responsive={'mobile': {'fontSize': m(32), 'x': 6, 'y': 51, 'width': 88}}),
        K.livello('to-t1s', contenuto='Una torre del Duecento con l’ascensore dentro. Si sale fino alla terrazza, sopra i tetti.',
                  x=6, y=73, w=40, size=19, peso='500', colore=mix(C['chiaro'], 90), lineHeight=1.45, ritardo=600,
                  scrollMove=poi(0, 1, 40, 46), responsive={'mobile': {'fontSize': m(16), 'x': 6, 'y': 63, 'width': 88}}),
        K.livello('to-t2', contenuto='Sesto piano:<br>la terrazza.', x=6, y=49, w=50, size=76, peso='400', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=1.04, entra='fadeIn', ritardo=0, scrollMove=poi(76, 84),
                  responsive={'mobile': {'fontSize': m(38), 'x': 6, 'y': 51, 'width': 88}}),
        K.livello('to-b1', tipo='button', contenuto='Le camere', x=6, y=73, w='auto', size=14, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=2, buttonUrl=CAMERE, entra='fadeIn',
                  ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=poi(80, 88),
                  hover={'bgColor': C['nero'], 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 62, 'fontSize': m(13)}}),
        K.livello('to-b2', tipo='button', contenuto='Prenota', x=21, y=73, w='auto', size=14, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=2, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  buttonUrl=PRENOTA, entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2,
                  scrollMove=poi(80, 88), hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 52, 'y': 62, 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('to-1', K.sfondo_video(V_TORRI, overlay=0.28, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── i piani (Effetto sticky «Ascensore») ──
def piano(t, etichetta, occhiello, titolo, testo, foto, link=None, destra=False, pos='center center'):
    """Un piano: la foto a tutto schermo e la scheda chiara col testo; «etichetta» è il nome nella fila dei piani."""
    blocco = [K.label(t, occhiello, color=C['acc']),
              K.headline(t, titolo, size=52, tag='h2', pad_bottom=14, sizes=(40, 30), maiuscolo=True),
              K.text(t, testo, size=16)]
    if link:
        blocco.append(K.button(t, link[0], link[1], kind='pieno', pad_top=12))
    scheda = {'bg': solid(mix(C['fondo'], 94)), 'padding_top': 36, 'padding_right': 40, 'padding_bottom': 34,
              'padding_left': 40, 'border_radius': '2px'}
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=scheda), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='46,54', valign='center', anim=None)],
                     bg=K.photo_bg(foto, overlay=10, position=pos), padding='large', width='large',
                     sticky_effect='ascensore', sticky_h_dir='sopra', sticky_etichetta=etichetta, sticky_colore='#A5844A',
                     sticky_h_align='center', style={'padding_top': 80, 'padding_bottom': 90})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Bed & breakfast dal 2019', ['Si sale', 'a piedi o in ascensore.'], size=64,
                                layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Centosettantadue gradini o un ascensore di vetro nel vano dove una volta passava la scala di '
                                'legno. Qui sotto ti portiamo noi: un piano alla volta, dalla reception alla terrazza.', size=17),
                      K.button(t, 'La torre', STORIA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out += [
        piano(t, 'T', 'Piano terra · la reception', 'La porta <em>del 1270.</em>', 'Quercia e ferro battuto, alta quattro '
              'metri. Dietro c’è Giacomo, le chiavi delle camere e una mappa di San Gimignano disegnata a mano.', PORTA_ARCO),
        piano(t, '1', 'Primo piano · camera del Pozzo', 'La più <em>fresca.</em>', 'I muri sono spessi un metro e mezzo: '
              'd’estate qui dentro non servono i condizionatori. Letto matrimoniale, bagno in pietra.', CAMERA_PIETRA,
              destra=True),
        piano(t, '2', 'Secondo piano · camera dei Merli', 'La finestra <em>sui merli.</em>', 'Dalla finestra si vedono i merli '
              'della torre di fronte, a tre metri. La mattina passano le rondini.', CAMERA_ARCO),
        piano(t, '3', 'Terzo piano · camera Rondine', 'Le travi <em>di castagno.</em>', 'Il soffitto è quello originale, '
              'restaurato trave per trave. La vasca da bagno è sotto la finestra.', CAMERA_TRAVI, destra=True),
        piano(t, '4', 'Quarto piano · camera del Vento', 'Quattro finestre, <em>quattro venti.</em>', 'La camera più grande, '
              'con una finestra per lato: tramontana, libeccio, levante, scirocco.', CAMERA_VISTA),
        piano(t, '5', 'Quinto piano · la colazione', 'Il pane <em>di Giacomo.</em>', 'Pane del forno di via San Giovanni, '
              'marmellate di casa, pecorino di Pienza. Dalle 8 alle 10:30, con la vista sui tetti.', COLAZIONE, destra=True),
        piano(t, '6', 'Sesto piano · la terrazza', 'Sopra <em>i tetti.</em>', 'Quarantadue metri sopra la piazza. La sera, un '
              'bicchiere di Vernaccia e le altre tredici torri che si accendono.', TERRAZZA_VISTA, link=('Prenota', PRENOTA)),
    ]
    out.append(t.section([t.one([t.bare('imagehero', {
        'eyebrow_text': 'Sesto piano · al tramonto', 'eyebrow_dot': True, 'headline_text': 'La terrazza', 'accent_text': 'più alta',
        'headline_tail': 'di San Gimignano', 'accent_italic': False, 'stack_lines': False,
        'subhead': 'Aperta agli ospiti dalle 7 a mezzanotte. Al tramonto portiamo su la Vernaccia e il pecorino.',
        'cta1_text': 'Prenota una camera', 'cta1_url': PRENOTA, 'cta2_text': 'Le camere', 'cta2_url': CAMERE,
        'bg_image': K.img(TERRAZZA), 'media_label': 'La terrazza con i divani e la vista sulle colline',
        'bg_color': C['nero'], 'text_position': 'left', 'text_align': 'left', 'content_width': 620, 'aspect_ratio': '21/9',
        'min_height': 560, 'overlay_color': C['nero'], 'overlay_top': 0.1, 'overlay_bottom': 0.8, 'overlay_sides': True,
        'accent': '#E2C27F', 'accent_on': C['nero'], 'text_color': C['chiaro'], 'sub_color': mix(C['chiaro'], 90),
        'eyebrow_color': '#E2C27F'})], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(X.recensioni(K, t, [
        ('Abbiamo dormito al quarto piano con le quattro finestre aperte. Al mattino le rondini entravano quasi.', 'Anna e Marco',
         'Bologna'),
        ('L’ascensore di vetro dentro una torre del Duecento: mio figlio ci è salito undici volte.', 'Familie Weber', 'Monaco'),
        ('La colazione al quinto piano vale il viaggio. Il pane di Giacomo, soprattutto.', 'Claire', 'Lione')],
        'Chi è salito', ['Tre ospiti,', 'tre piani preferiti.']))
    out.append(K.pannello(t, SG_NOTTE, 'Sei camere, una per piano', 'Scegli <em>il tuo piano.</em>', 'Le camere più alte '
                          'si prenotano prima: la terrazza è a una rampa di scale.', 'Prenota', PRENOTA, effetto=''))
    return out


def build_camere():
    t = Tpl(ID, 'le-camere')
    out = [K.intro(t, 'Le camere', ['Sei camere,', 'sei piani diversi.'], 'Ogni camera prende tutto il piano: niente vicini di '
                   'stanza, solo i muri di pietra e le finestre sulle torri.', bg=K.video_bg(V_MURA, overlay=30), size=96)]
    camere = [
        (CAMERA_PIETRA, 'Primo piano', 'Camera del Pozzo', 'La più fresca, coi muri spessi un metro e mezzo. Matrimoniale, bagno '
         'in pietra con la doccia.', 'da 140 € a notte'),
        (CAMERA_ARCO, 'Secondo piano', 'Camera dei Merli', 'La finestra sui merli della torre di fronte. Matrimoniale e un divano '
         'letto per un bambino.', 'da 150 € a notte'),
        (CAMERA_TRAVI, 'Terzo piano', 'Camera Rondine', 'Le travi di castagno originali e la vasca sotto la finestra.',
         'da 165 € a notte'),
        (CAMERA_VISTA, 'Quarto piano', 'Camera del Vento', 'La più grande, con quattro finestre e un letto king size.',
         'da 190 € a notte'),
    ]
    for k, (foto, quanto, nome, testo, prezzo) in enumerate(camere):
        cols = [t.col([K.image(t, foto, nome, ratio='4/3')], wm='1-2', ws='1-1'),
                t.col([K.label(t, quanto, color=C['acc']),
                       K.headline(t, nome, size=52, tag='h2', pad_bottom=14, maiuscolo=True, sizes=(40, 30)),
                       K.text(t, testo, size=17), K.label(t, prezzo, color=C['testo'], pad_top=8),
                       K.button(t, 'Prenota', PRENOTA, kind='pieno', pad_top=12)], wm='1-2', ws='1-1')]
        if k % 2:
            cols.reverse()
        out.append(t.section([t.row(cols, layout='50-50', gap=64, valign='center')],
                             bg=solid(C['sala']) if k % 2 else None, padding='large', width='large',
                             style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(K.domande(t, 'Prima di salire', ['Quello che', 'ci chiedono alla porta.'], [
        ('C’è davvero un ascensore?', 'Sì, di vetro, nel vano della vecchia scala di legno. Porta fino al quinto piano; alla '
         'terrazza si sale con una rampa di dodici gradini.'),
        ('La colazione è compresa?', 'Sì, al quinto piano dalle 8 alle 10:30. Se parti presto ti prepariamo un cestino.'),
        ('Dove si parcheggia?', 'Dentro le mura no: al parcheggio Bagnaia, a cinque minuti a piedi. Ti diamo il permesso.'),
        ('Accettate animali?', 'Cani piccoli sì, nella camera del Pozzo, che è al primo piano.')], 'toc'))
    return out


def build_storia():
    t = Tpl(ID, 'la-torre')
    out = [K.intro(t, 'La torre', ['Quarantadue metri,', 'settecentocinquant’anni.'], 'La costruì nel 1270 una famiglia di '
                   'mercanti di zafferano, per farsi vedere da Siena. Oggi la vedono gli ospiti, dall’alto.',
                   bg=K.video_bg(V_TORRI2, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'La storia', ['Sette secoli', 'in quattro piani di racconto.'], size=56)]),
        P.passi(K, t, [('1270', 'La costruiscono', 'I Ciuffagni, mercanti di zafferano: la torre più alta della via, per un '
                        'anno.'),
                       ('1553', 'La abbassano', 'Firenze ordina di tagliare le torri troppo alte. La nostra perde due piani.'),
                       ('1962', 'La abitano', 'Diventa la casa del fornaio Corsi, che cuoce il pane al piano terra.'),
                       ('2019', 'La riaprono', 'Giacomo, il nipote del fornaio, la restaura e ci mette l’ascensore.')],
                colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([t.one([K.numeri(t, [('42 m', 'di torre'), ('172', 'gradini'), ('6', 'camere, una per piano'),
                                              ('13', 'torri dalla terrazza')], colonne=4, size=58)])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi c’è', ['Alla porta', 'del 1270.'], size=56)]),
        t.row([t.col([K.image(t, GIACOMO, 'Giacomo che rifà un letto', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([P.citazione(K, t, 'Mio nonno qui faceva il pane. Io faccio i letti e la colazione: il pane lo compro, '
                                        'ma dallo stesso forno.', 'Giacomo Corsi, il padrone di casa')], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Scegli le notti,', 'poi il piano.'], 'Il calendario mostra le notti libere e il prezzo. '
                   'Ti rispondiamo entro la giornata con la camera confermata.', bg=K.video_bg(V_CAMERA, overlay=26), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le notti libere', ['Il calendario', 'della torre.'], size=56)]),
        t.one([t.classic('soggiorni', {
            'occupati': [{'id': 'to-occ-1', 'dal': '+2', 'al': '+5', 'nota': ''}, {'id': 'to-occ-2', 'dal': '+9', 'al': '+12', 'nota': ''},
                         {'id': 'to-occ-3', 'dal': '+18', 'al': '+21', 'nota': ''}, {'id': 'to-occ-4', 'dal': '+30', 'al': '+34', 'nota': ''}],
            'prezzo_notte': 150, 'stagioni': [{'id': 'to-st-1', 'nome': 'Vendemmia', 'dal': '09-01', 'al': '10-15',
                                               'prezzo_notte': 190, 'minimo_notti': 3},
                                              {'id': 'to-st-2', 'nome': 'Natale in torre', 'dal': '12-20', 'al': '01-06',
                                               'prezzo_notte': 175, 'minimo_notti': 3}],
            'minimo_notti': 2, 'ospiti_max': 3, 'ospiti_iniziali': 2, 'pulizie': 0, 'tassa_soggiorno': 2.5,
            'valuta': '€', 'mostra_prezzi': True, 'mesi_visibili': 2, 'mesi_avanti': 12,
            'testo_pulsante': 'Richiedi queste notti', 'link_richiesta': '#scrivi', 'nota': 'Colazione al quinto piano compresa.',
            'colore_scelto': C['acc'], 'colore_oggi': C['acc2'], 'sfondo_pannello': C['sala']}, style={})], anim=None),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('date', 'Arrivo', 'arrivo', True), K.campo('date', 'Partenza', 'partenza', True),
             K.campo('select', 'Piano', 'piano', True, opzioni='Primo · camera del Pozzo\nSecondo · camera dei Merli\n'
                     'Terzo · camera Rondine\nQuarto · camera del Vento\nIl più alto libero'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivici', ['Un piano', 'tutto per te.'],
                                'Ti confermiamo la camera entro la giornata, con le indicazioni per il parcheggio.', VOCI, campi,
                                'Torre — richiesta', 'Invia', 'Grazie! Ti scriviamo entro stasera con la camera confermata.'))
    out.append(X.zone(K, t, 'Dove siamo', ['In via San Matteo,', 'dentro le mura.'], 'La torre con la porta di quercia, a '
                      'cento passi da piazza della Cisterna.', [('Auto', 'parcheggio Bagnaia, 5 minuti a piedi'),
                      ('Bus', 'da Poggibonsi, fermata Porta San Giovanni'), ('A piedi', 'Porta San Matteo, 2 minuti'),
                      ('Telefono', PHONE)], LAT, LNG, 'Torre B&B · via San Matteo 70', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · piano inesistente', color='#E2C27F', align='center'),
                       K.headline(t, 'Questo piano <em>non c’è.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento='#E2C27F', maiuscolo=True),
                       K.text(t, 'Nel 1553 Firenze ci ha tagliato due piani: forse cercavi uno di quelli. Torna al piano '
                                 'terra.', size=17, align='center', color=mix(C['chiaro'], 90)),
                       K.button(t, 'Piano terra', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_TRAMONTO, overlay=36), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito-ascensore',
    ['bed and breakfast', 'ospitalità', 'toscana', 'torre', 'chiaro', 'slider pro', 'ascensore', 'scorrimento'],
    'Un bed and breakfast in una torre del Duecento a San Gimignano: nell’apertura la cabina sale nel vano accanto alle '
    'torri e il piano cambia in grande; poi le sezioni della home sono i piani, con le porte d’ottone che si chiudono e si '
    'riaprono e la fila dei piani che si accende. La terrazza al tramonto, le camere, il calendario delle notti libere.',
    [('Le camere', CAMERE), ('La torre', STORIA), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('le-camere', 'Le camere', 'le-camere'), ('la-torre', 'La torre', 'la-torre'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Nunito Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Marcellus\', Georgia, serif',
    '\'DM Mono\', ui-monospace, monospace', ['Nunito Sans', 'Marcellus', 'DM Mono'], corsivo_di=[],
    peso_titoli='400', raggio=2, grana=3, titoli_line='1.06', titoli_spazio='0.02em')
THEME['cursor']['style'] = 'pulsante'
THEME['cursor']['ring_color'] = HEX['accent']
THEME['cursor']['dot_color'] = '#FFD66B'
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'grazie'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-camere.json': build_camere(), 'la-torre.json': build_storia(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
