# -*- coding: utf-8 -*-
"""Genera il tema «Strappo» (id strappo): uno studio di art direction a Milano, dal 2011. Campagne, identità, fotografia
e set per marchi che hanno qualcosa da dire.

La trovata (20 nov 2026): il sito si strappa. Nell'apertura tre manifesti incollati uno sull'altro sopra piazza del
Duomo; scorrendo ognuno si strappa in due (due livelli dello Slider Pro col bordo frastagliato e l'anima bianca della
carta, che volano via ruotando nel movimento a scorrimento) e scopre quello sotto: «Strappo» · «Prima si imparano,» ·
«poi si strappano.» — e alla fine Milano. Poi i progetti sono sezioni col passaggio «Strappo» (Sezioni → Effetto
sticky → «Strappo»): la sezione si strappa come un manifesto e il lembo si ripiega. Cursore: il taglierino.

Tile poco usate: Shattered Image (la foto strappata in sei colonne), Blend Text, Category Rail, Confronto immagini (dal
moodboard alla campagna), Reveal Box (il metodo), Testo su tracciato (il timbro dello studio che gira).
Tema chiaro: carta, inchiostro, magenta, giallo del nastro. Titoli in Libre Caslon Display, testo in Onest, Azeret Mono.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, radius  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import agenzie as A  # noqa: E402

ID = 'strappo'
NAME = 'Strappo'
CATEGORIA = 'Creativi e portfolio'

HEX = {
    'primary': '#B0124F', 'primary_contrast': '#FFFFFF', 'secondary': '#141414', 'secondary_contrast': '#EFEBE3',
    'accent': '#F7C948', 'dark': '#141414', 'light': '#FFFFFF', 'muted': '#E5DFD4', 'muted_contrast': '#141414',
    'text': '#141414', 'text_muted': '#57534D', 'background': '#EFEBE3', 'border': '#D2CBBE', 'link': '#B0124F',
}
HOME, PROGETTI, STUDIO, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:progetti}}', '{{PAGINA:studio}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'studio@strappo.example', '02 000 2011', 'tel:+39020002011'
LAT, LNG = '45.4520', '9.1650'

(V_STRAPPO_ROSSO, V_FOGLIO_MANI, V_STRAPPO_BIANCO, V_CARTA_CREMA, V_CARTA_PEZZI, V_DUOMO, V_DUOMO_STATUA, V_CAMPIONI,
 V_SHOOTING, V_MODELLA, V_COLLAGE, V_MOODBOARD, V_FOTO_TAVOLO) = (
    'pexels-video-%d' % n for n in (6909992, 6962893, 9467582, 7233705, 7235113, 32697809, 12256583, 41120481, 7779057,
                                     8328604, 7868558, 7598835, 38973320))
VIDEOS = [V_STRAPPO_ROSSO, V_FOGLIO_MANI, V_STRAPPO_BIANCO, V_CARTA_CREMA, V_CARTA_PEZZI, V_DUOMO, V_DUOMO_STATUA, V_CAMPIONI,
          V_SHOOTING, V_MODELLA, V_COLLAGE, V_MOODBOARD, V_FOTO_TAVOLO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(MURO_NERO, COLONNA_STRAPPI, STRAPPI_BLU, STRAPPI_CREMA, STRAPPI_GIALLO, STRAPPI_ROSA, STRAPPI_ROSSI, BACHECA_STRAPPI,
 STRAPPI_RUGGINE, VETRINA_POSTER, COLLAGE_TONDO, COLLAGE_GRIGIO, COLLAGE_VERTICALE, TAVOLO_ILLUSTRAZIONI, COLLAGE_COLORI,
 COLLAGE_ROSSO, STUDIO_PARETE, RIUNIONE_COLORI, SCRIVANIA_LAPTOP, MATITE_TAVOLO, SCHIZZI, ART_DIRECTOR_DUE, DUOMO_GUGLIE,
 DUOMO, PORTONE, MILANO_LAMPIONI, GALLERIA_ARCO, DUOMO_CIELO, MOODBOARD_DONNA, CARTA_VIOLA, CARTA_BLU, CARTA_BIANCA,
 CARTA_KRAFT, CARTA_PIEGHE, CARTA_ROSA, POSTER_DREAM, POSTER_P21, POSTER_M, POSTER_21, POSTER_PAROLE, TACCUINO_OCCHI,
 TACCUINO_MANO, TACCUINO_VOLTI, TACCUINO_FIORI, TACCUINO_TASTIERA, TACCUINO_SCHIZZI, MOODBOARD_SUGHERO, MOODBOARD_PARETE,
 MOODBOARD_MODA, CAMPIONI, MOODBOARD_CORNICE, MOODBOARD_RAGAZZA, SHOOTING_PARCO, SHOOTING_MODELLA, SHOOTING_STUDIO,
 SHOOTING_TELEFONO, SHOOTING_GIALLO, SHOOTING_PAVIMENTO, BUCO_CARTA, BUCO_RIGHE, STRAPPO_QUADRETTI, STRISCE_NERE,
 STRAPPO_RIGHE, STRAPPO_QUADRETTI2) = f(
    17905715, 14466232, 5417350, 9712702, 10081257, 11027039, 11027038, 11808271, 38874284, 12825942, 18132988, 19058134,
    20010379, 14122051, 19780748, 16200035, 9849319, 6322359, 4977459, 4219217, 9849318, 36731578, 37536322, 15939547,
    10785352, 39801600, 34334963, 36527409, 7609956, 8749388, 8749405, 28380286, 13031767, 7233378, 8749406, 36246684,
    3964452, 3964485, 3964566, 17302471, 15100360, 2324808, 26692556, 4041667, 914245, 7147723, 7505177, 7598025, 4466999,
    6580566, 26892064, 7549484, 16018052, 7779226, 39190704, 7779220, 36697243, 13884519, 28380063, 28380062, 6672297,
    18718831, 28380057, 6672292)
PHOTOS = [MURO_NERO, COLONNA_STRAPPI, STRAPPI_BLU, STRAPPI_CREMA, STRAPPI_GIALLO, STRAPPI_ROSA, STRAPPI_ROSSI, BACHECA_STRAPPI,
          STRAPPI_RUGGINE, VETRINA_POSTER, COLLAGE_TONDO, COLLAGE_GRIGIO, COLLAGE_VERTICALE, TAVOLO_ILLUSTRAZIONI, COLLAGE_COLORI,
          COLLAGE_ROSSO, STUDIO_PARETE, RIUNIONE_COLORI, SCRIVANIA_LAPTOP, MATITE_TAVOLO, SCHIZZI, ART_DIRECTOR_DUE, DUOMO_GUGLIE,
          DUOMO, PORTONE, MILANO_LAMPIONI, GALLERIA_ARCO, DUOMO_CIELO, MOODBOARD_DONNA, CARTA_VIOLA, CARTA_BLU, CARTA_BIANCA,
          CARTA_KRAFT, CARTA_PIEGHE, CARTA_ROSA, POSTER_DREAM, POSTER_P21, POSTER_M, POSTER_21, POSTER_PAROLE, TACCUINO_OCCHI,
          TACCUINO_MANO, TACCUINO_VOLTI, TACCUINO_FIORI, TACCUINO_TASTIERA, TACCUINO_SCHIZZI, MOODBOARD_SUGHERO,
          MOODBOARD_PARETE, MOODBOARD_MODA, CAMPIONI, MOODBOARD_CORNICE, MOODBOARD_RAGAZZA, SHOOTING_PARCO, SHOOTING_MODELLA,
          SHOOTING_STUDIO, SHOOTING_TELEFONO, SHOOTING_GIALLO, SHOOTING_PAVIMENTO, BUCO_CARTA, BUCO_RIGHE, STRAPPO_QUADRETTI,
          STRISCE_NERE, STRAPPO_RIGHE, STRAPPO_QUADRETTI2]
# le metà dei manifesti (agenzie_disegni.py): (x, larghezza) in % della tela, del computer e del telefono
META = {1: ((0, 52.22), (44.58, 55.42), (0, 59.22), (41.41, 58.59)),
        2: ((0, 50.90), (43.26, 56.74), (0, 58.59), (42.50, 57.50)),
        3: ((0, 51.32), (43.06, 56.94), (0, 60.16), (44.22, 55.78))}
DISEGNI = (['strappo-%d%s-%s.png' % (n, t, l) for n in (1, 2, 3) for t in ('', '-t') for l in 'sd']
           + ['strappo-%d%s.jpg' % (n, t) for n in (1, 2, 3) for t in ('', '-t')])
LOGHI = ['strappo-logo-chiaro.png', 'strappo-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#FF6FA8')
C = K.c
MAGENTA, NASTRO, NERO, CARTA = '#B0124F', '#F7C948', '#141414', '#EFEBE3'
MONO = 'var(--olo-font-family-mono)'
TIT = 'var(--olo-font-family-heading)'
VOCI = [('map-pin', 'Via Tortona 31 · Milano', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '495/112',
                   'Strappo è uno studio di art direction a Milano: campagne, identità, fotografia e set per marchi che hanno '
                   'qualcosa da dire e non vogliono dirlo come tutti.',
                   [('Progetti', PROGETTI), ('Studio', STUDIO), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'In studio', 'Dal lunedì al venerdì 10 — 19<br>Il venerdì sera la porta è aperta', VOCI,
                   '© 2026 Strappo studio srl · Milano · P. IVA 00000002011', [('instagram', 'https://instagram.com'),
                                                                             ('pinterest', 'https://pinterest.com'),
                                                                             ('linkedin', 'https://linkedin.com')], scuro=False)


# ── L'apertura: tre manifesti che si strappano ──
# Ogni manifesto è diviso in due PNG (sinistra s, destra d) col bordo frastagliato: le due metà partono insieme a coprire
# la tela e al momento dello strappo volano via ai lati, ruotando. Sul telefono le stesse metà disegnate per la tela
# 640 × 1400 (livelli nascosti al computer). Il manifesto 1 sta sopra: si strappa per primo.
STRAPPI = {1: 5.0, 2: 30.0, 3: 55.0}


def meta(n, lato, tel):
    T = STRAPPI[n]
    k = -1 if lato == 's' else 1
    tp = [(0, {'x': 0, 'y': 0, 'r': 0, 'o': 100}), (T, {'x': 0, 'y': 0, 'r': 0, 'o': 100}),
          (T + 2.5, {'x': 26 * k, 'y': 6, 'r': 1.5 * k, 'o': 100}),
          (T + 9, {'x': 260 * k, 'y': 40 if k < 0 else -30, 'r': 7 * k, 'o': 100}),
          (T + 16, {'x': 980 * k, 'y': 160 if k < 0 else -110, 'r': 16 * k, 'o': 100}),
          (T + 16.01, {'x': 980 * k, 'o': 0}), (100, {'x': 980 * k, 'o': 0})]
    dx, dw, mx, mw = (*META[n][0 if lato == 's' else 1], *META[n][2 if lato == 's' else 3])
    nome = 'strappo-%d%s-%s.png' % (n, '-t' if tel else '', lato)
    alt = {1: 'Manifesto: Strappo, art direction a Milano', 2: 'Manifesto: prima si imparano,',
           3: 'Manifesto: poi si strappano.'}[n] if lato == 's' else ''
    if tel:
        return K.livello('st-%d%s-t' % (n, lato), tipo='image', imageSrc=K.img(nome), imageAlt=alt, objectFit='fill',
                         x=mx, y=0, w=mw, h=100, visible=False, entra='fadeIn', ritardo=0, durata=300, scrollMove=A.scorri(tp),
                         responsive={'mobile': {'visible': True, 'x': mx, 'y': 0, 'width': mw, 'height': 100}})
    return K.livello('st-%d%s' % (n, lato), tipo='image', imageSrc=K.img(nome), imageAlt=alt, objectFit='fill', x=dx, y=0,
                     w=dw, h=100, entra='fadeIn', ritardo=0, durata=300, scrollMove=A.scorri(tp),
                     responsive={'mobile': {'visible': False}})


def intero(n, tel):
    """Il manifesto intero sopra le sue due metà: a riposo non si vede la linea dello strappo; sparisce quando si strappa."""
    T = STRAPPI[n]
    tp = [(0, {'o': 100}), (T, {'o': 100}), (T + 0.3, {'o': 0}), (100, {'o': 0})]
    nome = 'strappo-%d%s.jpg' % (n, '-t' if tel else '')
    if tel:
        return K.livello('st-%di-t' % n, tipo='image', imageSrc=K.img(nome), imageAlt='', objectFit='fill', x=0, y=0, w=100,
                         h=100, visible=False, entra='fadeIn', ritardo=0, durata=300, scrollMove=A.scorri(tp),
                         responsive={'mobile': {'visible': True, 'x': 0, 'y': 0, 'width': 100, 'height': 100}})
    return K.livello('st-%di' % n, tipo='image', imageSrc=K.img(nome), imageAlt='', objectFit='fill', x=0, y=0, w=100, h=100,
                     entra='fadeIn', ritardo=0, durata=300, scrollMove=A.scorri(tp), responsive={'mobile': {'visible': False}})


def apertura(t):
    m = K.mob
    L = K.veli('st-velo', 'sinistra', 30, 60, colore=C['nero'], forza=82)
    for n in (3, 2, 1):
        for tel in (False, True):
            # la metà destra sotto la sinistra: l'anima bianca della sinistra resta in vista sul bordo
            L += [meta(n, 'd', tel), meta(n, 's', tel), intero(n, tel)]
    L += [
        K.livello('st-o', contenuto='Art direction · Milano · dal 2011', x=5, y=30, w=40, size=13, peso='700',
                  colore=mix(C['chiaro'], 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn',
                  ritardo=0, scrollMove=A.appare(70, 76), responsive={'mobile': {'x': 6, 'y': 50, 'width': 88, 'fontSize': m(11)}}),
        K.livello('st-t', contenuto='Diamo una faccia<br>ai marchi che hanno<br>qualcosa da dire.', tag='h1', x=5, y=35, w=46,
                  size=70, peso='400', colore=C['chiaro'], fontFamily=TIT, lineHeight=1.0, entra='fadeIn', ritardo=0,
                  scrollMove=A.appare(72, 80), responsive={'mobile': {'x': 6, 'y': 53, 'width': 88, 'fontSize': m(40)}}),
        K.livello('st-ts', contenuto='Campagne, identità, fotografia e set. Da via Tortona, per chi non vuole sembrare tutti '
                  'gli altri.', x=5, y=64, w=34, size=18, peso='500', colore=mix(C['chiaro'], 90), lineHeight=1.5,
                  entra='fadeIn', ritardo=0, scrollMove=A.appare(76, 84),
                  responsive={'mobile': {'x': 6, 'y': 70, 'width': 86, 'fontSize': m(16)}}),
        K.livello('st-b1', tipo='button', contenuto='I progetti', x=5, y=78, w='auto', size=14, peso='700', colore='#FFFFFF',
                  bgColor=MAGENTA, padding=18, borderRadius=0, buttonUrl=PROGETTI, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=A.appare(80, 88),
                  hover={'bgColor': NERO, 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 82, 'width': 'auto', 'fontSize': m(13)}}),
        K.livello('st-b2', tipo='button', contenuto='Scrivici', x=16, y=78, w='auto', size=14, peso='700', colore=NERO,
                  bgColor=NASTRO, padding=18, borderRadius=0, buttonUrl=CONTATTI, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=A.appare(80, 88),
                  hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 52, 'y': 82, 'width': 'auto', 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('st-1', K.sfondo_video(V_DUOMO, overlay=0.18, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── i progetti (Effetto sticky «Strappo») ──
def progetto(t, numero, cliente, titolo, testo, cosa, foto, verso, pos='center center', destra=False):
    blocco = [K.label(t, numero + ' · ' + cliente, color=MAGENTA),
              K.headline(t, titolo, size=58, tag='h2', pad_bottom=14, sizes=(44, 32)),
              K.text(t, testo, size=16),
              K.text(t, cosa, size=13, color=MAGENTA, pad=(10, 0, 0, 0))]
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=A.scheda(CARTA)), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='42,58', valign='end', anim=None)],
                     bg=K.photo_bg(foto, overlay=4, position=pos), padding='large', width='large',
                     sticky_effect='strappo', sticky_h_dir=verso, sticky_colore='#E9E1D0', sticky_h_align='bottom',
                     style={'padding_top': 120, 'padding_bottom': 80})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.label(t, 'Studio di art direction', color=MAGENTA),
                      K.headline(t, 'Le regole le conosciamo bene. <em>Per questo sappiamo dove strapparle.</em>', size=60,
                                 tag='h2', pad_bottom=0, sizes=(46, 32))], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo in sette, in un ex laboratorio di cornici in via Tortona. Pensiamo l’idea, scegliamo '
                                'chi la fotografa e chi la indossa, costruiamo il set e stiamo lì finché l’immagine non è quella '
                                'giusta. Poi la mettiamo dove serve: muri, riviste, vetrine, telefoni.', size=17),
                      K.button(t, 'Lo studio', STUDIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out += [
        progetto(t, '01', 'Sartoria Alzaia', 'Il cappotto che cammina', 'Una campagna d’autunno senza modelle in posa: '
                 'abbiamo seguito sei clienti della sartoria per una giornata, dal tram all’ufficio.',
                 'Campagna · fotografia · affissioni a Milano', SHOOTING_MODELLA, 'destra', pos='center 30%'),
        progetto(t, '02', 'Teatro dei Bastioni', 'Una stagione in sei lettere', 'Ogni spettacolo ha la sua lettera, '
                 'disegnata a mano e stampata enorme. Messi in fila, i manifesti scrivono il nome del teatro.',
                 'Identità della stagione · manifesti · programma', POSTER_P21, 'sinistra', destra=True),
        progetto(t, '03', 'Fondazione Ripa', 'L’archivio diventa rivista', 'Quarant’anni di fotografie dimenticate in '
                 'cantina, ritagliate e rimontate in un trimestrale che si colleziona.', 'Identità · art direction della '
                 'rivista · mostra', MOODBOARD_MODA, 'destra'),
        progetto(t, '04', 'Caffè Tortona', 'Il caffè ha le sue carte', 'Dodici carte diverse per dodici miscele: il '
                 'sacchetto si riconosce al tatto, prima ancora di leggere il nome.', 'Packaging · vetrine · menù',
                 CAMPIONI, 'sinistra', destra=True),
    ]
    out.append(t.section([
        t.row([t.col([t.classic('shatteredimage', {
            'image_url': K.img(COLLAGE_COLORI), 'preset': 'columns_6', 'gap': 10, 'height': '560px', 'image_ratio': '4/5',
            'image_position': 'center center', 'gap_color': C['fondo'], 'zoom_variation': True, 'zoom_min': 100, 'zoom_max': 140,
            'zoom_random': True, 'scroll_parallax': True, 'scroll_parallax_intensity': 40, 'scroll_reveal': True,
            'scroll_reveal_stagger': 140, 'scroll_reveal_duration': 700, 'kenburns': False, 'overlay': False,
            'border_radius_outer': radius(0), 'shadow': 'none'}, style={})], wm='1-2', ws='1-1'),
               t.col(A.testa(K, t, 'Come lavoriamo', 'Prima la strappiamo, <em>poi la rimontiamo.</em>', 'Ogni progetto comincia '
                             'così: prendiamo l’immagine che tutti si aspettano, la tagliamo in strisce e la rimettiamo insieme '
                             'in un altro ordine. Quasi sempre, la risposta è in una delle strisce.', size=52, sizes=(40, 30)) +
                     [K.button(t, 'I progetti', PROGETTI, kind='pieno', pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([t.classic('blendtext', {
        'text': 'Strappa', 'tag': 'div', 'font_size': '300', 'font_size_tablet': '180', 'font_size_mobile': '92',
        'font_weight': '400', 'font_family': 'heading', 'text_transform': 'none', 'letter_spacing': '-6', 'line_height': '1',
        'text_align': 'center', 'text_color': '#FFFFFF', 'blend_mode': 'difference', 'mode': 'text',
        'tile_padding': {'top': 10, 'right': 0, 'bottom': 10, 'left': 0}}, style={})], anim=None),
        t.one([K.text(t, 'Il venerdì sera la porta dello studio è aperta: si guarda quello che abbiamo strappato in settimana.',
                      size=18, align='center', color='#FFFFFF')], anim=None)],
        bg=K.video_bg(V_STRAPPO_ROSSO, overlay=10), padding='large', width='large',
        style={'padding_top': 160, 'padding_bottom': 150}))
    out.append(t.section([
        t.row([t.col([K.label(t, 'Un progetto da strappare?', color=NERO),
                      K.headline(t, 'Portaci l’immagine che non ti piace più.', size=70, tag='h2', color=NERO, pad_bottom=0,
                                 sizes=(50, 34))], wm='2-3', ws='1-1'),
               t.col([K.text(t, 'La guardiamo insieme, ti diciamo cosa terremmo e cosa strapperemmo. La prima chiacchierata è '
                                'gratis.', size=17, color=mix(NERO, 82)),
                      t.classic('button', {'text': 'Scrivici', 'url': CONTATTI, 'alignment': 'left', 'bg_color': NERO,
                                           'text_color': NASTRO, 'hover_bg_color': MAGENTA, 'hover_text_color': '#FFFFFF',
                                           'border_radius': 0, 'font_size': 13, 'font_weight': '700',
                                           'text_transform': 'uppercase', 'letter_spacing': '2'}, style={'padding_top': 18})],
                     wm='1-3', ws='1-1')],
              layout='custom', custom='64,36', gap=56, valign='end'),
    ], bg=solid(NASTRO), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_progetti():
    t = Tpl(ID, 'progetti')
    out = [t.section([
        t.row([t.col([K.label(t, 'Progetti', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Quindici anni<br>di muri strappati.', tag='h1', size=92, color='#FFFFFF', pad_bottom=18,
                                 sizes=(64, 40)),
                      K.text(t, 'Campagne, identità e riviste per sartorie, teatri, fondazioni e marchi di caffè. Scorri i '
                                'progetti, apri quello che ti incuriosisce.', size=18, color=mix('#FFFFFF', 90))],
                     wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_COLLAGE, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    voci = [(SHOOTING_MODELLA, 'Sartoria Alzaia', 'Campagna d’autunno'), (POSTER_P21, 'Teatro dei Bastioni', 'Stagione 2026'),
            (MOODBOARD_MODA, 'Fondazione Ripa', 'Rivista trimestrale'), (CAMPIONI, 'Caffè Tortona', 'Packaging'),
            (SHOOTING_GIALLO, 'Ottica Moscova', 'Campagna di primavera'), (POSTER_DREAM, 'Libreria del Naviglio', 'Manifesti'),
            (TACCUINO_VOLTI, 'Galleria Solferino', 'Catalogo della mostra')]
    out.append(t.section([
        t.one(A.testa(K, t, 'Sette progetti', 'Scorri <em>di lato.</em>', None, size=56, sizes=(42, 32))),
        t.one([t.classic('categoryrail', {
            'items': [{'image': K.img(fo), 'title': ti, 'subtitle': so, 'link': CONTATTI} for fo, ti, so in voci],
            'card_width': 300, 'card_aspect': '3/4', 'gap': 18, 'overlay_color': mix(NERO, 82), 'title_color': '#FFFFFF',
            'subtitle_color': mix('#FFFFFF', 86), 'radius': 0, 'object_position': 'center center', 'show_hint': True,
            'hint_text': 'Trascina per vederli tutti →', 'hint_color': MAGENTA, 'card_radius': radius(0)}, style={})], anim=None),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Sartoria Alzaia', 'Dal moodboard <em>alla campagna.</em>', 'Trascina la linea: a sinistra '
                             'la parete di sughero dove è nata l’idea, a destra la foto che è finita sui muri di Milano.',
                             size=48, sizes=(38, 30)), wm='1-3', ws='1-1'),
               t.col([X.confronto(K, t, MOODBOARD_SUGHERO, SHOOTING_STUDIO, 'Il moodboard', 'La campagna', ratio='16/10')],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    passi = [(MOODBOARD_PARETE, 'Uno', 'La parete', 'Due settimane a raccogliere immagini, ritagli, colori. Tu vieni in studio e '
              'strappi quelle che non ti somigliano.'),
             (SHOOTING_TELEFONO, 'Due', 'Il set', 'Scegliamo chi fotografa, chi posa, dove. Costruiamo il set e restiamo lì finché '
              'la foto non è giusta.'),
             (TAVOLO_ILLUSTRAZIONI, 'Tre', 'La carta', 'Impaginiamo, scegliamo la carta, seguiamo la stampa. Ogni bozza la vedi '
              'stampata, mai solo a schermo.')]
    out.append(t.section([
        t.one(A.testa(K, t, 'Il metodo', 'Tre passi, <em>passa sopra per leggerli.</em>', None, size=56, sizes=(42, 32))),
        t.row([t.col([t.classic('revealbox', {
            'top_media': {'type': 'image', 'image_url': K.img(fo), 'image_size': 'cover', 'image_position': 'center center'},
            'bottom_media': {'type': 'solid', 'color': MAGENTA}, 'visible_height': 440,
            'top_content': '<p><strong>%s</strong></p><p>%s</p>' % (n, ti),
            'bottom_content': '<p><strong>%s</strong></p><p>%s</p>' % (ti, te), 'reveal_effect': 'slide-up',
            'transition_speed': '0.6', 'top_text_color': '#FFFFFF', 'top_font_size': '20', 'bottom_text_color': '#FFFFFF',
            'bottom_font_size': '17', 'overlay_color': NERO, 'overlay_opacity': 30, 'top_align': 'flex-end',
            'top_justify': 'flex-start', 'bottom_align': 'center', 'bottom_justify': 'center', 'top_padding': '28',
            'bottom_padding': '32', 'border_radius': radius(0)}, style={})], wm='1-3', ws='1-1') for fo, n, ti, te in passi],
            layout='33-33-33', gap=20, valign='start'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 120}))
    return out


def build_studio():
    t = Tpl(ID, 'studio')
    out = [t.section([
        t.row([t.col([K.label(t, 'Lo studio', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Sette persone,<br>un ex laboratorio<br>di cornici.', tag='h1', size=86, color='#FFFFFF',
                                 pad_bottom=18, sizes=(60, 38)),
                      K.text(t, 'In via Tortona dal 2011. Le cornici non le facciamo più, ma il banco da lavoro è rimasto.',
                             size=18, color=mix('#FFFFFF', 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_MOODBOARD, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    out.append(t.section([
        t.row([t.col([t.classic('textpath', {
            'text': 'art direction · campagne · identità · set · via Tortona 31 · ',
            'path_preset': 'circle', 'font_size': '20', 'text_color': MAGENTA, 'letter_spacing': '3', 'animation': 'none',
            'spin': True, 'spin_speed': '24', 'spin_direction': 'cw', 'typography_preset': ''}, style={})], wm='2-5', ws='1-1'),
               t.col(A.testa(K, t, 'Chi siamo', 'Due art director, <em>cinque mani in più.</em>', '<p>Lo studio l’hanno aperto '
                             'Bianca e Leo, che si sono conosciuti strappando manifesti elettorali per un collage d’esame. Oggi '
                             'siamo in sette: due art director, una fotografa, un grafico, una producer, una copy e uno che '
                             'costruisce i set.</p><p>Non abbiamo un ufficio clienti: chi ti segue è chi lavora al tuo '
                             'progetto, dall’inizio alla fine.</p>', size=52, sizes=(40, 30)), wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, ART_DIRECTOR_DUE, 'Due art director che guardano le stampe al banco', ratio='4/5')],
                     wm='1-3', ws='1-1'),
               t.col([K.image(t, STUDIO_PARETE, 'La parete dello studio piena di schizzi e foto', ratio='4/5', drift=True)],
                     wm='1-3', ws='1-1'),
               t.col([K.image(t, SCHIZZI, 'Gli schizzi di un progetto sul tavolo', ratio='4/5')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=18, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Il venerdì', 'La porta <em>è aperta.</em>', 'Ogni venerdì dalle sei appendiamo al muro '
                             'quello che abbiamo fatto in settimana, anche gli scarti. Chiunque può entrare a guardare, e '
                             'strappare quello che non gli piace.', size=56, sizes=(42, 32)), wm='1-2', ws='1-1'),
               t.col([K.image(t, MURO_NERO, 'Un muro di manifesti strappati, nero e crema', ratio='16/10')], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [t.section([
        t.row([t.col([K.label(t, 'Contatti', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Portaci<br>un’immagine.', tag='h1', size=92, color='#FFFFFF', pad_bottom=18,
                                 sizes=(64, 40)),
                      K.text(t, 'Quella che ti piace, o quella che non sopporti più. Da lì cominciamo a parlare.', size=18,
                             color=mix('#FFFFFF', 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_FOGLIO_MANI, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('text', 'Marchio', 'marchio', True),
             K.campo('select', 'Che cosa', 'tipo', opzioni='Campagna\nIdentità\nFotografia e set\nRivista o catalogo\nNon lo so ancora'),
             K.campo('select', 'Per quando', 'quando', opzioni='Entro un mese\nEntro tre mesi\nCon calma'),
             K.campo('text', 'Un link da guardare', 'link', segnaposto='Il tuo sito, un profilo, una cartella…'),
             K.campo('textarea', 'Raccontaci il marchio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivici', ['Un marchio,', 'due righe.'],
                                'Ti rispondiamo entro tre giorni, con una data per vederci in studio.', VOCI, campi,
                                'Strappo — nuovo progetto', 'Invia', 'Grazie! Ti scriviamo entro tre giorni.'))
    out.append(X.zone(K, t, 'Dove siamo', ['In via Tortona,', 'nel cortile.'], 'Il portone verde, poi il cortile: lo studio è la '
                      'porta con i manifesti strappati.', [('Metro', 'linea 2, Porta Genova, 5 minuti'), ('Tram', 'linee 2 e 14'),
                      ('Bici', 'rastrelliera nel cortile'), ('Telefono', PHONE)],
                      LAT, LNG, 'Strappo · via Tortona 31', False, HEX['primary']))
    out.append(K.domande(t, 'Prima di cominciare', ['Quello che', 'ci chiedono al primo incontro.'], [
        ('Lavorate solo con i grandi marchi?', 'No. Metà dei nostri clienti sono botteghe, studi e piccoli editori. Conta la voglia '
         'di fare una cosa diversa, non il budget.'),
        ('Quanto costa un’identità?', 'Da 6.000 € per un marchio piccolo, con logo, caratteri, colori e le prime applicazioni. Il '
         'preventivo è chiuso.'),
        ('Fate anche i siti?', 'Disegniamo come devono apparire e li seguiamo con chi li sviluppa, ma non li programmiamo noi.'),
        ('Posso venire sul set?', 'Sì, anzi: ti chiediamo di esserci almeno il primo giorno.')], 'faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pagina strappata', color=NASTRO, align='center'),
                       K.headline(t, 'Qualcuno <em>l’ha strappata.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento='#FF6FA8'),
                       K.text(t, 'La pagina che cercavi non è più sul muro. Torna alla home: lì c’è ancora tutto.', size=17,
                              align='center', color=mix('#FFFFFF', 90)),
                       K.button(t, 'Alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_STRAPPO_BIANCO, overlay=36), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 220, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito-manifesto',
    ['agenzia', 'art direction', 'milano', 'chiaro', 'slider pro', 'strappo', 'collage', 'scorrimento'],
    'Uno studio di art direction a Milano: nell’apertura tre manifesti incollati sopra piazza del Duomo si strappano uno '
    'dopo l’altro mentre scorri, con l’anima bianca della carta sul bordo; poi i progetti si strappano come manifesti e il '
    'lembo si ripiega. La foto tagliata in strisce, dal moodboard alla campagna, il metodo, il timbro dello studio che gira.',
    [('Progetti', PROGETTI), ('Studio', STUDIO), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('progetti', 'Progetti', 'progetti'), ('studio', 'Studio', 'studio'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Onest\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Libre Caslon Display\', Georgia, serif',
    '\'Azeret Mono\', ui-monospace, monospace', ['Onest', 'Libre Caslon Display', 'Azeret Mono'], corsivo_di=[],
    peso_titoli='400', raggio=0, grana=4, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'taglierino'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['accent']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'grazie'}
THEME['preview_video_v'] = '1'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'progetti.json': build_progetti(), 'studio.json': build_studio(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
