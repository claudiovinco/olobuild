# -*- coding: utf-8 -*-
"""Genera il tema «Dorso» (id dorso): una legatoria artigiana in Oltrarno, a Firenze, dal 1962. Rilegature a mano,
restauro di libri, taccuini su misura, corsi.

La trovata (18 nov 2026, sera; utente: «hai fatto un ottimo lavoro con i siti pazzi, cerca di farne altri 5»): il sito si
sfoglia. Nell'apertura la copertina di cuoio con la cornice d'oro si apre (rotazione 3D a scorrimento dello Slider Pro) e
scopre le pagine che si sfogliano; poi le sezioni della home sono le pagine di un libro (Sezioni → Effetto sticky →
«Pagina»): scorrendo, quella di turno gira sul bordo e scopre la successiva. Una pagina si gira dal basso, come un
calendario da tavolo, e l'ultima da destra a sinistra, come un libro giapponese.

Tile poco usate messe alla prova: Masthead (mai usata) come gazzetta della bottega, Photo Cover (mai usata) per i
taccuini, Intro Split (mai usata) con i numeri della bottega, Divisore (mai usato) a diamanti, Lista, Lightbox.
Tema chiaro: carta avorio, cuoio bordeaux, oro, blu della carta marmorizzata. Titoli in Fraunces, testo in Lora.
Cursore: segnalibro.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'dorso'
NAME = 'Dorso'
CATEGORIA = 'Artigiani e mestieri'

HEX = {
    'primary': '#8C1C2B', 'primary_contrast': '#FFF8EC', 'secondary': '#2F4B6E', 'secondary_contrast': '#FFFFFF',
    'accent': '#C9A04A', 'dark': '#1E1612', 'light': '#FFFFFF', 'muted': '#F1E8D8', 'muted_contrast': '#2A211B',
    'text': '#2A211B', 'text_muted': '#66584B', 'background': '#FBF6EC', 'border': '#E2D6C2', 'link': '#8C1C2B',
}
HOME, BOTTEGA, LAVORI, CORSI = '{{PAGINA:home}}', '{{PAGINA:la-bottega}}', '{{PAGINA:i-lavori}}', '{{PAGINA:i-corsi}}'
MAIL, PHONE, PHONE_TEL = 'bottega@dorso.example', '055 000 1962', 'tel:+390550001962'
LAT, LNG = '43.7655', '11.2480'

V_PAGINE, V_LIBRO, V_DORSI, V_CUOIO, V_CARTA, V_SFOGLIA = (
    'pexels-video-9490310', 'pexels-video-40999847', 'pexels-video-11769112', 'pexels-video-34740247', 'pexels-video-7417543',
    'pexels-video-6219901')
VIDEOS = [V_PAGINE, V_LIBRO, V_DORSI, V_CUOIO, V_CARTA, V_SFOGLIA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(DORATURA, MARMO_TACCUINO, CUCITURA_DORSO, DORSI_PILA, LUCA_TORCHIO, TORCHIO, TACCUINI_FILA, BOTTEGA_BANCO, MARTA,
 TAGLIERINO, LIBRI_VECCHI, LIBRI_ROSSI, DORSI_SCRITTE, TACCUINO_PIANO, TACCUINO_FIORI, TACCUINO_BLU, PIERO, RESTAURO_MANI,
 LIBRO_BLU, MARMO_1, MARMO_2, ORO, FILO_FORBICI, VIA_MAGGIO, VICOLO, INCHIOSTRO, CALLIGRAFIA, BUSTE, SCAFFALE,
 BIBLIOTECA) = f(
    30560214, 16476327, 38963532, 35098073, 6620988, 6620959, 34870740, 37384667, 31422601, 6621007, 13923801, 36472452,
    8391471, 7657377, 37100698, 38075658, 8327822, 8327809, 36134863, 35985299, 35985306, 30243820, 803929, 33729878,
    38495381, 3826645, 6149064, 190295, 13061431, 36093623)
PHOTOS = [DORATURA, MARMO_TACCUINO, CUCITURA_DORSO, DORSI_PILA, LUCA_TORCHIO, TORCHIO, TACCUINI_FILA, BOTTEGA_BANCO, MARTA,
          TAGLIERINO, LIBRI_VECCHI, LIBRI_ROSSI, DORSI_SCRITTE, TACCUINO_PIANO, TACCUINO_FIORI, TACCUINO_BLU, PIERO,
          RESTAURO_MANI, LIBRO_BLU, MARMO_1, MARMO_2, ORO, FILO_FORBICI, VIA_MAGGIO, VICOLO, INCHIOSTRO, CALLIGRAFIA, BUSTE,
          SCAFFALE, BIBLIOTECA]
DISEGNI = ['dorso-copertina.svg', 'dorso-copertina-tel.svg']
LOGHI = ['dorso-logo-chiaro.png', 'dorso-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#E7C877')
C = K.c
FONT_T = 'Fraunces'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via Maggio 21 · Firenze', BOTTEGA + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]
ORO_COP = '#D9B45A'  # l'oro della copertina (il disegno SVG): le scritte sopra il cuoio ce l'hanno uguale


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 150, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '414/111',
                   'Dorso è una legatoria in Oltrarno dal 1962: rilegature cucite a mano, libri restaurati, taccuini su '
                   'misura e corsi per chi vuole imparare a farli.',
                   [('La bottega', BOTTEGA), ('I lavori', LAVORI), ('I corsi', CORSI), ('Privacy', '/privacy-policy/')],
                   'Orari', 'Martedì — sabato<br>9:30 — 13 · 15:30 — 19', VOCI,
                   '© 2026 Legatoria Dorso · Firenze · P. IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                            ('facebook', 'https://facebook.com')], scuro=False)


# ── L'apertura: la copertina che si apre ──
# La copertina (disegno a tutta tela) gira sul bordo sinistro della tela fino a 170° e sparisce (il retro nascosto);
# le scritte sulla copertina girano con lei: hanno lo stesso perno, scritto in percentuale del loro riquadro
# (x 20, larghezza 60 → il bordo della tela sta a -33,33 % del riquadro), e la stessa x sul telefono, così il perno vale
# su ogni tela. Poi, sul video delle pagine che si sfogliano, tre frasi una dopo l'altra.
def apertura(t):
    m = K.mob
    apri = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': 0}, {'p': 6, 'ry': 0}, {'p': 38, 'ry': 170}]}
    poi = lambda da, a, via_da=None, via_a=None: {'inizio': 0, 'fine': 100, 'tappe': (
        [{'p': 0, 'o': 0, 'y': 18}, {'p': da, 'o': 0, 'y': 18}, {'p': a, 'o': 100, 'y': 0}]
        + ([{'p': via_da, 'o': 100, 'y': 0}, {'p': via_a, 'o': 0, 'y': -18}, {'p': 100, 'o': 0, 'y': -18}] if via_da
           else [{'p': 100, 'o': 100, 'y': 0}]))}
    cop = 'transform-origin:0% 50%;backface-visibility:hidden;'
    scritta = 'transform-origin:-33.33% 50%;backface-visibility:hidden;'
    L = [
        K.livello('do-cop', tipo='image', imageSrc=K.img('dorso-copertina.svg'), imageAlt='', objectFit='fill', x=0, y=0, w=100,
                  h=100, entra='fadeIn', ritardo=0, durata=500, customCSS=cop, scrollMove=apri,
                  responsive={'mobile': {'visible': False}}),
        K.livello('do-cop-t', tipo='image', imageSrc=K.img('dorso-copertina-tel.svg'), imageAlt='', objectFit='fill', x=0, y=0,
                  w=100, h=100, visible=False, entra='fadeIn', ritardo=0, durata=500, customCSS=cop, scrollMove=apri,
                  responsive={'mobile': {'visible': True, 'x': 0, 'y': 0, 'width': 100, 'height': 100}}),
        K.livello('do-ct1', contenuto='Dorso', x=20, y=31, w=60, size=168, peso='600', colore=ORO_COP, align='center',
                  fontFamily=FONT_T, lineHeight=1.0, letterSpacing=6, entra='fadeIn', ritardo=200, durata=900,
                  customCSS=scritta, scrollMove=apri, responsive={'mobile': {'fontSize': m(78), 'x': 20, 'y': 36, 'width': 60}}),
        K.livello('do-ct2', contenuto='Legatoria in Oltrarno · dal 1962', x=20, y=58, w=60, size=16, peso='500', colore=ORO_COP,
                  align='center', letterSpacing=5, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=400,
                  customCSS=scritta, scrollMove=apri, responsive={'mobile': {'fontSize': m(11), 'x': 20, 'y': 46, 'width': 60}}),
        # prima frase
        K.livello('do-t1', contenuto='Ogni libro ha un dorso.', tag='h1', x=6, y=28, w=88, size=96, peso='600',
                  colore=C['chiaro'], align='center', fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0,
                  scrollMove=poi(36, 44, 58, 64), responsive={'mobile': {'fontSize': m(44), 'x': 5, 'y': 30, 'width': 90}}),
        K.livello('do-t1s', contenuto='Il nostro lo cuciamo a mano, una segnatura alla volta, in via Maggio dal 1962.', x=22,
                  y=50, w=56, size=21, peso='400', colore=mix(C['chiaro'], 92), align='center', lineHeight=1.45,
                  entra='fadeIn', ritardo=0, scrollMove=poi(38, 46, 58, 64),
                  responsive={'mobile': {'fontSize': m(16), 'x': 8, 'y': 44, 'width': 84}}),
        # seconda frase
        K.livello('do-t2', contenuto='Questo sito si sfoglia.', x=6, y=28, w=88, size=96, peso='600', colore=C['chiaro'],
                  align='center', fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=poi(66, 74),
                  responsive={'mobile': {'fontSize': m(44), 'x': 5, 'y': 30, 'width': 90}}),
        K.livello('do-t2s', contenuto='Ogni sezione è una pagina: quando arrivi in fondo, gira da sola.', x=22, y=50, w=56,
                  size=21, peso='400', colore=mix(C['chiaro'], 92), align='center', lineHeight=1.45, entra='fadeIn', ritardo=0,
                  scrollMove=poi(68, 76), responsive={'mobile': {'fontSize': m(16), 'x': 8, 'y': 44, 'width': 84}}),
        K.livello('do-b1', tipo='button', contenuto='I lavori', x=38, y=64, w='auto', size=15, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=4, buttonUrl=LAVORI, entra='fadeIn',
                  ritardo=0, scrollMove=poi(78, 86), hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 14, 'y': 56, 'fontSize': m(13)}}),
        K.livello('do-b2', tipo='button', contenuto='I corsi', x=52, y=64, w='auto', size=15, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=18, borderRadius=4, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), buttonUrl=CORSI, entra='fadeIn', ritardo=0, scrollMove=poi(78, 86),
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 54, 'y': 56, 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('do-1', K.sfondo_video(V_PAGINE, overlay=0.5, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── le pagine del libro (Effetto sticky «Pagina») ──
def carta():
    """La carta della pagina: avorio, con l'ombra della piega a sinistra e il taglio appena più scuro a destra."""
    return {'type': 'gradient', 'gradient': {'type': 'linear', 'angle': 90, 'stops': [
        {'color': 'color-mix(in srgb, var(--olo-color-dark) 26%, var(--olo-color-background))', 'position': 0},
        {'color': 'color-mix(in srgb, var(--olo-color-dark) 7%, var(--olo-color-background))', 'position': 4},
        {'color': C['fondo'], 'position': 9}, {'color': C['fondo'], 'position': 96},
        {'color': 'color-mix(in srgb, var(--olo-color-dark) 8%, var(--olo-color-background))', 'position': 100}]}}


def pagina(t, num, occhiello, titolo, testo, foto, alt, dir='destra', link=None, pos='center center'):
    """Una pagina: il testo a sinistra, la foto a destra (al telefono solo il testo, che sta in una schermata), il numero
    di pagina in basso. Arriva da «dir»: destra = si gira come un libro, sotto = come un calendario, sinistra = come un
    libro giapponese."""
    testo_col = [K.label(t, occhiello, color=C['acc']),
                 K.headline(t, titolo, size=60, tag='h2', pad_bottom=16, sizes=(46, 34)),
                 K.text(t, testo, size=17)]
    if link:
        testo_col.append(K.button(t, link[0], link[1], kind='pieno', pad_top=16))
    foto_t = K.image(t, foto, alt, ratio='4/5', position=pos)
    foto_t['advanced'].update({'visible_mobile': False, 'visible_mobile_landscape': False})
    return t.section([
        t.row([t.col(testo_col, wm='1-2', ws='1-1'), t.col([foto_t], wm='1-2', ws='1-1')],
              layout='custom', custom='52,48', gap=72, valign='center', anim=None),
        t.one([K.label(t, '— %d —' % num, color=C['tenue'], align='center', pad_top=28, pad_bottom=0)], anim=None),
    ], bg=carta(), padding='large', width='large', sticky_effect='pagina', sticky_h_dir=dir, sticky_h_align='center',
        style={'padding_top': 60, 'padding_bottom': 40})


def diamanti(t):
    return t.bare('divider', {'style': 'diamonds', 'width': 30, 'thickness': 2, 'color': C['acc2'], 'alignment': 'center',
                              'spacing': 8, 'text': '', 'icon_emoji': ''})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Legatoria in Oltrarno · dal 1962', ['Un libro', 'si cuce.'], size=76, layout='stack',
                                size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Le pagine si piegano in segnature, le segnature si cuciono sui nervi, il dorso si arrotonda '
                                'col martello e la copertina si veste di cuoio. Lo facciamo come lo faceva nonno Piero, nella '
                                'stessa bottega, con gli stessi torchi.', size=17),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([diamanti(t)], anim=None),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 70}))
    # il libro: sei pagine che si sfogliano
    out += [
        pagina(t, 1, 'Pagina 1 · La carta', 'Prima viene <em>la carta.</em>', 'La scegliamo a mano: cotone per i libri che '
               'devono durare cent’anni, carta di Amalfi per i taccuini, marmorizzata per i risguardi. La giri tra le dita e '
               'senti da che parte va la fibra.', MARMO_1, 'Fogli di carta marmorizzata blu e oro arrotolati'),
        pagina(t, 2, 'Pagina 2 · Le segnature', 'Poi si <em>cuce.</em>', 'Ago, filo di lino e cera d’api: ogni segnatura si '
               'cuce alla successiva, sui nervi tesi del telaio. Un libro di trecento pagine sono diciannove segnature e un '
               'pomeriggio intero.', CUCITURA_DORSO, 'Il dorso di un libro con la cucitura in vista'),
        pagina(t, 3, 'Pagina 3 · Il dorso', 'Il dorso <em>si arrotonda.</em>', 'Col martello, piano, finché le segnature '
               'fanno un arco: così il libro si apre bene e si richiude da solo. Poi la colla, la garza, i capitelli di seta.',
               DORSI_SCRITTE, 'Dorsi di libri antichi in cuoio con le scritte in oro'),
        pagina(t, 4, 'Pagina 4 · Si gira dal basso, come un calendario', 'Il cuoio <em>e l’oro.</em>', 'La copertina si veste '
               'di cuoio, e il titolo si scrive col ferro caldo e la foglia d’oro, una lettera alla volta. Se sbagli, '
               'ricominci la copertina.', DORATURA, 'Una mano che imprime l’oro su una copertina rossa', dir='sotto'),
        pagina(t, 5, 'Pagina 5 · Il restauro', 'I libri <em>che tornano.</em>', 'Bibbie di famiglia, registri, ricettari '
               'della nonna: li smontiamo, laviamo le carte, ricuciamo, e il libro torna a chiudersi. Senza nascondere gli '
               'anni che ha.', RESTAURO_MANI, 'Mani che restaurano un libro antico con la lente'),
        pagina(t, 6, 'Pagina 6 · Questa si gira al contrario, come un libro giapponese', 'Il tuo <em>taccuino.</em>',
               'Dicci la misura, la carta e il colore del cuoio: in due settimane è pronto, con le tue iniziali in oro sul '
               'dorso.', TACCUINO_BLU, 'Un taccuino blu fatto a mano su un tessuto blu', dir='sinistra',
               link=('I lavori', LAVORI)),
    ]
    # la gazzetta della bottega (Masthead)
    out.append(t.section([t.one([t.bare('masthead', {
        'edition_text': 'Venerdì 21 novembre 2026 · Firenze', 'nameplate_text': 'La Gazzetta del Dorso',
        'action1_text': 'Scrivici', 'action1_url': 'mailto:' + MAIL, 'action2_text': 'I corsi', 'action2_url': CORSI,
        'kicker_text': 'Restauro · Il caso del mese', 'headline_text': 'Il ricettario di nonna Ada',
        'headline_italic_text': 'si richiude da solo', 'subhead': 'Centoquaranta pagine scritte a mano tra il 1931 e il 1974, '
        'macchiate d’olio e di caffè. Le abbiamo lavate una per una, ricucite sui nervi originali e rimesse nella loro '
        'copertina di tela. Le macchie, quelle, sono rimaste: sono le ricette.',
        'byline_text': 'Di Marta Ricci · 4 minuti', 'bg_color': C['fondo'], 'ink_color': C['testo'], 'ink_soft_color': C['testo'],
        'ink_faint_color': C['tenue'], 'accent': C['acc'], 'rule_color': C['filo'], 'nameplate_size': 60, 'headline_size': 50})],
        anim=None)], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    # i lavori (Lightbox)
    lavoro = lambda n, foto, tit: {'id': 'do-lb-%d' % n, 'title': tit, 'type': 'image', 'url': K.img(foto), 'thumb': K.img(foto),
                                   'caption': tit}
    out.append(t.section([
        t.one([K.sechead(t, 'Dal banco', ['Gli ultimi', 'usciti dal torchio.'], size=60, layout='stack', size_mobile=38)]),
        t.one([diamanti(t)], anim=None),
        t.one([t.bare('lightbox', {'items': [lavoro(1, TACCUINO_FIORI, 'Taccuino con la copertina a fiori'),
                                             lavoro(2, MARMO_TACCUINO, 'Quaderno coi risguardi marmorizzati'),
                                             lavoro(3, LIBRI_ROSSI, 'Tre volumi restaurati per un notaio'),
                                             lavoro(4, TACCUINI_FILA, 'Cinquanta taccuini per un matrimonio'),
                                             lavoro(5, LIBRO_BLU, 'Un atlante del 1890, ricucito'),
                                             lavoro(6, DORATURA, 'Titoli in oro a mano')],
                                   'columns': '3', 'gap': '18', 'thumb_ratio': '4:3', 'thumb_radius': '4', 'thumb_fit': 'cover',
                                   'overlay_style': 'dark', 'show_caption': True, 'animation': 'fade'})], anim=None),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    # i taccuini (Photo Cover)
    out.append(t.section([t.one([t.bare('photocover', {
        'kicker_text': 'Su misura · in due settimane', 'headline_text': 'Il taccuino che non c’era', 'uppercase': False,
        'meta_items': [{'text': 'Carta di cotone o di Amalfi'}, {'text': 'Cuoio in dodici colori'}, {'text': 'Iniziali in oro'}],
        'bg_image': K.img(TACCUINO_PIANO), 'media_label': 'Un taccuino aperto con la penna e il timbro, visto dall’alto',
        'aspect_ratio': '21/9', 'min_height': 520, 'overlay_top': 0.15, 'overlay_bottom': 0.8, 'media_bg': C['nero'],
        'kicker_color': '#E7C877', 'headline_color': C['chiaro'], 'meta_color': C['chiaro']})], anim=None)],
        width='fullbleed', padding='remove-vertical'))
    out.append(X.recensioni(K, t, [
        ('Ho portato il messale di mio nonno a pezzi. Me l’hanno ridato che si apre come un libro nuovo, ma con le sue '
         'macchie.', 'Giovanna', 'Firenze'),
        ('Il corso del sabato: sono uscita con un quaderno cucito da me. Adesso ne ho fatti undici.', 'Elisa', 'Prato'),
        ('Cinquanta taccuini per gli invitati, ognuno col nome in oro. Li hanno tenuti tutti.', 'Lorenzo e Sara', 'Fiesole')],
        'Chi ci ha portato un libro', ['Tre libri,', 'tre persone che li sfogliano ancora.']))
    out.append(K.pannello(t, BOTTEGA_BANCO, 'Via Maggio 21 · Oltrarno', 'Entra <em>in bottega.</em>', 'Il torchio, i ferri '
                          'da doratura, le pelli appese: passa a vedere come si fa un libro, anche senza comprarne uno.',
                          'Dove siamo', BOTTEGA + '#zone', effetto=''))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Tre generazioni,', 'gli stessi torchi.'], 'Nonno Piero l’ha aperta nel 1962 con un '
                   'torchio comprato da un tipografo che chiudeva. Oggi ci lavorano Marta e Luca, e il torchio è ancora lì.',
                   bg=K.video_bg(V_CUOIO, overlay=26), size=100)]
    out.append(t.section([t.one([t.bare('introsplit', {
        'eyebrow': 'Dal 1962 in via Maggio', 'headline': 'Una bottega che fa', 'accent': 'i libri a mano', 'headline_tail': '',
        'uppercase': False, 'accent_italic': True, 'headline_weight': '600', 'headline_color': C['testo'],
        'accent_color': C['acc'], 'lead': 'Rilegature per chi scrive, restauri per chi conserva, taccuini per chi prende '
        'appunti. E corsi, per chi vuole imparare a farli da sé.', 'lead_color': C['tenue'],
        'stats': [{'number': '64', 'label': 'anni di bottega'}, {'number': '3', 'label': 'generazioni'},
                  {'number': '1.200', 'label': 'libri restaurati'}],
        'stat_number_color': C['acc'], 'stat_label_color': C['tenue'], 'cta_text': 'I lavori', 'cta_url': LAVORI,
        'cta_bg': C['acc'], 'cta_color': C['on_acc'], 'media_image': K.img(TORCHIO),
        'media_label': 'Il torchio della bottega con un libro in pressa', 'media_light': False, 'media_aspect': '4/4.4',
        'media_radius': 6, 'badge_number': '1962', 'badge_label': 'Aperta da nonno Piero', 'badge_bg': C['acc'],
        'badge_color': C['on_acc'], 'media_position': 'right'})], anim=None)],
        padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi c’è', ['Le mani', 'della bottega.'], size=60)]),
        K.squadra(t, [(PIERO, 'Piero', 'Il fondatore', 'Ha aperto la bottega nel 1962. A novant’anni passa ancora la mattina, '
                       'con la lente, a guardare le cuciture.', 'center 30%'),
                      (MARTA, 'Marta', 'Restauro', 'La nipote di Piero. Ha studiato restauro a Spoleto e lavora sui libri che '
                       'nessuno vuole più toccare.', 'center 25%'),
                      (LUCA_TORCHIO, 'Luca', 'Rilegature e corsi', 'Ha cominciato come allievo del sabato. Adesso i corsi li '
                       'tiene lui.', 'center 30%')], ratio='4/5', size='300'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([X.orari(K, t, [('Martedì — venerdì', '9:30 — 13 · 15:30 — 19', 'Bottega aperta'),
                                                ('Sabato', '9:30 — 13', 'Bottega e corsi'),
                                                ('Domenica e lunedì', 'chiuso', 'Il torchio riposa')])])],
                         padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 60}))
    out.append(K.domande(t, 'Prima di portarci un libro', ['Quello che', 'ci chiedono sempre.'], [
        ('Quanto costa restaurare un libro?', 'Dipende da quanto è rovinato: lo guardiamo insieme e ti diamo il prezzo prima di '
         'cominciare. Un ricettario di famiglia, di solito, tra 120 e 300 euro.'),
        ('Quanto tempo ci vuole?', 'Una rilegatura due settimane, un restauro da uno a tre mesi.'),
        ('Rilegate le tesi di laurea?', 'Sì, in tre giorni, in tela o in cuoio, col titolo in oro sulla copertina.'),
        ('Si può vedere come lavorate?', 'Sì: la bottega è aperta, entra pure. Il martedì mattina cuciamo.')], 'dob'))
    out.append(X.zone(K, t, 'Dove siamo', ['In via Maggio,', 'di là d’Arno.'], 'Tra Ponte Santa Trinita e Palazzo Pitti, la '
                      'porta di legno con la vetrina dei ferri da doratura.', [('A piedi', 'cinque minuti da Ponte Vecchio'),
                      ('Bus', 'linee C3 e D, fermata Maggio'), ('Bici', 'rastrelliera in piazza Frescobaldi'),
                      ('Telefono', PHONE)], LAT, LNG, 'Legatoria Dorso · via Maggio 21', False, HEX['primary']))
    return out


def build_lavori():
    t = Tpl(ID, 'i-lavori')
    out = [K.intro(t, 'I lavori', ['Rilegare, restaurare,', 'inventare un taccuino.'], 'Tre mestieri nella stessa bottega. '
                   'Qui sotto quello che facciamo più spesso, e quanto costa.', bg=K.video_bg(V_DORSI, overlay=30), size=100)]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Con cosa', ['I materiali', 'del banco.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Tutto quello che usiamo si trova in bottega: lo puoi toccare prima di scegliere.', size=17)],
                     wm='2-5', ws='1-1'),
               t.col([t.bare('list', {'items': [
                   {'text': 'Cuoio di capra conciato al vegetale, in dodici colori', 'icon': 'check'},
                   {'text': 'Pergamena e mezza pelle per i libri antichi', 'icon': 'check'},
                   {'text': 'Carta di cotone, carta di Amalfi, carta marmorizzata fiorentina', 'icon': 'check'},
                   {'text': 'Filo di lino cerato e capitelli di seta cuciti a mano', 'icon': 'check'},
                   {'text': 'Foglia d’oro a 23 carati e ferri da doratura dell’Ottocento', 'icon': 'check'},
                   {'text': 'Colle d’amido e di coniglio, reversibili: un restauro si può sempre disfare', 'icon': 'check'}],
                   'icon_default': 'check', 'icon_color': C['acc'], 'text_color': C['testo'], 'spacing': '16',
                   'icon_size': '18', 'icon_gap': '14'})], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il listino', ['Quanto costa', 'un libro fatto bene.'], size=56)]),
        t.one([P.listino(K, t, [
            ('Rilegatura di una tesi', 'In tela, col titolo in oro sulla copertina. Tre giorni.', '60 €', TACCUINI_FILA, ''),
            ('Rilegatura in mezza pelle', 'Dorso in cuoio e piatti in carta marmorizzata. Due settimane.', 'da 140 €', DORSI_PILA, ''),
            ('Taccuino su misura', 'Carta e cuoio a scelta, iniziali in oro sul dorso.', 'da 75 €', TACCUINO_BLU, 'Il più chiesto'),
            ('Restauro di un libro di famiglia', 'Smontato, lavato, ricucito, ricoperto. Da uno a tre mesi.', 'da 120 €',
             RESTAURO_MANI, ''),
            ('Scatola di conservazione', 'In cartone senza acidi, su misura per un libro raro.', '45 €', BUSTE, '')])], anim=None),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Una pagina alla volta', ['Come nasce', 'un taccuino.'], size=56)]),
        P.passi(K, t, [('01', 'Si sceglie', 'La misura, la carta, il colore del cuoio. Le iniziali, se le vuoi.'),
                       ('02', 'Si piega e si cuce', 'Le segnature, il filo di lino, i nervi sul telaio.'),
                       ('03', 'Si veste', 'Il cartone, il cuoio, i risguardi marmorizzati, la pressa per una notte.'),
                       ('04', 'Si firma', 'Le iniziali in oro col ferro caldo. Poi è tuo.')], colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(K.pannello(t, DORATURA, 'Due settimane', 'Un taccuino <em>col tuo nome.</em>', 'Scrivici la misura e il colore: ti '
                          'rispondiamo col prezzo e una foto della pelle.', 'Scrivici', CORSI + '#scrivi', effetto=''))
    return out


def build_corsi():
    t = Tpl(ID, 'i-corsi')
    out = [K.intro(t, 'I corsi', ['Il sabato mattina', 'si impara a cucire un libro.'], 'Quattro corsi, sei persone al banco, '
                   'tutto il materiale compreso. Esci col libro che hai fatto.', bg=K.video_bg(V_CARTA, overlay=30), size=100)]
    # anche qui le lezioni sono pagine che si sfogliano
    out += [
        pagina(t, 1, 'Corso 1 · Quattro sabati', 'Il quaderno <em>cucito.</em>', 'Piegare, forare, cucire a punto copto. Il '
               'primo sabato esci già con un quaderno. 160 euro, materiale compreso.', FILO_FORBICI,
               'Filo, forbici e un quaderno aperto'),
        pagina(t, 2, 'Corso 2 · Due sabati', 'La legatura <em>giapponese.</em>', 'Quattro fori, un filo, nessuna colla: la '
               'legatura più semplice e più bella. 90 euro.', CALLIGRAFIA, 'Pennini e inchiostro su un foglio scritto'),
        pagina(t, 3, 'Corso 3 · Tre sabati', 'Il dorso <em>tondo.</em>', 'La rilegatura classica, coi nervi, il martello e la '
               'pressa. Per chi ha già fatto il primo corso. 180 euro.', CUCITURA_DORSO,
               'Il dorso di un libro con la cucitura in vista', dir='sotto'),
        pagina(t, 4, 'Corso 4 · Un sabato', 'L’oro <em>a mano.</em>', 'Ferri caldi, foglia d’oro, pazienza: le tue iniziali '
               'su una copertina di cuoio. 70 euro.', ORO, 'Foglia d’oro stropicciata', link=('Prenota un posto', '#scrivi')),
    ]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Corso', 'corso', True, opzioni='Il quaderno cucito\nLa legatura giapponese\nIl dorso tondo\n'
                     'L’oro a mano\nUn taccuino su misura'),
             K.campo('textarea', 'Scrivici', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Sei posti al banco,', 'tienine uno.'],
                                'Ti rispondiamo entro un giorno con le date del prossimo corso.', VOCI, campi,
                                'Dorso — prenotazione corso', 'Prenota', 'Grazie! Ti scriviamo entro domani con le date.'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pagina strappata', color=C['acc2'], align='center'),
                       K.headline(t, 'Qui manca <em>una pagina.</em>', size=92, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento='#E7C877'),
                       K.text(t, 'Capita anche ai libri antichi: una segnatura si stacca e va persa. Torna all’indice e '
                                 'ricomincia a sfogliare.', size=17, align='center', color=mix(C['chiaro'], 90)),
                       K.button(t, 'Torna all’indice', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_SFOGLIA, overlay=40), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito che si sfoglia',
    ['legatoria', 'artigiano', 'libri', 'restauro', 'firenze', 'chiaro', 'slider pro', 'pagina', 'scorrimento'],
    'Una legatoria in Oltrarno, a Firenze, dal 1962: nell’apertura la copertina di cuoio con la cornice d’oro si apre e '
    'scopre le pagine che si sfogliano; poi le sezioni della home sono le pagine di un libro e girano una dopo l’altra, '
    'una dal basso come un calendario e l’ultima al contrario come un libro giapponese. La gazzetta della bottega, i lavori '
    'da sfogliare, i taccuini su misura e i corsi del sabato.',
    [('La bottega', BOTTEGA), ('I lavori', LAVORI), ('I corsi', CORSI)],
    [('home', 'Home', None), ('la-bottega', 'La bottega', 'la-bottega'), ('i-lavori', 'I lavori', 'i-lavori'),
     ('i-corsi', 'I corsi', 'i-corsi')],
    HEX, '\'Lora\', Georgia, serif', '\'Fraunces\', Georgia, serif', '\'IBM Plex Mono\', ui-monospace, monospace',
    ['Lora', 'Fraunces', 'IBM Plex Mono'], corsivo_di=['Lora', 'Fraunces'],
    peso_titoli='600', raggio=4, grana=3, titoli_line='1.02', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'segnalibro'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['accent']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'grazie'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-bottega.json': build_bottega(), 'i-lavori.json': build_lavori(), 'i-corsi.json': build_corsi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
