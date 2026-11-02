# -*- coding: utf-8 -*-
"""Genera il tema «Siero» (id siero): il negozio online di un laboratorio di cosmetica essenziale a Bologna — detergenti,
sieri, creme, trattamenti e kit, formule con pochi ingredienti in flaconi di vetro ricaricabili, la routine in tre passi.

Tema chiaro color porcellana: argilla rosata sui pulsanti, salvia, il rosso dei prezzi scontati. Titoli in Instrument
Serif (col corsivo), testo in Figtree, dati in IBM Plex Mono. Angoli morbidi, card con l'ombra leggera, foto quadrate.

Apertura: la vetrina delle offerte, come nei negozi di bellezza online: una fascia bassa che scorre da sola (il
kit del mattino, il siero ialuronico, la crema mani), sotto le quattro schede coi prodotti veri; il mega menu apre i
pannelli del negozio, delle novità e delle offerte. Cursore: la perla.

WooCommerce: catalogo coi filtri per tipo di pelle, scheda prodotto col video della texture nella galleria, carrello,
cassa, account, preferiti; la routine da comporre; i kit in offerta col conto alla rovescia.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
from negozio import Negozio, pannelli, tema_negozio, prodotto, scrivi_prodotti  # noqa: E402

ID = 'siero'
NAME = 'Siero'

HEX = {
    'primary': '#9B4A3C', 'primary_contrast': '#FFFFFF', 'secondary': '#7E9483', 'secondary_contrast': '#14201A',
    'accent': '#B3312A', 'dark': '#221A18', 'light': '#FFFCF9', 'muted': '#EFE4DC', 'muted_contrast': '#2A2220',
    'text': '#2A2220', 'text_muted': '#6A5C56', 'background': '#F7F1EC', 'border': '#DCCBC0', 'link': '#9B4A3C',
    'success': '#126B33', 'warning': '#9A4A08',
}
P = {k: '{{PAGINA:%s}}' % k for k in ('home', 'negozio', 'routine', 'ingredienti', 'offerte', 'assistenza', 'preferiti',
                                     'carrello', 'cassa', 'account')}
MAIL, PHONE, PHONE_TEL = 'ciao@siero.example', '+39 051 000 3317', 'tel:+390510003317'

V_GOCCIA, V_AMBRA, V_CREMA, V_MANI, V_ROSA, V_VISO, V_ACQUA = (
    'pexels-video-4132710', 'pexels-video-7607246', 'pexels-video-9697681', 'pexels-video-8141586', 'pexels-video-7305326',
    'pexels-video-6543580', 'pexels-video-4317444')
VIDEOS = [V_GOCCIA, V_AMBRA, V_CREMA, V_MANI, V_ROSA, V_VISO, V_ACQUA]
PHOTOS = [
    'pexels-8015784.jpg', 'pexels-8015792.jpg', 'pexels-8015774.jpg', 'pexels-8015461.jpg', 'pexels-7691112.jpg',
    'pexels-3940337.jpg', 'pexels-3940335.jpg', 'pexels-6690863.jpg', 'pexels-11485121.jpg', 'pexels-6693950.jpg',
    'pexels-7615819.jpg', 'pexels-7615869.jpg', 'pexels-4041231.jpg', 'pexels-4041392.jpg', 'pexels-4041386.jpg',
    'pexels-6693945.jpg', 'pexels-6693972.jpg', 'pexels-9774609.jpg', 'pexels-9774606.jpg', 'pexels-9774679.jpg',
    'pexels-6167445.jpg', 'pexels-6167444.jpg', 'pexels-8533222.jpg', 'pexels-8015480.jpg', 'pexels-8128674.jpg',
    'pexels-8129894.jpg', 'pexels-11417936.jpg', 'pexels-19572632.jpg', 'pexels-7321352.jpg', 'pexels-7321367.jpg',
    'pexels-7321363.jpg', 'pexels-4938500.jpg', 'pexels-8903614.jpg', 'pexels-7691167.jpg', 'pexels-8015698.jpg',
    'pexels-8015762.jpg', 'pexels-8049849.jpg', 'pexels-7691164.jpg', 'pexels-7321500.jpg', 'pexels-7622877.jpg',
    'pexels-28994646.jpg', 'pexels-9774854.jpg', 'pexels-15699693.jpg', 'pexels-8015898.jpg', 'pexels-8450510.jpg',
    'pexels-8450134.jpg', 'pexels-8450116.jpg', 'pexels-7011296.jpg', 'pexels-7011302.jpg', 'pexels-9775170.jpg',
    'pexels-9774782.jpg', 'pexels-9227978.jpg',
]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='400', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True)
C = K.c
TITOLI, MONO = 'Instrument Serif', 'IBM Plex Mono'
N = Negozio(K, NAME, P, raggio=14, ratio='1-1', card='shadow', hover='swap', testata_scura=False,
            consegna='Spedizione gratuita da 49 €', evidenza='Il preferito del laboratorio', peso_titoli='400')

CATEGORIE = [
    ('detergenti', 'Detergenti', 'Gel, latte e saponette: il primo passo, mattina e sera.', 'pexels-8015784.jpg'),
    ('sieri', 'Sieri', 'Vitamina C, acido ialuronico, retinolo, oli: il passo che tratta.', 'pexels-11485121.jpg'),
    ('creme', 'Creme', 'Idratare e proteggere, di giorno e di notte.', 'pexels-9774609.jpg'),
    ('trattamenti', 'Trattamenti', 'Maschere, contorno occhi, labbra e il rullo di quarzo.', 'pexels-7321352.jpg'),
    ('corpo', 'Corpo e mani', 'Oli e creme per il corpo, la crema mani da borsa.', 'pexels-19572632.jpg'),
    ('kit', 'Kit e routine', 'La routine completa in un kit, anche da viaggio.', 'pexels-7691167.jpg'),
]
PELLE = ['Tutti i tipi di pelle', 'Pelle secca', 'Pelle grassa', 'Pelle sensibile', 'Pelle matura']
T, S, G, SE, M = PELLE
PRODOTTI = [
    prodotto('SIE-001', 'Gel detergente Fiore', 'gel-detergente-fiore', 'detergenti', 24, 0, 60, 4.8, 612, True,
             ['pexels-8015784.jpg', 'pexels-8015792.jpg', 'pexels-8015774.jpg'],
             ['Tensioattivi dolci da zucchero', 'Acqua di fiori d’arancio', 'Non tira, non secca', 'Flacone in vetro ricaricabile'],
             'Il primo passo, mattina e sera: si massaggia sul viso umido e si sciacqua. La pelle resta morbida.',
             attributi={'pelle': [T, SE]}, cross=['SIE-005', 'SIE-009']),
    prodotto('SIE-002', 'Latte detergente all’avena', 'latte-detergente-avena', 'detergenti', 22, 0, 40, 4.6, 233, False,
             ['pexels-8015461.jpg', 'pexels-7691112.jpg'], ['Avena colloidale', 'Toglie anche il trucco', 'Senza risciacquo',
             'Per la pelle che tira'], 'Per la sera e per la pelle secca: si stende col cotone e toglie anche il trucco.',
             attributi={'pelle': [S, SE]}, cross=['SIE-007', 'SIE-010']),
    prodotto('SIE-003', 'Saponetta all’argilla verde', 'saponetta-argilla-verde', 'detergenti', 12, 9, 80, 4.5, 318, False,
             ['pexels-3940337.jpg', 'pexels-3940335.jpg'], ['Argilla verde e olio d’oliva', 'Saponificata a freddo',
             'Senza imballo di plastica', 'Dura due mesi'], 'Purifica senza seccare: per la pelle grassa, anche sul corpo.',
             attributi={'pelle': [G]}, cross=['SIE-016']),
    prodotto('SIE-004', 'Saponetta al carbone e oliva', 'saponetta-carbone-oliva', 'detergenti', 12, 0, 55, 4.4, 141, False,
             ['pexels-6690863.jpg'], ['Carbone vegetale attivo', 'Olio d’oliva', 'Saponificata a freddo', 'Profumo di salvia'],
             'Il carbone che pulisce i pori e l’olio che nutre: la saponetta della sera.', attributi={'pelle': [G]}),
    prodotto('SIE-005', 'Siero Vitamina C 15%', 'siero-vitamina-c', 'sieri', 38, 0, 45, 4.9, 1024, True,
             ['pexels-11485121.jpg', 'pexels-6693950.jpg'], ['Vitamina C stabilizzata al 15%', 'Illumina e uniforma',
             'Si usa al mattino, prima della crema', 'Il video della goccia nella galleria'],
             'La goccia del mattino: quattro gocce sul viso asciutto, prima della crema. In un mese il colorito è più uniforme.',
             video=V_AMBRA, attributi={'pelle': [T, M]}, upsell=['SIE-018'], cross=['SIE-009', 'SIE-011']),
    prodotto('SIE-006', 'Siero all’acido ialuronico', 'siero-acido-ialuronico', 'sieri', 34, 27, 52, 4.7, 760, True,
             ['pexels-7615819.jpg', 'pexels-7615869.jpg'], ['Tre pesi di acido ialuronico', 'Idrata in profondità',
             'Senza profumo', 'Va bene sotto ogni crema'], 'Il siero dell’acqua: tre pesi di ialuronico che idratano a strati.',
             attributi={'pelle': [T, S, SE]}, cross=['SIE-009', 'SIE-012']),
    prodotto('SIE-007', 'Olio viso alla rosa mosqueta', 'olio-viso-rosa-mosqueta', 'sieri', 32, 0, 30, 4.8, 288, False,
             ['pexels-4041231.jpg', 'pexels-4041392.jpg', 'pexels-4041386.jpg'], ['Rosa mosqueta spremuta a freddo',
             'Petali di rosa damascena', 'Nutre senza ungere', 'Per la sera'], 'Tre gocce la sera, sul viso ancora umido: '
             'la pelle secca si ammorbidisce dalla prima notte.', attributi={'pelle': [S, M]}, cross=['SIE-010', 'SIE-017']),
    prodotto('SIE-008', 'Siero notte al retinolo', 'siero-notte-retinolo', 'sieri', 42, 0, 26, 4.6, 402, False,
             ['pexels-6693945.jpg', 'pexels-6693972.jpg'], ['Retinolo incapsulato 0,3%', 'Si comincia due sere a settimana',
             'Con la protezione solare di giorno', 'Flacone scuro, al riparo dalla luce'],
             'Il siero che leviga: si comincia piano, due sere a settimana, e si sale.', attributi={'pelle': [M]},
             cross=['SIE-011', 'SIE-010']),
    prodotto('SIE-009', 'Crema giorno idratante', 'crema-giorno-idratante', 'creme', 34, 0, 48, 4.8, 845, True,
             ['pexels-9774609.jpg', 'pexels-9774606.jpg', 'pexels-9774679.jpg'], ['Squalano e ceramidi', 'Si assorbe in un minuto',
             'Ottima sotto il trucco', 'Il video della texture nella galleria'], 'La crema di ogni mattina: leggera come un '
             'gel, ricca come una crema. Guardatela nel video.', video=V_CREMA, attributi={'pelle': [T, S, SE]},
             upsell=['SIE-010'], cross=['SIE-005', 'SIE-011']),
    prodotto('SIE-010', 'Crema notte ricca', 'crema-notte-ricca', 'creme', 39, 0, 33, 4.7, 391, False,
             ['pexels-6167445.jpg', 'pexels-6167444.jpg'], ['Burro di karité e lavanda', 'Ricca, per la notte',
             'Ripara la barriera', 'Vasetto in vetro ambrato'], 'La crema della sera, ricca e profumata di lavanda.',
             attributi={'pelle': [S, M]}, cross=['SIE-007', 'SIE-008']),
    prodotto('SIE-011', 'Fluido protettivo SPF 50', 'fluido-spf-50', 'creme', 29, 0, 70, 4.6, 528, False,
             ['pexels-8533222.jpg'], ['Protezione alta, filtri di nuova generazione', 'Non lascia la patina bianca',
             'Resiste al sudore', 'Tutto l’anno'], 'L’ultimo passo del mattino, ogni giorno dell’anno.',
             attributi={'pelle': [T]}, cross=['SIE-005', 'SIE-009']),
    prodotto('SIE-012', 'Contorno occhi al caffè', 'contorno-occhi-caffe', 'trattamenti', 28, 0, 36, 4.5, 214, False,
             ['pexels-8015480.jpg'], ['Caffeina e peptidi', 'Sgonfia le borse', 'Si picchietta con l’anulare',
             'Tubetto con la punta fredda'], 'Il risveglio degli occhi: si picchietta col dito, la mattina.',
             attributi={'pelle': [T]}),
    prodotto('SIE-013', 'Balsamo labbra al lampone', 'balsamo-labbra-lampone', 'trattamenti', 9, 0, 120, 4.7, 902, False,
             ['pexels-8128674.jpg', 'pexels-8129894.jpg'], ['Burro di cacao e lampone', 'Un velo di colore', 'Vasetto da borsa',
             'Vegano'], 'Il balsamo da borsetta, rosa lampone: nutre e colora appena.', attributi={'pelle': [T]}),
    prodotto('SIE-014', 'Crema mani alla calendula', 'crema-mani-calendula', 'corpo', 14, 11, 90, 4.8, 1302, False,
             ['pexels-11417936.jpg'], ['Calendula e glicerina', 'Non unge, si assorbe subito', 'Tubetto da 50 ml',
             'Il video nella galleria'], 'La crema mani che si mette in tasca: non unge il telefono.', video=V_MANI,
             attributi={'pelle': [T, S]}),
    prodotto('SIE-015', 'Olio corpo all’elicriso', 'olio-corpo-elicriso', 'corpo', 29, 0, 28, 4.7, 176, False,
             ['pexels-19572632.jpg'], ['Olio di mandorle ed elicriso', 'Sulla pelle umida, dopo la doccia', 'Profumo di erbe',
             'Flacone a pompa'], 'Dopo la doccia, sulla pelle ancora umida: profuma di macchia mediterranea.',
             attributi={'pelle': [S]}),
    prodotto('SIE-016', 'Maschera all’argilla rossa', 'maschera-argilla-rossa', 'trattamenti', 26, 0, 40, 4.6, 233, False,
             ['pexels-7321352.jpg', 'pexels-7321367.jpg', 'pexels-7321363.jpg'], ['Argilla rossa in polvere',
             'Si prepara con l’acqua o il tonico', 'Dieci minuti, una volta a settimana', 'Per trenta maschere'],
             'Si prepara al momento, come nelle foto: argilla e qualche goccia di olio, dieci minuti.',
             attributi={'pelle': [G, T]}),
    prodotto('SIE-017', 'Rullo in quarzo rosa', 'rullo-quarzo-rosa', 'trattamenti', 26, 0, 25, 4.4, 160, False,
             ['pexels-4938500.jpg', 'pexels-8903614.jpg'], ['Quarzo rosa vero', 'Due rulli, grande e piccolo',
             'Tenuto in frigo sgonfia il viso', 'Con la custodia'], 'Il massaggio del mattino, freddo di frigo, dopo il siero.'),
    prodotto('SIE-018', 'Kit routine del mattino', 'kit-routine-mattino', 'kit', 79, 64, 20, 4.9, 312, True,
             ['pexels-7691167.jpg', 'pexels-8015698.jpg', 'pexels-8015762.jpg'], ['Gel detergente Fiore', 'Siero Vitamina C',
             'Crema giorno idratante', 'Il kit costa il 19% in meno'], 'I tre passi del mattino in un kit: detergi, tratta, '
             'idrata.', attributi={'pelle': [T]}, cross=['SIE-011']),
    prodotto('SIE-019', 'Kit da viaggio', 'kit-viaggio', 'kit', 39, 0, 30, 4.6, 98, False,
             ['pexels-8049849.jpg', 'pexels-7691164.jpg'], ['Cinque formati da 15 ml', 'Sacchetto in cotone', 'Sotto i 100 ml '
             'per l’aereo', 'Si ricaricano'], 'La routine intera nel bagaglio a mano.', attributi={'pelle': [T]}),
]
RECENSIONI = [[5, 'Pelle più luminosa in tre settimane. Il flacone in vetro è bellissimo.'],
              [5, 'Finalmente una crema che non mi fa venire i brufoli. Ricomprata.'],
              [4, 'Ottima, profumo leggero. Il contagocce a volte gocciola un po’.'],
              [5, 'Pelle sensibile e nessun rossore. Mi hanno consigliato loro la routine per email.'],
              [4, 'Funziona, ci vuole un mese per vedere la differenza come dicono.'],
              [5, 'Arrivato in due giorni con un campioncino in più. Gentilissimi.'],
              [5, 'La ricarica costa meno e il flacone dura per sempre. Bella idea.'],
              [4, 'Texture piacevole, la confezione potrebbe essere più grande.']]
NOMI = ['Giulia R.', 'Federica M.', 'Sara B.', 'Chiara L.', 'Anna P.', 'Elena C.', 'Laura D.', 'Valeria T.', 'Silvia G.',
        'Marta F.', 'Paola V.', 'Davide S.']
PROMESSE = [('truck', 'Spedizione gratuita da 49 €'), ('rotate-ccw', 'Reso gratuito in 30 giorni, anche aperto'),
            ('leaf', 'Al massimo dieci ingredienti'), ('recycle', 'Flaconi in vetro ricaricabili')]
CONTATTI = [('map-pin', 'Laboratorio · via Saragozza 40, Bologna', P['ingredienti'] + '#laboratorio'),
            ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return N.testata(Tpl(ID, 'header'), 'siero', icona='droplet', ricerca='Cerca un siero, una crema, un ingrediente…',
                     parole='vitamina C\nuna crema per la pelle secca\nla protezione solare\nun kit da viaggio',
                     annuncio=('Il kit della routine del mattino a −19%, fino a domenica.', 'Scopri i kit', P['offerte']), logo_size=32)


def build_footer():
    return N.piede(Tpl(ID, 'footer'), 'siero', 'droplet', 'Cosmetica essenziale da Bologna: al massimo dieci ingredienti per '
                   'formula, flaconi in vetro che si ricaricano, la routine in tre passi.',
                   [('Negozio', [('Tutti i prodotti', P['negozio']), ('Sieri', N.cat_url('sieri')), ('Creme', N.cat_url('creme')),
                                 ('Kit e offerte', P['offerte'])]),
                    ('Siero', [('Componi la routine', P['routine']), ('Gli ingredienti', P['ingredienti']),
                               ('I preferiti', P['preferiti']), ('Il mio account', P['account'])]),
                    ('Aiuto', [('Spedizioni e resi', P['assistenza'] + '#spedizioni'), ('Le ricariche', P['assistenza'] + '#spedizioni'),
                               ('Consulenza per email', P['assistenza'] + '#scrivici')])],
                   CONTATTI, '© 2026 Siero s.r.l. · Bologna · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a>',
                   PROMESSE, ('La lettera di Siero', 'Una routine al mese, scritta dal laboratorio.',
                              'Un ingrediente spiegato bene, i prodotti nuovi e le offerte, una volta al mese.',
                              'Fatto: la prima lettera arriva con un codice del 10%.'),
                   social=[('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')])


# ── L'apertura: la routine in tre lastre ──
# (Dal 1° nov 2026 la home apre con la vetrina delle offerte: questa apertura non è più usata.)
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
LASTRE = [('pexels-8015784.jpg', '01 · Detergi', 'Gel detergente Fiore', '24 €', 'detergenti'),
          ('pexels-11485121.jpg', '02 · Tratta', 'Siero Vitamina C', '38 €', 'sieri'),
          ('pexels-9774609.jpg', '03 · Idrata', 'Crema giorno', '34 €', 'creme')]


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    livelli = [
        K.livello('se-velo', tipo='shape', x=0, y=0, w=62, h=100, entra='fadeIn', ritardo=0,
                  shapeGradient={'angle': 90, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 42},
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 40}}),
    ]
    larg, alto, gap = 230.0, 307.0, 20.0
    xs = [690.0 + (larg + gap) * i for i in range(3)]
    y0 = 196.0
    lm, am = 150.0, 200.0
    xm = [WM / 2 - lm / 2 + (i - 1) * 111 for i in range(3)]   # sul telefono le lastre restano un po' sovrapposte
    ym = 680.0
    for i, (foto, passo, nome, prezzo, cat) in enumerate(LASTRE):
        dx = xs[1] - xs[i]
        sm = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': dx, 'y': 30 + 22 * (i - 1), 'rx': 58, 'r': 7 * (i - 1), 's': 0.9},
                                                  {'p': 58, 'x': 0, 'y': 0, 'rx': 0, 'r': 0, 's': 1},
                                                  {'p': 100, 'x': 0, 'y': 0, 'rx': 0, 'r': 0, 's': 1}]}
        a = _pc(xs[i], y0, larg, alto, W, H)
        b = _pc(xm[i], ym, lm, am, WM, HM)
        livelli.append(K.livello('se-lastra%d' % i, tipo='image', imageSrc=K.img(foto), objectFit='cover',
                                 objectPosition='center center', x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=18,
                                 borderWidth=1, borderColor=mix(C['chiaro'], 45),
                                 boxShadow={'x': 0, 'y': 24, 'blur': 48, 'spread': -12, 'color': mix(C['nero'], 60)},
                                 entra='fadeInUp', ritardo=200 + 150 * i, durata=1000, scrollMove=sm,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        compare = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 24}, {'p': 56, 'o': 0, 'y': 24},
                                                       {'p': 76, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
        a = _pc(xs[i], y0 + alto + 16, larg, 20, W, H)
        livelli.append(K.livello('se-passo%d' % i, contenuto=passo, x=a[0], y=a[1], w=a[2], size=11, peso='500',
                                 colore=mix(C['chiaro'], 82), fontFamily=MONO, letterSpacing=2, textTransform='uppercase',
                                 entra='fadeIn', ritardo=0, scrollMove=compare,
                                 responsive={'mobile': {'x': 8, 'y': 66 + 4.6 * i, 'width': 30, 'fontSize': m(10)}}))
        a = _pc(xs[i], y0 + alto + 38, larg, 26, W, H)
        livelli.append(K.livello('se-nome%d' % i, tipo='button', contenuto='%s · %s' % (nome, prezzo), x=a[0], y=a[1],
                                 w='auto', size=19, peso='400', colore=C['chiaro'], bgColor='transparent', padding=0,
                                 fontFamily=TITOLI, entra='fadeIn', ritardo=0, buttonUrl=N.cat_url(cat),
                                 hover={'color': mix(C['chiaro'], 70), 'duration': 200},
                                 scrollMove=compare,
                                 responsive={'mobile': {'x': 40, 'y': 65.6 + 4.6 * i, 'fontSize': m(15)}}))
    livelli += [
        K.livello('se-o', contenuto='Siero · cosmetica · Bologna', x=6, y=24, w=40, size=12, peso='500',
                  colore=mix(C['chiaro'], 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(11), 'y': 5.5, 'x': 7, 'width': 86}}),
        K.livello('se-t', contenuto='La pelle,<br><em>in tre passi.</em>', tag='h1', x=6, y=28, w=40, size=82, peso='400',
                  colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=1200,
                  responsive={'mobile': {'fontSize': m(46), 'y': 8.5, 'x': 7, 'width': 86}}),
        K.livello('se-s', contenuto='Detergi, tratta, idrata: al massimo dieci ingredienti per formula, in flaconi di vetro che '
                  'si ricaricano. Scorrete: la routine si mette in fila.', x=6, y=52, w=33, size=16, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=700,
                  responsive={'mobile': {'fontSize': m(13), 'y': 21, 'x': 7, 'width': 86}}),
        K.livello('se-b', tipo='button', contenuto='Componi la routine', x=6, y=66, w='auto', size=15, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=16, borderRadius=999, ritardo=1000, buttonUrl=P['routine'],
                  hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 32, 'x': 7, 'fontSize': m(12)}}),
        K.livello('se-l', tipo='button', contenuto='I kit −20%', x=21.4, y=66, w='auto', size=15, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), ritardo=1150, buttonUrl=P['offerte'],
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'y': 32, 'x': 56, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('se-1', K.sfondo_video(V_GOCCIA, overlay=0.12, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def compositore(t, titolo='La tua routine', cta='Vai ai prodotti', url=None):
    voci = [('Detergi', 'Gel detergente Fiore', '24', 'pexels-8015784.jpg'), ('Detergi', 'Latte all’avena', '22', 'pexels-8015461.jpg'),
            ('Detergi', 'Saponetta all’argilla', '9', 'pexels-3940337.jpg'),
            ('Tratta', 'Siero Vitamina C', '38', 'pexels-11485121.jpg'), ('Tratta', 'Siero ialuronico', '27', 'pexels-7615819.jpg'),
            ('Tratta', 'Olio alla rosa mosqueta', '32', 'pexels-4041231.jpg'),
            ('Idrata', 'Crema giorno', '34', 'pexels-9774609.jpg'), ('Idrata', 'Crema notte ricca', '39', 'pexels-6167445.jpg'),
            ('Proteggi', 'Fluido SPF 50', '29', 'pexels-8533222.jpg'), ('Proteggi', 'Balsamo labbra', '9', 'pexels-8128674.jpg')]
    return t.classic('lookbookmixer', {
        'items': [{'step': s, 'name': n, 'price': p, 'image': K.img(f), 'color': C['sala']} for s, n, p, f in voci],
        'currency': '€', 'card_title': titolo, 'card_steps_label': 'passi', 'card_sub': 'Quattro passi, il totale. Cambiate '
        'ogni passo finché è la vostra.', 'cta_text': cta, 'cta_url': url or P['negozio'], 'panel_bg': C['nero'],
        'slot_bg': C['nero'], 'accent': C['sec'], 'accent_ink': C['nero'], 'name_color': C['chiaro'],
        'price_color': mix(C['chiaro'], 76), 'line_color': mix(C['chiaro'], 14), 'show_swatch': False, 'image_veil': 55,
        'name_font_family': 'heading'})


def ingredienti(t):
    return t.classic('info-cards', {
        'items': [{'counter': n, 'counter_label': '', 'title': a, 'title_accent': '', 'title_accent_italic': False,
                   'description': b, 'icon': i, 'footer_dot_color': '', 'footer_text': '', 'link_url': P['ingredienti'],
                   'link_text': '', 'media_image': '', 'media_label': ''} for n, i, a, b in [
            ('01', 'sun', 'Vitamina C', 'Stabilizzata al 15%: illumina e uniforma il colorito.'),
            ('02', 'droplet', 'Acido ialuronico', 'Tre pesi diversi, per idratare a strati.'),
            ('03', 'flower-2', 'Rosa mosqueta', 'Spremuta a freddo: nutre la pelle secca.'),
            ('04', 'moon', 'Retinolo', 'Incapsulato, si comincia piano: leviga la notte.')]],
        'container_bg': {'type': 'none'}, 'container_radius': radius(0), 'container_padding': 0, 'container_gap': 0,
        'columns': 4, 'columns_tablet': 2, 'columns_mobile_landscape': 2, 'columns_mobile': 1, 'items_gap': 16,
        'card_bg': {'type': 'solid', 'color': C['chiaro']}, 'card_color': C['tenue'], 'card_accent_color': C['acc'],
        'card_radius': radius(18), 'card_radius_hover': radius(18), 'card_padding': 26, 'card_border': '', 'show_icon': True,
        'show_counter': True, 'show_counter_label': False, 'show_arrow': True, 'show_footer': False, 'show_link_text': False,
        'show_divider': False, 'show_media': False, 'title_font_family': 'heading', 'title_size': 30, 'title_weight': '400',
        'title_italic': False, 'title_color': C['testo'], 'description_size': 15, 'card_hover_effect': 'lift',
        'icon_color': C['acc'], 'icon_bg_color': mix(C['acc'], 12), 'counter_color': C['acc']})


# ── La vetrina delle offerte, le quattro schede e i pannelli del menu ──
# Dal 1° nov 2026 il negozio apre come quelli grandi: una fascia bassa di offerte che scorre da sola invece
# dell'apertura a tutto schermo, e il mega menu racconta le offerte e le novità nei suoi pannelli.
LARGO = 0.36  # la larghezza media di una lettera dei titoli (per la vetrina)


def vetrina(t):
    f = dict(titoli=TITOLI, mono=MONO, largo=LARGO)
    return N.vetrina(t, [
        N.offerta('se-o1', K.sfondo_video(V_AMBRA, overlay=0.12), 'Il kit del mattino · fino a domenica',
                  'La routine del mattino, −19%.', 'Detergente, siero e crema in un kit: 64 € invece di 79.', 'I kit',
                  N.cat_url('kit'), bollino='−19%', bollino_x=62, nota='Un campioncino scelto per voi in ogni ordine', primo=True, **f),
        N.offerta('se-o2', K.sfondo_foto('pexels-7011296.jpg', overlay=0.12, scala=1.06), 'I sieri · in offerta',
                  'Il siero ialuronico a 27 €.', 'Era 34 €: tre pesi di acido ialuronico, la pelle beve per ore.', 'I sieri',
                  N.cat_url('sieri'), bollino='−21%', nota='Spedizione gratuita da 49 €', **f),
        N.offerta('se-o3', K.sfondo_foto('pexels-9774854.jpg', overlay=0.12, scala=1.06), 'Corpo e mani · per l’inverno',
                  'La crema mani a 11 €.', 'Alla calendula: sta in borsa e non unge. Era 14 €.', 'Corpo e mani',
                  N.cat_url('corpo'), bollino='−21%', nota='Reso gratuito in 30 giorni, anche aperto', lato='destra', **f),
    ], transizione='zoomFade')


# le schede sotto la vetrina: «prezzi» (ogni negozio la sua forma, vedi Negozio.quadri)
def quadri(t):
    return N.quadri(t, stile='prezzi', voci=[
        ('Le offerte', {'on_sale': True, 'orderby': 'popularity'}, 'Vedi tutte', P['offerte']),
        ('I sieri', {'category': 'sieri', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('sieri')),
        ('Le creme', {'category': 'creme', 'orderby': 'rating'}, 'Vedi tutte', N.cat_url('creme')),
        ('Kit e routine', {'category': 'kit', 'orderby': 'popularity'}, 'Vedi tutti', N.cat_url('kit')),
    ])


def build_pannello_reparti():
    return N.pannello_reparti(Tpl(ID, 'pannello-reparti'), 'Il negozio', 'Per passo della routine', ('Il kit del mattino,', '−19%.', 'Detergente, siero e crema.', 'I kit', N.cat_url('kit'), 'pexels-7691167.jpg'))


def build_pannello_offerte():
    return N.pannello_offerte(Tpl(ID, 'pannello-offerte'), 'Kit e offerte', 'Fino a domenica.', 'Quelle di oggi finiscono tra:',
                              ('Il siero ialuronico,', '27 €.', 'Era 34 €, fino a domenica.', 'I sieri', N.cat_url('sieri'), 'pexels-9774782.jpg'))


def build_pannello_novita():
    return N.pannello_novita(Tpl(ID, 'pannello-novita'), 'Novità', 'Appena usciti dal laboratorio',
                             [(n, N.cat_url(s)) for s, n in (('detergenti', 'Detergenti'), ('sieri', 'Sieri'), ('creme', 'Creme'), ('trattamenti', 'Trattamenti'), ('corpo', 'Corpo e mani'))],
                             ('Le ricariche,', 'il vetro resta.', 'La ricarica costa il 15% in meno.', 'Come funziona', P['assistenza'] + '#spedizioni', 'pexels-8533222.jpg'))


def build_home():
    t = Tpl(ID, 'home')
    out = [vetrina(t), quadri(t)]
    out.append(t.section([t.one([N.promesse(t, PROMESSE)], anim=None)], bg=solid(C['sala']), padding='small'))
    out.append(t.section([
        N.testata_sezione(t, 'Per passo', 'Da dove cominciare', P['negozio'], 'Tutti i prodotti'),
        t.one([N.categorie(t, colonne=6, raggio_foto=999, ratio='1-1')]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 40}))
    out.append(t.section([
        N.testata_sezione(t, 'I più amati', 'Quelli che si ricomprano', P['negozio']),
        t.one([N.anteprima(t), N.prodotti(t, orderby='popularity', posts_per_page=8, columns=4)]),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 72}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Componi la routine'), N.titolo(t, 'Quattro passi,<br><em>scelti da voi.</em>', size=56),
                      N.testo(t, 'Scorrete le frecce di ogni passo: la card somma il prezzo. Il kit del mattino costa il 19% in '
                                 'meno dei tre prodotti presi uno a uno.', size=17),
                      N.pulsante(t, 'Il kit del mattino', P['offerte'], pad_top=20)], wm='2-5', ws='1-1'),
               t.col([compositore(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_VISO, 'Il gesto', 'Quattro gocce, <em>la mattina.</em>', 'Sul viso asciutto, prima della '
                             'crema: il siero si assorbe in un minuto e la pelle è pronta per il trucco.', overlay=30))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli ingredienti', ['Dieci al massimo,', 'tutti con un perché.'], size=60,
                         tagline='Ogni formula ne ha al massimo dieci: li scegliamo uno per uno e vi diciamo perché ci sono.')]),
        t.one([ingredienti(t)]),
        t.one([N.pulsante(t, 'Tutti gli ingredienti', P['ingredienti'], pieno=False, pad_top=28)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([N.banner(t, 'Il kit del mattino,', '−19%.', 'Mattino, sera e viaggio: fino a domenica a mezzanotte.',
                               'I kit in offerta', P['offerte'], K.photo_bg('pexels-7691167.jpg'))], wm='1-2', ws='1-1'),
               t.col([N.banner(t, 'Le ricariche,', 'il vetro resta.', 'Il flacone si tiene, la ricarica costa il 15% in meno.',
                               'Come funziona', P['assistenza'] + '#spedizioni', K.video_bg(V_ROSA))], wm='1-2', ws='1-1')],
              layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 40}))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'L’offerta finisce tra', color=mix(C['chiaro'], 80)),
                      N.titolo(t, 'Il kit del mattino <em>a 64 €.</em>', size=48, color=C['chiaro']),
                      N.testo(t, 'Gel detergente, siero alla vitamina C e crema giorno: 79 € presi uno a uno.',
                              color=mix(C['chiaro'], 82))], wm='1-2', ws='1-1'),
               t.col([N.conto(t, size=46), N.link(t, 'Prendi il kit', P['offerte'], chiaro=True, align='left', pad_top=18)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=40, valign='center'),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([
        N.testata_sezione(t, 'Le novità', 'Appena usciti dal laboratorio', P['negozio']),
        t.one([N.prodotti(t, orderby='date', posts_per_page=10, columns=4, layout='carousel')]),
    ], padding='default', width='large', style={'padding_top': 72, 'padding_bottom': 64}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8450510.jpg', 'Una chimica misura le gocce con la pipetta', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([N.occhiello(t, 'Il laboratorio'), N.titolo(t, 'Via Saragozza 40,<br><em>dove si fanno a mano.</em>', size=52),
                      N.testo(t, 'Due chimiche e un banco: ogni crema si fa a lotti di duecento vasetti, si prova sulla pelle '
                                 'di chi la fa e solo dopo arriva da voi.', size=17),
                      K.numeri(t, [('10', 'ingredienti al massimo'), ('200', 'vasetti a lotto'), ('0', 'profumi sintetici')],
                               colonne=3, size=46),
                      N.pulsante(t, 'Il laboratorio', P['ingredienti'] + '#laboratorio', pieno=False, pad_top=24)],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_routine():
    t = Tpl(ID, 'routine')
    out = [K.intro(t, 'La routine', ['Detergi, tratta,', 'idrata, proteggi.'], 'Quattro passi, mattina e sera: si comincia dal '
                   'tipo di pelle e si cambia un passo alla volta.', bg=K.video_bg(V_CREMA, overlay=25), size=92)]
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Componi la routine'), N.titolo(t, 'Scegliete ogni passo,<br><em>la card fa il conto.</em>',
                                                                     size=48),
                      N.testo(t, 'Poi aggiungete i prodotti dalla loro scheda. Avete un dubbio? Scriveteci: risponde una delle '
                                 'chimiche del laboratorio.', size=16),
                      N.pulsante(t, 'Chiedi alle chimiche', P['assistenza'] + '#scrivici', pieno=False, pad_top=18)],
                     wm='2-5', ws='1-1'),
               t.col([compositore(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    for i, (passo, titolo, testo, cat) in enumerate([
            ('01 · Detergi', 'Il primo passo', 'Gel per la pelle mista e grassa, latte per quella secca, saponetta per chi vuole '
                                               'meno plastica.', 'detergenti'),
            ('02 · Tratta', 'Il passo che fa la differenza', 'Vitamina C al mattino, retinolo la sera, ialuronico sempre.',
             'sieri'),
            ('03 · Idrata e proteggi', 'L’ultimo passo', 'La crema giorno e, ogni mattina, la protezione SPF 50.', 'creme')]):
        out.append(t.section([
            N.testata_sezione(t, passo, titolo, N.cat_url(cat), 'Tutti'),
            t.one([N.testo(t, testo, size=16)], style={'margin_bottom': 20}),
            t.one([N.prodotti(t, category=cat, posts_per_page=4, columns=4, orderby='popularity')]),
        ], bg=solid(C['sala']) if i % 2 == 0 else None, padding='default', width='large',
            style={'padding_top': 72, 'padding_bottom': 72}))
    return out


def build_ingredienti():
    t = Tpl(ID, 'ingredienti')
    out = [K.intro(t, 'Gli ingredienti', ['Dieci al massimo,', 'tutti spiegati.'], 'Nessun profumo sintetico, nessun colorante, '
                   'nessun ingrediente messo lì per la pubblicità.', bg=K.photo_bg('pexels-15699693.jpg'), size=92)]
    out.append(t.section([t.one([ingredienti(t)])], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 40}))
    out.append(K.domande(t, 'Ingredienti', 'e dubbi.', [
        ('La vitamina C si ossida?', 'La nostra è stabilizzata e il flacone è scuro: dura sei mesi aperto. Se diventa '
                                     'arancione scuro, scriveteci e ve la cambiamo.'),
        ('Il retinolo va bene per tutti?', 'Si comincia due sere a settimana e di giorno si mette la protezione solare. In '
                                           'gravidanza no.'),
        ('Perché al massimo dieci ingredienti?', 'Perché ogni ingrediente in più è una possibilità in più di irritare. E '
                                                 'perché vogliamo potervi spiegare ognuno.'),
        ('Testate sugli animali?', 'Mai. Le proviamo su di noi e su un gruppo di clienti volontarie.')], 'se-ing'))
    out.append(t.section([
        t.row([t.col([N.occhiello(t, 'Il laboratorio'), N.titolo(t, 'Via Saragozza 40, Bologna', size=44),
                      N.testo(t, 'Si può passare il sabato mattina: si annusano le creme, si prova la routine, si porta il flacone '
                                 'vuoto a ricaricare.', size=17),
                      K.specs(t, [('Il sabato', '10 — 13'), ('Le ricariche', 'anche in laboratorio'), ('La consulenza', 'gratis, '
                                                                                                        'su appuntamento')])],
                     wm='1-2', ws='1-1'),
               t.col([t.row([t.col([K.image(t, 'pexels-8450134.jpg', 'Il banco del laboratorio con le boccette', ratio='1/1')],
                                   wm='1-2', ws='1-2'),
                             t.col([K.image(t, 'pexels-8450116.jpg', 'Una mano dosa un estratto con la pipetta', ratio='1/1')],
                                   wm='1-2', ws='1-2')], layout='50-50', gap=14, anim=None)], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='laboratorio', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_negozio():
    t = Tpl(ID, 'negozio')
    return N.pagina_negozio(t, 'Il negozio', 'Tutti i prodotti', 'Filtrate per passo della routine, tipo di pelle e prezzo. '
                            'Il cuore li mette nei preferiti.', attributi='pa_pelle',
                            banner=N.banner(t, 'Non sapete da dove partire?', 'Ve lo diciamo noi.', 'Rispondono le chimiche del '
                                            'laboratorio, per email, in giornata.', 'Chiedi una routine',
                                            P['assistenza'] + '#scrivici', K.photo_bg('pexels-9775170.jpg'), size=36))


def build_offerte():
    t = Tpl(ID, 'offerte')
    extra = [t.section([t.row([
        t.col([N.banner(t, 'Il kit da viaggio,', 'la routine in tasca.', 'Cinque formati da 15 ml nel sacchetto di cotone.',
                        'Vedi il kit', N.cat_url('kit'), K.photo_bg('pexels-8049849.jpg'))], wm='1-2', ws='1-1'),
        t.col([N.banner(t, 'Il siero ialuronico,', '27 €.', 'Tre pesi di acido ialuronico, fino a domenica.',
                        'Vedi il siero', N.cat_url('sieri'), K.photo_bg('pexels-7615819.jpg'))], wm='1-2', ws='1-1')],
        layout='50-50', gap=20, valign='stretch')], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64})]
    return N.pagina_offerte(t, 'Kit e offerte', 'Il kit del mattino, −19%', 'Fino a domenica a mezzanotte: poi tornano a prezzo '
                            'pieno. La spedizione è gratuita da 49 €.', extra=extra)


def build_prodotto():
    return N.pagina_prodotto(Tpl(ID, 'prodotto'), [('truck', 'Spedizione gratuita da 49 €, in due giorni'),
                                                   ('rotate-ccw', 'Reso gratuito in 30 giorni, anche aperto'),
                                                   ('recycle', 'La ricarica costa il 15% in meno'),
                                                   ('message-circle', 'Un dubbio? Rispondono le chimiche')],
                             bundle='La routine completa', miniature='bottom', altezza='560px', bottone='Aggiungi al carrello')


def build_carrello():
    return N.pagina_carrello(Tpl(ID, 'carrello'), 'Spedizione gratuita da 49 €, e un campioncino in ogni pacco.', PROMESSE)


def build_cassa():
    return N.pagina_cassa(Tpl(ID, 'cassa'), 'I tuoi dati, la consegna, il pagamento. Il pagamento è protetto.')


def build_account():
    return N.pagina_account(Tpl(ID, 'account'), 'Gli ordini, le ricariche, gli indirizzi e la tua routine.',
                            ('Un ordine in arrivo?', 'Lo segui dall’assistenza col numero dell’ordine e la tua email.',
                             'Traccia l’ordine', P['assistenza'] + '#spedizioni'))


def build_preferiti():
    return N.pagina_preferiti(Tpl(ID, 'preferiti'), 'Il cuore sulle foto li tiene qui, anche senza account.',
                              'Ancora niente: toccate il cuore sulle foto dei prodotti.')


def build_assistenza():
    return N.pagina_assistenza(Tpl(ID, 'assistenza'), 'Per email e al telefono dalle 9 alle 18; le chimiche rispondono ai dubbi '
                               'sulla pelle.', [
        ('truck', 'Spedizioni', 'Gratis da 49 €, in due giorni. Sotto, 4,90 €.', 'spedizioni'),
        ('rotate-ccw', 'Resi', 'Trenta giorni, anche a flacone aperto.', 'spedizioni'),
        ('recycle', 'Ricariche', 'Il flacone si tiene, la ricarica costa meno.', 'spedizioni'),
        ('message-circle', 'Consulenza', 'Una routine scritta per voi, gratis.', 'scrivici')], [
        ('Quanto costa la spedizione?', 'Niente sopra i 49 €; sotto, 4,90 €. Arriva in due giorni lavorativi.'),
        ('Posso restituire un prodotto aperto?', 'Sì, entro trenta giorni: se non vi trovate bene, lo riprendiamo.'),
        ('Come funzionano le ricariche?', 'Comprate la ricarica in busta e riempite il flacone di vetro: costa il 15% in meno. '
                                          'O portate il flacone in laboratorio.'),
        ('Avete i campioncini?', 'Uno in ogni pacco, scelto in base a quello che avete ordinato.')], CONTATTI,
        campi_extra='Una consulenza sulla pelle\n', foto=('pexels-7321500.jpg', 'Una ragazza in accappatoio fra i vasetti'))


def build_404():
    return N.pagina_404(Tpl(ID, '404'), 'Questa pagina <em>è evaporata.</em>', 'Come una goccia di siero lasciata aperta. '
                        'Cercate il prodotto qui sopra.', K.video_bg(V_ACQUA, overlay=40))


THEME = tema(
    ID, NAME, 'Negozi online', 'Le offerte della routine',
    ['cosmetica', 'skincare', 'bellezza', 'woocommerce', 'e-commerce', 'routine', 'bologna', 'slider pro'],
    'Il negozio online di un laboratorio di cosmetica essenziale, color porcellana: in apertura la fascia delle offerte che scorre '
    '(il kit del mattino, il siero, la crema mani) e le quattro schede coi prodotti, il mega menu coi pannelli; la routine da '
    'comporre che somma il totale, gli ingredienti spiegati, i kit in offerta col conto alla rovescia, la scheda prodotto col '
    'video della texture nella galleria.',
    [('Tutti i prodotti', P['negozio']), ('Novità', P['negozio']), ('Sieri', '{{PAGINA:negozio}}?product_cat=sieri'),
     ('Creme', '{{PAGINA:negozio}}?product_cat=creme'), ('Componi la routine', P['routine']),
     ('Gli ingredienti', P['ingredienti']), ('Offerte', P['offerte'])],
    [('home', 'Home', None), ('negozio', 'Il negozio', 'negozio'), ('routine', 'La routine', 'routine'),
     ('ingredienti', 'Gli ingredienti', 'ingredienti'), ('offerte', 'Kit e offerte', 'offerte'),
     ('assistenza', 'Assistenza', 'assistenza'), ('preferiti', 'I preferiti', 'preferiti'), ('carrello', 'Il carrello', 'carrello'),
     ('cassa', 'La cassa', 'cassa'), ('account', 'Il mio account', 'il-mio-account')],
    HEX, '\'Figtree\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Instrument Serif\', Georgia, serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Instrument Serif', 'Figtree', 'IBM Plex Mono'],
    corsivo_di=['Instrument Serif'], peso_titoli='400', raggio=14, grana=0, titoli_line='1.0', titoli_spazio='-0.01em')
tema_negozio(THEME, NAME)
THEME['preview_v'] = THEME['preview_mobile_v'] = '4'  # anteprime con le testate nuove (2 nov 2026)
pannelli(THEME, NAME, {'Tutti i prodotti': ('pannello-reparti', 'Pannello del negozio'), 'Offerte': ('pannello-offerte', 'Pannello delle offerte'), 'Novità': ('pannello-novita', 'Pannello delle novità')})
THEME['cursor']['style'] = 'perla'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'negozio.json': build_negozio(), 'routine.json': build_routine(), 'ingredienti.json': build_ingredienti(),
                        'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
                        'preferiti.json': build_preferiti(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(),
                        'account.json': build_account(), 'prodotto.json': build_prodotto(), '404.json': build_404(),
                        'pannello-reparti.json': build_pannello_reparti(), 'pannello-offerte.json': build_pannello_offerte(),
                        'pannello-novita.json': build_pannello_novita()}, MEDIA)
    scrivi_prodotti(ID, MEDIA, CATEGORIE, PRODOTTI, RECENSIONI, NOMI, attributi={'pelle': {'nome': 'Tipo di pelle', 'termini': PELLE}},
                    dentro='Il flacone in vetro, la scheda degli ingredienti, un campioncino scelto per voi.')


if __name__ == '__main__':
    main()
