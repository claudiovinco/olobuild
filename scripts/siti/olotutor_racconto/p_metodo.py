# -*- coding: utf-8 -*-
"""Il metodo: la carta di ripasso che si gira scorrendo, i dieci riferimenti come carte."""
from comune import (K, Tpl, mix, radius, solid, img, occ, tit, txt, mano, btn, bottoni, foto, schermata, sez, due, griglia,
                    scheda, chiusura, flip_carta, marquee, MANO, CARTA, INK, TENUE, LAV, VERDE, ONV, GIALLO, GESSO, CIELO,
                    FILO, ROSSO, ARANCIO, AZZURRO, VERDE_G, GESSO_T, GESSO_F, LAV2, CARTA2, PRO)

m = K.mob


def apertura(t):
    L = K.livello
    liv = [
        L('m-occ', contenuto='La posizione', x=6, y=16, w=40, size=14, peso='700', colore=GIALLO, letterSpacing=2,
          textTransform='uppercase', ritardo=100, responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 2.4, 'width': 88}}),
        L('m-t1', contenuto='Il metodo,', tag='h1', x=6, y=20, w=46, size=96, peso='700', colore=GESSO, fontFamily='Lexend',
          lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=250, durata=1000,
          responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 4.2, 'width': 88}}),
        L('m-t2', contenuto='prima del software.', tag='p', x=6, y=33, w=46, size=54, peso='700', colore=GIALLO,
          fontFamily='Lexend', lineHeight=1.0, letterSpacing=-1, entra='maskFromBottom', ritardo=450, durata=1000,
          responsive={'mobile': {'fontSize': m(28), 'x': 6, 'y': 9.8, 'width': 88}}),
        L('m-s', contenuto='OLOtutor non promette risultati perché è digitale. I metodi che usa funzionavano prima del '
          'software, e funzionerebbero anche senza. Il docente li conosce già, e non ha il tempo di applicarli: il software '
          'serve a quello.', x=6, y=45, w=38, size=18, peso='400', colore=GESSO_T, lineHeight=1.55, ritardo=700,
          responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 14.6, 'width': 88}}),
        L('m-b', tipo='button', contenuto='I dieci riferimenti', x=6, y=63, w='auto', size=16, peso='700', colore=INK,
          bgColor=GIALLO, padding=17, borderRadius=12, buttonUrl='#riferimenti', ritardo=900, entra='fadeIn',
          hover={'bgColor': GESSO, 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 29.4, 'fontSize': m(13)}}),
        L('m-mano', contenuto='scorri, e la carta si gira ↻', x=6, y=76, w=38, size=30, peso='700', colore=VERDE_G,
          fontFamily=MANO, entra='fadeIn', ritardo=1200, responsive={'mobile': {'visible': False}}),
    ]
    # la carta: davanti la domanda, dietro la risposta; ogni livello gira attorno al suo centro, e tutti hanno la stessa
    # x e la stessa larghezza della carta, quindi girano attorno allo stesso asse
    X, W, Y, H = 56, 34, 13, 52
    TX, TW, TY, TH = 8, 84, 36.5, 34
    davanti = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': 0, 'o': 100}, {'p': 20, 'ry': 0, 'o': 100},
                                                   {'p': 39, 'ry': 90, 'o': 100}, {'p': 39.5, 'ry': 90, 'o': 0},
                                                   {'p': 100, 'ry': 90, 'o': 0}]}
    dietro = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'ry': -90, 'o': 0}, {'p': 39, 'ry': -90, 'o': 0},
                                                  {'p': 39.5, 'ry': -90, 'o': 100}, {'p': 58, 'ry': 0, 'o': 100},
                                                  {'p': 100, 'ry': 0, 'o': 100}]}
    ombra = {'x': 0, 'y': 30, 'blur': 60, 'spread': -16, 'color': mix('#000000', 50)}
    for lato, fondo, col, occh, frase, piede, sm in [
            ('f', GESSO, INK, 'Domanda · carta 1 di 10', 'Qual è il momento migliore per rivedere una cosa?',
             'gira la carta: scorri', davanti),
            ('r', GIALLO, INK, 'Risposta', 'Poco prima di dimenticarla.',
             'FSRS 4 · Free Spaced Repetition Scheduler, algoritmo aperto e documentato', dietro)]:
        liv += [
            L('m-%s-carta' % lato, tipo='shape', x=X, y=Y, w=W, h=H, bgColor=fondo, borderRadius=26, boxShadow=ombra,
              entra='fadeInUp', ritardo=400, durata=1000, scrollMove=sm,
              responsive={'mobile': {'x': TX, 'y': TY, 'width': TW, 'height': TH}}),
            L('m-%s-occ' % lato, contenuto=occh, x=X, y=Y + 6, w=W, size=14, peso='700', colore=VERDE if lato == 'f' else INK,
              textAlign='center', letterSpacing=2, textTransform='uppercase', entra='fadeIn', ritardo=600, scrollMove=sm,
              responsive={'mobile': {'x': TX, 'y': TY + 3.5, 'width': TW, 'fontSize': m(11)}}),
            L('m-%s-frase' % lato, contenuto=frase, tag='p', x=X + 3, y=Y + 15, w=W - 6, size=38, peso='700', colore=col,
              fontFamily='Lexend', textAlign='center', lineHeight=1.15, entra='fadeIn', ritardo=650, scrollMove=sm,
              responsive={'mobile': {'x': TX + 5, 'y': TY + 9, 'width': TW - 10, 'fontSize': m(24)}}),
            L('m-%s-piede' % lato, contenuto=piede, x=X + 3, y=Y + H - 11, w=W - 6, size=14, peso='400',
              colore=TENUE if lato == 'f' else INK, textAlign='center', lineHeight=1.4, entra='fadeIn', ritardo=700,
              scrollMove=sm, responsive={'mobile': {'x': TX + 4, 'y': TY + TH - 6.5, 'width': TW - 8, 'fontSize': m(11)}}),
        ]
    # i quattro tasti del ripasso, che arrivano dopo la risposta
    tasti = [('Di nuovo', ROSSO, GESSO, 59.7, 15.3), ('Difficile', ARANCIO, INK, 67.4, 35.3), ('Bene', VERDE, GESSO, 75.7, 57.3),
             ('Facile', AZZURRO, GESSO, 80.9, 70.3)]
    for k, (s, fondo, col, x, tx) in enumerate(tasti):
        da = 62 + k * 4
        liv.append(L('m-tasto%d' % k, contenuto=s, x=x, y=69, w='auto', size=16, peso='700', colore=col, bgColor=fondo,
                     padding=12, borderRadius=10, entra='fadeIn', ritardo=0,
                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 20}, {'p': da, 'o': 0, 'y': 20},
                                                                     {'p': da + 6, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]},
                     responsive={'mobile': {'x': tx, 'y': 73.5, 'fontSize': m(13)}}))
    liv.append(L('m-fine', contenuto='Da quella risposta dipende il giorno in cui la carta torna.', x=X, y=78, w=W, size=17,
                 peso='400', colore=GESSO_T, textAlign='center', lineHeight=1.4, entra='fadeIn', ritardo=0,
                 scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0}, {'p': 80, 'o': 0}, {'p': 88, 'o': 100},
                                                                 {'p': 100, 'o': 100}]},
                 responsive={'mobile': {'x': TX, 'y': 79, 'width': TW, 'fontSize': m(13)}}))
    sfondo = {'type': 'image', 'image': img('otr-hero-navy-bg.svg'), 'video': '', 'color': '', 'overlay': LAV,
              'overlayOpacity': 0, 'kenBurns': False}
    sl = [K.diapositiva('m-1', sfondo, liv, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=2000, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400}, globalBackground={'type': 'color', 'color': LAV})


def digitale(t):
    usi = [('Calcolare la data di ripasso di centinaia di schede per decine di studenti', 'ripasso distanziato',
            'otr-carta-ripasso.webp'),
           ('Presentare le domande in ordine intercalato invece che a blocchi', 'pratica intercalata', 'otr-studente-ripasso.webp'),
           ('Raccogliere due giri di voto da tutta la classe, anche senza telefoni', 'peer instruction', 'otr-cartoncini.webp'),
           ('Mantenere la stessa rubrica coerente su cinquanta consegne', 'rubriche analitiche', 'otr-portale.webp')]
    hl = t.classic('hoverlist', {
        'items': [{'name': a, 'sub': b, 'desc': '', 'image': img(c), 'color': VERDE} for a, b, c in usi],
        'lead_mode': 'number', 'mono_font_family': "'Atkinson Hyperlegible', sans-serif", 'number_color': VERDE, 'number_hover_color': INK, 'name_font_family': 'heading',
        'name_color': INK, 'name_hover_color': VERDE, 'name_size': 24, 'name_uppercase': False, 'sub_color': VERDE,
        'sub_hover_color': INK, 'sub_size': 15, 'sub_uppercase': True, 'row_padding_y': 22, 'hover_indent': 18,
        'row_bg_color': '', 'hover_bg': mix(VERDE, 8), 'line_color': FILO, 'peek': True, 'peek_mode': 'image',
        'peek_width': 240, 'peek_ratio': '3/4'})
    return sez(t, [
        due(t, [occ(t, 'Che cosa fa il software'),
                tit(t, 'Il digitale non insegna al posto tuo. <em>Serve a rendere veloci dei metodi che conosci già</em>, '
                       'e che dentro le tue ore non riusciresti ad applicare.', size=38, sizes=(32, 25)),
                mano(t, 'quattro esempi: passaci sopra →', col=VERDE, size=28, pad_bottom=0)],
            [hl], peso=(46, 54), valign='center'),
    ], fondo=CARTA, pad=(120, 110))


RIF = [  # nome, che cosa dice, dentro OLOtutor, dove leggerlo
    ('Butterfield e Metcalfe (2001)', 'Un errore fatto con sicurezza, una volta corretto, si ricorda meglio di uno fatto nel '
     'dubbio.', 'Le domande sbagliate con sicurezza tornano prima delle altre nel ripasso, nei quiz in cui il docente chiede '
     'quanto si è sicuri.', 'Butterfield e Metcalfe (2001), Errors committed with high confidence are hypercorrected. Journal '
     'of Experimental Psychology: LMC, 27(6).'),
    ('Eric Mazur, Peer Instruction', 'Si vota, se ne parla col vicino, si vota di nuovo: il confronto fra pari sposta le '
     'risposte.', 'Il doppio giro di voto del sondaggio d\'aula.', 'Mazur, E. (1997), Peer Instruction: A User\'s Manual. '
     'Prentice Hall.'),
    ('FSRS 4', 'Il momento migliore per rivedere una cosa è poco prima di dimenticarla.', 'Calcola la data di ritorno di ogni '
     'carta di ripasso.', 'Free Spaced Repetition Scheduler, versione 4, algoritmo aperto e documentato.'),
    ('Pratica intercalata', 'Alternare argomenti diversi costa più fatica sul momento, e prepara meglio a una verifica, dove '
     'gli argomenti sono mescolati.', 'L\'ordine in cui escono le domande: intercalato, non a blocchi.',
     'Rohrer, D. e Taylor, K. (2007), The shuffling of mathematics problems improves learning. Instructional Science, 35.'),
    ('Pratica di recupero', 'Tirare fuori una risposta dalla memoria la fissa più che rileggerla.', 'I quiz di allenamento, '
     'esclusi dalle medie.', 'Roediger, H. L. e Karpicke, J. D. (2006), Test-enhanced learning. Psychological Science, 17(3).'),
    ('Lezione capovolta', 'I contenuti si studiano prima, e il tempo in classe serve a lavorarci insieme.', 'A casa si studia '
     'sui materiali assegnati, in aula si lavora su quello che si è studiato.', 'Bergmann, J. e Sams, A. (2012), Flip Your '
     'Classroom. ISTE.'),
    ('Rubriche analitiche', 'Un giudizio si può discutere solo se i criteri sono scritti prima.', 'Griglie per la valutazione '
     'dei compiti, con quattro livelli di partenza.', 'Brookhart, S. M. (2013), How to Create and Use Rubrics. ASCD.'),
    ('Quadri di competenza', 'Le competenze si descrivono con le parole dei quadri ufficiali.', 'Sei quadri di riferimento, '
     'cinque con i descrittori nelle parole ufficiali.', 'DigComp, LifeComp, GreenComp, RFCDC, EntreComp, QCER. Commissione '
     'europea e Consiglio d\'Europa.'),
    ('Open Badges 2.0, WCAG 2.1 AA', 'Un riconoscimento vale se resta leggibile fuori dal sito che lo ha rilasciato. '
     'Un\'interfaccia vale se tutti possono usarla.', 'Badge in formato aperto e interfaccia conforme ai criteri di '
     'accessibilità.', '1EdTech, Open Badges 2.0. W3C, WCAG 2.1, riferimento della norma EN 301 549.'),
    ('AprilTag, famiglia tag36h11', 'Un cartoncino stampato basta a raccogliere le risposte di una classe intera.',
     'La risposta d\'aula senza dispositivi: la fotocamera legge i cartoncini.', 'Olson, E. (2011), AprilTag: a robust and '
     'flexible visual fiducial system. IEEE ICRA.'),
]
COLORI = [(GESSO, INK), (GIALLO, INK), (VERDE, GESSO), (LAV, GESSO), (GESSO, INK), (GIALLO, INK), (VERDE, GESSO), (LAV, GESSO),
          (GESSO, INK), (GIALLO, INK)]


def riferimenti(t):
    carte = []
    for i, ((nome, dice, dentro, fonte), (fb, fc)) in enumerate(zip(RIF, COLORI)):
        c = flip_carta(t, '%02d · %s' % (i + 1, nome), dice, 'Dentro OLOtutor', '%s — %s' % (dentro, fonte), fb, fc, INK, GESSO,
                       alto=400, size=20)
        c['settings'].update({'desc_size': '15', 'front_valign': 'top', 'back_valign': 'top',
                              'tile_padding': {'top': 26, 'right': 24, 'bottom': 26, 'left': 24}})
        carte.append([c])
    return sez(t, [
        due(t, [occ(t, 'I riferimenti'), tit(t, 'I dieci <em>riferimenti</em>', size=56)],
            [txt(t, 'Ogni cosa che OLOtutor fa in didattica viene da un riferimento preciso. Per ognuno c\'è scritto che cosa '
                    'dice, che cosa fa qui dentro e dove andarlo a leggere.'),
             mano(t, 'dieci carte: girale, come nel ripasso ↻', col=VERDE, size=28, pad_bottom=0)],
            peso=(50, 50), valign='end'),
        griglia(t, carte, n=5, gap=16),
    ], fondo=CIELO, pad=(120, 120), html_id='riferimenti')


def onesta(t):
    return t.section([
        due(t, [occ(t, 'Il limite, detto prima', col=GIALLO), tit(t, 'Una nota <em>onesta</em>', size=64, col=GESSO, acc=GIALLO)],
            [txt(t, 'Un metodo che ha funzionato in uno studio non funziona per forza allo stesso modo dentro un programma. '
                    'Rispettiamo il meccanismo descritto dalle ricerche, ma ogni classe e materia fanno storia a sé.', size=20,
                 col=GESSO_T),
             btn(t, 'La verifica sul campo', PRO, 'giallo')], peso=(45, 55), valign='center'),
    ], bg={'type': 'image', 'image_url': img('otr-onesta-cerchi.svg'), 'image_size': 'cover', 'image_position': 'left bottom',
           'overlay_color': LAV, 'overlay_opacity': 0}, padding='large', style={'padding_top': 120, 'padding_bottom': 120})


def build():
    t = Tpl('otr', 'metodo')
    return [
        K.apertura(t, apertura(t)),
        digitale(t),
        riferimenti(t),
        onesta(t),
        t.section([t.one([marquee(t, 'Ripasso distanziato | Pratica di recupero | Pratica intercalata | Peer Instruction | '
                                     'Ipercorrezione | Rubriche analitiche | Open Badges 2.0')], anim=None)],
                  width='fullbleed', padding='remove-vertical'),
        chiusura(t),
    ]
