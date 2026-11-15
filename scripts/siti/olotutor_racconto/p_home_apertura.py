# -*- coding: utf-8 -*-
"""L'apertura della home (7 nov 2026, seconda versione): il video in sottofondo e il racconto che scorre.

L'utente, sulla prima versione (il mazzo di carte su fondo blu): «la hero dovrebbe raccontare di più su olotutor,
dovrebbe colpire con qualche soluzione wow, un video in sottofondo, troppo minimale».

Il video (due studentesse che ripassano insieme, dalla libreria di OLObuild, già usato sul sottosito OLOtutor) sta a
tutta larghezza, pieno: la sfumatura scura sale solo dal basso, dove stanno i testi (regola: mai media velati). In
apertura: che cos'è OLOtutor. Scorrendo, la presentazione scende e un telefono sale in 3D a destra; sullo schermo le
schermate vere cambiano insieme a sei frasi grandi, prima lo studente (oggi, ripasso, allenamento, aula), poi il docente,
poi la scuola. In fondo: «e il voto? viene dopo». Sul telefono il video è un livello più largo della tela e spostato,
perché si veda la ragazza (con lo sfondo della diapositiva si vedrebbe il centro, fra le due).
"""
from comune import K, mix, img, MANO, INK, TENUE, LAV, VERDE, GIALLO, GESSO, GESSO_T, VERDE_G, STU, PRO

m = K.mob
VIDEO = 'pexels-video-8108842'

FATTI = [  # occhiello (chi, e il problema con le sue parole), titolo (due righe: la soluzione), testo, schermata, adattamento
    # 8 nov 2026, l'utente: «le prime cose dovrebbero parlare dei problemi che OLOtutor risolve, del semplificare lo
    # studio, dell'affiancare in parte il prof nella gestione d'aula, fornendo informazioni importanti sulla classe».
    # E: «non serve continuare a scrivere "che non contano per il voto"… è ovvio, visto che si sta esercitando».
    ('Lo studente · «Da dove comincio?»', 'Che cosa fare oggi,\nin un colpo d\'occhio.',
     'Il livello, il corso da riprendere, le carte e i compiti del giorno. Pillole da 3 a 5 minuti, al massimo 3 «Per oggi».',
     'otr-studente-home.webp', 'cover'),
    ('Lo studente · «Studio, e poi dimentico»', 'Gli errori diventano\ncarte di ripasso.',
     'Tornano nel giorno calcolato dall\'algoritmo FSRS: al massimo 5 carte nuove al giorno, uguali per tutti.',
     'otr-studente-ripasso.webp', 'contain'),
    ('Lo studente · «Lo so, o ho indovinato?»', 'Quello che sa,\nquello che indovina.',
     'Dopo ogni risposta «Controlla» dice subito se era giusta; alla consegna la certezza divide le risposte sapute da '
     'quelle azzeccate.', 'otr-studente-caselle.webp', 'contain'),   # quasi quadrata: intera, non ritagliata
    ('Il docente · «Trenta ragazzi e un\'ora»', 'Un aiuto in aula,\ndal telefono.',
     'Estrae un nome, chiede «Siamo al passo?», vede le mani alzate, fa partire un timer e fa l\'appello. Sul proiettore, '
     'la Regia.', 'otr-regia-ruota.webp', 'cover'),
    ('Il docente · «Chi ha capito, e chi no?»', 'Come va la classe,\nprima della verifica.',
     'In aula vede chi ha risposto e chi manca ancora; dopo un quiz con la certezza, le domande in cui molti erano sicuri e '
     'hanno sbagliato.', 'otr-regia-showdown.webp', 'cover'),
    ('La scuola · «Tre fogli, tre medie diverse»', 'Medie e presenze,\nin un solo punto.',
     'La famiglia segue il percorso dei figli in sola lettura, e i dati restano sul sito della scuola.',
     'otr-scuola-come-va.webp', 'contain'),
]
INIZIO, PASSO = 22.0, 11.5     # i sei fatti: dal 22% dello scorrimento, uno ogni 11,5%


def _finestra(k, n):
    """Opacità di un elemento del fatto k: entra, resta, esce (l'ultimo resta fino al finale)."""
    a, b = INIZIO + k * PASSO, INIZIO + (k + 1) * PASSO
    tp = [{'p': 0, 'o': 0, 'y': 18}, {'p': a - 2, 'o': 0, 'y': 18}, {'p': a + 2, 'o': 100, 'y': 0}]
    if k < n - 1:
        tp += [{'p': b - 2, 'o': 100, 'y': 0}, {'p': b + 1, 'o': 0, 'y': -12}, {'p': 100, 'o': 0, 'y': -12}]
    else:
        tp += [{'p': 91, 'o': 100, 'y': 0}, {'p': 94, 'o': 0, 'y': -12}, {'p': 100, 'o': 0, 'y': -12}]
    return {'inizio': 0, 'fine': 100, 'tappe': tp}


def apertura(t):
    L = K.livello
    via = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100, 'y': 0}, {'p': 10, 'o': 100, 'y': 0},
                                               {'p': 19, 'o': 0, 'y': -30}, {'p': 100, 'o': 0, 'y': -30}]}
    liv = [
        # il video, pieno; sul telefono più largo della tela (la proporzione del video) e spostato sulla ragazza
        L('h-video', tipo='video', videoSrc=img(VIDEO + '.mp4'), videoAutoplay=True, videoMuted=True, videoLoop=True,
          x=0, y=0, w=100, h=100, entra='fadeIn', ritardo=0, durata=1200,
                    responsive={'mobile': {'x': -76.6, 'y': 0, 'width': 389, 'height': 100}}),
        # la sfumatura scura dal basso, solo dove stanno i testi
        L('h-velo', tipo='shape', x=0, y=16, w=100, h=130, entra='fadeIn', ritardo=0, durata=600,
          shapeGradient={'angle': 0, 'from': mix(LAV, 94), 'to': mix(LAV, 0), 'fromStop': 75},
          responsive={'mobile': {'x': 0, 'y': 28, 'width': 100, 'height': 72}}),
        # la presentazione
        L('h-occ', contenuto='OLOtutor · la piattaforma e-learning per la scuola', x=6, y=36.5, w=50, size=14, peso='700',
          colore=GIALLO, letterSpacing=2, textTransform='uppercase', ritardo=200, scrollMove=via,
          responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 46.5, 'width': 88}}),
    ]
    for k, (riga, col, y, ty) in enumerate([('Tutto e subito?', GESSO, 40.5, 51.6), ('Un pezzo', GIALLO, 49.1, 55.8),
                                             ('al giorno.', GIALLO, 57.7, 60.0)]):
        liv.append(L('h-t%d' % (k + 1), contenuto=riga, tag='h1' if k == 0 else 'p', x=6, y=y, w=50, size=70, peso='700',
                     colore=col, fontFamily='Lexend', lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom',
                     ritardo=350 + 150 * k, durata=1000, scrollMove=via,
                     responsive={'mobile': {'fontSize': m(36), 'x': 6, 'y': ty, 'width': 88}}))
    liv += [
        L('h-s', contenuto='Lo studente sa che cosa studiare oggi e ripassa gli errori prima di dimenticarli. Il docente ha un '
          'aiuto per condurre l\'aula e vede, prima della verifica, dove la classe ha un\'idea sbagliata.',
          x=6, y=68.5, w=46, size=18, peso='400', colore=GESSO, lineHeight=1.5, ritardo=800, scrollMove=via,
          responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 65.6, 'width': 88}}),
        L('h-bollini', contenuto='Studio più semplice · Un aiuto in aula · La classe sotto gli occhi', x=6, y=79.6,
          w=50, size=14, peso='700', colore=GESSO_T, ritardo=900, scrollMove=via,
          responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 78.4, 'width': 88}}),
        L('h-b1', tipo='button', contenuto='Prova con una classe', x=6, y=84, w='auto', size=16, peso='700', colore=INK,
          bgColor=GIALLO, padding=16, borderRadius=12, buttonUrl=PRO, ritardo=1000, entra='fadeIn', scrollMove=via,
          hover={'bgColor': GESSO, 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 83.6, 'fontSize': m(12)}}),
        L('h-b2', tipo='button', contenuto='Per lo studente', x=24.5, y=84, w='auto', size=16, peso='700',
          colore=GESSO, bgColor=mix(LAV, 40), padding=16, borderRadius=12, borderWidth=2, borderColor=mix(GESSO, 55),
          buttonUrl=STU, ritardo=1100, entra='fadeIn', scrollMove=via, hover={'bgColor': mix(LAV, 80), 'duration': 250},
          responsive={'mobile': {'x': 53, 'y': 83.6, 'fontSize': m(12)}}),  # x 49 → 53: in de/fr il primo pulsante lo copriva
    ]
    # il telefono: sale dal basso girato in 3D e si raddrizza
    sale = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 420, 'ry': -32, 'o': 0}, {'p': 6, 'y': 380, 'ry': -32, 'o': 0},
                                                {'p': 14, 'y': 200, 'ry': -20, 'o': 100}, {'p': 24, 'y': 0, 'ry': 0, 'o': 100},
                                                {'p': 100, 'y': 0, 'ry': 0, 'o': 100}]}
    # 8 nov 2026, l'utente: «serve un cellulare meno stondato, con bordi più sottili». Cornice di 6 px a 1440 (4 px sul
    # telefono), raggio 20/14, e lo schermo con la proporzione delle schermate (640×1262 = 0,51): prima era 0,46 e il
    # «cover» ritagliava i margini dell'app, con l'angolo che mangiava la «S» di «STUDENTE».
    FX, FY, FW, FH = 68, 11.4, 22.5, 77.3
    SX, SY, SW, SH = 68.42, 12.14, 21.67, 75.8
    TF = {'x': 27.3, 'y': 4, 'width': 45.4, 'height': 40}
    TS = {'x': 28.33, 'y': 4.47, 'width': 43.33, 'height': 39.04}
    liv.append(L('h-tel', tipo='shape', x=FX, y=FY, w=FW, h=FH, bgColor=INK, borderRadius=20, entra='fadeIn', ritardo=0,
                 boxShadow={'x': 0, 'y': 40, 'blur': 80, 'spread': -18, 'color': mix('#000000', 60)}, borderWidth=1,
                 borderColor=mix(GESSO, 18), scrollMove=sale, responsive={'mobile': dict(TF)}))
    n = len(FATTI)
    for k, (occh, titolo, testo, schermo, fit) in enumerate(FATTI):
        a = INIZIO + k * PASSO
        # la schermata: la prima c'è già mentre il telefono sale, le altre entrano col loro fatto e restano sotto
        tp = [{'p': 0, 'y': 420, 'ry': -32, 'o': 0}, {'p': 6, 'y': 380, 'ry': -32, 'o': 0},
              {'p': 14, 'y': 200, 'ry': -20, 'o': 100 if k == 0 else 0}, {'p': 24, 'y': 0, 'ry': 0, 'o': 100 if k == 0 else 0}]
        if k > 0:
            tp += [{'p': a - 2, 'y': 0, 'ry': 0, 'o': 0}, {'p': a + 2, 'y': 0, 'ry': 0, 'o': 100}]
        if k < n - 1:
            b = INIZIO + (k + 1) * PASSO
            tp += [{'p': b + 2, 'y': 0, 'ry': 0, 'o': 100}, {'p': b + 3, 'y': 0, 'ry': 0, 'o': 0},
                   {'p': 100, 'y': 0, 'ry': 0, 'o': 0}]
        else:
            tp.append({'p': 100, 'y': 0, 'ry': 0, 'o': 100})
        liv.append(L('h-schermo%d' % k, tipo='image', imageSrc=img(schermo), objectFit=fit, objectPosition='center top',
                     x=SX, y=SY, w=SW, h=SH, borderRadius=14, bgColor=INK, entra='fadeIn', ritardo=0,
                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': tp}, responsive={'mobile': dict(TS)}))
        f = _finestra(k, n)
        r1, r2 = titolo.split('\n')
        liv += [
            L('h-f%d-occ' % k, contenuto=occh, x=6, y=45, w=56, size=14, peso='700', colore=GIALLO, letterSpacing=2,
              textTransform='uppercase', entra='fadeIn', ritardo=0, scrollMove=f,
              responsive={'mobile': {'x': 6, 'y': 47, 'width': 88, 'fontSize': m(11)}}),
            L('h-f%d-t1' % k, contenuto=r1, tag='p', x=6, y=49.5, w=58, size=58, peso='700', colore=GESSO,
              fontFamily='Lexend', lineHeight=1.0, letterSpacing=-1.5, entra='fadeIn', ritardo=0, scrollMove=f,
              responsive={'mobile': {'x': 6, 'y': 49.6, 'width': 88, 'fontSize': m(29)}}),
            L('h-f%d-t2' % k, contenuto=r2, tag='p', x=6, y=57.2, w=58, size=58, peso='700', colore=GIALLO,
              fontFamily='Lexend', lineHeight=1.0, letterSpacing=-1.5, entra='fadeIn', ritardo=0, scrollMove=f,
              responsive={'mobile': {'x': 6, 'y': 53.3, 'width': 88, 'fontSize': m(29)}}),
            L('h-f%d-s' % k, contenuto=testo, x=6, y=67, w=46, size=19, peso='400', colore=GESSO, lineHeight=1.5,
              entra='fadeIn', ritardo=0, scrollMove=f,
              responsive={'mobile': {'x': 6, 'y': 58.6, 'width': 88, 'fontSize': m(14)}}),
        ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 18}, {'p': 92, 'o': 0, 'y': 18},
                                                {'p': 96, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    liv += [
        L('h-voto', contenuto='e il voto? viene dopo,', x=6, y=52, w=58, size=58, peso='700', colore=GESSO, fontFamily=MANO,
          lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=fine,
          responsive={'mobile': {'x': 6, 'y': 49.6, 'width': 88, 'fontSize': m(30)}}),
        L('h-voto2', contenuto='dalle prove valutate.', x=6, y=60, w=58, size=58, peso='700', colore=GIALLO, fontFamily=MANO,
          lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=fine,
          responsive={'mobile': {'x': 6, 'y': 53.6, 'width': 88, 'fontSize': m(30)}}),
        L('h-fine-b', tipo='button', contenuto='Prova con una classe', x=6, y=72, w='auto', size=16, peso='700', colore=INK,
          bgColor=GIALLO, padding=16, borderRadius=12, buttonUrl=PRO, entra='fadeIn', ritardo=0, scrollMove=fine,
          hover={'bgColor': GESSO, 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 60.5, 'fontSize': m(13)}}),
    ]
    _formati(liv)
    sl = [K.diapositiva('h-1', {'type': 'color', 'color': LAV, 'image': '', 'video': '', 'overlay': LAV,
                                'overlayOpacity': 0}, liv, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=3400, scrollStep=False,
                       heightTablet={'mode': 'px', 'value': TELA_TABLET},
                       heightMobile={'mode': 'px', 'value': 1400}, globalBackground={'type': 'color', 'color': LAV})


# ── notebook e tablet (8 nov 2026) ──
# Lo Slider Pro ha una tela per formato: computer 1440, notebook 1240 (fino a 1240 px), tablet 1024 (fino a 1024),
# telefono 640. L'apertura aveva regole solo per il telefono, così da 641 a 1240 px i caratteri restavano quelli del
# computer su una tela più stretta: il sottotitolo copriva la riga «Studio più semplice…» (fino a 40 px a 1024).
# Notebook: la stessa impaginazione del computer, coi caratteri in proporzione (1240/1440). Tablet: tela 1024×820, più
# alta del 16:9 (576), con posizioni e caratteri suoi. Le chiavi del tablet sono anche in quelle del telefono, che
# vengono dopo e vincono: niente valori del tablet che scivolano sul telefono.
TELA_TABLET = 820



def _ty(px):
    return round(px / TELA_TABLET * 100, 2)


def _tablet():
    tt = {
        'h-occ': {'x': 6, 'y': _ty(312), 'width': 58, 'fontSize': 13},
        'h-t1': {'x': 6, 'y': _ty(340), 'width': 58, 'fontSize': 50},
        'h-t2': {'x': 6, 'y': _ty(395), 'width': 58, 'fontSize': 50},
        'h-t3': {'x': 6, 'y': _ty(450), 'width': 58, 'fontSize': 50},
        'h-s': {'x': 6, 'y': _ty(528), 'width': 56, 'fontSize': 17},
        'h-bollini': {'x': 6, 'y': _ty(648), 'width': 58, 'fontSize': 13},
        'h-b1': {'x': 6, 'y': _ty(688), 'fontSize': 15},
        'h-b2': {'x': 30.5, 'y': _ty(688), 'fontSize': 15},
        'h-voto': {'x': 6, 'y': _ty(360), 'width': 58, 'fontSize': 46},
        'h-voto2': {'x': 6, 'y': _ty(416), 'width': 58, 'fontSize': 46},
        'h-fine-b': {'x': 6, 'y': _ty(500), 'fontSize': 15},
        # il telefono: cornice 6 px, schermo 0,51 come sul computer, alto due terzi della tela
        'h-tel': {'x': 66, 'y': 16.3, 'width': 28, 'height': 67.4},
    }
    for k in range(6):
        tt['h-f%d-occ' % k] = {'x': 6, 'y': _ty(330), 'width': 58, 'fontSize': 13}
        tt['h-f%d-t1' % k] = {'x': 6, 'y': _ty(358), 'width': 58, 'fontSize': 44}
        tt['h-f%d-t2' % k] = {'x': 6, 'y': _ty(408), 'width': 58, 'fontSize': 44}
        tt['h-f%d-s' % k] = {'x': 6, 'y': _ty(472), 'width': 56, 'fontSize': 17}
        tt['h-schermo%d' % k] = {'x': 66.59, 'y': 17.03, 'width': 26.86, 'height': 65.98}
    return tt


def _formati(liv):
    """Le regole scritte qui vincono su quelle che il kit ricava da sé (K.formati_intermedi: titoli in proporzione,
    testi piccoli quasi interi). Sul notebook basta il sottotitolo: un punto in meno e una colonna più larga, così
    sta in tre righe in ogni lingua e non arriva alla riga «Studio più semplice…»."""
    tt = _tablet()
    for lv in liv:
        r = lv.setdefault('responsive', {})
        if lv['id'] == 'h-s':
            r['notebook'] = {'fontSize': 16, 'width': 50}
        if lv['id'] in tt:
            r['tablet'] = tt[lv['id']]
            manca = set(r['tablet']) - set(r.get('mobile', {}))
            assert not manca, (lv['id'], manca)   # sul telefono vincerebbero i valori del tablet
