# -*- coding: utf-8 -*-
"""Genera il tema «Affisso» (id affisso): un'agenzia di affissioni e pubblicità esterna a Napoli, dal 1987. Cartelloni
6 × 3, poster, pensiline, schermi; si prenotano le uscite di quattordici giorni.

La trovata (20 nov 2026; utente: «altri 5 temi, tutti strani, per agenzie di comunicazione… stupisci tutti»): il sito è
un cartellone a prismi. Nell'apertura, sopra Napoli di notte, il cartellone ha otto prismi che girano uno dopo l'altro
mentre scorri e mostrano tre manifesti (livelli del Pro Slider con la rotazione 3D nel movimento a scorrimento);
poi i formati sono sezioni col passaggio «Prismi» (Sezioni → Effetto sticky → «Prismi»): la sezione si divide in
strisce che girano e scoprono la successiva. Cursore: il pennello della colla.

Tile poco usate: Cartellone (le uscite del 2027, coi formati come generi), News Ticker (in strada adesso), Gratta e
Scopri (il manifesto vecchio da grattare), Calcolatore a slider (quanto costa un'uscita, con la lista scalata),
Scorrimento orizzontale (le campagne), Confronto immagini, Tabella.
Tema scuro: nero, carta, giallo segnale. Titoli in Archivo Black, testo in Schibsted Grotesk, IBM Plex Mono.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, radius  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import agenzie as A  # noqa: E402

ID = 'affisso'
NAME = 'Affisso'
CATEGORIA = 'Creativi e portfolio'

HEX = {
    'primary': '#FFD21F', 'primary_contrast': '#111111', 'secondary': '#E5322D', 'secondary_contrast': '#FFFFFF',
    'accent': '#FF6A3D', 'dark': '#0B0B0B', 'light': '#FFFFFF', 'muted': '#1B1B1A', 'muted_contrast': '#F2F0EA',
    'text': '#F2F0EA', 'text_muted': '#ADA99F', 'background': '#121211', 'border': '#2E2D2A', 'link': '#FFD21F',
}
HOME, FORMATI, CAMPAGNE, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:i-formati}}', '{{PAGINA:campagne}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'uscite@affisso.example', '081 000 1987', 'tel:+390810001987'
LAT, LNG = '40.8433', '14.2525'

V_NOTTE, V_GOLFO, V_ATTACCA, V_SCHERMO, V_LED, V_COLONNA, V_PLOTTER, V_SCOOTER, V_STRADA, V_ROSA = (
    'pexels-video-%d' % n for n in (19896387, 19896379, 7149362, 3360184, 5086601, 4550481, 36570935, 38612345, 855737,
                                     41031221))
VIDEOS = [V_NOTTE, V_GOLFO, V_ATTACCA, V_SCHERMO, V_LED, V_COLONNA, V_PLOTTER, V_SCOOTER, V_STRADA, V_ROSA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(MURO_VOLTI, MURO_GRAFFITI, MURO_FOGLI, GIALLI, LETTORE, MURO_BN, CARTELLONE_NOTTE, SCHERMI_FOLLA, VIA_1, VIA_2, VIA_3, VIA_4,
 VIA_5, VIA_6, VICOLO_1, VICOLO_2, VICOLO_3, VICOLO_4, VICOLO_5, PENSILINA, PENSILINA_NOTTE, PENSILINA_LUCI, STRAPPATI,
 MURO_COLORI, BACHECA, PALO, GIALLO_STRAPPATO, ATTACCHINI, SCALE_MANIFESTI, PARETE, PLOTTER, STAMPA_1, STAMPA_2, STAMPA_3,
 MACCHINA, MURALE, RIUNIONE, TAVOLO, RIUNIONE_ALTO, GOLFO, TRAMONTO, GOLFO_2, GOLFO_3, LUNGOMARE, CARTELLONE_BIANCO,
 PENSILINA_BIANCA, PENSILINA_BIANCA_2) = f(
    7149333, 35875430, 33289718, 10409887, 12602990, 15821804, 12811578, 18335917, 10045295, 12557762, 19082539, 36388787,
    10045289, 34963363, 38152123, 28990296, 10045286, 33289726, 37887442, 7252582, 1661496, 10539793, 31084610, 29501720,
    3964576, 20199527, 40701394, 7149338, 35877406, 12208210, 40241484, 716272, 40242209, 9313701, 41088462, 4397203,
    8117415, 6224, 7688336, 17311125, 39591616, 38206479, 12496249, 37933714, 13122335, 40952519, 40952510)
PHOTOS = [MURO_VOLTI, MURO_GRAFFITI, MURO_FOGLI, GIALLI, LETTORE, MURO_BN, CARTELLONE_NOTTE, SCHERMI_FOLLA, VIA_1, VIA_2, VIA_3,
          VIA_4, VIA_5, VIA_6, VICOLO_1, VICOLO_2, VICOLO_3, VICOLO_4, VICOLO_5, PENSILINA, PENSILINA_NOTTE, PENSILINA_LUCI,
          STRAPPATI, MURO_COLORI, BACHECA, PALO, GIALLO_STRAPPATO, ATTACCHINI, SCALE_MANIFESTI, PARETE, PLOTTER, STAMPA_1,
          STAMPA_2, STAMPA_3, MACCHINA, MURALE, RIUNIONE, TAVOLO, RIUNIONE_ALTO, GOLFO, TRAMONTO, GOLFO_2, GOLFO_3, LUNGOMARE,
          CARTELLONE_BIANCO, PENSILINA_BIANCA, PENSILINA_BIANCA_2]
FACCE = ['affisso-faccia-%s-%d.jpg' % (k, s) for k in 'abc' for s in range(8)] + ['affisso-faccia-%s.jpg' % k for k in 'abc']
DISEGNI = ['affisso-cartellone.svg', 'affisso-cartellone-t.svg'] + FACCE
LOGHI = ['affisso-logo-chiaro.png', 'affisso-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=False,
        accento_su_media='#FFD21F')
C = K.c
GIALLO, NERO = '#FFD21F', '#111111'
MONO = 'var(--olo-font-family-mono)'
TIT = 'var(--olo-font-family-heading)'
VOCI = [('map-pin', 'Via Gianturco 87 · Napoli', PRENOTA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '473/115',
                   'Affisso attacca manifesti a Napoli dal 1987: cartelloni 6 × 3, poster, pensiline, metro e schermi, in '
                   'città e in provincia. Stampiamo, attacchiamo e ti mandiamo la foto di ogni impianto.',
                   [('I formati', FORMATI), ('Campagne', CAMPAGNE), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'Ufficio uscite', 'Dal lunedì al venerdì 8:30 — 18<br>Il sabato mattina su appuntamento', VOCI,
                   '© 2026 Affisso srl · Napoli · P. IVA 00000001987', [('instagram', 'https://instagram.com'),
                                                                      ('linkedin', 'https://linkedin.com')], scuro=True)


# ── L'apertura: il cartellone a prismi ──
# Computer (tela 1440 × 810): il pannello 2:1 a x 47,5 %, y 17 %, largo 46,5 % (669,6 px), alto 41,33 %; otto prismi
# larghi 5,8125 %. La struttura (SVG 703 × 717, pannello a 20, 40 su 662 × 331) è scalata di 1,01148: x 46,10 %, y 12 %,
# 49,38 × 89,5 %. Telefono (tela 640 × 1400): pannello a x 6 %, y 9 %, 88 × 20,11 %; struttura (603 × 468) a x 2,875 %,
# y 6,14 %, 94,22 × 33,43 %. Ogni prisma ha tre facce: A gira via (0 → -90°) mentre entra B (90° → 0), e poi B → C.
PX, PY, PW, PH = 47.5, 17.0, 46.5, 41.33
MX, MY, MW, MH = 6.0, 13.0, 88.0, 20.11
G1, G2, PASSO, MEZZO = 18.0, 52.0, 1.8, 2.6


def faccia(k, s):
    s1, s2 = G1 + s * PASSO, G2 + s * PASSO
    if k == 'a':
        tp = [(0, {'ry': 0, 'o': 100}), (s1, {'ry': 0, 'o': 100}), (s1 + MEZZO, {'ry': -90, 'o': 100}),
              (s1 + MEZZO + 0.01, {'ry': -90, 'o': 0}), (100, {'ry': -90, 'o': 0})]
    elif k == 'b':
        tp = [(0, {'ry': 90, 'o': 0}), (s1 + MEZZO, {'ry': 90, 'o': 0}), (s1 + MEZZO + 0.01, {'ry': 90, 'o': 100}),
              (s1 + 2 * MEZZO, {'ry': 0, 'o': 100}), (s2, {'ry': 0, 'o': 100}), (s2 + MEZZO, {'ry': -90, 'o': 100}),
              (s2 + MEZZO + 0.01, {'ry': -90, 'o': 0}), (100, {'ry': -90, 'o': 0})]
    else:
        tp = [(0, {'ry': 90, 'o': 0}), (s2 + MEZZO, {'ry': 90, 'o': 0}), (s2 + MEZZO + 0.01, {'ry': 90, 'o': 100}),
              (s2 + 2 * MEZZO, {'ry': 0, 'o': 100}), (100, {'ry': 0, 'o': 100})]
    w, mw = PW / 8, MW / 8
    return K.livello('af-%s%d' % (k, s), tipo='image', imageSrc=K.img('affisso-faccia-%s-%d.jpg' % (k, s)),
                     imageAlt='' if s else {'a': 'Manifesto giallo: qui ci sta il tuo nome', 'b': 'Manifesto: ti vedono tutti',
                                            'c': 'Manifesto rosso: Affisso, dal 1987'}[k],
                     objectFit='fill', x=round(PX + s * w + 0.04, 3), y=PY, w=round(w - 0.08, 3), h=PH, entra='fadeIn', ritardo=150 + s * 40,
                     durata=500, scrollMove=A.scorri(tp),
                     responsive={'mobile': {'x': round(MX + s * mw + 0.08, 3), 'y': MY, 'width': round(mw - 0.16, 3), 'height': MH}})


def apertura(t):
    m = K.mob
    L = K.veli('af-velo', 'sinistra', 30, 60, colore=C['nero'], forza=86)
    L += [
        K.livello('af-struttura', tipo='image', imageSrc=K.img('affisso-cartellone.svg'), imageAlt='', objectFit='fill',
                  x=46.10, y=12.0, w=49.38, h=89.5, entra='fadeIn', ritardo=0, durata=700,
                  responsive={'mobile': {'visible': False}}),
        K.livello('af-struttura-t', tipo='image', imageSrc=K.img('affisso-cartellone-t.svg'), imageAlt='', objectFit='fill',
                  x=2.875, y=10.14, w=94.22, h=33.43, visible=False, entra='fadeIn', ritardo=0, durata=700,
                  responsive={'mobile': {'visible': True, 'x': 2.875, 'y': 10.14, 'width': 94.22, 'height': 33.43}}),
        K.livello('af-fondo', tipo='shape', bgColor='#050505', x=PX, y=PY, w=PW, h=PH, entra='fadeIn', ritardo=0, durata=500,
                  responsive={'mobile': {'x': MX, 'y': MY, 'width': MW, 'height': MH}}),
    ]
    L += [faccia(k, s) for k in 'abc' for s in range(8)]
    L += [
        # la luce delle tre lampade sul manifesto
        K.livello('af-luce', tipo='shape', x=PX, y=PY, w=PW, h=PH, entra='fadeIn', ritardo=600, durata=900,
                  shapeGradient={'angle': 180, 'from': 'rgba(255,244,205,0.22)', 'to': 'rgba(255,244,205,0)', 'fromStop': 0},
                  responsive={'mobile': {'x': MX, 'y': MY, 'width': MW, 'height': MH}}),
        K.livello('af-o', contenuto='Affissioni · Napoli · dal 1987', x=5, y=30, w=38, size=13, peso='500',
                  colore=mix(C['chiaro'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=100,
                  responsive={'mobile': {'x': 6, 'y': 50, 'width': 88, 'fontSize': m(11)}}),
        K.livello('af-t1', contenuto='Napoli si legge<br>sui muri.', tag='h1', x=5, y=35, w=40, size=70, peso='400',
                  colore=C['chiaro'], lineHeight=0.98, entra='fadeInUp', ritardo=250, durata=900,
                  scrollMove=A.appare(0, 1, 26, 32), responsive={'mobile': {'x': 6, 'y': 53, 'width': 88, 'fontSize': m(40)}}),
        K.livello('af-t1s', contenuto='Cartelloni 6 × 3, poster, pensiline e schermi in tutta la città. Tu scegli dove, noi '
                  'stampiamo e attacchiamo.', x=5, y=58, w=34, size=18, peso='500', colore=mix(C['chiaro'], 90),
                  lineHeight=1.5, ritardo=500, scrollMove=A.appare(0, 1, 26, 32),
                  responsive={'mobile': {'x': 6, 'y': 65, 'width': 86, 'fontSize': m(16)}}),
        K.livello('af-t2', contenuto='Novantamila auto<br>al giorno.<br><span style="color:#FFD21F">Tutte guardano qui.</span>',
                  x=5, y=35, w=42, size=60, peso='400', colore=C['chiaro'], lineHeight=1.0, entra='fadeIn', ritardo=0, fontFamily=TIT,
                  scrollMove=A.appare(36, 42, 60, 66), responsive={'mobile': {'x': 6, 'y': 53, 'width': 88, 'fontSize': m(36)}}),
        K.livello('af-t3', contenuto='Il prossimo manifesto<br>può essere il tuo.', x=5, y=35, w=42, size=64, peso='400',
                  colore=C['chiaro'], lineHeight=1.0, entra='fadeIn', ritardo=0, fontFamily=TIT, scrollMove=A.appare(72, 78),
                  responsive={'mobile': {'x': 6, 'y': 53, 'width': 88, 'fontSize': m(33)}}),
        K.livello('af-prenota', tipo='button', contenuto='Prenota un’uscita', x=5, y=60, w='auto', size=14, peso='700',
                  colore=NERO, bgColor=GIALLO, padding=18, borderRadius=0, buttonUrl=PRENOTA, entra='fadeIn', ritardo=0,
                  textTransform='uppercase', letterSpacing=2, scrollMove=A.appare(80, 86),
                  hover={'bgColor': C['chiaro'], 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 75, 'width': 'auto', 'fontSize': m(13)}}),
        K.livello('af-formati', tipo='button', contenuto='I formati', x=23.5, y=60, w='auto', size=14, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=0, borderWidth=2, borderColor=C['chiaro'], buttonUrl=FORMATI,
                  entra='fadeIn', ritardo=0, textTransform='uppercase', letterSpacing=2, scrollMove=A.appare(80, 86),
                  hover={'bgColor': mix(C['chiaro'], 16), 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 81, 'width': 'auto', 'fontSize': m(13)}}),
    ]
    sl = [K.diapositiva('af-1', K.sfondo_video(V_NOTTE, overlay=0.22, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=3000, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── i formati (Effetto sticky «Prismi») ──
def formato(t, numero, nome, misura, testo, dati, foto, verso, pos='center center', destra=False):
    blocco = [K.label(t, 'Formato %s di 4 · %s' % (numero, misura), color=GIALLO),
              K.headline(t, nome, size=58, tag='h2', pad_bottom=14, color=C['chiaro'], sizes=(44, 32)),
              K.text(t, testo, size=16, color=mix(C['chiaro'], 88)),
              K.text(t, dati, size=13, color=GIALLO, pad=(10, 0, 0, 0))]
    cols = [t.col(blocco, wm='1-2', ws='1-1', style=A.scheda(mix(C['nero'], 90))), t.col([], wm='1-2', ws='1-1')]
    if destra:
        cols.reverse()
    return t.section([t.row(cols, layout='custom', custom='44,56', valign='end', anim=None)],
                     bg=K.photo_bg(foto, overlay=8, position=pos), padding='large', width='large',
                     sticky_effect='prismi', sticky_h_dir=verso, sticky_h_align='bottom',
                     style={'padding_top': 120, 'padding_bottom': 70})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.label(t, 'Pubblicità esterna dal 1987', color=GIALLO),
                      K.headline(t, 'Un cartellone non si salta, non si silenzia, <em>non si scorre.</em>', size=60, tag='h2',
                                 pad_bottom=0, sizes=(46, 32))], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Da trentotto anni attacchiamo manifesti a Napoli e in provincia: 1.400 impianti fra cartelloni, '
                                'poster, pensiline, metro e schermi. Scegli la zona e le due settimane, noi stampiamo, attacchiamo '
                                'e ti mandiamo la foto di ogni impianto.', size=17),
                      K.button(t, 'I formati', FORMATI, kind='pieno', pad_top=20)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 70}))
    out.append(t.section([t.one([t.classic('newsticker', {
        'items': [{'title': 'Gelateria Fiordilatte · 24 cartelloni 6 × 3 sulla Tangenziale', 'badge': 'In strada', 'icon': 'map-pin',
                   'timestamp': '', 'url': ''},
                  {'title': 'Teatro Nuovo Sole · 300 manifesti 100 × 140 fra Vomero e Arenella', 'badge': 'In strada',
                   'icon': 'map-pin', 'timestamp': '', 'url': ''},
                  {'title': 'Mostra «Il mare dentro» · pensiline di via Caracciolo', 'badge': 'Da lunedì', 'icon': 'calendar',
                   'timestamp': '', 'url': ''},
                  {'title': 'Palestra Vesuvio Fit · schermi di piazza Garibaldi, 15 secondi ogni 3 minuti', 'badge': 'In strada',
                   'icon': 'map-pin', 'timestamp': '', 'url': ''},
                  {'title': 'Ottica Caracciolo · metro linea 1, stazioni Toledo e Dante', 'badge': 'Da lunedì', 'icon': 'calendar',
                   'timestamp': '', 'url': ''}],
        'show_label': True, 'label_text': 'Sui muri adesso', 'label_icon': 'eye', 'label_shape': 'arrow', 'label_bg': GIALLO,
        'label_color': NERO, 'badge_bg': mix(C['chiaro'], 10), 'badge_color': GIALLO, 'bg_color': C['nero'], 'text_color': C['chiaro'],
        'height': 58, 'font_size': 15, 'font_weight': '500', 'animation_type': 'marquee', 'marquee_duration': 46,
        'border_radius': radius(0), 'border': {'top': 1, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid',
                                                'color': C['filo']}}, style={})], anim=None)],
        width='fullbleed', padding='remove-vertical', style={'padding_bottom': 90}))
    out += [
        formato(t, '1', 'Il cartellone 6 × 3', 'diciotto metri quadri', 'Il formato che si vede dall’auto: sulle '
                'tangenziali, sugli assi, all’uscita delle stazioni. Lo stampiamo su carta blueback e lo attacchiamo in otto '
                'fogli, all’alba.', 'Da 290 € a cartellone · 14 giorni · foto di controllo', CARTELLONE_NOTTE, 'destra'),
        formato(t, '2', 'Il poster 100 × 140', 'a misura di marciapiede', 'Si legge a piedi, da vicino. Ne attacchiamo a centinaia '
                'nei quartieri che scegli tu, anche uno per via: per un teatro, una mostra, un’apertura.',
                'Da 2,40 € a manifesto · minimo 50 · 14 giorni', MURO_COLORI, 'sinistra', destra=True),
        formato(t, '3', 'Pensiline e metro', 'chi aspetta legge', 'Alla fermata si aspetta in media sette minuti: il tempo di '
                'leggere tutto il manifesto, anche il numero di telefono. Retroilluminate, di sera si vedono da lontano.',
                'Da 180 € a pensilina · 14 giorni · luce compresa', PENSILINA_LUCI, 'destra'),
        formato(t, '4', 'Gli schermi', 'il manifesto che si muove', 'Quindici secondi ogni tre minuti sugli schermi di piazza '
                'Garibaldi, del Vomero e del centro direzionale. Cambi il video quando vuoi, anche a metà campagna.',
                'Da 420 € a settimana · video da 15 secondi', SCHERMI_FOLLA, 'sinistra', destra=True),
    ]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Il calendario', 'Le uscite <em>del 2027.</em>', 'Le affissioni vanno a quattordicine: due '
                             'settimane che partono sempre di lunedì. Qui vedi quelle ancora libere, formato per formato.',
                             size=56, sizes=(42, 32)), wm='1-3', ws='1-1'),
               t.col([t.classic('cartellone', {
                   'vista': 'lista', 'raggruppa': True, 'ordina': True, 'passati': 'nascondi', 'filtri_generi': True,
                   'filtri_mesi': False, 'ricerca': False, 'immagini': False, 'testo_tutti': 'Tutti i formati',
                   'testo_biglietti': 'Prenota', 'testo_info': '', 'calendario': True, 'testo_calendario': 'Segna in agenda',
                   'conto': False, 'vuoto_testo': 'Il calendario del 2027 arriva a dicembre.',
                   'nessun_risultato': 'Nessuna uscita libera per questo formato.',
                   'generi': [{'id': 'af-g1', 'nome': '6 × 3', 'colore': GIALLO}, {'id': 'af-g2', 'nome': 'Poster', 'colore': '#FF6A3D'},
                              {'id': 'af-g3', 'nome': 'Pensiline', 'colore': '#8FD3FF'}, {'id': 'af-g4', 'nome': 'Schermi',
                                                                                       'colore': '#C9F25B'}],
                   'voci': [
                       {'id': 'af-u1', 'titolo': 'Quattordicina 1', 'sottotitolo': 'Tangenziale e assi mediani · 60 impianti',
                        'genere': '6 × 3', 'data': '2027-01-04', 'data_fine': '2027-01-17', 'ora': '', 'orari': '',
                        'luogo': 'Napoli e provincia', 'durata': '14 giorni', 'prezzo': 'da 290 €', 'stato': 'esaurito',
                        'etichette': '', 'descrizione': '', 'incontro': '', 'immagine': '', 'link_biglietti': PRENOTA + '#scrivi',
                        'link_info': ''},
                       {'id': 'af-u2', 'titolo': 'Quattordicina 2', 'sottotitolo': 'Vomero, Arenella, Chiaia · 800 posti',
                        'genere': 'Poster', 'data': '2027-01-18', 'data_fine': '2027-01-31', 'ora': '', 'orari': '',
                        'luogo': 'Napoli', 'durata': '14 giorni', 'prezzo': 'da 2,40 € a manifesto', 'stato': 'ultimi',
                        'etichette': '', 'descrizione': '', 'incontro': '', 'immagine': '', 'link_biglietti': PRENOTA + '#scrivi',
                        'link_info': ''},
                       {'id': 'af-u3', 'titolo': 'Quattordicina 2', 'sottotitolo': 'Lungomare e via Caracciolo · 40 pensiline',
                        'genere': 'Pensiline', 'data': '2027-01-18', 'data_fine': '2027-01-31', 'ora': '', 'orari': '',
                        'luogo': 'Napoli', 'durata': '14 giorni', 'prezzo': 'da 180 €', 'stato': '', 'etichette': '',
                        'descrizione': '', 'incontro': '', 'immagine': '', 'link_biglietti': PRENOTA + '#scrivi', 'link_info': ''},
                       {'id': 'af-u4', 'titolo': 'Settimane 5 e 6', 'sottotitolo': 'Piazza Garibaldi e centro direzionale',
                        'genere': 'Schermi', 'data': '2027-02-01', 'data_fine': '2027-02-14', 'ora': '', 'orari': '',
                        'luogo': 'Napoli', 'durata': '15 secondi ogni 3 minuti', 'prezzo': 'da 420 € a settimana', 'stato': '',
                        'etichette': '', 'descrizione': '', 'incontro': '', 'immagine': '', 'link_biglietti': PRENOTA + '#scrivi',
                        'link_info': ''},
                       {'id': 'af-u5', 'titolo': 'Quattordicina 4', 'sottotitolo': 'Uscite dell’autostrada A1 e A3 · 45 impianti',
                        'genere': '6 × 3', 'data': '2027-02-15', 'data_fine': '2027-02-28', 'ora': '', 'orari': '',
                        'luogo': 'Provincia', 'durata': '14 giorni', 'prezzo': 'da 260 €', 'stato': '', 'etichette': '',
                        'descrizione': '', 'incontro': '', 'immagine': '', 'link_biglietti': PRENOTA + '#scrivi', 'link_info': ''},
                       {'id': 'af-u6', 'titolo': 'Quattordicina 6', 'sottotitolo': 'Centro storico e università · 600 posti',
                        'genere': 'Poster', 'data': '2027-03-15', 'data_fine': '2027-03-28', 'ora': '', 'orari': '',
                        'luogo': 'Napoli', 'durata': '14 giorni', 'prezzo': 'da 2,40 € a manifesto', 'stato': '', 'etichette': '',
                        'descrizione': '', 'incontro': '', 'immagine': '', 'link_biglietti': PRENOTA + '#scrivi', 'link_info': ''},
                   ],
                   'accento': GIALLO, 'linea_colore': C['filo'], 'raggio': radius(0), 'raggio_pulsanti': radius(0),
                   'giorno_font_family': 'heading', 'giorno_color': C['chiaro'], 'filtri_aspetto': 'pillole', 'mese_color': GIALLO,
                   'titolo_color': C['testo'], 'titolo_italic': False, 'sottotitolo_color': C['tenue'], 'sottotitolo_italic': False,
                   'meta_color': C['tenue'], 'descrizione_color': C['tenue'], 'titolo_font_size': 28}, style={})],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([t.classic('scratchfx', {
            'image': K.img('affisso-faccia-a.jpg'), 'object_fit': 'cover', 'object_position': 'center center',
            'prize_eyebrow': 'Prima uscita con noi', 'prize_title': '−10 %', 'prize_text': 'Su qualunque formato, se è la prima volta '
            'che attacchi con Affisso.', 'coupon_code': 'PRIMOMURO', 'copy_label': 'Copia il codice', 'remember': True,
            'text_color': NERO, 'under_bg': GIALLO, 'hint': 'Gratta via il manifesto vecchio', 'show_button': True,
            'reveal_label': 'Strappa tutto', 'cover_type': 'image', 'cover_image': K.img(STRAPPATI), 'cover_text': '',
            'brush_size': 46, 'reveal_threshold': 55, 'aspect': '2/1', 'height_mode': 'aspect', 'align': 'left', 'max_width': 680,
            'border_radius': radius(0), 'shadow': 'none',
            'border': {'top': 10, 'right': 10, 'bottom': 10, 'left': 10, 'linked': True, 'style': 'solid', 'color': '#2A2A2A'}},
            style={})], wm='3-5', ws='1-1'),
               t.col(A.testa(K, t, 'Prova tu', 'Sotto ogni manifesto vecchio <em>c’è posto.</em>', 'Gratta il muro col dito o col '
                             'mouse: sotto c’è il tuo primo manifesto con noi, e uno sconto per attaccarlo.', size=48,
                             sizes=(38, 30)), wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.label(t, 'Il muro è libero', color=NERO),
                      K.headline(t, 'Dal 18 gennaio ci sono ancora 800 posti al Vomero.', size=72, tag='h2', color=NERO,
                                 pad_bottom=0, sizes=(52, 34))], wm='2-3', ws='1-1'),
               t.col([K.text(t, 'Ci dici cosa vuoi dire e a chi, noi ti diciamo dove attaccarlo. Il preventivo arriva in giornata.',
                             size=17, color=mix(NERO, 82)),
                      t.classic('button', {'text': 'Prenota l’uscita', 'url': PRENOTA, 'alignment': 'left', 'bg_color': NERO,
                                           'text_color': GIALLO, 'hover_bg_color': C['chiaro'], 'hover_text_color': NERO,
                                           'border_radius': 0, 'font_size': 13, 'font_weight': '700',
                                           'text_transform': 'uppercase', 'letter_spacing': '2'}, style={'padding_top': 18})],
                     wm='1-3', ws='1-1')],
              layout='custom', custom='64,36', gap=56, valign='end'),
    ], bg=solid(GIALLO), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_formati():
    t = Tpl(ID, 'i-formati')
    out = [t.section([
        t.row([t.col([K.label(t, 'I formati', color=GIALLO),
                      K.headline(t, 'Quattro misure,<br>1.400 muri.', tag='h1', size=92, color=C['chiaro'],
                                 pad_bottom=18, sizes=(64, 40)),
                      K.text(t, 'Dal manifesto da marciapiede al cartellone da diciotto metri quadri. Prezzi per quattordicina, '
                                'stampa e attacchinaggio compresi.', size=18, color=mix(C['chiaro'], 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_ATTACCA, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 230, 'padding_bottom': 90})]
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Listino 2027', 'Quanto costa <em>stare in strada.</em>', 'Due settimane, prezzi a impianto, IVA '
                             'esclusa. Sopra i cento manifesti il prezzo scende da solo.', size=52, sizes=(40, 30)),
                     wm='1-3', ws='1-1'),
               t.col([t.classic('table', {
                   'table_data': [['Formato', 'Misura', 'Dove', 'Quattordicina'],
                                  ['Cartellone', '6 × 3 m', 'Tangenziale, assi, autostrade', 'da 260 €'],
                                  ['Poster', '100 × 140 cm', 'Quartieri, scuole, università', 'da 2,40 €'],
                                  ['Poster grande', '140 × 200 cm', 'Centro e stazioni', 'da 6 €'],
                                  ['Pensilina', '120 × 176 cm', 'Lungomare, Vomero, Fuorigrotta', 'da 180 €'],
                                  ['Metro', '300 × 200 cm', 'Linea 1 e linea 6', 'da 340 €'],
                                  ['Schermo', '15 secondi', 'Garibaldi, Vomero, Centro direzionale', 'da 420 € a settimana']],
                   'has_header': True, 'striped': True, 'bordered': False, 'hover_effect': True, 'compact': False,
                   'first_col_bold': True, 'col_alignments': ['left', 'left', 'left', 'right'], 'responsive_mode': 'scroll',
                   'header_bg': GIALLO, 'header_text_color': NERO, 'text_color': C['testo'], 'border_color': C['filo'],
                   'even_row_bg': mix(C['chiaro'], 4), 'shadow': 'none'}, style={})], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 90}))
    out.append(t.section([
        t.row([t.col([K.image(t, PLOTTER, 'Il plotter che stampa un manifesto a colori', ratio='4/5', drift=True)], wm='2-5',
                     ws='1-1'),
               t.col([t.classic('projector', {
                   'eyebrow': 'Poster 100 × 140', 'heading': 'Quanti manifesti attacchiamo?',
                   'intro': 'Sposta il cursore: il prezzo comprende stampa, attacchinaggio e la foto di ogni manifesto.',
                   'min': '50', 'max': '1000', 'step': '50', 'value': '200', 'rate': '0', 'years': '2.4', 'currency': '€ ',
                   'input_label': 'Manifesti', 'input_unit': 'manifesti', 'out_caption': 'Per due settimane, IVA esclusa',
                   'result_suffix': '', 'decimals': 0, 'note': 'Sopra i 500 manifesti ti facciamo un prezzo a parte.',
                   'show_contrib': True, 'list_base': 100,
                   'list_items': [{'name': 'Persone che li vedono ogni giorno', 'amount': 18000, 'unit': 'circa'},
                                  {'name': 'Foto di controllo', 'amount': 100, 'unit': 'foto'},
                                  {'name': 'Colla', 'amount': 6, 'unit': 'litri'}],
                   'zone_accent': GIALLO, 'card_bg': C['sala'], 'align': 'left', 'border_radius': 0, 'shadow': 'none',
                   'tile_padding': {'top': 48, 'right': 48, 'bottom': 48, 'left': 48}}, style={})], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'Come lavoriamo', 'Dall’ufficio <em>al muro</em> in cinque giorni.', None, size=52,
                             sizes=(40, 30)), wm='1-3', ws='1-1'),
               t.col([K.text(t, '<p><strong>Lunedì</strong> ci mandi il file o l’idea: se serve, lo impaginiamo noi nella misura giusta.'
                                '</p><p><strong>Martedì</strong> stampiamo su carta blueback, che non lascia vedere il manifesto di sotto.'
                                '</p><p><strong>Da mercoledì a venerdì</strong> prepariamo i giri delle squadre, zona per zona.</p>'
                                '<p><strong>Lunedì all’alba</strong> si attacca. Entro sera hai le foto di ogni impianto, con la via e '
                                'l’ora.</p>', size=17)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_campagne():
    t = Tpl(ID, 'campagne')
    out = [t.section([
        t.row([t.col([K.label(t, 'Campagne', color=GIALLO),
                      K.headline(t, 'Ce le hanno<br>fotografate.', tag='h1', size=92, color=C['chiaro'], pad_bottom=18,
                                 sizes=(64, 40)),
                      K.text(t, 'Alcune delle campagne che abbiamo attaccato quest’anno, con le foto di controllo che mandiamo a '
                                'ogni cliente.', size=18, color=mix(C['chiaro'], 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_SCHERMO, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 230, 'padding_bottom': 90})]
    voci = [
        (MURO_VOLTI, 'Volti del quartiere', 'Associazione Sanità Viva · 120 poster', 'Le facce di chi abita il rione, attaccate dove abitano.'),
        (GIALLI, 'Settimana gialla', 'Libreria dei Banchi · 300 poster', 'Un colore solo per tutta la città: il giallo dei libri in saldo.'),
        (BACHECA, 'Natale al mercato', 'Mercato coperto di Fuorigrotta · 40 pensiline', 'Rosso e verde sotto le luci delle fermate.'),
        (GIALLO_STRAPPATO, 'Il manifesto che resta', 'Festival Mura · 600 poster', 'Quello che rimane dopo tre settimane di pioggia.'),
        (PALO, 'Il tempo cambia', 'Meteo Golfo · 80 pali', 'Bianco e nero, avvolti sui pali della luce del lungomare.'),
        (LETTORE, 'Chi si ferma legge', 'Teatro Nuovo Sole · 200 poster', 'Il programma intero, scritto piccolo, per chi ha tempo.'),
        (SCALE_MANIFESTI, 'Su per le scale', 'Galleria Pignasecca · 60 poster', 'Un manifesto a gradino, fino alla porta della mostra.'),
    ]
    out.append(t.section([t.one([t.classic('scrollscrub', {
        'behavior': 'pin', 'scroll_length': 3, 'align': 'center', 'gap': 28, 'show_progress': True, 'arrows': 'quadrate',
        'arrows_pos': 'sotto', 'arrows_bg': GIALLO, 'arrows_color': NERO, 'indicators': 'numeri',
        'indicators_color': C['tenue'], 'indicators_active_color': GIALLO, 'edge_fade': True, 'drag_scroll': True,
        'swing': True, 'heading': 'Sette campagne del 2026', 'kicker': 'Foto di controllo', 'heading_color': C['testo'],
        'kicker_color': GIALLO, 'heading_size': 48, 'heading_font': 'heading', 'item_width': 380, 'item_min_height': 520,
        'round': 0, 'item_padding': {'top': 18, 'right': 4, 'bottom': 8, 'left': 4}, 'item_bg_default': 'transparent',
        'text_color_default': C['testo'], 'progress_color': GIALLO, 'show_number': True, 'shadow': 'none',
        'items': [{'title': ti, 'subtitle': so, 'text': te, 'media': K.img(fo)} for fo, ti, so, te in voci]}, style={})],
        anim=None)], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 80}))
    out.append(t.section([
        t.row([t.col(A.testa(K, t, 'In stamperia e in strada', 'Prima il foglio, <em>poi il muro.</em>', 'Trascina la linea: a '
                             'sinistra il manifesto appena uscito dal plotter, a destra la squadra che lo attacca in via '
                             'dei Tribunali.', size=48, sizes=(38, 30)), wm='1-3', ws='1-1'),
               t.col([X.confronto(K, t, STAMPA_3, ATTACCHINI, 'In stamperia', 'In strada', ratio='16/10')], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.label(t, 'Per chi ha una cosa da dire', color=GIALLO),
                      K.headline(t, 'La tua campagna <em>la attacchiamo noi.</em>', size=64, tag='h2', pad_bottom=0,
                                 sizes=(46, 32))], wm='2-3', ws='1-1'),
               t.col([K.button(t, 'Prenota un’uscita', PRENOTA, kind='pieno', pad_top=0)], wm='1-3', ws='1-1')],
              layout='custom', custom='66,34', gap=48, valign='end'),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [t.section([
        t.row([t.col([K.label(t, 'Prenota', color=GIALLO),
                      K.headline(t, 'Dicci dove,<br>ti diciamo quando.', tag='h1', size=92, color=C['chiaro'], pad_bottom=18,
                                 sizes=(64, 40)),
                      K.text(t, 'Il preventivo arriva in giornata, con la mappa degli impianti liberi nella zona che hai scelto.',
                             size=18, color=mix(C['chiaro'], 90))], wm='2-3', ws='1-1'),
               t.col([], wm='1-3', ws='1-1')], layout='custom', custom='66,34', valign='end', anim='fade'),
    ], bg=K.sui_media(K.video_bg(V_STRADA, overlay=30), 'left', 80, 88)[0], padding='large',
        style={'padding_top': 230, 'padding_bottom': 90})]
    campi = [K.campo('text', 'Nome e azienda', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Formato', 'formato', True, opzioni='Cartellone 6 × 3\nPoster 100 × 140\nPensiline\nMetro\n'
                     'Schermi\nNon lo so ancora'),
             K.campo('date', 'Dalla quattordicina del', 'dal', True), K.campo('text', 'Zona', 'zona', segnaposto='Vomero, '
                                                                                                            'Tangenziale…'),
             K.campo('textarea', 'Cosa vuoi dire', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Il preventivo', ['Due righe', 'e ti richiamiamo.'],
                                'Se hai già il manifesto, allegalo dopo per email: ti diciamo se la misura va bene.', VOCI, campi,
                                'Affisso — richiesta di uscita', 'Chiedi il preventivo',
                                'Grazie! Entro stasera ti mandiamo gli impianti liberi.'))
    out.append(X.zone(K, t, 'Dove siamo', ['Il magazzino', 'a Gianturco.'], 'Qui stampiamo e da qui partono le squadre, ogni '
                      'lunedì alle cinque.', [('Auto', 'uscita Corso Malta, parcheggio interno'), ('Treno', 'stazione '
                      'Gianturco, 6 minuti'), ('Bus', 'linea 192, fermata Gianturco'), ('Telefono', PHONE)],
                      LAT, LNG, 'Affisso · via Gianturco 87', True, HEX['primary']))
    out.append(K.domande(t, 'Prima di prenotare', ['Quello che', 'ci chiedono tutti.'], [
        ('Quanto prima devo prenotare?', 'Per i cartelloni almeno tre settimane, perché vanno a ruba. Per i poster bastano dieci '
         'giorni.'),
        ('Mi mandate davvero le foto?', 'Sì, di ogni impianto, con la via, il giorno e l’ora. Le ricevi in una cartella entro la sera '
         'dell’attacchinaggio.'),
        ('E se piove?', 'La carta blueback regge. Se un manifesto si strappa nei primi sette giorni lo riattacchiamo noi, senza costi.'),
        ('Mi aiutate col manifesto?', 'Sì: se ci dai testo e foto, lo impaginiamo noi nella misura giusta. Costa 120 € a formato.')],
        'faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · muro libero', color=GIALLO, align='center'),
                       K.headline(t, 'Qui non c’è <em>niente attaccato.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=GIALLO),
                       K.text(t, 'La pagina che cercavi è stata staccata, o non è mai stata attaccata. Torna alla home.',
                              size=17, align='center', color=mix(C['chiaro'], 90)),
                       K.button(t, 'Alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.sui_media(K.video_bg(V_ROSA, overlay=36), 'bottom', 100, 88)[0], padding='xlarge',
        style={'padding_top': 240, 'padding_bottom': 200})]


THEME = tema(
    ID, NAME, CATEGORIA, 'Il sito-cartellone',
    ['agenzia', 'affissioni', 'pubblicità', 'napoli', 'scuro', 'slider pro', 'prismi', 'scorrimento'],
    'Un’agenzia di affissioni a Napoli: nell’apertura, sopra la città di notte, un cartellone a otto prismi gira mentre '
    'scorri e mostra tre manifesti; poi i formati passano l’uno nell’altro con i prismi, come su un cartellone vero. Le '
    'uscite del 2027 in calendario, il manifesto vecchio da grattare, quanto costa un’uscita, le campagne fotografate.',
    [('I formati', FORMATI), ('Campagne', CAMPAGNE), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('i-formati', 'I formati', 'i-formati'), ('campagne', 'Campagne', 'campagne'),
     ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Schibsted Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Archivo Black\', Impact, sans-serif',
    '\'IBM Plex Mono\', ui-monospace, monospace', ['Schibsted Grotesk', 'Archivo Black', 'IBM Plex Mono'], corsivo_di=[],
    peso_titoli='400', raggio=0, grana=4, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'colla'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = '#FFFFFF'
THEME['filtri'] = {'gesto': 'scorri', 'carattere': 'decorativo'}
THEME['preview_video_v'] = '1'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-formati.json': build_formati(), 'campagne.json': build_campagne(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
