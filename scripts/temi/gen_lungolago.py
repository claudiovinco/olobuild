# -*- coding: utf-8 -*-
"""Genera il tema «Lungolago» (id lungolago): un’agenzia di case di pregio sul lago di Como: ville con la darsena, attici
sull’acqua, la discrezione per chi vende e la ricerca su misura per chi compra.

Tema scuro ed elegante, il lago di sera: blu notte, oro, avorio e un azzurro d’acqua. Titoli in Bodoni Moda (col
corsivo), testo in Jost, dati in Azeret Mono.

Apertura: «il riflesso». Sul video del lago fermo come uno specchio, al centro, il titolo e sotto la linea dell’acqua il
suo seguito capovolto, sfocato e mosso come un riflesso. Scorrendo il riflesso si ribalta in 3D attorno all’orizzonte
(un rotateX da 180° a 0°) e diventa leggibile: la seconda riga del titolo. Le increspature sotto la linea scorrono piano.
Cursore: il riflesso.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'lungolago'
NAME = 'Lungolago'

HEX = {
    'primary': '#C9A96A', 'primary_contrast': '#0E1A24', 'secondary': '#8FB8C9', 'secondary_contrast': '#0E1A24',
    'accent': '#E8DCC6', 'dark': '#08121A', 'light': '#F6F1E7', 'muted': '#0F1E2A', 'muted_contrast': '#EDE6D8',
    'text': '#EDE6D8', 'text_muted': '#A3B3BF', 'background': '#0B1721', 'border': '#22384A', 'link': '#C9A96A',
}

HOME, VILLE, SERVIZI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:le-ville}}', '{{PAGINA:servizi}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'riservato@lungolago.example', '+39 031 000 880', 'tel:+39031000880'
LAT, LNG = '45.9850', '9.2580'

V_SPECCHIO, V_VILLA, V_VILLA2, V_RIVA, V_PRUA, V_MOTOSCAFO, V_PONTILE, V_TRAMONTO = (
    'pexels-video-5383179', 'pexels-video-8436076', 'pexels-video-8436041', 'pexels-video-26799497', 'pexels-video-8123948',
    'pexels-video-38690468', 'pexels-video-12373255', 'pexels-video-24797167')
PHOTOS = [
    'pexels-32716819.jpg', 'pexels-28267040.jpg', 'pexels-36610234.jpg', 'pexels-20927257.jpg', 'pexels-13777767.jpg',
    'pexels-14682511.jpg', 'pexels-29991356.jpg', 'pexels-10984784.jpg', 'pexels-33386371.jpg', 'pexels-16125979.jpg',
    'pexels-18155694.jpg', 'pexels-6294062.jpg', 'pexels-16954120.jpg', 'pexels-40229377.jpg', 'pexels-6872499.jpg',
    'pexels-28210422.jpg', 'pexels-4183590.jpg', 'pexels-28226576.jpg', 'pexels-6527069.jpg', 'pexels-6527068.jpg',
    'pexels-7546595.jpg', 'pexels-6580241.jpg', 'pexels-7546600.jpg', 'pexels-30816307.jpg', 'pexels-30816313.jpg',
    'pexels-27626180.jpg', 'pexels-36676787.jpg', 'pexels-38115059.jpg', 'pexels-35030159.jpg', 'pexels-34017020.jpg',
    'pexels-40100308.jpg', 'pexels-31523837.jpg', 'pexels-33577215.jpg', 'pexels-29106395.jpg', 'pexels-32942929.jpg',
    'pexels-34435116.jpg', 'pexels-13777755.jpg', 'pexels-1036914.jpg', 'pexels-31001488.jpg', 'pexels-37286919.jpg',
    'pexels-20451153.jpg', 'pexels-33073678.jpg', 'pexels-8062305.jpg',
]
VIDEOS = [V_SPECCHIO, V_VILLA, V_VILLA2, V_RIVA, V_PRUA, V_MOTOSCAFO, V_PONTILE, V_TRAMONTO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
LAGO, AVORIO = C['sec'], C['acc2']
MONO = 'Azeret Mono'
TITOLI = 'Bodoni Moda'
VOCI = [('map-pin', 'Lungolago Mafalda di Savoia 7 · Bellagio', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Lungolago', logo_size='24', overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Lungolago', 'Case di pregio sul lago di Como, da Bellagio a Tremezzo. Trattiamo poche case alla volta, '
                   'con la riservatezza che chiedono.',
                   [('Le ville', VILLE), ('I servizi', SERVIZI), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Lo studio', 'Su appuntamento, tutti i giorni<br>Visite anche dal lago', VOCI,
                   '© 2026 Lungolago · Bellagio · Iscritta al Ruolo agenti immobiliari', [('instagram', 'https://instagram.com')],
                   marchio_size=150, fondo=C['nero'])


# ── L'apertura: il riflesso ──
def apertura(t):
    m = K.mob
    ORIZ, ORIZM = 50.0, 40.0          # la linea dell'acqua (y %), computer e telefono
    gira = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'rx': 180, 'o': 45}, {'p': 10, 'rx': 180, 'o': 45},
                                                {'p': 62, 'rx': 0.01, 'o': 100}, {'p': 100, 'rx': 0.01, 'o': 100}]}
    increspature = []
    for k, (y, w, dur) in enumerate([(4.5, 22, 5200), (8, 30, 6400), (12, 16, 4600), (16.5, 26, 7000)]):
        increspature.append(K.livello('lu-onda%d' % k, tipo='shape', x=50 - w / 2 + (k % 2) * 4 - 2, y=ORIZ + y, w=w, h=0.25,
                                      bgColor=mix(C['chiaro'], 30), borderRadius=999, entra='fadeIn', ritardo=900 + k * 200,
                                      durata=1200, animLoop='waveSmallLeft' if k % 2 else 'waveSmallRight',
                                      animLoopDuration=dur,
                                      scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 40, 'o': 0},
                                                                                     {'p': 100, 'o': 0}]},
                                      responsive={'mobile': {'x': 50 - w * 1.4 / 2, 'y': ORIZM + y * 0.5, 'width': w * 1.4,
                                                             'height': 0.15}}))
    livelli = [
        K.livello('lu-o', contenuto='Lungolago · case di pregio · lago di Como', x=25, y=20, w=50, size=12, peso='500',
                  colore=mix(C['chiaro'], 86), align='center', letterSpacing=4, textTransform='uppercase', fontFamily=MONO,
                  ritardo=200, responsive={'mobile': {'fontSize': m(10), 'y': 24, 'x': 5, 'width': 90}}),
        K.livello('lu-t1', contenuto='Case che guardano', tag='h1', x=10, y=ORIZ - 16, w=80, size=100, peso='400',
                  colore=C['chiaro'], align='center', lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=400,
                  durata=1300, responsive={'mobile': {'fontSize': m(34), 'y': ORIZM - 6.5, 'x': 4, 'width': 92}}),
        K.livello('lu-linea', tipo='shape', x=20, y=ORIZ, w=60, h=0.2, bgColor=mix(C['acc'], 80), entra='fadeIn', ritardo=700,
                  durata=1200, responsive={'mobile': {'x': 10, 'y': ORIZM, 'width': 80, 'height': 0.12}}),
        # il seguito, capovolto sotto la linea come un riflesso: si ribalta sul posto (attorno al suo centro) e si legge
        K.livello('lu-t2', fontFamily=TITOLI, contenuto='<em>il lago, ogni mattina.</em>', tag='p', x=10, y=ORIZ + 0.6, w=80,
                  h=15, size=100, peso='400', colore=C['acc'], align='center', lineHeight=1.0, letterSpacing=-2,
                  entra='fadeIn', ritardo=900, durata=1600, scrollMove=gira,
                  responsive={'mobile': {'fontSize': m(34), 'y': ORIZM + 0.4, 'x': 4, 'width': 92, 'height': 6}}),
        K.livello('lu-s', contenuto='Ville con la darsena, attici sull’acqua, case di famiglia da Bellagio a Tremezzo. Poche '
                                    'alla volta, con discrezione.', x=30, y=72, w=40, size=16, peso='400',
                  colore=mix(C['chiaro'], 86), align='center', lineHeight=1.6, ritardo=1100,
                  responsive={'mobile': {'fontSize': m(13), 'y': 54, 'x': 8, 'width': 84}}),
        K.livello('lu-b', tipo='button', contenuto='Le ville', x=38.5, y=84, w='auto', size=12, peso='500',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=0, letterSpacing=3, textTransform='uppercase',
                  entra='fadeInUp', ritardo=1300, buttonUrl=VILLE, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'y': 61, 'x': 8, 'fontSize': m(11)}}),
        K.livello('lu-l', tipo='button', contenuto='Vendere con noi', x=49, y=84, w='auto', size=12, peso='500',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=0, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), letterSpacing=3, textTransform='uppercase', entra='fadeInUp', ritardo=1400,
                  buttonUrl=SERVIZI, hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'y': 61, 'x': 46, 'fontSize': m(11)}}),
    ]
    # il velo: una fascia scura dietro i testi che sfuma verso l'alto e verso il basso (due metà); il lago resta pieno
    fascia = lambda lid, y, h, ang, ym, hm: K.livello(lid, tipo='shape', x=0, y=y, w=100, h=h, entra='fadeIn', ritardo=0,
                                                      durata=600, shapeGradient={'angle': ang, 'from': mix(C['nero'], 74),
                                                                                 'to': mix(C['nero'], 0), 'fromStop': 25},
                                                      responsive={'mobile': {'x': 0, 'y': ym, 'width': 100, 'height': hm}})
    veli = [fascia('lu-velo1', 14, 36, 0, 18, 22), fascia('lu-velo2', 50, 44, 180, 40, 30)]
    sl = [K.diapositiva('lu-1', K.sfondo_video(V_SPECCHIO, overlay=0.24, colore=C['nero']), veli + increspature + livelli,
                        durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       scrollTimeline=True, scrollTimelineDistance=1300, scrollStep=False,
                       heightMobile={'mode': 'px', 'value': 1400})


VILLE_L = [  # nome, sotto, descrizione, foto
    ('Villa delle Ortensie', 'Bellagio · 620 m² · darsena', 'Una villa dell’Ottocento con il parco in discesa fino '
     'all’acqua, la darsena coperta e otto camere.', 'pexels-36610234.jpg'),
    ('L’attico sull’acqua', 'Tremezzo · 240 m² · terrazza', 'L’ultimo piano di un palazzo del Novecento, con la terrazza '
     'di 120 m² sul lago.', 'pexels-6580241.jpg'),
    ('La casa del pescatore', 'Varenna · 140 m² · pontile', 'Una casa stretta e alta, i piedi nell’acqua, il pontile '
     'privato e la barca di legno.', 'pexels-18155694.jpg'),
    ('Villa sul promontorio', 'Lenno · 900 m² · piscina', 'Il promontorio intero: la villa, la limonaia, la piscina a '
     'sfioro sul lago.', 'pexels-6872499.jpg'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lungolago · Bellagio', ['Poche case,', 'tutte sull’acqua.'], size=56, layout='stack',
                                size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Non pubblichiamo tutte le case che trattiamo: alcune famiglie preferiscono che restino '
                                'riservate. Chi cerca ci racconta cosa vorrebbe, e noi gli apriamo le porte giuste, anche dal '
                                'lago.', size=17), K.button(t, 'I servizi', SERVIZI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le ville', ['Quattro case', 'di questa stagione.'], size=56,
                         tagline='Passate su una villa: si affaccia il lago.')]),
        t.one([P.elenco_hover(K, t, VILLE_L, VILLE, size=48)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_VILLA, 'Dal lago', 'Le ville <em>si guardano dall’acqua.</em>',
                             'La prima visita la facciamo in barca: è da lì che sono state pensate.', overlay=26))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-33577215.jpg', 'Un motoscafo di legno sul lago', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ci hanno fatto vedere la casa al tramonto, dalla barca, con le finestre accese. Abbiamo '
                                          'deciso prima di scendere a terra.', 'Una famiglia di Monaco, Tremezzo 2025',
                                  size='40')], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72,
              valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lago', ['I paesi', 'dove lavoriamo.'], size=56)]),
        t.one([P.righe(K, t, [('Bellagio', 'La punta del lago', 'ville dell’Ottocento, parchi', '12 case'),
                              ('Tremezzo', 'La riva delle ville', 'attici, palazzi sul lungolago', '8 case'),
                              ('Varenna', 'Il borgo dei pescatori', 'case strette, pontili', '6 case'),
                              ('Lenno', 'Il golfo di Venere', 'promontori, limonaie', '4 case')], CONTATTI + '#scrivi',
                       titolo=40)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_PONTILE, 'Chi vende', 'Con discrezione, <em>senza cartelli.</em>',
                          'Una valutazione riservata, le visite solo con chi ha già i mezzi.', 'Vendere con noi', SERVIZI,
                          overlay=18, video=True))
    return out


def build_ville():
    t = Tpl(ID, 'le-ville')
    out = [K.intro(t, 'Le ville', ['Le case che si possono', 'mostrare.'],
                   'Qui ci sono quelle che i proprietari ci hanno permesso di pubblicare. Per le altre, scriveteci cosa '
                   'cercate.', bg=K.video_bg(V_VILLA2), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'In vendita', ['Girate le carte:', 'dietro i numeri.'], size=56)]),
        P.carte(K, t, [
            ('pexels-20927257.jpg', 'Villa delle Ortensie', 'Bellagio', '620 m², otto camere, parco di un ettaro, darsena '
             'coperta. Prezzo riservato.'),
            ('pexels-7546595.jpg', 'L’attico sull’acqua', 'Tremezzo', '240 m², terrazza di 120 m², ascensore privato. '
             '4.800.000 €.'),
            ('pexels-16954120.jpg', 'La casa del pescatore', 'Varenna', '140 m² su quattro piani, pontile e barca. '
             '1.950.000 €.'),
            ('pexels-28210422.jpg', 'Villa sul promontorio', 'Lenno', '900 m², limonaia, piscina a sfioro. Prezzo '
             'riservato.'),
        ], altezza='420'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Gli interni', ['Le stanze', 'che guardano il lago.'], size=56)]),
        P.fila_foto(K, t, [('pexels-6527069.jpg', 'Un salone con le vetrate sul lago'), ('pexels-30816307.jpg', 'Una camera '
                                                                                                             'con la vista'),
                           ('pexels-13777755.jpg', 'La terrazza sul lago'), ('pexels-38115059.jpg', 'Il giardino '
                                                                                                    'all’italiana')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MOTOSCAFO, 'La darsena', 'Si arriva <em>in barca.</em>',
                             'Metà delle ville che vendiamo ha la sua darsena: la barca resta, se volete.', overlay=26))
    return out


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Per chi vende,', 'per chi cerca.'],
                   'Seguiamo poche case alla volta e ognuna dall’inizio alla fine: la stima, le foto, le visite, il '
                   'notaio, e dopo anche chi apre la casa d’estate.', bg=K.photo_bg('pexels-29991356.jpg'), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Per chi vende', 'La stima', 'riservata',
         '<p>Una valutazione scritta, con le vendite vere degli ultimi anni sul lago.</p><ul><li>Senza annunci pubblici, se '
         'volete</li><li>Le visite solo con acquirenti verificati</li></ul>', 'pexels-14682511.jpg'),
        ('Per chi cerca', 'La ricerca', 'su misura',
         '<p>Ci dite come vorreste vivere sul lago, noi vi mostriamo anche le case che non sono in vendita… ancora.</p><ul>'
         '<li>Le visite in barca</li><li>Un interprete se serve</li></ul>', 'pexels-33386371.jpg'),
        ('Dopo il rogito', 'La casa', 'aperta',
         '<p>Chi apre la villa a maggio, il giardiniere, la barca in rimessaggio: vi presentiamo le persone giuste.</p><ul>'
         '<li>Ristrutturazioni con architetti del lago</li></ul>', 'pexels-40100308.jpg'),
    ])], anim=None)], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 80}))
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Quattro tempi,', 'nessuna fretta.'], size=56)]),
        t.one([P.passi(K, t, [('I', 'L’incontro', 'In studio o a casa vostra, anche in videochiamata.'),
                              ('II', 'La stima', 'Scritta, riservata, motivata casa per casa.'),
                              ('III', 'Le visite', 'Poche, con persone già verificate, anche dal lago.'),
                              ('IV', 'Il notaio', 'Seguiamo carte, traduzioni e consegna delle chiavi.')],
                       numero_font=MONO)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Una telefonata,', 'poi un caffè sul lago.'],
                   'Lo studio è sul lungolago di Bellagio. Riceviamo su appuntamento, anche la domenica.',
                   bg=K.photo_bg('pexels-1036914.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Lo studio', ['Chi vi', 'accompagna.'], size=56)]),
        K.squadra(t, [
            ('pexels-20451153.jpg', 'Beatrice Lario', 'Fondatrice', 'Ville storiche e vendite riservate. Parla inglese, '
             'tedesco e francese.', 'center 25%'),
            ('pexels-33073678.jpg', 'Giulia Verzi', 'Ricerca su misura', 'Segue chi cerca casa sul lago, dalla prima '
             'telefonata al rogito.', 'center 25%'),
        ], ratio='4/5', size='360', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Desiderate', 'cosa', opzioni='Comprare\nVendere\nUna valutazione riservata'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['In riservatezza,', 'vi rispondiamo noi.'],
                                'Rispondiamo entro un giorno, dal numero dello studio.', VOCI, campi, 'Lungolago — contatto',
                                'Invia', 'Grazie. Vi rispondiamo entro un giorno.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lo studio', ['Sul lungolago', 'di Bellagio.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Su appuntamento', 'tutti i giorni'), ('In battello', 'imbarcadero di Bellagio, due '
                                                                                         'minuti'),
                                  ('Da Milano', 'un’ora e un quarto in auto')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Lungolago · studio', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · acqua alta', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa riva <em>non esiste.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'La pagina è andata al largo. Torniamo al pontile.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_TRAMONTO, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Immobiliare', 'Il riflesso nel lago',
    ['immobili di pregio', 'ville', 'lago di como', 'luxury real estate', 'bellagio', 'scuro', 'slider pro', '3d'],
    'Case di pregio sul lago di Como, blu notte e oro: in apertura, sul video del lago a specchio, il titolo e sotto la '
    'linea dell’acqua il suo seguito capovolto come un riflesso, che scorrendo si ribalta in 3D e diventa leggibile; le '
    'ville da girare, la ricerca su misura, la vendita riservata e lo studio.',
    [('Le ville', VILLE), ('I servizi', SERVIZI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('le-ville', 'Le ville', 'le-ville'), ('servizi', 'I servizi', 'servizi'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Jost\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bodoni Moda\', Georgia, serif',
    '\'Azeret Mono\', ui-monospace, Menlo, monospace', ['Jost', 'Bodoni Moda', 'Azeret Mono'], corsivo_di=['Bodoni Moda'],
    peso_titoli='400', raggio=0, grana=3, titoli_line='1.02', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'riflesso'
THEME['cursor']['ring_color'] = HEX['secondary']
THEME['cursor']['dot_color'] = HEX['accent']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'le-ville.json': build_ville(), 'servizi.json': build_servizi(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
