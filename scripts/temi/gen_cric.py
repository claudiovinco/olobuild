# -*- coding: utf-8 -*-
"""Genera il tema «Cric» (id cric): un'officina meccanica di Torino — tagliandi, freni, gomme, diagnosi elettronica,
climatizzatori; l'auto di cortesia, il preventivo prima di mettere mano.

La trovata: «la spia». L'apertura è il posto di guida, di notte: la strada scorre davanti e sotto c'è il cruscotto, coi
due quadranti e quattro spie accese che lampeggiano (olio, freni, batteria, motore). Toccandone una (o col tasto Invio)
la spia si spegne col segno verde e il display al centro dice cos'è, quanto costa e quanto ci vuole. Lo stato «spia»
dello Slider Pro sceglie che cosa mostra il display.

Tema scuro: asfalto, l'ambra delle spie, il verde acqua del cruscotto. Titoli in Space Grotesk, testo in Onest, dati in
Martian Mono. Cursore: bullone (si avvita sui link).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'cric'
NAME = 'Cric'

HEX = {
    'primary': '#FFB020', 'primary_contrast': '#111317', 'secondary': '#2EC4B6', 'secondary_contrast': '#06161A',
    'accent': '#FF6B4A', 'dark': '#07090C', 'light': '#FFFFFF', 'muted': '#1A1E24', 'muted_contrast': '#E9EDF1',
    'text': '#E9EDF1', 'text_muted': '#9DA8B4', 'background': '#111317', 'border': '#2A3039', 'link': '#FFB020',
}
HOME, SERVIZI, OFFICINA, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:officina}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'officina@cric.example', '011 000 4040', 'tel:+390110004040'
LAT, LNG = '45.0522', '7.6664'

V_NOTTE, V_PONTE, V_FRENI, V_RUOTA, V_MOTORE, V_CONTAGIRI, V_SOTTO = (
    'pexels-video-15270404', 'pexels-video-8987272', 'pexels-video-6870344', 'pexels-video-6870347', 'pexels-video-14514790',
    'pexels-video-9174928', 'pexels-video-18101989')
VIDEOS = [V_NOTTE, V_PONTE, V_FRENI, V_RUOTA, V_MOTORE, V_CONTAGIRI, V_SOTTO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(FRANCO, SARA, DAVIDE, TUTA_BLU, BARBA, PONTE_BIANCO, OFFICINA_LUCE, SUV_PONTE, MAGGIOLINO, AUTO_IMPILATE, MERCEDES, DISCO,
 DISCO_MANO, PINZA, MOTORE, MOTORE_COLORI, SARA_OFFICINA) = f(7807035, 7006697, 6870324, 8985967, 4489761, 4116170, 8985517,
                                                              8986138, 27256501, 17552590, 29198149, 34277926, 30470930, 3642618,
                                                              11455717, 65623, 7006675)
PHOTOS = [FRANCO, SARA, DAVIDE, TUTA_BLU, BARBA, PONTE_BIANCO, OFFICINA_LUCE, SUV_PONTE, MAGGIOLINO, AUTO_IMPILATE, MERCEDES, DISCO,
          DISCO_MANO, PINZA, MOTORE, MOTORE_COLORI, SARA_OFFICINA]
SPIE = [('olio', 'Olio motore', 'La spia dell’olio', 'Olio basso o pressione scarsa: fermatevi appena potete. Controllo, '
         'rabbocco o tagliando.', 'da 39 € · 20 minuti'),
        ('freni', 'Impianto frenante', 'La spia dei freni', 'Pastiglie consumate o liquido basso. Controllo gratis, pastiglie '
         'nuove in mattinata.', 'da 89 € · mezza giornata'),
        ('batteria', 'Batteria e ricarica', 'La spia della batteria', 'L’alternatore non carica o la batteria è stanca: la '
         'proviamo col tester.', 'da 110 € · un’ora'),
        ('motore', 'Gestione motore', 'La spia del motore', 'La centralina ha trovato un guasto: lo leggiamo con la diagnosi, '
         'e vi diciamo cos’è prima di toccare.', 'diagnosi 45 € · 30 minuti')]
DISEGNI = ['cric-cruscotto-2.svg'] + ['cric-spia-%s.svg' % s[0] for s in SPIE] + ['cric-spia-%s-ok.svg' % s[0] for s in SPIE]
LOGHI = ['cric-logo-chiaro.png', 'cric-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='600', corsivo=False, maiuscole_pulsanti=True)
C = K.c
FONT_T = 'Space Grotesk'
MONO = 'Martian Mono'
VOCI = [('map-pin', 'Corso Francia 212 · Torino', PRENOTA + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 170, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '579/118', 'Cric: officina meccanica a Torino dal 1979. Tagliandi, freni, gomme, '
                   'diagnosi e climatizzatori, per tutte le marche. Il prezzo prima di mettere mano.',
                   [('Servizi', SERVIZI), ('L’officina', OFFICINA), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'L’officina', 'Lunedì — venerdì 8 — 19<br>Sabato 8 — 12:30', VOCI,
                   '© 2026 Cric snc · Torino · P.IVA 00000000000', [('facebook', 'https://facebook.com'),
                                                                   ('instagram', 'https://instagram.com')], scuro=True)


# ── L'apertura: la spia ──
def apertura(t):
    m = K.mob
    tutte = ','.join(s[0] for s in SPIE)
    VUOTO = {'nome': 'spia', 'vale': '!' + tutte, 'effetto': 'fade', 'durata': 400}
    L = K.veli('cr-velo', 'alto', 26, 44, colore=C['nero'], forza=80, mobile_alto=22) + [
        K.livello('cr-cruscotto', tipo='image', imageSrc=K.img(DISEGNI[0]), imageAlt='Il cruscotto di un’auto, di notte, coi due quadranti',
                  objectFit='contain', x=0, y=34, w=100, h=77.8, entra='fadeInUp', ritardo=200, durata=900,
                  responsive={'mobile': {'x': -50, 'y': 40, 'width': 200, 'height': 40}}),
        # sul telefono sotto il cruscotto un fondo scuro per il testo della spia (sul computer è fuori tela)
        K.livello('cr-fondo-tel', tipo='shape', bgColor='#07090C', x=-200, y=79, w=100, h=21, entra='fadeIn', ritardo=0, durata=300,
                  responsive={'mobile': {'x': 0, 'y': 79, 'width': 100, 'height': 21}}),
        K.livello('cr-o', contenuto='Cric · officina meccanica · Torino', x=6, y=13, w=50, size=13, peso='500', colore=mix(C['chiaro'], 82),
                  letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 9, 'width': 88}}),
        K.livello('cr-t', contenuto='Si è accesa una spia?', tag='h1', x=6, y=16.5, w=62, size=70, peso='600', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=900,
                  responsive={'mobile': {'fontSize': m(40), 'x': 6, 'y': 11.5, 'width': 88}}),
        K.livello('cr-s', contenuto='Toccatela: vi diciamo cos’è, quanto costa e quanto ci vuole.', x=6, y=26.5, w=44, size=18, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.5, ritardo=600,
                  responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 23.5, 'width': 86}}),
        K.livello('cr-b1', tipo='button', contenuto='Prenota', x=72, y=16, w='auto', size=13, peso='700', colore=C['on_acc'], bgColor=C['acc'],
                  padding=18, borderRadius=6, letterSpacing=2, textTransform='uppercase', buttonUrl=PRENOTA, entra='fadeIn', ritardo=800,
                  hover={'bgColor': '#FFFFFF', 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 31, 'fontSize': m(12)}}),
        K.livello('cr-b2', tipo='button', contenuto='I servizi', x=82, y=16, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=6, borderWidth=1, borderColor=mix(C['chiaro'], 55), letterSpacing=2,
                  textTransform='uppercase', buttonUrl=SERVIZI, entra='fadeIn', ritardo=900,
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250}, responsive={'mobile': {'x': 44, 'y': 31, 'fontSize': m(12)}}),
        # il display prima della scelta
        K.livello('cr-d0a', contenuto='Nessuna spia scelta', x=39.5, y=59, w=21, size=12, peso='500', colore=C['sec'], letterSpacing=2,
                  textTransform='uppercase', fontFamily=MONO, ritardo=900, stato=VUOTO,
                  responsive={'mobile': {'fontSize': m(10), 'x': 28, 'y': 53, 'width': 44}}),
        K.livello('cr-d0b', contenuto='Toccatene una.', x=39.5, y=62.5, w=21, size=26, peso='600', colore=C['chiaro'], fontFamily=FONT_T,
                  ritardo=1000, stato=VUOTO, responsive={'mobile': {'fontSize': m(18), 'x': 28, 'y': 55.5, 'width': 44}}),
        K.livello('cr-d0c', contenuto='Quattro spie accese qui sopra: il guasto più comune per ognuna, con il prezzo.', x=39.5, y=68.5, w=21,
                  size=14, peso='400', colore=mix(C['chiaro'], 80), lineHeight=1.45, ritardo=1100, stato=VUOTO,
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 83, 'width': 88}}),
    ]
    for k, (nome, sistema, titolo, testo, prezzo) in enumerate(SPIE):
        ACCESA = {'nome': 'spia', 'vale': '!' + nome, 'effetto': 'nessuno'}
        SCELTA = lambda rit=0: {'nome': 'spia', 'vale': nome, 'effetto': 'su', 'durata': 450, 'ritardo': rit}
        geo = dict(x=36.4 + k * 7.2, y=46, w=5.6, h=8.0)
        geo_m = {'x': 26.25 + k * 12.5, 'y': 44.5, 'width': 10, 'height': 3.66}
        L += [
            K.livello('cr-sp-' + nome, tipo='image', imageSrc=K.img('cric-spia-%s.svg' % nome), imageAlt=titolo, objectFit='contain',
                      entra='fadeIn', ritardo=1200 + k * 120, durata=400, stato=ACCESA,
                      action={'type': 'setState', 'target': 'spia', 'value': nome, 'label': titolo + ': cos’è'},
                      customCSS='--mps-attivo:#FFB020;', hover={'scale': 1.12, 'duration': 180},
                      responsive={'mobile': geo_m}, **geo),
            K.livello('cr-ok-' + nome, tipo='image', imageSrc=K.img('cric-spia-%s-ok.svg' % nome), imageAlt=titolo + ', sistemata',
                      objectFit='contain', entra='fadeIn', ritardo=0, durata=300, stato={'nome': 'spia', 'vale': nome, 'effetto': 'nessuno'},
                      responsive={'mobile': geo_m}, **geo),
            K.livello('cr-n-' + nome, contenuto=sistema, x=39.5, y=59, w=21, size=12, peso='500', colore=C['acc'], letterSpacing=2,
                      textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=0, stato=SCELTA(0),
                      responsive={'mobile': {'fontSize': m(10), 'x': 28, 'y': 53, 'width': 44}}),
            K.livello('cr-ti-' + nome, contenuto=titolo, x=39.5, y=62.5, w=21, size=24, peso='600', colore=C['chiaro'], fontFamily=FONT_T,
                      lineHeight=1.1, entra='fadeIn', ritardo=0, stato=SCELTA(80),
                      responsive={'mobile': {'fontSize': m(17), 'x': 28, 'y': 55.5, 'width': 44}}),
            K.livello('cr-tx-' + nome, contenuto=testo, x=39.5, y=67.5, w=21, size=14, peso='400', colore=mix(C['chiaro'], 82),
                      lineHeight=1.45, entra='fadeIn', ritardo=0, stato=SCELTA(160),
                      responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 82, 'width': 88}}),
            K.livello('cr-pr-' + nome, contenuto=prezzo, x=39.5, y=78.5, w=21, size=14, peso='600', colore=C['acc'], fontFamily=MONO,
                      entra='fadeIn', ritardo=0, stato=SCELTA(240),
                      responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 93.5, 'width': 88}}),
        ]
    sl = [K.diapositiva('cr-1', K.sfondo_video(V_NOTTE, overlay=0.15, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, stati='spia=',
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Cric · Torino, dal 1979', ['Il prezzo', 'prima di mettere mano.'], size=72, layout='stack',
                                size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo un’officina di quartiere con otto ponti e la diagnosi di tutte le marche. Prima di '
                                'smontare qualcosa vi chiamiamo e vi diciamo quanto costa: se non vi va bene, rimontiamo e '
                                'pagate solo il controllo.', size=17),
                      K.button(t, 'Prenota', PRENOTA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('phone', 'Vi chiamiamo <b>prima di smontare</b>'), ('car', 'Auto di cortesia <b>gratis</b>'),
                                ('shield-check', 'Tagliando <b>che non toglie la garanzia</b>'), ('gauge', 'Diagnosi <b>tutte le marche</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Dal tagliando', 'alla centralina.'], size=60)]),
        t.one([P.elenco_hover(K, t, [
            ('Tagliando', 'tutte le marche', 'Coi ricambi originali o equivalenti: la garanzia della casa resta.', MOTORE),
            ('Freni', 'dischi e pastiglie', 'Controllo gratis, pastiglie in mattinata, i dischi vecchi ve li facciamo vedere.', DISCO),
            ('Gomme', 'estive e invernali', 'Cambio stagionale, deposito delle gomme, convergenza.', SUV_PONTE),
            ('Diagnosi', 'la spia del motore', 'Leggiamo la centralina e vi diciamo il guasto prima di toccare.', MOTORE_COLORI),
            ('Clima', 'ricarica e igienizzazione', 'Gas, filtro abitacolo e il controllo delle perdite.', OFFICINA_LUCE),
        ], SERVIZI, size=46)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_PONTE, 'Sul ponte', 'Vi facciamo vedere <em>cosa si è rotto.</em>',
                             'Se volete, venite sotto la macchina con noi: il pezzo vecchio ve lo diamo in mano.', overlay=22))
    out.append(t.section([
        t.one([K.sechead(t, 'Le auto di quest’anno', ['Otto ponti,', 'ogni marca.'], size=56)]),
        t.one([K.coverflow(t, [(MAGGIOLINO, 'Un Maggiolino blu sul ponte', 'Il Maggiolino del 1971', 'restauro della meccanica'),
                               (MERCEDES, 'Una Mercedes d’epoca sul ponte', 'La berlina del 1984', 'freni e sospensioni'),
                               (PONTE_BIANCO, 'Un’auto bianca sollevata', 'La familiare', 'tagliando dei 120.000'),
                               (AUTO_IMPILATE, 'Tre auto sui ponti', 'Il sabato mattina', 'tre tagliandi'),
                               (SUV_PONTE, 'Un fuoristrada sul ponte', 'Il fuoristrada', 'gomme e convergenza')], altezza=420, larghezza=360)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('Mi hanno chiamato prima di cambiare i dischi: «si possono ancora usare, li cambiamo al prossimo giro». Raro.', 'Elisa', 'Torino, Pozzo Strada'),
        ('Spia del motore alla vigilia delle ferie. Diagnosi in mezz’ora, sensore cambiato, partiti alle due.', 'Matteo', 'Collegno'),
        ('Il Maggiolino di mio padre: lo trattano come se fosse loro.', 'Roberto', 'Rivoli')],
        'Chi è passato', ['Tre auto,', 'tre spie spente.']))
    return out


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Tutte le marche,', 'un solo prezzo detto prima.'],
                   'Tagliandi, freni, gomme, diagnosi, climatizzatori: i prezzi più chiesti, e cosa comprendono.',
                   bg=K.video_bg(V_FRENI, overlay=30), size=88)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Tagliando', 'Il tagliando', 'che tiene la garanzia', 'Olio, filtri, controlli e il libretto timbrato: per la casa vale come il '
         'loro.', MOTORE),
        ('Freni', 'I freni', 'in mattinata', 'Pastiglie e dischi originali o equivalenti; il controllo è sempre gratis.', DISCO_MANO),
        ('Gomme', 'Le gomme', 'e il deposito', 'Cambio stagionale, equilibratura, convergenza e il deposito al coperto.', SUV_PONTE),
        ('Diagnosi', 'La centralina', 'letta in mezz’ora', 'Tutte le marche, e le auto ibride.', MOTORE_COLORI)], altezza=420)])],
        padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['I prezzi', 'più chiesti.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'IVA compresa, per un’auto media. Per la vostra vi diciamo il prezzo esatto prima di iniziare.')],
                     wm='1-3', ws='1-1'),
               t.col([P.listino(K, t, [
                   ('Tagliando base', 'Olio, filtro olio e aria, controlli', 'da 149 €', '', ''),
                   ('Pastiglie anteriori', 'Ricambio e manodopera', 'da 89 €', '', ''),
                   ('Cambio gomme', 'Smontaggio, montaggio, equilibratura', '45 €', '', 'il più chiesto'),
                   ('Diagnosi elettronica', 'Lettura e spiegazione del guasto', '45 €', '', ''),
                   ('Ricarica clima', 'Gas, controllo perdite, filtro', '79 €', '', '')], immagini=False)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di venire,', 'le domande di tutti.', [
        ('Il tagliando da voi toglie la garanzia della casa?', 'No: con i ricambi originali o equivalenti e il libretto timbrato la '
         'garanzia resta, lo dice la legge europea.'),
        ('Avete l’auto di cortesia?', 'Sì, tre utilitarie, gratis per i lavori di più di un giorno.'),
        ('Posso aspettare in officina?', 'Per i cambi gomme e i controlli sì: c’è il caffè e il wi-fi.'),
    ], 'cr-faq'))
    return out


def build_officina():
    t = Tpl(ID, 'officina')
    out = [K.intro(t, 'L’officina', ['Otto ponti', 'in corso Francia.'],
                   'Dal 1979: il nonno aggiustava le Cinquecento, adesso leggiamo centraline. Ma il caffè è lo stesso.',
                   bg=K.video_bg(V_SOTTO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Chi mette le mani', ['Nell’auto', 'vostra.'], size=56)]),
        K.squadra(t, [(FRANCO, 'Franco Rinaudo', 'Titolare', 'Quarant’anni di officina: l’auto la sente prima di aprire il cofano.',
                       'center 25%'),
                      (SARA, 'Sara Bonino', 'Diagnosi', 'Centraline, ibride e climatizzatori: il tester è suo.', 'center 25%'),
                      (DAVIDE, 'Davide Ferrero', 'Meccanico', 'Freni, sospensioni e le auto d’epoca del sabato.', 'center 30%')],
                  ratio='4/5', size='320'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In officina', ['I pezzi,', 'da vicino.'], size=56)]),
        P.fila_foto(K, t, [(PINZA, 'La pinza dei freni'), (DISCO, 'Il disco forato'), (MOTORE, 'Il motore'),
                           (SARA_OFFICINA, 'Sara in officina')], ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_CONTAGIRI, 'Le auto d’epoca', 'Il sabato, <em>le storiche.</em>',
                          'Il sabato mattina il ponte otto è per le auto d’epoca: carburatori, puntine, freni a tamburo.',
                          'Prenota', PRENOTA + '#scrivi', video=True))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Scegliete il lavoro,', 'vi chiamiamo noi.'],
                   'Componete il lavoro per farvi un’idea del prezzo; poi vi richiamiamo per il giorno e l’ora.',
                   bg=K.video_bg(V_RUOTA, overlay=30), size=92)]
    conf = X.preventivo(K, t, [
        ('lavoro', 'Il lavoro', 'piu', True, 'Si possono sommare. Il prezzo esatto lo diciamo prima di iniziare.'),
        ('auto', 'L’auto', 'uno', True, '')], [
        ('tagliando', 'lavoro', 'Tagliando', 'Olio, filtri, controlli.', 149, 'il più chiesto', True),
        ('freni', 'lavoro', 'Pastiglie anteriori', 'Ricambio e manodopera.', 89, '', False),
        ('gomme', 'lavoro', 'Cambio gomme', 'Con l’equilibratura.', 45, '', False),
        ('diagnosi', 'lavoro', 'Diagnosi', 'Lettura della centralina.', 45, '', False),
        ('clima', 'lavoro', 'Ricarica clima', 'Gas e filtro.', 79, '', False),
        ('utilitaria', 'auto', 'Utilitaria', '', 0, '', True),
        ('media', 'auto', 'Media o familiare', '', 30, '', False),
        ('suv', 'auto', 'SUV o ibrida', '', 60, '', False)],
        'Il vostro lavoro', PRENOTA + '?lavoro={configurazione}#scrivi', 'IVA compresa · auto media', testo_pulsante='Prenota')
    out.append(X.sezione_preventivo(K, t, 'Il lavoro', ['Componete,', 'il totale si fa da sé.'], '', conf))
    out.append(t.section([t.one([X.orari(K, t, [('Lunedì — venerdì', '8 — 19', 'Accettazione fino alle 18'),
                                                ('Sabato', '8 — 12:30', 'Gomme e auto d’epoca'),
                                                ('Domenica', 'chiuso', 'Soccorso col carro attrezzi convenzionato')])])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('text', 'Auto e targa', 'auto', True), K.campo('date', 'Il giorno che preferite', 'giorno'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Cosa fa l’auto', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Vi richiamiamo', 'per l’ora.'],
                                'Entro mezza giornata, dal lunedì al sabato.', VOCI, campi, 'Cric — prenotazione', 'Prenota',
                                'Grazie! Vi richiamiamo entro mezza giornata.'))
    out.append(X.zone(K, t, 'L’officina', ['Corso Francia,', 'Pozzo Strada.'], 'A due passi dalla metro, fermata Marche.',
                      [('Metro', 'fermata Marche, 3 minuti'), ('Parcheggio', 'nel cortile'), ('Auto di cortesia', 'tre, gratis'),
                       ('Telefono', PHONE)], LAT, LNG, 'Cric · l’officina', True, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · strada chiusa', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>è rimasta a piedi.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Il navigatore si è perso. Torniamo in officina.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CONTAGIRI, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'La spia accesa',
    ['artigiani', 'meccanico', 'officina', 'auto', 'tagliando', 'scuro', 'slider pro', 'interattivo'],
    'Un’officina meccanica a Torino: l’apertura è il posto di guida di notte, la strada davanti e il cruscotto con quattro '
    'spie accese; toccandone una si spegne e il display dice cos’è, quanto costa e quanto ci vuole. Poi i servizi col listino, '
    'le auto sui ponti, la prenotazione da comporre e gli orari.',
    [('Servizi', SERVIZI), ('L’officina', OFFICINA), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('servizi', 'Servizi', 'servizi'), ('officina', 'L’officina', 'officina'), ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Onest\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Space Grotesk\', system-ui, sans-serif',
    '\'Martian Mono\', ui-monospace, Menlo, monospace', ['Onest', 'Space Grotesk', 'Martian Mono'], corsivo_di=[],
    peso_titoli='600', raggio=6, grana=4, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'bullone'
THEME['cursor']['ring_color'] = '#C3C9D1'
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'officina.json': build_officina(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
