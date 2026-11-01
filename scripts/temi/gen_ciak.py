# -*- coding: utf-8 -*-
"""Genera il tema «Ciak» (id ciak): un noleggio di attrezzature per il cinema e la fotografia a Roma: cineprese, ottiche,
luci, audio, droni e stabilizzatori, il teatro di posa e i tecnici per i set.

Tema scuro, la sala di montaggio: nero pellicola, giallo ciak, rosso REC e il grigio dell’alluminio. Titoli in Archivo
(stretto ma non schiacciato, largo 100), testo in Inter, dati in IBM Plex Mono.

Apertura: «si gira». Lo slider resta fermo in cima mentre si scorre: sul video dell’ottica di una cinepresa, a destra, un
ciak grande, col braccio a strisce aperto e la lavagnetta scritta a gesso (produzione, scena, ripresa). Scorrendo il
braccio si chiude sul cardine; al colpo un lampo bianco, si accende il REC col tempo che corre, e la lavagnetta cade via
e scopre la ripresa. Cursore: la messa a fuoco.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'ciak'
NAME = 'Ciak'

HEX = {
    'primary': '#F2C230', 'primary_contrast': '#111111', 'secondary': '#E2483D', 'secondary_contrast': '#FFFFFF',
    'accent': '#9AA3AD', 'dark': '#0B0B0C', 'light': '#F4F2EC', 'muted': '#16171A', 'muted_contrast': '#EDEBE5',
    'text': '#EDEBE5', 'text_muted': '#A3A6AD', 'background': '#0F1012', 'border': '#2A2C31', 'link': '#F2C230',
}

HOME, CATALOGO, SET, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:catalogo}}', '{{PAGINA:il-set}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'magazzino@ciak.example', '+39 06 0000 4412', 'tel:+390600004412'
LAT, LNG = '41.8520', '12.5550'

V_OTTICA, V_OPERATORE, V_CAMERA, V_SET, V_STUDIO, V_FARO, V_LUCI, V_CIAK, V_CIAK2 = (
    'pexels-video-31259707', 'pexels-video-34677880', 'pexels-video-38492076', 'pexels-video-36604691',
    'pexels-video-8089126', 'pexels-video-1534965', 'pexels-video-33684363', 'pexels-video-9808524', 'pexels-video-8089122')
PHOTOS = [
    'pexels-3418613.jpg', 'pexels-39579119.jpg', 'pexels-33899322.jpg', 'pexels-40399637.jpg', 'pexels-9357606.jpg',
    'pexels-13857091.jpg', 'pexels-13857093.jpg', 'pexels-8089650.jpg', 'pexels-8088386.jpg', 'pexels-40399674.jpg',
    'pexels-7239913.jpg', 'pexels-9811306.jpg', 'pexels-8088380.jpg', 'pexels-8390627.jpg', 'pexels-8089657.jpg',
    'pexels-29508640.jpg', 'pexels-7800235.jpg', 'pexels-9811308.jpg', 'pexels-28861948.jpg', 'pexels-7028561.jpg',
    'pexels-19224452.jpg', 'pexels-9808169.jpg', 'pexels-10321163.jpg', 'pexels-32183310.jpg', 'pexels-14830216.jpg',
    'pexels-32822409.jpg', 'pexels-31912615.jpg', 'pexels-11234307.jpg', 'pexels-16748169.jpg', 'pexels-11953865.jpg',
    'pexels-17193697.jpg', 'pexels-16259474.jpg', 'pexels-30696869.jpg', 'pexels-15325472.jpg',
]
VIDEOS = [V_OTTICA, V_OPERATORE, V_CAMERA, V_SET, V_STUDIO, V_FARO, V_LUCI, V_CIAK, V_CIAK2]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
REC, ALLU = C['sec'], C['acc2']
MONO = 'IBM Plex Mono'
VOCI = [('map-pin', 'Via Tuscolana 1055 · Roma', PRENOTA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Ciak', logo_size='26', overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Ciak', 'Noleggio di attrezzature per il cinema e la fotografia a Roma, a due passi dagli studi, dal '
                   '2009. Il magazzino apre alle sette: i set cominciano presto.',
                   [('Il catalogo', CATALOGO), ('Il set', SET), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'Il magazzino', 'Lunedì — sabato 7 — 20<br>La domenica su appuntamento', VOCI,
                   '© 2026 Ciak · Roma · P.IVA 00000000000', [('instagram', 'https://instagram.com')], marchio_size=160)


# ── L'apertura: si gira ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
STRISCE = 'background:repeating-linear-gradient(-58deg,var(--olo-color-light) 0 34px,var(--olo-color-dark) 34px 68px);'
COLPO = 44   # il momento del colpo, nel tratto dello scorrimento


def apertura(t):
    m = K.mob
    # il ciak: (x, y, larghezza) del corpo su computer e telefono; il braccio sta sopra la barra, col cardine a sinistra
    bx, by, bw, bh = 57.0, 40.0, 34.0, 46.0
    bxm, bym, bwm, bhm = 8.0, 47.0, 84.0, 25.0
    barra, braccio = 7.4, 7.4          # altezze (% della tela) di barra e braccio sul computer
    barram, bracciom = 3.4, 3.4
    cade = lambda: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0, 'o': 100}, {'p': COLPO + 8, 'y': 0, 'o': 100},
                                                         {'p': 86, 'y': 520, 'o': 0}, {'p': 100, 'y': 520, 'o': 0}]}

    def pezzo(lid, x, y, w, h, xm, ym, wm, hm, **kw):
        sm = kw.pop('scrollMove', None) or cade()
        return K.livello(lid, x=round(x, 2), y=round(y, 2), w=round(w, 2), h=round(h, 2), entra=kw.pop('entra', 'fadeInUp'),
                         ritardo=kw.pop('ritardo', 300), durata=kw.pop('durata', 900), scrollMove=sm,
                         responsive={'mobile': dict({'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                     'height': round(hm, 2)}, **kw.pop('mob', {}))}, **kw)

    chiudi = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': -14, 'y': 0, 'o': 100}, {'p': 8, 'r': -14, 'y': 0, 'o': 100},
                                                  {'p': COLPO, 'r': 0, 'y': 0, 'o': 100},
                                                  {'p': COLPO + 8, 'r': 0, 'y': 0, 'o': 100},
                                                  {'p': 86, 'r': 0, 'y': 520, 'o': 0}, {'p': 100, 'r': 0, 'y': 520, 'o': 0}]}
    gesso = mix(C['chiaro'], 90)
    righe = [('PROD.', 'CIAK · NOLEGGI ROMA', 0.16), ('SCENA', '12', 0.40), ('RIPRESA', '3', 0.40), ('REGIA', 'la vostra', 0.64),
             ('DATA', '01 · 11 · 26', 0.86)]
    livelli = [
        # il lampo del colpo, su tutta la tela
        K.livello('ci-lampo', tipo='shape', x=0, y=0, w=100, h=100, bgColor=C['chiaro'], entra='fadeIn', ritardo=0, durata=200,
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': COLPO - 0.5, 'o': 0},
                                                                 {'p': COLPO + 0.5, 'o': 85}, {'p': COLPO + 6, 'o': 0},
                                                                 {'p': 100, 'o': 0}]}),
        # il corpo
        pezzo('ci-corpo', bx, by, bw, bh, bxm, bym, bwm, bhm, tipo='shape', bgColor='#26282C', borderRadius=6,
              boxShadow={'x': 0, 'y': 30, 'blur': 70, 'spread': -18, 'color': mix(C['nero'], 80)}),
        pezzo('ci-barra', bx, by - barra, bw, barra, bxm, bym - barram, bwm, barram, tipo='shape', bgColor=C['chiaro'],
              borderRadius=3, customCSS=STRISCE),
        pezzo('ci-braccio', bx, by - barra - braccio - 0.4, bw, braccio, bxm, bym - barram - bracciom - 0.2, bwm, bracciom,
              tipo='shape', bgColor=C['chiaro'], borderRadius=3, customCSS=STRISCE + 'transform-origin:0% 100%;',
              scrollMove=chiudi),
        pezzo('ci-cardine', bx - 0.6, by - barra - 1.6, 1.6, 2.8, bxm - 1.2, bym - barram - 0.8, 3.4, 1.4, tipo='shape',
              bgColor=ALLU, borderRadius=999),
    ]
    for k, (etichetta, valore, fy) in enumerate(righe):
        y = by + bh * fy - 3.4
        ym = bym + bhm * fy - 1.6
        # la ripresa sta a metà riga, accanto alla scena
        dx, dxm = (bw / 2, bwm / 2) if k == 2 else (0, 0)
        livelli.append(pezzo('ci-e%d' % k, bx + 2.6 + dx, y, 12, 4, bxm + 5 + dxm, ym, 26, 2, contenuto=etichetta, size=12,
                             peso='600', colore=mix(C['chiaro'], 62), letterSpacing=2, fontFamily=MONO,
                             mob={'fontSize': m(10)}))
        livelli.append(pezzo('ci-v%d' % k, bx + (12 if k == 2 else 13.5) + dx, y - 1.6, (bw / 2 - 14) if k in (1, 2) else bw - 16,
                             6, bxm + (27 if k == 2 else 31) + dxm, ym - 0.8, (bwm / 2 - 30) if k in (1, 2) else bwm - 34, 3,
                             contenuto=valore,
                             size=30 if k else 22, peso='500', colore=gesso, fontFamily='Caveat',
                             mob={'fontSize': m(22 if k else 16)}))
        if k in (0, 1, 3):
            livelli.append(pezzo('ci-r%d' % k, bx + 2.6, by + bh * (fy + 0.12), bw - 5.2, 0.25, bxm + 5, bym + bhm * (fy + 0.12),
                                 bwm - 10, 0.12, tipo='shape', bgColor=mix(C['chiaro'], 22)))
    # il REC che si accende al colpo, col tempo che corre
    acceso = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': COLPO, 'o': 0}, {'p': COLPO + 4, 'o': 100},
                                                  {'p': 100, 'o': 100}]}
    livelli += [
        K.livello('ci-rec-punto', tipo='shape', x=83.2, y=12.6, w=1.1, h=1.95, bgColor=REC, borderRadius=999, entra='fadeIn',
                  ritardo=0, durata=300, animLoop='blinkLoop', animLoopDuration=1200, scrollMove=acceso,
                  responsive={'mobile': {'x': 62, 'y': 41.4, 'width': 2.6, 'height': 1.2}}),
        K.livello('ci-rec', contenuto='REC  00:00:12:04', x=85, y=11.8, w=14, size=15, peso='600', colore=C['chiaro'],
                  letterSpacing=2, fontFamily=MONO, entra='fadeIn', ritardo=0, durata=300, scrollMove=acceso,
                  responsive={'mobile': {'fontSize': m(12), 'x': 66, 'y': 40.9, 'width': 32}}),
        K.livello('ci-azione', contenuto='Azione.', x=57, y=50, w=40, size=110, peso='700', colore=C['acc'],
                  letterSpacing=-3, entra='fadeIn', ritardo=0, durata=300,
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.8}, {'p': 80, 'o': 0, 's': 0.8},
                                                                 {'p': 94, 'o': 100, 's': 1}, {'p': 100, 'o': 100, 's': 1}]},
                  responsive={'mobile': {'fontSize': m(56), 'x': 8, 'y': 55, 'width': 84}}),
    ]
    testi = [
        K.livello('ci-o', contenuto='Ciak · noleggio cinema e foto · Roma', x=6, y=24, w=40, size=12, peso='600',
                  colore=mix(C['chiaro'], 82), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('ci-t1', contenuto='Si gira.', tag='h1', x=6, y=30, w=44, size=120, peso='700', colore=C['chiaro'],
                  lineHeight=0.95, letterSpacing=-4, textTransform='uppercase', entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(56), 'y': 11, 'x': 7, 'width': 86}}),
        K.livello('ci-t2', fontFamily='Archivo', contenuto='Il resto lo portiamo noi.', tag='p', x=6, y=46, w=44, size=34, peso='600',
                  colore=C['acc'], lineHeight=1.1, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(22), 'y': 17, 'x': 7, 'width': 86}}),
        K.livello('ci-s', contenuto='Cineprese, ottiche, luci, audio e il teatro di posa. Consegna sul set dalle sei del '
                                    'mattino, i tecnici se servono.', x=6, y=55, w=34, size=16, peso='400',
                  colore=mix(C['chiaro'], 84), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 21, 'x': 7, 'width': 86}}),
        K.livello('ci-b', tipo='button', contenuto='Il catalogo', x=6, y=69, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=17, borderRadius=4, letterSpacing=2, textTransform='uppercase', entra='fadeInUp',
                  ritardo=1000, buttonUrl=CATALOGO, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'y': 28, 'x': 7, 'fontSize': m(11)}}),
        K.livello('ci-l', tipo='button', contenuto='Prenota', x=19.5, y=69, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=17, borderRadius=4, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  letterSpacing=2, textTransform='uppercase', entra='fadeInUp', ritardo=1100, buttonUrl=PRENOTA,
                  hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 24}, 'mobile': {'y': 28, 'x': 50, 'fontSize': m(11)}}),
        K.livello('ci-hint', contenuto='Scorrete: il ciak si chiude ↓', x=6, y=84, w=30, size=12, peso='600',
                  colore=mix(C['chiaro'], 80), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 20, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 33.5, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('ci-velo', 'sinistra', 36, 64, colore=C['nero'], forza=86, mobile_alto=35)
    sl = [K.diapositiva('ci-1', K.sfondo_video(V_OTTICA, overlay=0.2, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1700, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Ciak · Roma, dal 2009', ['Dal cortometraggio', 'alla serie.'], size=56, layout='stack',
                                size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Noleggiamo a produzioni, scuole di cinema, fotografi e a chi gira il primo corto con gli '
                                'amici. Tutto esce dal magazzino provato, pulito e con le batterie cariche: a mezzanotte, se '
                                'serve.', size=17),
                      K.button(t, 'Il catalogo', CATALOGO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('1.400', 'Pezzi in magazzino'), ('6:00', 'La prima consegna'), ('320', 'Set all’anno'),
                            ('2', 'Teatri di posa')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il catalogo', ['Per reparto,', 'al giorno.'], size=56,
                         tagline='I prezzi sono per un giorno di set; il fine settimana conta come un giorno.')]),
        t.one([P.elenco_hover(K, t, [
            ('Cineprese', 'Dal 4K al 6K, a corpo o in kit', 'Corpo, monitor, batterie e schede: ve le diamo pronte, già '
             'configurate per il vostro formato.', 'pexels-33899322.jpg'),
            ('Ottiche', 'Serie cine e fotografiche', 'Serie complete di fissi, zoom da documentario, gli anamorfici per il '
             'formato largo.', 'pexels-13857091.jpg'),
            ('Luci', 'LED, tungsteno, bandiere', 'Pannelli, fresnel, palloni e tutto il grip per tenerli su: stativi, sacchi, '
             'bandiere.', 'pexels-40399674.jpg'),
            ('Audio', 'Giraffe, radiomicrofoni, registratori', 'Il kit del fonico completo, con le cuffie e la giraffa da '
             'cinque metri.', 'pexels-9808169.jpg'),
        ], CATALOGO, size=52)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_FARO, 'Le luci', 'La luce <em>si costruisce.</em>',
                             'Un fresnel, una bandiera, un pallone: tre pezzi e la stanza cambia.', overlay=30))
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Dal preventivo', 'alla riconsegna.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'La lista', 'Mandateci cosa vi serve, anche a voce: la sistemiamo noi.'),
                              ('02', 'Il preventivo', 'In due ore, con le alternative se qualcosa è fuori.'),
                              ('03', 'La prova', 'Il giorno prima, in magazzino, accendete e provate tutto.'),
                              ('04', 'Il set', 'Ritirate voi o consegniamo noi, dalle sei del mattino.')], numero_font=MONO)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-29508640.jpg', 'Un ciak sul tavolo fra le cineprese', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Alle cinque del mattino ci si è rotto un monitor. Alle sei e mezza ne avevamo un altro '
                                          'sul set: non abbiamo perso nemmeno una ripresa.', 'Lucia Aragona, direttrice di '
                                          'produzione', size='40')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_STUDIO, 'Il teatro di posa', 'Duecento metri, <em>il nero e il bianco.</em>',
                          'Il fondo infinito, il trucco, la cucina: a giornata o a mezza giornata.', 'Il set', SET,
                          overlay=20, video=True))
    return out


def build_catalogo():
    t = Tpl(ID, 'catalogo')
    out = [K.intro(t, 'Il catalogo', ['Millequattrocento pezzi,', 'tutti provati.'],
                   'Qui c’è una parte del magazzino, coi prezzi al giorno. Per l’elenco completo e i kit su misura, '
                   'scriveteci: rispondiamo in due ore.', bg=K.video_bg(V_CAMERA), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Riprese', ['Cineprese', 'e ottiche.'], size=56)]),
        t.row([t.col([P.listino(K, t, [
            ('Cinepresa 6K in kit', 'Corpo, monitor, 4 batterie, 2 schede', '380 €', 'pexels-3418613.jpg', ''),
            ('Cinepresa 4K compatta', 'Per documentari e seconde unità', '160 €', 'pexels-40399637.jpg', ''),
            ('Stabilizzatore a tre assi', 'Con la valigia e il follow focus', '70 €', 'pexels-11234307.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Serie di fissi cine', 'Cinque ottiche, 18 — 85 mm', '260 €', 'pexels-13857093.jpg', 'la più chiesta'),
            ('Zoom da documentario', '28 — 76 mm, luminoso', '120 €', 'pexels-9357606.jpg', ''),
            ('Drone con camera', 'Pilota patentato a parte', '150 €', 'pexels-28861948.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Luce e suono', ['Luci, grip', 'e audio.'], size=56)]),
        t.row([t.col([P.listino(K, t, [
            ('Kit tre LED', 'Pannelli bicolore, stativi, borse', '90 €', 'pexels-8089650.jpg', ''),
            ('Fresnel 1000 W', 'Con bandiere e gelatine', '45 €', 'pexels-7239913.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Kit fonico', 'Registratore, giraffa, due radiomicrofoni', '110 €', 'pexels-19224452.jpg', ''),
            ('Microfono da studio', 'Per voci fuori campo e podcast', '30 €', 'pexels-10321163.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In magazzino', ['Gli scaffali,', 'in ordine.'], size=56)]),
        P.fila_foto(K, t, [('pexels-32183310.jpg', 'Le cineprese sugli scaffali'), ('pexels-14830216.jpg', 'Gli scaffali di '
                                                                                                          'legno'),
                           ('pexels-32822409.jpg', 'Le ottiche in fila'), ('pexels-31912615.jpg', 'Una fotocamera sul '
                                                                                                    'banco')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_set():
    t = Tpl(ID, 'il-set')
    out = [K.intro(t, 'Il set', ['Il teatro di posa', 'e chi ci lavora.'],
                   'Due teatri al piano terra, con il carico dal cortile. E i tecnici, se non li avete: operatori, '
                   'macchinisti, elettricisti, fonici.', bg=K.photo_bg('pexels-8088380.jpg'), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Teatro 1 · 200 m² · da 900 € al giorno', 'Il fondo', 'infinito',
         '<p>Il ciclorama bianco e quello nero, sei metri d’altezza, la graticcia per le luci.</p><ul><li>Camerino e trucco'
         '</li><li>Cucina e sala per la produzione</li><li>Carico dal cortile</li></ul>', 'pexels-8390627.jpg'),
        ('Teatro 2 · 80 m² · da 400 € al giorno', 'Lo studio', 'piccolo',
         '<p>Per i ritratti, le interviste e i prodotti: la luce già montata, si accende e si gira.</p><ul><li>Fondali di '
         'carta in otto colori</li><li>Tavolo per lo still life</li></ul>', 'pexels-8089657.jpg'),
        ('I tecnici · a giornata', 'Le persone', 'giuste',
         '<p>Operatori, assistenti, macchinisti, elettricisti e fonici: li conosciamo da anni.</p><ul><li>Giornata da '
         'dieci ore</li><li>Straordinari concordati prima</li></ul>', 'pexels-9811306.jpg'),
    ])], anim=None)], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.sechead(t, 'Sul set', ['Le persone', 'del magazzino.'], size=56)]),
        K.squadra(t, [
            ('pexels-16259474.jpg', 'Marco Pellegrini', 'Capo magazzino', 'Trent’anni di set. Prepara ogni kit come se dovesse '
             'usarlo lui.', 'center 25%'),
            ('pexels-30696869.jpg', 'Davide Rossi', 'Operatore di macchina', 'Steadicam e stabilizzatori, anche in barca.',
             'center 25%'),
            ('pexels-15325472.jpg', 'Andrea Costa', 'Tecnico delle luci', 'Il teatro di posa e le luci per i ritratti.',
             'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SET, 'La giornata', 'Dodici ore, <em>tutte buone.</em>',
                             'Il set comincia presto e finisce tardi: noi ci siamo dall’inizio alla fine.', overlay=30))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['La lista,', 'le date, il set.'],
                   'Mandateci la lista anche scritta male: la sistemiamo noi e vi diciamo cosa c’è e cosa conviene.',
                   bg=K.photo_bg('pexels-16748169.jpg'), size=92)]
    campi = [K.campo('text', 'Nome o produzione', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'), K.campo('date', 'Dal', 'dal', True), K.campo('date', 'Al', 'al', True),
             K.campo('select', 'Ritiro', 'ritiro', opzioni='In magazzino\nConsegna sul set'),
             K.campo('textarea', 'La lista', 'lista', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota', ['La lista e le date,', 'il preventivo in due ore.'],
                                'Per i set del giorno dopo, telefonate: il magazzino risponde fino alle 20.', VOCI, campi,
                                'Ciak — preventivo', 'Chiedi il preventivo', 'Ricevuto. Il preventivo arriva in due ore.'))
    out.append(K.domande(t, 'Prima del set', ['Le domande', 'delle produzioni.'], [
        ('Serve una cauzione?', 'Sì, per i privati: il 30% del valore, restituito alla riconsegna. Le produzioni con '
                                'contratto no.'),
        ('L’assicurazione?', 'Ogni noleggio è coperto; per i set all’estero si fa un’estensione.'),
        ('Fate sconti per le scuole?', 'Sì, il 40% per i corti delle scuole di cinema e per i progetti di tesi.'),
        ('E se qualcosa si rompe sul set?', 'Chiamate: sostituiamo il pezzo in giornata, in tutta Roma.'),
    ], 'ci-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il magazzino', ['Sulla Tuscolana,', 'col carico in cortile.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — sabato 7 — 20'), ('In metro', 'linea A, Subaugusta'),
                                  ('Il furgone', 'si carica nel cortile, al coperto')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Ciak · magazzino', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · scena tagliata', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa scena <em>è al montaggio.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La pagina non c’è. Rifacciamo: si torna all’inizio.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CIAK, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Noleggio', 'Il ciak che si chiude',
    ['noleggio attrezzature', 'cinema', 'fotografia', 'teatro di posa', 'roma', 'scuro', 'slider pro', 'scorrimento'],
    'Un noleggio di attrezzature per il cinema, nero pellicola e giallo ciak: in apertura un ciak grande sul video '
    'dell’ottica, che scorrendo si chiude col colpo e il lampo, accende il REC e cade via scoprendo la ripresa; il '
    'catalogo coi prezzi al giorno, il teatro di posa, i tecnici e il preventivo.',
    [('Il catalogo', CATALOGO), ('Il set', SET), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('catalogo', 'Il catalogo', 'catalogo'), ('il-set', 'Il set', 'il-set'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Inter\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Archivo\', system-ui, sans-serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Inter', 'Archivo', 'IBM Plex Mono', 'Caveat'], corsivo_di=[],
    peso_titoli='700', raggio=4, grana=6, titoli_line='0.96', titoli_spazio='-0.03em')
THEME['styles']['google_fonts_weights'] = '400;500;600;700'
THEME['cursor']['style'] = 'fuoco'
THEME['cursor']['ring_color'] = HEX['light']
THEME['cursor']['dot_color'] = '#41D37E'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'catalogo.json': build_catalogo(), 'il-set.json': build_set(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
