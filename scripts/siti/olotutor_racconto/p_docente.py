# -*- coding: utf-8 -*-
"""Il docente: «Prima, durante e dopo la lezione», in tre atti."""
from comune import (K, Tpl, mix, radius, solid, img, occ, tit, txt, mano, btn, bottoni, foto, schermata, sez, due, griglia,
                    scheda, chiusura, viste, altre_funzioni, VISTE, MANO, CARTA, INK, TENUE, LAV, VERDE, ONV, GIALLO, GESSO,
                    CIELO, FILO, VERDE_G, GESSO_T, GESSO_F, LAV2, CARTA2, PRO, STU)

m = K.mob

ATTI = [  # linguetta, foto, punto focale, occhiello, riga 1, riga 2, testo
    ('Prima', 'otr-docente-scrivania.webp', '72% 40%', 'Prima della lezione', 'Prepara', 'una volta.',
     'Una domanda scritta una volta serve all\'allenamento, alla prova valutata, ai giochi d\'aula e al ripasso dagli '
     'errori. E la lezione capovolta dice, prima di entrare in aula, chi ha studiato e che cosa non è stato capito.'),
    ('Durante', 'otr-aula-2.webp', '60% 40%', 'In aula', 'Conduce', 'dal telefono.',
     'Dal telefono il docente apre l\'aula della classe e la conduce: estrae un nome, chiede «Siamo al passo?», vede le '
     'mani alzate, fa partire un timer e fa l\'appello. Sul proiettore, la Regia.'),
    ('Dopo', 'otr-classe-codice.webp', '70% 40%', 'Dopo la lezione', 'Vede prima', 'l\'idea sbagliata.',
     'Per ogni quiz in cui chiede la certezza c\'è una tabella per domanda, ordinabile sulla colonna «sicuro e sbagliato». '
     'Saperlo prima della verifica permette di dedicarci il tempo che serve.'),
]


def apertura(t):
    L = K.livello
    sl = []
    for i, (tab, fotof, pos, occh, r1, r2, testo) in enumerate(ATTI):
        n = i + 1
        sf = K.sfondo_foto(fotof, overlay=0.28, kb=True, scala=1.12, durata=9000, colore=LAV)
        liv = K.veli('d-velo%d' % n, 'sinistra', 44, 74, colore=LAV, forza=92, mobile_da=42) + [
            L('d-parola%d' % n, contenuto=tab, x=40, y=60, w=56, size=190, peso='800', colore='transparent',
              textAlign='right', fontFamily='Lexend', lineHeight=1.0, letterSpacing=-4, textStrokeWidth=2,
              textStrokeColor=mix(GESSO, 55), entra='fadeIn', ritardo=200, durata=1600,
              responsive={'mobile': {'visible': False}}),
            L('d-occ%d' % n, contenuto=occh, x=6, y=21, w=40, size=14, peso='700', colore=GIALLO, letterSpacing=2,
              textTransform='uppercase', ritardo=200, responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 47, 'width': 88}}),
            L('d-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=26, w=48, size=68, peso='700', colore=GESSO,
              fontFamily='Lexend', lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=350, durata=1000,
              responsive={'mobile': {'fontSize': m(38), 'x': 6, 'y': 49.6, 'width': 88}}),
            L('d-u%d' % n, contenuto=r2, tag='p', x=6, y=35.5, w=48, size=68, peso='700', colore=GIALLO, fontFamily='Lexend',
              lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000,
              responsive={'mobile': {'fontSize': m(38), 'x': 6, 'y': 54.3, 'width': 88}}),
            L('d-s%d' % n, contenuto=testo, x=6, y=49, w=36, size=18, peso='400', colore=GESSO_T, lineHeight=1.55,
              ritardo=800, responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 60.4, 'width': 88}}),
            L('d-b%d' % n, tipo='button', contenuto='Prova con una classe', x=6, y=70, w='auto', size=16, peso='700',
              colore=INK, bgColor=GIALLO, padding=17, borderRadius=12, buttonUrl=PRO, ritardo=1000, entra='fadeIn',
              hover={'bgColor': GESSO, 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 75.5, 'fontSize': m(13)}}),
        ]
        sl.append(K.diapositiva('d-%d' % n, sf, liv, durata=8000, tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='paperCut', durata_trans=1100,
                       showArrows=False, showDots=False, showTabs=True, tabPosition='bottom', showProgressBar=True,
                       progressBarColor=GIALLO, autoplay=True, autoplaySpeed=8000, parallax=False, navColor=GESSO,
                       heightMobile={'mode': 'px', 'value': 1400}, globalBackground={'type': 'color', 'color': LAV})


def banca(t):
    usi = [('Allenamento', 'si rifà quante volte si vuole, con la risposta subito', 'otr-studente-caselle.webp'),
           ('Prova valutata', 'entra nel registro, nella media e nella pagella', 'otr-docente-registro.webp'),
           ('Giochi d\'aula', '29 giochi e 5 strumenti, in Strumenti Live', 'otr-regia-showdown.webp'),
           ('Ripasso dagli errori', 'la domanda sbagliata torna come carta', 'otr-carta-ripasso.webp')]
    hl = t.classic('hoverlist', {
        'items': [{'name': a, 'sub': b, 'desc': '', 'image': img(c), 'color': VERDE} for a, b, c in usi],
        'lead_mode': 'number', 'mono_font_family': "'Atkinson Hyperlegible', sans-serif", 'number_color': VERDE, 'number_hover_color': INK, 'name_font_family': 'heading',
        'name_color': INK, 'name_hover_color': VERDE, 'name_size': 32, 'name_uppercase': False, 'sub_color': TENUE,
        'sub_hover_color': INK, 'sub_size': 16, 'sub_uppercase': False, 'row_padding_y': 24, 'hover_indent': 22,
        'row_bg_color': '', 'hover_bg': mix(VERDE, 8), 'line_color': FILO, 'peek': True, 'peek_mode': 'image',
        'peek_width': 260, 'peek_ratio': '3/4'})
    return sez(t, [
        due(t, [occ(t, 'Prima della lezione'), tit(t, 'Una banca domande <em>per tutti gli strumenti</em>', size=52),
                txt(t, 'Una domanda scritta una volta serve all\'allenamento, alla prova valutata, ai giochi d\'aula e al '
                       'ripasso dagli errori. Le domande hanno categorie, argomenti, difficoltà e spiegazione, si condividono '
                       'con i colleghi e si esportano.'),
                mano(t, 'passa sulle voci: dove finisce la stessa domanda →', col=VERDE, size=27, pad_bottom=0)],
            [hl], peso=(44, 56), valign='center'),
    ], fondo=CARTA, pad=(120, 110))


def corso(t):
    viste_c = [('Elenco', 'otr-corso-elenco.webp'), ('Tessere', 'otr-corso-tessere.webp'), ('Percorso', 'otr-corso-percorso.webp'),
               ('Tabellone', 'otr-corso-tabellone.webp')]
    vista4 = griglia(t, [[schermata(t, f, 'Il corso di prova nella disposizione «%s»' % n, ratio='1600/1487', r=14),
                          mano(t, n.lower(), col=VERDE, size=24, align='center', pad_bottom=0)] for n, f in viste_c],
                     n=2, gap=18, anim='fade')
    lezioni = ['otr-corso-copertina.webp', 'otr-lezione-robot.webp', 'otr-lezione-dati.webp', 'otr-lezione-analista.webp',
               'otr-lezione-squadra.webp', 'otr-lezione-tavolo.webp', 'otr-lezione-server.webp', 'otr-lezione-presentazione.webp',
               'otr-lezione-social.webp', 'otr-esploso-portatile.webp', 'otr-esploso-scheda.webp']
    nastro = t.classic('marquee', {'content_type': 'images', 'text_items': '', 'images': [
        {'url': img(f), 'alt': 'Un\'immagine dei materiali del corso di prova'} for f in lezioni],
        'image_height': '200', 'image_fit': 'cover', 'speed': '40', 'direction': 'right', 'pause_hover': True, 'gap': 16,
        'velocity_skew': False, 'bg_color': 'transparent', 'height': 230, 'full_width': True})
    return t.section([
        t.row([t.col([occ(t, 'Il corso'), tit(t, 'Si prepara una volta, <em>e l\'anno dopo si riparte da lì</em>.', size=48),
                      txt(t, 'Il corso si duplica o si esporta in un pacchetto con tutto il materiale e nessun dato personale, e '
                             'l\'edizione dell\'anno successivo parte da lì. La lezione capovolta dice al docente, prima di '
                             'entrare in aula, chi ha studiato e che cosa non è stato capito.'),
                      mano(t, 'il corso di prova «L\'intelligenza artificiale in ufficio», in quattro disposizioni →', col=VERDE,
                           size=26, pad_bottom=0)], wm=None, ws='1-1'),
               t.col([vista4], wm=None, ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
        t.one([nastro], anim='fade', style={'margin_top': 56}),
    ], bg=solid(CIELO), padding='large', style={'padding_top': 110, 'padding_bottom': 70})


def regia(t):
    def cerchio(v, mx, titolo):
        return t.classic('countercircle', {'value': str(v), 'max_value': str(mx), 'suffix': '', 'prefix': '', 'title': titolo,
                                           'size': '210', 'stroke_width': '6', 'stroke_color': GIALLO, 'track_color': GESSO_F,
                                           'text_color': GIALLO, 'title_color': GESSO_T, 'duration': '2200',
                                           'title_position': 'below', 'text_effect': 'none'})
    telefoni = [('otr-regia-showdown.webp', 'Il Banco di uno Showdown sul telefono di chi conduce', 'il Banco: il codice e i comandi', '814/1600'),
                ('otr-regia-ruota.webp', 'La ruota dalla parte di chi conduce', 'la ruota, dalla parte di chi conduce', '814/1600'),
                ('otr-gioco-showdown.webp', 'Lo Showdown sul telefono di uno studente', 'lo Showdown, dal telefono dello studente', '387/839')]
    return sez(t, [
        due(t, [occ(t, 'In aula', col=GIALLO), tit(t, 'La console sul telefono <em>e la Regia sul proiettore</em>', size=52,
                                                    col=GESSO, acc=GIALLO),
                txt(t, 'Dal telefono il docente apre l\'aula della classe e la conduce: estrae un nome, chiede «Siamo al '
                       'passo?», vede le mani alzate, fa partire un timer e fa l\'appello.', col=GESSO_T, pad=(0, 0, 14, 0)),
                txt(t, 'Una partita ha due schermate: il Banco, in mano a chi conduce, con il codice e i comandi, e la Proiezione '
                       'sul muro, con a che punto è la partita, i primi tre e la classifica.', col=GESSO_T)],
            [schermata(t, 'otr-docente-regia.webp', 'La Regia proiettata durante uno Showdown: la domanda in corso e la '
                                                    'classifica', ratio='1600/811', pos='center center', r=18),
             mano(t, 'la Proiezione sul muro', col=VERDE_G, size=26, align='right', pad_bottom=0)],
            peso=(42, 58), valign='center'),
        griglia(t, [[cerchio(34, 34, 'attività in Strumenti Live')], [cerchio(29, 34, 'sono giochi, 5 sono strumenti')],
                    [cerchio(8, 29, 'giochi anche senza telefoni, con i cartoncini')]], n=3, gap=24, anim='fade'),
        griglia(t, [[schermata(t, f, a, ratio=rp, r=26), mano(t, c, col=VERDE_G, size=24, align='center', pad_bottom=0)]
                    for f, a, c, rp in telefoni], n=3, gap=28),
    ], fondo=LAV, pad=(120, 120), html_id='regia')


def due_giri(t):
    def grafico(titolo, valori):
        voci = ['Circa 100 MB', 'Circa 50 MB', 'Circa 12 MB', 'Dipende dal browser']
        return t.classic('chart', {
            'chart_type': 'bar', 'index_axis': 'y', 'dataset_label': '% della classe',
            'items': [{'label': a, 'value': str(v), 'color': VERDE if 'Circa 12' in a else mix(INK, 28)}
                      for a, v in zip(voci, valori)],
            'chart_height': '230', 'show_legend': False, 'show_title': True, 'chart_title': titolo, 'title_color': INK,
            'title_font_size': '16', 'title_font_weight': '700', 'show_subtitle': False, 'tooltip_enabled': True,
            'animate': True, 'bg_color': 'transparent', 'border_width': 0, 'border_color_override': '', 'bar_radius': '6',
            'bar_percentage': '0.78', 'category_percentage': '0.9', 'grid_color': FILO, 'text_color': TENUE,
            'tick_font_size': 12, 'show_x_grid': True, 'show_y_grid': False, 'begin_at_zero': True, 'y_min': '', 'y_max': '100'})
    carta = [occ(t, 'La domanda', pad_bottom=8),
             tit(t, 'Una connessione dichiara 100 Mb al secondo. Quanto scarica in un secondo, più o meno?', size=24, tag='h3',
                 pad_bottom=10),
             griglia(t, [[grafico('Primo giro', [50, 17, 25, 8])], [grafico('Secondo giro', [17, 8, 75, 0])]], n=2, gap=16,
                     anim='fade'),
             mano(t, 'circa 12 megabyte: dal 25% al 75% della classe, dopo la discussione', col=VERDE, size=25, pad_bottom=0)]
    return sez(t, [
        due(t, [occ(t, 'In aula'), tit(t, 'Il sondaggio <em>a due giri</em>', size=56),
                txt(t, 'Si vota, si fa discutere chi ha risposto in modo diverso e si vota di nuovo: è il procedimento di Peer '
                       'Instruction descritto da Eric Mazur. Il riquadro «Prima e dopo» mostra quanto si sono spostate le '
                       'risposte, e il primo giro resta scritto.', pad=(0, 0, 14, 0)),
                txt(t, 'C\'è poi il Richiamo, che propone le 3 domande sbagliate più spesso dalla classe nelle settimane '
                       'precedenti e ne fa uno Showdown da giocare a inizio lezione.')],
            [t.row([t.col(carta, style=scheda(GESSO, r=24, pad=(32, 30, 30, 30), bordo=FILO, ombra=True))], anim='fade'),
             mano(t, 'i numeri della classe di prova, presi dal riquadro «Prima e dopo»', col=TENUE, size=22, align='right',
                  pad_bottom=0)],
            peso=(42, 58), valign='center'),
    ], fondo=CARTA, pad=(120, 120), html_id='due-giri')


def sicuro_sbagliato(t):
    evid = txt(t, 'Una domanda in cui molti erano sicuri e hanno sbagliato indica un\'idea sbagliata diffusa nella classe, che '
                  'spesso arriva da prima e resiste anche a spiegazioni ben fatte.', size=24, col=GESSO)
    evid['settings'].update({'text_effect': 'highlight-grow', 'text_effect_target': 'text', 'text_effect_color': mix(GIALLO, 30),
                             'text_effect_delay': '200', 'line_height': 1.5})
    return sez(t, [
        due(t, [schermata(t, 'otr-docente-certezza.webp', 'La tabella della certezza per domanda, ordinata sulla colonna '
                                                          '«sicuro e sbagliato»', ratio='1600/1068', pos='left top', r=18)],
            [occ(t, 'Dopo la lezione', col=GIALLO), tit(t, 'La colonna <em>«sicuro e sbagliato»</em>', size=52, col=GESSO,
                                                        acc=GIALLO),
             txt(t, 'Per ogni quiz in cui il docente chiede la certezza c\'è una tabella per domanda, ordinabile sulla colonna '
                    '«sicuro e sbagliato».', col=GESSO_T, pad=(0, 0, 16, 0)),
             evid,
             txt(t, 'Saperlo prima della verifica permette di dedicarci il tempo che serve. La certezza non tocca il voto di '
                    'nessuno.', col=GESSO_T, pad=(16, 0, 0, 0))],
            peso=(52, 48), valign='center'),
    ], fondo=LAV, pad=(120, 120))


def registro(t):
    strategie = griglia(t, [[t.classic('badge', {'text': s, 'variant': 'solid', 'bg_color': VERDE, 'text_color': GESSO,
                                                 'alignment': 'center'})]
                            for s in ('Media pesata', 'A punti', 'Media semplice', 'Mediana')], n=4, gap=8, anim='fade')
    return sez(t, [
        due(t, [occ(t, 'Dopo la lezione'), tit(t, 'Il registro voti <em>con le strategie</em>', size=52),
                txt(t, 'Le prove si raggruppano in categorie, e ogni categoria ha una strategia con un nome:', pad=(0, 0, 14, 0)),
                strategie,
                txt(t, 'La media del registro, sempre in percentuale, è calcolata in un solo punto ed è la stessa che vedono la '
                       'famiglia e la pagella. I voti si caricano anche da un foglio, che si scarica con la classe già dentro. '
                       'Le consegne da correggere stanno in un solo elenco, «Da valutare», con la rubrica del compito sotto gli '
                       'occhi, e una parte della correzione può diventare valutazione tra pari.', pad=(18, 0, 0, 0))],
            [schermata(t, 'otr-docente-registro.webp', 'Il registro voti di Informatica con le categorie e la media',
                       ratio='1600/1565', pos='center top', r=20)],
            peso=(50, 50), valign='center'),
    ], fondo=CARTA2, pad=(120, 120))


def build():
    t = Tpl('otr', 'docente')
    return [
        K.apertura(t, apertura(t)),
        banca(t),
        corso(t),
        regia(t),
        due_giri(t),
        sicuro_sbagliato(t),
        registro(t),
        altre_funzioni(t, [
            ('calendar-days', 'Calendario', 'Le lezioni si creano, si spostano o si importano da un foglio, e il calendario '
                                            'segnala le sovrapposizioni e le chiusure della scuola.'),
            ('layout-grid', 'Come si svolge il corso', 'Online, frontale o misto. In un modulo frontale la lezione che il '
                                                       'docente apre in aula risulta fatta per tutta la classe, tranne gli '
                                                       'allenamenti, che contano solo per chi li supera.'),
            ('activity', 'Statistiche e «A rischio»', 'Chi sta perdendo terreno compare con il motivo scritto, misurato solo '
                                                      'su quello che il docente ha già aperto.'),
            ('life-buoy', 'Tutoraggio', 'Per chi segue degli studenti, un semaforo con le ragioni scritte a parole e un diario '
                                        'con tre livelli di visibilità.'),
            ('messages-square', 'Forum', 'Discussioni a carte coperte, in cui le risposte dei compagni si vedono solo dopo '
                                         'aver scritto la propria, e interventi che possono prendere un voto.'),
            ('eye', 'Come lo vede lo studente', 'Il programma del corso disegnato come lo vede uno studente iscritto, senza '
                                                'cambiare utente e senza salvare niente.'),
        ]),
        viste(t, 'Le stesse funzioni, viste dalle altre due figure', [VISTE['studente'], VISTE['scuola']]),
        chiusura(t),
    ]
