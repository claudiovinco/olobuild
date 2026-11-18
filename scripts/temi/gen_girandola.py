# -*- coding: utf-8 -*-
"""Genera il tema «Girandola» (id girandola): una famiglia di fuochisti a Napoli dal 1958 — matrimoni, feste di paese,
capodanni, spettacoli sul mare; i permessi e la sicurezza, il preventivo da comporre.

La trovata: «la parola che parte». Nell'apertura le nove lettere del nome aspettano a terra come razzi sulla rampa; scorrendo
partono una dopo l'altra con la loro scia e in cima scoppiano, in oro, rosa e azzurro, finché la parola sta in cielo. Nelle
pagine gli scoppi della sezione sui colori si aprono mentre scorri.

Tema scuro: notte blu, oro, rosa, azzurro. Titoli in Epilogue grassetto, testo in Readex Pro. Cursore: miccia (faville, fuoco d'artificio
al clic).
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'girandola'
NAME = 'Girandola'
CATEGORIA = 'Eventi e musica'

HEX = {
    'primary': '#FFC94A', 'primary_contrast': '#140F05', 'secondary': '#FF3D7F', 'secondary_contrast': '#FFFFFF',
    'accent': '#3DDCFF', 'dark': '#070A1F', 'light': '#FFFFFF', 'muted': '#11163A', 'muted_contrast': '#E9ECFF',
    'text': '#E9ECFF', 'text_muted': '#A6ACCF', 'background': '#0A0E27', 'border': '#262C57', 'link': '#FFC94A',
}
HOME, SPETTACOLI, SICUREZZA, PREVENTIVO = '{{PAGINA:home}}', '{{PAGINA:spettacoli}}', '{{PAGINA:sicurezza}}', '{{PAGINA:preventivo}}'
MAIL, PHONE, PHONE_TEL = 'spettacoli@girandola.example', '081 000 1958', 'tel:+390810001958'
LAT, LNG = '40.8518', '14.2681'

V_RAZZI, V_ROSSO, V_ACQUA, V_SCINTILLE, V_FOLLA, V_SCOPPIO, V_STELLINA = (
    'pexels-video-18686281', 'pexels-video-17737412', 'pexels-video-38736128', 'pexels-video-9665450', 'pexels-video-27806811',
    'pexels-video-28469346', 'pexels-video-7685044')
VIDEOS = [V_RAZZI, V_ROSSO, V_ACQUA, V_SCINTILLE, V_FOLLA, V_SCOPPIO, V_STELLINA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(TRE_SCOPPI, PALMA_VERDE, CRISANTEMO, ROSSI_SPARSI, BLU_BIANCO, ROSSO_PORTO, FONTANE_ROSSE, LASER, FONTANE_BIANCHE,
 SALICE, SAGOME_FUOCO, CANDELE, SAGOME_BLU, SAGOME_ORO, FESTA_LUCI, LUNA_FESTA, CAMPANILE, BORGO, CHIATTA, LAGO_ORO,
 RAZZO_LAGO, GOLFO, SPOSI, SPOSI_BACIO, SPOSI_CORTEO, SPOSA, STELLINA) = f(
    1503520, 27051702, 266497, 6942352, 949592, 27068360, 35497602, 1317364, 37094397, 3256800, 34408491, 13086763,
    34408521, 34408502, 15822284, 36513643, 6069425, 27517288, 40101766, 19561041, 17545544, 40866289, 20519799, 29081017,
    13110256, 15549004, 34416999)
PHOTOS = [TRE_SCOPPI, PALMA_VERDE, CRISANTEMO, ROSSI_SPARSI, BLU_BIANCO, ROSSO_PORTO, FONTANE_ROSSE, LASER, FONTANE_BIANCHE,
          SALICE, SAGOME_FUOCO, CANDELE, SAGOME_BLU, SAGOME_ORO, FESTA_LUCI, LUNA_FESTA, CAMPANILE, BORGO, CHIATTA, LAGO_ORO,
          RAZZO_LAGO, GOLFO, SPOSI, SPOSI_BACIO, SPOSI_CORTEO, SPOSA, STELLINA]
SCOPPI = ['girandola-scoppio-oro.svg', 'girandola-scoppio-rosa.svg', 'girandola-scoppio-azzurro.svg']
LOGHI = ['girandola-logo-chiaro.png', 'girandola-logo-scuro.png']
MEDIA = set(PHOTOS) | set(SCOPPI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=18, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
FONT_T = 'Epilogue'
COLORI = ['#FFC94A', '#FF3D7F', '#3DDCFF']
VOCI = [('map-pin', 'Via Marina 14 · Napoli', PREVENTIVO + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def scoppia(tile, da=0.35, a=1.15):
    """Uno scoppio che si apre mentre la pagina scorre: cresce e si fa più chiaro a metà schermo (parallax delle Avanzate)."""
    tile.setdefault('advanced', {})['parallax'] = {'scale': [{'value': da, 'position': 0}, {'value': a, 'position': 55}],
                                                    'opacity': [{'value': 0.2, 'position': 0}, {'value': 1, 'position': 45},
                                                                {'value': 0.6, 'position': 100}]}
    return tile


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 180, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '697/126', 'Girandola: fuochi d’artificio a Napoli dal 1958. Matrimoni, feste di paese, '
                   'capodanni e spettacoli sul mare, coi permessi e la squadra della sicurezza.',
                   [('Gli spettacoli', SPETTACOLI), ('La sicurezza', SICUREZZA), ('Preventivo', PREVENTIVO),
                    ('Privacy', '/privacy-policy/')],
                   'Il laboratorio', 'Lunedì — venerdì 9 — 18<br>Il sabato su appuntamento', VOCI,
                   '© 2026 Girandola snc · Napoli · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                      ('youtube', 'https://youtube.com')], scuro=True)


# ── L'apertura: la parola che parte ──
# Le lettere nelle loro caselle (x e larghezza in % della tela, computer e telefono), larghe come le lettere di Epilogue 700
# misurate in Chrome (misura_lettere.py, a 170 px su 1440 e a 82 su 640): a terra a p 0, poi una per volta in cielo (gli
# spostamenti sono in px della tela), con la scia sotto e lo scoppio dietro all'arrivo.
LETTERE = [('G', 15.08, 8.83, 12.1, 9.58), ('I', 23.91, 3.6, 21.69, 3.91), ('R', 27.51, 7.88, 25.6, 8.55),
           ('A', 35.39, 8.23, 34.15, 8.94), ('N', 43.63, 9.04, 43.08, 9.81), ('D', 52.67, 8.28, 52.89, 8.98),
           ('O', 60.94, 9.16, 61.87, 9.94), ('L', 70.1, 6.58, 71.82, 7.14), ('A', 76.68, 8.23, 78.96, 8.94)]
Y_CIELO, Y_CIELO_TEL = 27, 33
ALTA, ALTA_TEL = 21, 5.9  # l'altezza della lettera in % della tela
TERRA = 420


def apertura(t):
    m = K.mob
    L = [K.livello('gi-velo', tipo='shape', x=0, y=0, w=100, h=100, bgColor=mix(C['nero'], 24), entra='fadeIn', ritardo=0,
                   durata=600, esce='fadeOut')]
    lettere, scie, scoppi = [], [], []
    for k, (lettera, x, w, xm, wm) in enumerate(LETTERE):
        da = 6 + k * 6
        arrivo = da + 14
        col = COLORI[k % 3]
        cx, cxm = x + w / 2, xm + wm / 2
        vola = {'inizio': 0, 'fine': 100, 'tappe': [
            {'p': 0, 'y': TERRA, 's': 0.55, 'r': 0}, {'p': da, 'y': TERRA, 's': 0.55, 'r': 0},
            {'p': da + 9, 'y': -36, 's': 1.06, 'r': (-6 if k % 2 else 6)}, {'p': arrivo, 'y': 0, 's': 1, 'r': 0},
            {'p': 100, 'y': 0, 's': 1, 'r': 0}]}
        lettere.append(K.livello('gi-l%d' % k, contenuto=lettera, x=x, y=Y_CIELO, w=w, size=170, peso='700', colore=col,
                                 align='center', fontFamily=FONT_T, lineHeight=1.0, entra='fadeInUp', ritardo=200 + k * 70,
                                 durata=700, customCSS='transform-origin:50% 50%;',
                                 scrollMove=vola,
                                 responsive={'mobile': {'fontSize': m(50), 'x': xm, 'y': Y_CIELO_TEL, 'width': wm}}))
        scia = {'inizio': 0, 'fine': 100, 'tappe': [
            {'p': 0, 'y': TERRA, 'o': 0}, {'p': da, 'y': TERRA, 'o': 0}, {'p': da + 2, 'y': TERRA - 60, 'o': 100},
            {'p': da + 9, 'y': -36, 'o': 70}, {'p': arrivo, 'y': 0, 'o': 0}, {'p': 100, 'y': 0, 'o': 0}]}
        scie.append(K.livello('gi-s%d' % k, tipo='shape', x=cx - 0.2, y=Y_CIELO + ALTA, w=0.4, h=30, entra='fadeIn', ritardo=0,
                              durata=300, shapeGradient={'angle': 180, 'from': mix(col, 92), 'to': mix(col, 0), 'fromStop': 5},
                              scrollMove=scia,
                              responsive={'mobile': {'x': cxm - 0.4, 'y': Y_CIELO_TEL + ALTA_TEL, 'width': 0.8, 'height': 16}}))
        botto = {'inizio': 0, 'fine': 100, 'tappe': [
            {'p': 0, 's': 0.1, 'o': 0}, {'p': arrivo - 1, 's': 0.1, 'o': 0}, {'p': arrivo, 's': 0.35, 'o': 100},
            {'p': arrivo + 10, 's': 1.3, 'o': 0}, {'p': 100, 's': 1.3, 'o': 0}]}
        scoppi.append(K.livello('gi-sc%d' % k, tipo='image', imageSrc=K.img(SCOPPI[k % 3]), imageAlt='', objectFit='contain',
                                x=cx - 11, y=Y_CIELO + ALTA / 2 - 19.5, w=22, h=39, entra='fadeIn', ritardo=0, durata=300,
                                customCSS='transform-origin:50% 50%;', scrollMove=botto,
                                responsive={'mobile': {'x': cxm - 13, 'y': Y_CIELO_TEL + ALTA_TEL / 2 - 5.9, 'width': 26, 'height': 11.9}}))
    poi = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 3}, {'p': da, 'o': 0, 'y': 3},
                                                             {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    L += scoppi + scie + lettere + [
        K.livello('gi-o', contenuto='Girandola · fuochi d’artificio · Napoli dal 1958', tag='h1', x=6, y=12, w=88, size=13,
                  peso='500', colore=mix(C['chiaro'], 86), align='center', letterSpacing=3, textTransform='uppercase', ritardo=150,
                  responsive={'mobile': {'fontSize': m(10), 'x': 4, 'y': 9.5, 'width': 92}}),
        K.livello('gi-f', contenuto='Matrimoni, feste, capodanni: lo spettacolo lo scriviamo insieme.', x=12, y=56, w=76, size=28,
                  peso='500', colore=C['chiaro'], align='center', lineHeight=1.25, entra='fadeIn', ritardo=0, scrollMove=poi(70, 80),
                  responsive={'mobile': {'fontSize': m(18), 'x': 6, 'y': 46, 'width': 88}}),
        K.livello('gi-b1', tipo='button', contenuto='Il preventivo', x=36.5, y=68, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=18, borderRadius=999, letterSpacing=2, textTransform='uppercase', buttonUrl=PREVENTIVO,
                  entra='fadeIn', ritardo=0, scrollMove=poi(78, 88), hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 8, 'y': 55, 'fontSize': m(12)}}),
        K.livello('gi-b2', tipo='button', contenuto='Gli spettacoli', x=52, y=68, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  letterSpacing=2, textTransform='uppercase', buttonUrl=SPETTACOLI, entra='fadeIn', ritardo=0, scrollMove=poi(78, 88),
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250}, responsive={'mobile': {'x': 54, 'y': 55, 'fontSize': m(12)}}),
    ]
    sl = [K.diapositiva('gi-1', K.sfondo_video(V_RAZZI, overlay=0.2, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Girandola · Napoli, dal 1958', ['Tre generazioni', 'col naso all’insù.'], size=64,
                                layout='stack', size_mobile=38)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Il nonno Gennaro caricava i mortai a mano per la festa del Carmine. Oggi gli spettacoli partono '
                                'da un computer, sincronizzati alla musica, ma i colori li mescoliamo ancora noi, in laboratorio.',
                             size=17),
                      K.button(t, 'Il preventivo', PREVENTIVO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli spettacoli', ['Una sera,', 'tanti cieli.'], size=58)]),
        P.carte(K, t, [(SPOSI_CORTEO, 'Matrimoni', 'Il primo ballo, il cielo', 'Fontane fredde per il ballo, poi dieci minuti sul '
                        'giardino. Anche a basso rumore.'),
                       (CAMPANILE, 'Feste di paese', 'Il santo, la banda, i botti', 'Dal campanile alla piazza: lo spettacolo '
                        'più antico, e il più amato.'),
                       (CHIATTA, 'Capodanno', 'Mezzanotte in punto', 'Il conto alla rovescia sincronizzato, al secondo.'),
                       (RAZZO_LAGO, 'Sul mare', 'Dalla chiatta', 'A duecento metri dalla riva: ogni colpo arriva due volte, '
                        'in cielo e nell’acqua.')], altezza='420', colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_ACQUA, 'Sul mare', 'Il mare <em>li raddoppia.</em>',
                             'Dalla chiatta nel golfo: lo stesso spettacolo, due volte, e nessuno sotto i colpi.', overlay=26))
    out.append(t.section([
        t.one([K.sechead(t, 'In laboratorio', ['Ogni colore', 'ha il suo sale.'], size=56)]),
        t.row([t.col([scoppia(K.image(t, SCOPPI[k], '', ratio='1/1')),
                      K.headline(t, ti, size=30, tag='h3', align='center', pad_bottom=6),
                      K.text(t, tx, size=16, align='center')], wm='1-3', ws='1-1')
               for k, ti, tx in ((0, 'L’oro', 'Limatura di ferro e carbone: le scintille lunghe che cadono come un salice.'),
                                 (1, 'Il rosa e il rosso', 'Lo stronzio. Il rosso è il primo colore che abbiamo imparato.'),
                                 (2, 'L’azzurro', 'Il rame, il più difficile: un grado in più e diventa bianco.'))],
              layout='33-33-33', gap=40),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Quest’estate', ['Cieli', 'da ricordare.'], size=56)]),
        t.one([K.coverflow(t, [(BLU_BIANCO, 'Uno scoppio bianco e rosa sul blu', 'Ravello', 'luglio'),
                               (ROSSO_PORTO, 'Una palma rossa sul porto', 'Sorrento', 'agosto'),
                               (LASER, 'Fuochi e luci sul castello', 'Baia', 'settembre'),
                               (LAGO_ORO, 'Scoppi d’oro riflessi sul lago', 'Lago Patria', 'giugno'),
                               (GOLFO, 'Fuochi rossi e verdi sul golfo', 'Pozzuoli', 'agosto')], altezza=440, larghezza=340)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([K.numeri(t, [('1958', 'il primo spettacolo'), ('3', 'generazioni'), ('180', 'serate l’anno'),
                                              ('0', 'incidenti')], colonne=4, size=56)])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(X.recensioni(K, t, [
        ('Il primo ballo sotto le fontane fredde e poi il cielo tutto rosa: gli ospiti ne parlano ancora.', 'Chiara e Marco', 'Ravello'),
        ('Trent’anni di festa del patrono, sempre con loro. Il botto finale si sente a Pozzuoli.', 'Comitato San Gennaro', 'Agnano'),
        ('Capodanno sincronizzato al secondo con la musica della piazza. E le carte dei permessi, tutte in ordine.', 'Ufficio eventi',
         'Comune di Sorrento')], 'Chi ha guardato in su', ['Tre sere,', 'tre cieli.']))
    out.append(K.pannello(t, SAGOME_ORO, 'Il tuo spettacolo', 'Scriviamolo <em>insieme.</em>',
                          'Durata, colori, musica, il botto finale: lo componi in due minuti e ti richiamiamo per il sopralluogo.',
                          'Il preventivo', PREVENTIVO))
    return out


def build_spettacoli():
    t = Tpl(ID, 'spettacoli')
    out = [K.intro(t, 'Gli spettacoli', ['Dal primo ballo', 'al botto di mezzanotte.'],
                   'Ogni spettacolo nasce da un sopralluogo: guardiamo il cielo, gli alberi, le case e da dove guarderete voi.',
                   bg=K.video_bg(V_ROSSO, overlay=30), size=80)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Matrimoni', 'I matrimoni', 'anche a basso rumore', 'Le fontane fredde per il primo ballo, poi dieci minuti di cielo: '
         'col fuoco silenzioso per gli animali e i vicini.', SPOSA),
        ('Feste', 'Le feste di paese', 'col santo e la banda', 'Il campanile, la piazza, la processione: lo spettacolo di sempre, '
         'coi colori di oggi.', BORGO),
        ('Capodanno', 'I capodanni', 'al secondo', 'Il conto alla rovescia e la musica sincronizzata dal computer.', CHIATTA),
        ('Mare', 'Sul mare', 'dalla chiatta', 'Il cielo e il suo riflesso, e la gente sulla riva lontana dai colpi.', PALMA_VERDE)],
        altezza=420)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I matrimoni', ['Le stelline,', 'per tutti.'], size=52)]),
        P.fila_foto(K, t, [(SPOSI, 'Due sposi con le stelline accese'), (SPOSI_BACIO, 'Il bacio degli sposi fra le stelline'),
                           (SPOSI_CORTEO, 'Gli sposi fra due file di stelline'), (STELLINA, 'Una stellina accesa nel buio')],
                    ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_FOLLA, 'La piazza', 'Tutti <em>col naso all’insù.</em>',
                          'Da dove guarderete lo decidiamo prima: lo spettacolo si disegna per la piazza, non il contrario.',
                          'Il preventivo', PREVENTIVO + '#scrivi', video=True))
    return out


def build_sicurezza():
    t = Tpl(ID, 'sicurezza')
    out = [K.intro(t, 'La sicurezza', ['Il cielo pieno,', 'la piazza tranquilla.'],
                   'Permessi, distanze, la squadra che resta fino all’ultimo controllo: la parte che non si vede.',
                   bg=K.photo_bg(FONTANE_BIANCHE, overlay=48), size=80)]
    out.append(t.section([
        t.one([K.sechead(t, 'Dal sopralluogo alla pulizia', ['Cinque passi,', 'sempre gli stessi.'], size=52)]),
        t.one([P.passi(K, t, [('1', 'Il sopralluogo', 'Misuriamo le distanze dalle case, dagli alberi, dal pubblico.'),
                              ('2', 'I permessi', 'La licenza di Prefettura e le carte per il Comune: le prepariamo noi.'),
                              ('3', 'Il montaggio', 'Rampe e mortai a terra, la zona chiusa, l’estintore a ogni postazione.'),
                              ('4', 'Lo spettacolo', 'Acceso a distanza dal computer, con la squadra in contatto radio.'),
                              ('5', 'Il controllo', 'Il giro dei mortai e la pulizia: andiamo via per ultimi.')], colonne=5,
                       titolo=22)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([X.certificati(K, t, [('Licenza di Prefettura', 'Fabbricazione e accensione', 'artt. 46-47 TULPS', 'shield-check'),
                                                      ('Fochino abilitato', 'Patentino per l’accensione', 'ogni squadra', 'badge-check'),
                                                      ('Assicurazione', 'Responsabilità civile', 'fino a 5 milioni', 'umbrella'),
                                                      ('CE', 'Prodotti marcati', 'categorie F2 — F4', 'package-check')])])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(K.domande(t, 'Prima della sera,', 'le domande di tutti.', [
        ('Serve il permesso del Comune?', 'Sì, e per gli spettacoli professionali anche la licenza: le carte le prepariamo e le '
         'presentiamo noi, con la mappa delle distanze.'),
        ('E se piove o c’è vento?', 'Decidiamo insieme il pomeriggio: con il vento forte si rimanda, senza costi.'),
        ('I fuochi silenziosi esistono?', 'Sì: colori e scintille senza i colpi, per gli animali, gli ospedali e i vicini.'),
    ], 'gi-faq'))
    return out


def build_preventivo():
    t = Tpl(ID, 'preventivo')
    out = [K.intro(t, 'Il preventivo', ['Componi la sera,', 'il resto lo facciamo noi.'],
                   'Durata, colori e aggiunte per farti un’idea del prezzo; il prezzo esatto arriva dopo il sopralluogo.',
                   bg=K.video_bg(V_SCINTILLE, overlay=30), size=80)]
    conf = X.preventivo(K, t, [
        ('durata', 'La durata', 'uno', True, 'Uno spettacolo di dieci minuti è già lunghissimo.'),
        ('aggiunte', 'Le aggiunte', 'piu', False, '')], [
        ('cinque', 'durata', 'Cinque minuti', 'Il saluto in grande.', 900, '', False),
        ('dieci', 'durata', 'Dieci minuti', 'Con il finale lungo.', 1600, 'il più scelto', True),
        ('quindici', 'durata', 'Quindici minuti', 'Per le feste di paese.', 2300, '', False),
        ('musica', 'aggiunte', 'Musica sincronizzata', 'Ogni colpo sulla nota.', 400, '', False),
        ('fontane', 'aggiunte', 'Fontane fredde', 'Per il primo ballo, al chiuso.', 250, '', False),
        ('silenzioso', 'aggiunte', 'Fuoco silenzioso', 'Colori senza i colpi.', 0, '', False)],
        'La tua sera', PREVENTIVO + '?sera={configurazione}#scrivi', 'IVA compresa · permessi compresi', testo_pulsante='Chiedi il sopralluogo')
    out.append(X.sezione_preventivo(K, t, 'La sera', ['Componi,', 'il totale si fa da sé.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('date', 'La data', 'data', True), K.campo('text', 'Il posto', 'posto', True),
             K.campo('text', 'La sera', 'sera', larghezza='1-1'),
             K.campo('textarea', 'Raccontaci la festa', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Il sopralluogo', ['Veniamo', 'a vedere il cielo.'],
                                'Ti richiamiamo entro due giorni per fissare il sopralluogo, gratis.', VOCI, campi,
                                'Girandola — preventivo', 'Chiedi il sopralluogo', 'Grazie! Ti richiamiamo entro due giorni.'))
    out.append(X.zone(K, t, 'Dove lavoriamo', ['Napoli,', 'la costiera e le isole.'],
                      'Da Ischia a Positano, da Caserta al Cilento: il laboratorio è a Napoli, la squadra arriva ovunque.',
                      [('Costiera', 'Sorrento, Amalfi, Positano'), ('Isole', 'Ischia, Procida, Capri'),
                       ('Entroterra', 'Caserta, Avellino, Salerno'), ('Telefono', PHONE)], LAT, LNG, 'Girandola · il laboratorio', True,
                      HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · miccia bagnata', color=C['acc'], align='center'),
                       scoppia(K.headline(t, 'Questa pagina <em>non è partita.</em>', size=84, tag='h1', align='center',
                                          pad_bottom=20), da=0.7, a=1.0),
                       K.text(t, 'Capita: la miccia era bagnata. Ne accendiamo un’altra in home.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SCOPPIO, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, CATEGORIA, 'La parola che parte',
    ['eventi', 'fuochi d’artificio', 'matrimoni', 'feste', 'napoli', 'scuro', 'slider pro', 'scorrimento'],
    'Una famiglia di fuochisti a Napoli dal 1958: nell’apertura le lettere del nome aspettano a terra come razzi, poi '
    'scorrendo partono una dopo l’altra e scoppiano in cielo finché la parola sta lassù. Poi gli spettacoli da girare, i '
    'colori del laboratorio che si aprono mentre scorri, la sicurezza coi permessi e il preventivo da comporre.',
    [('Gli spettacoli', SPETTACOLI), ('La sicurezza', SICUREZZA), ('Preventivo', PREVENTIVO)],
    [('home', 'Home', None), ('spettacoli', 'Gli spettacoli', 'spettacoli'), ('sicurezza', 'La sicurezza', 'sicurezza'),
     ('preventivo', 'Preventivo', 'preventivo')],
    HEX, '\'Readex Pro\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Epilogue\', system-ui, sans-serif',
    'ui-monospace, Menlo, monospace', ['Readex Pro', 'Epilogue'], corsivo_di=[],
    peso_titoli='700', raggio=18, grana=4, titoli_line='1.02', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'miccia'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['secondary']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'senza'}


def _logo_v2(nodi):
    """Il logo rifatto in Epilogue (18 nov 2026) ha lo stesso nome di quello in Gluten: ?v=2 perché i browser, che lo
    tengono un mese, scarichino quello nuovo."""
    return json.loads(json.dumps(nodi, ensure_ascii=False).replace('girandola-logo-chiaro.png"', 'girandola-logo-chiaro.png?v=2"'))


def main():
    write_theme(THEME, {'header.json': _logo_v2(build_header()), 'footer.json': _logo_v2(build_footer()), 'home.json': build_home(),
                        'spettacoli.json': build_spettacoli(), 'sicurezza.json': build_sicurezza(),
                        'preventivo.json': build_preventivo(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
