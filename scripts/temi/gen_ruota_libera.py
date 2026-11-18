# -*- coding: utf-8 -*-
"""Genera il tema «Ruota Libera» (id ruota-libera): una ciclofficina popolare a Padova, associazione di volontari. Il
martedì e il giovedì sera attrezzi e mani a disposizione; si paga quello che si può; la «bici sospesa» per chi non ce l'ha.

La trovata (18 nov 2026, utente: «un sito che mentre scorre ruota su se stesso di qualche grado, proseguendo a cerchio»):
il sito è una ruota. Nell'apertura una ruota di bici enorme spunta dal fondo e gira mentre scorri, con le scritte sul
fianco del copertone che girano con lei; poi le sezioni della home stanno sul bordo di una ruota (Sezioni → Effetto
sticky → «Ruota», 22° fra l'una e l'altra): scorrendo la ruota gira e la sezione di turno arriva dritta in cima.

Tile poco usate messe alla prova: Popover (mai usata) sulle parti della bici, Ghiera, Cesto Fisico (mai usata nei temi)
coi pezzi della bici, Lucchetto, Popup Nascosto (mai usata) col campanello «drin», Tab a
Icone (mai usata) per le stazioni dell'officina.
Tema scuro: nero del grasso, arancio, menta, giallo. Titoli in Bricolage Grotesque, testo in Figtree. Cursore: raggi.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'ruota-libera'
NAME = 'Ruota Libera'
CATEGORIA = 'Associazioni e cause'

HEX = {
    'primary': '#FF6A1A', 'primary_contrast': '#140B05', 'secondary': '#2CD3A8', 'secondary_contrast': '#06231B',
    'accent': '#F7E733', 'dark': '#0B0B0B', 'light': '#FFFFFF', 'muted': '#1F1E1C', 'muted_contrast': '#F1EEE8',
    'text': '#F1EEE8', 'text_muted': '#ABA59C', 'background': '#141413', 'border': '#34322E', 'link': '#FF8A4C',
}
HOME, OFFICINA, SOSPESA, PARTECIPA = '{{PAGINA:home}}', '{{PAGINA:l-officina}}', '{{PAGINA:bici-sospesa}}', '{{PAGINA:partecipa}}'
MAIL, PHONE, PHONE_TEL = 'ciao@ruotalibera.example', '049 000 2626', 'tel:+390490002626'
LAT, LNG = '45.4107', '11.8915'

V_RAGGI, V_OFFICINA, V_CORTILE, V_CATENA, V_CITTA, V_BUIO = (
    'pexels-video-5228664', 'pexels-video-33325059', 'pexels-video-11798539', 'pexels-video-40301261', 'pexels-video-37655314',
    'pexels-video-3069096')
VIDEOS = [V_RAGGI, V_OFFICINA, V_CORTILE, V_CATENA, V_CITTA, V_BUIO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(DUE_RIPARANO, OFFICINA_GRUPPO, VETRINA, KOFI, BRUNO, BOTTEGA_GIALLA, MANI, MANI_PEZZO, CATENA_MANI, FORCELLA, RAGGI_NERI,
 RAGGI_BIANCHI, VOLONTARI, VOLONTARI_LIBRO, CASSETTA, PEDALE, BICI_ARANCIO, BICI_CESTINO, BICI_BIMBI, MAMMA_BIMBO,
 CICLISTI, AMICHE, ATTREZZI, CHIAVI, CATENA_SEI) = f(
    34185192, 35305055, 31452612, 28100861, 16884264, 31714310, 5803351, 11890954, 13122870, 36398681, 5911706, 8446101,
    6568953, 6647025, 4530854, 4530876, 32403277, 8545563, 13246520, 8504320, 24917427, 19037122, 10016886, 210881, 1061137)
PHOTOS = [DUE_RIPARANO, OFFICINA_GRUPPO, VETRINA, KOFI, BRUNO, BOTTEGA_GIALLA, MANI, MANI_PEZZO, CATENA_MANI, FORCELLA,
          RAGGI_NERI, RAGGI_BIANCHI, VOLONTARI, VOLONTARI_LIBRO, CASSETTA, PEDALE, BICI_ARANCIO, BICI_CESTINO, BICI_BIMBI,
          MAMMA_BIMBO, CICLISTI, AMICHE, ATTREZZI, CHIAVI, CATENA_SEI]
PEZZI = ['ruota-libera-pezzo-%s.svg' % p for p in ('ingranaggio', 'dado', 'campanello', 'maglia', 'pedale', 'valvola')]
DISEGNI = ['ruota-libera-ruota.svg'] + PEZZI
LOGHI = ['ruota-libera-logo-chiaro.png', 'ruota-libera-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=12, peso_titoli='800', corsivo=False, maiuscole_pulsanti=False, accento_su_media='#F7E733')
C = K.c
FONT_T = 'Bricolage Grotesque'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Via del Portello 12 · Padova', PARTECIPA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 196, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '682/119',
                   'Ruota Libera è una ciclofficina popolare: un’associazione di volontari che ti insegna ad aggiustare la '
                   'tua bici, con gli attrezzi di tutti. Si paga quello che si può.',
                   [('L’officina', OFFICINA), ('La bici sospesa', SOSPESA), ('Partecipa', PARTECIPA),
                    ('Privacy', '/privacy-policy/')],
                   'Quando', 'Martedì e giovedì 18 — 21<br>Sabato 10 — 13, solo bici sospese', VOCI,
                   '© 2026 Ruota Libera aps · Padova · C.F. 00000000000', [('instagram', 'https://instagram.com'),
                                                                          ('facebook', 'https://facebook.com')], scuro=True)


# ── L'apertura: la ruota che spunta dal fondo e gira ──
# Computer (tela 1440 × 810): la ruota nel riquadro x 14, y 44, 72 × 128 % (1037 × 1037 px), centro a 50 % × 108 %. Le
# scritte sul fianco del copertone sono riquadri alti 3 % col perno al centro della ruota: (108 - 45,7) / 3 = 2077 % del
# loro riquadro, in percentuale così vale su ogni tela. Telefono (tela 640 × 1400): ruota x -25, y 50, 150 × 68,57 %
# (960 px), centro a 84,3 %; scritte alte 1,8 % a y 50,8 → perno a 1861 %.
def apertura(t):
    m = K.mob
    poi = lambda da, a, via_da=None, via_a=None: {'inizio': 0, 'fine': 100, 'tappe': (
        [{'p': 0, 'o': 0, 'y': 18}, {'p': da, 'o': 0, 'y': 18}, {'p': a, 'o': 100, 'y': 0}]
        + ([{'p': via_da, 'o': 100, 'y': 0}, {'p': via_a, 'o': 0, 'y': -18}, {'p': 100, 'o': 0, 'y': -18}] if via_da
           else [{'p': 100, 'o': 100, 'y': 0}]))}
    via = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100, 'y': 0}, {'p': da, 'o': 100, 'y': 0},
                                                             {'p': a, 'o': 0, 'y': -18}, {'p': 100, 'o': 0, 'y': -18}]}
    gira = lambda da: {'inizio': 0, 'fine': 100, 'andamento': 'costante', 'tappe': [{'p': 0, 'r': da}, {'p': 100, 'r': da + 240}]}
    L = K.veli('rl-velo', 'alto', 36, 62, colore=C['nero'], forza=88, mobile_alto=43)
    L += [
        K.livello('rl-ruota', tipo='image', imageSrc=K.img('ruota-libera-ruota.svg'), imageAlt='Una ruota di bici che gira',
                  objectFit='contain', x=14, y=44, w=72, h=128, entra='fadeInUp', ritardo=200, durata=1200,
                  customCSS='transform-origin:50% 50%;', scrollMove=gira(0),
                  responsive={'mobile': {'x': -25, 'y': 50, 'width': 150, 'height': 68.57}}),
    ]
    # sul telefono scritte più corte e più vicine: a ±34° uscivano dallo schermo («uota Libera», «Padova · 28 × 1»)
    # e «ciclofficina popolare» andava su due righe
    for k, (testo, ang, tel, ang_tel) in enumerate((('Ruota Libera', -34, 'Ruota Libera', -27),
                                                    ('ciclofficina popolare', 0, 'ciclofficina', 0),
                                                    ('Padova · 28 × 1,75', 34, 'Padova', 27))):
        comune = dict(contenuto=testo, size=14, peso='700', colore=mix(C['chiaro'], 82), align='center', letterSpacing=4,
                      textTransform='uppercase', fontFamily=MONO, lineHeight=1.0, entra='fadeIn', ritardo=900, durata=900,
                      scrollMove=gira(ang))
        L.append(K.livello('rl-fianco%d' % k, x=39, y=45.7, w=22, h=3, customCSS='transform-origin:50% 2077%;',
                           responsive={'mobile': {'visible': False}}, **comune))
        # la stessa scritta per il telefono, col perno sul centro della ruota del telefono: nascosta al computer
        # (prima era un riquadro di 0,1 % nell'angolo e le lettere, una per riga, giravano accanto al logo)
        L.append(K.livello('rl-fianco%dt' % k, x=30, y=50.8, w=40, h=1.8, visible=False,
                           customCSS='transform-origin:50% 1861%;',
                           responsive={'mobile': {'visible': True, 'x': 30, 'y': 50.8, 'width': 40, 'height': 1.8,
                                                  'fontSize': 20}},
                           **dict(comune, contenuto=tel, letterSpacing=3, scrollMove=gira(ang_tel))))
    L += [
        K.livello('rl-o', contenuto='Ruota Libera · ciclofficina popolare · Padova', x=10, y=9, w=80, size=13, peso='600',
                  colore=mix(C['chiaro'], 86), align='center', letterSpacing=3, textTransform='uppercase', fontFamily=MONO,
                  ritardo=150, responsive={'mobile': {'fontSize': m(10), 'x': 4, 'y': 12.4, 'width': 92}}),
        # prima frase
        K.livello('rl-t1', contenuto='La bici si aggiusta.', tag='h1', x=6, y=14, w=88, size=92, peso='800',
                  colore=C['chiaro'], align='center', fontFamily=FONT_T, lineHeight=1.0, entra='fadeInUp', ritardo=300,
                  durata=900, scrollMove=via(26, 34), responsive={'mobile': {'fontSize': m(46), 'x': 5, 'y': 16.0, 'width': 90}}),
        K.livello('rl-t1b', contenuto='Insieme.', x=6, y=26, w=88, size=92, peso='800', colore=C['acc'], align='center',
                  fontFamily=FONT_T, lineHeight=1.0, entra='fadeInUp', ritardo=450, durata=900, scrollMove=via(26, 34),
                  responsive={'mobile': {'fontSize': m(46), 'x': 5, 'y': 27.6, 'width': 90}}),
        K.livello('rl-sub', contenuto='Porti la tua bici, trovi gli attrezzi e qualcuno che ti mostra come si fa.',
                  x=24, y=38.5, w=52, size=20, peso='500', colore=mix(C['chiaro'], 90), align='center', lineHeight=1.4,
                  ritardo=600, scrollMove=via(26, 34), responsive={'mobile': {'fontSize': m(16), 'x': 8, 'y': 34.0, 'width': 84}}),
        # seconda frase
        K.livello('rl-t2', contenuto='Si paga<br>quello che si può.', x=6, y=15, w=88, size=88, peso='800', colore=C['chiaro'],
                  align='center', fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=poi(36, 44, 60, 66),
                  responsive={'mobile': {'fontSize': m(44), 'x': 5, 'y': 17.0, 'width': 90}}),
        K.livello('rl-t2s', contenuto='Un’offerta per gli attrezzi e i ricambi: chi non può, non paga.', x=24, y=38, w=52,
                  size=20, peso='500', colore=mix(C['chiaro'], 90), align='center', lineHeight=1.4, entra='fadeIn', ritardo=0,
                  scrollMove=poi(38, 46, 60, 66), responsive={'mobile': {'fontSize': m(16), 'x': 8, 'y': 35.4, 'width': 84}}),
        # terza frase e pulsanti
        K.livello('rl-t3', contenuto='Martedì e giovedì,<br>dalle 18 alle 21.', x=6, y=15, w=88, size=88, peso='800',
                  colore=C['acc2'], align='center', fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0,
                  scrollMove=poi(68, 76), responsive={'mobile': {'fontSize': m(44), 'x': 5, 'y': 17.0, 'width': 90}}),
        K.livello('rl-b1', tipo='button', contenuto='Vieni in officina', x=36, y=39, w='auto', size=15, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=999, buttonUrl=OFFICINA, entra='fadeIn',
                  ritardo=0, scrollMove=poi(76, 84), hover={'bgColor': C['acc2'], 'duration': 250},
                  responsive={'mobile': {'x': 8, 'y': 36.6, 'fontSize': m(13)}}),
        K.livello('rl-b2', tipo='button', contenuto='La bici sospesa', x=52, y=39, w='auto', size=15, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=18, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), buttonUrl=SOSPESA, entra='fadeIn', ritardo=0, scrollMove=poi(76, 84),
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 52, 'y': 36.6, 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('rl-1', K.sfondo_video(V_OFFICINA, overlay=0.32, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── le sezioni sul bordo della ruota ──
def raggio(t, foto, occhiello, titolo, testo, link=None, pos='center center', destra=False):
    """Una sezione della ruota: la foto piena e la scheda scura col testo."""
    blocco = [K.label(t, occhiello, color=C['acc2']),
              K.headline(t, titolo, size=60, tag='h2', pad_bottom=14, color=C['chiaro'], accento=C['acc'], sizes=(46, 36)),
              K.text(t, testo, size=16, color=mix(C['chiaro'], 88))]
    if link:
        blocco.append(K.button(t, link[0], link[1], kind='pieno', pad_top=12))
    scheda = {'bg': solid(mix(C['nero'], 88)), 'padding_top': 38, 'padding_right': 42, 'padding_bottom': 36,
              'padding_left': 42, 'border_radius': '12px'}
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=scheda), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='50,50', valign='center', anim=None)],
                     bg=K.photo_bg(foto, overlay=12, position=pos), padding='large', width='large',
                     sticky_effect='ruota', sticky_ruota_gradi=22, sticky_h_dir='destra', sticky_h_align='center',
                     style={'padding_top': 110, 'padding_bottom': 80})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Ciclofficina popolare · dal 2011', ['Una ciclofficina,', 'non un negozio.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Qui la bici non la lasci: la aggiusti tu, con le nostre mani accanto alle tue. Gli attrezzi '
                                'sono di tutti, i ricambi usati costano un’offerta, e la seconda volta la camera d’aria la '
                                'cambi da solo.', size=17),
                      K.button(t, 'Come funziona', OFFICINA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    # la ruota: sei sezioni sul bordo
    out += [
        raggio(t, OFFICINA_GRUPPO, 'Il martedì e il giovedì', 'La porta <em>è aperta.</em>', 'Dalle 18 alle 21 l’officina è '
               'di chi entra: sei cavalletti, due banchi, una parete di chiavi. Non serve prenotare.'),
        raggio(t, MANI, 'La prima cosa che impari', 'La camera <em>d’aria.</em>', 'Smonti la ruota, trovi il buco nella '
               'bacinella, metti la toppa. Dieci minuti, e non hai più bisogno di nessuno.', destra=True),
        raggio(t, FORCELLA, 'Quelli che stridono', 'I <em>freni.</em>', 'Pattini consumati, cavi lenti: si regolano con una '
               'chiave da 10 e un po’ di pazienza. Te la diamo noi, la pazienza.'),
        raggio(t, CATENA_MANI, 'Quella che salta', 'La <em>catena.</em>', 'Si pulisce, si oliva, si cambia quando è lunga. '
               'Abbiamo il calibro per misurarla: se passa il segno, è da cambiare.', destra=True),
        raggio(t, BICI_CESTINO, 'Per chi esce di sera', 'Le <em>luci.</em>', 'Chi entra senza luci esce con le luci: ne '
               'abbiamo una scatola, regalate da chi è passato prima di te.'),
        raggio(t, BICI_BIMBI, 'Per chi non ce l’ha', 'La bici <em>sospesa.</em>', 'Ripariamo le bici che ci regalano e le '
               'diamo a chi ne ha bisogno, come il caffè sospeso di Napoli.', link=('La bici sospesa', SOSPESA), destra=True),
    ]
    # le parti della bici (Popover: i segnaposto sulla foto)
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Prima lezione', ['Come si chiama', 'quel pezzo?'], size=60, layout='stack', size_mobile=38),
                      K.text(t, 'Tocca i punti sulla bici: il nome di ogni parte e la cosa che si rompe più spesso. Martedì '
                                'te li facciamo vedere dal vero.', size=17)], wm='1-3', ws='1-1'),
               t.col([t.bare('popover', {
                   'preset': 'custom', 'image': K.img(BICI_ARANCIO), 'image_alt': 'Una bici da città arancione appoggiata al muro',
                   'image_height': '560', 'object_position': 'center center',
                   'markers': [
                       {'id': 'rl-mk-1', 'x': 73, 'y': 31, 'title': 'La sella', 'content': 'Si alza finché, col pedale in basso, '
                        'la gamba è quasi dritta. Quasi tutti la tengono troppo bassa.', 'image': ''},
                       {'id': 'rl-mk-2', 'x': 41, 'y': 35, 'title': 'Il freno davanti', 'content': 'Frena il 70% della bici. '
                        'Se la leva tocca la manopola, il cavo è lento.', 'image': ''},
                       {'id': 'rl-mk-3', 'x': 63, 'y': 65, 'title': 'La catena', 'content': 'Se fa rumore è secca: due gocce '
                        'd’olio, poi si asciuga con lo straccio.', 'image': ''},
                       {'id': 'rl-mk-4', 'x': 26, 'y': 73, 'title': 'Il copertone', 'content': 'Sul fianco c’è scritta la misura, '
                        'per esempio 28 × 1,75: serve per comprare la camera d’aria giusta.', 'image': ''}],
                   'marker_color': C['acc'], 'popup_bg': C['nero'], 'popup_color': C['testo'], 'popup_radius': '12',
                   'popup_img_height': '0', 'shadow': 'lg'})], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    # Ghiera: che cosa ti serve
    voce = lambda sig, tit, testo, prezzo, foto, link, tl: {'sigla': sig, 'titolo': tit, 'testo': testo, 'prezzo': prezzo,
                                                            'immagine': K.img(foto), 'video': '', 'link': link, 'testo_link': tl}
    out.append(t.section([
        t.one([K.sechead(t, 'Gira la ghiera', ['Che cosa', 'ti serve?'], size=60, align='center')]),
        t.one([t.classic('ghiera', {
            'voci': [voce('C', 'Una camera d’aria', 'La cambiamo insieme. La camera nuova costa quello che costa a noi.',
                          '4 €', MANI, OFFICINA, 'Vieni martedì'),
                     voce('F', 'Un freno che non frena', 'Pattini, cavo, leva: in mezz’ora la bici si ferma di nuovo.',
                          'offerta libera', FORCELLA, OFFICINA, 'Vieni giovedì'),
                     voce('R', 'Una ruota storta', 'La mettiamo sul centraruote e tiriamo i raggi uno alla volta.',
                          'offerta libera', RAGGI_NERI, OFFICINA, 'Vieni martedì'),
                     voce('B', 'Una bici intera', 'Se non ce l’hai, chiedi la bici sospesa: è aggiustata e ti aspetta.',
                          'gratis', BICI_BIMBI, SOSPESA, 'La bici sospesa'),
                     voce('M', 'Mani in più', 'Se sai già girare una chiave, l’officina ha bisogno di te il sabato.',
                          'il tuo tempo', VOLONTARI, PARTECIPA, 'Partecipa')],
            'partenza': 1, 'titolo_centro': 'Ruota Libera', 'mostra_numero': True, 'etichetta': 'Gira la ghiera',
            'stile': 'metallo', 'colore_ghiera': C['sala'], 'colore_sigle': C['testo'], 'colore_indice': C['acc'],
            'sfondo_pannello': mix(C['chiaro'], 5), 'colore_pulsante': C['acc'], 'colore_testo_pulsante': C['on_acc'],
            'titolo_font_family': 'heading', 'titolo_color': C['testo'], 'testo_color': C['tenue']}, style={})], anim=None),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    # Cesto Fisico: il cassetto dei pezzi
    pezzo = lambda n, r: {'id': 'rl-pz-%d' % n, 'shape': 'circle', 'color': C['acc'], 'radius': str(r), 'glyph': '',
                          'image': K.img(PEZZI[n % len(PEZZI)])}
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il cassetto dei pezzi', ['Tutto quello che', 'si svita, cade.'], size=60, layout='stack',
                                size_mobile=38),
                      K.text(t, 'Ingranaggi, dadi, campanelli, maglie di catena, pedali, valvole: il cassetto sotto il banco '
                                'è così. Prendili e tirali, o scuoti il cassetto.', size=17)], wm='1-3', ws='1-1'),
               t.col([t.bare('physicsbin', {
                   'preset': 'custom', 'items': [pezzo(k, r) for k, r in enumerate((46, 38, 42, 34, 44, 30, 40, 36, 48, 32,
                                                                                     38, 42))],
                   'gravity': '0.6', 'restitution': '0.55', 'friction': '0.99', 'walls': True, 'spawn': 'random',
                   'max_items': '12', 'hint_text': 'Trascina un pezzo e lancialo', 'shake': True,
                   'shake_label': 'Scuoti il cassetto', 'chemistry': False, 'molecules': [], 'bg_color': C['nero'],
                   'height': '460', 'border_radius': {'tl': 18, 'tr': 18, 'br': 18, 'bl': 18}, 'shadow': 'none'})],
                   wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    # Lucchetto: l'indovinello della bici sospesa
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il lucchetto', ['Apri il lucchetto,', 'vinci una camera d’aria.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Quattro cifre. Chi le trova ha una camera d’aria nuova, gratis, il prossimo martedì.',
                             size=17)], wm='2-5', ws='1-1'),
               t.col([t.classic('lucchetto', {
                   'enigma': '<p><strong>Quattro cifre</strong>, in quest’ordine:</p><p>le ruote di un triciclo, le selle di '
                             'un tandem, i campanelli di una bici, le sere della settimana in cui siamo aperti.</p>',
                   'indizio': 'Il triciclo ha una ruota in più della bici.', 'testo_indizio': 'Un indizio',
                   'combinazione': '3212', 'simboli': 'cifre', 'testo_pulsante': 'Apri', 'messaggio_errore': 'Non si apre. '
                   'Riprova.', 'messaggio_successo': 'Aperto! Il codice qui sotto vale una camera d’aria nuova al prossimo '
                   'martedì.', 'codice_premio': 'DRIN3212', 'testo_copia': 'Copia il codice', 'testo_copiato': 'Copiato',
                   'tentativi_indizio': 2, 'mostra_lucchetto': True, 'stile': 'ottone', 'colore_accento': C['acc'],
                   'sfondo_pannello': mix(C['chiaro'], 5)}, style={})], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    # il campanello nascosto (Popup Nascosto: scrivi «drin»). Il Contatore Visite era qui: nelle demo resta a 0 (le pagine
    # demo non hanno un post da contare) e con la cache a pagina intera conta solo quando la cache si rifà; tolto.
    out.append(t.section([
        t.one([K.label(t, 'C’è un campanello nascosto', color=C['acc2'], align='center'),
               K.text(t, 'Scrivi «drin» sulla tastiera, in qualsiasi punto della pagina.', size=17, align='center',
                      color=C['testo'], pad=(10, 0, 0, 0)),
               t.bare('hiddenpop', {'mode': 'simple', 'title': 'Drin drin!', 'subtitle': 'Hai suonato il campanello. Vieni a '
                                    'sentirlo dal vero, martedì alle 18: il primo caffè lo offriamo noi.',
                                    'cta_text': 'Dove siamo', 'cta_url': PARTECIPA + '#zone', 'cta_style': 'primary',
                                    'scroll_trigger': False, 'exit_intent': False, 'key_sequence': True,
                                    'key_sequence_keys': 'drin', 'key_sequence_confetti': True,
                                    'confetti_color_1': HEX['primary'], 'confetti_color_2': HEX['secondary'],
                                    'confetti_color_3': HEX['accent'], 'popup_frequency': 'always',
                                    'modal_bg_color': C['sala'], 'title_color': C['testo'], 'text_color': C['testo'],
                                    'modal_radius': '18'})], anim=None),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(X.recensioni(K, t, [
        ('Ero entrata per una foratura. Adesso il martedì sono io che insegno a cambiare la camera d’aria.', 'Chiara',
         'Arcella'),
        ('La mia bici aveva vent’anni e i freni di nessuno. Bruno me l’ha fatta tornare come nuova, con le mie mani.', 'Matteo',
         'Portello'),
        ('Mio figlio ha avuto la sua prima bici dalla bici sospesa. L’abbiamo dipinta noi, gialla.', 'Fatima', 'Stanga')],
        'Chi è passato', ['Tre bici,', 'tre persone che adesso pedalano.']))
    out.append(K.pannello(t, OFFICINA_GRUPPO, 'Martedì e giovedì, 18 — 21', 'Porta la bici, <em>e le mani.</em>',
                          'Non serve saper fare niente: è il posto dove si impara. Siamo in via del Portello 12.',
                          'Dove siamo', PARTECIPA + '#zone', effetto=''))
    return out


def build_officina():
    t = Tpl(ID, 'l-officina')
    out = [K.intro(t, 'L’officina', ['Gli attrezzi ci sono,', 'le mani anche.'], 'Cinque stazioni, sei cavalletti e una '
                   'parete di chiavi. Tu porti la bici, noi ti diciamo da dove cominciare.', bg=K.video_bg(V_CORTILE, overlay=22),
                   size=104)]
    tab = lambda n, icona, lab, testa, tit, testo: {'id': 'rl-tab-%d' % n, 'icon': icona, 'label': lab, 'heading': testa,
                                                     'title': tit, 'content': testo, 'link_text': '', 'link_url': ''}
    out.append(t.section([
        t.one([K.sechead(t, 'Le stazioni', ['Cinque posti,', 'un lavoro ciascuno.'], size=60)]),
        t.one([t.bare('icontabs', {
            'items': [tab(1, 'gauge', 'La pompa', 'Stazione 1', 'Gomme alla pressione giusta',
                          'Il numero è scritto sul fianco del copertone. Attacco per valvole auto, presta e Dunlop.'),
                      tab(2, 'wrench', 'Il cavalletto', 'Stazione 2', 'La bici sospesa a mezz’aria',
                          'Sei cavalletti: la bici si blocca all’altezza giusta e le ruote girano libere.'),
                      tab(3, 'cog', 'Il banco', 'Stazione 3', 'Mozzi, movimenti, cuscinetti',
                          'Il lavoro di precisione: le chiavi coniche, l’estrattore, la morsa con le ganasce morbide.'),
                      tab(4, 'droplets', 'Il lavaggio', 'Stazione 4', 'Catena e ingranaggi puliti',
                          'Sgrassatore, spazzole, stracci. Una catena pulita dura il doppio.'),
                      tab(5, 'package', 'I ricambi', 'Stazione 5', 'Usati, controllati, a offerta',
                          'Copertoni, selle, parafanghi, luci: tutto quello che smontiamo dalle bici donate.')],
            'preset': 'pill-default', 'pill_bg': C['sala'], 'active_bg': C['acc'], 'active_color': C['on_acc'],
            'inactive_color': C['testo'], 'card_bg': C['sala'], 'card_radius': '14', 'heading_color': C['acc2'],
            'title_color': C['testo'], 'text_color': C['tenue'], 'default_index': '0'})], anim=None),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi c’è', ['Le mani', 'del martedì e del giovedì.'], size=60)]),
        K.squadra(t, [(KOFI, 'Kofi', 'Il martedì', 'Ha imparato qui dieci anni fa con una bici rubata e ritrovata. Adesso '
                       'insegna le ruote.', 'center 30%'),
                      (BRUNO, 'Bruno', 'Il giovedì', 'Quarant’anni da meccanico di bici in Prato della Valle. In pensione, '
                       'non ha smesso.', 'center 30%'),
                      (VOLONTARI, 'I volontari', 'Il sabato', 'Studenti, pensionati, ciclisti della domenica: preparano le '
                       'bici sospese.', 'center 12%')], ratio='4/5', size='300'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([X.orari(K, t, [('Martedì', '18 — 21', 'Ruote, camere d’aria, freni'),
                                                ('Giovedì', '18 — 21', 'Cambi, catene, movimenti'),
                                                ('Sabato', '10 — 13', 'Solo le bici sospese, porte chiuse')])])],
                         padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 60}))
    out.append(K.domande(t, 'Prima di venire', ['Quello che', 'ci chiedono sempre.'], [
        ('Devo saper fare qualcosa?', 'No. La prima volta ti affianca qualcuno e fai tu, con le sue indicazioni.'),
        ('Quanto costa?', 'Gli attrezzi sono gratis. Per i ricambi chiediamo un’offerta: quello che costano a noi, se puoi.'),
        ('Mi aggiustate la bici mentre aspetto?', 'No: non siamo un negozio. La aggiusti tu, noi ti stiamo accanto.'),
        ('Posso portare dei pezzi?', 'Sì: se sono buoni finiscono nella cassetta dei ricambi, a disposizione di tutti.')],
        'rlo'))
    return out


def build_sospesa():
    t = Tpl(ID, 'bici-sospesa')
    out = [K.intro(t, 'La bici sospesa', ['Una bici aggiustata', 'per chi non ce l’ha.'], 'Come il caffè sospeso: qualcuno '
                   'lascia una bici che non usa, noi la rimettiamo in strada, e la diamo a chi ne ha bisogno per andare a '
                   'scuola o al lavoro.', bg=K.video_bg(V_CITTA, overlay=22), size=104)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Quattro passaggi,', 'una bici in più per strada.'], size=60)]),
        P.passi(K, t, [('01', 'La lasci', 'Una bici che non usi, anche rotta. La porti il sabato mattina o veniamo a prenderla.'),
                       ('02', 'La aggiustiamo', 'Il sabato i volontari la smontano, la puliscono, cambiano quello che serve.'),
                       ('03', 'Aspetta', 'Con la sua etichetta: la misura, il tipo, chi l’ha aggiustata.'),
                       ('04', 'Parte', 'La diamo a chi ce la chiede tramite le scuole, i servizi sociali e lo sportello '
                        'del quartiere.')], colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([t.one([K.numeri(t, [('312', 'bici date dal 2016'), ('48', 'sabati all’anno'), ('9', 'scuole con cui '
                                                                                                        'lavoriamo'),
                                              ('0 €', 'per chi la riceve')], colonne=4, size=58)])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(t.section([t.one([P.citazione(K, t, 'Con la bici arrivo a scuola in dodici minuti. Prima prendevo due '
                                                   'autobus.', 'Youssef, 15 anni, bici sospesa n. 287')])],
                         padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.pannello(t, BICI_ARANCIO, 'Il sabato, 10 — 13', 'Hai una bici <em>in cantina?</em>', 'Portacela anche rotta, '
                          'anche senza ruote. O scrivici e passiamo noi.', 'Scrivici', PARTECIPA + '#scrivi', effetto=''))
    return out


def build_partecipa():
    t = Tpl(ID, 'partecipa')
    out = [K.intro(t, 'Partecipa', ['Dai una mano,', 'o una bici.'], 'Servono volontari il sabato, bici in cantina, ricambi '
                   'buoni e qualche euro per gli attrezzi. Scrivici quello che puoi.', bg=K.video_bg(V_CATENA, overlay=22),
                   size=104)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Vorrei', 'vorrei', True, opzioni='Fare il volontario\nDonare una bici\nChiedere una bici '
                     'sospesa\nAltro'),
             K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scrivici', ['Ti rispondiamo', 'entro il martedì.'],
                                'Leggiamo i messaggi prima di aprire l’officina.', VOCI, campi, 'Ruota Libera — partecipa',
                                'Invia', 'Grazie! Ti rispondiamo entro martedì.'))
    out.append(X.zone(K, t, 'Dove siamo', ['Al Portello,', 'sotto il portico.'], 'Il portone verde accanto alla farmacia. '
                      'C’è la rastrelliera davanti, ovviamente.', [('Tram', 'fermata Portello, linea 1'),
                      ('Bus', 'linee 3 e 13'), ('Bici', 'rastrelliera da dodici posti'), ('Telefono', PHONE)],
                      LAT, LNG, 'Ruota Libera · via del Portello 12', True, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · foratura', color=C['acc2'], align='center'),
                       K.headline(t, 'Hai <em>forato.</em>', size=96, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc']),
                       K.text(t, 'Questa pagina non c’è. La camera d’aria si cambia in dieci minuti: torna alla home e '
                                 'riparti. E se ti annoi, scrivi «drin».', size=17, align='center',
                              color=mix(C['chiaro'], 88)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_BUIO, overlay=30), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito che gira',
    ['associazione', 'ciclofficina', 'bici', 'volontari', 'padova', 'scuro', 'slider pro', 'ruota', 'scorrimento'],
    'Una ciclofficina popolare a Padova: nell’apertura una ruota di bici enorme spunta dal fondo e gira mentre scorri, con '
    'le scritte sul fianco del copertone; poi le sezioni della home stanno sul bordo di una ruota che gira, e quella di turno '
    'arriva dritta in cima. Le parti della bici da toccare, la ghiera dei servizi, il cassetto dei pezzi da scuotere, il '
    'lucchetto da aprire e un campanello nascosto.',
    [('L’officina', OFFICINA), ('La bici sospesa', SOSPESA), ('Partecipa', PARTECIPA)],
    [('home', 'Home', None), ('l-officina', 'L’officina', 'l-officina'), ('bici-sospesa', 'La bici sospesa', 'bici-sospesa'),
     ('partecipa', 'Partecipa', 'partecipa')],
    HEX, '\'Figtree\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bricolage Grotesque\', system-ui, sans-serif',
    'ui-monospace, Menlo, monospace', ['Figtree', 'Bricolage Grotesque'], corsivo_di=[],
    peso_titoli='800', raggio=12, grana=4, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'raggi'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['secondary']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'senza'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'l-officina.json': build_officina(), 'bici-sospesa.json': build_sospesa(),
                        'partecipa.json': build_partecipa(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
