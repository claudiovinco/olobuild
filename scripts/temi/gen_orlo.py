# -*- coding: utf-8 -*-
"""Genera il tema «Orlo» (id orlo): il negozio online di un marchio di moda milanese — abiti, camicie, maglieria,
pantaloni, capispalla e accessori in lino, lana e seta cuciti in sei laboratori italiani. Quarantadue capi a stagione,
niente saldi di fine stagione: c'è l'Archivio, i capi delle stagioni passate fino al −28%.

Tema chiaro da rivista: carta avorio, inchiostro, il cobalto di Orlo sui pulsanti e il vermiglio dei prezzi scontati.
Titoli in Bodoni Moda, testo in Manrope, dati in DM Mono. Angoli vivi, foto in verticale 3:4, la seconda foto al
passaggio del mouse.

Apertura: la vetrina delle offerte, come nei negozi di moda online: una fascia bassa che scorre da sola (i
capispalla dell’Archivio, gli abiti, la maglieria nuova), sotto le quattro schede coi prodotti veri; il mega menu apre i
pannelli della collezione, delle novità e delle offerte. Cursore: il metro da sarta.

WooCommerce: le pagine del negozio sono template di OLObuild (Template WooCommerce): la collezione coi filtri per
taglia e prezzo, la scheda prodotto con le taglie e il video del capo nella galleria, il carrello, la cassa a passi,
l'account, i preferiti. I prodotti di prova: scripts/temi/prodotti/orlo.json (prodotti-demo.php).
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio  # noqa: E402

ID = 'orlo'
NAME = 'Orlo'

HEX = {
    'primary': '#2B3BE0', 'primary_contrast': '#FFFFFF', 'secondary': '#C9B79C', 'secondary_contrast': '#191714',
    'accent': '#C93A22', 'dark': '#141210', 'light': '#FFFDF9', 'muted': '#EAE4DA', 'muted_contrast': '#191714',
    'text': '#191714', 'text_muted': '#665F56', 'background': '#F4F0EA', 'border': '#D6CDBF', 'link': '#2B3BE0',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'lookbook', 'atelier', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'cura@orlo.example', '+39 02 0000 1427', 'tel:+390200001427'

V_PASSERELLA, V_TAILLEUR, V_SALVIA, V_CORALLO, V_COBALTO, V_ATELIER, V_TESSUTO = (
    'pexels-video-9167971', 'pexels-video-9167976', 'pexels-video-8531225', 'pexels-video-8928272', 'pexels-video-15017748',
    'pexels-video-6460176', 'pexels-video-30104895')
VIDEOS = [V_PASSERELLA, V_TAILLEUR, V_SALVIA, V_CORALLO, V_COBALTO, V_ATELIER, V_TESSUTO]
PHOTOS = [
    'pexels-8053672.jpg', 'pexels-8053683.jpg', 'pexels-8190290.jpg', 'pexels-8190278.jpg', 'pexels-8484131.jpg',
    'pexels-19771938.jpg', 'pexels-19771940.jpg', 'pexels-12466602.jpg', 'pexels-20117725.jpg', 'pexels-11411520.jpg',
    'pexels-31747192.jpg', 'pexels-13171461.jpg', 'pexels-37524408.jpg', 'pexels-31713105.jpg', 'pexels-18031036.jpg',
    'pexels-18031037.jpg', 'pexels-5149729.jpg', 'pexels-8367456.jpg', 'pexels-14642651.jpg', 'pexels-14641437.jpg',
    'pexels-7585220.jpg', 'pexels-7585736.jpg', 'pexels-6996083.jpg', 'pexels-5789010.jpg', 'pexels-14641596.jpg',
    'pexels-39457696.jpg', 'pexels-39457561.jpg', 'pexels-28168172.jpg', 'pexels-39457729.jpg', 'pexels-9489467.jpg',
    'pexels-19169191.jpg', 'pexels-12989422.jpg', 'pexels-31153342.jpg', 'pexels-9968534.jpg', 'pexels-9968536.jpg',
    'pexels-19501550.jpg', 'pexels-8125851.jpg', 'pexels-8125855.jpg', 'pexels-8125854.jpg', 'pexels-13025817.jpg',
    'pexels-10259873.jpg', 'pexels-30725738.jpg', 'pexels-27988923.jpg', 'pexels-7588570.jpg', 'pexels-7318681.jpg',
    'pexels-4646905.jpg', 'pexels-12251878.jpg', 'pexels-3984886.jpg', 'pexels-3984875.jpg', 'pexels-3119164.jpg',
    'pexels-7147458.jpg', 'pexels-8396317.jpg', 'pexels-9594685.jpg', 'pexels-20531147.jpg', 'pexels-36293915.jpg',
    'pexels-9167976-a.jpg', 'pexels-9167976-b.jpg', 'pexels-8531225-a.jpg', 'pexels-8531225-b.jpg', 'pexels-8928272-a.jpg',
    'pexels-8928272-b.jpg', 'pexels-15017748-a.jpg', 'pexels-15017748-b.jpg', 'pexels-35228822.jpg',
]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='500', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
VERMIGLIO = C['acc2']
TITOLI, MONO = 'Bodoni Moda', 'DM Mono'
N = Negozio(K, NAME, P, raggio=0, ratio='3-4', card='none', hover='swap', testata_scura=False,
            consegna='Spedizione gratuita da 150 €', evidenza='Scelto in atelier', peso_titoli='500')

CATEGORIE = [  # slug, nome, descrizione, foto
    ('abiti', 'Abiti', 'Lino, crêpe e seta: gli abiti per ogni giorno e per le sere.', 'pexels-8053683.jpg'),
    ('camicie', 'Camicie', 'Popeline, lino e seta, ampie o dritte.', 'pexels-31747192.jpg'),
    ('maglieria', 'Maglieria', 'Cashmere, merino e cotone lavorati a Biella.', 'pexels-14641437.jpg'),
    ('pantaloni', 'Pantaloni', 'Palazzo, dritti, jeans: la vita alta di Orlo.', 'pexels-39457561.jpg'),
    ('capispalla', 'Capispalla', 'Cappotti, trench e giacche in lana e cotone.', 'pexels-9968534.jpg'),
    ('accessori-moda', 'Accessori', 'Borse, mocassini e stivaletti in pelle.', 'pexels-8125851.jpg'),
]
TAGLIE = ['XS', 'S', 'M', 'L', 'XL']
NUMERI = ['36', '37', '38', '39', '40', '41']
# sku, nome, slug, categoria, prezzo, sconto, scorta, voto, recensioni, evidenza, foto, punti, testo, varianti, video
PRODOTTI = [
    ('ORL-001', 'Abito Vela in lino, arancio', 'abito-vela-lino', 'abiti', 159, 119, 22, 4.7, 186, True,
     ['pexels-8053672.jpg', 'pexels-8053683.jpg'], ['Lino lavato, morbido dal primo giorno', 'Allacciato in vita, scollo a V',
     'Lunghezza al polpaccio', 'Cucito a Carpi'], 'L’abito dell’estate che dura fino a ottobre: il lino lavato non si stropiccia '
     'più di così e il nodo in vita si stringe come si vuole.', (TAGLIE, ['L']), None),
    ('ORL-002', 'Abito Riva, lungo in lino naturale', 'abito-riva-lino', 'abiti', 189, 0, 18, 4.8, 94, True,
     ['pexels-8190290.jpg', 'pexels-8190278.jpg'], ['Lino pesante, tinta naturale', 'Fiocco sulla schiena', 'Tasche nelle cuciture',
     'Cucito a Carpi'], 'Lungo, dritto, col fiocco dietro: si porta coi sandali e col cardigan quando la sera rinfresca.',
     (TAGLIE, ['XS']), None),
    ('ORL-003', 'Abito Sottoveste in viscosa, salvia', 'abito-sottoveste-salvia', 'abiti', 139, 0, 25, 4.6, 72, False,
     ['pexels-8531225-a.jpg', 'pexels-8531225-b.jpg'], ['Viscosa fluida che si muove con te', 'Spalline regolabili',
     'Taglio in sbieco', 'Il video nella galleria'], 'Il taglio in sbieco lo fa scivolare: guardatelo nel video, quando gira.',
     (TAGLIE, []), V_SALVIA),
    ('ORL-004', 'Abito Corallo in crêpe', 'abito-corallo-crepe', 'abiti', 175, 139, 14, 4.5, 58, False,
     ['pexels-8928272-a.jpg', 'pexels-8928272-b.jpg'], ['Crêpe di viscosa opaco', 'Maniche a sbuffo', 'Schiena scoperta con laccio',
     'Fodera in cupro'], 'Rosso corallo, la schiena aperta e le maniche a sbuffo: l’abito per un matrimonio in campagna.',
     (TAGLIE, ['XL']), V_CORALLO),
    ('ORL-005', 'Abito Cobalto plissé', 'abito-cobalto-plisse', 'abiti', 210, 0, 16, 4.9, 41, True,
     ['pexels-15017748-a.jpg', 'pexels-15017748-b.jpg'], ['Plissé permanente, non si stira', 'Il cobalto di Orlo',
     'Lungo alla caviglia', 'Si piega in valigia'], 'Il blu che è il nostro colore, plissettato a Como: cammina e si apre come '
     'un ventaglio.', (TAGLIE, []), V_COBALTO),
    ('ORL-006', 'Abito Notte in seta', 'abito-notte-seta', 'abiti', 290, 0, 9, 4.8, 33, False,
     ['pexels-12466602.jpg', 'pexels-20117725.jpg'], ['Raso di seta da 22 momme', 'Scollo a cappuccio', 'Taglio in sbieco',
     'Tinto a Como'], 'Il raso di seta che prende la luce: l’abito delle sere lunghe.', (TAGLIE, ['XS', 'XL']), None),
    ('ORL-007', 'Camicione in seta bianca', 'camicione-seta-bianca', 'camicie', 245, 0, 12, 4.7, 49, False,
     ['pexels-19771938.jpg', 'pexels-19771940.jpg'], ['Crêpe de chine di seta', 'Fiocco al collo', 'Lungo, si porta anche aperto',
     'Bottoni in madreperla'], 'Si porta come abito o aperto sui pantaloni: la seta bianca va con tutto.', (TAGLIE, []), None),
    ('ORL-008', 'Camicia Ampia in popeline', 'camicia-ampia-popeline', 'camicie', 95, 0, 40, 4.6, 312, True,
     ['pexels-31747192.jpg', 'pexels-13171461.jpg'], ['Popeline di cotone organico', 'Spalla scesa, maniche lunghe',
     'Si porta dentro o fuori', 'Lavabile a 40°'], 'La camicia bianca di Orlo: ampia, croccante, con le maniche da arrotolare.',
     (TAGLIE, ['M']), None),
    ('ORL-009', 'Camicia Studio, collo alto', 'camicia-studio', 'camicie', 110, 79, 21, 4.4, 88, False,
     ['pexels-37524408.jpg', 'pexels-31713105.jpg'], ['Cotone e seta', 'Collo alto con laccio', 'Polsini lunghi',
     'Dall’Archivio, −28%'], 'Il collo alto e i polsini lunghi di una camicia d’atelier, dall’Archivio della scorsa stagione.',
     (TAGLIE, ['XS']), None),
    ('ORL-010', 'Giacca in lino, uomo', 'giacca-lino-uomo', 'capispalla', 235, 0, 11, 4.7, 64, False,
     ['pexels-18031036.jpg', 'pexels-18031037.jpg'], ['Lino e cotone, sfoderata', 'Due bottoni in corozo', 'Tasche applicate',
     'Spalla morbida'], 'La giacca da portare con la camicia aperta: sfoderata, leggera, si stropiccia nel modo giusto.',
     (['S', 'M', 'L', 'XL'], ['S']), None),
    ('ORL-011', 'Camicia coreana in lino, uomo', 'camicia-coreana-lino-uomo', 'camicie', 89, 0, 30, 4.5, 140, False,
     ['pexels-5149729.jpg', 'pexels-8367456.jpg'], ['Lino lavato', 'Collo alla coreana', 'Tre bottoni', 'Cucita a Carpi'],
     'Senza colletto, col lino lavato: l’estate in città.', (['S', 'M', 'L', 'XL'], []), None),
    ('ORL-012', 'Maglione Nuvola in cashmere', 'maglione-nuvola-cashmere', 'maglieria', 189, 0, 26, 4.9, 221, True,
     ['pexels-14642651.jpg', 'pexels-14641437.jpg', 'pexels-14641596.jpg'], ['Cashmere rigenerato e lana', 'Filato a Biella',
     'Girocollo morbido', 'Tre colori'], 'Leggero come il nome, caldo come un cappotto: il maglione che si regala.',
     (TAGLIE, []), None),
    ('ORL-013', 'Cardigan Filo rosso', 'cardigan-filo-rosso', 'maglieria', 149, 119, 17, 4.6, 97, False,
     ['pexels-7585220.jpg', 'pexels-7585736.jpg'], ['Cotone e alpaca', 'Il bordo a contrasto, rosso', 'Bottoni in legno',
     'Lavorato a mano'], 'Il cardigan col bordo rosso, fatto a mano da una magliaia di Biella.', (TAGLIE, ['L']), None),
    ('ORL-014', 'Dolcevita in merino', 'dolcevita-merino', 'maglieria', 129, 0, 34, 4.7, 156, False,
     ['pexels-6996083.jpg', 'pexels-5789010.jpg'], ['Merino extrafine', 'Collo alto rovesciato', 'Polsi a coste lunghi',
     'Non pizzica'], 'Il dolcevita che non pizzica: sotto la giacca, sotto il cappotto, da solo.', (TAGLIE, []), None),
    ('ORL-015', 'Pantalone Palazzo, salvia', 'pantalone-palazzo-salvia', 'pantaloni', 119, 0, 19, 4.5, 63, False,
     ['pexels-39457696.jpg'], ['Viscosa stampata', 'Vita alta elasticizzata', 'Gamba ampia', 'Tasche laterali'],
     'Il palazzo stampato che cammina con te: vita alta, gamba larga.', (TAGLIE, ['XS']), None),
    ('ORL-016', 'Pantalone Ampio, nero', 'pantalone-ampio-nero', 'pantaloni', 129, 99, 24, 4.6, 118, False,
     ['pexels-39457561.jpg', 'pexels-28168172.jpg'], ['Fresco di lana', 'Piega stirata', 'Vita alta', 'Orlo da adattare'],
     'Il pantalone nero che sta bene con tutto: la piega stirata, la vita alta.', (TAGLIE, []), None),
    ('ORL-017', 'Jeans Dritto', 'jeans-dritto', 'pantaloni', 99, 0, 45, 4.4, 402, False,
     ['pexels-39457729.jpg'], ['Denim giapponese 13 oz', 'Gamba dritta', 'Vita alta', 'Si scurisce col tempo'],
     'Il jeans dritto di denim giapponese: rigido il primo mese, poi è vostro.', (['XS', 'S', 'M', 'L'], []), None),
    ('ORL-018', 'Cappotto Cammello', 'cappotto-cammello', 'capispalla', 390, 290, 8, 4.8, 76, True,
     ['pexels-19169191.jpg'], ['Lana e cashmere', 'Cintura in vita', 'Lungo al polpaccio', 'Dall’Archivio, −26%'],
     'Il cappotto cammello con la cintura: quello che si tiene vent’anni.', (TAGLIE, ['XS', 'XL']), None),
    ('ORL-019', 'Cappotto Tabacco', 'cappotto-tabacco', 'capispalla', 360, 0, 10, 4.6, 39, False,
     ['pexels-12989422.jpg'], ['Lana cotta', 'Revers ampi', 'Tasche a filetto', 'Fodera in cupro'],
     'Lana cotta color tabacco: caldo e leggero, senza spalline.', (TAGLIE, []), None),
    ('ORL-020', 'Trench Lungo', 'trench-lungo', 'capispalla', 320, 0, 13, 4.9, 88, True,
     ['pexels-9968534.jpg', 'pexels-9968536.jpg'], ['Gabardine di cotone idrorepellente', 'Doppiopetto', 'Cintura e polsini',
     'Lungo alla caviglia'], 'Il trench lungo color sabbia: la pioggia di Milano non lo bagna.', (TAGLIE, ['M']), None),
    ('ORL-021', 'Giacca Tailleur, fucsia', 'giacca-tailleur-fucsia', 'capispalla', 280, 0, 12, 4.7, 52, True,
     ['pexels-9167976-a.jpg', 'pexels-9167976-b.jpg'], ['Crêpe di lana fucsia', 'Spalla costruita', 'Un bottone',
     'Il video nella galleria'], 'Il fucsia della passerella di Orlo: guardatelo camminare nel video.', (TAGLIE, ['XL']),
     V_TAILLEUR),
    ('ORL-022', 'Bauletto Paisley', 'bauletto-paisley', 'accessori-moda', 175, 0, 15, 4.6, 44, False,
     ['pexels-8125851.jpg', 'pexels-8125855.jpg', 'pexels-8125854.jpg'], ['Tela jacquard e pelle', 'Due manici e tracolla',
     'Zip in ottone', 'Fodera in cotone'], 'Il bauletto in tela paisley, coi manici in pelle: da città e da weekend.', None, None),
    ('ORL-023', 'Tote in tela e pelle', 'tote-tela-pelle', 'accessori-moda', 145, 109, 20, 4.5, 61, False,
     ['pexels-13025817.jpg'], ['Tela di cotone pesante', 'Manici in vacchetta', 'Tasca interna con zip', 'Ci sta il computer'],
     'La borsa di tutti i giorni: grande, robusta, col computer dentro.', None, None),
    ('ORL-024', 'Mocassini in pelle spazzolata', 'mocassini-pelle', 'accessori-moda', 165, 0, 18, 4.7, 83, False,
     ['pexels-10259873.jpg'], ['Vitello spazzolato nero', 'Suola in cuoio', 'Cuciti a mano nelle Marche', 'Calzata regolare'],
     'I mocassini neri lucidi, cuciti a mano nelle Marche.', (NUMERI, ['36']), None),
    ('ORL-025', 'Stivaletti texani', 'stivaletti-texani', 'accessori-moda', 210, 169, 9, 4.6, 47, False,
     ['pexels-30725738.jpg'], ['Pelle scamosciata cuoio', 'Punta a mandorla', 'Tacco da 4 cm', 'Cuciti nelle Marche'],
     'Gli stivaletti color cuoio che vanno col lino e col jeans.', (NUMERI, ['41']), None),
]
LEGAMI = {'ORL-001': (['ORL-005'], ['ORL-023', 'ORL-013']), 'ORL-002': (['ORL-006'], ['ORL-013', 'ORL-024']),
          'ORL-003': (['ORL-006'], ['ORL-014', 'ORL-025']), 'ORL-004': (['ORL-005'], ['ORL-022', 'ORL-024']),
          'ORL-005': ([], ['ORL-022', 'ORL-024']), 'ORL-006': ([], ['ORL-022', 'ORL-020']),
          'ORL-007': (['ORL-006'], ['ORL-016', 'ORL-024']), 'ORL-008': (['ORL-007'], ['ORL-017', 'ORL-023']),
          'ORL-009': (['ORL-007'], ['ORL-016', 'ORL-018']), 'ORL-010': ([], ['ORL-011', 'ORL-024']),
          'ORL-011': (['ORL-010'], ['ORL-017', 'ORL-023']), 'ORL-012': ([], ['ORL-016', 'ORL-018']),
          'ORL-013': (['ORL-012'], ['ORL-008', 'ORL-017']), 'ORL-014': (['ORL-012'], ['ORL-016', 'ORL-019']),
          'ORL-015': ([], ['ORL-008', 'ORL-025']), 'ORL-016': ([], ['ORL-014', 'ORL-024']),
          'ORL-017': ([], ['ORL-008', 'ORL-025']), 'ORL-018': ([], ['ORL-014', 'ORL-025']),
          'ORL-019': (['ORL-018'], ['ORL-012', 'ORL-025']), 'ORL-020': ([], ['ORL-008', 'ORL-023']),
          'ORL-021': ([], ['ORL-016', 'ORL-024']), 'ORL-022': ([], ['ORL-024', 'ORL-020']),
          'ORL-023': (['ORL-022'], ['ORL-008', 'ORL-017']), 'ORL-024': ([], ['ORL-016', 'ORL-023']),
          'ORL-025': ([], ['ORL-017', 'ORL-018'])}
RECENSIONI = [
    [5, 'Veste come in foto. Ho preso la mia taglia solita ed è perfetto.'],
    [5, 'Il tessuto è ancora più bello dal vivo. Arrivato in due giorni, incartato con cura.'],
    [4, 'Bellissimo, un po’ lungo per me: me l’hanno accorciato gratis in atelier.'],
    [5, 'Lo porto da tre stagioni ed è come nuovo. Ne ho preso un secondo.'],
    [4, 'Taglia un po’ abbondante: con la guida delle taglie avrei preso la S.'],
    [5, 'Cambio di taglia fatto in una settimana, senza spese. Gentilissimi.'],
    [5, 'Si vede che è cucito bene: le cuciture dentro sono rifinite come fuori.'],
    [4, 'Colore leggermente più scuro che in foto, ma mi piace di più così.'],
]
NOMI = ['Giulia R.', 'Martina S.', 'Elena B.', 'Chiara T.', 'Sara L.', 'Francesca M.', 'Alessandro P.', 'Valentina C.',
        'Marco D.', 'Irene F.', 'Beatrice G.', 'Luca V.']
PROMESSE = [('truck', 'Spedizione gratuita da 150 €'), ('rotate-ccw', 'Reso e cambio taglia gratis in 30 giorni'),
            ('scissors', 'Orlo su misura in atelier'), ('leaf', 'Cucito in Italia, in sei laboratori')]
CONTATTI = [('map-pin', 'Atelier · via Tortona 27, Milano', P['atelier'] + '#atelier'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


CAT_DI = {}  # slug del prodotto → categoria, per i link dei cartellini (i prodotti esistono solo nella demo)


def prodotto_url(slug):
    return N.cat_url(CAT_DI[slug])


# ══════════════════════════════════════════════════════════════════════════════
#  TESTATA · PIEDE
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    return N.testata(Tpl(ID, 'header'), 'orlo', icona='', ricerca='Cerca un capo, un tessuto, un colore…',
                     parole='un abito in lino\nil cappotto cammello\nun maglione in cashmere\nla camicia bianca',
                     annuncio=('Archivio: i capi delle stagioni passate fino al −28%, fino a domenica.', 'Scopri l’Archivio',
                               P['offerte']), logo_size=34)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'orlo', '', 'Abiti che durano: lino, lana e seta cuciti in sei laboratori italiani, '
                   'quarantadue capi a stagione e un atelier a Milano dove si accorciano gli orli.',
                   [('Negozio', [('Nuovi arrivi', P['negozio']), ('Abiti', N.cat_url('abiti')),
                                 ('Maglieria', N.cat_url('maglieria')), ('Capispalla', N.cat_url('capispalla')),
                                 ('L’Archivio', P['offerte'])]),
                    ('Orlo', [('Lookbook', P['lookbook']), ('L’atelier', P['atelier']), ('I preferiti', P['preferiti']),
                              ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni', P['assistenza'] + '#spedizioni'), ('Resi e cambi', P['assistenza'] + '#spedizioni'),
                               ('Guida alle taglie', P['atelier'] + '#taglie'), ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Orlo s.r.l. · Milano · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera di Orlo', 'La nuova collezione un giorno prima.',
                              'Una lettera al mese: i capi nuovi, l’Archivio e i laboratori che li cuciono.',
                              'Fatto: la prossima lettera arriva coi capi nuovi.'),
                   social=[('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')])


# ══════════════════════════════════════════════════════════════════════════════
#  APERTURA · le colonne in controsenso
# ══════════════════════════════════════════════════════════════════════════════
# (Dal 1° nov 2026 la home apre con la vetrina delle offerte: questa apertura non è più usata.)
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
SINISTRA = [('pexels-8053672.jpg', 'Abito Vela', '119 €', 'abito-vela-lino'),
            ('pexels-14642651.jpg', 'Maglione Nuvola', '189 €', 'maglione-nuvola-cashmere'),
            ('pexels-9968534.jpg', 'Trench Lungo', '320 €', 'trench-lungo'),
            ('pexels-31747192.jpg', 'Camicia Ampia', '95 €', 'camicia-ampia-popeline')]
DESTRA = [('pexels-12466602.jpg', 'Abito Notte', '290 €', 'abito-notte-seta'),
          ('pexels-19169191.jpg', 'Cappotto Cammello', '290 €', 'cappotto-cammello'),
          ('pexels-8190290.jpg', 'Abito Riva', '189 €', 'abito-riva-lino'),
          ('pexels-7585220.jpg', 'Cardigan Filo rosso', '119 €', 'cardigan-filo-rosso')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    livelli = [
        # la sfumatura scura sale dal fondo solo sotto i testi, il video resta pieno sopra
        K.livello('or-velo', tipo='shape', x=0, y=34, w=100, h=66, entra='fadeIn', ritardo=0, durata=800,
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 34},
                  responsive={'mobile': {'y': 44, 'height': 56}}),
    ]
    # due colonne di look: la sinistra sale, la destra scende, ognuna col cartellino del capo
    larg, alto, gap = 236.0, 315.0, 16.0
    lm, am, gm = 282.0, 376.0, 14.0
    corsa, corsa_m = 760, 560
    for lato, voci, x0, xm0, verso in (('s', SINISTRA, 44.0, 24.0, -1), ('d', DESTRA, W - 44 - larg, WM - 24 - lm, 1)):
        for k, (foto, nome, prezzo, slug) in enumerate(voci):
            y = 36 + (alto + gap) * k if verso < 0 else 36 - (alto + gap) * (len(voci) - 1 - k) + (alto + gap)
            ym = 30 + (am + gm) * k if verso < 0 else 30 - (am + gm) * (len(voci) - 1 - k) + (am + gm)
            sm = {'inizio': 0, 'fine': 100, 'da': {}, 'a': {'y': verso * corsa}}
            a = _pc(x0, y, larg, alto, W, H)
            b = _pc(xm0, ym, lm, am, WM, HM)
            livelli.append(K.livello('or-%s%d' % (lato, k), tipo='image', imageSrc=K.img(foto), objectFit='cover',
                                     objectPosition='center 30%', x=a[0], y=a[1], w=a[2], h=a[3], entra='fadeIn',
                                     ritardo=200 + 120 * k, durata=900, scrollMove=sm,
                                     responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
            a = _pc(x0 + 10, y + alto - 40, larg - 20, 30, W, H)
            b = _pc(xm0 + 10, ym + am - 44, lm - 20, 34, WM, HM)
            livelli.append(K.livello('or-%sc%d' % (lato, k), tipo='button', contenuto='%s · %s' % (nome, prezzo),
                                     x=a[0], y=a[1], w='auto', size=11, peso='500', colore=C['testo'], bgColor=C['chiaro'],
                                     padding=8, borderRadius=0, fontFamily=MONO, letterSpacing=0.5, entra='fadeIn',
                                     ritardo=500 + 120 * k, buttonUrl=prodotto_url(slug),
                                     hover={'bgColor': C['acc'], 'color': C['on_acc'], 'duration': 200}, scrollMove=sm,
                                     responsive={'mobile': {'visible': False}}))
    # il bollino dell'Archivio: gira piano mentre si scorre
    a = _pc(W / 2 - 64, 64, 128, 128, W, H)
    b = _pc(WM / 2 - 70, 1040, 140, 140, WM, HM)
    livelli.append(K.livello('or-bollino', contenuto='Archivio<br><strong style="font-size:1.6em">−30%</strong>', x=a[0], y=a[1],
                             w=a[2], h=a[3], size=13, peso='500', colore=C['chiaro'], bgColor=VERMIGLIO, borderRadius=999,
                             align='center', textValign='center', lineHeight=1.1, fontFamily=MONO, letterSpacing=1,
                             textTransform='uppercase', entra='zoomIn', ritardo=900, durata=700,
                             scrollMove={'inizio': 0, 'fine': 100, 'da': {'r': -12, 's': 1}, 'a': {'r': 12, 's': 1.08}},
                             responsive={'mobile': {'x': b[0], 'y': 47, 'width': b[2], 'height': b[3], 'fontSize': m(11)},
                                         'tablet': {'y': 9}}))
    livelli += [
        K.livello('or-o', contenuto='Orlo · Milano · autunno–inverno 2026', x=25, y=45, w=50, size=12, peso='500',
                  colore=mix(C['chiaro'], 90), align='center', letterSpacing=3, textTransform='uppercase', fontFamily=MONO,
                  ritardo=200, responsive={'mobile': {'fontSize': m(11), 'y': 60, 'x': 6, 'width': 88}, 'tablet': {'y': 43}}),
        K.livello('or-t', contenuto='Abiti che <em>durano.</em>', tag='h1', x=22, y=50.5, w=56, size=80, peso='500',
                  colore=C['chiaro'], align='center', fontFamily=TITOLI, lineHeight=1.0, entra='maskFromBottom', ritardo=300,
                  durata=1200, textShadow={'x': 0, 'y': 2, 'blur': 26, 'color': mix(C['nero'], 60)},
                  responsive={'mobile': {'fontSize': m(42), 'y': 63, 'x': 6, 'width': 88}, 'tablet': {'y': 49}}),
        K.livello('or-s', contenuto='Lino, lana e seta cuciti in sei laboratori italiani. Quarantadue capi a stagione, '
                  'niente saldi: c’è l’Archivio.', x=30, y=63.5, w=40, size=16, peso='400', colore=mix(C['chiaro'], 90),
                  align='center', lineHeight=1.6, ritardo=700,
                  responsive={'mobile': {'fontSize': m(13), 'y': 70.5, 'x': 8, 'width': 84}}),
        K.livello('or-b', tipo='button', contenuto='La collezione', x=37.4, y=73, w='auto', size=14, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=16, borderRadius=0, ritardo=1000, buttonUrl=P['negozio'],
                  hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                  responsive={'tablet': {'x': 34}, 'mobile': {'y': 78.5, 'x': 8, 'fontSize': m(12)}}),
        K.livello('or-l', tipo='button', contenuto='L’Archivio −30%', x=51.2, y=73, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=0, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), ritardo=1150, buttonUrl=P['offerte'],
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 52}, 'mobile': {'y': 78.5, 'x': 52, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('or-1', K.sfondo_video(V_PASSERELLA, overlay=0.12, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2000, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def nastro(t):
    return t.classic('marquee', {
        'content_type': 'text', 'text_items': 'Spedizione gratuita da 150 €\nReso e cambio taglia in 30 giorni\nCucito in Italia\n'
                                              'L’Archivio a −30%\nOrlo su misura in atelier',
        'separator': '✦', 'speed': 40, 'direction': 'left', 'pause_hover': True, 'drag_scroll': False, 'velocity_skew': True,
        'font_style': 'italic', 'font_family': 'heading', 'font_size': 30, 'text_color': C['testo'],
        'separator_color': C['acc'], 'separator_size': 16, 'bg_color': 'transparent', 'height': 76, 'full_width': True,
        'border_top': 1, 'border_bottom': 1, 'border_color': C['filo'], 'gap': 36})


def look(t):
    """Compra il look: la foto dell'editoriale coi punti sui capi, ognuno col prezzo e il link alla scheda."""
    return t.bare('hotspot', {
        'image': K.img('pexels-7588570.jpg'), 'image_alt': 'Una modella in camicia di lino seduta fra i teli dello studio',
        'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/5', 'object_fit': 'cover', 'object_position': 'center center',
        'border_radius': radius(0), 'preset': 'custom', 'marker_style': 'dot', 'marker_color': C['chiaro'],
        'marker_text_color': C['nero'], 'marker_size': 26, 'pulse_animation': True, 'tooltip_bg': C['chiaro'],
        'tooltip_color': C['testo'], 'tooltip_width': 240,
        'markers': [
            {'pos_x': 52, 'pos_y': 38, 'title': 'Camicia Ampia in popeline', 'description': '95 € · popeline organico',
             'icon': '', 'image': K.img('pexels-31747192.jpg'), 'link': prodotto_url('camicia-ampia-popeline'),
             'link_label': 'Vedi il capo', 'tooltip_position': 'right'},
            {'pos_x': 46, 'pos_y': 70, 'title': 'Pantalone Ampio, nero', 'description': '99 € invece di 129 €',
             'icon': '', 'image': K.img('pexels-39457561.jpg'), 'link': prodotto_url('pantalone-ampio-nero'),
             'link_label': 'Vedi il capo', 'tooltip_position': 'right'},
            {'pos_x': 22, 'pos_y': 30, 'title': 'I teli dello studio', 'description': 'Il lino grezzo da cui nascono i capi',
             'icon': '', 'image': '', 'link': P['atelier'], 'link_label': 'L’atelier', 'tooltip_position': 'right'},
        ]})


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
# Dal 1° nov 2026 il negozio apre come quelli grandi: una fascia bassa di offerte che scorre da sola invece
# dell'apertura a tutto schermo, e il mega menu racconta le offerte e le novità nei suoi pannelli.
LARGO = 0.45  # la larghezza media di una lettera dei titoli (per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, bollino_giro=-8, largo=LARGO)
    return N.vetrina(t, [
        N.offerta('or-o1', K.sfondo_video(V_PASSERELLA, overlay=0.12), 'L’Archivio · fino a domenica',
                  'I capispalla, fino al −26%.', 'Il cappotto Cammello a 290 € invece di 390: lana di Biella, cucito a Milano.',
                  'I capispalla', N.cat_url('capispalla'), bollino='−26%', nota='L’orlo su misura in atelier, gratis', primo=True, **f),
        N.offerta('or-o2', K.sfondo_foto('pexels-7318681.jpg', overlay=0.12, scala=1.06), 'Gli abiti · le offerte della settimana',
                  'Gli abiti, fino al −25%.', 'Il Vela in lino a 119 €, il Corallo in crêpe a 139 €.', 'Gli abiti',
                  N.cat_url('abiti'), bollino='−25%', nota='Reso e cambio taglia gratis in 30 giorni', lato='destra', **f),
        N.offerta('or-o3', K.sfondo_foto('pexels-9594685.jpg', overlay=0.12, scala=1.06), 'Nuovi arrivi · la maglieria',
                  'Cashmere e merino di Biella.', 'Lavorati a mano, appena arrivati. Il cardigan Filo rosso a 119 € invece di 149.',
                  'La maglieria', N.cat_url('maglieria'), bollino='−20%', nota='Spedizione gratuita da 150 €', **f),
    ], transizione='curtain3D')


# le schede sotto la vetrina: «promo» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='promo',
                    promo=('Le giacche,', 'spalle morbide.', 'Il tailleur fucsia, il lino da uomo: da 235 €, l’orlo su misura gratis.', 'I capispalla', N.cat_url('capispalla'), 'pexels-4646905.jpg'), voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Appena arrivati', {'orderby': 'date'}, 'Vedi tutti', P['negozio']),
        ('Sotto i 100 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=100'),
        ('I più amati', {'orderby': 'rating'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'La collezione', 'Sei categorie, la taglia giusta.', ('L’Archivio,', 'fino al −28%.', 'I capi delle stagioni passate.', 'Le offerte', P['offerte'], 'pexels-19169191.jpg'))


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'L’Archivio', 'Le offerte della settimana', 'Quelle di oggi finiscono tra:',
                              ('Il cappotto Cammello,', '290 €.', 'Lana di Biella, era 390 €.', 'I capispalla', N.cat_url('capispalla'), 'pexels-9968534.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Nuovi arrivi', 'Appena usciti dal laboratorio',
                             [(n, N.cat_url(s)) for s, n in (('abiti', 'Abiti'), ('camicie', 'Camicie'), ('maglieria', 'Maglieria'), ('pantaloni', 'Pantaloni'), ('capispalla', 'Capispalla'), ('accessori-moda', 'Accessori'))],
                             ('La maglieria,', 'appena arrivata.', 'Cashmere e merino di Biella.', 'La maglieria', N.cat_url('maglieria'), 'pexels-14641437.jpg'))


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([nastro(t)], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        N.testata_sezione(t, 'Le categorie', 'Cosa cerchi oggi?', P['negozio'], 'Tutta la collezione'),
        t.one([N.categorie(t, colonne=6, raggio_foto=0, ratio='3-4')]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        N.testata_sezione(t, 'Nuovi arrivi', 'Appena usciti dal laboratorio', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, posts_per_page=10, columns=4, layout='carousel', orderby='date')]),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 64}))
    out.append(t.section([
        t.row([t.col([look(t)], wm='1-2', ws='1-1'),
               t.col([N.occhiello(t, 'Compra il look'), N.titolo(t, 'La camicia bianca,<br><em>il pantalone nero.</em>', size=52),
                      N.testo(t, 'Il look dell’editoriale d’autunno, capo per capo: toccate i punti sulla foto per il prezzo '
                                 'e la scheda. Tutto si porta anche col cardigan Filo rosso.', size=17),
                      N.prodotti(t, posts_per_page=2, columns=2, columns_tablet=2, orderby='rating', category='camicie',
                                 show_add_to_cart=False, show_rating=False),
                      N.pulsante(t, 'Il lookbook', P['lookbook'], pieno=False, pad_top=20)], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'L’Archivio,', 'fino al −28%.', 'I capi delle stagioni passate, finché ci sono le taglie.',
                               'Entra nell’Archivio', P['offerte'], K.photo_bg('pexels-19169191.jpg', position='center 25%'))],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Il lino,', 'tutto l’anno.', 'D’estate da solo, d’inverno sotto il cappotto.',
                               'Gli abiti in lino', N.cat_url('abiti'), K.video_bg(V_SALVIA, position='center 30%'))],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        N.testata_sezione(t, 'I più amati', 'Quelli che si ricomprano', P['negozio']),
        t.one([N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 72}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'L’Archivio chiude tra', color=mix(C['chiaro'], 80)),
                      N.titolo(t, 'Poi i prezzi <em>tornano pieni.</em>', size=48, color=C['chiaro']),
                      N.testo(t, 'Ogni domenica a mezzanotte l’Archivio si rinnova: capi nuovi, taglie nuove.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=46, giorni=True), N.link(t, 'Entra nell’Archivio', P['offerte'], chiaro=True, align='left',
                                                              pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=40, valign='center'),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-3984875.jpg', 'La sarta di Orlo prova una giacca sul manichino', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'L’atelier'), N.titolo(t, 'Sei laboratori,<br><em>nessun magazzino pieno.</em>', size=52),
                      N.testo(t, 'Cuciamo quarantadue capi a stagione in sei laboratori fra Carpi, Biella, Como e le Marche. '
                                 'Ne facciamo pochi e li rifacciamo quando finiscono: per questo non ci sono i saldi.',
                              size=17),
                      K.numeri(t, [('42', 'capi a stagione'), ('6', 'laboratori'), ('0', 'saldi di fine stagione')],
                               colonne=3, size=48),
                      N.pulsante(t, 'Come lavoriamo', P['atelier'], pieno=False, pad_top=24)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        K.centered(t, [t.classic('testimonial', {
            'quote': 'Ho accorciato l’orlo del trench in atelier mentre aspettavo un caffè. Tre anni dopo è ancora il cappotto '
                     'che metto di più.', 'author_name': 'Martina S.', 'author_role': 'Cliente dal 2023', 'avatar': '',
            'rating': 5, 'text_color': C['testo'], 'star_color': C['acc'], 'author_color': C['tenue'],
            'quote_accent_color': C['acc'], 'quote_font': 'heading', 'quote_size': 34, 'author_uppercase': True,
            'quote_uppercase': False, 'layout': 'editorial', 'show_line': True, 'line_color': C['acc']}, style={})]),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([t.classic('marquee', {
        'content_type': 'images', 'images': [{'url': K.img(f), 'alt': a} for f, a in (
            ('pexels-7318681.jpg', 'Una modella in maglia beige'), ('pexels-4646905.jpg', 'Due amiche in giacca nello studio'),
            ('pexels-8484131.jpg', 'Gli abiti appesi in atelier'), ('pexels-12251878.jpg', 'Una modella seduta, in jeans'),
            ('pexels-3119164.jpg', 'L’angolo dei capi in atelier'), ('pexels-20531147.jpg', 'I rotoli di cotone stampato'))],
        'speed': 30, 'direction': 'right', 'pause_hover': True, 'image_height': 280, 'image_fit': 'cover', 'gap': 14,
        'bg_color': 'transparent', 'full_width': True, 'velocity_skew': False})], anim=None)],
        width='fullbleed', padding='default', style={'padding_top': 24, 'padding_bottom': 0}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LOOKBOOK · ATELIER
# ══════════════════════════════════════════════════════════════════════════════

def build_lookbook():
    t = Tpl(ID, 'lookbook')
    out = [K.intro(t, 'Lookbook · autunno–inverno 2026', ['Tre look,', 'dodici capi.'],
                   'Fotografato nello studio di via Tortona, coi capi della collezione: toccate i punti per la scheda.',
                   bg=K.video_bg(V_COBALTO, overlay=30), size=96)]
    out.append(t.section([
        t.row([t.col([look(t)], wm='1-2', ws='1-1'),
               t.col([t.bare('hotspot', {
                   'image': K.img('pexels-9968534.jpg'), 'image_alt': 'Una donna in trench lungo cammina in città',
                   'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/5', 'object_fit': 'cover', 'border_radius': radius(0),
                   'preset': 'custom', 'marker_style': 'number', 'marker_color': C['acc'], 'marker_text_color': C['on_acc'],
                   'marker_size': 28, 'pulse_animation': True, 'tooltip_bg': C['chiaro'], 'tooltip_color': C['testo'],
                   'tooltip_width': 240,
                   'markers': [{'pos_x': 50, 'pos_y': 42, 'title': 'Trench Lungo', 'description': '320 € · gabardine idrorepellente',
                                'icon': '', 'image': K.img('pexels-9968536.jpg'), 'link': prodotto_url('trench-lungo'),
                                'link_label': 'Vedi il capo', 'tooltip_position': 'left'},
                               {'pos_x': 52, 'pos_y': 88, 'title': 'Mocassini in pelle', 'description': '165 €',
                                'icon': '', 'image': K.img('pexels-10259873.jpg'), 'link': prodotto_url('mocassini-pelle'),
                                'link_label': 'Vedi il capo', 'tooltip_position': 'left'}]})], wm='1-2', ws='1-1')],
              layout='50-50', gap=24, valign='start'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 80}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Componi il look'), N.titolo(t, 'Uno sopra,<br><em>uno sotto, uno ai piedi.</em>', size=48),
                      N.testo(t, 'Scorrete le frecce di ogni riga: la card somma il prezzo del look. Poi aggiungete i capi '
                                 'dalle loro schede, con la vostra taglia.', size=16)], wm='2-5', ws='1-1'),
               t.col([t.classic('lookbookmixer', {
                   'items': [{'step': 'Sopra', 'name': 'Camicia Ampia', 'price': '95', 'image': K.img('pexels-31747192.jpg'),
                              'color': C['chiaro']},
                             {'step': 'Sopra', 'name': 'Maglione Nuvola', 'price': '189', 'image': K.img('pexels-14642651.jpg'),
                              'color': C['sec']},
                             {'step': 'Sopra', 'name': 'Dolcevita in merino', 'price': '129', 'image': K.img('pexels-6996083.jpg'),
                              'color': C['sala']},
                             {'step': 'Sotto', 'name': 'Pantalone Ampio', 'price': '99', 'image': K.img('pexels-39457561.jpg'),
                              'color': C['nero']},
                             {'step': 'Sotto', 'name': 'Jeans Dritto', 'price': '99', 'image': K.img('pexels-39457729.jpg'),
                              'color': C['acc']},
                             {'step': 'Sotto', 'name': 'Palazzo salvia', 'price': '119', 'image': K.img('pexels-39457696.jpg'),
                              'color': C['sec']},
                             {'step': 'Ai piedi', 'name': 'Mocassini', 'price': '165', 'image': K.img('pexels-10259873.jpg'),
                              'color': C['nero']},
                             {'step': 'Ai piedi', 'name': 'Stivaletti texani', 'price': '169', 'image': K.img('pexels-30725738.jpg'),
                              'color': C['sec']}],
                   'currency': '€', 'card_title': 'Il tuo look', 'card_steps_label': 'capi', 'card_sub': 'Tre capi, il totale.',
                   'cta_text': 'Vai alla collezione', 'cta_url': P['negozio'], 'panel_bg': C['nero'], 'slot_bg': C['nero'],
                   'accent': C['sec'], 'accent_ink': C['nero'], 'name_color': C['chiaro'], 'price_color': mix(C['chiaro'], 76),
                   'line_color': mix(C['chiaro'], 14), 'show_swatch': False, 'image_veil': 55})], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        N.testata_sezione(t, 'Nel lookbook', 'Tutti i capi delle foto', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, featured=True, posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_atelier():
    t = Tpl(ID, 'atelier')
    out = [K.intro(t, 'L’atelier', ['Via Tortona 27,', 'dove si accorciano gli orli.'],
                   'Una stanza con le macchine da cucire, i rotoli di lino e una prova d’abito sempre in corso.',
                   bg=K.video_bg(V_ATELIER, overlay=30), size=92)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Il disegno', 'description': 'Disegniamo la collezione in atelier: quarantadue capi, '
                                                                          'non uno di più.'},
                  {'number': '02', 'title': 'Il tessuto', 'description': 'Lino da Carpi, lana e cashmere da Biella, seta da '
                                                                          'Como: comprato a metraggio, senza avanzi.'},
                  {'number': '03', 'title': 'Il laboratorio', 'description': 'Sei laboratori cuciono i capi a piccoli lotti. Li '
                                                                              'conosciamo per nome.'},
                  {'number': '04', 'title': 'L’orlo', 'description': 'In atelier si prova e si accorcia, gratis. Anche dopo '
                                                                        'anni.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': C['testo'], 'title_size': 30,
        'title_weight': '500', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid',
                                                                  'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Pochi capi,', 'cuciti bene.'], size=60)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, f, a, ratio='3/4', lightbox=True)], wm='1-4', ws='1-2') for f, a in (
            ('pexels-3984886.jpg', 'La sarta sistema un abito bianco sullo stand'),
            ('pexels-7147458.jpg', 'I cartamodelli appesi alla parete dello studio'),
            ('pexels-9594685.jpg', 'Le maglie appese alla stender'),
            ('pexels-36293915.jpg', 'Le stoffe colorate arrotolate'))], layout='25-25-25-25', gap=16),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    tabella = t.classic('table', {
        'table_data': [['Taglia', 'Busto (cm)', 'Vita (cm)', 'Fianchi (cm)', 'Taglia italiana'],
                                  ['XS', '80–84', '62–66', '88–92', '38'], ['S', '84–88', '66–70', '92–96', '40'],
                                  ['M', '88–92', '70–74', '96–100', '42'], ['L', '92–98', '74–80', '100–106', '44'],
                                  ['XL', '98–104', '80–86', '106–112', '46']],
        'has_header': True, 'striped': True, 'bordered': True, 'hover_effect': True, 'compact': False, 'first_col_bold': True,
        'col_alignments': ['left', 'center', 'center', 'center', 'center'], 'responsive_mode': 'scroll',
        'header_bg': C['nero'], 'header_text_color': C['chiaro'], 'text_color': C['testo'], 'border_color': C['filo'],
        'even_row_bg': C['sala']})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La guida alle taglie'), N.titolo(t, 'Fra due taglie?<br><em>Prendete la più piccola.</em>',
                                                                       size=44),
                      N.testo(t, 'I nostri capi sono comodi: le spalle scese e le vite morbide vestono un po’ abbondante. '
                                 'Se sbagliate, il cambio è gratis per trenta giorni.', size=16)], wm='2-5', ws='1-1'),
               t.col([tabella], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', html_id='taglie', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Passate in atelier'), N.titolo(t, 'Via Tortona 27, Milano', size=40),
                      K.specs(t, [('Orari', 'da martedì a sabato 10 — 19'), ('L’orlo', 'gratis, mentre aspettate'),
                                  ('La prova', 'anche su appuntamento')]),
                      N.pulsante(t, 'Scrivici', P['assistenza'] + '#scrivici', pad_top=20)], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, '45.4520', '9.1680', 'Orlo · l’atelier', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='atelier', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  NEGOZIO
# ══════════════════════════════════════════════════════════════════════════════

def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'La collezione', 'Autunno–inverno 2026', 'Quarantadue capi, cuciti in Italia. Filtrate per '
                            'taglia, categoria e prezzo; il cuore li mette nei preferiti.', attributi='pa_taglia',
                            banner=N.banner(t, 'Non sai la taglia?', 'Te la diciamo noi.', 'La guida alle taglie, e il cambio '
                                            'gratis per trenta giorni.', 'La guida alle taglie', P['atelier'] + '#taglie',
                                            K.photo_bg('pexels-3119164.jpg'), size=36))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([
        t.row([t.col([N.banner(t, 'Il cappotto cammello,', '290 €.', 'L’ultimo inverno era 390. Restano la S, la M e la L.',
                               'Vedi il cappotto', prodotto_url('cappotto-cammello'),
                               K.photo_bg('pexels-19169191.jpg', position='center 25%'))], wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'La camicia Studio,', '79 €.', 'Collo alto, polsini lunghi, cotone e seta.',
                               'Vedi la camicia', prodotto_url('camicia-studio'), K.photo_bg('pexels-37524408.jpg'))],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'L’Archivio', 'I capi delle stagioni passate, fino al −28%', 'Niente saldi a fine stagione: quello '
                            'che resta va nell’Archivio, con le taglie che ci sono. Si rinnova ogni domenica.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Spedizione gratuita da 150 €, in due giorni'),
                                                   ('rotate-ccw', 'Reso e cambio taglia gratis in 30 giorni'),
                                                   ('scissors', 'L’orlo su misura, gratis, in atelier'),
                                                   ('ruler', 'Fra due taglie? Prendete la più piccola')],
                             bundle='Il look completo', miniature='left', altezza='720px', bottone='Aggiungi alla borsa')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 150 €. Il cambio taglia è sempre gratis.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna, il pagamento. Il pagamento è protetto, la carta non la '
                                            'salviamo.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'I tuoi ordini, i resi e i cambi di taglia, gli indirizzi.',
                            ('Un ordine in arrivo?', 'Lo segui dall’assistenza col numero dell’ordine e la tua email.',
                             'Traccia l’ordine', P['assistenza'] + '#spedizioni'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora nessun capo: toccate il cuore sulle foto della collezione.')


def build_assistenza():
    t = Tpl(ID, 'assistenza')
    return N.pagina_assistenza(t, 'Al telefono e per email dalle 9 alle 19, in atelier dal martedì al sabato.', [
        ('truck', 'Spedizioni', 'Gratis da 150 €, in due giorni lavorativi. Sotto, 6 €.', 'spedizioni'),
        ('rotate-ccw', 'Resi e cambi', 'Trenta giorni, gratis: il corriere passa a ritirare.', 'spedizioni'),
        ('ruler', 'Le taglie', 'La guida alle taglie e il cambio gratis.', 'spedizioni'),
        ('scissors', 'L’orlo', 'Accorciamo pantaloni e abiti in atelier, gratis.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 150 €; sotto, 6 €. Arriva in due giorni lavorativi in tutta Italia.'),
        ('Come cambio la taglia?', 'Dal link nella mail dell’ordine scegliete la taglia nuova: il corriere ritira e porta il '
                                   'capo giusto, senza spese.'),
        ('Posso restituire un capo dell’Archivio?', 'Sì, alle stesse condizioni della collezione: trenta giorni, gratis.'),
        ('Fate gli orli?', 'Sì, in atelier e gratis, anche per i capi comprati online: portateli quando volete.'),
        ('Come lavo il lino?', 'A 30°, al rovescio, e lo stendete senza strizzarlo. Il ferro, se proprio volete, umido.')],
        CONTATTI, campi_extra='Un cambio di taglia\n', foto=('pexels-8396317.jpg', 'Due clienti scelgono i capi dalla stender'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questo capo <em>non è più qui.</em>', 'Forse è finito nell’Archivio. O provate a '
                        'cercarlo qui sopra.', K.video_bg(V_TESSUTO, overlay=40))


THEME = tema(
    ID, NAME, 'Negozi online', 'Le offerte della collezione',
    ['moda', 'abbigliamento', 'woocommerce', 'e-commerce', 'lookbook', 'taglie', 'milano', 'slider pro'],
    'Il negozio online di un marchio di moda milanese, chiaro come una rivista: in apertura la fascia delle offerte che scorre '
    '(capispalla, abiti, maglieria) e le quattro schede coi prodotti, il mega menu coi pannelli della collezione, delle '
    'novità e delle offerte; le categorie, i nuovi arrivi, compra il look sulla foto, l’Archivio fino al −28% col conto alla rovescia, il lookbook che compone il look, la guida alle '
    'taglie; la scheda prodotto con le taglie e il video del capo nella galleria.',
    [('Collezione', P['negozio']), ('Nuovi arrivi', P['negozio']), ('Abiti', '{{PAGINA:negozio}}?product_cat=abiti'),
     ('Maglieria', '{{PAGINA:negozio}}?product_cat=maglieria'), ('Capispalla', '{{PAGINA:negozio}}?product_cat=capispalla'),
     ('Lookbook', P['lookbook']), ('Offerte', P['offerte']), ('L’atelier', P['atelier'])],
    [('home', 'Home', None), ('negozio', 'La collezione', 'collezione'), ('lookbook', 'Lookbook', 'lookbook'),
     ('atelier', 'L’atelier', 'atelier'), ('offerte', 'L’Archivio', 'archivio'), ('assistenza', 'Assistenza', 'assistenza'),
     ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'La borsa', 'carrello'), ('cassa', 'La cassa', 'cassa'),
     ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bodoni Moda\', \'Didot\', Georgia, serif',
    '\'DM Mono\', ui-monospace, Menlo, monospace', ['Bodoni Moda', 'Manrope', 'DM Mono'], corsivo_di=['Bodoni Moda'],
    peso_titoli='500', raggio=0, grana=0, titoli_line='1.02', titoli_spazio='-0.01em')
tema_negozio(THEME, NAME)
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # anteprime con la vetrina delle offerte (1° nov 2026)
pannelli(THEME, NAME, {'Collezione': ('pannello-reparti', 'Pannello del negozio'), 'Offerte': ('pannello-offerte', 'Pannello delle offerte'), 'Nuovi arrivi': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'metro'
THEME['cursor']['label_text'] = 'Vedi'


for _p in PRODOTTI:
    CAT_DI[_p[2]] = _p[3]


def prodotti_json():
    voci = []
    for (sku, nome, slug, cat, prezzo, sconto, scorta, voto, rec, evid, foto, punti, testo, var, video) in PRODOTTI:
        d = {'sku': sku, 'nome': nome, 'slug': slug, 'cat': [cat], 'prezzo': prezzo, 'sconto': sconto, 'scorta': scorta,
             'voto': voto, 'recensioni': rec, 'evidenza': evid, 'foto': foto, 'punti': punti, 'testo': testo,
             'dentro': 'Il capo, il sacchetto di cotone per riporlo, il biglietto del laboratorio che l’ha cucito.',
             'upsell': LEGAMI.get(sku, ([], []))[0], 'cross': LEGAMI.get(sku, ([], []))[1]}
        if var:
            d['varianti'] = {'attributo': 'taglia', 'valori': var[0], 'esaurite': var[1]}
        if video:
            d['video'], d['poster'] = video + '.mp4', video + '.jpg'
        voci.append(d)
    dati = {'tema': ID, 'titolo_dentro': 'Nella scatola',
            'attributi': {'taglia': {'nome': 'Taglia', 'termini': TAGLIE + NUMERI}},
            'categorie': [{'slug': s, 'nome': n, 'descrizione': d, 'foto': f} for s, n, d, f in CATEGORIE],
            'prodotti': voci, 'recensioni': RECENSIONI, 'nomi': NOMI}
    for d in dati['prodotti']:
        for f in d['foto'] + [d.get('video', ''), d.get('poster', '')]:
            assert not f or f in MEDIA, f
    for c in dati['categorie']:
        assert c['foto'] in MEDIA, c['foto']
    out = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'prodotti')
    os.makedirs(out, exist_ok=True)
    with open(os.path.join(out, ID + '.json'), 'w', encoding='utf-8', newline='\n') as fh:
        json.dump(dati, fh, ensure_ascii=False, indent=1)


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'lookbook.json': build_lookbook(), 'atelier.json': build_atelier(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    prodotti_json()


if __name__ == '__main__':
    main()
