# -*- coding: utf-8 -*-
"""Genera il tema «Rastrello» (id rastrello): giardinieri a Lucca — manutenzione di giardini e parchi di ville,
potature, siepi e topiaria, prati, irrigazione; l'abbonamento con le visite in calendario, stagione per stagione.

La trovata: «l'erba cresce». Lo slider resta fermo mentre si scorre: sul video di un giardino all'italiana, in basso,
l'erba si alza un ciuffo dopo l'altro, sempre più alta («L'erba cresce»); poi entra il tosaerba da sinistra e la taglia
fetta per fetta, coi fili che volano, e sotto resta il prato rasato a righe («Poi passiamo noi»). Dal 15 nov 2026 il
tosaerba è a cilindro, col manico dietro (si spinge), e ruota, rullo e lame girano solo mentre avanza, quanto la strada
fatta; l'erba alta ondeggia nel vento (animazione «Vento» del Pro Slider).

Tema chiaro: carta, verde bosso, l'oliva, la terracotta dei vasi di limoni. Titoli in Cormorant, testo in Figtree, dati
in Geist Mono. Cursore: irrigatore.
"""
import math
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'rastrello'
NAME = 'Rastrello'

HEX = {
    'primary': '#2F5D3A', 'primary_contrast': '#FFFFFF', 'secondary': '#7E9468', 'secondary_contrast': '#FFFFFF',
    'accent': '#A94F25', 'dark': '#142018', 'light': '#FFFFFF', 'muted': '#ECEDE3', 'muted_contrast': '#18261C',
    'text': '#18261C', 'text_muted': '#526057', 'background': '#F4F3EC', 'border': '#D3D5C4', 'link': '#2F5D3A',
}
HOME, GIARDINI, STAGIONI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:giardini}}', '{{PAGINA:le-stagioni}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'giardini@rastrello.example', '0583 000 321', 'tel:+390583000321'
LAT, LNG = '43.8429', '10.5027'

V_GIARDINO, V_PARTERRE, V_TOSAERBA, V_SIEPE, V_FORBICI, V_IRRIGA, V_VILLA = (
    'pexels-video-37618414', 'pexels-video-29393757', 'pexels-video-37733806', 'pexels-video-3078390', 'pexels-video-4153438',
    'pexels-video-20521968', 'pexels-video-4207865')
VIDEOS = [V_GIARDINO, V_PARTERRE, V_TOSAERBA, V_SIEPE, V_FORBICI, V_IRRIGA, V_VILLA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(CHIARA, LUCA, GIANNI, DONNA_POTA, ORTO, CESOIE, ANNAFFIA, TOSAERBA_ROSSO, TOSAERBA_ARANCIO, PRATO_SERA, PRATO_TRATTORE, VIALE_SIEPI,
 SIEPE_TAGLIO, BOSSO_SFERA, SIEPE_ALTA, VILLA_SCALE, PARTERRE_ALTO, VILLA_COLLINA, PARTERRE, LIMONAIA, GIARDINO_STORICO) = f(
    6231775, 24595772, 7509492, 27176779, 4975395, 38936344, 38673906, 4162011, 37554739, 6728925, 39625998, 36713682, 38936334,
    9204, 32427440, 24446088, 6711591, 31585417, 34017020, 38115055, 36580480)
PHOTOS = [CHIARA, LUCA, GIANNI, DONNA_POTA, ORTO, CESOIE, ANNAFFIA, TOSAERBA_ROSSO, TOSAERBA_ARANCIO, PRATO_SERA, PRATO_TRATTORE,
          VIALE_SIEPI, SIEPE_TAGLIO, BOSSO_SFERA, SIEPE_ALTA, VILLA_SCALE, PARTERRE_ALTO, VILLA_COLLINA, PARTERRE, LIMONAIA,
          GIARDINO_STORICO]
ERBA = ['rastrello-erba-%d.png' % k for k in range(1, 11)]
DISEGNI = ERBA + ['rastrello-prato.png', 'rastrello-tosaerba.svg', 'rastrello-sfalcio.svg']
# il tosaerba a cilindro del 15 nov 2026 (disegni_aperture_cinque.py): corpo, cilindro delle lame, ruota, rullo
TOSAERBA = ['rastrello-tosaerba2.svg', 'rastrello-cilindro.svg', 'rastrello-ruota.svg', 'rastrello-rullo.svg']
LOGHI = ['rastrello-logo-chiaro.png', 'rastrello-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(TOSAERBA) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#D9E7B8')
C = K.c
CHIARO = C['chiaro']
FONT_T = 'Cormorant'
VOCI = [('map-pin', 'Via di Tiglio 1210 · Lucca', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 170, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '483/119', 'Rastrello: giardinieri a Lucca dal 1988. Giardini di case e parchi '
                   'di ville, potature, siepi, prati e irrigazione, con le visite in calendario.',
                   [('I giardini', GIARDINI), ('Le stagioni', STAGIONI), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Il vivaio', 'Lunedì — sabato 7:30 — 17<br>D’estate dalle 6, col fresco', VOCI,
                   '© 2026 Rastrello · Lucca · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                   ('facebook', 'https://facebook.com')], scuro=False)


# ── L'apertura: l'erba cresce, poi passiamo noi ──
def taglio(k):
    return round(44 + 4.6 * k, 1)


def tosaerba(giro, dove, X0, Y0, W, H, sfalcio):
    """Il tosaerba a cilindro in quattro pezzi (disegni_aperture_cinque.py: corpo 600 × 420, ruota (330, 300) r 100,
    cilindro (395, 338) r 58, rullo (130, 370) r 30, terreno a y 400), più l'erba tagliata che vola dal cilindro.
    X0, Y0, W, H: il riquadro del corpo in % della tela. dove: 'd' computer e tablet, 'm' telefono (sul telefono il
    tosaerba è più grande rispetto alla tela, e le ruote devono girare di più per la stessa strada: un gruppo a parte).
    Le ruote girano in proporzione alla strada: gradi = spostamento (px della tela da 1440) × 150 / (2π × larghezza %)
    per la ruota da 100 unità; il rullo più piccolo gira più svelto, il cilindro va a ingranaggi, quattro volte la ruota.
    Andamento costante: il tosaerba va a velocità uniforme, non si ferma a ogni fetta d'erba."""
    ux, uy = W / 600.0, H / 420.0
    giri = 150.0 / (2 * math.pi * W)
    x0 = giro[0]['x']

    def pezzo(lid, nome, cx, cy, rr, fattore, alt=''):
        tappe = [{'p': g['p'], 'x': g['x'], 'r': round((g['x'] - x0) * giri * fattore, 1)} for g in giro]
        box = dict(x=round(X0 + (cx - rr) * ux, 3), y=round(Y0 + (cy - rr) * uy, 3), w=round(2 * rr * ux, 3), h=round(2 * rr * uy, 3))
        return pos(lid, nome, box, {'inizio': 0, 'fine': 100, 'tappe': tappe, 'andamento': 'costante'}, alt)

    def pos(lid, nome, box, mossa, alt=''):
        if dove == 'd':
            geo, resp = box, {'mobile': {'visible': False}}
        else:
            geo = dict(x=0, y=0, w=0.1, h=0.1)
            resp = {'mobile': {'x': box['x'], 'y': box['y'], 'width': box['w'], 'height': box['h']}}
        return K.livello(lid, tipo='image', imageSrc=K.img(nome), imageAlt=alt, objectFit='contain', entra='fadeIn', ritardo=0,
                         durata=300, scrollMove=mossa, responsive=resp, **geo)

    corpo = {'inizio': 0, 'fine': 100, 'tappe': [{'p': g['p'], 'x': g['x']} for g in giro], 'andamento': 'costante'}
    sx, sy, sw, sh = sfalcio
    vola = {'inizio': 0, 'fine': 100, 'andamento': 'costante',
            'tappe': [{'p': g['p'], 'x': g['x'], 'o': 0 if g['p'] < 42 or g['p'] > 90 else 100} for g in giro]}
    return [
        pos('ra-sfalcio-' + dove, DISEGNI[12], dict(x=sx, y=sy, w=sw, h=sh), vola),
        pos('ra-tosaerba-' + dove, TOSAERBA[0], dict(x=X0, y=Y0, w=W, h=H), corpo, 'Il tosaerba'),
        pezzo('ra-cilindro-' + dove, TOSAERBA[1], 395, 338, 58, 4.0),
        pezzo('ra-rullo-' + dove, TOSAERBA[3], 130, 370, 30, 100 / 30.0),
        pezzo('ra-ruota-' + dove, TOSAERBA[2], 330, 300, 100, 1.0),
    ]


def apertura(t):
    m = K.mob
    # giro: dove sta il tosaerba (px della tela da 1440) a ogni punto dello scorrimento. Il cilindro delle lame del tosaerba
    # largo 26 % (al 70 % della sua larghezza) arriva al centro della fetta k quando la taglia: tosaerba() mette il corpo
    # in modo che il cilindro cada lì anche col disegno più grande del telefono
    giro = [{'p': 0, 'x': -420}, {'p': 38, 'x': -420}]
    giro += [{'p': taglio(k), 'x': -276 + 144 * k} for k in range(10)]
    giro += [{'p': 96, 'x': 1500}, {'p': 100, 'x': 1500}]
    via = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 38, 'o': 100}, {'p': 46, 'o': 0}, {'p': 100, 'o': 0}]}
    arriva = lambda da: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 30}, {'p': da, 'o': 0, 'y': 30},
                                                              {'p': da + 6, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    L = K.veli('ra-velo', 'alto', 40, 62, colore=C['nero'], forza=86, mobile_alto=24) + [
        K.livello('ra-prato', tipo='image', imageSrc=K.img(DISEGNI[10]), imageAlt='', objectFit='cover', x=0, y=88.9, w=100, h=11.1,
                  entra='fadeIn', ritardo=0, durata=300, responsive={'mobile': {'x': 0, 'y': 94, 'width': 100, 'height': 6}}),
    ]
    for k in range(10):
        pc = taglio(k)
        L.append(K.livello('ra-erba%d' % k, tipo='image', imageSrc=K.img(ERBA[k]), imageAlt='', objectFit='cover', x=round(k * 10 - 0.1, 2),
                           y=61.1, w=10.2, h=38.9, entra='fadeIn', ritardo=0, durata=300, customCSS='transform-origin:50% 100%;',
                           animLoop='windLoop', animLoopDuration=4600, animLoopEasing='ease-in-out',
                           scrollMove={'inizio': 0, 'fine': 100, 'tappe': [
                               {'p': 0, 'sy': 0.28}, {'p': 8 + k * 2, 'sy': 0.3}, {'p': 34 + k, 'sy': 1}, {'p': pc, 'sy': 1, 'o': 100},
                               {'p': pc + 2, 'sy': 0.12, 'o': 100}, {'p': pc + 4, 'sy': 0.12, 'o': 0}, {'p': 100, 'sy': 0.12, 'o': 0}]},
                           responsive={'mobile': {'x': round(k * 10 - 0.1, 2), 'y': 76, 'width': 10.2, 'height': 24}}))
    L += tosaerba(giro, 'd', X0=6.0, Y0=64, W=26, H=32.4, sfalcio=(21.02, 71.4, 14, 24.9))
    L += tosaerba(giro, 'm', X0=-6.62, Y0=83.95, W=44, H=14.23, sfalcio=(19.05, 87.77, 22, 10.17))
    L += [
        K.livello('ra-o0', contenuto='Rastrello · giardinieri · Lucca', x=6, y=10, w=44, size=13, peso='500', colore=mix(CHIARO, 86),
                  letterSpacing=3, textTransform='uppercase', fontFamily='Geist Mono', ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 7, 'width': 88}}),
        K.livello('ra-t0', contenuto='L’erba cresce.', tag='h1', x=6, y=13, w=60, size=120, peso='600', colore=CHIARO, fontFamily=FONT_T,
                  lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=900, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(46), 'x': 6, 'y': 9.5, 'width': 88}}),
        K.livello('ra-s0', contenuto='D’estate anche tre centimetri a settimana.', x=6, y=29, w=36, size=18, peso='400', colore=mix(CHIARO, 92),
                  lineHeight=1.5, ritardo=700, scrollMove=via, responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 15.5, 'width': 86}}),
        K.livello('ra-t1', contenuto='Poi passiamo noi.', tag='h2', x=6, y=13, w=70, size=120, peso='600', fontStyle='italic', colore=CHIARO,
                  fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=arriva(84),
                  responsive={'mobile': {'fontSize': m(38), 'x': 6, 'y': 9.5, 'width': 88}}),
        K.livello('ra-s1', contenuto='Giardini di case e parchi di ville, a Lucca e sulle colline: prati, siepi, potature e '
                                     'irrigazione, con le visite in calendario.', x=6, y=29, w=38, size=18, peso='400',
                  colore=mix(CHIARO, 92), lineHeight=1.5, entra='fadeIn', ritardo=0, scrollMove=arriva(87),
                  responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 15.5, 'width': 86}}),
        K.livello('ra-b1', tipo='button', contenuto='Le stagioni', x=6, y=42, w='auto', size=15, peso='600', colore='#18261C',
                  bgColor='#D9E7B8', padding=18, borderRadius=2, buttonUrl=STAGIONI, entra='fadeIn', ritardo=0, scrollMove=arriva(90),
                  hover={'bgColor': '#FFFFFF', 'duration': 250}, responsive={'mobile': {'x': 6, 'y': 26, 'fontSize': m(13)}}),
        K.livello('ra-b2', tipo='button', contenuto='Un sopralluogo', x=18.5, y=42, w='auto', size=15, peso='600', colore=CHIARO,
                  bgColor='rgba(20,32,24,.5)', padding=18, borderRadius=2, borderWidth=1, borderColor=mix(CHIARO, 65),
                  buttonUrl=CONTATTI + '#scrivi', entra='fadeIn', ritardo=0, scrollMove=arriva(90),
                  hover={'bgColor': mix(CHIARO, 16), 'duration': 250}, responsive={'mobile': {'x': 46, 'y': 26, 'fontSize': m(13)}}),
    ]
    M.veli_oltre(L)
    sl = [K.diapositiva('ra-1', K.sfondo_video(V_GIARDINO, overlay=0.12, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=2000, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


# ── Questo mese (15 nov 2026): dodici «storie» firmate dall'agronoma, una per mese; il sito mostra da sé quella del mese
# in corso (condizione «Mesi dell'anno» sulla tile). Un giardiniere che scrive il giardino di adesso, senza aggiornare niente.
MESI = [
    (1, 'gennaio', GIANNI, 'Gli alberi, finché dormono.',
     'Si potano i frutti e gli alberi alti, con la piattaforma, quando non c’è linfa che scorre. Il prato si lascia in pace: '
     'calpestarlo col gelo lo rovina fino a primavera.'),
    (2, 'febbraio', DONNA_POTA, 'Le rose e gli olivi.',
     'È il mese della potatura delle rose e degli olivi della collina: tagli puliti, sopra una gemma che guarda fuori. '
     'Ricontrolliamo i pali e le legature dei rampicanti.'),
    (3, 'marzo', PARTERRE, 'Il giardino si sveglia.',
     'La prima concimazione, la prima rasatura alta, l’arieggiatura dove il prato ha fatto il muschio. L’impianto '
     'd’irrigazione si rimette in moto, zona per zona.'),
    (4, 'aprile', PRATO_TRATTORE, 'I prati nuovi.',
     'La terra si lavora e si semina: ad aprile il seme nasce in dieci giorni. Nei prati vecchi si riempiono le chiazze '
     'lasciate dall’inverno.'),
    (5, 'maggio', TOSAERBA_ROSSO, 'Il primo vero taglio.',
     'Il prato cresce di un centimetro al giorno: si passa ogni settimana, sempre alla stessa altezza. Le siepi di bosso '
     'hanno messo i getti nuovi e aspettano il filo.'),
    (6, 'giugno', SIEPE_TAGLIO, 'Le siepi a filo.',
     'A fine giugno si tagliano le siepi di bosso e di alloro a filo di cesoia, col filo teso fra due paletti. Si alza '
     'l’erba di un dito: all’ombra di sé stessa beve meno.'),
    (7, 'luglio', PRATO_SERA, 'L’acqua all’alba.',
     'L’irrigazione parte alle cinque, quando la terra è fresca e l’acqua non evapora. Il prato si taglia la sera, '
     'mai sotto il sole delle due.'),
    (8, 'agosto', LIMONAIA, 'Chi resta in giardino.',
     'Mentre siete al mare passiamo noi: i limoni in vaso, i sensori di pioggia, le piante appena messe. Vi mandiamo '
     'una foto a visita.'),
    (9, 'settembre', ORTO, 'La seconda primavera.',
     'Con le prime piogge il prato riparte: si risemina dove l’estate l’ha bruciato e si concima per l’inverno. È anche il '
     'mese migliore per piantare siepi nuove.'),
    (10, 'ottobre', VIALE_SIEPI, 'Le ultime rifiniture.',
     'L’ultima rifilatura delle siepi e dei bordi, le aiuole pulite, i bulbi di tulipano e narciso messi a dimora per '
     'marzo.'),
    (11, 'novembre', CESOIE, 'Le foglie e i bulbi.',
     'Le foglie si raccolgono ogni settimana, prima che soffochino il prato, e finiscono nel compost. Si piantano gli '
     'ultimi bulbi e gli alberi a radice nuda, che attecchiscono meglio col freddo.'),
    (12, 'dicembre', BOSSO_SFERA, 'Il giardino al riparo.',
     'I limoni vanno in limonaia, le piante giovani sotto il tessuto contro il gelo. Le topiarie si guardano soltanto: '
     'il bosso si taglia di nuovo a primavera.'),
]


def questo_mese(t):
    voci = []
    for m, nome, foto, titolo, testo in MESI:
        voci.append(t.bare('featuredstory', {
            'kicker_text': 'Questo mese · ' + nome, 'headline_text': titolo, 'headline_url': STAGIONI, 'standfirst': testo,
            'byline_pre': 'Il consiglio di', 'byline_name': 'Chiara Pardini', 'byline_meta': 'agronoma',
            'cover_image': K.img(foto), 'cover_url': STAGIONI, 'cover_label': titolo,
            'cta1_text': 'Un giro gratis', 'cta1_url': CONTATTI, 'cta2_text': '', 'media_side': 'left', 'col_ratio': '1.1fr .9fr',
            'cover_aspect': '4 / 3', 'media_radius': 2, 'standfirst_italic': False, 'placeholder_dark': False,
            'bg_color': 'transparent', 'kicker_color': C['acc'], 'headline_color': C['testo'], 'standfirst_color': C['testo'],
            'byline_color': C['tenue'], 'byline_name_color': C['testo'], 'media_bg': C['sala'], 'cta_solid_bg': C['acc'],
            'cta_solid_text': C['on_acc'], 'cta_radius': {'tl': 2, 'tr': 2, 'br': 2, 'bl': 2},
            # il sito la mostra solo nel suo mese
            'cond_type': 'months', 'cond_month_from': str(m), 'cond_month_to': str(m)}, style={}))
    return t.section([t.one(voci)], bg=solid(C['sala']), padding='large', width='large', html_id='questo-mese',
                     style={'padding_top': 110, 'padding_bottom': 110})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Rastrello · Lucca, dal 1988', ['Un giardino', 'si cura tutto l’anno.'], size=72, layout='stack',
                                size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo dieci giardinieri e un agronomo. Curiamo giardini di case e parchi di ville tra la Piana '
                                'e le colline: il prato, le siepi di bosso, gli olivi, i limoni in vaso, l’irrigazione. Con '
                                'l’abbonamento le visite sono in calendario, e non dovete chiamare.', size=17),
                      K.button(t, 'Le stagioni', STAGIONI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('calendar', 'Visite <b>in calendario</b>'), ('leaf', 'Potature <b>col potatore abilitato</b>'),
                                ('sprout', 'Niente diserbanti <b>chimici</b>'), ('truck', 'Il verde <b>lo portiamo via noi</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(questo_mese(t))
    out.append(t.section([
        t.one([K.sechead(t, 'Le stagioni', ['Quattro giri', 'del giardino.'], tagline='Passate sulle carte: cosa facciamo, '
                         'stagione per stagione.', size=60)]),
        t.one([P.carte(K, t, [(PARTERRE, 'Primavera', 'il risveglio', 'Concime, la prima rasatura, la potatura delle rose, '
                               'l’impianto di irrigazione rimesso in moto.'),
                              (PRATO_SERA, 'Estate', 'l’acqua', 'Il prato ogni dieci giorni, l’irrigazione all’alba, le siepi '
                               'a fine giugno.'),
                              (VILLA_COLLINA, 'Autunno', 'le foglie', 'Le foglie raccolte, i bulbi, la seconda potatura delle siepi.'),
                              (LIMONAIA, 'Inverno', 'gli alberi', 'La potatura degli olivi e dei frutti, i limoni al riparo.')],
                      altezza='420', colonne=4)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SIEPE, 'Le siepi', 'Il bosso <em>a filo di cesoia.</em>',
                             'Le siepi del giardino all’italiana si tagliano a mano, due volte l’anno, col filo teso per tenerle dritte.',
                             overlay=18))
    out.append(t.section([
        t.one([K.sechead(t, 'Alcuni giardini', ['Ville, case,', 'un orto in città.'], size=56)]),
        t.one([K.coverflow(t, [(VILLA_SCALE, 'La scalinata di una villa tra le siepi', 'La villa di Marlia', 'parco storico'),
                               (PARTERRE_ALTO, 'Un parterre di bosso visto dall’alto', 'Il parterre di Segromigno', 'bosso e ghiaia'),
                               (VIALE_SIEPI, 'Un viale tra due siepi', 'Il viale di Camigliano', 'siepi di alloro'),
                               (BOSSO_SFERA, 'Una sfera di bosso sul prato', 'Il giardino di Sant’Alessio', 'topiaria'),
                               (GIARDINO_STORICO, 'Un giardino storico col prato e le statue', 'La villa di Capannori', 'prato e statue')],
                              altezza=420, larghezza=360)]),
        t.one([K.button(t, 'I giardini', GIARDINI, align='center', pad_top=30)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('Con l’abbonamento non chiamo più nessuno: arrivano il martedì, e il prato è sempre a posto.', 'Gabriella', 'Sant’Anna'),
        ('Le siepi di bosso della villa erano malate. Le hanno salvate quasi tutte, senza veleni.', 'Federico', 'Marlia'),
        ('Mi hanno fatto l’irrigazione a goccia sotto gli olivi: in agosto il giardino era verde e la bolletta più bassa.', 'Paolo', 'Monte San Quirico')],
        'Chi ci chiama', ['Tre giardini,', 'tutto l’anno.'], fondo=True))
    return out


def build_giardini():
    t = Tpl(ID, 'giardini')
    out = [K.intro(t, 'I giardini', ['Dal prato', 'al parco della villa.'],
                   'Manutenzione, potature, siepi e topiaria, prati nuovi, irrigazione: quello che facciamo, e qualche giardino.',
                   bg=K.video_bg(V_PARTERRE, overlay=22), size=92)]
    out.append(t.section([t.one([P.elenco_hover(K, t, [
        ('Manutenzione', 'con l’abbonamento', 'Prato, siepi, aiuole e foglie: le visite in calendario, da marzo a novembre.', PRATO_SERA),
        ('Potature', 'olivi, frutti, alberi alti', 'Il potatore abilitato e la piattaforma per gli alberi alti.', CESOIE),
        ('Siepi e topiaria', 'bosso, alloro, tasso', 'Sfere, coni, parterre: a mano, col filo teso.', BOSSO_SFERA),
        ('Prati nuovi', 'a seme o a rotoli', 'Terra lavorata, drenaggio, seme o rotoli: pronti in tre settimane.', PRATO_TRATTORE),
        ('Irrigazione', 'a goccia e a pioggia', 'Programmata, coi sensori di pioggia: l’acqua solo quando serve.', ANNAFFIA),
    ], CONTATTI + '#scrivi', size=46)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Al lavoro', ['Le mani', 'nel verde.'], size=56)]),
        P.fila_foto(K, t, [(SIEPE_TAGLIO, 'La siepe tagliata a mano'), (DONNA_POTA, 'La potatura'), (ORTO, 'L’orto'),
                           (TOSAERBA_ROSSO, 'Il tosaerba sul prato')], ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_IRRIGA, 'L’acqua', 'Solo quando serve, <em>all’alba.</em>',
                          'L’irrigazione programmata coi sensori di pioggia: d’estate si risparmia un terzo dell’acqua.',
                          'Un sopralluogo', CONTATTI + '#scrivi', video=True))
    return out


def build_stagioni():
    t = Tpl(ID, 'le-stagioni')
    out = [K.intro(t, 'Le stagioni', ['Il giardino', 'in calendario.'],
                   'Con l’abbonamento le visite sono già scritte: sapete quando veniamo e cosa facciamo, e non dovete chiamare.',
                   bg=K.video_bg(V_FORBICI, overlay=24), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il calendario', ['Dodici mesi,', 'quattro lavori.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('mar', 'primavera', PARTERRE, 'Risvegliare', 'Il concime', 'e le rose', 'La prima rasatura, la potatura delle rose, il controllo '
             'dell’irrigazione.', '4', 'visite'),
            ('giu', 'estate', PRATO_SERA, 'Tenere', 'Il prato', 'all’alba', 'Ogni dieci giorni il prato, a fine giugno le siepi.', '9', 'visite'),
            ('nov', 'autunno e inverno', LIMONAIA, 'Preparare', 'Le foglie', 'e gli alberi', 'Le foglie, i bulbi, la potatura degli olivi.',
             '5', 'visite')], icona='calendar')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    conf = X.preventivo(K, t, [
        ('giardino', 'Il giardino', 'uno', True, 'L’abbonamento comprende prato, siepi, aiuole, foglie e lo smaltimento del verde.'),
        ('visite', 'Le visite', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('piccolo', 'giardino', 'Piccolo', 'Fino a 300 m².', 65, '', True),
        ('medio', 'giardino', 'Medio', 'Da 300 a 1.000 m².', 120, 'il più scelto', False),
        ('parco', 'giardino', 'Parco', 'Oltre 1.000 m², con gli alberi.', 260, '', False),
        ('mensile', 'visite', 'Una volta al mese', '', 0, '', True),
        ('quindici', 'visite', 'Ogni quindici giorni', '', 55, '', False),
        ('settimana', 'visite', 'Ogni settimana', 'Da aprile a settembre.', 140, '', False),
        ('irrigazione', 'extra', 'Irrigazione seguita', 'Programmi e controlli.', 18, '', False),
        ('olivi', 'extra', 'Potatura degli olivi', 'Una volta l’anno, ripartita.', 22, '', False),
        ('limoni', 'extra', 'Limoni in vaso', 'Concime, potatura, riparo d’inverno.', 15, '', False)],
        'Il vostro abbonamento', CONTATTI + '?lavoro={configurazione}#scrivi', 'IVA compresa · il primo sopralluogo è gratis',
        periodo='/mese', testo_pulsante='Chiedi il sopralluogo')
    out.append(X.sezione_preventivo(K, t, 'L’abbonamento', ['Componete,', 'il canone si fa da sé.'], '', conf))
    out.append(K.domande(t, 'Sull’abbonamento,', 'le domande di tutti.', [
        ('Posso sospenderlo?', 'Sì, da dicembre a febbraio si paga solo se ci sono potature; e si disdice con un mese di preavviso.'),
        ('Usate diserbanti?', 'No: le erbacce si tolgono a mano o col vapore, i parassiti si combattono con i loro nemici naturali.'),
        ('Il verde tagliato dove va?', 'Lo portiamo noi al compost: nel prezzo, sempre.'),
    ], 'ra-faq', bg=False))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Un giro', 'nel vostro giardino.'],
                   'Il primo sopralluogo è gratis: guardiamo il giardino con voi e vi lasciamo il calendario delle visite.',
                   bg=K.video_bg(V_VILLA, overlay=22), size=96)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Comune', 'comune'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Il giardino, in due righe', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Veniamo a vedere,', 'gratis.'],
                                'Vi richiamiamo entro due giorni per fissare il giro.', VOCI, campi, 'Rastrello — sopralluogo',
                                'Chiedi il sopralluogo', 'Grazie! Vi richiamiamo entro due giorni.'))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi viene da voi', ['Dieci giardinieri,', 'tre li conoscete qui.'], size=56)]),
        K.squadra(t, [(CHIARA, 'Chiara Pardini', 'Agronoma', 'I progetti e le piante malate: il bosso lo salva lei.', 'center 25%'),
                      (LUCA, 'Luca Bertolucci', 'Giardiniere', 'Siepi e topiaria: il filo teso e le cesoie a mano.', 'center 25%'),
                      (GIANNI, 'Gianni Lazzari', 'Potatore', 'Olivi e alberi alti, con la piattaforma.', 'center 25%')],
                  ratio='4/5', size='320'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.zone(K, t, 'Dove lavoriamo', ['La Piana', 'e le colline.'], 'I comuni dove passiamo ogni settimana.',
                      [('Lucca', 'dentro e fuori le Mura'), ('Capannori', 'ogni settimana'), ('Colline lucchesi', 'Marlia, Segromigno, Matraia'),
                       ('Porcari, Altopascio', 'ogni settimana'), ('Versilia', 'su abbonamento'), ('Garfagnana', 'potature')],
                      LAT, LNG, 'Rastrello · il vivaio', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · erba alta', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui non passa <em>nessuno.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=CHIARO, accento=K.acc_media),
                       K.text(t, 'L’erba è cresciuta sopra questa pagina. Torniamo al viale.', size=17, align='center', color=mix(CHIARO, 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_TOSAERBA, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'L’erba che cresce',
    ['artigiani', 'giardiniere', 'giardini', 'potature', 'manutenzione', 'chiaro', 'slider pro', 'scorrimento'],
    'Giardinieri a Lucca: scorrendo, sul video di un giardino all’italiana l’erba si alza un ciuffo dopo l’altro, finché entra '
    'il tosaerba e la taglia fetta per fetta, coi fili che volano e il prato rasato a righe. Poi le stagioni da girare, i '
    'giardini, il calendario e l’abbonamento da comporre.',
    [('I giardini', GIARDINI), ('Le stagioni', STAGIONI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('giardini', 'I giardini', 'giardini'), ('le-stagioni', 'Le stagioni', 'le-stagioni'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Figtree\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cormorant\', Georgia, serif',
    '\'Geist Mono\', ui-monospace, Menlo, monospace', ['Figtree', 'Cormorant', 'Geist Mono'], corsivo_di=['Cormorant'],
    peso_titoli='600', raggio=2, grana=4, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'irrigatore'
THEME['cursor']['ring_color'] = '#7CC6F2'
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'giardini.json': build_giardini(), 'le-stagioni.json': build_stagioni(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
