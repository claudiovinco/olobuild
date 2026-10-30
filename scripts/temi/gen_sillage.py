# -*- coding: utf-8 -*-
"""Genera il tema «Sillage» (id sillage): un laboratorio di profumeria artistica in Oltrarno, a Firenze. Dodici
fragranze fatte a mano, il profumo su misura in tre incontri, la bottega aperta fino alle otto di sera. Scuro come
l'ambra.

Nero tabacco, l'ambra delle resine, il rosso vino delle rose e il verde salvia del bergamotto. Titoli in Gloock, testo
in Figtree, note e formati in Red Hat Mono.

Apertura: Slider Pro sulla piramide olfattiva (note di testa, di cuore, di fondo) coi video veri (il fumo, le rose
bagnate, l'ambra versata); accanto al titolo un tondo con l'ingrediente che galleggia e, a lato, la piramide che si
tocca per saltare da una nota all'altra (livelli globali con «vai alla diapositiva»); il cambio è a strati (stack).
Poi la parola che dà il nome al laboratorio col fumo dentro le lettere (Text mask), le famiglie da scegliere girando
una ghiera (Ghiera), il profumo su misura da comporre nota per nota col prezzo (Lookbook mixer), le fragranze in
carosello (Overlay slider) e il flacone disegnato a tratto (SVG animator).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'sillage'
NAME = 'Sillage'

HEX = {
    'primary': '#E0A458', 'primary_contrast': '#140C07', 'secondary': '#7A2B3A', 'secondary_contrast': '#FAF3EA',
    'accent': '#A8C3A0', 'dark': '#060403', 'light': '#FAF3EA', 'muted': '#1A120D', 'muted_contrast': '#F2E8DC',
    'text': '#F2E8DC', 'text_muted': '#AE9F90', 'background': '#0D0907', 'border': '#2E2219', 'link': '#E0A458',
}

HOME, FRAGRANZE, LAB, BOTTEGA = '{{PAGINA:home}}', '{{PAGINA:le-fragranze}}', '{{PAGINA:il-laboratorio}}', '{{PAGINA:la-bottega}}'
MAIL, PHONE, PHONE_TEL = 'naso@sillage.example', '+39 055 000 2210', 'tel:+390550002210'
LAT, LNG = '43.7662', '11.2468'

V_AMBRA, V_FUMO, V_FILO, V_TEAL, V_ROSE, V_PIOGGIA, V_ANELLO = (
    'pexels-video-9368986', 'pexels-video-4320605', 'pexels-video-4320717', 'pexels-video-10004432', 'pexels-video-7675967',
    'pexels-video-7815723', 'pexels-video-7815727')
PHOTOS = [
    'pexels-7814724.jpg', 'pexels-8450515.jpg', 'pexels-8544957.jpg',
    'pexels-8450122.jpg', 'pexels-8450116.jpg', 'pexels-8450205.jpg', 'pexels-8450109.jpg', 'pexels-8450117.jpg',
    'pexels-8544939.jpg', 'pexels-9547307.jpg', 'pexels-6634312.jpg', 'pexels-6719774.jpg', 'pexels-6719771.jpg',
    'pexels-35963723.jpg', 'pexels-13834054.jpg', 'pexels-40561104.jpg', 'pexels-20138925.jpg', 'pexels-5856228.jpg',
    'pexels-30374517.jpg', 'pexels-11164337.jpg', 'pexels-6616435.jpg', 'pexels-35819765.jpg', 'pexels-35835724.jpg',
    'pexels-39172059.jpg', 'pexels-39692077.jpg', 'pexels-37387732.jpg',
    'pexels-12428350.jpg',
]
VIDEOS = [V_AMBRA, V_FUMO, V_FILO, V_TEAL, V_ROSE, V_PIOGGIA, V_ANELLO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Via dei Serragli 41r, Oltrarno · Firenze', BOTTEGA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]

FLACONE_SVG = ('<svg viewBox="0 0 220 260" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" '
               'stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">'
               '<rect x="86" y="18" width="48" height="30" rx="4"/><path d="M96 48v18M124 48v18"/>'
               '<path d="M62 66h96c10 0 16 6 16 16v132c0 14-10 24-24 24H70c-14 0-24-10-24-24V82c0-10 6-16 16-16z"/>'
               '<path d="M58 120h104"/><path d="M58 196h104"/><path d="M84 146c10-12 42-12 52 0"/>'
               '<path d="M92 162h36"/><path d="M100 174h20"/>'
               '<path d="M176 30c10 4 16 14 14 24M186 18c14 8 22 24 18 40M168 44c4 2 6 6 6 10"/></svg>')


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Sillage', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Sillage', 'Laboratorio di profumeria artistica in Oltrarno dal 2011. Dodici fragranze fatte a mano '
                   'in lotti da duecento flaconi, il profumo su misura in tre incontri.',
                   [('Le fragranze', FRAGRANZE), ('Il laboratorio', LAB), ('La bottega', BOTTEGA), ('Su misura', HOME + '#su-misura'),
                    ('Privacy', '/privacy-policy/')],
                   'Sillage', 'In francese è la scia che un profumo lascia dietro chi passa. È l’unica cosa che cerchiamo, '
                   'ogni volta.', VOCI, '© 2026 Sillage profumi artistici · Firenze · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')], marchio_size=150)


def apertura(t):
    m = K.mob
    note = [
        ('Note di testa · i primi quindici minuti', 'Prima arriva\nla luce.', 'Bergamotto di Reggio, pepe rosa, la buccia '
         'di un mandarino appena aperto. Durano poco: sono la promessa.', K.sfondo_video(V_FUMO, overlay=0.35),
         'pexels-35819765.jpg', 'Bergamotto e mandarino'),
        ('Note di cuore · le prime quattro ore', 'Poi si apre\nil fiore.', 'Rosa damascena, iris di Firenze, un filo di '
         'fumo di tè nero. È il carattere del profumo, quello che gli altri ricordano.', K.sfondo_video(V_ROSE, overlay=0.5),
         'pexels-6616435.jpg', 'Rosa damascena'),
        ('Note di fondo · fino a due giorni', 'E resta\nl’ambra.', 'Labdano, vaniglia, legno di oud. È ciò che rimane sul '
         'cappotto il giorno dopo: il sillage.', K.sfondo_video(V_AMBRA, overlay=0.4), 'pexels-35963723.jpg',
         'Resina di labdano'),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo, foto, didascalia) in enumerate(note):
        n = i + 1
        sl.append(K.diapositiva('si-%d' % n, sfondo, [
            K.livello('si-o%d' % n, contenuto=occ, x=6, y=24, w=44, size=12, peso='500', colore=C['acc'], ritardo=200,
                      letterSpacing=2, textTransform='uppercase', fontFamily='Red Hat Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 12, 'width': 88}}),
            K.livello('si-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=29, w=56, size=104, peso='400',
                      colore=C['chiaro'], ritardo=350, durata=1300, lineHeight=1.0, entra='fadeIn',
                      charAnim={'stagger': 35, 'offsetY': -18, 'blur': 14, 'opacity': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 28, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(44), 'y': 16, 'width': 88}}),
            K.livello('si-s%d' % n, contenuto=testo, x=6, y=64, w=34, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1200, lineHeight=1.6, responsive={'mobile': {'fontSize': m(15), 'y': 31, 'width': 88}}),
            K.livello('si-b%d' % n, tipo='button', contenuto='Componi il tuo', x=6, y=80, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=4, ritardo=1500, buttonUrl=HOME + '#su-misura', hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 47, 'fontSize': m(12)}}),
            K.livello('si-f%d' % n, tipo='image', imageSrc=K.img(foto), x=62, y=27, w=22, h=35, entra='zoomIn', ritardo=600,
                      durata=1400, objectFit='cover', borderRadius=999, borderWidth=1, borderColor=mix(C['acc'], 60),
                      animLoop='float', animLoopDuration=6000, parallaxDepth=6,
                      kenBurns={'type': 'zoomIn', 'intensity': 14, 'duration': 9000},
                      boxShadow={'x': 0, 'y': 30, 'blur': 70, 'spread': 0, 'color': mix(C['nero'], 70)},
                      responsive={'mobile': {'x': 22, 'y': 58, 'width': 56, 'height': 26}}),
            K.livello('si-d%d' % n, contenuto=didascalia, x=62, y=65, w=22, size=12, peso='500', colore=mix(C['chiaro'], 80),
                      align='center', entra='fadeIn', ritardo=1400, letterSpacing=2, textTransform='uppercase',
                      fontFamily='Red Hat Mono', responsive={'mobile': {'x': 6, 'y': 86, 'width': 88, 'fontSize': m(10)}}),
        ], durata=7500))
    piramide = []
    for k, (nome, y, w) in enumerate([('testa', 36, 6), ('cuore', 44, 9), ('fondo', 52, 12)]):
        piramide.append(K.livello('si-p%d' % k, tipo='button', contenuto=nome, x=94 - w / 2 - 2, y=y, w=w, size=11,
                                  peso='500', colore=C['chiaro'], bgColor=mix(C['acc'], 22), padding=8, align='center',
                                  letterSpacing=2, textTransform='uppercase', fontFamily='Red Hat Mono', borderRadius=4,
                                  entra='fadeIn', ritardo=600 + 150 * k, action={'type': 'goToSlide', 'target': k},
                                  hover={'bgColor': C['acc'], 'color': C['on_acc'], 'duration': 200},
                                  responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=piramide, transizione='stack', durata_trans=1200, showArrows=False, showDots=True,
                       parallax=True, parallaxIntensity=4)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    scia = t.classic('textmask', {
        'text': 'sillage', 'multiline': False, 'font_size': '260', 'font_size_tablet': '150', 'font_size_mobile': '84',
        'font_weight': '400', 'font_family': 'heading', 'text_transform': 'none', 'letter_spacing': '-4', 'line_height': '1',
        'text_align': 'center', 'video_url': K.img(V_FILO + '.mp4'), 'video_poster': K.img(V_FILO + '.jpg'),
        'video_opacity': '100', 'min_height': '440px', 'bg_color': C['nero'], 'mask_mode': 'blend', 'blend_mode': 'screen',
        'text_fill': C['acc'], 'overlay_opacity': '0', 'scroll_animate': False,
    }, style={})
    out.append(t.section([
        t.one([scia], anim=None),
        K.centered(t, [K.text(t, '«Sillage» è la scia che un profumo lascia dietro chi passa. Non la si vede, ma la si '
                                 'riconosce: è quello che cerchiamo in ogni fragranza.', size=18, align='center',
                              color=C['soft'], pad=(10, 0, 0, 0))]),
    ], bg=solid(C['nero']), padding='large', width='fullbleed', style={'padding_top': 90, 'padding_bottom': 120}))
    famiglie = t.classic('ghiera', {
        'voci': [{'sigla': s, 'titolo': ti, 'testo': tx, 'prezzo': pr, 'immagine': K.img(f), 'video': '',
                  'link': FRAGRANZE + '#' + a, 'testo_link': 'Le fragranze'} for s, ti, tx, pr, f, a in [
            ('A', 'Agrumata', 'Bergamotto, cedro, mandarino: le fragranze del mattino, fresche e brevi come un’estate.',
             '3 fragranze · da 95 €', 'pexels-35835724.jpg', 'agrumata'),
            ('F', 'Fiorita', 'Rosa, iris, gelsomino di notte. Le più amate, e le più difficili da fare bene.',
             '4 fragranze · da 110 €', 'pexels-30374517.jpg', 'fiorita'),
            ('L', 'Legnosa', 'Cedro dell’Atlante, vetiver, oud. Profumi lenti, per l’autunno e per la sera.',
             '2 fragranze · da 130 €', 'pexels-6634312.jpg', 'legnosa'),
            ('B', 'Ambrata', 'Labdano, vaniglia, benzoino: calde, avvolgenti, restano sulla sciarpa per giorni.',
             '2 fragranze · da 140 €', 'pexels-35963723.jpg', 'ambrata'),
            ('M', 'Fumé', 'Tè nero affumicato, incenso, betulla. Una sola fragranza, la più nostra.',
             '1 fragranza · 150 €', 'pexels-6719771.jpg', 'fume')]],
        'partenza': 1, 'titolo_centro': 'Famiglie', 'mostra_numero': True, 'etichetta': 'Gira la ghiera e scegli la famiglia',
        'stile': 'metallo', 'colore_ghiera': C['sala'], 'colore_sigle': C['chiaro'], 'colore_indice': C['acc'],
        'sfondo_pannello': '', 'colore_pulsante': C['acc'], 'colore_testo_pulsante': C['on_acc'], 'titolo_color': C['testo'],
        'testo_color': C['tenue'], 'prezzo_color': C['acc'], 'misura': 420, 'proporzione': '4/5', 'disposizione': 'lato',
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Le famiglie', ['Gira,', 'e annusa con gli occhi.'], size=64,
                         tagline='Cinque famiglie olfattive, dodici fragranze. Ognuna ha il suo lotto, il suo numero, la sua '
                                 'data scritta a mano sull’etichetta.')]),
        t.one([famiglie]),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 120}))
    misura = t.classic('lookbookmixer', {
        'items': [{'step': st, 'name': n, 'price': p, 'color': col} for st, n, p, col in [
            ('Testa', 'Bergamotto di Reggio', '20', C['acc2']), ('Testa', 'Pepe rosa', '24', C['acc2']),
            ('Testa', 'Mandarino verde', '18', C['acc2']),
            ('Cuore', 'Rosa damascena', '48', C['sec']), ('Cuore', 'Iris di Firenze', '62', C['sec']),
            ('Cuore', 'Gelsomino di notte', '40', C['sec']),
            ('Fondo', 'Ambra e labdano', '36', C['acc']), ('Fondo', 'Legno di oud', '70', C['acc']),
            ('Fondo', 'Vaniglia del Madagascar', '30', C['acc']),
            ('Formato', 'Flacone da 30 ml', '40', C['sala']), ('Formato', 'Flacone da 50 ml', '60', C['sala']),
            ('Formato', 'Flacone da 100 ml', '95', C['sala'])]],
        'currency': '€', 'card_title': 'Il tuo profumo', 'card_steps_label': 'note',
        'card_sub': 'Cambia ogni nota con le frecce: il prezzo si aggiorna da solo. Poi lo proviamo insieme, in bottega.',
        'cta_text': 'Prenota la prova', 'cta_url': BOTTEGA + '#scrivici', 'panel_bg': C['sala'],
        'slot_bg': mix(C['acc'], 10, C['sala']), 'accent': C['acc'], 'accent_ink': C['on_acc'], 'name_color': C['testo'],
        'price_color': mix(C['testo'], 85), 'line_color': C['filo'], 'name_font_family': 'heading',
        'mono_font_family': 'Red Hat Mono', 'show_swatch': True,
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Su misura', ['Il tuo profumo,', 'nota per nota.'], size=60, layout='stack',
                                size_mobile=38),
                      K.text(t, 'Scegli una nota di testa, una di cuore, una di fondo e il formato. In bottega la proviamo '
                                'sulla pelle e la correggiamo insieme: tre incontri, poi il flacone col tuo nome.'),
                      K.specs(t, [('Gli incontri', 'tre, di un’ora'), ('I tempi', 'tre settimane'),
                                  ('La formula', 'resta tua, la rifacciamo quando vuoi')])], wm='2-5', ws='1-1'),
               t.col([misura], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='su-misura',
        style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_PIOGGIA, 'In bottega', 'Un profumo si sceglie <em>sulla pelle.</em>',
                             'Sulla carta mente: cambia col calore, con l’ora, con chi lo porta. Per questo in bottega ti '
                             'facciamo provare, e ti mandiamo a casa con tre campioni.', overlay=55,
                             extra=[K.button(t, 'Vieni in bottega', BOTTEGA, kind='pieno', align='center', pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Sillage',
                page_intro_caption='la scia di chi passa', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_fragranze():
    t = Tpl(ID, 'le-fragranze')
    out = [K.intro(t, 'Le fragranze', ['Dodici,', 'in lotti da duecento.'],
                   'Ogni fragranza si fa in lotti piccoli, a mano, e matura tre mesi in cantina prima di andare nel '
                   'flacone. Sull’etichetta: il lotto, la data, la firma.', bg=K.video_bg(V_TEAL, overlay=55),
                   overlay_text=True)]
    carosello = t.classic('overlayslider', {
        'slides': [{'image': K.img(f), 'title': ti, 'subtitle': su, 'link': BOTTEGA + '#scrivici', 'ribbon': rb}
                   for f, ti, su, rb in [
                       ('pexels-35963723.jpg', 'Notte a Bellosguardo', 'Ambrata · 50 ml · 140 €', 'la più venduta'),
                       ('pexels-5856228.jpg', 'Iris Serragli', 'Fiorita · 50 ml · 120 €', ''),
                       ('pexels-6719771.jpg', 'Tè di Boboli', 'Fumé · 50 ml · 150 €', 'nuova'),
                       ('pexels-35835724.jpg', 'Bergamotto 1911', 'Agrumata · 50 ml · 95 €', ''),
                       ('pexels-6634312.jpg', 'Cedro del Forte', 'Legnosa · 50 ml · 130 €', ''),
                       ('pexels-30374517.jpg', 'Rosa d’Oltrarno', 'Fiorita · 50 ml · 110 €', '')]],
        'columns': '3', 'gap': 'medium', 'image_ratio': '4/5', 'image_height': '520', 'height': '520', 'image_fit': 'cover',
        'overlay_position': 'bottom', 'overlay_horizontal': 'left', 'overlay_style': 'overlay-default',
        'overlay_padding': 'medium', 'title_size': 'h3', 'hover_effect': 'zoom', 'hover_overlay': 'always',
        'show_arrows': True, 'show_dots': True, 'ribbon_position': 'top-right', 'ribbon_bg': C['acc'],
        'ribbon_color': C['on_acc'], 'shadow': 'none', 'slide_radius': 4, 'overlay_color': mix(C['nero'], 85),
        'overlay_gradient': True, 'title_color': C['chiaro'], 'title_weight': '400', 'subtitle_color': mix(C['chiaro'], 82),
        'subtitle_size': 14,
    }, style={})
    out.append(t.section([t.one([carosello])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    for aid, foto, nome, fam, testo, dati in [
        ('ambrata', 'pexels-7814724.jpg', 'Notte a Bellosguardo', 'Ambrata', 'La collina sopra Firenze a mezzanotte: '
         'labdano, vaniglia, una nota di fieno tagliato. Il nostro primo profumo, nel 2011.',
         [('Testa', 'mandarino, pepe rosa'), ('Cuore', 'fieno, tabacco biondo'), ('Fondo', 'labdano, vaniglia'),
          ('Durata', 'oltre 24 ore')]),
        ('fume', 'pexels-6719774.jpg', 'Tè di Boboli', 'Fumé', 'Il tè nero affumicato bevuto sotto la pioggia, al '
         'giardino. Incenso, betulla, un filo di cuoio.',
         [('Testa', 'bergamotto, cardamomo'), ('Cuore', 'tè lapsang, incenso'), ('Fondo', 'betulla, cuoio'),
          ('Durata', 'oltre 12 ore')]),
    ]:
        out.append(t.section([
            t.row([t.col([K.image(t, foto, 'Il flacone di ' + nome, ratio='4/5', tilt=True)], wm='2-5', ws='1-1'),
                   t.col([K.label(t, fam, color=C['acc']), K.headline(t, nome, size=60, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati), K.button(t, 'Provala in bottega', BOTTEGA + '#scrivici')],
                         wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if aid == 'fume' else None,
            style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'di scegliere.', [
        ('Quanto dura un profumo?', 'Un eau de parfum come i nostri dura dalle otto ore in su. Gli ambrati anche due giorni '
                                   'sul cappotto.'),
        ('Posso avere un campione?', 'Sì: tre campioni da 2 ml a 12 €, che scaliamo dal flacone se torni a prenderlo.'),
        ('Spedite?', 'In tutta Italia in 48 ore, con la carta velina e il sigillo di ceralacca.'),
        ('Sono testati sugli animali?', 'Mai. Le materie prime sono naturali dove si può, e lo scriviamo in etichetta.'),
    ], 'fr', bg=False))
    return out


def build_laboratorio():
    t = Tpl(ID, 'il-laboratorio')
    out = [K.intro(t, 'Il laboratorio', ['Duecento', 'bottigliette marroni.'],
                   'Il laboratorio è dietro la bottega: un organo di profumiere con duecento essenze, la bilancia al '
                   'centesimo di grammo e la cantina dove i profumi maturano al buio.',
                   bg=K.photo_bg('pexels-8450515.jpg', overlay=55), overlay_text=True)]
    flacone = t.classic('svganimator', {
        'source_type': 'code', 'svg_code': FLACONE_SVG, 'anim_type': 'draw', 'anim_sequence': 'one-by-one',
        'trigger': 'viewport', 'duration': 3200, 'stroke_color': C['acc'], 'stroke_linecap': 'round', 'show_fill': False,
        'loop': False, 'replay_button': False, 'max_width': '260px', 'alignment': 'center',
    }, style={})
    out.append(t.section([
        t.row([t.col([flacone], wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Il naso', color=C['acc']), K.headline(t, 'Arturo Pieralli, <em>quarant’anni di essenze.</em>',
                                                                       size=52, tag='h2', pad_bottom=14),
                      K.text(t, 'Ha cominciato a Grasse nel 1984, è tornato a Firenze per aprire Sillage con la figlia '
                                'Bianca. Ogni fragranza parte da un ricordo scritto su un quaderno: la prima prova è sempre '
                                'su una striscia di carta, l’ultima sulla pelle.'),
                      K.specs(t, [('Le essenze', '210, molte da piccoli produttori'), ('Le prove', 'da 40 a 200 per profumo'),
                                  ('La maturazione', 'tre mesi in cantina, al buio')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8450122.jpg', 'Il naso dosa un’essenza col contagocce', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-8544957.jpg', 'Provette di essenze colorate su fondo nero', ratio='4/5')],
                     wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-8450205.jpg', 'Le cartine per provare le essenze', ratio='4/5')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=16),
    ], padding='large', style={'padding_top': 20, 'padding_bottom': 100}))
    out.append(K.pannello(t, V_ANELLO, 'La cantina', 'Tre mesi al buio.', 'Il profumo appena fatto è spigoloso: le note '
                          'non si conoscono ancora. In cantina, a quattordici gradi, imparano a stare insieme.', 'Le fragranze',
                          FRAGRANZE, overlay=50, video=True))
    out.append(K.video_frase(t, V_FUMO, 'Il laboratorio aperto', 'Il sabato mattina <em>si annusa insieme.</em>',
                             'Due ore all’organo del profumiere, a riconoscere le essenze a occhi chiusi. Sei posti, 45 euro, '
                             'un caffè e una striscia di carta da portare a casa.', overlay=50,
                             extra=[K.button(t, 'Prenota il sabato', BOTTEGA + '#scrivici', kind='pieno', align='center',
                                             pad_top=18)]))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['In Oltrarno,', 'fino alle otto.'],
                   'Una vetrina in via dei Serragli, gli scaffali di noce con i flaconi in fila e il bancone dove si prova. '
                   'Aperta dal martedì al sabato, dalle 10 alle 20.', bg=K.photo_bg('pexels-37387732.jpg', overlay=60),
                   overlay_text=True)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Cosa vuoi fare', 'cosa', True,
                     opzioni='Provare le fragranze\nIl profumo su misura\nIl laboratorio del sabato\nUn regalo'),
             K.campo('date', 'Il giorno', 'giorno'), K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1',
                                                            segnaposto='Un profumo che ti è piaciuto, un ricordo, una persona da sorprendere…')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Un appuntamento', ['Ti teniamo', 'il bancone libero.'],
                                'Per provare basta passare. Per il su misura e il sabato serve un appuntamento: ti scriviamo '
                                'entro un giorno.', VOCI, campi, 'Sillage — appuntamento', 'Chiedi l’appuntamento',
                                'Ricevuto. Ti scriviamo entro domani con il giorno e l’ora.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Oltrarno,', 'via dei Serragli.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via dei Serragli 41r, Firenze'), ('Orari', 'martedì — sabato, 10 — 20'),
                                  ('A piedi', 'dieci minuti da Ponte Vecchio'), ('Bus', 'D, fermata Serragli')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Sillage · profumi artistici', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · nessuna scia', color=C['acc'], align='center'),
                       K.headline(t, 'Qui il profumo <em>è svanito.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Segui la scia fino all’inizio: lì c’è ancora.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_FILO, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Testa, cuore, fondo',
    ['profumeria', 'profumi artistici', 'firenze', 'fragranze', 'su misura', 'bottega', 'scuro', 'slider pro'],
    'Un laboratorio di profumeria artistica a Firenze, nero tabacco e ambra: l’apertura sulla piramide olfattiva col fumo, '
    'le rose e l’ambra versata, la piramide da toccare, la parola col fumo dentro le lettere, le famiglie da scegliere '
    'girando una ghiera, il profumo su misura da comporre nota per nota col prezzo e il flacone disegnato a tratto.',
    [('Le fragranze', FRAGRANZE), ('Il laboratorio', LAB), ('Su misura', HOME + '#su-misura'), ('La bottega', BOTTEGA)],
    [('home', 'Home', None), ('le-fragranze', 'Le fragranze', 'le-fragranze'),
     ('il-laboratorio', 'Il laboratorio', 'il-laboratorio'), ('la-bottega', 'La bottega', 'la-bottega')],
    HEX, '\'Figtree\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Gloock\', \'Didot\', Georgia, serif', '\'Red Hat Mono\', ui-monospace, Menlo, monospace',
    ['Gloock', 'Figtree', 'Red Hat Mono'], corsivo_di=[], peso_titoli='400', raggio=4, grana=5, titoli_line='1.02',
    titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-fragranze.json': build_fragranze(), 'il-laboratorio.json': build_laboratorio(),
                        'la-bottega.json': build_bottega(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
