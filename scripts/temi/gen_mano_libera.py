# -*- coding: utf-8 -*-
"""Genera il tema «Mano Libera» (id mano-libera): una barberia a Bari vecchia dal 1962, taglio, barba, rasatura col panno
caldo e il rasoio a mano libera; ora la portano avanti il nipote e due giovani barbieri.

Tema scuro: verde bottiglia quasi nero, ottone, rosso sangue di bue e crema. Titoli in DM Serif Text (anche corsivo),
testo in Albert Sans, dati in IBM Plex Mono.

Apertura: Slider Pro «la poltrona», alto 16:9. A destra la foto del lavoro in una cornice d'ottone che si inclina verso
il puntatore; davanti, un paio di forbici disegnate con le forme dello slider che apre e chiude le lame a tempo
(timeline con il perno in comune), e il bollino «dal 1962» che gira piano. Una diapositiva per servizio, a linguette.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'mano-libera'
NAME = 'Mano Libera'

HEX = {
    'primary': '#C99A4A', 'primary_contrast': '#0E1310', 'secondary': '#A3352E', 'secondary_contrast': '#FFFFFF',
    'accent': '#E9DFC9', 'dark': '#070A08', 'light': '#F3ECDD', 'muted': '#151C18', 'muted_contrast': '#ECE4D3',
    'text': '#ECE4D3', 'text_muted': '#A3A694', 'background': '#0E1310', 'border': '#26302A', 'link': '#C99A4A',
}

HOME, SERVIZI, BOTTEGA, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:la-bottega}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'poltrona@manolibera.example', '+39 080 000 1962', 'tel:+390800001962'
LAT, LNG = '41.1286', '16.8697'

V_TAGLIO, V_NUCA, V_COLLO, V_RASOIO, V_SFUMATURA = ('pexels-video-4177954', 'pexels-video-4177956', 'pexels-video-5450156',
                                                   'pexels-video-5450159', 'pexels-video-7686548')
PHOTOS = [
    'pexels-37764947.jpg', 'pexels-13058791.jpg', 'pexels-39553899.jpg', 'pexels-7195803.jpg', 'pexels-3998403.jpg',
    'pexels-39553892.jpg', 'pexels-7697490.jpg', 'pexels-9315046.jpg', 'pexels-7697711.jpg', 'pexels-5568407.jpg',
    'pexels-1319459.jpg', 'pexels-39559306.jpg', 'pexels-12464838.jpg', 'pexels-15659494.jpg', 'pexels-1805600.jpg',
    'pexels-12464841.jpg', 'pexels-34702982.jpg', 'pexels-3998414.jpg', 'pexels-897263.jpg', 'pexels-3998421.jpg',
    'pexels-7697478.jpg', 'pexels-1319461.jpg', 'pexels-13138476.jpg', 'pexels-12339159.jpg', 'pexels-20405689.jpg',
    'pexels-7697200.jpg', 'pexels-9511916.jpg', 'pexels-19572632.jpg', 'pexels-8490107.jpg', 'pexels-3998402.jpg',
    'pexels-8468132.jpg', 'pexels-9992819.jpg', 'pexels-3998429.jpg', 'pexels-33448218.jpg', 'pexels-6599037.jpg',
    'pexels-35069299.jpg', 'pexels-7832476.jpg', 'pexels-39559325.jpg', 'pexels-4690288.jpg', 'pexels-7697712.jpg',
    'pexels-13058740.jpg', 'pexels-3992874.jpg',
]
VIDEOS = [V_TAGLIO, V_NUCA, V_COLLO, V_RASOIO, V_SFUMATURA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
OTTONE, SANGUE = C['acc'], C['sec']
TITOLI, MONO = 'DM Serif Text', 'IBM Plex Mono'
VOCI = [('map-pin', 'Strada Palazzo dell’Intendenza 7 · Bari', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Mano Libera', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Mano Libera', 'Barberia a Bari vecchia dal 1962: taglio, barba, rasatura col panno caldo e il rasoio a '
                   'mano libera. Tre poltrone, un caffè, nessuna fretta.',
                   [('I servizi', SERVIZI), ('La bottega', BOTTEGA), ('Prenota', PRENOTA), ('Come arrivare', PRENOTA + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Martedì — sabato 9 — 20<br>Giovedì fino alle 22', VOCI,
                   '© 2026 Barberia Mano Libera di Greco · Bari · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')], marchio_size=130)


SERVIZI_SLIDE = [  # linguetta, occhiello, riga 1, riga 2, testo, foto, prezzo
    ('Taglio', '01 · il taglio', 'Forbici e pettine,', 'come una volta.', 'Il taglio classico si fa a forbice, la sfumatura '
     'con la macchinetta e il rasoio sulla nuca. Si comincia guardandovi allo specchio, e parlando.', 'pexels-39559306.jpg',
     '22 €'),
    ('Barba', '02 · la barba', 'La barba', 'si disegna.', 'Si spunta a forbice, si rifinisce col rasoio, si nutre con l’olio '
     'di mandorla. Vi spieghiamo come tenerla a casa fra una volta e l’altra.', 'pexels-3998421.jpg', '18 €'),
    ('Rasatura', '03 · la rasatura', 'Panno caldo,', 'rasoio a mano.', 'Due panni bollenti, il sapone montato col '
     'pennello, due passate di rasoio, il panno freddo e l’allume. Venti minuti di silenzio.', 'pexels-9315046.jpg', '25 €'),
    ('Rituale', '04 · il rituale', 'Taglio, barba', 'e un caffè.', 'Il servizio completo del sabato mattina: un’ora sulla '
     'poltrona, il caffè della moka e il giornale. Si prenota, perché va via in fretta.', 'pexels-20405689.jpg', '45 €'),
]


def forbici(px, py):
    """Le forbici: due lame e due anelli col perno comune in (px, py), le lame che si aprono e si chiudono."""
    lame = []
    kfs = [(0, 2), (260, 16), (520, 2), (780, 16), (1040, 2), (2400, 2)]
    for k, verso in enumerate([-1, 1]):
        bx, by, bw, bh = px - 0.45, py - 26, 0.9, 32
        lame.append(K.livello('ct-lama%d' % k, tipo='shape', x=bx, y=by, w=bw, h=bh, borderRadius=999,
                              shapeGradient={'angle': 90, 'from': mix(C['chiaro'], 90), 'to': mix(C['chiaro'], 45)},
                              boxShadow={'x': 0, 'y': 6, 'blur': 14, 'spread': -4, 'color': mix(C['nero'], 80)},
                              timeline=K.tl(bx, by, [(ms, {'rotation': verso * a, 'originX': 50, 'originY': 81.25})
                                                     for ms, a in kfs], 2400, curva='power2.inOut'),
                              responsive={'mobile': {'visible': False}}))
        rw, rh = 3.2, 5.7
        rx, ry = px - rw / 2 - verso * 2.4, py + 5
        lame.append(K.livello('ct-anello%d' % k, tipo='shape', x=round(rx, 2), y=ry, w=rw, h=rh, bgColor='transparent',
                              borderRadius=999, borderWidth=4, borderColor=OTTONE,
                              timeline=K.tl(round(rx, 2), ry, [(ms, {'rotation': -verso * a,
                                                                     'originX': round((px - rx) / rw * 100, 1),
                                                                     'originY': round((py - ry) / rh * 100, 1)})
                                                               for ms, a in kfs], 2400, curva='power2.inOut'),
                              responsive={'mobile': {'visible': False}}))
    lame.append(K.livello('ct-perno', tipo='shape', x=px - 0.35, y=py - 0.62, w=0.7, h=1.24, bgColor=OTTONE, borderRadius=999,
                          responsive={'mobile': {'visible': False}}))
    return lame


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, foto, prezzo) in enumerate(SERVIZI_SLIDE):
        n = i + 1
        sl.append(K.diapositiva('ct-%d' % n, K.sfondo_video([V_TAGLIO, V_SFUMATURA, V_RASOIO, V_NUCA][i], overlay=0.22,
                                                            colore=C['fondo']), [
            # lo schermo diviso: il video del barbiere a sinistra, il testo su fondo scuro a destra
            K.livello('ct-pan%d' % n, tipo='shape', x=50, y=0, w=50, h=100, bgColor=mix(C['fondo'], 93), entra='fadeIn',
                      ritardo=0, durata=500, esce='fadeOut',
                      responsive={'mobile': {'x': 0, 'y': 38, 'width': 100, 'height': 62}}),
            K.livello('ct-o%d' % n, contenuto=occ, x=56, y=18, w=38, size=12, peso='500', colore=OTTONE, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 42, 'x': 7, 'width': 86}}),
            K.livello('ct-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=56, y=22, w=40, size=50, peso='400',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromLeft', ritardo=350, durata=1100,
                      responsive={'mobile': {'fontSize': m(38), 'y': 44.5, 'x': 7, 'width': 88}}),
            K.livello('ct-i%d' % n, contenuto=r2, tag='p', x=56, y=29, w=40, size=50, peso='400', fontStyle='italic',
                      colore=OTTONE, fontFamily=TITOLI, lineHeight=1.04, entra='maskFromLeft', ritardo=600, durata=1100,
                      responsive={'mobile': {'fontSize': m(38), 'y': 48.8, 'x': 7, 'width': 88}}),
            K.livello('ct-s%d' % n, contenuto=testo, x=56, y=41, w=34, size=17, peso='400', colore=mix(C['chiaro'], 80),
                      lineHeight=1.65, ritardo=950,
                      responsive={'mobile': {'fontSize': m(14), 'y': 55, 'x': 7, 'width': 86}, 'tablet': {'width': 38}}),
            K.livello('ct-b%d' % n, tipo='button', contenuto='Prenota la poltrona', x=56, y=66, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=OTTONE, padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=4, ritardo=1250, buttonUrl=PRENOTA, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 70.5, 'x': 7, 'fontSize': m(11)}}),
            K.livello('ct-p%d' % n, contenuto=prezzo, x=73.5, y=67, w='auto', size=13, peso='500', colore=C['chiaro'],
                      fontFamily=MONO, padding=12, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 40),
                      entra='popUpBack', ritardo=1500,
                      responsive={'mobile': {'y': 71.1, 'x': 60, 'fontSize': m(12)}, 'tablet': {'x': 78}}),
        ], durata=7500, tab=tab))
    # le forbici sulla linea che divide lo schermo, e il bollino della bottega sul video
    globali = forbici(50.0, 62.0)
    globali.append(K.livello('ct-dal', contenuto='dal<br>1962', x=38.5, y=9, w=8.33, h=14.8, size=22, peso='400',
                             colore=C['nero'], bgColor=OTTONE, borderRadius=999, align='center', fontFamily=TITOLI,
                             fontStyle='italic', lineHeight=1.0, padding=26,
                             boxShadow={'x': 0, 'y': 14, 'blur': 30, 'spread': -8, 'color': mix(C['nero'], 80)},
                             timeline=K.tl(38.5, 9, [(0, {'rotation': -10}), (6000, {'rotation': 10}), (12000, {'rotation': -10})],
                                           12000, curva='sine.inOut'),
                             responsive={'mobile': {'x': 74, 'y': 32, 'width': 20, 'height': 9, 'fontSize': m(15)}, 'tablet': {'fontSize': 17},
                                         'notebook': {'fontSize': 20}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='cubeH',
                       durata_trans=1100, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=7500, progressBarColor=OTTONE, navColor=C['chiaro'])


def listino(t):
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in [
                      ('Taglio classico', 'Forbice e pettine, lavaggio e piega', '22 €', 'pexels-39559306.jpg', ''),
                      ('Sfumatura', 'Macchinetta, forbice e rasoio sulla nuca', '25 €', 'pexels-34702982.jpg', 'La più chiesta'),
                      ('Barba', 'Spuntata, disegnata, l’olio di mandorla', '18 €', 'pexels-3998421.jpg', ''),
                      ('Rasatura tradizionale', 'Panno caldo, rasoio a mano libera, allume', '25 €', 'pexels-9315046.jpg', ''),
                      ('Il rituale', 'Taglio, barba, rasatura del collo e il caffè', '45 €', 'pexels-20405689.jpg', 'Sabato'),
                      ('Taglio bambini', 'Fino a 12 anni, con la seduta alta', '15 €', 'pexels-12464841.jpg', '')]],
        'separator_style': 'dotted', 'separator_color': mix(OTTONE, 40), 'title_color': C['testo'], 'price_color': OTTONE,
        'description_color': C['tenue'], 'image_size': 76, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': SANGUE, 'badge_color': C['chiaro'], 'badge_border_radius': '4',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Mano Libera · Bari vecchia', ['Tre poltrone,', 'sessant’anni di rasoio.'], size=60,
                                layout='stack', size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Nonno Salvatore ha aperto nel 1962 con una poltrona sola, comprata a rate. Le poltrone di '
                                'ghisa e pelle sono ancora quelle; il rasoio l’ha passato al nipote Luca, che lavora con '
                                'Giada e Karim. Si viene per un taglio, si resta per il caffè.', size=17, color=C['testo']),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('1962', 'La prima poltrona'), ('3', 'Poltrone di ghisa'), ('20′', 'Per la rasatura'),
                            ('0', 'Fretta')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['Sei servizi,', 'un’ora di calma.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Si prenota online o al telefono; chi passa senza prenotare si siede e aspetta col giornale. '
                                'Paghiamo anche col telefono.'),
                      K.image(t, 'pexels-13058791.jpg', 'Gli strumenti della barberia sul bancone', ratio='4/3')],
                     wm='2-5', ws='1-1'),
               t.col([listino(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='listino', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_COLLO, 'Il rasoio', 'Mano libera, <em>mano ferma.</em>',
                             'Il rasoio si affila sulla striscia di cuoio prima di ogni cliente. Si tende la pelle con due '
                             'dita e si va contropelo solo alla seconda passata.', overlay=55))
    out.append(t.section([
        t.one([K.sechead(t, 'I tagli', ['Sei tagli', 'da girare.'], size=60,
                         tagline='Trascinate o lasciate scorrere: portate la foto, o ditecelo con le parole. Lo troviamo insieme.')]),
        t.one([K.coverflow(t, [
            ('pexels-34702982.jpg', 'Una sfumatura alta fatta con la macchinetta', 'Sfumatura alta', 'Macchinetta e rasoio'),
            ('pexels-39559325.jpg', 'Il profilo di un taglio corto e pulito', 'Corto classico', 'A forbice'),
            ('pexels-4690288.jpg', 'Un ciuffo pettinato all’indietro', 'Il ciuffo', 'Con la pomata opaca'),
            ('pexels-35069299.jpg', 'Baffi folti e capelli corti', 'Baffi a manubrio', 'Con la cera'),
            ('pexels-7832476.jpg', 'Un taglio medio con la barba corta', 'Medio e barba', 'Il più chiesto'),
            ('pexels-3998414.jpg', 'Una barba piena spuntata a forbice', 'Barba piena', 'L’olio di mandorla')],
            altezza=430, larghezza=320)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Ci venivo da bambino con mio padre, adesso ci porto mio figlio. La poltrona è la stessa, il caffè anche. Il '
                 'taglio, devo dire, è migliorato.', 'author_name': 'Nicola D.', 'author_role': 'Cliente dal 1988',
        'avatar': '', 'rating': '5', 'text_color': C['testo'], 'star_color': OTTONE, 'author_color': C['tenue'],
        'quote_accent_color': OTTONE, 'quote_font': 'heading', 'quote_size': 40, 'author_uppercase': True,
        'quote_uppercase': False, 'layout': 'editorial', 'show_line': True, 'line_color': SANGUE}, style={})])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_TAGLIO, 'Il giovedì', 'Aperti <em>fino alle dieci.</em>',
                          'Il giovedì sera la bottega resta aperta per chi esce tardi dal lavoro: birra fresca al posto del '
                          'caffè e i dischi di nonno Salvatore sul giradischi.', 'Prenota', PRENOTA, overlay=58, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Mano Libera',
                page_intro_caption='Barberia · dal 1962', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1500, page_intro_once=True)


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Ogni taglio', 'comincia allo specchio.'],
                   'Prima di prendere le forbici guardiamo il viso, il verso dei capelli, come li pettinate la mattina. '
                   'Poi decidiamo insieme.', bg=K.photo_bg('pexels-13138476.jpg', overlay=64), overlay_text=True)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Lo specchio', 'description': 'Due minuti a guardare: il viso, la nuca, i '
                   'vortici. E le vostre abitudini.'},
                  {'number': '02', 'title': 'Il lavaggio', 'description': 'Acqua tiepida e uno shampoo leggero, col massaggio '
                   'alla testa. Si chiudono gli occhi.'},
                  {'number': '03', 'title': 'Il taglio', 'description': 'Forbici, pettine, macchinetta solo dove serve. Il '
                   'rasoio sulla nuca e sulle basette.'},
                  {'number': '04', 'title': 'La piega', 'description': 'Vi mostriamo come rifarla a casa, con quale prodotto '
                   'e quanto poco ne serve.'}],
        'columns': 4, 'gap': 18, 'number_style': 'plain', 'number_color': OTTONE, 'number_size': 40, 'number_font': 'heading',
        'number_weight': '400', 'title_color': C['testo'], 'title_size': 24, 'title_weight': '400', 'title_font': 'heading',
        'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left', 'card_bg': C['sala'], 'card_border': '',
        'card_radius': radius(4), 'card_padding': 28, 'columns_tablet': 2, 'columns_mobile': 1}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Il taglio', ['Quattro tempi,', 'mezz’ora.'], size=56)]), t.one([passi])],
                         padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    rasatura = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': OTTONE, 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': OTTONE, 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': True, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('I', 'Il caldo', 'pexels-20405689.jpg', 'Il panno', 'Due panni', 'bollenti', 'Aprono i pori e ammorbidiscono la '
             'barba: tre minuti sotto il panno, a occhi chiusi.', '3′', 'sotto il panno'),
            ('II', 'Il sapone', 'pexels-9511916.jpg', 'Il pennello', 'La schiuma', 'montata', 'Sapone alla glicerina montato '
             'nella ciotola col pennello di tasso, steso in cerchi.', '1', 'pennello di tasso'),
            ('III', 'Il rasoio', 'pexels-5568407.jpg', 'La lama', 'Due passate', 'e l’allume', 'Prima a favore di pelo, poi '
             'contropelo. Il panno freddo, l’allume e il balsamo.', '2', 'passate')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': OTTONE,
        'counter_font_family': 'heading', 'counter_size': 84, 'counter_color': OTTONE, 'counter_italic': True,
        'counter_weight': '400', 'tag_color': C['chiaro'], 'pre_title_color': mix(SANGUE, 45, C['chiaro']), 'title_font_family': 'heading',
        'title_size': 32, 'title_weight': '400', 'title_color': C['chiaro'], 'title_accent_color': OTTONE,
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'clock', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(4)}, style={})
    out.append(t.section([t.one([K.sechead(t, 'La rasatura', ['Tre tempi,', 'venti minuti.'], size=56)]), t.one([rasatura])],
                         bg=solid(C['sala']), padding='large', width='large', html_id='rasatura',
                         style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SFUMATURA, 'La sfumatura', 'Dal corto <em>al niente.</em>',
                             'La sfumatura si fa a passaggi, cambiando il pettine della macchinetta ogni centimetro. La riga '
                             'non si deve vedere da nessuna parte.', overlay=55))
    prodotti = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': False, 'badge': ''}
                  for a, b, c, f in [('Olio da barba', 'Mandorla e cedro, 30 ml', '19 €', 'pexels-8490107.jpg'),
                                     ('Pomata opaca', 'Tenuta media, si lava via con l’acqua', '17 €', 'pexels-19572632.jpg'),
                                     ('Sapone da barba', 'Alla glicerina, nella ciotola di legno', '22 €', 'pexels-9511916.jpg'),
                                     ('Il pennello di tasso', 'Fatto a mano, dura vent’anni', '48 €', 'pexels-7697200.jpg')]],
        'separator_style': 'dotted', 'separator_color': mix(OTTONE, 40), 'title_color': C['testo'], 'price_color': OTTONE,
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': SANGUE, 'badge_color': C['chiaro'], 'badge_border_radius': '4',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Da portare a casa', ['Quattro cose,', 'non di più.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Usiamo solo quello che vendiamo, e vendiamo solo quello che usiamo. Ve lo facciamo provare '
                                'alla fine del taglio.')], wm='2-5', ws='1-1'),
               t.col([prodotti], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Le stesse poltrone,', 'tre generazioni.'],
                   'Il pavimento a scacchi, lo specchio con la cornice di noce, le poltrone di ghisa del 1962. Abbiamo '
                   'cambiato solo le luci e la macchina del caffè.', bg=K.photo_bg('pexels-12339159.jpg', overlay=64),
                   overlay_text=True)]
    storia = t.classic('timeline', {
        'items': [{'date': d, 'title': ti, 'tag': tag, 'description': de, 'image': K.img(f), 'video': '', 'icon': ic,
                   'category': 'primary'} for d, ti, tag, de, f, ic in [
            ('1962', 'La prima poltrona', 'L’apertura', 'Salvatore Greco, ventidue anni, apre con una poltrona comprata a '
             'rate e un rasoio del padre.', 'pexels-39553892.jpg', 'scissors'),
            ('1975', 'Tre poltrone', 'La bottega cresce', 'Arrivano le due poltrone di ghisa da Napoli, e il primo garzone.',
             'pexels-37764947.jpg', 'armchair'),
            ('1998', 'Il giradischi', 'La musica', 'Salvatore porta i suoi dischi: da allora il sabato si taglia col jazz.',
             'pexels-13058791.jpg', 'disc'),
            ('2019', 'Luca', 'Il nipote', 'Luca torna da Londra dopo otto anni nelle barberie di Soho e prende il rasoio.',
             'pexels-8468132.jpg', 'user'),
            ('2026', 'Il giovedì sera', 'Oggi', 'Tre barbieri, la sera lunga del giovedì, e Salvatore che passa ogni mattina.',
             'pexels-7195803.jpg', 'moon')]],
        'tl_layout': 'alt', 'tl_reveal': 'bloom', 'tl_theme': 'night', 'tl_card': 'polaroid', 'tl_thread': 'solid',
        'tl_node': 'year', 'tl_color': 'cat', 'tl_media': 'on', 'tl_density': 'comfy', 'tl_transparent': True,
        'tl_title_size': 24, 'tl_title_weight': '400', 'tl_title_family': 'heading', 'tl_show_tag': True}, style={})
    out.append(t.section([t.one([K.sechead(t, 'La storia', ['Sessant’anni', 'in cinque date.'], size=56)]), t.one([storia])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-39553892.jpg', 'Nonno Salvatore che legge il giornale in bottega', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Salvatore Greco · 1962', color=OTTONE),
                      t.classic('quotation', {
                          'content': 'Il barbiere non taglia i capelli: ascolta. I capelli li taglia mentre ascolta.',
                          'author': 'Salvatore Greco, ogni mattina alle nove', 'style': 'default', 'alignment': 'left',
                          'content_font_family': 'heading', 'content_font_size': '44', 'content_font_weight': '400',
                          'content_font_style': 'italic', 'content_color': C['testo'], 'author_color': C['tenue'],
                          'author_font_size': '15', 'text_effect': 'none'}, style={})],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I barbieri', ['Tre poltrone,', 'tre mani.'], size=56)]),
        K.squadra(t, [
            ('pexels-8468132.jpg', 'Luca Greco', 'Barbiere, il nipote', 'Otto anni a Londra, poi a casa. Fa le sfumature più '
             'pulite di Bari e la rasatura del nonno.', '50% 25%'),
            ('pexels-33448218.jpg', 'Giada Rizzi', 'Barbiera', 'Tagli lunghi e ricci, la barba disegnata. Ha il giradischi '
             'nella poltrona di destra.', '50% 25%'),
            ('pexels-9992819.jpg', 'Karim Bensaid', 'Barbiere', 'Viene da Casablanca, dove ha imparato il filo per le '
             'sopracciglia. Il più veloce, mai di fretta.', '50% 40%')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_NUCA, 'Il dettaglio', 'La nuca <em>si guarda per ultima.</em>',
                          'Alla fine vi giriamo lo specchio piccolo dietro la testa: è lì che si vede un buon barbiere.',
                          'Prenota', PRENOTA, overlay=58, video=True))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['La poltrona', 'vi aspetta.'],
                   'Prenotate qui o al telefono. Chi passa senza prenotare trova sempre una sedia e il giornale.',
                   bg=K.photo_bg('pexels-7195803.jpg', overlay=64), overlay_text=True, size=96)]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Martedì — sabato', 'time': '9 — 20', 'note': 'Orario continuato'},
                  {'day': 'Giovedì', 'time': '9 — 22', 'note': 'La sera lunga, col giradischi'},
                  {'day': 'Domenica e lunedì', 'time': 'Chiuso', 'note': 'Il rasoio riposa'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': OTTONE, 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 46,
        'time_weight': '400', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    out.append(t.section([t.one([orari])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 60}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('select', 'Il servizio', 'servizio', True,
                     opzioni='Taglio classico · 22 €\nSfumatura · 25 €\nBarba · 18 €\nRasatura tradizionale · 25 €\n'
                             'Il rituale · 45 €\nTaglio bambini · 15 €'),
             K.campo('select', 'Con chi', 'barbiere', opzioni='Il primo libero\nLuca\nGiada\nKarim'),
             K.campo('date', 'Giorno', 'giorno', True), K.campo('select', 'Ora', 'ora', True,
                                                               opzioni='9:00\n10:00\n11:00\n12:00\n15:00\n16:00\n17:00\n18:00\n19:00'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Una foto del taglio che vorreste, un ritocco, una domanda.')]
    out.append(K.sezione_modulo(t, 'prenota', 'La prenotazione', ['Scegliete', 'giorno e poltrona.'],
                                'Vi confermiamo con un messaggio. Se non potete venire, avvisateci: qualcuno sta aspettando.',
                                VOCI, campi, 'Mano Libera — prenotazione', 'Prenota', 'Ricevuto! Vi confermiamo con un messaggio.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['A Bari vecchia,', 'dietro il castello.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'strada Palazzo dell’Intendenza 7'), ('A piedi', 'dal castello, due minuti'),
                                  ('Parcheggio', 'lungomare Imperatore Augusto'), ('Pagamenti', 'contanti, carte e telefono')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Mano Libera · Bari vecchia', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di sedervi.', [
        ('Tagliate anche i capelli lunghi?', 'Sì, Giada soprattutto: ricci, lunghi, a strati, anche per le donne.'),
        ('Fate i bambini?', 'Sì, fino a dodici anni a 15 €, con la seduta alta e la pazienza che serve.'),
        ('Usate rasoi monouso?', 'Il rasoio è a mano libera ma la lama si cambia a ogni cliente; tutto il resto si sterilizza.'),
        ('Si può fare un regalo?', 'Sì, il buono del rituale: arriva in una busta di carta col sigillo di ceralacca.'),
    ], 'pr', bg=True))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · poltrona vuota', color=OTTONE, align='center'),
                       K.headline(t, 'Questa pagina <em>è andata contropelo.</em>', size=82, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=OTTONE),
                       K.text(t, 'Un ritocco e torniamo all’inizio.', size=17, align='center', color=mix(C['chiaro'], 80)),
                       K.button(t, 'Torna in bottega', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_RASOIO, overlay=66), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Le forbici che tagliano',
    ['barbiere', 'barberia', 'barba', 'rasatura', 'bari', 'scuro', 'slider pro'],
    'Una barberia di tre generazioni, verde bottiglia e ottone: in apertura lo schermo diviso, il video del barbiere '
    'al lavoro da una parte e il racconto dall’altra, con le forbici che aprono e chiudono le lame sulla linea di '
    'mezzo e il bollino «dal 1962»; il listino, i tagli in coverflow 3D, la rasatura e la prenotazione della '
    'poltrona.',
    [('I servizi', SERVIZI), ('La bottega', BOTTEGA), ('Il listino', HOME + '#listino'), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('servizi', 'I servizi', 'servizi'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Albert Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'DM Serif Text\', \'Georgia\', serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['DM Serif Text', 'Albert Sans', 'IBM Plex Mono'],
    corsivo_di=['DM Serif Text'], peso_titoli='400', raggio=4, grana=6, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # apertura rifatta il 31 ottobre 2026


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'la-bottega.json': build_bottega(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
