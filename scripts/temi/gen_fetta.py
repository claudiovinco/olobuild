# -*- coding: utf-8 -*-
"""Genera il tema «Fetta» (id fetta): una salumeria di Bologna in via Pescherie Vecchie, dal 1958. Mortadella al taglio,
parmigiano, tortellini fatti a mano, il pane e il vino.

Era «Scontrino» (18 nov 2026). Il 21 nov l'utente: «unire scontrino alla salumeria non va molto bene, servirebbe un nome
decisamente più intonato… e forse meglio evitare l'animazione dello scontrino per cercare altro», e prima: «in versione
larga ha poco senso, troppo spazio sprecato durante la fase di discesa». Nome scelto dall'utente: Fetta.

La trovata: la lama. Nell'apertura, sul video dell'affettatrice, il nome «Fetta» enorme; scorrendo passa la lama (una riga
d'acciaio che scende) e il nome si divide in sette fette sottili che scivolano via di lato, una dopo l'altra, come dalla
lama dell'affettatrice; poi la frase e i pulsanti. Le voci della home sono mezze schermate con la foto grande (al computer
lo spazio è pieno) che arrivano di lato una sull'altra (Cover orizzontale, da destra e da sinistra), col cartellino del
prezzo. La testata è l'insegna: verde col filo rosso.

Tile poco usate messe alla prova: Scaler (quanti tortellini per quante persone), Search Hero («cosa ti serve?»), Lista.
Tema chiaro: carta oleata, il rosso dell'affettatrice, il rosa della mortadella, il verde dell'insegna. Titoli in DM Serif
Display (col corsivo vero), testo in IBM Plex Sans, prezzi in IBM Plex Mono. Cursore: bilancia.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, radius  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'fetta'
NAME = 'Fetta'
CATEGORIA = 'Ristorazione'

HEX = {
    'primary': '#BE2720', 'primary_contrast': '#FFFFFF', 'secondary': '#2F6B4F', 'secondary_contrast': '#FFFFFF',
    'accent': '#E99BA0', 'dark': '#1F1C18', 'light': '#FFFFFF', 'muted': '#EFE8D8', 'muted_contrast': '#25221E',
    'text': '#25221E', 'text_muted': '#665D53', 'background': '#FBF8EF', 'border': '#DCD2BF', 'link': '#BE2720',
}
HOME, BANCO, BOTTEGA, ORDINA = '{{PAGINA:home}}', '{{PAGINA:il-banco}}', '{{PAGINA:la-bottega}}', '{{PAGINA:ordina}}'
MAIL, PHONE, PHONE_TEL = 'banco@fetta.example', '051 000 1958', 'tel:+390510001958'
LAT, LNG = '44.4939', '11.3449'

V_AFFETTATRICE, V_PROSCIUTTI, V_TORTELLINI, V_FORMAGGIO, V_BOLOGNA, V_POS = (
    'pexels-video-37326360', 'pexels-video-855237', 'pexels-video-7424471', 'pexels-video-6658176', 'pexels-video-34631457',
    'pexels-video-5240825')
VIDEOS = [V_AFFETTATRICE, V_PROSCIUTTI, V_TORTELLINI, V_FORMAGGIO, V_BOLOGNA, V_POS]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(BANCO_PROSCIUTTI, BANCO_VETRO, BANCO_PIENO, BANCO_MERCATO, MORTADELLA_FETTE, MORTADELLA_TAGLIERE, MORTADELLA_PANINO,
 MORTADELLA_PIATTO, INSEGNA, ANTICA_SALUMERIA, NEON, VETRINA, SALAME_TAGLIERE, FORME, FORMAGGI, BANCO_FORMAGGI,
 TORTELLINI, TORTELLINI_PIATTO, TORTELLINI_ROSSI, POS, MANI_CASSA, PORTICO_1, PORTICO_2, PORTICO_3, SFOGLIA, TAGLIATELLE,
 PASTA_MANI, IVO, SANDRO, OLIO, OLIO_BOTTIGLIA, PARMIGIANO, FORMA, PARMIGIANO_SCRITTA, GRATTUGIA) = f(
    11686671, 35474462, 35023463, 30811288, 19585058, 14278936, 17302464, 37679129, 35823638, 36595947, 36781148, 24252376,
    3593425, 33575864, 13079969, 39213896, 4039607, 8601595, 39777115, 9755391, 4959907, 29575250, 29575239, 29575236,
    6287316, 9807689, 6223218, 34674788, 8475169, 8469437, 7296399, 34037769, 18823977, 3693280, 6287519)
PHOTOS = [BANCO_PROSCIUTTI, BANCO_VETRO, BANCO_PIENO, BANCO_MERCATO, MORTADELLA_FETTE, MORTADELLA_TAGLIERE, MORTADELLA_PANINO,
          MORTADELLA_PIATTO, INSEGNA, ANTICA_SALUMERIA, NEON, VETRINA, SALAME_TAGLIERE, FORME, FORMAGGI, BANCO_FORMAGGI,
          TORTELLINI, TORTELLINI_PIATTO, TORTELLINI_ROSSI, POS, MANI_CASSA, PORTICO_1, PORTICO_2, PORTICO_3, SFOGLIA,
          TAGLIATELLE, PASTA_MANI, IVO, SANDRO, OLIO, OLIO_BOTTIGLIA, PARMIGIANO, FORMA, PARMIGIANO_SCRITTA, GRATTUGIA]
LOGHI = ['fetta-logo-chiaro.png', 'fetta-logo-scuro.png']      # fetta_loghi.py
MEDIA = set(PHOTOS) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#F4B6BA')
C = K.c
FONT_T = 'DM Serif Display'
INSEGNA_V = 'var(--olo-color-secondary)'      # il verde dell'insegna
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via Pescherie Vecchie 3 · Bologna', ORDINA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    """L'insegna: la testata verde col filo rosso sotto, sempre in vista."""
    sez = X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)
    s = sez[0]['children'][0]['children'][0]['children'][0]['settings']
    s.update({'header_mode': 'classic', 'nav_bg': INSEGNA_V, 'sticky': True, 'sticky_bg': INSEGNA_V, 'sticky_show_on_up': False,
              'sticky_shrink': False, 'text_color': C['chiaro'], 'logo_text_color': C['chiaro'], 'hamburger_color': C['chiaro'],
              'mobile_text_color': C['chiaro'], 'mobile_bg': INSEGNA_V, 'hover_color': '#FFFFFF', 'active_color': '#FFFFFF',
              'hover_effect_color': C['acc2'], 'mobile_accent_color': C['acc2'], 'nav_phone_color': '#FFFFFF',
              'border': {'top': 0, 'right': 0, 'bottom': 6, 'left': 0, 'style': 'solid', 'color': C['acc']}})
    return sez


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '618/109',
                   'Fetta è una salumeria in via Pescherie Vecchie dal 1958: mortadella al taglio con l’affettatrice '
                   'rossa, parmigiano di montagna, tortellini chiusi a mano ogni mattina.',
                   [('Il banco', BANCO), ('La bottega', BOTTEGA), ('Ordina', ORDINA), ('Privacy', '/privacy-policy/')],
                   'Orari', 'Lunedì — sabato 8 — 19:30<br>Domenica 9 — 13', VOCI,
                   '© 2026 Salumeria Fetta · Bologna · P. IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                                 ('facebook', 'https://facebook.com')],
                   scuro=False)


# ── L'apertura: la lama ──
# Tela 1440 × 810 (telefono 640 × 1400). Il nome «Fetta» è sette livelli uguali, ognuno con la maschera (mt/mb) sulla sua
# striscia: a riposo combaciano e si legge una parola sola. La lama (una riga d'acciaio col lampo) scende dall'alto della
# parola fino in fondo fra 8 e 40; quando passa una striscia, quella scivola via di lato (a destra e a sinistra, alternate),
# ruota appena e sfuma. Poi la frase, il sottotitolo e i pulsanti. Al telefono gli stessi livelli coi valori del telefono.
FETTE = 7
LAMA_DA, LAMA_A = 8, 40


def apertura(t):
    m = K.mob

    def poi(da, a):
        return {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 16}, {'p': da, 'o': 0, 'y': 16},
                                                   {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}

    def fetta(i, tel=False):
        """La striscia i (0 = la più alta): ferma finché passa la lama, poi scivola via di lato."""
        mt, mb = round(i * 100 / FETTE, 3), round(100 - (i + 1) * 100 / FETTE, 3)
        taglio = LAMA_DA + (i + 0.5) / FETTE * (LAMA_A - LAMA_DA)
        k = 1 if i % 2 == 0 else -1
        via = (520 + 70 * i) * (2.0 if tel else 1.0)
        fermo = {'x': 0, 'y': 0, 'r': 0, 'o': 100, 'mt': mt, 'mb': mb}
        tappe = [dict(fermo, p=0), dict(fermo, p=taglio),
                 {'p': taglio + 3, 'x': 14 * k, 'y': 2, 'r': 0.4 * k, 'o': 100, 'mt': mt, 'mb': mb},
                 {'p': taglio + 18, 'x': via * k, 'y': 26 + 6 * i, 'r': (3 + 0.7 * i) * k, 'o': 0, 'mt': mt, 'mb': mb},
                 {'p': 100, 'x': via * k, 'y': 26 + 6 * i, 'r': (3 + 0.7 * i) * k, 'o': 0, 'mt': mt, 'mb': mb}]
        base = dict(contenuto='Fetta', tag='div', peso='400', colore=C['chiaro'], align='center', fontFamily=FONT_T,
                    lineHeight=0.86, letterSpacing=-4, entra='fadeIn', ritardo=0, durata=600,
                    scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe})
        if tel:
            return K.livello('fe-f%d-t' % i, x=4, y=24, w=92, size=150, visible=False,
                             responsive={'mobile': {'visible': True, 'x': 4, 'y': 24, 'width': 92, 'fontSize': m(150)}}, **base)
        return K.livello('fe-f%d' % i, x=6, y=24, w=88, size=330, responsive={'mobile': {'visible': False}}, **base)

    acciaio = {'angle': 90, 'from': 'rgba(255,255,255,0)', 'to': 'rgba(255,255,255,0)', 'fromStop': 0,
               'stops': [{'color': 'rgba(210,214,218,0)', 'position': 0}, {'color': 'rgba(236,239,242,.95)', 'position': 30},
                         {'color': '#FFFFFF', 'position': 50}, {'color': 'rgba(236,239,242,.95)', 'position': 70},
                         {'color': 'rgba(210,214,218,0)', 'position': 100}]}
    lama = lambda y0, y1: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': y0, 'o': 0}, {'p': LAMA_DA - 2, 'y': y0, 'o': 0},
                                                              {'p': LAMA_DA, 'y': y0, 'o': 100}, {'p': LAMA_A, 'y': y1, 'o': 100},
                                                              {'p': LAMA_A + 3, 'y': y1, 'o': 0}, {'p': 100, 'y': y1, 'o': 0}]}
    L = K.veli('fe-velo', 'basso', 34, 70, colore=C['nero'], forza=78, mobile_da=60)
    L += [fetta(i) for i in range(FETTE)] + [fetta(i, True) for i in range(FETTE)]
    L += [
        # la lama: una riga d'acciaio col lampo al centro, che scende sulla parola
        K.livello('fe-lama', tipo='shape', x=0, y=26, w=100, h=0.5, shapeGradient=acciaio, entra='fadeIn', ritardo=0,
                  durata=300, customCSS='filter:drop-shadow(0 0 10px rgba(255,255,255,.85));',
                  scrollMove=lama(-20, 330), responsive={'mobile': {'visible': False}}),
        K.livello('fe-lama-t', tipo='shape', x=0, y=24, w=100, h=0.3, shapeGradient=acciaio, visible=False, entra='fadeIn',
                  ritardo=0, durata=300, customCSS='filter:drop-shadow(0 0 10px rgba(255,255,255,.85));',
                  scrollMove=lama(-20, 330),
                  responsive={'mobile': {'visible': True, 'x': 0, 'y': 24, 'width': 100, 'height': 0.3}}),
        K.livello('fe-occ', contenuto='Salumeria · Bologna · dal 1958', x=6, y=17, w=88, size=15, peso='600',
                  colore='#FFFFFF', align='center', letterSpacing=5, textTransform='uppercase', fontFamily=MONO,
                  entra='fadeIn', ritardo=200, textShadow={'x': 0, 'y': 1, 'blur': 12, 'color': mix(C['nero'], 85)}, responsive={'mobile': {'x': 6, 'y': 19, 'width': 88, 'fontSize': m(12)}}),
        K.livello('fe-t', contenuto='Tagliata sottile,<br><em>come una volta.</em>', tag='h1', x=10, y=36, w=80, size=88,
                  peso='400', colore=C['chiaro'], align='center', fontFamily=FONT_T, lineHeight=1.02, entra='fadeIn',
                  ritardo=0, scrollMove=poi(44, 54), textShadow={'x': 0, 'y': 2, 'blur': 24, 'color': mix(C['nero'], 70)},
                  responsive={'mobile': {'x': 6, 'y': 34, 'width': 88, 'fontSize': m(46)}}),
        K.livello('fe-sub', contenuto='Mortadella, parmigiano e tortellini dal banco di via Pescherie Vecchie. L’affettatrice è '
                  'quella rossa del 1961.', x=26, y=60, w=48, size=19, peso='500', colore='#FFFFFF',
                  align='center', lineHeight=1.5, entra='fadeIn', ritardo=0, scrollMove=poi(48, 58),
                  textShadow={'x': 0, 'y': 1, 'blur': 14, 'color': mix(C['nero'], 85)},
                  responsive={'mobile': {'x': 8, 'y': 50, 'width': 84, 'fontSize': m(16)}}),
        K.livello('fe-b1', tipo='button', contenuto='Il banco', x=39, y=74, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=0, buttonUrl=BANCO, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=poi(54, 64),
                  hover={'bgColor': C['nero'], 'duration': 250}, responsive={'mobile': {'x': 16, 'y': 64, 'fontSize': m(12)}}),
        K.livello('fe-b2', tipo='button', contenuto='Ordina', x=51, y=74, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=16, borderRadius=0, borderWidth=1, borderColor=mix(C['chiaro'], 70),
                  buttonUrl=ORDINA, entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2,
                  scrollMove=poi(54, 64), hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 54, 'y': 64, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('fe-1', K.sfondo_video(V_AFFETTATRICE, overlay=0.42, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2200, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── le voci del banco (mezze schermate con la foto, Cover orizzontale da destra e da sinistra) ──
def carta():
    """La carta oleata del banco: avorio, appena più scura ai bordi."""
    return {'type': 'gradient', 'gradient': {'type': 'linear', 'angle': 90, 'stops': [
        {'color': 'color-mix(in srgb, var(--olo-color-dark) 6%, var(--olo-color-background))', 'position': 0},
        {'color': C['fondo'], 'position': 5}, {'color': C['fondo'], 'position': 95},
        {'color': 'color-mix(in srgb, var(--olo-color-dark) 6%, var(--olo-color-background))', 'position': 100}]}}


def cartellino(t, testo):
    """Il cartellino del prezzo: rosso pieno, scritta bianca in mono."""
    return t.classic('badge', {'text': testo, 'variant': 'solid', 'bg_color': C['acc'], 'text_color': C['on_acc'],
                               'alignment': 'left', 'font_size': '13', 'font_weight': '700', 'text_transform': 'uppercase',
                               'letter_spacing': '1.6', 'badge_radius': radius(0), 'padding_y': 8, 'padding_x': 14,
                               'shadow': 'none', 'font_family': MONO}, style={'margin_top': 18})


def voce(t, n, reparto, titolo, testo, prezzo, foto, alt, pos='center center', link=None):
    """Una voce del banco: le parole da una parte e la foto grande dall'altra (al telefono solo le parole, che stanno in una
    schermata); arriva di lato, da destra le dispari e da sinistra le pari, come le fette che si posano una sull'altra."""
    parole = [K.label(t, reparto, color=C['acc']),
              K.headline(t, titolo, size=64, tag='h2', pad_bottom=16, sizes=(48, 36)),
              K.text(t, testo, size=18),
              cartellino(t, prezzo)]
    if link:
        parole.append(K.button(t, link[0], link[1], kind='pieno', pad_top=22))
    figura = K.image(t, foto, alt, ratio='4/5', position=pos)
    figura['advanced'].update({'visible_mobile': False, 'visible_mobile_landscape': False})
    cols = [t.col(parole, wm='1-2', ws='1-1'), t.col([figura], wm='1-2', ws='1-1')]
    if n % 2 == 0:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='50,50', gap=72, valign='center', anim=None)],
                     bg=carta(), padding='large', width='large', sticky_effect='cover-h',
                     sticky_h_dir='destra' if n % 2 else 'sinistra', sticky_h_align='center',
                     style={'padding_top': 96, 'padding_bottom': 72})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Salumeria · dal 1958', ['Una fetta', 'alla volta.'], size=64, layout='stack',
                                size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Nonna Iole lo diceva a tutti: la mortadella si taglia quando la chiedi, mai prima. Qui sotto '
                                'c’è il nostro banco, una cosa alla volta, come la serviamo in via Pescherie Vecchie.', size=17),
                      K.button(t, 'La bottega', BOTTEGA, kind='pieno', pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out += [
        voce(t, 1, 'Reparto salumi', 'La mortadella, <em>al taglio.</em>', 'Viene da Zola Predosa e la tagliamo con '
             'l’affettatrice rossa del 1961, sottile che quasi si vede attraverso.', 'Mortadella IGP · 2,40 € l’etto',
             MORTADELLA_FETTE, 'Fette di mortadella sottili, appena tagliate'),
        voce(t, 2, 'Reparto formaggi', 'Il parmigiano <em>di montagna.</em>', 'Trenta mesi, dai caseifici dell’Appennino '
             'sopra Reggio. Si rompe col coltello a mandorla, non si taglia.', 'Parmigiano 30 mesi · 2,90 € l’etto',
             PARMIGIANO, 'Scaglie di parmigiano stagionato sul tagliere'),
        voce(t, 3, 'Laboratorio', 'I tortellini, <em>chiusi a mano.</em>', 'Ogni mattina alle sei: la sfoglia tirata col '
             'mattarello, il ripieno di nonna Iole, l’ombelico chiuso col mignolo.', 'Tortellini · 28 € al chilo',
             TORTELLINI, 'Tortellini fatti a mano su un piano infarinato'),
        voce(t, 4, 'Il sabato mattina', 'Le crescentine, <em>calde.</em>', 'Fritte nello strutto e servite con lo squacquerone. '
             'Finiscono verso le undici, quindi non fate tardi.', 'Crescentine · 1 € l’una', SFOGLIA,
             'La sfoglia tirata col mattarello sul banco di legno'),
        voce(t, 5, 'Sempre compreso', 'Due chiacchiere <em>col banco.</em>', 'Non le facciamo pagare: sono la cosa che '
             'vendiamo di più. Il resto lo trovi al banco, o te lo portiamo a casa se ordini entro le undici.',
             'Chiacchiere · offre la casa', BANCO_PROSCIUTTI, 'Il banco della salumeria coi prosciutti appesi',
             link=('Ordina', ORDINA)),
    ]
    # quanti tortellini? (Scaler)
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La domanda di tutti', ['Quanti tortellini', 'per quanti?'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Alla cassa ce lo chiedono dieci volte al giorno. Muovi le persone: ti diciamo quanti tortellini, '
                                'quanto brodo e quanto parmigiano, come li dosava nonna Iole.', size=17)], wm='2-5', ws='1-1'),
               t.col([t.bare('scaler', {
                   'eyebrow': 'Tortellini in brodo', 'heading': 'Per una tavola di Natale', 'intro': '', 'mode': 'scale',
                   'base_label': 'Persone', 'base_value': 4, 'base_min': 1, 'base_max': 20, 'base_step': 1, 'base_suffix': '',
                   'items': [{'name': 'Tortellini', 'amount': 400, 'unit': 'g'}, {'name': 'Brodo di cappone', 'amount': 2, 'unit': 'l'},
                             {'name': 'Parmigiano 30 mesi', 'amount': 80, 'unit': 'g'}, {'name': 'Mortadella per l’antipasto',
                                                                                        'amount': 200, 'unit': 'g'}],
                   'show_total': False, 'zone_accent': C['acc'], 'card_bg': C['sala'], 'align': 'left'})],
                   wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    # cosa ti serve? (Search Hero)
    out.append(t.section([t.one([t.bare('searchhero', {
        'eyebrow_text': 'Al banco o a casa', 'headline_text': 'Cosa ti serve', 'headline_line2': 'per', 'accent_text': 'stasera?',
        'subhead': 'Scrivi quello che cerchi: ti diciamo se c’è e quanto costa al chilo.',
        'search_placeholder': 'Mortadella, parmigiano, tortellini…', 'search_button': 'Cerca al banco', 'search_url': BANCO,
        'chips': 'Mortadella, Parmigiano, Tortellini, Crescentine, Lambrusco, Aceto balsamico', 'bg_color': C['nero'],
        'panel_color': C['nero'], 'accent': C['acc2'], 'accent_on': C['nero'], 'glow_color': 'rgba(233,155,160,0.22)',
        'text_color': C['chiaro'], 'sub_color': mix(C['chiaro'], 76), 'chip_color': mix(C['chiaro'], 66)})], anim=None)],
        width='fullbleed', padding='remove-vertical'))
    out.append(X.recensioni(K, t, [
        ('Chiedi un etto di mortadella e Ivo ti racconta da dove viene. Esci con la spesa e mezz’ora di Bologna.', 'Paola',
         'Santo Stefano'),
        ('La mortadella tagliata così sottile l’avevo vista solo da mia nonna. Ivo ride e dice che è l’affettatrice.', 'Luca',
         'Bolognina'),
        ('Ordino i tortellini per Natale da dodici anni. Ogni anno mi chiedono quanti siamo, e ogni anno sbaglio.', 'Marisa',
         'Casalecchio')],
        'Chi torna al banco', ['Tre clienti,', 'tre fette di Bologna.']))
    out.append(K.pannello(t, BANCO_PROSCIUTTI, 'Via Pescherie Vecchie 3', 'Passa <em>al banco.</em>', 'Lunedì — sabato dalle 8, '
                          'la domenica fino all’una. L’assaggio è sempre compreso.', 'Il banco', BANCO, effetto=''))
    return out


def build_banco():
    t = Tpl(ID, 'il-banco')
    out = [K.intro(t, 'Il banco', ['Quello che c’è', 'sotto il vetro.'], 'I prezzi sono al chilo, come sul cartellino. Al banco '
                   'te li dice Ivo, che li sa a memoria da trent’anni.', bg=K.video_bg(V_PROSCIUTTI, overlay=30), size=96)]
    reparti = [
        ('Salumi', [('Mortadella di Bologna IGP', 'Da Zola Predosa, tagliata sottile', '24 € al kg', MORTADELLA_FETTE, 'La nostra'),
                    ('Prosciutto di Parma 24 mesi', 'Dolce, a mano col coltello il sabato', '38 € al kg', BANCO_PROSCIUTTI, ''),
                    ('Salame rosa', 'Il salame cotto di Bologna, da affettare spesso', '22 € al kg', SALAME_TAGLIERE, '')]),
        ('Formaggi', [('Parmigiano 30 mesi', 'Di montagna, dai caseifici sopra Reggio', '29 € al kg', PARMIGIANO, ''),
                      ('Squacquerone', 'Fresco, da spalmare sulla crescentina', '16 € al kg', FORMAGGI, ''),
                      ('Pecorino di fossa', 'Stagionato sotto terra a Sogliano', '42 € al kg', BANCO_FORMAGGI, '')]),
        ('Pasta fresca', [('Tortellini', 'Chiusi a mano ogni mattina', '28 € al kg', TORTELLINI, 'Si ordina'),
                          ('Tagliatelle', 'Tirate col mattarello, larghe otto millimetri', '14 € al kg', TAGLIATELLE, ''),
                          ('Tortelloni di ricotta', 'Col burro e la salvia, il venerdì', '20 € al kg', TORTELLINI_PIATTO, '')]),
    ]
    for k, (nome, voci) in enumerate(reparti):
        out.append(t.section([
            t.one([K.sechead(t, 'Reparto', [nome + '.'], size=52)]),
            t.one([P.listino(K, t, voci)], anim=None),
        ], bg=solid(C['sala']) if k % 2 else None, padding='large', width='large',
            style={'padding_top': 100, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Non lo troverai', ['Cose che al banco', 'non teniamo.'], size=52, layout='stack',
                                size_mobile=34)], wm='2-5', ws='1-1'),
               t.col([t.bare('list', {'items': [
                   {'text': 'Mortadella già affettata in vaschetta: si taglia quando la chiedi', 'icon': 'x'},
                   {'text': 'Tortellini surgelati: se finiscono, finiscono', 'icon': 'x'},
                   {'text': 'Parmigiano grattugiato in busta: te lo grattugiamo noi, davanti a te', 'icon': 'x'},
                   {'text': 'Fretta: per quella c’è il supermercato in fondo alla via', 'icon': 'x'}],
                   'icon_default': 'x', 'icon_color': C['acc'], 'text_color': C['testo'], 'spacing': '16', 'icon_size': '18',
                   'icon_gap': '14'})], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Dal 1958,', 'lo stesso banco.'], 'Nonna Iole aprì in via Pescherie Vecchie con una '
                   'mortadella e un’affettatrice a manovella. Oggi al banco ci sono suo figlio Ivo e il nipote Sandro.',
                   bg=K.video_bg(V_BOLOGNA, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'La storia della bottega', ['Sessantotto anni', 'in quattro date.'], size=56)]),
        P.passi(K, t, [('1958', 'Iole apre', 'Una mortadella, un’affettatrice a manovella e un banco di marmo.'),
                       ('1961', 'Arriva la rossa', 'L’affettatrice Berkel rossa: la usiamo ancora per la mortadella.'),
                       ('1987', 'Ivo al banco', 'Il figlio di Iole lascia l’università e impara i prezzi a memoria.'),
                       ('2020', 'Sandro in laboratorio', 'Il nipote chiude i tortellini all’alba, con la ricetta della nonna.')],
                colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Al banco', ['Chi ti taglia', 'la fetta.'], size=56)]),
        K.squadra(t, [(IVO, 'Ivo', 'Il banco, dal 1987', 'Sa i prezzi a memoria e i nomi di tutti i clienti. Il conto lo fa a '
                       'voce, e non sbaglia mai.', 'center 30%'),
                      (SANDRO, 'Sandro', 'Il laboratorio', 'Chiude i tortellini alle sei di mattina e la domenica insegna ai '
                       'clienti come si fa l’ombelico.', 'center 25%')], ratio='4/5', size='420'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([K.numeri(t, [('1958', 'la prima fetta'), ('120 kg', 'di mortadella a settimana'),
                                              ('6:00', 'si chiudono i tortellini'), ('0,00 €', 'le chiacchiere')], colonne=4,
                                             size=54)])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    return out


def build_ordina():
    t = Tpl(ID, 'ordina')
    out = [K.intro(t, 'Ordina', ['Lo prepariamo noi,', 'tu passi a ritirarlo.'], 'Ordina entro le undici: a mezzogiorno è pronto '
                   'al banco, incartato nella carta oleata. In centro lo portiamo anche a casa.', bg=K.video_bg(V_TORTELLINI,
                                                                                                                 overlay=26),
                   size=96)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True), K.campo('email', 'Email', 'email'),
             K.campo('select', 'Come', 'come', True, opzioni='Passo a ritirarlo\nPortatemelo a casa (in centro)'),
             K.campo('date', 'Per il giorno', 'giorno', True),
             K.campo('textarea', 'Cosa ti serve', 'messaggio', True, larghezza='1-1',
                     segnaposto='Esempio: 500 g di tortellini, 200 g di mortadella, un pezzo di parmigiano')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Ordina', ['Scrivi la lista,', 'noi tagliamo e incartiamo.'],
                                'Ti richiamiamo se manca qualcosa. Si paga al ritiro.', VOCI, campi, 'Fetta — ordine',
                                'Invia l’ordine', 'Ricevuto! Lo trovi pronto al banco, incartato.'))
    out.append(t.section([t.one([X.orari(K, t, [('Lunedì — venerdì', '8 — 19:30', 'Ordini entro le 11'),
                                                ('Sabato', '8 — 19:30', 'Crescentine calde fino alle 11'),
                                                ('Domenica', '9 — 13', 'Solo tortellini su ordinazione')])])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 60}))
    out.append(X.zone(K, t, 'Dove siamo', ['In Pescherie Vecchie,', 'sotto il portico.'], 'Nel Quadrilatero, tra piazza Maggiore '
                      'e le Due Torri: l’insegna verde col bordo rosso.', [('A piedi', 'due minuti da piazza Maggiore'),
                      ('Bus', 'fermata Rizzoli'), ('Bici', 'rastrelliera in via Clavature'), ('Telefono', PHONE)], LAT, LNG,
                      'Salumeria Fetta · via Pescherie Vecchie 3', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · banco vuoto', color='#F4B6BA', align='center'),
                       K.headline(t, 'Questa fetta <em>è finita.</em>', size=80, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento='#F4B6BA'),
                       K.text(t, 'La pagina che cerchi non c’è più. Torna al banco: qualcosa di buono lo troviamo.', size=17,
                              align='center', color=mix(C['chiaro'], 90)),
                       K.button(t, 'Torna al banco', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_PROSCIUTTI, overlay=40), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Tagliata sottile',
    ['salumeria', 'gastronomia', 'bologna', 'negozio', 'chiaro', 'slider pro', 'percorso', 'affettatrice', 'scorrimento'],
    'Una salumeria di Bologna dal 1958, con l’insegna verde e l’affettatrice rossa. Nell’apertura passa la lama e il nome si '
    'divide in fette sottili che scivolano via; poi le voci del banco arrivano di lato una sull’altra, ognuna con la sua foto e '
    'il cartellino del prezzo. Quanti tortellini per quante persone, la ricerca al banco, i prezzi al chilo.',
    [('Il banco', BANCO), ('La bottega', BOTTEGA), ('Ordina', ORDINA)],
    [('home', 'Home', None), ('il-banco', 'Il banco', 'il-banco'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('ordina', 'Ordina', 'ordina')],
    HEX, '\'IBM Plex Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'DM Serif Display\', Georgia, serif',
    '\'IBM Plex Mono\', ui-monospace, monospace', ['IBM Plex Sans', 'DM Serif Display', 'IBM Plex Mono'],
    corsivo_di=['IBM Plex Sans', 'DM Serif Display'], peso_titoli='400', raggio=0, grana=4, titoli_line='1.04',
    titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'bilancia'
THEME['cursor']['ring_color'] = '#F2EFE6'
THEME['cursor']['dot_color'] = HEX['primary']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'decorativo'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-banco.json': build_banco(), 'la-bottega.json': build_bottega(), 'ordina.json': build_ordina(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
