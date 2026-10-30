# -*- coding: utf-8 -*-
"""Genera il tema «Halden Classic» (id halden-classic): il reparto storico di una marca di auto sportive (inventata,
come una delle grandi case tedesche): vende auto d’epoca certificate dalla Casa, custodisce l’archivio di ogni telaio e
racconta la storia del marchio, anche quella curiosa. Halden Sportwagen nasce nel 1949 in una segheria di Hirschau.

Tema chiaro ed elegante, carta avorio d’archivio, inchiostro, l’Arancio Hirschau della Casa e l’oro vecchio delle
targhette. Titoli in Playfair Display (anche corsivo), testo in Libre Franklin, dati in DM Mono. Niente marchi veri:
le auto intere sono in corsa, in bianco e nero d’epoca o lontane; il resto sono dettagli (fari, cruscotti, griglie).

Apertura: Slider Pro «l’archivio». Una diapositiva per tappa della Casa (1949, 1957, 1965, 1973, 1995), con le
linguette degli anni in basso: l’anno enorme in corsivo contornato d’oro, il titolo, la targhetta coi dati della
vettura che si batte lettera per lettera e il pulsante «Lo sapevi?», che apre la scheda con la curiosità di
quell’auto (azione «mostra livello» dello Slider Pro).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'halden-classic'
NAME = 'Halden Classic'

HEX = {
    'primary': '#A23E16', 'primary_contrast': '#FFFFFF', 'secondary': '#2E3B44', 'secondary_contrast': '#FFFFFF',
    'accent': '#A88A4E', 'dark': '#151311', 'light': '#FBF8F2', 'muted': '#E8E0D2', 'muted_contrast': '#1C1A17',
    'text': '#1C1A17', 'text_muted': '#615950', 'background': '#F2ECE1', 'border': '#D5CAB7', 'link': '#A23E16',
}

HOME, AUTO, STORIA, ARCHIVIO = '{{PAGINA:home}}', '{{PAGINA:le-auto}}', '{{PAGINA:la-storia}}', '{{PAGINA:l-archivio}}'
MAIL, PHONE, PHONE_TEL = 'classic@halden.example', '+39 0332 000 949', 'tel:+390332000949'
LAT, LNG = '45.8284', '8.8236'

V_PISTA, V_STRADA, V_GRIGLIA, V_RUOTA, V_FARO, V_SPECCHIO = (
    'pexels-video-10231514', 'pexels-video-37528381', 'pexels-video-31220502', 'pexels-video-3066464',
    'pexels-video-31220545', 'pexels-video-31220509')
PHOTOS = [
    'pexels-33148004.jpg', 'pexels-39710680.jpg', 'pexels-13774488.jpg', 'pexels-8744627.jpg', 'pexels-16461387.jpg',
    'pexels-16461374.jpg', 'pexels-16461381.jpg', 'pexels-18108325.jpg', 'pexels-20093355.jpg', 'pexels-15742151.jpg',
    'pexels-39823750.jpg', 'pexels-33184116.jpg', 'pexels-10099175.jpg', 'pexels-31567727.jpg', 'pexels-32199841.jpg',
    'pexels-18435523.jpg', 'pexels-11877411.jpg', 'pexels-13528219.jpg', 'pexels-32364033.jpg', 'pexels-11381557.jpg',
    'pexels-11700328.jpg', 'pexels-18262229.jpg', 'pexels-9435513.jpg', 'pexels-14571665.jpg', 'pexels-12811449.jpg',
    'pexels-33661681.jpg', 'pexels-12953626.jpg', 'pexels-34914237.jpg', 'pexels-19560451.jpg', 'pexels-8882136.jpg',
    'pexels-15241013.jpg', 'pexels-28355523.jpg', 'pexels-30985269.jpg', 'pexels-38276873.jpg', 'pexels-38908429.jpg',
    'pexels-38666630.jpg', 'pexels-33656448.jpg', 'pexels-9617371.jpg', 'pexels-17257089.jpg', 'pexels-37448148.jpg',
    'pexels-6994369.jpg', 'pexels-9489648.jpg', 'pexels-39335075.jpg', 'pexels-8899953.jpg', 'pexels-7322131.jpg',
    'pexels-28100861.jpg', 'pexels-27875832.jpg', 'pexels-33323688.jpg',
]
VIDEOS = [V_PISTA, V_STRADA, V_GRIGLIA, V_RUOTA, V_FARO, V_SPECCHIO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media=mix('var(--olo-color-accent)', 45, 'var(--olo-color-light)'))
C = K.c
ORO = mix(C['acc2'], 45, C['chiaro'])  # l’oro delle targhette sopra le foto scure
TITOLI, MONO = 'Playfair Display', 'DM Mono'
VOCI = [('map-pin', 'Viale Valganna 88 · Varese', ARCHIVIO + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Halden Classic', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Halden Classic', 'Il reparto storico di Halden Sportwagen per l’Italia: auto d’epoca certificate '
                   'dalla Casa, l’archivio di ogni telaio dal 1949, i ricambi originali e il restauro in officina.',
                   [('Le auto', AUTO), ('La storia', STORIA), ('L’archivio', ARCHIVIO), ('Dove siamo', ARCHIVIO + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Il centro', 'Martedì — sabato 9 — 18<br>Le prove su strada su appuntamento', VOCI,
                   '© 2026 Halden Classic Italia srl · Varese · P.IVA 00000000000 · Halden è un marchio di fantasia',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=110,
                   fondo=C['sala'])


TAPPE = [  # anno, occhiello, riga 1, riga 2 (corsivo), testo, targhetta, curiosità, sfondo
    ('1949', 'Halden Nr. 1', 'Una segheria,', 'dodici operai.', 'Konrad Halden costruisce la prima auto dove prima si '
     'tagliavano i tronchi: carrozzeria d’alluminio battuta su una forma di legno del falegname accanto.',
     'Motore    4 cilindri · 40 CV\nPeso      585 kg\nEsemplari 1', 'Il cervo nel marchio ha dodici punte: una per ognuno '
     'dei dodici operai della segheria. Il tredicesimo, il cane di Konrad, è nascosto nella coda.', ('foto', 'pexels-16461381.jpg')),
    ('1957', 'H5 Speedster', 'Da Hirschau', 'a Siracusa.', 'Il parabrezza basso, il tetto di tela che pesa tre chili. Una '
     'H5 guida fino in Sicilia per la Coppa Florio: trentuno ore, nessuna candela cambiata.',
     'Motore    4 cilindri · 70 CV\nPeso      760 kg\nEsemplari 1.204', 'La H5 non aveva la radio: al suo posto un '
     'portaoggetti a misura di borraccia. Lo chiedeva il pilota collaudatore, che d’estate guidava senza fermarsi.',
     ('foto', 'pexels-39823750.jpg')),
    ('1965', 'H8 · la prima', 'Il sei cilindri', 'raffreddato ad aria.', 'Nasce la H8, la Halden di tutti: sei cilindri '
     'contrapposti dietro, cinque marce, l’Arancio Hirschau. Sarà prodotta per trent’anni.',
     'Motore    6 cilindri · 130 CV\nPeso      1.080 kg\nEsemplari 41.300', 'Ogni motore H8 porta inciso sul basamento il '
     'nome del meccanico che l’ha montato. In archivio li abbiamo tutti: 214 nomi in trent’anni.',
     ('foto', 'pexels-13774488.jpg')),
    ('1973', 'H8 RS', 'Leggera', 'per correre.', 'Lamiere più sottili, vetri più leggeri, lo spoiler a coda di rondine. Ne '
     'servivano cinquecento per correre: in sei settimane ne vendono millecinquecento.',
     'Motore    6 cilindri · 210 CV\nPeso      960 kg\nEsemplari 1.536', 'Il tappeto della RS era di gomma, non di lana: '
     'pesava 800 grammi in meno. Chi la comprava poteva chiedere quello di lana, a pagamento.', ('foto', 'pexels-33184116.jpg')),
    ('1995', 'H8 ultima serie', 'L’ultima', 'ad aria.', 'Il 31 marzo 1995 esce dalla linea l’ultima H8 raffreddata ad aria. '
     'Gli operai la firmano sotto il cofano, uno per uno, e la tengono in fabbrica.',
     'Motore    6 cilindri · 285 CV\nPeso      1.370 kg\nEsemplari 68.800', 'L’ultima H8 ad aria non è mai stata '
     'venduta: è in archivio, col pieno fatto il 31 marzo 1995 e quarantuno chilometri sul contachilometri.',
     ('foto', 'pexels-18108325.jpg')),
]


def apertura(t):
    m = K.mob
    sl = []
    for i, (anno, occ, r1, r2, testo, targa, curiosita, sfondo) in enumerate(TAPPE):
        n = i + 1
        sfondo_d = K.sfondo_foto(sfondo[1], overlay=0.55, scala=1.1, durata=11000, verso='in' if n % 2 else 'out',
                                 colore=C['nero'])
        sl.append(K.diapositiva('hc-%d' % n, sfondo_d, [
            K.livello('hc-y%d' % n, contenuto=anno, tag='p', x=46, y=4, w=50, size=300, peso='400', fontStyle='italic',
                      colore='transparent', align='right', fontFamily=TITOLI, lineHeight=0.9, textStrokeWidth=1,
                      textStrokeColor=mix(C['acc2'], 80, C['chiaro']), entra='maskFromBottom', ritardo=100, durata=1600,
                      esce='fadeOut', parallaxDepth=3,
                      responsive={'mobile': {'x': 6, 'y': 5, 'width': 88, 'fontSize': m(120)}}),
            K.livello('hc-o%d' % n, contenuto='%s · %s' % (anno, occ), x=6, y=22, w=40, size=12, peso='500', colore=ORO,
                      letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 27, 'x': 7, 'width': 86}}),
            K.livello('hc-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=26.5, w=48, size=86, peso='400',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, entra='maskFromBottom', ritardo=400, durata=1100,
                      responsive={'mobile': {'fontSize': m(40), 'y': 29.4, 'x': 7, 'width': 88}}),
            K.livello('hc-i%d' % n, contenuto=r2, tag='p', x=6, y=38, w=48, size=86, peso='400', fontStyle='italic',
                      colore=ORO, fontFamily=TITOLI, lineHeight=1.02, entra='maskFromBottom', ritardo=650, durata=1100,
                      responsive={'mobile': {'fontSize': m(40), 'y': 34.3, 'x': 7, 'width': 88}}),
            K.livello('hc-s%d' % n, contenuto=testo, x=6, y=53, w=33, size=17, peso='400', colore=mix(C['chiaro'], 88),
                      lineHeight=1.6, ritardo=1100, responsive={'mobile': {'fontSize': m(14), 'y': 41.5, 'x': 7, 'width': 86}}),
            # la targhetta della vettura, battuta lettera per lettera
            K.livello('hc-d%d' % n, contenuto=targa, x=6, y=69, w='auto', size=13, peso='400', colore=ORO, fontFamily=MONO,
                      lineHeight=1.7, bgColor=mix(C['nero'], 45), padding=14, borderWidth=1, borderColor=mix(C['acc2'], 70),
                      backdropBlur=6, entra='fadeIn', ritardo=1500, durata=400,
                      charAnim={'stagger': 14, 'offsetY': 0, 'blur': 0, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(11), 'y': 53.5, 'x': 7}}),
            K.livello('hc-l%d' % n, contenuto='Lo sapevi?', x=27, y=72.5, w='auto', size=13, peso='500', colore=C['chiaro'],
                      padding=12, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 60), cursor='pointer',
                      fontFamily=TITOLI, fontStyle='italic', entra='popUpBack', ritardo=2400, durata=700,
                      action={'type': 'toggleLayer', 'target': 'hc-q%d' % n},
                      hover={'bgColor': mix(C['acc2'], 55), 'borderColor': ORO, 'duration': 250},
                      responsive={'mobile': {'fontSize': m(13), 'y': 65, 'x': 60}}),
            # la scheda della curiosità, chiusa finché non si tocca «Lo sapevi?»
            K.livello('hc-q%d' % n, contenuto=curiosita, x=58, y=50, w=30, size=16, peso='400', colore=C['testo'],
                      bgColor=C['chiaro'], padding=24, lineHeight=1.6, borderWidth=1, borderColor=C['acc2'],
                      fontFamily=TITOLI, fontStyle='italic', initiallyHidden=True, entra='fadeIn', ritardo=0, durata=300,
                      boxShadow={'x': 0, 'y': 24, 'blur': 60, 'spread': -16, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'x': 7, 'y': 71, 'width': 86, 'fontSize': m(14)}}),
            K.livello('hc-b%d' % n, tipo='button', contenuto='Le auto in vendita', x=6, y=85, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=15, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=2000, buttonUrl=AUTO, hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 89, 'x': 7, 'fontSize': m(11)}}),
        ], durata=9000, tab=anno))
    return K.proslider(t, sl, altezza={'mode': 'vh', 'value': 92}, transizione='crossFade', durata_trans=1600,
                       showArrows=True, arrowStyle='minimal', showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=True, parallaxIntensity=4, autoplaySpeed=9000, progressBarColor=C['acc'])


def colori(t):
    voci = [('Arancio Hirschau', 'H-027 · 1965', 'Il colore della Casa: nacque da due barattoli mescolati per sbaglio. '
             'Oggi lo rifacciamo dal campione d’archivio, con la stessa formula.', 'pexels-12811449.jpg'),
            ('Crema Schwarzwald', 'H-004 · 1953', 'Il colore delle prime H4, scelto perché nascondeva la polvere delle '
             'strade bianche della Foresta Nera.', 'pexels-14571665.jpg'),
            ('Oro Antico', 'H-061 · 1969', 'Metallizzato con vera polvere di bronzo: sotto il sole cambia tre volte '
             'colore. Lo volevano gli importatori americani.', 'pexels-34914237.jpg'),
            ('Blu Lago', 'H-072 · 1977', 'Il blu del lago di Costanza al mattino, per le H9 Turbo. Una sola mano di '
             'trasparente in più, per la profondità.', 'pexels-12953626.jpg'),
            ('Marrone Tabacco', 'H-088 · 1981', 'Il colore delle pipe del direttore tecnico. Raro, e per questo oggi '
             'il più cercato dai collezionisti.', 'pexels-33661681.jpg')]
    return t.classic('provamateriale', {
        'voci': [{'id': 'hc-c%d' % k, 'nome': n, 'codice': c, 'testo': te, 'immagine': K.img(f), 'miniatura': '',
                  'colore': '', 'alt': '%s: un dettaglio di carrozzeria in questo colore' % n}
                 for k, (n, c, te, f) in enumerate(voci)],
        'iniziale': 0, 'lente': True, 'lente_zoom': 2.5, 'lente_misura': 200, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passate sopra la foto: la lente mostra la vernice da vicino',
        'testo_suggerimento_touch': 'Toccate la foto per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'Colore storico', 'accento': C['acc'], 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        'sfondo_pannello': '', 'etichetta_color': C['tenue'], 'nome_font_family': 'heading', 'nome_font_size': 48,
        'nome_font_weight': '400', 'nome_color': C['testo'], 'codice_font_family': 'var(--olo-font-family-mono)',
        'codice_color': C['acc'], 'testo_color': C['tenue'], 'raggio_immagine': radius(2), 'proporzione': '3/2',
        'campione_forma': 'quadretto', 'campione_misura': 52, 'disposizione': 'lato',
        'lato': 'sinistra', 'larghezza_immagine': 60, 'gap': 56}, style={})


AUTO_IN_VENDITA = [  # titolo, foto, decennio, descrizione, anno
    ('H4 Coupé', 'pexels-33148004.jpg', 'Anni Cinquanta', 'Argento · ex corsa, Mille Miglia storica 2019 · 285.000 €', '1954'),
    ('H5 Speedster', 'pexels-14571665.jpg', 'Anni Cinquanta', 'Crema Schwarzwald · tetto di tela nuovo · 340.000 €', '1958'),
    ('H8 2.0', 'pexels-13774488.jpg', 'Anni Sessanta', 'Arancio Hirschau · prima serie, motore originale · 165.000 €', '1965'),
    ('H8 Cielo', 'pexels-34914237.jpg', 'Anni Sessanta', 'Oro Antico · tetto Cielo, interni in pelle · 138.000 €', '1969'),
    ('H8 RS', 'pexels-39710680.jpg', 'Anni Settanta', 'Bianco Gran Premio · alleggerita, certificata · su richiesta', '1973'),
    ('H9 Turbo', 'pexels-12953626.jpg', 'Anni Settanta', 'Blu Lago · prima serie, 41.000 km · 189.000 €', '1977'),
    ('H8 3.2', 'pexels-33661681.jpg', 'Anni Ottanta e Novanta', 'Marrone Tabacco · un solo proprietario · 96.000 €', '1987'),
    ('H8 ultima serie', 'pexels-18108325.jpg', 'Anni Ottanta e Novanta', 'Arancio Hirschau · 12.000 km, tagliandi Casa · 210.000 €', '1995'),
]


def griglia_auto(t, voci, cerca=False):
    return t.classic('portfolio', {
        'source': 'manual', 'layout': 'grid', 'columns': 3, 'gap': 28, 'image_ratio': '4:3', 'image_fit': 'cover',
        'hover_effect': 'zoom', 'animation': 'fade', 'caption_position': 'below', 'enable_search': cerca,
        'search_placeholder': 'Cerca un modello, un colore, un anno…', 'show_title': True, 'show_category': True,
        'show_excerpt': True, 'font_family': 'heading', 'font_weight': '400', 'title_color': C['testo'],
        'text_color': C['tenue'], 'accent_color': C['acc'], 'border_radius': 2, 'year_stamp': True, 'index_numbering': False,
        'items': [{'title': ti, 'image_url': K.img(f), 'category': cat, 'description': d, 'year': a,
                   'link_url': ARCHIVIO + '?telaio=' + ti.replace(' ', '-') + '#certificato'} for ti, f, cat, d, a in voci],
        'columns_tablet': '2', 'columns_mobile_landscape': '2'}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    numeri = t.classic('statstrip', {
        'items': [{'value': '1949', 'label': 'La prima Halden, a Hirschau'}, {'value': '41.000', 'label': 'Schede di telaio in archivio'},
                  {'value': '380', 'label': 'Auto certificate ogni anno'}, {'value': '214', 'label': 'Meccanici firmati sui motori H8'}],
        'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'band_padding_y': 28, 'show_dividers': True,
        'divider_color': C['filo'], 'value_font_family': 'heading', 'value_color': C['testo'], 'value_size': 54,
        'value_weight': '400', 'label_color': C['tenue'], 'label_size': 12, 'label_uppercase': True, 'align': 'left',
        'mono_font_family': ''}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Halden Classic', ['Ogni auto ha', 'una cartella in archivio.'], size=60, layout='stack',
                                size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Dal 1949 la Casa conserva il foglio di montaggio di ogni telaio: il colore, gli optional, il '
                                'nome di chi l’ha costruita, a chi è stata consegnata. Le auto che vendiamo le confrontiamo '
                                'con quel foglio, pezzo per pezzo, e le certifichiamo con la firma dell’archivio.', size=17,
                             color=C['testo']),
                      K.button(t, 'L’archivio', ARCHIVIO, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([numeri], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I colori della Casa', ['Cinque vernici,', 'cinque storie.'], size=60,
                         tagline='Scegliete un campione: la foto cambia e la lente mostra la vernice da vicino, col codice '
                                 'd’archivio. Li rifacciamo tutti, con la formula originale.')]),
        t.one([colori(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='colori', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(t.section([
        t.one([K.sechead(t, 'In vendita', ['Certificate', 'dalla Casa.'], size=60,
                         tagline='Ogni auto ha il suo certificato d’archivio, una garanzia di due anni e la prova su strada.')]),
        t.one([griglia_auto(t, [AUTO_IN_VENDITA[i] for i in (2, 3, 5)])]),
        t.one([K.button(t, 'Tutte le auto', AUTO, kind='pieno', pad_top=36)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_GRIGLIA, 'Arancio Hirschau', 'Il colore <em>nacque da un errore.</em>',
                             'Nel 1965 il verniciatore mescolò il rosso delle H8 con l’arancio delle trebbiatrici della '
                             'fabbrica accanto. Il direttore lo vide asciugare e disse: lasciatelo così.', overlay=66))
    quiz = t.classic('scorequiz', {
        'eyebrow': 'Il quiz dell’archivio', 'heading': 'Quanto conoscete Halden?',
        'intro': 'Quattro domande dalla storia della Casa. Le risposte giuste le trovate tutte in questo sito.',
        'questions': [
            {'text': 'Dove nasce la prima Halden, nel 1949?', 'options': 'In un garage di Stoccarda|0\nIn una segheria di Hirschau|1\nIn un hangar per aerei|0'},
            {'text': 'Quante punte ha il cervo del marchio?', 'options': 'Otto|0\nDodici, come gli operai|1\nSedici|0'},
            {'text': 'Come nasce l’Arancio Hirschau?', 'options': 'Da un concorso fra i clienti|0\nDa due vernici mescolate per sbaglio|1\nDal colore di una pista|0'},
            {'text': 'Che cosa c’è sotto il cofano dell’ultima H8 ad aria?', 'options': 'La firma degli operai|1\nUna targa d’oro|0\nNiente di speciale|0'}],
        'results': [
            {'min': 0, 'title': 'Appassionati in rodaggio', 'text': 'Avete appena cominciato: venite in archivio, le schede si '
             'sfogliano anche senza comprare.', 'link_text': 'La storia della Casa', 'link_url': STORIA},
            {'min': 2, 'title': 'Collezionisti curiosi', 'text': 'Conoscete la Casa meglio di molti proprietari. Manca poco '
             'per il certificato da intenditori.', 'link_text': 'Le curiosità', 'link_url': STORIA + '#curiosita'},
            {'min': 4, 'title': 'Archivisti onorari', 'text': 'Tutte giuste: se passate da Varese, il caffè in archivio lo '
             'offriamo noi.', 'link_text': 'Prenota una visita', 'link_url': ARCHIVIO + '#certificato'}],
        'result_label': 'Siete…', 'back_label': 'Indietro', 'restart_label': 'Ricomincia', 'show_score': True,
        'zone_accent': C['acc'], 'zone_on': C['on_acc'], 'card_bg': C['chiaro'],
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': C['filo']},
        'align': 'left'}, style={})
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-38666630.jpg', 'Gli schedari dell’archivio storico', ratio='4/5', tilt=True)],
                     wm='2-5', ws='1-1'),
               t.col([quiz], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='quiz', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_PISTA, 'Le corse', 'Le Halden <em>nascono in pista.</em>',
                          'Dal 1951 ogni modello nuovo corre prima di essere venduto. Le auto con un passato di gara hanno '
                          'in archivio anche i tempi sul giro.', 'La storia', STORIA, overlay=66, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Halden Classic',
                page_intro_caption='Dal 1949', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_auto():
    t = Tpl(ID, 'le-auto')
    out = [K.intro(t, 'Le auto', ['Certificate,', 'garantite, provate.'],
                   'Le auto che vendiamo hanno passato tre mesi in officina e il confronto col foglio di montaggio. Cercate '
                   'per decennio, modello o colore: ogni scheda porta alla richiesta del certificato.',
                   bg=K.photo_bg('pexels-10099175.jpg', overlay=60), overlay_text=True)]
    out.append(t.section([t.one([griglia_auto(t, AUTO_IN_VENDITA, cerca=True)])], padding='large', width='large',
                         html_id='vendita', style={'padding_top': 100, 'padding_bottom': 100}))
    scheda = t.classic('schedatecnica', {
        'codice': 'H8-1965-0412', 'nome': 'H8 2.0 · 1965', 'etichetta': 'In evidenza',
        'descrizione': 'Prima serie, telaio 412 montato da Erwin Hauser il 3 maggio 1965. Arancio Hirschau originale, '
                       'motore e cambio con i numeri del foglio di montaggio. Restaurata in officina nel 2025.',
        'immagine': K.img('pexels-13774488.jpg'), 'titolo_specifiche': 'Il foglio di montaggio',
        'specifiche': [{'id': 'hs-%d' % k, 'gruppo': g, 'nome': a, 'valore': b, 'unita': u} for k, (g, a, b, u) in enumerate([
            ('Motore', 'Cilindri', '6 contrapposti', ''), ('Motore', 'Potenza', '130', 'CV'), ('Motore', 'Cilindrata', '1.991', 'cc'),
            ('Vettura', 'Peso', '1.080', 'kg'), ('Vettura', 'Chilometri', '86.400', 'km'), ('Vettura', 'Colore', 'H-027 Arancio Hirschau', ''),
            ('Archivio', 'Montata da', 'Erwin Hauser', ''), ('Archivio', 'Consegnata a', 'Monaco di Baviera, 1965', ''),
            ('Archivio', 'Prezzo', '165.000', '€')])],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'Certificato d’archivio Halden · garanzia 24 mesi',
        'cta_testo': 'Chiedi di vederla', 'cta_url': ARCHIVIO + '#certificato', 'cta_codice': True, 'cta_parametro': 'telaio',
        'dati_strutturati': True, 'accento': C['acc'], 'sfondo_scheda': C['chiaro'], 'colore_linee': C['filo'],
        'righe_alternate': False, 'nome_font_size': 40, 'nome_font_weight': '400', 'nome_color': C['testo'],
        'raggio': radius(2), 'raggio_pulsante': radius(2), 'padding_scheda': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40},
        'img_ratio': '4/5', 'img_fit': 'cover', 'foto_posizione': 'sinistra', 'larghezza_foto': 44, 'gap': 48,
        'colonne_specifiche': 1}, style={})
    out.append(t.section([t.one([scheda])], bg=solid(C['sala']), padding='large', width='large', html_id='h8',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'di comprare una storica.', [
        ('Che cosa certifica la Casa?', 'Che telaio, motore e cambio sono quelli del foglio di montaggio, e che colore e '
                                        'optional corrispondono. Se qualcosa è stato cambiato, lo scriviamo.'),
        ('Che garanzia avete?', 'Ventiquattro mesi su motore, cambio e impianto elettrico, nelle officine Halden Classic.'),
        ('Si può provare su strada?', 'Sì, su appuntamento, con uno di noi a bordo: il giro sul lago di Varese dura un’ora.'),
        ('Consegnate all’estero?', 'In tutta Europa, con la bisarca chiusa. Le pratiche di immatricolazione le seguiamo noi.'),
    ], 'au', bg=False))
    return out


def build_storia():
    t = Tpl(ID, 'la-storia')
    out = [K.intro(t, 'La storia', ['Settantasette anni', 'in sei tappe.'],
                   'Dalla segheria di Hirschau all’ultima H8 raffreddata ad aria: le date che contano, e le storie che '
                   'in fabbrica si raccontano ancora.', bg=K.photo_bg('pexels-17257089.jpg', overlay=55), overlay_text=True)]
    tappe = t.classic('timeline', {
        'items': [{'date': d, 'title': ti, 'tag': tag, 'description': de, 'image': K.img(f), 'video': '', 'icon': ic,
                   'category': 'primary'} for d, ti, tag, de, f, ic in [
            ('1949', 'La segheria', 'Le origini', 'Konrad Halden e dodici operai costruiscono la Nr. 1 dove prima si '
             'tagliavano i tronchi. La pagano quattro maiali e una moto.', 'pexels-6994369.jpg', 'hammer'),
            ('1953', 'La H4 Coupé', 'Il primo coupé', 'Il parabrezza in due pezzi e il cervo sul cofano. Prodotta a mano, '
             'otto al mese.', 'pexels-32199841.jpg', 'car'),
            ('1957', 'La H5 Speedster', 'L’America', 'Il tetto di tela da tre chili, pensata per la California. A Siracusa '
             'ci arriva guidando.', 'pexels-16461374.jpg', 'flag'),
            ('1965', 'La H8', 'L’icona', 'Il sei cilindri ad aria, l’Arancio Hirschau. Trent’anni di produzione, 214 meccanici '
             'firmati sul basamento.', 'pexels-12811449.jpg', 'gauge'),
            ('1973', 'La H8 RS', 'Le corse', 'Lamiere sottili, coda di rondine, il tappeto di gomma. Millecinquecento in sei '
             'settimane.', 'pexels-33184116.jpg', 'trophy'),
            ('1995', 'L’ultima ad aria', 'La fine di un’epoca', 'Gli operai la firmano sotto il cofano e la tengono: è in '
             'archivio, con quarantuno chilometri.', 'pexels-18108325.jpg', 'archive')]],
        'tl_layout': 'alt', 'tl_reveal': 'bloom', 'tl_theme': 'paper', 'tl_card': 'polaroid', 'tl_thread': 'dash',
        'tl_node': 'year', 'tl_color': 'cat', 'tl_media': 'on', 'tl_density': 'comfy', 'tl_transparent': True,
        'tl_title_size': 24, 'tl_title_weight': '400', 'tl_title_family': 'heading', 'tl_show_tag': True}, style={})
    out.append(t.section([t.one([tappe])], bg=solid(C['sala']), padding='large', width='large',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    carte = []
    for domanda, risposta, foto in [
        ('Perché un cervo?', 'Hirschau vuol dire «prato dei cervi». Le dodici punte delle corna sono i dodici operai del '
         '1949; il cane di Konrad è nascosto nella coda.', 'pexels-19560451.jpg'),
        ('Con che cosa si pagò la prima Halden?', 'Con quattro maiali e una motocicletta: nel 1949 il contadino che la '
         'comprò non si fidava ancora della moneta nuova.', 'pexels-16461387.jpg'),
        ('Perché la radio della H5 non c’era?', 'Al suo posto un portaborraccia: lo chiese il collaudatore, che d’estate '
         'guidava da Hirschau a Monaco senza fermarsi.', 'pexels-11877411.jpg'),
        ('Dove si ripone il tetto Cielo?', 'Nel bagagliaio davanti, dentro una fodera di feltro: ci entra con due '
         'centimetri di gioco, misurati apposta.', 'pexels-8882136.jpg'),
        ('Perché il tappeto della RS era di gomma?', 'Pesava ottocento grammi meno della lana. Chi voleva la lana la '
         'pagava a parte: lo chiesero in undici.', 'pexels-28355523.jpg'),
        ('Che cosa c’è nell’ultima H8 ad aria?', 'Il pieno del 31 marzo 1995, quarantuno chilometri e duecento firme sotto '
         'il cofano. Non è mai stata accesa da allora.', 'pexels-38276873.jpg')]:
        carte.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto)}, 'back_media': {'type': 'none'},
            'front_title': domanda, 'front_description': 'Lo sapevi? · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': mix(C['nero'], 60), 'back_title': 'Lo sapevi?',
            'back_description': risposta, 'back_bg': C['chiaro'], 'back_text_color': C['testo'], 'back_valign': 'center',
            'back_cta_text': '', 'back_cta_url': '', 'flip_direction': 'horizontal', 'flip_trigger': 'hover',
            'card_height': '380', 'card_border_radius': '2', 'card_shadow': 'md', 'title_size': '24', 'title_weight': '400',
            'desc_size': '15'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Lo sapevi?', ['Sei curiosità', 'dall’archivio.'], size=60,
                         tagline='Passate sopra una carta, o toccatela: dietro c’è la risposta, verificata sulle carte della Casa.')]),
        t.row(carte[:3], layout='33-33-33', gap=20),
        t.row(carte[3:], layout='33-33-33', gap=20, style={'margin_top': 20}),
    ], padding='large', width='large', html_id='curiosita', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-39335075.jpg', 'Konrad Halden, anziano, in un ritratto in bianco e nero', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Konrad Halden · 1974, l’ultimo giorno in fabbrica', color=C['acc']),
                      t.classic('quotation', {
                          'content': 'Un’auto sportiva deve essere leggera come un mobile ben fatto: niente che non serva, '
                                     'tutto che duri.', 'author': 'Konrad Halden, salutando gli operai nel 1974',
                          'style': 'default', 'alignment': 'left', 'content_font_family': 'heading', 'content_font_size': '42',
                          'content_font_weight': '400', 'content_font_style': 'italic', 'content_color': C['testo'],
                          'author_color': C['tenue'], 'author_font_size': '15', 'text_effect': 'none'}, style={})],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_STRADA, 'Il collaudo', 'Ogni Halden <em>passa le Alpi.</em>',
                             'Dal 1953 il giro di collaudo è sempre lo stesso: Hirschau, il passo dello Spluga, il lago di '
                             'Como. Le auto certificate da noi lo rifanno ancora.', overlay=64))
    return out


def build_archivio():
    t = Tpl(ID, 'l-archivio')
    out = [K.intro(t, 'L’archivio', ['Quarantunomila', 'cartelle.'],
                   'Il foglio di montaggio di ogni Halden costruita dal 1949: per una storica in vendita, un’eredità, una '
                   'perizia o la curiosità di sapere da dove arriva la vostra.',
                   bg=K.photo_bg('pexels-38908429.jpg', overlay=55), overlay_text=True, size=88)]
    telaio = t.classic('tracciamento', {
        'indirizzo': ARCHIVIO + '?telaio={codice}#certificato', 'operatori': [], 'etichetta': 'Il numero di telaio',
        'mostra_etichetta': True, 'segnaposto': 'Es. H8-1965-0412', 'testo_pulsante': 'Cerca in archivio',
        'icona_pulsante': 'arrow-right', 'icona_campo': 'archive',
        'nota': 'Lo trovate sul libretto e sulla targhetta nel vano motore. Vi rispondiamo con quello che c’è nella cartella.',
        'nuova_scheda': False, 'maiuscolo': True, 'lunghezza_minima': 6,
        'testo_errore': 'Scrivete il numero completo: almeno sei caratteri.', 'parametro': 'telaio',
        'sfondo_campo': C['chiaro'], 'colore_campo': C['testo'],
        'bordo_campo': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
        'colore_etichetta': C['tenue'], 'colore_nota': C['tenue'], 'colore_errore': C['acc'], 'sfondo_pulsante': C['acc'],
        'colore_pulsante': C['on_acc'], 'campo_font_family': 'var(--olo-font-family-mono)', 'raggio_campo': radius(2),
        'raggio_pulsante': radius(2), 'altezza': 58, 'larghezza_max': 680, 'disposizione': 'riga', 'allineamento': 'left'},
        style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La vostra Halden', ['Che cosa c’è', 'nella sua cartella?'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Scrivete il numero di telaio: vi portiamo alla richiesta già compilata. In due settimane vi '
                                'mandiamo l’estratto del foglio di montaggio, con il colore, gli optional, il meccanico e la '
                                'prima consegna.')], wm='2-5', ws='1-1'),
               t.col([telaio], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('text', 'Numero di telaio', 'telaio', True, param='telaio', segnaposto='H8-1965-0412'),
             K.campo('select', 'Che cosa vi serve', 'cosa', True,
                     opzioni='L’estratto del foglio di montaggio\nIl certificato d’autenticità\nVedere un’auto in vendita\n'
                             'Un preventivo di restauro\nVendere la mia Halden'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'La sua storia', 'messaggio', larghezza='1-1',
                     segnaposto='Da quando è vostra, dove è stata, che cosa sapete già. Ogni dettaglio aiuta l’archivio.')]
    out.append(K.sezione_modulo(t, 'certificato', 'Il certificato', ['Due settimane,', 'una cartella.'],
                                'L’estratto costa 90 €, il certificato d’autenticità con la verifica in officina 650 €. Per '
                                'le auto che vendiamo è compreso.', VOCI, campi, 'Halden Classic — richiesta d’archivio',
                                'Manda la richiesta', 'Ricevuta. L’archivio vi scrive entro due giorni con i tempi.'))
    persone = [('pexels-7322131.jpg', 'Elena Weiss', 'Archivio storico', 'Germanista, custodisce le cartelle dal 2009. '
                'Legge la grafia di ogni capo reparto dal 1949 in poi.', '50% 30%'),
               ('pexels-33323688.jpg', 'Marco Bernasconi', 'Officina Classic', 'Trent’anni sui sei cilindri ad aria. '
                'Ha montato e smontato quasi tutti i modelli della Casa.', '50% 30%'),
               ('pexels-27875832.jpg', 'Giorgio Ferrante', 'Vendite e perizie', 'Ex pilota di regolarità, segue le auto in '
                'vendita e le perizie per eredità e assicurazioni.', '50% 25%')]
    cols = []
    for foto, nome, ruolo, bio, pos in persone:
        cols.append(t.col([t.classic('team', {
            'photo': K.img(foto), 'hover_image': '', 'hover_video': '', 'name': nome, 'role': ruolo, 'bio': bio,
            'link_text': '', 'link_url': '', 'photo_size': '300', 'photo_ratio': '4/5', 'photo_fit': 'cover',
            'photo_object_position': pos, 'photo_shape': 'rounded', 'photo_radius': '2', 'photo_border_width': '0',
            'photo_shadow': 'none', 'info_bg_color': C['chiaro'], 'info_text_color': C['testo'], 'info_padding': '24',
            'info_radius': '2', 'info_align': 'left', 'name_size': '24', 'name_weight': '400', 'role_size': '13',
            'role_color': C['acc'], 'bio_size': '15', 'bg_color': 'transparent'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi apre', 'le cartelle.'], size=56)]),
        t.row(cols, layout='33-33-33', gap=24, valign='start'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Varese,', 'a dieci minuti dal lago.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'viale Valganna 88, Varese'), ('Da Milano', 'cinquanta minuti, A8'),
                                  ('Orari', 'martedì — sabato 9 — 18'), ('Archivio', 'su appuntamento')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Halden Classic · Varese', scuro=False, hex_marker=HEX['primary'], zoom='15')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · cartella mancante', color=ORO, align='center'),
                       K.headline(t, 'Questa cartella <em>non è in archivio.</em>', size=88, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=ORO),
                       K.text(t, 'Capita anche alle migliori: torniamo allo scaffale principale.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_RUOTA, overlay=66), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Negozi online', 'Lo sapevi?',
    ['auto d’epoca', 'auto storiche', 'vendita', 'archivio', 'marchio', 'vintage', 'chiaro', 'slider pro'],
    'Il reparto storico di una marca di auto sportive, avorio d’archivio e arancio: in apertura le tappe della Casa con '
    'l’anno enorme, la targhetta battuta a macchina e «Lo sapevi?» che apre la curiosità; i colori storici sotto la '
    'lente, le auto certificate con la ricerca, il quiz dell’archivio, le carte delle curiosità e il numero di telaio.',
    [('Le auto', AUTO), ('La storia', STORIA), ('I colori', HOME + '#colori'), ('L’archivio', ARCHIVIO)],
    [('home', 'Home', None), ('le-auto', 'Le auto', 'le-auto'), ('la-storia', 'La storia', 'la-storia'),
     ('l-archivio', 'L’archivio', 'l-archivio')],
    HEX, '\'Libre Franklin\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Playfair Display\', \'Georgia\', serif',
    '\'DM Mono\', ui-monospace, Menlo, monospace', ['Playfair Display', 'Libre Franklin', 'DM Mono'],
    corsivo_di=['Playfair Display'], peso_titoli='400', raggio=2, grana=6, titoli_line='1.04', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-auto.json': build_auto(), 'la-storia.json': build_storia(), 'l-archivio.json': build_archivio(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
