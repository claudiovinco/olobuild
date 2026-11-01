# -*- coding: utf-8 -*-
"""Genera il tema «Davanzale» (id davanzale): il negozio online di due architette torinesi che scelgono oggetti per la
casa — luci, ceramiche, tavola, tessili, candele, piccoli arredi — da laboratori e designer italiani, e li raccontano
stanza per stanza.

Tema chiaro color calce: il petrolio sui pulsanti, l'ottone, la terracotta dei prezzi scontati. Titoli in Syne, testo in
Instrument Sans, dati in Martian Mono. Foto 4:5 senza cornice, la seconda al passaggio.

Apertura: la vetrina delle offerte, come nei negozi di casa online: una fascia bassa che scorre da sola (la
lampada Lume, le candele di soia, i vasi Graffio), sotto le quattro schede coi prodotti veri; il mega menu apre i
pannelli degli oggetti, delle novità e delle offerte. Cursore: l’ombra lunga.

WooCommerce: il catalogo coi filtri, la scheda prodotto col video (la lampada che si accende, il vaso sul tornio) nella
galleria, carrello, cassa, account, preferiti; le stanze da comprare punto per punto; lo studio.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'davanzale'
NAME = 'Davanzale'

HEX = {
    'primary': '#2F4A4C', 'primary_contrast': '#FFFFFF', 'secondary': '#C79A3E', 'secondary_contrast': '#1E1A10',
    'accent': '#A8472A', 'dark': '#161513', 'light': '#FBF9F5', 'muted': '#E2DCD2', 'muted_contrast': '#1E1C19',
    'text': '#1E1C19', 'text_muted': '#615C55', 'background': '#EEEAE3', 'border': '#CFC7BA', 'link': '#2F4A4C',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'stanze', 'studio', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'ciao@davanzale.example', '+39 011 000 4417', 'tel:+390110004417'

V_STANZA, V_LUME, V_TORNIO, V_TENDE, V_RATTAN, V_ARGILLA = (
    'pexels-video-6853917', 'pexels-video-7164569', 'pexels-video-12171874', 'pexels-video-854116', 'pexels-video-5384813',
    'pexels-video-12690272')
VIDEOS = [V_STANZA, V_LUME, V_TORNIO, V_TENDE, V_RATTAN, V_ARGILLA]
PHOTOS = [
    'pexels-38986382.jpg', 'pexels-38986380.jpg', 'pexels-38986383.jpg', 'pexels-35137148.jpg', 'pexels-2416875.jpg',
    'pexels-18207375.jpg', 'pexels-12128458.jpg', 'pexels-14608171.jpg', 'pexels-14608172.jpg', 'pexels-6805522.jpg',
    'pexels-7119222.jpg', 'pexels-7303788.jpg', 'pexels-7303773.jpg', 'pexels-5700552.jpg', 'pexels-4466240.jpg',
    'pexels-30885857.jpg', 'pexels-37827298.jpg', 'pexels-3847434.jpg', 'pexels-3847451.jpg', 'pexels-3847470.jpg',
    'pexels-8903626.jpg', 'pexels-8903358.jpg', 'pexels-8903728.jpg', 'pexels-15221845.jpg', 'pexels-3996529.jpg',
    'pexels-6634551.jpg', 'pexels-8762876.jpg', 'pexels-37023127.jpg', 'pexels-7004674.jpg', 'pexels-7004681.jpg',
    'pexels-7004675.jpg', 'pexels-5933693.jpg', 'pexels-5933684.jpg', 'pexels-16425094.jpg', 'pexels-16425117.jpg',
    'pexels-16310427.jpg', 'pexels-16310389.jpg', 'pexels-35071044.jpg', 'pexels-27786195.jpg', 'pexels-7193648.jpg',
    'pexels-39247420.jpg', 'pexels-30287174.jpg', 'pexels-15291102.jpg', 'pexels-6634455.jpg', 'pexels-27469712.jpg',
    'pexels-27604117.jpg', 'pexels-36777534.jpg', 'pexels-23471276.jpg', 'pexels-37652587.jpg', 'pexels-20285350.jpg',
    'pexels-6527029.jpg', 'pexels-8135505.jpg', 'pexels-6301179.jpg', 'pexels-7504591.jpg', 'pexels-7505175.jpg',
]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
OTTONE, TERRACOTTA = C['sec'], C['acc2']
TITOLI, MONO = 'Syne', 'Martian Mono'
N = Negozio(K, NAME, P, raggio=4, ratio='4-5', card='none', hover='swap', testata_scura=False,
            consegna='Spedizione gratuita da 99 €', evidenza='Scelto dallo studio', peso_titoli='600')

CATEGORIE = [
    ('illuminazione', 'Illuminazione', 'Lampade da tavolo, da comodino e a sospensione.', 'pexels-38986382.jpg'),
    ('ceramiche', 'Ceramiche', 'Vasi e brocche di gres, fatti al tornio.', 'pexels-14608171.jpg'),
    ('cucina-e-tavola', 'Cucina e tavola', 'Servizi di piatti, tazze, ciotole.', 'pexels-3847434.jpg'),
    ('tessili', 'Tessili', 'Tovaglie di lino, plaid di lana, cuscini.', 'pexels-16425094.jpg'),
    ('profumi-per-la-casa', 'Candele', 'Candele di cera di soia in vetro.', 'pexels-7004674.jpg'),
    ('piccolo-arredo', 'Piccolo arredo', 'Sgabelli, specchi, tappeti, una poltrona.', 'pexels-27786195.jpg'),
]
PRODOTTI = [
    prodotto('DAV-001', 'Lampada Faro, noce e ottone', 'lampada-faro', 'illuminazione', 189, 0, 14, 4.9, 212, True,
             ['pexels-38986382.jpg', 'pexels-38986380.jpg', 'pexels-38986383.jpg'], ['Noce massello e ottone spazzolato',
             'Luce calda dimmerabile', 'Fatta a mano a Cantù', 'Alta 38 cm'], 'Una lama di luce calda fra due tavole di noce: '
             'la lampada da tavolo che illumina la stanza come un camino.', cross=['DAV-017', 'DAV-020']),
    prodotto('DAV-002', 'Lampada da comodino Lume', 'lampada-comodino-lume', 'illuminazione', 129, 99, 20, 4.7, 146, False,
             ['pexels-27469712.jpg', 'pexels-27604117.jpg'], ['Base in legno tornito', 'Paralume in lino', 'Interruttore sul cavo',
             'Il video nella galleria'], 'La luce della sera sul comodino: si accende nel video, guardatela.', video=V_LUME,
             cross=['DAV-016']),
    prodotto('DAV-003', 'Sospensione Pigna, rame', 'sospensione-pigna-rame', 'illuminazione', 245, 0, 8, 4.8, 88, False,
             ['pexels-35137148.jpg', 'pexels-2416875.jpg'], ['Lamelle di rame sovrapposte', 'Luce riflessa e morbida',
             'Diametro 35 cm', 'Cavo in tessuto da 2 m'], 'Le lamelle di rame fanno una luce riflessa, mai diretta: '
                                                              'sopra il tavolo da pranzo.'),
    prodotto('DAV-004', 'Sospensione Nuvola', 'sospensione-nuvola', 'illuminazione', 165, 0, 12, 4.6, 61, False,
             ['pexels-18207375.jpg', 'pexels-12128458.jpg'], ['Paralume in metallo bianco', 'Luce diretta', 'Diametro 30 cm',
             'Per la cucina e il corridoio'], 'Semplice e bianca, in fila sull’isola della cucina.'),
    prodotto('DAV-005', 'Vasi Brezza, set di tre', 'vasi-brezza', 'ceramiche', 79, 0, 22, 4.8, 174, True,
             ['pexels-14608171.jpg', 'pexels-14608172.jpg'], ['Gres bianco opaco', 'Tre altezze', 'Impermeabili',
             'Fatti al tornio a Castellamonte'], 'Tre vasi bianchi che stanno bene vuoti, col fiore solo, o con un ramo.',
             cross=['DAV-008']),
    prodotto('DAV-006', 'Vaso Anello', 'vaso-anello', 'ceramiche', 59, 0, 18, 4.5, 92, False, ['pexels-6805522.jpg'],
             ['Gres naturale', 'Forma ad anello', 'Per i fiori secchi', 'Pezzo unico, varia un poco'],
             'Il vaso tondo col buco al centro: per un ramo di eucalipto o niente.'),
    prodotto('DAV-007', 'Coppia di vasi Graffio', 'vasi-graffio', 'ceramiche', 98, 78, 10, 4.7, 66, False,
             ['pexels-7119222.jpg'], ['Gres graffiato a mano', 'Rosa e grigio', 'Alti 28 e 22 cm', 'Il video del tornio nella '
             'galleria'], 'Fatti al tornio e graffiati col pettine, uno per uno: guardate nel video come nascono.',
             video=V_TORNIO),
    prodotto('DAV-008', 'Brocca Ramo, gres', 'brocca-ramo', 'ceramiche', 45, 0, 26, 4.6, 118, False,
             ['pexels-7303788.jpg', 'pexels-7303773.jpg', 'pexels-5700552.jpg'], ['Gres smaltato', 'Per l’acqua e per i fiori',
             'Un litro', 'Va in lavastoviglie'], 'La brocca per l’acqua in tavola che diventa vaso per un ramo.'),
    prodotto('DAV-009', 'Tazze Onda, set di due', 'tazze-onda', 'cucina-e-tavola', 38, 0, 40, 4.7, 240, False,
             ['pexels-4466240.jpg', 'pexels-30885857.jpg'], ['Gres con decoro inciso', '300 ml', 'Lavastoviglie e microonde',
             'Ognuna un poco diversa'], 'Le tazze del caffè lungo della domenica, col decoro inciso a mano.'),
    prodotto('DAV-010', 'Servizio Notte, 12 pezzi', 'servizio-notte', 'cucina-e-tavola', 189, 0, 10, 4.9, 77, True,
             ['pexels-3847434.jpg', 'pexels-3847451.jpg', 'pexels-3847470.jpg'], ['Gres blu notte, grigio e rosso',
             'Piatti piani, fondi e ciotole per quattro', 'Lavastoviglie', 'Smalti reattivi: ogni pezzo diverso'],
             'Il servizio per quattro in blu notte, col rosso di una ciotola: la tavola si apparecchia da sola.'),
    prodotto('DAV-011', 'Servizio Arcobaleno', 'servizio-arcobaleno', 'cucina-e-tavola', 149, 119, 12, 4.6, 58, False,
             ['pexels-8903626.jpg', 'pexels-8903358.jpg', 'pexels-8903728.jpg'], ['Gres in cinque colori', 'Per quattro',
             'Si mescola come si vuole', 'Lavastoviglie'], 'Cinque colori da mescolare: ogni tavola è diversa.'),
    prodotto('DAV-012', 'Tovaglia in lino lavato', 'tovaglia-lino-lavato', 'tessili', 69, 0, 30, 4.8, 190, False,
             ['pexels-16425094.jpg', 'pexels-16425117.jpg'], ['Lino lavato, bianco', '160 × 220 cm', 'Non si stira',
             'Tessuta a Biella'], 'La tovaglia bianca che non si stira: si lava e si stende.', cross=['DAV-010']),
    prodotto('DAV-013', 'Tovaglia Terracotta', 'tovaglia-terracotta', 'tessili', 74, 0, 18, 4.7, 84, False,
             ['pexels-16310427.jpg', 'pexels-16310389.jpg'], ['Lino tinto in capo', '160 × 220 cm', 'Colore terracotta',
             'Tessuta a Biella'], 'Il colore della terra: con le stoviglie bianche e il legno.'),
    prodotto('DAV-014', 'Plaid Riga in lana', 'plaid-riga-lana', 'tessili', 89, 0, 16, 4.8, 132, True,
             ['pexels-15221845.jpg'], ['Lana merino e cotone', '130 × 180 cm', 'Le frange annodate a mano', 'Tessuto in Val '
             'Seriana'], 'Il plaid a righe sul bracciolo del divano, pronto per le sere di novembre.', cross=['DAV-016']),
    prodotto('DAV-015', 'Plaid Mohair, rosso', 'plaid-mohair-rosso', 'tessili', 119, 0, 9, 4.9, 47, False,
             ['pexels-3996529.jpg'], ['Mohair e lana', 'Leggerissimo e caldo', '130 × 170 cm', 'Rosso lacca'],
             'Leggero come una nuvola, rosso come una lacca: il plaid che si regala.'),
    prodotto('DAV-016', 'Cuscini in lino, set di tre', 'cuscini-lino', 'tessili', 64, 0, 24, 4.6, 101, False,
             ['pexels-8762876.jpg', 'pexels-37023127.jpg'], ['Lino lavato', '50 × 50 cm, imbottitura in piuma', 'Tre colori di '
             'terra', 'Federa sfoderabile'], 'Tre cuscini nei colori della terra, da mescolare sul divano.'),
    prodotto('DAV-017', 'Candele Ambra, set di tre', 'candele-ambra', 'profumi-per-la-casa', 42, 0, 50, 4.8, 305, False,
             ['pexels-7004674.jpg', 'pexels-7004681.jpg', 'pexels-7004675.jpg'], ['Cera di soia', 'Vetro ambrato riutilizzabile',
             'Legno di cedro e fico', 'Trenta ore ciascuna'], 'Tre vasetti di vetro ambrato che profumano di cedro e fico.'),
    prodotto('DAV-018', 'Candela Pesca', 'candela-pesca', 'profumi-per-la-casa', 26, 21, 40, 4.6, 140, False,
             ['pexels-5933693.jpg', 'pexels-5933684.jpg'], ['Cera di soia', 'Vetro colorato', 'Fiori d’arancio', 'Quaranta ore'],
             'Profuma di fiori d’arancio nel vetro color pesca.'),
    prodotto('DAV-019', 'Sgabello Trespolo, rovere', 'sgabello-trespolo', 'piccolo-arredo', 145, 0, 12, 4.7, 66, False,
             ['pexels-27786195.jpg', 'pexels-7193648.jpg', 'pexels-39247420.jpg'], ['Rovere massello', 'Alto 65 cm',
             'Fa da tavolino accanto al letto', 'Fatto a mano in Brianza'], 'Lo sgabello che fa da comodino, da tavolino, '
                                                                             'da sgabello.'),
    prodotto('DAV-020', 'Specchio Tondo', 'specchio-tondo', 'piccolo-arredo', 210, 0, 7, 4.8, 39, False,
             ['pexels-30287174.jpg'], ['Cornice in ferro nero', 'Diametro 80 cm', 'Per l’ingresso e il bagno',
             'Gancio compreso'], 'Il tondo nero che allarga l’ingresso.'),
    prodotto('DAV-021', 'Tappeto in juta', 'tappeto-juta', 'piccolo-arredo', 159, 129, 10, 4.5, 72, False,
             ['pexels-15291102.jpg', 'pexels-6634455.jpg'], ['Juta e cotone', '160 × 230 cm', 'Tessuto a mano',
             'Antiscivolo sotto'], 'Il tappeto che scalda il pavimento senza coprire la stanza.'),
    prodotto('DAV-022', 'Poltrona Nido', 'poltrona-nido', 'piccolo-arredo', 690, 0, 4, 4.9, 28, True,
             ['pexels-37652587.jpg', 'pexels-23471276.jpg'], ['Struttura in faggio', 'Velluto rosa cipria', 'Seduta profonda',
             'Fatta a Meda'], 'La poltrona in cui si legge un libro intero: profonda, morbida, rosa cipria.',
             cross=['DAV-014', 'DAV-001']),
]
RECENSIONI = [[5, 'Ancora più bella dal vivo. Imballata con una cura che non avevo mai visto.'],
              [5, 'Si vede il lavoro a mano. Ci hanno scritto chi l’ha fatta.'],
              [4, 'Molto bella, un po’ più piccola di come la immaginavo: leggete le misure.'],
              [5, 'Consegna al piano, montata e il cartone portato via.'],
              [5, 'Mi hanno consigliato loro per email il colore giusto per il soggiorno.'],
              [4, 'Ottima qualità, tempi un po’ lunghi perché fatta su ordinazione.'],
              [5, 'Regalo per la casa nuova di mia sorella: un successo.'],
              [5, 'I colori dello smalto sono diversi in ogni pezzo, proprio come scritto.']]
NOMI = ['Giulia R.', 'Andrea M.', 'Federica B.', 'Luca T.', 'Silvia P.', 'Marco C.', 'Elisa L.', 'Tommaso D.', 'Camilla G.',
        'Pietro V.', 'Anna S.', 'Matilde F.']
PROMESSE = [('truck', 'Spedizione gratuita da 99 €'), ('package', 'I mobili montati, il cartone lo portiamo via'),
            ('rotate-ccw', 'Reso gratuito in 30 giorni'), ('pen-tool', 'Consulenza dello studio, gratis')]
CONTATTI = [('map-pin', 'Studio · via Bonelli 9, Torino', P['studio'] + '#studio'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'davanzale', icona='', ricerca='Cerca una lampada, un vaso, un colore…',
                     parole='una lampada da comodino\nun vaso bianco\nun plaid di lana\nla tovaglia di lino',
                     annuncio=('Settimana della tavola: piatti e tovaglie fino a −20%.', 'Apparecchia', P['offerte']),
                     logo_size=30)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'davanzale', '', 'Oggetti per la casa scelti da uno studio di architettura torinese, da '
                   'laboratori e designer italiani. Una stanza alla volta.',
                   [('Negozio', [('Tutti gli oggetti', P['negozio']), ('Illuminazione', N.cat_url('illuminazione')),
                                 ('Ceramiche', N.cat_url('ceramiche')), ('Tessili', N.cat_url('tessili')),
                                 ('Offerte', P['offerte'])]),
                    ('Davanzale', [('Le stanze', P['stanze']), ('Lo studio', P['studio']), ('I preferiti', P['preferiti']),
                                   ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni e montaggio', P['assistenza'] + '#spedizioni'),
                               ('Resi', P['assistenza'] + '#spedizioni'), ('Consulenza', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Davanzale s.r.l. · Torino · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera dello studio', 'Una stanza al mese.',
                              'Una stanza vera, gli oggetti che ci abbiamo messo e perché.',
                              'Fatto: la prima stanza arriva il primo del mese.'),
                   social=[('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')])


# ── L'apertura: la stanza che si avvicina ──
# (Dal 1° nov 2026 la home apre con la vetrina delle offerte: questa apertura non è più usata.)
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
SCHEDE = [('pexels-38986382.jpg', 'Lampada Faro', '189 €', 'illuminazione'),
          ('pexels-15221845.jpg', 'Plaid Riga', '89 €', 'tessili'),
          ('pexels-14608171.jpg', 'Vasi Brezza', '79 €', 'ceramiche')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    livelli = [
        # la stanza è un livello video: con lo scorrimento si avvicina verso la poltrona
        K.livello('dv-stanza', tipo='video', videoSrc=K.img(V_STANZA + '.mp4'), videoPoster=K.img(V_STANZA + '.jpg'),
                  videoAutoplay=True, videoMuted=True, videoLoop=True, objectFit='cover', x=0, y=0, w=100, h=100,
                  entra='fadeIn', ritardo=0, durata=600,
                  scrollMove={'inizio': 0, 'fine': 100, 'da': {'s': 1}, 'a': {'s': 1.3, 'x': 140, 'y': 30}}),
        K.livello('dv-velo', tipo='shape', x=0, y=22, w=100, h=78, entra='fadeIn', ritardo=0,
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 40},
                  responsive={'mobile': {'x': 0, 'y': 36, 'width': 100, 'height': 64}}),
    ]
    # le schede degli oggetti: entrano da destra una dopo l'altra
    larg, alto = 300.0, 112.0
    lm, am = 560.0, 110.0
    for k, (foto, nome, prezzo, cat) in enumerate(SCHEDE):
        y = 150 + (alto + 18) * k
        ym = 120 + (am + 14) * k
        entra = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 480, 'o': 0}, {'p': 18 + 16 * k, 'x': 480, 'o': 0},
                                                     {'p': 40 + 16 * k, 'x': 0, 'o': 100}, {'p': 100, 'x': 0, 'o': 100}]}
        a = _pc(W - 64 - larg, y, larg, alto, W, H)
        b = _pc(40, ym, lm, am, WM, HM)
        livelli.append(K.livello('dv-scheda%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['chiaro'],
                                 borderRadius=4, boxShadow={'x': 0, 'y': 18, 'blur': 40, 'spread': -14, 'color': mix(C['nero'], 55)},
                                 entra='fadeIn', ritardo=0, scrollMove=entra,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a = _pc(W - 64 - larg + 10, y + 10, 92, 92, W, H)
        b = _pc(50, ym + 10, 90, 90, WM, HM)
        livelli.append(K.livello('dv-foto%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a[0], y=a[1], w=a[2],
                                 h=a[3], borderRadius=2, entra='fadeIn', ritardo=0, scrollMove=entra,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a = _pc(W - 64 - larg + 120, y + 22, 170, 24, W, H)
        b = _pc(160, ym + 18, 300, 30, WM, HM)
        livelli.append(K.livello('dv-nome%d' % k, contenuto=nome, x=a[0], y=a[1], w=a[2], size=19, peso='600', colore=C['testo'],
                                 fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=entra,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(15)}}))
        a = _pc(W - 64 - larg + 120, y + 58, 170, 30, W, H)
        b = _pc(160, ym + 58, 300, 30, WM, HM)
        livelli.append(K.livello('dv-prezzo%d' % k, tipo='button', contenuto='%s · vedi' % prezzo, x=a[0], y=a[1], w='auto',
                                 size=12, peso='500', colore=C['on_acc'], bgColor=C['acc'], padding=8, borderRadius=2,
                                 fontFamily=MONO, entra='fadeIn', ritardo=0, buttonUrl=N.cat_url(cat),
                                 hover={'bgColor': C['nero'], 'duration': 200}, scrollMove=entra,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(10)}}))
    livelli += [
        K.livello('dv-o', contenuto='Davanzale · oggetti per la casa · Torino', x=6, y=42, w=44, size=12, peso='500',
                  colore=mix(C['chiaro'], 88), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 47, 'x': 7, 'width': 86}}),
        K.livello('dv-t', contenuto='Una stanza<br>alla volta.', tag='h1', x=6, y=46, w=48, size=84, peso='600',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=0.98, letterSpacing=-2, entra='maskFromBottom', ritardo=300,
                  durata=1200, responsive={'mobile': {'fontSize': m(44), 'y': 50, 'x': 7, 'width': 86}}),
        K.livello('dv-s', contenuto='Luci, ceramiche, tessili e piccoli arredi scelti da uno studio di architettura. Scorrete: la '
                  'stanza si avvicina, gli oggetti vi vengono incontro.', x=6, y=67, w=36, size=15, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=700,
                  responsive={'mobile': {'fontSize': m(13), 'y': 63, 'x': 7, 'width': 86}}),
        K.livello('dv-b', tipo='button', contenuto='Le stanze', x=44, y=68, w='auto', size=14, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=16, borderRadius=4, ritardo=1000, buttonUrl=P['stanze'],
                  hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                  responsive={'tablet': {'x': 46}, 'mobile': {'y': 75, 'x': 7, 'fontSize': m(12)}}),
        K.livello('dv-l', tipo='button', contenuto='Tutti gli oggetti', x=53.4, y=68, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=4, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), ritardo=1150, buttonUrl=P['negozio'],
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 58}, 'mobile': {'y': 75, 'x': 40, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('dv-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1900, scrollStep=False, globalBackground={'type': 'color', 'color': C['nero']},
                       heightMobile={'mode': 'px', 'value': 1400})


def stanza(t, foto, alt, punti, ratio='16/10'):
    """Una stanza da comprare punto per punto: [(x, y, titolo, descrizione, foto, categoria)]."""
    return t.bare('hotspot', {
        'image': K.img(foto), 'image_alt': alt, 'aspect_ratio': 'custom', 'aspect_ratio_custom': ratio, 'object_fit': 'cover',
        'border_radius': radius(4), 'preset': 'custom', 'marker_style': 'number', 'marker_color': C['chiaro'],
        'marker_text_color': C['nero'], 'marker_size': 28, 'pulse_animation': True, 'tooltip_bg': C['chiaro'],
        'tooltip_color': C['testo'], 'tooltip_width': 250,
        'markers': [{'pos_x': x, 'pos_y': y, 'title': ti, 'description': de, 'icon': '', 'image': K.img(f) if f else '',
                     'link': N.cat_url(cat), 'link_label': 'Vedi', 'tooltip_position': 'top'} for x, y, ti, de, f, cat in punti]})


SOGGIORNO = ('pexels-36777534.jpg', 'Un soggiorno chiaro con la poltrona, il divano e i quadri', [
    (30, 62, 'La poltrona', 'Poltrona Nido · 690 €', 'pexels-37652587.jpg', 'piccolo-arredo'),
    (62, 66, 'Il plaid', 'Plaid Riga in lana · 89 €', 'pexels-15221845.jpg', 'tessili'),
    (48, 30, 'La luce', 'Sospensione Nuvola · 165 €', 'pexels-18207375.jpg', 'illuminazione')])
CAMERA = ('pexels-8135505.jpg', 'Una camera col letto verde e le lampade sui comodini', [
    (20, 52, 'Il comodino', 'Lampada Lume · 99 €', 'pexels-27469712.jpg', 'illuminazione'),
    (55, 70, 'Il letto', 'Cuscini in lino · 64 €', 'pexels-8762876.jpg', 'tessili')])
TAVOLA = ('pexels-35071044.jpg', 'Una tavola apparecchiata in giardino coi fiori', [
    (50, 62, 'La tavola', 'Tovaglia in lino lavato · 69 €', 'pexels-16425094.jpg', 'tessili'),
    (40, 48, 'I piatti', 'Servizio Notte · 189 €', 'pexels-3847434.jpg', 'cucina-e-tavola')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
# Dal 1° nov 2026 il negozio apre come quelli grandi: una fascia bassa di offerte che scorre da sola invece
# dell'apertura a tutto schermo, e il mega menu racconta le offerte e le novità nei suoi pannelli.
LARGO = 0.47  # la larghezza media di una lettera dei titoli (per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO)
    return N.vetrina(t, [
        N.offerta('dv-o1', K.sfondo_video(V_RATTAN, overlay=0.12), 'Illuminazione · fino a domenica',
                  'La luce della sera, −23%.', 'La lampada da comodino Lume a 99 € invece di 129.', 'Le luci',
                  N.cat_url('illuminazione'), bollino='−23%', bollino_x=66, nota='Spedizione gratuita da 99 €', primo=True, **f),
        N.offerta('dv-o2', K.sfondo_foto('pexels-7004675.jpg', overlay=0.12, scala=1.06), 'Candele · per novembre',
                  'Le candele di soia, da 21 €.', 'Cera di soia in vetro ambrato: la Pesca a 21 € invece di 26.', 'Le candele',
                  N.cat_url('profumi-per-la-casa'), bollino='−19%', bollino_x=80, nota='Reso gratuito in 30 giorni', **f),
        N.offerta('dv-o3', K.sfondo_foto('pexels-14608171.jpg', overlay=0.12, scala=1.06), 'Ceramiche · fatte al tornio',
                  'I vasi Graffio, la coppia a 78 €.', 'Gres di Castellamonte, ogni pezzo è diverso. Era 98 €.', 'Le ceramiche',
                  N.cat_url('ceramiche'), bollino='−20%', nota='La consulenza dello studio è gratis', **f),
    ], transizione='slideOver')


# le schede sotto la vetrina: «tre» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='tre', voci=[
        ('Le offerte', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Appena arrivati', {'orderby': 'date'}, 'Vedi tutte', P['negozio']),
        ('Sotto i 50 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=50'),
        ('I più amati', {'orderby': 'rating'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Gli oggetti', 'Scelti dallo studio', ('La luce della sera,', '−23%.', 'La lampada Lume a 99 €.', 'Le luci', N.cat_url('illuminazione'), 'pexels-38986382.jpg'))


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Settimana della tavola', 'Piatti e tovaglie, fino a −20%.', 'Quelle di oggi finiscono tra:',
                              ('Le candele di soia,', 'da 21 €.', 'In vetro ambrato, colate a mano.', 'Le candele', N.cat_url('profumi-per-la-casa'), 'pexels-7004681.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Dai laboratori, questa settimana',
                             [(n, N.cat_url(s)) for s, n in (('illuminazione', 'Illuminazione'), ('ceramiche', 'Ceramiche'), ('cucina-e-tavola', 'Cucina e tavola'), ('tessili', 'Tessili'), ('profumi-per-la-casa', 'Candele'), ('piccolo-arredo', 'Piccolo arredo'))],
                             ('Il plaid di novembre,', 'tessuto in valle.', 'Lana merino e cotone, le frange a mano.', 'I tessili', N.cat_url('tessili'), 'pexels-3996529.jpg'))


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small'))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Compra la stanza'), N.titolo(t, 'Il soggiorno<br>di via Bonelli.', size=56),
                      N.testo(t, 'Un soggiorno che abbiamo arredato quest’autunno: toccate i numeri per l’oggetto e il prezzo.',
                              size=17), N.pulsante(t, 'Tutte le stanze', P['stanze'], pieno=False, pad_top=20)],
                     wm='1-3', ws='1-1'),
               t.col([stanza(t, *SOGGIORNO)], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=48, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        N.testata_sezione(t, 'Gli scaffali', 'Cosa cercate?', P['negozio'], 'Tutti gli oggetti'),
        t.one([N.categorie(t, colonne=6, raggio_foto=4, ratio='4-5')]),
    ], bg=solid(C['sala']), padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 56}))
    out.append(t.section([
        N.testata_sezione(t, 'Appena arrivati', 'Dai laboratori, questa settimana', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, posts_per_page=10, columns=4, layout='carousel', orderby='date')]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 64}))
    out.append(K.pannello(t, V_TORNIO, 'Le ceramiche', 'Fatte al tornio, <em>una per una.</em>', 'Castellamonte, dove si fa la '
                          'ceramica da trecento anni: ogni vaso esce un poco diverso dall’altro.', 'Le ceramiche',
                          N.cat_url('ceramiche'), overlay=40, video=True))
    out.append(t.section([
        N.testata_sezione(t, 'I più amati', 'Quelli che si regalano', P['negozio']),
        t.one([N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 72}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Settimana della tavola', color=mix(C['chiaro'], 80)),
                      N.titolo(t, 'Piatti e tovaglie, fino a −20%.', size=48, color=C['chiaro']),
                      N.testo(t, 'Fino a domenica: il servizio Arcobaleno, le tovaglie di Biella, le candele.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=46, giorni=True), N.link(t, 'Apparecchia', P['offerte'], chiaro=True, align='left',
                                                              pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=40, valign='center'),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'La luce della sera,', 'sul comodino.', 'La lampada Lume a 99 €, fino a domenica.', 'Le luci',
                               N.cat_url('illuminazione'), K.video_bg(V_LUME))], wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Il plaid di novembre,', 'tessuto in valle.', 'Lana merino e cotone, le frange a mano.',
                               'I tessili', N.cat_url('tessili'), K.photo_bg('pexels-6634551.jpg'))], wm='1-2', ws='1-1')],
              layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-7504591.jpg', 'Il tavolo dello studio coi campioni di legno', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Lo studio'), N.titolo(t, 'Due architette,<br>una lista corta.', size=52),
                      N.testo(t, 'Disegniamo case da quindici anni: in ogni casa servono le stesse poche cose fatte bene. Le '
                                 'cerchiamo nei laboratori italiani e le vendiamo qui.', size=17),
                      K.numeri(t, [('31', 'laboratori'), ('140', 'oggetti, non di più'), ('15', 'anni di case')], colonne=3,
                               size=46),
                      N.pulsante(t, 'Lo studio', P['studio'], pieno=False, pad_top=24)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_stanze():
    t = Tpl(ID, 'stanze')
    out = [K.intro(t, 'Le stanze', ['Tre stanze,', 'punto per punto.'], 'Le abbiamo arredate davvero: toccate i numeri per '
                   'l’oggetto, il prezzo e la sua scheda.', bg=K.video_bg(V_TENDE, overlay=20), size=92)]
    for i, (dati, occh, tit) in enumerate([(SOGGIORNO, 'Il soggiorno', 'La poltrona, il plaid, la luce.'),
                                            (CAMERA, 'La camera', 'Il comodino e il letto.'),
                                            (TAVOLA, 'La tavola', 'Il pranzo in giardino.')]):
        out.append(t.section([
            t.one([N.occhiello(t, occh), N.titolo(t, tit, size=44)], style={'margin_bottom': 20}),
            t.one([stanza(t, *dati)]),
        ], bg=solid(C['sala']) if i % 2 == 0 else None, padding='large', width='large',
            style={'padding_top': 90, 'padding_bottom': 90}))
    return out


def build_studio():
    t = Tpl(ID, 'studio')
    out = [K.intro(t, 'Lo studio', ['Via Bonelli 9,', 'Torino.'], 'Uno studio di architettura con una stanza in più: quella '
                   'dove proviamo gli oggetti prima di venderli.', bg=K.photo_bg('pexels-7505175.jpg'), size=92)]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Come scegliamo'), N.titolo(t, 'Tre domande,<br>per ogni oggetto.', size=48)], wm='1-3', ws='1-1'),
               t.col([t.classic('process-steps', {
                   'items': [{'number': '01', 'title': 'Dura?', 'description': 'Materiali veri, giunzioni fatte bene, smalti '
                                                                                 'che non si scheggiano.'},
                             {'number': '02', 'title': 'Serve?', 'description': 'Un oggetto che si usa ogni giorno, non solo da '
                                                                                  'guardare.'},
                             {'number': '03', 'title': 'Chi lo fa?', 'description': 'Un laboratorio che conosciamo e che '
                                                                                      'andiamo a trovare.'}],
                   'columns': 3, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'],
                   'number_bg': '', 'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': C['testo'],
                   'title_size': 30, 'title_weight': '600', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15,
                   'align': 'left', 'item_gap': 10, 'card_bg': 'transparent',
                   'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
                   'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 3, 'columns_mobile': 1})],
                     wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='start'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([K.image(t, f, a, ratio='4/5', lightbox=True)], wm='1-3', ws='1-1') for f, a in (
            ('pexels-37827298.jpg', 'Un ceramista nel suo laboratorio'), ('pexels-20285350.jpg', 'Un soggiorno arredato dallo studio'),
            ('pexels-6301179.jpg', 'Una camera con la testiera imbottita'))], layout='33-33-33', gap=16),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La consulenza'), N.titolo(t, 'Una stanza da sistemare?', size=44),
                      N.testo(t, 'Mandateci due foto e le misure: vi rispondiamo con una lista di oggetti e dove metterli. Gratis.',
                              size=17), N.pulsante(t, 'Chiedi la consulenza', P['assistenza'] + '#scrivici', pad_top=18)],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, '45.0731', '7.6800', 'Davanzale · lo studio', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='studio', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il negozio', 'Tutti gli oggetti', 'Centoquaranta oggetti, non di più: filtrate per stanza, '
                            'categoria e prezzo.', banner=N.banner(t, 'Non sapete cosa manca?', 'Ve lo diciamo noi.',
                                                                   'Due foto e le misure: la consulenza dello studio è gratis.',
                                                                   'Chiedi', P['assistenza'] + '#scrivici',
                                                                   K.photo_bg('pexels-6527029.jpg'), size=36))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Il servizio Arcobaleno,', '119 €.', 'Cinque colori per quattro, fino a domenica.', 'Il servizio',
                        N.cat_url('cucina-e-tavola'), K.photo_bg('pexels-8903626.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Il tappeto in juta,', '129 €.', 'Tessuto a mano, 160 per 230.', 'Il tappeto',
                        N.cat_url('piccolo-arredo'), K.photo_bg('pexels-15291102.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Settimana della tavola', 'Piatti, tovaglie e candele, fino a −20%', 'Fino a domenica a '
                            'mezzanotte. La spedizione è gratuita da 99 €.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Spedizione gratuita da 99 €'),
                                                   ('package', 'I mobili montati, il cartone portato via'),
                                                   ('rotate-ccw', 'Reso gratuito in 30 giorni'),
                                                   ('pen-tool', 'Dove metterlo? Chiedete allo studio')],
                             bundle='Nella stessa stanza', miniature='bottom', altezza='680px', bottone='Aggiungi al carrello',
                             fondo_galleria=C['sala'])


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 99 €; i mobili arrivano montati.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna (anche al piano), il pagamento.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, le consegne al piano, gli indirizzi.',
                            ('Un ordine in arrivo?', 'Lo segui dall’assistenza col numero dell’ordine e la tua email.',
                             'Traccia l’ordine', P['assistenza'] + '#spedizioni'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto degli oggetti.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'Per email e al telefono dalle 9 alle 18; lo studio risponde sugli '
                               'arredi.', [
        ('truck', 'Spedizioni', 'Gratis da 99 €, in tre giorni lavorativi.', 'spedizioni'),
        ('package', 'Montaggio', 'I mobili arrivano montati; il cartone lo portiamo via.', 'spedizioni'),
        ('rotate-ccw', 'Resi', 'Trenta giorni, gratis, anche per i mobili.', 'spedizioni'),
        ('pen-tool', 'Consulenza', 'Due foto e le misure: vi rispondiamo con una lista.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 99 €; sotto, 7 €. I mobili arrivano al piano, montati.'),
        ('Le ceramiche si rompono in viaggio?', 'Viaggiano nella paglia di legno e nel cartone doppio: se arriva rotto, lo '
                                               'rimandiamo subito.'),
        ('Posso restituire un mobile?', 'Sì, entro trenta giorni: veniamo noi a riprenderlo.'),
        ('Fate progetti completi?', 'Lo studio sì: scriveteci e vi diciamo come lavoriamo.')], CONTATTI,
        campi_extra='Una consulenza per una stanza\n', foto=('pexels-27604117.jpg', 'Una camera con la lampada accesa sul comodino'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa stanza <em>è vuota.</em>', 'L’oggetto che cercate non c’è più, o non c’è mai '
                        'stato. Cercatelo qui sopra.', K.video_bg(V_RATTAN, overlay=40))


THEME = tema(
    ID, NAME, 'Negozi online', 'Le offerte per la casa',
    ['casa', 'design', 'arredamento', 'ceramiche', 'woocommerce', 'e-commerce', 'torino', 'slider pro'],
    'Il negozio online di uno studio di architettura che sceglie oggetti per la casa, color calce: in apertura la fascia delle offerte che '
    'scorre (la luce, le candele, le ceramiche) e le quattro schede coi prodotti, il mega menu coi pannelli; le stanze da '
    'comprare punto per punto, gli scaffali, la settimana della tavola col conto alla rovescia, la scheda prodotto col video '
    'nella galleria.',
    [('Tutti gli oggetti', P['negozio']), ('Novità', P['negozio']),
     ('Illuminazione', '{{PAGINA:negozio}}?product_cat=illuminazione'),
     ('Ceramiche', '{{PAGINA:negozio}}?product_cat=ceramiche'), ('Tessili', '{{PAGINA:negozio}}?product_cat=tessili'),
     ('Le stanze', P['stanze']), ('Offerte', P['offerte']), ('Lo studio', P['studio'])],
    [('home', 'Home', None), ('negozio', 'Gli oggetti', 'oggetti'), ('stanze', 'Le stanze', 'stanze'),
     ('studio', 'Lo studio', 'studio'), ('offerte', 'Offerte', 'offerte'), ('assistenza', 'Assistenza', 'assistenza'),
     ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'), ('cassa', 'La cassa', 'cassa'),
     ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Instrument Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Syne\', \'Helvetica Neue\', sans-serif',
    '\'Martian Mono\', ui-monospace, Menlo, monospace', ['Syne', 'Instrument Sans', 'Martian Mono'], corsivo_di=[],
    peso_titoli='600', raggio=4, grana=0, titoli_line='1.0', titoli_spazio='-0.02em')
tema_negozio(THEME, NAME)
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # anteprime con la vetrina delle offerte (1° nov 2026)
pannelli(THEME, NAME, {'Tutti gli oggetti': ('pannello-reparti', 'Pannello del negozio'), 'Offerte': ('pannello-offerte', 'Pannello delle offerte'), 'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'ombra'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'stanze.json': build_stanze(), 'studio.json': build_studio(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    dentro='L’oggetto, la scheda del laboratorio che l’ha fatto, l’imballo di carta e paglia di legno.')


if __name__ == '__main__':
    main()
