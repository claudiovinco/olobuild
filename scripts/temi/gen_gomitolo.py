# -*- coding: utf-8 -*-
"""Genera il tema «Gomitolo» (id gomitolo): un maglificio di Biella di maglioni, cardigan, berretti e sciarpe di lana
biellese, fatti a mano o sulla macchina rettilinea. Con ogni maglia arriva un gomitolo dello stesso filo, per rammendarla.

Un filo di lana attraversa il sito: fra una sezione e l'altra si srotola da solo, disegnandosi quando arriva sullo schermo,
e si riavvolge quando esce (SVG Animator «disegna», con «cancella all'uscita»). Apertura: la maglia che si fa da sola:
sul video dei ferri, in fondo alla tela, cinque file di punti a V nei colori della lana compaiono un punto dopo l'altro,
fila dopo fila, come una sciarpa sui ferri; sopra il titolo.

Tema chiaro, avena, terracotta, salvia e senape. Titoli in Fredoka, testo in Nunito Sans, dati in Azeret Mono. Logo: il
gomitolo col filo che corre sotto la parola. Cursore: il gomitolo (rotola e srotola il filo).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'gomitolo'
NAME = 'Gomitolo'

HEX = {
    'primary': '#A8402A', 'primary_contrast': '#FFFFFF', 'secondary': '#6B8F71', 'secondary_contrast': '#FFFFFF',
    'accent': '#E2A33B', 'dark': '#3A2A22', 'light': '#FFFFFF', 'muted': '#F3E4D2', 'muted_contrast': '#3A2A22',
    'text': '#3A2A22', 'text_muted': '#6B5A4E', 'background': '#FBF3E8', 'border': '#E3CDB5', 'link': '#A8402A',
}
LANE = ['#D8573A', '#E2A33B', '#8FB596', '#F4E6D0', '#C04A6A']

HOME, MAGLIE, LANA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:maglie}}', '{{PAGINA:la-lana}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@gomitolo.example', '+39 015 000 4411', 'tel:+390150004411'
LAT, LNG = '45.5660', '8.0530'

V_FERRI, V_ROSSI, V_MAGLIA, V_GOMITOLI, V_ABBRACCIO, V_PECORE, V_MONTONE, V_GREGGE, V_MACCHINA = (
    'pexels-video-7023754', 'pexels-video-34781318', 'pexels-video-7696971', 'pexels-video-6653607', 'pexels-video-10474566',
    'pexels-video-20463982', 'pexels-video-8855730', 'pexels-video-20469064', 'pexels-video-35469650')
VIDEOS = [V_FERRI, V_ROSSI, V_MAGLIA, V_GOMITOLI, V_ABBRACCIO, V_PECORE, V_MONTONE, V_GREGGE, V_MACCHINA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
MAG = f('14641437', '7585673', '5789010', '5788305', '14641438', '7585736', '13889763')
GOM = f('6766124', '18405400', '39647386', '33058635', '33058633', '19902866')
FER = f('5903547', '9077817', '7585811', '5788150', '38390726', '7585857')
MOD = f('29413405', '33617908', '20025026', '11055811', '28366156', '7576389')
PEC = f('4390263', '15801658', '37852838')
TRA = f('1487703', '14933287', '6275947', '14933288', '1487711')
CAR = f('5712106', '9872334', '6812207', '13745235', '12029974')
BER = f('6386211', '15490507', '7301578', '10234581', '7585201')
MAT = f('3693218', '33653862', '37031147', '3693229')
BOT = f('7585641', '6347956', '6347948', '40348298', '5711472')
LOGO_S = 'logo-gomitolo-scuro.png'
PHOTOS = MAG + GOM + FER + MOD + PEC + TRA + CAR + BER + MAT + BOT
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, 'logo-gomitolo-chiaro.png'}

K = Kit(ID, MEDIA, raggio=20, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#F2B544')
C = K.c
MONO = 'Azeret Mono'
VOCI = [('map-pin', 'Via Italia 33 · Biella', NEGOZI + '#maglificio'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]

# Il filo: un tratto ondulato con un nodo a metà, largo quanto la pagina
FILO = ('<svg viewBox="0 0 1200 140" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round">'
        '<path d="M-10 80 C120 30 220 120 360 76 S560 30 640 70 C700 100 720 40 690 30 C655 20 650 96 710 104 C790 114 860 50 '
        '960 62 S1120 120 1210 70"/></svg>')


def filo(t, colore, verso=False):
    """Una sezione sottile col filo di lana che si srotola arrivando sullo schermo e si riavvolge uscendo."""
    return t.section([t.one([t.classic('svganimator', {
        'source_type': 'code', 'svg_code': FILO, 'anim_type': 'draw', 'anim_sequence': 'sync', 'trigger': 'viewport',
        'duration': 2200, 'delay': 0, 'easing': 'ease-in-out', 'stroke_width': '5', 'stroke_color': colore,
        'stroke_linecap': 'round', 'show_fill': False, 'reverse': verso, 'erase_on_leave': True, 'loop': False,
        'max_width': '100%', 'alignment': 'center'}, style={})], anim=None)],
        width='fullbleed', padding='remove-vertical', style={'padding_top': 10, 'padding_bottom': 10})


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Gomitolo', LOGO_S, 190, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '1148/222', 'Gomitolo fa a Biella maglioni, cardigan, berretti e sciarpe di lana biellese, a mano '
                   'o sulla rettilinea. Con ogni maglia, un gomitolo dello stesso filo per rammendarla.',
                   [('Le maglie', MAGLIE), ('La lana', LANA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'Il maglificio', 'Via Italia 33, Biella<br>Martedì — sabato 9:30 — 12:30 e 15 — 19', VOCI,
                   '© 2026 Gomitolo · Biella · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')],
                   larghezza=440, fondo=C['sala'])


# ── L'apertura: la maglia che si fa da sola ──
def apertura(t):
    m = K.mob
    PUNTI = 'V' * 36
    file_ = []
    for k, col in enumerate(LANE):
        file_.append(K.livello('go-fila%d' % k, contenuto=PUNTI, x=0, y=62 + k * 6.2, w=100, size=48, peso='700', colore=col,
                               fontFamily='Fredoka', lineHeight=1.0, letterSpacing=0, textAlign='center', entra='fadeIn',
                               ritardo=900 + k * 1000, durata=500,
                               charAnim={'split': 'chars', 'direction': 'forward', 'stagger': 24, 'offsetY': -26, 'scale': 0.3,
                                         'opacity': 0},
                               customCSS='white-space:nowrap;text-shadow:0 2px 0 rgba(58,42,34,.35);',
                               responsive={'mobile': {'fontSize': m(14), 'y': 80 + k * 2.6, 'x': 0, 'width': 100}}))
    testi = [
        K.livello('go-o', contenuto='Gomitolo · maglie di lana · Biella', x=6, y=12, w=50, size=13, peso='500',
                  colore=mix('#FFFFFF', 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 6, 'x': 7, 'width': 86}}),
        K.livello('go-t1', contenuto='Fatto', tag='h1', x=6, y=17, w=60, size=128, peso='600', colore='#FFFFFF', lineHeight=0.95,
                  letterSpacing=-3, entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'fontSize': m(64), 'y': 9, 'x': 7, 'width': 90}}),
        K.livello('go-t2', fontFamily='Fredoka', contenuto='a maglia.', tag='p', x=6, y=33, w=60, size=128, peso='600',
                  colore='#F2B544', lineHeight=0.95, letterSpacing=-3, entra='maskFromBottom', ritardo=500, durata=1000,
                  responsive={'mobile': {'fontSize': m(64), 'y': 15, 'x': 7, 'width': 90}}),
        K.livello('go-s', contenuto='Maglioni, cardigan e berretti di lana biellese. Guardate in fondo: la sciarpa si fa adesso, '
                                    'un punto alla volta.', x=6, y=51, w=40, size=17, peso='600', colore=mix('#FFFFFF', 94),
                  lineHeight=1.55, ritardo=700, responsive={'tablet': {'width': 44}, 'mobile': {'fontSize': m(13), 'y': 24, 'x': 7, 'width': 86}}),
        K.livello('go-b', tipo='button', contenuto='Le maglie', x=52, y=51, w='auto', size=15, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=17, borderRadius=999, entra='fadeInUp', ritardo=900, buttonUrl=MAGLIE,
                  hover={'bgColor': '#3A2A22', 'duration': 250}, responsive={'tablet': {'x': 56}, 'mobile': {'y': 31, 'x': 7, 'fontSize': m(12)}}),
        K.livello('go-l', tipo='button', contenuto='La lana', x=62, y=51, w='auto', size=15, peso='700', colore='#3A2A22',
                  bgColor='#FBF3E8', padding=17, borderRadius=999, entra='fadeInUp', ritardo=1000, buttonUrl=LANA,
                  hover={'bgColor': '#F2B544', 'duration': 250},
                  responsive={'tablet': {'x': 70}, 'mobile': {'y': 31, 'x': 40, 'fontSize': m(12)}}),
    ]
    veli = K.veli('go-v', 'sinistra', 44, 74, colore=C['nero'], forza=80, mobile_alto=34)
    veli += K.veli('go-w', 'basso', 24, 40, colore=C['nero'], forza=70, mobile_da=78)
    sl = [K.diapositiva('go-1', K.sfondo_video(V_FERRI, overlay=0.18, colore=C['nero']), veli + testi + file_, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showProgressBar=False, autoplay=False, parallax=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Gomitolo · Biella, dal 1961', ['Una maglia', 'e il suo gomitolo.'], size=72, layout='stack',
                                size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'La lana la prendiamo dalle pecore delle valli biellesi, la filiamo a Valle Mosso e la '
                                'lavoriamo a mano o sulla rettilinea della nonna. Ogni maglia arriva con un gomitolo dello stesso '
                                'filo: quando si buca il gomito, si rammenda col suo.', size=17),
                      K.button(t, 'La lana', LANA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 70}))
    out.append(filo(t, LANE[0]))
    out.append(t.section([
        t.one([K.sechead(t, 'Le maglie', ['Quattro modi', 'di stare al caldo.'], size=60)]),
        t.one([M.capi(K, t, [
            (MAG[0], 'Maglioni', 'Nonna', '', 'Lana grossa, punto treccia, a mano.', '190 €', 'col gomitolo'),
            (CAR[1], 'Cardigan', 'Bottega', '', 'Bottoni di corno, tasche a toppa.', '170 €', ''),
            (BER[2], 'Berretti', 'Pecora', '', 'Lana non tinta, il colore della pecora.', '45 €', ''),
            (MOD[2], 'Maglioni', 'Valle', '', 'Collo alto, rettilinea, lana merino.', '150 €', 'nuovo')], MAGLIE)]),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 70}))
    out.append(filo(t, LANE[2], verso=True))
    out.append(t.section([
        t.one([K.sechead(t, 'Dal gomitolo alla maglia', ['Tre fili,', 'una maglia.'], size=60)]),
        P.carte(K, t, [(PEC[1], 'La pecora', 'Biellese, al pascolo', 'Le tosiamo a giugno: la lana è grossa e calda, non punge.'),
                       (MAT[0], 'La matassa', 'Filata a Valle Mosso', 'La lana si lava, si carda, si fila in matasse da cento '
                        'grammi.'),
                       (FER[3], 'I ferri', 'Una maglia in tre giorni', 'Il maglione Nonna si fa a mano: tre giorni, ventimila '
                        'punti.'),
                       (GOM[1], 'Il gomitolo', 'Arriva con la maglia', 'Lo stesso filo, per rammendare il gomito tra dieci '
                        'anni.')], altezza='400'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(filo(t, LANE[1]))
    out.append(K.video_frase(t, V_GOMITOLI, 'La lana', 'Si <em>srotola,</em> si riavvolge.',
                             'Come il filo di questo sito: arriva quando scorrete, torna nel gomitolo quando ve ne andate.',
                             overlay=20))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Al caldo,', 'da Biella.'], size=60)]),
        P.fila_foto(K, t, [(MOD[0], 'Il maglione rosa'), (CAR[0], 'Il cardigan in riva al mare'), (BER[0], 'Il berretto rosso'),
                           (MAG[4], 'Il maglione bianco')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 70}))
    out.append(filo(t, LANE[4], verso=True))
    out.append(M.lettera(K, t, 'La lettera', ['Un punto nuovo', 'ogni mese.'],
                         'Le maglie nuove, un punto da imparare, i negozi dove arrivano. Una volta al mese.',
                         bg=solid(C['sala'])))
    return out


def build_maglie():
    t = Tpl(ID, 'maglie')
    out = [K.intro(t, 'Le maglie', ['Otto maglie,', 'otto gomitoli.'],
                   'A mano o sulla rettilinea, sempre con la lana delle valli. Ogni maglia arriva col suo gomitolo. I prezzi '
                   'sono quelli consigliati.', bg=K.photo_bg(TRA[0]), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Maglioni e cardigan', ['Fatti', 'per durare.'], size=60)]),
        t.one([M.capi(K, t, [
            (MAG[0], 'Maglioni', 'Nonna', '', 'Punto treccia, fatto a mano.', '190 €', ''),
            (MOD[2], 'Maglioni', 'Valle', '', 'Collo alto, merino, rettilinea.', '150 €', 'nuovo'),
            (CAR[1], 'Cardigan', 'Bottega', '', 'Bottoni di corno, tasche a toppa.', '170 €', ''),
            (CAR[3], 'Cardigan', 'Lungo', '', 'Fino al ginocchio, da sopra il cappotto.', '210 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 70}))
    out.append(filo(t, LANE[0]))
    out.append(t.section([
        t.one([K.sechead(t, 'Berretti e sciarpe', ['Il resto', 'del gomitolo.'], size=60)]),
        t.one([M.capi(K, t, [
            (BER[2], 'Berretti', 'Pecora', '', 'Lana non tinta.', '45 €', ''),
            (BER[4], 'Berretti', 'Pompon', '', 'Col pompon di lana avanzata.', '49 €', ''),
            (GOM[4], 'Sciarpe', 'Due metri', '', 'Punto legaccio, due metri.', '79 €', ''),
            (TRA[3], 'Calze', 'Scarpone', '', 'Lana grossa, da montagna.', '29 €', 'ultime')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MACCHINA, 'La rettilinea', 'La macchina <em>della nonna.</em>',
                             'Una rettilinea del 1961, ancora in bottega: fa un maglione in quattro ore.', overlay=22))
    return out


def build_lana():
    t = Tpl(ID, 'la-lana')
    out = [K.intro(t, 'La lana', ['Dalla pecora', 'al gomito.'],
                   'Le pecore pascolano sopra Biella, la lana si fila a Valle Mosso, le maglie si fanno in bottega. Venti '
                   'chilometri in tutto.', bg=K.video_bg(V_PECORE), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come nasce', ['Tre passaggi,', 'venti chilometri.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'la tosatura', PEC[0], 'Tosare', 'Le pecore', 'biellesi', 'A giugno, in alpeggio: ogni pecora dà tre chili di '
             'lana.', '3', 'chili a pecora'),
            ('2', 'la filatura', MAT[2], 'Filare', 'A Valle', 'Mosso', 'La lana si lava, si carda e si fila in matasse.', '100',
             'grammi a matassa'),
            ('3', 'la maglia', FER[0], 'Lavorare', 'A mano', 'o in macchina', 'I maglioni Nonna a mano, gli altri sulla '
             'rettilinea.', '20.000', 'punti')], icona='heart')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 70}))
    out.append(filo(t, LANE[2]))
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi tiene', 'i ferri.'], size=60)]),
        K.squadra(t, [(MOD[1], 'Rosanna Ferraris', 'Fondatrice', 'Ha imparato a sei anni, sui ferri della nonna.', 'center 25%'),
                      (MOD[3], 'Elisa Bianco', 'La rettilinea', 'Fa un maglione in quattro ore, e lo rifinisce a mano.',
                       'center 25%'),
                      (MOD[5], 'Tobia Garella', 'I rammendi', 'Ripara i gomiti di maglie di quarant’anni.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prendersene cura', ['Le domande', 'della lana.'], [
        ('Punge?', 'La lana biellese no: è grossa ma morbida. Il merino ancora meno.'),
        ('Come si lava?', 'A mano, in acqua fredda col sapone di Marsiglia, e si asciuga stesa in piano.'),
        ('Fa i pallini?', 'Un po’, i primi mesi: si tolgono col pettine che vi diamo insieme al gomitolo.'),
        ('Si rammenda?', 'Sì, col gomitolo che arriva con la maglia. O in bottega, gratis.'),
    ], 'go-faq', bg=True))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove toccarle', 'con le mani.'],
                   'La lana si tocca: ecco i negozi che vendono le nostre maglie, e il maglificio di Biella, dove si vedono '
                   'nascere.', bg=K.photo_bg(BOT[1]), size=88)]
    out.append(M.negozi(K, t, [
        ('Biella', 'Maglificio Gomitolo', 'via Italia 33'), ('Torino', 'Ferri Corti', 'via Lagrange 15'),
        ('Milano', 'Treccia', 'corso di Porta Romana 98'), ('Aosta', 'Lana Grossa', 'via Aubert 12'),
        ('Verona', 'Punto Legaccio', 'via Mazzini 50'), ('Roma', 'Matassa', 'via del Boschetto 31'),
        ('Edimburgo', 'Ball of Wool', 'Victoria Street 22'), ('Oslo', 'Nøste', 'Markveien 41')],
        nota='Il maglificio rammenda gratis le maglie Gomitolo, anche vecchie.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un rammendo\nUna maglia su misura\nVendere Gomitolo\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Un gomito', 'da rammendare?'],
                                'Per un rammendo, una maglia su misura, o per venderci nel vostro negozio.', VOCI, campi,
                                'Gomitolo — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il maglificio', ['In via Italia,', 'con la rettilinea in vetrina.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'martedì — sabato 9:30 — 12:30 e 15 — 19'), ('Il sabato', 'si vede lavorare la rettilinea'),
                                  ('Rammendi', 'gratis per le maglie Gomitolo')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Gomitolo · maglificio', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='maglificio', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · punto perso', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui è scappato <em>un punto.</em>', size=80, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La pagina si è disfatta. Riprendiamo il filo dall’inizio.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ROSSI, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La maglia che si fa da sola',
    ['moda', 'maglieria', 'lana', 'artigianato', 'marchio', 'biella', 'chiaro', 'slider pro', 'svg'],
    'Un maglificio di Biella e un sito attraversato da un filo di lana che si srotola fra una sezione e l’altra e si '
    'riavvolge uscendo. In apertura una sciarpa si fa da sola in fondo allo schermo, un punto a V dopo l’altro, fila dopo '
    'fila; il cursore è un gomitolo che rotola e lascia il filo.',
    [('Le maglie', MAGLIE), ('La lana', LANA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('maglie', 'Le maglie', 'maglie'), ('la-lana', 'La lana', 'la-lana'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Nunito Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fredoka\', system-ui, sans-serif',
    '\'Azeret Mono\', ui-monospace, Menlo, monospace', ['Nunito Sans', 'Fredoka', 'Azeret Mono'], corsivo_di=[],
    peso_titoli='600', raggio=20, grana=3, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'gomitolo'
THEME['cursor']['ring_color'] = '#D8573A'
THEME['cursor']['dot_color'] = '#FFFFFF'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'maglie.json': build_maglie(), 'la-lana.json': build_lana(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
