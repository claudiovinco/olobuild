# -*- coding: utf-8 -*-
"""Genera il tema «Fermento» (id fermento): un birrificio artigianale con la tap room in Borgo Palazzo, a Bergamo.
Dodici birre l’anno, sei sempre in spina, la cucina della sera e le visite al sabato.

Tema scuro, nero di tostatura con l’ambra della birra, il verde del luppolo e il bianco della schiuma. Titoli in Big
Shoulders Display nero, testo in Barlow, numeri in JetBrains Mono.

Apertura: Slider Pro «le spine». Una diapositiva per birra, col suo video (le bolle, il luppolo, la spillatura): dietro,
enorme e solo contornato, lo stile (BLANCHE, IPA, AMBER, STOUT) che sale da sotto; davanti il nome, il racconto, e tre
barre che si riempiono di ambra per alcol, amaro e colore. Sopra tutte le diapositive salgono bolle vere, ognuna col
suo tempo. I numeri in basso sono le spine.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'fermento'
NAME = 'Fermento'

HEX = {
    'primary': '#F2A93B', 'primary_contrast': '#140F08', 'secondary': '#83A04A', 'secondary_contrast': '#0E0B07',
    'accent': '#F3E2C0', 'dark': '#070605', 'light': '#FFF6E6', 'muted': '#1A140E', 'muted_contrast': '#F3EADB',
    'text': '#F3EADB', 'text_muted': '#AB9C88', 'background': '#100C08', 'border': '#2E251B', 'link': '#F2A93B',
}

HOME, BIRRE, BIRRIFICIO, TAPROOM = '{{PAGINA:home}}', '{{PAGINA:le-birre}}', '{{PAGINA:il-birrificio}}', '{{PAGINA:tap-room}}'
MAIL, PHONE, PHONE_TEL = 'spina@fermento.example', '+39 035 000 2014', 'tel:+390350002014'
LAT, LNG = '45.6905', '9.6878'

V_SPINA, V_VERSA, V_BOLLE, V_LUPPOLO, V_MALTO, V_RUBINETTO, V_BRINDISI = (
    'pexels-video-6223837', 'pexels-video-15643096', 'pexels-video-7233690', 'pexels-video-27624193',
    'pexels-video-5538179', 'pexels-video-5532877', 'pexels-video-5847850')
PHOTOS = [
    'pexels-1921420.jpg', 'pexels-1267342.jpg', 'pexels-38209610.jpg', 'pexels-1267363.jpg', 'pexels-5537958.jpg',
    'pexels-5538254.jpg', 'pexels-9980815.jpg', 'pexels-5537955.jpg', 'pexels-32606571.jpg', 'pexels-26836163.jpg',
    'pexels-14421847.jpg', 'pexels-26836162.jpg', 'pexels-5538163.jpg', 'pexels-5538162.jpg', 'pexels-8545776.jpg',
    'pexels-1267361.jpg', 'pexels-18915643.jpg', 'pexels-6366897.jpg', 'pexels-1267314.jpg', 'pexels-1267330.jpg',
    'pexels-27623565.jpg', 'pexels-1269028.jpg', 'pexels-5530173.jpg', 'pexels-12774948.jpg', 'pexels-4968335.jpg',
    'pexels-5538224.jpg', 'pexels-8477416.jpg', 'pexels-7175953.jpg', 'pexels-7175986.jpg', 'pexels-1267349.jpg',
    'pexels-5532993.jpg',
]
VIDEOS = [V_SPINA, V_VERSA, V_BOLLE, V_LUPPOLO, V_MALTO, V_RUBINETTO, V_BRINDISI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=3, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True)
C = K.c
TITOLI, MONO = 'Big Shoulders Display', 'JetBrains Mono'
VOCI = [('map-pin', 'Via Borgo Palazzo 141 · Bergamo', TAPROOM + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]

BIRRE_SPINA = [  # nome, stile, abv, ibu, ebc, racconto, abbinamento, prezzo, foto
    ('Grano', 'Blanche', 4.8, 14, 6, 'Frumento crudo, coriandolo e scorza d’arancia amara. Torbida, fresca, si beve al '
     'sole.', 'insalate, pesce, il pomeriggio', '5 €', 'pexels-5538254.jpg'),
    ('Verde', 'IPA', 6.2, 55, 14, 'Tre luppoli in aggiunta a freddo: pompelmo, pino, un amaro lungo che pulisce la '
     'bocca.', 'burger, formaggi stagionati', '6 €', 'pexels-5537958.jpg'),
    ('Castagna', 'Amber', 5.6, 28, 30, 'Malto caramello e castagne della Val Brembana tostate al forno. Tonda, '
     'calda, d’autunno.', 'polenta, casoncelli, arrosti', '5,50 €', 'pexels-9980815.jpg'),
    ('Carbone', 'Stout', 7.0, 40, 80, 'Orzo tostato, caffè e un filo di cacao. Nera, cremosa, la schiuma color '
     'nocciola.', 'cioccolato, ostriche, la sera tardi', '6 €', 'pexels-1267363.jpg'),
    ('Nebbia', 'Hazy pale ale', 5.2, 30, 8, 'Avena e luppoli tropicali: succo di mango, poco amaro, molto profumo.',
     'piatti piccanti, tacos', '6 €', 'pexels-5538224.jpg'),
    ('Rintocco', 'Doppio malto', 8.5, 24, 45, 'La birra di Natale: datteri, prugne, miele di castagno. Va bevuta '
     'piano, in due.', 'panettone, formaggi erborinati', '7 €', 'pexels-27623565.jpg'),
]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Fermento', logo_size='30')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Fermento', 'Birrificio artigianale e tap room in Borgo Palazzo, a Bergamo, dal 2014. Sei birre '
                   'sempre in spina, la cucina della sera, le visite al sabato.',
                   [('Le birre', BIRRE), ('Il birrificio', BIRRIFICIO), ('La tap room', TAPROOM),
                    ('Dove siamo', TAPROOM + '#arrivare'), ('Privacy', '/privacy-policy/')],
                   'La tap room', 'Martedì — giovedì 17 — 24<br>Venerdì e sabato 17 — 1<br>Domenica 12 — 22',
                   VOCI, '© 2026 Birrificio Fermento srl · Bergamo · P.IVA 00000000000 · Bevete responsabilmente',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=180)


def apertura(t):
    m = K.mob
    sfondi = [K.sfondo_video(V_BOLLE, overlay=0.55), K.sfondo_video(V_LUPPOLO, overlay=0.6),
              K.sfondo_video(V_SPINA, overlay=0.5), K.sfondo_foto('pexels-1267363.jpg', overlay=0.55, scala=1.1)]
    sl = []
    for i, (nome, stile, abv, ibu, ebc, racconto, abb, prezzo, foto) in enumerate(BIRRE_SPINA[:4]):
        n = i + 1
        livelli = [
            K.livello('fe-w%d' % n, contenuto=stile.upper(), x=34, y=6, w=63, size=280, peso='800', colore='transparent',
                      align='right', fontFamily=TITOLI, lineHeight=0.86, entra='maskFromBottom', ritardo=100, durata=1500,
                      esce='fadeOut', textStrokeWidth=2, textStrokeColor=mix(C['acc2'], 42), parallaxDepth=3,
                      responsive={'mobile': {'x': 4, 'y': 10, 'width': 92, 'fontSize': m(108)}}),
            K.livello('fe-o%d' % n, contenuto='Spina 0%d · %s' % (n, stile), x=6, y=30, w=40, size=13, peso='500',
                      colore=C['acc'], letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=300,
                      responsive={'mobile': {'fontSize': m(11), 'y': 38, 'x': 7, 'width': 86}}),
            K.livello('fe-t%d' % n, contenuto=nome, tag='h1' if n == 1 else 'h2', x=6, y=33, w=50, size=168, peso='800',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=0.9, textTransform='uppercase', entra='fadeIn',
                      ritardo=450, durata=1100, charAnim={'stagger': 55, 'offsetY': 60, 'blur': 0, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(76), 'y': 40.4, 'x': 7, 'width': 86}}),
            K.livello('fe-s%d' % n, contenuto=racconto, x=6, y=53, w=30, size=18, peso='400', colore=mix(C['chiaro'], 86),
                      lineHeight=1.55, ritardo=1100, responsive={'mobile': {'fontSize': m(15), 'y': 50.6, 'x': 7, 'width': 86}}),
        ]
        # tre barre che si riempiono: alcol, amaro, colore
        for k, (etich, val, mass, testo) in enumerate([('Alcol', abv, 10, '%s%%' % str(abv).replace('.', ',')),
                                                       ('Amaro', ibu, 80, '%d IBU' % ibu), ('Colore', ebc, 90, '%d EBC' % ebc)]):
            y = 67 + k * 4.6
            ym = 61.5 + k * 3.2
            livelli += [
                K.livello('fe-l%d%d' % (n, k), contenuto=etich, x=6, y=y - 0.9, w=8, size=12, peso='500',
                          colore=mix(C['chiaro'], 70), letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                          ritardo=1300 + 120 * k, entra='fadeIn',
                          responsive={'mobile': {'x': 7, 'y': ym - 0.6, 'width': 22, 'fontSize': m(10)}}),
                K.livello('fe-r%d%d' % (n, k), tipo='shape', x=14, y=y, w=16, h=0.5, bgColor=mix(C['chiaro'], 16),
                          borderRadius=4, entra='fadeIn', ritardo=1300 + 120 * k,
                          responsive={'mobile': {'x': 30, 'y': ym, 'width': 44, 'height': 0.3}}),
                K.livello('fe-f%d%d' % (n, k), tipo='shape', x=14, y=y, w=round(16 * min(val, mass) / mass, 2), h=0.5,
                          bgColor=C['acc'], borderRadius=4, entra='maskFromLeft', ritardo=1500 + 160 * k, durata=1300,
                          boxShadow={'x': 0, 'y': 0, 'blur': 12, 'spread': 0, 'color': mix(C['acc'], 70)},
                          responsive={'mobile': {'x': 30, 'y': ym, 'width': round(44 * min(val, mass) / mass, 2), 'height': 0.3}}),
                K.livello('fe-v%d%d' % (n, k), contenuto=testo, x=31.5, y=y - 0.9, w=8, size=12, peso='500', colore=C['chiaro'],
                          fontFamily=MONO, ritardo=1800 + 160 * k, entra='fadeIn',
                          responsive={'mobile': {'x': 77, 'y': ym - 0.6, 'width': 18, 'fontSize': m(10)}}),
            ]
        livelli += [
            K.livello('fe-b%d' % n, tipo='button', contenuto='Le birre in spina', x=6, y=82, w='auto', size=13, peso='700',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=3, ritardo=2100, buttonUrl=BIRRE, hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 73.5, 'x': 7, 'fontSize': m(12)}}),
            K.livello('fe-p%d' % n, contenuto='%s · 0,4 l' % prezzo, x=22, y=83.3, w='auto', size=14, peso='500',
                      colore=C['chiaro'], fontFamily=MONO, ritardo=2300, entra='fadeIn',
                      responsive={'mobile': {'y': 74.8, 'x': 60, 'fontSize': m(12)}}),
        ]
        sl.append(K.diapositiva('fe-%d' % n, sfondi[i], livelli, durata=7500))
    # le bolle che salgono, ognuna col suo passo
    bolle = []
    for k, (x, sz, dur, rit) in enumerate([(58, 0.9, 7000, 0), (63, 0.5, 5200, 1400), (67, 1.2, 8600, 600),
                                           (72, 0.6, 6100, 2600), (76, 0.8, 7400, 400), (80, 0.45, 5000, 3100),
                                           (84, 1.0, 8000, 1800), (88, 0.55, 5600, 900), (92, 0.75, 6800, 2200),
                                           (69, 0.4, 4800, 3600), (86, 0.35, 4600, 4200), (95, 0.6, 6200, 500)]):
        deriva = 1.2 if k % 2 else -1.0
        bolle.append(K.livello('fe-g%d' % k, tipo='shape', x=x, y=104, w=sz, h=sz * 1.6, bgColor=mix(C['acc2'], 10),
                               borderRadius=999, borderWidth=1, borderColor=mix(C['acc2'], 55),
                               timeline={'duration': dur, 'delay': rit, 'loop': True, 'keyframes': [
                                   {'time': 0, 'props': {'x': x, 'y': 104, 'scale': 0.6, 'opacity': 0}, 'easing': 'ease-in'},
                                   {'time': int(dur * 0.12), 'props': {'x': x + deriva * 0.3, 'y': 90, 'scale': 0.8, 'opacity': 85},
                                    'easing': 'linear'},
                                   {'time': int(dur * 0.6), 'props': {'x': x + deriva, 'y': 40, 'scale': 1, 'opacity': 75},
                                    'easing': 'ease-out'},
                                   {'time': dur, 'props': {'x': x - deriva * 0.4, 'y': -6, 'scale': 1.15, 'opacity': 0}}]},
                               responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=bolle, transizione='fadeThroughDark', durata_trans=1300, showArrows=True,
                       showDots=True, dotStyle='numbers', parallax=True, parallaxIntensity=5, autoplaySpeed=7500)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    lavagna = t.classic('chalkboard', {
        'occhiello': 'Stasera in spina', 'titolo': 'Sei spine, sei storie', 'titolo_tag': 'h3', 'data_mostra': True,
        'data_formato': 'giorno_data',
        'voci': [{'id': 'fe-n%d' % k, 'nome': '%s · %s' % (b[0], b[1]), 'descrizione': '%s%% · %s' % (str(b[2]).replace('.', ','), b[6]),
                  'prezzo': b[7], 'giorni': '', 'evidenza': 'sottolineato' if k == 1 else '',
                  'etichetta': 'nuova' if k == 4 else ''} for k, b in enumerate(BIRRE_SPINA)],
        'vuoto_testo': 'Stasera le spine si riposano: la lavagna è pulita.', 'nota': 'Il gesso è sul bancone: scrivete '
        'la vostra preferita.', 'firma': '— Luca, il birraio',
        'disegni': [{'id': 'fe-d1', 'forma': 'spiga', 'icona': '', 'immagine': '', 'x': 91, 'y': 14, 'misura': 70,
                     'rotazione': -10, 'profondita': 70, 'colore': '', 'opacita': 85},
                    {'id': 'fe-d2', 'forma': 'icona', 'icona': 'hop', 'immagine': '', 'x': 93, 'y': 82, 'misura': 56,
                     'rotazione': 12, 'profondita': 45, 'colore': '', 'opacita': 80},
                    {'id': 'fe-d3', 'forma': 'stella', 'icona': '', 'immagine': '', 'x': 6, 'y': 88, 'misura': 34,
                     'rotazione': 0, 'profondita': 30, 'colore': '', 'opacita': 75}],
        'scrittura': 'scrive', 'scrittura_velocita': 'veloce', 'fondo': mix(C['nero'], 88, C['sec']),
        'accento_colore': C['acc'], 'texture': 'gesso', 'cornice': 'legno', 'appesa': True, 'inclinazione': -1.0,
        'larghezza': 760, 'allineamento': 'left', 'colonne': 1}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'In spina', ['Sei birre sempre,', 'una che cambia.'], size=64, layout='stack',
                                size_mobile=42),
                      K.text(t, 'La lavagna la scrive Luca ogni pomeriggio alle cinque, quando apre la tap room. Se una '
                                'spina è finita lo vedete subito: c’è la riga sopra.', size=17, color=C['testo']),
                      K.button(t, 'Tutte le birre', BIRRE, pad_top=18)], wm='2-5', ws='1-1'),
               t.col([lavagna], wm='3-5', ws='1-1')], layout='custom', custom='38,62', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='spine', style={'padding_top': 140, 'padding_bottom': 140}))
    classifica = t.classic('leaderboard', {
        'source': 'manual',
        'rows': [{'name': n, 'role': r, 'value': v, 'max': 900} for n, r, v in [
            ('Verde', 'IPA · la più amara', 812), ('Grano', 'Blanche · la più fresca', 744), ('Carbone', 'Stout · la più nera', 531),
            ('Castagna', 'Amber · d’autunno', 488), ('Nebbia', 'Hazy · la nuova', 362)]],
        'value_suffix': 'pinte', 'show_position': True, 'show_value': True, 'show_role': True, 'animate_on_view': True,
        'highlight_top': 1, 'row_bg': C['sala'], 'text_color': C['testo'], 'role_color': C['tenue'],
        'position_color': C['tenue'], 'badge_bg': mix(C['acc'], 16), 'badge_color': C['acc'],
        'bar_track_color': mix(C['testo'], 10), 'bar_gradient_from': C['sec'], 'bar_gradient_to': C['acc'],
        'highlight_color': C['acc'], 'name_size': 20, 'name_weight': '800', 'position_size': 30,
        'border_radius': radius(4)}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Ottobre', ['Le più spinate', 'del mese.'], size=64, layout='stack', size_mobile=42),
                      K.text(t, 'Le pinte versate alla tap room dal primo del mese. La Verde vince da sei mesi; la Nebbia è '
                                'uscita da tre settimane e sta già rincorrendo.')], wm='2-5', ws='1-1'),
               t.col([classifica], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_MALTO, 'Dal 2014', 'Malto, acqua, luppolo, <em>pazienza.</em>',
                             'Ventun giorni per una bionda, novanta per la Rintocco. Non abbiamo fretta: la birra si fa '
                             'nei tini, non al telefono.', overlay=55))
    piani = [('Visita breve', '15', 'il sabato alle 11', 'Il giro dei tini con Luca\nTre assaggi dalla spina\nIl '
              'bicchiere da portare a casa', False, ''),
             ('Visita e degustazione', '25', 'il sabato alle 16', 'Il giro dei tini e del magazzino\nSei birre col '
              'tagliere\nLe note di degustazione stampate', True, 'La più scelta'),
             ('Birraio per un giorno', '120', 'una domenica al mese', 'Una cotta dall’inizio alla fine\nIl pranzo in '
              'birrificio\nSei bottiglie della vostra birra', False, '')]
    cols = []
    for nome, prezzo, periodo, voci, pop, badge in piani:
        cols.append(t.col([t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': periodo,
            'features': voci, 'check_style': 'circle-check', 'cta_text': 'Prenota', 'cta_url': TAPROOM + '#prenota',
            'cta_bg_color': C['acc'] if pop else 'transparent', 'cta_text_color': C['on_acc'] if pop else C['testo'],
            'cta_border_width': '0' if pop else '1', 'cta_border_color': C['testo'], 'cta_radius': '3',
            'cta_hover_bg_color': C['chiaro'], 'additional_info': 'Dai 18 anni · massimo 14 persone',
            'is_popular': pop, 'badge_text': badge, 'badge_bg_color': C['acc'], 'badge_text_color': C['on_acc'],
            'bg_color': C['sala'], 'accent_color': C['acc'], 'text_color': C['testo'], 'price_color': C['chiaro'],
            'border_radius': '4', 'shadow': 'none'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le visite', ['Il sabato', 'si apre il birrificio.'], size=64,
                         tagline='Si vede dove nasce la birra, si sente l’odore del mosto, si assaggia dalla spina del tino.')]),
        t.row(cols, layout='33-33-33', gap=24, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_BRINDISI, 'La tap room', 'Il bancone <em>è lungo dodici metri.</em>',
                          'Sei spine, la cucina fino a mezzanotte, i tavoli di botte e la musica bassa. Il venerdì c’è il '
                          'quiz, la domenica il pranzo lungo.', 'Prenota un tavolo', TAPROOM + '#prenota', overlay=55, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Fermento',
                page_intro_caption='Birrificio · Bergamo', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def build_birre():
    t = Tpl(ID, 'le-birre')
    out = [K.intro(t, 'Le birre', ['Dodici l’anno,', 'sei sempre in spina.'],
                   'Ogni birra ha la sua scheda: alcol, amaro (IBU), colore (EBC), con cosa berla. In bottiglia le trovate '
                   'al bancone e nelle enoteche di Bergamo.', bg=K.photo_bg('pexels-27623565.jpg', overlay=58),
                   overlay_text=True)]
    for k, (nome, stile, abv, ibu, ebc, racconto, abb, prezzo, foto) in enumerate(BIRRE_SPINA):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, 'Un bicchiere di %s' % nome, ratio='4/5')], wm='2-5', ws='1-1'),
                   t.col([K.label(t, 'Spina 0%d · %s' % (k + 1, stile), color=C['acc']),
                          K.headline(t, nome, size=96, tag='h2', pad_bottom=12, maiuscolo=True),
                          K.text(t, racconto, size=17),
                          K.specs(t, [('Alcol', '%s%%' % str(abv).replace('.', ',')), ('Amaro', '%d IBU' % ibu),
                                      ('Colore', '%d EBC' % ebc), ('Con', abb), ('Alla spina', prezzo + ' · 0,4 l')])],
                         wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=nome.lower(), bg=solid(C['sala']) if k % 2 == 0 else None,
            style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Le domande', 'al bancone.', [
        ('Le birre sono filtrate?', 'No: non filtrate e non pastorizzate. Per questo il fondo della bottiglia è un po’ '
                                    'torbido, ed è buono.'),
        ('Avete birre senza glutine?', 'Una sola, la Grano Zero, solo in bottiglia. Le altre contengono orzo e frumento.'),
        ('Fate i fusti per le feste?', 'Sì, da 20 e 30 litri con la spillatrice in prestito. Prenotateli una settimana prima.'),
        ('Si comprano online?', 'Per ora no: al bancone, o nelle enoteche che trovate nella pagina della tap room.'),
    ], 'br', bg=True))
    return out


def build_birrificio():
    t = Tpl(ID, 'il-birrificio')
    out = [K.intro(t, 'Il birrificio', ['Diecimila litri', 'dietro una porta.'],
                   'Dietro il bancone della tap room ci sono la sala cotte, otto tini di fermentazione e la cella fredda. '
                   'Il sabato si visitano, gli altri giorni si sente il profumo dalla strada.',
                   bg=K.photo_bg('pexels-18915643.jpg', overlay=55), overlay_text=True)]
    passi = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('01', 'Lunedì', 'pexels-5538163.jpg', 'Il malto', 'Si macina', 'e si scalda', 'L’orzo maltato rotto a '
             'mano nel mulino, poi in acqua a 67 gradi: gli zuccheri si sciolgono.', '67 °C', 'per un’ora'),
            ('02', 'Lunedì', 'pexels-26836163.jpg', 'Il luppolo', 'Si bolle', 'col luppolo', 'Il mosto bolle novanta '
             'minuti: il luppolo dà l’amaro all’inizio, il profumo alla fine.', '90 min', 'di bollitura'),
            ('03', 'Tre settimane', 'pexels-1267361.jpg', 'I tini', 'Fermenta', 'e matura', 'Il lievito lavora nei tini, '
             'poi la birra riposa al freddo. Nessuna fretta, nessun filtro.', '21 giorni', 'almeno')]],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 20), 'timeline_dot_color': C['acc'],
        'counter_font_family': 'heading', 'counter_size': 88, 'counter_color': C['acc'], 'counter_italic': False,
        'counter_weight': '800', 'tag_color': C['chiaro'], 'pre_title_color': C['acc'], 'title_font_family': 'heading',
        'title_size': 34, 'title_weight': '800', 'title_color': C['chiaro'], 'title_accent_color': C['acc'],
        'description_color': mix(C['chiaro'], 72), 'footer_icon': 'timer', 'footer_value_color': C['chiaro'],
        'footer_label_color': mix(C['chiaro'], 55), 'columns': 3, 'gap': 48, 'media_radius': radius(4)}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Una cotta', ['Dal sacco di malto', 'al bicchiere.'], size=64)]),
        t.one([passi]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    persone = [('pexels-7175986.jpg', 'Luca Brambilla', 'Il birraio', 'Ingegnere chimico, ha cominciato in garage nel '
                '2009. Fa le ricette, assaggia ogni tino, scrive la lavagna.'),
               ('pexels-1267349.jpg', 'Sara Pellegrini', 'Cantina e qualità', 'Segue fermentazioni e imbottigliamento. Ha '
                'portato la Nebbia e le visite del sabato.'),
               ('pexels-5532993.jpg', 'Davide Rota', 'La tap room', 'Dietro il bancone dal primo giorno. Sa abbinare una '
                'birra a qualunque piatto, anche al panettone.')]
    cols = []
    for foto, nome, ruolo, bio in persone:
        cols.append(t.col([t.classic('team', {
            'photo': K.img(foto), 'hover_image': '', 'hover_video': '', 'name': nome, 'role': ruolo, 'bio': bio,
            'link_text': '', 'link_url': '', 'photo_size': '300', 'photo_ratio': '4/5', 'photo_fit': 'cover',
            'photo_object_position': '50% 25%', 'photo_shape': 'rounded', 'photo_radius': '4', 'photo_border_width': '0',
            'photo_shadow': 'none', 'info_bg_color': C['sala'], 'info_text_color': C['testo'], 'info_padding': '24',
            'info_radius': '4', 'info_align': 'left', 'name_size': '26', 'name_weight': '800', 'role_size': '13',
            'role_color': C['acc'], 'bio_size': '15', 'bg_color': 'transparent'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi fa la birra', ['Tre persone,', 'otto tini.'], size=64)]),
        t.row(cols, layout='33-33-33', gap=24, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_LUPPOLO, 'Il luppolo', 'Lo andiamo a prendere <em>in campo.</em>',
                             'A settembre i luppoli freschi delle colline di Bergamo, raccolti la mattina e nel tino la sera, '
                             'per la Verde d’autunno.', overlay=50))
    return out


def build_taproom():
    t = Tpl(ID, 'tap-room')
    out = [K.intro(t, 'La tap room', ['Sei spine,', 'tavoli di botte.'],
                   'Si mangia fino a mezzanotte: taglieri, panini col pane del forno di via Moroni, le patate fritte due '
                   'volte. Il venerdì il quiz, la domenica il pranzo lungo.', bg=K.photo_bg('pexels-1269028.jpg', overlay=55),
                   overlay_text=True)]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Martedì — giovedì', 'time': '17 — 24', 'note': 'La cucina chiude alle 23'},
                  {'day': 'Venerdì e sabato', 'time': '17 — 1', 'note': 'Il venerdì alle 21 il quiz'},
                  {'day': 'Domenica', 'time': '12 — 22', 'note': 'Il pranzo lungo, su prenotazione'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': C['acc'], 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 56,
        'time_weight': '800', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    cucina = t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in [
                      ('Il tagliere bergamasco', 'Strachitunt, branzi, salame di Gandino, mostarda', '16 €', 'pexels-8477416.jpg', 'Da dividere'),
                      ('Panino della spina', 'Porchetta, cipolla alla Castagna, senape', '11 €', 'pexels-8477416.jpg', ''),
                      ('Patate fritte due volte', 'Con la maionese alla Verde', '6 €', 'pexels-26836162.jpg', ''),
                      ('Brownie alla Carbone', 'Cioccolato fondente e la nostra stout', '7 €', 'pexels-1267363.jpg', '')]],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 30), 'title_color': C['testo'],
        'price_color': C['acc'], 'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '4',
        'show_image': True, 'price_position': 'right', 'badge_bg': C['acc'], 'badge_color': C['on_acc'],
        'badge_border_radius': '3', 'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'},
        style={})
    out.append(t.section([
        t.one([orari]),
        t.row([t.col([K.sechead(t, 'La cucina', ['Si mangia', 'fino a mezzanotte.'], size=60, layout='stack', size_mobile=40),
                      K.text(t, 'Pochi piatti, fatti per la birra. Il pane arriva ogni pomeriggio, i formaggi dalle valli.')],
                     wm='2-5', ws='1-1'),
               t.col([cucina], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start',
              style={'margin_top': 80}),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email'), K.campo('select', 'Cosa prenotate', 'cosa', True,
                                                         opzioni='Un tavolo\nLa visita breve\nVisita e degustazione\nBirraio per un giorno\nIl quiz del venerdì'),
             K.campo('date', 'Il giorno', 'data', True), K.campo('text', 'In quanti', 'persone', segnaposto='Es. 4'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Un compleanno, un’allergia, un tavolo fuori.')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Un tavolo', 'o una visita.'],
                                'Per i tavoli del venerdì e sabato scriveteci entro il pomeriggio. Le visite si chiudono a '
                                'quattordici persone.', VOCI, campi, 'Fermento — prenotazione', 'Prenota',
                                'Ricevuto! Vi confermiamo con un messaggio entro qualche ora.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Borgo Palazzo,', 'il portone verde.'], size=56, layout='stack', size_mobile=38),
                      K.specs(t, [('Indirizzo', 'via Borgo Palazzo 141, Bergamo'), ('Bus', 'linea 5, fermata Borgo Palazzo'),
                                  ('Parcheggio', 'nel cortile dopo le 19'), ('In bottiglia', 'anche in sei enoteche di città')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fermento · birrificio e tap room', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · spina vuota', color=C['acc'], align='center'),
                       K.headline(t, 'Questo fusto <em>è finito.</em>', size=110, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Cambiamo fusto e torniamo al bancone.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna al bancone', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_VERSA, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'Malto, luppolo, pazienza',
    ['birrificio', 'birra artigianale', 'tap room', 'bergamo', 'scuro', 'slider pro'],
    'Un birrificio artigianale con la tap room a Bergamo, nero di tostatura e ambra: in apertura le quattro spine coi '
    'loro video, lo stile enorme contornato dietro e le barre di alcol, amaro e colore che si riempiono, con le bolle che '
    'salgono; la lavagna a gesso della sera, le più spinate del mese, le visite e la cucina.',
    [('Le birre', BIRRE), ('Il birrificio', BIRRIFICIO), ('In spina', HOME + '#spine'), ('Tap room', TAPROOM)],
    [('home', 'Home', None), ('le-birre', 'Le birre', 'le-birre'), ('il-birrificio', 'Il birrificio', 'il-birrificio'),
     ('tap-room', 'La tap room', 'tap-room')],
    HEX, '\'Barlow\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Big Shoulders Display\', \'Arial Narrow\', sans-serif', '\'JetBrains Mono\', ui-monospace, Menlo, monospace',
    ['Big Shoulders Display', 'Barlow', 'JetBrains Mono'], corsivo_di=[], peso_titoli='800', raggio=3, grana=5,
    titoli_line='0.92', titoli_spazio='0')
THEME['styles']['google_fonts_weights'] = '400;500;600;700;800'


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-birre.json': build_birre(), 'il-birrificio.json': build_birrificio(), 'tap-room.json': build_taproom(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
