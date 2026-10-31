# -*- coding: utf-8 -*-
"""Genera il tema «Insegna» (id insegna): un'agenzia strategica di Milano che costruisce marchi per aziende che spendono
per durare (hotel, cantine, maison, orologerie, cantieri navali): strategia, identità, piattaforme digitali, contenuti e
la custodia tecnica, con il dominio registrato a nome del cliente, l'hosting, la posta, le copie e un referente.

Rifatto il 31 ottobre 2026 (la prima versione, chiara col violetto, «non si poteva vedere»): tema scuro, nero, avorio e un
rosso segnale usato poco; titoli in Noto Serif Display leggero col corsivo, testo in Instrument Sans, dati in IBM Plex Mono.

Apertura: il cubo dei mestieri. Uno Slider Pro alto quanto lo schermo che resta fermo in cima mentre si scorre, e il cubo
gira insieme alla pagina (si ferma a metà se ci si ferma, torna indietro risalendo): quattro facce, Strategia, Identità,
Piattaforme e Custodia tecnica, ognuna col suo video pieno, un velo scuro solo dal lato del testo e un collegamento a
una parte diversa del sito. Un solo effetto forte per pagina: nella pagina dei Lavori le sezioni salgono piegate da
sotto lo schermo.

Il preventivo: il Configuratore a macroblocchi (Strategia e identità, Piattaforma digitale, Contenuti, Dominio e hosting,
Assistenza), un blocco alla volta, coi subtotali, il totale una tantum e il canone di ogni anno, nel modulo della pagina.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'insegna'
NAME = 'Insegna'

HEX = {
    'primary': '#EDE6D9', 'primary_contrast': '#0B0B0C', 'secondary': '#8C9BAB', 'secondary_contrast': '#0B0B0C',
    'accent': '#FF5A36', 'dark': '#050506', 'light': '#F6F2EA', 'muted': '#141416', 'muted_contrast': '#ECE7DE',
    'text': '#ECE7DE', 'text_muted': '#A39D92', 'background': '#0B0B0C', 'border': '#2B2B2F', 'link': '#EDE6D9',
}

HOME, LAVORI, TECNICA, STUDIO, PREVENTIVO = ('{{PAGINA:home}}', '{{PAGINA:lavori}}', '{{PAGINA:dominio-e-hosting}}',
                                             '{{PAGINA:lo-studio}}', '{{PAGINA:preventivo}}')
MAIL, PHONE, PHONE_TEL = 'studio@insegna.example', '+39 02 0000 4471', 'tel:+390200004471'
LAT, LNG = '45.4719', '9.1873'

V_NOTTE, V_FOIL, V_SCHERMO, V_SERVER, V_INCHIOSTRO, V_PROFUMO, V_MODA, V_STAMPA, V_HOTEL, V_VIGNA = (
    'pexels-video-7669077', 'pexels-video-5963494', 'pexels-video-8100340', 'pexels-video-7140928', 'pexels-video-6071645',
    'pexels-video-7815751', 'pexels-video-3917524', 'pexels-video-3838400', 'pexels-video-34555902', 'pexels-video-36331668')
PHOTOS = [
    'pexels-28818507.jpg', 'pexels-14036246.jpg', 'pexels-2581022.jpg', 'pexels-30557572.jpg', 'pexels-7779763.jpg',
    'pexels-9849661.jpg', 'pexels-6758238.jpg', 'pexels-5673448.jpg', 'pexels-4871676.jpg', 'pexels-36662527.jpg',
    'pexels-36779955.jpg', 'pexels-36779951.jpg', 'pexels-23073436.jpg', 'pexels-5583620.jpg', 'pexels-9618453.jpg',
    'pexels-8327977.jpg', 'pexels-8327876.jpg', 'pexels-28821621.jpg', 'pexels-19047722.jpg', 'pexels-7598019.jpg',
    'pexels-7598011.jpg', 'pexels-6567868.jpg', 'pexels-6567864.jpg', 'pexels-14553704.jpg', 'pexels-17515568.jpg',
    'pexels-7428097.jpg', 'pexels-17323801.jpg', 'pexels-37605913.jpg', 'pexels-7045939.jpg', 'pexels-36831237.jpg',
    'pexels-15535141.jpg', 'pexels-6803550.jpg', 'pexels-4271698.jpg', 'pexels-15757520.jpg', 'pexels-8532943.jpg',
    'pexels-11188241.jpg', 'pexels-7578682.jpg', 'pexels-36378236.jpg', 'pexels-30743729.jpg', 'pexels-11395984.jpg',
]
VIDEOS = [V_NOTTE, V_FOIL, V_SCHERMO, V_SERVER, V_INCHIOSTRO, V_PROFUMO, V_MODA, V_STAMPA, V_HOTEL, V_VIGNA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='300', corsivo=True, maiuscole_pulsanti=True, tema_chiaro=False)
C = K.c
SEGNALE = C['acc2']
TITOLI, MONO = 'Noto Serif Display', 'IBM Plex Mono'
VOCI = [('map-pin', 'Via Solferino 40 · Milano', STUDIO + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Insegna', logo_size='24')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Insegna', 'Agenzia strategica di marca, Milano. Strategia, identità, piattaforme digitali e contenuti '
                   'per marchi che vogliono durare. E dietro, la tecnica: il dominio a nome vostro, l’hosting, la posta, le '
                   'copie e un referente che risponde.',
                   [('Lavori', LAVORI), ('Dominio e hosting', TECNICA), ('Lo studio', STUDIO), ('Preventivo', PREVENTIVO),
                    ('Privacy', '/privacy-policy/')],
                   'Lo studio', 'Lunedì — venerdì 9 — 19<br>Custodia tecnica sette giorni su sette', VOCI,
                   '© 2026 Insegna srl · Milano · P.IVA 00000000000 · Prezzi IVA esclusa',
                   [('instagram', 'https://instagram.com'), ('linkedin', 'https://linkedin.com')], marchio_size=140)


# ── L'apertura: il cubo dei mestieri ──
FACCE = [  # linguetta, mestiere, riga 1, riga 2, testo, video, pulsante, link, caso
    ('Strategia', 'Strategia', 'Prima la strategia,', 'poi la forma.',
     'Ricerca, posizionamento, architettura di marca: decidiamo insieme dove deve stare il vostro nome, prima di disegnarlo.',
     V_NOTTE, 'Il metodo', STUDIO + '#metodo', 'Caso · Cantine Morelli Vigna, Langhe'),
    ('Identità', 'Identità', 'Un segno che', 'si riconosce al buio.',
     'Nome, marchio, sistema tipografico, materiali: un’identità che regge su un biglietto in rilievo e su un’insegna di sei metri.',
     V_FOIL, 'I lavori', LAVORI, 'Caso · Orologeria Bassani 1911, Torino'),
    ('Piattaforme', 'Piattaforme', 'Siti che si usano,', 'non che si guardano.',
     'Esperienze digitali su misura, veloci e accessibili, collegate ai vostri sistemi: vendita, prenotazioni, aree riservate.',
     V_SCHERMO, 'Il preventivo', PREVENTIVO, 'Caso · Hotel Altamira, Lago di Como'),
    ('Custodia', 'Custodia tecnica', 'Il dominio è vostro.', 'Al resto pensiamo noi.',
     'Registriamo i domini a nome vostro, teniamo hosting, posta, certificati e copie. Un referente, quattro ore per rispondere.',
     V_SERVER, 'Dominio e hosting', TECNICA, 'Oggi · 212 domini a nome dei clienti'),
]


def cubo(t):
    m = K.mob
    sl = []
    for i, (tab, mestiere, r1, r2, testo, video, bottone, link, caso) in enumerate(FACCE):
        n = i + 1
        livelli = K.veli('in-velo%d' % n, 'sinistra', 44, 72, colore=C['nero'], forza=88, mobile_da=44) + [
            K.livello('in-n%d' % n, contenuto='0%d / 04 · %s' % (n, mestiere), x=6, y=16, w=50, size=12, peso='500',
                      colore=SEGNALE, letterSpacing=3, textTransform='uppercase', fontFamily=MONO, ritardo=150,
                      responsive={'tablet': {'y': 14}, 'mobile': {'fontSize': m(10), 'y': 12, 'x': 7, 'width': 86}}),
            # Le due righe del titolo in un livello solo (a capo e corsivo): separate, con l'altezza dello schermo che
            # cambia e il carattere che segue la larghezza, in una finestra larga e bassa si sovrapponevano (31 ott 2026).
            K.livello('in-t%d' % n, contenuto=r1 + '<br><em>' + r2 + '</em>', tag='h1' if n == 1 else 'h2', x=6, y=36,
                      w=80, size=96, peso='300', colore=C['chiaro'], fontFamily=TITOLI, lineHeight=1.04, letterSpacing=-2,
                      entra='maskFromBottom', ritardo=250, durata=1300,
                      responsive={'tablet': {'fontSize': 64, 'y': 40, 'width': 88},
                                  'mobile': {'fontSize': m(36), 'y': 44, 'x': 7, 'width': 88}}),
            K.livello('in-s%d' % n, contenuto=testo, x=6, y=72, w=34, size=17, peso='400', colore=mix(C['chiaro'], 84),
                      lineHeight=1.6, ritardo=900,
                      responsive={'tablet': {'fontSize': 18, 'y': 63, 'width': 62},
                                  'mobile': {'fontSize': m(14), 'y': 60, 'x': 7, 'width': 86}}),
            K.livello('in-b%d' % n, tipo='button', contenuto=bottone, x=6, y=86, w='auto', size=12, peso='600',
                      colore=C['chiaro'], bgColor='transparent', padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=999, borderWidth=1, borderColor=mix(C['chiaro'], 55), ritardo=1150, buttonUrl=link,
                      hover={'bgColor': C['chiaro'], 'color': C['nero'], 'borderColor': C['chiaro'], 'duration': 250},
                      responsive={'tablet': {'y': 78}, 'mobile': {'y': 77, 'x': 7, 'fontSize': m(11)}}),
            K.livello('in-c%d' % n, contenuto=caso, x=62, y=90, w=32, size=11, peso='400', colore=mix(C['chiaro'], 72),
                      letterSpacing=2, textTransform='uppercase', fontFamily=MONO, align='right', ritardo=1300,
                      responsive={'tablet': {'visible': False}, 'mobile': {'visible': False}}),
        ]
        if n == 1:
            livelli.append(K.livello('in-scorri', contenuto='Scorrete: il cubo gira', x=40, y=94, w=20, size=11, peso='500',
                                     colore=mix(C['chiaro'], 70), letterSpacing=3, textTransform='uppercase', fontFamily=MONO,
                                     align='center', entra='fadeIn', ritardo=1800,
                                     responsive={'tablet': {'visible': True, 'y': 92, 'x': 30, 'width': 40},
                                                 'mobile': {'y': 92, 'x': 10, 'width': 80, 'fontSize': m(10)}}))
        sl.append(K.diapositiva('in-%d' % n, K.sfondo_video(video, overlay=0.22, colore=C['nero']), livelli, durata=9000,
                                tab=tab))
    return K.proslider(t, sl, altezza={'mode': 'vh', 'value': 100}, transizione='cubeH', durata_trans=900,
                       heightNotebook={'mode': 'vh', 'value': 100}, heightTablet={'mode': 'vh', 'value': 100},
                       heightMobile={'mode': 'vh', 'value': 100}, autoplay=False, scrollTimeline=True,
                       scrollTimelineDistance=2400, scrollScrub=True, showArrows=False, showDots=False, showTabs=False,
                       showProgressBar=False, parallax=False, swipe=False, navColor=C['chiaro'],
                       globalBackground={'type': 'color', 'color': C['nero']})


# ── I mestieri, i lavori ──
def mestieri(t):
    voci = [('Strategia di marca', 'Ricerca · posizionamento · architettura', 'Interviste, mercato, concorrenti: il posto '
             'giusto per il vostro nome, scritto in una piattaforma di marca che tutti in azienda capiscono.', 'pexels-7598019.jpg'),
            ('Identità', 'Nome · marchio · sistema', 'Il marchio, i caratteri, i colori, i materiali e il manuale per usarli: '
             'dal biglietto in rilievo all’insegna, dal profilo social alla fiancata di una barca.', 'pexels-6567864.jpg'),
            ('Piattaforme digitali', 'Siti · vendita · prenotazioni', 'Progettate e costruite da noi, veloci, accessibili, '
             'collegate a magazzino, gestionale e prenotazioni. Le aggiornate da soli.', 'pexels-14553704.jpg'),
            ('Contenuti', 'Testi · fotografia · film', 'Scriviamo con voi e giriamo nei vostri luoghi: niente immagini di '
             'repertorio, nessuna frase che potrebbe dire anche un concorrente.', 'pexels-17515568.jpg'),
            ('Custodia tecnica', 'Dominio · hosting · sicurezza', 'Il dominio registrato a vostro nome, l’hosting in Europa, '
             'la posta, le copie ogni notte e un referente che risponde entro quattro ore.', 'pexels-37605913.jpg')]
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '%02d' % (k + 1), 'image': K.img(f), 'link_url': PREVENTIVO,
                   'color': C['acc']} for k, (n, s, d, f) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'circle', 'number_color': SEGNALE,
        'number_hover_color': SEGNALE, 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': 54, 'name_weight': '300', 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['acc'], 'sub_size': 12,
        'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 26, 'hover_indent': 28,
        'hover_bg': mix(C['acc'], 5), 'line_color': C['filo'], 'peek': True, 'peek_mode': 'image', 'peek_width': 280,
        'peek_ratio': '4/5', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


CASI = [  # foto, occhiello, titolo, ancora, colonne
    ('pexels-14036246.jpg', 'Ospitalità · Lago di Como', 'Hotel Altamira', 'altamira', 7),
    ('pexels-2581022.jpg', 'Vino · Langhe', 'Cantine Morelli Vigna', 'morelli', 5),
    ('pexels-8327977.jpg', 'Orologeria · Torino', 'Bassani 1911', 'bassani', 5),
    ('pexels-4871676.jpg', 'Nautica · Viareggio', 'Officine Navali Sereni', 'sereni', 7),
    ('pexels-9849661.jpg', 'Moda · Milano', 'Atelier Corte', 'corte', 6),
    ('pexels-36779955.jpg', 'Profumi · Firenze', 'Maison Elle', 'elle', 6),
]


def casi(t, voci=CASI):
    return t.classic('showcasegrid', {
        'items': [{'image': K.img(f), 'kicker': k, 'title': ti, 'link': LAVORI + '#' + a, 'span': s, 'aspect': '4/3'}
                  for f, k, ti, a, s in voci],
        'columns': 2, 'gap': 18, 'object_position': 'center center', 'object_fit': 'cover', 'radius': 2,
        'media_bg': C['nero'], 'veil_color': C['nero'], 'kicker_color': C['chiaro'], 'title_color': C['chiaro'],
        'arrow_bg': mix(C['chiaro'], 22), 'arrow_color': C['chiaro'], 'arrow_hover_bg': C['acc'],
        'arrow_hover_color': C['nero'], 'show_arrow': True, 'title_size': 40, 'title_weight': '300', 'kicker_size': 11,
        'card_radius': radius(2)}, style={})


def testimonianza(t, testo, nome, ruolo):
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': SEGNALE, 'quote_font': 'heading',
        'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': SEGNALE}, style={})


def clienti(t):
    return t.section([t.one([t.classic('marquee', {
        'content_type': 'text', 'text_items': 'Hotel Altamira | Cantine Morelli Vigna | Bassani 1911 | Officine Navali Sereni | '
                                              'Atelier Corte | Maison Elle | Studio Lorenzi Architetti | Galleria Ferri',
        'separator': ' · ', 'speed': '24', 'direction': 'left', 'pause_hover': True, 'gap': 40, 'velocity_skew': False,
        'bg_color': C['sala'], 'text_color': C['testo'], 'font_size': 44, 'font_weight': '300', 'letter_spacing': 0,
        'text_transform': 'none', 'font_family': 'heading', 'separator_color': SEGNALE, 'height': 120, 'full_width': True})],
        anim=None)], width='fullbleed', padding='remove-vertical')


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, cubo(t))]
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Insegna · Milano', ['Non vendiamo siti.', 'Costruiamo marchi che durano.'], size=64,
                                layout='stack', size_mobile=38)], wm='3-5', ws='1-1'),
               t.col([K.text(t, 'Lavoriamo con poche aziende alla volta, quasi sempre per anni: hotel, cantine, maison, '
                                'orologerie, cantieri. Partiamo da una domanda sola, dove deve stare il vostro nome, e '
                                'costruiamo tutto il resto da lì: il segno, il sito, le parole, le immagini. E la parte '
                                'tecnica, che resta vostra.', size=17, color=C['testo']),
                      K.button(t, 'Lo studio', STUDIO, pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='end'),
        t.one([K.numeri(t, [('18', 'Anni di lavoro'), ('64', 'Marchi seguiti oggi'), ('11', 'Premi di design'),
                            ('4 h', 'Per rispondere a un guasto')])], style={'margin_top': 72}),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 110}))
    out.append(clienti(t))
    out.append(t.section([
        t.one([K.sechead(t, 'I mestieri', ['Cinque mestieri,', 'un solo referente.'], size=60,
                         tagline='Si possono prendere tutti o solo quelli che mancano: il preventivo si compone blocco per blocco.')]),
        t.one([mestieri(t)]),
    ], padding='large', width='large', html_id='mestieri', style={'padding_top': 140, 'padding_bottom': 130}))
    out.append(t.section([
        t.one([K.sechead(t, 'Lavori scelti', ['Sei marchi,', 'sei modi di durare.'], size=60,
                         tagline='Ogni lavoro è cominciato con la stessa domanda e finito con un dominio intestato al cliente.')]),
        t.one([casi(t)]),
        t.one([K.button(t, 'Tutti i lavori', LAVORI, pad_top=30)]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_INCHIOSTRO, 'Il metodo', 'La forma <em>segue la strategia.</em>',
                             'Prima di un colore o di un carattere scriviamo una pagina sola: chi siete, per chi, contro chi. '
                             'Tutto quello che disegniamo dopo deve poterla rileggere.', overlay=40))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Custodia tecnica', ['Tutto a nome vostro,', 'tenuto da noi.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'La parte che di solito si perde fra fornitori, password e rinnovi dimenticati: la teniamo '
                                'noi, con ogni cosa intestata a voi e un referente con un nome e un numero.', size=17),
                      K.button(t, 'Dominio e hosting', TECNICA, pad_top=18)], wm='1-2', ws='1-1'),
               t.col([K.specs(t, [('Dominio', 'registrato a nome vostro'), ('Hosting', 'gestito, in Europa'),
                                  ('Posta', 'col vostro dominio, anche PEC'), ('Copie', 'ogni notte, trenta giorni'),
                                  ('Sicurezza', 'certificati e protezione dagli attacchi'),
                                  ('Risposta', 'entro quattro ore, sette giorni su sette')])], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 130}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Ci hanno chiesto chi volevamo diventare prima di parlare di logo. Due anni dopo l’hotel ha un nome che i clienti '
           'ripetono, un sito che prenota da solo e nessuna password nelle mani di qualcun altro.', 'Elena Altamira',
        'Direttrice · Hotel Altamira')])], bg=solid(C['sala']), padding='large',
        style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_PROFUMO, 'Il preventivo', 'Blocco per blocco, <em>al centesimo.</em>',
                          'Strategia, identità, piattaforma, contenuti, dominio e hosting, assistenza: il totale si aggiorna '
                          'mentre scegliete, e il canone di ogni anno è scritto a parte.', 'Componete il preventivo',
                          PREVENTIVO, overlay=40, video=True))
    return out


# ── Il preventivo a macroblocchi ──
def configuratore(t):
    def opz(i, g, nome, desc, prezzo, canone=0, pred=False, badge='', mn=0, mx=1):
        return {'id': i, 'gruppo': g, 'nome': nome, 'descrizione': desc, 'prezzo': prezzo, 'canone': canone, 'immagine': '',
                'badge': badge, 'predefinita': pred, 'punti': '', 'min': mn, 'max': mx}

    def gruppo(i, titolo, blocco, tipo='uno', obbl=True, aiuto=''):
        return {'id': i, 'titolo': titolo, 'blocco': blocco, 'tipo': tipo, 'obbligatorio': obbl, 'aiuto': aiuto}
    B1, B2, B3, B4, B5 = 'Strategia e identità', 'Piattaforma digitale', 'Contenuti', 'Dominio e hosting', 'Assistenza'
    return t.classic('configuratore', {
        'gruppi': [
            gruppo('strategia', 'La strategia', B1, aiuto='Se avete già una piattaforma di marca recente partiamo da quella.'),
            gruppo('nome', 'Il nome', B1, aiuto='Se il nome c’è già, controlliamo comunque marchio, dominio e social.'),
            gruppo('logo', 'L’identità visiva', B1),
            gruppo('stampati', 'Da toccare', B1, tipo='piu', obbl=False),
            gruppo('sito', 'La piattaforma', B2, aiuto='Istituzionale: chi siete, che cosa fate, dove trovarvi, in più lingue. '
                   'E-commerce: catalogo, pagamenti e spedizioni collegati al magazzino.'),
            gruppo('pagine', 'Pagine e lingue in più', B2, tipo='quantita', obbl=False),
            gruppo('funzioni', 'Funzioni', B2, tipo='piu', obbl=False),
            gruppo('testi', 'I testi', B3),
            gruppo('foto', 'Fotografia e film', B3, tipo='piu', obbl=False),
            gruppo('dominio', 'Il dominio', B4, aiuto='Il dominio è sempre intestato a voi: noi lo gestiamo per vostro conto e lo '
                   'rinnoviamo, avvisandovi un mese prima.'),
            gruppo('hosting', 'L’hosting', B4),
            gruppo('email', 'La posta', B4, tipo='quantita', obbl=False),
            gruppo('sicurezza', 'Sicurezza', B4, tipo='piu', obbl=False),
            gruppo('assistenza', 'L’assistenza', B5, aiuto='Il piano si cambia ogni anno; senza piano si paga l’intervento, '
                   '95 € l’ora.'),
            gruppo('corso', 'Per fare da soli', B5, tipo='piu', obbl=False),
        ],
        'opzioni': [
            opz('strategia-pronta', 'strategia', 'La avete già: partiamo da lì', 'Leggiamo i vostri documenti e li verifichiamo.', 0),
            opz('strategia-workshop', 'strategia', 'Una giornata di posizionamento', 'Workshop con la direzione, una pagina di sintesi.',
                3200, pred=True),
            opz('strategia-completa', 'strategia', 'Strategia di marca completa', 'Ricerca, interviste, piattaforma di marca.', 8500,
                badge='Consigliato'),
            opz('nome-ok', 'nome', 'Il nome c’è già', 'Controlliamo marchio, dominio e social.', 0, pred=True),
            opz('nome-cerca', 'nome', 'Lo cerchiamo insieme', 'Tre territori, nomi con dominio e social liberi.', 2400),
            opz('nome-marchio', 'nome', 'Nome e marchio verificato', 'Con la ricerca di anteriorità del marchio.', 4800),
            opz('logo-restyling', 'logo', 'Evoluzione del marchio esistente', 'Lo ridisegniamo senza perdere chi vi conosce.', 3500),
            opz('logo-nuovo', 'logo', 'Identità nuova', 'Marchio, caratteri, colori, le prime applicazioni.', 9000, pred=True),
            opz('logo-identita', 'logo', 'Sistema d’identità completo', 'Più marchi, icone, materiali e il manuale.', 18000,
                badge='Consigliato'),
            opz('biglietti', 'stampati', 'Carta e biglietti in rilievo', 'Progetto, carte, prove di stampa a caldo.', 1800),
            opz('insegna', 'stampati', 'Insegna e allestimento', 'Il progetto e la direzione dei lavori.', 2600),
            opz('social-kit', 'stampati', 'Sistema per i social', 'Profili, copertine, trenta modelli.', 1400),
            opz('sito-pagina', 'sito', 'Una pagina sola', 'Un racconto che scorre, per un lancio o un prodotto.', 5500),
            opz('sito-vetrina', 'sito', 'Sito istituzionale', 'Fino a dodici pagine, due lingue.', 12000, pred=True,
                badge='Il più scelto'),
            opz('sito-negozio', 'sito', 'E-commerce su misura', 'Catalogo, pagamenti e magazzino collegati.', 28000),
            opz('pagina-piu', 'pagine', 'Pagina in più', 'Oltre alle dodici del sito istituzionale.', 450, mx=20),
            opz('lingua-piu', 'pagine', 'Lingua in più', 'Il sito tradotto, con i suoi indirizzi.', 1800, mx=6),
            opz('prenotazioni', 'funzioni', 'Prenotazioni online', 'Disponibilità, conferme, pagamenti.', 2400),
            opz('notizie', 'funzioni', 'Magazine', 'Le storie del marchio, trovate da Google.', 1600),
            opz('modulo-preventivo', 'funzioni', 'Un preventivo come questo', 'Per i vostri clienti.', 2200),
            opz('area-riservata', 'funzioni', 'Area riservata', 'Listini, documenti e ordini per chi ha l’accesso.', 4500),
            opz('testi-voi', 'testi', 'Li scrivete voi, li rivediamo', 'Correzione, titoli e parole chiave.', 0, pred=True),
            opz('testi-noi', 'testi', 'Li scriviamo noi', 'Dopo due interviste, nel tono della marca.', 3200),
            opz('foto-mezza', 'foto', 'Una giornata di fotografia', 'Luoghi, prodotti, quaranta scatti.', 2800),
            opz('foto-intera', 'foto', 'Due giornate, anche le persone', 'Luoghi e ritratti, cento scatti.', 5200),
            opz('video', 'foto', 'Un film di sessanta secondi', 'Per il sito e per le campagne.', 6500),
            opz('dominio-trasferimento', 'dominio', 'Ce l’avete già: lo trasferiamo', 'Lo intestiamo a voi, se non lo è.', 90,
                canone=30),
            opz('dominio-it', 'dominio', 'Lo registriamo a vostro nome', 'Un dominio .it o .com, rinnovo compreso.', 0, canone=30,
                pred=True),
            opz('dominio-due', 'dominio', 'Il nome protetto: .it, .com e .eu', 'Gli altri portano al primo.', 0, canone=80),
            opz('hosting-base', 'hosting', 'Hosting gestito in Europa', 'Per un sito istituzionale, aggiornato da noi.', 0,
                canone=480, pred=True),
            opz('hosting-veloce', 'hosting', 'Alte prestazioni', 'Con la rete di distribuzione, per l’e-commerce.', 0,
                canone=1200, badge='Per vendere'),
            opz('hosting-dedicato', 'hosting', 'Infrastruttura dedicata', 'Solo per voi, controllata ogni giorno.', 0, canone=4800),
            opz('casella', 'email', 'Casella @vostrodominio', 'Sul telefono e sul computer.', 0, canone=60, pred=True, mx=200),
            opz('pec', 'email', 'Casella PEC', 'La posta certificata col vostro dominio.', 0, canone=40, mx=10),
            opz('ssl', 'sicurezza', 'Certificato del sito', 'Il lucchetto accanto all’indirizzo, rinnovato da solo.', 0,
                pred=True),
            opz('backup', 'sicurezza', 'Copia ogni notte', 'Trenta giorni di copie, fuori dal server.', 0, canone=180, pred=True),
            opz('firewall', 'sicurezza', 'Protezione dagli attacchi', 'Blocca accessi, spam e traffico malevolo.', 0, canone=360),
            opz('assistenza-no', 'assistenza', 'Su chiamata', 'Si paga l’intervento, 95 € l’ora.', 0),
            opz('assistenza-essenziale', 'assistenza', 'Essenziale', 'Aggiornamenti, controlli, risposta in un giorno.', 0,
                canone=1800, pred=True),
            opz('assistenza-serena', 'assistenza', 'Premium', 'Un referente, risposta in quattro ore, modifiche comprese.', 0,
                canone=4800, badge='Consigliato'),
            opz('assistenza-tutto', 'assistenza', 'Partner', 'Contenuti, campagne e crescita, ogni mese.', 0, canone=12000),
            opz('corso', 'corso', 'Mezza giornata di formazione', 'Per aggiornare pagine, foto e notizie.', 900),
        ],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'Una tantum', 'testo_canone': 'Ogni anno',
        'nota_prezzo': 'IVA esclusa. Il preventivo definitivo arriva dopo il primo incontro.', 'periodo': '',
        'mostra_differenze': True, 'riepilogo': True, 'titolo_riepilogo': 'Il vostro preventivo', 'a_passi': True,
        'testo_azzera': 'Ricomincia', 'testo_pulsante': 'Mandatecelo', 'link_azione': '', 'campo_modulo': 'preventivo',
        'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': C['sala'], 'colore_scelta': C['acc'],
        'colore_testo': C['testo'], 'colore_tenue': C['tenue'], 'colore_prezzo': C['acc'], 'sfondo_riepilogo': C['sala'],
        'sfondo_badge': SEGNALE, 'colore_badge': C['nero'], 'raggio_scheda': radius(2), 'gruppo_color': C['testo'],
        'gruppo_font_weight': '400', 'totale_color': C['acc'], 'totale_font_weight': '400'}, style={})


def build_preventivo():
    t = Tpl(ID, 'preventivo')
    out = [K.intro(t, 'Il preventivo', ['Blocco per blocco,', 'al centesimo.'],
                   'Cinque blocchi: strategia e identità, piattaforma, contenuti, dominio e hosting, assistenza. Il totale una '
                   'tantum e il canone di ogni anno si aggiornano mentre scegliete; alla fine il preventivo arriva nel modulo '
                   'qui sotto.', bg=K.video_bg(V_STAMPA), size=96)]
    out.append(t.section([t.one([configuratore(t)])], padding='large', width='large',
                         style={'padding_top': 110, 'padding_bottom': 110}))
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('text', 'Azienda', 'azienda', True),
             K.campo('text', 'Ruolo', 'ruolo'), K.campo('email', 'Email', 'email', True), K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Come vi seguiamo', 'come', True,
                     opzioni='Tutto: dalla strategia alla custodia tecnica\nSolo la parte tecnica: dominio, hosting, posta\n'
                             'Strategia e identità\nLa piattaforma digitale'),
             K.campo('textarea', 'Il preventivo', 'preventivo', larghezza='1-1',
                     segnaposto='Componetelo qui sopra e premete «Mandatecelo»: arriva qui da solo.'),
             K.campo('textarea', 'Qualcosa da aggiungere', 'messaggio', larghezza='1-1',
                     segnaposto='Chi siete, che cosa avete già, entro quando vi serve.')]
    out.append(K.sezione_modulo(t, 'richiesta', 'La richiesta', ['Un incontro,', 'poi il preventivo firmato.'],
                                'Vi chiama Marta per mezz’ora di domande, poi ci vediamo in studio o da voi. Il preventivo '
                                'definitivo arriva entro una settimana, con le date.', VOCI, campi, 'Insegna — preventivo',
                                'Inviate la richiesta', 'Ricevuta. Marta vi chiama entro due giorni lavorativi.'))
    out.append(K.domande(t, 'Prima', 'di firmare.', [
        ('Il dominio è nostro?', 'Sì, sempre: lo registriamo con i vostri dati, voi ne siete gli intestatari. Noi lo gestiamo '
         'per vostro conto e lo rinnoviamo, avvisandovi un mese prima.'),
        ('Possiamo portare tutto altrove?', 'Quando volete. Vi consegniamo file, database, codice e chiavi per trasferire il '
         'dominio, senza costi e senza domande.'),
        ('Che cosa comprende il canone?', 'Quello che avete scelto: dominio, hosting, caselle, copie, protezione e il piano di '
         'assistenza. Si paga una volta all’anno.'),
        ('Come si paga?', 'Un terzo alla firma, un terzo all’approvazione dell’identità, un terzo alla messa online.'),
        ('Quanto tempo serve?', 'Strategia e identità in otto-dieci settimane, la piattaforma in altre otto. Una pagina sola, '
         'per un lancio, in quattro.'),
    ], 'is-faq'))
    return out


# ── Dominio e hosting ──
def build_tecnica():
    t = Tpl(ID, 'dominio-e-hosting')
    out = [K.intro(t, 'Dominio e hosting', ['Tutto a nome vostro,', 'tenuto da noi.'],
                   'La parte tecnica di solito si perde fra fornitori, password e rinnovi dimenticati. Qui la teniamo noi, '
                   'con ogni cosa intestata a voi e un referente che risponde entro quattro ore.',
                   bg=K.video_bg(V_SERVER), size=96)]
    passi = t.classic('worklist', {
        'items': [{'number': nu, 'title': ti, 'category': ca, 'year': gr, 'link_url': PREVENTIVO} for nu, ti, ca, gr in [
            ('01', 'Registriamo il dominio a vostro nome', 'Il .it, il .com, il .eu · in giornata', '30 €/anno'),
            ('02', 'Accendiamo hosting e certificato', 'Server gestiti in Europa · il lucchetto accanto all’indirizzo',
             'da 480 €/anno'),
            ('03', 'Creiamo la posta', 'Sul telefono e sul computer · anche la PEC', '60 €/anno'),
            ('04', 'Facciamo le copie ogni notte', 'Trenta giorni di copie, fuori dal server', '180 €/anno'),
            ('05', 'Aggiorniamo e sorvegliamo', 'Un controllo ogni minuto · un rapporto al mese', 'da 1.800 €/anno'),
            ('06', 'Rispondiamo', 'Entro quattro ore, sette giorni su sette', 'compreso')]],
        'divider_color': C['filo'], 'row_padding_y': 26, 'row_hover_bg': mix(C['acc'], 5), 'hover_indent': 28,
        'number_color': SEGNALE, 'number_size': 13, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 34, 'title_weight': '300', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': C['acc'], 'year_size': 18, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': MONO}, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Il processo tecnico', ['Sei cose', 'che non dovete più ricordare.'], size=56,
                         tagline='Ogni voce entra nel canone solo se la scegliete. I prezzi sono quelli del preventivo.')]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'L’assistenza', ['Tre piani,', 'o su chiamata.'], size=56,
                         tagline='Senza piano ci chiamate quando serve e pagate l’intervento, 95 € l’ora. Il piano si cambia ogni anno.')]),
        K.piani(t, [('Essenziale', '1.800', '/anno', 'Aggiornamenti di sicurezza\nUn controllo ogni minuto\nRisposta in un giorno',
                     False, ''),
                    ('Premium', '4.800', '/anno', 'Tutto l’Essenziale\nUn referente con nome e numero\nRisposta in quattro ore\n'
                     'Modifiche comprese', True, 'Consigliato'),
                    ('Partner', '12.000', '/anno', 'Tutto il Premium\nContenuti e campagne ogni mese\nUn’analisi ogni trimestre',
                     False, '')],
                    'Scegliete nel preventivo', PREVENTIVO, nota='IVA esclusa', sfondo=C['nero']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.domande(t, 'Domande', 'tecniche.', [
        ('Che cos’è il DNS e chi lo gestisce?', 'È la rubrica che dice a internet dove sta il vostro sito e dove arriva la '
         'posta. La gestiamo noi, e se cambiate fornitore la passiamo a lui in un’ora.'),
        ('La posta vecchia si perde?', 'No: la trasferiamo nelle caselle nuove, cartelle comprese, di notte, quando non '
         'scrivete.'),
        ('Il sito rispetta privacy e cookie?', 'Sì: informativa, banner e registro dei consensi sono compresi, e li aggiorniamo '
         'quando cambiano le regole.'),
        ('E se il server si ferma?', 'Lo sappiamo prima di voi: un controllo ogni minuto ci avvisa. Con la copia di stanotte '
         'il sito torna su in meno di un’ora.'),
    ], 'is-faq-tec'))
    out.append(K.pannello(t, 'pexels-17323801.jpg', 'Il preventivo', 'Solo la parte tecnica? <em>Si può.</em>',
                          'Se il sito ce l’avete già, nel preventivo scegliete solo il blocco Dominio e hosting e l’assistenza.',
                          'Componete il preventivo', PREVENTIVO, overlay=40))
    return out


# ── Lavori ──
def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [K.intro(t, 'Lavori', ['Marchi costruiti', 'per durare.'],
                   'Ospitalità, vino, orologeria, nautica, moda, profumi. Per ognuno la strategia, il segno, la piattaforma e '
                   'un dominio intestato a loro.', bg=K.video_bg(V_VIGNA), size=96)]
    tutti = CASI + [('pexels-5583620.jpg', 'Architettura · Roma', 'Studio Lorenzi', 'lorenzi', 5),
                    ('pexels-5673448.jpg', 'Arredo · Brianza', 'Galleria Ferri', 'ferri', 7)]
    # La piega (un solo effetto forte in questa pagina): le sezioni dei lavori salgono piegate da sotto lo schermo.
    out.append(t.section([
        t.one([K.sechead(t, 'Otto storie', ['Otto marchi,', 'otto domini a loro nome.'], size=56,
                         tagline='Passate sopra una scheda: la freccia porta alla storia del lavoro.')]),
        t.one([casi(t, tutti)]),
    ], padding='large', width='large', html_id='storie', scroll_entrance='piega',
        style={'padding_top': 120, 'padding_bottom': 120}))
    dettagli = [('pexels-4271698.jpg', 'Scatole nere di misure diverse per un cofanetto'),
                ('pexels-8532943.jpg', 'Un biglietto bianco accanto a un vaso di ceramica'),
                ('pexels-6567868.jpg', 'Campioni di carta e di materiali sul tavolo'),
                ('pexels-15757520.jpg', 'Un cucchiaio dorato in una scatola nera')]
    out.append(t.section([
        t.one([K.sechead(t, 'I dettagli', ['Da toccare,', 'da guardare.'], size=56,
                         tagline='Carte, scatole, materiali: quello che resta in mano ai clienti dei nostri clienti.')]),
        t.row([t.col([K.image(t, f, alt, ratio='4/5')], wm='1-4', ws='1-2') for f, alt in dettagli], layout='25-25-25-25',
              gap=18),
    ], bg=solid(C['sala']), padding='large', width='large', scroll_entrance='piega',
        style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([K.centered(t, [testimonianza(
        t, 'Pensavamo di rifare l’etichetta. Ci hanno fatto rifare la domanda: chi beve il nostro vino e perché. L’etichetta è '
           'venuta dopo, ed è la prima cosa che ci chiedono in fiera.', 'Ottavio Morelli', 'Cantine Morelli Vigna · Langhe')])],
        padding='large', style={'padding_top': 120, 'padding_bottom': 110}))
    out.append(t.section([
        t.one([K.numeri(t, [('64', 'Marchi seguiti oggi'), ('212', 'Domini a nome dei clienti'), ('11', 'Premi di design'),
                            ('100%', 'Chiavi consegnate')])]),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 90}))
    out.append(K.pannello(t, V_HOTEL, 'Il prossimo', 'Il vostro nome, <em>qui.</em>',
                          'Raccontateci che cosa fate: il preventivo si compone in cinque minuti.', 'Componete il preventivo',
                          PREVENTIVO, overlay=40, video=True))
    return out


# ── Lo studio ──
def metodo(t):
    voci = [('Settimane 1 — 2', 'Ascolto', 'Interviste e ricerca', 'Parliamo con la direzione, con chi vende e con chi '
             'compra. Guardiamo i concorrenti come li guarda un cliente.', 'pexels-7598011.jpg', 'search'),
            ('Settimane 3 — 4', 'Strategia', 'Una pagina sola', 'Chi siete, per chi, contro chi: la piattaforma di marca, '
             'discussa e firmata prima di disegnare.', 'pexels-7598019.jpg', 'compass'),
            ('Settimane 5 — 10', 'Forma', 'Due strade, una scelta', 'Il segno, i caratteri, i materiali; poi la piattaforma e '
             'i contenuti, provati su un indirizzo di prova.', 'pexels-6567864.jpg', 'pen-tool'),
            ('Ogni anno', 'Custodia', 'Ci siamo noi', 'Il dominio a vostro nome, l’hosting, la posta, le copie, gli '
             'aggiornamenti e un referente che risponde.', 'pexels-37605913.jpg', 'shield')]
    return t.classic('timeline', {
        'items': [{'date': d, 'title': ti, 'tag': tag, 'description': de, 'image': K.img(f), 'video': '', 'icon': ic,
                   'category': 'primary'} for d, ti, tag, de, f, ic in voci],
        'tl_layout': 'alt', 'tl_reveal': 'bloom', 'tl_theme': 'night', 'tl_card': 'bubble', 'tl_thread': 'line',
        'tl_node': 'icon', 'tl_color': 'cat', 'tl_media': 'on', 'tl_density': 'comfy', 'tl_transparent': True,
        'tl_title_size': 28, 'tl_title_weight': '300', 'tl_title_family': 'heading', 'tl_show_tag': False,
        'tl_card_bg': C['sala'], 'tl_card_radius': radius(2), 'tl_rail_color': C['filo'], 'tl_fill_from': C['acc'],
        'tl_fill_to': SEGNALE}, style={})


def build_studio():
    t = Tpl(ID, 'lo-studio')
    out = [K.intro(t, 'Lo studio', ['Diciotto persone,', 'un piano a Brera.'],
                   'Strateghi, designer, sviluppatori, chi scrive e chi fotografa: tutti nella stessa stanza, dal 2008.',
                   bg=K.photo_bg('pexels-36831237.jpg'), size=96)]
    out.append(t.section([
        t.one([K.sechead(t, 'Il metodo', ['Quattro tempi,', 'sempre gli stessi.'], size=56,
                         tagline='Le date sono quelle di un marchio nuovo con il suo sito: una pagina sola si fa in un mese.')]),
        t.one([metodo(t)]),
    ], padding='large', width='large', html_id='metodo', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(t.section([
        t.one([K.sechead(t, 'Le persone', ['Chi pensa,', 'chi disegna, chi tiene acceso.'], size=56)]),
        K.squadra(t, [
            ('pexels-7578682.jpg', 'Marta Bellini', 'Direttrice strategia', 'Quindici anni fra ricerca e consulenza. Fa il '
             'primo incontro con ogni cliente.', 'center 25%'),
            ('pexels-36378236.jpg', 'Giulia Neri', 'Direttrice creativa', 'Marchi, caratteri, materiali. Disegna a matita '
             'prima di tutto il resto.', 'center 25%'),
            ('pexels-30743729.jpg', 'Tommaso Ruggeri', 'Direttore tecnico', 'Piattaforme e infrastruttura. È lui che '
             'risponde alle quattro del mattino.', 'center 25%'),
            ('pexels-11395984.jpg', 'Davide Conti', 'Referente clienti', 'Tiene il calendario, i rinnovi e le promesse. '
             'Un nome, un numero.', 'center 25%')], ratio='4/5', size='300', sfondo=C['sala']),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_MODA, 'Sul set', 'Le immagini <em>le facciamo noi.</em>',
                             'Niente repertorio: giriamo nei vostri luoghi, con le vostre persone, con la luce che avete.',
                             overlay=40))
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('select', 'Di che cosa si tratta', 'cosa', True,
                     opzioni='Un progetto nuovo\nCustodia tecnica di un sito che avete\nLavorare con noi'),
             K.campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1', segnaposto='Raccontateci tutto.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scriveteci', ['Un incontro,', 'a Brera o da voi.'],
                                'Per un progetto nuovo il preventivo si compone prima online; qui per tutto il resto.', VOCI,
                                campi, 'Insegna — contatto', 'Invia', 'Ricevuto. Vi rispondiamo entro un giorno lavorativo.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['A Brera,', 'al terzo piano.'], size=52, layout='stack', size_mobile=34),
                      K.specs(t, [('Indirizzo', 'Via Solferino 40, Milano'), ('Orari', 'lunedì — venerdì 9 — 19'),
                                  ('Custodia tecnica', 'sette giorni su sette'), ('Metro', 'Moscova, a 200 m')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Insegna · lo studio', scuro=True, hex_marker=HEX['accent'])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], bg=solid(C['sala']), padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · faccia mancante', color=SEGNALE, align='center'),
                       K.headline(t, 'Questa pagina <em>non c’è.</em>', size=84, tag='h1', align='center',
                                  pad_bottom=20, color=C['chiaro'], accento=C['acc']),
                       K.text(t, 'Il cubo ha quattro facce, questa non è una di loro. Torniamo all’ingresso.', size=17,
                              align='center', color=mix(C['chiaro'], 84)),
                       K.button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_INCHIOSTRO, overlay=40), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Studi e professioni', 'Il cubo dei mestieri',
    ['agenzia', 'branding', 'strategia', 'identità', 'sito', 'dominio', 'hosting', 'preventivo', 'scuro', 'slider pro', '3d'],
    'Un’agenzia strategica di marca, nero, avorio e un rosso segnale: in apertura il cubo dei mestieri, che gira insieme '
    'alla pagina e mostra strategia, identità, piattaforme e custodia tecnica, ognuno col suo video e il suo collegamento; '
    'i clienti che scorrono, i lavori che salgono piegati da sotto lo schermo, il dominio a nome del cliente e il preventivo '
    'a macroblocchi col canone di ogni anno a parte.',
    [('Lavori', LAVORI), ('Dominio e hosting', TECNICA), ('Lo studio', STUDIO), ('Preventivo', PREVENTIVO)],
    [('home', 'Home', None), ('lavori', 'Lavori', 'lavori'), ('dominio-e-hosting', 'Dominio e hosting', 'dominio-e-hosting'),
     ('lo-studio', 'Lo studio', 'lo-studio'), ('preventivo', 'Preventivo', 'preventivo')],
    HEX, '\'Instrument Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Noto Serif Display\', \'Georgia\', serif', '\'IBM Plex Mono\', ui-monospace, Menlo, monospace',
    ['Noto Serif Display', 'Instrument Sans', 'IBM Plex Mono'], corsivo_di=['Noto Serif Display'],
    peso_titoli='300', raggio=2, grana=3, titoli_line='1.02', titoli_spazio='-0.015em')
THEME['preview_v'] = THEME['preview_mobile_v'] = '2'  # tema rifatto da agenzia strategica il 31 ottobre 2026
THEME['cursor']['style'] = 'aggancio'  # cursore: l’anello abbraccia i pulsanti (31 ott 2026)


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'lavori.json': build_lavori(), 'dominio-e-hosting.json': build_tecnica(), 'lo-studio.json': build_studio(),
                        'preventivo.json': build_preventivo(), '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
