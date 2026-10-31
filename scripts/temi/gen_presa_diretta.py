# -*- coding: utf-8 -*-
"""Genera il tema «Presa Diretta» (id presa-diretta): uno studio di registrazione a Lecce, tre sale, una consolle
analogica e un registratore a nastro da due pollici; registrazione, mix, mastering e sala prove.

Tema scuro: nero di sala, l'ambra delle lancette dei VU-metri, il rosso della spia REC e il verde acqua dei led.
Titoli in SUSE, testo in Golos Text, dati in Reddit Mono.

Apertura: Slider Pro «la consolle», alto 16:9 perché i cerchi restino cerchi. Le due bobine del registratore che
girano insieme col nastro teso fra loro, due VU-metri con le lancette che ballano a tempo (timeline con l'origine in
basso) e la spia REC che lampeggia. Una diapositiva per servizio, a linguette: registrazione, mix, mastering, sala prove.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'presa-diretta'
NAME = 'Presa Diretta'

HEX = {
    'primary': '#F5A524', 'primary_contrast': '#140F08', 'secondary': '#E8463A', 'secondary_contrast': '#140F08',
    'accent': '#7FD1C2', 'dark': '#070609', 'light': '#F4F0EA', 'muted': '#18151B', 'muted_contrast': '#ECE6DF',
    'text': '#ECE6DF', 'text_muted': '#A39B94', 'background': '#0E0C10', 'border': '#2B2630', 'link': '#F5A524',
}

HOME, STUDIO, TARIFFE, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:lo-studio}}', '{{PAGINA:tariffe}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'sala@presadiretta.example', '+39 0832 000 245', 'tel:+390832000245'
LAT, LNG = '40.3489', '18.1810'

V_FADER, V_CONSOLLE, V_VOCE, V_MICROFONO, V_BATTERIA, V_NASTRO = (
    'pexels-video-36407032', 'pexels-video-8197844', 'pexels-video-7087183', 'pexels-video-7087180',
    'pexels-video-8117178', 'pexels-video-11124042')
PHOTOS = [
    'pexels-8197361.jpg', 'pexels-8132717.jpg', 'pexels-28930613.jpg', 'pexels-2573957.jpg', 'pexels-40213209.jpg',
    'pexels-8132712.jpg', 'pexels-8132724.jpg', 'pexels-7450049.jpg', 'pexels-8198559.jpg', 'pexels-5179401.jpg',
    'pexels-12564395.jpg', 'pexels-8476438.jpg', 'pexels-15383586.jpg', 'pexels-8198677.jpg', 'pexels-25677019.jpg',
    'pexels-8198136.jpg', 'pexels-33071233.jpg', 'pexels-8198055.jpg', 'pexels-8197270.jpg', 'pexels-7087608.jpg',
    'pexels-8132988.jpg', 'pexels-7091308.jpg', 'pexels-5650906.jpg', 'pexels-7088373.jpg', 'pexels-8197263.jpg',
    'pexels-5648526.jpg', 'pexels-7086295.jpg', 'pexels-7586649.jpg', 'pexels-7086736.jpg', 'pexels-8044048.jpg',
    'pexels-6671715.jpg', 'pexels-5809298.jpg', 'pexels-7858640.jpg', 'pexels-5809457.jpg', 'pexels-8198170.jpg',
    'pexels-37148209.jpg', 'pexels-8197364.jpg', 'pexels-8382031.jpg', 'pexels-8382285.jpg', 'pexels-8382282.jpg',
    'pexels-6764877.jpg', 'pexels-8102702.jpg', 'pexels-8132798.jpg', 'pexels-8412406.jpg', 'pexels-5657662.jpg',
    'pexels-5764288.jpg', 'pexels-19829189.jpg', 'pexels-12955672.jpg', 'pexels-4200747.jpg',
]
VIDEOS = [V_FADER, V_CONSOLLE, V_VOCE, V_MICROFONO, V_BATTERIA, V_NASTRO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='600', corsivo=False, maiuscole_pulsanti=True)
C = K.c
AMBRA, REC, LED = C['acc'], C['sec'], C['acc2']
TITOLI, MONO = 'SUSE', 'Reddit Mono'
VOCI = [('map-pin', 'Viale Otranto 15 · Lecce', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Presa Diretta', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Presa Diretta', 'Studio di registrazione a Lecce dal 2011: tre sale, una consolle analogica a '
                   'trentadue canali e un registratore a nastro da due pollici. Registrazione, mix, mastering e sala prove.',
                   [('Lo studio', STUDIO), ('Le tariffe', TARIFFE), ('Prenota', PRENOTA), ('Come arrivare', PRENOTA + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Lo studio', 'Tutti i giorni 10 — 2<br>La sala prove anche la domenica', VOCI,
                   '© 2026 Presa Diretta srl · Lecce · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com'), ('spotify', 'https://spotify.com')],
                   marchio_size=120)


SERVIZI = [  # linguetta, occhiello, riga 1, riga 2, testo, sfondo
    ('Registrazione', '01 · la registrazione', 'Suonate insieme,', 'come dal vivo.', 'La band in sala, tutti nella stessa '
     'stanza, sul nastro da due pollici. Le sovraincisioni dopo, se servono. Di solito non servono.', 'pexels-8198136.jpg'),
    ('Mix', '02 · il mix', 'Trentadue canali,', 'due mani sui fader.', 'Il mix lo facciamo sulla consolle analogica, '
     'in tempo reale, con voi in sala. Le versioni le ascoltate in auto prima di approvarle.', 'pexels-8197361.jpg'),
    ('Mastering', '03 · il mastering', 'L’ultimo passaggio,', 'senza schiacciare.', 'Per lo streaming, il vinile e la radio: '
     'tre master diversi, con la dinamica che il disco merita. Niente gara al volume.', 'pexels-25677019.jpg'),
    ('Sala prove', '04 · la sala prove', 'Una sala,', 'tutta la notte.', 'Batteria, amplificatori e impianto voce già '
     'montati. Si registra anche la prova, in stereo: vi portate a casa il file.', 'pexels-7091308.jpg'),
]


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, foto) in enumerate(SERVIZI):
        n = i + 1
        sl.append(K.diapositiva('pd-%d' % n, K.sfondo_foto(foto, overlay=0.84, scala=1.08, durata=10000,
                                                           verso='in' if n % 2 else 'out', colore=C['fondo']), [
            K.livello('pd-o%d' % n, contenuto=occ, x=6, y=18, w=40, size=12, peso='500', colore=AMBRA, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 42, 'x': 7, 'width': 86}}),
            K.livello('pd-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=22.5, w=50, size=76, peso='600',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=350,
                      durata=900, charAnim={'stagger': 18, 'offsetY': 40, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 44.5, 'x': 7, 'width': 88}}),
            K.livello('pd-i%d' % n, contenuto=r2, tag='p', x=6, y=33, w=50, size=76, peso='600', colore=AMBRA,
                      fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=600, durata=900,
                      charAnim={'stagger': 18, 'offsetY': 40, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 48.8, 'x': 7, 'width': 88}}),
            K.livello('pd-s%d' % n, contenuto=testo, x=6, y=48, w=33, size=17, peso='400', colore=mix(C['chiaro'], 80),
                      lineHeight=1.65, ritardo=950, responsive={'mobile': {'fontSize': m(14), 'y': 55, 'x': 7, 'width': 86}}),
            K.livello('pd-b%d' % n, tipo='button', contenuto='Prenota la sala', x=6, y=70, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=AMBRA, padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=6, ritardo=1250, buttonUrl=PRENOTA, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 70, 'x': 7, 'fontSize': m(11)}}),
        ], durata=7500, tab=tab))
    # le due bobine col nastro, i VU-metri e la spia REC: restano fra una diapositiva e l'altra
    globali = [K.livello('pd-nastro', tipo='shape', x=67.2, y=45.6, w=20.8, h=0.5, bgColor=mix(AMBRA, 55, C['nero']),
                         entra='maskFromLeft', ritardo=600, durata=900, responsive={'mobile': {'visible': False}})]
    for k, (x, foto, dur) in enumerate([(56.8, 'pexels-8382031.jpg', 9000), (77.6, 'pexels-6764877.jpg', 9000)]):
        globali.append(K.livello('pd-bobina%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=x, y=8.6,
                                 w=20.83, h=37.04, borderRadius=999, borderWidth=5, borderColor=mix(C['chiaro'], 18),
                                 boxShadow={'x': 0, 'y': 24, 'blur': 60, 'spread': -14, 'color': mix(C['nero'], 85)},
                                 timeline=K.tl(x, 8.6, [(0, {'rotation': 0}), (dur, {'rotation': -360})], dur),
                                 responsive={'mobile': {'x': 8 + 46 * k, 'y': 6, 'width': 40, 'height': 18.3}}))
        globali.append(K.livello('pd-mozzo%d' % k, tipo='shape', x=x + 9.03, y=8.6 + 16.05, w=2.78, h=4.94, bgColor=C['nero'],
                                 borderRadius=999, borderWidth=3, borderColor=mix(C['chiaro'], 40),
                                 responsive={'mobile': {'x': 8 + 46 * k + 17.6, 'y': 6 + 7.9, 'width': 4.8, 'height': 2.2}}))
    for k, x in enumerate([60.5, 77.0]):
        globali += [
            K.livello('pd-vu%d' % k, tipo='shape', x=x, y=56, w=15, h=27, bgColor=mix(AMBRA, 14, C['chiaro']), borderRadius=8,
                      borderWidth=2, borderColor=mix(C['nero'], 60), entra='fadeInUp', ritardo=700 + 150 * k,
                      boxShadow={'x': 0, 'y': 20, 'blur': 50, 'spread': -12, 'color': mix(AMBRA, 25)},
                      responsive={'mobile': {'x': 8 + 46 * k, 'y': 26, 'width': 40, 'height': 12.5}}),
            K.livello('pd-sc%d' % k, contenuto='−20  −10  −5  0  +3', x=x, y=59.5, w=15, size=12, peso='500',
                      colore=mix(C['nero'], 75), fontFamily=MONO, align='center', letterSpacing=1, entra='fadeIn',
                      ritardo=900 + 150 * k, responsive={'mobile': {'x': 8 + 46 * k, 'y': 27.4, 'width': 40, 'fontSize': m(10)}}),
            K.livello('pd-vul%d' % k, contenuto='VU · %s' % ('L' if k == 0 else 'R'), x=x, y=76.5, w=15, size=11, peso='600',
                      colore=mix(C['nero'], 70), fontFamily=MONO, align='center', letterSpacing=3, entra='fadeIn',
                      ritardo=900 + 150 * k, responsive={'mobile': {'x': 8 + 46 * k, 'y': 35.6, 'width': 40, 'fontSize': m(9)}}),
            K.livello('pd-ago%d' % k, tipo='shape', x=x + 7.35, y=63, w=0.3, h=16.5, bgColor=REC,
                      timeline=K.tl(x + 7.35, 63, [(ms, {'rotation': r, 'originX': 50, 'originY': 100})
                                                   for ms, r in [(0, -40), (260, 6 + 4 * k), (520, -8), (800, 24 - 6 * k),
                                                                 (1060, 2), (1300, 30), (1580, -14), (1860, 16 + 5 * k),
                                                                 (2140, -6), (2420, 26), (2800, -40)]],
                                    2800, curva='sine.inOut'),
                      responsive={'mobile': {'x': 8 + 46 * k + 19.85, 'y': 29.3, 'width': 0.5, 'height': 7}}),
        ]
    globali += [
        K.livello('pd-rec', tipo='shape', x=60.5, y=88.2, w=0.83, h=1.48, bgColor=REC, borderRadius=999,
                  boxShadow={'x': 0, 'y': 0, 'blur': 12, 'spread': 2, 'color': mix(REC, 70)},
                  timeline=K.tl(60.5, 88.2, [(0, {'opacity': 100}), (600, {'opacity': 100}), (700, {'opacity': 15}),
                                             (1200, {'opacity': 15}), (1300, {'opacity': 100})], 1300),
                  responsive={'mobile': {'x': 8, 'y': 40, 'width': 1.9, 'height': 0.86}}),
        K.livello('pd-tc', contenuto='REC · 00:03:27:14 · 24 bit / 96 kHz · nastro 2″', x=62, y=87.6, w=34, size=11, peso='500',
                  colore=mix(C['chiaro'], 62), fontFamily=MONO, letterSpacing=2, entra='fadeIn', ritardo=1500,
                  responsive={'mobile': {'x': 11, 'y': 39.6, 'width': 80, 'fontSize': m(9)}}),
    ]
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fadeThroughDark',
                       durata_trans=1000, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=7500, progressBarColor=AMBRA, navColor=C['chiaro'])


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Presa Diretta · Lecce', ['Tre sale,', 'un nastro da due pollici.'], size=60,
                                layout='stack', size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Abbiamo aperto in un ex mobilificio fuori porta Napoli: soffitti di sei metri, muri di tufo '
                                'e legno, una consolle a trentadue canali comprata a Londra e restaurata canale per canale. '
                                'Registriamo band, cantautori, colonne sonore e podcast.', size=17, color=C['testo']),
                      K.button(t, 'Lo studio', STUDIO, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('3', 'Sale, la grande da 80 m²'), ('32', 'Canali analogici'), ('2″', 'Il nastro, a 30 ips'),
                            ('400+', 'Dischi usciti da qui')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    sale = t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': k, 'title': ti, 'link': STUDIO + '#' + a, 'span': s, 'aspect': asp}
                  for f, k, ti, a, s, asp in [
                      ('pexels-7091308.jpg', '80 m² · tufo e legno · la band intera', 'La sala grande', 'sale', 6, '4/3'),
                      ('pexels-8198677.jpg', 'Consolle a 32 canali · nastro 2″', 'La regia', 'regia', 6, '4/3'),
                      ('pexels-12564395.jpg', '12 m² · assorbente · voci e fiati', 'La cabina', 'sale', 6, '4/3'),
                      ('pexels-8197270.jpg', 'Amplificatori e batteria montati', 'La sala prove', 'sale', 6, '4/3')]],
        'columns': 2, 'gap': 14, 'object_position': 'center center', 'object_fit': 'cover', 'radius': 6,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 22), 'arrow_color': C['chiaro'], 'arrow_hover_bg': AMBRA,
        'arrow_hover_color': C['on_acc'], 'show_arrow': True, 'title_size': 40, 'title_weight': '600', 'kicker_size': 11,
        'card_radius': radius(6)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Le sale', ['Quattro stanze,', 'un suono.'], size=60,
                         tagline='Tutte collegate alla regia con le linee analogiche: si suona in una, si ascolta nell’altra.')]),
        t.one([sale]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_MICROFONO, 'La voce', 'Un microfono <em>del 1958.</em>',
                             'Le voci le registriamo con un microfono a valvole che ha più anni dello studio. Lo accendiamo '
                             'mezz’ora prima: le valvole devono scaldarsi.', overlay=52))
    uscite = t.classic('worklist', {
        'items': [{'number': nu, 'title': ti, 'category': ca, 'year': an, 'link_url': STUDIO + '#uscite'} for nu, ti, ca, an in [
            ('LP', 'Tramontana', 'Le Salentine · registrato dal vivo in due giorni', '2026'),
            ('EP', 'Notte di tufo', 'Marco Vetrugno · voce, chitarra, un violoncello', '2026'),
            ('OST', 'Il ritorno del mare', 'Colonna sonora · trenta musicisti in sala grande', '2025'),
            ('LP', 'Fuori porta', 'Bassi & Calce · mix analogico, master per vinile', '2025'),
            ('PODCAST', 'Voci del Sud', 'Dodici puntate · registrate in cabina', '2025')]],
        'divider_color': C['filo'], 'row_padding_y': 28, 'row_hover_bg': mix(AMBRA, 8), 'hover_indent': 28,
        'number_color': AMBRA, 'number_size': 13, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 34, 'title_weight': '600', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': LED, 'year_size': 15, 'show_arrow': True, 'arrow_color': AMBRA,
        'mono_font_family': MONO}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Usciti da qui', ['Gli ultimi', 'dischi.'], size=60,
                         tagline='Band, cantautori, colonne sonore. Passate su una riga per vedere chi c’era in sala.')]),
        t.one([uscite]),
    ], padding='large', width='large', html_id='uscite', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Abbiamo registrato il disco in due giorni, tutti insieme nella sala grande, come ai concerti. Tommaso ci ha '
                 'fatto rifare una sola canzone: aveva ragione.', 'author_name': 'Le Salentine',
        'author_role': 'Tramontana · LP, 2026', 'avatar': '', 'rating': '5', 'text_color': C['testo'], 'star_color': AMBRA,
        'author_color': C['tenue'], 'quote_accent_color': AMBRA, 'quote_font': 'heading', 'quote_size': 36,
        'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True, 'line_color': REC},
        style={})])], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_NASTRO, 'Il nastro', 'Trenta pollici <em>al secondo.</em>',
                          'Il registratore a bobine gira a 30 ips, ventiquattro tracce su un nastro da due pollici. Il '
                          'suono è caldo, le chitarre respirano. Poi tutto passa in digitale per il mix.', 'Lo studio', STUDIO,
                          overlay=60, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Presa Diretta',
                page_intro_caption='● REC · Lecce', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1500, page_intro_once=True)


def build_studio():
    t = Tpl(ID, 'lo-studio')
    out = [K.intro(t, 'Lo studio', ['Un mobilificio,', 'tre sale, una regia.'],
                   'Ottocento metri quadri fuori porta Napoli: muri di tufo, soffitti alti, legno ovunque. Il suono della '
                   'sala grande è il motivo per cui le band vengono da Milano.', bg=K.photo_bg('pexels-8198055.jpg', overlay=64),
                   overlay_text=True)]
    consolle = t.classic('hotspot', {
        'image': K.img('pexels-8197361.jpg'), 'image_alt': 'La consolle analogica della regia, coi tasti illuminati',
        'image_height': '600', 'aspect_ratio': '16/9', 'object_fit': 'cover', 'object_position': 'center center',
        'markers': [{'id': 'pd-h%d' % i, 'pos_x': str(x), 'pos_y': str(y), 'title': ti, 'description': de, 'icon': '',
                     'tooltip_position': pos, 'link': '', 'link_label': ''} for i, (x, y, ti, de, pos) in enumerate([
                         (22, 70, 'I fader', 'Trentadue cursori, uno per strumento: il mix si fa con le mani, come un pianista.', 'right'),
                         (48, 38, 'Gli equalizzatori', 'Quattro bande per canale, restaurate condensatore per condensatore.', 'top'),
                         (72, 55, 'I bus', 'Si raggruppano batteria, chitarre e voci per comprimerle insieme.', 'left'),
                         (84, 24, 'Il monitor', 'Le casse di regia: il mix si ascolta anche sulla radiolina e in auto.', 'left')])],
        'border_radius': 6, 'marker_style': 'dot', 'marker_color': AMBRA, 'marker_text_color': C['nero'], 'marker_size': 22,
        'pulse_animation': True, 'tooltip_bg': C['sala'], 'tooltip_color': C['testo'], 'tooltip_width': 280})
    out.append(t.section([
        t.one([K.sechead(t, 'La regia', ['La consolle,', 'da vicino.'], size=56,
                         tagline='Toccate i punti: quattro cose che usiamo in ogni mix.')]),
        t.one([consolle]),
    ], padding='large', width='large', html_id='regia', style={'padding_top': 110, 'padding_bottom': 100}))
    sessione = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': AMBRA, 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': AMBRA, 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('10', 'La mattina', 'pexels-7450049.jpg', 'Il montaggio', 'Microfoni', 'e cuffie', 'Un’ora per piazzare i microfoni '
             'e trovare il suono di ogni strumento nella stanza.', '24', 'microfoni'),
            ('14', 'Il pomeriggio', 'pexels-5650906.jpg', 'Le riprese', 'Tutti', 'insieme', 'La band suona dal vivo, tre o '
             'quattro prese per brano. Si sceglie la migliore, non la perfetta.', '3', 'prese a brano'),
            ('20', 'La sera', 'pexels-8132717.jpg', 'Il premix', 'Si ascolta', 'in regia', 'Un primo mix al volo per portarvi a '
             'casa le canzoni del giorno, da riascoltare in auto.', '1', 'mix al giorno')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': AMBRA,
        'counter_font_family': 'heading', 'counter_size': 80, 'counter_color': AMBRA, 'counter_italic': False,
        'counter_weight': '600', 'tag_color': C['chiaro'], 'pre_title_color': LED, 'title_font_family': 'heading',
        'title_size': 30, 'title_weight': '600', 'title_color': C['chiaro'], 'title_accent_color': AMBRA,
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'clock', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(6)}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Una giornata', ['Dalle dieci', 'a notte fonda.'], size=56)]), t.one([sessione])],
                         bg=solid(C['sala']), padding='large', width='large', html_id='sale',
                         style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Tre orecchie', 'in regia.'], size=56)]),
        K.squadra(t, [
            ('pexels-8132798.jpg', 'Tommaso Greco', 'Fonico e produttore', 'Ha aperto lo studio nel 2011 dopo dieci anni a '
             'Londra. Registra e mixa quasi tutto quello che esce da qui.', '50% 25%'),
            ('pexels-8412406.jpg', 'Yuna Sato', 'Mastering', 'Da Tokyo a Lecce per il nastro. Fa i master per vinile, '
             'streaming e radio, ognuno diverso.', '50% 25%'),
            ('pexels-8102702.jpg', 'Samuel Okafor', 'Assistente e sala prove', 'Batterista, monta i microfoni e tiene in vita '
             'la sala prove fino all’alba.', '50% 30%')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_BATTERIA, 'La sala grande', 'La batteria <em>suona la stanza.</em>',
                             'Otto microfoni sulla batteria e due in alto, lontani: sono loro a prendere il suono del tufo, '
                             'quello che nessun plugin rifà.', overlay=55))
    return out


def build_tariffe():
    t = Tpl(ID, 'tariffe')
    out = [K.intro(t, 'Le tariffe', ['A giornata,', 'a brano, a ora.'],
                   'Il fonico è sempre compreso. Il nastro da due pollici si paga a parte, se lo volete: 180 € a bobina, '
                   'e la bobina è vostra.', bg=K.photo_bg('pexels-28930613.jpg', overlay=64), overlay_text=True)]
    out.append(t.section([
        t.one([K.sechead(t, 'In studio', ['Tre modi', 'di lavorare.'], size=56,
                         tagline='Prezzi IVA esclusa. Per i dischi interi facciamo un preventivo, con le giornate e i master.')]),
        K.piani(t, [('Sala prove', '18', 'all’ora · minimo due', 'Batteria e amplificatori montati\nImpianto voce\nLa prova '
                     'registrata in stereo', False, ''),
                    ('Giornata', '380', 'dieci ore con il fonico', 'Sala grande e regia\nFino a ventiquattro tracce\nIl premix '
                     'della sera', True, 'La più chiesta'),
                    ('Mix', '190', 'a brano', 'Sulla consolle analogica\nDue revisioni comprese\nStems per il live', False, '')],
                'Prenota', PRENOTA, nota='IVA esclusa · il nastro a parte'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    master = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in [
                      ('Master per lo streaming', 'Un brano, livelli per le piattaforme', '60 €', 'pexels-8132724.jpg', ''),
                      ('Master per vinile', 'Un lato, con la prova di lacca', '220 €', 'pexels-5764288.jpg', 'Analogico'),
                      ('Master per la radio', 'Un brano, versione più compatta', '50 €', 'pexels-2573957.jpg', ''),
                      ('Riversamento da nastro', 'Bobine vecchie in digitale, a ora', '45 €', 'pexels-8382285.jpg', '')]],
        'separator_style': 'dotted', 'separator_color': mix(AMBRA, 40), 'title_color': C['testo'], 'price_color': AMBRA,
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': LED, 'badge_color': C['nero'], 'badge_border_radius': '6',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il mastering', ['Tre master,', 'tre ascolti.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Ogni formato vuole il suo master: lo streaming abbassa il volume, il vinile non ama i bassi '
                                'stereo, la radio comprime tutto. Ve li facciamo ascoltare uno accanto all’altro.')],
                     wm='2-5', ws='1-1'),
               t.col([master], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di entrare in sala.', [
        ('Dobbiamo portare gli strumenti?', 'Le chitarre sì; batteria, amplificatori, tastiere e microfoni ci sono. Se avete '
                                            'il vostro rullante, portatelo.'),
        ('Quanto ci vuole per un disco?', 'Un disco di dieci brani suonato dal vivo: quattro o cinque giornate, più una '
                                          'settimana di mix.'),
        ('Fate anche podcast?', 'Sì, nella cabina: due o quattro voci, con la regia e il montaggio.'),
        ('Si può pagare a rate?', 'Per i dischi interi sì, in tre parti: all’inizio, al mix e al master.'),
    ], 'tr', bg=False))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Una giornata,', 'un’ora, una notte.'],
                   'Scriveteci che cosa volete registrare: vi rispondiamo con le date libere e un preventivo.',
                   bg=K.photo_bg('pexels-12564395.jpg', overlay=64), overlay_text=True, size=92)]
    campi = [K.campo('text', 'Nome o band', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa', 'cosa', True,
                     opzioni='Registrazione\nMix\nMastering\nSala prove\nPodcast\nUn disco intero'),
             K.campo('date', 'Dal giorno', 'dal', True), K.campo('date', 'Al giorno', 'al'),
             K.campo('select', 'Quanti siete', 'persone', opzioni='1\n2 — 3\n4 — 6\nPiù di 6'),
             K.campo('text', 'Link ai provini', 'link', larghezza='1-1', segnaposto='Anche un vocale registrato col telefono'),
             K.campo('textarea', 'Il progetto', 'messaggio', larghezza='1-1',
                     segnaposto='Quanti brani, che genere, che suono cercate.')]
    out.append(K.sezione_modulo(t, 'prenota', 'La prenotazione', ['Raccontateci', 'il progetto.'],
                                'Vi rispondiamo entro un giorno. La caparra è del 30% e si recupera se spostate con una '
                                'settimana di anticipo.', VOCI, campi, 'Presa Diretta — prenotazione', 'Invia',
                                'Ricevuta! Vi scriviamo entro un giorno con le date libere.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Fuori porta Napoli,', 'nell’ex mobilificio.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'viale Otranto 15, Lecce'), ('Carico', 'il furgone entra nel cortile'),
                                  ('Dormire', 'tre stanze sopra lo studio'), ('Aperto', 'tutti i giorni 10 — 2')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Presa Diretta · lo studio', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · nastro finito', color=AMBRA, align='center'),
                       K.headline(t, 'Questa traccia <em>non è stata registrata.</em>', size=80, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro']),
                       K.text(t, 'Riavvolgiamo e ripartiamo dall’inizio.', size=17, align='center', color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna in regia', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CONSOLLE, overlay=66), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Eventi e musica', 'Le bobine che girano',
    ['studio di registrazione', 'musica', 'band', 'mix', 'mastering', 'lecce', 'scuro', 'slider pro'],
    'Uno studio di registrazione, nero di sala e ambra delle lancette: in apertura le bobine del nastro che girano, i '
    'VU-metri con le lancette che ballano e la spia REC; le sale, la consolle da esplorare coi punti, i dischi usciti, '
    'la giornata in studio, le tariffe e la prenotazione.',
    [('Lo studio', STUDIO), ('Le tariffe', TARIFFE), ('Le uscite', HOME + '#uscite'), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('lo-studio', 'Lo studio', 'lo-studio'), ('tariffe', 'Le tariffe', 'tariffe'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Golos Text\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'SUSE\', system-ui, sans-serif',
    '\'Reddit Mono\', ui-monospace, Menlo, monospace', ['SUSE', 'Golos Text', 'Reddit Mono'], peso_titoli='600', raggio=6,
    grana=6, titoli_line='1.02', titoli_spazio='-0.025em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'lo-studio.json': build_studio(), 'tariffe.json': build_tariffe(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
