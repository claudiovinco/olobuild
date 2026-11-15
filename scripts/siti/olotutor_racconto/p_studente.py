# -*- coding: utf-8 -*-
"""Lo studente: «Lo studio di ogni giorno», raccontato dal telefono di Elena."""
from comune import (K, Tpl, mix, radius, solid, img, occ, tit, txt, mano, btn, bottoni, foto, schermata, sez, due, griglia,
                    scheda, chiusura, viste, altre_funzioni, hotspot, flip_carta, VISTE, MANO, CARTA, INK, TENUE, LAV, VERDE,
                    ONV, GIALLO, GESSO, CIELO, FILO, ROSSO, ARANCIO, AZZURRO, VERDE_G, GESSO_T, GESSO_F, LAV2, CARTA2, PRO,
                    MET, DOC)

m = K.mob

PASSI = [  # schermata, posizione, numero, titolo, riga
    ('otr-studente-home.webp', 'center top', '01', 'Che cosa fare oggi', 'Il livello, il corso da riprendere, le carte e i '
     'compiti del giorno.'),
    ('otr-studente-ripasso.webp', 'center top', '02', 'Il ripasso', 'Le carte nate dagli errori, una alla volta.'),
    ('otr-studente-aula.webp', 'center top', '03', 'In aula', 'La consegna, la mano alzata, «Siamo al passo?».'),
    ('otr-studente-statistiche.webp', 'center top', '04', 'I suoi progressi', 'Lo studio, le lezioni completate, i punti '
     'di forza.'),
]


def apertura(t):
    L = K.livello
    liv = [
        L('s-occ', contenuto='Per lo studente', x=6, y=15, w=40, size=14, peso='700', colore=VERDE, letterSpacing=2,
          textTransform='uppercase', ritardo=100, responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 2.4, 'width': 88}}),
        L('s-t1', contenuto='Lo studio', tag='h1', x=6, y=19.5, w=48, size=72, peso='700', colore=INK, lineHeight=1.0,
          letterSpacing=-2, entra='maskFromBottom', ritardo=250, durata=1000,
          responsive={'mobile': {'fontSize': m(40), 'x': 6, 'y': 4.3, 'width': 88}}),
        L('s-t2', contenuto='di ogni giorno.', tag='p', x=6, y=29.2, w=48, size=72, peso='700', colore=VERDE, lineHeight=1.0,
          letterSpacing=-2, entra='maskFromBottom', ritardo=400, durata=1000,
          responsive={'mobile': {'fontSize': m(40), 'x': 6, 'y': 9.0, 'width': 88}}),
        L('s-s', contenuto='Lo studente usa OLOtutor soprattutto dal telefono, in aula e a casa. Ogni giorno trova che cosa '
          'ripassare, si esercita con i quiz e alla consegna vede quali risposte sapeva davvero e quali ha indovinato.', x=6, y=42, w=40, size=18, peso='400', colore=TENUE, lineHeight=1.55, ritardo=600,
          responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 14.4, 'width': 88}}),
        L('s-b', tipo='button', contenuto='Prova con una classe', x=6, y=61, w='auto', size=16, peso='700', colore=ONV,
          bgColor=VERDE, padding=17, borderRadius=12, buttonUrl=PRO, ritardo=800, entra='fadeIn',
          hover={'bgColor': INK, 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 28.6, 'fontSize': m(13)}}),
        L('s-mano', contenuto='scorri: il telefono di Elena cambia schermata →', x=6, y=72, w=44, size=30, peso='700',
          colore=VERDE, fontFamily=MANO, ritardo=1100, entra='fadeIn', responsive={'mobile': {'visible': False}}),
        L('s-nota', contenuto='Le schermate vengono dalla piattaforma vera, usata da una classe di prova con nomi inventati.',
          x=6, y=86, w=36, size=13, peso='400', colore=TENUE, lineHeight=1.5, ritardo=1200,
          responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 96.6, 'width': 88, 'textAlign': 'center'}}),
        # il telefono
        L('s-tel', tipo='shape', x=56.5, y=6.5, w=24.5, h=87, bgColor=INK, borderRadius=46, entra='fadeInUp', ritardo=300,
          durata=1000, boxShadow={'x': 0, 'y': 40, 'blur': 80, 'spread': -20, 'color': mix(LAV, 45)},
          responsive={'mobile': {'x': 23, 'y': 33.5, 'width': 54, 'height': 51}}),
    ]
    n = len(PASSI)
    tratto = 100.0 / n
    for k, (nome, pos, num, titolo, riga) in enumerate(PASSI):
        a, b = k * tratto, (k + 1) * tratto
        tp = [{'p': 0, 'o': 100 if k == 0 else 0}]
        if k > 0:
            tp += [{'p': a - 3, 'o': 0}, {'p': a + 3, 'o': 100}]
        if k < n - 1:
            tp += [{'p': b - 3, 'o': 100}, {'p': b + 3, 'o': 0}]
        tp.append({'p': 100, 'o': 100 if k == n - 1 else 0})
        sm = {'inizio': 0, 'fine': 100, 'tappe': tp}
        liv += [
            L('s-sch%d' % k, tipo='image', imageSrc=img(nome), objectFit='contain' if 'ripasso' in nome else 'cover', objectPosition=pos, x=57.5, y=9.0, w=22.5,
              h=82.0, borderRadius=34, entra='fadeIn', ritardo=500, durata=800, scrollMove=sm,
              responsive={'mobile': {'x': 25, 'y': 34.8, 'width': 50, 'height': 48.4}}),
            L('s-num%d' % k, contenuto=num, x=84, y=30, w=13, size=56, peso='800', colore=VERDE, lineHeight=1.0, ritardo=700,
              entra='fadeIn', scrollMove=sm, responsive={'mobile': {'visible': False}}),
            L('s-tit%d' % k, contenuto=titolo, x=84, y=39, w=13, size=24, peso='700', colore=INK, lineHeight=1.15,
              ritardo=750, entra='fadeIn', scrollMove=sm,
              responsive={'mobile': {'x': 6, 'y': 86.6, 'width': 88, 'fontSize': m(18), 'textAlign': 'center'}}),
            L('s-riga%d' % k, contenuto=riga, x=84, y=50, w=13, size=15, peso='400', colore=TENUE, lineHeight=1.45,
              ritardo=800, entra='fadeIn', scrollMove=sm,
              responsive={'mobile': {'x': 8, 'y': 89.6, 'width': 84, 'fontSize': m(13), 'textAlign': 'center'}}),
        ]
    sfondo = {'type': 'image', 'image': img('otr-grid-tl-grigio.svg'), 'video': '', 'color': '', 'overlay': CARTA,
              'overlayOpacity': 0, 'kenBurns': False}
    sl = [K.diapositiva('s-1', sfondo, liv, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=2400, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400}, globalBackground={'type': 'color', 'color': CARTA})


def oggi(t):
    lista = t.classic('iconlist', {'items': [
        {'icon': 'layers', 'text': 'Le carte da ripassare oggi', 'link': '', 'color': ''},
        {'icon': 'clipboard-check', 'text': 'I compiti da consegnare', 'link': '', 'color': ''},
        {'icon': 'calendar-days', 'text': 'Le lezioni della settimana', 'link': '', 'color': ''}],
        'icon_color': ONV, 'icon_size': '16', 'text_color': INK, 'text_size': '18', 'gap': '14', 'icon_shape': 'circle',
        'icon_bg_color': VERDE, 'divider': False, 'layout': 'vertical'})
    return sez(t, [
        due(t, [occ(t, 'La home'), tit(t, 'Che cosa fare <em>oggi</em>', size=56),
                txt(t, 'La prima schermata mostra il livello raggiunto, il corso da riprendere e fino a 3 numeri utili:'),
                t.one([lista], anim=None, style={'margin_top': 16, 'margin_bottom': 10}),
                mano(t, 'un numero a zero non compare: senza carte da ripassare, il riquadro delle carte non c\'è.', col=VERDE,
                     size=27, pad_bottom=18),
                txt(t, 'Quando il docente apre l\'aula, in cima compare il pulsante per entrare, e sotto ci sono il calendario '
                       'del mese e l\'ultimo annuncio. Dalla schermata Calendario lo studente può portare le lezioni e le '
                       'scadenze dei compiti in Google Calendar, in Outlook o sul telefono.')],
            [griglia(t, [[schermata(t, 'otr-studente-home.webp', 'La home dello studente sul telefono: livello, corso da '
                                    'riprendere, carte e compiti', ratio='640/1262', r=28)],
                         [foto(t, 'otr-telefono-sera.webp', 'Uno studente guarda il telefono al parco, la sera', ratio='704/1528',
                               pos='50% 60%', r=28, drift=True)]], n=2, gap=18, anim='fade')],
            peso=(52, 48), valign='center'),
    ], fondo=CARTA, pad=(120, 110))


def ripasso(t):
    carta = flip_carta(t, 'Un file viene rinominato da «foto.png» a «foto.jpg». Che cosa è cambiato davvero?',
                       'Informatica di base · carta 1 di 3 · gira la carta ↻', 'Solo il nome: il contenuto resta in formato PNG.',
                       'Adesso lo studente dice com\'è andata: Di nuovo, Difficile, Bene o Facile.', LAV2, GESSO, GESSO, INK,
                       alto=470, size=26)  # 430: in tedesco a 390 la riga sotto usciva di 8 px
    tasti = griglia(t, [
        [t.classic('badge', {'text': 'Di nuovo', 'variant': 'solid', 'bg_color': ROSSO, 'text_color': GESSO, 'alignment': 'center'})],
        [t.classic('badge', {'text': 'Difficile', 'variant': 'solid', 'bg_color': ARANCIO, 'text_color': INK, 'alignment': 'center'})],
        [t.classic('badge', {'text': 'Bene', 'variant': 'solid', 'bg_color': VERDE, 'text_color': GESSO, 'alignment': 'center'})],
        [t.classic('badge', {'text': 'Facile', 'variant': 'solid', 'bg_color': AZZURRO, 'text_color': GESSO, 'alignment': 'center'})],
    ], n=4, gap=8, anim='fade')
    calcolo = t.classic('projector', {
        'eyebrow': 'Il tetto delle carte nuove', 'heading': 'Quanti giorni <em>di ripasso</em>?',
        'intro': 'Sposta il cursore: ogni giorno arrivano al massimo 5 carte nuove, contando tutti i corsi.',
        'min': '1', 'max': '30', 'step': '1', 'value': '7', 'rate': '0', 'years': '5', 'currency': '',
        'input_label': 'Giorni', 'input_unit': 'giorni', 'out_caption': 'carte nuove, al massimo', 'result_suffix': '',
        'decimals': 0, 'note': 'Il tetto è lo stesso per tutti, quindi chi è più indietro non riceve più lavoro degli altri.',
        'show_contrib': True, 'list_base': 1, 'list_items': [], 'zone_accent': GIALLO, 'card_bg': LAV2, 'align': 'left',
        'tile_padding': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40}, 'border_radius': 24, 'shadow': 'none'})
    return sez(t, [
        due(t, [carta, mano(t, 'una carta vera, come la vede lo studente', col=VERDE_G, size=27, align='center')],
            [occ(t, 'Il ripasso', col=GIALLO), tit(t, 'Il ripasso che nasce <em>dagli errori</em>', size=56, col=GESSO, acc=GIALLO),
             txt(t, 'Ogni domanda della banca sbagliata in un quiz o in una partita in aula diventa una carta di ripasso, che '
                    'torna nel giorno calcolato dall\'algoritmo FSRS. Davanti a ogni carta lo studente dice come è andata, e '
                    'da quella risposta dipende il giorno in cui la carta torna.', col=GESSO_T, pad=(0, 0, 18, 0)),
             tasti],
            peso=(44, 56), valign='center'),
        t.row([t.col([calcolo], wm=None, ws='1-1'), t.col([
            tit(t, 'Un carico <em>uguale per tutti</em>', size=40, col=GESSO, acc=GIALLO),
            txt(t, 'Ogni giorno arrivano al massimo 5 carte nuove, contando tutti i corsi, e il tetto è lo stesso per tutti, '
                   'quindi chi è più indietro non riceve più lavoro degli altri. Nella coda gli argomenti si alternano invece '
                   'di arrivare a blocchi.', col=GESSO_T),
            mano(t, 'il ripasso dagli errori lo accende il docente, corso per corso.', col=VERDE_G, size=28, pad_bottom=0)],
            wm=None, ws='1-1')], layout='custom', custom='46,54', gap=64, valign='center', style={'margin_top': 72}),
    ], fondo=LAV, pad=(120, 120), html_id='ripasso')


def certezza(t):
    return sez(t, [
        due(t, [occ(t, 'Allenamento e certezza'), tit(t, 'Quali risposte sapeva <em>e quali ha indovinato</em>', size=52),
                txt(t, 'L\'allenamento è un quiz che si rifà quante volte si vuole, e dopo ogni '
                       'risposta il pulsante «Controlla» mostra subito se era giusta. Una lezione di allenamento risulta fatta '
                       'solo quando lo studente supera il quiz.', pad=(0, 0, 14, 0)),
                txt(t, 'Nei quiz in cui il docente chiede la certezza, lo studente sceglie la risposta e poi dichiara quanto ne è '
                       'sicuro: «So la risposta», «Credo sia questa» o «Sto tirando a indovinare». Alla consegna vede le '
                       'risposte divise in quattro caselle: conoscenza solida, convinzione sbagliata, fortuna o intuizione, '
                       'lacuna.'),
                mano(t, 'la certezza non entra mai nel voto: conviene dichiararla con sincerità.', col=VERDE, size=27, pad_bottom=0)],
            [schermata(t, 'otr-studente-caselle.webp', 'Le quattro caselle alla consegna di un allenamento con la certezza',
                       ratio='1464/1500', pos='center top', r=22),
             btn(t, 'Provala tu, sulla home', '{{PAGINA:home}}#certezza', 'filo', align='center')],
            peso=(50, 50), valign='center'),
    ], fondo=CARTA, pad=(120, 120))


def aula(t):
    h = hotspot(t, 'otr-cartoncini.webp', 'Una classe che alza i cartoncini stampati per rispondere, il docente di spalle che '
                'inquadra l\'aula', [
                    (32, 21, 'Il cartoncino', 'Dove i telefoni non sono ammessi, lo studente risponde girando un cartoncino '
                     'stampato, che resta suo.', 'bottom'),
                    (50, 84, 'Il docente inquadra l\'aula', 'e raccoglie le risposte di tutta la classe in un colpo solo.', 'top'),
                    (84, 14, 'Il codice, o il QR', 'Con il telefono si entra nell\'aula aperta dal docente con un codice di 6 '
                     'caratteri o inquadrando il QR proiettato.', 'left'),
                ], altezza=640, ratio='976/1096')
    return sez(t, [
        due(t, [h, mano(t, 'tocca i punti sulla foto', col=VERDE, size=27, align='center', pad_bottom=0)],
            [occ(t, 'In aula'), tit(t, 'L\'aula dal telefono, <em>o con un cartoncino</em>', size=52),
             txt(t, 'Lo studente entra nell\'aula aperta dal docente con un codice di 6 caratteri o inquadrando il QR '
                    'proiettato. Dal telefono alza la mano, risponde a «Siamo al passo?» e partecipa ai giochi d\'aula, e gli '
                    'errori sulle domande della banca finiscono nel suo ripasso.', pad=(0, 0, 14, 0)),
             txt(t, 'Dove i telefoni non sono ammessi, lo studente risponde girando un cartoncino stampato, che resta suo, e il '
                    'docente raccoglie le risposte di tutta la classe inquadrando l\'aula. Anche la presenza lo studente la '
                    'segna da sé, con il QR o appoggiando il telefono alla targa del locale.'),
             griglia(t, [[schermata(t, 'otr-studente-aula.webp', 'La console d\'aula dello studente sul telefono', ratio='640/1385',
                                    r=20)],
                         [schermata(t, 'otr-studente-classe.webp', 'La mano alzata nella console d\'aula', ratio='737/1600',
                                    r=20)]], n=2, gap=14, anim='fade')],
            peso=(52, 48), valign='center'),
    ], fondo=CIELO, pad=(120, 120))


def build():
    t = Tpl('otr', 'studente')
    return [
        K.apertura(t, apertura(t)),
        oggi(t),
        ripasso(t),
        certezza(t),
        aula(t),
        altre_funzioni(t, [
            ('pill', 'Pillole', 'Contenuti brevi da 3 a 5 minuti in 8 formati, e in cima al massimo 3 pillole «Per oggi», '
                                'ognuna con quello che si saprà alla fine e quanto dura.'),
            ('book-open', 'Lezione capovolta', 'Dice che cosa studiare a casa ed entro quando, con il pulsante «Sono pronto» '
                                               'e un campo per scrivere al docente quello che non si è capito.'),
            ('users', 'Compiti e valutazione tra pari', 'Le consegne di testo e file, e nei compiti che lo prevedono la '
                                                        'valutazione anonima di alcuni elaborati dei compagni con la griglia del docente.'),
            ('folder-open', 'I miei file e portfolio', 'Uno spazio personale per ogni corso, con cartelle «solo io» o «anche '
                                                       'chi insegna», e un portfolio di lavori e riflessioni.'),
            ('award', 'Riconoscimenti e classifica', 'I badge dei propri corsi, anche quelli non ancora presi, con scritto come '
                                                     'si ottengono, e una classifica limitata ai propri corsi o alla propria classe.'),
            ('eye', 'Comandi della vista', 'Tema chiaro o scuro, testo in 3 dimensioni, un carattere per la dislessia e 4 lingue '
                                           'dell\'interfaccia. Le schermate si usano anche con la tastiera e con un lettore di schermo.'),
        ]),
        viste(t, 'Le stesse funzioni, viste dalle altre due figure', [VISTE['docente'], VISTE['scuola']]),
        chiusura(t),
    ]
