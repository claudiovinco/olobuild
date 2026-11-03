# -*- coding: utf-8 -*-
"""Genera il tema «Cangiante» (id cangiante): un marchio di Torino di giacche e impermeabili in tessuti che cambiano colore,
iridescenti, termocromici (col calore) e fotocromici (col sole).

Il sito cambia colore come i suoi tessuti: la Luce di pagina «Tutta la pagina» (1.4.957) dà a ogni sezione la sua tinta
piena, lime, viola, rosa, menta, giallo, arancio, e il testo cambia con lei (nero o bianco), testata e piè di pagina
compresi. Gli accenti e i pulsanti seguono il testo (i pulsanti sono il testo e il fondo scambiati), così restano
leggibili su ogni tinta; le schede dei capi hanno un fondo bianco e colori fissi.

Apertura: un titolo enorme al centro e il video che cambia tinta da solo: quattro veli di colore in fusione «colore»
che si danno il cambio in un ciclo. Titoli in Unbounded, testo in Manrope, dati in Fira Code. Cursore: il prisma.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema, TOK  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'cangiante'
NAME = 'Cangiante'

HEX = {
    'primary': '#15121F', 'primary_contrast': '#FFFFFF', 'secondary': '#FFFFFF', 'secondary_contrast': '#15121F',
    'accent': '#7B5CFF', 'dark': '#0D0B14', 'light': '#FFFFFF', 'muted': '#FFFFFF', 'muted_contrast': '#15121F',
    'text': '#FFFFFF', 'text_muted': '#D9D4E8', 'background': '#15121F', 'border': '#3A3448', 'link': '#FFFFFF',
}
INCHIOSTRO, VIOLA = '#15121F', '#5B3BE8'
TINTE = [('#D7FF3A', INCHIOSTRO), ('#4B2BFF', '#FFFFFF'), ('#FF4FB3', INCHIOSTRO), ('#2CE5C0', INCHIOSTRO),
         ('#FFC93C', INCHIOSTRO), ('#FF7A1A', INCHIOSTRO)]
SCURO = ('#15121F', '#FFFFFF')

HOME, CAPI, COLORE, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:il-colore}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@cangiante.example', '+39 011 000 7321', 'tel:+390110007321'
LAT, LNG = '45.0640', '7.6890'

V_PRISMI, V_IRIDE, V_BLU, V_BOLLA, V_ROSA, V_NEON, V_CORNICE, V_ARCO = (
    'pexels-video-20537241', 'pexels-video-26051329', 'pexels-video-26560379', 'pexels-video-6175779', 'pexels-video-4455251',
    'pexels-video-6835886', 'pexels-video-7315684', 'pexels-video-4431321')
VIDEOS = [V_PRISMI, V_IRIDE, V_BLU, V_BOLLA, V_ROSA, V_NEON, V_CORNICE, V_ARCO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
GIA = f('19138633', '10380590', '20669305', '33798239', '19592497', '19592494')
IRI = f('36450616', '5028493', '1998922', '33798236', '13057817')
COL = f('8973473', '2820585', '27677908', '7070775', '38606019', '4269552', '30960130')
NEO = f('7315526', '7315881', '18854542', '19149284')
PRI = f('32489243', '11734794', '5583574', '32489613')
RAS = f('37842436', '36267071', '36193665', '36267051', '37942452')
IMP = f('15063531', '5400093', '18005709', '2757520', '17540100')
SFO = f('7699265', '8041317', '7697299', '6651643', '30057726', '8206740', '6651644')
CAM = f('7264856', '18118237', '34438493', '33216831')
GRA = f('36267050', '36267070', '38394972', '36193672')
LOGO = 'logo-cangiante-chiaro.png'
PHOTOS = GIA + IRI + COL + NEO + PRI + RAS + IMP + SFO + CAM + GRA
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO, 'logo-cangiante-scuro.png'}

K = Kit(ID, MEDIA, raggio=18, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, accento_su_media='#FFFFFF')
C = K.c
# Gli accenti seguono il testo e i pulsanti pieni sono testo e fondo scambiati: su ogni tinta della pagina si leggono.
C['acc'] = TOK['testo']
C['on_acc'] = TOK['fondo']
C['sala'] = TOK['fondo']
MONO = 'Fira Code'
VOCI = [('map-pin', 'Via Mazzini 22 · Torino', NEGOZI + '#atelier'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def luce(t):
    """La Luce di pagina a tinta piena: un decoratore senza misure, dentro l'apertura."""
    return t.classic('pagelight', {'forma': 'tinta', 'light_color': SCURO[0], 'base_color': '', 'transition_ms': 900})


def tinge(sezioni):
    """Ogni sezione la sua tinta, in giro; quelle con una foto o un video restano scure col testo bianco, e il fondo pieno
    delle sezioni senza media si toglie (deve vedersi la tinta della pagina)."""
    k = 0
    for sez in sezioni:
        s = sez['settings']
        bg = s.get('bg')
        if s.get('light_color'):
            continue
        if isinstance(bg, dict) and bg.get('type') in ('image', 'video'):
            s['light_color'], s['light_text'] = SCURO
            continue
        s.pop('bg', None)
        s.pop('bg_scope', None)
        s['light_color'], s['light_text'] = TINTE[k % len(TINTE)]
        k += 1
    return sezioni


def build_header():
    sez = M.testata(K, Tpl(ID, 'header'), 'Cangiante', LOGO, 210, overlay=True, scuro=True)
    s = sez[0]['children'][0]['children'][0]['children'][0]['settings']
    # la barra e il menu del telefono prendono il fondo della pagina, che cambia tinta: il testo cambia con lui
    s.update({'sticky_bg': TOK['fondo'], 'mobile_bg': TOK['fondo'], 'logo_image': '', 'logo_text': 'cangiante',
              'logo_text_font': 'heading', 'logo_text_size': '24', 'logo_text_weight': '800', 'logo_text_spacing': -0.5,
              'logo_text_color': TOK['testo'], 'logo_mark': 'rainbow', 'logo_mark_color': TOK['testo']})
    return sez


def build_footer():
    t = Tpl(ID, 'footer')
    sez = M.piede(K, t, LOGO, '1412/192', 'Cangiante fa a Torino giacche e impermeabili che cambiano colore: con la luce, col '
                  'calore, col sole. Anche questo sito: ogni sezione la sua tinta.',
                  [('La collezione', CAPI), ('Il colore', COLORE), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                  'L’atelier', 'Via Mazzini 22, Torino<br>Martedì — sabato 11 — 19', VOCI,
                  '© 2026 Cangiante · Torino · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')],
                  larghezza=520, fondo=TOK['fondo'])
    # il logo a colori sta su un riquadro scuro fisso: si legge su ogni tinta
    riga = sez[0]['children'][1]
    riga['children'][0]['style'] = {'bg': solid(INCHIOSTRO), 'padding_top': 28, 'padding_right': 32, 'padding_bottom': 28,
                                    'padding_left': 32, 'border_radius': {'tl': 18, 'tr': 18, 'br': 18, 'bl': 18}}
    riga['settings']['layout'] = 'custom'
    riga['settings']['custom_widths'] = '100'
    return sez


# ── L'apertura: il video che cambia tinta da solo ──
def apertura(t):
    m = K.mob
    giro = 9000

    def veli_colore(sid):
        # quattro tinte in fusione «colore» sopra il video (sotto i testi): ognuna sale e scende a turno, il video cambia colore
        out = []
        for k, col in enumerate(['#D7FF3A', '#7B5CFF', '#FF4FB3', '#2CE5C0']):
            a = k * 25
            punti = [(0, {'opacity': 0}), (int(giro * a / 100), {'opacity': 0}),
                     (int(giro * (a + 12.5) / 100), {'opacity': 70}), (int(giro * min(100, a + 25) / 100), {'opacity': 0}),
                     (giro, {'opacity': 0})]
            punti = sorted({p[0]: p for p in punti}.values(), key=lambda p: p[0])
            out.append(K.livello('%s-tinta%d' % (sid, k), tipo='shape', x=0, y=0, w=100, h=100, bgColor=col, blendMode='color',
                                 entra='fadeIn', ritardo=0, durata=400, esce='fadeOut', opacity=100,
                                 timeline=K.tl(0, 0, punti, giro, loop=True)))
        return out

    def testi(sid, r1, r2, sotto):
        punti = []
        for k, col in enumerate(['#D7FF3A', '#7B5CFF', '#FF4FB3', '#2CE5C0']):
            punti.append(K.livello('%s-c%d' % (sid, k), tipo='shape', x=43.6 + k * 3.6, y=78, w=2.4, h=4.27, bgColor=col,
                                   borderRadius=999, entra='popUpBack', ritardo=1100 + k * 120, durata=600,
                                   responsive={'mobile': {'x': 32 + k * 9.5, 'y': 74, 'width': 6, 'height': 2.6}}))
        return [
            K.livello(sid + '-o', contenuto='Cangiante · giacche che cambiano colore · Torino', x=10, y=24, w=80, size=13,
                      peso='500', colore=mix('#FFFFFF', 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO,
                      ritardo=200, textAlign='center', responsive={'mobile': {'fontSize': m(10), 'y': 30, 'x': 6, 'width': 88}}),
            K.livello(sid + '-t1', contenuto=r1, tag='h1', x=4, y=32, w=92, size=118, peso='800', colore='#FFFFFF',
                      lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=350, durata=1000, textAlign='center',
                      responsive={'mobile': {'fontSize': m(40), 'y': 35, 'x': 4, 'width': 92}}),
            K.livello(sid + '-t2', fontFamily='Unbounded', contenuto=r2, tag='p', x=4, y=48, w=92, size=118, peso='800',
                      colore='#FFFFFF', lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=550, durata=1000,
                      textAlign='center', textStrokeWidth=0, responsive={'mobile': {'fontSize': m(34), 'y': 41, 'x': 4, 'width': 92}}),
            K.livello(sid + '-s', contenuto=sotto, x=27, y=66, w=46, size=17, peso='400', colore=mix('#FFFFFF', 92),
                      lineHeight=1.55, ritardo=800, textAlign='center',
                      responsive={'mobile': {'fontSize': m(13), 'y': 53, 'x': 8, 'width': 84}}),
            K.livello(sid + '-b', tipo='button', contenuto='La collezione', x=42.4, y=86, w='auto', size=14, peso='700',
                      colore=INCHIOSTRO, bgColor='#FFFFFF', padding=16, borderRadius=999, entra='fadeInUp', ritardo=1300,
                      buttonUrl=CAPI, hover={'bgColor': '#D7FF3A', 'duration': 250},
                      responsive={'mobile': {'x': 28, 'y': 80, 'fontSize': m(12)}}),
        ] + punti

    sl = [K.diapositiva('cg-1', K.sfondo_video(V_NEON, overlay=0.25, colore='#0D0B14'),
                        veli_colore('cg-1') + K.veli('cg-1-v', 'basso', 30, 100, colore='#0D0B14', forza=70, mobile_da=26)
                        + testi('cg-1', 'Cambia colore', 'mentre lo guardi.', 'Giacche e impermeabili in tessuti iridescenti, '
                                'termocromici e fotocromici. E un sito che fa lo stesso: scorrete.'), durata=9000),
          K.diapositiva('cg-2', K.sfondo_video(V_PRISMI, overlay=0.25, colore='#0D0B14'),
                        veli_colore('cg-2') + K.veli('cg-2-v', 'basso', 30, 100, colore='#0D0B14', forza=70, mobile_da=26)
                        + testi('cg-2', 'Al sole è viola,', 'all’ombra è verde.', 'Il colore lo decide la luce: noi '
                                'scegliamo solo da dove parte.'), durata=9000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='rotateSlide',
                       durata_trans=1200, autoplaySpeed=9000, showArrows=False, showDots=True, dotStyle='dash',
                       showProgressBar=False, parallax=False, heightMobile={'mode': 'px', 'value': 1300})


def tre_colori(t, voci):
    """Tre colonne: la foto e come cambia (voci = [(foto, titolo, testo)])."""
    cols = [t.col([K.image(t, f_, ti, ratio='4/5'), K.headline(t, ti, size=28, tag='h3', pad_bottom=6),
                   K.text(t, tx, size=15, color=C['testo'])], wm='1-3', ws='1-1') for f_, ti, tx in voci]
    return t.row(cols, layout='33-33-33', gap=28, valign='start')


def schede_fisse(nodo):
    """Le schede della squadra stanno su un fondo bianco fisso: il ruolo (che seguirebbe il testo della pagina) prende il viola."""
    if nodo.get('type') == 'team':
        nodo['settings']['role_color'] = VIOLA
    for c in nodo.get('children') or []:
        schede_fisse(c)
    return nodo


def capi(t, voci, colonne=4):
    return M.capi(K, t, voci, NEGOZI, colonne=colonne, sfondo='#FFFFFF', testo=INCHIOSTRO, tinta=VIOLA)


def build_home():
    t = Tpl(ID, 'home')
    eroe = K.apertura(t, apertura(t))
    eroe['settings']['light_color'], eroe['settings']['light_text'] = SCURO
    eroe['children'][0]['children'][0]['children'].append(luce(t))
    out = [eroe]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Cangiante · Torino, via Mazzini 22', ['Lo stesso giubbotto,', 'sei colori.'], size=64,
                                layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Il tessuto ha dentro una pellicola sottile come una bolla di sapone: la luce ci rimbalza e il '
                                'colore cambia con l’angolo. Gli impermeabili termocromici cambiano col calore della mano, '
                                'quelli fotocromici col sole. Anche questo sito cambia colore: è il nostro campionario.',
                              size=17, color=C['testo']),
                      K.button(t, 'Come funziona', COLORE, kind='pieno', pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro capi,', 'nessun colore fisso.'], size=56)]),
        t.one([capi(t, [
            (GIA[0], 'Giacche', 'Bolla', '', 'Nylon iridescente: dal verde al viola girandosi.', '280 €', 'nuova'),
            (IMP[0], 'Impermeabili', 'Termo', '', 'Rosa a 20 gradi, lilla sotto la mano.', '240 €', ''),
            (GIA[2], 'Giubbotti', 'Riflesso', '', 'Argento al buio, arcobaleno col flash.', '320 €', ''),
            (IRI[1], 'Abiti', 'Iride', '', 'Raso cangiante, cucito in un solo pezzo.', '260 €', '')])]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il colore', ['Tre modi', 'di cambiare.'], size=56)]),
        tre_colori(t, [(PRI[1], 'Con la luce', 'Iridescente: il colore dipende da dove guardate. Davanti è verde, di lato viola.'),
                       (RAS[2], 'Col calore', 'Termocromico: dove la mano lo scalda, il tessuto schiarisce per qualche minuto.'),
                       (SFO[3], 'Col sole', 'Fotocromico: grigio in casa, di colore pieno appena si esce.')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Per strada', 'cambia ancora.'], size=56)]),
        P.fila_foto(K, t, [(COL[0], 'Il giubbotto blu elettrico'), (GIA[4], 'L’impermeabile iridescente'),
                           (COL[3], 'L’abito che cambia col passo'), (NEO[2], 'Il giubbotto sotto il neon')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    nastro = t.section([t.one([M.nastro(K, t, ['Lime', 'Viola', 'Rosa', 'Menta', 'Giallo', 'Arancio', 'Di nuovo lime'],
                                        fondo=INCHIOSTRO, colore='#FFFFFF', size=58)], anim=None)],
                       width='fullbleed', padding='remove-vertical')
    nastro['settings']['light_color'], nastro['settings']['light_text'] = SCURO
    out.append(nastro)
    out.append(M.lettera(K, t, 'La lettera', ['Un colore nuovo', 'ogni mese.'],
                         'Le collezioni, i tessuti che proviamo, i negozi dove arrivano. Una volta al mese.'))
    return tinge(out)


def build_collezione():
    t = Tpl(ID, 'collezione')
    intro = K.intro(t, 'La collezione', ['Otto capi', 'senza un colore.'],
                    'Nelle foto li vedete con un colore: dal vivo ne hanno sei. I prezzi sono quelli consigliati.',
                    bg=K.photo_bg(GRA[0]), size=88)
    intro['children'][0]['children'][0]['children'].append(luce(t))
    out = [intro]
    out.append(t.section([
        t.one([K.sechead(t, 'Giacche e giubbotti', ['Iridescenti:', 'cambiano con la luce.'], size=56)]),
        t.one([capi(t, [
            (GIA[0], 'Giacche', 'Bolla', '', 'Nylon iridescente, dal verde al viola.', '280 €', 'nuova'),
            (GIA[2], 'Giubbotti', 'Riflesso', '', 'Argento al buio, arcobaleno col flash.', '320 €', ''),
            (GIA[5], 'Giacche', 'Petrolio', '', 'Nero che diventa blu e oro.', '300 €', ''),
            (IRI[3], 'Gilet', 'Scaglia', '', 'Le scaglie girano col movimento.', '190 €', '')])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Impermeabili e abiti', ['Termocromici:', 'cambiano col calore.'], size=56)]),
        t.one([capi(t, [
            (IMP[0], 'Impermeabili', 'Termo', '', 'Rosa a 20 gradi, lilla sotto la mano.', '240 €', ''),
            (IMP[2], 'Impermeabili', 'Sole', '', 'Grigio in casa, giallo appena esce.', '250 €', ''),
            (IRI[1], 'Abiti', 'Iride', '', 'Raso cangiante, un solo pezzo.', '260 €', ''),
            (COL[2], 'Tute', 'Prisma', '', 'Sei colori in una tuta da lavoro.', '230 €', 'ultime')])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_IRIDE, 'Il tessuto', 'Una bolla <em>di sapone,</em> da indossare.',
                             'Una pellicola sottile su nylon riciclato: la luce rimbalza fra i due strati.', overlay=20))
    return tinge(out)


def build_colore():
    t = Tpl(ID, 'il-colore')
    intro = K.intro(t, 'Il colore', ['Lo decide', 'la luce.'],
                    'Non tingiamo un colore: costruiamo come cambierà. Tre tecniche, tre laboratori, un atelier a Torino.',
                    bg=K.video_bg(V_BLU), size=88)
    intro['children'][0]['children'][0]['children'].append(luce(t))
    out = [intro]
    out.append(t.section([
        t.one([K.sechead(t, 'Le tre tecniche', ['Luce, calore,', 'sole.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'Iridescenza', 'Una pellicola di pochi millesimi di millimetro rimanda la luce in colori '
                                                     'diversi secondo l’angolo, come le ali di una farfalla.'),
                              ('02', 'Termocromia', 'Pigmenti che diventano trasparenti sopra i 28 gradi: dove la mano scalda, '
                                                    'appare il colore che c’è sotto.'),
                              ('03', 'Fotocromia', 'Pigmenti che si accendono coi raggi del sole e si spengono al chiuso, in '
                                                   'mezzo minuto.')], colonne=3)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In atelier', ['Chi sceglie', 'da che colore partire.'], size=56)]),
        schede_fisse(K.squadra(t, [(SFO[2], 'Chiara Ferrero', 'Fondatrice', 'Ha cucito la prima giacca Bolla con una tenda da doccia, nel 2020.',
                       'center 25%'),
                      (NEO[1], 'Tomás Rivera', 'Tessuti', 'Prova ogni pellicola sotto dieci luci diverse.', 'center 25%'),
                      (COL[5], 'Aisha Diallo', 'Modellista', 'Taglia in modo che le cuciture seguano il colore.', 'center 25%')],
                  ratio='4/5', size='320', sfondo='#FFFFFF', testo=INCHIOSTRO)),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, CAM[0], 'Un camaleonte sul ramo', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'La prima volta che l’ho portata a una festa, a metà serata qualcuno mi ha chiesto se mi '
                                        'ero cambiata.', 'Giorgia, Torino', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return tinge(out)


def build_negozi():
    t = Tpl(ID, 'negozi')
    intro = K.intro(t, 'I negozi', ['Dove vederli', 'cambiare.'],
                    'Un capo cangiante va visto dal vivo, sotto una luce vera. Ecco dove trovarli, e il nostro atelier.',
                    bg=K.photo_bg(SFO[5]), size=88)
    intro['children'][0]['children'][0]['children'].append(luce(t))
    out = [intro]
    out.append(M.negozi(K, t, [
        ('Torino', 'Atelier Cangiante', 'via Mazzini 22'), ('Milano', 'Riflesso', 'via Tortona 31'),
        ('Genova', 'Bolla', 'via San Bernardo 12'), ('Bologna', 'Prisma', 'via Pescherie Vecchie 5'),
        ('Roma', 'Iride', 'via del Governo Vecchio 80'), ('Bari', 'Arcobaleno', 'via Sparano 44'),
        ('Berlino', 'Schillern', 'Torstraße 99'), ('Tokyo', 'Tamamushi', 'Harajuku 3-21')],
        nota='L’elenco si aggiorna a ogni collezione.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un capo\nVendere Cangiante\nLa stampa\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Due righe,', 'rispondiamo noi.'],
                                'Per un capo, per venderci nel vostro negozio, per una domanda sul colore.', VOCI, campi,
                                'Cangiante — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’atelier', ['In via Mazzini,', 'sotto la luce vera.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Orari', 'martedì — sabato 11 — 19'), ('A piedi', 'da piazza Vittorio, cinque minuti'),
                                  ('Cosa si fa', 'si provano i capi alla finestra e al sole')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Cangiante · atelier', scuro=True, hex_marker='#D7FF3A')], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='atelier', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sul colore.'], [
        ('Il colore dura?', 'Sì: la pellicola e i pigmenti reggono cento lavaggi a trenta gradi.'),
        ('Si può scegliere il colore?', 'Si sceglie da che colore parte. Dove arriva lo decidono la luce e il calore.'),
        ('In foto è diverso?', 'Sempre: ogni foto ferma un colore solo. Per questo li vendono i negozi.'),
        ('Spedite?', 'No: si provano dal vivo. Il negozio più vicino può farsi mandare la taglia.'),
    ], 'cg-faq', bg=False))
    return tinge(out)


def build_404():
    t = Tpl(ID, '404')
    sez = t.section([
        K.centered(t, [K.label(t, 'Errore 404 · colore sconosciuto', color='#FFFFFF', align='center'),
                       K.headline(t, 'Questa pagina <em>ha cambiato colore</em> e non si trova più.', size=64, tag='h1',
                                  align='center', pad_bottom=20, color='#FFFFFF', accento='#D7FF3A'),
                       K.text(t, 'Torniamo dove i colori si sanno.', size=17, align='center', color=mix('#FFFFFF', 90)),
                       K.button(t, 'Torna alla home', HOME, kind='filo', align='center', chiaro=True)]),
    ], bg=K.video_bg(V_ARCO, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})
    return [sez]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il sito che cambia colore',
    ['moda', 'abbigliamento', 'iridescente', 'marchio', 'torino', 'colori', 'cambio colore', 'slider pro'],
    'Un marchio di giacche e impermeabili che cambiano colore, e un sito che fa lo stesso: ogni sezione riempie la pagina '
    'con la sua tinta, lime, viola, rosa, menta, giallo, e il testo cambia con lei, testata compresa. In apertura il video '
    'cambia tinta da solo; il cursore è un prisma che scompone la luce.',
    [('La collezione', CAPI), ('Il colore', COLORE), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('il-colore', 'Il colore', 'il-colore'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Unbounded\', system-ui, sans-serif',
    '\'Fira Code\', ui-monospace, Menlo, monospace', ['Manrope', 'Unbounded', 'Fira Code'], corsivo_di=[],
    peso_titoli='700', raggio=18, grana=0, titoli_line='1.0', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'prisma'
THEME['cursor']['ring_color'] = '#FFFFFF'
THEME['cursor']['dot_color'] = '#7B5CFF'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'il-colore.json': build_colore(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
