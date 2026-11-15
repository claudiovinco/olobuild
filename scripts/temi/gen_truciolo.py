# -*- coding: utf-8 -*-
"""Genera il tema «Truciolo» (id truciolo): una falegnameria di Udine — mobili su misura, cucine, librerie e armadi a
muro, porte e scale, restauro dei mobili di famiglia; il legno scelto in segheria, in Carnia.

La trovata: «il taglio». Lo slider resta fermo mentre si scorre: una tavola di abete attraversa lo schermo; scorrendo
scende la lama circolare che gira, la segatura vola, il taglio si allunga fino in fondo e le due metà si aprono e cadono
di lato, scoprendo la bottega e la frase «dal pezzo intero».

Tema scuro: noce, acero, il verde della Carnia. Titoli in Spectral, testo in Hanken Grotesk, dati in JetBrains Mono.
Cursore: trucioli (la pialla stacca i ricci).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import mestieri as X  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'truciolo'
NAME = 'Truciolo'

HEX = {
    'primary': '#E0A458', 'primary_contrast': '#1C1712', 'secondary': '#8FA67E', 'secondary_contrast': '#1C1712',
    'accent': '#E07A5A', 'dark': '#0F0C09', 'light': '#FFFFFF', 'muted': '#2A221A', 'muted_contrast': '#EFE5D6',
    'text': '#EFE5D6', 'text_muted': '#BBAA93', 'background': '#1C1712', 'border': '#3D3127', 'link': '#E0A458',
}
HOME, MOBILI, BOTTEGA, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:mobili}}', '{{PAGINA:la-bottega}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'bottega@truciolo.example', '0432 000 158', 'tel:+390432000158'
LAT, LNG = '46.0711', '13.2346'

V_PIALLA, V_SEGA, V_RICCI, V_LIMA, V_MANI, V_SCALPELLO = (
    'pexels-video-35170034', 'pexels-video-20660631', 'pexels-video-5972632', 'pexels-video-5895456', 'pexels-video-5973239',
    'pexels-video-4480557')
VIDEOS = [V_PIALLA, V_SEGA, V_RICCI, V_LIMA, V_MANI, V_SCALPELLO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
(GIULIA, GIULIA_TAVOLA, SEGHETTO_DONNA, BRUNO, TRAPANO, PIALLA_ANZIANO, ATTREZZI, PIALLA_MANO, PIALLA_LEGNO, RICCIOLI, TRUCIOLI,
 SEGATURA, SCALPELLO_RICCI, TAVOLO_FIUME, LEGNO_RICCI, INCASTRO, CREDENZA, ASSE_CHIARA, ASSI_VERTICALI, CATASTA, BOTTEGA_FOTO) = f(
    8447888, 8447892, 8961341, 5466229, 6790936, 32357250, 37663438, 5691541, 5711903, 5974251, 6791544, 4403500, 6790748,
    6394655, 7109998, 31755424, 11105148, 139306, 131664, 12278569, 6790066)
PHOTOS = [GIULIA, GIULIA_TAVOLA, SEGHETTO_DONNA, BRUNO, TRAPANO, PIALLA_ANZIANO, ATTREZZI, PIALLA_MANO, PIALLA_LEGNO, RICCIOLI,
          TRUCIOLI, SEGATURA, SCALPELLO_RICCI, TAVOLO_FIUME, LEGNO_RICCI, INCASTRO, CREDENZA, ASSE_CHIARA, ASSI_VERTICALI, CATASTA,
          BOTTEGA_FOTO]
DISEGNI = ['truciolo-tavola-sx.jpg', 'truciolo-tavola-dx.jpg', 'truciolo-lama.svg', 'truciolo-segatura.svg']
LOGHI = ['truciolo-logo-chiaro.png', 'truciolo-logo-scuro.png']
MEDIA = set(PHOTOS) | set(DISEGNI) | set(LOGHI) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=3, peso_titoli='500', corsivo=True, maiuscole_pulsanti=True)
C = K.c
FONT_T = 'Spectral'
VOCI = [('map-pin', 'Via Pracchiuso 58 · Udine', CONTATTI + '#zone'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return X.testata(K, Tpl(ID, 'header'), ID, NAME, 200, scuro=True, telefono=PHONE, tel=PHONE_TEL)


def build_footer():
    return X.piede(K, Tpl(ID, 'footer'), ID, '693/126', 'Truciolo: falegnameria a Udine dal 1958. Mobili su misura, cucine, '
                   'librerie, porte e scale, dal pezzo intero; e i mobili di famiglia rimessi in sesto.',
                   [('I mobili', MOBILI), ('La bottega', BOTTEGA), ('Contatti', CONTATTI), ('Privacy', '/privacy-policy/')],
                   'La bottega', 'Lunedì — venerdì 7:30 — 18<br>Sabato mattina su appuntamento', VOCI,
                   '© 2026 Truciolo · Udine · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                  ('pinterest', 'https://pinterest.com')], scuro=True)


# ── L'apertura: il taglio ──
def apertura(t):
    m = K.mob
    taglio = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'sy': 0}, {'p': 10, 'sy': 0}, {'p': 50, 'sy': 1}, {'p': 100, 'sy': 1}]}
    # la lama scende lungo il taglio (277 unità sul computer e sul telefono: la tavola ha le stesse proporzioni) ed esce
    # la lama sta DI TAGLIO rispetto alla tavola (girata di 74° in 3D, un'ellisse stretta), così il solco nasce dalla lama
    # e non dal centro del disco (l'utente, 13 nov 2026: «la sega taglia dalla lama, non dal centro»); gira da sola nel
    # disegno
    lama = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': -160, 'o': 0, 'ry': 74}, {'p': 6, 'y': -40, 'o': 100, 'ry': 74},
                                               {'p': 10, 'y': 0, 'o': 100, 'ry': 74}, {'p': 50, 'y': 277, 'o': 100, 'ry': 74},
                                               {'p': 58, 'y': 640, 'o': 0, 'ry': 74}, {'p': 100, 'y': 640, 'o': 0, 'ry': 74}]}
    polvere = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'y': 0, 'o': 0}, {'p': 10, 'y': 0, 'o': 100}, {'p': 50, 'y': 277, 'o': 100},
                                                  {'p': 54, 'y': 300, 'o': 0}, {'p': 100, 'y': 300, 'o': 0}]}
    cade = lambda s: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'x': 0, 'y': 0, 'r': 0, 'o': 100}, {'p': 52, 'x': 0, 'y': 0, 'r': 0, 'o': 100},
                                                           {'p': 60, 'x': s * 40, 'y': -20, 'r': s * 6, 'o': 100},
                                                           {'p': 80, 'x': s * 300, 'y': 520, 'r': s * 18, 'o': 0},
                                                           {'p': 100, 'x': s * 300, 'y': 520, 'r': s * 18, 'o': 0}]}
    via = {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 100}, {'p': 8, 'o': 100}, {'p': 18, 'o': 0}, {'p': 100, 'o': 0}]}
    arriva = lambda da: {'inizio': 0, 'fine': 100, 'tappe': [{'p': 0, 'o': 0, 'y': 30}, {'p': da, 'o': 0, 'y': 30},
                                                              {'p': da + 8, 'o': 100, 'y': 0}, {'p': 100, 'o': 100, 'y': 0}]}
    tav = {'x': 0, 'y': 33, 'w': 50, 'h': 34.2}
    L = K.veli('tr-velo', 'alto', 30, 56, colore=C['nero'], forza=80, mobile_alto=22) + [
        # la lama sta DIETRO la tavola: dentro il legno non si vede, escono solo i denti sopra e sotto (l'utente, 13 nov 2026:
        # «una sega quando taglia non scorre piatta sul legno»)
        K.livello('tr-lama', tipo='image', imageSrc=K.img(DISEGNI[2]), imageAlt='La lama circolare', objectFit='contain', x=42, y=18.8,
                  w=16, h=28.4, entra='fadeIn', ritardo=0, durata=300, scrollMove=lama,
                  responsive={'mobile': {'x': 32, 'y': 31.8, 'width': 36, 'height': 16.4}}),
        K.livello('tr-sx', tipo='image', imageSrc=K.img(DISEGNI[0]), imageAlt='Una tavola di abete', objectFit='cover',
                  objectPosition='right center', entra='fadeIn', ritardo=0, durata=500, scrollMove=cade(-1),
                  customCSS='transform-origin:0% 100%;',
                  responsive={'mobile': {'x': 0, 'y': 40, 'width': 50, 'height': 8.8}}, **tav),
        K.livello('tr-dx', tipo='image', imageSrc=K.img(DISEGNI[1]), imageAlt='', objectFit='cover', objectPosition='left center',
                  entra='fadeIn', ritardo=0, durata=500, scrollMove=cade(1),
                  customCSS='transform-origin:100% 100%;',
                  responsive={'mobile': {'x': 50, 'y': 40, 'width': 50, 'height': 8.8}}, **dict(tav, x=50)),
        K.livello('tr-taglio', tipo='shape', bgColor='#2A1A0E', x=49.8, y=33, w=0.4, h=34.2, entra='fadeIn', ritardo=0, durata=300,
                  customCSS='transform-origin:50% 0%;', scrollMove=dict(taglio, tappe=taglio['tappe'][:3] + [
                      {'p': 52, 'sy': 1, 'o': 100}, {'p': 56, 'sy': 1, 'o': 0}, {'p': 100, 'sy': 1, 'o': 0}]),
                  responsive={'mobile': {'x': 49.6, 'y': 40, 'width': 0.8, 'height': 8.8}}),
        # la metà della lama che viene verso chi guarda (girata di 74°, la sinistra) sta DAVANTI alla tavola, l'altra dietro:
        # la lama attraversa il legno, centrata sulla punta del solco (l'utente, 14 nov 2026: «la sega non deve stare
        # sotto, ma mezza sopra e mezza sotto»)
        K.livello('tr-lama-su', tipo='image', imageSrc=K.img(DISEGNI[2]), imageAlt='', objectFit='contain', x=42, y=18.8,
                  w=16, h=28.4, entra='fadeIn', ritardo=0, durata=300, scrollMove=lama, customCSS='clip-path:inset(0 50% 0 0);',
                  responsive={'mobile': {'x': 32, 'y': 31.8, 'width': 36, 'height': 16.4}}),
        K.livello('tr-segatura', tipo='image', imageSrc=K.img(DISEGNI[3]), imageAlt='', objectFit='contain', x=39, y=13.3, w=22, h=39,
                  entra='fadeIn', ritardo=0, durata=300, scrollMove=polvere,
                  responsive={'mobile': {'x': 25, 'y': 31.8, 'width': 50, 'height': 16.4}}),
        # sulla tavola, prima del taglio
        K.livello('tr-o0', contenuto='Truciolo · falegnameria · Udine, dal 1958', x=6, y=12, w=60, size=13, peso='500',
                  colore=mix(C['testo'], 85), letterSpacing=3, textTransform='uppercase', fontFamily='JetBrains Mono', ritardo=200,
                  responsive={'mobile': {'fontSize': m(10), 'x': 6, 'y': 8, 'width': 88}}),
        K.livello('tr-t0', contenuto='Su misura,', tag='h1', x=6, y=16.5, w=60, size=104, peso='500', colore=C['testo'], fontFamily=FONT_T,
                  lineHeight=1.0, entra='maskFromBottom', ritardo=300, durata=900,
                  responsive={'mobile': {'fontSize': m(52), 'x': 6, 'y': 10.5, 'width': 88}}),
        K.livello('tr-h0', contenuto='Scorrete: si taglia ↓', x=68, y=20, w=26, size=16, peso='500', align='right', colore=C['acc'],
                  fontFamily='JetBrains Mono', ritardo=700, scrollMove=via,
                  responsive={'mobile': {'fontSize': m(12), 'x': 6, 'y': 52, 'width': 88, 'textAlign': 'left'}}),
        # dentro il taglio, la bottega
        K.livello('tr-t1', contenuto='dal pezzo intero.', tag='p', x=10, y=40, w=80, size=104, peso='500', fontStyle='italic',
                  align='center', colore=C['acc'], fontFamily=FONT_T, lineHeight=1.0, entra='fadeIn', ritardo=0, scrollMove=arriva(70),
                  responsive={'mobile': {'fontSize': m(32), 'x': 5, 'y': 40, 'width': 90}}),
        K.livello('tr-s1', contenuto='Cucine, librerie, armadi, porte e scale fatti in bottega, a Udine, col legno scelto in segheria.',
                  x=28, y=57, w=44, size=18, peso='400', align='center', colore=mix(C['testo'], 92), lineHeight=1.55, entra='fadeIn',
                  ritardo=0, scrollMove=arriva(74), responsive={'mobile': {'fontSize': m(14), 'x': 8, 'y': 47, 'width': 84}}),
        K.livello('tr-b1', tipo='button', contenuto='I mobili', x=38.5, y=68, w='auto', size=13, peso='600', colore=C['on_acc'],
                  bgColor=C['acc'], padding=18, borderRadius=3, letterSpacing=2, textTransform='uppercase', buttonUrl=MOBILI,
                  entra='fadeIn', ritardo=0, scrollMove=arriva(78), hover={'bgColor': '#FFFFFF', 'duration': 250},
                  responsive={'mobile': {'x': 8, 'y': 56, 'fontSize': m(12)}}),
        K.livello('tr-b2', tipo='button', contenuto='La bottega', x=50.5, y=68, w='auto', size=13, peso='600', colore=C['testo'],
                  bgColor='transparent', padding=18, borderRadius=3, borderWidth=1, borderColor=mix(C['testo'], 55), letterSpacing=2,
                  textTransform='uppercase', buttonUrl=BOTTEGA, entra='fadeIn', ritardo=0, scrollMove=arriva(78),
                  hover={'bgColor': mix(C['testo'], 14), 'duration': 250},
                  responsive={'mobile': {'x': 50, 'y': 56, 'fontSize': m(12)}}),
    ]
    M.veli_oltre(L)
    sl = [K.diapositiva('tr-1', K.sfondo_video(V_PIALLA, overlay=0.3, colore=C['nero']), L, durata=600000)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fade', showArrows=False, showDots=False,
                       showTabs=False, showProgressBar=False, autoplay=False, parallax=False, scrollTimeline=True,
                       scrollTimelineDistance=1700, scrollStep=False, heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Truciolo · Udine, dal 1958', ['Il legno', 'ha un verso.'], size=72, layout='stack',
                                size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Siamo in cinque in bottega. Il legno lo scegliamo noi in segheria, in Carnia, e lo lasciamo '
                                'riposare un anno; poi lo tagliamo seguendo la vena, perché una tavola tagliata contro il verso '
                                'si torce. I mobili li disegniamo con voi e li montiamo noi.', size=17),
                      K.button(t, 'La bottega', BOTTEGA, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Cosa facciamo', ['Quattro mobili,', 'nessuno uguale.'], size=60)]),
        t.one([P.carte(K, t, [(INCASTRO, 'Cucine', 'in massello', 'Ante in rovere o in noce, il piano in legno trattato a olio: '
                               'una cucina che invecchia bene.'),
                              (TAVOLO_FIUME, 'Tavoli', 'dal pezzo intero', 'Una tavola sola, di noce o di olmo, con le gambe a incastro.'),
                              (CREDENZA, 'Restauri', 'dei mobili di famiglia', 'La credenza della nonna: tarli via, cera d’api, i cassetti '
                               'che scorrono di nuovo.'),
                              (ASSI_VERTICALI, 'Porte e scale', 'su misura', 'Porte interne in abete e scale a giorno in larice.')],
                      altezza='400', colonne=4)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_RICCI, 'La pialla', 'Un riccio <em>alla volta.</em>',
                             'La pialla a mano si usa ancora: per l’ultima passata, quella che si sente con il palmo.', overlay=20))
    out.append(X.prima_dopo(K, t, [
        (LEGNO_RICCI, CREDENZA, 'Prima', 'Dopo', 'La credenza di Tarcento', 'Tarli, cassetti bloccati, la vernice sopra la cera: '
         'tre settimane in bottega.'),
        (ASSE_CHIARA, TAVOLO_FIUME, 'La tavola', 'Il tavolo', 'Il tavolo di Cividale', 'Una tavola di noce di tre metri, scelta in '
         'segheria e lasciata riposare un anno.')],
        'Prima e dopo', ['Dal legno', 'al mobile.']))
    out.append(X.recensioni(K, t, [
        ('Ci hanno fatto la cucina in rovere. Dopo sei anni le ante chiudono come il primo giorno.', 'Francesca', 'Udine'),
        ('La credenza di mia nonna era finita in garage. Adesso è in sala, e i cassetti scorrono.', 'Davide', 'Tarcento'),
        ('La libreria a muro con la scala: l’hanno montata in un giorno, senza un graffio alle pareti.', 'Anna e Marco', 'Cividale')],
        'Chi ci ha chiamato', ['Tre case,', 'tre mobili.']))
    out.append(X.lettera(K, t, 'La lettera', ['Una volta al mese,', 'dalla bottega.'],
                         'Il legno che è arrivato, un mobile appena finito, come curare l’olio del vostro tavolo.'))
    return out


def build_mobili():
    t = Tpl(ID, 'mobili')
    out = [K.intro(t, 'I mobili', ['Disegnati con voi,', 'fatti qui.'],
                   'Cucine, tavoli, librerie, armadi a muro, porte e scale: in massello o in tamburato, con gli incastri a vista.',
                   bg=K.video_bg(V_SEGA, overlay=30), size=96)]
    out.append(t.section([t.one([P.pila(K, t, [
        ('Cucine', 'La cucina', 'in massello', 'Rovere, noce o frassino; il piano in legno a olio o in pietra. 6–8 settimane.', INCASTRO),
        ('Librerie', 'La libreria', 'a muro', 'Fino al soffitto, con la scala che scorre: misurata al millimetro sulle vostre pareti.',
         ATTREZZI),
        ('Tavoli', 'Il tavolo', 'dal pezzo intero', 'Una tavola sola, scelta con voi in segheria.', TAVOLO_FIUME),
        ('Restauri', 'Il mobile', 'di famiglia', 'Smontato, disinfestato, incollato, ripassato a cera.', CREDENZA)], altezza=420)])],
        padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    conf = X.preventivo(K, t, [
        ('mobile', 'Il mobile', 'uno', True, 'Indicativo: il prezzo vero dopo le misure in casa, gratis.'),
        ('legno', 'Il legno', 'uno', True, ''),
        ('extra', 'In più', 'piu', False, '')], [
        ('tavolo', 'mobile', 'Tavolo da sei', '200 × 95 cm, gambe a incastro.', 2400, '', True),
        ('libreria', 'mobile', 'Libreria a muro', '3 m di larghezza, fino al soffitto.', 4800, 'la più chiesta', False),
        ('cucina', 'mobile', 'Cucina', '4 m lineari con l’isola.', 14500, '', False),
        ('abete', 'legno', 'Abete della Carnia', 'Chiaro, profumato.', 0, '', True),
        ('rovere', 'legno', 'Rovere', 'Duro, con le specchiature.', 900, '', False),
        ('noce', 'legno', 'Noce nazionale', 'Scuro, venato.', 1800, '', False),
        ('scala', 'extra', 'La scala che scorre', 'In ferro e legno.', 780, '', False),
        ('olio', 'extra', 'Olio di lino', 'Al posto della vernice.', 160, '', False)],
        'Il vostro mobile', CONTATTI + '?lavoro={configurazione}#scrivi', 'IVA compresa · misure in casa gratis')
    out.append(X.sezione_preventivo(K, t, 'Il preventivo', ['Scegliete il legno,', 'il totale si fa da sé.'], '', conf))
    out.append(K.domande(t, 'Sul legno,', 'le domande di tutti.', [
        ('Massello o tamburato?', 'Il massello è legno pieno: pesa e dura una vita. Il tamburato è leggero e costa meno: va bene per '
         'le ante grandi. Spesso li mescoliamo.'),
        ('Il legno a olio si macchia?', 'Un po’, ed è il bello: le macchie si tolgono con la carta fine e una passata d’olio.'),
        ('Quanto ci vuole?', 'Da tre settimane per un tavolo a due mesi per una cucina, dal giorno in cui firmiamo il disegno.'),
    ], 'tr-faq', bg=False))
    return out


def build_bottega():
    t = Tpl(ID, 'la-bottega')
    out = [K.intro(t, 'La bottega', ['Tre generazioni', 'di trucioli.'],
                   'Nel 1958 il nonno aprì in via Pracchiuso con una sega a nastro e una pialla. La pialla c’è ancora.',
                   bg=K.video_bg(V_MANI, overlay=30), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come lavoriamo', ['Dalla segheria', 'a casa vostra.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'il legno', CATASTA, 'Scegliere', 'La tavola', 'in Carnia', 'Andiamo in segheria e scegliamo le tavole una per una.',
             '1', 'anno a riposare'),
            ('2', 'il disegno', ATTREZZI, 'Disegnare', 'Il mobile', 'con voi', 'Le misure in casa, il disegno a mano e al computer.',
             '2', 'settimane'),
            ('3', 'il lavoro', PIALLA_ANZIANO, 'Costruire', 'Gli incastri', 'a mano', 'Sega, pialla, scalpello; la colla solo dove serve.',
             '6', 'settimane')], icona='hammer')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In bottega', ['Chi tiene', 'la pialla.'], size=56)]),
        K.squadra(t, [(BRUNO, 'Bruno Del Fabbro', 'Fondatore', 'Il figlio del nonno: sceglie le tavole e fa gli incastri difficili.',
                       'center 30%'),
                      (GIULIA, 'Giulia Del Fabbro', 'Falegname', 'La nipote: disegna i mobili e segue le cucine dal rilievo al montaggio.',
                       'center 25%'),
                      (SEGHETTO_DONNA, 'Mei Lin', 'Restauri', 'I mobili di famiglia: tarli, colla di coniglio, cera d’api.', 'center 30%')],
                  ratio='4/5', size='320'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Il legno', ['Da vicino.'], size=56)]),
        P.fila_foto(K, t, [(RICCIOLI, 'I ricci della pialla'), (TRUCIOLI, 'I trucioli sul banco'), (SCALPELLO_RICCI, 'Lo scalpello'),
                           (SEGATURA, 'La segatura')], ratio='1/1'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Venite in bottega,', 'o veniamo noi.'],
                   'Per un mobile su misura veniamo a misurare; per un restauro portate il mobile, o lo veniamo a prendere.',
                   bg=K.video_bg(V_SCALPELLO, overlay=30), size=96)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('tel', 'Telefono', 'telefono', True),
             K.campo('email', 'Email', 'email', True), K.campo('select', 'Per', 'motivo',
                                                                opzioni='Un mobile su misura\nUna cucina\nUn restauro\nPorte e scale\nAltro'),
             K.campo('text', 'Il lavoro', 'lavoro', larghezza='1-1'),
             K.campo('textarea', 'Il mobile, in due righe', 'messaggio', larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Due righe,', 'e una foto se c’è.'],
                                'Vi rispondiamo entro due giorni.', VOCI, campi, 'Truciolo — richiesta', 'Invia',
                                'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(X.zone(K, t, 'La bottega', ['Via Pracchiuso,', 'a due passi dal centro.'], 'Il sabato mattina su appuntamento.',
                      [('Orari', 'lunedì — venerdì 7:30 — 18'), ('Parcheggio', 'in cortile'), ('Montaggi', 'in tutto il Friuli'),
                       ('Telefono', PHONE)], LAT, LNG, 'Truciolo · la bottega', True, HEX['primary']))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · contro venatura', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>si è torta.</em>', size=84, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'L’abbiamo tagliata contro il verso. Ripartiamo dalla tavola intera.', size=17, align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_LIMA, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, X.CATEGORIA, 'La tavola tagliata',
    ['artigiani', 'falegname', 'mobili su misura', 'restauro', 'legno', 'scuro', 'slider pro', 'scorrimento'],
    'Falegnami a Udine: scorrendo, una lama circolare scende su una tavola che attraversa lo schermo, la segatura vola e le '
    'due metà si aprono e cadono di lato, scoprendo la bottega. Poi i mobili da girare, i restauri prima e dopo, il preventivo '
    'col legno da scegliere e le persone della bottega.',
    [('I mobili', MOBILI), ('La bottega', BOTTEGA), ('Contatti', CONTATTI)],
    [('home', 'Home', None), ('mobili', 'I mobili', 'mobili'), ('la-bottega', 'La bottega', 'la-bottega'), ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Hanken Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Spectral\', Georgia, serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Hanken Grotesk', 'Spectral', 'JetBrains Mono'], corsivo_di=['Spectral'],
    peso_titoli='500', raggio=3, grana=6, titoli_line='1.0', titoli_spazio='-0.01em')
THEME['cursor']['style'] = 'trucioli'
THEME['cursor']['ring_color'] = HEX['primary']
THEME['cursor']['dot_color'] = '#8A5A2B'


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'mobili.json': build_mobili(), 'la-bottega.json': build_bottega(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
