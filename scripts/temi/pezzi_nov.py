# -*- coding: utf-8 -*-
"""Pezzi comuni dei quindici temi del 1° novembre 2026 (seconda serie: salute, noleggio, immobiliare, associazioni,
tecnologia, casa): sezioni ricorrenti costruite sulle tile del kit, coi colori del tema passati come token (K.c).
Ogni funzione riceve il Kit del tema (K) e il template (t)."""
from base import mix, radius  # noqa: F401


def passi(K, t, voci, colonne=4, numero_font='var(--olo-font-family-mono)', titolo=24):
    """Il metodo in passi numerati: voci = [(numero, titolo, testo)]."""
    C = K.c
    return t.classic('process-steps', {
        'items': [{'number': n, 'title': ti, 'description': d} for n, ti, d in voci],
        'columns': colonne, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': C['acc'], 'number_bg': '',
        'number_size': 13, 'number_font': numero_font, 'number_weight': '500', 'title_color': C['testo'], 'title_size': titolo,
        'title_weight': K.peso, 'title_font': 'heading', 'desc_color': C['tenue'], 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': C['filo']},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 2, 'columns_mobile': 1})


def listino(K, t, voci, immagini=True, separatore='dotted'):
    """Un listino: voci = [(titolo, descrizione, prezzo, foto o '', badge o '')]."""
    C = K.c
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': K.img(f) if f else '', 'highlighted': bool(h), 'badge': h}
                  for a, b, c, f, h in voci],
        'separator_style': separatore, 'separator_color': mix(C['acc'], 40), 'title_color': C['testo'], 'price_color': C['acc'],
        'description_color': C['tenue'], 'image_size': 72, 'image_border_radius': '999', 'show_image': immagini,
        'price_position': 'right', 'badge_bg': C['acc'], 'badge_color': C['on_acc'], 'badge_border_radius': '999',
        'tile_padding': {'top': 14, 'right': 0, 'bottom': 14, 'left': 0}, 'card_bg': 'transparent'}, style={})


def testimonianza(K, t, testo, nome, ruolo, size=34, linea=None):
    C = K.c
    return t.classic('testimonial', {
        'quote': testo, 'author_name': nome, 'author_role': ruolo, 'avatar': '', 'rating': '', 'text_color': C['testo'],
        'star_color': C['acc'], 'author_color': C['tenue'], 'quote_accent_color': C['acc'], 'quote_font': 'heading',
        'quote_size': size, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial', 'show_line': True,
        'line_color': linea or C['acc']}, style={})


def elenco_hover(K, t, voci, link, size=48, peek=True):
    """Righe grandi con la foto che si affaccia al passaggio: voci = [(nome, sotto, descrizione, foto)]."""
    C = K.c
    return t.classic('hoverlist', {
        'items': [{'name': n, 'sub': s, 'desc': d, 'number': '0%d' % (k + 1), 'image': K.img(f), 'link_url': link,
                   'color': C['acc']} for k, (n, s, d, f) in enumerate(voci)],
        'lead_mode': 'number', 'swatch_size': 18, 'swatch_shape': 'square', 'number_color': C['acc'],
        'number_hover_color': C['testo'], 'name_font_family': 'heading', 'name_color': C['testo'], 'name_hover_color': C['acc'],
        'name_size': size, 'name_weight': K.peso, 'name_uppercase': False, 'sub_color': C['tenue'], 'sub_hover_color': C['acc'],
        'sub_size': 12, 'sub_uppercase': True, 'desc_color': C['tenue'], 'desc_size': 15, 'row_padding_y': 22,
        'hover_indent': 28, 'hover_bg': mix(C['acc'], 6), 'line_color': C['filo'], 'peek': peek, 'peek_mode': 'image',
        'peek_width': 300, 'peek_ratio': '4/3', 'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def righe(K, t, voci, link, numero_size=14, titolo=36):
    """Un elenco a righe (worklist): voci = [(numero o data, titolo, categoria, colonna destra)]."""
    C = K.c
    return t.classic('worklist', {
        'items': [{'number': n, 'title': ti, 'category': c, 'year': y, 'link_url': link} for n, ti, c, y in voci],
        'divider_color': C['filo'], 'row_padding_y': 24, 'row_hover_bg': mix(C['acc'], 6), 'hover_indent': 24,
        'number_color': C['acc'], 'number_size': numero_size, 'title_font_family': 'heading', 'title_color': C['testo'],
        'title_size': titolo, 'title_weight': K.peso, 'show_category': True, 'category_color': C['tenue'], 'category_size': 13,
        'show_year': True, 'year_color': C['acc'], 'year_size': 15, 'show_arrow': True, 'arrow_color': C['acc'],
        'mono_font_family': 'var(--olo-font-family-mono)'}, style={})


def fila_foto(K, t, foto, ratio='4/5', gap=18):
    """Una riga di foto (2, 3 o 4) con l'ingrandimento: foto = [(file, alt)]."""
    n = len(foto)
    w = {2: '1-2', 3: '1-3', 4: '1-4'}[n]
    lay = {2: '50-50', 3: '33-33-33', 4: '25-25-25-25'}[n]
    return t.row([t.col([K.image(t, f, a, ratio=ratio, lightbox=True)], wm=w, ws='1-2' if n == 4 else '1-1') for f, a in foto],
                 layout=lay, gap=gap)


def carte(K, t, voci, altezza='380', colonne=4):
    """Carte che si girano al passaggio: voci = [(foto, titolo, davanti, dietro)]."""
    C = K.c
    w = {3: '1-3', 4: '1-4'}[colonne]
    lay = {3: '33-33-33', 4: '25-25-25-25'}[colonne]
    cols = []
    for foto, ti, da, di in voci:
        cols.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': 'center center',
                            'overlay_color': C['nero'], 'overlay_opacity': 52}, 'back_media': {'type': 'none'},
            'front_title': ti, 'front_description': da, 'front_text_color': C['chiaro'], 'front_valign': 'bottom',
            'front_overlay': '', 'back_title': ti, 'back_description': di, 'back_bg': C['sala'], 'back_text_color': C['testo'],
            'back_valign': 'center', 'back_cta_text': '', 'back_cta_url': '', 'flip_direction': 'horizontal',
            'flip_trigger': 'hover', 'card_height': altezza, 'card_border_radius': str(K.r), 'card_shadow': 'md',
            'title_size': '26', 'title_weight': K.peso, 'desc_size': '15'}, style={})], wm=w, ws='1-1'))
    return t.row(cols, layout=lay, gap=18)


def tappe(K, t, voci, icona='clock', colonne=3):
    """Tappe illustrate con la foto e il numero grande: voci = [(contatore, etichetta, foto, sopra, titolo, accento,
    testo, valore, unità)]."""
    C = K.c
    return t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': tag, 'tag_dot_color': C['acc'], 'media_type': 'image', 'media_image': K.img(f),
                   'media_label': '', 'media_content': '', 'media_bg': C['nero'], 'media_color': C['acc'], 'pre_title': pre,
                   'title': ti, 'title_accent': acc, 'title_accent_italic': K.corsivo, 'title_after': '',
                   'title_after_italic': False, 'description': d, 'footer_value': fv, 'footer_label': fl,
                   'separator_text': ''} for c, tag, f, pre, ti, acc, d, fv, fl in voci],
        'show_timeline': True, 'timeline_line_color': C['filo'], 'timeline_dot_color': C['acc'],
        'counter_font_family': 'heading', 'counter_size': 88, 'counter_color': C['acc'], 'counter_italic': K.corsivo,
        'counter_weight': K.peso, 'tag_color': C['testo'], 'pre_title_color': C['acc'], 'title_font_family': 'heading',
        'title_size': 34, 'title_weight': K.peso, 'title_color': C['testo'], 'title_accent_color': C['acc'],
        'description_color': C['tenue'], 'footer_icon': icona, 'footer_value_color': C['testo'],
        'footer_label_color': C['tenue'], 'columns': colonne, 'gap': 48, 'media_radius': radius(K.r)}, style={})


def pila(K, t, carte_, altezza=400):
    """Carte che si impilano scorrendo: carte_ = [(occhiello, titolo, accento, testo html, foto)]."""
    C = K.c
    return t.classic('stackscroll', {
        'top_offset': 110, 'top_step': 24, 'card_gap': 26, 'round': K.r, 'card_min_height': altezza, 'media_position': 'right',
        'card_bg_default': C['sala'], 'text_color_default': C['testo'], 'num_color_default': C['acc'],
        'shadow_color': mix(C['nero'], 70), 'show_number': True,
        'cards': [{'eyebrow': e, 'title': ti, 'accent': ac, 'text': tx, 'media': K.img(f), 'color': C['sala'],
                   'text_color': C['testo']} for e, ti, ac, tx, f in carte_]}, style={})


def citazione(K, t, testo, autore, size='44'):
    C = K.c
    return t.classic('quotation', {
        'content': testo, 'author': autore, 'style': 'default', 'alignment': 'left', 'content_font_family': 'heading',
        'content_font_size': size, 'content_font_weight': K.peso, 'content_font_style': 'italic' if K.corsivo else 'normal',
        'content_color': C['testo'], 'author_color': C['tenue'], 'author_font_size': '15', 'text_effect': 'none'}, style={})


def schede_icone(K, t, voci, colonne=4, fondo=None):
    """Schede col numero grande e l'icona: voci = [(contatore, etichetta, titolo, unità, testo, icona)]."""
    C = K.c
    return t.classic('info-cards', {
        'container_bg': {'type': 'none'}, 'container_radius': radius(0), 'container_padding': 0, 'container_gap': 0,
        'columns': colonne, 'items_gap': 18, 'columns_tablet': 2,
        'items': [{'counter': c, 'counter_label': lab, 'title': n, 'title_accent': u, 'title_accent_italic': False,
                   'description': d, 'icon': ic, 'footer_dot_color': C['acc'], 'footer_text': '', 'link_url': '', 'link_text': '',
                   'media_image': '', 'media_label': ''} for c, lab, n, u, d, ic in voci],
        'card_bg': {'type': 'solid', 'color': fondo or C['sala']}, 'card_color': C['testo'], 'card_radius': radius(K.r),
        'card_padding': 30, 'show_icon': True, 'show_counter': True, 'show_counter_label': True, 'title_font_family': 'heading',
        'title_size': 56, 'title_weight': K.peso, 'title_color': C['acc'], 'counter_color': C['tenue'], 'icon_color': C['acc'],
        'icon_bg_color': mix(C['acc'], 12), 'description_size': 15})
