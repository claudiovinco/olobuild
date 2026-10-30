# -*- coding: utf-8 -*-
"""Genera il tema «Fotogramma» (id fotogramma): un cineclub di quartiere a Torino, in una sala del 1958 con 120 posti
rossi e il proiettore a pellicola, che fa anche una scuola di cinema per ragazzi e adulti. Scuro come la sala.

Nero di sala, il giallo dei sottotitoli, il rosso del velluto e il ciano del fascio del proiettore. Titoli in Big
Shoulders Display, testo in IBM Plex Sans, dati in IBM Plex Mono.

Apertura: Slider Pro a tutto schermo con le bande nere del cinemascope sopra e sotto (livelli globali), il contatore
del fotogramma che tremola, i titoli che entrano lettera per lettera e i sottotitoli gialli che si scrivono a macchina;
fra una scena e l'altra il taglio passa dal nero (fadeThroughDark). Poi i titoli della rassegna che scorrono (Marquee),
il cartellone dei giovedì (Cartellone), la sala da trascinare come una pellicola (Film reel), una domanda da cineclub
(Quiz), la pianta della sala a ventaglio con la vista da ogni settore (Pianta sala), i corti della scuola (Video
playlist) e le tessere (Info cards).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'fotogramma'
NAME = 'Fotogramma'

HEX = {
    'primary': '#F4D03F', 'primary_contrast': '#0B0B0C', 'secondary': '#C1272D', 'secondary_contrast': '#F5F1E8',
    'accent': '#58C4DC', 'dark': '#050506', 'light': '#F5F1E8', 'muted': '#141416', 'muted_contrast': '#ECE8DF',
    'text': '#ECE8DF', 'text_muted': '#9E9A92', 'background': '#0A0A0B', 'border': '#27272B', 'link': '#F4D03F',
}

HOME, PROGRAMMA, SCUOLA, TESSERA = '{{PAGINA:home}}', '{{PAGINA:programma}}', '{{PAGINA:la-scuola}}', '{{PAGINA:la-tessera}}'
MAIL, PHONE, PHONE_TEL = 'sala@fotogramma.example', '+39 011 000 1958', 'tel:+390110001958'
LAT, LNG = '45.0703', '7.6869'

V_VECCHIO, V_FASCIO, V_PUBBLICO = 'pexels-video-31966138', 'pexels-video-18359612', 'pexels-video-7985890'
V_BOBINE, V_MECCANICA, V_COPPIA, V_GIRA = 'pexels-video-855412', 'pexels-video-856593', 'pexels-video-6552603', 'pexels-video-5927778'
PHOTOS = [
    'pexels-7991491.jpg', 'pexels-39587241.jpg', 'pexels-7991486.jpg', 'pexels-31593645.jpg', 'pexels-7991318.jpg',
    'pexels-5515483.jpg', 'pexels-19374140.jpg', 'pexels-34084909.jpg', 'pexels-34085954.jpg', 'pexels-34084882.jpg',
    'pexels-6782758.jpg', 'pexels-8272148.jpg', 'pexels-8263369.jpg', 'pexels-28821625.jpg', 'pexels-20184838.jpg',
    'pexels-12875832.jpg', 'pexels-12279444.jpg', 'pexels-32728014.jpg', 'pexels-32728011.jpg', 'pexels-32222018.jpg',
    'pexels-36450982.jpg', 'pexels-3945314.jpg', 'pexels-29696177.jpg', 'pexels-8261829.jpg', 'pexels-4374757.jpg',
    'pexels-31208770.jpg', 'pexels-8652476.jpg', 'pexels-8652199.jpg', 'pexels-8263357.jpg', 'pexels-14551498.jpg',
    'pexels-2554899.jpg', 'pexels-4881624.jpg',
]
VIDEOS = [V_VECCHIO, V_FASCIO, V_PUBBLICO, V_BOBINE, V_MECCANICA, V_COPPIA, V_GIRA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Via Baltea 19, Barriera di Milano · Torino', TESSERA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]

RASSEGNA = [
    ('fg-1', 'Le notti di Cabiria', 'Federico Fellini · 1957 · restauro in 35 mm', 'Notturni', '2026-11-05', '21:00', '110 min'),
    ('fg-2', 'Il cielo sopra Berlino', 'Wim Wenders · 1987 · versione originale', 'Notturni', '2026-11-12', '21:00', '128 min'),
    ('fg-3', 'Paris, Texas', 'Wim Wenders · 1984 · copia in 35 mm', 'Restauri', '2026-11-19', '21:00', '147 min'),
    ('fg-4', 'I corti della scuola', 'Sei film di quindici minuti, girati a Barriera', 'Scuola', '2026-11-26', '20:30', '90 min'),
    ('fg-5', 'Stalker', 'Andrej Tarkovskij · 1979 · versione originale', 'Restauri', '2026-12-03', '21:00', '161 min'),
    ('fg-6', 'Notte sulla Terra', 'Jim Jarmusch · 1991 · cinque taxi, cinque città', 'Notturni', '2026-12-10', '21:00', '129 min'),
    ('fg-7', 'Ladri di biciclette', 'Vittorio De Sica · 1948 · restauro in 4K', 'Restauri', '2026-12-17', '21:00', '89 min'),
    ('fg-8', 'Hiroshima mon amour', 'Alain Resnais · 1959 · versione originale', 'Notturni', '2027-01-14', '21:00', '90 min'),
]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'FOTOGRAMMA', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'FOTOGRAMMA', 'Un cineclub in una sala del 1958 a Barriera di Milano: centoventi posti rossi, un '
                   'proiettore a pellicola e una scuola di cinema. Associazione culturale dal 1987.',
                   [('Il programma', PROGRAMMA), ('La scuola', SCUOLA), ('La tessera', TESSERA), ('Dove siamo', TESSERA + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Il giovedì', 'Si apre alle 20:15, il film comincia alle 21 in punto. Chi arriva tardi aspetta il '
                   'secondo tempo: c’è sempre, lo facciamo noi.', VOCI,
                   '© 2026 Associazione Fotogramma APS · Torino · CF 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150)


def apertura(t):
    m = K.mob
    scene = [
        ('0001', 'Rassegna d’inverno · Notturni', 'Il cinema si\nguarda al buio.', '— Spegnete il telefono. Il proiezionista '
         'vi vede.', K.sfondo_video(V_BOBINE, overlay=0.52)),
        ('0024', 'La cabina · dal 1958', 'Trentacinque\nmillimetri.', '— Ogni giovedì una pellicola vera, col suo rumore.',
         K.sfondo_video(V_FASCIO, overlay=0.35)),
        ('0576', 'La sala · 120 posti', 'Centoventi\nposti rossi.', '— Dopo il film si resta: si parla, si litiga, si beve.',
         K.sfondo_video(V_PUBBLICO, overlay=0.4)),
        ('1440', 'L’arena · da giugno', 'D’estate,\nsul tetto.', '— Lo schermo sale in terrazza. Portate il cuscino.',
         K.sfondo_foto('pexels-4881624.jpg', overlay=0.5, scala=1.08)),
    ]
    sl = []
    for i, (fot, occ, tit, sotto, sfondo) in enumerate(scene):
        n = i + 1
        sl.append(K.diapositiva('fg-%d' % n, sfondo, [
            K.livello('fg-f%d' % n, contenuto='Fotogramma %s · 35 mm · 1:1,85' % fot, x=56, y=13, w=38, size=12, peso='400',
                      colore=mix(C['chiaro'], 70), align='right', entra='fadeIn', ritardo=100, letterSpacing=3,
                      textTransform='uppercase', fontFamily='IBM Plex Mono', animLoop='flicker', animLoopDuration=2600,
                      responsive={'mobile': {'visible': False}}),
            K.livello('fg-o%d' % n, contenuto=occ, x=6, y=22, w=50, size=13, peso='500', colore=C['acc'], ritardo=150,
                      letterSpacing=3, textTransform='uppercase', fontFamily='IBM Plex Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 13, 'width': 88}}),
            K.livello('fg-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=27, w=70, size=132, peso='800',
                      colore=C['chiaro'], ritardo=300, durata=900, lineHeight=0.9, letterSpacing=-1,
                      textTransform='uppercase', charAnim={'stagger': 22, 'offsetX': -36, 'opacity': 0, 'blur': 6},
                      responsive={'mobile': {'fontSize': m(52), 'y': 16, 'width': 90}}),
            K.livello('fg-b%d' % n, tipo='button', contenuto='Il programma', x=6, y=58, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      ritardo=1100, buttonUrl=PROGRAMMA, hover={'bgColor': C['chiaro'], 'duration': 200},
                      responsive={'mobile': {'y': 36, 'fontSize': m(12)}}),
            K.livello('fg-s%d' % n, contenuto=sotto, x=12, y=76, w=76, size=24, peso='500', colore=C['acc'], align='center',
                      entra='typewriter', ritardo=1500, durata=1600, lineHeight=1.3, fontFamily='IBM Plex Sans',
                      textShadow={'x': 0, 'y': 2, 'blur': 6, 'color': C['nero']},
                      responsive={'mobile': {'fontSize': m(16), 'x': 6, 'y': 52, 'width': 88}}),
        ], durata=7500))
    bande = [
        K.livello('fg-banda-su', tipo='shape', x=0, y=0, w=100, h=9, entra='fadeIn', ritardo=0, durata=600, bgColor=C['nero'],
                  responsive={'mobile': {'visible': False}}),
        K.livello('fg-banda-giu', tipo='shape', x=0, y=91, w=100, h=9, entra='fadeIn', ritardo=0, durata=600,
                  bgColor=C['nero'], responsive={'mobile': {'visible': False}}),
    ]
    return K.proslider(t, sl, globali=bande, transizione='fadeThroughDark', durata_trans=1200, showArrows=True,
                       showDots=True, parallax=True, parallaxIntensity=3)


def cartellone(t, limite=0, filtri=False, intro=False):
    return t.classic('cartellone', {
        'fonte': 'voci', 'vista': 'lista', 'raggruppa': bool(filtri), 'ordina': True, 'limite': limite, 'passati': 'nascondi',
        'filtri_generi': filtri, 'filtri_mesi': filtri, 'ricerca': False, 'solo_genere': '', 'escludi_generi': '',
        'immagini': False, 'testo_tutti': 'Tutti', 'testo_biglietti': 'Prenota il posto', 'testo_info': '',
        'calendario': True, 'testo_calendario': 'Aggiungi al calendario', 'luogo_evento': 'Cineclub Fotogramma, Torino',
        'conto': intro, 'testo_conto': 'Mancano', 'occhiello_prossimo': 'Questo giovedì',
        'vuoto_testo': 'Il programma della prossima rassegna arriva presto.',
        'nessun_risultato': 'Nessun film per questa scelta.',
        'titolo_font_weight': '800', 'titolo_font_size': 34, 'titolo_color': C['testo'],
        'generi': [{'id': 'fg-g1', 'nome': 'Notturni', 'colore': C['acc']}, {'id': 'fg-g2', 'nome': 'Restauri', 'colore': C['acc2']},
                   {'id': 'fg-g3', 'nome': 'Scuola', 'colore': mix(C['sec'], 55, C['chiaro'])}],
        'voci': [{'id': vid, 'titolo': ti, 'sottotitolo': so, 'genere': ge, 'data': da, 'ora': ora, 'data_fine': '',
                  'orari': '', 'luogo': 'Sala grande', 'durata': du, 'prezzo': '6 € · 4 € under 26', 'stato': '',
                  'etichette': '', 'descrizione': '', 'incontro': '', 'immagine': '', 'link_biglietti': PROGRAMMA + '#posto',
                  'link_info': ''} for vid, ti, so, ge, da, ora, du in RASSEGNA],
    })


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    titoli = t.classic('marquee', {
        'content_type': 'text', 'text_items': ' | '.join(r[1] for r in RASSEGNA), 'separator': ' ● ', 'speed': '30',
        'direction': 'left', 'pause_hover': True, 'gap': 34, 'velocity_skew': True, 'bg_color': C['acc'],
        'text_color': C['on_acc'], 'font_size': 44, 'font_weight': '800', 'letter_spacing': 0, 'text_transform': 'uppercase',
        'font_family': 'heading', 'separator_color': C['on_acc'], 'height': 84, 'full_width': True,
    })
    out.append(t.section([t.one([titoli], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il giovedì alle 21', ['Questo giovedì,', 'e i prossimi.'], size=64, layout='stack',
                                size_mobile=40),
                      K.text(t, 'Un film a settimana, sempre in versione originale coi sottotitoli, quasi sempre in pellicola. '
                                'Prima del film Elisa lo presenta in cinque minuti, dopo si resta a parlarne.'),
                      K.button(t, 'Tutto il programma', PROGRAMMA, pad_top=18)], wm='2-5', ws='1-1'),
               t.col([cartellone(t, limite=3, intro=True)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='start'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))

    pellicola = t.classic('filmreel', {
        'title': 'La sala', 'show_title': True, 'hint_text': 'Trascina la pellicola', 'show_hint': True,
        'intro_eyebrow': 'Via Baltea 19 · dal 1958', 'show_intro': True,
        'intro_text': 'Era il cinema parrocchiale del quartiere. Nel 1987 l’abbiamo riaperto noi, senza cambiare le poltrone.',
        'items': [{'image': K.img(f), 'name': n, 'tag': g, 'size': s, 'link': ''} for f, n, g, s in [
            ('pexels-7991491.jpg', 'Le poltrone', 'La sala', 'tall'), ('pexels-28821625.jpg', 'L’insegna', 'Fuori', ''),
            ('pexels-34085954.jpg', 'Il proiettore', 'La cabina', 'tall'), ('pexels-32728014.jpg', 'La pellicola', 'La cabina', ''),
            ('pexels-8272148.jpg', 'Il pubblico', 'Giovedì', 'tall'), ('pexels-12279444.jpg', 'L’atrio', 'Ingresso', ''),
            ('pexels-8652476.jpg', 'I popcorn', 'Il bar', 'tall'), ('pexels-6782758.jpg', 'Le bobine', 'L’archivio', '')]],
        'rec_overlay': True, 'velocity_skew': True, 'skew_max': '6', 'progress_bar': True, 'scroll_mode': 'free',
        'progress_color': C['acc'], 'accent': C['acc'], 'bg_color': C['sala'],
    })
    out.append(t.section([t.one([pellicola], anim=None)], bg=solid(C['sala']), width='fullbleed', padding='remove-vertical'))

    quiz = t.classic('scorequiz', {
        'eyebrow': 'Tre domande da cineclub', 'heading': 'Che spettatore sei?',
        'intro': 'Rispondi e ti diciamo quale giovedì non perdere.',
        'questions': [
            {'text': 'Al cinema ti siedi…', 'options': 'In prima fila, con lo schermo addosso|4\nAl centro, per il quadro '
                                                      'intero|2\nIn galleria, vicino al proiettore|0'},
            {'text': 'Il film perfetto finisce…', 'options': 'Alle due di notte, per strada|4\nCon una copia restaurata che '
                                                           'sembra nuova|2\nCoi titoli di coda pieni di amici|0'},
            {'text': 'Dopo il film…', 'options': 'Torno a casa a piedi e ci penso|4\nResto al bar a litigare sulla regia|2\n'
                                               'Voglio girarne uno anch’io|0'}],
        'results': [
            {'min': 0, 'title': 'Regista in erba', 'text': 'La scuola fa per te: il corso di Sguardo comincia a ottobre, e a '
             'maggio il tuo corto va sullo schermo.', 'link_text': 'La scuola', 'link_url': SCUOLA},
            {'min': 5, 'title': 'Restauratore', 'text': 'I tuoi giovedì sono quelli dei restauri: Paris, Texas e Stalker, '
             'in pellicola.', 'link_text': 'Il programma', 'link_url': PROGRAMMA},
            {'min': 9, 'title': 'Nottambulo', 'text': 'La rassegna Notturni è fatta per te: Cabiria, Berlino, i taxi di '
             'Jarmusch.', 'link_text': 'Il programma', 'link_url': PROGRAMMA}],
        'result_label': 'Sei un…', 'back_label': 'Indietro', 'restart_label': 'Rifai', 'show_score': False,
        'zone_accent': C['acc'], 'zone_on': C['on_acc'], 'card_bg': C['sala'],
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': C['filo']},
        'align': 'left',
    })
    out.append(t.section([K.centered(t, [quiz])], padding='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_MECCANICA, 'La cabina', 'La pellicola <em>fa rumore.</em>',
                             'Il nostro proiettore è un Cinemeccanica del 1961. Lo sentite girare dietro di voi per tutto il '
                             'film: a noi piace, e anche a voi, dopo la seconda volta.', overlay=62,
                             extra=[K.button(t, 'Prendi la tessera', TESSERA, kind='pieno', align='center', pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='FOTOGRAMMA',
                page_intro_caption='Si spengono le luci', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_programma():
    t = Tpl(ID, 'programma')
    out = [K.intro(t, 'Il programma', ['Un film', 'ogni giovedì.'],
                   'La rassegna d’inverno si chiama Notturni: film che succedono di notte. In mezzo i restauri e, a fine '
                   'novembre, la serata dei corti della nostra scuola.', bg=K.photo_bg('pexels-7991486.jpg', overlay=55),
                   overlay_text=True)]
    out.append(t.section([t.one([cartellone(t, filtri=True)])], padding='large', width='large',
                         style={'padding_top': 70, 'padding_bottom': 100}))
    sala = t.classic('piantasala', {
        'forma': 'ventaglio', 'palco_testo': 'Schermo', 'buca': False, 'buca_testo': '', 'file': 12, 'posti_fila': 14,
        'ordini': 1, 'palchi': 9, 'loggione': False, 'palco_centrale': False, 'numeri_ordini': False, 'iniziale': 'primo',
        'testo_invito': 'Passa o tocca un settore: il prezzo e la vista da lì.', 'testo_intero': 'Intero',
        'testo_ridotto': 'Under 26', 'testo_vista': 'Lo schermo da qui', 'testo_link': 'Prenota qui',
        'settori': [
            {'id': 'fg-s1', 'nome': 'Prime file', 'zone': 'platea-a', 'prezzo': '6 €', 'ridotto': '4 €', 'nota': '',
             'descrizione': 'Lo schermo riempie tutto lo sguardo: per i film che vanno presi in faccia.',
             'vista': K.img('pexels-39587241.jpg'), 'link': TESSERA + '#scrivici', 'colore': C['acc']},
            {'id': 'fg-s2', 'nome': 'Centro sala', 'zone': 'platea-b', 'prezzo': '6 €', 'ridotto': '4 €', 'nota': '',
             'descrizione': 'Il posto del proiezionista quando scende a vedere: il quadro intero e il suono migliore.',
             'vista': K.img('pexels-7991318.jpg'), 'link': TESSERA + '#scrivici',
             'colore': mix(C['acc'], 60, C['chiaro'])},
            {'id': 'fg-s3', 'nome': 'Galleria', 'zone': 'o1-c', 'prezzo': '5 €', 'ridotto': '4 €', 'nota': '',
             'descrizione': 'Sopra tutti, vicino alla cabina: si sente il proiettore e si vede il fascio di luce.',
             'vista': K.img('pexels-7991486.jpg'), 'link': TESSERA + '#scrivici', 'colore': C['acc2']},
            {'id': 'fg-s4', 'nome': 'Galleria di lato', 'zone': 'o1-l', 'prezzo': '5 €', 'ridotto': '4 €',
             'nota': 'Posti non numerati', 'descrizione': 'Le due ali della galleria: per chi arriva all’ultimo.',
             'vista': K.img('pexels-31593645.jpg'), 'link': TESSERA + '#scrivici', 'colore': C['sec']}],
        'disposizione': 'lato', 'larghezza_pianta': 58, 'gap': 48, 'sfondo_scheda': C['sala'],
        'bordo_scheda': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
        'raggio': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0}, 'nome_color': C['testo'], 'nome_font_size': 34,
        'prezzo_color': C['acc'], 'descrizione_color': C['tenue'], 'colore_etichette': C['tenue'],
        'colore_palco': C['sec'],
    })
    out.append(t.section([
        t.one([K.sechead(t, 'La sala', ['Scegli', 'da dove guardare.'], size=60,
                         tagline='I posti si prenotano fino al mercoledì sera. Il giovedì, quelli rimasti si danno alla porta.')]),
        t.one([sala]),
    ], bg=solid(C['sala']), padding='large', html_id='posto', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'del film.', [
        ('Serve la tessera?', 'Sì: siamo un’associazione e la legge lo chiede. Costa 15 € all’anno e la fai alla cassa in '
                              'due minuti, la prima sera.'),
        ('I film sono doppiati?', 'Mai. Versione originale coi sottotitoli in italiano, gialli come una volta.'),
        ('Si può arrivare dopo l’inizio?', 'Si entra fino alle 21:10. Dopo si aspetta l’intervallo, a metà film.'),
        ('C’è da mangiare?', 'Il bar fa popcorn col burro, vino, birra del quartiere e la torta di Mirella il giovedì.'),
    ], 'pr', bg=False))
    return out


def build_scuola():
    t = Tpl(ID, 'la-scuola')
    out = [K.intro(t, 'La scuola', ['Si impara', 'guardando.'],
                   'Tre corsi da ottobre a maggio, il lunedì e il mercoledì sera, in sala e nella cabina. Si finisce '
                   'girando un corto di quindici minuti che proiettiamo un giovedì, davanti a tutti.',
                   bg=K.video_bg(V_GIRA, overlay=55), overlay_text=True)]
    corti = t.classic('videoplaylist', {
        'videos': [{'url': K.img(v + '.mp4'), 'title': ti, 'duration': '', 'thumbnail': K.img(v + '.jpg')} for v, ti in [
            (V_VECCHIO, 'Bianco · corto in pellicola, corso di Sguardo 2026'),
            (V_COPPIA, 'Proiezione privata · corso di Montaggio 2026'),
            (V_GIRA, 'La bobina · corso di Pellicola 2025'),
            (V_PUBBLICO, 'Il pubblico del giovedì · documentario collettivo')]],
        'layout': 'sidebar-right', 'player_height': 460, 'sidebar_width': 320, 'sidebar_bg': C['sala'],
        'text_color': C['testo'], 'active_color': C['acc'], 'show_duration': False, 'autoplay_next': False,
        'thumbnail_ratio': '16/9',
    })
    out.append(t.section([
        t.one([K.sechead(t, 'I corti', ['Fatti qui,', 'visti qui.'], size=60,
                         tagline='Ogni corso finisce con un film. Questi sono quelli degli ultimi due anni.')]),
        t.one([corti]),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 110}))
    corsi = []
    for foto, nome, chi, testo, dati in [
        ('pexels-32222018.jpg', 'Sguardo', 'Per chi comincia', 'Come si guarda un film: l’inquadratura, la luce, il tempo. '
         'Si vedono insieme venti scene e si gira la prima.', [('Quando', 'lunedì 20:30'), ('Durata', 'ottobre — gennaio'),
                                                               ('Costo', '240 €')]),
        ('pexels-36450982.jpg', 'Montaggio', 'Con un po’ di pratica', 'Dal girato al film. Si lavora sul materiale dei '
         'corsi di Sguardo, poi sul proprio.', [('Quando', 'mercoledì 20:30'), ('Durata', 'febbraio — maggio'),
                                               ('Costo', '280 €')]),
        ('pexels-34084909.jpg', 'Pellicola', 'In cabina', 'Come si carica, si giunta e si proietta una pellicola da 35 mm. '
         'Sei sere col nostro proiezionista, Gianni.', [('Quando', 'sabato 15:00'), ('Durata', 'sei incontri'),
                                                         ('Costo', '120 €')]),
    ]:
        corsi.append(t.col([K.image(t, foto, 'Il corso di ' + nome, ratio='4/3'), K.label(t, chi, color=C['acc'], pad_top=18),
                            K.headline(t, nome, size=48, tag='h3', pad_bottom=10), K.text(t, testo), K.specs(t, dati)],
                           wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'I corsi', ['Tre sere', 'a settimana.'], size=60)]),
        t.row(corsi, layout='33-33-33', gap=36, valign='start'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_COPPIA, 'Il saggio', 'Il vostro film, sul nostro schermo.', 'A fine corso i corti si '
                          'proiettano in sala, un giovedì, coi titoli di testa e il pubblico vero. Poi si va al bar.',
                          'Iscriviti', TESSERA + '#scrivici', overlay=55, video=True))
    return out


def build_tessera():
    t = Tpl(ID, 'la-tessera')
    out = [K.intro(t, 'La tessera', ['Quindici euro,', 'tutto l’anno.'],
                   'Fotogramma è un’associazione: con la tessera entri a tutte le proiezioni, voti la rassegna dell’anno '
                   'dopo e puoi iscriverti ai corsi.', bg=K.photo_bg('pexels-8272148.jpg', overlay=60), overlay_text=True)]
    prezzi = t.classic('info-cards', {
        'container_bg': {'type': 'solid', 'color': C['sala']}, 'container_radius': radius(0), 'container_padding': 12,
        'columns': 4, 'columns_tablet': 2, 'card_color': C['testo'], 'card_radius': radius(0), 'card_padding': 28,
        'title_color': C['acc'], 'counter_color': C['tenue'], 'icon_color': C['acc'], 'title_size': 84, 'title_weight': '800',
        'items': [{'counter': '01', 'counter_label': 'La tessera', 'title': '15', 'title_accent': '€',
                   'title_accent_italic': False, 'description': 'Per un anno, da settembre a settembre. <strong>Si fa alla '
                   'cassa</strong> la prima sera.', 'icon': 'id-card', 'footer_dot_color': C['acc'], 'footer_text': '',
                   'link_url': '', 'link_text': '', 'media_image': '', 'media_label': ''},
                  {'counter': '02', 'counter_label': 'L’ingresso', 'title': '6', 'title_accent': '€',
                   'title_accent_italic': False, 'description': 'Ogni giovedì, <strong>4 € fino a 26 anni</strong>.',
                   'icon': 'ticket', 'footer_dot_color': C['acc'], 'footer_text': '', 'link_url': '', 'link_text': '',
                   'media_image': '', 'media_label': ''},
                  {'counter': '03', 'counter_label': 'Il carnet', 'title': '45', 'title_accent': '€',
                   'title_accent_italic': False, 'description': '<strong>Dieci ingressi</strong> da usare quando vuoi, '
                   'anche in due.', 'icon': 'layers', 'footer_dot_color': C['acc'], 'footer_text': '', 'link_url': '',
                   'link_text': '', 'media_image': '', 'media_label': ''},
                  {'counter': '04', 'counter_label': 'L’arena', 'title': '0', 'title_accent': '€',
                   'title_accent_italic': False, 'description': 'D’estate sul tetto <strong>si entra gratis</strong> con '
                   'la tessera.', 'icon': 'sun', 'footer_dot_color': C['acc'], 'footer_text': '', 'link_url': '',
                   'link_text': '', 'media_image': '', 'media_label': ''}],
    })
    out.append(t.section([t.one([prezzi])], padding='large', width='large', style={'padding_top': 70, 'padding_bottom': 90}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Cosa ti interessa', 'cosa', True,
                     opzioni='La tessera\nIl corso di Sguardo\nIl corso di Montaggio\nIl corso di Pellicola\nFare il volontario in sala'),
             K.campo('select', 'Età', 'eta', opzioni='Fino a 26 anni\nDa 27 anni in su'),
             K.campo('textarea', 'Il film che ci proporresti', 'film', larghezza='1-1',
                     segnaposto='Per la rassegna dell’anno prossimo: il titolo e perché.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scrivici', ['Ti teniamo', 'il posto.'],
                                'Ti rispondiamo entro due giorni. Per i corsi i posti sono quattordici, per la sala '
                                'centoventi.', VOCI, campi, 'Fotogramma — iscrizione', 'Mandala',
                                'Ricevuta. Ci vediamo al buio.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['A Barriera,', 'dietro la chiesa.'], size=52, layout='stack',
                                size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via Baltea 19, Torino'), ('Tram', '4, fermata Corelli'),
                                  ('Autobus', '18 e 49'), ('In bici', 'rastrelliera nel cortile')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fotogramma · cineclub', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pellicola spezzata', color=C['acc'], align='center'),
                       K.headline(t, 'Fine del <em>primo tempo.</em>', size=110, tag='h1', align='center', pad_bottom=20,
                                  maiuscolo=True),
                       K.text(t, 'Il proiezionista sta giuntando la pellicola. Intanto torna in sala.', size=17,
                              align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_VECCHIO, overlay=55), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Cultura e formazione', 'Buio in sala',
    ['cineclub', 'cinema', 'rassegna', 'scuola di cinema', 'associazione', 'pellicola', 'scuro', 'slider pro'],
    'Un cineclub di quartiere con la scuola di cinema, nero di sala col giallo dei sottotitoli: l’apertura col '
    'cinemascope, il contatore del fotogramma e i sottotitoli che si scrivono a macchina, il cartellone dei giovedì, la '
    'sala da trascinare come una pellicola, la pianta a ventaglio con la vista da ogni posto, i corti degli allievi e le '
    'tessere.',
    [('Il programma', PROGRAMMA), ('La scuola', SCUOLA), ('Dove siamo', TESSERA + '#arrivare'), ('La tessera', TESSERA)],
    [('home', 'Home', None), ('programma', 'Il programma', 'programma'), ('la-scuola', 'La scuola', 'la-scuola'),
     ('la-tessera', 'La tessera', 'la-tessera')],
    HEX, '\'IBM Plex Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Big Shoulders Display\', \'Oswald\', Impact, sans-serif', '\'IBM Plex Mono\', ui-monospace, Menlo, monospace',
    ['Big Shoulders Display', 'IBM Plex Sans', 'IBM Plex Mono'], peso_titoli='800', raggio=0, grana=7, titoli_line='0.95',
    titoli_spazio='-0.005em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'programma.json': build_programma(), 'la-scuola.json': build_scuola(),
                        'la-tessera.json': build_tessera(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
