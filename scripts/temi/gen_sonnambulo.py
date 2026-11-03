# -*- coding: utf-8 -*-
"""Genera il tema «Sonnambulo» (id sonnambulo): un marchio di Como di pigiami, camicie da notte e vestaglie in seta e
flanella, da portare anche a colazione, al bar sotto casa, ad aprire al corriere.

Il sito si addormenta (Effetti di pagina → «Il sito si addormenta», 1.4.957): dopo dodici secondi senza un movimento
scende un velo blu notte con le Z che salgono e la scritta; al primo movimento si sveglia. Anche il cursore, fermo, fa le
Z. Apertura: la parola della notte scritta sdraiata lungo il bordo sinistro, che respira piano; tre diapositive (chi
dorme, chi si gira, chi non si vuole alzare) che passano sfumando nel buio, e la luna che pulsa.

Tema scuro, blu notte, oro di luna e lavanda. Titoli in Cormorant corsivo, testo in Quicksand, dati in Source Code Pro.
Logo: la falce di luna col berretto da notte e «Sonnambulo». Cursore: il sonno (una falce di luna che, ferma, fa le Z).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, page  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'sonnambulo'
NAME = 'Sonnambulo'

HEX = {
    'primary': '#F3D98B', 'primary_contrast': '#121633', 'secondary': '#B4A8FF', 'secondary_contrast': '#121633',
    'accent': '#9C8CFF', 'dark': '#0B0E24', 'light': '#FFFFFF', 'muted': '#1A1F45', 'muted_contrast': '#EDE7FF',
    'text': '#EDE7FF', 'text_muted': '#B9B3D6', 'background': '#121633', 'border': '#2C3366', 'link': '#F3D98B',
}
LUNA = '#F3D98B'

HOME, PIGIAMI, NOTTE, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:pigiami}}', '{{PAGINA:la-notte}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'buonanotte@sonnambulo.example', '+39 031 000 2210', 'tel:+390310002210'
LAT, LNG = '45.8110', '9.0850'

V_BIONDA, V_FINESTRA, V_SORRISO, V_LUNA, V_PIENA, V_NUBI, V_COLAZIONE, V_TAZZA, V_UOMO = (
    'pexels-video-5983686', 'pexels-video-7394161', 'pexels-video-7505341', 'pexels-video-854739', 'pexels-video-6960054',
    'pexels-video-39750684', 'pexels-video-5353258', 'pexels-video-5353388', 'pexels-video-5983724')
VIDEOS = [V_BIONDA, V_FINESTRA, V_SORRISO, V_LUNA, V_PIENA, V_NUBI, V_COLAZIONE, V_TAZZA, V_UOMO]
f = lambda *n: ['pexels-%s.jpg' % x for x in n]
PIG = f('6443431', '6933041', '8416056', '20377876', '15585242', '15585232', '9499298', '8416408', '6443371')
SET = f('6443379', '33672397', '32343659', '20364755', '5690227')
LEN = f('9893931', '33312398', '7765000', '7765002')
DOR = f('6633579', '7622514', '9787785', '7622515', '3807760', '6633585')
UOM = f('5903815', '10565627', '6969655', '6969650')
LUN = f('10828622', '11838246', '40068652', '30138461')
CAM = f('8889733', '6940353', '7609139', '10387566', '6754022')
BAM = f('18820123', '8501428', '8790313', '8790329', '8501449')
MAS = f('8416413', '6963184', '9499267', '7269384')
CUS = f('4271665', '36740614', '7166552', '5895038')
LOGO = 'logo-sonnambulo-chiaro.png'
PHOTOS = PIG + SET + LEN + DOR + UOM + LUN + CAM + BAM + MAS + CUS
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO, 'logo-sonnambulo-scuro.png'}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='600', corsivo=True, maiuscole_pulsanti=False)
C = K.c
MONO = 'Source Code Pro'
VOCI = [('map-pin', 'Via Vittorio Emanuele 41 · Como', NEGOZI + '#atelier'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def pagina(nodi):
    """Ogni pagina si addormenta dopo dodici secondi fermi."""
    return page(nodi, page_sonno=True, page_sonno_secondi=12,
                page_sonno_testo='Shh. Il sito si è addormentato: muovete il mouse, piano, e si sveglia.')


def build_header():
    return M.testata(K, Tpl(ID, 'header'), 'Sonnambulo', LOGO, 210, overlay=True, scuro=True)


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO, '1223/226', 'Sonnambulo cuce a Como pigiami, camicie da notte e vestaglie in seta e flanella. '
                   'Si portano a letto, a colazione e al bar sotto casa.',
                   [('I pigiami', PIGIAMI), ('La notte', NOTTE), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'L’atelier', 'Via Vittorio Emanuele 41, Como<br>Martedì — sabato 10:30 — 19:30<br>La domenica dormiamo',
                   VOCI, '© 2026 Sonnambulo · Como · P.IVA 00000000000', [('instagram', 'https://instagram.com'),
                                                                          ('pinterest', 'https://pinterest.com')],
                   larghezza=460)


# ── L'apertura: la parola della notte, sdraiata ──
def apertura(t):
    m = K.mob

    def notte(sid, parola, riga, sotto, video):
        return K.diapositiva(sid, K.sfondo_video(video, overlay=0.22, colore=C['nero']), [
            *K.veli(sid + '-v', 'destra', 34, 66, colore=C['nero'], forza=84, mobile_da=50),
            *K.veli(sid + '-w', 'sinistra', 10, 24, colore=C['nero'], forza=70, mobile_da=96),
            # la parola sdraiata: ruotata di un quarto lungo il bordo sinistro, respira piano (sul telefono è dritta, sotto)
            K.livello(sid + '-p', contenuto=parola, tag='h1', x=-20, y=40, w=56, h=20, size=128, peso='600', colore='#FFFFFF',
                      fontStyle='italic', lineHeight=1.0, textAlign='center', textValign='center', customCSS='rotate:-90deg;',
                      entra='fadeIn', ritardo=300, durata=1600, animLoop='breathLoop', animLoopDuration=6000,
                      responsive={'mobile': {'visible': False}}),
            K.livello(sid + '-pm', contenuto=parola, tag='p', x=-60, y=0, w=0.1, h='auto', size=60, peso='600', colore='#FFFFFF',
                      fontStyle='italic', fontFamily='Cormorant', lineHeight=1.0, entra='fadeIn', ritardo=300, durata=1600,
                      animLoop='breathLoop', animLoopDuration=6000,
                      responsive={'mobile': {'x': 7, 'y': 56, 'width': 88, 'fontSize': m(56)}}),
            K.livello(sid + '-o', contenuto='Sonnambulo · pigiami di seta · Como', x=54, y=46, w=40, size=12, peso='500',
                      colore=mix('#FFFFFF', 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=500,
                      textAlign='right', responsive={'mobile': {'fontSize': m(10), 'y': 52.5, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello(sid + '-t', fontFamily='Cormorant', contenuto=riga, tag='p', x=40, y=51, w=54, size=58, peso='600',
                      fontStyle='italic', colore=LUNA, lineHeight=1.05, textAlign='right', entra='maskFromBottom', ritardo=700,
                      durata=1200, responsive={'mobile': {'fontSize': m(28), 'y': 62, 'x': 7, 'width': 88, 'textAlign': 'left'}}),
            K.livello(sid + '-s', contenuto=sotto, x=56, y=66, w=38, size=16, peso='500', colore=mix('#FFFFFF', 90),
                      lineHeight=1.6, ritardo=950, textAlign='right',
                      responsive={'mobile': {'fontSize': m(13), 'y': 67, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello(sid + '-b', tipo='button', contenuto='I pigiami', x=70, y=80, w='auto', size=15, peso='600',
                      colore=C['on_acc'], bgColor=LUNA, padding=16, borderRadius=999, entra='fadeInUp', ritardo=1200,
                      buttonUrl=PIGIAMI, hover={'bgColor': '#FFFFFF', 'duration': 300},
                      responsive={'mobile': {'y': 75, 'x': 7, 'fontSize': m(12)}}),
            K.livello(sid + '-l', tipo='button', contenuto='La notte', x=81.5, y=80, w='auto', size=15, peso='600',
                      colore='#FFFFFF', bgColor='transparent', padding=16, borderRadius=999, borderWidth=1,
                      borderColor=mix('#FFFFFF', 60), entra='fadeInUp', ritardo=1300, buttonUrl=NOTTE,
                      hover={'bgColor': mix('#FFFFFF', 14), 'duration': 300},
                      responsive={'tablet': {'x': 84}, 'mobile': {'y': 75, 'x': 40, 'fontSize': m(12)}}),
            # la luna che pulsa e le Z che salgono
            K.livello(sid + '-luna', tipo='shape', x=84, y=17, w=6.5, h=11.6, bgColor=LUNA, borderRadius=999, entra='zoomIn',
                      ritardo=400, durata=1500, animLoop='pulseLoop', animLoopDuration=4000,
                      customCSS='box-shadow:0 0 60px 18px rgba(243,217,139,.35);',
                      responsive={'mobile': {'x': 78, 'y': 5, 'width': 13, 'height': 6}}),
            *[K.livello('%s-z%d' % (sid, k), contenuto='z' if k < 2 else 'Z', x=76 + k * 2.6, y=24 - k * 5, w=4, size=26 + k * 12,
                        peso='600', colore=mix('#FFFFFF', 80 - k * 10), fontStyle='italic', fontFamily='Cormorant', entra='fadeIn',
                        ritardo=1500 + k * 500, durata=1200, animLoop='floatLoop', animLoopDuration=3000 + k * 600,
                        responsive={'mobile': {'x': 66 + k * 6, 'y': 14 - k * 3, 'width': 10, 'fontSize': m(18 + k * 8)}})
              for k in range(3)],
        ], durata=8000)

    sl = [notte('so-1', 'Buonanotte', 'Pigiami da non togliere.', 'Seta di Como e flanella: si portano a letto, a colazione, '
                'ad aprire al corriere. E questo sito, se vi fermate, si addormenta.', V_BIONDA),
          notte('so-2', 'Cinque minuti', 'Ancora cinque minuti.', 'La vestaglia di seta pesa quanto un lenzuolo: ci si alza '
                'senza accorgersene.', V_FINESTRA),
          notte('so-3', 'Sogni d’oro', 'Fino a mezzogiorno.', 'La camicia da notte di cotone lavato, che più si lava più è '
                'morbida.', V_SORRISO)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='fadeThroughDark', durata_trans=1600,
                       autoplaySpeed=8000, showArrows=False, showDots=True, dotStyle='dash', showProgressBar=False, parallax=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Sonnambulo · Como, dal 1952', ['Il pigiama', 'è un vestito.'], size=72, layout='stack',
                                size_mobile=44)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'La nonna cuciva camicie da notte per le filande del lago. Noi cuciamo pigiami di seta di Como '
                                'e di flanella che non si tolgono la mattina: a colazione, al bar sotto casa, ad aprire al '
                                'corriere. Nessuno se ne accorge, o tutti.', size=17),
                      K.button(t, 'La notte', NOTTE, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'I pigiami', ['Quattro notti,', 'quattro mattine.'], size=60)]),
        t.one([M.capi(K, t, [
            (SET[0], 'Seta', 'Lago', '', 'Seta di Como, colletto a rever.', '290 €', 'nuovo'),
            (PIG[1], 'Flanella', 'Baita', '', 'Flanella a quadri, bottoni di corno.', '140 €', ''),
            (PIG[4], 'Cotone', 'Mattina', '', 'Popeline lavato, pantalone largo.', '120 €', ''),
            (SET[3], 'Vestaglie', 'Sipario', '', 'Seta pesante, lunga fino ai piedi.', '340 €', '')], PIGIAMI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.row([t.col([K.image(t, LUN[1], 'La luna piena', ratio='1/1')], wm='2-5', ws='1-1'),
               t.col([K.sechead(t, 'Un avviso', ['Se vi fermate,', 'il sito si addormenta.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Provate: lasciate stare il mouse per dodici secondi. Scende la notte, salgono le Z. Basta un '
                                'movimento e si sveglia, un po’ stropicciato. Anche il cursore, se lo lasciate fermo, sbadiglia.',
                             size=17),
                      K.label(t, 'Shh', color=C['acc'], pad_top=18)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_PIENA, 'La notte', 'Si dorme <em>meglio in seta.</em>',
                             'La seta tiene il caldo d’inverno e il fresco d’estate: e non stropiccia la faccia.', overlay=20))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Al risveglio,', 'senza cambiarsi.'], size=60)]),
        P.fila_foto(K, t, [(PIG[0], 'Il pigiama rosso al mattino'), (SET[1], 'La seta verde'), (UOM[0], 'Il pigiama a righe'),
                           (PIG[7], 'La flanella sul divano')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Buonanotte', 'Cinque minuti ancora', 'Sogni d’oro', 'Shh'], fondo=C['sala'],
                                          colore=LUNA, size=56, velocita='22')], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(M.lettera(K, t, 'La lettera della buonanotte', ['Arriva alle dieci', 'di sera.'],
                         'Una volta al mese, alle dieci: un pigiama nuovo, una storia corta, niente sveglie.',
                         invito='A domani', grazie='Fatto. Buonanotte.'))
    return pagina(out)


def build_pigiami():
    t = Tpl(ID, 'pigiami')
    out = [K.intro(t, 'I pigiami', ['Otto modi', 'di dormire.'],
                   'Seta di Como, flanella, cotone lavato. Si portano anche di giorno: le taglie sono quelle dei vestiti. I '
                   'prezzi sono quelli consigliati.', bg=K.photo_bg(LEN[0]), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Seta', ['Del lago,', 'tessuta a Como.'], size=60)]),
        t.one([M.capi(K, t, [
            (SET[0], 'Seta', 'Lago', '', 'Colletto a rever, bordino a contrasto.', '290 €', 'nuovo'),
            (SET[2], 'Seta', 'Bruma', '', 'Grigio perla, pantalone dritto.', '290 €', ''),
            (SET[3], 'Vestaglie', 'Sipario', '', 'Seta pesante, fino ai piedi.', '340 €', ''),
            (SET[4], 'Camicie da notte', 'Lucciola', '', 'Seta lavata, spalline sottili.', '210 €', '')], NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Flanella e cotone', ['Per l’inverno', 'e per i bambini.'], size=60)]),
        t.one([M.capi(K, t, [
            (PIG[1], 'Flanella', 'Baita', '', 'A quadri, bottoni di corno.', '140 €', ''),
            (PIG[4], 'Cotone', 'Mattina', '', 'Popeline lavato, pantalone largo.', '120 €', ''),
            (BAM[0], 'Bambini', 'Ghiro', '', 'Flanella a stelle, dai 2 agli 8 anni.', '69 €', ''),
            (MAS[1], 'Mascherine', 'Eclissi', '', 'Seta imbottita, elastico morbido.', '35 €', 'ultime')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(K.video_frase(t, V_COLAZIONE, 'A colazione', 'Il pigiama <em>a tavola.</em>',
                             'Le taglie sono quelle dei vestiti: si scende così, e nessuno chiede niente.', overlay=22))
    return pagina(out)


def build_notte():
    t = Tpl(ID, 'la-notte')
    out = [K.intro(t, 'La notte', ['Cuciamo', 'mentre dormite.'],
                   'L’atelier apre alle dieci e mezza, perché il pigiama si prova riposati. La seta la tessono a Como, le '
                   'camicie da notte le cuciamo noi, a mano dove serve.', bg=K.video_bg(V_LUNA), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come nasce', ['Dalla seta', 'al cuscino.'], size=60)]),
        t.one([P.tappe(K, t, [
            ('1', 'la seta', SET[1], 'Tessere', 'Seta', 'di Como', 'La tessono a Cernobbio, sul lago: diciannove momme, il peso '
             'giusto per dormire.', '19', 'momme'),
            ('2', 'il taglio', LEN[2], 'Tagliare', 'Largo', 'e morbido', 'Si taglia di sbieco: la seta cade e non tira quando ci '
             'si gira.', '3', 'taglie'),
            ('3', 'la cucitura', CUS[1], 'Cucire', 'A mano', 'il colletto', 'Il colletto e i bordini si cuciono a mano, perché '
             'non grattino.', '6', 'ore a pigiama')], icona='moon')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'All’atelier', ['Chi cuce', 'la notte.'], size=60)]),
        K.squadra(t, [(SET[4], 'Ginevra Brivio', 'Fondatrice', 'Nipote della sarta delle filande: dorme in seta da quando ha '
                       'tre anni.', 'center 25%'),
                      (UOM[2], 'Luca Mauri', 'Il taglio', 'Taglia di sbieco, la notte, quando l’atelier è silenzioso.', 'center 25%'),
                      (PIG[8], 'Hana Sato', 'Le camicie da notte', 'Cuce a mano colletti e bordini.', 'center 25%')],
                  ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(t.section([
        t.row([t.col([K.image(t, DOR[4], 'Una persona che dorme', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Sono sceso a prendere il pane col pigiama Lago. Il fornaio mi ha chiesto dove avevo '
                                        'comprato la camicia.', 'Andrea, Como', size='40')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return pagina(out)


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarli,', 'senza fretta.'],
                   'Un pigiama si prova sdraiati: nei nostri negozi c’è sempre un letto. Ecco dove, e l’atelier sul lago.',
                   bg=K.photo_bg(CAM[3]), size=88)]
    out.append(M.negozi(K, t, [
        ('Como', 'Atelier Sonnambulo', 'via Vittorio Emanuele 41'), ('Milano', 'Cuscino', 'via della Spiga 12'),
        ('Bergamo', 'Ninna Nanna', 'via Gombito 20'), ('Venezia', 'Sogno', 'calle Larga XXII Marzo 2050'),
        ('Firenze', 'Buonanotte', 'via de’ Tornabuoni 70'), ('Roma', 'Ghiro', 'via dei Condotti 9'),
        ('Lugano', 'Lunaria', 'via Nassa 31'), ('Parigi', 'Somnambule', 'rue du Bac 108')],
        nota='In ogni negozio c’è un letto per provare.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un pigiama su misura\nUna taglia\nVendere Sonnambulo\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Anche di notte,', 'rispondiamo di giorno.'],
                                'Per un pigiama su misura, una taglia, o per venderci nel vostro negozio.', VOCI, campi,
                                'Sonnambulo — messaggio', 'Invia', 'Grazie! Vi rispondiamo domattina, dopo il caffè.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'L’atelier', ['In via Vittorio Emanuele,', 'a due passi dal lago.'], size=50, layout='stack',
                                size_mobile=32),
                      K.specs(t, [('Orari', 'martedì — sabato 10:30 — 19:30'), ('La domenica', 'dormiamo'),
                                  ('Su misura', 'si prende la misura sdraiati')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Sonnambulo · atelier', scuro=True, hex_marker=LUNA)], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='atelier', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di dormire', ['Le domande', 'della buonanotte.'], [
        ('La seta si lava in lavatrice?', 'Sì, a trenta gradi col programma seta, in un sacchetto. Si stende all’ombra.'),
        ('Che taglia prendo?', 'Quella dei vestiti: i pigiami sono tagliati larghi apposta.'),
        ('Fate pigiami su misura?', 'Sì: la misura si prende sdraiati, sul letto dell’atelier.'),
        ('Spedite?', 'No: si provano nei negozi. Il negozio più vicino si fa mandare la taglia.'),
    ], 'so-faq', bg=False))
    return pagina(out)


def build_404():
    t = Tpl(ID, '404')
    return pagina([t.section([
        K.centered(t, [K.label(t, 'Errore 404 · sonnambulismo', color=C['acc'], align='center'),
                       K.headline(t, 'Questa pagina <em>è uscita nel sonno.</em>', size=80, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Domattina non si ricorderà dov’è andata. Torniamo a letto, cioè all’inizio.', size=17,
                              align='center'),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_NUBI, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})])


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il sito che si addormenta',
    ['moda', 'pigiami', 'seta', 'notte', 'marchio', 'como', 'scuro', 'slider pro'],
    'Un marchio di pigiami e vestaglie di seta di Como, e un sito che si addormenta: dopo dodici secondi fermi scende la '
    'notte con le Z, e al primo movimento si sveglia. In apertura la parola della notte sdraiata lungo il bordo, che respira, '
    'e la luna che pulsa; il cursore, fermo, sbadiglia.',
    [('I pigiami', PIGIAMI), ('La notte', NOTTE), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('pigiami', 'I pigiami', 'pigiami'), ('la-notte', 'La notte', 'la-notte'), ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Quicksand\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Cormorant\', Georgia, serif',
    '\'Source Code Pro\', ui-monospace, Menlo, monospace', ['Quicksand', 'Cormorant', 'Source Code Pro'], corsivo_di=['Cormorant'],
    peso_titoli='600', raggio=14, grana=4, titoli_line='1.0', titoli_spazio='0')
THEME['cursor']['style'] = 'sonno'
THEME['cursor']['ring_color'] = LUNA
THEME['cursor']['dot_color'] = HEX['accent']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'pigiami.json': build_pigiami(), 'la-notte.json': build_notte(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
