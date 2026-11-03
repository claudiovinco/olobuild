# -*- coding: utf-8 -*-
"""Genera il tema «Ventaglio» (id ventaglio): una sartoria di Palermo che cuce abiti d'estate in lino e seta stampata, e
regala con ogni abito un ventaglio della stessa stoffa.

La trovata: «il ventaglio che si apre». In apertura, sopra il mare di Cefalù, c'è un ventaglio chiuso: scorrendo le sue
nove stecche, ognuna la foto di un abito, si aprono a mezzaluna attorno al perno (livelli dello Slider Pro che ruotano
attorno al punto in basso) e mostrano tutta la collezione. Più giù le stoffe in una galleria a ventaglio 3D (Galleria pro,
coverflow).

Tema chiaro: sabbia, corallo, verde fico d'India e giallo limone. Titoli in Yeseva One, testo in Jost, dati in Spline Sans
Mono. Logo: il ventaglio e il nome. Cursore: ventaglio (si apre sui link).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'ventaglio'
NAME = 'Ventaglio'

HEX = {
    'primary': '#B03A26', 'primary_contrast': '#FFFFFF', 'secondary': '#2F7A6A', 'secondary_contrast': '#FFFFFF',
    'accent': '#E8A33D', 'dark': '#1F120D', 'light': '#FFFFFF', 'muted': '#F2E4D4', 'muted_contrast': '#2A1913',
    'text': '#2A1913', 'text_muted': '#6E5448', 'background': '#FBF3EA', 'border': '#E2CDB8', 'link': '#B03A26',
}

HOME, CAPI, SARTORIA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:la-sartoria}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'sartoria@ventaglio.example', '+39 091 000 4473', 'tel:+390910004473'
LAT, LNG = '38.1160', '13.3640'

V_PRATO, V_ROSSO, V_BIANCO, V_SPIAGGIA, V_CEFALU, V_CEFALU2, V_ACQUA, V_SETA, V_MANO = (
    'pexels-video-8530705', 'pexels-video-34528355', 'pexels-video-9186997', 'pexels-video-7320540', 'pexels-video-7703563',
    'pexels-video-7703370', 'pexels-video-25811859', 'pexels-video-7677187', 'pexels-video-8478003')
VIDEOS = [V_PRATO, V_ROSSO, V_BIANCO, V_SPIAGGIA, V_CEFALU, V_CEFALU2, V_ACQUA, V_SETA, V_MANO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
VENT = f('6080959', '8143324', '39581238', '14297891')
ABIT = f('11425100', '36659579', '934698', '28292602', '26050608', '31649718')
FIOR = f('29277214', '38808677', '30603525', '30873757', '7509903', '29421662')
SICI = f('7555339', '35817977', '18453312', '18495537', '35786541', '19260575')
SETA = f('7956629', '7676887', '4938326', '13284489')
DONN = f('10293869', '21904517', '34277490', '20424188')
LINO = f('7789140', '8053683', '18444202', '38270321', '7789137', '10554488')
TERR = f('18385513', '11491324', '9464804', '12940806', '4916255')
SAND = f('23495502', '5618939', '33135578', '30979923', '9532741')
MERC = f('18525765', '5801963', '23025213', '20000379')
LOGO_S, LOGO_C = 'logo-ventaglio-scuro.png', 'logo-ventaglio-chiaro.png'
PHOTOS = VENT + ABIT + FIOR + SICI + SETA + DONN + LINO + TERR + SAND + MERC
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#F6C77A')
C = K.c
MONO = 'Spline Sans Mono'
VOCI = [('map-pin', 'Via Alloro 41 · Palermo', NEGOZI + '#sartoria'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Ventaglio', LOGO_S, 210, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '1244/224', 'Ventaglio cuce a Palermo abiti d’estate in lino e seta stampata, e con ogni abito '
                   'regala un ventaglio della stessa stoffa.',
                   [('La collezione', CAPI), ('La sartoria', SARTORIA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La sartoria', 'Via Alloro 41, Palermo<br>Lunedì — sabato 9:30 — 13:30, 16 — 20', VOCI,
                   '© 2026 Ventaglio · Palermo · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')],
                   larghezza=480, fondo=C['sala'])


# ── L'apertura: il ventaglio che si apre ──
def apertura(t):
    m = K.mob
    foto = [ABIT[0], FIOR[0], LINO[0], ABIT[3], FIOR[2], ABIT[2], LINO[3], FIOR[4], ABIT[5]]
    livelli = K.veli('ve-velo', 'sinistra', 32, 54, colore=C['nero'], forza=80, mobile_da=0, mobile_alto=24)
    for k, img in enumerate(foto):
        ang = -56 + k * 14
        da = 10 + abs(k - 4) * 3
        livelli.append(K.livello(
            've-s%d' % k, tipo='image', imageSrc=K.img(img), objectFit='cover', objectPosition='center 30%', x=69.75, y=36, w=6.5,
            h=56, borderRadius=48, entra='fadeInUp', ritardo=150 + abs(k - 4) * 60, durata=700,
            customCSS='transform-origin:50% 100%;box-shadow:0 10px 26px rgba(0,0,0,.35);outline:3px solid var(--olo-color-background);',
            scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': 0}, {'p': da, 'r': 0}, {'p': 58, 'r': ang}, {'p': 100, 'r': ang}]},
            responsive={'mobile': {'x': 44.5, 'y': 46, 'width': 11, 'height': 24}}))
    livelli.append(K.livello('ve-perno', tipo='shape', x=72.3, y=90.2, w=1.4, h=2.5, bgColor=C['acc'], borderRadius=999, entra='fadeIn',
                             ritardo=600, durata=500, customCSS='box-shadow:0 0 0 3px var(--olo-color-background);',
                             responsive={'mobile': {'x': 48.4, 'y': 69.3, 'width': 3.2, 'height': 1.5}}))
    livelli += [
        K.livello('ve-o', contenuto='Ventaglio · abiti d’estate · Palermo', x=6, y=16, w=44, size=13, peso='500', colore=mix('#FFFFFF', 88),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('ve-t1', contenuto='Si apre', tag='h1', x=6, y=22, w=40, size=100, peso='400', colore='#FFFFFF', lineHeight=0.95,
                  entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'fontSize': m(48), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('ve-t2', contenuto='col caldo.', tag='p', x=6, y=35, w=40, size=100, peso='400', fontFamily='Yeseva One',
                  colore='#F6C77A', lineHeight=0.95, entra='maskFromBottom', ritardo=500, durata=1000,
                  responsive={'mobile': {'fontSize': m(48), 'x': 6, 'y': 15.5, 'width': 88}}),
        K.livello('ve-s', contenuto='Scorrete: il ventaglio si apre, e ogni stecca è un abito della collezione.', x=6, y=54, w=32,
                  size=17, peso='400', colore=mix('#FFFFFF', 92), lineHeight=1.55, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 21, 'width': 86}}),
    ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 58, 'o': 0}, {'p': 72, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('ve-b', tipo='button', contenuto='La collezione', x=6, y=72, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=10, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 28, 'fontSize': m(11)}}),
        K.livello('ve-l', tipo='button', contenuto='La sartoria', x=20, y=72, w='auto', size=12, peso='600', colore='#FFFFFF',
                  bgColor='transparent', padding=16, borderRadius=10, borderWidth=1, borderColor=mix('#FFFFFF', 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=SARTORIA, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix('#FFFFFF', 14), 'borderColor': '#FFFFFF', 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'x': 55, 'y': 28, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    sl = [K.diapositiva('ve-1', K.sfondo_video(V_CEFALU, overlay=0.12, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Ventaglio · Palermo, via Alloro 41', ['Un abito d’estate', 'e il suo ventaglio.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Cuciamo abiti larghi, che lasciano passare l’aria: lino lavato, cotone, seta stampata coi '
                                'fiori dei giardini della Kalsa. Con ogni abito avanza un po’ di stoffa, e ne facciamo un '
                                'ventaglio: ve lo regaliamo, perché d’agosto a Palermo serve.', size=17),
                      K.button(t, 'La sartoria', SARTORIA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le stoffe', ['Sfogliatele', 'come un ventaglio.'], size=60)]),
        t.one([K.coverflow(t, [(SETA[0], 'Seta bianca', 'Seta', 'Crêpe di Como'), (FIOR[1], 'Fiori della Kalsa', 'Kalsa', 'Seta stampata'),
                               (LINO[1], 'Lino lavato', 'Lino', 'Lavato in pietra'), (SETA[2], 'Seta color pesca', 'Pesca', 'Raso'),
                               (FIOR[3], 'Fiori di limone', 'Limoni', 'Cotone stampato'), (LINO[4], 'Lino naturale', 'Naturale', 'Lino grezzo'),
                               (SETA[1], 'Seta rosa', 'Bouganville', 'Seta stampata')], altezza=460, larghezza=340)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro abiti,', 'quattro ventagli.'], size=60)]),
        t.one([M.capi(K, t, [
            (ABIT[0], 'Abiti', 'Kalsa', '', 'Seta stampata a fiori, lunga.', '290 €', 'nuovo'),
            (LINO[0], 'Abiti', 'Mondello', '', 'Lino lavato, le maniche a pipistrello.', '240 €', ''),
            (FIOR[2], 'Abiti', 'Zisa', '', 'Cotone a fiori, al ginocchio.', '210 €', ''),
            (ABIT[3], 'Abiti', 'Vucciria', '', 'Il rosso dei mercati, a portafoglio.', '260 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Lino e seta', 'Cuciti a Palermo', 'Un ventaglio in regalo', 'D’agosto serve'],
                                          fondo=C['sec'], colore='#FFFFFF', size=56)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(K.video_frase(t, V_ROSSO, 'Al mare', 'Un abito <em>che fa vento.</em>',
                             'Larghi, leggeri, cuciti per le giornate in cui il caldo non si muove.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Indossati', ['D’estate,', 'per strada.'], size=60)]),
        P.fila_foto(K, t, [(DONN[2], 'La Vucciria col suo ventaglio'), (FIOR[5], 'La Zisa al parco'), (ABIT[4], 'La Kalsa'),
                           (LINO[5], 'La Mondello')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Un ventaglio nuovo', 'ogni estate.'],
                         'Le stoffe nuove, gli abiti che escono, le sere in sartoria col cortile aperto. Due volte l’anno.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Dieci abiti,', 'dieci ventagli.'],
                   'Lino, cotone e seta stampata con i fiori dei giardini di Palermo. Ogni abito ha il suo ventaglio della stessa '
                   'stoffa, in regalo.', bg=K.video_bg(V_PRATO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Seta e cotone', ['I fiori', 'dei giardini.'], size=60)]),
        t.one([M.capi(K, t, [
            (ABIT[0], 'Abiti', 'Kalsa', '', 'Seta stampata, lunga.', '290 €', 'nuovo'),
            (FIOR[2], 'Abiti', 'Zisa', '', 'Cotone a fiori.', '210 €', ''),
            (FIOR[4], 'Abiti', 'Orto Botanico', '', 'Seta, i fiori grandi.', '300 €', ''),
            (ABIT[5], 'Abiti', 'Favorita', '', 'Cotone, la gonna a ruota.', '230 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Lino', ['Per le giornate', 'più calde.'], size=60)]),
        t.one([M.capi(K, t, [
            (LINO[0], 'Abiti', 'Mondello', '', 'Le maniche a pipistrello.', '240 €', ''),
            (LINO[2], 'Abiti', 'Capo Gallo', '', 'Il lino bianco, lungo.', '250 €', ''),
            (LINO[3], 'Abiti', 'Arenella', '', 'Il lino ruggine.', '240 €', ''),
            (ABIT[3], 'Abiti', 'Vucciria', '', 'Il rosso, a portafoglio.', '260 €', 'ultimi')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I ventagli', ['Della stessa', 'stoffa.'], size=60)]),
        P.fila_foto(K, t, [(VENT[0], 'Ventagli di carta e stoffa'), (VENT[1], 'Il ventaglio nel giardino'), (VENT[2], 'Il ventaglio blu'),
                           (VENT[3], 'Il ventaglio in bianco e nero')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_sartoria():
    t = Tpl(ID, 'la-sartoria')
    out = [K.intro(t, 'La sartoria', ['Un cortile,', 'quattro macchine.'],
                   'Cuciamo in un cortile della Kalsa, con la porta aperta per far passare l’aria. Ogni abito lo fa una sarta sola, '
                   'dall’inizio alla fine.', bg=K.video_bg(V_SETA, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si fa', ['La stoffa,', 'l’abito, il ventaglio.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'la stoffa', SETA[3], 'Scegliere', 'La stoffa', 'del giardino', 'I fiori li fotografiamo nei giardini di Palermo e li '
             'stampiamo sulla seta a Como.', '12', 'stampe'),
            ('2', 'l’abito', LINO[4], 'Cucire', 'L’abito', 'largo', 'Una sarta sola per abito, dall’inizio alla fine: due giorni.', '2',
             'giorni'),
            ('3', 'il ventaglio', VENT[0], 'Avanzare', 'Il ventaglio', 'in regalo', 'Con la stoffa avanzata si fa il ventaglio, sulle '
             'stecche di legno di ulivo.', '9', 'stecche')], icona='wind')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In sartoria', ['Chi cuce', 'nel cortile.'], size=60)]),
        K.squadra(t, [(DONN[3], 'Rosalia Lo Bue', 'Fondatrice', 'Cuciva per le spose della Kalsa; poi ha pensato all’estate.',
                       'center 25%'),
                      (DONN[0], 'Mariam Diop', 'Sarta', 'Cuce gli abiti di seta, i più difficili.', 'center 25%'),
                      (DONN[1], 'Giuseppe Ferrante', 'Ventagli', 'Monta i ventagli sulle stecche d’ulivo.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, MERC[1], 'Il mercato della Vucciria', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho comprato l’abito per un matrimonio ad agosto. Del vestito si sono accorti tutti, ma il '
                                        'ventaglio me l’hanno chiesto in prestito anche gli sposi.', 'Federica, Catania', size='38')],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove trovarli,', 'all’ombra.'],
                   'I nostri abiti nei negozi delle città di mare, e il cortile della sartoria aperto anche la sera.',
                   bg=K.photo_bg(SICI[0]), size=96)]
    out.append(M.negozi(K, t, [
        ('Palermo', 'Sartoria Ventaglio', 'via Alloro 41'), ('Palermo', 'Kalsa', 'piazza Marina 18'), ('Cefalù', 'Scirocco', 'corso Ruggero 70'),
        ('Siracusa', 'Ortigia', 'via Maestranza 22'), ('Napoli', 'Chiaia', 'via dei Mille 51'), ('Roma', 'Ombra', 'via dei Coronari 8'),
        ('Barcellona', 'Ventall', 'carrer del Rec 33'), ('Atene', 'Ripídi', 'Plaka, Adrianou 90')],
        nota='In sartoria gli abiti si accorciano e si allargano gratis, anche anni dopo.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un abito su misura\nUn ventaglio della mia stoffa\nVendere Ventaglio\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Un abito', 'su misura?'],
                                'Per un abito su misura, un ventaglio dalla vostra stoffa, o per vendere Ventaglio.', VOCI, campi,
                                'Ventaglio — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La sartoria', ['Nella Kalsa,', 'in un cortile.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'lunedì — sabato 9:30 — 13:30, 16 — 20'), ('La sera', 'cortile aperto il venerdì'),
                                  ('A piedi', 'da piazza Marina, due minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Ventaglio · la sartoria', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='sartoria', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sugli abiti.'], [
        ('Il ventaglio è davvero in regalo?', 'Sì, con ogni abito, della stessa stoffa.'),
        ('Il lino si stropiccia?', 'Sì, ed è il bello del lino: si porta così.'),
        ('Fate le taglie forti?', 'Sì, gli abiti sono larghi e si cuciono anche su misura.'),
        ('Spedite?', 'Sì, in tutta Europa, piegati nella carta velina col ventaglio.'),
    ], 've-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · troppo caldo', color='#F6C77A', align='center'),
                       K.headline(t, 'Qui non c’è <em>un filo d’aria.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color='#FFFFFF', accento='#F6C77A'),
                       K.text(t, 'Questa pagina si è sciolta al sole. Torniamo all’ombra, all’inizio.', size=17, align='center',
                              color=mix('#FFFFFF', 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_MANO, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il ventaglio che si apre',
    ['moda', 'abbigliamento', 'abiti', 'estate', 'palermo', 'sicilia', 'chiaro', 'slider pro', 'coverflow'],
    'Una sartoria di abiti d’estate che regala un ventaglio della stessa stoffa: in apertura, sopra il mare, un ventaglio '
    'chiuso si apre a mezzaluna scorrendo e ogni stecca è la foto di un abito; poi le stoffe in una galleria a ventaglio, la '
    'collezione, la sartoria nel cortile e i negozi.',
    [('La collezione', CAPI), ('La sartoria', SARTORIA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('la-sartoria', 'La sartoria', 'la-sartoria'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Jost\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Yeseva One\', Georgia, serif',
    '\'Spline Sans Mono\', ui-monospace, Menlo, monospace', ['Jost', 'Yeseva One', 'Spline Sans Mono'], corsivo_di=[],
    peso_titoli='400', raggio=10, grana=3, titoli_line='1.0', titoli_spazio='0')
THEME['cursor']['style'] = 'ventaglio'
THEME['cursor']['ring_color'] = HEX['secondary']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'la-sartoria.json': build_sartoria(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
