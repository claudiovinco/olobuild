# -*- coding: utf-8 -*-
"""Genera il tema «Respiro» (id respiro): uno studio di psicoterapia a Bologna: colloqui individuali, di coppia e
online, i gruppi per l’ansia, il primo colloquio per conoscersi.

Tema scuro, il blu della sera: notte profonda, lavanda, sabbia e un verde d’acqua. Titoli in Newsreader (col corsivo),
testo in Karla, dati in IBM Plex Mono.

Apertura: «il respiro». Sul video dell’acqua scura, al centro, tre anelli sottili che girano lenti in 3D, ognuno su un
asse diverso, come un giroscopio; dentro, un disco di luce che respira (otto secondi: si allarga inspirando, si stringe
espirando) e le parole «Inspira» ed «Espira» che si danno il cambio. Scorrendo via gli anelli si allargano e svaniscono.
Cursore: il respiro.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'respiro'
NAME = 'Respiro'

HEX = {
    'primary': '#B9A7F2', 'primary_contrast': '#15132C', 'secondary': '#F2C9A0', 'secondary_contrast': '#15132C',
    'accent': '#8FD1C4', 'dark': '#0B0D1D', 'light': '#F4F1FA', 'muted': '#171A33', 'muted_contrast': '#E9E6F5',
    'text': '#ECEAF6', 'text_muted': '#A9A8C2', 'background': '#10122A', 'border': '#2C2F4F', 'link': '#B9A7F2',
}

HOME, PERCORSI, NOI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:percorsi}}', '{{PAGINA:chi-siamo}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'ascolto@respiro.example', '+39 051 000 2290', 'tel:+390510002290'
LAT, LNG = '44.4920', '11.3460'

V_ACQUA, V_ACQUA2, V_NEBBIA, V_NEBBIA2, V_MARE, V_TENDA = ('pexels-video-1181911', 'pexels-video-31320313',
                                                           'pexels-video-4296589', 'pexels-video-3615892',
                                                           'pexels-video-37954399', 'pexels-video-854116')
PHOTOS = [
    'pexels-8082224.jpg', 'pexels-15172652.jpg', 'pexels-5336958.jpg', 'pexels-37359479.jpg', 'pexels-5825345.jpg',
    'pexels-12969046.jpg', 'pexels-6866724.jpg', 'pexels-20104252.jpg', 'pexels-32774620.jpg', 'pexels-6528796.jpg',
    'pexels-6933080.jpg', 'pexels-7114134.jpg', 'pexels-6634286.jpg', 'pexels-7578265.jpg', 'pexels-7578230.jpg',
    'pexels-10791749.jpg', 'pexels-6962951.jpg', 'pexels-8133129.jpg', 'pexels-6532528.jpg', 'pexels-7859072.jpg',
    'pexels-29295922.jpg', 'pexels-27855256.jpg', 'pexels-29546725.jpg', 'pexels-4098181.jpg', 'pexels-7579103.jpg',
    'pexels-4100482.jpg', 'pexels-4098342.jpg', 'pexels-4049517.jpg', 'pexels-7671356.jpg', 'pexels-6578777.jpg',
    'pexels-5699824.jpg', 'pexels-5336921.jpg', 'pexels-11326620.jpg', 'pexels-13240251.jpg', 'pexels-39798344.jpg',
    'pexels-28824144.jpg', 'pexels-10711264.jpg',
]
VIDEOS = [V_ACQUA, V_ACQUA2, V_NEBBIA, V_NEBBIA2, V_MARE, V_TENDA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=28, peso_titoli='400', corsivo=True, maiuscole_pulsanti=False)
C = K.c
SABBIA, ACQUA = C['sec'], C['acc2']
MONO = 'IBM Plex Mono'
VOCI = [('map-pin', 'Via Santo Stefano 38 · Bologna', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Respiro', logo_size='24', overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Respiro', 'Studio di psicoterapia a Bologna, dal 2014. Quattro psicoterapeuti, colloqui in studio e '
                   'online, un primo incontro per conoscersi senza impegno.',
                   [('I percorsi', PERCORSI), ('Chi siamo', NOI), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Gli orari', 'Lunedì — venerdì 9 — 21<br>Online anche il sabato mattina', VOCI,
                   '© 2026 Respiro · Bologna · Albo degli psicologi dell’Emilia-Romagna', [('instagram', 'https://instagram.com')],
                   marchio_size=150, fondo=C['nero'])


# ── L'apertura: il respiro ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0
RESPIRO = 8000


def _tondo(lid, cx, cy, d, cxm, cym, dm, **kw):
    """Un tondo di diametro d px (computer) centrato in cx, cy (%), e dm px sul telefono: larghezza e altezza in % della
    tela, così resta un cerchio sui due formati."""
    w, h = d / W * 100, d / H * 100
    wm, hm = dm / WM * 100, dm / HM * 100
    x, y = round(cx - w / 2, 2), round(cy - h / 2, 2)
    resp = kw.pop('responsive', {})
    resp.setdefault('mobile', {}).update({'x': round(cxm - wm / 2, 2), 'y': round(cym - hm / 2, 2), 'width': round(wm, 2),
                                          'height': round(hm, 2)})
    return K.livello(lid, tipo='shape', x=x, y=y, w=round(w, 2), h=round(h, 2), borderRadius=999, responsive=resp, **kw), x, y


def apertura(t):
    m = K.mob
    CX, CY, CXM, CYM = 50, 47, 50, 60
    via = lambda s: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 's': 1, 'o': 100}, {'p': 100, 's': s, 'o': 0}]}
    livelli = []
    # gli anelli: tre assi diversi, tre velocità
    for k, (d, dm, asse, dur, col) in enumerate([(560, 520, 'rotationX', 22000, mix(C['acc'], 70)),
                                                  (440, 410, 'rotationY', 28000, mix(ACQUA, 70)),
                                                  (330, 300, 'rotationX', 17000, mix(SABBIA, 60))]):
        L, x, y = _tondo('re-anello%d' % k, CX, CY, d, CXM, CYM, dm, bgColor='transparent', borderWidth=1.5,
                         borderColor=col, entra='fadeIn', ritardo=200 + k * 250, durata=1400, scrollMove=via(1.6 + k * 0.3))
        giri = 1 if k != 2 else -1
        base = {'rotation': 30 * k}
        if k == 2:
            base['rotationY'] = 0.01
        L['timeline'] = K.tl(x, y, [(0, dict(base, **{asse: 0.01})), (dur, dict(base, **{asse: 360 * giri}))], dur)
        livelli.append(L)
    # il disco che respira e la sua luce
    # l'alone: un tondo sfocato a parte, senza timeline (la timeline ritaglia il livello al suo riquadro: un'ombra lì
    # diventava un quadrato di luce)
    alone, _, _ = _tondo('re-alone', CX, CY, 380, CXM, CYM, 350, bgColor=mix(C['acc'], 22), filterBlur=46, entra='fadeIn',
                         ritardo=0, durata=1600, scrollMove=via(2.4), animLoop='breathLoop', animLoopDuration=RESPIRO)
    livelli.append(alone)
    L, x, y = _tondo('re-luce', CX, CY, 300, CXM, CYM, 280, bgColor=mix(C['acc'], 18), entra='fadeIn',
                     ritardo=0, durata=1200, scrollMove=via(2.2), borderWidth=1, borderColor=mix(C['acc'], 50))
    L['timeline'] = K.tl(x, y, [(0, {'scale': 0.62, 'opacity': 70}, 'sine.inOut'), (RESPIRO // 2, {'scale': 1.0, 'opacity': 100},
                                                                                  'sine.inOut'),
                                (RESPIRO, {'scale': 0.62, 'opacity': 70})], RESPIRO)
    livelli.append(L)
    for k, parola in enumerate(('Inspira', 'Espira')):
        on, off = (0, 0) if k == 0 else (0, 0)
        y_p = CY - 4.2
        L = K.livello('re-p%d' % k, contenuto=parola, x=30, y=y_p, w=40, size=46, peso='400', colore=C['chiaro'],
                      align='center', fontStyle='italic', entra='fadeIn', ritardo=0, durata=600,
                      scrollMove=via(1.2), responsive={'mobile': {'fontSize': m(30), 'x': 10, 'y': CYM - 1.6, 'width': 80}})
        if k == 0:
            pts = [(0, {'opacity': 0}), (900, {'opacity': 100}), (RESPIRO // 2 - 700, {'opacity': 100}),
                   (RESPIRO // 2, {'opacity': 0}), (RESPIRO, {'opacity': 0})]
        else:
            pts = [(0, {'opacity': 0}), (RESPIRO // 2, {'opacity': 0}), (RESPIRO // 2 + 900, {'opacity': 100}),
                   (RESPIRO - 700, {'opacity': 100}), (RESPIRO, {'opacity': 0})]
        L['timeline'] = K.tl(30, y_p, pts, RESPIRO, ritardo=1200)
        livelli.append(L)
    testi = [
        K.livello('re-o', contenuto='Respiro · psicoterapia · Bologna', x=6, y=12, w=40, size=12, peso='500',
                  colore=mix(C['chiaro'], 82), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('re-t1', contenuto='Un respiro,', tag='h1', x=6, y=72, w=44, size=72, peso='400', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=500, durata=1200,
                  responsive={'mobile': {'fontSize': m(44), 'y': 11, 'x': 7, 'width': 86}}),
        K.livello('re-t2', fontFamily='Newsreader', contenuto='<em>poi parliamo.</em>', tag='p', x=6, y=81, w=44, size=72, peso='400',
                  colore=C['acc'], lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=750, durata=1200,
                  responsive={'mobile': {'fontSize': m(44), 'y': 15, 'x': 7, 'width': 86}}),
        K.livello('re-s', contenuto='Colloqui individuali, di coppia e online. Il primo incontro serve a conoscerci: '
                                    'se non fa per voi, non si paga.', x=64, y=72, w=30, size=16, peso='400',
                  colore=mix(C['chiaro'], 86), lineHeight=1.6, ritardo=1000,
                  responsive={'mobile': {'fontSize': m(13), 'y': 20.5, 'x': 7, 'width': 86}}),
        K.livello('re-b', tipo='button', contenuto='Il primo incontro', x=64, y=84, w='auto', size=14, peso='600',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=999, entra='fadeInUp', ritardo=1200,
                  buttonUrl=CONTATTI, hover={'bgColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'y': 27, 'x': 7, 'fontSize': m(12)}}),
        K.livello('re-l', tipo='button', contenuto='I percorsi', x=79, y=84, w='auto', size=14, peso='600',
                  colore=C['chiaro'], bgColor='transparent', padding=17, borderRadius=999, borderWidth=1,
                  borderColor=mix(C['chiaro'], 60), entra='fadeInUp', ritardo=1300, buttonUrl=PERCORSI,
                  hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 81}, 'mobile': {'y': 27, 'x': 52, 'fontSize': m(12)}}),
        K.livello('re-n', contenuto='Seguite il cerchio: quattro secondi dentro, quattro fuori.', x=60, y=12, w=34,
                  size=12, peso='500', colore=mix(C['chiaro'], 80), align='right', letterSpacing=1, fontFamily=MONO,
                  ritardo=1800, responsive={'mobile': {'fontSize': m(10), 'x': 7, 'y': 82, 'width': 86, 'textAlign': 'center'}}),
    ]
    veli = [K.livello('re-velo', tipo='shape', x=0, y=55, w=100, h=45, entra='fadeIn', ritardo=0, durata=600,
                      shapeGradient={'angle': 0, 'from': mix(C['nero'], 86), 'to': mix(C['nero'], 0), 'fromStop': 45},
                      responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 36}}),
            K.livello('re-velom', tipo='shape', x=0, y=0, w=0.1, h=0.1, entra='fadeIn', ritardo=0, durata=600,
                      shapeGradient={'angle': 180, 'from': mix(C['nero'], 88), 'to': mix(C['nero'], 0), 'fromStop': 70},
                      responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 38}})]
    sl = [K.diapositiva('re-1', K.sfondo_video(V_ACQUA, overlay=0.2, colore=C['nero']), veli + livelli + testi, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       heightMobile={'mode': 'px', 'value': 1400})


PERCORSI_L = [  # nome, sotto, descrizione, foto
    ('Individuale', 'Cinquanta minuti, una volta a settimana', 'Per l’ansia, l’umore, i periodi che non passano, le scelte '
     'difficili. Si parte da quello che vi ha portato qui.', 'pexels-5336958.jpg'),
    ('Di coppia', 'Un’ora e mezza, ogni due settimane', 'Per le coppie che litigano sempre sulle stesse cose, o che hanno '
     'smesso di litigare. Si viene in due.', 'pexels-7671356.jpg'),
    ('Online', 'Da casa, con la stessa cura', 'Per chi vive lontano, viaggia per lavoro o in studio non riesce a venire. '
     'Bastano una stanza tranquilla e le cuffie.', 'pexels-6962951.jpg'),
    ('I gruppi', 'Otto incontri, il giovedì sera', 'Per l’ansia e il panico, in sei persone: si impara a respirare, e si '
     'scopre di non essere i soli.', 'pexels-4049517.jpg'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Lo studio · Bologna, dal 2014', ['Una stanza tranquilla,', 'il tempo che serve.'],
                                size=56, layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Non c’è bisogno di sapere già cosa dire. Il primo incontro è per conoscersi: ci raccontate '
                                'cosa vi porta qui, vi diciamo come lavoriamo, e insieme decidiamo se e come andare avanti.',
                             size=17), K.button(t, 'Chi siamo', NOI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('50′', 'Un colloquio'), ('4', 'Psicoterapeuti'), ('12', 'Anni di studio'),
                            ('0 €', 'Il primo incontro')])], style={'margin_top': 64}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I percorsi', ['Da soli, in due,', 'in gruppo.'], size=56,
                         tagline='Passate su un percorso: si apre la stanza.')]),
        t.one([P.elenco_hover(K, t, PERCORSI_L, PERCORSI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_NEBBIA2, 'Le cose che pesano', 'La nebbia <em>si alza piano.</em>',
                             'Non tutto e non subito: ma un giorno vi accorgete che vedete più lontano.', overlay=35))
    out.append(t.section([
        t.one([K.sechead(t, 'Come si comincia', ['Tre passi,', 'senza fretta.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'Ci scrivete', 'Due righe, o una telefonata: vi richiamiamo entro due giorni.'),
                              ('02', 'Il primo incontro', 'Cinquanta minuti per conoscerci. Gratuito.'),
                              ('03', 'Decidete voi', 'Se volete andare avanti, fissiamo insieme il ritmo dei colloqui.')],
                       colonne=3, numero_font=MONO, titolo=30)]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [P.testimonianza(
        K, t, 'Ho rimandato per due anni. Poi un giorno ho scritto, e la prima cosa che mi hanno chiesto è stata come stavo '
              'quella mattina. Nessuno me lo chiedeva da tanto.', 'Elena', 'In terapia da un anno', size=36)])],
        bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.pannello(t, V_TENDA, 'Online', 'Anche da casa, <em>con la porta chiusa.</em>',
                          'Gli stessi colloqui, in video: basta una stanza tranquilla e le cuffie.', 'Il primo incontro',
                          CONTATTI, overlay=25, video=True))
    return out


def build_percorsi():
    t = Tpl(ID, 'percorsi')
    out = [K.intro(t, 'I percorsi', ['Ogni percorso', 'ha il suo passo.'],
                   'Lavoriamo con l’approccio cognitivo-comportamentale e sistemico. Il numero di colloqui lo decidiamo '
                   'insieme, e lo rivediamo strada facendo.', bg=K.video_bg(V_ACQUA2), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'I colloqui', ['Quanto durano,', 'quanto costano.'], size=56,
                         tagline='Le spese per la psicoterapia si detraggono al 19% con la fattura.')]),
        t.row([t.col([P.listino(K, t, [
            ('Il primo incontro', 'Cinquanta minuti per conoscerci', 'gratis', 'pexels-8082224.jpg', ''),
            ('Colloquio individuale', 'Cinquanta minuti, in studio o online', '70 €', 'pexels-12969046.jpg', ''),
        ])], wm='1-2', ws='1-1'), t.col([P.listino(K, t, [
            ('Colloquio di coppia', 'Novanta minuti', '100 €', 'pexels-6578777.jpg', ''),
            ('Gruppo per l’ansia', 'Otto incontri di due ore', '280 €', 'pexels-5699824.jpg', 'in sei'),
        ])], wm='1-2', ws='1-1')], layout='50-50', gap=56),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Per cosa venite', ['Le cose che ci', 'raccontano più spesso.'], size=56)]),
        t.one([P.righe(K, t, [('01', 'Ansia e attacchi di panico', 'Il cuore che corre, il respiro corto', 'individuale · gruppo'),
                              ('02', 'Umore basso', 'La fatica di cominciare la giornata', 'individuale'),
                              ('03', 'La coppia', 'Le stesse liti, o il silenzio', 'di coppia'),
                              ('04', 'I passaggi', 'Un lutto, una separazione, un trasloco', 'individuale'),
                              ('05', 'Il lavoro', 'Lo stress, le decisioni, il troppo', 'individuale · online')],
                       PERCORSI, titolo=32)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le stanze', ['Luce bassa,', 'poltrone comode.'], size=56)]),
        P.fila_foto(K, t, [('pexels-20104252.jpg', 'Una poltrona accanto alla finestra'),
                           ('pexels-6866724.jpg', 'La stanza con le piante'),
                           ('pexels-7578230.jpg', 'Una tazza fra le mani'),
                           ('pexels-6528796.jpg', 'La luce dietro la tenda')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MARE, 'I gruppi', 'Respirare <em>insieme.</em>',
                             'Il giovedì sera, in sei: il respiro, il corpo, e quello che l’ansia vi racconta.', overlay=35))
    return out


def build_noi():
    t = Tpl(ID, 'chi-siamo')
    out = [K.intro(t, 'Chi siamo', ['Quattro voci,', 'un modo di ascoltare.'],
                   'Siamo psicologi e psicoterapeuti iscritti all’Albo dell’Emilia-Romagna. Ci confrontiamo ogni settimana '
                   'sui percorsi, sempre nel rispetto del segreto professionale.', bg=K.photo_bg('pexels-15172652.jpg'),
                   size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi vi', 'ascolterà.'], size=56)]),
        K.squadra(t, [
            ('pexels-4098181.jpg', 'Anna Guidi', 'Psicoterapeuta, fondatrice', 'Ansia, panico, i gruppi del giovedì. Ha '
             'aperto lo studio nel 2014.', 'center 25%'),
            ('pexels-4100482.jpg', 'Paolo Neri', 'Psicoterapeuta sistemico', 'Le coppie e le famiglie, i passaggi della '
             'vita.', 'center 25%'),
            ('pexels-7579103.jpg', 'Sara Monti', 'Psicoterapeuta', 'Umore, lavoro, i colloqui online per chi vive '
             'all’estero.', 'center 30%'),
            ('pexels-4098342.jpg', 'Marco Lenzi', 'Psicologo', 'Ragazzi e giovani adulti, lo studio e le scelte.',
             'center 25%'),
        ], ratio='4/5', size='300', sfondo=C['sala']),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Quello che conta', ['Il modo,', 'prima del metodo.'], size=56)]),
        t.one([P.passi(K, t, [('—', 'Riservatezza', 'Quello che dite resta nella stanza. Sempre.'),
                              ('—', 'Chiarezza', 'Vi diciamo cosa facciamo, perché e per quanto tempo.'),
                              ('—', 'Libertà', 'Potete fermarvi quando volete, senza spiegazioni.'),
                              ('—', 'Confronto', 'Ogni settimana rivediamo insieme i percorsi più difficili.')],
                       numero_font=MONO, titolo=28)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_NEBBIA, 'Il tempo', 'Un passo <em>alla volta.</em>',
                             'Non c’è un tempo giusto per stare meglio: c’è il vostro.', overlay=35))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Scriveteci', 'quando vi sentite pronti.'],
                   'Rispondiamo entro due giorni lavorativi, con discrezione. Se preferite, lasciate solo un numero: vi '
                   'richiamiamo noi.', bg=K.photo_bg('pexels-29295922.jpg'), size=92)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Il percorso', 'percorso', opzioni='Non lo so ancora\nIndividuale\nDi coppia\nOnline\nGruppo '
                     'per l’ansia'),
             K.campo('select', 'Come preferite', 'modo', opzioni='In studio\nOnline'),
             K.campo('textarea', 'Se volete, due righe', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Il primo incontro', ['Due righe bastano,', 'il resto lo diremo a voce.'],
                                'Il primo incontro è gratuito e senza impegno.', VOCI, campi, 'Respiro — primo incontro',
                                'Scrivete', 'Grazie. Vi rispondiamo entro due giorni.'))
    out.append(K.domande(t, 'Prima di scrivere', ['Le domande', 'che si fanno tutti.'], [
        ('Serve l’impegnativa del medico?', 'No. Si può venire direttamente.'),
        ('Quanto dura un percorso?', 'Dipende: a volte dieci incontri, a volte di più. Lo decidiamo insieme e lo rivediamo.'),
        ('Online funziona davvero?', 'Sì, per molte persone come in studio. Serve solo un posto dove nessuno vi ascolta.'),
        ('E se non mi trovo bene?', 'Ce lo dite e vi indichiamo un collega: succede, e va bene così.'),
    ], 're-faq'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['In via Santo Stefano,', 'secondo piano.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'lunedì — venerdì 9 — 21'), ('L’ingresso', 'il citofono «Respiro», ascensore'),
                                  ('In autobus', 'linee 11 e 13, fermata Santo Stefano')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Respiro · studio', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · una pausa', color=K.acc_media, align='center'),
                       K.headline(t, 'Respirate, <em>non è niente.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Questa pagina non c’è. Torniamo con calma all’inizio.', size=17, align='center',
                              color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_ACQUA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Salute e cura', 'Il cerchio che respira',
    ['psicoterapia', 'psicologo', 'benessere', 'colloqui online', 'bologna', 'scuro', 'slider pro', '3d'],
    'Uno studio di psicoterapia, il blu della sera e la lavanda: in apertura tre anelli che girano in 3D sul video '
    'dell’acqua scura, un disco di luce che respira e le parole «Inspira» ed «Espira» che si danno il cambio; i percorsi, '
    'le persone dello studio e il primo incontro gratuito.',
    [('I percorsi', PERCORSI), ('Chi siamo', NOI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('percorsi', 'I percorsi', 'percorsi'), ('chi-siamo', 'Chi siamo', 'chi-siamo'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Karla\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Newsreader\', Georgia, serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Karla', 'Newsreader', 'IBM Plex Mono'], corsivo_di=['Newsreader'],
    peso_titoli='400', raggio=28, grana=4, titoli_line='1.04', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'respiro'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'percorsi.json': build_percorsi(), 'chi-siamo.json': build_noi(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
