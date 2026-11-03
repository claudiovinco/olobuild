# -*- coding: utf-8 -*-
"""Genera il tema «A Cipolla» (id a-cipolla): un marchio di Belluno di abiti da montagna e da città pensati per vestirsi
a strati: la maglia di lana merino, il pile, il piumino, il guscio.

Le sezioni si coprono una sull'altra come gli strati che si indossano (effetto sticky «Cover»): la maglia, poi il pile
che la copre, poi il piumino, poi il guscio; più giù un gruppo orizzontale («Cover orizzontale») porta lo stesso strato in
città, in montagna e in rifugio, e nella pagina degli strati le carte si impilano scorrendo.

Apertura: «gli strati si tolgono». Lo slider resta fermo in cima mentre si scorre: quattro teli dei colori della cipolla
coprono il video e se ne vanno uno alla volta, ognuno da una parte (su, a sinistra, a destra, giù), e sotto c'è la
montagna. Tema chiaro: crema, rame, viola cipolla e salvia. Titoli in Fraunces, testo in DM Sans, dati in IBM Plex Mono.
Cursore: gli strati (anelli uno dentro l'altro che si sfogliano muovendosi).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'a-cipolla'
NAME = 'A Cipolla'

HEX = {
    'primary': '#A4471F', 'primary_contrast': '#FFFFFF', 'secondary': '#6D2E5B', 'secondary_contrast': '#FFFFFF',
    'accent': '#7A8B6A', 'dark': '#2A2420', 'light': '#FFFDF9', 'muted': '#EDE3D5', 'muted_contrast': '#2A2420',
    'text': '#2A2420', 'text_muted': '#5E544B', 'background': '#F6EFE6', 'border': '#DCCFBE', 'link': '#A4471F',
}
STRATI = [('#3B1F35', 'Strato 1', 'La pelle'), ('#8A3B1E', 'Strato 2', 'Il caldo'), ('#2F3B2F', 'Strato 3', 'L’aria'),
          ('#2A2420', 'Strato 4', 'Il guscio')]

HOME, CAPI, STRATO, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:gli-strati}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@acipolla.example', '+39 0437 000 812', 'tel:+390437000812'
LAT, LNG = '46.1410', '12.2170'

V_MONTE, V_GIALLO, V_NEVE, V_SCALINI, V_VESTE, V_ROSSO, V_SELFIE, V_MAGLIA = (
    'pexels-video-36464391', 'pexels-video-15469423', 'pexels-video-11253904', 'pexels-video-11253885', 'pexels-video-855730',
    'pexels-video-7034954', 'pexels-video-7009790', 'pexels-video-4201540')
VIDEOS = [V_MONTE, V_GIALLO, V_NEVE, V_SCALINI, V_VESTE, V_ROSSO, V_SELFIE, V_MAGLIA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
STR = f('31064107', '31064229', '10453389', '31084939', '19628611', '33714973')
PIU = f('14887834', '16018081', '15358886', '17474395', '19441381')
MAG = f('29413405', '19169483', '20025026', '11055811', '7576389')
PIL = f('36159344', '35392926', '4641111', '7010124', '30404528')
GUS = f('35746663', '4573076', '14124474', '39277371', '4751943', '16966163', '24405957')
CIT = f('16262214', '17568261', '20360140', '15889933')
ESC = f('35924800', '11970888', '10254876', '939727', '31348061', '1076109')
SCI = f('33715003', '19524510', '6800063', '6799710')
CIP = f('34324952', '35168133', '37540975')
RIF = f('36206791', '15506795', '14610315', '38558931')
LOGO_S, LOGO_C = 'logo-a-cipolla-scuro.png', 'logo-a-cipolla-chiaro.png'
PHOTOS = STR + PIU + MAG + PIL + GUS + CIT + ESC + SCI + CIP + RIF
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#F0A27C')
C = K.c
MONO = 'IBM Plex Mono'
VOCI = [('map-pin', 'Via Mezzaterra 40 · Belluno', NEGOZI + '#bottega'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'A Cipolla', LOGO_S, 168, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '962/273', 'A Cipolla cuce a Belluno abiti da mettere uno sopra l’altro: la lana merino, il '
                   'pile, il piumino, il guscio. Si toglie uno strato quando si sale, se ne mette uno quando si arriva.',
                   [('La collezione', CAPI), ('Gli strati', STRATO), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Via Mezzaterra 40, Belluno<br>Lunedì — sabato 9 — 12:30 e 15:30 — 19', VOCI,
                   '© 2026 A Cipolla · Belluno · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=380, fondo=C['sala'])


# ── L'apertura: i teli della cipolla che se ne vanno uno alla volta ──
def apertura(t):
    m = K.mob
    vie = [{'y': -900}, {'x': -1600}, {'x': 1600}, {'y': 900}]
    livelli = []
    # dal basso in alto: l'ultimo telo della lista è quello sopra, e se ne va per primo
    for k in range(3, -1, -1):
        col, num, nome = STRATI[k]
        a, b = 6 + k * 21, 6 + k * 21 + 18
        tappe = [dict({'p': 0}, **{c: 0 for c in vie[k]}), dict({'p': a}, **{c: 0 for c in vie[k]}),
                 dict({'p': b}, **vie[k]), dict({'p': 100}, **vie[k])]
        livelli.append(K.livello('ac-telo%d' % k, tipo='shape', x=0, y=0, w=100, h=100, bgColor=col, entra='fadeIn', ritardo=0,
                                 durata=500, customCSS='background-image:repeating-linear-gradient(90deg,rgba(255,255,255,.035) 0 2px,'
                                                       'transparent 2px 26px);',
                                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe},
                                 responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}}))
        livelli.append(K.livello('ac-nome%d' % k, contenuto='%s · %s' % (num, nome), x=6, y=86, w=40, size=13, peso='500',
                                 colore=mix('#FFFFFF', 82), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=300,
                                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe},
                                 responsive={'mobile': {'fontSize': m(10), 'y': 92, 'x': 7, 'width': 86}}))
    testi = [
        K.livello('ac-o', contenuto='A Cipolla · abiti a strati · Belluno', x=6, y=20, w=50, size=13, peso='500',
                  colore=mix('#FFFFFF', 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 9, 'x': 7, 'width': 86}}),
        K.livello('ac-t1', contenuto='Vestitevi', tag='h1', x=6, y=26, w=70, size=120, peso='600', colore='#FFFFFF',
                  lineHeight=1.0, entra='maskFromBottom', ritardo=350, durata=1100,
                  responsive={'mobile': {'fontSize': m(52), 'y': 12, 'x': 7, 'width': 90}}),
        K.livello('ac-t2', fontFamily='Fraunces', contenuto='a cipolla.', tag='p', x=6, y=41, w=70, size=120, peso='600',
                  fontStyle='italic', colore='#F0A27C', lineHeight=1.0, entra='maskFromBottom', ritardo=550, durata=1100,
                  responsive={'mobile': {'fontSize': m(52), 'y': 17.5, 'x': 7, 'width': 90}}),
        K.livello('ac-s', contenuto='Quattro strati che si mettono uno sopra l’altro: si toglie quello in più quando si sale, si '
                                    'rimette quando si arriva.', x=6, y=58, w=38, size=17, peso='400', colore=mix('#FFFFFF', 92),
                  lineHeight=1.6, ritardo=800, responsive={'mobile': {'fontSize': m(13), 'y': 24, 'x': 7, 'width': 86}}),
        K.livello('ac-b', tipo='button', contenuto='Gli strati', x=6, y=71, w='auto', size=14, peso='600', colore='#2A2420',
                  bgColor='#FFFDF9', padding=16, borderRadius=10, entra='fadeInUp', ritardo=1000, buttonUrl=STRATO,
                  hover={'bgColor': '#F0A27C', 'duration': 250}, responsive={'mobile': {'y': 31.5, 'x': 7, 'fontSize': m(12)}}),
        K.livello('ac-hint', contenuto='Scorrete: si toglie uno strato alla volta ↓', x=56, y=86, w=38, size=13, peso='500',
                  colore=mix('#FFFFFF', 85), fontFamily=MONO, ritardo=1300, textAlign='right',
                  scrollMove={'inizio': 0, 'fine': 12, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 37, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
    ]
    veli = K.veli('ac-velo', 'sinistra', 42, 70, colore=C['nero'], forza=84, mobile_alto=40)
    sl = [K.diapositiva('ac-1', K.sfondo_video(V_MONTE, overlay=0.15, colore=C['nero']), veli + livelli + testi, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2200, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def chiare(sp):
    """Le voci dei dati sopra il colore scuro dello strato: il nome tenue chiaro (il «tenue» del tema è scuro)."""
    sp['settings']['term_color'] = mix('#FFFFFF', 72)
    return sp


def strato(t, k, foto, titolo, accento, testo, capo, prezzo, peso):
    """Uno strato: una sezione del colore dello strato che copre quella prima (sticky «Cover»)."""
    col, num, nome = STRATI[k]
    blocco = [K.label(t, '%s · %s' % (num, nome), color=mix('#FFFFFF', 80)),
              K.headline(t, '%s <em>%s</em>' % (titolo, accento), size=72, tag='h2', pad_bottom=18, color='#FFFFFF',
                         accento='#F0A27C'),
              K.text(t, testo, size=17, color=mix('#FFFFFF', 88)),
              chiare(K.specs(t, [('Il capo', capo), ('Il peso', peso), ('Il prezzo', prezzo)], chiaro=True)),
              K.button(t, 'Nella collezione', CAPI, chiaro=True, pad_top=18)]
    return t.section([
        t.row([t.col(blocco, wm='1-2', ws='1-1'), t.col([K.image(t, foto, titolo, ratio='4/5')], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center', anim=None),
    ], bg=solid(col), padding='large', width='large', sticky_effect='cover',
        style={'padding_top': 110, 'padding_bottom': 110})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'A Cipolla · Belluno, dal 1987', ['Il segreto della montagna', 'è vestirsi a cipolla.'],
                                size=60, layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Non un giaccone pesante: quattro strati leggeri, uno sopra l’altro. Quando si sale se ne '
                                'toglie uno, quando ci si ferma si rimette. Li cuciamo perché stiano bene insieme, e ognuno '
                                'anche da solo, in città.', size=17),
                      K.button(t, 'Gli strati', STRATO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.label(t, 'Scorrete: ogni strato copre quello di prima', color=C['acc'], pad_top=48)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 90}))
    out.append(strato(t, 0, MAG[0], 'La maglia', 'di merino.', 'Sulla pelle: lana merino da 150 grammi, che scalda anche '
                      'sudata e non prende odore. Si porta per tre giorni di fila.', 'Maglia Pelle', '79 €', '150 g'))
    out.append(strato(t, 1, PIL[1], 'Il pile', 'che tiene.', 'Sopra la maglia: pile riciclato, il caldo che non pesa. Con le '
                      'tasche alte, che restano libere sotto lo zaino.', 'Pile Caldo', '110 €', '320 g'))
    out.append(strato(t, 2, PIU[2], 'Il piumino', 'd’aria.', 'Per le soste: piumino d’oca riciclato, si chiude nella sua tasca '
                      'come un’arancia.', 'Piumino Aria', '220 €', '280 g'))
    out.append(strato(t, 3, GUS[3], 'Il guscio', 'contro tutto.', 'Fuori da tutto: tre strati impermeabili e traspiranti, il '
                      'cappuccio che segue la testa.', 'Guscio Tutto', '290 €', '390 g'))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Ogni strato', 'anche da solo.'], size=56)]),
        t.one([M.capi(K, t, [
            (MAG[2], 'Strato 1', 'Maglia', ' Pelle', 'Merino 150 g, girocollo o lupetto.', '79 €', ''),
            (PIL[0], 'Strato 2', 'Pile', ' Caldo', 'Pile riciclato con le tasche alte.', '110 €', ''),
            (PIU[0], 'Strato 3', 'Piumino', ' Aria', 'Si chiude nella sua tasca.', '220 €', 'nuovo'),
            (GUS[1], 'Strato 4', 'Guscio', ' Tutto', 'Tre strati, cuciture nastrate.', '290 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, CIT[0], 'In città', 'Gli strati <em>anche in tram.</em>', 'La maglia sotto la camicia, il piumino '
                          'sotto il cappotto: la cipolla vale anche in ufficio.', 'La collezione', CAPI, effetto='cover-h'))
    out.append(K.pannello(t, ESC[3], 'In montagna', 'Si toglie <em>salendo.</em>', 'A metà salita via il pile, in cima si '
                          'rimette tutto: lo zaino ne tiene due strati.', 'Gli strati', STRATO, effetto='cover-h'))
    out.append(K.pannello(t, RIF[2], 'In rifugio', 'Si resta <em>in maglia.</em>', 'Davanti alla stufa basta il primo strato. '
                          'Gli altri asciugano appesi.', 'Negozi', NEGOZI, effetto='cover-h'))
    out.append(M.lettera(K, t, 'La lettera', ['Quando cambia il tempo,', 'vi scriviamo.'],
                         'A ogni stagione: quanti strati servono, i capi nuovi, i rifugi dove li trovate.', bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Quattro strati,', 'dodici capi.'],
                   'Si combinano come si vuole: ogni capo sta sopra quello dello strato prima. I prezzi sono quelli '
                   'consigliati.', bg=K.photo_bg(ESC[0]), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Strati 1 e 2', ['Sulla pelle', 'e il caldo.'], size=56)]),
        t.one([M.capi(K, t, [
            (MAG[2], 'Strato 1', 'Maglia', ' Pelle', 'Merino 150 g, girocollo.', '79 €', ''),
            (MAG[4], 'Strato 1', 'Lupetto', ' Pelle', 'Merino 200 g, collo alto.', '89 €', ''),
            (PIL[0], 'Strato 2', 'Pile', ' Caldo', 'Pile riciclato, tasche alte.', '110 €', ''),
            (MAG[3], 'Strato 2', 'Maglione', ' Nonno', 'Lana grossa della Valbelluna.', '150 €', 'ultimi')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Strati 3 e 4', ['L’aria', 'e il guscio.'], size=56)]),
        t.one([M.capi(K, t, [
            (PIU[0], 'Strato 3', 'Piumino', ' Aria', 'Piuma riciclata, si chiude in tasca.', '220 €', 'nuovo'),
            (PIU[3], 'Strato 3', 'Gilet', ' Aria', 'Il piumino senza maniche.', '150 €', ''),
            (GUS[1], 'Strato 4', 'Guscio', ' Tutto', 'Tre strati, cuciture nastrate.', '290 €', ''),
            (STR[1], 'Strato 4', 'Cappotto', ' Città', 'Lana cotta e fodera impermeabile.', '340 €', '')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Strato', 'su strato.'], size=56)]),
        P.fila_foto(K, t, [(STR[0], 'Tre strati in città'), (SCI[1], 'La sciarpa sopra il guscio'), (PIU[4], 'Il piumino aperto'),
                           (STR[5], 'Il cappotto sopra la maglia')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_strati():
    t = Tpl(ID, 'gli-strati')
    out = [K.intro(t, 'Gli strati', ['Come si veste', 'una cipolla.'],
                   'Quattro strati, ognuno con un compito. La regola è una: si toglie prima di sudare e si rimette prima di '
                   'avere freddo.', bg=K.video_bg(V_VESTE), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Uno sopra l’altro', ['Scorrete:', 'si mettono da soli.'], size=56)]),
        t.one([P.pila(K, t, [
            ('Strato 1 · la pelle', 'La maglia', ' di merino', '<p>Tiene asciutti. La lana merino porta via il sudore e scalda '
             'anche bagnata.</p><ul><li>150 o 200 grammi</li><li>Non prende odore: tre giorni di fila</li></ul>', MAG[1]),
            ('Strato 2 · il caldo', 'Il pile', ' che tiene', '<p>Tiene il caldo che fa il corpo. Si toglie per primo quando si '
             'sale.</p><ul><li>Pile riciclato da 320 grammi</li><li>Tasche alte, libere sotto lo zaino</li></ul>', PIL[3]),
            ('Strato 3 · l’aria', 'Il piumino', ' d’aria', '<p>Per le soste e la sera: l’aria ferma fra le piume è il miglior '
             'isolante.</p><ul><li>Piuma d’oca riciclata</li><li>Si chiude nella sua tasca</li></ul>', PIU[1]),
            ('Strato 4 · il guscio', 'Il guscio', ' contro tutto', '<p>Fuori da tutto: pioggia, vento, neve. Fa uscire il vapore '
             'e non entrare l’acqua.</p><ul><li>Tre strati, cuciture nastrate</li><li>Il cappuccio che segue la testa</li></ul>',
             GUS[5])], altezza=420)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_NEVE, 'La regola', 'Si toglie <em>prima di sudare.</em>',
                             'E si rimette prima di avere freddo: il segreto è tutto qui.', overlay=24))
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi cuce', 'gli strati.'], size=56)]),
        K.squadra(t, [(CIT[1], 'Elena Dal Farra', 'Fondatrice', 'Guida alpina, ha cucito il primo pile per i suoi clienti.',
                       'center 25%'),
                      (SCI[0], 'Paolo Zanella', 'Sarto', 'Taglia ogni strato perché stia sopra l’altro senza tirare.', 'center 25%'),
                      (CIT[3], 'Irene Fant', 'Tessuti', 'Prova le lane in rifugio, d’inverno.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarli,', 'uno sopra l’altro.'],
                   'Gli strati si provano insieme: nei negozi di montagna che ci vendono e nella bottega di Belluno.',
                   bg=K.photo_bg(RIF[0]), size=88)]
    out.append(M.negozi(K, t, [
        ('Belluno', 'Bottega A Cipolla', 'via Mezzaterra 40'), ('Cortina', 'La Baita', 'corso Italia 61'),
        ('Bolzano', 'Strati', 'via dei Portici 18'), ('Trento', 'Rifugio in Città', 'via Belenzani 9'),
        ('Aosta', 'Il Guscio', 'via Sant’Anselmo 33'), ('Milano', 'Alta Quota', 'via Vigevano 20'),
        ('Torino', 'Sentiero', 'via Garibaldi 41'), ('Innsbruck', 'Zwiebel', 'Maria-Theresien-Straße 7')],
        nota='Nei rifugi del Nevegal trovate le maglie da provare.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Quanti strati per un’escursione\nUn capo da riparare\nVendere A Cipolla\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Dove andate?', 'Vi diciamo cosa mettere.'],
                                'Diteci il giro che avete in mente: vi consigliamo gli strati, e dove provarli.', VOCI, campi,
                                'A Cipolla — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['In Mezzaterra,', 'sotto i portici.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — sabato 9 — 12:30 e 15:30 — 19'), ('Riparazioni', 'tutti i capi, anche vecchi'),
                                  ('In treno', 'dalla stazione, dieci minuti a piedi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'A Cipolla · bottega', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='bottega', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di partire', ['Le domande', 'di chi si veste a strati.'], [
        ('Quanti strati servono?', 'D’estate due, in primavera tre, d’inverno quattro. In città di solito due.'),
        ('Si lavano in lavatrice?', 'Sì, tutti: la lana a trenta gradi col programma lana, il guscio senza ammorbidente.'),
        ('Riparate i capi?', 'Sempre, anche quelli di dieci anni fa: la bottega ha un banco solo per le riparazioni.'),
        ('Spedite?', 'No: gli strati si provano insieme. Il negozio più vicino si fa mandare la taglia.'),
    ], 'ac-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · strato mancante', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui manca <em>uno strato.</em>', size=76, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La pagina non c’è. Rimettiamoci qualcosa addosso e torniamo all’inizio.', size=17,
                              align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SCALINI, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Gli strati che si coprono',
    ['moda', 'abbigliamento', 'montagna', 'outdoor', 'marchio', 'belluno', 'chiaro', 'sticky cover', 'slider pro'],
    'Un marchio di abiti da montagna e da città pensati per vestirsi a strati, e un sito fatto a strati: in apertura quattro '
    'teli dei colori della cipolla se ne vanno uno alla volta scorrendo, poi ogni strato copre quello di prima, e in città, '
    'in montagna e in rifugio arrivano di lato uno sopra l’altro.',
    [('La collezione', CAPI), ('Gli strati', STRATO), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('gli-strati', 'Gli strati', 'gli-strati'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'DM Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fraunces\', Georgia, serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['DM Sans', 'Fraunces', 'IBM Plex Mono'], corsivo_di=['Fraunces'],
    peso_titoli='600', raggio=10, grana=3, titoli_line='1.0', titoli_spazio='-0.015em')
THEME['cursor']['style'] = 'strati'
THEME['cursor']['ring_color'] = HEX['secondary']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'gli-strati.json': build_strati(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
