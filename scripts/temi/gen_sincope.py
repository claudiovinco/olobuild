# -*- coding: utf-8 -*-
"""Genera il tema «Sincope» (id sincope): un jazz club sui Navigli, a Milano, aperto nel 1987: musica dal vivo cinque
sere a settimana, la big band del giovedì, la jam session del sabato dopo mezzanotte, il bancone e i tavoli sotto il palco.

Tema scuro, il nero della sala, l'ottone dei fiati e il viola delle luci di scena. Titoli in Bodoni Moda (anche corsivo),
testo in Manrope, dati in Fragment Mono.

Apertura: Slider Pro «il giradischi», alta 16:9, una serata per diapositiva col suo video pieno (le mani sul piano, il sax
nel locale, la cantante, il palco nel fumo) e un velo scuro solo dal lato del testo. A destra un disco a trentatré giri:
i solchi, il riflesso fermo, l'etichetta col nome della serata che gira piano; il braccio parte dal riposo e si posa sul
disco a ogni diapositiva. Le serate passano attraverso il buio (fadeThroughDark). Cursore: l'alone, come un occhio di bue.
"""
import os
import sys
import math

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'sincope'
NAME = 'Sincope'

HEX = {
    'primary': '#D9A441', 'primary_contrast': '#120C08', 'secondary': '#8E6BFF', 'secondary_contrast': '#0F0B0D',
    'accent': '#E8C27A', 'dark': '#070506', 'light': '#FFF9F0', 'muted': '#1A1416', 'muted_contrast': '#EFE6D8',
    'text': '#EFE6D8', 'text_muted': '#A79C8C', 'background': '#0F0B0D', 'border': '#2E2427', 'link': '#D9A441',
}

HOME, PROGRAMMA, CLUB, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:programma}}', '{{PAGINA:il-club}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'tavoli@sincope.example', '+39 02 0000 1987', 'tel:+390200001987'
LAT, LNG = '45.4523', '9.1716'

V_PIANO, V_SAX_BAR, V_VOCE, V_PALCO, V_BASSO, V_SAX = ('pexels-video-8513594', 'pexels-video-8995561', 'pexels-video-8994835',
                                                     'pexels-video-9419420', 'pexels-video-38398606', 'pexels-video-8594184')
PHOTOS = [
    'pexels-8040838.jpg', 'pexels-8512617.jpg', 'pexels-8040887.jpg', 'pexels-32599545.jpg', 'pexels-9002831.jpg',
    'pexels-7450655.jpg', 'pexels-10517362.jpg', 'pexels-12448202.jpg', 'pexels-7902183.jpg', 'pexels-36638009.jpg',
    'pexels-38127520.jpg', 'pexels-36471696.jpg', 'pexels-14351798.jpg', 'pexels-17239882.jpg', 'pexels-8639379.jpg',
    'pexels-20385213.jpg',
]
VIDEOS = [V_PIANO, V_SAX_BAR, V_VOCE, V_PALCO, V_BASSO, V_SAX]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=False)
C = K.c
OTTONE, LUCI = C['acc'], C['sec']
TITOLI, MONO = 'Bodoni Moda', 'Fragment Mono'
VOCI = [('map-pin', 'Alzaia Naviglio Grande 64 · Milano', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Sincope', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Sincope', 'Jazz club sul Naviglio Grande dal 1987. Musica dal vivo dal martedì al sabato, il bancone '
                   'aperto dalle sette, la jam session del sabato fino a quando smettono.',
                   [('Il programma', PROGRAMMA), ('Il club', CLUB), ('Prenota un tavolo', PRENOTA),
                    ('Privacy', '/privacy-policy/')],
                   'Le serate', 'Dal martedì al sabato · bancone dalle 19<br>Musica dalle 21:30', VOCI,
                   '© 2026 Sincope · Associazione culturale · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150)


# ── L'apertura: il disco sul piatto ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
SERATE = [  # linguetta, occhiello, riga 1, riga 2, testo, video, etichetta, colore dell'etichetta
    ('Martedì', 'Martedì 4 novembre · ore 21:30', 'Tre musicisti,', 'nessuna scaletta.',
     'Il trio di Lia Ferrante: pianoforte, contrabbasso e batteria, e due ore di standard che non suonano mai uguali.',
     V_PIANO, 'Lia Ferrante<br>Trio', LUCI),
    ('Giovedì', 'Giovedì 6 novembre · ore 21:30', 'Diciassette fiati,', 'un palco piccolo.',
     'La big band di casa suona ogni giovedì da trentadue anni: si sta stretti, si sente tutto.',
     V_SAX_BAR, 'Big Band<br>di casa', OTTONE),
    ('Venerdì', 'Venerdì 7 novembre · ore 22:00', 'Una voce,', 'e il silenzio dopo.',
     'Ada Moretti canta Billie Holiday e le sue canzoni. Mentre canta non parla nessuno, nemmeno al bancone.',
     V_VOCE, 'Ada Moretti<br>Quartet', C['acc2']),
    ('Sabato', 'Sabato 8 novembre · da mezzanotte', 'Dopo mezzanotte', 'suona chi vuole.',
     'La jam session: chi ha portato lo strumento sale sul palco, chi no ascolta. Si chiude quando smettono.',
     V_PALCO, 'Jam<br>session', mix(LUCI, 60, OTTONE)),
]
# il disco sta al centro, dietro i testi, inclinato come sul piatto (58°); scorrendo si raddrizza e cresce
RADDRIZZA = {'inizio': 0, 'fine': 90, 'da': {'rx': 58}, 'a': {'rx': 0, 's': 1.08}}


def _box(cx, cy, r, tw, th):
    return round((cx - r) / tw * 100, 2), round((cy - r) / th * 100, 2), round(2 * r / tw * 100, 2), round(2 * r / th * 100, 2)


def disco(n, etichetta, colore):
    m = K.mob
    cx, cy, mx, my = 720.0, 470.0, 320.0, 780.0
    out = []

    def cerchio(lid, r, rm, **extra):
        x, y, w, h = _box(cx, cy, r, W, H)
        xm, ym, wm, hm = _box(mx, my, rm, WM, HM)
        resp = {'mobile': {'x': xm, 'y': ym, 'width': wm, 'height': hm}}
        return K.livello(lid, tipo=extra.pop('tipo', 'shape'), x=x, y=y, w=w, h=h, borderRadius=999, responsive=resp,
                         scrollMove=RADDRIZZA, **extra)

    out.append(cerchio('si-vinile%d' % n, 380, 300, bgColor=mix(C['nero'], 96), entra='fadeIn', ritardo=0, durata=900,
                       esce='fadeOut', boxShadow={'x': 0, 'y': -20, 'blur': 90, 'spread': -10, 'color': mix(OTTONE, 22)}))
    for k, (r, rm) in enumerate(((362, 286), (326, 258), (288, 228), (246, 194), (200, 158))):
        out.append(cerchio('si-solco%d-%d' % (n, k), r, rm, bgColor='transparent', borderWidth=1,
                           borderColor=mix(C['chiaro'], 9 if k % 2 else 14), entra='fadeIn', ritardo=200 + 60 * k,
                           durata=700, esce='fadeOut'))
    out.append(cerchio('si-riflesso%d' % n, 376, 296, shapeGradient={'angle': 135, 'from': mix(C['chiaro'], 0),
                                                                   'to': mix(C['chiaro'], 12), 'fromStop': 55},
                       entra='fadeIn', ritardo=400, durata=900, esce='fadeOut'))
    out.append(cerchio('si-etichetta%d' % n, 112, 96, bgColor=colore, entra='fadeIn', ritardo=300, durata=800,
                       esce='fadeOut'))
    # il nome sopra il foro e il lato sotto: girano col disco attorno al suo centro (l'origine sta fuori dal riquadro)
    for nome, testo, su, alto, size, sizem, font, peso in (('nome', etichetta, 68, 50, 20, 11, TITOLI, '600'),
                                                             ('lato', 'Lato A · 33 giri', -15, 19, 10, 8, MONO, '500')):
        x, _, w, _ = _box(cx, cy, 90, W, H)
        xm, _, wm, _ = _box(mx, my, 80, WM, HM)
        y, h = round((cy - su) / H * 100, 2), round(alto / H * 100, 2)
        fm = 96.0 / 112.0 * 0.95
        ym, hm = round((my - su * fm) / HM * 100, 2), round(alto * fm / HM * 100, 2)
        oy = round(su / alto * 100, 1)
        out.append(K.livello('si-%s%d' % (nome, n), contenuto=testo, x=x, y=y, w=w, h=h, size=size, peso=peso,
                             colore=C['nero'], align='center', fontFamily=font, lineHeight=1.15,
                             letterSpacing=1 if nome == 'lato' else 0, textTransform='uppercase' if nome == 'lato' else 'none',
                             esce='fadeOut', scrollMove=RADDRIZZA,
                             timeline=K.tl(x, y, [(0, {'rotation': 0, 'originX': 50, 'originY': oy, 'opacity': 0}),
                                                  (700, {'rotation': 12, 'originX': 50, 'originY': oy, 'opacity': 100}),
                                                  (20700, {'rotation': 372, 'originX': 50, 'originY': oy, 'opacity': 100})],
                                           20700, ritardo=400),
                             responsive={'mobile': {'x': xm, 'y': ym, 'width': wm, 'height': hm, 'fontSize': m(sizem)},
                                         'notebook': {'fontSize': round(size * 0.86)}, 'tablet': {'fontSize': round(size * 0.72)}}))
    out.append(cerchio('si-foro%d' % n, 8, 7, bgColor=mix(C['nero'], 100), entra='fadeIn', ritardo=500, durata=400,
                       esce='fadeOut'))
    return out


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, video, etichetta, colore) in enumerate(SERATE):
        n = i + 1
        livelli = [
            # un velo scuro sfumato dall'alto sotto i testi centrati (il video resta pieno ai lati e in basso)
            K.livello('si-cielo%d' % n, tipo='shape', x=0, y=0, w=100, h=62, entra='fadeIn', ritardo=0, durata=600,
                      esce='fadeOut', shapeGradient={'angle': 180, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0),
                                                     'fromStop': 55},
                      responsive={'mobile': {'height': 64}}),
        ]
        livelli += disco(n, etichetta, colore)
        livelli += [
            K.livello('si-o%d' % n, contenuto=occ, x=20, y=14, w=60, size=12, peso='400', colore=OTTONE, letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, align='center', ritardo=200,
                      responsive={'mobile': {'fontSize': m(11), 'y': 9, 'x': 6, 'width': 88}, 'tablet': {'y': 15.5}}),
            K.livello('si-t%d' % n, contenuto=r1 + '<br><em>' + r2 + '</em>', tag='h1' if n == 1 else 'h2', x=10, y=18,
                      w=80, size=80, peso='500', colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-1,
                      align='center', entra='maskFromBottom', ritardo=300, durata=1200,
                      responsive={'mobile': {'fontSize': m(36), 'y': 12.5, 'x': 5, 'width': 90}}),
            K.livello('si-s%d' % n, contenuto=testo, x=27, y=40.5, w=46, size=17, peso='400', colore=mix(C['chiaro'], 86),
                      lineHeight=1.6, align='center', ritardo=800,
                      responsive={'mobile': {'fontSize': m(14), 'y': 26, 'x': 7, 'width': 86}}),
            K.livello('si-b%d' % n, tipo='button', contenuto='Prenota un tavolo', x=33, y=75, w='auto', size=12, peso='700',
                      colore=C['on_acc'], bgColor=OTTONE, padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1100, buttonUrl=PRENOTA, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 37, 'x': 7, 'fontSize': m(11)}, 'tablet': {'x': 27}}),
            K.livello('si-l%d' % n, tipo='button', contenuto='Il programma', x=51, y=75, w='auto', size=12, peso='700',
                      colore=C['chiaro'], bgColor='transparent', padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, borderWidth=1, borderColor=mix(C['chiaro'], 50), ritardo=1250, buttonUrl=PROGRAMMA,
                      hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 37, 'x': 52, 'fontSize': m(11)}}),
        ]
        sl.append(K.diapositiva('si-%d' % n, K.sfondo_video(video, overlay=0.2, colore=C['nero']), livelli, durata=8000,
                                tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fadeThroughDark', durata_trans=1100,
                       showArrows=False, showDots=False, showTabs=True, tabPosition='bottom', parallax=False,
                       autoplaySpeed=8000, progressBarColor=OTTONE, navColor=C['chiaro'],
                       heightMobile={'mode': 'px', 'value': 1400})


PROGRAMMA_SERATE = [  # data, artista, genere e formazione, ingresso
    ('04.11', 'Lia Ferrante Trio', 'Pianoforte, contrabbasso, batteria · standard', '15 €'),
    ('05.11', 'Samuel Okoye', 'Chitarra e voce · soul e bossa', '12 €'),
    ('06.11', 'Big Band di casa', 'Diciassette fiati · swing', '18 €'),
    ('07.11', 'Ada Moretti Quartet', 'Voce, piano, contrabbasso, batteria', '20 €'),
    ('08.11', 'Jam session', 'Da mezzanotte · porta lo strumento', 'Gratis'),
    ('11.11', 'Davide Leone Quintet', 'Sax tenore e hard bop', '15 €'),
    ('13.11', 'Big Band di casa', 'Ospite: Nino Sala al soprano', '18 €'),
    ('14.11', 'Notte di Monk', 'Tre pianisti, un solo autore', '20 €'),
]


def programma(t, voci=PROGRAMMA_SERATE):
    return t.classic('worklist', {
        'items': [{'number': d, 'title': a, 'category': g, 'year': p, 'link_url': PRENOTA} for d, a, g, p in voci],
        'divider_color': C['filo'], 'row_padding_y': 26, 'row_hover_bg': mix(OTTONE, 7), 'hover_indent': 28,
        'number_color': OTTONE, 'number_size': 15, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 40, 'title_weight': '500', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': OTTONE, 'year_size': 18, 'show_arrow': True, 'arrow_color': OTTONE,
        'mono_font_family': MONO}, style={})


def musicisti(t):
    voci = [('Ada Moretti', 'Voce · il venerdì', 'Canta Billie Holiday e le sue canzoni, in italiano e in inglese.',
             'pexels-9002831.jpg'),
            ('Nino Sala', 'Sax soprano · il fondatore', 'Ha aperto il club nel 1987. Suona ancora, il secondo giovedì del mese.',
             'pexels-7450655.jpg'),
            ('Davide Leone', 'Sax tenore', 'Hard bop, Coltrane e Rollins, e un quintetto che non ha paura del volume.',
             'pexels-10517362.jpg'),
            ('Samuel Okoye', 'Chitarra e voce', 'Soul, bossa e qualche canzone sua: la serata più dolce della settimana.',
             'pexels-7902183.jpg')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': PROGRAMMA,
                   'color': OTTONE} for k, (n, s, d, f) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': OTTONE,
        'number_hover_color': LUCI, 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': OTTONE,
        'name_size': 54, 'name_weight': '500', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': OTTONE,
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(OTTONE, 6), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 240, 'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': OTTONE, 'author_color': C['tenue'], 'quote_accent_color': OTTONE, 'quote_font': 'heading',
        'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': OTTONE}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Sincope · Naviglio Grande, dal 1987', ['Ottanta posti,', 'un palco a un metro.'],
                                size=60, layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Una sala lunga e bassa, il bancone in fondo, il palco così vicino che dal primo tavolo si '
                                'sentono i musicisti respirare. Dal martedì al sabato si suona alle nove e mezza; il sabato, '
                                'dopo mezzanotte, il palco è di chi ci sale.', size=17, color=C['testo']),
                      K.button(t, 'Il club', CLUB, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('1987', 'L’anno della prima sera'), ('5', 'Sere a settimana'), ('80', 'Posti'),
                            ('12.000', 'Concerti, più o meno')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Questa settimana', ['Cinque sere,', 'cinque dischi diversi.'], size=60,
                         tagline='Passate su una serata: la freccia porta alla prenotazione del tavolo.')]),
        t.one([programma(t, PROGRAMMA_SERATE[:5])]),
        t.one([K.button(t, 'Tutto il programma', PROGRAMMA, pad_top=26)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SAX, 'Il palco', 'A un metro <em>dal primo tavolo.</em>',
                             'Senza transenne e senza palco rialzato: il sax lo sentite nello stomaco prima che nelle orecchie.',
                             overlay=40))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi suona', ['Le voci', 'di casa.'], size=60,
                         tagline='Passate su un nome: il musicista si affaccia.')]),
        t.one([musicisti(t)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Siamo entrati per un bicchiere, siamo usciti alle due. A mezzanotte un ragazzo è salito col trombone e la sala si è '
           'zittita. Non so come si chiamasse, ma lo ricordo meglio di molti concerti negli stadi.', 'Marta e Luca',
        'Un sabato di ottobre')])], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PALCO, 'I tavoli', 'Il tavolo <em>sotto il palco.</em>',
                          'Ottanta posti: i tavoli sotto il palco si prenotano, il bancone è di chi arriva.',
                          'Prenota un tavolo', PRENOTA, overlay=40, video=True))
    return out


def build_programma():
    t = Tpl(ID, 'programma')
    out = [K.intro(t, 'Il programma', ['Novembre,', 'sera per sera.'],
                   'Si suona alle 21:30, la jam del sabato da mezzanotte. L’ingresso si paga alla porta e comprende la prima '
                   'consumazione.', bg=K.video_bg(V_PALCO), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le prossime sere', ['Otto serate,', 'una per disco.'], size=56,
                         tagline='I tavoli sotto il palco finiscono prima: conviene prenotarli.')]),
        t.one([programma(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le sere fisse', ['Ogni settimana,', 'sempre uguali e sempre diverse.'], size=56)]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5', tilt=True), K.label(t, quando, color=OTTONE, pad_top=18),
                      K.headline(t, nome, size=28, pad_bottom=8), K.text(t, desc, size=15)], wm='1-3', ws='1-1')
               for f, alt, quando, nome, desc in (
                   ('pexels-8040838.jpg', 'Il quartetto sul palco con le luci accese', 'Il martedì', 'I trii',
                    'Pianoforte, contrabbasso, batteria: lo standard come si suonava a New York.'),
                   ('pexels-12448202.jpg', 'La sezione dei fiati della big band', 'Il giovedì', 'La big band',
                    'Diciassette fiati su un palco per sei: si sta stretti, si sente tutto.'),
                   ('pexels-8512617.jpg', 'Un sassofono sotto le luci viola', 'Il sabato', 'La jam session',
                    'Da mezzanotte: chi ha lo strumento sale, gli altri ascoltano.'))],
              layout='33-33-33', gap=32),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di venire.', [
        ('Serve prenotare?', 'Per i tavoli sì, il bancone no: chi arriva si siede finché c’è posto.'),
        ('Si può cenare?', 'Si mangia al bancone: taglieri, focacce e un piatto caldo che cambia ogni sera.'),
        ('Posso suonare alla jam?', 'Sì: ci si iscrive alla porta, il batterista e il contrabbassista ci sono già.'),
    ], 'si-faq'))
    return out


def build_club():
    t = Tpl(ID, 'il-club')
    out = [K.intro(t, 'Il club', ['Dal 1987,', 'la stessa sala.'],
                   'Nino Sala lo ha aperto in una vecchia rimessa di barche sul Naviglio. I tavoli sono quelli, il pianoforte '
                   'è il terzo.', bg=K.photo_bg('pexels-8040887.jpg'), size=96)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-7450655.jpg', 'Nino Sala col cappello e il sax soprano', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Nino Sala', ['Prima il sax,', 'poi il club.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Nino ha suonato per vent’anni nei club degli altri. Nel 1987 ha preso in affitto una rimessa '
                                'di barche, ha costruito il palco con le assi dei pontili e ha chiamato gli amici. Il primo '
                                'concerto lo hanno ascoltato in diciotto. Adesso viene il secondo giovedì del mese, e suona.',
                             size=17),
                      K.specs(t, [('Apertura', '12 marzo 1987'), ('Il palco', 'le assi dei vecchi pontili'),
                                  ('Il pianoforte', 'il terzo, un mezza coda del 1962')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    bar = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f) if f else '', 'highlighted': bool(h),
                   'badge': h} for a, b, c, f, h in [
                      ('Il Sincope', 'Rye whiskey, vermut rosso, amaro di carciofo', '11 €', 'pexels-38127520.jpg', 'Della casa'),
                      ('Naviglio', 'Gin, bitter, soda al pompelmo', '10 €', 'pexels-36638009.jpg', ''),
                      ('Old fashioned', 'Bourbon, zucchero di canna, angostura', '11 €', '', ''),
                      ('Vini al calice', 'Sei rossi e quattro bianchi, quasi tutti lombardi', 'da 6 €', '', ''),
                      ('Tagliere del bancone', 'Salumi della Brianza, formaggi, focaccia', '14 €', '', '')]],
        'separator_style': 'dotted', 'separator_color': mix(OTTONE, 40), 'title_color': C['testo'], 'price_color': OTTONE,
        'description_color': C['tenue'], 'image_size': 64, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': OTTONE, 'badge_color': C['nero'], 'badge_border_radius': '2',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il bancone', ['Dalle sette,', 'prima della musica.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Il bancone apre alle sette: un bicchiere prima del concerto, un piatto caldo, la musica di '
                                'sottofondo è su vinile.', size=16)], wm='2-5', ws='1-1'),
               t.col([bar], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'La sala', ['Trentanove anni', 'di sere.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-9002831.jpg', 'La cantante al microfono e il sax dietro', 'Ada Moretti', 'venerdì'),
                               ('pexels-17239882.jpg', 'Le mani sulla tromba', 'La tromba', 'big band'),
                               ('pexels-36471696.jpg', 'Un giradischi da vicino', 'Il vinile', 'al bancone'),
                               ('pexels-32599545.jpg', 'Una tromba nella luce blu', 'La jam', 'sabato'),
                               ('pexels-14351798.jpg', 'Il pubblico al buio davanti al palco', 'La sala', 'piena'),
                               ('pexels-20385213.jpg', 'Una tromba appoggiata sullo spartito', 'Lo spartito', 'prima di cominciare')])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_BASSO, 'Il contrabbasso', 'Quello che tiene <em>tutto insieme.</em>',
                             'Nessuno guarda il contrabbassista. Provate a toglierlo: la sala se ne accorge in due battute.',
                             overlay=40))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Un tavolo,', 'sotto il palco.'],
                   'I tavoli si prenotano fino alle sei del giorno della serata; il bancone resta di chi arriva.',
                   bg=K.photo_bg('pexels-8639379.jpg'), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'I tavoli', ['Al bancone', 'o sotto il palco.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Bancone', '12', 'l’ingresso, alla porta', 'Il posto di chi arriva, senza prenotare\nLa prima consumazione '
             'compresa\nDa 12 a 20 € a seconda della sera', False, ''),
            ('Tavolo in sala', '10', 'a persona, oltre l’ingresso', 'Il tavolo tenuto fino alle 21:30\nUn calice o un cocktail\nDa due '
             'a sei persone', True, 'il più scelto'),
            ('Sotto il palco', '20', 'a persona, oltre l’ingresso', 'Il tavolo a un metro dai musicisti\nUna bottiglia ogni due '
             'persone\nIl tagliere del bancone', False, ''),
        ], 'Prenota', PRENOTA + '#tavolo', nota='L’ingresso della serata si paga alla porta: da 12 a 20 €, la jam è gratis.'),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True),
             K.campo('select', 'La serata', 'serata', True, opzioni='\n'.join('%s · %s' % (d, a) for d, a, g, p in PROGRAMMA_SERATE)),
             K.campo('select', 'Il tavolo', 'tavolo', True, opzioni='Tavolo in sala\nSotto il palco'),
             K.campo('select', 'Quanti siete', 'persone', True, opzioni='2\n3\n4\n5\n6'),
             K.campo('textarea', 'Una nota', 'note', larghezza='1-1', segnaposto='Un compleanno, un posto senza scale, un brano da '
                                                                              'chiedere alla band.')]
    out.append(K.sezione_modulo(t, 'tavolo', 'Il tavolo', ['Scegliete la sera,', 'vi teniamo il posto.'],
                                'Confermiamo con un messaggio entro un’ora. Il tavolo si tiene fino alle 21:30.', VOCI, campi,
                                'Sincope — tavolo', 'Prenota il tavolo', 'Ricevuto. Vi confermiamo entro un’ora.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Sul Naviglio Grande,', 'al numero 64.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Alzaia Naviglio Grande 64, Milano'), ('Metro', 'Porta Genova, otto minuti'),
                                  ('Tram', '2 e 9, fermata Porta Genova')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Sincope · jazz club', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pausa', color=OTTONE, align='center'),
                       K.headline(t, 'Qui c’è <em>una pausa di troppo.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=OTTONE),
                       K.text(t, 'Capita anche ai migliori. Riprendiamo dal tema.', size=17, align='center',
                              color=mix(C['chiaro'], 84)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SAX_BAR, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Eventi e musica', 'Il disco sul piatto',
    ['jazz', 'club', 'musica dal vivo', 'concerti', 'locale', 'cocktail bar', 'milano', 'scuro', 'slider pro'],
    'Un jazz club sul Naviglio, nero di sala, ottone e luci viola: in apertura il disco sul piatto, inclinato dietro '
    'il titolo, che gira col nome della serata e si raddrizza mentre si scorre; il programma sera per sera, chi '
    'suona, il bancone e i tavoli da prenotare.',
    [('Il programma', PROGRAMMA), ('Il club', CLUB), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('programma', 'Il programma', 'programma'), ('il-club', 'Il club', 'il-club'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bodoni Moda\', \'Didot\', serif',
    '\'Fragment Mono\', ui-monospace, Menlo, monospace', ['Bodoni Moda', 'Manrope', 'Fragment Mono'],
    corsivo_di=['Bodoni Moda'], peso_titoli='500', raggio=2, grana=5, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'faro'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'programma.json': build_programma(), 'il-club.json': build_club(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
