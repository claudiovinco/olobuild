# -*- coding: utf-8 -*-
"""Genera il tema «Focolare» (id focolare): una bottega di stufe in maiolica e camini a legna a Cles, in Val di Non,
dal 1958. Le formelle si cuociono nel forno di bottega, le stufe ad accumulo si montano in casa in una settimana.
Scuro come una stube la sera, con la luce del fuoco.

Nero di fuliggine, l'arancio delle braci, il verde della maiolica trentina e il crema della calce. Titoli in Instrument
Serif, testo in Albert Sans, numeri in Sometype Mono.

Apertura: Slider Pro coi video veri (il fuoco dietro il vetro, le braci, il camino di mattoni) e la maiolica dipinta,
in dissolvenza lunga; sopra, fisse, piccole scintille che galleggiano e salgono (livelli globali con «fluttua» e
«deriva»), e la temperatura di fuori e di dentro. Poi le braci che salgono dietro il testo (Particle FX «embers»), la
legna che serve in un inverno a confronto (Chart a barre), la stufa da comporre col prezzo (Configuratore) e i modelli.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'focolare'
NAME = 'Focolare'

HEX = {
    'primary': '#FF8A3D', 'primary_contrast': '#0C0B0A', 'secondary': '#2F5D4F', 'secondary_contrast': '#F6F0E6',
    'accent': '#EADBC8', 'dark': '#060505', 'light': '#F6F0E6', 'muted': '#171412', 'muted_contrast': '#F0E8E0',
    'text': '#F0E8E0', 'text_muted': '#A69C93', 'background': '#0C0B0A', 'border': '#2B2622', 'link': '#FF8A3D',
}

HOME, STUFE, BOTTEGA, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:le-stufe}}', '{{PAGINA:la-bottega}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'fuoco@focolare.example', '+39 0463 000 958', 'tel:+390463000958'
LAT, LNG = '46.3644', '11.0337'

V_CAMINO, V_SCINTILLE, V_VETRO, V_SPORTELLO, V_MANI, V_BRACI = (
    'pexels-video-13081949', 'pexels-video-29936087', 'pexels-video-10482174', 'pexels-video-35481118',
    'pexels-video-8967847', 'pexels-video-9718394')
PHOTOS = [
    'pexels-39144595.jpg', 'pexels-39144594.jpg', 'pexels-4573121.jpg', 'pexels-20235001.jpg', 'pexels-5562761.jpg',
    'pexels-31092909.jpg', 'pexels-12932907.jpg', 'pexels-12932972.jpg', 'pexels-10861549.jpg', 'pexels-6832351.jpg',
    'pexels-35146882.jpg', 'pexels-9056661.jpg', 'pexels-9056659.jpg', 'pexels-9307960.jpg', 'pexels-6832199.jpg',
    'pexels-6394521.jpg', 'pexels-635926.jpg', 'pexels-22820417.jpg', 'pexels-9293750.jpg', 'pexels-10169335.jpg',
    'pexels-37803747.jpg', 'pexels-8730747.jpg', 'pexels-32840740.jpg', 'pexels-36357947.jpg', 'pexels-37252661.jpg',
    'pexels-5803417.jpg', 'pexels-5803416.jpg', 'pexels-11261548.jpg', 'pexels-8433203.jpg', 'pexels-11786084.jpg',
    'pexels-28995158.jpg', 'pexels-34823756.jpg', 'pexels-6623915.jpg', 'pexels-6530539.jpg', 'pexels-5406478.jpg',
    'pexels-12917473.jpg', 'pexels-20346807.jpg', 'pexels-10636452.jpg', 'pexels-18717806.jpg',
]
VIDEOS = [V_CAMINO, V_SCINTILLE, V_VETRO, V_SPORTELLO, V_MANI, V_BRACI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=4, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Via Doss di Pez 7 · Cles, Val di Non', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Focolare', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Focolare', 'Stufe in maiolica e camini a legna fatti a mano a Cles, in Val di Non, dal 1958. '
                   'Progettiamo, cuociamo le formelle, montiamo in casa e torniamo ogni autunno a controllare.',
                   [('Le stufe', STUFE), ('La bottega', BOTTEGA), ('Contatti', CONTATTI), ('Dove siamo', CONTATTI + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'L’autunno', 'A settembre e ottobre passiamo da tutte le stufe che abbiamo montato: pulizia della canna, '
                   'controllo delle guarnizioni, un caffè in cucina.', VOCI,
                   '© 2026 Focolare di Pancheri & figli snc · Cles · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=150)


def apertura(t):
    m = K.mob
    scene = [
        ('La stube · dal 1958', 'Il caldo\nche resta.', 'Stufe in maiolica e camini a legna fatti a mano in Val di Non. Una '
         'carica la sera, il caldo fino al mattino.', K.sfondo_video(V_VETRO, overlay=0.45)),
        ('La maiolica · dipinta a mano', 'Ogni formella\nè diversa.', 'Le cuociamo nel forno della bottega, smaltate in '
         'quaranta colori. Accumulano il calore e lo rendono piano, per ore.',
         K.sfondo_foto('pexels-11786084.jpg', overlay=0.6, scala=1.1)),
        ('Le braci · dodici ore', 'Due ciocchi,\nuna notte.', 'Una stufa ad accumulo brucia forte un’ora e scalda per '
         'dodici. Consuma un terzo della legna di un camino aperto.', K.sfondo_video(V_BRACI, overlay=0.4)),
        ('Il camino · su misura', 'Il fuoco\nda guardare.', 'Camini aperti e chiusi, in pietra, ferro e calce, disegnati '
         'per la vostra stanza e la vostra canna fumaria.', K.sfondo_video(V_CAMINO, overlay=0.5)),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo) in enumerate(scene):
        n = i + 1
        sl.append(K.diapositiva('fl-%d' % n, sfondo, [
            K.livello('fl-o%d' % n, contenuto=occ, x=6, y=24, w=44, size=12, peso='500', colore=C['acc'], ritardo=200,
                      letterSpacing=3, textTransform='uppercase', fontFamily='Sometype Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 12, 'width': 88}}),
            K.livello('fl-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=29, w=58, size=112, peso='400',
                      colore=C['chiaro'], ritardo=350, durata=1300, lineHeight=0.98, entra='fadeIn',
                      charAnim={'stagger': 30, 'offsetY': 34, 'blur': 10, 'opacity': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 30, 'color': mix(C['nero'], 65)},
                      responsive={'mobile': {'fontSize': m(46), 'y': 16, 'width': 88}}),
            K.livello('fl-s%d' % n, contenuto=testo, x=6, y=64, w=34, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1200, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 32, 'width': 88}}),
            K.livello('fl-b%d' % n, tipo='button', contenuto='Un sopralluogo', x=6, y=80, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=4, ritardo=1500, buttonUrl=CONTATTI + '#scrivici',
                      hover={'bgColor': C['acc2'], 'duration': 250}, responsive={'mobile': {'y': 48, 'fontSize': m(12)}}),
        ], durata=7000))
    scintille = []
    for k, (x, y, sz, anim, dur) in enumerate([(58, 70, 0.5, 'float', 4200), (66, 54, 0.35, 'drift', 6800),
                                              (73, 78, 0.6, 'float', 5200), (81, 46, 0.4, 'wander', 8000),
                                              (88, 66, 0.5, 'float', 4600), (62, 38, 0.3, 'drift', 7400),
                                              (92, 30, 0.35, 'wander', 9000)]):
        scintille.append(K.livello('fl-sc%d' % k, tipo='shape', x=x, y=y, w=sz, h=sz * 1.6, bgColor=C['acc'],
                                   borderRadius=999, entra='fadeIn', ritardo=400 + 150 * k, durata=1400, opacity=75,
                                   animLoop=anim, animLoopDuration=dur, parallaxDepth=4 + k % 3,
                                   boxShadow={'x': 0, 'y': 0, 'blur': 12, 'spread': 1, 'color': mix(C['acc'], 70)},
                                   responsive={'mobile': {'visible': False}}))
    scintille.append(K.livello('fl-temp', contenuto='Fuori −8 °C · in stube 21 °C', x=60, y=13, w=36, size=12, peso='500',
                               colore=mix(C['chiaro'], 78), align='right', entra='fadeIn', ritardo=500, letterSpacing=2,
                               textTransform='uppercase', fontFamily='Sometype Mono',
                               responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=scintille, transizione='crossFade', durata_trans=1700, showArrows=True,
                       showDots=True, parallax=True, parallaxIntensity=4)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    braci = t.classic('particlefx', {
        'scope': 'section', 'preset': 'embers', 'count': 40, 'speed': 0.7, 'size': 6, 'wind': 0.3, 'gravity': 1,
        'connect_lines': False, 'interact_on_hover': True, 'palette_1': C['acc'], 'palette_2': mix(C['acc'], 60, C['chiaro']),
        'palette_3': mix(C['acc'], 50, C['acc2']), 'palette_4': '', 'palette_5': '', 'particle_opacity': 75, 'content': '', 'min_height': 560,
    }, style={})
    out.append(t.section([
        t.one([braci], anim=None),
        t.row([t.col([K.sechead(t, 'Il calore che sale', ['Il fuoco brucia un’ora,', 'la maiolica scalda tutta la notte.'],
                                size=60, layout='stack', size_mobile=38),
                      K.text(t, 'In una stufa ad accumulo il fumo gira per metri di canali di mattone refrattario prima di '
                                'uscire: il calore resta dentro la stufa, e la maiolica lo rende piano, come un sasso al sole. '
                                'Niente aria che brucia la gola, niente polvere che gira.', size=17, color=C['testo']),
                      K.button(t, 'Le stufe', STUFE, pad_top=18)], wm='3-5', ws='1-1'),
               t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 160, 'padding_bottom': 160}))
    legna = t.classic('chart', {
        'chart_type': 'bar',
        'items': [{'label': l, 'value': v, 'color': c} for l, v, c in [
            ('Camino aperto', '60', mix(C['tenue'], 70)), ('Stufa in ghisa', '35', C['sec']),
            ('Camino chiuso', '30', mix(C['acc'], 55, C['sec'])), ('Stufa in maiolica', '20', C['acc'])]],
        'chart_height': '380', 'show_legend': False, 'legend_color': C['testo'], 'show_title': False,
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Un inverno', ['Quanta legna', 'per scaldare la casa.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Quintali di faggio per una casa di 120 metri quadri in Val di Non, da ottobre ad aprile. '
                                'Con la maiolica ne bruciate un terzo: il resto lo regalate al vicino.'),
                      K.specs(t, [('Rendimento della maiolica', 'oltre l’85%'), ('Cariche al giorno', 'una o due'),
                                  ('Polveri', 'classe 5 stelle')])], wm='2-5', ws='1-1'),
               t.col([legna], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    stufa = t.classic('configuratore', {
        'gruppi': [{'id': 'tipo', 'titolo': 'La stufa', 'tipo': 'uno', 'obbligatorio': True,
                    'aiuto': 'Il prezzo comprende progetto, formelle, montaggio e il collaudo. La canna fumaria la '
                             'verifichiamo al sopralluogo.'},
                   {'id': 'rivestimento', 'titolo': 'Il rivestimento', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': [
            {'id': 'piccola', 'gruppo': 'tipo', 'nome': 'Stube piccola', 'descrizione': 'Fino a 60 m², una stanza o un '
             'bilocale.', 'prezzo': 7800, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '', 'min': 0, 'max': 1},
            {'id': 'media', 'gruppo': 'tipo', 'nome': 'Stube di casa', 'descrizione': 'Fino a 120 m², la più scelta.',
             'prezzo': 11500, 'immagine': '', 'badge': 'La più scelta', 'predefinita': True, 'punti': '', 'min': 0, 'max': 1},
            {'id': 'grande', 'gruppo': 'tipo', 'nome': 'Stufa centrale', 'descrizione': 'Fino a 200 m², su due piani.',
             'prezzo': 16900, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '', 'min': 0, 'max': 1},
            {'id': 'liscia', 'gruppo': 'rivestimento', 'nome': 'Maiolica liscia', 'descrizione': 'Smalto in tinta unita, '
             'quaranta colori.', 'prezzo': 0, 'immagine': '', 'badge': '', 'predefinita': True, 'punti': '', 'min': 0, 'max': 1},
            {'id': 'rilievo', 'gruppo': 'rivestimento', 'nome': 'Maiolica a rilievo', 'descrizione': 'Formelle decorate '
             'a stampo, come le stube dell’Ottocento.', 'prezzo': 2400, 'immagine': '', 'badge': '', 'predefinita': False,
             'punti': '', 'min': 0, 'max': 1},
            {'id': 'ollare', 'gruppo': 'rivestimento', 'nome': 'Pietra ollare', 'descrizione': 'Lastre della Valmalenco, '
             'accumulano ancora di più.', 'prezzo': 3100, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '',
             'min': 0, 'max': 1},
            {'id': 'forno', 'gruppo': 'extra', 'nome': 'Forno per il pane', 'descrizione': 'Nella stufa, per pane, '
             'arrosti e la torta della domenica.', 'prezzo': 900, 'immagine': '', 'badge': '', 'predefinita': False,
             'punti': '', 'min': 0, 'max': 1},
            {'id': 'panca', 'gruppo': 'extra', 'nome': 'Panca riscaldata', 'descrizione': 'La panca attorno alla stufa, '
             'dove si legge d’inverno.', 'prezzo': 1200, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '',
             'min': 0, 'max': 1},
            {'id': 'acqua', 'gruppo': 'extra', 'nome': 'Scambiatore per l’acqua', 'descrizione': 'Scalda anche l’acqua '
             'dei termosifoni del piano di sopra.', 'prezzo': 2600, 'immagine': '', 'badge': '', 'predefinita': False,
             'punti': '', 'min': 0, 'max': 1}],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'Indicativo', 'nota_prezzo': 'IVA al 10% per la prima casa · '
        'detrazioni fiscali da verificare', 'periodo': '', 'riepilogo': True, 'titolo_riepilogo': 'La vostra stufa',
        'testo_azzera': 'Ricomincia', 'testo_pulsante': 'Chiedi il sopralluogo',
        'link_azione': CONTATTI + '?stufa={configurazione}#scrivici', 'vista_opzioni': 'righe', 'disposizione': 'lato',
        'sfondo_scheda': C['sala'], 'colore_scelta': C['acc'], 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        'colore_prezzo': C['acc'], 'sfondo_riepilogo': C['sala'], 'sfondo_badge': C['acc2'], 'colore_badge': C['on_acc'],
        'raggio_scheda': radius(4), 'gruppo_color': C['testo'], 'gruppo_font_weight': '400', 'totale_color': C['acc'],
        'totale_font_weight': '400',
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'La vostra stufa', ['Componetela,', 'poi veniamo a vedere.'], size=60,
                         tagline='Un prezzo indicativo, montaggio compreso. Quello vero lo scriviamo dopo il sopralluogo, '
                                 'e non cambia più.')]),
        t.one([stufa]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='componi', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_MANI, 'La sera', 'Due ciocchi, e basta.', 'Si carica alle sette, si chiude lo sportello '
                          'alle otto. Poi la stufa fa da sola: al mattino la maiolica è ancora tiepida.', 'Le stufe', STUFE,
                          overlay=55, video=True))
    out.append(K.video_frase(t, V_SCINTILLE, 'Dal 1958', 'Il fuoco <em>si tramanda.</em>',
                             'Nonno Arrigo, papà Mauro, adesso Elena e Paolo. Tre generazioni, più di duemila stufe accese in '
                             'tutto il Trentino.', overlay=40,
                             extra=[K.button(t, 'La bottega', BOTTEGA, kind='pieno', align='center', pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Focolare',
                page_intro_caption='Val di Non · dal 1958', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_stufe():
    t = Tpl(ID, 'le-stufe')
    out = [K.intro(t, 'Le stufe', ['Tre fuochi,', 'una sola regola.'],
                   'Ogni stufa la disegniamo per la vostra casa: la stanza, la canna fumaria, come vivete d’inverno. La '
                   'regola è sempre la stessa: bruciare poco, scaldare a lungo.', bg=K.video_bg(V_SPORTELLO, overlay=55),
                   overlay_text=True)]
    for k, (aid, foto, nome, chi, testo, dati) in enumerate([
        ('maiolica', 'pexels-11786084.jpg', 'Stufa in maiolica', 'La stube trentina', 'Il cuore in mattone refrattario, '
         'il rivestimento in formelle smaltate cotte da noi. Scalda per irraggiamento, come il sole d’inverno.',
         [('Accumulo', 'fino a 12 ore'), ('Peso', 'da 1.200 a 3.000 chili'), ('Da', '7.800 € montata')]),
        ('ghisa', 'pexels-10861549.jpg', 'Stufa in ghisa', 'Per la cucina e la taverna', 'Si scalda in mezz’ora e cuoce '
         'sul piano: per chi vuole il fuoco subito e la pentola del minestrone sempre calda.',
         [('Accumulo', '2 — 3 ore'), ('Piano cottura', 'sì, in ghisa'), ('Da', '3.400 € montata')]),
        ('camino', 'pexels-9307960.jpg', 'Camino su misura', 'Il fuoco da guardare', 'Aperto o chiuso col vetro, in '
         'pietra di Predazzo, ferro o calce. Il camino chiuso brucia la metà della legna di quello aperto.',
         [('Rivestimento', 'pietra, ferro, calce'), ('Rendimento', 'fino all’80% chiuso'), ('Da', '6.500 € montato')]),
    ]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, nome, ratio='4/5', tilt=True)], wm='2-5', ws='1-1'),
                   t.col([K.label(t, chi, color=C['acc']), K.headline(t, nome, size=60, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati), K.button(t, 'Componi la tua', HOME + '#componi')],
                         wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 else None, style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima', 'del sopralluogo.', [
        ('Si può montare in una casa già finita?', 'Quasi sempre. Serve una canna fumaria buona e un pavimento che regga '
                                                  'il peso: lo verifichiamo al sopralluogo.'),
        ('Quanto tempo ci vuole?', 'Dal sopralluogo all’accensione, due o tre mesi. Il montaggio in casa dura una settimana.'),
        ('Ci sono detrazioni?', 'Spesso sì, con le stufe a basse emissioni. Vi diamo la documentazione per il '
                                'commercialista.'),
        ('E la manutenzione?', 'Una pulizia l’anno, che facciamo noi in autunno. Le formelle durano più di cent’anni.'),
    ], 'st', bg=False))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Il forno', 'non si spegne.'],
                   'A Cles, sotto il Doss di Pez: il laboratorio delle formelle, il forno a 1.000 gradi, il magazzino '
                   'delle argille. E la stube di prova, dove si viene a sentire il caldo prima di decidere.',
                   bg=K.photo_bg('pexels-12917473.jpg', overlay=60), overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-28995158.jpg', 'Una vecchia stufa in maiolica in una stube', ratio='4/5', drift=True)],
                     wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Tre generazioni', color=C['acc2']),
                      K.headline(t, 'Dal nonno Arrigo <em>a Elena e Paolo.</em>', size=52, tag='h2', pad_bottom=14),
                      K.text(t, 'Arrigo Pancheri montava stufe in bicicletta, con le formelle nel carretto. Mauro ha '
                                'costruito il forno nuovo. Elena disegna i decori, Paolo calcola i canali dei fumi: la '
                                'bottega è la stessa, il fuoco anche.'),
                      K.specs(t, [('Stufe montate', 'oltre 2.000'), ('Formelle cotte all’anno', '9.000'),
                                  ('Colori di smalto', '40'), ('Zona', 'Trentino, Alto Adige, Lombardia')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-5803416.jpg', 'Una catasta di legna tagliata', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-6530539.jpg', 'Mani che si scaldano davanti al fuoco', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-10636452.jpg', 'Una casa nella neve con le finestre accese', ratio='4/5')],
                     wm='1-3', ws='1-1')], layout='33-33-33', gap=16),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_BRACI, 'La stube di prova', 'Venite a sentire <em>il caldo.</em>',
                             'Il sabato mattina la stube della bottega è accesa: ci si siede sulla panca, si tocca la '
                             'maiolica, si beve un vin brulé. Poi si decide.', overlay=45))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Il sopralluogo', 'è gratuito.'],
                   'Veniamo a vedere la casa, la canna fumaria e la stanza. Poi vi mandiamo il progetto e il prezzo scritto, '
                   'che non cambia più.', bg=K.photo_bg('pexels-20346807.jpg', overlay=55), overlay_text=True)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono', True), K.campo('text', 'Comune', 'comune', True),
             K.campo('select', 'Cosa vi interessa', 'cosa', opzioni='Stufa in maiolica\nStufa in ghisa\nCamino\nManutenzione'),
             K.campo('textarea', 'La vostra stufa', 'stufa', larghezza='1-1', param='stufa',
                     segnaposto='Se l’avete composta nella home la trovate qui. Altrimenti raccontateci la casa.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Il sopralluogo', ['Veniamo', 'a vedere.'],
                                'In Trentino e Alto Adige è gratuito. Fuori regione lo scaliamo dalla stufa.', VOCI, campi,
                                'Focolare — sopralluogo', 'Chiedi il sopralluogo',
                                'Ricevuto. Vi chiamiamo entro due giorni per fissare la data.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Cles,', 'Val di Non.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via Doss di Pez 7, Cles (TN)'), ('Orari', 'lunedì — sabato, 8 — 12 e 14 — 18'),
                                  ('Da Trento', 'cinquanta minuti'), ('La stube di prova', 'accesa il sabato mattina')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Focolare · stufe e camini', hex_marker=HEX['primary'], zoom='15')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · stufa spenta', color=C['acc'], align='center'),
                       K.headline(t, 'Qui la brace <em>si è spenta.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Due ciocchi e torniamo all’inizio.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BRACI, overlay=55), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Casa e design', 'Il caldo che resta',
    ['stufe', 'maiolica', 'camini', 'trentino', 'legna', 'artigianato', 'scuro', 'slider pro'],
    'Una bottega di stufe in maiolica e camini a legna in Val di Non, nero di fuliggine e arancio di brace: l’apertura '
    'col fuoco vero e le scintille che galleggiano, le braci che salgono dietro il testo, la legna di un inverno a '
    'confronto, la stufa da comporre col prezzo e i tre fuochi della bottega.',
    [('Le stufe', STUFE), ('La bottega', BOTTEGA), ('Componi', HOME + '#componi'), ('Sopralluogo', CONTATTI)],
    [('home', 'Home', None), ('le-stufe', 'Le stufe', 'le-stufe'), ('la-bottega', 'La bottega', 'la-bottega'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Albert Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Instrument Serif\', \'Georgia\', serif', '\'Sometype Mono\', ui-monospace, Menlo, monospace',
    ['Instrument Serif', 'Albert Sans', 'Sometype Mono'], corsivo_di=['Instrument Serif'], peso_titoli='400', raggio=4,
    grana=6, titoli_line='0.98', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-stufe.json': build_stufe(), 'la-bottega.json': build_bottega(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
