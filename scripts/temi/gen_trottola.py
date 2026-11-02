# -*- coding: utf-8 -*-
"""Genera il tema «Trottola» (id trottola): giocattoli in via Sparano, a Bari — giochi di legno, costruzioni, peluche e
bambole di pezza, giochi da tavolo e puzzle, aquiloni; le liste di nascita e dei compleanni, l'incarto con la carta a pois.

Tema chiaro e allegro ma educato: il blu dei cubi sui pulsanti, il giallo sole sui bollini, il corallo e il verde negli
accenti, la carta color legno chiaro della pagina. Titoli in Fredoka (tondi, da cubo), testo in Nunito, prezzi e età in
Azeret Mono. Angoli tondi.

Apertura: la vetrina delle offerte, bassa, che gira come un cubo (cubeV): il trenino di legno, l'arcobaleno da impilare, la
scatola delle serate; sotto le quattro schede coi prodotti veri; il mega menu apre il negozio, le offerte e le novità. A metà
pagina «le costruzioni»: lo slider resta fermo mentre si scorre e i cubi cadono uno alla volta sul pavimento di legno, con
un rimbalzo, finché si legge TROTTOLA; in alto gira la trottola vista dall'alto, alla fine arriva rotolando la palla. Sul
telefono le righe sono due, e prima cade quella di sotto. Cursore: la trottola, che gira più svelta quanto più si va.

WooCommerce: il negozio coi filtri, la scheda con la galleria (il video del gioco) e l'incarto a scelta, il carrello, la
cassa a passi, l'account, i preferiti; i giochi per età, le liste di nascita e dei compleanni con la richiesta.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'trottola'
NAME = 'Trottola'

HEX = {
    'primary': '#2B4FC2', 'primary_contrast': '#FFFFFF', 'secondary': '#FFC53D', 'secondary_contrast': '#1D1A14',
    'accent': '#C93D24', 'dark': '#1D1A14', 'light': '#FFFFFF', 'muted': '#F6EBDD', 'muted_contrast': '#1D1A14',
    'text': '#1D1A14', 'text_muted': '#5E574D', 'background': '#FFFAF2', 'border': '#EADCC8', 'link': '#2B4FC2',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'eta', 'liste', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'giochi@trottola.example', '+39 080 000 1212', 'tel:+390800001212'

V_TROTTOLA, V_GIRA, V_CASTELLO, V_TRENO, V_PAPERA, V_BARCA, V_ARCHI, V_ARCOBALENO, V_TORRE, V_TAPPETO, V_SORELLE, V_TORRETTA = (
    'pexels-video-7269978', 'pexels-video-7269975', 'pexels-video-7269976', 'pexels-video-10325105', 'pexels-video-3676982',
    'pexels-video-3677020', 'pexels-video-7270074', 'pexels-video-10555500', 'pexels-video-7269980', 'pexels-video-9873255',
    'pexels-video-8206343', 'pexels-video-3677022')
VIDEOS = [V_TROTTOLA, V_GIRA, V_CASTELLO, V_TRENO, V_PAPERA, V_BARCA, V_ARCHI, V_ARCOBALENO, V_TORRE, V_TAPPETO, V_SORELLE,
          V_TORRETTA]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    '7269975-a', '7269978-a', 7269694, 7269572, 7269676, 7269674, 3661201, 3661280, 3662945, 7269668, 7296783, 19734990,
    15250000, 4887210, 8612915, 8612924, 35572805, 3622561, 3622563, 34402762, 35514507, 5786757, 5786772, 4585556, 7436131,
    35172085, 836040, 207891, 9227506, 9227488, 9227507, 7269503, 9227499, 8385830, 8386176, 7269457, 9227510, 7117502,
    12955654, 12940817, 5435598, 6990329, 34817526, 35230776, 6693308, 34817532, 33043840, 26955420, 18864019, 18864022,
    15054343, 31501520, 30389636, 6158202, 13725133, 6333907, 11368584, 6333894, 10388909, 6333933, 4792369, 8120321, 8120340,
    8065098, 31358776, 17056455, 929362, 28729081, 36985957, 18343866, 32646283, 36985951, 5433851, 15255716, 6865340, 4887202,
    8364071, 19342203, 2767818, 11911812, 8922399, 4966470, 6437458, 14025659, 32228855, 7269539, 8364072, 6274960, 8654673,
    7296764, 7296780, 7296459, 12955537, 7269580, 3662662, 3662939, 7296767, 31485519, 30109293, 40339173, 39563778,
    36303741, 30109291, 691125, 35345175)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=20, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
GIALLO, CORALLO = C['sec'], C['acc2']
VERDE, ROSA, LEGNO, LEGNO_SCURO = '#2E8B5E', '#E07BA5', '#E9D3B5', '#D4B68E'
TITOLI, MONO = 'Fredoka', 'Azeret Mono'
N = Negozio(K, NAME, P, raggio=20, ratio='1-1', card='border', hover='swap', testata_scura=False,
            consegna='Incarto a pois gratis, spedizione in 24 ore', evidenza='Il più giocato', marca='', peso_titoli='600')

CATEGORIE = [
    ('primi-anni', '0–3 anni', 'Da impilare, da toccare, da mordere.', 'pexels-7269674.jpg'),
    ('legno', 'Giochi di legno', 'Trenini, trottole, animali, macchinine.', 'pexels-15250000.jpg'),
    ('costruzioni', 'Costruzioni', 'Blocchi naturali e cubi dell’alfabeto.', 'pexels-7269694.jpg'),
    ('peluche', 'Peluche e bambole', 'Orsi, bambole di pezza, folletti.', 'pexels-5786757.jpg'),
    ('tavolo', 'Giochi da tavolo e puzzle', 'Per le sere di pioggia.', 'pexels-6333907.jpg'),
    ('aria-aperta', 'All’aria aperta', 'Aquiloni per il lungomare.', 'pexels-32646283.jpg'),
]
INCARTI = ['Senza incarto', 'Carta a pois', 'Carta a righe']


def gioco(sku, nome, slug, cat, prezzo, sconto, voto, rec, evid, foto, punti, testo, incarto=True, **kw):
    if incarto:
        kw['varianti'] = ('incarto', INCARTI, [])
    return prodotto(sku, nome, slug, cat, prezzo, sconto, 25, voto, rec, evid, ['pexels-%s.jpg' % f for f in foto], punti, testo,
                    **kw)


PRODOTTI = [
    gioco('TRO-001', 'Trottola di legno tornita', 'trottola-legno', 'legno', 12, 0, 4.9, 512, True, ['7269975-a', '7269978-a'],
          ['Faggio tornito a mano', 'Righe incise', 'Gira più di un minuto', 'Dai 3 anni'],
          'La trottola che ci ha dato il nome: faggio tornito, le righe incise, gira più di un minuto.', video=V_GIRA,
          cross=['TRO-002', 'TRO-020']),
    gioco('TRO-002', 'Trenino di legno, quaranta pezzi', 'trenino-quaranta', 'legno', 49, 39, 4.9, 388, True, [15250000, 8612915],
          ['Binari, ponte e stazione', 'Tre vagoni magnetici', 'Faggio e colori all’acqua', 'Dai 2 anni'],
          'Quaranta pezzi: i binari, il ponte, la stazione e la locomotiva coi tre vagoni a calamita.', video=V_TRENO),
    gioco('TRO-003', 'Locomotiva con tre vagoni', 'locomotiva-vagoni', 'legno', 24, 0, 4.7, 176, False, [4887210, 3622563],
          ['Quattro pezzi', 'Ganci a calamita', 'Va sui binari del trenino', 'Dai 2 anni'],
          'La locomotiva gialla e i tre vagoni, coi ganci a calamita: vanno sui binari del trenino.'),
    gioco('TRO-004', 'Abaco dei numeri da impilare', 'abaco-numeri', 'primi-anni', 29, 0, 4.8, 203, False, [7269674, 7269676],
          ['Numeri da 1 a 5', 'Quindici anelli', 'Legno di faggio', 'Dai 18 mesi'],
          'Il carretto dei numeri: si infilano gli anelli, uno sull’uno e cinque sul cinque.'),
    gioco('TRO-005', 'Animali della savana, in legno', 'animali-savana', 'legno', 34, 0, 4.7, 121, False, [3661201, 3661280],
          ['Giraffa, elefante, zebra, leone', 'Legno d’acero', 'Stanno in piedi', 'Dai 2 anni'],
          'Quattro animali d’acero che stanno in piedi da soli: la giraffa arriva al davanzale.', incarto=False),
    gioco('TRO-006', 'Costruzioni naturali, sessanta blocchi', 'costruzioni-sessanta', 'costruzioni', 45, 0, 4.9, 344, True,
          [7269694, 7296783], ['Sessanta blocchi', 'Archi, cubi, colonne', 'Faggio grezzo, levigato', 'Dai 2 anni'],
          'Sessanta blocchi di faggio grezzo: archi, cubi, colonne, per il castello del sabato.', video=V_CASTELLO),
    gioco('TRO-007', 'Cubi dell’alfabeto, trenta', 'cubi-alfabeto', 'costruzioni', 32, 26, 4.8, 266, True, [12955654, 12940817],
          ['Trenta cubi', 'Lettere, numeri e animali', 'Colori all’acqua', 'Dai 18 mesi'],
          'I cubi coi bordi in rilievo: lettere, numeri e animali, per scrivere il proprio nome.'),
    gioco('TRO-008', 'Arcobaleno da impilare', 'arcobaleno-impilare', 'primi-anni', 39, 0, 4.9, 198, True, [34817532, 34817526],
          ['Sette archi', 'Colori pastello all’acqua', 'Si impila e si dondola', 'Dai 12 mesi'],
          'Sette archi di legno tinto: si impila, si dondola, diventa ponte, galleria, culla.', video=V_ARCOBALENO),
    gioco('TRO-009', 'Scatola delle forme', 'scatola-forme', 'primi-anni', 28, 0, 4.6, 142, False, [35230776, 6693308],
          ['Dodici forme', 'Il coperchio a buchi', 'Le ruote per tirarla', 'Dai 12 mesi'],
          'La scatola coi buchi e le ruote: ogni forma ha il suo, e poi si tira per casa.'),
    gioco('TRO-010', 'Orsetto di peluche, 30 cm', 'orsetto-30', 'peluche', 29, 0, 4.8, 455, False, [5786757, 207891],
          ['Peluche morbido', 'Occhi ricamati', 'Lavabile a 30°', 'Dalla nascita'],
          'L’orsetto con gli occhi ricamati, senza bottoni: va bene dalla culla.'),
    gioco('TRO-011', 'Orso grande, 80 cm', 'orso-80', 'peluche', 79, 65, 4.9, 233, True, [836040, 35172085],
          ['Ottanta centimetri', 'Fiocco a quadri', 'Imbottitura anallergica', 'Arriva in una scatola enorme'],
          'L’orso che si abbraccia con tutte e due le braccia, col fiocco a quadri.', incarto=False),
    gioco('TRO-012', 'Bambola di pezza col cappello rosso', 'bambola-cappello-rosso', 'peluche', 34, 0, 4.7, 188, False,
          [6158202, 15054343], ['Cotone e lana', 'Cucita a mano', 'Cappello a maglia', 'Dalla nascita'],
          'La bambola di pezza col cappello di lana rosso, cucita a mano in Puglia.'),
    gioco('TRO-013', 'Bambola di stoffa, la folletta', 'bambola-folletta', 'peluche', 29, 0, 4.6, 97, False, [31501520, 30389636],
          ['Feltro e cotone', 'Cappello a punta', 'Venti centimetri', 'Dai 3 anni'],
          'La folletta di feltro col cappello a punta: sta nella tasca del cappotto.'),
    gioco('TRO-014', 'Puzzle dello spazio, cento pezzi', 'puzzle-spazio', 'tavolo', 19, 0, 4.8, 221, False, [7269457, 7269503],
          ['Cento pezzi grandi', 'Razzi e pianeti', 'Cartone riciclato', 'Dai 5 anni'],
          'Cento pezzi grandi coi razzi, i pianeti e un astronauta che saluta.'),
    gioco('TRO-015', 'Puzzle colorato, ventiquattro pezzi', 'puzzle-ventiquattro', 'tavolo', 16, 0, 4.6, 133, False,
          [9227506, 9227510], ['Ventiquattro pezzi', 'Legno sottile', 'Colori pieni', 'Dai 3 anni'],
          'Il primo puzzle: ventiquattro pezzi di legno dai colori pieni.'),
    gioco('TRO-016', 'Scacchi in legno da viaggio', 'scacchi-viaggio', 'tavolo', 39, 0, 4.8, 87, False, [10388909, 6333933],
          ['Scacchiera pieghevole', 'Pezzi in bosso', 'Si chiude come una scatola', 'Dai 6 anni'],
          'La scacchiera che si chiude come una scatola coi pezzi dentro: per il treno.'),
    gioco('TRO-017', 'La scatola delle serate', 'scatola-serate', 'tavolo', 29, 22, 4.9, 302, True, [6333907, 6333894],
          ['Cinque giochi in una scatola', 'Carte, dadi e pedine', 'Da 2 a 6 giocatori', 'Dai 6 anni'],
          'Carte, dadi e pedine per cinque giochi di famiglia: per le sere di pioggia.'),
    gioco('TRO-018', 'Aquilone a delta, arcobaleno', 'aquilone-delta', 'aria-aperta', 25, 0, 4.7, 164, False, [32646283, 18343866],
          ['Nylon antistrappo', 'Vola con poco vento', 'Trenta metri di filo', 'Dai 5 anni'],
          'Il delta arcobaleno che vola anche col vento leggero del lungomare.', incarto=False),
    gioco('TRO-019', 'Aquilone a pesce, tre metri', 'aquilone-pesce', 'aria-aperta', 32, 0, 4.6, 76, False, [31358776, 36985957],
          ['Tre metri di coda', 'Nylon colorato', 'Cinquanta metri di filo', 'Dai 7 anni'],
          'Il pesce con tre metri di coda che si muove come nell’acqua.', incarto=False),
    gioco('TRO-020', 'Macchinine di legno, quattro', 'macchinine-quattro', 'legno', 22, 0, 4.7, 245, False, [6865340, 5433851],
          ['Quattro macchinine', 'Ruote che girano davvero', 'Colori all’acqua', 'Dai 18 mesi'],
          'Quattro macchinine tonde, con le ruote che girano davvero: per la pista sul tappeto.'),
    gioco('TRO-021', 'Libro degli animali, da toccare', 'libro-animali', 'primi-anni', 18, 0, 4.8, 154, False, [6437458, 7269539],
          ['Pagine spesse', 'Da toccare e sfogliare', 'Dieci animali', 'Dai 12 mesi'],
          'Dieci animali da toccare, sulle pagine spesse che resistono alle mani piccole.'),
]
RECENSIONI = [[5, 'Il trenino è bellissimo e arrivato incartato a pois.'],
              [5, 'La trottola gira davvero più di un minuto: mio figlio la cronometra.'],
              [4, 'Ottimo, ma la scatola dell’orso grande non passava dalla porta.'],
              [5, 'L’arcobaleno è il gioco più usato della casa.'],
              [5, 'I cubi dell’alfabeto: ha scritto il suo nome da solo.'],
              [4, 'Bello, i colori dal vivo sono più tenui.'],
              [5, 'La lista di nascita ci ha salvato da tre orsetti uguali.'],
              [3, 'Carino, ma l’aquilone col vento forte si gira.']]
NOMI = ['Marco D.', 'Giulia S.', 'Lorenzo P.', 'Francesca R.', 'Tommaso B.', 'Beatrice C.', 'Edoardo F.', 'Aurora M.',
        'Leonardo T.', 'Sofia G.', 'Mattia V.', 'Greta L.']
PROMESSE = [('truck', 'Spedizione gratuita da 39 €'), ('gift', 'Incarto a pois, gratis'),
            ('shield-check', 'Colori all’acqua, legno certificato'), ('rotate-ccw', 'Resi in 30 giorni')]
CONTATTI = [('map-pin', 'Trottola · via Sparano 112, Bari', P['liste'] + '#dove'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'TROTTOLA', icona='blocks', ricerca='Cerca un trenino, un orsetto, un puzzle…',
                     parole='un trenino di legno\nl’orso grande\nun puzzle da cento pezzi\nla trottola',
                     annuncio=('Il trenino di legno a 39 €, incartato a pois, fino a domenica.', 'Le offerte', P['offerte']),
                     logo_size=28)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'TROTTOLA', 'blocks', 'Giocattoli di legno, costruzioni, peluche e giochi da tavolo in via '
                   'Sparano, a Bari: si prova tutto sul tappeto del negozio, si incarta a pois, si fanno le liste di nascita.',
                   [('Il negozio', [('0–3 anni', N.cat_url('primi-anni')), ('Giochi di legno', N.cat_url('legno')),
                                    ('Costruzioni', N.cat_url('costruzioni')), ('Offerte', P['offerte'])]),
                    ('Trottola', [('Per età', P['eta']), ('Liste e compleanni', P['liste']), ('I preferiti', P['preferiti']),
                                  ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni', P['assistenza'] + '#spedizioni'), ('La sicurezza', P['eta'] + '#sicurezza'),
                               ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Trottola s.r.l. · Bari · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera di Trottola', 'I giochi nuovi, una volta al mese.',
                              'I giochi arrivati, le idee per i compleanni, un gioco da fare senza comprare niente.',
                              'Fatto: la prima lettera arriva con un gioco da stampare.'),
                   social=[('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.56  # la larghezza media di una lettera dei titoli (Fredoka, per la vetrina)


def vetrina(t):
    # largo: la larghezza media delle lettere di ogni titolo, misurata col carattere vero (spaziatura compresa)
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-10)
    return N.vetrina(t, [
        N.offerta('to-o1', K.sfondo_video(V_TRENO, overlay=0.12), 'Fino a domenica', 'Il trenino di legno a 39 €.',
                  'Quaranta pezzi: i binari, il ponte, la stazione e tre vagoni a calamita. Era 49 €.', 'Giochi di legno',
                  N.cat_url('legno'), bollino='−20%', nota='Incarto a pois, gratis', primo=True, **dict(f, largo=0.389)),
        N.offerta('to-o2', K.sfondo_video(V_ARCOBALENO, overlay=0.12), '0–3 anni · novità', 'L’arcobaleno da impilare.',
                  'Sette archi di legno tinto coi colori all’acqua: si impila, si dondola, si fa ponte. 39 €.', '0–3 anni',
                  N.cat_url('primi-anni'), bollino='Nuovo', nota='Dai 12 mesi', **dict(f, largo=0.433)),
        N.offerta('to-o3', K.sfondo_foto('pexels-8120321.jpg', overlay=0.12, scala=1.06), 'Per le sere di pioggia',
                  'La scatola delle serate a 22 €.', 'Carte, dadi e pedine per cinque giochi di famiglia. Era 29 €.',
                  'Giochi da tavolo', N.cat_url('tavolo'), bollino='−24%', bollino_x=82, nota='Da 2 a 6 giocatori', **dict(f, largo=0.418)),
    ], transizione='cubeV')


# le schede sotto la vetrina: «griglia» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='griglia', voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('0–3 anni', {'category': 'primi-anni', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('primi-anni')),
        ('Sotto i 25 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=25'),
        ('I più giocati', {'orderby': 'popularity'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Il negozio', 'Dalla culla al lungomare',
                              ('La trottola,', '12 €.', 'Gira più di un minuto.', 'Giochi di legno', N.cat_url('legno'),
                               'pexels-7269975-a.jpg'), tutto='Tutto il negozio')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte della settimana', 'Fino a domenica.',
                              'Le offerte della settimana finiscono tra:',
                              ('Il trenino,', '39 €.', 'Quaranta pezzi. Era 49 €.', 'Giochi di legno', N.cat_url('legno'),
                               'pexels-15250000.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Arrivati questo mese',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('L’arcobaleno', 'da impilare.', 'Sette archi di legno, 39 €.', '0–3 anni', N.cat_url('primi-anni'),
                              'pexels-34817532.jpg'), tutte='Tutto il negozio')


# ── Le costruzioni: lo slider resta fermo e i cubi cadono uno alla volta finché si legge TROTTOLA ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1100.0
SM = 1440.0 / 640.0   # sul telefono gli spostamenti valgono 640/1440 di quelli scritti: questo li riporta alle misure vere
LETTERE = 'TROTTOLA'
COLORI = [C['acc'], GIALLO, CORALLO, VERDE, ROSA, C['acc'], GIALLO, CORALLO]
SCURE = {1, 4, 6}     # le lettere scure sui cubi chiari (giallo, rosa)


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def _cade(p, su, rot, rimbalzo):
    return {'inizio': 0, 'fine': 100, 'tappe': [
        {'p': 0, 'y': su, 'r': rot, 'o': 0}, {'p': p, 'y': su, 'r': rot, 'o': 0}, {'p': p + 1, 'y': su * 0.9, 'r': rot, 'o': 100},
        {'p': p + 6, 'y': 0, 'r': 0, 'o': 100}, {'p': p + 7.5, 'y': rimbalzo, 'r': -rot / 8, 'o': 100},
        {'p': p + 9, 'y': 0, 'r': 0, 'o': 100}, {'p': 100, 'y': 0, 'r': 0, 'o': 100}]}


def costruzioni(t):
    m = K.mob
    L = []
    # il pavimento di legno, col bordo dell'asse
    L.append(K.livello('co-pav', tipo='shape', x=0, y=79, w=100, h=21, bgColor=LEGNO, borderRadius=0, entra='fadeIn', ritardo=0,
                       responsive={'mobile': {'y': 69, 'height': 31}}))
    L.append(K.livello('co-asse', tipo='shape', x=0, y=79, w=100, h=0.6, bgColor=LEGNO_SCURO, borderRadius=0, entra='fadeIn',
                       ritardo=0, responsive={'mobile': {'y': 69, 'height': 0.4}}))
    # la trottola vista dall'alto: tre anelli e due righe che girano, e intanto va un po' a spasso
    gira = lambda r0: {'inizio': 0, 'fine': 100, 'tappe': [
        {'p': 0, 'x': 0, 'y': 0, 'r': r0}, {'p': 25, 'x': -40, 'y': 14, 'r': r0 + 540}, {'p': 50, 'x': 10, 'y': 26, 'r': r0 + 1080},
        {'p': 75, 'x': 40, 'y': 4, 'r': r0 + 1620}, {'p': 100, 'x': 0, 'y': 0, 'r': r0 + 2160}]}
    tx, ty, txm, tym, kt = 1180.0, 230.0, 545.0, 110.0, 0.7
    for nome, d, col in (('a', 170, C['acc']), ('b', 124, GIALLO), ('c', 78, CORALLO)):
        a = _pc(tx - d / 2, ty - d / 2, d, d, W, H)
        b = _pc(txm - d * kt / 2, tym - d * kt / 2, d * kt, d * kt, WM, HM)
        extra = {'boxShadow': {'x': 0, 'y': 14, 'blur': 26, 'spread': -6, 'color': mix(C['nero'], 30)}} if nome == 'a' else {}
        L.append(K.livello('co-trottola-' + nome, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=col, borderRadius=999,
                           entra='zoomIn', ritardo=300, scrollMove=gira(0),
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}, **extra))
    for k in range(2):
        a = _pc(tx - 85, ty - 6, 170, 12, W, H)
        b = _pc(txm - 85 * kt, tym - 6 * kt, 170 * kt, 12 * kt, WM, HM)
        L.append(K.livello('co-riga%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=mix(C['chiaro'], 70),
                           borderRadius=999, entra='zoomIn', ritardo=300, scrollMove=gira(90 * k),
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a = _pc(tx - 13, ty - 13, 26, 26, W, H)
    b = _pc(txm - 13 * kt, tym - 13 * kt, 26 * kt, 26 * kt, WM, HM)
    L.append(K.livello('co-pomolo', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['nero'], borderRadius=999,
                       entra='zoomIn', ritardo=300, scrollMove=gira(0),
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # i cubi: sul computer una riga da sinistra a destra; sul telefono due righe, prima quella di sotto (TOLA) poi TROT
    LATO, GAP = 120.0, 10.0
    x0 = (W - (8 * LATO + 7 * GAP)) / 2
    LM, GM = 124.0, 10.0
    x0m = (WM - (4 * LM + 3 * GM)) / 2
    righe_m = {0: 758 - 2 * LM - 6 - 8, 1: 758 - LM - 8}       # y della riga di sopra e di quella di sotto (pavimento a 758)
    ordine_m = [4, 5, 6, 7, 0, 1, 2, 3]
    for k, lettera in enumerate(LETTERE):
        col = COLORI[k]
        stile = dict(contenuto=lettera, size=78, peso='600', colore=C['nero'] if k in SCURE else C['chiaro'], bgColor=col,
                     borderRadius=16, align='center', textValign='center', lineHeight=1, fontFamily=TITOLI, entra='fadeIn',
                     ritardo=0, boxShadow={'x': 0, 'y': 8, 'blur': 0, 'spread': 0, 'color': mix(col, 62, C['nero'])})
        rot = (-1) ** k * (14 + 6 * (k % 3))
        a = _pc(x0 + k * (LATO + GAP), 632 - LATO, LATO, LATO, W, H)
        L.append(K.livello('co-cubo%d' % k, x=a[0], y=a[1], w=a[2], h=a[3], scrollMove=_cade(8 + 9 * k, -820, rot, -30),
                           responsive={'mobile': {'visible': False}}, **stile))
        riga, colonna = (0, k) if k < 4 else (1, k - 4)
        b = _pc(x0m + colonna * (LM + GM), righe_m[riga], LM, LM, WM, HM)
        L.append(K.livello('co-cubom%d' % k, x=104, y=a[1], w=a[2], h=a[3],
                           scrollMove=_cade(8 + 9 * ordine_m.index(k), -1900, rot, -60),
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3], 'fontSize': 82}}, **stile))
    # la palla: arriva rotolando da destra e si ferma accanto all'ultimo cubo (sul telefono, sul pavimento)
    rotola = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 900, 'r': 900}, {'p': 82, 'x': 900, 'r': 900},
                                                  {'p': 90, 'x': 0, 'r': 0}, {'p': 92, 'x': -16, 'r': -20},
                                                  {'p': 95, 'x': 0, 'r': 0}, {'p': 100, 'x': 0, 'r': 0}]}
    a = _pc(x0 + 8 * (LATO + GAP) + 6, 632 - 92, 92, 92, W, H)
    b = _pc(470, 790, 96, 96, WM, HM)
    L.append(K.livello('co-palla', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                       shapeGradient={'angle': 90, 'from': CORALLO, 'fromStop': 50, 'to': GIALLO}, entra='fadeIn', ritardo=0,
                       boxShadow={'x': 0, 'y': 10, 'blur': 18, 'spread': -6, 'color': mix(C['nero'], 30)}, scrollMove=rotola,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # alla fine i cubi veri e il pulsante
    finale = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 16}, {'p': 84, 'o': 0, 'y': 16}, {'p': 92, 'o': 100, 'y': 0},
                                                  {'p': 100, 'o': 100, 'y': 0}]}
    L.append(K.livello('co-kit', contenuto='I cubi dell’alfabeto, trenta, coi bordi in rilievo · <strong>26&nbsp;€</strong>',
                       x=6, y=42, w=30, size=18, colore=C['testo'], lineHeight=1.5, entra='fadeIn', ritardo=0, scrollMove=finale,
                       responsive={'mobile': {'x': 7, 'y': 81.5, 'width': 60, 'fontSize': m(14)}}))
    L.append(K.livello('co-b', tipo='button', contenuto='Le costruzioni', x=6, y=52, w='auto', size=15, peso='700',
                       colore=C['on_acc'], bgColor=C['acc'], padding=14, borderRadius=999, entra='fadeIn', ritardo=0,
                       buttonUrl=N.cat_url('costruzioni'), hover={'bgColor': C['nero'], 'color': C['chiaro'], 'duration': 200},
                       scrollMove=finale, responsive={'mobile': {'x': 7, 'y': 90, 'fontSize': m(13)}}))
    # il titolo sopra a tutto: i cubi che cadono gli passano dietro
    L += [
        K.livello('co-o', contenuto='Le costruzioni · scorrete', x=6, y=9, w=40, size=13, peso='600', colore=C['acc'],
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.6, 'width': 86}}),
        K.livello('co-t', contenuto='Un cubo<br>alla volta.', tag='h2', x=6, y=13, w=40, size=66, peso='600', colore=C['testo'],
                  fontFamily=TITOLI, lineHeight=1.02, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(32), 'x': 7, 'y': 4.8, 'width': 60}}),
        K.livello('co-s', contenuto='Trenta cubi, ventisei lettere: scorrete, e si scrive il nostro nome.', x=6, y=31.5, w=30,
                  size=17, colore=C['tenue'], lineHeight=1.55, entra='fadeIn', ritardo=400,
                  responsive={'mobile': {'x': 7, 'y': 16.5, 'width': 86, 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('co-1', {'type': 'color', 'image': '', 'video': '', 'color': C['sala'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, L, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2200, scrollStep=False, globalBackground={'type': 'color', 'color': C['sala']},
                       heightMobile={'mode': 'px', 'value': HM})


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small',
                         style={'margin_top': 24}))
    out.append(t.section([
        N.testata_sezione(t, 'Il negozio', 'A cosa si gioca oggi?', P['negozio'], 'Tutto il negozio'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, costruzioni(t)))
    out.append(t.section([
        N.testata_sezione(t, 'I più giocati', 'Quelli che non tornano mai nella scatola', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'L’orso grande,', '80 centimetri.', 'Si abbraccia con due braccia: 65 €.', 'Peluche e bambole',
                               N.cat_url('peluche'), K.photo_bg('pexels-836040.jpg'), size=34)], wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Gli aquiloni,', 'per il lungomare.', 'Il delta vola anche col vento leggero: 25 €.',
                               'All’aria aperta', N.cat_url('aria-aperta'), K.photo_bg('pexels-32646283.jpg'), size=34)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte della settimana', color=GIALLO),
                      N.titolo(t, 'Fino a domenica.', size=44, color=C['chiaro']),
                      N.testo(t, 'Il trenino, l’orso grande, i cubi dell’alfabeto, la scatola delle serate.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left', pad_top=16)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(mix(C['acc'], 60, C['nero'])), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_TAPPETO, 'In negozio', 'Sul tappeto <em>si prova tutto.</em>',
                             'Il trenino è montato, i cubi sono per terra, l’orso aspetta sulla poltrona: si gioca, poi si sceglie.',
                             overlay=40, extra=[N.pulsante(t, 'Per età', P['eta'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, '0–3 anni', 'Per le mani piccole', N.cat_url('primi-anni')),
        t.one([N.prodotti(t, category='primi-anni', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-7296764.jpg', 'Una bambina infila gli anelli di legno sul gioco dei numeri', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Liste e compleanni'), N.titolo(t, 'La lista,<br>senza doppioni.', size=52),
                      N.testo(t, 'Per la nascita, il battesimo, i compleanni: scegliete i giochi in negozio o qui, noi teniamo '
                                 'la lista e la diamo a chi chiede. Niente tre orsetti uguali.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'La lista si condivide con un link', 'Si compra in negozio o online',
                              'Incartiamo e consegniamo il giorno della festa']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'Fare la lista', P['liste'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il negozio', 'Dalla culla al lungomare', 'Giochi di legno, costruzioni, peluche e bambole, giochi '
                            'da tavolo e puzzle, aquiloni: si incarta a pois, gratis.', colonne=3,
                            banner=N.banner(t, 'Il trenino di legno,', '39 €.', 'Quaranta pezzi, fino a domenica.',
                                            'Giochi di legno', N.cat_url('legno'), K.photo_bg('pexels-15250000.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'I cubi dell’alfabeto,', '26 €.', 'Trenta cubi coi bordi in rilievo. Erano 32 €.', 'Costruzioni',
                        N.cat_url('costruzioni'), K.photo_bg('pexels-12955654.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'L’orso grande,', '65 €.', 'Ottanta centimetri di abbracci. Era 79 €.', 'Peluche e bambole',
                        N.cat_url('peluche'), K.photo_bg('pexels-35172085.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Le offerte della settimana', 'Fino a domenica', 'Il trenino, l’orso grande, i cubi dell’alfabeto e la '
                            'scatola delle serate a meno, finché la trottola gira.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('gift', 'Incarto a pois, gratis'), ('truck', 'Gratis da 39 €'),
                                                   ('shield-check', 'Colori all’acqua'), ('rotate-ccw', 'Resi in 30 giorni')],
                             bundle='Si gioca insieme', miniature='left', altezza='560px', bottone='Aggiungi al carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 39 €. L’incarto a pois è gratis.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'L’indirizzo, la spedizione, il pagamento. Nelle note: il biglietto per il festeggiato.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, gli indirizzi, le vostre liste di nascita e dei compleanni.',
                            ('Dov’è il mio pacco?', 'Scriveteci col numero dell’ordine: vi diciamo dov’è.', 'Scrivici',
                             P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto dei giochi.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'Al telefono e in negozio dalle 9:30 alle 20, dal lunedì al sabato.', [
        ('truck', 'Spedizioni', 'In 24 ore, gratis sopra i 39 €.', 'spedizioni'),
        ('gift', 'Incarto', 'A pois o a righe, gratis.', 'spedizioni'),
        ('shield-check', 'Sicurezza', 'Colori all’acqua, età sull’etichetta.', 'scrivici'),
        ('rotate-ccw', 'Resi', 'In 30 giorni, anche i regali.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 39 €; sotto, 5 €. Parte il giorno dopo, incartata se lo chiedete.'),
        ('Si può rendere un regalo?', 'Sì, in 30 giorni, anche senza scontrino: lo cambiamo con un altro gioco.'),
        ('I giochi sono sicuri?', 'Tutti col marchio CE, i colori all’acqua e l’età giusta sull’etichetta.'),
        ('Consegnate il giorno della festa?', 'A Bari sì, con la bici: scriveteci il giorno e l’ora nelle note.')],
        CONTATTI, campi_extra='Una lista di nascita\nUn regalo da cambiare\n',
        foto=('pexels-31485519.jpg', 'Il negozio di giocattoli, con gli scaffali di legno'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa pagina è rotolata via.', 'Sarà sotto il divano: cercate nel negozio qui sopra.',
                        K.video_bg(V_TROTTOLA, overlay=40))


def build_eta():
    t = Tpl(ID, 'eta')
    out = [N.intestazione(t, 'Per età', 'Il gioco giusto, all’età giusta.', 'Ogni gioco ha la sua età sull’etichetta, ma conta '
                          'anche cosa piace: ecco da dove cominciare.')]
    fasce = [('baby', '0–12 mesi', 'Da guardare, toccare, mordere: il libro degli animali, l’orsetto, l’arcobaleno.',
              N.cat_url('primi-anni')),
             ('blocks', '1–3 anni', 'Da impilare e infilare: l’abaco dei numeri, la scatola delle forme, i cubi.',
              N.cat_url('primi-anni')),
             ('train-front', '3–6 anni', 'Da costruire e far correre: il trenino, le costruzioni, le macchinine.',
              N.cat_url('legno')),
             ('dices', 'Dai 6 anni', 'Da giocare insieme: la scatola delle serate, gli scacchi, i puzzle da cento.',
              N.cat_url('tavolo'))]
    out.append(t.section([
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': u, 'color': ''} for i, a, b, u in fasce],
            'icon_color': C['acc'], 'icon_size': '24', 'text_color': C['testo'], 'text_size': '18', 'gap': '20',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 40, 'padding_bottom': 56}))
    out.append(K.video_frase(t, V_ARCOBALENO, 'Il primo anno', 'Un arcobaleno <em>vale dieci giochi.</em>',
                             'Si impila, si dondola, diventa ponte e culla: i giochi aperti durano anni, perché il gioco lo '
                             'inventa chi gioca.', overlay=40))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La sicurezza'), N.titolo(t, 'Legno, acqua e niente spigoli.', size=44),
                      N.testo(t, 'Scegliamo giochi col marchio CE, colorati coi colori all’acqua, levigati a mano: sull’etichetta '
                                 'c’è l’età e, per i più piccoli, i pezzi sono più grandi della bocca.', size=17),
                      K.faq(t, [('Da quando il trenino?', 'Dai due anni: i vagoni sono grandi e le calamite chiuse nel legno.'),
                                ('I peluche si lavano?', 'Sì, a 30 gradi in lavatrice, in una federa.'),
                                ('E le pile?', 'Quasi nessun gioco le usa: si gioca a mano.')], 'sic')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-7296459.jpg', 'Un bambino infila le palline colorate sui bastoncini', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='sicurezza', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        N.testata_sezione(t, 'Dai 3 anni', 'Giochi di legno', N.cat_url('legno')),
        t.one([N.prodotti(t, category='legno', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_liste():
    t = Tpl(ID, 'liste')
    out = [K.intro(t, 'Liste e compleanni', ['La lista dei giochi,', 'senza doppioni.'], 'Per la nascita, il battesimo, i '
                   'compleanni: la scegliete con noi, la teniamo noi, la date a chi chiede.',
                   bg=K.video_bg(V_SORELLE, overlay=40), size=86)]
    passi = [('list', 'Si sceglie', 'In negozio, sul tappeto, o qui sul sito col cuore sulle foto.'),
             ('share-2', 'Si condivide', 'Vi diamo un link da mandare a nonni, zii e amici.'),
             ('gift', 'Si regala', 'Chi compra lo segna: nessun doppione. Incarto a pois, gratis.'),
             ('bike', 'Si consegna', 'A Bari, il giorno della festa, in bici.')]
    out.append(t.section([
        N.testata_sezione(t, 'Come funziona', 'Quattro passi'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in passi],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 56}))
    out.append(t.section([
        t.one([K.numeri(t, [('0', 'doppioni'), ('1 link', 'per tutti gli invitati'), ('10%', 'di sconto a chi fa la lista'),
                            ('24 h', 'per incartare e consegnare')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['acc']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    modulo = t.classic('form', {
        'fields': [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True, segnaposto='nome@esempio.it'),
                   K.campo('select', 'La lista', 'lista', True, opzioni='Nascita\nBattesimo\nCompleanno\nNatale'),
                   K.campo('date', 'Il giorno della festa', 'giorno', True),
                   K.campo('textarea', 'Qualcosa da dirci', 'note', False, '1-1', segnaposto='L’età, cosa gli piace, cosa ha già')],
        'email_subject': 'Trottola — una lista', 'submit_text': 'Apri la lista', 'submit_icon': '',
        'success_message': 'Grazie: vi scriviamo entro domani col link della lista.', 'submit_full_width': False,
        'submit_alignment': 'left', 'label_color': C['testo'], 'label_size': '13', 'label_weight': '600',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': C['chiaro'], 'input_color': C['testo'],
        'input_border_color': C['filo'], 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 14,
        'input_focus_border': C['acc'], 'input_focus_shadow': False, 'gap': '14', 'submit_bg': C['acc'],
        'submit_color': C['on_acc'], 'submit_radius': 999, 'submit_padding_x': '28', 'submit_padding_y': '15',
        'submit_font_size': '15', 'submit_font_weight': '700', 'submit_text_transform': 'none',
        'submit_letter_spacing': '0', 'submit_hover_bg': C['nero'], 'privacy_checkbox': True},
        style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
               'border_radius': 0})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'La vostra lista'), N.titolo(t, 'Cominciamo<br>da qui.', size=46),
                      N.testo(t, 'Diteci la festa e il giorno: vi scriviamo col link della lista e un’idea per cominciare.',
                              size=17),
                      K.image(t, 'pexels-7296780.jpg', 'Una bimba gioca coi blocchi di legno sul tavolino', ratio='4/3')],
                     wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start'),
    ], html_id='lista', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 88, 'padding_bottom': 88}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Dove'), N.titolo(t, 'In via Sparano.', size=40),
                      N.testo(t, 'Dal lunedì al sabato, 9:30–13:30 e 16:30–20:30. Il tappeto è al piano terra, il trenino gira '
                                 'tutto il giorno.', size=17),
                      K.faq(t, [('Si può giocare in negozio?', 'Sì: è fatto apposta. I giochi aperti sono sul tappeto.'),
                                ('C’è il fasciatoio?', 'Sì, nel bagno in fondo.'),
                                ('Si entra col passeggino?', 'Sì, la porta è larga e non ci sono gradini.')], 'dove')],
                     wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-31485519.jpg', 'Il negozio di giocattoli coi giochi sugli scaffali', ratio='4/5')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='dove', padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'Le costruzioni',
    ['giocattoli', 'giochi di legno', 'bambini', 'liste di nascita', 'woocommerce', 'e-commerce', 'bari', 'regali'],
    'Il negozio online di giocattoli a Bari: in apertura la fascia delle offerte che gira come un cubo e le quattro schede coi '
    'prodotti, il mega menu col negozio, le offerte e le novità; a metà pagina i cubi che cadono uno alla volta mentre si '
    'scorre, finché si legge TROTTOLA, con la trottola che gira e la palla che arriva rotolando; i giochi per età, le liste di '
    'nascita e dei compleanni, la scheda con l’incarto a scelta e il video del gioco.',
    [('Il negozio', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('0–3 anni', '{{PAGINA:negozio}}?product_cat=primi-anni'), ('Legno', '{{PAGINA:negozio}}?product_cat=legno'),
     ('Peluche', '{{PAGINA:negozio}}?product_cat=peluche'), ('Per età', P['eta']), ('Le liste', P['liste'])],
    [('home', 'Home', None), ('negozio', 'Il negozio', 'negozio'), ('eta', 'Per età', 'per-eta'),
     ('liste', 'Liste e compleanni', 'liste'), ('offerte', 'Le offerte della settimana', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Nunito\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fredoka\', \'Nunito\', sans-serif',
    '\'Azeret Mono\', ui-monospace, Menlo, monospace', ['Fredoka', 'Nunito', 'Azeret Mono'], corsivo_di=[],
    peso_titoli='600', raggio=20, grana=0, titoli_line='1.04', titoli_spazio='-0.005em')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Il negozio': ('pannello-reparti', 'Pannello del negozio'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['preview_v'] = THEME['preview_mobile_v'] = '4'  # anteprime con le testate nuove (2 nov 2026)
THEME['cursor']['style'] = 'trottola'
THEME['cursor']['ring_color'] = HEX['primary']     # le righe blu
THEME['cursor']['dot_color'] = HEX['secondary']    # e quelle gialle


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'eta.json': build_eta(), 'liste.json': build_liste(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'incarto': {'nome': 'Incarto', 'termini': INCARTI}},
                    dentro='Il gioco nella sua scatola, incartato se l’avete chiesto, col biglietto per il festeggiato.')


if __name__ == '__main__':
    main()
