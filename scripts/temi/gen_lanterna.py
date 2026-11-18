# -*- coding: utf-8 -*-
"""Genera il tema «Lanterna» (id lanterna): una escape room al Porto Antico di Genova, sotto la Lanterna. Tre stanze al
buio, da due a sei giocatori, sessanta minuti per uscire.

La trovata (18 nov 2026, sera; utente: «cerca di farne altri 5»): il sito è al buio. Il cursore «Torcia» spegne la
pagina e la illumina solo dove punti (al telefono la luce sta ferma al centro e la pagina le scorre sotto); l'interruttore
in basso a sinistra accende la luce per chi non vuole giocare. Nell'apertura il fascio del faro gira sopra la lanterna
mentre scorri.

Tile poco usate messe alla prova: Glow Hero (mai usata) per i sessanta minuti, Newsticker coi tempi record, Lucchetto
alla reception, Shattered Image sulle chiavi.
Tema scuro: nero, ambra della lanterna, rosso del segnale. Titoli in Cinzel (maiuscole romane, niente corsivo), testo in
Manrope. Cursore: torcia.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'lanterna'
NAME = 'Lanterna'
CATEGORIA = 'Eventi e musica'

HEX = {
    'primary': '#FFB547', 'primary_contrast': '#1A1206', 'secondary': '#3FA7A0', 'secondary_contrast': '#06201E',
    'accent': '#FF6B5E', 'dark': '#07070A', 'light': '#FFFFFF', 'muted': '#15151B', 'muted_contrast': '#EDE8DD',
    'text': '#EDE8DD', 'text_muted': '#A9A296', 'background': '#0C0C10', 'border': '#2C2C33', 'link': '#FFC66B',
}
HOME, STANZE, REGOLE, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:le-stanze}}', '{{PAGINA:le-regole}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'buio@lanterna.example', '010 000 6060', 'tel:+390100006060'
LAT, LNG = '44.4047', '8.9010'

V_FARO, V_TORCIA, V_CORRIDOIO, V_CANDELA, V_CHIAVE, V_SAGOMA = (
    'pexels-video-6256795', 'pexels-video-19405094', 'pexels-video-6814635', 'pexels-video-6227435', 'pexels-video-6980925',
    'pexels-video-4768550')
VIDEOS = [V_FARO, V_TORCIA, V_CORRIDOIO, V_CANDELA, V_CHIAVE, V_SAGOMA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(FARO_RAGGI, FARO_STELLE, FARO_ROSSO, SALA_MACCHINE, BIMBI_TENDA, DUE_TORCIA, DONNA_BUIO, CANTINA, CANDELA_LIBRI,
 CHIAVI_APPESE, CHIAVI_TANTE, SERRATURA, TOPPA, MAPPA_BUIO, MAPPE, MAPPA_VICINA, LUCCHETTO_PORTA, LUCCHETTO_RUGGINE,
 CANDELE, LUMINI, GENOVA_ALTO, GENOVA_PORTO, AMICHE_TRE, AMICHE_DUE, AMICHE_RIDONO, INGRANAGGI, INGRANAGGI_MURO,
 BUSSOLA_MAPPA, BUSSOLA_SCRIGNO, BUSSOLA, PORTA_PIETRA, PORTA_LUCE, PORTA_EDERA) = f(
    32846085, 35109032, 23961764, 30226733, 5951636, 5427442, 5427434, 6613803, 16827795, 36575138, 33388008, 37602341,
    31943789, 4402272, 33039807, 30247330, 9554231, 5795580, 37771422, 12493594, 11971015, 36268895, 4005032, 37576109,
    8910376, 37362875, 10583683, 14436272, 10728163, 28143581, 29452615, 30335237, 32822220)
PHOTOS = [FARO_RAGGI, FARO_STELLE, FARO_ROSSO, SALA_MACCHINE, BIMBI_TENDA, DUE_TORCIA, DONNA_BUIO, CANTINA, CANDELA_LIBRI,
          CHIAVI_APPESE, CHIAVI_TANTE, SERRATURA, TOPPA, MAPPA_BUIO, MAPPE, MAPPA_VICINA, LUCCHETTO_PORTA, LUCCHETTO_RUGGINE,
          CANDELE, LUMINI, GENOVA_ALTO, GENOVA_PORTO, AMICHE_TRE, AMICHE_DUE, AMICHE_RIDONO, INGRANAGGI, INGRANAGGI_MURO,
          BUSSOLA_MAPPA, BUSSOLA_SCRIGNO, BUSSOLA, PORTA_PIETRA, PORTA_LUCE, PORTA_EDERA]
DISEGNI = ['lanterna-fascio.svg']
LOGHI = ['lanterna-logo-chiaro.png', 'lanterna-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True, accento_su_media='#FFB547')
C = K.c
FONT_T = 'Cinzel'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Calata Mandraccio 7 · Genova', PRENOTA + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '699/119',
                   'Lanterna è una escape room al Porto Antico di Genova: tre stanze al buio, da due a sei giocatori, '
                   'sessanta minuti per trovare la chiave e uscire.',
                   [('Le stanze', STANZE), ('Le regole', REGOLE), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'Quando', 'Mercoledì — domenica<br>15 — 24, l’ultima partita alle 22:30', VOCI,
                   '© 2026 Lanterna escape room · Genova · P. IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                                ('tiktok', 'https://tiktok.com')], scuro=True)


# ── L'apertura: il fascio del faro che gira ──
# Il fascio (disegno 1600 × 420, la punta a sinistra al centro dell'altezza) gira attorno alla sua punta, che sta sulla
# lanterna del video: perno «0% 50%» del riquadro, quindi vale su ogni tela. Un giro intero e un pezzo mentre scorri.
def apertura(t):
    m = K.mob
    gira = {'inizio': 0, 'fine': 100, 'andamento': 'costante', 'tappe': [{'p': 0, 'r': -28}, {'p': 100, 'r': 400}]}
    poi = lambda da, a, via_da=None, via_a=None: {'inizio': 0, 'fine': 100, 'tappe': (
        [{'p': 0, 'o': 0, 'y': 16}, {'p': da, 'o': 0, 'y': 16}, {'p': a, 'o': 100, 'y': 0}]
        + ([{'p': via_da, 'o': 100, 'y': 0}, {'p': via_a, 'o': 0, 'y': -16}, {'p': 100, 'o': 0, 'y': -16}] if via_da
           else [{'p': 100, 'o': 100, 'y': 0}]))}
    L = K.veli('la-velo', 'basso', 44, 76, colore=C['nero'], forza=90, mobile_da=46)
    L += [
        K.livello('la-fascio', tipo='image', imageSrc=K.img('lanterna-fascio.svg'), imageAlt='', objectFit='fill', x=50, y=29,
                  w=64, h=30, entra='fadeIn', ritardo=300, durata=1200, customCSS='transform-origin:0% 50%;mix-blend-mode:screen;',
                  scrollMove=gira, responsive={'mobile': {'x': 50, 'y': 24.5, 'width': 74, 'height': 9}}),
        K.livello('la-o', contenuto='Escape room · Porto Antico · Genova', x=6, y=56, w=50, size=13, peso='600',
                  colore=mix(C['chiaro'], 86), letterSpacing=4, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                  scrollMove=poi(0, 1, 30, 36), responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 51, 'width': 88}}),
        K.livello('la-t1', contenuto='Sessanta minuti,<br>una torcia.', tag='h1', x=6, y=61, w=60, size=84, peso='700',
                  colore=C['chiaro'], fontFamily=FONT_T, lineHeight=1.02, entra='fadeInUp', ritardo=300, durata=900,
                  scrollMove=poi(0, 1, 30, 36), responsive={'mobile': {'fontSize': m(40), 'x': 6, 'y': 54, 'width': 88}}),
        K.livello('la-t1s', contenuto='Tre stanze al buio sotto la Lanterna di Genova. Si esce solo risolvendole.', x=6, y=86,
                  w=44, size=19, peso='500', colore=mix(C['chiaro'], 90), lineHeight=1.45, ritardo=600,
                  scrollMove=poi(0, 1, 30, 36), responsive={'mobile': {'fontSize': m(16), 'x': 6, 'y': 66, 'width': 88}}),
        K.livello('la-t2', contenuto='La luce<br>ce l’hai tu.', x=6, y=61, w=60, size=84, peso='700', colore=C['acc'],
                  fontFamily=FONT_T, lineHeight=1.02, entra='fadeIn', ritardo=0, scrollMove=poi(40, 48, 66, 72),
                  responsive={'mobile': {'fontSize': m(40), 'x': 6, 'y': 54, 'width': 88}}),
        K.livello('la-t2s', contenuto='Dove punti, si vede. Il resto resta al buio, come nelle nostre stanze.', x=6, y=86, w=44,
                  size=19, peso='500', colore=mix(C['chiaro'], 90), lineHeight=1.45, entra='fadeIn', ritardo=0,
                  scrollMove=poi(42, 50, 66, 72), responsive={'mobile': {'fontSize': m(16), 'x': 6, 'y': 66, 'width': 88}}),
        K.livello('la-t3', contenuto='Chi accende<br>ha già perso.', x=6, y=61, w=60, size=84, peso='700', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=1.02, entra='fadeIn', ritardo=0, scrollMove=poi(76, 84),
                  responsive={'mobile': {'fontSize': m(40), 'x': 6, 'y': 54, 'width': 88}}),
        K.livello('la-b1', tipo='button', contenuto='Le stanze', x=6, y=86, w='auto', size=14, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=6, buttonUrl=STANZE, entra='fadeIn',
                  ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=poi(80, 88),
                  hover={'bgColor': C['acc2'], 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 67, 'fontSize': m(13)}}),
        K.livello('la-b2', tipo='button', contenuto='Prenota', x=20, y=86, w='auto', size=14, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=18, borderRadius=6, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), buttonUrl=PRENOTA, entra='fadeIn', ritardo=0, textTransform='uppercase',
                  letterSpacing=2, scrollMove=poi(80, 88), hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 52, 'y': 67, 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('la-1', K.sfondo_video(V_FARO, overlay=0.2, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def stanza(t, foto, numero, nome, testo, specifiche, link=STANZE):
    """Una stanza: la foto, il nome, due righe, le specifiche in mono."""
    return t.col([K.image(t, foto, nome, ratio='4/5'),
                  K.label(t, numero, color=C['acc'], pad_top=22),
                  K.headline(t, nome, size=30, tag='h3', pad_bottom=10, maiuscolo=True),
                  K.text(t, testo, size=16),
                  K.label(t, specifiche, color=C['tenue'], pad_top=10),
                  K.button(t, 'Prenota questa stanza', link, pad_top=8)], wm='1-3', ws='1-1')


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Escape room · dal 2019', ['Il sito è al buio.', 'La torcia ce l’hai tu.'], size=64,
                                layout='stack', size_mobile=36)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Come nelle nostre stanze: si vede solo quello che illumini. In basso a sinistra c’è '
                                'l’interruttore, se vuoi leggere tutto con calma. Noi però lo lasceremmo spento.', size=17),
                      K.button(t, 'Le regole', REGOLE, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    # Glow Hero: i sessanta minuti
    out.append(t.section([t.one([t.bare('glowhero', {
        'eyebrow': 'Tre stanze | da 2 a 6 giocatori | sotto la Lanterna',
        'lines': [{'text': 'Sessanta', 'mode': ''}, {'text': 'minuti', 'mode': 'accent'}, {'text': 'per uscire.', 'mode': 'outline'}],
        'uppercase': True, 'eyebrow_dots': True, 'subhead': 'Vi chiudiamo dentro con una torcia e un orologio che conta all’indietro. '
        'La chiave c’è: sta a voi trovarla.', 'cta1_text': 'Prenota una partita', 'cta1_url': PRENOTA, 'cta2_text': 'Le stanze',
        'cta2_url': STANZE, 'show_scroll': False, 'bg_color': C['nero'], 'accent': C['acc'], 'accent2': C['acc2'],
        'accent_on': C['on_acc'], 'text_color': C['chiaro'], 'sub_color': C['tenue'], 'eyebrow_color': C['tenue'],
        'glow_color': 'rgba(255,181,71,0.20)', 'glow_x': 50, 'glow_y': 30, 'h_size_min': 48, 'h_size_vw': 10, 'h_size_max': 150,
        'align': 'center', 'min_height': 90, 'bottom_split': False})], anim=None)],
        width='fullbleed', padding='remove-vertical'))
    # le tre stanze
    out.append(t.section([
        t.one([K.sechead(t, 'Le stanze', ['Tre porte,', 'tre buio diversi.'], size=60)]),
        t.row([stanza(t, CANTINA, 'Stanza 1 · la più facile', 'La cripta del faro', 'Sotto la Lanterna c’era una cantina '
                      'murata. Il guardiano ci ha lasciato un messaggio, e una porta che si apre solo da dentro.',
                      '2–4 giocatori · difficoltà ●○○'),
               stanza(t, MAPPA_BUIO, 'Stanza 2', 'Il cartografo', 'Una mappa del porto del 1890 con un errore voluto. '
                      'Trovarlo vuol dire trovare il tesoro della Compagnia, e la via d’uscita.', '3–5 giocatori · difficoltà ●●○'),
               stanza(t, INGRANAGGI, 'Stanza 3 · la più difficile', 'Il meccanismo', 'Il motore che faceva girare la luce del '
                      'faro si è fermato. Avete un’ora per rimetterlo in moto prima che una nave arrivi sugli scogli.',
                      '4–6 giocatori · difficoltà ●●●')],
              layout='33-33-33', gap=32, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    # i tempi record (Newsticker)
    record = lambda n, testo, badge: {'id': 'la-nt-%d' % n, 'title': testo, 'url': '', 'badge': badge, 'icon': ''}
    out.append(t.section([t.one([t.bare('newsticker', {
        'items': [record(1, 'La cripta del faro · 31:48 · I Gatti del Molo', 'Record'),
                  record(2, 'Il cartografo · 42:05 · Famiglia Repetto, quattro generazioni', 'Record'),
                  record(3, 'Il meccanismo · 54:59 · Gli Ingegneri Stanchi, a un secondo dalla fine', 'Record'),
                  record(4, 'Questa settimana: 38 squadre entrate, 21 uscite', '')],
        'direction': 'horizontal', 'show_label': True, 'label_text': 'Tempi da battere', 'label_shape': 'arrow',
        'label_position': 'left', 'label_bg': C['acc'], 'label_color': C['on_acc'], 'badge_bg': C['acc2'], 'badge_color': C['nero'],
        'bg_color': C['sala'], 'text_color': C['testo'], 'height': '54', 'separator': '·', 'font_size': 15,
        'animation_type': 'marquee', 'marquee_direction': 'left', 'marquee_gap': 80, 'marquee_duration': 32,
        'pause_on_hover': True})], anim=None)], width='fullbleed', padding='remove-vertical'))
    # le chiavi (Shattered Image) e il lucchetto della reception
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'In ogni stanza', ['Cento chiavi,', 'una sola giusta.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Le abbiamo raccolte nei mercatini di Sottoripa: chiavi di portoni, di bauli, di casse del '
                                'porto. Ne serve una. Le altre sono lì per confondervi.', size=17)], wm='2-5', ws='1-1'),
               t.col([t.bare('shatteredimage', {'image_url': K.img(CHIAVI_TANTE), 'preset': 'shards', 'gap': 5, 'height': '480px',
                                                'image_position': 'center center', 'gap_color': C['fondo'], 'zoom_variation': True,
                                                'zoom_min': 100, 'zoom_max': 150, 'zoom_random': True, 'scroll_reveal': True,
                                                'scroll_reveal_stagger': 120, 'kenburns': True, 'kenburns_style': 'mixed',
                                                'kenburns_duration': 24, 'overlay': False})], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Alla reception', ['Prima di entrare,', 'un riscaldamento.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Quattro cifre. Chi apre il lucchetto qui ha dieci minuti in più nella prima stanza.',
                             size=17)], wm='2-5', ws='1-1'),
               t.col([t.classic('lucchetto', {
                   'enigma': '<p><strong>Quattro cifre</strong>, in quest’ordine:</p><p>le stanze di Lanterna, i giocatori che '
                             'entrano al massimo in una stanza, e i minuti che avete per uscire.</p>',
                   'indizio': 'I minuti sono scritti nell’apertura di questa pagina, in grande.', 'testo_indizio': 'Un indizio',
                   'combinazione': '3660', 'simboli': 'cifre', 'testo_pulsante': 'Apri', 'messaggio_errore': 'Non si apre. '
                   'Riprova.', 'messaggio_successo': 'Aperto! Dite questo codice alla reception: dieci minuti in più nella '
                   'prima stanza.', 'codice_premio': 'BUIO3660', 'testo_copia': 'Copia il codice', 'testo_copiato': 'Copiato',
                   'tentativi_indizio': 2, 'mostra_lucchetto': True, 'stile': 'ottone', 'colore_accento': C['acc'],
                   'sfondo_pannello': mix(C['chiaro'], 5)}, style={})], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('Siamo usciti dal Meccanismo a quattro secondi dalla fine. Abbiamo urlato così forte che è venuto il guardiano.',
         'Davide', 'Sampierdarena'),
        ('Ho festeggiato i quarant’anni nella cripta. Mia sorella ha trovato la chiave al buio, io la torcia.', 'Chiara',
         'Nervi'),
        ('Siamo entrati in sei che non si parlavano. Siamo usciti in sei che si davano ordini.', 'Ufficio Acquisti',
         'Porto Antico')],
        'Chi è uscito', ['Tre squadre,', 'tre porte aperte.']))
    out.append(K.pannello(t, DUE_TORCIA, 'Mercoledì — domenica, fino a mezzanotte', 'La prossima <em>siete voi.</em>',
                          'Prenotate la stanza e l’ora: vi aspettiamo dieci minuti prima, per le regole e la torcia.',
                          'Prenota', PRENOTA, effetto=''))
    return out


def build_stanze():
    t = Tpl(ID, 'le-stanze')
    out = [K.intro(t, 'Le stanze', ['Tre porte,', 'chiuse da dentro.'], 'Ogni stanza ha la sua storia, i suoi enigmi e la sua '
                   'luce: poca. Si gioca da due a sei, sempre in sessanta minuti.', bg=K.video_bg(V_CORRIDOIO, overlay=26), size=96)]
    righe = [
        (CANTINA, 'La cripta del faro', 'La più facile · 2–4 giocatori',
         'Nel 1840 il guardiano della Lanterna murò una cantina sotto le fondamenta. Dentro, oggi, ci siete voi, una lettera '
         'mai spedita e una porta che si apre solo da dentro. Perfetta per la prima volta.'),
        (MAPPE, 'Il cartografo', 'Media · 3–5 giocatori',
         'Lo studio di un cartografo della Compagnia del porto: mappe, compassi, un mappamondo che non gira. Una delle mappe '
         'ha un errore voluto. Trovarlo è metà della partita.'),
        (INGRANAGGI_MURO, 'Il meccanismo', 'La più difficile · 4–6 giocatori',
         'La sala del motore che faceva girare la luce. Ingranaggi, leve, un orologio che non perdona. Solo per chi ha già '
         'fatto almeno una escape room.'),
    ]
    for k, (foto, nome, quanto, testo) in enumerate(righe):
        cols = [t.col([K.image(t, foto, nome, ratio='4/3')], wm='1-2', ws='1-1'),
                t.col([K.label(t, quanto, color=C['acc']), K.headline(t, nome, size=56, tag='h2', pad_bottom=14, maiuscolo=True,
                                                                     sizes=(44, 32)),
                       K.text(t, testo, size=17), K.button(t, 'Prenota', PRENOTA, kind='pieno', pad_top=16)],
                      wm='1-2', ws='1-1')]
        if k % 2:
            cols.reverse()
        out.append(t.section([t.row(cols, layout='50-50', gap=64, valign='center')],
                             bg=solid(C['sala']) if k % 2 else None, padding='large', width='large',
                             style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(t.section([t.one([K.numeri(t, [('60', 'minuti per stanza'), ('2–6', 'giocatori'), ('58 %', 'squadre che escono'),
                                              ('31:48', 'il record più basso')], colonne=4, size=58)])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(K.pannello(t, PORTA_LUCE, 'La quarta stanza', 'Arriva <em>a marzo.</em>', 'Non possiamo dire niente. Solo che '
                          'dentro non c’è nemmeno la torcia.', 'Scrivici', 'mailto:' + MAIL, effetto=''))
    return out


def build_regole():
    t = Tpl(ID, 'le-regole')
    out = [K.intro(t, 'Le regole', ['Poche,', 'e tutte al buio.'], 'Si entra con una torcia a testa e il telefono nell’armadietto. '
                   'Il resto lo spieghiamo alla porta, in cinque minuti.', bg=K.video_bg(V_CANDELA, overlay=26), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Dalla porta', 'alla chiave.'], size=56)]),
        P.passi(K, t, [('01', 'Arrivate', 'Dieci minuti prima. Il telefono va nell’armadietto, la torcia in mano.'),
                       ('02', 'Vi chiudiamo', 'Il guardiano vi accompagna dentro e chiude. L’orologio parte.'),
                       ('03', 'Chiedete aiuto', 'Tre indizi per partita: basta dirlo ad alta voce, il guardiano sente.'),
                       ('04', 'Uscite', 'Con la chiave, o allo scadere dell’ora. Comunque con una foto di squadra.')], colonne=4),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Domande', ['Quello che', 'ci chiedono alla porta.'], [
        ('Fa paura?', 'No: è buio, non è un horror. Niente attori che saltano fuori, niente urla registrate.'),
        ('Si può uscire prima?', 'Sempre: la porta d’emergenza si apre con una maniglia, e la vedete appena entrate.'),
        ('I bambini possono giocare?', 'Dagli 8 anni con un adulto. La Cripta del faro è la stanza giusta per loro.'),
        ('E se non vedo bene al buio?', 'Diteci prima: alziamo un po’ le luci della stanza, la partita resta la stessa.'),
        ('Quanto costa?', 'Dai 22 euro a testa in sei ai 35 in due. Il prezzo esatto lo vedete prenotando.')], 'lar'))
    out.append(t.section([t.one([P.citazione(K, t, 'Al buio si sente tutto: chi ha capito, chi bluffa, chi ha paura di '
                                                   'sbagliare.', 'Matteo, il guardiano del mercoledì')])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Scegliete la stanza,', 'noi spegniamo la luce.'], 'Rispondiamo entro un’ora, dalle 15 a '
                   'mezzanotte. Per le feste e le aziende scriveteci: apriamo anche al mattino.',
                   bg=K.video_bg(V_TORCIA, overlay=24), size=96)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('select', 'Stanza', 'stanza', True, opzioni='La cripta del faro\nIl cartografo\nIl meccanismo\n'
                     'Non sappiamo, consigliateci voi'),
             K.campo('select', 'Giocatori', 'giocatori', True, opzioni='2\n3\n4\n5\n6'),
             K.campo('date', 'Giorno', 'giorno', True),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Una stanza,', 'un’ora, la vostra squadra.'],
                                'Vi rispondiamo con l’orario libero più vicino a quello che chiedete.', VOCI, campi,
                                'Lanterna — prenotazione', 'Prenota', 'Ricevuto! Vi scriviamo entro un’ora con l’orario.'))
    out.append(t.section([t.one([X.orari(K, t, [('Mercoledì — venerdì', '15 — 24', 'Ultima partita alle 22:30'),
                                                ('Sabato e domenica', '11 — 24', 'Anche al mattino per le famiglie'),
                                                ('Lunedì e martedì', 'chiuso', 'Le stanze riposano al buio')])])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 60}))
    out.append(X.zone(K, t, 'Dove siamo', ['Al Porto Antico,', 'sotto i portici.'], 'Calata Mandraccio 7: la porta nera con '
                      'la lanterna accesa, a due passi dall’Acquario.', [('Metro', 'fermata San Giorgio'),
                      ('Treno', 'Genova Piazza Principe, 15 minuti a piedi'), ('Auto', 'parcheggio del Porto Antico'),
                      ('Telefono', PHONE)], LAT, LNG, 'Lanterna escape room · Calata Mandraccio 7', True, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · stanza vuota', color=C['acc'], align='center'),
                       K.headline(t, 'Vi siete <em>persi al buio.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc'], maiuscolo=True),
                       K.text(t, 'Questa porta non porta da nessuna parte. Capita: anche nelle nostre stanze ce n’è una finta.',
                              size=17, align='center', color=mix(C['chiaro'], 88)),
                       K.button(t, 'Torna alla luce', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_SAGOMA, overlay=30), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito al buio',
    ['escape room', 'giochi', 'genova', 'buio', 'scuro', 'slider pro', 'torcia', 'cursore'],
    'Una escape room al Porto Antico di Genova: il sito è al buio e si vede solo dove punti la torcia, al telefono la luce '
    'sta al centro e la pagina ci scorre sotto; nell’apertura il fascio del faro gira sopra la lanterna. Le tre stanze, i '
    'tempi da battere che scorrono, cento chiavi in frantumi e un lucchetto da aprire alla reception.',
    [('Le stanze', STANZE), ('Le regole', REGOLE), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('le-stanze', 'Le stanze', 'le-stanze'), ('le-regole', 'Le regole', 'le-regole'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cinzel\', Georgia, serif',
    '\'JetBrains Mono\', ui-monospace, monospace', ['Manrope', 'Cinzel', 'JetBrains Mono'], corsivo_di=[],
    peso_titoli='700', raggio=6, grana=5, titoli_line='1.04', titoli_spazio='0.01em')
THEME['cursor']['style'] = 'torcia'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['primary']
THEME['cursor']['label_text'] = 'Luce'
THEME['filtri'] = {'gesto': 'muovi', 'carattere': 'grazie'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-stanze.json': build_stanze(), 'le-regole.json': build_regole(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
