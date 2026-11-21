# -*- coding: utf-8 -*-
"""Genera il tema «Zapping» (id zapping): un cineclub a Torino, in una ex sala parrocchiale. Pellicole il giovedì,
rassegne, il cineforum dei ragazzi, un archivio di VHS da prendere in prestito, la tessera annuale.

La trovata (18 nov 2026, sera; utente: «cerca di farne altri 5»): il sito cambia canale. Nell'apertura un televisore
arancione: scorrendo lo schermo si spegne come un vecchio tubo (si schiaccia in una riga, poi in un punto) e si riaccende
su un altro canale. Poi le sezioni della home sono canali (Sezioni → Effetto sticky → «Canali»): fra l'una e l'altra la
neve del televisore, e in alto a destra il numero del canale e il suo nome, verde come gli OSD di una volta.

Tile poco usate messe alla prova: North Video Hero (mai usata) col trailer della settimana, Pianta della sala a scatola,
Montaggio (il corso del sabato), ASCII Viz (la radio del cineclub), Media CTA (mai usata) per la tessera, Carosello.
Tema scuro: nero del tubo, rosso, verde fosforo. Titoli in Bungee, testo in Space Grotesk, mono Space Mono.
Cursore: telecomando.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'zapping'
NAME = 'Zapping'
CATEGORIA = 'Cultura e formazione'

HEX = {
    'primary': '#FF5A4E', 'primary_contrast': '#1A0504', 'secondary': '#2E6BFF', 'secondary_contrast': '#FFFFFF',
    'accent': '#7CFF6B', 'dark': '#060807', 'light': '#FFFFFF', 'muted': '#131816', 'muted_contrast': '#EAF2EC',
    'text': '#EAF2EC', 'text_muted': '#9DAAA2', 'background': '#0A0D0C', 'border': '#26302B', 'link': '#7CFF6B',
}
HOME, PROGRAMMA, SALA, TESSERA = '{{PAGINA:home}}', '{{PAGINA:il-programma}}', '{{PAGINA:la-sala}}', '{{PAGINA:la-tessera}}'
MAIL, PHONE, PHONE_TEL = 'sala@zapping.example', '011 000 0909', 'tel:+390110000909'
LAT, LNG = '45.0703', '7.6869'

V_TV, V_NEVE, V_PUBBLICO, V_PROIETTORE, V_VHS, V_TV_BUIO, V_SALA = (
    'pexels-video-6976087', 'pexels-video-15069622', 'pexels-video-7986770', 'pexels-video-856593', 'pexels-video-10599677',
    'pexels-video-10177084', 'pexels-video-7988642')
VIDEOS = [V_TV, V_NEVE, V_PUBBLICO, V_PROIETTORE, V_VHS, V_TV_BUIO, V_SALA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(TV_PILA, TV_VECCHIA, TV_GRANDE, TV_MOBILE, TV_PARETE, TV_GIALLA, TV_NEVE, TV_STANZA, PUBBLICO_3D, SCHERMO, PUBBLICO,
 DUE_POPCORN, SALA_BLU, PROIETTORI, PROIETTORE, BOBINE, VHS_TV, VHS, VHS_LEGNO, STANZA_TV, SALOTTO, POLTRONA, POPCORN_BOBINA,
 POPCORN, MOLE, TORINO_NOTTE, BOBINA, PELLICOLA, TELECOMANDO, TELECOMANDO_TASTI, MONITOR, MONITOR_FILA, POLTRONE_ROSSE,
 FILE_ROSSE) = f(
    38149593, 333984, 33805706, 16707770, 9681642, 6977377, 40072321, 15573679, 7991266, 7991579, 7991130, 8263369, 7513412,
    19374140, 34084878, 5826828, 10599804, 12341046, 157541, 32559667, 27307265, 8024438, 8652476, 39863137, 33040847,
    28708294, 6762236, 390089, 5202959, 14770618, 8875613, 9140600, 25857614, 31593645)
PHOTOS = [TV_PILA, TV_VECCHIA, TV_GRANDE, TV_MOBILE, TV_PARETE, TV_GIALLA, TV_NEVE, TV_STANZA, PUBBLICO_3D, SCHERMO, PUBBLICO,
          DUE_POPCORN, SALA_BLU, PROIETTORI, PROIETTORE, BOBINE, VHS_TV, VHS, VHS_LEGNO, STANZA_TV, SALOTTO, POLTRONA,
          POPCORN_BOBINA, POPCORN, MOLE, TORINO_NOTTE, BOBINA, PELLICOLA, TELECOMANDO, TELECOMANDO_TASTI, MONITOR,
          MONITOR_FILA, POLTRONE_ROSSE, FILE_ROSSE]
DISEGNI = ['zapping-tv-2.svg']
LOGHI = ['zapping-logo-chiaro.png', 'zapping-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=8, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, accento_su_media='#7CFF6B')
C = K.c
FONT_T = 'Bungee'
MONO = 'var(--olo-font-family-mono)'
VERDE = C['acc2']
VOCI = [('map-pin', 'Via Nizza 120 · Torino', SALA + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 190, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '588/114',
                   'Zapping è un cineclub di quartiere a Torino: pellicole il giovedì, rassegne il fine settimana, il '
                   'cineforum dei ragazzi e un archivio di VHS da prendere in prestito con la tessera.',
                   [('Il programma', PROGRAMMA), ('La sala', SALA), ('La tessera', TESSERA), ('Privacy', '/privacy-policy/')],
                   'In sala', 'Giovedì — domenica<br>porte alle 20:30, film alle 21', VOCI,
                   '© 2026 Zapping aps · Torino · C.F. 00000000000', [('instagram', 'https://instagram.com'),
                                                                     ('youtube', 'https://youtube.com')], scuro=True)


# ── L'apertura: il televisore che cambia canale ──
# Il televisore (disegno 1000 × 760, lo schermo è un buco da 9,5 a 70,5 % in larghezza e da 16 a 84 % in altezza) sta a
# destra: computer x 44, y 18, 50 × 67,5 % → schermo x 48,75, y 28,8, 30,5 × 45,9 %; telefono x 6, y 44, 88 × 30,6 % →
# schermo x 14,4, y 48,9, 53,7 × 20,8 %. Sotto il buco, tre canali (livelli video): ognuno si spegne come un tubo
# catodico (sy fino a una riga, poi sx fino a un punto) e il successivo si accende al contrario.
def apertura(t):
    m = K.mob
    schermo = dict(x=48.75, y=28.8, w=30.5, h=45.9)
    schermo_tel = {'x': 14.4, 'y': 48.9, 'width': 53.7, 'height': 20.8}

    def canale(accende, spegne):
        tappe = []
        if accende is not None:
            a = accende
            tappe += [{'p': 0, 'o': 0, 'sx': 0.02, 'sy': 0.01}, {'p': a, 'o': 0, 'sx': 0.02, 'sy': 0.01},
                      {'p': a + 0.5, 'o': 100, 'sx': 0.02, 'sy': 0.01}, {'p': a + 2, 'o': 100, 'sx': 1, 'sy': 0.012},
                      {'p': a + 5, 'o': 100, 'sx': 1, 'sy': 1}]
        else:
            tappe += [{'p': 0, 'o': 100, 'sx': 1, 'sy': 1}]
        if spegne is not None:
            s = spegne
            tappe += [{'p': s, 'o': 100, 'sx': 1, 'sy': 1}, {'p': s + 3, 'o': 100, 'sx': 1, 'sy': 0.012},
                      {'p': s + 5, 'o': 100, 'sx': 0.02, 'sy': 0.01}, {'p': s + 5.5, 'o': 0, 'sx': 0.02, 'sy': 0.01},
                      {'p': 100, 'o': 0, 'sx': 0.02, 'sy': 0.01}]
        else:
            tappe += [{'p': 100, 'o': 100, 'sx': 1, 'sy': 1}]
        return {'inizio': 0, 'fine': 100, 'tappe': tappe}

    def osd(lid, testo, da, a):
        sm = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': da, 'o': 0}, {'p': da + 1, 'o': 100},
                                                  {'p': a, 'o': 100}, {'p': a + 1, 'o': 0}, {'p': 100, 'o': 0}]}
        if da <= 0:
            sm['tappe'] = [{'p': 0, 'o': 100}, {'p': a, 'o': 100}, {'p': a + 1, 'o': 0}, {'p': 100, 'o': 0}]
        if a >= 100:
            sm['tappe'] = [{'p': 0, 'o': 0}, {'p': da, 'o': 0}, {'p': da + 1, 'o': 100}, {'p': 100, 'o': 100}]
        return K.livello(lid, contenuto=testo, x=51, y=31.5, w=24, size=30, peso='700', colore=VERDE, fontFamily=MONO,
                         letterSpacing=2, entra='fadeIn', ritardo=0, durata=300, scrollMove=sm,
                         customCSS='text-shadow:0 0 8px currentColor;',
                         responsive={'mobile': {'x': 17, 'y': 50.2, 'width': 40, 'fontSize': m(16)}})

    def poi(da, a, via_da=None, via_a=None):
        # Un testo che c'è già all'apertura (da = 0) parte visibile: con la tappa «0 → invisibile» il titolo
        # «Questo sito cambia canale.» compariva solo dopo aver cominciato a scorrere (20 nov 2026).
        entra = [{'p': 0, 'o': 100, 'y': 0}] if da <= 0 else \
            [{'p': 0, 'o': 0, 'y': 16}, {'p': da, 'o': 0, 'y': 16}, {'p': a, 'o': 100, 'y': 0}]
        esce = ([{'p': via_da, 'o': 100, 'y': 0}, {'p': via_a, 'o': 0, 'y': -16}, {'p': 100, 'o': 0, 'y': -16}] if via_da
                else [{'p': 100, 'o': 100, 'y': 0}])
        return {'inizio': 0, 'fine': 100, 'tappe': entra + esce}
    video = lambda lid, v, sm: K.livello(lid, tipo='video', videoSrc=K.img(v + '.mp4'), videoAutoplay=True, videoMuted=True,
                                         videoLoop=True, entra='fadeIn', ritardo=0, durata=400, scrollMove=sm,
                                         customCSS='transform-origin:50% 50%;', responsive={'mobile': dict(schermo_tel)},
                                         **schermo)
    L = [
        video('za-ch1', V_PUBBLICO, canale(None, 30)),
        video('za-ch2', V_PROIETTORE, canale(37, 64)),
        video('za-ch3', V_NEVE, canale(71, None)),
        osd('za-osd1', 'CH 01', 0, 30),
        osd('za-osd2', 'CH 02', 42, 64),
        osd('za-osd3', 'CH 03', 76, 100),
        K.livello('za-tv', tipo='image', imageSrc=K.img('zapping-tv-2.svg'), imageAlt='Un televisore arancione degli anni Settanta',
                  objectFit='fill', x=44, y=18, w=50, h=67.5, entra='fadeIn', ritardo=0, durata=600,
                  responsive={'mobile': {'x': 6, 'y': 44, 'width': 88, 'height': 30.6}}),
        K.livello('za-o', contenuto='Cineclub · via Nizza · Torino', x=6, y=24, w=34, size=13, peso='700', colore=VERDE,
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 12.5, 'width': 88}}),
        K.livello('za-t1', contenuto='Questo sito<br>cambia canale.', tag='h1', x=6, y=29, w=38, size=56, peso='400',
                  colore=C['chiaro'], fontFamily=FONT_T, lineHeight=1.04, entra='fadeInUp', ritardo=300, durata=900,
                  scrollMove=poi(0, 1, 28, 33), responsive={'mobile': {'fontSize': m(34), 'x': 6, 'y': 15, 'width': 88}}),
        K.livello('za-t1s', contenuto='Un cineclub di quartiere: pellicole il giovedì, rassegne, VHS in prestito e una sala da '
                  'centoventi posti.', x=6, y=52, w=34, size=18, peso='500', colore=mix(C['chiaro'], 88), lineHeight=1.5,
                  ritardo=600, scrollMove=poi(0, 1, 28, 33), responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 27, 'width': 88}}),
        K.livello('za-t2', contenuto='Il giovedì,<br>la pellicola.', x=6, y=29, w=38, size=56, peso='400', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=1.04, entra='fadeIn', ritardo=0, scrollMove=poi(38, 45, 62, 67),
                  responsive={'mobile': {'fontSize': m(34), 'x': 6, 'y': 15, 'width': 88}}),
        K.livello('za-t2s', contenuto='Trentacinque millimetri, il proiettore che ronza e il cambio bobina a metà film. Come '
                  'una volta, perché una volta era meglio così.', x=6, y=52, w=34, size=18, peso='500',
                  colore=mix(C['chiaro'], 88), lineHeight=1.5, entra='fadeIn', ritardo=0, scrollMove=poi(40, 47, 62, 67),
                  responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 27, 'width': 88}}),
        K.livello('za-t3', contenuto='Il telecomando<br>è tuo.', x=6, y=29, w=38, size=56, peso='400', colore=C['acc'],
                  fontFamily=FONT_T, lineHeight=1.04, entra='fadeIn', ritardo=0, scrollMove=poi(72, 80),
                  responsive={'mobile': {'fontSize': m(34), 'x': 6, 'y': 15, 'width': 88}}),
        K.livello('za-b1', tipo='button', contenuto='Il programma', x=6, y=54, w='auto', size=14, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=8, buttonUrl=PROGRAMMA, entra='fadeIn',
                  ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=poi(76, 84),
                  hover={'bgColor': C['nero'], 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 27.5, 'fontSize': m(13)}}),
        K.livello('za-b2', tipo='button', contenuto='La tessera', x=23, y=54, w='auto', size=14, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=8, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  buttonUrl=TESSERA, entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2,
                  scrollMove=poi(76, 84), hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 52, 'y': 27.5, 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('za-1', {'type': 'color', 'color': C['nero']}, L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── i canali (Effetto sticky «Canali») ──
def canale(t, nome, occhiello, titolo, testo, foto, link=None, destra=False, pos='center center'):
    """Un canale: la foto a tutto schermo e la scheda scura col testo; «nome» va sotto il numero del canale."""
    blocco = [K.label(t, occhiello, color=VERDE),
              K.headline(t, titolo, size=54, tag='h2', pad_bottom=14, color=C['chiaro'], accento=C['acc'], sizes=(42, 30)),
              K.text(t, testo, size=16, color=mix(C['chiaro'], 88))]
    if link:
        blocco.append(K.button(t, link[0], link[1], kind='pieno', pad_top=12))
    scheda = {'bg': solid(mix(C['nero'], 88)), 'padding_top': 36, 'padding_right': 40, 'padding_bottom': 34,
              'padding_left': 40, 'border_radius': '10px'}
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=scheda), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='50,50', valign='center', anim=None)],
                     bg=K.photo_bg(foto, overlay=14, position=pos), padding='large', width='large',
                     sticky_effect='canali', sticky_etichetta=nome, sticky_colore=HEX['accent'], sticky_h_align='center',
                     style={'padding_top': 120, 'padding_bottom': 80})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Cineclub di quartiere · dal 1987', ['Cinque canali,', 'una sala sola.'], size=64,
                                layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Qui sotto il palinsesto della settimana, un canale dopo l’altro. Fra un canale e l’altro c’è '
                                'la neve: è normale, l’antenna è quella del 1987.', size=17),
                      K.button(t, 'Il programma', PROGRAMMA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out += [
        canale(t, 'Stasera in sala', 'Giovedì alle 21 · in pellicola', 'Stasera: <em>Amarcord.</em>', 'La copia in 35 mm è '
               'arrivata da Rimini martedì. A metà film si cambia bobina: due minuti di luce accesa e caramelle.', SCHERMO),
        canale(t, 'La rassegna', 'Novembre · il fine settimana', 'Le donne <em>del noir.</em>', 'Otto film in quattro fine '
               'settimana, presentati da chi li ha studiati. Il sabato alle 18 e alle 21.', PUBBLICO_3D, destra=True),
        canale(t, 'Cineforum dei ragazzi', 'La domenica alle 16', 'Prima il film, <em>poi si parla.</em>', 'Per chi ha tra gli '
               'otto e i quattordici anni. I genitori aspettano al bar, con la merenda inclusa.', DUE_POPCORN),
        canale(t, 'L’archivio VHS', 'Milleduecento cassette', 'Si prendono <em>in prestito.</em>', 'Con la tessera porti a casa '
               'tre cassette per una settimana. Il videoregistratore, se non ce l’hai, te lo prestiamo noi.', VHS_TV,
               destra=True),
        canale(t, 'Fuori onda', 'Il canale della tessera', 'Il telecomando <em>è tuo.</em>', 'Con la tessera scegli un film '
               'all’anno per il giovedì: lo proiettiamo noi, tu lo presenti.', TELECOMANDO, link=('La tessera', TESSERA)),
    ]
    # il trailer della settimana (North Video Hero)
    out.append(t.section([t.one([t.bare('northvideohero', {
        'eyebrow_text': 'IL TRAILER DELLA SETTIMANA', 'crest_on': False, 'headline_text': 'Novanta secondi, poi decidi tu',
        'accent_text': 'se venire', 'subhead': 'Ogni settimana montiamo noi il trailer del film del giovedì, coi pezzi della '
        'pellicola che proiettiamo.', 'cta1_text': 'Il programma', 'cta1_url': PROGRAMMA, 'cta2_text': '', 'mock_mode': 'video',
        'video_src': K.img(V_PROIETTORE + '.mp4'), 'video_poster': K.img(V_PROIETTORE + '.jpg'), 'show_controls': True,
        'autoplay': False, 'muted': True, 'loop': True, 'media_label': 'Il proiettore del cineclub con le bobine che girano',
        'mock_reveal': True, 'headline_max': 1000, 'bg_color': C['nero'], 'text_color': C['chiaro'],
        'eyebrow_color': VERDE, 'sub_color': mix(C['chiaro'], 76), 'accent': C['acc'], 'frame_bg': C['sala']})], anim=None)],
        width='fullbleed', padding='remove-vertical'))
    # la radio del cineclub (ASCII Viz)
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Radio Zapping', ['Le colonne sonore,', 'in onda tutta la notte.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Quando la sala è chiusa trasmettiamo le musiche dei film del mese. In caratteri, come ai tempi '
                                'del televideo.', size=17)], wm='2-5', ws='1-1'),
               t.col([t.classic('asciiviz', {
                   'show_player': True, 'track_label': 'Ora in onda', 'track_name': 'Amarcord — Nino Rota', 'audio_url': '',
                   'autoplay': False, 'show_progress': True, 'show_listeners': True, 'listeners_label': 'in ascolto',
                   'listeners_count': 214, 'listeners_drift': True, 'react_to': 'simulated', 'cols': 56, 'rows': 12,
                   'color': VERDE, 'bg_color': C['nero'], 'glow': 10, 'radius': 12, 'padding': 24}, style={})],
                   wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    # il corso di montaggio (Montaggio)
    clip = lambda tit, chi, anno, tipo, v, d: {'titolo': tit, 'cliente': chi, 'anno': anno, 'tipo': tipo, 'video': K.img(v + '.mp4'),
                                               'immagine': K.img(v + '.jpg'), 'durata': d, 'link': '', 'testo_link': '',
                                               'colore': ''}
    out.append(t.section([
        t.one([K.sechead(t, 'Il corso del sabato', ['Si impara a montare', 'un film in sei sabati.'], size=56)]),
        t.one([t.classic('montaggio', {
            'clip': [clip('La sala', 'Corso 2025', '2025', 'Esercizio 1', V_SALA, 6),
                     clip('La neve', 'Corso 2025', '2025', 'Esercizio 2', V_NEVE, 5),
                     clip('Il proiettore', 'Corso 2026', '2026', 'Esercizio 3', V_PROIETTORE, 7)],
            'nome_sequenza': 'CORSO_MONTAGGIO_2026_v3', 'fps': 24, 'parti_da_sola': True, 'mostra_audio': True,
            'mostra_info': True, 'colore_fondo': C['nero'], 'colore_testo': C['testo'], 'colore_testina': C['acc'],
            'colore_clip': VERDE, 'colore_audio': mix(C['acc2'], 60), 'titolo_color': C['chiaro'],
            'raggio': {'tl': 8, 'tr': 8, 'br': 8, 'bl': 8}}, style={})], anim=None),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(X.recensioni(K, t, [
        ('Ho rivisto Amarcord in pellicola dopo quarant’anni. Il cambio bobina è stato il momento più bello.', 'Franco',
         'San Salvario'),
        ('Mio figlio ha scelto il film della domenica. Ne parla ancora come se l’avesse girato lui.', 'Irene', 'Nizza Millefonti'),
        ('Ho preso in prestito tre VHS e un videoregistratore. Me li hanno prestati senza chiedermi niente.', 'Samir', 'Lingotto')],
        'Chi viene il giovedì', ['Tre spettatori,', 'tre telecomandi.']))
    out.append(K.pannello(t, FILE_ROSSE, 'Centoventi posti, via Nizza 120', 'Ti teniamo <em>la fila sei.</em>',
                          'È la migliore: si vede tutto lo schermo e si sente il proiettore. Prenota il posto, paghi in sala.',
                          'La sala', SALA, effetto=''))
    return out


def build_programma():
    t = Tpl(ID, 'il-programma')
    out = [K.intro(t, 'Il programma', ['Il palinsesto', 'di novembre.'], 'Giovedì la pellicola, venerdì e sabato la rassegna, '
                   'domenica i ragazzi. Il biglietto costa 6 euro, 4 con la tessera.', bg=K.video_bg(V_SALA, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'In sala', ['Ogni sera', 'un canale diverso.'], size=56)]),
        t.one([P.listino(K, t, [
            ('Giovedì 20 · Amarcord', 'Federico Fellini, 1973 · in pellicola 35 mm · 123 minuti', '21:00', SCHERMO, 'Pellicola'),
            ('Venerdì 21 · La fiamma del peccato', 'Billy Wilder, 1944 · rassegna Le donne del noir', '21:00', PROIETTORE, ''),
            ('Sabato 22 · Gilda', 'Charles Vidor, 1946 · presentato da Elena Gai', '18:00', PUBBLICO, ''),
            ('Sabato 22 · Vertigo', 'Alfred Hitchcock, 1958 · rassegna Le donne del noir', '21:00', SALA_BLU, ''),
            ('Domenica 23 · Il mio vicino Totoro', 'Hayao Miyazaki, 1988 · cineforum dei ragazzi', '16:00', DUE_POPCORN,
             'Ragazzi')])], anim=None),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dalla sala', ['Le serate', 'passate.'], size=56)]),
        t.one([t.classic('carousel', {
            'slides': [{'id': 'za-cs-%d' % k, 'image_url': K.img(foto), 'image_alt': alt, 'link_url': '', 'caption': alt}
                       for k, (foto, alt) in enumerate([(PUBBLICO, 'La prima della rassegna, sala piena'),
                                                        (PROIETTORI, 'I due proiettori della cabina'),
                                                        (BOBINE, 'Le bobine di Amarcord arrivate da Rimini'),
                                                        (TV_PARETE, 'La parete dei televisori al bar'),
                                                        (POPCORN_BOBINA, 'Popcorn fatti in sala, con il sale grosso')])],
            'slides_to_show': '3', 'gap': '16', 'autoplay': True, 'autoplay_speed': '4500', 'show_arrows': True,
            'show_dots': True, 'loop': True, 'slide_height': 'auto', 'show_caption': True, 'caption_color': C['chiaro'],
            'caption_bg': mix(C['nero'], 70), 'object_fit': 'cover', 'aspect_ratio': '4/3', 'mobile_slides': '1',
            'arrow_color': C['chiaro'], 'arrow_bg': mix(C['nero'], 60), 'dot_color': C['acc'],
            'dot_inactive_color': mix(C['chiaro'], 30)}, style={})], anim=None),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_sala():
    t = Tpl(ID, 'la-sala')
    out = [K.intro(t, 'La sala', ['Centoventi posti,', 'una cabina, due proiettori.'], 'Era la sala parrocchiale di via Nizza. '
                   'Le poltrone sono quelle del cinema Lux, chiuso nel 1994: le abbiamo portate qui una a una.',
                   bg=K.video_bg(V_PUBBLICO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Scegli dove sederti', ['La pianta', 'della sala.'], size=56)]),
        t.one([t.classic('piantasala', {
            'forma': 'rettangolo', 'palco_testo': 'Schermo', 'buca': False, 'file': 12, 'posti_fila': 10, 'ordini': 0, 'palchi': 0,
            'loggione': False, 'palco_centrale': False, 'numeri_ordini': False, 'iniziale': 'primo',
            'testo_invito': 'Passa o tocca un settore: il prezzo e cosa si vede da lì.', 'testo_intero': 'Intero',
            'testo_ridotto': 'Con la tessera', 'testo_vista': 'Lo schermo da qui', 'testo_link': 'Prenota il posto',
            'settori': [{'id': 'za-set-1', 'nome': 'Le prime file', 'zone': 'platea-a', 'prezzo': '6 €', 'ridotto': '4 €',
                         'note': 'Lo schermo ti riempie gli occhi. Per chi ama i film d’azione e il collo dritto.',
                         'immagine': K.img(SCHERMO), 'link': TESSERA},
                        {'id': 'za-set-2', 'nome': 'Dalla fila sei in poi', 'zone': 'platea-b', 'prezzo': '6 €', 'ridotto': '4 €',
                         'note': 'Si vede tutto lo schermo e si sente il proiettore che ronza alle spalle.',
                         'immagine': K.img(FILE_ROSSE), 'link': TESSERA}],
            'disposizione': 'lato', 'colore_sala': C['sala'], 'colore_palco': C['chiaro'], 'colore_posti': C['acc'],
            'colore_etichette': C['tenue'], 'sfondo_scheda': C['sala'], 'nome_color': C['chiaro'], 'prezzo_color': VERDE,
            'descrizione_color': C['tenue']}, style={})], anim=None),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima di venire', ['Quello che', 'ci chiedono al bar.'], [
        ('Si prenota?', 'Sì, ma paghi in sala. I posti prenotati si tengono fino alle 20:50.'),
        ('Serve la tessera?', 'No, il biglietto è per tutti. Con la tessera costa meno e prendi in prestito le VHS.'),
        ('I film sono in lingua originale?', 'Il giovedì sì, coi sottotitoli. Il fine settimana dipende dalla copia che arriva.'),
        ('C’è un bar?', 'Sì: popcorn fatti da noi, vino del Monferrato, caffè della moka per i film lunghi.')], 'zas'))
    out.append(X.zone(K, t, 'Dove siamo', ['In via Nizza,', 'dopo la ferrovia.'], 'Il portone grande con l’insegna rossa '
                      'accesa, accanto alla chiesa di San Salvario.', [('Metro', 'fermata Nizza'), ('Tram', 'linea 18'),
                      ('Bici', 'rastrelliera davanti all’ingresso'), ('Telefono', PHONE)], LAT, LNG,
                      'Zapping · via Nizza 120', True, HEX['primary']))
    return out


def build_tessera():
    t = Tpl(ID, 'la-tessera')
    out = [t.section([t.one([t.bare('mediacta', {
        'eyebrow': 'La tessera 2026', 'eyebrow_mono': True, 'headline': 'Venti euro, un anno di', 'accent_text': 'giovedì',
        'uppercase': True, 'headline_color': C['chiaro'], 'subhead': 'Il biglietto a 4 euro, tre VHS a settimana in prestito, '
        'il cineforum dei ragazzi gratis e un film all’anno scelto da te.', 'subhead_color': mix(C['chiaro'], 86),
        'cta_style': 'button', 'cta1_text': 'Fai la tessera', 'cta1_url': '#scrivi', 'cta2_text': 'Il programma',
        'cta2_url': PROGRAMMA, 'bg_image': K.img(V_TV_BUIO + '.jpg'),
        'media_bg': {'type': 'video', 'video_url': K.img(V_TV_BUIO + '.mp4'), 'image_url': K.img(V_TV_BUIO + '.jpg')},
        'media_label': 'Un televisore acceso in una stanza buia', 'overlay_color': C['nero'], 'overlay_top': 0.55,
        'overlay_bottom': 0.85, 'accent': C['acc'], 'accent_on': C['on_acc'], 'btn_bg': C['acc'], 'btn_color': C['on_acc'],
        'text_color': C['chiaro'], 'align': 'center', 'pad_y': 200})], anim=None)], width='fullbleed', padding='remove-vertical')]
    out.append(t.section([
        t.one([K.sechead(t, 'Con la tessera', ['Cosa cambia', 'col telecomando in mano.'], size=56)]),
        P.passi(K, t, [('01', 'Il biglietto', 'Quattro euro invece di sei, tutte le sere.'),
                       ('02', 'Le cassette', 'Tre VHS a settimana dall’archivio, e il videoregistratore se serve.'),
                       ('03', 'I ragazzi', 'Il cineforum della domenica gratis per i figli dei tesserati.'),
                       ('04', 'Il tuo film', 'Uno all’anno, scelto da te e presentato da te in sala.')], colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Tessera', 'tessera', True, opzioni='Tessera annuale · 20 €\nTessera ragazzi · 5 €\n'
                     'Tessera sostenitore · 50 €'),
             K.campo('textarea', 'Il film che vorresti vedere in sala', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Fai la tessera', ['La ritiri in sala,', 'la prima sera che vieni.'],
                                'Lasciaci i tuoi dati: la tessera è pronta alla cassa, paghi lì.', VOCI, campi,
                                'Zapping — tessera', 'Fai la tessera', 'Fatto! La tua tessera ti aspetta alla cassa.'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · nessun segnale', color=VERDE, align='center'),
                       K.headline(t, 'Questo canale <em>non trasmette.</em>', size=76, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc']),
                       K.text(t, 'Controlla l’antenna o torna al canale uno.', size=17, align='center', color=mix(C['chiaro'], 88)),
                       K.button(t, 'Canale uno', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_NEVE, overlay=45), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito che cambia canale',
    ['cineclub', 'cinema', 'torino', 'televisione', 'scuro', 'slider pro', 'canali', 'scorrimento'],
    'Un cineclub di quartiere a Torino: nell’apertura un televisore arancione che, scorrendo, si spegne come un vecchio tubo '
    'e si riaccende su un altro canale; poi le sezioni della home sono canali, con la neve fra l’uno e l’altro e il numero '
    'del canale in verde. Il trailer della settimana, la radio delle colonne sonore, il corso di montaggio, la pianta della '
    'sala e la tessera.',
    [('Il programma', PROGRAMMA), ('La sala', SALA), ('La tessera', TESSERA)],
    [('home', 'Home', None), ('il-programma', 'Il programma', 'il-programma'), ('la-sala', 'La sala', 'la-sala'),
     ('la-tessera', 'La tessera', 'la-tessera')],
    HEX, '\'Space Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bungee\', system-ui, sans-serif',
    '\'Space Mono\', ui-monospace, monospace', ['Space Grotesk', 'Bungee', 'Space Mono'], corsivo_di=[],
    peso_titoli='400', raggio=8, grana=6, titoli_line='1.04', titoli_spazio='0')
THEME['cursor']['style'] = 'telecomando'
THEME['cursor']['ring_color'] = '#1D1D22'
THEME['cursor']['dot_color'] = HEX['primary']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'decorativo'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-programma.json': build_programma(), 'la-sala.json': build_sala(),
                        'la-tessera.json': build_tessera(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
