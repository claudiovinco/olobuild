# -*- coding: utf-8 -*-
"""Genera il tema «Ombra & Riserbo» (id ombra-riserbo): un'agenzia investigativa autorizzata a Milano dal 1971, in
corso Genova. Indagini aziendali, familiari e difensive per gli avvocati, con prove che reggono in tribunale. Scuro come
una strada bagnata di notte.

Nero d'asfalto, il rosso del filo sulla bacheca delle prove, l'ambra dei lampioni al sodio e il grigio acciaio. Titoli
in Spectral, testo in Public Sans, fascicoli e timbri in Special Elite (la macchina da scrivere).

Apertura: Slider Pro coi video veri (le sagome nella nebbia sotto i lampioni, il fumo controluce), ogni diapositiva è
un fascicolo; il timbro «RISERVATO» arriva come un colpo di tampone, e in alto, fisso, l'ora di una telecamera di
sorveglianza che tremola (livello globale). Poi la frase che si legge solo con la torcia (Blend text «spotlight»), i
servizi sulle carte da girare (Flip card), il metodo in quattro passi (Process steps), il lucchetto da aprire con
l'indizio nascosto nella pagina (Lucchetto) e il documento da grattare per la prima consulenza (Scratch).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'ombra-riserbo'
NAME = 'Ombra & Riserbo'

HEX = {
    'primary': '#E5534B', 'primary_contrast': '#0A0B0D', 'secondary': '#3A4A5C', 'secondary_contrast': '#F3F0EA',
    'accent': '#E8B04B', 'dark': '#050506', 'light': '#F3F0EA', 'muted': '#14161A', 'muted_contrast': '#E9E6E1',
    'text': '#E9E6E1', 'text_muted': '#9A9893', 'background': '#0A0B0D', 'border': '#26292E', 'link': '#E5534B',
}

HOME, SERVIZI, AGENZIA, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:l-agenzia}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'riserbo@ombrariserbo.example', '+39 02 000 1971', 'tel:+39020001971'
LAT, LNG = '45.4566', '9.1772'

V_NEBBIA, V_FARO, V_FUMO, V_PIOGGIA, V_AUTO = ('pexels-video-11839713', 'pexels-video-15019287', 'pexels-video-5544553',
                                               'pexels-video-4458918', 'pexels-video-32161278')
PHOTOS = [
    'pexels-28186200.jpg', 'pexels-28207369.jpg', 'pexels-12608643.jpg', 'pexels-29733300.jpg', 'pexels-11966621.jpg',
    'pexels-9999925.jpg', 'pexels-13413234.jpg', 'pexels-18663088.jpg', 'pexels-17245389.jpg', 'pexels-12291873.jpg',
    'pexels-30639466.jpg', 'pexels-36333607.jpg', 'pexels-1545728.jpg', 'pexels-15118801.jpg', 'pexels-10874801.jpg',
    'pexels-8369515.jpg', 'pexels-8369522.jpg', 'pexels-8369514.jpg', 'pexels-8371702.jpg', 'pexels-7319074.jpg',
    'pexels-17543687.jpg', 'pexels-7319080.jpg', 'pexels-7319078.jpg', 'pexels-8370871.jpg', 'pexels-8872588.jpg',
    'pexels-8382286.jpg', 'pexels-30399640.jpg', 'pexels-34947617.jpg', 'pexels-8872445.jpg', 'pexels-816622.jpg', 'pexels-11312207.jpg',
    'pexels-6958389.jpg', 'pexels-36680074.jpg', 'pexels-22845438.jpg', 'pexels-27568101.jpg',
]
VIDEOS = [V_NEBBIA, V_FARO, V_FUMO, V_PIOGGIA, V_AUTO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Corso Genova 21, terzo piano · Milano', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Ombra & Riserbo', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Ombra & Riserbo', 'Agenzia investigativa autorizzata dalla Prefettura di Milano dal 1971. Indagini '
                   'aziendali, familiari e difensive, in tutta Italia. Il primo colloquio è riservato e senza impegno.',
                   [('Servizi', SERVIZI), ('L’agenzia', AGENZIA), ('Contatti', CONTATTI), ('Dove siamo', CONTATTI + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'La licenza', 'Licenza prefettizia n. 0000/1971 ai sensi dell’art. 134 TULPS. Trattiamo i dati secondo '
                   'il codice deontologico delle investigazioni private.', VOCI,
                   '© 2026 Ombra & Riserbo srl · Milano · P.IVA 00000000000',
                   [('linkedin', 'https://linkedin.com')], marchio_size=110)


def apertura(t):
    m = K.mob
    fascicoli = [
        ('Fascicolo 0412 · aziende', 'Sappiamo\ndove guardare.', 'Agenzia investigativa autorizzata a Milano dal 1971. '
         'Indagini aziendali, familiari, difensive: con discrezione, e con prove che reggono in tribunale.',
         K.sfondo_video(V_NEBBIA, overlay=0.4), 'Riservato'),
        ('Fascicolo 0388 · lavoro', 'Un dipendente\nche non c’era.', 'Assenteismo, concorrenza sleale, notizie che '
         'escono dall’ufficio. Raccogliamo prove lecite, utilizzabili davanti al giudice del lavoro.',
         K.sfondo_foto('pexels-8369515.jpg', overlay=0.6, scala=1.08), 'Riservato'),
        ('Fascicolo 0301 · famiglia', 'Le cose\nche non si dicono.', 'Affidamento dei figli, separazioni, persone '
         'scomparse. Prima ascoltiamo, poi vi diciamo con franchezza se possiamo aiutarvi.',
         K.sfondo_video(V_FUMO, overlay=0.35), 'Riservato'),
        ('Fascicolo 0276 · difese', 'La verità\nvuole prove.', 'Indagini difensive per gli avvocati: testimoni, '
         'sopralluoghi, ricostruzioni. Lavoriamo con i penalisti di mezza Lombardia.', K.sfondo_video(V_FARO, overlay=0.45),
         'Chiuso'),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo, timbro) in enumerate(fascicoli):
        n = i + 1
        sl.append(K.diapositiva('or-%d' % n, sfondo, [
            K.livello('or-o%d' % n, contenuto=occ, x=6, y=24, w=44, size=14, peso='400', colore=C['acc2'], ritardo=150,
                      entra='typewriter', durata=900, letterSpacing=1, fontFamily='Special Elite',
                      responsive={'mobile': {'fontSize': m(12), 'y': 12, 'width': 88}}),
            K.livello('or-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=29, w=58, size=100, peso='400',
                      colore=C['chiaro'], ritardo=500, durata=1000, lineHeight=1.0, entra='fadeIn',
                      sfx={'effect': 'blockRight', 'color': C['acc'], 'duration': 1000},
                      textShadow={'x': 0, 'y': 2, 'blur': 28, 'color': mix(C['nero'], 65)},
                      responsive={'mobile': {'fontSize': m(42), 'y': 16, 'width': 88}}),
            K.livello('or-s%d' % n, contenuto=testo, x=6, y=64, w=34, size=17, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1300, lineHeight=1.6, responsive={'mobile': {'fontSize': m(15), 'y': 31, 'width': 88}}),
            K.livello('or-b%d' % n, tipo='button', contenuto='Un colloquio riservato', x=6, y=80, w='auto', size=12,
                      peso='600', colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      ritardo=1600, buttonUrl=CONTATTI + '#scrivici', hover={'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 48, 'fontSize': m(12)}}),
            K.livello('or-k%d' % n, contenuto=timbro, x=70, y=40, w='auto', size=30, peso='400', colore=C['acc'],
                      entra='zoomIn', ritardo=1900, durata=260, letterSpacing=6, textTransform='uppercase',
                      fontFamily='Special Elite', padding=14, borderWidth=3, borderColor=C['acc'], borderRadius=4,
                      opacity=88, hover={'rotation': -6, 'duration': 300},
                      responsive={'mobile': {'x': 50, 'y': 60, 'fontSize': m(18)}}),
        ], durata=7000))
    globali = [K.livello('or-cam', contenuto='● REC · CAM 03 · CORSO GENOVA · 23:47:12', x=56, y=13, w=40, size=12,
                         peso='400', colore=mix(C['chiaro'], 75), align='right', entra='fadeIn', ritardo=300, letterSpacing=2,
                         fontFamily='Special Elite', animLoop='flicker', animLoopDuration=3000,
                         responsive={'mobile': {'visible': False}})]
    return K.proslider(t, sl, globali=globali, transizione='zoom', durata_trans=1100, showArrows=True, showDots=True,
                       parallax=True, parallaxIntensity=3)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    torcia = t.classic('blendtext', {
        'text': 'Niente resta nell’ombra.', 'tag': 'p', 'font_size': '150', 'font_size_tablet': '96', 'font_size_mobile': '52',
        'font_weight': '400', 'font_family': 'heading', 'text_transform': 'none', 'letter_spacing': '-3', 'line_height': '1',
        'text_align': 'center', 'text_color': C['tenue'], 'blend_mode': 'difference', 'mode': 'spotlight',
        'spotlight_size': 320, 'spotlight_softness': 35, 'spotlight_blend': 'difference', 'spotlight_color': C['chiaro'],
        'spotlight_easing': 18,
    }, style={})
    out.append(t.section([
        t.one([K.label(t, 'Muovi la torcia', color=C['acc2'], align='center')]),
        t.one([torcia], anim=None),
        K.centered(t, [K.text(t, 'Lavoriamo dove gli altri non guardano: archivi, registri, strade alle due di notte. Ma '
                                 'sempre dentro la legge, perché una prova raccolta male non serve a nessuno.', size=17,
                              align='center')]),
    ], padding='large', style={'padding_top': 150, 'padding_bottom': 130}))

    def carta(foto, fronte, sotto, retro, testo):
        return t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center',
                            'overlay_color': C['nero'], 'overlay_opacity': 55},
            'back_media': {'type': 'none'}, 'front_title': fronte, 'front_description': sotto, 'front_text_color': C['chiaro'],
            'front_text_align': 'left', 'front_valign': 'bottom', 'back_title': retro, 'back_description': testo,
            'back_bg': C['sala'], 'back_text_color': C['testo'], 'back_text_align': 'left', 'back_valign': 'center',
            'back_cta_text': '', 'back_cta_url': '',
        })
    out.append(t.section([
        t.one([K.sechead(t, 'I fascicoli', ['Tre stanze,', 'un solo metodo.'], size=64,
                         tagline='Girate una carta: c’è quello che facciamo davvero, e quello che non facciamo mai.')]),
        t.row([t.col([carta('pexels-8369514.jpg', 'Aziende', 'Dipendenti, soci, concorrenti', 'Per le aziende',
                            'Assenteismo e malattie sospette, concorrenza sleale, fughe di dati, verifiche su soci e '
                            'fornitori. Relazione utilizzabile in giudizio.')], wm='1-3', ws='1-1'),
               t.col([carta('pexels-28186200.jpg', 'Famiglia', 'Separazioni, figli, persone', 'Per le famiglie',
                            'Affidamento dei minori, assegni di mantenimento, rintraccio di persone e di eredi. Mai '
                            'intercettazioni: sono reato, e chi ve le propone vi mette nei guai.')], wm='1-3', ws='1-1'),
               t.col([carta('pexels-7319080.jpg', 'Difese', 'Per gli avvocati', 'Per i penalisti',
                            'Indagini difensive ex art. 391-bis: ricerca di testimoni, sopralluoghi, analisi di tabulati '
                            'e immagini. Riferiamo solo all’avvocato.')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=20, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_PIOGGIA, 'Le notti', 'Pedinare è aspettare.', 'Su dieci ore di appostamento, nove sono '
                          'attesa sotto la pioggia. La decima è quella che vi serve: la fotografiamo, la datiamo e la '
                          'mettiamo a verbale.', 'I servizi', SERVIZI, overlay=50, video=True))
    lucchetto = t.classic('lucchetto', {
        'enigma': '<p><strong>Fascicolo n. 0.</strong> Il lucchetto ha quattro ruote. La combinazione è l’anno in cui '
                  'Ombra & Riserbo ha aperto la sua prima pratica.</p><p>È scritto in questa pagina, più di una volta: basta '
                  'guardare dove gli altri non guardano.</p>',
        'indizio': 'Scendete fino in fondo alla pagina: il numero della licenza prefettizia finisce con l’anno.',
        'testo_indizio': 'Mostra un indizio', 'tentativi_indizio': 2, 'combinazione': '1971', 'simboli': 'cifre',
        'testo_pulsante': 'Apri', 'messaggio_errore': 'Non si apre. Guardate meglio: la risposta è nella pagina.',
        'mostra_tentativi': True,
        'messaggio_successo': 'Aperto. Avete l’occhio giusto: è così che lavoriamo, si osserva e si collega. Il codice qui '
                              'sotto vale il primo colloquio gratuito.',
        'codice_premio': 'FASCICOLO1971', 'testo_copia': 'Copia il codice', 'testo_copiato': 'Copiato',
        'pulsante_premio_testo': 'Prenota il colloquio', 'pulsante_premio_url': CONTATTI + '?codice=FASCICOLO1971#scrivici',
        'ricorda': False, 'mostra_lucchetto': True, 'icona_lucchetto': '', 'stile': 'acciaio', 'colore_accento': C['acc'],
        'colore_successo': C['acc2'], 'colore_errore': C['acc'], 'sfondo_pannello': C['sala'], 'raggio': radius(0),
        'ombra': 'none', 'enigma_color': C['testo'], 'messaggio_color': C['testo'],
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Una prova', ['Aprite', 'il fascicolo.'], size=64, layout='stack', size_mobile=40),
                      K.text(t, 'Il nostro lavoro è notare i dettagli. Provateci voi: la combinazione è nascosta in questa '
                                'pagina. Chi apre il lucchetto ha il primo colloquio gratuito.')], wm='2-5', ws='1-1'),
               t.col([lucchetto], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_FARO, 'Dal 1971', 'Discrezione è <em>il nostro mestiere.</em>',
                             'Nessuno saprà che ci avete chiamato. I fascicoli chiusi si distruggono dopo cinque anni, '
                             'davanti a un notaio.', overlay=50,
                             extra=[K.button(t, 'Un colloquio riservato', CONTATTI + '#scrivici', kind='pieno', align='center',
                                             pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Ombra & Riserbo',
                page_intro_caption='Milano · dal 1971', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Prove lecite,', 'relazioni chiare.'],
                   'Ogni incarico comincia con un colloquio e un preventivo scritto, e finisce con una relazione firmata, '
                   'con le foto datate e i verbali: quella che il vostro avvocato porterà in aula.',
                   bg=K.photo_bg('pexels-11966621.jpg', overlay=60), overlay_text=True)]
    metodo = t.classic('process-steps', {
        'items': [{'number': '0%d' % (k + 1), 'title': ti, 'description': de} for k, (ti, de) in enumerate([
            ('Il colloquio', 'Un’ora, in agenzia o dal vostro avvocato. Ci raccontate, vi diciamo se si può fare e come.'),
            ('Il preventivo', 'Scritto, con le ore, i giorni e le spese. Nessuna sorpresa a fine indagine.'),
            ('L’indagine', 'Appostamenti, archivi, testimoni. Vi aggiorniamo ogni settimana, mai per telefono.'),
            ('La relazione', 'Firmata, con foto datate e verbali. Pronta per il giudice, e per il vostro avvocato.')])],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 15, 'number_font': 'Special Elite', 'number_weight': '400', 'title_color': C['testo'],
        'title_size': 28, 'title_weight': '400', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15,
        'align': 'left', 'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1,
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Il metodo', ['Quattro passi,', 'sempre gli stessi.'], size=56)]),
        t.one([metodo]),
    ], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 100}))
    for k, (aid, foto, nome, cosa, testo, dati) in enumerate([
        ('aziende', 'pexels-8382286.jpg', 'Indagini aziendali', 'Per imprenditori e uffici del personale',
         'Verifichiamo assenze e malattie, controlliamo il rispetto del patto di non concorrenza, cerchiamo chi porta fuori '
         'i progetti. Sempre nel rispetto dello Statuto dei lavoratori.',
         [('Tempi', 'da una settimana'), ('Costo', 'da 1.800 € a incarico'), ('Relazione', 'utilizzabile in giudizio')]),
        ('famiglia', 'pexels-17245389.jpg', 'Indagini familiari', 'Per chi deve proteggere qualcuno',
         'Separazioni e affidamento dei figli, frequentazioni a rischio dei ragazzi, rintraccio di persone scomparse e '
         'di eredi. Con il tatto che serve, e senza mai superare la legge.',
         [('Tempi', 'da tre giorni'), ('Costo', 'da 900 €'), ('Colloquio', 'anche la sera, anche a casa vostra')]),
        ('difese', 'pexels-7319074.jpg', 'Indagini difensive', 'Per gli avvocati penalisti',
         'Ricerchiamo testimoni, facciamo sopralluoghi e ricostruzioni, analizziamo immagini e tabulati che l’avvocato '
         'ci consegna. Riferiamo solo al difensore.',
         [('Incarico', 'dall’avvocato, ex art. 391-bis'), ('Tempi', 'secondo le udienze'), ('Costo', 'a preventivo')]),
    ]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, nome, ratio='4/5')], wm='2-5', ws='1-1'),
                   t.col([K.label(t, cosa, color=C['acc2']), K.headline(t, nome, size=56, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati)], wm='3-5', ws='1-1')],
                  layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 == 0 else None,
            style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima', 'di chiamarci.', [
        ('È legale?', 'Sì: siamo autorizzati dalla Prefettura e lavoriamo solo con mezzi leciti. Niente intercettazioni, '
                      'niente accessi a telefoni o email: sono reati, e invalidano le prove.'),
        ('Quanto costa?', 'Dipende dalle ore e dai giorni. Il preventivo è scritto e non cambia senza il vostro sì.'),
        ('Qualcuno saprà che vi ho chiamato?', 'No. Il segreto professionale vale per noi come per un avvocato.'),
        ('Lavorate fuori Milano?', 'In tutta Italia, e con corrispondenti in Svizzera e in Francia.'),
    ], 'se', bg=False))
    return out


def build_agenzia():
    t = Tpl(ID, 'l-agenzia')
    out = [K.intro(t, 'L’agenzia', ['Tre stanze', 'in corso Genova.'],
                   'L’ha aperta nel 1971 Ernesto Riva, ex ispettore di polizia. Oggi la dirige sua nipote Giulia, giurista, '
                   'con sei investigatori e un archivio di cinquant’anni di Milano.',
                   bg=K.photo_bg('pexels-8872588.jpg', overlay=60), overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-34947617.jpg', 'La direttrice Giulia Riva, a braccia conserte', ratio='4/5', position='center 30%',
                              drift=True)],
                     wm='2-5', ws='1-1'),
               t.col([K.label(t, 'La direttrice', color=C['acc2']),
                      K.headline(t, 'Giulia Riva, <em>terza generazione.</em>', size=52, tag='h2', pad_bottom=14),
                      K.text(t, 'Laureata in giurisprudenza, dieci anni in uno studio penalista prima di tornare in agenzia. '
                                'Ha portato il metodo: ogni prova raccolta deve poter reggere un controinterrogatorio.'),
                      K.specs(t, [('Investigatori', 'sei, tutti con licenza'), ('Archivio', 'oltre 9.000 fascicoli dal 1971'),
                                  ('Lingue', 'italiano, inglese, francese'), ('Corrispondenti', 'Lugano, Nizza, Roma')])],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-8369522.jpg', 'Due investigatori davanti alla bacheca delle prove', ratio='4/5')],
                     wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-28207369.jpg', 'Una persona con l’ombrello in una strada bagnata', ratio='4/5')],
                     wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-816622.jpg', 'Il teleobiettivo di una macchina fotografica', ratio='4/5')],
                     wm='1-3', ws='1-1')], layout='33-33-33', gap=16),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_AUTO, 'Una regola del nonno', 'Si guarda tutto, <em>si scrive solo il vero.</em>',
                             'Ernesto lo diceva a ogni nuovo investigatore. È rimasto scritto a macchina, sopra la porta '
                             'dell’archivio.', overlay=55))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Ci parliamo', 'a voce.'],
                   'Per il primo contatto basta un messaggio con un recapito: vi richiamiamo noi, all’ora che indicate, da '
                   'un numero riservato. Nessun dettaglio per iscritto, finché non ci vediamo.',
                   bg=K.video_bg(V_PIOGGIA, overlay=55), overlay_text=True)]
    gratta = t.classic('scratchfx', {
        'image': '', 'prize_eyebrow': 'Riservato', 'prize_title': 'Il primo colloquio è gratuito',
        'prize_text': 'Un’ora in agenzia, o dal vostro avvocato, per capire se possiamo aiutarvi. Citate il codice quando '
                      'chiamate.', 'coupon_code': 'RISERBO', 'copy_label': 'Copia il codice', 'remember': True,
        'text_color': C['testo'], 'under_bg': C['sala'], 'hint': 'Grattate il documento', 'show_button': True,
        'reveal_label': 'Scopri subito', 'cover_type': 'solid', 'cover_color': C['nero'], 'cover_color2': C['tenue'],
        'cover_text': '▇▇▇▇ OMISSIS ▇▇▇▇', 'cover_text_color': C['acc'], 'brush_size': 34, 'reveal_threshold': 55,
        'aspect': '16/9', 'height_mode': 'aspect', 'align': 'left', 'max_width': 560,
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Un documento', ['Sotto l’omissis,', 'un regalo.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Grattate via le righe nere: sotto c’è quello che offriamo a chi ci chiama per la prima '
                                'volta.')], wm='2-5', ws='1-1'),
               t.col([gratta], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Come vi chiamiamo', 'nome', True, segnaposto='Anche solo un nome'),
             K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('select', 'Quando richiamarvi', 'quando', True, opzioni='Mattina\nPomeriggio\nSera, dopo le 20'),
             K.campo('select', 'Di cosa si tratta', 'cosa', opzioni='Azienda\nFamiglia\nIndagine difensiva\nPreferisco dirlo a voce'),
             K.campo('text', 'Codice, se l’avete', 'codice', param='codice'),
             K.campo('textarea', 'Note', 'note', larghezza='1-1', segnaposto='Solo se volete. Possiamo parlarne a voce.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Un recapito', ['Vi richiamiamo', 'noi.'],
                                'Da un numero riservato, all’ora che scegliete. Se non rispondete, non lasciamo messaggi.',
                                VOCI, campi, 'Ombra & Riserbo — richiesta di contatto', 'Fatevi richiamare',
                                'Ricevuto. Vi richiamiamo all’ora indicata, da un numero riservato.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Corso Genova,', 'terzo piano.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'corso Genova 21, Milano'), ('Metro', 'Sant’Agostino, linea 2'),
                                  ('Orari', 'su appuntamento, anche la sera'), ('Ingresso', 'dal cortile, citofono «R»')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Ombra & Riserbo · agenzia investigativa', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pista fredda', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>è sparita.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Nessuna traccia, nessun testimone. Ricominciamo dall’inizio.', size=17, align='center',
                              color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_NEBBIA, overlay=55), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Studi e professioni', 'Il fascicolo',
    ['investigatore', 'agenzia investigativa', 'milano', 'noir', 'indagini', 'avvocati', 'scuro', 'slider pro'],
    'Un’agenzia investigativa a Milano, nera come una strada bagnata col rosso del filo delle prove: l’apertura sui '
    'fascicoli col timbro «riservato» e l’ora della telecamera, la frase che si legge con la torcia, i servizi su carte '
    'da girare, il lucchetto da aprire con l’indizio nascosto nella pagina e il documento da grattare.',
    [('Servizi', SERVIZI), ('L’agenzia', AGENZIA), ('Dove siamo', CONTATTI + '#arrivare'), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('servizi', 'Servizi', 'servizi'), ('l-agenzia', 'L’agenzia', 'l-agenzia'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Public Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Spectral\', \'Georgia\', serif', '\'Special Elite\', \'Courier New\', monospace',
    ['Spectral', 'Public Sans', 'Special Elite'], corsivo_di=['Spectral'], peso_titoli='400', raggio=0, grana=8,
    titoli_line='1.02', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'l-agenzia.json': build_agenzia(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
