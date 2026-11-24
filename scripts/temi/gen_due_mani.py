# -*- coding: utf-8 -*-
"""Genera il tema «Due Mani» (id due-mani): imbianchini a Bologna — pareti, soffitti, facciate, stucchi e smalti, le
tinte scelte coi campioni sul muro; la casa coperta e pulita alla fine di ogni giorno.

La trovata: «due mani». Scorrendo, un rullo alto quanto lo schermo attraversa l'apertura e dà la prima mano: il colore
copre il video ma lo si intravede ancora, come un muro vecchio sotto una mano sola. Poi torna indietro con la seconda, e
la parete è piena. Quattro barattoli in basso cambiano la tinta in ogni momento (lo stato «tinta» dello Slider Pro, col
suo colore nella variabile --mps-st-tinta: il rullo e le due mani la usano).

Tema chiaro, caldo: carta, terracotta, salvia e ocra. Titoli in Fraunces, testo in Work Sans, dati in Spline Sans Mono.
Cursore: rullo.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'due-mani'
NAME = 'Due Mani'

HEX = {
    'primary': '#A84A28', 'primary_contrast': '#FFFFFF', 'secondary': '#4F6B53', 'secondary_contrast': '#FFFFFF',
    'accent': '#D9A13B', 'dark': '#1F1B16', 'light': '#FFFFFF', 'muted': '#F1EBE1', 'muted_contrast': '#1F1B16',
    'text': '#1F1B16', 'text_muted': '#665C52', 'background': '#FBF8F3', 'border': '#E2D8CA', 'link': '#A84A28',
}
TINTE = [('terracotta', 'Terracotta', '#A84A28'), ('salvia', 'Salvia', '#4F6B53'), ('notte', 'Blu notte', '#2B4560'),
         ('ocra', 'Ocra bruciata', '#8A621A')]
TINTA = 'var(--mps-st-tinta, #A84A28)'
HOME, LAVORI, COLORI, PREVENTIVO = '{{PAGINA:home}}', '{{PAGINA:lavori}}', '{{PAGINA:colori}}', '{{PAGINA:preventivo}}'
MAIL, PHONE, PHONE_TEL = 'ciao@duemani.example', '051 000 2222', 'tel:+390510002222'
LAT, LNG = '44.4949', '11.3426'

V_RULLO, V_DONNA, V_TERRACOTTA, V_VASCHETTA, V_PITTRICE, V_SPRUZZO = (
    'pexels-video-6473946', 'pexels-video-9823147', 'pexels-video-5799490', 'pexels-video-6474155', 'pexels-video-6474260',
    'pexels-video-6474085')
VIDEOS = [V_RULLO, V_DONNA, V_TERRACOTTA, V_VASCHETTA, V_PITTRICE, V_SPRUZZO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(SCALA_BIANCO, COPPIA_PITTORI, MURO_ROSSO, TUTE_BIANCHE, ANZIANO_FACCIATA, RULLO_GIALLO, RULLO_GRIGIO, RULLO_SCURO,
 STRISCIA_BIANCA, SCALA_ARANCIO, RULLI_LEGNO, BARATTOLO, BARATTOLI, MURO_GIALLO, MURO_BLU, STUCCO_ROSA, MURO_VERDE, SALA_BLU,
 MURO_ARANCIO, SEDIE, STANZA_VUOTA, TUTA_GRIGIA) = f(
    7218683, 36153946, 16734519, 5493662, 31544574, 5583126, 1669754, 994164, 10533141, 5798984, 5853925, 15013713, 1887946,
    5137779, 40088359, 39836710, 8239800, 7166648, 5799059, 28158034, 7031599, 6474341)
PHOTOS = [SCALA_BIANCO, COPPIA_PITTORI, MURO_ROSSO, TUTE_BIANCHE, ANZIANO_FACCIATA, RULLO_GIALLO, RULLO_GRIGIO, RULLO_SCURO,
          STRISCIA_BIANCA, SCALA_ARANCIO, RULLI_LEGNO, BARATTOLO, BARATTOLI, MURO_GIALLO, MURO_BLU, STUCCO_ROSA, MURO_VERDE,
          SALA_BLU, MURO_ARANCIO, SEDIE, STANZA_VUOTA, TUTA_GRIGIA]
DISEGNI = ['duemani-staffa.svg', 'duemani-pelo.svg', 'duemani-trama.svg']
LOGHI = ['due-mani-logo-chiaro.png', 'due-mani-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#F0C07A')
C = K.c
CHIARO = C['chiaro']
FONT_T = 'Fraunces'
VOCI = [('map-pin', 'Via del Pratello 40 · Bologna', PREVENTIVO + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 190, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '569/114', 'Due Mani: imbianchini a Bologna dal 2006. Pareti, soffitti, '
                   'facciate, stucchi e smalti. Due mani sempre, e la casa pulita la sera.',
                   [('I lavori', LAVORI), ('I colori', COLORI), ('Preventivo', PREVENTIVO), ('Privacy', '/privacy-policy/')],
                   'Il laboratorio', 'Lunedì — venerdì 8 — 18<br>I campioni di colore si ritirano qui', VOCI,
                   '© 2026 Due Mani · Bologna · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                    ('pinterest', 'https://pinterest.com')], scuro=False)


# ── L'apertura: due mani di colore ──
def apertura(t):
    m = K.mob
    mano1 = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'sx': 0}, {'p': 8, 'sx': 0}, {'p': 44, 'sx': 1}, {'p': 100, 'sx': 1}]}
    mano2 = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'sx': 0}, {'p': 52, 'sx': 0}, {'p': 88, 'sx': 1}, {'p': 100, 'sx': 1}]}
    # il rullo largo 7 % della tela: il suo centro (50,4 unità) sta sul bordo della vernice; va a destra con la prima
    # mano, torna a sinistra con la seconda ed esce
    giro = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': -150, 'o': 100}, {'p': 8, 'x': -50}, {'p': 44, 'x': 1390},
                                               {'p': 52, 'x': 1390}, {'p': 88, 'x': -50}, {'p': 94, 'x': -260, 'o': 100},
                                               {'p': 96, 'x': -260, 'o': 0}, {'p': 100, 'x': -260, 'o': 0}]}
    via = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 7, 'o': 100}, {'p': 20, 'o': 0}, {'p': 100, 'o': 0}]}
    arriva = lambda da: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 30}, {'p': da, 'o': 0, 'y': 30},
                                                              {'p': da + 8, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    tinta_css = 'transition:background-color .8s ease;'
    L = K.veli('dm-velo', 'basso', 30, 56, colore=C['nero'], forza=84, mobile_da=46)
    L += [
        # prima: la stanza da dipingere e la promessa
        K.livello('dm-o0', contenuto='Due Mani · imbianchini · Bologna', x=6, y=44, w=40, size=13, peso='500', colore=mix(CHIARO, 85),
                  letterSpacing=3, textTransform='uppercase', fontFamily='Spline Sans Mono', ritardo=200, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 48, 'width': 88}}),
        K.livello('dm-t0', contenuto='Le pareti di casa,', tag='h1', x=6, y=48, w=60, size=84, peso='600', colore=CHIARO,
                  fontFamily=FONT_T, lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=900, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(38), 'x': 6, 'y': 51, 'width': 88}}),
        K.livello('dm-t0b', contenuto='come nuove.', tag='p', x=6, y=59, w=60, size=84, peso='600', fontStyle='italic', colore='#F0C07A',
                  fontFamily=FONT_T, lineHeight=1.0, entra='maskFromBottom', ritardo=450, durata=900, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(38), 'x': 6, 'y': 59, 'width': 88}}),
        K.livello('dm-h0', contenuto='Scorrete: diamo la prima mano ↓', x=6, y=71, w=40, size=16, peso='500', colore=mix(CHIARO, 92),
                  ritardo=800, scrollMove=via, responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 67, 'width': 88}}),
        # la prima mano lascia intravedere il muro di prima, la seconda copre; il colore arriva solo dove arriva il rullo
        # (y -4…86, come il cilindro: l'utente, 13 nov 2026, «non deve colorare nella parte sotto, dove il rullo non arriva»)
        K.livello('dm-m1', tipo='shape', bgColor=TINTA, opacity=72, x=0, y=-4, w=100, h=90, entra='fadeIn', ritardo=0, durata=300,
                  customCSS='transform-origin:0% 50%;' + tinta_css, scrollMove=mano1,
                  responsive={'mobile': {'x': 0, 'y': -4, 'width': 100, 'height': 90}}),
        K.livello('dm-r1', tipo='image', imageSrc=K.img(DISEGNI[2]), imageAlt='', objectFit='fill', x=0, y=-4, w=100, h=90,
                  entra='fadeIn', ritardo=0, durata=300, customCSS='transform-origin:0% 50%;', scrollMove=mano1,
                  responsive={'mobile': {'x': 0, 'y': -4, 'width': 100, 'height': 90}}),
        K.livello('dm-m2', tipo='shape', bgColor=TINTA, x=0, y=-4, w=100, h=90, entra='fadeIn', ritardo=0, durata=300,
                  customCSS='transform-origin:100% 50%;' + tinta_css, scrollMove=mano2,
                  responsive={'mobile': {'x': 0, 'y': -4, 'width': 100, 'height': 90}}),
        K.livello('dm-r2', tipo='image', imageSrc=K.img(DISEGNI[2]), imageAlt='', objectFit='fill', x=0, y=-4, w=100, h=90,
                  entra='fadeIn', ritardo=0, durata=300, customCSS='transform-origin:100% 50%;', scrollMove=mano2,
                  responsive={'mobile': {'x': 0, 'y': -4, 'width': 100, 'height': 90}}),
        # il rullo: il cilindro della tinta scelta, il pelo, la staffa col manico
        K.livello('dm-cil', tipo='shape', bgColor=TINTA, borderRadius=26, x=0, y=-4, w=7, h=90, entra='fadeIn', ritardo=0, durata=300,
                  customCSS='box-shadow:inset -10px 0 18px rgba(0,0,0,.28),inset 8px 0 14px rgba(255,255,255,.18);' + tinta_css,
                  scrollMove=giro, responsive={'mobile': {'x': 0, 'y': -4, 'width': 7, 'height': 90}}),
        K.livello('dm-pelo', tipo='image', imageSrc=K.img(DISEGNI[1]), imageAlt='', objectFit='fill', borderRadius=26, x=0, y=-4, w=7,
                  h=90, entra='fadeIn', ritardo=0, durata=300, opacity=70, scrollMove=giro,
                  responsive={'mobile': {'x': 0, 'y': -4, 'width': 7, 'height': 90}}),
        K.livello('dm-staffa', tipo='image', imageSrc=K.img(DISEGNI[0]), imageAlt='', objectFit='contain', x=2.5, y=85.1, w=20, h=35.5,
                  entra='fadeIn', ritardo=0, durata=300, scrollMove=giro,
                  responsive={'mobile': {'x': 1.25, 'y': 85.5, 'width': 45, 'height': 20.6}}),
        # dopo: sulla parete finita
        K.livello('dm-o1', contenuto='La prima copre, la seconda finisce', x=8, y=24, w=50, size=13, peso='500', colore=mix(CHIARO, 88),
                  letterSpacing=3, textTransform='uppercase', fontFamily='Spline Sans Mono', entra='fadeIn', ritardo=0, scrollMove=arriva(86),
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 16, 'width': 88}}),
        K.livello('dm-t1', contenuto='Due mani.', tag='h2', x=8, y=28, w=60, size=150, peso='600', colore=CHIARO, fontFamily=FONT_T,
                  lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=arriva(88),
                  responsive={'mobile': {'fontSize': m(48), 'x': 6, 'y': 19, 'width': 88}}),
        K.livello('dm-s1', contenuto='Pareti, soffitti, facciate e stucchi a Bologna. Copriamo, dipingiamo, puliamo: la sera la '
                                     'casa è di nuovo vostra.', x=8, y=50, w=38, size=19, peso='400', colore=mix(CHIARO, 94),
                  lineHeight=1.55, entra='fadeIn', ritardo=0, scrollMove=arriva(90),
                  responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 27, 'width': 86}}),
        K.livello('dm-b1', tipo='button', contenuto='Un preventivo', x=8, y=63, w='auto', size=15, peso='600', colore='#1F1B16',
                  bgColor='#FFFFFF', padding=18, borderRadius=4, buttonUrl=PREVENTIVO, entra='fadeIn', ritardo=0, scrollMove=arriva(91),
                  hover={'bgColor': '#F0C07A', 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 38, 'fontSize': m(13)}}),
        K.livello('dm-b2', tipo='button', contenuto='I lavori', x=21.5, y=63, w='auto', size=15, peso='600', colore=CHIARO,
                  bgColor='transparent', padding=18, borderRadius=4, borderWidth=1, borderColor=mix(CHIARO, 70), buttonUrl=LAVORI,
                  entra='fadeIn', ritardo=0, scrollMove=arriva(91), hover={'bgColor': mix(CHIARO, 16), 'duration': 250},
                  responsive={'mobile': {'x': 52, 'y': 38, 'fontSize': m(13)}}),
        # i barattoli: la tinta si cambia quando si vuole
        K.livello('dm-tinte', contenuto='La tinta', x=8, y=81, w=20, size=12, peso='600', colore=CHIARO, letterSpacing=2,
                  textTransform='uppercase', fontFamily='Spline Sans Mono', ritardo=900,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 78, 'width': 60}}),
    ]
    for k, (val, nome, hx) in enumerate(TINTE):
        L.append(K.livello('dm-c%d' % k, tipo='shape', bgColor=hx, borderRadius=999, borderWidth=3, borderColor='#FFFFFF',
                           x=8 + k * 4.4, y=85, w=3.4, h=6.04, entra='zoomIn', ritardo=1000 + k * 90, durata=500,
                           action={'type': 'setState', 'target': 'tinta', 'value': val, 'color': hx, 'label': 'Tinta ' + nome},
                           boxShadow={'x': 0, 'y': 6, 'blur': 18, 'spread': 0, 'color': 'rgba(0,0,0,.35)'},  # contorno: bianco di serie
                           hover={'scale': 1.12, 'duration': 200},
                           responsive={'mobile': {'x': 6 + k * 13, 'y': 80.5, 'width': 10, 'height': 4.57}}))
    M.veli_oltre(L)
    sl = [K.diapositiva('dm-1', K.sfondo_video(V_RULLO, overlay=0.14, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1900, scrollStep=False, stati='tinta=terracotta',
                       heightMobile={'mode': 'px', 'value': 1400})


def tavolozza(t, link):
    """La tavolozza delle tinte (Hover list coi campioni)."""
    s = P.elenco_hover(K, t, [
        ('Terracotta', 'calda, da salotto', 'Il colore dei coppi di Bologna: con la luce di sera diventa rame.', MURO_ARANCIO),
        ('Salvia', 'calma, da camera', 'Un verde grigio che sta bene col legno e col bianco dei soffitti.', MURO_VERDE),
        ('Blu notte', 'profondo, da studio', 'Fa sembrare la stanza più grande, se il soffitto resta bianco.', SALA_BLU),
        ('Ocra bruciata', 'luminosa, da cucina', 'La terra gialla dei portici: con la calce prende la luce.', MURO_GIALLO),
        ('Calce bianca', 'per tutto', 'Il bianco che respira: niente muffa nei bagni e negli angoli freddi.', STANZA_VUOTA),
    ], link, size=48)
    voci = s['settings']['items']
    for v, hx in zip(voci, ['#A84A28', '#4F6B53', '#2B4560', '#8A621A', '#EDE8DF']):
        v['color'] = hx
    s['settings'].update({'lead_mode': 'swatch', 'swatch_size': 26, 'swatch_shape': 'circle'})
    return s


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Due Mani · Bologna, dal 2006', ['Una mano copre,', 'la seconda finisce.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo in sette. Copriamo mobili e pavimenti, stucchiamo le crepe, diamo sempre due mani e '
                                'la sera puliamo: il giorno dopo ritrovate casa vostra, solo di un altro colore.', size=17),
                      K.button(t, 'I lavori', LAVORI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('paint-roller', 'Due mani <b>sempre</b>'), ('shield-check', 'Mobili <b>coperti</b>'),
                                ('sparkles', 'Casa pulita <b>la sera</b>'), ('palette', 'Campioni <b>sul vostro muro</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(X.prima_dopo(K, t, [
        (STANZA_VUOTA, SALA_BLU, 'Prima', 'Dopo', 'Lo studio di via Saragozza', 'Pareti in blu notte e il soffitto bianco: la stanza '
         'sembra più alta di mezzo metro.'),
        (SCALA_BIANCO, MURO_ARANCIO, 'Prima', 'Dopo', 'Il salotto del Pratello', 'Terracotta su tre pareti, la quarta in calce: '
         'due mani e un giorno.')],
        'Prima e dopo', ['Trascinate', 'la riga.']))
    out.append(t.section([
        t.one([K.sechead(t, 'I colori', ['Cinque tinte', 'per cominciare.'], tagline='Passate sulle righe: si affaccia una stanza '
                         'dipinta così. I campioni veri li stendiamo sul vostro muro.', size=60)]),
        t.one([tavolozza(t, COLORI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_TERRACOTTA, 'Il campione', 'Prima <em>sul vostro muro.</em>',
                             'Un metro quadro di colore, steso dove andrà: lo guardate di giorno e di sera, poi decidete.', overlay=18))
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Quattro cose', 'che facciamo sempre.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'Copriamo', 'Pavimenti, mobili, prese: teli e nastro, prima di aprire un barattolo.'),
                              ('02', 'Stucchiamo', 'Le crepe si aprono, si riempiono e si carteggiano: il muro torna dritto.'),
                              ('03', 'Due mani', 'Sempre due, anche quando la prima sembra bastare. Si vede dopo un anno.'),
                              ('04', 'Puliamo', 'La sera teli via, pavimento lavato: casa vostra, di un altro colore.')],
                       numero_font='var(--olo-font-family-mono)')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(X.recensioni(K, t, [
        ('Hanno dipinto casa con noi dentro, due stanze al giorno. La sera non c’era un granello di polvere.', 'Chiara', 'Bologna, Saragozza'),
        ('Ho cambiato idea sul colore dopo il campione sul muro. Nessuno ha sbuffato: ne hanno steso un altro.', 'Paolo', 'San Lazzaro'),
        ('La facciata della casa di campagna, coi ponteggi e i permessi. Il geometra non ha dovuto chiamare nessuno.', 'Elena', 'Casalecchio')],
        'Chi ci ha chiamato', ['Tre case,', 'tre colori.']))
    return out


def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [K.intro(t, 'I lavori', ['Pareti, soffitti,', 'facciate.'],
                   'Appartamenti abitati, negozi da riaprire il lunedì, case di campagna coi ponteggi: alcuni lavori di quest’anno.',
                   bg=K.video_bg(V_DONNA, overlay=26), size=96)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Interni', 'Le pareti', 'di casa', 'Due mani di idropittura lavabile, gli stucchi, gli angoli dritti. Due stanze al giorno.',
         SCALA_BIANCO),
        ('Soffitti', 'I soffitti', 'senza ombre', 'Rullo lungo e luce radente: il soffitto si vede solo quando è fatto male.', RULLO_GRIGIO),
        ('Decorativi', 'Gli stucchi', 'veneziani', 'Marmorino e stucco lucido, a spatola, per un ingresso o un bagno.', STUCCO_ROSA),
        ('Esterni', 'Le facciate', 'e le persiane', 'Ponteggi, permessi, silicati: la facciata dura vent’anni.', ANZIANO_FACCIATA)],
        altezza=420)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In cantiere', ['Le mani', 'che lo fanno.'], size=56)]),
        P.fila_foto(K, t, [(COPPIA_PITTORI, 'Due pittori al lavoro'), (MURO_ROSSO, 'Una parete grande sul ponteggio'),
                           (TUTE_BIANCHE, 'Le tute bianche'), (SCALA_ARANCIO, 'La scala e la parete arancio')], ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_SPRUZZO, 'I negozi', 'Chiusi il sabato, <em>aperti il lunedì.</em>',
                          'Per i negozi lavoriamo nel fine settimana, anche di notte: il lunedì si riapre col colore nuovo.',
                          'Un preventivo', PREVENTIVO + '#scrivi', video=True))
    return out


def build_colori():
    t = Tpl(ID, 'colori')
    out = [K.intro(t, 'I colori', ['Scegliere', 'senza sbagliare.'],
                   'Le tinte che usiamo di più, come si comportano con la luce, e il campione sul muro prima di decidere.',
                   bg=K.photo_bg(BARATTOLI, position='center 40%'), size=96)]
    out.append(t.section([t.one([tavolozza(t, PREVENTIVO + '#scrivi')])], padding='large', width='large',
                         style={'padding_top': 110, 'padding_bottom': 100}))
    gratta = t.classic('scratchfx', {
        'image': K.img(MURO_VERDE), 'object_fit': 'cover', 'object_position': 'center center', 'prize_eyebrow': 'Sotto il bianco',
        'prize_title': 'Il salvia della camera', 'prize_text': 'Grattate il muro: sotto c’è il colore. Il campione vero, un metro '
                       'quadro, lo stendiamo da voi gratis.', 'coupon_code': 'CAMPIONE', 'copy_label': 'Copia il codice',
        'remember': False, 'text_color': CHIARO, 'under_bg': C['sec'], 'hint': 'Grattate qui sopra', 'show_button': True,
        'reveal_label': 'Scopri il colore', 'cover_type': 'solid', 'cover_color': '#EDE8DF', 'cover_color2': '#D9D2C6',
        'cover_text': 'GRATTATE IL MURO', 'cover_text_color': C['tenue'], 'brush_size': 44, 'reveal_threshold': 55,
        'aspect': '16/9', 'height_mode': 'aspect', 'align': 'left', 'max_width': 760,
        'border_radius': {'tl': 4, 'tr': 4, 'br': 4, 'bl': 4}, 'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True,
                                                                         'style': 'solid', 'color': ''}})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il campione', ['Prima di decidere,', 'grattate.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Un colore sul catalogo non è mai quello sul muro: la luce di casa lo cambia. Per questo il campione '
                                'lo stendiamo da voi, e lo guardate un giorno intero.')], wm='2-5', ws='1-1'),
               t.col([gratta], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Sui colori,', 'le domande di tutti.', [
        ('Un colore scuro rimpicciolisce la stanza?', 'Non sempre: un blu notte su una parete sola, col soffitto bianco, la fa sembrare '
         'più profonda. Il campione lo dice prima.'),
        ('Che pittura usate in bagno e in cucina?', 'Lavabile antimuffa, o calce: respira e la muffa non attacca.'),
        ('Si può dipingere sopra la carta da parati?', 'Meglio di no: la togliamo, stucchiamo e poi dipingiamo. Dura il doppio.'),
    ], 'dm-faq', bg=False))
    return out


def build_preventivo():
    t = Tpl(ID, 'preventivo')
    out = [K.intro(t, 'Il preventivo', ['Quante stanze,', 'quanto costa.'],
                   'Componete il lavoro: il totale è indicativo. Poi veniamo a misurare e vi lasciamo il prezzo scritto.',
                   bg=K.video_bg(V_PITTRICE, overlay=26), size=96)]
    conf = X.preventivo(K, t, [
        ('casa', 'La casa', 'uno', True, 'Pareti e soffitti, due mani di idropittura lavabile, stucchi e pulizia compresi.'),
        ('finitura', 'La finitura', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('bilo', 'casa', 'Bilocale', 'Fino a 55 m².', 1450, '', True),
        ('trilo', 'casa', 'Trilocale', 'Da 55 a 90 m².', 2300, 'il più chiesto', False),
        ('grande', 'casa', 'Casa grande', 'Oltre 90 m².', 3400, '', False),
        ('bianco', 'finitura', 'Bianco', 'Pareti e soffitti bianchi.', 0, '', True),
        ('colori', 'finitura', 'Pareti colorate', 'Fino a tre tinte, soffitti bianchi.', 380, '', False),
        ('calce', 'finitura', 'Calce', 'Traspirante, antimuffa.', 620, '', False),
        ('stucco', 'extra', 'Stucco veneziano', 'Una parete d’ingresso, a spatola.', 540, '', False),
        ('porte', 'extra', 'Smalto delle porte', 'Fino a sei porte, coi telai.', 690, '', False),
        ('mobili', 'extra', 'Spostare i mobili', 'Li spostiamo e li rimettiamo noi.', 180, '', False)],
        'Il vostro lavoro', PREVENTIVO + '?lavoro={configurazione}#scrivi', 'IVA compresa · sopralluogo gratis')
    out.append(X.sezione_preventivo(K, t, 'Componete il lavoro', ['Scegliete,', 'il totale si fa da sé.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Quartiere o comune', 'comune'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Le stanze, i colori', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Veniamo a misurare,', 'gratis.'],
                                'Vi richiamiamo entro due giorni; il campione di colore lo portiamo noi.', VOCI, campi,
                                'Due Mani — preventivo', 'Chiedi il sopralluogo', 'Grazie! Vi richiamiamo entro due giorni.'))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi viene da voi', ['Sette pittori,', 'tre li conoscete qui.'], size=56)]),
        K.squadra(t, [(SCALA_BIANCO, 'Irene Calzolari', 'Pittrice', 'Interni e campioni: è lei che stende il metro quadro sul muro.',
                       'center 30%'),
                      (ANZIANO_FACCIATA, 'Walter Grandi', 'Fondatore', 'Facciate e persiane: quarant’anni di ponteggi.', 'center 30%'),
                      (TUTA_GRIGIA, 'Samuele Rossi', 'Decorativi', 'Stucchi, marmorini e smalti, a spatola e a pennello.', 'center 30%')],
                  ratio='4/5', size='320'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.zone(K, t, 'Dove lavoriamo', ['Bologna', 'e dintorni.'], 'I comuni dove andiamo ogni settimana.',
                      [('Bologna', 'tutti i quartieri'), ('Casalecchio, Zola', 'ogni settimana'), ('San Lazzaro, Ozzano', 'ogni settimana'),
                       ('Castel Maggiore', 'ogni settimana'), ('Colli bolognesi', 'su appuntamento'), ('Imola', 'per le facciate')],
                      LAT, LNG, 'Due Mani · il laboratorio', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · parete bianca', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui manca <em>una mano.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=CHIARO, accento=K.acc_media),
                       K.text(t, 'Questa pagina non l’abbiamo ancora dipinta. Torniamo dall’inizio.', size=17, align='center',
                              color=mix(CHIARO, 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_VASCHETTA, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'La prima mano, la seconda',
    ['artigiani', 'imbianchino', 'pittore edile', 'colori', 'chiaro', 'slider pro', 'scorrimento', 'interattivo'],
    'Imbianchini a Bologna: scorrendo un rullo alto quanto lo schermo dà la prima mano sull’apertura (sotto si intravede '
    'ancora il muro) e poi la seconda, che copre tutto; quattro barattoli cambiano la tinta in ogni momento. Poi i lavori '
    'prima e dopo, le tinte da guardare e da grattare, il preventivo da comporre.',
    [('I lavori', LAVORI), ('I colori', COLORI), ('Preventivo', PREVENTIVO)],
    [('home', 'Home', None), ('lavori', 'I lavori', 'lavori'), ('colori', 'I colori', 'colori'), ('preventivo', 'Preventivo', 'preventivo')],
    HEX, '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Fraunces\', Georgia, serif',
    '\'Spline Sans Mono\', ui-monospace, Menlo, monospace', ['Work Sans', 'Fraunces', 'Spline Sans Mono'], corsivo_di=['Fraunces'],
    peso_titoli='600', raggio=4, grana=4, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'rullo'
THEME['cursor']['ring_color'] = '#3A3530'
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'lavori.json': build_lavori(), 'colori.json': build_colori(), 'preventivo.json': build_preventivo(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
