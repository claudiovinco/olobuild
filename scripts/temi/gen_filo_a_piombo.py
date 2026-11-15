# -*- coding: utf-8 -*-
"""Genera il tema «Filo a Piombo» (id filo-a-piombo): un'impresa edile di Bergamo, muratori dal 1971 — ristrutturazioni
di case e cascine, muri in mattoni e in pietra, intonaci a calce, cappotti, consolidamenti; il cantiere seguito da un
geometra della ditta.

La trovata: «il muro si alza». Lo slider resta fermo mentre si scorre: sopra il video del muratore che posa, entrano da
destra e da sinistra tre file di mattoni, una sopra l'altra, sfalsate come si deve; finito il muro in cima scende la
livella e la bolla, dopo un paio di oscillazioni, va in mezzo alle tacche. A destra il filo a piombo dondola, dritto.

Tema chiaro: calce, cotto, grafite e il giallo della livella. Titoli in Epilogue, testo in Public Sans, dati in Overpass
Mono. Cursore: livella.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'filo-a-piombo'
NAME = 'Filo a Piombo'

HEX = {
    'primary': '#9E3D26', 'primary_contrast': '#FFFFFF', 'secondary': '#2E3A44', 'secondary_contrast': '#FFFFFF',
    'accent': '#E8B923', 'dark': '#1B1A18', 'light': '#FFFFFF', 'muted': '#E4DFD6', 'muted_contrast': '#1E1D1B',
    'text': '#1E1D1B', 'text_muted': '#5A554E', 'background': '#EEEAE3', 'border': '#CFC7BA', 'link': '#9E3D26',
}
HOME, CANTIERI, RISTRUTTURARE, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:cantieri}}', '{{PAGINA:ristrutturare}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'cantieri@filoapiombo.example', '035 000 1971', 'tel:+390350001971'
LAT, LNG = '45.6983', '9.6773'

V_MURO, V_MALTA, V_CANTIERE, V_MATTONI, V_PARETE, V_RASARE = (
    'pexels-video-30037263', 'pexels-video-34572321', 'pexels-video-37500519', 'pexels-video-15990535', 'pexels-video-5092252',
    'pexels-video-34572319')
VIDEOS = [V_MURO, V_MALTA, V_CANTIERE, V_MATTONI, V_PARETE, V_RASARE]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(ERNESTO, MALTA_MANO, MATTONE_MANO, CAZZUOLA, LIVELLA_CASCO, MARCO, LIVELLA_MURO, MURO_1, MURO_2, MURO_OMBRA, MURO_3, INTONACO,
 RASATURA, CASA_BIANCA, CASA_PIETRA, CASALE, PIETRA_FINESTRE, CASA_PERSIANE, RUDERE, LIVELLA_GIALLA, LIVELLA_LASER,
 INTERNO_BUIO) = f(30640160, 10383588, 38561974, 19688828, 8488008, 8482823, 4981810, 14982351, 17391841, 1101125, 6205564,
                   38561968, 5691612, 27531965, 19145511, 1631226, 37362298, 17325347, 13003058, 6251067, 5853931, 8586035)
PHOTOS = [ERNESTO, MALTA_MANO, MATTONE_MANO, CAZZUOLA, LIVELLA_CASCO, MARCO, LIVELLA_MURO, MURO_1, MURO_2, MURO_OMBRA, MURO_3,
          INTONACO, RASATURA, CASA_BIANCA, CASA_PIETRA, CASALE, PIETRA_FINESTRE, CASA_PERSIANE, RUDERE, LIVELLA_GIALLA,
          LIVELLA_LASER, INTERNO_BUIO]
FILE = ['filo-mattoni-%d.png' % k for k in range(1, 8)]
DISEGNI = FILE + ['filo-livella.svg', 'filo-bolla.svg', 'filo-piombo.svg']
LOGHI = ['filo-a-piombo-logo-chiaro.png', 'filo-a-piombo-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#E8B923')
C = K.c
CHIARO = C['chiaro']
FONT_T = 'Epilogue'
VOCI = [('map-pin', 'Via Borgo Palazzo 210 · Bergamo', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 230, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '722/125', 'Filo a Piombo: muratori a Bergamo dal 1971. Ristrutturazioni di case '
                   'e cascine, muri in mattoni e in pietra, intonaci a calce. Dritti, a bolla, a regola d’arte.',
                   [('I cantieri', CANTIERI), ('Ristrutturare', RISTRUTTURARE), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'L’ufficio', 'Lunedì — venerdì 7:30 — 17:30<br>Il geometra su appuntamento', VOCI,
                   '© 2026 Filo a Piombo srl · Bergamo · P.IVA 00000000000 · iscritta alla Cassa Edile',
                   [('facebook', 'https://facebook.com'), ('linkedin', 'https://linkedin.com')], scuro=False)


# ── L'apertura: il muro si alza ──
def apertura(t):
    m = K.mob
    L = K.veli('fp-velo', 'alto', 30, 56, colore=C['nero'], forza=84, mobile_alto=24)
    L.append(K.livello('fp-piombo', tipo='image', imageSrc=K.img(DISEGNI[9]), imageAlt='', objectFit='contain', x=86, y=0, w=6, h=64,
                       entra='fadeIn', ritardo=600, durata=900, responsive={'mobile': {'x': 82, 'y': 0, 'width': 12, 'height': 30}}))
    # tre file di mattoni dal basso: ognuna entra da un lato, scende e si posa (erano sei e coprivano mezza apertura con
    # un disegno grezzo: l'utente, 13 nov 2026, «bastano 3 righe di mattoni e non 5»)
    for k in range(3):
        posa = 14 + k * 14
        lato = 1500 if k % 2 else -1500
        L.append(K.livello('fp-fila%d' % k, tipo='image', imageSrc=K.img(FILE[k % 7]), imageAlt='', objectFit='cover',
                           x=0, y=round(88.89 - 11.11 * k, 2), w=100, h=11.11, entra='fadeIn', ritardo=0, durata=300,
                           scrollMove={'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': lato, 'y': -60, 'o': 0},
                                                                          {'p': posa - 10, 'x': lato, 'y': -60, 'o': 0},
                                                                          {'p': posa - 2, 'x': 0, 'y': -8, 'o': 100},
                                                                          {'p': posa, 'x': 0, 'y': 0, 'o': 100}, {'p': 100, 'x': 0, 'y': 0, 'o': 100}]},
                           customCSS='box-shadow:0 -6px 14px rgba(0,0,0,.25);',
                           responsive={'mobile': {'x': 0, 'y': 93 - 7 * k, 'width': 100, 'height': 7}}))
    cade = [{'p': 0, 'y': -500, 'o': 0}, {'p': 50, 'y': -500, 'o': 0}, {'p': 60, 'y': 0, 'o': 100}, {'p': 100, 'y': 0, 'o': 100}]
    L.append(K.livello('fp-livella', tipo='image', imageSrc=K.img(DISEGNI[7]), imageAlt='La livella sul muro finito', objectFit='contain',
                       x=52, y=59.15, w=40, h=8.5, entra='fadeIn', ritardo=0, durata=300, scrollMove={'inizio': 0, 'fine': 100, 'tappe': cade},
                       responsive={'mobile': {'x': 10, 'y': 74.6, 'width': 80, 'height': 4.4}}))
    bolla = [{'p': 0, 'y': -500, 'o': 0, 'x': 60}, {'p': 50, 'y': -500, 'o': 0, 'x': 60}, {'p': 60, 'y': 0, 'o': 100, 'x': 60},
             {'p': 68, 'y': 0, 'o': 100, 'x': -40}, {'p': 76, 'y': 0, 'o': 100, 'x': 18}, {'p': 84, 'y': 0, 'o': 100, 'x': 0},
             {'p': 100, 'y': 0, 'o': 100, 'x': 0}]
    L.append(K.livello('fp-bolla', tipo='image', imageSrc=K.img(DISEGNI[8]), imageAlt='', objectFit='contain', x=70.8, y=62.0, w=2.4,
                       h=2.8, entra='fadeIn', ritardo=0, durata=300, scrollMove={'inizio': 0, 'fine': 100, 'tappe': bolla},
                       responsive={'mobile': {'x': 47.6, 'y': 76.07, 'width': 4.8, 'height': 1.46}}))
    finale = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 20}, {'p': 66, 'o': 0, 'y': 20}, {'p': 76, 'o': 100, 'y': 0},
                                                 {'p': 100, 'o': 100, 'y': 0}]}
    L += [
        K.livello('fp-o', contenuto='Filo a Piombo · Bergamo, dal 1971', x=6, y=10, w=50, size=13, peso='500',
                  colore=mix(CHIARO, 85), letterSpacing=3, textTransform='uppercase', fontFamily='Overpass Mono', ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 6.5, 'width': 75}}),
        K.livello('fp-t', contenuto='Muri dritti.', tag='h1', x=6, y=13, w=56, size=112, peso='800', colore=CHIARO, fontFamily=FONT_T,
                  lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=900,
                  responsive={'mobile': {'fontSize': m(36), 'x': 6, 'y': 9, 'width': 78}}),
        K.livello('fp-s', contenuto='Case e cascine ristrutturate, muri in mattoni e in pietra, intonaci a calce. Scorrete: il muro '
                                    'lo tiriamo su adesso.', x=6, y=27, w=40, size=18, peso='400', colore=mix(CHIARO, 92), lineHeight=1.5,
                  ritardo=700, responsive={'mobile': {'fontSize': m(14), 'x': 6, 'y': 15.5, 'width': 74}}),
        K.livello('fp-b1', tipo='button', contenuto='I cantieri', x=6, y=47, w='auto', size=13, peso='700', colore='#1E1D1B',
                  bgColor='#E8B923', padding=18, borderRadius=2, letterSpacing=2, textTransform='uppercase', buttonUrl=CANTIERI,
                  entra='fadeIn', ritardo=0, scrollMove=finale, hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 44, 'fontSize': m(12)}}),
        K.livello('fp-b2', tipo='button', contenuto='Un sopralluogo', x=19, y=47, w='auto', size=13, peso='700', colore='#FFFFFF',
                  bgColor='#1E1D1B', padding=18, borderRadius=2, letterSpacing=2, textTransform='uppercase', buttonUrl=CONTATTI + '#scrivi',
                  entra='fadeIn', ritardo=0, scrollMove=finale, hover={'bgColor': '#9E3D26', 'duration': 250},
                  responsive={'mobile': {'x': 48, 'y': 44, 'fontSize': m(12)}}),
    ]
    M.veli_oltre(L)
    sl = [K.diapositiva('fp-1', K.sfondo_video(V_MURO, overlay=0.18, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1300, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Filo a Piombo · Bergamo, dal 1971', ['A piombo, a bolla,', 'a regola d’arte.'], size=72,
                                layout='stack', size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo una ditta di muratori, quattordici persone e un geometra. Ristrutturiamo case di città e '
                                'cascine di campagna, consolidiamo i muri vecchi e ne tiriamo su di nuovi, coi materiali di una '
                                'volta quando servono: mattoni pieni, pietra, calce.', size=17),
                      K.button(t, 'Ristrutturare', RISTRUTTURARE, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('hard-hat', 'Un geometra <b>della ditta</b>'), ('calendar', 'Tempi <b>scritti nel contratto</b>'),
                                ('layers', 'Calce, pietra, <b>mattone pieno</b>'), ('shield-check', 'Cassa Edile <b>e sicurezza</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(X.prima_dopo(K, t, [
        (RUDERE, PIETRA_FINESTRE, 'Prima', 'Dopo', 'La cascina di Almenno', 'I muri di pietra consolidati, le finestre riaperte '
         'dove erano nel 1890.'),
        (CASALE, CASA_PERSIANE, 'Prima', 'Dopo', 'La casa di Città Alta', 'Intonaco a calce e persiane nuove: in Città Alta si '
         'lavora come si deve.')],
        'Prima e dopo', ['Muri vecchi,', 'rimessi in piedi.']))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Dalle fondamenta', 'al tetto.'], size=60)]),
        t.one([P.elenco_hover(K, t, [
            ('Ristrutturazioni', 'case e appartamenti', 'Muri, bagni, impianti coordinati, tetti: una sola ditta, un solo geometra.', CASA_BIANCA),
            ('Muri in pietra', 'consolidati e nuovi', 'Si smontano, si numerano e si rimettono: con la malta di calce, come prima.', MURO_3),
            ('Mattoni a vista', 'pieni, fatti a mano', 'Muri, archi, camini: il mattone pieno di fornace, sfalsato a regola.', MURO_1),
            ('Intonaci a calce', 'e rasature', 'La calce respira: niente muffa, niente crepe col freddo.', INTONACO),
            ('Cappotti', 'e isolamento', 'Il cappotto in fibra di legno o sughero, per pagare meno di riscaldamento.', LIVELLA_MURO),
        ], CANTIERI, size=46)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_MALTA, 'La malta', 'La calce <em>e il tempo giusto.</em>',
                             'La malta si prepara in cantiere, ogni mattina: sabbia del Serio, calce, acqua. E si aspetta che faccia presa.',
                             overlay=20))
    out.append(t.section([
        t.one([K.sechead(t, 'Un cantiere', ['Dodici settimane,', 'scritte.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'il rilievo', RUDERE, 'Misurare', 'Il rilievo', 'col laser', 'Muri, crepe, umidità: il geometra disegna quello che c’è.',
             '2', 'settimane'),
            ('2', 'le pratiche', CASA_PIETRA, 'Chiedere', 'Il permesso', 'e il progetto', 'In Comune e in Soprintendenza, se la casa è vecchia.',
             '4', 'settimane'),
            ('3', 'il cantiere', MALTA_MANO, 'Costruire', 'I lavori', 'a calendario', 'Le date nel contratto: se ritardiamo, paghiamo noi.',
             '12', 'settimane')], icona='calendar')]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.recensioni(K, t, [
        ('Il preventivo diceva dodici settimane. Hanno finito in undici, e il geometra rispondeva sempre.', 'Laura e Pietro', 'Ponteranica'),
        ('La cascina era un rudere. Adesso i muri sono quelli di prima, ma dritti.', 'Giovanni', 'Almenno San Bartolomeo'),
        ('Il muro di cinta in pietra a secco: l’hanno rifatto un sasso alla volta, uguale al vecchio.', 'Rosa', 'Sorisole')],
        'Chi ci ha chiamato', ['Tre case,', 'tre muri dritti.'], fondo=False))
    return out


def build_cantieri():
    t = Tpl(ID, 'cantieri')
    out = [K.intro(t, 'I cantieri', ['Case, cascine,', 'muri di pietra.'],
                   'Alcuni lavori finiti e in corso: quello che c’era, quello che abbiamo fatto, quanto ci abbiamo messo.',
                   bg=K.video_bg(V_CANTIERE, overlay=28), size=96)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Città Alta', 'La casa', 'del Seicento', 'Intonaco a calce, solai consolidati con il legno, le persiane rifatte uguali. '
         '16 settimane, con la Soprintendenza.', CASA_PERSIANE),
        ('Almenno', 'La cascina', 'di pietra', 'Muri smontati e rimontati, il tetto in coppi vecchi, le stalle diventate casa. '
         '30 settimane.', PIETRA_FINESTRE),
        ('Bergamo bassa', 'L’appartamento', 'degli anni Sessanta', 'Muri spostati, bagno nuovo, il cappotto: 9 settimane, '
         'con la famiglia al piano di sopra.', CASA_BIANCA),
        ('Sorisole', 'Il muro di cinta', 'a secco', 'Sessanta metri di pietra a secco, un sasso alla volta.', MURO_3)],
        altezza=420)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In cantiere', ['Le mani', 'e gli attrezzi.'], size=56)]),
        P.fila_foto(K, t, [(MATTONE_MANO, 'Il mattone posato col guanto'), (CAZZUOLA, 'La cazzuola e i mattoni'),
                           (LIVELLA_GIALLA, 'La bolla della livella'), (RASATURA, 'La rasatura a spatola')], ratio='3/4'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.pannello(t, V_PARETE, 'Il mattone', 'Pieno, di fornace, <em>sfalsato.</em>',
                          'Il mattone a vista si sceglie uno per uno: il colore cambia con la cottura, e un muro bello lo mescola bene.',
                          'Un sopralluogo', CONTATTI + '#scrivi', video=True))
    return out


def build_ristrutturare():
    t = Tpl(ID, 'ristrutturare')
    out = [K.intro(t, 'Ristrutturare', ['Una ditta,', 'un geometra, un prezzo.'],
                   'Muri, bagni, impianti, tetti e pratiche: coordiniamo tutto noi, con le date e il prezzo scritti nel contratto.',
                   bg=K.photo_bg(LIVELLA_CASCO, position='center 30%'), size=88)]
    conf = X.preventivo(K, t, [
        ('lavoro', 'Il lavoro', 'uno', True, 'Indicativo, per una casa in buono stato. Il prezzo vero dopo il rilievo, gratis.'),
        ('misura', 'La casa', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('leggera', 'lavoro', 'Ristrutturazione leggera', 'Bagno, intonaci, pavimenti.', 42000, '', True),
        ('completa', 'lavoro', 'Ristrutturazione completa', 'Muri, impianti, bagni, serramenti.', 78000, 'la più chiesta', False),
        ('cascina', 'lavoro', 'Recupero di una cascina', 'Muri in pietra, tetto, solai.', 165000, '', False),
        ('70', 'misura', 'Fino a 70 m²', '', 0, '', True),
        ('110', 'misura', 'Da 70 a 110 m²', '', 21000, '', False),
        ('160', 'misura', 'Da 110 a 160 m²', '', 48000, '', False),
        ('cappotto', 'extra', 'Cappotto in fibra di legno', 'Per una casa di due piani.', 26000, '', False),
        ('tetto', 'extra', 'Tetto rifatto', 'Coppi, isolamento, lattoneria.', 34000, '', False),
        ('calce', 'extra', 'Intonaci a calce', 'Al posto del cemento.', 6500, '', False)],
        'Il vostro cantiere', CONTATTI + '?lavoro={configurazione}#scrivi', 'IVA esclusa · rilievo gratis')
    out.append(X.sezione_preventivo(K, t, 'Il preventivo', ['Componete il cantiere,', 'il totale si fa da sé.'], '', conf))
    out.append(t.section([t.one([X.certificati(K, t, [
        ('SOA OG2', 'Beni tutelati', 'Restauro di edifici vincolati dalla Soprintendenza', 'landmark'),
        ('SOA OG1', 'Edifici civili', 'Costruzione e ristrutturazione di edifici civili', 'house'),
        ('ISO 45001', 'Sicurezza', 'Il sistema per la salute e la sicurezza dei cantieri', 'hard-hat'),
        ('Cassa Edile', 'Regolarità', 'Contributi e formazione di tutte le persone in regola', 'badge-check')])])],
        padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima di ristrutturare,', 'le domande di tutti.', [
        ('Posso restare in casa durante i lavori?', 'Per lavori leggeri sì, organizziamo il cantiere una stanza alla volta. Per una '
         'ristrutturazione completa meglio di no: si finisce prima e costa meno.'),
        ('Chi fa le pratiche?', 'Il nostro geometra: CILA o permesso in Comune, Soprintendenza se serve, le detrazioni fiscali.'),
        ('E se i lavori ritardano?', 'Le date sono nel contratto: per ogni settimana di ritardo nostro c’è una penale a vostro favore.'),
    ], 'fp-faq'))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Un sopralluogo,', 'gratis.'],
                   'Veniamo a vedere con il geometra, misuriamo, e in due settimane avete il preventivo scritto.',
                   bg=K.video_bg(V_RASARE, overlay=28), size=96)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('text', 'Comune', 'comune'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'La casa, in due righe', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Il geometra', 'vi richiama.'],
                                'Entro due giorni, per fissare il sopralluogo.', VOCI, campi, 'Filo a Piombo — sopralluogo', 'Chiedi il sopralluogo',
                                'Grazie! Il geometra vi richiama entro due giorni.'))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi viene in cantiere', ['Le persone', 'della ditta.'], size=56)]),
        K.squadra(t, [(ERNESTO, 'Ernesto Locatelli', 'Capocantiere', 'Cinquant’anni di cazzuola: i muri in pietra li legge come un libro.',
                       'center 25%'),
                      (MARCO, 'Marco Pesenti', 'Geometra', 'Rilievi, pratiche e calendario: risponde sempre al telefono.', 'center 25%'),
                      (LIVELLA_CASCO, 'Samuel Okafor', 'Muratore', 'Intonaci a calce e mattoni a vista, a bolla.', 'center 25%')],
                  ratio='4/5', size='320'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.zone(K, t, 'Dove lavoriamo', ['Bergamo', 'e le valli.'], 'I cantieri di quest’anno, e quanto ci mettiamo ad arrivare.',
                      [('Bergamo e Città Alta', '15 minuti'), ('Valle Brembana', '40 minuti'), ('Valle Seriana', '35 minuti'),
                       ('Isola bergamasca', '30 minuti'), ('Lago d’Iseo', '45 minuti'), ('Bassa bergamasca', '35 minuti')],
                      LAT, LNG, 'Filo a Piombo · l’ufficio', False, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · fuori piombo', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>non è in bolla.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=CHIARO, accento=K.acc_media),
                       K.text(t, 'Qualcosa è storto: torniamo alle fondamenta.', size=17, align='center', color=mix(CHIARO, 90)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_MATTONI, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'Il muro che si alza',
    ['artigiani', 'muratore', 'impresa edile', 'ristrutturazioni', 'chiaro', 'slider pro', 'scorrimento'],
    'Muratori a Bergamo: scorrendo, sopra il video del muratore che posa entrano tre file di mattoni sfalsate, una sopra '
    'l’altra; finito il muro in cima scende la livella e la bolla, dopo un paio di oscillazioni, va in mezzo alle tacche. '
    'Poi i cantieri prima e dopo, le tappe col calendario, il preventivo da comporre e il geometra che richiama.',
    [('I cantieri', CANTIERI), ('Ristrutturare', RISTRUTTURARE), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('cantieri', 'I cantieri', 'cantieri'), ('ristrutturare', 'Ristrutturare', 'ristrutturare'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Public Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Epilogue\', system-ui, sans-serif',
    '\'Overpass Mono\', ui-monospace, Menlo, monospace', ['Public Sans', 'Epilogue', 'Overpass Mono'], corsivo_di=[],
    peso_titoli='700', raggio=2, grana=5, titoli_line='1.0', titoli_spazio='-0.03em')
THEME['cursor']['style'] = 'livella'
THEME['cursor']['ring_color'] = HEX['accent']
THEME['cursor']['dot_color'] = '#8FD16A'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'cantieri.json': build_cantieri(), 'ristrutturare.json': build_ristrutturare(),
                        'contatti.json': build_contatti(), '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
