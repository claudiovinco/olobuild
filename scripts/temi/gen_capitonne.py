# -*- coding: utf-8 -*-
"""Genera il tema «Capitonné» (id capitonne): una bottega di tappezzeria a Vicenza: poltrone e divani rifatti, il
capitonné a mano, le tende su misura, le testiere, la scelta dei tessuti in bottega.

Tema scuro e caldo, il velluto: bordeaux profondo, oro, beige e avorio. Titoli in Cinzel, testo in Raleway, dati in
Victor Mono.

Apertura: «la tenda che si raccoglie» (dal 15 nov 2026; prima dieci teli che giravano come una veneziana). Lo slider
resta fermo in cima mentre si scorre: tutta la tela è coperta da una tenda di velluto a pieghe, col titolo sopra.
Scorrendo la tenda si tira verso destra come una tenda vera: il bordo libero parte per primo, le pieghe si stringono
una dopo l'altra e si ammucchiano contro il muro, poi il cordone con la nappa la lega; dietro, il tappezziere al banco.
Cursore: la nappa.
"""
import math
import os
import random
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'capitonne'
NAME = 'Capitonné'

HEX = {
    'primary': '#C9A24A', 'primary_contrast': '#160C0F', 'secondary': '#E7C9A0', 'secondary_contrast': '#160C0F',
    'accent': '#8E2A37', 'dark': '#160C0F', 'light': '#FBF5EE', 'muted': '#1F1316', 'muted_contrast': '#F1E7DC',
    'text': '#F1E7DC', 'text_muted': '#BBA99C', 'background': '#170D10', 'border': '#3A262B', 'link': '#C9A24A',
}

HOME, LAVORI, BOTTEGA, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:lavori}}', '{{PAGINA:la-bottega}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'bottega@capitonne.example', '+39 0444 000 219', 'tel:+390444000219'
LAT, LNG = '45.5480', '11.5460'

V_BANCO, V_CUCITRICE, V_CUCITRICE2, V_TENDA, V_TENDA2, V_VELLUTO, V_ORO, V_MANI, V_TAGLIO = (
    'pexels-video-5759070', 'pexels-video-31650936', 'pexels-video-32205570', 'pexels-video-8334801', 'pexels-video-8347363',
    'pexels-video-7677187', 'pexels-video-7233567', 'pexels-video-6653241', 'pexels-video-4888938')
PHOTOS = [
    'pexels-3926175.jpg', 'pexels-39351124.jpg', 'pexels-3923362.jpg', 'pexels-6394545.jpg', 'pexels-29198180.jpg',
    'pexels-7545792.jpg', 'pexels-4133725.jpg', 'pexels-29198179.jpg', 'pexels-3764576.jpg', 'pexels-6766232.jpg',
    'pexels-6583373.jpg', 'pexels-6583362.jpg', 'pexels-4968584.jpg', 'pexels-36585045.jpg', 'pexels-8082565.jpg',
    'pexels-6527052.jpg', 'pexels-7534552.jpg', 'pexels-20531149.jpg', 'pexels-39536820.jpg', 'pexels-12691635.jpg',
    'pexels-5830633.jpg', 'pexels-33084139.jpg', 'pexels-32850675.jpg', 'pexels-12905114.jpg', 'pexels-6987721.jpg',
    'pexels-15758184.jpg', 'pexels-7045760.jpg', 'pexels-6920601.jpg', 'pexels-920383.jpg', 'pexels-6969774.jpg',
    'pexels-6980731.jpg', 'pexels-7546281.jpg', 'pexels-4300061.jpg', 'pexels-7546294.jpg', 'pexels-37886353.jpg',
    'pexels-31755426.jpg', 'pexels-8447803.jpg', 'pexels-6653990.jpg', 'pexels-36639339.jpg', 'pexels-17462141.jpg',
    'pexels-3689271.jpg', 'pexels-26805218.jpg',
]
VIDEOS = [V_BANCO, V_CUCITRICE, V_CUCITRICE2, V_TENDA, V_TENDA2, V_VELLUTO, V_ORO, V_MANI, V_TAGLIO]
# il velluto capitonné da accarezzare: la foto ricolorata sul bordeaux del tema e la stessa col pelo piegato, più chiara
VELLUTO, VELLUTO_CHIARO = 'capitonne-velluto.jpg', 'capitonne-velluto-chiaro.jpg'
# la tenda dell'apertura (disegni_capitonne_tenda.py, 21 nov 2026): il velluto intero, la tenda legata, il cordone, la nappa
TELO, LEGATA = 'capitonne-tenda-velluto.webp', 'capitonne-tenda-legata.webp'
CORDONE, NAPPA = 'capitonne-cordone.svg', 'capitonne-nappa-2.svg'
MEDIA = (set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} |
         {VELLUTO, VELLUTO_CHIARO, TELO, LEGATA, CORDONE, NAPPA})

K = Kit(ID, MEDIA, raggio=2, peso_titoli='500', corsivo=False, maiuscole_pulsanti=True)
C = K.c
BEIGE, BORDEAUX = C['sec'], C['acc2']
MONO = 'Victor Mono'
VOCI = [('map-pin', 'Contra’ Porti 9 · Vicenza', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Capitonné', logo_size='24', overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Capitonné', 'Bottega di tappezzeria a Vicenza, dal 1958. Poltrone e divani rifatti a mano, il capitonné '
                   'bottone per bottone, le tende cucite su misura.',
                   [('I lavori', LAVORI), ('La bottega', BOTTEGA), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Martedì — sabato 9 — 12:30 e 15 — 19<br>Il lunedì su appuntamento', VOCI,
                   '© 2026 Capitonné · Vicenza · P.IVA 00000000000', [('instagram', 'https://instagram.com')],
                   marchio_size=140)


# ── L'apertura: la tenda che si raccoglie ──
# Una tenda di velluto a pieghe copre tutta la tela. Scorrendo la si tira verso destra come una tenda vera: il bordo
# libero parte per primo e spinge le pieghe, che si stringono una dopo l'altra e si ammucchiano contro il muro; il fondo
# delle pieghe resta indietro mentre la tenda corre (si inclina appena) e all'arrivo oscilla e si ferma. Poi la tenda
# raccolta si stringe a clessidra, il cordone ritorto la avvolge dal muro in avanti e la nappa cade dal nodo, dondola e
# resta lì a dondolare piano. Il bastone d'ottone resta: è il binario. Dietro, la bottega.
# (21 nov 2026, utente: «rendi la tenda più realistica e morbida, lavora anche sull'aggancio giallo finale»: prima erano
# 14 strisce di sfumature CSS uguali e una barra d'oro dritta. Disegni in disegni_capitonne_tenda.py.)
# Il velluto è UN'immagine sola che le 14 pieghe si dividono (background-position): chiusa la tenda è un telo intero.
# Le pieghe e la tenda legata toccano i due bordi e lo Slider Pro le allunga a tutta la scena; cordone e nappa restano
# nella composizione 16:9: per questo la legatura sta a metà altezza (LEGA), l'unico punto in cui coincidono a ogni
# formato di finestra. Al telefono la tenda non si lega: le sue pieghe escono dallo schermo a destra.
PIEGHE = 14
RACCOLTA = 85.0      # dove comincia il mucchio delle pieghe raccolte (% della larghezza)
STRETTA = 0.3        # quanto si stringe in media ogni piega nel mucchio
LEGA = 50.0


def tp(p, **v):
    return dict(p=p, **v)


def corsa(k, dx, stretta, fine, oscilla=0, spegni=None):
    """Il movimento di una piega, campionato ogni 2 punti dello scorrimento con l'andamento costante fra le tappe: con
    l'andamento morbido di serie ogni tappa parte e arriva piano, e a metà corsa la tenda rallentava fino a fermarsi.
    La piega k parte (il bordo libero per primo) e arriva a dx px in fine con una curva liscia (smootherstep); si stringe
    soprattutto alla fine, quando le pieghe si ammucchiano; il fondo resta indietro quanto più la piega corre (rotazione
    dall'angolo in alto) e, se oscilla, dopo l'arrivo passa oltre e torna, smorzandosi, per oscilla punti."""
    parte = 6 + k * 2.2
    incl = 1.7 * (1 - k / PIEGHE) + 0.35
    ultimo = fine + oscilla
    spento = spegni[1] if spegni else 101

    def a(p):
        t = min(1.0, max(0.0, (p - parte) / (fine - parte)))
        e = t * t * t * (t * (6 * t - 15) + 10)
        v = 30 * t * t * (1 - t) * (1 - t) / 1.875                  # velocità relativa, 1 a metà corsa
        r = incl * v
        if oscilla and p > fine:
            q = (p - fine) / oscilla
            r = -0.5 * incl * math.exp(-3.2 * q) * math.sin(2 * math.pi * 1.4 * q)
        o = 100
        if spegni and p >= spegni[0]:
            o = max(0.0, 100 * (1 - (p - spegni[0]) / (spegni[1] - spegni[0])))
        return tp(round(p, 2), x=round(dx * e, 1), sx=round(1 - (1 - stretta) * e * e, 4), r=round(r, 3), o=round(o, 1))
    punti = sorted({0, round(parte, 2)} | {round(parte + i * 2, 2) for i in range(int((ultimo - parte) / 2) + 1)} |
                   {fine, ultimo} | (set(spegni) if spegni else set()) | {100})
    return {'inizio': 0, 'fine': 100, 'andamento': 'costante',
            'tappe': [a(p) for p in punti if p <= ultimo or p in (spegni or ()) or p == 100 or p >= spento]}


def apertura(t):
    m = K.mob
    livelli = []
    rnd = random.Random(1958)
    w = 100.0 / PIEGHE
    wl = w + 0.08                                      # ogni piega copre di poco la vicina: niente fessure
    # nel mucchio le pieghe non sono tutte uguali: passi e strette diverse, così non fanno righe regolari
    pesi = [0.7 + 0.6 * rnd.random() for _ in range(PIEGHE - 1)]
    resto = 100 - RACCOLTA - w * STRETTA
    sinistre = [RACCOLTA + resto * sum(pesi[:k]) / sum(pesi) for k in range(PIEGHE)]
    strette = [STRETTA * (0.85 + 0.3 * rnd.random()) for _ in range(PIEGHE - 1)] + [STRETTA * 1.2]
    fondi = ['background:url(%s?v=1) %.4f%% 0 / %.4f%% 100%% no-repeat;' % (K.img(TELO), 100 * k * w / (100 - wl), 100 * 100 / wl)
             for k in range(PIEGHE)]
    for k in range(PIEGHE):
        dx = round((sinistre[k] - k * w) * 14.4, 1)          # px della tela del computer (1440)
        ombra = 'box-shadow:-16px 0 30px rgba(8,2,4,.55);' if k == 0 else ''
        # computer: le pieghe si raccolgono contro il muro, oscillano e lasciano il posto alla tenda legata
        livelli.append(K.livello('ca-piega%d' % k, tipo='shape', x=round(k * w, 3), y=0, w=round(wl, 3), h=100,
                                 bgColor=BORDEAUX, entra='fadeIn', ritardo=0, durata=500,
                                 customCSS=fondi[k] + ombra + 'transform-origin:0 0;',
                                 scrollMove=corsa(k, dx, strette[k], 68, oscilla=10, spegni=(79, 85)),
                                 responsive={'mobile': {'visible': False}}))
        # telefono: la tenda raccolta copriva il paragrafo (utente, 21 nov 2026: «anzichè chiudere la tenda, falla sparire
        # a destra»): le pieghe corrono a destra raccogliendosi e il mucchio esce dallo schermo, senza legatura
        livelli.append(K.livello('ca-piegat%d' % k, tipo='shape', x=round(k * w, 3), y=0, w=round(wl, 3), h=100,
                                 bgColor=BORDEAUX, visible=False, entra='fadeIn', ritardo=0, durata=500,
                                 customCSS=fondi[k] + ombra + 'transform-origin:0 0;',
                                 scrollMove=corsa(k, round(dx + 18 * 14.4, 1), strette[k], 78, spegni=(78, 79)),
                                 responsive={'mobile': {'visible': True, 'x': round(k * w, 3), 'y': 0, 'width': round(wl, 3),
                                                        'height': 100}}))

    def appare(da, a):
        return {'inizio': 0, 'fine': 100, 'tappe': [tp(0, o=0), tp(da, o=0), tp(a, o=100), tp(100, o=100)]}
    # la tenda raccolta e legata, a clessidra: compare mentre le strisce si spengono (solo al computer)
    livelli += [
        K.livello('ca-legata', tipo='image', imageSrc=K.img(LEGATA), imageAlt='', objectFit='fill', x=80, y=0, w=20, h=100,
                  entra='fadeIn', ritardo=0, durata=300, scrollMove=appare(73, 80),
                  customCSS='filter:drop-shadow(-12px 0 18px rgba(8,2,4,.55));', responsive={'mobile': {'visible': False}}),
    ]
    # il bastone d'ottone in alto: il binario della tenda, resta
    livelli.append(K.livello('ca-bastone', tipo='shape', x=0, y=0, w=100, h=1.6, bgColor='#8E6E2A', entra='fadeIn',
                             ritardo=0, durata=600,
                             customCSS='background:linear-gradient(180deg,#3E2C0C 0,#F6E3A1 26%,#D7B35E 50%,#8E6E2A 76%,'
                                       '#2E2008 100%);box-shadow:0 5px 10px rgba(0,0,0,.55);',
                             responsive={'mobile': {'height': 0.8}}))
    # il cordone avvolge la tenda dal muro in avanti (la maschera si apre da destra), poi la nappa cade dal nodo e dondola
    avvolge = {'inizio': 0, 'fine': 100, 'tappe': [tp(0, o=0, ml=100), tp(76, o=0, ml=100), tp(77, o=100, ml=100),
                                                    tp(84, o=100, ml=0), tp(100, o=100, ml=0)]}
    dondola = {'inizio': 0, 'fine': 100, 'tappe': [tp(0, o=0, y=-30, r=0), tp(82, o=0, y=-30, r=0), tp(85, o=100, y=0, r=6),
                                                    tp(88, o=100, y=0, r=-3.2), tp(91, o=100, y=0, r=1.4),
                                                    tp(94, o=100, y=0, r=-0.5), tp(97, o=100, y=0, r=0),
                                                    tp(100, o=100, y=0, r=0)]}
    livelli += [
        K.livello('ca-cordone', tipo='image', imageSrc=K.img(CORDONE), imageAlt='', objectFit='contain',
                  objectPosition='left center', x=88.5, y=LEGA - 3, w=11.5, h=6, entra='fadeIn', ritardo=0, durata=300,
                  scrollMove=avvolge, responsive={'mobile': {'visible': False}}),
        K.livello('ca-nappa', tipo='image', imageSrc=K.img(NAPPA), imageAlt='', objectFit='contain',
                  objectPosition='center top', x=88.5, y=LEGA - 0.4, w=3.0, h=15.7, entra='fadeIn', ritardo=0, durata=300,
                  scrollMove=dondola, customCSS='transform-origin:50% 0;filter:drop-shadow(0 6px 8px rgba(0,0,0,.5));',
                  responsive={'mobile': {'visible': False}}),
    ]
    # Al telefono i testi piccoli non scendono sotto il carattere leggibile: a 320 px l'occhiello va su due righe e il
    # paragrafo su quattro, da 360 in su una e tre. Occhiello e paragrafo stanno in un riquadro alto quanto a 320 (l'occhiello
    # in fondo al suo, il paragrafo al centro del suo): lo spazio in più dei telefoni larghi va attorno ai testi, non tutto
    # sotto il paragrafo (utente, 21 nov 2026: «testo un po troppo appiccicato»). Al computer l'altezza è automatica e il
    # riquadro non cambia niente. Al tablet il paragrafo va su tre righe: i pulsanti scendono un poco.
    testi = [
        K.livello('ca-o', contenuto='Capitonné · tappezzeria e tende · Vicenza', x=6, y=24, w=44, size=12, peso='500',
                  colore=mix(C['chiaro'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  customCSS='display:flex;flex-direction:column;justify-content:flex-end;',
                  responsive={'mobile': {'fontSize': m(10), 'y': 10.6, 'x': 7, 'width': 90, 'height': 3.5}}),
        K.livello('ca-t1', contenuto='Ogni bottone', tag='h1', x=6, y=30, w=56, size=76, peso='500', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=1, entra='maskFromBottom', ritardo=350, durata=1100,
                  responsive={'mobile': {'fontSize': m(34), 'y': 15.3, 'x': 7, 'width': 90}}),
        K.livello('ca-t2', fontFamily='Cinzel', contenuto='tirato a mano.', tag='p', x=6, y=41, w=56, size=76, peso='500',
                  colore=C['acc'], lineHeight=1.0, letterSpacing=1, entra='maskFromBottom', ritardo=550, durata=1100,
                  responsive={'mobile': {'fontSize': m(34), 'y': 19.5, 'x': 7, 'width': 90}}),
        K.livello('ca-s', contenuto='Poltrone e divani rifatti come erano, il capitonné bottone per bottone, le tende cucite '
                                    'sulle vostre finestre. In bottega dal 1958.', x=6, y=55, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 88), lineHeight=1.6, ritardo=800,
                  customCSS='display:flex;flex-direction:column;justify-content:center;',
                  responsive={'mobile': {'fontSize': m(13), 'y': 24.6, 'x': 7, 'width': 86, 'height': 13.4}}),
        K.livello('ca-b', tipo='button', contenuto='I lavori', x=6, y=69, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=17, borderRadius=2, letterSpacing=3, textTransform='uppercase', entra='fadeInUp',
                  ritardo=1000, buttonUrl=LAVORI, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'y': 72.5}, 'mobile': {'y': 39.2, 'x': 7, 'fontSize': m(11)}}),
        K.livello('ca-l', tipo='button', contenuto='Un preventivo', x=16.7, y=69, w='auto', size=12, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=2, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), letterSpacing=3, textTransform='uppercase', entra='fadeInUp', ritardo=1100,
                  buttonUrl=CONTATTI, hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 21, 'y': 72.5}, 'mobile': {'y': 39.2, 'x': 40, 'fontSize': m(11)}}),
    ]
    veli = K.veli('ca-velo', 'sinistra', 40, 66, colore=C['nero'], forza=88, mobile_alto=43)
    sl = [K.diapositiva('ca-1', K.sfondo_video(V_BANCO, overlay=0.18, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def velluto(t):
    """Il velluto da accarezzare (15 nov 2026): passando il mouse il pelo si piega e si schiarisce, poi in pochi secondi si
    rialza e torna scuro (Gratta e scopri al passaggio, pennello morbido, copertura che ricresce). Sul telefono col dito.
    Niente scritte da scoprire e niente istruzioni: è il velluto di una poltrona, si tocca e basta."""
    pelo = t.classic('scratchfx', {
        'image': K.img(VELLUTO_CHIARO), 'object_fit': 'cover', 'object_position': 'center center',
        'prize_eyebrow': '', 'prize_title': '', 'prize_text': '', 'hint': '', 'show_button': False, 'coupon_code': '',
        'remember': False, 'cover_type': 'image', 'cover_image': K.img(VELLUTO), 'cover_color': '#4A0F1C',
        'cover_color2': '#4A0F1C', 'cover_text': '', 'brush_size': 70, 'reveal_threshold': 0, 'reset_on_leave': False,
        'al_passaggio': True, 'pennello_morbido': True, 'ricresce': 7, 'height_mode': 'aspect', 'aspect': '16/10',
        'border_radius': 2, 'under_bg': '#4A0F1C', 'shadow': 'none', 'max_width': 1000}, style={})
    return t.section([
        t.row([t.col([K.sechead(t, 'I tessuti', ['Seicento tessuti', 'da toccare.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Velluti, lini, damaschi, pelli: il campionario sta in bottega, e si sceglie con le mani.',
                             size=17)],
                     wm='2-5', ws='1-1'),
               t.col([pelo], wm='3-5', ws='1-1')], layout='custom', custom='36,64', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130})


# ── I passaggi fra le sezioni (21 nov 2026, utente: «usa delle transizioni di sezione, tra le ultime morbide e scorrevoli
# che hai creato») ──
# I lavori, in home, col Rullo: un rotolo di velluto bordeaux passa sulla pagina e la sezione dopo compare dove è passato,
# come una pezza srotolata sul banco; il rotolo arriva una volta da destra e una da sinistra. Le tre settimane di una
# poltrona, nella bottega, col Mazzo di carte: ogni settimana vola via e scopre la successiva.
ROTOLO = '#6E1E2B'


def scheda(t, blocco, destra):
    cols = [t.col(blocco, wm='1-2', ws='1-1', style={'bg': solid(C['fondo']), 'padding_top': 40, 'padding_right': 44,
                                                      'padding_bottom': 38, 'padding_left': 44, 'border_radius': 0}),
            t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.row(cols, layout='custom', custom='44,56', valign='center', anim=None)


def lavoro(t, numero, nome, testo, prezzo, foto, verso, pos='center center', destra=False):
    blocco = [K.label(t, numero, color=C['acc']),
              K.headline(t, nome, size=54, tag='h2', pad_bottom=14, sizes=(42, 32)),
              K.text(t, testo, size=16),
              K.text(t, prezzo, size=14, color=C['acc'], pad=(10, 0, 0, 0))]
    return t.section([scheda(t, blocco, destra)], bg=K.photo_bg(foto, overlay=6, position=pos), padding='large',
                     width='large', sticky_effect='rullo', sticky_h_dir=verso, sticky_colore=ROTOLO, sticky_h_align='center',
                     style={'padding_top': 100, 'padding_bottom': 100})


def settimana(t, numero, nome, titolo, testo, cifra, foto, pos='center center', destra=False):
    blocco = [K.label(t, 'Settimana %s · %s' % (numero, nome), color=C['acc']),
              K.headline(t, titolo, size=54, tag='h2', pad_bottom=14, sizes=(42, 32)),
              K.text(t, testo, size=16),
              K.text(t, cifra, size=14, color=C['acc'], pad=(10, 0, 0, 0))]
    return t.section([scheda(t, blocco, destra)], bg=K.photo_bg(foto, overlay=6, position=pos), padding='large',
                     width='large', sticky_effect='mazzo', sticky_h_dir='destra', sticky_h_align='center',
                     style={'padding_top': 100, 'padding_bottom': 100})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Capitonné · Vicenza, dal 1958', ['La poltrona del nonno,', 'come nuova.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'La smontiamo fino al legno, rifacciamo le cinghie e le molle legate a mano, la imbottiamo '
                                'col crine e la rivestiamo col tessuto che scegliete in bottega. Ci vogliono tre settimane: '
                                'dura altri cinquant’anni.', size=17),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('1958', 'L’apertura'), ('600', 'Tessuti in campionario'), ('3', 'Settimane per una poltrona'),
                            ('50', 'Anni di durata')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out += [
        lavoro(t, 'I · Le poltrone', 'Rifatte fino al legno', 'Cinghie nuove, molle legate a mano con lo spago, crine e '
               'imbottitura nuova, poi il tessuto che scegliete in bottega.', 'Da 480 €', 'pexels-39351124.jpg', 'destra', pos='22% center',
               destra=True),
        lavoro(t, 'II · Il capitonné', 'Bottone per bottone', 'Il tessuto tirato e piegato a mano, i bottoni rivestiti '
               'nello stesso velluto. Una Chesterfield vuole una settimana sola per lo schienale.', 'Da 900 €',
               'pexels-12905114.jpg', 'sinistra'),
        lavoro(t, 'III · Le tende', 'Cucite su misura', 'Prendiamo le misure a casa con i campioni, cuciamo in bottega, '
               'montiamo bastoni e binari.', 'Da 45 € al metro', 'pexels-8082565.jpg', 'destra', destra=True),
        lavoro(t, 'IV · Le testiere', 'Imbottite', 'Per il letto, nel tessuto che volete: dritte, centinate o col '
               'capitonné.', 'Da 380 €', 'pexels-7546281.jpg', 'sinistra', destra=True),
    ]
    out.append(velluto(t))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-37886353.jpg', 'Le mani del tappezziere sul tessuto', ratio='4/5')], wm='2-5',
                     ws='1-1'),
               t.col([P.citazione(K, t, 'Era la poltrona dove leggeva mio padre. L’hanno rifatta con lo stesso velluto verde, '
                                          'e quando mi ci siedo ha ancora la sua forma.', 'Giovanna, Bassano del Grappa',
                                  size='38')], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72,
              valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_TENDA, 'Le tende', 'Le misure <em>le prendiamo noi.</em>',
                          'Veniamo a casa con i campioni, cuciamo in bottega, montiamo bastoni e binari.', 'Un preventivo',
                          CONTATTI, overlay=18, video=True))
    return out


def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [K.intro(t, 'I lavori', ['Sedute, tende,', 'testiere.'],
                   'I prezzi sono indicativi, per una poltrona o un metro di tenda: il preventivo si fa guardando il pezzo, '
                   'in bottega o a casa vostra.', bg=K.video_bg(V_CUCITRICE), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il listino', ['Per orientarsi,', 'poi il preventivo.'], size=56,
                         tagline='Il tessuto è a parte: da 30 a 300 € al metro.')]),
        t.row([t.col([P.listino(K, t, [
            ('Poltrona rifatta', 'Fino al legno, molle e crine', '480 €', 'pexels-3923362.jpg', ''),
            ('Divano rifatto', 'Tre posti, fino al legno', '1.200 €', 'pexels-4133725.jpg', ''),
            ('Capitonné', 'Una poltrona Chesterfield', '900 €', 'pexels-6987721.jpg', 'il nostro mestiere'),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Sedia', 'La seduta, cinghie e imbottitura', '120 €', 'pexels-29198180.jpg', ''),
            ('Tenda su misura', 'Al metro, cucita e montata', '45 €', 'pexels-6527052.jpg', ''),
            ('Testiera imbottita', 'Matrimoniale', '380 €', 'pexels-4300061.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I lavori', ['Girateli:', 'dietro c’è il come.'], size=56)]),
        P.carte(K, t, [
            ('pexels-3926175.jpg', 'Le poltrone', 'Rifatte fino al legno', 'Cinghie, molle legate a mano, crine e '
             'imbottitura nuova. Da 480 €.'),
            ('pexels-12905114.jpg', 'Il capitonné', 'Bottone per bottone', 'Il tessuto tirato e piegato a mano, i bottoni '
             'rivestiti uguali. Da 900 €.'),
            ('pexels-8082565.jpg', 'Le tende', 'Cucite su misura', 'Prese le misure a casa, montate da noi. Da 45 € al '
             'metro.'),
            ('pexels-7546281.jpg', 'Le testiere', 'Imbottite', 'Per il letto, nel tessuto che volete. Da 380 €.'),
        ], altezza='420'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Prima e dopo', ['Le sedute', 'tornate a casa.'], size=56)]),
        P.fila_foto(K, t, [('pexels-39351124.jpg', 'Una poltrona rivestita'), ('pexels-3764576.jpg', 'Un divano Chesterfield '
                                                                                                    'in pelle'),
                           ('pexels-15758184.jpg', 'Il capitonné di un divano'), ('pexels-6394545.jpg', 'Una poltrona di '
                                                                                                       'velluto')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Tre generazioni,', 'gli stessi attrezzi.'],
                   'La bottega è in contra’ Porti dal 1958: il banco, le forbici del nonno, il martello col magnete per le '
                   'puntine. E il campionario dei tessuti, che occupa una parete.', bg=K.photo_bg('pexels-20531149.jpg'),
                   size=92)]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Una poltrona', ['Tre settimane,', 'tre passaggi.'], size=56, layout='stack',
                                size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Dal legno nudo al tessuto tirato: ogni poltrona passa dal banco tre volte, una alla '
                                'settimana.', size=17)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 90}))
    out += [
        settimana(t, 'I', 'Smontare', 'Fino al legno', 'Si toglie tutto: tessuto, imbottitura, molle. Il legno si incolla e '
                  'si rinforza dove serve.', '200 puntine tolte', 'pexels-12691635.jpg'),
        settimana(t, 'II', 'Imbottire', 'Molle e crine', 'Le cinghie nuove, le molle legate a mano con lo spago, il crine, '
                  'l’ovatta.', '8 nodi per ogni molla', 'pexels-29198180.jpg', destra=True),
        settimana(t, 'III', 'Rivestire', 'Il tessuto tirato a mano', 'Il tessuto si taglia, si tira, si fissa; le cuciture '
                  'a mano, i bottoni del capitonné.', 'Una poltrona come nuova', 'pexels-33084139.jpg'),
    ]
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi tira', 'i bottoni.'], size=56)]),
        K.squadra(t, [
            ('pexels-17462141.jpg', 'Bruno Dal Lago', 'Tappezziere, seconda generazione', 'In bottega dal 1974. Il capitonné '
             'lo fa ancora lui.', 'center 25%'),
            ('pexels-3689271.jpg', 'Marco Dal Lago', 'Tappezziere', 'Divani, sedie e le poltrone d’epoca.', 'center 25%'),
            ('pexels-26805218.jpg', 'Stefano Zanella', 'Tende e montaggi', 'Prende le misure a casa e monta bastoni e binari.',
             'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_MANI, 'Le mani', 'Il tessuto <em>si ascolta.</em>',
                             'Quanto tirarlo, dove piegarlo: lo dicono le mani, dopo trent’anni.', overlay=22))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Una foto della poltrona,', 'il preventivo.'],
                   'Mandateci una foto e le misure: vi rispondiamo con un prezzo indicativo, poi venite in bottega a '
                   'scegliere il tessuto.', bg=K.photo_bg('pexels-36585045.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Il lavoro', 'lavoro', opzioni='Una poltrona\nUn divano\nIl capitonné\nDelle tende\nUna '
                     'testiera\nAltro'),
             K.campo('textarea', 'Il pezzo, le misure, i desideri', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Il preventivo', ['Due righe e una foto,', 'il resto in bottega.'],
                                'Vi rispondiamo entro due giorni con un prezzo indicativo.', VOCI, campi,
                                'Capitonné — preventivo', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(K.domande(t, 'Prima di portarla', ['Le domande', 'di chi ci porta una poltrona.'], [
        ('Venite a ritirarla?', 'Sì, a Vicenza e provincia: il ritiro e la riconsegna costano 40 €.'),
        ('Posso portare il mio tessuto?', 'Certo: ne servono circa sei metri per una poltrona.'),
        ('Si può tenere la vecchia imbottitura?', 'Se il crine è sano sì: lo puliamo e lo riusiamo.'),
        ('Quanto tempo ci vuole?', 'Tre settimane per una poltrona, cinque per un divano.'),
    ], 'ca-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In contra’ Porti,', 'la vetrina coi velluti.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'martedì — sabato 9 — 12:30 e 15 — 19'), ('A piedi', 'da piazza dei Signori, '
                                                                                                   'cinque minuti'),
                                  ('In auto', 'parcheggio Canove, poi due passi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Capitonné · bottega', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · tenda tirata', color=K.acc_media, align='center'),
                       K.headline(t, 'Dietro questa tenda <em>non c’è niente.</em>', size=76, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La pagina non c’è. Torniamo in bottega.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ORO, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Casa e design', 'Il sipario di velluto',
    ['tappezzeria', 'tende su misura', 'capitonné', 'artigianato', 'vicenza', 'scuro', 'slider pro'],
    'Una bottega di tappezzeria, velluto bordeaux e oro: in apertura una tenda di velluto a pieghe copre tutto, e '
    'scorrendo si raccoglie di lato come una tenda vera, piega dopo piega, finché il cordone ritorto la lega e la nappa '
    'dondola; dietro, il tappezziere al banco. I lavori passano come una pezza di velluto srotolata, il velluto si '
    'accarezza, le tre settimane di una poltrona si sfogliano come un mazzo di carte; poi il listino e il preventivo.',
    [('I lavori', LAVORI), ('La bottega', BOTTEGA), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('lavori', 'I lavori', 'lavori'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Raleway\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cinzel\', Georgia, serif',
    '\'Victor Mono\', ui-monospace, Menlo, monospace', ['Raleway', 'Cinzel', 'Victor Mono'], corsivo_di=[],
    peso_titoli='500', raggio=2, grana=4, titoli_line='1.02', titoli_spazio='0.01em')
THEME['cursor']['style'] = 'nappa'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = '#7A1F2B'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'lavori.json': build_lavori(), 'la-bottega.json': build_bottega(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
