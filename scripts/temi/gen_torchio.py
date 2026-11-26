# -*- coding: utf-8 -*-
"""Genera il tema «Torchio» (id torchio): una stamperia a caratteri mobili e studio di design editoriale a Verona, dal
1961. Libri d'artista, riviste, inviti, biglietti da visita, caratteri su misura.

La trovata (20 nov 2026): il sito si stampa davanti a te. Nell'apertura un foglio è steso sul torchio, sopra la macchina
che gira; scorrendo passa il rullo nero e dove è passato compaiono le righe (livelli del Pro Slider con la maschera
che si apre insieme al rullo, a velocità costante), poi passa il rullo rosso e stampa il secondo colore. Poi i lavori
sono sezioni col passaggio «Rullo» (Sezioni → Effetto sticky → «Rullo»): un rullo inchiostrato passa sulla pagina e la
sezione dopo compare dove è passato. Cursore: il timbro.

Tile poco usate: PDF Pro (il campionario vero, otto pagine da sfogliare), Variazione testo (la lettera della casa col
peso e il corpo ottico che cambiano), Preventivo (componi la stampa), Carosello, Overlay Slider.
Tema chiaro: carta avorio, inchiostro, rosso tipografico, blu di Prussia. Titoli in IM Fell English (col suo corsivo
vero), testo in Literata, Cutive Mono.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, radius  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import agenzie as A  # noqa: E402

ID = 'torchio'
NAME = 'Torchio'
CATEGORIA = 'Creativi e portfolio'

HEX = {
    'primary': '#B3261E', 'primary_contrast': '#FFFFFF', 'secondary': '#1F3A5F', 'secondary_contrast': '#FFFFFF',
    'accent': '#1F3A5F', 'dark': '#1C1A17', 'light': '#FFFFFF', 'muted': '#EAE2D3', 'muted_contrast': '#1C1A17',
    'text': '#1C1A17', 'text_muted': '#5C554B', 'background': '#F5F0E6', 'border': '#D8CDBA', 'link': '#B3261E',
}
HOME, STAMPERIA, LAVORI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:la-stamperia}}', '{{PAGINA:lavori}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'bottega@torchio.example', '045 000 1961', 'tel:+390450001961'
LAT, LNG = '45.4420', '10.9930'

(V_MACCHINA, V_RULLI, V_RULLI_V, V_ORO, V_INCHIOSTRO_ROSSO, V_INCHIOSTRO_COLA, V_RULLO_V, V_SFOGLIA, V_RIVISTA, V_PONTE,
 V_ARENA, V_OFFSET, V_TIPOGRAFIA) = (
    'pexels-video-%d' % n for n in (29906414, 38826403, 39134701, 2849944, 10080479, 6661928, 5670916, 6666444, 2385274,
                                     5602945, 35104449, 37533330, 38826343))
VIDEOS = [V_MACCHINA, V_RULLI, V_RULLI_V, V_ORO, V_INCHIOSTRO_ROSSO, V_INCHIOSTRO_COLA, V_RULLO_V, V_SFOGLIA, V_RIVISTA,
          V_PONTE, V_ARENA, V_OFFSET, V_TIPOGRAFIA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(CARATTERI_ORO, CASSETTO_MANO, CASSETTI, LETTERE_LEGNO, TORCHIO_POSTER, FORMA_LEGNO, TORCHIO_GIALLO, RULLI_MACCHINA,
 TIPOGRAFIA_BN, INCHIOSTRO_ARANCIO, TORCHIO_PICCOLO, MANO_LEVA, RULLO_BLU, RULLO_VIOLA, STAMPA_BLU, BANCO_GUANTI,
 RULLI_ARANCIO, STAMPATORE, CARATTERI_FILA, MACCHINA_SCRIVERE, LETTERE_BARATTOLO, LETTERE_IDEA, RIVISTE_TAVOLO,
 RIVISTE_PARETE, RIVISTA_MANO, RIVISTA_APERTA, RIVISTE_CEREAL, RIVISTE_MANI, RIVISTE_PILA, PILA_COLORI, PILA_MATITE,
 RIVISTE_BN, EDICOLA, CARTA_PILA, CARTA_BIANCA, CARTA_STRATI, CARTA_GRIGIA, RACCOGLITORI, CARTE_ARANCIO, VERONA_ALTO, ARENA,
 ADIGE, TORRE_LAMBERTI, ADIGE_2, VERONA_TETTI, CARATTERI_NOMI, TASTI, CASSETTA_TIMBRI, MACCHINA_VECCHIA, TASTI_LOVE,
 LIBRO_AA, STAMPANTE, STAMPATORE_2, POSTER_CASSETTA, BANCARELLA, TORCHIO_FOGLI, STAMPATORE_3, LIBRERIA, LIBRO_ROSSO,
 ZINE_MANI, ZINE_TAVOLO, ZINE_ESPOSITORE, INCHIOSTRO_ACQUA, INCHIOSTRO_BN, INCHIOSTRO_TONDO, INCHIOSTRO_BLU,
 INCHIOSTRO_VIOLA) = f(
    31211171, 4140908, 4140925, 234133, 6620985, 4140916, 6620972, 1440504, 37394506, 6620992, 6620963, 6620970, 5641905,
    31092838, 5641904, 1092050, 6620997, 6621000, 31744749, 9286244, 5846256, 6475757, 8084481, 4271615, 7787200, 4271613,
    4271625, 6214, 3697718, 13589785, 6440, 29973922, 9717535, 30350318, 4038653, 13315189, 20062955, 357514, 3927131,
    39315116, 37290568, 15178042, 36319278, 15286715, 36720740, 5045981, 4089834, 8715526, 40652832, 28915556, 5266319,
    5727002, 6620973, 27854359, 37233951, 19316522, 6620986, 15867085, 26800063, 29507371, 13258586, 17112492, 7577872,
    4049660, 3336447, 7577920, 7577789)
PHOTOS = [CARATTERI_ORO, CASSETTO_MANO, CASSETTI, LETTERE_LEGNO, TORCHIO_POSTER, FORMA_LEGNO, TORCHIO_GIALLO, RULLI_MACCHINA,
          TIPOGRAFIA_BN, INCHIOSTRO_ARANCIO, TORCHIO_PICCOLO, MANO_LEVA, RULLO_BLU, RULLO_VIOLA, STAMPA_BLU, BANCO_GUANTI,
          RULLI_ARANCIO, STAMPATORE, CARATTERI_FILA, MACCHINA_SCRIVERE, LETTERE_BARATTOLO, LETTERE_IDEA, RIVISTE_TAVOLO,
          RIVISTE_PARETE, RIVISTA_MANO, RIVISTA_APERTA, RIVISTE_CEREAL, RIVISTE_MANI, RIVISTE_PILA, PILA_COLORI, PILA_MATITE,
          RIVISTE_BN, EDICOLA, CARTA_PILA, CARTA_BIANCA, CARTA_STRATI, CARTA_GRIGIA, RACCOGLITORI, CARTE_ARANCIO, VERONA_ALTO,
          ARENA, ADIGE, TORRE_LAMBERTI, ADIGE_2, VERONA_TETTI, CARATTERI_NOMI, TASTI, CASSETTA_TIMBRI, MACCHINA_VECCHIA,
          TASTI_LOVE, LIBRO_AA, STAMPANTE, STAMPATORE_2, POSTER_CASSETTA, BANCARELLA, TORCHIO_FOGLI, STAMPATORE_3, LIBRERIA,
          LIBRO_ROSSO, ZINE_MANI, ZINE_TAVOLO, ZINE_ESPOSITORE, INCHIOSTRO_ACQUA, INCHIOSTRO_BN, INCHIOSTRO_TONDO,
          INCHIOSTRO_BLU, INCHIOSTRO_VIOLA]
DISEGNI = ['torchio-rullo.svg', 'torchio-rullo-rosso.svg', 'torchio-campionario.pdf']
LOGHI = ['torchio-logo-chiaro.png', 'torchio-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#F2A39C')
C = K.c
ROSSO, NERO, CARTA, BLU = '#B3261E', '#1C1A17', '#F5F0E6', '#1F3A5F'
MONO = 'var(--olo-font-family-mono)'
TIT = 'var(--olo-font-family-heading)'
VOCI = [('map-pin', 'Vicolo San Marco 4 · Verona', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 250, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '748/128',
                   'Torchio stampa a caratteri mobili e disegna libri, riviste e cataloghi a Verona dal 1961. Biglietti da '
                   'visita, inviti, carta da lettere, libri d’artista: tutto composto a mano, una lettera alla volta.',
                   [('La stamperia', STAMPERIA), ('Lavori', LAVORI), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'In bottega', 'Dal martedì al sabato 9 — 13 e 15 — 19<br>Il lunedì si stampa a porte chiuse', VOCI,
                   '© 2026 Torchio di Bassi e figli snc · Verona · P. IVA 00000001961', [('instagram', 'https://instagram.com'),
                                                                                       ('pinterest', 'https://pinterest.com')],
                   scuro=False)


# ── L'apertura: il foglio sul torchio, il rullo nero e poi il rosso ──
# Computer: foglio a x 20 %, y 9 %, 60 × 82 %; il rullo (SVG 160 × 1000) alto il 90 % della tela, largo l'8,1 %, parte
# a x 11,9 % e scorre di 980 px a velocità costante (il suo centro va da 15,95 a 84,05 %): le righe stanno fra x 25 e
# 75 %, quindi la maschera si apre fra il 13,3 e l'86,7 % del tratto del rullo. Telefono: foglio a x 6 %, y 6 %,
# 88 × 50 %; rullo alto il 56 %, largo il 19,6 %, da x -13,6 %, 1549 px; righe fra x 12 e 88 % → stessi punti.
NERO_DA, NERO_A, ROSSO_DA, ROSSO_A = 8.0, 38.0, 47.0, 77.0


def finestra(da, a):
    return da + 0.147 * (a - da), da + 0.853 * (a - da)


def rullo(lid, svg, da, a, tel):
    dx = 1549 if tel else 980
    tp = [(0, {'x': 0, 'o': 100}), (da, {'x': 0, 'o': 100}), (a, {'x': dx, 'o': 100}), (a + 5, {'x': dx + 360, 'o': 0}),
          (100, {'x': dx + 360, 'o': 0})]
    if lid.startswith('to-rr'):
        tp = [(0, {'x': 0, 'o': 0}), (da - 4, {'x': 0, 'o': 0}), (da - 1, {'x': 0, 'o': 100})] + tp[2:]
    if tel:
        return K.livello(lid, tipo='image', imageSrc=K.img(svg), imageAlt='', objectFit='fill', x=-13.6, y=3, w=19.6, h=56,
                         visible=False, entra='fadeIn', ritardo=0, durata=300, scrollMove=A.scorri(tp, costante=True),
                         responsive={'mobile': {'visible': True, 'x': -13.6, 'y': 3, 'width': 19.6, 'height': 56}})
    return K.livello(lid, tipo='image', imageSrc=K.img(svg), imageAlt='Il rullo inchiostrato' if 'rosso' not in svg else '',
                     objectFit='fill', x=11.9, y=5, w=8.1, h=90, entra='fadeIn', ritardo=0, durata=300,
                     scrollMove=A.scorri(tp, costante=True), responsive={'mobile': {'visible': False}})


def riga(lid, html, y, size, colore, da, a, my, msize, corsivo=False, mono=False, **extra):
    m = K.mob
    p0, p1 = finestra(da, a)
    tp = [(0, {'mr': 100}), (p0, {'mr': 100}), (p1, {'mr': 0}), (100, {'mr': 0})]
    extra.setdefault('lineHeight', 1.0)
    return K.livello(lid, contenuto=html, x=25, y=y, w=50, size=size, peso='400', colore=colore, fontFamily=MONO if mono else TIT,
                     fontStyle='italic' if corsivo else 'normal', entra='fadeIn', ritardo=0, durata=200,
                     scrollMove=A.scorri(tp, costante=True),
                     responsive={'mobile': {'x': 11, 'y': my, 'width': 80, 'fontSize': m(msize)}}, **extra)


def apertura(t):
    m = K.mob
    L = [
        K.livello('to-foglio', tipo='shape', bgColor=CARTA, x=20, y=9, w=60, h=82, entra='fadeInUp', ritardo=0, durata=900,
                  boxShadow='0 30px 70px rgba(0,0,0,0.45)', responsive={'mobile': {'x': 6, 'y': 6, 'width': 88, 'height': 50}}),
        riga('to-r1', 'Stamperia e design editoriale · Verona · dal 1961', 13, 14, ROSSO, ROSSO_DA, ROSSO_A, 8.5, 10, mono=True,
             letterSpacing=3, textTransform='uppercase'),
        riga('to-n1', 'Si stampa', 18, 104, NERO, NERO_DA, NERO_A, 12, 38),
        riga('to-n2', 'ancora a mano,', 31, 104, NERO, NERO_DA, NERO_A, 17.5, 38),
        riga('to-n3', 'una lettera alla volta.', 45, 62, NERO, NERO_DA, NERO_A, 23.5, 27, corsivo=True),
        riga('to-r2', 'Torchio', 58, 64, ROSSO, ROSSO_DA, ROSSO_A, 33, 40, corsivo=True),
        riga('to-r3', 'Libri, riviste, inviti e biglietti da visita, composti in vicolo San Marco.', 68, 18, ROSSO, ROSSO_DA,
             ROSSO_A, 40.5, 14, lineHeight=1.4),
        rullo('to-rn', 'torchio-rullo.svg', NERO_DA, NERO_A, False),
        rullo('to-rn-t', 'torchio-rullo.svg', NERO_DA, NERO_A, True),
        rullo('to-rr', 'torchio-rullo-rosso.svg', ROSSO_DA, ROSSO_A, False),
        rullo('to-rr-t', 'torchio-rullo-rosso.svg', ROSSO_DA, ROSSO_A, True),
        K.livello('to-b1', tipo='button', contenuto='La stamperia', x=25, y=78, w='auto', size=14, peso='600', colore='#FFFFFF',
                  bgColor=ROSSO, padding=18, borderRadius=0, buttonUrl=STAMPERIA, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=A.appare(80, 87),
                  hover={'bgColor': NERO, 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 60, 'width': 'auto', 'fontSize': m(13)}}),
        K.livello('to-b2', tipo='button', contenuto='Sfoglia il campionario', x=41, y=78, w='auto', size=14, peso='600',
                  colore=NERO, bgColor='transparent', padding=18, borderRadius=0, borderWidth=1, borderColor=NERO,
                  buttonUrl=LAVORI + '#campionario', entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2,
                  scrollMove=A.appare(80, 87), hover={'bgColor': mix(NERO, 10), 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 66, 'width': 'auto', 'fontSize': m(13), 'visible': False}}),
    ]
    sl = [K.diapositiva('to-1', K.sfondo_video(V_MACCHINA, overlay=0.3, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=3000, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── cosa stampiamo (Effetto sticky «Rullo») ──
def lavoro(t, numero, nome, testo, prezzo, foto, verso, pos='center center', destra=False):
    blocco = [K.label(t, numero, color=ROSSO),
              K.headline(t, nome, size=60, tag='h2', pad_bottom=14, sizes=(46, 34)),
              K.text(t, testo, size=16),
              K.text(t, prezzo, size=14, color=ROSSO, pad=(10, 0, 0, 0))]
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=A.scheda(CARTA)), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='44,56', valign='center', anim=None)],
                     bg=K.photo_bg(foto, overlay=6, position=pos), padding='large', width='large',
                     sticky_effect='rullo', sticky_h_dir=verso, sticky_colore='#1C1A17', sticky_h_align='center',
                     style={'padding_top': 100, 'padding_bottom': 100})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        K.centered(t, [K.label(t, 'Dal 1961, in vicolo San Marco', color=ROSSO, align='center'),
                       K.headline(t, 'Un biglietto da visita <em>si tocca</em> prima di leggerlo.', size=64, tag='h2',
                                  align='center', pad_bottom=18, sizes=(48, 34)),
                       K.text(t, 'Tre generazioni di Bassi, quaranta casse di caratteri in legno e in piombo, un torchio '
                                 'Heidelberg del 1958 e uno a mano del 1890. Componiamo ogni lavoro lettera per lettera, poi lo '
                                 'stampiamo così forte che la carta si incide. Lo senti col dito.', size=17, align='center')],
                   side=12),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out += [
        lavoro(t, 'I · Biglietti e carta da lettere', 'Il nome inciso', 'Su cotone da 600 grammi, a uno o due colori, col '
               'bordo dipinto a mano se vuoi. Li riconosci al buio.', 'Cento biglietti da 120 €', CASSETTO_MANO, 'destra'),
        lavoro(t, 'II · Inviti e partecipazioni', 'Una festa su carta', 'Matrimoni, battesimi, inaugurazioni: componiamo '
               'l’invito con te in bottega, scegliamo la carta toccandola, foderiamo le buste.', 'Cinquanta inviti da 290 €',
               TORCHIO_FOGLI, 'sinistra', destra=True),
        lavoro(t, 'III · Riviste e cataloghi', 'Pagine da tenere', 'Disegniamo riviste, cataloghi di mostre e libri per '
               'editori piccoli. Il progetto grafico lo facciamo qui, la stampa la seguiamo fino all’ultima segnatura.',
               'Progetto grafico da 2.500 €', RIVISTA_APERTA, 'destra'),
        lavoro(t, 'IV · Libri d’artista', 'Trenta copie, numerate', 'Testi, incisioni e caratteri scelti con chi scrive o '
               'disegna. Cuciti a mano, in tirature piccole che si firmano una per una.', 'Da 1.400 € la tiratura',
               CARATTERI_ORO, 'sinistra', destra=True),
    ]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'La lettera della casa', 'Più nera, <em>più grande.</em>', 'La usiamo per i testi dei libri. '
                             'Premi sulla parola e tieni premuto: il peso cresce e il corpo ottico cambia, come quando si passa '
                             'dal corpo 8 al corpo 72 nella cassa.', size=52, sizes=(40, 30)), wm='1-3', ws='1-1'),
               t.col([t.classic('variablespecimen', {
                   'font_family': "'Literata', Georgia, serif", 'sample_text': 'Composto\na mano.', 'interaction': 'pressione',
                   'auto_animate': True, 'auto_speed': '8', 'drag_hint': '', 'show_readout': True,
                   'axes': [{'tag': 'wght', 'label': 'Peso', 'min': 300, 'max': 900, 'default_val': 380},
                            {'tag': 'opsz', 'label': 'Corpo ottico', 'min': 7, 'max': 72, 'default_val': 72}],
                   'text_color': NERO, 'accent_color': ROSSO, 'bg_color': '', 'font_size': '170', 'font_weight_fallback': '500',
                   'text_align': 'left', 'padding_y': '10', 'font_size_tablet': '130', 'font_size_mobile': '76',
                   'typography_preset': ''}, style={})], wm='2-3', ws='1-1')],
              layout='custom', custom='32,68', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([t.bare('pdfpro', {
            'pdf_url': K.img('torchio-campionario.pdf'), 'mode': 'flipbook', 'viewer_height': '640', 'start_page': '1',
            'initial_zoom': 'fit-width', 'theme': 'light', 'bg_color': C['sala'], 'show_toolbar': True, 'show_page_nav': True,
            'show_zoom': False, 'show_fullscreen': True, 'show_bottombar': True, 'show_bottombar_pages': True,
            'show_download': True, 'show_print': False, 'show_search': False, 'show_thumbnails': False, 'nav_click': True,
            'nav_swipe': True, 'nav_keyboard': True, 'hotspots': []})], wm='3-5', ws='1-1'),
               t.col(A.testa(K, t, 'Il campionario 2027', 'Otto pagine <em>da sfogliare.</em>', 'Le carte, i caratteri, i '
                             'biglietti, gli inviti e i prezzi. Giralo come un libretto, o scaricalo e stampalo: ma vieni a '
                             'toccare la carta vera.', size=52, sizes=(40, 30)) +
                     [K.button(t, 'Prenota una visita', CONTATTI, kind='pieno', pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=64, valign='center'),
    ], padding='large', width='large', html_id='campionario', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_INCHIOSTRO_ROSSO, 'In bottega', 'Vieni a <em>toccare la carta.</em>', 'Dal martedì al '
                             'sabato. Porta il testo, la data o anche solo un’idea: la componiamo insieme.',
                             extra=[K.button(t, 'Dove siamo', CONTATTI + '#zone', kind='pieno', align='center')]))
    return out


def build_stamperia():
    t = Tpl(ID, 'la-stamperia')
    out = [t.section([
        t.row([t.col([K.label(t, 'La stamperia', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Quaranta casse,<br>due torchi.', tag='h1', size=96, color='#FFFFFF', pad_bottom=18,
                                 sizes=(66, 42)),
                      K.text(t, 'Il laboratorio di vicolo San Marco: dove si compone, si stampa, si taglia e si cuce.', size=18,
                             color=mix('#FFFFFF', 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_TIPOGRAFIA, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Come si stampa', 'Quattro mani, <em>un foglio.</em>', '<p><strong>Si compone.</strong> Lettera per '
                             'lettera, al contrario, nel compositoio. Una riga di biglietto da visita sono cinque minuti.</p>'
                             '<p><strong>Si inchiostra.</strong> Il rullo passa sull’inchiostro, poi sulla forma. Un colore alla '
                             'volta: il rosso si stampa il giorno dopo il nero.</p><p><strong>Si preme.</strong> Il torchio '
                             'spinge la carta sulle lettere: tanto da inciderla, poco da non bucarla.</p><p><strong>Si '
                             'taglia.</strong> A mano, con la taglierina del 1961. Gli angoli arrotondati costano un po’ di più.'
                             '</p>', size=52, sizes=(40, 30)), wm='1-2', ws='1-1'),
               t.col([K.image(t, MANO_LEVA, 'La mano sulla leva del torchio', ratio='4/5', drift=True)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Componi la stampa', 'Il preventivo <em>in un minuto.</em>', 'Scegli cosa ti serve: il totale '
                             'si aggiorna da solo. Quando premi, ci arriva una mail con le tue scelte e ti richiamiamo per la '
                             'prova di stampa.', size=52, sizes=(40, 30)), wm='1-3', ws='1-1'),
               t.col([t.classic('builder', {
                   'eyebrow': 'Biglietti da visita', 'heading': 'Cento biglietti', 'intro': 'Cotone da 600 grammi, formato 85 × 55.',
                   'currency': '€ ', 'cap': 0,
                   'items': [{'name': 'Un colore, solo davanti', 'price': '90', 'note': 'Nero o rosso', 'start': 1},
                             {'name': 'Secondo colore', 'price': '30', 'note': 'Il rosso si stampa il giorno dopo', 'start': 1},
                             {'name': 'Stampa sul retro', 'price': '40', 'note': 'Un colore', 'start': 0},
                             {'name': 'Bordo dipinto a mano', 'price': '35', 'note': 'Rosso, blu o oro', 'start': 0},
                             {'name': 'Angoli arrotondati', 'price': '15', 'note': 'Raggio 4 mm', 'start': 0},
                             {'name': 'Scatolina di legno', 'price': '18', 'note': 'Col nome inciso', 'start': 0}],
                   'total_label': 'Totale', 'count_label': 'scelte', 'cta_text': 'Chiedi la prova di stampa', 'cta_url': '',
                   'send_mode': 'mail', 'send_to': MAIL, 'send_subject': 'Biglietti da visita', 'send_intro':
                   'Vorrei cento biglietti con:', 'zone_accent': ROSSO, 'zone_on': '#FFFFFF', 'card_bg': C['sala'],
                   'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
                   'align': 'left', 'layout': 'panel'}, style={})], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, CASSETTI, 'La cassettiera dei caratteri di legno', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, RULLI_ARANCIO, 'I rulli del torchio coperti di inchiostro arancio', ratio='4/5', drift=True)],
                     wm='1-3', ws='1-1'),
               t.col([K.image(t, STAMPATORE, 'Lo stampatore che stende l’inchiostro sui rulli', ratio='4/5')], wm='1-3',
                     ws='1-1')],
              layout='33-33-33', gap=18, valign='start'),
    ], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 110}))
    return out


def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [t.section([
        t.row([t.col([K.label(t, 'Lavori', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Carta che<br>si tiene.', tag='h1', size=96, color='#FFFFFF', pad_bottom=18,
                                 sizes=(66, 42)),
                      K.text(t, 'Riviste, cataloghi, inviti e libri d’artista usciti dal laboratorio quest’anno.', size=18,
                             color=mix('#FFFFFF', 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_SFOGLIA, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    out.append(t.section([
        t.one(A.testa(K, t, 'Riviste e cataloghi', 'Le pagine <em>di quest’anno.</em>', None, size=56, sizes=(42, 32))),
        t.one([t.classic('carousel', {
            'slides': [{'image_url': K.img(fo), 'image_alt': al, 'link_url': '', 'caption': ca} for fo, al, ca in (
                (RIVISTA_APERTA, 'Una rivista aperta sul tavolo', 'Quaderni dell’Adige · trimestrale'),
                (RIVISTE_PARETE, 'Pagine di rivista appese al muro', 'Mostra «Volti» · catalogo'),
                (RIVISTA_MANO, 'Una mano che regge una rivista', 'Rivista del Teatro Romano'),
                (ZINE_MANI, 'Due mani che sfogliano una fanzine', 'Fanzine «Ponte Pietra»'),
                (RIVISTE_PILA, 'Una pila di riviste', 'Archivio del Festival di poesia'),
                (LIBRERIA, 'Una libreria con le copertine in vetrina', 'Libreria Dante · segnalibri'))],
            'slides_to_show': 3, 'gap': 18, 'autoplay': True, 'autoplay_speed': 3600, 'show_arrows': True, 'show_dots': False,
            'loop': True, 'pause_on_hover': True, 'slide_height': 'fixed', 'fixed_height': 440, 'border_radius': radius(0),
            'arrow_color': '#FFFFFF', 'arrow_bg': ROSSO, 'show_caption': True, 'caption_color': '#FFFFFF',
            'caption_bg': mix(NERO, 72), 'object_fit': 'cover', 'aspect_ratio': '4/5', 'mobile_slides': 1}, style={})],
            anim=None),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one(A.testa(K, t, 'Inviti e biglietti', 'Fatti <em>a mano.</em>', None, size=56, sizes=(42, 32))),
        t.one([t.classic('overlayslider', {
            'slides': [{'image': K.img(fo), 'title': ti, 'subtitle': so, 'link': CONTATTI, 'ribbon': ri} for fo, ti, so, ri in (
                (TORCHIO_FOGLI, 'Giulia e Pietro', 'Partecipazioni · cotone avorio, due colori', 'matrimonio'),
                (STAMPA_BLU, 'Osteria del Ponte', 'Menù e biglietti · blu di Prussia', 'ristorante'),
                (LETTERE_LEGNO, 'Elena Bassi, restauro', 'Biglietti · caratteri di legno', 'biglietti'),
                (CASSETTA_TIMBRI, 'Festival di poesia', 'Cartoline · trenta versi', 'cartoline'))],
            'columns': '2', 'gap': 'medium', 'image_ratio': '4/3', 'image_fit': 'cover', 'overlay_position': 'bottom',
            'overlay_horizontal': 'left', 'overlay_style': 'overlay-primary', 'overlay_padding': 'medium', 'title_size': 'h3',
            'hover_effect': 'zoom', 'hover_overlay': 'always', 'show_arrows': True, 'show_dots': True,
            'ribbon_position': 'top-right', 'ribbon_bg': ROSSO, 'ribbon_color': '#FFFFFF', 'shadow': 'none', 'preset': 'custom',
            'slide_radius': 0, 'overlay_color': mix(NERO, 84), 'overlay_gradient': True, 'title_color': '#FFFFFF',
            'title_weight': '400', 'title_uppercase': False, 'subtitle_color': mix('#FFFFFF', 86), 'subtitle_size': 15,
            'show_cta': False}, style={})], anim=None),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([t.bare('pdfpro', {
            'pdf_url': K.img('torchio-campionario.pdf'), 'mode': 'flipbook', 'viewer_height': '640', 'start_page': '1',
            'initial_zoom': 'fit-width', 'theme': 'light', 'bg_color': C['sala'], 'show_toolbar': True, 'show_page_nav': True,
            'show_zoom': False, 'show_fullscreen': True, 'show_bottombar': True, 'show_bottombar_pages': True,
            'show_download': True, 'show_print': False, 'show_search': False, 'show_thumbnails': False, 'nav_click': True,
            'nav_swipe': True, 'nav_keyboard': True, 'hotspots': []})], wm='3-5', ws='1-1'),
               t.col(A.testa(K, t, 'Il campionario', 'Le carte <em>e i prezzi.</em>', 'Otto pagine con le sei carte, i caratteri, '
                             'esempi di biglietti e inviti e i prezzi per cominciare.', size=52, sizes=(40, 30)), wm='2-5',
                     ws='1-1')],
              layout='custom', custom='60,40', gap=64, valign='center'),
    ], padding='large', width='large', html_id='campionario', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [t.section([
        t.row([t.col([K.label(t, 'Contatti', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Passa<br>in bottega.', tag='h1', size=96, color='#FFFFFF', pad_bottom=18, sizes=(66, 42)),
                      K.text(t, 'Le carte si scelgono toccandole. Se non puoi venire, ti mandiamo i campioni per posta.', size=18,
                             color=mix('#FFFFFF', 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_PONTE, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Che cosa', 'tipo', True, opzioni='Biglietti da visita\nInviti e partecipazioni\nCarta da lettere\n'
                     'Rivista o catalogo\nLibro d’artista\nAltro'),
             K.campo('date', 'Per quando', 'quando'), K.campo('text', 'Quante copie', 'copie', segnaposto='Cento, cinquanta…'),
             K.campo('textarea', 'Il testo da stampare, o un’idea', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivici', ['Un testo,', 'una carta.'],
                                'Ti rispondiamo entro due giorni con un preventivo e una data per la prova di stampa.', VOCI,
                                campi, 'Torchio — richiesta', 'Invia', 'Grazie! Ti scriviamo entro due giorni.'))
    out.append(X.zone(K, t, 'Dove siamo', ['In vicolo San Marco,', 'dietro piazza delle Erbe.'], 'La porta con l’insegna di '
                      'legno e le lettere in vetrina. Si sente il torchio dalla strada.', [('A piedi', 'da piazza delle Erbe, '
                      '3 minuti'), ('Auto', 'parcheggio Cittadella, 12 minuti'), ('Treno', 'Porta Nuova, bus 11 e 12'),
                      ('Telefono', PHONE)], LAT, LNG, 'Torchio · vicolo San Marco 4', False, HEX['primary']))
    out.append(K.domande(t, 'Prima di stampare', ['Quello che', 'ci chiedono in bottega.'], [
        ('Quanto ci vuole?', 'Per i biglietti da visita una settimana, per gli inviti due. I libri d’artista dai due ai sei mesi.'),
        ('Posso vedere una prova?', 'Sempre: la stampiamo su un foglio della carta scelta e te la diamo da toccare, prima di dire sì.'),
        ('Posso usare il mio logo?', 'Sì: lo trasformiamo in un cliché di metallo, che resta tuo per le ristampe.'),
        ('Spedite?', 'In tutta Italia, in una scatola di cartone grigio con la carta velina. Arriva in tre giorni.')], 'faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · refuso', color='#F2A39C', align='center'),
                       K.headline(t, 'Qui c’è <em>un refuso.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento='#F2A39C'),
                       K.text(t, 'La pagina che cercavi è finita nella cassa sbagliata. La ricomponiamo: intanto torna alla home.',
                              size=17, align='center', color=mix('#FFFFFF', 90)),
                       K.button(t, 'Alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_ORO, overlay=36), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 220, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito che si stampa',
    ['agenzia', 'stampa', 'tipografia', 'design editoriale', 'verona', 'chiaro', 'slider pro', 'rullo', 'scorrimento'],
    'Una stamperia a caratteri mobili e studio editoriale a Verona: nell’apertura un foglio è steso sul torchio e scorrendo '
    'passa il rullo nero, che stampa le righe dove passa, poi il rosso col secondo colore; poi i lavori passano l’uno '
    'nell’altro sotto un rullo inchiostrato. La lettera della casa che cambia peso, il campionario vero da sfogliare, il '
    'preventivo da comporre.',
    [('La stamperia', STAMPERIA), ('Lavori', LAVORI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('la-stamperia', 'La stamperia', 'la-stamperia'), ('lavori', 'Lavori', 'lavori'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Literata\', Georgia, serif', '\'IM Fell English\', Georgia, serif', '\'Cutive Mono\', ui-monospace, monospace',
    ['Literata', 'IM Fell English', 'Cutive Mono'], corsivo_di=['IM Fell English'],
    peso_titoli='400', raggio=0, grana=5, titoli_line='1.04', titoli_spazio='0')
THEME['cursor']['style'] = 'timbro'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['secondary']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'grazie'}
THEME['preview_video_v'] = '1'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-stamperia.json': build_stamperia(), 'lavori.json': build_lavori(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
