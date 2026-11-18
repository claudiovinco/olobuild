# -*- coding: utf-8 -*-
"""Genera il tema «Vetrino» (id vetrino): un laboratorio di analisi a Parma che i referti li spiega.

La trovata: lo zoom che non finisce. Nell'apertura lo scorrimento entra nel volto di una persona: si apre una lente sulla
pelle, dentro la pelle una lente sulle cellule, poi sulla molecola, poi sul DNA, e alla fine il DNA si spegne e dietro c'è
il laboratorio, con la ricercatrice al microscopio. Lo Slider Pro fermo durante lo scorrimento e cinque livelli che
crescono a velocità costante (andamento «costante»: la scala sale in proporzione, come in un video «potenze di dieci»).
Le lenti sono riquadri quadrati con gli angoli tondi al massimo: a scala 1,2 coprono la tela, al computer e al telefono.

Tile poco usate messe alla prova: Evo Notes (mai usata), le note del vetrino scritte sopra la pagina, Terminale (mai usata) che
scrive le righe del referto, Avviso (mai usata) per il digiuno, Indice e Tempo di lettura nella guida al prelievo.
Tema chiaro: bianco di laboratorio, viola e rosa dei vetrini colorati (Giemsa ed eosina), ciano. Titoli in Sora, testo in
Inter Tight. Cursore: oculare.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'vetrino'
NAME = 'Vetrino'
CATEGORIA = 'Salute e cura'

HEX = {
    'primary': '#6A35D9', 'primary_contrast': '#FFFFFF', 'secondary': '#E8467C', 'secondary_contrast': '#FFFFFF',
    'accent': '#22B8CF', 'dark': '#0F1720', 'light': '#FFFFFF', 'muted': '#E9EEF4', 'muted_contrast': '#16222E',
    'text': '#16222E', 'text_muted': '#56636F', 'background': '#F6F8FB', 'border': '#D5DEE7', 'link': '#5A2BC4',
}
HOME, ESAMI, PREPARARSI, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:gli-esami}}', '{{PAGINA:prepararsi}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'ciao@vetrino.example', '0521 000 379', 'tel:+390521000379'
LAT, LNG = '44.8015', '10.3279'

V_MICRO, V_CELLULE, V_CELLULE2, V_PROVETTE, V_PIPETTA, V_OCCHIO, V_PELLE = (
    'pexels-video-4121129', 'pexels-video-29851224', 'pexels-video-35637540', 'pexels-video-11218535', 'pexels-video-8852382',
    'pexels-video-5944685', 'pexels-video-7298045')
VIDEOS = [V_MICRO, V_CELLULE, V_CELLULE2, V_PROVETTE, V_PIPETTA, V_OCCHIO, V_PELLE]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(VOLTO, PELLE, CELLULE_BN, SANGUE, GLOBULI, CELLULE_GIALLE, MOLECOLA, DNA_COLORI, DNA, OCCHIO, RICERCATRICE, MICROSCOPI,
 PROVETTA_MANO, PROVETTE_VERDE, TECNICO, LAB, TECNICA, LAB2, INFERMIERE, PAZIENTE, PIASTRA, PIPETTE, MODELLO) = f(
    37494545, 7479630, 8932609, 36372301, 29859866, 36372296, 25626509, 13014236, 35967917, 2440264, 8442568, 15290006,
    4040557, 4047146, 9574483, 8442509, 8851362, 3735786, 35645506, 6285376, 4031435, 8940509, 7722849)
PHOTOS = [VOLTO, PELLE, CELLULE_BN, SANGUE, GLOBULI, CELLULE_GIALLE, MOLECOLA, DNA_COLORI, DNA, OCCHIO, RICERCATRICE,
          MICROSCOPI, PROVETTA_MANO, PROVETTE_VERDE, TECNICO, LAB, TECNICA, LAB2, INFERMIERE, PAZIENTE, PIASTRA, PIPETTE, MODELLO]
LOGHI = ['vetrino-logo-chiaro.png', 'vetrino-logo-scuro.png']
MEDIA = set(PHOTOS) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#7FE3F2')
C = K.c
FONT_T = 'Sora'
MONO = 'var(--olo-font-family-mono)'
VOCI = [('map-pin', 'Borgo delle Colonne 26 · Parma', PRENOTA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 172, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '590/120',
                   'Vetrino è un laboratorio di analisi cliniche a Parma dal 1979: prelievi senza appuntamento la mattina, '
                   'referti sul telefono e spiegati a voce da chi li ha fatti.',
                   [('Gli esami', ESAMI), ('Prepararsi', PREPARARSI), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'I prelievi', 'Lunedì — sabato 7 — 10, senza appuntamento<br>Ritiro referti 11 — 18', VOCI,
                   '© 2026 Vetrino srl · Parma · P.IVA 00000000000 · Direttore sanitario dott.ssa M. Ferri',
                   [('instagram', 'https://instagram.com'), ('linkedin', 'https://linkedin.com')], scuro=False)


# ── L'apertura: lo zoom che non finisce ──
def lente(lid, foto, alt, tappe):
    """Una lente: un riquadro quadrato con gli angoli tondi al massimo (un cerchio) e il bordo chiaro di un vetrino; al
    computer 1440 × 1440 al centro della tela 1440 × 810, al telefono 1408 × 1408 al centro della tela 640 × 1400."""
    return K.livello(lid, tipo='image', imageSrc=K.img(foto), imageAlt=alt, objectFit='cover', x=0, y=-38.89, w=100, h=177.78,
                     borderRadius=9999, entra='fadeIn', ritardo=0, durata=300,
                     customCSS='transform-origin:50% 50%;box-shadow:0 0 0 5px rgba(255,255,255,.9),0 0 80px rgba(0,0,0,.55);',
                     scrollMove={'inizio': 0, 'fine': 100, 'andamento': 'costante', 'tappe': tappe},
                     responsive={'mobile': {'x': -60, 'y': -0.29, 'width': 220, 'height': 100.57}})


def zoom(da, pieno, fine, s_fine=26, vai=None):
    """Le tappe di una lente: minuscola (0,05) fino a `da`, piena (1,2) a `pieno`, poi grandissima fino a `fine`; compare
    subito dopo `da` e si spegne quando la lente dopo l'ha coperta (`vai`: (da, a) del suo spegnersi)."""
    vai = vai or (fine - 4, fine + 1)
    return [{'p': 0, 's': 0.05, 'o': 0}, {'p': da, 's': 0.05, 'o': 0}, {'p': da + 3, 's': 0.05 * (1.2 / 0.05) ** (3.0 / (pieno - da)), 'o': 100},
            {'p': pieno, 's': 1.2, 'o': 100}, {'p': vai[0], 's': 1.2 * (s_fine / 1.2) ** ((vai[0] - pieno) / float(fine - pieno)), 'o': 100},
            {'p': min(100, vai[1]), 's': s_fine, 'o': 0}, {'p': 100, 's': s_fine, 'o': 0}]


def apertura(t):
    m = K.mob
    finestra = lambda da, a, via_da, via_a: {'inizio': 0, 'fine': 100, 'tappe': [
        {'p': 0, 'o': 0, 'y': 6}, {'p': da, 'o': 0, 'y': 6}, {'p': a, 'o': 100, 'y': 0}, {'p': via_da, 'o': 100, 'y': 0},
        {'p': via_a, 'o': 0, 'y': -6}, {'p': 100, 'o': 0, 'y': -6}]}
    poi = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 14}, {'p': da, 'o': 0, 'y': 14},
                                                             {'p': a, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    via = lambda da, a: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': da, 'o': 100}, {'p': a, 'o': 0},
                                                             {'p': 100, 'o': 0}]}
    L = [
        K.livello('vt-volto', tipo='image', imageSrc=K.img(VOLTO), imageAlt='Il volto di una ragazza che sorride, da vicino',
                  objectFit='cover', x=0, y=0, w=100, h=100, entra='fadeIn', ritardo=0, durata=400,
                  customCSS='transform-origin:50% 50%;',
                  scrollMove={'inizio': 0, 'fine': 100, 'andamento': 'costante', 'tappe': [
                      {'p': 0, 's': 1, 'o': 100}, {'p': 8, 's': 1, 'o': 100}, {'p': 26, 's': 1 * 26 ** (18 / 22.0), 'o': 100},
                      {'p': 30, 's': 26, 'o': 0}, {'p': 100, 's': 26, 'o': 0}]},
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}}),
        lente('vt-pelle', PELLE, 'La pelle vista da vicinissimo', zoom(8, 30, 50)),
        lente('vt-cellule', SANGUE, 'Le cellule del sangue nel cerchio del microscopio', zoom(30, 50, 68)),
        lente('vt-molecola', MOLECOLA, 'Una molecola, i suoi atomi azzurri', zoom(50, 68, 84, s_fine=18)),
        lente('vt-dna', DNA, 'La doppia elica del DNA', zoom(68, 84, 100, s_fine=1.6, vai=(88, 96))),
    ]
    L += K.veli('vt-velo', 'basso', 26, 52, colore=C['nero'], forza=84, mobile_da=58)
    scala = [('La persona · 1,7 m', 0, 0, 24, 28), ('La pelle · 1 mm', 26, 30, 46, 50), ('La cellula · 10 µm', 48, 52, 64, 68),
             ('La molecola · 1 nm', 66, 70, 80, 84), ('Il DNA · 2 nm', 82, 86, 92, 96)]
    for k, (testo, a1, a2, v1, v2) in enumerate(scala):
        L.append(K.livello('vt-scala%d' % k, contenuto=testo, x=6, y=14, w=40, size=14, peso='600', colore=C['chiaro'],
                           letterSpacing=3, textTransform='uppercase', fontFamily=MONO, entra='fadeIn', ritardo=0 if k else 300,
                           scrollMove=finestra(max(0.01, a1), max(0.02, a2), v1, v2) if k else via(v1, v2),
                           responsive={'mobile': {'fontSize': m(11), 'x': 6, 'y': 6, 'width': 88}}))
    L += [
        K.livello('vt-t', contenuto='Ingrandiamo.', tag='h1', x=6, y=64, w=60, size=112, peso='700', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=1.0, letterSpacing=-2, entra='fadeInUp', ritardo=300, durata=900,
                  scrollMove=via(22, 28), responsive={'mobile': {'fontSize': m(54), 'x': 6, 'y': 74, 'width': 88}}),
        K.livello('vt-s', contenuto='Scorri: entriamo dove guarda il microscopio.', x=6, y=80, w=40, size=20, peso='500',
                  colore=mix(C['chiaro'], 90), lineHeight=1.4, ritardo=500, scrollMove=via(22, 28),
                  responsive={'mobile': {'fontSize': m(16), 'x': 6, 'y': 80.6, 'width': 84}}),
        K.livello('vt-f1', contenuto='Il referto,', x=6, y=58, w=60, size=96, peso='700', colore=C['chiaro'], fontFamily=FONT_T,
                  lineHeight=1.0, letterSpacing=-2, entra='fadeIn', ritardo=0, scrollMove=poi(90, 96),
                  responsive={'mobile': {'fontSize': m(48), 'x': 6, 'y': 70, 'width': 88}}),
        K.livello('vt-f2', contenuto='spiegato a te.', x=6, y=70, w=60, size=96, peso='700', colore=C['acc2'], fontFamily=FONT_T,
                  lineHeight=1.0, letterSpacing=-2, entra='fadeIn', ritardo=0, scrollMove=poi(91, 97),
                  responsive={'mobile': {'fontSize': m(48), 'x': 6, 'y': 74.6, 'width': 88}}),
        K.livello('vt-b1', tipo='button', contenuto='Prenota il prelievo', x=6, y=84, w='auto', size=15, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=18, borderRadius=10, buttonUrl=PRENOTA, entra='fadeIn',
                  ritardo=0, scrollMove=poi(94, 99), hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 81.6, 'fontSize': m(13)}}),
        K.livello('vt-b2', tipo='button', contenuto='Gli esami', x=21, y=84, w='auto', size=15, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=10, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  buttonUrl=ESAMI, entra='fadeIn', ritardo=0, scrollMove=poi(94, 99),
                  hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250}, responsive={'mobile': {'x': 56, 'y': 81.6, 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('vt-1', K.sfondo_video(V_MICRO, overlay=0.2, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=3400, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


ESAMI_VOCI = [
    ('Emocromo', 'I globuli rossi, i bianchi e le piastrine', '6,80 €'),
    ('Glicemia', 'Lo zucchero nel sangue, a digiuno', '3,20 €'),
    ('Colesterolo completo', 'Totale, HDL, LDL e trigliceridi', '9,50 €'),
    ('Ferritina', 'Le scorte di ferro', '8,40 €'),
    ('Vitamina D', 'Quella del sole, d’inverno spesso bassa', '22,00 €'),
    ('Tiroide', 'TSH, e se serve FT3 e FT4', '12,60 €'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Parma · dal 1979', ['Il referto', 'non è un rebus.'], size=76, layout='stack',
                                size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Ti misuriamo il sangue con gli stessi strumenti di un ospedale. Poi, cosa che un ospedale '
                                'non ha tempo di fare, ti spieghiamo i numeri: quelli in grassetto, quelli con l’asterisco, '
                                'quelli che non contano niente.', size=17),
                      K.button(t, 'Gli esami e i prezzi', ESAMI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    # Terminale: il referto che si scrive
    out.append(t.section([t.one([t.bare('terminalhero', {
        'show_label': True, 'label': 'IL TUO REFERTO · [PRONTO] ALLE 11',
        'heading': 'I numeri, e cosa vogliono dire.',
        'type_phrases': [{'text': 'emoglobina 13,8: nella norma'}, {'text': 'colesterolo LDL 142: da ricontrollare fra sei mesi'},
                         {'text': 'vitamina D 18: bassa, come in mezza Parma a febbraio'}, {'text': 'ferritina 64: scorte buone'}],
        'type_prefix': '› ', 'type_speed': 45, 'type_pause': 1500,
        'subhead': 'Il referto arriva sul telefono con una riga accanto a ogni valore. Se un numero è fuori dalla norma ti '
                   'chiamiamo noi, prima che tu lo legga.',
        'show_form': False, 'small_text': '', 'small_link_text': '', 'cta1_text': 'Prenota il prelievo', 'cta1_url': PRENOTA, 'cta2_text': 'Come prepararsi',
        'cta2_url': PREPARARSI, 'img_left': K.img(PROVETTA_MANO), 'img_right': K.img(PIPETTE), 'side_width': 420,
        'side_opacity': 100, 'show_crosshairs': True, 'show_topline': True, 'bg_color': C['sala'], 'text_color': C['testo'],
        'sub_color': C['tenue'], 'accent': C['acc'], 'accent_on': C['on_acc'], 'h_size_max': 76, 'type_size_max': 28,
        'align': 'center', 'max_width': 1100, 'min_height': 70})], anim=None)], padding='remove-vertical', width='fullbleed',
        html_id='referto'))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli esami più chiesti', ['Sei esami,', 'sei righe di spiegazione.'], size=60)]),
        P.righe(K, t, [('%02d' % (k + 1), n, d, p) for k, (n, d, p) in enumerate(ESAMI_VOCI)], ESAMI),
    ], padding='large', width='large', html_id='esami', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Una mattina da noi', ['Dal prelievo', 'al referto.'], size=60)]),
        P.passi(K, t, [('7:00', 'Entri', 'Senza appuntamento: prendi il numero, ti chiamiamo in media dopo undici minuti.'),
                       ('7:15', 'Il prelievo', 'Un ago sottile, una provetta per ogni gruppo di esami. Due minuti.'),
                       ('9:00', 'Il laboratorio', 'Le provette vanno negli analizzatori; quelle strane le guardiamo al '
                        'microscopio.'),
                       ('11:00', 'Il referto', 'Arriva sul telefono, con le righe di spiegazione. Se serve, ti chiamiamo.')],
                colonne=4),
    ], padding='large', width='large', html_id='mattina', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(X.recensioni(K, t, [
        ('Il valore in rosso era il ferro. Mi ha chiamata la biologa e in tre minuti mi ha tolto l’ansia.', 'Paola', 'Oltretorrente'),
        ('Prelievo alle sette e un quarto, al lavoro alle otto. Il referto mi è arrivato prima del pranzo.', 'Davide', 'San Lazzaro'),
        ('Mia figlia ha paura degli aghi: le hanno fatto guardare il microscopio prima. Non ha pianto.', 'Hana', 'Montanara')],
        'Chi è passato', ['Tre mattine,', 'tre referti capiti.']))
    out[-1].setdefault('advanced', {})['html_id'] = 'chi'
    out.append(K.pannello(t, RICERCATRICE, 'Lunedì — sabato, 7 — 10', 'Il prelievo <em>senza appuntamento.</em>',
                          'Vieni a digiuno, con la tessera sanitaria. Per i bambini c’è la sala coi pesci.', 'Prepararsi',
                          PREPARARSI, effetto=''))
    # Evo Notes: le note del vetrino scritte sopra la pagina (il pulsante fisso in basso a sinistra le accende, ogni numero
    # sta accanto alla sua sezione e apre il «prima → adesso»)
    out[-1]['children'][0]['children'][0]['children'].append(t.bare('evonotes', {
        'toggle_label': 'Il referto di prima e di adesso', 'toggle_label_active': 'Nascondi le note', 'toggle_side': 'left',
        'show_hint': True, 'hint_text': 'Tocca i numeri sulla pagina — prima → adesso', 'kicker_label': 'Il referto',
        'accent': C['acc'], 'card_bg': C['fondo'], 'text_color': C['testo'],
        'items': [{'number': '01', 'title': 'Dove lo leggi', 'text': 'Il referto arriva sul telefono alle 11, protetto da un '
                   'codice. Niente coda allo sportello.', 'before': 'Carta allo sportello', 'after': 'Sul telefono',
                   'anchor': 'referto', 'side': 'right', 'offset': '22%'},
                  {'number': '02', 'title': 'Come lo leggi', 'text': 'Accanto a ogni valore, una riga in italiano: cosa '
                   'misura e se devi preoccuparti.', 'before': 'Solo numeri', 'after': 'Numeri e parole', 'anchor': 'esami',
                   'side': 'left', 'offset': '30%'},
                  {'number': '03', 'title': 'Quanto aspetti', 'text': 'Prelievo senza appuntamento dalle 7 alle 10, in media '
                   'undici minuti di attesa.', 'before': 'Quaranta minuti', 'after': 'Undici', 'anchor': 'mattina',
                   'side': 'right', 'offset': '26%'},
                  {'number': '04', 'title': 'Chi ti chiama', 'text': 'Un valore fuori dalla norma? Ti chiama la biologa che '
                   'l’ha visto, non un centralino.', 'before': 'Nessuno', 'after': 'La biologa', 'anchor': 'chi',
                   'side': 'left', 'offset': '40%'}]}))
    return out


def build_esami():
    t = Tpl(ID, 'gli-esami')
    out = [K.intro(t, 'Gli esami', ['Duecento esami,', 'i prezzi scritti.'], 'Con l’impegnativa del medico paghi il ticket; '
                   'senza, paghi i prezzi qui sotto. Il referto è lo stesso.', bg=K.video_bg(V_PROVETTE, overlay=20), size=104)]
    out.append(t.section([
        t.one([K.sechead(t, 'I più chiesti', ['Il listino,', 'senza sorprese.'], size=60)]),
        P.listino(K, t, [(n, d, p, '', '') for n, d, p in ESAMI_VOCI] + [
            ('Urine', 'L’esame completo, col sedimento', '4,10 €', '', ''),
            ('PSA', 'La prostata, dopo i cinquant’anni', '14,30 €', '', ''),
            ('Intolleranza al lattosio', 'Il test del respiro, tre ore', '45,00 €', '', 'su appuntamento')], immagini=False),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'I pacchetti', ['Tre controlli', 'già composti.'], size=60, align='center')]),
        K.piani(t, [('Il controllo di base', '29', 'una volta all’anno', 'Emocromo\nGlicemia\nColesterolo completo\nUrine', False, ''),
                    ('Il controllo completo', '69', 'dopo i quarant’anni', 'Il controllo di base\nFerritina\nTiroide\n'
                     'Vitamina D\nFegato e reni', True, 'Il più scelto'),
                    ('Gli sportivi', '49', 'per la visita agonistica', 'Emocromo\nGlicemia\nUrine\nFerritina\nCK', False, '')],
                'Prenota', PRENOTA + '#scrivi', nota='Prelievo compreso · referto il giorno stesso'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_CELLULE, 'Al microscopio', 'Quando la macchina <em>non basta.</em>',
                             'Uno striscio su vetrino, colorato di viola e di rosa: lo guarda una persona, cellula per cellula.'))
    return out


def build_prepararsi():
    t = Tpl(ID, 'prepararsi')
    out = [K.intro(t, 'Prepararsi', ['Prima del prelievo,', 'quattro cose.'], 'Il digiuno, l’acqua, le medicine, le urine: '
                   'le regole sono poche, e qui sono scritte tutte.', bg=K.video_bg(V_PIPETTA, overlay=20), size=104)]
    out.append(t.section([
        t.row([t.col([t.classic('toc', {'title': 'In questa pagina', 'max_depth': '2', 'list_style': 'numbered',
                                         'text_color': C['testo'], 'link_color': C['acc'], 'title_color': C['testo'],
                                         'font_size': '15', 'sticky': True, 'highlight_active': True, 'smooth_scroll': True},
                                style={}),
                      t.classic('readingtime', {'words_per_minute': 200, 'format': 'full', 'prefix': 'Da leggere in',
                                                'suffix': 'minuti', 'icon': 'clock', 'show_icon': True,
                                                'text_color': C['tenue'], 'icon_color': C['acc'], 'font_size': '14'},
                                style={})], wm='1-3', ws='1-1'),
               t.col([t.bare('alert', {'preset': 'custom', 'alert_type': 'warning', 'title': 'Il digiuno vale per quasi tutti',
                                       'message': 'Da mezzanotte niente cibo, né caffè, né sigarette. L’acqua sì, anzi: '
                                                  'bevine un bicchiere prima di uscire.', 'show_icon': True,
                                       'dismissible': False, 'custom_bg_color': mix(C['acc2'], 14, C['fondo']),
                                       'custom_text_color': C['testo'], 'border_radius': '10'}),
                      K.headline(t, 'Il digiuno', size=36, tag='h2', pad_bottom=10),
                      K.text(t, 'Otto ore senza mangiare: cena alle venti, prelievo dopo le sette. Serve per la glicemia, il '
                                'colesterolo e i trigliceridi. Per l’emocromo da solo non servirebbe, ma quasi nessuno fa '
                                'l’emocromo da solo.', size=17, pad=(0, 0, 28, 0)),
                      K.headline(t, 'L’acqua', size=36, tag='h2', pad_bottom=10),
                      K.text(t, 'Bevi. Un bicchiere d’acqua la mattina gonfia le vene e il prelievo è più facile. Il tè e il '
                                'caffè no, anche senza zucchero.', size=17, pad=(0, 0, 28, 0)),
                      K.headline(t, 'Le medicine', size=36, tag='h2', pad_bottom=10),
                      K.text(t, 'Prendile come sempre, salvo la tiroide: la pastiglia la mattina del prelievo aspetta fino a '
                                'dopo. Se prendi anticoagulanti, dillo all’infermiera.', size=17, pad=(0, 0, 28, 0)),
                      K.headline(t, 'Le urine', size=36, tag='h2', pad_bottom=10),
                      K.text(t, 'Le prime del mattino, nel contenitore sterile che trovi in farmacia o da noi. Si porta '
                                'entro due ore.', size=17)], wm='2-3', ws='1-1')],
              layout='custom', custom='30,70', gap=64, valign='start'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.domande(t, 'Le altre domande', ['Bambini, gravidanza,', 'aghi e paure.'], [
        ('Fate i prelievi ai bambini?', 'Sì, dai sei mesi. C’è la sala coi pesci e un’infermiera che lo fa solo ai bambini, '
                                        'il martedì e il giovedì.'),
        ('Sono incinta: cambia qualcosa?', 'No, il prelievo è lo stesso. Dillo all’accettazione: alcuni valori si leggono '
                                           'in un altro modo.'),
        ('Ho paura degli aghi.', 'Dillo subito: ti facciamo stendere, usiamo l’ago più sottile e parliamo d’altro. Funziona.')],
        'vtp'))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Senza appuntamento', 'la mattina, o quando vuoi tu.'], 'Il prelievo si fa senza '
                   'prenotare dalle 7 alle 10. Gli esami speciali (il respiro, le curve) vanno prenotati.',
                   bg=K.video_bg(V_OCCHIO, overlay=18), size=100)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('date', 'Il giorno', 'giorno', True),
             K.campo('select', 'Gli esami', 'esami', True, opzioni='Il controllo di base\nIl controllo completo\nGli sportivi\n'
                     'Un esame speciale\nNon lo so, ho l’impegnativa'),
             K.campo('textarea', 'Qualcosa da dirci', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Ti confermiamo', 'il giorno e l’ora.'],
                                'Ti scriviamo entro due ore, anche il sabato mattina.', VOCI, campi, 'Vetrino — prenotazione',
                                'Prenota', 'Grazie! Ti confermiamo entro due ore.'))
    out.append(t.section([t.one([X.orari(K, t, [('Prelievi', '7 — 10', 'Lunedì — sabato, senza appuntamento'),
                                                ('Referti', '11 — 18', 'Sul telefono, o allo sportello'),
                                                ('Esami speciali', 'su appuntamento', 'Martedì e giovedì')])])],
                         padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 60}))
    out.append(X.zone(K, t, 'Dove siamo', ['Borgo delle Colonne,', 'in Oltretorrente.'], 'Il portone bianco al numero 26, '
                      'accanto alla farmacia. Con la carrozzina si entra dal cortile.', [('Bus', 'linee 1 e 8, fermata '
                      'Ospedale vecchio'), ('Bici', 'rastrelliera nel cortile'), ('Auto', 'parcheggio Toschi, 5 minuti'),
                      ('Telefono', PHONE)], LAT, LNG, 'Vetrino · Borgo delle Colonne 26', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · campione non trovato', color=C['acc2'], align='center'),
                       K.headline(t, 'Abbiamo ingrandito <em>troppo.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=C['acc2']),
                       K.text(t, 'A questo ingrandimento la pagina non si vede più. Torna alla home e rimettiamo a fuoco.',
                              size=17, align='center', color=mix(C['chiaro'], 88)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_CELLULE2, overlay=30), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Lo zoom che non finisce',
    ['salute', 'laboratorio', 'analisi', 'parma', 'chiaro', 'slider pro', 'zoom', 'scorrimento'],
    'Un laboratorio di analisi a Parma: nell’apertura lo scorrimento entra nel volto di una persona, poi nella pelle, nelle '
    'cellule, nella molecola e nel DNA, una lente dentro l’altra, e alla fine dietro c’è il laboratorio. Poi il referto che si '
    'scrive da solo, il referto di prima e di adesso, una mattina dal prelievo al referto, il listino e la guida al prelievo.',
    [('Gli esami', ESAMI), ('Prepararsi', PREPARARSI), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('gli-esami', 'Gli esami', 'gli-esami'), ('prepararsi', 'Prepararsi', 'prepararsi'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Inter Tight\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Sora\', system-ui, sans-serif',
    'ui-monospace, Menlo, monospace', ['Inter Tight', 'Sora'], corsivo_di=[],
    peso_titoli='700', raggio=10, grana=2, titoli_line='1.02', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'oculare'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = HEX['secondary']
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'senza'}


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'gli-esami.json': build_esami(), 'prepararsi.json': build_prepararsi(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
