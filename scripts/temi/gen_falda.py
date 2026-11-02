# -*- coding: utf-8 -*-
"""Genera il tema «Falda» (id falda): cappelli e ombrelli in via San Nicolò, a Trieste — fedora e feltri, paglie, coppole e
baschi, cuffie e sciarpe, ombrelli antivento; in bottega si prende la misura e si rimettono in forma i cappelli.

Tema chiaro color nebbia: il petrolio del mare d'inverno sui pulsanti, il giallo dell'impermeabile sui bollini, il bordeaux
del feltro negli accenti. Titoli in Fraunces (morbido, col corsivo vero), testo in Jost, prezzi e misure in Red Hat Mono.

Apertura: la vetrina delle offerte, bassa, che scorre di lato come col vento (slide): l'ombrello antivento, il fedora in
feltro, la coppola nuova; sotto le quattro schede coi prodotti veri; il mega menu apre il negozio, le offerte e le novità.
A metà pagina «il barometro»: lo slider resta fermo mentre si scorre, l'ago passa da sole a bora, pioggia e neve, il cielo
cambia colore, passano il vento, la pioggia e i fiocchi, e accanto cambia la foto col cappello (o l'ombrello) per quel tempo.
Cursore: l'ombrello, che il vento del movimento inclina e che sui link si chiude a metà.

WooCommerce: il negozio coi filtri, la scheda con la galleria (il video della bottega) e la misura (dal 55 al 60), il
carrello, la cassa a passi, l'account, i preferiti; la guida alla misura, la bottega con la prenotazione.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'falda'
NAME = 'Falda'

HEX = {
    'primary': '#0E5A6B', 'primary_contrast': '#FFFFFF', 'secondary': '#E9B824', 'secondary_contrast': '#1B2226',
    'accent': '#7A2E2E', 'dark': '#162026', 'light': '#FFFFFF', 'muted': '#E9ECEA', 'muted_contrast': '#162026',
    'text': '#162026', 'text_muted': '#55626A', 'background': '#F5F6F3', 'border': '#D6DCD8', 'link': '#0E5A6B',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'misure', 'bottega', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'bottega@falda.example', '+39 040 000 2121', 'tel:+390400002121'

V_BOTTEGA, V_PROVA, V_SCAFFALE, V_PIOGGIA, V_NEON, V_PARCO, V_ASFALTO, V_PASSEGGIO, V_CUFFIA, V_PORTA = (
    'pexels-video-5699613', 'pexels-video-5699555', 'pexels-video-5699564', 'pexels-video-6302989', 'pexels-video-31365792',
    'pexels-video-8323023', 'pexels-video-35209563', 'pexels-video-7820808', 'pexels-video-7578133', 'pexels-video-6975425')
VIDEOS = [V_BOTTEGA, V_PROVA, V_SCAFFALE, V_PIOGGIA, V_NEON, V_PARCO, V_ASFALTO, V_PASSEGGIO, V_CUFFIA, V_PORTA]
PHOTOS = ['pexels-%s.jpg' % n for n in (
    7578351, 7467786, 5699021, 5698920, 5699024, 5698983, 18856742, 39275404, 18856744, 4738060, 13184781, 38339999, 38339998,
    38689798, 29210958, 36551109, 8879828, 9064213, 30049764, 13960885, 32801810, 12077063, 10162823, 15906279, 15906278,
    18055802, 20900508, 17574569, 31108773, 6975408, 6069956, 9251126, 30404716, 16115837, 5623700, 6616097, 6667981, 6667921,
    6616146, 7585611, 34311307, 15171377, 19116705, 19116710, 30504807, 5358118, 13850641, 5927565, 14363140, 35159195, 5051802,
    12708083, 30617273, 8465590, 32677095, 15582713, 31215155, 19116812, 30598059, 32442106, 10206432, 32442108, 5699094,
    5699100, 5699025, 5699022, 5698918, 5699061, 5698915, 37578220, 5699055, 40412495, 12551957, 5699051, 10453034, 3757706,
    6633534, 33041884, 6617758, 19346997, 6633369, 3738699, 6500175)]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='500', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
GIALLO, FELTRO = C['sec'], C['acc2']
TITOLI, MONO = 'Fraunces', 'Red Hat Mono'
N = Negozio(K, NAME, P, raggio=10, ratio='4-5', card='border', hover='swap', testata_scura=False,
            consegna='Spedizione in 24 ore, cambio misura gratis', evidenza='Il più provato', marca='', peso_titoli='500')

CATEGORIE = [
    ('feltri', 'Fedora e feltri', 'Il nero, il cammello, la tesa larga.', 'pexels-5698920.jpg'),
    ('paglia', 'Paglie', 'Il panama, la paglietta, la rafia.', 'pexels-39275404.jpg'),
    ('coppole', 'Coppole e baschi', 'Tweed, lana cotta, il basco rosso.', 'pexels-37578220.jpg'),
    ('lana', 'Cuffie e sciarpe', 'Merino e lana, per la bora.', 'pexels-3738699.jpg'),
    ('ombrelli', 'Ombrelli', 'Antivento, da città, trasparenti.', 'pexels-15171377.jpg'),
]
MISURE = ['55', '56', '57', '58', '59', '60']


def articolo(sku, nome, slug, cat, prezzo, sconto, voto, rec, evid, foto, punti, testo, misura=False, **kw):
    if misura:
        kw['varianti'] = ('misura', MISURE, ['60'] if sku in ('FAL-004', 'FAL-008') else [])
    return prodotto(sku, nome, slug, cat, prezzo, sconto, 30, voto, rec, evid, ['pexels-%s.jpg' % f for f in foto], punti, testo,
                    **kw)


PRODOTTI = [
    articolo('FAL-001', 'Fedora in feltro di lana, nero', 'fedora-nero', 'feltri', 89, 75, 4.9, 312, True, [5699021, 7578351],
             ['Feltro di lana, 100 grammi', 'Tesa 6 cm', 'Nastro gros-grain', 'Si rimette in forma, gratis'],
             'Il fedora nero di tutti i giorni: feltro di lana, tesa da sei centimetri, il nastro gros-grain.', misura=True,
             video=V_BOTTEGA, cross=['FAL-015', 'FAL-017']),
    articolo('FAL-002', 'Fedora in feltro, cammello', 'fedora-cammello', 'feltri', 95, 0, 4.8, 166, False, [5698920, 5698983],
             ['Feltro di lana', 'Colore cammello', 'Nastro tono su tono', 'Fodera in raso'],
             'Il cammello che sta bene col cappotto blu e con quello grigio: feltro di lana, nastro tono su tono.', misura=True),
    articolo('FAL-003', 'Cappello a tesa larga, grigio perla', 'tesa-larga-grigio', 'feltri', 110, 0, 4.7, 74, False,
             [6975408, 18856744], ['Feltro di pelo di coniglio', 'Tesa 8 cm', 'Nastro nero', 'Scatola cappelliera'],
             'Il feltro più morbido, di pelo, con la tesa da otto centimetri: arriva nella cappelliera.', misura=True,
             video=V_PORTA),
    articolo('FAL-004', 'Cloche in feltro, nero', 'cloche-nero', 'feltri', 79, 0, 4.7, 98, False, [9251126, 6069956],
             ['Feltro di lana', 'Tesa corta e morbida', 'Si piega di lato', 'Per le teste piccole'],
             'La cloche degli anni Venti, con la tesa corta che si piega di lato.', misura=True),
    articolo('FAL-005', 'Cappello in feltro, bordeaux', 'feltro-bordeaux', 'feltri', 85, 0, 4.8, 121, False,
             [16115837, 30404716], ['Feltro di lana', 'Colore bordeaux', 'Nastro in tinta', 'Tesa 7 cm'],
             'Il bordeaux del feltro, quello del nostro nome: per il cappotto cammello.', misura=True, video=V_PASSEGGIO),
    articolo('FAL-006', 'Panama con la fascia nera', 'panama-fascia-nera', 'paglia', 120, 0, 4.9, 143, True, [39275404, 7467786],
             ['Paglia toquilla intrecciata a mano', 'Fascia nera', 'Si arrotola nel tubo', 'Dall’Ecuador'],
             'Il panama vero, intrecciato a mano in Ecuador, con la fascia nera: si arrotola nel tubo per il viaggio.',
             misura=True),
    articolo('FAL-007', 'Paglia a tesa larga', 'paglia-tesa-larga', 'paglia', 59, 45, 4.7, 266, True, [4738060, 9064213],
             ['Paglia naturale', 'Tesa 11 cm', 'Laccio sotto il mento', 'Per il mare e la campagna'],
             'La tesa da undici centimetri che fa ombra davvero, col laccio per la bora d’estate.', misura=True),
    articolo('FAL-008', 'Paglietta col nastro', 'paglietta-nastro', 'paglia', 69, 0, 4.6, 88, False, [36551109, 29210958],
             ['Paglia rigida', 'Cupola piatta', 'Nastro a righe', 'Per le domeniche'],
             'La paglietta rigida dei gondolieri e delle regate, col nastro a righe.', misura=True),
    articolo('FAL-009', 'Cappello da spiaggia in rafia', 'rafia-spiaggia', 'paglia', 39, 0, 4.5, 154, False, [8879828, 38689798],
             ['Rafia intrecciata', 'Si piega in borsa', 'Tesa 10 cm', 'Taglia unica'],
             'La rafia che si piega in borsa e torna come prima: per Barcola.'),
    articolo('FAL-010', 'Coppola di tweed', 'coppola-tweed', 'coppole', 59, 0, 4.9, 287, True, [15906278, 18055802],
             ['Tweed di lana', 'Cucita a Trieste', 'Fodera trapuntata', 'Non vola con la bora'],
             'La coppola di tweed cucita qui: aderisce alla testa, con la bora non vola.', misura=True),
    articolo('FAL-011', 'Coppola in lana cotta, grigia', 'coppola-grigia', 'coppole', 49, 0, 4.7, 131, False,
             [31108773, 37578220], ['Lana cotta', 'Grigio melange', 'Visiera corta', 'Leggera'],
             'La lana cotta grigia, più leggera del tweed, con la visiera corta.', misura=True),
    articolo('FAL-012', 'Basco di lana, rosso', 'basco-rosso', 'coppole', 35, 28, 4.8, 203, False, [12077063, 32801810],
             ['Lana merino follata', 'Rosso pieno', 'Taglia unica', 'Si porta di lato'],
             'Il basco rosso di lana follata: si porta di lato, col cappotto scuro.'),
    articolo('FAL-013', 'Cuffia a coste, ocra', 'cuffia-ocra', 'lana', 29, 0, 4.7, 244, False, [3738699, 5623700],
             ['Lana merino', 'Coste larghe', 'Risvolto', 'Taglia unica'],
             'La cuffia di merino a coste, col risvolto: l’ocra si vede anche nella nebbia.', video=V_CUFFIA),
    articolo('FAL-014', 'Cuffia di merino, nera', 'cuffia-nera', 'lana', 32, 0, 4.8, 176, False, [34311307, 7585611],
             ['Merino extrafine', 'Non punge', 'Senza risvolto', 'Taglia unica'],
             'Il merino extrafine che non punge, nero, senza risvolto.'),
    articolo('FAL-015', 'Sciarpa di merino, azzurra', 'sciarpa-azzurra', 'lana', 45, 0, 4.8, 152, False, [10453034, 6633534],
             ['Merino', '180 × 30 cm', 'Frange corte', 'Azzurro del golfo'],
             'L’azzurro del golfo d’inverno, in merino: un metro e ottanta da girare due volte.'),
    articolo('FAL-016', 'Sciarpa a quadri', 'sciarpa-quadri', 'lana', 55, 44, 4.7, 119, False, [3757706, 6633369],
             ['Lana d’agnello', 'Quadri scozzesi', '190 × 35 cm', 'Frange lunghe'],
             'I quadri in lana d’agnello, larghi e caldi: per le mattine di bora scura.'),
    articolo('FAL-017', 'Ombrello antivento, petrolio', 'ombrello-antivento', 'ombrelli', 39, 29, 4.9, 498, True,
             [15171377, 5358118], ['Stecche in fibra di vetro', 'Si rovescia e torna', 'Apertura automatica',
                                   'Collaudato con la bora a 100'],
             'L’ombrello che si rovescia con la raffica e torna com’era: collaudato in molo Audace.', video=V_PARCO),
    articolo('FAL-018', 'Ombrello da città, manico in legno', 'ombrello-citta', 'ombrelli', 69, 0, 4.8, 141, False,
             [13850641, 30504807], ['Manico in castagno curvato', 'Otto stecche d’acciaio', 'Tela antigoccia', 'Puntale in ottone'],
             'Il bastone da città: il manico in castagno curvato a vapore, otto stecche, il puntale d’ottone.', video=V_NEON),
    articolo('FAL-019', 'Ombrello trasparente a cupola', 'ombrello-trasparente', 'ombrelli', 25, 0, 4.6, 233, False,
             [32442106, 10206432], ['A cupola, scende sulle spalle', 'Si vede attraverso', 'Manico a uncino', 'Per la città'],
             'La cupola trasparente che scende sulle spalle: si vede dove si va.'),
    articolo('FAL-020', 'Ombrello pieghevole a fiori', 'ombrello-fiori', 'ombrelli', 22, 0, 4.5, 167, False,
             [31215155, 12708083], ['Pieghevole, 24 cm chiuso', 'Fodero', 'Tela a fiori', 'Per la borsa'],
             'Ventiquattro centimetri chiuso, nel suo fodero: in borsa per quando cambia il tempo.'),
]
RECENSIONI = [[5, 'Il fedora calza perfetto, la misura era quella giusta.'],
              [5, 'L’ombrello antivento ha retto la bora sul molo. Incredibile.'],
              [4, 'Bellissimo, ma la scatola cappelliera è arrivata un po’ ammaccata.'],
              [5, 'La coppola non vola, finalmente.'],
              [5, 'Mi hanno rimesso in forma il cappello del nonno.'],
              [4, 'Ottima sciarpa, il colore dal vivo è un po’ più scuro.'],
              [5, 'Il panama arrotolato nel tubo è tornato perfetto.'],
              [3, 'Bello, ma la 57 per me era stretta: l’ho cambiata gratis.']]
NOMI = ['Marco D.', 'Giulia S.', 'Lorenzo P.', 'Francesca R.', 'Tommaso B.', 'Beatrice C.', 'Edoardo F.', 'Aurora M.',
        'Leonardo T.', 'Sofia G.', 'Mattia V.', 'Greta L.']
PROMESSE = [('truck', 'Spedizione gratuita da 50 €'), ('ruler', 'Cambio misura gratis'),
            ('package', 'Nella cappelliera, senza schiacciarlo'), ('rotate-ccw', 'Resi in 30 giorni')]
CONTATTI = [('map-pin', 'Falda · via San Nicolò 21, Trieste', P['bottega'] + '#dove'), ('phone', PHONE, PHONE_TEL),
            ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'FALDA', icona='umbrella', ricerca='Cerca un fedora, una coppola, un ombrello…',
                     parole='un fedora nero\nla coppola di tweed\nun ombrello antivento\nla sciarpa di merino',
                     annuncio=('Arriva la bora: l’ombrello antivento a 29 €, fino a domenica.', 'Le offerte', P['offerte']),
                     logo_size=28)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'FALDA', 'umbrella', 'Cappelli e ombrelli in via San Nicolò, a due passi dal Canal Grande '
                   'di Trieste: in bottega si prende la misura, si rimettono in forma i feltri, si provano gli ombrelli col '
                   'ventilatore.',
                   [('Il negozio', [('Fedora e feltri', N.cat_url('feltri')), ('Coppole e baschi', N.cat_url('coppole')),
                                    ('Ombrelli', N.cat_url('ombrelli')), ('Offerte', P['offerte'])]),
                    ('Falda', [('La bottega', P['bottega']), ('Prendere la misura', P['misure']), ('I preferiti', P['preferiti']),
                               ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni', P['assistenza'] + '#spedizioni'), ('Cambio misura', P['misure'] + '#cambio'),
                               ('Scrivici', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Falda s.r.l. · Trieste · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('Il bollettino di Falda', 'Quando arriva la bora, ve lo diciamo.',
                              'Le previsioni della settimana, il cappello giusto, gli sconti dei giorni di pioggia.',
                              'Fatto: il primo bollettino arriva col prossimo cambio di tempo.'),
                   social=[('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')])


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
LARGO = 0.5  # la larghezza media di una lettera dei titoli (Fraunces, per la vetrina)


def vetrina(t):
    # largo: la larghezza media delle lettere di ogni titolo, misurata col carattere vero (spaziatura compresa)
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO, bollino_giro=8)
    return N.vetrina(t, [
        N.offerta('fa-o1', K.sfondo_video(V_PIOGGIA, overlay=0.12), 'Arriva la bora · fino a domenica',
                  'L’ombrello antivento a 29 €.', 'Si rovescia con la raffica e torna com’era: collaudato in molo Audace. Era 39 €.',
                  'Gli ombrelli', N.cat_url('ombrelli'), bollino='−25%', nota='Spedizione gratuita da 50 €', primo=True, **dict(f, largo=0.455)),
        N.offerta('fa-o2', K.sfondo_video(V_BOTTEGA, overlay=0.12), 'I feltri d’inverno',
                  'Il fedora nero a 75 €.', 'Feltro di lana, tesa da sei centimetri, il nastro gros-grain. Era 89 €.',
                  'Fedora e feltri', N.cat_url('feltri'), bollino='−15%', bollino_x=80, nota='Cambio misura gratis', **dict(f, largo=0.424)),
        N.offerta('fa-o3', K.sfondo_foto('pexels-37578220.jpg', overlay=0.12, scala=1.06), 'Novità · la coppola',
                  'Il tweed cucito a Trieste.', 'Sei tweed nuovi per la coppola che con la bora non vola: 59 €.',
                  'Coppole e baschi', N.cat_url('coppole'), bollino='Nuova', nota='Si prova in bottega', **dict(f, largo=0.424)),
    ], transizione='slide')


# le schede sotto la vetrina: «promo» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='promo',
                    promo=('Cento ombrelli,', 'uno per ogni tempo.', 'Antivento, da città, trasparenti, a fiori: da 22 €.', 'Gli ombrelli', N.cat_url('ombrelli'), 'pexels-5051802.jpg'), voci=[
        ('Le offerte della settimana', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('Fedora e feltri', {'category': 'feltri', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('feltri')),
        ('Per la pioggia', {'category': 'ombrelli', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('ombrelli')),
        ('I più provati', {'orderby': 'popularity'}, 'Vedi tutti', P['negozio']),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Il negozio', 'Dalla testa all’ombrello',
                              ('La coppola,', '59 €.', 'Il tweed cucito a Trieste.', 'Coppole e baschi', N.cat_url('coppole'),
                               'pexels-15906278.jpg'), tutto='Tutto il negozio')


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Le offerte della settimana', 'Fino a domenica.',
                              'Le offerte della settimana finiscono tra:',
                              ('L’ombrello antivento,', '29 €.', 'Si rovescia e torna. Era 39 €.', 'Ombrelli', N.cat_url('ombrelli'),
                               'pexels-15171377.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Arrivati questo mese',
                             [(n, N.cat_url(s)) for s, n, _, _ in CATEGORIE],
                             ('Il bordeaux', 'del feltro.', 'Il cappello col colore del nostro nome, 85 €.', 'Fedora e feltri',
                              N.cat_url('feltri'), 'pexels-16115837.jpg'), tutte='Tutto il negozio')


# ── Il barometro: lo slider resta fermo, l'ago passa da sole a bora, pioggia e neve; cambiano il cielo e il cappello ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1240.0
DX, DY, R = 790.0, 470.0, 220.0       # il quadrante sul computer: centro e raggio
DXM, DYM, KM = 320.0, 470.0, 0.86     # sul telefono
TEMPI = [
    # parola, angolo dell'ago, pressione, prodotto, prezzo, nota, foto, cielo, finestra (entra, pieno, fino a, esce)
    ('Sole.', -60, '1026 hPa · sereno', 'La paglia a tesa larga', '45 €', 'Ventiquattro gradi e niente vento: la tesa larga fa ombra.',
     'pexels-9064213.jpg', mix('#D98B2B', 30, C['nero']), (0, 0, 18, 24)),
    ('Bora.', -20, '1009 hPa · raffiche a 90', 'La coppola di tweed', '59 €', 'Aderisce alla testa: con la bora non vola.',
     'pexels-15906278.jpg', mix(C['acc'], 34, C['nero']), (18, 26, 42, 48)),
    ('Pioggia.', 20, '998 hPa · pioggia', 'L’ombrello antivento', '29 €', 'Le stecche in fibra si piegano col vento e tornano.',
     'pexels-5358118.jpg', C['nero'], (42, 50, 66, 72)),
    ('Neve.', 60, '1014 hPa · neve sul Carso', 'Cuffia e sciarpa di merino', '77 €', 'Il merino extrafine che non punge.',
     'pexels-3738699.jpg', mix('#9FB6CC', 24, C['nero']), (66, 74, 100, 100)),
]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def _quadrante(dx, dy, w, h):
    """Un riquadro dato dal centro rispetto al centro del quadrante: sul computer e sul telefono."""
    a = _pc(DX + dx - w / 2, DY + dy - h / 2, w, h, W, H)
    b = _pc(DXM + dx * KM - w * KM / 2, DYM + dy * KM - h * KM / 2, w * KM, h * KM, WM, HM)
    return a, b


def _finestra(f, extra=None):
    """Le tappe dell'opacità per un tempo: entra, pieno, fino a, esce."""
    e, p, q, u = f
    ex = extra or {}
    t = [dict({'p': 0, 'o': 100 if p == 0 else 0}, **ex)]
    if p > 0:
        t += [dict({'p': e, 'o': 0}, **ex), dict({'p': p, 'o': 100}, **ex)]
    t.append(dict({'p': q, 'o': 100}, **ex))
    if u < 100:
        t += [dict({'p': u, 'o': 0}, **ex), dict({'p': 100, 'o': 0}, **ex)]
    else:
        t.append(dict({'p': 100, 'o': 100}, **ex))
    return {'inizio': 0, 'fine': 100, 'tappe': t}


def _testo(f):
    """La finestra dei testi: escono prima che entri il successivo (non si sovrappongono mai)."""
    e, p, q, u = f
    return (0 if p == 0 else p - 4, p, q, 100 if u == 100 else q + 4)


def barometro(t):
    import math
    m = K.mob
    L = []
    # i cieli: quattro fondi che si danno il cambio
    for k, tempo in enumerate(TEMPI):
        L.append(K.livello('ba-cielo%d' % k, tipo='shape', x=0, y=0, w=100, h=100, bgColor=tempo[7], borderRadius=0, entra='fadeIn',
                           ritardo=0, scrollMove=_finestra(tempo[8])))
    # il vento della bora: righe che attraversano; la pioggia: righe che cadono; la neve: fiocchi
    for k, (y, x0) in enumerate([(4, 4), (8, 20), (46, 42), (51, 48), (88, 6), (92, 30), (96, 12)]):
        vento = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': -900, 'o': 0}, {'p': 20 + k, 'x': -900, 'o': 0},
                                                     {'p': 28 + k, 'x': -100 if x0 > 40 else -200, 'o': 70},
                                                     {'p': 40 + k, 'x': 500, 'o': 70},
                                                     {'p': 47 + k, 'x': 1100, 'o': 0}, {'p': 100, 'x': 1100, 'o': 0}]}
        L.append(K.livello('ba-vento%d' % k, tipo='shape', x=x0, y=y, w=18 + (k % 3) * 6, h=0.4,
                           bgColor=mix(C['chiaro'], 55), borderRadius=999, entra='fadeIn', ritardo=0, scrollMove=vento,
                           responsive={'mobile': {'height': 0.25, 'width': 30 + (k % 3) * 8}}))
    for k in range(16):
        x = 3 + (k * 37) % 94
        cade = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': -700, 'x': 0, 'o': 0}, {'p': 44, 'y': -700, 'x': 0, 'o': 0},
                                                    {'p': 50, 'y': -300 + (k % 4) * 60, 'x': -30, 'o': 60},
                                                    {'p': 68, 'y': 500 + (k % 4) * 60, 'x': -110, 'o': 60},
                                                    {'p': 72, 'y': 800, 'x': -130, 'o': 0}, {'p': 100, 'y': 800, 'x': -130, 'o': 0}]}
        L.append(K.livello('ba-goccia%d' % k, tipo='shape', x=x, y=(k * 23) % 60, w=0.2, h=7, bgColor=mix(C['chiaro'], 50),
                           borderRadius=999, entra='fadeIn', ritardo=0, scrollMove=cade,
                           responsive={'mobile': {'width': 0.4, 'height': 4}}))
    for k in range(18):
        x = 2 + (k * 29) % 95
        dim = 6 + (k % 3) * 3
        fiocco = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': -500, 'x': 0, 'o': 0}, {'p': 68, 'y': -500, 'x': 0, 'o': 0},
                                                      {'p': 76, 'y': -260 + (k % 5) * 40, 'x': 20, 'o': 90},
                                                      {'p': 100, 'y': 300 + (k % 5) * 60, 'x': -30 + (k % 3) * 30, 'o': 90}]}
        a = _pc(x / 100 * W, (k * 31) % 50 / 100 * H, dim, dim, W, H)
        L.append(K.livello('ba-fiocco%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['chiaro'], borderRadius=999,
                           entra='fadeIn', ritardo=0, scrollMove=fiocco,
                           responsive={'mobile': {'width': round(dim * 1.6 / WM * 100, 2), 'height': round(dim * 1.6 / HM * 100, 2)}}))
    # il titolo
    L += [
        K.livello('ba-o', contenuto='Il barometro · scorrete', x=6, y=10, w=36, size=13, peso='600', colore=GIALLO, letterSpacing=3,
                  textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 7, 'y': 2.2, 'width': 86}}),
        K.livello('ba-t', contenuto='Che tempo fa<br><em>a Trieste?</em>', tag='h2', x=6, y=14.5, w=36, size=56, peso='500',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromBottom', ritardo=200, durata=1100,
                  responsive={'mobile': {'fontSize': m(30), 'x': 7, 'y': 4.2, 'width': 86}}),
    ]
    # il quadrante: il fondo, le tacche, le quattro parole, la pressione, l'ago e il perno
    a, b = _quadrante(0, 0, 2 * R, 2 * R)
    L.append(K.livello('ba-quadrante', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#F2ECDE', borderRadius=999,
                       borderWidth=12, borderColor='#B08D57',
                       boxShadow={'x': 0, 'y': 24, 'blur': 50, 'spread': -12, 'color': mix(C['nero'], 80)}, entra='zoomIn',
                       ritardo=200, durata=900, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    for k in range(15):
        ang = -70 + k * 10
        lungo = ang in (-60, -20, 20, 60)
        rt = R - 34
        cx, cy = rt * math.sin(math.radians(ang)), -rt * math.cos(math.radians(ang))
        hh = 26 if lungo else 14
        a, b = _quadrante(cx, cy, 4 if lungo else 2, hh)
        fermo = {'inizio': 0, 'fine': 100, 'da': {'r': ang}, 'a': {'r': ang}}
        L.append(K.livello('ba-tacca%d' % k, tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor=C['nero'], borderRadius=2,
                           entra='fadeIn', ritardo=300, scrollMove=fermo,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': max(0.32, b[2]), 'height': b[3]}}))
    for k, tempo in enumerate(TEMPI):
        ang = tempo[1]
        rt = R + 40
        cx, cy = rt * math.sin(math.radians(ang)), -rt * math.cos(math.radians(ang))
        a, b = _quadrante(cx, cy, 120, 22)
        L.append(K.livello('ba-nome%d' % k, contenuto=tempo[0].rstrip('.'), x=a[0], y=a[1], w=a[2], size=13, peso='600',
                           colore=mix(C['chiaro'], 85), align='center', letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                           entra='fadeIn', ritardo=400,
                           responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': 18}}))
    for k, tempo in enumerate(TEMPI):
        a, b = _quadrante(0, 96, 300, 24)
        L.append(K.livello('ba-hpa%d' % k, contenuto=tempo[2], x=a[0], y=a[1], w=a[2], size=14, peso='500', colore=C['tenue'],
                           align='center', fontFamily=MONO, entra='fadeIn', ritardo=0, scrollMove=_finestra(_testo(tempo[8])),
                           responsive={'mobile': {'x': 15, 'y': b[1], 'width': 70, 'fontSize': 17}}))
    a, b = _quadrante(0, 140, 200, 20)
    L.append(K.livello('ba-marca', contenuto='FALDA · TRIESTE', x=a[0], y=a[1], w=a[2], size=11, peso='600', colore=FELTRO,
                       align='center', letterSpacing=3, fontFamily=MONO, entra='fadeIn', ritardo=500,
                       responsive={'mobile': {'x': 25, 'y': b[1], 'width': 50, 'fontSize': 13}}))
    # l'ago: un'asta col centro sul perno; la metà di sopra piena (la punta), quella di sotto che sfuma (il contrappeso)
    giro = [{'p': 0, 'r': -60}, {'p': 18, 'r': -60}, {'p': 26, 'r': -20}, {'p': 30, 'r': -14}, {'p': 34, 'r': -24},
            {'p': 38, 'r': -18}, {'p': 42, 'r': -20}, {'p': 50, 'r': 20}, {'p': 66, 'r': 20}, {'p': 74, 'r': 60}, {'p': 100, 'r': 60}]
    a, b = _quadrante(0, 0, 8, 2 * (R - 40))
    L.append(K.livello('ba-ago', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=999,
                       shapeGradient={'angle': 180, 'from': FELTRO, 'fromStop': 48, 'to': 'transparent'}, entra='fadeIn',
                       ritardo=500, scrollMove={'inizio': 0, 'fine': 100, 'tappe': giro},
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': max(0.9, b[2]), 'height': b[3]}}))
    a, b = _quadrante(0, 0, 30, 30)
    L.append(K.livello('ba-perno', tipo='shape', x=a[0], y=a[1], w=a[2], h=a[3], bgColor='#B08D57', borderRadius=999,
                       borderWidth=3, borderColor=FELTRO, entra='fadeIn', ritardo=500,
                       responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    # a destra la foto per quel tempo, a sinistra la parola, il cappello e il prezzo
    for k, tempo in enumerate(TEMPI):
        mov = _finestra(_testo(tempo[8]))
        a = _pc(1070, 120, 300, 570, W, H)
        L.append(K.livello('ba-foto%d' % k, tipo='image', imageSrc=K.img(tempo[6]), objectFit='cover', x=a[0], y=a[1], w=a[2],
                           h=a[3], borderRadius=10, entra='fadeIn', ritardo=300, scrollMove=_finestra(tempo[8]),
                           boxShadow={'x': 0, 'y': 24, 'blur': 50, 'spread': -14, 'color': mix(C['nero'], 80)},
                           responsive={'mobile': {'x': 7, 'y': 75.4, 'width': 86, 'height': 22.6}}))
        L.append(K.livello('ba-parola%d' % k, contenuto=tempo[0], tag='p', x=6, y=40, w=30, size=96, peso='500',
                           colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1, letterSpacing=-2, entra='fadeIn', ritardo=300,
                           scrollMove=mov, responsive={'mobile': {'x': 7, 'y': 54.8, 'width': 86, 'fontSize': m(46)}}))
        L.append(K.livello('ba-cosa%d' % k, contenuto='%s · <strong>%s</strong>' % (tempo[3], tempo[4].replace(' €', '&nbsp;€')),
                           x=6, y=58, w=32, size=24, peso='500', colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.25,
                           entra='fadeIn', ritardo=400, scrollMove=mov,
                           responsive={'mobile': {'x': 7, 'y': 62.1, 'width': 86, 'fontSize': m(16)}}))
        L.append(K.livello('ba-nota%d' % k, contenuto=tempo[5], x=6, y=68, w=30, size=16, colore=mix(C['chiaro'], 80),
                           lineHeight=1.55, entra='fadeIn', ritardo=500, scrollMove=mov,
                           responsive={'mobile': {'x': 7, 'y': 68.4, 'width': 86, 'fontSize': m(12)}}))
    L.append(K.livello('ba-b', tipo='button', contenuto='Tutto il negozio', x=6, y=82, w='auto', size=15, peso='600',
                       colore=C['nero'], bgColor=GIALLO, padding=14, borderRadius=10, entra='fadeIn', ritardo=600,
                       buttonUrl=P['negozio'], hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 200},
                       responsive={'mobile': {'visible': False}}))
    sl = [K.diapositiva('ba-1', {'type': 'color', 'image': '', 'video': '', 'color': C['nero'], 'overlay': C['nero'],
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
        N.testata_sezione(t, 'Il negozio', 'Cosa vi mettete in testa?', P['negozio'], 'Tutto il negozio'),
        t.one([N.categorie(t, colonne=5, raggio_foto=10, ratio='4-5')]),
    ], padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 40}))
    out.append(K.apertura(t, barometro(t)))
    out.append(t.section([
        N.testata_sezione(t, 'I più provati', 'Quelli che escono di bottega in testa', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Il panama,', 'arrotolato nel tubo.', 'Intrecciato a mano in Ecuador: 120 €.', 'Paglie',
                               N.cat_url('paglia'), K.photo_bg('pexels-39275404.jpg'), size=34)], wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'La sciarpa,', 'del colore del golfo.', 'Merino, un metro e ottanta: 45 €.', 'Cuffie e sciarpe',
                               N.cat_url('lana'), K.photo_bg('pexels-10453034.jpg'), size=34)], wm='1-2', ws='1-1')],
              layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Le offerte della settimana', color=GIALLO),
                      N.titolo(t, 'Fino a domenica.', size=44, color=C['chiaro']),
                      N.testo(t, 'L’ombrello antivento, il fedora nero, la paglia a tesa larga, il basco rosso, i quadri.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=42), N.link(t, 'Tutte le offerte', P['offerte'], chiaro=True, align='left', pad_top=16)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([N.prodotti(t, on_sale=True, posts_per_page=8, columns=4, columns_tablet=3, layout='carousel',
                          orderby='popularity', card_bg=C['chiaro'])]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 64, 'padding_bottom': 64}))
    out.append(K.video_frase(t, V_SCAFFALE, 'In bottega', 'Il cappello <em>si prova allo specchio.</em>',
                             'Prendiamo la misura col nastro, vi diamo tre forme da provare e il feltro si scalda al vapore '
                             'finché non sta bene a voi.', overlay=40, extra=[N.pulsante(t, 'La bottega', P['bottega'], pad_top=20)]))
    out.append(t.section([
        N.testata_sezione(t, 'Ombrelli', 'Per la bora e per la pioggia', N.cat_url('ombrelli')),
        t.one([N.prodotti(t, category='ombrelli', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-5699022.jpg', 'Una coppia prova i cappelli davanti alla parete dei feltri', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'La misura'), N.titolo(t, 'Dal 55 al 60,<br><em>col nastro da sarta.</em>', size=52),
                      N.testo(t, 'La misura del cappello è la circonferenza della testa in centimetri, un dito sopra le '
                                 'orecchie. Se siete fra due, prendete quella più grande: si stringe col nastrino di sughero. '
                                 'E se non va, si cambia gratis.', size=17),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Cambio misura gratis, entro 30 giorni', 'Il nastrino per stringere, nella scatola',
                              'In bottega si allarga al vapore']],
                          'icon_color': C['acc'], 'icon_size': '18', 'text_color': C['testo'], 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': False, 'layout': 'vertical'},
                          style={}),
                      N.pulsante(t, 'Prendere la misura', P['misure'], pad_top=20)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 96, 'padding_bottom': 96}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il negozio', 'Dalla testa all’ombrello', 'Fedora e feltri, paglie, coppole e baschi, cuffie e '
                            'sciarpe, ombrelli: la misura si cambia gratis.', colonne=3, attributi='misura',
                            banner=N.banner(t, 'L’ombrello antivento,', '29 €.', 'Si rovescia e torna, fino a domenica.',
                                            'Ombrelli', N.cat_url('ombrelli'), K.photo_bg('pexels-15171377.jpg'), size=34))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'La paglia a tesa larga,', '45 €.', 'Undici centimetri d’ombra. Era 59 €.', 'Paglie',
                        N.cat_url('paglia'), K.photo_bg('pexels-4738060.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Il basco rosso,', '28 €.', 'Lana follata, si porta di lato. Era 35 €.', 'Coppole e baschi',
                        N.cat_url('coppole'), K.photo_bg('pexels-12077063.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Le offerte della settimana', 'Fino a domenica', 'L’ombrello antivento, il fedora nero, la paglia, '
                            'il basco e la sciarpa a quadri a meno, finché soffia.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('ruler', 'Cambio misura gratis'), ('truck', 'Gratis da 50 €'),
                                                   ('package', 'Nella cappelliera'), ('rotate-ccw', 'Resi in 30 giorni')],
                             bundle='Si porta insieme', miniature='left', altezza='600px', bottone='Aggiungi al carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 50 €. I cappelli viaggiano nella cappelliera.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'L’indirizzo, la spedizione, il pagamento. Nelle note: se è un regalo, il biglietto.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, gli indirizzi, la vostra misura, i cambi in corso.',
                            ('La misura non va?', 'Scriveteci col numero dell’ordine: vi mandiamo l’altra, gratis.', 'Scrivici',
                             P['assistenza'] + '#scrivici'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto del negozio.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'Al telefono e in bottega dalle 9:30 alle 19:30, dal lunedì al sabato.', [
        ('truck', 'Spedizioni', 'In 24 ore, gratis sopra i 50 €.', 'spedizioni'),
        ('ruler', 'Misure', 'Il cambio è gratis, entro 30 giorni.', 'scrivici'),
        ('package', 'Cappelliera', 'I feltri viaggiano senza schiacciarsi.', 'spedizioni'),
        ('rotate-ccw', 'Resi', 'In 30 giorni, con l’etichetta.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 50 €; sotto, 6 €. I feltri partono nella cappelliera.'),
        ('Come scelgo la misura?', 'Col metro da sarta, un dito sopra le orecchie: nella guida c’è come si fa.'),
        ('E se la misura è sbagliata?', 'Ce lo rimandate e vi spediamo l’altra, gratis, entro 30 giorni.'),
        ('Rimettete in forma i cappelli vecchi?', 'Sì, in bottega: vapore, forma di legno e nastro nuovo, da 25 €.')],
        CONTATTI, campi_extra='Un cambio di misura\nUn cappello da rimettere in forma\n',
        foto=('pexels-5699094.jpg', 'La parete dei feltri in bottega'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa pagina <em>è volata via.</em>', 'L’ha presa la bora: cercate nel negozio qui sopra.',
                        K.video_bg(V_ASFALTO, overlay=40))


def build_misure():
    t = Tpl(ID, 'misure')
    out = [N.intestazione(t, 'Prendere la misura', 'Dal 55 al 60.', 'La misura del cappello è la circonferenza della testa in '
                          'centimetri. Bastano un metro da sarta e uno specchio.')]
    out.append(t.section([
        t.one([K.numeri(t, [('55–56', 'teste piccole · S'), ('57–58', 'le più comuni · M e L'), ('59–60', 'teste grandi · XL'),
                            ('1 cm', 'fra una misura e l’altra')], colonne=4, size=52)]),
    ], padding='default', width='large', style={'padding_top': 24, 'padding_bottom': 48}))
    passi = [('ruler', 'Il metro', 'Un metro da sarta morbido; se non c’è, uno spago e poi il righello.'),
             ('hat-glasses', 'Dove', 'Un dito sopra le orecchie e sulla fronte, dove starà il cappello.'),
             ('check', 'Quanto', 'Ben aderente ma senza stringere: leggete il numero in centimetri.'),
             ('plus', 'Fra due misure', 'Prendete la più grande: si stringe col nastrino di sughero.')]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Si fa così'), N.titolo(t, 'Quattro passi,<br><em>un minuto.</em>', size=46),
                      t.classic('iconlist', {
                          'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''}
                                    for i, a, b in passi],
                          'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
                          'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'},
                          style={})], wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-5699025.jpg', 'Un ragazzo prova un fedora davanti alla parete dei cappelli', ratio='4/5')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 56, 'padding_bottom': 72}))
    out.append(K.video_frase(t, V_PROVA, 'La prova', 'Il cappello giusto <em>non si sente.</em>',
                             'Non lascia il segno sulla fronte e con la bora non si muove: se stringe è piccolo, se scende sugli '
                             'occhi è grande.', overlay=40))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Il cambio'), N.titolo(t, 'Non va? Si cambia.', size=44),
                      N.testo(t, 'Entro 30 giorni ci rimandate il cappello con l’etichetta e vi spediamo l’altra misura: la '
                                 'spedizione è nostra, all’andata e al ritorno.', size=17),
                      K.faq(t, [('Il feltro si allarga?', 'Un poco, col vapore: in bottega fino a mezza misura.'),
                                ('E la paglia?', 'La paglia no: per lei scegliete la misura giusta, o la più grande.'),
                                ('Le cuffie hanno la misura?', 'No: cuffie, baschi e sciarpe sono taglia unica.')], 'mis')],
                     wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-5698918.jpg', 'I cappelli in feltro sulle forme in bottega', ratio='4/5')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='cambio', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(t.section([
        N.testata_sezione(t, 'Da provare', 'Fedora e feltri', N.cat_url('feltri')),
        t.one([N.prodotti(t, category='feltri', posts_per_page=4, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


def build_bottega():
    t = Tpl(ID, 'bottega')
    out = [K.intro(t, 'La bottega', ['In via San Nicolò,', 'dal 1952.'], 'Le forme di legno, il ferro a vapore, la parete '
                   'dei feltri: qui si prende la misura, si prova allo specchio e si rimettono in forma i cappelli di famiglia.',
                   bg=K.video_bg(V_BOTTEGA, overlay=40), size=86)]
    lavori = [('wind', 'Rimettere in forma', 'Vapore e forma di legno: il feltro torna com’era. Da 25 €.'),
              ('ribbon', 'Il nastro nuovo', 'Gros-grain di seta, nel colore che volete. 15 €.'),
              ('ruler', 'Allargare', 'Fino a mezza misura, col vapore. 12 €.'),
              ('umbrella', 'Gli ombrelli', 'Una stecca nuova, il puntale, la tela. Da 10 €.')]
    out.append(t.section([
        N.testata_sezione(t, 'I lavori', 'Quello che si aggiusta'),
        t.one([t.classic('iconlist', {
            'items': [{'icon': i, 'text': '<strong>%s.</strong> %s' % (a, b), 'link': '', 'color': ''} for i, a, b in lavori],
            'icon_color': C['acc'], 'icon_size': '22', 'text_color': C['testo'], 'text_size': '17', 'gap': '18',
            'icon_shape': 'circle', 'icon_bg_color': mix(C['acc'], 12), 'divider': True, 'layout': 'vertical'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 56}))
    out.append(t.section([
        t.one([K.numeri(t, [('1952', 'l’anno della bottega'), ('300', 'forme di legno'), ('7', 'giorni per un feltro rimesso'),
                            ('100 km/h', 'la bora del collaudo')], colonne=4, size=52, chiaro=True)]),
    ], bg=solid(C['nero']), padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 48}))
    modulo = t.classic('form', {
        'fields': [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True, segnaposto='nome@esempio.it'),
                   K.campo('select', 'Per cosa', 'cosa', True, opzioni='Prendere la misura e provare\nRimettere in forma un '
                                                                     'cappello\nIl nastro nuovo\nAggiustare un ombrello'),
                   K.campo('date', 'Il giorno', 'giorno', True),
                   K.campo('textarea', 'Qualcosa da dirci', 'note', False, '1-1', segnaposto='Il cappello, la misura, un’idea')],
        'email_subject': 'Falda — un appuntamento in bottega', 'submit_text': 'Prenota', 'submit_icon': '',
        'success_message': 'Grazie: vi scriviamo entro domani per confermare l’ora.', 'submit_full_width': False,
        'submit_alignment': 'left', 'label_color': C['testo'], 'label_size': '13', 'label_weight': '500',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': C['chiaro'], 'input_color': C['testo'],
        'input_border_color': C['filo'], 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 10,
        'input_focus_border': C['acc'], 'input_focus_shadow': False, 'gap': '14', 'submit_bg': C['acc'],
        'submit_color': C['on_acc'], 'submit_radius': 10, 'submit_padding_x': '28', 'submit_padding_y': '15',
        'submit_font_size': '14', 'submit_font_weight': '600', 'submit_text_transform': 'none',
        'submit_letter_spacing': '0', 'submit_hover_bg': C['nero'], 'privacy_checkbox': True},
        style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
               'border_radius': 0})
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Un appuntamento'), N.titolo(t, 'Venite<br><em>a provare.</em>', size=46),
                      N.testo(t, 'Mezz’ora con noi, senza fretta: la misura, tre forme da provare, il feltro che si scalda al '
                                 'vapore. Portate il cappello da aggiustare.', size=17),
                      K.image(t, 'pexels-5699061.jpg', 'Il cappellaio mostra un fedora al banco', ratio='4/3')],
                     wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start'),
    ], html_id='prenota', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 88, 'padding_bottom': 88}))
    out.append(K.video_frase(t, V_PORTA, 'La porta verde', 'Si entra <em>col cappello in mano.</em>',
                             'Via San Nicolò 21, fra il Canal Grande e piazza della Borsa: la porta verde con la falda dipinta.',
                             overlay=40))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Dove'), N.titolo(t, 'In via San Nicolò.', size=40),
                      N.testo(t, 'Dal lunedì al sabato, 9:30–13 e 15:30–19:30. Col sole, la bora e la pioggia.', size=17),
                      K.faq(t, [('Serve prenotare?', 'Per provare no; per rimettere in forma un cappello sì, così c’è il vapore.'),
                                ('Quanto ci vuole?', 'La prova mezz’ora; un feltro rimesso in forma una settimana.'),
                                ('Si parcheggia?', 'In piazza Libertà, a dieci minuti a piedi.')], 'bot')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-5051802.jpg', 'Gli ombrelli colorati appesi sopra la via', ratio='4/5')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='start'),
    ], html_id='dove', padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    return out


THEME = tema(
    ID, NAME, 'Negozi online', 'Il barometro',
    ['cappelli', 'ombrelli', 'cappelleria', 'sciarpe', 'woocommerce', 'e-commerce', 'trieste', 'moda'],
    'Il negozio online di una cappelleria con gli ombrelli a Trieste: in apertura la fascia delle offerte che scorre di lato '
    'come col vento e le quattro schede coi prodotti, il mega menu col negozio, le offerte e le novità; a metà pagina il '
    'barometro: scorrendo l’ago passa da sole a bora, pioggia e neve, il cielo cambia e accanto arriva il cappello giusto; la '
    'guida alla misura, la bottega con la prenotazione, la scheda con la misura a scelta e il video della bottega.',
    [('Il negozio', P['negozio']), ('Offerte', P['offerte']), ('Novità', P['negozio']),
     ('Feltri', '{{PAGINA:negozio}}?product_cat=feltri'), ('Coppole', '{{PAGINA:negozio}}?product_cat=coppole'),
     ('Ombrelli', '{{PAGINA:negozio}}?product_cat=ombrelli'), ('La misura', P['misure']), ('La bottega', P['bottega'])],
    [('home', 'Home', None), ('negozio', 'Il negozio', 'negozio'), ('misure', 'Prendere la misura', 'misura'),
     ('bottega', 'La bottega', 'bottega'), ('offerte', 'Le offerte della settimana', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Jost\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fraunces\', Georgia, serif',
    '\'Red Hat Mono\', ui-monospace, Menlo, monospace', ['Fraunces', 'Jost', 'Red Hat Mono'], corsivo_di=['Fraunces'],
    peso_titoli='500', raggio=10, grana=0, titoli_line='1.04', titoli_spazio='-0.015em')
tema_negozio(THEME, NAME)
pannelli(THEME, NAME, {'Il negozio': ('pannello-reparti', 'Pannello del negozio'),
                       'Offerte': ('pannello-offerte', 'Pannello delle offerte'),
                       'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['preview_v'] = THEME['preview_mobile_v'] = '4'  # anteprime con le testate nuove (2 nov 2026)
THEME['cursor']['style'] = 'ombrello'
THEME['cursor']['ring_color'] = HEX['primary']   # la tela dell'ombrello
THEME['cursor']['dot_color'] = HEX['accent']     # il manico


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'misure.json': build_misure(), 'bottega.json': build_bottega(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI,
                    attributi={'misura': {'nome': 'Misura', 'termini': MISURE}},
                    dentro='Il cappello nella cappelliera di cartone, il nastrino per stringere, il biglietto della bottega.')


if __name__ == '__main__':
    main()
