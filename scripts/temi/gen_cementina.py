# -*- coding: utf-8 -*-
"""Genera il tema «Cementina» (id cementina): un laboratorio di piastrelle di cemento decorate a mano a Palermo: le
collezioni, i colori su misura, i pavimenti restaurati e le visite al laboratorio.

Tema chiaro e mediterraneo: bianco latte, blu cobalto, ocra e rosso pompeiano. Titoli in Playfair Display (col corsivo),
testo in Mulish, dati in Noto Sans Mono.

Apertura: «il pavimento che si gira». Lo slider resta fermo in cima mentre si scorre: sul video di un pavimento antico,
a destra, sedici piastrelle grigie di cemento nudo. Scorrendo si girano in 3D una dopo l’altra, in un’onda che corre
lungo le diagonali, e dietro c’è il decoro: i quarti di cerchio agli angoli, le stelle, i fiori, che accostati formano i
cerchi grandi delle cementine. Cursore: il mosaico.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'cementina'
NAME = 'Cementina'

HEX = {
    'primary': '#1F4E8C', 'primary_contrast': '#FFFFFF', 'secondary': '#F2B544', 'secondary_contrast': '#1A1C22',
    'accent': '#B23A2E', 'dark': '#1A1C22', 'light': '#FFFFFF', 'muted': '#F2EEE6', 'muted_contrast': '#1E2027',
    'text': '#1E2027', 'text_muted': '#5C5E66', 'background': '#FBF9F4', 'border': '#E0DACE', 'link': '#1F4E8C',
}

HOME, COLLEZIONI, LAB, SHOWROOM = '{{PAGINA:home}}', '{{PAGINA:collezioni}}', '{{PAGINA:laboratorio}}', '{{PAGINA:showroom}}'
MAIL, PHONE, PHONE_TEL = 'laboratorio@cementina.example', '+39 091 000 2255', 'tel:+390910002255'
LAT, LNG = '38.1170', '13.3590'

V_PAVIMENTO, V_STAMPO, V_PIASTRELLE, V_MANI, V_MANI2, V_COLORE, V_COLORE2, V_POSA = (
    'pexels-video-4154310', 'pexels-video-6611032', 'pexels-video-6023466', 'pexels-video-9736882', 'pexels-video-20143605',
    'pexels-video-6341424', 'pexels-video-4365145', 'pexels-video-4198961')
PHOTOS = [
    'pexels-35972028.jpg', 'pexels-35466647.jpg', 'pexels-37341462.jpg', 'pexels-17012266.jpg', 'pexels-7566201.jpg',
    'pexels-2213495.jpg', 'pexels-33010398.jpg', 'pexels-5146920.jpg', 'pexels-32884025.jpg', 'pexels-5146922.jpg',
    'pexels-7195851.jpg', 'pexels-7195849.jpg', 'pexels-7195855.jpg', 'pexels-27573422.jpg', 'pexels-8141966.jpg',
    'pexels-8141963.jpg', 'pexels-7031838.jpg', 'pexels-6023571.jpg', 'pexels-33500527.jpg', 'pexels-39561057.jpg',
    'pexels-29370128.jpg', 'pexels-36006800.jpg', 'pexels-36101861.jpg', 'pexels-10788525.jpg', 'pexels-37852968.jpg',
    'pexels-32462699.jpg', 'pexels-31251949.jpg', 'pexels-17904851.jpg', 'pexels-31595229.jpg', 'pexels-30617002.jpg',
    'pexels-31530645.jpg', 'pexels-38262296.jpg', 'pexels-38942676.jpg', 'pexels-37075230.jpg', 'pexels-19734053.jpg',
    'pexels-38809083.jpg', 'pexels-38021464.jpg',
]
VIDEOS = [V_PAVIMENTO, V_STAMPO, V_PIASTRELLE, V_MANI, V_MANI2, V_COLORE, V_COLORE2, V_POSA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
OCRA, POMPEI = C['sec'], C['acc2']
MONO = 'Noto Sans Mono'
VOCI = [('map-pin', 'Via Alloro 64 · Palermo', SHOWROOM + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Cementina', logo_size='26', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Cementina', 'Piastrelle di cemento decorate a mano a Palermo, alla Kalsa, dal 1971. Una per una, con '
                   'gli stampi di ottone del nonno e i colori che volete voi.',
                   [('Le collezioni', COLLEZIONI), ('Il laboratorio', LAB), ('Lo showroom', SHOWROOM),
                    ('Privacy', '/privacy-policy/')],
                   'Lo showroom', 'Martedì — sabato 10 — 13 e 16 — 19<br>Il laboratorio su appuntamento', VOCI,
                   '© 2026 Cementina · Palermo · P.IVA 00000000000', [('instagram', 'https://instagram.com')],
                   marchio_size=150, fondo=C['sala'])


# ── L'apertura: il pavimento che si gira ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
N = 4
CEMENTO = 'background:linear-gradient(135deg,#B8B4AC,#9C9890);'
FONDO = 'var(--olo-color-light)'
B, O, R = 'var(--olo-color-primary)', 'var(--olo-color-secondary)', 'var(--olo-color-accent)'
DECORI = [
    # i quarti di cerchio agli angoli: accostati fanno i cerchi grandi; al centro un rombo
    ('radial-gradient(circle at 0 0,{b} 0 34%,transparent 35%),radial-gradient(circle at 100% 0,{b} 0 34%,transparent 35%),'
     'radial-gradient(circle at 0 100%,{b} 0 34%,transparent 35%),radial-gradient(circle at 100% 100%,{b} 0 34%,transparent 35%),'
     'radial-gradient(circle at 0 0,{o} 0 44%,transparent 45%),radial-gradient(circle at 100% 0,{o} 0 44%,transparent 45%),'
     'radial-gradient(circle at 0 100%,{o} 0 44%,transparent 45%),radial-gradient(circle at 100% 100%,{o} 0 44%,transparent 45%),'
     'linear-gradient(45deg,transparent 42%,{r} 42% 58%,transparent 58%),linear-gradient(-45deg,transparent 42%,{r} 42% 58%,'
     'transparent 58%),{f}'),
    # il fiore: un tondo al centro, quattro petali
    ('radial-gradient(circle at 50% 50%,{r} 0 13%,transparent 14%),radial-gradient(circle at 50% 22%,{b} 0 16%,transparent 17%),'
     'radial-gradient(circle at 50% 78%,{b} 0 16%,transparent 17%),radial-gradient(circle at 22% 50%,{b} 0 16%,transparent 17%),'
     'radial-gradient(circle at 78% 50%,{b} 0 16%,transparent 17%),radial-gradient(circle at 0 0,{o} 0 22%,transparent 23%),'
     'radial-gradient(circle at 100% 0,{o} 0 22%,transparent 23%),radial-gradient(circle at 0 100%,{o} 0 22%,transparent 23%),'
     'radial-gradient(circle at 100% 100%,{o} 0 22%,transparent 23%),{f}'),
]


def _decoro(k):
    return 'background:' + DECORI[k % len(DECORI)].format(b=B, o=O, r=R, f=FONDO) + ';'


def apertura(t):
    m = K.mob
    lato, latom = 126.0, 138.0            # lato della piastrella in px della tela (computer, telefono), fuga di 3 px
    x0, y0 = 56.0, 17.0                   # angolo del pavimento (% della tela)
    x0m, y0m = 6.0, 46.0
    livelli = []
    for r in range(N):
        for c in range(N):
            k = r * N + c
            a = 6 + (r + c) * 9                  # l'onda corre lungo le diagonali
            x = x0 + c * (lato + 3) / W * 100
            y = y0 + r * (lato + 3) / H * 100
            xm = x0m + c * (latom + 4) / WM * 100
            ym = y0m + r * (latom + 4) / HM * 100
            geo = dict(x=round(x, 2), y=round(y, 2), w=round(lato / W * 100, 2), h=round(lato / H * 100, 2))
            resp = {'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': round(latom / WM * 100, 2),
                               'height': round(latom / HM * 100, 2)}}
            nascondi = 'backface-visibility:hidden;-webkit-backface-visibility:hidden;'
            livelli.append(K.livello('ce-g%d' % k, tipo='shape', bgColor='#ABA79F', entra='fadeIn', ritardo=150 + k * 40,
                                     durata=700, customCSS=CEMENTO + nascondi,
                                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [
                                         {'p': 0, 'ry': 0.01}, {'p': a, 'ry': 0.01}, {'p': a + 16, 'ry': 180},
                                         {'p': 100, 'ry': 180}]}, responsive=resp, **geo))
            livelli.append(K.livello('ce-d%d' % k, tipo='shape', bgColor=FONDO, entra='fadeIn', ritardo=150 + k * 40,
                                     durata=700, customCSS=_decoro(r + c) + nascondi,
                                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [
                                         {'p': 0, 'ry': -180}, {'p': a, 'ry': -180}, {'p': a + 16, 'ry': 0.01},
                                         {'p': 100, 'ry': 0.01}]}, responsive={'mobile': dict(resp['mobile'])}, **geo))
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 20}, {'p': 86, 'o': 0, 'y': 20},
                                                {'p': 96, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    livelli.append(K.livello('ce-fatto', contenuto='Collezione Kalsa · 20 × 20 cm · 68 € al m²', x=56, y=84, w=35.8, size=13,
                             peso='600', colore=C['nero'], bgColor=OCRA, padding=9, align='center', fontFamily=MONO,
                             entra='fadeIn', ritardo=0, durata=300, scrollMove=fine,
                             responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 87.5, 'width': 88}}))
    testi = [
        K.livello('ce-o', contenuto='Cementina · piastrelle di cemento · Palermo', x=6, y=22, w=44, size=12, peso='600',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('ce-t1', contenuto='Ogni piastrella,', tag='h1', x=6, y=28, w=48, size=86, peso='500', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(36), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('ce-t2', fontFamily='Playfair Display', contenuto='<em>fatta a mano.</em>', tag='p', x=6, y=40, w=48,
                  size=86, peso='500', colore=OCRA, lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550,
                  durata=1000, responsive={'mobile': {'fontSize': m(36), 'y': 15, 'x': 7, 'width': 90}}),
        K.livello('ce-s', contenuto='Cemento, marmo in polvere e i colori versati nello stampo d’ottone, una piastrella '
                                    'alla volta. Dal 1971, alla Kalsa.', x=6, y=53, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 19.5, 'x': 7, 'width': 86}}),
        K.livello('ce-b', tipo='button', contenuto='Le collezioni', x=6, y=67, w='auto', size=12, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=0, letterSpacing=2, textTransform='uppercase',
                  entra='fadeInUp', ritardo=1000, buttonUrl=COLLEZIONI, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 26.5, 'x': 7, 'fontSize': m(11)}}),
        K.livello('ce-l', tipo='button', contenuto='Il laboratorio', x=20, y=67, w='auto', size=12, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=0, borderWidth=1,
                  borderColor=mix(C['chiaro'], 70), letterSpacing=2, textTransform='uppercase', entra='fadeInUp', ritardo=1100,
                  buttonUrl=LAB, hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'y': 26.5, 'x': 50, 'fontSize': m(11)}}),
        K.livello('ce-hint', contenuto='Scorrete: il pavimento si gira ↓', x=6, y=82, w=30, size=12, peso='600',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 15, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 32, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('ce-velo', 'sinistra', 38, 62, colore=C['nero'], forza=86, mobile_alto=35)
    sl = [K.diapositiva('ce-1', K.sfondo_video(V_PAVIMENTO, overlay=0.22, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1700, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Cementina · Palermo, dal 1971', ['Cemento e colore,', 'nient’altro.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Le cementine non si cuociono: si pressano. Il colore va nello stampo per primo, poi il '
                                'cemento, poi la pressa a mano. Dopo un mese di riposo sono pronte, e durano cent’anni: lo '
                                'dicono i pavimenti dei palazzi qui intorno.', size=17, color=C['testo']),
                      K.button(t, 'Il laboratorio', LAB, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('1971', 'Il primo stampo'), ('140', 'Decori in archivio'), ('28', 'Giorni di riposo'),
                            ('100', 'Anni di vita')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le collezioni', ['Girate le piastrelle:', 'dietro il prezzo.'], size=56)]),
        P.carte(K, t, [
            ('pexels-35972028.jpg', 'Kalsa', 'I cerchi grandi', 'Quarti di cerchio blu e ocra: accostate, fanno i rosoni '
             'dei palazzi. 68 € al m².'),
            ('pexels-17012266.jpg', 'Vucciria', 'Il fiore a quattro petali', 'Il decoro più antico dell’archivio, del 1912. '
             '74 € al m².'),
            ('pexels-7566201.jpg', 'Mondello', 'Il blu e il bianco', 'Due soli colori, il mare e la calce. 62 € al m².'),
            ('pexels-2213495.jpg', 'Su misura', 'Il vostro disegno', 'Un decoro nuovo con i colori che scegliete voi. Da '
             '90 € al m².'),
        ], altezza='400'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_COLORE, 'Il colore', 'Si versa <em>nello stampo.</em>',
                             'Pigmenti naturali e polvere di marmo: il colore entra per tre millimetri, non si consuma.',
                             overlay=24))
    out.append(t.section([
        t.one([K.sechead(t, 'Dove vanno', ['Cucine, bagni,', 'scale di palazzo.'], size=56)]),
        P.fila_foto(K, t, [('pexels-7195851.jpg', 'Una cucina con le cementine'), ('pexels-27573422.jpg', 'Un bagno con le '
                                                                                                         'maioliche'),
                           ('pexels-38262296.jpg', 'Una scala con le alzate decorate'),
                           ('pexels-33010398.jpg', 'Un pavimento antico')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-6023571.jpg', 'Un’artigiana mostra una piastrella dipinta', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Mio nonno diceva che una cementina si fa in tre minuti e si guarda per cent’anni. '
                                          'Noi continuiamo a farle così, una alla volta.', 'Rosalia Giacalone, terza '
                                          'generazione', size='40')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_POSA, 'Il restauro', 'I pavimenti antichi <em>tornano come nuovi.</em>',
                          'Rifacciamo le piastrelle rotte con lo stesso decoro, e le mettiamo al loro posto.', 'Lo showroom',
                          SHOWROOM, overlay=18, video=True))
    return out


def build_collezioni():
    t = Tpl(ID, 'collezioni')
    out = [K.intro(t, 'Le collezioni', ['Centoquaranta decori,', 'mille colori.'],
                   'Ogni decoro si può fare con i colori che volete: lo showroom ha le cartelle con duecento tinte e le '
                   'piastrelle di prova.', bg=K.video_bg(V_PIASTRELLE), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il listino', ['Al metro quadro,', 'formato 20 × 20.'], size=56,
                         tagline='Spessore 16 mm, per interni ed esterni coperti. La posa la consigliamo noi.')]),
        t.row([t.col([P.listino(K, t, [
            ('Kalsa', 'I cerchi grandi · blu e ocra', '68 €', 'pexels-35466647.jpg', 'la più scelta'),
            ('Vucciria', 'Il fiore del 1912', '74 €', 'pexels-37341462.jpg', ''),
            ('Mondello', 'Blu e bianco', '62 €', 'pexels-5146920.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Tinta unita', 'Un colore, per le cornici', '48 €', 'pexels-32884025.jpg', ''),
            ('Su misura', 'Il vostro decoro', 'da 90 €', 'pexels-36006800.jpg', ''),
            ('Restauro', 'Le piastrelle mancanti, uguali', 'a preventivo', 'pexels-5146922.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'L’archivio', ['Maioliche', 'e cementine.'], size=56)]),
        P.fila_foto(K, t, [('pexels-17904851.jpg', 'Piatti di maiolica decorati'), ('pexels-31595229.jpg', 'Una maiolica '
                                                                                                          'col veliero'),
                           ('pexels-30617002.jpg', 'Maioliche blu antiche'), ('pexels-31530645.jpg', 'Una parete di '
                                                                                                    'maioliche')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_lab():
    t = Tpl(ID, 'laboratorio')
    out = [K.intro(t, 'Il laboratorio', ['Tre minuti a piastrella,', 'un mese di riposo.'],
                   'Il laboratorio è dietro lo showroom, con la pressa del 1971 e gli stampi di ottone. Su appuntamento si '
                   'può vedere al lavoro, e fare la propria piastrella.', bg=K.photo_bg('pexels-29370128.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si fa', ['Tre gesti,', 'sempre gli stessi.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('I', 'il colore', 'pexels-10788525.jpg', 'Lo stampo', 'Il colore', 'nelle celle',
             'Il colore liquido si versa nelle celle dello stampo d’ottone, un colore per cella.', '3', 'mm di colore'),
            ('II', 'la pressa', 'pexels-36101861.jpg', 'Il corpo', 'Cemento', 'e pressa',
             'Sopra, il cemento asciutto; poi la pressa a mano, con tutto il peso.', '16', 'mm di spessore'),
            ('III', 'il riposo', 'pexels-39561057.jpg', 'L’attesa', 'Un mese', 'in acqua e all’ombra',
             'Una notte in acqua, poi ventotto giorni sugli scaffali, finché il cemento è forte.', '28', 'giorni')],
            icona='layers')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MANI, 'Le mani', 'Ogni piastrella <em>è un po’ diversa.</em>',
                             'Il colore non si stende mai due volte uguale: è il bello del fatto a mano.', overlay=24))
    out.append(t.section([
        t.one([K.sechead(t, 'Il laboratorio', ['Tre generazioni,', 'una pressa.'], size=56)]),
        K.squadra(t, [
            ('pexels-19734053.jpg', 'Rosalia Giacalone', 'Titolare, terza generazione', 'I decori su misura e il restauro dei '
             'pavimenti antichi.', 'center 25%'),
            ('pexels-38809083.jpg', 'Giorgia Lo Bue', 'Decoratrice', 'Gli stampi nuovi e i colori: ne prepara duecento.',
             'center 25%'),
            ('pexels-38021464.jpg', 'Marta Pecoraro', 'Showroom', 'Le cartelle dei colori e i preventivi per la posa.',
             'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    return out


def build_showroom():
    t = Tpl(ID, 'showroom')
    out = [K.intro(t, 'Lo showroom', ['Le piastrelle,', 'da toccare.'],
                   'Alla Kalsa, in via Alloro: le collezioni posate a terra, le cartelle dei colori e il laboratorio dietro.',
                   bg=K.photo_bg('pexels-37075230.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Vi interessa', 'cosa', opzioni='Una collezione\nUn decoro su misura\nIl restauro di un '
                     'pavimento\nUna visita al laboratorio'),
             K.campo('text', 'Metri quadri', 'mq'), K.campo('textarea', 'Il progetto', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['I metri quadri', 'e cosa sognate.'],
                                'Vi rispondiamo con le piastrelle di prova e un preventivo.', VOCI, campi,
                                'Cementina — preventivo', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(K.domande(t, 'Prima di ordinare', ['Le domande', 'di chi ristruttura.'], [
        ('Quanto tempo ci vuole?', 'Sei settimane dall’ordine: due per la produzione, quattro di riposo.'),
        ('Vanno bene in bagno?', 'Sì, con un trattamento antimacchia che facciamo noi prima della consegna.'),
        ('Spedite fuori Palermo?', 'In tutta Italia e in Europa, su bancale, con l’assicurazione.'),
        ('Posso avere un campione?', 'Sì: tre piastrelle di prova a 25 €, scontati dall’ordine.'),
    ], 'ce-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Alla Kalsa,', 'via Alloro 64.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Showroom', 'martedì — sabato 10 — 13 e 16 — 19'), ('A piedi', 'da piazza Marina, '
                                                                                                    'tre minuti'),
                                  ('Il laboratorio', 'su appuntamento')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Cementina · showroom', scuro=False, hex_marker=HEX['primary'])], wm='1-2',
                     ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · piastrella mancante', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui manca <em>una piastrella.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La rifaremo uguale. Intanto, torniamo all’ingresso.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_COLORE2, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Casa e design', 'Il pavimento che si gira',
    ['piastrelle', 'cementine', 'artigianato', 'pavimenti', 'palermo', 'chiaro', 'slider pro', '3d'],
    'Un laboratorio di cementine decorate a mano, cobalto, ocra e rosso pompeiano: in apertura sedici piastrelle di '
    'cemento nudo sul video di un pavimento antico, che scorrendo si girano in 3D in un’onda e mostrano il decoro; le '
    'collezioni da girare, il listino al metro quadro, come si fanno e lo showroom.',
    [('Le collezioni', COLLEZIONI), ('Il laboratorio', LAB), ('Lo showroom', SHOWROOM)],
    [('home', 'Home', None), ('collezioni', 'Le collezioni', 'collezioni'), ('laboratorio', 'Il laboratorio', 'laboratorio'),
     ('showroom', 'Lo showroom', 'showroom')],
    HEX, '\'Mulish\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Playfair Display\', Georgia, serif',
    '\'Noto Sans Mono\', ui-monospace, Menlo, monospace', ['Mulish', 'Playfair Display', 'Noto Sans Mono'],
    corsivo_di=['Playfair Display'], peso_titoli='500', raggio=0, grana=3, titoli_line='1.02', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'mosaico'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['secondary']
THEME['cursor']['hot_color'] = HEX['accent']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezioni.json': build_collezioni(), 'laboratorio.json': build_lab(),
                        'showroom.json': build_showroom(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
