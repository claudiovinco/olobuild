# -*- coding: utf-8 -*-
"""Genera il tema «Profondo» (id profondo): una scuola di apnea a Santa Teresa Gallura, in Sardegna, con la barca, la
cima di allenamento a 40 metri e i corsi da aprile a ottobre. Scuro come il blu sotto i trenta metri.

Blu abissale, l'arancio della boa di segnalazione, il ciano della luce che filtra dall'alto. Titoli in Sora, testo in
Manrope, profondità e tempi in JetBrains Mono.

Apertura: Slider Pro che scende scorrendo la pagina (scrollTimeline): cinque profondità, da 0 a −60 metri, la luce che
si spegne, la profondità scritta enorme in trasparenza e, sul lato, un profondimetro (livelli globali: la scala; per
diapositiva: il segno che scende e la pressione). Poi il manifesto del respiro che si accende scorrendo (Scrub text),
il percorso di un'allieva (Progress tracker), la settimana da comporre col prezzo (Configuratore), gli istruttori
(Team), i corsi in fila (Work list) e le uscite in barca secondo i giorni liberi (Availability).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'profondo'
NAME = 'Profondo'

HEX = {
    'primary': '#FF7A45', 'primary_contrast': '#03111A', 'secondary': '#0E4A63', 'secondary_contrast': '#F0FAFC',
    'accent': '#7FD8F0', 'dark': '#010A10', 'light': '#F0FAFC', 'muted': '#061D2B', 'muted_contrast': '#E4F2F7',
    'text': '#E4F2F7', 'text_muted': '#8FB0BE', 'background': '#03111A', 'border': '#12303F', 'link': '#FF7A45',
}

HOME, CORSI, USCITE, CENTRO = '{{PAGINA:home}}', '{{PAGINA:i-corsi}}', '{{PAGINA:le-uscite}}', '{{PAGINA:il-centro}}'
MAIL, PHONE, PHONE_TEL = 'respira@profondo.example', '+39 0789 000 040', 'tel:+390789000040'
LAT, LNG = '41.2420', '9.1880'

V_CIMA, V_GROTTA, V_PARETE, V_RAGGI, V_BUIO, V_ROCCE = ('pexels-video-17737924', 'pexels-video-17809337',
                                                        'pexels-video-17809340', 'pexels-video-2632737',
                                                        'pexels-video-32048613', 'pexels-video-40432354')
PHOTOS = [
    'pexels-5106323.jpg', 'pexels-11348765.jpg', 'pexels-4961417.jpg', 'pexels-5008216.jpg', 'pexels-5560869.jpg',
    'pexels-38069747.jpg', 'pexels-31493484.jpg', 'pexels-5008217.jpg', 'pexels-932638.jpg', 'pexels-5007946.jpg',
    'pexels-6406224.jpg', 'pexels-35974038.jpg', 'pexels-4700871.jpg', 'pexels-15591206.jpg', 'pexels-31740996.jpg',
    'pexels-11258455.jpg', 'pexels-4628217.jpg', 'pexels-4628215.jpg', 'pexels-4628357.jpg', 'pexels-4961416.jpg',
    'pexels-4591927.jpg', 'pexels-4621619.jpg', 'pexels-4621616.jpg', 'pexels-5008229.jpg', 'pexels-39001598.jpg',
    'pexels-39726966.jpg', 'pexels-33543386.jpg', 'pexels-5349757.jpg', 'pexels-5901261.jpg', 'pexels-4621381.jpg',
    'pexels-5440333.jpg', 'pexels-31493479.jpg', 'pexels-32293259.jpg', 'pexels-33992333.jpg', 'pexels-33157670.jpg',
    'pexels-37331181.jpg', 'pexels-19863229.jpg', 'pexels-13444408.jpg', 'pexels-33085682.jpg', 'pexels-35974035.jpg',
    'pexels-35974031.jpg',
]
VIDEOS = [V_CIMA, V_GROTTA, V_PARETE, V_RAGGI, V_BUIO, V_ROCCE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=14, peso_titoli='600', corsivo=False, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Porto di Santa Teresa Gallura, pontile 3 · Sardegna', CENTRO + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'profondo', logo_size='22')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'profondo', 'Scuola di apnea a Santa Teresa Gallura dal 2014. Corsi da aprile a ottobre, la barca al '
                   'pontile 3, la cima di allenamento a quaranta metri davanti a Capo Testa.',
                   [('I corsi', CORSI), ('Le uscite', USCITE), ('Il centro', CENTRO), ('Come arrivare', CENTRO + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'La regola', 'Mai da soli. In apnea si scende sempre in coppia: uno giù, uno su a guardare. Lo '
                   'impari il primo giorno e non lo dimentichi più.', VOCI,
                   '© 2026 Profondo ASD · Santa Teresa Gallura · centro affiliato', [('instagram', 'https://instagram.com'),
                                                                                    ('youtube', 'https://youtube.com')],
                   marchio_size=150)


def apertura(t):
    m = K.mob
    quote = [
        ('0', '1', 'In superficie', 'Respira.\nPoi scendi.', 'Due minuti di respiro lento sulla boa, l’ultima '
         'inspirazione profonda, la capriola. Il resto lo fa il mare.', K.sfondo_foto('pexels-5440333.jpg', overlay=0.52, kb=False), 0),
        ('−10', '2', 'La luce', 'Il mare\nti prende.', 'A dieci metri il corpo smette di galleggiare: da qui in giù non '
         'nuoti più, scivoli.', K.sfondo_foto('pexels-5106323.jpg', overlay=0.48, kb=False), 1),
        ('−25', '3,5', 'La cima', 'Segui\nla corda.', 'La cima di allenamento scende a quaranta metri davanti a Capo '
         'Testa. Ogni metro è segnato, ogni discesa è tua.', K.sfondo_video(V_CIMA, overlay=0.5), 2),
        ('−40', '5', 'Il blu', 'Il cuore\nrallenta.', 'Il battito scende sotto i quaranta colpi al minuto. Non è sforzo: è '
         'quiete, la più profonda che conoscerai.', K.sfondo_foto('pexels-38069747.jpg', overlay=0.55, kb=False), 3),
        ('−60', '7', 'Il silenzio', 'Poi si torna\na guardare il sole.', 'Il tuo compagno ti aspetta a metà strada. In '
         'superficie, il primo respiro è il più bello della giornata.', K.sfondo_foto('pexels-15591206.jpg', overlay=0.6, kb=False), 4),
    ]
    sl = []
    for i, (metri, bar, occ, tit, testo, sfondo, k) in enumerate(quote):
        n = i + 1
        sl.append(K.diapositiva('pr-%d' % n, sfondo, [
            K.livello('pr-q%d' % n, contenuto=metri, x=34, y=18, w=56, size=300, peso='700', colore=mix(C['chiaro'], 14),
                      align='right', entra='fadeIn', ritardo=0, durata=900, lineHeight=0.85, letterSpacing=-12,
                      fontFamily='Sora', responsive={'mobile': {'fontSize': m(130), 'x': 6, 'y': 62, 'width': 88}}),
            K.livello('pr-o%d' % n, contenuto='%s m · %s' % (metri, occ), x=6, y=29, w='auto', size=13, peso='500',
                      colore=C['acc2'], ritardo=100, letterSpacing=3, textTransform='uppercase', fontFamily='JetBrains Mono',
                      bgColor=mix(C['nero'], 62), padding=8, borderRadius=6,
                      responsive={'mobile': {'fontSize': m(11), 'y': 13, 'width': 88}}),
            K.livello('pr-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=35, w=56, size=84, peso='600',
                      colore=C['chiaro'], ritardo=200, durata=900, lineHeight=1.0, letterSpacing=-2,
                      mask={'top': 0, 'right': 0, 'bottom': 100, 'left': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 24, 'color': mix(C['nero'], 55)},
                      responsive={'mobile': {'fontSize': m(40), 'y': 17, 'width': 88}}),
            K.livello('pr-s%d' % n, contenuto=testo, x=6, y=64, w=34, size=18, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=500, lineHeight=1.55, responsive={'mobile': {'fontSize': m(15), 'y': 35, 'width': 88}}),
            K.livello('pr-m%d' % n, tipo='shape', x=93.2, y=19 + 15 * k, w=1.6, h=0.5, bgColor=C['acc'], entra='fadeIn',
                      ritardo=150, durata=500, borderRadius=4, responsive={'mobile': {'visible': False}}),
            K.livello('pr-p%d' % n, contenuto='%s m<br>%s bar' % (metri, bar), x=84, y=17.6 + 15 * k, w=8.6, size=11,
                      peso='500', colore=C['acc'], align='right', entra='fadeIn', ritardo=200, lineHeight=1.4,
                      fontFamily='JetBrains Mono', responsive={'mobile': {'visible': False}}),
        ], durata=6000))
    globali = [
        K.livello('pr-scala', tipo='shape', x=94.9, y=18, w=0.12, h=62, bgColor=mix(C['chiaro'], 35), entra='fadeIn',
                  ritardo=0, durata=900, responsive={'mobile': {'visible': False}}),
        K.livello('pr-giu', contenuto='Scorri per scendere ↓', x=6, y=90, w=40, size=12, peso='500',
                  colore=mix(C['chiaro'], 75), entra='fadeIn', ritardo=800, letterSpacing=2, textTransform='uppercase',
                  fontFamily='JetBrains Mono', animLoop='float', animLoopDuration=2600,
                  responsive={'mobile': {'fontSize': m(10), 'y': 92, 'width': 88}}),
    ]
    return K.proslider(t, sl, globali=globali, transizione='crossFade', durata_trans=900, autoplay=False, showArrows=False,
                       showDots=True, dotStyle='dash', showProgressBar=False, scrollTimeline=True,
                       scrollTimelineDistance=3000, parallax=False, height={'mode': 'vh', 'value': 100}, sizingMode='auto')


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    respiro = t.classic('scrubtext', {
        'text': 'In apnea non si trattiene il respiro: <em>si lascia andare</em> tutto il resto.',
        'show_lead': True, 'lead': 'Il primo giorno non si scende: si impara a respirare. È la cosa più difficile del corso, '
                                   'e quella che ti porti a casa anche se non tornerai mai in mare.',
        'scroll_reveal': True, 'dim_opacity': 18, 'accent': C['acc'], 'text_color': C['testo'], 'lead_color': C['tenue'],
        'size_min': 32, 'size_max': 64, 'max_width_ch': 22, 'lead_size': 18, 'text_font_weight': '600', 'text_align': 'center',
    }, style={})
    out.append(t.section([t.one([respiro])], padding='large', style={'padding_top': 170, 'padding_bottom': 150}))
    percorso = t.classic('progresstracker', {
        'items': [{'title': ti, 'description': de, 'icon': ic, 'status': st} for ti, de, ic, st in [
            ('Prova in piscina', 'Aprile · il respiro, la capriola, i primi otto metri.', 'waves', 'completed'),
            ('Apnea 1 · 10 metri', 'Maggio · tre giorni al mare, la compensazione Frenzel.', 'anchor', 'completed'),
            ('Apnea 2 · 20 metri', 'Luglio · la cima, il salvataggio del compagno.', 'arrow-down', 'active'),
            ('Apnea 3 · 30 metri', 'Settembre · il blu, la caduta libera.', 'circle-dot', 'pending'),
            ('Istruttrice', 'Il sogno di Marta: insegnare qui, dall’anno prossimo.', 'star', 'pending')]],
        'layout': 'vertical', 'connector_style': 'line', 'connector_color': mix(C['testo'], 20), 'completed_color': C['acc2'],
        'active_color': C['acc'], 'pending_color': mix(C['testo'], 30), 'text_color': C['testo'], 'show_description': True,
        'show_numbers': False,
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il percorso di Marta', ['Da zero', 'a venti metri.'], size=60, layout='stack',
                                size_mobile=38),
                      K.text(t, 'Marta è arrivata ad aprile senza saper compensare. Oggi scende a venti metri lungo la cima, e '
                                'a settembre prova i trenta. Ogni corso è un passo: nessuno ti fa saltare quello prima.'),
                      K.button(t, 'I corsi', CORSI, pad_top=18)], wm='2-5', ws='1-1'),
               t.col([percorso], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, V_GROTTA, '−12 m · la grotta', 'Dove la luce entra dall’alto.', 'La grotta dell’Ago è a '
                          'dieci minuti di barca: si entra in apnea da un arco a dodici metri e si esce nel sole. Solo dal '
                          'corso Apnea 2.', 'Le uscite', USCITE, overlay=50, video=True))
    settimana = t.classic('configuratore', {
        'gruppi': [{'id': 'corso', 'titolo': 'Il corso', 'tipo': 'uno', 'obbligatorio': True,
                    'aiuto': 'Ogni corso comprende la teoria, la piscina o il mare calmo e il brevetto. Se non sai da dove '
                             'partire, comincia dalla prova.'},
                   {'id': 'muta', 'titolo': 'L’attrezzatura', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': [
            {'id': 'prova', 'gruppo': 'corso', 'nome': 'Una giornata di prova', 'descrizione': 'Respiro, capriola, i primi '
             'metri lungo la cima. In mare calmo.', 'prezzo': 90, 'immagine': '', 'badge': '', 'predefinita': False,
             'punti': '', 'min': 0, 'max': 1},
            {'id': 'a1', 'gruppo': 'corso', 'nome': 'Apnea 1 · 10 metri', 'descrizione': 'Tre giorni, il brevetto '
             'internazionale.', 'prezzo': 290, 'immagine': '', 'badge': 'Il più scelto', 'predefinita': True, 'punti': '',
             'min': 0, 'max': 1},
            {'id': 'a2', 'gruppo': 'corso', 'nome': 'Apnea 2 · 20 metri', 'descrizione': 'Quattro giorni, la grotta '
             'dell’Ago compresa.', 'prezzo': 390, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '',
             'min': 0, 'max': 1},
            {'id': 'mia', 'gruppo': 'muta', 'nome': 'Ho la mia', 'descrizione': 'Muta, pinne lunghe, maschera a basso '
             'volume.', 'prezzo': 0, 'immagine': '', 'badge': '', 'predefinita': True, 'punti': '', 'min': 0, 'max': 1},
            {'id': 'noleggio', 'gruppo': 'muta', 'nome': 'Noleggio', 'descrizione': 'Tutto quello che serve, della tua '
             'taglia, per tutto il corso.', 'prezzo': 40, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '',
             'min': 0, 'max': 1},
            {'id': 'foto', 'gruppo': 'extra', 'nome': 'Le tue foto sott’acqua', 'descrizione': 'Venti scatti di Giulia, '
             'dalla barca e in profondità.', 'prezzo': 50, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '',
             'min': 0, 'max': 1},
            {'id': 'tramonto', 'gruppo': 'extra', 'nome': 'Uscita al tramonto', 'descrizione': 'Due ore in barca a Capo '
             'Testa, con l’aperitivo a bordo.', 'prezzo': 35, 'immagine': '', 'badge': '', 'predefinita': False,
             'punti': '', 'min': 0, 'max': 1},
            {'id': 'yoga', 'gruppo': 'extra', 'nome': 'Yoga del respiro', 'descrizione': 'Ogni mattina alle 7, sulla '
             'spiaggia di Rena Bianca.', 'prezzo': 30, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '',
             'min': 0, 'max': 1}],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'Totale', 'nota_prezzo': 'Assicurazione e brevetto compresi',
        'periodo': '', 'riepilogo': True, 'titolo_riepilogo': 'La tua settimana', 'testo_azzera': 'Ricomincia',
        'testo_pulsante': 'Chiedi le date', 'link_azione': CENTRO + '?settimana={configurazione}#scrivici',
        'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': C['sala'], 'colore_scelta': C['acc'],
        'colore_testo': C['testo'], 'colore_tenue': C['tenue'], 'colore_prezzo': C['acc'], 'sfondo_riepilogo': C['sala'],
        'sfondo_badge': C['acc2'], 'colore_badge': C['on_acc'], 'raggio_scheda': radius(14), 'gruppo_color': C['testo'],
        'gruppo_font_weight': '600', 'totale_color': C['acc'], 'totale_font_weight': '700',
    }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'La tua settimana', ['Componila,', 'poi tuffati.'], size=64,
                         tagline='Scegli il corso, l’attrezzatura e cosa aggiungere: il totale è quello che pagherai, '
                                 'senza sorprese al pontile.')]),
        t.one([settimana]),
    ], padding='large', width='large', html_id='settimana', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.video_frase(t, V_RAGGI, 'Il primo giorno', 'Nessuno ti chiede <em>quanto scendi.</em>',
                             'Ti chiediamo come ti senti. Il resto arriva, metro dopo metro, col tempo che serve a te.',
                             overlay=45, extra=[K.button(t, 'Prenota la prova', CENTRO + '#scrivici', kind='pieno',
                                                         align='center', pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='profondo',
                page_intro_caption='inspira · scendi', page_intro_bg=HEX['dark'], page_intro_color=HEX['accent'],
                page_intro_duration=1800, page_intro_once=True)


def build_corsi():
    t = Tpl(ID, 'i-corsi')
    out = [K.intro(t, 'I corsi', ['Un metro', 'alla volta.'],
                   'Quattro livelli, da aprile a ottobre, con al massimo quattro allievi per istruttore. Teoria al centro, '
                   'pratica in mare calmo dentro la baia, poi la cima al largo.', bg=K.video_bg(V_PARETE, overlay=55),
                   overlay_text=True)]
    lista = t.classic('worklist', {
        'items': [{'number': nu, 'title': ti, 'category': ca, 'year': pr, 'link_url': CENTRO + '#scrivici'} for nu, ti, ca, pr in [
            ('1 GG', 'Giornata di prova', 'Respiro e primi metri · da 12 anni', '90 €'),
            ('LIV 1', 'Apnea 1 · fino a 10 metri', 'Tre giorni · piscina e mare', '290 €'),
            ('LIV 2', 'Apnea 2 · fino a 20 metri', 'Quattro giorni · la cima e la grotta', '390 €'),
            ('LIV 3', 'Apnea 3 · fino a 30 metri', 'Cinque giorni · la caduta libera', '490 €'),
            ('PRO', 'Istruttore', 'Venti giorni · da settembre', '1.400 €')]],
        'divider_color': C['filo'], 'row_padding_y': 28, 'row_hover_bg': mix(C['acc'], 10), 'hover_indent': 28,
        'number_color': C['acc'], 'number_size': 14, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': 34, 'title_weight': '600', 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': C['acc2'], 'year_size': 16, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': 'JetBrains Mono',
    }, style={})
    out.append(t.section([t.one([lista])], padding='large', width='large', style={'padding_top': 70, 'padding_bottom': 90}))

    def istruttore(foto, nome, ruolo, bio, pos='center center'):
        return t.classic('team', {
            'photo': K.img(foto), 'name': nome, 'role': ruolo, 'bio': bio, 'link_text': '', 'link_url': '',
            'photo_size': '150', 'photo_ratio': '1/1', 'photo_fit': 'cover', 'photo_object_position': pos,
            'photo_shape': 'circle', 'photo_border_width': '3', 'photo_border_color': C['acc'], 'photo_shadow': 'none',
            'info_bg_color': C['sala'], 'info_text_color': C['testo'], 'info_radius': '14', 'info_align': 'center',
            'role_color': C['acc'], 'name_weight': '600', 'bg_color': 'transparent',
        }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'Gli istruttori', ['Tre persone,', 'un’unica regola.'], size=60,
                         tagline='Mai da soli: ogni discesa ha qualcuno che guarda dall’alto. Sono loro.')]),
        t.row([t.col([istruttore('pexels-5901261.jpg', 'Giulia Deiana', 'Fondatrice · −62 m in assetto costante',
                                 'Sedici anni di apnea, campionessa italiana nel 2019. Insegna il respiro come nessuno.')],
                     wm='1-3', ws='1-1'),
               t.col([istruttore('pexels-31493479.jpg', 'Andrea Columbu', 'Istruttore · sicurezza',
                                 'Ex sommozzatore dei vigili del fuoco. È lui che ti aspetta a metà cima.')], wm='1-3', ws='1-1'),
               t.col([istruttore('pexels-4621381.jpg', 'Sara Piras', 'Istruttrice · ragazzi',
                                 'Insegna ai più piccoli in piscina d’inverno e in baia d’estate. Fa anche le foto.')],
                     wm='1-3', ws='1-1')], layout='33-33-33', gap=24, valign='stretch'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.domande(t, 'Prima', 'del primo tuffo.', [
        ('Devo saper nuotare bene?', 'Devi stare a tuo agio in acqua alta. Non serve essere atleti: serve essere tranquilli.'),
        ('Mi fanno male le orecchie quando scendo.', 'Succede a tutti all’inizio. La compensazione si impara: il primo '
                                                    'giorno è dedicato a quella.'),
        ('Serve il certificato medico?', 'Sì, quello sportivo non agonistico. Se non ce l’hai, ti diciamo dove farlo a '
                                         'Santa Teresa.'),
        ('E se ho paura?', 'Bene: vuol dire che rispetti il mare. Si scende solo quando te la senti, mai un metro prima.'),
    ], 'co', bg=False))
    return out


def build_uscite():
    t = Tpl(ID, 'le-uscite')
    out = [K.intro(t, 'Le uscite', ['La barca parte', 'alle otto e mezza.'],
                   'Ogni mattina da aprile a ottobre, col mare permettendo. Quattro ore fra Capo Testa, la grotta dell’Ago e '
                   'la secca dei Monaci, la cima calata dove l’acqua è più blu.',
                   bg=K.photo_bg('pexels-33992333.jpg', overlay=55), overlay_text=True)]
    quando = t.classic('availability', {
        'eyebrow': 'Quando sei libero', 'heading': 'Tocca i momenti in cui puoi uscire',
        'intro': 'Ti diciamo quale formula ti conviene: la barca va all’alba, a metà mattina e al tramonto.',
        'days': 'lun, mar, mer, gio, ven, sab, dom', 'bands': 'all’alba, a metà mattina, al tramonto',
        'count_label': 'Uscite a settimana', 'verdict_label': 'La formula giusta',
        'tiers': [{'min': 0, 'label': 'Uscita singola · 65 €', 'text': 'Qualche tuffo in vacanza: prenoti la mattina prima, '
                   'se c’è posto.'},
                  {'min': 3, 'label': 'Carnet 5 uscite · 280 €', 'text': 'Una settimana al mare con la cima tutti i giorni: '
                   'cinque uscite da usare quando vuoi.'},
                  {'min': 7, 'label': 'Stagione · 690 €', 'text': 'Vivi qui o ci passi l’estate: uscite illimitate e '
                   'allenamento col gruppo agonistico.'}],
        'zone_accent': C['acc'], 'zone_on': C['on_acc'], 'cell_bg': C['sala'],
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': C['filo']}, 'align': 'left',
    }, style={})
    out.append(t.section([
        t.row([t.col([quando], wm='3-5', ws='1-1'),
               t.col([K.image(t, 'pexels-37331181.jpg', 'La barca della scuola nella baia', ratio='4/5', drift=True)],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 90, 'padding_bottom': 110}))
    for foto, ey, ti, tx, video in [
        (V_ROCCE, '−15 m · la secca dei Monaci', 'Rocce, cernie, e la luce che danza.', 'La secca sale fino a quindici metri '
         'dalla superficie: il posto per chi vuole guardare più che scendere.', True),
        ('pexels-35974031.jpg', '−12 m · la grotta dell’Ago', 'Un arco, poi il sole.', 'Si entra da sotto, si esce nella luce. '
         'Dieci minuti di barca dal porto, solo col mare calmo.', False),
    ]:
        out.append(K.pannello(t, foto, ey, ti, tx, 'Chiedi un posto', CENTRO + '#scrivici', overlay=52, video=video))
    return out


def build_centro():
    t = Tpl(ID, 'il-centro')
    out = [K.intro(t, 'Il centro', ['Al pontile 3,', 'davanti alle Bocche.'],
                   'Un container blu al porto con le mute appese, l’aula per la teoria e la doccia calda. La barca è '
                   'ormeggiata davanti, la cima a venti minuti.', bg=K.photo_bg('pexels-33157670.jpg', overlay=66),
                   overlay_text=True)]
    campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('date', 'Dal', 'dal', True), K.campo('date', 'Al', 'al', True),
             K.campo('select', 'Cosa vuoi fare', 'cosa', True,
                     opzioni='La giornata di prova\nApnea 1\nApnea 2\nApnea 3\nSolo le uscite in barca'),
             K.campo('textarea', 'La tua settimana', 'settimana', larghezza='1-1', param='settimana',
                     segnaposto='Se l’hai composta nella home la trovi già qui. Altrimenti raccontaci cosa ti piacerebbe.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Scrivici', ['Ti teniamo', 'un posto in barca.'],
                                'Ti rispondiamo entro sera con le date libere e le previsioni del mare.', VOCI, campi,
                                'Profondo — richiesta', 'Chiedi le date', 'Ricevuto. Entro sera ti scriviamo: intanto, respira.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Come arrivare', ['Porto di', 'Santa Teresa.'], size=52, layout='stack', size_mobile=36),
                      K.specs(t, [('Aeroporto', 'Olbia, un’ora di macchina'), ('Traghetto', 'da Bonifacio, cinquanta minuti'),
                                  ('Parcheggio', 'gratis al porto, fino alle 9'), ('Il centro', 'pontile 3, container blu')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Profondo · scuola di apnea', hex_marker=HEX['primary'], zoom='15')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · troppo in fondo', color=C['acc'], align='center'),
                       K.headline(t, 'Qui non c’è <em>più luce.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Risali con calma, senza fretta: la superficie è a un clic.', size=17, align='center',
                              color=C['soft']),
                       K.button(t, 'Torna in superficie', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_BUIO, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Sport e benessere', 'Scorri per scendere',
    ['apnea', 'freediving', 'sardegna', 'mare', 'corsi', 'sport', 'scuro', 'slider pro'],
    'Una scuola di apnea in Sardegna, blu abissale con l’arancio della boa: l’apertura che scende scorrendo da 0 a −60 '
    'metri col profondimetro e la luce che si spegne, il manifesto del respiro che si accende, il percorso di un’allieva '
    'livello per livello, la settimana da comporre col prezzo, gli istruttori e le uscite in barca secondo i giorni liberi.',
    [('I corsi', CORSI), ('Le uscite', USCITE), ('Come arrivare', CENTRO + '#arrivare'), ('Prenota', CENTRO + '#scrivici')],
    [('home', 'Home', None), ('i-corsi', 'I corsi', 'i-corsi'), ('le-uscite', 'Le uscite', 'le-uscite'),
     ('il-centro', 'Il centro', 'il-centro')],
    HEX, '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Sora\', \'Helvetica Neue\', Arial, sans-serif', '\'JetBrains Mono\', ui-monospace, Menlo, monospace',
    ['Sora', 'Manrope', 'JetBrains Mono'], peso_titoli='600', raggio=14, grana=3, titoli_line='1.02',
    titoli_spazio='-0.03em')

THEME['preview_v'] = '2'          # la prima anteprima aveva la fascia vuota dello scorrimento
THEME['preview_mobile_v'] = '2'


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'i-corsi.json': build_corsi(), 'le-uscite.json': build_uscite(), 'il-centro.json': build_centro(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
