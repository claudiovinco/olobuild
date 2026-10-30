# -*- coding: utf-8 -*-
"""Genera il tema «Kaamos» (id kaamos): un lodge di cabine di vetro sopra il Circolo Polare, a Kittilä, tenuto da due
italiani. Scuro come la notte polare.

Blu di notte polare, il verde dell'aurora, il viola delle sue frange e la luce calda delle lanterne. Titoli in
Unbounded, testo in Nunito Sans, dati (gradi, latitudine, probabilità) in Azeret Mono.

Apertura: Slider Pro coi video veri delle aurore, transizione a sfocatura, lettere che salgono dal buio, una fascia di
luce verde che respira sopra il titolo e un'etichetta fissa con la latitudine e la temperatura di adesso. Poi l'ora di
Kittilä accanto a quella di casa (Timezone), le quattro cabine (Showcase grid), il calendario delle notti libere col
prezzo (Soggiorni), le esperienze che si svelano di lato, la cena davanti al fuoco.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'kaamos'
NAME = 'Kaamos'

HEX = {
    'primary': '#5CF2B0', 'primary_contrast': '#04121A', 'secondary': '#8E7BFF', 'secondary_contrast': '#04121A',
    'accent': '#FFB86B', 'dark': '#03060A', 'light': '#F2F7F9', 'muted': '#0C1520', 'muted_contrast': '#E6EEF2',
    'text': '#E6EEF2', 'text_muted': '#93A3AF', 'background': '#060B12', 'border': '#1B2836', 'link': '#5CF2B0',
}

HOME, LODGE, ESP, ARRIVARE = '{{PAGINA:home}}', '{{PAGINA:il-lodge}}', '{{PAGINA:esperienze}}', '{{PAGINA:arrivare}}'
MAIL, PHONE, PHONE_TEL = 'notti@kaamos.example', '+358 40 000 6868', 'tel:+358400006868'
LAT, LNG = '67.6950', '24.8450'

V_AURORA1, V_AURORA2, V_AURORA3 = 'pexels-video-31401354', 'pexels-video-5600930', 'pexels-video-28889010'
V_AURORA4, V_SLITTA, V_NEVE = 'pexels-video-11860943', 'pexels-video-37483076', 'pexels-video-6415315'
PHOTOS = [
    'pexels-6386260.jpg', 'pexels-91216.jpg', 'pexels-28344022.jpg', 'pexels-36245503.jpg', 'pexels-37843545.jpg',
    'pexels-11869673.jpg', 'pexels-28237726.jpg', 'pexels-19584291.jpg', 'pexels-31177223.jpg', 'pexels-19306889.jpg',
    'pexels-10661646.jpg', 'pexels-28811165.jpg', 'pexels-30654164.jpg', 'pexels-31038013.jpg', 'pexels-13643451.jpg',
    'pexels-34911269.jpg', 'pexels-36152821.jpg', 'pexels-35836363.jpg', 'pexels-36152822.jpg', 'pexels-31092918.jpg',
    'pexels-31092902.jpg', 'pexels-36072601.jpg', 'pexels-11578665.jpg', 'pexels-23939046.jpg', 'pexels-31312071.jpg',
    'pexels-20242213.jpg', 'pexels-6793801.jpg', 'pexels-7746106.jpg', 'pexels-4572734.jpg', 'pexels-7245418.jpg',
    'pexels-6881069.jpg', 'pexels-6803600.jpg', 'pexels-6232530.jpg', 'pexels-6232603.jpg',
]
VIDEOS = [V_AURORA1, V_AURORA2, V_AURORA3, V_AURORA4, V_SLITTA, V_NEVE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Levitunturi, Kittilä · Lapponia finlandese', ARRIVARE), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'KAAMOS', logo_size='20')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'KAAMOS', 'Quattro cabine di vetro e una sauna sopra il Circolo Polare, a dieci minuti da Levi. Le '
                   'teniamo Marta e Luca, da Bergamo, dal 2019. Si parla italiano, anche alle due di notte.',
                   [('Il lodge', LODGE), ('Le esperienze', ESP), ('Come arrivare', ARRIVARE), ('Le notti libere', LODGE + '#notti'),
                    ('Privacy', '/privacy-policy/')],
                   'Kaamos', 'La notte polare: dal 10 dicembre al 2 gennaio il sole non sorge. È la stagione più buia, e la '
                   'più bella per le aurore.', VOCI, '© 2026 Kaamos Lodge Oy · Kittilä · Y-tunnus 0000000-0',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=150)


def apertura(t):
    m = K.mob
    capitoli = [
        ('Stanotte, aurora al 78%', 'Dormi sotto\nil cielo che danza.', 'Quattro cabine di vetro a 68° nord. Il tetto è '
         'trasparente: se l’aurora arriva alle tre, la vedi dal cuscino.', K.sfondo_video(V_AURORA1, overlay=0.25)),
        ('Kaamos · la notte polare', 'Ventitré giorni\nsenza sole.', 'Da metà dicembre il sole resta sotto l’orizzonte. Il '
         'cielo diventa blu alle undici e nero alle due: è il nostro mese preferito.', K.sfondo_video(V_AURORA3, overlay=0.3)),
        ('Il lago ghiacciato', 'La luce\nsi specchia due volte.', 'Quando il lago di Immeljärvi gela, l’aurora si specchia '
         'sul ghiaccio. Ci andiamo a piedi, con le ciaspole e il termos.', K.sfondo_foto('pexels-19584291.jpg', overlay=0.25)),
        ('La cabina', 'Il fuoco dentro,\nmeno trenta fuori.', 'Letto king, stufa a legna, sauna privata. E una sveglia che '
         'suona da sola quando il sensore vede l’aurora.', K.sfondo_foto('pexels-91216.jpg', overlay=0.3, verso='out')),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo) in enumerate(capitoli):
        n = i + 1
        sl.append(K.diapositiva('ka-%d' % n, sfondo, [
            K.livello('ka-l%d' % n, tipo='shape', x=4, y=24, w=40, h=1.2, entra='fadeIn', ritardo=300, durata=1600,
                      shapeGradient={'angle': 90, 'from': 'transparent', 'to': C['acc']}, opacity=70,
                      animLoop='pulse', animLoopDuration=3600, responsive={'mobile': {'y': 12, 'width': 80}}),
            K.livello('ka-o%d' % n, contenuto=occ, x=6, y=27, w=50, size=13, peso='400', colore=C['acc'], ritardo=200,
                      letterSpacing=3, textTransform='uppercase', fontFamily='Azeret Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 14, 'width': 88}}),
            K.livello('ka-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=32, w=62, size=78, peso='400',
                      colore=C['chiaro'], ritardo=350, durata=1000, lineHeight=1.04, letterSpacing=-2,
                      charAnim={'stagger': 28, 'offsetY': 40, 'blur': 12, 'opacity': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 26, 'color': mix(C['nero'], 55)},
                      responsive={'mobile': {'fontSize': m(34), 'y': 18, 'width': 88}}),
            K.livello('ka-s%d' % n, contenuto=testo, x=6, y=60, w=36, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1000, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 34, 'width': 88}}),
            K.livello('ka-b%d' % n, tipo='button', contenuto='Le notti libere', x=6, y=76, w='auto', size=12, peso='700',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=999, ritardo=1300, buttonUrl=LODGE + '#notti',
                      hover={'scale': 1.04, 'bgColor': C['chiaro'], 'duration': 250},
                      responsive={'mobile': {'y': 50, 'fontSize': m(12)}}),
        ], durata=7500))
    globali = [K.livello('ka-g', contenuto='68°02′ N · Kittilä · −24 °C · luna al 9%', x=60, y=90, w=36, size=12, peso='400',
                         colore=mix(C['chiaro'], 75), align='right', entra='fadeIn', ritardo=600, letterSpacing=2,
                         textTransform='uppercase', fontFamily='Azeret Mono',
                         responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 92, 'width': 88}})]
    return K.proslider(t, sl, globali=globali, transizione='blur', durata_trans=1400, showArrows=True, showDots=True,
                       parallax=True, parallaxIntensity=3)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    ore = t.classic('timezone', {
        'eyebrow': 'Che ore sono lassù', 'heading': 'Scegli la tua ora: ti diciamo com’è il cielo a Kittilä',
        'intro': '', 'base_label': 'In Italia', 'input_value': 21, 'work_start': 9, 'work_end': 17,
        'items': [{'city': 'Bergamo', 'tz': 'Europe/Rome', 'offset': 1, 'label': 'Casa di Marta e Luca'},
                  {'city': 'Kittilä', 'tz': 'Europe/Helsinki', 'offset': 2, 'label': 'Il lodge'},
                  {'city': 'Rovaniemi', 'tz': 'Europe/Helsinki', 'offset': 2, 'label': 'Aeroporto con voli diretti'}],
        'zone_accent': C['acc'], 'work_color': C['acc2'], 'ok_color': C['acc'], 'sleep_color': C['sec'],
        'card_bg': C['sala'], 'card_border': C['filo'], 'align': 'left',
    })
    out.append(t.section([
        t.row([t.col([K.sechead(t, '01 — Un’ora avanti', ['Quando da te', 'è ora di cena…'], size=52, layout='stack',
                                size_mobile=36),
                      K.text(t, '…da noi è l’ora buona per il cielo. Le aurore più forti arrivano fra le 22 e l’una: '
                                'sposta il cursore e guarda che ore sono, qui sopra il Circolo Polare.')], wm='2-5', ws='1-1'),
               t.col([ore], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 110}))

    cabine = t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': k, 'title': ti, 'link': LODGE + '#' + a, 'span': s, 'aspect': ar}
                  for f, k, ti, a, s, ar in [
                      ('pexels-91216.jpg', 'Per due · tetto di vetro', 'Revontuli', 'revontuli', 7, '7/5'),
                      ('pexels-10661646.jpg', 'Per quattro · camino', 'Tunturi', 'tunturi', 5, '1/1'),
                      ('pexels-6386260.jpg', 'Per due · vasca calda', 'Kuu', 'kuu', 5, '1/1'),
                      ('pexels-36072601.jpg', 'Per sei · kota lappone', 'Kota', 'kota', 7, '7/5')]],
        'columns': 12, 'gap': 14, 'aspect': '4/5', 'object_position': 'center center', 'object_fit': 'cover', 'radius': 10,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 20), 'arrow_color': C['chiaro'], 'arrow_hover_bg': C['acc'],
        'arrow_hover_color': C['on_acc'], 'show_arrow': True, 'title_size': 40, 'title_weight': '400',
        'title_uppercase': False, 'kicker_size': 11, 'card_padding': {'top': 26, 'right': 26, 'bottom': 26, 'left': 26},
        'card_radius': radius(10), 'shadow': 'none',
    })
    out.append(t.section([
        t.one([K.sechead(t, '02 — Le cabine', ['Quattro tetti', 'per guardare in su.'], size=72,
                         tagline='Tutte a cinquanta metri l’una dall’altra, nel bosco di betulle, senza luci accese fra loro.')]),
        t.one([cabine]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 130}))

    for foto, ey, ti, tx, video in [
        (V_SLITTA, '03 / 05 — Con gli husky', 'Quaranta cani, una slitta.', 'Il canile di Jussi è a tre chilometri. Due ore '
         'nel bosco, poi il fuoco e il caffè nella kota.', True),
        ('pexels-31038013.jpg', '04 / 05 — Con le renne', 'Il ritmo lento del nord.', 'Una famiglia sami alleva renne qui '
         'da sei generazioni. Ci racconta l’inverno mentre si va al passo.', False),
        ('pexels-31092918.jpg', '05 / 05 — La sauna', 'Poi, nella neve.', 'Ogni cabina ha la sua sauna a legna. Il bagno '
         'nel buco del ghiaccio lo facciamo il giovedì, con chi se la sente.', False),
    ]:
        out.append(K.pannello(t, foto, ey, ti, tx, 'Le esperienze', ESP, overlay=50, video=video))

    voce = t.classic('testimonial', {
        'quote': 'Alle tre di notte è suonata la sveglia dell’aurora. <em>Abbiamo guardato il cielo verde dal letto, senza '
                 'dire niente, per un’ora.</em>',
        'author_name': 'Chiara e Tommaso', 'author_role': 'cabina Revontuli · gennaio 2026', 'rating': '5',
        'text_color': C['testo'], 'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'],
        'quote_font': 'heading', 'quote_size': 34, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': C['acc'],
    })
    out.append(t.section([K.centered(t, [voce])], padding='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_NEVE, 'Ottobre — aprile', 'Il bosco <em>fa silenzio.</em>',
                             'Con un metro di neve i suoni spariscono. Si sente solo il fuoco nella stufa e, a volte, il '
                             'ghiaccio del lago che si assesta.', overlay=40,
                             extra=[K.button(t, 'Scegli le tue notti', LODGE + '#notti', kind='pieno', align='center', pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='KAAMOS',
                page_intro_caption='68° nord · la notte comincia', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_lodge():
    t = Tpl(ID, 'il-lodge')
    out = [K.intro(t, 'Il lodge', ['Vetro, legno,', 'e un metro di neve.'],
                   'Quattro cabine costruite da un carpentiere di Kittilä col pino della valle. Il tetto di vetro scalda da '
                   'solo e non si appanna; la stufa la accendiamo noi prima che arrivi.',
                   bg=K.photo_bg('pexels-10661646.jpg', overlay=55), overlay_text=True)]
    for aid, foto, nome, chi, testo, dati in [
        ('revontuli', 'pexels-91216.jpg', 'Revontuli', 'Per due', 'Il tetto di vetro sopra il letto e la sauna privata. '
         '«Revontuli» vuol dire «fuochi della volpe»: così chiamano l’aurora qui.',
         [('Ospiti', '2'), ('Letto', 'king, sotto il vetro'), ('Sauna', 'privata, a legna'), ('Da', '420 € a notte')]),
        ('tunturi', 'pexels-4572734.jpg', 'Tunturi', 'Per quattro', 'Due stanze, il camino in mezzo e una vetrata sul '
         'monte Levi. Per le famiglie: il soppalco piace ai bambini.',
         [('Ospiti', '4'), ('Stanze', '2 + soppalco'), ('Camino', 'a legna'), ('Da', '560 € a notte')]),
    ]:
        out.append(t.section([
            t.row([t.col([K.image(t, foto, 'La cabina ' + nome, ratio='4/3', tilt=True)], wm='3-5', ws='1-1'),
                   t.col([K.label(t, chi, color=C['acc']), K.headline(t, nome, size=56, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati)], wm='2-5', ws='1-1')],
                  layout='custom', custom='60,40', gap=56, valign='center'),
        ], padding='large', html_id=aid))
    notti = t.classic('soggiorni', {
        'occupati': [{'id': 'ka-oc-1', 'dal': '+3', 'al': '+7', 'nota': 'Revontuli occupata'},
                     {'id': 'ka-oc-2', 'dal': '+15', 'al': '+19', 'nota': ''}],
        'prezzo_notte': 420,
        'stagioni': [{'id': 'ka-st-kaamos', 'nome': 'Kaamos, la notte polare', 'dal': '12-10', 'al': '01-02', 'prezzo_notte': 560,
                      'minimo_notti': 4},
                     {'id': 'ka-st-marzo', 'nome': 'Marzo, neve e luce', 'dal': '03-01', 'al': '03-31', 'prezzo_notte': 380,
                      'minimo_notti': 3}],
        'minimo_notti': 3, 'ospiti_max': 2, 'ospiti_iniziali': 2, 'pulizie': 90, 'tassa_soggiorno': 0, 'etichetta_tassa': '',
        'valuta': '€', 'mostra_prezzi': True, 'mesi_visibili': 2, 'mesi_avanti': 10,
        'testo_pulsante': 'Chiedi queste notti', 'link_richiesta': ARRIVARE + '#scrivici',
        'campi_modulo': 'arrivo,partenza,ospiti', 'parametri': True,
        'nota': 'La richiesta non impegna: rispondiamo entro sera con la conferma e i voli più comodi da Milano e Roma.',
        'testo_scegli_arrivo': 'Scegli la notte di arrivo', 'testo_scegli_partenza': 'Ora scegli la partenza',
        'testo_minimo': 'In questo periodo servono almeno {n} notti', 'testo_occupato': 'In queste notti la cabina è occupata',
        'legenda': True, 'colore_scelto': C['acc'], 'sfondo_pannello': C['fondo'],
    })
    out.append(t.section([
        t.one([K.sechead(t, 'Le notti libere', ['Scegli', 'quando venire.'], size=64,
                         tagline='Il prezzo è per la cabina Revontuli, colazione e sveglia dell’aurora comprese. Il calendario '
                                 'ti dice il totale prima di scriverci.')]),
        t.one([notti]),
    ], bg=solid(C['sala']), padding='large', html_id='notti', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_esperienze():
    t = Tpl(ID, 'esperienze')
    out = [K.intro(t, 'Le esperienze', ['Il nord,', 'un passo alla volta.'],
                   'Le facciamo con le persone del posto, in piccoli gruppi, e solo quando il tempo lo permette. Le prenoti '
                   'arrivato al lodge: decidiamo insieme ogni sera, guardando il cielo.',
                   bg=K.video_bg(V_AURORA2, overlay=64), overlay_text=True)]
    passi = t.classic('process-steps', {
        'items': [{'number': '21:00', 'title': 'Caccia all’aurora', 'description': 'In furgone verso il cielo più sgombro, con '
                   'treppiede e cioccolata calda. 95 €.'},
                  {'number': '10:00', 'title': 'Husky', 'description': 'Due ore di slitta nel bosco, poi il fuoco nella kota. '
                   '180 €.'},
                  {'number': '14:00', 'title': 'Renne', 'description': 'Con la famiglia Aikio, al passo, e il racconto dell’inverno. '
                   '120 €.'},
                  {'number': '18:00', 'title': 'Sauna e ghiaccio', 'description': 'La sauna fumosa del lago e il tuffo nel buco '
                   'del ghiaccio. 60 €.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 14, 'number_font': C['mono'], 'number_weight': '400', 'title_color': C['testo'], 'title_size': 26,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([t.one([passi])], padding='large', style={'padding_top': 60}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-36152821.jpg', 'Una muta di husky corre nella neve', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-23939046.jpg', 'Una persona cammina sotto l’aurora', ratio='4/5')], wm='1-3', ws='1-1'),
               t.col([K.image(t, 'pexels-6803600.jpg', 'Una coppia brinda in una cabina di legno', ratio='4/5')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=16),
    ], padding='large', style={'padding_top': 20}))
    out.append(K.domande(t, 'Prima', 'di partire.', [
        ('L’aurora è garantita?', 'No, e diffida di chi te lo promette. Da ottobre a marzo la vediamo due notti su tre: con '
                                  'quattro notti, quasi sempre.'),
        ('Che freddo fa?', 'Fra −10 e −30. Ti prestiamo tuta termica, scarponi e guanti: porta solo la lana sotto.'),
        ('Si può venire coi bambini?', 'Sì, dai quattro anni. La cabina Tunturi ha il soppalco, e gli husky li adorano.'),
        ('Si mangia al lodge?', 'Colazione sempre; la cena davanti al fuoco tre sere a settimana, salmone e renna.'),
    ], 'esp', bg=False))
    return out


def build_arrivare():
    t = Tpl(ID, 'arrivare')
    out = [K.intro(t, 'Come arrivare', ['Tre ore', 'da Milano.'],
                   'Voli diretti da Milano Malpensa a Kittilä da dicembre a marzo; da Roma con scalo a Helsinki. Ti veniamo '
                   'a prendere noi in aeroporto: dieci minuti di strada.', bg=K.photo_bg('pexels-30654164.jpg', overlay=55),
                   overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.specs(t, [('Aeroporto', 'Kittilä (KTT), 12 km'), ('Voli diretti', 'Milano Malpensa, dicembre — marzo'),
                                  ('Navetta', 'la nostra, compresa nel prezzo'), ('Lingue', 'italiano, inglese, finlandese')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Kaamos · il lodge', hex_marker=HEX['primary'], zoom='10')], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], padding='large', style={'padding_top': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('date', 'Arrivo', 'arrivo', True, param='arrivo'), K.campo('date', 'Partenza', 'partenza', True, param='partenza'),
             K.campo('select', 'Ospiti', 'ospiti', True, opzioni='1\n2\n3\n4\n5\n6', param='ospiti'),
             K.campo('select', 'La cabina', 'cabina', opzioni='Revontuli\nTunturi\nKuu\nKota\nCi consigliate voi'),
             K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1', segnaposto='Un anniversario, i bambini, una cena…')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scrivici', ['Ti rispondiamo', 'entro sera.'],
                                'Con la conferma ti mandiamo i voli più comodi e la lista di cosa mettere in valigia.', VOCI,
                                campi, 'Kaamos — richiesta', 'Chiedi le notti', 'Grazie: entro sera ti scriviamo con la conferma.'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · nessuna aurora qui', color=C['acc'], align='center'),
                       K.headline(t, 'Stanotte <em>il cielo è coperto.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Torniamo al lodge e riproviamo domani.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_AURORA4, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Ospitalità e viaggi', 'Dormi sotto l’aurora',
    ['aurora boreale', 'lapponia', 'lodge', 'cabine di vetro', 'viaggi', 'inverno', 'scuro', 'slider pro'],
    'Un lodge di cabine di vetro sopra il Circolo Polare, blu di notte polare col verde dell’aurora: l’apertura coi video '
    'veri delle aurore e le lettere che salgono dal buio, l’ora di Kittilä accanto a quella di casa, le quattro cabine, il '
    'calendario delle notti libere col prezzo, le esperienze con gli husky e le renne e la sauna nella neve.',
    [('Il lodge', LODGE), ('Esperienze', ESP), ('Come arrivare', ARRIVARE), ('Le notti', LODGE + '#notti')],
    [('home', 'Home', None), ('il-lodge', 'Il lodge', 'il-lodge'), ('esperienze', 'Le esperienze', 'esperienze'),
     ('arrivare', 'Come arrivare', 'arrivare')],
    HEX, '\'Nunito Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Unbounded\', \'Helvetica Neue\', Arial, sans-serif', '\'Azeret Mono\', ui-monospace, Menlo, monospace',
    ['Unbounded', 'Nunito Sans', 'Azeret Mono'], peso_titoli='400', raggio=10, grana=4, titoli_line='1.05',
    titoli_spazio='-0.02em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-lodge.json': build_lodge(), 'esperienze.json': build_esperienze(),
                        'arrivare.json': build_arrivare(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
