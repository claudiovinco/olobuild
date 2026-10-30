# -*- coding: utf-8 -*-
"""Genera il tema «Bialbero» (id bialbero): un’officina di restauro di auto d’epoca alle porte di Modena. Carrozzeria,
meccanica, verniciatura e interni sotto lo stesso tetto; qualche auto restaurata la vendono, con la sua storia.

Tema scuro, nero d’officina col rosso corsa, il cromo e il cuoio chiaro dei sedili. Titoli in Archivo, testo in Sofia
Sans, le pratiche in Courier Prime, come scritte a macchina.

Apertura: Slider Pro «il libretto». Ogni diapositiva è una pratica dell’officina: il video o la foto dell’auto, il
numero di pratica, il nome dell’auto, i dati di telaio e le ore di lavoro che si battono a macchina lettera per lettera,
e alla fine il timbro rosso che cade di traverso sulla pratica (RESTAURATA, IN OFFICINA…). In un angolo, fisso, il
contagiri: la lancetta sale, scende e riparte, come un motore che si scalda.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'bialbero'
NAME = 'Bialbero'

HEX = {
    'primary': '#F04A3E', 'primary_contrast': '#0D0D0E', 'secondary': '#A9AFB4', 'secondary_contrast': '#0D0D0E',
    'accent': '#E7D3AC', 'dark': '#060607', 'light': '#F5F2EC', 'muted': '#17171A', 'muted_contrast': '#EFECE6',
    'text': '#EFECE6', 'text_muted': '#A09B93', 'background': '#0D0D0E', 'border': '#2A2A2E', 'link': '#F04A3E',
}

HOME, RESTAURI, VENDITA, OFFICINA = '{{PAGINA:home}}', '{{PAGINA:restauri}}', '{{PAGINA:in-vendita}}', '{{PAGINA:l-officina}}'
MAIL, PHONE, PHONE_TEL = 'officina@bialbero.example', '+39 059 000 1989', 'tel:+390590001989'
LAT, LNG = '44.6385', '10.9612'

V_STRADA, V_CRUSCOTTO, V_SCINTILLE, V_FAVILLE, V_VERNICE, V_LUCIDA, V_MOTORE = (
    'pexels-video-34279669', 'pexels-video-31220595', 'pexels-video-8470299', 'pexels-video-6046357',
    'pexels-video-8469684', 'pexels-video-6159183', 'pexels-video-8469674')
PHOTOS = [
    'pexels-33380762.jpg', 'pexels-39910669.jpg', 'pexels-33014967.jpg', 'pexels-38203113.jpg', 'pexels-39540261.jpg',
    'pexels-4489766.jpg', 'pexels-4480505.jpg', 'pexels-4489761.jpg', 'pexels-29198156.jpg', 'pexels-4116232.jpg',
    'pexels-8310738.jpg', 'pexels-8310758.jpg', 'pexels-10866831.jpg', 'pexels-19581671.jpg', 'pexels-32674055.jpg',
    'pexels-28856789.jpg', 'pexels-32199842.jpg', 'pexels-30061846.jpg', 'pexels-2810140.jpg', 'pexels-11381557.jpg',
    'pexels-39122952.jpg', 'pexels-38442145.jpg', 'pexels-14096710.jpg', 'pexels-18136808.jpg', 'pexels-37881079.jpg',
    'pexels-30767235.jpg', 'pexels-17377224.jpg', 'pexels-31988791.jpg', 'pexels-10402008.jpg', 'pexels-29972359.jpg',
    'pexels-28194169.jpg', 'pexels-32558145.jpg', 'pexels-18262241.jpg', 'pexels-36635361.jpg', 'pexels-30767229.jpg',
    'pexels-19581670.jpg', 'pexels-34914237.jpg', 'pexels-16243260.jpg', 'pexels-16243258.jpg', 'pexels-16679591.jpg',
    'pexels-31295161.jpg', 'pexels-4486212.jpg', 'pexels-7006697.jpg',
]
VIDEOS = [V_STRADA, V_CRUSCOTTO, V_SCINTILLE, V_FAVILLE, V_VERNICE, V_LUCIDA, V_MOTORE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
TITOLI, MONO = 'Archivo', 'Courier Prime'
VOCI = [('map-pin', 'Via Emilia Est 1220 · Modena', OFFICINA + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Bialbero', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Bialbero', 'Restauro di auto d’epoca alle porte di Modena, dal 1989. Carrozzeria, meccanica, '
                   'verniciatura e interni sotto lo stesso tetto, e ogni auto con la sua pratica scritta a macchina.',
                   [('I restauri', RESTAURI), ('In vendita', VENDITA), ('L’officina', OFFICINA),
                    ('Dove siamo', OFFICINA + '#arrivare'), ('Privacy', '/privacy-policy/')],
                   'L’officina', 'Lunedì — venerdì 8 — 12 e 14 — 18:30<br>Sabato mattina su appuntamento', VOCI,
                   '© 2026 Bialbero restauri di Bettini &amp; Rinaldi snc · Modena · P.IVA 00000000000',
                   [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')], marchio_size=120)


def apertura(t):
    m = K.mob
    pratiche = [
        ('0147', 'Il primo giro', 'dopo vent’anni.', ['Telaio  AR 10512*0147', 'Spider 1966 · rosso corsa',
                                                      'Ore di lavoro: 2.140'], 'Restaurata', K.sfondo_video(V_CRUSCOTTO, overlay=0.55)),
        ('0152', 'Spider 1600', 'del 1971.', ['Telaio  AR 30588*0152', 'Motore bialbero 1.570 cc',
                                             'In vendita: 48.000 €'], 'Omologata',
         K.sfondo_foto('pexels-10402008.jpg', overlay=0.5, scala=1.1)),
        ('0158', 'La lamiera', 'si rifà a mano.', ['Berlina 1968 · scocca', 'Lamiere sostituite: 14',
                                                   'Saldature a stagno'], 'In officina', K.sfondo_video(V_SCINTILLE, overlay=0.5)),
        ('0161', 'Dalla scocca', 'nuda.', ['Coupé 1969 · sverniciata', 'Fondo, stucco, sette mani',
                                           'Consegna: marzo 2027'], 'In verniciatura',
         K.sfondo_foto('pexels-18136808.jpg', overlay=0.55, scala=1.08)),
    ]
    sl = []
    for i, (num, r1, r2, dati, timbro, sfondo) in enumerate(pratiche):
        n = i + 1
        livelli = [
            K.livello('bi-o%d' % n, contenuto='Pratica n. %s · Modena' % num, x=6, y=24, w=40, size=15, peso='700',
                      colore=C['acc'], letterSpacing=2, fontFamily=MONO, ritardo=200, entra='fadeIn',
                      responsive={'mobile': {'fontSize': m(12), 'y': 37, 'x': 7, 'width': 86}}),
            K.livello('bi-t%d' % n, contenuto=r1, tag='h1' if n == 1 else 'h2', x=6, y=28.5, w=56, size=80, peso='700',
                      colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.02, entra='skewShortFromLeft', ritardo=350,
                      durata=900, responsive={'mobile': {'fontSize': m(38), 'y': 39.6, 'x': 7, 'width': 88}}),
            K.livello('bi-i%d' % n, contenuto=r2, tag='p', x=6, y=38.5, w=56, size=80, peso='700', colore=C['acc'],
                      fontFamily=TITOLI, lineHeight=1.02, entra='skewShortFromLeft', ritardo=550, durata=900,
                      responsive={'mobile': {'fontSize': m(38), 'y': 44.2, 'x': 7, 'width': 88}}),
        ]
        for k, riga in enumerate(dati):
            livelli.append(K.livello('bi-d%d%d' % (n, k), contenuto=riga, x=6, y=53 + k * 4, w=34, size=17, peso='400',
                                     colore=mix(C['chiaro'], 82), fontFamily=MONO, entra='fadeIn', ritardo=900 + 520 * k,
                                     durata=400, charAnim={'stagger': 22, 'offsetY': 0, 'blur': 0, 'opacity': 0},
                                     responsive={'mobile': {'fontSize': m(13), 'y': 51 + k * 2.6, 'x': 7, 'width': 86}}))
        sx, sy = 33, 54

        def kf(tempo, scala, opac):
            return {'time': tempo, 'props': {'x': sx, 'y': sy, 'scale': scala, 'rotation': -9, 'opacity': opac,
                                             'originX': 50, 'originY': 50}, 'easing': 'ease-out'}
        livelli += [
            # il timbro che cade di traverso sulla pratica
            K.livello('bi-z%d' % n, contenuto=timbro.upper(), x=sx, y=sy, w='auto', size=24, peso='700', colore=C['acc'],
                      fontFamily=TITOLI, letterSpacing=4, padding=12, borderWidth=4, borderColor=C['acc'], borderRadius=4,
                      opacity=88, timeline={'duration': 620, 'delay': 2600, 'loop': False,
                                            'keyframes': [kf(0, 2.6, 0), kf(380, 0.94, 92), kf(620, 1.0, 88)]},
                      responsive={'mobile': {'x': 44, 'y': 60.6, 'fontSize': m(16)}}),
            K.livello('bi-b%d' % n, tipo='button', contenuto='La storia del restauro', x=6, y=70, w='auto', size=12,
                      peso='700', colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2,
                      textTransform='uppercase', borderRadius=2, ritardo=2900, buttonUrl=RESTAURI,
                      hover={'bgColor': C['chiaro'], 'duration': 250}, responsive={'mobile': {'y': 68, 'x': 7, 'fontSize': m(11)}}),
        ]
        sl.append(K.diapositiva('bi-%d' % n, sfondo, livelli, durata=8000))
    # il contagiri nell’angolo: la lancetta sale e scende come un motore che si scalda
    cx, cy, cw, ch = 84.5, 63, 10, 16

    def ago(tempo, rot):
        return {'time': tempo, 'props': {'x': cx + cw / 2 - 0.12, 'y': cy + 2, 'scale': 1, 'rotation': rot, 'opacity': 100,
                                         'originX': 50, 'originY': 100}, 'easing': 'ease-in-out'}
    globali = [
        K.livello('bi-giri', tipo='shape', x=cx, y=cy, w=cw, h=ch, bgColor=mix(C['nero'], 55), borderRadius=999,
                  borderWidth=2, borderColor=mix(C['chiaro'], 35), backdropBlur=6, entra='fadeIn', ritardo=600,
                  responsive={'mobile': {'visible': False}}),
        K.livello('bi-rosso', tipo='shape', x=cx + 7.2, y=cy + 3.2, w=1.6, h=2.6, bgColor=C['acc'], borderRadius=999,
                  opacity=80, entra='fadeIn', ritardo=800, responsive={'mobile': {'visible': False}}),
        K.livello('bi-ago', tipo='shape', x=cx + cw / 2 - 0.12, y=cy + 2, w=0.24, h=6, bgColor=C['acc'], borderRadius=2,
                  boxShadow={'x': 0, 'y': 0, 'blur': 10, 'spread': 0, 'color': mix(C['acc'], 70)},
                  timeline={'duration': 5600, 'delay': 900, 'loop': True,
                            'keyframes': [ago(0, -115), ago(1300, 72), ago(2300, 18), ago(3200, 88), ago(4300, 30),
                                          ago(5600, -115)]},
                  responsive={'mobile': {'visible': False}}),
        K.livello('bi-perno', tipo='shape', x=cx + cw / 2 - 0.45, y=cy + ch / 2 - 0.72, w=0.9, h=1.44, bgColor=C['chiaro'],
                  borderRadius=999, entra='fadeIn', ritardo=800, responsive={'mobile': {'visible': False}}),
        K.livello('bi-giri-l', contenuto='× 1000 giri/min', x=cx - 1, y=cy + ch + 1.2, w=cw + 2, size=11, peso='700',
                  colore=mix(C['chiaro'], 70), align='center', fontFamily=MONO, entra='fadeIn', ritardo=900,
                  responsive={'mobile': {'visible': False}}),
    ]
    return K.proslider(t, sl, globali=globali, transizione='zoom', durata_trans=900, showArrows=True, arrowStyle='boxed',
                       showDots=True, dotStyle='bars', parallax=True, parallaxIntensity=4, autoplaySpeed=8000)


def scheda(t, aid, foto, nome, etich, descr, spec, prezzo):
    return t.classic('schedatecnica', {
        'codice': aid.upper(), 'nome': nome, 'etichetta': etich, 'descrizione': descr, 'immagine': K.img(foto),
        'titolo_specifiche': 'La pratica',
        'specifiche': [{'id': '%s-%d' % (aid, k), 'gruppo': g, 'nome': a, 'valore': b, 'unita': u}
                       for k, (g, a, b, u) in enumerate(spec)],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'Documenti in regola · targa originale',
        'cta_testo': 'Chiedi di vederla · %s' % prezzo, 'cta_url': OFFICINA + '#scrivici', 'cta_codice': True,
        'cta_parametro': 'auto', 'dati_strutturati': True, 'accento': C['acc'], 'sfondo_scheda': C['sala'],
        'colore_linee': C['filo'], 'righe_alternate': False, 'nome_font_size': 34, 'nome_font_weight': '600',
        'raggio': radius(2), 'raggio_pulsante': radius(2), 'padding_scheda': {'top': 40, 'right': 40, 'bottom': 40, 'left': 40},
        'img_ratio': '4/5', 'img_fit': 'cover', 'foto_posizione': 'sinistra', 'larghezza_foto': 44, 'gap': 48,
        'colonne_specifiche': 1, 'nome_color': C['chiaro'], 'descrizione_color': mix(C['chiaro'], 86),
        'gruppo_color': C['acc'], 'voce_color': C['tenue'], 'valore_color': C['testo'], 'unita_color': C['tenue'],
        'codice_color': mix(C['acc'], 70, C['chiaro'])}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    numeri = t.classic('statstrip', {
        'items': [{'value': '214', 'label': 'Auto restaurate dal 1989'}, {'value': '37', 'label': 'Anni di officina'},
                  {'value': '2.140', 'label': 'Ore per una spider'}, {'value': '9', 'label': 'Premi ai concorsi d’eleganza'}],
        'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'band_padding_y': 30, 'show_dividers': True,
        'divider_color': C['filo'], 'value_font_family': 'heading', 'value_color': C['chiaro'], 'value_size': 56,
        'value_weight': '600', 'label_color': C['tenue'], 'label_size': 12, 'label_uppercase': True, 'align': 'left',
        'mono_font_family': ''}, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dal 1989', ['Un’auto alla volta,', 'tutta dentro casa.'], size=60, layout='stack',
                                size_mobile=34)], wm='1-2', ws='1-1'),
               t.col([K.text(t, 'Franco ha aperto l’officina nel garage del padre, sulla via Emilia. Oggi siamo in sei: '
                                'carrozziere, motorista, verniciatore, tappezziere. Ogni auto ha la sua pratica, scritta a '
                                'macchina, con le foto di ogni bullone.', size=17, color=C['testo']),
                      K.button(t, 'L’officina', OFFICINA, pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='end'),
        t.one([numeri], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    fasi = t.classic('scrollscrub', {
        'behavior': 'inline', 'scroll_length': 3, 'align': 'start', 'gap': 20, 'show_progress': True, 'arrows': 'quadrate',
        'arrows_pos': 'fuori', 'arrows_bg': C['chiaro'], 'arrows_color': C['nero'], 'indicators': 'numeri',
        'indicators_color': C['tenue'], 'indicators_active_color': C['acc'], 'edge_fade': True, 'drag_scroll': True,
        'heading': '', 'kicker': '', 'item_width': 340, 'item_min_height': 460, 'round': 2,
        'item_padding': {'top': 18, 'right': 4, 'bottom': 8, 'left': 4}, 'text_color_default': C['testo'],
        'progress_color': C['acc'], 'show_number': True, 'overlay_scrim_color': C['nero'], 'overlay_scrim_opacity': 80,
        'overlay_scrim_height': 62,
        'items': [{'title': ti, 'subtitle': so, 'text': te, 'media': K.img(f)} for ti, so, te, f in [
            ('Lo smontaggio', 'Fase 1 · due settimane', 'Ogni pezzo fotografato, numerato e messo in una cassetta col '
             'numero di pratica.', 'pexels-4480505.jpg'),
            ('La lattoneria', 'Fase 2 · due mesi', 'La ruggine si taglia, la lamiera nuova si batte a mano sulle forme '
             'di legno.', 'pexels-39122952.jpg'),
            ('La verniciatura', 'Fase 3 · un mese', 'Fondo, stucco, carteggiatura, sette mani di colore e due di '
             'trasparente, in cabina.', 'pexels-14096710.jpg'),
            ('La meccanica', 'Fase 4 · un mese', 'Il motore aperto sul banco: pistoni, valvole, carburatori revisionati '
             'e tarati.', 'pexels-32674055.jpg'),
            ('Gli interni', 'Fase 5 · tre settimane', 'Pelle, panno e legno come usciti dalla fabbrica, cuciti dal '
             'tappezziere di Carpi.', 'pexels-18262241.jpg'),
            ('Il collaudo', 'Fase 6 · cinquecento chilometri', 'Sull’Appennino, in salita e in discesa, fino a quando '
             'non si sente più niente che non deve sentirsi.', 'pexels-17377224.jpg')]]}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Le fasi', ['Sei mesi', 'per una pratica.'], size=60,
                         tagline='Trascinate o scorrete: ogni fase con il suo tempo. Le foto di ogni passaggio finiscono nel '
                                 'libro dell’auto, che vi consegniamo con le chiavi.')]),
        t.one([fasi]),
    ], bg=solid(C['sala']), padding='large', width='large', html_id='fasi', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_VERNICE, 'In cabina', 'Sette mani di vernice, <em>tutte a mano.</em>',
                             'Il rosso si prepara in officina col campione originale, il trasparente si stende la mattina '
                             'presto, quando non c’è polvere.', overlay=55))
    out.append(t.section([
        t.one([K.sechead(t, 'In vendita', ['Ogni tanto', 'una la teniamo.'], size=60,
                         tagline='Auto restaurate da noi, con la pratica completa e cinquecento chilometri di collaudo. '
                                 'Si vedono in officina, si provano su strada.')]),
        t.one([scheda(t, 'sp71', 'pexels-33014967.jpg', 'Spider 1600 · 1971', 'Restaurata nel 2025',
                      'Rosso corsa, interni in pelle nera, capote nuova. Restauro completo in diciotto mesi, motore '
                      'rifatto e omologazione d’epoca.',
                      [('Motore', 'Cilindrata', '1.570', 'cc'), ('Motore', 'Potenza', '109', 'CV'),
                       ('Strada', 'Dal restauro', '2.300', 'km'), ('Strada', 'Cambio', '5 marce', ''),
                       ('Documenti', 'Prezzo', '48.000', '€')], '48.000 €')]),
        t.one([K.button(t, 'Tutte le auto in vendita', VENDITA, pad_top=34)]),
    ], bg=solid(C['nero']), padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_STRADA, 'Il collaudo', 'Cinquecento chilometri <em>sull’Appennino.</em>',
                          'Prima di riconsegnarla, l’auto fa la strada del passo delle Radici: salite, tornanti, una notte '
                          'al freddo. Poi si controlla tutto, ancora una volta.', 'I restauri', RESTAURI, overlay=50,
                          video=True))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Bialbero',
                page_intro_caption='Restauri d’epoca · Modena', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1600, page_intro_once=True)


def build_restauri():
    t = Tpl(ID, 'restauri')
    out = [K.intro(t, 'I restauri', ['Duecentoquattordici', 'pratiche chiuse.'],
                   'Spider, berline, coupé, qualche camioncino. Alcune tornano ai concorsi d’eleganza, quasi tutte '
                   'tornano sulla strada, che è il posto loro.', bg=K.photo_bg('pexels-4489766.jpg', overlay=55),
                   overlay_text=True, size=84)]
    lavori = t.classic('overlaygrid', {
        'items': [{'image': K.img(f), 'title': ti, 'subtitle': su, 'link': OFFICINA + '#scrivici', 'ribbon': r}
                  for f, ti, su, r in [
                      ('pexels-33380762.jpg', 'Berlina 1968', 'Pratica 0139 · 1.600 ore', ''),
                      ('pexels-38203113.jpg', 'Coupé 1962', 'Pratica 0128 · gomme a fascia bianca', 'Concorso 2024'),
                      ('pexels-30767235.jpg', 'Berlinetta 1958', 'Pratica 0112 · cromature rifatte', ''),
                      ('pexels-37881079.jpg', 'Gran turismo 1964', 'Pratica 0131 · fari e calandra', ''),
                      ('pexels-8310758.jpg', 'Cabriolet 1969', 'Pratica 0144 · in consegna', 'Nuova'),
                      ('pexels-31988791.jpg', 'Berlina 1972', 'Pratica 0121 · collaudo', '')]],
        'columns': '3', 'columns_tablet': '2', 'columns_mobile': '1', 'gap': 'medium', 'height': '420', 'image_ratio': '4/5',
        'layout_mode': 'uniform', 'overlay_position': 'bottom', 'overlay_style': 'overlay-primary',
        'hover_effect': 'zoom', 'hover_overlay': 'always', 'ribbon_bg': C['acc'], 'ribbon_color': C['on_acc'],
        'item_radius': 2, 'overlay_color': mix(C['nero'], 72), 'title_color': C['chiaro'], 'title_weight': '600',
        'subtitle_color': mix(C['chiaro'], 80), 'subtitle_size': 14}, style={})
    out.append(t.section([t.one([lavori])], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    for k, (aid, foto, titolo, chi, testo, dati) in enumerate([
        ('motori', 'pexels-19581671.jpg', 'Il motore sul banco', 'Meccanica · Nadia', 'Ogni motore si apre, si misura e '
         'si rimonta con i ricambi originali o rifatti al tornio. I carburatori si tarano sul banco a rulli, non a orecchio.',
         [('Motori rifatti', 'oltre 180'), ('Banco a rulli', 'in officina'), ('Ricambi', 'originali o rifatti')]),
        ('interni', 'pexels-2810140.jpg', 'Il cruscotto come nuovo', 'Interni · Walter', 'Strumenti revisionati, '
         'legno restaurato, la pelle tagliata sul modello originale. Il volante si rifà a mano, in tre settimane.',
         [('Tappezziere', 'a Carpi, dal 1974'), ('Strumenti', 'revisionati e tarati'), ('Pelle', 'conciata al vegetale')]),
    ]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, titolo, ratio='4/5')], wm='2-5', ws='1-1'),
                   t.col([K.label(t, chi, color=C['acc']), K.headline(t, titolo, size=52, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati)], wm='3-5', ws='1-1')],
                  layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 == 0 else None,
            style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_FAVILLE, 'In lattoneria', 'La ruggine <em>non si copre.</em>',
                             'Si taglia, si butta e si rifà la lamiera. È il lavoro più lungo, e quello che non si vede: per '
                             'questo lo fotografiamo tutto.', overlay=50))
    return out


def build_vendita():
    t = Tpl(ID, 'in-vendita')
    out = [K.intro(t, 'In vendita', ['Restaurate da noi,', 'con la pratica.'],
                   'Poche auto, scelte una per una e restaurate in officina. Si vedono su appuntamento, si provano su '
                   'strada, e la pratica con le foto di ogni fase viene con le chiavi.',
                   bg=K.photo_bg('pexels-19581670.jpg', overlay=55), overlay_text=True)]
    auto = [
        ('sp71', 'pexels-33014967.jpg', 'Spider 1600 · 1971', 'Restaurata nel 2025', 'Rosso corsa, interni in pelle nera, '
         'capote nuova. Diciotto mesi di restauro, motore rifatto e omologazione d’epoca.',
         [('Motore', 'Cilindrata', '1.570', 'cc'), ('Motore', 'Potenza', '109', 'CV'), ('Strada', 'Dal restauro', '2.300', 'km'),
          ('Strada', 'Cambio', '5 marce', ''), ('Documenti', 'Prezzo', '48.000', '€')], '48.000 €'),
        ('be68', 'pexels-33380762.jpg', 'Berlina 1968', 'Restaurata nel 2024', 'Una berlina di famiglia, rimasta in '
         'garage trent’anni. Carrozzeria rifatta, interni in panno originale, motore revisionato.',
         [('Motore', 'Cilindrata', '1.198', 'cc'), ('Motore', 'Potenza', '60', 'CV'), ('Strada', 'Dal restauro', '4.100', 'km'),
          ('Strada', 'Cambio', '4 marce', ''), ('Documenti', 'Prezzo', '16.500', '€')], '16.500 €'),
        ('co69', 'pexels-8310738.jpg', 'Cabriolet 1969', 'Restauro in chiusura', 'Argento, capote in tela blu, cromature '
         'rifatte. Si può prenotare adesso e scegliere il colore degli interni.',
         [('Motore', 'Cilindrata', '2.496', 'cc'), ('Motore', 'Potenza', '150', 'CV'), ('Strada', 'Consegna', 'aprile 2027', ''),
          ('Strada', 'Cambio', 'automatico', ''), ('Documenti', 'Prezzo', 'su richiesta', '')], 'su richiesta'),
    ]
    for k, (aid, foto, nome, etich, descr, spec, prezzo) in enumerate(auto):
        out.append(t.section([t.one([scheda(t, aid, foto, nome, etich, descr, spec, prezzo)])], padding='large',
                             width='large', html_id=aid, bg=solid(C['nero']) if k % 2 else None,
                             style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(K.domande(t, 'Prima', 'di una prova su strada.', [
        ('Si possono provare?', 'Sì, su appuntamento, con uno di noi a bordo. Serve la patente da almeno tre anni.'),
        ('Fate le permute?', 'A volte: se l’auto ci interessa la valutiamo in officina, con calma.'),
        ('E dopo l’acquisto?', 'Il primo tagliando è compreso. Per due anni rispondiamo noi di tutto quello che abbiamo fatto.'),
        ('Spedite all’estero?', 'Sì, con la bisarca chiusa, in tutta Europa. Le pratiche le seguiamo noi.'),
    ], 've', bg=True))
    return out


def build_officina():
    t = Tpl(ID, 'l-officina')
    out = [K.intro(t, 'L’officina', ['Sei persone,', 'un ponte sollevatore.'],
                   'Novecento metri quadri sulla via Emilia: la lattoneria, la cabina di verniciatura, il banco motori e la '
                   'stanza delle pratiche, dove si scrive tutto a macchina.', bg=K.photo_bg('pexels-16243258.jpg', overlay=55),
                   overlay_text=True)]
    persone = [('pexels-31295161.jpg', 'Franco Bettini', 'Carrozzeria e lattoneria', 'Ha aperto nel 1989 nel garage del '
                'padre. Batte la lamiera a mano sulle forme di legno che si è fatto da solo.'),
               ('pexels-7006697.jpg', 'Nadia Rinaldi', 'Meccanica e motori', 'Motorista, entrata come apprendista nel '
                '2008, socia dal 2019. I carburatori li tara lei, sul banco a rulli.'),
               ('pexels-4486212.jpg', 'Walter Gibertini', 'Verniciatura e interni', 'Prepara il colore col campione '
                'originale e segue il tappezziere. Il rosso corsa è la sua specialità.')]
    cols = []
    for foto, nome, ruolo, bio in persone:
        cols.append(t.col([t.classic('team', {
            'photo': K.img(foto), 'hover_image': '', 'hover_video': '', 'name': nome, 'role': ruolo, 'bio': bio,
            'link_text': '', 'link_url': '', 'photo_size': '300', 'photo_ratio': '4/5', 'photo_fit': 'cover',
            'photo_object_position': '50% 25%', 'photo_shape': 'rounded', 'photo_radius': '2', 'photo_border_width': '0',
            'photo_shadow': 'none', 'info_bg_color': C['sala'], 'info_text_color': C['testo'], 'info_padding': '24',
            'info_radius': '2', 'info_align': 'left', 'name_size': '22', 'name_weight': '600', 'role_size': '13',
            'role_color': C['acc'], 'bio_size': '15', 'bg_color': 'transparent'}, style={})], wm='1-3', ws='1-1'))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi ci lavora', ['Le mani', 'dietro le pratiche.'], size=56)]),
        t.row(cols, layout='33-33-33', gap=24, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'), K.campo('text', 'L’auto', 'auto', True, param='auto',
                                                            segnaposto='Marca, modello e anno'),
             K.campo('select', 'Cosa serve', 'cosa', opzioni='Restauro completo\nSolo carrozzeria\nSolo meccanica\nInterni\nVorrei vedere un’auto in vendita'),
             K.campo('textarea', 'Raccontateci l’auto', 'messaggio', larghezza='1-1',
                     segnaposto='Da quanto è ferma, dov’è, se avete le foto. La storia ci interessa.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Una pratica nuova', ['Portatecela,', 'o mandateci le foto.'],
                                'Il primo sopralluogo è gratuito, anche a domicilio in Emilia. Poi vi scriviamo il '
                                'preventivo per fasi, con i tempi.', VOCI, campi, 'Bialbero — una pratica nuova',
                                'Manda', 'Ricevuto. Vi chiamiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Sulla via Emilia,', 'il portone rosso.'], size=52, layout='stack',
                                size_mobile=34),
                      K.specs(t, [('Indirizzo', 'via Emilia Est 1220, Modena'), ('Dall’autostrada', 'Modena Sud, otto minuti'),
                                  ('Orari', 'lun — ven 8 — 12 e 14 — 18:30'), ('Il sabato', 'su appuntamento')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Bialbero · restauri d’epoca', hex_marker=HEX['primary'], zoom='15')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pratica non trovata', color=C['acc'], align='center'),
                       K.headline(t, 'Questa strada <em>è chiusa.</em>', size=88, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Facciamo inversione e torniamo in officina.', size=17, align='center', color=C['soft']),
                       K.button(t, 'Torna in officina', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_STRADA, overlay=55), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Industria e manifattura', 'Un’auto alla volta',
    ['auto d’epoca', 'restauro', 'officina', 'modena', 'scuro', 'slider pro'],
    'Un’officina di restauro di auto d’epoca a Modena, nero d’officina e rosso corsa: in apertura le pratiche scritte a '
    'macchina lettera per lettera, col timbro che cade di traverso e il contagiri che sale e scende in un angolo; le sei '
    'fasi da trascinare, le schede delle auto in vendita, i restauri e le persone dell’officina.',
    [('I restauri', RESTAURI), ('In vendita', VENDITA), ('Le fasi', HOME + '#fasi'), ('L’officina', OFFICINA)],
    [('home', 'Home', None), ('restauri', 'I restauri', 'restauri'), ('in-vendita', 'In vendita', 'in-vendita'),
     ('l-officina', 'L’officina', 'l-officina')],
    HEX, '\'Sofia Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Archivo\', \'Arial\', sans-serif',
    '\'Courier Prime\', \'Courier New\', monospace', ['Archivo', 'Sofia Sans', 'Courier Prime'], corsivo_di=[],
    peso_titoli='700', raggio=2, grana=6, titoli_line='1.0', titoli_spazio='-0.02em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'restauri.json': build_restauri(), 'in-vendita.json': build_vendita(), 'l-officina.json': build_officina(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
