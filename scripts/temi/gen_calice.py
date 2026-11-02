# -*- coding: utf-8 -*-
"""Genera il tema «Calice» (id calice): enoteca in via di Città, a Siena — rossi, bianchi, bollicine, rosati e vini da
dessert, cassette e regali, calici e dispensa; le degustazioni in enoteca il giovedì sera, le cantine da cui compriamo.
La vendita di vino è riservata ai maggiorenni: lo dicono l'annuncio, la cassa, l'assistenza e il piede.

Tema chiaro color pietra: il bordeaux sui pulsanti, l'oro sui bollini, il verde della vigna negli accenti. Titoli in
Cormorant Garamond (col corsivo vero), testo in Manrope, prezzi e annate in JetBrains Mono.

Apertura: la vetrina delle offerte, bassa, in dissolvenza incrociata (crossFade): il Chianti Classico a 14,90 €, sei
bollicine con la settima in regalo, il Brunello nuovo; sotto le quattro schede coi vini veri; il mega menu apre la cantina,
le offerte e le novità. A metà pagina «quattro calici»: lo slider resta fermo mentre si scorre e il calice, visto dall'alto,
si riempie quattro volte — una goccia cade, il colore si allarga dal centro, il riflesso gira come quando si fa ruotare il
vino: la Vernaccia paglierina, il rosato, il Chianti rubino, il Brunello granato, ognuno col suo naso e il suo piatto.
Cursore: il calice, col vino che ondeggia quando ci si muove.

WooCommerce: la cantina coi filtri, la scheda con la galleria (il video del vino versato) e l'astuccio a scelta, il carrello,
la cassa a passi, l'account, i preferiti; le cantine, le degustazioni con la prenotazione.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'calice'
NAME = 'Calice'

HEX = {
    'primary': '#6E1A2C', 'primary_contrast': '#FFFFFF', 'secondary': '#C9A45C', 'secondary_contrast': '#1C1012',
    'accent': '#3F5A2A', 'dark': '#1C1012', 'light': '#FFFFFF', 'muted': '#EFE7DD', 'muted_contrast': '#1C1012',
    'text': '#1F1416', 'text_muted': '#5F5255', 'background': '#F8F4EF', 'border': '#E2D6C9', 'link': '#6E1A2C',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'cantine', 'degustazioni', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'enoteca@calice.example', '+39 0577 000 777', 'tel:+390577000777'

V_VERSA, V_BOTTIGLIA, V_BORDO, V_ROSSO, V_VERSA2, V_COLLINE, V_FILARI, V_TRAMONTO, V_BOTTE, V_BARRICAIA, V_TAPPI, V_DOGHE, \
    V_CANDELE, V_CIELO, V_TAVOLA = (
        'pexels-video-19826636', 'pexels-video-1003928', 'pexels-video-4061885', 'pexels-video-10163756', 'pexels-video-7687087',
        'pexels-video-32889119', 'pexels-video-31193873', 'pexels-video-19812433', 'pexels-video-5537827',
        'pexels-video-10040032', 'pexels-video-6356810', 'pexels-video-2932297', 'pexels-video-6270528', 'pexels-video-8071316',
        'pexels-video-8922777')
VIDEOS = [V_VERSA, V_BOTTIGLIA, V_BORDO, V_ROSSO, V_VERSA2, V_COLLINE, V_FILARI, V_TRAMONTO, V_BOTTE, V_BARRICAIA, V_TAPPI,
          V_DOGHE, V_CANDELE, V_CIELO, V_TAVOLA]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    8473179, 8472740, 8473210, 18737012, 8473175, 8473140, 28826629, 8473177, 8472744, 5895443, 8472730, 8472887, 3019019,
    8472929, 7022837, 8472735, 14169599, 14071962, 3616954, 29811321, 8459381, 31011765, 9822760, 5713783, 8459368, 16441860,
    5472971, 10594798, 34400995, 4994753, 6234214, 8459364, 8459365, 6236629, 15006643, 39005672, 19714331, 30557566,
    27305292, 2580989, 30557559, 30557568, 15527037, 19393193, 30557561, 19714325, 9840058, 5633602, 28438011, 27500678,
    34004191, 6489360, 38270187, 33344325, 16663452, 31561190, 13310076, 9658801, 7101917, 14129222, 31425021, 28128757,
    30447488, 5732799, 5732814, 8473206, 7447405, 6762291, 8473221, 8472786, 5086618, 31125884, 34621126, 8472731, 8472900,
    8472897, 5732751, 8472893, 6004801, 8472749, 8015269, 13975271, 8015267, 5499117, 13975260, 7984853, 8015284,
    4877844, 11289743, 13452633, 38556967, 3937673, 36827003, 9179281, 12268564, 29251126, 19393192, 1484528, 14393731,
    39852931, 14129219, 9199101, 5732808, 39852932, 30557624, 17501156)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
ORO, VIGNA = C['sec'], C['acc2']
TITOLI, MONO = 'Cormorant Garamond', 'JetBrains Mono'
N = Negozio(K, NAME, P, raggio=2, ratio='4-5', card='border', hover='zoom', testata_scura=False,
            consegna='Consegna in 48 ore, a temperatura', evidenza='Il più stappato', marca='', peso_titoli='600')

CATEGORIE = [
    ('rossi', 'Rossi', 'Chianti, Brunello, Nobile, il fiasco.', 'pexels-8473140.jpg'),
    ('bianchi', 'Bianchi', 'Vernaccia, Vermentino, l’anfora.', 'pexels-8472887.jpg'),
    ('bollicine', 'Bollicine', 'Metodo classico, rosé, extra dry.', 'pexels-9822760.jpg'),
    ('rosati', 'Rosati e da dessert', 'Il rosato di Toscana, il Vin Santo.', 'pexels-34400995.jpg'),
    ('regali', 'Cassette e regali', 'Le cassette, le confezioni, la degustazione.', 'pexels-4877844.jpg'),
    ('dispensa', 'Calici e dispensa', 'I calici, il cavatappi, il tagliere.', 'pexels-9179281.jpg'),
]
ASTUCCI = ['Senza astuccio', 'Astuccio di legno']


def vino(sku, nome, slug, cat, prezzo, sconto, voto, rec, evid, foto, punti, testo, astuccio=False, **kw):
    if astuccio:
        kw['varianti'] = ('astuccio', ASTUCCI, [])
    return prodotto(sku, nome, slug, cat, prezzo, sconto, 60, voto, rec, evid, ['pexels-%s.jpg' % f for f in foto], punti, testo,
                    **kw)


PRODOTTI = [
    vino('CAL-001', 'Chianti Classico 2022', 'chianti-classico-2022', 'rossi', 19.9, 14.9, 4.8, 412, True, [8473179, 8473175],
         ['Sangiovese di Castellina', 'Rubino, viola e ciliegia', 'Dodici mesi in botte grande', 'Con la ribollita'],
         'Il Chianti di Castellina: rubino vivo, viola e ciliegia, la bistecca e la ribollita.', astuccio=True, video=V_ROSSO,
         cross=['CAL-020', 'CAL-018']),
    vino('CAL-002', 'Brunello di Montalcino 2020', 'brunello-2020', 'rossi', 54, 0, 4.9, 233, True, [8473140, 8472744],
         ['Sangiovese grosso', 'Granato, prugna e tabacco', 'Tre anni in botte', 'Da aprire un’ora prima'],
         'Il Brunello dell’annata calda: granato, prugna e tabacco, da aprire un’ora prima.', astuccio=True,
         video=V_BARRICAIA),
    vino('CAL-003', 'Rosso di Montalcino 2023', 'rosso-montalcino-2023', 'rossi', 24, 0, 4.7, 176, False, [8472740, 8473210],
         ['Sangiovese grosso', 'Ciliegia e fiori', 'Un anno in botte', 'Il fratello giovane del Brunello'],
         'Il fratello giovane del Brunello: la stessa vigna, un anno in botte, da bere adesso.', astuccio=True),
    vino('CAL-004', 'Nobile di Montepulciano 2021', 'nobile-2021', 'rossi', 26, 22, 4.8, 143, False, [8473177, 28826629],
         ['Prugnolo gentile', 'Mora e spezie', 'Due anni in legno', 'Con l’arrosto'],
         'Il prugnolo gentile di Montepulciano: mora e spezie, per l’arrosto della domenica.', astuccio=True),
    vino('CAL-005', 'Rosso di Toscana in barrique, «Torre»', 'rosso-torre', 'rossi', 39, 0, 4.7, 88, False,
         [18737012, 30557566], ['Sangiovese e merlot', 'Diciotto mesi in barrique', 'Morbido e scuro', 'Seimila bottiglie'],
         'Sangiovese e merlot in barrique: il rosso scuro e morbido delle cene lunghe.'),
    vino('CAL-006', 'Il fiasco della casa, rosso, 1,5 l', 'fiasco-casa', 'rossi', 12, 0, 4.6, 305, False, [14169599, 3937673],
         ['Un litro e mezzo', 'Nella paglia', 'Sangiovese e canaiolo', 'Per la tavola di tutti i giorni'],
         'Il fiasco impagliato da un litro e mezzo: il rosso di tutti i giorni, per la tavola lunga.'),
    vino('CAL-007', 'Vernaccia di San Gimignano 2024', 'vernaccia-2024', 'bianchi', 15, 0, 4.8, 221, True, [8472887, 8472730],
         ['Vernaccia', 'Paglierino coi riflessi verdi', 'Mela e mandorla', 'Coi crostini'],
         'La Vernaccia dalle torri: paglierina, mela e mandorla amara, coi crostini neri.', astuccio=True),
    vino('CAL-008', 'Vermentino di Toscana 2024', 'vermentino-2024', 'bianchi', 13.5, 0, 4.6, 167, False, [8472929, 8472735],
         ['Vermentino della costa', 'Salino', 'Agrumi e macchia', 'Col pesce'],
         'Il Vermentino della costa: salino, agrumi e macchia mediterranea, per il pesce.'),
    vino('CAL-009', 'Bianco macerato in anfora', 'bianco-anfora', 'bianchi', 29, 0, 4.5, 64, False, [7022837, 3019019],
         ['Trebbiano e malvasia', 'Sei mesi in anfora', 'Ambrato, sulle bucce', 'Per chi cerca altro'],
         'Il bianco lasciato sulle bucce in anfora: ambrato, tannico, per chi cerca altro.'),
    vino('CAL-010', 'Metodo classico brut, 36 mesi', 'metodo-classico-36', 'bollicine', 32, 0, 4.9, 198, True,
         [3616954, 14071962], ['Chardonnay e pinot nero', 'Trentasei mesi sui lieviti', 'Perlage fine', 'Per brindare'],
         'Trentasei mesi sui lieviti: la bollicina fine, crosta di pane e agrumi, per brindare davvero.', astuccio=True,
         video=V_CIELO),
    vino('CAL-011', 'Spumante extra dry', 'spumante-extra-dry', 'bollicine', 15, 12, 4.6, 344, False, [9822760, 5472971],
         ['Glera', 'Fresco e fruttato', 'Mela verde e glicine', 'Per l’aperitivo'],
         'La bollicina dell’aperitivo: fresca, mela verde e glicine, da tenere sempre in frigo.'),
    vino('CAL-012', 'Metodo classico rosé', 'metodo-classico-rose', 'bollicine', 22, 0, 4.7, 121, False, [8459381, 8459368],
         ['Pinot nero', 'Rosa tenue', 'Fragolina e lampone', 'Ventiquattro mesi sui lieviti'],
         'Il rosé di pinot nero: rosa tenue, fragolina e lampone, ventiquattro mesi sui lieviti.'),
    vino('CAL-013', 'Rosato di Toscana 2024', 'rosato-2024', 'rosati', 14, 0, 4.6, 133, False, [34400995, 6236629],
         ['Sangiovese', 'Buccia di cipolla', 'Pompelmo e sale', 'Con la pappa al pomodoro'],
         'Il rosato di sangiovese, color buccia di cipolla: pompelmo e sale, per l’estate e oltre.'),
    vino('CAL-014', 'Vin Santo del Chianti, mezza bottiglia', 'vin-santo', 'rosati', 28, 0, 4.9, 156, False, [4994753, 15006643],
         ['Uve appassite sui graticci', 'Cinque anni in caratello', 'Ambra, fico e noce', 'Coi cantucci'],
         'Le uve appassite sui graticci, cinque anni nei caratelli: ambra, fico, noce. Coi cantucci.'),
    vino('CAL-015', 'Cassetta «Toscana in tre»', 'cassetta-toscana-tre', 'regali', 69, 59, 4.8, 187, True, [4877844, 13975260],
         ['Chianti, Nobile, Rosso di Montalcino', 'Cassetta di legno', 'Il biglietto scritto a mano', 'Consegna a temperatura'],
         'Tre rossi in una cassetta di legno: Chianti, Nobile e Rosso di Montalcino, col biglietto.'),
    vino('CAL-016', 'Confezione regalo, due bottiglie', 'confezione-due', 'regali', 45, 0, 4.7, 98, False, [13975271, 8015269],
         ['Due bottiglie a scelta', 'Scatola rossa col nastro', 'Il biglietto', 'Scriveteci quali'],
         'Due bottiglie scelte da voi nella scatola rossa col nastro: scriveteci quali nelle note.'),
    vino('CAL-017', 'Sei bollicine, la settima è nostra', 'sei-bollicine', 'regali', 90, 0, 4.9, 76, True, [5499117, 8015267],
         ['Sei extra dry', 'La settima in regalo', 'Cartone da sette', 'Per le feste'],
         'Sei bottiglie di extra dry per le feste, e la settima la mettiamo noi.'),
    vino('CAL-018', 'Calici da degustazione, sei', 'calici-sei', 'dispensa', 48, 0, 4.8, 143, False, [9179281, 11289743],
         ['Cristallo sonoro', 'Soffiati a mano', 'Per rossi e bianchi', 'Sei calici'],
         'Sei calici di cristallo soffiati a mano: la forma giusta per sentire il naso.'),
    vino('CAL-019', 'Cavatappi da sommelier', 'cavatappi-sommelier', 'dispensa', 24, 0, 4.7, 112, False, [30557624, 39852932],
         ['Doppia leva', 'Lama per la capsula', 'Manico in olivo', 'Si chiude in tasca'],
         'Il cavatappi a doppia leva col manico d’olivo: il tappo esce senza rompersi.'),
    vino('CAL-020', 'Tagliere di pecorino e salumi, per due', 'tagliere-due', 'dispensa', 24, 0, 4.8, 254, False,
         [34621126, 5732751], ['Pecorino di Pienza', 'Finocchiona e salame', 'Miele e noci', 'Per due'],
         'Il pecorino di Pienza, la finocchiona, il miele e le noci: per due, col rosso.'),
    vino('CAL-021', 'Degustazione in enoteca, per due', 'degustazione-due', 'regali', 40, 0, 4.9, 167, False, [6762291, 7447405],
         ['Sei vini', 'Il giovedì sera', 'Col tagliere', 'Per due persone'],
         'Il biglietto per il giovedì sera: sei vini, il tagliere, il sommelier che racconta.'),
]
RECENSIONI = [[5, 'Il Chianti è arrivato in due giorni, perfetto e ben imballato.'],
              [5, 'Il Brunello vale ogni euro. Aperto un’ora prima, come consigliato.'],
              [4, 'Ottima cassetta, il biglietto scritto a mano è un bel pensiero.'],
              [5, 'La degustazione del giovedì è stata la serata più bella del mese.'],
              [5, 'Il Vin Santo coi cantucci: come a casa della nonna.'],
              [4, 'Buono, ma avrei preferito l’annata più vecchia.'],
              [5, 'Sei bollicine e la settima in regalo: le feste sono salve.'],
              [3, 'Il vino è buono, il corriere però ha chiesto due volte il documento.']]
NOMI = ['Marco D.', 'Giulia S.', 'Lorenzo P.', 'Francesca R.', 'Tommaso B.', 'Beatrice C.', 'Edoardo F.', 'Aurora M.',
        'Leonardo T.', 'Sofia G.', 'Mattia V.', 'Greta L.']
PROMESSE = [('truck', 'Consegna gratuita da 60 €'), ('thermometer', 'Spedito a temperatura'),
            ('package', 'Imballo che non si rompe'), ('badge-check', 'Solo ai maggiorenni')]
CONTATTI = [('map-pin', 'Calice · via di Città 77, Siena', P['degustazioni'] + '#dove'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'CALICE', icona='wine', ricerca='Cerca un Chianti, un Brunello, delle bollicine…',
                     parole='un Chianti Classico\nil Brunello\nsei bollicine\nun Vin Santo',
                     annuncio=('Il Chianti Classico a 14,90 €, fino a domenica. Vendita riservata ai maggiorenni.',
                               'Le offerte', P['offerte']), logo_size=30)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'CALICE', 'wine', 'Enoteca in via di Città, fra il Campo e il Duomo: i vini delle cantine '
                   'che conosciamo una per una, la degustazione del giovedì sera, la consegna a temperatura.',
                   [('La cantina', [('Rossi', N.cat_url('rossi')), ('Bianchi', N.cat_url('bianchi')),
                                    ('Bollicine', N.cat_url('bollicine')), ('Offerte', P['offerte'])]),
                    ('Calice', [('Le cantine', P['cantine']), ('Le degustazioni', P['degustazioni']), ('I preferiti', P['preferiti']),
                                ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni', P['assistenza'] + '#spedizioni'), ('Maggiorenni', P['assistenza'] + '#scrivici'),
                               ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Calice s.r.l. · Siena · P. IVA 00000000000 · Vendita di alcolici riservata ai maggiori di '
                   '18 anni · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera della cantina', 'Una bottiglia al mese, raccontata.',
                              'La bottiglia del mese, le date delle degustazioni, le annate che arrivano.',
                              'Fatto: la prima lettera arriva col vino del mese.'),
                   social=[('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.42  # la larghezza media di una lettera dei titoli (Cormorant Garamond, per la vetrina)


def vetrina(t):
    # largo: la larghezza media delle lettere di ogni titolo, misurata col carattere vero (spaziatura compresa)
    # il bollino in JetBrains Mono: il Cormorant, in un disco piccolo, veniva sottile e spento
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-8, bollino_fondo=ORO, bollino_testo=C['nero'],
             bollino_font=MONO)
    return N.vetrina(t, [
        N.offerta('cl-o1', K.sfondo_video(V_VERSA, overlay=0.12), 'Il rosso della settimana',
                  'Il Chianti Classico a 14,90 €.', 'Castellina in Chianti, annata 2022: rubino, viola e ciliegia. Era 19,90 €.',
                  'I rossi', N.cat_url('rossi'), bollino='−25%', nota='Solo ai maggiorenni · consegna in 48 ore', primo=True, **dict(f, largo=0.352)),
        N.offerta('cl-o2', K.sfondo_video(V_CANDELE, overlay=0.12), 'Bollicine per le feste',
                  'Sei bottiglie, la settima è nostra.', 'Sei extra dry nel cartone da sette: la settima la mettiamo noi. 90 €.',
                  'Cassette e regali', N.cat_url('regali'), bollino='6+1', nota='Consegna gratuita da 60 €', **dict(f, largo=0.34)),
        N.offerta('cl-o3', K.sfondo_video(V_COLLINE, overlay=0.12), 'In vigna · novità', 'Il Brunello 2020 è arrivato.',
                  'L’annata calda di Montalcino: granato, prugna e tabacco. Da aprire un’ora prima, 54 €.', 'Il Brunello',
                  N.cat_url('rossi'), bollino='Nuovo', nota='Con l’astuccio di legno, in omaggio', **dict(f, largo=0.353)),
    ], transizione='crossFade')


# le schede sotto la vetrina: «prezzi» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='prezzi', voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Bianchi', {'category': 'bianchi', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('bianchi')),
        ('Bollicine', {'category': 'bollicine', 'orderby': 'popularity'}, 'Vedi tutte', N.cat_url('bollicine')),
        ('Cassette e regali', {'category': 'regali', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('regali')),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'La cantina', 'Dalla vigna alla tavola',
                              ('Il Brunello,', '54 €.', 'L’annata 2020, appena arrivata.', 'Rossi', N.cat_url('rossi'),
                               'pexels-8473140.jpg'), tutto='Tutta la cantina')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte della settimana', 'Fino a domenica.',
                              'Le offerte della settimana finiscono tra:',
                              ('Il Chianti Classico,', '14,90 €.', 'Annata 2022. Era 19,90 €.', 'Rossi', N.cat_url('rossi'),
                               'pexels-8473179.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Arrivati in cantina',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('Il bianco', 'in anfora.', 'Sei mesi sulle bucce, 29 €.', 'Bianchi', N.cat_url('bianchi'),
                              'pexels-7022837.jpg'), tutte='Tutta la cantina')


# ── Quattro calici: lo slider resta fermo e il calice, visto dall'alto, si riempie quattro volte ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1000.0
GX, GY = 430.0, 480.0           # il calice sul computer
GXM, GYM, KM = 320.0, 410.0, 0.8  # sul telefono
VINI = [
    # occhiello, nome, colore del vino, note, prezzo, finestra (entra, pieno, fino a, esce)
    ('San Gimignano · bianco', 'Vernaccia di San Gimignano', '#E3C96A',
     ('paglierino coi riflessi verdi', 'mela, mandorla, fiori di campo', 'i crostini neri, il pecorino giovane'), '15 €',
     (0, 0, 20, 26)),
    ('Maremma · rosato', 'Rosato di Toscana', '#E8907F',
     ('buccia di cipolla, appena', 'fragolina, pompelmo, sale', 'la pappa al pomodoro'), '14 €', (20, 28, 44, 50)),
    ('Castellina · rosso', 'Chianti Classico', '#8E1B2E',
     ('rubino vivo', 'viola, ciliegia, un po’ di pepe', 'la ribollita, la bistecca'), '14,90 €', (44, 52, 68, 74)),
    ('Montalcino · rosso', 'Brunello di Montalcino', '#561020',
     ('granato, coi riflessi aranciati', 'prugna, tabacco, cuoio', 'il cinghiale, i formaggi stagionati'), '54 €',
     (68, 76, 100, 100)),
]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def _calice(d):
    """Un cerchio di diametro d col centro sul calice: sul computer e sul telefono."""
    a = _pc(GX - d / 2, GY - d / 2, d, d, W, H)
    b = _pc(GXM - d * KM / 2, GYM - d * KM / 2, d * KM, d * KM, WM, HM)
    return a, b


def _finestra(f, extra_in=None, extra_out=None):
    """Le tappe di un vino: entra (da extra_in), pieno, fino a, esce."""
    e, p, q, u = f
    ei, eo = extra_in or {}, extra_out or {}
    piena = {k: (1 if k == 's' else 0) for k in ei}
    t = [dict({'p': 0, 'o': 100 if p == 0 else 0}, **(piena if p == 0 else ei))]
    if p > 0:
        t += [dict({'p': e, 'o': 0}, **ei), dict({'p': p, 'o': 100}, **piena)]
    t.append(dict({'p': q, 'o': 100}, **piena))
    if u < 100:
        t += [dict({'p': u, 'o': 0}, **(eo or piena)), dict({'p': 100, 'o': 0}, **(eo or piena))]
    else:
        t.append(dict({'p': 100, 'o': 100}, **piena))
    return {'inizio': 0, 'fine': 100, 'tappe': t}


def _testo(f):
    """La finestra dei testi: escono prima che entri il successivo (non si sovrappongono mai)."""
    e, p, q, u = f
    return (0 if p == 0 else p - 4, p, q, 100 if u == 100 else q + 4)


def calici(t):
    m = K.mob
    L = [
        K.livello('cc-o', contenuto='Dall’alto · scorrete', x=6, y=8, w=40, size=13, peso='600', colore=ORO, letterSpacing=3,
                  textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.8, 'width': 86}}),
        K.livello('cc-t', contenuto='Quattro calici,<br><em>quattro colori.</em>', tag='h2', x=6, y=12, w=40, size=60, peso='600',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(32), 'x': 7, 'y': 5.2, 'width': 86}}),
    ]
    # il calice visto dall'alto: l'ombra del piede, il vetro, il bordo
    a, b = _calice(470)
    L.append(K.livello('cc-piede', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['chiaro'], 4), borderRadius=999,
                       borderWidth=1, borderColor=mix(C['chiaro'], 14), entra='zoomIn', ritardo=200, durata=900,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a, b = _calice(380)
    L.append(K.livello('cc-vetro', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['chiaro'], 7), borderRadius=999,
                       entra='zoomIn', ritardo=300, durata=900,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # il vino: per ognuno una goccia che cade dall'alto e il colore che si allarga dal centro sopra il vino di prima
    for k, (occ, nome, col, note, prezzo, f) in enumerate(VINI):
        a, b = _calice(300)
        L.append(K.livello('cc-vino%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=col, borderRadius=999,
                           entra='fadeIn', ritardo=400, scrollMove=_finestra(f, {'s': 0.12}),
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        if f[1] > 0:
            e = f[0]
            goccia = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': -520, 'o': 0, 's': 1}, {'p': e - 2, 'y': -520, 'o': 0, 's': 1},
                                                          {'p': e, 'y': -480, 'o': 100, 's': 1},
                                                          {'p': e + 4, 'y': 0, 'o': 100, 's': 1.4},
                                                          {'p': e + 5, 'y': 0, 'o': 0, 's': 2.6}, {'p': 100, 'y': 0, 'o': 0, 's': 2.6}]}
            a, b = _calice(22)
            L.append(K.livello('cc-goccia%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=col, borderRadius=999,
                               entra='fadeIn', ritardo=0, scrollMove=goccia,
                               responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # il riflesso: una luce sul vino che gira come quando si fa ruotare il calice
    a, b = _calice(300)
    L.append(K.livello('cc-riflesso', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                       shapeGradient={'angle': 135, 'from': mix(C['chiaro'], 16), 'fromStop': 0, 'to': 'transparent'},
                       entra='fadeIn', ritardo=500,
                       scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': 0}, {'p': 100, 'r': 1440}]},
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a, b = _calice(384)
    L.append(K.livello('cc-bordo', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='transparent', borderRadius=999,
                       borderWidth=4, borderColor=mix(C['chiaro'], 40), entra='zoomIn', ritardo=300, durata=900,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # a destra: il vino, le note, il prezzo
    for k, (occ, nome, col, note, prezzo, f) in enumerate(VINI):
        mov = _finestra(_testo(f), {'y': 18}, {'y': -18})
        L.append(K.livello('cc-occ%d' % k, contenuto=occ, x=53, y=24, w=40, size=13, peso='600', colore=ORO, letterSpacing=2.5,
                           textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=300, scrollMove=mov,
                           responsive={'mobile': {'x': 7, 'y': 62.8, 'width': 86, 'fontSize': m(11)}}))
        L.append(K.livello('cc-nome%d' % k, contenuto=nome, tag='p', x=53, y=28, w=40, size=62, peso='600', colore=C['chiaro'],
                           fontFamily=TITOLI, lineHeight=1, entra='fadeIn', ritardo=300, scrollMove=mov,
                           responsive={'mobile': {'x': 7, 'y': 65.6, 'width': 86, 'fontSize': m(30)}}))
        righe = '<br>'.join('<strong>%s</strong> · %s' % (a_, b_) for a_, b_ in zip(('Colore', 'Naso', 'In tavola'), note))
        L.append(K.livello('cc-note%d' % k, contenuto=righe, x=53, y=48, w=40, size=17, colore=mix(C['chiaro'], 82),
                           lineHeight=1.75, entra='fadeIn', ritardo=400, scrollMove=mov,
                           responsive={'mobile': {'x': 7, 'y': 71.8, 'width': 86, 'fontSize': m(13)}}))
        L.append(K.livello('cc-prezzo%d' % k, contenuto=prezzo.replace(' €', '&nbsp;€') + ' · la bottiglia', x=53, y=65, w=40,
                           size=24, peso='500', colore=C['chiaro'], fontFamily=MONO, entra='fadeIn', ritardo=500, scrollMove=mov,
                           responsive={'mobile': {'x': 7, 'y': 84, 'width': 86, 'fontSize': m(16)}}))
        # il segno di dov'è: quattro gocce in fila, quella del vino di adesso si accende
        a = _pc(GX - 66 + k * 44 - 8, GY + 270 - 8, 16, 16, W, H)
        bm = _pc(45 + k * 36, 902, 20, 20, WM, HM)
        acceso = _finestra(f, {'s': 1}, {'s': 1})
        for tp in acceso['tappe']:
            tp['o'] = 40 + tp['o'] * 0.6
            tp['s'] = 1 + 0.5 * (tp['o'] - 40) / 60
        L.append(K.livello('cc-punto%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=col, borderRadius=999,
                           borderWidth=1, borderColor=mix(C['chiaro'], 50), entra='fadeIn', ritardo=600, scrollMove=acceso,
                           responsive={'mobile': {'x': bm[0], 'y': bm[1], 'width': bm[2], 'height': bm[3]}}))
    L.append(K.livello('cc-b', tipo='button', contenuto='Tutta la cantina', x=53, y=78, w='auto', size=15, peso='600',
                       colore=C['nero'], bgColor=ORO, padding=14, borderRadius=2, entra='fadeIn', ritardo=600,
                       buttonUrl=P['negozio'], hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 200},
                       responsive={'mobile': {'x': 54, 'y': 88.4, 'fontSize': m(13)}}))
    sl = [K.diapositiva('cc-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
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
        N.testata_sezione(t, 'La cantina', 'Cosa si stappa stasera?', P['negozio'], 'Tutta la cantina'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, calici(t)))
    out.append(t.section([
        N.testata_sezione(t, 'I più stappati', 'Quelli che riordiniamo ogni settimana', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'La cassetta,', 'Toscana in tre.', 'Chianti, Nobile e Rosso di Montalcino: 59 €.',
                               'Cassette e regali', N.cat_url('regali'), K.photo_bg('pexels-4877844.jpg'), size=34)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Il Vin Santo,', 'coi cantucci.', 'Cinque anni nei caratelli: 28 € la mezza.',
                               'Rosati e da dessert', N.cat_url('rosati'), K.photo_bg('pexels-4994753.jpg'), size=34)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte della settimana', color=ORO),
                      N.titolo(t, 'Fino a domenica.', size=44, color=C['chiaro']),
                      N.testo(t, 'Il Chianti Classico, il Nobile, la cassetta in tre, la bollicina dell’aperitivo.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left', pad_top=16)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_BOTTE, 'In cantina', 'Le conosciamo <em>una per una.</em>',
                             'Ogni bottiglia viene da una cantina dove siamo stati: abbiamo camminato la vigna, toccato le botti, '
                             'assaggiato le annate.', overlay=40, extra=[N.pulsante(t, 'Le cantine', P['cantine'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Bollicine', 'Per brindare', N.cat_url('bollicine')),
        t.one([N.prodotti(t, category='bollicine', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-6762291.jpg', 'Due amici assaggiano il rosso alla degustazione', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Il giovedì sera'), N.titolo(t, 'Sei vini,<br><em>un tagliere.</em>', size=52),
                      N.testo(t, 'Ogni giovedì alle 19 dodici persone al banco lungo: sei vini di una zona, il pecorino e la '
                                 'finocchiona, il sommelier che racconta. Si prenota qui.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Dodici posti, il giovedì alle 19', 'Sei vini e il tagliere: 25 €',
                              'Chi compra quella sera ha il 10%']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'Le degustazioni', P['degustazioni'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'La cantina', 'Dalla vigna alla tavola', 'Rossi, bianchi, bollicine, rosati e vini da dessert, '
                            'cassette e regali, calici e dispensa: consegna a temperatura, solo ai maggiorenni.', colonne=3,
                            banner=N.banner(t, 'Il Chianti Classico,', '14,90 €.', 'Annata 2022, fino a domenica.', 'Rossi',
                                            N.cat_url('rossi'), K.photo_bg('pexels-8473179.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Il Nobile,', '22 €.', 'Montepulciano, annata 2021. Era 26 €.', 'Rossi', N.cat_url('rossi'),
                        K.photo_bg('pexels-8473177.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'La bollicina,', '12 €.', 'Extra dry, per l’aperitivo. Era 15 €.', 'Bollicine', N.cat_url('bollicine'),
                        K.photo_bg('pexels-9822760.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Le offerte della settimana', 'Fino a domenica', 'Il Chianti Classico, il Nobile, la cassetta in tre '
                            'e l’extra dry a meno, finché ce n’è in cantina.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('thermometer', 'Spedito a temperatura'), ('truck', 'Gratis da 60 €'),
                                                   ('package', 'Imballo che non si rompe'), ('badge-check', 'Solo ai maggiorenni')],
                             bundle='Si stappa insieme', miniature='left', altezza='620px', bottone='Aggiungi al carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Consegna gratuita da 60 €. Vendita riservata ai maggiorenni.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'L’indirizzo, la consegna, il pagamento. Il vino si consegna solo ai maggiorenni: al '
                                            'corriere si mostra un documento.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, gli indirizzi, le degustazioni prenotate, i vini da ricomprare.',
                            ('Dov’è la mia cassetta?', 'Scriveteci col numero dell’ordine: vi diciamo dov’è.', 'Scrivici',
                             P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto della cantina.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'Al telefono e in enoteca dalle 10 alle 20, dal martedì alla domenica.', [
        ('truck', 'Spedizioni', 'In 48 ore, gratis sopra i 60 €.', 'spedizioni'),
        ('thermometer', 'Temperatura', 'D’estate col cartone isolante.', 'spedizioni'),
        ('badge-check', 'Maggiorenni', 'Il vino si vende solo dai 18 anni.', 'scrivici'),
        ('rotate-ccw', 'Bottiglie difettose', 'Sapore di tappo? La cambiamo.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 60 €; sotto, 9 €. Parte in 48 ore, nell’imballo di cartone sagomato.'),
        ('Perché il corriere chiede un documento?', 'La legge vieta di vendere alcolici ai minori: alla consegna si mostra un '
                                                    'documento.'),
        ('E se il vino sa di tappo?', 'Scriveteci entro 30 giorni con una foto: vi mandiamo un’altra bottiglia.'),
        ('Spedite d’estate?', 'Sì, col cartone isolante; sopra i 32 gradi aspettiamo la mattina presto.')],
        CONTATTI, campi_extra='Una cassetta per un’azienda\nUna bottiglia che sa di tappo\n',
        foto=('pexels-14129222.jpg', 'In enoteca, davanti agli scaffali dei vini'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa pagina <em>è stata stappata.</em>', 'Non c’è più: cercate nella cantina qui sopra.',
                        K.video_bg(V_TAPPI, overlay=40))


def build_cantine():
    t = Tpl(ID, 'cantine')
    out = [K.intro(t, 'Le cantine', ['Ci siamo stati,', 'una per una.'], 'Ventidue cantine fra il Chianti, Montalcino, '
                   'Montepulciano e la costa: piccole, quasi tutte di famiglia, alcune in biologico.',
                   bg=K.video_bg(V_COLLINE, overlay=40), size=86)]
    out.append(t.section([
        t.one([K.numeri(t, [('22', 'cantine'), ('4', 'zone di Toscana'), ('9', 'in biologico'), ('180', 'etichette in cantina')],
                           colonne=4, size=52)]),
    ], padding='default', width='large', style={'padding_top': 56, 'padding_bottom': 48}))
    zone = [('grape', 'Il Chianti Classico', 'Castellina, Radda, Gaiole: il sangiovese delle colline fra Siena e Firenze.'),
            ('sun', 'Montalcino', 'Il Brunello e il Rosso: il sangiovese grosso, sulle pendici calde verso il mare.'),
            ('wine', 'Montepulciano', 'Il Nobile: il prugnolo gentile, la cittadina di mattoni sul colle.'),
            ('wind', 'La costa', 'Il Vermentino e i rossi della Maremma: il sale, la macchia, il vento.')]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le zone'), N.titolo(t, 'Quattro posti,<br><em>quattro vini.</em>', size=46),
                      t.classic('iconlist', {
                          'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''}
                                    for i, a, b in zone],
                          'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'},
                          style={})], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-38270187.jpg', 'I grappoli di sangiovese maturi fra le foglie', ratio='4/5')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 56, 'padding_bottom': 72}))
    out.append(K.video_frase(t, V_DOGHE, 'Nelle botti', 'Il tempo <em>è un ingrediente.</em>',
                             'Il Brunello tre anni in botte grande, il Vin Santo cinque nei caratelli: compriamo le annate quando '
                             'sono pronte, non prima.', overlay=40))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Come scegliamo'), N.titolo(t, 'Prima la vigna, poi la bottiglia.', size=44),
                      N.testo(t, 'Andiamo in vigna in primavera e in cantina in autunno; assaggiamo dalla botte e in bottiglia. '
                                 'Se il vino ci piace e la cantina ci convince, entra.', size=17),
                      K.faq(t, [('Avete vini biologici?', 'Sì, nove cantine: nella scheda c’è scritto.'),
                                ('E fuori dalla Toscana?', 'Solo le bollicine metodo classico, da due cantine del nord.'),
                                ('Si può visitare una cantina?', 'Sì: organizziamo la gita di primavera, chiedeteci le date.')],
                            'can')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-30557559.jpg', 'Le bottiglie coricate sugli scaffali della cantina', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        N.testata_sezione(t, 'Dalle cantine', 'I rossi', N.cat_url('rossi')),
        t.one([N.prodotti(t, category='rossi', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_degustazioni():
    t = Tpl(ID, 'degustazioni')
    out = [K.intro(t, 'Le degustazioni', ['Il giovedì sera,', 'al banco lungo.'], 'Dodici persone, sei vini di una zona, '
                   'il pecorino e la finocchiona, il sommelier che racconta: alle 19, in via di Città.',
                   bg=K.video_bg(V_TAVOLA, overlay=40), size=86)]
    serate = [('grape', 'Il Chianti Classico', 'Sei vini da Castellina, Radda e Gaiole.'),
              ('sun', 'Montalcino', 'Il Rosso e il Brunello, due annate a confronto.'),
              ('sparkles', 'Le bollicine', 'Metodo classico: trentasei mesi contro dodici.'),
              ('wine', 'I vini da dessert', 'Il Vin Santo coi cantucci, l’ultimo giovedì del mese.')]
    out.append(t.section([
        N.testata_sezione(t, 'Le serate', 'Una zona alla volta'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in serate],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 56}))
    out.append(t.section([
        t.one([K.numeri(t, [('12', 'posti al banco'), ('6', 'vini a serata'), ('25 €', 'col tagliere'),
                            ('10%', 'su quello che comprate')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    modulo = t.classic('form', {
        'fields': [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True, segnaposto='nome@esempio.it'),
                   K.campo('select', 'La serata', 'serata', True, opzioni='Il Chianti Classico\nMontalcino\nLe bollicine\n'
                                                                        'I vini da dessert'),
                   K.campo('select', 'Quanti', 'quanti', True, opzioni='Uno\nDue\nTre\nQuattro'),
                   K.campo('date', 'Il giovedì', 'giorno', True),
                   K.campo('checkbox', 'L’età', 'maggiorenne', True, '1-1', opzioni='Ho almeno 18 anni'),
                   K.campo('textarea', 'Qualcosa da dirci', 'note', False, '1-1', segnaposto='Allergie, un compleanno, un regalo')],
        'email_subject': 'Calice — un posto al banco', 'submit_text': 'Prenota il posto', 'submit_icon': '',
        'success_message': 'Grazie: vi scriviamo entro domani per confermare.', 'submit_full_width': False,
        'submit_alignment': 'left', 'label_color': C['testo'], 'label_size': '13', 'label_weight': '500',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': C['chiaro'], 'input_color': C['testo'],
        'input_border_color': C['filo'], 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 2,
        'input_focus_border': C['acc'], 'input_focus_shadow': False, 'gap': '14', 'submit_bg': C['acc'],
        'submit_color': C['on_acc'], 'submit_radius': 2, 'submit_padding_x': '28', 'submit_padding_y': '15',
        'submit_font_size': '14', 'submit_font_weight': '600', 'submit_text_transform': 'none',
        'submit_letter_spacing': '0', 'submit_hover_bg': C['nero'], 'privacy_checkbox': True},
        style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
               'border_radius': 0})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Un posto al banco'), N.titolo(t, 'Prenotate<br><em>il giovedì.</em>', size=46),
                      N.testo(t, 'Scegliete la serata e il giovedì: vi scriviamo per confermare. Si paga la sera stessa; le '
                                 'degustazioni sono solo per i maggiorenni.', size=17),
                      K.image(t, 'pexels-7447405.jpg', 'Una signora alza il calice di rosso alla degustazione', ratio='4/3')],
                     wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start'),
    ], html_id='prenota', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 88, 'padding_bottom': 88}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Dove'), N.titolo(t, 'In via di Città.', size=40),
                      N.testo(t, 'Fra il Campo e il Duomo, al 77: la vetrina con le bottiglie coricate. Dal martedì alla '
                                 'domenica, 10–20; il giovedì fino alle 22.', size=17),
                      K.faq(t, [('Si può venire senza prenotare?', 'In enoteca sì, sempre; al banco del giovedì no, i posti sono '
                                                                    'dodici.'),
                                ('C’è da mangiare?', 'Il tagliere di pecorino e salumi, e il pane toscano.'),
                                ('Si può regalare?', 'Sì: c’è il biglietto della degustazione per due, nei regali.')], 'dov')],
                     wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-14129222.jpg', 'Due clienti scelgono una bottiglia dagli scaffali', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='dove', padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'Quattro calici',
    ['enoteca', 'vino', 'vini toscani', 'degustazioni', 'woocommerce', 'e-commerce', 'siena', 'regali'],
    'Il negozio online di un’enoteca a Siena: in apertura la fascia delle offerte in dissolvenza e le quattro schede coi vini, '
    'il mega menu con la cantina, le offerte e le novità; a metà pagina il calice visto dall’alto che si riempie quattro volte '
    'mentre si scorre, dalla Vernaccia al Brunello, ognuno col suo colore, il naso e il piatto; le cantine, le degustazioni del '
    'giovedì con la prenotazione, la scheda con l’astuccio a scelta e il video del vino versato. Vendita riservata ai '
    'maggiorenni.',
    [('La cantina', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Rossi', '{{PAGINA:negozio}}?product_cat=rossi'), ('Bianchi', '{{PAGINA:negozio}}?product_cat=bianchi'),
     ('Bollicine', '{{PAGINA:negozio}}?product_cat=bollicine'), ('Le cantine', P['cantine']), ('Il giovedì', P['degustazioni'])],
    [('home', 'Home', None), ('negozio', 'La cantina', 'cantina'), ('cantine', 'Le cantine', 'cantine'),
     ('degustazioni', 'Le degustazioni', 'degustazioni'), ('offerte', 'Le offerte della settimana', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cormorant Garamond\', Garamond, Georgia, serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Cormorant Garamond', 'Manrope', 'JetBrains Mono'],
    corsivo_di=['Cormorant Garamond'], peso_titoli='600', raggio=2, grana=0, titoli_line='1.02', titoli_spazio='-0.005em')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'La cantina': ('pannello-reparti', 'Pannello della cantina'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['preview_v'] = THEME['preview_mobile_v'] = '4'  # anteprime con le testate nuove (2 nov 2026)
THEME['cursor']['style'] = 'calice'
THEME['cursor']['ring_color'] = HEX['accent']    # il vetro, verde bottiglia
THEME['cursor']['dot_color'] = HEX['primary']    # il vino


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'cantine.json': build_cantine(),
                        'degustazioni.json': build_degustazioni(), 'offerte.json': build_offerte(),
                        'assistenza.json': build_assistenza(), 'preferiti.json': build_preferiti(),
                        'carrello.json': build_carrello(), 'cassa.json': build_cassa(), 'account.json': build_account(),
                        'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'astuccio': {'nome': 'Astuccio', 'termini': ASTUCCI}},
                    dentro='La bottiglia nell’imballo di cartone sagomato, l’astuccio se l’avete scelto, il biglietto della '
                           'cantina.')


if __name__ == '__main__':
    main()
