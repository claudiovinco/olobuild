# -*- coding: utf-8 -*-
"""Genera il tema «Doppio Senso» (id doppio-senso): uno studio di traduttori e interpreti a Trieste, dall'italiano e verso
l'italiano, in sette lingue.

La trovata: il sito va nei due sensi. Nell'apertura lo schermo è diviso in due: a sinistra il video della penna scende,
a destra la città riflessa nell'acqua (capovolta) sale; «Doppio» parte in alto a sinistra, «Senso» in basso a destra, e
a metà dello scorrimento si incontrano sulla stessa riga. Poi in home le due colonne di una sezione scorrono in senso
contrario (il parallasse delle Avanzate sulle colonne: l'italiano scende, l'inglese sale).

Tile poco usate messe alla prova: Chat Hero (mai usata) per una conversazione fra cliente e traduttrice, Pulsante Toggle
per leggere la pagina in inglese (mostra una sezione nascosta), Blend Text in negativo sopra le foto.
Tema scuro: inchiostro, carta, oro, verde acqua, rosso della correzione. Titoli in Cormorant Garamond (col corsivo),
testo in Work Sans. Cursore: doppione (il gemello allo specchio).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'doppio-senso'
NAME = 'Doppio Senso'
CATEGORIA = 'Studi e professioni'

HEX = {
    'primary': '#E8B04B', 'primary_contrast': '#15131A', 'secondary': '#5EC4B6', 'secondary_contrast': '#0B1A18',
    'accent': '#F25F5C', 'dark': '#0C0B10', 'light': '#FFFFFF', 'muted': '#1F1C25', 'muted_contrast': '#EEE8DC',
    'text': '#EEE8DC', 'text_muted': '#ABA49B', 'background': '#15131A', 'border': '#36313E', 'link': '#F0C26A',
}
HOME, SERVIZI, LINGUE, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:lingue}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'ciao@doppiosenso.example', '040 000 2222', 'tel:+390400002222'
LAT, LNG = '45.6519', '13.7713'

V_TASTI, V_CABINA, V_FOLLA, V_PENNA, V_RIFLESSO, V_TRENO = (
    'pexels-video-7581246', 'pexels-video-6951461', 'pexels-video-853946', 'pexels-video-7685212', 'pexels-video-40180706',
    'pexels-video-5210762')
VIDEOS = [V_TASTI, V_CABINA, V_FOLLA, V_PENNA, V_RIFLESSO, V_TRENO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(LIBRO_PENNA, SCRIVANIA_BANDIERE, SCRIVANIA, DIZIONARI, ENCICLOPEDIE, PAGINA, DUE_POLTRONE, DUE_GIOVANI, DUE_UFFICIO,
 APPUNTI_ANTICHI, QUADERNO_NERO, APPUNTI, CUFFIE_MURO, CUFFIA, IRENA, MACCHINA_MENTA, MACCHINA, LETTERE_AEREE, LETTERE,
 PARTENZE, PARTENZE2, AEROPORTO, RIFLESSO_VIA, RIFLESSO_CITTA, RIFLESSO_BN, BIBLIOTECA, SPECCHIO, LETTRICE) = f(
    226612, 40025134, 8369211, 13745412, 16009564, 135129, 2422280, 8518732, 7964543, 34423686, 35522712, 5185091, 8866731,
    19130627, 7681947, 5993641, 4065407, 1921957, 15873606, 2833379, 36681344, 12717154, 18592481, 2476595, 12796422, 256455,
    1061130, 17548877)
PHOTOS = [LIBRO_PENNA, SCRIVANIA_BANDIERE, SCRIVANIA, DIZIONARI, ENCICLOPEDIE, PAGINA, DUE_POLTRONE, DUE_GIOVANI, DUE_UFFICIO,
          APPUNTI_ANTICHI, QUADERNO_NERO, APPUNTI, CUFFIE_MURO, CUFFIA, IRENA, MACCHINA_MENTA, MACCHINA, LETTERE_AEREE, LETTERE,
          PARTENZE, PARTENZE2, AEROPORTO, RIFLESSO_VIA, RIFLESSO_CITTA, RIFLESSO_BN, BIBLIOTECA, SPECCHIO, LETTRICE]
LOGHI = ['doppio-senso-logo-chiaro.png', 'doppio-senso-logo-scuro.png']
MEDIA = set(PHOTOS) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='600', corsivo=True, maiuscole_pulsanti=True, accento_su_media='#E8B04B')
C = K.c
FONT_T = 'Cormorant Garamond'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via San Nicolò 9 · Trieste', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '654/109',
                   'Doppio Senso traduce e interpreta a Trieste dal 1998: dall’italiano e verso l’italiano, in sette lingue, '
                   'per tribunali, aziende, editori e famiglie.',
                   [('I servizi', SERVIZI), ('Le lingue', LINGUE), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Lo studio', 'Lunedì — venerdì 9 — 18<br>Interpreti anche la sera e nei fine settimana', VOCI,
                   '© 2026 Doppio Senso snc · Trieste · P.IVA 00000000000', [('linkedin', 'https://linkedin.com'),
                                                                            ('instagram', 'https://instagram.com')], scuro=True)


# ── L'apertura: le due metà in senso contrario ──
# Tela del computer 1440 × 810. Le due metà sono video alti 140 % (da -20 %): scendendo o salendo di 160 px non scoprono
# mai il fondo. «Doppio» parte in alto a sinistra e scende, «Senso» parte in basso a destra e sale: a metà dello
# scorrimento stanno sulla stessa riga (y 42 %). Gli spostamenti sono in px della tela del computer: sul telefono (tela
# 640 × 1400) 243 px diventano 108, il 7,7 % dell'altezza, e i due titoli partono a 38,3 e 53,7 % per trovarsi al 46 %.
def apertura(t):
    m = K.mob
    scendi = lambda dy, da=0, a=100: {'inizio': 0, 'fine': 100, 'andamento': 'costante',
                                       'tappe': [{'p': da, 'y': 0}, {'p': a, 'y': dy}]}
    incontro = lambda dy: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0}, {'p': 10, 'y': 0}, {'p': 50, 'y': dy},
                                                               {'p': 100, 'y': dy}]}
    poi = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 14}, {'p': da, 'o': 0, 'y': 14},
                                                             {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    via = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': da, 'o': 100}, {'p': a, 'o': 0},
                                                             {'p': 100, 'o': 0}]}
    L = [
        K.livello('ds-sx', tipo='video', videoSrc=K.img(V_PENNA + '.mp4'), videoAutoplay=True, videoMuted=True, videoLoop=True,
                  x=0, y=-20, w=50, h=140, entra='fadeInDown', ritardo=0, durata=900, scrollMove=scendi(160),
                  customCSS='filter:brightness(.72);', responsive={'mobile': {'x': 0, 'y': -12, 'width': 50, 'height': 124}}),
        K.livello('ds-dx', tipo='video', videoSrc=K.img(V_RIFLESSO + '.mp4'), videoAutoplay=True, videoMuted=True,
                  videoLoop=True, x=50, y=-20, w=50, h=140, entra='fadeInUp', ritardo=0, durata=900, scrollMove=scendi(-160),
                  customCSS='filter:brightness(.72);', responsive={'mobile': {'x': 50, 'y': -12, 'width': 50, 'height': 124}}),
        K.livello('ds-filo', tipo='shape', x=49.9, y=0, w=0.2, h=100, bgColor=C['acc'], entra='fadeIn', ritardo=400, durata=800,
                  responsive={'mobile': {'x': 49.8, 'y': 0, 'width': 0.4, 'height': 100}}),
        K.livello('ds-it', contenuto='Italiano', x=4, y=6, w=40, size=13, peso='600', colore=mix(C['chiaro'], 86),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'x': 4, 'y': 4.4, 'width': 42}}),
        K.livello('ds-en', contenuto='English', x=56, y=90, w=40, size=13, peso='600', colore=mix(C['chiaro'], 86),
                  align='right', letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'x': 54, 'y': 94, 'width': 42}}),
        K.livello('ds-t1', contenuto='Doppio', tag='h1', x=4, y=12, w=44, size=150, peso='600', colore=C['chiaro'],
                  align='right', fontFamily=FONT_T, lineHeight=0.9, entra='fadeInDown', ritardo=300, durata=900,
                  scrollMove=incontro(243), responsive={'mobile': {'fontSize': m(52), 'x': 2, 'y': 38.3, 'width': 46}}),
        K.livello('ds-t2', contenuto='Senso', x=52, y=72, w=44, size=150, peso='600', fontStyle='italic', colore=C['acc'],
                  fontFamily=FONT_T, lineHeight=0.9, entra='fadeInUp', ritardo=450, durata=900, scrollMove=incontro(-243),
                  responsive={'mobile': {'fontSize': m(52), 'x': 52, 'y': 53.7, 'width': 46}}),
        K.livello('ds-s1', contenuto='Dall’italiano,', x=4, y=34, w=44, size=24, peso='500', colore=mix(C['chiaro'], 90),
                  align='right', fontStyle='italic', fontFamily=FONT_T, ritardo=600, scrollMove=via(20, 32),
                  responsive={'mobile': {'fontSize': m(15), 'x': 2, 'y': 44.8, 'width': 46}}),
        K.livello('ds-s2', contenuto='into Italian.', x=52, y=64, w=44, size=24, peso='500', colore=mix(C['chiaro'], 90),
                  fontStyle='italic', fontFamily=FONT_T, ritardo=600, scrollMove=via(20, 32),
                  responsive={'mobile': {'fontSize': m(15), 'x': 52, 'y': 50.6, 'width': 46}}),
        K.livello('ds-f', contenuto='Traduciamo nei due sensi, in sette lingue, da Trieste.', x=20, y=62, w=60, size=24,
                  peso='500', colore=C['chiaro'], align='center', lineHeight=1.4, entra='fadeIn', ritardo=0,
                  scrollMove=poi(56, 66), responsive={'mobile': {'fontSize': m(17), 'x': 8, 'y': 56, 'width': 84}}),
        K.livello('ds-b1', tipo='button', contenuto='I servizi', x=37, y=74, w='auto', size=13, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=4, letterSpacing=2, textTransform='uppercase',
                  buttonUrl=SERVIZI, entra='fadeIn', ritardo=0, scrollMove=poi(68, 78), hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'x': 8, 'y': 62, 'fontSize': m(12)}}),
        K.livello('ds-b2', tipo='button', contenuto='Chiedi un preventivo', x=50.5, y=74, w='auto', size=13, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=18, borderRadius=4, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), letterSpacing=2, textTransform='uppercase', buttonUrl=CONTATTI + '#scrivi',
                  entra='fadeIn', ritardo=0, scrollMove=poi(68, 78), hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 44, 'y': 62, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('ds-1', K.sfondo_video(V_TASTI, overlay=0.4, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def contrario(col, dy):
    """La colonna scorre per conto suo mentre la sezione passa: in giù (dy > 0) o in su (dy < 0)."""
    col.setdefault('advanced', {})['parallax'] = {'y': [{'value': -dy, 'position': 0}, {'value': dy, 'position': 100}]}
    return col


def fascia_contraria(t, sx, dx, dy):
    """Le due colonne in senso contrario. La colonna che parte più in alto non deve finire sul titolo: la riga si tiene in
    cima lo spazio della corsa. Sotto i 960 px le colonne vanno una sotto l'altra e il movimento resta: la seconda si
    tiene lo spazio per non salire sopra la prima."""
    dx.setdefault('style', {}).update({'margin_top_tablet': int(abs(dy) * 1.3), 'margin_top_mobile': int(abs(dy) * 1.3)})
    return t.row([contrario(sx, dy), contrario(dx, -dy)], layout='custom', custom='50,50', gap=80, valign='start', anim=None,
                 style={'margin_top': abs(dy)})


def frase(t, lingua, testo, nota, colore):
    return [K.label(t, lingua, color=colore),
            K.headline(t, testo, size=44, tag='p', pad_bottom=8, sizes=(36, 30)),
            K.text(t, nota, size=15, color=C['tenue'], pad=(0, 0, 46, 0))]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Trieste · dal 1998', ['Diciamo la stessa cosa', 'in un’altra lingua.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Un contratto, un romanzo, un’udienza in tribunale, la lettera di una nonna emigrata: '
                                'traduciamo il senso, non le parole. Dall’italiano verso sei lingue, e da sei lingue verso '
                                'l’italiano.', size=17),
                      K.button(t, 'I servizi', SERVIZI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 90}))
    # le due colonne in senso contrario: l'italiano scende, l'inglese sale
    sx = t.col(frase(t, 'Italiano', 'In bocca al lupo.', 'Si risponde «crepi»: guai a dire grazie.', C['acc'])
               + frase(t, 'Italiano', 'Non tutte le ciambelle riescono col buco.', 'Le cose non vengono sempre come vuoi.',
                       C['acc'])
               + frase(t, 'Italiano', 'Avere le mani in pasta.', 'Saperci fare, ed esserci dentro fino al collo.', C['acc']),
               wm='1-2', ws='1-1')
    dx = t.col(frase(t, 'English', 'Break a leg.', 'Said to actors, never to skiers.', C['sec'])
               + frase(t, 'English', 'You can’t win them all.', 'Our doughnut, without the hole.', C['sec'])
               + frase(t, 'English', 'To have a finger in every pie.', 'Pasta for them, pie for us.', C['sec']),
               wm='1-2', ws='1-1')
    out.append(t.section([
        t.one([K.sechead(t, 'Doppio senso', ['Scorri:', 'le lingue vanno al contrario.'], size=56, align='center')]),
        fascia_contraria(t, sx, dx, 120),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 140, 'overflow': 'hidden'}))
    # Chat Hero: una conversazione fra cliente e traduttrice
    out.append(t.section([t.one([t.bare('chathero', {
        'pill_text': 'Una richiesta di martedì mattina', 'pill_dot': True, 'headline_text': 'Ci scrivi alle nove, ',
        'accent_text': 'ti rispondiamo alle nove e dieci.',
        'subhead': 'Niente moduli: ci mandi il documento, ti diciamo quanto costa, quando è pronto e chi lo traduce. Per '
                   'nome.', 'cta1_text': 'Scrivici', 'cta1_url': CONTATTI + '#scrivi', 'cta2_text': 'Le lingue',
        'cta2_url': LINGUE, 'chat_enabled': True, 'chat_label': 'doppio senso · Irena',
        'messages': [{'side': 'you', 'text': 'Buongiorno, mi serve il contratto di vendita in tedesco, giurato in tribunale. '
                      'Venerdì ho il notaio a Graz.'},
                     {'side': 'ai', 'text': 'Buongiorno! Dodici pagine: venerdì alle 12 è pronto e giurato. Sono 34 € a '
                      'pagina, le mando il preventivo fra dieci minuti.'},
                     {'side': 'you', 'text': 'Perfetto. E all’atto serve anche l’interprete?'},
                     {'side': 'ai', 'text': 'Se il notaio lo chiede, vengo io a Graz. Mi dica l’ora.'}],
        'bg_color': C['fondo'], 'panel_color': C['sala'], 'panel2_color': mix(C['sala'], 80, C['nero']), 'accent': C['acc'],
        'accent2': C['sec'], 'accent_on': C['on_acc'], 'text_color': C['testo'], 'sub_color': C['tenue'],
        'msg_text_color': C['testo'], 'pill_color': C['acc'], 'glow_color': mix(C['acc'], 22)})], anim=None)],
        padding='remove-vertical', width='fullbleed'))
    # Blend Text in negativo sopra le foto
    out.append(t.section([t.one([t.classic('blendtext', {
        'text': 'Doppio senso', 'tag': 'div', 'font_size': '170', 'font_size_tablet': '110', 'font_size_mobile': '54',
        'font_weight': '600', 'font_family': 'heading', 'text_transform': 'none', 'letter_spacing': '0', 'line_height': '1',
        'text_align': 'center', 'text_color': C['chiaro'], 'blend_mode': 'difference',
        'tile_padding': {'top': 40, 'right': 20, 'bottom': 40, 'left': 20}}, style={})], anim=None)],
        bg=K.photo_bg(SPECCHIO, overlay=0), padding='xlarge', width='fullbleed',
        style={'padding_top': 220, 'padding_bottom': 220}))
    # Pulsante Toggle: la pagina in inglese, nascosta
    out.append(t.section([t.one([
        K.label(t, 'In English, too', color=C['sec'], align='center'),
        K.text(t, 'Questa pagina c’è anche in inglese: la teniamo piegata qui sotto.', size=17, align='center',
               pad=(8, 0, 18, 0)),
        t.classic('togglebtn', {'text_show': 'Read it in English', 'text_hide': 'Close the English page', 'icon_show': 'languages',
                                'icon_hide': 'chevron-up', 'icon_position': 'left', 'target_id': 'in-english',
                                'initial_state': 'hidden', 'animation': 'collapse', 'duration': '450', 'btn_bg': 'transparent',
                                'btn_color': C['testo'], 'btn_hover_bg': mix(C['chiaro'], 8), 'btn_border_width': '1',
                                'btn_border_color': C['sec'], 'btn_border_radius': '4', 'btn_font_size': '15',
                                'btn_font_weight': '600', 'btn_align': 'center'}, style={})])],
        padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 40}))
    out.append(t.section([t.row([
        t.col([K.headline(t, 'We say the same thing <em>in another language.</em>', size=52, tag='h2', pad_bottom=16),
               K.text(t, 'A contract, a novel, a court hearing, a letter from a grandmother who emigrated: we translate the '
                         'meaning, not the words. From Italian into six languages, and from six languages into Italian. '
                         'Sworn translations for the Trieste court, interpreters for meetings and notaries.', size=17)],
              wm='2-3', ws='1-1'),
        t.col([K.image(t, LETTERE_AEREE, 'Airmail letters on a table', ratio='4/5')], wm='1-3', ws='1-1')],
        layout='custom', custom='62,38', gap=56, valign='center')],
        html_id='in-english', bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(X.recensioni(K, t, [
        ('La sentenza in tedesco era pronta il giovedì, giurata. A Graz il notaio non ha cambiato una virgola.', 'Stefano',
         'Muggia'),
        ('Hanno tradotto le lettere di mio nonno dal croato: abbiamo pianto tutti, in italiano.', 'Marina', 'Trieste'),
        ('Due giorni di fiera a Lubiana con un’interprete che sapeva anche di valvole.', 'Ferruccio', 'Monfalcone')],
        'Chi ci ha scritto', ['Tre lingue,', 'tre cose dette bene.']))
    out.append(K.pannello(t, PARTENZE, 'Da Trieste, in sette lingue', 'Mandaci il documento: <em>il prezzo arriva subito.</em>',
                          'Una foto con il telefono va benissimo. Ti rispondiamo entro un’ora, dal lunedì al venerdì.',
                          'Scrivici', CONTATTI + '#scrivi', effetto=''))
    return out


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Scritto o a voce,', 'nei due sensi.'], 'Traduzioni scritte, traduzioni giurate per il '
                   'tribunale, interpreti in sala e da remoto, revisione dei testi già tradotti.',
                   bg=K.video_bg(V_CABINA, overlay=22), size=104)]
    out.append(t.section([
        t.one([K.sechead(t, 'Quattro servizi', ['Quello che facciamo,', 'e quanto costa.'], size=60)]),
        P.carte(K, t, [(DIZIONARI, 'Traduzioni', 'Scritte', 'Contratti, manuali, siti, cataloghi, romanzi. Da 0,09 € a '
                        'parola.'),
                       (PAGINA, 'Giurate', 'Per il tribunale', 'Asseverate a Trieste, coi bolli: valgono in tutta Europa. '
                        '34 € a pagina.'),
                       (CUFFIA, 'Interpreti', 'In sala e da remoto', 'Consecutiva, simultanea, chuchotage. Da 320 € la mezza '
                        'giornata.'),
                       (APPUNTI, 'Revisione', 'Di un testo tradotto', 'Lo leggiamo come lo leggerà chi lo riceve. 0,04 € a '
                        'parola.')], altezza='420', colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    # un'altra sezione in senso contrario: la richiesta scende, la consegna sale
    sx = t.col(frase(t, 'Ci mandi', 'Il documento.', 'Una foto col telefono, un PDF, un link.', C['acc'])
               + frase(t, 'Ti mandiamo', 'Il preventivo.', 'Entro un’ora, con la data di consegna.', C['acc']), wm='1-2', ws='1-1')
    dx = t.col(frase(t, 'Lo traduce', 'Una persona.', 'Madrelingua della lingua d’arrivo, sempre.', C['sec'])
               + frase(t, 'Lo rilegge', 'Un’altra persona.', 'Chi non l’ha tradotto: vede quello che sfugge.', C['sec']),
               wm='1-2', ws='1-1')
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Quattro mani', 'per ogni pagina.'], size=56, align='center')]),
        fascia_contraria(t, sx, dx, -90),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 130, 'overflow': 'hidden'}))
    out.append(K.domande(t, 'Prima di chiederci', ['Le domande', 'che arrivano più spesso.'], [
        ('Quanto ci vuole?', 'Dieci pagine in tre giorni lavorativi. Se è urgente lo diciamo subito: di solito si può.'),
        ('Che cos’è una traduzione giurata?', 'Una traduzione che il traduttore giura in tribunale: ha valore legale, con la '
                                             'marca da bollo. Serve per certificati, sentenze, atti.'),
        ('Fate anche l’apostille?', 'La chiediamo noi in Procura, se serve. Ci vogliono due giorni in più.')], 'dss'))
    return out


def build_lingue():
    t = Tpl(ID, 'lingue')
    out = [K.intro(t, 'Le lingue', ['Sette lingue,', 'una sola regola.'], 'Si traduce verso la propria lingua madre. Per '
                   'questo siamo in nove: ognuno porta i testi a casa sua.', bg=K.video_bg(V_FOLLA, overlay=22), size=104)]
    out.append(t.section([
        t.one([K.sechead(t, 'Da e verso l’italiano', ['Le coppie', 'di lingue.'], size=60)]),
        P.righe(K, t, [('DE', 'Tedesco', 'Austria e Germania · contratti, tribunali, fiere', 'Irena'),
                       ('SL', 'Sloveno', 'il confine è a dieci minuti · atti, scuole, famiglie', 'Matej'),
                       ('HR', 'Croato', 'lettere, eredità, certificati del mare', 'Ana'),
                       ('EN', 'Inglese', 'manuali, siti, cataloghi, romanzi', 'Paul'),
                       ('FR', 'Francese', 'editoria e cultura', 'Claire'),
                       ('ES', 'Spagnolo', 'turismo e università', 'Lucía')], LINGUE),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([t.one([K.numeri(t, [('7', 'lingue'), ('9', 'traduttori madrelingua'), ('1998', 'il primo contratto'),
                                              ('4', 'interpreti di tribunale')], colonne=4, size=58)])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([t.one([P.citazione(K, t, 'Tradurre è dire quasi la stessa cosa.', 'Umberto Eco, che a Trieste '
                                                   'abbiamo letto in sette lingue')])],
                         padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Mandaci il documento,', 'ti diciamo il prezzo.'], 'Va bene una foto col telefono. '
                   'Rispondiamo entro un’ora, dal lunedì al venerdì dalle 9 alle 18.', bg=K.video_bg(V_TRENO, overlay=22),
                   size=100)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Da e verso', 'lingue', True, opzioni='Italiano → tedesco\nTedesco → italiano\nItaliano → '
                     'sloveno\nSloveno → italiano\nItaliano → inglese\nInglese → italiano\nAltre lingue'),
             K.campo('select', 'Il servizio', 'servizio', True, opzioni='Traduzione scritta\nTraduzione giurata\nInterprete\n'
                     'Revisione'),
             K.campo('textarea', 'Che cosa c’è da tradurre', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivici', ['Il preventivo', 'entro un’ora.'], 'Allega il documento nella '
                                'mail di risposta: ti scriviamo noi per primi.', VOCI, campi, 'Doppio Senso — preventivo',
                                'Invia', 'Grazie! Ti scriviamo entro un’ora.'))
    out.append(X.zone(K, t, 'Lo studio', ['In via San Nicolò,', 'dietro la Borsa.'], 'Al primo piano, il portone accanto alla '
                      'libreria. A cinque minuti dal tribunale.', [('A piedi', 'da piazza Unità, 4 minuti'),
                      ('Treno', 'dalla stazione centrale, 12 minuti'), ('Tribunale', 'cinque minuti, per i giuramenti'),
                      ('Telefono', PHONE)], LAT, LNG, 'Doppio Senso · via San Nicolò 9', True, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · lost in translation', color=C['sec'], align='center'),
                       K.headline(t, 'Questa pagina <em>non si traduce.</em>', size=88, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc']),
                       K.text(t, 'In nessuna delle sette lingue. Torna alla home: lì ci capiamo.', size=17, align='center',
                              color=mix(C['chiaro'], 88)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_RIFLESSO, overlay=30), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito nei due sensi',
    ['traduzioni', 'interpreti', 'studio', 'trieste', 'lingue', 'scuro', 'slider pro', 'scorrimento'],
    'Uno studio di traduttori a Trieste: nell’apertura lo schermo è diviso in due, a sinistra la penna scende, a destra la '
    'città riflessa sale, e «Doppio» e «Senso» si incontrano a metà strada. Poi le due colonne che scorrono in senso '
    'contrario, l’italiano giù e l’inglese su, una conversazione con la traduttrice, la scritta in negativo sopra lo '
    'specchio e la pagina in inglese piegata sotto un pulsante.',
    [('I servizi', SERVIZI), ('Le lingue', LINGUE), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('servizi', 'I servizi', 'servizi'), ('lingue', 'Le lingue', 'lingue'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cormorant Garamond\', Georgia, serif',
    'ui-monospace, Menlo, monospace', ['Work Sans', 'Cormorant Garamond'], corsivo_di=['Cormorant Garamond'],
    peso_titoli='600', raggio=4, grana=4, titoli_line='0.98', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'doppione'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['text']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'grazie'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'lingue.json': build_lingue(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
