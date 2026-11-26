# -*- coding: utf-8 -*-
"""Genera il tema «Sifone» (id sifone): idraulici a Verona — perdite e scarichi, bagni rifatti chiavi in mano, caldaie
e pompe di calore, il pronto intervento.

La trovata: «la perdita». Nell'apertura un tubo di rame attraversa lo schermo: dal raccordo cadono le gocce e sotto si
allarga una pozza, il manometro trema nel rosso. Si clicca il raccordo che perde (o Invio): arriva la chiave inglese e lo
stringe, la perdita si ferma, l'acqua corre nel tubo fino al rubinetto, l'ago sale al verde e il video del rubinetto che gocciola
diventa acqua che scorre. Gli stati del Pro Slider (perdita = riparata).

Tema chiaro: bianco smalto, il rame dei tubi, il blu dell'acqua, il rosso della valvola. Titoli in Bricolage Grotesque,
testo in Karla, dati in IBM Plex Mono. Cursore: gocciolio.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'sifone'
NAME = 'Sifone'

HEX = {
    'primary': '#A64B22', 'primary_contrast': '#FFFFFF', 'secondary': '#1E6F9F', 'secondary_contrast': '#FFFFFF',
    'accent': '#D7372F', 'dark': '#0E1E26', 'light': '#FFFFFF', 'muted': '#E6EEF1', 'muted_contrast': '#10232B',
    'text': '#10232B', 'text_muted': '#4A5D66', 'background': '#F4F7F8', 'border': '#CFDCE1', 'link': '#1E6F9F',
}
HOME, SERVIZI, BAGNI, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:servizi}}', '{{PAGINA:bagni}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'chiamate@sifone.example', '045 000 2468', 'tel:+390450002468'
LAT, LNG = '45.4386', '10.9928'

V_GOCCIA, V_ACQUA, V_IDRAULICO, V_SOTTO, V_CALDAIA, V_TUBI, V_PERDITA = (
    'pexels-video-5698926', 'pexels-video-11593539', 'pexels-video-9890450', 'pexels-video-7584762', 'pexels-video-7647713',
    'pexels-video-5571839', 'pexels-video-32711364')
VIDEOS = [V_GOCCIA, V_ACQUA, V_IDRAULICO, V_SOTTO, V_CALDAIA, V_TUBI, V_PERDITA]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(MARTA, GINO, FURGONE, MANI_TUBI, RADIATORE_POSA, PIEGA_RAME, RACCORDI, TUBO_RAME, GOMITO, DOCCE_RAME, BAGNO_RAME, MANOMETRI,
 COLLETTORE, CALDAIA_MANI, BAGNO_DOPPIO, DOCCIA_VETRO, BAGNO_PIETRA, RUBINETTI_ROSSI, RUBINETTO_ACQUA, TERMOSTATO, POMPA_CALORE,
 RADIATORE_BIANCO, LUCA) = f(7600830, 34938441, 6196229, 6419128, 29226620, 39885416, 28169591, 3721272, 28178448, 2918,
                             6123308, 5506046, 7937300, 7859953, 35868666, 7005268, 10827349, 3616761, 861414, 12644994,
                             20046689, 36344878, 32588548)
PHOTOS = [MARTA, GINO, FURGONE, MANI_TUBI, RADIATORE_POSA, PIEGA_RAME, RACCORDI, TUBO_RAME, GOMITO, DOCCE_RAME, BAGNO_RAME,
          MANOMETRI, COLLETTORE, CALDAIA_MANI, BAGNO_DOPPIO, DOCCIA_VETRO, BAGNO_PIETRA, RUBINETTI_ROSSI, RUBINETTO_ACQUA,
          TERMOSTATO, POMPA_CALORE, RADIATORE_BIANCO, LUCA]
D = {k: 'sifone-%s.svg' % k for k in ('tubi', 'volantino', 'perdita', 'pozza', 'acqua', 'ago-giu', 'ago-su', 'chiave')}
LOGHI = ['sifone-logo-chiaro.png', 'sifone-logo-scuro.png']
MEDIA = set(PHOTOS) | set(D.values()) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=10, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False, tema_chiaro=True,
        accento_su_media='#F2B48C')
C = K.c
FONT_T = 'Bricolage Grotesque'
CHIARO = C['chiaro']
VOCI = [('map-pin', 'Via Albere 31 · Verona', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 190, scuro=False, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '591/120', 'Sifone: idraulici a Verona dal 1994. Perdite, scarichi, bagni '
                   'rifatti, caldaie e pompe di calore. Se l’acqua va dove non deve, si chiama noi.',
                   [('Servizi', SERVIZI), ('Bagni', BAGNI), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'Le urgenze', 'Tutti i giorni dalle 7 alle 22<br>Il sabato anche per le caldaie', VOCI,
                   '© 2026 Sifone snc · Verona · P.IVA 00000000000', [('facebook', 'https://facebook.com'),
                                                                     ('instagram', 'https://instagram.com')], scuro=False)


# ── L'apertura: la perdita ──
def apertura(t):
    m = K.mob
    PERDE = lambda eff='fade', dur=600: {'nome': 'perdita', 'vale': '!riparata', 'effetto': eff, 'durata': dur}
    RIPARATA = lambda rit=0, eff='fade', dur=700: {'nome': 'perdita', 'vale': 'riparata', 'effetto': eff, 'durata': dur, 'ritardo': rit}
    # si stringe il raccordo che perde (prima si girava il volantino della valvola: una perdita non si ripara così,
    # l'utente, 13 nov 2026)
    gira = {'type': 'setState', 'target': 'perdita', 'value': 'riparata', 'label': 'Stringi il raccordo che perde', 'segno': '#F2B48C'}
    # la striscia dei tubi (1600 × 420): sul computer larga quanto la tela, sul telefono più larga e spostata, così la
    # valvola e il raccordo che perde restano al centro
    banda = dict(x=0, y=45, w=100, h=46.7)
    banda_m = {'x': -28, 'y': 50, 'width': 170, 'height': 20.4}

    def pezzo(lid, nome, stato, alt='', **kw):
        if stato:
            kw['stato'] = stato
        return K.livello(lid, tipo='image', imageSrc=K.img(D[nome]), imageAlt=alt, objectFit='contain', entra='fadeIn', ritardo=0,
                         durata=500, esce='fadeOut', responsive={'mobile': dict(banda_m)}, **dict(banda, **kw))
    veli = K.veli('si-velo', 'alto', 30, 58, colore=C['nero'], forza=86, mobile_alto=34)
    L = [
        # il video del rubinetto aperto, sopra quello che gocciola, quando è riparato (sotto i veli dei testi)
        K.livello('si-acqua-video', tipo='video', videoSrc=K.img(V_ACQUA + '.mp4'), videoAutoplay=True, videoMuted=True, videoLoop=True,
                  x=0, y=0, w=100, h=100, entra='fadeIn', ritardo=0, durata=400, esce='fadeOut', stato=RIPARATA(0, 'fade', 1400),
                  objectFit='cover', responsive={'mobile': {'x': 0, 'y': 0, 'width': 100, 'height': 100}}),
    ] + veli + [
        pezzo('si-tubi', 'tubi', None, 'Un tubo di rame con la valvola e il manometro'),
        pezzo('si-pozza', 'pozza', PERDE('fade', 900)),
        pezzo('si-perdita', 'perdita', PERDE('nessuno')),
        pezzo('si-acqua', 'acqua', RIPARATA(300, 'fade', 900)),
        pezzo('si-ago-giu', 'ago-giu', PERDE('nessuno')),
        pezzo('si-ago-su', 'ago-su', RIPARATA(0, 'nessuno')),
        pezzo('si-vol', 'volantino', None),
        pezzo('si-chiave', 'chiave', RIPARATA(0, 'nessuno'), 'La chiave che stringe il raccordo'),
        # dove si clicca: il raccordo che gocciola (x 424 / 1600, y 252 / 420 della striscia dei tubi), trasparente
        K.livello('si-presa', tipo='shape', bgColor='transparent', borderRadius=999, x=22.5, y=64, w=8, h=18, entra='fadeIn',
                  ritardo=0, durata=300, esce='fadeOut', action=gira, stato=PERDE('nessuno'),
                  responsive={'mobile': {'x': 10, 'y': 57, 'width': 14, 'height': 10.5}}),
        # prima: la domanda
        K.livello('si-o0', contenuto='Sifone · idraulici · Verona', x=6, y=12, w=40, size=13, peso='500', colore=mix(CHIARO, 82),
                  letterSpacing=3, textTransform='uppercase', fontFamily='IBM Plex Mono', ritardo=200, stato=PERDE(),
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 6, 'width': 88}}),
        K.livello('si-t0', contenuto='Perde?', tag='h1', x=6, y=16, w=44, size=132, peso='800', colore=CHIARO, fontFamily=FONT_T,
                  lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=900, stato=PERDE(),
                  responsive={'mobile': {'fontSize': m(64), 'x': 6, 'y': 9, 'width': 88}}),
        K.livello('si-s0', contenuto='Una goccia al secondo sono seicento litri al mese, pagati e buttati.', x=6, y=33, w=34,
                  size=19, peso='400', colore=mix(CHIARO, 92), lineHeight=1.5, ritardo=700, stato=PERDE(),
                  responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 17, 'width': 86}}),
        # dopo: riparato
        K.livello('si-o1', contenuto='Riparato in quaranta minuti', x=6, y=12, w=40, size=13, peso='500', colore='#9FD8FF', letterSpacing=3,
                  textTransform='uppercase', fontFamily='IBM Plex Mono', ritardo=0, stato=RIPARATA(400, 'su'),
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 6, 'width': 88}}),
        K.livello('si-t1', contenuto='Asciutto.', tag='h2', x=6, y=16, w=48, size=132, peso='800', colore=CHIARO, fontFamily=FONT_T,
                  lineHeight=1.0, ritardo=0, stato=RIPARATA(500, 'su'),
                  responsive={'mobile': {'fontSize': m(64), 'x': 6, 'y': 9, 'width': 88}}),
        K.livello('si-s1', contenuto='Perdite, scarichi, caldaie e bagni rifatti a Verona. Per le urgenze siamo da voi in giornata.',
                  x=6, y=33, w=34, size=19, peso='400', colore=mix(CHIARO, 92), lineHeight=1.5, ritardo=0, stato=RIPARATA(600, 'su'),
                  responsive={'mobile': {'fontSize': m(15), 'x': 6, 'y': 17, 'width': 86}}),
        K.livello('si-b1', tipo='button', contenuto='Chiama un idraulico', x=56, y=18, w='auto', size=15, peso='700', colore='#10232B',
                  bgColor='#F2B48C', padding=18, borderRadius=10, buttonUrl=PHONE_TEL, entra='fadeIn', ritardo=0,
                  stato=RIPARATA(700, 'sinistra'), hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 78, 'fontSize': m(13)}}),
        K.livello('si-b2', tipo='button', contenuto='Il bagno nuovo', x=56, y=27, w='auto', size=15, peso='700', colore=CHIARO,
                  bgColor='transparent', padding=18, borderRadius=10, borderWidth=1, borderColor=mix(CHIARO, 60), buttonUrl=BAGNI,
                  entra='fadeIn', ritardo=0, stato=RIPARATA(800, 'sinistra'),
                  hover={'bgColor': mix(CHIARO, 14), 'borderColor': CHIARO, 'duration': 250},
                  responsive={'mobile': {'x': 6, 'y': 84, 'fontSize': m(13)}}),
        K.livello('si-r1', tipo='button', contenuto='Riapri la perdita', x=86, y=86, w='auto', size=12, peso='500', colore=mix(CHIARO, 80),
                  bgColor='transparent', padding=8, borderRadius=10, buttonUrl='#', entra='fadeIn', ritardo=0,
                  action={'type': 'setState', 'target': 'perdita', 'value': '', 'label': 'Riapri la perdita'},
                  stato=RIPARATA(1400), hover={'color': CHIARO, 'duration': 200},
                  responsive={'mobile': {'x': 6, 'y': 93, 'fontSize': m(11)}}),
    ]
    sl = [K.diapositiva('si-1', K.sfondo_video(V_GOCCIA, overlay=0.2, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, stati='perdita=',
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Sifone · Verona, dal 1994', ['L’acqua va', 'dove deve andare.'], size=72, layout='stack',
                                size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo sei idraulici e una persona al telefono che sa di tubi. Ripariamo le perdite senza '
                                'spaccare mezza casa (prima cerchiamo il punto con la termocamera), rifacciamo bagni e '
                                'cambiamo caldaie con le pratiche fatte.', size=17),
                      K.button(t, 'I servizi', SERVIZI, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([X.fiducia(K, t, [('droplet', 'Perdite trovate <b>senza demolire</b>'), ('clock', 'Urgenze <b>in giornata</b>'),
                                ('flame', 'Caldaie <b>e pompe di calore</b>'), ('receipt', 'Prezzo <b>prima di iniziare</b>')])],
              style={'margin_top': 56}),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Dal rubinetto', 'alla caldaia.'], size=60)]),
        t.one([P.schede_icone(K, t, [
            ('01', 'perdite', 'Perdite', '', 'La termocamera trova il punto: si apre una piastrella, non tutto il muro.', 'droplet'),
            ('02', 'scarichi', 'Scarichi', '', 'Lavandini, docce e colonne: disostruiti con la sonda, senza acidi.', 'wrench'),
            ('03', 'bagni', 'Bagni', '', 'Rifatti chiavi in mano in dieci giorni, piastrelle e mobili compresi.', 'bath'),
            ('04', 'calore', 'Caldaie', '', 'Cambio, manutenzione e pompe di calore, con gli incentivi.', 'flame')],
            fondo=C['sala'])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(X.urgenze(K, t, 'Acqua sul pavimento?', 'Chiudete e chiamate.', 'Chiudete il rubinetto generale (di solito sotto '
                         'il lavello o vicino al contatore) e chiamateci: in città arriviamo in giornata, spesso in due ore.',
                         PHONE, PHONE_TEL, 'Dove arriviamo', CONTATTI + '#zone', FURGONE, 'center 40%'))
    out.append(t.section([
        t.one([K.sechead(t, 'I bagni', ['Dieci giorni,', 'un bagno nuovo.'], tagline='Dal sopralluogo alle chiavi: una sola ditta '
                         'per idraulica, piastrelle, mobili e vetri della doccia.', size=60)]),
        t.one([K.coverflow(t, [(BAGNO_DOPPIO, 'Un bagno con due lavabi e il mobile in legno', 'Borgo Trento', 'due lavabi, 9 m²'),
                               (DOCCIA_VETRO, 'La doccia a filo pavimento col vetro', 'San Zeno', 'doccia a filo, 6 m²'),
                               (BAGNO_PIETRA, 'Un bagno in pietra con la doccia grande', 'Valpolicella', 'pietra e vetro'),
                               (BAGNO_RAME, 'I tubi di rame a vista sopra la vasca', 'Veronetta', 'il rame a vista'),
                               (DOCCE_RAME, 'Due docce coi tubi di rame su un muro di cemento', 'Bussolengo', 'spogliatoio')],
                              altezza=420, larghezza=360)]),
        t.one([K.button(t, 'I bagni', BAGNI, align='center', pad_top=30)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_IDRAULICO, 'In mezz’ora', 'Il rubinetto <em>che non gocciola più.</em>',
                             'Guarnizioni, cartucce, sifoni: la maggior parte delle riparazioni si fa in mezz’ora, coi pezzi nel furgone.',
                             overlay=20))
    out.append(X.recensioni(K, t, [
        ('La macchia sul soffitto del vicino: hanno trovato il tubo con la termocamera e aperto una sola piastrella.', 'Giulia', 'Borgo Roma'),
        ('Bagno rifatto in nove giorni. Il decimo hanno pulito, e sembrava che non fossero mai passati.', 'Franco e Anna', 'Montorio'),
        ('Caldaia ferma il 2 gennaio, alle undici avevamo l’acqua calda. Il prezzo era quello detto al telefono.', 'Stefano', 'San Massimo')],
        'Chi ci ha chiamato', ['Tre case,', 'tutte asciutte.']))
    out.append(K.domande(t, 'Prima di chiamarci,', 'le domande di tutti.', [
        ('Dov’è il rubinetto generale?', 'Di solito sotto il lavello della cucina, vicino al contatore dell’acqua o in cantina. Se non lo '
         'trovate, al telefono vi aiutiamo a cercarlo.'),
        ('Quanto costa un’uscita?', '50 € in città con la prima mezz’ora di lavoro; i pezzi a parte, con il prezzo detto prima.'),
        ('Fate anche le pratiche per gli incentivi?', 'Sì, per le caldaie a condensazione e le pompe di calore, compreso il libretto.'),
    ], 'si-faq'))
    return out


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [K.intro(t, 'I servizi', ['Tubi, scarichi,', 'caldaie.'],
                   'Tutto quello che passa dentro i muri di casa: lo troviamo, lo ripariamo e lo rifacciamo, con le carte in regola.',
                   bg=K.video_bg(V_SOTTO, overlay=30), size=96)]
    out.append(t.section([t.one([P.elenco_hover(K, t, [
        ('Perdite', 'con la termocamera', 'Troviamo il punto prima di rompere: una piastrella, non un muro.', MANI_TUBI),
        ('Scarichi', 'lavandini, docce, colonne', 'Sonda e telecamera: vediamo dov’è il tappo, e lo togliamo.', TUBO_RAME),
        ('Impianti', 'rame e multistrato', 'Impianti nuovi o rifatti, coi collettori in vista per ogni stanza.', COLLETTORE),
        ('Caldaie', 'cambio e manutenzione', 'Caldaie a condensazione, il libretto e il controllo dei fumi ogni anno.', CALDAIA_MANI),
        ('Pompe di calore', 'e radiatori', 'Il riscaldamento a corrente, con il fotovoltaico se c’è.', POMPA_CALORE),
    ], SERVIZI, size=46)])], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il listino', ['Il prezzo,', 'prima di iniziare.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'I lavori più chiesti, IVA compresa. Per i bagni e gli impianti il preventivo è gratis.')], wm='1-3', ws='1-1'),
               t.col([P.listino(K, t, [
                   ('Uscita in città', 'Verona e comuni attaccati, la prima mezz’ora compresa', '50 €', '', ''),
                   ('Ricerca perdite con termocamera', 'Il punto segnato sul muro, con le foto', '180 €', '', 'la più chiesta'),
                   ('Scarico disostruito', 'Lavandino, doccia o wc, con la sonda', '90 €', '', ''),
                   ('Manutenzione caldaia', 'Pulizia, controllo dei fumi, bollino', '110 €', '', ''),
                   ('Caldaia a condensazione', 'Montata, con lo smaltimento della vecchia', 'da 2.100 €', '', '')], immagini=False)],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=64, valign='start'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([t.one([X.certificati(K, t, [
        ('DM 37/08', 'Abilitazione', 'Impianti idraulici, termici e del gas (lettere C, D, E)', 'badge-check'),
        ('F-gas', 'Patentino', 'Pompe di calore e climatizzatori, gas fluorurati', 'thermometer'),
        ('CIT', 'Catasto impianti', 'Il libretto della caldaia registrato in Regione', 'file-check')])])],
        padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.pannello(t, V_CALDAIA, 'Le caldaie', 'Una caldaia nuova, <em>e il conto più basso.</em>',
                          'Le caldaie a condensazione consumano un quarto in meno: con gli incentivi si ripagano in pochi inverni.',
                          'Chiedete il preventivo', CONTATTI + '#scrivi', video=True))
    return out


def build_bagni():
    t = Tpl(ID, 'bagni')
    out = [K.intro(t, 'I bagni', ['Dieci giorni,', 'chiavi in mano.'],
                   'Idraulica, piastrelle, mobili, vetri della doccia: una sola ditta e un solo prezzo, scritto prima.',
                   bg=K.photo_bg(BAGNO_DOPPIO, position='center 55%'), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Dal sopralluogo', 'alle chiavi.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'il sopralluogo', BAGNO_PIETRA, 'Guardare', 'Il bagno', 'di oggi', 'Misure, tubi, scarichi: vediamo cosa si può spostare.',
             '1', 'ora'),
            ('2', 'il progetto', DOCCIA_VETRO, 'Disegnare', 'Il bagno', 'nuovo', 'Il disegno in 3D, le piastrelle da toccare in negozio.',
             '1', 'settimana'),
            ('3', 'i lavori', BAGNO_RAME, 'Rifare', 'Dieci giorni', 'di cantiere', 'Smontare, rifare i tubi, piastrellare, montare, pulire.',
             '10', 'giorni')], icona='calendar')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    conf = X.preventivo(K, t, [
        ('misura', 'Il bagno', 'uno', True, 'Il prezzo comprende smontaggio, impianto, piastrelle di fascia media e montaggio.'),
        ('lavaggio', 'Doccia o vasca', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('piccolo', 'misura', 'Piccolo', 'Fino a 5 m².', 7800, '', True),
        ('medio', 'misura', 'Medio', 'Da 5 a 8 m².', 9600, 'il più chiesto', False),
        ('grande', 'misura', 'Grande', 'Oltre 8 m², due lavabi.', 12400, '', False),
        ('doccia', 'lavaggio', 'Doccia a filo pavimento', 'Col vetro fisso.', 0, '', True),
        ('vasca', 'lavaggio', 'Vasca', 'Da incasso, col box.', 450, '', False),
        ('entrambe', 'lavaggio', 'Doccia e vasca', 'Se lo spazio c’è.', 1900, '', False),
        ('sospesi', 'extra', 'Sanitari sospesi', 'Con le cassette nel muro.', 650, '', False),
        ('termoarredo', 'extra', 'Scaldasalviette', 'Elettrico o ad acqua.', 380, '', False),
        ('mobile', 'extra', 'Mobile su misura', 'In rovere, col piano.', 1200, '', False)],
        'Il vostro bagno', CONTATTI + '?lavoro={configurazione}#scrivi', 'IVA al 10 % · piastrelle di fascia media')
    out.append(X.sezione_preventivo(K, t, 'Il preventivo', ['Componete il bagno,', 'il totale si fa da sé.'], '', conf))
    out.append(t.section([
        t.one([K.sechead(t, 'Alcuni bagni', ['Fatti', 'quest’anno.'], size=56)]),
        P.fila_foto(K, t, [(BAGNO_DOPPIO, 'Due lavabi'), (DOCCIA_VETRO, 'La doccia a filo'), (BAGNO_PIETRA, 'Pietra e vetro')], ratio='4/5'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Chiamate,', 'veniamo noi.'],
                   'Per le urgenze il telefono, per i lavori il modulo: vi richiamiamo in giornata.',
                   bg=K.video_bg(V_TUBI, overlay=30), size=96)]
    out.append(t.section([t.one([X.orari(K, t, [('Tutti i giorni', '7 — 22', 'Urgenze: perdite, scarichi, caldaie ferme'),
                                                ('Lunedì — venerdì', '8 — 18', 'Lavori programmati e sopralluoghi'),
                                                ('Sabato', '8 — 13', 'Manutenzioni delle caldaie')])])],
                         bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 80, 'padding_bottom': 80}))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email'), K.campo('select', 'Per', 'motivo',
                                                          opzioni='Una perdita\nUno scarico\nLa caldaia\nUn bagno nuovo\nAltro'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Cosa succede', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Due righe,', 'e vi richiamiamo.'],
                                'Per le urgenze chiamate: il modulo lo leggiamo entro sera.', VOCI, campi, 'Sifone — richiesta',
                                'Invia', 'Grazie! Vi richiamiamo in giornata.'))
    out.append(t.section([
        t.one([K.sechead(t, 'Chi viene da voi', ['Sei idraulici,', 'tre li conoscete qui.'], size=56)]),
        K.squadra(t, [(MARTA, 'Marta Zanoni', 'Idraulica', 'Perdite e scarichi: la termocamera è sua.', 'center 25%'),
                      (GINO, 'Gino Bertolini', 'Caldaie', 'Trent’anni di caldaie: le sente prima di aprirle.', 'center 30%'),
                      (LUCA, 'Luca Fiorini', 'Bagni', 'Segue i cantieri dei bagni dal primo giorno alle chiavi.', 'center 30%')],
                  ratio='4/5', size='320'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(X.zone(K, t, 'Dove arriviamo', ['Verona', 'e intorno.'], 'I tempi di un giorno feriale, per le urgenze.',
                      [('Verona città', 'entro 2 ore'), ('San Martino, Lavagno', 'entro 2 ore'), ('Valpolicella', 'entro 3 ore'),
                       ('Lago di Garda', 'entro 3 ore'), ('Villafranca, Sommacampagna', 'entro 2 ore e mezza'), ('Bassa veronese', 'in giornata')],
                      LAT, LNG, 'Sifone · il magazzino', False, HEX['secondary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · scarico intasato', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>non scende.</em>', size=84, tag='h1', align='center', pad_bottom=20,
                                  color=CHIARO, accento=K.acc_media),
                       K.text(t, 'Qualcosa si è fermato nel tubo. Ripartiamo dall’inizio.', size=17, align='center', color=mix(CHIARO, 88)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_PERDITA, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'La perdita da riparare',
    ['artigiani', 'idraulico', 'bagni', 'caldaie', 'pronto intervento', 'chiaro', 'slider pro', 'interattivo'],
    'Idraulici a Verona: nell’apertura un tubo di rame perde, le gocce cadono e la pozza si allarga; un clic sul raccordo, la '
    'chiave inglese lo stringe e la perdita si ferma, l’acqua corre fino al rubinetto e il manometro sale al verde. Poi i servizi, i bagni chiavi in '
    'mano col preventivo da comporre, il listino, le urgenze e le zone.',
    [('Servizi', SERVIZI), ('Bagni', BAGNI), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('servizi', 'Servizi', 'servizi'), ('bagni', 'Bagni', 'bagni'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Karla\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Bricolage Grotesque\', system-ui, sans-serif',
    '\'IBM Plex Mono\', ui-monospace, Menlo, monospace', ['Karla', 'Bricolage Grotesque', 'IBM Plex Mono'], corsivo_di=[],
    peso_titoli='700', raggio=10, grana=0, titoli_line='1.0', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'gocciolio'
THEME['cursor']['ring_color'] = '#4FB3E8'
THEME['cursor']['dot_color'] = HEX['accent']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'servizi.json': build_servizi(), 'bagni.json': build_bagni(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
