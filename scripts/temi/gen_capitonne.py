# -*- coding: utf-8 -*-
"""Genera il tema «Capitonné» (id capitonne): una bottega di tappezzeria a Vicenza: poltrone e divani rifatti, il
capitonné a mano, le tende su misura, le testiere, la scelta dei tessuti in bottega.

Tema scuro e caldo, il velluto: bordeaux profondo, oro, beige e avorio. Titoli in Cinzel, testo in Raleway, dati in
Victor Mono.

Apertura: «il sipario a pannelli». Lo slider resta fermo in cima mentre si scorre: tutta la tela è coperta da dieci teli
di velluto a pieghe, col titolo sopra. Scorrendo i teli girano in 3D uno dopo l’altro, da sinistra a destra, come le
lamelle di una veneziana di stoffa, e scoprono il video della bottega: il tappezziere al banco. Cursore: la nappa.
"""
import os
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
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

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


# ── L'apertura: il sipario a pannelli ──
TELI = 10


def apertura(t):
    m = K.mob
    livelli = []
    w = 100.0 / TELI
    for k in range(TELI):
        a = 8 + k * 6.5
        # le pieghe: ogni telo è un gradiente dal bordeaux scuro alla luce sul colmo della piega
        piega = 'background:linear-gradient(90deg,#3A0F18 0%,#6E1E2B 30%,#9C3343 52%,#6E1E2B 74%,#2E0B13 100%);'
        livelli.append(K.livello('ca-telo%d' % k, tipo='shape', x=round(k * w, 3), y=0, w=round(w + 0.06, 3), h=100,
                                 bgColor=BORDEAUX, entra='fadeIn', ritardo=0, durata=500,
                                 customCSS=piega + 'backface-visibility:hidden;-webkit-backface-visibility:hidden;',
                                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': 0.01}, {'p': a, 'ry': 0.01},
                                                                                {'p': a + 18, 'ry': 120},
                                                                                {'p': 100, 'ry': 120}]},
                                 responsive={'mobile': {'x': round(k * w, 3), 'y': 0, 'width': round(w + 0.1, 3),
                                                        'height': 100}}))
    # il bastone dorato in alto con gli anelli
    livelli.append(K.livello('ca-bastone', tipo='shape', x=0, y=0, w=100, h=1.6, shapeGradient={'angle': 180, 'from': '#E9CC7E',
                                                                                              'to': '#8E6E2A', 'fromStop': 0},
                             entra='fadeIn', ritardo=0, durata=600,
                             scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 70, 'o': 100},
                                                                            {'p': 90, 'o': 0}, {'p': 100, 'o': 0}]},
                             responsive={'mobile': {'height': 0.8}}))
    testi = [
        K.livello('ca-o', contenuto='Capitonné · tappezzeria e tende · Vicenza', x=6, y=24, w=44, size=12, peso='500',
                  colore=mix(C['chiaro'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('ca-t1', contenuto='Ogni bottone', tag='h1', x=6, y=30, w=56, size=76, peso='500', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=1, entra='maskFromBottom', ritardo=350, durata=1100,
                  responsive={'mobile': {'fontSize': m(34), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('ca-t2', fontFamily='Cinzel', contenuto='tirato a mano.', tag='p', x=6, y=41, w=56, size=76, peso='500',
                  colore=C['acc'], lineHeight=1.0, letterSpacing=1, entra='maskFromBottom', ritardo=550, durata=1100,
                  responsive={'mobile': {'fontSize': m(34), 'y': 15, 'x': 7, 'width': 90}}),
        K.livello('ca-s', contenuto='Poltrone e divani rifatti come erano, il capitonné bottone per bottone, le tende cucite '
                                    'sulle vostre finestre. In bottega dal 1958.', x=6, y=55, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 88), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 19.5, 'x': 7, 'width': 86}}),
        K.livello('ca-b', tipo='button', contenuto='I lavori', x=6, y=69, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=17, borderRadius=2, letterSpacing=3, textTransform='uppercase', entra='fadeInUp',
                  ritardo=1000, buttonUrl=LAVORI, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'y': 26.5, 'x': 7, 'fontSize': m(11)}}),
        K.livello('ca-l', tipo='button', contenuto='Un preventivo', x=16.7, y=69, w='auto', size=12, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=2, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), letterSpacing=3, textTransform='uppercase', entra='fadeInUp', ritardo=1100,
                  buttonUrl=CONTATTI, hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 21}, 'mobile': {'y': 26.5, 'x': 40, 'fontSize': m(11)}}),
        K.livello('ca-hint', contenuto='Scorrete: si apre la bottega ↓', x=6, y=84, w=30, size=12, peso='500',
                  colore=mix(C['chiaro'], 82), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 15, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 32, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('ca-velo', 'sinistra', 40, 66, colore=C['nero'], forza=88, mobile_alto=35)
    sl = [K.diapositiva('ca-1', K.sfondo_video(V_BANCO, overlay=0.18, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1500, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


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
    out.append(K.video_frase(t, V_VELLUTO, 'I tessuti', 'Seicento tessuti <em>da toccare.</em>',
                             'Velluti, lini, damaschi, pelli: il campionario sta in bottega, e si sceglie con le mani.',
                             overlay=22))
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
        t.one([K.sechead(t, 'Prima e dopo', ['Le sedute', 'tornate a casa.'], size=56)]),
        P.fila_foto(K, t, [('pexels-39351124.jpg', 'Una poltrona rivestita'), ('pexels-3764576.jpg', 'Un divano Chesterfield '
                                                                                                    'in pelle'),
                           ('pexels-15758184.jpg', 'Il capitonné di un divano'), ('pexels-6394545.jpg', 'Una poltrona di '
                                                                                                       'velluto')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Tre generazioni,', 'gli stessi attrezzi.'],
                   'La bottega è in contra’ Porti dal 1958: il banco, le forbici del nonno, il martello col magnete per le '
                   'puntine. E il campionario dei tessuti, che occupa una parete.', bg=K.photo_bg('pexels-20531149.jpg'),
                   size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Una poltrona', ['Tre settimane,', 'tre passaggi.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('I', 'settimana', 'pexels-12691635.jpg', 'Smontare', 'Fino', 'al legno',
             'Si toglie tutto: tessuto, imbottitura, molle. Il legno si incolla e si rinforza dove serve.', '200', 'puntine '
             'tolte'),
            ('II', 'settimana', 'pexels-5830633.jpg', 'Imbottire', 'Molle', 'e crine',
             'Le cinghie nuove, le molle legate a mano con lo spago, il crine, l’ovatta.', '8', 'nodi a molla'),
            ('III', 'settimana', 'pexels-33084139.jpg', 'Rivestire', 'Il tessuto', 'tirato a mano',
             'Il tessuto si taglia, si tira, si fissa; le cuciture a mano, i bottoni del capitonné.', '1', 'poltrona come '
             'nuova')], icona='scissors')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
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
    ['tappezzeria', 'tende su misura', 'capitonné', 'artigianato', 'vicenza', 'scuro', 'slider pro', '3d'],
    'Una bottega di tappezzeria, velluto bordeaux e oro: in apertura dieci teli di velluto a pieghe coprono tutto, e '
    'scorrendo girano in 3D uno dopo l’altro come una veneziana di stoffa scoprendo il tappezziere al banco; i lavori da '
    'girare, il listino, i tre passaggi di una poltrona e il preventivo.',
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
