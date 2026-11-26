# -*- coding: utf-8 -*-
"""Genera il tema «Pollice» (id pollice): un'agenzia social a Bologna. Piani editoriali, contenuti, community e
pubblicità sui social per chi ha un negozio, un ristorante, uno studio.

La trovata (20 nov 2026): il sito è un telefono che non smette di vibrare. Nell'apertura, sopra Bologna al tramonto, un
telefono gira un reel e intorno spuntano le notifiche (livelli del Pro Slider che si ingrandiscono nel movimento a
scorrimento), salgono i cuori; poi i servizi sono sezioni col passaggio «Mazzo di carte», con «Arriva da: Sotto»: ogni
sezione vola via verso l'alto come una storia scartata col pollice. Cursore: il cuore.

Tile poco usate: Link in Bio (mai usata: la pagina «Link» dell'agenzia), Griglia Presenze (chi ti risponde adesso),
Scorrimento verticale (i reel che scorrono in colonne), Finder (che social fa per te), Grafico e Counter Circle (un caso).
Tema chiaro: crema, oltremare, pomodoro. Titoli in Gabarito, testo in DM Sans, Space Mono.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, radius  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import agenzie as A  # noqa: E402

ID = 'pollice'
NAME = 'Pollice'
CATEGORIA = 'Creativi e portfolio'

HEX = {
    'primary': '#3A26E0', 'primary_contrast': '#FFFFFF', 'secondary': '#FF5436', 'secondary_contrast': '#16141F',
    'accent': '#FF5436', 'dark': '#16141F', 'light': '#FFFFFF', 'muted': '#EFE8DC', 'muted_contrast': '#16141F',
    'text': '#16141F', 'text_muted': '#5C5766', 'background': '#F8F4ED', 'border': '#DED5C7', 'link': '#3A26E0',
}
HOME, LINK, CASI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:link}}', '{{PAGINA:casi}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'ciao@pollice.example', '051 000 0451', 'tel:+390510000451'
LAT, LNG = '44.4949', '11.3426'

(V_SCROLL, V_SCROLL_V, V_TAVOLO, V_CREATOR_V, V_CUCINA, V_APP, V_TREPPIEDE_V, V_RING_V, V_RING_LUCE_V, V_RING_TEL_V,
 V_TRAMONTO, V_BOLOGNA_V, V_AMICHE, V_POSA, V_MANO_V) = (
    'pexels-video-%d' % n for n in (6756650, 17512949, 6611954, 9384669, 12691850, 38410500, 13929839, 6342205, 7677006,
                                     33048633, 35104440, 36718323, 5085429, 4722973, 8819428))
VIDEOS = [V_SCROLL, V_SCROLL_V, V_TAVOLO, V_CREATOR_V, V_CUCINA, V_APP, V_TREPPIEDE_V, V_RING_V, V_RING_LUCE_V, V_RING_TEL_V,
          V_TRAMONTO, V_BOLOGNA_V, V_AMICHE, V_POSA, V_MANO_V]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(CREATOR_TRECCE, CAPPELLO_RING, CUCINA_CREATOR, RING_MANO, TELEFONO_TREPPIEDE, LIBRO_RING, MANO_TELEFONO, TREPPIEDE_MANO,
 TELEFONO_SCHERMO, RING_TELEFONO, GIMBAL, TREPPIEDI_SALA, MANO_ROSA, ICONE_SOCIAL, PROFILO, MANO_ANELLO, RING_IPHONE,
 RING_TRUCCO, RING_BLU, RING_BRACCIO, RING_NERO, PORTICI, TETTI_BOLOGNA, BOLOGNA_VIA, CANALE, TORRE, VIA_SERA, SQUADRA_1,
 SQUADRA_2, SQUADRA_PC, SQUADRA_MURALE, SQUADRA_RIDE, SQUADRA_TAVOLO, TELEFONO_BUIO, TELEFONO_MANI, TELEFONO_GIALLO,
 TELEFONO_CAPELLI, TELEFONO_GINOCCHIO, TELEFONO_CAMICIA, SELFIE_ALBERI, SELFIE_RING, SELFIE_TREPPIEDE, SELFIE_UOMO,
 SELFIE_CORNETTO, SELFIE_TREPPIEDE2, NEON_NOODLE, NEON_LINEE, ROSA_UOMO, ROSA_DONNA, ROSA_SFOCATA, ROSA_ABITO, ROSA_COPPIA,
 ROSA_SEDUTA, TASTIERA, MANO_BUIO, MESSAGGI, TELEFONO_VERDE, UOMO_TELEFONO, MANO_BUIO2, AMICHE_MARE, SELFIE_DUE,
 SELFIE_GRUPPO, SELFIE_INVERNO, SELFIE_NONNI, SELFIE_PIANTE) = f(
    13929970, 6333683, 8357241, 6333749, 8357671, 6593731, 18285525, 6954104, 6953836, 9413701, 8357670, 9622793, 7411,
    16229745, 7662060, 3244608, 13929321, 5149734, 4793158, 4125688, 7824639, 2903241, 1541363, 11776478, 31205420,
    32213180, 36799908, 23496662, 23496667, 7675029, 19400118, 7213644, 7675016, 11989314, 18160079, 3756879, 12498416,
    29685331, 7641426, 27086990, 6593775, 7514828, 8303436, 12679986, 6964858, 14934911, 15166489, 4584606, 30273030,
    20179044, 38765054, 7389029, 5775975, 5717938, 7054507, 40382716, 6260105, 5835330, 7054503, 9326121, 786801, 8556342,
    3228723, 8153880, 3228698)
PHOTOS = [CREATOR_TRECCE, CAPPELLO_RING, CUCINA_CREATOR, RING_MANO, TELEFONO_TREPPIEDE, LIBRO_RING, MANO_TELEFONO,
          TREPPIEDE_MANO, TELEFONO_SCHERMO, RING_TELEFONO, GIMBAL, TREPPIEDI_SALA, MANO_ROSA, ICONE_SOCIAL, PROFILO, MANO_ANELLO,
          RING_IPHONE, RING_TRUCCO, RING_BLU, RING_BRACCIO, RING_NERO, PORTICI, TETTI_BOLOGNA, BOLOGNA_VIA, CANALE, TORRE,
          VIA_SERA, SQUADRA_1, SQUADRA_2, SQUADRA_PC, SQUADRA_MURALE, SQUADRA_RIDE, SQUADRA_TAVOLO, TELEFONO_BUIO,
          TELEFONO_MANI, TELEFONO_GIALLO, TELEFONO_CAPELLI, TELEFONO_GINOCCHIO, TELEFONO_CAMICIA, SELFIE_ALBERI, SELFIE_RING,
          SELFIE_TREPPIEDE, SELFIE_UOMO, SELFIE_CORNETTO, SELFIE_TREPPIEDE2, NEON_NOODLE, NEON_LINEE, ROSA_UOMO, ROSA_DONNA,
          ROSA_SFOCATA, ROSA_ABITO, ROSA_COPPIA, ROSA_SEDUTA, TASTIERA, MANO_BUIO, MESSAGGI, TELEFONO_VERDE, UOMO_TELEFONO,
          MANO_BUIO2, AMICHE_MARE, SELFIE_DUE, SELFIE_GRUPPO, SELFIE_INVERNO, SELFIE_NONNI, SELFIE_PIANTE]
DISEGNI = ['pollice-telefono.svg']
LOGHI = ['pollice-logo-chiaro.png', 'pollice-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=18, peso_titoli='800', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#FF8A70')
C = K.c
BLU, POMODORO, INCHIOSTRO = '#3A26E0', '#FF5436', '#16141F'
POMODORO_TESTO = '#C23A1F'  # il pomodoro per le scritte piccole sul crema (contrasto 5,2)
MONO = 'var(--olo-font-family-mono)'
TIT = 'var(--olo-font-family-heading)'
VOCI = [('map-pin', 'Via del Pratello 21 · Bologna', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 190, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '534/118',
                   'Pollice cura i social di negozi, ristoranti e studi di Bologna: il piano, le foto e i video, le risposte '
                   'ai commenti, la pubblicità. Ogni mese un resoconto che si legge in cinque minuti.',
                   [('Link', LINK), ('Casi', CASI), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'In agenzia', 'Dal lunedì al venerdì 9 — 18<br>I commenti li leggiamo anche nel weekend', VOCI,
                   '© 2026 Pollice srl · Bologna · P. IVA 00000000451', [('instagram', 'https://instagram.com'),
                                                                       ('tiktok', 'https://tiktok.com'),
                                                                       ('linkedin', 'https://linkedin.com')], scuro=False)


# ── L'apertura: il telefono e le notifiche ──
# Computer (tela 1440 × 810): il telefono (SVG 400 × 820, vetro da 14 a 386 × 14 a 806) a x 62 %, y 15 %, largo 19 %
# (273,6 px, alto 560,9 px = 69,25 %); sotto il vetro il video verticale a x 62,665 %, y 16,18 %, 17,67 × 66,89 %.
# Telefono (tela 640 × 1400): telefono a x 23 %, y 9 %, largo 54 % (alto 50,62 %); video a x 24,89 %, y 9,86 %,
# 50,22 × 48,89 %.
TX, TY, TW, TH = 62.0, 15.0, 19.0, 69.25
MX, MY, MW, MH = 23.0, 9.0, 54.0, 50.62


def vetro(x, y, w, h):
    return x + w * 14 / 400, y + h * 14 / 820, w * 372 / 400, h * 792 / 820


def apertura(t):
    m = K.mob
    vx, vy, vw, vh = vetro(TX, TY, TW, TH)
    mvx, mvy, mvw, mvh = vetro(MX, MY, MW, MH)
    via = [(62, {'o': 100, 'y': 0, 's': 1}), (70, {'o': 0, 'y': -70, 's': 0.9}), (100, {'o': 0, 'y': -70, 's': 0.9})]

    def bolla(k, html, x, y, w, p, mx, my, mw, icona='bell'):
        tp = [(0, {'o': 0, 'y': 24, 's': 0.6}), (p, {'o': 0, 'y': 24, 's': 0.6}), (p + 5, {'o': 100, 'y': 0, 's': 1})] + via
        return K.livello('po-n%d' % k, contenuto=html, x=x, y=y, w=w, size=15, peso='500', colore=INCHIOSTRO, bgColor='#FFFFFF',
                         padding=14, borderRadius=18, lineHeight=1.35, entra='fadeIn', ritardo=0, durata=300,
                         scrollMove=A.scorri(tp), boxShadow='0 18px 40px rgba(22,20,31,0.28)',
                         responsive={'mobile': {'x': mx, 'y': my, 'width': mw, 'fontSize': m(13)}})

    def cuore(k, x, y, p, mx, my):
        tp = [(0, {'o': 0, 'y': 0, 's': 0.5}), (p, {'o': 0, 'y': 0, 's': 0.5}), (p + 2, {'o': 100, 'y': -20, 's': 1.1}),
              (p + 14, {'o': 0, 'y': -260, 's': 0.8}), (100, {'o': 0, 'y': -260, 's': 0.8})]
        return K.livello('po-c%d' % k, tipo='icon', iconName='heart', x=x, y=y, w='auto', size=40, colore=POMODORO,
                         iconFillColor=POMODORO, entra='fadeIn', ritardo=0, durata=300, scrollMove=A.scorri(tp),
                         responsive={'mobile': {'x': mx, 'y': my, 'fontSize': m(30)}})
    L = K.veli('po-velo', 'sinistra', 32, 60, colore=C['nero'], forza=84)
    L += [
        K.livello('po-reel', tipo='video', videoSrc=K.img(V_RING_LUCE_V + '.mp4'), videoAutoplay=True, videoMuted=True,
                  videoLoop=True, x=round(vx, 3), y=round(vy, 3), w=round(vw, 3), h=round(vh, 3), entra='fadeIn', ritardo=200,
                  durata=600, responsive={'mobile': {'x': round(mvx, 3), 'y': round(mvy, 3), 'width': round(mvw, 3),
                                                     'height': round(mvh, 3)}}),
        K.livello('po-telefono', tipo='image', imageSrc=K.img('pollice-telefono.svg'), imageAlt='Un telefono che gira un reel',
                  objectFit='fill', x=TX, y=TY, w=TW, h=TH, entra='fadeIn', ritardo=200, durata=600,
                  responsive={'mobile': {'x': MX, 'y': MY, 'width': MW, 'height': MH}}),
    ]
    L += [
        bolla(1, '<b>Forno Calzolari</b> ha iniziato a seguirti', 41.5, 22, 18, 6, 4, 13, 46),
        bolla(2, '<b>+1.284</b> visualizzazioni nell’ultima ora', 82.5, 28, 15, 13, 52, 22, 44),
        bolla(3, '<b>Marta</b>: «Domani passo a provarla!»', 43, 44, 16.5, 20, 4, 32, 46),
        bolla(4, 'Il reel di martedì è <b>fra i più visti</b> di Bologna', 82.5, 52, 15, 27, 52, 41, 44),
        bolla(5, '<b>37 messaggi</b> nuovi da leggere', 45, 66, 14.5, 34, 6, 50, 42),
        cuore(1, 76, 72, 9, 70, 52), cuore(2, 78.5, 74, 17, 74, 54), cuore(3, 75, 73, 25, 68, 53), cuore(4, 77.5, 72, 33, 72, 52),
        cuore(5, 76.5, 74, 41, 70, 54),
        K.livello('po-o', contenuto='Agenzia social · Bologna', x=5, y=30, w=32, size=13, peso='700',
                  colore=mix(C['chiaro'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=100,
                  responsive={'mobile': {'x': 6, 'y': 63, 'width': 88, 'fontSize': m(11)}}),
        K.livello('po-t1', contenuto='Il pollice in su<br>si guadagna.', tag='h1', x=5, y=35, w=36, size=74, peso='800',
                  colore=C['chiaro'], fontFamily=TIT, lineHeight=0.98, letterSpacing=-1, entra='fadeInUp', ritardo=250,
                  durata=900, scrollMove=A.appare(0, 1, 50, 56),
                  responsive={'mobile': {'x': 6, 'y': 66, 'width': 88, 'fontSize': m(42)}}),
        K.livello('po-t1s', contenuto='Piani editoriali, foto, video e risposte ai commenti per chi lavora a Bologna e vuole '
                  'farsi trovare anche sul telefono.', x=5, y=58, w=32, size=18, peso='500', colore=mix(C['chiaro'], 90),
                  lineHeight=1.5, ritardo=500, scrollMove=A.appare(0, 1, 50, 56),
                  responsive={'mobile': {'x': 6, 'y': 78, 'width': 86, 'fontSize': m(16)}}),
        K.livello('po-t2', contenuto='Un post alla volta.<br><span style="color:#FF8A70">Tutti i giorni.</span>', x=5, y=35,
                  w=38, size=70, peso='800', colore=C['chiaro'], fontFamily=TIT, lineHeight=0.98, letterSpacing=-1,
                  entra='fadeIn', ritardo=0, scrollMove=A.appare(66, 74),
                  responsive={'mobile': {'x': 6, 'y': 66, 'width': 88, 'fontSize': m(40)}}),
        K.livello('po-b1', tipo='button', contenuto='Cosa facciamo', x=5, y=60, w='auto', size=16, peso='700', colore='#FFFFFF',
                  bgColor=BLU, padding=18, borderRadius=999, buttonUrl=CASI, entra='fadeIn', ritardo=0,
                  scrollMove=A.appare(72, 80), hover={'bgColor': POMODORO, 'color': INCHIOSTRO, 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 80, 'width': 'auto', 'fontSize': m(14)}}),
        K.livello('po-b2', tipo='button', contenuto='Scrivici', x=19, y=60, w='auto', size=16, peso='700', colore=INCHIOSTRO,
                  bgColor='#FFFFFF', padding=18, borderRadius=999, buttonUrl=CONTATTI, entra='fadeIn', ritardo=0,
                  scrollMove=A.appare(72, 80), hover={'bgColor': POMODORO, 'color': INCHIOSTRO, 'duration': 250},
                  responsive={'mobile': {'x': 56, 'y': 80, 'width': 'auto', 'fontSize': m(14)}}),
    ]
    sl = [K.diapositiva('po-1', K.sfondo_video(V_TRAMONTO, overlay=0.2, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── i servizi (Effetto sticky «Mazzo di carte», da sotto come le storie) ──
def carta(t, numero, nome, testo, prezzo, foto, pos='center center', destra=False):
    blocco = [K.label(t, numero, color=BLU),
              K.headline(t, nome, size=60, tag='h2', pad_bottom=14, sizes=(46, 34)),
              K.text(t, testo, size=16),
              K.text(t, prezzo, size=14, color=BLU, pad=(10, 0, 0, 0))]
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=A.scheda(C['fondo'], r=22)), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='44,56', valign='center', anim=None)],
                     bg=K.photo_bg(foto, overlay=6, position=pos), padding='large', width='large',
                     sticky_effect='mazzo', sticky_h_dir='sotto', sticky_h_align='center',
                     style={'padding_top': 100, 'padding_bottom': 100})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.label(t, 'Dal 2017, in via del Pratello', color=POMODORO_TESTO),
                      K.headline(t, 'Il telefono è la vetrina più grande di Bologna. <em>La teniamo accesa noi.</em>', size=58,
                                 tag='h2', pad_bottom=0, sizes=(44, 32), accento=BLU)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo in nove: chi scrive, chi fotografa, chi monta, chi risponde. Lavoriamo per negozi, '
                                'ristoranti, palestre e studi che non hanno il tempo di stare sui social, ma sanno che i '
                                'clienti li cercano lì.', size=17)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 90}))
    out += [
        carta(t, 'Carta 1 · La strategia', 'Prima si ascolta', 'Un mese a guardare i tuoi clienti, i tuoi concorrenti e '
              'i tuoi numeri. Poi un piano su una pagina: dove stare, cosa dire, quante volte.', 'Il primo mese: 690 €',
              SQUADRA_MURALE),
        carta(t, 'Carta 2 · I contenuti', 'Foto e video veri', 'Veniamo da te una mattina al mese e torniamo con trenta '
              'giorni di foto, reel e storie. Niente immagini di repertorio: le tue mani, la tua vetrina.',
              'Da 850 € al mese', CUCINA_CREATOR, destra=True),
        carta(t, 'Carta 3 · La community', 'Rispondiamo noi', 'Commenti, messaggi, recensioni: rispondiamo entro un’ora, '
              'con la tua voce. Le domande difficili te le giriamo prima.', 'Da 390 € al mese', SELFIE_GRUPPO,
              pos='center 40%'),
        carta(t, 'Carta 4 · La pubblicità', 'Pochi euro, ben spesi', 'Campagne piccole, in un raggio di pochi chilometri, '
              'per chi può davvero passare da te. Ogni lunedì ti diciamo quanto hai speso e cosa è tornato.',
              'Gestione da 250 € al mese', TELEFONO_GIALLO, destra=True),
    ]
    membri = [(ROSA_DONNA, 'Giulia', 'Testi e piano', True), (ROSA_UOMO, 'Samuele', 'Video', True),
              (SELFIE_RING, 'Irene', 'Community', True), (CAPPELLO_RING, 'Tommaso', 'Foto', False),
              (SELFIE_UOMO, 'Davide', 'Pubblicità', True), (CREATOR_TRECCE, 'Noemi', 'Reel', False)]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'In agenzia adesso', 'Chi ti risponde <em>oggi.</em>', 'Il pallino acceso vuol dire che è in '
                             'agenzia o sul set. Tutti rispondono a tutti: non devi ricordarti chi segue cosa.', size=52,
                             sizes=(40, 30), accento=BLU), wm='1-3', ws='1-1'),
               t.col([t.classic('presencegrid', {
                   'source': 'manual', 'members': [{'name': n, 'avatar': K.img(fo), 'role': r, 'online': o} for fo, n, r, o in membri],
                   'columns': 3, 'columns_tablet': 3, 'columns_mobile': 2, 'gap': 18, 'show_ranks': False,
                   'online_label': 'In agenzia', 'offline_label': 'Sul set', 'show_ticker': False, 'card_bg': '#FFFFFF',
                   'card_color': INCHIOSTRO, 'role_color': BLU, 'avatar_size': 84, 'avatar_shape': 'circle',
                   'online_color': '#1FA463', 'offline_color': POMODORO, 'dot_size': 14, 'name_size': 17, 'name_weight': '700',
                   'role_size': 11, 'card_radius': radius(22), 'card_hover_effect': 'lift',
                   'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                   style={})], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    voci = [
        {'tipo': 'immagine', 'immagine': K.img(SELFIE_CORNETTO), 'alt': 'Una ragazza col cornetto che si fa un selfie', 'colonna': '1'},
        {'tipo': 'testo', 'testo': '+38 % di follower in tre mesi', 'colonna': '1'},
        {'tipo': 'icona', 'icona': 'heart', 'colonna': '1', 'colore': '#FFFFFF', 'sfondo': POMODORO},
        {'tipo': 'immagine', 'immagine': K.img(CUCINA_CREATOR), 'alt': 'Una cuoca che gira un video in cucina', 'colonna': '1'},
        {'tipo': 'testo', 'testo': 'Il reel del lunedì', 'colonna': '1'},
        {'tipo': 'immagine', 'immagine': K.img(RING_TELEFONO), 'alt': 'Un telefono davanti all’anello di luce', 'colonna': '2'},
        {'tipo': 'testo', 'testo': '4 minuti per rispondere', 'colonna': '2'},
        {'tipo': 'separatore', 'colonna': '2'},
        {'tipo': 'immagine', 'immagine': K.img(SELFIE_DUE), 'alt': 'Due amiche che si fanno un selfie', 'colonna': '2'},
        {'tipo': 'icona', 'icona': 'message-circle', 'colonna': '2', 'colore': '#FFFFFF', 'sfondo': BLU},
        {'tipo': 'immagine', 'immagine': K.img(TREPPIEDE_MANO), 'alt': 'Una mano che sistema il telefono sul treppiede', 'colonna': '3'},
        {'tipo': 'testo', 'testo': '30 giorni di contenuti in una mattina', 'colonna': '3'},
        {'tipo': 'immagine', 'immagine': K.img(SELFIE_NONNI), 'alt': 'Una famiglia a tavola che si fa un selfie', 'colonna': '3'},
        {'tipo': 'icona', 'icona': 'thumbs-up', 'colonna': '3', 'colore': INCHIOSTRO, 'sfondo': '#FFFFFF'},
        {'tipo': 'testo', 'testo': 'Niente foto di repertorio', 'colonna': '3'},
    ]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Quello che pubblichiamo', 'Scorre <em>da solo.</em>', 'Foto e frasi dei profili che curiamo: '
                             'passa sopra col mouse per fermarle.', size=56, sizes=(42, 32), accento=BLU) +
                     [K.button(t, 'I casi', CASI, kind='pieno', pad_top=18)], wm='1-3', ws='1-1'),
               t.col([t.classic('scorrimentoverticale', {
                   'voci': voci, 'colonne': '3', 'colonne_mobile': '2', 'durata': 30, 'direzione_1': 'giu', 'direzione_2': 'su',
                   'direzione_3': 'giu', 'pausa_hover': True, 'altezza': 620, 'altezza_mobile': 460, 'gap': 20,
                   'gap_colonne': 16, 'sfumatura_bordi': 18, 'allineamento': 'center', 'testo_font_family': 'heading',
                   'testo_font_size': 26, 'testo_font_size_mobile': 20, 'testo_font_weight': '800', 'testo_colore': INCHIOSTRO,
                   'immagine_altezza': 260, 'immagine_raggio': radius(22), 'separatore_colore': POMODORO}, style={})],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    scelte = [
        ('Ristorante', 'utensils', 'Instagram e TikTok', 'Il piatto prima di ordinarlo', 'Reel dalla cucina, storie col menù del '
         'giorno, le recensioni sotto controllo. Il sabato sera si vede.', 'Da 850 € al mese', CUCINA_CREATOR),
        ('Negozio', 'shopping-bag', 'Instagram e Facebook', 'La vetrina in tasca', 'Le novità in storia appena arrivano, i '
         'clienti in foto (se vogliono), la pubblicità a 3 chilometri dal negozio.', 'Da 690 € al mese', MANO_ROSA),
        ('Studio', 'briefcase', 'LinkedIn e Instagram', 'Farsi conoscere prima', 'Chi sceglie un dentista o un avvocato prima '
         'guarda chi è. Raccontiamo le persone dello studio, con calma.', 'Da 590 € al mese', SQUADRA_TAVOLO),
        ('Palestra', 'dumbbell', 'Instagram e TikTok', 'L’energia si vede', 'Video brevi dalle lezioni, i risultati di chi '
         'si allena, gli orari sempre aggiornati.', 'Da 750 € al mese', SELFIE_TREPPIEDE),
    ]
    out.append(t.section([t.one([t.classic('finder', {
        'eyebrow': 'In un clic', 'heading': 'Che social fa per te?', 'intro': 'Dicci cosa fai: ti diciamo dove stare e '
        'da dove cominceremmo.',
        'items': [{'option': o, 'icon': i, 'kicker': k, 'title': ti, 'text': te, 'meta': me, 'cta_text': 'Parliamone',
                   'cta_url': CONTATTI, 'image': K.img(fo), 'media_label': ''} for o, i, k, ti, te, me, fo in scelte],
        'zone_accent': BLU, 'zone_on': '#FFFFFF', 'card_bg': '#FFFFFF', 'media_ratio': '3/4', 'align': 'center',
        'default_index': '0', 'chip_bg': '#FFFFFF', 'chip_radius': 999, 'card_radius': 22, 'card_max_width': 760,
        'shadow': 'md'}, style={})], anim=None)],
        bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.label(t, 'Prova un mese', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Il primo mese lo paghi solo se resti.', size=70, tag='h2', color='#FFFFFF', pad_bottom=0,
                                 sizes=(50, 34))], wm='2-3', ws='1-1'),
               t.col([K.text(t, 'Un mese di piano e contenuti. Se alla fine non vuoi continuare, ci salutiamo e non ci devi '
                                'niente.', size=17, color=mix('#FFFFFF', 88)),
                      t.classic('button', {'text': 'Cominciamo', 'url': CONTATTI, 'alignment': 'left', 'bg_color': '#FFFFFF',
                                           'text_color': BLU, 'hover_bg_color': POMODORO, 'hover_text_color': INCHIOSTRO,
                                           'border_radius': 999, 'font_size': 15, 'font_weight': '700', 'text_transform': 'none',
                                           'letter_spacing': '0'}, style={'padding_top': 18})], wm='1-3', ws='1-1')],
              layout='custom', custom='64,36', gap=56, valign='end'),
    ], bg=solid(BLU), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_link():
    t = Tpl(ID, 'link')
    voci = [('Il calendario di novembre: cosa pubblicano i nostri clienti', CASI, 'calendar', 'filled'),
            ('Prenota una chiamata di 20 minuti', CONTATTI + '#scrivi', 'phone', 'filled'),
            ('Il caso Forno Calzolari: +38 % in tre mesi', CASI, 'trending-up', 'filled'),
            ('Scrivici su WhatsApp', 'https://wa.me/390510000451', 'message-circle', 'outline'),
            ('Cerchiamo una persona per la community', CONTATTI + '#scrivi', 'users', 'outline'),
            ('Dove siamo, in via del Pratello', CONTATTI + '#zone', 'map-pin', 'outline')]
    return [t.section([
        K.centered(t, [t.bare('linkinbio', {
            'preset': 'custom', 'bg': {'type': 'none'}, 'typography_preset': '',
            'items': [{'id': 'po-l%d' % k, 'title': ti, 'url': u, 'icon': i, 'image_url': '', 'style': s}
                      for k, (ti, u, i, s) in enumerate(voci, 1)],
            'profile_image': K.img(SELFIE_RING), 'profile_image_object_position': 'center 30%', 'profile_name': 'Pollice',
            'profile_bio': 'Agenzia social a Bologna. Tutto quello che ci chiedono, in un posto solo.', 'max_width': '460',
            'link_color': INCHIOSTRO, 'link_bg': mix(BLU, 7, '#FFFFFF'), 'link_hover_bg': mix(BLU, 16, '#FFFFFF'),
            'link_border_radius': '999', 'tile_padding': {'top': 16, 'right': 18, 'bottom': 16, 'left': 18}, 'gap': '12',
            'text_align': 'center', 'profile_name_color': INCHIOSTRO, 'bio_color': C['tenue'], 'background_color': '#FFFFFF',
            'background_gradient': '', 'show_social_icons': True,
            'social_links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                             {'platform': 'tiktok', 'url': 'https://tiktok.com'},
                             {'platform': 'linkedin', 'url': 'https://linkedin.com'}], 'shadow': 'none',
            'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''},
            'border_radius': radius(0)})], side=25),
    ], bg=K.sui_media(K.video_bg(V_BOLOGNA_V, overlay=40), 'bottom', 100, 70)[0], padding='large',
        style={'padding_top': 120, 'padding_bottom': 120})]


def build_casi():
    t = Tpl(ID, 'casi')
    out = [t.section([
        t.row([t.col([K.label(t, 'Casi', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Tre mesi col<br>Forno Calzolari.', tag='h1', size=92, color='#FFFFFF', pad_bottom=18,
                                 sizes=(64, 40)),
                      K.text(t, 'Un forno di quartiere, due vetrine e un profilo fermo da un anno. Ecco cosa è successo da agosto '
                                'a ottobre.', size=18, color=mix('#FFFFFF', 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_CUCINA, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'I follower, settimana per settimana', 'Da 1.240 <em>a 1.710.</em>', 'La linea sale piano '
                             'per un mese, poi il reel della sfoglia del 12 settembre fa il salto. Da lì non è più scesa.',
                             size=52, sizes=(40, 30), accento=BLU), wm='1-3', ws='1-1'),
               t.col([t.classic('chart', {
                   'chart_type': 'line', 'items': [{'label': l, 'value': str(v), 'color': BLU} for l, v in (
                       ('6 ago', 1240), ('13 ago', 1262), ('20 ago', 1281), ('27 ago', 1305), ('3 set', 1330), ('10 set', 1356),
                       ('17 set', 1498), ('24 set', 1561), ('1 ott', 1604), ('8 ott', 1639), ('15 ott', 1672), ('22 ott', 1690),
                       ('29 ott', 1710))],
                   'chart_height': '380', 'show_legend': False, 'show_title': False, 'tooltip_enabled': True,
                   'tooltip_bg': INCHIOSTRO, 'tooltip_text_color': '#FFFFFF', 'animate': True, 'bg_color': 'transparent',
                   'border_width': 3, 'border_color_override': BLU, 'fill_area': False, 'point_radius': 5,
                   'point_hover_radius': 8, 'point_style': 'circle', 'tension': 0.3, 'grid_color': mix(INCHIOSTRO, 10),
                   'text_color': C['tenue'], 'show_x_grid': False, 'show_y_grid': True, 'begin_at_zero': False, 'y_min': '1200',
                   'dataset_label': 'Follower', 'tooltip_suffix': ' follower', 'number_format': True}, style={})],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 90}))
    cerchi = [('38', '%', 'Follower in più'), ('4', ' min', 'Per rispondere a un messaggio'),
              ('212', '', 'Persone passate col coupon del reel')]
    out.append(t.section([
        t.row([t.col([t.classic('countercircle', {
            'value': v, 'max_value': '100' if s == '%' else ('10' if s == ' min' else '250'), 'suffix': s, 'prefix': '',
            'title': ti, 'size': '220', 'stroke_width': '6', 'stroke_color': c, 'track_color': C['filo'], 'text_color': INCHIOSTRO,
            'title_color': C['tenue'], 'duration': '2200', 'title_position': 'below'}, style={})], wm='1-3', ws='1-1')
            for (v, s, ti), c in zip(cerchi, (BLU, POMODORO, BLU))], layout='33-33-33', gap=32, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, CUCINA_CREATOR, 'Una cuoca che gira un video in cucina col telefono sul treppiede',
                              ratio='4/5')], wm='2-5', ws='1-1'),
               t.col(A.testa(K, t, 'Cosa abbiamo fatto', 'Una mattina al mese, <em>in laboratorio.</em>', '<p>Alle cinque, quando '
                             'si inforna. Abbiamo girato la sfoglia, le mani di Rita, il primo cliente in fila. Trenta giorni di '
                             'contenuti in una mattina.</p><p>Il resto è stato rispondere: a ogni commento, entro un’ora, con '
                             'il tono del forno. E una piccola pubblicità, nei tre chilometri attorno al negozio.</p>',
                             size=52, sizes=(40, 30), accento=BLU), wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.label(t, 'Il prossimo caso', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Può essere il tuo negozio.', size=70, tag='h2', color='#FFFFFF', pad_bottom=0,
                                 sizes=(50, 34))], wm='2-3', ws='1-1'),
               t.col([t.classic('button', {'text': 'Parliamone', 'url': CONTATTI, 'alignment': 'left', 'bg_color': '#FFFFFF',
                                           'text_color': BLU, 'hover_bg_color': POMODORO, 'hover_text_color': INCHIOSTRO,
                                           'border_radius': 999, 'font_size': 15, 'font_weight': '700', 'text_transform': 'none',
                                           'letter_spacing': '0'}, style={'padding_top': 0})], wm='1-3', ws='1-1')],
              layout='custom', custom='64,36', gap=56, valign='end'),
    ], bg=solid(BLU), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [t.section([
        t.row([t.col([K.label(t, 'Contatti', color=mix('#FFFFFF', 86)),
                      K.headline(t, 'Passa a trovarci,<br>o scrivici qui.', tag='h1', size=92, color='#FFFFFF', pad_bottom=18,
                                 sizes=(64, 40)),
                      K.text(t, 'Ti rispondiamo in giornata. Se preferisci, la prima chiacchierata la facciamo davanti a un caffè '
                                'in via del Pratello.', size=18, color=mix('#FFFFFF', 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_AMICHE, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 200, 'padding_bottom': 90})]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('text', 'La tua attività', 'attivita', True, segnaposto='Forno, negozio, studio…'),
             K.campo('select', 'Ti interessa', 'servizio', opzioni='Tutto\nStrategia\nContenuti\nCommunity\nPubblicità\n'
                     'Lavorare con voi'),
             K.campo('text', 'Il tuo profilo', 'profilo', segnaposto='@nomedelprofilo'),
             K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivici', ['Due righe', 'e un caffè.'],
                                'Dicci cosa fai e dove ti trovano adesso: guardiamo il tuo profilo prima di risponderti.', VOCI,
                                campi, 'Pollice — nuovo contatto', 'Invia', 'Grazie! Ti scriviamo entro stasera.'))
    out.append(X.zone(K, t, 'Dove siamo', ['In via del Pratello,', 'sopra la libreria.'], 'Primo piano, campanello «Pollice». '
                      'Il caffè c’è sempre.', [('A piedi', 'da piazza Maggiore, 12 minuti'), ('Bus', 'linee 13 e 19, fermata '
                      'Pratello'), ('Bici', 'rastrelliera davanti al portone'), ('Telefono', PHONE)],
                      LAT, LNG, 'Pollice · via del Pratello 21', False, HEX['primary']))
    out.append(K.domande(t, 'Prima di cominciare', ['Quello che', 'ci chiedono tutti.'], [
        ('Devo firmare per un anno?', 'No. Si comincia con un mese di prova e poi si va avanti di tre mesi in tre mesi.'),
        ('Chi fa le foto?', 'Noi, da te, una mattina al mese. Se ti va puoi esserci anche tu, ma non è obbligatorio.'),
        ('E se un cliente scrive una cosa brutta?', 'Ti avvisiamo subito e decidiamo insieme come rispondere. Non cancelliamo mai '
         'un commento solo perché è scomodo.'),
        ('Come so se funziona?', 'Ogni mese ti mandiamo un resoconto di una pagina: quante persone ti hanno visto, scritto, sono '
         'passate. Niente numeri che non servono.')], 'faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · contenuto non disponibile', color='#FF8A70', align='center'),
                       K.headline(t, 'Questa storia <em>è scaduta.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento='#FF8A70'),
                       K.text(t, 'Come le storie dopo ventiquattro ore: la pagina che cercavi non c’è più. Torna alla home.',
                              size=17, align='center', color=mix('#FFFFFF', 90)),
                       K.button(t, 'Alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_SCROLL, overlay=36), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 220, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito-telefono',
    ['agenzia', 'social', 'bologna', 'chiaro', 'slider pro', 'mazzo di carte', 'link in bio', 'scorrimento'],
    'Un’agenzia social a Bologna: nell’apertura un telefono gira un reel sopra la città al tramonto, e scorrendo spuntano le '
    'notifiche e salgono i cuori; poi i servizi volano via verso l’alto come storie scartate col pollice. Chi risponde '
    'adesso, i reel che scorrono, che social fa per te, un caso coi numeri, e la pagina «Link in bio» dell’agenzia.',
    [('Link', LINK), ('Casi', CASI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('link', 'Link', 'link'), ('casi', 'Casi', 'casi'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'DM Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Gabarito\', system-ui, sans-serif',
    '\'Space Mono\', ui-monospace, monospace', ['DM Sans', 'Gabarito', 'Space Mono'], corsivo_di=[],
    peso_titoli='800', raggio=18, grana=0, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'cuore'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['accent']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'senza'}
THEME['preview_video_v'] = '1'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'link.json': build_link(), 'casi.json': build_casi(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
