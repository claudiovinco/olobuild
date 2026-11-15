# -*- coding: utf-8 -*-
"""La home: «Tutto e subito? Un pezzo al giorno.»

Tutto parte dallo studente (richiesta dell'utente, 7 nov 2026): prima lo studio di ogni giorno, il ripasso e
l'allenamento, poi il docente, poi la gestione della scuola. Il voto viene dopo, come conseguenza del metodo di studio
e della partecipazione: la prima versione apriva sul voto che passava dal registro alla famiglia, ed è stata bocciata
(«fa pensare subito al voto, che invece è solo un derivato del metodo di studio e del coinvolgimento dello studente»).
"""
from comune import (K, Tpl, mix, radius, solid, img, occ, tit, txt, mano, btn, bottoni, foto, schermata, sez, due, griglia,
                    scheda, marquee, chiusura, numeri_banda, hotspot, MANO, CARTA, INK, TENUE, LAV, VERDE, ONV, GIALLO, GESSO,
                    CIELO, FILO, ROSSO, VERDE_G, GESSO_T, GESSO_F, LAV2, CARTA2, STU, DOC, SCU, PRO, MET)

m = K.mob

# L'apertura sta in p_home_apertura.py (video di sottofondo, telefono in 3D, sei fatti).
from p_home_apertura import apertura  # noqa: E402


# ── 1. Lo studente ──
def studente_prima(t):
    cards = [
        {'eyebrow': 'Da 3 a 5 minuti', 'title': 'Le pillole', 'accent': '', 'media': img('otr-studentessa-parco.webp'),
         'text': '<p>Contenuti brevi in 8 formati. In cima al massimo 3 pillole «Per oggi», ognuna con quello che si saprà '
                 'alla fine e quanto dura.</p>', 'color': GIALLO, 'text_color': INK},
        {'eyebrow': 'Al massimo 5 carte nuove al giorno', 'title': 'Il ripasso', 'accent': 'che nasce dagli errori',
         'media': img('otr-studente-ripasso.webp'), 'fit': 'contain',   # intera: in «cover» perdeva i tasti di valutazione
         'text': '<p>Ogni domanda sbagliata in un quiz o in una partita in aula diventa una carta di ripasso, che torna nel '
                 'giorno calcolato dall\'algoritmo FSRS. Il tetto è lo stesso per tutti: chi è più indietro non riceve più '
                 'lavoro degli altri.</p>', 'color': LAV, 'text_color': GESSO},
        {'eyebrow': 'La risposta, subito', 'title': 'L\'allenamento', 'accent': '', 'media': img('otr-studente-caselle.webp'),
         'fit': 'contain',
         'text': '<p>Un quiz che si rifà quante volte si vuole: dopo ogni risposta «Controlla» '
                 'mostra subito se era giusta. Alla consegna le risposte stanno in quattro caselle: conoscenza solida, '
                 'convinzione sbagliata, fortuna o intuizione, lacuna.</p>', 'color': CIELO, 'text_color': INK},
        {'eyebrow': 'Dal telefono, o con un cartoncino', 'title': 'In aula', 'accent': '', 'media': img('otr-cartoncini.webp'),
         'text': '<p>Si entra con un codice di 6 caratteri o col QR, si alza la mano, si risponde a «Siamo al passo?» e si '
                 'gioca. Dove i telefoni non sono ammessi basta un cartoncino stampato, che resta suo.</p>',
         'color': VERDE, 'text_color': GESSO},
    ]
    st = t.classic('stackscroll', {'cards': cards, 'top_offset': 110, 'top_step': 26, 'card_gap': 28,
                                   'scale_on_stack': True, 'scale_amount': 3, 'card_min_height': 420, 'round': 28,
                                   'card_padding': {'top': 44, 'right': 48, 'bottom': 44, 'left': 48},
                                   'media_position': 'right', 'object_position': 'center top', 'show_number': True,
                                   'num_color_default': 'currentColor',
                                   'shadow_color': mix(LAV, 30)})
    return sez(t, [
        due(t, [occ(t, 'Per lo studente · tutto parte da qui'), tit(t, 'Un po\' ogni giorno, <em>dal telefono</em>.', size=60)],
            [txt(t, 'Lo studente usa OLOtutor soprattutto dal telefono, in aula e a casa. Ogni giorno trova che cosa '
                    'ripassare, si esercita con i quiz e alla consegna vede quali risposte sapeva davvero e quali ha '
                    'indovinato.'),
             btn(t, 'Che cosa trova lo studente', STU, 'verde')], peso=(55, 45), valign='end'),
        t.one([st], style={'margin_top': 40}),
    ], fondo=CARTA, pad=(120, 110), html_id='studente')


def certezza(t):
    q = t.classic('scorequiz', {
        'eyebrow': 'Una domanda di Informatica di base', 'heading': 'Prova la certezza',
        'intro': 'Due passi: scegli la risposta, poi di\' quanto ne sei sicuro.',
        'questions': [
            {'text': 'Un file viene rinominato da «foto.png» a «foto.jpg». Che cosa è cambiato davvero?',
             'options': 'Il file adesso è un JPG|0\nSolo il nome: il contenuto resta in formato PNG|10\nIl file è stato compresso|0'},
            {'text': 'Quanto ne sei sicuro?', 'options': 'So la risposta|2\nCredo sia questa|1\nSto tirando a indovinare|0'},
        ],
        'results': [
            {'min': 0, 'title': 'Lacuna', 'text': 'Sbagliata, e non ne eri sicuro. Il nome è cambiato, il contenuto è '
             'rimasto in formato PNG. In OLOtutor una domanda della banca sbagliata diventa una carta di ripasso.',
             'link_text': 'Il ripasso che nasce dagli errori', 'link_url': STU + '#ripasso'},
            {'min': 2, 'title': 'Convinzione sbagliata', 'text': 'Eri sicuro, ed era sbagliata: è cambiato solo il nome. '
             'Un errore fatto con sicurezza, una volta corretto, si ricorda meglio di uno fatto nel dubbio, e nel ripasso '
             'torna prima degli altri.', 'link_text': 'Da dove viene: il metodo', 'link_url': MET},
            {'min': 10, 'title': 'Fortuna o intuizione', 'text': 'Giusta, ma non ne eri sicuro: nelle caselle della '
             'certezza resta scritto che era una risposta azzeccata, non saputa. La certezza non entra mai nel voto.',
             'link_text': 'Che cosa vede lo studente', 'link_url': STU},
            {'min': 12, 'title': 'Conoscenza solida', 'text': 'Giusta, e lo sapevi: cambia solo il nome, il contenuto resta '
             'PNG. Questa è la casella in cui si vorrebbe vedere tutta la classe.', 'link_text': 'Che cosa vede il docente',
             'link_url': DOC},
        ],
        'result_label': 'La tua casella:', 'back_label': 'Indietro', 'restart_label': 'Riprova', 'show_score': False,
        'zone_accent': VERDE, 'zone_on': ONV, 'card_bg': GESSO,
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': FILO},
        'align': 'left'})
    caselle = griglia(t, [
        [occ(t, 'sicuro · giusta', col=GESSO, pad_bottom=6), tit(t, 'Conoscenza solida', size=24, col=GESSO, pad_bottom=0)],
        [occ(t, 'sicuro · sbagliata', col=GESSO, pad_bottom=6), tit(t, 'Convinzione sbagliata', size=24, col=GESSO, pad_bottom=0)],
        [occ(t, 'incerto · giusta', col=INK, pad_bottom=6), tit(t, 'Fortuna o intuizione', size=24, col=INK, pad_bottom=0)],
        [occ(t, 'incerto · sbagliata', col=INK, pad_bottom=6), tit(t, 'Lacuna', size=24, col=INK, pad_bottom=0)],
    ], n=2, gap=12, stili=[scheda(VERDE, r=18, pad=(22, 22, 22, 22)), scheda(ROSSO, r=18, pad=(22, 22, 22, 22)),
                          scheda(GIALLO, r=18, pad=(22, 22, 22, 22)), scheda(GESSO, r=18, pad=(22, 22, 22, 22), bordo=FILO)],
        anim='fade')
    return sez(t, [
        due(t, [occ(t, 'Allenamento e certezza'),
                tit(t, 'So la risposta, <em>o sto tirando a indovinare?</em>', size=52),
                txt(t, 'Nei quiz in cui il docente chiede la certezza, lo studente sceglie la risposta e poi dichiara quanto '
                       'ne è sicuro. Alla consegna vede le risposte divise in quattro caselle. La certezza non entra mai nel '
                       'voto, quindi conviene dichiararla con sincerità.', pad=(0, 0, 24, 0)),
                caselle],
            [mano(t, 'una domanda vera della piattaforma ↓', col=VERDE, size=30), q], peso=(50, 50), valign='center'),
    ], fondo=CARTA2, pad=(120, 120), html_id='certezza')


def tre_misure(t):
    """Partecipazione, certezza e poi il voto: tre misure separate, il voto per ultimo."""
    def misura(occhiello, nome, testo, chiusa, col):
        return [occ(t, occhiello, col=col, pad_bottom=8), tit(t, nome, size=34, col=col, pad_bottom=12),
                txt(t, testo, size=17, col=col), txt(t, '<strong>%s</strong>' % chiusa, size=17, col=col, pad=(10, 0, 0, 0))]
    return sez(t, [
        due(t, [occ(t, 'Tre misure tenute separate'),
                tit(t, 'Quanto partecipa, quanto è sicuro, <em>e poi quanto sa</em>.', size=52)],
            [txt(t, 'Quanto uno studente sa, quanto partecipa e quanto è sicuro di una risposta sono tre informazioni '
                    'diverse, che di solito finiscono in un numero solo. OLOtutor le tiene separate.'),
             mano(t, 'il voto viene dopo: lo fanno solo le prove valutate.', col=VERDE, size=28, pad_bottom=0)],
            peso=(55, 45), valign='end'),
        griglia(t, [
            misura('Quanto partecipa', 'I punti esperienza', 'Ci sono 12 modi di prenderli e 8 premiano la partecipazione: '
                   'completare le lezioni, provare i quiz anche senza superarli, segnare la presenza, scrivere nel forum, '
                   'studiare più giorni di seguito.', 'Fanno il livello e la classifica, mai il voto.', INK),
            misura('Quanto è sicuro', 'La certezza', 'La dichiara lo studente nei quiz in cui il docente la chiede, e serve a '
                   'distinguere una risposta saputa da una indovinata.', 'Non entra mai nel voto.', GESSO),
            misura('Quanto sa', 'Il voto', 'Viene soltanto dalle prove valutate, dai compiti corretti e dagli interventi '
                   'valutati nel forum.', 'Entra nel registro, nella media e nella pagella.', INK),
        ], n=3, gap=20, stili=[scheda(GIALLO, r=24, pad=(34, 32, 34, 32)), scheda(LAV, r=24, pad=(34, 32, 34, 32)),
                               scheda(GESSO, r=24, pad=(34, 32, 34, 32), bordo=FILO)]),
    ], fondo=CARTA, pad=(110, 120))


# ── 2. Il docente ──
def frase(t):
    sc = t.classic('scrubtext', {
        'text': 'I metodi che funzionano esistevano prima del software. <em>OLOtutor serve a trovare il tempo di usarli.</em>',
        'show_lead': True, 'lead': 'OLOtutor non promette risultati perché è digitale. I metodi che usa funzionavano prima '
        'del software, e funzionerebbero anche senza. Il docente li conosce già, e non ha il tempo di applicarli: il '
        'software serve a quello.', 'scroll_reveal': True, 'dim_opacity': 18, 'accent': VERDE, 'text_color': INK,
        'lead_color': TENUE, 'size_min': 34, 'size_max': 72, 'max_width_ch': 22, 'lead_size': 20, 'lead_max_width_ch': 60,
        'text_font_family': 'var(--olo-font-family-heading)', 'text_font_weight': '700', 'text_align': 'left'})
    return t.section([
        t.row([t.col([occ(t, 'Perché è nato'), sc], wm=None, ws='1-1'), t.col([
            mano(t, 'ripasso distanziato,', col=VERDE, size=34),
            mano(t, 'pratica di recupero,', col=VERDE, size=34),
            mano(t, 'peer instruction…', col=VERDE, size=34),
            mano(t, '… li conosci già.', col=INK, size=34, pad_bottom=26),
            btn(t, 'Perché ci crediamo: il metodo', MET, 'filo', pad_top=0)], wm=None, ws='1-1')],
            layout='custom', custom='68,32', gap=56, valign='end'),
    ], bg={'type': 'image', 'image_url': img('otr-grid-tr.svg'), 'image_size': 'cover', 'image_position': 'center center',
           'overlay_color': CARTA, 'overlay_opacity': 0}, padding='large', style={'padding_top': 130, 'padding_bottom': 130})


GIOCHI = [('otr-gioco-showdown.webp', 'Showdown', 'la domanda per tutti, la classifica dal vivo'),
          ('otr-gioco-ruota.webp', 'La ruota', 'la sorte sceglie il tipo di domanda'),
          ('otr-gioco-memory.webp', 'Memory', 'le coppie da ritrovare'),
          ('otr-gioco-impiccato.webp', 'Impiccato', 'le parole del glossario'),
          ('otr-gioco-cruciverba.webp', 'Cruciverba', 'le definizioni del corso'),
          ('otr-gioco-type-race.webp', 'Type Race', 'chi scrive per primo'),
          ('otr-gioco-linea-del-tempo.webp', 'Linea del tempo', 'mettere in ordine'),
          ('otr-gioco-memory-test.webp', 'Memory Test', 'quanto resta in mente')]
# 8 nov 2026 («porre sempre attenzione ai tagli non voluti»): nel nastro restano solo le schermate verticali del
# telefono (proporzione 0,46, come la carta 270×585). Il Milionario (orizzontale) perdeva il 39% per lato e metà
# delle domande; Battle royale, Caccia al tesoro, Conquista, Eliminazione, Sfida fotografica e Tabellone (0,5–0,8)
# perdevano dal 5 al 19% ai lati, con le scritte tagliate.


def docente_poi(t):
    cf = K.coverflow(t, [(f, 'Il gioco «%s» di OLOtutor' % n, n, s) for f, n, s in GIOCHI], altezza=585, larghezza=270)
    cf['settings'].update({'filmstrip_dots_color': VERDE, 'thumb_radius': 22})
    return sez(t, [
        due(t, [occ(t, 'Per il docente · prima, durante e dopo la lezione'),
                tit(t, 'Prepara una volta, <em>conduce dal telefono</em>.', size=56)],
            [txt(t, 'Il materiale si prepara una volta e serve a quiz, prove, giochi e ripasso; l\'aula si conduce dal '
                    'telefono, e prima della verifica si vede dove la classe ha un\'idea sbagliata. In Strumenti Live ci sono '
                    '34 attività, 29 giochi e 5 strumenti: 8 giochi si giocano anche senza telefoni, con i cartoncini.'),
             btn(t, 'Che cosa trova il docente', DOC, 'verde')], peso=(52, 48), valign='end'),
        t.one([cf], style={'margin_top': 40}),
    ], fondo=CARTA2, pad=(120, 100), html_id='docente')


# ── 3. La scuola ──
def scuola_poi(t):
    lista = t.classic('iconlist', {'items': [
        {'icon': 'calculator', 'text': 'Medie e frequenza calcolate in un solo punto, uguali in registro, stampe e pagelle',
         'link': '', 'color': ''},
        {'icon': 'clock', 'text': 'Presenze a ore, e registri con l\'intestazione della scuola', 'link': '', 'color': ''},
        {'icon': 'users', 'text': 'Le famiglie seguono il percorso dei figli, in sola lettura', 'link': '', 'color': ''},
        {'icon': 'sprout', 'text': 'Si comincia da una classe e da un corso', 'link': '', 'color': ''}],
        'icon_color': INK, 'icon_size': '16', 'text_color': GESSO, 'text_size': '18', 'gap': '16', 'icon_shape': 'circle',
        'icon_bg_color': GIALLO, 'divider': True, 'divider_color': GESSO_F, 'layout': 'vertical'})
    return sez(t, [
        due(t, [occ(t, 'Per la scuola', col=GIALLO),
                tit(t, 'Registro, medie, presenze e famiglie, <em>in un solo posto</em>.', size=52, col=GESSO, acc=GIALLO),
                t.one([lista], anim=None, style={'margin_top': 8}),
                btn(t, 'Che cosa trova la scuola', SCU, 'giallo')],
            [griglia(t, [[schermata(t, 'otr-scuola-medie.webp', 'Le medie della classe: studenti in riga e materie in colonna',
                                    ratio='1600/989', pos='left top', r=16),
                          mano(t, 'le medie della classe, per periodo', col=VERDE_G, size=24, align='center', pad_bottom=0)],
                         [schermata(t, 'otr-scuola-come-va.webp', 'La schermata Come va del genitore sul telefono',
                                    ratio='640/1035', r=22),
                          mano(t, '«Come va», per la famiglia', col=VERDE_G, size=24, align='center', pad_bottom=0)]],
                     n=2, gap=18, anim='fade')],
            peso=(46, 54), valign='center'),
    ], fondo=LAV, pad=(120, 120), html_id='scuola')


def dati(t):
    # il portale vero, installato sul sito della scuola (7 nov 2026: prima c'era il disegno esploso di un computer, che
    # viene da un test di un corso e qui non c'entrava)
    # 8 nov 2026, l'utente: «elimina i 5 punti gialli, non hanno senso». Il portale resta, intero.
    h = schermata(t, 'otr-portale.webp', 'La pagina iniziale del portale per l\'amministratore: i corsi, '
                  'l\'aula aperta, studenti, valutazione e amministrazione', ratio='1532/1132', pos='center center',
                  r=18)
    return sez(t, [
        due(t, [occ(t, 'Dove stanno i dati'), tit(t, 'I dati restano <em>sul sito della scuola</em>.', size=56),
                txt(t, 'OLOtutor è un plugin di WordPress e si installa sul sito dell\'istituto, quindi voti, presenze e lavori '
                       'degli studenti stanno nel database di quel sito.')],
            [h], peso=(42, 58), valign='center'),
    ], fondo=CIELO, pad=(110, 110))


# ── in fondo: le tre figure e i numeri ──
def flip(t, foto_n, pos, chi, titolo, testo, link_t, link_u, fondo, col, cta_bg, cta_col):
    return t.classic('flipcard', {
        'front_media': {'type': 'image', 'image_url': img(foto_n), 'image_size': 'cover', 'image_position': pos,
                        'overlay_color': LAV, 'overlay_opacity': 88, 'overlay_fade': 'bottom', 'overlay_fade_reach': 60},
        'back_media': {'type': 'none'}, 'front_image': '', 'front_video': '', 'front_icon': '',
        'front_title': chi, 'front_description': titolo, 'front_text_color': GESSO, 'front_text_align': 'left',
        'front_valign': 'bottom', 'back_image': '', 'back_video': '', 'back_icon': '', 'back_title': titolo,
        'back_description': testo, 'back_bg': fondo, 'back_text_color': col, 'back_text_align': 'left',
        'back_valign': 'center', 'back_cta_text': link_t, 'back_cta_url': link_u, 'back_cta_target': False,
        'back_cta_bg': cta_bg, 'back_cta_color': cta_col, 'back_cta_radius': '12', 'flip_direction': 'horizontal',
        'flip_duration': '800', 'flip_trigger': 'hover', 'card_height': '520', 'card_border_radius': '24',
        'card_shadow': 'lg', 'title_size': '30', 'title_weight': '700', 'desc_size': '17', 'text_effect': 'none',
        'tile_padding': {'top': 30, 'right': 30, 'bottom': 30, 'left': 30}})


def tre_ingressi(t):
    carte = [
        flip(t, 'otr-studentessa-telefono.webp', '40% 30%', 'Per lo studente', 'Lo studio di ogni giorno',
             'Ogni giorno lo studente trova che cosa ripassare, si esercita con i quiz e vede '
             'quali risposte sapeva davvero e quali ha indovinato.', 'Che cosa trova lo studente', STU, GIALLO, INK, INK, GESSO),
        flip(t, 'otr-classe-prof.webp', '62% 40%', 'Per il docente', 'Prima, durante e dopo la lezione',
             'Il docente prepara il materiale una volta e lo usa in quiz, giochi e ripasso, conduce l\'aula dal proprio '
             'telefono e vede prima della verifica dove la classe ha un\'idea sbagliata.', 'Che cosa trova il docente', DOC,
             VERDE, ONV, GESSO, INK),
        flip(t, 'otr-classe-lezione.webp', '50% 40%', 'Per la scuola', 'Registro, medie, presenze e famiglie',
             'La scuola ha medie, presenze e registri calcolati in un solo punto, famiglie che seguono il percorso dei figli '
             'e un\'adozione che può cominciare da una classe.', 'Che cosa trova la scuola', SCU, CARTA, INK, VERDE, ONV),
    ]
    return sez(t, [
        due(t, [occ(t, 'Chi usa OLOtutor', col=GIALLO), tit(t, 'Tre ingressi <em>sugli stessi dati</em>', size=64, col=GESSO,
                                                              acc=GIALLO)],
            [txt(t, 'Quello che lo studente fa ogni giorno, il docente lo vede prima della lezione e la scuola lo ritrova nel '
                    'registro, senza ricopiare niente. Scegli da dove cominciare.', col=GESSO_T)], peso=(55, 45), valign='end'),
        griglia(t, [[c] for c in carte], n=3, gap=24),
        t.one([mano(t, 'passa sopra una carta, o toccala, per girarla ↻', col=VERDE_G, size=28, align='center')],
              style={'margin_top': 26}),
    ], fondo=LAV, pad=(120, 110), html_id='tre-ingressi')


def numeri(t):
    a = numeri_banda(t, [('5', 'carte di ripasso nuove al giorno, al massimo'), ('3–5', 'minuti per pillola, in 8 formati'),
                         ('12', 'modi di prendere i punti esperienza'), ('4', 'caselle della certezza, fuori dal voto')])
    b = numeri_banda(t, [('34', 'attività in aula: 29 giochi e 5 strumenti'), ('8', 'giochi anche senza telefoni'),
                         ('77', 'definizioni ufficiali di competenza'), ('5', 'quadri europei con i descrittori ufficiali')])
    head = K.sechead(t, 'I numeri', ['Quanto c\'è dentro,', 'in cifre.'], tagline='I contenuti ufficiali sono già dentro '
                     'il programma. Un corso preparato si duplica o si esporta in un pacchetto, e l\'anno dopo si riparte da '
                     'lì invece di rifarlo.', size=64, chiaro=True, size_mobile=40)
    hs = head['settings']
    hs['headline_lines'][1]['color'] = GIALLO
    hs.update({'eyebrow_color': GIALLO, 'eyebrow_dot_color': GIALLO, 'eyebrow_font_family': 'heading',
               'eyebrow_font_size': 13, 'eyebrow_font_weight': '700', 'tagline_text_color': GESSO_T, 'tagline_text_size': 18})
    return t.section([t.one([head]), t.one([a], style={'margin_top': 20}), t.one([b], style={'margin_top': 8})],
                     bg={'type': 'image', 'image_url': img('otr-num-cerchi-navy.svg'), 'image_size': 'cover',
                         'image_position': 'right top', 'overlay_color': LAV, 'overlay_opacity': 0},
                     padding='large', style={'padding_top': 120, 'padding_bottom': 110})


def build():
    t = Tpl('otr', 'home')
    return [
        K.apertura(t, apertura(t)),
        frase(t),            # perché è nato
        tre_ingressi(t),     # le tre figure: da qui si sceglie dove andare
        studente_prima(t),   # 1. lo studente
        certezza(t),
        tre_misure(t),
        docente_poi(t),      # 2. il docente
        scuola_poi(t),       # 3. la scuola
        dati(t),
        numeri(t),
        t.section([t.one([marquee(t, 'Ripasso distanziato | Pratica di recupero | Pratica intercalata | Peer Instruction | '
                                     'Ipercorrezione | Rubriche analitiche | Open Badges 2.0')], anim=None)],
                  width='fullbleed', padding='remove-vertical'),
        chiusura(t),
    ]
