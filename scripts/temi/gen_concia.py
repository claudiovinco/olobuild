# -*- coding: utf-8 -*-
"""Genera il tema «Concia» (id concia): una pelletteria artigiana in Oltrarno, a Firenze: borse, cartelle, cinture e
portafogli in cuoio conciato al vegetale, tagliati e cuciti a mano da padre e figlio; la collezione e il su misura.

Tema scuro, cuoio e ottone: nero bottega, cuoio, salvia e miele. Titoli in Gilda Display, testo in Rethink Sans, dati in
Courier Prime.

Apertura: «la vetrina che scorre». Lo slider resta fermo in cima mentre si scorre e la vetrina scorre di lato: prima il
video della bottega, poi i pezzi della collezione uno dopo l'altro, col nome e il prezzo; il titolo e i pulsanti restano
fermi in alto. Cursore: il filo con l'ago.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402

ID = 'concia'
NAME = 'Concia'

HEX = {
    'primary': '#C8763A', 'primary_contrast': '#120D0A', 'secondary': '#7E9C86', 'secondary_contrast': '#0E1210',
    'accent': '#E7B46A', 'dark': '#0B0806', 'light': '#FFF8EF', 'muted': '#201914', 'muted_contrast': '#EFE6DA',
    'text': '#EFE6DA', 'text_muted': '#AD9F8D', 'background': '#15110E', 'border': '#3A2E25', 'link': '#C8763A',
}

HOME, COLLEZIONE, SU_MISURA, BOTTEGA, CONTATTI = ('{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:su-misura}}',
                                                  '{{PAGINA:la-bottega}}', '{{PAGINA:contatti}}')
MAIL, PHONE, PHONE_TEL = 'bottega@concia.example', '+39 055 000 1848', 'tel:+390550001848'
LAT, LNG = '43.7651', '11.2486'

V_BOTTEGA, V_CUCE, V_TAGLIO, V_PELLE, V_BANCO, V_BORSA = ('pexels-video-34740029', 'pexels-video-6654775',
                                                         'pexels-video-4455881', 'pexels-video-4456116',
                                                         'pexels-video-34740037', 'pexels-video-34740247')
PHOTOS = [
    'pexels-6650001.jpg', 'pexels-13525167.jpg', 'pexels-10919291.jpg', 'pexels-17315572.jpg', 'pexels-6653235.jpg',
    'pexels-32599546.jpg', 'pexels-3801349.jpg', 'pexels-33428417.jpg', 'pexels-33428433.jpg', 'pexels-4452616.jpg',
    'pexels-4452629.jpg', 'pexels-4452613.jpg', 'pexels-4452644.jpg', 'pexels-4452633.jpg', 'pexels-34443363.jpg',
    'pexels-35322147.jpg', 'pexels-6651193.jpg',
]
VIDEOS = [V_BOTTEGA, V_CUCE, V_TAGLIO, V_PELLE, V_BANCO, V_BORSA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=False)
C = K.c
MIELE, SALVIA = C['acc2'], C['sec']
TITOLI, MONO = 'Gilda Display', 'Courier Prime'
VOCI = [('map-pin', 'Via de’ Serragli 41 · Firenze', CONTATTI + '#bottega'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Concia', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Concia', 'Pelletteria in Oltrarno dal 1978: borse, cartelle, cinture e portafogli in cuoio conciato al '
                   'vegetale, tagliati e cuciti a mano da Renzo e Tommaso Ghelardini.',
                   [('La collezione', COLLEZIONE), ('Su misura', SU_MISURA), ('La bottega', BOTTEGA), ('Contatti', CONTATTI),
                    ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Da martedì a sabato 9:30 — 19<br>Il lunedì solo su appuntamento', VOCI,
                   '© 2026 Concia di Ghelardini · Firenze · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com')], marchio_size=140)


# ── L'apertura: la vetrina che scorre ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
PEZZI = [  # foto, nome, prezzo, punto focale
    ('pexels-10919291.jpg', 'Borsa Oltrarno', '360 €', 'center center'),
    ('pexels-6650001.jpg', 'Le cartelle', 'da 420 €', '20% center'),
    ('pexels-34443363.jpg', 'Cinture', '85 €', 'center center'),
    ('pexels-6651193.jpg', 'Portafoglio a libro', '95 €', 'center center'),
    ('pexels-13525167.jpg', 'Tote morbide', '290 €', 'center center'),
]
SCORRE = 1440        # di quanto scorre la vetrina (px della tela del computer)


def _pc(x, y, w, h, tw, th):
    return round(x / tw * 100, 2), round(y / th * 100, 2), round(w / tw * 100, 2), round(h / th * 100, 2)


def apertura(t):
    m = K.mob
    va = {'inizio': 0, 'fine': 88, 'da': {}, 'a': {'x': -SCORRE}}
    livelli = [
        K.livello('co-o', contenuto='Concia · pelletteria in Oltrarno, dal 1978', x=6, y=12, w=46, size=12, peso='400',
                  colore=MIELE, letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 9, 'x': 6, 'width': 88}, 'tablet': {'y': 15.5}}),
        K.livello('co-t', contenuto='Cucito a mano,<br><em>punto dopo punto.</em>', tag='h1', x=6, y=15.5, w=50, size=62,
                  peso='400', colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, entra='maskFromBottom', ritardo=300,
                  durata=1200, responsive={'mobile': {'fontSize': m(32), 'y': 11, 'x': 6, 'width': 88}, 'tablet': {'y': 19}}),
        K.livello('co-s', contenuto='Cuoio conciato al vegetale, tagliato e cucito a mano in bottega. Scorrete: la vetrina '
                  'vi passa davanti.', x=60, y=15, w=32, size=15, peso='400', colore=mix(C['chiaro'], 84), lineHeight=1.6,
                  ritardo=700, responsive={'mobile': {'fontSize': m(13), 'y': 62, 'x': 6, 'width': 88}, 'tablet': {'y': 17}}),
        K.livello('co-b', tipo='button', contenuto='La collezione', x=60, y=26, w='auto', size=12, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=15, letterSpacing=2, textTransform='uppercase', borderRadius=4,
                  ritardo=1000, buttonUrl=COLLEZIONE, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'y': 70, 'x': 6, 'fontSize': m(11)}, 'tablet': {'y': 28.5}}),
        K.livello('co-l', tipo='button', contenuto='Su misura', x=73.5, y=26, w='auto', size=12, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=15, letterSpacing=2, textTransform='uppercase',
                  borderRadius=4, borderWidth=1, borderColor=mix(C['chiaro'], 50), ritardo=1150, buttonUrl=SU_MISURA,
                  hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 77, 'y': 28.5}, 'mobile': {'y': 70, 'x': 50, 'fontSize': m(11)}}),
    ]
    # la vetrina: il video della bottega e i pezzi, in fila; tutti scorrono insieme
    top, alto = 290.0, 340.0                 # computer
    topm, altom = 500.0, 300.0               # telefono
    a = _pc(80, top, 560, alto, W, H)
    b = _pc(24, topm, 300, altom, WM, HM)
    livelli.append(K.livello('co-video', tipo='video', videoSrc=K.img(V_BOTTEGA + '.mp4'), videoAutoplay=True,
                             videoMuted=True, videoLoop=True, x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=4, entra='fadeIn',
                             ritardo=200, durata=900, scrollMove=va,
                             responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
    a = _pc(80, top + alto + 14, 560, 24, W, H)
    b = _pc(24, topm + altom + 10, 300, 24, WM, HM)
    livelli.append(K.livello('co-video-t', contenuto='La bottega · Renzo al banco da quarantotto anni', x=a[0], y=a[1], w=a[2],
                             size=13, peso='400', colore=mix(C['chiaro'], 76), fontFamily=MONO, letterSpacing=1, ritardo=600,
                             scrollMove=va, responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(9)}}))
    for k, (foto, nome, prezzo, pos) in enumerate(PEZZI):
        x, xm = 680 + 430 * k, 340 + 186 * k
        a = _pc(x, top, 400, alto, W, H)
        b = _pc(xm, topm, 172, altom, WM, HM)
        livelli.append(K.livello('co-pezzo%d' % k, tipo='image', imageSrc=K.img(foto), objectFit='cover', objectPosition=pos,
                                 x=a[0], y=a[1], w=a[2], h=a[3], borderRadius=4, entra='fadeIn', ritardo=300 + 120 * k,
                                 durata=900, scrollMove=va,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'height': b[3]}}))
        a = _pc(x, top + alto + 14, 400, 24, W, H)
        b = _pc(xm, topm + altom + 10, 172, 24, WM, HM)
        livelli.append(K.livello('co-nome%d' % k, contenuto='%s <span style="color:var(--olo-color-primary)">· %s</span>' % (nome, prezzo),
                                 x=a[0], y=a[1], w=a[2], size=16, peso='400', colore=C['chiaro'], fontFamily=TITOLI,
                                 ritardo=600, scrollMove=va,
                                 responsive={'mobile': {'x': b[0], 'y': b[1], 'width': b[2], 'fontSize': m(10)}}))
    # il segno di quanto manca: una barra sottile che si riempie
    livelli += [
        K.livello('co-barra', tipo='shape', x=6, y=84, w=88, h=0.25, bgColor=mix(C['chiaro'], 18), entra='fadeIn', ritardo=800,
                  responsive={'mobile': {'y': 84, 'height': 0.15}}),
        K.livello('co-punto', tipo='shape', x=5.5, y=83.6, w=1, h=1.1, borderRadius=999, bgColor=C['acc'], entra='fadeIn',
                  ritardo=800, scrollMove={'inizio': 0, 'fine': 88, 'da': {}, 'a': {'x': 1267}},
                  responsive={'mobile': {'y': 83.7, 'width': 2.2, 'height': 0.55}}),
    ]
    sl = [K.diapositiva('co-1', {'type': 'color', 'image': '', 'video': '', 'color': C['fondo'], 'overlay': C['nero'],
                                 'overlayOpacity': 0}, livelli, durata=60000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=2000, scrollStep=False,
                       globalBackground={'type': 'color', 'color': C['fondo']}, heightMobile={'mode': 'px', 'value': 1400})


COLLEZIONE_LISTA = [  # nome, descrizione, prezzo, foto, badge
    ('Borsa Oltrarno', 'Vacchetta conciata al vegetale, cucita a mano a punto sellaio', '360 €', 'pexels-10919291.jpg', 'La più amata'),
    ('Cartella da lavoro', 'Due scomparti, la chiusura in ottone, il portacomputer imbottito', '420 €', 'pexels-6650001.jpg', ''),
    ('Tote morbida', 'Cuoio nappato, una tasca dentro, tre colori', '290 €', 'pexels-13525167.jpg', ''),
    ('Cintura', 'Altezza 3,5 cm, fibbia in ottone pieno, il bordo tinto a mano', '85 €', 'pexels-35322147.jpg', ''),
    ('Portafoglio a libro', 'Otto carte, la tasca per le monete, cucito a mano', '95 €', 'pexels-6651193.jpg', 'Si incide il nome'),
]


def collezione(t):
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f), 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in COLLEZIONE_LISTA],
        'separator_style': 'dotted', 'separator_color': mix(C['acc'], 40), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 84, 'image_border_radius': '4', 'show_image': True,
        'price_position': 'right', 'badge_bg': MIELE, 'badge_color': C['nero'], 'badge_border_radius': '2',
        'tile_padding': {'top': 16, 'right': 0, 'bottom': 16, 'left': 0}, 'card_bg': 'transparent'}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 36, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc']}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Concia · via de’ Serragli', ['Padre e figlio,', 'un banco, nessuna macchina.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Renzo ha aperto la bottega nel 1978, Tommaso è al banco con lui da dieci anni. Il cuoio arriva '
                                'dalle concerie al vegetale di Santa Croce sull’Arno; si taglia col trincetto, si cuce a mano '
                                'col punto sellaio, il bordo si tinge e si lucida a mano.', size=17, color=C['testo']),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('1978', 'La bottega'), ('2', 'Persone al banco'), ('6 ore', 'Per una borsa'),
                            ('a vita', 'Le riparazioni')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La collezione', ['Pochi pezzi,', 'fatti bene.'], size=52, layout='stack', size_mobile=34),
                      K.text(t, 'La collezione cambia poco: quando un pezzo funziona, lo facciamo per anni. I colori sono '
                                'quattro: naturale, cuoio, testa di moro, nero.', size=16),
                      K.button(t, 'Tutta la collezione', COLLEZIONE, kind='pieno', pad_top=14)], wm='2-5', ws='1-1'),
               t.col([collezione(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_CUCE, 'Il punto sellaio', 'Due aghi, <em>un filo cerato.</em>',
                             'Ogni punto passa due volte nello stesso foro: se un filo si rompe, l’altro tiene. Per questo una '
                             'borsa cucita a mano dura trent’anni.', overlay=40))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Ho portato la cartella di mio nonno, comprata qui nel 1981. Renzo l’ha riconosciuta, ha cambiato la tracolla e '
           'non ha voluto essere pagato. Ne ho ordinata una per mio figlio.', 'Alberto F.', 'Cliente da due generazioni')])],
        padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_BANCO, 'Su misura', 'La vostra borsa, <em>disegnata con voi.</em>',
                          'Si comincia da un foglio di carta e da quello che ci volete mettere dentro.', 'Su misura', SU_MISURA,
                          overlay=40, video=True))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Cinque pezzi,', 'quattro colori.'],
                   'Ogni pezzo si può avere nei quattro colori della bottega e con le iniziali incise a caldo.',
                   bg=K.photo_bg('pexels-10919291.jpg'), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Prezzi veri,', 'nessun saldo.'], size=56)]),
        t.one([collezione(t)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Da vicino', ['I dettagli', 'che contano.'], size=56, tagline='Trascinate o lasciate scorrere.')]),
        t.one([K.coverflow(t, [('pexels-34443363.jpg', 'Cinture appese in fila', 'Le cinture', 'tinte a mano'),
                               ('pexels-6651193.jpg', 'Portafogli e la carta da regalo', 'I portafogli', 'otto carte'),
                               ('pexels-33428433.jpg', 'Mani che rifiniscono un portafoglio', 'Il bordo', 'lucidato a mano'),
                               ('pexels-32599546.jpg', 'Gli attrezzi nella custodia di pelle', 'Gli attrezzi', 'di Renzo'),
                               ('pexels-17315572.jpg', 'Mani su una pelle intera', 'La pelle', 'scelta una per una')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Sul', 'cuoio.', [
        ('Perché conciato al vegetale?', 'Perché si conciava così da secoli: coi tannini delle cortecce, senza cromo. Il '
         'cuoio respira, invecchia bene e prende il colore di chi lo usa.'),
        ('Si macchia con l’acqua?', 'Un po’ sì, all’inizio. Poi si scurisce e la macchia sparisce: è la patina.'),
        ('Lo riparate?', 'Sempre, per tutta la vita del pezzo: cuciture, tracolle, fibbie.'),
    ], 'co-faq'))
    return out


def build_su_misura():
    t = Tpl(ID, 'su-misura')
    out = [K.intro(t, 'Su misura', ['Disegnata con voi,', 'cucita per voi.'],
                   'Una borsa per il vostro computer, una cintura col vostro numero di buchi, una custodia per un oggetto '
                   'che non ha custodia.', bg=K.video_bg(V_TAGLIO), size=92)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Il disegno', 'description': 'In bottega o per email: misure, tasche, colore, '
                   'chiusure. Facciamo il disegno su carta.'},
                  {'number': '02', 'title': 'Il cartamodello', 'description': 'Lo tagliamo in cartone e ve lo facciamo provare: '
                   'si sposta una tasca, si allunga una tracolla.'},
                  {'number': '03', 'title': 'Taglio e cucitura', 'description': 'La pelle scelta con voi, tagliata col '
                   'trincetto e cucita a mano.'},
                  {'number': '04', 'title': 'Il ritiro', 'description': 'In bottega, con le iniziali incise. O a casa vostra, '
                   'ovunque siate.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': C['testo'], 'title_size': 26,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})
    out.append(t.section([
        t.one([K.sechead(t, 'Come si fa', ['Quattro passi,', 'cinque settimane.'], size=56)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Da dove', 'si parte.'], size=52, size_mobile=34)]),
        K.piani(t, [
            ('Piccola pelletteria', '140', 'da', 'Portafogli, custodie, astucci\nTre settimane\nLe iniziali comprese', False, ''),
            ('Borsa su misura', '480', 'da', 'Il disegno e il cartamodello\nCinque settimane\nRiparazioni a vita', True,
             'la più chiesta'),
            ('Cartella da lavoro', '620', 'da', 'Scomparti per il vostro lavoro\nSei settimane\nLa chiusura in ottone pieno',
             False, ''),
        ], 'Chiedi il disegno', SU_MISURA + '#disegno', nota='Il prezzo esatto arriva col disegno; l’acconto è del 30%.'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='prezzi', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa vi serve', 'pezzo', True, opzioni='Una borsa\nUna cartella\nUna cintura\nUn portafoglio '
                                                                             'o una custodia\nAltro'),
             K.campo('select', 'Il colore', 'colore', opzioni='Naturale\nCuoio\nTesta di moro\nNero\nNon so ancora'),
             K.campo('textarea', 'Che cosa ci metterete dentro, le misure', 'note', larghezza='1-1',
                     segnaposto='Un computer da 14 pollici, due quaderni, una borraccia…')]
    out.append(K.sezione_modulo(t, 'disegno', 'Il disegno', ['Raccontateci,', 'disegniamo noi.'],
                                'Vi rispondiamo con le domande giuste, poi un primo disegno.', VOCI, campi, 'Concia — su misura',
                                'Chiedi il disegno', 'Ricevuto. Vi rispondiamo entro due giorni.'))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Via de’ Serragli,', 'dal 1978.'],
                   'Una stanza sola: il banco, la parete degli attrezzi, le pelli appese. Si lavora davanti a chi entra.',
                   bg=K.video_bg(V_BOTTEGA), size=92)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-4452629.jpg', 'Tommaso al banco col trincetto', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Renzo e Tommaso', ['Il padre insegna,', 'il figlio cuce.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Renzo ha imparato da un sellaio di San Frediano, a quattordici anni. Tommaso ha studiato '
                                'architettura, poi è tornato al banco: disegna lui i pezzi nuovi, il padre li fa a prova di '
                                'trent’anni.', size=17),
                      K.specs(t, [('Renzo', 'al banco dal 1978'), ('Tommaso', 'al banco dal 2016'),
                                  ('Il cuoio', 'Santa Croce sull’Arno'), ('Il filo', 'lino cerato')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il banco', ['Gli attrezzi,', 'le mani.'], size=56)]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5', lightbox=True)], wm='1-4', ws='1-2') for f, alt in (
            ('pexels-4452616.jpg', 'Il punteruolo sulla pelle'), ('pexels-6653235.jpg', 'Il grembiule di cuoio e la lesina'),
            ('pexels-33428417.jpg', 'Il martello sul portafoglio'), ('pexels-3801349.jpg', 'Gli attrezzi appesi alla parete'))],
              layout='25-25-25-25', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_PELLE, 'Il taglio', 'Una pelle, <em>quattro borse.</em>',
                             'Si guarda la pelle intera prima di tagliare: le parti più belle vanno sul davanti, le altre '
                             'dentro.', overlay=40))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Passate in bottega,', 'si lavora a porta aperta.'],
                   'Per un pezzo della collezione, un su misura o una riparazione.', bg=K.photo_bg('pexels-4452633.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'cosa', True, opzioni='Un pezzo della collezione\nUn su misura\nUna '
                                                                                 'riparazione\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Una riga,', 'rispondiamo la sera.'],
                                'Di giorno siamo al banco: rispondiamo entro un giorno.', VOCI, campi, 'Concia — contatto',
                                'Invia', 'Ricevuto. Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['In Oltrarno,', 'via de’ Serragli 41.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 9:30 — 19'), ('Il lunedì', 'su appuntamento'),
                                  ('Dal Ponte Vecchio', 'dieci minuti a piedi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Concia · la bottega', scuro=True, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='bottega', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · punto saltato', color=C['acc'], align='center'),
                       K.headline(t, 'Qui manca <em>un punto.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc']),
                       K.text(t, 'Lo ricuciamo subito. Intanto, la vetrina.', size=17, align='center', color=mix(C['chiaro'], 84)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CUCE, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La vetrina che scorre',
    ['pelletteria', 'borse', 'cuoio', 'artigiano', 'su misura', 'firenze', 'scuro', 'slider pro'],
    'Una pelletteria artigiana in Oltrarno, cuoio e ottone: in apertura la vetrina che scorre di lato mentre si scende, col '
    'video della bottega e i pezzi della collezione col nome e il prezzo; la collezione, il su misura in quattro passi, '
    'padre e figlio al banco.',
    [('La collezione', COLLEZIONE), ('Su misura', SU_MISURA), ('La bottega', BOTTEGA), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('su-misura', 'Su misura', 'su-misura'),
     ('la-bottega', 'La bottega', 'la-bottega'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Rethink Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Gilda Display\', \'Georgia\', serif',
    '\'Courier Prime\', ui-monospace, Menlo, monospace', ['Gilda Display', 'Rethink Sans', 'Courier Prime'], corsivo_di=[],
    peso_titoli='400', raggio=4, grana=5, titoli_line='1.04', titoli_spazio='0')
THEME['cursor']['style'] = 'filo'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'su-misura.json': build_su_misura(),
                        'la-bottega.json': build_bottega(), 'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
