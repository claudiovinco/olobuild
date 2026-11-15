# -*- coding: utf-8 -*-
"""Riga di Comando: apertura nuova (15 nov 2026). L'utente: «ha una hero banner troppo psicadelica, cambia per farla
rimanere interessante ma decisamente più elegante. stravolgilo pure».

Prima: la Hero con un video di anelli grigi concentrici (un tunnel ipnotico), il bagliore verde e tre archi di foto.
Ora: lo Slider Pro su un video caldo e calmo (una mano che scrive su una tastiera, la lampada accesa dietro: Pexels
36392564), tutto il resto è una riga.
- In alto a sinistra, dove il video è già scuro: occhiello, «Pubblica il sito / con una riga.», due righe e i pulsanti.
- In basso, a tutta larghezza, il terminale: «~/il-mio-sito $» e la riga «riga deploy --prod» che si scrive da sola col
  cursore ambra. Scorrendo il comando parte: un filo ambra sottile si riempie da sinistra a destra (la barra di
  avanzamento), con la testina che corre, e sotto compaiono i passi del rilascio uno alla volta (build, certificato,
  anteprima del ramo) fino a «online» col pallino verde. Lo scorrimento è il rilascio.
Il tema non ha un generatore (è della prima serie): lo script sostituisce la prima sezione di home.json e basta.
Si può rilanciare (riconosce l'apertura già fatta dagli id «-home-ap-»).
uso: python riga_apertura_2026-11-15.py"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, mix  # noqa: E402
from kit import Kit  # noqa: E402

ID = 'riga-di-comando'
ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
HOME = os.path.join(ROOT, 'assets', 'data', 'themes', ID, 'home.json')
V = 'pexels-video-36392564'
MEDIA = {V + '.mp4', V + '.jpg'}
K = Kit(ID, MEDIA, raggio=999, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False)
C = K.c
m = K.mob
MONO = 'JetBrains Mono'
SANS = 'IBM Plex Sans'
LETTERA = 0.6          # JetBrains Mono: ogni carattere è largo 0,6 em


def larghezza(testo, px, tela):
    """quanto occupa una riga in monospazio, in % della tela"""
    return len(testo) * LETTERA * px / tela * 100


def apertura(t):
    L = K.veli('rc-velo', 'sinistra', 26, 58, colore=C['nero'], forza=78, mobile_alto=30)
    # il velo in basso, sotto la riga del terminale (sul video resta la mano, piena)
    L.append(K.livello('rc-velo-basso', tipo='shape', x=0, y=60, w=100, h=40, entra='fadeIn', ritardo=0, durata=600, esce='fadeOut',
                       shapeGradient={'angle': 0, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 45},
                       responsive={'mobile': {'x': 0, 'y': 58, 'width': 100, 'height': 42}}))
    L += [
        K.livello('rc-o', contenuto='riga · v2.4 · open source', x=6, y=14, w=50, size=13, peso='500', colore=C['acc'],
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=100,
                  responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 6.5, 'width': 88}}),
        K.livello('rc-t1', contenuto='Pubblica il sito', tag='h1', x=6, y=18, w=62, size=82, peso='700', colore=C['chiaro'],
                  fontFamily=MONO, lineHeight=1.08, letterSpacing=-1, entra='maskFromBottom', ritardo=200, durata=900,
                  responsive={'mobile': {'fontSize': m(33), 'x': 6, 'y': 8.8, 'width': 90}}),
        K.livello('rc-t2', contenuto='con una riga.', tag='p', x=6, y=29.6, w=62, size=82, peso='700', colore=C['acc'],
                  fontFamily=MONO, lineHeight=1.08, letterSpacing=-1, entra='maskFromBottom', ritardo=380, durata=900,
                  responsive={'mobile': {'fontSize': m(33), 'x': 6, 'y': 12.4, 'width': 90}}),
        K.livello('rc-s', contenuto='riga prende la cartella del tuo progetto e la mette online, con il certificato e '
                                    'un’anteprima per ogni ramo. Niente pannelli, niente clic.',
                  x=6, y=44, w=34, size=18, peso='400', colore=mix(C['chiaro'], 86), lineHeight=1.6, fontFamily=SANS,
                  entra='fadeIn', ritardo=600, responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 17, 'width': 88}}),
        K.livello('rc-b', tipo='button', contenuto='Installa riga', x=6, y=57.5, w='auto', size=15, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=16, borderRadius=999, fontFamily=MONO, buttonUrl='{{PAGINA:docs}}', entra='fadeIn',
                  ritardo=750, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'y': 58}, 'mobile': {'x': 6, 'y': 25.5, 'fontSize': m(13)}}),
        K.livello('rc-l', tipo='button', contenuto='Il changelog', x=19.5, y=57.5, w='auto', size=15, peso='500', colore=C['chiaro'],
                  bgColor='transparent', padding=16, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 40),
                  fontFamily=MONO, buttonUrl='{{PAGINA:changelog}}', entra='fadeIn', ritardo=850,
                  hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'notebook': {'x': 22}, 'tablet': {'x': 25, 'y': 58}, 'mobile': {'x': 50, 'y': 25.5, 'fontSize': m(13)}}),
    ]
    # ── il terminale in basso ──
    PERCORSO, COMANDO = '~/il-mio-sito $', 'riga deploy --prod'
    dpx, mpx = 19, m(15)
    dx_cmd = 6 + larghezza(PERCORSO + ' ', dpx, 1440)
    mx_cmd = 6 + larghezza(PERCORSO + ' ', mpx, 640)
    # sul notebook (tela 1240) e sul tablet (1024) i caratteri piccoli restano in pixel: il comando parte più a destra
    npx = 18
    nx_cmd = 6 + larghezza(PERCORSO + ' ', npx, 1240)
    tx_cmd = 6 + larghezza(PERCORSO + ' ', npx, 1024)
    scrive = 900                                   # quando comincia a scriversi (ms), una lettera ogni 45 ms
    finito = scrive + len(COMANDO) * 45 + 150
    parte = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 6, 'o': 100}, {'p': 10, 'o': 0}, {'p': 100, 'o': 0}]}
    L += [
        K.livello('rc-percorso', contenuto=PERCORSO, x=6, y=72.5, w=30, size=dpx, peso='400', colore=mix(C['chiaro'], 55),
                  fontFamily=MONO, lineHeight=1.2, entra='fadeIn', ritardo=700, customCSS='white-space:nowrap;',
                  responsive={'mobile': {'fontSize': mpx, 'x': 6, 'y': 70, 'width': 60}, 'notebook': {'fontSize': npx},
                              'tablet': {'fontSize': npx}}),
        K.livello('rc-comando', contenuto=COMANDO, x=round(dx_cmd, 2), y=72.5, w=40, size=dpx, peso='500', colore=C['chiaro'],
                  fontFamily=MONO, lineHeight=1.2, entra='fadeIn', ritardo=scrive, durata=60, customCSS='white-space:nowrap;',
                  charAnim={'split': 'chars', 'stagger': 45, 'direction': 'forward'},
                  responsive={'mobile': {'fontSize': mpx, 'x': round(mx_cmd, 2), 'y': 70, 'width': 60},
                              'notebook': {'fontSize': npx, 'x': round(nx_cmd, 2)}, 'tablet': {'fontSize': npx, 'x': round(tx_cmd, 2)}}),
        # il cursore: lampeggia finita la riga, sparisce quando il comando parte
        K.livello('rc-cursore', tipo='shape', x=round(dx_cmd + larghezza(COMANDO, dpx, 1440) + 0.3, 2), y=72.6, w=0.72, h=3.1,
                  bgColor=C['acc'], entra='fadeIn', ritardo=finito, durata=80, animLoop='blinkLoop', animLoopDuration=1050,
                  scrollMove=parte,
                  responsive={'mobile': {'x': round(mx_cmd + larghezza(COMANDO, mpx, 640) + 0.4, 2), 'y': 70.1, 'width': 1.4,
                                         'height': 1.75},
                              'notebook': {'x': round(nx_cmd + larghezza(COMANDO, npx, 1240) + 0.3, 2), 'width': 0.8},
                              'tablet': {'x': round(tx_cmd + larghezza(COMANDO, npx, 1024) + 0.3, 2), 'width': 0.95}}),
    ]
    # il binario e la barra che si riempie (andamento costante: barra e testina vanno insieme)
    corre = {'inizio': 0, 'fine': 100, 'andamento': 'costante',
             'tappe': [{'p': 0, 'mr': 100}, {'p': 8, 'mr': 100}, {'p': 72, 'mr': 0}, {'p': 100, 'mr': 0}]}
    testina = {'inizio': 0, 'fine': 100, 'andamento': 'costante',
               'tappe': [{'p': 0, 'x': 0, 'o': 0}, {'p': 7, 'x': 0, 'o': 0}, {'p': 8, 'x': 0, 'o': 100},
                         {'p': 72, 'x': round(0.88 * 1440, 1), 'o': 100}, {'p': 78, 'x': round(0.88 * 1440, 1), 'o': 0},
                         {'p': 100, 'x': round(0.88 * 1440, 1), 'o': 0}]}
    L += [
        K.livello('rc-binario', tipo='shape', x=6, y=79.2, w=88, h=0.25, bgColor=mix(C['chiaro'], 18), entra='fadeIn', ritardo=700,
                  responsive={'mobile': {'x': 6, 'y': 74, 'width': 88, 'height': 0.15}}),
        K.livello('rc-avanza', tipo='shape', x=6, y=79.2, w=88, h=0.25, bgColor=C['acc'], entra='fadeIn', ritardo=0, durata=200,
                  scrollMove=corre, responsive={'mobile': {'x': 6, 'y': 74, 'width': 88, 'height': 0.15}}),
        K.livello('rc-testina', tipo='shape', x=5.72, y=78.75, w=0.56, h=1.0, bgColor=C['acc'], borderRadius=999, entra='fadeIn',
                  ritardo=0, durata=200, scrollMove=testina,
                  customCSS='box-shadow:0 0 0 4px color-mix(in srgb,var(--olo-color-primary) 22%,transparent),0 0 18px var(--olo-color-primary);',
                  responsive={'mobile': {'x': 5.4, 'y': 73.8, 'width': 1.25, 'height': 0.57}}),
    ]
    # i passi del rilascio: sotto la barra, ognuno quando la barra ci arriva; sul telefono in colonna, come un registro
    passi = [('rc-p1', '✓  build  1,2 s', 6, 20), ('rc-p2', '✓  certificato', 28, 36), ('rc-p3', '✓  anteprima del ramo', 50, 52)]
    for k, (lid, testo, x, p) in enumerate(passi):
        L.append(K.livello(lid, contenuto=testo, x=x, y=82, w=21, size=14, peso='400', colore=mix(C['chiaro'], 72), fontFamily=MONO,
                           lineHeight=1.2, entra='fadeIn', ritardo=0, customCSS='white-space:nowrap;',
                           scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 8}, {'p': p, 'o': 0, 'y': 8},
                                                                          {'p': p + 4, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]},
                           responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 76.5 + k * 2.6, 'width': 88},
                                       'tablet': {'x': [6, 25, 44][k]}}))
    online = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 8}, {'p': 70, 'o': 0, 'y': 8}, {'p': 75, 'o': 100, 'y': 0},
                                                  {'p': 100, 'o': 100, 'y': 0}]}
    L += [
        K.livello('rc-pallino', tipo='shape', x=72, y=82.55, w=0.6, h=1.07, bgColor=C['acc2'], borderRadius=999, entra='fadeIn',
                  ritardo=0, durata=200, scrollMove=online, animLoop='pulseLoop', animLoopDuration=1600,
                  customCSS='box-shadow:0 0 12px var(--olo-color-accent);',
                  responsive={'mobile': {'x': 6, 'y': 84.65, 'width': 1.6, 'height': 0.72}, 'notebook': {'x': 69.6, 'width': 0.7},
                              'tablet': {'x': 65.6, 'width': 0.85}}),
        K.livello('rc-p4', contenuto='online · il-mio-sito.riga.app', x=73.4, y=82, w=22, size=14, peso='500', colore=C['acc2'],
                  fontFamily=MONO, lineHeight=1.2, entra='fadeIn', ritardo=0, customCSS='white-space:nowrap;', scrollMove=online,
                  responsive={'mobile': {'fontSize': m(13), 'x': 9.5, 'y': 84.3, 'width': 84},
                              'notebook': {'x': 71.2, 'width': 27}, 'tablet': {'x': 67.4, 'width': 31}}),
    ]
    sl = [K.diapositiva('rc-1', K.sfondo_video(V, overlay=0.16, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1300, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def main():
    d = json.load(open(HOME, encoding='utf-8'))
    nodi = d['content'] if isinstance(d, dict) else d
    primo = nodi[0]
    s = json.dumps(primo, ensure_ascii=False)
    if '"hero"' not in s and '-home-ap-' not in s:
        raise SystemExit('la prima sezione non è l\'apertura che mi aspettavo: niente cambiato')
    t = Tpl(ID, 'home-ap')
    nuova = K.apertura(t, apertura(t))
    nodi[0] = nuova
    with open(HOME, 'w', encoding='utf-8', newline='\n') as fh:
        fh.write(json.dumps(d, ensure_ascii=False, separators=(',', ':')))
    print('apertura nuova scritta in', os.path.relpath(HOME, ROOT))


if __name__ == '__main__':
    main()
