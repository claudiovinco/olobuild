# -*- coding: utf-8 -*-
"""Genera il tema «Ristretto» (id ristretto): una torrefazione in via dei Tribunali, a Napoli, che vende online capsule
compostabili compatibili con le macchine più diffuse, caffè in grani e macinato per moka ed espresso, abbonamenti, macchine,
tazzine e scatole regalo.

Tema chiaro e caldo: il marrone dell'espresso sui pulsanti, il color crema dei bollini, il rosso dei prezzi scontati.
Titoli in Young Serif, testo in Inter Tight, intensità e prezzi in JetBrains Mono. Angoli morbidi.

Apertura: la vetrina delle offerte, come nei negozi grandi: una fascia bassa che scorre da sola (le capsule Intenso, la
scatola Napoli, la macchina a leva), sotto le quattro schede coi prodotti veri; il mega menu apre le miscele, le offerte e le
novità. A metà pagina «la scala dell'intensità»: lo slider resta fermo mentre si scorre, tredici capsule dalla più chiara
alla più scura si accendono una dopo l'altra, la tacca scorre e dice il gusto (dolce, cremoso, intenso, ristretto), in fondo
arrivano le miscele col prezzo. Cursore: il chicco.

WooCommerce: il catalogo coi filtri, la scheda con la galleria (il video dell'estrazione) e la macinatura, il carrello, la
cassa a passi, l'account, i preferiti; la torrefazione e la guida all'intensità.
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'ristretto'
NAME = 'Ristretto'

HEX = {
    'primary': '#4A2C1D', 'primary_contrast': '#FFFFFF', 'secondary': '#D9A066', 'secondary_contrast': '#1C120C',
    'accent': '#B3261E', 'dark': '#1C120C', 'light': '#FFFFFF', 'muted': '#F1E7DC', 'muted_contrast': '#24170F',
    'text': '#24170F', 'text_muted': '#6A5646', 'background': '#F8F2EA', 'border': '#E3D3C2', 'link': '#4A2C1D',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'torrefazione', 'intensita', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'bottega@ristretto.example', '+39 081 000 1313', 'tel:+390810001313'

V_GOCCIA, V_DUE_TAZZE, V_TAZZA_SCURA, V_BICCHIERINO, V_FILTRO, V_CHICCHI, V_MUCCHIO, V_VAPORE, V_CUCCUMA, V_TURCHESE = (
    'pexels-video-7019759', 'pexels-video-2853788', 'pexels-video-9399042', 'pexels-video-30353740', 'pexels-video-36285235',
    'pexels-video-20534643', 'pexels-video-32896425', 'pexels-video-6938926', 'pexels-video-6062966', 'pexels-video-6890261')
VIDEOS = [V_GOCCIA, V_DUE_TAZZE, V_TAZZA_SCURA, V_BICCHIERINO, V_FILTRO, V_CHICCHI, V_MUCCHIO, V_VAPORE, V_CUCCUMA, V_TURCHESE]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    32297816, 21033142, 7243711, 18071820, 7243717, 14792388, 7657981, 9594423, 8015467, 11160133, 11160128, 302893, 29642266,
    13305631, 12265177, 4921556, 4921523, 36192953, 34206522, 9996344, 14356266, 8243689, 15923577, 12703962, 4195607,
    4195322, 4195613, 12267620, 18714960, 39292521, 12784890, 4195608, 12620633, 38722832, 19162213, 8218593, 37510306,
    10357551, 33015766, 9892391, 29611242, 13696018, 32355737, 6032786, 6032787, 459489, 162886, 459306, 18163776, 302903,
    15447295, 12703064, 35411022, 28272182, 4327979, 19791015, 5872188, 5994300, 6958455, 19791005, 27246443, 13119652,
    34800842, 21558658, 14686500, 11038306, 6079719, 4820676, 22718754, 37481133, 7175982, 20121334, 7761601, 7791207,
    36776700, 5266771, 29639312, 7791215, 16785688, 18997241, 37603073, 7658133, 37486085, 18997398)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=12, peso_titoli='400', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
CREMA, ROSSO = C['sec'], C['acc2']
TITOLI, MONO = 'Young Serif', 'JetBrains Mono'
N = Negozio(K, NAME, P, raggio=12, ratio='1-1', card='border', hover='zoom', testata_scura=False,
            consegna='Spedizione gratuita da 39 €', evidenza='Tostato questa settimana', marca='', peso_titoli='400')
N.cta_media = (C['sec'], C['nero'])  # il primario è quasi nero: sul velo scuro il pulsante è color crema

CATEGORIE = [
    ('capsule', 'Capsule', 'Compostabili, compatibili con le macchine più diffuse.', 'pexels-21033142.jpg'),
    ('grani', 'Grani e macinato', 'Per la moka, per l’espresso, in grani.', 'pexels-19162213.jpg'),
    ('abbonamenti', 'Abbonamenti', 'La scatola ogni mese, si sospende quando si vuole.', 'pexels-8015467.jpg'),
    ('macchine', 'Macchine e moka', 'A leva, a capsule, la napoletana.', 'pexels-6032786.jpg'),
    ('tazzine', 'Tazzine e tazze', 'Ceramica di Vietri e vetro doppio.', 'pexels-4195607.jpg'),
    ('regali', 'Regali', 'Scatole con le miscele e le tazzine.', 'pexels-5994300.jpg'),
]
MACINATURE = ['In grani', 'Per espresso', 'Per moka']
PRODOTTI = [
    prodotto('RIS-001', 'Capsule Intenso n. 11, cento pezzi', 'capsule-intenso', 'capsule', 32, 26, 80, 4.8, 1204, True,
             ['pexels-32297816.jpg', 'pexels-9996344.jpg'], ['Intensità 11 su 13', '70% arabica, 30% robusta',
                                                             'Capsule compostabili', 'Il video dell’estrazione'],
             'La miscela del bar sotto casa: corpo pieno, crema nocciola, un finale di cacao amaro.', video=V_GOCCIA,
             cross=['RIS-015']),
    prodotto('RIS-002', 'Capsule Cremoso n. 8, cento pezzi', 'capsule-cremoso', 'capsule', 30, 0, 90, 4.7, 866, True,
             ['pexels-21033142.jpg', 'pexels-36192953.jpg'], ['Intensità 8 su 13', '80% arabica', 'Crema densa',
                                                               'Per il cappuccino'],
             'La crema più alta delle nostre miscele: rotonda, morbida, perfetta col latte.'),
    prodotto('RIS-003', 'Capsule Ristretto n. 13, cento pezzi', 'capsule-ristretto', 'capsule', 34, 27, 60, 4.9, 932, True,
             ['pexels-12265177.jpg', 'pexels-18071820.jpg'], ['Intensità 13 su 13', 'Robusta indiana', 'Tostatura scura',
                                                               'Due sorsi'], 'Il caffè napoletano vero: corto, scuro, carico.'),
    prodotto('RIS-004', 'Capsule Dek, cento pezzi', 'capsule-dek', 'capsule', 31, 0, 70, 4.6, 411, False,
             ['pexels-4921556.jpg'], ['Decaffeinato ad acqua', 'Intensità 7 su 13', 'Arabica brasiliana',
                                     'Gusto pieno, senza caffeina'],
             'Il decaffeinato ad acqua che sa di caffè: per la sera e per chi ne beve tanti.'),
    prodotto('RIS-005', 'Capsule Sidamo, arabica d’Etiopia, cinquanta pezzi', 'capsule-sidamo', 'capsule', 19, 0, 40, 4.7, 233,
             False, ['pexels-9892391.jpg', 'pexels-14356266.jpg'], ['Monorigine Etiopia', 'Intensità 6 su 13',
                                                                     'Note di gelsomino e agrumi', 'Tostatura chiara'],
             'Un’arabica d’altura, fiorita e leggera: per chi il caffè lo assaggia.'),
    prodotto('RIS-006', 'Capsule Ginseng, cinquanta pezzi', 'capsule-ginseng', 'capsule', 18, 0, 45, 4.4, 188, False,
             ['pexels-8243689.jpg'], ['Caffè e ginseng', 'Dolce, cremoso', 'Senza zuccheri aggiunti', 'Come al bar'],
             'Il ginseng come lo fanno al bar: dolce e cremoso, da bere a metà pomeriggio.'),
    prodotto('RIS-007', 'Capsula ricaricabile in acciaio, due pezzi', 'capsula-ricaricabile', 'capsule', 29, 24, 35, 4.6, 154,
             False, ['pexels-7243711.jpg', 'pexels-7243717.jpg'], ['Acciaio inox', 'Si riempie col nostro macinato',
                                                                    'Dura anni', 'Niente rifiuti'],
             'La capsula d’acciaio che si riempie col macinato per espresso: zero rifiuti, il caffè che volete.'),
    prodotto('RIS-008', 'Miscela Tribunali, un chilo', 'miscela-tribunali', 'grani', 26, 0, 50, 4.9, 640, True,
             ['pexels-19162213.jpg', 'pexels-8218593.jpg'], ['La miscela della bottega', 'Tostata a legna',
                                                              'Macinata il giorno della spedizione', 'Il video dei chicchi'],
             'La miscela che serviamo al banco da cinquant’anni: in grani, per espresso o per moka.',
             varianti=('macinatura', MACINATURE, []), video=V_CHICCHI),
    prodotto('RIS-009', 'Moka Napoletana, arabica, 250 g', 'moka-napoletana', 'grani', 9.5, 7.5, 70, 4.7, 512, False,
             ['pexels-37510306.jpg', 'pexels-14792388.jpg'], ['Macinata per moka', 'Arabica 100%', 'Busta con la valvola',
                                                               'Tostata questa settimana'],
             'La macinatura grossa per la moka: il caffè che sale piano e profuma la cucina.',
             varianti=('macinatura', ['Per moka', 'Per espresso'], [])),
    prodotto('RIS-010', 'Cold brew in bottiglia, sei pezzi', 'cold-brew', 'grani', 21, 0, 30, 4.5, 96, False,
             ['pexels-18997241.jpg', 'pexels-16785688.jpg'], ['Infuso a freddo per 18 ore', 'Senza zucchero',
                                                               'Bottiglie da 250 ml', 'In frigo, con il ghiaccio'],
             'Il caffè infuso a freddo per diciotto ore: morbido, senza amaro, da bere col ghiaccio.'),
    prodotto('RIS-011', 'Abbonamento Capsule, cento al mese', 'abbonamento-capsule', 'abbonamenti', 27, 0, 999, 4.8, 344, True,
             ['pexels-8015467.jpg', 'pexels-9594423.jpg'], ['Cento capsule ogni mese', 'Si sceglie la miscela',
                                                             'Si sospende quando si vuole', 'Spedizione compresa'],
             'Cento capsule a casa ogni mese, la miscela che volete: si sospende con un clic.'),
    prodotto('RIS-012', 'Abbonamento Grani, un chilo al mese', 'abbonamento-grani', 'abbonamenti', 24, 0, 999, 4.8, 201, False,
             ['pexels-6079719.jpg', 'pexels-4820676.jpg'], ['Un chilo ogni mese', 'Tostato pochi giorni prima',
                                                             'Una miscela nuova ogni tanto', 'Spedizione compresa'],
             'Un chilo di grani tostati pochi giorni prima, ogni mese.'),
    prodotto('RIS-013', 'Macchina Lucida, espresso a leva', 'macchina-lucida', 'macchine', 389, 329, 6, 4.9, 77, True,
             ['pexels-6032786.jpg', 'pexels-6032787.jpg', 'pexels-29611242.jpg'], ['Acciaio lucido', 'A leva, a mano',
                                                                                   'Caldaia da un litro', 'Il video della leva'],
             'La macchina a leva per chi vuole l’espresso del bar a casa: si impara in una settimana.', video=V_FILTRO),
    prodotto('RIS-014', 'Macchina Gialla, per capsule', 'macchina-gialla', 'macchine', 149, 0, 10, 4.5, 120, False,
             ['pexels-13696018.jpg'], ['Per le nostre capsule', 'Pronta in 25 secondi', 'Serbatoio da un litro',
                                       'Gialla'], 'La macchina a capsule piccola e gialla: pronta in venticinque secondi.'),
    prodotto('RIS-015', 'Tazzine Vesuvio, set di sei', 'tazzine-vesuvio', 'tazzine', 36, 0, 25, 4.8, 290, False,
             ['pexels-4195607.jpg', 'pexels-4195322.jpg', 'pexels-4195608.jpg'], ['Ceramica di Vietri', 'Parete spessa',
                                                                                   'Tengono il caldo', 'Sei colori'],
             'Le tazzine spesse di Vietri: tengono il caffè caldo fino all’ultimo sorso.'),
    prodotto('RIS-016', 'Tazze da cappuccino, set di due', 'tazze-cappuccino', 'tazzine', 28, 0, 20, 4.7, 133, False,
             ['pexels-12620633.jpg', 'pexels-459489.jpg', 'pexels-15447295.jpg'], ['Ceramica smaltata', '180 ml',
                                                                                    'Per il latte art', 'Col piattino'],
             'Le tazze larghe per il cappuccino e per disegnare col latte.'),
    prodotto('RIS-017', 'Scatola Napoli, tre miscele e due tazzine', 'scatola-napoli', 'regali', 49, 39, 15, 4.9, 258, True,
             ['pexels-5994300.jpg', 'pexels-19791005.jpg'], ['Tre miscele in capsule', 'Due tazzine di Vietri',
                                                              'Il biglietto scritto a mano', 'Nella scatola col nastro'],
             'Il regalo per chi beve caffè: tre miscele, due tazzine, il biglietto scritto a mano.'),
    prodotto('RIS-018', 'Kit Colazione, capsule e biscotti', 'kit-colazione', 'regali', 35, 0, 18, 4.6, 87, False,
             ['pexels-27246443.jpg', 'pexels-34800842.jpg'], ['Cinquanta capsule Cremoso', 'Biscotti al burro',
                                                               'Due tazze', 'Nella scatola regalo'],
             'La colazione da regalare: le capsule Cremoso, i biscotti al burro, due tazze.'),
]
RECENSIONI = [[5, 'Finalmente un caffè in capsula che sa di caffè. La Intenso è il bar di casa.'],
              [5, 'Arrivato in due giorni, tostato da una settimana: si sente.'],
              [4, 'Buonissimo, le capsule a volte si incastrano un po’ nella mia macchina.'],
              [5, 'L’abbonamento è comodissimo, l’ho sospeso ad agosto senza problemi.'],
              [5, 'La scatola Napoli regalata a mio padre: commosso.'],
              [4, 'Ottimo, la Ristretto è davvero forte: va presa sul serio.'],
              [5, 'Le tazzine di Vietri sono bellissime e tengono il caldo.'],
              [3, 'Il cold brew è buono ma per me un po’ leggero.']]
NOMI = ['Gennaro E.', 'Carmela R.', 'Salvatore P.', 'Rosaria M.', 'Ciro L.', 'Assunta D.', 'Pasquale F.', 'Titti C.',
        'Mariano B.', 'Lucia V.', 'Antonio S.', 'Teresa G.']
PROMESSE = [('truck', 'Spedizione gratuita da 39 €'), ('flame', 'Tostato a legna ogni settimana'),
            ('leaf', 'Capsule compostabili'), ('repeat', 'Abbonamento: si sospende quando si vuole')]
CONTATTI = [('map-pin', 'Torrefazione · via dei Tribunali 77, Napoli', P['torrefazione'] + '#bottega'),
            ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'Ristretto', icona='coffee', ricerca='Cerca una miscela, una capsula, una tazzina…',
                     parole='un caffè intenso\nle capsule decaffeinate\nla moka napoletana\nun regalo per chi ama il caffè',
                     annuncio=('Settimana del caffè: fino al −21% sulle capsule, la spedizione è gratis da 39 €.',
                               'Le offerte', P['offerte']), logo_size=32)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'Ristretto', 'coffee', 'Una torrefazione in via dei Tribunali, a Napoli: tostiamo a legna '
                   'ogni settimana e mettiamo il caffè nelle capsule compostabili, in grani e nelle buste per la moka.',
                   [('Il caffè', [('Capsule', N.cat_url('capsule')), ('Grani e macinato', N.cat_url('grani')),
                                  ('Abbonamenti', N.cat_url('abbonamenti')), ('Offerte', P['offerte'])]),
                    ('Ristretto', [('La torrefazione', P['torrefazione']), ('La scala dell’intensità', P['intensita']),
                                   ('I preferiti', P['preferiti']), ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni', P['assistenza'] + '#spedizioni'),
                               ('Le capsule e la mia macchina', P['intensita'] + '#macchine'),
                               ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Ristretto s.r.l. · Napoli · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera della torrefazione', 'Una miscela nuova, ogni tanto.',
                              'Una lettera al mese: cosa abbiamo tostato, da dove arriva, come prepararlo.',
                              'Fatto: la prima lettera arriva con un codice per le capsule.'),
                   social=[('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.5  # la larghezza media di una lettera dei titoli (Young Serif, per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-8)
    return N.vetrina(t, [
        N.offerta('ri-o1', K.sfondo_video(V_GOCCIA, overlay=0.12), 'Settimana del caffè · fino a domenica',
                  'Le capsule Intenso a 26 €.', 'Cento capsule compostabili, la miscela del bar sotto casa. Erano 32 €.',
                  'Le capsule', N.cat_url('capsule'), bollino='−19%', bollino_x=70, nota='Spedizione gratuita da 39 €',
                  primo=True, **f),
        N.offerta('ri-o2', K.sfondo_foto('pexels-5994300.jpg', overlay=0.12, scala=1.06), 'Regali · per Natale',
                  'La scatola Napoli a 39 €.', 'Tre miscele, due tazzine di Vietri, il biglietto scritto a mano. Era 49 €.',
                  'I regali', N.cat_url('regali'), bollino='−20%', nota='Spedita nel giorno che scegliete', **f),
        N.offerta('ri-o3', K.sfondo_video(V_FILTRO, overlay=0.12), 'Macchine · il bar a casa',
                  'La macchina a leva a 329 €.', 'Acciaio lucido, caldaia da un litro: l’espresso del bar. Era 389 €.',
                  'Le macchine', N.cat_url('macchine'), bollino='−15%', bollino_x=72, nota='Due anni di garanzia', **f),
    ], transizione='zoomFade')


# le schede sotto la vetrina: «reparti» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='reparti', reparti='I reparti', voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Le capsule', {'category': 'capsule', 'orderby': 'popularity'}, 'Vedi tutte', N.cat_url('capsule')),
        ('Sotto i 25 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=25'),
        ('I più amati', {'orderby': 'rating'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Il caffè', 'In capsula, in grani, in tazzina',
                              ('La miscela Tribunali,', 'tostata a legna.', 'In grani, per espresso o per moka.', 'Grani e macinato',
                               N.cat_url('grani'), 'pexels-19162213.jpg'), tutto='Tutto il caffè')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Settimana del caffè', 'Fino al −21%.',
                              'Quelle di oggi finiscono tra:',
                              ('Le capsule Ristretto,', '27 €.', 'Cento pezzi, intensità 13. Erano 34 €.', 'Le capsule',
                               N.cat_url('capsule'), 'pexels-12265177.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Appena tostato',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('L’abbonamento,', 'si sospende quando si vuole.', 'Cento capsule al mese, spedizione compresa.',
                              'Gli abbonamenti', N.cat_url('abbonamenti'), 'pexels-8015467.jpg'), tutte='Tutte le novità')


# ── La scala dell'intensità: lo slider resta fermo, le capsule si accendono, la tacca dice il gusto ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 980.0
GUSTI = [(4, 'Dolce', 'Sidamo · 19 €'), (8, 'Cremoso', 'Cremoso · 30 €'), (11, 'Intenso', 'Intenso · 26 €'),
         (13, 'Ristretto', 'Ristretto · 27 €')]
ARRIVI = [('Capsule Cremoso', 'pexels-21033142.jpg', '30 €', 'capsule'), ('Capsule Intenso', 'pexels-32297816.jpg', '26 €',
                                                                          'capsule'),
          ('Capsule Ristretto', 'pexels-12265177.jpg', '27 €', 'capsule')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def scala(t):
    m = K.mob
    L = [
        K.livello('sc-o', contenuto='La scala dell’intensità · scorrete', x=6, y=10, w=44, size=13, peso='500', colore=CREMA,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.6, 'width': 86}}),
        K.livello('sc-t', contenuto='Dal dolce<br>al ristretto.', tag='h2', x=6, y=14, w=44, size=70, peso='400',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(36), 'x': 7, 'y': 4.6, 'width': 86}}),
        K.livello('sc-s', contenuto='Tredici capsule, dalla più chiara alla più scura: scorrete e la tacca vi dice il gusto.',
                  x=6, y=36, w=34, size=16, colore=mix(C['chiaro'], 82), lineHeight=1.6, ritardo=500,
                  responsive={'mobile': {'visible': False}}),
    ]
    # le tredici capsule: dalla crema all'espresso, si accendono una dopo l'altra
    x0, y0, passo, d = 100.0, 520.0, 72.0, 58.0
    xm0, ym0, passom, dm = 46.0, 330.0, 44.0, 36.0
    for k in range(13):
        tinta = 'color-mix(in srgb, %s %d%%, %s)' % (CREMA, 100 - k * 8, C['nero'])
        accende = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 25, 's': 0.8}, {'p': 4 + 5 * k, 'o': 25, 's': 0.8},
                                                       {'p': 8 + 5 * k, 'o': 100, 's': 1}, {'p': 100, 'o': 100, 's': 1}]}
        a, b = _pc(x0 + passo * k, y0, d, d, W, H), _pc(xm0 + passom * k, ym0, dm, dm, WM, HM)
        L.append(K.livello('sc-cap%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=tinta, borderRadius=999,
                           borderWidth=2, borderColor=mix(C['chiaro'], 30), entra='fadeIn', ritardo=0, scrollMove=accende,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0 + passo * k, y0 + d + 12, d, 22, W, H), _pc(xm0 + passom * k, ym0 + dm + 8, dm, 22, WM, HM)
        L.append(K.livello('sc-num%d' % k, contenuto=str(k + 1), x=a[0], y=a[1], w=a[2], size=14, peso='500',
                           colore=mix(C['chiaro'], 70), align='center', fontFamily=MONO, entra='fadeIn', ritardo=0,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(10)}}))
    # la tacca: scorre sopra le capsule fino alla 13; il gusto cambia nei quattro punti
    tacca = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 8, 'x': 0}, {'p': 70, 'x': passo * 12},
                                                 {'p': 100, 'x': passo * 12}]}
    a = _pc(x0 + d / 2 - 2, y0 - 46, 4, 36, W, H)
    L.append(K.livello('sc-tacca', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=CREMA, entra='fadeIn', ritardo=0,
                       scrollMove=tacca, responsive={'mobile': {'visible': False}}))
    for k, (n, gusto, miscela) in enumerate(GUSTI):
        pa = 8 + 62 * (n - 1) / 12
        pb = 8 + 62 * (GUSTI[k + 1][0] - 1) / 12 if k + 1 < len(GUSTI) else 101
        vis = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': max(0, pa - 4), 'o': 0}, {'p': pa, 'o': 100},
                                                   {'p': max(pa, pb - 4), 'o': 100}, {'p': min(100, pb), 'o': 0 if pb <= 100 else 100},
                                                   {'p': 100, 'o': 0 if pb <= 100 else 100}]}
        a, b = _pc(x0, y0 - 150, 640, 70, W, H), _pc(xm0, ym0 - 120, 560, 50, WM, HM)
        L.append(K.livello('sc-gusto%d' % k, contenuto='%d · %s' % (n, gusto), x=a[0], y=a[1], w=a[2], size=46, peso='400',
                           colore=CREMA, fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=vis,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(26)}}))
        a, b = _pc(x0, y0 - 84, 640, 26, W, H), _pc(xm0, ym0 - 70, 560, 26, WM, HM)
        L.append(K.livello('sc-misc%d' % k, contenuto=miscela, x=a[0], y=a[1], w=a[2], size=14, peso='500',
                           colore=mix(C['chiaro'], 80), letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                           entra='fadeIn', ritardo=0, scrollMove=vis,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(10)}}))
    # in fondo, le miscele col prezzo
    cw, ch, cwm, chm = 330.0, 118.0, 560.0, 112.0
    for k, (nome, foto, prezzo, cat) in enumerate(ARRIVI):
        y, ym = 120 + (ch + 18) * k, 520 + (chm + 16) * k
        entra = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 380, 'o': 0}, {'p': 64 + 10 * k, 'x': 380, 'o': 0},
                                                     {'p': 74 + 10 * k, 'x': 0, 'o': 100}, {'p': 100, 'x': 0, 'o': 100}]}
        x0c = W - 70 - cw
        a, b = _pc(x0c, y, cw, ch, W, H), _pc(40, ym, cwm, chm, WM, HM)
        L.append(K.livello('sc-scheda%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['chiaro'], borderRadius=12,
                           boxShadow={'x': 0, 'y': 20, 'blur': 44, 'spread': -16, 'color': mix(C['nero'], 70)}, entra='fadeIn',
                           ritardo=0, scrollMove=entra, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0c + 10, y + 10, 98, 98, W, H), _pc(50, ym + 10, 92, 92, WM, HM)
        L.append(K.livello('sc-foto%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a[0], y=a[1], w=a[2], h=a[3],
                           borderRadius=8, entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0c + 124, y + 20, 196, 34, W, H), _pc(160, ym + 18, 420, 36, WM, HM)
        L.append(K.livello('sc-nome%d' % k, contenuto=nome, x=a[0], y=a[1], w=a[2], size=22, peso='400', colore=C['testo'],
                           fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(17)}}))
        a, b = _pc(x0c + 124, y + 66, 160, 32, W, H), _pc(160, ym + 62, 260, 32, WM, HM)
        L.append(K.livello('sc-prezzo%d' % k, tipo='button', contenuto='%s · vedi' % prezzo, x=a[0], y=a[1], w='auto', size=12,
                           peso='500', colore=C['on_acc'], bgColor=C['acc'], padding=8, borderRadius=8, fontFamily=MONO,
                           entra='fadeIn', ritardo=0, buttonUrl=N.cat_url(cat), hover={'bgColor': CREMA, 'color': C['nero'],
                                                                                       'duration': 200},
                           scrollMove=entra, responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(10)}}))
    sl = [K.diapositiva('sc-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, L, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1900, scrollStep=False, globalBackground={'type': 'color', 'color': C['nero']},
                       heightMobile={'mode': 'px', 'value': int(HM)})


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small',
                         style={'margin_top': 24}))
    out.append(t.section([
        N.testata_sezione(t, 'Il caffè', 'Come lo bevete?', P['negozio'], 'Tutto il caffè'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, scala(t)))
    out.append(t.section([
        N.testata_sezione(t, 'Appena tostati', 'Usciti dal tostino questa settimana', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='date', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'L’abbonamento,', 'senza pensieri.', 'Cento capsule al mese: si sospende quando si vuole.',
                               'Gli abbonamenti', N.cat_url('abbonamenti'), K.photo_bg('pexels-9594423.jpg'), size=36)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'La capsula d’acciaio,', 'zero rifiuti.', 'Si riempie col nostro macinato per espresso.',
                               'La capsula ricaricabile', N.cat_url('capsule'), K.photo_bg('pexels-7243711.jpg'), size=36)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Settimana del caffè', color=CREMA),
                      N.titolo(t, 'Finisce domenica.', size=46, color=C['chiaro']),
                      N.testo(t, 'Fino al −21% su capsule, macinato, macchine e regali.', color=mix(C['chiaro'], 82))],
                     wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42, giorni=True), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left',
                                                              pad_top=16)], wm='1-2', ws='1-1')],
              layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_CHICCHI, 'La torrefazione', 'Tostato a legna, <em>ogni martedì.</em>',
                             'In via dei Tribunali, dietro il banco: il tostino a legna, i sacchi di juta, il caffè che si '
                             'raffredda girando. Parte il mercoledì.', overlay=40,
                             extra=[N.pulsante(t, 'La torrefazione', P['torrefazione'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'I più amati', 'Quelli che si ricomprano', P['negozio']),
        t.one([N.prodotti(t, orderby='rating', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-4195607.jpg', 'Due tazzine di caffè viste dall’alto', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'La vostra macchina'), N.titolo(t, 'Le capsule entrano?<br>Ve lo diciamo noi.', size=50),
                      N.testo(t, 'Le nostre capsule sono compatibili con le macchine a capsula più diffuse. Nella guida trovate '
                                 'come controllare la vostra; se non va, la prima scatola la riprendiamo noi.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Capsule compostabili, nell’umido', 'La prima scatola si restituisce se non va',
                              'Tostato da meno di due settimane']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'La guida', P['intensita'] + '#macchine', pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'La bottega', 'Tutto il caffè', 'Capsule compostabili, grani e macinato, abbonamenti, macchine, '
                            'tazzine e regali: tostato a legna ogni settimana.', colonne=3,
                            banner=N.banner(t, 'Un regalo?', 'La scatola Napoli.', 'Tre miscele, due tazzine, il biglietto.',
                                            'I regali', N.cat_url('regali'), K.photo_bg('pexels-19791005.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'La moka napoletana,', '7,50 €.', 'Arabica, macinata per moka. Era 9,50 €.', 'Grani e macinato',
                        N.cat_url('grani'), K.photo_bg('pexels-14792388.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'La capsula d’acciaio,', '24 €.', 'Due pezzi, zero rifiuti. Era 29 €.', 'Le capsule',
                        N.cat_url('capsule'), K.photo_bg('pexels-7243717.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Settimana del caffè', 'Fino al −21%, fino a domenica', 'Capsule, macinato, la macchina a leva '
                            'e la scatola Napoli a meno, finché c’è il tostato della settimana.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Spedizione gratuita da 39 €'), ('flame', 'Tostato a legna'),
                                                   ('leaf', 'Capsule compostabili'), ('repeat', 'Anche in abbonamento')],
                             bundle='Con questo, in tazzina', miniature='left', altezza='600px', bottone='Metti nel carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 39 €. Parte il mercoledì, tostato il martedì.',
                             PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna, il pagamento. Per un regalo, il biglietto nelle note.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, l’abbonamento da sospendere o cambiare, gli indirizzi.',
                            ('L’abbonamento?', 'Si cambia la miscela o si sospende dal vostro account, quando volete.',
                             'Gli abbonamenti', N.cat_url('abbonamenti')))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto del caffè.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'In torrefazione dal lunedì al sabato, 7–20; al telefono e in chat.', [
        ('truck', 'Spedizioni', 'Gratis da 39 €; partono il mercoledì.', 'spedizioni'),
        ('repeat', 'Abbonamenti', 'Si cambia, si sospende, si chiude in un clic.', 'scrivici'),
        ('coffee', 'Le capsule', 'Compatibili con le macchine più diffuse.', 'scrivici'),
        ('gift', 'Regali', 'Il biglietto a mano, la consegna nel giorno scelto.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 39 €; sotto, 4,90 €. Il caffè parte il mercoledì e arriva in due '
                                        'giorni.'),
        ('Le capsule vanno con la mia macchina?', 'Sono compatibili con le macchine a capsula più diffuse: nella guida c’è '
                                                  'come controllarlo. Se non vanno, la prima scatola la riprendiamo.'),
        ('Come sospendo l’abbonamento?', 'Dal vostro account, con un clic: riparte quando volete.'),
        ('Le capsule si buttano nell’umido?', 'Sì, sono compostabili: capsula e caffè insieme, nell’organico.')], CONTATTI,
        campi_extra='Un abbonamento\nLe capsule e la mia macchina\n',
        foto=('pexels-35411022.jpg', 'Il banco della torrefazione con la macchina dell’espresso'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa tazzina <em>è vuota.</em>', 'La pagina non c’è più. Cercate il caffè qui sopra.',
                        K.video_bg(V_VAPORE, overlay=40))


def build_torrefazione():
    t = Tpl(ID, 'torrefazione')
    out = [K.intro(t, 'La torrefazione', ['Tostato a legna,', 'ogni martedì.'], 'In via dei Tribunali dal 1951: il '
                   'tostino a legna, i sacchi di juta, il caffè che si raffredda girando.',
                   bg=K.video_bg(V_MUCCHIO, overlay=40), size=86)]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Da dove arriva'), N.titolo(t, 'Dalle ciliegie<br>alla tazzina.', size=46),
                      N.testo(t, 'Compriamo il caffè verde da sei piantagioni, in Etiopia, Brasile, India e Guatemala: le '
                                 'ciliegie si raccolgono a mano quando sono rosse, si lavano, si seccano al sole.', size=17)],
                     wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-7791207.jpg', 'Le ciliegie del caffè rosse e gialle sul ramo', ratio='4/3')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 64}))
    out.append(t.section([
        t.one([K.numeri(t, [('1951', 'la torrefazione apre'), ('6', 'piantagioni'), ('14 min', 'di tostatura'),
                            ('2 settimane', 'al massimo dal tostino a voi')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    out.append(K.video_frase(t, V_BICCHIERINO, 'In tazzina', 'Venticinque secondi, <em>venticinque millilitri.</em>',
                             'L’espresso giusto: la crema nocciola, il corpo pieno, niente amaro bruciato.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-7175982.jpg', 'Il torrefattore sorride accanto al tostino', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Il torrefattore'), N.titolo(t, 'Quattordici minuti,<br>a orecchio.', size=46),
                      N.testo(t, 'Il tostino non ha il timer: si ascolta il primo crack, poi il secondo, e si apre. Ogni '
                                 'miscela ha il suo punto, scritto a mano su un quaderno del 1951.', size=17)],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], html_id='bottega', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    out.append(t.section([
        N.testata_sezione(t, 'Tostati qui', 'Le miscele della bottega', P['negozio']),
        t.one([N.prodotti(t, featured=True, posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_intensita():
    t = Tpl(ID, 'intensita')
    out = [N.intestazione(t, 'La guida', 'La scala dell’intensità', 'Da 1 a 13: quanto è carico il caffè, dal dolce al '
                          'ristretto. E come controllare se le capsule vanno nella vostra macchina.')]
    righe = [['Intensità', 'Gusto', 'Miscela', 'Si beve']]
    righe += [['4', 'Dolce, fiorito', 'Sidamo', 'Lungo, senza zucchero'], ['6', 'Rotondo', 'Dek', 'La sera'],
              ['8', 'Cremoso', 'Cremoso', 'Col latte'], ['11', 'Intenso', 'Intenso', 'Al banco'],
              ['13', 'Ristretto', 'Ristretto', 'In due sorsi']]
    tabella = t.classic('table', {
        'table_data': righe, 'has_header': True, 'striped': True, 'bordered': True,
        'hover_effect': True, 'compact': False, 'first_col_bold': True, 'col_alignments': ['center', 'left', 'left', 'left'],
        'responsive_mode': 'scroll', 'header_bg': C['nero'], 'header_text_color': C['chiaro'], 'text_color': C['testo'],
        'border_color': C['filo'], 'even_row_bg': C['sala']})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La tabella'), N.titolo(t, 'Quale fa per voi?', size=46),
                      N.testo(t, 'Il numero dice il corpo e l’amaro, non la caffeina: la Sidamo, arabica, ne ha meno della '
                                 'Ristretto, che è robusta.', size=17)], wm='2-5', ws='1-1'),
               t.col([tabella], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='start'),
    ], padding='large', width='large', style={'padding_top': 72, 'padding_bottom': 64}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La vostra macchina'), N.titolo(t, 'Le capsule entrano?', size=40),
                      K.faq(t, [('Come controllo?', 'Guardate la capsula originale della vostra macchina: se è a tronco di '
                                                    'cono, alta un dito e mezzo, le nostre vanno. Nel dubbio scriveteci il '
                                                    'modello.'),
                                ('E se non vanno?', 'La prima scatola la riprendiamo noi, gratis, e vi rimborsiamo.'),
                                ('Si possono usare nelle macchine a cialda?', 'No: per quelle ci sono le cialde di carta, '
                                                                              'chiedetecele.')], 'mac')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-18071820.jpg', 'Le capsule riposte accanto alla macchina', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='macchine', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        N.testata_sezione(t, 'Le capsule', 'Tutte le miscele', N.cat_url('capsule')),
        t.one([N.prodotti(t, category='capsule', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'La scala dell’intensità',
    ['caffè', 'capsule', 'torrefazione', 'woocommerce', 'e-commerce', 'abbonamento', 'napoli', 'regali'],
    'Il negozio online di una torrefazione napoletana: in apertura la fascia delle offerte che scorre e le quattro schede coi '
    'prodotti, il mega menu col caffè, le offerte e le novità; a metà pagina la scala dell’intensità, con le tredici capsule '
    'che si accendono e la tacca che dice il gusto; la torrefazione, la guida alle capsule e alla macchina, la scheda prodotto '
    'con la macinatura e il video dell’estrazione.',
    [('Il caffè', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Capsule', '{{PAGINA:negozio}}?product_cat=capsule'), ('Grani', '{{PAGINA:negozio}}?product_cat=grani'),
     ('Abbonamenti', '{{PAGINA:negozio}}?product_cat=abbonamenti'), ('La torrefazione', P['torrefazione']),
     ('L’intensità', P['intensita'])],
    [('home', 'Home', None), ('negozio', 'Il caffè', 'caffe'), ('torrefazione', 'La torrefazione', 'torrefazione'),
     ('intensita', 'La scala dell’intensità', 'intensita'), ('offerte', 'Settimana del caffè', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Inter Tight\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Young Serif\', Georgia, serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Young Serif', 'Inter Tight', 'JetBrains Mono'], corsivo_di=[],
    peso_titoli='400', raggio=12, grana=0, titoli_line='1.04', titoli_spazio='-0.01em')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Il caffè': ('pannello-reparti', 'Pannello del caffè'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'chicco'
THEME['cursor']['ring_color'] = HEX['secondary']  # il colore del cursore (sul fondo chiaro il chiaro non si vede)


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'torrefazione.json': build_torrefazione(),
                        'intensita.json': build_intensita(), 'offerte.json': build_offerte(),
                        'assistenza.json': build_assistenza(), 'preferiti.json': build_preferiti(),
                        'carrello.json': build_carrello(), 'cassa.json': build_cassa(), 'account.json': build_account(),
                        'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'macinatura': {'nome': 'Macinatura', 'termini': MACINATURE}},
                    dentro='Il caffè nella busta con la valvola o nella scatola delle capsule, il biglietto della tostatura.')


if __name__ == '__main__':
    main()
