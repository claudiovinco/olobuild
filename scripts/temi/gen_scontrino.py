# -*- coding: utf-8 -*-
"""Genera il tema «Scontrino» (id scontrino): una salumeria di Bologna in via Pescherie Vecchie, dal 1958. Mortadella al
taglio, parmigiano, tortellini fatti a mano, il pane e il vino.

La trovata (18 nov 2026, sera; utente: «cerca di farne altri 5»): il sito esce dalla stampante come uno scontrino. La
testata è la stampante (scura, sempre in vista); nell'apertura lo scontrino esce dalla fessura riga per riga mentre scorri,
coi prezzi e i pulsanti stampati sopra; poi le sezioni della home sono un percorso dove ogni sezione arriva da «Sopra»
(Sezioni → Effetto sticky → Cover orizzontale, «Arriva da: sopra»): scorrendo in giù la carta scende da sotto la stampante,
una voce dello scontrino alla volta, fino al totale.

Tile poco usate messe alla prova: Scaler (mai usata: quanti tortellini per quante persone), Search Hero (mai usata: «cosa
ti serve?»), Divisore (mai usato) tratteggiato e a zigzag, Lista.
Tema chiaro: carta termica, inchiostro, il rosso del timbro, il rosa della mortadella. Titoli in Courier Prime, testo in
IBM Plex Sans. Cursore: bilancia.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'scontrino'
NAME = 'Scontrino'
CATEGORIA = 'Ristorazione'

HEX = {
    'primary': '#BE2720', 'primary_contrast': '#FFFFFF', 'secondary': '#2F6B4F', 'secondary_contrast': '#FFFFFF',
    'accent': '#E99BA0', 'dark': '#1F1C18', 'light': '#FFFFFF', 'muted': '#EFE8D8', 'muted_contrast': '#25221E',
    'text': '#25221E', 'text_muted': '#665D53', 'background': '#FBF8EF', 'border': '#DCD2BF', 'link': '#BE2720',
}
HOME, BANCO, BOTTEGA, ORDINA = '{{PAGINA:home}}', '{{PAGINA:il-banco}}', '{{PAGINA:la-bottega}}', '{{PAGINA:ordina}}'
MAIL, PHONE, PHONE_TEL = 'banco@scontrino.example', '051 000 1958', 'tel:+390510001958'
LAT, LNG = '44.4939', '11.3449'

V_AFFETTATRICE, V_PROSCIUTTI, V_TORTELLINI, V_FORMAGGIO, V_BOLOGNA, V_POS = (
    'pexels-video-37326360', 'pexels-video-855237', 'pexels-video-7424471', 'pexels-video-6658176', 'pexels-video-34631457',
    'pexels-video-5240825')
VIDEOS = [V_AFFETTATRICE, V_PROSCIUTTI, V_TORTELLINI, V_FORMAGGIO, V_BOLOGNA, V_POS]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(BANCO_PROSCIUTTI, BANCO_VETRO, BANCO_PIENO, BANCO_MERCATO, MORTADELLA_FETTE, MORTADELLA_TAGLIERE, MORTADELLA_PANINO,
 MORTADELLA_PIATTO, INSEGNA, ANTICA_SALUMERIA, NEON, VETRINA, SALAME_TAGLIERE, FORME, FORMAGGI, BANCO_FORMAGGI,
 TORTELLINI, TORTELLINI_PIATTO, TORTELLINI_ROSSI, POS, SCONTRINO_MANI, PORTICO_1, PORTICO_2, PORTICO_3, SFOGLIA, TAGLIATELLE,
 PASTA_MANI, IVO, SANDRO, OLIO, OLIO_BOTTIGLIA, PARMIGIANO, FORMA, PARMIGIANO_SCRITTA, GRATTUGIA) = f(
    11686671, 35474462, 35023463, 30811288, 19585058, 14278936, 17302464, 37679129, 35823638, 36595947, 36781148, 24252376,
    3593425, 33575864, 13079969, 39213896, 4039607, 8601595, 39777115, 9755391, 4959907, 29575250, 29575239, 29575236,
    6287316, 9807689, 6223218, 34674788, 8475169, 8469437, 7296399, 34037769, 18823977, 3693280, 6287519)
PHOTOS = [BANCO_PROSCIUTTI, BANCO_VETRO, BANCO_PIENO, BANCO_MERCATO, MORTADELLA_FETTE, MORTADELLA_TAGLIERE, MORTADELLA_PANINO,
          MORTADELLA_PIATTO, INSEGNA, ANTICA_SALUMERIA, NEON, VETRINA, SALAME_TAGLIERE, FORME, FORMAGGI, BANCO_FORMAGGI,
          TORTELLINI, TORTELLINI_PIATTO, TORTELLINI_ROSSI, POS, SCONTRINO_MANI, PORTICO_1, PORTICO_2, PORTICO_3, SFOGLIA,
          TAGLIATELLE, PASTA_MANI, IVO, SANDRO, OLIO, OLIO_BOTTIGLIA, PARMIGIANO, FORMA, PARMIGIANO_SCRITTA, GRATTUGIA]
DISEGNI = ['scontrino-stampante.svg', 'scontrino-rotolo.svg']
LOGHI = ['scontrino-logo-chiaro.png', 'scontrino-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='700', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#F4B6BA')
C = K.c
FONT_T = 'Courier Prime'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via Pescherie Vecchie 3 · Bologna', ORDINA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    """La stampante: la testata scura, sempre in vista (non sparisce scorrendo in giù), col filo rosso sotto. Nel
    percorso dello scontrino la carta esce da qui sotto."""
    sez = X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)
    s = sez[0]['children'][0]['children'][0]['children'][0]['settings']
    s.update({'header_mode': 'classic', 'nav_bg': C['nero'], 'sticky': True, 'sticky_bg': C['nero'], 'sticky_show_on_up': False,
              'sticky_shrink': False, 'text_color': C['chiaro'], 'logo_text_color': C['chiaro'], 'hamburger_color': C['chiaro'],
              'mobile_text_color': C['chiaro'], 'mobile_bg': C['nero'], 'hover_color': C['acc2'], 'active_color': C['acc2'],
              'hover_effect_color': C['acc2'], 'mobile_accent_color': C['acc2'], 'nav_phone_color': C['acc2'],
              'border': {'top': 0, 'right': 0, 'bottom': 6, 'left': 0, 'style': 'solid', 'color': C['acc']}})
    return sez


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '628/114',
                   'Scontrino è una salumeria in via Pescherie Vecchie dal 1958: mortadella al taglio, parmigiano di '
                   'montagna, tortellini chiusi a mano ogni mattina. E lo scontrino, sempre.',
                   [('Il banco', BANCO), ('La bottega', BOTTEGA), ('Ordina', ORDINA), ('Privacy', '/privacy-policy/')],
                   'Orari', 'Lunedì — sabato 8 — 19:30<br>Domenica 9 — 13', VOCI,
                   '© 2026 Salumeria Scontrino · Bologna · P. IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                                 ('facebook', 'https://facebook.com')],
                   scuro=False)


# ── L'apertura: lo scontrino che esce dalla stampante ──
# Computer (tela 1440 × 810): la stampante in cima al centro (x 34, y 0, 32 × 13 %), lo scontrino sotto (x 37, 26 × 130 %),
# che parte tutto sopra la tela (y -118: il bordo strappato appena sotto la fessura) e scende di 900 px. Le righe e i
# pulsanti stampati sono livelli con lo stesso movimento, sopra la carta; la stampante sta sopra a tutto e copre quello che
# non è ancora uscito. Telefono (tela 640 × 1400): stessa idea con livelli solo per il telefono (movimenti × 2,25).
# Le righe dello scontrino (20 nov 2026, utente: «anche il tema scontrino ha problemi»). Lo Slider Pro non scende mai
# sotto 14 px per i paragrafi e 11 per le scritte in maiuscolo: con righe da 36 caratteri e minuscole il testo andava a
# capo dai 1280 px in giù, si allungava oltre la carta e finiva sotto i pulsanti. Ora tutto in maiuscolo (come la carta
# termica), 32 caratteri al computer e 22 al telefono e sul tablet, con caratteri che riempiono la carta e scalano con lei.
RIGHE = ('SALUMERIA SCONTRINO · DAL 1958<br>PESCHERIE VECCHIE 3 · BOLOGNA<br>- - - - - - - - - - - - - - - -<br>'
         '1 x MORTADELLA IGP 100 g .. 2,40<br>1 x PARMIGIANO 30 MESI .... 7,80<br>1 x TORTELLINI 500 g ..... 14,00<br>'
         '1 x CRESCENTINE CALDE ..... 3,00<br>1 x DUE CHIACCHIERE ....... 0,00<br>- - - - - - - - - - - - - - - -<br>'
         'TOTALE ................... 27,20<br><br>GRAZIE E TORNI PRESTO')
RIGHE_T = ('SALUMERIA SCONTRINO<br>BOLOGNA · DAL 1958<br>- - - - - - - - - - -<br>MORTADELLA IGP .. 2,40<br>'
           'PARMIGIANO ...... 7,80<br>TORTELLINI ..... 14,00<br>CRESCENTINE ..... 3,00<br>DUE CHIACCHIERE . 0,00<br>'
           '- - - - - - - - - - -<br>TOTALE ......... 27,20<br>GRAZIE E TORNI PRESTO')


def apertura(t):
    m = K.mob
    esce = lambda dy: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0}, {'p': 6, 'y': 0}, {'p': 88, 'y': dy},
                                                          {'p': 100, 'y': dy}]}
    def poi(da, a, via_da=None, via_a=None):
        # Un testo che c'è già all'apertura (da = 0) parte visibile: con la tappa «0 → invisibile» il titolo
        # compariva solo dopo aver cominciato a scorrere (20 nov 2026, come in Zapping).
        entra = [{'p': 0, 'o': 100, 'y': 0}] if da <= 0 else [{'p': 0, 'o': 0, 'y': 14}, {'p': da, 'o': 0, 'y': 14}, {'p': a, 'o': 100, 'y': 0}]
        esce = ([{'p': via_da, 'o': 100, 'y': 0}, {'p': via_a, 'o': 0, 'y': -14}, {'p': 100, 'o': 0, 'y': -14}] if via_da
                else [{'p': 100, 'o': 100, 'y': 0}])
        return {'inizio': 0, 'fine': 100, 'tappe': entra + esce}
    carta = 'filter:drop-shadow(0 18px 30px rgba(0,0,0,.35));'
    D, T = -900 * 1.0, -900 * 2.25  # (il segno lo gira esce(): qui sotto è positivo)
    L = K.veli('sc-velo', 'basso', 30, 64, colore=C['nero'], forza=86, mobile_da=58)
    L += [
        # computer
        K.livello('sc-carta', tipo='image', imageSrc=K.img('scontrino-rotolo.svg'), imageAlt='', objectFit='fill', x=37, y=-118,
                  w=26, h=130, entra='fadeIn', ritardo=0, durata=400, customCSS=carta, scrollMove=esce(-D),
                  responsive={'tablet': {'visible': False}, 'mobile': {'visible': False}}),
        K.livello('sc-righe', contenuto=RIGHE, x=38.5, y=-104, w=23, size=14, peso='700', colore=C['testo'], align='left',
                  fontFamily=MONO, lineHeight=1.75, letterSpacing=0, textTransform='uppercase', entra='fadeIn', ritardo=0,
                  durata=400, scrollMove=esce(-D), responsive={'tablet': {'visible': False}, 'mobile': {'visible': False}}),
        K.livello('sc-b1', tipo='button', contenuto='Il banco', x=40, y=-58, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=14, borderRadius=0, buttonUrl=BANCO, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=esce(-D), hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'tablet': {'visible': False}, 'mobile': {'visible': False}}),
        K.livello('sc-b2', tipo='button', contenuto='Ordina', x=51, y=-58, w='auto', size=13, peso='700', colore=C['testo'],
                  bgColor='transparent', padding=14, borderRadius=0, borderWidth=1, borderColor=C['testo'], buttonUrl=ORDINA,
                  entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=esce(-D),
                  hover={'bgColor': mix(C['testo'], 10), 'duration': 250}, responsive={'tablet': {'visible': False}, 'mobile': {'visible': False}}),
        # telefono
        K.livello('sc-carta-t', tipo='image', imageSrc=K.img('scontrino-rotolo.svg'), imageAlt='', objectFit='fill', x=12, y=-50,
                  w=76, h=56, visible=False, entra='fadeIn', ritardo=0, durata=400, customCSS=carta, scrollMove=esce(-T),
                  responsive={'mobile': {'visible': True, 'x': 12, 'y': -50, 'width': 76, 'height': 56}}),
        K.livello('sc-righe-t', contenuto=RIGHE_T, x=14, y=-45, w=72, size=14, peso='700', colore=C['testo'], align='left',
                  fontFamily=MONO, lineHeight=1.5, textTransform='uppercase', visible=False, entra='fadeIn', ritardo=0, durata=400, scrollMove=esce(-T),
                  responsive={'mobile': {'visible': True, 'x': 14, 'y': -45, 'width': 72, 'fontSize': m(18)}}),
        K.livello('sc-b1-t', tipo='button', contenuto='Il banco', x=16, y=-14, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=14, borderRadius=0, buttonUrl=BANCO, visible=False, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=esce(-T),
                  responsive={'mobile': {'visible': True, 'x': 16, 'y': -7, 'fontSize': m(12)}}),
        K.livello('sc-b2-t', tipo='button', contenuto='Ordina', x=52, y=-14, w='auto', size=13, peso='700', colore=C['testo'],
                  bgColor='transparent', padding=14, borderRadius=0, borderWidth=1, borderColor=C['testo'], buttonUrl=ORDINA,
                  visible=False, entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=esce(-T),
                  responsive={'mobile': {'visible': True, 'x': 52, 'y': -7, 'fontSize': m(12)}}),
        # tablet (tela 1024 × 576): carta più larga, righe corte, stessa corsa del computer
        K.livello('sc-carta-tab', tipo='image', imageSrc=K.img('scontrino-rotolo.svg'), imageAlt='', objectFit='fill', x=32,
                  y=-118, w=36, h=130, visible=False, entra='fadeIn', ritardo=0, durata=400, customCSS=carta,
                  scrollMove=esce(-D), responsive={'tablet': {'visible': True}, 'mobile': {'visible': False}}),
        K.livello('sc-righe-tab', contenuto=RIGHE_T, x=34, y=-104, w=32, size=21, peso='700', colore=C['testo'],
                  align='left', fontFamily=MONO, lineHeight=1.5, textTransform='uppercase', visible=False, entra='fadeIn',
                  ritardo=0, durata=400, scrollMove=esce(-D),
                  responsive={'tablet': {'visible': True, 'width': 32, 'fontSize': 21}, 'mobile': {'visible': False}}),
        K.livello('sc-b1-tab', tipo='button', contenuto='Il banco', x=34, y=-41, w='auto', size=13, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=14, borderRadius=0, buttonUrl=BANCO, visible=False,
                  entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=esce(-D),
                  hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'tablet': {'visible': True, 'x': 34}, 'mobile': {'visible': False}}),
        K.livello('sc-b2-tab', tipo='button', contenuto='Ordina', x=51, y=-41, w='auto', size=13, peso='700',
                  colore=C['testo'], bgColor='transparent', padding=14, borderRadius=0, borderWidth=1,
                  borderColor=C['testo'], buttonUrl=ORDINA, visible=False, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=esce(-D),
                  hover={'bgColor': mix(C['testo'], 10), 'duration': 250},
                  responsive={'tablet': {'visible': True, 'x': 51}, 'mobile': {'visible': False}}),
        # la stampante, sopra a tutto
        K.livello('sc-stampante', tipo='image', imageSrc=K.img('scontrino-stampante.svg'), imageAlt='La stampante dello scontrino',
                  objectFit='fill', x=34, y=-1, w=32, h=13, entra='fadeIn', ritardo=0, durata=400,
                  responsive={'tablet': {'x': 28, 'y': -1, 'width': 44, 'height': 17.8},
                              'mobile': {'x': 6, 'y': -0.5, 'width': 88, 'height': 5.4}}),
        # il titolo, diviso ai due lati dello scontrino
        K.livello('sc-t1', contenuto='Questo sito', tag='h1', x=3, y=40, w=31, size=60, peso='700', colore=C['chiaro'],
                  align='right', fontFamily=FONT_T, lineHeight=1.05, entra='fadeInLeft', ritardo=300, durata=900,
                  responsive={'tablet': {'x': 1, 'width': 29}, 'mobile': {'visible': False}}),
        K.livello('sc-t2', contenuto='si stampa.', x=66, y=40, w=31, size=60, peso='700', colore=C['acc2'], align='left',
                  fontFamily=FONT_T, lineHeight=1.05, fontStyle='italic', entra='fadeInRight', ritardo=450, durata=900,
                  responsive={'tablet': {'x': 70, 'width': 29}, 'mobile': {'visible': False}}),
        K.livello('sc-t1t', contenuto='Questo sito <em>si stampa.</em>', x=6, y=78, w=88, size=60, peso='700',
                  colore=C['chiaro'], align='center', fontFamily=FONT_T, lineHeight=1.05, visible=False, entra='fadeInUp',
                  ritardo=300, durata=900, responsive={'mobile': {'visible': True, 'x': 6, 'y': 80, 'width': 88, 'fontSize': m(36)}}),
        K.livello('sc-sub', contenuto='Una salumeria di Bologna dal 1958. Il conto si fa ancora a voce, ma lo scontrino esce '
                  'sempre.', x=3, y=58, w=31, size=18, peso='500', colore=mix(C['chiaro'], 90), align='right', lineHeight=1.5,
                  ritardo=600, scrollMove=poi(0, 1, 50, 56),
                  responsive={'tablet': {'x': 1, 'width': 29},
                              'mobile': {'x': 8, 'y': 88, 'width': 84, 'fontSize': m(15), 'textAlign': 'center'}}),
    ]
    sl = [K.diapositiva('sc-1', K.sfondo_video(V_AFFETTATRICE, overlay=0.42, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── le voci dello scontrino (percorso: ogni sezione arriva da sopra) ──
def carta():
    """La carta termica: avorio, appena più scura ai bordi come il rotolo."""
    return {'type': 'gradient', 'gradient': {'type': 'linear', 'angle': 90, 'stops': [
        {'color': 'color-mix(in srgb, var(--olo-color-dark) 8%, var(--olo-color-background))', 'position': 0},
        {'color': C['fondo'], 'position': 6}, {'color': C['fondo'], 'position': 94},
        {'color': 'color-mix(in srgb, var(--olo-color-dark) 8%, var(--olo-color-background))', 'position': 100}]}}


def tratteggio(t, stile='dashed'):
    return t.bare('divider', {'style': stile, 'width': 100, 'thickness': 2, 'color': C['testo'], 'alignment': 'center',
                              'spacing': 18, 'text': '', 'icon_emoji': ''})


def voce(t, reparto, titolo, testo, riga, link=None):
    """Una voce: il reparto, il titolo, due righe e la riga dello scontrino col prezzo, centrate in una colonna stretta."""
    tiles = [K.label(t, reparto, color=C['acc'], align='center'),
             K.headline(t, titolo, size=58, tag='h2', pad_bottom=14, align='center', sizes=(44, 32)),
             K.text(t, testo, size=17, align='center'),
             tratteggio(t),
             K.text(t, '<p><strong>%s</strong></p>' % riga, size=15, align='center', color=C['testo'])]
    if link:
        tiles.append(K.button(t, link[0], link[1], kind='pieno', align='center', pad_top=10))
    return t.section([K.centered(t, tiles, side=22)], bg=carta(), padding='large', width='large',
                     sticky_effect='cover-h', sticky_h_dir='sopra', sticky_h_align='center',
                     style={'padding_top': 110, 'padding_bottom': 60})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Salumeria · dal 1958', ['Lo scontrino', 'lo facciamo sempre.'], size=64, layout='stack',
                                size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Nonna Iole lo diceva a tutti: lo scontrino è la firma della bottega. Qui sotto c’è il nostro, '
                                'una voce alla volta, come esce dalla cassa di via Pescherie Vecchie.', size=17),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out += [
        voce(t, 'Reparto salumi · voce 1', 'La mortadella, <em>al taglio.</em>', 'Viene da Zola Predosa, la tagliamo con '
             'l’affettatrice rossa del 1961, sottile che quasi si vede attraverso.', '1 x MORTADELLA IGP 100 g ...... 2,40'),
        voce(t, 'Reparto formaggi · voce 2', 'Il parmigiano <em>di montagna.</em>', 'Trenta mesi, dai caseifici dell’Appennino '
             'sopra Reggio. Si rompe col coltello a mandorla, non si taglia.', '1 x PARMIGIANO 30 MESI ........ 7,80'),
        voce(t, 'Laboratorio · voce 3', 'I tortellini, <em>chiusi a mano.</em>', 'Ogni mattina alle sei: la sfoglia tirata col '
             'mattarello, il ripieno di nonna Iole, l’ombelico chiuso col mignolo.', '1 x TORTELLINI 500 g ......... 14,00'),
        voce(t, 'Forno · voce 4', 'Le crescentine, <em>calde.</em>', 'Il sabato mattina, fritte nello strutto. Finiscono verso '
             'le undici, quindi non fate tardi.', '1 x CRESCENTINE CALDE ......... 3,00'),
        voce(t, 'Omaggio · voce 5', 'Due chiacchiere <em>col banco.</em>', 'Non le facciamo pagare. Ma le scriviamo sullo '
             'scontrino, perché sono la cosa che vendiamo di più.', '1 x DUE CHIACCHIERE COL BANCO .. 0,00'),
        voce(t, 'Totale', 'Grazie <em>e torni presto.</em>', 'Lo scontrino finisce qui. Il resto lo trovi al banco, o te lo '
             'portiamo a casa se ordini prima delle undici.', 'TOTALE ....................... 27,20',
             link=('Ordina', ORDINA)),
    ]
    out.append(t.section([t.one([tratteggio(t, 'zigzag')], anim=None)], bg=solid(C['fondo']), padding='small',
                         style={'padding_top': 0, 'padding_bottom': 0}))
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
        ('Lo scontrino me lo sono incorniciato: c’era scritto «due chiacchiere col banco, 0,00». Erano le migliori.', 'Paola',
         'Santo Stefano'),
        ('La mortadella tagliata così sottile l’avevo vista solo da mia nonna. Ivo ride e dice che è l’affettatrice.', 'Luca',
         'Bolognina'),
        ('Ordino i tortellini per Natale da dodici anni. Ogni anno mi chiedono quanti siamo, e ogni anno sbaglio.', 'Marisa',
         'Casalecchio')],
        'Chi ha lo scontrino', ['Tre clienti,', 'tre scontrini tenuti.']))
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
        t.one([K.sechead(t, 'Lo scontrino della bottega', ['Sessantotto anni', 'in quattro righe.'], size=56)]),
        P.passi(K, t, [('1958', 'Iole apre', 'Una mortadella, un’affettatrice a manovella e la cassa che stampa lo scontrino.'),
                       ('1961', 'Arriva la rossa', 'L’affettatrice Berkel rossa: la usiamo ancora per la mortadella.'),
                       ('1987', 'Ivo al banco', 'Il figlio di Iole lascia l’università e impara i prezzi a memoria.'),
                       ('2020', 'Sandro in laboratorio', 'Il nipote chiude i tortellini all’alba, con la ricetta della nonna.')],
                colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Al banco', ['Chi ti fa', 'lo scontrino.'], size=56)]),
        K.squadra(t, [(IVO, 'Ivo', 'Il banco, dal 1987', 'Sa i prezzi a memoria e i nomi di tutti i clienti. Il conto lo fa a '
                       'voce, poi controlla sullo scontrino.', 'center 30%'),
                      (SANDRO, 'Sandro', 'Il laboratorio', 'Chiude i tortellini alle sei di mattina e la domenica insegna ai '
                       'clienti come si fa l’ombelico.', 'center 25%')], ratio='4/5', size='300'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([K.numeri(t, [('1958', 'il primo scontrino'), ('120 kg', 'di mortadella a settimana'),
                                              ('6:00', 'si chiudono i tortellini'), ('0,00 €', 'le chiacchiere')], colonne=4,
                                             size=54)])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    return out


def build_ordina():
    t = Tpl(ID, 'ordina')
    out = [K.intro(t, 'Ordina', ['Lo prepariamo noi,', 'tu passi a ritirarlo.'], 'Ordina entro le undici: a mezzogiorno è pronto '
                   'al banco, incartato e con lo scontrino. In centro lo portiamo anche a casa.', bg=K.video_bg(V_TORTELLINI,
                                                                                                                 overlay=26),
                   size=96)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True), K.campo('email', 'Email', 'email'),
             K.campo('select', 'Come', 'come', True, opzioni='Passo a ritirarlo\nPortatemelo a casa (in centro)'),
             K.campo('date', 'Per il giorno', 'giorno', True),
             K.campo('textarea', 'Cosa ti serve', 'messaggio', True, larghezza='1-1',
                     segnaposto='Esempio: 500 g di tortellini, 200 g di mortadella, un pezzo di parmigiano')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Ordina', ['Scrivi la lista,', 'noi facciamo lo scontrino.'],
                                'Ti richiamiamo se manca qualcosa. Si paga al ritiro.', VOCI, campi, 'Scontrino — ordine',
                                'Invia l’ordine', 'Ricevuto! Lo trovi pronto al banco, con lo scontrino.'))
    out.append(t.section([t.one([X.orari(K, t, [('Lunedì — venerdì', '8 — 19:30', 'Ordini entro le 11'),
                                                ('Sabato', '8 — 19:30', 'Crescentine calde fino alle 11'),
                                                ('Domenica', '9 — 13', 'Solo tortellini su ordinazione')])])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 60}))
    out.append(X.zone(K, t, 'Dove siamo', ['In Pescherie Vecchie,', 'sotto il portico.'], 'Nel Quadrilatero, tra piazza Maggiore '
                      'e le Due Torri: l’insegna verde col bordo rosso.', [('A piedi', 'due minuti da piazza Maggiore'),
                      ('Bus', 'fermata Rizzoli'), ('Bici', 'rastrelliera in via Clavature'), ('Telefono', PHONE)], LAT, LNG,
                      'Salumeria Scontrino · via Pescherie Vecchie 3', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · carta finita', color='#F4B6BA', align='center'),
                       K.headline(t, 'Questo scontrino <em>è finito.</em>', size=80, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento='#F4B6BA'),
                       K.text(t, 'La pagina che cerchi non c’è. Mettiamo un rotolo nuovo: torna alla prima riga.', size=17,
                              align='center', color=mix(C['chiaro'], 90)),
                       K.button(t, 'Prima riga', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_POS, overlay=40), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito che si stampa',
    ['salumeria', 'gastronomia', 'bologna', 'negozio', 'chiaro', 'slider pro', 'percorso', 'scontrino', 'scorrimento'],
    'Una salumeria di Bologna dal 1958: la testata è la stampante e il sito ne esce come uno scontrino. Nell’apertura lo '
    'scontrino scende dalla fessura riga per riga coi prezzi e i pulsanti stampati; poi le voci della home arrivano dall’alto, '
    'una alla volta, fino al totale. Quanti tortellini per quante persone, la ricerca al banco, i prezzi al chilo.',
    [('Il banco', BANCO), ('La bottega', BOTTEGA), ('Ordina', ORDINA)],
    [('home', 'Home', None), ('il-banco', 'Il banco', 'il-banco'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('ordina', 'Ordina', 'ordina')],
    HEX, '\'IBM Plex Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Courier Prime\', \'Courier New\', monospace',
    '\'IBM Plex Mono\', ui-monospace, monospace', ['IBM Plex Sans', 'Courier Prime', 'IBM Plex Mono'],
    corsivo_di=['IBM Plex Sans', 'Courier Prime'], peso_titoli='700', raggio=0, grana=4, titoli_line='1.06',
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
