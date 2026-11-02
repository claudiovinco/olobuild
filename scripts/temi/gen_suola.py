# -*- coding: utf-8 -*-
"""Genera il tema «Suola» (id suola): un negozio di scarpe in via Garibaldi, a Torino, col calzolaio in fondo alla bottega —
sneakers, mocassini, stivaletti, décolleté e sandali, scarpe da uomo e da corsa, coi numeri e le risuolature.

Tema chiaro e deciso: il blu inchiostro dei pulsanti, il rosso pomodoro dei bollini, il bianco delle sneakers. Titoli in
Anton (stretto e alto, da insegna), testo in Manrope, numeri e prezzi in Space Mono. Angoli appena smussati.

Apertura: la vetrina delle offerte, come nei negozi grandi: una fascia bassa che scorre da sola (la sneaker Gesso, le
décolleté per le feste, lo stivaletto per la pioggia), sotto le quattro schede coi prodotti veri; il mega menu apre i
reparti, le offerte e le novità. A metà pagina «il passo»: lo slider resta fermo mentre si scorre, le impronte camminano
sullo schermo, il metro dei numeri scorre in basso e in fondo al cammino arrivano le scarpe. Cursore: il laccio.

WooCommerce: il catalogo coi filtri per numero, la scheda con la galleria (il video della scarpa) e i numeri, il carrello,
la cassa a passi, l'account, i preferiti; la guida ai numeri e il calzolaio (risuolature e riparazioni).
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'suola'
NAME = 'Suola'

HEX = {
    'primary': '#1E2A78', 'primary_contrast': '#FFFFFF', 'secondary': '#F25C3A', 'secondary_contrast': '#10142E',
    'accent': '#C2281C', 'dark': '#10142E', 'light': '#FFFFFF', 'muted': '#EEF0F7', 'muted_contrast': '#12162E',
    'text': '#12162E', 'text_muted': '#555B75', 'background': '#F7F7FA', 'border': '#D9DCE8', 'link': '#1E2A78',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'numeri', 'calzolaio', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'negozio@suola.example', '+39 011 000 3846', 'tel:+390110003846'

V_STIVALI, V_STRISCE, V_SNEAKERS, V_STRADA, V_CALZOLAIA, V_MOLA, V_TAGLIO, V_TACCHI, V_PARCO, V_SCALE = (
    'pexels-video-5321384', 'pexels-video-5434539', 'pexels-video-5665059', 'pexels-video-32267115', 'pexels-video-37655310',
    'pexels-video-9936742', 'pexels-video-40318177', 'pexels-video-8061666', 'pexels-video-15017744', 'pexels-video-9807773')
VIDEOS = [V_STIVALI, V_STRISCE, V_SNEAKERS, V_STRADA, V_CALZOLAIA, V_MOLA, V_TAGLIO, V_TACCHI, V_PARCO, V_SCALE]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    28645957, 7193623, 537466, 7193634, 13807630, 11962357, 13923530, 13919725, 13923676, 13525164, 8193568, 13922656,
    4046699, 29090887, 30915712, 10939830, 27533556, 4729488, 2128379, 30826570, 3682290, 3682291, 37173051,
    16681834, 17591638, 12896849, 25190055, 13525105, 30133918, 34968265, 8069881, 4637874, 35641833, 8625661, 13611901,
    5894283, 40490864, 8933241, 1556668, 29494530, 6766308, 35641834, 9992898, 38946186, 3801349, 37273499, 34279970,
    2331069, 3360545, 6752428, 5705482, 18984547, 14192652, 7777179, 1858407, 17931282, 26852035, 28381500)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='400', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
POMODORO, ROSSO = C['sec'], C['acc2']
TITOLI, MONO = 'Anton', 'Space Mono'
N = Negozio(K, NAME, P, raggio=6, ratio='1-1', card='border', hover='swap', testata_scura=False,
            consegna='Spedizione gratuita da 79 €', evidenza='Il più venduto in bottega', marca='', peso_titoli='400')

CATEGORIE = [
    ('sneakers', 'Sneakers', 'In pelle e in tela, bianche e da tutti i giorni.', 'pexels-28645957.jpg'),
    ('mocassini', 'Mocassini', 'Col tacco o bassi, in pelle e in nappa.', 'pexels-13923676.jpg'),
    ('stivaletti', 'Stivaletti', 'Per la pioggia e per la sera.', 'pexels-4046699.jpg'),
    ('decollete', 'Décolleté e sandali', 'Tacchi, slingback, ballerine.', 'pexels-3682290.jpg'),
    ('uomo', 'Uomo', 'Oxford, derby e brogue cuciti Goodyear.', 'pexels-34968265.jpg'),
    ('sport', 'Sport', 'Da corsa e da camminata.', 'pexels-26852035.jpg'),
]
NUMERI = [str(n) for n in range(35, 47)]
DONNA, UOMO, TUTTI = NUMERI[1:7], NUMERI[5:11], NUMERI[1:11]


def scarpa(sku, nome, slug, cat, prezzo, sconto, scorta, voto, rec, evid, foto, punti, testo, numeri=None, esauriti=(),
           video=None, **kw):
    num = numeri or (UOMO if cat == 'uomo' else TUTTI if cat in ('sneakers', 'sport') else DONNA)
    return prodotto(sku, nome, slug, cat, prezzo, sconto, scorta, voto, rec, evid, foto, punti, testo,
                    varianti=('numero', num, list(esauriti)), video=video, **kw)


PRODOTTI = [
    scarpa('SUO-001', 'Sneaker Gesso, pelle bianca', 'sneaker-gesso', 'sneakers', 129, 99, 30, 4.8, 612, True,
           ['pexels-28645957.jpg', 'pexels-7193634.jpg', 'pexels-2331069.jpg'], ['Pelle bianca pieno fiore',
                                                                                 'Suola in gomma cucita', 'Plantare estraibile',
                                                                                 'Il video della scarpa'],
           'La sneaker bianca che va con tutto: pelle pieno fiore, suola cucita, plantare che si lava.',
           esauriti=['36'], video=V_SNEAKERS, cross=['SUO-021']),
    scarpa('SUO-002', 'Sneaker Vimini', 'sneaker-vimini', 'sneakers', 119, 0, 18, 4.6, 144, False,
           ['pexels-7193623.jpg', 'pexels-13807630.jpg'], ['Pelle e camoscio', 'Linguetta imbottita', 'Suola leggera',
                                                            'Lacci di cotone cerato'],
           'La sneaker bassa di pelle e camoscio, leggera come una pantofola.'),
    scarpa('SUO-003', 'Sneaker Prato, tela', 'sneaker-prato', 'sneakers', 89, 0, 26, 4.5, 203, False,
           ['pexels-11962357.jpg', 'pexels-537466.jpg', 'pexels-1858407.jpg'], ['Tela di cotone', 'Suola vulcanizzata',
                                                                                 'Si lava a 30°', 'Bianca o naturale'],
           'La tela bianca dell’estate e del weekend: si lava in lavatrice.'),
    scarpa('SUO-004', 'Mocassino Notte, pelle nera', 'mocassino-notte', 'mocassini', 159, 0, 14, 4.8, 188, True,
           ['pexels-13919725.jpg', 'pexels-13922656.jpg'], ['Pelle spazzolata nera', 'Cucitura a mano sulla tomaia',
                                                             'Suola in cuoio', 'Fatto nelle Marche'],
           'Il mocassino nero classico: cucito a mano sulla tomaia, la suola in cuoio che si risuola.'),
    scarpa('SUO-005', 'Mocassino col tacco, pelle nera', 'mocassino-tacco', 'mocassini', 175, 139, 10, 4.7, 96, False,
           ['pexels-13923676.jpg', 'pexels-13923530.jpg'], ['Tacco a blocco 5 cm', 'Pelle nera lucida', 'Plantare in pelle',
                                                             'Comodo tutto il giorno'],
           'Il mocassino col tacco a blocco: si sta in piedi tutto il giorno.'),
    scarpa('SUO-006', 'Mocassino Limone, nappa', 'mocassino-limone', 'mocassini', 145, 0, 8, 4.6, 52, False,
           ['pexels-13525164.jpg'], ['Nappa morbida gialla', 'Senza fodera', 'Suola in gomma sottile', 'Si piega in borsa'],
           'La nappa gialla senza fodera: morbida come un guanto, si piega in borsa.'),
    scarpa('SUO-007', 'Mocassino Amarena', 'mocassino-amarena', 'mocassini', 165, 0, 12, 4.7, 77, False,
           ['pexels-8193568.jpg'], ['Pelle bordeaux spazzolata', 'Mascherina cucita', 'Suola in cuoio', 'Uomo e donna'],
           'Il bordeaux spazzolato per l’autunno: col pantalone grigio e coi jeans.', numeri=TUTTI),
    scarpa('SUO-008', 'Stivaletto Senape', 'stivaletto-senape', 'stivaletti', 189, 149, 9, 4.8, 133, True,
           ['pexels-4046699.jpg'], ['Camoscio senape', 'Tacco 4 cm', 'Zip laterale', 'Suola in gomma'],
           'Lo stivaletto senape col tacchetto: col vestito a fiori e coi jeans.', esauriti=['40']),
    scarpa('SUO-009', 'Stivaletto Bosco, pelle ingrassata', 'stivaletto-bosco', 'stivaletti', 219, 0, 15, 4.9, 241, True,
           ['pexels-10939830.jpg', 'pexels-29090887.jpg'], ['Pelle ingrassata', 'Suola in gomma cucita', 'Impermeabile',
                                                             'Il video sotto la pioggia'],
           'Lo stivaletto da pioggia: pelle ingrassata, la suola cucita, l’acqua non passa. Guardatelo nel video.',
           numeri=TUTTI, video=V_STIVALI),
    scarpa('SUO-010', 'Anfibio Radio, nero', 'anfibio-radio', 'stivaletti', 199, 0, 12, 4.6, 88, False,
           ['pexels-30915712.jpg', 'pexels-27533556.jpg'], ['Pelle nera', 'Otto occhielli', 'Suola carrarmato',
                                                             'Unisex'], 'L’anfibio nero a otto occhielli, uomo e donna.',
           numeri=TUTTI),
    scarpa('SUO-011', 'Décolleté Ciliegia, vernice rossa', 'decollete-ciliegia', 'decollete', 189, 149, 7, 4.8, 102, True,
           ['pexels-3682290.jpg', 'pexels-3682291.jpg', 'pexels-18984547.jpg'], ['Vernice rossa', 'Tacco 9 cm',
                                                                                  'Plantare imbottito', 'Fatta a mano'],
           'La décolleté rossa di vernice: il tacco a spillo da nove, il plantare imbottito per ballare.'),
    scarpa('SUO-013', 'Slingback Cipria', 'slingback-cipria', 'decollete', 169, 0, 9, 4.7, 58, False,
           ['pexels-30826570.jpg'], ['Pelle cipria', 'Punta affusolata', 'Tacco 5 cm', 'Cinturino elastico'],
           'La slingback cipria col tacco basso: dall’ufficio alla cena.'),
    scarpa('SUO-014', 'Sandalo Glicine, cinturino alla caviglia', 'sandalo-glicine', 'decollete', 155, 0, 8, 4.6, 41, False,
           ['pexels-17591638.jpg'], ['Camoscio glicine', 'Cinturino alla caviglia', 'Tacco a blocco 7 cm', 'Fibbia dorata'],
           'Il camoscio color glicine col tacco a blocco: per i matrimoni di primavera.'),
    scarpa('SUO-015', 'Décolleté Sole, giallo', 'decollete-sole', 'decollete', 145, 0, 6, 4.5, 37, False,
           ['pexels-2128379.jpg'], ['Pelle gialla', 'Tacco 8 cm', 'Punta arrotondata', 'Plantare in pelle'],
           'La décolleté gialla per chi non ha paura dei colori.'),
    scarpa('SUO-016', 'Sandalo Cuoio, due fibbie', 'sandalo-cuoio', 'decollete', 119, 95, 14, 4.7, 121, False,
           ['pexels-12896849.jpg', 'pexels-25190055.jpg'], ['Cuoio conciato al vegetale', 'Due fibbie regolabili',
                                                             'Plantare anatomico', 'Ultimi numeri dell’estate'],
           'Il sandalo in cuoio con le due fibbie: gli ultimi numeri dell’estate, a meno.', numeri=TUTTI),
    scarpa('SUO-017', 'Ballerina Sabbia', 'ballerina-sabbia', 'decollete', 99, 0, 16, 4.6, 69, False,
           ['pexels-1556668.jpg', 'pexels-8933241.jpg'], ['Nappa color sabbia', 'Suola flessibile', 'Fiocchetto', 'Si piega'],
           'La ballerina color sabbia che si piega in borsa.'),
    scarpa('SUO-018', 'Oxford Castagna, cucita Goodyear', 'oxford-castagna', 'uomo', 245, 0, 12, 4.9, 287, True,
           ['pexels-34968265.jpg', 'pexels-8069881.jpg', 'pexels-13611901.jpg'], ['Vitello castagna', 'Cucitura Goodyear',
                                                                                    'Suola in cuoio', 'Si risuola in bottega'],
           'L’oxford castagna cucita Goodyear: la suola si cambia in bottega, la scarpa dura vent’anni.', video=V_CALZOLAIA),
    scarpa('SUO-019', 'Derby Brogue, coda di rondine', 'derby-brogue', 'uomo', 229, 189, 10, 4.8, 156, False,
           ['pexels-4637874.jpg', 'pexels-6766308.jpg', 'pexels-9992898.jpg'], ['Vitello marrone', 'Traforature brogue',
                                                                                 'Suola in gomma e cuoio', 'Larga in pianta'],
           'La derby traforata, larga in pianta: per chi sta in piedi tutto il giorno.', esauriti=['45']),
    scarpa('SUO-020', 'Oxford Notte, nera', 'oxford-notte', 'uomo', 259, 0, 9, 4.8, 98, False,
           ['pexels-8625661.jpg', 'pexels-29494530.jpg'], ['Vitello nero lucido', 'Cucitura Goodyear', 'Per lo smoking',
                                                            'Suola in cuoio'],
           'L’oxford nera lucida per la cerimonia e per lo smoking.'),
    scarpa('SUO-021', 'Running Nuvola, grigia', 'running-nuvola', 'sport', 149, 119, 20, 4.7, 340, True,
           ['pexels-26852035.jpg', 'pexels-28381500.jpg'], ['Intersuola ammortizzata', 'Tomaia in rete', 'Pesa 240 g',
                                                             'Per correre fino a 21 km'],
           'La scarpa da corsa morbida per la mezza maratona: 240 grammi, l’intersuola che rimbalza.'),
    scarpa('SUO-022', 'Running Ardesia, nera', 'running-ardesia', 'sport', 139, 0, 16, 4.6, 167, False,
           ['pexels-17931282.jpg'], ['Tomaia in maglia', 'Suola grippante', 'Per camminare e correre', 'Nera'],
           'La scarpa nera per camminare in città e correre al parco.'),
]
RECENSIONI = [[5, 'Il numero giusto al primo colpo grazie alla guida. Comodissime.'],
              [5, 'Arrivate in due giorni, e il cambio numero è stato gratis.'],
              [4, 'Belle, la pelle all’inizio è un po’ rigida, poi si adatta.'],
              [5, 'Le ho fatte risuolare in bottega dopo due anni: come nuove.'],
              [5, 'Le uso tutti i giorni al lavoro, mai un dolore.'],
              [4, 'Calzano mezzo numero abbondante.'],
              [5, 'Commessi gentilissimi, mi hanno misurato il piede.'],
              [3, 'Belle ma il colore dal vivo è più chiaro.']]
NOMI = ['Federico M.', 'Valentina S.', 'Andrea L.', 'Giorgia T.', 'Luca B.', 'Serena P.', 'Matteo R.', 'Alice D.',
        'Davide C.', 'Martina G.', 'Simone F.', 'Noemi V.']
PROMESSE = [('truck', 'Spedizione gratuita da 79 €'), ('repeat', 'Cambio numero gratis'),
            ('rotate-ccw', 'Reso gratuito in 30 giorni'), ('hammer', 'Risuolature in bottega')]
CONTATTI = [('map-pin', 'Negozio · via Garibaldi 31, Torino', P['calzolaio'] + '#bottega'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'SUOLA', icona='footprints', ricerca='Cerca una scarpa, un numero, un colore…',
                     parole='una sneaker bianca\nun mocassino col tacco\nuno stivaletto per la pioggia\nun’oxford da cerimonia',
                     annuncio=('Settimana del passo: fino al −25% e il cambio numero sempre gratis.', 'Le offerte',
                               P['offerte']), logo_size=30)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'SUOLA', 'footprints', 'Un negozio di scarpe in via Garibaldi, a Torino, col calzolaio in '
                   'fondo alla bottega: misuriamo il piede, cambiamo il numero, risuoliamo.',
                   [('Le scarpe', [('Sneakers', N.cat_url('sneakers')), ('Mocassini', N.cat_url('mocassini')),
                                   ('Décolleté e sandali', N.cat_url('decollete')), ('Offerte', P['offerte'])]),
                    ('Suola', [('La guida ai numeri', P['numeri']), ('Il calzolaio', P['calzolaio']),
                               ('I preferiti', P['preferiti']), ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni e resi', P['assistenza'] + '#spedizioni'),
                               ('Cambio numero', P['assistenza'] + '#spedizioni'), ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Suola s.r.l. · Torino · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera del negozio', 'I nuovi arrivi e i saldi, prima.',
                              'Una lettera al mese, e una per i saldi: ventiquattro ore prima degli altri.',
                              'Fatto: la prossima lettera arriva coi nuovi arrivi.'),
                   social=[('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.46  # la larghezza media di una lettera dei titoli (Anton, per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-8)
    return N.vetrina(t, [
        N.offerta('su-o1', K.sfondo_video(V_SNEAKERS, overlay=0.12), 'Sneakers · le offerte della settimana',
                  'La sneaker Gesso a 99 €.', 'Pelle bianca e suola cucita: si lava, si risuola. Era 129 €.', 'Le sneakers',
                  N.cat_url('sneakers'), bollino='−23%', nota='Cambio numero gratis, anche in negozio', primo=True, **f),
        N.offerta('su-o2', K.sfondo_foto('pexels-3682290.jpg', overlay=0.12, scala=1.06), 'Décolleté · per le feste',
                  'La décolleté Ciliegia a 149 €.', 'Vernice rossa, tacco nove, il plantare imbottito per ballare. Era 189 €.',
                  'Décolleté e sandali', N.cat_url('decollete'), bollino='−21%', nota='Spedizione gratuita da 79 €', **f),
        N.offerta('su-o3', K.sfondo_video(V_STIVALI, overlay=0.12), 'Stivaletti · per la pioggia',
                  'Lo stivaletto Senape a 149 €.', 'Camoscio e suola in gomma, col tacchetto. Era 189 €.', 'Gli stivaletti',
                  N.cat_url('stivaletti'), bollino='−21%', nota='Reso gratuito in 30 giorni', **f),
    ], transizione='cubeH')


# le schede sotto la vetrina: «striscia» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='striscia', voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Appena arrivate', {'orderby': 'date'}, 'Vedi tutte', P['negozio']),
        ('Sotto i 100 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutte', P['negozio'] + '?max_price=100'),
        ('Le più amate', {'orderby': 'rating'}, 'Vedi tutte', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'I reparti', 'Sei reparti, dal 35 al 46',
                              ('L’oxford Castagna,', 'cucita Goodyear.', 'Si risuola in bottega, dura vent’anni.', 'Uomo',
                               N.cat_url('uomo'), 'pexels-34968265.jpg'), tutto='Tutte le scarpe')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Settimana del passo', 'Fino al −25%.',
                              'Quelle di oggi finiscono tra:',
                              ('La running Nuvola,', '119 €.', 'Per la mezza maratona. Era 149 €.', 'Sport',
                               N.cat_url('sport'), 'pexels-26852035.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Appena arrivate in negozio',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('Le décolleté', 'per le feste.', 'Vernice, camoscio, cinturini.', 'Décolleté e sandali',
                              N.cat_url('decollete'), 'pexels-3682290.jpg'), tutte='Tutte le novità')


# ── Il passo: lo slider resta fermo, le impronte camminano, il metro dei numeri scorre, arrivano le scarpe ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1200.0
ARRIVI = [('Sneaker Gesso', 'pexels-28645957.jpg', '99 €', 'sneakers'),
          ('Stivaletto Bosco', 'pexels-10939830.jpg', '219 €', 'stivaletti'),
          ('Oxford Castagna', 'pexels-34968265.jpg', '245 €', 'uomo')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def passo(t):
    m = K.mob
    L = [
        K.livello('pa-o', contenuto='Il passo · scorrete', x=6, y=10, w=40, size=13, peso='700', colore=POMODORO,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.6, 'width': 86}}),
        K.livello('pa-t', contenuto='Ogni passo,<br>la sua misura.', tag='h2', x=6, y=14, w=44, size=84, peso='400',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=0.98, textTransform='uppercase', entra='maskFromBottom',
                  ritardo=200, durata=1100, responsive={'mobile': {'fontSize': m(40), 'x': 7, 'y': 4.6, 'width': 86}}),
        K.livello('pa-s', contenuto='Misuriamo il piede in negozio, cambiamo il numero gratis, risuoliamo in bottega.',
                  x=6, y=40, w=30, size=16, colore=mix(C['chiaro'], 82), lineHeight=1.6, ritardo=500,
                  responsive={'mobile': {'visible': False}}),
    ]
    # le impronte: la pianta e il tallone, una dopo l'altra lungo il cammino, sinistra e destra. Sul computer
    # camminano verso destra (il tallone dietro, a sinistra), sul telefono salgono (il tallone sotto): due serie,
    # quella del telefono sta fuori dalla tela sugli schermi grandi e rientra sul telefono.
    def impronta(nome, k, pianta, tallone, mob_pianta=None, mob_tallone=None, ultima=False):
        appare = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 1.3}, {'p': 4 + 6 * k, 'o': 0, 's': 1.3},
                                                      {'p': 8 + 6 * k, 'o': 100, 's': 1}, {'p': 100, 'o': 100, 's': 1}]}
        colore = POMODORO if ultima else mix(C['chiaro'], 24)
        for parte, geo, geom in (('p', pianta, mob_pianta), ('t', tallone, mob_tallone)):
            a = _pc(*geo, W, H)
            resp = {'mobile': {'visible': False}}
            if geom:
                b = _pc(*geom, WM, HM)
                resp = {'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}
            L.append(K.livello('pa-%s%s%d' % (nome, parte, k), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=colore,
                               borderRadius=999, entra='fadeIn', ritardo=0, scrollMove=appare, responsive=resp))
    passi = 9
    for k in range(passi):
        cx, cy = 190 + 92 * k, 560 + (22 if k % 2 else -22)
        impronta('d', k, (cx - 33, cy - 20, 66, 40), (cx - 33 - 46, cy - 16, 36, 32), ultima=k == passi - 1)
    passi_m = 7
    for k in range(passi_m):
        cx, cy = 300 + (34 if k % 2 else -34), 1070 - 72 * k
        fuori = (-200, -200, 10, 10)
        impronta('m', k, fuori, fuori, (cx - 20, cy - 33, 40, 66), (cx - 16, cy + 39, 32, 34), ultima=k == passi_m - 1)
    # il metro dei numeri: scorre verso sinistra sotto una tacca fissa
    metro = {'inizio': 0, 'fine': 100, 'da': {'x': 0}, 'a': {'x': -660}}
    for k, n in enumerate(range(35, 47)):
        a = _pc(560 + 110 * k, 742, 80, 36, W, H)
        L.append(K.livello('pa-num%d' % k, contenuto=str(n), x=a[0], y=a[1], w=a[2], size=26, peso='700',
                           colore=mix(C['chiaro'], 70), align='center', fontFamily=MONO, entra='fadeIn', ritardo=0,
                           scrollMove=metro, responsive={'mobile': {'visible': False}}))
    a = _pc(560, 730, 880, 2, W, H)
    L.append(K.livello('pa-riga', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['chiaro'], 30), entra='fadeIn',
                       ritardo=0, responsive={'mobile': {'visible': False}}))
    a = _pc(1000 - 2, 716, 4, 70, W, H)
    L.append(K.livello('pa-tacca', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=POMODORO, entra='fadeIn', ritardo=0,
                       responsive={'mobile': {'visible': False}}))
    # le scarpe in fondo al cammino
    cw, ch, cwm, chm = 330.0, 118.0, 560.0, 112.0
    for k, (nome, foto, prezzo, cat) in enumerate(ARRIVI):
        y, ym = 150 + (ch + 18) * k, 240 + (chm + 16) * k
        entra = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 380, 'o': 0}, {'p': 56 + 12 * k, 'x': 380, 'o': 0},
                                                     {'p': 68 + 12 * k, 'x': 0, 'o': 100}, {'p': 100, 'x': 0, 'o': 100}]}
        x0 = W - 70 - cw
        a, b = _pc(x0, y, cw, ch, W, H), _pc(40, ym, cwm, chm, WM, HM)
        L.append(K.livello('pa-scheda%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['chiaro'], borderRadius=8,
                           boxShadow={'x': 0, 'y': 20, 'blur': 44, 'spread': -16, 'color': mix(C['nero'], 70)}, entra='fadeIn',
                           ritardo=0, scrollMove=entra, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0 + 10, y + 10, 98, 98, W, H), _pc(50, ym + 10, 92, 92, WM, HM)
        L.append(K.livello('pa-foto%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a[0], y=a[1], w=a[2], h=a[3],
                           borderRadius=6, entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0 + 124, y + 20, 196, 34, W, H), _pc(160, ym + 18, 420, 36, WM, HM)
        L.append(K.livello('pa-nome%d' % k, contenuto=nome, x=a[0], y=a[1], w=a[2], size=24, peso='400', colore=C['testo'],
                           fontFamily=TITOLI, textTransform='uppercase', entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(17)}}))
        a, b = _pc(x0 + 124, y + 66, 160, 32, W, H), _pc(160, ym + 62, 260, 32, WM, HM)
        L.append(K.livello('pa-prezzo%d' % k, tipo='button', contenuto='%s · vedi' % prezzo, x=a[0], y=a[1], w='auto', size=12,
                           peso='700', colore=C['on_acc'], bgColor=C['acc'], padding=8, borderRadius=4, fontFamily=MONO,
                           entra='fadeIn', ritardo=0, buttonUrl=N.cat_url(cat), hover={'bgColor': POMODORO, 'color': C['nero'],
                                                                                       'duration': 200},
                           scrollMove=entra, responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(10)}}))
    sl = [K.diapositiva('pa-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, L, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1900, scrollStep=False, globalBackground={'type': 'color', 'color': C['nero']},
                       heightMobile={'mode': 'px', 'value': 1200})


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small',
                         style={'margin_top': 24}))
    out.append(t.section([
        N.testata_sezione(t, 'I reparti', 'Che scarpa cercate?', P['negozio'], 'Tutte le scarpe'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, passo(t)))
    out.append(t.section([
        N.testata_sezione(t, 'Appena arrivate', 'Nuove in vetrina questa settimana', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='date', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Le décolleté', 'per le feste.', 'Vernice rossa, camoscio, cinturini: dal 35 al 41.',
                               'Décolleté e sandali', N.cat_url('decollete'), K.photo_bg('pexels-3682291.jpg'), size=36)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Le oxford', 'che durano vent’anni.', 'Cucite Goodyear: la suola si cambia in bottega.',
                               'Uomo', N.cat_url('uomo'), K.photo_bg('pexels-6766308.jpg'), size=36)], wm='1-2', ws='1-1')],
              layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Settimana del passo', color=POMODORO),
                      N.titolo(t, 'Finisce domenica.', size=48, color=C['chiaro']),
                      N.testo(t, 'Fino al −25% su sneakers, stivaletti e décolleté, finché ci sono i numeri.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42, giorni=True), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left',
                                                              pad_top=16)], wm='1-2', ws='1-1')],
              layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=9, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_MOLA, 'Il calzolaio', 'La suola si cambia, <em>la scarpa resta.</em>',
                             'In fondo alla bottega: risuolature, tacchi, cuciture, in una settimana. Anche per le scarpe non '
                             'comprate da noi.', overlay=40, extra=[N.pulsante(t, 'Il calzolaio', P['calzolaio'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Le più amate', 'Quelle che si ricomprano', P['negozio']),
        t.one([N.prodotti(t, orderby='rating', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-3360545.jpg', 'Le scarpe colorate in fila sullo scaffale', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Il numero giusto'), N.titolo(t, 'Fra due numeri?<br>Ve lo cambiamo.', size=52),
                      N.testo(t, 'Il cambio numero è sempre gratis: in negozio, o col corriere che passa a prendere la scatola. '
                                 'La guida ai numeri vi dice come misurare il piede a casa, con un foglio e una matita.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Cambio numero gratis, sempre', 'Il piede misurato in negozio', 'Reso gratuito in 30 giorni']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'La guida ai numeri', P['numeri'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il negozio', 'Tutte le scarpe', 'Sneakers, mocassini, stivaletti, décolleté, scarpe da uomo e da '
                            'corsa, dal 35 al 46: il cambio numero è sempre gratis.', colonne=3,
                            banner=N.banner(t, 'Fra due numeri?', 'Ve lo cambiamo.', 'Gratis, in negozio o col corriere.',
                                            'La guida ai numeri', P['numeri'], K.photo_bg('pexels-2331069.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Il sandalo Cuoio,', '95 €.', 'Gli ultimi numeri dell’estate. Era 119 €.', 'Décolleté e sandali',
                        N.cat_url('decollete'), K.photo_bg('pexels-12896849.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'La derby Brogue,', '189 €.', 'Larga in pianta, era 229 €.', 'Uomo',
                        N.cat_url('uomo'), K.photo_bg('pexels-4637874.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Settimana del passo', 'Fino al −25%, fino a domenica', 'Sneakers, stivaletti, décolleté e '
                            'scarpe da uomo a meno, finché ci sono i numeri. Il cambio numero resta gratis.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Spedizione gratuita da 79 €'), ('repeat', 'Cambio numero gratis'),
                                                   ('rotate-ccw', 'Reso gratuito in 30 giorni'),
                                                   ('hammer', 'Risuolature in bottega')],
                             bundle='Si abbina con', miniature='left', altezza='600px', bottone='Metti nel carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 79 €. Il cambio numero è sempre gratis.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna, il pagamento. Anche in tre rate senza interessi.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, i cambi di numero, le risuolature in corso.',
                            ('Un cambio di numero?', 'Scriveteci col numero dell’ordine: passa il corriere a prendere la scatola.',
                             'Chiedi il cambio', P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto le tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto delle scarpe.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'In negozio dal lunedì al sabato, 9:30–19:30; al telefono e in chat.', [
        ('truck', 'Spedizioni', 'Gratis da 79 €, in due giorni lavorativi.', 'spedizioni'),
        ('repeat', 'Cambio numero', 'Sempre gratis, col corriere o in negozio.', 'spedizioni'),
        ('rotate-ccw', 'Resi', 'Trenta giorni, gratis.', 'spedizioni'),
        ('hammer', 'Calzolaio', 'Risuolature e riparazioni in una settimana.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 79 €; sotto, 5,90 €. Arriva in due giorni lavorativi.'),
        ('Come cambio il numero?', 'Scriveteci col numero dell’ordine: il corriere passa a prendere la scatola e vi porta il '
                                   'numero giusto, gratis.'),
        ('Come misuro il piede?', 'Con un foglio e una matita: c’è la guida ai numeri, passo per passo.'),
        ('Risuolate anche scarpe non vostre?', 'Sì: portatele in negozio o speditecele, il preventivo è gratis.')], CONTATTI,
        campi_extra='Un cambio di numero\nUna risuolatura\n', foto=('pexels-34279970.jpg', 'Le scarpe nere esposte sugli scaffali'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questo numero <em>è finito.</em>', 'La pagina non c’è più. Cercate la scarpa qui sopra: '
                        'forse c’è nel vostro numero.', K.video_bg(V_STRISCE, overlay=40))


def build_numeri():
    t = Tpl(ID, 'numeri')
    out = [N.intestazione(t, 'La guida ai numeri', 'Il numero giusto, al primo colpo.', 'Un foglio, una matita, un righello: '
                          'due minuti, e il cambio numero resta gratis comunque.')]
    passi = [('file', 'Il foglio', 'Mettete un foglio bianco per terra, contro il muro.'),
             ('footprints', 'Il piede', 'In piedi sul foglio, col tallone contro il muro e la calza che usate.'),
             ('pen-tool', 'Il segno', 'Segnate con la matita la punta dell’alluce più lungo.'),
             ('ruler', 'La misura', 'Misurate dal muro al segno, in centimetri, e cercatela nella tabella.')]
    out.append(t.section([
        N.testata_sezione(t, 'Come si misura', 'Quattro passi, due minuti'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in passi],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 72, 'padding_bottom': 56}))
    righe = [['Numero', 'Piede (cm)', 'UK', 'US donna', 'US uomo']]
    for n in range(35, 47):
        righe.append([str(n), '%.1f' % (22.5 + (n - 35) * 0.67), str(n - 33 if n < 46 else 13), str(n - 31), str(n - 32)])
    tabella = t.classic('table', {
        'table_data': righe, 'has_header': True, 'striped': True, 'bordered': True,
        'hover_effect': True, 'compact': True, 'first_col_bold': True,
        'col_alignments': ['left', 'center', 'center', 'center', 'center'], 'responsive_mode': 'scroll',
        'header_bg': C['nero'], 'header_text_color': C['chiaro'], 'text_color': C['testo'], 'border_color': C['filo'],
        'even_row_bg': C['sala']})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La tabella'), N.titolo(t, 'Dal 35 al 46.', size=48),
                      N.testo(t, 'Se la misura cade fra due numeri, prendete il più grande: la pelle si allarga un poco, non si '
                                 'accorcia. I mocassini calzano mezzo numero abbondante.', size=17)], wm='2-5', ws='1-1'),
               t.col([tabella], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le domande'), N.titolo(t, 'Ancora un dubbio?', size=40),
                      K.faq(t, [('Il numero cambia da un modello all’altro?', 'Un poco: nella scheda di ogni scarpa trovate se '
                                                                              'calza giusta, stretta o abbondante.'),
                                ('Ho il piede largo.', 'Le derby e le sneakers in pelle sono le più larghe in pianta; i '
                                                       'mocassini le più strette.'),
                                ('Posso provarle in negozio?', 'Certo, in via Garibaldi 31: misuriamo il piede con la pedana.')],
                            'num')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-14192652.jpg', 'Due piedi coi mocassini neri e le calze bianche', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_calzolaio():
    t = Tpl(ID, 'calzolaio')
    out = [K.intro(t, 'Il calzolaio', ['La suola si cambia,', 'la scarpa resta.'], 'In fondo alla bottega di via '
                   'Garibaldi: risuolature, tacchi, cuciture e lucidature, in una settimana.',
                   bg=K.video_bg(V_CALZOLAIA, overlay=40), size=86)]
    listino = [('Risuolatura in cuoio', '65 €'), ('Risuolatura in gomma', '45 €'), ('Tacchi da donna', '18 €'),
               ('Tacchi da uomo', '24 €'), ('Cucitura della tomaia', 'da 12 €'), ('Lucidatura a specchio', '15 €')]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Il listino'), N.titolo(t, 'Quanto costa.', size=46),
                      t.classic('iconlist', {
                          'items': [{'icon': 'hammer', 'text': '%s · <strong>%s</strong>' % (a, b), 'link': '', 'color': ''}
                                    for a, b in listino],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '16', 'gap': '12',
                          'icon_shape': 'none', 'icon_bg_color': '', 'divider': True, 'layout': 'vertical'}, style={}),
                      N.testo(t, 'Anche per le scarpe non comprate da noi. Il preventivo è gratis.', size=15,
                              color=C['tenue'], pad=(14, 0, 0, 0))], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-38946186.jpg', 'Il calzolaio al banco nella luce calda della bottega', ratio='4/5')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], html_id='bottega', padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 72}))
    out.append(t.section([
        t.one([K.numeri(t, [('1952', 'la bottega apre'), ('7 giorni', 'per una risuolatura'), ('20 anni', 'dura un’oxford'),
                            ('0 €', 'il preventivo')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    out.append(K.video_frase(t, V_TAGLIO, 'A mano', 'Il guardolo <em>si cuce due volte.</em>',
                             'Così la suola si stacca e si rifà senza toccare la tomaia: è la cucitura Goodyear.', overlay=40))
    out.append(t.section([
        N.testata_sezione(t, 'Da risuolare fra vent’anni', 'Le scarpe cucite Goodyear', N.cat_url('uomo')),
        t.one([N.prodotti(t, category='uomo,mocassini', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'Il passo',
    ['scarpe', 'calzature', 'sneakers', 'woocommerce', 'e-commerce', 'calzolaio', 'torino', 'negozio'],
    'Il negozio online di un negozio di scarpe in via Garibaldi a Torino, col calzolaio in fondo alla bottega: in apertura la '
    'fascia delle offerte che scorre e le quattro schede coi prodotti, il mega menu coi reparti, le offerte e le novità; a '
    'metà pagina il passo, con le impronte che camminano e il metro dei numeri che scorre; la guida ai numeri, il calzolaio, '
    'la scheda prodotto coi numeri e il video della scarpa.',
    [('Le scarpe', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Sneakers', '{{PAGINA:negozio}}?product_cat=sneakers'), ('Donna', '{{PAGINA:negozio}}?product_cat=decollete'),
     ('Uomo', '{{PAGINA:negozio}}?product_cat=uomo'), ('I numeri', P['numeri']), ('Il calzolaio', P['calzolaio'])],
    [('home', 'Home', None), ('negozio', 'Le scarpe', 'scarpe'), ('numeri', 'La guida ai numeri', 'guida-ai-numeri'),
     ('calzolaio', 'Il calzolaio', 'calzolaio'), ('offerte', 'Settimana del passo', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Anton\', \'Impact\', sans-serif',
    '\'Space Mono\', ui-monospace, Menlo, monospace', ['Anton', 'Manrope', 'Space Mono'], corsivo_di=[],
    peso_titoli='400', raggio=6, grana=0, titoli_line='1', titoli_spazio='0.005em')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Le scarpe': ('pannello-reparti', 'Pannello dei reparti'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['preview_v'] = THEME['preview_mobile_v'] = '4'  # anteprime con le testate nuove (2 nov 2026)
THEME['cursor']['style'] = 'laccio'
THEME['cursor']['ring_color'] = HEX['secondary']  # il colore del cursore (sul fondo chiaro il chiaro non si vede)


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'numeri.json': build_numeri(), 'calzolaio.json': build_calzolaio(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'numero': {'nome': 'Numero', 'termini': NUMERI}},
                    dentro='Le scarpe nella scatola di cartone riciclato, i sacchetti di cotone, il cartellino del cambio.')


if __name__ == '__main__':
    main()
