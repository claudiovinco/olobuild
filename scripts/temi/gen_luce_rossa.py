# -*- coding: utf-8 -*-
"""Genera il tema «Luce Rossa» (id luce-rossa): una stamperia di Trieste che fa foulard di seta stampati da fotografie,
sviluppate in camera oscura prima di andare sulla seta.

La trovata: «la camera oscura». Tutto il sito sta sotto la luce rossa di sicurezza (Luce di pagina) e in apertura la
stampa si sviluppa scorrendo: sulla carta bianca, nella vasca, l'immagine affiora mentre il timer passa da rivelatore ad
arresto a fissaggio. Più giù lo stesso foulard «sviluppato» con pellicole diverse (Pellicole) e le collezioni scelte
girando la ghiera di una macchina fotografica (Ghiera).

Tema scuro: nero, rosso di sicurezza e rosa carne. Titoli in Cormorant Garamond corsivo, testo in Karla, dati in
Overpass Mono. Logo: la lampadina rossa e il nome. Cursore: lanterna (la luce rossa che segue la mano).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'luce-rossa'
NAME = 'Luce Rossa'

HEX = {
    'primary': '#E5483D', 'primary_contrast': '#120808', 'secondary': '#B88A80', 'secondary_contrast': '#120808',
    'accent': '#FFB199', 'dark': '#0A0404', 'light': '#FFFFFF', 'muted': '#1E1010', 'muted_contrast': '#F2E4E1',
    'text': '#F2E4E1', 'text_muted': '#C7AAA5', 'background': '#120808', 'border': '#3A2220', 'link': '#FF6B5E',
}

HOME, CAPI, OSCURA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:camera-oscura}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'stampa@lucerossa.example', '+39 040 000 6631', 'tel:+390400006631'
LAT, LNG = '45.6500', '13.7680'

V_VASCA, V_FOGLIO, V_PINZA, V_LAMPADA, V_TAVOLO, V_SPIAGGIA, V_VENTO, V_MARE, V_RULLINO = (
    'pexels-video-10276470', 'pexels-video-10276359', 'pexels-video-10276363', 'pexels-video-10276176', 'pexels-video-10273773',
    'pexels-video-8995420', 'pexels-video-10559384', 'pexels-video-19047961', 'pexels-video-29258894')
VIDEOS = [V_VASCA, V_FOGLIO, V_PINZA, V_LAMPADA, V_TAVOLO, V_SPIAGGIA, V_VENTO, V_MARE, V_RULLINO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
FOUL = f('36455711', '27408060', '2120584', '38841957', '36455708', '27487686', '34225148', '31744189')
MODE = f('5567211', '17608018', '12471946', '27230001', '29182809', '17974367')
OSCU = f('7206565', '7206205', '7206415', '8114113', '7206574', '8114191', '8114347')
NEGA = f('10276253', '10276039', '10390837', '38163944', '8113887')
MACC = f('21274274', '3733981', '4372403', '3945326')
SETA = f('8465934', '8793891', '7956629', '7676332', '6037948')
STAM = f('10500586', '4337155', '8114086', '8114115', '8114354', '10273414')
TEST = f('36110421', '21637919', '33627097', '8898725', '37347157')
MARE = f('29277070', '36478680', '33412530', '19933672', '36370048', '39166339')
VECC = f('4780860', '31415467', '8848784')
LOGO_S, LOGO_C = 'logo-luce-rossa-scuro.png', 'logo-luce-rossa-chiaro.png'
PHOTOS = FOUL + MODE + OSCU + NEGA + MACC + SETA + STAM + TEST + MARE + VECC
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='600', corsivo=True, maiuscole_pulsanti=True)
C = K.c
MONO = 'Overpass Mono'
VOCI = [('map-pin', 'Via San Nicolò 9 · Trieste', NEGOZI + '#stamperia'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Luce Rossa', LOGO_C, 200, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_C, '1034/270', 'Luce Rossa stampa foulard di seta a Trieste: ogni disegno nasce da una fotografia '
                   'sviluppata in camera oscura, poi passa sulla seta di Como.',
                   [('La collezione', CAPI), ('La camera oscura', OSCURA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La stamperia', 'Via San Nicolò 9, Trieste<br>Martedì — sabato 10 — 19', VOCI,
                   '© 2026 Luce Rossa · Trieste · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')],
                   larghezza=420)


def luce(t):
    """La luce rossa di sicurezza su tutto il sito, un alone che resta acceso dietro le sezioni."""
    return t.classic('pagelight', {'forma': 'alone', 'light_color': HEX['primary'], 'base_color': '', 'position': 'top',
                                   'size': 110, 'intensity': 30, 'transition_ms': 900})


# ── L'apertura: la stampa che si sviluppa ──
def apertura(t):
    m = K.mob
    carta = {'mobile': {'x': 12, 'y': 37, 'width': 76, 'height': 38}}
    foto = {'mobile': {'x': 14.5, 'y': 38.2, 'width': 71, 'height': 35.6}}
    storta = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': -3}, {'p': 100, 'r': -2}]}
    livelli = K.veli('lr-velo', 'sinistra', 36, 60, colore=C['nero'], forza=82, mobile_da=0, mobile_alto=32) + [
        K.livello('lr-carta', tipo='shape', x=55, y=11, w=31, h=78, bgColor='#F6F1EA', borderRadius=2, entra='fadeIn', ritardo=200,
                  durata=900, scrollMove=storta, customCSS='box-shadow:0 30px 60px rgba(0,0,0,.55);', responsive=carta),
        K.livello('lr-foto', tipo='image', imageSrc=K.img(FOUL[1]), objectFit='cover', objectPosition='center 30%', x=56.6, y=13.8,
                  w=27.8, h=72.4, entra='fadeIn', ritardo=200, durata=900, filterContrast=108, filterSaturate=92,
                  scrollMove=storta, responsive=foto),
        # il bianco della carta che se ne va: l'immagine affiora mentre si scorre
        K.livello('lr-bianco', tipo='shape', x=56.6, y=13.8, w=27.8, h=72.4, bgColor='#F6F1EA', entra='fadeIn', ritardo=200, durata=900,
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100, 'r': -3}, {'p': 8, 'o': 100, 'r': -3},
                                                                  {'p': 40, 'o': 55, 'r': -2.6}, {'p': 66, 'o': 12, 'r': -2.2},
                                                                  {'p': 80, 'o': 0, 'r': -2}, {'p': 100, 'o': 0, 'r': -2}]},
                  responsive=foto),
        K.livello('lr-o', contenuto='Luce Rossa · foulard di seta · Trieste', x=6, y=16, w=44, size=13, peso='500',
                  colore=mix(C['testo'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('lr-t1', contenuto='Si sviluppano', tag='h1', x=6, y=22, w=48, size=118, peso='600', fontStyle='italic',
                  colore=C['testo'], lineHeight=0.95, entra='maskFromBottom', ritardo=300, durata=1100,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('lr-t2', contenuto='scorrendo.', tag='p', x=6, y=37, w=48, size=118, peso='600', fontStyle='italic',
                  colore=C['acc'], lineHeight=0.95, entra='maskFromBottom', ritardo=500, durata=1100,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 15.5, 'width': 88}}),
        K.livello('lr-s', contenuto='Ogni foulard nasce da una fotografia stampata in camera oscura. Scorrete e la vedrete affiorare '
                  'dalla carta.', x=6, y=55, w=36, size=17, peso='400', colore=mix(C['testo'], 92), lineHeight=1.55, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 21, 'width': 86}}),
    ]
    # il timer della camera oscura: i tre bagni si accendono uno dopo l'altro
    for k, (nome, da, a, x, xm) in enumerate([('Rivelatore', 6, 40, 6, 6), ('Arresto', 40, 62, 21.5, 36),
                                               ('Fissaggio', 62, 90, 33.5, 62)]):
        livelli.append(K.livello(
            'lr-bagno%d' % k, contenuto='%02d · %s' % (k + 1, nome), x=x, y=76, w=15, size=14, peso='600', colore=C['acc'],
            letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=900 + k * 100,
            scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 30}, {'p': da, 'o': 30}, {'p': da + 4, 'o': 100},
                                                            {'p': a, 'o': 100}, {'p': a + 4, 'o': 30}, {'p': 100, 'o': 30}]},
            responsive={'mobile': {'fontSize': m(10), 'x': xm, 'y': 28, 'width': 32}}))
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 82, 'o': 0}, {'p': 94, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('lr-b', tipo='button', contenuto='La collezione', x=6, y=84, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=2, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 81, 'fontSize': m(11)}}),
        K.livello('lr-l', tipo='button', contenuto='La camera oscura', x=20, y=84, w='auto', size=12, peso='600', colore=C['testo'],
                  bgColor='transparent', padding=16, borderRadius=2, borderWidth=1, borderColor=mix(C['testo'], 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=OSCURA, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix(C['testo'], 14), 'borderColor': C['testo'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 86, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    M.scendi_tel(livelli)
    sl = [K.diapositiva('lr-1', K.sfondo_video(V_VASCA, overlay=0.2, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def pellicole(t, foto, alt):
    """Lo stesso foulard sviluppato con pellicole diverse: il divisore mostra lo scatto grezzo e lo sviluppo."""
    return t.classic('pellicole', {
        'immagine': K.img(foto), 'alt': alt,
        'pellicole': [{'nome': 'Notte sul molo', 'nota': 'Ottanio e arancio, le luci del porto', 'look': 'notte', 'colore': ''},
                      {'nome': 'Bora', 'nota': 'Bianco e nero di grana grossa', 'look': 'bn', 'colore': ''},
                      {'nome': 'Archivio', 'nota': 'Seppia, come le foto della nonna', 'look': 'seppia', 'colore': ''},
                      {'nome': 'Estate', 'nota': 'Colori saturi, rossi accesi', 'look': 'ektar', 'colore': ''},
                      {'nome': 'Sera rossa', 'nota': 'L’alone rosso delle luci', 'look': 'cinestill', 'colore': ''}],
        'partenza': 1, 'confronto': True, 'divisore': 50, 'etichetta_grezzo': 'Lo scatto', 'mostra_nota': True,
        'mostra_etichette': True, 'grana': 30, 'colore_accento': C['acc'], 'colore_divisore': C['chiaro'],
        'sfondo_rullino': mix(C['testo'], 6), 'nome_font_family': 'heading', 'nome_font_size': 22, 'nome_font_weight': '600',
        'nome_color': C['testo'], 'nota_font_size': 13, 'nota_color': C['tenue'], 'proporzione': '4/5', 'raggio': radius(2),
        'raggio_rullino': radius(2), 'posizione_rullini': 'sotto', 'gap': 18}, style={})


def ghiera(t):
    """Le collezioni scelte girando la ghiera della macchina fotografica."""
    voci = [('T', 'Trieste', 'Le rive, il molo Audace, il Canal Grande: la città in sei foulard.', '6 foulard · da 160 €', FOUL[0]),
            ('B', 'Bora', 'Il vento del nord in bianco e nero: le onde sul molo, i cappotti al vento.', '4 foulard · da 170 €', FOUL[3]),
            ('N', 'Notte', 'Il porto di notte, le gru e le luci: rosso, arancio e ottanio.', '5 foulard · da 180 €', FOUL[6]),
            ('A', 'Archivio', 'Le foto di famiglia dei nostri clienti, stampate sulla seta.', 'su richiesta · da 240 €', VECC[1]),
            ('M', 'Miramare', 'Il castello bianco e il mare verde, le estati di Barcola.', '5 foulard · da 160 €', FOUL[2])]
    return t.classic('ghiera', {
        'voci': [{'sigla': s, 'titolo': ti, 'testo': tx, 'prezzo': pr, 'immagine': K.img(im), 'video': '', 'link': CAPI,
                  'testo_link': 'Vedi la collezione'} for s, ti, tx, pr, im in voci],
        'partenza': 1, 'titolo_centro': 'Collezioni', 'mostra_numero': True, 'etichetta': 'Girate la ghiera e scegliete la collezione',
        'stile': 'metallo', 'colore_ghiera': C['sala'], 'colore_sigle': C['testo'], 'colore_indice': C['acc'], 'sfondo_pannello': '',
        'colore_pulsante': C['acc'], 'colore_testo_pulsante': C['on_acc'], 'ombra': 'none', 'sigla_font_family': 'heading',
        'sigla_font_size': 24, 'sigla_font_weight': '600', 'titolo_font_family': 'heading', 'titolo_font_size': 64,
        'titolo_font_size_mobile': 44, 'titolo_font_weight': '600', 'titolo_line_height': 1.0, 'titolo_color': C['testo'],
        'testo_font_size': 17, 'testo_color': C['tenue'], 'prezzo_font_family': 'var(--olo-font-family-mono)', 'prezzo_font_size': 14,
        'prezzo_font_weight': '500', 'prezzo_color': C['acc'], 'misura': 420, 'raggio_media': radius(2),
        'raggio_pulsante': radius(2), 'proporzione': '4/5', 'disposizione': 'lato', 'gap': 64}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([luce(t), K.sechead(t, 'Luce Rossa · Trieste, via San Nicolò 9', ['La seta', 'nasce al buio.'], size=72,
                                         layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Ogni foulard comincia con una fotografia: un rullino scattato per Trieste, sviluppato da noi, '
                                'stampato su carta sotto la luce rossa. Solo quando la stampa è giusta il disegno passa sulla '
                                'seta di Como, novanta per novanta, con l’orlo arrotolato a mano.', size=17),
                      K.button(t, 'La camera oscura', OSCURA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Le pellicole', ['Lo stesso foulard,', 'cinque sviluppi.'], size=60, layout='stack',
                                size_mobile=40),
                      K.text(t, 'Scegliete il rullino e trascinate il divisore: a sinistra lo scatto, a destra lo sviluppo. Il '
                                'foulard si stampa con quello che preferite.', size=17)], wm='1-3', ws='1-1'),
               t.col([pellicole(t, FOUL[4], 'Una donna col foulard di seta stampato, in riva al mare')], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le collezioni', ['Girate', 'la ghiera.'], size=60)]),
        t.one([ghiera(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro foulard', 'da cominciare.'], size=60)]),
        t.one([M.capi(K, t, [
            (FOUL[0], 'Trieste', 'Molo Audace', '', 'Il molo al tramonto, novanta per novanta.', '160 €', 'nuovo'),
            (FOUL[3], 'Bora', 'Raffica', '', 'Le onde sul molo, in bianco e nero.', '170 €', ''),
            (FOUL[6], 'Notte', 'Gru', '', 'Il porto di notte, le luci arancio.', '180 €', ''),
            (FOUL[2], 'Miramare', 'Barcola', '', 'Il mare verde d’estate.', '160 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Rivelatore', 'Arresto', 'Fissaggio', 'Lavaggio', 'Seta di Como'],
                                          fondo=C['acc'], colore=C['on_acc'], size=56)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(K.video_frase(t, V_SPIAGGIA, 'Al vento', 'Dalla carta <em>alla seta.</em>',
                             'La stampa giusta diventa un foulard leggero come la carta su cui è nata.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Addosso', ['Fotografie', 'da portare.'], size=60)]),
        P.fila_foto(K, t, [(MODE[0], 'Il Molo Audace in città'), (TEST[0], 'Il foulard Bora sui capelli'),
                           (MODE[3], 'La Gru annodata al collo'), (TEST[3], 'Il foulard Archivio')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Un rullino nuovo', 'ogni stagione.'],
                         'Le fotografie che scattiamo, i foulard che ne escono, le serate in camera oscura. Quattro volte l’anno.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Venti fotografie,', 'venti foulard.'],
                   'Seta twill di Como, novanta per novanta, orlo arrotolato a mano. Ogni foulard porta sul bordo il numero '
                   'del fotogramma da cui è nato.', bg=K.video_bg(V_VENTO, overlay=30), size=96)]
    out.append(t.section([
        t.row([t.col([luce(t), K.sechead(t, 'Trieste e Bora', ['La città', 'e il suo vento.'], size=60)]),
               ], anim=None),
        t.one([M.capi(K, t, [
            (FOUL[0], 'Trieste', 'Molo Audace', '', 'Il molo al tramonto.', '160 €', 'nuovo'),
            (FOUL[5], 'Trieste', 'Canal Grande', '', 'Le barche e la chiesa.', '160 €', ''),
            (FOUL[3], 'Bora', 'Raffica', '', 'Le onde sul molo.', '170 €', ''),
            (FOUL[7], 'Bora', 'Cappotto', '', 'I passanti piegati dal vento.', '170 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Notte e Miramare', ['Il porto', 'e il castello.'], size=60)]),
        t.one([M.capi(K, t, [
            (FOUL[6], 'Notte', 'Gru', '', 'Le luci arancio del porto.', '180 €', ''),
            (FOUL[4], 'Notte', 'Faro', '', 'La Lanterna, di notte.', '180 €', ''),
            (FOUL[2], 'Miramare', 'Barcola', '', 'Il mare verde d’estate.', '160 €', ''),
            (FOUL[1], 'Miramare', 'Castello', '', 'Il bianco del castello.', '160 €', 'ultimi')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Sviluppateli voi', ['Cinque pellicole,', 'un foulard.'], size=56, layout='stack',
                                size_mobile=40)], wm='1-3', ws='1-1'),
               t.col([pellicole(t, MODE[2], 'Una donna col foulard annodato sui capelli')], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_oscura():
    t = Tpl(ID, 'camera-oscura')
    out = [K.intro(t, 'La camera oscura', ['Tre bagni,', 'una stampa.'],
                   'Sviluppiamo i rullini e stampiamo le fotografie a mano, sotto la luce rossa, come si faceva prima. Solo '
                   'la stampa scelta passa sulla seta.', bg=K.video_bg(V_LAMPADA, overlay=30), size=96)]
    out.append(t.section([
        t.one([luce(t), K.sechead(t, 'Come si stampa', ['Rivelatore, arresto,', 'fissaggio.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'il rivelatore', OSCU[1], 'Rivelare', 'L’immagine', 'affiora', 'La carta esposta va nel primo bagno: in un minuto '
             'l’immagine compare, prima i neri, poi i grigi.', '60', 'secondi'),
            ('2', 'l’arresto', OSCU[3], 'Fermare', 'Il bagno', 'di arresto', 'Un bagno d’acqua e aceto ferma lo sviluppo nel punto '
             'giusto.', '30', 'secondi'),
            ('3', 'il fissaggio', STAM[0], 'Fissare', 'La stampa', 'resta', 'Il fissaggio rende la stampa stabile alla luce; poi si '
             'lava e si appende ad asciugare.', '5', 'minuti')], icona='timer')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In camera oscura', ['Chi accende', 'la luce rossa.'], size=60)]),
        K.squadra(t, [(TEST[2], 'Nives Furlan', 'Fondatrice', 'Fotografa del porto da trent’anni: i foulard sono le sue foto.',
                       'center 25%'),
                      (MODE[5], 'Elia Brandolin', 'Stampa', 'Stampa le fotografie a mano; ne butta nove su dieci.', 'center 25%'),
                      (TEST[4], 'Amina Haddad', 'Seta', 'Porta le stampe sulla seta e arrotola gli orli.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le stampe', ['Appese', 'ad asciugare.'], size=60)]),
        P.fila_foto(K, t, [(STAM[1], 'Le stampe appese'), (STAM[3], 'La stampa nel bagno'), (NEGA[0], 'I negativi'),
                           (STAM[5], 'Le stampe del porto')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, MACC[1], 'La macchina fotografica del porto', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho portato le foto di mia nonna sul molo, negli anni cinquanta. Me le hanno ridate su un '
                                        'foulard: adesso la nonna la porto al collo.', 'Chiara, Udine', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove sceglierli', 'alla luce.'],
                   'Un foulard si guarda alla luce del giorno, sulla pelle. Ecco dove trovarli, e la stamperia.',
                   bg=K.photo_bg(MARE[0]), size=96)]
    out.append(M.negozi(K, t, [
        ('Trieste', 'Stamperia Luce Rossa', 'via San Nicolò 9'), ('Trieste', 'Molo', 'riva Nazario Sauro 4'),
        ('Venezia', 'Fondaco', 'calle Larga 22'), ('Milano', 'Negativo', 'via Solferino 30'), ('Firenze', 'Seta', 'via Maggio 12'),
        ('Roma', 'Fotogramma', 'via dei Coronari 19'), ('Vienna', 'Rotlicht', 'Neubaugasse 41'), ('Lubiana', 'Svila', 'Trubarjeva 6')],
        nota='Alla stamperia si portano le proprie fotografie: diventano un foulard in un mese.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un foulard da una mia foto\nUna serata in camera oscura\nVendere Luce Rossa\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una vostra foto', 'sulla seta?'],
                                'Per un foulard da una vostra fotografia, una serata in camera oscura, o per vendere Luce Rossa.',
                                VOCI, campi, 'Luce Rossa — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La stamperia', ['In Borgo Teresiano,', 'al primo piano.'], size=54, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('Camera oscura', 'il giovedì sera, su prenotazione'),
                                  ('A piedi', 'da piazza Unità, sei minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Luce Rossa · la stamperia', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='stamperia', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sui foulard.'], [
        ('Si lavano?', 'A mano, in acqua fredda, e si asciugano stesi all’ombra. Oppure in lavanderia.'),
        ('Le fotografie sono vere?', 'Sì: ogni foulard nasce da un fotogramma nostro, sviluppato e stampato da noi.'),
        ('Fate foulard dalle mie foto?', 'Sì, anche da una vecchia stampa: in un mese, da 240 €.'),
        ('Spedite?', 'Sì, piegati in una busta di carta da stampa, come le fotografie.'),
    ], 'lr-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pellicola velata', color=C['acc'], align='center'),
                       K.headline(t, 'Qualcuno <em>ha acceso la luce.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Questa pagina si è velata: non si vede più niente. Torniamo all’inizio, al buio.', size=17,
                              align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LAMPADA, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La camera oscura',
    ['moda', 'accessori', 'foulard', 'seta', 'fotografia', 'trieste', 'scuro', 'slider pro', 'luce di pagina'],
    'Una stamperia di foulard di seta nati da fotografie: tutto il sito sta sotto la luce rossa della camera oscura e in '
    'apertura la stampa si sviluppa scorrendo, mentre il timer passa da un bagno all’altro; poi lo stesso foulard con '
    'cinque pellicole, le collezioni scelte girando la ghiera della macchina, la camera oscura e i negozi.',
    [('La collezione', CAPI), ('La camera oscura', OSCURA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('camera-oscura', 'La camera oscura', 'camera-oscura'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Karla\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cormorant Garamond\', Georgia, serif',
    '\'Overpass Mono\', ui-monospace, Menlo, monospace', ['Karla', 'Cormorant Garamond', 'Overpass Mono'],
    corsivo_di=['Cormorant Garamond'], peso_titoli='600', raggio=2, grana=7, titoli_line='0.95', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'lanterna'
THEME['cursor']['ring_color'] = HEX['text']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'camera-oscura.json': build_oscura(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
