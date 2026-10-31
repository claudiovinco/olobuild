# -*- coding: utf-8 -*-
"""Genera il tema «Soffio» (id soffio): uno studio di yoga e pilates in Veronetta, a Verona, in un ex laboratorio di
cappelli con le finestre sull’Adige. Al massimo dodici persone per lezione, dalle sette del mattino alle nove di sera.

Tema chiaro, sabbia e lino, col cotto e la salvia. Titoli in Petrona leggero (anche corsivo), testo in Onest, orari in
Spline Sans Mono.

Apertura: Slider Pro che respira. Quattro momenti della giornata (l’alba, la pausa pranzo, il tardo pomeriggio, la
sera), ognuno col suo video chiaro e un velo di lino a sinistra che tiene il testo; a destra, fisso su tutte le
diapositive, un cerchio che si gonfia e si sgonfia in otto secondi, con «Inspira» ed «Espira» che si danno il cambio:
si può respirare con lui. Le linguette in basso sono gli orari delle lezioni; allo scorrimento l’apertura sfuma.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'soffio'
NAME = 'Soffio'

HEX = {
    'primary': '#9C4A2A', 'primary_contrast': '#FFFFFF', 'secondary': '#6E7F64', 'secondary_contrast': '#FFFFFF',
    'accent': '#E3BC93', 'dark': '#25221D', 'light': '#FFFDF8', 'muted': '#EDE5D8', 'muted_contrast': '#2A2722',
    'text': '#2A2722', 'text_muted': '#665E53', 'background': '#F6F1E9', 'border': '#DDD2C1', 'link': '#9C4A2A',
}

HOME, LEZIONI, INSEGNANTI, PROVA = '{{PAGINA:home}}', '{{PAGINA:le-lezioni}}', '{{PAGINA:gli-insegnanti}}', '{{PAGINA:prova}}'
MAIL, PHONE, PHONE_TEL = 'respira@soffio.example', '+39 045 000 1207', 'tel:+390450001207'
LAT, LNG = '45.4425', '11.0052'

V_ALBA, V_MARE, V_NAMASTE, V_FINESTRA, V_TENDA, V_PILATES, V_MANI = (
    'pexels-video-4352399', 'pexels-video-5928289', 'pexels-video-4384298', 'pexels-video-5835693',
    'pexels-video-35161057', 'pexels-video-6023202', 'pexels-video-4325503')
PHOTOS = [
    'pexels-7319694.jpg', 'pexels-8436748.jpg', 'pexels-8436635.jpg', 'pexels-4998828.jpg', 'pexels-18499505.jpg',
    'pexels-11036670.jpg', 'pexels-18075025.jpg', 'pexels-33360216.jpg', 'pexels-6957318.jpg', 'pexels-6648578.jpg',
    'pexels-6648592.jpg', 'pexels-6648595.jpg', 'pexels-4083051.jpg', 'pexels-8958837.jpg', 'pexels-6800258.jpg',
    'pexels-6454061.jpg', 'pexels-7530597.jpg', 'pexels-7530596.jpg', 'pexels-8436402.jpg', 'pexels-8437076.jpg',
    'pexels-8436574.jpg', 'pexels-763147.jpg', 'pexels-13371944.jpg', 'pexels-7114134.jpg', 'pexels-31578585.jpg',
    'pexels-3723874.jpg', 'pexels-6957894.jpg', 'pexels-6957887.jpg', 'pexels-6739125.jpg', 'pexels-8436717.jpg',
    'pexels-5890311.jpg',
]
VIDEOS = [V_ALBA, V_MARE, V_NAMASTE, V_FINESTRA, V_TENDA, V_PILATES, V_MANI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=24, peso_titoli='300', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media=mix('var(--olo-color-accent)', 45, 'var(--olo-color-light)'))
C = K.c
TITOLI, MONO = 'Petrona', 'Spline Sans Mono'
VOCI = [('map-pin', 'Via XX Settembre 40 · Verona', PROVA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Soffio', logo_size='28', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Soffio', 'Yoga e pilates in Veronetta, in un ex laboratorio di cappelli con le finestre sull’Adige. '
                   'Dodici persone al massimo, tappetini e cuscini li abbiamo noi.',
                   [('Le lezioni', LEZIONI), ('Gli insegnanti', INSEGNANTI), ('Prova gratis', PROVA),
                    ('Dove siamo', PROVA + '#arrivare'), ('Privacy', '/privacy-policy/')],
                   'Lo studio', 'Lunedì — venerdì 7 — 21<br>Sabato 9 — 13<br>Domenica: meditazione alle 10', VOCI,
                   '© 2026 Soffio associazione sportiva dilettantistica · Verona · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150,
                   fondo=C['sala'])


def apertura(t):
    m = K.mob
    momenti = [
        ('06:45 · Hatha all’alba', 'Il respiro', 'viene prima.', 'Un’ora lenta prima del lavoro: posizioni tenute a '
         'lungo, il corpo che si sveglia una vertebra alla volta.', V_MARE, '06:45'),
        ('12:30 · Pilates in pausa pranzo', 'Forza,', 'senza fretta.', 'Cinquanta minuti sui reformer, sei persone per '
         'turno: si torna in ufficio dritti, non stanchi.', V_PILATES, '12:30'),
        ('18:00 · Meditazione', 'Dieci minuti', 'di silenzio.', 'Seduti, gli occhi chiusi, la voce di Bianca che conta i '
         'respiri. Si impara in quattro settimane.', V_TENDA, '18:00'),
        ('20:30 · Yin alla sera', 'Quando la città', 'si spegne.', 'Posizioni a terra, cuscini e coperte, le luci basse. '
         'Si esce leggeri e si dorme meglio.', V_FINESTRA, '20:30'),
    ]
    sl = []
    for i, (occ, r1, r2, testo, video, tab) in enumerate(momenti):
        n = i + 1
        sfondo = K.sfondo_video(video, overlay=0.22, colore=C['fondo'])
        sl.append(K.diapositiva('so-%d' % n, sfondo, [
            # il velo di lino che tiene il testo
            K.livello('so-v%d' % n, tipo='shape', x=0, y=0, w=42, h=100, entra='fadeIn', ritardo=0, durata=900, esce='fadeOut',
                      bgColor=mix(C['fondo'], 88), responsive={'mobile': {'x': 0, 'y': 47.5, 'width': 100, 'height': 52.5}}),
            K.livello('so-w%d' % n, tipo='shape', x=42, y=0, w=24, h=100, entra='fadeIn', ritardo=0, durata=900, esce='fadeOut',
                      shapeGradient={'angle': 90, 'from': mix(C['fondo'], 88), 'to': mix(C['fondo'], 0)},
                      responsive={'mobile': {'visible': False}}),
            K.livello('so-o%d' % n, contenuto=occ, x=7, y=27, w=40, size=12, peso='500', colore=C['acc'], ritardo=250,
                      letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                      responsive={'mobile': {'fontSize': m(11), 'y': 54, 'x': 8, 'width': 84}}),
            K.livello('so-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=7, y=31.5, w=46, size=96, peso='300',
                      colore=C['testo'], ritardo=400, durata=1400, lineHeight=1.02, entra='maskFromBottom', fontFamily=TITOLI,
                      responsive={'mobile': {'fontSize': m(44), 'y': 56.5, 'x': 8, 'width': 86}}),
            K.livello('so-i%d' % n, contenuto=r2, tag='p', x=7, y=43, w=46, size=96, peso='300', fontStyle='italic',
                      colore=C['acc'], ritardo=750, durata=1400, lineHeight=1.02, entra='maskFromBottom', fontFamily=TITOLI,
                      responsive={'mobile': {'fontSize': m(44), 'y': 61.3, 'x': 8, 'width': 86}}),
            K.livello('so-s%d' % n, contenuto=testo, x=7, y=58, w=31, size=18, peso='400', colore=C['tenue'], ritardo=1300,
                      lineHeight=1.6, entra='fadeIn', responsive={'mobile': {'fontSize': m(15), 'y': 67.5, 'x': 8, 'width': 84}}),
            K.livello('so-b%d' % n, tipo='button', contenuto='La prima lezione è gratis', x=7, y=72, w='auto', size=14,
                      peso='600', colore=C['on_acc'], bgColor=C['acc'], padding=16, borderRadius=999, ritardo=1600,
                      buttonUrl=PROVA + '#prova', hover={'bgColor': C['testo'], 'duration': 250},
                      responsive={'mobile': {'y': 77, 'x': 8, 'fontSize': m(13)}}),
        ], durata=8000, tab=tab))
    # il cerchio che respira: otto secondi, quattro dentro e quattro fuori, uguale su ogni diapositiva
    cx, cy, cw, ch = 63, 20, 26, 42

    def kf(tempo, scala, opac, x=cx, y=cy):
        return {'time': tempo, 'props': {'x': x, 'y': y, 'scale': scala, 'opacity': opac, 'originX': 50, 'originY': 50},
                'easing': 'ease-in-out'}
    globali = [
        K.livello('so-alone', tipo='shape', x=cx, y=cy, w=cw, h=ch, bgColor=mix(C['acc2'], 26), borderRadius=999,
                  borderWidth=2, borderColor=mix(C['acc'], 70), backdropBlur=8, backdropBrightness=106,
                  timeline={'duration': 8000, 'delay': 0, 'loop': True,
                            'keyframes': [kf(0, 0.62, 70), kf(4000, 1.0, 100), kf(8000, 0.62, 70)]},
                  responsive={'mobile': {'x': 20, 'y': 11, 'width': 60, 'height': 27}}),
        K.livello('so-nucleo', tipo='shape', x=cx + 9.75, y=cy + 15.75, w=6.5, h=10.5, bgColor=mix(C['acc'], 30),
                  borderRadius=999, timeline={'duration': 8000, 'delay': 0, 'loop': True,
                                              'keyframes': [kf(0, 1.0, 90, cx + 9.75, cy + 15.75),
                                                            kf(4000, 1.5, 60, cx + 9.75, cy + 15.75),
                                                            kf(8000, 1.0, 90, cx + 9.75, cy + 15.75)]},
                  responsive={'mobile': {'x': 42.5, 'y': 21.2, 'width': 15, 'height': 6.8}}),
        K.livello('so-dentro', contenuto='Inspira', x=cx, y=cy + 26.5, w=cw, size=13, peso='500', colore=C['testo'],
                  align='center', letterSpacing=3, textTransform='uppercase', fontFamily=MONO,
                  timeline={'duration': 8000, 'delay': 0, 'loop': True,
                            'keyframes': [kf(0, 1, 0, cx, cy + 26.5), kf(600, 1, 100, cx, cy + 26.5),
                                          kf(3400, 1, 100, cx, cy + 26.5), kf(4000, 1, 0, cx, cy + 26.5),
                                          kf(8000, 1, 0, cx, cy + 26.5)]},
                  responsive={'mobile': {'x': 20, 'y': 27.5, 'width': 60, 'fontSize': m(11)}}),
        K.livello('so-fuori', contenuto='Espira', x=cx, y=cy + 26.5, w=cw, size=13, peso='500', colore=C['testo'],
                  align='center', letterSpacing=3, textTransform='uppercase', fontFamily=MONO,
                  timeline={'duration': 8000, 'delay': 0, 'loop': True,
                            'keyframes': [kf(0, 1, 0, cx, cy + 26.5), kf(4000, 1, 0, cx, cy + 26.5),
                                          kf(4600, 1, 100, cx, cy + 26.5), kf(7400, 1, 100, cx, cy + 26.5),
                                          kf(8000, 1, 0, cx, cy + 26.5)]},
                  responsive={'mobile': {'x': 20, 'y': 27.5, 'width': 60, 'fontSize': m(11)}}),
        K.livello('so-respira', contenuto='Respira con il cerchio', x=cx, y=cy + 46, w=cw, size=11, peso='500',
                  colore=C['testo'], align='center', letterSpacing=2, textTransform='uppercase', fontFamily=MONO,
                  entra='fadeIn', ritardo=2400, responsive={'mobile': {'visible': False}}),
    ]
    return K.proslider(t, sl, globali=globali, transizione='blur', durata_trans=1400, showArrows=False, showDots=False,
                       showTabs=True, tabPosition='bottom', parallax=False, scrollEffect='fadeBlur', navColor=C['testo'],
                       autoplaySpeed=8000, progressBarColor=C['acc'],
                       globalBackground={'type': 'color', 'color': C['fondo']})


def ghiera(t):
    voci = [
        ('Ha', 'Hatha', 'Posizioni tenute a lungo e il respiro contato: la base di tutto, per chi comincia e per chi '
         'ricomincia.', 'Lunedì, mercoledì, venerdì · 6:45', 'pexels-7319694.jpg', 'hatha'),
        ('Vi', 'Vinyasa', 'Le posizioni legate al respiro, una dopo l’altra, come una danza lenta. Si suda, si sorride.',
         'Martedì e giovedì · 18:30', 'pexels-6454061.jpg', 'vinyasa'),
        ('Yi', 'Yin', 'A terra, coi cuscini, tre o quattro minuti per posizione. Per le articolazioni e per il sonno.',
         'Lunedì e giovedì · 20:30', 'pexels-8436748.jpg', 'yin'),
        ('Pm', 'Pilates a terra', 'Il centro del corpo, la schiena, l’equilibrio. Sul tappetino, con elastici e palle.',
         'Martedì e venerdì · 9:00', 'pexels-8958837.jpg', 'pilates'),
        ('Pr', 'Pilates reformer', 'Sei macchine di legno, sei persone: ogni esercizio regolato su di voi. In coppia o '
         'da soli.', 'Tutti i giorni · su prenotazione', 'pexels-18499505.jpg', 'reformer'),
        ('Me', 'Meditazione', 'Seduti, in silenzio, con la voce che guida i primi minuti. Quattro settimane per '
         'imparare a farlo da soli.', 'Mercoledì 18:00 · domenica 10:00', 'pexels-6648592.jpg', 'meditazione'),
    ]
    return t.classic('ghiera', {
        'voci': [{'sigla': s, 'titolo': ti, 'testo': te, 'prezzo': o, 'immagine': K.img(f), 'video': '',
                  'link': LEZIONI + '#' + a, 'testo_link': 'L’orario'} for s, ti, te, o, f, a in voci],
        'partenza': 1, 'titolo_centro': 'Sei pratiche', 'mostra_numero': True,
        'etichetta': 'Gira la ghiera: una pratica per ogni momento', 'stile': 'piatto',
        'colore_ghiera': C['sala'], 'colore_sigle': C['testo'], 'colore_indice': C['acc'], 'sfondo_pannello': '',
        'colore_pulsante': C['acc'], 'colore_testo_pulsante': C['on_acc'], 'sigla_font_family': 'heading',
        'sigla_font_size': 22, 'sigla_font_weight': '400', 'titolo_font_family': 'heading', 'titolo_font_size': 60,
        'titolo_font_size_mobile': 40, 'titolo_font_weight': '300', 'titolo_line_height': 1.0, 'titolo_color': C['testo'],
        'testo_font_size': 17, 'testo_color': C['tenue'], 'prezzo_font_family': 'var(--olo-font-family-mono)',
        'prezzo_font_size': 14, 'prezzo_font_weight': '500', 'prezzo_color': C['acc'], 'misura': 420,
        'raggio_media': radius(200), 'raggio_pulsante': radius(999), 'proporzione': '4/5', 'disposizione': 'lato', 'gap': 64},
        style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lo studio', ['Dodici tappetini,', 'una finestra sul fiume.'], size=60, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Soffio è un ex laboratorio di cappelli, al primo piano, con tre finestre sull’Adige. Le '
                                'lezioni sono piccole apposta: dodici persone sul tappetino, sei sui reformer. Così '
                                'l’insegnante vi vede, vi corregge e si ricorda il vostro nome.', size=17, color=C['testo']),
                      K.specs(t, [('Per lezione', 'al massimo 12 persone'), ('Lezioni a settimana', '38'),
                                  ('Tappetini, cuscini, coperte', 'li abbiamo noi')])], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Sei pratiche', ['Una per ogni momento', 'della giornata.'], size=56,
                         tagline='Girate la ghiera, o trascinatela: a ogni tacca una pratica, con gli orari della '
                                 'settimana.')]),
        t.one([ghiera(t)]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='pratiche', style={'padding_top': 120, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_ALBA, 'Inspira, espira', 'Quattro dentro, <em>quattro fuori.</em>',
                             'È tutto quello che serve per cominciare. Il resto lo impariamo insieme, una lezione alla '
                             'volta.', overlay=66,
                             extra=[K.button(t, 'La prima lezione è gratis', PROVA + '#prova', kind='pieno', align='center',
                                             pad_top=18)]))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-6739125.jpg', 'Bianca Moro, la fondatrice di Soffio', ratio='4/5', drift=True)],
                     wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Bianca Moro · fondatrice', color=C['acc']),
                      t.classic('quotation', {
                          'content': 'Non serve essere flessibili per fare yoga. Si fa yoga per diventarlo: nel corpo, e '
                                     'un poco anche nella testa.', 'author': 'Bianca Moro, insegnante dal 2004',
                          'style': 'default', 'alignment': 'left', 'content_font_family': 'heading', 'content_font_size': '44',
                          'content_font_weight': '300', 'content_font_style': 'italic', 'content_color': C['testo'],
                          'author_color': C['tenue'], 'author_font_size': '15', 'text_effect': 'none'}, style={}),
                      K.button(t, 'Gli insegnanti', INSEGNANTI, pad_top=24)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, 'pexels-18499505.jpg', 'Il pilates reformer', 'Sei macchine, <em>sei persone.</em>',
                          'Le macchine di legno regolate su di voi, l’insegnante che gira fra i reformer. In pausa pranzo, '
                          'la sera, o a due con un’amica.', 'L’orario', LEZIONI + '#reformer', overlay=68))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Soffio',
                page_intro_caption='Yoga e pilates · Verona', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_lezioni():
    t = Tpl(ID, 'le-lezioni')
    out = [K.intro(t, 'Le lezioni', ['Dalle sette', 'alle nove di sera.'],
                   'Trentotto lezioni a settimana, in tre sale. Scegliete la pratica e il livello: l’orario mostra solo '
                   'quelle che vi interessano, e oggi è evidenziato.', bg=K.photo_bg('pexels-763147.jpg', overlay=80, color=C['fondo']))]
    corsi = [('hatha', 'Hatha', C['acc'], 'Posizioni tenute a lungo, il respiro contato.', 60),
             ('vinyasa', 'Vinyasa', C['sec'], 'Le posizioni legate al respiro, in sequenza.', 60),
             ('yin', 'Yin', mix(C['acc'], 55, C['sec']), 'A terra coi cuscini, posizioni lunghe.', 75),
             ('pilates', 'Pilates a terra', mix(C['acc2'], 80, C['acc']), 'Sul tappetino, con piccoli attrezzi.', 50),
             ('reformer', 'Pilates reformer', C['testo'], 'Sei reformer di legno, sei persone.', 50),
             ('meditazione', 'Meditazione', mix(C['sec'], 60, C['testo']), 'Seduti, in silenzio, guidati.', 30)]
    lezioni = []
    piano = [  # giorno, inizio, fine, corso, insegnante, sala, livello
        (1, '06:45', '07:45', 'hatha', 'Bianca Moro', 'Fiume', 'tutti'), (1, '12:30', '13:20', 'reformer', 'Matteo Sala', 'Legno', 'tutti'),
        (1, '18:30', '19:30', 'pilates', 'Matteo Sala', 'Fiume', 'base'), (1, '20:30', '21:45', 'yin', 'Irene Bassi', 'Fiume', 'tutti'),
        (2, '09:00', '09:50', 'pilates', 'Matteo Sala', 'Fiume', 'tutti'), (2, '12:30', '13:20', 'reformer', 'Matteo Sala', 'Legno', 'intermedio'),
        (2, '18:30', '19:30', 'vinyasa', 'Irene Bassi', 'Fiume', 'intermedio'),
        (3, '06:45', '07:45', 'hatha', 'Bianca Moro', 'Fiume', 'tutti'), (3, '12:30', '13:20', 'reformer', 'Matteo Sala', 'Legno', 'tutti'),
        (3, '18:00', '18:30', 'meditazione', 'Bianca Moro', 'Silenzio', 'tutti'), (3, '19:00', '20:00', 'vinyasa', 'Irene Bassi', 'Fiume', 'base'),
        (4, '12:30', '13:20', 'reformer', 'Matteo Sala', 'Legno', 'intermedio'), (4, '18:30', '19:30', 'vinyasa', 'Irene Bassi', 'Fiume', 'intermedio'),
        (4, '20:30', '21:45', 'yin', 'Irene Bassi', 'Fiume', 'tutti'),
        (5, '06:45', '07:45', 'hatha', 'Bianca Moro', 'Fiume', 'tutti'), (5, '09:00', '09:50', 'pilates', 'Matteo Sala', 'Fiume', 'tutti'),
        (5, '18:30', '19:20', 'reformer', 'Matteo Sala', 'Legno', 'tutti'),
        (6, '09:30', '10:30', 'vinyasa', 'Irene Bassi', 'Fiume', 'tutti'), (6, '11:00', '11:50', 'reformer', 'Matteo Sala', 'Legno', 'tutti'),
        (7, '10:00', '10:30', 'meditazione', 'Bianca Moro', 'Silenzio', 'tutti')]
    for k, (g, a, b, c, ins, sala, liv) in enumerate(piano):
        lezioni.append({'id': 'so-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': ins, 'sala': sala,
                        'livello': liv, 'posti': 6 if c == 'reformer' else 12, 'nota': ''})
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': lezioni, 'ora_inizio': '06:30', 'ora_fine': '22:00', 'mostra_filtri': True, 'etichetta_tutti': 'Tutte',
        'filtro_livello': True, 'filtro_istruttore': True, 'testo_vuoto': 'Nessuna lezione con questi filtri: togliene uno.',
        'testo_prenota': 'Prenota la prova', 'link_prenota': PROVA + '?corso={corso}&giorno={giorno}&ora={ora}#prova',
        'colore_oggi': C['acc'], 'colore_adesso': C['sec'], 'colore_tenue': C['testo'], 'giorno_font_family': 'heading', 'giorno_font_size': 22,
        'giorno_font_weight': '400', 'giorno_uppercase': False, 'giorno_letter_spacing': 0, 'corso_font_family': 'heading',
        'corso_font_size': 18, 'corso_font_weight': '400', 'ora_font_family': 'var(--olo-font-family-mono)',
        'raggio_lezione': radius(12), 'altezza_ora': 60, 'gap': 4, 'forma_filtri': 'pillola'}, style={})
    out.append(t.section([t.one([orario])], padding='large', width='large', html_id='orario',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    for k, (aid, foto, nome, chi, testo, dati) in enumerate([
        ('hatha', 'pexels-7319694.jpg', 'Hatha e Vinyasa', 'Sul tappetino', 'Hatha al mattino presto, Vinyasa la sera: le '
         'stesse posizioni, due modi di respirarle. Si comincia sempre da qui.',
         [('Durata', '60 minuti'), ('Livelli', 'base, tutti, intermedio'), ('Sala', 'Fiume, la grande')]),
        ('reformer', 'pexels-18075025.jpg', 'Pilates reformer', 'Sulle macchine di legno', 'Sei reformer regolati su di voi: '
         'molle, cinghie e carrello lavorano coi muscoli profondi. Ottimo dopo un mal di schiena, col via del medico.',
         [('Durata', '50 minuti'), ('Posti', '6 per turno'), ('Sala', 'Legno')]),
        ('yin', 'pexels-8436748.jpg', 'Yin e meditazione', 'La sera e la domenica', 'Le pratiche lente: a terra, coi '
         'cuscini e le coperte, o seduti in silenzio. Si esce piano, si dorme meglio.',
         [('Durata', 'da 30 a 75 minuti'), ('Livello', 'per tutti'), ('Sala', 'Silenzio, la piccola')]),
    ]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, nome, ratio='4/5')], wm='2-5', ws='1-1'),
                   t.col([K.label(t, chi, color=C['acc']), K.headline(t, nome, size=56, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati), K.button(t, 'Prova gratis', PROVA + '#prova')],
                         wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid + '-pratica', bg=solid(C['sala']) if k % 2 == 0 else None,
            style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima', 'della prima lezione.', [
        ('Non sono flessibile: posso venire?', 'Sì. Le posizioni si adattano con mattoncini e cinghie, e l’insegnante vi '
                                               'mostra la versione per voi.'),
        ('Cosa mi porto?', 'Vestiti comodi e una bottiglia d’acqua. Tappetini, cuscini e coperte li abbiamo noi.'),
        ('E se ho mal di schiena?', 'Ditecelo prima: il pilates reformer e l’hatha aiutano, ma con le correzioni giuste.'),
        ('Come si disdice?', 'Fino a tre ore prima, da telefono o per email: la lezione resta nel carnet.'),
    ], 'le', bg=False))
    return out


def build_insegnanti():
    t = Tpl(ID, 'gli-insegnanti')
    out = [K.intro(t, 'Gli insegnanti', ['Tre insegnanti,', 'trent’anni di pratica.'],
                   'Vi chiamano per nome dalla seconda lezione. Ognuno ha la sua pratica, ma si danno il cambio: se uno '
                   'manca, la lezione si fa lo stesso.', bg=K.photo_bg('pexels-13371944.jpg', overlay=80, color=C['fondo']))]
    persone = [
        ('pexels-6739125.jpg', 'Bianca Moro', 'Hatha e meditazione', 'Ha aperto Soffio nel 2004, dopo dieci anni in '
         'ufficio e due in India. Insegna al mattino presto e la domenica.', '50% 30%'),
        ('pexels-6957887.jpg', 'Irene Bassi', 'Vinyasa e Yin', 'Ex ballerina, insegna dal 2015. Le sue lezioni della sera '
         'hanno la lista d’attesa: prenotate il lunedì.', '50% 30%'),
        ('pexels-8436717.jpg', 'Matteo Sala', 'Pilates a terra e reformer', 'Fisioterapista, segue chi arriva dopo un '
         'infortunio. Regola ogni reformer prima che saliate.', '50% 25%'),
    ]
    cols = []
    for foto, nome, ruolo, bio, pos in persone:
        cols.append(t.col([t.classic('team', {
            'photo': K.img(foto), 'hover_image': '', 'hover_video': '', 'name': nome, 'role': ruolo, 'bio': bio,
            'link_text': 'Le sue lezioni', 'link_url': LEZIONI + '#orario', 'photo_size': '200', 'photo_ratio': '1/1',
            'photo_fit': 'cover', 'photo_object_position': pos, 'photo_shape': 'circle', 'photo_border_width': '6',
            'photo_border_color': C['chiaro'], 'photo_shadow': 'md', 'info_bg_color': C['chiaro'], 'info_text_color': C['testo'],
            'info_padding': '28', 'info_radius': '24', 'info_align': 'center', 'name_size': '26', 'name_weight': '400',
            'role_size': '13', 'role_color': C['acc'], 'bio_size': '15', 'bg_color': 'transparent'}, style={})],
            wm='1-3', ws='1-1'))
    out.append(t.section([t.row(cols, layout='33-33-33', gap=24, valign='start')], bg=solid(C['sala']), padding='large',
                         width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, f, a, ratio='4/5')], wm='1-3', ws='1-1') for f, a in [
            ('pexels-8436402.jpg', 'La classe del mattino col saluto finale'),
            ('pexels-8436574.jpg', 'Si arrotolano i tappetini a fine lezione'),
            ('pexels-31578585.jpg', 'Il tè della fine lezione in una tazza dipinta')]], layout='33-33-33', gap=16),
    ], padding='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_TENDA, 'A fine lezione', 'Il tè <em>è compreso.</em>',
                             'Zenzero e limone d’inverno, menta d’estate. Ci si siede vicino alla finestra, e si torna nel '
                             'mondo piano.', overlay=66))
    return out


def build_prova():
    t = Tpl(ID, 'prova')
    out = [K.intro(t, 'Prova gratis', ['La prima lezione', 'è un regalo.'],
                   'Scegliete il giorno e la pratica: vi aspettiamo dieci minuti prima, vi diamo il tappetino e vi '
                   'presentiamo all’insegnante.', bg=K.photo_bg('pexels-7114134.jpg', overlay=80, color=C['fondo']))]
    piani = [('Carnet 10', '140', 'dieci lezioni in quattro mesi', 'Tutte le pratiche a terra\nUna lezione di reformer '
              'compresa\nSi sospende se state male', False, ''),
             ('Mensile', '85', 'al mese, senza vincolo', 'Lezioni a terra senza limiti\nReformer a 12 € a turno\nUna '
              'lezione per un’amica, ogni mese', True, 'La più scelta'),
             ('Reformer 8', '160', 'otto turni in due mesi', 'Sei persone per turno\nIl programma scritto per voi\nUn '
              'colloquio con Matteo all’inizio', False, '')]
    cols = []
    for nome, prezzo, periodo, voci, pop, badge in piani:
        cols.append(t.col([t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': periodo,
            'features': voci, 'check_style': 'circle-check', 'cta_text': 'Comincia con la prova', 'cta_url': PROVA + '#prova',
            'cta_bg_color': C['acc'] if pop else C['testo'], 'cta_text_color': C['on_acc'] if pop else C['fondo'],
            'cta_radius': '999', 'cta_hover_bg_color': C['testo'], 'additional_info': 'La prima lezione è gratis',
            'is_popular': pop, 'badge_text': badge, 'badge_bg_color': C['acc2'], 'badge_text_color': C['testo'],
            'bg_color': C['chiaro'], 'accent_color': C['acc'], 'text_color': C['testo'], 'price_color': C['testo'],
            'border_radius': '28', 'shadow': 'md' if pop else 'none'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Si paga quando', 'si è deciso.'], size=56,
                         tagline='Prima la prova, poi il carnet o il mensile. Gli studenti e chi ha più di settanta anni '
                                 'hanno il 20%.')]),
        t.row(cols, layout='33-33-33', gap=24, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'), K.campo('select', 'Che pratica', 'corso', True,
                                                            opzioni='Hatha\nVinyasa\nYin\nPilates a terra\nPilates reformer\nMeditazione',
                                                            param='corso'),
             K.campo('text', 'Che giorno', 'giorno', param='giorno', segnaposto='Es. martedì sera'),
             K.campo('textarea', 'Qualcosa da dirci', 'messaggio', larghezza='1-1',
                     segnaposto='Un infortunio, una gravidanza, la prima volta: tutto quello che serve all’insegnante.')]
    out.append(K.sezione_modulo(t, 'prova', 'La lezione di prova', ['Scegliete', 'il primo respiro.'],
                                'Vi rispondiamo in giornata con la conferma. Se la lezione è piena, vi proponiamo la '
                                'prima libera.', VOCI, campi, 'Soffio — lezione di prova', 'Prenota la prova',
                                'Grazie! Vi scriviamo entro sera con la conferma.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['In Veronetta,', 'sopra il fiume.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via XX Settembre 40, primo piano'), ('Dal Ponte Navi', 'tre minuti a piedi'),
                                  ('Bici', 'la rastrelliera è nel cortile'), ('Orari', 'lun — ven 7 — 21 · sab 9 — 13')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Soffio · yoga e pilates', scuro=False, hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pagina assente', color=K.acc_media, align='center'),
                       K.headline(t, 'Respira. <em>Non è qui.</em>', size=96, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Quattro dentro, quattro fuori, e torniamo all’inizio.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_TENDA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'Il respiro viene prima',
    ['yoga', 'pilates', 'meditazione', 'verona', 'studio', 'chiaro', 'slider pro'],
    'Uno studio di yoga e pilates a Verona, sabbia, lino e cotto: in apertura quattro momenti della giornata in video, '
    'col cerchio che si gonfia e si sgonfia in otto secondi per respirare insieme; la ghiera delle sei pratiche, l’orario '
    'della settimana coi filtri, gli insegnanti e la prima lezione gratis.',
    [('Le lezioni', LEZIONI), ('Gli insegnanti', INSEGNANTI), ('Le pratiche', HOME + '#pratiche'), ('Prova gratis', PROVA)],
    [('home', 'Home', None), ('le-lezioni', 'Le lezioni', 'le-lezioni'), ('gli-insegnanti', 'Gli insegnanti', 'gli-insegnanti'),
     ('prova', 'Prova gratis', 'prova')],
    HEX, '\'Onest\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Petrona\', \'Georgia\', serif',
    '\'Spline Sans Mono\', ui-monospace, Menlo, monospace', ['Petrona', 'Onest', 'Spline Sans Mono'],
    corsivo_di=['Petrona'], peso_titoli='300', raggio=24, grana=0, titoli_line='1.02', titoli_spazio='-0.015em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-lezioni.json': build_lezioni(), 'gli-insegnanti.json': build_insegnanti(),
                        'prova.json': build_prova(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
