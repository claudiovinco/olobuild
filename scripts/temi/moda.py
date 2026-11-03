# -*- coding: utf-8 -*-
"""Pezzi comuni dei dieci marchi di abbigliamento del 3 nov 2026 (Rovescio, Cangiante, A Cipolla, Cerniera, Spaiato,
Trentagradi, Sonnambulo, Scroscio, Gomitolo, Fuorimisura). Sono siti di marchio, non negozi: la collezione, il marchio,
i negozi che lo vendono. I pezzi: il logo vero (un PNG disegnato per il marchio) in testata e nel piede, le schede dei
capi, il dritto e il rovescio, il nastro di scritte, l'elenco dei negozi, la lettera dal marchio.

Uso in un generatore:
    import moda as M
    M.testata(K, t, 'Rovescio', 'logo-rovescio-scuro.png', 190)
"""
from base import mix, radius, solid  # noqa: F401


def logo_tile(K, t, logo, proporzione, larghezza=420, allinea='left', alt=''):
    """Il logo come immagine (contenuto intero, nessun ritaglio), largo al massimo `larghezza` px."""
    return t.classic('image', {
        'image_url': K.img(logo), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
        'aspect_ratio_custom': proporzione, 'height': '', 'object_fit': 'contain', 'object_position': 'left center',
        'max_width': str(larghezza), 'image_alignment': allinea, 'border_radius': radius(0), 'lightbox': False,
        'shadow': 'none', 'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
        style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def testata(K, t, nome, logo, larghezza=170, logo_sticky=None, overlay=True, scuro=True, **extra):
    """La testata del kit col logo disegnato al posto del nome scritto. `logo_sticky`: il logo per la barra che resta in
    cima scorrendo (nei temi chiari la barra ha il fondo chiaro e il logo dell'apertura, chiaro, non si leggerebbe)."""
    sez = K.testata(t, nome, overlay=overlay, scuro=scuro)
    s = sez[0]['children'][0]['children'][0]['children'][0]['settings']
    s.update({'logo_image': K.img(logo), 'logo_text': '', 'logo_width': str(larghezza), 'logo_mark': '', 'logo_dot': False,
              'logo_crest': ''})
    if logo_sticky:
        s['logo_sticky'] = K.img(logo_sticky)
    s.update(extra)
    return sez


def piede(K, t, logo, proporzione, claim, pagine, titolo_terza, terza_html, voci, legale, social, larghezza=460, fondo=None):
    """Il piede del kit col logo vero al posto del marchio scritto in grande."""
    sez = K.piede(t, 'x', claim, pagine, titolo_terza, terza_html, voci, legale, social, fondo=fondo)
    riga = sez[0]['children'][1]
    riga['children'][0]['children'] = [logo_tile(K, t, logo, proporzione, larghezza)]
    return sez


def capi(K, t, voci, link, colonne=4, sfondo=None, testo=None, invito='Dove si trova', proporzione='4/5', raggio=None,
         tinta=None):
    """Le schede dei capi: voci = [(foto, linea, nome, accento, descrizione, prezzo, badge o '')]."""
    c = K.c
    r = K.r if raggio is None else raggio
    items = [{'letter': '', 'top_bg': {'type': 'image', 'image_url': K.img(f), 'image_size': 'cover', 'image_position': 'center center'},
              'brand_label': linea, 'brand_color': tinta or c['acc'], 'show_badge': bool(badge), 'badge_text': badge,
              'badge_bg': tinta or c['acc'], 'badge_color': '#FFFFFF' if tinta else c['on_acc'], 'title': nome,
              'title_accent': accento, 'title_accent_italic': K.corsivo, 'description': desc, 'price': prezzo, 'cta_text': invito,
              'cta_url': link} for f, linea, nome, accento, desc, prezzo, badge in voci]
    return t.classic('product-cards', {
        'items': items, 'columns': colonne, 'gap': 18, 'card_bg': solid(sfondo or c['sala']), 'card_color': testo or c['testo'],
        'card_radius': radius(r), 'card_radius_hover': radius(r), 'card_shadow': 'none', 'card_padding': 18,
        'top_aspect_ratio': proporzione, 'title_font_family': 'heading', 'title_size': 24, 'title_weight': K.peso, 'price_color': tinta or c['acc'],
        'brand_size': 11, 'card_hover_effect': 'lift'}, style={})


def dritto_rovescio(K, t, voci, colonne=3, prima='Dritto', dopo='Rovescio'):
    """Il capo da una parte e dall'altra, col cursore da trascinare: voci = [(foto prima, foto dopo, titolo, testo)]."""
    c = K.c
    return t.classic('beforeafter', {
        'items': [{'before_image': K.img(a), 'after_image': K.img(b), 'before_label': prima, 'after_label': dopo, 'title': ti, 'text': tx}
                  for a, b, ti, tx in voci],
        'columns': colonne, 'gap': 22, 'media_aspect': '4/5', 'accent': c['acc'], 'before_label_color': c['chiaro'],
        'after_label_color': c['on_acc'], 'title_color': c['testo'], 'text_color': c['tenue'], 'card_bg': c['sala'], 'radius': K.r,
        'card_radius': radius(K.r), 'shadow': 'none', 'handle_color': c['chiaro'], 'autoplay': True}, style={})


def nastro(K, t, voci, fondo=None, colore=None, size=46, velocita='34', verso='left', separatore=' · ', altezza=88):
    """Un nastro di scritte che scorre (Marquee) e si inclina con lo scorrimento."""
    c = K.c
    return t.classic('marquee', {
        'text_items': ' | '.join(voci), 'separator': separatore, 'speed': velocita, 'direction': verso, 'bg_color': fondo or c['acc'],
        'text_color': colore or c['on_acc'], 'font_size': size, 'font_weight': '800', 'text_transform': 'uppercase',
        'font_family': 'heading', 'separator_color': colore or c['on_acc'], 'height': altezza, 'velocity_skew': True}, style={})


def negozi(K, t, righe, titolo=('Dove si trova', 'Nei negozi che lo vendono.'), nota=''):
    """L'elenco dei negozi che vendono il marchio: righe = [(città, negozio, indirizzo)] come lista a due colonne."""
    met = (len(righe) + 1) // 2
    col = lambda rr: K.specs(t, [('%s · %s' % (a, b), c) for a, b, c in rr])
    sinistra = [K.sechead(t, titolo[0], [titolo[1]], size=56, layout='stack', size_mobile=36)]
    if nota:
        sinistra.append(K.text(t, nota))
    return t.section([
        t.row([t.col(sinistra, wm='1-3', ws='1-1'), t.col([col(righe[:met])], wm='1-3', ws='1-1'),
               t.col([col(righe[met:])], wm='1-3', ws='1-1')], layout='custom', custom='34,33,33', gap=48, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110})


def lettera(K, t, occhiello, righe, testo, invito='Iscriviti', grazie='Fatto: la prossima lettera arriva a te.', bg=None):
    """La lettera del marchio (newsletter): titolo, due righe e il modulo con la sola email."""
    campi = [K.campo('email', 'La tua email', 'email', True, larghezza='1-1')]
    return t.section([
        t.row([t.col([K.sechead(t, occhiello, righe, size=64, layout='stack', size_mobile=38), K.text(t, testo)], wm='1-2', ws='1-1'),
               t.col([K.modulo(t, campi, 'Lettera — iscrizione', invito, grazie)], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='end'),
    ], bg=bg, padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120})
