# -*- coding: utf-8 -*-
"""Genera il tema «Stender» (id stender): un camiciaio di Milano che vende le camicie appese, mai piegate.

La trovata: «la stender». In apertura le lettere del nome stanno appese a una stender, ognuna al suo gancio, sopra il video
delle grucce; scorrendo scivolano via lungo la sbarra una dopo l'altra, dondolando, e lasciano la frase del marchio. Più
giù la collezione è una stender vera: un nastro orizzontale (ScrollScrub) con le camicie appese che dondolano con la
velocità dello scorrimento.

Tema chiaro: bianco camicia, blu oxford e il rosso dell'asola. Titoli in DM Serif Display, testo in DM Sans, dati in DM Mono.
Logo: la gruccia e il nome. Cursore: gruccia (dondola).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'stender'
NAME = 'Stender'

HEX = {
    'primary': '#1F3FA0', 'primary_contrast': '#FFFFFF', 'secondary': '#9DB1D6', 'secondary_contrast': '#1C2230',
    'accent': '#B5452F', 'dark': '#10141F', 'light': '#FFFFFF', 'muted': '#E8E4DA', 'muted_contrast': '#1C2230',
    'text': '#1C2230', 'text_muted': '#555D6E', 'background': '#F3F1EC', 'border': '#D6D1C6', 'link': '#1F3FA0',
}
CROMO = '#DADDE3'

HOME, CAPI, MISURA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:su-misura}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'bottega@stender.example', '+39 02 0000 4412', 'tel:+390200004412'
LAT, LNG = '45.4640', '9.1780'

V_GRUCCE, V_STENDER, V_BIANCHE, V_VENTO, V_DONNA, V_SPIAGGIA, V_BOTTONI, V_COLLO, V_STIRO = (
    'pexels-video-7764692', 'pexels-video-8890976', 'pexels-video-9218096', 'pexels-video-9309690', 'pexels-video-8055355',
    'pexels-video-10143252', 'pexels-video-7648782', 'pexels-video-6481813', 'pexels-video-10558375')
VIDEOS = [V_GRUCCE, V_STENDER, V_BIANCHE, V_VENTO, V_DONNA, V_SPIAGGIA, V_BOTTONI, V_COLLO, V_STIRO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
GRUC = f('8306388', '8483487', '7989576', '1682699', '3735641', '7755387', '40450765')
BIAN = f('1304647', '18397428', '17858294', '9775825', '27302850', '7665789', '18119612')
RIGH = f('12871418', '36457394', '19544237', '30696578', '30479413', '3967060', '37229881')
LINO = f('22441297', '22441291', '17281873', '37166938', '37166939', '37166937', '17492498')
COLL = f('6276002', '12629399', '6843227', '6968322', '3023669', '17919906')
APPE = f('18725622', '9594088', '7679827', '28905704', '12708059')
PIEG = f('4440566', '4440572', '4440574', '8346226')
SART = f('6766375', '5264949', '26608702', '6765654')
DONN = f('9558588', '3754181', '18786589', '6389359', '8796462')
STEN = f('5490975', '5531540', '5531542', '13532891', '7679742')
LOGO_S, LOGO_C = 'logo-stender-scuro.png', 'logo-stender-chiaro.png'
PHOTOS = GRUC + BIAN + RIGH + LINO + COLL + APPE + PIEG + SART + DONN + STEN
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media=HEX['secondary'])
C = K.c
MONO = 'DM Mono'
VOCI = [('map-pin', 'Via Vigevano 18 · Milano', NEGOZI + '#bottega'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Stender', LOGO_S, 168, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '1020/232', 'Stender cuce camicie a Milano e le vende appese, mai piegate: popeline, oxford, '
                   'lino e flanella, il colletto montato a mano.',
                   [('La collezione', CAPI), ('Su misura', MISURA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Via Vigevano 18, Milano<br>Martedì — sabato 10 — 19', VOCI,
                   '© 2026 Stender · Milano · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')],
                   larghezza=400, fondo=C['sala'])


# ── L'apertura: le lettere appese alla stender ──
def apertura(t):
    m = K.mob
    lettere = 'STENDER'
    livelli = K.veli('st-velo', 'alto', 46, 80, colore=C['nero'], forza=70, mobile_alto=34) + [
        K.livello('st-sbarra', tipo='shape', x=0, y=12, w=100, h=0.55, bgColor=CROMO, borderRadius=4, entra='fadeIn', ritardo=0,
                  durata=500, customCSS='box-shadow:0 2px 6px rgba(0,0,0,.35);',
                  responsive={'mobile': {'x': 0, 'y': 7, 'width': 100, 'height': 0.3}}),
    ]
    for i, ch in enumerate(lettere):
        x, xm = 7 + i * 12.4, 3 + i * 13.6
        y, h, ym, hm = 24, 30, 11, 9
        via = 4 + (len(lettere) - 1 - i) * 7.5
        # le lettere vanno via verso destra, l'ultima per prima, dondolando mentre corrono
        mossa = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0, 'r': 0}, {'p': via, 'x': 0, 'r': 0},
                                                     {'p': via + 8, 'x': 260, 'r': -11}, {'p': via + 16, 'x': 900, 'r': 7},
                                                     {'p': via + 24, 'x': 1700, 'r': -3}, {'p': 100, 'x': 1700, 'r': 0}]}
        gancio_y, gancio_ym = 12.2, 7.2
        livelli.append(K.livello('st-g%d' % i, tipo='shape', x=x + 4.9, y=gancio_y, w=0.18, h=y - gancio_y + 2, bgColor=CROMO,
                                 entra='fadeInDown', ritardo=150 + i * 70, durata=600, customCSS='transform-origin:50% 0%;',
                                 scrollMove=mossa, responsive={'mobile': {'x': xm + 5.6, 'y': gancio_ym, 'width': 0.4,
                                                                          'height': ym - gancio_ym + 1}}))
        livelli.append(K.livello('st-l%d' % i, contenuto=ch, tag='span', x=x, y=y, w=10, h=h, size=190, peso='400', colore='#FFFFFF', fontFamily='DM Serif Display',
                                 align='center', lineHeight=1.0, entra='fadeInDown', ritardo=250 + i * 70, durata=800,
                                 customCSS='transform-origin:50%% %.0f%%;text-shadow:0 4px 18px rgba(0,0,0,.35);' % (-(y - gancio_y) / h * 100),
                                 scrollMove=mossa, responsive={'mobile': {'fontSize': m(54), 'x': xm, 'y': ym, 'width': 12,
                                                                          'height': hm}}))
    livelli += [
        K.livello('st-o', contenuto='Stender · camicie · Milano', x=7, y=62, w=40, size=13, peso='500', colore=mix('#FFFFFF', 88),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=900,
                  scrollMove={'inizio': 0, 'fine': 30, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 22, 'width': 88}}),
        K.livello('st-s', contenuto='Scorrete: la stender si svuota.', x=7, y=67, w=40, size=18, peso='400', colore=mix('#FFFFFF', 92),
                  ritardo=1100, scrollMove={'inizio': 0, 'fine': 30, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 25, 'width': 88}}),
    ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 70, 'o': 0}, {'p': 86, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('st-f1', contenuto='Camicie appese,', tag='h1', x=7, y=22, w=70, size=118, peso='400', colore='#FFFFFF', lineHeight=1.0,
                  scrollMove=fine, entra='fadeIn', ritardo=0, responsive={'mobile': {'fontSize': m(44), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('st-f2', contenuto='mai piegate.', tag='p', x=7, y=38, w=70, size=118, peso='400', fontStyle='italic', fontFamily='DM Serif Display', colore=C['sec'],
                  lineHeight=1.0, scrollMove=fine, entra='fadeIn', ritardo=0,
                  responsive={'mobile': {'fontSize': m(44), 'x': 6, 'y': 15.5, 'width': 88}}),
        K.livello('st-b', tipo='button', contenuto='La collezione', x=7, y=58, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=0, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 21, 'fontSize': m(11)}}),
        K.livello('st-l', tipo='button', contenuto='Su misura', x=21, y=58, w='auto', size=12, peso='600', colore='#FFFFFF',
                  bgColor='transparent', padding=16, borderRadius=0, borderWidth=1, borderColor=mix('#FFFFFF', 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=MISURA, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix('#FFFFFF', 14), 'borderColor': '#FFFFFF', 'duration': 250},
                  responsive={'tablet': {'x': 26}, 'mobile': {'x': 55, 'y': 21, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    sl = [K.diapositiva('st-1', K.sfondo_video(V_STENDER, overlay=0.12, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def stender(t, voci, titolo, occhiello):
    """Il nastro orizzontale delle camicie appese, che dondolano scorrendo: voci = [(foto, nome, tessuto, testo)]."""
    return t.classic('scrollscrub', {
        'behavior': 'pin', 'scroll_length': 3, 'align': 'center', 'gap': 28, 'swing': True, 'show_progress': True,
        'arrows': 'none', 'indicators': 'linea', 'indicators_color': C['tenue'], 'indicators_active_color': C['acc'],
        'edge_fade': True, 'drag_scroll': True, 'heading': titolo, 'kicker': occhiello, 'heading_color': C['testo'],
        'kicker_color': C['acc'], 'heading_size': 56, 'heading_font': 'heading', 'item_width': 300, 'item_min_height': 470,
        'round': 0, 'item_padding': {'top': 18, 'right': 4, 'bottom': 8, 'left': 4}, 'item_bg_default': 'transparent',
        'text_color_default': C['testo'], 'progress_color': C['acc'], 'show_number': False, 'shadow': 'none',
        'items': [{'title': n, 'subtitle': s, 'text': tx, 'media': K.img(fo), 'media_label': 'Immagine', 'color': '', 'text_color': ''}
                  for fo, n, s, tx in voci]}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Stender · Milano, via Vigevano 18', ['Una camicia', 'non si piega.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Piegata in una scatola una camicia prende le pieghe della scatola. Le nostre restano appese '
                                'dalla bottega al negozio, sulla loro gruccia di faggio, e arrivano a voi come escono dal ferro: '
                                'il collo dritto, le maniche lunghe, nessuna riga sul petto.', size=17),
                      K.button(t, 'Su misura', MISURA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 90}))
    out.append(t.section([t.one([stender(t, [
        (BIAN[0], 'Popeline', 'Bianca · 120 €', 'Il cotone fitto e liscio: la camicia del lunedì.'),
        (RIGH[2], 'Bastone', 'A righe · 130 €', 'Righe blu larghe un dito, il collo morbido.'),
        (LINO[0], 'Coreana', 'Lino · 140 €', 'Il collo alla coreana, il lino che respira.'),
        (BIAN[3], 'Oxford', 'Azzurra · 125 €', 'La trama a cesto, il bottone sotto le punte.'),
        (RIGH[4], 'Marina', 'Righe larghe · 135 €', 'Bianca e blu, da portare fuori dai pantaloni.'),
        (LINO[4], 'Spiaggia', 'Lino lavato · 145 €', 'Larga, senza colletto, per l’estate.'),
        (DONN[3], 'Grande', 'Oversize · 150 €', 'La camicia dell’uomo, tagliata per una donna.'),
        (BIAN[5], 'Sera', 'Popeline fine · 160 €', 'Il collo alto e i polsini doppi.')],
        'La stender', 'Scorrete: le camicie dondolano')], anim=None)],
        padding='remove-vertical', width='fullbleed'))
    out.append(t.section([
        t.one([K.sechead(t, 'I tessuti', ['Quattro cotoni,', 'un lino.'], size=60)]),
        t.one([P.elenco_hover(K, t, [
            ('Popeline', 'cotone egiziano · 120 fili', 'Fitto, liscio, lucido appena: la camicia bianca per eccellenza.', BIAN[2]),
            ('Oxford', 'cotone a cesto', 'Più spesso, opaco, si stira poco e invecchia bene.', COLL[3]),
            ('Lino', 'lino irlandese', 'Si stropiccia, ed è lì il bello: d’estate non si sente addosso.', LINO[2]),
            ('Flanella', 'cotone garzato', 'Morbido come una coperta: la camicia di novembre.', PIEG[0])], CAPI, size=56)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Appese, mai piegate', 'Collo montato a mano', 'Gruccia di faggio',
                                                 'Bottoni di madreperla'], fondo=C['acc'], colore='#FFFFFF', size=56,
                                          verso='right')], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro camicie', 'da cominciare.'], size=60)]),
        t.one([M.capi(K, t, [
            (BIAN[1], 'Popeline', 'Bianca', '', 'Il collo francese, i polsini da gemelli.', '120 €', 'nuova'),
            (RIGH[0], 'Oxford', 'Bastone', '', 'Righe larghe, il collo button down.', '130 €', ''),
            (LINO[5], 'Lino', 'Coreana', '', 'Il collo alla coreana, lino lavato.', '140 €', ''),
            (DONN[1], 'Popeline', 'Grande', '', 'Oversize, da portare aperta.', '150 €', '')], CAPI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_BOTTONI, 'Il bottone', 'Abbottonata <em>a mano.</em>',
                             'Ogni asola si cuce a mano col filo rosso: è il nostro segno, dentro il colletto.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Addosso', ['Appese a voi,', 'per strada.'], size=60)]),
        P.fila_foto(K, t, [(DONN[0], 'La Grande, portata aperta'), (RIGH[5], 'La Bastone col maglione'),
                           (LINO[6], 'La Coreana d’estate'), (BIAN[6], 'La Popeline bianca')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Una camicia nuova', 'ogni mese.'],
                         'I tessuti che arrivano, le camicie che escono, i negozi dove appenderle. Una volta al mese.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Dodici camicie,', 'tutte appese.'],
                   'Popeline, oxford, lino e flanella. Il collo montato a mano, i bottoni di madreperla, l’asola rossa dentro '
                   'il colletto. I prezzi sono quelli consigliati.', bg=K.video_bg(V_BIANCHE, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Bianche e azzurre', ['Le camicie', 'di tutti i giorni.'], size=60)]),
        t.one([M.capi(K, t, [
            (BIAN[1], 'Popeline', 'Bianca', '', 'Il collo francese.', '120 €', 'nuova'),
            (BIAN[3], 'Oxford', 'Azzurra', '', 'La trama a cesto.', '125 €', ''),
            (BIAN[5], 'Popeline', 'Sera', '', 'I polsini doppi.', '160 €', ''),
            (COLL[1], 'Popeline', 'Collo alto', '', 'Il collo da cravatta.', '135 €', '')], NEGOZI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([t.one([stender(t, [
        (RIGH[2], 'Bastone', 'Righe blu · 130 €', 'Righe larghe un dito.'),
        (RIGH[4], 'Marina', 'Bianca e blu · 135 €', 'Da portare fuori.'),
        (RIGH[3], 'Millerighe', 'Righe fini · 130 €', 'Quasi un azzurro.'),
        (RIGH[6], 'Notte', 'Righe nere · 140 €', 'Per la sera.'),
        (RIGH[1], 'Tenda', 'Righe colorate · 135 €', 'Come una tenda da spiaggia.'),
        (RIGH[5], 'Pigiama', 'Righe sottili · 130 €', 'Morbida, la si dorme anche.')],
        'Le righe', 'Scorrete la stender')], anim=None)], padding='remove-vertical', width='fullbleed', bg=solid(C['sala'])))
    out.append(t.section([
        t.one([K.sechead(t, 'Lino e flanella', ['Le camicie', 'di stagione.'], size=60)]),
        t.one([M.capi(K, t, [
            (LINO[0], 'Lino', 'Coreana', '', 'Il collo alla coreana.', '140 €', ''),
            (LINO[4], 'Lino', 'Spiaggia', '', 'Larga, senza colletto.', '145 €', ''),
            (PIEG[1], 'Flanella', 'Bosco', '', 'Garzata, a quadri.', '150 €', ''),
            (PIEG[2], 'Flanella', 'Neve', '', 'Bianca, morbida.', '150 €', 'ultime')], NEGOZI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_sartoria():
    t = Tpl(ID, 'su-misura')
    out = [K.intro(t, 'Su misura', ['La vostra camicia,', 'sulla vostra gruccia.'],
                   'Prendiamo diciotto misure, scegliete il tessuto, il collo e i polsini: in tre settimane la camicia arriva '
                   'appesa, col vostro nome sulla gruccia.', bg=K.video_bg(V_STIRO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si fa', ['Tre incontri,', 'una camicia.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'le misure', SART[0], 'Misurare', 'Diciotto', 'misure', 'Collo, spalle, polsi, la schiena dritta o curva: '
             'si prende tutto, anche come portate l’orologio.', '18', 'misure'),
            ('2', 'la prova', SART[1], 'Provare', 'La prova', 'in tela', 'Una prima camicia di tela bianca per vedere dove tira.',
             '1', 'prova'),
            ('3', 'la gruccia', APPE[0], 'Appendere', 'La camicia', 'sulla gruccia', 'Arriva appesa, col vostro nome inciso sulla '
             'gruccia di faggio.', '3', 'settimane')], icona='ruler')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi cuce', 'i colletti.'], size=60)]),
        K.squadra(t, [(SART[3], 'Ernesto Valli', 'Camiciaio', 'Monta i colletti a mano da quarant’anni, e li riconosce da lontano.',
                       'center 25%'),
                      (DONN[2], 'Sara Okafor', 'Modellista', 'Disegna le camicie da donna, larghe dove serve.', 'center 25%'),
                      (BIAN[4], 'Luca Meda', 'Stiro', 'Stira ogni camicia prima di appenderla.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, GRUC[3], 'Le camicie appese in bottega', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'La mia camicia è arrivata appesa, in una custodia di cotone, col mio nome sulla gruccia. '
                                        'Ho tenuto la gruccia più della camicia.', 'Giorgio, Bergamo', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove trovarle', 'appese.'],
                   'In ogni negozio le nostre camicie stanno sulla loro stender, mai piegate in uno scaffale.',
                   bg=K.photo_bg(STEN[1]), size=96)]
    out.append(M.negozi(K, t, [
        ('Milano', 'Bottega Stender', 'via Vigevano 18'), ('Milano', 'Colletto', 'corso Magenta 40'),
        ('Torino', 'Polsino', 'via Lagrange 9'), ('Genova', 'Asola', 'via Luccoli 22'), ('Bologna', 'Gruccia', 'via Farini 6'),
        ('Roma', 'Popeline', 'via Margutta 14'), ('Parigi', 'Le Cintre', 'rue de Charonne 31'), ('Tokyo', 'Hangā', 'Daikanyama 3-1')],
        nota='In bottega si possono portare le camicie da accorciare o da rifare il collo, anche non nostre.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una camicia su misura\nUn collo da rifare\nVendere Stender\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una camicia', 'su misura?'],
                                'Per un appuntamento, un collo da rifare, o per venderci nel vostro negozio.', VOCI, campi,
                                'Stender — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['Sui Navigli,', 'al pianterreno.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('Su misura', 'su appuntamento'),
                                  ('A piedi', 'dalla Darsena, quattro minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Stender · la bottega', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='bottega', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sulle camicie.'], [
        ('Perché appese?', 'Perché una camicia piegata prende le pieghe della scatola, e non le perde più del tutto.'),
        ('Come si lavano?', 'A trenta gradi, e appese ad asciugare sulla loro gruccia: si stirano quasi da sole.'),
        ('Fate le maniche corte?', 'Sì, d’estate, in lino e in popeline.'),
        ('Spedite?', 'Sì, appese in una scatola alta: arrivano senza una piega.'),
    ], 'st-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · gruccia vuota', color=C['sec'], align='center'),
                       K.headline(t, 'Qui la gruccia <em>è vuota.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento=C['sec']),
                       K.text(t, 'La camicia che cercavate l’ha presa qualcun altro. Torniamo alla stender.', size=17,
                              align='center', color=mix('#FFFFFF', 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_GRUCCE, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La stender che dondola',
    ['moda', 'abbigliamento', 'camicie', 'milano', 'chiaro', 'slider pro', 'scorrimento orizzontale'],
    'Un camiciaio che vende le camicie appese, mai piegate: in apertura le lettere del nome stanno appese a una stender e '
    'scorrendo scivolano via una dopo l’altra dondolando; poi la collezione su un nastro orizzontale dove le camicie '
    'dondolano con lo scorrimento, i tessuti, il su misura e i negozi.',
    [('La collezione', CAPI), ('Su misura', MISURA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('su-misura', 'Su misura', 'su-misura'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'DM Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'DM Serif Display\', Georgia, serif',
    '\'DM Mono\', ui-monospace, Menlo, monospace', ['DM Sans', 'DM Serif Display', 'DM Mono'], corsivo_di=['DM Serif Display'],
    peso_titoli='400', raggio=0, grana=3, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'gruccia'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['accent']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'su-misura.json': build_sartoria(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
