# -*- coding: utf-8 -*-
"""La scuola: «Registro, medie, presenze e famiglie»."""
from comune import (K, Tpl, mix, radius, solid, img, occ, tit, txt, mano, btn, bottoni, foto, schermata, sez, due, griglia,
                    scheda, chiusura, viste, altre_funzioni, VISTE, MANO, CARTA, INK, TENUE, LAV, VERDE, ONV, GIALLO, GESSO,
                    CIELO, FILO, ROSSO, VERDE_G, GESSO_T, GESSO_F, LAV2, CARTA2, PRO)

m = K.mob


def apertura(t):
    L = K.livello
    liv = [
        L('c-occ', contenuto='Per la scuola', x=6, y=14, w=40, size=14, peso='700', colore=GIALLO, letterSpacing=2,
          textTransform='uppercase', ritardo=100, responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 2.4, 'width': 88}}),
    ]
    for k, (riga, col, y, ty) in enumerate([('Registro,', GESSO, 18, 4.2), ('medie, presenze', GESSO, 26.4, 8.6),
                                             ('e famiglie.', GIALLO, 34.8, 13.0)]):
        liv.append(L('c-t%d' % k, contenuto=riga, tag='h1' if k == 0 else 'p', x=6, y=y, w=44, size=64, peso='700',
                     colore=col, fontFamily='Lexend', lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom',
                     ritardo=250 + 150 * k, durata=1000, responsive={'mobile': {'fontSize': m(36), 'x': 6, 'y': ty, 'width': 88}}))
    liv += [
        L('c-s', contenuto='Con «scuola» si intendono il dirigente, la segreteria, i coordinatori e, in un ente di formazione, '
          'la direzione. OLOtutor calcola medie e presenze in un solo punto, riduce il lavoro di segreteria di settembre, '
          'apre alle famiglie una vista in sola lettura e si può adottare cominciando da una classe.', x=6, y=46, w=38,
          size=17, peso='400', colore=GESSO_T, lineHeight=1.55, ritardo=700,
          responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 18.0, 'width': 88}}),
        L('c-b', tipo='button', contenuto='Prova con una classe', x=6, y=67, w='auto', size=16, peso='700', colore=INK,
          bgColor=GIALLO, padding=17, borderRadius=12, buttonUrl=PRO, ritardo=900, entra='fadeIn',
          hover={'bgColor': GESSO, 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 33.4, 'fontSize': m(13)}}),
        # il registro stampato: steso sul tavolo, si alza mentre si scorre
        L('c-reg', tipo='image', imageSrc=img('otr-scuola-registro.webp'), objectFit='cover', objectPosition='center top',
          x=48, y=11, w=46, h=56, borderRadius=6, entra='fadeIn', ritardo=300, durata=1000,
          boxShadow={'x': 0, 'y': 40, 'blur': 70, 'spread': -18, 'color': mix('#000000', 55)},
          scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'rx': 58, 's': 0.86, 'y': 70},
                                                          {'p': 55, 'rx': 0, 's': 1, 'y': 0},
                                                          {'p': 100, 'rx': 0, 's': 1, 'y': 0}]},
          responsive={'mobile': {'x': 5, 'y': 41, 'width': 90, 'height': 27}}),
    ]
    chips = [('Fino a 3 loghi', 48, 72, 5, 69.5), ('FSE · PNRR', 61.5, 72, 50, 69.5), ('Il firmatario', 73.2, 72, 5, 74.5),
             ('Gli stessi numeri delle schermate', 48, 79, 5, 79.5)]
    for k, (s, x, y, tx, ty) in enumerate(chips):
        da = 58 + k * 9
        liv.append(L('c-chip%d' % k, contenuto=s, x=x, y=y, w='auto', size=16, peso='700', colore=GESSO,
                     bgColor=mix(GESSO, 10), padding=12, borderRadius=999, borderWidth=1, borderColor=mix(GESSO, 30),
                     entra='fadeIn', ritardo=0,
                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 's': 0.8}, {'p': da, 'o': 0, 's': 0.8},
                                                                     {'p': da + 6, 'o': 100, 's': 1}, {'p': 100, 'o': 100, 's': 1}]},
                     responsive={'mobile': {'x': tx, 'y': ty, 'fontSize': m(12)}}))
    liv.append(L('c-mano', contenuto='scorri: il registro stampato si alza dal tavolo', x=48, y=88, w=46, size=28, peso='700',
                 colore=VERDE_G, fontFamily=MANO, entra='fadeIn', ritardo=1200,
                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 30, 'o': 0}, {'p': 100, 'o': 0}]},
                 responsive={'mobile': {'x': 6, 'y': 86, 'width': 88, 'fontSize': m(18)}}))
    sfondo = {'type': 'image', 'image': img('otr-num-cerchi-navy.svg'), 'video': '', 'color': '', 'overlay': LAV,
              'overlayOpacity': 0, 'kenBurns': False}
    sl = [K.diapositiva('c-1', sfondo, liv, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400}, globalBackground={'type': 'color', 'color': LAV})


def un_calcolo(t):
    giro = t.classic('textpath', {'text': 'registro · stampe · pagelle · quadro della classe · statistiche · famiglia · ',
                                  'path_preset': 'circle', 'font_size': '16',
                                  'text_color': VERDE, 'letter_spacing': '1', 'animation': 'none', 'spin': True,
                                  'spin_speed': '24', 'spin_direction': 'cw'})
    return sez(t, [
        due(t, [giro, mano(t, 'sei posti, un solo calcolo', col=INK, size=32, align='center', pad_bottom=0)],
            [occ(t, 'Medie e pagelle'), tit(t, 'Un solo calcolo <em>per la media e per la frequenza</em>', size=52),
             txt(t, 'La media che compare nel registro, nelle stampe, nelle pagelle, nel quadro della classe, nelle statistiche '
                    'e nella schermata della famiglia viene dallo stesso calcolo, e così la frequenza. Davanti a un genitore '
                    'che contesta una media, la scuola mostra come è stata calcolata con il nome della strategia e il peso '
                    'delle categorie.', pad=(0, 0, 14, 0)),
             txt(t, 'Il quadro della classe mette gli studenti in riga e le materie in colonna, per periodo, e da lì si '
                    'stampano le pagelle. Le medie sono in percentuale.', pad=(0, 0, 22, 0)),
             schermata(t, 'otr-scuola-medie.webp', 'Le medie della classe: studenti in riga e materie in colonna',
                       ratio='1600/989', pos='left top', r=16)],
            peso=(40, 60), valign='center'),
    ], fondo=CARTA, pad=(120, 120))


def organizzazione(t):
    lista = t.classic('iconlist', {'items': [
        {'icon': 'users', 'text': 'Chi fa parte della classe si dichiara una volta, anche caricando un foglio', 'link': '', 'color': ''},
        {'icon': 'user-check', 'text': 'Chi entra viene iscritto a tutte le materie', 'link': '', 'color': ''},
        {'icon': 'calendar-off', 'text': 'Domeniche e feste nazionali si calcolano da sole', 'link': '', 'color': ''},
        {'icon': 'log-in', 'text': 'Si entra anche con l\'account Google o Microsoft 365 dell\'istituto', 'link': '', 'color': ''}],
        'icon_color': ONV, 'icon_size': '16', 'text_color': INK, 'text_size': '18', 'gap': '16', 'icon_shape': 'circle',
        'icon_bg_color': VERDE, 'divider': True, 'divider_color': FILO, 'layout': 'vertical'})
    return sez(t, [
        due(t, [occ(t, 'Organizzazione'), tit(t, 'L\'elenco della classe <em>e le materie che uno studente non fa</em>', size=48),
                txt(t, 'Gli esoneri, come la religione per chi non se ne avvale o una seconda lingua diversa, si scrivono una '
                       'volta, e il quadro della classe smette di segnalarli come iscrizioni mancanti.', pad=(0, 0, 14, 0)),
                txt(t, 'Le ore del giorno scolastico, le chiusure e i periodi dell\'anno si scrivono una volta per tutta la '
                       'scuola. Per entrare con l\'account dell\'istituto, la scuola registra prima l\'applicazione presso '
                       'Google o Microsoft.')],
            [t.row([t.col([lista], style=scheda(GESSO, r=24, pad=(30, 30, 24, 30), bordo=FILO))], anim='fade'),
             mano(t, 'si scrive una volta, vale per tutta la scuola', col=VERDE, size=27, align='right', pad_bottom=0)],
            peso=(50, 50), valign='center'),
    ], fondo=CIELO, pad=(110, 110))


def presenze(t):
    modi = griglia(t, [[t.classic('badge', {'text': s, 'variant': 'solid', 'bg_color': GIALLO, 'text_color': INK,
                                            'alignment': 'center'})] for s in ('a ore', 'a giornate', 'a durata effettiva')],
                   n=3, gap=8, anim='fade')
    return sez(t, [
        due(t, [occ(t, 'Presenze e registri', col=GIALLO),
                tit(t, 'Presenze a ore e registri <em>con l\'intestazione della scuola</em>', size=48, col=GESSO, acc=GIALLO),
                txt(t, 'Ogni corso conta le presenze:', col=GESSO_T, pad=(0, 0, 12, 0)), modi,
                txt(t, 'Le ore della giornata si salvano sulla riga della presenza, quindi un cambio d\'orario a gennaio non '
                       'modifica le presenze di ottobre.', col=GESSO_T, pad=(20, 0, 14, 0)),
                txt(t, 'Il registro di classe, il registro voti e il registro presenze si stampano con un\'intestazione che porta '
                       'fino a 3 loghi, la dicitura del finanziamento (FSE, PNRR) e il firmatario, con gli stessi numeri delle '
                       'schermate.', col=GESSO_T)],
            [schermata(t, 'otr-piantine.webp', 'La piantina della scuola, con i cartellini da stampare', ratio='760/957',
                       pos='center top', r=20),
             mano(t, 'la piantina della scuola, coi cartellini da stampare', col=VERDE_G, size=25, align='center', pad_bottom=0)],
            peso=(56, 44), valign='center'),
    ], fondo=LAV, pad=(120, 120))


def famiglie(t):
    def lista(voci, icona, colore):
        return t.classic('iconlist', {'items': [{'icon': icona, 'text': v, 'link': '', 'color': ''} for v in voci],
                                      'icon_color': GESSO, 'icon_size': '14', 'text_color': INK, 'text_size': '17', 'gap': '14',
                                      'icon_shape': 'circle', 'icon_bg_color': colore, 'divider': False, 'layout': 'vertical'})
    vede = lista(['Un semaforo con le ragioni scritte a parole', 'L\'avanzamento e la media del registro, corso per corso',
                  'Le consegne mancanti', 'Le presenze giorno per giorno', 'I nomi dei docenti e dei tutor a cui scrivere'],
                 'check', VERDE)
    no = lista(['Punti e classifica', 'Badge', 'Certezze', 'Risposte dei quiz', 'File e messaggi del figlio'], 'x', ROSSO)
    registro = t.classic('countercircle', {'value': '24', 'max_value': '24', 'suffix': '', 'prefix': '',
                                           'title': 'mesi: tanto resta il registro degli accessi', 'size': '200',
                                           'stroke_width': '6', 'stroke_color': VERDE, 'track_color': FILO, 'text_color': VERDE,
                                           'title_color': TENUE, 'duration': '2200', 'title_position': 'below',
                                           'text_effect': 'none'})
    return sez(t, [
        due(t, [occ(t, 'Le famiglie'), tit(t, 'Che cosa vede un genitore, <em>e che cosa no</em>', size=52)],
            [txt(t, 'Il genitore, il tutore legale o l\'affidatario riceve un invito e sceglie da sé la password, che la scuola '
                    'non vede. Può giustificare un\'assenza, e il docente la accoglie o la respinge.')],
            peso=(55, 45), valign='end'),
        t.row([
            t.col([occ(t, 'Per ogni figlio vede', pad_bottom=12), vede], style=scheda(GESSO, r=24, bordo=FILO)),
            t.col([schermata(t, 'otr-scuola-come-va.webp', 'La schermata Come va del genitore sul telefono', ratio='640/1035',
                             r=28)]),
            t.col([occ(t, 'Non vede', col=ROSSO, pad_bottom=12), no], style=scheda(GESSO, r=24, bordo=FILO)),
        ], layout='100', anim='slide-up', layout_mode='grid', grid_columns='1fr 0.8fr 1fr', grid_column_gap=24,
            grid_row_gap=24, grid_align_items='center', style={'margin_top': 40}),
        due(t, [registro],
            [txt(t, 'Ogni volta che un familiare o un tutor apre i dati di uno studente resta una riga nel registro degli '
                    'accessi, conservata per 24 mesi, e lo studente vede nel suo profilo chi lo segue.', size=20, col=INK)],
            peso=(30, 70), valign='center'),
    ], fondo=CARTA2, pad=(120, 110))


def tempo_in_piu(t):
    calcolo = t.classic('projector', {
        'eyebrow': 'Tempo in più', 'heading': 'Una prova <em>a tempo</em>',
        'intro': 'Sposta il cursore sulla durata della prova: il tempo in più arriva fino al doppio.',
        'min': '10', 'max': '120', 'step': '5', 'value': '45', 'rate': '0', 'years': '2', 'currency': '',
        'input_label': 'Durata della prova', 'input_unit': 'minuti', 'out_caption': 'minuti al massimo, con il tempo in più',
        'result_suffix': '', 'decimals': 0, 'note': 'Il tempo in più è una percentuale, fino al doppio.',
        'show_contrib': False, 'list_base': 1, 'list_items': [], 'zone_accent': GIALLO, 'card_bg': LAV2, 'align': 'left',
        'tile_padding': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40}, 'border_radius': 24, 'shadow': 'none'})
    return sez(t, [
        due(t, [occ(t, 'Legge 170/2010', col=GIALLO), tit(t, 'Il tempo in più, <em>fino al doppio</em>', size=48, col=GESSO,
                                                           acc=GIALLO),
                txt(t, 'Una percentuale di tempo in più su ogni prova a tempo, fino al doppio, per la misura compensativa '
                       'prevista dalla legge 170/2010.', col=GESSO_T)],
            [calcolo], peso=(46, 54), valign='center'),
    ], fondo=LAV, pad=(110, 110))


def ente(t):
    freq = t.classic('countercircle', {'value': '70', 'max_value': '100', 'suffix': '%', 'prefix': '',
                                       'title': 'la frequenza minima che il docente può fissare, per esempio', 'size': '240',
                                       'stroke_width': '7', 'stroke_color': VERDE, 'track_color': FILO, 'text_color': VERDE,
                                       'title_color': TENUE, 'duration': '2400', 'title_position': 'below', 'text_effect': 'none'})
    return sez(t, [
        due(t, [occ(t, 'Enti di formazione'), tit(t, 'Se l\'istituto è <em>un ente di formazione</em>', size=52),
                txt(t, 'Per un ente contano di più le presenze a durata effettiva, le unità formative numerate in ordine dentro '
                       'la classe, il monte ore dichiarato accanto alle ore a calendario, la permanenza minima sulle lezioni, le '
                       'intestazioni FSE e PNRR e le iscrizioni su approvazione o a pagamento.', pad=(0, 0, 14, 0)),
                txt(t, 'L\'attestato riporta il monte ore del corso e le ore frequentate, contate dal registro senza le assenze '
                       'giustificate. Nei corsi frontali parte da solo il giorno dopo la fine del corso, a chi ha la frequenza, e '
                       'il docente può rilasciarlo prima con un pulsante.')],
            [freq, t.row([t.col([
                occ(t, 'Il QR sull\'attestato', pad_bottom=8),
                txt(t, 'Apre una pagina di verifica che si legge senza accedere: dice a chi è stato rilasciato, per quale corso '
                       'e quando, con quante ore, e se è ancora valido.', col=INK)],
                style=scheda(GESSO, r=22, pad=(26, 26, 26, 26), bordo=FILO))], anim='fade', style={'margin_top': 28})],
            peso=(56, 44), valign='center'),
    ], fondo=CIELO, pad=(120, 120))


def limiti(t):
    voci = [('Il registro del ministero', 'OLOtutor tiene gli argomenti delle lezioni, le note disciplinari e didattiche e lo '
             'scrutinio della classe, ma non ha i collegamenti con i sistemi del ministero: in una scuola statale il registro '
             'elettronico ministeriale resta necessario, e lo scrutinio ufficiale si chiude lì.'),
            ('L\'accessibilità, per iscritto', 'Il lavoro sui criteri WCAG 2.1 AA è stato fatto e misurato, ma la relazione '
             'di conformità EN 301 549 da allegare alla dichiarazione AgID non esiste ancora.'),
            ('Le partite dal vivo', 'Una partita funziona bene con 50 studenti contemporanei senza il servizio aggiuntivo per '
             'il tempo reale, e con 100 usandolo.')]
    return t.section([
        t.one([occ(t, 'Da sapere prima di cominciare', col=GIALLO), tit(t, 'Note operative <em>e limiti</em>', size=56,
                                                                          col=GESSO, acc=GIALLO),
               mano(t, 'lo diciamo prima, invece di farlo scoprire dopo.', col=VERDE_G, size=30, pad_bottom=24)]),
        griglia(t, [[occ(t, a, col=GIALLO, pad_bottom=10), txt(t, b, col=GESSO_T, size=17)] for a, b in voci], n=3, gap=20,
                stili=scheda(LAV2, r=22, pad=(30, 28, 30, 28), bordo=GESSO_F)),
    ], bg={'type': 'image', 'image_url': img('otr-onesta-cerchi.svg'), 'image_size': 'cover', 'image_position': 'left bottom',
           'overlay_color': LAV, 'overlay_opacity': 0}, padding='large', style={'padding_top': 120, 'padding_bottom': 120})


def build():
    t = Tpl('otr', 'scuola')
    return [
        K.apertura(t, apertura(t)),
        un_calcolo(t),
        organizzazione(t),
        presenze(t),
        famiglie(t),
        tempo_in_piu(t),
        altre_funzioni(t, [
            ('shield-check', 'Ruoli e permessi', '8 ruoli e 20 permessi che si accendono per ruolo. La segreteria può ricevere '
                                                 'solo quello che le serve, e i controlli stanno sul server, non solo nei menu.'),
            ('handshake', 'Tutoraggi', 'Incarichi per una persona, un gruppo o un corso, di tipo didattico, orientamento, PCTO, '
                                       'inclusione e fra pari, con una scheda stampabile per la rendicontazione.'),
            ('timer', 'Tempo in più', 'Una percentuale di tempo in più su ogni prova a tempo, fino al doppio, per la misura '
                                      'compensativa prevista dalla legge 170/2010.'),
            ('badge-check', 'Certificati', 'Modelli comuni disegnati da chi coordina. L\'attestato parte da solo quando il corso '
                                           'è completato, con un numero e un collegamento di verifica.'),
            ('languages', 'Accessibilità e lingue', 'Schermate riviste e misurate sui criteri WCAG 2.1 AA e interfaccia in '
                                                    'italiano, inglese, tedesco e francese, nella lingua del profilo di ognuno.'),
            ('sprout', 'Adozione graduale', 'Punti, classifica, certificati, forum, messaggi e ripasso si spengono per tutto il '
                                            'sito, e si può cominciare da una classe e da un corso.'),
        ]),
        ente(t),
        limiti(t),
        viste(t, 'Le stesse funzioni, viste dalle altre due figure', [VISTE['studente'], VISTE['docente']]),
        chiusura(t),
    ]
