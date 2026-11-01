# -*- coding: utf-8 -*-
"""Genera il tema «Spiedo» (id spiedo): kebab, dürüm, falafel e grigliate in via del Boschetto, a Roma — da ordinare online,
da ritirare in quindici minuti o da farsi portare in bici entro mezz'ora.

Tema chiaro e caldo: il rosso del peperoncino sui pulsanti, il giallo della curcuma sui bollini, la carta da forno della pagina.
Titoli in Archivo Black (grossi, da insegna), testo in Work Sans, prezzi e orari in Space Mono. Angoli morbidi.

Apertura: la vetrina delle offerte, come nei negozi grandi: una fascia bassa che scorre da sola (il menu del giorno, il piatto
misto per due, l'adana alla brace), sotto le quattro schede coi piatti veri; il mega menu apre il menù, le offerte e le novità.
A metà pagina «il dürüm»: lo slider resta fermo mentre si scorre, sulla piadina arrivano la carne, l'insalata, il pomodoro, la
cipolla e la salsa, e lo scontrino a sinistra si allunga riga per riga fino al totale. Cursore: il peperoncino.

WooCommerce: il menù coi filtri, la scheda del piatto con la galleria (il video dello spiedo) e la carne (pollo, vitello,
misto), il carrello, la cassa a passi, l'account, i preferiti; la consegna coi tempi e le zone, la cucina.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'spiedo'
NAME = 'Spiedo'

HEX = {
    'primary': '#B3261E', 'primary_contrast': '#FFFFFF', 'secondary': '#F2B705', 'secondary_contrast': '#1A120D',
    'accent': '#8E1B10', 'dark': '#1A120D', 'light': '#FFFFFF', 'muted': '#F3E9DA', 'muted_contrast': '#22160F',
    'text': '#22160F', 'text_muted': '#665343', 'background': '#FBF5EB', 'border': '#E6D6C0', 'link': '#B3261E',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'consegna', 'cucina', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'ordini@spiedo.example', '+39 06 0000 7171', 'tel:+390600007171'

V_COLTELLO, V_GIRA, V_BRACE_SPIEDO, V_VASSOIO, V_TAGLIO, V_VERDURE, V_CARNE, V_GRATICOLA, V_FALAFEL, V_FRITTURA = (
    'pexels-video-5528187', 'pexels-video-5528177', 'pexels-video-5528189', 'pexels-video-5780703', 'pexels-video-5528188',
    'pexels-video-35594347', 'pexels-video-10329455', 'pexels-video-35008170', 'pexels-video-36985377', 'pexels-video-37191796')
VIDEOS = [V_COLTELLO, V_GIRA, V_BRACE_SPIEDO, V_VASSOIO, V_TAGLIO, V_VERDURE, V_CARNE, V_GRATICOLA, V_FALAFEL, V_FRITTURA]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    15202777, 37229056, 23385290, 18062061, 18330008, 10837796, 5779372, 4899802, 15390220, 15390671, 7439809, 14457194,
    18177341, 16022887, 4899787, 40262789, 32401042, 18177335, 9213971, 6275187, 27556250, 38431258, 6275158, 6275223,
    30488354, 36100519, 7129408, 26838687, 30666770, 30350308, 1603463, 38602968, 29244071, 29244073, 12738458, 29274608,
    5840330, 37650413, 38209559, 39572215, 1893555, 32854867, 8165289, 14537694, 29908848, 32909266, 37228290, 6252676,
    5737364, 6089614, 15481449, 6089617, 6089625, 11842151, 11842139, 37347576, 16107643, 25388876, 21370815, 8635161,
    20183029, 20183032, 7803118, 18142599, 9259573, 15999630, 5370563, 18142615, 17313147, 32801356, 39069513, 24196143,
    5779791, 20743741)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='400', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
CURCUMA, BRUNO = C['sec'], C['acc2']
TITOLI, MONO = 'Archivo Black', 'Space Mono'
N = Negozio(K, NAME, P, raggio=14, ratio='1-1', card='border', hover='zoom', testata_scura=False,
            consegna='Ritiro in 15 minuti, consegna in 30', evidenza='Il più ordinato', marca='', peso_titoli='400')

CATEGORIE = [
    ('kebab', 'Kebab e dürüm', 'La carne dallo spiedo, nel pane o nella piadina.', 'pexels-15202777.jpg'),
    ('piatti', 'Piatti e menu', 'Col riso, l’insalata, l’hummus; i menu con la bibita.', 'pexels-14457194.jpg'),
    ('vegetariano', 'Falafel e vegetariano', 'Falafel, hummus, la piadina agli spinaci.', 'pexels-27556250.jpg'),
    ('griglia', 'Alla brace', 'Spiedini, adana, la grigliata per due.', 'pexels-38602968.jpg'),
    ('contorni', 'Contorni e salse', 'Patatine, hummus, le salse della casa.', 'pexels-32854867.jpg'),
    ('dolci-bibite', 'Dolci e bibite', 'Baklava, limonata alla menta.', 'pexels-16107643.jpg'),
]
CARNI = ['Pollo', 'Vitello', 'Misto']


def piatto(sku, nome, slug, cat, prezzo, sconto, voto, rec, evid, foto, punti, testo, carne=False, **kw):
    if carne:
        kw['varianti'] = ('carne', CARNI, [])
    return prodotto(sku, nome, slug, cat, prezzo, sconto, 99, voto, rec, evid, foto, punti, testo, **kw)


PRODOTTI = [
    piatto('SPI-001', 'Kebab nel pane', 'kebab-pane', 'kebab', 6.5, 5.5, 4.8, 1312, True,
           ['pexels-5779372.jpg', 'pexels-15202777.jpg'], ['La carne dallo spiedo', 'Pane cotto al momento', 'Insalata, pomodoro, '
                                                            'cipolla', 'Il video dello spiedo'],
           'Il kebab come si deve: il pane caldo, la carne tagliata dallo spiedo, la salsa allo yogurt.', carne=True,
           video=V_COLTELLO, cross=['SPI-015', 'SPI-019']),
    piatto('SPI-002', 'Dürüm', 'durum', 'kebab', 7.5, 0, 4.9, 1544, True, ['pexels-10837796.jpg', 'pexels-4899802.jpg'],
           ['Piadina turca arrotolata', 'Carne, insalata, pomodoro', 'Salsa allo yogurt', 'Si mangia camminando'],
           'La piadina turca arrotolata stretta: si mangia camminando per Monti.', carne=True, video=V_TAGLIO),
    piatto('SPI-003', 'Dürüm piccante', 'durum-piccante', 'kebab', 8, 0, 4.7, 621, False, ['pexels-15390220.jpg'],
           ['Peperoncino di Urfa', 'Salsa harissa', 'Cipolla al sommacco', 'Piccante davvero'],
           'Il dürüm col peperoncino di Urfa e l’harissa: piccante davvero.', carne=True),
    piatto('SPI-004', 'Kebab nel cornetto salato', 'kebab-cornetto', 'kebab', 9.5, 0, 4.6, 233, False, ['pexels-23385290.jpg'],
           ['Cornetto salato al burro', 'Vitello dallo spiedo', 'Insalata e salsa', 'L’invenzione della casa'],
           'L’invenzione della casa: il cornetto salato caldo, col vitello dallo spiedo dentro.'),
    piatto('SPI-005', 'Box kebab e patatine', 'box-kebab', 'piatti', 10.5, 8.9, 4.8, 702, True,
           ['pexels-15390671.jpg', 'pexels-39572215.jpg'], ['Carne dallo spiedo', 'Patatine fritte al momento', 'Due salse',
                                                             'Nella scatola, con la forchetta'],
           'La scatola col kebab, le patatine fritte al momento e due salse: per il pranzo in ufficio.', carne=True),
    piatto('SPI-006', 'Piatto shawarma con hummus', 'piatto-shawarma', 'piatti', 12.5, 0, 4.8, 388, False,
           ['pexels-18177341.jpg', 'pexels-40262789.jpg'], ['Shawarma di pollo', 'Hummus e pane caldo', 'Insalata e sottaceti',
                                                             'Salsa all’aglio'],
           'Lo shawarma di pollo nel piatto, con l’hummus, il pane caldo e i sottaceti rosa.'),
    piatto('SPI-007', 'Piatto misto per due', 'piatto-misto-due', 'piatti', 24, 19, 4.9, 455, True,
           ['pexels-14457194.jpg', 'pexels-32401042.jpg'], ['Kebab, adana, spiedini', 'Hummus e tabulè', 'Pane caldo',
                                                             'Per due, anche tre'],
           'La grigliata da dividere: kebab, adana, spiedini, hummus, tabulè e il pane caldo.'),
    piatto('SPI-008', 'Falafel nel pane', 'falafel-pane', 'vegetariano', 6, 0, 4.7, 610, False,
           ['pexels-27556250.jpg', 'pexels-9213971.jpg'], ['Falafel fritti al momento', 'Hummus e tahina', 'Insalata e pomodoro',
                                                            'Vegano'],
           'I falafel fritti al momento nel pane caldo, con la tahina: vegani.', video=V_FALAFEL),
    piatto('SPI-009', 'Bowl di falafel e tabulè', 'bowl-falafel', 'vegetariano', 10, 0, 4.6, 205, False,
           ['pexels-30488354.jpg', 'pexels-6275187.jpg'], ['Falafel', 'Tabulè di bulgur', 'Hummus', 'Melograno'],
           'La ciotola leggera: falafel, tabulè, hummus e i chicchi di melograno.'),
    piatto('SPI-010', 'Piadina turca spinaci e formaggio', 'piadina-spinaci', 'vegetariano', 7, 0, 4.6, 233, False,
           ['pexels-30350308.jpg', 'pexels-36100519.jpg'], ['Sfoglia tirata a mano', 'Spinaci e formaggio', 'Cotta sulla piastra',
                                                             'Vegetariana'],
           'La gözleme: la sfoglia tirata a mano, gli spinaci, il formaggio, la piastra.'),
    piatto('SPI-011', 'Lahmacun, la pizza turca', 'lahmacun', 'vegetariano', 6.5, 0, 4.5, 140, False, ['pexels-1603463.jpg'],
           ['Sottile e croccante', 'Carne macinata e spezie', 'Prezzemolo e limone', 'Si arrotola'],
           'La pizza turca sottile con la carne speziata: si arrotola col prezzemolo e il limone.'),
    piatto('SPI-012', 'Spiedini di pollo, tre pezzi', 'spiedini-pollo', 'griglia', 9.5, 0, 4.7, 312, False,
           ['pexels-37650413.jpg', 'pexels-29244071.jpg'], ['Pollo marinato allo yogurt', 'Alla brace', 'Col riso o le patatine',
                                                             'Salsa all’aglio'],
           'Il pollo marinato una notte nello yogurt e nelle spezie, sulla brace.', video=V_GRATICOLA),
    piatto('SPI-013', 'Adana kebab con riso', 'adana-riso', 'griglia', 13, 10.5, 4.9, 287, True,
           ['pexels-7439809.jpg', 'pexels-38209559.jpg'], ['Carne macinata a mano', 'Peperoncino e grasso di coda', 'Alla brace',
                                                            'Col riso al burro'],
           'L’adana: la carne battuta a mano col peperoncino, sulla brace, col riso al burro.'),
    piatto('SPI-014', 'Spiedini misti alla brace, per due', 'spiedini-misti', 'griglia', 19, 0, 4.8, 166, False,
           ['pexels-38602968.jpg', 'pexels-29244073.jpg', 'pexels-12738458.jpg'], ['Pollo, vitello, verdure', 'Sei spiedini',
                                                                                    'Pane caldo', 'Due salse'],
           'Sei spiedini misti dalla brace, il pane caldo, due salse: per due.', video=V_VERDURE),
    piatto('SPI-015', 'Patatine con due salse', 'patatine-salse', 'contorni', 4, 0, 4.6, 980, False,
           ['pexels-32854867.jpg', 'pexels-8165289.jpg'], ['Fritte al momento', 'Due salse a scelta', 'Sale e paprika',
                                                            'Porzione grande'],
           'Le patatine fritte al momento, con due salse della casa.', video=V_FRITTURA),
    piatto('SPI-016', 'Hummus e pane caldo', 'hummus-pane', 'contorni', 5.5, 0, 4.8, 344, False,
           ['pexels-6089614.jpg', 'pexels-11842151.jpg'], ['Ceci e tahina', 'Olio e paprika', 'Pane caldo', 'Vegano'],
           'L’hummus cremoso con l’olio e la paprika, e il pane appena uscito dal forno.'),
    piatto('SPI-017', 'Le salse della casa, quattro vasetti', 'salse-casa', 'contorni', 3.5, 0, 4.7, 412, False,
           ['pexels-29908848.jpg', 'pexels-6252676.jpg'], ['Yogurt e aglio', 'Harissa', 'Tahina', 'Peperoncino'],
           'Le quattro salse: yogurt e aglio, harissa, tahina, peperoncino.'),
    piatto('SPI-018', 'Baklava al pistacchio, sei pezzi', 'baklava-pistacchio', 'dolci-bibite', 7.5, 0, 4.9, 377, False,
           ['pexels-16107643.jpg', 'pexels-20183029.jpg'], ['Pasta fillo e burro', 'Pistacchi di Antep', 'Sciroppo al limone',
                                                             'Sei pezzi'],
           'La baklava coi pistacchi di Antep: sei pezzi, croccanti e dolcissimi.'),
    piatto('SPI-019', 'Limonata alla menta', 'limonata-menta', 'dolci-bibite', 3.5, 2.5, 4.7, 266, False,
           ['pexels-9259573.jpg', 'pexels-15999630.jpg'], ['Limoni spremuti', 'Menta fresca', 'Poco zucchero',
                                                            'Mezzo litro'],
           'La limonata spremuta con la menta fresca: spegne il peperoncino.'),
    piatto('SPI-020', 'Menu Spiedo: kebab, patatine e bibita', 'menu-spiedo', 'piatti', 12, 9.9, 4.8, 860, True,
           ['pexels-15202777.jpg', 'pexels-39572215.jpg'], ['Kebab nel pane', 'Patatine', 'Una bibita', 'Fino alle 15'],
           'Il menu del pranzo: il kebab nel pane, le patatine e una bibita, fino alle tre.', carne=True),
]
RECENSIONI = [[5, 'Il miglior dürüm di Roma, e arriva ancora caldo.'],
              [5, 'Ordinato alle 13, ritirato alle 13:12. Perfetto.'],
              [4, 'Buonissimo, la salsa piccante è davvero piccante.'],
              [5, 'I falafel sono croccanti fuori e morbidi dentro.'],
              [5, 'Il piatto misto per due ci ha sfamati in tre.'],
              [4, 'Ottimo, le patatine erano un po’ tiepide con la consegna.'],
              [5, 'La baklava è da ordinare sempre.'],
              [3, 'Buono, ma il venerdì sera la consegna è più lenta.']]
NOMI = ['Marco D.', 'Giulia S.', 'Lorenzo P.', 'Francesca R.', 'Tommaso B.', 'Beatrice C.', 'Edoardo F.', 'Aurora M.',
        'Leonardo T.', 'Sofia G.', 'Mattia V.', 'Greta L.']
PROMESSE = [('clock', 'Ritiro in 15 minuti'), ('bike', 'Consegna in bici entro 30 minuti'), ('truck', 'Consegna gratuita da 20 €'),
            ('flame', 'Lo spiedo gira dalle 11 alle 2')]
CONTATTI = [('map-pin', 'Spiedo · via del Boschetto 41, Roma', P['consegna'] + '#ritiro'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'SPIEDO', icona='flame', ricerca='Cerca un kebab, i falafel, la baklava…',
                     parole='un dürüm piccante\ni falafel vegani\nla grigliata per due\nla baklava al pistacchio',
                     annuncio=('Il menu del giorno a 9,90 €: kebab, patatine e bibita, fino alle 15.', 'Le offerte',
                               P['offerte']), logo_size=28)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'SPIEDO', 'flame', 'Kebab, dürüm, falafel e grigliate in via del Boschetto, a Monti: lo '
                   'spiedo gira dalle undici alle due di notte, si ritira in quindici minuti o arriva in bici in mezz’ora.',
                   [('Il menù', [('Kebab e dürüm', N.cat_url('kebab')), ('Piatti e menu', N.cat_url('piatti')),
                                 ('Falafel e vegetariano', N.cat_url('vegetariano')), ('Offerte', P['offerte'])]),
                    ('Spiedo', [('La consegna', P['consegna']), ('La cucina', P['cucina']), ('I preferiti', P['preferiti']),
                                ('Il mio account', P['account'])]),
                    ('Aiuto', [('Tempi e zone', P['consegna'] + '#zone'), ('Allergeni', P['cucina'] + '#allergeni'),
                               ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Spiedo s.r.l. · Roma · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera dello spiedo', 'Il piatto nuovo del mese.',
                              'Una lettera al mese: il piatto nuovo, gli sconti del martedì, le sere lunghe.',
                              'Fatto: la prima lettera arriva con la limonata gratis.'),
                   social=[('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.62  # la larghezza media di una lettera dei titoli (Archivo Black, per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-10)
    return N.vetrina(t, [
        N.offerta('sp-o1', K.sfondo_video(V_COLTELLO, overlay=0.12), 'Il menu del giorno · fino alle 15',
                  'Kebab, patatine e bibita a 9,90 €.', 'La carne tagliata dallo spiedo, le patatine fritte al momento. Era 12 €.',
                  'Il menu', N.cat_url('piatti'), bollino='−18%', bollino_x=74, nota='Ritiro in 15 minuti, consegna in 30',
                  primo=True, **f),
        N.offerta('sp-o2', K.sfondo_foto('pexels-14457194.jpg', overlay=0.12, scala=1.06), 'Per due · la grigliata',
                  'Il piatto misto a 19 €.', 'Kebab, adana, spiedini, hummus, tabulè e il pane caldo. Era 24 €.', 'I piatti',
                  N.cat_url('piatti'), bollino='−21%', nota='Consegna gratuita da 20 €', **f),
        N.offerta('sp-o3', K.sfondo_video(V_VERDURE, overlay=0.12), 'Alla brace · stasera',
                  'L’adana a 10,50 €.', 'La carne battuta a mano col peperoncino, sulla brace, col riso. Era 13 €.',
                  'Alla brace', N.cat_url('griglia'), bollino='−19%', nota='Si ordina fino alle 23:30', **f),
    ], transizione='pushDown')


# le schede sotto la vetrina: «tre» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='tre', voci=[
        ('Le offerte di oggi', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Kebab e dürüm', {'category': 'kebab', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('kebab')),
        ('Sotto i 6 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=6'),
        ('I più ordinati', {'orderby': 'popularity'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Il menù', 'Dallo spiedo, dalla brace, dal forno',
                              ('Il dürüm,', '7,50 €.', 'Pollo, vitello o misto.', 'Kebab e dürüm', N.cat_url('kebab'),
                               'pexels-10837796.jpg'), tutto='Tutto il menù')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte di oggi', 'Fino a mezzanotte.',
                              'Quelle di oggi finiscono tra:',
                              ('La limonata,', '2,50 €.', 'Spremuta, con la menta. Era 3,50 €.', 'Dolci e bibite',
                               N.cat_url('dolci-bibite'), 'pexels-9259573.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Il piatto nuovo del mese',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('Il kebab', 'nel cornetto.', 'L’invenzione della casa, 9,50 €.', 'Kebab e dürüm',
                              N.cat_url('kebab'), 'pexels-23385290.jpg'), tutte='Tutto il menù')


# ── Il dürüm: lo slider resta fermo, sulla piadina arrivano gli ingredienti, lo scontrino si allunga fino al totale ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1200.0
RIGHE = [('La piadina turca', '1,00'), ('Il vitello dallo spiedo', '4,50'), ('Insalata e pomodoro', '0,80'),
         ('Cipolla al sommacco', '0,30'), ('Salsa allo yogurt', '0,90')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def durum(t):
    m = K.mob
    L = [
        K.livello('du-o', contenuto='Il dürüm · scorrete', x=6, y=8, w=40, size=13, peso='700', colore=CURCUMA,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.6, 'width': 86}}),
        K.livello('du-t', contenuto='Si fa così,<br>in un minuto.', tag='h2', x=6, y=12, w=44, size=60, peso='400',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(30), 'x': 7, 'y': 4.6, 'width': 86}}),
    ]
    # la piadina
    px, py, pd = 960.0, 420.0, 420.0
    pxm, pym, pdm = 320.0, 360.0, 380.0
    a, b = _pc(px - pd / 2, py - pd / 2, pd, pd, W, H), _pc(pxm - pdm / 2, pym - pdm / 2, pdm, pdm, WM, HM)
    L.append(K.livello('du-piadina', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#E8C78E', borderRadius=999,
                       borderWidth=3, borderColor='#C99A55', entra='zoomIn', ritardo=0, durata=900,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # gli ingredienti: (nome, colore, forme [(dx, dy, w, h)], da dove arrivano, tratto dello scorrimento)
    ingr = [('carne', '#6B3A1F', [(-90, -40, 70, 34), (-10, -70, 80, 30), (60, -20, 74, 32), (-60, 30, 76, 30), (20, 40, 70, 32),
                                 (90, 50, 60, 28)], (-700, -300), 14),
            ('insalata', '#5E9E3A', [(-120, -90, 60, 26), (40, -110, 64, 26), (110, 0, 58, 24), (-30, 100, 62, 26)], (700, -300), 30),
            ('pomodoro', '#D7372B', [(-40, -10, 36, 36), (50, 80, 34, 34), (-110, 60, 34, 34), (100, -80, 32, 32)], (700, 300), 44),
            ('cipolla', '#8E3C8C', [(10, -40, 40, 40), (-80, -100, 36, 36), (80, 100, 38, 38)], (-700, 300), 56),
            ('salsa', '#FFFFFF', [(-20, 0, 220, 40)], (0, -500), 68)]
    for nome, col, forme, (fx, fy), p in ingr:
        arriva = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': fx, 'y': fy, 'o': 0}, {'p': p - 2, 'x': fx, 'y': fy, 'o': 0},
                                                      {'p': p + 8, 'x': 0, 'y': 0, 'o': 100}, {'p': 100, 'x': 0, 'y': 0, 'o': 100}]}
        for k, (dx, dy, w, h) in enumerate(forme):
            a = _pc(px + dx - w / 2, py + dy - h / 2, w, h, W, H)
            km = pdm / pd
            b = _pc(pxm + dx * km - w * km / 2, pym + dy * km - h * km / 2, w * km, h * km, WM, HM)
            stile = {'bgColor': col} if nome != 'cipolla' else {'bgColor': 'transparent', 'borderWidth': 5, 'borderColor': col}
            if nome == 'salsa':
                stile = {'bgColor': mix('#FFFFFF', 80)}
            L.append(K.livello('du-%s%d' % (nome, k), tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999, entra='fadeIn',
                               ritardo=0, scrollMove=arriva, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2],
                                                                                     'height': b[3]}}, **stile))
    # lo scontrino: una riga alla volta, poi il totale e il pulsante
    a, b = _pc(80, 330, 420, 400, W, H), _pc(60, 590, 520, 470, WM, HM)
    L.append(K.livello('du-carta', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#FFFDF6', borderRadius=4,
                       boxShadow={'x': 0, 'y': 20, 'blur': 40, 'spread': -14, 'color': mix(C['nero'], 70)}, entra='fadeIn',
                       ritardo=200, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a, b = _pc(108, 350, 360, 24, W, H), _pc(84, 610, 470, 30, WM, HM)
    L.append(K.livello('du-intest', contenuto='SPIEDO · via del Boschetto 41', x=a[0], y=a[1], w=a[2], size=12, peso='700',
                       colore=C['tenue'], letterSpacing=2, fontFamily=MONO, entra='fadeIn', ritardo=300,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(10)}}))
    tratti = [12, 26, 40, 52, 64]
    for k, (voce, prezzo) in enumerate(RIGHE):
        y, ym = 392 + 40 * k, 660 + 52 * k
        appare = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'x': -16}, {'p': tratti[k], 'o': 0, 'x': -16},
                                                      {'p': tratti[k] + 6, 'o': 100, 'x': 0}, {'p': 100, 'o': 100, 'x': 0}]}
        a, b = _pc(108, y, 270, 26, W, H), _pc(84, ym, 360, 32, WM, HM)
        L.append(K.livello('du-voce%d' % k, contenuto=voce, x=a[0], y=a[1], w=a[2], size=15, peso='400', colore=C['testo'],
                           fontFamily=MONO, entra='fadeIn', ritardo=0, scrollMove=appare,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(12)}}))
        a, b = _pc(390, y, 80, 26, W, H), _pc(460, ym, 96, 32, WM, HM)
        L.append(K.livello('du-prezzo%d' % k, contenuto=prezzo, x=a[0], y=a[1], w=a[2], size=15, peso='700', colore=C['testo'],
                           align='right', fontFamily=MONO, entra='fadeIn', ritardo=0, scrollMove=appare,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(12)}}))
    totale = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.9}, {'p': 76, 'o': 0, 's': 0.9},
                                                  {'p': 84, 'o': 100, 's': 1}, {'p': 100, 'o': 100, 's': 1}]}
    a, b = _pc(108, 604, 360, 2, W, H), _pc(84, 925, 470, 2, WM, HM)
    L.append(K.livello('du-riga', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['filo'], entra='fadeIn', ritardo=0,
                       scrollMove=totale, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a, b = _pc(108, 616, 250, 36, W, H), _pc(84, 940, 330, 40, WM, HM)
    L.append(K.livello('du-tot', contenuto='Il vostro dürüm', x=a[0], y=a[1], w=a[2], size=20, peso='400', colore=C['testo'],
                       fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=totale,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(16)}}))
    a, b = _pc(360, 614, 110, 36, W, H), _pc(430, 938, 126, 40, WM, HM)
    L.append(K.livello('du-totp', contenuto='7,50 €', x=a[0], y=a[1], w=a[2], size=22, peso='700', colore=C['acc'],
                       align='right', fontFamily=MONO, entra='fadeIn', ritardo=0, scrollMove=totale,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(17)}}))
    a, b = _pc(108, 666, 200, 40, W, H), _pc(84, 994, 300, 44, WM, HM)
    L.append(K.livello('du-ordina', tipo='button', contenuto='Ordina il dürüm', x=a[0], y=a[1], w='auto', size=14, peso='700',
                       colore=C['on_acc'], bgColor=C['acc'], padding=12, borderRadius=10, entra='fadeIn', ritardo=0,
                       buttonUrl=N.cat_url('kebab'), hover={'bgColor': C['nero'], 'duration': 200}, scrollMove=totale,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(12)}}))
    sl = [K.diapositiva('du-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
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
        N.testata_sezione(t, 'Il menù', 'Cosa vi portiamo?', P['negozio'], 'Tutto il menù'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, durum(t)))
    out.append(t.section([
        N.testata_sezione(t, 'I più ordinati', 'Quelli che non finiscono mai', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'I falafel,', 'fritti adesso.', 'Nel pane o nella ciotola, vegani: da 6 €.',
                               'Falafel e vegetariano', N.cat_url('vegetariano'), K.photo_bg('pexels-27556250.jpg'), size=34)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'La baklava,', 'sei pezzi.', 'Pistacchi di Antep, pasta fillo e burro.',
                               'Dolci e bibite', N.cat_url('dolci-bibite'), K.photo_bg('pexels-16107643.jpg'), size=34)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte di oggi', color=CURCUMA),
                      N.titolo(t, 'Fino a mezzanotte.', size=44, color=C['chiaro']),
                      N.testo(t, 'Il menu del giorno, il piatto misto, l’adana alla brace, la limonata.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left', pad_top=16)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_GIRA, 'La cucina', 'Lo spiedo gira <em>dalle undici alle due.</em>',
                             'Vitello e pollo marinati una notte, impilati a mano la mattina, tagliati al momento per ogni ordine.',
                             overlay=40, extra=[N.pulsante(t, 'La cucina', P['cucina'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Alla brace', 'Dalla griglia, stasera', N.cat_url('griglia')),
        t.one([N.prodotti(t, category='griglia', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-5779791.jpg', 'Il cuoco gira gli spiedini sulla brace', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'La consegna'), N.titolo(t, 'In bici,<br>entro mezz’ora.', size=52),
                      N.testo(t, 'Consegniamo in bici in tutto il centro, da Monti a Trastevere, entro trenta minuti; gratis '
                                 'sopra i 20 €. Oppure ordinate e passate: in quindici minuti è pronto.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Ritiro in 15 minuti', 'Consegna in bici entro 30', 'Gratis sopra i 20 €']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'Tempi e zone', P['consegna'] + '#zone', pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il menù', 'Tutto quello che esce dalla cucina', 'Kebab e dürüm, piatti e menu, falafel, brace, '
                            'contorni, dolci e bibite: ritiro in quindici minuti, consegna in trenta.', colonne=3,
                            banner=N.banner(t, 'Il menu del giorno,', '9,90 €.', 'Kebab, patatine e bibita, fino alle 15.',
                                            'Il menu', N.cat_url('piatti'), K.photo_bg('pexels-39572215.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Il box kebab,', '8,90 €.', 'Con patatine e due salse. Era 10,50 €.', 'Piatti e menu',
                        N.cat_url('piatti'), K.photo_bg('pexels-15390671.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Il kebab nel pane,', '5,50 €.', 'Pollo, vitello o misto. Era 6,50 €.', 'Kebab e dürüm',
                        N.cat_url('kebab'), K.photo_bg('pexels-5779372.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Le offerte di oggi', 'Fino a mezzanotte', 'Il menu del giorno, il piatto misto, l’adana, il box, '
                            'il kebab nel pane e la limonata a meno, finché gira lo spiedo.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('clock', 'Ritiro in 15 minuti'), ('bike', 'Consegna in bici entro 30'),
                                                   ('truck', 'Gratis da 20 €'), ('leaf', 'Allergeni in cucina, chiedete')],
                             bundle='Ci sta bene', miniature='left', altezza='560px', bottone='Aggiungi all’ordine')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Consegna gratuita da 20 €. Ritiro in quindici minuti.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'Ritiro o consegna, l’indirizzo, il pagamento. Nelle note: il citofono, le allergie.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, gli indirizzi, i piatti di sempre da ripetere.',
                            ('Dov’è il mio ordine?', 'Scriveteci col numero dell’ordine: vi diciamo dov’è la bici.', 'Scrivici',
                             P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto del menù.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'Al telefono e in chat dalle 11 alle 2, tutti i giorni.', [
        ('clock', 'Ritiro', 'In quindici minuti, in via del Boschetto.', 'spedizioni'),
        ('bike', 'Consegna', 'In bici entro trenta minuti, nel centro.', 'spedizioni'),
        ('leaf', 'Allergeni', 'Glutine, sesamo, latte: chiedete in cucina.', 'scrivici'),
        ('briefcase', 'Aziende', 'Pranzi per l’ufficio, da dieci persone.', 'scrivici')], [
        ('Quanto costa la consegna?', 'Niente sopra i 20 €; sotto, 2,50 €. Arriva in bici entro mezz’ora nel centro.'),
        ('Fin dove consegnate?', 'Da Monti a Trastevere, dall’Esquilino a Prati: nella pagina della consegna c’è la mappa.'),
        ('E se arriva freddo?', 'Scriveteci entro un’ora: lo rifacciamo o vi rimborsiamo.'),
        ('Fate pranzi per l’ufficio?', 'Sì, da dieci persone in su, coi piatti misti e i falafel: scriveteci il giorno prima.')],
        CONTATTI, campi_extra='Un pranzo per l’ufficio\nUn ordine arrivato male\n',
        foto=('pexels-32801356.jpg', 'I cuochi al lavoro dietro il banco'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questo piatto <em>è finito.</em>', 'La pagina non c’è più: cercate nel menù qui sopra.',
                        K.video_bg(V_BRACE_SPIEDO, overlay=40))


def build_consegna():
    t = Tpl(ID, 'consegna')
    out = [N.intestazione(t, 'Asporto e consegna', 'In bici, entro mezz’ora.', 'Ritiro in quindici minuti in via del Boschetto; '
                          'consegna in bici in tutto il centro, gratis sopra i 20 €.')]
    zone = [('Monti, Esquilino', '15–20 minuti'), ('Centro storico, Pantheon', '20–25 minuti'), ('Trastevere, Testaccio', '25–30 minuti'),
            ('Prati, San Giovanni', '30 minuti')]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le zone'), N.titolo(t, 'Fin dove arriviamo.', size=46),
                      t.classic('iconlist', {
                          'items': [{'icon': 'map-pin', 'text': '%s · <strong>%s</strong>' % (a, b), 'link': '', 'color': ''}
                                    for a, b in zone],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '16', 'gap': '12',
                          'icon_shape': 'none', 'icon_bg_color': '', 'divider': True, 'layout': 'vertical'}, style={}),
                      N.testo(t, 'Fuori dalle zone? Ordinate e passate: in quindici minuti è pronto.', size=15, color=C['tenue'],
                              pad=(14, 0, 0, 0))], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-20743741.jpg', 'Il carretto del cibo di strada la sera', ratio='4/5')], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], html_id='zone', padding='large', width='large', style={'padding_top': 72, 'padding_bottom': 64}))
    out.append(t.section([
        t.one([K.numeri(t, [('15 min', 'per il ritiro'), ('30 min', 'per la consegna'), ('20 €', 'consegna gratis da'),
                            ('2:00', 'l’ultimo ordine')], colonne=4, size=52, chiaro=True)]),
    ], html_id='ritiro', bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le domande'), N.titolo(t, 'Prima di ordinare.', size=40),
                      K.faq(t, [('Posso scegliere l’ora?', 'Sì: nella cassa scegliete «il prima possibile» o un’ora, anche '
                                                           'per stasera.'),
                                ('Si paga alla consegna?', 'Si paga online; al ritiro anche con la carta al banco.'),
                                ('Le salse sono comprese?', 'Una salsa sì; le altre si aggiungono dai contorni.')], 'con')],
                     wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-39069513.jpg', 'Il cuoco taglia la carne dallo spiedo', ratio='4/5')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_cucina():
    t = Tpl(ID, 'cucina')
    out = [K.intro(t, 'La cucina', ['Marinato una notte,', 'tagliato adesso.'], 'Lo spiedo si prepara la mattina, a '
                   'mano; il pane si cuoce al momento; le salse le facciamo noi.', bg=K.video_bg(V_TAGLIO, overlay=40), size=86)]
    passi = [('moon', 'La sera prima', 'Il vitello e il pollo vanno nella marinata di yogurt, cipolla e spezie.'),
             ('layers', 'La mattina', 'Si impilano a mano sullo spiedo, strato dopo strato.'),
             ('flame', 'Il giorno', 'Lo spiedo gira davanti alla brace dalle undici alle due.'),
             ('scissors', 'Per voi', 'Si taglia al momento, per ogni ordine: mai prima.')]
    out.append(t.section([
        N.testata_sezione(t, 'Lo spiedo', 'Quattro tempi'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in passi],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 56}))
    out.append(K.video_frase(t, V_FRITTURA, 'I falafel', 'Si friggono <em>quando ordinate.</em>',
                             'Ceci ammollati una notte, prezzemolo, coriandolo, cumino: in padella solo all’ultimo.', overlay=40))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Gli allergeni'), N.titolo(t, 'Ditecelo prima.', size=44),
                      N.testo(t, 'Il pane contiene glutine e sesamo, le salse latte e sesamo, la baklava frutta a guscio. Nelle '
                                 'note dell’ordine scrivete le vostre allergie: la cucina le legge tutte.', size=17)],
                     wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-29908848.jpg', 'Le salse della casa nelle ciotole', ratio='4/3')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='center'),
    ], html_id='allergeni', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        N.testata_sezione(t, 'Dallo spiedo', 'I kebab', N.cat_url('kebab')),
        t.one([N.prodotti(t, category='kebab', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'Il dürüm',
    ['kebab', 'cibo di strada', 'asporto', 'consegna', 'woocommerce', 'e-commerce', 'roma', 'falafel'],
    'Il negozio online di un kebab a Monti, da ritirare in quindici minuti o da farsi portare in bici: in apertura la fascia '
    'delle offerte che scorre e le quattro schede coi piatti, il mega menu col menù, le offerte e le novità; a metà pagina il '
    'dürüm che si compone sulla piadina mentre lo scontrino si allunga fino al totale; la consegna con le zone e i tempi, la '
    'cucina, la scheda del piatto con la carne a scelta e il video dello spiedo.',
    [('Il menù', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Kebab', '{{PAGINA:negozio}}?product_cat=kebab'), ('Falafel', '{{PAGINA:negozio}}?product_cat=vegetariano'),
     ('Brace', '{{PAGINA:negozio}}?product_cat=griglia'), ('La consegna', P['consegna']), ('La cucina', P['cucina'])],
    [('home', 'Home', None), ('negozio', 'Il menù', 'menu'), ('consegna', 'Asporto e consegna', 'consegna'),
     ('cucina', 'La cucina', 'cucina'), ('offerte', 'Le offerte di oggi', 'offerte'), ('assistenza', 'Assistenza', 'assistenza'),
     ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'L’ordine', 'carrello'), ('cassa', 'La cassa', 'cassa'),
     ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Archivo Black\', \'Impact\', sans-serif',
    '\'Space Mono\', ui-monospace, Menlo, monospace', ['Archivo Black', 'Work Sans', 'Space Mono'], corsivo_di=[],
    peso_titoli='400', raggio=14, grana=0, titoli_line='1.02', titoli_spazio='-0.01em')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Il menù': ('pannello-reparti', 'Pannello del menù'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'peperoncino'
THEME['cursor']['ring_color'] = HEX['primary']  # il peperoncino è rosso; il punto (dot) è la curcuma
THEME['cursor']['dot_color'] = HEX['secondary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'consegna.json': build_consegna(), 'cucina.json': build_cucina(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'carne': {'nome': 'Carne', 'termini': CARNI}},
                    dentro='Il piatto nella scatola di carta, le posate di legno, i tovaglioli, la salsa scelta.')


if __name__ == '__main__':
    main()
