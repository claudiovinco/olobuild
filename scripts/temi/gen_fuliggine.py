# -*- coding: utf-8 -*-
"""Genera il tema «Fuliggine» (id fuliggine): spazzacamini a Santa Maria Maggiore, in Val Vigezzo, la valle degli
spazzacamini — pulizia di canne fumarie, camini e stufe, la videoispezione, il certificato per l'assicurazione, i
comignoli rifatti.

La trovata: «puliscimi». L'apertura è coperta di fuliggine, con la scritta fatta col dito come sui vetri delle auto
sporche. Passandoci sopra col mouse (o col dito sul telefono) la fuliggine se ne va dove passa lo scovolo; pulita più di
metà, il resto si stacca e sotto c'è il camino acceso con la frase «Adesso tira». È il «livello da pulire» dello Slider
Pro (1.4.981), con lo stato camino = pulito che fa comparire i pulsanti; «Pulisci tu» lo fa per chi non vuole strofinare.

Tema chiaro come una canna pulita: calce, nero fuliggine, il rosso del fuoco. Titoli in Crimson Pro, testo in Nunito
Sans, dati in Fira Code. Cursore: scovolo.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'fuliggine'
NAME = 'Fuliggine'

HEX = {
    'primary': '#1F1C1A', 'primary_contrast': '#FFFFFF', 'secondary': '#6B655E', 'secondary_contrast': '#FFFFFF',
    'accent': '#B23E1A', 'dark': '#12100F', 'light': '#FFFFFF', 'muted': '#EBE5DB', 'muted_contrast': '#1F1C1A',
    'text': '#1F1C1A', 'text_muted': '#5C5650', 'background': '#F6F2EA', 'border': '#D8D0C3', 'link': '#B23E1A',
}
HOME, SERVIZI, VALLE, PRENOTA = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:la-valle}}', '{{PAGINA:prenota}}'
MAIL, PHONE, PHONE_TEL = 'spazzacamini@fuliggine.example', '0324 000 1902', 'tel:+390324001902'
LAT, LNG = '46.1360', '8.4600'

V_FUOCO, V_TETTO, V_COMIGNOLO, V_STUFA, V_CAMINETTO, V_FUMI = (
    'pexels-video-13081949', 'pexels-video-35504492', 'pexels-video-35636667', 'pexels-video-40576579', 'pexels-video-10482174',
    'pexels-video-10799308')
VIDEOS = [V_FUOCO, V_TETTO, V_COMIGNOLO, V_STUFA, V_CAMINETTO, V_FUMI]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(PIETRO, DUE_TETTO, SCALA_BN, STATUA, TETTI_SERA, TEGOLE, TETTO_LAGO, ARDESIA, COPPI, COMIGNOLI, FUOCO_ALTO, FUOCO_LARGO, BRACI,
 FUOCO_BASSO, STUFA_FUOCO, STUFA_NERA, STUFA_BUIO, STUFA_ROSSA, STUFA_LEGNA, LEGNA_1, LEGNA_2, CASE_PIETRA, BAITE) = f(
    12341265, 34020199, 36009570, 39501972, 38097885, 17191633, 22711618, 13075346, 11569721, 36027487, 5551173, 35160053, 9877234,
    6375887, 6747098, 14545484, 39626992, 36803100, 5213535, 4148362, 8827814, 7946836, 15122602)
PHOTOS = [PIETRO, DUE_TETTO, SCALA_BN, STATUA, TETTI_SERA, TEGOLE, TETTO_LAGO, ARDESIA, COPPI, COMIGNOLI, FUOCO_ALTO, FUOCO_LARGO,
          BRACI, FUOCO_BASSO, STUFA_FUOCO, STUFA_NERA, STUFA_BUIO, STUFA_ROSSA, STUFA_LEGNA, LEGNA_1, LEGNA_2, CASE_PIETRA, BAITE]
DISEGNI = ['fuliggine-lastra.jpg', 'fuliggine-lastra-telefono.jpg']
LOGHI = ['fuliggine-logo-chiaro.png', 'fuliggine-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#FFB27A')
C = K.c
CHIARO = C['chiaro']
FONT_T = 'Crimson Pro'
VOCI = [('map-pin', 'Piazza Risorgimento 3 · Santa Maria Maggiore', PRENOTA + '#zone'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 180, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '526/112', 'Fuliggine: spazzacamini in Val Vigezzo dal 1902. Canne fumarie, '
                   'camini e stufe puliti, la videoispezione e il certificato. Da novembre a marzo il telefono squilla.',
                   [('I servizi', SERVIZI), ('La valle', VALLE), ('Prenota', PRENOTA), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Lunedì — sabato 8 — 18<br>Settembre e ottobre i posti si prenotano prima', VOCI,
                   '© 2026 Fuliggine · Santa Maria Maggiore (VB) · P.IVA 00000000000',
                   [('facebook', 'https://facebook.com'), ('instagram', 'https://instagram.com')], scuro=False)


# ── L'apertura: puliscimi ──
def apertura(t):
    m = K.mob
    PULITO = lambda rit=0, eff='su': {'nome': 'camino', 'vale': 'pulito', 'effetto': eff, 'durata': 700, 'ritardo': rit}
    SPORCO = {'nome': 'camino', 'vale': '!pulito', 'effetto': 'fade', 'durata': 500}
    pul = lambda: {'attivo': True, 'pennello': 95, 'soglia': 52, 'stato': 'camino=pulito', 'colore': '#1D1B19'}
    L = K.veli('fu-velo', 'basso', 30, 60, colore=C['nero'], forza=78, mobile_da=50) + [
        # sotto la fuliggine: il camino acceso e la frase
        K.livello('fu-o', contenuto='Fuliggine · spazzacamini · Val Vigezzo, dal 1902', x=15, y=30, w=70, size=13, peso='500',
                  align='center', colore=mix(CHIARO, 85), letterSpacing=3, textTransform='uppercase', fontFamily='Fira Code', ritardo=0,
                  responsive={'mobile': {'fontSize': m(10), 'x': 5, 'y': 30, 'width': 90}}),
        K.livello('fu-t', contenuto='Adesso tira.', tag='h1', x=10, y=35, w=80, size=150, peso='600', align='center', colore=CHIARO,
                  fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0,
                  responsive={'mobile': {'fontSize': m(50), 'x': 5, 'y': 34, 'width': 90}}),
        K.livello('fu-s', contenuto='Canne fumarie, camini e stufe puliti, con la videoispezione e il certificato per l’assicurazione.',
                  x=27, y=56, w=46, size=19, peso='400', align='center', colore=mix(CHIARO, 92), lineHeight=1.5, entra='fadeIn',
                  ritardo=0, responsive={'mobile': {'fontSize': m(15), 'x': 8, 'y': 44, 'width': 84}}),
        # la fuliggine: una lastra per il computer, una alta per il telefono (fuori tela sugli altri formati)
        K.livello('fu-lastra', tipo='image', imageSrc=K.img(DISEGNI[0]), imageAlt='', objectFit='cover', x=0, y=0, w=100, h=100,
                  entra='fadeIn', ritardo=0, durata=300, pulisci=pul(),
                  responsive={'mobile': {'x': -200, 'y': 0, 'width': 100, 'height': 100}}),
        K.livello('fu-lastra-tel', tipo='image', imageSrc=K.img(DISEGNI[1]), imageAlt='', objectFit='cover', x=-200, y=0, w=100, h=100,
                  entra='fadeIn', ritardo=0, durata=300, pulisci=pul(),
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}}),
        # sopra la fuliggine: il suggerimento, e chi non vuole strofinare
        K.livello('fu-h', contenuto='Passateci sopra: lo scovolo lo avete voi.', x=25, y=84, w=50, size=17, peso='600', align='center',
                  colore='#C9C1B6', fontFamily='Fira Code', ritardo=600, stato=SPORCO, animLoop='pulseLoop', animLoopDuration=2400,
                  responsive={'mobile': {'fontSize': m(13), 'x': 6, 'y': 80, 'width': 88}}),
        K.livello('fu-p', tipo='button', contenuto='Pulisci tu', x=45, y=90, w='auto', size=13, peso='600', colore='#F6F2EA',
                  bgColor='transparent', padding=10, borderRadius=2, borderWidth=1, borderColor='#8A837A', buttonUrl='#',
                  entra='fadeIn', ritardo=900, stato=SPORCO,
                  action={'type': 'setState', 'target': 'camino', 'value': 'pulito', 'label': 'Pulisci tutta la fuliggine'},
                  hover={'bgColor': '#F6F2EA', 'color': '#1F1C1A', 'duration': 200},
                  responsive={'mobile': {'x': 36, 'y': 86, 'fontSize': m(12)}}),
        # pulito: i pulsanti
        K.livello('fu-b1', tipo='button', contenuto='Prenota la pulizia', x=36, y=68, w='auto', size=16, peso='700', colore='#1F1C1A',
                  bgColor='#FFB27A', padding=18, borderRadius=2, buttonUrl=PRENOTA, entra='fadeIn', ritardo=0, stato=PULITO(300),
                  hover={'bgColor': '#FFFFFF', 'duration': 250}, responsive={'mobile': {'x': 8, 'y': 54, 'fontSize': m(14)}}),
        K.livello('fu-b2', tipo='button', contenuto='I servizi', x=53, y=68, w='auto', size=16, peso='700', colore=CHIARO,
                  bgColor='transparent', padding=18, borderRadius=2, borderWidth=1, borderColor=mix(CHIARO, 60), buttonUrl=SERVIZI,
                  entra='fadeIn', ritardo=0, stato=PULITO(420), hover={'bgColor': mix(CHIARO, 16), 'duration': 250},
                  responsive={'mobile': {'x': 8, 'y': 60.5, 'fontSize': m(14)}}),
        K.livello('fu-r', tipo='button', contenuto='Sporca di nuovo', x=86, y=90, w='auto', size=12, peso='500', colore=mix(CHIARO, 75),
                  bgColor='transparent', padding=8, borderRadius=2, buttonUrl=HOME, entra='fadeIn', ritardo=0, stato=PULITO(1500, 'fade'),
                  hover={'color': CHIARO, 'duration': 200}, responsive={'mobile': {'x': 6, 'y': 93, 'fontSize': m(11)}}),
    ]
    sl = [K.diapositiva('fu-1', K.sfondo_video(V_FUOCO, overlay=0.18, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, stati='camino=sporco',
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Fuliggine · Val Vigezzo, dal 1902', ['Un camino pulito', 'scalda di più.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Dalla Val Vigezzo gli spazzacamini partivano a piedi per Milano e Parigi. Noi siamo rimasti: '
                                'puliamo canne fumarie, camini e stufe da Domodossola al Lago Maggiore, con la videoispezione e '
                                'il certificato che chiede l’assicurazione.', size=17),
                      K.button(t, 'La valle', VALLE, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('flame', 'Il fuoco <b>tira meglio</b>'), ('file-check', 'Certificato <b>per l’assicurazione</b>'),
                                ('shield-check', 'Teli <b>e aspiratore</b>'), ('calendar', 'Una volta <b>all’anno</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa puliamo', ['Dal comignolo', 'alla stufa.'], size=60)]),
        t.one([P.schede_icone(K, t, [
            ('01', 'canne fumarie', 'Canne', '', 'Lo scovolo dal tetto o dal basso, e l’aspiratore: la fuliggine non entra in casa.', 'flame'),
            ('02', 'camini', 'Camini', '', 'Il focolare, la cappa e la canna; il controllo del tiraggio a fuoco acceso.', 'house'),
            ('03', 'stufe', 'Stufe', '', 'Stufe a legna e a pellet: giro fumi, guarnizioni, il vetro che torna trasparente.', 'thermometer'),
            ('04', 'videoispezione', 'Ispezioni', '', 'La telecamera nella canna: vediamo le crepe e ve le facciamo vedere.', 'search')],
            fondo=C['sala'])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_COMIGNOLO, 'Sul tetto', 'Dove il fumo <em>deve uscire.</em>',
                             'Il comignolo si controlla dal tetto: se tira male, il fumo torna in casa. Lo rifacciamo in pietra o in rame.',
                             overlay=18))
    out.append(t.section([
        t.row([t.col([K.image(t, PIETRO, 'Uno spazzacamino col cilindro e la giacca coi bottoni', ratio='4/5', position='center 30%')],
                     wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Mio nonno partiva a ottobre con lo scovolo in spalla e tornava a primavera. Io torno la sera, '
                                        'ma il lavoro è quello: la canna deve tirare.', 'Pietro Ferraris, spazzacamino', size='38')],
                     wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('La stufa faceva fumo in casa da due inverni. Mezz’ora di lavoro, e adesso il fuoco parte con un fiammifero.', 'Marta', 'Malesco'),
        ('Teli dappertutto, l’aspiratore acceso: in sala non c’era un granello nero.', 'Luigi', 'Druogno'),
        ('Ci hanno fatto vedere la crepa nella canna con la telecamera. Senza, non l’avremmo mai saputo.', 'Silvia e Andrea', 'Domodossola')],
        'Chi ci ha chiamato', ['Tre case,', 'tre fuochi che tirano.'], fondo=False))
    out.append(K.domande(t, 'Prima di chiamarci,', 'le domande di tutti.', [
        ('Ogni quanto si pulisce la canna?', 'Una volta all’anno per la legna e il pellet, prima dell’inverno; ogni due anni per il gas.'),
        ('Si sporca la casa?', 'No: teli sul pavimento e l’aspiratore collegato alla canna. La fuliggine va nel sacco, non in sala.'),
        ('Il certificato serve davvero?', 'Sì: se c’è un incendio l’assicurazione lo chiede. Ve lo lasciamo firmato, con la data.'),
    ], 'fu-faq'))
    return out


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Canne, camini,', 'stufe, comignoli.'],
                   'Pulizia, videoispezione, risanamento delle canne e comignoli nuovi, con il certificato per l’assicurazione.',
                   bg=K.video_bg(V_STUFA, overlay=30), size=96)]
    out.append(t.section([t.one([P.elenco_hover(K, t, [
        ('Pulizia della canna', 'una volta all’anno', 'Scovolo dal tetto o dal basso, aspiratore, il certificato.', COMIGNOLI),
        ('Camini aperti', 'focolare e cappa', 'La cappa sgrassata, la canna pulita, il tiraggio provato col fuoco acceso.', FUOCO_LARGO),
        ('Stufe a legna e pellet', 'giro fumi e vetro', 'Le guarnizioni nuove, il vetro trasparente, la ventola pulita.', STUFA_FUOCO),
        ('Videoispezione', 'la canna vista dentro', 'La telecamera fino al comignolo: le crepe si vedono prima che facciano danni.', ARDESIA),
        ('Comignoli', 'in pietra o in rame', 'Rifatti sul tetto, con la cuffia antivento.', TEGOLE),
    ], PRENOTA, size=46)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['Il prezzo,', 'prima di salire.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'IVA compresa, in Val Vigezzo e Ossola. Sul lago e a Domodossola 15 € di uscita.')], wm='1-3', ws='1-1'),
               t.col([P.listino(K, t, [
                   ('Pulizia della canna', 'Fino a due piani, col certificato', '95 €', '', 'la più chiesta'),
                   ('Pulizia della stufa', 'Giro fumi, vetro, guarnizioni', '85 €', '', ''),
                   ('Canna e stufa insieme', 'Lo stesso giorno', '150 €', '', ''),
                   ('Videoispezione', 'Con le foto e la relazione', '120 €', '', ''),
                   ('Comignolo nuovo', 'In pietra o in rame, montato', 'da 480 €', '', '')], immagini=False)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([X.certificati(K, t, [
        ('UNI 10683', 'Installazione', 'Generatori a legna e pellet: installazione, verifica e manutenzione', 'flame'),
        ('UNI 10847', 'Pulizia', 'Pulizia e controllo dei sistemi di evacuazione dei fumi', 'badge-check'),
        ('DM 37/08', 'Abilitazione', 'Impianti di riscaldamento e canne fumarie (lettera C)', 'file-check')])])],
        padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    return out


def build_valle():
    t = Tpl(ID, 'la-valle')
    out = [K.intro(t, 'La valle', ['La valle', 'degli spazzacamini.'],
                   'Dalla Val Vigezzo, per tre secoli, i ragazzi partivano a ottobre per pulire i camini delle città. Qui c’è il loro museo, '
                   'e ogni settembre il loro raduno.', bg=K.video_bg(V_TETTO, overlay=26), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Centovent’anni', ['Quattro generazioni', 'di scovoli.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1902', 'il bisnonno', SCALA_BN, 'Partire', 'Da Malesco', 'a Milano', 'Giuseppe parte a dodici anni con lo scovolo e la scala.',
             '12', 'anni'),
            ('1951', 'il nonno', STATUA, 'Tornare', 'La bottega', 'in valle', 'Apre la bottega in piazza: le città hanno il gas, la valle la legna.',
             '1', 'bottega'),
            ('2026', 'Pietro', DUE_TETTO, 'Restare', 'La telecamera', 'e lo scovolo', 'La videoispezione, l’aspiratore, ma il tetto è lo stesso.',
             '4', 'generazioni')], icona='flame')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Sui tetti', ['La valle', 'dall’alto.'], size=56)]),
        P.fila_foto(K, t, [(CASE_PIETRA, 'Case di pietra'), (BAITE, 'Baite e comignoli'), (TETTO_LAGO, 'I tetti sul lago'),
                           (COPPI, 'I coppi e i comignoli')], ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_FUMI, 'Il raduno', 'A settembre, <em>mille scovoli.</em>',
                          'Il primo fine settimana di settembre gli spazzacamini di tutto il mondo tornano in valle: noi ci siamo, col cilindro.',
                          'Prenota prima dell’inverno', PRENOTA + '#scrivi', video=True))
    return out


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [K.intro(t, 'Prenota', ['Prima dell’inverno,', 'non dopo.'],
                   'Da novembre a marzo le giornate sono piene: prenotate a settembre e ottobre, e il primo fuoco tira.',
                   bg=K.video_bg(V_CAMINETTO, overlay=26), size=88)]
    conf = X.preventivo(K, t, [
        ('cosa', 'Cosa puliamo', 'piu', True, 'Si possono sommare: lo stesso giorno costa meno.'),
        ('casa', 'La casa', 'uno', True, '')], [
        ('canna', 'cosa', 'Canna fumaria', 'Col certificato.', 95, 'la più chiesta', True),
        ('stufa', 'cosa', 'Stufa a legna o pellet', 'Giro fumi e vetro.', 85, '', False),
        ('camino', 'cosa', 'Camino aperto', 'Focolare e cappa.', 70, '', False),
        ('video', 'cosa', 'Videoispezione', 'Con le foto.', 120, '', False),
        ('due', 'casa', 'Fino a due piani', '', 0, '', True),
        ('tre', 'casa', 'Tre piani o più', 'La scala lunga.', 30, '', False),
        ('baita', 'casa', 'Baita in quota', 'Raggiungibile in auto.', 25, '', False)],
        'La vostra pulizia', PRENOTA + '?lavoro={configurazione}#scrivi', 'IVA compresa · certificato compreso', testo_pulsante='Prenota')
    out.append(X.sezione_preventivo(K, t, 'La pulizia', ['Componete,', 'il totale si fa da sé.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email'), K.campo('text', 'Comune', 'comune', True),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'La stufa, il camino', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Prenota', ['Vi richiamiamo', 'per il giorno.'],
                                'Entro due giorni; d’inverno anche il sabato.', VOCI, campi, 'Fuliggine — prenotazione', 'Prenota',
                                'Grazie! Vi richiamiamo entro due giorni.'))
    out.append(X.zone(K, t, 'Dove saliamo', ['Val Vigezzo,', 'Ossola e lago.'], 'I comuni dove andiamo ogni settimana d’inverno.',
                      [('Val Vigezzo', 'tutti i comuni'), ('Domodossola', 'ogni settimana'), ('Valle Cannobina', 'ogni settimana'),
                       ('Lago Maggiore', 'da Cannobio a Verbania'), ('Valle Antigorio', 'su appuntamento'), ('Centovalli', 'su appuntamento')],
                      LAT, LNG, 'Fuliggine · la bottega', False, HEX['accent']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · canna otturata', color=K.acc_media, align='center'),
                       K.headline(t, 'Qui il fumo <em>non esce.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=CHIARO, accento=K.acc_media),
                       K.text(t, 'Questa pagina è piena di fuliggine. Torniamo al focolare.', size=17, align='center', color=mix(CHIARO, 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_CAMINETTO, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'Puliscimi',
    ['artigiani', 'spazzacamino', 'canne fumarie', 'stufe', 'camini', 'chiaro', 'slider pro', 'interattivo'],
    'Spazzacamini in Val Vigezzo: l’apertura è coperta di fuliggine con la scritta «puliscimi» fatta col dito; passandoci '
    'sopra col mouse o col dito la fuliggine se ne va e sotto c’è il camino acceso. Poi cosa si pulisce, il listino, le '
    'norme, la storia della valle degli spazzacamini e la prenotazione prima dell’inverno.',
    [('I servizi', SERVIZI), ('La valle', VALLE), ('Prenota', PRENOTA)],
    [('home', 'Home', None), ('servizi', 'I servizi', 'servizi'), ('la-valle', 'La valle', 'la-valle'), ('prenota', 'Prenota', 'prenota')],
    HEX, '\'Nunito Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Crimson Pro\', Georgia, serif',
    '\'Fira Code\', ui-monospace, Menlo, monospace', ['Nunito Sans', 'Crimson Pro', 'Fira Code'], corsivo_di=['Crimson Pro'],
    peso_titoli='600', raggio=2, grana=6, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'scovolo'
THEME['cursor']['ring_color'] = '#2A2624'
THEME['cursor']['dot_color'] = '#C0782F'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'la-valle.json': build_valle(), 'prenota.json': build_prenota(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
