# -*- coding: utf-8 -*-
"""Genera il tema «Crocevia» (id crocevia): un grande magazzino online multisettore — moda, casa, tech, bellezza, sport,
bimbi, tempo libero, gourmet — con le offerte del giorno, la consegna in 24 ore e il reso in 30 giorni.

Tema chiaro e vivace: il viola elettrico sui pulsanti, il verde acqua, il corallo dei prezzi scontati, la testata blu
notte. Titoli in Bricolage Grotesque, testo in Inter, dati in Space Mono. Card arrotondate col bordo, foto quadrate.

Apertura: la vetrina delle offerte del giorno, come nei grandi magazzini online: una fascia bassa che scorre
da sola (la settimana col codice, le sneakers, le cuffie, il tappetino), sotto le quattro schede coi prodotti veri; il
mega menu apre i pannelli dei reparti, delle offerte (col conto alla rovescia) e delle novità. Cursore: il cartellino
del prezzo.

WooCommerce: i reparti, il catalogo coi filtri per reparto e marca, il confronto, la scheda prodotto (taglie delle scarpe,
video nella galleria), il carrello, la cassa a passi, l'account, i preferiti; la Settimana Crocevia col codice sconto.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'crocevia'
NAME = 'Crocevia'

HEX = {
    'primary': '#5B2EFF', 'primary_contrast': '#FFFFFF', 'secondary': '#00B39A', 'secondary_contrast': '#06221E',
    'accent': '#D21E40', 'dark': '#121027', 'light': '#FFFFFF', 'muted': '#ECEAF6', 'muted_contrast': '#16142B',
    'text': '#16142B', 'text_muted': '#57546F', 'background': '#F6F5FB', 'border': '#D9D6EA', 'link': '#5B2EFF',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'reparti', 'offerte', 'confronta', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'aiuto@crocevia.example', '+39 02 0000 2424', 'tel:+390200002424'

V_INCROCIO, V_CITTA, V_NASTRO, V_MAGLIA, V_SCATOLA, V_NASTRINO = (
    'pexels-video-2340563', 'pexels-video-15679245', 'pexels-video-5903898', 'pexels-video-9594579', 'pexels-video-9594575',
    'pexels-video-4440949')
VIDEOS = [V_INCROCIO, V_CITTA, V_NASTRO, V_MAGLIA, V_SCATOLA, V_NASTRINO]
PHOTOS = [
    'pexels-11324518.jpg', 'pexels-11324548.jpg', 'pexels-34207686.jpg', 'pexels-8104329.jpg', 'pexels-15403324.jpg',
    'pexels-7241444.jpg', 'pexels-8217430.jpg', 'pexels-8217295.jpg', 'pexels-8217294.jpg', 'pexels-33693664.jpg',
    'pexels-34241799.jpg', 'pexels-33797083.jpg', 'pexels-8533397.jpg', 'pexels-38448889.jpg', 'pexels-36179805.jpg',
    'pexels-15601643.jpg', 'pexels-3614119.jpg', 'pexels-35062097.jpg', 'pexels-15574229.jpg', 'pexels-3785784.jpg',
    'pexels-7256120.jpg', 'pexels-7256160.jpg', 'pexels-3800060.jpg', 'pexels-4793328.jpg', 'pexels-8899524.jpg',
    'pexels-9004302.jpg', 'pexels-16679756.jpg', 'pexels-16567048.jpg', 'pexels-16567046.jpg', 'pexels-7879967.jpg',
    'pexels-7879796.jpg', 'pexels-7269676.jpg', 'pexels-7269674.jpg', 'pexels-7269668.jpg', 'pexels-3661201.jpg',
    'pexels-3662945.jpg', 'pexels-34817526.jpg', 'pexels-35230776.jpg', 'pexels-3852577.jpg', 'pexels-24551366.jpg',
    'pexels-32505301.jpg', 'pexels-10554053.jpg', 'pexels-14545422.jpg', 'pexels-7657839.jpg', 'pexels-6167340.jpg',
    'pexels-6167329.jpg', 'pexels-6167345.jpg', 'pexels-4113365.jpg', 'pexels-5868269.jpg', 'pexels-5868133.jpg',
    'pexels-5868240.jpg', 'pexels-4127636.jpg', 'pexels-36138736.jpg', 'pexels-8387122.jpg', 'pexels-39962496.jpg',
]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
ACQUA, CORALLO = C['sec'], C['acc2']
TITOLI, MONO = 'Bricolage Grotesque', 'Space Mono'
N = Negozio(K, NAME, P, raggio=14, ratio='1-1', card='border', hover='zoom', testata_scura=True,
            consegna='Domani a casa, gratis da 29 €', evidenza='Il più scelto del reparto', marca='marca', peso_titoli='700')

REPARTI = [  # slug, nome, descrizione, foto, sconto, stato
    ('moda', 'Moda', 'Sneakers, zaini, occhiali.', 'pexels-11324518.jpg', '−30%', 'In partenza'),
    ('casa', 'Casa', 'Tazze, piante, piccole cose belle.', 'pexels-8533397.jpg', '−25%', 'Imbarco'),
    ('tech', 'Tech', 'Cuffie, smartwatch, casse.', 'pexels-8217430.jpg', '−20%', 'In partenza'),
    ('bellezza', 'Bellezza', 'Profumi, trucchi, pennelli.', 'pexels-15574229.jpg', '3 × 2', 'Ultimi posti'),
    ('sport', 'Sport', 'Yoga, pesi, borracce.', 'pexels-4793328.jpg', '−40%', 'In partenza'),
    ('bimbi', 'Bimbi', 'Giochi di legno e costruzioni.', 'pexels-7269676.jpg', '−15%', 'Imbarco'),
    ('tempo-libero', 'Tempo libero', 'Puzzle, libri, giochi da tavolo.', 'pexels-3852577.jpg', '−20%', 'In orario'),
    ('gourmet', 'Gourmet', 'Caffè e cioccolato.', 'pexels-6167340.jpg', '−25%', 'Ultimi posti'),
]
CATEGORIE = [(s, n, d, f) for s, n, d, f, _, _ in REPARTI]
NUMERI = ['38', '39', '40', '41', '42', '43', '44']
PRODOTTI = [
    prodotto('CRO-001', 'Sneakers Passo, bianche', 'sneakers-passo', 'moda', 69, 49, 60, 4.6, 1840, True,
             ['pexels-11324518.jpg', 'pexels-11324548.jpg'], ['Pelle e rete traspirante', 'Suola in gomma riciclata',
             'Lacci di ricambio', 'Dal 38 al 44'], 'Le sneakers bianche di tutti i giorni, con la suola che non scivola.',
             varianti=('numero', NUMERI, ['38', '44']), attributi={'marca': ['Passo']}, cross=['CRO-003', 'CRO-016']),
    prodotto('CRO-002', 'Zaino da città Valico', 'zaino-valico', 'moda', 79, 0, 35, 4.7, 612, False,
             ['pexels-34207686.jpg'], ['Tela cerata impermeabile', 'Tasca imbottita per il portatile', '22 litri',
             'Spallacci regolabili'], 'Lo zaino che va in ufficio e in montagna: tela cerata, ci sta il portatile.',
             attributi={'marca': ['Valico']}),
    prodotto('CRO-003', 'Occhiali da sole Faro', 'occhiali-faro', 'moda', 59, 45, 40, 4.5, 388, False,
             ['pexels-8104329.jpg', 'pexels-15403324.jpg'], ['Lenti polarizzate', 'Montatura in acetato', 'Custodia rigida',
             'Protezione UV400'], 'Gli occhiali neri dalla montatura squadrata, con le lenti polarizzate.',
             attributi={'marca': ['Faro']}),
    prodotto('CRO-004', 'Cuffie wireless Eco', 'cuffie-eco', 'tech', 129, 99, 30, 4.6, 2210, True,
             ['pexels-7241444.jpg'], ['Cancellazione attiva del rumore', '40 ore di musica', 'Ricarica USB-C', 'Si piegano'],
             'Silenziose in treno, comode per ore: le cuffie con la cancellazione del rumore.', attributi={'marca': ['Eco']},
             cross=['CRO-007']),
    prodotto('CRO-005', 'Smartwatch Polso', 'smartwatch-polso', 'tech', 199, 0, 25, 4.7, 1470, True,
             ['pexels-8217430.jpg', 'pexels-8217295.jpg', 'pexels-8217294.jpg'], ['GPS e cardiofrequenzimetro',
             'Sette giorni di batteria', 'Impermeabile', 'Notifiche dal telefono'], 'L’orologio che conta i passi e il sonno, '
                                                                                       'e dura una settimana.',
             attributi={'marca': ['Polso']}, cross=['CRO-016']),
    prodotto('CRO-006', 'Cassa bluetooth Mimetica', 'cassa-mimetica', 'tech', 59, 0, 45, 4.4, 690, False,
             ['pexels-33693664.jpg', 'pexels-33797083.jpg'], ['Impermeabile IP67', '12 ore di musica', 'Laccetto in silicone',
             'Si abbina a una seconda'], 'La cassa da zaino, mimetica e impermeabile.', attributi={'marca': ['Tuono']}),
    prodotto('CRO-007', 'Cassa bluetooth Rombo', 'cassa-rombo', 'tech', 49, 39, 50, 4.5, 512, False,
             ['pexels-34241799.jpg'], ['Bassi profondi', '10 ore di musica', 'Laccetto da polso', 'Vivavoce'],
             'Piccola, nera a righe, coi bassi di una cassa grande.', attributi={'marca': ['Tuono']}),
    prodotto('CRO-008', 'Tazza Tondo, bianca', 'tazza-tondo', 'casa', 14, 0, 120, 4.8, 330, False,
             ['pexels-8533397.jpg'], ['Porcellana', '350 ml', 'Col piattino', 'Lavastoviglie'], 'La tazza bianca col piattino '
                                                                                                 'lungo per il biscotto.',
             attributi={'marca': ['Tondo']}, cross=['CRO-023']),
    prodotto('CRO-009', 'Tazza Rossa smaltata', 'tazza-rossa', 'casa', 16, 12, 90, 4.6, 210, False,
             ['pexels-38448889.jpg', 'pexels-36179805.jpg'], ['Gres smaltato rosso', '300 ml', 'Lavastoviglie e microonde',
             'Manico grande'], 'Il rosso che sveglia: la tazza del caffè lungo.', attributi={'marca': ['Tondo']},
             cross=['CRO-023']),
    prodotto('CRO-010', 'Aloe in vaso di cemento', 'aloe-vaso-cemento', 'casa', 24, 0, 40, 4.7, 280, False,
             ['pexels-15601643.jpg', 'pexels-3614119.jpg'], ['Aloe vera di serra', 'Vaso in cemento grigio', 'Si annaffia ogni '
             'due settimane', 'Spedita protetta'], 'La pianta che non muore: aloe nel vaso di cemento.',
             attributi={'marca': ['Serra Viva']}),
    prodotto('CRO-011', 'Pianta nel vaso arancio', 'pianta-vaso-arancio', 'casa', 29, 0, 25, 4.5, 96, False,
             ['pexels-35062097.jpg'], ['Dracena di serra', 'Vaso in plastica riciclata', 'Luce indiretta', 'Alta 50 cm'],
             'La dracena nel vaso arancio, per un angolo che ha bisogno di colore.', attributi={'marca': ['Serra Viva']}),
    prodotto('CRO-012', 'Eau de parfum Vetro, 50 ml', 'eau-de-parfum-vetro', 'bellezza', 79, 59, 30, 4.6, 540, True,
             ['pexels-15574229.jpg', 'pexels-3785784.jpg'], ['Fiori bianchi e legno di cedro', 'Flacone in vetro spesso',
             'Dura tutto il giorno', 'Fatto a Grasse'], 'Fiori bianchi e cedro: il profumo che si ricorda.',
             attributi={'marca': ['Vetro']}),
    prodotto('CRO-013', 'Palette viso Pesca', 'palette-viso-pesca', 'bellezza', 35, 0, 50, 4.5, 418, False,
             ['pexels-7256120.jpg', 'pexels-7256160.jpg'], ['Fard, illuminante e terra', 'Con lo specchio', 'Senza parabeni',
             'Tre pennelli inclusi'], 'Tutto il viso in una palette color pesca.', attributi={'marca': ['Pesca']}),
    prodotto('CRO-014', 'Set di pennelli', 'set-pennelli', 'bellezza', 29, 0, 60, 4.6, 302, False,
             ['pexels-3800060.jpg'], ['Otto pennelli', 'Setole sintetiche morbide', 'Bicchiere portapennelli', 'Si lavano'],
             'Otto pennelli nel bicchiere: dal fondotinta alla sfumatura.', attributi={'marca': ['Pesca']}),
    prodotto('CRO-015', 'Tappetino yoga Lilla', 'tappetino-yoga-lilla', 'sport', 39, 29, 70, 4.7, 960, True,
             ['pexels-4793328.jpg', 'pexels-8899524.jpg', 'pexels-9004302.jpg'], ['Gomma naturale antiscivolo', '6 mm',
             'Cinghia per portarlo', 'Si lava con acqua'], 'Il tappetino che non scivola anche quando si suda.',
             attributi={'marca': ['Asana']}, cross=['CRO-016', 'CRO-017']),
    prodotto('CRO-016', 'Borraccia termica in acciaio', 'borraccia-termica', 'sport', 25, 0, 100, 4.8, 1320, False,
             ['pexels-7879967.jpg', 'pexels-7879796.jpg'], ['Acciaio inossidabile', 'Fredda 24 ore, calda 12', '750 ml',
             'Non perde'], 'L’acqua fredda tutto il giorno, il tè caldo fino a sera.', attributi={'marca': ['Sorgente']}),
    prodotto('CRO-017', 'Manubri 1,5 kg, la coppia', 'manubri-coppia', 'sport', 19, 15, 80, 4.6, 640, False,
             ['pexels-16679756.jpg', 'pexels-16567048.jpg', 'pexels-16567046.jpg'], ['Rivestiti in vinile', 'Esagonali: non '
             'rotolano', 'Due pesi da 1,5 kg', 'Presa morbida'], 'Per l’allenamento a casa: i manubri che non rotolano.',
             attributi={'marca': ['Asana']}),
    prodotto('CRO-018', 'Treno dei numeri, legno', 'treno-numeri-legno', 'bimbi', 34, 0, 40, 4.9, 410, True,
             ['pexels-7269676.jpg', 'pexels-7269674.jpg', 'pexels-7269668.jpg'], ['Legno di faggio', 'Colori all’acqua',
             'Dai 18 mesi', 'Si impara a contare'], 'Il treno coi numeri da infilare: si gioca e si conta.',
             attributi={'marca': ['Trottola']}, cross=['CRO-019', 'CRO-020']),
    prodotto('CRO-019', 'Animali in legno, sei pezzi', 'animali-legno', 'bimbi', 28, 0, 50, 4.8, 260, False,
             ['pexels-3661201.jpg', 'pexels-3662945.jpg'], ['Giraffa, elefante e altri quattro', 'Legno levigato a mano',
             'Dai 12 mesi', 'Sacchetto di cotone'], 'Sei animali di legno levigato, da tenere in mano.',
             attributi={'marca': ['Trottola']}),
    prodotto('CRO-020', 'Carretto delle forme', 'carretto-forme', 'bimbi', 29, 24, 35, 4.7, 188, False,
             ['pexels-35230776.jpg'], ['Dodici forme colorate', 'Le ruote girano', 'Dai 12 mesi', 'Legno certificato'],
             'Il carretto da tirare, con le forme da far passare dai buchi giusti.', attributi={'marca': ['Trottola']}),
    prodotto('CRO-021', 'Cubi colorati, 20 pezzi', 'cubi-colorati', 'bimbi', 18, 0, 60, 4.6, 140, False,
             ['pexels-34817526.jpg'], ['Legno colorato', 'Venti cubi', 'Dai 10 mesi', 'Scatola di cartone'],
             'Venti cubi di legno per le prime torri.', attributi={'marca': ['Trottola']}),
    prodotto('CRO-022', 'Puzzle in legno, 1000 pezzi', 'puzzle-legno', 'tempo-libero', 24, 0, 40, 4.7, 350, False,
             ['pexels-3852577.jpg', 'pexels-24551366.jpg'], ['Pezzi in legno tagliati al laser', 'Forme insolite',
             '50 × 70 cm', 'Il poster dell’immagine'], 'Mille pezzi di legno, ognuno con una forma: la sera di novembre.',
             attributi={'marca': ['Tassello']}),
    prodotto('CRO-023', 'Caffè in grani Crocevia, 1 kg', 'caffe-grani-crocevia', 'gourmet', 22, 18, 80, 4.7, 870, False,
             ['pexels-14545422.jpg', 'pexels-7657839.jpg'], ['Miscela 70% arabica', 'Tostatura media', 'Per macchina e moka',
             'Sacchetto con valvola'], 'La nostra miscela: cioccolato e nocciola, per l’espresso di casa.',
             attributi={'marca': ['Crocevia']}, cross=['CRO-024', 'CRO-008']),
    prodotto('CRO-024', 'Cioccolato fondente 70%, tre tavolette', 'cioccolato-fondente', 'gourmet', 12, 9, 120, 4.8, 1020,
             False, ['pexels-6167340.jpg', 'pexels-6167329.jpg', 'pexels-6167345.jpg'], ['Cacao del Perù', 'Tre tavolette da '
             '100 g', 'Senza lattosio', 'Fatto a Modica'], 'Il fondente al 70%, croccante, di Modica.',
             attributi={'marca': ['Crocevia']}),
    prodotto('CRO-025', 'Tavolette bianco e latte', 'tavolette-bianco-latte', 'gourmet', 7, 0, 90, 4.4, 230, False,
             ['pexels-4113365.jpg'], ['Una bianca, una al latte', '100 g ciascuna', 'Latte delle Alpi', 'Per i più golosi'],
             'Due tavolette, bianca e al latte, per chi non ama il fondente.', attributi={'marca': ['Crocevia']}),
    prodotto('CRO-026', 'Il quaderno delle letture', 'quaderno-letture', 'tempo-libero', 18, 0, 70, 4.6, 190, False,
             ['pexels-32505301.jpg', 'pexels-10554053.jpg'], ['192 pagine a righe', 'Carta avoriata', 'Le schede dei libri '
             'letti', 'Copertina in tela'], 'Per tenere il conto dei libri letti e delle frasi da ricordare.',
             attributi={'marca': ['Tassello']}),
    prodotto('CRO-027', 'Maglietta in cotone organico', 'maglietta-cotone-organico', 'moda', 19, 15, 90, 4.6, 720, True,
             ['pexels-video-9594579.jpg'], ['Cotone organico pesante', 'Taglio dritto', 'Lavabile a 40°', 'Il video '
             'dell’apertura del pacco nella galleria'], 'La maglietta bianca di cotone pesante: guardate nel video come arriva, '
                                                         'piegata nella carta.', video=V_MAGLIA,
             attributi={'marca': ['Crocevia']}),
    prodotto('CRO-028', 'Confezione regalo Crocevia', 'confezione-regalo', 'casa', 4, 0, 500, 4.9, 1280, False,
             ['pexels-video-9594575.jpg'], ['Scatola di cartone riciclato', 'Carta velina e nastro', 'Il biglietto scritto da '
             'voi', 'Il video nella galleria'], 'Aggiungetela al carrello: il regalo parte incartato, col biglietto. Nel video, '
                                                'com’è fatta.', video=V_SCATOLA, attributi={'marca': ['Crocevia']}),
]
RECENSIONI = [[5, 'Ordinato la sera, arrivato la mattina dopo. Imballo perfetto.'],
              [5, 'Prezzo migliore che altrove e il reso è stato facilissimo.'],
              [4, 'Buon prodotto, la confezione era un po’ ammaccata.'],
              [5, 'Comprato in offerta col codice, ottimo affare.'],
              [4, 'Corrisponde alla descrizione, consegna puntuale.'],
              [5, 'Ho trovato tutto in un posto solo: regali per tutta la famiglia.'],
              [5, 'Assistenza in chat veloce e gentile.'],
              [3, 'Va bene, ma mi aspettavo un colore più acceso.']]
NOMI = ['Giulia R.', 'Marco T.', 'Sara B.', 'Luca F.', 'Elena M.', 'Davide C.', 'Chiara P.', 'Paolo G.', 'Francesca L.',
        'Andrea S.', 'Marta V.', 'Riccardo N.']
PROMESSE = [('truck', 'Domani a casa, gratis da 29 €'), ('rotate-ccw', 'Reso gratuito in 30 giorni'),
            ('credit-card', 'In tre rate senza interessi'), ('message-circle', 'Assistenza in chat, 7 giorni su 7')]
CONTATTI = [('message-circle', 'Chat dalle 8 alle 22, tutti i giorni', P['assistenza'] + '#scrivici'),
            ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'crocevia', icona='compass', ricerca='Cerca fra mille cose…',
                     parole='un regalo sotto i 30 €\ndelle cuffie col silenzio\ngiochi di legno\nun tappetino da yoga',
                     annuncio=('Settimana Crocevia: −10% in più su tutto col codice CROCEVIA10.', 'Le offerte', P['offerte']),
                     logo_size=30)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'crocevia', 'compass', 'Il grande magazzino online: otto reparti, mille cose, un carrello. '
                   'Domani a casa, reso in trenta giorni.',
                   [('Reparti', [(n, N.cat_url(s)) for s, n, _, _, _, _ in REPARTI[:5]]),
                    ('Crocevia', [('Tutti i reparti', P['reparti']), ('Offerte', P['offerte']), ('Confronta', P['confronta']),
                                  ('I preferiti', P['preferiti'])]),
                    ('Aiuto', [('Spedizioni', P['assistenza'] + '#spedizioni'), ('Resi e rimborsi', P['assistenza'] + '#spedizioni'),
                               ('Il mio account', P['account']), ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Crocevia s.p.a. · Milano · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('Le offerte di domani', 'Le partenze di domani, stasera.',
                              'Ogni sera alle 21 le offerte del giorno dopo, un’ora prima degli altri.',
                              'Fatto: stasera alle 21 la prima lettera, col codice del 10%.'),
                   social=[('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com'),
                           ('youtube', 'https://youtube.com')])


# ── L'apertura: il tabellone delle partenze ──
# (Dal 1° nov 2026 la home apre con la vetrina delle offerte: questa apertura non è più usata.)
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    livelli = [
        K.livello('cv-velo', tipo='shape', x=0, y=0, w=55, h=100, entra='fadeIn', ritardo=0,
                  shapeGradient={'angle': 90, 'from': mix(C['nero'], 90), 'to': mix(C['nero'], 0), 'fromStop': 45},
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 42}}),
    ]
    # il tabellone
    px, py, pw, ph = 760.0, 92.0, 620.0, 516.0
    pxm, pym, pwm, phm = 24.0, 560.0, 592.0, 800.0
    a = _pc(px, py, pw, ph, W, H)
    b = _pc(pxm, pym, pwm, phm, WM, HM)
    livelli.append(K.livello('cv-tabellone', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['nero'], 94),
                             borderRadius=16, borderWidth=1, borderColor=mix(C['chiaro'], 16),
                             boxShadow={'x': 0, 'y': 30, 'blur': 60, 'spread': -20, 'color': mix(C['nero'], 80)},
                             entra='fadeInUp', ritardo=150, durata=900,
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a = _pc(px + 28, py + 26, 420, 24, W, H)
    b = _pc(pxm + 22, pym + 24, 400, 30, WM, HM)
    livelli.append(K.livello('cv-tit', contenuto='Partenze · le offerte di oggi', x=a[0], y=a[1], w=a[2], size=13, peso='700',
                             colore=ACQUA, fontFamily=MONO, letterSpacing=3, textTransform='uppercase', entra='fadeIn', ritardo=400,
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(11)}}))
    a = _pc(px + pw - 120, py + 22, 92, 30, W, H)
    b = _pc(pxm + pwm - 110, pym + 20, 90, 34, WM, HM)
    livelli.append(K.livello('cv-ora', contenuto='21:40', x=a[0], y=a[1], w=a[2], size=20, peso='700', colore=C['chiaro'],
                             align='right', fontFamily=MONO, entra='fadeIn', ritardo=400,
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(15)}}))
    colonne = [  # nome, x, larghezza (computer) · x, larghezza (telefono)
        ('bin', 28, 66, 22, 70), ('rep', 104, 250, 100, 240), ('sco', 364, 110, 350, 110), ('sta', 484, 112, 468, 104)]
    riga_h, riga_hm = 54.0, 86.0
    for r, (slug, nome, _, _, sconto, stato) in enumerate(REPARTI):
        y = py + 66 + riga_h * r
        ym = pym + 76 + riga_hm * r
        gira = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'rx': -90, 'o': 0}, {'p': 6 + 9 * r, 'rx': -90, 'o': 0},
                                                    {'p': 14 + 9 * r, 'rx': 0, 'o': 100}, {'p': 100, 'rx': 0, 'o': 100}]}
        for nomec, cx, cw, cxm, cwm in colonne:
            a = _pc(px + cx, y, cw, riga_h - 12, W, H)
            b = _pc(pxm + cxm, ym, cwm, riga_hm - 16, WM, HM)
            base = dict(x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['chiaro'], 7), borderRadius=6, padding=0,
                        textValign='center', entra='fadeIn', ritardo=0, scrollMove=gira,
                        responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}})
            if nomec == 'bin':
                L = K.livello('cv-%s%d' % (nomec, r), contenuto='%02d' % (r + 1), size=20, peso='700', colore=mix(C['chiaro'], 70),
                              align='center', fontFamily=MONO, **base)
                L['responsive']['mobile']['fontSize'] = m(16)
            elif nomec == 'rep':
                L = K.livello('cv-%s%d' % (nomec, r), tipo='button', contenuto=nome, size=20, peso='700', colore=C['chiaro'],
                              fontFamily=TITOLI, buttonUrl=N.cat_url(slug), hover={'bgColor': C['acc'], 'duration': 200},
                              **dict(base, padding=9))
                L['responsive']['mobile']['fontSize'] = m(17)
                L['textAlign'] = 'left'
            elif nomec == 'sco':
                L = K.livello('cv-%s%d' % (nomec, r), contenuto=sconto, size=22, peso='700', colore=C['chiaro'], align='center',
                              fontFamily=MONO, **dict(base, bgColor=CORALLO if r % 2 == 0 else C['acc']))
                L['responsive']['mobile']['fontSize'] = m(16)
            else:
                L = K.livello('cv-%s%d' % (nomec, r), contenuto=stato, size=11, peso='700',
                              colore=ACQUA if stato != 'Ultimi posti' else C['sec'], align='center', fontFamily=MONO,
                              letterSpacing=1, textTransform='uppercase', **base)
                L['responsive']['mobile']['fontSize'] = m(9)
            livelli.append(L)
    livelli += [
        K.livello('cv-o', contenuto='Crocevia · il grande magazzino online', x=6, y=25, w=44, size=12, peso='700',
                  colore=mix(C['chiaro'], 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(11), 'y': 5.5, 'x': 7, 'width': 86}}),
        K.livello('cv-t', contenuto='Mille cose,<br>un carrello.', tag='h1', x=6, y=29, w=44, size=88, peso='700',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=0.96, letterSpacing=-2, entra='maskFromBottom', ritardo=300,
                  durata=1200, responsive={'mobile': {'fontSize': m(46), 'y': 8.5, 'x': 7, 'width': 86}}),
        K.livello('cv-s', contenuto='Otto reparti, domani a casa, reso in trenta giorni. Scorrete: partono le offerte di oggi.',
                  x=6, y=53, w=34, size=17, peso='400', colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=700,
                  responsive={'mobile': {'fontSize': m(13), 'y': 22, 'x': 7, 'width': 86}}),
        K.livello('cv-b', tipo='button', contenuto='Le offerte di oggi', x=6, y=65, w='auto', size=15, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=14, ritardo=1000, buttonUrl=P['offerte'],
                  hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 31, 'x': 7, 'fontSize': m(12)}}),
        K.livello('cv-l', tipo='button', contenuto='Tutti i reparti', x=21.2, y=65, w='auto', size=15, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=14, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), ritardo=1150, buttonUrl=P['reparti'],
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 24.5}, 'mobile': {'y': 31, 'x': 54, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('cv-1', K.sfondo_video(V_INCROCIO, overlay=0.1, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2000, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def notiziario(t):
    return t.bare('newsticker', {
        'items': [{'title': s, 'url': u, 'badge': b, 'icon': '', 'timestamp': ''} for s, u, b in [
            ('Sneakers Passo a 49 €: restano il 39 e il 42', N.cat_url('moda'), '−29%'),
            ('Cuffie Eco col silenzio, a 99 €', N.cat_url('tech'), '−23%'),
            ('Il tappetino yoga Lilla a 29 €', N.cat_url('sport'), '−26%'),
            ('Profumi e trucchi: 3 × 2 fino a domenica', N.cat_url('bellezza'), '3×2'),
            ('Codice CROCEVIA10: un altro 10% su tutto', P['offerte'], 'codice')]],
        'direction': 'horizontal', 'show_label': True, 'label_text': 'Partenze', 'label_icon': 'zap', 'animation_type': 'marquee',
        'speed': 40, 'auto_scroll': True, 'pause_on_hover': True, 'loop': True, 'marquee_direction': 'left', 'show_controls': False,
        'preset': 'custom', 'label_shape': 'arrow', 'label_bg': C['acc'], 'label_color': C['on_acc'], 'label_position': 'left',
        'badge_bg': CORALLO, 'badge_color': C['chiaro'], 'bg_color': C['nero'], 'text_color': C['chiaro'], 'height': 52,
        'separator': '·', 'item_hover_effect': 'underline'})


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
# Dal 1° nov 2026 il negozio apre come quelli grandi: una fascia bassa di offerte che scorre da sola invece
# dell'apertura a tutto schermo, e il mega menu racconta le offerte e le novità nei suoi pannelli.
LARGO = 0.5  # la larghezza media di una lettera dei titoli (per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO)
    return N.vetrina(t, [
        N.offerta('cv-o1', K.sfondo_video(V_NASTRINO, overlay=0.12), 'Settimana Crocevia · fino a domenica',
                  'Un altro 10%, su tutto.', 'Anche sulle offerte già scontate: il codice si scrive nel carrello.',
                  'Fai la spesa', P['offerte'], bollino='−10%', nota='Codice CROCEVIA10', primo=True, **f),
        N.offerta('cv-o2', K.sfondo_foto('pexels-11324548.jpg', overlay=0.12, scala=1.06), 'Moda · offerta lampo',
                  'Sneakers Passo a 49 €.', 'Erano 69 €: restano il 39 e il 42. Domani a casa, gratis.', 'Il reparto Moda',
                  N.cat_url('moda'), bollino='−29%', nota='Finché ci sono i numeri', lato='destra', **f),
        N.offerta('cv-o3', K.sfondo_foto('pexels-7241444.jpg', overlay=0.12, scala=1.06), 'Tech · il silenzio costa meno',
                  'Le cuffie Eco a 99 €.', 'Cancellano il rumore, trenta ore di musica. Erano 129 €.', 'Il reparto Tech',
                  N.cat_url('tech'), bollino='−23%', bollino_x=82, nota='Anche in tre rate senza interessi', **f),
        N.offerta('cv-o4', K.sfondo_foto('pexels-4793328.jpg', overlay=0.12, scala=1.06), 'Sport · per cominciare bene',
                  'Il tappetino Lilla a 29 €.', 'Antiscivolo, si arrotola e si porta ovunque. Era 39 €.', 'Il reparto Sport',
                  N.cat_url('sport'), bollino='−26%', nota='Reso gratuito in 30 giorni', **f),
    ], transizione='flipV')


# le schede sotto la vetrina: «tempo» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='tempo', voci=[
        ('Le offerte di oggi', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Appena arrivati', {'orderby': 'date'}, 'Vedi tutte', P['negozio']),
        ('Sotto i 15 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=15'),
        ('I più votati', {'orderby': 'rating'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Gli otto reparti', 'Da dove partiamo?', ('Bimbi,', 'da 18 €.', 'I giochi di legno per Natale.', 'Il reparto', N.cat_url('bimbi'), 'pexels-35230776.jpg'), colonne=4)


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte lampo', 'Partono a mezzanotte.', 'Quelle di oggi finiscono tra:',
                              ('Un altro 10%,', 'su tutto.', 'Codice CROCEVIA10, fino a domenica.', 'Le offerte', P['offerte'], 'pexels-5868269.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Arrivati questa settimana',
                             [(n, N.cat_url(s)) for s, n in (('moda', 'Moda'), ('casa', 'Casa'), ('tech', 'Tech'), ('bellezza', 'Bellezza'), ('sport', 'Sport'), ('gourmet', 'Gourmet'))],
                             ('La cassa Rombo,', '39 €.', 'Bluetooth, resiste alla pioggia. Era 49 €.', 'Il reparto Tech', N.cat_url('tech'), 'pexels-34241799.jpg'))


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([notiziario(t)], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        N.testata_sezione(t, 'Gli otto reparti', 'Da dove partiamo?', P['reparti'], 'Tutti i reparti'),
        t.one([N.categorie(t, colonne=8, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte lampo', color=mix(C['chiaro'], 80)),
                      N.titolo(t, 'Partono a mezzanotte.', size=44, color=C['chiaro']),
                      N.testo(t, 'Ogni giorno nuove offerte, finché ci sono i pezzi.', color=mix(C['chiaro'], 82))],
                     wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left', pad_top=16)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.anteprima(t), N.prodotti(t, on_sale=True, posts_per_page=10, columns=5, columns_tablet=3, layout='carousel',
                                          orderby='popularity')]),
    ], bg=solid(C['nero']), padding='default', width='large', html_id='offerte', style={'padding_top': 56, 'padding_bottom': 60}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Bellezza,', '−25%.', 'L’eau de parfum Vetro a 59 €.', 'Il reparto',
                               N.cat_url('bellezza'), K.photo_bg('pexels-15574229.jpg'), size=34)], wm='1-3', ws='1-1'),
               t.col([N.banner(t, 'Gourmet,', '−25%.', 'Il fondente 70%, tre tavolette a 9 €.', 'Il reparto',
                               N.cat_url('gourmet'), K.photo_bg('pexels-4113365.jpg'), size=34)], wm='1-3', ws='1-1'),
               t.col([N.banner(t, 'Bimbi,', 'da 18 €.', 'Giochi di legno per Natale.', 'Il reparto', N.cat_url('bimbi'),
                               K.photo_bg('pexels-7269674.jpg'), size=34)], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=18, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(t.section([
        N.testata_sezione(t, 'I più venduti', 'Quelli che mettono tutti nel carrello', P['negozio']),
        t.one([N.prodotti(t, orderby='popularity', posts_per_page=10, columns=5, columns_tablet=3, show_compare=True)]),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 64}))
    out.append(t.section([
        N.testata_sezione(t, 'Scelti per voi', 'Il meglio di ogni reparto', P['negozio']),
        t.one([N.prodotti(t, featured=True, posts_per_page=10, columns=5, columns_tablet=3, layout='carousel', orderby='rating')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(t.section([
        t.one([K.numeri(t, [('24 h', 'dal clic al portone'), ('8', 'reparti'), ('30 giorni', 'per il reso'),
                            ('4,7/5', 'il voto dei clienti')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-5868269.jpg', 'Una ragazza sorride con le borse della spesa', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Il reso'), N.titolo(t, 'Non va bene?<br>Passa il corriere.', size=52),
                      N.testo(t, 'Trenta giorni per ripensarci: scegliete il giorno, il corriere ritira e il rimborso parte appena '
                                 'riceviamo il pacco. Senza spese, senza domande.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Reso gratuito in trenta giorni', 'Rimborso in tre giorni', 'Cambio taglia senza spese']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'Come funziona', P['assistenza'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    return out


def build_reparti():
    t = Tpl(ID, 'reparti')
    out = [K.intro(t, 'I reparti', ['Otto reparti,', 'un carrello solo.'], 'Dalla moda al cioccolato: si mette tutto nello stesso '
                   'carrello e arriva tutto insieme, domani.', bg=K.video_bg(V_CITTA, overlay=25), size=92)]
    for i, (slug, nome, descr, foto, sconto, stato) in enumerate(REPARTI):
        out.append(t.section([
            t.row([t.col([N.occhiello(t, 'Reparto %02d · %s' % (i + 1, sconto)), N.titolo(t, nome, size=44),
                          N.testo(t, descr, size=16), N.link(t, 'Tutto il reparto', N.cat_url(slug), align='left')],
                         wm='1-4', ws='1-1'),
                   t.col([N.prodotti(t, category=slug, posts_per_page=4, columns=4, columns_tablet=2, orderby='popularity',
                                     show_rating=False)], wm='3-4', ws='1-1')],
                  layout='custom', custom='25,75', gap=40, valign='start'),
        ], bg=solid(C['sala']) if i % 2 == 0 else None, padding='default', width='large',
            html_id=slug, style={'padding_top': 64, 'padding_bottom': 64}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il negozio', 'Tutti i prodotti', 'Filtrate per reparto, marca e prezzo; «Confronta» li mette '
                            'fianco a fianco.', attributi='pa_marca,pa_numero', colonne=3,
                            banner=N.banner(t, 'Codice CROCEVIA10,', 'un altro 10%.', 'Anche sulle offerte, fino a domenica.',
                                            'Le offerte', P['offerte'], K.photo_bg('pexels-5868240.jpg', position='center 30%'),
                                            size=36))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Il tappetino yoga,', '29 €.', 'Gomma naturale, non scivola.', 'Lo sport', N.cat_url('sport'),
                        K.photo_bg('pexels-9004302.jpg'), size=34)], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Profumi e trucchi,', '3 × 2.', 'Il terzo lo paghiamo noi.', 'La bellezza', N.cat_url('bellezza'),
                        K.photo_bg('pexels-7256120.jpg'), size=34)], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Settimana Crocevia', 'Le partenze di oggi', 'Cambiano ogni giorno a mezzanotte. Col codice '
                            'CROCEVIA10 un altro 10% su tutto, anche qui.', extra=extra)


def build_confronta():
    t = Tpl(ID, 'confronta')
    out = [N.intestazione(t, 'Confronta', 'Fianco a fianco', 'I prodotti messi a confronto dal negozio, riga per riga: prezzo, '
                          'voto, disponibilità e caratteristiche.')]
    out.append(t.section([
        t.one([t.bare('woo_comparison', {'max_products': 4, 'show_image': True, 'show_price': True, 'show_rating': True,
                                         'show_stock': True, 'show_sku': False, 'show_description': True, 'show_attributes': True,
                                         'show_add_to_cart': True, 'header_bg': C['nero'], 'header_color': C['chiaro'],
                                         'border_color': C['filo'], 'accent_color': C['acc'], 'btn_bg': C['acc'],
                                         'btn_color': C['on_acc'], 'empty_text': 'Ancora niente: nel negozio premete «Confronta» '
                                                                                  'sotto due o tre prodotti.'})]),
        t.one([N.pulsante(t, 'Torna al negozio', P['negozio'], pad_top=24)]),
    ], padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 64}))
    return out


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Domani a casa, gratis da 29 €'),
                                                   ('rotate-ccw', 'Reso gratuito in 30 giorni'),
                                                   ('credit-card', 'In tre rate senza interessi'),
                                                   ('tag', 'Codice CROCEVIA10: −10% in più')],
                             bundle='Spesso comprati insieme', miniature='left', altezza='560px', bottone='Aggiungi al carrello',
                             fondo_galleria=C['chiaro'])


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Domani a casa, gratis da 29 €. Il codice CROCEVIA10 va nel riquadro del '
                                                  'buono.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna, il pagamento: anche in tre rate senza interessi.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, i resi, gli indirizzi e i buoni.',
                            ('Un ordine in arrivo?', 'Lo segui dall’assistenza col numero dell’ordine e la tua email.',
                             'Traccia l’ordine', P['assistenza'] + '#spedizioni'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto dei prodotti.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'In chat e al telefono dalle 8 alle 22, tutti i giorni.', [
        ('truck', 'Spedizioni', 'Domani a casa, gratis da 29 €.', 'spedizioni'),
        ('rotate-ccw', 'Resi', 'Trenta giorni, gratis: passa il corriere.', 'spedizioni'),
        ('credit-card', 'Pagamenti', 'Carte, portafogli digitali, tre rate.', 'spedizioni'),
        ('tag', 'Buoni e codici', 'Il codice va nel riquadro del carrello.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 29 €; sotto, 3,90 €. Ordinate entro le 20 e arriva domani.'),
        ('Come faccio un reso?', 'Dal link nella mail dell’ordine: scegliete il giorno, il corriere ritira, il rimborso parte '
                                 'appena riceviamo il pacco.'),
        ('Posso pagare a rate?', 'Sì, in tre rate senza interessi sopra i 99 €.'),
        ('Il codice vale anche sulle offerte?', 'Sì: CROCEVIA10 vale su tutto, fino a domenica.')], CONTATTI,
        campi_extra='Un buono o un codice\n', foto=('pexels-4127636.jpg', 'Una ragazza cammina con le borse della spesa'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questo treno <em>è già partito.</em>', 'La pagina non c’è più. Cercate quello che vi '
                        'serve qui sopra: c’è quasi tutto.', K.video_bg(V_INCROCIO, overlay=40))


THEME = tema(
    ID, NAME, 'Negozi online', 'Le offerte del giorno',
    ['multisettore', 'grande magazzino', 'marketplace', 'woocommerce', 'e-commerce', 'offerte', 'reparti', 'slider pro'],
    'Un grande magazzino online multisettore, vivace e chiaro con la testata blu notte: in apertura la fascia delle offerte del '
    'giorno che scorre e le quattro schede coi prodotti, il mega menu coi pannelli dei reparti, delle offerte e delle '
    'novità; il notiziario delle offerte, gli otto reparti, le offerte lampo col conto alla rovescia, il codice della settimana, il '
    'confronto, la scheda prodotto coi numeri delle scarpe.',
    [('Tutti i reparti', P['reparti']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Moda', '{{PAGINA:negozio}}?product_cat=moda'),
     ('Casa', '{{PAGINA:negozio}}?product_cat=casa'), ('Tech', '{{PAGINA:negozio}}?product_cat=tech'),
     ('Bimbi', '{{PAGINA:negozio}}?product_cat=bimbi'), ('Confronta', P['confronta'])],
    [('home', 'Home', None), ('negozio', 'Il negozio', 'negozio'), ('reparti', 'I reparti', 'reparti'),
     ('offerte', 'Settimana Crocevia', 'offerte'), ('confronta', 'Confronta', 'confronta'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Inter\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bricolage Grotesque\', \'Helvetica Neue\', sans-serif',
    '\'Space Mono\', ui-monospace, Menlo, monospace', ['Bricolage Grotesque', 'Inter', 'Space Mono'], corsivo_di=[],
    peso_titoli='700', raggio=14, grana=0, titoli_line='0.98', titoli_spazio='-0.02em')
tema_negozio(THEME, NAME)
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # anteprime con la vetrina delle offerte (1° nov 2026)
pannelli(THEME, NAME, {'Tutti i reparti': ('pannello-reparti', 'Pannello dei reparti'), 'Offerte': ('pannello-offerte', 'Pannello delle offerte'), 'Novità': ('pannello-novita', 'Pannello delle novità')})
next(v for v in THEME['menu']['items'] if v['title'] == 'Offerte')['classi'] = ['badge-oggi']
THEME['cursor']['style'] = 'cartellino'
THEME['cursor']['label_text'] = '−30%'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'reparti.json': build_reparti(), 'offerte.json': build_offerte(),
                        'confronta.json': build_confronta(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'marca': {'nome': 'Marca', 'termini': []}, 'numero': {'nome': 'Numero', 'termini': NUMERI}},
                    dentro='Il prodotto, lo scontrino e l’etichetta per il reso, già pronta.')


if __name__ == '__main__':
    main()
