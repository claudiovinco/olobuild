# -*- coding: utf-8 -*-
"""Genera il tema «Foglia Lenta» (id foglia-lenta): una sala da tè a Trieste, nella città del caffè: quaranta tè da
Giappone, Taiwan, India e Cina, le degustazioni guidate, il matcha sbattuto al banco e i dolci di riso.

Tema chiaro, carta di riso, verde tè, ambra dell'oolong e oro. Titoli in Shippori Mincho, testo in Zen Kaku Gothic New,
dati in Overpass Mono.

Apertura: Slider Pro «la tazza», alta 16:9, un tè per diapositiva col suo video pieno (la raccolta, la teiera piccola,
la tazza che fuma, la frusta di bambù) e un velo scuro solo dal lato del testo. A destra la tazza vista dall'alto sul
piattino: il tè ha il colore di quell'infusione, sulla superficie si allargano i cerchi di una goccia e sopra sale il
vapore; sotto, temperatura e tempo. I tè si danno il cambio sfumando (crossFade). Cursore: la goccia.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'foglia-lenta'
NAME = 'Foglia Lenta'

HEX = {
    'primary': '#4F6B2E', 'primary_contrast': '#FFFFFF', 'secondary': '#9A4B26', 'secondary_contrast': '#FFFFFF',
    'accent': '#C9A227', 'dark': '#171A14', 'light': '#FFFFFF', 'muted': '#E8E4D8', 'muted_contrast': '#23271F',
    'text': '#23271F', 'text_muted': '#5E6457', 'background': '#F3F1EA', 'border': '#D9D3C4', 'link': '#4F6B2E',
}

HOME, CARTA, DEGUSTAZIONI, SALA = '{{PAGINA:home}}', '{{PAGINA:la-carta}}', '{{PAGINA:degustazioni}}', '{{PAGINA:la-sala}}'
MAIL, PHONE, PHONE_TEL = 'te@foglialenta.example', '+39 040 000 2210', 'tel:+390400002210'
LAT, LNG = '45.6495', '13.7768'

V_RACCOLTA, V_GONGFU, V_VAPORE, V_MATCHA, V_VERSA, V_FRUSTA = ('pexels-video-39566938', 'pexels-video-8507785',
                                                              'pexels-video-6938926', 'pexels-video-33050657',
                                                              'pexels-video-8507717', 'pexels-video-8507724')
PHOTOS = [
    'pexels-40304847.jpg', 'pexels-35380303.jpg', 'pexels-12731352.jpg', 'pexels-10874339.jpg', 'pexels-40268383.jpg',
    'pexels-8952037.jpg', 'pexels-12755354.jpg', 'pexels-34705969.jpg', 'pexels-4390022.jpg', 'pexels-12898337.jpg',
    'pexels-37252639.jpg', 'pexels-39648901.jpg', 'pexels-34386518.jpg', 'pexels-38987827.jpg', 'pexels-8951771.jpg',
    'pexels-33037261.jpg', 'pexels-1581484.jpg', 'pexels-39692079.jpg', 'pexels-10676682.jpg', 'pexels-24186411.jpg',
]
VIDEOS = [V_RACCOLTA, V_GONGFU, V_VAPORE, V_MATCHA, V_VERSA, V_FRUSTA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='500', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
ORO, OOLONG = C['acc2'], C['sec']
TITOLI, MONO = 'Shippori Mincho', 'Overpass Mono'
VOCI = [('map-pin', 'Via San Nicolò 9 · Trieste', SALA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Foglia Lenta', logo_size='22', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Foglia Lenta', 'Sala da tè a Trieste, nella città del caffè. Quaranta tè da Giappone, Taiwan, India e '
                   'Cina, le degustazioni guidate, il matcha sbattuto al banco e i dolci di riso.',
                   [('La carta', CARTA), ('Degustazioni', DEGUSTAZIONI), ('La sala', SALA), ('Privacy', '/privacy-policy/')],
                   'La sala', 'Da martedì a domenica 11 — 19<br>Il venerdì fino alle 22', VOCI,
                   '© 2026 Foglia Lenta · Trieste · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=120, fondo=C['sala'])


# ── L'apertura: la tazza ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
TE = [  # linguetta, occhiello, riga 1, riga 2, testo, video, colore del tè, temperatura e tempo
    ('Sencha', 'Sencha · Kyushu, Giappone', 'Il verde,', 'a ottanta gradi.',
     'Foglie stese e lucide, un minuto a ottanta gradi: sa d’erba appena tagliata e, in fondo, di mare.',
     V_RACCOLTA, mix(ORO, 55, C['acc']), '80° · 1 minuto'),
    ('Oolong', 'Oolong · Alishan, Taiwan', 'Sette infusioni,', 'sette tè diversi.',
     'Nella teiera piccola, trenta secondi alla volta: si versa, si ricomincia, e ogni volta il tè cambia.',
     V_GONGFU, mix(ORO, 60, OOLONG), '95° · 30 secondi'),
    ('Darjeeling', 'Darjeeling · primo raccolto, India', 'Il primo raccolto,', 'a duemila metri.',
     'Chiaro, floreale, quasi un moscato: tre minuti, non uno di più, e niente latte.',
     V_VAPORE, mix(OOLONG, 72, ORO), '90° · 3 minuti'),
    ('Matcha', 'Matcha · Uji, Giappone', 'Polvere verde,', 'frusta di bambù.',
     'Si sbatte al banco, davanti a voi, finché la schiuma è fitta come velluto. Si beve in tre sorsi.',
     V_MATCHA, mix(C['acc'], 78, C['chiaro']), '70° · si sbatte'),
]


def _box(cx, cy, r, tw, th):
    return round((cx - r) / tw * 100, 2), round((cy - r) / th * 100, 2), round(2 * r / tw * 100, 2), round(2 * r / th * 100, 2)


def tazza(n, colore, scheda):
    m = K.mob
    cx, cy, mx, my, sm = 760.0, 410.0, 320.0, 560.0, 0.92
    out = []
    sale = {'inizio': 0, 'fine': 100, 'da': {}, 'a': {'y': -140, 's': 1.1}}   # scorrendo la tazza sale verso di voi

    def cerchio(lid, r, **extra):
        x, y, w, h = _box(cx, cy, r, W, H)
        xm, ym, wm, hm = _box(mx, my, r * sm, WM, HM)
        resp = {'mobile': {'x': xm, 'y': ym, 'width': wm, 'height': hm}}
        return K.livello(lid, tipo='shape', x=x, y=y, w=w, h=h, borderRadius=999, responsive=resp, scrollMove=sale, **extra)

    out.append(cerchio('fl-piattino%d' % n, 212, bgColor=mix(C['chiaro'], 96), entra='zoomIn', ritardo=0, durata=900,
                       esce='fadeOut', boxShadow={'x': 0, 'y': 34, 'blur': 70, 'spread': -18, 'color': mix(C['nero'], 70)}))
    out.append(cerchio('fl-incavo%d' % n, 166, bgColor='transparent', borderWidth=2, borderColor=mix(C['nero'], 7),
                       entra='fadeIn', ritardo=150, durata=700, esce='fadeOut'))
    out.append(cerchio('fl-tazza%d' % n, 143, bgColor=C['chiaro'], entra='zoomIn', ritardo=200, durata=900, esce='fadeOut',
                       boxShadow={'x': 0, 'y': 16, 'blur': 34, 'spread': -10, 'color': mix(C['nero'], 45)}))
    out.append(cerchio('fl-te%d' % n, 124, bgColor=colore, entra='zoomIn', ritardo=450, durata=900, esce='fadeOut',
                       boxShadow={'x': 0, 'y': 6, 'blur': 18, 'spread': 0, 'color': mix(C['nero'], 30)}))
    # i cerchi della goccia sul tè
    for k in range(3):
        x, y, w, h = _box(cx, cy, 118, W, H)
        out.append(cerchio('fl-onda%d-%d' % (n, k), 118, bgColor='transparent', borderWidth=2, borderColor=mix(C['chiaro'], 55),
                           esce='fadeOut',
                           timeline=K.tl(x, y, [(0, {'scale': 0.12, 'opacity': 0}), (200, {'scale': 0.15, 'opacity': 90}),
                                                (3600, {'scale': 1, 'opacity': 0})], 3600, ritardo=900 + 1200 * k)))
    # temperatura e tempo
    x, y = round((cx - 130) / W * 100, 2), round((cy + 232) / H * 100, 2)
    out.append(K.livello('fl-scheda%d' % n, contenuto=scheda, x=x, y=y, w=round(260 / W * 100, 2), size=13, peso='600',
                         colore=C['nero'], bgColor=ORO, padding=10, borderRadius=999, align='center', fontFamily=MONO,
                         letterSpacing=1, entra='fadeInUp', ritardo=1300,
                         scrollMove=sale, responsive={'mobile': {'x': 25, 'y': round((my - 300) / HM * 100, 2), 'width': 50,
                                                'fontSize': m(11)}}))
    return out


def apertura(t):
    """Divisa a metà: a sinistra la carta di riso col testo scuro, a destra il video pieno, senza veli; la tazza vista
    dall'alto sta sulla linea fra le due metà, col tè del colore dell'infusione, i cerchi della goccia.
    Scorrendo la tazza sale verso di voi. Sul telefono la carta è in basso e la tazza sta sul bordo."""
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, video, colore, scheda) in enumerate(TE):
        n = i + 1
        livelli = [
            K.livello('fl-carta%d' % n, tipo='shape', x=0, y=0, w=50, h=100, bgColor=C['fondo'], entra='fadeIn', ritardo=0,
                      durata=500, esce='fadeOut', responsive={'mobile': {'x': 0, 'y': 46, 'width': 100, 'height': 54}}),
        ]
        livelli += tazza(n, colore, scheda)
        livelli += [
            K.livello('fl-o%d' % n, contenuto=occ, x=6, y=20, w=30, size=12, peso='600', colore=C['acc'], letterSpacing=3,
                      textTransform='uppercase', fontFamily=MONO, ritardo=200,
                      responsive={'mobile': {'fontSize': m(11), 'y': 63, 'x': 7, 'width': 86}}),
            K.livello('fl-t%d' % n, contenuto=r1 + '<br><span style="color:var(--olo-color-secondary)">' + r2 + '</span>',
                      tag='h1' if n == 1 else 'h2', x=6, y=25, w=31, size=50, peso='500', colore=C['testo'],
                      fontFamily=TITOLI, lineHeight=1.12, entra='maskFromBottom', ritardo=300, durata=1200,
                      responsive={'mobile': {'fontSize': m(31), 'y': 66.5, 'x': 7, 'width': 88}}),
            K.livello('fl-s%d' % n, contenuto=testo, x=6, y=46, w=27, size=16, peso='400', colore=C['tenue'],
                      lineHeight=1.7, ritardo=800, responsive={'mobile': {'fontSize': m(14), 'y': 76, 'x': 7, 'width': 86}}),
            K.livello('fl-b%d' % n, tipo='button', contenuto='La carta dei tè', x=6, y=73, w='auto', size=14, peso='600',
                      colore=C['chiaro'], bgColor=C['acc'], padding=16, borderRadius=6, ritardo=1100, buttonUrl=CARTA,
                      hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 85, 'x': 7, 'fontSize': m(12)}}),
            K.livello('fl-l%d' % n, tipo='button', contenuto='Le degustazioni', x=19.5, y=73, w='auto', size=14, peso='600',
                      colore=C['testo'], bgColor='transparent', padding=16, borderRadius=6, borderWidth=1,
                      borderColor=mix(C['testo'], 40), ritardo=1250, buttonUrl=DEGUSTAZIONI,
                      hover={'bgColor': mix(C['testo'], 8), 'borderColor': C['testo'], 'duration': 250},
                      responsive={'tablet': {'x': 23}, 'mobile': {'y': 85, 'x': 48, 'fontSize': m(12)}}),
        ]
        sl.append(K.diapositiva('fl-%d' % n, K.sfondo_video(video, overlay=0.05, colore=C['nero']), livelli, durata=8500,
                                tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='crossFade', durata_trans=1200,
                       showArrows=False, showDots=False, showTabs=True, tabPosition='bottom', parallax=False,
                       autoplaySpeed=8500, progressBarColor=C['acc'], navColor=C['chiaro'],
                       heightMobile={'mode': 'px', 'value': 1400})


CARTA_TE = [  # nome, provenienza, descrizione, prezzo, foto, colore
    ('Sencha', 'Kyushu, Giappone', 'Erba tagliata e mare. Un minuto a 80°.', '6 €', 'pexels-12898337.jpg', mix(ORO, 55, C['acc'])),
    ('Gyokuro', 'Yame, Giappone', 'Coltivato all’ombra: dolce, quasi brodo. A 60°.', '9 €', 'pexels-4390022.jpg',
     mix(C['acc'], 50, ORO)),
    ('Oolong Alishan', 'Taiwan', 'Fiori di montagna, sette infusioni nella teiera piccola.', '8 €', 'pexels-8952037.jpg',
     mix(ORO, 60, OOLONG)),
    ('Darjeeling primo raccolto', 'India', 'Chiaro e floreale, quasi un moscato.', '7 €', 'pexels-12731352.jpg',
     mix(OOLONG, 72, ORO)),
    ('Pu-erh di sette anni', 'Yunnan, Cina', 'Terra, sottobosco, cuoio. Si beve dopo pranzo.', '9 €', 'pexels-40268383.jpg',
     mix(OOLONG, 70, C['nero'])),
    ('Matcha cerimoniale', 'Uji, Giappone', 'Sbattuto al banco, con un dolce di riso.', '10 €', 'pexels-38987827.jpg',
     mix(C['acc'], 70, ORO)),
]


def carta(t):
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': p + ' · ' + pr, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f),
                   'link_url': CARTA, 'color': col} for k, (n, p, d, pr, f, col) in enumerate(CARTA_TE)],
        'lead_mode': 'swatch', 'swatch_size': 22, 'swatch_shape': 'circle', 'number_color': C['acc'],
        'number_hover_color': C['acc'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 46, 'name_weight': '500', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': OOLONG,
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(ORO, 10), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image',
        'peek_width': 260, 'peek_ratio': '4/3', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def dolci(t):
    voci = [('Daifuku alla fragola', 'Riso glutinoso, anko di fagioli rossi, una fragola intera', '5 €', 'pexels-10676682.jpg', ''),
            ('Yokan al tè verde', 'Gelatina di fagioli e matcha, tagliata a mano', '4 €', 'pexels-24186411.jpg', ''),
            ('Agrume al miele', 'Mezzo mandarino glassato, per il pu-erh', '4 €', 'pexels-39692079.jpg', 'Solo d’inverno'),
            ('Teiera per due', 'Un tè della carta e due dolci a scelta', '16 €', 'pexels-12755354.jpg', 'Il più scelto')]
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in voci],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 35), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'badge_bg': ORO, 'badge_color': C['nero'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': ORO}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Foglia Lenta · Trieste', ['Nella città del caffè,', 'una sala per il tè.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Dodici tavoli di legno chiaro, la luce che arriva dal canale, quaranta tè in barattoli di '
                                'latta. Qui il tè non si fa in un minuto: si sceglie, si aspetta, si guarda cambiare colore. '
                                'Chi ha fretta trova un caffè ottimo all’angolo.', size=17, color=C['testo']),
                      K.button(t, 'La sala', SALA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('40', 'Tè in carta'), ('5', 'Paesi'), ('12', 'Tavoli'), ('3', 'Minuti, in media')])],
              style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La carta', ['Sei tè da cui', 'cominciare.'], size=56,
                         tagline='Passate su un tè: la foto si affaccia. Il tondo ha il colore della tazza.')]),
        t.one([carta(t)]),
        t.one([K.button(t, 'Tutta la carta', CARTA, pad_top=26)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_VERSA, 'Il gesto', 'Si versa <em>quando è pronto.</em>',
                             'Non prima, non dopo: per questo il timer sta sul tavolo, accanto alla teiera.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8951771.jpg', 'Agnese prepara il matcha al banco', ratio='4/5', tilt=True)],
                     wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Le degustazioni', ['Cinque tè,', 'un’ora e mezza.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Il sabato pomeriggio, otto persone al tavolo grande: cinque tè dal più chiaro al più scuro, '
                                'come si scaldano, quanto si aspetta, che cosa si sente. Si esce sapendo scegliere.', size=17),
                      K.specs(t, [('Quando', 'il sabato alle 16'), ('Quanti', 'otto persone al massimo'), ('Prezzo', '35 € con '
                                  'i dolci')]),
                      K.button(t, 'Prenota la degustazione', DEGUSTAZIONI, kind='pieno', pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Sono triestino, bevo quattro caffè al giorno. Il sabato vengo qui e per un’ora e mezza non guardo il telefono. Il '
           'gyokuro sa di brodo, mi avevano avvertito, ed è vero.', 'Paolo R.', 'Cliente del sabato')])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_FRUSTA, 'Il matcha', 'Sbattuto al banco, <em>davanti a voi.</em>',
                          'Con un dolce di riso, il pomeriggio, da martedì a domenica.', 'La carta', CARTA,
                          overlay=40, video=True))
    return out


def build_carta():
    t = Tpl(ID, 'la-carta')
    out = [K.intro(t, 'La carta', ['Quaranta tè,', 'cinque paesi.'],
                   'Ogni tè ha la sua temperatura e il suo tempo: ve li scriviamo sul cartellino, il timer è sul tavolo.',
                   bg=K.photo_bg('pexels-40268383.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Dal più chiaro', ['al più scuro.'], size=56,
                         tagline='Il prezzo è per una teiera, che si può rabboccare due volte.')]),
        t.one([carta(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'I dolci', ['Di riso,', 'di fagioli, di frutta.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'Li fa Agnese ogni mattina, pochi: quando finiscono, finiscono.', size=16)], wm='2-5', ws='1-1'),
               t.col([dolci(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le foglie', ['Da vicino.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-34705969.jpg', 'Foglie e fiori secchi su un tagliere', 'Le miscele', 'fatte qui'),
                               ('pexels-4390022.jpg', 'Una teiera bianca e la tazza di tè chiaro', 'Il bianco', 'Fujian'),
                               ('pexels-38987827.jpg', 'Il matcha in polvere nella ciotola', 'Il matcha', 'Uji'),
                               ('pexels-12898337.jpg', 'Foglie di tè in infusione nel piatto', 'Le foglie aperte', 'dopo tre minuti'),
                               ('pexels-1581484.jpg', 'Una tazza di matcha su una pietra', 'Il matcha', 'freddo d’estate')])]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    return out


def build_degustazioni():
    t = Tpl(ID, 'degustazioni')
    out = [K.intro(t, 'Degustazioni', ['Imparare il tè,', 'una tazza alla volta.'],
                   'Il sabato pomeriggio al tavolo grande, oppure per voi soli, in sala o a casa vostra.',
                   bg=K.video_bg(V_GONGFU), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le formule', ['Al tavolo grande,', 'per due, a casa vostra.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Il sabato', '35', 'a persona', 'Cinque tè, dal più chiaro al più scuro\nDue dolci di riso\nIl quaderno con le '
             'temperature', False, ''),
            ('Per due', '90', 'un pomeriggio a scelta', 'Sette tè scelti per voi\nLa teiera piccola, alla cinese\nUn barattolo da '
             'portare a casa', True, 'il più regalato'),
            ('A casa vostra', '280', 'fino a dieci persone', 'Agnese porta tè, teiere e tazze\nUn’ora e mezza\nI dolci per '
             'tutti', False, ''),
        ], 'Prenota', DEGUSTAZIONI + '#prenota', nota='Le degustazioni si possono regalare: il buono vale sei mesi.',
            sfondo=C['sala']),
    ], padding='large', width='large', html_id='prezzi',
        style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'La degustazione', 'degustazione', True, opzioni='Il sabato · 35 €\nPer due · 90 €\nA casa vostra '
                                                                                 '· 280 €\nUn buono regalo'),
             K.campo('date', 'La data', 'data'), K.campo('select', 'Quanti siete', 'persone', opzioni='1\n2\n3\n4\n5\n6\n7\n8'),
             K.campo('textarea', 'Una nota', 'note', larghezza='1-1', segnaposto='Un tè che volete assaggiare, un’allergia.')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['Scegliete il giorno,', 'scaldiamo l’acqua.'],
                                'Vi rispondiamo entro un giorno con la conferma.', VOCI, campi, 'Foglia Lenta — degustazione',
                                'Prenota', 'Ricevuto. Vi rispondiamo entro un giorno.'))
    out.append(K.domande(t, 'Prima', 'di venire.', [
        ('Devo sapere qualcosa di tè?', 'No: la degustazione del sabato comincia dall’acqua, da come si scalda.'),
        ('C’è anche il tè freddo?', 'D’estate sì: matcha e sencha in infusione fredda, dodici ore.'),
        ('Si possono comprare i tè?', 'Tutti quelli della carta, in barattoli da 50 o 100 grammi.'),
    ], 'fl-faq'))
    return out


def build_sala():
    t = Tpl(ID, 'la-sala')
    out = [K.intro(t, 'La sala', ['Dodici tavoli,', 'la luce del canale.'],
                   'Agnese l’ha aperta nel 2019 dopo tre anni fra Kyoto e Taipei. Il legno dei tavoli è quello di una '
                   'vecchia barca del porto.', bg=K.photo_bg('pexels-34386518.jpg'), size=92)]
    out.append(t.section([
        t.row([t.col([K.image(t, f, alt, ratio='4/5', lightbox=True)], wm='1-4', ws='1-2') for f, alt in (
            ('pexels-37252639.jpg', 'Un tavolino basso con la teiera'), ('pexels-39648901.jpg', 'La sala con le finestre di carta'),
            ('pexels-40304847.jpg', 'Tre tazze rosse sul banco di legno'), ('pexels-35380303.jpg', 'Una tazza dorata su una mensola di pietra'))],
              layout='25-25-25-25', gap=18),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_VAPORE, 'L’attesa', 'Tre minuti <em>sono tanti.</em>',
                             'Abbastanza per guardare fuori, per parlare, per accorgersi che il tè è cambiato.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Sul canale,', 'a due passi dal mare.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Via San Nicolò 9, Trieste'), ('Orari', 'martedì — domenica 11 — 19'),
                                  ('Il venerdì', 'fino alle 22')]),
                      K.contacts(t, VOCI)], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Foglia Lenta · sala da tè', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · in infusione', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>non è ancora pronta.</em>', size=80, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Tre minuti, forse quattro. Intanto, la home.', size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_VAPORE, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ristorazione', 'La tazza a metà',
    ['tè', 'sala da tè', 'matcha', 'degustazioni', 'dolci', 'trieste', 'chiaro', 'slider pro'],
    'Una sala da tè nella città del caffè, carta di riso e verde tè: in apertura metà carta e metà video, con la '
    'tazza vista dall’alto sulla linea fra le due, il tè del colore di ogni infusione e i cerchi di una goccia; la '
    'carta dal più chiaro al più scuro, i dolci di riso e le degustazioni.',
    [('La carta', CARTA), ('Degustazioni', DEGUSTAZIONI), ('La sala', SALA)],
    [('home', 'Home', None), ('la-carta', 'La carta', 'la-carta'), ('degustazioni', 'Degustazioni', 'degustazioni'),
     ('la-sala', 'La sala', 'la-sala')],
    HEX, '\'Zen Kaku Gothic New\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Shippori Mincho\', \'Georgia\', serif', '\'Overpass Mono\', ui-monospace, Menlo, monospace',
    ['Shippori Mincho', 'Zen Kaku Gothic New', 'Overpass Mono'], corsivo_di=[], peso_titoli='500', raggio=6, grana=3,
    titoli_line='1.12', titoli_spazio='0')
THEME['cursor']['style'] = 'inchiostro'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-carta.json': build_carta(), 'degustazioni.json': build_degustazioni(), 'la-sala.json': build_sala(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
