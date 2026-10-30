# -*- coding: utf-8 -*-
"""Genera il tema «Forma» (id forma): una calzoleria su misura a Lucca, in una bottega dentro le mura dal 1962. Scarpe
fatte a mano in otto settimane sulla forma di legno di chi le porta, risuolature e restauri. Scuro come il cuoio.

Nero di bottega, il cuoio cognac, il verde bottiglia del banco e il color pergamena del filo cerato. Titoli in Young
Serif, testo in Work Sans, misure in Overpass Mono.

Apertura: Slider Pro con le quattro tappe di una scarpa (la misura, la forma di faggio, la cucitura, la lucidatura) coi
video veri della bottega, in dissolvenza con zoom; in basso, fisso, un metro da sarto disegnato coi numeri (livello
globale). Poi i modelli in elenco con l'anteprima che segue il cursore (Hover list), la pelle che si compone a schegge
scorrendo (Shattered image), il restauro prima e dopo da trascinare (Before/after), il tracciamento delle scarpe in
lavorazione (Tracciamento) e le otto settimane in colonne che scorrono (Scorrimento verticale).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'forma'
NAME = 'Forma'

HEX = {
    'primary': '#C8743A', 'primary_contrast': '#120C08', 'secondary': '#2F4A3A', 'secondary_contrast': '#F7F0E6',
    'accent': '#D9C8A9', 'dark': '#060504', 'light': '#F7F0E6', 'muted': '#191410', 'muted_contrast': '#EFE6DA',
    'text': '#EFE6DA', 'text_muted': '#A89A8A', 'background': '#0E0B09', 'border': '#2C241D', 'link': '#C8743A',
}

HOME, SCARPE, MISURA, BOTTEGA = '{{PAGINA:home}}', '{{PAGINA:le-scarpe}}', '{{PAGINA:su-misura}}', '{{PAGINA:la-bottega}}'
MAIL, PHONE, PHONE_TEL = 'bottega@forma.example', '+39 0583 000 162', 'tel:+390583000162'
LAT, LNG = '43.8429', '10.5027'

V_BORDEAUX, V_SPAZZOLA, V_PANNO, V_TOMAIA, V_MARTELLO, V_ARTIGIANO = (
    'pexels-video-40318161', 'pexels-video-40318167', 'pexels-video-9058244', 'pexels-video-40318175',
    'pexels-video-40318179', 'pexels-video-34740055')
PHOTOS = [
    'pexels-40290857.jpg', 'pexels-3801349.jpg', 'pexels-38946185.jpg', 'pexels-37273499.jpg', 'pexels-38946186.jpg',
    'pexels-40290858.jpg', 'pexels-38946163.jpg', 'pexels-4607773.jpg', 'pexels-37325419.jpg', 'pexels-40080052.jpg',
    'pexels-30743419.jpg', 'pexels-4452610.jpg', 'pexels-8899943.jpg', 'pexels-37443510.jpg', 'pexels-34279969.jpg',
    'pexels-8899959.jpg', 'pexels-4637874.jpg', 'pexels-4065507.jpg', 'pexels-40290861.jpg', 'pexels-32076605.jpg',
    'pexels-35385654.jpg', 'pexels-38946188.jpg', 'pexels-17705884.jpg', 'pexels-8193568.jpg', 'pexels-32142573.jpg',
    'pexels-5379387.jpg', 'pexels-15586850.jpg', 'pexels-6069536.jpg', 'pexels-32938850.jpg',
]
VIDEOS = [V_BORDEAUX, V_SPAZZOLA, V_PANNO, V_TOMAIA, V_MARTELLO, V_ARTIGIANO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Via Fillungo 112, dentro le mura · Lucca', BOTTEGA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]
MODELLI = [
    ('oxford', 'Oxford', 'Allacciatura chiusa · da 690 €', 'pexels-34279969.jpg', 'La più formale: per le nozze, per '
     'l’ufficio, per chi vuole una scarpa sola che vada bene con tutto.'),
    ('derby', 'Derby', 'Allacciatura aperta · da 650 €', 'pexels-4637874.jpg', 'Più comoda sul collo del piede: la '
     'scarpa di tutti i giorni, anche col pantalone di velluto.'),
    ('mocassino', 'Mocassino', 'Senza lacci · da 590 €', 'pexels-8193568.jpg', 'Cucito a mano sulla tomaia, in vitello o '
     'scamosciato. D’estate senza calze.'),
    ('stivaletto', 'Stivaletto', 'Chelsea o con lacci · da 790 €', 'pexels-6069536.jpg', 'Per l’inverno, in cordovan o '
     'vitello ingrassato. Suola in cuoio con la mezza suola in gomma, se piove.'),
]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Forma', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Forma', 'Calzoleria su misura dentro le mura di Lucca dal 1962. Scarpe fatte a mano in otto '
                   'settimane, sulla forma di legno di chi le porta. Risuolature e restauri di ogni scarpa buona.',
                   [('Le scarpe', SCARPE), ('Su misura', MISURA), ('La bottega', BOTTEGA), ('Dove siamo', BOTTEGA + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'La forma', 'La tua forma di faggio resta in bottega, col tuo nome inciso. Il secondo paio costa un '
                   'terzo in meno, e arriva in cinque settimane.', VOCI, '© 2026 Forma calzoleria · Lucca · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('pinterest', 'https://pinterest.com')], marchio_size=150)


def apertura(t):
    m = K.mob
    tappe = [
        ('01 / 04 · La misura', 'Prima,\nil tuo piede.', 'Ti misuriamo in piedi e seduto, disegniamo la pianta su carta e '
         'guardiamo come cammini. Ci vuole un’ora, e un caffè.', K.sfondo_foto('pexels-38946185.jpg', overlay=0.45, scala=1.08)),
        ('02 / 04 · La forma', 'Una forma\ndi faggio.', 'Da quel disegno il formaio tornisce una forma di legno solo tua. '
         'Resta in bottega, col tuo nome inciso, per sempre.', K.sfondo_video(V_TOMAIA, overlay=0.5)),
        ('03 / 04 · La cucitura', 'Cucita\na mano.', 'Guardolo cucito a mano e suola di cuoio conciato al vegetale: '
         'duecento punti per scarpa, due giorni di lavoro.', K.sfondo_video(V_ARTIGIANO, overlay=0.55)),
        ('04 / 04 · La lucidatura', 'Poi si\nlucida.', 'Tre strati di cera e la patina a mano. Dopo otto settimane le '
         'scarpe escono dalla bottega, e durano vent’anni.', K.sfondo_video(V_BORDEAUX, overlay=0.45)),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo) in enumerate(tappe):
        n = i + 1
        sl.append(K.diapositiva('sc-%d' % n, sfondo, [
            K.livello('sc-o%d' % n, contenuto=occ, x=6, y=24, w=44, size=13, peso='500', colore=C['acc2'], ritardo=150,
                      letterSpacing=2, textTransform='uppercase', fontFamily='Overpass Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 12, 'width': 88}}),
            K.livello('sc-r%d' % n, tipo='shape', x=6, y=28.6, w=10, h=0.25, bgColor=C['acc'], entra='fadeInLeft', ritardo=300,
                      durata=900, responsive={'mobile': {'y': 14.6, 'width': 22}}),
            K.livello('sc-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=32, w=60, size=110, peso='400',
                      colore=C['chiaro'], ritardo=400, durata=1000, lineHeight=0.98, letterSpacing=-2,
                      mask={'top': 0, 'right': 0, 'bottom': 100, 'left': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 26, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(46), 'y': 17, 'width': 88}}),
            K.livello('sc-s%d' % n, contenuto=testo, x=6, y=66, w=34, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1000, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 34, 'width': 88}}),
            K.livello('sc-b%d' % n, tipo='button', contenuto='Prendi le misure', x=66, y=72, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1300, buttonUrl=BOTTEGA + '#scrivici',
                      hover={'bgColor': C['acc2'], 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 50, 'fontSize': m(12)}}),
        ], durata=7000))
    metro = ''.join(('%d' % c) if c % 5 == 0 else '·' for c in range(0, 31))
    globali = [
        K.livello('sc-metro', contenuto=metro, x=6, y=90, w=88, size=12, peso='500', colore=mix(C['acc2'], 70),
                  entra='fadeIn', ritardo=500, durata=1200, letterSpacing=17, fontFamily='Overpass Mono',
                  responsive={'mobile': {'visible': False}}),
        K.livello('sc-metro-l', tipo='shape', x=6, y=89, w=88, h=0.12, bgColor=mix(C['acc2'], 40), entra='fadeIn',
                  ritardo=400, durata=1200, responsive={'mobile': {'visible': False}}),
    ]
    return K.proslider(t, sl, globali=globali, transizione='zoomFade', durata_trans=1300, showArrows=True, showDots=True,
                       parallax=True, parallaxIntensity=3)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    modelli = t.classic('hoverlist', {
        'items': [{'name': nome, 'sub': sotto, 'desc': desc, 'number': '%02d' % (i + 1), 'image': K.img(foto),
                   'link_url': SCARPE + '#' + sid, 'color': C['acc']} for i, (sid, nome, sotto, foto, desc) in enumerate(MODELLI)],
        'lead_mode': 'number', 'number_color': C['acc'], 'number_hover_color': C['chiaro'], 'name_font_family': 'heading',
        'name_color': C['testo'], 'name_hover_color': C['acc'], 'name_size': 52, 'name_uppercase': False,
        'sub_color': C['tenue'], 'sub_hover_color': C['acc2'], 'sub_size': 12, 'sub_uppercase': True,
        'desc_color': C['tenue'], 'desc_hover_color': C['testo'], 'desc_size': 15, 'row_padding_y': 28, 'hover_indent': 28,
        'hover_bg': mix(C['acc'], 7), 'line_color': C['filo'], 'line_hover_color': C['acc'], 'peek': True,
        'peek_mode': 'image', 'peek_width': 260, 'peek_ratio': '4/5', 'mono_font_family': 'Overpass Mono',
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'I modelli', ['Quattro forme,', 'mille scarpe.'], size=60, layout='stack', size_mobile=38),
                      K.text(t, 'Partiamo da quattro modelli e li adattiamo a te: la punta, il tacco, la pelle, il colore '
                                'del filo. Passa sopra un modello per vederlo.'),
                      K.button(t, 'Tutte le scarpe', SCARPE, pad_top=18)], wm='1-3', ws='1-1'),
               t.col([modelli], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 120}))
    pelle = t.classic('shatteredimage', {
        'image_url': K.img('pexels-40080052.jpg'), 'preset': 'shards', 'gap': 4, 'height': '520px', 'image_ratio': '4/5',
        'image_position': 'center center', 'gap_color': C['sala'], 'scroll_reveal': True, 'scroll_reveal_stagger': 110,
        'scroll_reveal_duration': 700, 'kenburns': True, 'kenburns_style': 'mixed', 'kenburns_duration': 22,
        'kenburns_intensity': 1.15, 'overlay': False,
    }, style={})
    out.append(t.section([
        t.row([t.col([pelle], wm='1-2', ws='1-1'),
               t.col([K.sechead(t, 'La pelle', ['Si sceglie', 'toccandola.'], size=60, layout='stack', size_mobile=38),
                      K.text(t, 'Vitello francese, cordovan, scamosciato, e per chi osa il coccodrillo di allevamento '
                                'certificato. In bottega ci sono quaranta pelli: le tocchi, le pieghi, le senti. Poi '
                                'scegliamo insieme il pezzo da tagliare, quello senza difetti.'),
                      K.specs(t, [('Pelli in bottega', '40 colori e finiture'), ('Concia', 'vegetale, dalla Toscana'),
                                  ('Suole', 'cuoio di bue, o gomma Vibram')])], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_SPAZZOLA, 'Il sabato', 'Si lucida in bottega.', 'Porta le tue scarpe il sabato mattina: '
                          'te le lucidiamo davanti a un caffè, e ti insegniamo come si fa. Gratis, per chi ha scarpe nostre.',
                          'La bottega', BOTTEGA, overlay=50, video=True))
    traccia = t.classic('tracciamento', {
        'indirizzo': MISURA + '?ordine={codice}#lavorazione', 'operatori': [], 'etichetta': 'Il codice del tuo paio',
        'mostra_etichetta': True, 'segnaposto': 'Es. FM2611', 'testo_pulsante': 'Dove sono', 'icona_pulsante': 'arrow-right',
        'icona_campo': 'footprints', 'nota': 'Lo trovi sulla ricevuta della misura, accanto al tuo nome.',
        'nuova_scheda': False, 'maiuscolo': True, 'lunghezza_minima': 6,
        'testo_errore': 'Il codice comincia con FM e ha sei caratteri.', 'parametro': 'ordine',
        'sfondo_campo': C['sala'], 'colore_campo': C['testo'],
        'bordo_campo': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']},
        'colore_etichetta': C['tenue'], 'colore_nota': C['tenue'], 'colore_errore': C['acc'], 'sfondo_pulsante': C['acc'],
        'colore_pulsante': C['on_acc'],
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Le tue scarpe', ['A che punto', 'sono?'], size=60, layout='stack', size_mobile=38),
                      K.text(t, 'Otto settimane sono lunghe. Scrivi il codice del tuo paio: ti diciamo se siamo alla forma, '
                                'al taglio, alla cucitura o alla lucidatura.')], wm='1-2', ws='1-1'),
               t.col([traccia], wm='1-2', ws='1-1')], layout='50-50', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_MARTELLO, 'Il maestro', 'Una scarpa buona <em>si risuola.</em>',
                             'Le nostre si risuolano quattro, cinque volte. Le scarpe di vent’anni fa tornano in bottega e '
                             'ripartono come nuove.', overlay=70,
                             extra=[K.button(t, 'Risuolature e restauri', SCARPE + '#restauri', kind='pieno', align='center',
                                             pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Forma',
                page_intro_caption='Lucca · dal 1962', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_scarpe():
    t = Tpl(ID, 'le-scarpe')
    out = [K.intro(t, 'Le scarpe', ['Quattro modelli,', 'il tuo piede.'],
                   'Ogni scarpa parte da un modello e diventa tua: la punta, il tacco, la pelle e il colore del filo li '
                   'scegli tu, alla prima misura.', bg=K.photo_bg('pexels-38946163.jpg', overlay=55), overlay_text=True)]
    for k, (sid, nome, sotto, foto, desc) in enumerate(MODELLI):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, 'Il modello ' + nome, ratio='4/5', tilt=True)], wm='2-5', ws='1-1'),
                   t.col([K.label(t, sotto, color=C['acc']), K.headline(t, nome, size=64, tag='h2', pad_bottom=14),
                          K.text(t, desc), K.button(t, 'Prendi le misure', BOTTEGA + '#scrivici')], wm='3-5', ws='1-1')],
                  layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=sid, bg=solid(C['sala']) if k % 2 else None, style={'padding_top': 100, 'padding_bottom': 100}))
    restauri = t.classic('beforeafter', {
        'items': [{'before_image': K.img(a), 'after_image': K.img(b), 'before_label': 'Prima', 'after_label': 'Dopo',
                   'title': ti, 'text': tx} for a, b, ti, tx in [
            ('pexels-15586850.jpg', 'pexels-4637874.jpg', 'Risuolatura · 10 giorni', 'Suola nuova in cuoio, tacco rifatto, '
             'la pelle nutrita e rilucidata. Da 120 €.'),
            ('pexels-5379387.jpg', 'pexels-8193568.jpg', 'Restauro completo · 3 settimane', 'Cuciture rifatte, fodera nuova, '
             'il colore ripreso a mano. Da 220 €.'),
            ('pexels-32142573.jpg', 'pexels-34279969.jpg', 'Lucidatura a specchio · 1 giorno', 'Tre strati di cera e la '
             'punta a specchio, come il giorno delle nozze. 35 €.')]],
        'columns': 3, 'gap': 20, 'media_aspect': '4/5', 'media_fit': 'cover', 'accent': C['acc'],
        'before_label_color': C['chiaro'], 'after_label_color': C['on_acc'], 'title_color': C['testo'],
        'text_color': C['tenue'], 'card_bg': C['sala'], 'radius': 2, 'card_radius': radius(2), 'shadow': 'none',
        'handle_color': C['chiaro'], 'autoplay': True, 'autoplay_delay': 4, 'autoplay_speed': 3,
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Risuolature e restauri', ['Trascina,', 'e guarda la differenza.'], size=60,
                         tagline='Rifacciamo le scarpe buone, anche quelle non nostre. Portale in bottega: ti diciamo subito '
                                 'se vale la pena, e quanto costa.')]),
        t.one([restauri]),
    ], padding='large', width='large', html_id='restauri', style={'padding_top': 130, 'padding_bottom': 130}))
    return out


def build_misura():
    t = Tpl(ID, 'su-misura')
    out = [K.intro(t, 'Su misura', ['Otto settimane,', 'vent’anni.'],
                   'Dalla prima misura alla consegna passano otto settimane e tre appuntamenti in bottega: la misura, la '
                   'prova con la scarpa di prova, la consegna.', bg=K.video_bg(V_PANNO, overlay=55), overlay_text=True)]
    voci = []
    for col, items in enumerate([
        [('immagine', 'pexels-38946186.jpg', 'Il maestro misura e disegna la pianta'), ('testo', 'Settimana 1 · la misura', ''),
         ('immagine', 'pexels-40080052.jpg', 'I fili cerati colorati sul banco'), ('testo', 'Settimana 5 · il taglio', '')],
        [('testo', 'Settimana 2 · la forma di faggio', ''), ('immagine', 'pexels-38946188.jpg', 'Il calzolaio monta la tomaia sulla forma'),
         ('testo', 'Settimana 6 · la cucitura a mano', ''), ('immagine', 'pexels-4452610.jpg', 'Mani che lavorano un pezzo di cuoio')],
        [('immagine', 'pexels-17705884.jpg', 'La scarpa di prova sul banco'), ('testo', 'Settimana 4 · la scarpa di prova', ''),
         ('immagine', 'pexels-8899943.jpg', 'Mani che lucidano una scarpa nera'), ('testo', 'Settimana 8 · la consegna', '')],
    ], start=1):
        for tipo, val, alt in items:
            if tipo == 'immagine':
                voci.append({'tipo': 'immagine', 'immagine': K.img(val), 'alt': alt, 'colonna': str(col)})
            else:
                voci.append({'tipo': 'testo', 'testo': val, 'colonna': str(col)})
    scorre = t.classic('scorrimentoverticale', {
        'voci': voci, 'colonne': '3', 'colonne_mobile': '2', 'durata': 32, 'direzione_1': 'giu', 'direzione_2': 'su',
        'direzione_3': 'giu', 'pausa_hover': True, 'altezza': 640, 'altezza_mobile': 460, 'gap': 22, 'gap_colonne': 14,
        'sfumatura_bordi': 18, 'allineamento': 'center', 'testo_font_family': 'heading', 'testo_font_size': 30,
        'testo_font_size_mobile': 20, 'testo_font_weight': '400', 'testo_colore': C['testo'], 'immagine_altezza': 220,
        'immagine_raggio': radius(2), 'separatore_colore': C['acc'],
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Le otto settimane', ['Tre appuntamenti,', 'il resto lo facciamo noi.'], size=56,
                                layout='stack', size_mobile=36),
                      K.specs(t, [('Il primo paio', 'da 590 €, la forma compresa'), ('Il secondo', 'un terzo in meno, in cinque settimane'),
                                  ('L’acconto', 'un terzo alla misura'), ('La consegna', 'in bottega, o a casa in Italia')]),
                      K.button(t, 'Prendi le misure', BOTTEGA + '#scrivici', kind='pieno', pad_top=22)], wm='2-5', ws='1-1'),
               t.col([scorre], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', html_id='lavorazione', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Prima', 'della misura.', [
        ('Quanto costano?', 'Il primo paio da 590 € con la forma di legno compresa. Le pelli rare costano di più: te lo '
                            'diciamo prima, alla misura.'),
        ('E se non mi stanno bene?', 'Alla settimana 4 provi la scarpa di prova, in pelle grezza: lì correggiamo tutto. '
                                     'Dopo, la sistemiamo finché non ti va come un guanto.'),
        ('Fate anche scarpe da donna?', 'Sì, con tacchi fino a sei centimetri. E stivaletti, e scarpe da sposa.'),
        ('Venite a misurare a casa?', 'In Toscana sì, il lunedì. Fuori dalla Toscana, due volte l’anno a Milano e Roma.'),
    ], 'mi', bg=True))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Dentro le mura,', 'dal 1962.'],
                   'Una stanza in via Fillungo con il banco di noce, le forme di faggio appese al muro e il deschetto del '
                   'nonno. Aperta dal martedì al sabato, dalle 9 alle 19.', bg=K.photo_bg('pexels-3801349.jpg', overlay=60),
                   overlay_text=True)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Cosa ti serve', 'cosa', True,
                     opzioni='Un paio su misura\nUna risuolatura\nUn restauro\nUna lucidatura\nUn regalo'),
             K.campo('text', 'Il tuo numero', 'numero', segnaposto='Es. 42 e mezzo'),
             K.campo('date', 'Il giorno che preferisci', 'giorno'),
             K.campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1', segnaposto='Un’occasione, un modello visto, una scarpa da salvare…')]
    out.append(K.sezione_modulo(t, 'scrivici', 'La misura', ['Un’ora,', 'e un caffè.'],
                                'La prima misura dura un’ora ed è gratis, anche se poi non ordini. Ti scriviamo entro un giorno '
                                'con l’orario.', VOCI, campi, 'Forma — appuntamento', 'Chiedi la misura',
                                'Ricevuto. Ti scriviamo entro domani con il giorno e l’ora.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Via Fillungo,', 'dentro le mura.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via Fillungo 112, Lucca'), ('Orari', 'martedì — sabato, 9 — 19'),
                                  ('Dalla stazione', 'quindici minuti a piedi'), ('Parcheggio', 'Mazzini, poi cinque minuti')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Forma · calzoleria su misura', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · scarpa spaiata', color=C['acc'], align='center'),
                       K.headline(t, 'Qui manca <em>l’altra scarpa.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'La troviamo in bottega: torna all’inizio.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BORDEAUX, overlay=55), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Dalla misura alla scarpa',
    ['calzolaio', 'scarpe su misura', 'artigianato', 'lucca', 'pelle', 'risuolatura', 'scuro', 'slider pro'],
    'Una calzoleria su misura a Lucca, nero di bottega e cuoio cognac: l’apertura con le quattro tappe di una scarpa e il '
    'metro da sarto disegnato in basso, i modelli con l’anteprima che segue il cursore, la pelle che si compone a '
    'schegge, il restauro prima e dopo da trascinare, il tracciamento del proprio paio e le otto settimane che scorrono.',
    [('Le scarpe', SCARPE), ('Su misura', MISURA), ('Dove siamo', BOTTEGA + '#arrivare'), ('La misura', BOTTEGA)],
    [('home', 'Home', None), ('le-scarpe', 'Le scarpe', 'le-scarpe'), ('su-misura', 'Su misura', 'su-misura'),
     ('la-bottega', 'La bottega', 'la-bottega')],
    HEX, '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Young Serif\', \'Georgia\', serif', '\'Overpass Mono\', ui-monospace, Menlo, monospace',
    ['Young Serif', 'Work Sans', 'Overpass Mono'], peso_titoli='400', raggio=2, grana=6, titoli_line='1.0',
    titoli_spazio='-0.02em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-scarpe.json': build_scarpe(), 'su-misura.json': build_misura(), 'la-bottega.json': build_bottega(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
