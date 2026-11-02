# -*- coding: utf-8 -*-
"""Genera il tema «Madia» (id madia): la bottega online di una dispensa di Fidenza che vende i prodotti dei piccoli
produttori — pasta trafilata al bronzo, olio del Garda, Parmigiano di montagna, salumi, conserve, miele, caffè, vino —
e una box del mese in abbonamento.

Tema chiaro color farina: il verde oliva sui pulsanti, lo zafferano, il rosso pomodoro delle offerte. Titoli in Fraunces
(col corsivo), testo in Karla, dati in IBM Plex Mono. Card col bordo sottile, foto quadrate che si avvicinano al passaggio.

Apertura: la vetrina delle offerte, come nelle botteghe online: una fascia bassa che scorre da sola (il
tagliere delle colline, l’olio e le conserve, la pasta di Gragnano), sotto le quattro schede coi prodotti veri; il mega
menu apre i pannelli della dispensa, delle novità e delle offerte. Cursore: la farina.

WooCommerce: la dispensa coi filtri, la scheda prodotto col video (l'olio che scende, il Parmigiano tagliato, il sugo,
la sfoglia) nella galleria, carrello, cassa, account, preferiti; la box regalo e l'abbonamento; i produttori.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'madia'
NAME = 'Madia'

HEX = {
    'primary': '#4E6130', 'primary_contrast': '#FFFFFF', 'secondary': '#E0A43A', 'secondary_contrast': '#2A1E10',
    'accent': '#B8321F', 'dark': '#1F1812', 'light': '#FFFBF4', 'muted': '#ECE1CF', 'muted_contrast': '#2A2018',
    'text': '#2A2018', 'text_muted': '#665848', 'background': '#F5EEE3', 'border': '#D9CBB4', 'link': '#4E6130',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'box', 'produttori', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'bottega@madia.example', '+39 0524 000 812', 'tel:+390524000812'

V_FARINA, V_OLIO, V_FORMAGGIO, V_SUGO, V_SFOGLIA, V_GRANO, V_FILO = (
    'pexels-video-6286098', 'pexels-video-14527415', 'pexels-video-6658177', 'pexels-video-19286023', 'pexels-video-26811243',
    'pexels-video-4073698', 'pexels-video-26620319')
VIDEOS = [V_FARINA, V_OLIO, V_FORMAGGIO, V_SUGO, V_SFOGLIA, V_GRANO, V_FILO]
PHOTOS = [
    'pexels-8964263.jpg', 'pexels-8963456.jpg', 'pexels-8964286.jpg', 'pexels-8964250.jpg', 'pexels-8964276.jpg',
    'pexels-8963420.jpg', 'pexels-4033404.jpg', 'pexels-8963413.jpg', 'pexels-4699965.jpg', 'pexels-9025046.jpg',
    'pexels-21911357.jpg', 'pexels-10548607.jpg', 'pexels-1611560.jpg', 'pexels-1414693.jpg', 'pexels-20927312.jpg',
    'pexels-533236.jpg', 'pexels-33984942.jpg', 'pexels-33984944.jpg', 'pexels-3693283.jpg', 'pexels-3693282.jpg',
    'pexels-6660175.jpg', 'pexels-6659624.jpg', 'pexels-4087609.jpg', 'pexels-34621129.jpg', 'pexels-6004718.jpg',
    'pexels-19585048.jpg', 'pexels-6004709.jpg', 'pexels-7175710.jpg', 'pexels-8621806.jpg', 'pexels-8964238.jpg',
    'pexels-8963387.jpg', 'pexels-34185861.jpg', 'pexels-34185860.jpg', 'pexels-7615874.jpg', 'pexels-12585560.jpg',
    'pexels-11505588.jpg', 'pexels-5714577.jpg', 'pexels-8108069.jpg', 'pexels-8108020.jpg', 'pexels-31694877.jpg',
    'pexels-10510951.jpg', 'pexels-29020603.jpg', 'pexels-34803729.jpg', 'pexels-33313084.jpg', 'pexels-40427744.jpg',
    'pexels-8747538.jpg', 'pexels-5732765.jpg', 'pexels-14515698.jpg', 'pexels-35855516.jpg', 'pexels-30832873.jpg',
    'pexels-8472737.jpg', 'pexels-3184191.jpg', 'pexels-3938970.jpg', 'pexels-1537635.jpg', 'pexels-4087615.jpg',
    'pexels-38179370.jpg', 'pexels-38164887.jpg', 'pexels-8580727.jpg',
]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='500', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
ZAFFERANO, POMODORO = C['sec'], C['acc2']
TITOLI, MONO = 'Fraunces', 'IBM Plex Mono'
N = Negozio(K, NAME, P, raggio=6, ratio='1-1', card='border', hover='zoom', testata_scura=False,
            consegna='Spedizione refrigerata da 59 €', evidenza='Scelto in bottega', peso_titoli='500')

CATEGORIE = [
    ('pasta-e-riso', 'Pasta e riso', 'Trafilata al bronzo, all’uovo, il Carnaroli e i cereali antichi.', 'pexels-8964263.jpg'),
    ('olio-e-conserve', 'Olio e conserve', 'L’olio del Garda, la passata, i sott’oli della nonna.', 'pexels-9025046.jpg'),
    ('formaggi-e-salumi', 'Formaggi e salumi', 'Parmigiano di montagna, pecorino, il salame di Felino.', 'pexels-3693283.jpg'),
    ('dolci-e-caffe', 'Dolci e caffè', 'Cantucci, miele di montagna, caffè tostato a legna.', 'pexels-8621806.jpg'),
    ('vini', 'Vini', 'I vini dei colli, da piccole cantine.', 'pexels-8472737.jpg'),
    ('box-regalo', 'Box e regali', 'La box del mese, i taglieri, i regali già pronti.', 'pexels-4087609.jpg'),
]
PRODOTTI = [
    prodotto('MAD-001', 'Tagliatelle all’uovo, 500 g', 'tagliatelle-uovo', 'pasta-e-riso', 6.5, 0, 80, 4.8, 512, True,
             ['pexels-8964263.jpg', 'pexels-8963456.jpg', 'pexels-8964286.jpg'], ['Uova di galline allevate a terra',
             'Tirata a mano, essiccata lentamente', 'Cuoce in tre minuti', 'Il video della sfoglia nella galleria'],
             'La sfoglia di Rosa, a Collecchio: uova, farina e mattarello. Guardatela passare nella macchina, nel video.',
             video=V_SFOGLIA, cross=['MAD-010', 'MAD-005']),
    prodotto('MAD-002', 'Ruote tricolori, 500 g', 'ruote-tricolori', 'pasta-e-riso', 4.9, 0, 90, 4.5, 188, False,
             ['pexels-8964250.jpg', 'pexels-8964276.jpg', 'pexels-8963420.jpg'], ['Colorate con spinaci e pomodoro',
             'Trafilate al bronzo', 'Piacciono ai bambini', 'Grano duro italiano'], 'Spinaci, pomodoro e grano duro: le ruote '
             'che fanno mangiare la pasta anche ai bambini.', cross=['MAD-009']),
    prodotto('MAD-003', 'Spaghetti di Gragnano, 1 kg', 'spaghetti-gragnano', 'pasta-e-riso', 7.9, 6.4, 120, 4.9, 1210, True,
             ['pexels-4033404.jpg', 'pexels-8963413.jpg'], ['Trafilati al bronzo', 'Essiccati 48 ore', 'Pasta di Gragnano IGP',
             'Tengono la cottura'], 'Gli spaghetti ruvidi che tengono il sugo: trafilati al bronzo, essiccati due giorni.',
             cross=['MAD-009', 'MAD-005']),
    prodotto('MAD-004', 'Riso Carnaroli e cereali antichi', 'riso-carnaroli-cereali', 'pasta-e-riso', 8.5, 0, 45, 4.6, 166,
             False, ['pexels-8108069.jpg', 'pexels-8108020.jpg'], ['Carnaroli invecchiato un anno', 'Farro e orzo perlato',
             'Riso venere', 'Quattro sacchetti da 250 g'], 'Il Carnaroli per il risotto e tre cereali per le zuppe d’inverno.'),
    prodotto('MAD-005', 'Olio extravergine del Garda, 500 ml', 'olio-extravergine-garda', 'olio-e-conserve', 19, 0, 50, 4.9,
             844, True, ['pexels-9025046.jpg', 'pexels-21911357.jpg', 'pexels-10548607.jpg'], ['Casaliva in purezza',
             'Raccolto a ottobre, frantoio in giornata', 'Fruttato leggero', 'Il video dell’olio nella galleria'],
             'L’olio del Garda di Luca, a Malcesine: fruttato leggero, che sa di mandorla.', video=V_OLIO,
             cross=['MAD-003', 'MAD-007']),
    prodotto('MAD-006', 'Olive taggiasche in salamoia', 'olive-taggiasche', 'olio-e-conserve', 9.5, 0, 60, 4.7, 233, False,
             ['pexels-20927312.jpg'], ['Raccolte a mano in Liguria', 'Salamoia leggera', 'Vaso da 280 g', 'Senza conservanti'],
             'Le olive piccole e dolci della Liguria: per l’aperitivo e per la pasta.'),
    prodotto('MAD-007', 'Melanzane sott’olio', 'melanzane-sottolio', 'olio-e-conserve', 8.9, 0, 40, 4.6, 120, False,
             ['pexels-1611560.jpg', 'pexels-1414693.jpg'], ['Melanzane dell’orto, a fette', 'Olio extravergine', 'Aglio e menta',
             'Vaso da 300 g'], 'Come le faceva la nonna: a fette, con la menta, sotto l’olio buono.'),
    prodotto('MAD-008', 'Pomodori secchi sott’olio', 'pomodori-secchi', 'olio-e-conserve', 7.5, 5.9, 55, 4.5, 98, False,
             ['pexels-533236.jpg'], ['Pomodori pugliesi seccati al sole', 'Capperi e origano', 'Vaso da 280 g', 'In offerta'],
             'I pomodori seccati al sole di Puglia, con capperi e origano.'),
    prodotto('MAD-009', 'Passata di San Marzano', 'passata-san-marzano', 'olio-e-conserve', 4.9, 0, 150, 4.8, 970, False,
             ['pexels-33984942.jpg', 'pexels-33984944.jpg'], ['Pomodoro San Marzano DOP', 'Solo pomodoro e sale',
             'Bottiglia da 700 g', 'Il video del sugo nella galleria'], 'Solo pomodoro e un pizzico di sale: il sugo è già fatto '
             'a metà. Guardatelo bollire nel video.', video=V_SUGO, cross=['MAD-003', 'MAD-005']),
    prodotto('MAD-010', 'Parmigiano di montagna 30 mesi, 1 kg', 'parmigiano-montagna-30-mesi', 'formaggi-e-salumi', 34, 0, 30,
             4.9, 1488, True, ['pexels-3693283.jpg', 'pexels-3693282.jpg', 'pexels-6660175.jpg'],
             ['Latte di vacche rosse dell’Appennino', 'Trenta mesi di stagionatura', 'Spedito sottovuoto, refrigerato',
             'Il video del taglio nella galleria'], 'Il Parmigiano del caseificio di Corniglio, a 700 metri: trenta mesi, '
                                                    'cristalli che scrocchiano. Guardate il taglio nel video.',
             video=V_FORMAGGIO, upsell=['MAD-013'], cross=['MAD-012', 'MAD-018']),
    prodotto('MAD-011', 'Pecorino stagionato', 'pecorino-stagionato', 'formaggi-e-salumi', 22, 0, 26, 4.6, 210, False,
             ['pexels-8747538.jpg', 'pexels-5732765.jpg'], ['Latte di pecora crudo', 'Dodici mesi in grotta', 'Forma da 900 g',
             'Crosta trattata con olio'], 'Il pecorino di grotta, piccante quanto basta, da mangiare con il miele.',
             cross=['MAD-016']),
    prodotto('MAD-012', 'Salame di Felino', 'salame-felino', 'formaggi-e-salumi', 16, 0, 40, 4.7, 432, False,
             ['pexels-6004718.jpg', 'pexels-19585048.jpg', 'pexels-6004709.jpg'], ['Carne di suino nazionale',
             'Insaccato nel budello naturale', 'Stagionato sessanta giorni', 'Circa 500 g'],
             'Il salame dolce della Bassa, tagliato a fetta obliqua come si fa a Felino.', cross=['MAD-010', 'MAD-018']),
    prodotto('MAD-013', 'Il tagliere delle colline', 'tagliere-colline', 'box-regalo', 59, 49, 18, 4.8, 156, True,
             ['pexels-4087609.jpg', 'pexels-7175710.jpg', 'pexels-34621129.jpg'], ['Parmigiano, pecorino, salame di Felino',
             'Miele millefiori e cantucci', 'Tagliere in legno di faggio', 'Pronto da regalare'],
             'Il tagliere già fatto, col legno da tenere: un regalo che si apre a tavola.', cross=['MAD-018']),
    prodotto('MAD-014', 'Cantucci alle mandorle', 'cantucci-mandorle', 'dolci-e-caffe', 8.5, 0, 70, 4.7, 610, False,
             ['pexels-8621806.jpg', 'pexels-8964238.jpg', 'pexels-8963387.jpg'], ['Mandorle intere di Sicilia',
             'Cotti due volte', 'Sacchetto da 300 g', 'Col vin santo, o col caffè'], 'Croccanti, con le mandorle intere: '
                                                                               'cotti due volte, come a Prato.',
             cross=['MAD-017']),
    prodotto('MAD-015', 'Miele millefiori di montagna', 'miele-millefiori-montagna', 'dolci-e-caffe', 11, 0, 44, 4.8, 288,
             False, ['pexels-34185861.jpg', 'pexels-34185860.jpg'], ['Arnie sopra i mille metri', 'Non pastorizzato',
             'Vaso da 500 g', 'Cristallizza: è normale'], 'Il miele delle arnie di Bardi, sopra i mille metri.',
             cross=['MAD-011']),
    prodotto('MAD-016', 'Miele di castagno', 'miele-castagno', 'dolci-e-caffe', 12, 0, 38, 4.6, 140, False,
             ['pexels-7615874.jpg'], ['Scuro e amarognolo', 'Col pecorino e coi formaggi stagionati', 'Vaso da 500 g',
             'Non pastorizzato'], 'Scuro, amarognolo, buonissimo col pecorino.'),
    prodotto('MAD-017', 'Caffè tostato a legna, 500 g', 'caffe-tostato-legna', 'dolci-e-caffe', 16, 13, 60, 4.7, 532, False,
             ['pexels-12585560.jpg', 'pexels-11505588.jpg', 'pexels-5714577.jpg'], ['Miscela 80% arabica',
             'Tostato a legna a Fidenza', 'In grani o macinato per moka', 'Sacchetto con la valvola'],
             'La miscela della torrefazione di Fidenza, tostata nel forno a legna.'),
    prodotto('MAD-018', 'Bianco dei colli, 75 cl', 'bianco-dei-colli', 'vini', 14, 0, 36, 4.5, 120, False,
             ['pexels-8472737.jpg'], ['Malvasia di Candia', 'Fresco, profumato', 'Col salame e coi tortelli', 'Piccola cantina'],
             'La Malvasia dei colli di Parma: fresca, profumata, da salame e tortelli.', cross=['MAD-012']),
    prodotto('MAD-019', 'La Madia del mese, abbonamento', 'madia-del-mese', 'box-regalo', 39, 0, 200, 4.9, 640, True,
             ['pexels-8580727.jpg', 'pexels-38179370.jpg', 'pexels-38164887.jpg'], ['Otto prodotti scelti ogni mese',
             'Una ricetta scritta dalla bottega', 'Consegna il primo giovedì', 'Si sospende quando volete'],
             'Ogni mese una scatola con otto prodotti dei nostri produttori e una ricetta per usarli.'),
]
RECENSIONI = [[5, 'Arrivato refrigerato e imballato benissimo. Il Parmigiano è spettacolare.'],
              [5, 'Si sente che sono prodotti veri. Ho già riordinato la pasta.'],
              [4, 'Tutto buonissimo, la spedizione refrigerata costa un po’ sotto i 59 €.'],
              [5, 'Regalata la box a mia madre: la ricetta allegata è stata la sorpresa più bella.'],
              [5, 'L’olio del Garda è il migliore che abbia comprato online.'],
              [4, 'Ottimi, i cantucci finiti in due giorni. Confezione da fare più grande.'],
              [5, 'Bottega gentilissima, mi hanno chiamato per una sostituzione.'],
              [5, 'Prezzi onesti per la qualità. Si capisce chi fa le cose.']]
NOMI = ['Giovanna R.', 'Paolo M.', 'Lucia B.', 'Stefano T.', 'Antonella P.', 'Roberto C.', 'Cristina L.', 'Matteo D.',
        'Franca G.', 'Alberto V.', 'Daniela S.', 'Enrico F.']
PROMESSE = [('truck', 'Spedizione refrigerata gratuita da 59 €'), ('snowflake', 'I freschi viaggiano a 4 gradi'),
            ('users', 'Trenta piccoli produttori, tutti conosciuti'), ('gift', 'Il biglietto scritto a mano nei regali')]
CONTATTI = [('map-pin', 'Bottega · via Berenini 12, Fidenza', P['produttori'] + '#bottega'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'Madia', icona='wheat', ricerca='Cerca la pasta, l’olio, un formaggio…',
                     parole='il Parmigiano 30 mesi\nla pasta all’uovo\nl’olio del Garda\nla box del mese',
                     annuncio=('La box di novembre: tartufo nero, castagne e pecorino.', 'Scopri la box', P['box']),
                     logo_size=32)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'Madia', 'wheat', 'La dispensa dei piccoli produttori: trenta famiglie fra la pianura e '
                   'l’Appennino, una bottega a Fidenza, le scatole che partono il giovedì.',
                   [('La dispensa', [('Tutti i prodotti', P['negozio']), ('Pasta e riso', N.cat_url('pasta-e-riso')),
                                     ('Formaggi e salumi', N.cat_url('formaggi-e-salumi')), ('Offerte', P['offerte'])]),
                    ('Madia', [('La box del mese', P['box']), ('I produttori', P['produttori']),
                               ('I preferiti', P['preferiti']), ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni refrigerate', P['assistenza'] + '#spedizioni'),
                               ('Regali aziendali', P['assistenza'] + '#scrivici'), ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Madia s.r.l. · Fidenza · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera della bottega', 'Una ricetta, i prodotti nuovi.',
                              'Ogni giovedì: cosa è arrivato in bottega e come cucinarlo.',
                              'Fatto: la prima lettera arriva giovedì, con la ricetta.'),
                   social=[('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')])


# ── L'apertura: la scatola che si apre ──
# (Dal 1° nov 2026 la home apre con la vetrina delle offerte: questa apertura non è più usata.)
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
SCALA = 0.444   # gli spostamenti in px della tela del computer, sul telefono (la tela è larga 640 e si vede a ~390)
SCATOLA = (1040.0, 560.0)          # centro della scatola (computer)
SCATOLA_M = (320.0, 1130.0)        # telefono
USCITE = [  # foto, nome, prezzo, categoria, centro d'arrivo (computer)
    ('pexels-8964263.jpg', 'Tagliatelle', '6,50 €', 'pasta-e-riso', (740.0, 340.0)),
    ('pexels-9025046.jpg', 'Olio', '19 €', 'olio-e-conserve', (890.0, 190.0)),
    ('pexels-3693283.jpg', 'Parmigiano', '34 €', 'formaggi-e-salumi', (1040.0, 110.0)),
    ('pexels-34185861.jpg', 'Miele', '11 €', 'dolci-e-caffe', (1190.0, 190.0)),
    ('pexels-12585560.jpg', 'Caffè', '13 €', 'dolci-e-caffe', (1340.0, 340.0)),
]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    kraft = mix(ZAFFERANO, 62, C['nero'])
    kraft2 = mix(ZAFFERANO, 46, C['nero'])
    livelli = [
        K.livello('md-velo', tipo='shape', x=0, y=0, w=58, h=100, entra='fadeIn', ritardo=0,
                  shapeGradient={'angle': 90, 'from': mix(C['nero'], 86), 'to': mix(C['nero'], 0), 'fromStop': 40},
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 44}}),
    ]
    d = 120.0
    dm = 104.0
    # i prodotti, disegnati prima della scatola: partono dentro e salgono ad arco
    for k, (foto, nome, prezzo, cat, (cx, cy)) in enumerate(USCITE):
        ox, oy = SCATOLA[0] - cx, SCATOLA[1] + 20 - cy
        cxm, cym = SCATOLA_M[0] - ox * SCALA, SCATOLA_M[1] + 20 * SCALA - oy * SCALA
        sale = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': ox, 'y': oy, 's': 0.55}, {'p': 30 + 4 * k, 'x': ox, 'y': oy, 's': 0.55},
                                                    {'p': 66 + 4 * k, 'x': 0, 'y': 0, 's': 1}, {'p': 100, 'x': 0, 'y': 0, 's': 1}]}
        a = _pc(cx - d / 2, cy - d / 2, d, d, W, H)
        b = _pc(cxm - dm / 2, cym - dm / 2, dm, dm, WM, HM)
        livelli.append(K.livello('md-p%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a[0], y=a[1], w=a[2],
                                 h=a[3], borderRadius=999, borderWidth=3, borderColor=C['chiaro'],
                                 boxShadow={'x': 0, 'y': 14, 'blur': 30, 'spread': -8, 'color': mix(C['nero'], 60)},
                                 entra='fadeIn', ritardo=100, durata=600, scrollMove=sale,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        compare = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 68 + 4 * k, 'o': 0}, {'p': 80 + 4 * k, 'o': 100},
                                                       {'p': 100, 'o': 100}]}
        a = _pc(cx - 80, cy + d / 2 + 6, 160, 28, W, H)
        b = _pc(cxm - 80, cym + dm / 2 + 6, 160, 30, WM, HM)
        livelli.append(K.livello('md-c%d' % k, tipo='button', contenuto='%s · %s' % (nome, prezzo), x=a[0], y=a[1], w='auto',
                                 size=12, peso='600', colore=C['nero'], bgColor=C['chiaro'], padding=7, borderRadius=999,
                                 fontFamily=MONO, entra='fadeIn', ritardo=0, buttonUrl=N.cat_url(cat),
                                 hover={'bgColor': ZAFFERANO, 'duration': 200}, scrollMove=compare,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(9)}}))
    # la scatola: il fondo col nastro e l'etichetta, poi il coperchio che si apre
    bx, by, bw, bh = SCATOLA[0] - 220, SCATOLA[1] - 90, 440.0, 220.0
    mx_, my_, mw, mh = SCATOLA_M[0] - 190, SCATOLA_M[1] - 80, 380.0, 190.0
    for nome, x, y, w, h, extra, xm2, ym2, wm2, hm2 in [
            ('fondo', bx, by, bw, bh, dict(shapeGradient={'angle': 180, 'from': kraft, 'to': kraft2}, borderRadius=6),
             mx_, my_, mw, mh),
            ('nastro', SCATOLA[0] - 16, by, 32, bh, dict(bgColor=POMODORO), SCATOLA_M[0] - 14, my_, 28, mh)]:
        a = _pc(x, y, w, h, W, H)
        b = _pc(xm2, ym2, wm2, hm2, WM, HM)
        livelli.append(K.livello('md-' + nome, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], entra='fadeInUp', ritardo=150,
                                 durata=900, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}},
                                 **extra))
    a = _pc(bx, by + 120, bw, 40, W, H)
    b = _pc(mx_, my_ + 104, mw, 36, WM, HM)
    livelli.append(K.livello('md-etichetta', contenuto='MADIA · la box del mese', x=a[0], y=a[1], w=a[2], size=13, peso='600',
                             colore=C['chiaro'], align='center', fontFamily=MONO, letterSpacing=3, textTransform='uppercase',
                             entra='fadeIn', ritardo=400, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2],
                                                                                 'fontSize': m(10)}}))
    apre = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0, 'rx': 0, 'o': 100}, {'p': 6, 'y': 0, 'rx': 0, 'o': 100},
                                                {'p': 34, 'y': -150, 'rx': -78, 'o': 100}, {'p': 52, 'y': -230, 'rx': -88, 'o': 0},
                                                {'p': 100, 'y': -230, 'rx': -88, 'o': 0}]}
    for nome, x, w, extra, xm2, wm2 in [
            ('coperchio', bx - 14, bw + 28, dict(shapeGradient={'angle': 180, 'from': mix(ZAFFERANO, 70, C['nero']), 'to': kraft},
                                                  borderRadius=6, boxShadow={'x': 0, 'y': 10, 'blur': 20, 'spread': -6,
                                                                             'color': mix(C['nero'], 50)}),
             mx_ - 12, mw + 24),
            ('nastro2', SCATOLA[0] - 16, 32, dict(bgColor=POMODORO), SCATOLA_M[0] - 14, 28)]:
        a = _pc(x, by - 52, w, 62, W, H)
        b = _pc(xm2, my_ - 46, wm2, 56, WM, HM)
        livelli.append(K.livello('md-' + nome, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], entra='fadeInDown', ritardo=250,
                                 durata=900, scrollMove=apre,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}, **extra))
    livelli += [
        K.livello('md-o', contenuto='Madia · la dispensa dei piccoli produttori', x=6, y=22, w=44, size=12, peso='500',
                  colore=mix(C['chiaro'], 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(11), 'y': 5.5, 'x': 7, 'width': 86}}),
        K.livello('md-t', contenuto='Il buono,<br><em>in una scatola.</em>', tag='h1', x=6, y=26, w=44, size=76, peso='500',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, entra='maskFromBottom', ritardo=300, durata=1200,
                  responsive={'mobile': {'fontSize': m(42), 'y': 8.5, 'x': 7, 'width': 86}}),
        K.livello('md-s', contenuto='Trenta piccoli produttori fra la pianura e l’Appennino. Scorrete: la box di novembre si '
                  'apre.', x=6, y=48, w=32, size=16, peso='400', colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=700,
                  responsive={'mobile': {'fontSize': m(13), 'y': 20, 'x': 7, 'width': 86}}),
        K.livello('md-b', tipo='button', contenuto='La dispensa', x=6, y=60, w='auto', size=15, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=16, borderRadius=6, ritardo=1000, buttonUrl=P['negozio'],
                  hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 28.5, 'x': 7, 'fontSize': m(12)}}),
        K.livello('md-l', tipo='button', contenuto='La box del mese · 39 €', x=20, y=60, w='auto', size=15, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=6, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), ritardo=1150, buttonUrl=P['box'],
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 23}, 'mobile': {'y': 28.5, 'x': 40, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('md-1', K.sfondo_video(V_FARINA, overlay=0.12, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1900, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def listino(t, *args, **kw):
    """K.piani col bollino leggibile: testo chiaro sul rosso pomodoro."""
    riga = K.piani(t, *args, **kw)
    for col in riga['children']:
        for tile in col['children']:
            tile['settings']['badge_text_color'] = C['chiaro']
    return riga


def produttori(t):
    return K.squadra(t, [
        ('pexels-31694877.jpg', 'Luca, l’olio', 'Malcesine · lago di Garda', 'Casaliva in purezza, raccolta a mano e frantoio in '
                                                                             'giornata.', 'center 30%'),
        ('pexels-40427744.jpg', 'Matteo, il Parmigiano', 'Corniglio · 700 metri', 'Vacche rosse, latte di montagna, trenta mesi '
                                                                                  'di stagionatura.', 'center 30%'),
        ('pexels-35855516.jpg', 'Silvia, il vino', 'Colli di Parma', 'Malvasia e Lambrusco da sei ettari di vigna.', 'center 30%')],
        ratio='4/5', size='300')


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
# Dal 1° nov 2026 il negozio apre come quelli grandi: una fascia bassa di offerte che scorre da sola invece
# dell'apertura a tutto schermo, e il mega menu racconta le offerte e le novità nei suoi pannelli.
LARGO = 0.45  # la larghezza media di una lettera dei titoli (per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, bollino_giro=6, largo=LARGO)
    return N.vetrina(t, [
        N.offerta('md-o1', K.sfondo_video(V_FORMAGGIO, overlay=0.12), 'Box e regali · fino a domenica',
                  'Il tagliere delle colline, 49 €.', 'Parmigiano, pecorino, salame e miele col tagliere di faggio. Era 59 €.',
                  'Box e regali', N.cat_url('box-regalo'), bollino='−17%', nota='Il biglietto scritto a mano nei regali',
                  primo=True, **f),
        N.offerta('md-o2', K.sfondo_video(V_OLIO, overlay=0.12), 'Olio e conserve · le offerte del giovedì',
                  'Olio e conserve, fino al −21%.', 'I pomodori secchi sott’olio a 5,90 € invece di 7,50. E l’olio nuovo del Garda.',
                  'Olio e conserve', N.cat_url('olio-e-conserve'), bollino='−21%', nota='Spedizione refrigerata gratuita da 59 €', **f),
        N.offerta('md-o3', K.sfondo_foto('pexels-8964263.jpg', overlay=0.12, scala=1.06), 'Pasta e riso',
                  'Gli spaghetti di Gragnano, 6,40 €.', 'Il chilo trafilato al bronzo, era 7,90 €. E le tagliatelle all’uovo, tirate a mano.',
                  'Pasta e riso', N.cat_url('pasta-e-riso'), bollino='−19%', nota='I freschi viaggiano a 4 gradi', **f),
    ], transizione='stack')


# le schede sotto la vetrina: «tempo» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='tempo', voci=[
        ('Le offerte del giovedì', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Appena arrivati', {'orderby': 'date'}, 'Vedi tutte', P['negozio']),
        ('Sotto i 7 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=7'),
        ('I più amati', {'orderby': 'rating'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'La dispensa', 'Gli scaffali della madia', ('La box di novembre,', 'tartufo e castagne.', 'Il regalo già pronto, col biglietto.', 'La box', P['box'], 'pexels-4087609.jpg'))


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte', 'Cambiano il giovedì.', 'Quelle di oggi finiscono tra:',
                              ('Il tagliere delle colline,', '49 €.', 'Era 59 €, col tagliere di faggio.', 'Box e regali', N.cat_url('box-regalo'), 'pexels-34621129.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Arrivati in bottega',
                             [(n, N.cat_url(s)) for s, n in (('pasta-e-riso', 'Pasta e riso'), ('olio-e-conserve', 'Olio e conserve'), ('formaggi-e-salumi', 'Formaggi e salumi'), ('dolci-e-caffe', 'Dolci e caffè'), ('vini', 'Vini'))],
                             ('L’olio nuovo,', 'è arrivato.', 'Casaliva del Garda, spremuta a ottobre.', 'Olio e conserve', N.cat_url('olio-e-conserve'), 'pexels-21911357.jpg'))


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small'))
    out.append(t.section([
        N.testata_sezione(t, 'Gli scaffali', 'Cosa c’è nella madia', P['negozio'], 'Tutta la dispensa'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        N.testata_sezione(t, 'I più amati', 'Quello che finisce prima', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 72}))
    out.append(t.section([
        t.one([K.sechead(t, 'La box del mese', ['Otto prodotti,', 'una ricetta.'], size=60,
                         tagline='Il primo giovedì del mese, a casa vostra: otto prodotti dei nostri produttori e la ricetta per '
                                 'usarli. Si sospende quando volete.')]),
        listino(t, [('La box piccola', '39', 'al mese', 'Otto prodotti\nUna ricetta\nSpedizione compresa', False, ''),
                    ('La box grande', '69', 'al mese', 'Quattordici prodotti\nDue ricette\nUn vino dei colli\nSpedizione compresa',
                     True, 'la più scelta'),
                    ('Da regalare', '59', 'una volta', 'La box grande\nIl biglietto scritto a mano\nConsegna nel giorno scelto',
                     False, '')], 'Scegli la box', P['box'], nota='La box di novembre: tartufo nero, castagne e pecorino.'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_OLIO, 'L’olio nuovo', 'È arrivato <em>quello di ottobre.</em>', 'Casaliva del Garda, spremuta '
                             'il giorno della raccolta: fruttato leggero, sa di mandorla.', overlay=30,
                             extra=[N.pulsante(t, 'L’olio del Garda', N.cat_url('olio-e-conserve'), align='center', chiaro=True)]))
    out.append(t.section([
        t.one([K.sechead(t, 'I produttori', ['Trenta famiglie,', 'tutte conosciute.'], size=60,
                         tagline='Andiamo da ognuno almeno una volta all’anno: per questo possiamo raccontarvi chi fa cosa.')]),
        produttori(t),
        t.one([N.pulsante(t, 'Tutti i produttori', P['produttori'], pieno=False, pad_top=28)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Il tagliere delle colline,', '49 €.', 'Parmigiano, pecorino, salame e miele, col tagliere '
                               'di faggio.', 'Il tagliere', N.cat_url('box-regalo'), K.photo_bg('pexels-4087609.jpg'))],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'La pasta di Rosa,', 'tirata a mano.', 'Le tagliatelle all’uovo di Collecchio, essiccate piano.',
                               'La pasta', N.cat_url('pasta-e-riso'), K.video_bg(V_SFOGLIA))], wm='1-2', ws='1-1')],
              layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte della settimana', color=mix(C['chiaro'], 80)),
                      N.titolo(t, 'Finiscono <em>giovedì.</em>', size=52, color=C['chiaro']),
                      N.testo(t, 'Ogni giovedì cambiano: quello che i produttori hanno in più, a meno.', color=mix(C['chiaro'], 82))],
                     wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=46, giorni=True), N.link(t, 'Le offerte', P['offerte'], chiaro=True, align='left', pad_top=18)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=40, valign='center'),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, layout='carousel', card_bg=C['chiaro'])],
              style={'margin_top': 40}),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([t.one([t.classic('marquee', {
        'content_type': 'images', 'images': [{'url': K.img(f), 'alt': a} for f, a in (
            ('pexels-3184191.jpg', 'Una tavola apparecchiata vista dall’alto'), ('pexels-1537635.jpg', 'Due amiche a cena'),
            ('pexels-14515698.jpg', 'Le colline coi vigneti'), ('pexels-33313084.jpg', 'Le forme di formaggio a stagionare'),
            ('pexels-38179370.jpg', 'Gli scaffali dei barattoli di miele'), ('pexels-3938970.jpg', 'Un piatto di spaghetti'))],
        'speed': 32, 'direction': 'left', 'pause_hover': True, 'image_height': 260, 'image_fit': 'cover', 'gap': 14,
        'bg_color': 'transparent', 'full_width': True, 'velocity_skew': False})], anim=None)],
        width='fullbleed', padding='default', style={'padding_top': 64, 'padding_bottom': 0}))
    return out


def build_box():
    t = Tpl(ID, 'box')
    out = [K.intro(t, 'La box del mese', ['Otto prodotti,', 'una ricetta, il giovedì.'], 'La componiamo in bottega coi produttori '
                   'del mese: dentro c’è anche il biglietto con la ricetta.', bg=K.photo_bg('pexels-8580727.jpg'), size=92)]
    out.append(t.section([
        listino(t, [('La box piccola', '39', 'al mese', 'Otto prodotti\nUna ricetta\nSpedizione compresa', False, ''),
                    ('La box grande', '69', 'al mese', 'Quattordici prodotti\nDue ricette\nUn vino dei colli\nSpedizione compresa',
                     True, 'la più scelta'),
                    ('Da regalare', '59', 'una volta', 'La box grande\nIl biglietto scritto a mano\nConsegna nel giorno scelto',
                     False, '')], 'Abbonati', N.cat_url('box-regalo'), nota='Si sospende o si disdice con un clic dall’account.'),
    ], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.sechead(t, 'Novembre', ['Tartufo nero,', 'castagne, pecorino.'], size=56)]),
        t.row([t.col([K.image(t, f, a, ratio='1/1', lightbox=True)], wm='1-4', ws='1-2') for f, a in (
            ('pexels-8747538.jpg', 'Il pecorino stagionato'), ('pexels-34185860.jpg', 'Il miele di montagna col tè'),
            ('pexels-8964286.jpg', 'Le tagliatelle all’uovo'), ('pexels-12585560.jpg', 'Il caffè in grani'))],
              layout='25-25-25-25', gap=16),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(t.section([
        N.testata_sezione(t, 'Box e regali', 'Già pronti da regalare', N.cat_url('box-regalo')),
        t.one([N.prodotti(t, category='box-regalo', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 40}))
    out.append(K.domande(t, 'Sulla', 'box.', [
        ('Quando arriva?', 'Il primo giovedì del mese, refrigerata. Se non ci siete, il corriere riprova il venerdì.'),
        ('Posso saltare un mese?', 'Sì, dall’account, fino al lunedì prima della spedizione.'),
        ('Ci sono allergeni?', 'Ogni mese pubblichiamo l’elenco: se avete un’intolleranza, la box si cambia.'),
        ('Si può regalare?', 'Sì: scegliete il giorno, scriviamo noi il biglietto a mano.')], 'md-box'))
    return out


def build_produttori():
    t = Tpl(ID, 'produttori')
    out = [K.intro(t, 'I produttori', ['Trenta famiglie,', 'fra pianura e Appennino.'], 'Li conosciamo per nome e li andiamo a '
                   'trovare: ecco chi fa cosa.', bg=K.video_bg(V_GRANO, overlay=25), size=92)]
    out.append(t.section([produttori(t)], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 60}))
    for i, (foto, occh, tit, testo, cat) in enumerate([
            ('pexels-10510951.jpg', 'L’olio · Malcesine', 'La raccolta <em>dura tre settimane.</em>', 'Luca raccoglie a mano la '
             'Casaliva e la porta al frantoio la sera stessa: per questo l’olio è così fresco.', 'olio-e-conserve'),
            ('pexels-33313084.jpg', 'Il Parmigiano · Corniglio', 'Trenta mesi <em>sulle scalere.</em>', 'Le forme di Matteo '
             'stagionano a 700 metri: l’aria di montagna le asciuga piano.', 'formaggi-e-salumi'),
            ('pexels-30832873.jpg', 'Il vino · Colli di Parma', 'Sei ettari, <em>due vitigni.</em>', 'Silvia fa la Malvasia '
             'ferma e il Lambrusco: poche bottiglie, tutte buone.', 'vini')]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, occh, ratio='4/3')], wm='1-2', ws='1-1'),
                   t.col([N.occhiello(t, occh), N.titolo(t, tit, size=46), N.testo(t, testo, size=17),
                          N.pulsante(t, 'I suoi prodotti', N.cat_url(cat), pieno=False, pad_top=18)], wm='1-2', ws='1-1')],
                  layout='50-50', gap=64, valign='center'),
        ], bg=solid(C['sala']) if i % 2 == 0 else None, padding='large', width='large',
            style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La bottega'), N.titolo(t, 'Via Berenini 12, Fidenza', size=44),
                      K.specs(t, [('Orari', 'da martedì a sabato 8:30 — 13 e 16 — 19:30'), ('Il giovedì', 'partono le box'),
                                  ('L’assaggio', 'il sabato mattina')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, '44.8656', '10.0605', 'Madia · la bottega', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='bottega', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'La dispensa', 'Tutti i prodotti', 'Pasta, olio, formaggi, salumi, conserve, dolci e vini dei '
                            'nostri produttori. I freschi viaggiano refrigerati.', colonne=3,
                            banner=N.banner(t, 'Un regalo per Natale?', 'Ci pensiamo noi.', 'Le box e i taglieri, col biglietto '
                                            'scritto a mano.', 'Box e regali', N.cat_url('box-regalo'),
                                            K.photo_bg('pexels-4087615.jpg'), size=36))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Gli spaghetti di Gragnano,', '6,40 €.', 'Il chilo trafilato al bronzo, fino a giovedì.', 'Gli spaghetti',
                        N.cat_url('pasta-e-riso'), K.photo_bg('pexels-4033404.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Il caffè a legna,', '13 €.', 'La miscela di Fidenza in grani o per la moka.', 'Il caffè',
                        N.cat_url('dolci-e-caffe'), K.photo_bg('pexels-11505588.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Le offerte', 'Quello che i produttori hanno in più', 'Cambiano ogni giovedì: un raccolto '
                            'abbondante, una forma in più, a meno.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Spedizione refrigerata gratuita da 59 €'),
                                                   ('snowflake', 'I freschi viaggiano a 4 gradi'),
                                                   ('calendar', 'Ordini entro mercoledì, parte giovedì'),
                                                   ('gift', 'Si può regalare col biglietto')],
                             bundle='Con questo, a tavola', miniature='left', altezza='600px', bottone='Metti nella sporta')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione refrigerata gratuita da 59 €. Partono il giovedì.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna, il pagamento. Potete scrivere il biglietto per un regalo.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, la box del mese da sospendere o cambiare, gli indirizzi.',
                            ('Un ordine in arrivo?', 'Lo segui dall’assistenza col numero dell’ordine e la tua email.',
                             'Traccia l’ordine', P['assistenza'] + '#spedizioni'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto della dispensa.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'In bottega e al telefono dalle 8:30 alle 19:30, dal martedì al sabato.', [
        ('truck', 'Spedizioni', 'Refrigerate, gratis da 59 €. Partono il giovedì.', 'spedizioni'),
        ('snowflake', 'I freschi', 'Viaggiano a 4 gradi nella scatola isotermica.', 'spedizioni'),
        ('gift', 'I regali', 'Il biglietto a mano, la consegna nel giorno scelto.', 'scrivici'),
        ('briefcase', 'Aziende', 'Le strenne di Natale per i clienti e i colleghi.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 59 €; sotto, 7,90 €, refrigerata. Partono il giovedì e arrivano il '
                                        'venerdì.'),
        ('E se non sono in casa?', 'La scatola isotermica tiene il fresco per 48 ore: il corriere riprova il giorno dopo.'),
        ('Posso restituire un prodotto?', 'I freschi no, per legge; le conserve, la pasta e i vini sì, entro quattordici giorni.'),
        ('Fate le strenne aziendali?', 'Sì, da dieci box in su, col vostro biglietto: scriveteci.')], CONTATTI,
        campi_extra='Le strenne aziendali\n', foto=('pexels-38164887.jpg', 'Gli scaffali della bottega coi barattoli'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questo scaffale <em>è vuoto.</em>', 'Forse il prodotto è finito: i produttori fanno '
                        'piccole quantità. Cercatelo qui sopra.', K.video_bg(V_GRANO, overlay=40))


THEME = tema(
    ID, NAME, 'Negozi online', 'Le offerte della bottega',
    ['gastronomia', 'alimentari', 'prodotti tipici', 'woocommerce', 'e-commerce', 'abbonamento', 'box regalo', 'slider pro'],
    'La bottega online dei piccoli produttori, color farina: in apertura la fascia delle offerte che '
    'scorre (il tagliere, l’olio e le conserve, la pasta) e le quattro schede coi prodotti, il mega menu coi pannelli; gli scaffali, la box in abbonamento coi listini, i produttori, le '
    'offerte che cambiano il giovedì col conto alla rovescia, la scheda prodotto col video nella galleria.',
    [('La dispensa', P['negozio']), ('Novità', P['negozio']), ('Pasta e riso', '{{PAGINA:negozio}}?product_cat=pasta-e-riso'),
     ('Formaggi e salumi', '{{PAGINA:negozio}}?product_cat=formaggi-e-salumi'), ('La box del mese', P['box']),
     ('I produttori', P['produttori']), ('Offerte', P['offerte'])],
    [('home', 'Home', None), ('negozio', 'La dispensa', 'dispensa'), ('box', 'La box del mese', 'box-del-mese'),
     ('produttori', 'I produttori', 'produttori'), ('offerte', 'Le offerte', 'offerte'), ('assistenza', 'Assistenza', 'assistenza'),
     ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'La sporta', 'carrello'), ('cassa', 'La cassa', 'cassa'),
     ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Karla\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fraunces\', Georgia, serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Fraunces', 'Karla', 'IBM Plex Mono'], corsivo_di=['Fraunces'],
    peso_titoli='500', raggio=6, grana=3, titoli_line='1.04', titoli_spazio='-0.01em')
tema_negozio(THEME, NAME)
THEME['preview_v'] = THEME['preview_mobile_v'] = '4'  # anteprime con le testate nuove (2 nov 2026)
pannelli(THEME, NAME, {'La dispensa': ('pannello-reparti', 'Pannello del negozio'), 'Offerte': ('pannello-offerte', 'Pannello delle offerte'), 'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'farina'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'box.json': build_box(), 'produttori.json': build_produttori(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    dentro='Il prodotto, il biglietto del produttore, la scatola isotermica per i freschi.')


if __name__ == '__main__':
    main()
