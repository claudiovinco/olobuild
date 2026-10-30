# -*- coding: utf-8 -*-
"""Genera il tema «Controtempo» (id controtempo): una scuola di danza a Padova, in un ex cinema di quartiere con tre
sale e il palco vero. Classico, contemporaneo, tango e hip hop, dai quattro anni agli ottanta; a giugno il saggio al
Teatro Verdi.

Tema scuro, nero di palcoscenico col fucsia e il viola delle luci di scena e il caldo dell’occhio di bue. Titoli in Bricolage Grotesque,
testo in Plus Jakarta Sans, orari in Martian Mono.

Apertura: Slider Pro che balla. Quattro corsi in video (le ballerine di luce, le ombre viola, il tango sul palco, l’hip
hop al neon); il nome del corso è enorme e le sue lettere non smettono di muoversi, avanti e indietro, sopra la loro
ombra contornata. Un occhio di bue segue il mouse sulla scena, e in un angolo l’insegnante conta: cinque, sei, sette,
otto. Si passa da un corso all’altro girando, come in una piroetta.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'controtempo'
NAME = 'Controtempo'

HEX = {
    'primary': '#FF5FA8', 'primary_contrast': '#0B0A10', 'secondary': '#8A73FF', 'secondary_contrast': '#0B0A10',
    'accent': '#FFD8A3', 'dark': '#050408', 'light': '#FFF6FB', 'muted': '#16141E', 'muted_contrast': '#F4EEF7',
    'text': '#F4EEF7', 'text_muted': '#A79FB3', 'background': '#0B0A10', 'border': '#2A2636', 'link': '#FF5FA8',
}

HOME, CORSI, INSEGNANTI, SAGGIO = '{{PAGINA:home}}', '{{PAGINA:i-corsi}}', '{{PAGINA:gli-insegnanti}}', '{{PAGINA:il-saggio}}'
MAIL, PHONE, PHONE_TEL = 'iscrizioni@controtempo.example', '+39 049 000 5678', 'tel:+390490005678'
LAT, LNG = '45.4085', '11.8862'

V_LUCE, V_OMBRE, V_NEON, V_TANGO, V_STREET, V_GRUPPO, V_FINESTRA, V_PIEDI = (
    'pexels-video-3578236', 'pexels-video-6982944', 'pexels-video-8462453', 'pexels-video-31537692',
    'pexels-video-8688591', 'pexels-video-6939798', 'pexels-video-5111183', 'pexels-video-8281158')
PHOTOS = [
    'pexels-8820330.jpg', 'pexels-5135111.jpg', 'pexels-24408966.jpg', 'pexels-5135109.jpg', 'pexels-8462760.jpg',
    'pexels-14605645.jpg', 'pexels-24408964.jpg', 'pexels-8935886.jpg', 'pexels-5254983.jpg', 'pexels-5255117.jpg',
    'pexels-5254988.jpg', 'pexels-19458612.jpg', 'pexels-8285998.jpg', 'pexels-9176793.jpg', 'pexels-8973443.jpg',
    'pexels-33005056.jpg', 'pexels-27352253.jpg', 'pexels-23775850.jpg', 'pexels-6221592.jpg', 'pexels-7667552.jpg',
    'pexels-2188012.jpg', 'pexels-38787638.jpg', 'pexels-38787648.jpg', 'pexels-30102478.jpg', 'pexels-3901637.jpg',
    'pexels-6715772.jpg', 'pexels-3902512.jpg', 'pexels-3902517.jpg', 'pexels-6715562.jpg', 'pexels-6717369.jpg',
    'pexels-5635570.jpg', 'pexels-12092991.jpg', 'pexels-13312280.jpg', 'pexels-2019327.jpg', 'pexels-7991568.jpg',
    'pexels-31241090.jpg', 'pexels-8933836.jpg', 'pexels-3901636.jpg', 'pexels-19128473.jpg', 'pexels-30718161.jpg',
    'pexels-30133725.jpg',
]
VIDEOS = [V_LUCE, V_OMBRE, V_NEON, V_TANGO, V_STREET, V_GRUPPO, V_FINESTRA, V_PIEDI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
TITOLI, MONO = 'Bricolage Grotesque', 'Martian Mono'
VOCI = [('map-pin', 'Via Belzoni 64 · Padova', CORSI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Controtempo', logo_size='19')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Controtempo', 'Scuola di danza in un ex cinema di quartiere a Padova, dal 2001. Tre sale col '
                   'parquet flottante, il palco vero, e a giugno il saggio al Teatro Verdi.',
                   [('I corsi', CORSI), ('Gli insegnanti', INSEGNANTI), ('Il saggio', SAGGIO),
                    ('Dove siamo', CORSI + '#arrivare'), ('Privacy', '/privacy-policy/')],
                   'La segreteria', 'Lunedì — venerdì 15 — 21<br>Sabato 9 — 13<br>Prova gratuita tutto l’anno', VOCI,
                   '© 2026 Controtempo associazione sportiva dilettantistica · Padova · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com'), ('youtube', 'https://youtube.com')],
                   marchio_size=130)


def apertura(t):
    m = K.mob
    corsi = [
        ('Corso 01 · dai 6 anni', 'Classico', 'Sbarra, centro, punte dal terzo anno. Il metodo della Royal Academy, '
         'gli esami a maggio.', 'Lun · mer · ven 17:00', V_LUCE),
        ('Corso 02 · dai 12 anni', 'Contemporaneo', 'Il pavimento, il peso, il respiro. Si improvvisa e si compone, '
         'ogni anno una coreografia nuova.', 'Mar · gio 18:30', V_OMBRE),
        ('Corso 03 · per adulti', 'Tango', 'L’abbraccio, la camminata, l’ascolto. Si viene anche da soli: le coppie '
         'cambiano ogni dieci minuti.', 'Venerdì 21:00', V_TANGO),
        ('Corso 04 · dagli 11 anni', 'Hip hop', 'Groove, popping, freestyle. Si balla in cerchio, si sfida in cerchio, '
         'si cresce in cerchio.', 'Mar · gio 17:00 · sab 11:00', V_STREET),
    ]
    sl = []
    for i, (occ, nome, testo, quando, video) in enumerate(corsi):
        n = i + 1
        # le lettere restano piene e ondeggiano: salgono, scendono e si piegano appena, senza fine
        lettere = {'stagger': 45, 'offsetY': 22, 'rotation': 5, 'blur': 0, 'opacity': 100, 'yoyo': True}
        grande = min(150, int(1300 / (len(nome) * 0.64)))
        piccolo = min(m(64), int(560 / (len(nome) * 0.64)))
        sl.append(K.diapositiva('co-%d' % n, K.sfondo_video(video, overlay=0.42), [
            K.livello('co-o%d' % n, contenuto=occ, x=5.5, y=26, w=40, size=13, peso='500', colore=C['acc'], letterSpacing=2,
                      textTransform='uppercase', fontFamily=MONO, ritardo=200,
                      responsive={'mobile': {'fontSize': m(11), 'y': 36, 'x': 7, 'width': 86}}),
            # l’ombra contornata, ferma, e sopra le lettere che ballano
            K.livello('co-w%d' % n, contenuto=nome, tag='p', x=6.1, y=31, w=92, size=grande, peso='800', colore='transparent',
                      fontFamily=TITOLI, lineHeight=0.95, textStrokeWidth=2, textStrokeColor=mix(C['acc'], 75),
                      entra='fadeIn', ritardo=300, durata=1400, letterSpacing=-2,
                      responsive={'mobile': {'fontSize': piccolo, 'y': 39.4, 'x': 7.6, 'width': 90}}),
            K.livello('co-t%d' % n, contenuto=nome, tag='h1' if n == 1 else 'h2', x=5.5, y=30, w=92, size=grande, peso='800',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=0.95, entra='fadeIn', ritardo=400, durata=1200,
                      letterSpacing=-2, charAnim=lettere,
                      responsive={'mobile': {'fontSize': piccolo, 'y': 39, 'x': 7, 'width': 90}}),
            K.livello('co-s%d' % n, contenuto=testo, x=5.5, y=53, w=34, size=18, peso='400', colore=mix(C['chiaro'], 88),
                      lineHeight=1.55, ritardo=1200, responsive={'mobile': {'fontSize': m(15), 'y': 49, 'x': 7, 'width': 86}}),
            K.livello('co-q%d' % n, contenuto=quando, x=5.5, y=66, w='auto', size=13, peso='500', colore=C['chiaro'],
                      bgColor=mix(C['nero'], 55), padding=10, borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 30),
                      fontFamily=MONO, backdropBlur=8, ritardo=1450, entra='popUpBack', durata=700,
                      responsive={'mobile': {'fontSize': m(11), 'y': 59.5, 'x': 7}}),
            K.livello('co-b%d' % n, tipo='button', contenuto='Prova una lezione', x=5.5, y=74, w='auto', size=13, peso='700',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=999, ritardo=1650, buttonUrl=CORSI + '#iscriviti',
                      hover={'bgColor': C['acc2'], 'scale': 1.04, 'duration': 250},
                      responsive={'mobile': {'y': 65.5, 'x': 7, 'fontSize': m(12)}}),
        ], durata=7500))
    # l’occhio di bue che segue il mouse
    globali = [K.livello('co-luce', tipo='shape', x=48, y=8, w=34, h=54, bgColor=mix(C['acc2'], 9), borderRadius=999,
                         blendMode='screen', parallaxDepth=18, animLoop='wander', animLoopDuration=16000, entra='fadeIn',
                         ritardo=600, durata=2000,
                         boxShadow={'x': 0, 'y': 0, 'blur': 160, 'spread': 70, 'color': mix(C['acc2'], 14)},
                         responsive={'mobile': {'visible': False}})]
    # cinque, sei, sette, otto: un battito ogni mezzo secondo
    for k, num in enumerate(['5', '6', '7', '8']):
        kfs = []
        for tempo, opac in [(0, 0), (k * 500, 0), (k * 500 + 60, 100), (k * 500 + 440, 100), (k * 500 + 500, 0), (2000, 0)]:
            if kfs and kfs[-1]['time'] == tempo:
                kfs[-1]['props']['opacity'] = opac
                continue
            kfs.append({'time': tempo, 'props': {'x': 88, 'y': 70, 'scale': 1.15 if opac and tempo == k * 500 + 60 else 1,
                                                 'opacity': opac}, 'easing': 'linear'})
        globali.append(K.livello('co-n%d' % k, contenuto=num, x=88, y=70, w=7, size=96, peso='800', colore=C['acc'],
                                 align='center', fontFamily=TITOLI, lineHeight=1,
                                 timeline={'duration': 2000, 'delay': 0, 'loop': True, 'keyframes': kfs},
                                 responsive={'mobile': {'x': 76, 'y': 11.5, 'width': 18, 'fontSize': m(54)}}))
    globali.append(K.livello('co-n-l', contenuto='e cinque, sei, sette, otto', x=80, y=82.5, w=16, size=11, peso='500',
                             colore=mix(C['chiaro'], 70), align='center', fontFamily=MONO, entra='fadeIn', ritardo=800,
                             responsive={'mobile': {'visible': False}}))
    return K.proslider(t, sl, globali=globali, transizione='rotateSlide', durata_trans=1100, showArrows=True,
                       arrowStyle='rounded', showDots=True, dotStyle='circles', parallax=True, parallaxIntensity=8,
                       autoplaySpeed=7500)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    saggio = t.classic('montaggio', {
        'clip': [{'titolo': ti, 'tipo': tipo, 'cliente': chi, 'anno': '2026', 'video': K.img(v + '.mp4'),
                  'immagine': K.img(v + '.jpg'), 'durata': d, 'link': CORSI + '#' + a, 'testo_link': 'Il corso', 'colore': ''}
                 for ti, tipo, chi, v, d, a in [
                     ('Lago di luce', 'Classico · quinto anno', 'Coreografia di Mei Lanzoni', V_LUCE, 10, 'classico'),
                     ('Ombre', 'Contemporaneo · avanzato', 'Coreografia di Elisa Ferraro', V_OMBRE, 10, 'contemporaneo'),
                     ('Neon', 'Contemporaneo · ragazzi', 'Coreografia di Elisa Ferraro', V_NEON, 8, 'contemporaneo'),
                     ('Milonga', 'Tango · adulti', 'Coreografia di Andrea Costa', V_TANGO, 10, 'tango'),
                     ('Cypher', 'Hip hop · crew', 'Coreografia di Samuele Ricci', V_STREET, 9, 'hiphop')]],
        'nome_sequenza': 'SAGGIO_2026_teatro_verdi', 'fps': 25, 'parti_da_sola': True, 'mostra_audio': True,
        'mostra_info': True, 'colore_fondo': C['sala'], 'colore_testo': C['testo'], 'colore_testina': C['acc'],
        'colore_clip': mix(C['acc2'], 30, C['nero']), 'colore_audio': mix(C['acc'], 70),
        'titolo_font_family': 'heading', 'titolo_font_size': 44, 'titolo_font_size_mobile': 30, 'titolo_font_weight': '700',
        'titolo_color': C['testo'], 'raggio': radius(14), 'proporzione': '21/9', 'altezza_traccia': 58,
        'posizione_info': 'lato'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il saggio 2026', ['Cinque coreografie,', 'centoventi allievi.'], size=60, layout='stack',
                                size_mobile=36)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Il montaggio del saggio di giugno al Teatro Verdi: premete play e scorrete la timeline, '
                                'come in sala di montaggio. Ogni clip porta al suo corso.', size=17, color=C['testo'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=64, valign='end'),
        t.one([saggio], style={'margin_top': 48}),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 130}))
    lista = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': CORSI + '#' + a,
                   'color': col} for k, (n, s, d, f, a, col) in enumerate([
                       ('Propedeutica', 'Dai 4 ai 6 anni', 'Il ritmo, lo spazio, il gioco: la prima danza.', 'pexels-6717369.jpg',
                        'classico', C['acc2']),
                       ('Classico', 'Dai 6 anni · 8 livelli', 'Sbarra, centro, punte, gli esami di fine anno.', 'pexels-5254988.jpg',
                        'classico', C['acc']),
                       ('Contemporaneo', 'Dai 12 anni', 'Il pavimento, il peso, l’improvvisazione.', 'pexels-6221592.jpg',
                        'contemporaneo', C['sec']),
                       ('Hip hop', 'Dagli 11 anni', 'Groove, popping, freestyle e la crew delle gare.', 'pexels-8973443.jpg',
                        'hiphop', mix(C['acc'], 60, C['sec'])),
                       ('Tango', 'Adulti, anche da soli', 'L’abbraccio, la camminata, la milonga del venerdì.',
                        'pexels-2188012.jpg', 'tango', C['acc']),
                       ('Danza over 60', 'Il martedì mattina', 'Equilibrio, memoria, e tanta musica.', 'pexels-3901637.jpg',
                        'classico', C['acc2'])])],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': C['tenue'],
        'number_hover_color': C['acc'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 40, 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['acc2'], 'sub_size': 12,
        'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 26, 'hover_indent': 28,
        'hover_bg': mix(C['acc'], 6), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image', 'peek_width': 220,
        'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'I corsi', ['Dai quattro anni', 'agli ottanta.'], size=64, layout='stack', size_mobile=40),
                      K.text(t, 'Passate sopra un corso: compare una lezione vera. La prima lezione è gratis, per tutti.'),
                      K.button(t, 'L’orario della settimana', CORSI + '#orario', pad_top=18)], wm='1-3', ws='1-1'),
               t.col([lista], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['nero']), padding='large', width='large', html_id='corsi', style={'padding_top': 150, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_FINESTRA, 'Il palco vero', 'Si prova <em>come in teatro.</em>',
                             'L’ex cinema ha tenuto il suo palco: le luci, le quinte, il sipario. Ogni allievo ci sale almeno '
                             'due volte l’anno, prima del saggio.', overlay=45))
    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2027-06-12T20:30', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': False, 'expired_message': 'Il sipario si è alzato!', 'label_days': 'giorni',
        'label_hours': 'ore', 'label_minutes': 'minuti', 'text_color': C['chiaro'], 'accent_color': C['acc'],
        'number_font_size': 88, 'number_font_weight': '800', 'label_font_size': '13', 'item_min_width': 160,
        'item_bg_color': mix(C['nero'], 60), 'item_radius': {'tl': 999, 'tr': 999, 'br': 14, 'bl': 14},
        'item_padding': {'top': 46, 'right': 22, 'bottom': 22, 'left': 22}}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Il saggio 2027', ['Teatro Verdi,', '12 giugno, ore 20:30.'], size=60, align='center', chiaro=True)]),
        t.one([conto]),
        t.one([K.button(t, 'Scegli il posto', SAGGIO + '#posti', kind='pieno', align='center', pad_top=30)]),
    ], bg=K.photo_bg('pexels-12092991.jpg', overlay=62), padding='large', width='large',
        style={'padding_top': 140, 'padding_bottom': 140}))
    out.append(K.pannello(t, V_PIEDI, 'Il venerdì sera', 'La milonga <em>è aperta a tutti.</em>',
                          'Dopo il corso di tango la sala grande diventa una milonga: luci basse, il bandoneón, si invita '
                          'con lo sguardo. Ingresso libero per gli allievi.', 'Il corso di tango', CORSI + '#tango',
                          overlay=55, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Controtempo',
                page_intro_caption='Scuola di danza · Padova', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def build_corsi():
    t = Tpl(ID, 'i-corsi')
    out = [K.intro(t, 'I corsi', ['Ventotto lezioni', 'a settimana.'],
                   'Tre sale, otto insegnanti, dal pomeriggio alla sera e il sabato mattina. Filtrate per corso o livello: '
                   'l’orario mostra solo quello che vi serve, e oggi è acceso.',
                   bg=K.photo_bg('pexels-6715772.jpg', overlay=58), overlay_text=True)]
    corsi = [('propedeutica', 'Propedeutica', C['acc2'], 'Il ritmo e il gioco, dai 4 anni.', 45),
             ('classico', 'Classico', C['acc'], 'Sbarra, centro, punte.', 75),
             ('contemporaneo', 'Contemporaneo', C['sec'], 'Pavimento, peso, improvvisazione.', 75),
             ('hiphop', 'Hip hop', mix(C['acc'], 60, C['sec']), 'Groove, popping, freestyle.', 60),
             ('tango', 'Tango', mix(C['acc'], 55, C['acc2']), 'L’abbraccio e la camminata.', 90),
             ('over60', 'Danza over 60', mix(C['sec'], 60, C['acc2']), 'Equilibrio e musica.', 60)]
    piano = [
        (1, '16:00', '16:45', 'propedeutica', 'Mei Lanzoni', 'Sala Rossa', 'base'), (1, '17:00', '18:15', 'classico', 'Mei Lanzoni', 'Sala Rossa', 'base'),
        (1, '18:30', '19:45', 'classico', 'Mei Lanzoni', 'Sala Rossa', 'avanzato'), (1, '19:00', '20:15', 'contemporaneo', 'Elisa Ferraro', 'Palco', 'intermedio'),
        (2, '10:00', '11:00', 'over60', 'Mei Lanzoni', 'Sala Blu', 'tutti'), (2, '17:00', '18:00', 'hiphop', 'Samuele Ricci', 'Sala Blu', 'base'),
        (2, '18:30', '19:45', 'contemporaneo', 'Elisa Ferraro', 'Palco', 'avanzato'), (2, '20:00', '21:00', 'hiphop', 'Samuele Ricci', 'Sala Blu', 'avanzato'),
        (3, '16:00', '16:45', 'propedeutica', 'Mei Lanzoni', 'Sala Rossa', 'base'), (3, '17:00', '18:15', 'classico', 'Mei Lanzoni', 'Sala Rossa', 'intermedio'),
        (3, '19:00', '20:15', 'contemporaneo', 'Elisa Ferraro', 'Palco', 'base'),
        (4, '17:00', '18:00', 'hiphop', 'Samuele Ricci', 'Sala Blu', 'intermedio'), (4, '18:30', '19:45', 'contemporaneo', 'Elisa Ferraro', 'Palco', 'intermedio'),
        (4, '20:00', '21:30', 'tango', 'Andrea Costa', 'Palco', 'base'),
        (5, '17:00', '18:15', 'classico', 'Mei Lanzoni', 'Sala Rossa', 'base'), (5, '18:30', '19:45', 'classico', 'Mei Lanzoni', 'Sala Rossa', 'avanzato'),
        (5, '21:00', '22:30', 'tango', 'Andrea Costa', 'Palco', 'tutti'),
        (6, '10:00', '10:45', 'propedeutica', 'Mei Lanzoni', 'Sala Rossa', 'base'), (6, '11:00', '12:00', 'hiphop', 'Samuele Ricci', 'Sala Blu', 'tutti')]
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'co-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': ins, 'sala': sala,
                     'livello': liv, 'posti': 16, 'nota': ''} for k, (g, a, b, c, ins, sala, liv) in enumerate(piano)],
        'giorni': 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato', 'ora_inizio': '10:00', 'ora_fine': '22:30',
        'mostra_filtri': True, 'etichetta_tutti': 'Tutti', 'filtro_livello': True, 'filtro_istruttore': True,
        'testo_vuoto': 'Nessuna lezione con questi filtri: togliene uno.', 'testo_prenota': 'Prova gratis',
        'link_prenota': CORSI + '?corso={corso}&giorno={giorno}&ora={ora}#iscriviti', 'colore_oggi': C['acc'],
        'colore_adesso': C['acc2'], 'giorno_font_family': 'heading', 'giorno_font_size': 20, 'giorno_font_weight': '700',
        'giorno_uppercase': True, 'corso_font_family': 'heading', 'corso_font_size': 17, 'corso_font_weight': '700',
        'ora_font_family': 'var(--olo-font-family-mono)', 'raggio_lezione': radius(10), 'altezza_ora': 58, 'gap': 4,
        'forma_filtri': 'pillola'}, style={})
    out.append(t.section([t.one([orario])], padding='large', width='large', html_id='orario',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    for k, (aid, foto, nome, chi, testo, dati) in enumerate([
        ('classico', 'pexels-5254988.jpg', 'Classico', 'Mei Lanzoni · dal 2006', 'Otto livelli, dalla propedeutica alle '
         'punte. Il metodo della Royal Academy, gli esami a maggio con la commissione esterna.',
         [('Età', 'dai 6 anni'), ('Lezioni', 'da 2 a 4 a settimana'), ('Divisa', 'body nero, calze rosa')]),
        ('contemporaneo', 'pexels-6221592.jpg', 'Contemporaneo', 'Elisa Ferraro · dal 2012', 'Il pavimento, il peso, la '
         'relazione con gli altri. Ogni anno una coreografia nuova, nata dalle improvvisazioni del gruppo.',
         [('Età', 'dai 12 anni'), ('Lezioni', '2 a settimana'), ('Sala', 'il palco')]),
        ('hiphop', 'pexels-8973443.jpg', 'Hip hop', 'Samuele Ricci · dal 2018', 'Groove, popping, locking e freestyle. La '
         'crew dei più grandi fa le gare regionali; gli altri il saggio e le jam del sabato.',
         [('Età', 'dagli 11 anni'), ('Lezioni', '2 a settimana'), ('Gare', 'per chi vuole')]),
        ('tango', 'pexels-2188012.jpg', 'Tango', 'Andrea Costa · dal 2015', 'Si viene anche da soli: le coppie cambiano '
         'ogni dieci minuti. Il venerdì dopo il corso la milonga, aperta a tutti.',
         [('Età', 'adulti'), ('Lezioni', 'giovedì e venerdì'), ('Scarpe', 'col tacco o col cuoio, non obbligatorie')]),
    ]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, nome, ratio='4/5')], wm='2-5', ws='1-1'),
                   t.col([K.label(t, chi, color=C['acc']), K.headline(t, nome, size=72, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati), K.button(t, 'Prova gratis', CORSI + '#iscriviti')],
                         wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 == 0 else None,
            style={'padding_top': 100, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome di chi balla', 'nome', True), K.campo('text', 'Età', 'eta', True),
             K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Il corso', 'corso', True, param='corso',
                     opzioni='Propedeutica\nClassico\nContemporaneo\nHip hop\nTango\nDanza over 60'),
             K.campo('text', 'Il giorno della prova', 'giorno', param='giorno', segnaposto='Es. martedì'),
             K.campo('textarea', 'Qualcosa da dirci', 'messaggio', larghezza='1-1',
                     segnaposto='Esperienze precedenti, un infortunio, la timidezza: ci aiuta a preparare la lezione.')]
    out.append(K.sezione_modulo(t, 'iscriviti', 'La prova gratuita', ['Venite a ballare,', 'la prima è gratis.'],
                                'Vi confermiamo la lezione entro un giorno. Portate vestiti comodi e una bottiglia d’acqua: '
                                'le scarpette per la prova le prestiamo noi.', VOCI, campi, 'Controtempo — prova gratuita',
                                'Prenota la prova', 'Grazie! Vi scriviamo entro domani con l’orario.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['L’ex cinema', 'di via Belzoni.'], size=56, layout='stack', size_mobile=38),
                      K.specs(t, [('Indirizzo', 'via Belzoni 64, Padova'), ('Tram', 'linea SIR1, fermata Portello'),
                                  ('Segreteria', 'lun — ven 15 — 21 · sab 9 — 13'), ('Genitori', 'la sala d’attesa col caffè')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Controtempo · scuola di danza', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_insegnanti():
    t = Tpl(ID, 'gli-insegnanti')
    out = [K.intro(t, 'Gli insegnanti', ['Quattro modi', 'di contare il tempo.'],
                   'Hanno ballato nelle compagnie, nelle crew, nelle milonghe di Buenos Aires. Insegnano qui perché '
                   'vedere qualcuno che impara a ballare è ancora la cosa più bella che conoscono.',
                   bg=K.photo_bg('pexels-24408966.jpg', overlay=55), overlay_text=True)]
    persone = [('pexels-3901636.jpg', 'Mei Lanzoni', 'Classico e propedeutica', 'Diplomata alla Royal Academy, dieci anni '
                'in compagnia a Verona. Ha fondato la scuola nel 2001.', '50% 30%'),
               ('pexels-19128473.jpg', 'Elisa Ferraro', 'Contemporaneo', 'Danzatrice e coreografa, lavora coi teatri di '
                'mezza Europa. Le sue coreografie nascono in sala, coi ragazzi.', '50% 25%'),
               ('pexels-30718161.jpg', 'Andrea Costa', 'Tango e latino', 'Sei inverni nelle milonghe di Buenos Aires. Il '
                'venerdì mette la musica alla milonga della scuola.', '50% 25%'),
               ('pexels-30133725.jpg', 'Samuele Ricci', 'Hip hop', 'Cresciuto nelle jam del Portello, campione regionale '
                'di popping. Allena la crew delle gare.', '50% 25%')]
    cols = []
    for foto, nome, ruolo, bio, pos in persone:
        cols.append(t.col([t.classic('team', {
            'photo': K.img(foto), 'hover_image': '', 'hover_video': '', 'name': nome, 'role': ruolo, 'bio': bio,
            'link_text': 'I suoi corsi', 'link_url': CORSI + '#orario', 'photo_size': '260', 'photo_ratio': '3/4',
            'photo_fit': 'cover', 'photo_object_position': pos, 'photo_shape': 'rounded', 'photo_radius': '14',
            'photo_border_width': '0', 'photo_shadow': 'none', 'info_bg_color': C['sala'], 'info_text_color': C['testo'],
            'info_padding': '24', 'info_radius': '14', 'info_align': 'left', 'name_size': '24', 'name_weight': '700',
            'role_size': '13', 'role_color': C['acc'], 'bio_size': '15', 'bg_color': 'transparent'}, style={})],
            wm='1-4', ws='1-2', width_default='1-2'))
    out.append(t.section([t.row(cols, layout='25-25-25-25', gap=20, valign='start')], padding='large', width='large',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_GRUPPO, 'In sala', 'Si sbaglia <em>tutti insieme.</em>',
                             'Davanti allo specchio, il primo anno, nessuno è bravo. È per questo che è bello: si ride, si '
                             'ricomincia, e a giugno si sale sul palco.', overlay=50,
                             extra=[K.button(t, 'Prova gratis', CORSI + '#iscriviti', kind='pieno', align='center', pad_top=18)]))
    return out


def build_saggio():
    t = Tpl(ID, 'il-saggio')
    out = [K.intro(t, 'Il saggio', ['Una sera sola,', 'centoventi allievi.'],
                   'Il 12 giugno 2027 al Teatro Verdi: classico, contemporaneo, hip hop e tango, due ore e un intervallo. '
                   'I posti si scelgono qui, i biglietti si ritirano in segreteria.',
                   bg=K.photo_bg('pexels-7991568.jpg', overlay=55), overlay_text=True)]
    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2027-06-12T20:30', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': True, 'expired_message': 'Il sipario si è alzato!', 'label_days': 'giorni',
        'label_hours': 'ore', 'label_minutes': 'minuti', 'label_seconds': 'secondi', 'text_color': C['testo'],
        'accent_color': C['acc'], 'number_font_size': 72, 'number_font_weight': '800', 'label_font_size': '12',
        'item_min_width': 140, 'item_bg_color': C['sala'], 'item_radius': {'tl': 999, 'tr': 999, 'br': 14, 'bl': 14},
        'item_padding': {'top': 40, 'right': 20, 'bottom': 20, 'left': 20}}, style={})
    out.append(t.section([t.one([conto])], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 60}))
    settori = [('co-s1', 'Platea, prime file', 'platea-a', '18 €', '12 €', 'Si vedono le punte e il sudore: per i genitori '
                'che vogliono stare vicini.', 'pexels-8462760.jpg', C['acc']),
               ('co-s2', 'Platea centrale', 'platea-b', '15 €', '10 €', 'Il quadro intero delle coreografie, il suono migliore.',
                'pexels-27352253.jpg', mix(C['acc'], 60, C['chiaro'])),
               ('co-s3', 'Palchi centrali', 'o1-c', '15 €', '10 €', 'Dall’alto si vedono i disegni del gruppo, soprattutto '
                'nel contemporaneo.', 'pexels-14605645.jpg', C['sec']),
               ('co-s4', 'Palchi laterali', 'o1-l', '12 €', '8 €', 'Un po’ di lato, ma con le quinte e un pezzo di backstage.',
                'pexels-5135111.jpg', mix(C['sec'], 60, C['chiaro']))]
    pianta = t.classic('piantasala', {
        'forma': 'ferro', 'palco_testo': 'Palco', 'buca': False, 'buca_testo': '', 'file': 14, 'posti_fila': 16, 'ordini': 1,
        'palchi': 11, 'loggione': False, 'palco_centrale': False, 'numeri_ordini': False, 'iniziale': 'primo',
        'testo_invito': 'Passate o toccate un settore: il prezzo e la vista dal posto.', 'testo_intero': 'Intero',
        'testo_ridotto': 'Allievi e under 12', 'testo_vista': 'Il palco da qui', 'testo_link': 'Prenota qui',
        'settori': [{'id': i, 'nome': n, 'zone': z, 'prezzo': p, 'ridotto': r, 'nota': '', 'descrizione': d,
                     'vista': K.img(f), 'link': SAGGIO + '#prenota', 'colore': col} for i, n, z, p, r, d, f, col in settori],
        'disposizione': 'lato', 'larghezza_pianta': 56, 'gap': 48, 'colore_palco': C['sec'], 'colore_etichette': C['tenue'],
        'sfondo_scheda': C['sala'], 'bordo_scheda': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
        'raggio': radius(14), 'nome_font_family': 'heading', 'nome_font_size': 32, 'nome_font_weight': '700',
        'nome_color': C['testo'], 'prezzo_font_family': 'heading', 'prezzo_font_size': 36, 'prezzo_color': C['acc'],
        'descrizione_color': C['tenue']}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'I posti', ['Scegliete dove', 'sedervi.'], size=60,
                         tagline='Il Teatro Verdi ha la platea e un ordine di palchi. Gli allievi hanno il ridotto per due '
                                 'persone di famiglia.')]),
        t.one([pianta]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='posti', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Il settore', 'settore', True, opzioni='Platea, prime file\nPlatea centrale\nPalchi centrali\nPalchi laterali'),
             K.campo('text', 'Quanti biglietti', 'biglietti', True, segnaposto='Es. 3'),
             K.campo('text', 'Di quale allievo siete', 'allievo', segnaposto='Per il ridotto famiglia'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Un posto per una carrozzina, un bambino in braccio.')]
    out.append(K.sezione_modulo(t, 'prenota', 'Prenota i biglietti', ['Tenete il posto,', 'pagate in segreteria.'],
                                'Le prenotazioni si chiudono il 5 giugno. I posti non ritirati entro il 10 tornano liberi.',
                                VOCI, campi, 'Controtempo — biglietti del saggio', 'Prenota i posti',
                                'Fatto! Vi scriviamo il codice di prenotazione entro domani.'))
    out.append(K.video_frase(t, V_NEON, 'Dietro le quinte', 'Le gambe tremano, <em>poi passa.</em>',
                             'Dieci minuti prima del sipario si fa il cerchio, ci si tiene per mano e si conta fino a otto. '
                             'Poi si accendono le luci.', overlay=45))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · passo falso', color=C['acc'], align='center'),
                       K.headline(t, 'Fuori tempo, <em>ricominciamo.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'E cinque, sei, sette, otto: si torna all’inizio.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_OMBRE, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Cultura e formazione', 'E cinque, sei, sette, otto',
    ['danza', 'scuola di danza', 'classico', 'hip hop', 'tango', 'padova', 'scuro', 'slider pro'],
    'Una scuola di danza in un ex cinema di Padova, nero di palco col fucsia e il viola delle luci: in apertura i corsi '
    'in video coi nomi enormi che non smettono di ballare, l’occhio di bue che segue il mouse e il conteggio «cinque, '
    'sei, sette, otto»; il montaggio del saggio, l’orario coi filtri, il conto alla rovescia e i posti in teatro.',
    [('I corsi', CORSI), ('Gli insegnanti', INSEGNANTI), ('Il saggio', SAGGIO), ('Prova gratis', CORSI + '#iscriviti')],
    [('home', 'Home', None), ('i-corsi', 'I corsi', 'i-corsi'), ('gli-insegnanti', 'Gli insegnanti', 'gli-insegnanti'),
     ('il-saggio', 'Il saggio', 'il-saggio')],
    HEX, '\'Plus Jakarta Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bricolage Grotesque\', \'Arial\', sans-serif',
    '\'Martian Mono\', ui-monospace, Menlo, monospace', ['Bricolage Grotesque', 'Plus Jakarta Sans', 'Martian Mono'], corsivo_di=[],
    peso_titoli='700', raggio=14, grana=5, titoli_line='0.98', titoli_spazio='-0.02em')
THEME['styles']['google_fonts_weights'] = '400;500;600;700;800'


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-corsi.json': build_corsi(), 'gli-insegnanti.json': build_insegnanti(), 'il-saggio.json': build_saggio(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
