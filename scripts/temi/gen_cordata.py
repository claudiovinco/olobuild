# -*- coding: utf-8 -*-
"""Genera il tema «Cordata» (id cordata): la stazione del Soccorso Alpino delle Grigne, volontari. Scuro, d'emergenza.

Ardesia quasi nera, il rosso del soccorso e il giallo dei segnali. Titoli in Barlow Condensed, testo in Barlow, orari
e numeri in JetBrains Mono.

Apertura: Slider Pro a schede (112 · Di notte · Il volo · Volontari) con transizione a cubo, il «112» enorme solo
contornato, i video della notte con le frontali e dell'elicottero, il titolo che entra dal blocco rosso. Poi i numeri
dell'anno nei cerchi che si riempiono, la catena del soccorso minuto per minuto (Step timeline), lo zaino con le carte
da girare, la raccolta per il fuoristrada nuovo, le regole prima di partire.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'cordata'
NAME = 'Cordata'

HEX = {
    'primary': '#FF4438', 'primary_contrast': '#0C1013', 'secondary': '#2E3A42', 'secondary_contrast': '#ECEFF1',
    'accent': '#FFC531', 'dark': '#06080A', 'light': '#F5F7F8', 'muted': '#141A1F', 'muted_contrast': '#ECEFF1',
    'text': '#ECEFF1', 'text_muted': '#98A2AA', 'background': '#0C1013', 'border': '#253039', 'link': '#FF4438',
}

HOME, INTER, VOLO, SOST = '{{PAGINA:home}}', '{{PAGINA:interventi}}', '{{PAGINA:volontari}}', '{{PAGINA:sostienici}}'
MAIL, PHONE, PHONE_TEL = 'stazione@cordata.example', '0341 000 118', 'tel:+390341000118'
LAT, LNG = '45.9289', '9.3884'

V_ELI, V_VALLE, V_FRONTALI = 'pexels-video-28965702', 'pexels-video-7456210', 'pexels-video-6831478'
V_SCALATA, V_TEMPESTA, V_BAITA = 'pexels-video-2894897', 'pexels-video-19450621', 'pexels-video-30185611'
PHOTOS = [
    'pexels-35019486.jpg', 'pexels-20045916.jpg', 'pexels-31670792.jpg', 'pexels-36180742.jpg', 'pexels-23542313.jpg',
    'pexels-35924789.jpg', 'pexels-18331874.jpg', 'pexels-6831227.jpg', 'pexels-11792447.jpg', 'pexels-38344214.jpg',
    'pexels-38322600.jpg', 'pexels-5916215.jpg', 'pexels-7473239.jpg', 'pexels-30548527.jpg', 'pexels-38377476.jpg',
    'pexels-18614970.jpg', 'pexels-14499644.jpg', 'pexels-31550913.jpg', 'pexels-18331864.jpg', 'pexels-11848388.jpg',
    'pexels-21967608.jpg', 'pexels-36873465.jpg', 'pexels-20183985.jpg', 'pexels-15608719.jpg', 'pexels-15608716.jpg',
]
VIDEOS = [V_ELI, V_VALLE, V_FRONTALI, V_SCALATA, V_TEMPESTA, V_BAITA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='600', corsivo=False)
C = K.c
VOCI = [('phone', 'Emergenza: 112', 'tel:112'), ('map-pin', 'Sede: via ai Resinelli 4, Ballabio (LC)', SOST + '#dove'),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'CORDATA', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'CORDATA', 'Stazione del Soccorso Alpino delle Grigne: 46 volontari, 24 ore su 24, dal 1956. In '
                   'montagna, in grotta e nei boschi, dalla Valsassina al lago.',
                   [('Come interveniamo', INTER), ('Diventa volontario', VOLO), ('Sostienici', SOST),
                    ('Il 5 per mille', SOST + '#dona'), ('Privacy', '/privacy-policy/')],
                   'In caso di incidente', 'Chiama il 112 e di’: «soccorso alpino». Resta dove sei, tieni acceso il telefono, '
                   'fatti vedere coi colori.', VOCI,
                   '© 2026 Cordata · Soccorso Alpino delle Grigne, associazione di volontariato · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=150)


def apertura(t):
    m = K.mob
    occhio = dict(size=13, peso='500', colore=C['acc2'], letterSpacing=3, textTransform='uppercase', fontFamily='JetBrains Mono')
    titolo = dict(size=92, peso='600', colore=C['chiaro'], lineHeight=0.95, letterSpacing=-1, textTransform='uppercase',
                  sfx={'effect': 'blockUp', 'color': C['acc'], 'duration': 800},
                  textShadow={'x': 0, 'y': 2, 'blur': 22, 'color': mix(C['nero'], 60)})
    capitoli = [
        ('112', 'Se succede in montagna', 'Chiama il 112.\nNoi arriviamo.',
         'Di’ «soccorso alpino», dove sei e cosa è successo. Al resto pensiamo noi, a piedi o dal cielo.',
         K.sfondo_foto('pexels-18614970.jpg', overlay=0.45), 'Cosa dire al telefono', INTER + '#chiamata'),
        ('Di notte', 'Ogni ora, ogni stagione', 'Anche di notte,\nanche a Natale.',
         'Un terzo delle nostre uscite comincia dopo il tramonto: frontali, radio e una barella che si porta in sei.',
         K.sfondo_video(V_FRONTALI, overlay=0.3), 'Come interveniamo', INTER),
        ('Il volo', 'Con l’elisoccorso di Como', 'In volo\nin dodici minuti.',
         'Il tecnico del Soccorso Alpino sale a bordo con il medico: verricello, barella, e via.',
         K.sfondo_video(V_ELI, overlay=0.35), 'La catena del soccorso', INTER + '#catena'),
        ('Volontari', 'Il corso parte a marzo', 'Ci serve fiato,\nnon coraggio.',
         'Due anni di formazione, una reperibilità al mese. Se vai già in montagna, sei a metà strada.',
         K.sfondo_foto('pexels-11792447.jpg', overlay=0.4, scala=1.1), 'Diventa volontario', VOLO),
    ]
    sl = []
    for i, (tab, occ, tit, testo, sfondo, cta, url) in enumerate(capitoli):
        n = i + 1
        livelli = []
        if n == 1:
            livelli.append(K.livello('co-n', contenuto='112', x=52, y=12, w=46, size=380, peso='700', colore='transparent',
                                     tag='p', entra='zoomIn', ritardo=100, durata=1200, lineHeight=0.85, letterSpacing=-12,
                                     textStrokeWidth=3, textStrokeColor=C['acc'], opacity=90,
                                     animLoop='pulse', animLoopDuration=2400, animLoopEasing='ease-in-out',
                                     responsive={'mobile': {'fontSize': m(150), 'x': 6, 'y': 58, 'width': 90}}))
        livelli += [
            K.livello('co-o%d' % n, contenuto=occ, x=6, y=30, w=50, ritardo=150, **occhio,
                      responsive={'mobile': {'fontSize': m(11), 'y': 14, 'width': 88}}),
            K.livello('co-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=35, w=55, ritardo=350, durata=900,
                      **titolo, responsive={'mobile': {'fontSize': m(44), 'y': 18, 'width': 88}}),
            K.livello('co-s%d' % n, contenuto=testo, x=6, y=62, w=36, size=18, peso='400', colore=mix(C['chiaro'], 88),
                      ritardo=1000, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 37, 'width': 88}}),
            K.livello('co-b%d' % n, tipo='button', contenuto=cta, x=6, y=77, w='auto', size=12, peso='700',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      ritardo=1300, buttonUrl=url, hover={'scale': 1.04, 'bgColor': C['acc2'], 'duration': 250},
                      responsive={'mobile': {'y': 52, 'fontSize': m(12)}}),
        ]
        sl.append(K.diapositiva('co-%d' % n, sfondo, livelli, durata=7000, tab=tab))
    return K.proslider(t, sl, transizione='cubeH', durata_trans=1000, showDots=False, showTabs=True, tabPosition='bottom',
                       showArrows=False, parallax=True, parallaxIntensity=3)


def cerchio(t, valore, massimo, titolo, colore=None):
    return t.classic('countercircle', {
        'value': valore, 'max_value': massimo, 'suffix': '', 'prefix': '', 'title': titolo, 'size': '220', 'stroke_width': '4',
        'stroke_color': colore or C['acc'], 'track_color': C['filo'], 'text_color': C['testo'], 'title_color': C['tenue'],
        'duration': '2400', 'title_position': 'below',
    })


def catena(t):
    passi = [('0′', 'La chiamata', 'Il 112', 'risponde', 'La centrale chiede dove sei e cosa è successo, e ti tiene al telefono.',
              '1 min', 'In media', 'pexels-15608719.jpg'),
             ('3′', 'L’allerta', 'La stazione', 'si muove', 'Il capo squadra chiama i reperibili: il primo parte entro cinque minuti.',
              '46', 'Volontari', 'pexels-6831227.jpg'),
             ('12′', 'Il volo', 'L’elicottero', 'decolla', 'Se il tempo lo permette, il nostro tecnico sale a bordo con il medico.',
              '12 min', 'Al decollo', 'pexels-36180742.jpg'),
             ('40′', 'Sul posto', 'La barella', 'scende', 'A piedi o col verricello: stabilizzare, scaldare, portare giù.',
              '6', 'Per barella', 'pexels-38344214.jpg')]
    return t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': '',
                   'title': ti, 'title_accent': acc, 'title_accent_italic': False, 'title_after': '', 'title_after_italic': False,
                   'description': d, 'footer_value': fv, 'footer_label': fl, 'separator_text': '→'}
                  for c, tag, ti, acc, d, fv, fl, f in passi],
        'show_timeline': True, 'timeline_line_color': mix(C['chiaro'], 18), 'timeline_dot_color': C['acc'],
        'timeline_dot_size': 12, 'timeline_height': 2, 'timeline_margin_bottom': 50, 'counter_font_family': 'heading',
        'counter_size': 84, 'counter_color': C['acc'], 'counter_italic': False, 'counter_weight': '600', 'tag_size': 12,
        'tag_color': C['chiaro'], 'media_aspect_ratio': '4/3', 'media_radius': radius(2), 'columns': 4, 'gap': 24,
    })


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.one([K.sechead(t, '01 — Il 2025', ['Trecentodiciotto', 'uscite in un anno.'], size=72,
                         tagline='Escursionisti, sciatori, cercatori di funghi, speleologi. E un cane, l’anno scorso, rimasto '
                                 'su una cengia del Grignone.')]),
        t.row([t.col([cerchio(t, '318', '400', 'Interventi', C['acc'])], wm='1-3', ws='1-1', width_default='1-1'),
               t.col([cerchio(t, '342', '400', 'Persone soccorse', C['acc2'])], wm='1-3', ws='1-2', width_default='1-2'),
               t.col([cerchio(t, '96', '100', 'Interventi notturni su cento', C['chiaro'])], wm='1-3', ws='1-2', width_default='1-2')],
              layout='33-33-33', gap=24, valign='center'),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 110}))

    out.append(t.section([
        t.one([K.sechead(t, '02 — La catena del soccorso', ['Dalla chiamata', 'alla barella.'], size=72,
                         tagline='Quaranta minuti, in media, dalla tua telefonata al primo soccorritore accanto a te.')]),
        t.one([catena(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='catena', style={'padding_top': 140, 'padding_bottom': 120}))

    out.append(K.video_frase(t, V_TEMPESTA, 'Grignone · 2.410 metri · gennaio', 'Il meteo cambia <em>in venti minuti.</em>',
                             'La metà dei nostri interventi d’inverno comincia con un temporale di neve che nessuno aveva '
                             'visto arrivare. Guarda il bollettino, e torna indietro presto.', overlay=40))

    out.append(t.section([
        t.row([t.col([K.label(t, '03 — Prima di partire', color=C['acc']),
                      K.headline(t, 'Cinque regole <em>che ci risparmiano</em> un’uscita.', size=56, tag='h2', pad_bottom=16),
                      K.text(t, 'Le ripetiamo a ogni serata nelle scuole e nei CAI della valle. Le sappiamo a memoria, '
                                'eppure le infrangiamo anche noi.')], wm='2-5', ws='1-1'),
               t.col([K.faq(t, [
                   ('Lascia detto dove vai', 'Itinerario e ora del ritorno a qualcuno che resta a valle. Se non torni, sa '
                                             'dove mandarci.'),
                   ('Batteria carica e powerbank', 'Il telefono è la tua radio: col GPS acceso ti troviamo in metà tempo.'),
                   ('Il bollettino della sera prima', 'Neve, vento e temporali sulle Grigne cambiano in un’ora.'),
                   ('Un capo colorato', 'Dall’elicottero un giubbotto nero sul ghiaione non si vede.'),
                   ('Torna indietro', 'La vetta resta lì. Si torna per pranzo, non per la foto.'),
               ], 'reg')], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='start'),
    ], padding='large', style={'padding_top': 130, 'padding_bottom': 120}))

    voce = t.classic('testimonial', {
        'quote': 'Mi sono rotto la caviglia sotto la Cresta Segantini, di notte. <em>Ho visto le loro frontali salire, una '
                 'dopo l’altra.</em> Non dimentico quella fila di luci.',
        'author_name': 'Andrea P.', 'author_role': 'soccorso il 3 febbraio 2026', 'rating': '5', 'text_color': C['testo'],
        'star_color': C['acc2'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': C['acc'],
    })
    out.append(t.section([K.centered(t, [voce])], bg=solid(C['sala']), padding='large',
                         style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, 'pexels-30548527.jpg', '04 — Le unità cinofile', 'Due cani, mille metri di bosco in un’ora.',
                          'Kira e Bruno cercano per fiuto anche sotto mezzo metro di neve. Si addestrano ogni sabato con i '
                          'loro conduttori, e ogni tanto con le scuole.', 'Conoscili', VOLO + '#cani', overlay=55, effetto=''))
    return page(out)


def build_interventi():
    t = Tpl(ID, 'interventi')
    out = [K.intro(t, 'Come interveniamo', ['A piedi, in volo,', 'sottoterra.'],
                   'Ogni intervento parte dal 112. Dalla centrale arriva la chiamata al capo squadra, che decide chi parte e '
                   'come. Il resto lo fanno le gambe, le corde e l’esperienza.', bg=K.photo_bg('pexels-35924789.jpg', overlay=60),
                   overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La chiamata', ['Cosa dire', 'al telefono.'], size=60, layout='stack', size_mobile=40),
                      K.text(t, 'Respira e parla piano. Le domande sono sempre le stesse: aiutaci a risponderle in fretta.')],
                     wm='2-5', ws='1-1'),
               t.col([K.specs(t, [('Chi sei', 'nome e numero da cui chiami'), ('Dove sei', 'sentiero, quota, cosa vedi intorno; '
                                   'le coordinate dal telefono'), ('Cosa è successo', 'caduta, malore, sperso, bloccato'),
                                  ('Quante persone', 'e come stanno: coscienti, respirano, sanguinano'),
                                  ('Il tempo', 'nebbia, vento, neve: decide se si vola')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='start'),
    ], padding='large', html_id='chiamata', style={'padding_top': 120, 'padding_bottom': 110}))
    for foto, ey, ti, tx, video in [
        (V_VALLE, 'In volo', 'Il verricello, in parete.', 'Con l’elisoccorso di Como portiamo giù chi è ferito dove la barella '
         'non passa. Il nostro tecnico scende col cavo, aggancia, risale.', True),
        ('pexels-38322600.jpg', 'Nei boschi', 'Le ricerche, palmo a palmo.', 'Chi si perde cercando funghi lo troviamo quasi '
         'sempre di notte: squadre a pettine, cani, droni con la termocamera.', False),
        (V_SCALATA, 'In parete', 'Corde, soste, pazienza.', 'Sulla roccia delle Grigne il recupero si fa come una salita al '
         'contrario: una sosta alla volta, con il ferito legato alla barella.', True),
    ]:
        out.append(K.pannello(t, foto, ey, ti, tx, overlay=55, video=video))
    out.append(K.domande(t, 'Quanto', 'costa?', [
        ('Il soccorso si paga?', 'Il soccorso sanitario no. Se chiami senza essere ferito o in pericolo, la Regione può chiedere '
                                 'un contributo per l’elicottero.'),
        ('Siete pagati?', 'No: siamo volontari. Le spese le coprono la Regione, le donazioni e il 5 per mille.'),
        ('Venite anche per un cane?', 'Sì, se il proprietario rischia per recuperarlo. Meglio chiamarci prima.'),
        ('Fate corsi?', 'Ogni autunno, gratis, nelle scuole e nelle sezioni CAI della provincia.'),
    ], 'int', nota='Per tutto il resto scrivici: rispondiamo la sera.'))
    return out


def build_volontari():
    t = Tpl(ID, 'volontari')
    out = [K.intro(t, 'Diventa volontario', ['Due anni', 'per imparare.'],
                   'Il corso parte a marzo: roccia, neve, barella, primo soccorso, radio. Poi un tirocinio con la squadra e la '
                   'prima reperibilità. Non serve essere guide: serve andare in montagna, spesso.',
                   bg=K.photo_bg('pexels-18331864.jpg', overlay=55), overlay_text=True)]

    def carta(foto, fronte, sotto, retro, testo):
        return t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center',
                            'overlay_color': C['nero'], 'overlay_opacity': 45},
            'back_media': {'type': 'none'}, 'front_title': fronte, 'front_description': sotto, 'front_text_color': C['chiaro'],
            'front_text_align': 'left', 'front_valign': 'bottom', 'back_title': retro, 'back_description': testo,
            'back_bg': C['sala'], 'back_text_color': C['testo'], 'back_text_align': 'left', 'back_valign': 'center',
            'back_cta_text': '', 'back_cta_url': '',
        })
    out.append(t.section([
        t.one([K.sechead(t, 'Lo zaino della reperibilità', ['Sempre pronto,', 'sempre pesante.'], size=60,
                         tagline='Tocca una carta: c’è quello che portiamo e perché. Sedici chili, in media.')]),
        t.row([t.col([carta('pexels-5916215.jpg', 'Corde e moschettoni', 'La tecnica', '60 metri, 12 moschettoni',
                            'La corda da soccorso, il discensore, le fettucce per le soste. Tutto controllato ogni mese.')],
                     wm='1-4', ws='1-2', width_default='1-2'),
               t.col([carta('pexels-35924789.jpg', 'Frontale e radio', 'La notte', 'Due frontali, una radio',
                            'La seconda luce è per il ferito. La radio ci tiene in contatto con la centrale e l’elicottero.')],
                     wm='1-4', ws='1-2', width_default='1-2'),
               t.col([carta('pexels-21967608.jpg', 'Il telo termico', 'Il freddo', 'Pesa 50 grammi',
                            'Chi è fermo da ore ha più freddo che male. Il telo e il tè caldo vengono prima della barella.')],
                     wm='1-4', ws='1-2', width_default='1-2'),
               t.col([carta('pexels-7473239.jpg', 'Il cane', 'Il fiuto', 'Kira, pastore tedesco',
                            'Brevettata nel 2023. Cerca chi è sotto la neve o perso nel bosco, anche di notte.')],
                     wm='1-4', ws='1-2', width_default='1-2')],
              layout='25-25-25-25', gap=20, valign='start'),
    ], padding='large', html_id='cani', style={'padding_top': 60}))

    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono', True), K.campo('text', 'Età', 'eta'),
             K.campo('select', 'Cosa fai in montagna', 'esperienza', True,
                     opzioni='Escursioni\nAlpinismo e roccia\nSci alpinismo\nSpeleologia\nSono medico o infermiere'),
             K.campo('select', 'Abiti in valle?', 'zona', opzioni='Valsassina\nLecco e lago\nAltrove, entro un’ora'),
             K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1', segnaposto='Le tue uscite, i tuoi corsi.')]
    out.append(K.sezione_modulo(t, 'iscriviti', 'La candidatura', ['Scrivici', 'entro gennaio.'],
                                'A febbraio facciamo una giornata di prova sulla neve e una in falesia. Poi decidiamo insieme.',
                                VOCI, campi, 'Cordata — candidatura volontario', 'Mando la candidatura',
                                'Ricevuta. Ti chiamiamo a gennaio per la giornata di prova.'))
    return out


def build_sostienici():
    t = Tpl(ID, 'sostienici')
    out = [K.intro(t, 'Sostienici', ['Il fuoristrada', 'ha vent’anni.'],
                   'Ci serve un mezzo nuovo per portare barelle e squadre fino all’attacco dei sentieri, anche con la neve. '
                   'Ogni euro va lì, e il nome di chi dona va sulla fiancata.', bg=K.photo_bg('pexels-36873465.jpg', overlay=55),
                   overlay_text=True)]
    barre = t.classic('progress', {
        'bars': 'Fuoristrada 4×4 attrezzato|62\nBarella a ruota per i sentieri|100\nDue termocamere per il drone|35',
        'bar_color': C['acc'], 'bar_bg': mix(C['acc'], 14), 'text_color': C['testo'], 'height': '10', 'show_percentage': True,
        'animated': True, 'layout': 'bar', 'animate_counter': True, 'animation_duration': '1800', 'border_radius': '2',
    })
    out.append(t.section([
        t.row([t.col([K.label(t, 'La raccolta 2026', color=C['acc']),
                      K.headline(t, '48.000 euro, <em>ne mancano 18.200.</em>', size=56, tag='h2', pad_bottom=16),
                      K.text(t, 'La barella a ruota è già arrivata, grazie a 140 persone. Ora il fuoristrada.'),
                      K.button(t, 'Dona adesso', SOST + '#dona', kind='pieno', pad_top=16)], wm='1-2', ws='1-1'),
               t.col([barre, K.text(t, 'Aggiornato il 28 ottobre 2026', size=13, pad=(12, 0, 0, 0))], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come donare', ['Tre modi,', 'tutti detraibili.'], size=56, layout='stack', size_mobile=38)],
                     wm='1-3', ws='1-1'),
               t.col([K.specs(t, [('Bonifico', 'IT00 X000 0000 0000 0000 0000 000 · causale «fuoristrada»'),
                                  ('5 per mille', 'codice fiscale 00000000000 nella dichiarazione dei redditi'),
                                  ('Di persona', 'in sede, il giovedì sera dalle 20.30'),
                                  ('Ricevuta', 'per la detrazione, la mandiamo entro un mese')])], wm='2-3', ws='1-1')],
              layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', html_id='dona'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La sede', ['Ai piedi', 'dei Resinelli.'], size=48, layout='stack', size_mobile=34),
                      K.text(t, 'Il giovedì sera ci trovi in sede per l’allenamento con le barelle: vieni a vedere.'),
                      K.contacts(t, VOCI)], wm='1-3', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Cordata · sede della stazione', hex_marker=HEX['primary'], zoom='13')],
                     wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], padding='large', html_id='dove'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori sentiero', color=C['acc'], align='center'),
                       K.headline(t, 'Ti sei <em>perso.</em>', size=120, tag='h1', align='center', pad_bottom=20, spacing=-2),
                       K.text(t, 'Resta dove sei: torniamo all’inizio insieme.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BAITA, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Associazioni e cause', 'Sali con la squadra',
    ['soccorso alpino', 'volontariato', 'montagna', 'associazione', 'raccolta fondi', 'scuro', 'slider pro'],
    'La stazione del Soccorso Alpino delle Grigne, ardesia e rosso d’emergenza: l’apertura a schede col 112 enorme e le '
    'transizioni a cubo, i numeri dell’anno nei cerchi, la catena del soccorso minuto per minuto, lo zaino con le carte '
    'da girare, le regole prima di partire, la candidatura dei volontari e la raccolta per il fuoristrada.',
    [('Come interveniamo', INTER), ('Volontari', VOLO), ('Sostienici', SOST), ('Dona', SOST + '#dona')],
    [('home', 'Home', None), ('interventi', 'Come interveniamo', 'interventi'), ('volontari', 'Diventa volontario', 'volontari'),
     ('sostienici', 'Sostienici', 'sostienici')],
    HEX, '\'Barlow\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Barlow Condensed\', \'Arial Narrow\', Arial, sans-serif', '\'JetBrains Mono\', ui-monospace, Menlo, monospace',
    ['Barlow Condensed', 'Barlow', 'JetBrains Mono'], peso_titoli='600', raggio=2, grana=4, titoli_line='0.98',
    titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'interventi.json': build_interventi(), 'volontari.json': build_volontari(),
                        'sostienici.json': build_sostienici(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
