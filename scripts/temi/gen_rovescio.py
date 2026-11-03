# -*- coding: utf-8 -*-
"""Genera il tema «Rovescio» (id rovescio): un marchio di abbigliamento di Milano che cuce i capi col rovescio in vista,
cuciture fuori, etichette fuori, fodere a vista, e li fa double-face: si portano da tutte e due le parti.

Il sito scorre alla rovescia (Effetti di pagina → «Scorrimento alla rovescia», 1.4.957): si apre dal fondo, con
l'apertura in basso e la testata incollata al bordo inferiore dello schermo, e si legge salendo fino al piè di pagina,
che sta in cima. La rotella va al contrario: il gesto di sempre fa salire la pagina. Le righe arrivano dall'alto.

Tema chiaro, tela ecrù, inchiostro nero e il filo arancio delle cuciture. Titoli in Space Grotesk, testo in Inter, dati
in JetBrains Mono. Logo: ROVE dritto e SCIO capovolto, sopra una cucitura tratteggiata. Cursore: lo specchio (un secondo
puntatore rovesciato corre dall'altra parte dello schermo).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, mix, solid, page  # noqa: E402
from kit import Kit, tema  # noqa: E402
import moda as M  # noqa: E402
import pezzi_nov as P  # noqa: E402

ID = 'rovescio'
NAME = 'Rovescio'

HEX = {
    'primary': '#9E300C', 'primary_contrast': '#FFFFFF', 'secondary': '#151515', 'secondary_contrast': '#EDE6DA',
    'accent': '#E8571C', 'dark': '#151515', 'light': '#F6F1E8', 'muted': '#E3DACB', 'muted_contrast': '#151515',
    'text': '#151515', 'text_muted': '#544D44', 'background': '#EDE6DA', 'border': '#CFC4B2', 'link': '#9E300C',
}

HOME, CAPI, ROVESCIO, NEGOZI = '{{PAGINA:home}}', '{{PAGINA:collezione}}', '{{PAGINA:il-rovescio}}', '{{PAGINA:negozi}}'
MAIL, PHONE, PHONE_TEL = 'ciao@rovescio.example', '+39 02 0000 461', 'tel:+39020000461'
LAT, LNG = '45.4930', '9.2240'

V_UOMO, V_ABITO, V_CUCE, V_PIEDINO, V_ROSA, V_ARANCIO, V_BIANCO, V_CAPELLI = (
    'pexels-video-7451431', 'pexels-video-8431506', 'pexels-video-3358672', 'pexels-video-7776736', 'pexels-video-9167971',
    'pexels-video-10508140', 'pexels-video-7792712', 'pexels-video-8328576')
VIDEOS = [V_UOMO, V_ABITO, V_CUCE, V_PIEDINO, V_ROSA, V_ARANCIO, V_BIANCO, V_CAPELLI]
MOD = ['pexels-%s.jpg' % n for n in ('34239221', '12349052', '7760026', '28452456', '40546616', '12911893', '16238582', '9823823')]
CUC = ['pexels-%s.jpg' % n for n in ('6276004', '6275949', '6275961', '6275963', '16603754')]
SAR = ['pexels-%s.jpg' % n for n in ('19340518', '11359034', '3984840', '7776123', '6461152')]
ETI = ['pexels-%s.jpg' % n for n in ('8148725', '9594084', '11134390', '6441572', '9594144', '3558661')]
RIT = ['pexels-%s.jpg' % n for n in ('36484175', '24287019', '32597631', '16532060', '17200343', '29516979')]
APP = ['pexels-%s.jpg' % n for n in ('9594684', '9594686', '9594682', '7671167', '9594678', '8311880')]
LIN = ['pexels-%s.jpg' % n for n in ('1487713', '6843273', '6275942', '3942924')]
GIA = ['pexels-%s.jpg' % n for n in ('30373577', '17730610', '15049674', '19502587')]
STU = ['pexels-%s.jpg' % n for n in ('9850411', '9850083', '9849647', '7283170', '9852958', '8516214')]
NEG = ['pexels-%s.jpg' % n for n in ('8306388', '9594677', '28905704', '13532891', '8997984')]
LOGO_S, LOGO_C = 'logo-rovescio-scuro.png', 'logo-rovescio-chiaro.png'
PHOTOS = MOD + CUC + SAR + ETI + RIT + APP + LIN + GIA + STU + NEG
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS} | {LOGO_S, LOGO_C}

K = Kit(ID, MEDIA, raggio=0, peso_titoli='700', corsivo=False, maiuscole_pulsanti=True, tema_chiaro=True,
        accento_su_media='#FF8A57')
C = K.c
MONO = 'JetBrains Mono'
VOCI = [('map-pin', 'Via Padova 61 · Milano', NEGOZI + '#laboratorio'), ('phone', PHONE, PHONE_TEL), ('mail', MAIL, 'mailto:' + MAIL)]


def alla_rovescia(nodi):
    """Le righe arrivano dall'alto (si sale), e niente effetti sticky: in una pagina rovesciata andrebbero all'indietro."""
    for n in nodi:
        s = n.get('settings') or {}
        if n.get('type') == 'row' and s.get('entrance_animation'):
            s['entrance_animation'] = 'slide-down'
        if n.get('type') == 'section':
            s.pop('sticky_effect', None)
            s.pop('sticky_h_align', None)
        alla_rovescia(n.get('children') or [])
    return nodi


def pagina(nodi):
    return page(alla_rovescia(nodi), page_rovescia=True, page_rovescia_rotella=True)


def build_header():
    # la testata sta in fondo allo schermo (la mette lì lo scorrimento alla rovescia): classica, col fondo e il filo sopra
    sez = M.testata(K, Tpl(ID, 'header'), 'Rovescio', LOGO_S, 150, overlay=False, scuro=False, sticky=False, sticky_show_on_up=False,
                    nav_bg=C['fondo'],
                    border={'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['testo']})
    return sez


def build_footer():
    t = Tpl(ID, 'footer')
    return M.piede(K, t, LOGO_S, '922/233', 'Rovescio cuce a Milano capi da portare da tutte e due le parti: le cuciture fuori, '
                   'le etichette fuori, le fodere a vista. Siete arrivati in cima: il sito finisce qui.',
                   [('La collezione', CAPI), ('Il rovescio', ROVESCIO), ('Negozi', NEGOZI), ('Privacy', '/privacy-policy/')],
                   'Il laboratorio', 'Via Padova 61, Milano<br>Aperto il sabato, 10 — 19', VOCI,
                   '© 2026 Rovescio · Milano · P.IVA 00000000000', [('instagram', 'https://instagram.com'), ('tiktok', 'https://tiktok.com')],
                   larghezza=420, fondo=C['sala'])


# ── L'apertura: il titolo a metà capovolto, in fondo alla pagina ──
def apertura(t):
    m = K.mob
    giu = 'rotate:180deg;'

    def testi(sid, r1, r2, sotto, video):
        livelli = [
            K.livello(sid + '-h', contenuto='↑ Il sito si legge salendo: girate la rotella come sempre', x=50, y=7, w=44, size=12,
                      peso='500', colore=mix(C['chiaro'], 88), align='right', letterSpacing=1, fontFamily=MONO, ritardo=1400,
                      textAlign='right', responsive={'mobile': {'fontSize': m(10), 'y': 56, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello(sid + '-o', contenuto='Rovescio · collezione n.4 · Milano', x=44, y=50, w=50, size=12, peso='500',
                      colore=mix(C['chiaro'], 86), letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=200,
                      textAlign='right', responsive={'mobile': {'fontSize': m(10), 'y': 60, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello(sid + '-t1', contenuto=r1, tag='h1', x=34, y=55, w=60, size=96, peso='700', colore=C['chiaro'],
                      lineHeight=1.0, letterSpacing=-2, entra='maskFromBottom', ritardo=350, durata=1000, textAlign='right',
                      responsive={'mobile': {'fontSize': m(44), 'y': 63.5, 'x': 7, 'width': 90, 'textAlign': 'left'}}),
            # la seconda riga è capovolta: rotate (la proprietà a sé) si somma all'ingresso senza toccarlo
            K.livello(sid + '-t2', fontFamily='Space Grotesk', contenuto=r2, tag='p', x=34, y=67, w=60, size=96, peso='700',
                      colore=K.acc_media, lineHeight=1.0, letterSpacing=-2, entra='flipFromTop', ritardo=650, durata=1300,
                      textAlign='left', customCSS=giu,
                      responsive={'mobile': {'fontSize': m(44), 'y': 68, 'x': 7, 'width': 86, 'textAlign': 'right'}}),
            K.livello(sid + '-s', contenuto=sotto, x=58, y=80.5, w=36, size=16, peso='400', colore=mix(C['chiaro'], 90),
                      lineHeight=1.55, ritardo=900, textAlign='right',
                      responsive={'mobile': {'fontSize': m(13), 'y': 74, 'x': 7, 'width': 86, 'textAlign': 'left'}}),
            K.livello(sid + '-b', tipo='button', contenuto='La collezione', x=70.5, y=89, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, borderRadius=0, letterSpacing=3, textTransform='uppercase',
                      entra='fadeInUp', ritardo=1100, buttonUrl=CAPI, hover={'bgColor': C['nero'], 'duration': 250},
                      responsive={'mobile': {'y': 82, 'x': 7, 'fontSize': m(11)}}),
            K.livello(sid + '-l', tipo='button', contenuto='Il rovescio', x=83, y=89, w='auto', size=12, peso='600',
                      colore=C['chiaro'], bgColor='transparent', padding=16, borderRadius=0, borderWidth=1,
                      borderColor=mix(C['chiaro'], 70), letterSpacing=3, textTransform='uppercase', entra='fadeInUp', ritardo=1200,
                      buttonUrl=ROVESCIO, hover={'bgColor': mix(C['chiaro'], 14), 'borderColor': C['chiaro'], 'duration': 250},
                      responsive={'tablet': {'x': 80}, 'mobile': {'y': 82, 'x': 45, 'fontSize': m(11)}}),
        ]
        veli = K.veli(sid + '-v', 'destra', 40, 70, colore=C['nero'], forza=86, mobile_da=56)
        return K.diapositiva(sid, K.sfondo_video(video, overlay=0.2, colore=C['nero']), veli + livelli, durata=7000)

    sl = [testi('ro-1', 'Il dritto', 'sta dentro.', 'Cuciture fuori, etichette fuori, fodere a vista: capi fatti per essere '
                'girati, e portati da tutte e due le parti.', V_ABITO),
          testi('ro-2', 'Si porta', 'al contrario.', 'Ogni capo è double-face: il lino ecrù da una parte, la cucitura arancio '
                'dall’altra. Si decide la mattina.', V_ARANCIO),
          testi('ro-3', 'La cucitura', 'è il disegno.', 'Non la nascondiamo sotto la fodera: la ribattiamo in arancio, a vista, e '
                'diventa il motivo del capo.', V_PIEDINO)]
    return K.proslider(t, sl, altezza={'mode': 'ratio', 'value': '16:9'}, transizione='flipV', durata_trans=1100, autoplaySpeed=7000,
                       showArrows=False, showDots=True, dotStyle='dash', showProgressBar=True, parallax=False,
                       heightMobile={'mode': 'px', 'value': 1400})


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Rovescio · Milano, via Padova 61', ['Cuciture fuori,', 'per scelta.'], size=64,
                                layout='stack', size_mobile=38)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Una giacca ha due facce e di solito se ne mostra una sola. Noi le cuciamo tutte e due: '
                                'il lino dritto da una parte, le cuciture ribattute in arancio dall’altra, l’etichetta '
                                'cucita fuori dove si legge. La mattina si sceglie quale faccia portare.', size=17),
                      K.button(t, 'Il rovescio', ROVESCIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 100}))
    out.append(t.section([
        t.one([K.sechead(t, 'Dritto e rovescio', ['Trascinate il filo:', 'è lo stesso capo.'], size=56)]),
        t.one([M.dritto_rovescio(K, t, [
            (GIA[0], CUC[0], 'Giacca Ponte', 'Lino ecrù fuori, ribattiture arancio dentro. O viceversa.'),
            (MOD[1], LIN[1], 'Cappotto Fodera', 'La fodera a righe diventa il fuori: basta girarlo.'),
            (RIT[3], ETI[2], 'Felpa Etichetta', 'Le etichette cucite fuori, una per ogni lavaggio che ha fatto.')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Collezione n.4', ['Quattro capi', 'da girare.'], size=56, tagline='Nei negozi da settembre. I prezzi '
                         'sono quelli consigliati.')]),
        t.one([M.capi(K, t, [
            (GIA[1], 'Giacche', 'Ponte', '', 'Lino e cotone, double-face: ecrù o nero, cuciture arancio.', '240 €', 'nuova'),
            (MOD[3], 'Camicie', 'Orlo', ' fuori', 'L’orlo ribattuto all’esterno, il collo che si porta anche rovesciato.', '110 €', ''),
            (MOD[5], 'Pantaloni', 'Cimosa', '', 'La cimosa del tessuto lungo la gamba, in vista: è la firma.', '150 €', ''),
            (RIT[1], 'Cappotti', 'Fodera', '', 'Lana fuori e righe dentro, o righe fuori e lana dentro.', '390 €', '')], CAPI)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([t.one([M.nastro(K, t, ['Cuciture fuori', 'Etichette fuori', 'Fodere a vista', 'Si gira e si riporta'],
                                          fondo=C['testo'], colore=C['fondo'], size=52)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))
    out.append(t.section([
        t.one([K.sechead(t, 'Il lookbook', ['Girati,', 'per strada.'], size=56)]),
        P.fila_foto(K, t, [(MOD[0], 'Il cappotto ecrù col rovescio arancio'), (RIT[0], 'La giacca nera girata'),
                           (MOD[6], 'Il pantalone con la cimosa'), (GIA[3], 'La giacca vista da dietro')]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(M.lettera(K, t, 'La lettera', ['Una volta al mese,', 'scritta a mano.'],
                         'Le collezioni nuove, i negozi dove arrivano, i capi che rifacciamo: e niente altro.',
                         bg=solid(C['sala'])))
    return pagina(out)


def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [K.intro(t, 'La collezione n.4', ['Otto capi,', 'sedici facce.'],
                   'Ogni capo si porta da tutte e due le parti: qui li vedete col dritto, nei negozi provateli anche girati. I '
                   'prezzi sono quelli consigliati.', bg=K.photo_bg(APP[0]), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Giacche e cappotti', ['Il fuori', 'che si cambia.'], size=56)]),
        t.one([M.capi(K, t, [
            (GIA[1], 'Giacche', 'Ponte', '', 'Lino e cotone, double-face, cuciture arancio.', '240 €', 'nuova'),
            (GIA[2], 'Giacche', 'Sbieco', '', 'Tagliata di sbieco, l’orlo grezzo fuori.', '260 €', ''),
            (RIT[1], 'Cappotti', 'Fodera', '', 'Lana fuori e righe dentro, o il contrario.', '390 €', ''),
            (MOD[1], 'Cappotti', 'Imbastito', '', 'Le imbastiture bianche restano: le togliete voi, se volete.', '420 €', '')],
            NEGOZI)]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 90}))
    out.append(t.section([
        t.one([K.sechead(t, 'Camicie, felpe, pantaloni', ['Tutti i giorni,', 'da tutte e due le parti.'], size=56)]),
        t.one([M.capi(K, t, [
            (MOD[3], 'Camicie', 'Orlo', ' fuori', 'L’orlo ribattuto fuori, il collo reversibile.', '110 €', ''),
            (RIT[3], 'Felpe', 'Etichetta', '', 'Le etichette cucite fuori, numerate.', '95 €', ''),
            (MOD[5], 'Pantaloni', 'Cimosa', '', 'La cimosa del tessuto in vista lungo la gamba.', '150 €', ''),
            (MOD[7], 'Gonne', 'Pince', '', 'Le pince cucite a vista come un disegno.', '130 €', 'ultime')], NEGOZI)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'Provate a girarli', ['Dritto', 'e rovescio.'], size=56)]),
        t.one([M.dritto_rovescio(K, t, [
            (MOD[2], CUC[2], 'Abito Telo', 'Il dritto in tela, il rovescio con le cuciture a zig-zag.'),
            (GIA[0], CUC[4], 'Giacca Ponte', 'Ecrù da una parte, arancio dall’altra.'),
            (RIT[5], ETI[4], 'Maglia Lavaggi', 'Fuori le etichette: ognuna dice come lavarla.')])]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))
    return pagina(out)


def build_rovescio():
    t = Tpl(ID, 'il-rovescio')
    out = [K.intro(t, 'Il rovescio', ['Nel laboratorio', 'si cuce due volte.'],
                   'In via Padova cuciamo ogni capo due volte: una per il dritto e una per il rovescio, che non è mai un ripiego. '
                   'Siamo in sei, e ogni capo porta fuori il nome di chi l’ha cucito.', bg=K.video_bg(V_CUCE), size=88)]
    out.append(t.section([
        t.one([K.sechead(t, 'Come nasce un capo', ['Tre passaggi,', 'due facce.'], size=56)]),
        t.one([P.tappe(K, t, [
            ('1', 'il taglio', SAR[0], 'Tagliare', 'Due tessuti', 'in uno',
             'Il dritto e il rovescio si tagliano insieme, sovrapposti: devono combaciare al millimetro.', '2', 'tessuti'),
            ('2', 'la cucitura', CUC[1], 'Cucire', 'La ribattitura', 'arancio',
             'Le cuciture si ribattono fuori, col filo arancio: tengono il capo e lo disegnano.', '1.400', 'punti a capo'),
            ('3', 'l’etichetta', ETI[0], 'Firmare', 'Il nome', 'di chi cuce',
             'L’etichetta va fuori, col nome di chi ha cucito e la data.', '1', 'firma')], icona='scissors')]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.sechead(t, 'In laboratorio', ['Chi cuce', 'al contrario.'], size=56)]),
        K.squadra(t, [
            (RIT[2], 'Marta Colombo', 'Fondatrice e modellista', 'Ha disegnato la prima giacca double-face nel 2019, per sé.',
             'center 25%'),
            (RIT[4], 'Samir Haddad', 'Sarto', 'Ribatte le cuciture: millequattrocento punti a giacca.', 'center 25%'),
            (MOD[4], 'Giulia Sala', 'Tessuti', 'Sceglie i tessuti a coppie: devono stare bene da tutte e due le parti.',
             'center 25%')], ratio='4/5', size='320', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))
    out.append(K.video_frase(t, V_UOMO, 'Il manifesto', 'Niente <em>da nascondere.</em>',
                             'Un capo che si può girare dura il doppio: quando una faccia si consuma, c’è l’altra.', overlay=24))
    out.append(t.section([
        t.row([t.col([K.image(t, STU[3], 'Il laboratorio di via Padova', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([P.citazione(K, t, 'Ho girato il cappotto per sbaglio, in metro. Una signora mi ha chiesto dove l’avevo '
                                        'comprato: quello col rovescio fuori.', 'Luca, Milano', size='38')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))
    return pagina(out)


def build_negozi():
    t = Tpl(ID, 'negozi')
    out = [K.intro(t, 'I negozi', ['Dove provarli', 'girati.'],
                   'Rovescio non ha un negozio online: i capi si provano, da tutte e due le parti. Li trovate in questi negozi e, '
                   'il sabato, in laboratorio.', bg=K.photo_bg(NEG[1]), size=88)]
    out.append(M.negozi(K, t, [
        ('Milano', 'Laboratorio Rovescio', 'via Padova 61, il sabato'), ('Milano', 'Cimosa', 'corso di Porta Ticinese 32'),
        ('Torino', 'Bottone Blu', 'via Po 18'), ('Bologna', 'Ago e Filo', 'via del Pratello 40'),
        ('Firenze', 'Il Taglio', 'borgo San Frediano 21'), ('Roma', 'Fodera', 'via del Pigneto 77'),
        ('Napoli', 'Imbastitura', 'via dei Tribunali 150'), ('Parigi', 'L’Envers', 'rue de Charonne 45')],
        nota='L’elenco si aggiorna a ogni collezione.'))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Scrivete per', 'motivo', opzioni='Un capo da rifare\nVendere Rovescio nel vostro negozio\nLa stampa\nAltro'),
             K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
    out.append(K.sezione_modulo(t, 'scrivi', 'Scriveteci', ['Due righe,', 'rispondiamo noi.'],
                                'Per un capo da aggiustare, per venderci nel vostro negozio o solo per una domanda.', VOCI, campi,
                                'Rovescio — messaggio', 'Invia', 'Grazie! Vi rispondiamo entro due giorni.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il laboratorio', ['In via Padova,', 'il sabato.'], size=50, layout='stack', size_mobile=32),
                      K.specs(t, [('Orari', 'sabato 10 — 19'), ('Metro', 'M1 Rovereto, cinque minuti'),
                                  ('Cosa si fa', 'si provano i capi e si aggiustano quelli vecchi')])], wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Rovescio · laboratorio', scuro=False, hex_marker=HEX['primary'])], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='laboratorio', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima di comprare', ['Le domande', 'di chi gira i capi.'], [
        ('Come si lavano?', 'A trenta gradi, girati sul lato che volete lavare meno: c’è scritto sull’etichetta, fuori.'),
        ('Le cuciture fuori si rovinano?', 'No: sono ribattute due volte, più forti di quelle nascoste.'),
        ('Rifate i capi vecchi?', 'Sì, in laboratorio il sabato: anche quelli di altri marchi, se si possono girare.'),
        ('Spedite?', 'No: si provano nei negozi. Se il negozio più vicino non ha la taglia, la manda lui.'),
    ], 'ro-faq', bg=False))
    return pagina(out)


def build_404():
    t = Tpl(ID, '404')
    return pagina([t.section([
        K.centered(t, [K.label(t, 'Errore 404 · pagina girata', color=K.acc_media, align='center'),
                       K.headline(t, 'Questa pagina <em>è rimasta dentro.</em>', size=76, tag='h1', align='center', pad_bottom=20,
                                  color=C['chiaro'], accento=K.acc_media),
                       K.text(t, 'Non c’è, o l’abbiamo girata dalla parte sbagliata. Torniamo all’inizio, che qui è in fondo.',
                              size=17, align='center', color=K.soft_media),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BIANCO, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})])


THEME = tema(
    ID, NAME, 'Moda e bellezza', 'Il sito alla rovescia',
    ['moda', 'abbigliamento', 'double-face', 'marchio', 'milano', 'chiaro', 'scorrimento alla rovescia', 'slider pro'],
    'Un marchio di abbigliamento che cuce i capi col rovescio in vista, e un sito che scorre al contrario: si apre dal '
    'fondo, la testata sta in basso, si legge salendo fino al piè di pagina in cima, e la rotella va al contrario. Il logo '
    'a metà capovolto, il dritto e il rovescio da trascinare, la collezione, i negozi che lo vendono.',
    [('La collezione', CAPI), ('Il rovescio', ROVESCIO), ('Negozi', NEGOZI)],
    [('home', 'Home', None), ('collezione', 'La collezione', 'collezione'), ('il-rovescio', 'Il rovescio', 'il-rovescio'),
     ('negozi', 'Negozi', 'negozi')],
    HEX, '\'Inter\', system-ui, -apple-system, \'Segoe UI\', sans-serif', '\'Space Grotesk\', system-ui, sans-serif',
    '\'JetBrains Mono\', ui-monospace, Menlo, monospace', ['Inter', 'Space Grotesk', 'JetBrains Mono'], corsivo_di=[],
    peso_titoli='700', raggio=0, grana=3, titoli_line='0.98', titoli_spazio='-0.02em')
THEME['cursor']['style'] = 'specchio'
THEME['cursor']['ring_color'] = HEX['text']
THEME['cursor']['dot_color'] = HEX['accent']


def main():
    write_theme(THEME, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'collezione.json': build_collezione(), 'il-rovescio.json': build_rovescio(), 'negozi.json': build_negozi(),
                        '404.json': build_404()}, MEDIA)


if __name__ == '__main__':
    main()
