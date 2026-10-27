# -*- coding: utf-8 -*-
"""Genera il tema «Tacet» (id tacet): una galleria d'arte su appuntamento a Pietrasanta che vende con WooCommerce
sculture in marmo, gesso, bronzo, pietra, ceramica, vetro e legno.

Tacet è il segno della partitura che dice «qui si tace». Il sito parla poco: il bianco caldo del gesso, il nero
dell'inchiostro e un solo bronzo; titoli in Cormorant Garamond sottile, testo in Inter Tight, i numeri d'inventario
in IBM Plex Mono. Angoli vivi, molta aria, nessuna decorazione.

Ogni opera è un prodotto di WooCommerce e la scheda è dinamica: galleria, artista (attributo), titolo, anno e materiale,
prezzo o «Prezzo riservato» (Template WooCommerce → prodotti senza prezzo), acquisto o richiesta di visione privata
col titolo già scritto nel modulo, la scheda tecnica per l'installazione (misure, peso, appoggio, carico, luce, cassa)
e il pedigree delle mostre, letti dai campi dell'opera con «Righe di un campo». Le opere di prova le crea
prodotti-tacet.php (wp eval-file) sul sito della demo (meta _olo_demo = tacet).
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'tacet'
NAME = 'Tacet'

HEX = {
    'primary': '#1A1917', 'primary_contrast': '#F4F1EB', 'secondary': '#7A5C3D', 'secondary_contrast': '#FBFAF7',
    'accent': '#7A5C3D', 'dark': '#121110', 'light': '#FBFAF7', 'muted': '#E7E2D9', 'muted_contrast': '#1A1917',
    'text': '#1A1917', 'text_muted': '#66625B', 'background': '#F4F1EB', 'border': '#D8D2C7', 'link': '#1A1917',
}

INK = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
FONDO = 'var(--olo-color-background)'
GESSO = 'var(--olo-color-muted)'
CARTA = 'var(--olo-color-light)'
NERO = 'var(--olo-color-dark)'
BRONZO = 'var(--olo-color-secondary)'
FILO = 'var(--olo-color-border)'
FILO_INK = mix(INK, 70)
CHIARO = 'var(--olo-color-light)'
SOFT_CHIARO = mix(CHIARO, 70)
FILO_CHIARO = mix(CHIARO, 18)
# Il bronzo sui fondi neri: quello scuro lì scende a 3:1, schiarito col chiaro della palette supera 7:1.
BRONZO_CHIARO = mix(BRONZO, 55, CHIARO)
MONO = 'var(--olo-font-family-mono)'
SQ = radius(0)
ZERO = {'top': 0, 'right': 0, 'bottom': 0, 'left': 0}

MAIL = 'sala@tacet.example'
PHONE = '0584 000 917'
PHONE_TEL = 'tel:+390584000917'
HOME = '{{PAGINA:home}}'
OPERE = '{{PAGINA:opere}}'
ARTISTI = '{{PAGINA:artisti}}'
GALLERIA = '{{PAGINA:galleria}}'
VISIONE = '{{PAGINA:visione}}'
CARRELLO = '{{PAGINA:carrello}}'

V_CORRIDOIO = 'pexels-video-39795982'  # il corridoio bianco con le finestre e una statua in fondo
V_LUCE = 'pexels-video-7492695'        # la luce che scorre su una figura di pietra, nel buio
V_SCALPELLO = 'pexels-video-6719220'   # lo scalpello sul gesso, nella penombra
V_CAVA = 'pexels-video-8902670'        # la parete di marmo che scende nell'acqua della cava

PHOTOS = [
    'pexels-7828709.jpg', 'pexels-7828701.jpg', 'pexels-7828706.jpg', 'pexels-7828698.jpg', 'pexels-7828700.jpg',
    'pexels-15535441.jpg', 'pexels-39363466.jpg', 'pexels-7828179.jpg', 'pexels-7827881.jpg', 'pexels-36382214.jpg',
    'pexels-9733007.jpg', 'pexels-20515535.jpg', 'pexels-40547007.jpg', 'pexels-30654319.jpg', 'pexels-35279463.jpg',
    'pexels-28845599.jpg', 'pexels-20364955.jpg', 'pexels-35805444.jpg', 'pexels-29833481.jpg', 'pexels-11016739.jpg',
    'pexels-37258072.jpg', 'pexels-5682147.jpg', 'pexels-8496566.jpg', 'pexels-5682313.jpg', 'pexels-10431040.jpg',
    'pexels-36309990.jpg', 'pexels-11114125.jpg', 'pexels-37220225.jpg', 'pexels-6727760.jpg', 'pexels-35870237.jpg',
    'pexels-5564949.jpg', 'pexels-7011807.jpg', 'pexels-2570059.jpg', 'pexels-36499163.jpg', 'pexels-4709423.jpg',
    'pexels-6714319.jpg', 'pexels-6713852.jpg', 'pexels-33753659.jpg', 'pexels-6714343.jpg', 'pexels-4709409.jpg',
    'pexels-35647471.jpg', 'pexels-11003040.jpg', 'pexels-29472294.jpg',
]
VIDEOS = [V_CORRIDOIO, V_LUCE, V_SCALPELLO, V_CAVA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

MATERIALI = [('Marmo', 'marmo'), ('Pietra', 'pietra'), ('Gesso', 'gesso'), ('Bronzo', 'bronzo'), ('Ceramica', 'ceramica'),
             ('Terracotta', 'terracotta'), ('Vetro e metallo', 'vetro-metallo'), ('Legno', 'legno')]

# Gli artisti: nome, ancora, luogo e anni, una riga, la frase, la foto (un'opera)
ARTISTI_DATI = [
    ('E.R.', 'e-r', 'Carrara, 1961', 'Lavora solo il marmo bianco. Lucida di notte.', '«Di giorno il marmo mente.»',
     'pexels-7828709.jpg'),
    ('Halvik', 'halvik', 'Tórshavn, 1974', 'Vive su un’isola che non nomina. Non firma: numera.', '«Il nome sta nella pietra.»',
     'pexels-15535441.jpg'),
    ('I.M.', 'i-m', 'Cagliari, 1983', 'Gesso, terra, marmo di Lasa.', '«Quello che resta quando si toglie.»',
     'pexels-36382214.jpg'),
    ('Nove Lune', 'nove-lune', 'dal 2009', 'Tre persone, un forno a legna, nessuna fotografia.', '«Una serie ogni nove lune.»',
     'pexels-9733007.jpg'),
    ('il Muto', 'il-muto', 'A.V., 1938 — 2019', 'Fonditore a Pietrasanta per cinquant’anni. Mai venduto un’opera in vita.',
     '«…»', 'pexels-35279463.jpg'),
    ('K.O.', 'k-o', 'Kanazawa, 1969', 'Vetro, acciaio, resina.', '«Taglio le cose per vedere cosa pensano.»',
     'pexels-35805444.jpg'),
]


def img(name):
    assert name in MEDIA, name
    return lib(name)


def cat_url(slug):
    return OPERE + '?product_cat=' + slug


def video_bg(name, overlay=40, color=NERO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=36, color=NERO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


def dyn(node, **bindings):
    """Collega campi della tile ai dati dell'opera: campo=(fonte, campo)."""
    node['dynamic'] = {k.replace('__', '.'): {'source': s, 'field': f} for k, (s, f) in bindings.items()}
    return node


def righe(node, chiave, mappa, items_key='items'):
    """Le voci di un ripetitore dalle righe di un campo dell'opera (Sorgente dinamica › Righe di un campo)."""
    node.setdefault('dynamic', {})
    node['dynamic']['_query'] = {'enabled': True, 'source': 'righe', 'righe_key': chiave, 'righe_max': 0, 'items_key': items_key}
    node['dynamic']['_itemMap'] = mappa
    return node


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def headline(t, label_, size=40, color=INK, tag='h3', pad_bottom=10, align='left', weight='300', spacing=None, italic=False):
    """La parte fra <em> diventa il testo accento: corsivo, color bronzo."""
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading',
         'heading_italic': italic}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': True, 'accent_color': BRONZO})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(30, int(size * 0.58)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=TENUE, pad_bottom=14, align='left', pad_top=0, size=11):
    """I numeri d'inventario e i dati: monospazio, maiuscolo, spaziato."""
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': size, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '1.8'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=16, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='filo', align='left', pad_top=14, icon=''):
    """Pulsanti piatti ad angoli vivi: inchiostro pieno, col filo, o chiaro sopra le foto."""
    if kind == 'pieno':
        o = {'bg_color': INK, 'text_color': FONDO, 'hover_bg_color': BRONZO, 'hover_text_color': CARTA}
    elif kind == 'chiaro':
        o = {'bg_color': 'transparent', 'text_color': CHIARO, 'hover_bg_color': CHIARO, 'hover_text_color': NERO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(CHIARO, 70), 'shadow_inset': True}
    elif kind == 'testo':
        o = {'bg_color': 'transparent', 'text_color': INK, 'hover_bg_color': 'transparent', 'hover_text_color': BRONZO,
             'padding_x': 0, 'padding_y': 4, 'tile_padding': {'top': 4, 'right': 0, 'bottom': 4, 'left': 0}}
    else:
        o = {'bg_color': 'transparent', 'text_color': INK, 'hover_bg_color': INK, 'hover_text_color': FONDO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': FILO_INK, 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 11, 'font_weight': '500',
              'text_transform': 'uppercase', 'letter_spacing': '2.4'})
    if icon:
        o.update({'icon': icon, 'icon_position': 'after', 'icon_spacing': 10, 'icon_size': 14})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', drift=False, lightbox=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -24, 'position': 0}, {'value': 24, 'position': 100}]}} if drift else None)


def video(t, name, ratio='4:5'):
    return t.bare('video', {'source_type': 'file', 'video_url': '', 'file_url': img(name + '.mp4'),
                            'poster_image': img(name + '.jpg'), 'display_mode': ratio, 'aspect_ratio': ratio, 'cover_mode': False,
                            'object_position': 'center center', 'border_radius': 0, 'autoplay': True, 'muted': True,
                            'loop': True, 'controls': False, 'show_play_icon': False, 'facade': False})


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs, color=INK, filo=FILO, termine=TENUE):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'horizontal', 'term_color': termine, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': color, 'definition_font_size': '16', 'separator': True, 'border_color': filo,
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'after',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': BRONZO, 'header_font_size': 24, 'header_font_weight': '300',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': INK, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, titolo, body, size=96, extra=None):
    """L'apertura delle pagine interne: il numero in mono, il titolo sottile, due righe a destra in basso."""
    return t.section([
        t.row([t.col([label(t, eyebrow), headline(t, titolo, size=size, tag='h1', pad_bottom=0)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=17)] + (extra or []), wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], padding='large', width='large', style={'padding_top': 190, 'padding_bottom': 56})


def opere(t, **kw):
    """La griglia delle opere: foto alta, l'artista sopra il titolo, il prezzo (o «Prezzo riservato») piccolo."""
    o = {
        'posts_per_page': 9, 'columns': 3, 'columns_tablet': 2, 'columns_mobile': 2, 'orderby': 'date', 'order': 'ASC',
        'category': '', 'tag': '', 'on_sale': False, 'featured': False,
        'show_image': True, 'show_title': True, 'show_price': True, 'show_rating': False, 'show_rating_count': False,
        'show_add_to_cart': False, 'show_badge': False, 'show_compare': False, 'show_wishlist': False,
        'mostra_risparmio': False, 'mostra_scorta': False, 'marca_attributo': 'artista', 'consegna_testo': '',
        'testo_evidenza': '', 'titolo_righe': 2, 'image_ratio': '4-5', 'gap': 32, 'card_style': 'none', 'hover_effect': 'swap',
        'card_bg': 'transparent', 'card_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0},
        'card_padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0},
        'title_color': INK, 'price_color': TENUE, 'sale_color': INK, 'button_bg': INK, 'button_color': FONDO, 'badge_bg': INK,
        'pagination': False, 'layout': 'grid', 'preset': 'custom', 'shadow': 'none',
        'carousel_autoplay': False, 'carousel_loop': False, 'carousel_arrows': True, 'carousel_dots': False, 'carousel_speed': 4000,
    }
    o.update(kw)
    return t.bare('woo_products', o)


def modulo_visione(t, scuro=False):
    """La richiesta di visione privata: il titolo dell'opera arriva già scritto (?opera=… dal pulsante della scheda)."""
    col = CHIARO if scuro else INK
    campi = [
        {'field_type': 'text', 'label': 'Nome e cognome', 'name': 'nome', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
        {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
        {'field_type': 'text', 'label': 'L’opera', 'name': 'opera', 'placeholder': 'Il titolo, se l’avete già scelta', 'required': False,
         'width': '1-2', 'options': '', 'icon': '', 'param': 'opera'},
        {'field_type': 'select', 'label': 'Dove guardarla', 'name': 'dove', 'placeholder': '', 'required': True, 'width': '1-2',
         'options': 'In galleria, a Pietrasanta\nNella sala privata, a Milano\nA casa vostra, con una prova in scala 1:1\nIn videochiamata, con la luce giusta',
         'icon': ''},
        {'field_type': 'textarea', 'label': 'Due righe, se volete', 'name': 'messaggio', 'placeholder': 'Lo spazio, la luce, il pavimento.',
         'required': False, 'width': '1-1', 'options': '', 'icon': ''},
    ]
    return t.classic('form', {
        'fields': campi, 'email_subject': 'Tacet — visione privata', 'submit_text': 'Chiedi la visione', 'submit_icon': '',
        'success_message': 'Ricevuto. Vi scriviamo entro domani, con l’indirizzo.',
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': col, 'label_size': '11',
        'label_weight': '400', 'label_transform': 'uppercase', 'label_letter_spacing': '1.8', 'input_bg': 'transparent',
        'input_color': col, 'input_border_color': FILO_CHIARO if scuro else FILO, 'input_border_width': '1', 'input_border_style': 'solid',
        'input_radius': 0, 'input_focus_border': BRONZO, 'input_focus_shadow': False, 'gap': '18',
        'submit_bg': CHIARO if scuro else INK, 'submit_color': NERO if scuro else FONDO, 'submit_radius': 0,
        'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '11', 'submit_font_weight': '500',
        'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2.4', 'submit_hover_bg': BRONZO, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})


def garanzie(t, color=INK):
    return t.classic('iconlist', {
        'items': [{'icon': i, 'text': s, 'link': '', 'color': ''} for i, s in [
            ('shield-check', 'Certificato di autenticità, firmato dall’artista e dalla galleria'),
            ('package', 'Cassa in legno su misura, trasporto d’arte assicurato'),
            ('wrench', 'Installazione col tecnico della galleria')]],
        'icon_color': BRONZO, 'icon_size': '16', 'text_color': color, 'text_size': '14', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': True, 'divider_color': FILO, 'layout': 'vertical',
        'list_padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0},
    }, style={'margin_top': 22, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER · FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': 'TACET', 'logo_text_size': '19', 'logo_text_color': INK,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 9, 'logo_text_transform': 'uppercase',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': mix(FONDO, 92), 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': INK, 'hover_color': BRONZO, 'active_color': BRONZO,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2.4', 'item_gap': '34',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': BRONZO,
        'button_mode': 'last', 'btn_bg': INK, 'btn_color': FONDO, 'btn_hover_bg': BRONZO, 'btn_hover_color': CARTA,
        'btn_radius': SQ, 'btn_padding': {'top': 12, 'right': 20, 'bottom': 12, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '500', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2.4',
        'social_in_navbar': False, 'social_in_mobile': False,
        'mobile_style': 'fullscreen', 'mobile_bg': FONDO, 'mobile_text_color': INK, 'mobile_accent_color': BRONZO,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '40', 'mobile_link_size': '40',
        'mobile_link_font': 'heading', 'mobile_link_weight': '300',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(INK, 10)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


def build_footer():
    t = Tpl(ID, 'footer')

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': SOFT_CHIARO, 'link_hover_color': CHIARO, 'active_color': CHIARO,
                                 'font_size': '14', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    marchio = headline(t, 'TACET', size=150, color=CHIARO, tag='p', pad_bottom=0, weight='300', spacing=24)
    claim = text(t, '<p>Galleria su appuntamento. Pietrasanta, Lucca.<br>L’indirizzo arriva con la conferma.</p>', size=15,
                 color=SOFT_CHIARO, pad=(10, 0, 18, 0))
    contatti = t.classic('iconlist', {
        'items': [{'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''},
                  {'icon': 'phone', 'text': PHONE + ' · dalle 11 alle 19', 'link': PHONE_TEL, 'color': ''}],
        'icon_color': BRONZO_CHIARO, 'icon_size': '15', 'text_color': CHIARO, 'text_size': '14', 'gap': '10', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical'}, style={})
    cols = t.row([
        t.col([claim, contatti], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le opere', color=SOFT_CHIARO), nav([(n, cat_url(s)) for n, s in MATERIALI[:5]])],
              wm='1-4', ws='1-2', width_default='1-2'),
        t.col([label(t, 'La galleria', color=SOFT_CHIARO), nav([('Artisti', ARTISTI), ('Come lavoriamo', GALLERIA),
                                                               ('Il pedigree', GALLERIA + '#pedigree'), ('Visione privata', VISIONE),
                                                               ('Il carrello', CARRELLO)])],
              wm='1-4', ws='1-2', width_default='1-2'),
    ], layout='custom', custom='50,25,25', gap=40, valign='start', anim=None)
    legal = t.classic('text-block', {'content': '<p>© 2026 Tacet · Pietrasanta (LU) · P. IVA 00000000000 · '
                                                '<a href="/privacy-policy/">Privacy</a></p>',
                                     'font_size': 12, 'line_height': 1.75, 'text_color': SOFT_CHIARO, 'link_color': CHIARO,
                                     'text_align': 'left', 'tile_padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0}})
    totop = t.classic('totop', {'icon_color': NERO, 'posizione': 'fisso', 'style': 'default'})
    fondo = t.row([t.col([legal, totop], wm='1-1', ws='1-1')], anim=None,
                  style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO_CHIARO},
                         'padding_top': 18, 'margin_top': 40})
    return [t.section([t.one([marchio], anim=None), cols, fondo], bg=solid(NERO), padding='large', width='large',
                      style={'padding_top': 90, 'padding_bottom': 40})]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def build_home():
    t = Tpl(ID, 'home')
    out = []
    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Pietrasanta · su appuntamento', 'eyebrow_dot': False,
        'title': 'Opere che parlano<br><em>sottovoce.</em>',
        'subtitle': 'Marmo, gesso, bronzo, pietra. Venti opere, sei artisti, nessuna fretta.',
        'media_bg': {'type': 'video', 'video_url': img(V_CORRIDOIO + '.mp4'), 'video_poster': img(V_CORRIDOIO + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': FONDO, 'overlay_top': 0.2, 'overlay_bottom': 0.82, 'overlay_sides': True, 'overlay_direction': 'left',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': BRONZO, 'text_color': INK, 'title_color': INK, 'subtitle_color': mix(INK, 78),
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 112, 'title_font_weight': '300',
        'title_line_height': '0.98', 'title_letter_spacing': '-2', 'title_text_transform': 'none',
        'subtitle_font_size': 17, 'subtitle_max_width': 460,
        'min_height': '100vh', 'content_max_width': 1320, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 140, 'right': 40, 'bottom': 72, 'left': 40},
        'cta_text': 'Le opere', 'cta_url': OPERE, 'cta_bg_color': INK, 'cta_text_color': FONDO,
        'cta_radius': SQ, 'cta_size': '11', 'cta_style': 'filled', 'cta_font_weight': '500', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2.4',
        'cta2_text': 'Visione privata', 'cta2_url': VISIONE, 'cta2_text_color': INK, 'cta2_style': 'ghost',
        'meta_text': 'N. 001 — 020', 'scroll_hint': '', 'scene_depth': 'none', 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    out.append(t.section([
        t.row([t.col([label(t, 'In sala, questo mese'), headline(t, 'Sei opere. <em>Le altre</em> si chiedono.', size=64, tag='h2',
                                                                  pad_bottom=0)], wm='2-3', ws='1-1'),
               t.col([button(t, 'Tutte le opere', OPERE, kind='testo', align='right', icon='arrow-right')], wm='1-3', ws='1-1')],
              layout='custom', custom='70,30', valign='end', style={'margin_bottom': 56}),
        t.one([opere(t, featured=True, posts_per_page=6)]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))

    out.append(t.section([
        centered(t, [label(t, 'E.R., da una lettera', align='center', color=SOFT_CHIARO),
                     headline(t, '«Il marmo ricorda l’ultima mano che l’ha toccato.»', size=64, color=CHIARO, tag='p',
                              align='center', italic=True, pad_bottom=0)]),
    ], bg=video_bg(V_LUCE, overlay=30), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220}))

    out.append(t.section([
        t.row([t.col([label(t, 'I materiali'), headline(t, 'Otto materie.', size=56, tag='h2', pad_bottom=0)], wm='1-2', ws='1-1'),
               t.col([text(t, '<p>Si sceglie dalla materia, come in un laboratorio: il peso, la temperatura, quanto tiene la luce.</p>')],
                     wm='1-2', ws='1-1')],
              layout='50-50', gap=48, valign='end', style={'margin_bottom': 48}),
        t.one([t.bare('woo_categories', {
            'columns': 4, 'columns_tablet': 4, 'columns_mobile': 2, 'show_image': True, 'show_count': False,
            'show_description': False, 'hide_empty': True, 'orderby': 'name', 'parent_only': True, 'overlay': False,
            'text_color': INK, 'title_tag': 'h3', 'gap': 24, 'image_ratio': '4-5', 'border_radius': 0, 'hover_effect': 'zoom',
            'posizione_testo': 'sotto', 'scorri_mobile': True, 'pagina_negozio': OPERE,
        })]),
    ], bg=solid(CARTA), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))

    elenco = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': luogo, 'desc': riga, 'number': '%02d' % (i + 1), 'image': img(f),
                   'link_url': ARTISTI + '#' + a, 'color': ''}
                  for i, (n, a, luogo, riga, frase, f) in enumerate(ARTISTI_DATI)],
        'lead_mode': 'number', 'number_color': TENUE, 'number_hover_color': BRONZO,
        'name_font_family': 'heading', 'name_weight': '300', 'name_color': INK, 'name_hover_color': INK, 'name_size': 56,
        'name_uppercase': False,
        'desc_color': TENUE, 'desc_hover_color': INK, 'desc_size': 13, 'sub_uppercase': False,
        'row_padding_y': 22, 'hover_indent': 28, 'hover_bg': 'transparent', 'line_color': FILO,
        'peek': True, 'peek_mode': 'image', 'peek_width': 300, 'peek_ratio': '4/5',
        'row_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
    })
    out.append(t.section([
        t.row([t.col([label(t, 'Gli artisti'), headline(t, 'Sei nomi. <em>Alcuni</em> non sono nomi.', size=56, tag='h2'),
                      text(t, '<p>Sappiamo di loro quello che vogliono farci sapere. Il resto è nelle opere.</p>'),
                      button(t, 'Gli artisti', ARTISTI)], wm='1-3', ws='1-1'),
               t.col([elenco], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-35870237.jpg', 'Un busto di marmo su un piedistallo nero, al centro di una sala vuota',
                            ratio='4/5', drift=True)], wm='1-2', ws='1-1'),
               t.col([label(t, 'Il pedigree'),
                      headline(t, 'Ogni opera viaggia col suo <em>passaporto.</em>', size=56, tag='h2'),
                      text(t, '<p>Dove è stata esposta, in quale anno, in quali mani. Lo trovate in fondo a ogni scheda, riga per riga, '
                              'e su carta nella cassa. Un’opera che ha girato il mondo lo porta addosso.</p>'),
                      specs(t, [('Equilibrio, I', 'In viaggio da cinque anni, quattro città'),
                                ('Undici', 'Sei città in sette anni'), ('Le mani di prima', 'Trentanove anni, quasi tutti in silenzio')]),
                      button(t, 'Come lavoriamo', GALLERIA, pad_top=24)], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], bg=solid(GESSO), padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 120}))

    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'La visione', 'description': 'In galleria, nella sala di Milano o a casa vostra con una '
                                                                         'sagoma di cartone in scala 1:1.'},
                  {'number': '02', 'title': 'Il certificato', 'description': 'Firmato dall’artista e dalla galleria, con il pedigree '
                                                                             'e il rapporto sullo stato di conservazione.'},
                  {'number': '03', 'title': 'La cassa', 'description': 'In legno, costruita sull’opera. Viaggia assicurata, con un '
                                                                       'trasportatore d’arte.'},
                  {'number': '04', 'title': 'Il posto', 'description': 'Il nostro tecnico la installa: perni, base, la luce giusta. '
                                                                       'Poi se ne va in silenzio.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': BRONZO, 'number_bg': '',
        'number_size': 12, 'number_font': MONO, 'number_weight': '400', 'title_color': INK, 'title_size': 28,
        'title_weight': '300', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 14, 'align': 'left', 'item_gap': 10,
    })
    out.append(t.section([
        t.one([label(t, 'Da qui a casa vostra'), headline(t, 'Quattro passi, nessuno di troppo.', size=56, tag='h2', pad_bottom=40)]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col([label(t, 'Prezzo riservato', color=SOFT_CHIARO),
                      headline(t, 'Alcune opere non hanno prezzo esposto.', size=64, color=CHIARO, tag='h2', pad_bottom=14),
                      text(t, '<p>Si chiede. Si guarda dal vero. Si decide con calma.</p>', size=17, color=SOFT_CHIARO),
                      button(t, 'Visione privata', VISIONE, kind='chiaro', pad_top=20)], wm='3-5', ws='1-1'),
               t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', valign='end'),
    ], bg=photo_bg('pexels-10431040.jpg', overlay=58, position='center 40%'), padding='xlarge',
        style={'padding_top': 220, 'padding_bottom': 110}))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='TACET',
                page_intro_caption='qui si tace', page_intro_bg=HEX['background'], page_intro_color=HEX['text'],
                page_intro_duration=2200, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  LE OPERE · GLI ARTISTI
# ══════════════════════════════════════════════════════════════════════════════

def build_opere():
    t = Tpl(ID, 'opere')
    filtro = t.bare('woo_product_filter', {
        'show_price': True, 'show_categories': True, 'show_attributes': True, 'show_stock': False, 'show_active': True,
        'price_min': 0, 'price_max': 40000, 'price_step': 500, 'attributes': 'pa_artista', 'collapsed': False,
        'heading_color': INK, 'text_color': INK, 'accent_color': BRONZO, 'bg_color': 'transparent', 'border_color': FILO,
        'border_radius': 0, 'button_text': 'Mostra le opere', 'reset_text': 'Tutte',
        'show_price_range': True, 'show_count': True, 'apply_button': True, 'filter_style': 'sidebar', 'collapsible': False,
        'active_color': BRONZO, 'button_color': FONDO, 'button_bg': INK,
    })
    out = [intro(t, 'Catalogo · N. 001 — 020', 'Le opere', '<p>Alcune hanno un prezzo, altre si chiedono. Tutte hanno una scheda '
                                                           'tecnica e un pedigree.</p>')]
    out.append(t.section([
        t.row([t.col([filtro], wm='1-4', ws='1-1'),
               t.col([opere(t, posts_per_page=30, columns=3, columns_tablet=2)], wm='3-4', ws='1-1')],
              layout='custom', custom='22,78', gap=56, valign='start', anim=None),
    ], padding='default', width='large', style={'padding_top': 24, 'padding_bottom': 120}))
    out.append(t.section([
        t.row([t.col([label(t, 'Non trovate quello che cercate', color=SOFT_CHIARO),
                      headline(t, 'Non tutto è in catalogo.', size=56, color=CHIARO, tag='h2', pad_bottom=12),
                      text(t, '<p>Ci sono opere che escono dall’archivio una alla volta, e opere che un artista fa su richiesta, '
                              'una volta sola.</p>', color=SOFT_CHIARO),
                      button(t, 'Chiedete', VISIONE, kind='chiaro', pad_top=18)], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_CAVA, overlay=64), padding='xlarge', style={'padding_top': 200, 'padding_bottom': 100}))
    return out


def build_artisti():
    t = Tpl(ID, 'artisti')
    out = [intro(t, 'Sei nomi', 'Gli artisti', '<p>Sappiamo di loro quello che vogliono farci sapere. Due righe a testa; '
                                                 'il resto è nelle opere.</p>')]
    for i, (n, a, luogo, riga, frase, f) in enumerate(ARTISTI_DATI):
        testo = [label(t, '%02d · %s' % (i + 1, luogo)), headline(t, n, size=88, tag='h2', pad_bottom=8),
                 text(t, '<p>' + riga + '</p>', size=18, color=INK),
                 headline(t, frase, size=28, color=BRONZO, tag='p', italic=True, pad_bottom=0),
                 button(t, 'Le opere', OPERE + '?filter_artista=' + a, kind='testo', icon='arrow-right', pad_top=22)]
        foto = [image(t, f, 'Un’opera di ' + n, ratio='4/5', drift=True)]
        cols = [t.col(testo, wm='3-5', ws='1-1'), t.col(foto, wm='2-5', ws='1-1')]
        if i % 2:
            cols = [t.col(foto, wm='2-5', ws='1-1'), t.col(testo, wm='3-5', ws='1-1')]
        out.append(t.section([
            t.row(cols, layout='custom', custom='60,40' if i % 2 == 0 else '40,60', gap=80, valign='center'),
        ], bg=solid(CARTA) if i % 2 else None, padding='large', width='large', html_id=a,
            style={'padding_top': 100, 'padding_bottom': 100}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LA GALLERIA · VISIONE PRIVATA
# ══════════════════════════════════════════════════════════════════════════════

def build_galleria():
    t = Tpl(ID, 'galleria')
    out = [intro(t, 'Pietrasanta · Milano', 'Una stanza, <em>nessuna</em> vetrina.',
                 '<p>Una sala a Pietrasanta, a duecento metri dai laboratori dove si lavora il marmo da cinque secoli. Una stanza a '
                 'Milano per chi non può venire. Si entra su appuntamento.</p>')]
    out.append(t.section([
        t.row([t.col([video(t, V_SCALPELLO, ratio='4:5')], wm='1-2', ws='1-1'),
               t.col([label(t, 'Come lavoriamo'),
                      headline(t, 'Conosciamo ogni opera per nome, peso e difetti.', size=48, tag='h2'),
                      text(t, '<p>Le opere arrivano dai laboratori e dagli archivi, mai dalle aste. Le guardiamo per mesi prima di '
                              'esporle: se una crepa si muove, lo sappiamo noi prima di voi.</p>'
                              '<p>Ogni opera ha una scheda tecnica scritta per chi la installerà e un pedigree per chi la erediterà.</p>'),
                      ], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], padding='large', width='large', style={'padding_top': 40, 'padding_bottom': 120}))

    esempio = t.classic('timeline', {
        'items': [{'title': t_, 'tag': c, 'description': '', 'date': a, 'status': '', 'image': '', 'video': '', 'icon': '',
                   'category': 'primary'} for a, c, t_ in [
            ('2021', 'Pietrasanta', 'Prima uscita, «Bianco su nero», Galleria Tacet'), ('2022', 'Basilea', '«Peso specifico», Kunstraum Nebel'),
            ('2023', 'Ginevra', 'In prestito alla Fondazione Lanterna'), ('2025', 'Seul', '«Stone Silence», Hall Nove'),
            ('2026', 'Pietrasanta', 'Rientrata in galleria')]],
        'tl_layout': 'schedule', 'tl_node': 'dot', 'tl_media': 'off', 'tl_density': 'compact', 'tl_line': 'solid',
        'tl_reveal': 'sides', 'tl_theme': 'night', 'tl_transparent': True, 'tl_color': 'mono', 'tl_text_align': 'left',
        'tl_show_tag': True, 'tl_tag_color': BRONZO_CHIARO, 'tl_rail_color': FILO_CHIARO, 'tl_rail_w': 1, 'tl_node_size': 9,
        'tl_title_color': CHIARO, 'tl_title_size': 22, 'tl_title_family': 'var(--olo-font-family-heading)',
        'tl_text_color': SOFT_CHIARO, 'tl_yr_color': SOFT_CHIARO, 'tl_yr_size': 13, 'tl_yr_family': MONO,
        'icon_color': BRONZO_CHIARO,
    })
    out.append(t.section([
        t.row([t.col([label(t, 'Il pedigree', color=SOFT_CHIARO),
                      headline(t, 'Dove è stata, in ordine.', size=56, color=CHIARO, tag='h2'),
                      text(t, '<p>Una riga per ogni volta che l’opera ha lasciato la cassa: l’anno, la città, la mostra o la '
                              'collezione. Così si legge quello di «Equilibrio, I».</p>', color=SOFT_CHIARO)], wm='2-5', ws='1-1'),
               t.col([esempio], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(NERO), padding='large', width='large', html_id='pedigree', style={'padding_top': 120, 'padding_bottom': 120}))

    out.append(t.section([
        t.row([t.col([label(t, 'L’installazione'), headline(t, 'Prima che arrivi.', size=56, tag='h2'),
                      text(t, '<p>Ogni scheda dice quanto pesa l’opera, su quanto spazio, con quale luce. Qui il resto.</p>')],
                     wm='1-3', ws='1-1'),
               t.col([specs(t, [('Il pavimento', 'Sopra i 150 kg/m² un sopralluogo, gratuito. Il parquet vuole una piastra.'),
                                ('Le basi', 'Le facciamo noi, in ferro, pietra o legno, sulle misure dell’opera.'),
                                ('I perni', 'Inox, nascosti. Un’opera sopra il metro non si appoggia e basta.'),
                                ('La luce', '2700–3000 K, radente. Mai sopra la testa per i volti.'),
                                ('Il clima', 'Gesso e legno lontani da termosifoni e finestre a sud.')])],
                     wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=72, valign='start'),
    ], padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 100}))

    out.append(t.section([
        t.row([t.col([label(t, 'Le domande'), headline(t, 'Quelle che ci fanno tutti.', size=48, tag='h2')], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Perché alcune opere non hanno prezzo?', 'Perché l’artista, o i suoi eredi, vogliono sapere dove andranno. '
                                                              'Il prezzo si dice dopo la visione.'),
                   ('Spedite fuori dall’Italia?', 'Sì, con un trasportatore d’arte e i documenti per la dogana. La cassa viaggia '
                                                   'assicurata dalla porta della galleria alla vostra.'),
                   ('Posso vederla a casa mia prima di comprarla?', 'Portiamo una sagoma di cartone in scala 1:1 e la luce. Per le '
                                                                   'opere piccole, a volte, l’opera stessa.'),
                   ('Cosa c’è nella cassa?', 'L’opera, il certificato di autenticità, il pedigree su carta e il rapporto sullo stato '
                                             'di conservazione con le fotografie.'),
                   ('Si può pagare in più volte?', 'Sì, in tre o sei rate per le opere con prezzo esposto. L’opera parte dopo l’ultima.'),
               ], 'gal')], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=72, valign='start'),
    ], bg=solid(CARTA), padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col([label(t, 'Da dove vengono', color=SOFT_CHIARO),
                      headline(t, 'Il bianco viene da lassù.', size=64, color=CHIARO, tag='h2', pad_bottom=12),
                      text(t, '<p>Le cave sono a mezz’ora. Gli artisti scelgono il blocco di persona; noi andiamo con loro.</p>',
                           color=SOFT_CHIARO)], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=photo_bg('pexels-36499163.jpg', overlay=64, position='center center'), padding='xlarge',
        style={'padding_top': 220, 'padding_bottom': 100}))
    return out


def build_visione():
    t = Tpl(ID, 'visione')
    out = [intro(t, 'Su appuntamento', 'Visione privata', '<p>Le opere si guardano dal vero. In galleria, nella sala di Milano, '
                                                          'a casa vostra o, per chi è lontano, in videochiamata con la luce giusta.</p>')]
    out.append(t.section([
        t.row([t.col([modulo_visione(t)], wm='3-5', ws='1-1'),
               t.col([label(t, 'Dove'), text(t, '<p>Pietrasanta, centro storico.<br>Milano, una stanza in Brera.</p>', color=INK),
                      label(t, 'Quando', pad_top=18), text(t, '<p>Dal martedì al sabato, dalle 11 alle 19. La domenica, se serve.</p>', color=INK),
                      label(t, 'L’indirizzo', pad_top=18),
                      text(t, '<p>Arriva con la conferma, insieme al nome di chi vi aprirà.</p>', color=INK),
                      image(t, 'pexels-5564949.jpg', 'Una testa di marmo bianco su fondo bianco', ratio='4/3')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=72, valign='start', anim=None),
    ], padding='default', width='large', style={'padding_top': 24, 'padding_bottom': 120}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LA SCHEDA DELL'OPERA (Template WooCommerce → Singolo prodotto)
# ══════════════════════════════════════════════════════════════════════════════

def build_opera():
    t = Tpl(ID, 'opera')
    galleria = t.bare('woo_product_gallery_slider', {
        'show_thumbnails': True, 'thumbnail_position': 'bottom', 'enable_zoom': True, 'enable_lightbox': True,
        'main_height': '76vh', 'max_width': 0, 'thumbnail_size': 64, 'thumbnail_gap': 10, 'arrows': True, 'main_bg': GESSO,
        'thumbnail_border': 'transparent', 'thumbnail_active_border': INK, 'arrow_color': INK, 'arrow_bg': mix(CARTA, 90),
        'show_badge': False, 'border_radius': 0, 'transition': 'fade',
    })
    numero = dyn(label(t, 'N. —', pad_bottom=26), heading=('woocommerce', 'product_sku'))
    artista = dyn(t.classic('headline', {'heading': 'Artista', 'tag': 'p', 'alignment': 'left', 'decoration': 'none', 'subtitle': '',
                                         'heading_uppercase': True, 'heading_font_size': 13, 'heading_weight': '500',
                                         'heading_color': BRONZO, 'heading_font': 'body', 'heading_letter_spacing': '3'},
                            style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 10, 'padding_left': 0}),
                  heading=('woocommerce', 'attributo:pa_artista'))
    destra = [
        numero, artista,
        t.bare('woo_product_title', {'tag': 'h1', 'text_align': 'left', 'color': INK, 'font_size': 60, 'font_weight': '300',
                                     'line_height': '1.02', 'link_to_product': False}),
        dyn(label(t, 'Anno · materiale', pad_top=14, pad_bottom=24), heading=('custom_field', 'riga_opera')),
        t.bare('woo_price', {'show_regular': True, 'show_sale': True, 'show_suffix': False, 'price_color': INK,
                             'sale_color': INK, 'regular_color': TENUE, 'font_size': 22, 'font_weight': '400',
                             'text_align': 'left'}),
        t.bare('woo_product_description', {'content_type': 'short', 'text_color': TENUE, 'font_size': 16, 'line_height': '1.75',
                                           'text_align': 'left', 'max_lines': 0}, style={'margin_top': 14}),
        t.bare('woo_addtocart', {'button_text': 'Acquista', 'show_quantity': False, 'show_icon': False, 'icon': 'cart',
                                 'style': 'filled', 'size': 'large', 'full_width': True, 'bg_color': INK, 'text_color': FONDO,
                                 'font_size': 11, 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': 2.4,
                                 'hover_bg': BRONZO, 'hover_text': CARTA, 'quantity_style': 'input', 'border_radius': SQ,
                                 'testo_senza_prezzo': 'Chiedi prezzo e visione', 'link_senza_prezzo': '#visione',
                                 'parametro_senza_prezzo': 'opera'}, style={'margin_top': 22}),
        button(t, 'Scheda tecnica e pedigree', '#scheda', kind='testo', icon='arrow-down', pad_top=14),
        garanzie(t),
    ]
    out = [t.section([
        t.row([t.col([galleria], wm='3-5', ws='1-1'), t.col(destra, wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=64, valign='start', anim=None),
    ], padding='default', width='large', style={'padding_top': 130, 'padding_bottom': 90})]

    out.append(t.section([
        t.row([t.col([label(t, 'La nota')], wm='1-3', ws='1-1'),
               t.col([t.bare('woo_product_description', {'content_type': 'full', 'text_color': INK, 'font_size': 22,
                                                          'line_height': '1.6', 'text_align': 'left', 'max_lines': 0})],
                     wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=56, valign='start'),
    ], bg=solid(CARTA), padding='large', width='large', style={'padding_top': 100, 'padding_bottom': 100}))

    scheda = t.classic('schedatecnica', {
        'codice': '', 'nome': '', 'etichetta': '', 'descrizione': '', 'immagine': '',
        'titolo_specifiche': '', 'specifiche': [
            {'gruppo': 'Misure', 'nome': 'Altezza', 'valore': '58', 'unita': 'cm'},
            {'gruppo': 'Misure', 'nome': 'Peso', 'valore': '96', 'unita': 'kg'},
            {'gruppo': 'Installazione', 'nome': 'Appoggio', 'valore': 'Piano orizzontale', 'unita': ''}],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': '',
        'cta_testo': 'Chiedi il rapporto sullo stato di conservazione', 'cta_url': '#visione', 'cta_codice': True,
        'cta_parametro': 'opera', 'dati_strutturati': False,
        'accento': BRONZO, 'sfondo_scheda': 'transparent', 'bordo_scheda': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''},
        'colore_linee': FILO, 'righe_alternate': False, 'raggio': SQ, 'raggio_pulsante': SQ,
        'padding_scheda': ZERO, 'foto_posizione': 'sopra', 'gap': 32, 'colonne_specifiche': 2,
    })
    righe(dyn(scheda, codice=('woocommerce', 'product_title')), 'scheda_tecnica',
          {'gruppo': 'parte_1', 'nome': 'parte_2', 'valore': 'parte_3', 'unita': 'parte_4'}, items_key='specifiche')
    out.append(t.section([
        t.row([t.col([label(t, 'Scheda tecnica'), headline(t, 'Per chi la ospiterà.', size=48, tag='h2'),
                      text(t, '<p>Misure, peso, appoggio, luce e cassa: quello che serve sapere prima che arrivi. Il nostro tecnico '
                              'fa il resto.</p>')], wm='1-3', ws='1-1'),
               t.col([scheda], wm='2-3', ws='1-1')], layout='custom', custom='30,70', gap=64, valign='start'),
    ], padding='large', width='large', html_id='scheda', style={'padding_top': 110, 'padding_bottom': 110}))

    viaggio = dyn(headline(t, 'In viaggio.', size=56, color=CHIARO, tag='h2'), heading=('custom_field', 'viaggio'))
    pedigree = righe(t.classic('timeline', {
        'items': [{'title': 'La mostra', 'tag': 'La città', 'description': '', 'date': '2026', 'status': '', 'image': '',
                   'video': '', 'icon': '', 'category': 'primary'}],
        'tl_layout': 'schedule', 'tl_node': 'dot', 'tl_media': 'off', 'tl_density': 'compact', 'tl_line': 'solid',
        'tl_reveal': 'sides', 'tl_theme': 'night', 'tl_transparent': True, 'tl_color': 'mono', 'tl_text_align': 'left',
        'tl_show_tag': True, 'tl_tag_color': BRONZO_CHIARO, 'tl_rail_color': FILO_CHIARO, 'tl_rail_w': 1, 'tl_node_size': 9,
        'tl_title_color': CHIARO, 'tl_title_size': 22, 'tl_title_family': 'var(--olo-font-family-heading)',
        'tl_text_color': SOFT_CHIARO, 'tl_yr_color': SOFT_CHIARO, 'tl_yr_size': 13, 'tl_yr_family': MONO,
        'icon_color': BRONZO_CHIARO,
    }), 'pedigree', {'date': 'parte_1', 'tag': 'parte_2', 'title': 'parte_3'})
    out.append(t.section([
        t.row([t.col([label(t, 'Pedigree', color=SOFT_CHIARO), viaggio,
                      text(t, '<p>Dove è stata, in ordine. Il resto lo racconta la cassa.</p>', color=SOFT_CHIARO)],
                     wm='2-5', ws='1-1'),
               t.col([pedigree], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(NERO), padding='large', width='large', html_id='pedigree', style={'padding_top': 110, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col([label(t, 'Visione privata'), headline(t, 'Vederla dal vero.', size=56, tag='h2'),
                      text(t, '<p>In galleria, a Milano, a casa vostra con una sagoma in scala 1:1, o in videochiamata. Il titolo '
                              'dell’opera è già scritto.</p>')], wm='1-3', ws='1-1'),
               t.col([modulo_visione(t)], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=72, valign='start'),
    ], bg=solid(GESSO), padding='large', width='large', html_id='visione', style={'padding_top': 110, 'padding_bottom': 110}))

    out.append(t.section([
        t.one([label(t, 'Nella stessa stanza'), headline(t, 'Della stessa materia.', size=48, tag='h2', pad_bottom=40)]),
        t.one([t.bare('woo_related', {'posts_per_page': 3, 'columns': 3, 'columns_tablet': 3, 'columns_mobile': 2,
                                      'show_image': True, 'show_title': True, 'show_price': True, 'card_style': 'none',
                                      'gap': 40, 'title_color': INK, 'price_color': TENUE, 'show_heading': False,
                                      'heading_text': ''})]),
    ], padding='large', width='large', style={'padding_top': 110, 'padding_bottom': 120}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  CARRELLO · CASSA · 404
# ══════════════════════════════════════════════════════════════════════════════

def build_carrello():
    t = Tpl(ID, 'carrello')
    out = [intro(t, 'Il carrello', 'Quello che avete scelto.', '<p>Il trasporto d’arte e l’installazione si concordano dopo '
                                                               'l’ordine: vi scriviamo noi, entro un giorno.</p>', size=80)]
    out.append(t.section([
        t.one([t.bare('woo_cart', {'show_thumbnail': True, 'show_coupon': False, 'show_totals': True, 'show_cross_sells': False,
                                   'button_color': FONDO, 'button_bg': INK, 'text_color': INK, 'heading_color': INK,
                                   'border_color': FILO})]),
        t.one([button(t, 'Torna alle opere', OPERE, pad_top=28)]),
    ], padding='default', width='large', style={'padding_top': 24, 'padding_bottom': 120}))
    return out


def build_cassa():
    t = Tpl(ID, 'cassa')
    out = [intro(t, 'La cassa', 'Ancora un passo.', '<p>I vostri dati e l’indirizzo dove l’opera abiterà. Nelle note: il piano, '
                                                    'l’ascensore, gli scalini. Servono al trasportatore.</p>', size=80)]
    out.append(t.section([
        t.one([t.bare('woo_checkout', {'layout': 'two_columns', 'show_order_notes': True, 'form_style': 'modern',
                                       'accent_color': BRONZO, 'text_color': INK, 'heading_color': INK, 'border_color': FILO,
                                       'button_color': FONDO, 'button_bg': INK, 'input_bg': CARTA, 'input_text_color': INK,
                                       'input_radius': '0', 'panel_bg': CARTA, 'notice_bg': GESSO, 'notice_text': INK})]),
    ], padding='default', width='large', style={'padding_top': 24, 'padding_bottom': 120}))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404', align='center', color=SOFT_CHIARO),
                     headline(t, 'Questa stanza è vuota.', size=88, color=CHIARO, tag='h1', align='center', pad_bottom=14),
                     text(t, '<p>L’opera che cercate è partita, o non è mai arrivata.</p>', size=17, color=SOFT_CHIARO, align='center'),
                     button(t, 'Le opere', OPERE, kind='chiaro', align='center', pad_top=20)]),
    ], bg=video_bg(V_LUCE, overlay=48), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 200})]


THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Negozi online', 'zone': 'Opere su appuntamento',
    'requires_plugin': {'slug': 'woocommerce', 'label': 'WooCommerce', 'check': 'class:WooCommerce',
                        'purchase_url': 'https://wordpress.org/plugins/woocommerce/'},
    'tags': ['galleria', 'arte', 'sculture', 'woocommerce', 'minimale', 'lusso', 'marmo'],
    'description': 'Una galleria d’arte su appuntamento che vende sculture con WooCommerce: le opere in griglia con l’artista e '
                   'il prezzo o «Prezzo riservato», i materiali, gli artisti, e una scheda dell’opera dinamica con la scheda '
                   'tecnica per l’installazione (misure, peso, appoggio, luce, cassa), il pedigree delle mostre e la richiesta '
                   'di visione privata col titolo già scritto.',
    'menu': {'name': 'Tacet — menu', 'items': [{'title': 'Opere', 'url': OPERE}, {'title': 'Artisti', 'url': ARTISTI},
                                              {'title': 'La galleria', 'url': GALLERIA}, {'title': 'Carrello', 'url': CARRELLO},
                                              {'title': 'Visione privata', 'url': VISIONE}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Tacet — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Tacet — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Tacet — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Tacet — Home'},
        'opere': {'file': 'opere.json', 'type': 'page', 'title': 'Tacet — Le opere'},
        'artisti': {'file': 'artisti.json', 'type': 'page', 'title': 'Tacet — Gli artisti'},
        'galleria': {'file': 'galleria.json', 'type': 'page', 'title': 'Tacet — La galleria'},
        'visione': {'file': 'visione.json', 'type': 'page', 'title': 'Tacet — Visione privata'},
        'carrello': {'file': 'carrello.json', 'type': 'page', 'title': 'Tacet — Carrello'},
        'cassa': {'file': 'cassa.json', 'type': 'page', 'title': 'Tacet — Cassa'},
        'opera': {'file': 'opera.json', 'type': 'single', 'title': 'Tacet — Scheda dell’opera', 'post_type': 'product'},
    },
    'singles': {'product': 'opera'},
    # Template WooCommerce all'import (Impostazioni → Template WooCommerce) e la scritta dei prodotti senza prezzo.
    'woo': {'product_archive': 'opere', 'product_category': 'opere', 'cart': 'carrello', 'checkout': 'cassa',
            'senza_prezzo': 'Prezzo riservato'},
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'opere': {'title': 'Le opere', 'template': 'opere', 'slug': 'opere'},
        'artisti': {'title': 'Gli artisti', 'template': 'artisti', 'slug': 'artisti'},
        'galleria': {'title': 'La galleria', 'template': 'galleria', 'slug': 'la-galleria'},
        'visione': {'title': 'Visione privata', 'template': 'visione', 'slug': 'visione-privata'},
        'carrello': {'title': 'Carrello', 'template': 'carrello', 'slug': 'carrello'},
        'cassa': {'title': 'Cassa', 'template': 'cassa', 'slug': 'cassa'},
    },
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Inter Tight\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Cormorant Garamond\', Georgia, \'Times New Roman\', serif',
            'font_family_mono': '\'IBM Plex Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '300',
            'heading_line_height': '1.05', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '4.4rem', 'font_size_h2': '3.2rem', 'font_size_h3': '2rem',
            'font_size_h4': '1.5rem', 'font_size_h5': '1.2rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.6rem',
            'font_size_h2_mobile': '2.1rem', 'font_size_h3_mobile': '1.6rem',
        },
        'google_fonts': ['Cormorant Garamond', 'Inter Tight', 'IBM Plex Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': ['Cormorant Garamond'],
        'buttons': {'font_size': '11px', 'font_weight': '500', 'padding_x': '26px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '0.22em', 'hover_brightness': '100'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1320px',
                   'container_narrow': '760px', 'container_wide': '1520px'},
        'forms': {'field_bg': 'transparent', 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '0px', 'field_padding': '13px 14px', 'field_font_size': '15px',
                  'focus_border_color': HEX['secondary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '400', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'opere.json': build_opere(), 'artisti.json': build_artisti(), 'galleria.json': build_galleria(),
              'visione.json': build_visione(), 'opera.json': build_opera(), 'carrello.json': build_carrello(),
              'cassa.json': build_cassa(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
