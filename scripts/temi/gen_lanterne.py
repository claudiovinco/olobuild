# -*- coding: utf-8 -*-
"""Genera il tema «Lanterne» (id lanterne): un'unità di strada a Torino che di notte porta cena, tè e coperte. Scuro e caldo.

Nero di notte, l'ambra delle lanterne, il corallo delle braci e un verde salvia. Titoli nel DM Serif Display (anche in
corsivo), testo in Figtree, orari in Martian Mono.

Apertura: Slider Pro guidato dallo scorrimento (Scroll timeline): scendendo nella pagina si attraversa la notte di un
turno, dalle 18:30 in cucina al rientro dopo mezzanotte, con l'ora enorme a contorno che cambia a ogni passo e un
indice fisso che dice «continua a scorrere». Poi le colonne di foto e parole che scorrono in direzioni opposte
(Scorrimento verticale), i numeri dell'inverno, le coperte raccolte, i turni della settimana (Schedule) e la richiesta
per diventare volontari.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'lanterne'
NAME = 'Lanterne'

HEX = {
    'primary': '#F4A63A', 'primary_contrast': '#100D0A', 'secondary': '#5E7F6F', 'secondary_contrast': '#100D0A',
    'accent': '#EE7552', 'dark': '#080605', 'light': '#F8F2EA', 'muted': '#1A1511', 'muted_contrast': '#F1E9DE',
    'text': '#F1E9DE', 'text_muted': '#A99E90', 'background': '#100D0A', 'border': '#2F271F', 'link': '#F4A63A',
}

HOME, NOTTE, UNISCITI, DONA = '{{PAGINA:home}}', '{{PAGINA:la-notte}}', '{{PAGINA:unisciti}}', '{{PAGINA:dona}}'
MAIL, PHONE, PHONE_TEL = 'turni@lanterne.example', '011 000 2210', 'tel:+390110002210'
LAT, LNG = '45.0632', '7.6781'

V_LANTERNA, V_GALLEGGIANTI, V_STRADA = 'pexels-video-4180633', 'pexels-video-39181590', 'pexels-video-6470925'
V_FINESTRA, V_ZUPPA = 'pexels-video-4458918', 'pexels-video-18744488'
PHOTOS = [
    'pexels-19796702.jpg', 'pexels-14556059.jpg', 'pexels-10398361.jpg', 'pexels-36569990.jpg', 'pexels-6646987.jpg',
    'pexels-6646923.jpg', 'pexels-6646989.jpg', 'pexels-6647017.jpg', 'pexels-6994938.jpg', 'pexels-6646864.jpg',
    'pexels-6995065.jpg', 'pexels-36048313.jpg', 'pexels-35313198.jpg', 'pexels-6143814.jpg', 'pexels-30312788.jpg',
    'pexels-16695774.jpg', 'pexels-1853541.jpg', 'pexels-6647063.jpg', 'pexels-34011408.jpg', 'pexels-28872869.jpg',
    'pexels-13517612.jpg', 'pexels-38336797.jpg', 'pexels-29801148.jpg', 'pexels-23440319.jpg', 'pexels-26522388.jpg',
    'pexels-19581702.jpg', 'pexels-6132955.jpg',
]
VIDEOS = [V_LANTERNA, V_GALLEGGIANTI, V_STRADA, V_FINESTRA, V_ZUPPA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='400', corsivo=True, maiuscole_pulsanti=False)
C = K.c
VOCI = [('map-pin', 'La cucina: via Saluzzo 40, Torino', NOTTE + '#dove'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Lanterne', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Lanterne', 'Unità di strada a Torino dal 2011: tre furgoni, centoventi volontari, ogni sera '
                   'dell’anno. Una cena calda, una coperta, e il tempo di parlare.',
                   [('La notte di un turno', NOTTE), ('Diventa volontario', UNISCITI), ('Dona', DONA),
                    ('Porta una coperta', DONA + '#raccolta'), ('Privacy', '/privacy-policy/')],
                   'Stasera', 'Partiamo alle 20.30 da via Saluzzo. Servono termos e calze di lana: le porti in cucina fino '
                   'alle 19.', VOCI, '© 2026 Lanterne ODV · Torino · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=150)


def apertura(t):
    m = K.mob
    ore = [
        ('18:30', 'In cucina', 'La zuppa\nper centoventi.', 'Il primo turno taglia le verdure e riempie i termos. Il menu '
         'lo decide chi è arrivato per primo.', K.sfondo_foto('pexels-6647063.jpg', overlay=0.5)),
        ('20:30', 'Si parte', 'Tre furgoni,\nsei fermate.', 'Porta Nuova, Porta Susa, il Valentino, sotto i portici di '
         'via Nizza. Ogni furgone ha un medico o un infermiere.', K.sfondo_video(V_STRADA, overlay=0.45)),
        ('22:00', 'In strada', 'Prima del tè,\nil nome.', 'Non chiediamo niente a nessuno. Ci presentiamo, versiamo il tè, e '
         'aspettiamo. A volte parlano per un’ora.', K.sfondo_foto('pexels-35313198.jpg', overlay=0.45)),
        ('00:30', 'Il rientro', 'Si torna quando\nl’ultimo ha una coperta.', 'Si lavano i termos, si scrive il diario del '
         'turno, si dorme poco. Domani esce un’altra squadra.', K.sfondo_video(V_FINESTRA, overlay=0.4)),
    ]
    sl = []
    for i, (ora, occ, tit, testo, sfondo) in enumerate(ore):
        n = i + 1
        sl.append(K.diapositiva('la-%d' % n, sfondo, [
            K.livello('la-h%d' % n, contenuto=ora, x=52, y=14, w=46, size=180, peso='400', colore='transparent', tag='p',
                      entra='fadeIn', ritardo=0, durata=900, lineHeight=0.9, letterSpacing=-6, fontFamily='Martian Mono',
                      textStrokeWidth=1.5, textStrokeColor=C['acc'], opacity=85,
                      charAnim={'stagger': 60, 'offsetY': 40, 'opacity': 0, 'blur': 6},
                      responsive={'mobile': {'fontSize': m(84), 'x': 6, 'y': 66, 'width': 90}}),
            K.livello('la-o%d' % n, contenuto='%s · %s' % (ora, occ), x=6, y=34, w=40, size=13, peso='400', colore=C['acc'],
                      ritardo=100, letterSpacing=3, textTransform='uppercase', fontFamily='Martian Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 14, 'width': 88}}),
            K.livello('la-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=39, w=62, size=86, peso='400',
                      colore=C['chiaro'], ritardo=250, durata=900, lineHeight=1.0, letterSpacing=-1,
                      mask={'top': 0, 'right': 0, 'bottom': 100, 'left': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 20, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(40), 'y': 18, 'width': 88}}),
            K.livello('la-s%d' % n, contenuto=testo, x=6, y=68, w=34, size=18, peso='400', colore=mix(C['chiaro'], 88),
                      ritardo=650, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 38, 'width': 88}}),
        ], durata=6000))
    globali = [
        K.livello('la-g1', contenuto='La notte di un turno · continua a scorrere ↓', x=6, y=90, w=50, size=12, peso='400',
                  colore=mix(C['chiaro'], 70), entra='fadeIn', ritardo=800, letterSpacing=2, textTransform='uppercase',
                  fontFamily='Martian Mono', animLoop='float', animLoopDuration=2600,
                  responsive={'mobile': {'fontSize': m(10), 'y': 92, 'width': 88}}),
    ]
    return K.proslider(t, sl, globali=globali, transizione='crossFade', durata_trans=900, autoplay=False, showArrows=False,
                       showDots=True, dotStyle='dash', showProgressBar=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, parallax=False, height={'mode': 'vh', 'value': 100}, sizingMode='auto')


def scorrimento(t):
    voci = []
    for col, items in enumerate([
        [('immagine', 'pexels-6646987.jpg', 'Un volontario versa la zuppa'), ('testo', 'Una zuppa calda', ''),
         ('immagine', 'pexels-36048313.jpg', 'Mani attorno a una tazza calda'), ('testo', 'Un tè con lo zucchero', '')],
        [('testo', 'Un nome', ''), ('immagine', 'pexels-29801148.jpg', 'Un ragazzo sorride nel cappotto d’inverno'),
         ('testo', 'Una coperta', ''), ('immagine', 'pexels-6994938.jpg', 'Un volontario con le coperte in braccio')],
        [('immagine', 'pexels-30312788.jpg', 'Una mano regge una lanterna accesa'), ('testo', 'Un passaggio in ospedale', ''),
         ('immagine', 'pexels-6647017.jpg', 'Due volontari caricano le scatole sul furgone'), ('testo', 'Un’ora di tempo', '')],
    ], start=1):
        for tipo, val, alt in items:
            if tipo == 'immagine':
                voci.append({'tipo': 'immagine', 'immagine': K.img(val), 'alt': alt, 'colonna': str(col)})
            else:
                voci.append({'tipo': 'testo', 'testo': val, 'colonna': str(col)})
    return t.classic('scorrimentoverticale', {
        'voci': voci, 'colonne': '3', 'colonne_mobile': '2', 'durata': 30, 'direzione_1': 'giu', 'direzione_2': 'su',
        'direzione_3': 'giu', 'pausa_hover': True, 'altezza': 640, 'altezza_mobile': 460, 'gap': 22, 'gap_colonne': 14,
        'sfumatura_bordi': 18, 'allineamento': 'center', 'testo_font_family': 'heading', 'testo_font_size': 34,
        'testo_font_size_mobile': 22, 'testo_font_weight': '400', 'testo_colore': C['testo'], 'immagine_altezza': 220,
        'immagine_raggio': radius(6), 'separatore_colore': C['acc'],
    })


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, '01 — Cosa portiamo', ['Non solo', 'la cena.'], size=72, layout='stack', size_mobile=44),
                      K.text(t, 'Ogni sera i furgoni escono con le stesse cose. Ma quello che conta di più non sta nelle '
                                'casse: è la stessa faccia che torna, il martedì dopo, alla stessa ora.'),
                      K.button(t, 'Com’è un turno', NOTTE, pad_top=18)], wm='2-5', ws='1-1'),
               t.col([scorrimento(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 120}))

    numeri = t.classic('statstrip', {
        'items': [{'value': '365', 'label': 'sere all’anno, Natale compreso'}, {'value': '41.200', 'label': 'cene nel 2025'},
                  {'value': '3.870', 'label': 'coperte e sacchi a pelo'}, {'value': '120', 'label': 'volontari'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': True,
        'value_font_family': 'heading', 'value_color': C['testo'], 'value_size': 60, 'value_weight': '400',
        'label_color': C['tenue'], 'label_size': 11, 'label_uppercase': True, 'label_weight': '400', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([t.one([numeri])], bg=solid(C['sala']), padding='large', style={'padding_top': 70, 'padding_bottom': 70}))

    out.append(K.video_frase(t, V_LANTERNA, 'Perché ci chiamiamo così', 'Una luce <em>che si avvicina.</em>',
                             'La prima unità di strada usciva con le lanterne da campeggio, perché i lampioni sotto i portici '
                             'erano spenti. Le lanterne sono rimaste: di notte, una luce che si avvicina piano fa meno paura.',
                             overlay=45))

    raccolta = t.classic('progress', {
        'bars': 'Coperte di lana|74\nSacchi a pelo|52\nCalze e guanti|88', 'bar_color': C['acc'], 'bar_bg': mix(C['acc'], 14),
        'text_color': C['testo'], 'height': '10', 'show_percentage': True, 'animated': True, 'layout': 'bar',
        'animate_counter': True, 'animation_duration': '1800', 'border_radius': '6',
    })
    out.append(t.section([
        t.row([t.col([K.label(t, '02 — La raccolta d’inverno', color=C['acc']),
                      K.headline(t, 'Ci servono <em>seicento coperte</em> entro dicembre.', size=56, tag='h2', pad_bottom=16),
                      K.text(t, 'Nuove o lavate, di lana o di pile. Le porti in cucina dal lunedì al sabato, dalle 15 alle 19, '
                                'oppure le compri da noi: 18 euro l’una.'),
                      K.button(t, 'Porta o dona una coperta', DONA + '#raccolta', kind='pieno', pad_top=16)], wm='1-2', ws='1-1'),
               t.col([raccolta, K.text(t, 'Aggiornato ogni lunedì · 28 ottobre 2026', size=13, pad=(12, 0, 0, 0))],
                     wm='1-2', ws='1-1')], layout='50-50', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 130, 'padding_bottom': 120}))

    voce = t.classic('testimonial', {
        'quote': 'Pensavo di andare a dare qualcosa. <em>Ogni martedì torno a casa con qualcosa in più io.</em>',
        'author_name': 'Giulia, 27 anni', 'author_role': 'volontaria del turno del martedì, dal 2023', 'rating': '',
        'text_color': C['testo'], 'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'],
        'quote_font': 'heading', 'quote_size': 44, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': C['acc'],
    })
    out.append(t.section([K.centered(t, [voce])], bg=solid(C['sala']), padding='large',
                         style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(t.section([
        t.row([t.col([K.headline(t, 'Hai una sera <em>al mese?</em>', size=80, tag='h2', pad_bottom=18),
                      K.text(t, 'Basta quella. Un corso di due sere, un turno di affiancamento, e poi una sera al mese, quella '
                                'che scegli tu.', size=17),
                      K.button(t, 'Diventa volontario', UNISCITI, kind='pieno', pad_top=12)], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-16695774.jpg', 'Una mano regge una lanterna accesa nel buio', ratio='4/5', drift=True)],
                     wm='2-5', ws='1-1')], layout='custom', custom='58,42', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 140}))
    return page(out)


def build_notte():
    t = Tpl(ID, 'la-notte')
    out = [K.intro(t, 'La notte di un turno', ['Sei ore,', 'sei persone.'],
                   'Ogni squadra ha un capo turno, un autista, un medico o un infermiere e tre volontari. Partiamo da via '
                   'Saluzzo e torniamo lì: in mezzo, sei fermate e molte soste fuori programma.',
                   bg=K.photo_bg('pexels-19796702.jpg', overlay=58), overlay_text=True)]
    turni = t.classic('schedule', {
        'eyebrow': 'La settimana', 'heading': 'Chi esce, quando',
        'days': 'Lun, Mar, Mer, Gio, Ven, Sab, Dom', 'corner_label': 'Ora',
        'rows': [{'time': '15:00', 'cells': 'Raccolta | Raccolta | !Cucina scuola | Raccolta | Raccolta | !Raccolta grande | '},
                 {'time': '18:30', 'cells': 'Cucina | Cucina | Cucina | Cucina | Cucina | Cucina | !Cucina festiva'},
                 {'time': '20:30', 'cells': '!Furgone nord | !Furgone sud | !Furgone nord | !Furgone sud | !Tre furgoni | '
                                            '!Tre furgoni | !Furgone centro'},
                 {'time': '23:30', 'cells': 'Ospedali | | Ospedali | | Ospedali | Ospedali | '}],
        'zone_accent': C['acc'], 'zone_on': C['on_acc'], 'cell_bg': C['sala'],
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
        'head_color': C['tenue'], 'heading_color': C['testo'], 'align': 'left',
    })
    out.append(t.section([t.one([turni])], padding='large', style={'padding_top': 60}))
    for foto, ey, ti, tx, video in [
        (V_ZUPPA, '18:30 · La cucina', 'Si cucina come a casa.', 'Le verdure arrivano dal mercato di Porta Palazzo, il pane '
         'dai forni del quartiere. Il martedì la zuppa la fanno i ragazzi dell’alberghiero.', True),
        ('pexels-13517612.jpg', '20:30 · Il furgone', 'Le stesse fermate, le stesse facce.', 'Sei fermate fisse a orari fissi: '
         'chi ci aspetta sa che arriviamo. La costanza è la prima medicina.', False),
        ('pexels-6143814.jpg', '22:00 · Il tè', 'Si parla, se si vuole.', 'Il tè è una scusa. Ascoltiamo, segnaliamo ai servizi '
         'chi ce lo chiede, accompagniamo in ospedale chi non ce la fa.', False),
    ]:
        out.append(K.pannello(t, foto, ey, ti, tx, overlay=58, video=video))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove', ['La cucina', 'di via Saluzzo.'], size=48, layout='stack', size_mobile=34),
                      K.text(t, 'A San Salvario, nel cortile della parrocchia. Le donazioni si portano qui dal lunedì al '
                                'sabato, dalle 15 alle 19.'), K.contacts(t, VOCI)], wm='1-3', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Lanterne · la cucina', hex_marker=HEX['primary'])], wm='2-3', ws='1-1')],
              layout='custom', custom='33,67', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='dove'))
    return out


def build_unisciti():
    t = Tpl(ID, 'unisciti')
    out = [K.intro(t, 'Diventa volontario', ['Una sera', 'al mese basta.'],
                   'Non serve esperienza. Serve esserci quando hai detto che ci saresti, e lasciare il giudizio a casa.',
                   bg=K.photo_bg('pexels-6646864.jpg', overlay=58), overlay_text=True)]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Il colloquio', 'description': 'Mezz’ora in cucina con un capo turno: ci racconti chi '
                   'sei, ti raccontiamo come lavoriamo.'},
                  {'number': '02', 'title': 'Il corso', 'description': 'Due sere: ascolto, primo soccorso, i servizi della città, '
                   'cosa dire e cosa no.'},
                  {'number': '03', 'title': 'L’affiancamento', 'description': 'Un turno accanto a un volontario esperto, '
                   'senza fretta.'},
                  {'number': '04', 'title': 'La tua sera', 'description': 'Scegli la squadra e il giorno: da lì in poi, una '
                   'sera al mese.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': C['mono'], 'number_weight': '400', 'title_color': C['testo'], 'title_size': 28,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([t.one([passi])], padding='large', style={'padding_top': 60}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Che sera preferisci', 'sera', opzioni='Lunedì\nMartedì\nMercoledì\nGiovedì\nVenerdì\nSabato\n'
                     'Domenica\nNon importa'),
             K.campo('select', 'Cosa ti piacerebbe fare', 'ruolo', opzioni='Cucina\nStrada\nGuidare il furgone\nSono medico o '
                     'infermiere\nRaccolta e magazzino'),
             K.campo('textarea', 'Due righe su di te', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scrivici', ['Il prossimo corso', 'è a novembre.'],
                                'Ti chiamiamo entro una settimana per il colloquio in cucina.', VOCI, campi,
                                'Lanterne — nuovo volontario', 'Mi propongo', 'Grazie. Ti chiamiamo entro una settimana.'))
    out.append(K.domande(t, 'Prima', 'del primo turno.', [
        ('È pericoloso?', 'Usciamo sempre in squadra, con un capo turno che conosce le fermate da anni. In quindici anni non ci '
                          'è mai successo niente di serio.'),
        ('Posso venire con un amico?', 'Sì: fate il corso insieme e scegliete la stessa sera.'),
        ('Serve essere maggiorenni?', 'Per la strada sì. Dai sedici anni puoi aiutare in cucina e in magazzino.'),
        ('E se una sera non posso?', 'Avvisi il capo turno: c’è sempre una lista di riserve.'),
    ], 'uni', bg=False))
    return out


def build_dona():
    t = Tpl(ID, 'dona')
    out = [K.intro(t, 'Dona', ['Una cena', 'costa 2,40 euro.'],
                   'Con 24 euro paghi dieci cene; con 18, una coperta di lana. Tutto quello che arriva va in strada: '
                   'nessuno di noi prende uno stipendio.', bg=K.video_bg(V_GALLEGGIANTI, overlay=50), overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come donare', ['Tre modi,', 'tutti detraibili.'], size=56, layout='stack', size_mobile=38)],
                     wm='1-3', ws='1-1'),
               t.col([K.specs(t, [('Bonifico', 'IT00 X000 0000 0000 0000 0000 000 · causale «cene»'),
                                  ('5 per mille', 'codice fiscale 00000000000'),
                                  ('Una coperta', 'in cucina, dal lunedì al sabato dalle 15 alle 19'),
                                  ('Ricevuta', 'per la detrazione, entro un mese')])], wm='2-3', ws='1-1')],
              layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', html_id='raccolta'))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-6995065.jpg', 'Due volontari sistemano i vestiti donati', ratio='4/3')], wm='1-2', ws='1-1'),
               t.col([K.label(t, 'Cosa ci serve davvero', color=C['acc']),
                      K.headline(t, 'Calze, guanti, <em>termos.</em>', size=56, tag='h2', pad_bottom=14),
                      K.text(t, 'I vestiti ce li danno in tanti. Mancano sempre le cose piccole: calze di lana nuove, guanti, '
                                'cappelli, termos da un litro, biancheria pulita. E i sacchi a pelo, d’inverno.')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404', color=C['acc'], align='center'),
                       K.headline(t, 'Questa via <em>è al buio.</em>', size=110, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Torniamo dove c’è luce.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LANTERNA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Associazioni e cause', 'Scorri la notte',
    ['unità di strada', 'volontariato', 'senza dimora', 'torino', 'raccolta', 'scuro', 'slider pro'],
    'Un’unità di strada a Torino, nera e calda come una lanterna: l’apertura che si scorre attraverso le ore di un turno, '
    'dalle 18:30 in cucina al rientro, le colonne di foto e parole che scorrono in direzioni opposte, i numeri '
    'dell’inverno, le coperte raccolte, i turni della settimana e la richiesta per diventare volontari.',
    [('La notte di un turno', NOTTE), ('Volontari', UNISCITI), ('Dona', DONA), ('Una sera al mese', UNISCITI + '#scrivici')],
    [('home', 'Home', None), ('la-notte', 'La notte di un turno', 'la-notte'), ('unisciti', 'Diventa volontario', 'unisciti'),
     ('dona', 'Dona', 'dona')],
    HEX, '\'Figtree\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'DM Serif Display\', Georgia, \'Times New Roman\', serif', '\'Martian Mono\', ui-monospace, Menlo, monospace',
    ['DM Serif Display', 'Figtree', 'Martian Mono'], corsivo_di=['DM Serif Display'], peso_titoli='400', raggio=6, grana=6,
    titoli_line='1.0', titoli_spazio='-0.01em')

THEME['preview_v'] = '2'          # la prima anteprima aveva la fascia vuota dello scorrimento
THEME['preview_mobile_v'] = '2'
THEME['cursor']['style'] = 'alone'  # cursore: una luce che insegue (31 ott 2026)


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-notte.json': build_notte(), 'unisciti.json': build_unisciti(), 'dona.json': build_dona(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
