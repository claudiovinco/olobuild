# -*- coding: utf-8 -*-
"""Genera il tema «Pianta Libera» (id pianta-libera): una residenza nuova in costruzione a Milano, venduta sulla carta:
ventiquattro appartamenti dal bilocale all’attico, le finiture da scegliere, il cantiere da seguire, l’ufficio vendite.

Tema chiaro, il cantiere ordinato: cemento chiaro, nero grafite, arancio cantiere e un verde salvia. Titoli in Space
Grotesk, testo in Manrope, dati in JetBrains Mono.

Apertura: «la pianta si alza». Lo slider resta fermo in cima mentre si scorre: sul video della facciata coi balconi, a
destra, la pianta di un trilocale stesa a terra in prospettiva, come un foglio sul tavolo. Scorrendo il foglio si alza
(tutte le stanze ruotano insieme attorno al bordo in basso, come un piano solo) finché la pianta guarda chi legge; poi
le stanze si riempiono di verde una alla volta, compaiono la quota della facciata e la scritta «Disponibile · 3° piano».
Cursore: la quota.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'pianta-libera'
NAME = 'Pianta Libera'

HEX = {
    'primary': '#B23F19', 'primary_contrast': '#FFFFFF', 'secondary': '#F0B48A', 'secondary_contrast': '#191A18',
    'accent': '#8FA89A', 'dark': '#191A18', 'light': '#FFFFFF', 'muted': '#ECEAE4', 'muted_contrast': '#191A18',
    'text': '#191A18', 'text_muted': '#5A5C57', 'background': '#F7F6F2', 'border': '#D9D6CD', 'link': '#B23F19',
}

HOME, APP, CANTIERE, VENDITE = '{{PAGINA:home}}', '{{PAGINA:appartamenti}}', '{{PAGINA:cantiere}}', '{{PAGINA:ufficio-vendite}}'
MAIL, PHONE, PHONE_TEL = 'vendite@piantalibera.example', '+39 02 0000 3390', 'tel:+390200003390'
LAT, LNG = '45.4990', '9.2210'

V_FACCIATA, V_DISEGNO, V_SQUADRA, V_SALONE, V_BALCONE, V_LUCE, V_GRU, V_FACCIATA2 = (
    'pexels-video-34941080', 'pexels-video-6614807', 'pexels-video-8725793', 'pexels-video-7614529', 'pexels-video-40348394',
    'pexels-video-8471040', 'pexels-video-32636373', 'pexels-video-6221101')
PHOTOS = [
    'pexels-37224965.jpg', 'pexels-5674684.jpg', 'pexels-29174530.jpg', 'pexels-12029115.jpg', 'pexels-7167073.jpg',
    'pexels-36777534.jpg', 'pexels-6782362.jpg', 'pexels-6489096.jpg', 'pexels-8089170.jpg', 'pexels-19966809.jpg',
    'pexels-6523300.jpg', 'pexels-6969818.jpg', 'pexels-6508353.jpg', 'pexels-7031840.jpg', 'pexels-27626183.jpg',
    'pexels-7005476.jpg', 'pexels-7005268.jpg', 'pexels-7749046.jpg', 'pexels-6817188.jpg', 'pexels-8089074.jpg',
    'pexels-9584741.jpg', 'pexels-14872287.jpg', 'pexels-8142972.jpg', 'pexels-4458196.jpg', 'pexels-4458193.jpg',
    'pexels-4792483.jpg', 'pexels-4458205.jpg', 'pexels-30547510.jpg', 'pexels-14537425.jpg', 'pexels-5505131.jpg',
    'pexels-4977410.jpg', 'pexels-4249690.jpg', 'pexels-4249583.jpg', 'pexels-4249590.jpg', 'pexels-7490893.jpg',
    'pexels-8469999.jpg', 'pexels-8278913.jpg', 'pexels-5292233.jpg',
]
VIDEOS = [V_FACCIATA, V_DISEGNO, V_SQUADRA, V_SALONE, V_BALCONE, V_LUCE, V_GRU, V_FACCIATA2]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='600', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='var(--olo-color-secondary)')
C = K.c
PESCA, SALVIA = C['sec'], C['acc2']
MONO = 'JetBrains Mono'
VOCI = [('map-pin', 'Via Rubattino 12 · Milano', VENDITE + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Pianta Libera', logo_size='20', overlay=False, scuro=False)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Pianta Libera', 'Ventiquattro appartamenti in classe A a Lambrate, Milano. Consegna prevista a giugno '
                   '2027. Si scelgono la pianta, le finiture e il piano: i muri li alziamo noi.',
                   [('Gli appartamenti', APP), ('Il cantiere', CANTIERE), ('Ufficio vendite', VENDITE),
                    ('Privacy', '/privacy-policy/')],
                   'Ufficio vendite', 'Martedì — sabato 10 — 19<br>Su appuntamento anche la domenica', VOCI,
                   '© 2026 Pianta Libera · Milano · Le immagini sono indicative', [('instagram', 'https://instagram.com')],
                   marchio_size=120, fondo=C['sala'])


# ── L'apertura: la pianta si alza ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
PIANTA = (53.0, 18.0, 39.0, 66.0)        # x, y, larghezza, altezza della pianta sul computer (% della tela)
PIANTAM = (8.0, 42.0, 84.0, 38.0)        # e sul telefono
STANZE = [  # nome, m², x, y, larghezza, altezza (frazioni della pianta)
    ('Soggiorno', '32 m²', 0.0, 0.0, 0.58, 0.55), ('Cucina', '12 m²', 0.58, 0.0, 0.42, 0.30),
    ('Camera', '16 m²', 0.58, 0.30, 0.42, 0.38), ('Bagno', '7 m²', 0.58, 0.68, 0.42, 0.32),
    ('Camera', '11 m²', 0.0, 0.55, 0.34, 0.45), ('Ingresso', '6 m²', 0.34, 0.55, 0.24, 0.45),
]
ALZA = 48   # quando la pianta è dritta, nel tratto dello scorrimento


def _in_pianta(fx, fy, fw, fh, mob=False):
    px, py, pw, ph = PIANTAM if mob else PIANTA
    return px + fx * pw, py + fy * ph, fw * pw, fh * ph


def _piano(lid, fx, fy, fw, fh, extra_tappe=None, **kw):
    """Un pezzo della pianta: ruota in rotateX attorno al bordo in basso della pianta (lo stesso asse per tutti, scritto
    nel transform-origin in % della scatola del pezzo: uguale sui due formati, perché le proporzioni lo sono)."""
    x, y, w, h = _in_pianta(fx, fy, fw, fh)
    xm, ym, wm, hm = _in_pianta(fx, fy, fw, fh, True)
    oy = (1.0 - fy) / fh * 100 if fh else 100
    tappe = [{'p': 0, 'rx': 74}, {'p': 6, 'rx': 74}, {'p': ALZA, 'rx': 0.01}, {'p': 100, 'rx': 0.01}]
    resp = kw.pop('responsive', {})
    resp.setdefault('mobile', {}).update({'x': round(xm, 2), 'y': round(ym, 2), 'width': round(wm, 2), 'height': round(hm, 2)})
    return K.livello(lid, x=round(x, 2), y=round(y, 2), w=round(w, 2), h=round(h, 2), entra='fadeIn',
                     ritardo=kw.pop('ritardo', 300), durata=kw.pop('durata', 900),
                     scrollMove={'inizio': 0, 'fine': 100, 'tappe': tappe}, customCSS='transform-origin:50%% %.1f%%;' % oy,
                     responsive=resp, **kw)


def apertura(t):
    m = K.mob
    livelli = [_piano('pl-foglio', -0.04, -0.14, 1.08, 1.19, tipo='shape', bgColor=mix(C['chiaro'], 94), borderRadius=4,
                      boxShadow={'x': 0, 'y': 18, 'blur': 50, 'spread': -14, 'color': mix(C['nero'], 50)})]
    for k, (nome, mq, fx, fy, fw, fh) in enumerate(STANZE):
        livelli.append(_piano('pl-s%d' % k, fx, fy, fw, fh, tipo='shape', bgColor='transparent', borderWidth=4,
                              borderColor=C['nero'], ritardo=400 + k * 80))
        # il verde che riempie la stanza, dopo che la pianta si è alzata
        riempi = _piano('pl-v%d' % k, fx + 0.008, fy + 0.008, fw - 0.016, fh - 0.016, tipo='shape', bgColor=mix(SALVIA, 55),
                        ritardo=0, durata=300)
        a = ALZA + 6 + k * 5
        riempi['scrollMove']['tappe'] = [{'p': 0, 'rx': 74, 'o': 0}, {'p': 6, 'rx': 74, 'o': 0}, {'p': ALZA, 'rx': 0.01, 'o': 0},
                                         {'p': a, 'rx': 0.01, 'o': 0}, {'p': a + 6, 'rx': 0.01, 'o': 100},
                                         {'p': 100, 'rx': 0.01, 'o': 100}]
        livelli.append(riempi)
        livelli.append(_piano('pl-n%d' % k, fx + 0.04, fy + fh / 2 - 0.07, fw - 0.06, 0.14,
                              contenuto='%s<br><span style="opacity:.7">%s</span>' % (nome.upper(), mq), size=13, peso='600',
                              colore=C['nero'], lineHeight=1.35, letterSpacing=1.5, fontFamily=MONO,
                              responsive={'mobile': {'fontSize': m(10)}}))
    # la porta d'ingresso (un varco nel muro) e la finestra grande del soggiorno
    livelli += [
        _piano('pl-porta', 0.40, 0.985, 0.10, 0.03, tipo='shape', bgColor=mix(C['chiaro'], 94)),
        _piano('pl-finestra', 0.10, -0.012, 0.36, 0.026, tipo='shape', bgColor=SALVIA),
        _piano('pl-quota', 0.0, -0.105, 1.0, 0.06, contenuto='←  11,40 m  →', size=13, peso='600', colore=C['nero'],
               align='center', fontFamily=MONO, letterSpacing=2, responsive={'mobile': {'fontSize': m(10)}}),
    ]
    fine = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 24}, {'p': 84, 'o': 0, 'y': 24},
                                                {'p': 94, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    livelli += [
        K.livello('pl-disp', contenuto='Disponibile · 3° piano · 92 m² · 486.000 €', x=53, y=88, w=40, size=14, peso='700',
                  colore=C['chiaro'], bgColor=C['acc'], padding=10, letterSpacing=1, fontFamily=MONO, align='center',
                  entra='fadeIn', ritardo=0, durata=300, scrollMove=fine,
                  responsive={'mobile': {'fontSize': m(11), 'x': 8, 'y': 82.5, 'width': 84}}),
    ]
    testi = [
        K.livello('pl-o', contenuto='Pianta Libera · residenze a Lambrate · Milano', x=6, y=22, w=44, size=12, peso='600',
                  colore=mix(C['chiaro'], 90), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('pl-t1', contenuto='Scegliete la pianta,', tag='h1', x=6, y=28, w=46, size=76, peso='600',
                  colore=C['chiaro'], lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(28), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('pl-t2', fontFamily='Space Grotesk', contenuto='noi alziamo i muri.', tag='p', x=6, y=38, w=46, size=76, peso='600', colore=PESCA,
                  lineHeight=1.0, letterSpacing=-3, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(28), 'y': 14, 'x': 7, 'width': 90}}),
        K.livello('pl-s', contenuto='Ventiquattro appartamenti in classe A, dal bilocale all’attico, con le finiture da '
                                    'scegliere. Consegna a giugno 2027.', x=6, y=50, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 20, 'x': 7, 'width': 86}}),
        K.livello('pl-b', tipo='button', contenuto='Gli appartamenti', x=6, y=65, w='auto', size=13, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=2, letterSpacing=2, textTransform='uppercase',
                  entra='fadeInUp', ritardo=1000, buttonUrl=APP, hover={'bgColor': C['nero'], 'duration': 250},
                  responsive={'mobile': {'y': 27, 'x': 7, 'fontSize': m(11)}}),
        K.livello('pl-l', tipo='button', contenuto='Il cantiere', x=22.5, y=65, w='auto', size=13, peso='700',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=2, borderWidth=1,
                  borderColor=mix(C['chiaro'], 70), letterSpacing=2, textTransform='uppercase', entra='fadeInUp', ritardo=1100,
                  buttonUrl=CANTIERE, hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 28}, 'mobile': {'y': 27, 'x': 56, 'fontSize': m(11)}}),
        K.livello('pl-hint', contenuto='Scorrete: la pianta si alza ↓', x=6, y=82, w=30, size=12, peso='600',
                  colore=mix(C['chiaro'], 84), letterSpacing=1, fontFamily=MONO, ritardo=1300,
                  scrollMove={'inizio': 0, 'fine': 20, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 33, 'x': 7, 'width': 86}}),
    ]
    veli = K.veli('pl-velo', 'sinistra', 36, 64, colore=C['nero'], forza=86, mobile_alto=35)
    sl = [K.diapositiva('pl-1', K.sfondo_video(V_FACCIATA, overlay=0.22, colore=C['nero']), veli + livelli + testi,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1800, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


TIPI = [  # nome, sotto, descrizione, foto
    ('Bilocale', 'Da 58 m² · da 318.000 €', 'Soggiorno con cucina a vista, una camera, il balcone sul cortile. Otto '
     'appartamenti, dal primo al quarto piano.', 'pexels-7167073.jpg'),
    ('Trilocale', 'Da 86 m² · da 452.000 €', 'Due camere, due bagni, la loggia profonda dove mangiare d’estate. Dieci '
     'appartamenti.', 'pexels-36777534.jpg'),
    ('Quadrilocale', 'Da 118 m² · da 640.000 €', 'Tre camere e lo studio, la cucina abitabile, due affacci. Quattro '
     'appartamenti.', 'pexels-6489096.jpg'),
    ('Attico', '164 m² · 1.180.000 €', 'All’ultimo piano, con la terrazza di 90 m² sui tetti di Lambrate. Due soli, uno '
     'già venduto.', 'pexels-9584741.jpg'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Pianta Libera · Lambrate', ['Ventiquattro case,', 'nessuna uguale.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Comprando adesso, sulla carta, scegliete voi: si possono spostare i tramezzi, unire due '
                                'camere, cambiare i pavimenti e i bagni dal capitolato. L’architetto vi segue in tre '
                                'incontri, fino all’ultima piastrella.', size=17, color=C['testo']),
                      K.button(t, 'Gli appartamenti', APP, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([P.schede_icone(K, t, [('A4', 'classe energetica', '24', ' case', 'Dal bilocale all’attico, su sei piani.',
                                      'building-2'),
                                     ('06', 'giugno', '2027', '', 'La consegna prevista delle chiavi.', 'key-round'),
                                     ('03', 'incontri', '3', ' con l’architetto', 'Per le finiture e i tramezzi.', 'ruler'),
                                     ('90', 'm² di giardino', '900', ' m²', 'Il giardino comune, con gli alberi già piantati.',
                                      'trees')], fondo=C['sala'])], style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli appartamenti', ['Quattro tagli,', 'a partire da qui.'], size=56,
                         tagline='Passate su un taglio: si affaccia la stanza più bella.')]),
        t.one([P.elenco_hover(K, t, TIPI, APP, size=52)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_SALONE, 'La luce', 'Finestre <em>dal pavimento al soffitto.</em>',
                             'Ogni soggiorno ha la sua vetrata, verso sud o verso il giardino.', overlay=26))
    out.append(t.section([
        t.row([t.col([P.citazione(K, t, 'Abbiamo disegnato prima le finestre e poi le stanze: la luce decide dove si sta '
                                          'bene, il resto si adatta.', 'Chiara Valsecchi, architetta del progetto', size='40')],
                     wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-4458205.jpg', 'Le tavole del progetto sul tavolo', ratio='4/5')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le finiture', ['Si scelgono', 'in showroom.'], size=56)]),
        P.fila_foto(K, t, [('pexels-4977410.jpg', 'I campioni dei materiali sul tavolo'),
                           ('pexels-4249583.jpg', 'Le piastrelle e i legni'), ('pexels-7490893.jpg', 'I campioni di legno'),
                           ('pexels-4249690.jpg', 'Le tinte delle pareti')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_GRU, 'Il cantiere', 'Al quarto piano <em>su sei.</em>',
                          'Le gru lavorano, il tetto arriva in primavera: seguite il cantiere mese per mese.', 'Il cantiere',
                          CANTIERE, overlay=22, video=True))
    return out


def build_appartamenti():
    t = Tpl(ID, 'appartamenti')
    out = [K.intro(t, 'Gli appartamenti', ['Ventiquattro,', 'piano per piano.'],
                   'I prezzi comprendono la cantina; il box auto è a parte. Si versa il 20% al compromesso, il resto a '
                   'stati di avanzamento, garantito da fideiussione.', bg=K.photo_bg('pexels-29174530.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Ancora liberi', ['Gli appartamenti', 'disponibili oggi.'], size=56)]),
        t.one([P.righe(K, t, [('1° piano', 'Bilocale B1', '58 m² · balcone sul cortile', '318.000 €'),
                              ('2° piano', 'Trilocale A2', '86 m² · loggia a sud', '452.000 €'),
                              ('3° piano', 'Trilocale C3', '92 m² · due bagni', '486.000 €'),
                              ('4° piano', 'Quadrilocale D4', '118 m² · doppio affaccio', '640.000 €'),
                              ('6° piano', 'Attico E6', '164 m² · terrazza 90 m²', '1.180.000 €')], VENDITE, titolo=32)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dentro', ['Come saranno,', 'stanza per stanza.'], size=56,
                         tagline='Immagini indicative: le finiture le scegliete voi.')]),
        P.carte(K, t, [
            ('pexels-6782362.jpg', 'Il soggiorno', 'Vetrate a tutta altezza', 'Parquet di rovere in tre tinte, '
             'riscaldamento a pavimento, predisposizione per il climatizzatore.'),
            ('pexels-6969818.jpg', 'La cucina', 'A vista o chiusa', 'Si decide al primo incontro: il tramezzo si sposta '
             'senza costi fino a gennaio.'),
            ('pexels-7005476.jpg', 'Il bagno', 'Gres o resina', 'Doccia a filo pavimento, sanitari sospesi, il mobile su '
             'misura.'),
            ('pexels-7749046.jpg', 'La camera', 'Silenziosa', 'Vetri tripli verso la strada, armadio a muro compreso.'),
        ]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_BALCONE, 'Il balcone', 'Un’altra stanza, <em>all’aperto.</em>',
                             'Logge profonde due metri: ci sta il tavolo per sei.', overlay=26))
    return out


def build_cantiere():
    t = Tpl(ID, 'cantiere')
    out = [K.intro(t, 'Il cantiere', ['Mese per mese,', 'fino alle chiavi.'],
                   'Lavoriamo con un’impresa milanese che costruisce dal 1962. Qui le tappe, e una volta al mese la visita '
                   'in cantiere con il casco, per chi ha comprato.', bg=K.video_bg(V_GRU), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le tappe', ['Dagli scavi', 'al tetto.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'marzo 2025', 'pexels-30547510.jpg', 'Fatto', 'Gli scavi', 'e le fondazioni',
             'Il terreno bonificato, le fondazioni e i due piani interrati dei box.', '100', '% fatto'),
            ('2', 'oggi', 'pexels-14537425.jpg', 'In corso', 'La struttura', 'al quarto piano',
             'I solai salgono di un piano al mese; il tetto arriva in primavera.', '62', '% fatto'),
            ('3', 'giugno 2027', 'pexels-12029115.jpg', 'Da fare', 'Le finiture', 'e le chiavi',
             'Gli impianti, i pavimenti scelti da voi, il giardino. Poi il rogito.', '0', '% fatto')], icona='hard-hat')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il progetto', ['Le tavole,', 'prima dei muri.'], size=56)]),
        P.fila_foto(K, t, [('pexels-4458196.jpg', 'Una tavola di progetto'), ('pexels-4458193.jpg', 'La pianta di un piano'),
                           ('pexels-4792483.jpg', 'Il disegno sul tavolo con la squadra')], ratio='4/3'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_DISEGNO, 'Il disegno', 'Ogni linea <em>è una parete.</em>',
                             'Le piante si correggono fino a gennaio: dopo, si costruisce.', overlay=26))
    return out


def build_vendite():
    t = Tpl(ID, 'ufficio-vendite')
    out = [K.intro(t, 'Ufficio vendite', ['Le piante sul tavolo,', 'il caffè pronto.'],
                   'L’ufficio vendite è davanti al cantiere, in via Rubattino. Ci sono le piante di tutti i piani, i '
                   'campioni delle finiture e chi può rispondere a ogni domanda.', bg=K.photo_bg('pexels-37224965.jpg'),
                   size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Chi vi riceve', ['Due persone,', 'tutte le risposte.'], size=56)]),
        K.squadra(t, [
            ('pexels-8469999.jpg', 'Chiara Valsecchi', 'Architetta del progetto', 'Le piante, i tramezzi, le finiture: i tre '
             'incontri li fa lei.', 'center 25%'),
            ('pexels-8278913.jpg', 'Elisa Bonfanti', 'Responsabile vendite', 'I prezzi, i pagamenti, il mutuo e il rogito.',
             'center 25%'),
        ], ratio='4/5', size='360', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Vi interessa', 'taglio', opzioni='Bilocale\nTrilocale\nQuadrilocale\nAttico\nNon so ancora'),
             K.campo('date', 'Il giorno della visita', 'giorno'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'visita', 'Una visita', ['Venite a vedere', 'le piante.'],
                                'Vi confermiamo l’appuntamento in giornata.', VOCI, campi, 'Pianta Libera — visita',
                                'Fissa la visita', 'Ricevuto. Vi confermiamo l’appuntamento in giornata.'))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'di chi compra sulla carta.'], [
        ('Cosa succede se il cantiere si ferma?', 'Ogni versamento è garantito da una fideiussione bancaria, come vuole la '
                                                  'legge.'),
        ('Si possono cambiare le stanze?', 'Sì, i tramezzi interni si spostano fino a gennaio 2026, senza costi.'),
        ('Il box è compreso?', 'No, i box si comprano a parte, da 38.000 €. La cantina è compresa.'),
        ('Si può avere il mutuo?', 'Lavoriamo con tre banche: vi aiutiamo a confrontare le offerte.'),
    ], 'pl-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Davanti al cantiere,', 'in via Rubattino.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'martedì — sabato 10 — 19'), ('In treno', 'stazione di Lambrate, 8 minuti'),
                                  ('In auto', 'parcheggio davanti all’ufficio')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Pianta Libera · ufficio vendite', scuro=False, hex_marker=HEX['primary'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · stanza non in pianta', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa stanza <em>non è in pianta.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'L’abbiamo tolta in progetto. Torniamo all’ingresso.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_FACCIATA2, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Immobiliare', 'La pianta che si alza',
    ['immobiliare', 'nuova costruzione', 'appartamenti', 'vendita sulla carta', 'milano', 'chiaro', 'slider pro', '3d'],
    'Una residenza nuova venduta sulla carta, cemento e arancio cantiere: in apertura la pianta di un trilocale stesa in '
    'prospettiva sul video della facciata, che scorrendo si alza in 3D, si riempie stanza per stanza e mostra prezzo e '
    'piano; i tagli, le finiture, le tappe del cantiere e l’ufficio vendite.',
    [('Gli appartamenti', APP), ('Il cantiere', CANTIERE), ('Ufficio vendite', VENDITE)],
    [('home', 'Home', None), ('appartamenti', 'Gli appartamenti', 'appartamenti'), ('cantiere', 'Il cantiere', 'cantiere'),
     ('ufficio-vendite', 'Ufficio vendite', 'ufficio-vendite')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Space Grotesk\', system-ui, sans-serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Manrope', 'Space Grotesk', 'JetBrains Mono'], corsivo_di=[],
    peso_titoli='600', raggio=2, grana=0, titoli_line='1.0', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'quota'
THEME['cursor']['ring_color'] = HEX['dark']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'appartamenti.json': build_appartamenti(), 'cantiere.json': build_cantiere(),
                        'ufficio-vendite.json': build_vendite(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
