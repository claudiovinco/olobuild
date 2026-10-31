# -*- coding: utf-8 -*-
"""Genera il tema «Anima» (id anima): una bottega di liuteria a Cremona, violini, viole e violoncelli fatti a mano,
restauri e una piccola scuola. L'anima è il cilindretto di abete dentro il violino che porta il suono dal ponticello al
fondo: senza, lo strumento non suona.

Tema chiaro e caldo: carta, acero dorato, il rosso bruno della vernice e il verde del feltro. Titoli in Crimson Pro
(anche corsivo), testo in Gantari, dati in Fragment Mono.

Apertura: Slider Pro «il suono», alto 16:9 perché i cerchi restino cerchi. Lo strumento in una finestra ad arco che si
inclina in 3D verso il puntatore; dalle effe partono onde concentriche che si allargano e svaniscono (il suono); quattro
corde tese attraversano l'apertura e vibrano ognuna alla sua frequenza (sol, re, la, mi).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'anima'
NAME = 'Anima'

HEX = {
    'primary': '#8E3B12', 'primary_contrast': '#FFFFFF', 'secondary': '#3F5641', 'secondary_contrast': '#FFFFFF',
    'accent': '#C99A5B', 'dark': '#22170F', 'light': '#FFFCF7', 'muted': '#EBE1D3', 'muted_contrast': '#24190F',
    'text': '#24190F', 'text_muted': '#6D5E4F', 'background': '#F5EFE6', 'border': '#DCCDB8', 'link': '#8E3B12',
}

HOME, STRUMENTI, BOTTEGA, VISITA = '{{PAGINA:home}}', '{{PAGINA:gli-strumenti}}', '{{PAGINA:la-bottega}}', '{{PAGINA:visita}}'
MAIL, PHONE, PHONE_TEL = 'bottega@anima.example', '+39 0372 000 1644', 'tel:+3903720001644'
LAT, LNG = '45.1335', '10.0227'

V_MAESTRO, V_ARCO, V_SUONO, V_SGORBIA, V_BOTTEGA = ('pexels-video-37864529', 'pexels-video-7095069', 'pexels-video-5331169',
                                                   'pexels-video-5972648', 'pexels-video-35124443')
PHOTOS = [
    'pexels-8471819.jpg', 'pexels-306175.jpg', 'pexels-37210788.jpg', 'pexels-25859077.jpg', 'pexels-7095503.jpg',
    'pexels-6037775.jpg', 'pexels-36881009.jpg', 'pexels-8929455.jpg', 'pexels-4231488.jpg', 'pexels-19585038.jpg',
    'pexels-6075991.jpg', 'pexels-39294525.jpg', 'pexels-37864375.jpg', 'pexels-3811842.jpg', 'pexels-4403539.jpg',
    'pexels-9996436.jpg', 'pexels-6544938.jpg', 'pexels-33999088.jpg', 'pexels-15209791.jpg', 'pexels-6075992.jpg',
    'pexels-32599546.jpg', 'pexels-6788401.jpg', 'pexels-7480720.jpg', 'pexels-5974292.jpg', 'pexels-33640401.jpg',
    'pexels-8519631.jpg', 'pexels-8519647.jpg', 'pexels-5709687.jpg', 'pexels-19591432.jpg', 'pexels-39518610.jpg',
    'pexels-30563415.jpg', 'pexels-30563418.jpg', 'pexels-34265902.jpg', 'pexels-5914001.jpg', 'pexels-30563416.jpg',
    'pexels-20067759.jpg', 'pexels-14335436.jpg', 'pexels-6661905.jpg', 'pexels-36458695.jpg', 'pexels-38440303.jpg',
]
VIDEOS = [V_MAESTRO, V_ARCO, V_SUONO, V_SGORBIA, V_BOTTEGA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media=mix('var(--olo-color-accent)', 70, 'var(--olo-color-light)'))
C = K.c
ACERO = C['acc2']
TITOLI, MONO = 'Crimson Pro', 'Fragment Mono'
VOCI = [('map-pin', 'Via Platina 18 · Cremona', VISITA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Anima', logo_size='28', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Anima', 'Bottega di liuteria a Cremona dal 1988: violini, viole e violoncelli costruiti a mano su forme '
                   'interne, abete della Val di Fiemme e acero dei Balcani, vernice a olio. Restauri e una piccola scuola.',
                   [('Gli strumenti', STRUMENTI), ('La bottega', BOTTEGA), ('Visita e prova', VISITA),
                    ('Come arrivare', VISITA + '#arrivare'), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Lunedì — venerdì 9 — 12:30 e 14:30 — 18<br>La prova degli strumenti su appuntamento', VOCI,
                   '© 2026 Liuteria Anima · Cremona · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150,
                   fondo=C['sala'])


LAVORI = [  # linguetta, occhiello, riga 1, riga 2, testo, foto, prezzo/nota
    ('Violini', '01 · il violino', 'Duecento ore', 'per un violino.', 'Abete della Val di Fiemme per il piano, acero dei '
     'Balcani per il fondo, ventiquattro mani di vernice a olio. Ogni violino ha la sua etichetta scritta a mano.',
     'pexels-36881009.jpg', 'da 14.000 €'),
    ('Violoncelli', '02 · il violoncello', 'Una voce grave', 'che riempie la stanza.', 'Sul modello di Stradivari del '
     '1707, con le misure adattate a chi lo suonerà. Lo si prova in bottega, con il proprio arco.',
     'pexels-8519647.jpg', 'da 26.000 €'),
    ('Restauro', '03 · il restauro', 'Ogni crepa', 'ha la sua storia.', 'Strumenti di famiglia, di orchestra, di scuola: '
     'si apre il piano, si incolla, si ritocca la vernice senza cancellarne gli anni.',
     'pexels-39294525.jpg', 'preventivo gratuito'),
    ('Scuola', '04 · la scuola', 'Tre anni', 'con la sgorbia in mano.', 'Quattro allievi all’anno, cinque giorni alla '
     'settimana al banco. Si esce col primo violino costruito da soli, dalla forma alla vernice.',
     'pexels-5974292.jpg', 'iscrizioni a giugno'),
]


def apertura(t):
    m = K.mob
    sl = []
    W, H = 1440.0, 810.0
    bx, by, bw, bh = 56.0, 16.0, 36.11, 64.2               # la buca: 520 px, la foto dello strumento dentro
    rx, ry, rw, rh = bx - 1.39, by - 2.47, bw + 2.78, bh + 4.94   # la rosetta attorno alla buca
    for i, (tab, occ, r1, r2, testo, foto, nota) in enumerate(LAVORI):
        n = i + 1
        sl.append(K.diapositiva('an-%d' % n, K.sfondo_video([V_BOTTEGA, V_ARCO, V_SGORBIA, V_MAESTRO][i], overlay=0.6,
                                                            colore=C['fondo']),
                                K.veli('an-velo%d' % n, 'sinistra', 48, 60) + [
            K.livello('an-a%d' % n, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=bx, y=by, w=bw, h=bh,
                      borderRadius=999, entra='zoomIn', ritardo=100, durata=1200, esce='fadeOut',
                      boxShadow={'x': 0, 'y': 30, 'blur': 70, 'spread': -18, 'color': mix(C['nero'], 40)},
                      responsive={'mobile': {'x': 14, 'y': 5, 'width': 72, 'height': 32.91}}),
            K.livello('an-o%d' % n, contenuto=occ, x=6, y=19, w=40, size=12, peso='400', colore=C['acc'], letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 41, 'x': 7, 'width': 86}}),
            K.livello('an-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=23.5, w=44, size=66, peso='500',
                      colore=C['testo'], fontFamily=TITOLI, lineHeight=1.0, entra='fadeIn', ritardo=350, durata=1300,
                      charAnim={'stagger': 30, 'offsetY': 24, 'blur': 6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 43.5, 'x': 7, 'width': 88}}),
            K.livello('an-i%d' % n, contenuto=r2, tag='p', x=6, y=32.5, w=44, size=66, peso='500', fontStyle='italic',
                      colore=C['acc'], fontFamily=TITOLI, lineHeight=1.0, entra='fadeIn', ritardo=700, durata=1300,
                      charAnim={'stagger': 30, 'offsetY': 24, 'blur': 6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 48.2, 'x': 7, 'width': 88}}),
            K.livello('an-s%d' % n, contenuto=testo, x=6, y=47, w=32, size=17, peso='400', colore=C['tenue'],
                      lineHeight=1.65, ritardo=1000, responsive={'mobile': {'fontSize': m(14), 'y': 55, 'x': 7, 'width': 86}}),
            K.livello('an-b%d' % n, tipo='button', contenuto='Prenota una prova', x=6, y=72, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=4, ritardo=1300, buttonUrl=VISITA, hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 70, 'x': 7, 'fontSize': m(11)}}),
            K.livello('an-n%d' % n, contenuto=nota, x=22, y=73, w='auto', size=12, peso='400', colore=C['testo'],
                      fontFamily=MONO, padding=11, borderRadius=999, borderWidth=1, borderColor=mix(C['testo'], 30),
                      entra='popUpBack', ritardo=1550, responsive={'mobile': {'y': 70.6, 'x': 58, 'fontSize': m(10)}}),
        ], durata=8000, tab=tab))
    # la rosetta, il ponticello e le quattro corde (sol, re, la, mi) che passano sopra la buca e vibrano;
    # le onde del suono che escono dalla buca
    globali = [K.livello('an-rosetta', tipo='shape', x=rx, y=ry, w=rw, h=rh, bgColor='transparent', borderRadius=999,
                         borderWidth=3, borderStyle='dotted', borderColor=mix(C['acc'], 70),
                         timeline=K.tl(rx, ry, [(0, {'rotation': 0, 'opacity': 0}), (1200, {'rotation': 20, 'opacity': 100}),
                                                (90000, {'rotation': 380, 'opacity': 100})], 90000),
                         responsive={'mobile': {'x': 12, 'y': 3.86, 'width': 76, 'height': 35.2}}),
               K.livello('an-ponte', tipo='shape', x=48.6, y=41.5, w=0.7, h=15.5, bgColor=mix(C['testo'], 85), borderRadius=3,
                         entra='fadeIn', ritardo=500, durata=800,
                         responsive={'mobile': {'visible': False}})]
    cy = by + bh / 2
    for k, (periodo, spessore) in enumerate([(150, 0.4), (120, 0.33), (96, 0.27), (78, 0.2)]):
        y = round(cy - 4.5 + 3 * k, 2)
        my = round(5 + 32.91 / 2 - 2.25 + 1.5 * k, 2)
        globali.append(K.livello('an-corda%d' % k, tipo='shape', x=48.9, y=y, w=51.1, h=spessore,
                                 bgColor=mix(C['acc2'], 70, C['testo']), entra='maskFromLeft', ritardo=600 + 120 * k,
                                 durata=900,
                                 timeline=K.tl(48.9, y, [(0, {'y': y - 0.08}), (periodo // 2, {'y': y + 0.08}),
                                                         (periodo, {'y': y - 0.08})], periodo, curva='sine.inOut'),
                                 responsive={'mobile': {'x': 0, 'y': my, 'width': 100, 'height': round(spessore / 2.2, 2)}}))
    cx = bx + bw / 2
    for k in range(3):
        d = 30.0
        h = round(d * W / H, 2)
        globali.append(K.livello('an-onda%d' % k, tipo='shape', x=round(cx - d / 2, 2), y=round(cy - h / 2, 2), w=d, h=h,
                                 bgColor='transparent', borderRadius=999, borderWidth=1, borderColor=mix(C['acc'], 55),
                                 timeline=K.tl(round(cx - d / 2, 2), round(cy - h / 2, 2),
                                               [(0, {'scale': 0.6, 'opacity': 0}), (1000 * k, {'scale': 0.6, 'opacity': 0}, 'power2.out'),
                                                (1000 * k + 200, {'scale': 0.65, 'opacity': 90}, 'power2.out'),
                                                (1000 * k + 2800, {'scale': 1.6, 'opacity': 0}), (3600, {'scale': 1.6, 'opacity': 0})],
                                               3600),
                                 responsive={'mobile': {'visible': False}}))
    globali.append(K.livello('an-note', contenuto='Sol · Re · La · Mi — la 442 Hz', x=6, y=86, w=30, size=11, peso='400',
                             colore=C['tenue'], fontFamily=MONO, letterSpacing=2, entra='fadeIn', ritardo=1800,
                             responsive={'mobile': {'y': 77, 'x': 7, 'width': 86, 'fontSize': m(10)}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='paperCut',
                       durata_trans=1200, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=8000, progressBarColor=C['acc'], navColor=C['testo'],
                       globalBackground={'type': 'color', 'color': C['fondo']})


def legni(t):
    voci = [('Abete rosso', 'Val di Fiemme · piano armonico', 'Gli alberi «di risonanza», cresciuti lenti al freddo: anelli '
             'fitti e regolari. Stagionato almeno dieci anni.', 'pexels-6544938.jpg'),
            ('Acero marezzato', 'Balcani · fondo e fasce', 'La marezzatura, le onde di luce sul fondo, viene dalle fibre '
             'ondulate. Più è profonda, più il violino è bello da vedere.', 'pexels-4403539.jpg'),
            ('Ebano', 'Tastiera e piroli', 'Durissimo e nero: la tastiera dove le dita premono le corde per cent’anni.',
             'pexels-9996436.jpg'),
            ('Anelli', 'L’età del legno', 'Dagli anelli si legge l’anno in cui l’albero è cresciuto. Il nostro abete più '
             'vecchio ha quattrocento anni.', 'pexels-33999088.jpg')]
    return t.classic('provamateriale', {
        'voci': [{'id': 'an-l%d' % k, 'nome': n, 'codice': c, 'testo': te, 'immagine': K.img(f), 'miniatura': '',
                  'colore': '', 'alt': 'Il legno: %s' % n.lower()} for k, (n, c, te, f) in enumerate(voci)],
        'iniziale': 1, 'lente': True, 'lente_zoom': 2.6, 'lente_misura': 210, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passate sopra la foto: la lente mostra la fibra del legno',
        'testo_suggerimento_touch': 'Toccate la foto per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'Il legno', 'accento': C['acc'], 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        'sfondo_pannello': '', 'etichetta_color': C['tenue'], 'nome_font_family': 'heading', 'nome_font_size': 52,
        'nome_font_weight': '500', 'nome_color': C['testo'], 'codice_font_family': 'var(--olo-font-family-mono)',
        'codice_color': C['acc'], 'testo_color': C['tenue'], 'raggio_immagine': radius(4), 'proporzione': '3/2',
        'campione_forma': 'quadretto', 'campione_misura': 52, 'disposizione': 'lato', 'lato': 'sinistra',
        'larghezza_immagine': 58, 'gap': 56}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Anima · Cremona', ['Una bottega,', 'quattrocento anni di scuola.'], size=60,
                                layout='stack', size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Costruiamo come si costruiva a Cremona nel Seicento: su una forma interna di noce, a mano, '
                                'un pezzo alla volta. Sei violini, due viole e un violoncello all’anno, ognuno per chi lo '
                                'suonerà, provato in bottega finché non trova la sua voce.', size=17, color=C['testo']),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('200 h', 'Per un violino'), ('9', 'Strumenti all’anno'), ('24', 'Mani di vernice'),
                            ('1988', 'La bottega in via Platina')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I legni', ['Il suono', 'comincia dal bosco.'], size=60,
                         tagline='Scegliete un legno: la lente mostra la fibra, come la guardiamo noi prima di scegliere la '
                                 'tavola.')]),
        t.one([legni(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='legni', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_MAESTRO, 'La bottega', 'Il violino <em>si ascolta col pollice.</em>',
                             'Il maestro batte il piano con il pollice mentre lo assottiglia: dal suono capisce dove togliere '
                             'ancora un decimo di millimetro.', overlay=58))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli strumenti in bottega', ['Pronti', 'da provare.'], size=60,
                         tagline='Trascinate o lasciate scorrere. Si provano in bottega, con il vostro arco e il vostro '
                                 'insegnante, se volete.')]),
        t.one([K.coverflow(t, [
            ('pexels-36881009.jpg', 'Le effe di un violino da vicino', 'Violino «Platina»', 'Modello Guarneri · 2026'),
            ('pexels-306175.jpg', 'Un violino antico appoggiato', 'Violino del 1921', 'Restaurato · Mittenwald'),
            ('pexels-8519647.jpg', 'Un violoncello suonato con l’arco', 'Violoncello «Po»', 'Modello Stradivari · 2025'),
            ('pexels-25859077.jpg', 'Il riccio di un violino', 'Viola «Torrazzo»', '40,5 cm · 2026'),
            ('pexels-4231488.jpg', 'Un violino sugli spartiti', 'Violino da studio', 'Per i conservatori'),
            ('pexels-37210788.jpg', 'Il riccio e i piroli', 'Violino «Mezzanotte»', 'Vernice scura · 2025')],
            altezza=440, larghezza=320)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'L’ho provato accanto a tre violini antichi, al buio, e ho scelto il suo. Il maestro ha cambiato l’anima di '
                 'mezzo millimetro: adesso il mi canta anche in sala grande.', 'author_name': 'Elena Visconti',
        'author_role': 'Primo violino · orchestra da camera', 'avatar': '', 'rating': '5', 'text_color': C['testo'],
        'star_color': C['acc2'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc2']}, style={})])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_ARCO, 'La prova', 'Al buio, <em>col vostro arco.</em>',
                          'Gli strumenti si scelgono con le orecchie: in bottega li proviamo dietro una tenda, senza sapere '
                          'quale si sta ascoltando.', 'Prenota una prova', VISITA, overlay=60, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Anima',
                page_intro_caption='Liuteria · Cremona', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


STRUMENTI_ = [  # titolo, foto, categoria, descrizione, anno
    ('Violino «Platina»', 'pexels-36881009.jpg', 'Violini', 'Modello Guarneri del Gesù, vernice ambra · 16.500 €', '2026'),
    ('Violino «Mezzanotte»', 'pexels-37210788.jpg', 'Violini', 'Modello Stradivari, vernice scura · 15.000 €', '2025'),
    ('Violino del 1921', 'pexels-306175.jpg', 'Violini', 'Mittenwald, restaurato in bottega · 7.800 €', '1921'),
    ('Violino da studio', 'pexels-4231488.jpg', 'Violini', 'Per i conservatori, montato in bottega · 4.200 €', '2026'),
    ('Viola «Torrazzo»', 'pexels-25859077.jpg', 'Viole', 'Cassa 40,5 cm, voce scura · 17.000 €', '2026'),
    ('Viola «Lanterna»', 'pexels-7095503.jpg', 'Viole', 'Cassa 41 cm, modello Gasparo · 18.500 €', '2025'),
    ('Violoncello «Po»', 'pexels-8519647.jpg', 'Violoncelli', 'Modello Stradivari 1707 · 28.000 €', '2025'),
    ('Violoncello da studio', 'pexels-33640401.jpg', 'Violoncelli', 'Tre quarti, per i ragazzi · 9.500 €', '2026'),
]


def build_strumenti():
    t = Tpl(ID, 'gli-strumenti')
    out = [K.intro(t, 'Gli strumenti', ['Pronti in bottega,', 'su ordinazione.'],
                   'Gli strumenti pronti si provano su appuntamento; per quelli su ordinazione servono dieci mesi. Cercate '
                   'per strumento, modello o anno.', bg=K.photo_bg('pexels-19585038.jpg', overlay=82, color=C['fondo']))]
    griglia = t.classic('portfolio', {
        'source': 'manual', 'layout': 'grid', 'columns': 4, 'gap': 24, 'image_ratio': '4:5', 'image_fit': 'cover',
        'hover_effect': 'zoom', 'animation': 'fade', 'caption_position': 'below', 'enable_search': True,
        'search_placeholder': 'Cerca: violino, Stradivari, 2026…', 'show_title': True, 'show_category': True,
        'show_excerpt': True, 'font_family': 'heading', 'font_weight': '500', 'title_color': C['testo'],
        'text_color': C['tenue'], 'accent_color': C['acc'], 'border_radius': 4, 'year_stamp': True, 'index_numbering': False,
        'items': [{'title': ti, 'image_url': K.img(f), 'category': cat, 'description': d, 'year': a,
                   'link_url': VISITA + '?strumento=' + ti.replace(' ', '-').replace('«', '').replace('»', '') + '#prova'}
                  for ti, f, cat, d, a in STRUMENTI_],
        'columns_tablet': '2', 'columns_mobile_landscape': '2'}, style={})
    out.append(t.section([t.one([griglia])], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    scheda = t.classic('schedatecnica', {
        'codice': 'AN-2026-07', 'nome': 'Violino «Platina»', 'etichetta': 'In bottega',
        'descrizione': 'Sul modello del «Cannone» di Guarneri del Gesù, con le bombature un poco più alte per una voce più '
                       'calda. Abete di Fiemme del 2004, acero dei Balcani marezzato a fiamma larga.',
        'immagine': K.img('pexels-36881009.jpg'), 'titolo_specifiche': 'Le misure',
        'specifiche': [{'id': 'as-%d' % k, 'gruppo': g, 'nome': a, 'valore': b, 'unita': u} for k, (g, a, b, u) in enumerate([
            ('Cassa', 'Lunghezza', '354', 'mm'), ('Cassa', 'Fasce', '30,5', 'mm'), ('Cassa', 'Peso', '398', 'g'),
            ('Legni', 'Piano', 'Abete di Fiemme, 2004', ''), ('Legni', 'Fondo', 'Acero dei Balcani', ''),
            ('Finitura', 'Vernice', 'Olio, 24 mani', ''), ('Finitura', 'Corde', 'Budello e acciaio', ''),
            ('Prezzo', 'Con custodia', '16.500', '€')])],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'Certificato della bottega · etichetta firmata',
        'cta_testo': 'Prenota la prova', 'cta_url': VISITA + '#prova', 'cta_codice': True, 'cta_parametro': 'strumento',
        'dati_strutturati': True, 'accento': C['acc'], 'sfondo_scheda': C['chiaro'], 'colore_linee': C['filo'],
        'righe_alternate': False, 'nome_font_size': 44, 'nome_font_weight': '500', 'nome_color': C['testo'],
        'raggio': radius(4), 'raggio_pulsante': radius(4), 'padding_scheda': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40},
        'img_ratio': '4/5', 'img_fit': 'cover', 'foto_posizione': 'sinistra', 'larghezza_foto': 42, 'gap': 48,
        'colonne_specifiche': 1}, style={})
    out.append(t.section([t.one([scheda])], bg=solid(C['sala']), padding='large', width='large',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SUONO, 'Il suono', 'Un violino nuovo <em>impara a suonare.</em>',
                             'Nei primi mesi il legno si assesta e la voce si apre. Chi compra da noi torna dopo un anno per '
                             'la messa a punto: è compresa.', overlay=58))
    out.append(K.domande(t, 'Prima', 'di scegliere.', [
        ('Si può provare a casa?', 'Sì, per una settimana, dopo la prima prova in bottega. Lo assicuriamo noi.'),
        ('Fate il permuta?', 'Sì: valutiamo il vostro strumento e lo scaliamo dal prezzo del nuovo.'),
        ('Quanto ci vuole su ordinazione?', 'Dieci mesi per un violino, quattordici per un violoncello, con due prove a metà '
                                            'lavoro.'),
        ('Fate strumenti per bambini?', 'Sì, da un quarto a tre quarti, anche a noleggio con riscatto.'),
    ], 'st', bg=False))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Quattro banchi,', 'una finestra a nord.'],
                   'La luce da nord non cambia durante il giorno: per questo le botteghe di liuteria guardano tutte da quella '
                   'parte. La nostra dà sui tetti di via Platina.', bg=K.photo_bg('pexels-6075991.jpg', overlay=80,
                                                                                   color=C['fondo']))]
    fasi = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': True, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('I', 'La forma', 'pexels-6075992.jpg', 'Le fasce', 'Il profilo', 'sulla forma', 'Le fasce d’acero piegate col '
             'ferro caldo attorno alla forma di noce: il disegno di tutto lo strumento.', '1,1', 'mm le fasce'),
            ('II', 'Il piano', 'pexels-37864375.jpg', 'L’intaglio', 'Sgorbia', 'e pialletti', 'Il piano si scava dal pieno, '
             'si assottiglia ascoltandolo, fino a due millimetri e mezzo.', '2,5', 'mm al centro'),
            ('III', 'La vernice', 'pexels-19591432.jpg', 'La finitura', 'Ventiquattro', 'mani a olio', 'Una mano al giorno, '
             'asciugata al sole del terrazzo. È lei a dare il colore, e un po’ della voce.', '24', 'mani')]],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': C['acc'],
        'counter_font_family': 'heading', 'counter_size': 88, 'counter_color': C['acc'], 'counter_italic': True,
        'counter_weight': '500', 'tag_color': C['testo'], 'pre_title_color': C['acc'], 'title_font_family': 'heading',
        'title_size': 34, 'title_weight': '500', 'title_color': C['testo'], 'title_accent_color': C['acc'],
        'description_color': C['tenue'], 'footer_icon': 'ruler', 'footer_value_color': C['testo'],
        'footer_label_color': C['tenue'], 'columns': 3, 'gap': 48, 'media_radius': radius(4)}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Come nasce', ['Duecento ore,', 'tre momenti.'], size=56)]), t.one([fasi])],
                         padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SGORBIA, 'L’intaglio', 'Un decimo <em>alla volta.</em>',
                             'La sgorbia toglie trucioli sottili come carta. Si misura con lo spessimetro e si ascolta col '
                             'pollice, finché il legno risponde.', overlay=58))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Un maestro,', 'due mani in più.'], size=56)]),
        K.squadra(t, [
            ('pexels-39294525.jpg', 'Ettore Bassi', 'Maestro liutaio', 'Diplomato alla scuola di Cremona nel 1979, ha costruito '
             'più di trecento strumenti. Fa ancora ogni piano da sé.', '50% 30%'),
            ('pexels-6661905.jpg', 'Giulia Rota', 'Vernici e restauro', 'Restauratrice, prepara le vernici a olio e ritocca '
             'gli strumenti antichi senza toglierne gli anni.', '50% 30%'),
            ('pexels-5974292.jpg', 'Jun Park', 'Allievo del terzo anno', 'Arrivato da Seul per la scuola, sta finendo il suo '
             'primo violoncello.', '50% 30%')], sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='scuola', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([t.classic('scrubtext', {
            'text': 'L’anima è un cilindro d’abete di sei millimetri: <em>spostata di un soffio, cambia la voce.</em>',
            'show_lead': True, 'lead': 'Sta in piedi da sola dentro il violino, fra il piano e il fondo, sotto il piede destro '
                                       'del ponticello. Gli strumenti si regolano spostandola con un ferro sottile dalla effe.',
            'scroll_reveal': True, 'dim_opacity': 18, 'accent': C['acc'], 'text_color': C['testo'], 'lead_color': C['tenue'],
            'size_min': 32, 'size_max': 60, 'max_width_ch': 24, 'lead_size': 18, 'text_font_weight': '500',
            'text_align': 'center'}, style={})]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_visita():
    t = Tpl(ID, 'visita')
    out = [K.intro(t, 'Visita e prova', ['In bottega,', 'su appuntamento.'],
                   'Venite a provare uno strumento, a portarne uno da restaurare, o solo a vedere come si lavora. Il caffè è '
                   'sul fornello del retro.', bg=K.photo_bg('pexels-36458695.jpg', overlay=80, color=C['fondo']), size=92)]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Lunedì — venerdì', 'time': '9 — 12:30', 'note': 'La mattina si costruisce: visite brevi'},
                  {'day': 'Lunedì — venerdì', 'time': '14:30 — 18', 'note': 'Il pomeriggio le prove degli strumenti'},
                  {'day': 'Sabato', 'time': 'Su richiesta', 'note': 'Per chi viene da lontano'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': C['acc'], 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 44,
        'time_weight': '500', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    out.append(t.section([t.one([orari])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 60}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Perché venite', 'cosa', True,
                     opzioni='Provare uno strumento\nUn restauro\nUno strumento su ordinazione\nLa scuola\nUna visita'),
             K.campo('text', 'Strumento', 'strumento', param='strumento', segnaposto='Se ne avete visto uno'),
             K.campo('date', 'Giorno', 'giorno'), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Il vostro strumento', 'messaggio', larghezza='1-1',
                     segnaposto='Che cosa suonate, a che livello, che voce cercate.')]
    out.append(K.sezione_modulo(t, 'prova', 'L’appuntamento', ['Portate', 'il vostro arco.'],
                                'La prova dura un’ora e mezza. Portate l’arco e, se volete, il vostro insegnante: ascoltare da '
                                'fuori aiuta a scegliere.', VOCI, campi, 'Anima — appuntamento', 'Chiedi l’appuntamento',
                                'Ricevuto. Vi scriviamo entro due giorni con le date.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Dietro il Torrazzo,', 'in via Platina.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via Platina 18, Cremona'), ('Dalla stazione', 'quindici minuti a piedi'),
                                  ('Il Museo del Violino', 'a dieci minuti'), ('Parcheggio', 'piazza Marconi')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Liuteria Anima · via Platina 18', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · corda rotta', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è stonata.</em>', size=88, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Riaccordiamo e torniamo all’inizio.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna in bottega', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BOTTEGA, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Industria e manifattura', 'Le corde che vibrano',
    ['liuteria', 'violino', 'violoncello', 'artigianato', 'cremona', 'chiaro', 'slider pro', '3d'],
    'Una bottega di liuteria, carta, acero e vernice a olio: in apertura, sopra il video della bottega, lo strumento '
    'nella buca rotonda con la rosetta che gira, quattro corde che ci passano sopra e vibrano e le onde del suono; i '
    'legni sotto la lente, gli strumenti in coverflow 3D, la prova al buio e la scuola.',
    [('Gli strumenti', STRUMENTI), ('La bottega', BOTTEGA), ('I legni', HOME + '#legni'), ('Visita e prova', VISITA)],
    [('home', 'Home', None), ('gli-strumenti', 'Gli strumenti', 'gli-strumenti'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('visita', 'Visita e prova', 'visita')],
    HEX, '\'Gantari\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Crimson Pro\', \'Georgia\', serif',
    '\'Fragment Mono\', ui-monospace, Menlo, monospace', ['Crimson Pro', 'Gantari', 'Fragment Mono'],
    corsivo_di=['Crimson Pro'], peso_titoli='500', raggio=4, grana=5, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '3'  # apertura scura sul video pieno, 31 ottobre 2026


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'gli-strumenti.json': build_strumenti(), 'la-bottega.json': build_bottega(), 'visita.json': build_visita(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
