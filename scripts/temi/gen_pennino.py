# -*- coding: utf-8 -*-
"""Genera il tema «Pennino» (id pennino): cartoleria e stilografiche in calle della Mandola, a Venezia — stilografiche e
inchiostri, quaderni cuciti a mano, carta da lettere, sigilli e ceralacca, acquerelli; i corsi di calligrafia al piano di sopra.

Tema chiaro di carta: il blu dell'inchiostro sui pulsanti, il rosso della ceralacca sui bollini e sui prezzi in offerta,
l'oro del pennino sui fondi scuri, la carta avorio della pagina.
Titoli in Bodoni Moda (col corsivo vero sulle parole d'accento), testo in Karla, prezzi e orari in IBM Plex Mono; la lettera
del pezzo forte in Dancing Script, la grafia di chi scrive col pennino.

Apertura: la vetrina delle offerte, bassa, che gira come un biglietto (flipH): la stilografica Laguna col nome inciso, il kit
della lettera, gli acquerelli nuovi; sotto le quattro schede coi prodotti veri; il mega menu apre il negozio, le offerte e le
novità. A metà pagina «la lettera»: lo slider resta fermo mentre si scorre, il pennino scrive la lettera riga per riga, poi il
foglio si rimpicciolisce e scende nella busta, la busta si chiude e il sigillo di ceralacca ci cade sopra. Cursore: il pennino,
che scrive in corsivo dietro al puntatore.

WooCommerce: il negozio coi filtri, la scheda con la galleria (il video del pennino) e la punta (fine, media, larga), il
carrello, la cassa a passi, l'account, i preferiti; i corsi di calligrafia con la prenotazione, la guida per scegliere il pennino.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'pennino'
NAME = 'Pennino'

HEX = {
    'primary': '#1F3A8A', 'primary_contrast': '#FFFFFF', 'secondary': '#D9B54A', 'secondary_contrast': '#131A2E',
    'accent': '#B3312C', 'dark': '#131A2E', 'light': '#FFFFFF', 'muted': '#EFE8DA', 'muted_contrast': '#1A1F33',
    'text': '#1A1F33', 'text_muted': '#5A5866', 'background': '#F8F4EA', 'border': '#E2D9C6', 'link': '#1F3A8A',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'calligrafia', 'pennini', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'negozio@pennino.example', '+39 041 000 1843', 'tel:+390410001843'

V_PENNINO, V_QUADERNO, V_BOCCETTA, V_INTINGE, V_CARTELLINO, V_BIGLIETTO, V_LETTERING, V_SFOGLIA, V_NUVOLA, V_VIOLA = (
    'pexels-video-9388054', 'pexels-video-854192', 'pexels-video-7344549', 'pexels-video-11137474', 'pexels-video-3826729',
    'pexels-video-3826737', 'pexels-video-3826741', 'pexels-video-7114738', 'pexels-video-4508065', 'pexels-video-4508060')
VIDEOS = [V_PENNINO, V_QUADERNO, V_BOCCETTA, V_INTINGE, V_CARTELLINO, V_BIGLIETTO, V_LETTERING, V_SFOGLIA, V_NUVOLA, V_VIOLA]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    33868622, 5264514, 15876131, 7333611, 6918487, 35522712, 15915565, 114107, 7333604, 6149082, 6149064, 6149116, 15877831,
    20729443, 7718812, 19143361, 7718817, 14436269, 18332807, 7718893, 40249883, 4968675, 3838324, 4792671, 36824942, 5485169,
    4792674, 3826681, 36824941, 194098, 10753540, 4519774, 9939269, 10876231, 28169331, 4514774, 7610808, 18624694, 11934017,
    7718655, 19216748, 19216747, 3838319, 9017595, 3838334, 15713286, 5706225, 6170644, 4968706, 19196316, 12336448, 7859265,
    1213431, 19196318, 7859492, 5412106, 114123, 7859162, 6607587, 5705956, 11650187, 5706047, 11650189, 11650092, 33738528,
    30109297, 30109289, 4348298, 34149050, 3826660, 3826654, 7335421, 3826691, 3826652, 6918481, 7333599)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='500', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
# l'oro del pennino è il secondario (il kit lo usa sui fondi scuri), la ceralacca l'accento (prezzi in offerta, bollini)
CERALACCA, ORO = C['acc2'], C['sec']
TITOLI, MONO, MANO = 'Bodoni Moda', 'IBM Plex Mono', 'Dancing Script'
N = Negozio(K, NAME, P, raggio=4, ratio='4-5', card='border', hover='zoom', testata_scura=False,
            consegna='Incarto a mano, spedizione in 24 ore', evidenza='Il più regalato', marca='', peso_titoli='500')

CATEGORIE = [
    ('stilografiche', 'Stilografiche', 'Da quella per cominciare all’ebanite.', 'pexels-5264514.jpg'),
    ('inchiostri', 'Inchiostri', 'Il blu Laguna, il rosso ceralacca, l’oro.', 'pexels-7333604.jpg'),
    ('quaderni', 'Quaderni e agende', 'Cuciti a mano, in pelle, a puntini.', 'pexels-14436269.jpg'),
    ('carta', 'Carta e buste', 'Da lettere, da invito, i biglietti.', 'pexels-4792671.jpg'),
    ('sigilli', 'Sigilli e ceralacca', 'L’iniziale in ottone, la ceralacca.', 'pexels-36824942.jpg'),
    ('colori', 'Matite e acquerelli', 'Per chi disegna: godet, matite, pennelli.', 'pexels-19196316.jpg'),
]
PUNTE = ['Fine', 'Media', 'Larga']


def articolo(sku, nome, slug, cat, prezzo, sconto, voto, rec, evid, foto, punti, testo, punta=False, **kw):
    if punta:
        kw['varianti'] = ('punta', PUNTE, [])
    return prodotto(sku, nome, slug, cat, prezzo, sconto, 40, voto, rec, evid, ['pexels-%s.jpg' % f for f in foto], punti, testo,
                    **kw)


PRODOTTI = [
    articolo('PEN-001', 'Stilografica Laguna, blu notte', 'stilografica-laguna', 'stilografiche', 89, 69, 4.9, 214, True,
             [5264514, 33868622], ['Pennino d’acciaio lucidato', 'Corpo in resina blu notte', 'Il nome inciso, gratis',
                                   'Converter e due cartucce'],
             'La stilografica di casa: corpo blu notte, pennino d’acciaio, il vostro nome inciso sul cappuccio.', punta=True,
             video=V_PENNINO, cross=['INK-006', 'QUA-009']),
    articolo('PEN-002', 'Stilografica per cominciare', 'stilografica-cominciare', 'stilografiche', 29, 24, 4.7, 388, True,
             [6918487, 6918481], ['Impugnatura sagomata', 'Pennino d’acciaio', 'Sei cartucce blu', 'Per i dieci anni'],
             'Quella per cominciare: l’impugnatura che guida le dita, il pennino che non graffia, sei cartucce blu.', punta=True),
    articolo('PEN-003', 'Stilografica in ebanite nera', 'stilografica-ebanite', 'stilografiche', 149, 0, 4.9, 61, False,
             [114107, 35522712], ['Ebanite tornita a mano', 'Pennino in oro 14 carati', 'Caricamento a stantuffo',
                                  'Astuccio in pelle'],
             'L’ebanite tornita a mano, il pennino d’oro che si adatta alla vostra mano dopo una settimana.', punta=True,
             video=V_QUADERNO),
    articolo('PEN-004', 'Cannuccia da calligrafia con sei pennini', 'cannuccia-pennini', 'stilografiche', 24, 0, 4.8, 143, False,
             [7333611, 7718655], ['Cannuccia in legno di pero', 'Sei pennini a punta e tronchi', 'Per il corsivo inglese',
                                  'Scatola di latta'],
             'La cannuccia di pero e sei pennini, a punta e tronchi: quella che usiamo ai corsi.', video=V_CARTELLINO),
    articolo('PEN-005', 'Poggiapenne in ottone, il serpente', 'poggiapenne-serpente', 'stilografiche', 32, 0, 4.6, 52, False,
             [15915565, 15876131], ['Ottone brunito', 'Fuso a cera persa', 'Per una penna', 'Fatto a Venezia'],
             'Il serpente d’ottone su cui la penna riposa: fuso a cera persa da un fonditore della Giudecca.'),
    articolo('INK-006', 'Inchiostro blu Laguna, 50 ml', 'inchiostro-laguna', 'inchiostri', 14, 0, 4.9, 506, True,
             [7333604, 15877831], ['Blu che vira al grigio', 'Asciuga in dieci secondi', 'Boccetta di vetro col calamaio',
                                   'Per ogni stilografica'],
             'Il nostro blu: scuro appena scritto, vira al grigio della laguna quando asciuga.', video=V_INTINGE),
    articolo('INK-007', 'Inchiostro rosso ceralacca, 50 ml', 'inchiostro-ceralacca', 'inchiostri', 14, 11, 4.7, 188, False,
             [6149064, 6149082], ['Rosso pieno', 'Per le correzioni e i titoli', 'Boccetta di vetro', 'Per ogni stilografica'],
             'Il rosso della ceralacca, per i titoli, le correzioni e le firme che si devono vedere.'),
    articolo('INK-008', 'Inchiostro oro per i biglietti, 30 ml', 'inchiostro-oro', 'inchiostri', 16, 0, 4.6, 97, False,
             [6149116, 11650189], ['Oro con la mica', 'Per il pennino da calligrafia', 'Da agitare', 'Sulla carta scura'],
             'L’oro con la mica, che brilla sulla carta scura: per i biglietti e le buste degli inviti.'),
    articolo('QUA-009', 'Quaderno in pelle cucito a mano', 'quaderno-pelle', 'quaderni', 45, 0, 4.9, 233, True,
             [14436269, 20729443], ['Pelle conciata al vegetale', 'Carta da 120 grammi', 'Cucito a mano, filo cerato',
                                    'Si apre piatto'],
             'La pelle che invecchia con voi, la carta da 120 grammi che regge l’inchiostro, il filo cerato.'),
    articolo('QUA-010', 'Tre taccuini di carta riciclata', 'taccuini-tre', 'quaderni', 18, 15, 4.6, 412, False,
             [7718812, 7718817], ['A righe, a quadretti, bianco', 'Carta riciclata da 90 grammi', 'Formato A6',
                                  'Copertine in cartoncino'],
             'Tre taccuini da tasca, a righe, a quadretti e bianco: per la lista della spesa e le idee.', video=V_SFOGLIA),
    articolo('QUA-011', 'Taccuino arancio a puntini', 'taccuino-arancio', 'quaderni', 12, 0, 4.7, 266, False,
             [7718893, 40249883], ['Puntini ogni 5 mm', 'Elastico e segnalibro', 'Formato A5', 'Carta da 100 grammi'],
             'L’arancio che si trova in fondo alla borsa: puntini, elastico, segnalibro.'),
    articolo('QUA-012', 'Agenda settimanale 2027', 'agenda-2027', 'quaderni', 28, 0, 4.8, 154, False, [5706225, 15713286],
             ['Una settimana su due pagine', 'Copertina in tela', 'Le fasi della luna', 'Rubrica in fondo'],
             'L’anno nuovo su due pagine alla volta: la tela blu, le fasi della luna, la rubrica.'),
    articolo('CAR-013', 'Carta da lettere vergata, cinquanta fogli', 'carta-vergata', 'carta', 22, 0, 4.8, 121, False,
             [5485169, 4792674], ['Carta di cotone vergata', 'Formato A4', 'Cinquanta fogli', 'Venti buste foderate'],
             'La carta di cotone con le righe d’acqua in controluce, e le buste foderate di blu.'),
    articolo('CAR-014', 'Biglietti con busta kraft, dieci', 'biglietti-kraft', 'carta', 16, 13, 4.7, 309, True,
             [6607587, 4968675], ['Cartoncino avorio', 'Buste kraft', 'Dieci biglietti', 'Per i ringraziamenti'],
             'Dieci biglietti avorio con la busta kraft: per dire grazie a mano.'),
    articolo('CAR-015', 'Buste e cartoncini da invito, venti', 'buste-inviti', 'carta', 24, 0, 4.6, 88, False,
             [11650187, 11650092], ['Cartoncino da 300 grammi', 'Buste color sabbia', 'Venti inviti', 'Si stampano anche qui'],
             'Il cartoncino spesso degli inviti e le buste color sabbia: li scriviamo anche noi, a mano.'),
    articolo('SIG-016', 'Sigillo in ottone con l’iniziale', 'sigillo-iniziale', 'sigilli', 34, 0, 4.9, 176, True,
             [19216748, 36824942], ['La vostra iniziale', 'Ottone inciso', 'Manico in legno di noce', 'Due bastoncini di ceralacca'],
             'Il sigillo con l’iniziale, in ottone inciso e noce: la busta si chiude come una volta.'),
    articolo('SIG-017', 'Ceralacca rossa, sei bastoncini', 'ceralacca-rossa', 'sigilli', 12, 0, 4.7, 221, False,
             [36824941, 19216747], ['Sei bastoncini', 'Con lo stoppino', 'Rosso ceralacca', 'Per una trentina di buste'],
             'Sei bastoncini col loro stoppino: una trentina di buste chiuse di rosso.'),
    articolo('SIG-018', 'Timbri dell’alfabeto in legno', 'timbri-alfabeto', 'sigilli', 39, 32, 4.6, 74, False,
             [3838319, 3838334], ['Ventisei lettere e i numeri', 'Gomma su legno', 'Cassetta in legno', 'Tampone nero'],
             'L’alfabeto di gomma nella sua cassetta: per i pacchi, le etichette, i nomi sui quaderni.'),
    articolo('COL-019', 'Acquerelli in scatola, ventiquattro godet', 'acquerelli-ventiquattro', 'colori', 58, 46, 4.8, 167, True,
             [19196316, 7859265], ['Ventiquattro godet in pasticca', 'Scatola di latta', 'Un pennello a serbatoio',
                                   'Per i taccuini di viaggio'],
             'Gli acquerelli in pasticca nella scatola di latta: per dipingere la laguna sul taccuino.', video=V_LETTERING),
    articolo('COL-020', 'Matite colorate, trentasei', 'matite-trentasei', 'colori', 26, 0, 4.7, 245, False, [194098, 4519774],
             ['Trentasei colori', 'Mina morbida da 3,8 mm', 'Legno di cedro', 'Scatola di metallo'],
             'Trentasei colori a mina morbida, in legno di cedro: si temperano senza spezzarsi.'),
    articolo('COL-021', 'Pennelli per acquerello, quattro', 'pennelli-quattro', 'colori', 22, 0, 4.6, 93, False,
             [114123, 12336448], ['Pelo sintetico', 'Tondi 2, 6, 10 e un piatto', 'Manici corti', 'Astuccio di tela'],
             'Quattro pennelli in pelo sintetico che tengono l’acqua come la martora: tondi e un piatto.'),
]
RECENSIONI = [[5, 'La Laguna scrive benissimo, e il nome inciso è perfetto.'],
              [5, 'L’inchiostro blu vira davvero al grigio: bellissimo.'],
              [4, 'Ottimo, la spedizione ha preso un giorno in più.'],
              [5, 'Il pacchetto incartato a mano era già un regalo.'],
              [5, 'Il quaderno in pelle si apre piatto, la carta regge tutto.'],
              [4, 'Buono, avrei voluto più colori di ceralacca.'],
              [5, 'Il corso di calligrafia è stato il regalo più bello.'],
              [3, 'Bel prodotto, ma la punta fine per me graffia un po’.']]
NOMI = ['Marco D.', 'Giulia S.', 'Lorenzo P.', 'Francesca R.', 'Tommaso B.', 'Beatrice C.', 'Edoardo F.', 'Aurora M.',
        'Leonardo T.', 'Sofia G.', 'Mattia V.', 'Greta L.']
PROMESSE = [('truck', 'Spedizione gratuita da 40 €'), ('gift', 'Incarto a mano, gratis'),
            ('pen-tool', 'Il nome inciso sulle stilografiche'), ('rotate-ccw', 'Resi in 30 giorni')]
CONTATTI = [('map-pin', 'Pennino · calle della Mandola 3801, Venezia', P['calligrafia'] + '#dove'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'PENNINO', icona='pen-tool', ricerca='Cerca una stilografica, un inchiostro, un quaderno…',
                     parole='una stilografica blu\nl’inchiostro rosso\nun quaderno in pelle\nil sigillo con l’iniziale',
                     annuncio=('Il nome inciso gratis su ogni stilografica, fino a domenica.', 'Le offerte', P['offerte']),
                     logo_size=26)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'PENNINO', 'pen-tool', 'Stilografiche, inchiostri, quaderni cuciti a mano e carta da lettere '
                   'in calle della Mandola, fra Rialto e la Fenice: al piano di sopra i corsi di calligrafia.',
                   [('Il negozio', [('Stilografiche', N.cat_url('stilografiche')), ('Inchiostri', N.cat_url('inchiostri')),
                                    ('Quaderni e agende', N.cat_url('quaderni')), ('Offerte', P['offerte'])]),
                    ('Pennino', [('I corsi di calligrafia', P['calligrafia']), ('Scegliere il pennino', P['pennini']),
                                 ('I preferiti', P['preferiti']), ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni', P['assistenza'] + '#spedizioni'), ('L’incisione', P['pennini'] + '#incisione'),
                               ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Pennino s.n.c. · Venezia · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera di Pennino', 'Una volta al mese, scritta a mano.',
                              'Gli inchiostri nuovi, le date dei corsi, una pagina di calligrafia da ricopiare.',
                              'Fatto: la prima lettera arriva col campione dell’inchiostro blu.'),
                   social=[('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.5  # la larghezza media di una lettera dei titoli (Bodoni Moda, per la vetrina)


def vetrina(t):
    # largo: la larghezza media delle lettere di ogni titolo, misurata col carattere vero (spaziatura compresa)
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-8, bollino_fondo=CERALACCA, bollino_testo=C['chiaro'])
    return N.vetrina(t, [
        N.offerta('pe-o1', K.sfondo_video(V_PENNINO, overlay=0.12), 'Le stilografiche · fino a domenica',
                  'La Laguna a 69 €, col nome inciso.', 'Corpo blu notte, pennino d’acciaio, il vostro nome sul cappuccio. Era 89 €.',
                  'Le stilografiche', N.cat_url('stilografiche'), bollino='−22%', nota='Spedizione gratuita da 40 €', primo=True, **dict(f, largo=0.451)),
        N.offerta('pe-o2', K.sfondo_foto('pexels-36824941.jpg', overlay=0.12, scala=1.06), 'Carta e ceralacca',
                  'Il kit della lettera a 54 €.', 'Carta vergata, venti buste, l’inchiostro blu e il sigillo con l’iniziale. '
                  'Presi uno per uno, 72 €.', 'Carta e buste', N.cat_url('carta'), bollino='−25%',
                  nota='Incartato a mano, gratis', **dict(f, largo=0.393)),
        N.offerta('pe-o3', K.sfondo_video(V_LETTERING, overlay=0.12), 'Novità · gli acquerelli',
                  'Ventiquattro godet in scatola di latta.', 'Gli acquerelli in pasticca e il pennello a serbatoio: per '
                  'dipingere sul taccuino di viaggio. Ora a 46 €.', 'Matite e acquerelli', N.cat_url('colori'), bollino='Nuovi',
                  nota='Si provano in negozio', **dict(f, largo=0.424)),
    ], transizione='flipH')


# le schede sotto la vetrina: «reparti» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='reparti', reparti='I reparti', voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Stilografiche', {'category': 'stilografiche', 'orderby': 'popularity'}, 'Vedi tutte', N.cat_url('stilografiche')),
        ('Sotto i 20 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=20'),
        ('I più regalati', {'orderby': 'popularity'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Il negozio', 'Dalla penna alla busta',
                              ('La Laguna,', '69 €.', 'Col nome inciso, fino a domenica.', 'Stilografiche',
                               N.cat_url('stilografiche'), 'pexels-5264514.jpg'), tutto='Tutto il negozio')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte della settimana', 'Fino a domenica.',
                              'Le offerte della settimana finiscono tra:',
                              ('Il kit della lettera,', '54 €.', 'Carta, buste, inchiostro e sigillo. Presi uno per uno, 72 €.',
                               'Carta e buste', N.cat_url('carta'), 'pexels-36824941.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Arrivati questo mese',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('Gli acquerelli', 'in scatola di latta.', 'Ventiquattro godet, 46 €.', 'Matite e acquerelli',
                              N.cat_url('colori'), 'pexels-19196316.jpg'), tutte='Tutto il negozio')


# ── La lettera: lo slider resta fermo, il pennino scrive riga per riga, il foglio scende nella busta, il sigillo ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1120.0
CX, CY, PW, PH = 1030.0, 405.0, 500.0, 650.0        # il foglio sul computer: centro e misure
CXM, CYM, KM = 320.0, 590.0, 0.86                   # sul telefono: il centro e la scala delle misure
RIGHE = ['Cara Giulia,', 'ti scrivo da Venezia:', 'qui piove sui canali', 'e l’inchiostro è blu,', 'quello che piaceva a te.',
         'Torna presto.']
FIRMA = 'Marco'
SM = 1440.0 / 640.0   # sul telefono gli spostamenti valgono 640/1440 di quelli scritti: questo li riporta alle misure vere


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def _due(dx, dy, w, h):
    """Un riquadro dato dal centro rispetto al centro del foglio (in px del computer): la posizione sul computer e quella
    sul telefono, in percentuali delle tele."""
    a = _pc(CX + dx - w / 2, CY + dy - h / 2, w, h, W, H)
    b = _pc(CXM + dx * KM - w * KM / 2, CYM + dy * KM - h * KM / 2, w * KM, h * KM, WM, HM)
    return a, b


def lettera(t):
    m = K.mob
    L = [
        K.livello('le-o', contenuto='La lettera · scorrete', x=6, y=12, w=40, size=13, peso='600', colore=mix(CERALACCA, 55, C['chiaro']),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.4, 'width': 86}}),
        K.livello('le-t', contenuto='Si scrive ancora<br><em>a mano.</em>', tag='h2', x=6, y=17, w=40, size=64, peso='500',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(32), 'x': 7, 'y': 4.6, 'width': 86}}),
        K.livello('le-s', contenuto='Carta di cotone, l’inchiostro blu Laguna, la ceralacca rossa: in negozio c’è tutto quello che '
                                    'serve per una lettera vera. Scorrete: si scrive da sola.', x=6, y=43, w=32, size=17,
                  colore=mix(C['chiaro'], 82), lineHeight=1.6, entra='fadeIn', ritardo=400,
                  responsive={'mobile': {'visible': False}}),
    ]
    # la busta aperta dietro al foglio: il lembo è un quadrato girato di 45° col centro sul bordo della busta; la metà di
    # sotto sta dietro al corpo, quella di sopra è il lembo aperto
    entra_busta = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'r': 45}, {'p': 50, 'o': 0, 'r': 45},
                                                       {'p': 58, 'o': 100, 'r': 45}, {'p': 72, 'o': 100, 'r': 45},
                                                       {'p': 78, 'o': 0, 'r': 45}, {'p': 100, 'o': 0, 'r': 45}]}
    lato = 440 / 2 ** 0.5
    a, b = _due(0, 0, lato, lato)
    L.append(K.livello('le-lembo', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#B88C5A', borderRadius=6, entra='fadeIn',
                       ritardo=0, scrollMove=entra_busta, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # il foglio: si rimpicciolisce e scende nella busta, poi sparisce dentro
    nel_foglio = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 1, 'o': 100, 'r': -1.5}, {'p': 50, 's': 1, 'o': 100, 'r': -1.5},
                                                      {'p': 62, 's': 0.5, 'o': 100, 'r': 0}, {'p': 68, 's': 0.5, 'o': 100, 'r': 0},
                                                      {'p': 72, 's': 0.46, 'o': 0, 'r': 0}, {'p': 100, 's': 0.46, 'o': 0, 'r': 0}]}
    a, b = _due(0, 0, PW, PH)
    L.append(K.livello('le-foglio', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#FFFDF6', borderRadius=3,
                       boxShadow={'x': 0, 'y': 24, 'blur': 50, 'spread': -16, 'color': mix(C['nero'], 80)}, entra='fadeIn',
                       ritardo=200, scrollMove=nel_foglio,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # le righe: si scrivono una alla volta, poi seguono il foglio (verso il suo centro, rimpicciolite) e spariscono con lui.
    # Sul computer e sul telefono le distanze dal centro sono diverse: due livelli per riga, ognuno sulla sua tela.
    tutte = RIGHE + [FIRMA]
    tratti = [(6 + 6 * k, 11 + 6 * k) for k in range(len(tutte))]
    LR, X0 = 400.0, -200.0
    for k, testo in enumerate(tutte):
        firma = k == len(tutte) - 1
        dy = -250 + 66 * k + (34 if firma else 0)
        a0, a1 = tratti[k]
        for dove in ('d', 'm'):
            fatt = 1.0 if dove == 'd' else KM * SM
            cy = dy + 22
            mov = {'inizio': 0, 'fine': 100, 'tappe': [
                {'p': 0, 'o': 0, 'x': -12, 'y': 0, 's': 1}, {'p': a0, 'o': 0, 'x': -12, 'y': 0, 's': 1},
                {'p': a1, 'o': 100, 'x': 0, 'y': 0, 's': 1}, {'p': 50, 'o': 100, 'x': 0, 'y': 0, 's': 1},
                {'p': 62, 'o': 100, 'x': 0, 'y': round(-cy * 0.5 * fatt, 1), 's': 0.5},
                {'p': 68, 'o': 100, 'x': 0, 'y': round(-cy * 0.5 * fatt, 1), 's': 0.5},
                {'p': 72, 'o': 0, 'x': 0, 'y': round(-cy * 0.54 * fatt, 1), 's': 0.46},
                {'p': 100, 'o': 0, 'x': 0, 'y': round(-cy * 0.54 * fatt, 1), 's': 0.46}]}
            a, b = _due(0, cy, LR, 44)
            stile = dict(contenuto=testo, w=a[2], size=40 if not firma else 46, peso='600', colore=C['acc'], fontFamily=MANO,
                         lineHeight=1.1, align='right' if firma else 'left', entra='fadeIn', ritardo=0, scrollMove=mov)
            if dove == 'd':
                L.append(K.livello('le-r%d' % k, x=a[0], y=a[1], responsive={'mobile': {'visible': False}}, **stile))
            else:
                # sul computer fuori dalla tela, a destra; sul telefono al suo posto
                L.append(K.livello('le-rm%d' % k, x=104, y=a[1], responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2],
                                                                                         'fontSize': round(stile['size'] * KM, 1)}},
                                   **stile))
    # il pennino: corre lungo la riga che si scrive, torna a capo, poi si alza e sparisce
    lungh = [min(LR - 20, len(s) * 40 * 0.42) for s in tutte]
    for dove in ('d', 'm'):
        fatt = 1.0 if dove == 'd' else KM * SM
        tappe = [{'p': 0, 'x': 0, 'y': 0, 'o': 0, 'r': 0}, {'p': 4, 'x': 0, 'y': 0, 'o': 100, 'r': 0}]
        for k, (a0, a1) in enumerate(tratti):
            firma = k == len(tutte) - 1
            x_ini = (LR - lungh[k]) if firma else 0
            y_k = (66 * k + (34 if firma else 0)) * fatt
            tappe.append({'p': a0, 'x': round(x_ini * fatt, 1), 'y': round(y_k, 1), 'o': 100, 'r': 0})
            tappe.append({'p': a1, 'x': round((x_ini + lungh[k]) * fatt, 1), 'y': round(y_k, 1), 'o': 100, 'r': 0})
        ultimo = tappe[-1]
        tappe += [{'p': 50, 'x': ultimo['x'], 'y': ultimo['y'], 'o': 100, 'r': 0},
                  {'p': 54, 'x': round(ultimo['x'] + 60 * fatt, 1), 'y': round(ultimo['y'] - 60 * fatt, 1), 'o': 0, 'r': 20},
                  {'p': 100, 'x': round(ultimo['x'] + 60 * fatt, 1), 'y': round(ultimo['y'] - 60 * fatt, 1), 'o': 0, 'r': 20}]
        a, b = _due(X0 + 34, -250 + 30 + 34, 68, 68)
        # il pennino dorato col contorno scuro (pieno sarebbe una macchia sul foglio)
        stile = dict(tipo='icon', iconName='pen-tool', w=a[2], h=a[3], size=68, colore=C['nero'], iconFillColor='#D9B54A',
                     iconStrokeColor=C['nero'], iconStrokeWidth=1.4, entra='fadeIn', ritardo=0,
                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe})
        if dove == 'd':
            L.append(K.livello('le-penna', x=a[0], y=a[1], responsive={'mobile': {'visible': False}}, **stile))
        else:
            L.append(K.livello('le-pennam', x=104, y=a[1], responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2],
                                                                                    'height': b[3], 'fontSize': 58}}, **stile))
    # il corpo della busta, davanti al foglio: il foglio ci scende dietro
    corpo = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 40}, {'p': 50, 'o': 0, 'y': 40}, {'p': 58, 'o': 100, 'y': 0},
                                                 {'p': 100, 'o': 100, 'y': 0}]}
    a, b = _due(0, 117.5, 440, 235)
    L.append(K.livello('le-busta', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#D3AC7C', borderRadius=6, entra='fadeIn',
                       ritardo=0, boxShadow={'x': 0, 'y': 18, 'blur': 40, 'spread': -14, 'color': mix(C['nero'], 70)},
                       scrollMove=corpo, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # la busta chiusa: le due pieghe del lembo a V sul corpo
    import math
    ang = math.degrees(math.atan2(130, 220))
    lung = (220 ** 2 + 130 ** 2) ** 0.5
    for k, (sx, rr) in enumerate([(-110, ang), (110, -ang)]):
        chiude = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'r': rr}, {'p': 74, 'o': 0, 'r': rr},
                                                      {'p': 80, 'o': 100, 'r': rr}, {'p': 100, 'o': 100, 'r': rr}]}
        a, b = _due(sx, 65, lung, 3)
        L.append(K.livello('le-piega%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#9C7448', borderRadius=2,
                           entra='fadeIn', ritardo=0, scrollMove=chiude,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': max(0.3, b[3])}}))
    # il sigillo di ceralacca: cade dall'alto, grande e girato, e si posa sulla punta del lembo
    sigillo = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 2.4, 'r': -40}, {'p': 80, 'o': 0, 's': 2.4, 'r': -40},
                                                   {'p': 87, 'o': 100, 's': 1, 'r': 0}, {'p': 100, 'o': 100, 's': 1, 'r': 0}]}
    a, b = _due(0, 130, 96, 96)
    L.append(K.livello('le-sigillo', contenuto='P', x=a[0], y=a[1], w=a[2], h=a[3], size=50, peso='600', colore=C['chiaro'],
                       bgColor=CERALACCA, borderRadius=999, align='center', textValign='center', lineHeight=1, fontFamily=TITOLI,
                       boxShadow={'x': 0, 'y': 6, 'blur': 0, 'spread': 0, 'color': mix(CERALACCA, 70, C['nero'])},
                       entra='fadeIn', ritardo=0, scrollMove=sigillo,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3], 'fontSize': 43}}))
    # alla fine: il kit e il pulsante
    finale = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 16}, {'p': 86, 'o': 0, 'y': 16}, {'p': 93, 'o': 100, 'y': 0},
                                                  {'p': 100, 'o': 100, 'y': 0}]}
    L.append(K.livello('le-kit', contenuto='Il kit della lettera: carta vergata, venti buste, l’inchiostro blu e il sigillo con '
                                           'l’iniziale · <strong>54&nbsp;€</strong>', x=6, y=66, w=32, size=17,
                       colore=C['chiaro'], lineHeight=1.55, entra='fadeIn', ritardo=0, scrollMove=finale,
                       responsive={'mobile': {'x': 7, 'y': 85.5, 'width': 86, 'fontSize': m(13)}}))
    L.append(K.livello('le-b', tipo='button', contenuto='Il kit della lettera', x=6, y=79, w='auto', size=15, peso='700',
                       colore=C['nero'], bgColor=C['chiaro'], padding=14, borderRadius=4, entra='fadeIn', ritardo=0,
                       buttonUrl=N.cat_url('carta'), hover={'bgColor': C['sec'], 'color': C['chiaro'], 'duration': 200},
                       scrollMove=finale, responsive={'mobile': {'x': 7, 'y': 92, 'fontSize': m(13)}}))
    sl = [K.diapositiva('le-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, L, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2200, scrollStep=False, globalBackground={'type': 'color', 'color': C['nero']},
                       heightMobile={'mode': 'px', 'value': HM})


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small',
                         style={'margin_top': 24}))
    out.append(t.section([
        N.testata_sezione(t, 'Il negozio', 'Cosa scrivete oggi?', P['negozio'], 'Tutto il negozio'),
        t.one([N.categorie(t, colonne=6, raggio_foto=4, ratio='4-5')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, lettera(t)))
    out.append(t.section([
        N.testata_sezione(t, 'I più regalati', 'Quelli che si incartano di più', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Il quaderno in pelle,', 'cucito a mano.', 'Pelle al vegetale, carta da 120 grammi: 45 €.',
                               'Quaderni e agende', N.cat_url('quaderni'), K.photo_bg('pexels-14436269.jpg'), size=34)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Il sigillo,', 'con la vostra iniziale.', 'Ottone inciso e noce, con la ceralacca: 34 €.',
                               'Sigilli e ceralacca', N.cat_url('sigilli'), K.photo_bg('pexels-19216748.jpg'), size=34)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte della settimana', color=mix(CERALACCA, 55, C['chiaro'])),
                      N.titolo(t, 'Fino a domenica.', size=44, color=C['chiaro']),
                      N.testo(t, 'La Laguna col nome inciso, il kit della lettera, gli acquerelli, l’inchiostro rosso.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left', pad_top=16)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_INTINGE, 'Al banco', 'Ogni stilografica <em>si prova prima.</em>',
                             'Sul blocco di carta vergata, con l’inchiostro che volete: fine, media o larga, finché la mano non '
                             'dice quale.', overlay=40, extra=[N.pulsante(t, 'Scegliere il pennino', P['pennini'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Stilografiche', 'Dalla prima all’ebanite', N.cat_url('stilografiche')),
        t.one([N.prodotti(t, category='stilografiche', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-3826691.jpg', 'Una mano scrive in corsivo col pennino su un quaderno', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Al piano di sopra'), N.titolo(t, 'I corsi<br><em>di calligrafia.</em>', size=52),
                      N.testo(t, 'Il sabato mattina, sei persone al tavolo grande: il corsivo inglese, il cancelleresco, le '
                                 'buste degli inviti. La cannuccia, i pennini e la carta li diamo noi.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Quattro sabati, tre ore ciascuno', 'Sei persone al massimo', 'Il materiale è compreso']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'I corsi', P['calligrafia'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il negozio', 'Dalla penna alla busta', 'Stilografiche e inchiostri, quaderni e agende, carta e '
                            'buste, sigilli e ceralacca, matite e acquerelli: incartiamo tutto a mano.', colonne=3,
                            attributi='punta',
                            banner=N.banner(t, 'Il kit della lettera,', '54 €.', 'Carta, buste, inchiostro e sigillo.',
                                            'Carta e buste', N.cat_url('carta'), K.photo_bg('pexels-36824941.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Tre taccuini,', '15 €.', 'A righe, a quadretti e bianco. Erano 18 €.', 'Quaderni e agende',
                        N.cat_url('quaderni'), K.photo_bg('pexels-7718812.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Gli acquerelli,', '46 €.', 'Ventiquattro godet in scatola di latta. Erano 58 €.', 'Matite e acquerelli',
                        N.cat_url('colori'), K.photo_bg('pexels-7859265.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Le offerte della settimana', 'Fino a domenica', 'La Laguna col nome inciso, il kit della lettera, '
                            'gli acquerelli, i taccuini e l’inchiostro rosso a meno, finché c’è.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('gift', 'Incarto a mano, gratis'), ('truck', 'Gratis da 40 €'),
                                                   ('pen-tool', 'Il nome inciso, gratis'), ('rotate-ccw', 'Resi in 30 giorni')],
                             bundle='Si regala insieme', miniature='left', altezza='600px', bottone='Aggiungi al carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 40 €. Il pacchetto lo incartiamo a mano.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'L’indirizzo, la spedizione, il pagamento. Nelle note: il nome da incidere, il '
                                            'biglietto da scrivere.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, gli indirizzi, i corsi prenotati, gli inchiostri da ricomprare.',
                            ('Dov’è il mio pacchetto?', 'Scriveteci col numero dell’ordine: vi diciamo dov’è.', 'Scrivici',
                             P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto del negozio.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'Al telefono e in negozio dalle 10 alle 19, dal martedì al sabato.', [
        ('truck', 'Spedizioni', 'In 24 ore, gratis sopra i 40 €.', 'spedizioni'),
        ('gift', 'Incarto', 'A mano, con la carta e lo spago.', 'spedizioni'),
        ('pen-tool', 'Incisione', 'Il nome sulle stilografiche, gratis.', 'scrivici'),
        ('rotate-ccw', 'Resi', 'In 30 giorni, se non incisi.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 40 €; sotto, 5 €. Parte il giorno dopo l’ordine, incartata a mano.'),
        ('Posso rendere una stilografica incisa?', 'No: col nome inciso è solo vostra. Senza incisione si rende in 30 giorni.'),
        ('Che punta scelgo?', 'La media va bene a quasi tutti; la fine per chi scrive piccolo. Nella guida c’è la prova.'),
        ('Scrivete voi i biglietti?', 'Sì: scrivete il testo nelle note dell’ordine, lo scriviamo a mano col pennino.')],
        CONTATTI, campi_extra='Un’incisione\nUn corso di calligrafia\n',
        foto=('pexels-30109289.jpg', 'Il negozio, con gli scaffali della carta'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa pagina <em>è una macchia.</em>', 'La pagina non c’è più: cercate nel negozio '
                        'qui sopra.', K.video_bg(V_NUVOLA, overlay=40))


def build_calligrafia():
    t = Tpl(ID, 'calligrafia')
    out = [K.intro(t, 'I corsi di calligrafia', ['Il sabato mattina,', 'al piano di sopra.'], 'Sei persone al tavolo '
                   'grande, la luce del canale, la cannuccia in mano: in quattro sabati si scrive una busta da invito.',
                   bg=K.video_bg(V_BIGLIETTO, overlay=40), size=86)]
    corsi = [('pen-tool', 'Il corsivo inglese', 'Quattro sabati, 10–13. Il più chiesto: la grafia elegante delle lettere.'),
             ('feather', 'Il cancelleresco', 'Quattro sabati, 10–13. Il corsivo italiano del Cinquecento, col pennino tronco.'),
             ('mail', 'Buste e inviti', 'Un sabato, 10–13. Per scrivere da sé gli inviti delle nozze.'),
             ('palette', 'Acquerello sul taccuino', 'Due sabati, 10–13. La laguna in dieci minuti, coi godet in scatola.')]
    out.append(t.section([
        N.testata_sezione(t, 'I corsi', 'Quattro, da ottobre a maggio'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in corsi],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 56}))
    out.append(t.section([
        t.one([K.numeri(t, [('6', 'persone al massimo'), ('3 ore', 'ogni sabato'), ('95 €', 'il corso di quattro sabati'),
                            ('0 €', 'il materiale, compreso')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    out.append(K.video_frase(t, V_CARTELLINO, 'Il materiale', 'La cannuccia, i pennini, <em>la carta: li diamo noi.</em>',
                             'Alla fine del corso la cannuccia resta vostra, coi sei pennini nella scatola di latta.', overlay=40))
    modulo = t.classic('form', {
        'fields': [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True, segnaposto='nome@esempio.it'),
                   K.campo('select', 'Il corso', 'corso', True, opzioni='Il corsivo inglese\nIl cancelleresco\nBuste e inviti\n'
                                                                      'Acquerello sul taccuino'),
                   K.campo('date', 'Il primo sabato', 'giorno', True),
                   K.campo('textarea', 'Qualcosa da dirci', 'note', False, '1-1', segnaposto='Se siete mancini, se è un regalo')],
        'email_subject': 'Pennino — un posto al corso', 'submit_text': 'Prenota il posto', 'submit_icon': '',
        'success_message': 'Grazie: vi scriviamo entro domani per confermare il posto.', 'submit_full_width': False,
        'submit_alignment': 'left', 'label_color': C['testo'], 'label_size': '13', 'label_weight': '500',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': C['chiaro'], 'input_color': C['testo'],
        'input_border_color': C['filo'], 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 4,
        'input_focus_border': C['acc'], 'input_focus_shadow': False, 'gap': '14', 'submit_bg': C['acc'],
        'submit_color': C['on_acc'], 'submit_radius': 4, 'submit_padding_x': '28', 'submit_padding_y': '15',
        'submit_font_size': '14', 'submit_font_weight': '600', 'submit_text_transform': 'none',
        'submit_letter_spacing': '0', 'submit_hover_bg': C['nero'], 'privacy_checkbox': True},
        style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
               'border_radius': 0})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Un posto al tavolo'), N.titolo(t, 'Prenotate<br><em>il sabato.</em>', size=46),
                      N.testo(t, 'Scegliete il corso e il primo sabato: vi scriviamo per confermare. Si paga il primo giorno, '
                                 'in negozio.', size=17),
                      K.image(t, 'pexels-3826654.jpg', 'Mani che scrivono su una busta kraft col pennino', ratio='4/3')],
                     wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start'),
    ], html_id='prenota', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 88, 'padding_bottom': 88}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Dove'), N.titolo(t, 'In calle della Mandola.', size=40),
                      N.testo(t, 'Fra campo Manin e la Fenice, a cinque minuti da Rialto: la porta verde accanto alla vetrina, '
                                 'su per la scala di legno.', size=17),
                      K.faq(t, [('Serve saper scrivere bene?', 'No: si comincia dalle aste e dagli ovali, come a scuola.'),
                                ('E se sono mancino?', 'Ditecelo nella prenotazione: il pennino giusto lo abbiamo.'),
                                ('Si può regalare?', 'Sì: c’è il biglietto del corso, nel negozio sotto «Carta e buste».')],
                            'cal')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-4348298.jpg', 'Il tavolo grande dei corsi, con la luce della finestra', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='dove', padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_pennini():
    t = Tpl(ID, 'pennini')
    out = [N.intestazione(t, 'Scegliere il pennino', 'Fine, media o larga?', 'La punta decide la riga: più è larga, più il tratto '
                          'è pieno e l’inchiostro si vede. Ecco come scegliere, e come si prova.')]
    out.append(t.section([
        t.one([K.numeri(t, [('F', 'fine · 0,5 mm, per chi scrive piccolo'), ('M', 'media · 0,7 mm, la più scelta'),
                            ('B', 'larga · 0,9 mm, per firmare'), ('14 k', 'l’oro, che si adatta alla mano')], colonne=4,
                           size=56)]),
    ], padding='default', width='large', style={'padding_top': 24, 'padding_bottom': 48}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-33868622.jpg', 'Stilografiche e pennini da calligrafia in fila sul tavolo', ratio='4/3')],
                     wm='1-2', ws='1-1'),
               t.col([N.occhiello(t, 'La prova'), N.titolo(t, 'Scrivete<br><em>il vostro nome.</em>', size=46),
                      N.testo(t, 'In negozio si prova sul blocco: il nome, una riga di «m», un otto. Se la punta graffia è troppo '
                                 'fine per voi; se l’inchiostro si allarga, troppo larga. Online, scegliete la media: si cambia '
                                 'gratis entro 30 giorni, se non incisa.', size=17),
                      N.pulsante(t, 'Le stilografiche', N.cat_url('stilografiche'), pad_top=20)], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 56, 'padding_bottom': 72}))
    out.append(K.video_frase(t, V_QUADERNO, 'L’acciaio e l’oro', 'L’oro <em>impara la vostra mano.</em>',
                             'Il pennino d’acciaio resta com’è; quello d’oro, dopo una settimana, si piega appena al vostro modo '
                             'di scrivere.', overlay=40))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'L’incisione'), N.titolo(t, 'Il nome sul cappuccio.', size=44),
                      N.testo(t, 'Incidiamo il nome, le iniziali o una data sul cappuccio, gratis su ogni stilografica: '
                                 'scrivetelo nelle note dell’ordine, in stampatello. Parte il giorno dopo.', size=17),
                      K.faq(t, [('Quanti caratteri?', 'Fino a venti, spazi compresi.'),
                                ('Che carattere usate?', 'Un corsivo inglese, o lo stampatello se lo preferite.'),
                                ('Si può rendere?', 'Incisa no: è solo vostra.')], 'inc')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-15877831.jpg', 'Una stilografica nera sul poggiapenne d’ottone', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='incisione', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        N.testata_sezione(t, 'Da provare', 'Le stilografiche', N.cat_url('stilografiche')),
        t.one([N.prodotti(t, category='stilografiche', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'La lettera',
    ['cartoleria', 'stilografiche', 'calligrafia', 'carta', 'woocommerce', 'e-commerce', 'venezia', 'regali'],
    'Il negozio online di una cartoleria con le stilografiche a Venezia: in apertura la fascia delle offerte che gira come un '
    'biglietto e le quattro schede coi prodotti, il mega menu col negozio, le offerte e le novità; a metà pagina la lettera '
    'che si scrive col pennino mentre si scorre, scende nella busta e si chiude col sigillo di ceralacca; i corsi di calligrafia '
    'con la prenotazione, la guida per scegliere il pennino, la scheda con la punta a scelta e il video del pennino.',
    [('Il negozio', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Stilografiche', '{{PAGINA:negozio}}?product_cat=stilografiche'), ('Carta', '{{PAGINA:negozio}}?product_cat=carta'),
     ('Sigilli', '{{PAGINA:negozio}}?product_cat=sigilli'), ('I corsi', P['calligrafia']), ('Il pennino', P['pennini'])],
    [('home', 'Home', None), ('negozio', 'Il negozio', 'negozio'), ('calligrafia', 'I corsi di calligrafia', 'corsi'),
     ('pennini', 'Scegliere il pennino', 'pennino'), ('offerte', 'Le offerte della settimana', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Karla\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bodoni Moda\', \'Didot\', Georgia, serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Bodoni Moda', 'Karla', 'IBM Plex Mono', 'Dancing Script'],
    corsivo_di=['Bodoni Moda'], peso_titoli='500', raggio=4, grana=0, titoli_line='1.04', titoli_spazio='-0.01em')
# Dancing Script ha i pesi dal 400 al 700: senza il 300 Google rifiuterebbe la richiesta di quella famiglia
THEME['styles']['google_fonts_weights'] = '400;500;600;700'
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Il negozio': ('pannello-reparti', 'Pannello del negozio'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['preview_v'] = THEME['preview_mobile_v'] = '4'  # anteprime con le testate nuove (2 nov 2026)
THEME['cursor']['style'] = 'pennino'
THEME['cursor']['ring_color'] = '#B8902B'           # il metallo del pennino
THEME['cursor']['dot_color'] = HEX['primary']       # l'inchiostro blu


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'calligrafia.json': build_calligrafia(), 'pennini.json': build_pennini(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'punta': {'nome': 'Punta', 'termini': PUNTE}},
                    dentro='Il pacchetto incartato a mano con la carta blu e lo spago, il biglietto se l’avete chiesto.')


if __name__ == '__main__':
    main()
