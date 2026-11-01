# -*- coding: utf-8 -*-
"""Genera il tema «Velluto» (id velluto): un atelier d'alta moda in via Tornabuoni, a Firenze — abiti da sera, sposa e
cerimonia, tailleur, cappe, smoking, gioielli e guanti — col negozio online e le prove su appuntamento.

Tema chiaro e prezioso: il bordeaux del velluto sui pulsanti, l'oro dei bollini e dei dettagli, l'avorio della carta e il
nero dell'inchiostro. Titoli in Italiana (sottile, da invito), testo ed etichette in Jost. Angoli vivi.

Apertura: la vetrina delle offerte, come nei negozi grandi: una fascia bassa che scorre da sola (l'archivio di sartoria, la
sposa, lo smoking in velluto), sotto le quattro schede coi capi veri; il mega menu apre le collezioni, l'archivio e le
novità. A metà pagina «il sipario»: lo slider resta fermo mentre si scorre, le due metà di un sipario di velluto rosso si
aprono sulla passerella e compare il programma della sfilata, un abito alla volta col prezzo. Cursore: le paillettes.

WooCommerce: il catalogo coi filtri per taglia, la scheda con la galleria (il video della passerella o della sartoria) e le
taglie, il carrello, la cassa a passi, l'account, i preferiti; l'atelier con la prova da prenotare e la collezione.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'velluto'
NAME = 'Velluto'

HEX = {
    'primary': '#5E0F23', 'primary_contrast': '#FFFFFF', 'secondary': '#C8A45C', 'secondary_contrast': '#1A1113',
    'accent': '#9B1B30', 'dark': '#120A0C', 'light': '#FFFCF7', 'muted': '#EDE3D6', 'muted_contrast': '#1A1113',
    'text': '#1A1113', 'text_muted': '#5F5254', 'background': '#F7F2EA', 'border': '#DCCFC0', 'link': '#5E0F23',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'atelier', 'collezione', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'atelier@velluto.example', '+39 055 000 2290', 'tel:+390550002290'

V_PASSERELLA, V_BIANCO, V_ROSA, V_LONTANO, V_MANICHE, V_MACCHINA, V_RICAMO, V_AGO, V_RASO_NERO, V_RASO_BLU = (
    'pexels-video-9512045', 'pexels-video-9511845', 'pexels-video-7305172', 'pexels-video-9509329', 'pexels-video-9509332',
    'pexels-video-5815527', 'pexels-video-8527342', 'pexels-video-5815277', 'pexels-video-7677321', 'pexels-video-7677239')
VIDEOS = [V_PASSERELLA, V_BIANCO, V_ROSA, V_LONTANO, V_MANICHE, V_MACCHINA, V_RICAMO, V_AGO, V_RASO_NERO, V_RASO_BLU]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    33782082, 38142668, 36973652, 30455749, 7615906, 9705033, 27677905, 30873252, 30873276, 29368455, 31862127, 31168033,
    39089253, 23516316, 9856621, 17420227, 34582584, 39117653, 36698066, 34342524, 17728712, 30479352, 12911893, 30468671,
    40139753, 39687774, 29632552, 7203478, 29016259, 29368477, 31779379, 9821752, 40274439, 6726841, 6702736, 20370082,
    8459378, 12060805, 20370081, 20352747, 20352749, 11076482, 20091882, 12972006, 17246956, 30826705, 3563554, 8516534,
    7761844, 7147594, 7776137, 32641547, 8516202, 3984870, 6843283, 35974685, 6843237, 4938326, 19856109, 34010218, 6642209,
    6556321, 38404664, 32286014, 9391678, 31052874, 31052875, 7322157, 9391688, 36716402, 6461076, 8526968, 6764919, 8844393)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True)
C = K.c
ORO, RUBINO = C['sec'], C['acc2']
TITOLI, MONO = 'Italiana', 'Jost'
N = Negozio(K, NAME, P, raggio=0, ratio='3-4', card='none', hover='swap', testata_scura=False,
            consegna='Consegna a mano a Firenze, gratis in Italia', evidenza='Cucito in atelier', marca='', peso_titoli='400')

CATEGORIE = [
    ('abiti-da-sera', 'Abiti da sera', 'Crêpe di seta, velluto, raso, paillettes.', 'pexels-29368455.jpg'),
    ('sposa', 'Sposa e cerimonia', 'Pizzo, organza, applicazioni: su appuntamento.', 'pexels-20352747.jpg'),
    ('tailleur', 'Giacche e tailleur', 'Lana, cashmere, gessato: dal giorno alla sera.', 'pexels-30479352.jpg'),
    ('cappe', 'Cappe e capispalla', 'Mantelle, cappotti, trench.', 'pexels-29368477.jpg'),
    ('uomo', 'Smoking e uomo', 'Smoking in lana e in velluto, cuciti su misura.', 'pexels-17420227.jpg'),
    ('accessori', 'Gioielli e guanti', 'Orecchini d’artista, guanti da sera.', 'pexels-34010218.jpg'),
]
TAGLIE = ['38', '40', '42', '44', '46', '48', '50', '52', '54']
DONNA, UOMO = TAGLIE[:5], TAGLIE[3:]


def abito(sku, nome, slug, cat, prezzo, sconto, scorta, voto, rec, evid, foto, punti, testo, esaurite=(), video=None, **kw):
    taglie = UOMO if cat == 'uomo' else DONNA
    return prodotto(sku, nome, slug, cat, prezzo, sconto, scorta, voto, rec, evid, foto, punti, testo,
                    varianti=('taglia', taglie, list(esaurite)), video=video, **kw)


PRODOTTI = [
    abito('VEL-001', 'Abito Rubino, crêpe di seta', 'abito-rubino', 'abiti-da-sera', 2900, 2300, 4, 4.9, 38, True,
          ['pexels-29368455.jpg', 'pexels-31862127.jpg'], ['Crêpe di seta pesante', 'Scollo a barca, schiena nuda',
                                                            'Ventaglio in seta compreso', 'Il video della passerella'],
          'Il rosso della collezione Notte: crêpe di seta che cade dritto, la schiena nuda, il ventaglio dello stesso tessuto.',
          esaurite=['46'], video=V_PASSERELLA, cross=['VEL-022']),
    abito('VEL-002', 'Abito Notte, velluto nero', 'abito-notte', 'abiti-da-sera', 3400, 0, 3, 5.0, 22, True,
          ['pexels-33782082.jpg', 'pexels-36973652.jpg'], ['Velluto di seta', 'Lungo a colonna', 'Fodera in seta',
                                                            'Cucito in atelier'],
          'Il velluto nero lungo a colonna: il vestito che non passa di moda, ritoccato su di voi alla prova.',
          cross=['VEL-024']),
    abito('VEL-003', 'Abito Champagne, raso', 'abito-champagne', 'abiti-da-sera', 2600, 1950, 3, 4.8, 19, False,
          ['pexels-38142668.jpg'], ['Raso di seta champagne', 'Spalline sottili', 'Spacco laterale', 'Dall’archivio di sartoria'],
          'Il raso color champagne che prende la luce: dall’archivio di sartoria, a meno.'),
    abito('VEL-004', 'Abito Oltremare, seta', 'abito-oltremare', 'abiti-da-sera', 2400, 0, 4, 4.7, 16, False,
          ['pexels-27677905.jpg'], ['Seta lavata blu oltremare', 'Drappeggio sul fianco', 'Lungo alla caviglia', 'Leggero'],
          'La seta oltremare drappeggiata sul fianco: per le sere d’estate e i matrimoni in giardino.'),
    abito('VEL-005', 'Abito Girasole, chiffon', 'abito-girasole', 'abiti-da-sera', 1900, 0, 5, 4.6, 12, False,
          ['pexels-9705033.jpg'], ['Chiffon di seta giallo', 'Monospalla', 'Gonna a balze', 'Fodera in raso'],
          'Il giallo per chi non vuole passare inosservata: chiffon a balze, una sola spalla.'),
    abito('VEL-006', 'Abito Oro, paillettes', 'abito-oro', 'abiti-da-sera', 4200, 3400, 2, 4.9, 27, True,
          ['pexels-30873276.jpg', 'pexels-30873252.jpg'], ['Paillettes cucite a mano', 'Tulle color carne', 'Scollo a V',
                                                            'Trecento ore di ricamo'],
          'Trecento ore di ricamo, una paillette alla volta: l’abito di Capodanno della collezione.', esaurite=['38']),
    abito('VEL-007', 'Abito Fiamma, tulle', 'abito-fiamma', 'abiti-da-sera', 3100, 0, 3, 4.8, 14, False,
          ['pexels-31168033.jpg', 'pexels-9856621.jpg'], ['Tulle rosso a strati', 'Corpetto drappeggiato', 'Strascico corto',
                                                           'Sottogonna in organza'],
          'Il tulle rosso a strati che si muove come una fiamma: per i balli e le prime.'),
    abito('VEL-008', 'Abito Galleria, volant', 'abito-galleria', 'abiti-da-sera', 2800, 0, 3, 4.7, 11, False,
          ['pexels-39089253.jpg', 'pexels-23516316.jpg'], ['Raso rosso a volant', 'Scollo a cuore', 'Gonna a sirena',
                                                            'Cucito in atelier'],
          'La sirena di raso rosso coi volant fino a terra: da fotografare su uno scalone.'),
    abito('VEL-009', 'Abito Balcone, paillettes rosse', 'abito-balcone', 'abiti-da-sera', 3600, 0, 2, 4.8, 9, False,
          ['pexels-7615906.jpg', 'pexels-30455749.jpg'], ['Paillettes rosse su tulle', 'Maniche lunghe', 'Gonna ampia',
                                                           'Ricamo a mano'],
          'Le paillettes rosse a maniche lunghe, con la gonna ampia che gira.'),
    abito('VEL-010', 'Abito da sposa Ninfea, pizzo', 'sposa-ninfea', 'sposa', 5800, 0, 2, 5.0, 31, True,
          ['pexels-20352747.jpg', 'pexels-8459378.jpg', 'pexels-11076482.jpg'], ['Pizzo chantilly', 'Scollo a barca',
                                                                                  'Strascico staccabile', 'Tre prove comprese'],
          'Il pizzo chantilly ricamato a mano, lo strascico che si stacca per ballare: tre prove in atelier comprese.',
          video=V_RICAMO),
    abito('VEL-011', 'Abito da sposa Aurora, applicazioni', 'sposa-aurora', 'sposa', 6400, 0, 1, 4.9, 18, False,
          ['pexels-20370082.jpg', 'pexels-30826705.jpg'], ['Fiori di organza applicati', 'Corpetto a stecche',
                                                            'Gonna in tulle', 'Tre prove comprese'],
          'I fiori di organza cuciti uno per uno sul tulle: per un matrimonio di primavera.'),
    abito('VEL-012', 'Abito da sposa Brezza, organza', 'sposa-brezza', 'sposa', 4900, 3900, 2, 4.8, 15, True,
          ['pexels-20352749.jpg', 'pexels-20091882.jpg'], ['Organza di seta', 'Ricamo sul corpetto', 'Linea ad A',
                                                            'Dall’archivio: tre prove comprese'],
          'L’organza leggera ricamata sul corpetto: dall’archivio di sartoria, sempre con tre prove.'),
    abito('VEL-013', 'Tailleur Ardesia, giacca e gonna', 'tailleur-ardesia', 'tailleur', 1650, 0, 6, 4.7, 24, False,
          ['pexels-30479352.jpg', 'pexels-30468671.jpg'], ['Lana fredda grigio ardesia', 'Giacca sciancrata', 'Gonna a matita',
                                                            'Fodera in seta'],
          'Il tailleur da riunione che arriva a cena: lana fredda, la giacca sciancrata, la gonna a matita.'),
    abito('VEL-014', 'Giacca Duna, lana e cashmere', 'giacca-duna', 'tailleur', 980, 790, 7, 4.8, 33, True,
          ['pexels-12911893.jpg'], ['Lana e cashmere', 'Spalle morbide', 'Revers a lancia', 'Dall’archivio'],
          'La giacca color sabbia che va su tutto: lana e cashmere, le spalle morbide.'),
    abito('VEL-015', 'Completo Inchiostro, pantalone a palazzo', 'completo-inchiostro', 'tailleur', 1480, 0, 5, 4.6, 12,
          False, ['pexels-29632552.jpg', 'pexels-39687774.jpg'], ['Gessato blu inchiostro', 'Giacca doppiopetto',
                                                                   'Pantalone a palazzo', 'Orlo su misura'],
          'Il doppiopetto gessato col pantalone a palazzo: l’orlo si fa su di voi, in atelier.'),
    abito('VEL-016', 'Cappa Ciliegia, lana doppia', 'cappa-ciliegia', 'cappe', 1900, 0, 4, 4.9, 17, True,
          ['pexels-29368477.jpg'], ['Lana doppia rossa', 'Collo a cappuccio morbido', 'Fodera in seta', 'Taglia unica regolabile'],
          'La cappa rossa sopra l’abito da sera: il colpo di teatro all’uscita.'),
    abito('VEL-017', 'Mantella Plissé, organza', 'mantella-plisse', 'cappe', 1350, 0, 3, 4.6, 8, False,
          ['pexels-29016259.jpg'], ['Organza plissettata', 'Iridescente', 'Si chiude col fiocco', 'Leggerissima'],
          'L’organza plissettata che cambia colore con la luce: sopra un abito nero, sulle spalle nude.'),
    abito('VEL-018', 'Trench Bordeaux', 'trench-bordeaux', 'cappe', 1200, 950, 5, 4.7, 21, False,
          ['pexels-9821752.jpg'], ['Gabardine di cotone', 'Cintura e polsini', 'Fodera in seta', 'Dall’archivio'],
          'Il trench bordeaux col collo alto: dall’archivio, a meno.'),
    abito('VEL-019', 'Cappotto Avorio', 'cappotto-avorio', 'cappe', 1650, 0, 4, 4.8, 13, False,
          ['pexels-6702736.jpg', 'pexels-6726841.jpg'], ['Lana e cashmere avorio', 'Taglio a vestaglia', 'Cintura',
                                                          'Lungo al polpaccio'],
          'Il cappotto avorio a vestaglia: si chiude con la cintura, sta sopra tutto.'),
    abito('VEL-020', 'Smoking Mezzanotte, lana e raso', 'smoking-mezzanotte', 'uomo', 2200, 0, 5, 4.9, 29, True,
          ['pexels-17420227.jpg', 'pexels-34342524.jpg', 'pexels-39117653.jpg'], ['Lana fredda nero mezzanotte',
                                                                                   'Revers a scialle in raso', 'Pantalone con la banda',
                                                                                   'Orlo e maniche su misura'],
          'Lo smoking a un bottone col revers a scialle in raso: orlo e maniche si fanno su di voi.'),
    abito('VEL-021', 'Smoking in velluto blu notte', 'smoking-velluto', 'uomo', 2500, 1990, 3, 4.8, 14, False,
          ['pexels-17728712.jpg', 'pexels-36698066.jpg'], ['Velluto di cotone blu notte', 'Revers in raso nero',
                                                            'Fodera in seta stampata', 'Orlo su misura in due giorni'],
          'La giacca in velluto blu notte per le feste: col pantalone nero o coi jeans.', esaurite=['54']),
    prodotto('VEL-022', 'Orecchini Cascata, argento e lapislazzuli', 'orecchini-cascata', 'accessori', 690, 0, 6, 4.8, 19,
             False, ['pexels-34010218.jpg', 'pexels-6642209.jpg'], ['Argento 925', 'Lapislazzuli', 'Pendenti 6 cm',
                                                                    'Nella scatola di velluto'],
             'Gli orecchini a cascata d’argento e lapislazzuli: per l’abito nero e per quello rosso.'),
    prodotto('VEL-023', 'Orecchini Stella d’oro', 'orecchini-stella', 'accessori', 480, 0, 8, 4.7, 23, False,
             ['pexels-38404664.jpg', 'pexels-32286014.jpg'], ['Ottone bagnato in oro', 'Stelle traforate', 'Leggeri',
                                                               'Nella scatola di velluto'],
             'Le stelle d’oro traforate a mano: grandi e leggere.'),
    prodotto('VEL-024', 'Guanti da sera in raso', 'guanti-raso', 'accessori', 220, 175, 10, 4.9, 41, False,
             ['pexels-31052875.jpg', 'pexels-31052874.jpg', 'pexels-7322157.jpg'], ['Raso di seta nero', 'Sopra il gomito',
                                                                                     'Tre bottoncini al polso', 'Dall’archivio'],
             'I guanti lunghi di raso nero sopra il gomito: il dettaglio che fa la sera.'),
]
RECENSIONI = [[5, 'La prova in atelier è stata un’esperienza: l’abito sembrava cucito addosso.'],
              [5, 'Consegnato a mano, nella custodia di velluto. Perfetto per il matrimonio.'],
              [5, 'Il tessuto è meraviglioso, si sente la qualità.'],
              [4, 'Ho dovuto accorciarlo un po’, l’hanno fatto in due giorni.'],
              [5, 'Ho ricevuto più complimenti di tutta la vita.'],
              [5, 'Gentilissimi, mi hanno seguita in tre prove.'],
              [4, 'Bellissimo, la taglia veste un po’ stretta: prendete quella sopra.'],
              [5, 'Lo smoking mi sta come un guanto.']]
NOMI = ['Beatrice A.', 'Ginevra L.', 'Costanza R.', 'Vittoria M.', 'Ludovica F.', 'Camilla S.', 'Margherita P.',
        'Alessandro D.', 'Edoardo C.', 'Aurora B.', 'Ottavia G.', 'Tommaso V.']
PROMESSE = [('truck', 'Consegna gratuita in Italia, a mano a Firenze'), ('scissors', 'Orlo e maniche su misura, compresi'),
            ('calendar', 'Prove in atelier su appuntamento'), ('gift', 'Nella custodia di velluto')]
CONTATTI = [('map-pin', 'Atelier · via Tornabuoni 21, Firenze', P['atelier'] + '#atelier'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'Velluto', icona='sparkles', ricerca='Cerca un abito, una cappa, uno smoking…',
                     parole='un abito rosso per Capodanno\nla sposa in pizzo\nuno smoking in velluto\ngli orecchini a cascata',
                     annuncio=('L’archivio di sartoria: fino al −25%, la prova in atelier compresa.', 'L’archivio',
                               P['offerte']), logo_size=34)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'Velluto', 'sparkles', 'Un atelier d’alta moda in via Tornabuoni, a Firenze: abiti da '
                   'sera, sposa e cerimonia, tailleur e smoking cuciti al piano di sopra, provati su di voi.',
                   [('Le collezioni', [('Abiti da sera', N.cat_url('abiti-da-sera')), ('Sposa e cerimonia', N.cat_url('sposa')),
                                       ('Smoking e uomo', N.cat_url('uomo')), ('L’archivio', P['offerte'])]),
                    ('Velluto', [('L’atelier', P['atelier']), ('La collezione Notte', P['collezione']),
                                 ('I preferiti', P['preferiti']), ('Il mio account', P['account'])]),
                    ('Aiuto', [('Consegne e resi', P['assistenza'] + '#spedizioni'),
                               ('Prenota una prova', P['atelier'] + '#prova'), ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Velluto s.r.l. · Firenze · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera dell’atelier', 'Le collezioni, prima della sfilata.',
                              'Due lettere a stagione: i bozzetti, i tessuti, gli inviti alle presentazioni.',
                              'Fatto: l’invito alla prossima presentazione arriva qui.'),
                   social=[('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.5  # la larghezza media di una lettera dei titoli (Italiana, per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO)
    return N.vetrina(t, [
        N.offerta('ve-o1', K.sfondo_video(V_PASSERELLA, overlay=0.12), 'L’archivio di sartoria · fino a domenica',
                  'Gli abiti da sera, fino al −25%.', 'Il Champagne in raso a 1.950 €, il Rubino in crêpe di seta a 2.300 €.',
                  'Gli abiti da sera', N.cat_url('abiti-da-sera'), bollino='−25%', bollino_x=72,
                  nota='La prova in atelier è compresa', primo=True, **f),
        N.offerta('ve-o2', K.sfondo_video(V_BIANCO, overlay=0.12), 'Sposa · su appuntamento',
                  'L’abito Brezza a 3.900 €.', 'Organza ricamata, ritoccata su di voi in tre prove. Era 4.900 €.',
                  'Sposa e cerimonia', N.cat_url('sposa'), bollino='−20%', bollino_x=72,
                  nota='Tre prove comprese, in via Tornabuoni', **f),
        N.offerta('ve-o3', K.sfondo_foto('pexels-36698066.jpg', overlay=0.12, scala=1.06), 'Uomo · per le feste',
                  'Lo smoking in velluto a 1.990 €.', 'Velluto blu notte e revers in raso nero. Era 2.500 €.',
                  'Smoking e uomo', N.cat_url('uomo'), bollino='−20%', nota='Orlo e maniche su misura, in due giorni', **f),
    ], transizione='blur')


# le schede sotto la vetrina: «prezzi» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='prezzi', voci=[
        ('L’archivio di sartoria', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutto', P['offerte']),
        ('Giacche e tailleur', {'category': 'tailleur', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('tailleur')),
        ('Abiti da sera', {'category': 'abiti-da-sera', 'orderby': 'rating'}, 'Vedi tutti', N.cat_url('abiti-da-sera')),
        ('Sposa e cerimonia', {'category': 'sposa', 'orderby': 'date'}, 'Vedi tutti', N.cat_url('sposa')),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Le collezioni', 'Sei sartorie, una sola misura: la vostra',
                              ('L’abito Rubino,', '2.300 €.', 'Crêpe di seta, era 2.900 €.', 'Abiti da sera',
                               N.cat_url('abiti-da-sera'), 'pexels-29368455.jpg'), tutto='Tutte le collezioni')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'L’archivio di sartoria', 'Fino al −25%.',
                              'L’archivio chiude tra:',
                              ('I guanti da sera,', '175 €.', 'Raso, sopra il gomito. Era 220 €.', 'Gioielli e guanti',
                               N.cat_url('accessori'), 'pexels-31052875.jpg'), tutte='Tutto l’archivio')


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'La collezione Notte',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('La sposa,', 'su appuntamento.', 'Tre prove in atelier, comprese.', 'Prenota una prova',
                              P['atelier'] + '#prova', 'pexels-20352747.jpg'), tutte='Tutta la collezione')


# ── Il sipario: lo slider resta fermo, il velluto si apre sulla passerella, poi il programma della sfilata ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1200.0
PROGRAMMA = [('I', 'Abito Notte', 'Velluto nero', '3.400 €', 'abiti-da-sera'),
             ('II', 'Abito Rubino', 'Crêpe di seta', '2.300 €', 'abiti-da-sera'),
             ('III', 'Abito Oro', 'Paillettes a mano', '3.400 €', 'abiti-da-sera'),
             ('IV', 'Smoking Mezzanotte', 'Lana e raso', '2.200 €', 'uomo')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def sipario(t):
    m = K.mob
    L = []
    # il programma: a destra, sul velo scuro che sale dal lato del testo (la passerella resta piena a sinistra)
    L.append(K.livello('si-velo', tipo='shape', x=48, y=0, w=52, h=100, entra='fadeIn', ritardo=0,
                       shapeGradient={'angle': 270, 'from': mix(C['nero'], 90), 'to': mix(C['nero'], 0), 'fromStop': 55},
                       responsive={'mobile': {'x': 0, 'y': 40, 'width': 100, 'height': 60}}))
    L.append(K.livello('si-velo-m', tipo='shape', x=0, y=0, w=0.1, h=0.1, entra='fadeIn', ritardo=0,
                       shapeGradient={'angle': 0, 'from': mix(C['nero'], 92), 'to': mix(C['nero'], 0), 'fromStop': 70},
                       responsive={'mobile': {'x': 0, 'y': 30, 'width': 100, 'height': 70}}))
    L += [
        K.livello('si-o', contenuto='La sfilata · collezione Notte', x=58, y=12, w=36, size=13, peso='500', colore=ORO,
                  letterSpacing=4, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 41, 'width': 86}}),
        K.livello('si-t', contenuto='Il programma<br>della sera.', tag='h2', x=58, y=16.5, w=38, size=64, peso='400',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, entra='fadeIn', ritardo=200,
                  responsive={'mobile': {'fontSize': m(34), 'x': 7, 'y': 43.5, 'width': 86}}),
    ]
    # le uscite, una dopo l'altra mentre si scorre
    for k, (num, nome, tessuto, prezzo, cat) in enumerate(PROGRAMMA):
        y, ym = 340 + 96 * k, 640 + 128 * k
        appare = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 30, 'o': 0}, {'p': 34 + 14 * k, 'y': 30, 'o': 0},
                                                      {'p': 44 + 14 * k, 'y': 0, 'o': 100}, {'p': 100, 'y': 0, 'o': 100}]}
        a, b = _pc(835, y, 60, 50, W, H), _pc(44, ym, 70, 50, WM, HM)
        L.append(K.livello('si-num%d' % k, contenuto=num, x=a[0], y=a[1], w=a[2], size=34, peso='400', colore=ORO,
                           fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=appare,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(22)}}))
        a, b = _pc(905, y, 330, 40, W, H), _pc(120, ym, 380, 40, WM, HM)
        L.append(K.livello('si-nome%d' % k, contenuto=nome, x=a[0], y=a[1], w=a[2], size=30, peso='400', colore=C['chiaro'],
                           fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=appare,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(19)}}))
        a, b = _pc(905, y + 44, 330, 24, W, H), _pc(120, ym + 40, 380, 30, WM, HM)
        L.append(K.livello('si-tess%d' % k, contenuto=tessuto, x=a[0], y=a[1], w=a[2], size=13, peso='400',
                           colore=mix(C['chiaro'], 76), letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                           entra='fadeIn', ritardo=0, scrollMove=appare,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(10)}}))
        a, b = _pc(1250, y + 4, 140, 34, W, H), _pc(500, ym + 4, 120, 40, WM, HM)
        L.append(K.livello('si-prezzo%d' % k, tipo='button', contenuto=prezzo, x=a[0], y=a[1], w='auto', size=13, peso='500',
                           colore=C['nero'], bgColor=ORO, padding=9, borderRadius=0, fontFamily=MONO, entra='fadeIn',
                           ritardo=0, buttonUrl=N.cat_url(cat), hover={'bgColor': C['chiaro'], 'duration': 200},
                           scrollMove=appare, responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(10)}}))
    # il sipario: due teli di velluto che coprono tutto e si aprono ai lati; la frangia d'oro sul bordo
    apri_s = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 6, 'x': 0}, {'p': 32, 'x': -760},
                                                  {'p': 100, 'x': -760}]}
    apri_d = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 6, 'x': 0}, {'p': 32, 'x': 760},
                                                  {'p': 100, 'x': 760}]}
    for lato, x, mossa in (('s', 0, apri_s), ('d', 50, apri_d)):
        L.append(K.livello('si-telo-%s' % lato, tipo='image', imageSrc=K.img('pexels-6843283.jpg'), objectFit='cover',
                           x=x, y=0, w=50.5, h=100, entra='fadeIn', ritardo=0, scrollMove=mossa,
                           responsive={'mobile': {'x': x, 'y': 0, 'width': 50.5, 'height': 100}}))
        bordo = 49.4 if lato == 's' else 50
        L.append(K.livello('si-frangia-%s' % lato, tipo='shape', x=bordo, y=0, w=0.6, h=100, bgColor=ORO, entra='fadeIn',
                           ritardo=0, scrollMove=mossa,
                           responsive={'mobile': {'x': bordo, 'y': 0, 'width': 0.8, 'height': 100}}))
    L += [
        K.livello('si-cartello', contenuto='Scorrete:<br>si apre il sipario', x=30, y=40, w=40, size=46, peso='400',
                  colore=C['chiaro'], align='center', fontFamily=TITOLI, lineHeight=1.1, entra='fadeIn', ritardo=300,
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 8, 'o': 100}, {'p': 16, 'o': 0},
                                                                  {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(26), 'x': 8, 'y': 42, 'width': 84}}),
    ]
    sl = [K.diapositiva('si-1', K.sfondo_video(V_PASSERELLA, overlay=0.1), L, durata=60000)]
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
        N.testata_sezione(t, 'Le collezioni', 'Per quale sera?', P['negozio'], 'Tutte le collezioni'),
        t.one([N.categorie(t, colonne=6, raggio_foto=0, ratio='3-4')]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(K.apertura(t, sipario(t)))
    out.append(t.section([
        N.testata_sezione(t, 'La collezione Notte', 'Appena uscita dall’atelier', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='date', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 48}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'La sposa,', 'su appuntamento.', 'Pizzo, organza, applicazioni: tre prove comprese.',
                               'Prenota una prova', P['atelier'] + '#prova', K.photo_bg('pexels-20370082.jpg'), size=36)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Gli smoking,', 'cuciti su di voi.', 'Lana, velluto e raso: orlo e maniche compresi.',
                               'Smoking e uomo', N.cat_url('uomo'), K.photo_bg('pexels-34342524.jpg'), size=36)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'L’archivio di sartoria', color=ORO),
                      N.titolo(t, 'Chiude domenica.', size=46, color=C['chiaro']),
                      N.testo(t, 'I capi delle collezioni passate fino al −25%, sempre con la prova in atelier.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42, giorni=True), N.link(t, 'Tutto l’archivio', P['offerte'], chiaro=True, align='left',
                                                              pad_top=16)], wm='1-2', ws='1-1')],
              layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'], card_style='border')]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_RICAMO, 'L’atelier', 'Trecento ore, <em>una paillette alla volta.</em>',
                             'Al piano di sopra, in via Tornabuoni: le sarte cuciono, ricamano, provano. Ogni capo passa da qui.',
                             overlay=40, extra=[N.pulsante(t, 'L’atelier', P['atelier'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Gioielli e guanti', 'Il dettaglio che fa la sera', N.cat_url('accessori')),
        t.one([N.prodotti(t, category='accessori', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 48}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-6461076.jpg', 'La sarta prende la misura della vita col metro', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'La prova'), N.titolo(t, 'Su di voi,<br>non su una taglia.', size=52),
                      N.testo(t, 'Ogni capo comprato online si prova in atelier o a casa vostra, a Firenze: orli, maniche e '
                                 'pinces si fanno su misura, compresi nel prezzo. Fuori Firenze, la sarta vi segue in video.',
                              size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Orli e maniche su misura, compresi', 'La prova a casa, a Firenze',
                              'La sposa: tre prove comprese']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'Prenota una prova', P['atelier'] + '#prova', pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Le collezioni', 'Tutto l’atelier', 'Abiti da sera, sposa e cerimonia, tailleur, cappe, smoking, '
                            'gioielli e guanti: orli e maniche su misura, compresi.', colonne=3,
                            banner=N.banner(t, 'Fra due taglie?', 'Prendete la più grande.', 'La sarta stringe, non allarga.',
                                            'Prenota una prova', P['atelier'] + '#prova', K.photo_bg('pexels-8844393.jpg'),
                                            size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'La giacca Duna,', '790 €.', 'Lana e cashmere, era 980 €.', 'Giacche e tailleur',
                        N.cat_url('tailleur'), K.photo_bg('pexels-12911893.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Il trench Bordeaux,', '950 €.', 'Gabardine e seta, era 1.200 €.', 'Cappe e capispalla',
                        N.cat_url('cappe'), K.photo_bg('pexels-9821752.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'L’archivio di sartoria', 'Fino al −25%, fino a domenica', 'I capi delle collezioni passate, '
                            'pochi pezzi per taglia. La prova in atelier resta compresa.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Consegna gratuita in Italia'),
                                                   ('scissors', 'Orlo e maniche su misura, compresi'),
                                                   ('calendar', 'La prova in atelier su appuntamento'),
                                                   ('gift', 'Nella custodia di velluto')],
                             bundle='Per completare', miniature='left', altezza='680px', bottone='Aggiungi alla borsa')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Consegna gratuita in Italia. Alla cassa potete chiedere la prova in atelier.',
                             PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I vostri dati, la consegna, il pagamento. Nelle note: il giorno per la prova.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, le prove prenotate, le vostre misure.',
                            ('Una prova?', 'Scegliete il giorno: in atelier o a casa vostra, a Firenze.',
                             'Prenota una prova', P['atelier'] + '#prova'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto della collezione.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'In atelier dal martedì al sabato, 10–19; al telefono e in chat.', [
        ('truck', 'Consegne', 'Gratis in Italia; a Firenze a mano, nella custodia.', 'spedizioni'),
        ('rotate-ccw', 'Resi', 'Quattordici giorni, se il capo non è stato ritoccato.', 'spedizioni'),
        ('scissors', 'Su misura', 'Orli e maniche compresi; il resto su preventivo.', 'scrivici'),
        ('calendar', 'Prove', 'In atelier o a casa vostra, su appuntamento.', 'scrivici')], [
        ('Quanto costa la consegna?', 'Niente in Italia, col corriere assicurato; a Firenze la portiamo a mano.'),
        ('Posso restituire un abito?', 'Sì, entro quattordici giorni, se non è stato ritoccato. Dopo la prova su misura no.'),
        ('Che taglia prendo?', 'Fra due taglie la più grande: la sarta stringe, non allarga. Scriveteci le misure.'),
        ('Cucite su misura da zero?', 'Sì, per la sposa e per la sera: dal bozzetto alla prova finale, in sei settimane.')],
        CONTATTI, campi_extra='Una prova in atelier\nUn abito su misura\n',
        foto=('pexels-12060805.jpg', 'Gli abiti da sposa appesi in atelier'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questo abito <em>è già andato.</em>', 'Ne cuciamo pochi per taglia. Cercate qui '
                        'sopra, o scriveteci: forse lo rifacciamo su misura.', K.video_bg(V_RASO_NERO, overlay=40))


def build_atelier():
    t = Tpl(ID, 'atelier')
    out = [K.intro(t, 'L’atelier', ['Al piano di sopra,', 'in via Tornabuoni.'], 'Le sarte, il manichino, la luce del '
                   'pomeriggio: ogni capo di Velluto si cuce qui e si prova su di voi.',
                   bg=K.video_bg(V_MACCHINA, overlay=40), size=86)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-7761844.jpg', 'La stilista drappeggia il raso blu sul manichino', ratio='4/5')],
                     wm='1-2', ws='1-1'),
               t.col([N.occhiello(t, 'Su misura'), N.titolo(t, 'Dal bozzetto<br>alla prova finale.', size=46),
                      N.testo(t, 'Per la sposa e per la sera cuciamo anche da zero: il bozzetto, la tela, tre prove, sei '
                                 'settimane. Per il resto, ogni capo si ritocca su di voi: orli, maniche, pinces.', size=17),
                      K.numeri(t, [('6', 'settimane per un abito su misura'), ('3', 'prove per la sposa'),
                                   ('300', 'ore di ricamo per l’abito Oro')], colonne=3, size=40)], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 72}))
    out.append(K.video_frase(t, V_AGO, 'A mano', 'L’orlo invisibile <em>si cuce a mano.</em>',
                             'Punto per punto, col filo di seta dello stesso colore: dal dritto non si vede.', overlay=40))
    modulo = t.classic('form', {
        'fields': [{'field_type': 'text', 'label': 'Nome e cognome', 'name': 'nome', 'placeholder': '', 'required': True,
                    'width': '1-2', 'options': '', 'icon': ''},
                   {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': 'nome@esempio.it', 'required': True,
                    'width': '1-2', 'options': '', 'icon': ''},
                   {'field_type': 'select', 'label': 'La prova', 'name': 'prova', 'placeholder': '', 'required': True,
                    'width': '1-2', 'options': 'Un capo comprato online\nSposa e cerimonia\nUn abito su misura\nUno smoking',
                    'icon': ''},
                   {'field_type': 'date', 'label': 'Il giorno', 'name': 'giorno', 'placeholder': '', 'required': True,
                    'width': '1-2', 'options': '', 'icon': ''},
                   {'field_type': 'textarea', 'label': 'Qualcosa da dirci', 'name': 'note', 'placeholder': 'L’occasione, la taglia, '
                    'un’idea', 'required': False, 'width': '1-1', 'options': '', 'icon': ''}],
        'email_subject': 'Velluto — una prova in atelier', 'submit_text': 'Prenota la prova', 'submit_icon': '',
        'success_message': 'Grazie: vi scriviamo entro domani per confermare l’ora.', 'submit_full_width': False,
        'submit_alignment': 'left', 'label_color': C['testo'], 'label_size': '13', 'label_weight': '500',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': C['chiaro'], 'input_color': C['testo'],
        'input_border_color': C['filo'], 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 0,
        'input_focus_border': C['acc'], 'input_focus_shadow': False, 'gap': '14', 'submit_bg': C['acc'],
        'submit_color': C['on_acc'], 'submit_radius': 0, 'submit_padding_x': '28', 'submit_padding_y': '15',
        'submit_font_size': '13', 'submit_font_weight': '500', 'submit_text_transform': 'uppercase',
        'submit_letter_spacing': '2', 'submit_hover_bg': C['nero'], 'privacy_checkbox': True},
        style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
               'border_radius': 0})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La prova'), N.titolo(t, 'Prenotate<br>una prova.', size=50),
                      N.testo(t, 'In atelier, in via Tornabuoni 21, dal martedì al sabato; o a casa vostra, a Firenze. Un’ora, '
                                 'con la sarta e un bicchiere d’acqua frizzante.', size=17)], wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='start'),
    ], html_id='prova', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    out.append(t.section([
        N.testata_sezione(t, 'Cuciti qui', 'I capi dell’atelier', P['negozio']),
        t.one([N.prodotti(t, featured=True, posts_per_page=8, columns=4)]),
    ], html_id='atelier', padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione Notte', ['Velluto, raso,', 'paillettes.'], 'Ventiquattro capi per le sere d’inverno: '
                   'il rosso, il nero, l’oro. Si provano in atelier.', bg=K.video_bg(V_RASO_BLU, overlay=35), size=90)]
    coppie = [('pexels-33782082.jpg', 'L’abito Notte in velluto nero', 'pexels-29368455.jpg', 'L’abito Rubino in crêpe di seta'),
              ('pexels-30873276.jpg', 'L’abito Oro di paillettes', 'pexels-29368477.jpg', 'La cappa Ciliegia'),
              ('pexels-17420227.jpg', 'Lo smoking Mezzanotte', 'pexels-31052874.jpg', 'I guanti da sera in raso')]
    for k, (f1, a1, f2, a2) in enumerate(coppie):
        out.append(t.section([t.row([
            t.col([K.image(t, f1, a1, ratio='3/4')], wm='1-2', ws='1-1'),
            t.col([K.image(t, f2, a2, ratio='3/4')], wm='1-2', ws='1-1')], layout='50-50', gap=24, valign='start')],
            padding='default', width='large', style={'padding_top': 48 if k else 96, 'padding_bottom': 24}))
    out.append(t.section([
        N.testata_sezione(t, 'Tutta la collezione', 'Ventiquattro capi', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='date', posts_per_page=12, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'Il sipario',
    ['alta moda', 'atelier', 'abiti da sera', 'sposa', 'woocommerce', 'e-commerce', 'firenze', 'lusso'],
    'Il negozio online di un atelier d’alta moda in via Tornabuoni: in apertura la fascia delle offerte che scorre e le quattro '
    'schede coi capi, il mega menu con le collezioni, l’archivio di sartoria e le novità; a metà pagina il sipario di velluto '
    'che si apre sulla passerella e il programma della sfilata; la prova in atelier da prenotare, la collezione, la scheda '
    'prodotto con le taglie e il video della passerella.',
    [('Le collezioni', P['negozio']), ('Archivio', P['offerte']), ('Novità', P['negozio']),
     ('Sera', '{{PAGINA:negozio}}?product_cat=abiti-da-sera'), ('Sposa', '{{PAGINA:negozio}}?product_cat=sposa'),
     ('Uomo', '{{PAGINA:negozio}}?product_cat=uomo'), ('L’atelier', P['atelier']), ('La collezione', P['collezione'])],
    [('home', 'Home', None), ('negozio', 'Le collezioni', 'collezioni'), ('atelier', 'L’atelier', 'atelier'),
     ('collezione', 'La collezione Notte', 'collezione-notte'), ('offerte', 'L’archivio di sartoria', 'archivio'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'La borsa', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Jost\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Italiana\', \'Didot\', Georgia, serif',
    '\'Jost\', system-ui, sans-serif', ['Italiana', 'Jost'], corsivo_di=[],
    peso_titoli='400', raggio=0, grana=0, titoli_line='1.04', titoli_spazio='0')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Le collezioni': ('pannello-reparti', 'Pannello delle collezioni'),
                       'Archivio': ('pannello-offerte', 'Pannello dell’archivio'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'paillettes'
THEME['cursor']['ring_color'] = HEX['secondary']  # il colore del cursore (sul fondo chiaro il chiaro non si vede)


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'atelier.json': build_atelier(), 'collezione.json': build_collezione(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'taglia': {'nome': 'Taglia', 'termini': TAGLIE}},
                    dentro='Il capo nella custodia di velluto, il biglietto della sarta, l’invito alla prova.')


if __name__ == '__main__':
    main()
