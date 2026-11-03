# -*- coding: utf-8 -*-
"""Genera il tema «Scroscio» (id scroscio): un marchio di Trieste di impermeabili, cerate e stivali da portare quando
piove davvero, e con la bora.

Su tutto il sito piove: le Particelle «Pioggia» su tutta la pagina (1.4.957), gocce lunghe e inclinate dal vento che
cadono sopra ogni sezione; il puntatore è un ombrello a cupola (il cursore nuovo) e sotto la cupola non piove: le gocce
schizzano sul bordo e cadono ai lati. Apertura: tre diapositive di pioggia che arrivano dall'alto («Scorri in giù»), il
titolo enorme in cima e il resto in fondo.

Tema scuro, ardesia bagnata, giallo cerata e azzurro. Titoli in Epilogue corsivo, testo in Red Hat Text, dati in Overpass
Mono. Logo: la goccia gialla con le righe di pioggia e SCROSCIO inclinato. Cursore: l'ombrello a cupola.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'scroscio'
NAME = 'Scroscio'

HEX = {
    'primary': '#FFD400', 'primary_contrast': '#12181F', 'secondary': '#4FA3D9', 'secondary_contrast': '#12181F',
    'accent': '#6FB7E8', 'dark': '#0A0F14', 'light': '#FFFFFF', 'muted': '#1A222C', 'muted_contrast': '#E6EEF5',
    'text': '#E6EEF5', 'text_muted': '#A9B8C6', 'background': '#12181F', 'border': '#2A3644', 'link': '#FFD400',
}
GIALLO = '#FFD400'

HOME, CAPI, PIOGGIA, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:impermeabili}}', '{{PAGINA:la-pioggia}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@scroscio.example', '+39 040 000 7788', 'tel:+390400007788'
LAT, LNG = '45.6500', '13.7680'

V_TAXI, V_NOTTE, V_LUCI, V_CANI, V_PANCHINA, V_VETRO, V_ARCOBALENO, V_TELO, V_OMBRELLI = (
    'pexels-video-3037170', 'pexels-video-3037497', 'pexels-video-6470925', 'pexels-video-8498887', 'pexels-video-8499358',
    'pexels-video-15454042', 'pexels-video-5658999', 'pexels-video-32062391', 'pexels-video-32502252')
VIDEOS = [V_TAXI, V_NOTTE, V_LUCI, V_CANI, V_PANCHINA, V_VETRO, V_ARCOBALENO, V_TELO, V_OMBRELLI]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
IMP = f('30598069', '17540090', '18500130', '15063531', '37242408', '8499234', '7578674')
GIA = f('14753790', '7578749', '14753898', '17540089', '17081924')
STI = f('12932484', '12932482', '5792942', '8499244', '14433788', '3036397')
OMB = f('5166265', '15171377', '1211847', '19116705', '19116710', '30504807')
GIC = f('16701711', '17540101', '31897362', '14753847', '14748078')
POZ = f('30313007', '2422896', '3315791', '33394732')
CIT = f('28993172', '31292495', '14116120', '14361923')
BAM = f('7880432', '7880431', '7880430', '7880437')
GOC = f('40097335', '371717', '39662939', '9853090')
CIE = f('31130767', '13519434', '19187094')
LOGO = 'logo-scroscio-chiaro.png'
PHOTOS = IMP + GIA + STI + OMB + GIC + POZ + CIT + BAM + GOC + CIE
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO, 'logo-scroscio-scuro.png'}

K = Kit(ID, MEDIA, raggio=6, peso_titoli='800', corsivo=True, maiuscole_pulsanti=True)
C = K.c
MONO = 'Overpass Mono'
VOCI = [('map-pin', 'Riva del Mandracchio 4 · Trieste', NEGOZI + '#bottega'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def pioggia(t):
    """La pioggia su tutta la pagina, col puntatore che fa da ombrello (raggio 48, quello del cursore a cupola)."""
    return t.classic('particlefx', {'scope': 'page', 'preset': 'rain', 'count': 140, 'speed': 1.1, 'size': 7, 'wind': 0.7,
                                    'gravity': 1.3, 'interact_on_hover': True, 'umbrella_size': 48, 'palette_1': '#BFD7EA',
                                    'palette_2': '#9FC3E0', 'palette_3': '#E6F0F8', 'palette_4': '', 'palette_5': '',
                                    'particle_opacity': 55})


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Scroscio', LOGO, 200, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO, '1255/229', 'Scroscio cuce a Trieste impermeabili e cerate per chi esce quando piove, e con la '
                   'bora. Si provano sotto la doccia della bottega.',
                   [('Gli impermeabili', CAPI), ('La pioggia', PIOGGIA), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Riva del Mandracchio 4, Trieste<br>Aperto anche quando piove: soprattutto', VOCI,
                   '© 2026 Scroscio · Trieste · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('youtube', 'https://youtube.com')],
                   larghezza=480)


# ── L'apertura: la pioggia che arriva dall'alto ──
def apertura(t):
    m = K.mob

    def acquazzone(sid, r1, r2, sotto, video):
        return K.diapositiva(sid, K.sfondo_video(video, overlay=0.2, colore=C['nero']), [
            *K.veli(sid + '-v', 'alto', 44, 70, colore=C['nero'], forza=78, mobile_alto=30),
            *K.veli(sid + '-w', 'basso', 22, 40, colore=C['nero'], forza=84, mobile_da=70),
            K.livello(sid + '-o', contenuto='Scroscio · impermeabili · Trieste', x=6, y=13, w=50, size=13, peso='500',
                      colore=mix('#FFFFFF', 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                      responsive={'mobile': {'fontSize': m(10), 'y': 6, 'x': 6, 'width': 88}}),
            K.livello(sid + '-t1', contenuto=r1, tag='h1', x=5, y=16.5, w=90, size=200, peso='900', fontStyle='italic',
                      colore='#FFFFFF', lineHeight=0.9, letterSpacing=-6, entra='slideInDown', ritardo=300, durata=1100,
                      responsive={'mobile': {'fontSize': m(76), 'y': 9.5, 'x': 5, 'width': 92}}),
            K.livello(sid + '-t2', fontFamily='Epilogue', contenuto=r2, tag='p', x=6, y=43, w=60, size=64, peso='800',
                      fontStyle='italic', colore=GIALLO, lineHeight=1.0, letterSpacing=-2, entra='slideInDown', ritardo=600,
                      durata=1100, responsive={'mobile': {'fontSize': m(34), 'y': 19.5, 'x': 6, 'width': 90}}),
            K.livello(sid + '-s', contenuto=sotto, x=52, y=80, w=42, size=17, peso='500', colore=mix('#FFFFFF', 92), lineHeight=1.55,
                      ritardo=900, textAlign='right', responsive={'mobile': {'fontSize': m(13), 'y': 77, 'x': 6, 'width': 88,
                                                                          'textAlign': 'left'}}),
            K.livello(sid + '-b', tipo='button', contenuto='Gli impermeabili', x=6, y=85, w='auto', size=12, peso='700',
                      colore=C['on_acc'], bgColor=GIALLO, padding=16, borderRadius=6, letterSpacing=2, textTransform='uppercase',
                      entra='fadeInUp', ritardo=1100, buttonUrl=CAPI, hover={'bgColor': '#FFFFFF', 'duration': 250},
                      responsive={'mobile': {'y': 86, 'x': 6, 'fontSize': m(11)}}),
            K.livello(sid + '-l', tipo='button', contenuto='Ombrello: il mouse', x=21.5, y=85, w='auto', size=12, peso='700',
                      colore='#FFFFFF', bgColor='transparent', padding=16, borderRadius=6, borderWidth=1,
                      borderColor=mix('#FFFFFF', 60), letterSpacing=2, textTransform='uppercase', entra='fadeInUp', ritardo=1200,
                      buttonUrl=PIOGGIA, hover={'bgColor': mix('#FFFFFF', 14), 'duration': 250},
                      responsive={'tablet': {'x': 26}, 'mobile': {'y': 86, 'x': 52, 'fontSize': m(11)}}),
        ], durata=7500)

    sl = [acquazzone('sc-1', 'Piove.', 'Meno male.', 'Impermeabili e cerate per chi esce lo stesso. E qui piove davvero: il '
                     'vostro mouse è l’ombrello.', V_TAXI),
          acquazzone('sc-2', 'Fuori.', 'Lo stesso.', 'La cerata gialla col cappuccio che non vola via, nemmeno con la bora.',
                     V_CANI),
          acquazzone('sc-3', 'Scroscia.', 'E allora?', 'Cuciture nastrate, tasche che non si riempiono, maniche che non '
                     'gocciolano sulle mani.', V_LUCI)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='slideDown', durata_trans=1000,
                       autoplaySpeed=7500, showArrows=False, showDots=True, dotStyle='dash', showProgressBar=True, parallax=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    eroe = K.apertura(t, apertura(t))
    eroe['children'][0]['children'][0]['children'].append(pioggia(t))
    out = [eroe]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Scroscio · Trieste, riva del Mandracchio', ['Quando piove,', 'si esce.'], size=72,
                                layout='stack', size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'A Trieste la pioggia arriva di traverso, con la bora. Le nostre cerate le proviamo lì: sulla '
                                'riva, a novembre. Tre strati, cuciture nastrate, un cappuccio che si stringe con una mano. '
                                'Provate a muovere il mouse: sotto l’ombrello non piove.', size=17),
                      K.button(t, 'La pioggia', PIOGGIA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli impermeabili', ['Quattro modi', 'di bagnarsi poco.'], size=60)]),
        t.one([M.capi(K, t, [
            (GIA[0], 'Cerate', 'Bora', '', 'Gialla, il cappuccio che non vola via.', '190 €', 'nuova'),
            (IMP[2], 'Trench', 'Molo', '', 'Cotone cerato, lungo al ginocchio.', '260 €', ''),
            (GIC[1], 'Giacche', 'Scroscio', '', 'Tre strati, cuciture nastrate.', '230 €', ''),
            (STI[1], 'Stivali', 'Pozza', '', 'Gomma naturale, la suola che non scivola.', '89 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Piove', 'Meno male', 'Cuciture nastrate', 'Cappuccio che non vola',
                                                 'Anche con la bora'], fondo=GIALLO, colore=C['nero'], size=58)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(K.pannello(t, POZ[0], 'Le pozzanghere', 'Saltateci <em>dentro.</em>', 'Gli stivali Pozza arrivano a metà '
                          'polpaccio: le pozzanghere di Trieste non li superano.', 'Gli stivali', CAPI, effetto='reveal-h'))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Bagnati,', 'contenti.'], size=60)]),
        P.fila_foto(K, t, [(GIA[1], 'La cerata gialla sotto la pioggia'), (IMP[4], 'L’impermeabile nel bosco'),
                           (BAM[0], 'I bambini con le cerate'), (GIC[3], 'La giacca blu in città')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Quando a Trieste', 'piove, vi scriviamo.'],
                         'Più o meno una volta al mese. Le cerate nuove, i negozi, e se domani serve l’ombrello.',
                         bg=solid(C['sala'])))
    return out


def build_impermeabili():
    t = Tpl(ID, 'impermeabili')
    intro = K.intro(t, 'Gli impermeabili', ['Otto capi', 'da pioggia vera.'],
                    'Cotone cerato, nylon a tre strati, gomma naturale. Si provano sotto la doccia della bottega. I prezzi sono '
                    'quelli consigliati.', bg=K.photo_bg(CIT[1]), size=96)
    intro['children'][0]['children'][0]['children'].append(pioggia(t))
    out = [intro]
    out.append(t.section([
        t.one([K.sechead(t, 'Cerate e trench', ['Il cotone', 'che non beve.'], size=60)]),
        t.one([M.capi(K, t, [
            (GIA[0], 'Cerate', 'Bora', '', 'Gialla, il cappuccio che non vola.', '190 €', 'nuova'),
            (GIA[3], 'Cerate', 'Faro', '', 'Arancio, le bande riflettenti.', '200 €', ''),
            (IMP[2], 'Trench', 'Molo', '', 'Cotone cerato, al ginocchio.', '260 €', ''),
            (IMP[0], 'Mantelle', 'Canale', '', 'Si porta sopra lo zaino.', '150 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Giacche e stivali', ['Tre strati,', 'zero gocce.'], size=60)]),
        t.one([M.capi(K, t, [
            (GIC[1], 'Giacche', 'Scroscio', '', 'Tre strati, cuciture nastrate.', '230 €', ''),
            (GIC[4], 'Giacche', 'Notte', '', 'Nera, riflettente al buio.', '240 €', ''),
            (STI[1], 'Stivali', 'Pozza', '', 'Gomma naturale, a metà polpaccio.', '89 €', ''),
            (BAM[2], 'Bambini', 'Pozzanghera', '', 'Cerata gialla dai 2 ai 10 anni.', '79 €', 'ultime')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_VETRO, 'Dalla finestra', 'Guardatela <em>da fuori.</em>',
                             'La pioggia è più bella vista da fuori. Con una cerata addosso.', overlay=20))
    return out


def build_pioggia():
    t = Tpl(ID, 'la-pioggia')
    intro = K.intro(t, 'La pioggia', ['Le proviamo', 'sulla riva.'],
                    'Ogni cerata, prima di uscire dalla bottega, passa dieci minuti sotto la doccia e una mattina di bora sul molo '
                    'Audace. Quelle che gocciolano dentro le rifacciamo.', bg=K.video_bg(V_NOTTE), size=96)
    intro['children'][0]['children'][0]['children'].append(pioggia(t))
    out = [intro]
    out.append(t.section([
        t.one([K.sechead(t, 'Come nasce', ['Tre strati,', 'una prova.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'il tessuto', GOC[1], 'Cerare', 'Cotone', 'cerato', 'Il cotone si passa nella cera calda: l’acqua scivola e '
             'il vapore esce.', '3', 'passate'),
            ('2', 'le cuciture', IMP[6], 'Nastrare', 'Ogni', 'cucitura', 'Dentro, su ogni cucitura, un nastro termosaldato: '
             'l’acqua non trova buchi.', '14', 'metri di nastro'),
            ('3', 'la prova', CIE[0], 'Provare', 'Sotto', 'la doccia', 'Dieci minuti di doccia e una mattina di bora: se '
             'gocciola, si rifà.', '10', 'minuti')], icona='cloud-rain')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi esce', 'quando piove.'], size=60)]),
        K.squadra(t, [(IMP[1], 'Irena Kos', 'Fondatrice', 'Ex marinaia: ha cucito la prima cerata per la sua barca.', 'center 25%'),
                      (GIC[0], 'Marko Vidali', 'Il taglio', 'Taglia i cappucci perché non volino con la bora.', 'center 25%'),
                      (IMP[5], 'Giada Sain', 'La prova', 'Prova ogni cerata sotto la doccia, cronometro in mano.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, OMB[2], 'Un ombrello sotto la pioggia', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho portato la cerata Bora sul molo Audace con la bora a cento all’ora. Il cappuccio è '
                                        'rimasto su. Io quasi no.', 'Davide, Trieste', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_negozi():
    t = Tpl(ID, 'negozi')
    intro = K.intro(t, 'I negozi', ['Dove provarli', 'sotto la doccia.'],
                    'In ogni negozio che ci vende c’è un ombrello per uscire a provarli. Nella bottega di Trieste, la doccia.',
                    bg=K.photo_bg(CIT[3]), size=96)
    intro['children'][0]['children'][0]['children'].append(pioggia(t))
    out = [intro]
    out.append(M.negozi(K, t, [
        ('Trieste', 'Bottega Scroscio', 'riva del Mandracchio 4'), ('Udine', 'Acquazzone', 'via Mercatovecchio 18'),
        ('Venezia', 'Acqua Alta', 'fondamenta della Misericordia 2560'), ('Milano', 'Temporale', 'via Savona 24'),
        ('Genova', 'Libeccio', 'via San Lorenzo 60'), ('Bergamo', 'Nuvola', 'via XX Settembre 70'),
        ('Lubiana', 'Naliv', 'Trubarjeva 20'), ('Amburgo', 'Schauer', 'Schanzenstraße 43')],
        nota='La bottega di Trieste è aperta anche quando piove: soprattutto.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Una cerata da riparare\nUna taglia\nVendere Scroscio\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Gocciola?', 'La rifacciamo.'],
                                'Per una cerata da riparare, una taglia, o per venderci nel vostro negozio.', VOCI, campi,
                                'Scroscio — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni, piova o no.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La bottega', ['Sulle rive,', 'col molo davanti.'], size=54, layout='stack', size_mobile=34),
                      K.specs(t, [('Orari', 'lunedì — sabato 9:30 — 19'), ('La doccia', 'per provare le cerate'),
                                  ('A piedi', 'da piazza Unità, due minuti')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Scroscio · bottega', scuro=True, hex_marker=GIALLO)], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='bottega', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di bagnarsi', ['Le domande', 'di chi esce lo stesso.'], [
        ('La cera si rinnova?', 'Sì, ogni due inverni: in bottega lo facciamo gratis, ci vuole un’ora.'),
        ('Si lavano in lavatrice?', 'Le giacche a tre strati sì, a trenta gradi. Le cerate solo con la spugna.'),
        ('Il cappuccio vola con la bora?', 'No: si stringe con una mano e ha la visiera rigida.'),
        ('Spedite?', 'No: si provano nei negozi. Il negozio più vicino si fa mandare la taglia.'),
    ], 'sc-faq', bg=False))
    return out


def build_404():
    t = Tpl(ID, '404')
    sez = t.section([
        K.centered(t, [K.label(t, 'Errore 404 · portata via dalla pioggia', color=GIALLO, align='center'),
                       K.headline(t, 'Questa pagina <em>se l’è portata via la bora.</em>', size=72, tag='h1', align='center',
                                  pad_bottom=20, accento=GIALLO),
                       K.text(t, 'La stiamo cercando in fondo al molo. Intanto, tornate all’asciutto.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_TELO, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})
    sez['children'][0]['children'][1]['children'].append(pioggia(t))
    return [sez]


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Piove su tutto il sito',
    ['moda', 'impermeabili', 'cerate', 'pioggia', 'marchio', 'trieste', 'scuro', 'particelle', 'slider pro'],
    'Un marchio di impermeabili e cerate di Trieste, e un sito su cui piove: gocce lunghe e inclinate dal vento cadono su '
    'tutte le pagine, e il puntatore è un ombrello a cupola sotto cui non piove. In apertura tre acquazzoni che arrivano '
    'dall’alto, il titolo enorme; poi le cerate, le pozzanghere, la prova sotto la doccia.',
    [('Gli impermeabili', CAPI), ('La pioggia', PIOGGIA), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('impermeabili', 'Gli impermeabili', 'impermeabili'), ('la-pioggia', 'La pioggia', 'la-pioggia'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Red Hat Text\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Epilogue\', system-ui, sans-serif',
    '\'Overpass Mono\', ui-monospace, Menlo, monospace', ['Red Hat Text', 'Epilogue', 'Overpass Mono'], corsivo_di=['Epilogue'],
    peso_titoli='800', raggio=6, grana=0, titoli_line='0.95', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'cupola'
THEME['cursor']['ring_color'] = '#E6F0F8'
THEME['cursor']['dot_color'] = GIALLO


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'impermeabili.json': build_impermeabili(), 'la-pioggia.json': build_pioggia(),
                        'negozi.json': build_negozi(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
