# -*- coding: utf-8 -*-
"""La Pro Gallery nei temi Pro (23 nov 2026). L'utente: «ora che sai usare bene la pro gallery, rivedi tutti i temi pro che
han bisogno di una galleria (non metterla dove non serve) ed usa una bella personalizzazione per integrarla nel modo
migliore dentro i temi (non metterla nei temi gratuiti). Cerca di metterla quando si tratta di ecommerce (magari solo in
un paio, come dimostrazione)».

Ogni PROGETTO trasforma UNA tile di un tema Pro in una Pro Gallery pensata per quel tema:
- le vecchie «Galleria» (sei foto in griglia, spesso senza titoli) diventano gallerie con i testi sulle foto, i numeri, i
  filtri, la cornice o la pellicola, coi titoli e le categorie scritti per il tema;
- in due negozi (Tracolla, Ristretto) la griglia «Appena arrivate / Appena tostati» diventa una Pro Gallery che legge i
  prodotti di WooCommerce (Sorgente dinamica: foto, nome, categoria, prezzo, link) con la Vista rapida sull'icona.
Colori solo dai token del tema (mai codici fissi), id delle tile invariati (le traduzioni restano), foto della libreria.
Si riapplica da base.write_theme (dopo gallerie.py) e da qui sui JSON già scritti.
uso: python gallerie_pro.py [--scrivi]"""
import json
import os
import sys

QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
FREE = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'temi-free.json'))

LIB = '{{OLOBUILD_LIBRARY}}/media/'
P, S, A = 'var(--olo-color-primary)', 'var(--olo-color-secondary)', 'var(--olo-color-accent)'
TESTO, LUCE, SCURO = 'var(--olo-color-text)', 'var(--olo-color-light)', 'var(--olo-color-dark)'


def tipo(pre, fam='', size='', peso='', tr='', ls='', col=''):
    o = {}
    for k, v in (('font_family', fam), ('font_size', size), ('font_weight', peso), ('text_transform', tr), ('letter_spacing', ls), ('color', col)):
        if v != '':
            o[pre + '_' + k] = v
    return o


def pad(t, r, b, l):
    return {'top': t, 'right': r, 'bottom': b, 'left': l}


def unisci(*d):
    o = {}
    for x in d:
        o.update(x)
    return o


def prodotti(n, ordine='date'):
    """La Sorgente dinamica coi prodotti di WooCommerce: foto, nome, categoria, prezzo nella nota, link alla scheda."""
    return {'_query': {'enabled': True, 'source': 'posts', 'post_type': 'product', 'posts_per_page': n, 'orderby': ordine,
                       'order': 'DESC', 'taxonomy': '', 'terms': [], 'items_key': 'images'},
            '_itemMap': {'url': 'featured_image', 'caption': 'post_title', 'nota': 'wc_price', 'category': 'first_term',
                         'link': 'permalink', 'alt': 'post_title'}}


PROGETTI = {
    # Il club dei videogiochi d'epoca, scuro: i livelli del club come schermate di gioco, «Stage 01/06», il filo ciano
    'pixel-club': [{'file': 'club.json', 'id': 'pixel-club-club-10', 'voci': [
        {'caption': 'La console fra i neon', 'category': 'Console'},
        {'caption': 'Il televisore del sabato sera', 'category': 'Salotto'},
        {'caption': 'Il computer davanti alla neve', 'category': 'Computer'},
        {'caption': 'La tastiera degli anni Ottanta', 'category': 'Computer'},
        {'caption': 'L’insegna sopra la vetrina', 'category': 'Insegne'},
        {'caption': 'La galleria con le luci blu', 'category': 'Insegne'}],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'aspect_ratio': '4/3', 'gap': 10, 'thumb_radius': 0,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1',
            'hover_effect': 'glow', 'hover_glow_color': S, 'hover_glow_spread': 22, 'entrance': 'zoom-center', 'entrance_stagger': 90,
            'frame': 'filo', 'frame_color': S, 'frame_spessore': 1,
            'scheda': 'sopra', 'scheda_velo': 82, 'scheda_velo_alto': 62, 'scheda_numero': True, 'scheda_numero_formato': 'su',
            'scheda_numero_prefisso': 'Stage ', 'scheda_categoria': True, 'scheda_stile_categoria': 'testo', 'scheda_accento': P,
            'scheda_separatore': 'punto',
            'zona_numero': 'as', 'zona_categoria': 'ad', 'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_font': 'heading',
            'scheda_titolo_size': 15, 'scheda_peso': '400', 'scheda_colore': LUCE},
            tipo('scheda_meta', 'mono', 10, '600', 'uppercase', 1.5, P))}],

    # L'atelier di abiti da sposa: la collezione in masonry, il nome in corsivo sotto la foto, i filtri per tessuto
    'maison-blanche': [{'file': 'collezione.json', 'id': 'maison-blanche-collezione-12', 'voci': [{}, {}, {}, {}, {}, {}],
        'impostazioni': unisci({
            'layout': 'masonry', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'gap': 28, 'thumb_radius': 0,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1', 'lightbox_animation': 'fade',
            'hover_effect': 'zoom', 'hover_zoom_scale': 1.03, 'entrance': 'fade-up', 'entrance_stagger': 120, 'entrance_duration': 900,
            'scheda': 'sotto', 'scheda_categoria': True, 'scheda_stile_categoria': 'testo', 'zona_categoria': 'as',
            'scheda_numero': True, 'scheda_numero_prefisso': 'N. ', 'zona_numero': 'ad', 'zona_titolo': 'bs', 'scheda_linea_sotto': True,
            'scheda_icona': '', 'scheda_font': 'heading', 'scheda_titolo_size': 26, 'scheda_peso': '400', 'scheda_titolo_italic': True,
            'scheda_colore': TESTO, 'scheda_accento': S, 'scheda_padding': pad(14, 0, 12, 0), 'scheda_anima': 'maschera',
            'filtri': True, 'filtri_stile': 'sottolineato', 'filtri_riga_sopra': True, 'filtri_colore': TESTO,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutta la collezione'},
            tipo('scheda_meta', '', 10, '500', 'uppercase', 2, S), tipo('filtri', '', 12, '500', 'uppercase', 1.5))}],

    # Il negozio vintage: il martedì arriva la merce nuova. La galleria sta in mezza colonna dentro le sezioni che scorrono
    # di lato: niente nastro da trascinare, una griglia di foto attaccate col nastro adesivo, col filtro d'epoca che torna a
    # colori al passaggio, e titolo e decennio che compaiono sulla foto
    'rewind-vintage': [{'file': 'home.json', 'id': 'rewind-vintage-home-54', 'voci': [
        {'caption': 'L’abito a righe', 'subtitle': 'anni Settanta', 'category': 'Abiti'},
        {'caption': 'Gli scaffali pieni', 'subtitle': 'i capi comprati il martedì', 'category': 'Il negozio'},
        {'caption': 'Lo scooter rosso', 'subtitle': 'anni Sessanta', 'category': 'In giro'},
        {'caption': 'Pois e occhiali da sole', 'subtitle': 'anni Cinquanta', 'category': 'Abiti'},
        {'caption': 'L’auto in negozio', 'subtitle': 'anni Cinquanta', 'category': 'In giro'},
        {'caption': 'L’insegna accesa', 'subtitle': 'anni Ottanta', 'category': 'Il negozio'}],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 3, 'tablet_columns': 3, 'mobile_columns': 2, 'aspect_ratio': '1/1', 'gap': 16, 'thumb_radius': 0,
            'frame': 'tape', 'filter': 'vintage', 'filter_hover_restore': True,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04,
            'entrance': 'land', 'entrance_stagger': 90,
            'scheda': 'sopra', 'scheda_quando': 'passaggio', 'scheda_velo': 78, 'scheda_categoria': False, 'scheda_icona': '',
            'scheda_titolo_size': 14, 'scheda_peso': '700', 'scheda_colore': LUCE,
            'filtri': True, 'filtri_stile': 'bordate', 'filtri_conteggi_stile': 'accanto', 'filtri_colore': TESTO, 'filtri_tutti': 'Tutto'},
            tipo('scheda_sot', 'mono', 10, '', '', '', LUCE), tipo('filtri', '', 12, '600'))}],

    # La tessitura: le sei fasi del reparto in ordine, numerate, coi filtri Orditura e Tessitura
    'tessiture-navetta': [{'file': 'laboratorio.json', 'id': 'tessiture-navetta-laboratorio-37', 'voci': [
        {'category': 'Orditura'}, {'category': 'Orditura'}, {'category': 'Orditura'},
        {'category': 'Tessitura'}, {'category': 'Tessitura'}, {'category': 'Tessitura'}],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'aspect_ratio': '4/3', 'gap': 16, 'thumb_radius': 4,
            'lightbox': True, 'lightbox_thumbs': 'right', 'lightbox_thumbs_rows': '2',
            'hover_effect': 'lift', 'entrance': 'slide-in', 'entrance_stagger': 100,
            'scheda': 'sotto', 'scheda_numero': True, 'scheda_numero_prefisso': 'Fase ', 'zona_numero': 'as',
            'scheda_categoria': True, 'scheda_stile_categoria': 'testo', 'zona_categoria': 'ad', 'scheda_separatore': 'barra',
            'zona_titolo': 'bs', 'scheda_titolo_size': 17, 'scheda_peso': '500', 'scheda_colore': TESTO, 'scheda_accento': P,
            'scheda_linea_sotto': True, 'scheda_icona': '', 'scheda_padding': pad(14, 2, 12, 2),
            'filtri': True, 'filtri_stile': 'semplice', 'filtri_forma': 'squadrata', 'filtri_colore': P,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutto il reparto'},
            tipo('scheda_meta', 'mono', 10, '600', 'uppercase', 1.5, P), tipo('filtri', 'mono', 11, '600', 'uppercase', 1.5))}],

    # Il laboratorio di microsaldatura: card bianche, Diagnosi o Riparazione nell'etichetta con l'icona, il numero sul totale
    'scheda-madre': [{'file': 'assistenza.json', 'id': 'scheda-madre-assistenza-43', 'voci': [
        {'category': 'Diagnosi', 'icona': 'microscope'}, {'category': 'Diagnosi', 'icona': 'microscope'},
        {'category': 'Diagnosi', 'icona': 'microscope'}, {'category': 'Riparazione', 'icona': 'wrench'},
        {'category': 'Riparazione', 'icona': 'wrench'}, {'category': 'Riparazione', 'icona': 'wrench'}],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'aspect_ratio': '3/2', 'gap': 18, 'thumb_radius': 12,
            'lightbox': True, 'lightbox_thumbs': 'none', 'lightbox_animation': 'scale',
            'hover_effect': 'zoom', 'hover_zoom_scale': 1.05, 'entrance': 'fade-up', 'entrance_stagger': 80,
            'scheda': 'sotto', 'scheda_sfondo': LUCE, 'scheda_padding': pad(16, 18, 20, 18),
            'scheda_categoria': True, 'scheda_stile_categoria': 'chiaro', 'zona_categoria': 'as',
            'scheda_numero': True, 'scheda_numero_formato': 'su', 'zona_numero': 'ad', 'zona_titolo': 'bs',
            'scheda_titolo_size': 15, 'scheda_peso': '500', 'scheda_colore': TESTO, 'scheda_accento': P, 'scheda_icona': ''},
            tipo('scheda_meta', 'mono', 10, '600', 'uppercase', 1, P))}],

    # Negozio di borse: le ultime arrivate dal laboratorio, coi prodotti veri, il prezzo grande, i filtri per forma e la
    # Vista rapida sul «+»
    'tracolla': [{'file': 'home.json', 'id': 'tracolla-home-48', 'dinamica': prodotti(8),
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 2, 'aspect_ratio': '4/5', 'gap': 20, 'thumb_radius': 10,
            'images': [], 'lightbox': False, 'hover_effect': 'zoom', 'hover_zoom_scale': 1.05, 'entrance': 'fade-up', 'entrance_stagger': 80,
            'scheda': 'sotto', 'scheda_categoria': True, 'scheda_stile_categoria': 'testo', 'zona_categoria': 'as', 'zona_titolo': 'bs',
            'zona_nota': 'ps', 'scheda_nota_stile': 'grande', 'scheda_icona': 'piu', 'vista_rapida': True,
            # il titolo si adatta: 18 px sul computer, ~15 sul telefono (a due colonne i nomi lunghi non si tagliano)
            'scheda_font': 'heading', 'scheda_titolo_size': 18, 'scheda_titolo_adatta': True, 'scheda_peso': '400',
            'scheda_colore': TESTO, 'scheda_accento': P, 'scheda_padding': pad(12, 2, 4, 2), 'scheda_anima': 'sale',
            'filtri': True, 'filtri_stile': 'pillole', 'filtri_forma': 'pillola', 'filtri_colore': P,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutte'},
            tipo('scheda_meta', 'mono', 10, '500', 'uppercase', 1.5, P), tipo('filtri', '', 13, '500'))}],

    # Torrefazione: i caffè appena tostati in un nastro da trascinare, il numero del lotto, il reparto in un'etichetta, il
    # prezzo grande e la Vista rapida
    'ristretto': [{'file': 'home.json', 'id': 'ristretto-home-48', 'dinamica': prodotti(8),
        'impostazioni': unisci({
            'layout': 'strip', 'strip_height': 300, 'strip_item_width': 300, 'gap': 16, 'thumb_radius': 12,
            'strip_arrows': True, 'strip_arrows_style': 'circle', 'strip_arrows_bg': LUCE, 'strip_arrows_color': SCURO,
            'strip_arrows_size': 40, 'strip_fade_edges': False, 'strip_avanzamento': True,
            'images': [], 'lightbox': False, 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04, 'entrance': 'slide-in', 'entrance_stagger': 70,
            'scheda': 'sotto', 'scheda_numero': True, 'scheda_numero_prefisso': 'Lotto ', 'zona_numero': 'as',
            'scheda_categoria': True, 'scheda_stile_categoria': 'pieno', 'scheda_accento': P, 'zona_categoria': 'ad',
            'zona_titolo': 'bs', 'zona_nota': 'ps', 'scheda_nota_stile': 'grande', 'scheda_icona': 'piu', 'vista_rapida': True,
            'scheda_font': 'heading', 'scheda_titolo_size': 18, 'scheda_peso': '400', 'scheda_colore': TESTO,
            'scheda_padding': pad(12, 2, 4, 2)},
            # senza colore: l'etichetta piena tiene il bianco sul marrone del tema, il numero prende il colore del testo
            tipo('scheda_meta', 'mono', 10, '600', 'uppercase', 1.5))}],
}


def foto(px, alt, caption, **extra):
    """Una voce del secondo giro: la foto pexels-<px>.jpg della libreria coi suoi testi."""
    return dict({'file': 'pexels-%d.jpg' % px, 'alt': alt, 'caption': caption}, **extra)


# ─── Secondo giro (24 nov 2026, utente: «Sì, secondo giro» e «continua pure a fare il giro dove serve, non fermarti») ───
# Le file di 3-4 tile Immagine messe a mano dove le foto sono una raccolta (le quinte, le camere, le esperienze, i
# pigmenti, l'archivio, gli ospiti da adottare, le stampe, i lavori) diventano una Pro Gallery, con le foto nuove di Pexels
# e testi scritti per il tema. Le file di foto che fanno solo da pausa restano com'erano.
PROGETTI.update({
    # Il teatro: una sera vista dalle quinte, dalle 9.30 del mattino al sipario delle 21, con l'ora sulla linea
    'teatro-darsena': [{'file': 'teatro.json', 'riga': 'teatro-darsena-teatro-x-17', 'voci': [
        foto(29858811, 'Il palco vuoto con le scale e le attrezzature per montare le scene', 'Il palco vuoto', subtitle='Si montano le scene', nota='09:30', category='Il palco'),
        foto(37127036, 'Una sarta al lavoro fra stoffe e piume nella sartoria del teatro', 'La sartoria', subtitle='Gli ultimi orli ai costumi', nota='15:00', category='La sartoria'),
        foto(4718641, 'Due attori nel camerino, fra i costumi appesi', 'Il camerino', subtitle='Le luci attorno allo specchio', nota='18:30', category='I camerini'),
        foto(6877435, 'Un’attrice si trucca allo specchio del camerino', 'Il trucco', subtitle='Un’ora prima di entrare', nota='19:15', category='I camerini'),
        foto(6895798, 'Un attore ripassa il copione sotto le luci di scena', 'L’ultima lettura', subtitle='Il copione, sotto i fari', nota='20:00', category='Il palco'),
        foto(23092130, 'Un’attrice seduta in quinta davanti al sipario rosso', 'In quinta', subtitle='Il sipario sta per aprirsi', nota='20:55', category='Il palco'),
        foto(8994700, 'La sala col sipario di velluto rosso, vista da un palco', 'Dal palco numero 7', subtitle='Si spengono le luci', nota='21:00', category='La sala')],
        'impostazioni': unisci({
            'layout': 'masonry', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'gap': 20, 'thumb_radius': 0,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.03,
            'entrance': 'fade-up', 'entrance_stagger': 110,
            'scheda': 'sotto', 'scheda_categoria': False, 'zona_nota': 'as', 'scheda_nota_stile': 'linea', 'zona_titolo': 'bs',
            'scheda_icona': '', 'scheda_font': 'heading', 'scheda_titolo_size': 24, 'scheda_peso': '400', 'scheda_titolo_italic': True,
            'scheda_colore': TESTO, 'scheda_accento': P, 'scheda_padding': pad(12, 0, 6, 0),
            'filtri': True, 'filtri_stile': 'sottolineato', 'filtri_riga_sopra': True, 'filtri_colore': TESTO,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutta la sera'},
            tipo('scheda_nota', 'mono', 11, '600', '', 1, P), tipo('scheda_sot', '', 13, '', '', '', TESTO), tipo('filtri', '', 12, '600', 'uppercase', 1.5))}],

    # Il B&B: le camere e la casa in card bianche, il nome in un'etichetta, quante persone ci dormono
    'casa-scogliera': [{'file': 'prenota.json', 'riga': 'casa-scogliera-prenota-x-17', 'voci': [
        foto(24807130, 'La camera Il Faro, col letto e la portafinestra sul terrazzo', 'Il Faro', subtitle='Vista sul promontorio', nota='due persone', category='Le camere'),
        foto(16436925, 'La camera Il Giardino, con la finestra sulle piante', 'Il Giardino', subtitle='Al piano terra, fra i limoni', nota='tre persone', category='Le camere'),
        foto(16116303, 'La camera La Vela, in mansarda, col lucernario aperto sul cielo', 'La Vela', subtitle='Sotto il tetto, con l’abbaino', nota='due persone', category='Le camere'),
        foto(34854573, 'Un bagno con la parete di pietra, il lavabo e la doccia', 'Il bagno in pietra', subtitle='In ogni camera, la doccia grande', category='La casa'),
        foto(36371415, 'Un limone in vaso accanto a una porta azzurra', 'I limoni sulla porta', subtitle='Il giardino, a maggio', category='La casa'),
        foto(35517713, 'La terrazza di pietra coi limoni in vaso e le persiane azzurre', 'La terrazza', subtitle='La colazione, all’ombra', category='La casa')],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'aspect_ratio': '4/3', 'gap': 18, 'thumb_radius': 12,
            'lightbox': True, 'lightbox_thumbs': 'none', 'lightbox_animation': 'fade', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.05,
            'entrance': 'fade-up', 'entrance_stagger': 90,
            'scheda': 'sotto', 'scheda_sfondo': LUCE, 'scheda_padding': pad(16, 18, 18, 18), 'scheda_categoria': True,
            'scheda_stile_categoria': 'chiaro', 'zona_categoria': 'as', 'zona_nota': 'ad', 'scheda_nota_stile': 'etichetta',
            'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_font': 'heading', 'scheda_titolo_size': 22, 'scheda_peso': '400',
            'scheda_colore': TESTO, 'scheda_accento': P,
            'filtri': True, 'filtri_stile': 'pillole', 'filtri_forma': 'pillola', 'filtri_colore': P,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutto'},
            tipo('scheda_meta', '', 11, '600', 'uppercase', 1), tipo('scheda_sot', '', 13, '', '', '', TESTO), tipo('filtri', '', 13, '500'))}],

    # Il lodge al Circolo Polare: una giornata dalle 10 all'una di notte, coi filtri Di giorno e Di notte
    'kaamos': [{'file': 'esperienze.json', 'riga': 'kaamos-esperienze-17', 'voci': [
        foto(36152821, 'Una slitta trainata dagli husky su un sentiero nel bosco innevato', 'Con gli husky', subtitle='Due ore nel bosco innevato', nota='10:00', category='Di giorno'),
        foto(3835754, 'Una renna nella neve, col muso coperto di fiocchi', 'Le renne', subtitle='La fattoria dei pastori sami', nota='13:30', category='Di giorno'),
        foto(6803600, 'Due persone brindano dentro una cabina di legno col tetto a punta', 'La merenda in cabina', subtitle='Il vino caldo e la neve fuori', nota='16:00', category='Di giorno'),
        foto(32504776, 'Un uomo nella sauna di legno illuminata, dietro la vetrata', 'La sauna', subtitle='Poi un tuffo nella neve', nota='19:00', category='Di notte'),
        foto(36245503, 'Una tenda illuminata nella neve sotto l’aurora boreale verde', 'La kota', subtitle='La tenda dei sami, sotto l’aurora', nota='22:30', category='Di notte'),
        foto(23939046, 'Una persona cammina nel buio sotto un’aurora rosa e verde', 'A caccia di aurore', subtitle='Quando il cielo diventa rosa', nota='01:00', category='Di notte')],
        'impostazioni': unisci({
            'layout': 'masonry', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'gap': 14, 'thumb_radius': 10,
            'lightbox': True, 'lightbox_thumbs': 'left', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04,
            'entrance': 'blur-in', 'entrance_stagger': 120,
            'scheda': 'sopra', 'scheda_velo': 86, 'scheda_velo_alto': 60, 'scheda_categoria': True, 'scheda_stile_categoria': 'testo',
            'zona_nota': 'as', 'scheda_nota_stile': 'linea', 'zona_categoria': 'ad', 'zona_titolo': 'bs', 'scheda_icona': '',
            'scheda_titolo_size': 21, 'scheda_peso': '600', 'scheda_colore': LUCE, 'scheda_accento': P,
            'filtri': True, 'filtri_stile': 'contorno', 'filtri_forma': 'squadrata', 'filtri_colore': P,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutta la giornata'},
            tipo('scheda_meta', 'mono', 9, '600', 'uppercase', 1.5, LUCE), tipo('scheda_nota', 'mono', 11, '600', '', 1, P),
            tipo('scheda_sot', '', 12, '', '', '', LUCE), tipo('filtri', 'mono', 11, '600', 'uppercase', 1.5))}],

    # La bottega dei pigmenti: ogni card nel colore del suo pigmento, il nome e a cosa serve
    'calce-pigmento': [{'file': 'i-pigmenti.json', 'riga': 'calce-pigmento-i-pigmenti-21', 'voci': [
        foto(32444178, 'Barattoli di vetro pieni di pigmenti colorati su uno scaffale di legno', 'La parete dei barattoli', subtitle='Quaranta terre e ossidi, a peso', category='In bottega', colore='#ddd0b6'),
        foto(13013813, 'Pigmenti naturali in ciotole e barattoli con gli attrezzi, su un tavolo di legno', 'Prima della calce', subtitle='Si pesano e si setacciano a mano', category='In bottega', colore='#e6dac4'),
        foto(33554810, 'Una ciotola colma di pigmento giallo ocra', 'Ocra gialla', subtitle='La terra del sole, per le facciate', category='Terre', colore='#ecca78'),
        foto(40701182, 'Pigmento di terra rossa in polvere, da vicino', 'Terra rossa', subtitle='Cotta, per gli ingressi caldi', category='Terre', colore='#e0a07c'),
        foto(31280796, 'Sette coppette di pigmenti in polvere: ocra, aranci e rossi attorno a un bianco', 'La tavolozza delle terre', subtitle='Sette tinte che si mescolano fra loro', category='Terre', colore='#dcb994'),
        foto(15278967, 'Pigmento blu oltremare in un sacco, con una paletta arancione', 'Blu oltremare', subtitle='Il blu dei soffitti di una volta', category='Blu', colore='#a9bbe9'),
        foto(4618506, 'Un mucchietto di pigmento blu su un foglio giallo', 'Blu su ocra', subtitle='La prova di un colore nuovo', category='Blu', colore='#bccaec')],
        'impostazioni': unisci({
            'layout': 'masonry', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'gap': 16, 'thumb_radius': 2,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '2', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04,
            'entrance': 'fade-up', 'entrance_stagger': 90,
            'scheda': 'sotto', 'scheda_sfondo_voce': True, 'scheda_padding': pad(16, 18, 18, 18), 'scheda_categoria': True,
            'scheda_stile_categoria': 'testo', 'zona_categoria': 'as', 'scheda_numero': True, 'scheda_numero_prefisso': 'N. ',
            'zona_numero': 'ad', 'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_font': 'heading', 'scheda_titolo_size': 22,
            'scheda_peso': '500', 'scheda_colore': SCURO,
            'filtri': True, 'filtri_stile': 'bordate', 'filtri_colore': TESTO, 'filtri_conteggi_stile': 'accanto',
            'filtri_tutti': 'Tutta la tavolozza'},
            tipo('scheda_meta', 'mono', 10, '600', 'uppercase', 1.5), tipo('scheda_sot', '', 13), tipo('filtri', '', 12, '600'))}],

    # Il laboratorio di cementine: l'archivio come un pavimento, quadrati senza spazi divisi da una linea sottile, il nome
    # del disegno e l'anno al passaggio
    'cementina': [{'file': 'collezioni.json', 'riga': 'cementina-collezioni-27', 'voci': [
        foto(18764945, 'Un pavimento di cementine verdi e rosse a fiori', 'Zagara', nota='1971', category='Pavimenti'),
        foto(15703818, 'Cementine a quadri rossi e grigi in una cornice di greche', 'Quattro Canti', nota='1974', category='Pavimenti'),
        foto(30617002, 'Piastrelle bianche e blu con figure dipinte', 'Delft', nota='1978', category='Pareti'),
        foto(31595229, 'Un pannello di piastrelle dipinte con una nave a vele spiegate', 'Il vascello', nota='1981', category='Pareti'),
        foto(5769593, 'Cementine arancio e azzurre con un disegno geometrico', 'Ballarò', nota='1983', category='Pavimenti'),
        foto(31530645, 'Pareti rivestite di piastrelle blu in una sala con le volte', 'La sala grande', nota='1989', category='Pareti'),
        foto(32021732, 'Cementine di disegni diversi accostate come una toppa', 'Scampoli', nota='1996', category='Pavimenti'),
        foto(17904851, 'Piatti di maiolica siciliana dipinti di giallo e blu', 'Maioliche', nota='2004', category='Ispirazioni')],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 2, 'aspect_ratio': '1/1', 'gap': 0, 'thumb_radius': 0,
            'griglia_linee': True, 'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '2',
            'hover_effect': 'zoom', 'hover_zoom_scale': 1.07, 'entrance': 'fade-scale', 'entrance_stagger': 70,
            'scheda': 'sopra', 'scheda_quando': 'passaggio', 'scheda_velo': 78, 'scheda_velo_alto': 55, 'scheda_categoria': True,
            'scheda_stile_categoria': 'testo', 'zona_categoria': 'as', 'zona_nota': 'ad', 'scheda_nota_stile': 'etichetta',
            'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_font': 'heading', 'scheda_titolo_size': 22, 'scheda_peso': '500',
            'scheda_colore': LUCE,
            'filtri': True, 'filtri_stile': 'semplice', 'filtri_forma': 'squadrata', 'filtri_colore': P,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutto l’archivio'},
            tipo('scheda_meta', 'mono', 10, '600', 'uppercase', 1.5, LUCE), tipo('filtri', '', 13, '600'))}],

    # Il gattile: gli ospiti da adottare, il nome, il carattere, l'età in un'etichetta, la card che porta alla visita
    'cuccia': [{'file': 'adotta.json', 'riga': 'cuccia-adotta-25', 'voci': [
        foto(6900232, 'Un gattino grigio a pelo lungo seduto su un cuscino rosso', 'Briciola', subtitle='Curiosa di tutto, dorme sui cuscini', nota='3 mesi', category='Cuccioli', link='{{PAGINA:vieni-a-trovarci}}'),
        foto(37220239, 'Un gattino color crema con gli occhi azzurri', 'Panna', subtitle='Fa le fusa appena la prendi in braccio', nota='4 mesi', category='Cuccioli', link='{{PAGINA:vieni-a-trovarci}}'),
        foto(34093718, 'Un gatto tigrato e bianco che guarda dritto', 'Tobia', subtitle='Tranquillo, va d’accordo coi cani', nota='3 anni', category='Adulti', link='{{PAGINA:vieni-a-trovarci}}'),
        foto(12510164, 'Un gatto rosso seduto, con gli occhi ambra', 'Rosso', subtitle='Un signore: vuole una casa silenziosa', nota='11 anni', category='Adulti', link='{{PAGINA:vieni-a-trovarci}}'),
        foto(30626722, 'Un gatto tigrato sdraiato su una coperta', 'Tartufo', subtitle='Pigro e goloso, ama il sole', nota='5 anni', category='Adulti', link='{{PAGINA:vieni-a-trovarci}}'),
        foto(16609367, 'Un gatto grigio con gli occhi verdi su un fondo blu', 'Nebbia', subtitle='Timida i primi giorni, poi non ti lascia più', nota='2 anni', category='Adulti', link='{{PAGINA:vieni-a-trovarci}}'),
        foto(39453193, 'Un gatto nero con gli occhi gialli, da vicino', 'Ombra', subtitle='Occhi gialli e tanta pazienza', nota='4 anni', category='Adulti', link='{{PAGINA:vieni-a-trovarci}}'),
        foto(31735117, 'Un gatto bianco e nero con gli occhi verdi', 'Frac', subtitle='Elegante, chiacchierone, gioca con tutto', nota='6 anni', category='Adulti', link='{{PAGINA:vieni-a-trovarci}}')],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 2, 'aspect_ratio': '4/5', 'object_position': 'center 30%',
            'gap': 18, 'thumb_radius': 20, 'lightbox': False, 'hover_effect': 'zoom', 'hover_zoom_scale': 1.05,
            'entrance': 'land', 'entrance_stagger': 80,
            'scheda': 'sotto', 'scheda_categoria': False, 'zona_nota': 'ad', 'scheda_nota_stile': 'etichetta', 'zona_titolo': 'bs',
            'scheda_icona': 'destra', 'scheda_font': 'heading', 'scheda_titolo_size': 22, 'scheda_titolo_adatta': True,
            'scheda_peso': '600', 'scheda_colore': TESTO, 'scheda_accento': P, 'scheda_padding': pad(12, 4, 6, 4),
            'filtri': True, 'filtri_stile': 'pillole', 'filtri_forma': 'pillola', 'filtri_colore': P,
            'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutti i gatti'},
            tipo('scheda_nota', '', 11, '700'), tipo('scheda_sot', '', 13, '', '', '', TESTO), tipo('filtri', '', 13, '600'))}],

    # La stamperia di foulard: dalla foto al foulard su una pellicola vera, col bordo arancio dei negativi a colori
    'luce-rossa': [{'file': 'camera-oscura.json', 'riga': 'luce-rossa-camera-oscura-37', 'voci': [
        foto(10276253, 'Strisce di negativi a colori, controluce', 'Il negativo', subtitle='Si sceglie il fotogramma', category='In camera oscura'),
        foto(27772364, 'Una camera oscura illuminata di rosso, con l’ingranditore e le bacinelle', 'La luce rossa', subtitle='L’ingranditore è acceso', category='In camera oscura'),
        foto(8114084, 'Una stampa che affiora nella bacinella sotto la luce rossa', 'Nella bacinella', subtitle='L’immagine affiora in un minuto', category='In camera oscura', segno='cerchio'),
        foto(10273414, 'Pellicole appese ad asciugare su un fondo rosso', 'Ad asciugare', subtitle='Le pellicole appese alle mollette', category='In camera oscura'),
        foto(8114115, 'Un uomo appende le stampe in camera oscura', 'La scelta', subtitle='Le prove appese al filo', category='In camera oscura'),
        foto(4337155, 'Tre stampe in bianco e nero appese con le mollette', 'Tre prove', subtitle='Ne resta una sola', category='In camera oscura'),
        foto(8752421, 'Un tessuto di seta stampato a fiori, mosso', 'Sulla seta', subtitle='La foto diventa disegno', category='Sulla seta'),
        foto(36455708, 'Un foulard di seta stampato in arancio, blu e oro', 'Il foulard finito', subtitle='Novanta centimetri, orli a mano', category='Sulla seta')],
        'impostazioni': unisci({
            'layout': 'strip', 'strip_height': 400, 'strip_item_width': 300, 'strip_arrows': True, 'strip_arrows_style': 'circle',
            'strip_arrows_bg': LUCE, 'strip_arrows_color': SCURO, 'strip_arrows_size': 40, 'strip_fade_edges': True,
            'strip_avanzamento': True, 'gap': 0, 'thumb_radius': 0, 'lightbox': True, 'hover_effect': 'none',
            'frame': 'pellicola', 'frame_color': A, 'pellicola_primo': 21, 'pellicola_passo': 1,
            'scheda': 'sotto', 'scheda_categoria': False, 'scheda_icona': '', 'scheda_titolo_size': 17, 'scheda_peso': '600',
            'scheda_colore': TESTO, 'filtri': True, 'filtri_stile': 'bordate', 'filtri_conteggi_stile': 'accanto',
            'filtri_colore': TESTO, 'filtri_tutti': 'Dalla foto al foulard'},
            tipo('scheda_sot', 'mono', 11, '', '', '', TESTO), tipo('filtri', '', 12, '600'))}],

    # La tappezzeria: i lavori nel velluto, il filo d'oro come un cordoncino, il numero fra parentesi, e la poltrona
    # com'era prima che arrivasse in bottega
    'capitonne': [{'file': 'lavori.json', 'riga': 'capitonne-lavori-40', 'voci': [
        foto(20212637, 'Una vecchia poltrona a fiori con la seduta strappata, in strada', 'Com’era arrivata', subtitle='Una bergère con la seduta sfondata', category='Prima'),
        foto(10567019, 'Una bergère in velluto bordeaux capitonné accanto a un tavolino di marmo', 'Bergère bordeaux', subtitle='Capitonné a mano, bottoni ricoperti', category='Poltrone e sedie'),
        foto(39351124, 'Due poltrone in velluto rosa antico e una panca col kilim', 'Le poltrone del salotto', subtitle='Velluto rosa antico e un kilim', category='Poltrone e sedie'),
        foto(2894961, 'Una poltroncina con la seduta in gobelin a fiori e il legno lucidato', 'La poltroncina a fiori', subtitle='Gobelin rifatto, legno a cera', category='Poltrone e sedie'),
        foto(3764576, 'Un divano Chesterfield chiaro in una stanza luminosa', 'Il Chesterfield', subtitle='Tre posti, imbottitura in crine', category='Divani e testate'),
        foto(7535044, 'Un letto con la testata trapuntata a rombi in velluto blu e la panca', 'La testata blu', subtitle='Rombi trapuntati, la panca coordinata', category='Divani e testate'),
        foto(15758184, 'Un cuscino capitonné in velluto chiaro, da vicino', 'Il capitonné', subtitle='Le pieghe tirate a mano', category='Dettagli'),
        foto(6394545, 'Una sedia in velluto bordeaux accanto a un tavolino d’ottone', 'La sedia in velluto', subtitle='Bordeaux e ottone', category='Poltrone e sedie')],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 1, 'aspect_ratio': '3/4', 'gap': 12, 'thumb_radius': 2,
            'frame': 'filo', 'frame_color': P, 'frame_spessore': 1,
            'lightbox': True, 'lightbox_thumbs': 'right', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04,
            'entrance': 'fade-scale', 'entrance_stagger': 90,
            'scheda': 'sopra', 'scheda_velo': 84, 'scheda_velo_alto': 58, 'scheda_numero': True, 'scheda_numero_prefisso': '(',
            'scheda_numero_suffisso': ')', 'zona_numero': 'as', 'scheda_categoria': True, 'scheda_stile_categoria': 'testo',
            'zona_categoria': 'ad', 'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_font': 'heading', 'scheda_titolo_size': 21,
            'scheda_peso': '400', 'scheda_titolo_italic': True, 'scheda_colore': LUCE, 'scheda_accento': P,
            'filtri': True, 'filtri_stile': 'sottolineato', 'filtri_colore': P, 'filtri_conteggi_stile': 'accanto',
            'filtri_tutti': 'Tutti i lavori'},
            tipo('scheda_meta', 'mono', 9, '600', 'uppercase', 1.5, P), tipo('scheda_sot', '', 12, '', '', '', LUCE),
            tipo('filtri', '', 12, '600', 'uppercase', 1.5))}],
})


# ─── Terzo giro (24 nov 2026, «continua pure a fare il giro dove serve»): altre file di foto che sono raccolte ───
PROGETTI.update({
    # Il marchio di basi lavate cento volte: il lookbook coi codici dei look davanti al titolo e la lente
    'trentagradi': [{'file': 'i-capi.json', 'riga': 'trentagradi-i-capi-32', 'voci': [
        foto(39915457, 'Un ragazzo con la polo nera seduto su uno sgabello', 'La polo nera', subtitle='Piqué pesante · sopra', nota='L·01'),
        foto(8217366, 'Una ragazza con la maglietta bianca e il cappello da pescatore', 'La maglietta bianca', subtitle='Cotone 220 g · sopra', nota='L·02'),
        foto(4456780, 'Una ragazza con la camicia bianca aperta e i pantaloni neri', 'La camicia aperta', subtitle='Popeline · sopra e sotto', nota='L·03'),
        foto(39809934, 'Una ragazza con la camicia rossa leggera, all’aperto', 'La camicia rossa', subtitle='Viscosa · sopra', nota='L·04'),
        foto(5149729, 'Un ragazzo con la camicia di lino chiara davanti a una tenda', 'La camicia di lino', subtitle='Lino lavato · sopra e sotto', nota='L·05'),
        foto(4319139, 'Un ragazzo con la maglietta bianca larga e gli occhiali da sole', 'La maglietta larga', subtitle='Taglio ampio · sopra', nota='L·06'),
        foto(18153493, 'Un ragazzo con la camicia a maniche corte, in bianco e nero', 'La camicia corta', subtitle='Cotone e lino · sopra', nota='L·07'),
        foto(40183053, 'Una camicia di lino bianca piegata, con gli occhiali da sole sopra', 'Dopo cento lavaggi', subtitle='La stessa camicia, ancora bianca', nota='L·08')],
        'impostazioni': unisci({
            # griglia, non masonry: i codici dei look si leggono in ordine, riga per riga
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 2, 'aspect_ratio': '3/4', 'gap': 18, 'thumb_radius': 0, 'shadow': 'sm',
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04,
            'entrance': 'fade-up', 'entrance_stagger': 80,
            'scheda': 'sotto', 'scheda_categoria': False, 'zona_nota': 'pt', 'zona_titolo': 'bs', 'scheda_icona': 'lente', 'zona_icona': 'bd',
            'scheda_titolo_size': 16, 'scheda_titolo_adatta': True, 'scheda_peso': '600', 'scheda_colore': TESTO, 'scheda_padding': pad(12, 2, 4, 2)},
            tipo('scheda_nota', 'mono', 10, '700', 'uppercase', 1, P), tipo('scheda_sot', 'mono', 10, '', '', '', TESTO))}],

    # L'hammam: le quattro sale del rituale, due foto per sala, la temperatura sulla linea, i filtri per sala
    'tepidario': [{'file': 'il-rituale.json', 'riga': 'tepidario-il-rituale-vp-9', 'voci': [
        foto(7598373, 'Una sala del vapore rivestita di mosaico d’oro, con le panche di marmo', 'Il tepidario', subtitle='Il caldo che prepara', nota='38 °C', category='Il tepidario'),
        foto(15528970, 'Un hammam di marmo bianco con la pietra calda ottagonale al centro', 'La pietra calda', subtitle='Ci si sdraia, si respira', nota='38 °C', category='Il tepidario'),
        foto(3079492, 'Una sala a cupola illuminata di verde, con le arcate', 'Il calidario', subtitle='Il vapore sotto la cupola', nota='45 °C', category='Il calidario'),
        foto(38911900, 'Una stanza di pietra con gli archi e i lucernari a stella', 'I lucernari a stella', subtitle='La luce che entra dalla volta', nota='45 °C', category='Il calidario'),
        foto(12940660, 'Una donna avvolta nell’asciugamano con la maschera di argilla sul viso', 'Il gommage', subtitle='Argilla e guanto di crine', nota='40 °C', category='Il gommage'),
        foto(15528971, 'Il rubinetto di rame che versa l’acqua in una vasca di marmo', 'La kurna', subtitle='L’acqua tiepida sul marmo', nota='40 °C', category='Il gommage'),
        foto(7391598, 'Una sala con le lanterne, il soffitto di legno e le maioliche', 'Il riposo', subtitle='Il tè alla menta, senza fretta', nota='24 °C', category='Il riposo'),
        foto(15528975, 'Asciugamani piegati, un secchio di rame e le candele accese', 'Gli asciugamani caldi', subtitle='Pronti all’uscita', nota='24 °C', category='Il riposo')],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 1, 'aspect_ratio': '4/5', 'gap': 14, 'thumb_radius': 0,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '2', 'lightbox_animation': 'fade',
            'hover_effect': 'zoom', 'hover_zoom_scale': 1.04, 'entrance': 'fade-scale', 'entrance_stagger': 90,
            # il velo in alto più scuro: le temperature si leggono anche sul mosaico d'oro e sul marmo bianco
            'scheda': 'sopra', 'scheda_velo': 84, 'scheda_velo_alto': 80, 'scheda_categoria': True, 'scheda_stile_categoria': 'testo',
            'zona_categoria': 'ad', 'zona_nota': 'as', 'scheda_nota_stile': 'linea', 'zona_titolo': 'bs', 'scheda_icona': '',
            'scheda_font': 'heading', 'scheda_titolo_size': 22, 'scheda_peso': '400', 'scheda_titolo_italic': True, 'scheda_colore': LUCE,
            'scheda_accento': P,
            'filtri': True, 'filtri_stile': 'sottolineato', 'filtri_colore': P, 'filtri_conteggi_stile': 'accanto',
            'filtri_tutti': 'Tutto il rituale'},
            tipo('scheda_meta', 'mono', 9, '600', 'uppercase', 1.5, LUCE), tipo('scheda_nota', 'mono', 11, '600', '', 1, A),
            tipo('scheda_sot', '', 12, '', '', '', LUCE), tipo('filtri', '', 12, '600', 'uppercase', 1.5))}],

    # Il cuoco senza ristorante: i luoghi delle cene col nome del posto, la zona e il mese della tappa
    'tavola-errante': [{'file': 'le-cene.json', 'riga': 'tavola-errante-le-cene-25', 'voci': [
        foto(17086146, 'Un giardino con la pergola e il pavimento di pietra, fra gli ulivi', 'Una villa sui colli', subtitle='Colli Euganei', nota='giugno', category='Giardini'),
        foto(39450891, 'Un tavolo apparecchiato in mezzo a un prato, al tramonto', 'Una radura sul lago', subtitle='Sila', nota='luglio', category='All’aperto'),
        foto(19987294, 'Il cortile di un vecchio maso coi ballatoi di legno', 'Un rifugio', subtitle='Dolomiti', nota='agosto', category='Case'),
        foto(33966954, 'Un tavolo con la pizza e il vino bianco davanti alle vigne', 'Una cascina in collina', subtitle='Langhe', nota='settembre', category='All’aperto'),
        foto(36989618, 'Un uliveto con un tavolo di pietra, le botti e un furgoncino azzurro', 'Un uliveto', subtitle='Val d’Orcia', nota='settembre', category='All’aperto'),
        foto(33927685, 'Una tavola lunga sotto un portico di pietra ad archi', 'Una corte di mattoni', subtitle='Bologna', nota='ottobre', category='Case'),
        foto(32179374, 'Una tavola lunga apparecchiata coi fiori in un giardino, di sera', 'Una tavola per sessanta', subtitle='Ogni tappa, la stessa tavola', category='Giardini')],
        'impostazioni': unisci({
            'layout': 'masonry', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'gap': 16, 'thumb_radius': 4,
            'lightbox': True, 'lightbox_thumbs': 'right', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04,
            'entrance': 'blur-in', 'entrance_stagger': 100,
            'scheda': 'sopra', 'scheda_velo': 82, 'scheda_velo_alto': 50, 'scheda_categoria': False, 'zona_nota': 'as',
            'scheda_nota_stile': 'etichetta', 'zona_titolo': 'bs', 'scheda_icona': 'freccia', 'zona_icona': 'bd',
            'scheda_font': 'heading', 'scheda_titolo_size': 24, 'scheda_peso': '400', 'scheda_colore': LUCE, 'scheda_accento': P,
            'filtri': True, 'filtri_stile': 'bordate', 'filtri_colore': P, 'filtri_conteggi_stile': 'accanto', 'filtri_tutti': 'Tutti i luoghi'},
            tipo('scheda_nota', 'mono', 10, '600', 'uppercase', 1.5), tipo('scheda_sot', 'mono', 11, '', 'uppercase', 1.5, LUCE),
            tipo('filtri', '', 12, '600'))}],

    # Il noleggio per il cinema: gli scaffali in ordine, la sigla dello scaffale, il tipo di pezzo, i filtri gialli ciak
    'ciak': [{'file': 'catalogo.json', 'riga': 'ciak-catalogo-36', 'voci': [
        foto(13828234, 'Una cinepresa smontata con le ottiche, le batterie e i cavi in ordine su un fondo blu', 'Il kit, prima di uscire', subtitle='Contato pezzo per pezzo', nota='A-01', category='Cineprese'),
        foto(14830216, 'Corpi macchina allineati su uno scaffale illuminato', 'I corpi macchina', subtitle='Provati il giorno prima', nota='A-04', category='Cineprese'),
        foto(39579120, 'Una cinepresa da cinema con l’ottica montata, da vicino', 'La 12K', subtitle='Con la gabbia e il monitor', nota='A-07', category='Cineprese'),
        foto(32822409, 'Ottiche cine allineate su un ripiano di legno illuminato', 'La serie di fissi', subtitle='Dal 18 al 135 mm', nota='B-02', category='Ottiche'),
        foto(2873486, 'Una cinepresa sul cavalletto fra le luci di un set', 'Pronta sul cavalletto', subtitle='Testa fluida e piastra', nota='B-05', category='Grip'),
        foto(11748183, 'Una cinepresa sul cavalletto in uno studio col muro di mattoni', 'In teatro di posa', subtitle='Luci e cavalletti compresi', nota='C-01', category='Grip'),
        foto(32183310, 'Scaffali di legno con le macchine fotografiche d’epoca', 'Lo scaffale d’epoca', subtitle='Per i film in costume', nota='D-03', category='Vintage'),
        foto(31912615, 'Una stanza con gli scaffali di vecchie macchine e una lampada', 'La stanza del vintage', subtitle='Super 8 e radio a valvole', nota='D-06', category='Vintage')],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 1, 'aspect_ratio': '4/3', 'gap': 12, 'thumb_radius': 2,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.05,
            'entrance': 'slide-in', 'entrance_stagger': 70,
            'scheda': 'sotto', 'scheda_categoria': True, 'scheda_stile_categoria': 'testo', 'zona_categoria': 'as', 'zona_nota': 'ad',
            'scheda_nota_stile': 'etichetta', 'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_titolo_size': 17, 'scheda_peso': '700',
            'scheda_colore': TESTO, 'scheda_accento': P, 'scheda_linea_sotto': True, 'scheda_padding': pad(12, 0, 12, 0),
            'filtri': True, 'filtri_stile': 'contorno', 'filtri_forma': 'squadrata', 'filtri_colore': P, 'filtri_conteggi_stile': 'accanto',
            'filtri_tutti': 'Tutto il magazzino'},
            tipo('scheda_meta', 'mono', 10, '600', 'uppercase', 1.5, P), tipo('scheda_nota', 'mono', 10, '700', 'uppercase', 1),
            tipo('scheda_sot', 'mono', 11, '', '', '', TESTO), tipo('filtri', 'mono', 11, '600', 'uppercase', 1.5))}],

    # Il museo delle illusioni: al passaggio le altre foto si sfocano, come quando l'occhio si inganna
    'abbaglio': [{'file': 'le-sale.json', 'riga': 'abbaglio-le-sale-22', 'voci': [
        foto(2889489, 'Una stanza buia piena di lampadine riflesse all’infinito negli specchi', 'Gli specchi infiniti', subtitle='Mille lampadine che sono una', category='Specchi'),
        foto(7724506, 'Cornici di luce che si ripetono in un tunnel che sembra girare', 'Il tunnel che gira', subtitle='Il pavimento è dritto', category='Movimento'),
        foto(39240990, 'Una parete verde coperta di specchi con le cornici dorate', 'La parete degli specchi', subtitle='Quale sei tu?', category='Specchi'),
        foto(16946958, 'Un uomo davanti a una parete di luci geometriche', 'La sala op art', subtitle='Righe ferme che si muovono', category='Movimento'),
        foto(6727760, 'Una stanza bianca con quadrati di luce sospesi uno dentro l’altro', 'La stanza di luce', subtitle='La sala 6', category='Luce'),
        foto(1502800, 'Uno specchio rotondo che ripete la stanza in una spirale', 'Lo specchio a spirale', subtitle='Ci si vede sempre più piccoli', category='Specchi'),
        foto(19035844, 'Un uomo in piedi in mezzo a fili di luce azzurra', 'La pioggia di luce', subtitle='Si cammina fra le gocce', category='Luce'),
        foto(9407241, 'Un corridoio rosso con una porta che si accende', 'Il corridoio rosso', subtitle='Più lungo di quanto sembri', category='Luce')],
        'impostazioni': unisci({
            'layout': 'grid', 'columns': 4, 'tablet_columns': 2, 'mobile_columns': 2, 'aspect_ratio': '3/4', 'gap': 8, 'thumb_radius': 0,
            'lightbox': True, 'lightbox_thumbs': 'none', 'lightbox_animation': 'scale', 'hover_effect': 'blur-peers',
            'entrance': 'zoom-center', 'entrance_stagger': 80,
            'scheda': 'sopra', 'scheda_velo': 84, 'scheda_velo_alto': 55, 'scheda_categoria': True, 'scheda_stile_categoria': 'testo',
            'zona_categoria': 'as', 'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_titolo_size': 18, 'scheda_titolo_adatta': True,
            'scheda_peso': '700', 'scheda_colore': LUCE, 'scheda_accento': P,
            'filtri': True, 'filtri_stile': 'contorno', 'filtri_forma': 'squadrata', 'filtri_colore': P, 'filtri_conteggi_stile': 'accanto',
            'filtri_tutti': 'Tutte le sale'},
            tipo('scheda_meta', 'mono', 10, '700', 'uppercase', 1.5, P), tipo('scheda_sot', '', 12, '', '', '', LUCE),
            tipo('filtri', 'mono', 11, '600', 'uppercase', 1.5))}],

    # Il rifugio a 2.104 metri: dentro e fuori, con l'etichetta da cartello (quota o tempo di cammino) e i filtri Dentro e Fuori
    'rifugio-larice': [{'file': 'rifugio.json', 'riga': 'rifugio-larice-rifugio-x-17', 'voci': [
        foto(16543351, 'Una fetta di strudel su un piatto, con la panna', 'Lo strudel', subtitle='Mele della valle, ogni mattina', nota='dalle 10', category='Dentro'),
        foto(39626992, 'Una stufa di ghisa accesa con una pentola sopra, nella penombra', 'La stufa', subtitle='Accesa da sessant’anni', nota='2.104 m', category='Dentro'),
        foto(18074507, 'Una baita di legno su un prato coi monti dietro', 'La baita del fieno', subtitle='Dieci minuti sotto il rifugio', nota='10 min', category='Fuori'),
        foto(31288562, 'Una baita di pietra fra i larici gialli d’autunno', 'Fra i larici', subtitle='Il sentiero del lago', nota='45 min', category='Fuori'),
        foto(5227965, 'Un sentiero nel bosco di larici e abeti in autunno', 'Il sentiero 610', subtitle='Segnavia bianco e rosso', nota='1 h 30', category='Fuori'),
        foto(14273963, 'Un bosco di larici dorati visto dall’alto', 'Il bosco d’oro', subtitle='A ottobre, dal crinale', nota='2 h', category='Fuori'),
        foto(34741634, 'Un palo coi cartelli gialli dei sentieri davanti alla valle', 'Il bivio', subtitle='Tre sentieri, tre cime', nota='2.230 m', category='Fuori')],
        'impostazioni': unisci({
            'layout': 'masonry', 'columns': 3, 'tablet_columns': 2, 'mobile_columns': 1, 'gap': 16, 'thumb_radius': 4,
            'lightbox': True, 'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.04,
            'entrance': 'land', 'entrance_stagger': 90,
            'scheda': 'sotto', 'scheda_categoria': True, 'scheda_stile_categoria': 'pieno', 'scheda_accento': P, 'zona_categoria': 'as',
            'zona_nota': 'ad', 'scheda_nota_stile': 'etichetta', 'zona_titolo': 'bs', 'scheda_icona': '', 'scheda_font': 'heading',
            'scheda_titolo_size': 24, 'scheda_peso': '700', 'scheda_colore': TESTO, 'scheda_padding': pad(12, 0, 8, 0),
            'filtri': True, 'filtri_stile': 'semplice', 'filtri_forma': 'squadrata', 'filtri_colore': S, 'filtri_conteggi_stile': 'accanto',
            'filtri_tutti': 'Dentro e fuori'},
            tipo('scheda_meta', '', 10, '700', 'uppercase', 1.5), tipo('scheda_nota', 'mono', 10, '700', 'uppercase', 1),
            tipo('scheda_sot', '', 13, '', '', '', TESTO), tipo('filtri', '', 12, '700', 'uppercase', 1.5))}],
})


def _trova(nodi, id_):
    for n in nodi:
        if n.get('id') == id_:
            return n
        x = _trova(n.get('children') or [], id_)
        if x is not None:
            return x
    return None


def applica(tile, pr):
    """La tile diventa la Pro Gallery del progetto: stesse foto (coi testi del progetto), o i prodotti della Sorgente dinamica."""
    vecchie = (tile.get('settings') or {}).get('images') or []
    s = dict(pr['impostazioni'])
    if 'dinamica' in pr:
        s['images'] = []
        tile['dynamic'] = pr['dinamica']
    else:
        voci = pr.get('voci') or []
        assert len(vecchie) == len(voci), (tile['id'], len(vecchie), len(voci))
        foto = []
        for img, v in zip(vecchie, voci):
            x = {k: img[k] for k in ('id', 'url', 'alt', 'caption', 'subtitle', 'text', 'category') if k in img and img[k] not in ('', None)}
            x.update(v)
            foto.append(x)
        s['images'] = foto
    tile['type'] = 'progallery'
    tile['settings'] = s


def applica_riga(riga, pr):
    """Secondo giro: una fila di tile Immagine (una per colonna, una galleria fatta a mano) diventa una riga sola con la
    Pro Gallery del progetto. Le foto sono quelle del progetto (le vecchie della fila più quelle nuove), coi testi."""
    if riga.get('children') and riga['children'][0].get('id') == riga['id'] + '-pgc':
        t = riga['children'][0]['children'][0]   # già fatta: si riscrive solo la galleria
    else:
        t = {'id': riga['id'] + '-pg', 'type': 'progallery', 'settings': {}, 'style': {}, 'advanced': {}}
        riga['children'] = [{'id': riga['id'] + '-pgc', 'type': 'column', 'settings': {}, 'style': {}, 'advanced': {}, 'children': [t]}]
    riga.setdefault('settings', {})['layout'] = '100'
    s = dict(pr['impostazioni'])
    s['images'] = [dict({'url': LIB + v['file']}, **{k: x for k, x in v.items() if k != 'file'}) for v in pr['voci']]
    t['settings'] = s
    return t


def media_di(tid):
    """Le foto della libreria che i progetti del tema aggiungono (per il controllo dei media di base.write_theme)."""
    return [v['file'] for pr in PROGETTI.get(tid, []) if 'riga' in pr for v in pr['voci']]


def applica_tema(tid, nome_file, built):
    """Per base.write_theme."""
    nodi = built['content'] if isinstance(built, dict) else built
    for pr in PROGETTI.get(tid, []):
        if pr['file'] == nome_file:
            t = _trova(nodi, pr.get('riga') or pr['id'])
            if t is not None:
                applica_riga(t, pr) if 'riga' in pr else applica(t, pr)


def main():
    sys.stdout.reconfigure(encoding='utf-8')
    free = set(json.load(open(FREE, encoding='utf-8'))['free'])
    scrivi = '--scrivi' in sys.argv
    for tid, prs in PROGETTI.items():
        assert tid not in free, tid + ' è un tema Free: la Pro Gallery su misura va solo nei Pro'
        for pr in prs:
            p = os.path.join(TEMI, tid, pr['file'])
            built = json.load(open(p, encoding='utf-8'))
            nodi = built['content'] if isinstance(built, dict) else built
            if 'riga' in pr:
                r = _trova(nodi, pr['riga'])
                assert r is not None, (tid, pr['riga'])
                t = applica_riga(r, pr)
                print('%-18s %-18s %-34s fila di immagini → progallery (%d foto)' % (tid, pr['file'], pr['riga'], len(t['settings']['images'])))
                if scrivi:
                    open(p, 'w', encoding='utf-8', newline='\n').write(json.dumps(built, ensure_ascii=False, separators=(',', ':')))
                continue
            t = _trova(nodi, pr['id'])
            assert t is not None, (tid, pr['id'])
            prima = t['type']
            applica(t, pr)
            print('%-18s %-18s %-34s %s → progallery (%s)' % (tid, pr['file'], pr['id'], prima,
                  'prodotti' if 'dinamica' in pr else '%d foto' % len(t['settings']['images'])))
            if scrivi:
                open(p, 'w', encoding='utf-8', newline='\n').write(json.dumps(built, ensure_ascii=False, separators=(',', ':')))
    print('scritto' if scrivi else '(prova: --scrivi per scrivere)')


if __name__ == '__main__':
    main()
