# -*- coding: utf-8 -*-
"""Genera il tema «Tracolla» (id tracolla): una bottega di borse nel centro di Bologna, in via Clavature, col laboratorio
dietro il negozio — borse a mano, tracolle, zaini, pochette, piccola pelletteria e borse da viaggio, cucite a mano in
pelle conciata al vegetale.

Tema chiaro e caldo: il cuoio cognac dei pulsanti, l'ottone delle fibbie, il rosso dei prezzi scontati, la carta color
crema. Titoli in DM Serif Display (col corsivo), testo in Outfit, prezzi e orari in DM Mono. Angoli morbidi.

Apertura: la vetrina delle offerte, come nei negozi grandi: una fascia bassa che scorre da sola (la borsa Cognac, le
pochette per le feste, il borsone weekend), sotto le quattro schede coi prodotti veri; il mega menu apre i pannelli delle
borse per forma, delle offerte e delle novità. A metà pagina «la giornata in borse»: lo slider resta fermo mentre si
scorre, la lancetta dell'orologio gira dalle 8 alle 21 e per ogni ora entra la sua borsa col prezzo. Cursore: la fibbia.

WooCommerce: il catalogo coi filtri, la scheda con la galleria (il video della borsa) e i colori, il carrello, la cassa a
passi, l'account, i preferiti; il laboratorio e le misure (cosa ci sta dentro ogni borsa).
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'tracolla'
NAME = 'Tracolla'

HEX = {
    'primary': '#8A3B1F', 'primary_contrast': '#FFFFFF', 'secondary': '#D9A441', 'secondary_contrast': '#2A1A0F',
    'accent': '#B8321F', 'dark': '#1F1510', 'light': '#FFFDF9', 'muted': '#F2E8DA', 'muted_contrast': '#2B1D15',
    'text': '#2B1D15', 'text_muted': '#6E5B4C', 'background': '#FAF5EE', 'border': '#E5D6C3', 'link': '#8A3B1F',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'laboratorio', 'misure', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'bottega@tracolla.example', '+39 051 000 4170', 'tel:+390510004170'

V_TOTE, V_TRENCH, V_TELEFONO, V_PELLE, V_CUCITURA, V_ZIP, V_BOTTEGA, V_SCAFFALI, V_PAGLIA, V_CAMICIA = (
    'pexels-video-7570173', 'pexels-video-7820810', 'pexels-video-7681888', 'pexels-video-34740247', 'pexels-video-34740037',
    'pexels-video-34740373', 'pexels-video-6649983', 'pexels-video-8195533', 'pexels-video-8195096', 'pexels-video-12271185')
VIDEOS = [V_TOTE, V_TRENCH, V_TELEFONO, V_PELLE, V_CUCITURA, V_ZIP, V_BOTTEGA, V_SCAFFALI, V_PAGLIA, V_CAMICIA]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    10919291, 13525167, 4499757, 29359841, 21897313, 21897124, 12061812, 8801100, 20399717, 30574508, 6648416, 12572107,
    5357600, 35270330, 12945022, 2453823, 164697, 36958694, 10820410, 15432831, 20591024, 6153891, 15432797, 36674977,
    33198066, 5243552, 32954513, 6651193, 11676702, 28028262, 15763948, 1058959, 6650001, 7821312, 29068686, 7821329, 8801075,
    10329714, 21837372, 21837375, 21837385, 19270121, 32599546, 6653235, 6660912, 33428433, 33428417, 6653993, 40038347,
    14806252, 5717978, 5717972, 9852955, 8217756, 8801061, 7735762, 39169004, 17214199, 38350333, 17363018, 17363016)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='400', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
OTTONE, ROSSO = C['sec'], C['acc2']
TITOLI, MONO = 'DM Serif Display', 'DM Mono'
N = Negozio(K, NAME, P, raggio=10, ratio='1-1', card='none', hover='swap', testata_scura=False,
            consegna='Spedizione gratuita da 99 €', evidenza='Cucita in bottega', marca='', peso_titoli='400')

CATEGORIE = [
    ('borse-a-mano', 'Borse a mano', 'Tote, bauletti e secchielli: per il lavoro e per la sera.', 'pexels-10919291.jpg'),
    ('tracolle', 'Tracolle', 'A spalla o di traverso, piccole e mini.', 'pexels-12061812.jpg'),
    ('zaini', 'Zaini', 'In cuoio, in tela, a uncinetto.', 'pexels-36958694.jpg'),
    ('pochette', 'Pochette', 'Per le feste: catene, strass, trapuntate.', 'pexels-20591024.jpg'),
    ('piccola-pelletteria', 'Piccola pelletteria', 'Portafogli e portacarte cuciti a mano.', 'pexels-32954513.jpg'),
    ('viaggio', 'Viaggio', 'Borsoni, valigette e borse per il fine settimana.', 'pexels-1058959.jpg'),
]
COLORI = ['Cognac', 'Nero', 'Grigio fumo', 'Cuoio naturale']
CN, NE, GR, NA = COLORI
PRODOTTI = [
    prodotto('TRA-001', 'Borsa a mano Cognac, conciata al vegetale', 'borsa-cognac', 'borse-a-mano', 289, 229, 14, 4.9, 412, True,
             ['pexels-10919291.jpg', 'pexels-13525167.jpg', 'pexels-14806252.jpg'],
             ['Pelle conciata al vegetale in Toscana', 'Cucita a mano in bottega', 'Tasca interna con la zip',
              'Il video della borsa nella galleria'],
             'La borsa che si porta tutti i giorni: due manici, la tracolla staccabile, la pelle che col tempo si scurisce.',
             varianti=('colore', [CN, NE, GR], [GR]), video=V_TOTE, cross=['TRA-016', 'TRA-017']),
    prodotto('TRA-002', 'Tote Biblioteca, porta il computer da 14″', 'tote-biblioteca', 'borse-a-mano', 239, 0, 20, 4.8, 268, True,
             ['pexels-29359841.jpg', 'pexels-21897124.jpg'],
             ['Ci sta un computer da 14″', 'Fondo rigido, sta in piedi', 'Due tasche interne', 'Chiusura a calamita'],
             'La tote del lavoro: il computer, il quaderno, la borraccia e un maglione, e sta in piedi sotto la scrivania.',
             varianti=('colore', [CN, NE], []), cross=['TRA-016']),
    prodotto('TRA-003', 'Borsa Polaroid, cuoio tamponato', 'borsa-polaroid', 'borse-a-mano', 195, 159, 9, 4.7, 154, False,
             ['pexels-4499757.jpg'], ['Cuoio tamponato a mano', 'Due manici e la tracolla', 'Fodera in cotone', 'Fatta in 40 pezzi'],
             'Il cuoio tamponato a mano a strati, come una fotografia sbiadita: ognuna ha il suo colore.'),
    prodotto('TRA-004', 'Secchiello Sabbia', 'secchiello-sabbia', 'borse-a-mano', 179, 0, 16, 4.6, 98, False,
             ['pexels-12572107.jpg', 'pexels-12945022.jpg'], ['Il manico ad anello in legno', 'Coulisse in cuoio',
                                                               'Fodera in lino', 'Pesa 480 g'],
             'Il secchiello col manico ad anello: si chiude con la coulisse, si porta a mano o al gomito.'),
    prodotto('TRA-005', 'Bauletto Notte', 'bauletto-notte', 'borse-a-mano', 219, 175, 6, 4.8, 131, True,
             ['pexels-35270330.jpg', 'pexels-21837372.jpg'], ['Pelle liscia color tabacco', 'Doppia zip in ottone',
                                                               'Piedini sul fondo', 'Tracolla regolabile'],
             'Il bauletto per la sera in città: rigido, con la zip doppia e i piedini, non si siede mai sul tavolino.'),
    prodotto('TRA-006', 'Tracolla Onda', 'tracolla-onda', 'tracolle', 149, 0, 22, 4.7, 187, False,
             ['pexels-12061812.jpg', 'pexels-20399717.jpg'], ['Patta con la fibbia in ottone', 'Tracolla regolabile',
                                                               'Ci sta un telefono grande', 'Tre colori'],
             'La tracolla piccola che sta su tutto: la patta, la fibbia d’ottone, la tracolla lunga per portarla di traverso.',
             varianti=('colore', [NA, CN, NE], [])),
    prodotto('TRA-007', 'Mini Mandarino', 'mini-mandarino', 'tracolle', 129, 99, 12, 4.8, 220, True,
             ['pexels-8801100.jpg', 'pexels-8217756.jpg'], ['Pelle liscia color mandarino', 'Manico corto e tracolla',
                                                             'Ci stanno chiavi, rossetto e carte', 'Chiusura a calamita'],
             'La borsa mini color mandarino: chiavi, rossetto, carte e il telefono, e basta così.', cross=['TRA-017']),
    prodotto('TRA-008', 'Tracolla Vimini', 'tracolla-vimini', 'tracolle', 165, 0, 10, 4.5, 64, False,
             ['pexels-20399717.jpg'], ['Pelle e intreccio', 'Fodera in tela', 'Tracolla in cuoio', 'Fatta a mano'],
             'Il cuoio liscio davanti, l’intreccio sui fianchi: la tracolla di mezza stagione.'),
    prodotto('TRA-009', 'Mini Limone, arricciata', 'mini-limone', 'tracolle', 119, 0, 18, 4.6, 76, False,
             ['pexels-8801061.jpg', 'pexels-7735762.jpg'], ['Nappa morbida arricciata', 'Manico corto imbottito',
                                                             'Tracolla a catena', 'Giallo limone'],
             'La nappa arricciata come una nuvola: si porta a mano o con la catena, per l’estate e per le feste.'),
    prodotto('TRA-010', 'Zaino Città, cuoio e tela', 'zaino-citta', 'zaini', 210, 0, 15, 4.8, 203, True,
             ['pexels-36958694.jpg', 'pexels-2453823.jpg', 'pexels-164697.jpg'],
             ['Tela cerata e cuoio', 'Tasca imbottita per il computer', 'Spallacci regolabili', 'Resiste alla pioggia'],
             'Lo zaino per andare al lavoro in bicicletta: la tela cerata non teme la pioggia, il cuoio invecchia bene.',
             varianti=('colore', [NA, NE], [])),
    prodotto('TRA-011', 'Zaino Corda, a uncinetto', 'zaino-corda', 'zaini', 89, 69, 11, 4.4, 58, False,
             ['pexels-10820410.jpg'], ['Cotone a uncinetto', 'Patta in cuoio', 'Fodera interna', 'Leggero: 320 g'],
             'Lo zaino a uncinetto con la patta in cuoio: leggero, per il mercato e per la gita.'),
    prodotto('TRA-012', 'Pochette Serata, catena e strass', 'pochette-serata', 'pochette', 159, 0, 8, 4.7, 92, False,
             ['pexels-15432831.jpg'], ['Raso nero e strass', 'Catena staccabile', 'Ci sta il telefono',
                                       'Nella scatola regalo'], 'La pochette per Capodanno: il raso nero, gli strass e la catena.'),
    prodotto('TRA-013', 'Pochette Trapuntata, catena dorata', 'pochette-trapuntata', 'pochette', 175, 139, 9, 4.9, 176, True,
             ['pexels-20591024.jpg', 'pexels-5243552.jpg'], ['Nappa trapuntata a rombi', 'Catena dorata lunga',
                                                              'Si porta a mano o a spalla', 'Nella scatola regalo'],
             'La trapuntata col catenella dorata: a mano a cena, a spalla quando si balla.', cross=['TRA-016']),
    prodotto('TRA-014', 'Pochette Cuore, argento', 'pochette-cuore', 'pochette', 99, 0, 7, 4.5, 41, False,
             ['pexels-15432797.jpg'], ['A forma di cuore', 'Argento lucido', 'Manico rigido', 'Ci stanno rossetto e chiavi'],
             'Un cuore d’argento da tenere in mano: per gli sposi, le feste, San Valentino.'),
    prodotto('TRA-015', 'Clutch Perla', 'clutch-perla', 'pochette', 139, 0, 10, 4.6, 63, False,
             ['pexels-33198066.jpg', 'pexels-36674977.jpg'], ['Pelle color perla', 'Chiusura a scatto', 'Tasca per le carte',
                                                               'Catena nascosta'], 'La clutch bianca delle cerimonie.'),
    prodotto('TRA-016', 'Portafoglio Libro, cuoio', 'portafoglio-libro', 'piccola-pelletteria', 79, 59, 25, 4.8, 302, False,
             ['pexels-32954513.jpg', 'pexels-15763948.jpg'], ['Otto carte e due tasche', 'Cuoio conciato al vegetale',
                                                               'Cuciture a mano', 'Le iniziali impresse a caldo'],
             'Il portafoglio che si apre come un libro: otto carte, le banconote, le iniziali sul retro.'),
    prodotto('TRA-017', 'Portacarte Cuciture, set di due', 'portacarte-cuciture', 'piccola-pelletteria', 49, 0, 30, 4.7, 188, False,
             ['pexels-28028262.jpg', 'pexels-11676702.jpg', 'pexels-6651193.jpg'],
             ['Due portacarte', 'Cucitura a mano a punto sellaio', 'Quattro carte ciascuno', 'Il video del laboratorio'],
             'Due portacarte cuciti a punto sellaio: uno per voi, uno da regalare. Guardate come si cuciono nel video.',
             video=V_PELLE),
    prodotto('TRA-018', 'Borsone Weekend, con la tasca per il passaporto', 'borsone-weekend', 'viaggio', 349, 279, 6, 4.9, 144,
             True, ['pexels-1058959.jpg', 'pexels-7821312.jpg', 'pexels-7821329.jpg'],
             ['Sta nel bagaglio a mano', 'Tasca esterna per il passaporto', 'Tracolla imbottita', 'Fondo con i piedini'],
             'Il borsone da due notti fuori: sta nel bagaglio a mano, la tasca davanti tiene il passaporto e la carta d’imbarco.',
             cross=['TRA-016']),
    prodotto('TRA-019', 'Valigetta Ufficio, tre scomparti', 'valigetta-ufficio', 'viaggio', 259, 0, 8, 4.6, 72, False,
             ['pexels-6650001.jpg'], ['Tre scomparti', 'Chiusura con la fibbia', 'Ci sta un computer da 15″', 'Tracolla staccabile'],
             'La valigetta classica col cuoio spesso: tre scomparti, la fibbia, la maniglia cucita due volte.'),
    prodotto('TRA-020', 'Borsa Paglia Fiori', 'borsa-paglia-fiori', 'borse-a-mano', 69, 55, 14, 4.6, 120, False,
             ['pexels-17214199.jpg', 'pexels-17363018.jpg', 'pexels-17363016.jpg'],
             ['Paglia intrecciata a mano', 'Tonda, coi manici in cuoio', 'Fodera in cotone', 'Ultimi pezzi dell’estate'],
             'La borsa di paglia tonda dell’estate: gli ultimi pezzi, a meno.'),
    prodotto('TRA-021', 'Cesto Bambù', 'cesto-bambu', 'borse-a-mano', 85, 0, 9, 4.5, 37, False,
             ['pexels-38350333.jpg', 'pexels-40038347.jpg'], ['Intreccio di fibre naturali', 'Manici ad anello in bambù',
                                                               'Nappine fatte a mano', 'Ogni pezzo è diverso'],
             'Il cesto coi manici di bambù e le nappine: fatto a mano, ognuno diverso.'),
]
RECENSIONI = [[5, 'La pelle è bellissima e si sente il profumo del cuoio. Cucita benissimo.'],
              [5, 'Arrivata in due giorni, impacchettata con la carta della bottega.'],
              [4, 'Un po’ più piccola di come la immaginavo, ma perfetta.'],
              [5, 'Mi hanno impresso le iniziali senza spese: un regalo riuscito.'],
              [5, 'Dopo un anno la pelle è più bella di prima.'],
              [4, 'Ottima, la tracolla potrebbe essere un po’ più lunga.'],
              [5, 'Sono passata in bottega a ritirarla: gentilissimi.'],
              [3, 'Bella, ma il colore dal vivo è più scuro che in foto.']]
NOMI = ['Giulia F.', 'Martina C.', 'Elena R.', 'Sofia B.', 'Chiara D.', 'Anna P.', 'Laura M.', 'Federica V.', 'Marco S.',
        'Silvia T.', 'Paola G.', 'Ilaria N.']
PROMESSE = [('truck', 'Spedizione gratuita da 99 €'), ('rotate-ccw', 'Reso gratuito in 30 giorni'),
            ('wrench', 'Riparazioni gratis per due anni'), ('pen-tool', 'Le iniziali impresse a caldo')]
CONTATTI = [('map-pin', 'Bottega · via Clavature 18, Bologna', P['laboratorio'] + '#bottega'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'Tracolla', icona='shopping-bag', ricerca='Cerca una borsa, uno zaino, un portafoglio…',
                     parole='una borsa per il computer\nuna pochette per Capodanno\nun portafoglio da regalare\nlo zaino per la bici',
                     annuncio=('Settimana della pelle: fino al −25%, le iniziali impresse a caldo gratis.', 'Le offerte',
                               P['offerte']), logo_size=32)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'Tracolla', 'shopping-bag', 'Una bottega di borse in via Clavature, a Bologna, col '
                   'laboratorio dietro il negozio: pelle conciata al vegetale, cuciture a mano, riparazioni per sempre.',
                   [('Le borse', [('Borse a mano', N.cat_url('borse-a-mano')), ('Tracolle', N.cat_url('tracolle')),
                                  ('Pochette', N.cat_url('pochette')), ('Offerte', P['offerte'])]),
                    ('Tracolla', [('Il laboratorio', P['laboratorio']), ('Le misure', P['misure']),
                                  ('I preferiti', P['preferiti']), ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni e resi', P['assistenza'] + '#spedizioni'),
                               ('Riparazioni', P['assistenza'] + '#scrivici'), ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Tracolla s.r.l. · Bologna · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera della bottega', 'Le borse nuove, prima degli altri.',
                              'Una lettera al mese: cosa esce dal laboratorio e come si cura la pelle.',
                              'Fatto: la prima lettera arriva coi saldi in anteprima.'),
                   social=[('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.5  # la larghezza media di una lettera dei titoli (DM Serif Display, per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-6)
    return N.vetrina(t, [
        N.offerta('tr-o1', K.sfondo_video(V_TOTE, overlay=0.12), 'Settimana della pelle · fino a domenica',
                  'La borsa Cognac a 229 €.', 'Pelle conciata al vegetale, cucita a mano in via Clavature. Era 289 €.',
                  'Le borse a mano', N.cat_url('borse-a-mano'), bollino='−21%', bollino_x=70, nota='Le iniziali impresse a caldo, gratis',
                  primo=True, **f),
        N.offerta('tr-o2', K.sfondo_video(V_BOTTEGA, overlay=0.12), 'Pochette per le feste',
                  'La pochette trapuntata a 139 €.', 'Catena dorata: a mano a cena, a spalla quando si balla. Era 175 €.',
                  'Le pochette', N.cat_url('pochette'), bollino='−21%', nota='Nella scatola regalo di Tracolla', **f),
        N.offerta('tr-o3', K.sfondo_foto('pexels-1058959.jpg', overlay=0.12, scala=1.06), 'Viaggio · per le partenze di dicembre',
                  'Il borsone Weekend a 279 €.', 'Sta nel bagaglio a mano, la tasca davanti tiene il passaporto. Era 349 €.',
                  'Le borse da viaggio', N.cat_url('viaggio'), bollino='−20%', nota='Spedizione gratuita da 99 €', **f),
    ], transizione='paperCut')


# le schede sotto la vetrina: «griglia» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='griglia', voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Appena arrivate', {'orderby': 'date'}, 'Vedi tutte', P['negozio']),
        ('Sotto i 100 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutte', P['negozio'] + '?max_price=100'),
        ('Le più amate', {'orderby': 'rating'}, 'Vedi tutte', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Le borse per forma', 'Sei forme, una per ogni ora',
                              ('La borsa Cognac,', '229 €.', 'Conciata al vegetale, era 289 €.', 'Borse a mano',
                               N.cat_url('borse-a-mano'), 'pexels-10919291.jpg'), tutto='Tutte le borse')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Settimana della pelle', 'Fino al −25%.',
                              'Quelle di oggi finiscono tra:',
                              ('Il portafoglio Libro,', '59 €.', 'In cuoio, con le iniziali. Era 79 €.', 'Piccola pelletteria',
                               N.cat_url('piccola-pelletteria'), 'pexels-32954513.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Appena uscite dal laboratorio',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('Le pochette', 'per le feste.', 'Catene, strass, trapuntate.', 'Le pochette', N.cat_url('pochette'),
                              'pexels-20591024.jpg'), tutte='Tutte le novità')


# ── La giornata in borse: lo slider resta fermo, la lancetta gira, per ogni ora entra la sua borsa ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1200.0
ORE = [('08:00 · in ufficio', 'Tote Biblioteca', 'pexels-29359841.jpg', '239 €', 'borse-a-mano', 240),
       ('13:00 · a pranzo', 'Tracolla Onda', 'pexels-12061812.jpg', '149 €', 'tracolle', 390),
       ('18:30 · l’aperitivo', 'Mini Mandarino', 'pexels-8801100.jpg', '99 €', 'tracolle', 555),
       ('21:00 · a teatro', 'Pochette Trapuntata', 'pexels-20591024.jpg', '139 €', 'pochette', 630)]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def giornata(t):
    m = K.mob
    chiaro_t = mix(C['chiaro'], 62)
    L = [
        K.livello('gi-o', contenuto='La giornata in borse', x=6, y=10, w=40, size=13, peso='500', colore=OTTONE,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.6, 'width': 86}}),
        K.livello('gi-t', contenuto='Una borsa<br><em>per ogni ora.</em>', tag='h2', x=6, y=14, w=42, size=66, peso='400',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.0, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(36), 'x': 7, 'y': 4.6, 'width': 86}}),
    ]
    # l'orologio: il quadrante, le ore, la lancetta che gira con lo scorrimento, il perno
    cx, cy, d = 310.0, 560.0, 360.0
    cxm, cym, dm = 320.0, 380.0, 300.0
    a, b = _pc(cx - d / 2, cy - d / 2, d, d, W, H), _pc(cxm - dm / 2, cym - dm / 2, dm, dm, WM, HM)
    L.append(K.livello('gi-quadrante', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['chiaro'], 5),
                       borderRadius=999, borderWidth=2, borderColor=mix(C['chiaro'], 28), entra='fadeIn', ritardo=0,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    for n, (dx, dy) in (('12', (0, -1)), ('3', (1, 0)), ('6', (0, 1)), ('9', (-1, 0))):
        r, rm = d / 2 - 30, dm / 2 - 26
        a = _pc(cx + dx * r - 20, cy + dy * r - 12, 40, 24, W, H)
        b = _pc(cxm + dx * rm - 20, cym + dy * rm - 12, 40, 24, WM, HM)
        L.append(K.livello('gi-n%s' % n, contenuto=n, x=a[0], y=a[1], w=a[2], size=15, peso='500', colore=chiaro_t,
                           align='center', fontFamily=MONO, entra='fadeIn', ritardo=0,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(12)}}))
    lancetta = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': 240}, {'p': 12, 'r': 240}, {'p': 32, 'r': 390},
                                                    {'p': 52, 'r': 555}, {'p': 72, 'r': 630}, {'p': 100, 'r': 690}]}
    lh, lhm = d * 0.84, dm * 0.84
    a, b = _pc(cx - 3, cy - lh / 2, 6, lh, W, H), _pc(cxm - 3, cym - lhm / 2, 6, lhm, WM, HM)
    L.append(K.livello('gi-lancetta', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=3,
                       shapeGradient={'angle': 180, 'from': OTTONE, 'to': mix(OTTONE, 0), 'fromStop': 50},
                       entra='fadeIn', ritardo=0, scrollMove=lancetta,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a, b = _pc(cx - 9, cy - 9, 18, 18, W, H), _pc(cxm - 8, cym - 8, 16, 16, WM, HM)
    L.append(K.livello('gi-perno', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=OTTONE, borderRadius=999,
                       entra='fadeIn', ritardo=0, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # le borse: entrano da destra una dopo l'altra e restano; l'ora di ognuna compare sotto il titolo
    cw, ch, cwm, chm = 600.0, 132.0, 560.0, 120.0
    for k, (ora, nome, foto, prezzo, cat, _) in enumerate(ORE):
        y, ym = 120 + (ch + 22) * k, 570 + (chm + 18) * k
        entra = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 520, 'o': 0}, {'p': 12 + 20 * k, 'x': 520, 'o': 0},
                                                     {'p': 26 + 20 * k, 'x': 0, 'o': 100}, {'p': 100, 'x': 0, 'o': 100}]}
        x0 = W - 80 - cw
        a, b = _pc(x0, y, cw, ch, W, H), _pc(40, ym, cwm, chm, WM, HM)
        L.append(K.livello('gi-scheda%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['chiaro'],
                           borderRadius=12, boxShadow={'x': 0, 'y': 20, 'blur': 44, 'spread': -16, 'color': mix(C['nero'], 70)},
                           entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0 + 12, y + 12, 108, 108, W, H), _pc(52, ym + 12, 96, 96, WM, HM)
        L.append(K.livello('gi-foto%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a[0], y=a[1], w=a[2],
                           h=a[3], borderRadius=8, entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0 + 140, y + 18, 440, 22, W, H), _pc(166, ym + 14, 410, 26, WM, HM)
        L.append(K.livello('gi-ora%d' % k, contenuto=ora, x=a[0], y=a[1], w=a[2], size=13, peso='500', colore=C['acc'],
                           letterSpacing=1, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=0,
                           scrollMove=entra, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(10)}}))
        a, b = _pc(x0 + 140, y + 42, 440, 34, W, H), _pc(166, ym + 40, 410, 34, WM, HM)
        L.append(K.livello('gi-nome%d' % k, contenuto=nome, x=a[0], y=a[1], w=a[2], size=27, peso='400', colore=C['testo'],
                           fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(17)}}))
        a, b = _pc(x0 + 140, y + 86, 200, 32, W, H), _pc(166, ym + 80, 260, 30, WM, HM)
        L.append(K.livello('gi-prezzo%d' % k, tipo='button', contenuto='%s · vedi' % prezzo, x=a[0], y=a[1], w='auto', size=12,
                           peso='500', colore=C['on_acc'], bgColor=C['acc'], padding=8, borderRadius=6, fontFamily=MONO,
                           entra='fadeIn', ritardo=0, buttonUrl=N.cat_url(cat), hover={'bgColor': C['nero'], 'duration': 200},
                           scrollMove=entra, responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(10)}}))
    L.append(K.livello('gi-s', contenuto='Scorrete la giornata: la lancetta gira e arriva la borsa giusta, dalla tote del '
                       'lavoro alla pochette del teatro.', x=6, y=34, w=26, size=15, colore=mix(C['chiaro'], 82),
                       lineHeight=1.6, ritardo=500, responsive={'mobile': {'visible': False}}))
    sl = [K.diapositiva('gi-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, L, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, globalBackground={'type': 'color', 'color': C['nero']},
                       heightMobile={'mode': 'px', 'value': 1200})


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small',
                         style={'margin_top': 24}))
    out.append(t.section([
        N.testata_sezione(t, 'Le borse per forma', 'Che borsa cercate?', P['negozio'], 'Tutte le borse'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, giornata(t)))
    out.append(t.section([
        N.testata_sezione(t, 'Appena arrivate', 'Uscite dal laboratorio questa settimana', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='date', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Le pochette', 'per le feste.', 'Catene, strass, raso: da Capodanno alle cerimonie.',
                               'Le pochette', N.cat_url('pochette'), K.photo_bg('pexels-15432831.jpg'), size=36)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Lo zaino', 'che va al lavoro.', 'Tela cerata e cuoio: la pioggia non lo bagna.',
                               'Gli zaini', N.cat_url('zaini'), K.video_bg(V_CAMICIA), size=36)], wm='1-2', ws='1-1')],
              layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Settimana della pelle', color=OTTONE),
                      N.titolo(t, 'Finisce domenica.', size=44, color=C['chiaro']),
                      N.testo(t, 'Fino al −25% sulle borse e sulla piccola pelletteria, finché ci sono i pezzi.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42, giorni=True), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left',
                                                              pad_top=16)], wm='1-2', ws='1-1')],
              layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=9, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'], card_style='border')]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_CUCITURA, 'Il laboratorio', 'Cucita a mano, <em>dietro la bottega.</em>',
                             'Due banchi, il filo cerato, il punto sellaio: ogni borsa esce di qui, e qui torna se si scuce.',
                             overlay=40, extra=[N.pulsante(t, 'Il laboratorio', P['laboratorio'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Le più amate', 'Quelle che si regalano', P['negozio']),
        t.one([N.prodotti(t, orderby='rating', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-33428433.jpg', 'Due mani cuciono un portafoglio sul tappetino verde', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Per sempre'), N.titolo(t, 'Si scuce?<br><em>La ricuciamo noi.</em>', size=50),
                      N.testo(t, 'Due anni di riparazioni gratuite su ogni borsa, e dopo a prezzo di costo: cuciture, fibbie, '
                                 'manici, fodere. Si porta in bottega o si spedisce col corriere che mandiamo noi.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Riparazioni gratis per due anni', 'Le iniziali impresse a caldo, gratis',
                              'Il cuoio nutrito una volta l’anno, in bottega']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'Come funziona', P['assistenza'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'La bottega', 'Tutte le borse', 'Borse a mano, tracolle, zaini, pochette, piccola pelletteria e '
                            'borse da viaggio: pelle conciata al vegetale, cucite a mano.', colonne=3,
                            banner=N.banner(t, 'Un regalo?', 'Le iniziali, gratis.', 'Impresse a caldo sul cuoio, in due giorni.',
                                            'La piccola pelletteria', N.cat_url('piccola-pelletteria'),
                                            K.photo_bg('pexels-6651193.jpg'), size=36))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'La Mini Mandarino,', '99 €.', 'Chiavi, rossetto e carte. Era 129 €.', 'Le tracolle',
                        N.cat_url('tracolle'), K.photo_bg('pexels-8217756.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'La paglia d’estate,', '55 €.', 'Gli ultimi pezzi, coi manici in cuoio.', 'Le borse a mano',
                        N.cat_url('borse-a-mano'), K.photo_bg('pexels-17363018.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Settimana della pelle', 'Fino al −25%, fino a domenica', 'Borse, pochette, zaini e portafogli '
                            'a meno, finché ci sono i pezzi. Le iniziali impresse a caldo restano gratis.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Spedizione gratuita da 99 €'),
                                                   ('rotate-ccw', 'Reso gratuito in 30 giorni'),
                                                   ('wrench', 'Riparazioni gratis per due anni'),
                                                   ('pen-tool', 'Le iniziali impresse a caldo')],
                             bundle='Si porta con', miniature='left', altezza='600px', bottone='Metti nel carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 99 €. Le iniziali si chiedono alla cassa, nelle note.',
                             PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna, il pagamento. Nelle note le iniziali da imprimere.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, le riparazioni in corso, gli indirizzi.',
                            ('Una riparazione?', 'Scriveteci col numero dell’ordine: mandiamo noi il corriere.',
                             'Chiedi la riparazione', P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto le tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto delle borse.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'In bottega dal martedì al sabato, 10–19:30; al telefono e in chat.', [
        ('truck', 'Spedizioni', 'Gratis da 99 €, in due giorni lavorativi.', 'spedizioni'),
        ('rotate-ccw', 'Resi', 'Trenta giorni, gratis, col corriere che mandiamo noi.', 'spedizioni'),
        ('wrench', 'Riparazioni', 'Gratis per due anni, poi a prezzo di costo.', 'scrivici'),
        ('pen-tool', 'Iniziali', 'Impresse a caldo sul cuoio, in due giorni.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 99 €; sotto, 6,90 €. Arriva in due giorni lavorativi.'),
        ('Come si cura la pelle?', 'Un panno asciutto e, una volta l’anno, la crema per il cuoio: la passiamo noi in bottega, '
                                   'gratis.'),
        ('Posso restituire una borsa con le iniziali?', 'Sì, entro trenta giorni come le altre: le iniziali le teniamo noi.'),
        ('Fate borse su misura?', 'Sì, a partire dai nostri modelli: colore, misure, tasche. Scriveteci.')], CONTATTI,
        campi_extra='Una borsa su misura\nUna riparazione\n', foto=('pexels-6648416.jpg', 'Le borse sugli scaffali di legno'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa borsa <em>non c’è più.</em>', 'Forse l’abbiamo venduta: ne cuciamo poche. '
                        'Cercatela qui sopra.', K.video_bg(V_SCAFFALI, overlay=40))


def build_laboratorio():
    t = Tpl(ID, 'laboratorio')
    out = [K.intro(t, 'Il laboratorio', ['Cucita a mano,', 'dietro la bottega.'], 'Due banchi, il filo cerato, il '
                   'punto sellaio: ogni borsa di Tracolla esce dal laboratorio di via Clavature.',
                   bg=K.video_bg(V_PELLE, overlay=40), size=86)]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La pelle'), N.titolo(t, 'Conciata al vegetale,<br><em>in Toscana.</em>', size=46),
                      N.testo(t, 'La pelle arriva da una conceria di Santa Croce sull’Arno che usa i tannini delle cortecce, '
                                 'non il cromo: per questo profuma di cuoio e col tempo si scurisce, invece di sciuparsi.',
                              size=17)], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-6660912.jpg', 'La pelle tagliata e gli attrezzi sul banco', ratio='4/3')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 64}))
    passi = [('scissors', 'Il taglio', 'Ogni pezzo si taglia a mano sul cartamodello: una borsa ne ha da 20 a 40.'),
             ('pen-tool', 'La cucitura', 'Punto sellaio con due aghi e il filo cerato: se un punto si rompe, gli altri tengono.'),
             ('flame', 'I bordi', 'Si levigano e si tingono tre volte, a cera calda: non si sfilacciano.'),
             ('award', 'La firma', 'Il numero della borsa e le iniziali di chi l’ha cucita, impressi sotto la patta.')]
    out.append(t.section([
        N.testata_sezione(t, 'Come nasce', 'Quattro passaggi, due giorni di lavoro'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in passi],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.numeri(t, [('2', 'banchi in laboratorio'), ('40', 'pezzi in una borsa'), ('2 anni', 'di riparazioni gratis'),
                            ('1974', 'la bottega apre')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    out.append(K.video_frase(t, V_ZIP, 'Il dettaglio', 'La zip si cuce <em>per ultima.</em>',
                             'Ottone pieno, che non si ossida: si cambia in bottega in venti minuti, se un giorno si rompe.',
                             overlay=40))
    out.append(t.section([
        N.testata_sezione(t, 'Cucite qui', 'Le borse del laboratorio', P['negozio']),
        t.one([N.prodotti(t, featured=True, posts_per_page=8, columns=4)]),
    ], html_id='bottega', padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 72}))
    return out


def build_misure():
    t = Tpl(ID, 'misure')
    out = [N.intestazione(t, 'Le misure', 'Cosa ci sta dentro?', 'Per ogni forma le misure e quello che porta: il computer, il '
                          'quaderno, la borraccia, il telefono grande.')]
    punti = [(38, 58, 'Il computer', 'Fino a 14″ nella tote Biblioteca', 'pexels-29359841.jpg', 'borse-a-mano'),
             (64, 40, 'Il quaderno', 'Un A4 e due libri', 'pexels-21897124.jpg', 'borse-a-mano'),
             (52, 74, 'La tasca', 'Interna, con la zip: chiavi e carte', 'pexels-10919291.jpg', 'borse-a-mano')]
    hotspot = t.bare('hotspot', {
        'image': K.img('pexels-29359841.jpg'), 'image_alt': 'Una tote in pelle sul divano col computer dentro',
        'aspect_ratio': 'custom', 'aspect_ratio_custom': '3/2', 'object_fit': 'cover', 'border_radius': radius(12),
        'preset': 'custom', 'marker_style': 'number', 'marker_color': C['chiaro'], 'marker_text_color': C['nero'],
        'marker_size': 28, 'pulse_animation': True, 'tooltip_bg': C['chiaro'], 'tooltip_color': C['testo'], 'tooltip_width': 250,
        'markers': [{'pos_x': x, 'pos_y': y, 'title': ti, 'description': de, 'icon': '', 'image': K.img(f), 'link': N.cat_url(cat),
                     'link_label': 'Vedi', 'tooltip_position': 'top'} for x, y, ti, de, f, cat in punti]})
    tabella = t.classic('table', {
        'table_data': [['Forma', 'Misure (cm)', 'Ci sta', 'Peso'],
                                  ['Tote', '38 × 30 × 12', 'Computer 14″, A4, borraccia', '780 g'],
                                  ['Borsa a mano', '32 × 24 × 13', 'A5, ombrello piccolo, astuccio', '640 g'],
                                  ['Secchiello', '24 × 26 × 20', 'Portafoglio, occhiali, una sciarpa', '480 g'],
                                  ['Tracolla', '24 × 17 × 7', 'Telefono grande, portafoglio, chiavi', '360 g'],
                                  ['Mini', '18 × 13 × 6', 'Telefono, carte, rossetto', '240 g'],
                                  ['Pochette', '24 × 14 × 4', 'Telefono, carte, chiavi', '220 g'],
                                  ['Zaino', '30 × 40 × 14', 'Computer 15″, cambio, borraccia', '1,1 kg'],
                                  ['Borsone', '50 × 28 × 24', 'Due notti fuori, nel bagaglio a mano', '1,4 kg']],
        'has_header': True, 'striped': True, 'bordered': True, 'hover_effect': True, 'compact': False, 'first_col_bold': True,
        'col_alignments': ['left', 'center', 'left', 'center'], 'responsive_mode': 'scroll',
        'header_bg': C['nero'], 'header_text_color': C['chiaro'], 'text_color': C['testo'], 'border_color': C['filo'],
        'even_row_bg': C['sala']})
    out.append(t.section([
        t.row([t.col([hotspot], wm='3-5', ws='1-1'),
               t.col([N.occhiello(t, 'Toccate i numeri'), N.titolo(t, 'La tote del lavoro,<br><em>aperta.</em>', size=44),
                      N.testo(t, 'Il computer da 14″ entra di lato, il quaderno A4 sta dritto, la tasca con la zip tiene chiavi '
                                 'e carte. Sul fondo i piedini: la borsa sta in piedi sotto la scrivania.', size=17)],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=48, valign='center'),
    ], padding='large', width='large', style={'padding_top': 64, 'padding_bottom': 56}))
    out.append(t.section([
        N.testata_sezione(t, 'La tabella', 'Le misure di ogni forma'),
        t.one([tabella]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 72, 'padding_bottom': 72}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le domande'), N.titolo(t, 'Fra due misure?', size=40),
                      K.faq(t, [('Ci sta il mio computer?', 'Misurate la diagonale: fino a 14″ nella tote, fino a 15″ nello zaino '
                                                            'e nella valigetta.'),
                                ('La tracolla si accorcia?', 'Sì, ha cinque fori: da 100 a 130 cm. Se serve più corta la '
                                                             'accorciamo in bottega.'),
                                ('Posso provarla in negozio?', 'Certo, in via Clavature 18: tutte le borse del sito sono anche lì.')],
                            'mis')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-6648416.jpg', 'Le borse sugli scaffali di legno della bottega', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'La giornata in borse',
    ['borse', 'pelletteria', 'pelle', 'woocommerce', 'e-commerce', 'bottega', 'bologna', 'artigianato'],
    'Il negozio online di una bottega di borse nel centro di Bologna, col laboratorio dietro il negozio: in apertura la fascia '
    'delle offerte che scorre e le quattro schede coi prodotti, il mega menu con le borse per forma, le offerte e le novità; '
    'a metà pagina la giornata in borse, con la lancetta che gira e una borsa per ogni ora; le misure di ogni forma, il '
    'laboratorio, la scheda prodotto col video e i colori.',
    [('Le borse', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Borse a mano', '{{PAGINA:negozio}}?product_cat=borse-a-mano'), ('Tracolle', '{{PAGINA:negozio}}?product_cat=tracolle'),
     ('Pochette', '{{PAGINA:negozio}}?product_cat=pochette'), ('Il laboratorio', P['laboratorio']), ('Le misure', P['misure'])],
    [('home', 'Home', None), ('negozio', 'Le borse', 'borse'), ('laboratorio', 'Il laboratorio', 'laboratorio'),
     ('misure', 'Le misure', 'misure'), ('offerte', 'Settimana della pelle', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Outfit\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'DM Serif Display\', Georgia, serif',
    '\'DM Mono\', ui-monospace, Menlo, monospace', ['DM Serif Display', 'Outfit', 'DM Mono'], corsivo_di=['DM Serif Display'],
    peso_titoli='400', raggio=10, grana=0, titoli_line='1.02', titoli_spazio='-0.01em')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Le borse': ('pannello-reparti', 'Pannello delle borse'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'fibbia'
THEME['cursor']['ring_color'] = HEX['secondary']  # il colore del cursore (sul fondo chiaro il chiaro non si vede)


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'laboratorio.json': build_laboratorio(), 'misure.json': build_misure(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'colore': {'nome': 'Colore', 'termini': COLORI}},
                    dentro='La borsa nella sua sacca di cotone, la scheda della pelle, il biglietto di chi l’ha cucita.')


if __name__ == '__main__':
    main()
