# -*- coding: utf-8 -*-
"""Genera il tema «Strato» (id strato): un laboratorio di stampa 3D e prototipi a Bergamo: stampa a filamento e a resina,
taglio laser, progettazione CAD, piccole serie per le aziende e i pezzi di ricambio che non si trovano più.

Tema scuro, il laboratorio di notte: grafite, arancio filamento, un verde acqua tecnico. Titoli in Chakra Petch, testo in
IBM Plex Sans, dati in Geist Mono.

Apertura: «la stampa». Lo slider resta fermo in cima mentre si scorre: sul video di un ugello al lavoro, a destra, il
telaio di una stampante col piatto. Scorrendo il vaso si stampa davvero, uno strato alla volta dal basso: il carrello con
l’ugello sale con gli strati e corre avanti e indietro, ogni strato nuovo è arancio caldo e poi si raffredda nel colore
del filamento. A vaso finito compaiono il tempo di stampa e lo spessore degli strati. Cursore: l’ugello.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'strato'
NAME = 'Strato'

HEX = {
    'primary': '#FF7A1A', 'primary_contrast': '#111214', 'secondary': '#FFC48A', 'secondary_contrast': '#111214',
    'accent': '#7FD3C3', 'dark': '#0E0F11', 'light': '#F2F3F5', 'muted': '#16181B', 'muted_contrast': '#ECEDEF',
    'text': '#ECEDEF', 'text_muted': '#9EA3AA', 'background': '#111214', 'border': '#2A2D31', 'link': '#FF7A1A',
}

HOME, SERVIZI, PROGETTI, PREVENTIVO = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:progetti}}', '{{PAGINA:preventivo}}'
MAIL, PHONE, PHONE_TEL = 'officina@strato.example', '+39 035 000 3344', 'tel:+390350003344'
LAT, LNG = '45.6890', '9.6680'

V_UGELLO, V_PIATTO, V_STAMPA, V_LUCI, V_UGELLO2, V_SCINTILLE, V_LASER, V_CAD, V_CAD2 = (
    'pexels-video-27537380', 'pexels-video-7035591', 'pexels-video-31076271', 'pexels-video-30260830', 'pexels-video-24816306',
    'pexels-video-37889019', 'pexels-video-30455960', 'pexels-video-31575760', 'pexels-video-7504609')
PHOTOS = [
    'pexels-19278850.jpg', 'pexels-4485456.jpg', 'pexels-20877039.jpg', 'pexels-40175783.jpg', 'pexels-3861437.jpg',
    'pexels-17301204.jpg', 'pexels-38277122.jpg', 'pexels-40180234.jpg', 'pexels-8957244.jpg', 'pexels-7181116.jpg',
    'pexels-3862621.jpg', 'pexels-3861953.jpg', 'pexels-13189375.jpg', 'pexels-7181105.jpg', 'pexels-3913017.jpg',
    'pexels-3862372.jpg', 'pexels-3862367.jpg', 'pexels-3912948.jpg', 'pexels-5904081.jpg', 'pexels-34034805.jpg',
    'pexels-19895784.jpg', 'pexels-7868884.jpg', 'pexels-7869137.jpg', 'pexels-7180748.jpg', 'pexels-6263112.jpg',
    'pexels-8940360.jpg', 'pexels-7006141.jpg', 'pexels-8278917.jpg', 'pexels-38977107.jpg', 'pexels-6474475.jpg',
    'pexels-149387.jpg', 'pexels-28752152.jpg', 'pexels-39277867.jpg', 'pexels-39244200.jpg',
]
VIDEOS = [V_UGELLO, V_PIATTO, V_STAMPA, V_LUCI, V_UGELLO2, V_SCINTILLE, V_LASER, V_CAD, V_CAD2]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='600', corsivo=False, maiuscole_pulsanti=True)
C = K.c
PESCA, ACQUA = C['sec'], C['acc2']
MONO = 'Geist Mono'
VOCI = [('map-pin', 'Via Zanica 102 · Bergamo', PREVENTIVO + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Strato', logo_size='24', overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Strato', 'Stampa 3D, taglio laser e progettazione a Bergamo, dal 2017. Prototipi, piccole serie e i '
                   'pezzi di ricambio che non si trovano più.',
                   [('I servizi', SERVIZI), ('I progetti', PROGETTI), ('Preventivo', PREVENTIVO), ('Privacy', '/privacy-policy/')],
                   'L’officina', 'Lunedì — venerdì 8:30 — 18:30<br>Le stampanti lavorano anche di notte', VOCI,
                   '© 2026 Strato · Bergamo · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                     ('linkedin', 'https://linkedin.com')], marchio_size=170)


# ── L'apertura: la stampa ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
STRATI = 16


def _raggio(t):
    """Il profilo del vaso: la mezza larghezza (0-1) all'altezza t (0 = base, 1 = bocca)."""
    punti = [(0, 0.52), (0.12, 0.74), (0.35, 1.0), (0.55, 0.9), (0.78, 0.48), (0.9, 0.46), (1, 0.62)]
    for (a, ra), (b, rb) in zip(punti, punti[1:]):
        if a <= t <= b:
            f = (t - a) / (b - a)
            f = f * f * (3 - 2 * f)
            return ra + (rb - ra) * f
    return punti[-1][1]


def apertura(t):
    m = K.mob
    # la stampante: centro del vaso, base del vaso (piatto), altezza di uno strato, larghezza massima (computer e telefono)
    CX, BASE, SH, MW = 73.0, 82.0, 2.9, 21.0
    CXM, BASEM, SHM, MWM = 50.0, 82.0, 1.7, 52.0
    passo = 72.0 / STRATI                      # il tratto di scorrimento di uno strato
    livelli = [
        # il telaio: due colonne, la traversa in alto, il piatto
        K.livello('st-col1', tipo='shape', x=CX - 17, y=14, w=0.8, h=BASE - 14 + 4, bgColor=mix(C['chiaro'], 22),
                  borderRadius=4, entra='fadeIn', ritardo=200, durata=900,
                  responsive={'mobile': {'x': CXM - 42, 'y': 42, 'width': 1.6, 'height': BASEM - 42 + 3}}),
        K.livello('st-col2', tipo='shape', x=CX + 16.2, y=14, w=0.8, h=BASE - 14 + 4, bgColor=mix(C['chiaro'], 22),
                  borderRadius=4, entra='fadeIn', ritardo=200, durata=900,
                  responsive={'mobile': {'x': CXM + 40.4, 'y': 42, 'width': 1.6, 'height': BASEM - 42 + 3}}),
        K.livello('st-trave', tipo='shape', x=CX - 17, y=13, w=34, h=1.4, bgColor=mix(C['chiaro'], 22), borderRadius=4,
                  entra='fadeIn', ritardo=200, durata=900,
                  responsive={'mobile': {'x': CXM - 42, 'y': 41.5, 'width': 84, 'height': 0.7}}),
        K.livello('st-piatto', tipo='shape', x=CX - 15, y=BASE, w=30, h=2.2, shapeGradient={'angle': 90, 'from': '#3A3D42',
                                                                                          'to': '#24272B', 'fromStop': 0},
                  borderRadius=3, entra='fadeIn', ritardo=300, durata=900,
                  responsive={'mobile': {'x': CXM - 38, 'y': BASEM, 'width': 76, 'height': 1.2}}),
    ]
    # gli strati, dal basso: ogni strato entra (dal lato dell'ugello) caldo, poi si raffredda
    for k in range(STRATI):
        tt = (k + 0.5) / STRATI
        r = _raggio(tt)
        w, wm = MW * r, MWM * r
        y, ym = BASE - (k + 1) * SH, BASEM - (k + 1) * SHM
        a = 6 + k * passo
        freddo = mix(PESCA, 55, '#C8CDD3') if k % 2 else mix(PESCA, 45, '#B9BEC5')
        stampa = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.6}, {'p': a, 'o': 0, 's': 0.6},
                                                      {'p': a + passo * 0.6, 'o': 100, 's': 1}, {'p': 100, 'o': 100, 's': 1}]}
        livelli.append(K.livello('st-s%d' % k, tipo='shape', x=round(CX - w / 2, 2), y=round(y, 2), w=round(w, 2),
                                 h=round(SH * 0.92, 2), bgColor=freddo, borderRadius=999, entra='fadeIn', ritardo=0, durata=300,
                                 scrollMove=stampa,
                                 responsive={'mobile': {'x': round(CXM - wm / 2, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                        'height': round(SHM * 0.92, 2)}}))
        # il calore: lo stesso strato in arancio che si spegne poco dopo
        caldo = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.6}, {'p': a, 'o': 0, 's': 0.6},
                                                     {'p': a + passo * 0.6, 'o': 100, 's': 1},
                                                     {'p': a + passo * 2.4, 'o': 0, 's': 1}, {'p': 100, 'o': 0, 's': 1}]}
        livelli.append(K.livello('st-c%d' % k, tipo='shape', x=round(CX - w / 2, 2), y=round(y, 2), w=round(w, 2),
                                 h=round(SH * 0.92, 2), bgColor=C['acc'], borderRadius=999, entra='fadeIn', ritardo=0,
                                 durata=300, scrollMove=caldo,
                                 boxShadow={'x': 0, 'y': 0, 'blur': 18, 'spread': 0, 'color': mix(C['acc'], 70)},
                                 responsive={'mobile': {'x': round(CXM - wm / 2, 2), 'y': round(ym, 2), 'width': round(wm, 2),
                                                        'height': round(SHM * 0.92, 2)}}))
    # il carrello: la barra, la testina e l'ugello salgono con gli strati; la testina corre avanti e indietro. Gli
    # spostamenti sono in px della tela del computer e il JS li riduce con la larghezza: sul telefono gli strati sono
    # più alti in proporzione, quindi il carrello del telefono è un gruppo a parte (fuori tela sugli altri formati) con
    # gli spostamenti moltiplicati per 1440/640.
    def sale(mob):
        sh_px = (SHM / 100 * HM * W / WM) if mob else SH / 100 * H
        return {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0}] + [
            {'p': round(6 + k * passo + passo * 0.6, 2), 'y': round(-(k + 1) * sh_px, 1)} for k in range(STRATI)] +
            [{'p': 100, 'y': round(-(STRATI + 2) * sh_px, 1)}]}

    y0, y0m = BASE - 7.6, BASEM - 4.4
    carrello = [  # (id, x, y, w, h, xm, ym, wm, hm, extra)
        ('st-barra', CX - 16.6, y0, 33.2, 1.0, CXM - 41.6, y0m, 83.2, 0.5,
         dict(tipo='shape', bgColor=mix(C['chiaro'], 40), borderRadius=4)),
        ('st-testa', CX - 3.4, y0 - 2.2, 4.6, 6.4, CXM - 6, y0m - 1.2, 9, 3.6,
         dict(tipo='shape', shapeGradient={'angle': 180, 'from': '#4A4E55', 'to': '#2B2E33', 'fromStop': 0}, borderRadius=6,
              borderWidth=1, borderColor=mix(C['chiaro'], 30), animLoop='slideHLoop', animLoopDuration=1500)),
        ('st-ugello', CX - 1.4, y0 + 4.2, 0.6, 1.6, CXM - 1.8, y0m + 2.4, 1.2, 0.9,
         dict(tipo='shape', bgColor=PESCA, borderRadius=2, animLoop='slideHLoop', animLoopDuration=1500,
              boxShadow={'x': 0, 'y': 4, 'blur': 10, 'spread': 1, 'color': mix(C['acc'], 90)})),
    ]
    for lid, x, y, w, h, xm, ym, wm, hm, extra in carrello:
        livelli.append(K.livello(lid, x=round(x, 2), y=round(y, 2), w=w, h=h, entra='fadeIn', ritardo=300, durata=900,
                                 scrollMove=sale(False), responsive={'mobile': {'visible': False}}, **extra))
        livelli.append(K.livello(lid + 'm', x=-60, y=round(ym, 2), w=wm, h=hm, entra='fadeIn', ritardo=300, durata=900,
                                 scrollMove=sale(True),
                                 responsive={'mobile': {'x': round(xm, 2), 'y': round(ym, 2), 'width': wm, 'height': hm}},
                                 **extra))
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 20}, {'p': 86, 'o': 0, 'y': 20},
                                                {'p': 96, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    livelli.append(K.livello('st-fatto', contenuto='14 h 20 min · 0,2 mm per strato · PLA riciclato', x=CX - 17, y=BASE + 4.4,
                             w=34, size=13, peso='500', colore=C['nero'], bgColor=C['acc'], padding=9, borderRadius=6,
                             align='center', fontFamily=MONO, entra='fadeIn', ritardo=0, durata=300, scrollMove=fine,
                             responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': BASEM + 2.6, 'width': 88}}))
    testi = [
        K.livello('st-o', contenuto='Strato · stampa 3D e prototipi · Bergamo', x=6, y=22, w=44, size=12, peso='500',
                  colore=mix(C['chiaro'], 82), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('st-t1', contenuto='Disegnatelo,', tag='h1', x=6, y=28, w=48, size=96, peso='600', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, textTransform='uppercase', entra='maskFromBottom', ritardo=350,
                  durata=1000, responsive={'mobile': {'fontSize': m(42), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('st-t2', fontFamily='Chakra Petch', contenuto='lo stampiamo.', tag='p', x=6, y=40, w=48, size=96,
                  peso='600', colore=C['acc'], lineHeight=1.0, letterSpacing=-2, textTransform='uppercase',
                  entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(42), 'y': 15.5, 'x': 7, 'width': 90}}),
        K.livello('st-s', contenuto='Prototipi in un giorno, piccole serie in una settimana, il pezzo di ricambio che non '
                                    'produce più nessuno. Dal disegno o dal pezzo rotto.', x=6, y=53, w=36, size=17,
                  peso='400', colore=mix(C['chiaro'], 84), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 20.5, 'x': 7, 'width': 86}}),
        K.livello('st-b', tipo='button', contenuto='Chiedi un preventivo', x=6, y=68, w='auto', size=13, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=6, letterSpacing=1.5,
                  textTransform='uppercase', entra='fadeInUp', ritardo=1000, buttonUrl=PREVENTIVO,
                  hover={'bgColor': C['chiaro'], 'duration': 250}, responsive={'mobile': {'y': 27.5, 'x': 7, 'fontSize': m(11)}}),
        K.livello('st-l', tipo='button', contenuto='I servizi', x=25, y=68, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=17, borderRadius=6, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  letterSpacing=1.5, textTransform='uppercase', entra='fadeInUp', ritardo=1100, buttonUrl=SERVIZI,
                  hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 31}, 'mobile': {'y': 27.5, 'x': 63, 'fontSize': m(11)}}),
        K.livello('st-hint', contenuto='Scorrete: si stampa ↓', x=6, y=84, w=30, size=12, peso='500',
                  colore=mix(C['chiaro'], 80), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 15, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 33, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('st-velo', 'sinistra', 38, 64, colore=C['nero'], forza=88, mobile_alto=36)
    sl = [K.diapositiva('st-1', K.sfondo_video(V_UGELLO, overlay=0.24, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=2000, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Strato · Bergamo, dal 2017', ['Dal file al pezzo,', 'senza stampi.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Lavoriamo per aziende meccaniche, designer, architetti e per chi ha rotto il pezzo di un '
                                'elettrodomestico che non si trova più. Mandateci un disegno, una foto o il pezzo stesso: lo '
                                'misuriamo, lo ridisegniamo e lo stampiamo.', size=17),
                      K.button(t, 'I servizi', SERVIZI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('24 h', 'Un prototipo'), ('14', 'Stampanti'), ('0,05', 'mm di precisione'),
                            ('3.200', 'Pezzi nel 2025')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le tecnologie', ['Quattro macchine,', 'quattro mestieri.'], size=56,
                         tagline='Passate su una tecnologia: si affaccia un pezzo.')]),
        t.one([P.elenco_hover(K, t, [
            ('Filamento', 'FDM · PLA, PETG, nylon, carbonio', 'Per i prototipi funzionali e le piccole serie: robusto, '
             'economico, pronto in un giorno.', 'pexels-40175783.jpg'),
            ('Resina', 'SLA · dettagli da 0,05 mm', 'Per i gioielli, i modelli, i pezzi piccoli e precisi: superfici lisce '
             'come lo stampo.', 'pexels-3861437.jpg'),
            ('Taglio laser', 'Legno, plexiglas, cartone', 'Per insegne, plastici, scatole, prototipi piani: dal disegno al '
             'pezzo in un’ora.', 'pexels-8940360.jpg'),
            ('Progettazione', 'CAD · reverse engineering', 'Ridisegniamo il pezzo rotto misurandolo, o il vostro schizzo '
             'su un tovagliolo.', 'pexels-3912948.jpg'),
        ], SERVIZI, size=48)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SCINTILLE, 'Il laser', 'Un raggio, <em>un taglio pulito.</em>',
                             'Fino a otto millimetri di compensato, senza una bava.', overlay=26))
    out.append(t.section([
        t.one([K.sechead(t, 'Come si lavora', ['Dal file', 'al pacco.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'Il file', 'STL, STEP o una foto con le misure: va bene tutto.'),
                              ('02', 'Il preventivo', 'In giornata, con il materiale consigliato.'),
                              ('03', 'La stampa', 'Di notte, mentre dormite: il pezzo è pronto al mattino.'),
                              ('04', 'Il pacco', 'Ritiro in officina o corriere in tutta Italia.')], numero_font=MONO)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([P.citazione(K, t, 'Il pezzo della macchina era fuori produzione da vent’anni. Gliel’abbiamo portato '
                                          'rotto in tre pezzi: il giorno dopo la linea ripartiva.', 'Officine Rota, '
                                          'Dalmine', size='38')], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-39277867.jpg', 'Un pezzo meccanico stampato', ratio='4/5')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_CAD, 'Il disegno', 'Anche <em>dal tovagliolo.</em>',
                          'Ci mandate uno schizzo, noi lo trasformiamo in un file pronto da stampare.', 'Preventivo',
                          PREVENTIVO, overlay=18, video=True))
    return out


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Quattro mestieri', 'sotto un tetto.'],
                   'Ogni lavoro passa dalle stesse mani: chi disegna, chi stampa e chi controlla il pezzo col calibro '
                   'prima di spedirlo.', bg=K.video_bg(V_STAMPA), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Prototipi · da 40 €', 'Il primo', 'pezzo',
         '<p>Un prototipo funzionale in 24 ore, per provare forma, ingombri, incastri.</p><ul><li>PLA, PETG, nylon</li><li>'
         'Due revisioni comprese</li></ul>', 'pexels-7181116.jpg'),
        ('Piccole serie · da 200 pezzi', 'La serie', 'senza stampo',
         '<p>Fino a mille pezzi senza i costi di uno stampo: ogni pezzo controllato.</p><ul><li>Consegna in una settimana'
         '</li><li>Imballo e spedizione</li></ul>', 'pexels-38277122.jpg'),
        ('Ricambi · da 30 €', 'Il pezzo', 'che non c’è più',
         '<p>Ci portate il pezzo rotto, lo misuriamo, lo ridisegniamo, lo stampiamo più robusto.</p><ul><li>Elettrodomestici, '
         'auto d’epoca, macchinari</li></ul>', 'pexels-149387.jpg'),
    ])], anim=None)], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Per orientarsi,', 'poi il preventivo.'], size=56)]),
        t.row([t.col([P.listino(K, t, [
            ('Stampa a filamento', 'Al grammo, PLA o PETG', '0,12 €', 'pexels-4485456.jpg', ''),
            ('Stampa a resina', 'Al millilitro', '0,35 €', 'pexels-17301204.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Taglio laser', 'Al minuto di macchina', '1,50 €', 'pexels-6263112.jpg', ''),
            ('Progettazione CAD', 'All’ora', '55 €', 'pexels-3862372.jpg', ''),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_progetti():
    t = Tpl(ID, 'progetti')
    out = [K.intro(t, 'I progetti', ['Tre pezzi,', 'tre storie.'],
                   'Ogni settimana passano dall’officina un centinaio di pezzi diversi. Questi tre li ricordiamo bene.',
                   bg=K.photo_bg('pexels-34034805.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Dal laboratorio', ['Prima', 'e dopo.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('01', 'ricambio', 'pexels-28752152.jpg', 'Officine Rota', 'L’ingranaggio', 'fuori produzione',
             'Ridisegnato da tre frammenti, stampato in nylon caricato carbonio: la linea è ripartita il giorno dopo.',
             '24', 'ore'),
            ('02', 'prototipo', 'pexels-3862621.jpg', 'Uno studio di design', 'La lampada', 'in sette versioni',
             'Sette prototipi in due settimane, fino alla forma giusta del diffusore.', '7', 'versioni'),
            ('03', 'serie', 'pexels-40180234.jpg', 'Una scuola', 'Seicento', 'portachiavi',
             'Il logo della scuola per la festa di fine anno, in PLA riciclato.', '600', 'pezzi')], icona='box')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi disegna,', 'chi stampa.'], size=56)]),
        K.squadra(t, [
            ('pexels-7006141.jpg', 'Sara Locatelli', 'Ingegnera, fondatrice', 'Reverse engineering e progettazione CAD.',
             'center 25%'),
            ('pexels-6474475.jpg', 'Paolo Bonetti', 'Stampa e materiali', 'Le quattordici stampanti, di giorno e di notte.',
             'center 25%'),
            ('pexels-38977107.jpg', 'Ilaria Ghisalberti', 'Laser e finiture', 'Il taglio laser e la verniciatura dei pezzi.',
             'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    return out


def build_preventivo():
    t = Tpl(ID, 'preventivo')
    out = [K.intro(t, 'Preventivo', ['Il file o la foto,', 'il prezzo in giornata.'],
                   'Scriveteci cosa vi serve: per i file grandi vi rispondiamo con un link dove caricarli.',
                   bg=K.photo_bg('pexels-7868884.jpg'), size=92)]
    campi = [K.campo('text', 'Nome o azienda', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Il lavoro', 'lavoro', opzioni='Un prototipo\nUna piccola serie\nUn ricambio\nTaglio laser\n'
                     'Progettazione'),
             K.campo('text', 'Quanti pezzi', 'quantita'), K.campo('textarea', 'Il pezzo, le misure, il materiale', 'messaggio',
                                                                  True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'preventivo', 'Preventivo', ['Due righe sul pezzo,', 'al resto pensiamo noi.'],
                                'Rispondiamo in giornata, con il materiale consigliato.', VOCI, campi, 'Strato — preventivo',
                                'Chiedi il preventivo', 'Ricevuto. Vi rispondiamo in giornata.'))
    out.append(K.domande(t, 'Prima di ordinare', ['Le domande', 'più frequenti.'], [
        ('Che file servono?', 'STL o STEP; se non li avete, bastano foto e misure.'),
        ('Quanto è resistente un pezzo stampato?', 'Dipende dal materiale: il nylon caricato carbonio regge come '
                                                  'l’alluminio in molti usi.'),
        ('Spedite?', 'In tutta Italia con corriere, in uno o due giorni.'),
        ('Si può avere un campione?', 'Sì: per le serie stampiamo prima un pezzo da approvare.'),
    ], 'st-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’officina', ['In via Zanica,', 'il capannone grigio.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — venerdì 8:30 — 18:30'), ('Dall’autostrada', 'uscita Bergamo, 5 minuti'),
                                  ('Parcheggio', 'nel cortile')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Strato · officina', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · stampa fallita', color=K.acc_media, align='center'),
                       K.headline(t, 'Si è staccato <em>dal piatto.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Capita: la pagina non c’è. Ripartiamo dalla home.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_UGELLO2, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Tecnologia', 'Il vaso stampato strato per strato',
    ['stampa 3d', 'prototipi', 'taglio laser', 'progettazione cad', 'bergamo', 'scuro', 'slider pro', 'scorrimento'],
    'Un laboratorio di stampa 3D, grafite e arancio filamento: in apertura una stampante sul video dell’ugello al lavoro, '
    'e scorrendo il vaso si stampa uno strato alla volta, col carrello che sale e gli strati che passano dal caldo al '
    'freddo; le tecnologie, il metodo, i prezzi, i progetti e il preventivo.',
    [('I servizi', SERVIZI), ('I progetti', PROGETTI), ('Preventivo', PREVENTIVO)],
    [('home', 'Home', None), ('servizi', 'I servizi', 'servizi'), ('progetti', 'I progetti', 'progetti'),
     ('preventivo', 'Preventivo', 'preventivo')],
    HEX, '\'IBM Plex Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Chakra Petch\', system-ui, sans-serif',
    '\'Geist Mono\', ui-monospace, Menlo, monospace', ['IBM Plex Sans', 'Chakra Petch', 'Geist Mono'], corsivo_di=[],
    peso_titoli='600', raggio=6, grana=4, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'ugello'
THEME['cursor']['ring_color'] = '#9AA0A6'
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'progetti.json': build_progetti(),
                        'preventivo.json': build_preventivo(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
