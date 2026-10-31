# -*- coding: utf-8 -*-
"""Genera il tema «Diottria» (id diottria): un'ottica con optometrista a Napoli, montature scelte da piccoli
produttori, occhiali da sole, occhiali per bambini e l'esame della vista su appuntamento.

Tema chiaro e nitido: bianco ottico, blu oltremare e un giallo sole per i bollini. Titoli e testo in Geist, dati in
Geist Mono: una sola famiglia, come una montatura ben fatta.

Apertura: Slider Pro «la vetrina», alto 16:9. Nelle prime tre diapositive la montatura della stagione gira su sé stessa
in 3D (rotazione sull'asse verticale, avanti e indietro, come sul piatto girevole di una vetrina), con il tondo di chi
la porta; nell'ultima la tavola dell'esame della vista, con le righe di lettere che passano da sfocate a nitide una
dopo l'altra, come quando l'optometrista trova la lente giusta.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'diottria'
NAME = 'Diottria'

HEX = {
    'primary': '#2340D0', 'primary_contrast': '#FFFFFF', 'secondary': '#0F766E', 'secondary_contrast': '#FFFFFF',
    'accent': '#F2B33D', 'dark': '#11151C', 'light': '#FFFFFF', 'muted': '#E7EAE5', 'muted_contrast': '#141820',
    'text': '#141820', 'text_muted': '#5B6270', 'background': '#F3F4F1', 'border': '#D3D8D0', 'link': '#2340D0',
}

HOME, MONTATURE, ESAME, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:montature}}', '{{PAGINA:l-esame}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'ottica@diottria.example', '+39 081 000 2020', 'tel:+390810002020'
LAT, LNG = '40.8443', '14.2486'

V_OCCHIALI, V_FORO, V_OCCHIO, V_SOLE, V_LEI = ('pexels-video-6332725', 'pexels-video-5241181', 'pexels-video-5995137',
                                              'pexels-video-5241102', 'pexels-video-8136709')
PHOTOS = [
    'pexels-28834289.jpg', 'pexels-28488304.jpg', 'pexels-28578769.jpg', 'pexels-28881361.jpg', 'pexels-26050120.jpg',
    'pexels-12720210.jpg', 'pexels-4039400.jpg', 'pexels-27471495.jpg', 'pexels-14849415.jpg', 'pexels-8084237.jpg',
    'pexels-1669595.jpg', 'pexels-38185097.jpg', 'pexels-12936741.jpg', 'pexels-7819295.jpg', 'pexels-22042260.jpg',
    'pexels-9396142.jpg', 'pexels-2267817.jpg', 'pexels-8971514.jpg', 'pexels-17151925.jpg', 'pexels-7256348.jpg',
    'pexels-19122051.jpg', 'pexels-8554143.jpg', 'pexels-19364613.jpg', 'pexels-8499536.jpg', 'pexels-13594615.jpg',
    'pexels-6749729.jpg', 'pexels-6749781.jpg', 'pexels-5996759.jpg', 'pexels-3846022.jpg', 'pexels-5996643.jpg',
    'pexels-5202046.jpg', 'pexels-5202060.jpg', 'pexels-29348783.jpg', 'pexels-29191794.jpg', 'pexels-9929047.jpg',
    'pexels-4347472.jpg', 'pexels-11377318.jpg', 'pexels-6749735.jpg', 'pexels-6749756.jpg', 'pexels-14683152.jpg',
    'pexels-1694208.jpg', 'pexels-4037164.jpg', 'pexels-7117764.jpg',
]
VIDEOS = [V_OCCHIALI, V_FORO, V_OCCHIO, V_SOLE, V_LEI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=20, peso_titoli='600', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media=mix('var(--olo-color-accent)', 85, 'var(--olo-color-light)'))
C = K.c
TITOLI, MONO = 'Geist', 'Geist Mono'
VOCI = [('map-pin', 'Via Toledo 210 · Napoli', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Diottria', logo_size='24', scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Diottria', 'Ottica e optometria in via Toledo: l’esame della vista su appuntamento, montature di '
                   'piccoli produttori, lenti montate nel nostro laboratorio, occhiali per bambini garantiti due anni.',
                   [('Le montature', MONTATURE), ('L’esame della vista', ESAME), ('Prenota', PRENOTA),
                    ('Come arrivare', PRENOTA + '#arrivare'), ('Privacy', '/privacy-policy/')],
                   'Il negozio', 'Lunedì — sabato 9:30 — 19:30<br>Esami della vista su appuntamento', VOCI,
                   '© 2026 Diottria srl · Napoli · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=130, fondo=C['sala'])


VETRINA = [  # linguetta, occhiello, riga 1, riga 2, testo, montatura, chi la porta, didascalia, prezzo, fondo
    ('Vista', '01 · da vista', 'Vedere bene', 'è un dettaglio.', 'Montature in acetato e titanio da piccoli produttori del '
     'Cadore, scelte una per una. Le lenti le montiamo noi, in laboratorio, mentre aspettate un caffè.',
     'pexels-28488304.jpg', 'pexels-22042260.jpg', 'Bruma 47 · acetato blu notte', 'da 160 €', mix(C['acc'], 12, C['fondo'])),
    ('Sole', '02 · da sole', 'Lenti polarizzate,', 'montature leggere.', 'Le lenti da sole anche graduate, nei colori che '
     'non falsano il mondo: grigio, marrone, verde. Il rosa per chi vuole vederlo più bello.',
     'pexels-1669595.jpg', 'pexels-7117764.jpg', 'Rosa 49 · metallo dorato', 'da 140 €', mix(C['acc2'], 26, C['fondo'])),
    ('Bambini', '03 · per i bambini', 'Occhiali che', 'resistono a tutto.', 'Cerniere flessibili, plastica che non si '
     'rompe, lenti infrangibili. Se si rompono entro due anni, li rifacciamo gratis.',
     'pexels-28881361.jpg', 'pexels-13594615.jpg', 'Tondo 42 · acetato arancio', 'da 95 €', mix(C['sec'], 12, C['fondo'])),
]
SNELLEN = ['E', 'F P', 'T O Z', 'L P E D', 'P E C F D', 'E D F C Z P', 'F E L O P Z D']


def apertura(t):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, montatura, persona, didascalia, prezzo, fondo) in enumerate(VETRINA):
        n = i + 1
        cx, cy, cw, ch = 54.0, 20.0, 37.5, 48.0
        sl.append(K.diapositiva('di-%d' % n, {'type': 'gradient', 'gradientFrom': C['fondo'], 'gradientTo': fondo,
                                              'gradientAngle': 120, 'image': '', 'video': '', 'color': '', 'overlay': '',
                                              'overlayOpacity': 0, 'kenBurns': False}, [
            # la montatura sul piatto girevole: gira avanti e indietro sull'asse verticale
            K.livello('di-m%d' % n, tipo='image', imageSrc=K.img(montatura), objectFit='cover', x=cx, y=cy, w=cw, h=ch,
                      borderRadius=28, boxShadow={'x': 0, 'y': 40, 'blur': 80, 'spread': -24, 'color': mix(C['nero'], 35)},
                      timeline=K.tl(cx, cy, [(0, {'rotationY': -26, 'rotationX': 6, 'opacity': 0, 'scale': 0.9}, 'power3.out'),
                                             (1200, {'rotationY': -18, 'rotationX': 6, 'opacity': 100, 'scale': 1}, 'sine.inOut'),
                                             (5200, {'rotationY': 18, 'rotationX': 6, 'opacity': 100, 'scale': 1}, 'sine.inOut'),
                                             (9200, {'rotationY': -18, 'rotationX': 6, 'opacity': 100, 'scale': 1})], 9200,
                                    loop=False),
                      responsive={'mobile': {'x': 8, 'y': 8, 'width': 84, 'height': 25.7}}),
            K.livello('di-f%d' % n, tipo='image', imageSrc=K.img(persona), objectFit='cover', x=49, y=58, w=11.11, h=19.75,
                      borderRadius=999, borderWidth=6, borderColor=C['chiaro'], entra='popUpBack', ritardo=1300, durata=800,
                      esce='fadeOut', parallax={'type': 'mouse', 'depth': 6, 'tilt3d': True, 'scrollSpeed': 0.5},
                      boxShadow={'x': 0, 'y': 18, 'blur': 40, 'spread': -12, 'color': mix(C['nero'], 35)},
                      responsive={'mobile': {'x': 6, 'y': 27.5, 'width': 25, 'height': 11.43}}),
            K.livello('di-d%d' % n, contenuto=didascalia, x=61, y=72, w='auto', size=12, peso='500', colore=C['testo'],
                      fontFamily=MONO, letterSpacing=1, bgColor=C['chiaro'], padding=10, borderRadius=999, entra='fadeInLeft',
                      ritardo=1600, responsive={'mobile': {'x': 33, 'y': 33.5, 'width': 'auto', 'fontSize': m(10)}}),
            K.livello('di-o%d' % n, contenuto=occ, x=6, y=20, w=40, size=12, peso='500', colore=C['acc'], letterSpacing=2,
                      textTransform='uppercase', fontFamily=MONO, ritardo=250,
                      responsive={'mobile': {'fontSize': m(11), 'y': 42, 'x': 7, 'width': 86}}),
            K.livello('di-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=24.5, w=46, size=76, peso='600',
                      colore=C['testo'], fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=350,
                      durata=1100, charAnim={'stagger': 24, 'offsetY': 0, 'blur': 14, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 44.5, 'x': 7, 'width': 88}}),
            K.livello('di-i%d' % n, contenuto=r2, tag='p', x=6, y=35, w=46, size=76, peso='600', colore=C['acc'],
                      fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=700, durata=1100,
                      charAnim={'stagger': 24, 'offsetY': 0, 'blur': 14, 'opacity': 0},
                      responsive={'mobile': {'fontSize': m(38), 'y': 49, 'x': 7, 'width': 88}}),
            K.livello('di-s%d' % n, contenuto=testo, x=6, y=50, w=33, size=17, peso='400', colore=C['tenue'],
                      lineHeight=1.65, ritardo=1000, responsive={'mobile': {'fontSize': m(14), 'y': 55, 'x': 7, 'width': 86}}),
            K.livello('di-b%d' % n, tipo='button', contenuto='Prenota l’esame', x=6, y=71, w='auto', size=14, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, ritardo=1300,
                      buttonUrl=PRENOTA, hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 69, 'x': 7, 'fontSize': m(13)}}),
            K.livello('di-p%d' % n, contenuto=prezzo, x=19.5, y=72.2, w='auto', size=13, peso='500', colore=C['testo'],
                      bgColor=C['acc2'], padding=12, borderRadius=999, fontFamily=MONO, entra='popUpBack', ritardo=1550,
                      responsive={'mobile': {'y': 69.6, 'x': 53, 'fontSize': m(12)}}),
        ], durata=9200, tab=tab))
    # l'esame: la tavola di Snellen che va a fuoco riga per riga
    tav = [K.livello('di-tav', tipo='shape', x=58, y=8, w=32, h=78, bgColor=C['chiaro'], borderRadius=28, entra='zoomIn',
                     ritardo=0, durata=900, esce='fadeOut',
                     parallax={'type': 'mouse', 'depth': 4, 'tilt3d': True, 'scrollSpeed': 0.5},
                     boxShadow={'x': 0, 'y': 40, 'blur': 80, 'spread': -24, 'color': mix(C['nero'], 30)},
                     responsive={'mobile': {'x': 10, 'y': 7, 'width': 80, 'height': 33}})]
    righe = [(120, 13, 100, 6.2), (84, 30, 70, 13.6), (62, 42.5, 52, 19), (46, 52.5, 40, 23.3), (34, 60.5, 30, 26.6),
             (26, 67, 23, 29.2), (20, 72.5, 18, 31.4)]
    for k, (testo, (sz, y, msz, my)) in enumerate(zip(SNELLEN, righe)):
        tav.append(K.livello('di-r%d' % k, contenuto=testo, x=58, y=y, w=32, size=sz, peso='600', colore=C['testo'],
                             align='center', fontFamily=TITOLI, lineHeight=1.0, letterSpacing=int(sz * 0.18), entra='fadeIn',
                             ritardo=500 + 380 * k, durata=900,
                             charAnim={'stagger': 70, 'offsetY': 0, 'blur': 16, 'opacity': 20},
                             responsive={'mobile': {'x': 10, 'y': my + 3.5, 'width': 80, 'fontSize': msz}}))
    tav.append(K.livello('di-dieci', contenuto='10/10', x=84, y=66.7, w=5, size=11, peso='500', colore=C['acc'], fontFamily=MONO,
                         entra='fadeIn', ritardo=3200, responsive={'mobile': {'x': 80, 'y': 32.6, 'fontSize': m(9)}}))
    tav.append(K.livello('di-filo', tipo='shape', x=60, y=70.4, w=24, h=0.25, bgColor=C['acc'], entra='maskFromLeft',
                         ritardo=3000, durata=900, responsive={'mobile': {'x': 14, 'y': 34.4, 'width': 66, 'height': 0.14}}))
    tav += [
        K.livello('di-o4', contenuto='04 · l’esame della vista', x=6, y=20, w=40, size=12, peso='500', colore=C['acc'],
                  letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=250,
                  responsive={'mobile': {'fontSize': m(11), 'y': 42, 'x': 7, 'width': 86}}),
        K.livello('di-t4', contenuto='Venti minuti', tag='h2', x=6, y=24.5, w=46, size=76, peso='600', colore=C['testo'],
                  fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=350, durata=1100,
                  charAnim={'stagger': 24, 'offsetY': 0, 'blur': 14, 'opacity': 0},
                  responsive={'mobile': {'fontSize': m(38), 'y': 44.5, 'x': 7, 'width': 88}}),
        K.livello('di-i4', contenuto='per vederci chiaro.', tag='p', x=6, y=35, w=46, size=76, peso='600', colore=C['acc'],
                  fontFamily=TITOLI, lineHeight=1.02, letterSpacing=-2, entra='fadeIn', ritardo=700, durata=1100,
                  charAnim={'stagger': 24, 'offsetY': 0, 'blur': 14, 'opacity': 0},
                  responsive={'mobile': {'fontSize': m(38), 'y': 49, 'x': 7, 'width': 88}}),
        K.livello('di-s4', contenuto='L’optometrista misura la vista da lontano e da vicino, la pressione dell’occhio e '
                  'come lavorano insieme i due occhi. Gratis se poi fate gli occhiali da noi.', x=6, y=50, w=33, size=17,
                  peso='400', colore=C['tenue'], lineHeight=1.65, ritardo=1000,
                  responsive={'mobile': {'fontSize': m(14), 'y': 55, 'x': 7, 'width': 86}}),
        K.livello('di-b4', tipo='button', contenuto='Prenota l’esame', x=6, y=71, w='auto', size=14, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, ritardo=1300, buttonUrl=PRENOTA,
                  hover={'bgColor': C['testo'], 'duration': 250}, responsive={'mobile': {'y': 69, 'x': 7, 'fontSize': m(13)}}),
    ]
    sl.append(K.diapositiva('di-4', {'type': 'gradient', 'gradientFrom': C['fondo'], 'gradientTo': mix(C['acc'], 8, C['sala']),
                                     'gradientAngle': 120, 'image': '', 'video': '', 'color': '', 'overlay': '',
                                     'overlayOpacity': 0, 'kenBurns': False}, tav, durata=9200, tab='L’esame'))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='blur', durata_trans=1000,
                       showArrows=False, showDots=False, showTabs=True, tabPosition='bottom', parallax=False,
                       autoplaySpeed=9200, progressBarColor=C['acc'], navColor=C['testo'],
                       globalBackground={'type': 'color', 'color': C['fondo']})


LENTI = [('Grigio', 'G-15 · neutra', 'Non cambia i colori: il mondo resta com’è, solo più calmo. La più scelta per guidare.',
          'pexels-38185097.jpg'),
         ('Marrone', 'B-10 · contrasto', 'Alza il contrasto e scalda i toni: per la montagna, il mare e le giornate velate.',
          'pexels-14849415.jpg'),
         ('Verde', 'G-12 · classica', 'Il verde delle lenti di una volta: riposa la vista, toglie i riflessi sull’acqua.',
          'pexels-12936741.jpg'),
         ('Rosa', 'R-05 · leggera', 'Una sfumatura appena, per la città e le ore di luce bassa. Fa vedere tutto più bello.',
          'pexels-1669595.jpg'),
         ('Specchiata', 'M-20 · mare', 'Specchio blu sulla lente marrone polarizzata: per la barca e la neve.',
          'pexels-8084237.jpg')]


def lenti(t):
    return t.classic('provamateriale', {
        'voci': [{'id': 'di-l%d' % k, 'nome': n, 'codice': c, 'testo': te, 'immagine': K.img(f), 'miniatura': '',
                  'colore': '', 'alt': 'Occhiali da sole con lenti %s' % n.lower()} for k, (n, c, te, f) in enumerate(LENTI)],
        'iniziale': 0, 'lente': True, 'lente_zoom': 2.4, 'lente_misura': 200, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passate sopra la foto: la lente ingrandisce la montatura',
        'testo_suggerimento_touch': 'Toccate la foto per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'Il colore della lente', 'accento': C['acc'], 'colore_testo': C['testo'],
        'colore_tenue': C['tenue'], 'sfondo_pannello': '', 'etichetta_color': C['tenue'], 'nome_font_family': 'heading',
        'nome_font_size': 48, 'nome_font_weight': '600', 'nome_color': C['testo'],
        'codice_font_family': 'var(--olo-font-family-mono)', 'codice_color': C['acc'], 'testo_color': C['tenue'],
        'raggio_immagine': radius(20), 'proporzione': '3/2', 'campione_forma': 'cerchio', 'campione_misura': 48,
        'disposizione': 'lato', 'lato': 'sinistra', 'larghezza_immagine': 58, 'gap': 56}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Diottria · Napoli', ['Un’ottica', 'col laboratorio dietro.'], size=60, layout='stack',
                                size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'In via Toledo dal 1987. L’esame della vista lo fa un’optometrista, con calma; le lenti le '
                                'tagliamo e le montiamo nel laboratorio dietro il bancone, quasi sempre in giornata. Le '
                                'montature le scegliamo da piccoli produttori del Cadore e del Giappone.', size=17,
                             color=C['testo']),
                      K.button(t, 'L’esame della vista', ESAME, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('20′', 'Per l’esame della vista'), ('1987', 'In via Toledo'), ('600', 'Montature in negozio'),
                            ('24', 'Mesi di garanzia ai bambini')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le montature della stagione', ['Otto montature,', 'da girare.'], size=60,
                         tagline='Trascinate o lasciate scorrere. Le trovate tutte in negozio, anche da provare a casa.')]),
        t.one([K.coverflow(t, [
            ('pexels-28488304.jpg', 'Occhiali neri su un piedistallo azzurro', 'Bruma 47', 'Acetato · da vista'),
            ('pexels-28834289.jpg', 'Due paia di occhiali su fondo giallo', 'Ottone 50', 'Metallo · da vista'),
            ('pexels-28578769.jpg', 'Occhiali verdi e trasparenti su fondo menta', 'Salvia 48', 'Acetato · da vista'),
            ('pexels-28881361.jpg', 'Occhiali tondi arancio e tartaruga', 'Tondo 42', 'Acetato · bambini'),
            ('pexels-1669595.jpg', 'Occhiali da sole tondi con lenti rosa', 'Rosa 49', 'Metallo · da sole'),
            ('pexels-38185097.jpg', 'Occhiali da sole dorati con lenti scure', 'Faro 52', 'Titanio · da sole'),
            ('pexels-27471495.jpg', 'Occhiali neri e libri su fondo arancio', 'Lettura 51', 'Acetato · da vista'),
            ('pexels-4039400.jpg', 'Occhiali su una superficie che riflette', 'Riva 50', 'Titanio · da vista')],
            altezza=400, larghezza=380)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_OCCHIO, 'L’esame', 'Venti minuti, <em>dieci misure.</em>',
                             'Vista da lontano e da vicino, astigmatismo, pressione dell’occhio, come lavorano insieme i due '
                             'occhi. Alla fine vi spieghiamo tutto, con le parole di tutti i giorni.', overlay=60))
    out.append(t.section([
        t.one([K.sechead(t, 'Le lenti da sole', ['Cinque colori,', 'cinque modi di vedere.'], size=60,
                         tagline='Scegliete un colore: la foto cambia e la lente ingrandisce la montatura. Tutte '
                                 'polarizzate, tutte anche graduate.')]),
        t.one([lenti(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='lenti', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Mi hanno trovato l’astigmatismo che nessuno aveva visto, e una montatura che finalmente non mi scivola sul '
                 'naso. Gli occhiali erano pronti il giorno dopo.', 'author_name': 'Valeria C.',
        'author_role': 'Occhiali progressivi, settembre 2026', 'avatar': '', 'rating': '5', 'text_color': C['testo'],
        'star_color': C['acc2'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 36, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc']}, style={})])], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_OCCHIALI, 'La prova', 'Provateli <em>a casa.</em>',
                          'Scegliete fino a quattro montature: ve le prestiamo per due giorni, senza lenti. Le riportate e '
                          'decidete con calma, davanti al vostro specchio.', 'Le montature', MONTATURE, overlay=66, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Diottria',
                page_intro_caption='Ottica · Napoli', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


MODELLI = [  # nome, foto, categoria, descrizione, misura
    ('Bruma 47', 'pexels-28488304.jpg', 'Da vista', 'Acetato blu notte, ponte a chiave · 160 €', '47'),
    ('Ottone 50', 'pexels-28834289.jpg', 'Da vista', 'Metallo dorato, aste sottili · 185 €', '50'),
    ('Salvia 48', 'pexels-28578769.jpg', 'Da vista', 'Acetato verde trasparente · 170 €', '48'),
    ('Lettura 51', 'pexels-27471495.jpg', 'Da vista', 'Acetato nero, per leggere e il computer · 120 €', '51'),
    ('Riva 50', 'pexels-4039400.jpg', 'Da vista', 'Titanio, sei grammi in tutto · 240 €', '50'),
    ('Rosa 49', 'pexels-1669595.jpg', 'Da sole', 'Metallo dorato, lenti rosa · 140 €', '49'),
    ('Faro 52', 'pexels-38185097.jpg', 'Da sole', 'Titanio, lenti grigie polarizzate · 210 €', '52'),
    ('Scoglio 54', 'pexels-12936741.jpg', 'Da sole', 'Acetato, lenti verdi classiche · 150 €', '54'),
    ('Tondo 42', 'pexels-28881361.jpg', 'Bambini', 'Acetato arancio, cerniere flessibili · 95 €', '42'),
    ('Lupo 44', 'pexels-8499536.jpg', 'Bambini', 'Plastica infrangibile, elastico sportivo · 110 €', '44'),
]


def build_montature():
    t = Tpl(ID, 'montature')
    out = [K.intro(t, 'Le montature', ['Seicento montature,', 'una alla volta.'],
                   'Cercate per nome, materiale o misura. Il numero è la larghezza della lente in millimetri: vi aiutiamo '
                   'noi a trovare la vostra.', bg=K.photo_bg('pexels-11377318.jpg', overlay=80, color=C['fondo']))]
    griglia = t.classic('portfolio', {
        'source': 'manual', 'layout': 'grid', 'columns': 3, 'gap': 24, 'image_ratio': '4:3', 'image_fit': 'cover',
        'hover_effect': 'zoom', 'animation': 'fade', 'caption_position': 'below', 'enable_search': True,
        'search_placeholder': 'Cerca: acetato, titanio, sole, 48…', 'show_title': True, 'show_category': True,
        'show_excerpt': True, 'font_family': 'heading', 'font_weight': '600', 'title_color': C['testo'],
        'text_color': C['tenue'], 'accent_color': C['acc'], 'border_radius': 20, 'year_stamp': True, 'index_numbering': False,
        'items': [{'title': ti, 'image_url': K.img(f), 'category': cat, 'description': d, 'year': mi,
                   'link_url': PRENOTA + '?montatura=' + ti.replace(' ', '-') + '#appuntamento'} for ti, f, cat, d, mi in MODELLI],
        'columns_tablet': '2', 'columns_mobile_landscape': '2'}, style={})
    out.append(t.section([t.one([griglia])], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'La misura', 'description': 'Larghezza del viso e del naso: tre numeri che '
                   'escludono metà delle montature.'},
                  {'number': '02', 'title': 'La prova', 'description': 'Allo specchio e in foto, di fronte e di profilo. '
                   'O a casa per due giorni.'},
                  {'number': '03', 'title': 'Le lenti', 'description': 'Sottili, antiriflesso, filtro per lo schermo se '
                   'serve. Tagliate e montate qui.'},
                  {'number': '04', 'title': 'La regolazione', 'description': 'Aste e naselli scaldati e piegati sul vostro '
                   'viso. Per sempre gratis.'}],
        'columns': 4, 'gap': 18, 'number_style': 'plain', 'number_color': C['acc'], 'number_size': 40, 'number_font': 'heading',
        'number_weight': '600', 'title_color': C['testo'], 'title_size': 22, 'title_weight': '600', 'title_font': 'heading',
        'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left', 'card_bg': C['chiaro'], 'card_border': '',
        'card_radius': radius(20), 'card_padding': 28, 'columns_tablet': 2, 'columns_mobile': 1}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Come si sceglie', ['Quattro passi,', 'un viso.'], size=56)]), t.one([passi])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_SOLE, 'Da sole', 'Anche <em>graduati.</em>',
                          'Ogni montatura da sole si fa con le vostre lenti: polarizzate, fotocromatiche o specchiate. '
                          'Pronte in tre giorni.', 'Prenota la prova', PRENOTA + '#appuntamento', overlay=55, video=True))
    out.append(K.domande(t, 'Le', 'montature.', [
        ('Posso portare una montatura mia?', 'Sì: la controlliamo e ci montiamo le lenti nuove, se è in buono stato.'),
        ('Quanto costano le lenti?', 'Da 60 € la coppia per le monofocali con antiriflesso; le progressive da 290 €.'),
        ('Fate le lenti a contatto?', 'Sì, con la prova e il controllo dopo un mese. Le giornaliere si ordinano anche online.'),
        ('E se mi si rompono?', 'Le montature hanno due anni di garanzia; per i bambini anche le lenti.'),
    ], 'mo', bg=False))
    return out


def build_esame():
    t = Tpl(ID, 'l-esame')
    out = [K.intro(t, 'L’esame della vista', ['Venti minuti,', 'con calma.'],
                   'Su appuntamento, con un’optometrista. Gratis se poi fate gli occhiali da noi, 35 € altrimenti.',
                   bg=K.photo_bg('pexels-6749735.jpg', overlay=80, color=C['fondo']))]
    passi = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': False, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in [
            ('01', 'Le domande', 'pexels-6749781.jpg', 'Prima di tutto', 'Come', 'usate gli occhi', 'Lavoro, schermo, guida, '
             'lettura: la lente giusta dipende da quello che fate.', '5′', 'di chiacchiere'),
            ('02', 'Le misure', 'pexels-6749729.jpg', 'Il forottero', 'Lente', 'dopo lente', 'Da lontano e da vicino, un '
             'occhio alla volta e poi insieme: «meglio così, o così?».', '10′', 'di misure'),
            ('03', 'Il risultato', 'pexels-3846022.jpg', 'La prova', 'Le lenti', 'in mano', 'Con la montatura di prova: '
             'camminate, leggete, guardate fuori. Se serve, si ritocca.', '5′', 'di prova')]],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': C['acc'],
        'counter_font_family': 'heading', 'counter_size': 80, 'counter_color': C['acc'], 'counter_italic': False,
        'counter_weight': '600', 'tag_color': C['testo'], 'pre_title_color': C['acc'], 'title_font_family': 'heading',
        'title_size': 32, 'title_weight': '600', 'title_color': C['testo'], 'title_accent_color': C['acc'],
        'description_color': C['tenue'], 'footer_icon': 'clock', 'footer_value_color': C['testo'],
        'footer_label_color': C['tenue'], 'columns': 3, 'gap': 48, 'media_radius': radius(20)}, style={})
    out.append(t.section([t.one([K.sechead(t, 'Come funziona', ['Tre momenti,', 'venti minuti.'], size=56)]), t.one([passi])],
                         padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    quiz = t.classic('scorequiz', {
        'eyebrow': 'Il test delle abitudini', 'heading': 'È ora di un controllo?',
        'intro': 'Quattro domande, trenta secondi. Non sostituiscono l’esame, ma vi dicono se è il momento.',
        'questions': [
            {'text': 'Quante ore al giorno passate davanti a uno schermo?', 'options': 'Meno di due|0\nDa due a sei|1\nPiù di sei|2'},
            {'text': 'Vi capita di allontanare il telefono per leggere?', 'options': 'Mai|0\nA volte|1\nSempre più spesso|2'},
            {'text': 'Di sera, guidando, le luci vi abbagliano?', 'options': 'No|0\nUn po’|1\nSì, parecchio|2'},
            {'text': 'Quando avete fatto l’ultimo controllo?', 'options': 'Quest’anno|0\nDue o tre anni fa|1\nNon me lo ricordo|2'}],
        'results': [
            {'min': 0, 'title': 'Occhi riposati', 'text': 'Per ora tutto bene: un controllo ogni due anni basta.',
             'link_text': 'Le montature', 'link_url': MONTATURE},
            {'min': 3, 'title': 'Un controllo non guasta', 'text': 'Qualche segnale c’è: venti minuti e lo sapete con '
             'certezza.', 'link_text': 'Prenota l’esame', 'link_url': PRENOTA + '#appuntamento'},
            {'min': 6, 'title': 'È il momento', 'text': 'I vostri occhi lavorano troppo. Venite a trovarci: l’esame è '
             'gratuito se fate gli occhiali da noi.', 'link_text': 'Prenota adesso', 'link_url': PRENOTA + '#appuntamento'}],
        'result_label': 'Il risultato', 'back_label': 'Indietro', 'restart_label': 'Ricomincia', 'show_score': False,
        'zone_accent': C['acc'], 'zone_on': C['on_acc'], 'card_bg': C['chiaro'],
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': C['filo']},
        'align': 'left'}, style={})
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-5996643.jpg', 'Una bambina dall’optometrista', ratio='4/5', tilt=True)], wm='2-5', ws='1-1'),
               t.col([quiz], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='test', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_FORO, 'Il forottero', '«Meglio così, <em>o così?»</em>',
                             'La domanda più famosa dell’ottica. Non ci sono risposte sbagliate: se non vedete differenza, '
                             'ditelo, è un’informazione anche quella.', overlay=55))
    out.append(K.domande(t, 'L’esame', 'della vista.', [
        ('Quanto costa?', 'Gratis se fate gli occhiali da noi, 35 € altrimenti. Vi diamo sempre la prescrizione scritta.'),
        ('È adatto ai bambini?', 'Dai tre anni, con i test a figure. Il primo controllo si fa prima della scuola.'),
        ('Sostituisce l’oculista?', 'No: misura la vista. Se vediamo qualcosa che riguarda la salute dell’occhio, vi '
                                    'mandiamo dall’oculista.'),
        ('Devo portare i vecchi occhiali?', 'Sì, ci aiutano a capire da dove partiamo.'),
    ], 'es', bg=False))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Un esame,', 'una prova, un saluto.'],
                   'L’esame della vista si prenota; per scegliere le montature passate quando volete.',
                   bg=K.photo_bg('pexels-5202060.jpg', overlay=80, color=C['fondo']), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa vi serve', 'cosa', True,
                     opzioni='L’esame della vista\nProvare delle montature\nLenti a contatto\nUn controllo per un bambino'),
             K.campo('date', 'Giorno', 'giorno', True), K.campo('select', 'Ora', 'ora', True,
                                                               opzioni='Mattina, 9:30 — 13\nPomeriggio, 15 — 19:30'),
             K.campo('text', 'Montatura', 'montatura', param='montatura', segnaposto='Se ne avete vista una'),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Occhiali che portate, fastidi, domande.')]
    out.append(K.sezione_modulo(t, 'appuntamento', 'L’appuntamento', ['Scegliete', 'il giorno.'],
                                'Vi confermiamo con un messaggio. L’esame dura venti minuti: arrivate cinque minuti prima.',
                                VOCI, campi, 'Diottria — appuntamento', 'Prenota', 'Ricevuto. Vi confermiamo entro oggi.'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi vi guarda', 'negli occhi.'], size=56)]),
        K.squadra(t, [
            ('pexels-6749781.jpg', 'Elena Gatti', 'Optometrista', 'Laurea in ottica e optometria a Napoli, fa gli esami '
             'della vista e segue i bambini.', '50% 25%'),
            ('pexels-7256348.jpg', 'Davide Russo', 'Laboratorio', 'Taglia e monta le lenti, regola le montature. Ripara '
             'anche quelle che altri buttano.', '50% 30%'),
            ('pexels-8971514.jpg', 'Chiara Valli', 'Montature e stile', 'Sceglie le collezioni dai piccoli produttori e vi '
             'aiuta a trovare la vostra.', '50% 30%')], sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In via Toledo,', 'a due passi dal metrò.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via Toledo 210, Napoli'), ('Metro', 'Toledo, linea 1, a cento metri'),
                                  ('Orari', 'lunedì — sabato 9:30 — 19:30'), ('Esami', 'su appuntamento')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Diottria · via Toledo 210', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori fuoco', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>non si vede.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Anche con gli occhiali giusti. Torniamo all’inizio.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla vetrina', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LEI, overlay=62), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Studi e professioni', 'La tavola che va a fuoco',
    ['ottica', 'occhiali', 'optometrista', 'napoli', 'chiaro', 'slider pro', '3d'],
    'Un’ottica con l’optometrista, bianco ottico e blu oltremare: in apertura le montature che girano in 3D come su un '
    'piatto da vetrina e la tavola dell’esame della vista che va a fuoco riga per riga; le montature in coverflow 3D, i '
    'colori delle lenti sotto la lente d’ingrandimento, il test delle abitudini e la prenotazione dell’esame.',
    [('Le montature', MONTATURE), ('L’esame', ESAME), ('Le lenti', HOME + '#lenti'), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('montature', 'Le montature', 'montature'), ('l-esame', 'L’esame della vista', 'l-esame'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Geist\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Geist\', system-ui, sans-serif',
    '\'Geist Mono\', ui-monospace, Menlo, monospace', ['Geist', 'Geist Mono'], peso_titoli='600', raggio=20, grana=0,
    titoli_line='1.02', titoli_spazio='-0.03em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'montature.json': build_montature(), 'l-esame.json': build_esame(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
