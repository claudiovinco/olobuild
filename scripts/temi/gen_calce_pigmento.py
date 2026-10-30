# -*- coding: utf-8 -*-
"""Genera il tema «Calce & Pigmento» (id calce-pigmento): una bottega di intonaci a calce e pigmenti naturali a Vicenza.
Marmorino, tadelakt, calce rasata e coccio pesto stesi a mano; le terre e gli ossidi venduti a peso; i corsi del sabato
per imparare a stenderli. Scuro come un muro di sera, coi colori delle terre.

Carbone, l'ocra gialla, il rosso ossido e la terra verde di Verona. Titoli in DM Serif Display, testo in Instrument
Sans, sigle dei pigmenti in Martian Mono.

Apertura: Slider Pro sulle quattro cose della bottega (i pigmenti, la calce, il tadelakt, la luce radente), coi video
veri; a destra un campione di pigmento rotondo che respira, con la sigla del Colour Index come su un barattolo (PY43,
PR101…); il cambio ruota la scena (rotateSlide). Poi le finiture da guardare con la lente (Prova materiale), i
pigmenti che si mescolano seguendo il mouse (Goo), la tavolozza delle terre (Hover list a campioni), il muro grezzo e
finito da trascinare (Confronto immagini) e la cazzuola disegnata a tratto (SVG animator).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'calce-pigmento'
NAME = 'Calce & Pigmento'

HEX = {
    'primary': '#E3B341', 'primary_contrast': '#101214', 'secondary': '#A8432F', 'secondary_contrast': '#F4F0E8',
    'accent': '#7FA58C', 'dark': '#08090A', 'light': '#F4F0E8', 'muted': '#1A1D20', 'muted_contrast': '#ECE8E1',
    'text': '#ECE8E1', 'text_muted': '#A3A49E', 'background': '#101214', 'border': '#2A2E31', 'link': '#E3B341',
}

HOME, FINITURE, PIGMENTI, BOTTEGA = '{{PAGINA:home}}', '{{PAGINA:le-finiture}}', '{{PAGINA:i-pigmenti}}', '{{PAGINA:la-bottega}}'
MAIL, PHONE, PHONE_TEL = 'bottega@calcepigmento.example', '+39 0444 000 451', 'tel:+390444000451'
LAT, LNG = '45.5475', '11.5448'

V_STESURA, V_SPATOLA, V_BLU, V_CIOTOLE, V_ROSSO = ('pexels-video-5500757', 'pexels-video-6474174', 'pexels-video-7195022',
                                                  'pexels-video-7195027', 'pexels-video-7195102')
PHOTOS = [
    'pexels-39836710.jpg', 'pexels-11025197.jpg', 'pexels-11025126.jpg', 'pexels-1764702.jpg', 'pexels-15060583.jpg',
    'pexels-10059336.jpg', 'pexels-25820432.jpg', 'pexels-33991011.jpg', 'pexels-35658707.jpg', 'pexels-39875019.jpg',
    'pexels-13833561.jpg', 'pexels-5677655.jpg', 'pexels-6963783.jpg', 'pexels-28967212.jpg', 'pexels-33312287.jpg',
    'pexels-19303106.jpg', 'pexels-16387455.jpg', 'pexels-33682091.jpg', 'pexels-36068751.jpg', 'pexels-20737560.jpg',
    'pexels-16436922.jpg', 'pexels-16436970.jpg', 'pexels-11299685.jpg', 'pexels-16436916.jpg', 'pexels-16436967.jpg',
    'pexels-31280796.jpg', 'pexels-33554810.jpg', 'pexels-33440713.jpg', 'pexels-8093426.jpg', 'pexels-33703944.jpg',
    'pexels-36695633.jpg', 'pexels-8381871.jpg', 'pexels-36495702.jpg', 'pexels-6473982.jpg',
]
VIDEOS = [V_STESURA, V_SPATOLA, V_BLU, V_CIOTOLE, V_ROSSO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Contrà Porti 9, centro storico · Vicenza', BOTTEGA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]

CAZZUOLA_SVG = ('<svg viewBox="0 0 260 200" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" '
                'stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">'
                '<path d="M30 150 L170 150 L200 120 L60 120 Z"/><path d="M115 120 L130 82"/>'
                '<path d="M130 82 L150 82"/><path d="M148 82 L196 44 C204 38 214 46 208 54 L168 92"/>'
                '<path d="M40 170 C80 182 150 182 200 168"/><path d="M60 186 C100 194 140 194 180 186"/></svg>')


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Calce & Pigmento', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Calce & Pigmento', 'Bottega di intonaci a calce e pigmenti naturali nel centro di Vicenza dal 1998. '
                   'Stendiamo marmorino, tadelakt e calce rasata in case, chiese e negozi; vendiamo le terre a peso.',
                   [('Le finiture', FINITURE), ('I pigmenti', PIGMENTI), ('La bottega', BOTTEGA), ('I corsi', BOTTEGA + '#corsi'),
                    ('Privacy', '/privacy-policy/')],
                   'La calce', 'Respira, non ammuffisce, invecchia bene. Dopo cent’anni un muro a calce è più bello del '
                   'primo giorno: basta una mano di grassello.', VOCI,
                   '© 2026 Calce & Pigmento snc · Vicenza · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')], marchio_size=110)


def apertura(t):
    m = K.mob
    scene = [
        ('I pigmenti · terre e ossidi', 'Il colore\nviene dalla terra.', 'Ocra di Roussillon, terra verde di Verona, rosso '
         'di Pozzuoli. Li vendiamo a peso e li mescoliamo davanti a te.', K.sfondo_video(V_BLU, overlay=0.5),
         ('PY43', 'Ocra gialla'), C['acc'], mix(C['acc'], 55, C['sec'])),
        ('La calce · grassello stagionato', 'Stesa a mano,\nmano dopo mano.', 'Tre anni di stagionatura in fossa, poi la '
         'spatola: il marmorino si lucida da solo, sotto il ferro.', K.sfondo_video(V_STESURA, overlay=0.55),
         ('PR101', 'Rosso ossido'), C['sec'], mix(C['sec'], 60, C['nero'])),
        ('Il tadelakt · calce e sapone', 'L’acqua\nci scivola sopra.', 'L’intonaco marocchino che non teme la doccia: '
         'lisciato con un sasso di fiume e chiuso col sapone nero.', K.sfondo_foto('pexels-16436967.jpg', overlay=0.5, scala=1.08),
         ('PG23', 'Terra verde'), C['acc2'], mix(C['acc2'], 55, C['nero'])),
        ('La luce · radente, di sera', 'Un muro\nche cambia con l’ora.', 'Alla luce radente la calce si accende di '
         'velature: lo stesso muro al mattino è un altro muro.', K.sfondo_foto('pexels-28967212.jpg', overlay=0.5, scala=1.1),
         ('PBk9', 'Nero d’osso'), C['chiaro'], C['nero']),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo, (sigla, nome), c1, c2) in enumerate(scene):
        n = i + 1
        sl.append(K.diapositiva('cp-%d' % n, sfondo, [
            K.livello('cp-o%d' % n, contenuto=occ, x=6, y=24, w=44, size=12, peso='500', colore=C['acc'], ritardo=200,
                      letterSpacing=2, textTransform='uppercase', fontFamily='Martian Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 12, 'width': 88}}),
            K.livello('cp-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=29, w=58, size=100, peso='400',
                      colore=C['chiaro'], ritardo=350, durata=1100, lineHeight=1.0, entra='fadeIn',
                      charAnim={'stagger': 22, 'offsetY': 26, 'rotation': -8, 'opacity': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 26, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(42), 'y': 16, 'width': 88}}),
            K.livello('cp-s%d' % n, contenuto=testo, x=6, y=64, w=34, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1100, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 31, 'width': 88}}),
            K.livello('cp-b%d' % n, tipo='button', contenuto='Vieni in bottega', x=6, y=79, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1400, buttonUrl=BOTTEGA, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 47, 'fontSize': m(12)}}),
            K.livello('cp-c%d' % n, tipo='shape', x=66, y=26, w=20, h=32, entra='zoomIn', ritardo=600, durata=1300,
                      borderRadius=999, shapeGradient={'angle': 135, 'from': c1, 'to': c2},
                      boxShadow={'x': 0, 'y': 28, 'blur': 60, 'spread': 0, 'color': mix(C['nero'], 70)},
                      animLoop='breathe', animLoopDuration=5200, parallaxDepth=5, hover={'scale': 1.06, 'duration': 400},
                      responsive={'mobile': {'x': 26, 'y': 59, 'width': 48, 'height': 22}}),
            K.livello('cp-k%d' % n, contenuto='%s<br>%s' % (sigla, nome), x=66, y=61, w=20, size=13, peso='500',
                      colore=mix(C['chiaro'], 85), align='center', entra='fadeIn', ritardo=1300, lineHeight=1.6,
                      letterSpacing=2, textTransform='uppercase', fontFamily='Martian Mono',
                      responsive={'mobile': {'x': 6, 'y': 83, 'width': 88, 'fontSize': m(11)}}),
        ], durata=7000))
    return K.proslider(t, sl, transizione='rotateSlide', durata_trans=1200, showArrows=True, showDots=True, parallax=True,
                       parallaxIntensity=4)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    finiture = t.classic('provamateriale', {
        'voci': [{'id': 'cp-f%d' % k, 'nome': n, 'codice': c, 'testo': tx, 'immagine': K.img(f), 'miniatura': '', 'colore': '',
                  'alt': a} for k, (n, c, tx, f, a) in enumerate([
            ('Marmorino', 'Lucido · da 85 €/m²', 'Grassello e polvere di marmo, steso in tre mani e lucidato col ferro: '
             'la finitura dei palazzi veneziani.', 'pexels-39836710.jpg', 'Marmorino rosso con le velature della spatola'),
            ('Calce rasata', 'Opaca · da 45 €/m²', 'Una sola mano sottile, quasi un velo: il muro resta morbido e '
             'respira. Per le camere da letto.', 'pexels-33991011.jpg', 'Calce rasata bianca con la grana in rilievo'),
            ('Coccio pesto', 'Rustica · da 60 €/m²', 'Calce e cocci di mattone macinati: rosa, ruvido, antico. Per i '
             'portici e i muri di casa vecchia.', 'pexels-11025197.jpg', 'Intonaco aranciato con la grana del coccio'),
            ('Stucco antico', 'Velato · da 95 €/m²', 'Velature di terre una sopra l’altra: il colore che cambia con la '
             'luce del giorno.', 'pexels-25820432.jpg', 'Intonaco velato rosso e arancio'),
            ('Intonaco a frattazzo', 'Grezzo · da 35 €/m²', 'La prima mano, quella che regge tutto. Si lascia anche '
             'così, per chi ama il muro vero.', 'pexels-39875019.jpg', 'Intonaco grezzo a frattazzo grigio')])],
        'iniziale': 0, 'lente': True, 'lente_zoom': 2.6, 'lente_misura': 210, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passa sopra il muro per guardarlo con la lente',
        'testo_suggerimento_touch': 'Tocca il muro per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'Finitura', 'accento': C['acc'], 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        'sfondo_pannello': '', 'etichetta_color': C['tenue'], 'nome_color': C['testo'], 'codice_color': C['acc'],
        'testo_color': C['tenue'], 'proporzione': '3/2', 'campione_forma': 'quadretto', 'campione_misura': 58,
        'disposizione': 'lato', 'lato': 'sinistra', 'larghezza_immagine': 60, 'gap': 56, 'allineamento': 'left',
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Le finiture', ['Cinque muri,', 'da guardare da vicino.'], size=64,
                         tagline='Scegli una finitura e passa sopra la foto: con la lente vedi la grana, le velature, il '
                                 'segno della spatola. Come in bottega, col naso sul campione.')]),
        t.one([finiture]),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 120}))
    tinte = t.classic('goo', {
        'scope': 'section', 'mode': 'goo', 'color_1': C['acc'], 'color_2': C['sec'], 'color_3': C['acc2'],
        'color_4': mix(C['acc'], 50, C['sec']), 'color_5': '', 'blob_count': 9, 'blob_size_min': 90, 'blob_size_max': 200,
        'drift_speed': 0.7, 'drift_amplitude': 1, 'goo_strength': 26, 'follow_cursor': True, 'cursor_blob_size': 150,
        'blend_mode': 'normal', 'layer_opacity': 40, 'content': '', 'min_height': 560,
    }, style={})
    out.append(t.section([
        t.one([tinte], anim=None),
        t.row([t.col([K.sechead(t, 'I pigmenti', ['Il colore', 'si mescola a mano.'], size=72, layout='stack', size_mobile=42),
                      K.text(t, 'Muovi il mouse: le terre si cercano e si fondono, come nel secchio quando le stemperiamo '
                                'nel latte di calce. Nessuna tinta è uguale all’altra, ed è giusto così.', size=18,
                             color=C['testo']),
                      K.button(t, 'I pigmenti', PIGMENTI, pad_top=18)], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 160, 'padding_bottom': 160}))
    out.append(K.pannello(t, V_SPATOLA, 'Il cantiere', 'Il ferro fa la luce.', 'L’ultima mano del marmorino si tira col '
                          'ferro, a pressione: la calce si chiude e diventa lucida come una pietra. Nessuna cera, nessuna '
                          'vernice.', 'Le finiture', FINITURE, overlay=50, video=True))
    muro = t.classic('imgcompare', {
        'before_image': K.img('pexels-39875019.jpg'), 'after_image': K.img('pexels-39836710.jpg'),
        'before_label': 'Il muro grezzo', 'after_label': 'Il marmorino finito', 'show_labels': True, 'start_position': 50,
        'orientation': 'horizontal', 'handle_color': C['chiaro'], 'handle_size': 46, 'line_width': 2, 'height': '480',
        'aspect_ratio': '3/2', 'object_fit': 'cover', 'autoplay': True, 'autoplay_delay': '3', 'autoplay_speed': '2',
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Prima e dopo', ['Lo stesso muro,', 'tre settimane dopo.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Trascina la linea: a sinistra l’intonaco grezzo, a destra il marmorino rosso di Pozzuoli '
                                'steso in un salotto di Contrà Riale. Tre mani, venti giorni d’attesa fra l’una e l’altra.'),
                      K.button(t, 'Chiedi un sopralluogo', BOTTEGA + '#scrivici', pad_top=18)], wm='2-5', ws='1-1'),
               t.col([muro], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_CIOTOLE, 'Il sabato', 'Impara a stendere <em>la calce.</em>',
                             'Un sabato al mese, otto persone, un muro di prova a testa. Si va a casa con le mani bianche e '
                             'un barattolo di marmorino.', overlay=50,
                             extra=[K.button(t, 'I corsi', BOTTEGA + '#corsi', kind='pieno', align='center', pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Calce & Pigmento',
                page_intro_caption='Vicenza · dal 1998', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_finiture():
    t = Tpl(ID, 'le-finiture')
    out = [K.intro(t, 'Le finiture', ['Muri che', 'respirano.'],
                   'La calce non fa la muffa, non si scrosta e invecchia meglio di qualunque pittura. Ogni finitura la '
                   'tingiamo coi nostri pigmenti, sul campione, prima di cominciare.',
                   bg=K.photo_bg('pexels-16387455.jpg', overlay=55), overlay_text=True)]
    for k, (aid, foto, nome, dove, testo, dati) in enumerate([
        ('marmorino', 'pexels-13833561.jpg', 'Marmorino', 'Soggiorni, ingressi, scale', 'Il lucido dei palazzi veneziani, '
         'senza cere. Si tinge in pasta e si lucida col ferro all’ultima mano.',
         [('Mani', 'tre'), ('Tempi', 'tre settimane per stanza'), ('Prezzo', 'da 85 €/m²')]),
        ('tadelakt', 'pexels-16436922.jpg', 'Tadelakt', 'Bagni, docce, lavabi', 'La calce marocchina che non teme l’acqua: '
         'senza fughe, senza piastrelle, si lava col sapone.',
         [('Mani', 'due, più il sapone nero'), ('Tempi', 'due settimane'), ('Prezzo', 'da 120 €/m²')]),
        ('rasata', 'pexels-6963783.jpg', 'Calce rasata', 'Camere, studi, case di campagna', 'Un velo sottile che lascia il '
         'muro morbido e opaco. La più economica, la più silenziosa.',
         [('Mani', 'una o due'), ('Tempi', 'una settimana'), ('Prezzo', 'da 45 €/m²')]),
    ]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, 'Una stanza con la finitura ' + nome, ratio='4/5', drift=True)], wm='2-5', ws='1-1'),
                   t.col([K.label(t, dove, color=C['acc']), K.headline(t, nome, size=64, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati), K.button(t, 'Chiedi un campione', BOTTEGA + '#scrivici')],
                         wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 else None, style={'padding_top': 100, 'padding_bottom': 100}))
    cazzuola = t.classic('svganimator', {
        'source_type': 'code', 'svg_code': CAZZUOLA_SVG, 'anim_type': 'draw', 'anim_sequence': 'one-by-one',
        'trigger': 'viewport', 'duration': 2600, 'stroke_color': C['acc'], 'stroke_linecap': 'round', 'show_fill': False,
        'loop': False, 'max_width': '300px', 'alignment': 'center',
    }, style={})
    out.append(t.section([
        K.centered(t, [cazzuola, K.headline(t, 'Ogni muro ha <em>la sua mano.</em>', size=56, tag='h2', align='center',
                                            pad_bottom=14),
                       K.text(t, 'Non esistono due marmorini uguali: lo stesso colore, steso da due mani diverse, viene '
                                 'diverso. Per questo il campione lo facciamo sul tuo muro.', align='center', size=17)]),
    ], padding='large', style={'padding_top': 120, 'padding_bottom': 130}))
    return out


def build_pigmenti():
    t = Tpl(ID, 'i-pigmenti')
    out = [K.intro(t, 'I pigmenti', ['Terre, ossidi,', 'e un po’ di blu.'],
                   'Sessanta pigmenti in barattoli di vetro, a peso, dalle cave e dai produttori che conosciamo. Si '
                   'stemperano nel latte di calce, nell’olio, nell’uovo: ti spieghiamo come.',
                   bg=K.video_bg(V_ROSSO, overlay=55), overlay_text=True)]
    tavolozza = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': '', 'number': '', 'image': '', 'link_url': BOTTEGA + '#scrivici', 'color': c}
                  for n, s, c in [
                      ('Ocra gialla di Roussillon', 'PY43 · 9 €/etto', '#C9962E'),
                      ('Rosso di Pozzuoli', 'PR101 · 11 €/etto', '#9E3B26'),
                      ('Terra verde di Verona', 'PG23 · 14 €/etto', '#6F8A6A'),
                      ('Terra d’ombra bruciata', 'PBr7 · 9 €/etto', '#5A3A26'),
                      ('Blu oltremare', 'PB29 · 16 €/etto', '#2E3F8F'),
                      ('Nero d’osso', 'PBk9 · 8 €/etto', '#1E1C1A'),
                      ('Bianco di San Giovanni', 'calce e marmo · 6 €/etto', '#EDE7DA')]],
        'lead_mode': 'swatch', 'swatch_size': 30, 'swatch_shape': 'circle', 'name_font_family': 'heading',
        'name_color': C['testo'], 'name_hover_color': C['acc'], 'name_size': 36, 'name_uppercase': False,
        'sub_color': C['tenue'], 'sub_hover_color': C['acc'], 'sub_size': 12, 'sub_uppercase': True, 'row_padding_y': 22,
        'hover_indent': 26, 'hover_bg': mix(C['acc'], 6), 'line_color': C['filo'], 'line_hover_color': C['acc'],
        'peek': False, 'mono_font_family': 'Martian Mono',
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La tavolozza', ['Sessanta barattoli,', 'sette preferiti.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Le sigle sono quelle del Colour Index, come su ogni barattolo serio. Il prezzo è a etto: '
                                'con un etto di ocra tingi una parete di venti metri quadri.'),
                      K.image(t, 'pexels-33703944.jpg', 'Pigmenti naturali in ciotole sul banco', ratio='4/3')],
                     wm='2-5', ws='1-1'),
               t.col([tavolozza], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start'),
    ], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-31280796.jpg', 'Terre colorate in ciotole su fondo scuro', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-33440713.jpg', 'Un mucchio di rosso ossido', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-33554810.jpg', 'Ocra gialla in polvere in una ciotola', ratio='4/5')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=16),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_BLU, 'Una regola', 'Prima il campione, <em>poi il muro.</em>',
                             'Il colore bagnato non è il colore asciutto. Facciamo sempre un campione e aspettiamo che '
                             'asciughi: due giorni di pazienza, vent’anni di muro giusto.', overlay=55))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Contrà Porti,', 'dietro il Palladio.'],
                   'Una stanza a volta con i barattoli di pigmento fino al soffitto, le vasche del grassello e il banco dei '
                   'campioni. Aperta dal martedì al sabato, dalle 9 alle 19.', bg=K.photo_bg('pexels-36695633.jpg', overlay=60),
                   overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-36495702.jpg', 'Un’artigiana stende la calce sul muro', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([K.label(t, 'I corsi del sabato', color=C['acc']),
                      K.headline(t, 'Un muro di prova <em>a testa.</em>', size=56, tag='h2', pad_bottom=14),
                      K.text(t, 'Un sabato al mese, dalle 9 alle 17, otto persone. La mattina la teoria della calce e dei '
                                'pigmenti, il pomeriggio la spatola sul tuo pannello di prova, che porti a casa.'),
                      K.specs(t, [('Marmorino', 'primo sabato del mese · 160 €'), ('Tadelakt', 'secondo sabato · 190 €'),
                                  ('Colore e velature', 'terzo sabato · 140 €'), ('Compreso', 'pranzo, attrezzi, materiali')])],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', html_id='corsi', style={'padding_top': 110, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Cosa ti serve', 'cosa', True,
                     opzioni='Un sopralluogo\nUn campione di finitura\nI pigmenti\nUn corso del sabato'),
             K.campo('text', 'Quanti metri, se lo sai', 'metri'), K.campo('text', 'Dove', 'dove', segnaposto='Città, tipo di stanza'),
             K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1', segnaposto='La stanza, la luce, un colore che hai in mente…')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scrivici', ['Ti facciamo', 'il campione.'],
                                'Il sopralluogo a Vicenza e provincia è gratis. Fuori provincia lo scaliamo dal lavoro.',
                                VOCI, campi, 'Calce & Pigmento — richiesta', 'Manda la richiesta',
                                'Ricevuto. Ti chiamiamo entro due giorni per fissare il sopralluogo.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Contrà Porti,', 'Vicenza.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'contrà Porti 9, Vicenza'), ('Orari', 'martedì — sabato, 9 — 19'),
                                  ('Dalla stazione', 'quindici minuti a piedi'), ('Parcheggio', 'Verdi, poi cinque minuti')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Calce & Pigmento · bottega', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · muro da rifare', color=C['acc'], align='center'),
                       K.headline(t, 'Qui manca <em>l’ultima mano.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Torna all’inizio: la spatola è già pronta.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_STESURA, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Casa e design', 'Terre e calce',
    ['intonaci', 'calce', 'marmorino', 'pigmenti', 'tadelakt', 'artigianato', 'scuro', 'slider pro'],
    'Una bottega di intonaci a calce e pigmenti naturali a Vicenza, carbone e terre: l’apertura coi campioni di pigmento '
    'che respirano e le sigle del Colour Index, le finiture da guardare con la lente, i pigmenti che si mescolano '
    'seguendo il mouse, la tavolozza delle terre, il muro grezzo e finito da trascinare e la cazzuola disegnata a tratto.',
    [('Le finiture', FINITURE), ('I pigmenti', PIGMENTI), ('I corsi', BOTTEGA + '#corsi'), ('La bottega', BOTTEGA)],
    [('home', 'Home', None), ('le-finiture', 'Le finiture', 'le-finiture'), ('i-pigmenti', 'I pigmenti', 'i-pigmenti'),
     ('la-bottega', 'La bottega', 'la-bottega')],
    HEX, '\'Instrument Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'DM Serif Display\', \'Georgia\', serif', '\'Martian Mono\', ui-monospace, Menlo, monospace',
    ['DM Serif Display', 'Instrument Sans', 'Martian Mono'], corsivo_di=['DM Serif Display'], peso_titoli='400', raggio=2,
    grana=6, titoli_line='1.02', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-finiture.json': build_finiture(), 'i-pigmenti.json': build_pigmenti(),
                        'la-bottega.json': build_bottega(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
