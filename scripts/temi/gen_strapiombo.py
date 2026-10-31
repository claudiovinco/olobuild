# -*- coding: utf-8 -*-
"""Genera il tema «Strapiombo» (id strapiombo): una palestra di boulder a Trento, ottocento metri quadri di pareti
strapiombanti, le vie cambiate ogni settimana, corsi per adulti e bambini e le uscite in falesia.

Tema chiaro e grafico: cemento chiaro, nero, il rosso segnale e i colori delle prese. Titoli in Schibsted Grotesk
nero pieno, dati in JetBrains Mono.

Apertura divisa in due metà quasi indipendenti, ognuna col suo Slider Pro: Dentro (il boulder, i corsi, i bambini)
scorre dall'alto ogni 7 secondi, Fuori (la falesia, il materiale) sfuma ogni 9 e mezzo. I video si vedono pieni, il
testo sta in basso su una sfumatura scura; la metà sotto il puntatore si allarga. Sotto i 1200 px una sotto l'altra.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'strapiombo'
NAME = 'Strapiombo'

HEX = {
    'primary': '#B52A12', 'primary_contrast': '#FFFFFF', 'secondary': '#5541D9', 'secondary_contrast': '#FFFFFF',
    'accent': '#F5C02E', 'dark': '#15171A', 'light': '#FFFFFF', 'muted': '#E3E6E0', 'muted_contrast': '#15171A',
    'text': '#15171A', 'text_muted': '#575C63', 'background': '#EEF0EC', 'border': '#CCD1CA', 'link': '#B52A12',
}

HOME, PALESTRA, CORSI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:la-palestra}}', '{{PAGINA:corsi}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'parete@strapiombo.example', '+39 0461 000 868', 'tel:+390461000868'
LAT, LNG = '46.0664', '11.1257'

V_SOTTO, V_PRESE, V_MAGNESITE, V_FALESIA, V_BIMBI = ('pexels-video-6702517', 'pexels-video-6703359', 'pexels-video-5964195',
                                                     'pexels-video-5963230', 'pexels-video-5916206')
PHOTOS = [
    'pexels-5383474.jpg', 'pexels-5384162.jpg', 'pexels-5384156.jpg', 'pexels-5384642.jpg', 'pexels-5916161.jpg',
    'pexels-5384172.jpg', 'pexels-5384630.jpg', 'pexels-5383759.jpg', 'pexels-5383781.jpg', 'pexels-5383475.jpg',
    'pexels-5384404.jpg', 'pexels-5383555.jpg', 'pexels-6642532.jpg', 'pexels-5384033.jpg', 'pexels-5383790.jpg',
    'pexels-5383559.jpg', 'pexels-6642529.jpg', 'pexels-8728582.jpg', 'pexels-5383524.jpg', 'pexels-5383466.jpg',
    'pexels-8481198.jpg', 'pexels-8728658.jpg', 'pexels-5383487.jpg', 'pexels-8728663.jpg', 'pexels-5384037.jpg',
    'pexels-5384025.jpg', 'pexels-5916169.jpg', 'pexels-2930163.jpg', 'pexels-28576495.jpg', 'pexels-16097561.jpg',
    'pexels-5384283.jpg', 'pexels-8728635.jpg', 'pexels-24964833.jpg', 'pexels-8728592.jpg', 'pexels-37516086.jpg',
    'pexels-5384018.jpg', 'pexels-8729207.jpg', 'pexels-5916221.jpg',
]
VIDEOS = [V_SOTTO, V_PRESE, V_MAGNESITE, V_FALESIA, V_BIMBI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='var(--olo-color-accent)')
C = K.c
GIALLO, VIOLA = C['acc2'], C['sec']
TITOLI, MONO = 'Schibsted Grotesk', 'JetBrains Mono'
VOCI = [('map-pin', 'Via del Brennero 220 · Trento', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Strapiombo', logo_size='22', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Strapiombo', 'Palestra di boulder a Trento: ottocento metri quadri di pareti, centoventi blocchi '
                   'cambiati ogni settimana, corsi per tutti e le uscite in falesia da aprile a ottobre.',
                   [('La palestra', PALESTRA), ('I corsi', CORSI), ('Contatti', CONTATTI), ('Le vie', PALESTRA + '#vie'),
                    ('Privacy', '/privacy-policy/')],
                   'La palestra', 'Lunedì — venerdì 10 — 23<br>Sabato e domenica 9 — 21', VOCI,
                   '© 2026 Strapiombo asd · Trento · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=140,
                   fondo=C['sala'])


# ── L'apertura: due metà, Dentro e Fuori ──
# Ogni metà è uno Slider Pro con la sua tela: sul computer la metà è larga circa metà della tela da 1440, quindi le
# misure sul computer sono il doppio di quelle che si vedono (128 = un titolo da 64). Tablet e telefono le hanno una
# sotto l'altra, a tutta larghezza, con le loro misure.
DENTRO = [  # linguetta, occhiello, riga 1, riga 2, testo, video, pulsante, link
    ('Boulder', 'Il boulder', 'Quattro metri,', 'nessuna corda.', 'Centoventi blocchi dal 3 al 7C, tracciati di nuovo ogni '
     'lunedì. Si cade sul materasso alto quaranta centimetri.', V_SOTTO, 'Prova un ingresso', PALESTRA + '#prezzi'),
    ('Corsi', 'I corsi', 'Dal primo blocco', 'al primo 6A.', 'Otto lezioni in gruppi di sei: come si cade, come si usano i '
     'piedi, come si legge un blocco prima di toccarlo.', V_PRESE, 'Prova gratis', CORSI),
    ('Bambini', 'I bambini', 'Arrampicare', 'è giocare.', 'Dai cinque anni, il sabato mattina e il mercoledì, con una '
     'parete tutta per loro e le prese a forma di animale.', V_BIMBI, 'I corsi junior', CORSI + '#orario'),
]
FUORI = [
    ('Falesia', 'La falesia', 'Da aprile,', 'sulla roccia vera.', 'Ogni domenica una falesia del Trentino con le guide '
     'alpine: Arco, la Valle dei Laghi, la Paganella.', V_FALESIA, 'Le uscite', CORSI + '#uscite'),
    ('Materiale', 'Il materiale', 'La corda,', 've la diamo noi.', 'Imbrago, corda, rinvii e casco li prestiamo noi; il '
     'pullmino parte alle otto dalla palestra.', V_MAGNESITE, 'Il calendario', CORSI + '#uscite'),
]


def meta(t, pid, nome, voci, transizione, durata_trans, ritmo):
    m = K.mob
    sl = []
    for i, (tab, occ, r1, r2, testo, video, bottone, link) in enumerate(voci):
        n = '%s%d' % (pid, i + 1)
        sl.append(K.diapositiva(n, K.sfondo_video(video, overlay=0.18, colore=C['nero']),
                                K.veli(n + '-velo', 'basso', 42, 64, colore=C['nero'], forza=88, mobile_da=44) + [
            K.livello(n + '-o', contenuto=occ, x=6, y=39, w=80, size=26, peso='600', colore=GIALLO, letterSpacing=4,
                      textTransform='uppercase', fontFamily=MONO, ritardo=200,
                      responsive={'notebook': {'fontSize': 14, 'y': 39}, 'tablet': {'fontSize': 14, 'y': 41},
                                  'mobile': {'fontSize': m(11), 'y': 50, 'x': 7, 'width': 86}}),
            # Le due righe del titolo in un livello solo (la seconda gialla): separate, con l'altezza dello schermo che
            # cambia e il carattere che segue la larghezza, in una finestra larga e bassa si toccavano (31 ott 2026).
            K.livello(n + '-t', contenuto=r1 + '<br><span style="color:' + GIALLO + '">' + r2 + '</span>',
                      tag='h1' if (pid == 'sd' and i == 0) else 'h2', x=6, y=44, w=88, size=128, peso='800',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=0.98, letterSpacing=-2, entra='maskFromBottom',
                      ritardo=300, durata=1100,
                      responsive={'notebook': {'fontSize': 64}, 'tablet': {'fontSize': 60, 'y': 46},
                                  'mobile': {'fontSize': m(32), 'y': 55, 'x': 7, 'width': 88}}),
            K.livello(n + '-s', contenuto=testo, x=6, y=72, w=76, size=34, peso='400', colore=mix(C['chiaro'], 88),
                      lineHeight=1.55, ritardo=850,
                      responsive={'notebook': {'fontSize': 17, 'width': 56}, 'tablet': {'fontSize': 17, 'y': 71, 'width': 60},
                                  'mobile': {'fontSize': m(14), 'y': 71, 'x': 7, 'width': 86}}),
            K.livello(n + '-b', tipo='button', contenuto=bottone, x=6, y=89, w='auto', size=26, peso='700',
                      colore=C['on_acc'], bgColor=C['acc'], padding=28, letterSpacing=2, textTransform='uppercase',
                      borderRadius=12, ritardo=1100, buttonUrl=link,
                      hover={'bgColor': C['chiaro'], 'color': C['nero'], 'duration': 250},
                      responsive={'notebook': {'fontSize': 13}, 'tablet': {'fontSize': 13, 'y': 85},
                                  'mobile': {'y': 88, 'x': 7, 'fontSize': m(12)}}),
        ], durata=ritmo, tab=tab))
    globali = [K.livello(pid + '-nome', contenuto=nome, x=6, y=6, w='auto', size=24, peso='600', colore=C['chiaro'],
                         letterSpacing=4, textTransform='uppercase', fontFamily=MONO, bgColor=mix(C['nero'], 72), padding=18,
                         borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 30), entra='fadeInDown', ritardo=150,
                         responsive={'notebook': {'fontSize': 12}, 'tablet': {'fontSize': 12},
                                     'mobile': {'fontSize': m(11), 'x': 7, 'y': 5}})]
    return K.proslider(t, sl, globali=globali, altezza={'mode': 'vh', 'value': 88}, transizione=transizione,
                       durata_trans=durata_trans, heightNotebook={'mode': 'vh', 'value': 88},
                       heightTablet={'mode': 'ratio', 'value': '4:3'}, heightMobile={'mode': 'px', 'value': 1000},
                       sizingMode='auto', showArrows=False, showDots=True, dotStyle='dash', showTabs=False, parallax=False,
                       autoplaySpeed=ritmo, progressBarColor=GIALLO, navColor=C['chiaro'],
                       globalBackground={'type': 'color', 'color': C['nero']})


def apertura(t):
    """Due metà quasi indipendenti: Dentro scorre dall'alto ogni 7 secondi, Fuori sfuma ogni 9 e mezzo; la metà sotto
    il puntatore si allarga (riga «Le colonne si allargano al passaggio»). Sotto i 1200 px una sotto l'altra."""
    return t.section([t.row([
        t.col([meta(t, 'sd', 'Dentro · la palestra', DENTRO, 'pushDown', 1000, 7000)], wm='1-2', ws='1-1'),
        t.col([meta(t, 'sf', 'Fuori · la falesia', FUORI, 'crossFade', 1400, 9500)], wm='1-2', ws='1-1'),
    ], layout='50-50', gap=0, anim=None, stack=True, stack_tablet=True, espandi_hover=True)],
        width='fullbleed', padding='remove-vertical')


def vie(t):
    return t.classic('worklist', {
        'items': [{'number': nu, 'title': ti, 'category': ca, 'year': gr, 'link_url': PALESTRA + '#vie'} for nu, ti, ca, gr in [
            ('GIALLA', 'La via del giorno', 'Settore Strapiombo · otto prese, un lancio finale', '6A'),
            ('BLU', 'Il diedro', 'Settore Placca · tutto di piedi', '5C'),
            ('ROSSA', 'Il tetto', 'Settore Tetto · quattro metri in orizzontale', '7A'),
            ('VERDE', 'Il primo', 'Settore Bambini · prese grandi, cadute basse', '4'),
            ('VIOLA', 'La gara di sabato', 'Settore Gara · si apre solo il sabato', '6C+')]],
        'divider_color': C['filo'], 'row_padding_y': 26, 'row_hover_bg': mix(C['acc'], 8), 'hover_indent': 28,
        'number_color': C['acc'], 'number_size': 13, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 34, 'title_weight': '800', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': VIOLA, 'year_size': 20, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': MONO}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [apertura(t)]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Strapiombo · Trento', ['Ottocento metri', 'di parete.'], size=64, layout='stack',
                                size_mobile=38)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Un ex magazzino della ferrovia, alto undici metri, diventato la più grande palestra di '
                                'boulder del Trentino. Ogni lunedì i tracciatori smontano metà dei blocchi e ne inventano di '
                                'nuovi: non si fa mai due volte la stessa via.', size=17, color=C['testo']),
                      K.button(t, 'La palestra', PALESTRA, kind='pieno', pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([K.numeri(t, [('800 m²', 'Di pareti'), ('120', 'Blocchi, dal 3 al 7C'), ('60', 'Blocchi nuovi ogni lunedì'),
                            ('45°', 'Lo strapiombo più ripido')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([t.one([t.classic('marquee', {
        'content_type': 'text', 'text_items': 'Tacca | Svaso | Pinza | Rovescio | Lancio | Tallonaggio | Incastro | Dinamico',
        'separator': ' ● ', 'speed': '30', 'direction': 'left', 'pause_hover': True, 'gap': 34, 'velocity_skew': True,
        'bg_color': C['testo'], 'text_color': C['chiaro'], 'font_size': 48, 'font_weight': '800', 'letter_spacing': -1,
        'text_transform': 'uppercase', 'font_family': 'heading', 'separator_color': GIALLO, 'height': 92, 'full_width': True})],
        anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'Le vie della settimana', ['Cinque colori,', 'cinque gradi.'], size=60,
                         tagline='Ogni blocco ha il colore delle sue prese e il grado sul cartellino. Passate su una via.')]),
        t.one([vie(t)]),
    ], padding='large', width='large', html_id='vie', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SOTTO, 'Lo strapiombo', 'Quarantacinque gradi, <em>a testa in giù.</em>',
                             'Nello strapiombo si sale coi piedi, non con le braccia: si tallona, si incastra, si sposta il '
                             'peso. Le braccia servono solo a non lasciare.', overlay=62))
    colpi = []
    for nome, cosa, foto in [('La tacca', 'Una presa piccola, da tenere con la punta delle dita. Si impara a fidarsi.', 'pexels-5384033.jpg'),
                             ('Lo svaso', 'Una presa tonda senza bordo: si tiene col palmo e con l’attrito.', 'pexels-5383555.jpg'),
                             ('Il tallonaggio', 'Il tallone appoggiato sopra la presa, come una terza mano.', 'pexels-5383487.jpg'),
                             ('Il lancio', 'Quando la presa è troppo lontana: si salta e si prende al volo.', 'pexels-5383781.jpg')]:
        colpi.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center', 'overlay_color': C['nero'], 'overlay_opacity': 88, 'overlay_fade': 'bottom', 'overlay_fade_reach': 75}, 'back_media': {'type': 'none'},
            'front_title': nome, 'front_description': 'I movimenti · girate la carta', 'front_text_color': C['chiaro'],
            'front_valign': 'bottom', 'front_overlay': '', 'back_title': nome, 'back_description': cosa,
            'back_bg': C['testo'], 'back_text_color': C['chiaro'], 'back_valign': 'center', 'back_cta_text': 'Impararlo',
            'back_cta_url': CORSI, 'flip_direction': 'vertical', 'flip_trigger': 'hover', 'card_height': '400',
            'card_border_radius': '6', 'card_shadow': 'md', 'title_size': '30', 'title_weight': '800', 'desc_size': '15'},
            style={})], wm='1-4', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'I movimenti', ['Quattro gesti', 'da girare.'], size=60,
                         tagline='L’arrampicata ha il suo vocabolario. Girate le carte, poi venite a provarlo sulla parete.')]),
        t.row(colpi, layout='25-25-25-25', gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [t.classic('testimonial', {
        'quote': 'Ho cominciato per accompagnare mia figlia al corso del sabato. Dopo un mese ero io a chiederle di venire: '
                 'adesso facciamo i blocchi gialli insieme, e lei mi batte.', 'author_name': 'Stefano B.',
        'author_role': 'Abbonato dal 2025', 'avatar': '', 'rating': '5', 'text_color': C['testo'], 'star_color': C['acc'],
        'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading', 'quote_size': 36,
        'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True, 'line_color': GIALLO},
        style={})])], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_MAGNESITE, 'La magnesite', 'Mani bianche, <em>testa vuota.</em>',
                          'Prima di un blocco difficile ci si ferma, ci si passa la magnesite e si guarda la via dal '
                          'basso. Tre respiri, poi si parte.', 'I corsi', CORSI, overlay=62, video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Strapiombo',
                page_intro_caption='Boulder · Trento', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1400, page_intro_once=True)


def build_palestra():
    t = Tpl(ID, 'la-palestra')
    out = [K.intro(t, 'La palestra', ['Un ingresso,', 'centoventi blocchi.'],
                   'Si entra quando si vuole, senza prenotare. Le scarpette si noleggiano alla cassa; la prima volta vi '
                   'spieghiamo come si cade.', bg=K.photo_bg('pexels-5384642.jpg', overlay=82, color=C['fondo']))]
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'Lunedì — venerdì', 'time': '10 — 23', 'note': 'Il lunedì mattina si tracciano i blocchi nuovi'},
                  {'day': 'Sabato e domenica', 'time': '9 — 21', 'note': 'I bambini il sabato mattina'},
                  {'day': 'Pausa pranzo', 'time': '12 — 14', 'note': 'L’ingresso ridotto a 9 €'}],
        'columns': 3, 'band_padding_y': 30, 'show_dividers': True, 'divider_color': C['filo'], 'band_border': False,
        'day_color': C['acc'], 'day_size': 12, 'time_font_family': 'heading', 'time_color': C['testo'], 'time_size': 44,
        'time_weight': '800', 'note_color': C['tenue'], 'note_size': 14, 'mono_font_family': ''}, style={})
    out.append(t.section([t.one([orari])], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 60}))
    out.append(t.section([
        t.one([K.sechead(t, 'I prezzi', ['Entrate,', 'arrampicate, tornate.'], size=56,
                         tagline='La tessera associativa costa 15 € all’anno e comprende l’assicurazione.')]),
        K.piani(t, [('Ingresso', '13', 'una giornata intera', 'Tutti i blocchi\nSpogliatoi e docce\nScarpette a noleggio 4 €',
                     False, ''),
                    ('Dieci ingressi', '115', 'senza scadenza', 'Anche condivisi con un amico\nUn noleggio scarpette in '
                     'regalo\nIl caffè al bar', False, ''),
                    ('Mensile', '62', 'al mese, senza vincoli', 'Ingressi illimitati\nLa zona allenamento\nSconto 20% sui '
                     'corsi', True, 'Il più scelto')],
                'Iscriviti', CONTATTI + '#scrivici', nota='Ridotto under 18 e over 65', sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='prezzi', style={'padding_top': 120, 'padding_bottom': 120}))
    classifica = t.classic('leaderboard', {
        'source': 'manual',
        'rows': [{'name': n, 'role': r, 'value': v, 'max': 60} for n, r, v in [
            ('Giulia Pedrotti', 'Chiusi tutti i viola', 54), ('Tommaso Bertoldi', 'Il re del tetto', 51),
            ('Lin Bassi', 'Istruttrice, fuori classifica', 49), ('Marco Zeni', 'Ha chiuso il 7A', 44),
            ('Sara Moser', 'Under 16', 41)]],
        'value_suffix': 'blocchi', 'show_position': True, 'show_value': True, 'show_role': True, 'animate_on_view': True,
        'highlight_top': 1, 'row_bg': C['chiaro'], 'text_color': C['testo'], 'role_color': C['tenue'],
        'position_color': C['tenue'], 'badge_bg': mix(C['acc'], 12), 'badge_color': C['acc'],
        'bar_track_color': mix(C['testo'], 8), 'bar_gradient_from': GIALLO, 'bar_gradient_to': C['acc'],
        'highlight_color': C['acc'], 'name_size': 20, 'name_weight': '800', 'position_size': 30,
        'border_radius': radius(6)}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La classifica', ['I blocchi', 'chiusi a ottobre.'], size=52, layout='stack',
                                size_mobile=34),
                      K.text(t, 'Chi vuole segna i blocchi chiusi sul cartellone vicino alla cassa. A fine mese chi ne ha '
                                'chiusi di più vince una maglietta e il primo blocco del mese dopo.')], wm='2-5', ws='1-1'),
               t.col([classifica], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_PRESE, 'Il lunedì', 'Sessanta blocchi <em>nuovi.</em>',
                             'Il lunedì mattina la palestra è chiusa: i tracciatori smontano le prese, le lavano e inventano '
                             'blocchi nuovi. Il pomeriggio si fa la coda per provarli.', overlay=62))
    out.append(K.domande(t, 'Prima', 'della prima volta.', [
        ('Serve esperienza?', 'No. La prima volta vi spieghiamo in dieci minuti come si cade e quali blocchi provare.'),
        ('Che cosa devo portare?', 'Vestiti comodi. Scarpette e magnesite si noleggiano alla cassa.'),
        ('Da che età si può venire?', 'Dai cinque anni coi corsi, dai dodici da soli con un genitore in palestra.'),
        ('C’è un armadietto?', 'Sì, negli spogliatoi, col lucchetto a combinazione. Le docce sono gratis.'),
    ], 'pa', bg=True))
    return out


def build_corsi():
    t = Tpl(ID, 'corsi')
    out = [K.intro(t, 'I corsi', ['Otto lezioni,', 'sei persone.'],
                   'Gruppi piccoli dello stesso livello, la prima lezione gratis. Gli istruttori sono tutti della '
                   'federazione.', bg=K.photo_bg('pexels-5384283.jpg', overlay=82, color=C['fondo']))]
    corsi = [('base', 'Base', C['acc'], 'Mai arrampicato: le cadute, i piedi, i primi blocchi.', 90),
             ('intermedio', 'Intermedio', VIOLA, 'Dal 5 al 6A: tecnica e lettura dei blocchi.', 90),
             ('forza', 'Forza e trave', mix(C['acc'], 55, C['testo']), 'Allenamento alla trave e al pannello.', 60),
             ('junior', 'Junior', mix(GIALLO, 80, C['acc']), 'Dai 5 ai 12 anni, col gioco.', 60),
             ('donne', 'Solo donne', mix(VIOLA, 55, C['acc']), 'Il giovedì sera, con Lin.', 90)]
    piano = [(1, '18:30', '20:00', 'base', 'Davide Rota', 'Strapiombo', 'base'), (1, '20:00', '21:30', 'intermedio', 'Lin Bassi', 'Placca', 'intermedio'),
             (2, '19:00', '20:00', 'forza', 'Davide Rota', 'Zona allenamento', 'avanzato'),
             (3, '17:00', '18:00', 'junior', 'Chiara Valsecchi', 'Bambini', 'base'), (3, '18:30', '20:00', 'base', 'Lin Bassi', 'Strapiombo', 'base'),
             (4, '19:00', '20:30', 'donne', 'Lin Bassi', 'Placca', 'tutti'), (4, '20:30', '21:30', 'forza', 'Davide Rota', 'Zona allenamento', 'avanzato'),
             (5, '18:30', '20:00', 'intermedio', 'Davide Rota', 'Tetto', 'intermedio'),
             (6, '09:30', '10:30', 'junior', 'Chiara Valsecchi', 'Bambini', 'base'), (6, '10:30', '11:30', 'junior', 'Chiara Valsecchi', 'Bambini', 'intermedio')]
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'sp-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': ins, 'sala': sala,
                     'livello': liv, 'posti': 6, 'nota': ''} for k, (g, a, b, c, ins, sala, liv) in enumerate(piano)],
        'giorni': 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato', 'ora_inizio': '09:00', 'ora_fine': '22:00',
        'mostra_filtri': True, 'etichetta_tutti': 'Tutti', 'filtro_livello': True, 'filtro_istruttore': True,
        'testo_vuoto': 'Nessuna lezione con questi filtri: togline uno.', 'testo_prenota': 'Prova gratis',
        'link_prenota': CONTATTI + '?corso={corso}&giorno={giorno}&ora={ora}#scrivici', 'colore_oggi': C['acc'],
        'colore_adesso': GIALLO, 'colore_tenue': C['testo'], 'giorno_font_family': 'heading', 'giorno_font_size': 20,
        'giorno_font_weight': '800', 'giorno_uppercase': True, 'corso_font_family': 'heading', 'corso_font_size': 17,
        'corso_font_weight': '800', 'ora_font_family': 'var(--olo-font-family-mono)', 'raggio_lezione': radius(6),
        'altezza_ora': 56, 'gap': 4, 'forma_filtri': 'pillola'}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'L’orario', ['Una settimana', 'di corsi.'], size=56,
                         tagline='Filtrate per livello o istruttore; toccate una lezione per prenotare la prova gratuita.')]),
        t.one([orario]),
    ], padding='large', width='large', html_id='orario', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli istruttori', ['Tre istruttori,', 'tutte le pareti.'], size=56)]),
        K.squadra(t, [
            ('pexels-5384018.jpg', 'Lin Bassi', 'Istruttrice federale', 'Ex campionessa italiana giovanile di boulder. Segue '
             'l’intermedio e il corso del giovedì solo per donne.', '50% 30%'),
            ('pexels-8729207.jpg', 'Davide Rota', 'Istruttore e tracciatore', 'Traccia metà dei blocchi nuovi del lunedì. '
             'Guida le uscite in falesia ad Arco.', '50% 30%'),
            ('pexels-5916221.jpg', 'Chiara Valsecchi', 'Corsi junior', 'Insegnante di educazione fisica, ha inventato la parete '
             'con le prese a forma di animale.', '50% 35%')], sfondo=C['chiaro']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_FALESIA, 'Le uscite', 'Ogni domenica, <em>in falesia.</em>',
                          'Da aprile a ottobre partiamo alle otto col pulmino: Arco, la Valle dei Laghi, la Paganella. Corda, '
                          'imbrago e casco li prestiamo noi. 45 € con la guida.', 'Prenota un’uscita',
                          CONTATTI + '#scrivici', overlay=62, video=True))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Venite a provare,', 'la prima è gratis.'],
                   'Per i corsi, le uscite, le feste di compleanno sulla parete o per tracciare con noi.',
                   bg=K.photo_bg('pexels-5383475.jpg', overlay=82, color=C['fondo']), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Che cosa', 'cosa', True,
                     opzioni='La lezione di prova\nUn corso\nUn’uscita in falesia\nUna festa di compleanno\nL’abbonamento'),
             K.campo('text', 'Corso', 'corso', param='corso'), K.campo('text', 'Giorno', 'giorno', param='giorno'),
             K.campo('text', 'Ora', 'ora', param='ora'), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('textarea', 'Note', 'messaggio', larghezza='1-1', segnaposto='Età, esperienza, domande.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Una prova,', 'un blocco giallo.'],
                                'Vi rispondiamo entro un giorno con le date libere. Per le feste servono due settimane di '
                                'anticipo.', VOCI, campi, 'Strapiombo — contatto', 'Invia', 'Ricevuto! Ci sentiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Nel magazzino', 'della ferrovia.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via del Brennero 220, Trento'), ('Treno', 'Trento Nord, a cinque minuti'),
                                  ('Parcheggio', 'cento posti, gratis'), ('Bar', 'aperto fino a chiusura')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Strapiombo · palestra di boulder', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · presa mancata', color=GIALLO, align='center'),
                       K.headline(t, 'Questa pagina <em>è caduta sul materasso.</em>', size=80, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=GIALLO),
                       K.text(t, 'Nessun danno: magnesite, e si riparte dal basso.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla parete', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_SOTTO, overlay=62), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'Dentro e fuori',
    ['arrampicata', 'boulder', 'palestra', 'corsi', 'trento', 'chiaro', 'slider pro', 'diviso'],
    'Una palestra di boulder, cemento chiaro e rosso segnale: in apertura due metà indipendenti, Dentro e Fuori, '
    'ognuna coi suoi video e il suo ritmo, e la metà sotto il puntatore che si allarga; le vie della settimana, i movimenti su carte da girare, i prezzi, la classifica e l’orario dei corsi filtrabile.',
    [('La palestra', PALESTRA), ('I corsi', CORSI), ('Le vie', HOME + '#vie'), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('la-palestra', 'La palestra', 'la-palestra'), ('corsi', 'I corsi', 'corsi'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Schibsted Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Schibsted Grotesk\', system-ui, sans-serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Schibsted Grotesk', 'JetBrains Mono'], peso_titoli='800',
    raggio=6, grana=0, titoli_line='0.98', titoli_spazio='-0.035em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '3'  # apertura divisa in due il 31 ottobre 2026
THEME['cursor']['style'] = 'scia'  # cursore: la scia di magnesite (31 ott 2026)


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'la-palestra.json': build_palestra(), 'corsi.json': build_corsi(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
