# -*- coding: utf-8 -*-
"""Genera il tema «Undici Ulivi» (id undici-ulivi): undici residenze fra gli ulivi sulla collina di Gardone Riviera.

Minimale e a due toni come i siti immobiliari premiati del 2026 (ERA Residence, Belgrade Arbor, LPAS): lino non
sbiancato e verde oliva quasi nero, nient'altro. Titoli in uno Spectral sottilissimo col corsivo, testo in Albert
Sans, didascalie in maiuscoletto spaziato, angoli vivi e fili da un pixel. Di sera la pagina cambia palette
(interruttore «Di giorno / Di sera» in basso a sinistra, palette scura del tema).

Dettagli: l'apertura della home (una riga che si disegna e il velo che si apre in due), il cursore ad anello
sottile, la grana leggera della carta, i numeri delle sezioni, le coordinate sotto il titolo.

Moduli: il manifesto che si accende scorrendo (Scrub testo), le undici case sul plastico (Hotspot), una giornata in
casa che scorre di lato mentre si scende (ScrollScrub «pin»), la collina vista dal drone a tutto schermo, i quattro
materiali da guardare con la lente (Prova materiale), le quattro case con la foto che segue il cursore (Hover List),
la disponibilità casa per casa (Work List), il cantiere a che punto è (Progress tracker), quanto consuma una casa in
classe A4 (Grafico), le stagioni sul lago e la visita in cantiere (Form con la data).
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid, rgba  # noqa: E402

ID = 'undici-ulivi'
NAME = 'Undici Ulivi'

HEX = {
    'primary': '#3D4A2E', 'primary_contrast': '#F1ECE1', 'secondary': '#8A7A5C', 'secondary_contrast': '#F1ECE1',
    'accent': '#A88B52', 'dark': '#1C2017', 'light': '#F8F5EE', 'muted': '#E7E1D4', 'muted_contrast': '#262B1F',
    'text': '#262B1F', 'text_muted': '#6B6C5D', 'background': '#F1ECE1', 'border': '#D2CBBA', 'link': '#3D4A2E',
}
# Di sera: la stessa casa con le luci accese (interruttore nel piè di pagina).
DARK = {
    'background': '#161A13', 'text': '#ECE6D8', 'text_muted': '#A29F8E', 'muted': '#20251C', 'muted_contrast': '#ECE6D8',
    'border': '#363C2E', 'primary': '#C9AE74', 'primary_contrast': '#161A13', 'secondary': '#C9AE74',
    'secondary_contrast': '#161A13', 'accent': '#C9AE74', 'link': '#C9AE74',
}

INK = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
OLIVA = 'var(--olo-color-primary)'
ON_OLIVA = 'var(--olo-color-primary-contrast)'
ORO = 'var(--olo-color-accent)'
FONDO = 'var(--olo-color-background)'
LINO = 'var(--olo-color-muted)'
NOTTE = 'var(--olo-color-dark)'
CHIARO = 'var(--olo-color-light)'
FILO = 'var(--olo-color-border)'
FILO_INK = mix(INK, 70)
SOFT_CHIARO = mix(CHIARO, 82)
SQ = radius(0)

ADDRESS = 'Via dei Colli 11, 25083 Gardone Riviera (BS)'
UFFICIO = 'Ufficio vendite: piazza Vittorio Emanuele 4, Salò'
PHONE = '0365 000 011'
PHONE_TEL = 'tel:+390365000011'
MAIL = 'visite@undiciulivi.example'
LAT, LNG = '45.6219', '10.5622'
HOME = '{{PAGINA:home}}'
RESIDENZE = '{{PAGINA:residenze}}'
LUOGO = '{{PAGINA:il-luogo}}'
PROGETTO = '{{PAGINA:progetto}}'
VISITA = '{{PAGINA:visita}}'

V_ULIVI = 'pexels-video-28581354'   # i rami d'ulivo davanti al lago e ai monti
V_DRONE = 'pexels-video-17692982'   # dal drone: la villa fra gli uliveti, scendendo fino alla piscina
V_OMBRA = 'pexels-video-5732088'    # l'ombra di una foglia sul muro a calce
V_GARDA = 'pexels-video-35998905'   # il lago dall'alto, i paesi sulla costa
V_TENDA = 'pexels-video-5854057'    # la tenda bianca che si muove alla finestra

PHOTOS = [
    'pexels-10610733.jpg', 'pexels-10610731.jpg', 'pexels-28054862.jpg', 'pexels-28054864.jpg', 'pexels-30781823.jpg',
    'pexels-31817157.jpg', 'pexels-34169538.jpg', 'pexels-36713221.jpg', 'pexels-20924617.jpg', 'pexels-16841981.jpg',
    'pexels-31199348.jpg', 'pexels-12372367.jpg', 'pexels-11653973.jpg', 'pexels-19143503.jpg', 'pexels-5320082.jpg',
    'pexels-23825911.jpg', 'pexels-19477644.jpg', 'pexels-34162143.jpg', 'pexels-40078553.jpg', 'pexels-35580849.jpg',
    'pexels-7303777.jpg', 'pexels-35021550.jpg', 'pexels-11299685.jpg', 'pexels-15852131.jpg', 'pexels-6962862.jpg',
    'pexels-1410227.jpg', 'pexels-3872927.jpg', 'pexels-39726130.jpg', 'pexels-7799723.jpg', 'pexels-13777755.jpg',
    'pexels-37128295.jpg', 'pexels-4709462.jpg', 'pexels-12106848.jpg', 'pexels-36146010.jpg', 'pexels-6544938.jpg',
    'pexels-29618523.jpg', 'pexels-6757413.jpg', 'pexels-5987195.jpg', 'pexels-10767817.jpg', 'pexels-5777366.jpg',
    'pexels-34492974.jpg', 'pexels-20543598.jpg', 'pexels-32796350.jpg', 'pexels-9052535.jpg', 'pexels-6614748.jpg',
    'pexels-7883886.jpg', 'pexels-20208884.jpg', 'pexels-9618453.jpg', 'pexels-15458299.jpg', 'pexels-18080900.jpg',
    'pexels-8482816.jpg', 'pexels-15565290.jpg', 'pexels-20225736.jpg', 'pexels-5835536.jpg', 'pexels-5292204.jpg',
    'pexels-6568669.jpg', 'pexels-9618125.jpg', 'pexels-3771808.jpg', 'pexels-3763781.jpg', 'pexels-4183590.jpg',
]
VIDEOS = [V_ULIVI, V_DRONE, V_OMBRA, V_GARDA, V_TENDA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=40, color=NOTTE, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, num, label, lines, tagline='', size=76, align='left', layout='split', size_mobile=42, color=INK):
    """Il numero della sezione in maiuscoletto («01 — La collina»), poi il titolo sottile: le righe dispari in
    corsivo, come una seconda voce."""
    eyebrow = ('%s — %s' % (num, label)) if num else label
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': TENUE, 'eyebrow_dot_color': OLIVA,
        'eyebrow_separator': '', 'eyebrow_font_family': 'body', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '500',
        'headline_lines': [{'text': l, 'color': color, 'italic': i % 2 == 1} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.02,
        'headline_font_weight': '200', 'headline_align': align,
        'headline_font_size_tablet': max(40, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 16, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 36, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label, size=40, color=INK, tag='h3', pad_bottom=10, align='left', weight='300'):
    """Il Titolo toglie i tag: la parte fra <em> diventa il suo «testo accento», in corsivo e dello stesso colore."""
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    m = re.search(r'<em>(.*?)</em>', label)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': True, 'accent_color': color})
    o['heading'] = label.replace('<em>', '').replace('</em>', '')
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(30, int(size * 0.6)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=TENUE, pad_bottom=14, align='left', pad_top=0):
    """La didascalia in maiuscoletto spaziato: il tono di voce di tutto il tema."""
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 11, 'heading_weight': '500',
                                  'heading_color': color, 'heading_font': 'body', 'heading_letter_spacing': '2.5'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=16, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='filo', align='left', pad_top=14):
    """Pulsanti piatti ad angoli vivi: pieno d'oliva o col filo da un pixel."""
    if kind == 'pieno':
        o = {'bg_color': OLIVA, 'text_color': ON_OLIVA, 'hover_bg_color': INK, 'hover_text_color': FONDO}
    elif kind == 'chiaro':
        o = {'bg_color': 'transparent', 'text_color': CHIARO, 'hover_bg_color': CHIARO, 'hover_text_color': NOTTE,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(CHIARO, 70), 'shadow_inset': True}
    else:
        o = {'bg_color': 'transparent', 'text_color': INK, 'hover_bg_color': INK, 'hover_text_color': FONDO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': FILO_INK, 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 11, 'font_weight': '500',
              'text_transform': 'uppercase', 'letter_spacing': '2.5'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', zoom=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': False, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -28, 'position': 0}, {'value': 28, 'position': 100}]}} if zoom else None)


def centered(t, tiles, side=16.66):
    """Il testo al centro, largo due terzi: due colonne vuote ai lati (sul telefono spariscono)."""
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs, layout='vertical'):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': layout, 'term_color': TENUE, 'term_font_size': '11', 'term_font_weight': '500',
        'definition_color': INK, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': LUOGO + '#mappa', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': OLIVA, 'icon_size': '16', 'text_color': INK, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def hours(t, color=INK):
    return t.classic('hoursstrip', {
        'items': [{'day': 'sabato e domenica', 'time': '10 — 18', 'note': 'in cantiere, col caschetto: lo portiamo noi'},
                  {'day': 'lunedì — venerdì', 'time': 'su appuntamento', 'note': 'anche di sera, con le luci accese'},
                  {'day': 'ufficio vendite', 'time': 'Salò', 'note': 'piazza Vittorio Emanuele 4, tutti i giorni 9 — 13'}],
        'columns': 3, 'band_padding_y': 28, 'show_dividers': True, 'divider_color': FILO, 'band_border': False,
        'day_color': TENUE, 'day_size': 11, 'time_font_family': 'heading', 'time_color': color, 'time_size': 40,
        'time_weight': '200', 'note_color': TENUE, 'note_size': 14, 'mono_font_family': '',
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': OLIVA, 'header_font_size': 24, 'header_font_weight': '300',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': INK, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, num, label_, lines, body, size=96):
    """L'apertura delle pagine interne: niente fascia scura, solo il titolo grande sul lino e il filo sotto."""
    return t.section([
        t.row([t.col([sechead(t, num, label_, lines, size=size, size_mobile=46)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=17, color=TENUE)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], padding='large', style={'padding_top': 150, 'padding_bottom': 60})


# ══════════════════════════════════════════════════════════════════════════════
#  LE CASE
# ══════════════════════════════════════════════════════════════════════════════

TIPI = [
    {'nome': 'Casa sull’uliveto', 'quante': 'cinque case', 'foto': 'pexels-10610731.jpg', 'pos': 'center 60%',
     'testo': 'Un piano solo, lungo il muro a secco del terrazzamento: il soggiorno guarda il lago, le camere guardano '
              'gli ulivi. Il giardino arriva fino al bordo del muro, la piscina è lunga come la casa.',
     'specs': [('Camere', 'tre, con due bagni'), ('Superficie', '168 — 184 m²'), ('Giardino', '380 — 460 m²'),
               ('In più', 'piscina di 14 metri, posto auto coperto')], 'da': 'da 1.180.000 €'},
    {'nome': 'Casa lunga', 'quante': 'tre case', 'foto': 'pexels-28054864.jpg', 'pos': 'center center',
     'testo': 'Due camere dietro un portico di pietra, il tetto piantato a timo e rosmarino. La cucina si apre del tutto sul '
              'patio: d’estate la casa diventa una stanza sola.',
     'specs': [('Camere', 'due, con due bagni'), ('Superficie', '118 — 126 m²'), ('Giardino', '210 m² e il patio'),
               ('In più', 'tetto giardino, cantina')], 'da': 'da 760.000 €'},
    {'nome': 'Casa alta', 'quante': 'due case', 'foto': 'pexels-30781823.jpg', 'pos': 'center 40%',
     'testo': 'Tre piani in cima alla collina. In alto la terrazza col lago tutto intero, da Sirmione a Malcesine; sotto, '
              'quattro camere e lo studio con la finestra sugli ulivi.',
     'specs': [('Camere', 'quattro e uno studio'), ('Superficie', '210 m² su tre piani'), ('Giardino', '520 m²'),
               ('In più', 'terrazza di 60 m², ascensore, piscina')], 'da': 'da 1.640.000 €'},
    {'nome': 'Casa della limonaia', 'quante': 'una casa', 'foto': 'pexels-40078553.jpg', 'pos': 'center 40%',
     'testo': 'Dentro i pilastri della limonaia dell’Ottocento, restaurata pietra per pietra. Le travi di castagno sono quelle di '
              'allora; i vetri, d’inverno, si chiudono come facevano per i limoni.',
     'specs': [('Camere', 'due, con un bagno'), ('Superficie', '132 m²'), ('Giardino', 'gli agrumi della limonaia'),
               ('In più', 'vincolo paesaggistico, già restaurata')], 'da': '940.000 €'},
]

# Numero, nome, dove sta, superficie e giardino, prezzo o stato.
CASE = [
    ('01', 'Casa della limonaia', 'Sotto la strada, fra i pilastri dell’Ottocento', '132 m² · agrumeto', 'venduta'),
    ('02', 'Casa sull’uliveto', 'Primo terrazzamento, la più vicina al lago', '172 m² · giardino 420 m²', 'venduta'),
    ('03', 'Casa sull’uliveto', 'Primo terrazzamento, verso Salò', '168 m² · giardino 380 m²', '1.180.000 €'),
    ('04', 'Casa lunga', 'Secondo terrazzamento, col portico a est', '118 m² · giardino 210 m²', 'in trattativa'),
    ('05', 'Casa lunga', 'Secondo terrazzamento, al centro', '122 m² · giardino 210 m²', '780.000 €'),
    ('06', 'Casa lunga', 'Secondo terrazzamento, verso il bosco', '126 m² · giardino 230 m²', '760.000 €'),
    ('07', 'Casa sull’uliveto', 'Terzo terrazzamento, sotto l’ulivo del Seicento', '184 m² · giardino 460 m²', 'venduta'),
    ('08', 'Casa sull’uliveto', 'Terzo terrazzamento, verso Gardone', '176 m² · giardino 400 m²', '1.260.000 €'),
    ('09', 'Casa sull’uliveto', 'Quarto terrazzamento, la più riservata', '170 m² · giardino 440 m²', '1.220.000 €'),
    ('10', 'Casa alta', 'In cima, verso Malcesine', '210 m² · terrazza 60 m²', 'venduta'),
    ('11', 'Casa alta', 'In cima, verso Sirmione', '214 m² · terrazza 62 m²', '1.640.000 €'),
]

# Dove stanno le case sul plastico (percentuali sulla foto) e cosa si legge toccandole.
PLASTICO = [
    ('52', '25', '01'), ('64', '42', '02'), ('76', '47', '03'), ('37', '35', '04'), ('22', '24', '05'), ('12', '40', '06'),
    ('30', '54', '07'), ('46', '62', '08'), ('24', '72', '09'), ('58', '80', '10'), ('40', '86', '11'),
]


def stato(prezzo):
    return prezzo if prezzo in ('venduta', 'in trattativa') else 'disponibile · ' + prezzo


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': NAME, 'logo_text_size': '26', 'logo_text_color': INK,
        'logo_text_font': 'heading', 'logo_text_weight': '300', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'classic', 'sticky': True,
        'sticky_bg': mix(FONDO, 92), 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': FONDO, 'text_color': INK, 'hover_color': OLIVA, 'active_color': OLIVA,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2.5', 'item_gap': '34',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': INK,
        'button_mode': 'last', 'btn_bg': INK, 'btn_color': FONDO, 'btn_hover_color': FONDO,
        'btn_radius': SQ, 'btn_padding': {'top': 13, 'right': 22, 'bottom': 13, 'left': 22}, 'btn_hover_effect': 'none',
        'btn_font_weight': '500', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2.5',
        'social_in_navbar': False, 'social_in_mobile': False, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': FONDO, 'mobile_text_color': INK, 'mobile_accent_color': OLIVA,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '40', 'mobile_link_size': '40',
        'mobile_link_font': 'heading', 'mobile_link_weight': '200',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': FILO},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, NAME, size=84, color=INK, tag='p', pad_bottom=6, weight='200')
    claim = text(t, '<p>Undici case fra trecentododici ulivi, sulla collina di Gardone Riviera. Progetto dello studio '
                    'Ferlini Guerini, consegna nella primavera del 2027.</p>', size=15, color=TENUE, pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': INK, 'link_hover_color': OLIVA, 'active_color': OLIVA,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('Le residenze', RESIDENZE), ('Il luogo', LUOGO), ('Il progetto', PROGETTO), ('Prenota una visita', VISITA),
                  ('Privacy', '/privacy-policy/')])
    visite = text(t, '<p>Sabato e domenica 10 — 18<br>in settimana su appuntamento</p><p>' + UFFICIO + '</p>', size=15,
                  color=INK)
    legal = text(t, '<p>© 2026 Undici Ulivi s.r.l. · Gardone Riviera · Le immagini delle case sono illustrative: fanno fede '
                    'il capitolato e le planimetrie depositate.</p>', size=12, color=TENUE)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': TENUE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': INK, 'posizione': 'fisso', 'style': 'default'})
    # Di giorno / di sera: la palette scura del tema, ricordata per chi torna. Un tondo col fondo del tema (si legge
    # anche sopra i video), fisso a destra sopra il «torna in cima»: a sinistra copriva i pulsanti dei titoli.
    sera = t.bare('darkmode', {'style': 'icon', 'light_icon': 'moon', 'dark_icon': 'sun', 'icon_size': 18,
                               'button_text_light': 'Di sera', 'button_text_dark': 'Di giorno', 'toggle_color': INK,
                               'toggle_active_color': ORO, 'save_preference': True, 'respect_system': False,
                               'transition_duration': 600},
                  style={'bg': solid(FONDO), 'border_radius': radius(999), 'padding_top': 4, 'padding_right': 4,
                         'padding_bottom': 4, 'padding_left': 4,
                         'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': FILO}},
                  advanced={'position_mode': 'fixed', 'position_bottom': '86', 'position_right': '22', 'position_zindex': '9990',
                            # sul telefono in basso a sinistra: a destra copriva i pulsanti del titolo
                            'position_bottom_mobile': '24', 'position_right_mobile': 'auto',
                            'position_left_mobile': '20'})
    top = t.row([
        t.col([brand, claim, contacts(t)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine'), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'Le visite'), visite], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop, sera], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
                          'padding_top': 22, 'margin_top': 56})
    return [t.section([top, bottom], bg=solid(FONDO), padding='large',
                      style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}})]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

GIORNATA = [
    ('07:10 · La cucina', 'pexels-7303777.jpg', 'Il sole arriva da Salò e attraversa il piano in pietra di Botticino. Si fa colazione '
     'col lago ancora grigio.'),
    ('10:30 · Lo studio', 'pexels-20543598.jpg', 'Una finestra sola, quadrata, sugli ulivi. La luce cambia ogni ora sul muro a calce.'),
    ('13:00 · Il patio', 'pexels-10610731.jpg', 'L’ulivo nella vasca di pietra era già lì: la casa gli gira intorno.'),
    ('16:40 · La piscina', 'pexels-3763781.jpg', 'Quattordici metri lungo il muro a secco, l’acqua alla stessa quota del lago.'),
    ('19:20 · Il bagno', 'pexels-11299685.jpg', 'Un lavabo scavato in un blocco di pietra, la luce bassa, nient’altro.'),
    ('22:00 · La camera', 'pexels-6962862.jpg', 'Lino, rovere e il buio vero: sulla collina non ci sono lampioni.'),
]

MATERIALI = [
    ('pietra', 'Pietra di Botticino', 'Brescia · 30 km', 'La pietra del Vittoriano viene da qui. Levigata per i piani della cucina e '
     'del bagno, lasciata grezza per le soglie.', 'pexels-12106848.jpg'),
    ('rovere', 'Rovere oliato', 'Slavonia · tavole da 30 cm', 'Pavimenti a tavole larghe, oliate a mano: col tempo diventano color miele.',
     'pexels-6544938.jpg'),
    ('calce', 'Intonaco a calce', 'impastato in cantiere', 'Respira, non fa muffa, prende la luce in modo diverso a ogni ora del giorno.',
     'pexels-5987195.jpg'),
    ('secco', 'Muro a secco', 'le pietre della collina', 'I terrazzamenti crollati li abbiamo rimontati a mano, pietra per pietra, '
     'senza malta.', 'pexels-36146010.jpg'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = []

    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Gardone Riviera · lago di Garda', 'eyebrow_dot': False,
        'title': 'Undici case<br><em>fra gli ulivi.</em>',
        'subtitle': 'Sulla collina sopra il lago, fra trecentododici ulivi che nessuno ha spostato. Consegna nella primavera del 2027.',
        'media_bg': {'type': 'video', 'video_url': img(V_ULIVI + '.mp4'), 'video_poster': img(V_ULIVI + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': NOTTE, 'overlay_top': 0.0, 'overlay_bottom': 0.62, 'overlay_sides': False, 'overlay_direction': '',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': CHIARO, 'text_color': CHIARO, 'title_color': CHIARO, 'subtitle_color': SOFT_CHIARO,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 136, 'title_font_weight': '200',
        'title_line_height': '0.94', 'title_letter_spacing': '-2', 'title_text_transform': 'none',
        'subtitle_font_size': 16, 'subtitle_max_width': 430,
        'min_height': '100vh', 'content_max_width': 1360, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 140, 'right': 48, 'bottom': 64, 'left': 48},
        'cta_text': 'Le undici case', 'cta_url': RESIDENZE, 'cta_bg_color': CHIARO, 'cta_text_color': NOTTE,
        'cta_radius': SQ, 'cta_size': '11', 'cta_style': 'filled', 'cta_font_weight': '500', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2.5',
        'cta2_text': 'Prenota una visita', 'cta2_url': VISITA, 'cta2_text_color': CHIARO, 'cta2_style': 'ghost',
        'meta_text': '45°37′ N · 10°34′ E · 180 metri sul lago',
        'scroll_hint': 'Scorri', 'scene_depth': 'soft', 'scene_depth_fade': True, 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    manifesto = t.classic('scrubtext', {
        'text': 'Abbiamo disegnato undici case <em>dove gli ulivi lasciavano spazio</em>. Gli altri trecentododici sono rimasti '
                'dove li avevano piantati.',
        'show_lead': True,
        'lead': 'Tre ettari di uliveto a terrazze, a centottanta metri sul lago. Le case seguono le curve dei muri a secco: nessun '
                'albero è stato spostato, due sono stati curati da un arboricoltore e quest’anno hanno dato di nuovo le olive.',
        'scroll_reveal': True, 'dim_opacity': 18, 'accent': OLIVA, 'text_color': INK, 'lead_color': TENUE,
        'size_min': 34, 'size_max': 66, 'max_width_ch': 26, 'lead_size': 16, 'lead_max_width_ch': 60,
        'text_font_family': 'heading', 'text_font_weight': '300', 'text_align': 'left',
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    numeri = t.classic('statstrip', {
        'items': [{'value': '11', 'label': 'case, nessuna uguale'}, {'value': '312', 'label': 'ulivi, nessuno tagliato'},
                  {'value': '180 m', 'label': 'sopra il lago'}, {'value': 'A4', 'label': 'classe energetica'},
                  {'value': '2027', 'label': 'primavera, le chiavi'}],
        'columns': 5, 'columns_tablet_landscape': 5, 'columns_tablet': 3, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': INK, 'value_size': 46, 'value_weight': '200',
        'label_color': TENUE, 'label_size': 11, 'label_uppercase': True, 'label_weight': '500', 'align': 'left',
        'mono_font_family': 'sans-serif',
    })
    out.append(t.section([
        t.row([t.col([label(t, '00 — Il progetto')], wm='1-4', ws='1-1'), t.col([manifesto], wm='3-4', ws='1-1')],
              layout='custom', custom='25,75', gap=32, valign='start'),
        t.one([numeri], style={'margin_top': 72}),
    ], padding='large', html_id='progetto', style={'padding_top': 140, 'padding_bottom': 120}))

    plastico = t.classic('hotspot', {
        'image': img('pexels-7883886.jpg'), 'image_alt': 'Il plastico in legno della collina, con le curve di livello dei terrazzamenti',
        'image_height': '', 'aspect_ratio': '3/2', 'aspect_ratio_custom': '3/2', 'object_fit': 'cover',
        'object_position': 'center center',
        'markers': [{'pos_x': x, 'pos_y': y, 'title': 'Casa %s · %s' % (n, c[1]),
                     'description': '%s. %s — %s.' % (c[2], c[3], stato(c[4])), 'icon': 'pin',
                     'tooltip_position': 'left' if float(x) > 55 else 'right', 'link': RESIDENZE + '#disponibilita',
                     'link_label': 'La scheda'}
                    for x, y, n in PLASTICO for c in CASE if c[0] == n],
        'border_radius': 0, 'marker_style': 'number', 'marker_color': INK, 'marker_text_color': FONDO, 'marker_size': 30,
        'pulse_animation': False, 'tooltip_bg': INK, 'tooltip_color': FONDO, 'tooltip_width': 260,
    })
    out.append(t.section([
        t.row([t.col([sechead(t, '01', 'La collina', ['Undici case,', 'una collina sola.'], size=60, layout='stack', size_mobile=40),
                      text(t, '<p>Toccate i numeri sul plastico: è quello che lo studio ha costruito in legno di tiglio, '
                              'una curva di livello ogni mezzo metro. Ogni casa sta su un terrazzamento che c’era già.</p>'
                              '<p>Le case segnate «venduta» hanno già un nome sul campanello.</p>', size=16),
                      button(t, 'La disponibilità', RESIDENZE + '#disponibilita')], wm='1-3', ws='1-1'),
               t.col([plastico], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='center'),
    ], bg=solid(LINO), padding='large', html_id='collina'))

    giornata = t.classic('scrollscrub', {
        'behavior': 'pin', 'scroll_length': 3, 'align': 'start', 'gap': 28, 'easing': 'linear', 'show_progress': True,
        'arrows': 'minimale', 'arrows_pos': 'sotto', 'arrows_size': 40, 'arrows_bg': 'transparent', 'arrows_color': INK,
        'indicators': 'none', 'edge_fade': False, 'drag_scroll': True,
        'heading': '', 'kicker': '', 'item_width': 540, 'item_min_height': 0, 'round': 0,
        'item_padding': {'top': 18, 'right': 0, 'bottom': 8, 'left': 0}, 'item_bg_default': 'transparent',
        'text_color_default': INK, 'progress_color': INK, 'show_number': False,
        'items': [{'title': ti, 'subtitle': '', 'text': tx, 'media': img(f)} for ti, f, tx in GIORNATA],
    })
    out.append(t.section([
        t.one([sechead(t, '02', 'Una giornata', ['Dalle sette', 'alle ventidue.'], size=72,
                       tagline='Scendete con la rotella: la giornata scorre di lato, stanza dopo stanza, in una Casa '
                               'sull’uliveto.')]),
        t.one([giornata]),
    ], padding='large', html_id='giornata'))

    out.append(t.section([
        centered(t, [label(t, '03 — Dall’alto', color=SOFT_CHIARO, align='center'),
                     headline(t, 'Dal drone, <em>le case quasi non si vedono.</em>', size=72, color=CHIARO, tag='h2',
                              align='center', weight='200', pad_bottom=18),
                     text(t, '<p>I tetti sono giardini: timo, rosmarino e lavanda sopra le camere. Da Gardone, guardando su, '
                             'si vedono gli ulivi.</p>', size=16, color=SOFT_CHIARO, align='center')]),
    ], bg=video_bg(V_DRONE, overlay=52), padding='xlarge', html_id='drone',
        style={'padding_top': 220, 'padding_bottom': 220}))

    materiali = t.classic('provamateriale', {
        'voci': [{'id': 'uu-%s' % k, 'nome': n, 'codice': c, 'testo': tx, 'immagine': img(f), 'miniatura': '', 'colore': '',
                  'alt': n + ': ' + tx} for k, n, c, tx, f in MATERIALI],
        'iniziale': 0, 'lente': True, 'lente_zoom': 2.4, 'lente_misura': 180, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passate sopra la foto: la lente mostra la grana',
        'testo_suggerimento_touch': 'Toccate la foto per accendere la lente', 'mostra_testi': True,
        'etichetta_campioni': 'Materiale', 'accento': INK, 'colore_testo': INK, 'colore_tenue': TENUE, 'sfondo_pannello': '',
        'ombra': 'none', 'etichetta_font_family': 'sans-serif', 'etichetta_font_size': 11, 'etichetta_font_weight': '500',
        'etichetta_color': TENUE, 'nome_font_family': 'heading', 'nome_font_size': 44, 'nome_font_size_tablet': 36,
        'nome_font_size_mobile': 32, 'nome_font_weight': '200', 'nome_color': INK, 'codice_font_family': 'sans-serif',
        'codice_font_size': 11, 'codice_font_weight': '500', 'codice_color': OLIVA, 'testo_font_family': 'sans-serif',
        'testo_font_size': 15,
    })
    out.append(t.section([
        t.one([sechead(t, '04', 'I materiali', ['Quattro materiali,', 'tutti da qui intorno.'], size=72,
                       tagline='Niente che arrivi da più lontano di un giorno di camion: la pietra da Botticino, la calce '
                               'impastata in cantiere, le pietre dei muri raccolte sulla collina.')]),
        t.one([materiali]),
    ], padding='large', html_id='materiali'))

    tipi = t.classic('hoverlist', {
        'items': [{'name': x['nome'], 'sub': x['quante'], 'desc': '%s · %s' % (x['quante'], x['da']), 'number': '0%d' % (i + 1),
                   'image': img(x['foto']), 'link_url': RESIDENZE + '#tipo-%d' % (i + 1), 'color': ''}
                  for i, x in enumerate(TIPI)],
        'lead_mode': 'number', 'number_color': TENUE, 'number_hover_color': INK,
        'name_font_family': 'heading', 'name_weight': '300', 'name_color': INK, 'name_hover_color': OLIVA, 'name_size': 52,
        'name_uppercase': False,
        'desc_color': TENUE, 'desc_hover_color': INK, 'desc_size': 14, 'sub_uppercase': False,
        'row_padding_y': 26, 'hover_indent': 24, 'hover_bg': 'transparent', 'line_color': FILO,
        'peek': True, 'peek_mode': 'image', 'peek_width': 300, 'peek_ratio': '4/5',
        'row_padding': {'top': 26, 'right': 0, 'bottom': 26, 'left': 0},
    })
    out.append(t.section([
        t.row([t.col([sechead(t, '05', 'Le case', ['Quattro modi', 'di stare qui.'], size=60, layout='stack', size_mobile=40),
                      text(t, '<p>Passate sopra i nomi: la foto segue il cursore. Tutte hanno il giardino, tutte guardano il '
                              'lago, nessuna guarda dentro un’altra.</p>', size=16),
                      button(t, 'Piante e prezzi', RESIDENZE)], wm='1-3', ws='1-1'),
               t.col([tipi], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(LINO), padding='large', html_id='case'))

    out.append(t.section([
        t.row([t.col([headline(t, 'Venite a vederle <em>di sabato</em>, quando il cantiere è fermo.', size=80, tag='h2',
                               weight='200', pad_bottom=22),
                      text(t, '<p>Vi accompagnano gli architetti, col caschetto e le scarpe giuste: le portiamo noi. Dura '
                              'un’ora; alla fine si beve l’olio nuovo sul pane, al frantoio di Gardone.</p>', size=17),
                      button(t, 'Prenota una visita', VISITA, kind='pieno', pad_top=10),
                      text(t, '<p>Oppure chiamate il ' + PHONE + ': risponde Elena, dell’ufficio vendite di Salò.</p>',
                           size=14, pad=(18, 0, 0, 0))],
                     wm='3-5', ws='1-1'),
               t.col([image(t, 'pexels-15565290.jpg', 'Una mano aperta con due olive verdi appena raccolte', ratio='4/5',
                            position='center 55%', zoom=True)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], padding='large', html_id='visita', style={'padding_top': 150, 'padding_bottom': 150}))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text=NAME,
                page_intro_caption='Gardone Riviera · lago di Garda', page_intro_duration=2600, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  LE RESIDENZE
# ══════════════════════════════════════════════════════════════════════════════

def build_residenze():
    t = Tpl(ID, 'residenze')
    out = [intro(t, '', 'Le residenze', ['Quattro case,', 'undici indirizzi.'],
                 '<p>Cinque Case sull’uliveto, tre Case lunghe, due Case alte e la Casa della limonaia. Le piante, i capitolati e '
                 'i prezzi sono qui sotto; le case vendute restano in elenco, col loro numero.</p>')]
    out.append(t.section([t.one([image(t, 'pexels-10610733.jpg', 'Una Casa sull’uliveto: vetrate, pietra chiara e un ulivo nel patio',
                                       ratio='21/9', position='center 62%', zoom=True)], anim='fade')],
                         padding='remove-vertical', width='wide'))
    for i, x in enumerate(TIPI):
        foto = t.col([image(t, x['foto'], x['nome'], ratio='4/5', position=x['pos'])], wm='1-2', ws='1-1')
        testo = t.col([label(t, '0%d — %s' % (i + 1, x['quante'])),
                       headline(t, x['nome'], size=64, tag='h2', weight='200', pad_bottom=16),
                       text(t, '<p>' + x['testo'] + '</p>', size=16),
                       specs(t, x['specs'] + [('Prezzo', x['da'])]),
                       button(t, 'Chiedi la pianta', VISITA + '?casa=' + x['nome'].replace('’', '%27'), pad_top=20)],
                      wm='1-2', ws='1-1')
        cols = [foto, testo] if i % 2 == 0 else [testo, foto]
        out.append(t.section([t.row(cols, layout='50-50', gap=80, valign='center')], padding='large',
                             bg=solid(LINO) if i % 2 else None, html_id='tipo-%d' % (i + 1)))

    lista = t.classic('worklist', {
        'items': [{'number': n, 'title': nome, 'category': mq, 'year': stato(p)} for n, nome, dove, mq, p in CASE],
        'divider_color': FILO, 'row_padding_y': 22, 'row_hover_bg': mix(INK, 4), 'hover_indent': 16,
        'number_color': TENUE, 'number_size': 12, 'title_font_family': 'heading', 'title_color': INK, 'title_size': 34,
        'title_weight': '300', 'show_category': True, 'category_color': TENUE, 'category_size': 14, 'show_year': True,
        'year_color': OLIVA, 'year_size': 13, 'show_arrow': False, 'arrow_color': INK, 'mono_font_family': 'body',
        'row_padding': {'top': 20, 'right': 6, 'bottom': 20, 'left': 6}, 'title_size_tablet': 28, 'title_size_mobile': 22,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.one([sechead(t, '', 'La disponibilità', ['Casa per casa,', 'aggiornata ogni lunedì.'], size=64,
                       tagline='I numeri sono quelli del plastico in home. Il prezzo comprende il giardino, il posto auto e '
                               'le finiture del capitolato; la piscina dove c’è.')]),
        t.one([lista]),
    ], padding='large', html_id='disponibilita'))

    out.append(t.section([
        t.row([t.col([sechead(t, '', 'Il capitolato', ['Cosa c’è', 'dentro i muri.'], size=56, layout='stack', size_mobile=38),
                      text(t, '<p>Il capitolato completo, quaranta pagine, lo mandiamo con la pianta della casa che vi '
                              'interessa.</p>'), button(t, 'Chiedilo', VISITA)], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('La struttura', 'Cemento armato a vista nelle fondazioni, muri in blocchi di laterizio porizzato da 45 cm, '
                                    'cappotto in fibra di legno. Tetti giardino con 40 cm di terra.'),
                   ('Il riscaldamento e il fresco', 'Pompa di calore geotermica con sonde a 120 metri, pavimento radiante e '
                                                   'raffrescante, ventilazione meccanica con recupero del calore.'),
                   ('La luce', 'Fotovoltaico integrato nelle coperture della Casa alta e del posto auto: 6 kW per casa, con '
                               'batteria da 10 kWh.'),
                   ('Le finiture', 'Pavimenti in rovere oliato o in pietra di Botticino, intonaco a calce, serramenti in legno '
                                   'e alluminio brunito. Si scelgono con gli architetti, senza sovrapprezzo fino al rogito.'),
                   ('Il giardino', 'Gli ulivi restano dove sono e si curano insieme. L’olio di tutti gli alberi si divide fra le '
                                   'undici case: circa trenta litri a famiglia, ogni novembre.'),
               ], 'cap')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(LINO), padding='large', html_id='capitolato'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  IL LUOGO
# ══════════════════════════════════════════════════════════════════════════════

STAGIONI = [
    ('Gennaio', 'La nebbia resta sotto la collina: sopra, il sole.', 'pexels-20924617.jpg'),
    ('Maggio', 'Gli ulivi fioriscono per dieci giorni. Il polline è d’oro sulle terrazze.', 'pexels-31199348.jpg'),
    ('Agosto', 'Alle sette di sera il lago diventa d’argento verso Sirmione.', 'pexels-34162143.jpg'),
    ('Novembre', 'La raccolta: le reti sotto gli alberi, il frantoio di Gardone aperto fino a tardi.', 'pexels-15565290.jpg'),
]


def build_luogo():
    t = Tpl(ID, 'il-luogo')
    out = []
    out.append(t.section([
        t.row([t.col([label(t, 'Il luogo', color=SOFT_CHIARO),
                      headline(t, 'Il lago <em>a ottocento passi.</em>', size=120, color=CHIARO, tag='h1', weight='200', pad_bottom=16),
                      text(t, '<p>Gardone Riviera, sulla sponda bresciana: il Vittoriale sopra, il lungolago sotto, Salò a cinque '
                              'chilometri. Il clima è quello dei limoni: d’inverno qui non gela.</p>', size=17, color=SOFT_CHIARO)],
                     wm='3-5', ws='1-1')], layout='custom', custom='60,40', valign='end'),
    ], bg=video_bg(V_GARDA, overlay=42), padding='xlarge', style={'padding_top': 260, 'padding_bottom': 90}))

    distanze = t.classic('statstrip', {
        'items': [{'value': '9′', 'label': 'al lungolago, a piedi'}, {'value': '6′', 'label': 'al Vittoriale, salendo'},
                  {'value': '12′', 'label': 'all’imbarcadero per Limone'}, {'value': '8′', 'label': 'a Salò, in auto'},
                  {'value': '70′', 'label': 'all’aeroporto di Verona'}, {'value': '1h50', 'label': 'a Milano'}],
        'columns': 6, 'columns_tablet_landscape': 3, 'columns_tablet': 3, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': INK, 'value_size': 52, 'value_weight': '200',
        'label_color': TENUE, 'label_size': 11, 'label_uppercase': True, 'label_weight': '500', 'align': 'left',
        'mono_font_family': 'sans-serif',
    })
    out.append(t.section([
        t.one([sechead(t, '01', 'Le distanze', ['Tutto a piedi,', 'tranne Milano.'], size=64,
                       tagline='Misurate dal cancello di via dei Colli, a passo normale e col traffico di un martedì di ottobre.')]),
        t.one([distanze]),
    ], padding='large'))

    righe = []
    for i in range(0, 4, 2):
        cols = []
        for mese, frase, f in STAGIONI[i:i + 2]:
            cols.append(t.col([image(t, f, mese + ': ' + frase, ratio='5/4'),
                               label(t, mese, color=INK, pad_bottom=6, pad_top=16),
                               text(t, '<p>' + frase + '</p>', size=15, pad=(0, 0, 0, 0))], wm='1-2', ws='1-1',
                              style={'padding_top': 12}))
        righe.append(t.row(cols, layout='50-50', gap=40, style={'margin_bottom': 48}))
    out.append(t.section([
        t.one([sechead(t, '02', 'Le stagioni', ['Un anno', 'sulla collina.'], size=64, layout='stack')]),
    ] + righe, bg=solid(LINO), padding='large'))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-39726130.jpg', 'Un pontile di legno che entra nel lago nella nebbia', ratio='4/5')],
                     wm='1-2', ws='1-1'),
               t.col([label(t, '03 — Il lago d’inverno'),
                      headline(t, 'D’inverno il lago è <em>tutto vostro</em>.', size=64, tag='h2', weight='200', pad_bottom=16),
                      text(t, '<p>Da novembre a marzo i battelli passano ogni ora, le spiagge sono vuote e i pontili si '
                              'possono usare per leggere. Gardone ha il suo cinema d’essai, due panetterie e il mercato '
                              'del mercoledì.</p>', size=16)], wm='1-2', ws='1-1')],
              layout='50-50', gap=80, valign='center'),
    ], padding='large'))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '14', 'height': '520', 'marker': True,
                                 'marker_popup': 'Undici Ulivi · via dei Colli 11', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'positron', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, '04', 'La mappa', ['Via dei Colli,', 'numero undici.'], size=56, layout='stack', size_mobile=38),
                      contacts(t),
                      text(t, '<p>Dalla Gardesana si sale da via Roma verso il Vittoriale e si prende la prima a destra dopo '
                              'il cancello. Il cantiere ha un parcheggio per le visite.</p>', size=15, pad=(16, 0, 0, 0))],
                     wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], bg=solid(LINO), padding='large', html_id='mappa'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  IL PROGETTO
# ══════════════════════════════════════════════════════════════════════════════

STORIA = [
    ('1708', 'Il catasto teresiano', 'La collina è già un uliveto a terrazze, con un muro a secco ogni due metri di quota.', ''),
    ('1841', 'La limonaia', 'I pilastri in pietra per i limoni: d’inverno si chiudevano con le assi e le vetrate.', ''),
    ('1921', 'D’Annunzio arriva a Gardone', 'Compra la villa che diventerà il Vittoriale, trecento metri più su.', ''),
    ('1974', 'L’abbandono', 'Gli ultimi limoni muoiono col gelo; l’uliveto si raccoglie a mano fino al 1990, poi più niente.', ''),
    ('2022', 'Il rilievo', 'Contiamo gli alberi: trecentododici, il più vecchio è del Seicento. Diamo a ognuno un numero.', ''),
    ('2025', 'Il primo scavo', 'Si comincia dalla limonaia, restaurata con la Soprintendenza.', ''),
    ('2027', 'Le chiavi', 'In primavera, con le olive dell’autunno prima già in bottiglia.', ''),
]


def build_progetto():
    t = Tpl(ID, 'progetto')
    out = [intro(t, '', 'Il progetto', ['Disegnate dalla collina,', 'non sulla collina.'],
                 '<p>Lo studio Ferlini Guerini ha passato un anno a misurare: gli alberi, i muri, il sole a ogni ora. Le case sono '
                 'venute dopo, negli spazi rimasti.</p>')]

    def architetto(foto, nome, ruolo, bio, pos):
        return [image(t, foto, nome + ', ' + ruolo.lower(), ratio='4/5', position=pos),
                headline(t, nome, size=30, tag='h3', pad_bottom=2),
                label(t, ruolo, pad_bottom=8, pad_top=4),
                text(t, '<p>' + bio + '</p>', size=15)]
    out.append(t.section([
        t.row([t.col(architetto('pexels-5292204.jpg', 'Marta Ferlini', 'Architetta · Brescia',
                                 'Ha disegnato le Case sull’uliveto e la luce di ogni stanza: una finestra per ogni ora.',
                                 'center 30%'), wm='1-3', ws='1-1'),
               t.col(architetto('pexels-6568669.jpg', 'Paolo Guerini', 'Architetto · Brescia',
                                 'Ha restaurato la limonaia e scelto i materiali, campione per campione, sul muro dello studio.',
                                 'center 35%'), wm='1-3', ws='1-1'),
               t.col([label(t, 'Lo studio'),
                      text(t, '<p>Ferlini Guerini architetti, Brescia, dal 2006. Case, cantine e il restauro di tre limonaie '
                              'fra Gargnano e Limone. Nel 2024 il premio regionale per l’architettura del paesaggio.</p>'
                              '<p>In cantiere sono loro due, ogni sabato mattina.</p>', size=16)], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=48, valign='start'),
    ], padding='large'))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-9052535.jpg', 'Le mani di un architetto che disegnano una pianta con riga e penna',
                            ratio='5/4'), label(t, 'Prima la matita', pad_bottom=0, pad_top=14)], wm='1-2', ws='1-1',
                     style={'padding_top': 0}),
               t.col([image(t, 'pexels-9618453.jpg', 'Un architetto che costruisce il plastico col compasso', ratio='5/4'),
                      label(t, 'Poi il legno', pad_bottom=0, pad_top=14)], wm='1-2', ws='1-1')],
              layout='50-50', gap=32),
    ], padding='remove-vertical', style={'padding_bottom': 40}))

    filo = {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}
    storia = [t.row([t.col([label(t, anno, color=OLIVA, pad_bottom=0, pad_top=6)], wm='1-6', ws='1-1'),
                     t.col([headline(t, titolo, size=28, tag='h3', pad_bottom=0)], wm='1-3', ws='1-1'),
                     t.col([text(t, '<p>' + frase + '</p>', size=15)], wm='1-2', ws='1-1')],
                    layout='custom', custom='16,34,50', gap=24, valign='start',
                    style={'border': filo, 'padding_top': 22, 'padding_bottom': 18})
              for anno, titolo, frase, _ in STORIA]
    out.append(t.section([
        t.one([sechead(t, '01', 'La storia', ['Trecento anni', 'in sette righe.'], size=56, size_mobile=38,
                       tagline='Dal catasto di Maria Teresa alle chiavi: la collina è cambiata poco, ed è il motivo per cui ci '
                               'abbiamo messo tre anni.')]),
    ] + storia + [t.one([], anim=None, style={'border': filo})], padding='large'))

    consumi = t.classic('chart', {
        'chart_type': 'bar', 'index_axis': 'y',
        'items': [{'label': 'Casa degli anni Settanta', 'value': '182', 'color': mix(INK, 25)},
                  {'label': 'Casa nuova in classe A1', 'value': '58', 'color': mix(INK, 45)},
                  {'label': 'Undici Ulivi, classe A4', 'value': '14', 'color': OLIVA}],
        'chart_height': '300', 'show_legend': False, 'tooltip_enabled': True, 'tooltip_bg': INK, 'tooltip_text_color': FONDO,
        'tooltip_suffix': ' kWh/m² l’anno', 'tooltip_prefix': '', 'animate': True, 'bar_radius': '0', 'bar_percentage': '0.55',
        'category_percentage': '0.9', 'grid_color': FILO, 'text_color': TENUE, 'tick_font_size': 12, 'show_x_grid': True,
        'show_y_grid': False, 'show_x_border': False, 'show_y_border': True, 'begin_at_zero': True,
        'dataset_label': 'Energia l’anno', 'border_width': 0, 'border_color_override': '', 'fill_area': False,
        'x_label': 'kWh per metro quadro, ogni anno', 'y_label': '',
    })
    out.append(t.section([
        t.row([t.col([sechead(t, '02', 'L’energia', ['Quattordici', 'chilowattora.'], size=56, layout='stack', size_mobile=38),
                      text(t, '<p>È quanto consuma in un anno ogni metro quadro di una Casa sull’uliveto, riscaldamento e fresco '
                              'compresi. Il resto lo fanno i muri spessi, la terra sui tetti e la geotermia.</p>')],
                     wm='1-3', ws='1-1'),
               t.col([consumi], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='center'),
    ], bg=solid(LINO), padding='large'))

    cantiere = t.classic('progresstracker', {
        'items': [{'title': 'Il rilievo', 'description': 'I 312 ulivi numerati, i muri a secco misurati.', 'icon': 'map', 'status': 'completed'},
                  {'title': 'La limonaia', 'description': 'Restaurata, coi vetri d’inverno.', 'icon': 'home', 'status': 'completed'},
                  {'title': 'Le strutture', 'description': 'Tutte le undici case, in cemento e laterizio.', 'icon': 'layers', 'status': 'completed'},
                  {'title': 'I tetti giardino', 'description': 'Terra, timo e lavanda: ottobre e novembre.', 'icon': 'feather', 'status': 'active'},
                  {'title': 'Le finiture', 'description': 'Rovere, calce, pietra. Si scelgono insieme.', 'icon': 'droplet', 'status': 'pending'},
                  {'title': 'Le chiavi', 'description': 'Primavera 2027.', 'icon': 'key', 'status': 'pending'}],
        'layout': 'horizontal', 'connector_style': 'solid', 'connector_color': FILO, 'completed_color': INK,
        'active_color': OLIVA, 'pending_color': mix(INK, 30), 'text_color': INK, 'show_description': True, 'show_numbers': False,
        'circle_size': '42', 'font_size': '15', 'gap': '0', 'shadow': 'none',
    })
    out.append(t.section([
        t.one([sechead(t, '03', 'Il cantiere', ['A che punto', 'siamo.'], size=56,
                       tagline='Aggiornato il primo lunedì del mese. Chi ha comprato riceve le foto del cantiere ogni venerdì.')]),
        t.one([cantiere]),
        t.row([t.col([image(t, f, a, ratio='4/3')], wm='1-3', ws='1-1') for f, a in [
            ('pexels-15458299.jpg', 'Le fondazioni in cemento di una casa, al sole'),
            ('pexels-18080900.jpg', 'Le casseforme di legno di un solaio contro il cielo'),
            ('pexels-8482816.jpg', 'Un muratore che controlla la parete con la livella')]],
              layout='33-33-33', gap=20, style={'margin_top': 56}),
    ], padding='large', html_id='cantiere'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  VISITA
# ══════════════════════════════════════════════════════════════════════════════

def build_visita():
    t = Tpl(ID, 'visita')
    out = []
    modulo = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome e cognome', 'name': 'nome', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'tel', 'label': 'Telefono', 'name': 'telefono', 'placeholder': '', 'required': False, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'La casa che vi interessa', 'name': 'casa', 'placeholder': '', 'required': False, 'width': '1-2',
             'options': 'Non lo so ancora\nCasa sull’uliveto\nCasa lunga\nCasa alta\nCasa della limonaia', 'icon': '', 'param': 'casa'},
            {'field_type': 'date', 'label': 'Il giorno della visita', 'name': 'giorno', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'A che ora', 'name': 'ora', 'placeholder': '', 'required': True, 'width': '1-2',
             'options': 'Mattina, 10 — 13\nPomeriggio, 14 — 18\nDi sera, con le luci accese', 'icon': ''},
            {'field_type': 'textarea', 'label': 'Qualcosa da dirci', 'name': 'messaggio', 'placeholder': 'Quante persone, un cane, una domanda…',
             'required': False, 'width': '1-1', 'options': '', 'icon': ''},
        ],
        'email_subject': 'Visita a Undici Ulivi', 'submit_text': 'Prenota la visita', 'submit_icon': '',
        'success_message': 'Ricevuto. Vi confermiamo il giorno entro domani, con il punto d’incontro.',
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': INK, 'label_size': '11', 'label_weight': '500',
        'label_transform': 'uppercase', 'label_letter_spacing': '2', 'input_bg': 'transparent', 'input_color': INK,
        'input_border_color': FILO_INK, 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 0,
        'input_focus_border': OLIVA, 'input_focus_shadow': False, 'gap': '20', 'submit_bg': INK, 'submit_color': FONDO,
        'submit_radius': 0, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '11',
        'submit_font_weight': '500', 'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2.5',
        'submit_hover_bg': OLIVA, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, '', 'La visita', ['Prenota', 'una visita.'], size=96, layout='stack', size_mobile=46),
                      text(t, '<p>Un’ora in cantiere con gli architetti, la casa che vi interessa e quella accanto. Il caschetto '
                              'e le scarpe le portiamo noi. Di sera, su richiesta, con le luci delle case finite.</p>', size=16,
                           pad=(0, 0, 36, 0)),
                      modulo], wm='3-5', ws='1-1'),
               t.col([t.bare('video', {
                   'source_type': 'file', 'video_url': '', 'file_url': img(V_TENDA + '.mp4'), 'poster_image': img(V_TENDA + '.jpg'),
                   'display_mode': '4:5', 'aspect_ratio': '4:5', 'cover_mode': False, 'object_position': 'center center',
                   'border_radius': 0, 'autoplay': True, 'muted': True, 'loop': True, 'controls': False,
                   'show_play_icon': False, 'facade': False}),
                   label(t, 'La finestra della camera, Casa 03', pad_bottom=0, pad_top=14)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='start'),
    ], padding='large', style={'padding_top': 150}))

    out.append(t.section([t.one([hours(t)])], bg=solid(LINO), padding='default'))

    out.append(t.section([
        t.row([t.col([sechead(t, '', 'Le domande', ['Quello che', 'ci chiedono tutti.'], size=56, layout='stack', size_mobile=38),
                      contacts(t)], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Si può cambiare la pianta?', 'Fino alla fine delle strutture sì: le pareti interne, i bagni, la cucina. Lo '
                                                  'studio rifà i disegni senza costi fino a due versioni.'),
                   ('Quando si firma, quando si paga?', 'Il preliminare con il 20%, il 30% a fine strutture, il resto al rogito '
                                                       'in primavera 2027. Le somme sono garantite da fideiussione.'),
                   ('Quanto costano le spese comuni?', 'Circa 140 € al mese per casa: il cancello, la strada, il giardiniere degli '
                                                      'ulivi e la raccolta delle olive.'),
                   ('Si può affittare la casa?', 'Sì, anche per brevi periodi. Il regolamento chiede solo il silenzio dopo le '
                                                 'ventidue, come in un uliveto.'),
                   ('Cosa succede agli ulivi?', 'Restano di tutti. Un arboricoltore li pota a febbraio, la raccolta è a novembre e '
                                               'chi vuole raccoglie insieme a noi.'),
               ], 'faq')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404', align='center'),
                     headline(t, 'Questa pagina non c’è.<br><em>Gli ulivi, sì.</em>', size=104, tag='h1', align='center',
                              weight='200', pad_bottom=20),
                     text(t, '<p>Forse l’indirizzo è cambiato quando abbiamo venduto una casa. Le altre sono qui.</p>',
                          size=16, align='center'),
                     button(t, 'Torna alla home', HOME, align='center')]),
    ], padding='xlarge', style={'padding_top': 200, 'padding_bottom': 200})]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Casa e design', 'zone': 'Quale casa è ancora libera?',
    'tags': ['immobiliare', 'residenze', 'architettura', 'lago di garda', 'minimale'],
    'description': 'Undici residenze fra gli ulivi sopra il lago di Garda, minimale e a due toni: lino e verde oliva, un serif '
                   'sottilissimo, l’apertura con la riga che si disegna, le case da toccare sul plastico, una giornata che scorre di '
                   'lato, i materiali sotto la lente, la foto che segue il cursore e la palette della sera.',
    'menu': {'name': 'Undici Ulivi — menu', 'items': [
        {'title': 'Le residenze', 'url': RESIDENZE}, {'title': 'Il luogo', 'url': LUOGO}, {'title': 'Il progetto', 'url': PROGETTO},
        {'title': 'Prenota una visita', 'url': VISITA}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Undici Ulivi — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Undici Ulivi — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Undici Ulivi — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Undici Ulivi — Home'},
        'residenze': {'file': 'residenze.json', 'type': 'page', 'title': 'Undici Ulivi — Le residenze'},
        'il-luogo': {'file': 'il-luogo.json', 'type': 'page', 'title': 'Undici Ulivi — Il luogo'},
        'progetto': {'file': 'progetto.json', 'type': 'page', 'title': 'Undici Ulivi — Il progetto'},
        'visita': {'file': 'visita.json', 'type': 'page', 'title': 'Undici Ulivi — Prenota una visita'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'residenze': {'title': 'Le residenze', 'template': 'residenze', 'slug': 'residenze'},
        'il-luogo': {'title': 'Il luogo', 'template': 'il-luogo', 'slug': 'il-luogo'},
        'progetto': {'title': 'Il progetto', 'template': 'progetto', 'slug': 'progetto'},
        'visita': {'title': 'Prenota una visita', 'template': 'visita', 'slug': 'visita'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 30, 'ring_color': HEX['primary'], 'ring_width': 1,
               'dot_size': 4, 'dot_color': HEX['primary'], 'hot_scale': 2.2, 'hot_color': HEX['primary'],
               'hot_fill': rgba(HEX['primary'], 0.08), 'blend_mode': 'normal', 'magnetic_selector': 'a, button',
               'pull_strength': 0.25, 'follow_easing': 0.16},
    'styles': {
        'colors': dict(HEX),
        'dark_colors': dict(DARK),
        'typography': {
            'font_family': '\'Albert Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Spectral\', Georgia, \'Times New Roman\', serif',
            'font_family_mono': '\'Albert Sans\', system-ui, sans-serif',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '200',
            'heading_line_height': '1.02', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '6.5rem', 'font_size_h2': '4.5rem', 'font_size_h3': '2.4rem',
            'font_size_h4': '1.6rem', 'font_size_h5': '1.2rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3.2rem',
            'font_size_h2_mobile': '2.5rem', 'font_size_h3_mobile': '1.8rem',
        },
        'google_fonts': ['Spectral', 'Albert Sans'],
        'google_fonts_weights': '200;300;400;500;600',
        'google_fonts_italic': ['Spectral'],
        'buttons': {'font_size': '11px', 'font_weight': '500', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '2.5px', 'hover_brightness': '100'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': 'transparent', 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '0px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '500', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 4, 'scale': 180},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'residenze.json': build_residenze(), 'il-luogo.json': build_luogo(), 'progetto.json': build_progetto(),
              'visita.json': build_visita(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
