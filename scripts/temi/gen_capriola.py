# -*- coding: utf-8 -*-
"""Genera il tema «Capriola» (id capriola): una scuola di acrobatica aerea a Bologna — tessuti, cerchio, trapezio,
verticali; corsi da 6 a 66 anni, classi di otto, la prima lezione gratis, il saggio di giugno sotto il tendone.

La trovata: «il mondo a testa in giù». Nell'apertura un'acrobata è appesa a testa in giù, in un video tondo; scorrendo,
il tondo fa mezzo giro e lei torna dritta, mentre le parole intorno finiscono sottosopra (18 nov 2026, utente: niente corda
né cerchio d'acciaio, «lascia il video della ragazza in un cerchio, togli la corda e il resto»). Nelle pagine i titoli
arrivano capovolti e si raddrizzano a metà schermo, una foto si gira mentre scorri, e il pulsante «Non cliccare 5 volte,
pericolo» capovolge il sito (la Sorpresa nascosta delle Avanzate).

Tema chiaro: crema, corallo del tendone, blu circo, oro. Titoli in Unbounded, testo in Urbanist. Cursore: cerchio aereo
(dondola, capriola sui link).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'capriola'
NAME = 'Capriola'
CATEGORIA = 'Sport e benessere'

HEX = {
    'primary': '#FF5A3C', 'primary_contrast': '#1A0F14', 'secondary': '#2347FF', 'secondary_contrast': '#FFFFFF',
    'accent': '#FFC53D', 'dark': '#160F1E', 'light': '#FFFFFF', 'muted': '#F3EADB', 'muted_contrast': '#2A2130',
    'text': '#2A2130', 'text_muted': '#665A6C', 'background': '#FBF6EE', 'border': '#E6DBC9', 'link': '#C8361A',
}
HOME, CORSI, SCUOLA, PROVA = '{{PAGINA:home}}', '{{PAGINA:corsi}}', '{{PAGINA:la-scuola}}', '{{PAGINA:prova}}'
MAIL, PHONE, PHONE_TEL = 'ciao@capriola.example', '051 000 6262', 'tel:+390510006262'
LAT, LNG = '44.5075', '11.3514'

V_SALA, V_TESTA, V_CERCHIO, V_TRAPEZIO, V_SALTO, V_TESSUTI = (
    'pexels-video-8396591', 'pexels-video-5027046', 'pexels-video-10039643', 'pexels-video-8396596', 'pexels-video-6571883',
    'pexels-video-5026177')
VIDEOS = [V_SALA, V_TESTA, V_CERCHIO, V_TRAPEZIO, V_SALTO, V_TESSUTI]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(TESSUTI_BLU, GIULIA, TESSUTI_BN, TESSUTI_BIANCO, TESSUTI_ROSSI, CERCHIO_BUIO, CERCHIO_ROSA, SARA, CERCHIO_BIANCO,
 TRAPEZIO_BLU, TRAPEZIO, PIUME, CIELO, ACROBATA_BN, PALO, SALA_FINESTRE, YOGA_FINESTRA, YOGA_BIANCO, VERTICALE_PRATO,
 VERTICALE_SOLE, VERTICALE_MARE, MAGNESITE, MANI_BIANCHE, TENDONE, TENDE_BN, DAVIDE) = f(
    29715464, 29715462, 29715417, 23638608, 30131258, 15362819, 13425675, 5930281, 15362813, 35017410, 37070994, 36146672,
    1366838, 10899160, 33317816, 4323299, 4999856, 4999499, 37343220, 18353907, 20064293, 703008, 3763696, 28679253,
    38244823, 19138965)
PHOTOS = [TESSUTI_BLU, GIULIA, TESSUTI_BN, TESSUTI_BIANCO, TESSUTI_ROSSI, CERCHIO_BUIO, CERCHIO_ROSA, SARA, CERCHIO_BIANCO,
          TRAPEZIO_BLU, TRAPEZIO, PIUME, CIELO, ACROBATA_BN, PALO, SALA_FINESTRE, YOGA_FINESTRA, YOGA_BIANCO,
          VERTICALE_PRATO, VERTICALE_SOLE, VERTICALE_MARE, MAGNESITE, MANI_BIANCHE, TENDONE, TENDE_BN, DAVIDE]
DISEGNI = ['capriola-cerchio.svg']
LOGHI = ['capriola-logo-chiaro.png', 'capriola-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#FFC53D')
C = K.c
FONT_T = 'Unbounded'
VOCI = [('map-pin', 'Via del Pratello 41 · Bologna', PROVA + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


# ── gli effetti a scorrimento delle pagine ──
def capovolto(tile, fino=48):
    """La capriola dei titoli: il blocco arriva a testa in giù e si raddrizza a metà schermo (parallax delle Avanzate)."""
    tile.setdefault('advanced', {})['parallax'] = {'rotate': [{'value': 180, 'position': 0}, {'value': 0, 'position': fino}],
                                                    'opacity': [{'value': 0.25, 'position': 0}, {'value': 1, 'position': fino}]}
    return tile


def ondeggia(tile, da=-7, a=7):
    """Una foto che dondola piano mentre la pagina scorre, come appesa."""
    tile.setdefault('advanced', {})['parallax'] = {'rotate': [{'value': da, 'position': 0}, {'value': a, 'position': 100}]}
    return tile


def pericolo(tile):
    """Cinque clic e il sito si capovolge per tre secondi (Avanzate → Sorpresa nascosta); con il movimento ridotto resta
    solo la scritta."""
    tile.setdefault('advanced', {}).update({'sorpresa': 'capovolto', 'sorpresa_innesco': 'clic',
                                            'sorpresa_testo': 'Te l’avevamo detto.'})
    return tile


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 176, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '752/121', 'Capriola è una scuola di acrobatica aerea a Bologna: tessuti, cerchio, '
                   'trapezio e verticali, da 6 a 66 anni. La prima lezione è gratis.',
                   [('I corsi', CORSI), ('La scuola', SCUOLA), ('Prova', PROVA), ('Privacy', '/privacy-policy/')],
                   'La sala', 'Lunedì — venerdì 16 — 22<br>Sabato 9:30 — 13', VOCI,
                   '© 2026 Capriola asd · Bologna · C.F. 00000000000', [('instagram', 'https://instagram.com'),
                                                                       ('youtube', 'https://youtube.com')], scuro=False)


# ── L'apertura: il mondo a testa in giù ──
# Tela del computer 16:9: il video tondo ha il centro a 50% × 46%. Telefono (tela 640 × 1400): centro a 50% × 43,2%.
def apertura(t):
    m = K.mob
    # mezzo giro pulito, senza dondolio
    gira = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': 0}, {'p': 10, 'r': 0}, {'p': 52, 'r': 180}, {'p': 100, 'r': 180}]}
    sottosopra = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': 0, 'o': 100}, {'p': 10, 'r': 0, 'o': 100},
                                                      {'p': 52, 'r': 180, 'o': 100}, {'p': 58, 'r': 180, 'o': 100},
                                                      {'p': 66, 'r': 180, 'o': 0}, {'p': 100, 'r': 180, 'o': 0}]}
    poi = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 3}, {'p': da, 'o': 0, 'y': 3},
                                                             {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    velo = lambda lid, y, h, ang, mob: K.livello(lid, tipo='shape', x=0, y=y, w=100, h=h, entra='fadeIn', ritardo=0, durata=600,
                                                 esce='fadeOut', shapeGradient={'angle': ang, 'from': mix(C['nero'], 82),
                                                                                'to': mix(C['nero'], 0), 'fromStop': 30},
                                                 responsive={'mobile': mob})
    L = [
        velo('cp-velo-a', 0, 30, 180, {'y': 0, 'height': 24}),
        velo('cp-velo-b', 64, 36, 0, {'y': 58, 'height': 42}),
        # l'acrobata a testa in giù, in un tondo che fa mezzo giro
        K.livello('cp-acrobata', tipo='video', videoSrc=K.img(V_TESTA + '.mp4'), videoAutoplay=True, videoMuted=True,
                  videoLoop=True, x=36.15, y=21.37, w=27.7, h=49.26, borderRadius=999, entra='zoomIn', ritardo=300, durata=900,
                  customCSS='transform-origin:50% 50%;overflow:hidden;', scrollMove=gira,
                  responsive={'mobile': {'x': 13.1, 'y': 26.3, 'width': 73.8, 'height': 33.8}}),
        K.livello('cp-o', contenuto='Capriola · scuola di acrobatica aerea · Bologna', tag='h1', x=6, y=8, w=88, size=13, peso='600',
                  colore=mix(C['chiaro'], 86), align='center', letterSpacing=3, textTransform='uppercase', ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 4, 'y': 6.6, 'width': 92}}),
        K.livello('cp-t1', contenuto='Il mondo,', x=3, y=38, w=30, size=54, peso='700', colore=C['chiaro'], align='right',
                  fontFamily=FONT_T, lineHeight=1.0, entra='fadeInLeft', ritardo=400, durata=900,
                  customCSS='transform-origin:50% 50%;', scrollMove=sottosopra,
                  responsive={'mobile': {'fontSize': m(40), 'x': 4, 'y': 12, 'width': 92, 'textAlign': 'center'}}),
        K.livello('cp-t2', contenuto='a testa<br>in giù.', x=67, y=48, w=30, size=54, peso='700', colore=C['acc2'], align='left',
                  fontFamily=FONT_T, lineHeight=1.0, entra='fadeInRight', ritardo=550, durata=900,
                  customCSS='transform-origin:50% 50%;', scrollMove=sottosopra,
                  responsive={'mobile': {'fontSize': m(40), 'x': 4, 'y': 63.5, 'width': 92, 'textAlign': 'center'}}),
        # dopo il mezzo giro: lei è dritta, le parole no
        K.livello('cp-f', contenuto='E lei, adesso, è dritta.', x=10, y=78, w=80, size=34, peso='700', colore=C['chiaro'],
                  align='center', fontFamily=FONT_T, lineHeight=1.1, entra='fadeIn', ritardo=0, scrollMove=poi(60, 70),
                  responsive={'mobile': {'fontSize': m(22), 'x': 6, 'y': 69.5, 'width': 88}}),
        K.livello('cp-b1', tipo='button', contenuto='Lezione di prova', x=37, y=88, w='auto', size=13, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=999, letterSpacing=2, textTransform='uppercase',
                  buttonUrl=PROVA, entra='fadeIn', ritardo=0, scrollMove=poi(72, 82),
                  hover={'bgColor': '#FFFFFF', 'duration': 250}, responsive={'mobile': {'x': 8, 'y': 76.5, 'fontSize': m(12)}}),
        K.livello('cp-b2', tipo='button', contenuto='I corsi', x=54.5, y=88, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  letterSpacing=2, textTransform='uppercase', buttonUrl=CORSI, entra='fadeIn', ritardo=0, scrollMove=poi(72, 82),
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250}, responsive={'mobile': {'x': 58, 'y': 76.5, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('cp-1', K.sfondo_video(V_SALA, overlay=0.42, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([capovolto(K.sechead(t, 'Capriola · Bologna, dal 2014', ['Si impara', 'a testa in giù.'], size=68,
                                          layout='stack', size_mobile=40))], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Tre sale col soffitto a dodici metri, tappetoni ovunque e classi di otto persone. Si comincia '
                                'da terra, con la forza e la fiducia; il primo giro in aria arriva quando sei pronta, o pronto, '
                                'non prima.', size=17),
                      K.button(t, 'Prenota la prova', PROVA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('gift', 'La prima lezione <b>gratis</b>'), ('users', 'Classi <b>di otto</b>'),
                                ('heart', 'Da <b>6 a 66 anni</b>'), ('shield-check', 'Tappetoni <b>ovunque</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([capovolto(K.sechead(t, 'Le discipline', ['Quattro attrezzi,', 'un solo pavimento.'], size=60))]),
        P.carte(K, t, [(TESSUTI_BLU, 'Tessuti', 'Due teli e niente nodi', 'Salite, chiusure e cadute controllate: il tessuto '
                        'ti tiene, tu impari a fidarti.'),
                       (CERCHIO_BIANCO, 'Cerchio', 'Il cerchio aereo', 'Si sale, si gira, ci si siede nel vuoto. Il più '
                        'scelto da chi comincia.'),
                       (TRAPEZIO, 'Trapezio', 'Fisso e ballant', 'Prima il trapezio fermo, poi quello che dondola. Le '
                        'mani le prepari a terra.'),
                       (VERTICALE_PRATO, 'Verticali', 'A testa in giù, da terra', 'Equilibri sulle mani, contro il muro e '
                        'poi senza. La base di tutto il resto.')], altezza='420', colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SALTO, 'Il tappeto elastico', 'Su è giù, <em>giù è su.</em>',
                             'Dopo un mese di tappeto elastico il pavimento ti sembra un’opinione.', overlay=30))
    out.append(t.section([
        t.row([t.col([capovolto(K.sechead(t, 'Gira la foto', ['Chi è', 'a testa in giù?'], size=56, layout='stack',
                                          size_mobile=36)),
                      K.text(t, 'Scorri: la foto fa mezzo giro. Chi era appesa al contrario si ritrova dritta, e il soffitto '
                                'finisce sotto i piedi. In aria succede così ogni volta.', size=17),
                      pericolo(K.button(t, 'Non cliccare 5 volte, pericolo', '#', pad_top=22))], wm='1-2', ws='1-1'),
               t.col([ondeggia(K.image(t, TESSUTI_BN, 'Un’acrobata appesa ai tessuti bianchi, in bianco e nero', ratio='3/4'),
                               da=180, a=0)], wm='1-2', ws='1-1')],
              layout='custom', custom='50,50', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([K.numeri(t, [('12 m', 'di soffitto'), ('8', 'allievi per classe'), ('6 — 66', 'anni'),
                                              ('3', 'sale')], colonne=4, size=58)])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il saggio di giugno', ['Sotto il tendone,', 'tutti insieme.'], size=56)]),
        t.row([t.col([ondeggia(K.image(t, foto, alt, ratio='3/4'), da=da, a=-da)], wm='1-4', ws='1-2')
               for foto, alt, da in ((PIUME, 'Un’acrobata al trapezio col costume di piume', -8),
                                     (TESSUTI_ROSSI, 'Un’acrobata sui tessuti rossi', 6),
                                     (TESSUTI_BIANCO, 'Un’acrobata a testa in giù sul tessuto blu', -5),
                                     (TRAPEZIO_BLU, 'Un trapezista nella luce blu', 8))],
              layout='25-25-25-25', gap=18),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('Ho cominciato a quarantotto anni, convinta di non avere braccia. A giugno ero sul cerchio al saggio.', 'Patrizia', 'Bologna'),
        ('Mio figlio ha nove anni e adesso cammina sulle mani in corridoio. Grazie, credo.', 'Luca', 'Casalecchio'),
        ('Classi piccole davvero: Giulia ti guarda le mani a ogni salita.', 'Martina', 'San Lazzaro')],
        'Chi è salito', ['Tre persone,', 'tre prime volte.']))
    out.append(K.pannello(t, TENDE_BN, 'La prima lezione', 'Gratis, <em>senza impegno.</em>',
                          'Un’ora con l’insegnante, la sala, i tappetoni e un attrezzo da provare. Porta una tuta.',
                          'Prenota la prova', PROVA + '#scrivi'))
    return out


def build_corsi():
    t = Tpl(ID, 'corsi')
    out = [K.intro(t, 'I corsi', ['Dal primo appiglio', 'al saggio sotto il tendone.'],
                   'Quattro discipline, tre livelli e i corsi per i bambini: si cambia attrezzo quando si vuole.',
                   bg=K.video_bg(V_CERCHIO, overlay=34), size=84)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Tessuti', 'I tessuti', 'principianti e avanzati', 'Salite, avvolgimenti e le prime cadute controllate, sempre sul '
         'tappetone.', TESSUTI_BLU),
        ('Cerchio', 'Il cerchio', 'aereo, statico', 'Il più scelto da chi comincia: ci si siede nel vuoto dalla seconda lezione.',
         CERCHIO_BUIO),
        ('Trapezio', 'Il trapezio', 'fisso e ballant', 'Prima fermo, poi a dondolo con la longe: le mani si preparano a terra.',
         TRAPEZIO),
        ('Bambini', 'I bambini', 'da 6 a 12 anni', 'Tappeto elastico, verticali e un attrezzo aereo, a misura di chi ha nove anni.',
         VERTICALE_SOLE)], altezza=420)])],
        padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([capovolto(K.sechead(t, 'Le quote', ['Quanto costa', 'stare in aria.'], size=56, layout='stack',
                                          size_mobile=36)),
                      K.text(t, 'La tessera associativa è di 20 € l’anno e comprende l’assicurazione. La prima lezione è gratis.')],
                     wm='1-3', ws='1-1'),
               t.col([P.listino(K, t, [
                   ('Lezione singola', 'Un’ora e mezza, un attrezzo', '22 €', '', ''),
                   ('Dieci lezioni', 'Da usare in quattro mesi', '180 €', '', 'la più scelta'),
                   ('Mensile', 'Due lezioni a settimana', '75 €', '', ''),
                   ('Bambini', 'Una lezione a settimana, al mese', '48 €', '', ''),
                   ('Lezione privata', 'Tu e l’insegnante', '45 €', '', '')], immagini=False)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([X.orari(K, t, [('Lunedì e mercoledì', '17 — 22', 'Tessuti e cerchio, tre livelli'),
                                                ('Martedì e giovedì', '16 — 22', 'Bambini dalle 16, trapezio dalle 19'),
                                                ('Venerdì', '18 — 22', 'Verticali e sala libera'),
                                                ('Sabato', '9:30 — 13', 'Aerial yoga e lezioni private')])])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(K.domande(t, 'Prima di salire,', 'le domande di tutti.', [
        ('Serve essere forti o flessibili?', 'No: la forza e la flessibilità vengono con le lezioni. Si comincia da terra.'),
        ('Cosa porto?', 'Una tuta aderente e una maglia a maniche lunghe: il tessuto sulla pelle scotta. Niente anelli.'),
        ('E se ho paura dell’altezza?', 'Le prime lezioni si sta a un metro da terra, sopra i tappetoni. Si sale quando lo decidi tu.'),
    ], 'cp-faq'))
    return out


def build_scuola():
    t = Tpl(ID, 'la-scuola')
    out = [K.intro(t, 'La scuola', ['Tre sale', 'in via del Pratello.'],
                   'Un vecchio magazzino coi soffitti a dodici metri, la luce dall’alto e i tappetoni che non finiscono mai.',
                   bg=K.photo_bg(SALA_FINESTRE, overlay=40), size=92)]
    out.append(t.section([
        t.one([capovolto(K.sechead(t, 'Chi ti tiene', ['Gli insegnanti,', 'tutti dal circo.'], size=56))]),
        K.squadra(t, [(GIULIA, 'Giulia Neri', 'Tessuti', 'Dieci anni di compagnie in Francia: ti guarda le mani a ogni salita.',
                       'center 30%'),
                      (SARA, 'Sara Galli', 'Cerchio aereo', 'Dal ginnastica ritmica al cerchio: insegna a sederti nel vuoto.',
                       'center 25%'),
                      (DAVIDE, 'Davide Ferri', 'Verticali e trapezio', 'Cammina sulle mani da quando aveva otto anni.',
                       'center 35%')], ratio='4/5', size='320'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In sala', ['La magnesite,', 'le mani, la luce.'], size=56)]),
        P.fila_foto(K, t, [(MAGNESITE, 'Le mani nella ciotola di magnesite'), (YOGA_FINESTRA, 'Una lezione di aerial yoga'),
                           (MANI_BIANCHE, 'Le mani bianche di magnesite'), (YOGA_BIANCO, 'Un’allieva sul tessuto, nella luce')],
                    ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_TRAPEZIO, 'Lo spettacolo', 'Il saggio, <em>sotto il tendone.</em>',
                          'A giugno montiamo un tendone vero ai Giardini Margherita: ogni allievo ha il suo numero, anche il primo anno.',
                          'Prova una lezione', PROVA + '#scrivi', video=True))
    return out


def build_prova():
    t = Tpl(ID, 'prova')
    out = [K.intro(t, 'Prova', ['La prima lezione', 'è gratis.'],
                   'Scegli la disciplina e i giorni che ti vanno bene: ti richiamiamo per l’orario.',
                   bg=K.video_bg(V_TESSUTI, overlay=36), size=92)]
    conf = X.preventivo(K, t, [
        ('disciplina', 'La disciplina', 'uno', True, 'La prova è gratis; poi si può cambiare quando si vuole.'),
        ('formula', 'Dopo la prova', 'uno', True, '')], [
        ('tessuti', 'disciplina', 'Tessuti', 'Due teli.', 0, 'la più scelta', True),
        ('cerchio', 'disciplina', 'Cerchio aereo', 'Statico.', 0, '', False),
        ('trapezio', 'disciplina', 'Trapezio', 'Fisso.', 0, '', False),
        ('verticali', 'disciplina', 'Verticali', 'Da terra.', 0, '', False),
        ('singola', 'formula', 'Lezione singola', 'Quando vuoi.', 22, '', False),
        ('dieci', 'formula', 'Dieci lezioni', 'In quattro mesi.', 180, '', True),
        ('mensile', 'formula', 'Mensile', 'Due a settimana.', 75, '', False)],
        'La tua prova', PROVA + '?prova={configurazione}#scrivi', 'Tessera 20 € l’anno · prima lezione gratis', testo_pulsante='Prenota la prova')
    out.append(X.sezione_preventivo(K, t, 'Componi', ['Scegli,', 'il resto lo decidi dopo.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Età', 'eta'),
             K.campo('text', 'La disciplina', 'prova', larghezza='1-1'),
             K.campo('textarea', 'Qualcosa da dirci (un infortunio, un dubbio)', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Ti richiamiamo', 'per l’orario.'],
                                'Entro un giorno, dal lunedì al sabato.', VOCI, campi, 'Capriola — lezione di prova', 'Prenota la prova',
                                'Grazie! Ti richiamiamo entro un giorno.'))
    out.append(X.zone(K, t, 'La sala', ['Via del Pratello,', 'dentro le mura.'], 'Dieci minuti a piedi da piazza Maggiore.',
                      [('Autobus', 'linee 13 e 19, fermata Pratello'), ('Bici', 'rastrelliera in cortile'),
                       ('Spogliatoi', 'con le docce'), ('Telefono', PHONE)], LAT, LNG, 'Capriola · la sala', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · caduta dal trapezio', color=C['acc2'], align='center'),
                       capovolto(K.headline(t, 'Questa pagina <em>ha mollato la presa.</em>', size=80, tag='h1', align='center',
                                            pad_bottom=20), fino=40),
                       K.text(t, 'Niente paura: sotto c’è il tappetone. Torna alla home e riprova.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SALTO, overlay=55), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il mondo a testa in giù',
    ['sport', 'acrobatica', 'circo', 'tessuti aerei', 'scuola', 'bologna', 'chiaro', 'slider pro', 'scorrimento'],
    'Una scuola di acrobatica aerea a Bologna: nell’apertura un’acrobata è appesa a testa in giù in un video tondo; scorrendo '
    'il tondo fa mezzo giro e lei torna dritta, mentre le parole intorno finiscono sottosopra. Nelle pagine i titoli '
    'arrivano capovolti e si raddrizzano, una foto si gira e un pulsante, se lo premi cinque volte, capovolge il sito; poi le '
    'discipline da girare, le quote, gli orari e la prova gratis.',
    [('I corsi', CORSI), ('La scuola', SCUOLA), ('Prova', PROVA)],
    [('home', 'Home', None), ('corsi', 'I corsi', 'corsi'), ('la-scuola', 'La scuola', 'la-scuola'), ('prova', 'Prova', 'prova')],
    HEX, '\'Urbanist\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Unbounded\', system-ui, sans-serif',
    'ui-monospace, Menlo, monospace', ['Urbanist', 'Unbounded'], corsivo_di=[],
    peso_titoli='700', raggio=14, grana=3, titoli_line='1.05', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'cerchio'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['dark']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'decorativo'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'corsi.json': build_corsi(), 'la-scuola.json': build_scuola(), 'prova.json': build_prova(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
