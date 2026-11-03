# -*- coding: utf-8 -*-
"""Genera il tema «Combinazione» (id combinazione): una bottega di Genova che fa valigette e borse di cuoio con la
serratura a combinazione, dal 1953.

La trovata: «il caveau». In apertura c'è la porta tonda di una cassaforte: scorrendo la manopola gira sui numeri della
combinazione, la ruota a tre razze fa il suo giro e la porta si spalanca in 3D sui cardini, scoprendo la bottega piena
di borse. Più giù il «drop segreto» si apre con un lucchetto a combinazione (Lucchetto): le cifre sono nascoste nella
pagina stessa.

Tema scuro: acciaio brunito, ottone e il rosso del cuoio. Titoli in Marcellus, testo in Mulish, dati in Azeret Mono.
Logo: la manopola e il nome. Cursore: quadrante (la manopola che gira a scatti).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'combinazione'
NAME = 'Combinazione'

HEX = {
    'primary': '#CFA54C', 'primary_contrast': '#100E0B', 'secondary': '#8C7B60', 'secondary_contrast': '#100E0B',
    'accent': '#E07A68', 'dark': '#070605', 'light': '#FFFFFF', 'muted': '#1B1813', 'muted_contrast': '#EFE6D3',
    'text': '#EFE6D3', 'text_muted': '#B9AD95', 'background': '#100E0B', 'border': '#352E24', 'link': '#CFA54C',
}
ACCIAIO = '#2A251E'

HOME, CAPI, BOTTEGA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:la-bottega}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'bottega@combinazione.example', '+39 010 000 1953', 'tel:+390100001953'
LAT, LNG = '44.4100', '8.9290'

V_NEGOZIO, V_SCAFFALI, V_LIBRI, V_CUOIO, V_VALIGETTA, V_PASSI, V_SCRIGNO, V_CHIAVE, V_PORTA = (
    'pexels-video-6649983', 'pexels-video-6648425', 'pexels-video-8731589', 'pexels-video-34740040', 'pexels-video-6533048',
    'pexels-video-8275793', 'pexels-video-31750573', 'pexels-video-9792637', 'pexels-video-34800569')
VIDEOS = [V_NEGOZIO, V_SCAFFALI, V_LIBRI, V_CUOIO, V_VALIGETTA, V_PASSI, V_SCRIGNO, V_CHIAVE, V_PORTA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
VALI = f('9566324', '2977304', '4378647', '10973347', '35859938', '12025430')
BORS = f('7953286', '4830924', '22432991', '35666033', '33853704', '20380721')
LUCC = f('35625441', '411227', '943922', '11391947')
CAVE = f('19882101', '23322329', '5332291', '29807086', '60611')
MODE = f('21897312', '21897135', '1381553', '21897151', '21897137', '8989866')
BOTT = f('5963132', '5963131', '40080053', '4452610', '5894159', '5963058')
OTTO = f('36740854', '6654805', '6654804')
VALIGIA = f('36933446', '5138184', '14539683', '10259772')
GENO = f('36268895', '28993332', '18671585', '10666150', '11849088')
CHIA = f('15446635', '33388008', '615350', '33388010')
DISEGNI = ['cassaforte-quadrante.svg', 'cassaforte-ruota.svg']
LOGO_S, LOGO_C = 'logo-combinazione-scuro.png', 'logo-combinazione-chiaro.png'
PHOTOS = VALI + BORS + LUCC + CAVE + MODE + BOTT + OTTO + VALIGIA + GENO + CHIA + DISEGNI
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True)
C = K.c
MONO = 'Azeret Mono'
VOCI = [('map-pin', 'Via di Sottoripa 7 · Genova', NEGOZI + '#bottega'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Combinazione', LOGO_C, 250, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_C, '1512/230', 'Combinazione fa a Genova, dal 1953, valigette e borse di cuoio con la serratura a '
                   'combinazione d’ottone: tre ruote, mille numeri, una sola strada per aprirle.',
                   [('La collezione', CAPI), ('La bottega', BOTTEGA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Via di Sottoripa 7, Genova<br>Martedì — sabato 10 — 19', VOCI,
                   '© 2026 Combinazione · Genova · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=520)


# ── L'apertura: la porta del caveau ──
def apertura(t):
    m = K.mob
    # la porta gira sui cardini (rotazione 3D attorno al bordo sinistro) fin quasi di taglio, poi sparisce
    porta = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': 0, 'o': 100}, {'p': 48, 'ry': 0, 'o': 100}, {'p': 70, 'ry': -86, 'o': 100},
                                                 {'p': 78, 'ry': -86, 'o': 0}, {'p': 100, 'ry': -86, 'o': 0}]}
    # manopola e ruota girano su sé stesse; quando la porta si apre la seguono verso il cardine stringendosi (come
    # la porta vista di taglio: la larghezza è il coseno dell'angolo), e svaniscono con lei
    def segue(d, tappe):
        return {'inizio': 0, 'fine': 100, 'tappe': tappe + [
            {'p': 48, 'x': 0, 'sx': 1, 'o': 100}, {'p': 59, 'x': round(-d * 0.29), 'sx': 0.71, 'o': 100},
            {'p': 68, 'x': round(-d * 0.9), 'sx': 0.1, 'o': 60}, {'p': 70, 'x': -d, 'sx': 0.05, 'o': 0}, {'p': 100, 'x': -d, 'sx': 0.05, 'o': 0}]}
    quad = segue(245, [{'p': 0, 'r': 0}, {'p': 8, 'r': 0}, {'p': 20, 'r': 252}, {'p': 30, 'r': 108}, {'p': 42, 'r': 360}])
    ruota = segue(245, [{'p': 0, 'r': 0}, {'p': 42, 'r': 0}, {'p': 47, 'r': 120}])
    livelli = K.veli('co-velo', 'sinistra', 34, 56, colore=C['nero'], forza=84, mobile_da=0, mobile_alto=24) + [
        K.livello('co-porta', tipo='shape', x=53, y=19.75, w=34, h=60.5, bgColor=ACCIAIO, borderRadius=999, borderWidth=8,
                  borderColor=C['acc'], entra='fadeIn', ritardo=100, durata=700,
                  shapeGradient={'angle': 145, 'from': '#3A332A', 'to': '#15120E', 'fromStop': 10},
                  customCSS='transform-origin:0% 50%;box-shadow:0 30px 80px rgba(0,0,0,.7),inset 0 0 0 26px rgba(0,0,0,.25);',
                  scrollMove=porta, responsive={'mobile': {'x': 8, 'y': 28, 'width': 84, 'height': 38.4}}),
        K.livello('co-quadrante', tipo='image', imageSrc=K.img(DISEGNI[0]), objectFit='contain', x=64.8, y=27.8, w=10.4, h=18.5,
                  entra='fadeIn', ritardo=300, durata=700,
                  scrollMove=quad, responsive={'mobile': {'x': 37.1, 'y': 33.5, 'width': 25.8, 'height': 11.8}}),
        K.livello('co-ruota', tipo='image', imageSrc=K.img(DISEGNI[1]), objectFit='contain', x=64.1, y=51.2, w=11.8, h=21,
                  entra='fadeIn', ritardo=400, durata=700,
                  scrollMove=ruota, responsive={'mobile': {'x': 35.4, 'y': 55.8, 'width': 29.2, 'height': 13.4}}),
        K.livello('co-o', contenuto='Combinazione · borse e valigette · Genova, dal 1953', x=6, y=16, w=44, size=13, peso='500',
                  colore=mix(C['testo'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('co-t1', contenuto='Si apre', tag='h1', x=6, y=22, w=44, size=110, peso='400', colore=C['testo'], lineHeight=0.95,
                  entra='maskFromBottom', ritardo=300, durata=1000,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 11, 'width': 88}}),
        K.livello('co-t2', contenuto='a combinazione.', tag='p', x=6, y=36, w=44, size=72, peso='400', fontFamily='Marcellus',
                  colore=C['acc'], lineHeight=1.0, entra='maskFromBottom', ritardo=500, durata=1000,
                  responsive={'mobile': {'fontSize': m(32), 'x': 6, 'y': 15.5, 'width': 88}}),
        K.livello('co-s', contenuto='Scorrete: la manopola gira, la ruota fa il suo giro e la porta si apre.', x=6, y=50, w=32, size=17,
                  peso='400', colore=mix(C['testo'], 92), lineHeight=1.55, ritardo=800,
                  scrollMove={'inizio': 0, 'fine': 40, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 19.5, 'width': 86}}),
    ]
    # i tre numeri della combinazione, che si accendono quando la manopola ci arriva
    for k, (num, p, x) in enumerate([('70', 20, 6), ('30', 30, 13), ('00', 42, 20)]):
        livelli.append(K.livello('co-n%d' % k, contenuto=num, x=x, y=64, w=6, size=40, peso='400', colore=C['acc'], fontFamily=MONO,
                                 entra='fadeIn', ritardo=0,
                                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 22}, {'p': p - 3, 'o': 22}, {'p': p, 'o': 100},
                                                                                 {'p': 100, 'o': 100}]},
                                 responsive={'mobile': {'fontSize': m(22), 'x': 6 + k * 12, 'y': 82, 'width': 12}}))
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 72, 'o': 0}, {'p': 86, 'o': 100}, {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('co-b', tipo='button', contenuto='La collezione', x=6, y=78, w='auto', size=12, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=2, letterSpacing=3, textTransform='uppercase', buttonUrl=CAPI,
                  scrollMove=fine, entra='fadeIn', ritardo=0, hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 87, 'fontSize': m(11)}}),
        K.livello('co-l', tipo='button', contenuto='Il drop segreto', x=20.5, y=78, w='auto', size=12, peso='600', colore=C['testo'],
                  bgColor='transparent', padding=16, borderRadius=2, borderWidth=1, borderColor=mix(C['testo'], 60), letterSpacing=3,
                  textTransform='uppercase', buttonUrl='#drop', scrollMove=fine, entra='fadeIn', ritardo=0,
                  hover={'bgColor': mix(C['testo'], 14), 'borderColor': C['testo'], 'duration': 250},
                  responsive={'mobile': {'x': 55, 'y': 87, 'fontSize': m(11)}}),
    ]
    M.veli_oltre(livelli)
    M.scendi_tel(livelli)
    sl = [K.diapositiva('co-1', K.sfondo_video(V_NEGOZIO, overlay=0.12, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1800, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def lucchetto(t):
    """Il drop segreto: un lucchetto a quattro ruote; le cifre stanno nascoste nella pagina."""
    return t.classic('lucchetto', {
        'enigma': '<p><strong>Il drop segreto.</strong> Una volta al mese mettiamo in vendita una valigetta sola, numerata: si '
                  'apre con questa combinazione.</p><p>La prima cifra è il numero civico della bottega. La seconda, quante borse ci '
                  'sono nella collezione qui sopra. Le ultime due, l’anno della nostra prima valigetta.</p>',
        'indizio': 'L’indirizzo sta in fondo alla pagina, la collezione poco sopra, l’anno nell’introduzione: le ultime due cifre.',
        'testo_indizio': 'Un indizio', 'tentativi_indizio': 3, 'combinazione': '7453', 'simboli': 'cifre', 'testo_pulsante': 'Apri',
        'messaggio_errore': 'Non si apre. Rileggete la pagina.', 'mostra_tentativi': True,
        'messaggio_successo': 'Aperto. La valigetta di questo mese è la numero 12, cuoio rosso e ottone: è vostra al prezzo di '
                              'bottega con questo codice, fino a domenica.',
        'codice_premio': 'CAVEAU12', 'testo_copia': 'Copia il codice', 'testo_copiato': 'Copiato',
        'pulsante_premio_testo': 'Dove ritirarla', 'pulsante_premio_url': NEGOZI + '#bottega', 'ricorda': False,
        'mostra_lucchetto': True, 'icona_lucchetto': '', 'stile': 'ottone', 'colore_accento': C['acc'], 'colore_successo': C['acc'],
        'colore_errore': C['acc2'], 'sfondo_pannello': C['sala'], 'raggio': radius(2), 'ombra': 'lg', 'enigma_font_family': 'heading',
        'enigma_font_size': 20, 'enigma_font_size_tablet': 19, 'enigma_font_size_mobile': 17, 'enigma_font_weight': '400',
        'enigma_color': C['testo'], 'simbolo_font_family': 'mono', 'simbolo_font_weight': '700', 'messaggio_font_family': 'sans-serif',
        'messaggio_font_size': 16, 'messaggio_font_weight': '500', 'messaggio_color': C['testo'], 'misura_ruota': 64, 'gap': 28,
        'allineamento': 'center'}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Combinazione · Genova, via di Sottoripa', ['Una borsa', 'non si apre a tutti.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Facciamo valigette e borse di cuoio dal 1953, quando il fondatore riparava le serrature dei '
                                'bauli del porto. Ognuna ha la sua serratura a combinazione d’ottone: tre ruote, mille numeri, '
                                'e la combinazione la scegliete voi.', size=17),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'La collezione', ['Quattro borse,', 'una serratura ciascuna.'], size=60)]),
        t.one([M.capi(K, t, [
            (VALI[0], 'Valigette', 'Sottoripa', '', 'Il cuoio nero, la serratura a tre ruote.', '520 €', 'nuova'),
            (BORS[3], 'Borse', 'Banchi', '', 'La borsa a mano col lucchetto d’ottone.', '380 €', ''),
            (VALI[2], 'Valigette', 'Darsena', '', 'Il cuoio miele, due serrature.', '560 €', ''),
            (BORS[1], 'Borse', 'Lanterna', '', 'Il verde petrolio, la chiusura a scatto.', '340 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il drop segreto', ['Si apre', 'con le cifre giuste.'], size=60, layout='stack', size_mobile=40),
                      K.text(t, 'Le cifre sono tutte in questa pagina. Girate le ruote con le frecce, o scriveteci sopra.', size=17)],
                     wm='1-3', ws='1-1'),
               t.col([lucchetto(t)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='drop', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([M.nastro(K, t, ['Tre ruote', 'Mille numeri', 'Ottone e cuoio', 'Genova, dal 1953'],
                                          fondo=C['acc'], colore=C['on_acc'], size=56)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le serrature', ['Ognuna', 'ha il suo segreto.'], size=60)]),
        t.one([P.pila(K, t, [
            ('Tre ruote', 'La valigetta', 'a combinazione', 'Tre ruote d’ottone sotto il manico: la combinazione la scegliete voi al '
             'ritiro, e la sappiamo solo noi e voi.', VALI[4]),
            ('A scatto', 'La borsa', 'col lucchetto', 'Un lucchetto vero, smontabile: d’estate la borsa si porta aperta.', OTTO[0]),
            ('Doppia', 'Il baule', 'del porto', 'Due serrature e la chiave: il modello che riparava il fondatore.', VALIGIA[0])],
            altezza=420)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_VALIGETTA, 'In città', 'La combinazione <em>è vostra.</em>',
                             'Al ritiro scegliete tre numeri: da quel momento la borsa si apre solo con quelli.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Addosso', ['Chiuse', 'e portate.'], size=60)]),
        P.fila_foto(K, t, [(MODE[0], 'La Banchi'), (MODE[2], 'La Lanterna in città'), (MODE[4], 'La Sottoripa'),
                           (MODE[5], 'La Darsena')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Il drop', 'ogni mese.'],
                         'La valigetta del mese, la combinazione per aprire il drop, le borse nuove. Una volta al mese.',
                         bg=solid(C['sala'])))
    return out


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione', ['Otto borse,', 'otto serrature.'],
                   'Cuoio conciato al vegetale, ottone fuso a Genova, cuciture a mano. La combinazione si sceglie al ritiro.',
                   bg=K.video_bg(V_SCAFFALI, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Valigette', ['Per lavorare,', 'e viaggiare.'], size=60)]),
        t.one([M.capi(K, t, [
            (VALI[0], 'Valigette', 'Sottoripa', '', 'Cuoio nero, tre ruote.', '520 €', 'nuova'),
            (VALI[2], 'Valigette', 'Darsena', '', 'Cuoio miele, due serrature.', '560 €', ''),
            (VALI[3], 'Valigette', 'Molo', '', 'Sottile, per il computer.', '480 €', ''),
            (VALI[5], 'Valigette', 'Ponente', '', 'Il cuoio rosso del porto.', '540 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Borse', ['Col lucchetto', 'd’ottone.'], size=60)]),
        t.one([M.capi(K, t, [
            (BORS[3], 'Borse', 'Banchi', '', 'A mano, il lucchetto.', '380 €', ''),
            (BORS[1], 'Borse', 'Lanterna', '', 'Verde petrolio.', '340 €', ''),
            (BORS[0], 'Borse', 'Caruggio', '', 'Piccola, a tracolla.', '290 €', ''),
            (BORS[5], 'Borse', 'Mercanti', '', 'Grande, da viaggio.', '420 €', 'ultime')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Settant’anni', 'di serrature.'],
                   'Nel 1953 si riparavano le serrature dei bauli che arrivavano al porto. Oggi le serrature le fondiamo noi, e '
                   'ci mettiamo intorno una borsa.', bg=K.video_bg(V_CUOIO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si fa', ['Il cuoio,', 'l’ottone, i numeri.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'il cuoio', BOTT[0], 'Tagliare', 'Il cuoio', 'a mano', 'Il cuoio conciato al vegetale si taglia a coltello sulla '
             'dima di cartone.', '4', 'ore'),
            ('2', 'l’ottone', OTTO[1], 'Fondere', 'La serratura', 'd’ottone', 'Le ruote si fondono e si incidono a Genova, una per una, '
             'coi numeri da zero a nove.', '3', 'ruote'),
            ('3', 'i numeri', LUCC[1], 'Scegliere', 'La combinazione', 'vostra', 'Al ritiro scegliete i numeri: li impostiamo davanti a '
             'voi e poi li dimentichiamo.', '1000', 'combinazioni')], icona='lock')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi conosce', 'le combinazioni.'], size=60)]),
        K.squadra(t, [(BOTT[3], 'Rinaldo Parodi', 'Fondatore', 'Nipote del fondatore: apre ancora i bauli del porto, per gioco.',
                       'center 30%'),
                      (MODE[3], 'Elena Sanguineti', 'Cuoio', 'Taglia e cuce le borse, il punto sellaio a due aghi.', 'center 25%'),
                      (BOTT[5], 'Hamid Benali', 'Ottone', 'Fonde e incide le ruote delle serrature.', 'center 30%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, CHIA[0], 'Le vecchie chiavi della bottega', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho dimenticato la combinazione della valigetta di mio padre, del 1961. In bottega l’hanno '
                                        'aperta in cinque minuti, ascoltando le ruote.', 'Marco, Savona', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove sceglierle,', 'e chiuderle.'],
                   'La combinazione si sceglie in negozio, al ritiro: ecco dove, e la bottega nei caruggi.',
                   bg=K.photo_bg(GENO[0]), size=96)]
    out.append(M.negozi(K, t, [
        ('Genova', 'Bottega Combinazione', 'via di Sottoripa 7'), ('Genova', 'Porto Antico', 'calata Cattaneo 15'),
        ('Milano', 'Serratura', 'via Brera 12'), ('Torino', 'Ruota', 'via Roma 210'), ('Roma', 'Caveau', 'via Frattina 33'),
        ('Napoli', 'Chiave', 'via Toledo 256'), ('Zurigo', 'Tresor', 'Bahnhofstrasse 41'), ('Londra', 'Combination', 'Jermyn Street 18')],
        nota='In bottega si aprono anche le serrature delle valigie dimenticate: si ascoltano le ruote, e si aprono.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una combinazione dimenticata\nUna riparazione\nVendere Combinazione\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una combinazione', 'dimenticata?'],
                                'Per una combinazione dimenticata, una riparazione, o per vendere le nostre borse.', VOCI, campi,
                                'Combinazione — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['Sotto i portici,', 'davanti al porto.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('Il drop', 'ritiro in bottega, il sabato'),
                                  ('A piedi', 'dall’Acquario, tre minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Combinazione · la bottega', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='bottega', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'sulle serrature.'], [
        ('E se dimentico la combinazione?', 'Portate la borsa in bottega: la apriamo ascoltando le ruote, e ne scegliete un’altra.'),
        ('Posso cambiarla?', 'Sì, quando volete, in bottega o in negozio: due minuti.'),
        ('Il cuoio si rovina con la pioggia?', 'Il cuoio al vegetale scurisce e si fa più bello: va solo ingrassato una volta l’anno.'),
        ('Spedite?', 'Sì, con la serratura aperta: la combinazione la scegliete voi, col foglietto dentro.'),
    ], 'co-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · combinazione sbagliata', color=C['acc'], align='center'),
                       K.headline(t, 'Questa porta <em>non si apre.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'I numeri non tornano. Proviamo dall’inizio.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_PORTA, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il caveau che si apre',
    ['moda', 'accessori', 'borse', 'valigette', 'cuoio', 'genova', 'scuro', 'slider pro', '3d', 'enigma'],
    'Una bottega di valigette e borse con la serratura a combinazione: in apertura la porta tonda di un caveau, scorrendo '
    'la manopola gira sui numeri, la ruota fa il suo giro e la porta si spalanca in 3D sulla bottega; poi il drop segreto '
    'da aprire con un lucchetto a combinazione, le cifre nascoste nella pagina, la bottega e i negozi.',
    [('La collezione', CAPI), ('La bottega', BOTTEGA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Mulish\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Marcellus\', Georgia, serif',
    '\'Azeret Mono\', ui-monospace, Menlo, monospace', ['Mulish', 'Marcellus', 'Azeret Mono'], corsivo_di=[],
    peso_titoli='400', raggio=2, grana=5, titoli_line='1.0', titoli_spazio='0.01em')
THEME['cursor']['style'] = 'quadrante'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['accent']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'la-bottega.json': build_bottega(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
