# -*- coding: utf-8 -*-
"""Genera il tema «Sirena» (id sirena): un’associazione di volontari del soccorso a Treviso: le ambulanze giorno e notte,
i corsi di primo soccorso, i defibrillatori in paese, i turni dei volontari e le donazioni.

Tema scuro, la notte di un turno: nero blu, arancio, giallo e l’azzurro del lampeggiante. Titoli in Barlow, testo in
Public Sans, dati in Source Code Pro.

Apertura: «il lampeggiante». Sul video della città di notte, a destra, un lampeggiante: la cupola arancio col segno del
soccorso che gira in 3D su sé stessa e due fasci di luce che spazzano la tela, come sul tetto di un’ambulanza. Sotto il
titolo il tracciato di un battito pulsa, e il contatore dice quante volte siamo usciti quest’anno. Cursore: il battito.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'sirena'
NAME = 'Sirena'

HEX = {
    'primary': '#FF6B1A', 'primary_contrast': '#101418', 'secondary': '#FFD34D', 'secondary_contrast': '#101418',
    'accent': '#5BC0EB', 'dark': '#0A0D12', 'light': '#F5F7FA', 'muted': '#121821', 'muted_contrast': '#E8ECF1',
    'text': '#E8ECF1', 'text_muted': '#9AA4B2', 'background': '#0D1117', 'border': '#263040', 'link': '#FF6B1A',
}

HOME, NOI, VOLONTARIO, SOSTIENI = '{{PAGINA:home}}', '{{PAGINA:chi-siamo}}', '{{PAGINA:diventa-volontario}}', '{{PAGINA:sostienici}}'
MAIL, PHONE, PHONE_TEL = 'sede@sirena.example', '+39 0422 000 118', 'tel:+390422000118'
LAT, LNG = '45.6670', '12.2430'

V_CITTA, V_LUCE, V_STRADA, V_NOTTE2, V_DALTO, V_SFOCATO, V_GUIDA = (
    'pexels-video-10692235', 'pexels-video-11387539', 'pexels-video-15285348', 'pexels-video-3371152',
    'pexels-video-10433645', 'pexels-video-6079699', 'pexels-video-8943636')
PHOTOS = [
    'pexels-9965658.jpg', 'pexels-35831388.jpg', 'pexels-33862096.jpg', 'pexels-33317471.jpg', 'pexels-33317473.jpg',
    'pexels-28271058.jpg', 'pexels-8460088.jpg', 'pexels-8460089.jpg', 'pexels-8460092.jpg', 'pexels-13418669.jpg',
    'pexels-6647006.jpg', 'pexels-6995109.jpg', 'pexels-6646916.jpg', 'pexels-29947326.jpg', 'pexels-5839955.jpg',
    'pexels-7659453.jpg', 'pexels-10725414.jpg', 'pexels-6412579.jpg', 'pexels-28271067.jpg', 'pexels-6519933.jpg',
    'pexels-6520140.jpg', 'pexels-6520139.jpg', 'pexels-6519852.jpg',
]
VIDEOS = [V_CITTA, V_LUCE, V_STRADA, V_NOTTE2, V_DALTO, V_SFOCATO, V_GUIDA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
GIALLO, BLU = C['sec'], C['acc2']
MONO = 'Source Code Pro'
VOCI = [('map-pin', 'Via Ospedale 14 · Treviso', NOI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Sirena', logo_size='26', overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Sirena', 'Associazione di volontari del soccorso a Treviso, dal 1979. Ambulanze giorno e notte, corsi '
                   'di primo soccorso, defibrillatori nei paesi. Per le emergenze chiamate sempre il 112.',
                   [('Chi siamo', NOI), ('Diventa volontario', VOLONTARIO), ('Sostienici', SOSTIENI),
                    ('Privacy', '/privacy-policy/')],
                   'La sede', 'Aperta sempre, giorno e notte<br>L’ufficio: lunedì — venerdì 9 — 18', VOCI,
                   '© 2026 Sirena · Treviso · Organizzazione di volontariato · C.F. 00000000000',
                   [('instagram', 'https://instagram.com'), ('facebook', 'https://facebook.com')], marchio_size=170)


# ── L'apertura: il lampeggiante ──
W, H, WM, HM = 1440.0, 810.0, 640.0, 1400.0


def apertura(t):
    m = K.mob
    CX, CY, D = 72.0, 44.0, 230.0      # la cupola, computer (centro %, diametro px)
    CXM, CYM, DM = 50.0, 60.0, 240.0   # e telefono
    w, h, wm, hm = D / W * 100, D / H * 100, DM / WM * 100, DM / HM * 100
    fasci = []
    # i due fasci: lunghi, sfumati, col cardine al centro della cupola (il loro bordo sinistro), girano insieme
    for k in range(2):
        L, Hf = 1100.0, 150.0
        fasci.append(K.livello('si-fascio%d' % k, tipo='shape', x=round(CX, 2), y=round(CY - Hf / H * 50, 2),
                               w=round(L / W * 100, 2), h=round(Hf / H * 100, 2),
                               shapeGradient={'angle': 90, 'from': mix(C['acc'], 46 if k == 0 else 40),
                                              'to': mix(C['acc'], 0), 'fromStop': 0},
                               borderRadius=999, entra='fadeIn', ritardo=600, durata=1200, animLoop='spinCW',
                               animLoopDuration=2600, animLoopEasing='linear',
                               customCSS='transform-origin:0% 50%;clip-path:polygon(0 42%,100% 0,100% 100%,0 58%);',
                               scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'r': 180 * k, 'o': 100},
                                                                              {'p': 60, 'r': 180 * k, 'o': 0},
                                                                              {'p': 100, 'r': 180 * k, 'o': 0}]},
                               responsive={'mobile': {'x': CXM, 'y': round(CYM - Hf / HM * 50, 2), 'width': round(L / WM * 100, 2),
                                                      'height': round(Hf / HM * 100, 2)}}))
    cupola = [
        K.livello('si-alone', tipo='shape', x=round(CX - w * 0.8, 2), y=round(CY - h * 0.8, 2), w=round(w * 1.6, 2),
                  h=round(h * 1.6, 2), bgColor=mix(C['acc'], 26), borderRadius=999, entra='fadeIn', ritardo=300, durata=1200,
                  animLoop='pulseLoop', animLoopDuration=1300,
                  responsive={'mobile': {'x': round(CXM - wm * 0.8, 2), 'y': round(CYM - hm * 0.8, 2), 'width': round(wm * 1.6, 2),
                                         'height': round(hm * 1.6, 2)}}),
        K.livello('si-cupola', tipo='shape', x=round(CX - w / 2, 2), y=round(CY - h / 2, 2), w=round(w, 2), h=round(h, 2),
                  shapeGradient={'angle': 160, 'from': '#FFB070', 'to': C['acc'], 'fromStop': 10}, borderRadius=999,
                  borderWidth=3, borderColor=mix(C['chiaro'], 60), entra='zoomIn', ritardo=300, durata=1000,
                  responsive={'mobile': {'x': round(CXM - wm / 2, 2), 'y': round(CYM - hm / 2, 2), 'width': round(wm, 2),
                                         'height': round(hm, 2)}}),
    ]
    # il segno del soccorso: gira in 3D sull'asse verticale, dentro la cupola
    sx, sy, sw, sh = CX - w * 0.32, CY - h * 0.32, w * 0.64, h * 0.64
    segno = K.livello('si-segno', tipo='icon', iconName='plus', colore=C['chiaro'], iconStrokeWidth=3.2, x=round(sx, 2),
                      y=round(sy, 2), w=round(sw, 2), h=round(sh, 2), size=140, entra='fadeIn', ritardo=800, durata=600,
                      responsive={'mobile': {'x': round(CXM - wm * 0.32, 2), 'y': round(CYM - hm * 0.32, 2),
                                             'width': round(wm * 0.64, 2), 'height': round(hm * 0.64, 2), 'fontSize': 150}})
    segno['timeline'] = K.tl(round(sx, 2), round(sy, 2), [(0, {'rotationY': 0.01}), (3200, {'rotationY': 360})], 3200,
                             ritardo=1400)
    battito = K.livello('si-battito', tipo='icon', iconName='activity', colore=C['acc'], iconStrokeWidth=2.2, x=6, y=79, w=4.2,
                        h=7.4, size=56, entra='fadeIn', ritardo=1300, durata=800, animLoop='pulseLoop', animLoopDuration=860,
                        responsive={'mobile': {'x': 7, 'y': 36.3, 'width': 9, 'height': 4, 'fontSize': 90}})
    testi = [
        K.livello('si-o', contenuto='Sirena · volontari del soccorso · Treviso', x=6, y=18, w=44, size=12, peso='600',
                  colore=mix(C['chiaro'], 82), letterSpacing=2, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'y': 8, 'x': 7, 'width': 86}}),
        K.livello('si-t1', contenuto='Quando chiamate,', tag='h1', x=6, y=24, w=52, size=86, peso='700', colore=C['chiaro'],
                  lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=350, durata=1000,
                  responsive={'mobile': {'fontSize': m(38), 'y': 11, 'x': 7, 'width': 90}}),
        K.livello('si-t2', fontFamily='Barlow', contenuto='partiamo noi.', tag='p', x=6, y=35, w=52, size=86, peso='700',
                  colore=C['acc'], lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=550, durata=1000,
                  responsive={'mobile': {'fontSize': m(38), 'y': 15, 'x': 7, 'width': 90}}),
        K.livello('si-s', contenuto='Centoventi volontari, quattro ambulanze, giorno e notte. Per le emergenze chiamate il 112: '
                                    'la centrale manda noi.', x=6, y=48, w=36, size=17, peso='400',
                  colore=mix(C['chiaro'], 86), lineHeight=1.6, ritardo=800,
                  responsive={'mobile': {'fontSize': m(13), 'y': 19.5, 'x': 7, 'width': 86}}),
        K.livello('si-b', tipo='button', contenuto='Diventa volontario', x=6, y=60, w='auto', size=13, peso='700',
                  colore=C['on_acc'], bgColor=C['acc'], padding=17, borderRadius=10, letterSpacing=1.5,
                  textTransform='uppercase', entra='fadeInUp', ritardo=1000, buttonUrl=VOLONTARIO,
                  hover={'bgColor': C['chiaro'], 'duration': 250}, responsive={'mobile': {'y': 26, 'x': 7, 'fontSize': m(11)}}),
        K.livello('si-l', tipo='button', contenuto='Sostienici', x=24, y=60, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=17, borderRadius=10, borderWidth=1, borderColor=mix(C['chiaro'], 60),
                  letterSpacing=1.5, textTransform='uppercase', entra='fadeInUp', ritardo=1100, buttonUrl=SOSTIENI,
                  hover={'bgColor': mix(C['chiaro'], 12), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'tablet': {'x': 30}, 'mobile': {'y': 26, 'x': 56, 'fontSize': m(11)}}),
        K.livello('si-n', contenuto='<strong style="color:var(--olo-color-secondary)">2.340</strong> uscite nel 2025 · una '
                                    'ogni 3 ore e 45', x=11, y=81.4, w=40, size=15, peso='500', colore=mix(C['chiaro'], 86),
                  fontFamily=MONO, ritardo=1400,
                  responsive={'mobile': {'fontSize': m(12), 'y': 37, 'x': 18, 'width': 76}}),
    ]
    veli = K.veli('si-velo', 'sinistra', 38, 66, colore=C['nero'], forza=86, mobile_alto=38)
    sl = [K.diapositiva('si-1', K.sfondo_video(V_CITTA, overlay=0.18, colore=C['nero']), veli + fasci + cupola + [segno, battito]
                        + testi, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False,
                       showDots=False, showTabs=False, showProgressBar=False, autoplay=False, parallax=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Sirena · Treviso, dal 1979', ['Gente comune,', 'turni da sei ore.'], size=56,
                                layout='stack', size_mobile=34)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo impiegati, studenti, pensionati, un’idraulica e due panettieri. Facciamo un turno a '
                                'settimana, di giorno o di notte, dopo un corso di quattro mesi. Nessuno di noi è pagato: '
                                'le ambulanze le pagano le donazioni.', size=17),
                      K.button(t, 'Chi siamo', NOI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([P.schede_icone(K, t, [('01', 'volontari', '120', '', 'Dai diciotto ai settantadue anni.', 'users'),
                                     ('02', 'ambulanze', '4', '', 'E un’auto medica, sempre pronte.', 'ambulance'),
                                     ('03', 'uscite', '2.340', '', 'Nel 2025, una ogni tre ore e tre quarti.', 'siren'),
                                     ('04', 'defibrillatori', '38', '', 'Nei paesi, nelle scuole, nei campi sportivi.',
                                      'heart-pulse')], fondo=C['sala'])], style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_LUCE, 'Di notte', 'Le luci accese <em>per qualcun altro.</em>',
                             'Il turno di notte comincia alle otto di sera e finisce col caffè delle due.', overlay=24))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Non solo', 'le ambulanze.'], size=56)]),
        P.carte(K, t, [
            ('pexels-35831388.jpg', 'Il soccorso', '112 · giorno e notte', 'Quattro ambulanze in servizio con la centrale '
             'operativa: equipaggi di tre volontari.'),
            ('pexels-33317471.jpg', 'I corsi', 'Primo soccorso per tutti', 'Il massaggio cardiaco e il defibrillatore in '
             'tre ore, nelle aziende e nelle scuole.'),
            ('pexels-8460089.jpg', 'I defibrillatori', '38 in provincia', 'Li installiamo, li controlliamo ogni mese, '
             'insegniamo a usarli.'),
            ('pexels-6647006.jpg', 'Le persone', 'Trasporti e assistenza', 'Le visite in ospedale per chi non può guidare, '
             'gli eventi, le sagre.'),
        ]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-28271058.jpg', 'Un corso di massaggio cardiaco col manichino', ratio='4/5')],
                     wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Il defibrillatore era al bar della piazza. Lo aveva messo Sirena l’anno prima, e il '
                                          'barista aveva fatto il corso. Mio padre adesso gioca a carte lì, ogni giovedì.',
                                  'Michela, Paese (TV)', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_GUIDA, 'Diventa volontario', 'Quattro mesi, <em>poi in turno.</em>',
                          'Il prossimo corso comincia il 12 gennaio: due sere a settimana, in sede.', 'Iscriviti', VOLONTARIO,
                          overlay=20, video=True))
    return out


def build_noi():
    t = Tpl(ID, 'chi-siamo')
    out = [K.intro(t, 'Chi siamo', ['Dal 1979,', 'una sirena sola.'],
                   'Ci siamo fondati in nove, con un’ambulanza comprata coi soldi della sagra. Oggi siamo centoventi, ma la '
                   'sede è sempre quella, accanto all’ospedale.', bg=K.video_bg(V_DALTO), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'La storia', ['Quarantasette anni', 'in tre tappe.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1979', 'la fondazione', 'pexels-9965658.jpg', 'L’inizio', 'Nove amici', 'e un’ambulanza',
             'La prima ambulanza comprata coi soldi della sagra di San Martino.', '9', 'volontari'),
            ('1998', 'la sede', 'pexels-28271067.jpg', 'La casa', 'La sede', 'accanto all’ospedale',
             'Il comune ci dà la vecchia portineria: la ristrutturiamo noi, nei fine settimana.', '60', 'volontari'),
            ('2026', 'oggi', 'pexels-13418669.jpg', 'Adesso', 'Centoventi', 'e quattro mezzi',
             'Turni giorno e notte, i corsi nelle scuole, trentotto defibrillatori.', '120', 'volontari')], icona='users')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In turno', ['Tre di noi,', 'stanotte.'], size=56)]),
        K.squadra(t, [
            ('pexels-6519933.jpg', 'Laura Zanatta', 'Capoturno, infermiera', 'Volontaria dal 2004. Il martedì notte guida '
             'l’ambulanza uno.', 'center 25%'),
            ('pexels-6520140.jpg', 'Giorgio Bettiol', 'Autista soccorritore', 'Pensionato, ex camionista: conosce ogni '
             'strada della provincia.', 'center 25%'),
            ('pexels-6519852.jpg', 'Samuele Tonon', 'Soccorritore', 'Studente di infermieristica, in turno il sabato.',
             'center 25%'),
        ], ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'La sede', ['Accanto all’ospedale,', 'sempre aperta.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('La sede', 'aperta giorno e notte'), ('L’ufficio', 'lunedì — venerdì 9 — 18'),
                                  ('Per le emergenze', 'chiamate il 112')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Sirena · sede', scuro=True, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_volontario():
    t = Tpl(ID, 'diventa-volontario')
    out = [K.intro(t, 'Diventa volontario', ['Sei ore a settimana,', 'una vita intera.'],
                   'Non servono esperienze né lauree: servono diciotto anni, quattro mesi di corso e la voglia di esserci '
                   'una sera a settimana.', bg=K.photo_bg('pexels-33317473.jpg'), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il percorso', ['Dal corso', 'al primo turno.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'La serata', 'Una sera per conoscerci: vi raccontiamo come funziona.'),
                              ('02', 'Il corso', 'Quattro mesi, due sere a settimana, con l’esame finale.'),
                              ('03', 'L’affiancamento', 'Dieci turni accanto a un capoturno esperto.'),
                              ('04', 'In turno', 'Sei ore a settimana, il giorno che scegliete voi.')], numero_font=MONO)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono', True), K.campo('text', 'Età', 'eta'),
             K.campo('select', 'Quando potreste', 'quando', opzioni='Di giorno\nDi sera\nDi notte\nNel fine settimana'),
             K.campo('textarea', 'Perché vorreste farlo', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'iscriviti', 'Il prossimo corso', ['12 gennaio,', 'in sede alle 20:30.'],
                                'Vi invitiamo alla serata di presentazione: senza impegno.', VOCI, campi,
                                'Sirena — nuovo volontario', 'Iscrivimi', 'Grazie! Vi chiamiamo per la serata di presentazione.'))
    out.append(K.domande(t, 'Prima di iscrivervi', ['Le domande', 'di chi comincia.'], [
        ('Serve essere infermieri o medici?', 'No. Il corso insegna tutto quello che serve, dal massaggio cardiaco alla barella.'),
        ('Quanto tempo ci vuole?', 'Un turno di sei ore a settimana, più una serata di aggiornamento al mese.'),
        ('E se mi fa impressione il sangue?', 'Succede a molti, all’inizio. Si può fare anche il trasporto dei pazienti o '
                                              'la centrale.'),
        ('C’è un limite di età?', 'Dai diciotto anni. Il nostro volontario più anziano ne ha settantadue.'),
    ], 'si-faq'))
    return out


def build_sostieni():
    t = Tpl(ID, 'sostienici')
    out = [K.intro(t, 'Sostienici', ['Un’ambulanza costa', 'quanto una casa.'],
                   'Ottantamila euro, più la benzina, le revisioni, i corsi. Ogni euro che ci date resta qui, a Treviso, e '
                   'trovate tutto nel bilancio pubblicato ogni anno.', bg=K.video_bg(V_STRADA), size=92)]
    out.append(t.section([
        t.one([K.sechead(t, 'Dona', ['Quanto volete,', 'quello che conta.'], size=56,
                         tagline='Le donazioni si detraggono: vi mandiamo la ricevuta a casa.')]),
        K.piani(t, [
            ('Un turno', '20', 'una volta', 'La benzina di un turno di notte\nLa ricevuta per la detrazione', False, ''),
            ('Un volontario', '120', 'all’anno', 'La divisa e il corso di un nuovo volontario\nIl nome sulla bacheca della '
             'sede', True, 'il più scelto'),
            ('Un defibrillatore', '1.500', 'una volta', 'Un defibrillatore nel vostro paese\nIl corso per usarlo, gratis',
             False, ''),
        ], 'Dona', SOSTIENI + '#dona', nota='Il 5×1000: nella dichiarazione dei redditi, il codice fiscale 00000000000.'),
    ], padding='large', width='large', html_id='dona', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dove vanno', ['Il bilancio 2025,', 'riga per riga.'], size=56)]),
        t.one([P.righe(K, t, [('41%', 'I mezzi', 'Benzina, revisioni, assicurazioni', '96.000 €'),
                              ('22%', 'I corsi', 'Volontari, scuole, aziende', '52.000 €'),
                              ('19%', 'Le attrezzature', 'Defibrillatori, barelle, ossigeno', '45.000 €'),
                              ('18%', 'La sede', 'Bollette, pulizie, riparazioni', '42.000 €')], SOSTIENI, titolo=32)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_SFOCATO, 'Grazie', 'Ogni uscita <em>è anche vostra.</em>',
                             'Duemilatrecentoquaranta volte, l’anno scorso.', overlay=24))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · indirizzo non trovato', color=K.acc_media, align='center'),
                       K.headline(t, 'Questo indirizzo <em>non c’è.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Per le emergenze chiamate il 112. Per il resto, torniamo alla home.', size=17,
                              align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_NOTTE2, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Associazioni e cause', 'Il lampeggiante',
    ['volontariato', 'soccorso', 'ambulanze', 'primo soccorso', 'treviso', 'scuro', 'slider pro', '3d'],
    'Un’associazione di volontari del soccorso, la notte di un turno: in apertura un lampeggiante sul video della città, '
    'con la cupola che gira in 3D e i fasci di luce che spazzano lo schermo, e il battito che pulsa sotto il titolo; i '
    'servizi da girare, la storia, il corso per diventare volontari e le donazioni col bilancio.',
    [('Chi siamo', NOI), ('Diventa volontario', VOLONTARIO), ('Sostienici', SOSTIENI)],
    [('home', 'Home', None), ('chi-siamo', 'Chi siamo', 'chi-siamo'),
     ('diventa-volontario', 'Diventa volontario', 'diventa-volontario'), ('sostienici', 'Sostienici', 'sostienici')],
    HEX, '\'Public Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Barlow\', system-ui, sans-serif',
    '\'Source Code Pro\', ui-monospace, Menlo, monospace', ['Public Sans', 'Barlow', 'Source Code Pro'], corsivo_di=[],
    peso_titoli='700', raggio=10, grana=0, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'battito'
THEME['cursor']['ring_color'] = HEX['light']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'chi-siamo.json': build_noi(), 'diventa-volontario.json': build_volontario(),
                        'sostienici.json': build_sostieni(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
