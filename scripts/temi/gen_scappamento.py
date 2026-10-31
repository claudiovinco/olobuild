# -*- coding: utf-8 -*-
"""Genera il tema «Scappamento» (id scappamento): una bottega di orologeria a Torino dal 1952, revisioni e restauri di
orologi meccanici, da tasca e da polso, e orologi d'epoca in vendita con la loro busta di lavoro.

Tema scuro: blu notte, champagne e argento. Titoli in Playfair (anche corsivo), testo in Mona Sans, dati in Chivo Mono.

Apertura: Slider Pro «il movimento a vista», alto 16:9 perché i cerchi restino cerchi. A destra due ruote (foto di
movimenti in finestre rotonde) che girano in versi opposti, a velocità diverse, come ingranate; il bilanciere che
oscilla avanti e indietro; un anello tratteggiato col punto che fa il giro in un minuto esatto, come la lancetta dei
secondi. Una diapositiva per lavoro della bottega (revisione, restauro, orologi d'epoca, su misura), a linguette: la
ruota grande cambia, la piccola e il bilanciere continuano a battere.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'scappamento'
NAME = 'Scappamento'

HEX = {
    'primary': '#C9A96E', 'primary_contrast': '#0B1220', 'secondary': '#5C7A99', 'secondary_contrast': '#FFFFFF',
    'accent': '#AFC2D6', 'dark': '#070C16', 'light': '#F2EEE6', 'muted': '#111A2B', 'muted_contrast': '#E9E4DA',
    'text': '#E9E4DA', 'text_muted': '#9AA6B6', 'background': '#0B1220', 'border': '#24324C', 'link': '#C9A96E',
}

HOME, LAB, OROLOGI, BOTTEGA = '{{PAGINA:home}}', '{{PAGINA:il-laboratorio}}', '{{PAGINA:orologi}}', '{{PAGINA:la-bottega}}'
MAIL, PHONE, PHONE_TEL = 'bottega@scappamento.example', '+39 011 000 1952', 'tel:+390110001952'
LAT, LNG = '45.0703', '7.6869'

V_MOVIMENTO, V_TASCA, V_LENTE, V_MANI, V_RUOTE = ('pexels-video-31592458', 'pexels-video-4952990', 'pexels-video-8321917',
                                                  'pexels-video-8321898', 'pexels-video-4824781')
PHOTOS = [
    'pexels-4952980.jpg', 'pexels-416339.jpg', 'pexels-38983088.jpg', 'pexels-10741520.jpg', 'pexels-8327816.jpg',
    'pexels-8327819.jpg', 'pexels-31947898.jpg', 'pexels-28919499.jpg', 'pexels-18279789.jpg', 'pexels-16886383.jpg',
    'pexels-6166171.jpg', 'pexels-34514268.jpg', 'pexels-35086700.jpg', 'pexels-6795489.jpg', 'pexels-8327806.jpg',
    'pexels-8327977.jpg', 'pexels-8327596.jpg', 'pexels-8327532.jpg', 'pexels-8327746.jpg', 'pexels-15881359.jpg',
    'pexels-8327590.jpg', 'pexels-8327576.jpg', 'pexels-8327533.jpg', 'pexels-8327683.jpg', 'pexels-8327808.jpg',
    'pexels-8327535.jpg', 'pexels-16216124.jpg', 'pexels-6263055.jpg', 'pexels-6263114.jpg', 'pexels-6328859.jpg',
    'pexels-6328850.jpg', 'pexels-36484112.jpg', 'pexels-28436130.jpg', 'pexels-2922718.jpg', 'pexels-16591489.jpg',
    'pexels-31878776.jpg', 'pexels-10105257.jpg', 'pexels-15955387.jpg', 'pexels-15743955.jpg', 'pexels-8327954.jpg',
    'pexels-39069672.jpg', 'pexels-35497937.jpg', 'pexels-18525473.jpg',
]
VIDEOS = [V_MOVIMENTO, V_TASCA, V_LENTE, V_MANI, V_RUOTE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True)
C = K.c
ARG = C['acc2']  # l'argento
TITOLI, MONO = 'Playfair', 'Chivo Mono'
VOCI = [('map-pin', 'Via della Rocca 12 · Torino', BOTTEGA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Scappamento', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Scappamento', 'Orologeria in via della Rocca dal 1952: revisioni e restauri di orologi meccanici, '
                   'ricambi rifatti al tornio, orologi d’epoca garantiti due anni.',
                   [('Il laboratorio', LAB), ('Gli orologi', OROLOGI), ('La bottega', BOTTEGA), ('A che punto è?', BOTTEGA + '#busta'),
                    ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Martedì — venerdì 9:30 — 13 e 15 — 19<br>Sabato 9:30 — 13', VOCI,
                   '© 2026 Scappamento snc · Torino · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=120)


LAVORI = [  # linguetta, occhiello, riga 1, riga 2, testo, ruota grande, foto di sfondo
    ('Revisione', '01 · la revisione', 'Ogni orologio', 'ha un battito.', 'Smontiamo, laviamo, lubrifichiamo e regoliamo '
     'in sei posizioni: torna a battere come il giorno in cui è uscito dalla manifattura.', 'pexels-4952980.jpg',
     'pexels-8327977.jpg'),
    ('Restauro', '02 · il restauro', 'I pezzi che mancano', 'li rifacciamo.', 'Una ruota, un perno, una vite del 1890: al '
     'tornio, a mano, con l’acciaio e l’ottone di allora.', 'pexels-31947898.jpg', 'pexels-8327746.jpg'),
    ('D’epoca', '03 · in vendita', 'Orologi che hanno', 'già una storia.', 'Da tasca e da polso, revisionati e '
     'garantiti due anni. Ognuno con la sua busta di lavoro e le foto dell’interno.', 'pexels-2922718.jpg',
     'pexels-8327596.jpg'),
    ('Su misura', '04 · su misura', 'Il cinturino, il vetro,', 'l’incisione.', 'Pelle conciata al vegetale, vetro zaffiro '
     'bombato, il vostro nome inciso a bulino sul fondello.', 'pexels-8327816.jpg', 'pexels-8327806.jpg'),
]


def apertura(t):
    m = K.mob
    sl = []
    # la ruota grande: 480 px su una tela di 1440 × 810 (16:9), centro al 72,67% × 41,6%
    gx, gy, gw, gh = 56.0, 12.0, 33.33, 59.26
    for i, (tab, occ, r1, r2, testo, ruota, foto) in enumerate(LAVORI):
        n = i + 1
        sl.append(K.diapositiva('sc-%d' % n, K.sfondo_foto(foto, overlay=0.86, scala=1.08, durata=10000,
                                                           verso='in' if n % 2 else 'out', colore=C['fondo']), [
            K.livello('sc-r%d' % n, tipo='image', imageSrc=K.img(ruota), objectFit='cover', x=gx, y=gy, w=gw, h=gh,
                      borderRadius=999, borderWidth=6, borderColor=mix(C['acc'], 55),
                      boxShadow={'x': 0, 'y': 30, 'blur': 80, 'spread': -10, 'color': mix(C['nero'], 80)},
                      timeline=K.tl(gx, gy, [(0, {'rotation': 0, 'opacity': 0, 'scale': 0.92}),
                                             (1200, {'rotation': 6, 'opacity': 100, 'scale': 1}),
                                             (60000, {'rotation': 360, 'opacity': 100, 'scale': 1})], 60000),
                      responsive={'mobile': {'x': 17.2, 'y': 5, 'width': 65.6, 'height': 30}}),
            K.livello('sc-o%d' % n, contenuto=occ, x=6, y=18, w=40, size=12, peso='400', colore=C['acc'], letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 41.5, 'x': 7, 'width': 86}}),
            K.livello('sc-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=23, w=46, size=80, peso='500',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromBottom', ritardo=350, durata=1100,
                      responsive={'mobile': {'fontSize': m(38), 'y': 44, 'x': 7, 'width': 88}}),
            K.livello('sc-i%d' % n, contenuto=r2, tag='p', x=6, y=34.5, w=46, size=80, peso='500', fontStyle='italic',
                      colore=C['acc'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromBottom', ritardo=600, durata=1100,
                      responsive={'mobile': {'fontSize': m(38), 'y': 48.6, 'x': 7, 'width': 88}}),
            K.livello('sc-s%d' % n, contenuto=testo, x=6, y=51, w=33, size=17, peso='400', colore=mix(C['chiaro'], 80),
                      lineHeight=1.65, ritardo=1000, responsive={'mobile': {'fontSize': m(14), 'y': 55, 'x': 7, 'width': 86}}),
            K.livello('sc-b%d' % n, tipo='button', contenuto='Porta il tuo orologio', x=6, y=71, w='auto', size=12,
                      peso='600', colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1300, buttonUrl=BOTTEGA + '#scrivici',
                      hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 68.5, 'x': 7, 'fontSize': m(11)}}),
        ], durata=8000, tab=tab))
    # la ruota piccola ingranata, il bilanciere e l'anello dei secondi restano fra una diapositiva e l'altra
    bx, by = 49.97, 55.95
    globali = [
        K.livello('sc-anello', tipo='shape', x=53.92, y=8.27, w=37.5, h=66.67, bgColor='transparent', borderRadius=999,
                  borderWidth=2, borderStyle='dashed', borderColor=mix(C['acc'], 45), entra='fadeIn', ritardo=300, durata=1500,
                  responsive={'mobile': {'x': 13.3, 'y': 3.2, 'width': 73.4, 'height': 33.6}}),
        K.livello('sc-secondi', tipo='shape', x=72.32, y=7.65, w=0.7, h=1.24, bgColor=C['acc'], borderRadius=999,
                  entra='fadeIn', ritardo=1500, durata=800,
                  boxShadow={'x': 0, 'y': 0, 'blur': 12, 'spread': 2, 'color': mix(C['acc'], 70)},
                  motionPath={'path': 'M 0 0 A 270 270 0 1 1 0 540 A 270 270 0 1 1 0 0', 'duration': 60000,
                              'autoRotate': False, 'loop': True, 'easing': 'linear'},
                  responsive={'mobile': {'visible': False}}),
        K.livello('sc-piccola', tipo='image', imageSrc=K.img('pexels-8327819.jpg'), objectFit='cover', x=bx, y=by, w=18.06,
                  h=32.1, borderRadius=999, borderWidth=4, borderColor=mix(ARG, 50),
                  boxShadow={'x': 0, 'y': 20, 'blur': 50, 'spread': -8, 'color': mix(C['nero'], 80)},
                  timeline=K.tl(bx, by, [(0, {'rotation': 0}), (20000, {'rotation': -360})], 20000),
                  responsive={'mobile': {'x': 60.95, 'y': 23.6, 'width': 28.1, 'height': 12.86}}),
    ]
    # il bilanciere: un anello e il suo braccio, stesso centro, stessa oscillazione
    for lid, x, y, w, h, extra in [('sc-bil', 81.05, 62.65, 13.89, 24.69, dict(bgColor='transparent', borderWidth=3,
                                                                                 borderColor=C['acc'], borderRadius=999)),
                                   ('sc-braccio', 87.86, 62.65, 0.28, 24.69, dict(bgColor=C['acc']))]:
        mob = {'x': 11.05, 'y': 26, 'width': 21.9, 'height': 10} if lid == 'sc-bil' else {'x': 21.7, 'y': 26, 'width': 0.6, 'height': 10}
        globali.append(K.livello(lid, tipo='shape', x=x, y=y, w=w, h=h,
                                 timeline=K.tl(x, y, [(0, {'rotation': -110}), (700, {'rotation': 110}), (1400, {'rotation': -110})],
                                               1400, curva='ease-in-out'),
                                 responsive={'mobile': mob}, **extra))
    globali.append(K.livello('sc-perno', tipo='shape', x=87.3, y=73.77, w=1.39, h=2.47, bgColor=C['acc'], borderRadius=999,
                             responsive={'mobile': {'x': 20.9, 'y': 30.5, 'width': 2.2, 'height': 1.0}}))
    globali.append(K.livello('sc-bpz', contenuto='Il bilanciere · 2,5 battiti al secondo', x=78, y=89, w=20, size=10,
                             peso='400', colore=mix(C['chiaro'], 60), letterSpacing=2, textTransform='uppercase',
                             fontFamily=MONO, align='center', entra='fadeIn', ritardo=1600,
                             responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='blur',
                       durata_trans=1100, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=C['acc'], navColor=C['chiaro'])


def movimento(t):
    voci = [(28, 30, 'Il bariletto', 'La molla avvolta: quando caricate l’orologio, la tendete. Si scarica piano, in '
             'quaranta ore.', 'right'),
            (52, 44, 'Il treno del tempo', 'Le ruote che dividono la forza della molla e la portano alle lancette: ore, '
             'minuti, secondi.', 'top'),
            (70, 32, 'Lo scappamento', 'La ruota e l’àncora che lasciano andare la forza un dente alla volta: è il tic-tac.',
             'left'),
            (64, 68, 'Il bilanciere', 'Il volano che oscilla avanti e indietro, con la sua spirale: è lui che decide la '
             'precisione.', 'left'),
            (36, 70, 'I rubini', 'Pietre sintetiche nei punti dove i perni girano: niente attrito, niente usura per decenni.',
             'right')]
    return t.classic('hotspot', {
        'image': K.img('pexels-8327816.jpg'), 'image_alt': 'Il movimento di un orologio antico, con le ruote a vista',
        'image_height': '620', 'aspect_ratio': '16/9', 'object_fit': 'cover', 'object_position': 'center center',
        'markers': [{'id': 'sc-h%d' % i, 'pos_x': str(x), 'pos_y': str(y), 'title': ti, 'description': de, 'icon': '',
                     'tooltip_position': pos, 'link': '', 'link_label': ''} for i, (x, y, ti, de, pos) in enumerate(voci)],
        'border_radius': 2, 'marker_style': 'dot', 'marker_color': C['acc'], 'marker_text_color': C['nero'], 'marker_size': 22,
        'pulse_animation': True, 'tooltip_bg': C['sala'], 'tooltip_color': C['testo'], 'tooltip_width': 280})


def servizi(t):
    return t.classic('worklist', {
        'items': [{'number': nu, 'title': ti, 'category': ca, 'year': pr, 'link_url': LAB + '#prezzi'} for nu, ti, ca, pr in [
            ('01', 'Revisione completa', 'Smontaggio, lavaggio, lubrificazione, regolazione · 3 settimane', 'da 220 €'),
            ('02', 'Restauro di un pezzo d’epoca', 'Ricambi rifatti al tornio, quadrante e lancette', 'su preventivo'),
            ('03', 'Lucidatura della cassa', 'A mano, rispettando spigoli e satinature originali', 'da 90 €'),
            ('04', 'Vetro e impermeabilità', 'Zaffiro o plexiglass bombato, prova in pressione', 'da 60 €'),
            ('05', 'Cinturino su misura', 'Pelle al vegetale cucita a mano, quindici colori', '120 €')]],
        'divider_color': C['filo'], 'row_padding_y': 28, 'row_hover_bg': mix(C['acc'], 8), 'hover_indent': 28,
        'number_color': C['acc'], 'number_size': 14, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 34, 'title_weight': '500', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': ARG, 'year_size': 16, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': MONO}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Scappamento · Torino', ['Settant’anni', 'di tic-tac.'], size=64, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'La bottega l’ha aperta Ernesto Bellandi nel 1952, sotto i portici. Oggi al banco ci sono suo '
                                'nipote Giorgio, Sara e Matteo: tre lenti, un tornio del 1938 e la pazienza di aspettare '
                                'sette giorni prima di riconsegnare un orologio, per essere sicuri che batta giusto.',
                             size=17, color=C['testo']),
                      K.button(t, 'Il laboratorio', LAB, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('1952', 'La bottega sotto i portici'), ('3.200', 'Orologi revisionati in un anno'),
                            ('6', 'Posizioni di regolazione'), ('24', 'Mesi di garanzia')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dentro un movimento', ['Che cosa c’è', 'sotto il quadrante.'], size=60,
                         tagline='Toccate i punti: cinque parti che ogni revisione smonta, lava e rimette a posto.')]),
        t.one([movimento(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_MOVIMENTO, 'Il ritmo', 'Diciottomila <em>battiti l’ora.</em>',
                             'Un orologio da polso degli anni Sessanta batte cinque volte al secondo: in un giorno sono '
                             'quattrocentotrentaduemila. Basta un granello di polvere per perdere un minuto.', overlay=55))
    out.append(t.section([
        t.one([K.sechead(t, 'Il laboratorio', ['Cinque lavori,', 'tutti fatti qui.'], size=60,
                         tagline='Niente spedito fuori: ogni orologio resta in bottega dal primo all’ultimo giorno.')]),
        t.one([servizi(t)]),
        t.one([K.button(t, 'I prezzi e i tempi', LAB, kind='pieno', pad_top=36)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Era l’orologio da tasca di mio nonno, fermo da quarant’anni in un cassetto. Me l’hanno ridato con la '
                 'busta del lavoro e la foto della ruota che hanno rifatto. Adesso lo carico ogni mattina.',
        'author_name': 'Andrea M.', 'author_role': 'Orologio da tasca, 1911', 'avatar': '', 'rating': '5',
        'text_color': C['testo'], 'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'],
        'quote_font': 'heading', 'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False,
        'layout': 'editorial', 'show_line': True, 'line_color': C['acc']}, style={})])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_LENTE, 'La bottega', 'Le mani, la lente, <em>il tempo.</em>',
                          'Giorgio porta la lente da quarant’anni. Dice che un orologio si capisce prima ascoltandolo: '
                          'lo appoggia all’orecchio, poi lo apre.', 'Chi siamo', BOTTEGA, overlay=58, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Scappamento',
                page_intro_caption='Orologeria · Torino · 1952', page_intro_bg=HEX['background'],
                page_intro_color=HEX['primary'], page_intro_duration=1800, page_intro_once=True)


def build_lab():
    t = Tpl(ID, 'il-laboratorio')
    out = [K.intro(t, 'Il laboratorio', ['Tre settimane', 'e sette giorni.'],
                   'Tre settimane di lavoro, poi sette giorni di prova al polso del nostro robot e nel cassetto: lo '
                   'riconsegniamo solo quando ha battuto giusto per una settimana intera.',
                   bg=K.photo_bg('pexels-8327532.jpg', overlay=66), overlay_text=True)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'La busta', 'description': 'Lo apriamo davanti a voi, lo fotografiamo e gli '
                   'diamo un numero: la busta lo segue fino alla riconsegna.'},
                  {'number': '02', 'title': 'La diagnosi', 'description': 'Lo ascoltiamo al cronocomparatore: ampiezza, '
                   'battito, errore. In due giorni vi mandiamo il preventivo.'},
                  {'number': '03', 'title': 'Lo smontaggio', 'description': 'Fino all’ultima vite, nel lavaggio a ultrasuoni. '
                   'Ogni pezzo consumato si cambia o si rifà.'},
                  {'number': '04', 'title': 'La regolazione', 'description': 'In sei posizioni, al caldo e al freddo. Poi '
                   'sette giorni di prova prima della riconsegna.'}],
        'columns': 4, 'gap': 18, 'number_style': 'plain', 'number_color': C['acc'], 'number_size': 40, 'number_font': 'heading',
        'number_weight': '500', 'title_color': C['testo'], 'title_size': 24, 'title_weight': '500', 'title_font': 'heading',
        'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left', 'card_bg': C['sala'], 'card_border': '',
        'card_radius': radius(2), 'card_padding': 28, 'columns_tablet': 2, 'columns_mobile': 1}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Come lavoriamo', ['Dalla busta', 'alla riconsegna.'], size=56)]), t.one([passi])],
                         padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['La revisione,', 'per tipo di orologio.'], size=56,
                         tagline='Il prezzo comprende lavaggio, lubrificazione, guarnizioni e regolazione. I ricambi si '
                                 'aggiungono solo se servono, e ve lo diciamo prima.')]),
        K.piani(t, [('Solo tempo', '220', 'ore, minuti, secondi', 'Carica manuale o automatica\nRegolazione in sei '
                     'posizioni\nProva di sette giorni\nGaranzia 24 mesi', False, ''),
                    ('Cronografo', '360', 'con i contatori', 'Tutto quello della revisione\nIl meccanismo del cronografo\n'
                     'Azzeramento perfetto\nGaranzia 24 mesi', True, 'Il più chiesto'),
                    ('Complicazioni', '520', 'da questo prezzo', 'Calendario, ripetizione, fasi di luna\nPreventivo dopo la '
                     'diagnosi\nRicambi rifatti al tornio\nGaranzia 24 mesi', False, '')],
                'Porta il tuo orologio', BOTTEGA + '#scrivici', nota='IVA compresa · preventivo gratuito'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='prezzi', style={'padding_top': 120, 'padding_bottom': 120}))
    carte = []
    for parola, spiegazione, foto in [
        ('Scappamento', 'La ruota dentata e l’àncora che lasciano passare la forza un dente alla volta. Il nome della bottega '
                        'viene da qui.', 'pexels-8327816.jpg'),
        ('Bilanciere', 'Il piccolo volano che oscilla: con la sua spirale fa da cuore all’orologio. Più è stabile, più '
                       'l’orologio è preciso.', 'pexels-4952980.jpg'),
        ('Rubini', 'Pietre dure sintetiche dove girano i perni. Diciassette rubini erano il segno di un buon orologio.',
         'pexels-38983088.jpg'),
        ('Riserva di carica', 'Quante ore cammina l’orologio a molla carica: di solito quaranta. Si carica ogni mattina, alla '
                              'stessa ora.', 'pexels-6328850.jpg'),
        ('Complicazione', 'Tutto quello che fa oltre a ore e minuti: data, cronografo, fasi di luna, la suoneria.',
         'pexels-31947898.jpg'),
        ('Cronocomparatore', 'Lo strumento che ascolta il tic-tac e disegna il battito: ci dice l’errore al secondo.',
         'pexels-8327806.jpg')]:
        carte.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 62}, 'back_media': {'type': 'none'},
            'front_title': parola, 'front_description': 'Le parole della bottega · girate la carta',
            'front_text_color': C['chiaro'], 'front_valign': 'bottom', 'front_overlay': '',
            'back_title': parola, 'back_description': spiegazione, 'back_bg': C['sala'], 'back_text_color': C['testo'],
            'back_valign': 'center', 'back_cta_text': '', 'back_cta_url': '', 'flip_direction': 'horizontal',
            'flip_trigger': 'hover', 'card_height': '360', 'card_border_radius': '2', 'card_shadow': 'md', 'title_size': '26',
            'title_weight': '500', 'desc_size': '15'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le parole della bottega', ['Sei parole', 'per capire un orologio.'], size=56,
                         tagline='Passate sopra una carta, o toccatela: dietro c’è la spiegazione.')]),
        t.row(carte[:3], layout='33-33-33', gap=20),
        t.row(carte[3:], layout='33-33-33', gap=20, style={'margin_top': 20}),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_RUOTE, 'Il tornio', 'Una ruota <em>rifatta a mano.</em>',
                             'Quando un ricambio non esiste più lo rifacciamo: si misura il vecchio, si taglia l’ottone, si '
                             'fresano i denti uno a uno. Un pomeriggio per una ruota.', overlay=58))
    out.append(K.domande(t, 'Prima', 'di lasciarci il vostro orologio.', [
        ('Ogni quanto si fa la revisione?', 'Ogni cinque, sette anni, o quando l’orologio comincia a perdere o a fermarsi. '
                                            'Gli olii invecchiano anche se non si usa.'),
        ('Riparate anche orologi al quarzo?', 'Sì, cambio della pila e della guarnizione mentre aspettate. Le revisioni '
                                              'complete le facciamo sui meccanici.'),
        ('Tenete i pezzi originali?', 'Sempre: quello che cambiamo ve lo ridiamo nella busta, con la foto del prima e del dopo.'),
        ('Spedite?', 'Sì, assicurato, in tutta Italia. Vi mandiamo noi la scatola imbottita e l’etichetta.'),
    ], 'lab', bg=True))
    return out


VENDITA = [  # titolo, foto, categoria, descrizione, anno
    ('Da tasca, cassa in oro', 'pexels-2922718.jpg', 'Da tasca', 'Oro 18 carati, quadrante smaltato, carica a chiave · 1.900 €', '1908'),
    ('Scheletrato in argento', 'pexels-4952980.jpg', 'Da tasca', 'Movimento a vista, cassa savonette · 1.450 €', '1924'),
    ('Da polso, cuoio', 'pexels-6328859.jpg', 'Da polso', 'Carica manuale, quadrante crema, 34 mm · 780 €', '1956'),
    ('Calendario completo', 'pexels-6166171.jpg', 'Da polso', 'Automatico, giorno e mese, 36 mm · 1.250 €', '1962'),
    ('Numeri romani', 'pexels-36484112.jpg', 'Da polso', 'Acciaio, carica manuale, vetro bombato · 640 €', '1971'),
    ('Da tasca, argento', 'pexels-34514268.jpg', 'Da tasca', 'Con la catena originale, revisionato · 520 €', '1915'),
    ('Da polso, cassa d’oro', 'pexels-15955387.jpg', 'Da polso', 'Oro rosa, carica manuale, cinturino nuovo · 1.680 €', '1949'),
    ('Movimento a vista', 'pexels-38983088.jpg', 'Da polso', 'Fondello trasparente, automatico · 940 €', '1988'),
]


def build_orologi():
    t = Tpl(ID, 'orologi')
    out = [K.intro(t, 'Gli orologi', ['Già revisionati,', 'garantiti due anni.'],
                   'Ogni orologio in vendita è passato dal banco: busta di lavoro, foto dell’interno, sette giorni di prova. '
                   'Cercate per tipo, anno o materiale.', bg=K.photo_bg('pexels-39069672.jpg', overlay=64), overlay_text=True)]
    griglia = t.classic('portfolio', {
        'source': 'manual', 'layout': 'grid', 'columns': 4, 'gap': 24, 'image_ratio': '4:5', 'image_fit': 'cover',
        'hover_effect': 'zoom', 'animation': 'fade', 'caption_position': 'below', 'enable_search': True,
        'search_placeholder': 'Cerca: tasca, oro, 1950, automatico…', 'show_title': True, 'show_category': True,
        'show_excerpt': True, 'font_family': 'heading', 'font_weight': '500', 'title_color': C['testo'],
        'text_color': C['tenue'], 'accent_color': C['acc'], 'border_radius': 2, 'year_stamp': True, 'index_numbering': False,
        'items': [{'title': ti, 'image_url': K.img(f), 'category': cat, 'description': d, 'year': a,
                   'link_url': BOTTEGA + '?orologio=' + ti.replace(' ', '-').replace('’', '') + '#scrivici'}
                  for ti, f, cat, d, a in VENDITA],
        'columns_tablet': '2', 'columns_mobile_landscape': '2'}, style={})
    out.append(t.section([t.one([griglia])], padding='large', width='large', html_id='vendita',
                         style={'padding_top': 100, 'padding_bottom': 100}))
    scheda = t.classic('schedatecnica', {
        'codice': 'SC-1908-041', 'nome': 'Da tasca, cassa in oro · 1908', 'etichetta': 'In vetrina',
        'descrizione': 'Savonette in oro 18 carati, quadrante smaltato a fuoco, carica a chiave. Ruota dei secondi rifatta al '
                       'tornio nel 2026: la vecchia è nella busta.',
        'immagine': K.img('pexels-16886383.jpg'), 'titolo_specifiche': 'La busta di lavoro',
        'specifiche': [{'id': 'ss-%d' % k, 'gruppo': g, 'nome': a, 'valore': b, 'unita': u} for k, (g, a, b, u) in enumerate([
            ('Cassa', 'Materiale', 'Oro 18 kt', ''), ('Cassa', 'Diametro', '51', 'mm'), ('Cassa', 'Peso', '96', 'g'),
            ('Movimento', 'Carica', 'A chiave', ''), ('Movimento', 'Rubini', '15', ''), ('Movimento', 'Riserva', '36', 'ore'),
            ('Lavoro', 'Revisione', 'Marzo 2026', ''), ('Lavoro', 'Errore', '+6', 's/giorno'), ('Lavoro', 'Prezzo', '1.900', '€')])],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'Garanzia 24 mesi · busta di lavoro con le foto',
        'cta_testo': 'Chiedi di vederlo', 'cta_url': BOTTEGA + '#scrivici', 'cta_codice': True, 'cta_parametro': 'orologio',
        'dati_strutturati': True, 'accento': C['acc'], 'sfondo_scheda': C['sala'], 'colore_linee': C['filo'],
        'righe_alternate': False, 'nome_font_size': 40, 'nome_font_weight': '500', 'nome_color': C['testo'],
        'descrizione_color': C['tenue'], 'gruppo_color': C['acc'], 'voce_color': C['tenue'], 'valore_color': C['testo'],
        'unita_color': C['tenue'], 'codice_color': C['acc'], 'raggio': radius(2), 'raggio_pulsante': radius(2),
        'padding_scheda': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40}, 'img_ratio': '4/5', 'img_fit': 'cover',
        'foto_posizione': 'sinistra', 'larghezza_foto': 44, 'gap': 48, 'colonne_specifiche': 1}, style={})
    out.append(t.section([t.one([scheda])], bg=solid(C['sala']), padding='large', width='large', html_id='vetrina',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_TASCA, 'Da tasca', 'Cent’anni, <em>e batte ancora.</em>',
                          'Gli orologi da tasca sono quelli che arrivano più spesso: dai cassetti, dalle eredità, dai '
                          'mercatini. Li riportiamo a battere e li vendiamo con la loro storia.', 'Porta il tuo',
                          BOTTEGA + '#scrivici', overlay=60, video=True))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Sotto i portici,', 'dal 1952.'],
                   'Tre banchi, un tornio, i cassetti coi ricambi di settant’anni. Venite a portare un orologio, o anche '
                   'solo a vederlo aprire.', bg=K.photo_bg('pexels-8327954.jpg', overlay=64), overlay_text=True)]
    busta = t.classic('tracciamento', {
        'indirizzo': BOTTEGA + '?busta={codice}#scrivici', 'operatori': [], 'etichetta': 'Il numero della busta',
        'mostra_etichetta': True, 'segnaposto': 'Es. 26-0412', 'testo_pulsante': 'A che punto è?',
        'icona_pulsante': 'arrow-right', 'icona_campo': 'clock',
        'nota': 'Lo trovate sulla ricevuta. Vi rispondiamo con la fase del lavoro e la data di riconsegna.',
        'nuova_scheda': False, 'maiuscolo': True, 'lunghezza_minima': 5,
        'testo_errore': 'Scrivete il numero completo, come sulla ricevuta.', 'parametro': 'busta',
        'sfondo_campo': C['sala'], 'colore_campo': C['testo'],
        'bordo_campo': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
        'colore_etichetta': C['tenue'], 'colore_nota': C['tenue'], 'colore_errore': C['acc'], 'sfondo_pulsante': C['acc'],
        'colore_pulsante': C['on_acc'], 'campo_font_family': 'var(--olo-font-family-mono)', 'raggio_campo': radius(2),
        'raggio_pulsante': radius(2), 'altezza': 58, 'larghezza_max': 680, 'disposizione': 'riga', 'allineamento': 'left'},
        style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il vostro orologio', ['A che punto è', 'il lavoro?'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Scrivete il numero della busta: vi portiamo al modulo già compilato e vi rispondiamo con la '
                                'fase del lavoro, dalla diagnosi alla prova.')], wm='2-5', ws='1-1'),
               t.col([busta], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', html_id='busta', style={'padding_top': 110, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa vi serve', 'cosa', True,
                     opzioni='Una revisione\nUn restauro\nVedere un orologio in vendita\nUn cinturino su misura\n'
                             'Sapere a che punto è il lavoro'),
             K.campo('text', 'Numero della busta', 'busta', param='busta', segnaposto='Se l’orologio è già da noi'),
             K.campo('text', 'Orologio in vendita', 'orologio', param='orologio', segnaposto='Il nome della scheda'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Il vostro orologio', 'messaggio', larghezza='1-1',
                     segnaposto='Marca, anni, che cosa fa (o non fa). Una foto del quadrante aiuta.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Un orologio,', 'una busta.'],
                                'Il preventivo è gratuito. Se spedite, vi mandiamo noi la scatola imbottita e l’etichetta '
                                'assicurata.', VOCI, campi, 'Scappamento — richiesta', 'Manda la richiesta',
                                'Ricevuta. Vi rispondiamo entro due giorni lavorativi.'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Tre lenti,', 'un tornio.'], size=56)]),
        K.squadra(t, [
            ('pexels-8327535.jpg', 'Giorgio Bellandi', 'Maestro orologiaio', 'Nipote di Ernesto, al banco dal 1984. Le '
             'complicazioni e gli orologi da tasca sono suoi.', '50% 30%'),
            ('pexels-6263055.jpg', 'Sara Conti', 'Casse e lucidatura', 'Orafa, rimette a nuovo casse e bracciali senza '
             'perdere uno spigolo. Taglia e cuce i cinturini.', '50% 30%'),
            ('pexels-8327683.jpg', 'Matteo Rinaldi', 'Revisioni e tornio', 'Diplomato alla scuola di orologeria di Ginevra, '
             'rifà al tornio i pezzi che non esistono più.', '50% 30%')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Sotto i portici,', 'davanti alla Mole.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via della Rocca 12, Torino'), ('Orari', 'mar — ven 9:30 — 13 · 15 — 19'),
                                  ('Sabato', '9:30 — 13'), ('Tram', '13 e 15, fermata Vittorio Veneto')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Scappamento · via della Rocca 12', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · orologio fermo', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>ha perso un minuto.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro']),
                       K.text(t, 'Capita anche ai migliori movimenti: rimettiamola all’ora giusta.', size=17,
                              align='center', color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna alla bottega', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_MANI, overlay=72), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il bilanciere che oscilla',
    ['orologeria', 'orologi', 'restauro', 'torino', 'vintage', 'scuro', 'slider pro'],
    'Una bottega di orologeria, blu notte e champagne: in apertura due ruote che girano ingranate, il bilanciere che '
    'oscilla e il punto dei secondi che fa il giro in un minuto; il movimento da esplorare coi punti, i servizi, le '
    'parole della bottega su carte da girare, gli orologi d’epoca con la ricerca e la busta del lavoro da seguire.',
    [('Il laboratorio', LAB), ('Gli orologi', OROLOGI), ('A che punto è?', BOTTEGA + '#busta'), ('La bottega', BOTTEGA)],
    [('home', 'Home', None), ('il-laboratorio', 'Il laboratorio', 'il-laboratorio'), ('orologi', 'Gli orologi', 'orologi'),
     ('la-bottega', 'La bottega', 'la-bottega')],
    HEX, '\'Mona Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Playfair\', \'Georgia\', serif',
    '\'Chivo Mono\', ui-monospace, Menlo, monospace', ['Playfair', 'Mona Sans', 'Chivo Mono'],
    corsivo_di=['Playfair'], peso_titoli='500', raggio=2, grana=5, titoli_line='1.04', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-laboratorio.json': build_lab(), 'orologi.json': build_orologi(), 'la-bottega.json': build_bottega(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
