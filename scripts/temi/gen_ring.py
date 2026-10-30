# -*- coding: utf-8 -*-
"""Genera il tema «Ring» (id ring): una palestra popolare di pugilato nel Rione Sanità, a Napoli, dal 1978. Corsi per
ragazzi dai dieci anni, il martedì e il giovedì per le donne, gli agonisti tutti i giorni; la quota la paga chi può.
Scuro come una palestra di sera, con le luci sopra il ring.

Nero, il rosso dei guantoni, l'oro della cintura e il cuoio del sacco. Titoli in Anton, testo in Archivo, dati in Space
Mono.

Apertura: Slider Pro coi video veri (la sagoma nel ring, il sacco in controluce, la palestra rossa), tre round: il
cambio è un taglio diagonale (paperCut), il titolo arriva come un pugno (lettere che si rimpiccioliscono fino al loro
posto), l'occhiello si scopre dietro una lama rossa e la barra in basso si riempie come il cronometro del round. Poi
chi c'è stasera in palestra (Presence grid), i prossimi incontri (Match fixtures), i numeri (Counter), l'orario dei
corsi (Orario corsi), i maestri (Team), la palestra che si apre al passaggio (Pro gallery «expand») e le quote (Pricing).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'ring'
NAME = 'Ring'

HEX = {
    'primary': '#F0473B', 'primary_contrast': '#0B0A0A', 'secondary': '#5A1E19', 'secondary_contrast': '#F4EFE9',
    'accent': '#F2C14E', 'dark': '#050404', 'light': '#F4EFE9', 'muted': '#161312', 'muted_contrast': '#F1ECE6',
    'text': '#F1ECE6', 'text_muted': '#A89F98', 'background': '#0B0A0A', 'border': '#2A2422', 'link': '#F0473B',
}

HOME, CORSI, PALESTRA, ISCRIVITI = '{{PAGINA:home}}', '{{PAGINA:i-corsi}}', '{{PAGINA:la-palestra}}', '{{PAGINA:iscriviti}}'
MAIL, PHONE, PHONE_TEL = 'ring@ringsanita.example', '+39 081 000 1978', 'tel:+390810001978'
LAT, LNG = '40.8597', '14.2496'

V_BENDE, V_CORDA, V_ROSSA, V_SAGOMA, V_GUANTI, V_GIALLO, V_CATENA, V_SACCO = (
    'pexels-video-8736285', 'pexels-video-4761426', 'pexels-video-5752073', 'pexels-video-8478320', 'pexels-video-9944847',
    'pexels-video-4438072', 'pexels-video-5263694', 'pexels-video-8478081')
PHOTOS = [
    'pexels-9944002.jpg', 'pexels-6198812.jpg', 'pexels-4398908.jpg', 'pexels-6295796.jpg', 'pexels-4398904.jpg',
    'pexels-4401807.jpg', 'pexels-3871133.jpg', 'pexels-4401815.jpg', 'pexels-27616646.jpg', 'pexels-6295870.jpg',
    'pexels-6166440.jpg', 'pexels-6296124.jpg', 'pexels-27302386.jpg', 'pexels-9944239.jpg', 'pexels-9944851.jpg',
    'pexels-4804072.jpg', 'pexels-4401810.jpg', 'pexels-9944844.jpg', 'pexels-6295781.jpg', 'pexels-6295857.jpg',
    'pexels-33408974.jpg', 'pexels-9944753.jpg', 'pexels-7777424.jpg', 'pexels-9944665.jpg', 'pexels-5750873.jpg',
    'pexels-9945193.jpg', 'pexels-4401814.jpg', 'pexels-598664.jpg', 'pexels-9945197.jpg', 'pexels-9944893.jpg',
    'pexels-4164521.jpg', 'pexels-4164517.jpg', 'pexels-33408976.jpg', 'pexels-5535931.jpg', 'pexels-9943223.jpg',
    'pexels-9945112.jpg', 'pexels-12859238.jpg', 'pexels-6293165.jpg',
]
VIDEOS = [V_BENDE, V_CORDA, V_ROSSA, V_SAGOMA, V_GUANTI, V_GIALLO, V_CATENA, V_SACCO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Vico Lammatari 12, Rione Sanità · Napoli', ISCRIVITI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'RING', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'RING', 'Palestra popolare di pugilato nel Rione Sanità dal 1978. Un ring, quattordici sacchi, il '
                   'maestro Gennaro e trecentoventi iscritti. Chi non può, non paga.',
                   [('I corsi', CORSI), ('La palestra', PALESTRA), ('Iscriviti', ISCRIVITI), ('Dove siamo', ISCRIVITI + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Gli orari', 'Dal lunedì al sabato, dalle 15 alle 22. La domenica mattina il ring è aperto a chi vuole '
                   'fare sparring, con un maestro.', VOCI, '© 2026 ASD Ring Sanità · Napoli · affiliata FPI',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=170)


def apertura(t):
    m = K.mob
    round_ = [
        ('Round 1 · 3:00', 'In\nguardia.', 'Una palestra popolare nel cuore della Sanità, dal 1978. Si comincia dai '
         'piedi, poi le mani, poi la testa.', K.sfondo_video(V_SAGOMA, overlay=0.35)),
        ('Round 2 · 3:00', 'Le mani\nnon mentono.', 'Tre sere di sacco a settimana e in un mese lo senti: nel fiato, nelle '
         'spalle, nel modo in cui cammini per strada.', K.sfondo_video(V_SACCO, overlay=0.45)),
        ('Round 3 · 3:00', 'Il ring\nè di tutti.', 'Ragazzi dai dieci anni, donne il martedì e il giovedì, agonisti tutti i '
         'giorni. La quota la paga chi può.', K.sfondo_video(V_ROSSA, overlay=0.55)),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo) in enumerate(round_):
        n = i + 1
        sl.append(K.diapositiva('ri-%d' % n, sfondo, [
            K.livello('ri-o%d' % n, contenuto=occ, x=6, y=24, w='auto', size=14, peso='700', colore=C['acc'], ritardo=150,
                      durata=600, entra='fadeIn', letterSpacing=3, textTransform='uppercase', fontFamily='Space Mono',
                      sfx={'effect': 'blockRight', 'color': C['acc'], 'duration': 800},
                      responsive={'mobile': {'fontSize': m(12), 'y': 12}}),
            K.livello('ri-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=29, w=70, size=170, peso='400',
                      colore=C['chiaro'], ritardo=500, durata=700, entra='fadeIn', lineHeight=0.88, letterSpacing=0,
                      textTransform='uppercase', charAnim={'stagger': 45, 'scale': 2.4, 'opacity': 0, 'blur': 4},
                      textShadow={'x': 0, 'y': 3, 'blur': 30, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(70), 'y': 16, 'width': 90}}),
            K.livello('ri-s%d' % n, contenuto=testo, x=6, y=70, w=34, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1400, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 37, 'width': 88}}),
            K.livello('ri-b%d' % n, tipo='button', contenuto='Prova gratis', x=60, y=74, w='auto', size=14, peso='700',
                      colore=C['on_acc'], bgColor=C['acc'], padding=18, letterSpacing=2, textTransform='uppercase',
                      ritardo=1700, buttonUrl=ISCRIVITI + '#scrivici', hover={'bgColor': C['acc2'], 'scale': 1.04, 'duration': 200},
                      responsive={'mobile': {'x': 6, 'y': 52, 'fontSize': m(13)}}),
        ], durata=6500))
    globali = [K.livello('ri-live', contenuto='● Stasera aperto fino alle 22', x=64, y=14, w=32, size=12, peso='700',
                         colore=C['chiaro'], align='right', entra='fadeIn', ritardo=400, letterSpacing=2,
                         textTransform='uppercase', fontFamily='Space Mono', animLoop='pulse', animLoopDuration=2000,
                         responsive={'mobile': {'visible': False}})]
    return K.proslider(t, sl, globali=globali, transizione='paperCut', durata_trans=800, showArrows=True, showDots=False,
                       showProgressBar=True, progressBarHeight=6, parallax=True, parallaxIntensity=3)


def contatore(t, numero, etichetta, suffisso=''):
    return t.classic('counter', {
        'number': numero, 'label': etichetta, 'prefix': '', 'suffix': suffisso, 'icon_emoji': '', 'text_color': C['acc'],
        'number_font_size': '84', 'number_font_weight': '400', 'label_color': C['testo'], 'label_font_size': '13',
        'label_font_weight': '700', 'bg_type': 'color', 'bg_color': '',
    }, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    stasera = t.classic('presencegrid', {
        'source': 'manual',
        'members': [{'name': n, 'avatar': K.img(f), 'role': r, 'online': o} for n, f, r, o in [
            ('Ciro', 'pexels-4398904.jpg', 'Elite · 69 kg', True), ('Assunta', 'pexels-9944753.jpg', 'Corso donne', True),
            ('Gennaro', 'pexels-6293165.jpg', 'Il maestro', True), ('Lucia', 'pexels-9945197.jpg', 'Junior · 57 kg', True),
            ('Totò', 'pexels-6295796.jpg', 'Ragazzi', False), ('Rita', 'pexels-9943223.jpg', 'Corso donne', True),
            ('Salvatore', 'pexels-4401807.jpg', 'Amatori', True), ('Carmela', 'pexels-9945112.jpg', 'Elite · 60 kg', False)]],
        'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'gap': 14, 'show_ranks': False, 'online_label': 'In palestra',
        'offline_label': 'Arriva alle 20', 'show_ticker': True,
        'ticker_text': 'Sparring alle 20 sul ring grande · porta le bende · stasera c’è la pizza per chi ha fatto il compito',
        'ticker_speed': 26, 'ticker_bg': C['acc'], 'ticker_color': C['on_acc'], 'card_bg': C['sala'], 'card_color': C['testo'],
        'role_color': C['acc2'], 'avatar_size': 84, 'avatar_shape': 'rounded', 'online_color': C['acc'],
        'offline_color': C['tenue'], 'card_radius': radius(0), 'card_hover_effect': 'scale',
        'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Stasera', ['Chi c’è', 'in palestra.'], size=80,
                         tagline='La palestra apre alle 15. Alle 20 c’è lo sparring sul ring grande: si guarda anche senza '
                                 'guantoni.')]),
        t.one([stasera]),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    incontri = t.classic('matchfixtures', {
        'items': [{'day': d, 'time_place': tp, 'league': lg, 'matchday': md, 'home_crest': hc, 'home_crest_bg': C['acc'],
                   'home_name': hn, 'away_crest': ac, 'away_crest_bg': C['chiaro'], 'away_name': an, 'score': sc,
                   'venue': ''} for d, tp, lg, md, hc, hn, ac, an, sc in [
            ('Sab 14.11', '20:30 · Palazzetto Caserta', 'Elite · 69 kg', 'Campionato regionale', 'CE', 'Ciro Esposito',
             'MR', 'Marco Russo', ''),
            ('Sab 21.11', '19:00 · Ring Sanità', 'Junior · 57 kg', 'Riunione di casa', 'LC', 'Lucia Cuomo', 'AD',
             'Anna De Luca', ''),
            ('Sab 05.12', '21:00 · PalaVesuvio', 'Elite · 60 kg', 'Finale regionale', 'CA', 'Carmela Amato', 'GF',
             'Giulia Ferraro', '')]],
        'columns': 3, 'gap': 14, 'card_bg': C['sala'], 'card_border': C['filo'], 'accent': C['acc'], 'day_color': C['testo'],
        'meta_color': C['tenue'], 'name_color': C['testo'], 'score_color': C['testo'], 'crest_text_color': C['on_acc'],
        'radius': 0,
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'I prossimi incontri', ['Sul ring', 'coi nostri colori.'], size=64,
                         tagline='Gli agonisti combattono per la Ring Sanità dal 1981. Venite a fare il tifo: si entra gratis '
                                 'alle riunioni di casa.')]),
        t.one([incontri]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_BENDE, 'Prima dei guanti', 'Le bende, sempre.', 'Quattro metri e mezzo di cotone per mano, '
                          'avvolti così da quarant’anni. Il maestro te lo insegna la prima sera, e poi ti controlla ogni volta.',
                          'I corsi', CORSI, overlay=50, video=True))
    out.append(t.section([
        t.row([t.col([contatore(t, '1978', 'Anno di apertura')], wm='1-4', ws='1-2', width_default='1-2'),
               t.col([contatore(t, '320', 'Iscritti')], wm='1-4', ws='1-2', width_default='1-2'),
               t.col([contatore(t, '86', 'Ragazzi che non pagano')], wm='1-4', ws='1-2', width_default='1-2'),
               t.col([contatore(t, '12', 'Titoli regionali')], wm='1-4', ws='1-2', width_default='1-2')],
              layout='25-25-25-25', gap=24, valign='start'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_SAGOMA, 'Il maestro dice', 'Qui nessuno <em>combatte da solo.</em>',
                             'Chi arriva il primo giorno trova qualcuno che gli tiene il sacco. Dopo un mese, lo tiene lui '
                             'a chi arriva.', overlay=50,
                             extra=[K.button(t, 'Vieni a provare', ISCRIVITI + '#scrivici', kind='pieno', align='center', pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='RING',
                page_intro_caption='Sanità · dal 1978', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def build_corsi():
    t = Tpl(ID, 'i-corsi')
    out = [K.intro(t, 'I corsi', ['Dai dieci anni', 'in su.'], 'Cinque corsi, dal lunedì al sabato. La prima settimana è '
                   'gratis per tutti: ti presti i guantoni, le bende le compri al bancone per tre euro.',
                   bg=K.video_bg(V_CORDA, overlay=55), overlay_text=True, size=110)]
    corsi = [('ragazzi', 'Boxe ragazzi', C['acc2'], 'Dai 10 ai 15 anni: coordinazione, corda, sacco. Niente colpi al viso '
              'fino ai 14.', 60),
             ('donne', 'Boxe donne', C['acc'], 'Solo donne, con la maestra Rita: tecnica, sacco e sparring leggero per chi '
              'vuole.', 75),
             ('adulti', 'Boxe adulti', mix(C['acc'], 60, C['chiaro']), 'Per tutti dai 16 anni: tecnica, preparazione e '
              'sacco. Lo sparring solo quando lo decide il maestro.', 90),
             ('agonisti', 'Agonisti', C['chiaro'], 'Per chi combatte: preparazione, sparring, strategia degli incontri.', 120),
             ('atletica', 'Preparazione atletica', mix(C['acc2'], 60, C['chiaro']), 'Corda, circuito, fiato. Senza guantoni, '
              'per chi vuole solo allenarsi.', 60)]
    lezioni = [(1, '16:00', '17:00', 'ragazzi', 'Totò Ruggiero', 'Sala sacchi', 'base', 20),
               (1, '19:00', '20:30', 'adulti', 'Gennaro Pagano', 'Ring grande', 'tutti', 24),
               (2, '18:00', '19:15', 'donne', 'Rita Iorio', 'Sala sacchi', 'tutti', 16),
               (2, '20:00', '22:00', 'agonisti', 'Gennaro Pagano', 'Ring grande', 'avanzato', 12),
               (3, '16:00', '17:00', 'ragazzi', 'Totò Ruggiero', 'Sala sacchi', 'base', 20),
               (3, '19:00', '20:00', 'atletica', 'Rita Iorio', 'Cortile', 'tutti', 20),
               (4, '18:00', '19:15', 'donne', 'Rita Iorio', 'Sala sacchi', 'tutti', 16),
               (4, '19:00', '20:30', 'adulti', 'Gennaro Pagano', 'Ring grande', 'tutti', 24),
               (5, '20:00', '22:00', 'agonisti', 'Gennaro Pagano', 'Ring grande', 'avanzato', 12),
               (6, '10:00', '11:00', 'ragazzi', 'Totò Ruggiero', 'Sala sacchi', 'base', 20)]
    orario = t.classic('orariocorsi', {
        'corsi': [{'id': i, 'nome': n, 'colore': col, 'descrizione': d, 'durata': du} for i, n, col, d, du in corsi],
        'lezioni': [{'id': 'ri-l%d' % k, 'giorno': g, 'inizio': a, 'fine': b, 'corso': c, 'istruttore': ist, 'sala': sala,
                     'livello': liv, 'posti': p, 'nota': ''} for k, (g, a, b, c, ist, sala, liv, p) in enumerate(lezioni)],
        'giorni': 'Lunedì,Martedì,Mercoledì,Giovedì,Venerdì,Sabato,Domenica', 'vista': 'griglia', 'ora_inizio': '10:00',
        'ora_fine': '22:00', 'mostra_filtri': True, 'etichetta_tutti': 'Tutti i corsi', 'filtro_livello': True,
        'filtro_istruttore': True, 'testo_vuoto': 'Nessun corso con questi filtri.', 'evidenzia_oggi': True,
        'mostra_adesso': True, 'dettagli': True, 'testo_prenota': 'Prenota la prova',
        'link_prenota': ISCRIVITI + '?corso={corso}&giorno={giorno}#scrivici', 'colore_oggi': C['acc'],
        'colore_adesso': C['acc2'], 'ombra': 'none', 'giorno_font_family': 'heading', 'giorno_font_size': 22,
        'giorno_font_weight': '400', 'giorno_uppercase': True,
    }, style={})
    out.append(t.section([t.one([orario])], padding='large', width='large', html_id='orario', style={'padding_top': 70}))

    def maestro(foto, nome, ruolo, bio):
        return t.classic('team', {
            'photo': K.img(foto), 'name': nome, 'role': ruolo, 'bio': bio, 'link_text': '', 'link_url': '',
            'photo_size': '160', 'photo_ratio': '1/1', 'photo_fit': 'cover', 'photo_shape': 'square', 'photo_radius': '0',
            'photo_border_width': '0', 'photo_shadow': 'none', 'info_bg_color': C['sala'], 'info_text_color': C['testo'],
            'info_radius': '0', 'info_align': 'left', 'role_color': C['acc'], 'name_weight': '400', 'name_size': '30',
            'bg_color': 'transparent',
        }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'I maestri', ['Tre angoli,', 'un solo ring.'], size=72)]),
        t.row([t.col([maestro('pexels-6293165.jpg', 'Gennaro Pagano', 'Maestro · dal 1978',
                              'Ha aperto la palestra a ventisei anni, dopo quaranta incontri da dilettante. Non ha mai '
                              'chiuso un giorno, nemmeno col terremoto.')], wm='1-3', ws='1-1'),
               t.col([maestro('pexels-9944665.jpg', 'Rita Iorio', 'Maestra · corso donne',
                              'Campionessa italiana nel 2011. Il martedì e il giovedì la palestra è sua.')], wm='1-3', ws='1-1'),
               t.col([maestro('pexels-6295796.jpg', 'Totò Ruggiero', 'Istruttore · ragazzi',
                              'È entrato qui a undici anni. Oggi insegna a chi ha l’età che aveva lui.')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=20, valign='stretch'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'di salire sul ring.', [
        ('Mi faranno a pugni il primo giorno?', 'No. Lo sparring arriva dopo mesi, e solo se lo vuoi tu e lo decide il '
                                                'maestro. Il primo giorno si salta la corda.'),
        ('Serve il certificato medico?', 'Sì: quello sportivo non agonistico per i corsi, l’agonistico per chi combatte.'),
        ('Cosa porto?', 'Scarpe da ginnastica, una maglietta, una borraccia. Guantoni e corda te li prestiamo.'),
        ('E se non posso pagare?', 'Lo dici al maestro, e basta. Ottantasei ragazzi si allenano gratis: nessuno lo sa, '
                                   'nessuno lo chiede.'),
    ], 'co', bg=False))
    return out


def build_palestra():
    t = Tpl(ID, 'la-palestra')
    out = [K.intro(t, 'La palestra', ['Un ring,', 'quattordici sacchi.'],
                   'Un ex deposito di carrozze sotto il ponte della Sanità. Il ring l’ha costruito il maestro con il '
                   'falegname del vicolo, nel 1978. Il pavimento è ancora quello.',
                   bg=K.photo_bg('pexels-9944239.jpg', overlay=55), overlay_text=True, size=110)]
    galleria = t.classic('progallery', {
        'images': [{'url': K.img(f), 'alt': a, 'caption': c, 'subtitle': s, 'text': ''} for f, a, c, s in [
            ('pexels-6296124.jpg', 'Guantoni appesi al muro', 'I guantoni', 'Quelli di tutti'),
            ('pexels-27302386.jpg', 'Due pugili sul ring in bianco e nero', 'Il ring', 'Dal 1978'),
            ('pexels-4164521.jpg', 'Un sacco nero con le catene', 'I sacchi', 'Quattordici'),
            ('pexels-7777424.jpg', 'Una ragazza si allena nel fumo', 'Il martedì', 'Corso donne'),
            ('pexels-5535931.jpg', 'Un gruppo si allena ai sacchi', 'La sera', 'Tutti insieme'),
            ('pexels-598664.jpg', 'Un pugile alza le braccia sul ring', 'La vittoria', 'Riunione di casa')]],
        'layout': 'expand', 'layout_family': 'classic', 'columns': 6, 'gap': 6, 'img_height': '520px', 'object_fit': 'cover',
        'thumb_radius': 0, 'mobile_columns': 2, 'expand_ratio': 3.2, 'expand_shrink': 0.55, 'expand_speed': 500,
        'entrance': 'none', 'hover_effect': 'none',
    }, style={})
    out.append(t.section([t.one([galleria])], padding='large', width='fullbleed', style={'padding_top': 70, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([K.label(t, 'Com’è fatta', color=C['acc']), K.headline(t, 'Il ring, i sacchi, <em>il cortile.</em>',
                                                                           size=72, tag='h2', pad_bottom=14),
                      K.text(t, 'Il ring grande da sei metri, la sala dei sacchi con le catene al soffitto, il cortile per la '
                                'corda e la preparazione quando fa bello. Le docce sono nuove: le hanno pagate gli iscritti '
                                'con la lotteria di Natale.'),
                      K.specs(t, [('Ring', '6 × 6 metri, corde nuove dal 2024'), ('Sacchi', '14, da 30 a 60 chili'),
                                  ('Spogliatoi', 'due, con le docce calde'), ('Aperta', 'lunedì — sabato, 15 — 22')])],
                     wm='1-2', ws='1-1'),
               t.col([K.image(t, 'pexels-9944851.jpg', 'Un pugile si allena fra i sacchi', ratio='4/5', drift=True)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_CATENA, 'Il sacco di Gennaro', 'Quarant’anni di pugni.', 'Il primo sacco della palestra è '
                          'ancora appeso, rattoppato col nastro americano. Ci si allena solo il maestro, e chi vince un titolo.',
                          'I corsi', CORSI, overlay=45, video=True))
    out.append(K.video_frase(t, V_GUANTI, 'La regola', 'Fuori dal ring, <em>si è tutti amici.</em>',
                             'Il maestro lo dice alla fine di ogni sparring. Poi ci si abbraccia, anche quando brucia.',
                             overlay=50))
    return out


def build_iscriviti():
    t = Tpl(ID, 'iscriviti')
    out = [K.intro(t, 'Iscriviti', ['La prima settimana', 'è gratis.'],
                   'Vieni quando vuoi dalle 15 alle 22, chiedi del maestro. Se dopo una settimana vuoi restare, ti '
                   'iscrivi; se non puoi pagare, lo dici a lui e basta.', bg=K.video_bg(V_GIALLO, overlay=55),
                   overlay_text=True, size=110)]

    def quota(nome, prezzo, periodo, voci, popolare=False, info=''):
        return t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': periodo,
            'features': '\n'.join(voci), 'cta_text': 'Vieni a provare', 'cta_url': ISCRIVITI + '#scrivici',
            'cta_bg_color': C['acc'], 'cta_text_color': C['on_acc'], 'cta_radius': '0', 'cta_hover_bg_color': C['acc2'],
            'cta_hover_text_color': C['on_acc'], 'additional_info': info, 'is_popular': popolare, 'badge_text': 'Il più scelto',
            'badge_bg_color': C['acc2'], 'badge_text_color': C['on_acc'], 'badge_radius': '0', 'bg_color': C['sala'],
            'accent_color': C['acc'], 'text_color': C['testo'], 'price_color': C['acc'], 'border_radius': '0',
            'shadow': 'none', 'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid',
                                         'color': C['filo']},
        })
    out.append(t.section([
        t.row([t.col([quota('Ragazzi', '10', 'al mese', ['Dai 10 ai 15 anni', 'Tre lezioni a settimana', 'Guantoni in prestito'],
                            info='Chi non può, non paga')], wm='1-3', ws='1-1'),
               t.col([quota('Adulti', '35', 'al mese', ['Tutti i corsi', 'Sala sacchi libera', 'Preparazione atletica'],
                            popolare=True, info='Iscrizione e assicurazione 20 € l’anno')], wm='1-3', ws='1-1'),
               t.col([quota('Agonisti', '45', 'al mese', ['Allenamento tutti i giorni', 'Sparring col maestro',
                                                            'Trasferte pagate dalla palestra'], info='Su invito del maestro')],
                     wm='1-3', ws='1-1')], layout='33-33-33', gap=20, valign='stretch'),
    ], padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('select', 'Il corso', 'corso', True, param='corso',
                     opzioni='Boxe ragazzi\nBoxe donne\nBoxe adulti\nPreparazione atletica\nNon so ancora'),
             K.campo('select', 'Età', 'eta', opzioni='10 — 15 anni\n16 — 25 anni\nPiù di 25 anni'),
             K.campo('textarea', 'Scrivici', 'messaggio', larghezza='1-1', segnaposto='Hai già fatto boxe? Che sere preferisci?')]
    out.append(K.sezione_modulo(t, 'scrivici', 'La prova', ['Vieni', 'a sudare.'],
                                'Ti chiamiamo per dirti quando c’è il corso giusto. Oppure passa e basta: la porta è aperta.',
                                VOCI, campi, 'Ring — prova gratuita', 'Prenota la prova', 'Ricevuto. Ci vediamo in palestra.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Sotto il ponte', 'della Sanità.'], size=60, layout='stack', size_mobile=38),
                      K.specs(t, [('Indirizzo', 'vico Lammatari 12, Napoli'), ('Metro', 'Linea 1, Museo, poi 10 minuti'),
                                  ('Ascensore', 'dal ponte della Sanità'), ('Bus', 'C51 e 168')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Ring Sanità · palestra popolare', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · colpo a vuoto', color=C['acc'], align='center'),
                       K.headline(t, 'Hai mancato <em>il bersaglio.</em>', size=120, tag='h1', align='center', pad_bottom=20,
                                  maiuscolo=True),
                       K.text(t, 'Rimettiti in guardia e torna all’inizio.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_GUANTI, overlay=55), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'Tre round',
    ['pugilato', 'boxe', 'palestra', 'napoli', 'sport', 'corsi', 'scuro', 'slider pro'],
    'Una palestra popolare di pugilato a Napoli, nero col rosso dei guantoni: l’apertura in tre round col taglio '
    'diagonale, i titoli che arrivano come un pugno e la barra che si riempie come il cronometro, chi c’è stasera in '
    'palestra, i prossimi incontri, l’orario dei corsi, i maestri, la palestra che si apre al passaggio e le quote.',
    [('I corsi', CORSI), ('La palestra', PALESTRA), ('Dove siamo', ISCRIVITI + '#arrivare'), ('Prova gratis', ISCRIVITI)],
    [('home', 'Home', None), ('i-corsi', 'I corsi', 'i-corsi'), ('la-palestra', 'La palestra', 'la-palestra'),
     ('iscriviti', 'Iscriviti', 'iscriviti')],
    HEX, '\'Archivo\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Anton\', \'Impact\', \'Arial Narrow\', sans-serif', '\'Space Mono\', ui-monospace, Menlo, monospace',
    ['Anton', 'Archivo', 'Space Mono'], peso_titoli='400', raggio=0, grana=6, titoli_line='0.95', titoli_spazio='0')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-corsi.json': build_corsi(), 'la-palestra.json': build_palestra(), 'iscriviti.json': build_iscriviti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
