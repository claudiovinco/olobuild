# -*- coding: utf-8 -*-
"""Genera il tema «Fuori Registro» (id fuori-registro): una stamperia di Bologna che serigrafa magliette e borse a mano,
un colore alla volta, e tiene apposta un millimetro di errore.

La trovata: «la stampa va a registro». In apertura il nome è stampato tre volte, ciano, magenta e giallo, sfasate e
storte come una prova di stampa sbagliata; scorrendo le tre lastre scivolano una sopra l'altra fino a combaciare e il
nome diventa bianco e netto (tre livelli dello Slider Pro con la fusione «schermo»). Più giù una parola stampata sopra la
foto in differenza, tre nastri nei tre colori che corrono in versi opposti e la prova di stampa prima e dopo.

Tema chiaro: carta, nero inchiostro, magenta e ciano. Titoli in Anton, testo in Work Sans, dati in Space Mono.
Logo: FUORI REGISTRO in tre colori sfasati, col segno di registro. Cursore: registro (tre colori che inseguono il segno).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'fuori-registro'
NAME = 'Fuori Registro'

HEX = {
    'primary': '#C20066', 'primary_contrast': '#FFFFFF', 'secondary': '#007FAD', 'secondary_contrast': '#FFFFFF',
    'accent': '#F2C300', 'dark': '#121212', 'light': '#FFFFFF', 'muted': '#EAE4D6', 'muted_contrast': '#161616',
    'text': '#161616', 'text_muted': '#55524C', 'background': '#F4F0E6', 'border': '#D2CBBB', 'link': '#C20066',
}
CIANO, MAGENTA, GIALLO = '#00B7F0', '#FF2E98', '#FFD84A'

HOME, CAPI, TELAIO, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:il-telaio}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'stampa@fuoriregistro.example', '+39 051 000 2290', 'tel:+390510002290'
LAT, LNG = '44.5010', '11.3480'

V_PRESSA, V_RACLA, V_RACLA2, V_ROSA, V_NERA, V_DUE, V_VORTICE, V_MARMO, V_BANCO = (
    'pexels-video-34888657', 'pexels-video-38826404', 'pexels-video-34356283', 'pexels-video-38298253', 'pexels-video-7325836',
    'pexels-video-4065932', 'pexels-video-11749691', 'pexels-video-11749692', 'pexels-video-34356281')
VIDEOS = [V_PRESSA, V_RACLA, V_RACLA2, V_ROSA, V_NERA, V_DUE, V_VORTICE, V_MARMO, V_BANCO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
TELA = f('27893069', '27893067', '27893044', '27893026', '3966277', '33284506', '3966272', '27893033')
MODE = f('11931375', '14941602', '14798947', '12922529', '5560613', '36908562', '38402003')
MAGL = f('32963962', '18805427', '37026122', '36908588', '36908564', '37075908')
BARA = f('35658360', '6920160', '4348164', '15013713')
BOTT = f('6620992', '9313700', '5642074', '6621000')
MANIF = f('9712702', '331986', '6475460', '35658700')
COLO = f('7676822', '10757430', '38499194', '7676831')
PILA = f('8346218', '18186105', '30940601', '4440574')
BORS = f('9393350', '32033029', '18347938')
MANI = f('31092838', '10617290')
LOGO_S, LOGO_C = 'logo-fuori-registro-scuro.png', 'logo-fuori-registro-chiaro.png'
PHOTOS = TELA + MODE + MAGL + BARA + BOTT + MANIF + COLO + PILA + BORS + MANI
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media=GIALLO)
C = K.c
MONO = 'Space Mono'
VOCI = [('map-pin', 'Via del Pratello 54 · Bologna', NEGOZI + '#stamperia'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Fuori Registro', LOGO_S, 210, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '1240/208', 'Fuori Registro serigrafa a mano magliette e borse a Bologna, un colore alla volta, '
                   'con inchiostri ad acqua. Il millimetro di errore lo teniamo: è la firma.',
                   [('La collezione', CAPI), ('Il telaio', TELAIO), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La stamperia', 'Via del Pratello 54, Bologna<br>Lunedì — venerdì 10 — 18', VOCI,
                   '© 2026 Fuori Registro · Bologna · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')],
                   larghezza=480, fondo=C['sala'])


# ── L'apertura: tre lastre che vanno a registro ──
def apertura(t):
    m = K.mob
    lastre = [(CIANO, -80, -34, -2.2), (MAGENTA, 70, 24, 1.6), (GIALLO, -24, 58, 0.9)]
    livelli = K.veli('fr-velo', 'basso', 30, 64, colore=C['nero'], forza=78, mobile_da=50)
    for riga, (parola, y, ym) in enumerate([('FUORI', 14, 10), ('REGISTRO', 40, 16)]):
        for k, (col, dx, dy, r) in enumerate(lastre):
            livelli.append(K.livello(
                'fr-%d%d' % (riga, k), contenuto=parola, tag='h1' if (riga, k) == (0, 0) else 'p', x=6, y=y, w=90, size=230,
                peso='400', colore=col, fontFamily='Anton', lineHeight=0.9, letterSpacing=2, blendMode='screen', entra='fadeIn', ritardo=100 + k * 120,
                durata=700,
                scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': dx, 'y': dy, 'r': r}, {'p': 12, 'x': dx, 'y': dy, 'r': r},
                                                                {'p': 64, 'x': 0, 'y': 0, 'r': 0}, {'p': 100, 'x': 0, 'y': 0, 'r': 0}]},
                responsive={'mobile': {'fontSize': m(76), 'x': 6, 'y': ym, 'width': 90}}))
    livelli += [
        K.livello('fr-o', contenuto='Fuori Registro · magliette serigrafate a mano · Bologna', x=6, y=8, w=60, size=13, peso='700',
                  colore=mix('#FFFFFF', 88), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=300,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 6, 'width': 88}}),
        K.livello('fr-s', contenuto='Scorrete: i tre colori vanno a registro.', x=6, y=74, w=40, size=18, peso='400',
                  colore=mix('#FFFFFF', 92), ritardo=900, scrollMove={'inizio': 0, 'fine': 26, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 28.5, 'width': 88}}),
    ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 70, 'o': 0}, {'p': 86, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('fr-f1', contenuto='Stampate a mano, un colore alla volta.', tag='p', x=6, y=70, w=50, size=30, peso='500',
                  colore='#FFFFFF', lineHeight=1.2, scrollMove=fine, entra='fadeIn', ritardo=0,
                  responsive={'mobile': {'fontSize': m(17), 'x': 6, 'y': 26.5, 'width': 88}}),
        K.livello('fr-b', tipo='button', contenuto='La collezione', x=6, y=82, w='auto', size=12, peso='700', colore='#FFFFFF',
                  bgColor=C['acc'], padding=16, borderRadius=0, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 31, 'fontSize': m(11)}}),
        K.livello('fr-l', tipo='button', contenuto='Il telaio', x=20, y=82, w='auto', size=12, peso='700', colore='#FFFFFF',
                  bgColor='transparent', padding=16, borderRadius=0, borderWidth=1, borderColor=mix('#FFFFFF', 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=TELAIO, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix('#FFFFFF', 14), 'borderColor': '#FFFFFF', 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'x': 55, 'y': 31, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    sl = [K.diapositiva('fr-1', K.sfondo_video(V_PRESSA, overlay=0.35, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def parola_in_differenza(t, testo, foto):
    """Una parola enorme stampata sopra la foto in «differenza»: dove passa sopra i colori si rovesciano."""
    return t.section([t.one([t.classic('blendtext', {
        'text': testo, 'tag': 'p', 'font_size': '300', 'font_size_tablet': '190', 'font_size_mobile': '92', 'font_weight': '400',
        'font_family': 'heading', 'text_transform': 'uppercase', 'letter_spacing': '2', 'line_height': '0.9', 'text_align': 'center',
        'text_color': '#FFFFFF', 'blend_mode': 'difference', 'mode': 'text',
        'tile_padding': {'top': 10, 'right': 0, 'bottom': 10, 'left': 0}}, style={})], anim=None)],
        bg=K.photo_bg(foto, overlay=0), padding='xlarge', width='fullbleed', style={'padding_top': 220, 'padding_bottom': 220})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Fuori Registro · Bologna, via del Pratello 54', ['Un millimetro', 'di errore, apposta.'],
                                size=72, layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Ogni maglietta passa sotto il telaio tre volte: ciano, magenta, giallo. Le lastre si allineano '
                                'a mano, a occhio, coi segni di registro negli angoli; il millimetro che sfugge resta, ed è lui '
                                'che fa capire che è stampata da una persona.', size=17),
                      K.button(t, 'Il telaio', TELAIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(parola_in_differenza(t, 'In registro', COLO[0]))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro stampe,', 'tre colori ognuna.'], size=60)]),
        t.one([M.capi(K, t, [
            (MODE[1], 'Magliette', 'Lastra', '', 'Il segno di registro stampato in grande.', '45 €', 'nuova'),
            (MAGL[2], 'Magliette', 'Tre Volte', '', 'La stessa onda in ciano, magenta e giallo.', '45 €', ''),
            (MODE[4], 'Felpe', 'Retino', '', 'I punti del retino ingranditi cento volte.', '85 €', ''),
            (BORS[0], 'Borse', 'Prova', '', 'Le prove di stampa, una per borsa.', '30 €', '')], CAPI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    for testo, fondo, colore, verso in [(['Ciano', 'Prima lastra', 'Il cielo'], CIANO, C['nero'], 'left'),
                                        (['Magenta', 'Seconda lastra', 'Il fiore'], MAGENTA, C['nero'], 'right'),
                                        (['Giallo', 'Terza lastra', 'Il sole'], GIALLO, C['nero'], 'left')]:
        out.append(t.section([t.one([M.nastro(K, t, testo, fondo=fondo, colore=colore, size=54, verso=verso, altezza=78)],
                                    anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'Prova e stampa', ['Trascinate:', 'prima e dopo il registro.'], size=60)]),
        t.one([M.dritto_rovescio(K, t, [
            (TELA[1], TELA[0], 'Il telaio', 'La lastra prima della racla, e dopo.'),
            (MAGL[0], MAGL[3], 'La Tre Volte', 'La prova fuori registro e la maglietta buona.'),
            (MANIF[0], MANIF[2], 'I manifesti', 'Le prove appese al muro, e quelle scelte.')], prima='Prova', dopo='Stampa')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_RACLA, 'La racla', 'Una passata <em>per colore.</em>',
                             'Tre passate di racla per maglietta, a mano. Duecento magliette al giorno, quando va bene.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Addosso', ['Stampate,', 'e poi indossate.'], size=60)]),
        P.fila_foto(K, t, [(MODE[0], 'La Lastra'), (MODE[2], 'La Tre Volte'), (MODE[5], 'La Retino'), (MODE[6], 'La Prova')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Una stampa nuova', 'ogni mese.'],
                         'Le stampe nuove, le tirature limitate, le serate in stamperia col telaio aperto. Una volta al mese.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Otto stampe,', 'tre colori.'],
                   'Cotone biologico pesante, inchiostri ad acqua, tirature di cento pezzi numerati a mano sull’etichetta. '
                   'I prezzi sono quelli consigliati.', bg=K.video_bg(V_VORTICE, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Magliette', ['Tre passate', 'di racla.'], size=60)]),
        t.one([M.capi(K, t, [
            (MODE[1], 'Magliette', 'Lastra', '', 'Il segno di registro in grande.', '45 €', 'nuova'),
            (MAGL[2], 'Magliette', 'Tre Volte', '', 'L’onda in tre colori.', '45 €', ''),
            (MAGL[4], 'Magliette', 'Bozza', '', 'La prima prova, stampata per sbaglio.', '40 €', ''),
            (MODE[6], 'Magliette', 'Nero Pieno', '', 'I tre colori uno sopra l’altro.', '50 €', '')], NEGOZI, tinta=C['acc'])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(parola_in_differenza(t, 'Fuori', TELA[3]))
    out.append(t.section([
        t.one([K.sechead(t, 'Felpe e borse', ['La stampa', 'più grande.'], size=60)]),
        t.one([M.capi(K, t, [
            (MODE[4], 'Felpe', 'Retino', '', 'I punti del retino ingranditi.', '85 €', ''),
            (MODE[3], 'Felpe', 'Segno', '', 'Il segno di registro sulla schiena.', '85 €', ''),
            (BORS[0], 'Borse', 'Prova', '', 'Una prova di stampa per borsa.', '30 €', ''),
            (BORS[2], 'Borse', 'Telaio', '', 'Il telaio disegnato, a grandezza vera.', '30 €', 'ultime')], NEGOZI, tinta=C['acc'])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_telaio():
    t = Tpl(ID, 'il-telaio')
    out = [K.intro(t, 'Il telaio', ['Tre lastre,', 'una maglietta.'],
                   'La serigrafia è la stampa più semplice che ci sia: un telaio, una rete, l’inchiostro e una racla. Il resto '
                   'è mano e pazienza.', bg=K.video_bg(V_BANCO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si stampa', ['Ciano, magenta,', 'giallo.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'la lastra', TELA[4], 'Incidere', 'La lastra', 'alla luce', 'Il disegno si incide sulla rete con la luce: una lastra '
             'per ogni colore.', '3', 'lastre'),
            ('2', 'la racla', TELA[5], 'Stampare', 'La racla', 'a mano', 'L’inchiostro si tira con la racla: una passata decisa, '
             'senza fermarsi.', '3', 'passate'),
            ('3', 'il forno', TELA[7], 'Fissare', 'Il colore', 'nel forno', 'Un minuto a centosessanta gradi: il colore entra nel '
             'cotone e non va più via.', '160', 'gradi')], icona='printer')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In stamperia', ['Chi tira', 'la racla.'], size=60)]),
        K.squadra(t, [(MANI[0], 'Bruno Zanarini', 'Fondatore', 'Stampava manifesti per i concerti; poi qualcuno gli ha chiesto una '
                       'maglietta.', 'center 30%'),
                      (MODE[2], 'Giulia Testoni', 'Lastre', 'Incide le lastre e decide quanto può sbagliare il registro.',
                       'center 25%'),
                      (TELA[2], 'Amadou Diallo', 'Racla', 'Tira duecento passate al giorno, tutte uguali.', 'center 30%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, BARA[1], 'Gli inchiostri della stamperia', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho chiesto se potevano stamparla dritta. Mi hanno guardato come se avessi chiesto di '
                                        'togliere la firma a un quadro. L’ho presa storta, e ne ho prese altre due.',
                                  'Marta, Ferrara', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove trovarle,', 'fuori registro.'],
                   'Le nostre stampe nei negozi che amano le cose fatte a mano, e la stamperia aperta il venerdì.',
                   bg=K.photo_bg(BOTT[0]), size=96)]
    out.append(M.negozi(K, t, [
        ('Bologna', 'Stamperia', 'via del Pratello 54'), ('Bologna', 'Retino', 'via Mascarella 19'),
        ('Milano', 'Lastra', 'via Lecco 11'), ('Torino', 'Racla', 'via Santa Giulia 30'), ('Napoli', 'Inchiostro', 'via Bellini 7'),
        ('Roma', 'Telaio', 'via del Pigneto 88'), ('Berlino', 'Siebdruck', 'Weserstraße 50'), ('Lisbona', 'Serigrafia', 'Rua da Rosa 210')],
        nota='Il venerdì la stamperia è aperta: si può stampare la propria maglietta, con le nostre lastre.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una stampa per il mio gruppo\nIl venerdì in stamperia\nVendere Fuori Registro\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una stampa', 'per voi?'],
                                'Per stampare le magliette del vostro gruppo, venire il venerdì, o vendere le nostre stampe.', VOCI,
                                campi, 'Fuori Registro — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La stamperia', ['Al Pratello,', 'nel cortile.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'lunedì — venerdì 10 — 18'), ('Il venerdì', 'stamperia aperta 18 — 21'),
                                  ('A piedi', 'da Porta San Felice, cinque minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fuori Registro · la stamperia', scuro=False, hex_marker=HEX['primary'])], wm='1-2',
                     ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='stamperia', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sulle stampe.'], [
        ('Perché sono storte?', 'Perché le lastre si allineano a mano: il millimetro di errore è la nostra firma.'),
        ('Si lavano?', 'Sì, a trenta gradi e al rovescio: gli inchiostri ad acqua restano morbidi e non si crepano.'),
        ('Stampate per un gruppo?', 'Sì, da venti magliette in su, con le vostre lastre.'),
        ('Spedite?', 'Sì, in tutta Europa, in una busta di carta stampata anche lei.'),
    ], 'fr-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · stampa fallita', color=GIALLO, align='center'),
                       K.headline(t, 'Questa pagina è <em>fuori registro.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento=GIALLO),
                       K.text(t, 'Le lastre non combaciano: la buttiamo e ne stampiamo un’altra.', size=17, align='center',
                              color=mix('#FFFFFF', 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_MARMO, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La stampa che va a registro',
    ['moda', 'abbigliamento', 'magliette', 'serigrafia', 'bologna', 'chiaro', 'slider pro', 'fusione'],
    'Una stamperia che serigrafa magliette a mano: in apertura il nome è stampato tre volte in ciano, magenta e giallo, '
    'sfasato, e scorrendo le tre lastre combaciano finché il nome diventa bianco e netto; poi una parola stampata in '
    'differenza sopra la foto, tre nastri nei tre colori, la prova e la stampa da trascinare, il telaio e i negozi.',
    [('La collezione', CAPI), ('Il telaio', TELAIO), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('il-telaio', 'Il telaio', 'il-telaio'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Anton\', Impact, sans-serif',
    '\'Space Mono\', ui-monospace, Menlo, monospace', ['Work Sans', 'Anton', 'Space Mono'], corsivo_di=[],
    peso_titoli='400', raggio=0, grana=6, titoli_line='0.95', titoli_spazio='0.01em')
THEME['cursor']['style'] = 'registro'
THEME['cursor']['ring_color'] = HEX['text']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'il-telaio.json': build_telaio(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
