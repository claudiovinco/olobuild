# -*- coding: utf-8 -*-
"""Genera il tema «Meridiana» (id meridiana): uno studio di consulenza patrimoniale indipendente sotto i portici di via
Santo Stefano, a Bologna. Consulenti pagati solo dai clienti, a parcella fissa, senza provvigioni sui prodotti. Scuro
come un portico la sera.

Blu ardesia notturno, il crema del sole sulla pietra, il blu cielo delle mattine e l'ottone delle meridiane. Titoli in
Newsreader, testo in Hanken Grotesk, cifre in Fragment Mono.

Apertura: Slider Pro sulle ore di una vita (costruire, proteggere, godersi, passare il testimone), coi video veri della
luce che si sposta sul muro; a destra un quadrante disegnato coi livelli (il cerchio, le ore, lo gnomone che gira piano
come un'ombra al sole); il cambio è una dissolvenza lunga. Poi il risparmio che cresce col cursore (Projector), un
portafoglio prudente a ciambella (Chart), il confronto dei costi con la banca (Table), il metodo e i consulenti.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, mix, radius, solid  # noqa: E402
from kit import Kit, tema  # noqa: E402
import firme  # noqa: E402

ID = 'meridiana'
NAME = 'Meridiana'

HEX = {
    'primary': '#E9D8A6', 'primary_contrast': '#0B1013', 'secondary': '#2E4756', 'secondary_contrast': '#F5F2EA',
    'accent': '#86B3C9', 'dark': '#060A0C', 'light': '#F5F2EA', 'muted': '#131A1F', 'muted_contrast': '#E8EDF0',
    'text': '#E8EDF0', 'text_muted': '#9AA7AF', 'background': '#0B1013', 'border': '#22303A', 'link': '#E9D8A6',
}

HOME, METODO, STUDIO, CONTATTI = '{{PAGINA:home}}', '{{PAGINA:il-metodo}}', '{{PAGINA:lo-studio}}', '{{PAGINA:contatti}}'
MAIL, PHONE, PHONE_TEL = 'studio@meridiana.example', '+39 051 000 1230', 'tel:+390510001230'
LAT, LNG = '44.4910', '11.3480'

V_PERSIANE, V_FINESTRE, V_PASSANTI, V_TASCHINO, V_QUADRANTE = ('pexels-video-5061816', 'pexels-video-5155184',
                                                               'pexels-video-18735298', 'pexels-video-8321892',
                                                               'pexels-video-856199')
PHOTOS = [
    'pexels-35616936.jpg', 'pexels-36286320.jpg', 'pexels-27183730.jpg', 'pexels-36148553.jpg', 'pexels-28540407.jpg',
    'pexels-37985248.jpg', 'pexels-17327727.jpg', 'pexels-20422361.jpg', 'pexels-6998684.jpg', 'pexels-19542056.jpg',
    'pexels-39374832.jpg', 'pexels-13691276.jpg', 'pexels-15046187.jpg', 'pexels-15138264.jpg', 'pexels-12366635.jpg',
    'pexels-7319071.jpg', 'pexels-3847582.jpg', 'pexels-12900435.jpg', 'pexels-14620937.jpg', 'pexels-36093623.jpg',
    'pexels-2319419.jpg', 'pexels-37836856.jpg', 'pexels-8698215.jpg', 'pexels-1095602.jpg', 'pexels-8327539.jpg',
    'pexels-30480339.jpg', 'pexels-8327528.jpg', 'pexels-8730991.jpg', 'pexels-1325746.jpg', 'pexels-11368557.jpg',
    'pexels-11368556.jpg', 'pexels-20992128.jpg', 'pexels-16116476.jpg', 'pexels-12066670.jpg', 'pexels-34529440.jpg',
]
VIDEOS = [V_PERSIANE, V_FINESTRE, V_PASSANTI, V_TASCHINO, V_QUADRANTE]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

K = Kit(ID, MEDIA, raggio=2, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True)
C = K.c
VOCI = [('map-pin', 'Via Santo Stefano 38, sotto il portico · Bologna', CONTATTI + '#arrivare'), ('phone', PHONE, PHONE_TEL),
        ('mail', MAIL, 'mailto:' + MAIL)]


def build_header():
    return K.testata(Tpl(ID, 'header'), 'Meridiana', logo_size='26')


def build_footer():
    t = Tpl(ID, 'footer')
    return K.piede(t, 'Meridiana', 'Consulenza patrimoniale indipendente a Bologna dal 2009. Ci pagate solo voi, a parcella '
                   'fissa: niente provvigioni sui prodotti che vi consigliamo, mai.',
                   [('Il metodo', METODO), ('Lo studio', STUDIO), ('Contatti', CONTATTI), ('Dove siamo', CONTATTI + '#arrivare'),
                    ('Privacy', '/privacy-policy/')],
                   'Iscrizione', 'Consulenti finanziari autonomi iscritti all’Albo OCF, sezione consulenti autonomi. Gli '
                   'esempi del sito sono illustrativi e non sono una raccomandazione.', VOCI,
                   '© 2026 Meridiana consulenza patrimoniale srl · Bologna · P.IVA 00000000000',
                   [('linkedin', 'https://linkedin.com')], marchio_size=140)


def apertura(t):
    m = K.mob
    ore = [
        ('Ore 9 · costruire', 'Il mattino\nsi semina.', 'A trent’anni il tempo è il capitale più grande. Vi aiutiamo a '
         'metterlo a frutto: un fondo di sicurezza, un piano d’accumulo, niente prodotti che non capite.',
         K.sfondo_video(V_FINESTRE, overlay=0.64), 'IX'),
        ('Ore 12 · proteggere', 'A mezzogiorno,\nl’ombra è corta.', 'La casa, i figli, l’azienda. È il momento di '
         'proteggere quello che avete costruito, senza smettere di farlo crescere.',
         K.sfondo_foto('pexels-17327727.jpg', overlay=0.55, scala=1.08), 'XII'),
        ('Ore 16 · godersi', 'Il pomeriggio\nè vostro.', 'La pensione non è un salto nel vuoto: la prepariamo dieci anni '
         'prima, con una rendita che vi lasci fare quello che avete rimandato.', K.sfondo_video(V_PERSIANE, overlay=0.45),
         'IV'),
        ('Ore 19 · passare il testimone', 'Prima del tramonto,\nil testimone.', 'Successioni, donazioni, il patrimonio di '
         'famiglia. Con il notaio e il commercialista, perché passi intero a chi verrà dopo.',
         K.sfondo_video(V_PASSANTI, overlay=0.55), 'VII'),
    ]
    sl = []
    for i, (occ, tit, testo, sfondo, ora) in enumerate(ore):
        n = i + 1
        sl.append(K.diapositiva('me-%d' % n, sfondo, [
            K.livello('me-o%d' % n, contenuto=occ, x=6, y=24, w=44, size=12, peso='500', colore=C['acc'], ritardo=200,
                      letterSpacing=3, textTransform='uppercase', fontFamily='Fragment Mono',
                      responsive={'mobile': {'fontSize': m(11), 'y': 12, 'width': 88}}),
            K.livello('me-t%d' % n, contenuto=tit, tag='h1' if n == 1 else 'h2', x=6, y=29, w=58, size=96, peso='400',
                      colore=C['chiaro'], ritardo=400, durata=1400, lineHeight=1.02, entra='fadeIn',
                      mask={'top': 0, 'right': 100, 'bottom': 0, 'left': 0},
                      textShadow={'x': 0, 'y': 2, 'blur': 26, 'color': mix(C['nero'], 60)},
                      responsive={'mobile': {'fontSize': m(40), 'y': 16, 'width': 88}}),
            K.livello('me-s%d' % n, contenuto=testo, x=6, y=64, w=34, size=17, peso='400', colore=mix(C['chiaro'], 90),
                      ritardo=1200, lineHeight=1.6, responsive={'mobile': {'fontSize': m(15), 'y': 31, 'width': 88}}),
            K.livello('me-b%d' % n, tipo='button', contenuto='Un primo incontro', x=6, y=80, w='auto', size=12, peso='600',
                      colore=C['on_acc'], bgColor=C['acc'], padding=16, letterSpacing=2, textTransform='uppercase',
                      borderRadius=2, ritardo=1500, buttonUrl=CONTATTI + '#scrivici',
                      hover={'bgColor': C['acc2'], 'duration': 250}, responsive={'mobile': {'y': 48, 'fontSize': m(12)}}),
            K.livello('me-h%d' % n, contenuto=ora, x=67, y=21, w=20, size=22, peso='400', colore=C['acc'], align='center',
                      entra='fadeIn', ritardo=900, durata=1200, fontFamily='Newsreader', letterSpacing=4,
                      responsive={'mobile': {'visible': False}}),
        ], durata=7500))
    quadrante = [
        K.livello('me-cerchio', tipo='shape', x=67, y=26, w=20, h=32, bgColor='transparent', borderWidth=1,
                  borderColor=mix(C['acc'], 55), borderRadius=999, entra='fadeIn', ritardo=300, durata=1600,
                  responsive={'mobile': {'visible': False}}),
        K.livello('me-gnomone', tipo='shape', x=76.9, y=29, w=0.2, h=26, bgColor=C['acc'], entra='fadeIn', ritardo=600,
                  durata=1600, animLoop='spin', animLoopDuration=60000, animLoopEasing='linear', opacity=85,
                  responsive={'mobile': {'visible': False}}),
        K.livello('me-perno', tipo='shape', x=76.6, y=41, w=0.8, h=1.3, bgColor=C['acc'], borderRadius=999, entra='fadeIn',
                  ritardo=700, responsive={'mobile': {'visible': False}}),
        K.livello('me-motto', contenuto='Horas non numero nisi serenas', x=62, y=61, w=30, size=13, peso='400',
                  colore=mix(C['chiaro'], 70), align='center', entra='fadeIn', ritardo=1000, fontStyle='italic',
                  fontFamily='Newsreader', letterSpacing=1, responsive={'mobile': {'visible': False}}),
    ]
    return K.proslider(t, sl, globali=quadrante, transizione='fade', durata_trans=1600, showArrows=True, showDots=True,
                       parallax=True, parallaxIntensity=2)


def build_home():
    t = Tpl(ID, 'home')
    out = [K.apertura(t, apertura(t))]
    calcolo = t.classic('projector', {
        'eyebrow': 'Un esempio', 'heading': 'Quanto diventa <em>un anno alla volta</em>?',
        'intro': 'Sposta il cursore su quanto potreste mettere da parte ogni anno.', 'min': '1000', 'max': '30000',
        'step': '500', 'value': '6000', 'rate': '0.03', 'years': '20', 'currency': '€ ', 'input_label': 'Ogni anno',
        'input_unit': '', 'out_caption': 'Fra vent’anni, al 3% netto all’anno', 'result_suffix': '', 'decimals': 0,
        'note': 'Esempio illustrativo con un rendimento costante del 3% netto: i rendimenti reali variano e non sono '
                'garantiti. Non è una raccomandazione.', 'show_contrib': True, 'list_base': 1, 'list_items': [],
        'zone_accent': C['acc'], 'card_bg': C['sala'], 'align': 'left', 'border_radius': 2,
        'tile_padding': {'top': 44, 'right': 44, 'bottom': 44, 'left': 44},
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Il tempo', ['L’interesse', 'più grande.'], size=60, layout='stack', size_mobile=38),
                      K.text(t, 'Non serve partire con molto: serve partire presto, e non fermarsi. Provate il cursore: la '
                                'differenza fra quattromila e seimila euro l’anno, in vent’anni, è una stanza in più.'),
                      K.button(t, 'Il metodo', METODO, pad_top=18)], wm='2-5', ws='1-1'),
               t.col([calcolo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 130}))
    torta = t.classic('chart', {
        'chart_type': 'doughnut',
        'items': [{'label': l, 'value': v, 'color': c} for l, v, c in [
            ('Obbligazioni di qualità', '45', C['acc']), ('Azioni globali', '30', C['acc2']),
            ('Liquidità e conti deposito', '15', mix(C['acc'], 45, C['nero'])), ('Oro', '10', C['sec'])]],
        'chart_height': '380', 'show_legend': True, 'legend_position': 'bottom', 'legend_align': 'center',
        'legend_color': C['testo'], 'legend_font_size': '13', 'show_title': False,
    }, style={})
    out.append(t.section([
        t.row([t.col([torta], wm='1-2', ws='1-1'),
               t.col([K.sechead(t, 'Un portafoglio prudente', ['Poco rumore,', 'molta pazienza.'], size=56, layout='stack',
                                size_mobile=36),
                      K.text(t, 'Un esempio per chi ha dieci anni davanti e non vuole perdere il sonno: strumenti a basso '
                                'costo, diversificati nel mondo, ribilanciati una volta l’anno. Il vostro sarà diverso, '
                                'perché siete diversi.'),
                      K.specs(t, [('Costo medio degli strumenti', 'sotto lo 0,3% l’anno'),
                                  ('Ribilanciamento', 'una volta l’anno, con voi'),
                                  ('Rischio', 'concordato e scritto, prima di cominciare')])], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], bg=solid(C['sala']), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))
    out.append(K.pannello(t, 'pexels-15138264.jpg', 'Sotto i portici', 'Quaranta chilometri di ombra.', 'Bologna ha i '
                          'portici più lunghi del mondo: si cammina all’asciutto da centinaia d’anni perché qualcuno, un '
                          'tempo, ha pensato a chi sarebbe venuto dopo. È il nostro modo di lavorare.', 'Lo studio', STUDIO,
                          overlay=58))
    costi = t.classic('table', {
        'table_data': [['Su 300.000 € investiti', 'Banca tradizionale', 'Meridiana'],
                       ['Costo dei prodotti', '1,8% l’anno', '0,25% l’anno'],
                       ['Consulenza', 'compresa nei prodotti', 'parcella fissa, 3.000 €'],
                       ['Provvigioni', 'sì, nascoste', 'nessuna'],
                       ['In dieci anni, circa', '54.000 €', '37.500 €']],
        'has_header': True, 'striped': True, 'bordered': False, 'hover_effect': True, 'first_col_bold': True,
        'col_alignments': ['left', 'right', 'right'], 'responsive_mode': 'scroll', 'header_bg': C['acc'],
        'header_text_color': C['on_acc'], 'text_color': C['testo'], 'even_row_bg': mix(C['acc'], 6),
    }, style={})
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'I costi', ['Quello che', 'non vi dicono.'], size=56, layout='stack', size_mobile=36),
                      K.text(t, 'Un esempio semplificato, senza contare i rendimenti: la differenza la fanno i costi, anno '
                                'dopo anno. Noi li scriviamo tutti, prima di cominciare.')], wm='2-5', ws='1-1'),
               t.col([costi], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_TASCHINO, 'Il nostro motto', 'Contiamo solo <em>le ore serene.</em>',
                             'È inciso sulla meridiana del cortile, in latino. Vuol dire che il nostro lavoro è togliervi '
                             'pensieri, non aggiungerne.', overlay=55,
                             extra=[K.button(t, 'Un primo incontro', CONTATTI + '#scrivici', kind='pieno', align='center',
                                             pad_top=18)]))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Meridiana',
                page_intro_caption='horas non numero nisi serenas', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


def build_metodo():
    t = Tpl(ID, 'il-metodo')
    out = [K.intro(t, 'Il metodo', ['Prima la vita,', 'poi i numeri.'],
                   'Non partiamo dai prodotti: partiamo da quello che volete fare, fra cinque, dieci, trent’anni. Poi '
                   'scriviamo il piano, e lo rivediamo insieme ogni anno.', bg=K.video_bg(V_QUADRANTE, overlay=65),
                   overlay_text=True)]
    out.append(t.section([
        t.row([t.col([K.label(t, '0%d' % (k + 1), color=C['acc']), K.headline(t, ti, size=34, tag='h3', pad_bottom=10),
                      K.text(t, tx)], wm='1-4', ws='1-2', width_default='1-2') for k, (ti, tx) in enumerate([
            ('Ascoltare', 'Due ore senza numeri: la famiglia, il lavoro, le paure, i progetti.'),
            ('Fotografare', 'Tutto quello che avete, dove sta e quanto vi costa. Spesso è la sorpresa più grande.'),
            ('Scrivere il piano', 'Obiettivi, tempi, rischio accettato. Su carta, firmato da noi due.'),
            ('Rivedere', 'Una volta l’anno, o quando cambia la vita. Il piano cambia con voi.')])],
              layout='25-25-25-25', gap=36, valign='start'),
    ], padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))
    for k, (aid, foto, nome, chi, testo, dati) in enumerate([
        ('famiglie', 'pexels-11368556.jpg', 'Per le famiglie', 'Risparmio, casa, figli', 'Il fondo per le emergenze, '
         'l’università dei figli, il mutuo da estinguere o no. Un piano che si legge in una pagina.',
         [('Parcella', 'da 1.200 € l’anno'), ('Incontri', 'due l’anno, più quando serve'), ('Durata', 'fin che volete')]),
        ('imprenditori', 'pexels-14620937.jpg', 'Per gli imprenditori', 'Azienda e patrimonio', 'Separare quello che è '
         'dell’azienda da quello che è della famiglia, il TFR, il passaggio generazionale. Col vostro commercialista.',
         [('Parcella', 'da 3.000 € l’anno'), ('Con chi', 'commercialista e notaio'), ('Durata', 'pluriennale')]),
        ('successioni', 'pexels-8730991.jpg', 'Successioni', 'Il testimone', 'Testamento, donazioni, polizze: perché il '
         'patrimonio arrivi intero, e senza litigi, a chi volete voi.',
         [('Parcella', 'a progetto, da 2.000 €'), ('Con chi', 'il vostro notaio'), ('Tempi', 'tre mesi')]),
    ]):
        out.append(t.section([
            t.row([t.col([K.image(t, foto, nome, ratio='4/5')], wm='2-5', ws='1-1'),
                   t.col([K.label(t, chi, color=C['acc2']), K.headline(t, nome, size=56, tag='h2', pad_bottom=14),
                          K.text(t, testo), K.specs(t, dati)], wm='3-5', ws='1-1')],
                  layout='custom', custom='40,60', gap=64, valign='center'),
        ], padding='large', html_id=aid, bg=solid(C['sala']) if k % 2 == 0 else None,
            style={'padding_top': 100, 'padding_bottom': 100}))
    out.append(K.domande(t, 'Prima', 'del primo incontro.', [
        ('Quanto costa il primo incontro?', 'Niente. Un’ora e mezza per conoscerci: se non c’è niente da migliorare, ve lo '
                                           'diciamo.'),
        ('Devo spostare i miei soldi?', 'No. Restano dove sono, intestati a voi: noi consigliamo, non custodiamo.'),
        ('Da che cifra lavorate?', 'Non c’è un minimo. Conta il piano, non il conto.'),
        ('Come guadagnate?', 'Solo dalla parcella che ci pagate voi. Nessuna provvigione, da nessuno.'),
    ], 'me', bg=False))
    return out


def build_studio():
    t = Tpl(ID, 'lo-studio')
    out = [K.intro(t, 'Lo studio', ['Un primo piano', 'sotto il portico.'],
                   'Quattro stanze affacciate sul cortile di Santo Stefano, con la meridiana del Settecento sul muro di '
                   'fronte. Ci lavoriamo in cinque: tre consulenti, un’analista, una segretaria che sa tutto.',
                   bg=K.photo_bg('pexels-12900435.jpg', overlay=60), overlay_text=True)]

    def persona(foto, nome, ruolo, bio):
        return t.classic('team', {
            'photo': K.img(foto), 'name': nome, 'role': ruolo, 'bio': bio, 'link_text': '', 'link_url': '',
            'photo_size': '150', 'photo_ratio': '1/1', 'photo_fit': 'cover', 'photo_shape': 'circle',
            'photo_border_width': '1', 'photo_border_color': C['acc'], 'photo_shadow': 'none', 'info_bg_color': C['sala'],
            'info_text_color': C['testo'], 'info_radius': '2', 'info_align': 'center', 'role_color': C['acc'],
            'name_weight': '400', 'bg_color': 'transparent',
        }, style={})
    out.append(t.section([
        t.one([K.sechead(t, 'I consulenti', ['Tre persone,', 'un telefono che risponde.'], size=60)]),
        t.row([t.col([persona('pexels-12066670.jpg', 'Andrea Malaguti', 'Fondatore · consulente autonomo',
                              'Vent’anni in banca, poi la scelta di stare dall’altra parte del tavolo.')], wm='1-3', ws='1-1'),
               t.col([persona('pexels-34529440.jpg', 'Chiara Bergonzoni', 'Consulente · successioni',
                              'Avvocata di formazione, lavora coi notai sui passaggi di famiglia.')], wm='1-3', ws='1-1'),
               t.col([persona('pexels-20992128.jpg', 'Luca Fontana', 'Consulente · imprenditori',
                              'Commercialista per dieci anni, parla la lingua dei bilanci.')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=24, valign='stretch'),
    ], bg=solid(C['sala']), padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([K.image(t, 'pexels-36286320.jpg', 'La meridiana di pietra con l’ombra dello gnomone', ratio='4/5',
                              drift=True)], wm='2-5', ws='1-1'),
               t.col([K.label(t, 'Il nome', color=C['acc2']),
                      K.headline(t, 'Una meridiana <em>non ha fretta.</em>', size=56, tag='h2', pad_bottom=14),
                      K.text(t, 'Segna l’ora solo quando c’è il sole, e lo fa da trecento anni senza sbagliare. Ci è sembrata '
                                'la descrizione giusta di un buon piano: semplice, paziente, fatto per durare.'),
                      K.specs(t, [('Fondato', '2009'), ('Famiglie seguite', '180'), ('Patrimoni consigliati', '320 milioni'),
                                  ('Albo', 'OCF, consulenti autonomi')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='center'),
    ], padding='large', style={'padding_top': 120, 'padding_bottom': 120}))
    out.append(K.video_frase(t, V_FINESTRE, 'Il cortile', 'La luce <em>gira piano.</em>',
                             'D’inverno entra in studio alle dieci e se ne va alle tre. Abbiamo imparato a fare le riunioni '
                             'importanti quando c’è.', overlay=70))
    return out


def build_contatti():
    t = Tpl(ID, 'contatti')
    out = [K.intro(t, 'Contatti', ['Un’ora e mezza,', 'un caffè.'],
                   'Il primo incontro è gratuito e senza impegno, in studio o in videochiamata. Portate solo le domande: '
                   'i documenti servono dopo.', bg=K.photo_bg('pexels-13691276.jpg', overlay=60), overlay_text=True)]
    campi = [K.campo('text', 'Nome e cognome', 'nome', True), K.campo('email', 'Email', 'email', True),
             K.campo('tel', 'Telefono', 'telefono'),
             K.campo('select', 'Cosa vi serve', 'cosa', True,
                     opzioni='Un piano per la famiglia\nPatrimonio e azienda\nPensione\nSuccessione\nUna seconda opinione'),
             K.campo('select', 'Come preferite', 'come', opzioni='In studio a Bologna\nIn videochiamata\nA casa vostra'),
             K.campo('textarea', 'Raccontateci', 'messaggio', larghezza='1-1', segnaposto='Solo quello che volete: ne parliamo a voce.')]
    out.append(K.sezione_modulo(t, 'scrivici', 'Il primo incontro', ['Senza impegno,', 'senza fretta.'],
                                'Vi rispondiamo entro un giorno lavorativo con due o tre date possibili.', VOCI, campi,
                                'Meridiana — primo incontro', 'Chiedi l’incontro',
                                'Ricevuto. Vi scriviamo entro domani con le date.'))
    out.append(t.section([
        t.row([t.col([K.sechead(t, 'Dove siamo', ['Santo Stefano,', 'sotto il portico.'], size=52, layout='stack',
                                size_mobile=36),
                      K.specs(t, [('Indirizzo', 'via Santo Stefano 38, Bologna'), ('Orari', 'lunedì — venerdì, 9 — 18'),
                                  ('A piedi', 'cinque minuti dalle Due Torri'), ('Parcheggio', 'Staveco, poi dieci minuti')])],
                     wm='1-2', ws='1-1'),
               t.col([K.mappa(t, LAT, LNG, 'Meridiana · consulenza patrimoniale', hex_marker=HEX['primary'], zoom='16')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='center'),
    ], padding='large', html_id='arrivare', style={'padding_top': 120, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        K.centered(t, [K.label(t, 'Errore 404 · ora che non c’è', color=C['acc'], align='center'),
                       K.headline(t, 'Qui il sole <em>non arriva.</em>', size=96, tag='h1', align='center', pad_bottom=20),
                       K.text(t, 'Una meridiana all’ombra non segna niente: torniamo alla luce.', size=17, align='center',
                              color=C['soft']),
                       K.button(t, 'Torna all’inizio', HOME, kind='pieno', align='center')]),
    ], bg=K.video_bg(V_PERSIANE, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = tema(
    ID, NAME, 'Studi e professioni', 'Le ore serene',
    ['consulente finanziario', 'patrimonio', 'bologna', 'pianificazione', 'successioni', 'indipendente', 'scuro',
     'slider pro'],
    'Uno studio di consulenza patrimoniale indipendente sotto i portici di Bologna, ardesia e luce sulla pietra: '
    'l’apertura sulle ore di una vita col quadrante e lo gnomone che gira come un’ombra, il risparmio che cresce col '
    'cursore, un portafoglio prudente a ciambella, i costi a confronto con la banca, il metodo e i consulenti.',
    [('Il metodo', METODO), ('Lo studio', STUDIO), ('Dove siamo', CONTATTI + '#arrivare'), ('Primo incontro', CONTATTI)],
    [('home', 'Home', None), ('il-metodo', 'Il metodo', 'il-metodo'), ('lo-studio', 'Lo studio', 'lo-studio'),
     ('contatti', 'Contatti', 'contatti')],
    HEX, '\'Hanken Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
    '\'Newsreader\', \'Georgia\', serif', '\'Fragment Mono\', ui-monospace, Menlo, monospace',
    ['Newsreader', 'Hanken Grotesk', 'Fragment Mono'], corsivo_di=['Newsreader'], peso_titoli='400', raggio=2, grana=4,
    titoli_line='1.02', titoli_spazio='-0.01em')


def main():
    write_theme(THEME, firme.applica(ID, {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
                        'il-metodo.json': build_metodo(), 'lo-studio.json': build_studio(), 'contatti.json': build_contatti(),
                        '404.json': build_404()}), MEDIA)


if __name__ == '__main__':
    main()
