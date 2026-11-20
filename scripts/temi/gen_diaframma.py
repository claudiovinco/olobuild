# -*- coding: utf-8 -*-
"""Genera il tema «Diaframma» (id diaframma): una casa di produzione video a Roma, dal 2009. Spot, documentari, video
per le aziende, videoclip; la troupe, il montaggio e il colore in casa.

La trovata (20 nov 2026): il sito si guarda dentro un obiettivo. Nell'apertura le sette lamelle del diaframma coprono
tutto, tranne un foro al centro da cui si vede il set; scorrendo il diaframma si apre (il livello delle lamelle si
ingrandisce e gira, nel movimento a scorrimento dello Slider Pro), il numero f scende da f/22 a f/1.4 e la luce entra.
Poi i servizi sono sezioni col passaggio «Diaframma» (Sezioni → Effetto sticky → «Diaframma»): le lamelle si chiudono
sulla sezione e si riaprono sulla successiva. Cursore: il ciak.

Tile poco usate: Video Playlist (lo showreel), Pellicole (i look del colore, sulla stessa foto di Roma), Montaggio (i
lavori sulla timeline, come in sala di montaggio), Film Reel, Schedule (il foglio di chiamata di una settimana di
riprese), Step Timeline.
Tema scuro: nero caldo, tungsteno, rosso della spia. Titoli in Gloock, testo in Inter Tight, DM Mono.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, radius  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import agenzie as A  # noqa: E402

ID = 'diaframma'
NAME = 'Diaframma'
CATEGORIA = 'Creativi e portfolio'

HEX = {
    'primary': '#FF9F2E', 'primary_contrast': '#140C04', 'secondary': '#E5402F', 'secondary_contrast': '#FFFFFF',
    'accent': '#F4D9B0', 'dark': '#090807', 'light': '#FFF8EE', 'muted': '#1A1714', 'muted_contrast': '#EFE6D8',
    'text': '#EFE6D8', 'text_muted': '#A89C8C', 'background': '#0F0D0B', 'border': '#2B2621', 'link': '#FF9F2E',
}
HOME, LAVORI, SET, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:lavori}}', '{{PAGINA:il-set}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'set@diaframma.example', '06 000 2009', 'tel:+39060002009'
LAT, LNG = '41.8766', '12.4757'

(V_SET, V_SET2, V_MONITOR, V_LENTE, V_MANI, V_LENTE2, V_TROUPE, V_ROMA, V_ROMA2, V_ROMA3, V_CARRELLO, V_LUCI) = (
    'pexels-video-%d' % n for n in (39402053, 36604691, 8089229, 4181829, 8089525, 7205347, 8089226, 31725311, 31725302,
                                     27690323, 37903859, 17770559))
VIDEOS = [V_SET, V_SET2, V_MONITOR, V_LENTE, V_MANI, V_LENTE2, V_TROUPE, V_ROMA, V_ROMA2, V_ROMA3, V_CARRELLO, V_LUCI]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(SET_ROSSO, ARRI, CIAK_LAGO, SET_CAMPO, MONITOR_MANO, CIAK_DIRECTOR, CAMERA_STRADA, CINEPRESA_MANI, CAMERA_VIOLA,
 CINEPRESA_TAVOLO, OBIETTIVI, FOTOCAMERA_BIANCA, CANON, OBIETTIVO_GIALLO, LAMELLE, LENTE_RIFLESSO, CIAK_VERDE, CIAK_TAVOLO,
 CIAK_MENSOLA, CIAK_PENNA, CIAK_ROSA, TROUPE_CASA, TROUPE_TENDA, MONITOR_BUIO, TROUPE_BAR, SEDIA_ROSSA, SEDIA_GRIGIA,
 SEDIA_LAGO, SEDIA_LUCE, SEDIA_OMBRA, REGISTA_SCHIENA, COLOR_RUOTE, COLOR_SCOPE, COLOR_MONITOR, COLOR_SALA, COLOR_POSTAZIONE,
 COLOR_UOMO, STUDIO_VUOTO, SOFTBOX, STUDIO_BIANCO, LUCE_VIOLA, ROMA_1, ROMA_2, ROMA_BN, ROMA_3, ROMA_4, ROMA_5, TRUCCO_1,
 TRUCCO_2, TRUCCO_3, TRUCCO_4, OPERATORE_1, OPERATORE_2, OPERATORE_3, OPERATORE_4, OPERATORE_5, MONTAGGIO_1, TIMELINE,
 MONTAGGIO_ROSSO, MIXER_AUDIO, MONTAGGIO_2, STUDIO_AUDIO) = f(
    8088386, 10395639, 19224454, 275977, 8089248, 1117132, 2443272, 3945336, 2873486, 3945322, 24017910, 30399599, 12058988,
    7789081, 10137512, 32651080, 5662857, 29508639, 29696177, 9809835, 12919007, 19102538, 13812367, 5026523, 9810391,
    30714259, 31011456, 10047641, 40980061, 30173144, 8088387, 11894290, 39694481, 8100065, 39694504, 8100063, 8102676,
    30332804, 29877910, 134469, 13808615, 19855229, 20386742, 20753327, 21550392, 38331930, 19855233, 38561051, 38561064,
    33690218, 18887217, 15911024, 11043433, 33329953, 14699461, 32292610, 15713296, 1188751, 11063289, 10681799, 9913480,
    5749192)
PHOTOS = [SET_ROSSO, ARRI, CIAK_LAGO, SET_CAMPO, MONITOR_MANO, CIAK_DIRECTOR, CAMERA_STRADA, CINEPRESA_MANI, CAMERA_VIOLA,
          CINEPRESA_TAVOLO, OBIETTIVI, FOTOCAMERA_BIANCA, CANON, OBIETTIVO_GIALLO, LAMELLE, LENTE_RIFLESSO, CIAK_VERDE,
          CIAK_TAVOLO, CIAK_MENSOLA, CIAK_PENNA, CIAK_ROSA, TROUPE_CASA, TROUPE_TENDA, MONITOR_BUIO, TROUPE_BAR, SEDIA_ROSSA,
          SEDIA_GRIGIA, SEDIA_LAGO, SEDIA_LUCE, SEDIA_OMBRA, REGISTA_SCHIENA, COLOR_RUOTE, COLOR_SCOPE, COLOR_MONITOR, COLOR_SALA,
          COLOR_POSTAZIONE, COLOR_UOMO, STUDIO_VUOTO, SOFTBOX, STUDIO_BIANCO, LUCE_VIOLA, ROMA_1, ROMA_2, ROMA_BN, ROMA_3, ROMA_4,
          ROMA_5, TRUCCO_1, TRUCCO_2, TRUCCO_3, TRUCCO_4, OPERATORE_1, OPERATORE_2, OPERATORE_3, OPERATORE_4, OPERATORE_5,
          MONTAGGIO_1, TIMELINE, MONTAGGIO_ROSSO, MIXER_AUDIO, MONTAGGIO_2, STUDIO_AUDIO]
DISEGNI = ['diaframma-iride.svg']
LOGHI = ['diaframma-logo-chiaro.png', 'diaframma-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=False,
        accento_su_media='#FF9F2E')
C = K.c
AMBRA, SPIA = '#FF9F2E', '#E5402F'
MONO = 'var(--olo-font-family-mono)'
TIT = 'var(--olo-font-family-heading)'
VOCI = [('map-pin', 'Via del Porto Fluviale 9 · Roma', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 230, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '636/117',
                   'Diaframma gira spot, documentari, video per le aziende e videoclip da Roma, dal 2009. Troupe, luci, '
                   'montaggio, colore e suono in casa, dalla prima idea alla copia per la messa in onda.',
                   [('Lavori', LAVORI), ('Il set', SET), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'In ufficio', 'Dal lunedì al venerdì 9:30 — 19<br>Sul set quando serve, anche di notte', VOCI,
                   '© 2026 Diaframma produzioni srl · Roma · P. IVA 00000002009', [('instagram', 'https://instagram.com'),
                                                                                  ('youtube', 'https://youtube.com')], scuro=True)


# ── L'apertura: il diaframma che si apre ──
# Le lamelle (SVG 1600 × 900, foro di raggio 64 al centro) coprono un livello del 120 %: sul computer il foro è largo
# circa 70 px, e a scala 14 ha già scoperto gli angoli della tela; sul telefono (tela 640 × 1400) basta meno. Il numero f
# scende in basso a destra, l'angolo che si scopre per ultimo.
def apertura(t):
    m = K.mob
    FN = [('f/22', 0, 12), ('f/11', 12, 22), ('f/5.6', 22, 32), ('f/2.8', 32, 42), ('f/1.4', 42, 54)]

    def numero(k, testo, da, a):
        tp = [(0, {'o': 0}), (max(0.01, da - 0.5), {'o': 0}), (da, {'o': 100}), (a - 0.5, {'o': 100}), (a, {'o': 0}), (100, {'o': 0})]
        if da == 0:
            tp = [(0, {'o': 100}), (a - 0.5, {'o': 100}), (a, {'o': 0}), (100, {'o': 0})]
        return K.livello('di-f%d' % k, contenuto=testo, x=74, y=80, w=22, size=60, peso='400', colore=AMBRA, align='right',
                         fontFamily=MONO, entra='fadeIn', ritardo=300, durata=500, scrollMove=A.scorri(tp),
                         responsive={'mobile': {'x': 50, 'y': 10.5, 'width': 44, 'fontSize': m(34)}})
    L = K.veli('di-velo', 'sinistra', 30, 62, colore=C['nero'], forza=86)
    L += [
        K.livello('di-lamelle', tipo='image', imageSrc=K.img('diaframma-iride.svg'), imageAlt='Le lamelle del diaframma che '
                  'si aprono', objectFit='cover', x=-10, y=-10, w=120, h=120, entra='fadeIn', ritardo=0, durata=400,
                  scrollMove=A.scorri([(0, {'s': 1, 'r': 0, 'o': 100}), (6, {'s': 1, 'r': 0, 'o': 100}),
                                       (52, {'s': 16, 'r': 78, 'o': 100}), (56, {'s': 19, 'r': 84, 'o': 0}),
                                       (100, {'s': 19, 'r': 84, 'o': 0})], costante=True)),
        K.livello('di-o', contenuto='Diaframma · produzioni video · Roma', x=5, y=16, w=40, size=13, peso='500',
                  colore=mix(C['chiaro'], 82), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=100,
                  responsive={'mobile': {'x': 6, 'y': 11.5, 'width': 46, 'fontSize': m(10)}}),
    ]
    L += [numero(k, *x) for k, x in enumerate(FN)]
    L += [
        K.livello('di-a1', contenuto='Prima c’è il buio.', x=5, y=42, w=30, size=58, peso='400', colore=C['chiaro'],
                  fontFamily=TIT, lineHeight=1.02, entra='fadeIn', ritardo=400, durata=900, scrollMove=A.appare(0, 1, 11, 16),
                  responsive={'mobile': {'x': 6, 'y': 70, 'width': 88, 'fontSize': m(36)}}),
        K.livello('di-a2', contenuto='Poi si apre il diaframma,<br>e con la luce entra la storia.', x=5, y=40, w=34, size=46,
                  peso='400', colore=C['chiaro'], fontFamily=TIT, lineHeight=1.06, entra='fadeIn', ritardo=0,
                  scrollMove=A.appare(18, 24, 40, 46), responsive={'mobile': {'x': 6, 'y': 70, 'width': 88, 'fontSize': m(30)}}),
        K.livello('di-t', contenuto='Giriamo storie<br>a Roma, dal 2009.', tag='h1', x=5, y=38, w=44, size=78, peso='400',
                  colore=C['chiaro'], fontFamily=TIT, lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=A.appare(56, 64),
                  responsive={'mobile': {'x': 6, 'y': 56, 'width': 88, 'fontSize': m(42)}}),
        K.livello('di-ts', contenuto='Spot, documentari, video per le aziende e videoclip. Dalla prima idea alla copia per la '
                  'messa in onda, tutto in casa.', x=5, y=60, w=34, size=18, peso='500', colore=mix(C['chiaro'], 90),
                  lineHeight=1.5, entra='fadeIn', ritardo=0, scrollMove=A.appare(60, 68),
                  responsive={'mobile': {'x': 6, 'y': 68, 'width': 86, 'fontSize': m(16)}}),
        K.livello('di-b1', tipo='button', contenuto='Guarda i lavori', x=5, y=74, w='auto', size=14, peso='700',
                  colore=HEX['primary_contrast'], bgColor=AMBRA, padding=18, borderRadius=2, buttonUrl=LAVORI, entra='fadeIn',
                  ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=A.appare(64, 72),
                  hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 80, 'width': 'auto', 'fontSize': m(13)}}),
        K.livello('di-b2', tipo='button', contenuto='Raccontaci il progetto', x=19.5, y=74, w='auto', size=14, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=18, borderRadius=2, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), buttonUrl=CONTATTI, entra='fadeIn', ritardo=0, textTransform='uppercase',
                  letterSpacing=2, scrollMove=A.appare(64, 72), hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 86, 'width': 'auto', 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('di-1', K.sfondo_video(V_SET, overlay=0.12, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── i servizi (Effetto sticky «Diaframma») ──
def servizio(t, numero, nome, testo, dati, video, destra=False, pos='center center'):
    blocco = [K.label(t, numero, color=AMBRA),
              K.headline(t, nome, size=60, tag='h2', pad_bottom=14, color=C['chiaro'], sizes=(46, 34)),
              K.text(t, testo, size=16, color=mix(C['chiaro'], 88)),
              K.text(t, dati, size=13, color=AMBRA, pad=(10, 0, 0, 0))]
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=A.scheda(mix(C['nero'], 86), r=2)), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='42,58', valign='center', anim=None)],
                     bg=K.video_bg(video, overlay=10, position=pos), padding='large', width='large',
                     sticky_effect='diaframma', sticky_colore='#17130F', sticky_h_align='center',
                     style={'padding_top': 100, 'padding_bottom': 100})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        K.centered(t, [K.label(t, 'Una casa di produzione, non un’agenzia', color=AMBRA, align='center'),
                       K.headline(t, 'Scriviamo, giriamo, montiamo e coloriamo <em>nella stessa casa.</em>', size=60, tag='h2',
                                  align='center', pad_bottom=18, sizes=(46, 32)),
                       K.text(t, 'Dodici persone fisse, una troupe che conosciamo da quindici anni e una sala di montaggio a '
                                 'trenta passi dal magazzino delle luci. Così una storia non passa di mano in mano: resta con chi '
                                 'l’ha pensata, fino alla consegna.', size=17, align='center')], side=12),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out += [
        servizio(t, '01 — Spot', 'Trenta secondi che restano', 'Per la televisione, per il cinema, per i social. Dalla '
                 'sceneggiatura al casting, dalla location al colore: un solo gruppo dall’inizio alla fine.',
                 'Da 3 a 10 giorni di riprese · consegna in 4 settimane', V_MONITOR),
        servizio(t, '02 — Documentari', 'Storie vere, tempi veri', 'Seguiamo le persone per settimane, senza copione. Poi '
                 'in montaggio troviamo la storia che c’era già, e la raccontiamo in 50 minuti o in 5.',
                 'Da 6 settimane a un anno · festival e piattaforme', V_ROMA2, destra=True),
        servizio(t, '03 — Video per le aziende', 'Il lavoro di chi lavora', 'Fabbriche, laboratori, uffici: mostriamo come si '
                 'fa una cosa e chi la fa. Per il sito, per le fiere, per chi entra in azienda il primo giorno.',
                 'Una o due giornate di set · versioni da 15, 60 e 180 secondi', V_TROUPE),
        servizio(t, '04 — Videoclip', 'La canzone si vede', 'Con i musicisti scegliamo un’idea sola e la portiamo fino in '
                 'fondo: un piano sequenza, una stanza, una luce. Il budget va dove si vede.',
                 'Uno o due giorni di riprese · consegna in 15 giorni', V_LUCI, destra=True),
    ]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Lo showreel 2026', 'Cinque lavori <em>in cinque minuti.</em>', 'Scegli dall’elenco: '
                             'ogni video parte da dove l’hai lasciato.', size=52, sizes=(40, 30)), wm='1-3', ws='1-1'),
               t.col([t.classic('videoplaylist', {
                   'videos': [{'url': K.img(v + '.mp4'), 'title': ti, 'duration': du, 'thumbnail': K.img(v + '.jpg')}
                              for v, ti, du in ((V_SET2, 'Notte al Pigneto · spot per un amaro', '0:30'),
                                                (V_ROMA, 'San Pietro alle sei · documentario', '4:10'),
                                                (V_MANI, 'Le mani di Ottavio · video per un’ottica', '1:00'),
                                                (V_CARRELLO, 'Carrello lungo · videoclip', '3:24'),
                                                (V_LENTE2, 'Messa a fuoco · sigla di un festival', '0:20'))],
                   'layout': 'sidebar-right', 'player_height': 460, 'sidebar_width': 300,
                   'sidebar_bg': mix(C['sala'], 92), 'text_color': C['testo'], 'active_color': AMBRA, 'show_duration': True,
                   'autoplay_next': False, 'thumbnail_ratio': 'custom', 'thumbnail_ratio_custom': '16/9'}, style={})],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='30,70', gap=56, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([t.classic('pellicole', {
            'immagine': K.img(ROMA_3), 'alt': 'Una via di Roma col sole basso, fra i palazzi',
            'pellicole': [{'nome': 'Tungsteno', 'nota': 'Il nostro: caldo nelle luci, freddo nelle ombre', 'look': 'cinestill', 'colore': ''},
                          {'nome': 'Notte romana', 'nota': 'Ottanio e arancio, i lampioni di Trastevere', 'look': 'notte', 'colore': ''},
                          {'nome': 'Archivio', 'nota': 'Bianco e nero di grana, per i documentari', 'look': 'bn', 'colore': ''},
                          {'nome': 'Pelle', 'nota': 'Morbido sui volti, per i ritratti', 'look': 'portra', 'colore': ''},
                          {'nome': 'Flash', 'nota': 'Lampo diretto, per i videoclip', 'look': 'flash', 'colore': ''}],
            'partenza': 1, 'confronto': True, 'divisore': 50, 'etichetta_grezzo': 'Com’è uscita dalla macchina',
            'mostra_nota': True, 'mostra_etichette': True, 'grana': 30, 'colore_accento': AMBRA, 'colore_divisore': C['chiaro'],
            'sfondo_rullino': mix(C['testo'], 6), 'nome_font_family': 'heading', 'nome_font_size': 22, 'nome_font_weight': '400',
            'nome_color': C['testo'], 'nota_font_size': 13, 'nota_color': C['tenue'], 'proporzione': '16/9',
            'raggio': radius(2), 'raggio_rullino': radius(2), 'posizione_rullini': 'sotto', 'gap': 18}, style={})],
                     wm='3-5', ws='1-1'),
               t.col(A.testa(K, t, 'In sala colore', 'La stessa via, <em>cinque luci.</em>', 'Il colore decide come ti senti '
                             'prima ancora di capire cosa stai guardando. Scegli un look e trascina la linea: a sinistra la '
                             'ripresa com’è uscita dalla macchina.', size=52, sizes=(40, 30)), wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_ROMA3, 'Hai una storia?', 'Noi abbiamo <em>la luce.</em>', 'Raccontacela in dieci righe: ti '
                             'rispondiamo con un’idea, un preventivo e una data per il primo sopralluogo.',
                             extra=[K.button(t, 'Scrivici', CONTATTI, kind='pieno', align='center')]))
    return out


def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [t.section([
        t.row([t.col([K.label(t, 'Lavori', color=AMBRA),
                      K.headline(t, 'Sulla timeline,<br>come in sala.', tag='h1', size=96, color=C['chiaro'], pad_bottom=18,
                                 sizes=(66, 42)),
                      K.text(t, 'Gli ultimi lavori montati uno dopo l’altro. Premi play: la testina scorre e ogni clip ti dice '
                                'per chi l’abbiamo girata.', size=18, color=mix(C['chiaro'], 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_LENTE, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 230, 'padding_bottom': 90})]
    clip = [(V_SET2, 'Notte al Pigneto', 'Spot · 30 secondi', 'Amaro Cinquecolli', 9),
            (V_ROMA, 'San Pietro alle sei', 'Documentario · 52 minuti', 'Rai Cultura', 8),
            (V_MANI, 'Le mani di Ottavio', 'Video aziendale · 60 secondi', 'Ottica Lumen', 7),
            (V_CARRELLO, 'Carrello lungo', 'Videoclip', 'Marta e i Fiumi', 8),
            (V_TROUPE, 'Primo giorno', 'Video aziendale · 3 minuti', 'Cantieri Navali Tirreno', 9),
            (V_LUCI, 'Luce dura', 'Videoclip', 'Ottone', 7)]
    out.append(t.section([t.one([t.classic('montaggio', {
        'clip': [{'titolo': ti, 'tipo': tp, 'cliente': cl, 'anno': '2026', 'video': K.img(v + '.mp4'), 'immagine': K.img(v + '.jpg'),
                  'durata': du, 'link': CONTATTI, 'testo_link': 'Un progetto così', 'colore': ''} for v, ti, tp, cl, du in clip],
        'nome_sequenza': 'DIAFRAMMA_REEL_2026_v12_definitivo', 'fps': 25, 'parti_da_sola': True, 'mostra_audio': True,
        'mostra_info': True, 'colore_fondo': C['sala'], 'colore_testo': C['testo'], 'colore_testina': '#FF6A55',
        'colore_clip': mix(AMBRA, 30, C['nero']), 'colore_audio': mix(AMBRA, 70), 'titolo_font_family': 'heading',
        'titolo_font_size': 46, 'titolo_font_size_mobile': 30, 'titolo_font_weight': '400', 'titolo_color': C['testo'],
        'raggio': radius(2), 'proporzione': '21/9', 'altezza_traccia': 58, 'posizione_info': 'lato'}, style={})], anim=None)],
        padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([t.one([t.classic('filmreel', {
        'title': 'Dal set', 'show_title': True, 'hint_text': 'Trascina la pellicola', 'show_hint': True,
        'intro_eyebrow': 'Foto di scena', 'intro_text': 'Quello che succede dietro la macchina, fra un ciak e l’altro.',
        'show_intro': True,
        'items': [{'image': K.img(fo), 'name': no, 'tag': ta, 'size': si, 'link': ''} for fo, no, ta, si in (
            (OPERATORE_2, 'Prova obiettivi', 'Notte al Pigneto', 'tall'), (CIAK_DIRECTOR, 'Scena 3, ciak 7', 'Primo giorno', 'wide'),
            (TRUCCO_1, 'Trucco alle sei', 'Luce dura', 'tall'), (TROUPE_TENDA, 'Sopralluogo', 'San Pietro alle sei', 'wide'),
            (COLOR_POSTAZIONE, 'Sala colore', 'Carrello lungo', 'tall'), (SEDIA_ROSSA, 'Pausa', 'Ottone', 'wide'),
            (OPERATORE_5, 'In spalla', 'Le mani di Ottavio', 'tall'))],
        'rec_overlay': True, 'velocity_skew': True, 'skew_max': '6', 'progress_bar': True, 'scroll_mode': 'free',
        'progress_color': AMBRA, 'accent': AMBRA, 'bg_color': C['fondo'], 'border_color': C['filo']}, style={})], anim=None)],
        width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.row([t.col([K.label(t, 'Per chi ha una storia', color=AMBRA),
                      K.headline(t, 'Il prossimo clip sulla timeline <em>può essere il tuo.</em>', size=60, tag='h2', pad_bottom=0,
                                 sizes=(44, 32))], wm='2-3', ws='1-1'),
               t.col([K.button(t, 'Raccontaci il progetto', CONTATTI, kind='pieno', pad_top=0)], wm='1-3', ws='1-1')],
              layout='custom', custom='66,34', gap=48, valign='end'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_set():
    t = Tpl(ID, 'il-set')
    out = [t.section([
        t.row([t.col([K.label(t, 'Il set', color=AMBRA),
                      K.headline(t, 'Una settimana<br>di riprese.', tag='h1', size=96, color=C['chiaro'], pad_bottom=18,
                                 sizes=(66, 42)),
                      K.text(t, 'Come lavoriamo, dal primo caffè con te alla copia per la messa in onda. Con il foglio di '
                                'chiamata vero di uno spot girato a ottobre.', size=18, color=mix(C['chiaro'], 90))],
                     wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_CARRELLO, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 230, 'padding_bottom': 90})]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Foglio di chiamata', 'Notte al Pigneto, <em>cinque giorni.</em>', 'Le celle accese sono quelle '
                             'in cui la macchina gira. Il resto è preparazione: senza, non gira niente.', size=52,
                             sizes=(40, 30)), wm='1-3', ws='1-1'),
               t.col([t.classic('schedule', {
                   'eyebrow': 'Ottobre 2026', 'heading': 'La settimana dello spot', 'days': 'Lun, Mar, Mer, Gio, Ven',
                   'corner_label': 'Ora',
                   'rows': [{'time': '06:30', 'cells': 'Sopralluogo | Trucco | Trucco | Trucco | Montaggio'},
                            {'time': '09:00', 'cells': 'Prova luci | !Esterni Pigneto | !Bar Necci | !Interni casa | Montaggio'},
                            {'time': '14:00', 'cells': 'Prova costumi | !Esterni Pigneto | !Bar Necci | !Interni casa | Colore'},
                            {'time': '19:00', 'cells': 'Riunione | !Notte · strada | Riunione | !Notte · terrazza | Prima visione'}],
                   'zone_accent': AMBRA, 'zone_on': HEX['primary_contrast'], 'cell_bg': C['sala'],
                   'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
                   'head_color': C['tenue'], 'heading_color': C['testo'], 'align': 'left'}, style={})], wm='2-3', ws='1-1')],
              layout='custom', custom='32,68', gap=56, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    passi = [('01', 'Settimana 1', OBIETTIVI, 'L’idea', 'Una pagina', 'sola', 'Ci racconti cosa vuoi dire. Torniamo con un’idea '
              'scritta in una pagina, un riferimento visivo e un preventivo chiuso.', '1 settimana', 'Gratis'),
             ('02', 'Settimane 2–3', SEDIA_LUCE, 'La preparazione', 'Casting', 'e location', 'Sopralluoghi, provini, costumi, '
              'permessi del Comune. Il foglio di chiamata è pronto tre giorni prima del set.', '2 settimane', 'Nel preventivo'),
             ('03', 'Settimana 4', ARRI, 'Il set', 'Si gira', '', 'Troupe di otto persone, macchina da cinema, luci nostre. Tu '
              'guardi dal monitor, noi giriamo.', 'Da 1 a 10 giorni', 'Nel preventivo'),
             ('04', 'Settimane 5–6', COLOR_SALA, 'Montaggio e colore', 'In sala', 'con te', 'Due giri di montaggio con te in sala, '
              'poi colore, suono, sottotitoli e le versioni per ogni schermo.', '2 settimane', 'Nel preventivo')]
    out.append(t.section([
        t.one(A.testa(K, t, 'Come lavoriamo', 'Dall’idea <em>alla consegna.</em>', None, size=56, sizes=(42, 32))),
        t.one([t.classic('step-timeline', {
            'items': [{'counter': c, 'tag_text': tg, 'tag_dot_color': AMBRA, 'media_type': 'image', 'media_image': K.img(fo),
                       'media_label': '', 'media_content': '', 'media_bg': mix(C['chiaro'], 6), 'media_color': AMBRA,
                       'pre_title': pre, 'title': ti, 'title_accent': ac, 'title_accent_italic': False, 'title_after': '',
                       'title_after_italic': False, 'description': de, 'footer_value': fv, 'footer_label': fl,
                       'separator_text': ''} for c, tg, fo, pre, ti, ac, de, fv, fl in passi],
            'columns': 4, 'columns_tablet': 2, 'show_timeline': True, 'timeline_line_color': mix(AMBRA, 45),
            'timeline_dot_color': AMBRA,
            'counter_font_family': 'heading', 'counter_size': 72, 'counter_color': AMBRA, 'counter_italic': False,
            'counter_weight': '400', 'tag_color': C['tenue'], 'media_aspect_ratio': '4/3', 'media_radius': radius(2),
            'media_radius_hover': radius(2), 'pre_title_color': AMBRA, 'title_font_family': 'heading', 'title_size': 28,
            'title_weight': '400', 'title_color': C['testo'], 'title_accent_color': AMBRA, 'description_color': C['tenue'],
            'footer_value_color': C['testo'], 'footer_label_color': C['tenue'], 'separator_color': AMBRA}, style={})],
            anim=None),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, REGISTA_SCHIENA, 'La regista seduta sulla sedia col suo nome, di spalle', ratio='4/5',
                              drift=True)], wm='2-5', ws='1-1'),
               t.col(A.testa(K, t, 'Chi c’è', 'Dodici persone, <em>una sedia col nome.</em>', 'Regia, produzione, fotografia, '
                             'montaggio, colore e suono: siamo dodici, fissi. Per i set grandi chiamiamo la troupe con cui '
                             'giriamo da quindici anni. La sedia col nome ce l’ha solo chi dirige, ma a turno ci sediamo tutti.',
                             size=52, sizes=(40, 30)) + [K.button(t, 'Lavora con noi', CONTATTI + '#scrivi', pad_top=18)],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [t.section([
        t.row([t.col([K.label(t, 'Contatti', color=AMBRA),
                      K.headline(t, 'Raccontaci<br>la storia.', tag='h1', size=96, color=C['chiaro'], pad_bottom=18,
                                 sizes=(66, 42)),
                      K.text(t, 'Bastano dieci righe. Ti rispondiamo entro due giorni con un’idea e una data per vederci.',
                             size=18, color=mix(C['chiaro'], 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_ROMA3, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 230, 'padding_bottom': 90})]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Che cosa', 'tipo', True, opzioni='Spot\nDocumentario\nVideo aziendale\nVideoclip\nAltro'),
             K.campo('date', 'Per quando', 'quando'), K.campo('select', 'Budget', 'budget', opzioni='Fino a 10.000 €\n'
                                                                                              '10.000 — 40.000 €\nOltre 40.000 €\n'
                                                                                              'Non lo so ancora'),
             K.campo('textarea', 'La storia in dieci righe', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Il progetto', ['Dieci righe', 'e un caffè.'],
                                'Se hai già un copione o dei riferimenti, scrivici dove trovarli.', VOCI, campi,
                                'Diaframma — nuovo progetto', 'Invia la storia',
                                'Grazie! La leggiamo tutti insieme e ti rispondiamo entro due giorni.'))
    out.append(X.zone(K, t, 'Dove siamo', ['Al Porto Fluviale,', 'dietro il gazometro.'], 'Un ex magazzino con la sala di '
                      'posa, il montaggio e il colore. Il caffè è sempre acceso.', [('Metro', 'linea B, Piramide, 10 minuti'),
                      ('Treno', 'Ostiense, 8 minuti'), ('Auto', 'parcheggio in cortile per le troupe'), ('Telefono', PHONE)],
                      LAT, LNG, 'Diaframma · via del Porto Fluviale 9', True, HEX['primary']))
    out.append(K.domande(t, 'Prima di cominciare', ['Quello che', 'ci chiedono al primo caffè.'], [
        ('Quanto costa uno spot?', 'Dipende dai giorni di set e da quante persone servono davanti alla macchina. Uno spot semplice '
         'parte da 12.000 €; il preventivo è chiuso e non cambia durante il lavoro.'),
        ('Lavorate anche fuori Roma?', 'Sì, in tutta Italia e all’estero. La troupe viaggia con noi, le luci anche.'),
        ('Posso venire sul set?', 'Devi: un monitor e una sedia sono per te. E anche in sala di montaggio, almeno due volte.'),
        ('Di chi sono i diritti?', 'Del girato montato sono tuoi, per sempre. Il girato grezzo lo teniamo noi per due anni, se '
         'ti serve un’altra versione.')], 'faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · scena tagliata', color=AMBRA, align='center'),
                       K.headline(t, 'Questa scena <em>è rimasta in montaggio.</em>', size=80, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=AMBRA),
                       K.text(t, 'La pagina che cercavi non è nel montaggio finale. Torna all’inizio del film.', size=17,
                              align='center', color=mix(C['chiaro'], 90)),
                       K.button(t, 'Dall’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_LENTE2, overlay=36), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito-obiettivo',
    ['agenzia', 'produzione video', 'cinema', 'roma', 'scuro', 'slider pro', 'diaframma', 'scorrimento'],
    'Una casa di produzione video a Roma: nell’apertura le lamelle di un obiettivo coprono tutto tranne un foro sul set, e '
    'scorrendo il diaframma si apre mentre il numero f scende fino a f/1.4; poi i servizi passano l’uno nell’altro con le '
    'lamelle che si chiudono e si riaprono. Lo showreel, i look del colore, i lavori sulla timeline, il foglio di chiamata.',
    [('Lavori', LAVORI), ('Il set', SET), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('lavori', 'Lavori', 'lavori'), ('il-set', 'Il set', 'il-set'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Inter Tight\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Gloock\', Georgia, serif',
    '\'DM Mono\', ui-monospace, monospace', ['Inter Tight', 'Gloock', 'DM Mono'], corsivo_di=[],
    peso_titoli='400', raggio=2, grana=5, titoli_line='1.02', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'ciak'
THEME['cursor']['ring_color'] = HEX['accent']
THEME['cursor']['dot_color'] = HEX['primary']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'grazie'}
THEME['preview_video_v'] = '1'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'lavori.json': build_lavori(), 'il-set.json': build_set(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
