# -*- coding: utf-8 -*-
"""Genera il tema «Chiavistello» (id chiavistello): un’agenzia immobiliare nel centro storico di Lucca: case dentro le
mura, palazzi con le travi a vista, la stima gratuita per chi vende e tre agenti che conoscono ogni vicolo.

Tema chiaro e caldo, la città di mattoni: avorio, cotto, ottone e il blu ardesia delle persiane. Titoli in Cormorant (col
corsivo), testo in Work Sans, dati in Fira Code.

Apertura: «l’insegna». Lo slider resta fermo in cima mentre si scorre: sul video dei tetti, a destra, un’insegna a
bandiera appesa al suo braccio di ferro battuto, che dondola piano. Davanti dice «Vendesi», con la casa e il prezzo.
Scorrendo l’insegna si gira in 3D sulle catene e dietro dice «Venduto · in 21 giorni». Cursore: la chiave.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'chiavistello'
NAME = 'Chiavistello'

HEX = {
    'primary': '#A9472E', 'primary_contrast': '#FFFFFF', 'secondary': '#E9C46A', 'secondary_contrast': '#1E1A17',
    'accent': '#2F4858', 'dark': '#1E1A17', 'light': '#FFFFFF', 'muted': '#F3ECE2', 'muted_contrast': '#231F1B',
    'text': '#231F1B', 'text_muted': '#615A52', 'background': '#FBF7F1', 'border': '#E5D9C8', 'link': '#A9472E',
}

HOME, CASE, VENDERE, AGENZIA = '{{PAGINA:home}}', '{{PAGINA:in-vendita}}', '{{PAGINA:vendere}}', '{{PAGINA:agenzia}}'
MAIL, PHONE, PHONE_TEL = 'case@chiavistello.example', '+39 0583 000 512', 'tel:+390583000512'
LAT, LNG = '43.8430', '10.5050'

V_TETTI, V_TETTI2, V_TETTI3, V_VICOLO, V_PORTICO, V_PORTONE, V_CHIAVE = (
    'pexels-video-34631436', 'pexels-video-29385634', 'pexels-video-3109535', 'pexels-video-37139949', 'pexels-video-10046800',
    'pexels-video-34800569', 'pexels-video-2070044')
PHOTOS = [
    'pexels-17076024.jpg', 'pexels-19466870.jpg', 'pexels-13716356.jpg', 'pexels-31754121.jpg', 'pexels-17917088.jpg',
    'pexels-15286712.jpg', 'pexels-16871486.jpg', 'pexels-14018344.jpg', 'pexels-17857002.jpg', 'pexels-6970000.jpg',
    'pexels-7195866.jpg', 'pexels-7746646.jpg', 'pexels-7174386.jpg', 'pexels-7174402.jpg', 'pexels-7061410.jpg',
    'pexels-14023359.jpg', 'pexels-39769444.jpg', 'pexels-14325874.jpg', 'pexels-39431879.jpg', 'pexels-39769470.jpg',
    'pexels-36604912.jpg', 'pexels-33073557.jpg', 'pexels-36604914.jpg', 'pexels-36240462.jpg', 'pexels-31188305.jpg',
    'pexels-6034029.jpg', 'pexels-13920307.jpg', 'pexels-16262621.jpg', 'pexels-33692879.jpg', 'pexels-31470244.jpg',
    'pexels-12291542.jpg', 'pexels-38974458.jpg', 'pexels-39801698.jpg', 'pexels-15287543.jpg', 'pexels-30990621.jpg',
    'pexels-37410120.jpg', 'pexels-37842958.jpg', 'pexels-7821728.jpg', 'pexels-19273414.jpg',
]
VIDEOS = [V_TETTI, V_TETTI2, V_TETTI3, V_VICOLO, V_PORTICO, V_PORTONE, V_CHIAVE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
OTTONE, ARDESIA = C['sec'], C['acc2']
MONO = 'Fira Code'
VOCI = [('map-pin', 'Via Fillungo 88 · Lucca', AGENZIA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Chiavistello', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Chiavistello', 'Agenzia immobiliare dentro le mura di Lucca, dal 1998. Case, palazzi e botteghe del '
                   'centro storico: le conosciamo una per una.',
                   [('In vendita', CASE), ('Vendere', VENDERE), ('L’agenzia', AGENZIA), ('Privacy', '/privacy-policy/')],
                   'L’agenzia', 'Lunedì — sabato 9:30 — 19<br>Le visite anche la domenica', VOCI,
                   '© 2026 Chiavistello · Lucca · Iscritta al Ruolo agenti immobiliari', [('instagram', 'https://instagram.com')],
                   marchio_size=130, fondo=C['sala'])


# ── L'apertura: l'insegna ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
DONDOLA = 3600
GIRA_A, GIRA_B = 34, 58   # il giro dell'insegna, nel tratto dello scorrimento


def apertura(t):
    m = K.mob
    # l'insegna sul computer e sul telefono: il braccio (y), le catene, la tavola (x, y, larghezza, altezza)
    D = dict(bx0=49.0, bx1=84.0, by=16.0, cx0=57.0, cx1=77.0, tx=54.0, ty=31.0, tw=26.0, th=36.0)
    M = dict(bx0=20.0, bx1=94.0, by=40.0, cx0=34.0, cx1=80.0, tx=26.0, ty=45.5, tw=60.0, th=27.0)
    pivot = lambda g: ((g['cx0'] + g['cx1']) / 2, g['by'])

    def dondola(x, y, w, h, g=D):
        px, py = pivot(g)
        ox, oy = (px - x) / w * 100 if w else 50, (py - y) / h * 100 if h else 0
        return K.tl(x, y, [(0, {'rotation': -3, 'originX': round(ox, 1), 'originY': round(oy, 1)}, 'sine.inOut'),
                           (DONDOLA // 2, {'rotation': 3, 'originX': round(ox, 1), 'originY': round(oy, 1)}, 'sine.inOut'),
                           (DONDOLA, {'rotation': -3, 'originX': round(ox, 1), 'originY': round(oy, 1)})], DONDOLA)

    def pezzo(lid, gx, **kw):
        """gx(g) → (x, y, w, h) sul formato g: computer e telefono con la stessa forma."""
        x, y, w, h = gx(D)
        xm, ym, wm, hm = gx(M)
        sw = kw.pop('dondola', True)
        L = K.livello(lid, x=round(x, 2), y=round(y, 2), w=round(w, 2), h=round(h, 2), entra='fadeIn',
                      ritardo=kw.pop('ritardo', 300), durata=kw.pop('durata', 900),
                      responsive={'mobile': dict({'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                  'height': round(hm, 2)}, **kw.pop('mob', {}))}, **kw)
        if sw:
            L['timeline'] = dondola(x, y, w, h)
        if 'scrollMove' in kw or L.get('scrollMove'):
            L['customCSS'] = 'backface-visibility:hidden;-webkit-backface-visibility:hidden;'
        return L

    # il giro: davanti da 0 a 180°, dietro da -180 a 0°, con il retro nascosto (backface-visibility): niente opacità,
    # perché la timeline del dondolio riscrive il filtro a ogni fotogramma
    davanti = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': 0.01}, {'p': GIRA_A, 'ry': 0.01}, {'p': GIRA_B, 'ry': 180},
                                                   {'p': 100, 'ry': 180}]}
    dietro = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': -180}, {'p': GIRA_A, 'ry': -180}, {'p': GIRA_B, 'ry': 0.01},
                                                  {'p': 100, 'ry': 0.01}]}
    tavola = lambda g: (g['tx'], g['ty'], g['tw'], g['th'])
    riga = lambda fy, fh: (lambda g: (g['tx'] + g['tw'] * 0.08, g['ty'] + g['th'] * fy, g['tw'] * 0.84, g['th'] * fh))
    cornice = {'borderWidth': 3, 'borderColor': OTTONE}
    livelli = [
        # il braccio di ferro battuto e il ricciolo al muro
        pezzo('ch-braccio', lambda g: (g['bx0'], g['by'] - 0.6, g['bx1'] - g['bx0'], 1.2), tipo='shape', bgColor=C['nero'],
              borderRadius=999, dondola=False),
        pezzo('ch-ricciolo', lambda g: (g['bx0'] - 1.2, g['by'] - 3.2, 2.4, 6.4), tipo='shape', bgColor='transparent',
              borderWidth=3, borderColor=C['nero'], borderRadius=999, dondola=False),
        # le catene
        pezzo('ch-catena1', lambda g: (g['cx0'] - 0.15, g['by'], 0.3, g['ty'] - g['by'] + 0.5), tipo='shape',
              bgColor=mix(C['nero'], 90)),
        pezzo('ch-catena2', lambda g: (g['cx1'] - 0.15, g['by'], 0.3, g['ty'] - g['by'] + 0.5), tipo='shape',
              bgColor=mix(C['nero'], 90)),
        # davanti: Vendesi
        pezzo('ch-tav-a', tavola, tipo='shape', bgColor=C['acc'], scrollMove=davanti, **cornice),
        pezzo('ch-a1', riga(0.12, 0.12), contenuto='Via Fillungo · terzo piano', size=13, peso='500', colore=OTTONE,
              align='center', letterSpacing=2, textTransform='uppercase', fontFamily=MONO, scrollMove=davanti,
              mob={'fontSize': m(10)}),
        pezzo('ch-a2', riga(0.30, 0.30), contenuto='Vendesi', size=76, peso='500', colore=C['chiaro'], align='center',
              fontFamily='Cormorant', fontStyle='italic', lineHeight=1.0, scrollMove=davanti, mob={'fontSize': m(46)}),
        pezzo('ch-a3', riga(0.68, 0.18), contenuto='Trilocale con le travi · 340.000 €', size=15, peso='500',
              colore=mix(C['chiaro'], 92), align='center', fontFamily=MONO, scrollMove=davanti, mob={'fontSize': m(11)}),
        # dietro: Venduto
        pezzo('ch-tav-b', tavola, tipo='shape', bgColor=ARDESIA, scrollMove=dietro, **cornice),
        pezzo('ch-b1', riga(0.12, 0.12), contenuto='Via Fillungo · terzo piano', size=13, peso='500', colore=OTTONE,
              align='center', letterSpacing=2, textTransform='uppercase', fontFamily=MONO, scrollMove=dietro,
              mob={'fontSize': m(10)}),
        pezzo('ch-b2', riga(0.30, 0.30), contenuto='Venduto', size=76, peso='500', colore=C['chiaro'], align='center',
              fontFamily='Cormorant', fontStyle='italic', lineHeight=1.0, scrollMove=dietro, mob={'fontSize': m(46)}),
        pezzo('ch-b3', riga(0.68, 0.18), contenuto='in 21 giorni, al prezzo chiesto', size=15, peso='500', colore=OTTONE,
              align='center', fontFamily=MONO, scrollMove=dietro, mob={'fontSize': m(11)}),
    ]
    testi = [
        K.livello('ch-o', contenuto='Chiavistello · case nel centro storico · Lucca', x=6, y=24, w=40, size=12, peso='500',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('ch-t1', contenuto='Dentro le mura,', tag='h1', x=6, y=30, w=48, size=78, peso='500', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(40), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('ch-t2', contenuto='<em>case con la storia.</em>', tag='p', x=6, y=41, w=48, size=78, peso='500',
                  fontFamily='Cormorant',
                  colore=OTTONE, lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(40), 'y': 15, 'x': 7, 'width': 90}}),
        K.livello('ch-s', contenuto='Palazzi con le travi, appartamenti sui tetti, botteghe sotto i portici. Le conosciamo '
                                    'una per una, dal 1998.', x=6, y=55, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 20, 'x': 7, 'width': 86}}),
        K.livello('ch-b', tipo='button', contenuto='Le case in vendita', x=6, y=69, w='auto', size=12, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=0, letterSpacing=2, textTransform='uppercase',
                  entra='fadeInUp', ritardo=1000, buttonUrl=CASE, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 27, 'x': 7, 'fontSize': m(11)}}),
        K.livello('ch-l', tipo='button', contenuto='La stima gratuita', x=23.5, y=69, w='auto', size=12, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=0, borderWidth=1,
                  borderColor=mix(C['chiaro'], 70), letterSpacing=2, textTransform='uppercase', entra='fadeInUp', ritardo=1100,
                  buttonUrl=VENDERE, hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 30}, 'mobile': {'y': 31, 'x': 7, 'fontSize': m(11)}}),
        K.livello('ch-hint', contenuto='Scorrete: l’insegna si gira ↓', x=6, y=84, w=30, size=12, peso='500',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 20, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 35.5, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('ch-velo', 'sinistra', 36, 62, colore=C['nero'], forza=86, mobile_alto=37)
    sl = [K.diapositiva('ch-1', K.sfondo_video(V_TETTI, overlay=0.2, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1400, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


CASE_L = [  # nome, sotto, descrizione, foto
    ('Il trilocale con le travi', 'Via Fillungo · 98 m² · 340.000 €', 'Terzo piano senza ascensore, le travi di castagno, '
     'due finestre sui tetti e la vista sulla torre.', 'pexels-7174386.jpg'),
    ('L’attico sui tetti', 'Piazza San Michele · 120 m² · 690.000 €', 'Una terrazza fra i coppi, due camere, il bagno '
     'con la finestra sul campanile.', 'pexels-36604912.jpg'),
    ('La casa sulle mura', 'Baluardo San Paolino · 160 m² · 820.000 €', 'Tre piani a schiera, il giardino che finisce '
     'sulle mura alberate.', 'pexels-14023359.jpg'),
    ('La bottega sotto il portico', 'Via del Battistero · 45 m² · 210.000 €', 'Le volte a mattoni, la vetrina sul '
     'vicolo, il retro per il laboratorio.', 'pexels-14325874.jpg'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Chiavistello · Lucca, dal 1998', ['Una città piccola,', 'mille case diverse.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Nel centro storico non ci sono due case uguali: soffitti a volta, scale di pietra, finestre '
                                'che guardano una torre. Per venderle bisogna raccontarle, e per comprarle bisogna sapere '
                                'cosa c’è dietro i muri. Noi lo sappiamo.', size=17, color=C['testo']),
                      K.button(t, 'L’agenzia', AGENZIA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('28', 'Anni dentro le mura'), ('21', 'Giorni in media per vendere'), ('3', 'Agenti'),
                            ('0 €', 'La stima')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In vendita', ['Quattro case', 'di questo mese.'], size=56,
                         tagline='Passate su una casa: si affaccia la stanza più bella.')]),
        t.one([P.elenco_hover(K, t, CASE_L, CASE, size=44)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_VICOLO, 'Il centro', 'Dietro ogni portone <em>un cortile.</em>',
                             'E dietro ogni cortile una scala, e in cima una casa che aspetta.', overlay=28))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi vende', ['Dalla stima', 'al rogito.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'La stima', 'Gratuita, in un’ora, con i prezzi veri delle case vicine.'),
                              ('02', 'Il racconto', 'Le foto, la pianta, la storia del palazzo: la casa si presenta bene.'),
                              ('03', 'Le visite', 'Solo con chi ha già il mutuo pronto: niente curiosi in casa vostra.'),
                              ('04', 'Il rogito', 'Seguiamo le carte con il notaio fino alle chiavi.')],
                       numero_font=MONO)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [P.testimonianza(
        K, t, 'Avevamo ereditato la casa della nonna in via Santa Croce, piena di mobili e di ricordi. L’hanno raccontata '
              'così bene che la coppia che l’ha comprata ha voluto tenere la credenza.', 'Famiglia Pieri', 'Hanno venduto '
              'nel 2025', size=34)])], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PORTICO, 'Vendete con noi', 'La stima <em>è gratuita.</em>',
                          'Veniamo a vedere la casa, vi diciamo quanto vale e perché. Senza impegno.', 'La stima', VENDERE,
                          overlay=22, video=True))
    return out


def build_in_vendita():
    t = Tpl(ID, 'in-vendita')
    out = [K.intro(t, 'In vendita', ['Case dentro le mura,', 'una per una.'],
                   'Tutte le case che vendiamo le abbiamo viste, misurate e fotografate noi. Per visitarle basta una '
                   'telefonata.', bg=K.photo_bg('pexels-36604914.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le case', ['Girate le carte:', 'dietro ci sono i numeri.'], size=56)]),
        P.carte(K, t, [
            ('pexels-7195866.jpg', 'Via Fillungo', 'Trilocale · 340.000 €', '98 m², terzo piano, travi di castagno, due '
             'camere, riscaldamento autonomo.'),
            ('pexels-7746646.jpg', 'San Michele', 'Attico · 690.000 €', '120 m² e 40 m² di terrazza, due camere, due bagni, '
             'ascensore.'),
            ('pexels-7061410.jpg', 'Sulle mura', 'Casa a schiera · 820.000 €', '160 m² su tre piani, giardino di 80 m², '
             'cantina.'),
            ('pexels-6970000.jpg', 'Corte San Lorenzo', 'Bilocale · 245.000 €', '62 m², primo piano sul cortile, '
             'ristrutturato nel 2023.'),
        ]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le altre', ['Tutte le case', 'di questa settimana.'], size=56)]),
        t.one([P.righe(K, t, [('62 m²', 'Bilocale in corte', 'Corte San Lorenzo · primo piano', '245.000 €'),
                              ('75 m²', 'Mansarda con terrazzino', 'Via San Paolino · quarto piano', '285.000 €'),
                              ('98 m²', 'Trilocale con le travi', 'Via Fillungo · terzo piano', '340.000 €'),
                              ('140 m²', 'Piano nobile', 'Via del Fosso · secondo piano', '560.000 €'),
                              ('120 m²', 'Attico con terrazza', 'Piazza San Michele', '690.000 €')], AGENZIA + '#scrivi',
                       titolo=30)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I dettagli', ['Quello che', 'non si rifà.'], size=56)]),
        P.fila_foto(K, t, [('pexels-33692879.jpg', 'Una scala di pietra'), ('pexels-39801698.jpg', 'Le persiane sulla '
                                                                                                     'facciata'),
                           ('pexels-16262621.jpg', 'Le chiavi antiche'), ('pexels-39431879.jpg', 'Il cortile coi vasi')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_vendere():
    t = Tpl(ID, 'vendere')
    out = [K.intro(t, 'Vendere', ['Una casa del centro', 'si vende raccontandola.'],
                   'La stima è gratuita e senza impegno: veniamo a vedere la casa, vi diciamo quanto vale e come la '
                   'presenteremmo.', bg=K.video_bg(V_PORTONE), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il metodo', ['Tre settimane,', 'non tre anni.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'settimana', 'pexels-17857002.jpg', 'La casa', 'Le foto', 'e la pianta',
             'Un fotografo, un rilievo con il laser, la storia del palazzo dall’archivio.', '1', 'giorno di foto'),
            ('2', 'settimana', 'pexels-7174402.jpg', 'Le persone', 'Le visite', 'giuste',
             'Prima chi ha già il mutuo approvato, poi gli altri: poche visite, tutte serie.', '8', 'visite in media'),
            ('3', 'settimana', 'pexels-13920307.jpg', 'Le chiavi', 'La proposta', 'e il notaio',
             'La proposta scritta, il compromesso, le carte: seguiamo tutto noi.', '21', 'giorni in media')], icona='key')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email'), K.campo('text', 'Indirizzo della casa', 'indirizzo', True),
             K.campo('select', 'Quando vendere', 'quando', opzioni='Subito\nEntro sei mesi\nSto solo valutando'),
             K.campo('textarea', 'Due righe sulla casa', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'stima', 'La stima', ['L’indirizzo e un numero,', 'veniamo noi.'],
                                'Vi richiamiamo in giornata per fissare la visita.', VOCI, campi, 'Chiavistello — stima',
                                'Chiedi la stima', 'Ricevuto. Vi richiamiamo in giornata.'))
    return out


def build_agenzia():
    t = Tpl(ID, 'agenzia')
    out = [K.intro(t, 'L’agenzia', ['Tre agenti,', 'tutti nati qui.'],
                   'L’agenzia è in via Fillungo, dietro una porta verde. Dentro ci sono le chiavi di quaranta case e tre '
                   'persone che sanno aprirle tutte.', bg=K.photo_bg('pexels-17076024.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi vi apre', 'la porta.'], size=56)]),
        K.squadra(t, [
            ('pexels-37842958.jpg', 'Lorenzo Pieroni', 'Titolare, agente dal 1998', 'Palazzi e piani nobili. Conosce gli '
             'archivi e la storia di ogni via.', 'center 25%'),
            ('pexels-7821728.jpg', 'Matteo Bianchi', 'Agente immobiliare', 'Bilocali, mansarde, le prime case.',
             'center 25%'),
            ('pexels-19273414.jpg', 'Andrea Lucchesi', 'Agente immobiliare', 'Case sulle mura e botteghe.', 'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Cercate', 'cosa', opzioni='Un bilocale\nUn trilocale\nUna casa grande\nUna bottega\nNon so ancora'),
             K.campo('text', 'Il budget', 'budget'), K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Cosa cercate,', 've la troviamo.'],
                                'Vi mandiamo le case giuste prima che vadano online.', VOCI, campi, 'Chiavistello — ricerca',
                                'Invia', 'Grazie. Vi scriviamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In via Fillungo,', 'la porta verde.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — sabato 9:30 — 19'), ('A piedi', 'da piazza dell’Anfiteatro, due minuti'),
                                  ('In auto', 'parcheggio fuori le mura, porta Santa Maria')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Chiavistello · agenzia', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · porta chiusa', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa porta <em>è chiusa a chiave.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La pagina non c’è. Proviamo dal portone principale.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_TETTI2, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Immobiliare', 'L’insegna che si gira',
    ['agenzia immobiliare', 'centro storico', 'case in vendita', 'stima gratuita', 'lucca', 'chiaro', 'slider pro', '3d'],
    'Un’agenzia immobiliare nel centro storico, cotto e ottone: in apertura un’insegna a bandiera che dondola sul video dei '
    'tetti, «Vendesi» davanti, e che scorrendo si gira in 3D sulle catene per dire «Venduto · in 21 giorni»; le case in '
    'vendita da girare, il metodo per vendere, la stima gratuita e gli agenti.',
    [('In vendita', CASE), ('Vendere', VENDERE), ('L’agenzia', AGENZIA)],
    [('home', 'Home', None), ('in-vendita', 'In vendita', 'in-vendita'), ('vendere', 'Vendere', 'vendere'),
     ('agenzia', 'L’agenzia', 'agenzia')],
    HEX, '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cormorant\', Georgia, serif',
    '\'Fira Code\', ui-monospace, Menlo, monospace', ['Work Sans', 'Cormorant', 'Fira Code'], corsivo_di=['Cormorant'],
    peso_titoli='500', raggio=0, grana=3, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'chiave'
THEME['cursor']['ring_color'] = HEX['dark']
THEME['cursor']['dot_color'] = '#C9A24A'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'in-vendita.json': build_in_vendita(), 'vendere.json': build_vendere(), 'agenzia.json': build_agenzia(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
