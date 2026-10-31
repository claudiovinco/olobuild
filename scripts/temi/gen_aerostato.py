# -*- coding: utf-8 -*-
"""Genera il tema «Aerostato» (id aerostato): voli in mongolfiera all'alba sulle colline del Monferrato: il volo
condiviso, il volo privato per due, il buono regalo, il brindisi all'atterraggio.

Tema chiaro, il cielo dell'alba: crema, arancio bruciato del pallone, oro del bruciatore e azzurro. Titoli in DM Serif
Display (anche corsivo), testo in Figtree, dati in Fira Code.

Apertura: Slider Pro «l'altimetro», alto 16:9, un momento del volo per diapositiva col suo video pieno (il gonfiaggio, il
bruciatore, il cielo pieno di palloni, il volo sul prato) e un velo scuro solo dal lato del testo. A destra la scala
dell'altimetro, da zero a millecinquecento metri: il pallone sale (o scende) alla quota di quel momento, con la quota e
l'ora accanto. Le diapositive salgono dal basso (slideUp). Cursore: la scia.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'aerostato'
NAME = 'Aerostato'

HEX = {
    'primary': '#C2410C', 'primary_contrast': '#FFFFFF', 'secondary': '#2E6F95', 'secondary_contrast': '#FFFFFF',
    'accent': '#F2B33D', 'dark': '#141821', 'light': '#FFFFFF', 'muted': '#EDE6DC', 'muted_contrast': '#1F2430',
    'text': '#1F2430', 'text_muted': '#5A6070', 'background': '#F7F3EE', 'border': '#DDD3C5', 'link': '#B23C0B',
}

HOME, VOLI, REGALA, COME, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:voli}}', '{{PAGINA:regala-un-volo}}',
                                      '{{PAGINA:come-funziona}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'volo@aerostato.example', '+39 0141 000 315', 'tel:+390141000315'
LAT, LNG = '45.0507', '8.2662'

V_GONFIA, V_FUOCO, V_CIELO, V_ALBA, V_PRATO, V_SAGOMA = ('pexels-video-38862037', 'pexels-video-38840906',
                                                         'pexels-video-19308329', 'pexels-video-13444940',
                                                         'pexels-video-9490977', 'pexels-video-6277472')
PHOTOS = [
    'pexels-27677381.jpg', 'pexels-4007910.jpg', 'pexels-15994189.jpg', 'pexels-15617711.jpg', 'pexels-31456598.jpg',
    'pexels-38071581.jpg', 'pexels-10506211.jpg', 'pexels-9674332.jpg', 'pexels-16239705.jpg', 'pexels-8531189.jpg',
    'pexels-8915669.jpg',
]
VIDEOS = [V_GONFIA, V_FUOCO, V_CIELO, V_ALBA, V_PRATO, V_SAGOMA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='400', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
ORO, CIELO = C['acc2'], C['sec']
MONO = 'Fira Code'
VOCI = [('map-pin', 'Cascina Bricco 3 · Moncalvo (AT)', CONTATTI + '#decollo'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Aerostato', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Aerostato', 'Voli in mongolfiera all’alba sul Monferrato, da aprile a novembre. Due piloti, tre '
                   'palloni, il brindisi all’atterraggio in un prato diverso ogni volta.',
                   [('I voli', VOLI), ('Regala un volo', REGALA), ('Come funziona', COME), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'Il decollo', 'All’alba, da aprile a novembre<br>Il ritrovo un’ora prima del sole', VOCI,
                   '© 2026 Aerostato srl · Moncalvo · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=140,
                   fondo=C['sala'])


# ── L'apertura: la salita ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    """La salita: l'apertura resta ferma in cima mentre si scorre e il pallone sale dal fondo fino a uscire dall'alto,
    oscillando appena; le quote sulla destra e le nuvole scendono (più veloci quelle vicine), i testi si danno il cambio:
    il decollo, la quota, l'invito. Un solo video, il cielo dell'alba pieno di palloni."""
    m = K.mob
    entra = lambda i, f, esce=None: {'inizio': i, 'fine': esce or 100, 'tappe': (
        [{'p': 0, 'y': 50, 'o': 0}, {'p': (f - i) / float((esce or 100) - i) * 100, 'y': 0, 'o': 100}]
        + ([{'p': 80, 'y': 0, 'o': 100}, {'p': 100, 'y': -50, 'o': 0}] if esce else []))}
    livelli = []
    livelli = [
        # il cielo dell'alba è chiaro in alto: una sfumatura scura scende dall'alto sotto i titoli
        K.livello('ae-cielo', tipo='shape', x=0, y=0, w=100, h=62, entra='fadeIn', ritardo=0, durata=800,
                  shapeGradient={'angle': 180, 'from': mix(C['nero'], 82), 'to': mix(C['nero'], 0), 'fromStop': 45},
                  responsive={'mobile': {'height': 50}}),
    ]
    # le quote: scendono come se il pallone salisse
    for k, q in enumerate(('0 m', '300 m', '600 m', '900 m', '1.200 m')):
        y = 88 - 22 * k
        livelli.append(K.livello('ae-quota%d' % k, contenuto=q, x=88, y=y, w='auto', size=12, peso='600', colore=C['chiaro'],
                                 bgColor=mix(C['nero'], 55), padding=6, borderRadius=4, fontFamily=MONO, letterSpacing=1,
                                 entra='fadeIn', ritardo=300 + 100 * k,
                                 scrollMove={'inizio': 0, 'fine': 100, 'da': {}, 'a': {'y': 1400}},
                                 responsive={'mobile': {'x': 76, 'y': y, 'fontSize': m(10)}}))
    livelli.append(K.livello('ae-asta', tipo='shape', x=87.2, y=0, w=0.2, h=100, bgColor=mix(C['chiaro'], 40), entra='fadeIn',
                             ritardo=200, responsive={'mobile': {'x': 74.6, 'width': 0.4}}))
    # il pallone, disegnato in px attorno al centro dell'involucro: l'involucro tondo, la gonna che si stringe verso la
    # bocca, lo spicchio chiaro, le funi e il cesto; salgono insieme dal fondo fino a uscire dall'alto, oscillando
    sale = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0}, {'p': 30, 'x': 30, 'y': -280}, {'p': 60, 'x': -20, 'y': -600},
                                               {'p': 100, 'x': 20, 'y': -1150}]}
    cx, cy, mx, my = 720.0, 560.0, 320.0, 1000.0
    pezzi = [  # nome, dx, dy, larghezza, altezza (px dal centro dell'involucro), aspetto
        ('busta', -110, -110, 220, 220, dict(borderRadius=999, shapeGradient={'angle': 160, 'from': ORO, 'to': C['acc']})),
        ('gonna', -76, 50, 152, 100, dict(borderRadiusLinked=False, borderRadiusTL=0, borderRadiusTR=0, borderRadiusBR=76,
                                          borderRadiusBL=76, shapeGradient={'angle': 160, 'from': mix(ORO, 70, C['acc']),
                                                                            'to': C['acc']})),
        ('spicchio', -34, -108, 68, 250, dict(borderRadius=999, bgColor=mix(C['chiaro'], 20))),
        ('bocca', -26, 146, 52, 12, dict(borderRadius=4, bgColor=mix(C['acc'], 60, C['nero']))),
        ('fune1', -22, 158, 2, 42, dict(bgColor=mix(C['chiaro'], 75))),
        ('fune2', 20, 158, 2, 42, dict(bgColor=mix(C['chiaro'], 75))),
        ('cesto', -24, 198, 48, 40, dict(borderRadius=6, bgColor=mix(C['acc'], 40, C['nero']))),
    ]
    for nome, dx, dy, w, h, extra in pezzi:
        a_ = _pc(cx + dx, cy + dy, w, h, W, H)
        b_ = _pc(mx + dx, my + dy, w, h, WM, HM)
        livelli.append(K.livello('ae-' + nome, tipo='shape', x=a_[0], y=a_[1], w=a_[2], h=a_[3], entra='fadeInUp', ritardo=100,
                                 durata=1200, esce='fadeOut', scrollMove=sale,
                                 responsive={'mobile': {'x': b_[0], 'y': b_[1], 'width': b_[2], 'height': b_[3]}}, **extra))
    # i testi: il decollo (subito), la quota (a metà), l'invito (alla fine)
    livelli += [
        K.livello('ae-o1', contenuto='Ore 5:40 · il ritrovo al campo', x=6, y=16, w=40, size=12, peso='600', colore=ORO,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  scrollMove={'inizio': 0, 'fine': 30, 'da': {}, 'a': {'y': -80, 'o': 0}},
                  responsive={'mobile': {'fontSize': m(11), 'y': 9, 'x': 7, 'width': 86}}),
        K.livello('ae-t1', contenuto='All’alba si sale,<br><em>senza un rumore.</em>', tag='h1', x=6, y=20, w=44, size=76,
                  peso='400', colore=C['chiaro'], fontFamily='DM Serif Display', lineHeight=1.04, entra='maskFromBottom',
                  ritardo=300, durata=1200, scrollMove={'inizio': 0, 'fine': 30, 'da': {}, 'a': {'y': -120, 'o': 0}},
                  textShadow={'x': 0, 'y': 2, 'blur': 28, 'color': mix(C['nero'], 70)},
                  responsive={'mobile': {'fontSize': m(36), 'y': 12, 'x': 7, 'width': 86}}),
        K.livello('ae-s1', contenuto='Scorrete: il pallone si stacca dall’erba.', x=6, y=43.5, w=30, size=15, peso='500',
                  colore=mix(C['chiaro'], 90), fontFamily=MONO, letterSpacing=1, ritardo=900,
                  textShadow={'x': 0, 'y': 1, 'blur': 14, 'color': mix(C['nero'], 70)},
                  scrollMove={'inizio': 0, 'fine': 20, 'da': {}, 'a': {'o': 0}},
                  responsive={'mobile': {'fontSize': m(12), 'y': 23, 'x': 7, 'width': 62}}),
        K.livello('ae-t2', contenuto='Milleduecento metri,<br><em>e sotto le Langhe.</em>', tag='p', x=6, y=36, w=48, size=70,
                  peso='400', colore=C['chiaro'], fontFamily='DM Serif Display', lineHeight=1.04, entra='fadeIn', ritardo=0,
                  textShadow={'x': 0, 'y': 2, 'blur': 28, 'color': mix(C['nero'], 70)}, scrollMove=entra(32, 48, 72),
                  responsive={'mobile': {'fontSize': m(33), 'y': 34, 'x': 7, 'width': 86}}),
        K.livello('ae-velo', tipo='shape', x=0, y=36, w=100, h=64, entra='fadeIn', ritardo=0,
                  shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 30},
                  scrollMove={'inizio': 66, 'fine': 82, 'da': {'o': 0}, 'a': {'o': 100}},
                  responsive={'mobile': {'y': 40, 'height': 60}}),
        K.livello('ae-t3', contenuto='Un’ora in cielo,<br><em>la prossima alba.</em>', tag='p', x=6, y=49, w=50, size=72,
                  peso='400', colore=C['chiaro'], fontFamily='DM Serif Display', lineHeight=1.04, entra='fadeIn', ritardo=0,
                  scrollMove=entra(72, 88), responsive={'mobile': {'fontSize': m(34), 'y': 58, 'x': 7, 'width': 86}}),
        K.livello('ae-b', tipo='button', contenuto='Prenota il volo', x=6, y=70, w='auto', size=14, peso='600',
                  colore=C['nero'], bgColor=ORO, padding=16, borderRadius=999, entra='fadeIn', ritardo=0, buttonUrl=VOLI,
                  hover={'bgColor': C['chiaro'], 'duration': 250}, scrollMove=entra(80, 94),
                  responsive={'mobile': {'y': 75, 'x': 7, 'fontSize': m(12)}}),
        K.livello('ae-l', tipo='button', contenuto='Regala un volo', x=19, y=70, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), entra='fadeIn', ritardo=0, buttonUrl=REGALA,
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  scrollMove=entra(82, 96), responsive={'tablet': {'x': 22}, 'mobile': {'y': 80, 'x': 7, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('ae-1', K.sfondo_video(V_CIELO, overlay=0.1, colore=C['nero']), livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2200, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def ora_per_ora(t):
    fasi = [('05:40', 'Il ritrovo', 'Al campo di decollo, un’ora prima del sole: caffè e due parole col pilota.',
             'pexels-38071581.jpg'),
            ('06:00', 'Il gonfiaggio', 'Tutti a tenere il pallone mentre si riempie: è la parte che si ricorda di più.',
             'pexels-27677381.jpg'),
            ('06:15', 'Il decollo', 'Il cesto si stacca dall’erba. Nessuno scossone, nessun rumore oltre al fuoco.',
             'pexels-15617711.jpg'),
            ('06:50', 'In quota', 'Fino a milleduecento metri, dove va il vento: le Langhe, le Alpi, il Monviso.',
             'pexels-10506211.jpg'),
            ('08:00', 'Il brindisi', 'Si atterra in un prato, si brinda e si torna al campo col furgone.',
             'pexels-8531189.jpg')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': o, 'desc': d, 'number': o, 'image': K.img(f), 'link_url': VOLI, 'color': C['acc']}
                  for o, n, d, f in fasi],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': C['acc'],
        'number_hover_color': C['acc'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 52, 'name_weight': '400', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['acc'],
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(ORO, 12), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 280, 'peek_ratio': '4/3', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': ORO}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Aerostato · Monferrato, dal 2004', ['Un’ora sopra le colline,', 'alla velocità del vento.'],
                                size=58, layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Si parte all’alba, quando l’aria è ferma e fresca. Il pallone va dove va il vento: per questo '
                                'in cesto non si sente un filo d’aria, e si parla a voce bassa senza volerlo. Si atterra in un '
                                'prato diverso ogni volta, e la squadra a terra arriva con lo spumante.', size=17,
                             color=C['testo']),
                      K.button(t, 'Come funziona', COME, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('1.200 m', 'La quota più alta'), ('1 ora', 'Di volo'), ('8.600', 'Passeggeri'),
                            ('3', 'Palloni')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Ora per ora', ['Dal buio', 'al brindisi.'], size=58,
                         tagline='Passate su un’ora: la foto si affaccia.')]),
        t.one([ora_per_ora(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SAGOMA, 'Il silenzio', 'Lassù <em>non si sente il vento.</em>',
                             'Perché il pallone va con lui, alla sua velocità. Si sente un cane che abbaia, una campana, il fuoco '
                             'ogni tanto.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8915669.jpg', 'Due persone brindano con lo spumante', ratio='4/5', tilt=True)],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Regala un volo', ['Un buono', 'per l’alba giusta.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Il buono vale un anno: chi lo riceve sceglie la data, noi confermiamo la sera prima guardando il '
                                'vento. Arriva per email o stampato in una busta col sigillo.', size=17),
                      K.specs(t, [('Volo condiviso', '230 € a persona'), ('Volo privato per due', '690 €'),
                                  ('Valido', 'dodici mesi, data a scelta')]),
                      K.button(t, 'Regala un volo', REGALA, kind='pieno', pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Avevo paura dell’altezza. Dopo cinque minuti mi sono dimenticata di averla: il cesto è fermo come un balcone, e '
           'sotto c’era tutto il Monferrato rosa di sole.', 'Valeria T.', 'Volo condiviso, settembre 2025')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_ALBA, 'I voli', 'La prossima alba <em>è libera.</em>',
                          'Da aprile a novembre, ogni mattina che il vento lo permette.', 'Scegli il volo', VOLI,
                          overlay=40, video=True))
    return out


def build_voli():
    t = Tpl(ID, 'voli')
    out = [K.intro(t, 'I voli', ['Un’ora in cielo,', 'tutta la mattina con noi.'],
                   'Il volo dura un’ora; con il ritrovo, il gonfiaggio, il brindisi e il ritorno al campo contate quattro ore.',
                   bg=K.video_bg(V_CIELO), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'I voli', ['Tre modi', 'di salire.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Volo condiviso', '230', 'a persona', 'Un’ora di volo, fino a dodici passeggeri\nIl brindisi all’atterraggio\nIl '
             'certificato di volo', False, ''),
            ('Volo privato per due', '690', 'il cesto è solo vostro', 'Un’ora di volo col pilota\nLa colazione in cascina\nLe '
             'foto del volo', True, 'il più scelto'),
            ('Volo per il gruppo', '1.900', 'fino a otto persone', 'Il pallone tutto per voi\nLa colazione in cascina\nUn video '
             'di tre minuti', False, ''),
        ], 'Prenota la data', CONTATTI + '#scrivici', nota='Se il vento non lo permette il volo si sposta: non si perde mai.',
            sfondo=C['sala']),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dall’alto', ['Quello che', 'si vede.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-10506211.jpg', 'La nebbia sulle colline e i boschi', 'La nebbia', 'alle 6:30'),
                               ('pexels-16239705.jpg', 'Le colline e le montagne all’orizzonte', 'Le Alpi', 'col cielo terso'),
                               ('pexels-9674332.jpg', 'Il sole che sale sopra le colline', 'Il sole', 'alle 6:40'),
                               ('pexels-38071581.jpg', 'Un prato in collina con la nebbia bassa', 'I prati', 'per atterrare'),
                               ('pexels-31456598.jpg', 'Tanti palloni contro il sole che sorge', 'Gli altri palloni',
                                'nei giorni di festa')])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Sul', 'volo.', [
        ('E se c’è vento?', 'Il pilota decide la sera prima e la mattina stessa. Se non si vola si sceglie un’altra data, '
         'senza costi.'),
        ('Che cosa indosso?', 'Vestiti comodi e scarpe chiuse: in quota fa la stessa temperatura che a terra.'),
        ('C’è un limite di età?', 'Dai sei anni, se il bambino supera il bordo del cesto. Nessun limite verso l’alto.'),
    ], 'ae-faq'))
    return out


def build_regala():
    t = Tpl(ID, 'regala-un-volo')
    out = [K.intro(t, 'Regala un volo', ['Un buono,', 'un’alba a scelta.'],
                   'Vale dodici mesi. Chi lo riceve sceglie la data; se il vento non è buono si sposta, senza costi.',
                   bg=K.photo_bg('pexels-27677381.jpg'), size=92)]
    campi = [K.campo('text', 'Il vostro nome', 'nome', True), K.campo('email', 'La vostra email', 'email', True),
             K.campo('text', 'Per chi è', 'per', True, segnaposto='Il nome che va sul buono'),
             K.campo('select', 'Il volo', 'volo', True, opzioni='Volo condiviso · 230 €\nDue voli condivisi · 460 €\n'
                                                                  'Volo privato per due · 690 €'),
             K.campo('select', 'Come lo volete', 'consegna', True, opzioni='Per email, subito\nStampato, in busta col sigillo'),
             K.campo('textarea', 'La dedica', 'dedica', larghezza='1-1', segnaposto='Va stampata sul buono, così com’è.')]
    out.append(K.sezione_modulo(t, 'buono', 'Il buono', ['Scegliete il volo,', 'scrivete la dedica.'],
                                'Vi mandiamo il link per il pagamento; il buono arriva appena è pagato.', VOCI, campi,
                                'Aerostato — buono regalo', 'Prepara il buono', 'Ricevuto. Vi scriviamo con il link per il pagamento.'))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'L’ho regalato a mio padre per i settant’anni. È tornato a casa e per una settimana ha raccontato solo del silenzio.',
        'Francesca M.', 'Ha regalato un volo privato')])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_come():
    t = Tpl(ID, 'come-funziona')
    out = [K.intro(t, 'Come funziona', ['Prima, durante,', 'dopo il volo.'],
                   'Tutto quello che c’è da sapere: il ritrovo, il meteo, la sicurezza, il ritorno al campo.',
                   bg=K.video_bg(V_GONFIA), size=92)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Scegliete la data', 'description': 'Da aprile a novembre. Vi confermiamo la sera '
                   'prima, guardando il vento.'},
                  {'number': '02', 'title': 'Il ritrovo all’alba', 'description': 'Al campo, un’ora prima del sole. Il caffè '
                   'lo portiamo noi.'},
                  {'number': '03', 'title': 'Un’ora di volo', 'description': 'Con il pilota, fino a milleduecento metri.'},
                  {'number': '04', 'title': 'Il brindisi', 'description': 'All’atterraggio, poi il furgone vi riporta al '
                   'campo.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '500', 'title_color': C['testo'], 'title_size': 26,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})
    out.append(t.section([
        t.one([K.sechead(t, 'Quattro passi', ['Voi vi alzate presto,', 'al resto pensiamo noi.'], size=56)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-15994189.jpg', 'Un pallone a strisce contro il cielo dell’alba', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'La sicurezza', ['Due piloti,', 'trentamila ore.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'I palloni sono revisionati ogni cento ore di volo; i piloti hanno la licenza commerciale e '
                                'volano qui da vent’anni. Se il vento supera i dieci nodi non si parte, nemmeno se siete '
                                'arrivati da lontano.', size=17),
                      K.specs(t, [('Piloti', 'due, con licenza commerciale'), ('Revisione', 'ogni cento ore'),
                                  ('Vento massimo', 'dieci nodi al decollo')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_FUOCO, 'Il bruciatore', 'Tre secondi di fuoco, <em>cento metri di quota.</em>',
                             'Il pilota scalda l’aria nel pallone a colpi brevi: è l’unico rumore del volo.', overlay=40))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Scriveteci,', 'guardiamo il vento.'],
                   'Per una data, un buono, un volo per il gruppo o una domanda sul meteo.',
                   bg=K.photo_bg('pexels-9674332.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Il volo', 'volo', True, opzioni='Volo condiviso\nVolo privato per due\nVolo per il gruppo\n'
                                                                  'Ho un buono da usare'),
             K.campo('date', 'La data che preferite', 'data'), K.campo('select', 'Quanti siete', 'persone', opzioni='1\n2\n3\n4\n'
                                                                                                                 '5\n6\n7\n8'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Una data,', 'e vi diciamo se si vola.'],
                                'Rispondiamo entro un giorno. La conferma arriva la sera prima del volo.', VOCI, campi,
                                'Aerostato — contatto', 'Invia', 'Ricevuto. Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il campo di decollo', ['A Moncalvo,', 'sopra le vigne.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Cascina Bricco 3, Moncalvo (AT)'), ('Il ritrovo', 'un’ora prima dell’alba'),
                                  ('Parcheggio', 'in cascina')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Aerostato · il campo di decollo', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='decollo', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori rotta', color=K.acc_media, align='center'),
                       K.headline(t, 'Il vento <em>ci ha portati altrove.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Capita. Si atterra e si torna al campo.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SAGOMA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ospitalità e viaggi', 'La salita',
    ['mongolfiera', 'voli', 'esperienze', 'regalo', 'alba', 'monferrato', 'turismo', 'chiaro', 'slider pro'],
    'Voli in mongolfiera all’alba, crema e arancio bruciato: in apertura la salita, col pallone che sale nel cielo '
    'pieno di palloni mentre si scorre, le quote che scendono e i testi che si danno il cambio; il volo ora per ora, '
    'i voli coi prezzi, il buono regalo con la dedica e la sicurezza.',
    [('I voli', VOLI), ('Regala un volo', REGALA), ('Come funziona', COME), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('voli', 'I voli', 'voli'), ('regala-un-volo', 'Regala un volo', 'regala-un-volo'),
     ('come-funziona', 'Come funziona', 'come-funziona'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Figtree\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'DM Serif Display\', \'Georgia\', serif',
    '\'Fira Code\', ui-monospace, Menlo, monospace', ['DM Serif Display', 'Figtree', 'Fira Code'],
    corsivo_di=['DM Serif Display'], peso_titoli='400', raggio=14, grana=0, titoli_line='1.04', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'bolle'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'voli.json': build_voli(), 'regala-un-volo.json': build_regala(), 'come-funziona.json': build_come(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
