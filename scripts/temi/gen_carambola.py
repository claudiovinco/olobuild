# -*- coding: utf-8 -*-
"""Genera il tema «Carambola» (id carambola): una sala da biliardo e bar in via Vigevano, sui Navigli di Milano — dodici
tavoli da pool, carambola e snooker da prenotare a ore, serate e feste, corsi e tornei, e il negozio delle stecche.

Tema chiaro con la testata scura: il verde del panno sui pulsanti, il giallo della palla 1 nei bollini, il rosso della 3 nei
prezzi scontati. Titoli in Alfa Slab One (da insegna di sala giochi), testo in Barlow, orari e prezzi in Space Mono.

Apertura: la vetrina delle offerte, come nei negozi grandi: una fascia bassa che scorre da sola (lo snooker del martedì, la
serata per quattro, il corso base), sotto le quattro schede coi prodotti veri; il mega menu apre la sala, le offerte e le
novità. A metà pagina «il colpo d'apertura»: lo slider resta fermo mentre si scorre, la stecca tira, la bianca corre e apre
il triangolo, le quindici palle si sparpagliano sul panno e arrivano i tavoli da prenotare. Cursore: la stecca.

WooCommerce: i tavoli a ore con la fascia (pomeriggio, sera, notte), le serate, i corsi, le stecche e gli accessori, le carte
regalo; la scheda con la galleria e il video del colpo, il carrello, la cassa a passi, l'account, i preferiti; la sala e i
corsi coi tornei.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'carambola'
NAME = 'Carambola'

HEX = {
    'primary': '#0F5132', 'primary_contrast': '#FFFFFF', 'secondary': '#E8B931', 'secondary_contrast': '#0B1A13',
    'accent': '#C62828', 'dark': '#0B1A13', 'light': '#FFFFFF', 'muted': '#ECEFE8', 'muted_contrast': '#10201A',
    'text': '#10201A', 'text_muted': '#4F5E55', 'background': '#F4F1EA', 'border': '#D8DCD2', 'link': '#0F5132',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'sala', 'corsi', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'sala@carambola.example', '+39 02 0000 8158', 'tel:+390200008158'

V_APERTURA, V_BIANCA, V_ROSSO, V_SALA, V_PARTITA, V_TRIANGOLO, V_STECCHE, V_RACK, V_PANNO_ROSSO, V_VERDE = (
    'pexels-video-7279167', 'pexels-video-7727501', 'pexels-video-6503782', 'pexels-video-6253638', 'pexels-video-6253874',
    'pexels-video-7279180', 'pexels-video-7279165', 'pexels-video-7279085', 'pexels-video-6503787', 'pexels-video-6253870')
VIDEOS = [V_APERTURA, V_BIANCA, V_ROSSO, V_SALA, V_PARTITA, V_TRIANGOLO, V_STECCHE, V_RACK, V_PANNO_ROSSO, V_VERDE]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    13036106, 13036112, 13036107, 6253979, 10627132, 8092382, 11354500, 31916152, 11367368, 10627113, 39332572, 13179841,
    13036108, 13793180, 39981594, 6481560, 37157955, 13089177, 10627103, 34763004, 10627100, 10634430, 5055266, 5055423,
    5055414, 5055413, 8872378, 5055275, 10627152, 10627101, 37352317, 38345586, 6253920, 38345584, 36643764, 5055427,
    13089179, 6254496, 13089176, 16064240, 38127520, 27897590, 27897615, 4485379, 4485382, 12940784, 5858069, 1269042,
    1269025, 27623559, 1269040, 31091453, 16454047, 31425369, 5550311, 1876878, 5055270, 5055749, 5055271, 5055415, 5055425,
    35538586, 36640536, 30483014, 13179865)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=8, peso_titoli='400', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
GIALLO, ROSSO = C['sec'], C['acc2']
TITOLI, MONO = 'Alfa Slab One', 'Space Mono'
N = Negozio(K, NAME, P, raggio=8, ratio='1-1', card='border', hover='zoom', testata_scura=True,
            consegna='Prenoti ora, giochi stasera', evidenza='Il più prenotato', marca='', peso_titoli='400')
N.cta_media = (C['chiaro'], C['acc'])  # il verde del panno è scuro: sul velo scuro il pulsante è bianco

CATEGORIE = [
    ('tavoli', 'Prenota un tavolo', 'Pool, carambola e snooker, a ore.', 'pexels-8092382.jpg'),
    ('serate', 'Serate e feste', 'Per quattro, per il compleanno, per l’aperitivo.', 'pexels-5055270.jpg'),
    ('corsi', 'Corsi e tornei', 'Il corso base, il maestro, il torneo del giovedì.', 'pexels-10627100.jpg'),
    ('stecche', 'Stecche e custodie', 'Acero, ebano, la custodia in cuoio.', 'pexels-37157955.jpg'),
    ('accessori', 'Palle, gesso e guanti', 'Tutto per il tavolo di casa.', 'pexels-11367368.jpg'),
    ('regali', 'Carte regalo', 'Una serata da regalare.', 'pexels-31916152.jpg'),
]
FASCE = ['Pomeriggio', 'Sera', 'Notte']


def tavolo(sku, nome, slug, prezzo, sconto, voto, rec, evid, foto, punti, testo, **kw):
    return prodotto(sku, nome, slug, 'tavoli', prezzo, sconto, 99, voto, rec, evid, foto, punti, testo,
                    varianti=('fascia', FASCE, []), **kw)


PRODOTTI = [
    tavolo('CAR-001', 'Un’ora di pool', 'ora-pool', 14, 0, 4.8, 612, True, ['pexels-10627132.jpg', 'pexels-8092382.jpg'],
           ['Tavolo da 9 piedi', 'Stecche e gesso compresi', 'Pomeriggio, sera o notte', 'Il video del colpo d’apertura'],
           'Un tavolo da pool per un’ora: stecche, gesso e triangolo compresi. Scegliete la fascia, il tavolo lo teniamo noi.',
           video=V_APERTURA, cross=['CAR-013']),
    tavolo('CAR-002', 'Un’ora di carambola, tre sponde', 'ora-carambola', 16, 0, 4.7, 188, False,
           ['pexels-13036106.jpg', 'pexels-13036112.jpg'], ['Tavolo senza buche', 'Tre palle', 'Il gioco delle sponde',
                                                             'Il maestro il martedì'],
           'Il tavolo senza buche delle tre sponde: il biliardo dei nonni, che è il più difficile.'),
    tavolo('CAR-003', 'Un’ora di snooker', 'ora-snooker', 18, 14, 4.8, 141, True, ['pexels-11354500.jpg', 'pexels-36640536.jpg'],
           ['Tavolo da 12 piedi', 'Ventidue palle', 'Il martedì a meno', 'Stecche da snooker comprese'],
           'Il tavolo grande dello snooker, ventidue palle: il martedì costa meno.'),
    prodotto('CAR-004', 'Serata per quattro: due ore di pool e quattro birre', 'serata-quattro', 'serate', 59, 49, 40, 4.9, 344,
             True, ['pexels-5055270.jpg', 'pexels-12940784.jpg'], ['Due ore di tavolo', 'Quattro birre artigianali',
                                                                    'Patatine e olive', 'Dal giovedì alla domenica'],
             'Il modo migliore di passare il venerdì: due ore di pool, quattro birre della casa, patatine e olive.',
             cross=['CAR-006']),
    prodotto('CAR-005', 'Festa di compleanno: tre tavoli e buffet', 'festa-compleanno', 'serate', 240, 0, 10, 4.8, 66, False,
             ['pexels-5055275.jpg', 'pexels-1269025.jpg'], ['Tre tavoli per tre ore', 'Buffet per quindici',
                                                             'La torta la portate voi', 'Il maestro per una sfida'],
             'Il compleanno in sala: tre tavoli, il buffet per quindici, il maestro che sfida il festeggiato.'),
    prodotto('CAR-006', 'Aperitivo e partita, per due', 'aperitivo-partita', 'serate', 29, 0, 40, 4.7, 205, False,
             ['pexels-27897615.jpg', 'pexels-5055415.jpg'], ['Un’ora di tavolo', 'Due cocktail', 'Il tagliere',
                                                              'Dalle 18 alle 21'],
             'Un’ora di tavolo, due cocktail e il tagliere: l’aperitivo che finisce a biliardo.'),
    prodotto('CAR-007', 'Corso base, sei lezioni', 'corso-base', 'corsi', 120, 99, 12, 4.9, 98, True,
             ['pexels-10627100.jpg', 'pexels-38345584.jpg'], ['Sei lezioni da un’ora', 'Il ponte, la mira, l’effetto',
                                                               'Gruppi di quattro', 'Stecca in prestito'],
             'Sei lezioni per tenere la stecca come si deve, mirare, dare l’effetto: in gruppi di quattro.'),
    prodotto('CAR-008', 'Lezione privata col maestro', 'lezione-privata', 'corsi', 45, 0, 20, 4.9, 61, False,
             ['pexels-10627103.jpg', 'pexels-6253920.jpg'], ['Un’ora col maestro', 'Sul vostro gioco', 'Anche lo snooker',
                                                              'Si prenota il giorno'],
             'Un’ora col maestro, sul vostro gioco: il colpo che non viene, la sponda che sbagliate.'),
    prodotto('CAR-009', 'Torneo del giovedì, iscrizione', 'torneo-giovedi', 'corsi', 15, 0, 32, 4.6, 152, False,
             ['pexels-5055413.jpg', 'pexels-37352317.jpg'], ['Pool a eliminazione', 'Trentadue giocatori',
                                                              'Si vince la stecca d’ebano', 'Dalle 21'],
             'Il torneo di pool del giovedì sera: trentadue giocatori, si vince la stecca d’ebano.'),
    prodotto('CAR-010', 'Stecca Acero, due pezzi', 'stecca-acero', 'stecche', 129, 99, 15, 4.7, 176, True,
             ['pexels-37157955.jpg', 'pexels-13089177.jpg'], ['Acero canadese', 'Due pezzi con la vite', 'Puntale da 13 mm',
                                                               '19 once'],
             'La prima stecca vera: acero canadese, due pezzi, il puntale da tredici millimetri.'),
    prodotto('CAR-011', 'Stecca Ebano, intarsiata', 'stecca-ebano', 'stecche', 249, 0, 6, 4.9, 54, False,
             ['pexels-34763004.jpg'], ['Ebano e acero', 'Intarsi in madreperla', 'Due pezzi', 'Nella custodia'],
             'La stecca d’ebano con gli intarsi: quella che si vince al torneo, o si regala.'),
    prodotto('CAR-012', 'Custodia in cuoio per due stecche', 'custodia-cuoio', 'stecche', 89, 0, 10, 4.6, 42, False,
             ['pexels-10627113.jpg'], ['Cuoio e velluto', 'Per due stecche in due pezzi', 'Tracolla', 'Tasca per il gesso'],
             'La custodia di cuoio per portare le stecche al torneo.'),
    prodotto('CAR-013', 'Gesso blu, dodici pezzi', 'gesso-blu', 'accessori', 9, 0, 60, 4.5, 233, False,
             ['pexels-13179841.jpg'], ['Dodici cubetti', 'Blu', 'Non sporca il panno', 'Per pool e carambola'],
             'Il gesso blu che tiene il puntale: dodici cubetti.'),
    prodotto('CAR-014', 'Guanto da biliardo, tre dita', 'guanto-biliardo', 'accessori', 12, 9, 40, 4.6, 120, False,
             ['pexels-13089176.jpg'], ['Lycra traspirante', 'Tre dita', 'Per destri e mancini', 'La stecca scorre'],
             'Il guanto a tre dita: la stecca scorre sul ponte senza attrito.'),
    prodotto('CAR-015', 'Set di palle da pool, sedici pezzi', 'set-palle', 'accessori', 79, 0, 12, 4.8, 88, False,
             ['pexels-11367368.jpg', 'pexels-13036108.jpg', 'pexels-39332572.jpg'], ['Resina fenolica', '57 mm',
                                                                                      'Quindici più la bianca', 'Nella valigetta'],
             'Le sedici palle in resina, come quelle della sala: per il tavolo di casa.'),
    prodotto('CAR-016', 'Triangolo in legno', 'triangolo-legno', 'accessori', 25, 0, 20, 4.5, 37, False,
             ['pexels-13793180.jpg'], ['Faggio', 'Per palle da 57 mm', 'Angoli rinforzati', 'Fatto a mano'],
             'Il triangolo di faggio per stringere le palle come si deve.'),
    prodotto('CAR-017', 'Carta regalo, 50 €', 'carta-regalo-50', 'regali', 50, 0, 999, 4.9, 140, False,
             ['pexels-31916152.jpg'], ['Vale un anno', 'Per tavoli, corsi e bar', 'Arriva per email', 'Si stampa'],
             'Cinquanta euro da spendere in sala: tavoli, corsi, bar.'),
    prodotto('CAR-018', 'Carta regalo: una serata per due', 'carta-serata-due', 'regali', 39, 32, 999, 4.8, 99, False,
             ['pexels-16064240.jpg', 'pexels-1269042.jpg'], ['Due ore di tavolo', 'Due drink', 'Vale un anno',
                                                              'Arriva per email'],
             'Una serata da regalare: due ore di tavolo e due drink.'),
]
RECENSIONI = [[5, 'Tavoli perfetti, panno nuovo, stecche dritte. Si prenota in un minuto.'],
              [5, 'La serata per quattro è stata la cosa migliore del mese.'],
              [4, 'Bellissimo posto, il venerdì è un po’ affollato: prenotate.'],
              [5, 'Il corso base mi ha cambiato il gioco.'],
              [5, 'Ho regalato la carta a mio fratello: ci torniamo ogni settimana.'],
              [4, 'Ottime birre, il tagliere potrebbe essere più grande.'],
              [5, 'Il maestro è pazientissimo.'],
              [3, 'Bel posto, la musica il sabato è un po’ alta.']]
NOMI = ['Riccardo P.', 'Elisa M.', 'Stefano G.', 'Michela T.', 'Alberto R.', 'Chiara N.', 'Giacomo V.', 'Sara L.',
        'Fabio C.', 'Irene B.', 'Nicola D.', 'Veronica F.']
PROMESSE = [('calendar', 'Prenoti ora, giochi stasera'), ('clock', 'Aperti tutti i giorni fino alle 2'),
            ('rotate-ccw', 'Disdetta gratis fino a due ore prima'), ('beer', 'Il bar della sala')]
CONTATTI = [('map-pin', 'Sala · via Vigevano 18, Milano', P['sala'] + '#sala'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'Carambola', icona='circle-dot', ricerca='Cerca un tavolo, un corso, una stecca…',
                     parole='un tavolo per stasera\nil corso base\nuna stecca in acero\nuna serata da regalare',
                     annuncio=('Martedì dello snooker: il tavolo a 14 € l’ora, la disdetta è gratis.', 'Le offerte',
                               P['offerte']), logo_size=28)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'Carambola', 'circle-dot', 'Una sala da biliardo e bar in via Vigevano, sui Navigli: '
                   'dodici tavoli da pool, carambola e snooker, i corsi del maestro, il torneo del giovedì.',
                   [('Giocare', [('Prenota un tavolo', N.cat_url('tavoli')), ('Serate e feste', N.cat_url('serate')),
                                 ('Corsi e tornei', N.cat_url('corsi')), ('Offerte', P['offerte'])]),
                    ('Carambola', [('La sala', P['sala']), ('I corsi', P['corsi']), ('Le stecche', N.cat_url('stecche')),
                                   ('Il mio account', P['account'])]),
                    ('Aiuto', [('Prenotazioni e disdette', P['assistenza'] + '#spedizioni'),
                               ('Le feste', P['assistenza'] + '#scrivici'), ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Carambola s.r.l. · Milano · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera della sala', 'I tornei e le serate, prima.',
                              'Una lettera al mese: il calendario dei tornei, le serate, i tavoli liberi a meno.',
                              'Fatto: la prima lettera arriva col calendario dei tornei.'),
                   social=[('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.6  # la larghezza media di una lettera dei titoli (Alfa Slab One, per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=-10)
    return N.vetrina(t, [
        N.offerta('ca-o1', K.sfondo_video(V_APERTURA, overlay=0.12), 'Martedì dello snooker',
                  'Lo snooker a 14 € l’ora.', 'Il tavolo grande, ventidue palle e le stecche giuste. Il martedì, invece di 18 €.',
                  'Prenota il tavolo', N.cat_url('tavoli'), bollino='−22%', bollino_x=74, nota='Disdetta gratis fino a due ore prima',
                  primo=True, **f),
        N.offerta('ca-o2', K.sfondo_foto('pexels-5055270.jpg', overlay=0.12, scala=1.06), 'Serate · dal giovedì alla domenica',
                  'La serata per quattro a 49 €.', 'Due ore di pool, quattro birre artigianali, patatine e olive. Era 59 €.',
                  'Le serate', N.cat_url('serate'), bollino='−17%', nota='Si prenota anche per stasera', **f),
        N.offerta('ca-o3', K.sfondo_video(V_PARTITA, overlay=0.12), 'Corsi · si comincia lunedì',
                  'Il corso base a 99 €.', 'Sei lezioni col maestro: il ponte, la mira, l’effetto. Era 120 €.',
                  'I corsi', N.cat_url('corsi'), bollino='−18%', bollino_x=74, nota='Stecca in prestito compresa', **f),
    ], transizione='rotateSlide')


# le schede sotto la vetrina: «scuro» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='scuro', voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Prenota un tavolo', {'category': 'tavoli', 'orderby': 'popularity'}, 'Vedi i tavoli', N.cat_url('tavoli')),
        ('Sotto i 20 €', {'orderby': 'price', 'order': 'ASC'}, 'Vedi tutti', P['negozio'] + '?max_price=20'),
        ('I più amati', {'orderby': 'rating'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'La sala', 'Dodici tavoli, un bar, il maestro',
                              ('Un’ora di pool,', '14 €.', 'Stecche e gesso compresi.', 'Prenota un tavolo', N.cat_url('tavoli'),
                               'pexels-8092382.jpg'), tutto='Tutto Carambola')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte', 'Fino al −25%.', 'Quelle di oggi finiscono tra:',
                              ('La stecca Acero,', '99 €.', 'Due pezzi, puntale da 13 mm. Era 129 €.', 'Le stecche',
                               N.cat_url('stecche'), 'pexels-37157955.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'In sala questo mese',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('Il torneo del giovedì,', 'si vince l’ebano.', 'Trentadue giocatori, dalle 21.', 'I tornei',
                              N.cat_url('corsi'), 'pexels-5055413.jpg'), tutte='Tutte le novità')


# ── Il colpo d'apertura: lo slider resta fermo, la stecca tira, la bianca apre il triangolo, le palle si sparpagliano ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1060.0
COLORI_PALLE = ['#F2C230', '#1E4FC2', '#D2302C', '#6A2C91', '#E8762D', '#0E7A3E', '#7A1F2B', '#111111',
                '#F2C230', '#1E4FC2', '#D2302C', '#6A2C91', '#E8762D', '#0E7A3E', '#7A1F2B']
# dove finisce ogni palla dopo l'apertura (spostamento in px sulla tela del computer)
SPARSE = [(-120, -230), (180, -260), (240, 120), (-60, 250), (300, -40), (60, -150), (-200, 160), (120, 60), (330, 230),
          (-260, -60), (200, 300), (20, 180), (-150, -300), (260, -180), (-30, 40)]
ARRIVI = [('Un’ora di pool', 'pexels-10627132.jpg', '14 €', 'tavoli'), ('Serata per quattro', 'pexels-5055270.jpg', '49 €',
                                                                        'serate'),
          ('Corso base', 'pexels-10627100.jpg', '99 €', 'corsi')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def colpo(t):
    m = K.mob
    L = [
        K.livello('co-o', contenuto='Il colpo d’apertura · scorrete', x=6, y=8, w=46, size=13, peso='700', colore=GIALLO,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.6, 'width': 86}}),
        K.livello('co-t', contenuto='Il primo colpo<br>non si scorda.', tag='h2', x=6, y=12, w=46, size=64, peso='400',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(32), 'x': 7, 'y': 4.6, 'width': 86}}),
    ]
    # la bianca e la stecca, a sinistra; il triangolo a destra
    d, dm = 34.0, 26.0
    cx, cy = 380.0, 470.0
    ax, ay = 900.0, 470.0
    k_m = WM / W  # sul telefono la stessa scena, rimpicciolita
    cym_off = 400.0 - cy * k_m

    def geo(x, y, w, h):
        return _pc(x, y, w, h, W, H), _pc(x * k_m, y * k_m + cym_off, w * k_m, h * k_m, WM, HM)
    stecca = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 8, 'x': -50}, {'p': 13, 'x': 30}, {'p': 20, 'x': -10},
                                                  {'p': 30, 'x': -10, 'o': 100}, {'p': 40, 'x': -10, 'o': 0}, {'p': 100, 'o': 0}]}
    a, b = geo(cx - d / 2 - 330, cy - 5, 320, 10)
    L.append(K.livello('co-stecca', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=6, entra='fadeIn', ritardo=0,
                       shapeGradient={'angle': 90, 'from': '#5A3A1C', 'to': '#E8D2A6', 'fromStop': 20}, scrollMove=stecca,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    corsa = ax - d - cx
    bianca = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 13, 'x': 0}, {'p': 22, 'x': corsa},
                                                  {'p': 34, 'x': corsa - 140, 'y': 60}, {'p': 100, 'x': corsa - 140, 'y': 60}]}
    a, b = geo(cx - d / 2, cy - d / 2, d, d)
    L.append(K.livello('co-bianca', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#F8F6EF', borderRadius=999,
                       boxShadow={'x': 0, 'y': 4, 'blur': 8, 'spread': 0, 'color': mix(C['nero'], 50)}, entra='fadeIn', ritardo=0,
                       scrollMove=bianca, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    n = 0
    for r in range(5):
        for i in range(r + 1):
            x = ax + r * d * 0.88
            y = ay + (i - r / 2.0) * (d + 1)
            dx, dy = SPARSE[n]
            sparsa = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0, 'y': 0, 'r': 0}, {'p': 22, 'x': 0, 'y': 0, 'r': 0},
                                                          {'p': 40, 'x': dx, 'y': dy, 'r': dx * 1.5},
                                                          {'p': 100, 'x': dx, 'y': dy, 'r': dx * 1.5}]}
            a, b = geo(x - d / 2, y - d / 2, d, d)
            L.append(K.livello('co-p%d' % n, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=COLORI_PALLE[n],
                               borderRadius=999, borderWidth=2 if n >= 8 else 0, borderColor='#F8F6EF',
                               boxShadow={'x': 0, 'y': 4, 'blur': 8, 'spread': 0, 'color': mix(C['nero'], 45)}, entra='fadeIn',
                               ritardo=0, scrollMove=sparsa,
                               responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
            n += 1
    # dopo l'apertura, i tavoli da prenotare
    cw, ch, cwm, chm = 330.0, 118.0, 560.0, 112.0
    for k, (nome, foto, prezzo, cat) in enumerate(ARRIVI):
        y, ym = 140 + (ch + 18) * k, 600 + (chm + 14) * k
        entra = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 380, 'o': 0}, {'p': 52 + 12 * k, 'x': 380, 'o': 0},
                                                     {'p': 64 + 12 * k, 'x': 0, 'o': 100}, {'p': 100, 'x': 0, 'o': 100}]}
        x0 = W - 70 - cw
        a, b = _pc(x0, y, cw, ch, W, H), _pc(40, ym, cwm, chm, WM, HM)
        L.append(K.livello('co-scheda%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['chiaro'], borderRadius=10,
                           boxShadow={'x': 0, 'y': 20, 'blur': 44, 'spread': -16, 'color': mix(C['nero'], 70)}, entra='fadeIn',
                           ritardo=0, scrollMove=entra, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0 + 10, y + 10, 98, 98, W, H), _pc(50, ym + 10, 92, 92, WM, HM)
        L.append(K.livello('co-foto%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=a[0], y=a[1], w=a[2], h=a[3],
                           borderRadius=6, entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a, b = _pc(x0 + 124, y + 20, 196, 34, W, H), _pc(160, ym + 18, 420, 36, WM, HM)
        L.append(K.livello('co-nome%d' % k, contenuto=nome, x=a[0], y=a[1], w=a[2], size=20, peso='400', colore=C['testo'],
                           fontFamily=TITOLI, entra='fadeIn', ritardo=0, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(16)}}))
        a, b = _pc(x0 + 124, y + 66, 160, 32, W, H), _pc(160, ym + 62, 260, 32, WM, HM)
        L.append(K.livello('co-prezzo%d' % k, tipo='button', contenuto='%s · prenota' % prezzo, x=a[0], y=a[1], w='auto',
                           size=12, peso='700', colore=C['on_acc'], bgColor=C['acc'], padding=8, borderRadius=6, fontFamily=MONO,
                           entra='fadeIn', ritardo=0, buttonUrl=N.cat_url(cat),
                           hover={'bgColor': GIALLO, 'color': C['nero'], 'duration': 200}, scrollMove=entra,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'fontSize': m(10)}}))
    sl = [K.diapositiva('co-1', {'type': 'color', 'image': '', 'video': '', 'color': C['acc'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, L, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1900, scrollStep=False, globalBackground={'type': 'color', 'color': C['acc']},
                       heightMobile={'mode': 'px', 'value': int(HM)})


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small',
                         style={'margin_top': 24}))
    out.append(t.section([
        N.testata_sezione(t, 'Carambola', 'Cosa vi va stasera?', P['negozio'], 'Tutto Carambola'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, colpo(t)))
    out.append(t.section([
        N.testata_sezione(t, 'Prenota un tavolo', 'Pool, carambola, snooker', N.cat_url('tavoli')),
        t.one([N.anteprima(t), N.prodotti(t, category='tavoli,serate', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Il torneo del giovedì,', 'si vince l’ebano.', 'Trentadue giocatori, iscrizione 15 €.',
                               'I tornei', N.cat_url('corsi'), K.photo_bg('pexels-5055413.jpg'), size=34)],
                     wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Il bar della sala,', 'fino alle due.', 'Birre artigianali, cocktail, il tagliere.',
                               'Aperitivo e partita', N.cat_url('serate'), K.photo_bg('pexels-27897615.jpg'), size=34)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte della settimana', color=GIALLO),
                      N.titolo(t, 'Finiscono domenica.', size=44, color=C['chiaro']),
                      N.testo(t, 'Lo snooker del martedì, la serata per quattro, il corso base, la stecca in acero.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42, giorni=True), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left',
                                                              pad_top=16)], wm='1-2', ws='1-1')],
              layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_SALA, 'La sala', 'Dodici tavoli, <em>la luce bassa.</em>',
                             'Le lampade verdi sopra ogni tavolo, il bar in fondo, il maestro che passa e corregge il ponte. '
                             'Aperti tutti i giorni fino alle due.', overlay=40,
                             extra=[N.pulsante(t, 'La sala', P['sala'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Il negozio', 'Stecche, palle, gesso', N.cat_url('stecche')),
        t.one([N.prodotti(t, category='stecche,accessori', posts_per_page=4, columns=4, orderby='rating')]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-10627100.jpg', 'Un giocatore concentrato prende la mira', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Il maestro'), N.titolo(t, 'Il ponte giusto<br>in sei lezioni.', size=48),
                      N.testo(t, 'Il corso base comincia ogni lunedì: sei lezioni in gruppi di quattro, la stecca in prestito. '
                                 'Poi il torneo del giovedì, se vi va.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Gruppi di quattro', 'Stecca in prestito', 'Il torneo del giovedì per provare']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'I corsi', P['corsi'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Carambola', 'Tavoli, serate, corsi e negozio', 'Si prenota il tavolo a ore, la serata, il corso; '
                            'nel negozio le stecche e tutto per il tavolo di casa.', colonne=3,
                            banner=N.banner(t, 'Una serata', 'da regalare.', 'Due ore di tavolo e due drink, per un anno.',
                                            'Le carte regalo', N.cat_url('regali'), K.photo_bg('pexels-16064240.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Il guanto,', '9 €.', 'Tre dita, la stecca scorre. Era 12 €.', 'Gli accessori',
                        N.cat_url('accessori'), K.photo_bg('pexels-13089176.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'La serata per due,', '32 €.', 'Da regalare, vale un anno. Era 39 €.', 'Le carte regalo',
                        N.cat_url('regali'), K.photo_bg('pexels-1269042.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Le offerte', 'Fino al −25%, fino a domenica', 'Lo snooker del martedì, la serata per quattro, il '
                            'corso base, la stecca in acero e le carte regalo a meno.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('calendar', 'Prenoti ora, giochi stasera'),
                                                   ('rotate-ccw', 'Disdetta gratis fino a due ore prima'),
                                                   ('truck', 'Il negozio spedisce in due giorni'),
                                                   ('gift', 'Si regala con la carta')],
                             bundle='Con questo, a biliardo', miniature='left', altezza='560px', bottone='Prenota')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'La disdetta è gratis fino a due ore prima. Il negozio spedisce in due giorni.',
                             PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati e il pagamento. Nelle note: il giorno e l’ora del tavolo.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Le prenotazioni, i corsi, le carte regalo, gli ordini del negozio.',
                            ('Cambiare l’ora?', 'Scriveteci o chiamate: fino a due ore prima è gratis.', 'Scrivici',
                             P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'In sala tutti i giorni dalle 15 alle 2; al telefono e in chat.', [
        ('calendar', 'Prenotazioni', 'Il tavolo è vostro all’ora scelta.', 'spedizioni'),
        ('rotate-ccw', 'Disdette', 'Gratis fino a due ore prima.', 'spedizioni'),
        ('party-popper', 'Feste', 'Compleanni, aziende, addii al celibato.', 'scrivici'),
        ('truck', 'Il negozio', 'Stecche e accessori in due giorni.', 'spedizioni')], [
        ('Posso disdire?', 'Sì, gratis fino a due ore prima: vi rimborsiamo tutto.'),
        ('E se arrivo in ritardo?', 'Il tavolo vi aspetta un quarto d’ora; poi lo diamo a chi aspetta, ma l’ora resta vostra '
                                    'per un altro giorno.'),
        ('Serve saper giocare?', 'No: il maestro passa e vi dà una mano, e c’è il corso base.'),
        ('Fate feste aziendali?', 'Sì, fino a quaranta persone, con tutti i tavoli e il buffet: scriveteci.')], CONTATTI,
        campi_extra='Una festa\nUn corso\n', foto=('pexels-8092382.jpg', 'La sala coi tavoli e le lampade'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa palla <em>è finita in buca.</em>', 'La pagina non c’è più. Cercate qui sopra.',
                        K.video_bg(V_VERDE, overlay=40))


def build_sala():
    t = Tpl(ID, 'sala')
    out = [K.intro(t, 'La sala', ['Dodici tavoli,', 'la luce bassa.'], 'In via Vigevano dal 1968: otto tavoli da pool, '
                   'due da carambola, due da snooker, il bar in fondo.', bg=K.video_bg(V_PARTITA, overlay=40), size=86)]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'I tavoli'), N.titolo(t, 'Panno nuovo<br>ogni anno.', size=46),
                      N.testo(t, 'Cambiamo il panno ogni gennaio e livelliamo i tavoli ogni mese: la palla va dove la mandate. '
                                 'Stecche, gesso e triangolo sono compresi nell’ora.', size=17)], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-8092382.jpg', 'La sala coi tavoli e la moquette', ratio='4/3')], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], html_id='sala', padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 64}))
    out.append(t.section([
        t.one([K.numeri(t, [('12', 'tavoli'), ('1968', 'la sala apre'), ('2', 'di notte, si chiude'), ('15 €', 'il torneo')],
                        colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-4485382.jpg', 'Il barista versa un cocktail al bancone', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Il bar'), N.titolo(t, 'Birre di Lambrate,<br>cocktail classici.', size=46),
                      N.testo(t, 'Sei birre alla spina di un birrificio di quartiere, i cocktail classici, il tagliere di salumi '
                                 'e formaggi. Si porta al tavolo, sul ripiano apposta: niente bicchieri sul panno.', size=17)],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    out.append(t.section([
        N.testata_sezione(t, 'Prenota', 'I tavoli e le serate', N.cat_url('tavoli')),
        t.one([N.prodotti(t, category='tavoli,serate', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_corsi():
    t = Tpl(ID, 'corsi')
    out = [N.intestazione(t, 'Corsi e tornei', 'Dal ponte al torneo.', 'Il corso base ogni lunedì, le lezioni private col maestro, '
                          'il torneo di pool ogni giovedì.', scuro=True)]
    passi = [('hand', 'Il ponte', 'La mano che regge la stecca: chiuso, aperto, sulla sponda.'),
             ('crosshair', 'La mira', 'Dove colpire la bianca per mandare la palla in buca.'),
             ('rotate-cw', 'L’effetto', 'Colpire alto, basso, di lato: la bianca torna, si ferma, gira.'),
             ('trophy', 'La partita', 'Le regole del pool e della carambola, e il primo torneo.')]
    out.append(t.section([
        N.testata_sezione(t, 'Il corso base', 'Sei lezioni, quattro cose'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in passi],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 72, 'padding_bottom': 56}))
    out.append(K.video_frase(t, V_TRIANGOLO, 'Il torneo', 'Il giovedì, <em>dalle ventuno.</em>',
                             'Trentadue giocatori a eliminazione diretta: chi vince si porta a casa la stecca d’ebano.', overlay=40))
    out.append(t.section([
        N.testata_sezione(t, 'Iscriviti', 'Corsi, lezioni, tornei', N.cat_url('corsi')),
        t.one([N.prodotti(t, category='corsi', posts_per_page=4, columns=3)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'Il colpo d’apertura',
    ['biliardo', 'sala giochi', 'bar', 'woocommerce', 'e-commerce', 'prenotazioni', 'milano', 'corsi'],
    'Il negozio online di una sala da biliardo e bar sui Navigli: in apertura la fascia delle offerte che scorre e le quattro '
    'schede, il mega menu con la sala, le offerte e le novità; a metà pagina il colpo d’apertura, con la stecca che tira, la '
    'bianca che apre il triangolo e le palle che si sparpagliano; i tavoli a ore con la fascia, le serate, i corsi e il torneo, '
    'il negozio delle stecche.',
    [('Carambola', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Tavoli', '{{PAGINA:negozio}}?product_cat=tavoli'), ('Serate', '{{PAGINA:negozio}}?product_cat=serate'),
     ('Stecche', '{{PAGINA:negozio}}?product_cat=stecche'), ('La sala', P['sala']), ('I corsi', P['corsi'])],
    [('home', 'Home', None), ('negozio', 'Carambola', 'prenota'), ('sala', 'La sala', 'la-sala'), ('corsi', 'Corsi e tornei', 'corsi'),
     ('offerte', 'Le offerte', 'offerte'), ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'),
     ('carrello', 'Il carrello', 'carrello'), ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Barlow\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Alfa Slab One\', Georgia, serif',
    '\'Space Mono\', ui-monospace, Menlo, monospace', ['Alfa Slab One', 'Barlow', 'Space Mono'], corsivo_di=[],
    peso_titoli='400', raggio=8, grana=0, titoli_line='1.04', titoli_spazio='0')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Carambola': ('pannello-reparti', 'Pannello della sala'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'stecca'
THEME['cursor']['ring_color'] = HEX['secondary']  # il colore del cursore (sul fondo chiaro il chiaro non si vede)


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'sala.json': build_sala(), 'corsi.json': build_corsi(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'fascia': {'nome': 'Fascia oraria', 'termini': FASCE}},
                    dentro='La conferma con il tavolo e l’ora, o la stecca nella sua custodia.')


if __name__ == '__main__':
    main()
