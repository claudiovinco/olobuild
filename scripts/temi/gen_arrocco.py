# -*- coding: utf-8 -*-
"""Genera il tema «Arrocco» (id arrocco): il circolo di scacchi più vecchio di Trieste, dal 1904 al primo piano del Caffè
delle Torri. Si gioca tutte le sere, si impara il martedì, il venerdì c'è il semilampo. Scuro come l'ebano.

Ebano, avorio dei pezzi bianchi, il verde del panno da torneo e il rosso cuoio delle sedie. Titoli in Libre Caslon
Display, testo in Karla, la notazione delle mosse in Courier Prime (come sui formulari di gara).

Apertura: Slider Pro con la transizione a cubo verticale, ogni diapositiva è una mossa (1. e4 · 14… Dxd4 · 22. Tf1 ·
31. Dh7#) scritta enorme e solo in contorno; attorno, come una scacchiera, le coordinate a–h in basso e 8–1 sul lato
(livelli globali). Poi la lavagna della settimana (Chalkboard), la classifica Elo dei soci (Leaderboard), il torneo
di Natale (Countdown), i corsi che si impilano scorrendo (Stack scroll), la storia del circolo (Timeline) e le quote
(Pricing).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'arrocco'
NAME = 'Arrocco'

HEX = {
    'primary': '#E8DCC4', 'primary_contrast': '#0C0B09', 'secondary': '#7A2E22', 'secondary_contrast': '#F6F0E2',
    'accent': '#8DB596', 'dark': '#050504', 'light': '#F6F0E2', 'muted': '#16140F', 'muted_contrast': '#EDE6D8',
    'text': '#EDE6D8', 'text_muted': '#A39C8F', 'background': '#0C0B09', 'border': '#2B2720', 'link': '#E8DCC4',
}

HOME, CORSI, CIRCOLO, ISCRIVITI = '{{PAGINA:home}}', '{{PAGINA:i-corsi}}', '{{PAGINA:il-circolo}}', '{{PAGINA:iscriviti}}'
MAIL, PHONE, PHONE_TEL = 'segreteria@arrocco.example', '+39 040 000 1904', 'tel:+390400001904'
LAT, LNG = '45.6497', '13.7795'

V_ARCHI, V_ARGENTO, V_SFOCATI, V_MANI, V_OROLOGIO = ('pexels-video-6599643', 'pexels-video-6600356', 'pexels-video-7688892',
                                                     'pexels-video-7320262', 'pexels-video-8327789')
PHOTOS = [
    'pexels-35300539.jpg', 'pexels-10091063.jpg', 'pexels-4576321.jpg', 'pexels-4576336.jpg', 'pexels-4576322.jpg',
    'pexels-4576324.jpg', 'pexels-4576337.jpg', 'pexels-12978859.jpg', 'pexels-4576332.jpg', 'pexels-28677061.jpg',
    'pexels-37033374.jpg', 'pexels-29041833.jpg', 'pexels-29041850.jpg', 'pexels-36038794.jpg', 'pexels-6202958.jpg',
    'pexels-9726970.jpg', 'pexels-8865448.jpg', 'pexels-6202992.jpg', 'pexels-8900007.jpg', 'pexels-6114979.jpg',
    'pexels-9501388.jpg', 'pexels-30480337.jpg', 'pexels-30480339.jpg', 'pexels-14642767.jpg', 'pexels-957312.jpg',
    'pexels-39742535.jpg', 'pexels-38889506.jpg', 'pexels-9864184.jpg', 'pexels-12343245.jpg', 'pexels-12570668.jpg',
    'pexels-34084890.jpg', 'pexels-8465503.jpg', 'pexels-11368599.jpg', 'pexels-33029353.jpg', 'pexels-34715863.jpg',
    'pexels-28397731.jpg', 'pexels-33647015.jpg', 'pexels-16256893.jpg',
]
VIDEOS = [V_ARCHI, V_ARGENTO, V_SFOCATI, V_MANI, V_OROLOGIO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
NOTAZIONE = '\'Courier Prime\', ui-monospace, monospace'
VOCI = [('map-pin', 'Via Cesare Battisti 18, primo piano · Trieste', ISCRIVITI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Arrocco', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Arrocco', 'Circolo scacchistico triestino dal 1904, al primo piano del Caffè delle Torri. Trenta '
                   'scacchiere, gli orologi a pendolo e il caffè portato al tavolo.',
                   [('I corsi', CORSI), ('Il circolo', CIRCOLO), ('Iscriviti', ISCRIVITI), ('Dove siamo', ISCRIVITI + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Le sere', 'Aperto dal lunedì al venerdì, dalle 18 a mezzanotte. Il sabato pomeriggio i ragazzi, la '
                   'domenica si chiude: anche gli scacchisti dormono.', VOCI,
                   '© 2026 Circolo scacchistico Arrocco ASD · Trieste · affiliato FSI', [('instagram', 'https://instagram.com'),
                                                                                       ('youtube', 'https://youtube.com')],
                   marchio_size=150)


def apertura(t):
    m = K.mob
    mosse = [
        ('1. e4', 'Mossa 1 · l’apertura', 'Ogni partita\ncomincia così.', 'Il circolo di scacchi più vecchio di Trieste: '
         'dal 1904 al primo piano del Caffè delle Torri. Si gioca tutte le sere, si impara il martedì.',
         K.sfondo_video(V_ARCHI, overlay=0.4)),
        ('14… Dxd4', 'Mossa 14 · il sacrificio', 'La mossa\nche non vedi.', 'Il martedì il maestro Ferluga spiega una '
         'partita famosa alla lavagna, una mossa alla volta, finché non la vedi anche tu.', K.sfondo_video(V_SFOCATI, overlay=0.5)),
        ('22. Tf1', 'Mossa 22 · la calma', 'Qui si pensa\nin silenzio.', 'Trenta scacchiere, gli orologi a pendolo e il '
         'caffè portato al tavolo. Si parla dopo, giù al bancone.', K.sfondo_foto('pexels-6202992.jpg', overlay=0.55, scala=1.08)),
        ('31. Dh7#', 'Mossa 31 · il matto', 'E poi,\nscacco matto.', 'Il venerdì c’è il semilampo: cinque minuti a testa, '
         'otto turni, chiunque può iscriversi alla porta.', K.sfondo_video(V_ARGENTO, overlay=0.45)),
    ]
    sl = []
    for i, (nota, occ, tit, testo, sfondo) in enumerate(mosse):
        n = i + 1
        sl.append(K.diapositiva('ar-%d' % n, sfondo, [
            K.livello('ar-n%d' % n, contenuto=nota, x=40, y=15, w=54, size=150, peso='700', colore='transparent', align='right',
                      entra='fadeIn', ritardo=200, durata=1400, fontFamily='Courier Prime', letterSpacing=-4,
                      textStrokeWidth=1, textStrokeColor=mix(C['acc'], 75), parallaxDepth=3,
                      responsive={'mobile': {'fontSize': m(64), 'x': 6, 'y': 66, 'width': 88, 'textAlign': 'left'}}),
            K.livello('ar-o%d' % n, contenuto=occ, x=6, y=26, w=40, size=13, peso='400', colore=C['acc'], ritardo=300,
                      letterSpacing=2, textTransform='uppercase', fontFamily='Courier Prime',
                      responsive={'mobile': {'fontSize': m(12), 'y': 12, 'width': 88}}),
            K.livello('ar-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=31, w=56, size=96, peso='400',
                      colore=C['chiaro'], ritardo=450, durata=1100, lineHeight=1.0, mask={'top': 0, 'right': 0, 'bottom': 0, 'left': 0},
                      charAnim={'stagger': 20, 'offsetY': 30, 'opacity': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 24, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(42), 'y': 16, 'width': 88}}),
            K.livello('ar-s%d' % n, contenuto=testo, x=6, y=62, w=34, size=17, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1100, lineHeight=1.6, responsive={'mobile': {'fontSize': m(15), 'y': 31, 'width': 88}}),
            K.livello('ar-b%d' % n, tipo='button', contenuto='Vieni a giocare', x=6, y=78, w='auto', size=12, peso='700',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1400, buttonUrl=ISCRIVITI, hover={'bgColor': C['acc2'], 'duration': 250},
                      responsive={'mobile': {'y': 49, 'fontSize': m(12)}}),
        ], durata=7000))
    coord = [
        K.livello('ar-file', contenuto='a      b      c      d      e      f      g      h', x=6, y=93, w=84, size=12,
                  peso='400', colore=mix(C['chiaro'], 55), entra='fadeIn', ritardo=0, durata=1200, letterSpacing=6,
                  fontFamily='Courier Prime', align='left', responsive={'mobile': {'visible': False}}),
        K.livello('ar-traverse', contenuto='8<br>7<br>6<br>5<br>4<br>3<br>2<br>1', x=96.5, y=14, w=2, size=12, peso='400',
                  colore=mix(C['chiaro'], 55), entra='fadeIn', ritardo=0, durata=1200, lineHeight=2.9,
                  fontFamily='Courier Prime', align='center', responsive={'mobile': {'visible': False}}),
    ]
    return K.proslider(t, sl, globali=coord, transizione='cubeV', durata_trans=1100, showArrows=True, showDots=True,
                       parallax=True, parallaxIntensity=4)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    lavagna = t.classic('chalkboard', {
        'occhiello': 'In lavagna', 'titolo': 'Questa settimana al circolo', 'titolo_tag': 'h3', 'data_mostra': True,
        'data_formato': 'giorno_data',
        'voci': [{'id': 'ar-l1', 'nome': 'Lunedì · gioco libero', 'descrizione': 'dalle 18, le scacchiere sono di chi arriva',
                  'prezzo': 'tutti', 'giorni': '', 'evidenza': 'nessuna', 'etichetta': ''},
                 {'id': 'ar-l2', 'nome': 'Martedì · la partita alla lavagna', 'descrizione': 'Tal contro Botvinnik, 1960, '
                  'sesta partita', 'prezzo': 'M° Ferluga', 'giorni': '', 'evidenza': 'sottolineato', 'etichetta': 'da non perdere'},
                 {'id': 'ar-l3', 'nome': 'Mercoledì · corso principianti', 'descrizione': 'lezione 6: il finale di re e pedone',
                  'prezzo': 'Sala verde', 'giorni': '', 'evidenza': 'nessuna', 'etichetta': ''},
                 {'id': 'ar-l4', 'nome': 'Venerdì · semilampo', 'descrizione': '5 minuti a testa, otto turni, iscrizioni alle 20:30',
                  'prezzo': '5 €', 'giorni': '', 'evidenza': 'cerchio', 'etichetta': ''},
                 {'id': 'ar-l5', 'nome': 'Problema della settimana', 'descrizione': 'il bianco muove e matta in due: '
                  'la soluzione venerdì', 'prezzo': 'in bacheca', 'giorni': '', 'evidenza': 'stella', 'etichetta': ''}],
        'nota': 'Chi trova il matto in due offre il caffè. O lo beve gratis, dipende dal barista.',
        'firma': '— la segreteria',
        'disegni': [{'id': 'ar-d1', 'forma': 'icona', 'icona': 'crown', 'immagine': '', 'x': 90, 'y': 12, 'misura': 60,
                     'rotazione': 8, 'profondita': 70, 'colore': '', 'opacita': 80},
                    {'id': 'ar-d2', 'forma': 'icona', 'icona': 'castle', 'immagine': '', 'x': 88, 'y': 82, 'misura': 52,
                     'rotazione': -6, 'profondita': 50, 'colore': '', 'opacita': 70},
                    {'id': 'ar-d3', 'forma': 'freccia', 'icona': '', 'immagine': '', 'x': 70, 'y': 50, 'misura': 70,
                     'rotazione': 20, 'profondita': 40, 'colore': '', 'opacita': 55}],
        'fondo': 'color-mix(in srgb, var(--olo-color-dark) 82%, var(--olo-color-accent))', 'accento_colore': C['acc'],
        'cornice': 'legno', 'cornice_colore': mix(C['sec'], 70, C['nero']), 'larghezza': 820, 'shadow': 'xl',
    })
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Le sere del circolo', ['Cosa si gioca', 'questa settimana.'], size=60, layout='stack',
                                size_mobile=38),
                      K.text(t, 'La lavagna all’ingresso la scrive la segretaria, Nives, ogni lunedì mattina. Da cent’anni '
                                'col gesso, da tre anni anche qui.'),
                      K.button(t, 'I corsi', CORSI, pad_top=18)], wm='1-3', ws='1-1'),
               t.col([lavagna], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 120}))
    classifica = t.classic('leaderboard', {
        'source': 'manual',
        'rows': [{'name': n, 'role': r, 'value': v, 'max': 2400} for n, r, v in [
            ('Marko Ferluga', 'Maestro FIDE · istruttore', 2287), ('Ilaria Pahor', 'Candidata maestro', 2154),
            ('Davide Zorzenon', 'Prima nazionale', 1986), ('Tea Kosovel', 'Under 16 · campionessa regionale', 1902),
            ('Giorgio Sancin', 'Socio dal 1971', 1855), ('Luca Vascotto', 'Seconda nazionale', 1720)]],
        'name_prefix': '', 'value_suffix': 'Elo', 'show_position': True, 'show_value': True, 'show_role': True,
        'animate_on_view': True, 'animation_duration': 1500, 'highlight_top': 3, 'row_bg': C['sala'],
        'text_color': C['testo'], 'role_color': C['tenue'], 'position_color': C['tenue'],
        'badge_bg': mix(C['acc'], 16), 'badge_color': C['acc'], 'bar_track_color': mix(C['testo'], 10),
        'bar_color': C['acc2'],
    })
    out.append(t.section([
        t.row([t.col([classifica], wm='3-5', ws='1-1'),
               t.col([K.sechead(t, 'La classifica', ['Chi gioca', 'più forte.'], size=60, layout='stack', size_mobile=38),
                      K.text(t, 'Il punteggio Elo dei soci, aggiornato dopo ogni torneo. Tea ha quindici anni e il '
                                'prossimo anno, dice, li supera tutti.')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_MANI, 'Il martedì', 'Si impara <em>perdendo.</em>',
                             'Le partite perse le analizziamo insieme, al tavolo, subito dopo: è la lezione migliore che ci '
                             'sia, e non costa niente.', overlay=55,
                             extra=[K.button(t, 'Il corso principianti', CORSI, kind='pieno', align='center', pad_top=18)]))
    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2026-12-19T15:00', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': False, 'expired_message': 'Il torneo è cominciato: buona partita!',
        'label_days': 'giorni', 'label_hours': 'ore', 'label_minutes': 'minuti', 'text_color': C['testo'],
        'accent_color': C['acc'], 'number_font_size': 88, 'number_font_weight': '400', 'label_font_size': '12',
        'label_font_weight': '400', 'item_min_width': 150, 'item_bg_color': C['sala'],
        'item_radius': {'tl': 2, 'tr': 2, 'br': 2, 'bl': 2}, 'item_padding': {'top': 30, 'right': 20, 'bottom': 22, 'left': 20},
    })
    out.append(t.section([
        t.one([K.sechead(t, 'Torneo di Natale · 19 dicembre', ['Sette turni,', 'una notte.'], size=64, align='center',
                         size_mobile=40)]),
        K.centered(t, [conto, K.text(t, 'Rapid 15 minuti più 5 secondi a mossa, aperto a tutti, anche ai non soci. Panettone '
                                        'alla quarta partita.', align='center', size=16, pad=(18, 0, 0, 0)),
                       K.button(t, 'Iscriviti al torneo', ISCRIVITI + '#scrivici', kind='pieno', align='center', pad_top=20)]),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 140}))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Arrocco',
                page_intro_caption='0-0 · Trieste dal 1904', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_corsi():
    t = Tpl(ID, 'i-corsi')
    out = [K.intro(t, 'I corsi', ['Dal primo pedone', 'al primo torneo.'],
                   'Tre corsi da ottobre a maggio, in piccoli gruppi e con le scacchiere del circolo. La prima lezione è una '
                   'prova: se non ti piace, non paghi.', bg=K.video_bg(V_OROLOGIO, overlay=60), overlay_text=True)]
    pila = t.classic('stackscroll', {
        'top_offset': 110, 'top_step': 24, 'card_gap': 26, 'round': 2, 'card_min_height': 400, 'media_position': 'right',
        'card_bg_default': C['sala'], 'text_color_default': C['testo'], 'num_color_default': mix(C['acc'], 70),
        'shadow_color': mix(C['nero'], 70), 'show_number': True,
        'cards': [{'eyebrow': e, 'title': ti, 'accent': ac, 'text': tx, 'media': K.img(f), 'color': C['sala'], 'text_color': C['testo']}
                  for e, ti, ac, tx, f in [
                      ('Mercoledì 18:30 · 12 lezioni · 120 €', 'Principianti', 'da zero',
                       '<p>Come si muovono i pezzi, le aperture che servono davvero, i primi finali. Si gioca a ogni lezione '
                       'contro chi è al tuo livello.</p><ul><li>Scacchiera e manuale del circolo compresi</li><li>Tessera '
                       'del primo anno inclusa</li></ul>', 'pexels-36038794.jpg'),
                      ('Sabato 15:00 · da 7 a 14 anni · 90 €', 'Ragazzi', 'il sabato',
                       '<p>Due ore di gioco e di problemi, con la merenda a metà. A primavera il campionato giovanile '
                       'regionale, per chi vuole.</p><ul><li>Gruppi per età</li><li>Gli istruttori hanno la qualifica '
                       'federale</li></ul>', 'pexels-33029353.jpg'),
                      ('Martedì 20:30 · tutto l’anno · 180 €', 'Agonisti', 'col maestro',
                       '<p>Repertorio di aperture, analisi delle tue partite, preparazione ai tornei. Con il maestro FIDE '
                       'Marko Ferluga.</p><ul><li>Una partita commentata a settimana</li><li>Accesso al database del '
                       'circolo</li></ul>', 'pexels-9726970.jpg')]],
    }, style={})
    out.append(t.section([t.one([pila], anim=None)], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(K.pannello(t, 'pexels-39742535.jpg', 'D’estate', 'Si gioca in piazza.', 'Da giugno a settembre portiamo le '
                          'scacchiere in piazza Cavana, il giovedì sera. Chiunque può sedersi: anche chi non ha mai giocato.',
                          'Il circolo', CIRCOLO, overlay=55))
    out.append(K.domande(t, 'Prima', 'della prima mossa.', [
        ('Devo saper giocare?', 'No: il corso principianti parte dal movimento dei pezzi. Basta la curiosità.'),
        ('Serve una scacchiera mia?', 'Al circolo ce ne sono trenta. Per giocare a casa ti diamo quella del corso.'),
        ('C’è un’età massima?', 'Il nostro socio più anziano ha 91 anni e gioca ancora il semilampo del venerdì.'),
        ('Posso venire solo a guardare?', 'Certo, in silenzio. Il caffè al tavolo lo portano anche a chi guarda.'),
    ], 'co', bg=True))
    return out


def build_circolo():
    t = Tpl(ID, 'il-circolo')
    out = [K.intro(t, 'Il circolo', ['Centoventidue anni', 'di partite.'],
                   'Nel 1904 dodici soci affittano una stanza sopra il Caffè delle Torri per giocare in pace. La stanza è '
                   'diventata tre sale, i soci duecento, la pace è rimasta.', bg=K.photo_bg('pexels-957312.jpg', overlay=60),
                   overlay_text=True)]
    storia = t.classic('timeline', {
        'items': [{'date': d, 'title': ti, 'tag': tg, 'description': de, 'image': K.img(f), 'video': '', 'icon': ic,
                   'category': 'primary', 'icon_color': '', 'status': ''} for d, ti, tg, de, f, ic in [
            ('1904', 'Dodici soci, una stanza', 'Le origini', 'Il circolo nasce sopra il Caffè delle Torri: sei scacchiere, '
             'una stufa e il regolamento scritto a mano.', 'pexels-39742535.jpg', 'home'),
            ('1954', 'Il torneo internazionale', 'La festa', 'Trieste torna all’Italia e il circolo organizza un torneo con '
             'maestri da otto paesi. Le foto sono ancora appese in sala.', 'pexels-28677061.jpg', 'star'),
            ('1988', 'La prima socia campionessa', 'Le donne', 'Vesna Kralj vince il campionato regionale assoluto. Da allora '
             'il trofeo del circolo porta il suo nome.', 'pexels-12978859.jpg', 'crown'),
            ('2026', 'Duecento soci, i ragazzi', 'Oggi', 'Il sabato le sale si riempiono di ragazzi: sono la metà dei soci '
             'nuovi degli ultimi tre anni.', 'pexels-8465503.jpg', 'users')]],
        'tl_layout': 'alt', 'tl_reveal': 'bloom', 'tl_theme': 'night', 'tl_card': 'polaroid', 'tl_thread': 'dash',
        'tl_node': 'year', 'tl_transparent': True, 'tl_title_color': C['testo'], 'tl_text_color': C['tenue'],
        'tl_yr_color': C['acc'], 'tl_tag_color': C['acc2'], 'tl_title_family': 'heading',
        'tl_card_bg': C['sala'], 'tl_rail_color': C['filo'],
    })
    out.append(t.section([t.one([storia])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-28397731.jpg', 'La sala grande durante un torneo', ratio='4/3')], wm='1-2', ws='1-1'),
               t.col([K.label(t, 'Le tre sale', color=C['acc']), K.headline(t, 'Verde, rossa, <em>e la grande.</em>', size=52,
                                                                          tag='h2', pad_bottom=14),
                      K.text(t, 'La sala verde per i corsi, la rossa per le partite lunghe, la grande per i tornei: trenta '
                                'tavoli col piano di noce e una finestra sulla piazza.'),
                      K.specs(t, [('Scacchiere', '30, coi pezzi Staunton'), ('Orologi', '30 digitali, 4 a pendolo'),
                                  ('Biblioteca', '1.400 libri di scacchi'), ('Caffè', 'portato al tavolo')])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_ARCHI, 'Il motto', 'Pensa <em>due volte,</em> muovi una.',
                             'È scritto sopra la porta dal 1904, in triestino. La traduzione è nostra, la regola è la '
                             'stessa.', overlay=50))
    return out


def build_iscriviti():
    t = Tpl(ID, 'iscriviti')
    out = [K.intro(t, 'Iscriviti', ['Una tessera,', 'tutte le sere.'],
                   'Con la tessera giochi tutte le sere, partecipi ai tornei del circolo e prendi in prestito i libri della '
                   'biblioteca. La prima sera è gratis per tutti.', bg=K.photo_bg('pexels-16256893.jpg', overlay=55),
                   overlay_text=True)]

    def quota(nome, prezzo, periodo, voci, popolare=False, info=''):
        return t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': periodo,
            'features': '\n'.join(voci), 'cta_text': 'Iscriviti', 'cta_url': ISCRIVITI + '#scrivici', 'cta_bg_color': C['acc'],
            'cta_text_color': C['on_acc'], 'cta_radius': '2', 'cta_hover_bg_color': C['acc2'], 'cta_hover_text_color': C['on_acc'],
            'additional_info': info, 'is_popular': popolare, 'badge_text': 'La più scelta', 'badge_bg_color': C['acc2'],
            'badge_text_color': C['on_acc'], 'bg_color': C['sala'], 'accent_color': C['acc'], 'text_color': C['testo'],
            'price_color': C['acc'], 'border_radius': '2', 'shadow': 'none',
            'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': C['filo']},
        })
    out.append(t.section([
        t.row([t.col([quota('Socio', '120', 'all’anno', ['Gioco libero tutte le sere', 'Tornei del circolo', 'Prestito dei libri'],
                            info='Tessera FSI compresa')], wm='1-3', ws='1-1'),
               t.col([quota('Ragazzi', '60', 'all’anno', ['Fino a 18 anni', 'Il sabato col corso', 'Campionato giovanile'],
                            popolare=True, info='Il secondo fratello gratis')], wm='1-3', ws='1-1'),
               t.col([quota('Una sera', '5', 'a ingresso', ['Per chi passa da Trieste', 'Partite libere fino a mezzanotte',
                                                              'Semilampo del venerdì'], info='Si paga alla porta')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=24, valign='stretch'),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Cosa ti interessa', 'cosa', True,
                     opzioni='La tessera da socio\nIl corso principianti\nIl corso ragazzi\nIl corso agonisti\nIl torneo di Natale'),
             K.campo('select', 'Il tuo livello', 'livello', opzioni='Non ho mai giocato\nSo muovere i pezzi\nGioco nei tornei'),
             K.campo('text', 'Il tuo Elo, se ce l’hai', 'elo'),
             K.campo('textarea', 'Scrivici', 'messaggio', larghezza='1-1', segnaposto='Quando vorresti venire, cosa ti piacerebbe imparare…')]
    out.append(K.sezione_modulo(t, 'scrivici', 'La prima sera', ['È gratis,', 'vieni a giocare.'],
                                'Ti rispondiamo entro due giorni con la sera giusta e il nome di chi ti aspetta alla porta.',
                                VOCI, campi, 'Arrocco — iscrizione', 'Manda la richiesta',
                                'Ricevuta. Ti scriviamo entro due giorni: porta solo la curiosità.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Sopra il caffè,', 'primo piano.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via Cesare Battisti 18, Trieste'), ('Autobus', '9, 10, 11 · fermata Battisti'),
                                  ('Dalla stazione', 'venti minuti a piedi'), ('Accesso', 'ascensore dal cortile')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Arrocco · circolo scacchistico', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · mossa illegale', color=C['acc'], align='center'),
                       K.headline(t, 'Questa casella <em>è vuota.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Rimetti il pezzo e torna all’inizio: qui non c’è nessuna partita.', size=17, align='center',
                              color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ARGENTO, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Cultura e formazione', 'Muovi il cubo',
    ['scacchi', 'circolo', 'trieste', 'corsi', 'tornei', 'associazione', 'scuro', 'slider pro'],
    'Il circolo di scacchi più vecchio di Trieste, ebano e avorio: l’apertura a cubo verticale con le mosse scritte '
    'enormi e la scacchiera attorno, la lavagna della settimana scritta col gesso, la classifica Elo che si riempie, il '
    'conto alla rovescia del torneo, i corsi che si impilano scorrendo e la storia dal 1904.',
    [('I corsi', CORSI), ('Il circolo', CIRCOLO), ('Dove siamo', ISCRIVITI + '#arrivare'), ('Iscriviti', ISCRIVITI)],
    [('home', 'Home', None), ('i-corsi', 'I corsi', 'i-corsi'), ('il-circolo', 'Il circolo', 'il-circolo'),
     ('iscriviti', 'Iscriviti', 'iscriviti')],
    HEX, '\'Karla\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Libre Caslon Display\', \'Didot\', Georgia, serif', NOTAZIONE,
    ['Libre Caslon Display', 'Karla', 'Courier Prime'], peso_titoli='400', raggio=2, grana=5, titoli_line='1.02',
    titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-corsi.json': build_corsi(), 'il-circolo.json': build_circolo(), 'iscriviti.json': build_iscriviti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
