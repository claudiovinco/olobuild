# -*- coding: utf-8 -*-
"""Genera il tema «Incudine» (id incudine): un fabbro di Bienno, in Valle Camonica, il borgo dei magli — cancelli,
ringhiere, inferriate e parapetti forgiati a mano, lampade e attrezzi, il restauro del ferro antico.

La trovata: «un colpo per gesto». Lo slider resta fermo e ogni gesto della rotella (o del dito) è un colpo di martello: il
ferro passa dalla forgia (1.100 gradi, dritto e giallo) alla punta tirata, alla piega, al ricciolo che si raffredda
grigio. Quattro diapositive col «Una diapositiva per gesto» del Pro Slider, la transizione a scatto (zoom), le
scintille a ogni colpo e la temperatura che scende, grande, dietro il ferro.

Tema scuro: carbone, il ferro rovente, l'acciaio. Titoli in Zilla Slab, testo in Libre Franklin, dati in Azeret Mono.
Cursore: martello (al clic batte e fa scintille).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'incudine'
NAME = 'Incudine'

HEX = {
    'primary': '#FF7A1A', 'primary_contrast': '#0D0B0A', 'secondary': '#8A97A3', 'secondary_contrast': '#0D0B0A',
    'accent': '#FFC24D', 'dark': '#050404', 'light': '#FFFFFF', 'muted': '#1A1614', 'muted_contrast': '#F1E9DF',
    'text': '#F1E9DF', 'text_muted': '#B5A99C', 'background': '#0D0B0A', 'border': '#2E2722', 'link': '#FF7A1A',
}
HOME, LAVORI, FORGIA, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:lavori}}', '{{PAGINA:la-forgia}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'forgia@incudine.example', '0364 000 777', 'tel:+390364000777'
LAT, LNG = '45.9330', '10.2990'

V_COLPI, V_FORGIA, V_BARRA, V_BUIO, V_FUOCO, V_MARTELLO, V_PIEGA = (
    'pexels-video-4186969', 'pexels-video-5846389', 'pexels-video-5846379', 'pexels-video-5846589', 'pexels-video-5846383',
    'pexels-video-31638148', 'pexels-video-38103748')
VIDEOS = [V_COLPI, V_FORGIA, V_BARRA, V_BUIO, V_FUOCO, V_MARTELLO, V_PIEGA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(FABBRO_PRATO, MARTELLO_FUOCO, SCINTILLE_FABBRO, FABBRO_RUOTA, FABBRO_FORGIA, SCINTILLE_ALTO, FABBRO_SCINTILLE, SCINTILLE_RITRATTO,
 FORGIA_BUIO, BARRA_ROVENTE, MARTELLO_FERRO, INCUDINE_ATTREZZI, INCUDINE_BUIO, CORNO, BARRA_INCUDINE, FERRO_FUOCO, CANCELLO_BIANCO,
 CANCELLO_BN, CANCELLETTO, CANCELLO_ORNATO, CANCELLO_ARCO, CANCELLO_VERDE, FABBRO_ANZIANO) = f(
    334032, 38156404, 40815798, 33739127, 14966843, 37226044, 37579453, 36531902, 5845892, 3997247, 37278119, 10025348, 36164065,
    618151, 5845951, 6804260, 32185454, 36780441, 8125961, 7540035, 15507814, 38951991, 38092597)
PHOTOS = [FABBRO_PRATO, MARTELLO_FUOCO, SCINTILLE_FABBRO, FABBRO_RUOTA, FABBRO_FORGIA, SCINTILLE_ALTO, FABBRO_SCINTILLE,
          SCINTILLE_RITRATTO, FORGIA_BUIO, BARRA_ROVENTE, MARTELLO_FERRO, INCUDINE_ATTREZZI, INCUDINE_BUIO, CORNO, BARRA_INCUDINE,
          FERRO_FUOCO, CANCELLO_BIANCO, CANCELLO_BN, CANCELLETTO, CANCELLO_ORNATO, CANCELLO_ARCO, CANCELLO_VERDE, FABBRO_ANZIANO]
DISEGNI = ['incudine-ferro-%d.svg' % k for k in range(1, 5)] + ['incudine-scintille.svg']
LOGHI = ['incudine-logo-chiaro.png', 'incudine-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
FONT_T = 'Zilla Slab'
VOCI = [('map-pin', 'Via dei Magli 9 · Bienno', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '640/110', 'Incudine: fabbri a Bienno, il borgo dei magli, dal 1921. Cancelli, '
                   'ringhiere e inferriate forgiati a mano, e il ferro antico rimesso a nuovo.',
                   [('I lavori', LAVORI), ('La forgia', FORGIA), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'La forgia', 'Lunedì — sabato 8 — 18<br>La domenica il maglio per i visitatori, d’estate', VOCI,
                   '© 2026 Incudine · Bienno (BS) · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                        ('youtube', 'https://youtube.com')], scuro=True)


# ── L'apertura: un colpo per gesto ──
COLPI = [
    ('1 · scaldare', 'Nel fuoco,', 'a millecento gradi.', 'Il ferro entra nella forgia nero ed esce giallo: solo così si lavora.',
     '1.100°', V_BUIO, 'Scaldare'),
    ('2 · tirare', 'Il primo colpo', 'tira la punta.', 'Sull’incudine, finché è giallo. Quando diventa rosso si torna nel fuoco.',
     '980°', V_COLPI, 'Tirare'),
    ('3 · piegare', 'Il secondo', 'lo piega.', 'Sul corno dell’incudine, a colpi piccoli, girando il ferro con la tenaglia.',
     '850°', V_FORGIA, 'Piegare'),
    ('4 · raffreddare', 'Il ricciolo.', 'Adesso è ferro.', 'Si raffredda all’aria, poi la cera d’api calda. Dura cent’anni.',
     '20°', V_BARRA, 'Raffreddare'),
]


def apertura(t):
    m = K.mob
    sl = []
    for k, (occ, t1, t2, sub, gradi, video, tab) in enumerate(COLPI):
        s = 'in-%d-' % (k + 1)
        L = K.veli(s + 'velo', 'sinistra', 34, 58, colore=C['nero'], forza=88, mobile_alto=28) + [
            K.livello(s + 'gradi', contenuto=gradi, x=34, y=47, w=62, size=300, peso='700', align='right', fontFamily=FONT_T,
                      colore=mix(C['chiaro'], 10 if k < 3 else 7), lineHeight=1.0, entra='fadeIn', ritardo=0, durata=900,
                      responsive={'mobile': {'fontSize': m(80), 'x': 4, 'y': 26, 'width': 92}}),
            K.livello(s + 'ferro', tipo='image', imageSrc=K.img(DISEGNI[k]), imageAlt='Il ferro: ' + tab.lower(), objectFit='contain',
                      x=20, y=46, w=60, h=42.6, entra='zoomIn' if k else 'fadeIn', ritardo=150, durata=500,
                      responsive={'mobile': {'x': 4, 'y': 48, 'width': 92, 'height': 16.8}}),
        ]
        if k in (1, 2):
            L.append(K.livello(s + 'scintille', tipo='image', imageSrc=K.img(DISEGNI[4]), imageAlt='', objectFit='contain', x=20, y=40,
                               w=60, h=42.6, entra='fadeIn', ritardo=100, durata=200,
                               responsive={'mobile': {'x': 4, 'y': 44, 'width': 92, 'height': 16.8}}))
        L += [
            K.livello(s + 'o', contenuto=occ, x=6, y=13, w=40, size=13, peso='500', colore=C['acc'], letterSpacing=3,
                      textTransform='uppercase', fontFamily='Azeret Mono', ritardo=100,
                      responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
            K.livello(s + 't1', contenuto=t1, tag='h1' if k == 0 else 'h2', x=6, y=17, w=50, size=76, peso='700', colore=C['chiaro'],
                      fontFamily=FONT_T, lineHeight=1.0, entra='maskFromBottom', ritardo=150, durata=700,
                      responsive={'mobile': {'fontSize': m(31), 'x': 6, 'y': 10.5, 'width': 88}}),
            K.livello(s + 't2', contenuto=t2, tag='p', x=6, y=26.5, w=50, size=76, peso='700', colore=C['acc'], fontFamily=FONT_T,
                      lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=700,
                      responsive={'mobile': {'fontSize': m(31), 'x': 6, 'y': 14, 'width': 88}}),
            K.livello(s + 's', contenuto=sub, x=6, y=37.5, w=32, size=17, peso='400', colore=mix(C['chiaro'], 90), lineHeight=1.55,
                      ritardo=450, responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 19, 'width': 86}}),
        ]
        if k == 0:
            L.append(K.livello(s + 'h', contenuto='Scorrete: un colpo alla volta ↓', x=6, y=47, w=32, size=15, peso='500', colore=C['acc'],
                               fontFamily='Azeret Mono', ritardo=900, animLoop='pulseLoop', animLoopDuration=2200,
                               responsive={'mobile': {'fontSize': m(12), 'x': 6, 'y': 68, 'width': 86}}))
        if k == 3:
            L += [
                K.livello(s + 'b1', tipo='button', contenuto='I cancelli', x=6, y=48, w='auto', size=13, peso='700', colore=C['on_acc'],
                          bgColor=C['acc'], padding=18, borderRadius=0, letterSpacing=2, textTransform='uppercase', buttonUrl=LAVORI,
                          entra='fadeIn', ritardo=600, hover={'bgColor': '#FFFFFF', 'duration': 250},
                          responsive={'mobile': {'x': 6, 'y': 70, 'fontSize': m(12)}}),
                K.livello(s + 'b2', tipo='button', contenuto='La forgia', x=19, y=48, w='auto', size=13, peso='700', colore=C['chiaro'],
                          bgColor='transparent', padding=18, borderRadius=0, borderWidth=1, borderColor=mix(C['chiaro'], 55),
                          letterSpacing=2, textTransform='uppercase', buttonUrl=FORGIA, entra='fadeIn', ritardo=700,
                          hover={'bgColor': mix(C['chiaro'], 14), 'duration': 250},
                          responsive={'mobile': {'x': 50, 'y': 70, 'fontSize': m(12)}}),
            ]
        M.veli_oltre(L)
        sl.append(K.diapositiva('in-%d' % (k + 1), K.sfondo_video(video, overlay=0.34, colore=C['nero']), L, durata=600000, tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='zoom', durata_trans=500, showArrows=False,
                       showDots=False, showTabs=True, tabPosition='bottom', showProgressBar=False, autoplay=False, loop=False,
                       parallax=False, scrollTimeline=True, scrollTimelineDistance=2600, scrollStep=True,
                       heightMobile={'mode': 'px', 'value': 1400})


def cancello_punti(t):
    """Il cancello coi punti da toccare (Hotspot)."""
    return t.classic('hotspot', {
        'image': K.img(CANCELLO_ORNATO), 'image_alt': 'Un cancello di ferro battuto a due ante, coi riccioli e le punte',
        'image_height': '560', 'aspect_ratio': '16/9', 'object_fit': 'cover', 'object_position': 'center center',
        'markers': [
            {'id': 'in-h1', 'pos_x': '50', 'pos_y': '30', 'title': 'I riccioli', 'description': 'Forgiati uno a uno sul corno '
             'dell’incudine: nessuno è uguale all’altro, ed è così che si riconosce il ferro battuto.', 'icon': '',
             'tooltip_position': 'bottom', 'link': '', 'link_label': ''},
            {'id': 'in-h2', 'pos_x': '30', 'pos_y': '55', 'title': 'Le chiodature', 'description': 'Le giunzioni a caldo coi chiodi '
             'ribattuti, come nel Settecento: niente saldature a vista.', 'icon': '', 'tooltip_position': 'right', 'link': '',
             'link_label': ''},
            {'id': 'in-h3', 'pos_x': '70', 'pos_y': '72', 'title': 'La zincatura', 'description': 'A caldo, prima della cera o della '
             'vernice: trent’anni senza ruggine, anche in montagna.', 'icon': '', 'tooltip_position': 'left', 'link': '',
             'link_label': ''},
            {'id': 'in-h4', 'pos_x': '50', 'pos_y': '86', 'title': 'Il motore nascosto', 'description': 'Si apre col telecomando: '
             'il motore sta nel pilastro, i cardini restano quelli forgiati.', 'icon': '', 'tooltip_position': 'top', 'link': '',
             'link_label': ''}]}, style={})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Incudine · Bienno, dal 1921', ['Il ferro', 'si batte caldo.'], size=72, layout='stack',
                                size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'A Bienno i magli battono dal Quattrocento, con l’acqua del Grigna. La nostra forgia è del 1921: '
                                'cancelli, ringhiere e inferriate li facciamo ancora col fuoco e il martello, e il ferro antico '
                                'lo restauriamo senza rifarlo.', size=17),
                      K.button(t, 'La forgia', FORGIA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il cancello', ['Toccate i punti,', 'vi diciamo come si fa.'], size=60)]),
        t.one([cancello_punti(t)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I lavori', ['Ferro battuto,', 'per cent’anni.'], size=60)]),
        t.one([K.coverflow(t, [(CANCELLO_ORNATO, 'Un cancello a due ante coi riccioli', 'Il cancello di Breno', 'due ante, 4 m'),
                               (CANCELLO_ARCO, 'Un cancello ad arco in una porta di pietra', 'Il portale di Lovere', 'ad arco'),
                               (CANCELLO_BIANCO, 'Un cancelletto bianco in un giardino', 'Il cancelletto di Boario', 'verniciato'),
                               (CANCELLO_VERDE, 'Un cancello verde tra due pilastri', 'La villa di Darfo', 'a due ante'),
                               (CANCELLETTO, 'Un cancelletto tra due muri di pietra', 'L’orto di Bienno', 'a un’anta')],
                              altezza=440, larghezza=340)]),
        t.one([K.button(t, 'Tutti i lavori', LAVORI, align='center', pad_top=30)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MARTELLO, 'Il martello', 'Mille colpi, <em>e nessuno a caso.</em>',
                             'Un ricciolo ne vuole una quarantina: il ferro lo dice lui quando torna nel fuoco.', overlay=20))
    out.append(X.recensioni(K, t, [
        ('Il cancello della casa dei nonni era a pezzi. L’hanno smontato, raddrizzato, rifatto il ricciolo che mancava.', 'Lucia', 'Breno'),
        ('La ringhiera della scala: siamo andati a vederla forgiare. Mio figlio ne parla ancora.', 'Andrea', 'Darfo Boario'),
        ('Inferriate che sembrano decorazioni, e invece sono robuste come quelle di un tempo.', 'Giorgio', 'Lovere')],
        'Chi ci ha chiamato', ['Tre case,', 'tre ferri battuti.']))
    return out


def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [K.intro(t, 'I lavori', ['Cancelli, ringhiere,', 'inferriate.'],
                   'Disegnati con voi, forgiati a mano a Bienno, montati da noi: e il ferro antico restaurato, quando si può.',
                   bg=K.video_bg(V_PIEGA, overlay=30), size=92)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Cancelli', 'Il cancello', 'forgiato', 'Riccioli, punte, chiodature a caldo; il motore nel pilastro se serve. 6–10 settimane.',
         CANCELLO_ORNATO),
        ('Ringhiere', 'La ringhiera', 'della scala', 'Il corrimano in ferro battuto, piegato a caldo sulla curva della vostra scala.',
         CANCELLO_BN),
        ('Inferriate', 'L’inferriata', 'che non sembra', 'Robusta come una grata, disegnata come un ornamento.', CANCELLO_ARCO),
        ('Restauri', 'Il ferro', 'antico', 'Sabbiato, raddrizzato a caldo, i pezzi mancanti rifatti uguali, la cera d’api.',
         CANCELLETTO)], altezza=420)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In forgia', ['Il fuoco,', 'il martello, l’incudine.'], size=56)]),
        P.fila_foto(K, t, [(BARRA_INCUDINE, 'La barra rovente sull’incudine'), (MARTELLO_FERRO, 'Il martello sul ferro caldo'),
                           (CORNO, 'Il corno dell’incudine'), (FORGIA_BUIO, 'La forgia accesa')], ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_forgia():
    t = Tpl(ID, 'la-forgia')
    out = [K.intro(t, 'La forgia', ['Cent’anni', 'di fuoco.'],
                   'Nel 1921 il bisnonno comprò il maglio sul Grigna. Adesso lo usiamo per i visitatori, d’estate; il resto lo fa il martello.',
                   bg=K.video_bg(V_FUOCO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come si forgia', ['Tre passi,', 'mille volte.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'scaldare', FORGIA_BUIO, 'Il fuoco', 'A millecento', 'gradi', 'Il carbone di legna e il mantice: il ferro deve diventare giallo.',
             '1.100', '°C'),
            ('2', 'battere', MARTELLO_FUOCO, 'Il martello', 'Sull’incudine', 'finché è caldo', 'Un minuto per volta, poi si torna nel fuoco.',
             '40', 'colpi'),
            ('3', 'finire', CORNO, 'La cera', 'D’api', 'calda', 'Sul ferro ancora tiepido: protegge, e lascia vedere i colpi.',
             '100', 'anni')], icona='hammer')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Alla forgia', ['Chi batte', 'il ferro.'], size=56)]),
        K.squadra(t, [(FABBRO_ANZIANO, 'Angelo Morandini', 'Mastro fabbro', 'Sessant’anni di forgia: i riccioli difficili li fa ancora lui.',
                       'center 30%'),
                      (FABBRO_FORGIA, 'Pietro Morandini', 'Fabbro', 'Il nipote: disegna i cancelli e segue i montaggi.', 'center 30%'),
                      (FABBRO_PRATO, 'Tomas Bettoni', 'Restauri', 'Il ferro antico: sabbiatura, raddrizzatura a caldo, cera.', 'center 25%')],
                  ratio='4/5', size='320'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_COLPI, 'Le visite', 'D’estate, <em>il maglio si accende.</em>',
                          'La domenica di luglio e agosto apriamo la forgia: si vede battere il ferro, e i bambini provano il martello piccolo.',
                          'Scriveteci', CONTATTI + '#scrivi', video=True))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Un disegno,', 'e lo forgiamo.'],
                   'Mandateci una foto e le misure, o venite in forgia: il preventivo è gratis, il disegno a mano anche.',
                   bg=K.photo_bg(SCINTILLE_FABBRO, position='center 40%'), size=96)]
    conf = X.preventivo(K, t, [
        ('lavoro', 'Il lavoro', 'uno', True, 'Indicativo, compreso il montaggio in Valle Camonica.'),
        ('stile', 'Lo stile', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('cancelletto', 'lavoro', 'Cancelletto pedonale', 'Un’anta, 1 m.', 1900, '', True),
        ('cancello', 'lavoro', 'Cancello carraio', 'Due ante, 4 m.', 6800, 'il più chiesto', False),
        ('ringhiera', 'lavoro', 'Ringhiera di scala', 'Al metro, piegata sulla curva.', 650, '', False),
        ('inferriata', 'lavoro', 'Inferriata', 'Per una finestra.', 780, '', False),
        ('lineare', 'stile', 'Lineare', 'Barre e piatti, poche decorazioni.', 0, '', True),
        ('riccioli', 'stile', 'Coi riccioli', 'Riccioli e punte forgiati.', 900, '', False),
        ('antico', 'stile', 'Copia di un antico', 'Uguale a un disegno o a un pezzo vecchio.', 1600, '', False),
        ('zinco', 'extra', 'Zincatura a caldo', 'Trent’anni senza ruggine.', 380, '', False),
        ('motore', 'extra', 'Motore nel pilastro', 'Col telecomando.', 1450, '', False)],
        'Il vostro ferro', CONTATTI + '?lavoro={configurazione}#scrivi', 'IVA compresa · disegno a mano gratis')
    out.append(X.sezione_preventivo(K, t, 'Il preventivo', ['Componete il lavoro,', 'il totale si fa da sé.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Comune', 'comune'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Le misure, il disegno', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Una foto,', 'le misure.'],
                                'Vi rispondiamo entro tre giorni, col disegno a mano se serve.', VOCI, campi, 'Incudine — richiesta',
                                'Invia', 'Grazie! Vi rispondiamo entro tre giorni.'))
    out.append(X.zone(K, t, 'La forgia', ['Via dei Magli,', 'sul Grigna.'], 'Bienno è a mezz’ora da Darfo, un’ora da Brescia.',
                      [('Orari', 'lunedì — sabato 8 — 18'), ('Visite', 'domeniche di luglio e agosto'), ('Montaggi', 'Valle Camonica, Sebino, Brescia'),
                       ('Telefono', PHONE)], LAT, LNG, 'Incudine · la forgia', True, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · ferro freddo', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>non si piega.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Si è raffreddata prima del colpo. Torniamo nel fuoco.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_FUOCO, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'Un colpo per gesto',
    ['artigiani', 'fabbro', 'ferro battuto', 'cancelli', 'ringhiere', 'scuro', 'slider pro', 'scorrimento'],
    'Fabbri a Bienno: nell’apertura ogni gesto della rotella è un colpo di martello, e il ferro passa dalla forgia a 1.100 '
    'gradi alla punta, alla piega, al ricciolo che si raffredda; le scintille a ogni colpo, la temperatura che scende. Poi il '
    'cancello coi punti da toccare, i lavori, la forgia e il preventivo da comporre.',
    [('I lavori', LAVORI), ('La forgia', FORGIA), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('lavori', 'I lavori', 'lavori'), ('la-forgia', 'La forgia', 'la-forgia'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Libre Franklin\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Zilla Slab\', Georgia, serif',
    '\'Azeret Mono\', ui-monospace, Menlo, monospace', ['Libre Franklin', 'Zilla Slab', 'Azeret Mono'], corsivo_di=[],
    peso_titoli='700', raggio=0, grana=7, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'martello'
THEME['cursor']['ring_color'] = '#3A3A3A'
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'lavori.json': build_lavori(), 'la-forgia.json': build_forgia(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
