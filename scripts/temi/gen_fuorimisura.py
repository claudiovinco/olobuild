# -*- coding: utf-8 -*-
"""Genera il tema «Fuorimisura» (id fuorimisura): un marchio di streetwear di Napoli che fa tutto oversize, in una taglia
sola: grande. Felpe come tendoni, magliette come lenzuola, jeans a paracadute.

Tutto è troppo grande, anche il sito: in apertura la parola FUORIMISURA è più larga dello schermo e scorrendo passa di
lato (resta ferma in cima mentre si scorre), con sotto una riga che va nel verso opposto; i titoli sono enormi, le schede
dei capi due per riga, tre pannelli arrivano di lato uno sopra l'altro («Cover orizzontale») e il nastro di scritte è
alto come un piano. Il cursore è XXL.

Tema scuro, nero asfalto, verde acido e arancio. Titoli in Lexend Zetta (larghissimo), testo in Outfit, dati in Martian
Mono. Logo: FUORIMISURA che esce dalla sua etichetta, col cartellino XXL. Cursore: XXL (quattro volte la freccia).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'fuorimisura'
NAME = 'Fuorimisura'

HEX = {
    'primary': '#C6FF00', 'primary_contrast': '#111111', 'secondary': '#FF4D2E', 'secondary_contrast': '#111111',
    'accent': '#FF4D2E', 'dark': '#0A0A0A', 'light': '#FFFFFF', 'muted': '#1B1B1B', 'muted_contrast': '#F2F2EE',
    'text': '#F2F2EE', 'text_muted': '#B5B5AE', 'background': '#111111', 'border': '#2E2E2E', 'link': '#C6FF00',
}
ACIDO = '#C6FF00'

HOME, CAPI, TAGLIA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:capi}}', '{{PAGINA:la-taglia}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@fuorimisura.example', '+39 081 000 9090', 'tel:+390810009090'
LAT, LNG = '40.8430', '14.2470'

V_PIUMINO, V_RIFLESSO, V_TUTA, V_SKATE, V_PIAZZA, V_TAVOLA, V_BALLO, V_SCRITTE, V_MURO = (
    'pexels-video-10380610', 'pexels-video-10380614', 'pexels-video-6321961', 'pexels-video-5039486', 'pexels-video-5039462',
    'pexels-video-7644968', 'pexels-video-8928136', 'pexels-video-6189526', 'pexels-video-8928134')
VIDEOS = [V_PIUMINO, V_RIFLESSO, V_TUTA, V_SKATE, V_PIAZZA, V_TAVOLA, V_BALLO, V_SCRITTE, V_MURO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
FEL = f('16982868', '18956666', '16982942', '30257616', '14610779', '16982850', '16982834')
STR = f('7061988', '7327029', '17459726', '34582214', '20467325', '6905495', '12821063')
JEA = f('10400987', '25484767', '36510884', '29347454', '28996603', '39182834')
SKA = f('27396756', '27456921', '5157742', '5159250', '5158834')
URB = f('17794429', '37036193', '13197630', '15853436', '36840412')
MAG = f('8794470', '16048127', '18301882', '5325899', '18584221', '8089961', '8089958')
SNE = f('5037306', '15229823', '40059921', '31432165', '38487263')
MUR = f('2247213', '29678667', '3844145', '35178630')
HIP = f('33332852', '17505433', '7958142', '13482152', '8973527')
CAM = f('35984242', '2820900', '8695266', '8980684', '8694446')
LOGO = 'logo-fuorimisura-chiaro.png'
PHOTOS = FEL + STR + JEA + SKA + URB + MAG + SNE + MUR + HIP + CAM
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO, 'logo-fuorimisura-scuro.png'}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='800', corsivo=False, maiuscole_pulsanti=True)
C = K.c
MONO = 'Martian Mono'
VOCI = [('map-pin', 'Vico Lungo Gelso 22 · Napoli', NEGOZI + '#bottega'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Fuorimisura', LOGO, 220, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO, '1500/283', 'Fuorimisura fa a Napoli felpe, magliette e jeans in una taglia sola: grande. Le '
                   'altre misure le fa la cintura, o il vento.',
                   [('I capi', CAPI), ('La taglia', TAGLIA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Vico Lungo Gelso 22, Napoli<br>Tutti i giorni 11 — 21', VOCI,
                   '© 2026 Fuorimisura · Napoli · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')],
                   larghezza=620)


# ── L'apertura: la parola più larga dello schermo ──
def apertura(t):
    m = K.mob
    livelli = [
        *K.veli('fm-v', 'basso', 34, 64, colore=C['nero'], forza=86, mobile_da=60),
        K.livello('fm-gigante', contenuto='FUORIMISURA', tag='h1', x=4, y=24, w=320, size=330, peso='900', colore=ACIDO,
                  fontFamily='Lexend Zetta', lineHeight=0.85, letterSpacing=-18, entra='fadeInRight', ritardo=200, durata=1200,
                  customCSS='white-space:nowrap;',
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 100, 'x': -3900}]},
                  responsive={'mobile': {'fontSize': m(170), 'y': 15, 'x': 4, 'width': 1000}}),
        K.livello('fm-riga', contenuto='TAGLIA UNICA · GRANDE · LE ALTRE LE FA LA CINTURA · TAGLIA UNICA · GRANDE ·', tag='p', x=-160,
                  y=58, w=300, size=40, peso='800', colore='#FFFFFF', fontFamily='Lexend Zetta', lineHeight=1.0, letterSpacing=-2,
                  entra='fadeInLeft', ritardo=500, durata=1000, customCSS='white-space:nowrap;',
                  scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0}, {'p': 100, 'x': 1800}]},
                  responsive={'mobile': {'fontSize': m(22), 'y': 37, 'x': -160, 'width': 500}}),
        K.livello('fm-o', contenuto='Fuorimisura · streetwear oversize · Napoli', x=4, y=74, w=50, size=13, peso='500',
                  colore=mix('#FFFFFF', 88), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=300,
                  responsive={'mobile': {'fontSize': m(10), 'y': 71, 'x': 6, 'width': 88}}),
        K.livello('fm-s', contenuto='Felpe come tendoni, magliette come lenzuola, jeans a paracadute. Una taglia sola: grande.', x=4,
                  y=78.5, w=40, size=18, peso='500', colore=mix('#FFFFFF', 92), lineHeight=1.5, ritardo=700,
                  responsive={'mobile': {'fontSize': m(14), 'y': 74, 'x': 6, 'width': 88}}),
        K.livello('fm-b', tipo='button', contenuto='I capi', x=62, y=80, w='auto', size=22, peso='800', colore=C['on_acc'],
                  bgColor=ACIDO, padding=26, borderRadius=0, letterSpacing=1, textTransform='uppercase', fontFamily='Lexend Zetta',
                  entra='fadeInUp', ritardo=900, buttonUrl=CAPI, hover={'bgColor': '#FFFFFF', 'duration': 200},
                  responsive={'mobile': {'y': 84, 'x': 6, 'fontSize': m(16)}}),
        K.livello('fm-l', tipo='button', contenuto='La taglia', x=76, y=80, w='auto', size=22, peso='800', colore='#FFFFFF',
                  bgColor='transparent', padding=26, borderRadius=0, borderWidth=3, borderColor='#FFFFFF', letterSpacing=1,
                  textTransform='uppercase', fontFamily='Lexend Zetta', entra='fadeInUp', ritardo=1000, buttonUrl=TAGLIA,
                  hover={'bgColor': '#FF4D2E', 'borderColor': '#FF4D2E', 'duration': 200},
                  responsive={'tablet': {'x': 79}, 'mobile': {'y': 84, 'x': 46, 'fontSize': m(16)}}),
        K.livello('fm-hint', contenuto='Scorrete: la parola non ci sta →', x=62, y=72, w=34, size=13, peso='500', colore=ACIDO,
                  fontFamily=MONO, ritardo=1200,
                  scrollMove={'inizio': 0, 'fine': 15, 'tappe': [{'p': 0, 'o': 100}, {'p': 100, 'o': 0}]},
                  responsive={'mobile': {'fontSize': m(10), 'y': 67, 'x': 6, 'width': 88}}),
    ]
    sl = [K.diapositiva('fm-1', K.sfondo_video(V_SKATE, overlay=0.25, colore=C['nero']), livelli, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1600, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def gigante(t, occhiello, righe, size=130, tagline=''):
    """Un titolo di sezione enorme (e sul telefono ancora grande)."""
    return K.sechead(t, occhiello, righe, size=size, size_mobile=46, tagline=tagline)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.one([gigante(t, 'Fuorimisura · Napoli, vico Lungo Gelso 22', ['Una taglia.', 'Grande.'], size=150)]),
        t.row([t.col([], wm='1-3', ws='1-1'),
               t.col([K.text(t, 'Nei Quartieri le felpe le prestano i fratelli grandi. Noi le facciamo già così: maniche che '
                                'coprono le mani, cappucci dove ci si sta in due, jeans dove ci sta il vento. Una taglia sola, e '
                                'se è troppo grande va bene: è giusta.', size=20),
                      K.button(t, 'La taglia', TAGLIA, kind='pieno', pad_top=22)], wm='2-3', ws='1-1')],
              layout='custom', custom='33,67', gap=48, valign='start'),
    ], padding='large', width='expand', style={'padding_top': 150, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([gigante(t, 'I capi', ['Due per riga:', 'non ci stanno.'], size=110)]),
        t.one([M.capi(K, t, [
            (FEL[0], 'Felpe', 'Tendone', '', 'Cotone garzato, il cappuccio dove ci si sta in due.', '120 €', 'XXL'),
            (MAG[0], 'Magliette', 'Lenzuolo', '', 'Fino al ginocchio, le maniche ai gomiti.', '49 €', ''),
            (JEA[0], 'Jeans', 'Paracadute', '', 'Larghi quanto una gonna, la cintura in regalo.', '110 €', ''),
            (STR[1], 'Giacche', 'Coperta', '', 'Imbottita, si porta anche sul divano.', '190 €', 'nuova')], CAPI, colonne=2,
            proporzione='1/1')]),
    ], padding='large', width='expand', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, STR[3], 'XL', 'Grande <em>per tutti.</em>', 'La stessa felpa a chi è alto un metro e sessanta e a chi '
                          'è alto due: a uno arriva al ginocchio, all’altro alla vita.', 'I capi', CAPI, effetto='cover-h'))
    out.append(K.pannello(t, URB[2], 'XXL', 'Le maniche <em>sono guanti.</em>', 'Coprono le mani d’inverno, si arrotolano '
                          'd’estate. Nessuno ve le accorcia.', 'La taglia', TAGLIA, effetto='cover-h'))
    out.append(K.pannello(t, HIP[1], 'XXXL', 'Ci sta <em>il vento.</em>', 'I jeans Paracadute col vento si gonfiano: è voluto, '
                          'sono fatti così.', 'Negozi', NEGOZI, effetto='cover-h'))
    out.append(t.section([t.one([M.nastro(K, t, ['Taglia unica', 'Grande', 'Fuori misura', 'Napoli'], fondo=ACIDO, colore=C['nero'],
                                          size=150, altezza=210, velocita='26')], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([gigante(t, 'Il lookbook', ['Larghi,', 'per strada.'], size=110)]),
        P.fila_foto(K, t, [(FEL[3], 'La felpa nera oversize'), (JEA[2], 'I jeans larghi'), (MAG[4], 'La maglietta rossa lunga'),
                           (URB[4], 'La felpa per strada')]),
    ], padding='large', width='expand', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Una volta al mese,', 'scritta grande.'],
                         'I capi nuovi, i negozi, le serate al campetto. Una volta al mese.', bg=solid(C['sala'])))
    return out


def build_capi():
    t = Tpl(ID, 'capi')
    out = [K.intro(t, 'I capi', ['Otto capi,', 'una taglia.'],
                   'Tutti in taglia unica: grande. Sotto ogni capo c’è chi lo porta, e quanto è alto. I prezzi sono quelli '
                   'consigliati.', bg=K.photo_bg(MUR[2]), size=110)]
    out.append(t.section([
        t.one([gigante(t, 'Sopra', ['Felpe', 'e magliette.'], size=110)]),
        t.one([M.capi(K, t, [
            (FEL[0], 'Felpe', 'Tendone', '', 'Cappuccio dove ci si sta in due.', '120 €', 'XXL'),
            (FEL[4], 'Felpe', 'Tenda', '', 'Senza cappuccio, il collo largo.', '110 €', ''),
            (MAG[0], 'Magliette', 'Lenzuolo', '', 'Fino al ginocchio.', '49 €', ''),
            (MAG[2], 'Magliette', 'Bandiera', '', 'Verde, le maniche ai gomiti.', '49 €', '')], NEGOZI, colonne=2, proporzione='1/1')]),
    ], padding='large', width='expand', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([gigante(t, 'Sotto', ['Jeans', 'e il resto.'], size=110)]),
        t.one([M.capi(K, t, [
            (JEA[0], 'Jeans', 'Paracadute', '', 'Larghi come una gonna.', '110 €', ''),
            (JEA[4], 'Jeans', 'Sacco', '', 'Col cavallo basso, la cintura in regalo.', '115 €', ''),
            (STR[1], 'Giacche', 'Coperta', '', 'Imbottita, da divano.', '190 €', 'nuova'),
            (SNE[1], 'Scarpe', 'Barca', '', 'Due numeri in più, i lacci lunghi un metro.', '140 €', 'ultime')], NEGOZI, colonne=2,
            proporzione='1/1')]),
    ], bg=solid(C['sala']), padding='large', width='expand', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_BALLO, 'Si balla', 'Si muove <em>tutto.</em>',
                             'Una felpa grande si muove un attimo dopo chi la porta: è il bello.', overlay=22))
    return out


def build_taglia():
    t = Tpl(ID, 'la-taglia')
    out = [K.intro(t, 'La taglia', ['Unica.', 'Grande.'],
                   'Abbiamo provato a fare la S. Era troppo piccola. Così facciamo una taglia sola, e la cintura, il nodo e le '
                   'maniche arrotolate fanno le altre.', bg=K.video_bg(V_PIAZZA), size=120)]
    out.append(t.section([
        t.one([gigante(t, 'Come si porta', ['Tre trucchi,', 'nessuna sarta.'], size=110)]),
        t.one([P.passi(K, t, [('01', 'La cintura', 'I jeans Paracadute si stringono in vita: la cintura è in regalo, lunga un '
                                                    'metro e mezzo.'),
                              ('02', 'Il nodo', 'La maglietta Lenzuolo si annoda sul fianco: diventa corta quanto volete.'),
                              ('03', 'Il risvolto', 'Le maniche si arrotolano: due giri d’estate, nessuno d’inverno.')],
                         colonne=3, titolo=34)]),
    ], padding='large', width='expand', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([gigante(t, 'In bottega', ['Chi taglia', 'grande.'], size=110)]),
        K.squadra(t, [(HIP[0], 'Gennaro Esposito', 'Fondatore', 'Portava le felpe del fratello: ha deciso di farle già così.',
                       'center 25%'),
                      (URB[1], 'Assunta Russo', 'Il taglio', 'Taglia con le forbici del nonno, che faceva le vele.', 'center 25%'),
                      (STR[4], 'Kwame Boateng', 'La strada', 'Prova i capi al campetto: se si gioca bene, va.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='expand', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, CAM[1], 'Il campetto da basket visto dall’alto', ratio='1/1')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho comprato la felpa Tendone per me. Adesso la porta mia madre, mio padre e il cane. A '
                                        'turno.', 'Ciro, Napoli', size='44')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='expand', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarli,', 'larghi.'],
                   'Nei negozi c’è uno specchio grande il doppio, per vederli tutti. Ecco dove, e la bottega ai Quartieri.',
                   bg=K.photo_bg(MUR[0]), size=110)]
    out.append(M.negozi(K, t, [
        ('Napoli', 'Bottega Fuorimisura', 'vico Lungo Gelso 22'), ('Napoli', 'Largo', 'via Toledo 210'),
        ('Roma', 'Taglia Unica', 'via del Pigneto 101'), ('Milano', 'Oversize', 'via Padova 140'),
        ('Bari', 'XXL', 'via Manzoni 60'), ('Palermo', 'Lenzuolo', 'via Maqueda 300'),
        ('Marsiglia', 'Trop Grand', 'cours Julien 31'), ('Londra', 'Too Big', 'Brick Lane 140')],
        nota='La bottega è aperta tutti i giorni, anche la domenica.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('number', 'Quanto siete alti (cm)', 'altezza'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Siete troppo', 'piccoli? No.'],
                                'Diteci quanto siete alti: vi diciamo dove vi arriva la felpa. Anche per vendere Fuorimisura.',
                                VOCI, campi, 'Fuorimisura — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['Ai Quartieri,', 'col campetto dietro.'], size=60, layout='stack',
                                size_mobile=36),
                      K.specs(t, [('Orari', 'tutti i giorni 11 — 21'), ('Lo specchio', 'grande il doppio'),
                                  ('In metro', 'Toledo, cinque minuti a piedi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Fuorimisura · bottega', scuro=True, hex_marker=ACIDO)], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='bottega', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · troppo piccola', color=ACIDO, align='center'),
                       K.headline(t, 'Questa pagina <em>era una S.</em>', size=110, tag='h1', align='center', pad_bottom=20,
                                  accento=ACIDO),
                       K.text(t, 'Non l’abbiamo trovata: era troppo piccola. Qui facciamo solo il grande.', size=18, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_MURO, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'La parola che non ci sta',
    ['moda', 'streetwear', 'oversize', 'marchio', 'napoli', 'scuro', 'slider pro', 'scorrimento'],
    'Un marchio di streetwear oversize di Napoli, in una taglia sola: grande, e un sito dove tutto è troppo grande. In '
    'apertura la parola FUORIMISURA è più larga dello schermo e passa di lato scorrendo; titoli enormi, schede due per '
    'riga, pannelli che arrivano di lato e un nastro alto come un piano. Il cursore è XXL.',
    [('I capi', CAPI), ('La taglia', TAGLIA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('capi', 'I capi', 'capi'), ('la-taglia', 'La taglia', 'la-taglia'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Outfit\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Lexend Zetta\', system-ui, sans-serif',
    '\'Martian Mono\', ui-monospace, Menlo, monospace', ['Outfit', 'Lexend Zetta', 'Martian Mono'], corsivo_di=[],
    peso_titoli='800', raggio=0, grana=5, titoli_line='0.92', titoli_spazio='-0.06em')
THEME['cursor']['style'] = 'xxl'
THEME['cursor']['ring_color'] = ACIDO
THEME['cursor']['dot_color'] = '#111111'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'capi.json': build_capi(), 'la-taglia.json': build_taglia(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
