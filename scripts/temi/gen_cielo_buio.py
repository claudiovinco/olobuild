# -*- coding: utf-8 -*-
"""Genera il tema «Cielo Buio» (id cielo-buio): un'associazione di astrofili sui Monti Sibillini. Scuro come la notte.

Blu-nero, il rosso delle torce da osservazione (non abbaglia l'occhio abituato al buio) e l'oro delle stelle. Titoli in
Syne, testo in Inter Tight, coordinate e orari in Space Mono.

Apertura: Slider Pro a tutto schermo, quattro capitoli della notte (il cielo di Castelluccio, le serate al telescopio,
le stelle cadenti, la Luna) con le lettere del titolo che cadono come stelle, una stella cadente che attraversa la
diapositiva lungo un tracciato e un indice fisso in basso che porta a ogni capitolo. Poi la mappa del cielo da toccare
(Punti sull'immagine), il paesaggio a strati notturno con la luna, il conto alla rovescia alle Geminidi, la raccolta
per il telescopio nuovo, l'orario delle serate, la luce di pagina.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'cielo-buio'
NAME = 'Cielo Buio'

HEX = {
    'primary': '#FF5E52', 'primary_contrast': '#0A0B14', 'secondary': '#6C7BFF', 'secondary_contrast': '#0A0B14',
    'accent': '#F2C97D', 'dark': '#04050B', 'light': '#F1F2F8', 'muted': '#0E1121', 'muted_contrast': '#E7E9F2',
    'text': '#E7E9F2', 'text_muted': '#959CB4', 'background': '#070912', 'border': '#1E2338', 'link': '#FF5E52',
}

HOME, SERATE, CIELO, ASSOC = '{{PAGINA:home}}', '{{PAGINA:serate}}', '{{PAGINA:il-cielo}}', '{{PAGINA:associazione}}'
MAIL, PHONE, PHONE_TEL = 'serate@cielobuio.example', '0737 000 223', 'tel:+390737000223'
LAT, LNG = '42.8285', '13.2075'

V_SCIE, V_ARANCIO, V_COPPIA = 'pexels-video-40085408', 'pexels-video-5884322', 'pexels-video-9121281'
V_SAGOMA, V_LAGO, V_CITTA = 'pexels-video-9121282', 'pexels-video-857136', 'pexels-video-13322952'
PHOTOS = [
    'pexels-27061475.jpg', 'pexels-6141340.jpg', 'pexels-12987747.jpg', 'pexels-20464621.jpg', 'pexels-5428304.jpg',
    'pexels-5753506.jpg', 'pexels-11720316.jpg', 'pexels-32535331.jpg', 'pexels-12290473.jpg', 'pexels-33391538.jpg',
    'pexels-29136126.jpg', 'pexels-27396910.jpg', 'pexels-8557470.jpg', 'pexels-13331099.jpg', 'pexels-8869933.jpg',
    'pexels-9160637.jpg', 'pexels-36489751.jpg', 'pexels-37269532.jpg', 'pexels-27091263.jpg', 'pexels-27091260.jpg',
    'pexels-2901134.jpg', 'pexels-13635174.jpg', 'pexels-18006509.jpg', 'pexels-20033310.jpg', 'pexels-33775549.jpg',
    'pexels-14404262.jpg', 'pexels-35461840.jpg', 'pexels-39080530.jpg', 'pexels-27741843.jpg',
]
VIDEOS = [V_SCIE, V_ARANCIO, V_COPPIA, V_SAGOMA, V_LAGO, V_CITTA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='500', corsivo=False)
C = K.c
VOCI = [('map-pin', 'Pian Grande di Castelluccio, Norcia (PG)', ASSOC + '#dove'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def stelle_cadenti(lid, ritardo=0):
    """Una stella cadente: una scia sottile che corre lungo un tracciato in diagonale, all'infinito."""
    return K.livello(lid, tipo='shape', x=62, y=8, w=14, h=0.4, entra='fadeIn', ritardo=ritardo, durata=400,
                     bgColor='', opacity=90,
                     shapeGradient={'angle': 90, 'from': 'transparent', 'to': C['chiaro']},
                     motionPath={'path': 'M 0 0 L -520 300', 'duration': 2600, 'autoRotate': True, 'loop': True,
                                 'easing': 'ease-in'})


def titolo_stellato(lid, testo, y, size=96, w=60, ritardo=300):
    return K.livello(lid, contenuto=testo, tag='h1' if lid.endswith('1') else 'h2', x=6, y=y, w=w, size=size, peso='500',
                     colore=C['chiaro'], ritardo=ritardo, durata=900, lineHeight=0.98, letterSpacing=-2,
                     charAnim={'stagger': 35, 'offsetY': -60, 'blur': 8, 'opacity': 0},
                     textShadow={'x': 0, 'y': 2, 'blur': 24, 'color': mix(C['nero'], 60)},
                     responsive={'mobile': {'fontSize': K.mob(40), 'width': 88, 'y': 20}})


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Cielo Buio', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Cielo Buio', 'Associazione astrofili dei Monti Sibillini, dal 1998. Serate pubbliche al telescopio, '
                   'corsi, notti per le scuole e la difesa del cielo buio di Castelluccio.',
                   [('Le serate', SERATE), ('Il cielo del mese', CIELO), ('L’associazione', ASSOC),
                    ('Iscriviti', ASSOC + '#iscriviti'), ('Privacy', '/privacy-policy/')],
                   'Stanotte', 'Luna al 12%: buio quasi perfetto. Il telescopio grande è puntato su Saturno dalle 21.30.',
                   VOCI, '© 2026 Associazione Astrofili Cielo Buio · Norcia · Cielo protetto dalla riserva dei Sibillini',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')])


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def apertura(t):
    sl = []
    capitoli = [
        ('Il cielo più buio\ndel Centro Italia.', 'Pian Grande di Castelluccio · 1.452 m · SQM 21,6',
         'Qui la Via Lattea fa ombra. Ogni venerdì di bel tempo apriamo i telescopi a chi vuole guardare.',
         K.sfondo_foto('pexels-27061475.jpg', overlay=0.25, scala=1.12), 'Il cielo'),
        ('Una sera,\ndieci telescopi.', 'Le serate pubbliche · dalle 21.30',
         'Saturno con gli anelli, la nebulosa di Orione, le galassie di Andromeda. Ti spieghiamo cosa stai guardando.',
         K.sfondo_video(V_COPPIA, overlay=0.3), 'Le serate'),
        ('Ottanta stelle\ncadenti all’ora.', 'Geminidi · notte fra il 13 e il 14 dicembre',
         'La pioggia di meteore più ricca dell’anno. Sacchi a pelo, tè caldo e nessuna luce accesa fino all’alba.',
         K.sfondo_foto('pexels-39080530.jpg', overlay=0.25, scala=1.15, verso='out'), 'Le stelle cadenti'),
        ('La Luna,\nda vicino.', 'Corso per principianti · 4 lunedì',
         'I crateri, i mari, la linea del giorno che avanza. Il primo oggetto da imparare, e il più bello.',
         K.sfondo_foto('pexels-13331099.jpg', overlay=0.15, scala=1.08), 'La Luna'),
    ]
    for i, (tit, occhio, testo, sfondo, tab) in enumerate(capitoli):
        n = i + 1
        livelli = [
            K.livello('cb-o%d' % n, contenuto=occhio, x=6, y=22, w=60, size=13, peso='400', colore=C['acc2'],
                      ritardo=150, letterSpacing=3, textTransform='uppercase', fontFamily='Space Mono',
                      sfx={'effect': 'blockRight', 'color': C['acc'], 'duration': 700},
                      responsive={'mobile': {'fontSize': K.mob(11), 'y': 14, 'width': 88}}),
            titolo_stellato('cb-t%d' % n, tit, 27),
            K.livello('cb-s%d' % n, contenuto=testo, x=6, y=62, w=36, size=18, peso='400', colore=mix(C['chiaro'], 88),
                      ritardo=900, lineHeight=1.55, responsive={'mobile': {'fontSize': K.mob(15), 'width': 88, 'y': 36}}),
            K.livello('cb-b%d' % n, tipo='button', contenuto='Prenota la serata' if n != 4 else 'Iscriviti al corso',
                      x=6, y=76, w='auto', size=12, peso='600', colore=C['on_acc'], bgColor=C['acc'], padding=16,
                      letterSpacing=2, textTransform='uppercase', ritardo=1200,
                      buttonUrl=SERATE + '#programma' if n != 4 else ASSOC + '#iscriviti',
                      hover={'scale': 1.04, 'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                      responsive={'mobile': {'y': 52, 'fontSize': K.mob(12)}}),
        ]
        if n in (1, 3):
            livelli.append(stelle_cadenti('cb-m%d' % n, ritardo=1500))
        sl.append(K.diapositiva('cb-%d' % n, sfondo, livelli, durata=7500, tab=tab))
    indice = []
    for i, (_, _, _, _, tab) in enumerate(capitoli):
        indice.append(K.livello('cb-i%d' % i, contenuto='0%d  %s' % (i + 1, tab), x=6 + i * 16, y=91, w=15, size=12,
                                peso='400', colore=mix(C['chiaro'], 75), entra='fadeIn', ritardo=0, letterSpacing=2,
                                textTransform='uppercase', fontFamily='Space Mono', action={'type': 'goToSlide', 'target': i},
                                hover={'color': C['acc'], 'duration': 200}, cursor='pointer',
                                responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=indice, transizione='zoomFade', durata_trans=1100, showDots=False, showArrows=True,
                       parallax=True, parallaxType='mouse', parallaxIntensity=4)


def cartina_del_cielo(t, foto, voci, altezza='620'):
    return t.classic('hotspot', {
        'image': K.img(foto), 'image_alt': 'Il cielo d’estate sopra le montagne, con la Via Lattea',
        'image_height': altezza, 'aspect_ratio': '16/9', 'object_fit': 'cover', 'object_position': 'center center',
        'markers': [{'id': 'cb-h%d' % i, 'pos_x': str(x), 'pos_y': str(y), 'title': ti, 'description': de, 'icon': '',
                     'tooltip_position': pos, 'link': '', 'link_label': ''} for i, (x, y, ti, de, pos) in enumerate(voci)],
        'border_radius': 2, 'marker_style': 'dot', 'marker_color': C['acc2'], 'marker_text_color': C['nero'], 'marker_size': 22,
        'pulse_animation': True, 'tooltip_bg': C['sala'], 'tooltip_color': C['testo'], 'tooltip_width': 280,
    })


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]

    out.append(t.section([
        t.one([K.sechead(t, '01 — Stanotte, verso sud', ['Tocca le stelle', 'che vedi a occhio nudo.'], size=76,
                         tagline='Le cinque cose da riconoscere in una notte d’estate dal Pian Grande. Tocca un punto: ti '
                                 'diciamo cos’è e con cosa guardarlo.')]),
        t.one([cartina_del_cielo(t, 'pexels-32535331.jpg', [
            (22, 30, 'Vega', 'La stella più luminosa dell’estate, nella Lira. A occhio nudo è azzurra: dista 25 anni luce.', 'right'),
            (47, 20, 'Il nucleo della Via Lattea', 'Verso il Sagittario: lì c’è il centro della Galassia, a 26.000 anni luce. '
             'Si vede solo con il cielo buio come questo.', 'bottom'),
            (66, 38, 'Saturno', 'Il puntino giallo che non scintilla. Al telescopio da 300 mm si vedono gli anelli e Titano.', 'left'),
            (35, 55, 'La nebulosa Laguna', 'Una nuvola di gas dove nascono stelle. Col binocolo è una macchia grigia, '
             'in foto è rosa.', 'top'),
            (80, 18, 'Altair', 'Con Vega e Deneb forma il Triangolo estivo: la prima figura da imparare.', 'left'),
        ])]),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 110}))

    numeri = t.classic('statstrip', {
        'items': [{'value': '21,6', 'label': 'mag/arcsec² · qualità del cielo'}, {'value': '2', 'label': 'classe di Bortle su 9'},
                  {'value': '1.452', 'label': 'metri sul mare'}, {'value': '180', 'label': 'notti serene all’anno'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': True,
        'value_font_family': 'heading', 'value_color': C['testo'], 'value_size': 60, 'value_weight': '500',
        'label_color': C['tenue'], 'label_size': 11, 'label_uppercase': True, 'label_weight': '400', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([t.one([numeri])], padding='large', style={'padding_top': 20, 'padding_bottom': 60}))

    paesaggio = t.classic('layerscape', {
        'strati': [
            {'forma': 'cime', 'variante': 4, 'dettaglio': 70, 'immagine': '', 'profondita': 92, 'altezza': 74,
             'colore': mix(C['sec'], 22, C['nero']), 'opacita': 100, 'sfocatura': 0},
            {'forma': 'montagne', 'variante': 7, 'dettaglio': 50, 'immagine': '', 'profondita': 62, 'altezza': 52,
             'colore': mix(C['sec'], 12, C['nero']), 'opacita': 100, 'sfocatura': 0},
            {'forma': 'colline', 'variante': 2, 'dettaglio': 50, 'immagine': '', 'profondita': 30, 'altezza': 34,
             'colore': mix(C['sec'], 6, C['nero']), 'opacita': 100, 'sfocatura': 0},
            {'forma': 'bosco', 'variante': 5, 'dettaglio': 55, 'immagine': '', 'profondita': 0, 'altezza': 22,
             'colore': C['nero'], 'opacita': 100, 'sfocatura': 0},
        ],
        'cielo': 'sfumato', 'cielo_alto': C['nero'], 'cielo_basso': mix(C['sec'], 30, C['nero']),
        'stelle': True, 'stelle_numero': 220, 'stelle_comparsa': 'scorrendo', 'stelle_scintillio': True,
        'stelle_colore': C['chiaro'], 'astro': 'luna', 'astro_x': 78, 'astro_y': 30, 'astro_moto': 'sorge',
        'astro_colore': C['acc2'], 'astro_misura': 120, 'astro_alone': 70, 'astro_x_mobile': 74, 'astro_y_mobile': 26,
        'astro_misura_mobile': 90, 'nebbia': True, 'nebbia_scorre': True, 'nebbia_colore': mix(C['sec'], 25, C['chiaro']),
        'nebbia_opacita': 18, 'occhiello': 'Ore 23:10 · dal rifugio', 'titolo': 'Si spegne il paese,\nsi accende il cielo',
        'titolo_tag': 'h2', 'testo': 'Castelluccio spegne i lampioni alle undici, ogni notte, per regolamento del Parco. '
                                     'Da quel momento la luce la danno solo le stelle e la luna che sorge dietro il Vettore.',
        'pulsante_testo': '', 'pulsante_link': '', 'testo_dietro': 1, 'testo_profondita': 70, 'movimento': True,
        'intensita': 70, 'mouse': True, 'altezza': 820, 'altezza_tablet': 760, 'altezza_mobile': 720,
    })
    out.append(t.section([t.one([paesaggio], anim=None)], width='fullbleed', padding='remove-vertical'))

    conto = t.classic('countdown', {
        'countdown_style': 'custom', 'countdown_type': 'date', 'display_mode': 'block', 'target_date': '2026-12-14T02:00',
        'show_days': True, 'show_hours': True, 'show_minutes': True, 'show_seconds': False,
        'expired_message': 'Le Geminidi sono qui: alza gli occhi.', 'label_days': 'giorni', 'label_hours': 'ore',
        'label_minutes': 'minuti', 'separator': '', 'text_color': C['testo'], 'accent_color': C['acc2'],
        'number_font_size': 88, 'number_font_weight': '500', 'label_font_size': '12', 'label_font_weight': '400',
        'item_min_width': 160, 'item_bg_color': C['sala'], 'item_radius': radius(2),
        'item_padding': {'top': 32, 'right': 22, 'bottom': 22, 'left': 22}, 'tile_padding': {'top': 10, 'right': 0, 'bottom': 10, 'left': 0},
    })
    out.append(t.section([
        t.row([t.col([K.label(t, '02 — Il prossimo appuntamento', color=C['acc']),
                      K.headline(t, 'Alle Geminidi <em>mancano</em>', size=64, tag='h2', pad_bottom=14),
                      K.text(t, 'Notte fra il 13 e il 14 dicembre, picco alle 2. Portiamo i teli termici e i lettini; tu '
                                'porta un amico e niente torce bianche.'),
                      K.button(t, 'Prenota il posto sul prato', SERATE + '#prenota', kind='pieno', pad_top=18)], wm='2-5', ws='1-1'),
               t.col([conto], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))

    def serata(foto, quando, titolo, testo):
        return [K.image(t, foto, titolo, ratio='4/5', tilt=True), K.label(t, quando, color=C['acc'], pad_top=16, pad_bottom=6),
                K.headline(t, titolo, size=26, tag='h3', pad_bottom=6), K.text(t, testo, size=15)]
    out.append(t.section([
        t.one([K.sechead(t, '03 — Cosa facciamo', ['Tre modi', 'di passare la notte.'], size=64)]),
        t.row([t.col(serata('pexels-5428304.jpg', 'Venerdì · gratis', 'Le serate pubbliche',
                            'Dieci telescopi sul prato, un astronomo per ognuno. Dai sei anni in su.'), wm='1-3', ws='1-1'),
               t.col(serata('pexels-27396910.jpg', 'Weekend · da 45 €', 'Fotografare la notte',
                            'Due notti di astrofotografia: dalla Via Lattea al primo pianeta col telescopio.'), wm='1-3', ws='1-1'),
               t.col(serata('pexels-18006509.jpg', 'Su richiesta', 'Le notti per le scuole',
                            'Una classe, un pullman, il planetario gonfiabile se piove.'), wm='1-3', ws='1-1')],
              layout='33-33-33', gap=28, valign='start'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 120}))

    raccolta = t.classic('progress', {
        'bars': 'Telescopio da 500 mm|68\nCupola scorrevole|41\nPannelli solari|90', 'bar_color': C['acc'],
        'bar_bg': mix(C['acc'], 14), 'text_color': C['testo'], 'height': '10', 'show_percentage': True, 'animated': True,
        'layout': 'bar', 'animate_counter': True, 'animation_duration': '1800', 'border_radius': '2',
    })
    out.append(t.section([
        t.row([t.col([K.label(t, '04 — Il telescopio nuovo', color=C['acc']),
                      K.headline(t, 'Un occhio <em>da mezzo metro.</em>', size=64, tag='h2', pad_bottom=14),
                      K.text(t, 'Con 34.000 euro mettiamo un riflettore da 500 mm sotto una cupola scorrevole, aperto a '
                                'tutti. Mancano 10.900 euro: ogni donazione ha il nome sulla targa.'),
                      K.button(t, 'Dona per il telescopio', ASSOC + '#iscriviti', kind='pieno', pad_top=18)], wm='1-2', ws='1-1'),
               t.col([raccolta, K.text(t, 'Aggiornato il 28 ottobre 2026 · 212 donatori', size=13, pad=(12, 0, 0, 0))],
                     wm='1-2', ws='1-1')], layout='50-50', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))

    out.append(K.video_frase(t, V_SCIE, 'Una notte intera in trenta secondi', 'Il cielo gira <em>attorno alla Polare.</em>',
                             'Ogni scia è una stella che si muove per la rotazione della Terra. Alle serate ti insegniamo a '
                             'trovarla in dieci secondi, e non ti perdi più.', overlay=35))

    voce = t.classic('testimonial', {
        'quote': 'Avevo quarant’anni e non avevo mai visto la Via Lattea. <em>Mia figlia di otto mi ha mostrato Saturno.</em>',
        'author_name': 'Marco R.', 'author_role': 'da Perugia, serata pubblica di agosto 2026', 'rating': '5',
        'text_color': C['testo'], 'star_color': C['acc2'], 'author_color': C['tenue'], 'quote_accent_color': C['acc2'],
        'quote_font': 'heading', 'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': C['acc'],
    })
    out.append(t.section([K.centered(t, [voce])], bg=solid(C['sala']), padding='large',
                         style={'padding_top': 130, 'padding_bottom': 130}))
    luce = t.classic('pagelight', {'light_color': C['sec'], 'base_color': '', 'position': 'spread', 'size': 110,
                                   'intensity': 12, 'transition_ms': 1600})
    out.append(t.section([t.one([luce], anim=None)], padding='remove-vertical'))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Cielo Buio',
                page_intro_caption='Spengo le luci · lascia abituare gli occhi', page_intro_bg=HEX['dark'],
                page_intro_color=HEX['accent'], page_intro_duration=1800, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  LE SERATE
# ══════════════════════════════════════════════════════════════════════════════

def build_serate():
    t = Tpl(ID, 'serate')
    out = [K.intro(t, 'Le serate', ['Da aprile', 'a novembre, al buio.'],
                   'Ogni venerdì sereno le osservazioni pubbliche, il sabato i corsi, la domenica le notti lunghe di '
                   'fotografia. Se piove, il planetario gonfiabile nel rifugio.', bg=K.photo_bg('pexels-27091263.jpg', overlay=58),
                   overlay_text=True)]
    corsi = [('osserva', 'Osservazione pubblica', C['acc2'], 'Dieci telescopi sul prato, un astronomo per ognuno. Gratis, '
              'su prenotazione.', 150),
             ('principianti', 'Corso per principianti', C['acc'], 'Il cielo a occhio nudo, il binocolo, il primo telescopio.', 120),
             ('foto', 'Astrofotografia', C['sec'], 'Dalla Via Lattea col cavalletto alla camera al telescopio.', 240),
             ('bambini', 'Notte dei bambini', C['acc2'], 'Dai 6 ai 12 anni: costellazioni, miti e un razzo ad acqua.', 90)]
    lezioni = [(5, '21:30', '00:00', 'osserva', 'Luca Fabbri', 'Prato grande', 'tutti', 120),
               (6, '21:00', '23:00', 'principianti', 'Elena Santini', 'Rifugio', 'base', 20),
               (6, '23:00', '03:00', 'foto', 'Davide Bucci', 'Piano alto', 'medio', 12),
               (7, '20:30', '22:00', 'bambini', 'Sara Tomassini', 'Planetario', 'tutti', 30),
               (5, '00:00', '02:00', 'foto', 'Davide Bucci', 'Piano alto', 'avanzato', 8),
               (3, '21:30', '23:00', 'principianti', 'Elena Santini', 'Online', 'base', 40)]
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'cb-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': ist, 'sala': sala,
                     'livello': liv, 'posti': p, 'nota': ''} for k, (g, a, b, c, ist, sala, liv, p) in enumerate(lezioni)],
        'giorni': 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato,Domenica', 'vista': 'griglia', 'ora_inizio': '20:00',
        'ora_fine': '03:00', 'mostra_filtri': True, 'etichetta_tutti': 'Tutte le serate', 'filtro_livello': True,
        'filtro_istruttore': False, 'testo_vuoto': 'Nessuna serata con questi filtri.', 'evidenzia_oggi': True,
        'mostra_adesso': True, 'dettagli': True, 'testo_prenota': 'Prenota il posto',
        'link_prenota': SERATE + '?serata={corso}&giorno={giorno}#prenota', 'colore_oggi': C['acc'], 'colore_adesso': C['acc2'],
        'ombra': 'none', 'giorno_font_family': 'heading', 'giorno_font_size': 18, 'giorno_font_weight': '500',
        'giorno_uppercase': True,
    })
    out.append(t.section([
        t.one([K.sechead(t, 'Il programma', ['Una settimana', 'di notti.'], size=60,
                         tagline='Tocca una serata per i dettagli e la prenotazione. Le osservazioni si fanno solo col cielo '
                                 'sereno: se è nuvoloso ti scriviamo entro le 18.')]),
        t.one([orario]),
    ], padding='large', html_id='programma', style={'padding_top': 60}))

    out.append(K.pannello(t, 'pexels-33391538.jpg', 'Cosa portare', 'Vestiti da montagna, anche ad agosto.',
                          'A 1.450 metri di notte fa freddo: pile, giacca, cappello. Una torcia rossa (te la prestiamo), '
                          'il tè caldo lo offriamo noi. Niente luci bianche: l’occhio ci mette venti minuti ad abituarsi.',
                          overlay=58, effetto=''))

    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'La serata', 'serata', True, opzioni='Osservazione pubblica\nCorso per principianti\n'
                     'Astrofotografia\nNotte dei bambini\nGeminidi (13-14 dicembre)', param='serata'),
             K.campo('date', 'La data', 'data', True), K.campo('select', 'Quanti siete', 'persone', True,
                                                                opzioni='1\n2\n3 — 4\n5 — 8\nUna classe'),
             K.campo('text', 'Telefono', 'telefono', segnaposto='Per avvisarti se il cielo si copre'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Bambini, accessibilità, un telescopio tuo…')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Un posto', 'sotto le stelle.'],
                                'Le serate pubbliche sono gratis ma a numero chiuso: 120 persone. Ti scriviamo il giorno '
                                'stesso entro le 18 se il cielo si copre.', VOCI, campi, 'Cielo Buio — prenotazione',
                                'Prenota', 'Prenotato. Il giorno stesso, entro le 18, ti confermiamo col meteo.'))
    out.append(K.domande(t, 'Prima', 'di salire.', [
        ('E se è nuvoloso?', 'Ti scriviamo entro le 18 e spostiamo la prenotazione alla serata dopo. Se il cielo si copre '
                             'mentre siamo lì, si va nel planetario gonfiabile del rifugio.'),
        ('Serve saperne qualcosa?', 'No. Le serate pubbliche sono fatte per chi non ha mai guardato in un telescopio.'),
        ('Posso portare il mio telescopio?', 'Sì: c’è un angolo del prato per gli strumenti dei soci e degli ospiti, con la '
                                             'corrente.'),
        ('Si può dormire lì?', 'Il rifugio ha 24 posti letto per le notti di fotografia; per le serate pubbliche ci sono gli '
                               'agriturismi di Castelluccio.'),
    ], 'ser', nota='Scrivici se la tua non c’è: rispondiamo la mattina.'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  IL CIELO DEL MESE
# ══════════════════════════════════════════════════════════════════════════════

def build_cielo():
    t = Tpl(ID, 'il-cielo')
    out = [K.intro(t, 'Il cielo del mese · novembre', ['Andromeda alta,', 'Saturno a sud.'],
                   'Cosa vedere a occhio nudo, col binocolo e al telescopio questo mese, da un cielo buio. Aggiornato '
                   'ogni primo del mese dai soci.', bg=K.photo_bg('pexels-9160637.jpg', overlay=55), overlay_text=True)]
    out.append(t.section([
        t.one([K.sechead(t, 'A occhio nudo', ['Cinque punti', 'da trovare.'], size=56)]),
        t.one([cartina_del_cielo(t, 'pexels-6141340.jpg', [
            (30, 25, 'La galassia di Andromeda', 'Una macchia lattiginosa sopra la W di Cassiopea: è la cosa più lontana che '
             'vedi a occhio nudo, 2,5 milioni di anni luce.', 'right'),
            (58, 18, 'Cassiopea', 'La W del cielo autunnale: puntala e scendi verso sud per trovare Andromeda.', 'bottom'),
            (72, 45, 'Saturno', 'A sud dopo il tramonto, non scintilla. Al telescopio gli anelli quest’anno sono quasi di '
             'taglio.', 'left'),
            (45, 52, 'Le Pleiadi', 'Sette stelle strette, a est verso le 22. Col binocolo diventano cinquanta.', 'top'),
            (15, 60, 'La Via Lattea', 'D’autunno attraversa il cielo da Cassiopea al Cigno, più tenue che d’estate.', 'right'),
        ], altezza='560')]),
    ], padding='large', width='large', style={'padding_top': 60}))

    def oggetto(foto, tipo, nome, testo):
        return [K.image(t, foto, nome, ratio='1/1'), K.label(t, tipo, color=C['acc'], pad_top=14, pad_bottom=6),
                K.headline(t, nome, size=24, tag='h3', pad_bottom=6), K.text(t, testo, size=15)]
    out.append(t.section([
        t.one([K.sechead(t, 'Al telescopio', ['Quello che fotografano', 'i soci.'], size=56,
                         tagline='Foto fatte dal Pian Grande con gli strumenti dell’associazione. A occhio, al telescopio, '
                                 'sono più tenui e grigie: ma sono vere.')]),
        t.row([t.col(oggetto('pexels-9160637.jpg', 'Nebulosa · Cigno', 'America del Nord',
                             'Tre ore di posa con il filtro H-alfa, ottobre 2026.'), wm='1-3', ws='1-1'),
               t.col(oggetto('pexels-36489751.jpg', 'Nebulosa · Orione', 'La Testa di Cavallo',
                             'Solo in foto: all’oculare è un’ombra appena.'), wm='1-3', ws='1-1'),
               t.col(oggetto('pexels-8869933.jpg', 'Luna · eclissi', 'La Luna rossa',
                             'L’eclissi totale del 7 settembre 2025, dal prato grande.'), wm='1-3', ws='1-1')],
              layout='33-33-33', gap=24, valign='start'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))

    out.append(K.video_frase(t, V_LAGO, 'Glossario per la prima sera', 'Il buio <em>si misura.</em>',
                             'Si misura in magnitudini per secondo d’arco quadrato: più alto il numero, più nero il cielo. '
                             'Milano è a 17, Castelluccio a 21,6. La differenza sono diecimila stelle.', overlay=40))
    out.append(K.domande(t, 'Le parole', 'del cielo.', [
        ('Cos’è la scala di Bortle?', 'Va da 1, il cielo più buio della Terra, a 9, il centro di una città. Il Pian Grande è 2.'),
        ('Che differenza c’è fra stella e pianeta?', 'A occhio: la stella scintilla, il pianeta no. Al telescopio il pianeta è '
                                                    'un disco, la stella resta un punto.'),
        ('Quanti ingrandimenti servono?', 'Meno di quanto pensi: per la Luna e Saturno bastano 100. Conta il diametro.'),
        ('Che binocolo compro?', 'Un 10×50: costa poco, si tiene in mano e mostra le lune di Giove e cento galassie.'),
    ], 'cie', bg=False))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  L'ASSOCIAZIONE
# ══════════════════════════════════════════════════════════════════════════════

def build_associazione():
    t = Tpl(ID, 'associazione')
    out = [K.intro(t, 'L’associazione', ['Trecento soci', 'e un cielo da difendere.'],
                   'Siamo nati nel 1998 per salvare il buio del Pian Grande dai nuovi lampioni. Oggi teniamo le serate, '
                   'i corsi e l’osservatorio, e diamo una mano ai Comuni che vogliono spegnere bene.',
                   bg=K.photo_bg('pexels-12290473.jpg', overlay=55), overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-11720316.jpg', 'La cupola dell’osservatorio sotto il cielo stellato', ratio='4/5',
                              drift=True)], wm='2-5', ws='1-1'),
               t.col([K.label(t, 'L’osservatorio', color=C['acc']),
                      K.headline(t, 'Una cupola, <em>tre telescopi.</em>', size=56, tag='h2', pad_bottom=14),
                      K.text(t, 'Un riflettore da 300 mm, un rifrattore per il Sole e la Luna e una camera per le foto del '
                                'cielo profondo. I soci li usano quando vogliono, con la chiave e un corso di due sere.'),
                      K.specs(t, [('Quota', '1.452 m, Pian Grande'), ('Qualità del cielo', 'SQM 21,6 · Bortle 2'),
                                  ('Strumenti', '300 mm, 120 mm, camera raffreddata'), ('Soci', '312 nel 2026')])],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))

    out.append(K.pannello(t, 'pexels-2901134.jpg', 'Il cielo buio', 'Spegnere bene, non spegnere tutto.',
                          'Aiutiamo i Comuni dei Sibillini a cambiare i lampioni: luce verso il basso, calda, che si abbassa '
                          'dopo mezzanotte. Si risparmia la metà e si rivedono le stelle.', overlay=58, effetto=''))

    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'La quota', 'quota', True, opzioni='Socio ordinario · 40 €\nSocio giovane (fino a 25 anni) · 15 €\n'
                     'Famiglia · 60 €\nSostenitore del telescopio · da 100 €'),
             K.campo('select', 'Hai uno strumento?', 'strumento', opzioni='No, solo occhi\nUn binocolo\nUn telescopio\nUna camera'),
             K.campo('textarea', 'Due righe su di te', 'messaggio', larghezza='1-1', segnaposto='Cosa ti piacerebbe imparare?')]
    out.append(K.sezione_modulo(t, 'iscriviti', 'Iscriviti', ['Il cielo', 'è di tutti.'],
                                'Con la tessera hai le serate dei soci, la chiave dell’osservatorio dopo il corso, lo sconto '
                                'sui weekend di fotografia e il bollettino del cielo ogni mese.', VOCI, campi,
                                'Cielo Buio — iscrizione', 'Mi iscrivo', 'Benvenuto. Ti scriviamo con la tessera e la prima serata dei soci.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove', ['Al Pian Grande,', 'sotto il Vettore.'], size=48, layout='stack', size_mobile=34),
                      K.text(t, 'Il rifugio dell’associazione è a due chilometri da Castelluccio, sulla strada per Forca di '
                                'Presta. Si parcheggia prima del cancello, con i fari spenti.')], wm='1-3', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Cielo Buio · rifugio e osservatorio', hex_marker=HEX['primary'], zoom='12')],
                     wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], padding='large', html_id='dove'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · oggetto non in catalogo', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina è <em>oltre l’orizzonte.</em>', size=104, tag='h1', align='center',
                                  pad_bottom=20, spacing=-3),
                       K.text(t, 'Aspetta che la Terra giri, oppure torna all’inizio.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ARANCIO, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Associazioni e cause', 'Tocca le stelle',
    ['astrofili', 'astronomia', 'associazione', 'cielo buio', 'sibillini', 'serate', 'scuro', 'slider pro'],
    'Un’associazione di astrofili ai Sibillini, blu notte con la luce rossa delle torce: l’apertura in quattro capitoli con '
    'le lettere che cadono come stelle e una stella cadente che attraversa il cielo, la mappa del cielo da toccare, il '
    'paesaggio notturno a strati con la luna, il conto alla rovescia alle Geminidi, la raccolta per il telescopio e '
    'l’orario delle serate.',
    [('Le serate', SERATE), ('Il cielo', CIELO), ('L’associazione', ASSOC), ('Prenota', SERATE + '#prenota')],
    [('home', 'Home', None), ('serate', 'Le serate', 'serate'), ('il-cielo', 'Il cielo del mese', 'il-cielo'),
     ('associazione', 'L’associazione', 'associazione')],
    HEX, '\'Inter Tight\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Syne\', \'Helvetica Neue\', Arial, sans-serif', '\'Space Mono\', ui-monospace, Menlo, monospace',
    ['Syne', 'Inter Tight', 'Space Mono'], peso_titoli='500', raggio=2, grana=5, titoli_spazio='-0.02em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'serate.json': build_serate(), 'il-cielo.json': build_cielo(),
                        'associazione.json': build_associazione(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
