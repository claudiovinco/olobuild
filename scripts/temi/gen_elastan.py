# -*- coding: utf-8 -*-
"""Genera il tema «Elastan» (id elastan): un marchio di Milano di abbigliamento sportivo in tessuto elastico, che si
allunga fino al triplo e torna com'era.

La trovata: «tutto si allunga». In apertura il nome ELASTAN è un elastico: scorrendo si tende in larghezza fino a uscire
dallo schermo (e si assottiglia, come la gomma tirata), poi lo si lascia andare e torna al suo posto con un rimbalzo
(livelli dello Slider Pro con l'allungamento orizzontale e verticale). Il nastro di scritte si piega con la velocità
dello scorrimento e i numeri del tessuto si riempiono in cerchio (CounterCircle).

Tema scuro: nero palestra, rosa acceso e acquamarina. Titoli in Anybody, testo in Manrope, dati in Martian Mono.
Logo: ELASTAN con le lettere di mezzo tirate e la molla. Cursore: molla (si allunga e si richiude).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'elastan'
NAME = 'Elastan'

HEX = {
    'primary': '#FF4F8B', 'primary_contrast': '#0E0E11', 'secondary': '#8A8AA0', 'secondary_contrast': '#0E0E11',
    'accent': '#3DF5C8', 'dark': '#060608', 'light': '#FFFFFF', 'muted': '#18181D', 'muted_contrast': '#F5F5F2',
    'text': '#F5F5F2', 'text_muted': '#A9A9B6', 'background': '#0E0E11', 'border': '#2A2A33', 'link': '#FF4F8B',
}
ACQUA = HEX['accent']

HOME, CAPI, TESSUTO, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:il-tessuto}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'palestra@elastan.example', '+39 02 0000 3300', 'tel:+390200003300'
LAT, LNG = '45.4850', '9.2050'

V_MARE, V_PARCO, V_PISTA, V_GAMBE, V_SBARRA, V_SPACCATA, V_PUNTE, V_GINNASTA, V_SOLE = (
    'pexels-video-10042757', 'pexels-video-4455053', 'pexels-video-8691728', 'pexels-video-8456207', 'pexels-video-5139002',
    'pexels-video-10640538', 'pexels-video-5138509', 'pexels-video-6571894', 'pexels-video-8691808')
VIDEOS = [V_MARE, V_PARCO, V_PISTA, V_GAMBE, V_SBARRA, V_SPACCATA, V_PUNTE, V_GINNASTA, V_SOLE]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
LEGG = f('7593007', '5262953', '8567617', '7593199', '16701769', '31679369', '7593012', '7592435')
SPOR = f('40428936', '8520561', '13066312', '6477388', '39354529', '15390867')
ALLU = f('6926037', '6339350', '3984353', '8436627')
YOGA = f('13849110', '13849179', '373907', '374012')
TOP = f('39635391', '29138809', '6674232', '13923898', '6250870', '8484671')
PALE = f('32610333', '32695898', '3888405')
ATLE = f('20615473', '30246193', '6767341', '17428611', '7005562')
ELAS = f('4148956', '863977', '4376713', '14925388', '6932262')
PIST = f('8533636', '37718409', '19190279', '12659352')
TESS = f('34851009', '19856113', '25033893', '20814217')
LOGO_S, LOGO_C = 'logo-elastan-scuro.png', 'logo-elastan-chiaro.png'
PHOTOS = LEGG + SPOR + ALLU + YOGA + TOP + PALE + ATLE + ELAS + PIST + TESS
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True)
C = K.c
MONO = 'Martian Mono'
VOCI = [('map-pin', 'Via Ventura 6 · Milano', NEGOZI + '#palestra'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Elastan', LOGO_C, 190, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_C, '1240/254', 'Elastan fa a Milano leggings, top e tute in un tessuto che si allunga fino al '
                   'triplo e torna com’era, per duecento lavaggi.',
                   [('La collezione', CAPI), ('Il tessuto', TESSUTO), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La palestra', 'Via Ventura 6, Milano<br>Tutti i giorni 7 — 22', VOCI,
                   '© 2026 Elastan · Milano · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')],
                   larghezza=440)


# ── L'apertura: il nome che si tende come un elastico ──
def apertura(t):
    m = K.mob
    tira = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'sx': 1, 'sy': 1}, {'p': 8, 'sx': 1, 'sy': 1}, {'p': 46, 'sx': 2.6, 'sy': 0.66},
                                                {'p': 54, 'sx': 0.78, 'sy': 1.22}, {'p': 59, 'sx': 1.12, 'sy': 0.92},
                                                {'p': 64, 'sx': 0.95, 'sy': 1.04}, {'p': 69, 'sx': 1, 'sy': 1}, {'p': 100, 'sx': 1, 'sy': 1}]}
    livelli = K.veli('el-velo', 'basso', 30, 62, colore=C['nero'], forza=82, mobile_da=58) + [
        K.livello('el-nome', contenuto='ELASTAN', tag='h1', x=0, y=30, w=100, size=230, peso='900', fontFamily='Anybody',
                  colore=C['testo'], align='center', lineHeight=0.9, letterSpacing=4, entra='fadeIn', ritardo=200, durata=900,
                  customCSS='transform-origin:50% 50%;', scrollMove=tira,
                  responsive={'mobile': {'fontSize': m(60), 'letterSpacing': 1, 'x': 0, 'y': 31, 'width': 100}}),
        K.livello('el-elastico', tipo='shape', x=25, y=60, w=50, h=0.9, bgColor=C['acc'], borderRadius=999, entra='fadeIn', ritardo=400,
                  durata=700, customCSS='transform-origin:50% 50%;', scrollMove=tira,
                  responsive={'mobile': {'x': 15, 'y': 37.6, 'width': 70, 'height': 0.4}}),
        K.livello('el-o', contenuto='Elastan · abbigliamento sportivo · Milano', x=6, y=14, w=50, size=13, peso='500',
                  colore=mix(C['testo'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('el-s', contenuto='Scorrete: tiratelo.', x=0, y=68, w=100, size=20, peso='500', colore=mix(C['testo'], 92),
                  align='center', ritardo=800,
                  scrollMove={'inizio': 0, 'fine': 30, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(14), 'x': 0, 'y': 41, 'width': 100}}),
    ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 66, 'o': 0}, {'p': 80, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('el-f1', contenuto='Si allunga del 300%. Torna sempre com’era.', tag='p', x=10, y=68, w=80, size=30, peso='600',
                  colore=C['testo'], align='center', lineHeight=1.2, scrollMove=fine, entra='fadeIn', ritardo=0,
                  responsive={'mobile': {'fontSize': m(17), 'x': 6, 'y': 41, 'width': 88}}),
        K.livello('el-b', tipo='button', contenuto='La collezione', x=36, y=78, w='auto', size=12, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=999, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': ACQUA, 'duration': 250},
                  responsive={'tablet': {'x': 30}, 'mobile': {'x': 8, 'y': 47, 'fontSize': m(11)}}),
        K.livello('el-l', tipo='button', contenuto='Il tessuto', x=51, y=78, w='auto', size=12, peso='700', colore=C['testo'],
                  bgColor='transparent', padding=16, borderRadius=999, borderWidth=1, borderColor=mix(C['testo'], 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=TESSUTO, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix(C['testo'], 14), 'borderColor': C['testo'], 'duration': 250},
                  responsive={'tablet': {'x': 52}, 'mobile': {'x': 55, 'y': 47, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    sl = [K.diapositiva('el-1', K.sfondo_video(V_SPACCATA, overlay=0.3, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1700, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def cerchi(t, voci):
    """I numeri del tessuto che si riempiono in cerchio: voci = [(valore, massimo, suffisso, titolo, colore)]."""
    return t.row([t.col([t.classic('countercircle', {
        'value': str(v), 'max_value': str(mx), 'suffix': su, 'prefix': '', 'title': ti, 'size': '240', 'stroke_width': '6',
        'stroke_color': col, 'track_color': C['filo'], 'text_color': col, 'title_color': C['testo'], 'duration': '2600',
        'title_position': 'below'}, style={})], wm='1-3', ws='1-1') for v, mx, su, ti, col in voci], layout='33-33-33', gap=32)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Elastan · Milano, via Ventura 6', ['Si tende.', 'E torna.'], size=72, layout='stack',
                                size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Il nostro tessuto ha quattro vie: si allunga in largo e in lungo, fino al triplo, e quando lo '
                                'lasciate torna com’era, senza ginocchia gonfie né cuciture che cedono. Lo proviamo in palestra, '
                                'su chi si allunga per mestiere: ballerine, ginnaste, arrampicatori.', size=17),
                      K.button(t, 'Il tessuto', TESSUTO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Tira', 'Molla', 'Tira', 'Molla', 'Torna'], fondo=C['acc'], colore=C['on_acc'],
                                          size=72, velocita='26')], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'Il tessuto in numeri', ['Quanto si allunga,', 'quanto torna.'], size=60)]),
        cerchi(t, [(300, 300, '%', 'Si allunga fino al triplo', C['acc']), (98, 100, '%', 'Torna com’era, dopo cento trazioni', ACQUA),
                   (200, 200, '', 'Lavaggi senza perdere forma', C['acc'])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro capi', 'che si tendono.'], size=60)]),
        t.one([M.capi(K, t, [
            (LEGG[0], 'Leggings', 'Spaccata', '', 'Vita alta, quattro vie.', '95 €', 'nuovo'),
            (TOP[1], 'Top', 'Ponte', '', 'Il sostegno che segue la schiena.', '65 €', ''),
            (LEGG[3], 'Leggings', 'Corsa', '', 'Con la tasca per il telefono.', '90 €', ''),
            (SPOR[1], 'Tute', 'Sbarra', '', 'Intera, per la danza.', '140 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'A riposo e tirato', ['Trascinate:', 'è lo stesso tessuto.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (TESS[0], TESS[2], 'La maglia', 'Il tessuto fermo, e tirato al triplo.'),
            (LEGG[6], LEGG[7], 'I Spaccata', 'In piedi e nella spaccata.'),
            (ELAS[2], ELAS[1], 'La prova', 'Cento trazioni con l’elastico: torna com’era.')], prima='A riposo', dopo='Tirato')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_PISTA, 'In pista', 'Si muove <em>con voi.</em>',
                             'Il tessuto non tira e non scivola: dopo dieci minuti non vi ricordate di averlo addosso.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Addosso', ['Tesi', 'ogni giorno.'], size=60)]),
        P.fila_foto(K, t, [(ATLE[1], 'La tuta Sbarra'), (SPOR[3], 'I Corsa'), (TOP[4], 'Il top Ponte'), (ATLE[3], 'I Spaccata')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Un allenamento', 'ogni settimana.'],
                         'I capi nuovi, le lezioni aperte in palestra, un esercizio di allungamento a settimana.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Dodici capi,', 'quattro vie.'],
                   'Leggings, top, tute e shorts nel nostro tessuto elastico, filato in Italia da reti da pesca riciclate.',
                   bg=K.video_bg(V_SBARRA, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Leggings', ['Vita alta,', 'nessuna cucitura dietro.'], size=60)]),
        t.one([M.capi(K, t, [
            (LEGG[0], 'Leggings', 'Spaccata', '', 'Vita alta, quattro vie.', '95 €', 'nuovo'),
            (LEGG[3], 'Leggings', 'Corsa', '', 'La tasca per il telefono.', '90 €', ''),
            (LEGG[4], 'Leggings', 'Rosa', '', 'Il rosa del marchio.', '95 €', ''),
            (LEGG[5], 'Leggings', 'Notte', '', 'Riflettenti, per la sera.', '100 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([t.one([M.nastro(K, t, ['Quattro vie', 'Trecento per cento', 'Duecento lavaggi', 'Reti da pesca'],
                                          fondo=ACQUA, colore=C['on_acc'], size=56, verso='right')], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'Top e tute', ['Per chi', 'si piega.'], size=60)]),
        t.one([M.capi(K, t, [
            (TOP[1], 'Top', 'Ponte', '', 'Il sostegno che segue la schiena.', '65 €', ''),
            (TOP[3], 'Top', 'Rosso', '', 'Le spalline incrociate.', '60 €', ''),
            (SPOR[1], 'Tute', 'Sbarra', '', 'Intera, per la danza.', '140 €', ''),
            (TOP[5], 'Top', 'Corsa', '', 'Leggero, traforato.', '60 €', 'ultimi')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_tessuto():
    t = Tpl(ID, 'il-tessuto')
    out = [K.intro(t, 'Il tessuto', ['Quattro vie,', 'tre volte tanto.'],
                   'Un tessuto si dice a quattro vie quando si allunga in tutte le direzioni. Il nostro arriva al triplo, e torna.',
                   bg=K.video_bg(V_GINNASTA, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si fa', ['Dalla rete', 'ai leggings.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'il filo', TESS[1], 'Filare', 'Il filo', 'riciclato', 'Le reti da pesca recuperate in Adriatico diventano un filo '
             'di poliammide, unito all’elastan.', '80', '% riciclato'),
            ('2', 'la maglia', TESS[3], 'Lavorare', 'La maglia', 'a quattro vie', 'Il filo si lavora a maglia stretta: si allunga in '
             'tutte le direzioni.', '4', 'vie'),
            ('3', 'la prova', ALLU[0], 'Provare', 'In palestra', 'sul serio', 'Ogni capo lo provano per un mese ballerine e ginnaste '
             'prima di andare in negozio.', '30', 'giorni')], icona='activity')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le prove', ['Tirato,', 'lavato, rifatto.'], size=60)]),
        cerchi(t, [(300, 300, '%', 'Allungamento massimo', C['acc']), (98, 100, '%', 'Ritorno dopo cento trazioni', ACQUA),
                   (80, 100, '%', 'Filo riciclato', C['acc'])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In palestra', ['Chi lo tira', 'ogni giorno.'], size=60)]),
        K.squadra(t, [(ATLE[0], 'Chiara Bassi', 'Fondatrice', 'Ex ginnasta: voleva dei leggings che non le stessero addosso.',
                       'center 25%'),
                      (ATLE[2], 'Leon Weber', 'Tessuti', 'Prova ogni filo cento volte prima di sceglierlo.', 'center 25%'),
                      (ATLE[4], 'Nia Johnson', 'Prove', 'Ballerina: prova i capi nella spaccata.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, YOGA[0], 'Una lezione in palestra', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Li ho tirati in tutte le posizioni dello yoga, poi li ho lavati duecento volte. Sono '
                                        'ancora quelli del primo giorno, io un po’ meno.', 'Sara, insegnante di yoga', size='38')],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarli', 'in movimento.'],
                   'Nei negozi c’è sempre uno specchio e un tappetino: si provano piegandosi. E c’è la nostra palestra.',
                   bg=K.photo_bg(PALE[0]), size=96)]
    out.append(M.negozi(K, t, [
        ('Milano', 'Palestra Elastan', 'via Ventura 6'), ('Milano', 'Molla', 'corso Garibaldi 60'), ('Torino', 'Spaccata', 'via Po 12'),
        ('Bologna', 'Ponte', 'via d’Azeglio 30'), ('Roma', 'Allungo', 'via del Corso 400'), ('Napoli', 'Elastico', 'via Chiaia 60'),
        ('Berlino', 'Dehnung', 'Torstraße 99'), ('Copenaghen', 'Stræk', 'Jægersborggade 20')],
        nota='In palestra le lezioni di allungamento sono gratis, la domenica mattina.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una lezione in palestra\nUn capo da sostituire\nVendere Elastan\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una lezione', 'di prova?'],
                                'Per una lezione in palestra, un capo da sostituire, o per vendere Elastan nel vostro negozio.', VOCI,
                                campi, 'Elastan — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La palestra', ['A Lambrate,', 'in un capannone.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'tutti i giorni 7 — 22'), ('Lezioni gratis', 'la domenica alle 10'),
                                  ('In metro', 'Lambrate, linea 2')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Elastan · la palestra', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='palestra', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sul tessuto.'], [
        ('Diventa trasparente piegandosi?', 'No: la maglia è stretta, la proviamo in spaccata davanti alla luce.'),
        ('Si lava?', 'A trenta gradi, senza ammorbidente: duecento lavaggi senza perdere forma.'),
        ('Fate le taglie forti?', 'Sì, dalla XXS alla 4XL: il tessuto si allunga, la taglia resta giusta.'),
        ('Spedite?', 'Sì, in tutta Europa, in una busta compostabile.'),
    ], 'el-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · elastico rotto', color=ACQUA, align='center'),
                       K.headline(t, 'Abbiamo tirato <em>troppo.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Questa pagina si è strappata. Torniamo all’inizio, piano.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_PUNTE, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il nome che si allunga',
    ['moda', 'abbigliamento', 'sport', 'leggings', 'milano', 'scuro', 'slider pro', 'contatori'],
    'Un marchio di abbigliamento sportivo in tessuto elastico: in apertura il nome si tende come un elastico scorrendo, si '
    'assottiglia e poi torna al suo posto con un rimbalzo; poi il nastro di scritte che si piega con lo scorrimento, i '
    'numeri del tessuto che si riempiono in cerchio, a riposo e tirato da trascinare, il tessuto e i negozi.',
    [('La collezione', CAPI), ('Il tessuto', TESSUTO), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('il-tessuto', 'Il tessuto', 'il-tessuto'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Anybody\', \'Manrope\', sans-serif',
    '\'Martian Mono\', ui-monospace, Menlo, monospace', ['Manrope', 'Anybody', 'Martian Mono'], corsivo_di=[],
    peso_titoli='800', raggio=14, grana=3, titoli_line='0.95', titoli_spazio='0')
THEME['styles']['google_fonts_weights'] = '300;400;500;600;700;800;900'
THEME['cursor']['style'] = 'molla'
THEME['cursor']['ring_color'] = HEX['accent']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'il-tessuto.json': build_tessuto(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
