# -*- coding: utf-8 -*-
"""Genera il tema «Spaiato» (id spaiato): un calzificio di Bologna che vende i calzini uno alla volta, mai in coppia: ogni
calzino ha un disegno suo, il compagno lo sceglie chi lo compra. E adotta i calzini rimasti soli: si manda quello
superstite, si riceve un compagno nuovo, diverso.

Tutto il sito è spaiato: le due metà dell'apertura scorrono in versi opposti, i titoli hanno due righe in due caratteri,
le carte si girano e dietro c'è il compagno che non torna. Apertura: lo slider resta fermo in cima mentre si scorre: sul
video dei calzini, due colonne di foto ai lati del titolo: quella a sinistra sale, quella a destra scende.

Tema chiaro, panna, pomodoro, verde acqua e girasole. Titoli in Bricolage Grotesque, testo in Nunito, dati in Victor Mono.
Logo: due calzini diversi incrociati e «spaiato» con due lettere di colore. Cursore: spaiato (un tondo e un quadrato che
non si somigliano e si rincorrono).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'spaiato'
NAME = 'Spaiato'

HEX = {
    'primary': '#B92E1C', 'primary_contrast': '#FFFFFF', 'secondary': '#1FA595', 'secondary_contrast': '#1E1B2E',
    'accent': '#FFC93C', 'dark': '#1E1B2E', 'light': '#FFFFFF', 'muted': '#FFEBD2', 'muted_contrast': '#1E1B2E',
    'text': '#1E1B2E', 'text_muted': '#5C5670', 'background': '#FFF7EC', 'border': '#F0D9BC', 'link': '#B92E1C',
}
VERDE = '#147A6E'

HOME, CALZINI, ADOZIONI, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:calzini}}', '{{PAGINA:adozioni}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@spaiato.example', '+39 051 000 2244', 'tel:+390510002244'
LAT, LNG = '44.4990', '11.3470'

V_LETTO, V_ROSA, V_AMICHE, V_PUNTE, V_DIVANO, V_CAMERA, V_MACCHINA, V_ROCCHETTO, V_BUIO = (
    'pexels-video-9590626', 'pexels-video-8777920', 'pexels-video-8921947', 'pexels-video-6940598', 'pexels-video-6381280',
    'pexels-video-32225366', 'pexels-video-35469650', 'pexels-video-35469631', 'pexels-video-6170032')
VIDEOS = [V_LETTO, V_ROSA, V_AMICHE, V_PUNTE, V_DIVANO, V_CAMERA, V_MACCHINA, V_ROCCHETTO, V_BUIO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
CAL = f('10563910', '4495756', '7946986', '7946993', '13335024', '9594142', '9050049')
SPA = f('4495760', '6617527', '4826711', '8413756', '6533982')
PIE = f('8790286', '6632977', '7341304', '14754671', '32195924')
CAS = f('15376336')
SNE = f('7207544', '7207545', '9521206', '5739154', '8412794')
CES = f('8580730', '5902895', '8286096')
FIL = f('30727556', '32938974', '32938975')
BAM = f('36667774', '7207558', '14930452')
RIG = f('5730457', '7186633', '9050057')
PER = f('4523755', '9086936', '7035168', '16191659', '5227559', '9086957')
LOGO_S = 'logo-spaiato-scuro.png'
PHOTOS = CAL + SPA + PIE + CAS + SNE + CES + FIL + BAM + RIG + PER
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, 'logo-spaiato-chiaro.png'}

K = Kit(ID, MEDIA, raggio=22, peso_titoli='800', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#FFC93C')
C = K.c
MONO = 'Victor Mono'
VOCI = [('map-pin', 'Via del Pratello 12 · Bologna', NEGOZI + '#calzificio'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def spaia(nodi):
    """Le seconde righe dei titoli di sezione nel carattere dei dati e in verde acqua: anche i titoli non sono in coppia."""
    for n in nodi:
        if n.get('type') == 'section-header':
            for k, riga in enumerate(n['settings'].get('headline_lines') or []):
                if k % 2 == 1:
                    riga['color'] = VERDE
        spaia(n.get('children') or [])
    return nodi


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Spaiato', LOGO_S, 150, overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '1005/251', 'Spaiato fa a Bologna calzini da comprare uno alla volta: ognuno col suo disegno, '
                   'il compagno lo scegliete voi. Quelli rimasti soli li adottiamo.',
                   [('I calzini', CALZINI), ('Adozioni', ADOZIONI), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'Il calzificio', 'Via del Pratello 12, Bologna<br>Martedì — sabato 10 — 19:30', VOCI,
                   '© 2026 Spaiato · Bologna · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')],
                   larghezza=360, fondo=C['sala'])


# ── L'apertura: le due metà che scorrono in versi opposti ──
def apertura(t):
    m = K.mob
    livelli = []
    colonne = [('s', [CAL[0], SPA[1], RIG[0], CAL[3]], 3.5, -620), ('d', [PIE[0], CAL[1], SNE[3], SPA[4]], 77.5, 620)]
    for lato, foto, x, corsa in colonne:
        for k, ff in enumerate(foto):
            y0 = -18 + k * 42 if lato == 's' else -60 + k * 42
            livelli.append(K.livello('sp-%s%d' % (lato, k), tipo='image', x=x, y=y0, w=19, h=38, imageSrc=K.img(ff), objectFit='cover',
                                     borderRadius=22, entra='fadeIn', ritardo=200 + k * 80, durata=700,
                                     customCSS='box-shadow:0 18px 40px rgba(30,27,46,.35);',
                                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0, 'r': -2 if lato == 's' else 3},
                                                                                    {'p': 100, 'y': corsa, 'r': 4 if lato == 's' else -3}]},
                                     responsive={'mobile': {'x': 3 if lato == 's' else 52, 'y': 48 + (k - (0 if lato == 's' else 1)) * 17,
                                                            'width': 45, 'height': 15}}))
    testi = [
        K.livello('sp-o', contenuto='Spaiato · calzini uno alla volta · Bologna', x=26, y=22, w=48, size=13, peso='600',
                  colore=mix('#FFFFFF', 92), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  textAlign='center', responsive={'mobile': {'fontSize': m(10), 'y': 7, 'x': 6, 'width': 88}}),
        K.livello('sp-t1', contenuto='Mai', tag='h1', x=26, y=28, w=48, size=150, peso='800', colore='#FFFFFF', lineHeight=0.95,
                  letterSpacing=-4, entra='bounceInDown', ritardo=350, durata=1000, textAlign='center',
                  responsive={'mobile': {'fontSize': m(70), 'y': 10, 'x': 6, 'width': 88}}),
        K.livello('sp-t2', fontFamily='Victor Mono', contenuto='in coppia.', tag='p', x=26, y=47, w=48, size=92, peso='700',
                  fontStyle='italic', colore='#FFC93C', lineHeight=1.0, entra='bounceInUp', ritardo=600, durata=1000,
                  textAlign='center', responsive={'mobile': {'fontSize': m(42), 'y': 18, 'x': 6, 'width': 88}}),
        K.livello('sp-s', contenuto='Calzini da comprare uno alla volta, ognuno col suo disegno. Il compagno lo scegliete voi: '
                                    'basta che non sia uguale.', x=30, y=62, w=40, size=17, peso='600', colore=mix('#FFFFFF', 94),
                  lineHeight=1.55, ritardo=800, textAlign='center',
                  responsive={'mobile': {'fontSize': m(13), 'y': 25, 'x': 8, 'width': 84}}),
        K.livello('sp-b', tipo='button', contenuto='Scegli il primo', x=36.5, y=77, w='auto', size=15, peso='800', colore=C['on_acc'],
                  bgColor=C['acc'], padding=17, borderRadius=999, entra='fadeInUp', ritardo=1000, buttonUrl=CALZINI,
                  hover={'bgColor': '#1E1B2E', 'duration': 250}, responsive={'mobile': {'y': 32.5, 'x': 8, 'fontSize': m(12)}}),
        K.livello('sp-l', tipo='button', contenuto='Adotta un compagno', x=51, y=77, w='auto', size=15, peso='800', colore='#1E1B2E',
                  bgColor='#FFC93C', padding=17, borderRadius=999, entra='fadeInUp', ritardo=1100, buttonUrl=ADOZIONI,
                  hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'tablet': {'x': 53}, 'mobile': {'y': 32.5, 'x': 50, 'fontSize': m(12)}}),
    ]
    veli = K.veli('sp-velo', 'basso', 40, 100, colore=C['nero'], forza=62, mobile_alto=38)
    sl = [K.diapositiva('sp-1', K.sfondo_video(V_LETTO, overlay=0.22, colore=C['nero']), veli + livelli + testi, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1500, scrollStep=False, heightMobile={'mode': 'px', 'value': 1500})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Spaiato · Bologna, via del Pratello 12', ['Un calzino', 'alla volta.'], size=72,
                                layout='stack', size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'In lavatrice se ne perde sempre uno. Noi abbiamo smesso di cercarlo: ogni calzino ha un '
                                'disegno solo suo e si compra da solo. A righe con uno a pois, rosso con uno verde, lungo con uno '
                                'corto. La coppia la fate voi, e non torna mai.', size=17),
                      K.button(t, 'I calzini', CALZINI, kind='pieno', pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le coppie che non tornano', ['Girate la carta:', 'dietro c’è il compagno.'], size=60)]),
        P.carte(K, t, [(CAL[2], 'Righe', 'Rosso e bianco, lungo', 'Il compagno: pois verde acqua, corto. Non si somigliano per '
                        'niente.'),
                       (SPA[0], 'Pois', 'Verde acqua, corto', 'Il compagno: tinta unita girasole, col tallone rosso.'),
                       (RIG[1], 'Rombi', 'Blu e senape', 'Il compagno: righe arcobaleno. Alla stessa festa.'),
                       (CAL[4], 'Girasole', 'Giallo, spugna', 'Il compagno: nero con un fulmine. Per i giorni di pioggia.')],
                altezza='400'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I calzini', ['Sei euro', 'l’uno, uno solo.'], size=60, tagline='Tre calzini a scelta: il quarto, '
                         'il più strano, lo scegliamo noi e ve lo regaliamo.')]),
        t.one([M.capi(K, t, [
            (CAL[5], 'Lunghi', 'Righe', ' sinistro', 'Cotone pettinato, righe rosse.', '6 €', 'solo'),
            (SPA[2], 'Corti', 'Pois', ' destro', 'Verde acqua, pois bianchi.', '6 €', ''),
            (RIG[2], 'Lunghi', 'Rombi', ' qualunque', 'Lana e cotone, rombi blu e senape.', '7 €', ''),
            (SNE[0], 'Fantasmini', 'Fulmine', ' sinistro', 'Da sneakers, col fulmine giallo.', '5 €', '')], CALZINI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Mai in coppia', 'Uno alla volta', 'Il destro non sa del sinistro',
                                                 'Spaiati è meglio'], fondo=VERDE, colore='#FFFFFF', size=50, verso='right')],
                                anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(K.pannello(t, CES[0], 'Il cesto dei dispersi', 'Ve n’è rimasto <em>uno solo?</em>', 'Mandatecelo: vi spediamo un '
                          'compagno nuovo, diverso apposta. Il vostro finisce nel cesto e prima o poi trova qualcuno.',
                          'Le adozioni', ADOZIONI, effetto='reveal-h'))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Piedi', 'spaiati.'], size=60)]),
        P.fila_foto(K, t, [(PIE[2], 'Due calzini diversi sul pavimento'), (SNE[1], 'Le sneakers coi calzini spaiati'),
                           (BAM[1], 'I bambini coi calzini a righe'), (PIE[4], 'I calzini sul divano')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Un calzino nuovo', 'ogni mese.'],
                         'Il disegno del mese, le adozioni riuscite, i negozi dove trovarci.', bg=solid(C['sala'])))
    return spaia(out)


def build_calzini():
    t = Tpl(ID, 'calzini')
    out = [K.intro(t, 'I calzini', ['Ventiquattro disegni,', 'nessuna coppia.'],
                   'Si comprano uno alla volta. Tre a scelta e il quarto, il più strano, ve lo regaliamo noi.',
                   bg=K.photo_bg(CAS[0]), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'I lunghi', ['Fino al polpaccio,', 'uno per gamba.'], size=60)]),
        t.one([M.capi(K, t, [
            (CAL[5], 'Lunghi', 'Righe', ' sinistro', 'Cotone pettinato, righe rosse.', '6 €', ''),
            (RIG[2], 'Lunghi', 'Rombi', ' qualunque', 'Lana e cotone, rombi blu e senape.', '7 €', ''),
            (CAL[6], 'Lunghi', 'Arcobaleno', ' destro', 'Sette righe, sette colori.', '6 €', 'nuovo'),
            (RIG[0], 'Lunghi', 'Tennis', ' sinistro', 'Bianco, due righe in cima.', '6 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'I corti', ['Sopra la scarpa,', 'sotto la caviglia.'], size=60)]),
        t.one([M.capi(K, t, [
            (SPA[2], 'Corti', 'Pois', ' destro', 'Verde acqua, pois bianchi.', '6 €', ''),
            (SNE[0], 'Fantasmini', 'Fulmine', ' sinistro', 'Da sneakers, fulmine giallo.', '5 €', ''),
            (CAL[1], 'Corti', 'Rosa', ' qualunque', 'Rosa confetto, tallone rosso.', '6 €', ''),
            (BAM[0], 'Bambini', 'Dinosauro', ' piccolo', 'Per i piedi dai 2 ai 6 anni.', '4 €', 'ultimi')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MACCHINA, 'La macchina', 'Un disegno <em>alla volta.</em>',
                             'La circolare fa un calzino ogni due minuti: cambiamo il disegno a ogni calzino.', overlay=24))
    return spaia(out)


def build_adozioni():
    t = Tpl(ID, 'adozioni')
    out = [K.intro(t, 'Le adozioni', ['Un calzino solo', 'non è un problema.'],
                   'Mandateci il calzino rimasto senza compagno: vi spediamo un compagno nuovo, scelto apposta perché non '
                   'c’entri niente. Il vostro va nel cesto, e prima o poi qualcuno lo adotta.', bg=K.video_bg(V_DIVANO), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Tre passi,', 'nessuna coppia.'], size=60)]),
        t.one([P.passi(K, t, [('01', 'Lo mandate', 'In una busta, anche bucato: lo ricuciamo noi.'),
                              ('02', 'Lo guardiamo', 'Colore, lunghezza, stagione: gli cerchiamo il compagno più diverso.'),
                              ('03', 'Arriva il compagno', 'In una settimana, con una cartolina che li presenta.')], colonne=3)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Adottati di recente', ['Coppie', 'che non tornano.'], size=60)]),
        P.fila_foto(K, t, [(SPA[3], 'Un calzino a righe col compagno a pois'), (FIL[1], 'I calzini stesi ad asciugare'),
                           (CES[2], 'Il cesto dei calzini soli')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Al calzificio', ['Chi sceglie', 'i compagni.'], size=60)]),
        K.squadra(t, [(PER[0], 'Viola Mazzanti', 'Fondatrice', 'Ha perso il primo calzino nel 1998 e ha deciso di non cercarlo più.',
                       'center 25%'),
                      (PER[1], 'Ahmed Saleh', 'Le macchine', 'Cambia il disegno della circolare a ogni calzino.', 'center 25%'),
                      (PER[3], 'Bianca Lodi', 'Le adozioni', 'Sceglie i compagni: più sono diversi, più è contenta.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('text', 'Il calzino solo (colore e lunghezza)', 'calzino', True),
             K.campo('textarea', 'Dove l’avete perso il compagno', 'storia', False, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'adotta', 'Adottate', ['Raccontateci', 'il calzino solo.'],
                                'Vi mandiamo l’indirizzo per spedirlo e, appena arriva, il suo compagno nuovo.', VOCI, campi,
                                'Spaiato — adozione', 'Invia', 'Grazie! Vi mandiamo l’indirizzo per la busta.'))
    return spaia(out)


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove sceglierli', 'uno per uno.'],
                   'Nei negozi c’è il cesto dei calzini: si prendono uno alla volta, e il quarto lo sceglie il negoziante.',
                   bg=K.photo_bg(CES[1]), size=88)]
    out.append(M.negozi(K, t, [
        ('Bologna', 'Calzificio Spaiato', 'via del Pratello 12'), ('Bologna', 'Il Cassetto', 'via Clavature 9'),
        ('Milano', 'Destro', 'via Paolo Sarpi 40'), ('Torino', 'Sinistro', 'via Sant’Agostino 22'),
        ('Firenze', 'Tallone', 'via dei Neri 18'), ('Roma', 'Punta', 'via Ostiense 72'),
        ('Napoli', 'Pois', 'via Bellini 33'), ('Barcellona', 'Desparejado', 'Carrer de Verdi 101')],
        nota='In ogni negozio c’è il cesto delle adozioni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il calzificio', ['Al Pratello,', 'col cesto in vetrina.'], size=54, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19:30'), ('Le macchine', 'si vedono lavorare il sabato'),
                                  ('Adozioni', 'si portano i calzini soli')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Spaiato · calzificio', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='calzificio', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'di chi li vuole spaiati.'], [
        ('Si possono comprare due uguali?', 'No: non ne facciamo due uguali. Al massimo due cugini.'),
        ('Che taglie ci sono?', 'Tre: 35-38, 39-42, 43-46. E i bambini dai 2 ai 6 anni.'),
        ('Il quarto calzino lo scelgo io?', 'No: lo sceglie il negoziante, ed è il più strano che ha.'),
        ('Spedite?', 'Solo le adozioni: i calzini si scelgono dal cesto, nei negozi.'),
    ], 'sp-faq', bg=False))
    return spaia(out)


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · calzino perso', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è rimasta sola.</em>', size=80, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'L’abbiamo cercata in lavatrice, dietro al termosifone, sotto il letto. Niente.', size=17,
                              align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BUIO, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Le metà che scorrono al contrario',
    ['moda', 'calzini', 'abbigliamento', 'marchio', 'bologna', 'chiaro', 'colorato', 'slider pro'],
    'Un calzificio che vende i calzini uno alla volta, mai in coppia, e un sito spaiato: in apertura due colonne di foto '
    'scorrono in versi opposti ai lati del titolo, le carte si girano e dietro c’è il compagno che non torna, i calzini '
    'rimasti soli si adottano. Il cursore sono due compagni che non si somigliano.',
    [('I calzini', CALZINI), ('Adozioni', ADOZIONI), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('calzini', 'I calzini', 'calzini'), ('adozioni', 'Adozioni', 'adozioni'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Nunito\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bricolage Grotesque\', system-ui, sans-serif',
    '\'Victor Mono\', ui-monospace, Menlo, monospace', ['Nunito', 'Bricolage Grotesque', 'Victor Mono'], corsivo_di=['Victor Mono'],
    peso_titoli='800', raggio=22, grana=0, titoli_line='1.0', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'spaiato'
THEME['cursor']['ring_color'] = HEX['secondary']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'calzini.json': build_calzini(), 'adozioni.json': build_adozioni(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
