# -*- coding: utf-8 -*-
"""Genera il tema «Bandeja» (id bandeja): un circolo di padel a Riccione, sei campi coperti e due all'aperto, scuola
per adulti e bambini, tornei del venerdì e la terrazza per dopo la partita.

Tema chiaro e sportivo: bianco, blu campo e il giallo lime della pallina. Titoli in Funnel Display, testo in Funnel
Sans, dati e punteggi in Kode Mono.

Apertura: Slider Pro «la partita», alto 16:9. Il campo visto dall'alto (un video vero, ripreso col drone) è inclinato
in 3D come un pavimento in prospettiva, e sopra la pallina gioca uno scambio: parabola, rimbalzo dall'altra parte,
ritorno, con la sua ombra che corre sul campo. Una diapositiva per servizio del circolo (campi, corsi, tornei,
terrazza) con la foto che si inclina verso il puntatore.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'bandeja'
NAME = 'Bandeja'

HEX = {
    'primary': '#1F5FD1', 'primary_contrast': '#FFFFFF', 'secondary': '#0E7C66', 'secondary_contrast': '#FFFFFF',
    'accent': '#D6F04A', 'dark': '#0E1B2C', 'light': '#FFFFFF', 'muted': '#E4EBF2', 'muted_contrast': '#0F1D2E',
    'text': '#0F1D2E', 'text_muted': '#526177', 'background': '#F4F6F8', 'border': '#D2DAE4', 'link': '#1F5FD1',
}

HOME, CAMPI, CORSI, TORNEI = '{{PAGINA:home}}', '{{PAGINA:campi}}', '{{PAGINA:corsi}}', '{{PAGINA:tornei}}'
MAIL, PHONE, PHONE_TEL = 'campo@bandeja.example', '+39 0541 000 606', 'tel:+390541000606'
LAT, LNG = '43.9997', '12.6559'

V_DRONE, V_CAMPI, V_PALLINE, V_PARTITA = ('pexels-video-32210388', 'pexels-video-33444758', 'pexels-video-10340698',
                                          'pexels-video-992695')
PHOTOS = [
    'pexels-35248327.jpg', 'pexels-34079415.jpg', 'pexels-37978999.jpg', 'pexels-35248274.jpg', 'pexels-33095503.jpg',
    'pexels-38347564.jpg', 'pexels-1103829.jpg', 'pexels-38031043.jpg', 'pexels-33083425.jpg', 'pexels-38091066.jpg',
    'pexels-39072991.jpg', 'pexels-38090725.jpg', 'pexels-33111439.jpg', 'pexels-23542291.jpg', 'pexels-8224406.jpg',
    'pexels-20186417.jpg', 'pexels-13025827.jpg', 'pexels-8224761.jpg', 'pexels-8224638.jpg', 'pexels-32832525.jpg',
    'pexels-35248244.jpg', 'pexels-35248256.jpg', 'pexels-35248248.jpg', 'pexels-35248484.jpg', 'pexels-31559336.jpg',
    'pexels-31559337.jpg', 'pexels-31559379.jpg', 'pexels-36014315.jpg', 'pexels-11767213.jpg', 'pexels-12645170.jpg',
    'pexels-18473592.jpg', 'pexels-38896843.jpg', 'pexels-12645180.jpg', 'pexels-8224496.jpg', 'pexels-8223997.jpg',
    'pexels-8224405.jpg', 'pexels-8224716.jpg', 'pexels-8223955.jpg', 'pexels-31559351.jpg', 'pexels-6292457.jpg',
    'pexels-23379595.jpg', 'pexels-21050394.jpg',
]
VIDEOS = [V_DRONE, V_CAMPI, V_PALLINE, V_PARTITA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
LIME = C['acc2']
TITOLI, MONO = 'Funnel Display', 'Kode Mono'
VOCI = [('map-pin', 'Viale dei Mille 30 · Riccione', CAMPI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Bandeja', logo_size='24', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Bandeja', 'Circolo di padel a Riccione: sei campi coperti e due all’aperto, la scuola per adulti e '
                   'bambini, i tornei del venerdì e la terrazza sul viale. Si prenota anche all’ultimo minuto.',
                   [('I campi', CAMPI), ('I corsi', CORSI), ('I tornei', TORNEI), ('Prenota', CAMPI + '#prenota'),
                    ('Privacy', '/privacy-policy/')],
                   'Il circolo', 'Tutti i giorni 7 — 1<br>La terrazza dalle 17', VOCI,
                   '© 2026 Bandeja Padel asd · Riccione · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=140,
                   fondo=C['sala'])


SERVIZI = [  # linguetta, occhiello, riga 1, riga 2, testo, foto, didascalia, bottone, link
    ('Campi', '01 · i campi', 'Sei campi coperti,', 'aperti fino all’una.', 'Vetro temperato, erba sintetica nuova, luce '
     'senza ombre. Prenotate un’ora e mezza, anche all’ultimo minuto: se manca il quarto, ve lo troviamo noi.',
     'pexels-35248484.jpg', 'Sara, campo 3 · ore 19:30', 'Prenota un campo', CAMPI + '#prenota'),
    ('Corsi', '02 · la scuola', 'La bandeja', 'si impara in otto lezioni.', 'Gruppi di quattro per livello, con il '
     'maestro sempre in campo. La prima lezione è gratis, la racchetta ve la prestiamo noi.',
     'pexels-11767213.jpg', 'Diego, maestro · livello 3', 'Prova gratis', CORSI),
    ('Tornei', '03 · il venerdì', 'Il venerdì sera', 'si gioca la classifica.', 'Americano a coppie che cambiano, sedici '
     'giocatori, tre ore di partite brevi. Si vince in punti, si festeggia in terrazza.',
     'pexels-8223997.jpg', 'Junior · torneo d’autunno', 'Iscriviti', TORNEI),
    ('Terrazza', '04 · dopo', 'Dopo la partita,', 'la terrazza.', 'Spritz, centrifughe e piadine sul viale. La partita si '
     'commenta lì, guardando gli altri campi dalla vetrata.',
     'pexels-12645170.jpg', 'La terrazza · dalle 17', 'Il circolo', CAMPI + '#arrivare'),
]


def scambio():
    """La pallina: andata con rimbalzo dall'altra parte, ritorno con rimbalzo dalla nostra. (ms, x, y, y del campo sotto
    la pallina: il campo va da 76, vicino, a 58, lontano, e la pallina rimbalza dove lo tocca)"""
    punti = [(0, 52, 66), (300, 56, 52), (600, 60, 44), (900, 64, 42), (1200, 68, 48), (1450, 72, 64), (1700, 76, 52),
             (1950, 80, 47), (2200, 84, 50), (2500, 80, 43), (2800, 76, 40), (3100, 72, 42), (3400, 68, 50), (3650, 63, 60),
             (3900, 58, 72), (4150, 55, 67), (4400, 52, 66)]
    return [(ms, x, y, round(76 - 18 * ms / 2200 if ms <= 2200 else 58 + 18 * (ms - 2200) / 2200, 2)) for ms, x, y in punti]


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, foto, didascalia, bottone, link) in enumerate(SERVIZI):
        n = i + 1
        sl.append(K.diapositiva('bj-%d' % n, K.sfondo_video([V_PARTITA, V_CAMPI, V_PALLINE, V_PARTITA][i], overlay=0.6,
                                                            colore=C['fondo']),
                                K.veli('bj-velo%d' % n, 'sinistra', 50, 66) + [
            K.livello('bj-f%d' % n, tipo='image', imageSrc=K.img(foto), objectFit='cover', x=76, y=11.5, w=15, h=34,
                      borderRadius=22, borderWidth=6, borderColor=C['chiaro'], entra='zoomIn', ritardo=300, durata=900,
                      esce='fadeOut', parallax={'type': 'mouse', 'depth': 7, 'tilt3d': True, 'scrollSpeed': 0.5},
                      boxShadow={'x': 0, 'y': 24, 'blur': 50, 'spread': -16, 'color': mix(C['nero'], 35)},
                      responsive={'mobile': {'x': 56, 'y': 8.5, 'width': 37, 'height': 20}}),
            K.livello('bj-d%d' % n, contenuto=didascalia, x=58, y=13, w='auto', size=12, peso='500', colore=C['testo'],
                      fontFamily=MONO, bgColor=LIME, padding=10, borderRadius=999, entra='fadeInLeft', ritardo=900,
                      responsive={'mobile': {'x': 7, 'y': 26, 'fontSize': m(10)}}),
            K.livello('bj-o%d' % n, contenuto=occ, x=6, y=17, w=40, size=12, peso='500', colore=C['acc'], letterSpacing=2,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 41, 'x': 7, 'width': 86}}),
            K.livello('bj-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=21.5, w=50, size=62, peso='600',
                      colore=C['testo'], fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=350,
                      durata=1000, charAnim={'stagger': 22, 'offsetY': 46, 'rotation': -6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(31), 'y': 43.5, 'x': 7, 'width': 88}}),
            K.livello('bj-i%d' % n, contenuto=r2, tag='p', x=6, y=30.5, w=50, size=62, peso='600', colore=C['acc'],
                      fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=650, durata=1000,
                      charAnim={'stagger': 22, 'offsetY': 46, 'rotation': -6, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(31), 'y': 47.8, 'x': 7, 'width': 88}}),
            K.livello('bj-s%d' % n, contenuto=testo, x=6, y=44, w=33, size=17, peso='400', colore=C['tenue'],
                      lineHeight=1.65, ritardo=1000, responsive={'mobile': {'fontSize': m(14), 'y': 53.5, 'x': 7, 'width': 86}}),
            K.livello('bj-b%d' % n, tipo='button', contenuto=bottone, x=6, y=66, w='auto', size=15, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, ritardo=1300, buttonUrl=link,
                      hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 69, 'x': 7, 'fontSize': m(13)}}),
        ], durata=7500, tab=tab))
    # il campo dal drone, inclinato in 3D come un pavimento, con la pallina che gioca lo scambio
    cx, cy, cw, ch = 44.0, 42.0, 54.0, 52.0
    globali = [K.livello('bj-campo', tipo='video', videoSrc=K.img(V_DRONE + '.mp4'), videoAutoplay=True, videoMuted=True,
                         videoLoop=True, x=cx, y=cy, w=cw, h=ch, borderRadius=18,
                         boxShadow={'x': 0, 'y': 40, 'blur': 60, 'spread': -20, 'color': mix(C['nero'], 30)},
                         timeline=K.tl(cx, cy, [(0, {'rotationX': 58, 'rotation': -8, 'opacity': 0, 'scale': 0.9}, 'power3.out'),
                                                (1400, {'rotationX': 58, 'rotation': -8, 'opacity': 100, 'scale': 1})],
                                       1400, loop=False),
                         responsive={'mobile': {'x': 4, 'y': 76, 'width': 92, 'height': 16}})]
    punti = scambio()
    globali.append(K.livello('bj-ombra', tipo='shape', x=50, y=66, w=2.6, h=1.6, bgColor=mix(C['nero'], 26),
                             borderRadius=999,
                             timeline=K.tl(50, 66, [(ms, {'x': x + 0.0, 'y': yc + 2.2,
                                                          'scale': round(max(0.4, 1 - (yc - y) / 40), 2),
                                                          'opacity': 100}) for ms, x, y, yc in punti], 4400),
                             responsive={'mobile': {'visible': False}}))
    globali.append(K.livello('bj-palla', tipo='shape', x=50, y=48, w=2.5, h=4.44, bgColor=LIME, borderRadius=999,
                             borderWidth=2, borderColor=mix(C['chiaro'], 70),
                             boxShadow={'x': 0, 'y': 0, 'blur': 18, 'spread': 2, 'color': mix(LIME, 60)},
                             timeline=K.tl(50, 48, [(ms, {'x': x, 'y': y, 'rotation': ms / 10}) for ms, x, y, yc in punti], 4400),
                             responsive={'mobile': {'visible': False}}))
    globali.append(K.livello('bj-punti', contenuto='15 — 30', x=6, y=86, w=20, size=12, peso='500', colore=C['tenue'],
                             fontFamily=MONO, letterSpacing=2, entra='fadeIn', ritardo=1800,
                             responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='push',
                       durata_trans=1000, showArrows=False, showDots=False, showTabs=True, tabPosition='bottom',
                       parallax=False, autoplaySpeed=7500, progressBarColor=C['acc'], navColor=C['testo'],
                       globalBackground={'type': 'color', 'color': C['fondo']})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Bandeja · Riccione', ['Otto campi,', 'una comunità.'], size=64, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Abbiamo aperto nel 2021 in un vecchio capannone delle colonie. Oggi ci giocano più di mille '
                                'soci: chi ha cominciato a sessant’anni, chi a sei, chi viene in pausa pranzo. Se vi manca il '
                                'quarto, scrivete nel gruppo del circolo: in dieci minuti lo trovate.', size=17,
                             color=C['testo']),
                      K.button(t, 'Prenota un campo', CAMPI + '#prenota', kind='pieno', pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('8', 'Campi, sei coperti'), ('1.200', 'Soci'), ('7 — 1', 'Aperti ogni giorno'),
                            ('90′', 'Una partita')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([t.one([t.classic('marquee', {
        'content_type': 'text', 'text_items': 'Bandeja | Víbora | Chiquita | Globo | Bajada | Por tres | Remate',
        'separator': ' ● ', 'speed': '30', 'direction': 'left', 'pause_hover': True, 'gap': 34, 'velocity_skew': True,
        'bg_color': C['acc'], 'text_color': C['on_acc'], 'font_size': 48, 'font_weight': '600', 'letter_spacing': 0,
        'text_transform': 'none', 'font_family': 'heading', 'separator_color': LIME, 'height': 90, 'full_width': True})],
        anim=None)], width='fullbleed', padding='remove-vertical'))
    colpi = []
    for nome, cosa, foto in [
        ('Bandeja', 'Il colpo che dà il nome al circolo: una volée alta, tagliata, che tiene gli avversari in fondo. Il '
                    'vassoio, in spagnolo.', 'pexels-38347564.jpg'),
        ('Víbora', 'La bandeja cattiva: più taglio, più velocità, la palla che esce bassa dal vetro laterale.',
         'pexels-38031043.jpg'),
        ('Globo', 'Il pallonetto: alto sopra la coppia a rete, per riprendersi il centro del campo.', 'pexels-37978999.jpg'),
        ('Por tres', 'Lo smash che rimbalza così alto da uscire dal campo. Si festeggia al bar.', 'pexels-39072991.jpg')]:
        colpi.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 58}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': 'I colpi · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': nome, 'back_description': cosa,
            'back_bg': C['acc'], 'back_text_color': C['on_acc'], 'back_valign': 'center', 'back_cta_text': 'Impararlo',
            'back_cta_url': CORSI, 'flip_direction': 'vertical', 'flip_trigger': 'hover', 'card_height': '400',
            'card_border_radius': '14', 'card_shadow': 'md', 'title_size': '30', 'title_weight': '600', 'desc_size': '15'},
            style={})], wm='1-4', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Il vocabolario', ['Quattro colpi', 'da girare.'], size=60,
                         tagline='Il padel parla spagnolo. Girate le carte: dietro c’è che cosa vuol dire e come si gioca.')]),
        t.row(colpi, layout='25-25-25-25', gap=18),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_CAMPI, 'I campi', 'Sei campi, <em>una sola regola.</em>',
                             'Si prenota un’ora e mezza e si lascia il campo pulito: palline nel cesto, porta chiusa. Il '
                             'resto lo decidete voi, in campo.', overlay=58))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Ho cominciato a cinquantotto anni con il corso del martedì. Adesso gioco tre volte a settimana e al torneo '
                 'del venerdì ho battuto mio figlio.', 'author_name': 'Gabriella M.', 'author_role': 'Socia dal 2023',
        'avatar': '', 'rating': '5', 'text_color': C['testo'], 'star_color': C['acc'], 'author_color': C['tenue'],
        'quote_accent_color': C['acc'], 'quote_font': 'heading', 'quote_size': 38, 'author_uppercase': True,
        'quote_uppercase': False, 'layout': 'editorial', 'show_line': True, 'line_color': C['acc']}, style={})])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PARTITA, 'Aperti', 'Dalle sette <em>all’una di notte.</em>',
                          'Prima del lavoro, in pausa pranzo, dopo cena. I campi all’aperto si giocano da aprile a ottobre, '
                          'quelli coperti tutto l’anno.', 'Prenota', CAMPI + '#prenota', overlay=62, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Bandeja',
                page_intro_caption='Padel · Riccione', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1500, page_intro_once=True)


def build_campi():
    t = Tpl(ID, 'campi')
    out = [K.intro(t, 'I campi', ['Un’ora e mezza,', 'quattro giocatori.'],
                   'Si prenota fino a sette giorni prima. Chi è socio paga meno e può prenotare il giorno prima delle 20.',
                   bg=K.photo_bg('pexels-35248274.jpg', overlay=80, color=C['fondo']))]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Tutti i giorni', 'time': '7 — 1', 'note': 'Ultimo turno alle 23:30'},
                  {'day': 'Coperti', 'time': '6 campi', 'note': 'Tutto l’anno, riscaldati d’inverno'},
                  {'day': 'All’aperto', 'time': '2 campi', 'note': 'Da aprile a ottobre, con le luci'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': C['acc'], 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 44,
        'time_weight': '600', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    out.append(t.section([t.one([orari])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 60}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['A campo,', 'non a testa.'], size=56,
                         tagline='Il prezzo è per un’ora e mezza e si divide in quattro. Racchette a noleggio 3 €, palline nuove 6 €.')]),
        K.piani(t, [('Mattina', '36', 'a campo · 7 — 14', 'Un’ora e mezza\nSpogliatoi e docce\nIl caffè al bar, offerto',
                     False, ''),
                    ('Sera', '52', 'a campo · 17 — 1', 'Un’ora e mezza\nLuci e riscaldamento\nPrenotazione 7 giorni prima',
                     True, 'La più chiesta'),
                    ('Tessera dieci', '420', 'dieci partite serali', 'Quando volete, entro sei mesi\nAnche con amici diversi\n'
                     'Una lezione di prova compresa', False, '')],
                'Prenota', CAMPI + '#prenota', nota='Soci: −20% · tessera annuale 40 €'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('date', 'Giorno', 'giorno', True),
             K.campo('select', 'Ora', 'ora', True, opzioni='7:00\n8:30\n10:00\n11:30\n13:00\n17:00\n18:30\n20:00\n21:30\n23:00'),
             K.campo('select', 'Campo', 'campo', opzioni='Coperto, uno qualsiasi\nAll’aperto\nCampo centrale'),
             K.campo('select', 'Vi manca qualcuno?', 'manca', opzioni='Siamo in quattro\nCi manca uno\nCi mancano due'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Racchette a noleggio, livello di gioco…')]
    out.append(K.sezione_modulo(t, 'prenota', 'La prenotazione', ['Scegliete', 'campo e ora.'],
                                'Vi confermiamo con un messaggio. Se vi manca un giocatore lo cerchiamo noi nel gruppo del '
                                'circolo, del vostro livello.', VOCI, campi, 'Bandeja — prenotazione campo', 'Prenota',
                                'Ricevuta! Vi confermiamo il campo con un messaggio.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Sul viale,', 'dietro le colonie.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'viale dei Mille 30, Riccione'), ('Parcheggio', '80 posti, gratis'),
                                  ('In bici', 'la ciclabile del lungomare'), ('Spogliatoi', 'con docce e armadietti')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Bandeja · circolo di padel', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_corsi():
    t = Tpl(ID, 'corsi')
    out = [K.intro(t, 'I corsi', ['Otto lezioni,', 'quattro per campo.'],
                   'Gruppi di quattro dello stesso livello, il maestro sempre in campo. La prima lezione è gratis e la '
                   'racchetta ve la prestiamo.', bg=K.photo_bg('pexels-8224496.jpg', overlay=80, color=C['fondo']))]
    corsi = [('primi', 'Primi colpi', C['acc'], 'Mai giocato: regole, impugnatura, i vetri.', 90),
             ('livello2', 'Livello 2', C['sec'], 'Bandeja, globo e posizione a rete.', 90),
             ('agonisti', 'Agonisti', mix(C['acc'], 60, C['testo']), 'Víbora, tattica, preparazione ai tornei.', 90),
             ('junior', 'Junior', mix(C['sec'], 55, C['acc']), 'Dai 7 ai 14 anni, col gioco.', 60),
             ('over60', 'Over 60', mix(C['acc'], 45, C['sec']), 'Senza correre, con tanta tecnica.', 60)]
    piano = [(1, '09:30', '11:00', 'over60', 'Walter Rinaldi', 'Campo 2', 'base'), (1, '18:30', '20:00', 'primi', 'Sara Benedetti', 'Campo 4', 'base'),
             (1, '20:00', '21:30', 'livello2', 'Diego Morales', 'Campo 1', 'intermedio'),
             (2, '17:00', '18:00', 'junior', 'Walter Rinaldi', 'Campo 5', 'base'), (2, '19:00', '20:30', 'agonisti', 'Diego Morales', 'Campo 1', 'avanzato'),
             (3, '13:00', '14:30', 'primi', 'Sara Benedetti', 'Campo 3', 'base'), (3, '20:00', '21:30', 'livello2', 'Sara Benedetti', 'Campo 2', 'intermedio'),
             (4, '09:30', '11:00', 'over60', 'Walter Rinaldi', 'Campo 2', 'base'), (4, '17:00', '18:00', 'junior', 'Walter Rinaldi', 'Campo 5', 'base'),
             (4, '19:00', '20:30', 'agonisti', 'Diego Morales', 'Campo 1', 'avanzato'),
             (5, '18:30', '20:00', 'primi', 'Sara Benedetti', 'Campo 4', 'base'),
             (6, '10:00', '11:00', 'junior', 'Walter Rinaldi', 'Campo 5', 'base'), (6, '11:00', '12:30', 'livello2', 'Diego Morales', 'Campo 1', 'intermedio')]
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'bj-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': ins, 'sala': sala,
                     'livello': liv, 'posti': 4, 'nota': ''} for k, (g, a, b, c, ins, sala, liv) in enumerate(piano)],
        'giorni': 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato', 'ora_inizio': '09:00', 'ora_fine': '22:00',
        'mostra_filtri': True, 'etichetta_tutti': 'Tutti', 'filtro_livello': True, 'filtro_istruttore': True,
        'testo_vuoto': 'Nessuna lezione con questi filtri: togline uno.', 'testo_prenota': 'Prova gratis',
        'link_prenota': CORSI + '?corso={corso}&giorno={giorno}&ora={ora}#iscriviti', 'colore_oggi': C['acc'],
        'colore_adesso': LIME, 'colore_tenue': C['testo'], 'giorno_font_family': 'heading', 'giorno_font_size': 20,
        'giorno_font_weight': '600', 'giorno_uppercase': False, 'corso_font_family': 'heading', 'corso_font_size': 17,
        'corso_font_weight': '600', 'ora_font_family': 'var(--olo-font-family-mono)', 'raggio_lezione': radius(12),
        'altezza_ora': 56, 'gap': 4, 'forma_filtri': 'pillola'}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'L’orario', ['Una settimana', 'di lezioni.'], size=56,
                         tagline='Filtrate per livello o per maestro; toccate una lezione per prenotare la prova gratuita.')]),
        t.one([orario]),
    ], padding='large', width='large', html_id='orario', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I maestri', ['Tre maestri,', 'otto campi.'], size=56)]),
        K.squadra(t, [
            ('pexels-36014315.jpg', 'Diego Morales', 'Maestro nazionale', 'Arriva da Valencia, dove il padel si gioca in ogni '
             'cortile. Segue gli agonisti e il livello 2.', '50% 30%'),
            ('pexels-35248256.jpg', 'Sara Benedetti', 'Istruttrice', 'Ex tennista, insegna a chi non ha mai preso in mano una '
             'racchetta. Pazienza infinita.', '50% 30%'),
            ('pexels-6292457.jpg', 'Walter Rinaldi', 'Junior e over 60', 'Quarant’anni di tennis, poi il padel. Allena i '
             'bambini il martedì e i nonni il lunedì.', '50% 30%')], sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('text', 'Corso', 'corso', param='corso'), K.campo('text', 'Giorno', 'giorno', param='giorno'),
             K.campo('text', 'Ora', 'ora', param='ora'),
             K.campo('select', 'Avete già giocato?', 'livello', opzioni='Mai\nQualche volta\nGioco spesso'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Età dei bambini, infortuni, domande.')]
    out.append(K.sezione_modulo(t, 'iscriviti', 'La lezione di prova', ['Gratis,', 'la prima volta.'],
                                'Portate scarpe da ginnastica pulite; racchetta e palline ve le diamo noi.', VOCI, campi,
                                'Bandeja — lezione di prova', 'Prenota la prova', 'Ricevuta! Vi scriviamo per confermare.'))
    return out


def build_tornei():
    t = Tpl(ID, 'tornei')
    out = [K.intro(t, 'I tornei', ['Il venerdì,', 'a coppie che cambiano.'],
                   'Americano da sedici giocatori: ogni partita con un compagno diverso, si vince in punti. Tre ore, poi la '
                   'premiazione in terrazza.', bg=K.photo_bg('pexels-8224638.jpg', overlay=80, color=C['fondo']))]
    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2026-12-13T09:00', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': False, 'expired_message': 'Si gioca!', 'label_days': 'giorni',
        'label_hours': 'ore', 'label_minutes': 'minuti', 'text_color': C['testo'], 'accent_color': C['acc'],
        'number_font_size': 76, 'number_font_weight': '600', 'label_font_size': '12', 'item_min_width': 150,
        'item_bg_color': C['chiaro'], 'item_radius': radius(14),
        'item_padding': {'top': 30, 'right': 20, 'bottom': 22, 'left': 20}}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il torneo di Natale', ['Trentadue coppie,', 'un fine settimana.'], size=52,
                                layout='stack', size_mobile=34),
                      K.text(t, 'Sabato 13 e domenica 14 dicembre, tre categorie. Iscrizione 25 € a giocatore, con la '
                                'maglietta e la cena di sabato.'),
                      K.button(t, 'Iscriviti', TORNEI + '#iscrizione', kind='pieno', pad_top=14)], wm='2-5', ws='1-1'),
               t.col([conto], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    classifica = t.classic('leaderboard', {
        'source': 'manual',
        'rows': [{'name': n, 'role': r, 'value': v, 'max': 120} for n, r, v in [
            ('Laura Fabbri', 'Sei venerdì su sei', 112), ('Matteo Gori', 'Il re della víbora', 104),
            ('Elisa Conti', 'Socia dal 2022', 97), ('Paolo Righi', 'Il più costante', 91), ('Giada Neri', 'La rivelazione', 84),
            ('Marco Pari', 'Sempre a rete', 77)]],
        'value_suffix': 'punti', 'show_position': True, 'show_value': True, 'show_role': True, 'animate_on_view': True,
        'highlight_top': 1, 'row_bg': C['chiaro'], 'text_color': C['testo'], 'role_color': C['tenue'],
        'position_color': C['tenue'], 'badge_bg': mix(C['acc'], 12), 'badge_color': C['acc'],
        'bar_track_color': mix(C['testo'], 8), 'bar_gradient_from': C['acc'], 'bar_gradient_to': C['sec'],
        'highlight_color': C['acc'], 'name_size': 20, 'name_weight': '600', 'position_size': 30,
        'border_radius': radius(12)}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La classifica', ['Il venerdì', 'dopo sei tornei.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Punti di ogni partita vinta, più tre per chi arriva in finale. A giugno i primi otto giocano '
                                'il master, con la cena offerta dal circolo.')], wm='2-5', ws='1-1'),
               t.col([classifica], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_PALLINE, 'Il venerdì', 'Palline nuove, <em>ogni partita.</em>',
                             'Al torneo si cambiano le palline a ogni partita: rimbalzano uguali per tutti, dalla prima '
                             'all’ultima.', overlay=55))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Torneo', 'torneo', True, opzioni='Americano del venerdì\nTorneo di Natale\nMaster di giugno'),
             K.campo('select', 'Categoria', 'categoria', True, opzioni='Principianti\nIntermedi\nAgonisti\nMisto'),
             K.campo('text', 'Il vostro compagno', 'compagno', segnaposto='Se avete già una coppia'),
             K.campo('tel', 'Telefono', 'telefono')]
    out.append(K.sezione_modulo(t, 'iscrizione', 'L’iscrizione', ['Da soli', 'o in coppia.'],
                                'All’americano del venerdì si viene anche da soli: le coppie le formiamo noi, per livello.',
                                VOCI, campi, 'Bandeja — iscrizione al torneo', 'Iscrivimi', 'Iscritto! Ci vediamo in campo.'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · palla fuori', color=LIME, align='center'),
                       K.headline(t, 'Questa pagina <em>è uscita dal campo.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=LIME),
                       K.text(t, 'Por tres, si direbbe. Torniamo a giocare.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna al circolo', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_DRONE, overlay=64), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'La pallina in campo',
    ['padel', 'circolo', 'sport', 'corsi', 'tornei', 'riccione', 'chiaro', 'slider pro', '3d'],
    'Un circolo di padel, bianco, blu campo e giallo lime: in apertura il campo ripreso dal drone inclinato in 3D e la '
    'pallina che gioca lo scambio con la sua ombra; i colpi su carte da girare, i prezzi dei campi, l’orario dei corsi '
    'filtrabile, il conto alla rovescia del torneo e la classifica del venerdì.',
    [('I campi', CAMPI), ('I corsi', CORSI), ('I tornei', TORNEI), ('Prenota', CAMPI + '#prenota')],
    [('home', 'Home', None), ('campi', 'I campi', 'campi'), ('corsi', 'I corsi', 'corsi'), ('tornei', 'I tornei', 'tornei')],
    HEX, '\'Funnel Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Funnel Display\', system-ui, sans-serif',
    '\'Kode Mono\', ui-monospace, Menlo, monospace', ['Funnel Display', 'Funnel Sans', 'Kode Mono'], peso_titoli='600',
    raggio=14, grana=0, titoli_line='1.02', titoli_spazio='-0.025em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '3'  # apertura scura sul video pieno, 31 ottobre 2026
THEME['cursor']['style'] = 'cometa'  # cursore: la scia della pallina (31 ott 2026)


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'campi.json': build_campi(), 'corsi.json': build_corsi(), 'tornei.json': build_tornei(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
