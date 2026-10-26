# -*- coding: utf-8 -*-
"""Genera il tema «Bagnasciuga» (id bagnasciuga): gelateria sul lungomare di Cesenatico, dal 1958.

Riviera anni Cinquanta: panna, amarena, menta e cialda; il nome in corsivo da insegna (Yellowtail), i titoli in
un bastoni-e-grazie da manifesto (Abril Fatface), il testo in Rethink Sans. Ogni pagina si apre sotto il tendone a
righe con la mantovana smerlata (pattern a righe verticali + Shape Divider «Gocce» sotto la sezione); la sezione
del laboratorio cola cioccolato su quella dopo (Shape Divider «Colature»).

Moduli: la colonna dei gusti che scorre (Scorrimento verticale, mai usato prima in un tema), la pellicola delle
estati (Film Reel), la lavagna del giorno (Chalkboard), il conto alla rovescia della riapertura con i numeri nelle
palline, la torta gelato da comporre col prezzo che si aggiorna (Configuratore → Form), la storia a polaroid
(Timeline), le carte della frutta da girare (Flip Card), le schede che si impilano del laboratorio (Stack Scroll).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, lib, mix, radius, corners, solid, rgba  # noqa: E402

ID = 'bagnasciuga'
NAME = 'Bagnasciuga'

HEX = {
    'primary': '#C2183F', 'primary_contrast': '#FFF6E8', 'secondary': '#BFE6D3', 'secondary_contrast': '#2B1810',
    'accent': '#F2C14E', 'dark': '#2B1810', 'light': '#FFFFFF', 'muted': '#F8E6CF', 'muted_contrast': '#3A2116',
    'text': '#3A2116', 'text_muted': '#6E5244', 'background': '#FFF6E8', 'border': '#EED9BF', 'link': '#A5122F',
}

INK = 'var(--olo-color-text)'
MUTED = 'var(--olo-color-text-muted)'
AMARENA = 'var(--olo-color-primary)'
ON_AMARENA = 'var(--olo-color-primary-contrast)'
MENTA = 'var(--olo-color-secondary)'
CIALDA = 'var(--olo-color-accent)'
CACAO = 'var(--olo-color-dark)'
PANNA = 'var(--olo-color-background)'
SABBIA = 'var(--olo-color-muted)'
WHITE = 'var(--olo-color-light)'
LINK = 'var(--olo-color-link)'
LINE = mix(INK, 14)
ON = '#FFF6E8'
ON_SOFT = rgba('#FFF6E8', 0.86)
ON_DIM = rgba('#FFF6E8', 0.66)
SCRIPT = "'Yellowtail', cursive"   # apici singoli: le doppie chiuderebbero l'attributo style
PILL = radius(999)

ADDRESS = 'Viale Carducci 112, 47042 Cesenatico (FC)'
PHONE = '0547 000 000'
PHONE_TEL = 'tel:+390547000000'
MAIL = 'ciao@bagnasciuga.example'
LAT, LNG = '44.2009', '12.4048'
GUSTI = '{{PAGINA:gusti}}'
LAB = '{{PAGINA:laboratorio}}'
TORTE = '{{PAGINA:torte-e-vaschette}}'
HOME = '{{PAGINA:home}}'

V_HERO = 'pexels-video-5659616'        # la spatola nel caramello
V_CONO = 'pexels-video-5659616-cono'   # il cono riempito a spatola
V_MANTE = 'pexels-video-5659912'       # il gelato che esce dal mantecatore
V_SPIAGGIA = 'pexels-video-20156106'   # gli ombrelloni dal drone
V_SCIOLTO = 'pexels-video-8093894'     # il cono caduto che si scioglie

PHOTOS = [
    'pexels-5108033.jpg', 'pexels-12551947.jpg', 'pexels-24427097.jpg', 'pexels-8734293.jpg', 'pexels-23884580.jpg',
    'pexels-20704514.jpg', 'pexels-20446421.jpg', 'pexels-5060942.jpg', 'pexels-39641643.jpg', 'pexels-37280334.jpg',
    'pexels-29864467.jpg', 'pexels-27367377.jpg', 'pexels-28007084.jpg', 'pexels-18894624.jpg', 'pexels-37176404.jpg',
    'pexels-25391594.jpg', 'pexels-8926029.jpg', 'pexels-8777829.jpg', 'pexels-4870551.jpg', 'pexels-8888514.jpg',
    'pexels-31756481.jpg', 'pexels-10066836.jpg', 'pexels-40479201.jpg', 'pexels-4040888.jpg', 'pexels-8705929.jpg',
    'pexels-8724096.jpg', 'pexels-15047607.jpg', 'pexels-13683072.jpg', 'pexels-9227980.jpg', 'pexels-9227978.jpg',
    'pexels-24828974.jpg', 'pexels-33647547.jpg', 'pexels-5425021.jpg', 'pexels-31462802.jpg', 'pexels-9306017.jpg',
    'pexels-37657828.jpg', 'pexels-7761791.jpg', 'pexels-6182388.jpg', 'pexels-8426675.jpg', 'pexels-16537980.jpg',
]
TONDI = ['39641643', '24828974', '33647547', '5425021', '9306017', '37657828', '31462802', '24427097', '5060942', '9227980']
VIDEOS = [V_HERO, V_CONO, V_MANTE, V_SPIAGGIA, V_SCIOLTO]
MEDIA = set(PHOTOS) | {'pexels-%s-tondo.jpg' % k for k in TONDI} | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def tondo(k):
    return img('pexels-%s-tondo.jpg' % k)


def video_bg(name, overlay=40, color=CACAO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=40, color=CACAO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def tendone(t):
    """Il tendone a righe sotto la testata, con la mantovana smerlata che scende sulla sezione dopo."""
    righe = {'type': 'pattern', 'pattern_type': 'vertical-lines', 'pattern_color': HEX['primary'],
             'pattern_bg_color': HEX['background'], 'pattern_size': 24, 'pattern_thickness': 12, 'pattern_opacity': 100,
             'pattern_rotation': 0}
    smerlo = t.classic('shapedivider', {'shape': 'drops', 'position': 'bottom', 'flip_vertical': False, 'flip_horizontal': False,
                                        'color': AMARENA, 'height': 26, 'width': '100', 'z_index': '3',
                                        'responsive_height_tablet': '22', 'responsive_height_mobile': '16'})
    spazio = t.bare('spacer', {'height': '30'})
    return t.section([t.one([spazio, smerlo], anim=None)], bg=righe, width='fullbleed', padding='remove-vertical')


def sechead(t, eyebrow, lines, tagline='', size=58, align='left', light=False, size_mobile=38, layout='split', ratio='1.25fr 1fr'):
    """Occhiello amarena, titolo in Abril Fatface con la seconda riga in amarena, sottotitolo."""
    ink = ON if light else INK
    acc = HEX['accent'] if light else AMARENA
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': HEX['accent'] if light else LINK,
        'eyebrow_dot_color': acc, 'eyebrow_separator': '', 'eyebrow_font_family': 'body', 'eyebrow_font_size': 13,
        'eyebrow_font_weight': '800',
        'headline_lines': [{'text': l, 'color': acc if i == 1 else ink, 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.02,
        'headline_font_weight': '400', 'headline_align': align,
        'headline_font_size_tablet': max(34, int(size * 0.8)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False,
        'tagline_text_color': ON_SOFT if light else MUTED, 'tagline_text_size': 18, 'tagline_font_family': 'body',
        'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': ratio, 'gap': 20,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 28, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def script(t, label, size=44, color=AMARENA, align='left', pad_bottom=4, tag='p'):
    return t.classic('headline', {'heading': label, 'tag': tag, 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': False, 'heading_font_size': size, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': SCRIPT,
                                  'heading_font_size_mobile': str(max(30, int(size * 0.72)))},
                     style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def headline(t, label, size=34, color=INK, tag='h3', pad_bottom=10, align='left', accent='', accent_color=AMARENA):
    o = {'heading': label, 'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': '400', 'heading_color': color, 'heading_font': 'heading'}
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.82)), 'heading_font_size_mobile': str(max(28, int(size * 0.6)))})
    if accent:
        o.update({'accent_text': accent, 'accent_color': accent_color, 'accent_italic': False})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=18, color=MUTED, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.7, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label, url, kind='amarena', align='left', pad_top=8, inline=False):
    if kind == 'amarena':
        o = {'bg_color': AMARENA, 'text_color': ON_AMARENA, 'hover_bg_color': CACAO, 'hover_text_color': ON}
    elif kind == 'panna':
        o = {'bg_color': ON, 'text_color': HEX['primary'], 'hover_bg_color': CIALDA, 'hover_text_color': HEX['dark']}
    elif kind == 'cacao':
        o = {'bg_color': CACAO, 'text_color': ON, 'hover_bg_color': AMARENA, 'hover_text_color': ON}
    else:  # filo
        o = {'bg_color': 'transparent', 'text_color': INK, 'hover_bg_color': CACAO, 'hover_text_color': ON,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 2,
             'shadow_color': mix(INK, 30), 'shadow_inset': True}
    o.update({'text': label, 'url': url, 'alignment': align, 'border_radius': 999, 'font_size': 16, 'font_weight': '700',
              'text_transform': 'none', 'letter_spacing': '0'})
    return t.classic('button', o, style={'padding_top': pad_top}, advanced={'tile_width': 'inline'} if inline else None)


def titlebar(t, title_sub, image, position='center center', overlay=50):
    return t.classic('pagetitlebar', {
        'media_bg': {'type': 'image', 'image_url': img(image), 'image_size': 'cover', 'image_position': position},
        'bg': {'type': 'none'}, 'title_tag': 'h1', 'title_color': ON, 'title_size': 88, 'title_weight': '400',
        'title_align': 'left', 'subtitle': title_sub, 'subtitle_color': ON_SOFT, 'subtitle_size': 20,
        'show_breadcrumbs': True, 'breadcrumb_color': ON_DIM, 'breadcrumb_separator': '·',
        'bg_overlay': overlay, 'bg_overlay_color': CACAO, 'min_height': 440,
        'tile_padding': {'top': 110, 'right': 32, 'bottom': 64, 'left': 32}, 'content_width': 1200, 'padding_y': '40',
    })


def osm(t, height=440):
    return t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '16', 'height': str(height), 'marker': True,
                                'marker_popup': NAME + ' · Viale Carducci 112', 'scroll_wheel_zoom': False,
                                'tile_layer': 'voyager', 'border_radius': radius(28), 'marker_color': HEX['primary']},
                     style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'first', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': AMARENA, 'header_font_size': 24, 'header_font_weight': '400',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 17, 'text_color': MUTED,
        'icon_color': AMARENA, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 2, 'left': 0, 'linked': False, 'style': 'dashed', 'color': mix(AMARENA, 40)},
        'header_padding': {'top': 20, 'right': 0, 'bottom': 20, 'left': 0},
        'content_padding': {'top': 0, 'right': 40, 'bottom': 22, 'left': 0},
    }, style={})


def hours(t, light=False):
    return t.classic('hoursstrip', {
        'items': [
            {'day': 'Marzo — maggio', 'time': '12 — 20', 'note': 'Nel weekend fino alle 23'},
            {'day': 'Giugno — settembre', 'time': '10 — 1', 'note': 'Tutti i giorni, anche a Ferragosto'},
            {'day': 'Ottobre', 'time': '12 — 19', 'note': 'Fino al 2 novembre, poi il letargo'},
        ],
        'columns': 3, 'band_padding_y': 28, 'show_dividers': True,
        'divider_color': rgba('#FFF6E8', 0.2) if light else mix(INK, 16), 'band_border': False,
        'day_color': HEX['accent'] if light else LINK, 'day_size': 13, 'time_font_family': 'heading',
        'time_color': ON if light else INK, 'time_size': 46, 'time_weight': '400',
        'note_color': ON_DIM if light else MUTED, 'note_size': 14, 'mono_font_family': '',
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def contacts(t, light=False):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': HOME + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': HEX['accent'] if light else ON_AMARENA, 'icon_size': '18', 'text_color': ON if light else INK,
        'text_size': '16', 'gap': '14', 'icon_shape': 'circle',
        'icon_bg_color': rgba('#FFF6E8', 0.1) if light else AMARENA, 'divider': False, 'layout': 'vertical',
    }, style={})


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

TICKER = [
    'Oggi in vetrina: pesca di Romagna, zuppa inglese, pistacchio e il Bagnasciuga',
    'Il latte arriva ogni mattina alle 6:30 da una stalla di Gambettola',
    'Ultimi giorni: siamo aperti fino al 2 novembre',
    'Ventotto gusti su trentadue sono senza glutine',
]


def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': NAME, 'logo_text_size': '40', 'logo_text_color': AMARENA,
        'logo_text_font': SCRIPT, 'logo_text_weight': '400', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'classic', 'sticky': True,
        'sticky_bg': mix(PANNA, 96), 'sticky_shadow': True, 'sticky_shrink': True, 'sticky_show_on_up': False,
        'nav_bg': PANNA, 'text_color': INK, 'hover_color': AMARENA, 'active_color': AMARENA,
        'font_size': '16', 'font_weight': '700', 'text_transform': 'none', 'letter_spacing': '0', 'item_gap': '30',
        'bar_width': 'classic', 'nav_height': '78', 'bar_padding': {'top': 10, 'right': 28, 'bottom': 10, 'left': 28},
        'hover_effect': 'underline', 'hover_effect_color': AMARENA,
        'button_mode': 'last', 'btn_bg': AMARENA, 'btn_color': ON_AMARENA, 'btn_hover_color': ON_AMARENA,
        'btn_radius': PILL, 'btn_padding': {'top': 11, 'right': 22, 'bottom': 11, 'left': 22}, 'btn_hover_effect': 'lift',
        'btn_font_weight': '700', 'btn_transform': 'none', 'btn_letter_spacing': '0',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '#', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': PANNA, 'mobile_text_color': INK, 'mobile_accent_color': AMARENA,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '34', 'mobile_link_size': '34',
        'mobile_link_font': 'heading', 'mobile_link_weight': '400',
        'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''},
        'topbar_enabled': True, 'topbar_bg': AMARENA, 'topbar_text_color': ON, 'topbar_link_color': HEX['accent'],
        'topbar_border_bottom': False, 'topbar_font_size': '13', 'topbar_height': '38',
        'topbar_hide_mobile': True, 'topbar_hide_sticky': True,
        'topbar_left_content': 'ticker', 'topbar_ticker_label': 'In gelateria', 'topbar_ticker_items': '\n'.join(TICKER),
        'topbar_ticker_speed': '18',
        'topbar_right_text': 'Chiamateci <a href="' + PHONE_TEL + '">' + PHONE + '</a>',
        'topbar_right_social': False, 'topbar_right_search': False, 'topbar_right_cart': False,
        'topbar_right_cta_label': '', 'topbar_right_cta_url': '', 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def foot_title(t, label):
    return script(t, label, size=32, color=HEX['accent'], pad_bottom=6, tag='h4')


def foot_nav(t, items):
    return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items],
                             'style': 'default', 'direction': 'vertical', 'alignment': 'left',
                             'text_transform': 'none', 'letter_spacing': '0',
                             'link_color': ON_SOFT, 'link_hover_color': HEX['accent'], 'active_color': ON,
                             'font_size': '16', 'font_weight': '500',
                             'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})


def build_footer():
    t = Tpl(ID, 'footer')
    brand = script(t, NAME, size=76, color=ON, pad_bottom=0, tag='p')
    invite = headline(t, 'Un cono, due gusti, sessanta passi fino al mare.', size=34, color=ON_SOFT, tag='p', pad_bottom=0)
    invite_btn = button(t, 'I gusti di oggi', GUSTI, kind='panna', align='right')
    about = text(t, '<p>Gelateria artigianale sul lungomare di Cesenatico, dal 1958. Latte di Romagna, frutta del mercato, '
                    'niente basi pronte. Ivonne e Gino l’hanno aperta, oggi la portano avanti Marta e Luca.</p>',
                 size=15, color=ON_DIM, pad=(4, 0, 18, 0))
    quando = text(t, '<p><strong>Marzo — maggio</strong><br>12–20, nel weekend fino alle 23</p>'
                     '<p><strong>Giugno — settembre</strong><br>10–1, tutti i giorni</p>'
                     '<p><strong>Ottobre</strong><br>12–19, fino al 2 novembre</p>', size=15, color=ON_DIM)
    pagine = foot_nav(t, [('I gusti', GUSTI), ('Il laboratorio', LAB), ('Torte e vaschette', TORTE),
                          ('Dove siamo', HOME + '#dove'), ('Informativa privacy', '/privacy-policy/')])
    legal = text(t, '<p>© 2026 Bagnasciuga · Gelateria artigianale, Cesenatico. Gli allergeni di ogni gusto sono scritti sul '
                    'cartellino in vetrina: chiedeteci pure.</p>', size=13, color=rgba('#FFF6E8', 0.5))
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'facebook', 'url': 'https://facebook.com'}],
                                  'size': '34', 'alignment': 'right', 'icon_color': ON_DIM, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': HEX['accent'], 'posizione': 'fisso', 'style': 'default'})
    line = {'style': 'dashed', 'color': rgba('#FFF6E8', 0.18)}
    first = t.row([t.col([brand, invite], wm='2-3', ws='1-1'), t.col([invite_btn], wm='1-3', ws='1-1')],
                  layout='custom', custom='66.66,33.33', valign='end', anim='slide-up',
                  style={'border': dict(top=0, right=0, bottom=2, left=0, **line), 'padding_bottom': 34, 'margin_bottom': 40})
    top = t.row([
        t.col([foot_title(t, 'Dove'), contacts(t, light=True), about], wm='1-3', ws='1-1'),
        t.col([foot_title(t, 'Quando'), quando], wm='1-3', ws='1-1'),
        t.col([foot_title(t, 'Le pagine'), pagine], wm='1-3', ws='1-1'),
    ], layout='33-33-33', gap=48, anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': dict(top=2, right=0, bottom=0, left=0, **line), 'padding_top': 20, 'margin_top': 34})
    return [t.section([first, top, bottom], bg=solid(CACAO), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def scorrono(t):
    """Tre colonne che scorrono: gli ingredienti nei tondi, i nomi dei gusti fra le palline, altri tondi."""
    v = []
    for k in ['39641643', '24828974', '5425021', '9306017']:
        v.append({'tipo': 'immagine', 'immagine': tondo(k), 'alt': '', 'colonna': '1'})
    gusti = ['Pistacchio', 'Zuppa inglese', 'Pesca di Romagna', 'Fiordilatte', 'Bagnasciuga', 'Amarena', 'Nocciola tostata',
             'Limone e menta']
    for i, g in enumerate(gusti):
        if i:
            v.append({'tipo': 'separatore', 'colonna': '2'})
        v.append({'tipo': 'testo', 'testo': g, 'colonna': '2', 'colore': AMARENA if i % 2 else INK})
    for k in ['33647547', '31462802', '37657828', '24427097']:
        v.append({'tipo': 'immagine', 'immagine': tondo(k), 'alt': '', 'colonna': '3'})
    return t.bare('scorrimentoverticale', {
        'voci': v, 'colonne': '3', 'colonne_mobile': '2', 'durata': 26, 'direzione_1': 'giu', 'direzione_2': 'su',
        'direzione_3': 'giu', 'direzione_4': 'su', 'pausa_hover': True,
        'altezza': 580, 'altezza_mobile': 420, 'gap': 26, 'gap_colonne': 14, 'sfumatura_bordi': 16, 'allineamento': 'center',
        'testo_font_family': 'heading', 'testo_font_size': 27, 'testo_font_size_mobile': 22, 'testo_font_weight': '400',
        'testo_colore': INK, 'immagine_altezza': 170, 'immagine_raggio': PILL, 'separatore_colore': CIALDA,
    })


FILM = [
    ('pexels-13683072.jpg', '1962 · I primi tavolini', 'Il caffè', 'tall'),
    ('pexels-37280334.jpg', 'Il bagno 64, di fronte', 'La spiaggia', ''),
    ('pexels-8926029.jpg', 'I ghiaccioli dell’una', 'Agosto', 'tall'),
    ('pexels-5108033.jpg', 'I pozzetti col coperchio', 'La vetrina', ''),
    ('pexels-8734293.jpg', 'Il cono più alto dell’estate', 'Luglio', 'tall'),
    ('pexels-8777829.jpg', 'La compagnia del muretto', 'Le sere', ''),
]


def build_home():
    t = Tpl(ID, 'home')
    out = [tendone(t)]

    # Apertura: la spatola nel caramello, il velo panna a sinistra sotto il titolo.
    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Gelateria · Cesenatico, sul lungomare', 'eyebrow_dot': True,
        'title': 'Gelato <em>di spiaggia</em>,<br>dal 1958.',
        'subtitle': 'Latte di Romagna che arriva alle sei e mezza, frutta del mercato di Cesena e una spatola per pozzetto. '
                    'Trentadue gusti d’estate, tutti fatti qui dietro, a sessanta passi dalla sabbia.',
        'media_bg': {'type': 'video', 'video_url': img(V_HERO + '.mp4'), 'video_poster': img(V_HERO + '.jpg'),
                     'video_size': 'cover', 'video_position': '70% center'},
        'overlay_color': PANNA, 'overlay_top': 0.0, 'overlay_bottom': 0.92, 'overlay_sides': False, 'overlay_direction': 'left',
        'glow_on': True, 'glow_color': rgba(HEX['accent'], 0.28), 'glow_w': 760, 'glow_h': 520, 'glow_blur': 130,
        'glow_x': 16, 'glow_y': 70,
        'accent': AMARENA, 'text_color': INK, 'title_color': INK, 'subtitle_color': MUTED,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 104, 'title_font_weight': '400',
        'title_line_height': '0.98', 'title_letter_spacing': '-1', 'title_text_transform': 'none',
        'subtitle_font_size': 19, 'subtitle_max_width': 540,
        'min_height': '88vh', 'content_max_width': 1240, 'vertical_align': 'center', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 120, 'right': 32, 'bottom': 96, 'left': 32},
        'cta_text': 'I gusti di oggi', 'cta_url': GUSTI, 'cta_bg_color': AMARENA, 'cta_text_color': ON_AMARENA,
        'cta_radius': PILL, 'cta_size': '17', 'cta_style': 'filled', 'cta_font_weight': '700', 'cta_text_transform': 'none',
        'cta_letter_spacing': '0',
        'cta2_text': 'Componi una torta', 'cta2_url': TORTE + '#componi', 'cta2_text_color': INK, 'cta2_style': 'outline',
        'meta_text': 'Aperti tutti i giorni fino al 2 novembre · Viale Carducci 112',
        'scroll_hint': '', 'scene_depth': 'soft', 'scene_depth_fade': True, 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    # Le promesse, sulla fascia menta.
    trust = t.classic('trust-strip', {
        'items': [
            {'icon': 'milk', 'icon_color': LINK, 'text': 'Latte <b>di Romagna</b> ogni mattina', 'badge': ''},
            {'icon': 'cherry', 'icon_color': LINK, 'text': 'Frutta <b>dal mercato di Cesena</b>', 'badge': ''},
            {'icon': 'ban', 'icon_color': LINK, 'text': 'Niente <b>basi pronte né coloranti</b>', 'badge': ''},
            {'icon': 'wheat-off', 'icon_color': LINK, 'text': '<b>28 gusti su 32</b> senza glutine', 'badge': ''},
        ],
        'text_color': INK, 'text_size': 16, 'font_family': 'body', 'align': 'center', 'flow': 'wrap', 'gap': 26,
        'variant': 'inline', 'icon_size': 18, 'separator_char': '✻', 'separator_color': AMARENA,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([t.one([trust], anim='fade')], bg=solid(MENTA), padding='small'))

    # Trenta gusti: la colonna dei gusti che scorre.
    left = [sechead(t, 'In vetrina, oggi', ['Trentadue gusti,', 'una spatola ciascuno.'],
                    size=56, size_mobile=38),
            text(t, '<p>Ogni gusto ha il suo pozzetto col coperchio, la sua spatola e il suo cartellino con gli allergeni. '
                    'Le creme si fanno la mattina presto, la frutta appena arriva dal mercato: quando un gusto finisce, '
                    'finisce, e lo scriviamo sulla lavagna.</p>', size=18),
            t.classic('desclist', {
                'items': [{'term': 'Le creme', 'definition': 'Fiordilatte, nocciola, pistacchio, zuppa inglese, caffè…', 'icon': ''},
                          {'term': 'La frutta', 'definition': 'Sorbetti senza latte: pesca, fragola, limone, anguria…', 'icon': ''},
                          {'term': 'Della casa', 'definition': 'Il Bagnasciuga: fiordilatte, sale di Cervia e caramello', 'icon': ''}],
                'layout': 'inline', 'term_color': LINK, 'term_font_size': '14', 'term_font_weight': '800',
                'definition_color': INK, 'definition_font_size': '16', 'separator': True, 'border_color': mix(AMARENA, 25),
            }, style={'margin_top': 8, 'margin_bottom': 18}),
            button(t, 'Tutti i gusti', GUSTI, kind='amarena')]
    out.append(t.section([
        t.row([t.col(left, wm='1-2', ws='1-1'), t.col([scorrono(t)], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], padding='large', html_id='gusti'))

    # Il gusto della casa: il cono sotto il cielo nell'arco, il distintivo che gira.
    arco = t.classic('image', {'image_url': img('pexels-8734293.jpg'), 'alt_text': 'Un cono di fiordilatte alzato contro il cielo del mare',
                               'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/5',
                               'height': '', 'object_fit': 'cover', 'object_position': 'center 40%',
                               'border_radius': corners(999, 999, 28, 28), 'lightbox': False, 'shadow': 'none'})
    giro = t.classic('textpath', {'text': 'il gusto della casa · dal 1958 · sale di Cervia · ', 'path_preset': 'circle',
                                  'font_size': '19', 'text_color': AMARENA, 'letter_spacing': '3', 'animation': 'none',
                                  'spin': True, 'spin_speed': '18', 'spin_direction': 'cw', 'typography_preset': ''},
                     style={'tile_max_width': '210px'})
    casa = [giro,
            script(t, 'Bagnasciuga', size=78, color=AMARENA, pad_bottom=0, tag='h2'),
            headline(t, 'Fiordilatte, sale di Cervia e caramello bruciato.', size=34, pad_bottom=14),
            text(t, '<p>L’ha inventato Ivonne nel 1966, per i bagnini che volevano «qualcosa di salato». Il sale arriva '
                    'dalla salina di Cervia, a dieci chilometri; lo zucchero lo bruciamo in un paiolo di rame finché non '
                    'diventa scuro come il legno della barca di Gino.</p>', size=18),
            t.classic('badge', {'text': 'Senza glutine · contiene latte', 'variant': 'soft', 'bg_color': AMARENA,
                                'text_color': LINK, 'alignment': 'left'}),
            ]
    out.append(t.section([
        t.row([t.col([arco], wm='2-5', ws='1-1'), t.col(casa, wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], bg=solid(SABBIA), padding='large', html_id='della-casa'))

    # La riviera dal drone, coi numeri.
    numeri = t.classic('statstrip', {
        'items': [{'value': '1958', 'label': 'La prima vaschetta, al porto canale'},
                  {'value': '32', 'label': 'Gusti in vetrina d’estate'},
                  {'value': '60', 'label': 'Passi fino alla sabbia'},
                  {'value': '0', 'label': 'Basi pronte e coloranti'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 24, 'show_dividers': True, 'divider_color': rgba('#FFF6E8', 0.25), 'band_border': False,
        'value_font_family': 'heading', 'value_color': ON, 'value_size': 72, 'value_weight': '400',
        'label_color': ON_SOFT, 'label_size': 14, 'label_uppercase': False, 'label_weight': '600', 'align': 'left',
        'mono_font_family': 'body',
    })
    out.append(t.section([
        t.one([script(t, 'La riviera, da quassù', size=40, color=HEX['accent'], tag='p'),
               headline(t, 'Fra l’ombrellone e la vetrina ci sono sessanta passi.', size=58, color=ON, tag='h2', pad_bottom=36)],
              anim='fade'),
        t.one([numeri]),
    ], bg=video_bg(V_SPIAGGIA, overlay=38, color=CACAO), padding='xlarge', width='default'))

    # Dal latte al cono: il mantecatore in video e le quattro ore della mattina che si impilano.
    mante = t.classic('video', {'source_type': 'file', 'file_url': img(V_MANTE + '.mp4'), 'poster_image': img(V_MANTE + '.jpg'),
                                'display_mode': '4:3', 'aspect_ratio': '4:3', 'facade': False, 'autoplay': True, 'muted': True,
                                'loop': True, 'controls': False, 'show_play_icon': False, 'overlay_text': '',
                                'overlay_opacity': 0, 'border_radius': 28,
                                'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                       style={'border_radius': 28}, advanced={})
    pila = t.classic('stackscroll', {
        'top_offset': 110, 'top_step': 26, 'card_gap': 18, 'scale_on_stack': True, 'scale_amount': 5, 'card_min_height': 230,
        'round': 32, 'card_padding': {'top': 34, 'right': 34, 'bottom': 34, 'left': 34}, 'show_number': True,
        'media_position': 'right', 'object_position': 'center center', 'title_display': True,
        'shadow': 'md', 'shadow_color': mix(CACAO, 40),
        'cards': [
            {'eyebrow': 'Alle 6:30', 'title': 'Il latte', 'accent': '', 'media': img('pexels-9227978.jpg'), 'color': PANNA, 'text_color': INK,
             'text': 'Arriva in bidoni da una stalla di Gambettola, a dodici chilometri. Si pastorizza qui, a 85 gradi, con lo zucchero e la panna.'},
            {'eyebrow': 'Alle 8', 'title': 'La frutta', 'accent': '', 'media': img('pexels-24828974.jpg'), 'color': MENTA, 'text_color': HEX['dark'],
             'text': 'Luca torna dal mercato di Cesena con le cassette: si pela, si taglia e si frulla entro un’ora. Mai frutta surgelata.'},
            {'eyebrow': 'Alle 9:30', 'title': 'Il mantecatore', 'accent': '', 'media': img('pexels-12551947.jpg'), 'color': CIALDA, 'text_color': HEX['dark'],
             'text': 'Dieci minuti nel mantecatore per ogni gusto, poi il gelato scende nei pozzetti a meno 14 gradi. Ne facciamo poco, più volte al giorno.'},
            {'eyebrow': 'Alle 10', 'title': 'La vetrina', 'accent': '', 'media': img('pexels-5108033.jpg'), 'color': AMARENA, 'text_color': ON,
             'text': 'Si alzano le serrande. Il primo cono della giornata, di solito, è un pistacchio per il bagnino del 64.'},
        ]})
    out.append(t.section([
        t.one([sechead(t, 'Il laboratorio, ogni mattina', ['Dal latte al cono', 'prima delle dieci.'], light=True, size=56,
                       tagline='Il laboratorio è dietro la vetrina, con una finestra sul banco: chi passa presto ci vede lavorare.')]),
        t.row([t.col([mante, button(t, 'Com’è fatto il laboratorio', LAB, kind='panna', pad_top=22)], wm='1-2', ws='1-1'),
               t.col([pila], wm='1-2', ws='1-1')], layout='50-50', gap=56, valign='start'),
        t.one([t.classic('shapedivider', {'shape': 'drips', 'position': 'bottom', 'flip_vertical': False, 'flip_horizontal': False,
                                          'color': CACAO, 'height': 70, 'width': '100', 'z_index': '3',
                                          'responsive_height_tablet': '54', 'responsive_height_mobile': '40'})], anim=None),
    ], bg=solid(CACAO), padding='large', html_id='laboratorio'))

    # Sessant'anni di estati, in pellicola.
    film = t.classic('filmreel', {
        'title': 'Sessant’anni di estati', 'show_title': True, 'hint_text': 'Trascinate o usate la rotella', 'show_hint': True,
        'intro_eyebrow': 'L’album di Ivonne', 'intro_text': 'Il chiosco al porto canale, il bagno 64 di fronte, i ghiaccioli '
                         'all’ora del pranzo e il muretto delle sere d’agosto.', 'show_intro': True,
        'items': [{'image': img(f), 'name': n, 'tag': g, 'size': s, 'link': ''} for f, n, g, s in FILM],
        'rec_overlay': False, 'velocity_skew': True, 'skew_max': '6', 'progress_bar': True, 'scroll_mode': 'free',
        'accent': AMARENA, 'bg_color': PANNA, 'border_color': LINE,
    })
    out.append(t.section([t.one([film], anim='fade')], padding='remove-vertical', width='fullbleed'))

    # Una voce dal muretto.
    voce = t.classic('testimonial', {
        'quote': 'Vengo qui dal 1971: prima mi ci portava mio padre, ora porto i nipoti. Il pistacchio <em>è sempre lo stesso, '
                 'per fortuna.</em>', 'author_name': 'Giordano B.', 'author_role': 'da Forlì, ogni domenica d’estate',
        'rating': '5', 'text_color': INK, 'star_color': AMARENA, 'author_color': MUTED, 'quote_accent_color': AMARENA,
        'quote_font': 'heading', 'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': mix(AMARENA, 40),
    })
    out.append(t.section([t.one([voce], anim='fade')], padding='large'))

    # Il letargo: i giorni alla riapertura dentro le palline.
    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2027-03-01T11:00', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': False, 'expired_message': 'Siamo aperti: vi aspettiamo in vetrina!',
        'label_days': 'giorni', 'label_hours': 'ore', 'label_minutes': 'minuti', 'separator': '',
        'text_color': AMARENA, 'accent_color': AMARENA, 'number_font_size': 72, 'number_font_weight': '400',
        'label_font_size': '14', 'label_font_weight': '700', 'item_min_width': 170, 'item_bg_color': ON,
        'item_radius': PILL, 'item_padding': {'top': 44, 'right': 18, 'bottom': 44, 'left': 18},
        'tile_padding': {'top': 10, 'right': 0, 'bottom': 10, 'left': 0},
    })
    out.append(t.section([
        t.row([t.col([script(t, 'Il letargo', size=46, color=HEX['accent'], tag='p'),
                      headline(t, 'Chiudiamo il 2 novembre, riapriamo il primo marzo.', size=42, color=ON, tag='h2', pad_bottom=14),
                      text(t, '<p>D’inverno la vetrina dorme e noi andiamo a trovare i produttori. Le torte gelato però si '
                              'ordinano tutto l’anno, con due giorni di anticipo.</p>', size=17, color=ON_SOFT),
                      button(t, 'Ordina una torta', TORTE, kind='panna')], wm='2-5', ws='1-1'),
               t.col([conto], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(AMARENA), padding='large', html_id='riapertura'))

    # Dove e quando.
    out.append(t.section([
        t.row([t.col([sechead(t, 'Dove e quando', ['Sul lungomare,', 'davanti al bagno 64.'], size=50, size_mobile=36),
                      contacts(t), text(t, '<p>Da piazza Andrea Costa sono cinque minuti a piedi. Le biciclette si lasciano '
                                          'nella rastrelliera sotto il tendone; i cani bevono dalla ciotola accanto alla porta.</p>',
                                       size=16, pad=(18, 0, 0, 0))], wm='2-5', ws='1-1'),
               t.col([osm(t, 460)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
        t.one([hours(t)], style={'margin_top': 48}),
    ], padding='large', html_id='dove'))

    # Le torte.
    cta = t.classic('cta-banner', {
        'headline': 'Un compleanno, una festa sulla spiaggia?', 'headline_accent': 'C’è la torta gelato.',
        'headline_accent_italic': False,
        'subtitle': 'Dimensione, base, due o tre gusti e la scritta: componetela, il prezzo si aggiorna mentre scegliete.',
        'cta_text': 'Componi la torta', 'cta_url': TORTE + '#componi', 'cta2_text': 'Le vaschette', 'cta2_url': TORTE + '#vaschette',
        'cta2_bg': 'transparent', 'cta2_color': ON, 'cta2_border': rgba('#FFF6E8', 0.6),
        'bg': photo_bg('pexels-8888514.jpg', overlay=60, color=CACAO, position='center 66%'),
        'text_color': ON, 'accent_color': HEX['accent'], 'subtitle_color': ON_SOFT,
        'cta_bg': CIALDA, 'cta_bg_hover': ON, 'cta_color': HEX['dark'], 'cta_color_hover': HEX['dark'],
        'cta_radius': PILL, 'cta_radius_hover': PILL, 'headline_font_family': 'heading', 'headline_size': 56,
        'headline_weight': '400', 'subtitle_size': 18, 'layout': 'stack', 'banner_radius': radius(32),
        'banner_radius_hover': radius(32), 'banner_padding': {'top': 88, 'right': 40, 'bottom': 88, 'left': 40},
    })
    out.append(t.section([t.one([cta], anim='zoom-in')], padding='default'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  I GUSTI
# ══════════════════════════════════════════════════════════════════════════════

LAVAGNA = [
    ('Pesca di Romagna', 'le ultime pesche nettarine IGP', 'sorbetto', 'cerchio', 'ultimi giorni'),
    ('Zuppa inglese', 'crema, cacao e savoiardi all’alchermes', 'crema', 'nessuna', ''),
    ('Bagnasciuga', 'fiordilatte, sale di Cervia, caramello', 'della casa', 'stella', ''),
    ('Pistacchio', 'solo pistacchio di Bronte, salato', 'crema', 'nessuna', ''),
    ('Fico e noci', 'i fichi dell’orto di Ivonne', 'crema', 'sottolineato', 'nuovo'),
    ('Limone e menta', 'limoni di Sorrento, menta del vaso', 'sorbetto', 'nessuna', ''),
]

CREME = [
    ('pexels-9227978.jpg', 'Fiordilatte', 'Latte, panna, zucchero', 'Il bianco da cui partono tutti gli altri: latte della stalla di Gambettola, niente altro.'),
    ('pexels-39641643.jpg', 'Pistacchio', 'Bronte DOP · salato', 'Pasta pura di pistacchio di Bronte, tostato leggero e una punta di sale.'),
    ('pexels-5425021.jpg', 'Nocciola tostata', 'Piemonte IGP', 'Le tostiamo noi la mattina, in un forno piccolo: si sente dalla strada.'),
    ('pexels-31462802.jpg', 'Cioccolato fondente', '70% · senza latte', 'Fatto con l’acqua, come una volta: più scuro e più fresco.'),
    ('pexels-20704514.jpg', 'Crema della nonna', 'Tuorli e scorza di limone', 'La ricetta di Ivonne del 1958, scritta a matita sul quaderno a quadretti.'),
    ('pexels-10066836.jpg', 'Caffè della moka', 'Miscela romagnola', 'Si fa con la moka da dodici tazze, tre volte, finché non è abbastanza forte.'),
]

FRUTTA = [
    ('pexels-24828974.jpg', 'Fragola', 'Fragole di Cesena, zucchero e limone. Da maggio a luglio.'),
    ('pexels-9306017.jpg', 'Pesca di Romagna', 'Pesche e nettarine IGP, con la buccia. Da giugno a ottobre.'),
    ('pexels-33647547.jpg', 'Limone', 'Limoni di Sorrento spremuti la mattina e una foglia di menta.'),
    ('pexels-8724096.jpg', 'Anguria', 'Solo ad agosto, finché ci sono le angurie del campo di Gatteo.'),
]

PREZZI = [
    ('Cono o coppetta piccola', 'Due gusti', '3 €', 'pexels-24427097-tondo.jpg'),
    ('Cono o coppetta media', 'Tre gusti', '3,80 €', 'pexels-5060942-tondo.jpg'),
    ('Coppa grande', 'Quattro gusti e la panna montata', '5,50 €', 'pexels-9227980-tondo.jpg'),
    ('Panna montata', 'Montata ogni ora, senza zucchero aggiunto', '+0,50 €', 'pexels-9227978.jpg'),
    ('Affogato al caffè', 'Fiordilatte e un espresso della moka', '4,50 €', 'pexels-10066836.jpg'),
    ('Ghiacciolo di frutta', 'Limone, anguria o pesca, fatti a stampo', '2,50 €', 'pexels-8705929.jpg'),
]

FAQ_GUSTI = [
    ('Avete gusti senza lattosio?', 'Tutti i sorbetti di frutta e il cioccolato fondente sono fatti con l’acqua, senza latte. Sul cartellino di ogni pozzetto c’è la goccia verde.'),
    ('Il cono è senza glutine?', 'Il cono classico no; teniamo i coni senza glutine in una scatola chiusa e li serviamo con una spatola pulita. Chiedeteli alla cassa.'),
    ('Posso assaggiare prima di scegliere?', 'Certo: il cucchiaino di legno è lì apposta. Anche due, se siete indecisi.'),
    ('Perché alcuni gusti ci sono solo certi mesi?', 'Perché facciamo la frutta solo quando è di stagione: le fragole a maggio, le pesche d’estate, i fichi a settembre. Fuori stagione non le compriamo.'),
]


def build_gusti():
    t = Tpl(ID, 'gusti')
    out = [tendone(t)]
    out.append(t.section([t.one([titlebar(t, 'Trentadue d’estate, venti in primavera e in autunno. Le creme col latte di Romagna, '
                                             'la frutta solo di stagione.', 'pexels-12551947.jpg', overlay=46)], anim=None)],
                         width='fullbleed', padding='remove-vertical'))

    lavagna = t.classic('chalkboard', {
        'occhiello': 'Oggi in vetrina', 'titolo': 'I gusti del giorno', 'titolo_tag': 'h2', 'data_mostra': True,
        'data_formato': 'giorno_data',
        'voci': [{'id': 'bl-%d' % (i + 1), 'nome': n, 'descrizione': d, 'prezzo': p, 'giorni': '', 'evidenza': e, 'etichetta': lab}
                 for i, (n, d, p, e, lab) in enumerate(LAVAGNA)],
        'vuoto_testo': 'Stamattina la lavagna è ancora da scrivere: passate dalla vetrina.',
        'nota': 'Quando un gusto finisce, lo cancelliamo col dito.', 'firma': '— Marta, alle 9:40',
        'disegni': [{'id': 'bd-1', 'forma': 'cuore', 'icona': '', 'immagine': '', 'x': 92, 'y': 14, 'misura': 52, 'rotazione': 10,
                     'profondita': 60, 'colore': '', 'opacita': 80},
                    {'id': 'bd-2', 'forma': 'stella', 'icona': '', 'immagine': '', 'x': 6, 'y': 78, 'misura': 40, 'rotazione': -14,
                     'profondita': 40, 'colore': '', 'opacita': 75}],
        'scrittura': 'scrive', 'scrittura_velocita': 'veloce', 'disegni_tracciati': True, 'parallasse': True, 'intensita': 50,
        'mouse': True, 'fondo': mix(CACAO, 82, MENTA), 'accento_colore': CIALDA, 'texture': 'gesso', 'polvere': 50,
        'aloni': 40, 'cornice': 'legno', 'cornice_colore': mix(CACAO, 55, CIALDA), 'cornice_spessore': 16, 'appesa': True,
        'inclinazione': 1.0, 'shadow': 'xl', 'larghezza': 720, 'colonne': 1, 'gap': 14, 'puntini': True, 'separatore': 'scarabocchio',
        'allineamento': 'left', 'allineamento_mobile': 'left',
    })
    out.append(t.section([
        t.row([t.col([sechead(t, 'La lavagna', ['Cosa c’è', 'stamattina.'], size=56,
                              tagline='La scrive Marta alle nove e mezza, prima di alzare le serrande. I gusti che cambiano spesso sono questi; '
                                      'quelli di sempre li trovate qui sotto.', layout='stack'),
                      button(t, 'Ordina una vaschetta', TORTE + '#vaschette', kind='amarena')], wm='1-3', ws='1-1'),
               t.col([lavagna], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=56, valign='center'),
    ], padding='large', html_id='lavagna'))

    creme = t.classic('workgrid', {
        'items': [{'image': img(f), 'title': n, 'meta': m, 'description': d, 'tall': False, 'link_url': ''} for f, n, m, d in CREME],
        'columns': 3, 'items_gap': 30, 'media_aspect': '1/1', 'media_tall_aspect': '4/5', 'media_bg': SABBIA, 'hover_zoom': True,
        'title_font_family': 'heading', 'title_color': INK, 'title_size': 32, 'title_weight': '400', 'meta_color': LINK,
        'meta_size': 13, 'show_desc': True, 'desc_color': MUTED, 'desc_size': 16, 'mono_font_family': 'body',
        'card_style': 'none', 'card_radius': radius(28), 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([t.one([sechead(t, 'Le creme', ['Col latte di Romagna,', 'tutti i giorni.'], size=52)]), t.one([creme])],
                         bg=solid(SABBIA), padding='large', html_id='creme'))

    flips = []
    for f, n, d in FRUTTA:
        flips.append(t.col([t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': img(f), 'image_size': 'cover', 'image_position': 'center center',
                            'overlay_color': CACAO, 'overlay_opacity': 25},
            'front_title': n, 'front_description': 'Sorbetto · senza latte', 'front_text_color': ON, 'front_text_align': 'left',
            'front_valign': 'bottom', 'back_title': n, 'back_description': d, 'back_bg': MENTA, 'back_text_color': HEX['dark'],
            'back_text_align': 'left', 'back_valign': 'center', 'back_cta_text': 'Ordina la vaschetta',
            'back_cta_url': TORTE + '#vaschette', 'back_cta_bg': AMARENA, 'back_cta_color': ON, 'back_cta_radius': '999',
            'flip_direction': 'horizontal', 'flip_trigger': 'hover', 'card_height': '400', 'card_border_radius': '40',
            'card_shadow': 'md', 'title_size': '30', 'title_weight': '400',
        }, style={'margin_top': 0, 'margin_right': 6, 'margin_bottom': 12, 'margin_left': 6})], wm='1-4', ws='1-2'))
    out.append(t.section([
        t.one([sechead(t, 'La frutta', ['Solo quando', 'è di stagione.'], size=52,
                       tagline='Passate il dito (o il mouse) sulle carte: dietro c’è da dove arriva la frutta e in che mesi la trovate.')]),
        t.row(flips, layout='25-25-25-25', gap=24),
    ], padding='large', html_id='frutta'))

    listino = t.classic('pricelist', {
        'items': [{'title': n, 'description': d, 'price': p, 'image_url': img(f), 'highlighted': False, 'badge': ''}
                  for n, d, p, f in PREZZI],
        'separator_style': 'dotted', 'separator_color': mix(AMARENA, 40), 'title_color': INK, 'price_color': LINK,
        'description_color': MUTED, 'image_size': 72, 'image_border_radius': '999', 'show_image': True, 'price_position': 'right',
        'title_size': '22', 'description_size': '15', 'price_size': '22',
    }, style={'bg': solid(WHITE), 'padding_top': 28, 'padding_right': 36, 'padding_bottom': 28, 'padding_left': 36, 'border_radius': 28})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Il listino', ['Coni, coppette', 'e affogati.'], size=50, layout='stack',
                              tagline='La cialda la fa Gino, il fratello di Ivonne, a Gatteo. Il cono senza glutine c’è sempre: chiedetelo alla cassa.'),
                      t.classic('image', {'image_url': img('pexels-4040888.jpg'), 'alt_text': 'Due coni, uno rosso e uno al cacao, su un foglio a pois gialli',
                                          'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/3',
                                          'height': '', 'object_fit': 'cover', 'border_radius': radius(28), 'shadow': 'none'})],
                     wm='2-5', ws='1-1'),
               t.col([listino], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(MENTA), padding='large', html_id='listino'))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Allergeni e domande', ['Chiedeteci', 'tutto.'], size=50, layout='stack')], wm='1-3', ws='1-1'),
               t.col([faq(t, FAQ_GUSTI, 'bg-faq')], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=56),
    ], padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  IL LABORATORIO
# ══════════════════════════════════════════════════════════════════════════════

STORIA = [
    ('1958', 'Il chiosco al porto canale', 'Gli inizi', 'Ivonne e Gino aprono un chiosco di legno sul porto canale: tre gusti, una ghiacciaia e il cono a cinquanta lire.', 'pexels-13683072.jpg'),
    ('1966', 'Nasce il Bagnasciuga', 'Il gusto', 'I bagnini chiedono «qualcosa di salato»: Ivonne prova col sale di Cervia e il caramello. Non l’ha più tolto dalla vetrina.', 'pexels-27367377.jpg'),
    ('1974', 'Sul lungomare', 'La bottega', 'La gelateria si sposta in viale Carducci, davanti al bagno 64, col tendone a righe che c’è ancora.', 'pexels-37280334.jpg'),
    ('2003', 'I pozzetti', 'La vetrina', 'Arrivano i pozzetti col coperchio: il gelato resta al buio e al freddo giusto, e si serve con la spatola.', 'pexels-5108033.jpg'),
    ('2019', 'Marta e Luca', 'Oggi', 'I nipoti prendono il laboratorio: stesse ricette, il latte di una stalla sola e la frutta dal mercato.', 'pexels-25391594.jpg'),
]

ORE = [
    ('6:30', 'Il latte', 'Arriva da Gambettola e si pastorizza subito, con lo zucchero e la panna.'),
    ('8:00', 'Il mercato', 'Luca sceglie la frutta a Cesena: quello che è maturo oggi diventa sorbetto domani.'),
    ('9:30', 'Il mantecatore', 'Dieci minuti a gusto, un gusto alla volta, sempre in piccole quantità.'),
    ('15:00', 'Il secondo giro', 'D’estate si rifà quello che finisce: il pistacchio, di solito, due volte.'),
]

FORNITORI = [
    ('01', 'Il latte', '12', 'km', 'Una stalla sola, a Gambettola: sessanta vacche e il latte munto la sera prima.', 'milk'),
    ('02', 'La frutta', '15', 'km', 'Il mercato ortofrutticolo di Cesena, ogni mattina alle otto.', 'apple'),
    ('03', 'Il sale', '10', 'km', 'La salina di Cervia: il sale dolce, raccolto a mano a fine estate.', 'droplets'),
    ('04', 'Le nocciole', '410', 'km', 'Nocciole Piemonte IGP, che tostiamo noi la mattina.', 'nut'),
]


def build_laboratorio():
    t = Tpl(ID, 'laboratorio')
    out = [tendone(t)]
    out.append(t.section([t.one([titlebar(t, 'Dietro la vetrina c’è una stanza con un mantecatore, un paiolo di rame e una finestra '
                                             'sul banco. Da sessantotto anni.', 'pexels-9227978.jpg', overlay=48, position='center center')],
                                anim=None)], width='fullbleed', padding='remove-vertical'))

    storia = t.classic('timeline', {
        'items': [{'date': d, 'title': ti, 'tag': tag, 'description': ds, 'image': img(f), 'video': '', 'icon': 'star',
                   'category': 'primary', 'icon_color': '', 'status': ''} for d, ti, tag, ds, f in STORIA],
        'tl_layout': 'alt', 'tl_reveal': 'bloom', 'tl_theme': 'paper', 'tl_card': 'polaroid', 'tl_thread': 'dash',
        'tl_node': 'year', 'tl_color': 'cat', 'tl_media': 'on', 'tl_transparent': True, 'tl_title_size': 26,
        'tl_title_weight': '400', 'tl_title_family': 'heading', 'tl_yr_family': 'heading', 'tl_show_tag': True,
    })
    out.append(t.section([t.one([sechead(t, 'La storia', ['Sessantotto estati,', 'una ricetta per volta.'], size=56)]),
                          t.one([storia])], padding='large', html_id='storia'))

    cono = t.classic('video', {'source_type': 'file', 'file_url': img(V_CONO + '.mp4'), 'poster_image': img(V_CONO + '.jpg'),
                               'display_mode': '4:3', 'aspect_ratio': '4:3', 'facade': False, 'autoplay': True, 'muted': True,
                               'loop': True, 'controls': False, 'show_play_icon': False, 'overlay_text': '', 'overlay_opacity': 0,
                               'border_radius': 28,
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                      style={'border_radius': 28})
    ore = t.classic('process-steps', {
        'items': [{'number': n, 'title': ti, 'description': d} for n, ti, d in ORE],
        'columns': 2, 'gap': 16, 'auto_number': False, 'number_style': 'plain', 'number_color': CIALDA, 'number_bg': '',
        'number_size': 40, 'number_font': 'heading', 'number_weight': '400', 'title_color': ON, 'title_size': 24,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': ON_SOFT, 'desc_size': 16, 'align': 'left', 'item_gap': 8,
        'card_bg': rgba('#FFF6E8', 0.06), 'card_border': '', 'card_radius': radius(24), 'card_padding': 26,
        'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, 'Una giornata', ['Le ore del gelato.'], light=True, size=56,
                       tagline='Il gelato non si fa la sera per il giorno dopo: si fa la mattina, poco alla volta, e si rifà quando finisce.')]),
        t.row([t.col([cono], wm='1-2', ws='1-1'), t.col([ore], wm='1-2', ws='1-1')], layout='50-50', gap=48, valign='center'),
        t.one([t.classic('shapedivider', {'shape': 'drips', 'position': 'bottom', 'flip_vertical': False, 'flip_horizontal': True,
                                          'color': CACAO, 'height': 70, 'width': '100', 'z_index': '3',
                                          'responsive_height_tablet': '54', 'responsive_height_mobile': '40'})], anim=None),
    ], bg=solid(CACAO), padding='large', html_id='giornata'))

    km = t.classic('info-cards', {
        'container_bg': {'type': 'none'}, 'container_radius': radius(0), 'container_padding': 0, 'container_gap': 0,
        'columns': 4, 'items_gap': 18, 'columns_tablet': 2,
        'items': [{'counter': c, 'counter_label': lab, 'title': n, 'title_accent': u, 'title_accent_italic': False,
                   'description': d, 'icon': ic, 'footer_dot_color': AMARENA, 'footer_text': '', 'link_url': '', 'link_text': '',
                   'media_image': '', 'media_label': ''} for c, lab, n, u, d, ic in FORNITORI],
        'card_bg': solid(WHITE), 'card_color': INK, 'card_radius': radius(28), 'card_padding': 30, 'show_icon': True,
        'show_counter': True, 'show_counter_label': True, 'title_font_family': 'heading', 'title_size': 72, 'title_weight': '400',
        'title_color': AMARENA, 'counter_color': MUTED, 'icon_color': AMARENA, 'icon_bg_color': mix(AMARENA, 12),
        'description_size': 15,
    })
    out.append(t.section([t.one([sechead(t, 'Da dove arriva', ['Chilometri,', 'non stabilimenti.'], size=52,
                                         tagline='Tutto quello che finisce in vetrina ha un nome e un indirizzo. Il più lontano è il pistacchio.')]),
                          t.one([km])], bg=solid(SABBIA), padding='large', html_id='fornitori'))

    def persona(foto, nome, ruolo, bio, pos):
        return t.classic('team', {'photo': img(foto), 'name': nome, 'role': ruolo, 'bio': bio, 'photo_size': 220,
                                  'photo_ratio': '1/1', 'photo_shape': 'circle', 'photo_object_position': pos,
                                  'photo_border_width': '6', 'photo_border_color': CIALDA, 'info_align': 'center',
                                  'info_text_color': INK, 'name_size': '30', 'name_weight': '400', 'role_size': '13',
                                  'role_color': LINK, 'bio_size': '16', 'bg_color': 'transparent', 'link_text': '', 'link_url': ''},
                         advanced={'mouse_tilt': True, 'mouse_tilt_intensity': 6})
    out.append(t.section([
        t.one([sechead(t, 'Chi c’è al banco', ['I nipoti', 'di Ivonne.'], size=52, align='center')]),
        t.row([t.col([persona('pexels-8426675.jpg', 'Marta', 'Il laboratorio',
                              'Fa le creme alle sei e mezza e scrive la lavagna. Il Bagnasciuga lo assaggia ancora con la nonna.', '50% 28%')],
                     wm='1-2', ws='1-1'),
               t.col([persona('pexels-16537980.jpg', 'Luca', 'La frutta e il banco',
                              'Va al mercato alle otto e sta alla spatola tutta l’estate. Sa a memoria il gusto di chi torna.', '50% 30%')],
                     wm='1-2', ws='1-1')], layout='50-50', gap=48),
    ], padding='large', html_id='persone'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  TORTE E VASCHETTE
# ══════════════════════════════════════════════════════════════════════════════

FAQ_TORTE = [
    ('Quanto prima devo ordinare?', 'Due giorni prima per le torte, anche il giorno stesso per le vaschette (entro le 17). D’inverno, a gelateria chiusa, le torte si ritirano il sabato e la domenica dalle 10 alle 12.'),
    ('Quanto si conserva?', 'Nel freezer di casa, a meno 18, una settimana. Toglietela dieci minuti prima di tagliarla: il coltello va passato sotto l’acqua calda.'),
    ('Fate la scritta?', 'Sì, col cioccolato fondente o bianco, fino a venti lettere. Scrivetela nelle note del modulo.'),
    ('Fate torte senza glutine o senza lattosio?', 'Sì: con la base di meringa e due sorbetti di frutta è senza glutine e senza lattosio. Avvisateci nelle note.'),
]


def opzioni(lst):
    base = {'descrizione': '', 'prezzo': 0, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '', 'min': 0, 'max': 10}
    return [dict(base, **o) for o in lst]


def build_torte():
    t = Tpl(ID, 'torte')
    out = [tendone(t)]
    out.append(t.section([t.one([titlebar(t, 'Le torte gelato da comporre e le vaschette da portare in spiaggia, a casa o a una festa.',
                                          'pexels-8888514.jpg', overlay=50, position='center 74%')], anim=None)],
                         width='fullbleed', padding='remove-vertical'))

    torta = t.classic('configuratore', {
        'gruppi': [
            {'id': 'misura', 'titolo': 'Per quante persone', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
            {'id': 'base', 'titolo': 'La base', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': 'La meringa è senza glutine.'},
            {'id': 'gusti', 'titolo': 'I gusti', 'tipo': 'piu', 'obbligatorio': True, 'aiuto': 'Sceglietene due o tre: di più non si sentono.'},
            {'id': 'sopra', 'titolo': 'Sopra', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''},
        ],
        'opzioni': opzioni([
            {'id': 'p6', 'gruppo': 'misura', 'nome': '6 persone', 'descrizione': '18 cm, circa 800 g', 'prezzo': 28, 'predefinita': True},
            {'id': 'p10', 'gruppo': 'misura', 'nome': '10 persone', 'descrizione': '22 cm, circa 1,3 kg', 'prezzo': 42, 'predefinita': False},
            {'id': 'p16', 'gruppo': 'misura', 'nome': '16 persone', 'descrizione': '28 cm, circa 2 kg', 'prezzo': 60, 'predefinita': False},
            {'id': 'spagna', 'gruppo': 'base', 'nome': 'Pan di Spagna', 'descrizione': 'Bagnato all’alchermes o al caffè', 'prezzo': 0, 'predefinita': True},
            {'id': 'biscotto', 'gruppo': 'base', 'nome': 'Biscotto al cacao', 'descrizione': 'Croccante, coi frollini sbriciolati', 'prezzo': 2, 'predefinita': False},
            {'id': 'meringa', 'gruppo': 'base', 'nome': 'Meringa', 'descrizione': 'Senza glutine', 'prezzo': 3, 'predefinita': False},
            {'id': 'g-fiordi', 'gruppo': 'gusti', 'nome': 'Fiordilatte', 'descrizione': '', 'prezzo': 0, 'predefinita': True},
            {'id': 'g-pista', 'gruppo': 'gusti', 'nome': 'Pistacchio', 'descrizione': '', 'prezzo': 3, 'predefinita': False, 'badge': '+3 €'},
            {'id': 'g-noc', 'gruppo': 'gusti', 'nome': 'Nocciola tostata', 'descrizione': '', 'prezzo': 0, 'predefinita': False},
            {'id': 'g-zuppa', 'gruppo': 'gusti', 'nome': 'Zuppa inglese', 'descrizione': '', 'prezzo': 0, 'predefinita': True},
            {'id': 'g-bagna', 'gruppo': 'gusti', 'nome': 'Bagnasciuga', 'descrizione': 'Fiordilatte, sale e caramello', 'prezzo': 0, 'predefinita': False},
            {'id': 'g-frag', 'gruppo': 'gusti', 'nome': 'Fragola', 'descrizione': 'Sorbetto', 'prezzo': 0, 'predefinita': False},
            {'id': 's-panna', 'gruppo': 'sopra', 'nome': 'Panna e amarene', 'descrizione': '', 'prezzo': 3, 'predefinita': False},
            {'id': 's-ciocc', 'gruppo': 'sopra', 'nome': 'Colata di cioccolato', 'descrizione': 'Fondente o bianco', 'prezzo': 4, 'predefinita': False},
            {'id': 's-coni', 'gruppo': 'sopra', 'nome': 'Coni capovolti', 'descrizione': 'Come in vetrina', 'prezzo': 2, 'predefinita': False},
        ]),
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'La vostra torta', 'nota_prezzo': 'Si paga al ritiro',
        'periodo': '', 'mostra_differenze': True, 'indicatori': [], 'riepilogo': True, 'titolo_riepilogo': 'Riepilogo',
        'testo_azzera': 'Ricomincia', 'testo_pulsante': 'Ordinala', 'link_azione': '#ordina', 'campo_modulo': 'torta',
        'vista_opzioni': 'schede', 'disposizione': 'lato', 'sfondo_scheda': WHITE, 'colore_scelta': AMARENA,
        'colore_testo': INK, 'colore_tenue': MUTED, 'colore_prezzo': LINK, 'sfondo_riepilogo': CACAO,
        'sfondo_badge': CIALDA, 'colore_badge': HEX['dark'], 'raggio_scheda': radius(22), 'ombra': 'sm',
        'gruppo_font_family': 'heading', 'gruppo_font_size': 30, 'gruppo_font_weight': '400', 'gruppo_color': INK,
        'opzione_font_family': 'body', 'opzione_font_size': 17, 'opzione_font_weight': '700',
        'descrizione_font_family': 'body', 'descrizione_font_size': 14, 'descrizione_font_weight': '400',
        'prezzo_font_family': 'heading', 'prezzo_font_size': 22, 'prezzo_font_weight': '400',
        'totale_font_family': 'heading', 'totale_font_size': 72, 'totale_font_weight': '400', 'totale_color': CIALDA, 'gap': 14,
    })
    out.append(t.section([
        t.one([sechead(t, 'La torta gelato', ['Componetela,', 'noi la facciamo.'], size=56,
                       tagline='Il prezzo si aggiorna mentre scegliete. Quando avete finito, «Ordinala» porta la torta nel modulo qui sotto: '
                               'mancano solo il giorno del ritiro e il vostro nome.')]),
        t.one([torta]),
    ], padding='large', html_id='componi'))

    def vaschetta(nome, peso, prezzo, gusti, popolare=False):
        return t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': peso,
            'currency_size': '24',
            'features': gusti, 'check_style': 'circle-check', 'cta_text': 'Ordinala', 'cta_url': '#ordina',
            'cta_bg_color': AMARENA if popolare else CACAO, 'cta_text_color': ON, 'cta_radius': '999',
            'cta_hover_bg_color': CIALDA, 'cta_hover_text_color': HEX['dark'], 'is_popular': popolare,
            'badge_text': 'La più presa', 'badge_bg_color': CIALDA, 'badge_text_color': HEX['dark'],
            'bg_color': WHITE, 'accent_color': AMARENA, 'text_color': INK, 'border_radius': '32', 'shadow': 'md',
            'additional_info': 'Pronta in dieci minuti',
        }, style={'margin_top': 0, 'margin_right': 8, 'margin_bottom': 0, 'margin_left': 8})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Le vaschette', ['Da portare', 'via.'], size=52, layout='stack',
                              tagline='Nel contenitore termico con la borsa di carta: dura un’ora sotto l’ombrellone, tre in macchina col condizionatore.'),
                      t.classic('image', {'image_url': img('pexels-7761791.jpg'), 'alt_text': 'Una vaschetta di gelato coi cucchiaini colorati',
                                          'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/5',
                                          'height': '', 'object_fit': 'cover', 'border_radius': corners(999, 999, 28, 28), 'shadow': 'none'})],
                     wm='1-3', ws='1-1'),
               t.col([t.row([t.col([vaschetta('Mezzo chilo', '500 g', '11', 'Due gusti a scelta\nIl contenitore termico\nI cucchiaini di legno')], wm='1-3', ws='1-1'),
                             t.col([vaschetta('Tre etti in più', '750 g', '15', 'Tre gusti a scelta\nIl contenitore termico\nLa borsa coi ghiaccioli', True)], wm='1-3', ws='1-1'),
                             t.col([vaschetta('Un chilo', '1 kg', '19', 'Quattro gusti a scelta\nIl contenitore termico\nLe cialde a parte')], wm='1-3', ws='1-1')],
                            layout='33-33-33', gap=16)], wm='2-3', ws='1-1')],
              layout='custom', custom='34,66', gap=48, valign='center'),
    ], bg=solid(MENTA), padding='large', html_id='vaschette'))

    form = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'La torta', 'name': 'torta', 'placeholder': 'Componetela qui sopra, o scrivetela voi',
             'required': True, 'width': '1-1', 'options': '', 'icon': 'cake'},
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': 'Giulia', 'required': True, 'width': '1-2', 'options': '', 'icon': 'user'},
            {'field_type': 'tel', 'label': 'Telefono', 'name': 'telefono', 'placeholder': '333 123 4567', 'required': True, 'width': '1-2', 'options': '', 'icon': 'phone'},
            {'field_type': 'date', 'label': 'Giorno del ritiro', 'name': 'ritiro', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Ora del ritiro', 'name': 'ora', 'placeholder': 'Scegliete', 'required': True, 'width': '1-2',
             'options': 'Fra le 11 e le 13\nFra le 15 e le 17\nFra le 17 e le 19\nDopo cena, fra le 21 e le 23', 'icon': ''},
            {'field_type': 'textarea', 'label': 'La scritta e le note', 'name': 'note', 'placeholder': '«Buon compleanno Sara», senza frutta secca…',
             'required': False, 'width': '1-1', 'options': '', 'icon': ''},
        ],
        'email_subject': 'Una torta gelato dal sito', 'success_message': 'Ricevuta! Vi richiamiamo entro sera per confermare il ritiro.',
        'submit_text': 'Ordina la torta', 'submit_icon': 'send', 'submit_full_width': True, 'label_color': INK,
        'input_bg': PANNA, 'input_radius': 16, 'input_focus_border': AMARENA, 'submit_bg': AMARENA, 'submit_color': ON,
        'submit_radius': 999, 'submit_hover_bg': CACAO, 'privacy_checkbox': True,
    }, style={'bg': solid(WHITE), 'padding_top': 36, 'padding_right': 40, 'padding_bottom': 36, 'padding_left': 40, 'border_radius': 32})
    out.append(t.section([
        t.row([t.col([sechead(t, 'L’ordine', ['Ditteci quando', 'passate.'], size=52, layout='stack',
                              tagline='Vi richiamiamo entro sera per confermare. Si paga al ritiro, anche con la carta.'),
                      faq(t, FAQ_TORTE, 'bg-torte')], wm='2-5', ws='1-1'),
               t.col([form], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='start'),
    ], padding='large', html_id='ordina'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    testa = t.classic('section-header', {
        'eyebrow_show': True, 'eyebrow_text': 'Errore 404', 'eyebrow_color': HEX['accent'], 'eyebrow_dot_color': HEX['accent'],
        'eyebrow_separator': '', 'eyebrow_font_family': 'body', 'eyebrow_font_size': 13, 'eyebrow_font_weight': '800',
        'headline_lines': [{'text': 'Questa pagina', 'color': ON, 'italic': False},
                           {'text': 'si è sciolta.', 'color': HEX['accent'], 'italic': False}],
        'headline_font_family': 'heading', 'headline_font_size': 96, 'headline_line_height': 1.0, 'headline_font_weight': '400',
        'headline_align': 'center', 'headline_font_size_tablet': 72, 'headline_font_size_mobile': 50,
        'tagline_show': True, 'tagline_text': 'Succede, con trentadue gradi all’ombra. In vetrina però ce ne sono altre.',
        'tagline_text_color': ON_SOFT, 'tagline_text_size': 20, 'tagline_font_family': 'body', 'layout': 'center', 'gap': 18,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 18, 'padding_left': 0})
    return [t.section([
        t.one([testa, button(t, 'Torna alla vetrina', HOME, kind='panna', align='center')], anim='fade'),
    ], bg=video_bg(V_SCIOLTO, overlay=58, color=CACAO), padding='xlarge', width='default')]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Ristorazione', 'zone': 'Componi la torta',
    'tags': ['gelateria', 'gelato', 'riviera', 'pasticceria', 'anni cinquanta'],
    'description': 'Gelateria sul lungomare dal 1958: il tendone a righe sopra ogni pagina, la spatola nel caramello in '
                   'apertura, la colonna dei gusti che scorre, la lavagna del giorno, i giorni alla riapertura nelle palline e '
                   'la torta gelato da comporre col prezzo che si aggiorna.',
    'menu': {'name': 'Bagnasciuga — menu', 'items': [
        {'title': 'I gusti', 'url': GUSTI}, {'title': 'Il laboratorio', 'url': LAB},
        {'title': 'Dove siamo', 'url': HOME + '#dove'}, {'title': 'Torte e vaschette', 'url': TORTE}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Bagnasciuga — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Bagnasciuga — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Bagnasciuga — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Bagnasciuga — Home'},
        'gusti': {'file': 'gusti.json', 'type': 'page', 'title': 'Bagnasciuga — I gusti'},
        'laboratorio': {'file': 'laboratorio.json', 'type': 'page', 'title': 'Bagnasciuga — Il laboratorio'},
        'torte-e-vaschette': {'file': 'torte-e-vaschette.json', 'type': 'page', 'title': 'Bagnasciuga — Torte e vaschette'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'gusti': {'title': 'I gusti', 'template': 'gusti', 'slug': 'gusti'},
        'laboratorio': {'title': 'Il laboratorio', 'template': 'laboratorio', 'slug': 'laboratorio'},
        'torte-e-vaschette': {'title': 'Torte e vaschette', 'template': 'torte-e-vaschette', 'slug': 'torte-e-vaschette'},
    },
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Rethink Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Abril Fatface\', Georgia, \'Times New Roman\', serif',
            'font_family_mono': '\'Rethink Sans\', system-ui, sans-serif',
            'font_size_base': '18px', 'line_height': '1.65', 'font_weight_body': '400', 'font_weight_heading': '400',
            'heading_line_height': '1.04', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '5rem', 'font_size_h2': '3.4rem', 'font_size_h3': '1.9rem',
            'font_size_h4': '1.5rem', 'font_size_h5': '1.2rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3rem',
            'font_size_h2_mobile': '2.3rem', 'font_size_h3_mobile': '1.5rem',
        },
        'google_fonts': ['Abril Fatface', 'Rethink Sans', 'Yellowtail'],
        'google_fonts_weights': '400;500;600;700;800',
        'google_fonts_italic': [],
        'buttons': {'font_size': '16px', 'font_weight': '700', 'padding_x': '26px', 'padding_y': '14px', 'border_radius': '999px',
                    'text_transform': 'none', 'letter_spacing': '0', 'hover_brightness': '94'},
        'layout': {'border_radius': '24px', 'border_radius_large': '32px', 'container_max_width': '1240px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': HEX['background'], 'field_border_color': rgba(HEX['text'], 0.2), 'field_border_width': '1',
                  'field_border_radius': '16px', 'field_padding': '12px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': '0 0 0 4px ' + rgba(HEX['primary'], 0.18),
                  'label_font_size': '14px', 'label_font_weight': '700', 'label_margin_bottom': '6px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': False, 'opacity': 0, 'scale': 160},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'gusti.json': build_gusti(), 'laboratorio.json': build_laboratorio(),
              'torte-e-vaschette.json': build_torte(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
