# -*- coding: utf-8 -*-
"""Genera il tema «Morsetto» (id morsetto): elettricisti a Brescia dal 1987 — impianti di casa, quadri e salvavita,
domotica, fotovoltaico con accumulo, colonnine di ricarica; il pronto intervento sette giorni su sette.

La trovata: «è saltata la luce». L'apertura è al buio: si vede solo il salvavita al centro, nella luce di una torcia.
Cliccando la leva (o premendo Invio) la leva sale, scatta un lampo, la corrente corre nei fili e si accende la stanza
(il video delle lampadine che dondolano); ricliccando si torna al buio. Sono gli «stati» dello Slider Pro (1.4.981):
la leva imposta lo stato luce = accesa e i livelli scelgono in quale stato farsi vedere.

Tema scuro: blu notte, il giallo del filo di fase, l'azzurro dell'arco elettrico. Titoli in Red Hat Display, testo in
Red Hat Text, dati in Red Hat Mono. Cursore: arco (la scarica salta sul pulsante più vicino).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'morsetto'
NAME = 'Morsetto'

HEX = {
    'primary': '#FFD23F', 'primary_contrast': '#0B0F17', 'secondary': '#7FD8FF', 'secondary_contrast': '#0B0F17',
    'accent': '#FF7A4D', 'dark': '#05070B', 'light': '#FFFFFF', 'muted': '#141A24', 'muted_contrast': '#EAF0F7',
    'text': '#EAF0F7', 'text_muted': '#A3AFBF', 'background': '#0B0F17', 'border': '#263040', 'link': '#FFD23F',
}
HOME, SERVIZI, URGENZE, PREVENTIVO = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:pronto-intervento}}', '{{PAGINA:preventivo}}'
MAIL, PHONE, PHONE_TEL = 'impianti@morsetto.example', '030 000 1987', 'tel:+390300001987'
LAT, LNG = '45.5416', '10.2118'

V_LAMPADE, V_COPPIA, V_QUADRO, V_SOFFITTO, V_LAMPADINA, V_CAVI = (
    'pexels-video-853788', 'pexels-video-6079427', 'pexels-video-10664446', 'pexels-video-5391403', 'pexels-video-853772',
    'pexels-video-6079417')
VIDEOS = [V_LAMPADE, V_COPPIA, V_QUADRO, V_SOFFITTO, V_LAMPADINA, V_CAVI]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(QUADRO_TEC, ANZIANO_QUADRO, DONNA_BLU, DONNA_PANNELLI, COPPIA_MURO, DONNA_TRAPANO, QUADRO_ORDINE, QUADRO_MANO, QUADRO_BIANCO,
 LAMPADINA, EDISON, EDISON2, TRE_LAMPADINE, SMART, SMART_BIANCO, SOLARE_TETTO, SOLARE_SQUADRA, ELETTRICISTA_CASCO,
 INTERRUTTORE, CAMERA_SMART, PRESE, CAVI_CASA) = f(
    9679179, 32497160, 20500461, 17843269, 8961701, 8486929, 28950842, 257736, 5767595, 296322, 4074955, 543351, 11236658,
    20713462, 29091470, 9875418, 6961122, 4254164, 17828637, 17536106, 5691588, 38171148)
PHOTOS = [QUADRO_TEC, ANZIANO_QUADRO, DONNA_BLU, DONNA_PANNELLI, COPPIA_MURO, DONNA_TRAPANO, QUADRO_ORDINE, QUADRO_MANO,
          QUADRO_BIANCO, LAMPADINA, EDISON, EDISON2, TRE_LAMPADINE, SMART, SMART_BIANCO, SOLARE_TETTO, SOLARE_SQUADRA,
          ELETTRICISTA_CASCO, INTERRUTTORE, CAMERA_SMART, PRESE, CAVI_CASA]
DISEGNI = ['morsetto-salvavita-giu.svg', 'morsetto-salvavita-su.svg', 'morsetto-corrente.svg', 'morsetto-lampo.svg',
           'morsetto-buio.svg', 'pexels-8488029.jpg', 'morsetto-luce.svg']
LOGHI = ['morsetto-logo-chiaro.png', 'morsetto-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}
MEDIA |= {'pexels-8487400.jpg'}  # foto sostituita il 13 nov 2026 (patch_persone_2026-11-13.py)

K = Kit(ID, MEDIA, raggio=6, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True)
C = K.c
FONT_T = 'Red Hat Display'
VOCI = [('map-pin', 'Via Corsica 118 · Brescia', PREVENTIVO + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 210, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '738/116', 'Morsetto fa impianti elettrici a Brescia dal 1987: case, negozi, '
                   'quadri, domotica e fotovoltaico. Se salta la luce, si chiama noi.',
                   [('Servizi', SERVIZI), ('Pronto intervento', URGENZE), ('Preventivo', PREVENTIVO), ('Privacy', '/privacy-policy/')],
                   'Pronto intervento', '7 giorni su 7, dalle 7 alle 23<br>Di notte per i quadri dei negozi', VOCI,
                   '© 2026 Morsetto srl · Brescia · P.IVA 00000000000 · abilitazione DM 37/08 lettere A, B',
                   [('facebook', 'https://facebook.com'), ('instagram', 'https://instagram.com')], scuro=True)


# ── L'apertura: è saltata la luce ──
def apertura(t):
    m = K.mob
    BUIO = {'nome': 'luce', 'vale': '!accesa', 'effetto': 'fade', 'durata': 1100}
    ACCESA = lambda rit=0, eff='fade', dur=700: {'nome': 'luce', 'vale': 'accesa', 'effetto': eff, 'durata': dur, 'ritardo': rit}
    leva = {'type': 'cycleState', 'target': 'luce', 'values': 'accesa,spenta', 'segno': C['acc']}  # contorno: «Colore del contorno»
    # il salvavita al centro: 13 % della tela, alto come le sue proporzioni (200 × 360)
    sv = dict(x=43.5, y=34, w=13, h=41.6)
    sv_m = {'x': 33, 'y': 36, 'width': 34, 'height': 28}
    veli = [dict(L, stato=ACCESA(200)) for L in K.veli('mo-velo', 'sinistra', 30, 46, colore=C['nero'], forza=90, mobile_da=58)]
    veli += [dict(L, stato=ACCESA(200)) for L in K.veli('mo-veld', 'destra', 26, 42, colore=C['nero'], forza=86)[:1]]
    L = [K.livello('mo-luce', tipo='image', imageSrc=K.img(DISEGNI[6]), imageAlt='', objectFit='fill', x=0, y=0, w=100, h=100,
                   entra='fadeIn', ritardo=0, durata=400, esce='fadeOut', stato=ACCESA(100, 'fade', 1600),
                   responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}})] + veli + [
        K.livello('mo-buio', tipo='image', imageSrc=K.img(DISEGNI[4]), imageAlt='', objectFit='cover', x=0, y=0, w=100, h=100,
                  entra='fadeIn', ritardo=0, durata=400, esce='fadeOut', stato=BUIO,
                  responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}}),
        K.livello('mo-fili', tipo='image', imageSrc=K.img(DISEGNI[2]), imageAlt='', objectFit='contain', x=41.9, y=0, w=16.2, h=35,
                  entra='fadeIn', ritardo=0, durata=400, esce='fadeOut', stato=ACCESA(150),
                  responsive={'mobile': {'x': 16.5, 'y': 0, 'width': 67, 'height': 36.5}}),
        K.livello('mo-giu', tipo='image', imageSrc=K.img(DISEGNI[0]), imageAlt='Il salvavita, con la leva giù',
                  objectFit='contain', entra='fadeIn', ritardo=300, durata=700, esce='fadeOut', action=dict(leva, label='Tira su la leva: riattacca la corrente'),
                  stato={'nome': 'luce', 'vale': '!accesa', 'effetto': 'nessuno'},
                  responsive={'mobile': sv_m}, **sv),
        K.livello('mo-su', tipo='image', imageSrc=K.img(DISEGNI[1]), imageAlt='Il salvavita, con la leva su',
                  objectFit='contain', entra='fadeIn', ritardo=0, durata=300, esce='fadeOut', action=dict(leva, label='Abbassa la leva: torna il buio'),
                  stato={'nome': 'luce', 'vale': 'accesa', 'effetto': 'nessuno'},
                  responsive={'mobile': sv_m}, **sv),
        K.livello('mo-lampo', tipo='image', imageSrc=K.img(DISEGNI[3]), imageAlt='', objectFit='contain', x=40, y=29, w=20, h=35.5,
                  entra='fadeIn', ritardo=0, durata=200, esce='fadeOut', stato=ACCESA(0, 'nessuno'),
                  responsive={'mobile': {'x': 20, 'y': 31, 'width': 60, 'height': 27}}),
        # al buio: la domanda sopra, il suggerimento sotto
        K.livello('mo-o0', contenuto='Morsetto · impianti elettrici · Brescia', x=20, y=13, w=60, size=13, peso='500', align='center',
                  colore=mix(C['chiaro'], 78), letterSpacing=3, textTransform='uppercase', fontFamily='Red Hat Mono', ritardo=200,
                  stato=BUIO, responsive={'mobile': {'fontSize': m(10), 'x': 5, 'y': 10.5, 'width': 90}}),
        K.livello('mo-t0', contenuto='È saltata la luce?', tag='h1', x=10, y=17.5, w=80, size=88, peso='800', align='center',
                  colore=C['chiaro'], fontFamily=FONT_T, lineHeight=1.0, entra='maskFromBottom', ritardo=400, durata=1000, stato=BUIO,
                  responsive={'mobile': {'fontSize': m(42), 'x': 5, 'y': 14, 'width': 90}}),
        K.livello('mo-h0', contenuto='Tira su la leva del salvavita.', x=25, y=80, w=50, size=18, peso='500', align='center',
                  colore=C['acc'], ritardo=900, stato=BUIO, animLoop='pulseLoop', animLoopDuration=2400,
                  responsive={'mobile': {'fontSize': m(15), 'x': 8, 'y': 67, 'width': 84}}),
        # con la luce: a sinistra la frase, a destra le due strade
        K.livello('mo-o1', contenuto='Arriviamo in giornata', x=5, y=31, w=34, size=13, peso='500', colore=C['acc'], letterSpacing=3,
                  textTransform='uppercase', fontFamily='Red Hat Mono', ritardo=0, stato=ACCESA(500, 'su'),
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 67, 'width': 88}}),
        K.livello('mo-t1', contenuto='Ecco fatto.', tag='h2', x=5, y=35, w=36, size=92, peso='800', colore=C['chiaro'],
                  fontFamily=FONT_T, lineHeight=1.0, ritardo=0, stato=ACCESA(600, 'su'),
                  responsive={'mobile': {'fontSize': m(44), 'x': 6, 'y': 69.5, 'width': 88}}),
        K.livello('mo-s1', contenuto='Quadri, impianti di casa, domotica e fotovoltaico, a Brescia e provincia. Se salta '
                                     'qualcosa, un elettricista è da voi in due ore.', x=5, y=50, w=31, size=18, peso='400',
                  colore=mix(C['chiaro'], 90), lineHeight=1.55, ritardo=0, stato=ACCESA(750, 'su'),
                  responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 75.5, 'width': 86}}),
        K.livello('mo-b1', tipo='button', contenuto='Pronto intervento', x=66, y=44, w='auto', size=13, peso='700', colore=C['on_acc'],
                  bgColor=C['acc'], padding=18, borderRadius=6, letterSpacing=2, textTransform='uppercase', buttonUrl=URGENZE,
                  entra='fadeIn', ritardo=0, stato=ACCESA(850, 'sinistra'), hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 85, 'fontSize': m(12)}}),
        K.livello('mo-b2', tipo='button', contenuto='Il preventivo', x=66, y=53, w='auto', size=13, peso='700', colore=C['chiaro'],
                  bgColor='transparent', padding=18, borderRadius=6, borderWidth=1, borderColor=mix(C['chiaro'], 60), letterSpacing=2,
                  textTransform='uppercase', buttonUrl=PREVENTIVO, entra='fadeIn', ritardo=0, stato=ACCESA(950, 'sinistra'),
                  hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 91, 'fontSize': m(12)}}),
        K.livello('mo-n1', contenuto=PHONE + ' · 7 giorni su 7', x=66, y=63, w=28, size=15, peso='500', colore=mix(C['chiaro'], 85),
                  fontFamily='Red Hat Mono', ritardo=0, stato=ACCESA(1050, 'sinistra'),
                  responsive={'mobile': {'visible': False}}),
    ]
    sl = [K.diapositiva('mo-1', K.sfondo_video(V_LAMPADE, overlay=0.12, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, stati='luce=spenta',
                       heightMobile={'mode': 'px', 'value': 1400})


def servizi_hover(t, link):
    return P.elenco_hover(K, t, [
        ('Impianti di casa', 'nuovi e rifatti', 'Dal progetto alla dichiarazione di conformità: punti luce, prese, linee separate.', CAVI_CASA),
        ('Quadri e salvavita', 'il cuore dell’impianto', 'Il quadro rifatto in ordine, coi differenziali giusti per ogni linea.', QUADRO_ORDINE),
        ('Domotica', 'luci, tapparelle, clima', 'Interruttori smart e scenari: dal telefono o con la voce, senza rompere i muri.', INTERRUTTORE),
        ('Fotovoltaico', 'e batterie di accumulo', 'Il tetto che produce e la batteria che tiene la corrente per la sera.', SOLARE_TETTO),
        ('Colonnine di ricarica', 'per l’auto elettrica', 'In garage o nel cortile del condominio, con la potenza che si regola da sola.', PRESE),
    ], link, size=46)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Morsetto · Brescia, dal 1987', ['Impianti fatti', 'per non pensarci più.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo in nove, elettricisti e un progettista. Facciamo impianti nuovi e rifacciamo quelli '
                                'vecchi, con le carte in regola: alla fine lasciamo la dichiarazione di conformità e lo schema '
                                'del quadro attaccato allo sportello.', size=17),
                      K.button(t, 'I servizi', SERVIZI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('zap', 'Pronto intervento <b>in due ore</b>'), ('shield-check', 'Abilitati <b>DM 37/08</b>'),
                                ('file-check', 'Conformità <b>sempre</b>'), ('clock', 'Preventivo <b>in 48 ore</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Cinque lavori,', 'fatti bene.'], size=60)]),
        t.one([servizi_hover(t, SERVIZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(X.urgenze(K, t, 'Salta la corrente?', 'Chiamaci adesso.', 'Sette giorni su sette dalle 7 alle 23: in città '
                         'arriviamo in due ore. Prima ti diciamo al telefono cosa controllare.', PHONE, PHONE_TEL,
                         'Come funziona', URGENZE, ELETTRICISTA_CASCO, 'center 30%'))
    out.append(X.prima_dopo(K, t, [
        ('pexels-8488029.jpg', QUADRO_ORDINE, 'Prima', 'Dopo', 'Il quadro di una villetta', 'Fili volanti e un solo '
         'differenziale per tutta la casa: ora otto linee, ognuna col suo.'),
        (QUADRO_BIANCO, QUADRO_MANO, 'Prima', 'Dopo', 'Il negozio di via Milano', 'Il quadro rifatto in una notte, '
         'per aprire alle nove senza perdere un giorno.')],
        'Prima e dopo', ['Trascinate:', 'si vede la differenza.']))
    out.append(K.video_frase(t, V_QUADRO, 'Il quadro', 'Ogni filo <em>ha il suo nome.</em>',
                             'Sul quadro lasciamo lo schema e le etichette: chi viene dopo di noi sa dove mettere le mani.', overlay=22))
    out.append(X.recensioni(K, t, [
        ('Era saltato tutto la sera di Natale. Alle nove erano qui, alle dieci avevamo il forno acceso.', 'Paola', 'Brescia, Lamarmora'),
        ('Ci hanno rifatto l’impianto con noi dentro casa, una stanza alla volta. Polvere quasi zero.', 'Ivo e Carla', 'Rezzato'),
        ('Il fotovoltaico con la batteria: d’estate la bolletta è a zero, e ce l’hanno spiegato senza paroloni.', 'Marco', 'Gussago')],
        'Chi ci ha chiamato', ['Tre case,', 'tre sere salvate.']))
    out.append(X.lettera(K, t, 'I consigli', ['Una volta al mese,', 'due righe utili.'],
                         'Come leggere il contatore, quando cambiare il salvavita, gli incentivi per il fotovoltaico. Niente pubblicità.'))
    return out


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Dalla presa', 'al tetto.'],
                   'Impianti di casa e di negozio, quadri, domotica, fotovoltaico e ricarica: progetto, lavoro e carte, da una sola ditta.',
                   bg=K.video_bg(V_COPPIA, overlay=30), size=96)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Impianti', 'L’impianto', 'di casa', 'Nuovo o rifatto, con le linee separate per cucina, bagni e clima. Lavoriamo una stanza '
         'alla volta, anche se abitate in casa.', CAVI_CASA),
        ('Quadri', 'Il quadro', 'e i salvavita', 'Differenziali per ogni linea, scaricatori contro i fulmini, lo schema attaccato allo '
         'sportello.', QUADRO_TEC),
        ('Domotica', 'La casa', 'che ubbidisce', 'Luci, tapparelle e clima dal telefono; gli interruttori smart vanno nelle scatole '
         'di sempre.', SMART),
        ('Energia', 'Il fotovoltaico', 'con la batteria', 'Pannelli, inverter e accumulo: le pratiche col gestore le facciamo noi.',
         SOLARE_SQUADRA)], altezza=420)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['Prezzi chiari,', 'prima di iniziare.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'I prezzi più chiesti, IVA compresa. Per i lavori grandi il preventivo è gratis, in 48 ore.')],
                     wm='1-3', ws='1-1'),
               t.col([P.listino(K, t, [
                   ('Uscita in città', 'Brescia e comuni attaccati, la prima mezz’ora compresa', '45 €', '', ''),
                   ('Salvavita nuovo', 'Differenziale 30 mA, montato e provato', '120 €', '', ''),
                   ('Punto luce o presa', 'In più su un impianto esistente, con la scatola', '60 €', '', ''),
                   ('Quadro di un appartamento', 'Rifatto con sei linee, schema e conformità', 'da 780 €', '', 'il più chiesto'),
                   ('Colonnina da 7,4 kW', 'In garage, con la gestione della potenza', 'da 1.150 €', '', '')], immagini=False)],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([X.certificati(K, t, [
        ('DM 37/08', 'Abilitazione', 'Impianti elettrici, elettronici e di protezione dai fulmini (lettere A e B)', 'badge-check'),
        ('CEI 64-8', 'Norma degli impianti', 'Progetti e verifiche secondo la norma degli impianti in bassa tensione', 'file-check'),
        ('FER', 'Rinnovabili', 'Installatori qualificati per fotovoltaico e accumulo', 'sun'),
        ('PES · PAV', 'Lavori sotto tensione', 'Persone esperte e avvertite, con la formazione rinnovata', 'shield-check')])])],
        padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima di chiamarci,', 'le domande di tutti.', [
        ('Salta il salvavita appena lo rialzo: cosa faccio?', 'Staccate tutte le spine e rialzatelo: se tiene, riattaccatele una a una. '
         'Quella che lo fa saltare è il colpevole. Se salta anche senza niente attaccato, chiamateci.'),
        ('Mi lasciate la dichiarazione di conformità?', 'Sempre, con lo schema e la relazione dei materiali: serve per la casa, per '
         'l’assicurazione e se un giorno vendete.'),
        ('Lavorate anche il sabato?', 'Sì, per i lavori programmati la mattina; per le urgenze sette giorni su sette.'),
        ('Fate le pratiche per gli incentivi?', 'Per il fotovoltaico e l’accumulo sì, comprese quelle col gestore della rete.'),
    ], 'mo-faq'))
    return out


def build_urgenze():
    t = Tpl(ID, 'pronto-intervento')
    out = [K.intro(t, 'Pronto intervento', ['Al buio', 'non vi lasciamo.'],
                   'Sette giorni su sette dalle 7 alle 23, di notte per i negozi e le aziende: in città arriviamo in due ore.',
                   bg=K.video_bg(V_SOFFITTO, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come funziona', ['Quattro passi,', 'in due ore.'], size=56)]),
        t.one([P.passi(K, t, [('01', 'Chiamate', 'Al ' + PHONE + ': risponde un elettricista, non un centralino.'),
                              ('02', 'Vi guidiamo', 'Al telefono vi diciamo cosa controllare: a volte basta una spina.'),
                              ('03', 'Arriviamo', 'In città entro due ore, in provincia entro tre. Vi diciamo l’ora.'),
                              ('04', 'Ripariamo', 'Prima vi diciamo il prezzo, poi lavoriamo. Si paga a lavoro fatto.')],
                       numero_font='var(--olo-font-family-mono)')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([t.one([X.orari(K, t, [('Lunedì — venerdì', '7 — 23', 'Uscita 45 €, la prima mezz’ora compresa'),
                                                ('Sabato e domenica', '8 — 22', 'Uscita festiva 70 €'),
                                                ('Di notte', '23 — 7', 'Solo negozi e aziende con contratto')])])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    out.append(X.zone(K, t, 'Dove arriviamo', ['Brescia', 'e intorno.'], 'I tempi sono quelli di un giorno feriale, col traffico.',
                      [('Brescia città', 'entro 1 ora e mezza'), ('Rezzato, Castenedolo', 'entro 2 ore'), ('Gussago, Cellatica', 'entro 2 ore'),
                       ('Franciacorta', 'entro 2 ore e mezza'), ('Lago d’Iseo', 'entro 3 ore'), ('Lago di Garda, sponda bresciana', 'entro 3 ore')],
                      LAT, LNG, 'Morsetto · il magazzino', True, HEX['primary']))
    out.append(K.domande(t, 'Prima che arriviamo,', 'le cose da sapere.', [
        ('Quanto costa un’uscita?', '45 € nei giorni feriali, con la prima mezz’ora di lavoro; 70 € di sabato e domenica. I pezzi a parte.'),
        ('Posso pagare col bancomat?', 'Sì, col bancomat o la carta; vi mandiamo la fattura per mail.'),
        ('E se è saltata la corrente in tutto il palazzo?', 'Allora è il gestore della rete: vi diamo il numero giusto e restiamo '
         'a disposizione se serve controllare il vostro quadro.'),
    ], 'mo-faq2', bg=False))
    return out


def build_preventivo():
    t = Tpl(ID, 'preventivo')
    out = [K.intro(t, 'Il preventivo', ['Un prezzo,', 'prima di iniziare.'],
                   'Componete il lavoro qui sotto: il totale è indicativo. Poi veniamo a vedere, gratis, e lo scriviamo.',
                   bg=K.photo_bg(DONNA_BLU, position='center 40%'), size=96)]
    conf = X.preventivo(K, t, [
        ('lavoro', 'Il lavoro', 'uno', True, 'Il prezzo comprende materiali, mano d’opera e dichiarazione di conformità.'),
        ('casa', 'La casa', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('rifare', 'lavoro', 'Rifare l’impianto', 'Tracce, fili nuovi, prese e interruttori.', 3400, 'il più chiesto', True),
        ('quadro', 'lavoro', 'Solo il quadro', 'Quadro nuovo con le linee separate.', 780, '', False),
        ('domotica', 'lavoro', 'Domotica', 'Luci e tapparelle smart nelle scatole di oggi.', 1600, '', False),
        ('fv', 'lavoro', 'Fotovoltaico 6 kW', 'Pannelli, inverter e pratiche.', 8900, '', False),
        ('bilo', 'casa', 'Bilocale', 'Fino a 60 m².', 0, '', True),
        ('trilo', 'casa', 'Trilocale', 'Da 60 a 100 m².', 1100, '', False),
        ('villa', 'casa', 'Villetta', 'Oltre 100 m², su due piani.', 2600, '', False),
        ('colonnina', 'extra', 'Colonnina in garage', '7,4 kW con la gestione della potenza.', 1150, '', False),
        ('batteria', 'extra', 'Batteria di accumulo', '10 kWh, per la sera.', 5200, '', False),
        ('scaricatori', 'extra', 'Protezione dai fulmini', 'Scaricatori sul quadro.', 280, '', False)],
        'Il vostro lavoro', PREVENTIVO + '?lavoro={configurazione}#scrivi', 'IVA compresa · sopralluogo gratis')
    out.append(X.sezione_preventivo(K, t, 'Componete il lavoro', ['Scegliete,', 'il totale si fa da sé.'], '', conf))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Comune', 'comune'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Due righe sulla casa', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Veniamo a vedere,', 'gratis.'],
                                'Vi richiamiamo entro un giorno per fissare il sopralluogo.', VOCI, campi,
                                'Morsetto — preventivo', 'Chiedi il sopralluogo', 'Grazie! Vi richiamiamo entro un giorno.'))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi viene da voi', ['Le persone,', 'non una squadra a caso.'], size=56)]),
        K.squadra(t, [(ANZIANO_QUADRO, 'Bruno Ferrari', 'Fondatore', 'Ha aperto la ditta nel 1987; i quadri difficili li guarda ancora lui.',
                       'center 30%'),
                      ('pexels-8487400.jpg', 'Sara Bonetti', 'Elettricista', 'Impianti di casa e domotica: lavora una stanza alla volta.', 'center 35%'),
                      (ELETTRICISTA_CASCO, 'Davide Moretti', 'Fotovoltaico', 'Pannelli, batterie e colonnine, e le pratiche col gestore.',
                       'center 25%')], ratio='4/5', size='320'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.zone(K, t, 'Il magazzino', ['Via Corsica,', 'zona Lamarmora.'], 'Per i sopralluoghi veniamo noi; in '
                      'magazzino si passa per i pezzi, dalle 8 alle 18.', [('Orari', 'lunedì — venerdì 8 — 18'), ('Parcheggio', 'nel cortile'),
                                                                         ('Autobus', 'linea 3, fermata Corsica'), ('Telefono', PHONE)],
                      LAT, LNG, 'Morsetto · il magazzino', True, HEX['primary'], html_id='zone'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · circuito aperto', color=C['acc'], align='center'),
                       K.headline(t, 'Qui la corrente <em>non arriva.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'La pagina che cercate è staccata. Riattacchiamo dall’inizio.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LAMPADINA, overlay=45), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'È saltata la luce',
    ['artigiani', 'elettricista', 'impianti elettrici', 'pronto intervento', 'domotica', 'fotovoltaico', 'scuro', 'slider pro',
     'interattivo'],
    'Elettricisti a Brescia: l’apertura è al buio, si vede solo il salvavita nella luce di una torcia; cliccando la leva sale, '
    'scatta un lampo, la corrente corre nei fili e si accende la stanza. Poi i servizi, il pronto intervento coi tempi per '
    'zona, il listino, i quadri prima e dopo e il preventivo da comporre.',
    [('Servizi', SERVIZI), ('Pronto intervento', URGENZE), ('Preventivo', PREVENTIVO)],
    [('home', 'Home', None), ('servizi', 'Servizi', 'servizi'), ('pronto-intervento', 'Pronto intervento', 'pronto-intervento'),
     ('preventivo', 'Preventivo', 'preventivo')],
    HEX, '\'Red Hat Text\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Red Hat Display\', system-ui, sans-serif',
    '\'Red Hat Mono\', ui-monospace, Menlo, monospace', ['Red Hat Text', 'Red Hat Display', 'Red Hat Mono'], corsivo_di=[],
    peso_titoli='700', raggio=6, grana=4, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'arco'
THEME['cursor']['ring_color'] = HEX['secondary']
THEME['cursor']['dot_color'] = HEX['primary']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'pronto-intervento.json': build_urgenze(),
                        'preventivo.json': build_preventivo(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
