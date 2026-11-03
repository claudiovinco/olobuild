# -*- coding: utf-8 -*-
"""Genera il tema «Falcata» (id falcata): un calzaturificio della Riviera del Brenta che fa scarpe da passerella a mano.

La trovata: «la passerella la fate voi». In apertura la modella sta in fondo alla passerella e cammina solo se scorrete:
il video segue lo scorrimento (avanti scendendo, indietro risalendo: la si può far tornare indietro), un contapassi nell'
angolo conta le falcate, e in fondo alla passerella scatta il lampo dei fotografi. Più giù le forme di legno che si
girano e diventano la scarpa (Flip Card).

Tema scuro: nero di sala, oro e il rosso della suola. Titoli in Bodoni Moda, testo in Inter, dati in Red Hat Mono.
Logo: FALCATA spaziato con la riga d'oro. Cursore: flash (al clic un lampo, sui link il mirino mette a fuoco).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'falcata'
NAME = 'Falcata'

HEX = {
    'primary': '#E9C46A', 'primary_contrast': '#0C0B0A', 'secondary': '#A89F92', 'secondary_contrast': '#0C0B0A',
    'accent': '#FF6A5A', 'dark': '#050505', 'light': '#FFFFFF', 'muted': '#181614', 'muted_contrast': '#F3EFE8',
    'text': '#F3EFE8', 'text_muted': '#B5ADA2', 'background': '#0C0B0A', 'border': '#2E2A25', 'link': '#E9C46A',
}

HOME, CAPI, BOTTEGA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:la-bottega}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'bottega@falcata.example', '+39 049 000 8812', 'tel:+390490008812'
LAT, LNG = '45.4310', '12.0390'

V_PASSO = 'pexels-video-9509332-passo'
V_ROSA, V_ARCO, V_ARCO2, V_SALA, V_SCALE, V_ROSSE, V_PARCO, V_MOLA, V_TACCO = (
    'pexels-video-9167971', 'pexels-video-7305163', 'pexels-video-7305169', 'pexels-video-9509332', 'pexels-video-8061666',
    'pexels-video-8083752', 'pexels-video-15017744', 'pexels-video-5758632', 'pexels-video-9936744')
VIDEOS = [V_PASSO, V_ROSA, V_ARCO, V_ARCO2, V_SALA, V_SCALE, V_ROSSE, V_PARCO, V_MOLA, V_TACCO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
TACC = f('7318919', '34294446', '7588398', '5398968', '7760805', '31450735')
STIV = f('27639594', '3076770', '26851199', '26851193', '2112753', '5385354')
CALZ = f('40290857', '34510831', '5894239', '38946169', '4190570', '37273499')
CAMM = f('16234533', '19920691', '33621284', '21316057', '15080063', '14577038')
PELL = f('32142573', '9992898', '36253710', '4637874', '8899943', '6765524')
SCAT = f('31452056', '31452059', '38570118', '31452062')
FLAS = f('13521541', '18288856', '16870829', '36363423', '5577960')
STRA = f('37146330', '33992026', '32285300', '33659072', '23385346', '33402097', '33401681')
FORM = f('38946185', '4577361', '13524876', '40290860')
LOGO_S, LOGO_C = 'logo-falcata-scuro.png', 'logo-falcata-chiaro.png'
PHOTOS = TACC + STIV + CALZ + CAMM + PELL + SCAT + FLAS + STRA + FORM
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='600', corsivo=True, maiuscole_pulsanti=True)
C = K.c
MONO = 'Red Hat Mono'
VOCI = [('map-pin', 'Via Riviera Berica 12 · Stra', NEGOZI + '#bottega'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Falcata', LOGO_C, 190, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_C, '1164/296', 'Falcata fa a mano, sulla Riviera del Brenta, scarpe e stivali da passerella: la '
                   'forma di legno, la pelle tagliata a coltello, la suola cucita.',
                   [('La collezione', CAPI), ('La bottega', BOTTEGA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Via Riviera Berica 12, Stra<br>Lunedì — venerdì 9 — 18', VOCI,
                   '© 2026 Falcata · Stra · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=420)


# ── L'apertura: la passerella che si percorre scorrendo ──
def apertura(t):
    m = K.mob
    sfondo = K.sfondo_video(V_PASSO, overlay=0.08, colore=C['nero'])
    sfondo['videoScroll'] = True
    livelli = K.veli('fa-velo', 'sinistra', 30, 56, colore=C['nero'], forza=84, mobile_da=0, mobile_alto=30) + [
        K.livello('fa-o', contenuto='Falcata · scarpe da passerella · Riviera del Brenta', x=6, y=16, w=44, size=13, peso='500',
                  colore=mix(C['testo'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('fa-t1', contenuto='Ogni passo', tag='h1', x=6, y=22, w=44, size=104, peso='600', colore=C['testo'], lineHeight=0.95,
                  entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('fa-t2', contenuto='lo fate voi.', tag='p', x=6, y=36, w=44, size=104, peso='600', fontStyle='italic',
                  fontFamily='Bodoni Moda', colore=C['acc'], lineHeight=0.95, entra='maskFromBottom', ritardo=500, durata=1000,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 15.5, 'width': 88}}),
        K.livello('fa-s', contenuto='La modella cammina solo se scorrete. Risalite, e torna indietro.', x=6, y=52, w=30, size=17,
                  peso='400', colore=mix(C['testo'], 92), lineHeight=1.55, ritardo=800,
                  scrollMove={'inizio': 0, 'fine': 30, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 21, 'width': 86}}),
        K.livello('fa-passi', contenuto='Falcate', x=6, y=74, w=16, size=12, peso='600', colore=mix(C['testo'], 80), letterSpacing=3,
                  textTransform='uppercase', fontFamily=MONO, ritardo=900,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 92, 'width': 30}}),
    ]
    # il contapassi: un numero per tratto di scorrimento, si accende uno alla volta
    for k in range(10):
        da, a = 6 + k * 8, 14 + k * 8
        tappe = [{'p': 0, 'o': 0}, {'p': da, 'o': 0}, {'p': da + 1, 'o': 100}, {'p': a, 'o': 100}, {'p': a + 1, 'o': 0}, {'p': 100, 'o': 0}]
        if k == 9:
            tappe = [{'p': 0, 'o': 0}, {'p': da, 'o': 0}, {'p': da + 1, 'o': 100}, {'p': 100, 'o': 100}]
        livelli.append(K.livello('fa-n%d' % k, contenuto='%02d' % (k + 1), x=6, y=78, w=16, size=64, peso='600', colore=C['acc'],
                                 fontFamily=MONO, ritardo=0, entra='fadeIn', scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe},
                                 responsive={'mobile': {'fontSize': m(30), 'x': 6, 'y': 94, 'width': 30}}))
    # in fondo alla passerella: il lampo dei fotografi
    livelli.append(K.livello('fa-lampo', tipo='shape', x=0, y=0, w=100, h=100, bgColor='#FFFFFF', entra='fadeIn', ritardo=0, durata=200,
                             scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 86, 'o': 0}, {'p': 87.5, 'o': 85},
                                                                             {'p': 92, 'o': 0}, {'p': 100, 'o': 0}]}))
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 88, 'o': 0}, {'p': 95, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('fa-b', tipo='button', contenuto='La collezione', x=6, y=62, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=0, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 26, 'fontSize': m(11)}}),
        K.livello('fa-l', tipo='button', contenuto='La bottega', x=20, y=62, w='auto', size=12, peso='600', colore=C['testo'],
                  bgColor='transparent', padding=16, borderRadius=0, borderWidth=1, borderColor=mix(C['testo'], 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl=BOTTEGA, scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix(C['testo'], 14), 'borderColor': C['testo'], 'duration': 250},
                  responsive={'tablet': {'x': 25}, 'mobile': {'x': 55, 'y': 26, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    M.scendi_tel(livelli)
    sl = [K.diapositiva('fa-1', sfondo, livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Falcata · Stra, Riviera del Brenta', ['Una scarpa', 'si giudica camminando.'],
                                size=72, layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Ferme in vetrina tutte le scarpe sono belle. Le nostre le facciamo per la passerella: il '
                                'tacco a filo della pianta, la suola che non scivola sul legno, la pelle che si piega senza '
                                'segnarsi. Dal 1961, a Stra, in una bottega sul canale.', size=17),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro scarpe', 'da passerella.'], size=60)]),
        t.one([M.capi(K, t, [
            (TACC[1], 'Décolleté', 'Falcata', '', 'Il tacco da otto, la punta affilata.', '420 €', 'nuova'),
            (STIV[1], 'Stivaletti', 'Canale', '', 'Il tronchetto di vitello bianco.', '480 €', ''),
            (TACC[3], 'Sandali', 'Villa', '', 'I listini sottili, il tacco a spillo.', '390 €', ''),
            (STIV[3], 'Stivaletti', 'Burchiello', '', 'La pelle di cervo, il tacco largo.', '460 €', '')], CAPI, proporzione='1/1')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dalla forma', ['Passate sopra:', 'la forma diventa scarpa.'], size=60)]),
        P.carte(K, t, [(FORM[1], 'La forma', 'Il legno di faggio, tornito sul piede.', 'Ogni modello ha la sua forma di legno: la '
                        'teniamo per cinquant’anni.'),
                       (CALZ[1], 'Il taglio', 'La pelle tagliata a coltello.', 'Si taglia a mano seguendo la venatura: nessun pezzo è '
                        'uguale all’altro.'),
                       (CALZ[3], 'La cucitura', 'La tomaia montata sulla forma.', 'La tomaia si tira sulla forma con le pinze e si '
                        'lascia riposare una notte.'),
                       (PELL[3], 'La suola', 'Cucita, non incollata.', 'La suola si cuce a mano: si risuola dieci volte.')],
                altezza='420', colonne=4),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([M.nastro(K, t, ['Fatte a mano', 'Riviera del Brenta', 'Dal 1961', 'Tacco a filo'],
                                          fondo=C['acc'], colore=C['on_acc'], size=56)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(K.video_frase(t, V_SCALE, 'Il tacco', 'Otto centimetri <em>che non si sentono.</em>',
                             'Il tacco sta sotto il tallone, a filo della pianta: il peso cade dove deve, e si cammina tutta la sera.',
                             overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Per strada', ['La passerella', 'continua fuori.'], size=60)]),
        P.fila_foto(K, t, [(STRA[1], 'La Falcata in centro'), (STRA[3], 'Il Canale'), (STRA[5], 'La Villa d’estate'),
                           (STRA[6], 'Il Burchiello')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Una scarpa nuova', 'ogni sfilata.'],
                         'Le scarpe nuove, le sfilate dove camminano, le serate aperte in bottega. Due volte l’anno.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Otto modelli,', 'una forma ciascuno.'],
                   'Vitello, cervo, capretto e raso. Ogni modello ha la sua forma di legno e il suo nome, preso da una villa '
                   'della Riviera.', bg=K.video_bg(V_ARCO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Décolleté e sandali', ['Il tacco', 'a filo.'], size=60)]),
        t.one([M.capi(K, t, [
            (TACC[1], 'Décolleté', 'Falcata', '', 'Il tacco da otto.', '420 €', 'nuova'),
            (TACC[0], 'Décolleté', 'Pisani', '', 'Il raso rosso, la punta tonda.', '440 €', ''),
            (TACC[3], 'Sandali', 'Villa', '', 'I listini sottili.', '390 €', ''),
            (TACC[4], 'Sandali', 'Foscari', '', 'Il cinturino alla caviglia.', '400 €', '')], NEGOZI, proporzione='1/1')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Stivali', ['Fino', 'al ginocchio.'], size=60)]),
        t.one([M.capi(K, t, [
            (STIV[1], 'Stivaletti', 'Canale', '', 'Il tronchetto bianco.', '480 €', ''),
            (STIV[3], 'Stivaletti', 'Burchiello', '', 'Il cervo, il tacco largo.', '460 €', ''),
            (STIV[4], 'Stivali', 'Malcontenta', '', 'Il gambale fino al ginocchio.', '620 €', ''),
            (STIV[0], 'Stivaletti', 'Barchessa', '', 'Il cuoio lucido, i lacci.', '470 €', 'ultimi')], NEGOZI, proporzione='1/1')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Una forma,', 'centoventi passaggi.'],
                   'Una scarpa da passerella passa per le mani di sei persone, in tre settimane. Ve le presentiamo.',
                   bg=K.video_bg(V_MOLA, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si fa', ['Dalla forma', 'alla passerella.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'la forma', FORM[0], 'Tornire', 'La forma', 'di faggio', 'Il legno si tornisce sul piede del modello: è la forma che '
             'decide come si cammina.', '50', 'anni di vita'),
            ('2', 'la tomaia', CALZ[4], 'Montare', 'La tomaia', 'sulla forma', 'La pelle si tira sulla forma con le pinze e riposa una '
             'notte.', '1', 'notte'),
            ('3', 'la suola', CALZ[5], 'Cucire', 'La suola', 'a mano', 'La suola si cuce e si lucida a mano: si potrà risuolare dieci '
             'volte.', '10', 'risuolature')], icona='footprints')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi fa', 'le scarpe.'], size=60)]),
        K.squadra(t, [(CALZ[0], 'Ottavio Zanon', 'Formaio', 'Tornisce le forme di legno da quando aveva quattordici anni.',
                       'center 30%'),
                      (CAMM[3], 'Martina Rizzo', 'Modellista', 'Disegna le scarpe camminando: se non le sente, non le disegna.',
                       'center 25%'),
                      (CALZ[2], 'Samuel Okoye', 'Suole', 'Cuce le suole a mano, quattrocento punti per scarpa.', 'center 30%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, FLAS[2], 'I fotografi a bordo passerella', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho sfilato dieci minuti con le loro décolleté, poi sono andata a cena e a ballare. A '
                                        'mezzanotte ancora non sapevo di averle ai piedi.', 'Aurora, modella', size='38')],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarle', 'camminando.'],
                   'Una scarpa si prova facendo dieci passi: in ogni negozio c’è un tappeto lungo, la nostra piccola passerella.',
                   bg=K.photo_bg(SCAT[1]), size=96)]
    out.append(M.negozi(K, t, [
        ('Stra', 'Bottega Falcata', 'via Riviera Berica 12'), ('Venezia', 'Passerella', 'calle Vallaresso 3'),
        ('Padova', 'Tacco', 'via Roma 61'), ('Milano', 'Pianta', 'via Montenapoleone 9'), ('Firenze', 'Forma', 'via del Parione 5'),
        ('Roma', 'Suola', 'via del Babuino 140'), ('Parigi', 'Le Pas', 'rue Saint-Honoré 270'), ('New York', 'Stride', 'Madison Avenue 760')],
        nota='In bottega si risuolano le nostre scarpe, anche dopo vent’anni.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una risuolatura\nUn paio su misura\nVendere Falcata\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Un paio', 'su misura?'],
                                'Per una risuolatura, un paio su misura sulla vostra forma, o per vendere Falcata.', VOCI, campi,
                                'Falcata — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['Sul Brenta,', 'davanti alla villa.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'lunedì — venerdì 9 — 18'), ('Su misura', 'su appuntamento'),
                                  ('In battello', 'il Burchiello si ferma davanti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Falcata · la bottega', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='bottega', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sulle scarpe.'], [
        ('Il tacco fa male?', 'Il tacco sta a filo della pianta: il peso cade sul tallone e non sulle dita.'),
        ('Si risuolano?', 'Sì, in bottega, anche dieci volte: la suola è cucita, non incollata.'),
        ('Fate le mezze misure?', 'Sì, e su misura facciamo la vostra forma di legno, che teniamo per voi.'),
        ('Spedite?', 'Sì, nella loro scatola col sacchetto di cotone: e se non vanno bene tornano gratis.'),
    ], 'fa-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori passerella', color=C['acc'], align='center'),
                       K.headline(t, 'Qui la passerella <em>è finita.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Un passo di troppo. Torniamo all’inizio della sfilata.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SALA, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La passerella che si cammina',
    ['moda', 'scarpe', 'stivali', 'artigianato', 'riviera del brenta', 'scuro', 'slider pro', 'video a scorrimento'],
    'Un calzaturificio di scarpe da passerella: in apertura la modella cammina solo se scorrete, il video va avanti e torna '
    'indietro con lo scorrimento, il contapassi conta le falcate e in fondo alla passerella scatta il lampo dei fotografi; '
    'poi le forme di legno che si girano e diventano scarpa, la bottega e i negozi.',
    [('La collezione', CAPI), ('La bottega', BOTTEGA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Inter\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bodoni Moda\', Didot, Georgia, serif',
    '\'Red Hat Mono\', ui-monospace, Menlo, monospace', ['Inter', 'Bodoni Moda', 'Red Hat Mono'], corsivo_di=['Bodoni Moda'],
    peso_titoli='600', raggio=0, grana=4, titoli_line='0.95', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'flash'
THEME['cursor']['ring_color'] = HEX['text']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'la-bottega.json': build_bottega(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
