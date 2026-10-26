# -*- coding: utf-8 -*-
"""Genera il tema «Raggiera» (id raggiera): ciclofficina a Ferrara, la città delle biciclette.

Bauhaus: carta, nero e i tre primari (rosso, blu, giallo) a campiture piene; cerchi e rettangoli, niente angoli
arrotondati a metà. Titoli tutti in minuscolo in League Spartan (la «scrittura universale» di Bayer), testo in
Poppins. Il cerchio è la ruota: il video dei raggi dentro un tondo nell'apertura (Hero Split col «Raggio
pannello» a 999), gli anelli concentrici dello sfondo «Geometrie», la ruota da 36 raggi che si disegna da sola
(SVG Animator), il nome che gira in tondo (Testo su tracciato).

Moduli: il cartellino da cercare per sapere a che punto è la bici (Tracciamento → campo del Form compilato
dall'indirizzo), il quiz «che bici fa per te» (Score Quiz), la bici da comporre con le foto dei pezzi e il totale
(Lookbook Mixer), il profilo altimetrico della lunga del delta (Chart), le uscite della domenica (Cartellone), i
meccanici nei tondi (Team), il listino (Price List) e il noleggio (Pricing).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, lib, mix, radius, corners, solid, rgba  # noqa: E402

ID = 'raggiera'
NAME = 'raggiera'

HEX = {
    'primary': '#D63A22', 'primary_contrast': '#FFFFFF', 'secondary': '#1F4AA8', 'secondary_contrast': '#FFFFFF',
    'accent': '#F4B400', 'dark': '#171717', 'light': '#FAF7F1', 'muted': '#E7E0D3', 'muted_contrast': '#171717',
    'text': '#171717', 'text_muted': '#56524B', 'background': '#F1ECE2', 'border': '#D6CDBE', 'link': '#1F4AA8',
}

INK = 'var(--olo-color-text)'
MUTED = 'var(--olo-color-text-muted)'
ROSSO = 'var(--olo-color-primary)'
BLU = 'var(--olo-color-secondary)'
GIALLO = 'var(--olo-color-accent)'
NERO = 'var(--olo-color-dark)'
CARTA = 'var(--olo-color-background)'
CHIARO = 'var(--olo-color-light)'
GRIGIO = 'var(--olo-color-muted)'
BIANCO = '#FFFFFF'
ON_SOFT = rgba('#FFFFFF', 0.84)
ON_DIM = rgba('#FFFFFF', 0.62)
LINE = mix(INK, 16)
SQ = radius(0)
TONDO = radius(999)

ADDRESS = 'Via Ripagrande 18, 44121 Ferrara'
PHONE = '0532 000 000'
PHONE_TEL = 'tel:+390532000000'
MAIL = 'officina@raggiera.example'
LAT, LNG = '44.8316', '11.6157'
HOME = '{{PAGINA:home}}'
OFFICINA = '{{PAGINA:officina}}'
MISURA = '{{PAGINA:su-misura}}'
GIRI = '{{PAGINA:giri-e-noleggio}}'

V_RAGGI = 'pexels-video-5228664'     # i raggi che girano, luce calda
V_OFFICINA = 'pexels-video-33325059'  # il meccanico e la bici rossa sul cavalletto
V_MANI = 'pexels-video-6333110'      # le mani sul mozzo
V_STRADA = 'pexels-video-36306825'   # la ruota sull'asfalto
V_GRAVEL = 'pexels-video-4995092'    # la strada bianca

PHOTOS = [
    'pexels-6334159.jpg', 'pexels-3817785.jpg', 'pexels-16135854.jpg', 'pexels-40046218.jpg', 'pexels-1154089.jpg',
    'pexels-28100861.jpg', 'pexels-16884264.jpg', 'pexels-10263879.jpg', 'pexels-10213677.jpg', 'pexels-5446296.jpg',
    'pexels-30361320.jpg', 'pexels-6156531.jpg', 'pexels-36398681.jpg', 'pexels-38628458.jpg', 'pexels-34756141.jpg',
    'pexels-36068823.jpg', 'pexels-4828817.jpg', 'pexels-16096769.jpg', 'pexels-40002193.jpg', 'pexels-2131791.jpg',
    'pexels-30144868.jpg', 'pexels-36565840.jpg', 'pexels-13716354.jpg', 'pexels-7478562.jpg', 'pexels-17899127.jpg',
    'pexels-36518530.jpg', 'pexels-29095762.jpg', 'pexels-4081214.jpg', 'pexels-10005316.jpg', 'pexels-34677186.jpg',
    'pexels-16567773.jpg', 'pexels-5446289.jpg', 'pexels-33506466.jpg', 'pexels-33868920.jpg', 'pexels-33497179.jpg',
    'pexels-6209913.jpg', 'pexels-36215202.jpg', 'pexels-7951516.jpg', 'pexels-19438258.jpg', 'pexels-5446291.jpg',
]
VIDEOS = [V_RAGGI, V_OFFICINA, V_MANI, V_STRADA, V_GRAVEL]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=40, color=NERO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=40, color=NERO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


def anelli(base=CARTA, x=76, y=50, rings=7, r=170, step=44, color=None, opacity=11, accent=2, accent_opacity=70):
    """Lo sfondo «Geometrie»: anelli concentrici come una ruota, la trama di puntini, due anelli in rosso."""
    return {'type': 'geo', 'geo_base': base, 'geo_rings': rings, 'geo_rings_x': x, 'geo_rings_y': y, 'geo_rings_r': r,
            'geo_rings_step': step, 'geo_rings_width': 2, 'geo_rings_color': color or HEX['text'],
            'geo_rings_opacity': opacity, 'geo_accent': accent, 'geo_accent_color': ROSSO, 'geo_accent_opacity': accent_opacity,
            'geo_texture': 'dots', 'geo_tex_gap': 26, 'geo_tex_size': 2, 'geo_tex_color': color or HEX['text'],
            'geo_tex_opacity': 9, 'geo_fade': False, 'geo_rings2': 0, 'geo_dot': 0}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=64, light=False, colors=None, align='left', layout='split', size_mobile=40):
    """Occhiello in maiuscoletto (lo fa la tile), titolo minuscolo con ogni riga del suo colore."""
    ink = BIANCO if light else INK
    colors = colors or [ink, ROSSO]
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': GIALLO if light else BLU,
        'eyebrow_dot_color': ROSSO, 'eyebrow_separator': '', 'eyebrow_font_family': 'body', 'eyebrow_font_size': 12,
        'eyebrow_font_weight': '700',
        'headline_lines': [{'text': l, 'color': colors[i % len(colors)], 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 0.95,
        'headline_font_weight': '800', 'headline_align': align,
        'headline_font_size_tablet': max(36, int(size * 0.8)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False,
        'tagline_text_color': ON_SOFT if light else MUTED, 'tagline_text_size': 17, 'tagline_font_family': 'body',
        'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.3fr 1fr', 'gap': 20,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 30, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label, size=34, color=INK, tag='h3', pad_bottom=10, align='left', weight='800'):
    o = {'heading': label, 'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.82)), 'heading_font_size_mobile': str(max(28, int(size * 0.6)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=17, color=MUTED, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.7, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label, url, kind='rosso', align='left', pad_top=8):
    o = {'rosso': {'bg_color': ROSSO, 'text_color': BIANCO, 'hover_bg_color': NERO, 'hover_text_color': BIANCO},
         'blu': {'bg_color': BLU, 'text_color': BIANCO, 'hover_bg_color': NERO, 'hover_text_color': BIANCO},
         'giallo': {'bg_color': GIALLO, 'text_color': HEX['dark'], 'hover_bg_color': BIANCO, 'hover_text_color': HEX['dark']},
         'nero': {'bg_color': NERO, 'text_color': BIANCO, 'hover_bg_color': ROSSO, 'hover_text_color': BIANCO},
         'bianco': {'bg_color': BIANCO, 'text_color': HEX['dark'], 'hover_bg_color': GIALLO, 'hover_text_color': HEX['dark']}}[kind]
    o = dict(o)
    o.update({'text': label, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 16, 'font_weight': '700',
              'text_transform': 'lowercase', 'letter_spacing': '0'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='1/1', r=None, position='center center'):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': r if r is not None else SQ, 'lightbox': False, 'shadow': 'none'})


def titlebar(t, sub, bg_color, image_name, position='center center'):
    """La testa delle pagine interne: una campitura piena a sinistra, la foto nel tondo a destra."""
    titolo = t.classic('pagetitlebar', {
        'media_bg': {'type': 'none'}, 'bg': {'type': 'none'}, 'title_tag': 'h1', 'title_color': BIANCO, 'title_size': 112,
        'title_weight': '800', 'title_align': 'left', 'subtitle': sub, 'subtitle_color': ON_SOFT, 'subtitle_size': 19,
        'show_breadcrumbs': True, 'breadcrumb_color': ON_DIM, 'breadcrumb_separator': '/', 'bg_overlay': 0, 'bg_color': bg_color,
        'min_height': 0, 'tile_padding': {'top': 40, 'right': 0, 'bottom': 20, 'left': 0}, 'content_width': 1200, 'padding_y': '20',
    })
    tondo = image(t, image_name, '', ratio='1/1', r=TONDO, position=position)
    return t.section([t.row([t.col([titolo], wm='3-5', ws='1-1'), t.col([tondo], wm='2-5', ws='1-1')],
                            layout='custom', custom='60,40', gap=48, valign='center', anim='fade')],
                     bg=solid(bg_color), padding='large')


def osm(t, height=460):
    return t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '16', 'height': str(height), 'marker': True,
                                'marker_popup': 'raggiera · via Ripagrande 18', 'scroll_wheel_zoom': False,
                                'tile_layer': 'gray', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})


def contacts(t, light=False):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': HOME + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': BIANCO if not light else HEX['dark'], 'icon_size': '18', 'text_color': BIANCO if light else INK,
        'text_size': '16', 'gap': '14', 'icon_shape': 'circle', 'icon_bg_color': GIALLO if light else ROSSO,
        'divider': False, 'layout': 'vertical',
    }, style={})


def hours(t, light=False):
    return t.classic('hoursstrip', {
        'items': [{'day': 'lunedì', 'time': '15 — 19', 'note': 'solo ritiri e consegne'},
                  {'day': 'martedì — venerdì', 'time': '8:30 — 19', 'note': 'officina sempre aperta, anche a pranzo'},
                  {'day': 'sabato', 'time': '9 — 13', 'note': 'il pomeriggio si esce in bici'}],
        'columns': 3, 'band_padding_y': 26, 'show_dividers': True, 'divider_color': rgba('#FFFFFF', 0.22) if light else LINE,
        'band_border': False, 'day_color': GIALLO if light else ROSSO, 'day_size': 13, 'time_font_family': 'heading',
        'time_color': BIANCO if light else INK, 'time_size': 48, 'time_weight': '800',
        'note_color': ON_DIM if light else MUTED, 'note_size': 14, 'mono_font_family': '',
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def faq(t, pairs, prefix, light=False):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'first', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': ROSSO, 'header_font_size': 22, 'header_font_weight': '700',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 16, 'text_color': MUTED,
        'icon_color': ROSSO, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 3, 'left': 0, 'linked': False, 'style': 'solid', 'color': INK},
        'header_padding': {'top': 20, 'right': 0, 'bottom': 20, 'left': 0},
        'content_padding': {'top': 0, 'right': 40, 'bottom': 22, 'left': 0},
    }, style={})


# La ruota da 36 raggi: cerchione, copertone, mozzo e i raggi incrociati, a tratto (l'animazione li traccia uno a uno).
def _ruota():
    import math
    cx = cy = 200
    parti = ['<circle cx="200" cy="200" r="190"/>', '<circle cx="200" cy="200" r="176"/>', '<circle cx="200" cy="200" r="22"/>',
             '<circle cx="200" cy="200" r="8"/>']
    for i in range(36):
        a = math.radians(i * 10)
        sfaso = math.radians(i * 10 + (16 if i % 2 else -16))
        x1, y1 = cx + 22 * math.cos(sfaso), cy + 22 * math.sin(sfaso)
        x2, y2 = cx + 176 * math.cos(a), cy + 176 * math.sin(a)
        parti.append('<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>' % (x1, y1, x2, y2))
    return ('<svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" '
            'stroke-width="2.5" stroke-linecap="round">' + ''.join(parti) + '</svg>')


RUOTA = _ruota()


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': NAME, 'logo_text_size': '30', 'logo_text_color': INK,
        'logo_text_font': 'heading', 'logo_text_weight': '800', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': 'bike', 'logo_mark_size': 18, 'logo_mark_stroke': 2.25, 'logo_mark_color': BIANCO,
        'logo_mark_bg': ROSSO, 'logo_mark_shape': 'circle', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'classic', 'sticky': True,
        'sticky_bg': CARTA, 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': False,
        'nav_bg': CARTA, 'text_color': INK, 'hover_color': ROSSO, 'active_color': ROSSO,
        'font_size': '17', 'font_weight': '600', 'text_transform': 'lowercase', 'letter_spacing': '0', 'item_gap': '32',
        'bar_width': 'wide', 'nav_height': '76', 'bar_padding': {'top': 10, 'right': 32, 'bottom': 10, 'left': 32},
        'hover_effect': 'dot', 'hover_effect_color': ROSSO,
        'button_mode': 'last', 'btn_bg': NERO, 'btn_color': BIANCO, 'btn_hover_color': BIANCO,
        'btn_radius': SQ, 'btn_padding': {'top': 12, 'right': 22, 'bottom': 12, 'left': 22}, 'btn_hover_effect': 'none',
        'btn_font_weight': '700', 'btn_transform': 'lowercase', 'btn_letter_spacing': '0',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '#', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': GIALLO, 'mobile_text_color': HEX['dark'], 'mobile_accent_color': ROSSO,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '40', 'mobile_link_size': '40',
        'mobile_link_font': 'heading', 'mobile_link_weight': '800',
        'border': {'top': 0, 'right': 0, 'bottom': 3, 'left': 0, 'style': 'solid', 'color': HEX['text']},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    giro = t.classic('textpath', {'text': 'ciclofficina · ferrara · dal 1999 · ripariamo tutto · ', 'path_preset': 'circle',
                                  'font_size': '18', 'text_color': GIALLO, 'letter_spacing': '4', 'animation': 'none',
                                  'spin': True, 'spin_speed': '24', 'spin_direction': 'cw', 'typography_preset': ''},
                     style={'tile_max_width': '230px'})
    brand = headline(t, 'raggiera', size=96, color=BIANCO, tag='p', pad_bottom=6)
    claim = text(t, '<p>Ripariamo, montiamo e restauriamo biciclette dentro le mura di Ferrara dal 1999. '
                    'Tre meccanici, un cavalletto in più del necessario e la domenica in sella.</p>', size=16, color=ON_SOFT)

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'lowercase', 'letter_spacing': '0',
                                 'link_color': ON_SOFT, 'link_hover_color': GIALLO, 'active_color': BIANCO,
                                 'font_size': '17', 'font_weight': '600', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    titolo = lambda s, c: headline(t, s, size=22, color=c, tag='h4', pad_bottom=10)  # noqa: E731
    pagine = nav([('officina', OFFICINA), ('su misura e restauri', MISURA), ('giri e noleggio', GIRI),
                  ('dove siamo', HOME + '#dove'), ('privacy', '/privacy-policy/')])
    orari = text(t, '<p>lun 15–19<br>mar–ven 8:30–19<br>sab 9–13</p>', size=16, color=ON_SOFT)
    legal = text(t, '<p>© 2026 raggiera ciclofficina · Ferrara. Le bici lasciate più di sessanta giorni dopo la riparazione '
                    'diventano bici da noleggio: venite a prenderle.</p>', size=13, color=rgba('#FFFFFF', 0.5))
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'facebook', 'url': 'https://facebook.com'}],
                                  'size': '34', 'alignment': 'right', 'icon_color': ON_DIM, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': GIALLO, 'posizione': 'fisso', 'style': 'default'})
    line = {'style': 'solid', 'color': rgba('#FFFFFF', 0.2)}
    top = t.row([
        t.col([giro], wm='1-4', ws='1-1'),
        t.col([brand, claim, contacts(t, light=True)], wm='1-3', ws='1-1'),
        t.col([titolo('le pagine', GIALLO), pagine], wm='1-5', ws='1-2'),
        t.col([titolo('gli orari', GIALLO), orari], wm='1-5', ws='1-2'),
    ], layout='custom', custom='24,36,20,20', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': dict(top=3, right=0, bottom=0, left=0, **line), 'padding_top': 20, 'margin_top': 48})
    strisce = t.row([t.col([t.bare('spacer', {'height': '14'}, style={'bg': solid(ROSSO)})]),
                     t.col([t.bare('spacer', {'height': '14'}, style={'bg': solid(GIALLO)})]),
                     t.col([t.bare('spacer', {'height': '14'}, style={'bg': solid(BLU)})])],
                    layout='33-33-33', gap=0, anim=None, stack=False)
    return [t.section([strisce], width='fullbleed', padding='remove-vertical', bg=solid(NERO)),
            t.section([top, bottom], bg=solid(NERO), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

SERVIZI = [
    ('wrench', 'riparazioni', 'Tagliandi, freni, cambi, ruote e camere d’aria. Il preventivo prima di toccare la bici, il messaggio quando è pronta.', OFFICINA, ROSSO, BIANCO, corners(0, 0, 140, 0)),
    ('ruler', 'su misura', 'Telai d’acciaio saldati a Ferrara sulle vostre misure: ci vediamo, misuriamo, scegliete i pezzi.', MISURA, BLU, BIANCO, SQ),
    ('paintbrush', 'restauri', 'Le bici del nonno tornano a girare: sverniciatura, cromature, pezzi d’epoca recuperati.', MISURA + '#restauri', GIALLO, HEX['dark'], SQ),
    ('route', 'giri e noleggio', 'Bici da città, gravel e cargo a giornata. E la domenica si esce insieme sugli argini.', GIRI, NERO, BIANCO, corners(0, 140, 0, 0)),
]

QUIZ = {
    'eyebrow': 'il quiz', 'heading': 'che bici fa per te?', 'intro': 'Tre domande. Poi ti diciamo cosa provare, a noleggio o su misura.',
    'questions': [
        {'text': 'dove pedali di più?', 'options': 'In città, per andare al lavoro|0\nSugli argini e sulle strade bianche|2\nSu asfalto, più forte che posso|4'},
        {'text': 'cosa porti con te?', 'options': 'La spesa, o un bambino|0\nUna borsa e la merenda|2\nSolo la borraccia|4'},
        {'text': 'quanti chilometri a settimana?', 'options': 'Meno di trenta|0\nFra trenta e cento|2\nPiù di cento|4'},
    ],
    'results': [
        {'min': 0, 'title': 'una bici da città, o una cargo', 'text': 'Telaio basso, parafanghi, il portapacchi davanti. Provane una a noleggio per un fine settimana.', 'link_text': 'Il noleggio', 'link_url': GIRI + '#noleggio'},
        {'min': 5, 'title': 'una gravel', 'text': 'Copertoni larghi e manubrio da corsa: va sull’asfalto e sulle strade bianche del Delta. La componiamo insieme.', 'link_text': 'Componi la tua', 'link_url': MISURA + '#componi'},
        {'min': 9, 'title': 'una bici da corsa in acciaio', 'text': 'Leggera ma comoda per i lunghi: il telaio lo saldiamo sulle tue misure.', 'link_text': 'Su misura', 'link_url': MISURA + '#componi'},
    ],
}


def tracciamento(t, light=False):
    return t.classic('tracciamento', {
        'indirizzo': OFFICINA + '?cartellino={codice}#stato', 'operatori': [], 'etichetta': 'Numero del cartellino',
        'mostra_etichetta': True, 'segnaposto': 'es. RG2614', 'testo_pulsante': 'a che punto è?', 'icona_pulsante': 'arrow-right',
        'icona_campo': 'tag', 'nota': 'È scritto sul cartellino rosso legato al manubrio quando ci lasciate la bici.',
        'nuova_scheda': False, 'maiuscolo': True, 'lunghezza_minima': 6,
        'testo_errore': 'Scrivete il numero completo: comincia con RG e ha sei caratteri.', 'parametro': 'cartellino',
        'sfondo_campo': BIANCO, 'colore_campo': HEX['dark'], 'bordo_campo': {'top': 3, 'right': 3, 'bottom': 3, 'left': 3, 'style': 'solid', 'color': HEX['dark']},
        'colore_etichetta': HEX['dark'], 'colore_nota': HEX['dark'], 'colore_errore': ROSSO,
        'sfondo_pulsante': NERO, 'colore_pulsante': BIANCO, 'ombra': 'none',
        'etichetta_font_family': 'body', 'etichetta_font_size': 14, 'etichetta_font_weight': '700',
        'campo_font_family': 'heading', 'campo_font_size': 22, 'campo_font_weight': '700',
        'pulsante_font_family': 'body', 'pulsante_font_size': 15, 'pulsante_font_weight': '700',
        'nota_font_family': 'body', 'nota_font_size': 14, 'raggio_campo': SQ, 'raggio_pulsante': SQ,
        'altezza': 62, 'larghezza_max': 640, 'disposizione': 'riga', 'allineamento': 'left',
    })


def persona(t, foto, nome, ruolo, bio, colore, pos='center 30%'):
    return t.classic('team', {'photo': img(foto), 'name': nome, 'role': ruolo, 'bio': bio, 'photo_size': 210,
                              'photo_ratio': '1/1', 'photo_shape': 'circle', 'photo_object_position': pos,
                              'photo_border_width': '8', 'photo_border_color': colore, 'info_align': 'left',
                              'info_text_color': INK, 'info_bg_color': 'transparent', 'name_size': '30', 'name_weight': '800',
                              'role_size': '13', 'role_color': BLU, 'bio_size': '15', 'bg_color': 'transparent',
                              'link_text': '', 'link_url': ''})


def build_home():
    t = Tpl(ID, 'home')
    out = []

    # Apertura: il titolo in minuscolo e la ruota che gira dentro il tondo, sugli anelli concentrici.
    hero = t.classic('hero-split', {
        'eyebrow_text': 'ciclofficina · ferrara, dentro le mura', 'eyebrow_dot_color': ROSSO, 'eyebrow_color': INK,
        'headline_lines': [{'text': 'ripariamo', 'color': INK, 'italic': False},
                           {'text': 'biciclette.', 'color': ROSSO, 'italic': False},
                           {'text': 'anche la tua.', 'color': BLU, 'italic': False}],
        'headline_font_family': 'heading', 'headline_font_size': 108, 'headline_line_height': 0.92, 'headline_font_weight': 800,
        'headline_align': 'left',
        'subhead': 'Tagliandi in quarantotto ore, ruote montate a mano, telai d’acciaio su misura e le bici del nonno che '
                   'tornano a girare. Tre meccanici, in via Ripagrande dal 1999.',
        'subhead_color': INK, 'subhead_size': 18, 'subhead_max_width': 480,
        'cta1_text': 'porta la bici', 'cta1_url': OFFICINA + '#prenota', 'cta1_bg': ROSSO, 'cta1_bg_hover': NERO,
        'cta1_color': BIANCO, 'cta1_size': 16, 'cta1_radius': SQ, 'cta1_radius_hover': SQ,
        'cta2_text': 'a che punto è la mia?', 'cta2_url': '#cartellino', 'cta2_bg': 'transparent', 'cta2_bg_hover': NERO,
        'cta2_color': INK, 'cta2_color_hover': BIANCO, 'cta2_border': INK, 'cta2_size': 16, 'cta2_radius': SQ, 'cta2_radius_hover': SQ,
        'stats': [{'value': '1999', 'value_color': ROSSO, 'label': 'in via Ripagrande', 'italic': False},
                  {'value': '48 h', 'value_color': BLU, 'label': 'per un tagliando', 'italic': False},
                  {'value': '4.200', 'value_color': INK, 'label': 'bici riparate all’anno', 'italic': False}],
        'panel': 'media',
        'panel_media': {'type': 'video', 'video_url': img(V_RAGGI + '.mp4'), 'video_poster': img(V_RAGGI + '.jpg'),
                        'video_fit': 'cover', 'image_position': 'center center'},
        'panel_media_label': '', 'panel_aspect': '1/1', 'panel_radius': TONDO,
        'panel_badge_number': '36', 'panel_badge_label': 'raggi per ruota, montati a mano',
        'split_ratio': '1.2fr 1fr', 'gap': 64, 'min_height': 0,
        'tile_padding': {'top': 56, 'right': 32, 'bottom': 56, 'left': 32}, 'scene_depth': 'soft', 'scene_depth_fade': True,
    }, style={'bg': {'type': 'none'}})
    out.append(t.section([t.one([hero], anim=None)], bg=anelli(), width='default', padding='default'))

    # Il cartellino: a che punto è la bici.
    out.append(t.section([
        t.row([t.col([headline(t, 'a che punto è la tua bici?', size=48, color=HEX['dark'], tag='h2', pad_bottom=6),
                      text(t, '<p>Scrivete il numero del cartellino: vi diciamo cosa abbiamo fatto, cosa manca e quando passare.</p>',
                           size=17, color=HEX['dark'])], wm='2-5', ws='1-1'),
               t.col([tracciamento(t)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=48, valign='center'),
    ], bg=solid(GIALLO), padding='default', html_id='cartellino'))

    # Quattro campiture: cosa facciamo.
    cards = []
    for icon, title, desc, url, bg, fg, rad in SERVIZI:
        cards.append(t.col([t.classic('iconbox', {
            'media_bg': {'type': 'solid', 'color': bg}, 'icon_emoji': icon, 'title': title, 'description': desc,
            'link_url': url, 'link_text': 'scopri', 'alignment': 'left', 'text_color': fg, 'icon_size': '2',
            'icon_position': 'top', 'icon_bg_color': 'transparent', 'icon_bg_shape': 'none', 'icon_color': fg,
            'title_font_size': 34, 'title_font_weight': '800', 'title_color': fg, 'link_color': fg, 'shadow': 'none',
            'icon_gap': '28', 'title_gap': '10', 'desc_gap': '22', 'tile_padding': {'top': 36, 'right': 32, 'bottom': 40, 'left': 32},
            'border_radius': rad,
        }, style={'margin_top': 0, 'margin_right': 0, 'margin_bottom': 0, 'margin_left': 0},
            advanced={})], wm='1-4', ws='1-2'))
    out.append(t.section([
        t.one([sechead(t, 'cosa facciamo', ['quattro mestieri,', 'un’officina.'], size=68,
                       tagline='Dentro le mura le biciclette sono più delle persone. Noi ci occupiamo di tutte: quelle per andare '
                               'al lavoro, quelle della domenica e quelle che stanno in cantina da trent’anni.')]),
        t.row(cards, layout='25-25-25-25', gap=0),
    ], padding='large', html_id='servizi'))

    # La ruota che si disegna: trentasei raggi, uno alla volta.
    ruota = t.classic('svganimator', {'source_type': 'code', 'svg_code': RUOTA, 'anim_type': 'draw', 'anim_sequence': 'one-by-one',
                                      'trigger': 'viewport', 'duration': 2600, 'delay': 0, 'easing': 'ease-in-out',
                                      'stagger_delay': 45, 'stroke_width': '', 'stroke_color': BIANCO, 'show_fill': False,
                                      'loop': False, 'replay_button': False, 'replay_button_label': 'rifalla',
                                      'max_width': '440px', 'alignment': 'center'},
                      style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.row([t.col([ruota], wm='1-2', ws='1-1'),
               t.col([sechead(t, 'le ruote', ['trentasei raggi,', 'uno alla volta.'], size=64, light=True,
                              colors=[BIANCO, GIALLO], layout='stack'),
                      text(t, '<p>Una ruota montata a mano dura il doppio di una di serie: ogni raggio ha la sua tensione, '
                              'controllata col tensiometro e poi a orecchio, come una corda di chitarra. Ci vogliono due ore.</p>',
                           size=18, color=ON_SOFT),
                      t.classic('desclist', {
                          'items': [{'term': 'centratura', 'definition': '20 € · in giornata', 'icon': ''},
                                    {'term': 'raggio rotto', 'definition': '12 € compreso il raggio', 'icon': ''},
                                    {'term': 'ruota nuova a mano', 'definition': 'da 180 € · una settimana', 'icon': ''}],
                          'layout': 'inline', 'term_color': GIALLO, 'term_font_size': '14', 'term_font_weight': '700',
                          'definition_color': BIANCO, 'definition_font_size': '17', 'separator': True,
                          'border_color': rgba('#FFFFFF', 0.2)}, style={'margin_top': 6, 'margin_bottom': 20}),
                      button(t, 'il listino dell’officina', OFFICINA + '#listino', kind='giallo')], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], bg=solid(BLU), padding='large', html_id='ruote'))

    # L'officina dal vivo.
    officina = t.classic('video', {'source_type': 'file', 'file_url': img(V_OFFICINA + '.mp4'),
                                   'poster_image': img(V_OFFICINA + '.jpg'), 'display_mode': '16:9', 'aspect_ratio': '16:9',
                                   'facade': False, 'autoplay': True, 'muted': True, 'loop': True, 'controls': False,
                                   'show_play_icon': False, 'overlay_text': '', 'overlay_opacity': 0, 'border_radius': 0,
                                   'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                          style={'border_radius': 0})
    out.append(t.section([
        t.row([t.col([officina], wm='3-5', ws='1-1'),
               t.col([sechead(t, 'l’officina', ['tre cavalletti,', 'una finestra.'], size=56, layout='stack'),
                      text(t, '<p>Lavoriamo con la vetrina aperta su via Ripagrande: se passate, vedete la vostra bici sul cavalletto. '
                              'Il preventivo arriva prima di cominciare, per messaggio; se costa più di quanto vale la bici, ve lo diciamo.</p>',
                           size=17),
                      button(t, 'prenota il controllo', OFFICINA + '#prenota', kind='rosso')], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='center'),
    ], padding='large', html_id='officina'))

    # Il quiz.
    quiz = t.classic('scorequiz', dict(QUIZ, **{
        'result_label': 'ti consigliamo…', 'back_label': 'indietro', 'restart_label': 'rifai il quiz', 'show_score': False,
        'zone_accent': ROSSO, 'zone_on': BIANCO, 'card_bg': CHIARO,
        'card_border': {'top': 3, 'right': 3, 'bottom': 3, 'left': 3, 'linked': True, 'style': 'solid', 'color': HEX['text']},
        'align': 'left'}), style={'tile_max_width': '760px'})
    out.append(t.section([
        t.row([t.col([sechead(t, 'non sai da dove partire?', ['tre domande', 'e una bici.'], size=60, layout='stack',
                              colors=[INK, BLU]),
                      image(t, 'pexels-16096769.jpg', 'Una bici azzurra appoggiata a un muro giallo sotto una finestra',
                            ratio='1/1', r=TONDO)], wm='2-5', ws='1-1'),
               t.col([quiz], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=64, valign='center'),
    ], bg=solid(GRIGIO), padding='large', html_id='quiz'))

    # Chi ti ripara la bici.
    out.append(t.section([
        t.one([sechead(t, 'chi c’è al banco', ['tre meccanici,', 'tre colori.'], size=60)]),
        t.row([t.col([persona(t, 'pexels-16884264.jpg', 'Ettore', 'il fondatore · i restauri',
                              'Ha aperto nel 1999 dopo trent’anni da meccanico al velodromo. Riconosce un telaio dal rumore della catena.', ROSSO, 'center 25%')], wm='1-3', ws='1-1'),
               t.col([persona(t, 'pexels-28100861.jpg', 'Samuel', 'le ruote · su misura',
                              'Monta le ruote a mano e salda i telai su misura. Il martedì insegna a cambiare una camera d’aria.', BLU, 'center 30%')], wm='1-3', ws='1-1'),
               t.col([persona(t, 'pexels-16567773.jpg', 'Giulia', 'noleggi · i giri della domenica',
                              'Disegna i giri sugli argini e sul Delta, e vi consegna la bici a noleggio con la mappa in tasca.', GIALLO, 'center 25%')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=40),
    ], padding='large', html_id='persone'))

    # La domenica si esce: il video della strada bianca.
    domenica = t.classic('cta-banner', {
        'headline': 'la domenica', 'headline_accent': 'si pedala insieme.', 'headline_accent_italic': False,
        'subtitle': 'Un giro facile sulle mura, una lunga sul Delta del Po, una sera con le luci di Natale. Si parte dall’officina alle nove.',
        'cta_text': 'le prossime uscite', 'cta_url': GIRI + '#uscite', 'cta2_text': 'noleggia una bici', 'cta2_url': GIRI + '#noleggio',
        'cta2_bg': 'transparent', 'cta2_color': BIANCO, 'cta2_border': BIANCO,
        'bg': video_bg(V_GRAVEL, overlay=42, color=NERO), 'text_color': BIANCO, 'accent_color': GIALLO, 'subtitle_color': ON_SOFT,
        'cta_bg': GIALLO, 'cta_bg_hover': BIANCO, 'cta_color': HEX['dark'], 'cta_color_hover': HEX['dark'],
        'cta_radius': SQ, 'cta_radius_hover': SQ, 'headline_font_family': 'heading', 'headline_size': 76, 'headline_weight': '800',
        'subtitle_size': 18, 'layout': 'stack', 'banner_radius': SQ, 'banner_radius_hover': SQ,
        'banner_padding': {'top': 120, 'right': 48, 'bottom': 120, 'left': 48},
    })
    out.append(t.section([t.one([domenica], anim='fade')], width='fullbleed', padding='remove-vertical'))

    # Dove.
    out.append(t.section([
        t.row([t.col([sechead(t, 'dove siamo', ['via ripagrande 18,', 'sotto le mura.'], size=54, layout='stack'),
                      contacts(t),
                      text(t, '<p>A due passi dalla Darsena e dal percorso delle mura. Davanti all’officina c’è la rastrelliera: '
                              'le bici in attesa le portiamo dentro la sera.</p>', size=16, pad=(18, 0, 0, 0))], wm='2-5', ws='1-1'),
               t.col([osm(t, 480)], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
        t.one([hours(t)], style={'margin_top': 48}),
    ], padding='large', html_id='dove'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  OFFICINA
# ══════════════════════════════════════════════════════════════════════════════

LISTINO = [
    ('Tagliando completo', 'Pulizia, regolazione di freni e cambio, controllo delle ruote. In 48 ore.', '45 €', 'pexels-6334159.jpg'),
    ('Camera d’aria', 'Smontaggio, camera nuova e rimontaggio. Mentre aspettate.', '12 €', 'pexels-5446296.jpg'),
    ('Freni', 'Pastiglie o pattini nuovi, cavi e regolazione.', 'da 18 €', 'pexels-36398681.jpg'),
    ('Cambio e catena', 'Catena nuova, pulizia della trasmissione e registrazione.', 'da 35 €', 'pexels-40046218.jpg'),
    ('Centratura della ruota', 'Raggi in tensione e cerchio dritto, in giornata.', '20 €', 'pexels-1154089.jpg'),
    ('Bici elettrica', 'Diagnosi del motore e della batteria, aggiornamento.', 'da 40 €', 'pexels-10263879.jpg'),
]

PASSI = [
    ('01', 'pexels-36518530.jpg', 'la portate', 'Senza appuntamento per le piccole cose, prenotando per il tagliando. Vi leghiamo il cartellino rosso al manubrio.'),
    ('02', 'pexels-5446296.jpg', 'il preventivo', 'Entro sera vi scriviamo cosa c’è da fare e quanto costa. Se non vi convince, la riprendete gratis.'),
    ('03', 'pexels-6334159.jpg', 'il messaggio', 'Quando è pronta vi arriva un messaggio. Il cartellino serve anche per sapere a che punto è.'),
    ('04', 'pexels-7478562.jpg', 'il ritiro', 'La provate in strada con noi prima di pagare. Per tre mesi, se qualcosa non va, la rimettiamo a posto.'),
]

FAQ_OFFICINA = [
    ('Serve prenotare?', 'Per una camera d’aria o una regolazione no: passate. Per il tagliando sì, così la bici esce in 48 ore e non in una settimana.'),
    ('Riparate le bici elettriche?', 'Sì, tutti i motori più diffusi. Per la batteria vi diciamo prima se conviene ripararla o cambiarla.'),
    ('Venite a prenderla voi?', 'Dentro le mura sì, col cargo: 10 € andata e ritorno. Ditecelo nel modulo.'),
    ('E se la riparazione costa troppo?', 'Ve lo diciamo nel preventivo. A volte una bici usata revisionata da noi costa meno: ne abbiamo sempre qualcuna.'),
]


def build_officina():
    t = Tpl(ID, 'officina')
    out = [titlebar(t, 'Tagliandi, freni, cambi e ruote, con il preventivo prima e il messaggio quando è pronta.', ROSSO,
                    'pexels-6156531.jpg')]
    listino = t.classic('pricelist', {
        'items': [{'title': n, 'description': d, 'price': p, 'image_url': img(f), 'highlighted': False, 'badge': ''}
                  for n, d, p, f in LISTINO],
        'separator_style': 'solid', 'separator_color': INK, 'title_color': INK, 'price_color': ROSSO,
        'description_color': MUTED, 'image_size': 76, 'image_border_radius': '999', 'show_image': True,
        'price_position': 'right', 'title_size': '22', 'description_size': '15', 'price_size': '24',
    }, style={'bg': solid(CHIARO), 'padding_top': 24, 'padding_right': 32, 'padding_bottom': 24, 'padding_left': 32,
              'border_radius': 0, 'border': {'top': 3, 'right': 3, 'bottom': 3, 'left': 3, 'style': 'solid', 'color': HEX['text']}})
    mani = t.classic('video', {'source_type': 'file', 'file_url': img(V_MANI + '.mp4'), 'poster_image': img(V_MANI + '.jpg'),
                               'display_mode': '1:1', 'aspect_ratio': '1:1', 'facade': False, 'autoplay': True, 'muted': True,
                               'loop': True, 'controls': False, 'show_play_icon': False, 'overlay_text': '', 'overlay_opacity': 0,
                               'border_radius': 999,
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                      style={'border_radius': 999, 'overflow': 'hidden'})
    out.append(t.section([
        t.row([t.col([sechead(t, 'il listino', ['prezzi chiari,', 'pezzi a parte.'], size=56, layout='stack',
                              tagline='I pezzi di ricambio si pagano a parte e ve li facciamo vedere prima. La manodopera è quella scritta qui.'),
                      mani], wm='1-3', ws='1-1'),
               t.col([listino], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=56, valign='start'),
    ], padding='large', html_id='listino'))

    passi = t.classic('step-timeline', {
        'items': [{'counter': c, 'tag_text': '', 'tag_dot_color': ROSSO, 'media_type': 'image', 'media_image': img(f), 'media_label': '',
                   'media_content': '', 'media_bg': GRIGIO, 'media_color': ROSSO, 'pre_title': '', 'title': ti, 'title_accent': '',
                   'title_after': '', 'title_accent_italic': False, 'title_after_italic': False, 'description': d,
                   'footer_value': '', 'footer_label': '', 'separator_text': ''} for c, f, ti, d in PASSI],
        'show_timeline': True, 'timeline_line_color': rgba('#FFFFFF', 0.3), 'timeline_dot_color': GIALLO, 'timeline_dot_size': 16,
        'timeline_height': 3, 'counter_font_family': 'heading', 'counter_size': 96, 'counter_color': GIALLO, 'counter_weight': 800,
        'title_font_family': 'heading', 'title_size': 30, 'title_weight': 800, 'title_color': BIANCO, 'title_accent_color': GIALLO,
        'description_size': 15, 'description_color': ON_SOFT, 'show_media_label': False, 'show_separator': False,
        'media_aspect_ratio': '1/1', 'media_radius': TONDO, 'media_radius_hover': TONDO, 'media_shadow': 'none',
        'columns': 4, 'gap': 36, 'footer_icon': '',
    })
    out.append(t.section([t.one([sechead(t, 'come funziona', ['dal cartellino', 'alla strada.'], size=60, light=True,
                                         colors=[BIANCO, GIALLO])]), t.one([passi])], bg=solid(NERO), padding='large'))

    # La pagina d'arrivo del cartellino: il numero arriva dall'indirizzo e compila il modulo.
    stato = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Numero del cartellino', 'name': 'cartellino', 'placeholder': 'RG2614', 'required': True,
             'width': '1-2', 'options': '', 'icon': 'tag', 'param': 'cartellino'},
            {'field_type': 'tel', 'label': 'Telefono', 'name': 'telefono', 'placeholder': '333 123 4567', 'required': True,
             'width': '1-2', 'options': '', 'icon': 'phone'},
            {'field_type': 'textarea', 'label': 'Volete dirci qualcosa?', 'name': 'note', 'placeholder': 'Passo domani dopo le 17…',
             'required': False, 'width': '1-1', 'options': '', 'icon': ''},
        ],
        'email_subject': 'A che punto è la mia bici?', 'success_message': 'Ricevuto: vi scriviamo entro un’ora (in orario di officina).',
        'submit_text': 'chiedi a che punto è', 'submit_icon': 'send', 'submit_full_width': False, 'label_color': HEX['dark'],
        'input_bg': BIANCO, 'input_radius': 0, 'input_focus_border': ROSSO, 'submit_bg': NERO, 'submit_color': BIANCO,
        'submit_radius': 0, 'submit_hover_bg': ROSSO, 'submit_text_transform': 'lowercase', 'privacy_checkbox': True,
    }, style={'bg': solid(CHIARO), 'padding_top': 32, 'padding_right': 36, 'padding_bottom': 32, 'padding_left': 36, 'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'a che punto è', ['la tua bici?'], size=56, layout='stack', colors=[HEX['dark']]),
                      text(t, '<p>Se siete arrivati dal cartellino, il numero è già scritto qui accanto. Lasciateci il telefono: '
                              'vi rispondiamo con una foto della bici sul cavalletto.</p>', size=17, color=HEX['dark'])], wm='2-5', ws='1-1'),
               t.col([stato], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=48, valign='center'),
    ], bg=solid(GIALLO), padding='large', html_id='stato'))

    prenota = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': 'Lucia', 'required': True, 'width': '1-2', 'options': '', 'icon': 'user'},
            {'field_type': 'tel', 'label': 'Telefono', 'name': 'telefono', 'placeholder': '333 123 4567', 'required': True, 'width': '1-2', 'options': '', 'icon': 'phone'},
            {'field_type': 'select', 'label': 'Che bici è', 'name': 'bici', 'placeholder': 'Scegliete', 'required': True, 'width': '1-2',
             'options': 'Da città\nDa corsa o gravel\nMountain bike\nElettrica\nCargo\nD’epoca', 'icon': ''},
            {'field_type': 'select', 'label': 'Cosa serve', 'name': 'lavoro', 'placeholder': 'Scegliete', 'required': True, 'width': '1-2',
             'options': 'Tagliando completo\nFreni\nCambio e catena\nRuote\nNon lo so, dateci un’occhiata', 'icon': ''},
            {'field_type': 'date', 'label': 'Quando la portate', 'name': 'giorno', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'checkbox', 'label': 'Ritiro col cargo', 'name': 'ritiro', 'placeholder': '',
             'required': False, 'width': '1-2', 'options': 'Venite a prenderla voi (10 €, dentro le mura)', 'icon': ''},
            {'field_type': 'textarea', 'label': 'Cosa fa di strano?', 'name': 'note', 'placeholder': 'Cigola quando freno, il cambio salta sulla terza…',
             'required': False, 'width': '1-1', 'options': '', 'icon': ''},
        ],
        'email_subject': 'Prenotazione in officina dal sito', 'success_message': 'Prenotata. Vi aspettiamo: il cartellino ve lo diamo al banco.',
        'submit_text': 'prenota il tagliando', 'submit_icon': 'calendar', 'submit_full_width': True, 'label_color': INK,
        'input_bg': CHIARO, 'input_radius': 0, 'input_focus_border': ROSSO, 'submit_bg': ROSSO, 'submit_color': BIANCO,
        'submit_radius': 0, 'submit_hover_bg': NERO, 'submit_text_transform': 'lowercase', 'privacy_checkbox': True,
    }, style={'bg': solid(CHIARO), 'padding_top': 36, 'padding_right': 40, 'padding_bottom': 36, 'padding_left': 40, 'border_radius': 0,
              'border': {'top': 3, 'right': 3, 'bottom': 3, 'left': 3, 'style': 'solid', 'color': HEX['text']}})
    out.append(t.section([
        t.row([t.col([sechead(t, 'prenota', ['il tagliando.'], size=56, layout='stack', colors=[ROSSO]),
                      faq(t, FAQ_OFFICINA, 'rg-faq')], wm='2-5', ws='1-1'),
               t.col([prenota], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='start'),
    ], padding='large', html_id='prenota'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  SU MISURA E RESTAURI
# ══════════════════════════════════════════════════════════════════════════════

MIXER = [
    ('telaio', 'Acciaio, saldato qui', 690, 'pexels-4828817.jpg', ROSSO),
    ('telaio', 'Acciaio da viaggio, coi portapacchi', 790, 'pexels-38628458.jpg', BLU),
    ('telaio', 'Acciaio gravel, passaggio largo', 840, 'pexels-36565840.jpg', NERO),
    ('ruote', 'Alluminio, montate a mano', 320, 'pexels-6156531.jpg', GRIGIO),
    ('ruote', 'Mozzo con la dinamo', 410, 'pexels-36398681.jpg', ROSSO),
    ('ruote', 'Cerchi alti in carbonio', 980, 'pexels-1154089.jpg', NERO),
    ('trasmissione', 'Monocorona, 11 velocità', 380, 'pexels-40046218.jpg', GRIGIO),
    ('trasmissione', 'Doppia corona, 12 velocità', 610, 'pexels-10263879.jpg', NERO),
    ('manubrio', 'Curvo da corsa', 90, 'pexels-34756141.jpg', BIANCO),
    ('manubrio', 'Dritto da città', 60, 'pexels-40002193.jpg', NERO),
    ('sella', 'Cuoio, si fa alla vostra forma', 140, 'pexels-19438258.jpg', mix(GIALLO, 45, NERO)),
    ('colore', 'Rosso', 0, 'pexels-4828817.jpg', ROSSO),
    ('colore', 'Blu', 0, 'pexels-38628458.jpg', BLU),
    ('colore', 'Giallo', 0, 'pexels-16096769.jpg', GIALLO),
]

FASI = [
    ('01', 'la misura', 'Un’ora in officina: altezza, cavallo, braccia, e come pedalate sulla vostra bici di adesso.'),
    ('02', 'il disegno', 'Vi mandiamo il disegno del telaio quotato e il preventivo coi pezzi scelti.'),
    ('03', 'la saldatura', 'Samuel salda il telaio in due giorni; poi la verniciatura a polvere, nel colore scelto.'),
    ('04', 'il montaggio', 'Ruote a mano, trasmissione, freni. Il primo giro lo facciamo insieme, sulle mura.'),
]

RESTAURI = [
    ('pexels-38628458.jpg', 'Bici da corsa del 1978', 'Restauro', 'Sverniciata, ricromata, coi tubolari nuovi.'),
    ('pexels-13716354.jpg', 'Graziella azzurra', 'Restauro', 'La bici della zia, col cestino originale.'),
    ('pexels-34756141.jpg', 'Manubrio rifasciato', 'Dettaglio', 'Nastro di cotone e gommalacca, come una volta.'),
    ('pexels-36068823.jpg', 'Mozzo a cono', 'Dettaglio', 'Sfere nuove e grasso: gira per altri quarant’anni.'),
    ('pexels-29095762.jpg', 'Due bici da città', 'Restauro', 'Ritrovate in un cortile, ora vanno al mercato.'),
    ('pexels-36215202.jpg', 'La verniciatura', 'In officina', 'A polvere, in un capannone di Pontelagoscuro.'),
]


def build_misura():
    t = Tpl(ID, 'misura')
    out = [titlebar(t, 'Telai d’acciaio saldati sulle vostre misure e le bici d’epoca che tornano a girare.', BLU,
                    'pexels-5446289.jpg', position='center 40%')]
    mixer = t.classic('lookbookmixer', {
        'items': [{'step': s, 'name': n, 'price': p, 'image': img(f), 'color': c} for s, n, p, f, c in MIXER],
        'currency': '€', 'card_title': 'la vostra bici', 'card_steps_label': 'pezzi',
        'card_sub': 'Cambiate ogni pezzo con le frecce: il prezzo si aggiorna da solo. Montaggio e prima regolazione compresi.',
        'cta_text': 'fissa la misura', 'cta_url': MISURA + '#misura', 'panel_bg': NERO, 'slot_bg': mix(NERO, 86, BIANCO),
        'accent': GIALLO, 'accent_ink': HEX['dark'], 'name_color': BIANCO, 'price_color': ON_SOFT, 'line_color': rgba('#FFFFFF', 0.18),
        'name_font_family': 'heading', 'mono_font_family': '', 'show_swatch': True, 'image_veil': 25,
    })
    out.append(t.section([
        t.one([sechead(t, 'componi', ['la tua bici,', 'pezzo per pezzo.'], size=64,
                       tagline='Telaio, ruote, trasmissione, manubrio, sella e colore. È un’idea di prezzo: il preventivo vero arriva dopo la misura.')]),
        t.one([mixer]),
    ], padding='large', html_id='componi'))

    fasi = t.classic('process-steps', {
        'items': [{'number': n, 'title': ti, 'description': d} for n, ti, d in FASI],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'circle', 'number_color': HEX['dark'], 'number_bg': GIALLO,
        'number_size': 22, 'number_font': 'heading', 'number_weight': '800', 'title_color': INK, 'title_size': 26,
        'title_weight': '800', 'title_font': 'heading', 'desc_color': MUTED, 'desc_size': 15, 'align': 'left', 'item_gap': 12,
        'card_bg': CHIARO, 'card_border': HEX['text'], 'card_radius': SQ, 'card_padding': 28, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, 'su misura', ['quattro settimane', 'dalla misura alla strada.'], size=60, colors=[INK, BLU])]),
        t.one([fasi]),
        t.row([t.col([image(t, 'pexels-3817785.jpg', 'Il banco dell’officina sotto le finestre industriali', ratio='16/9')], wm='2-3', ws='1-1'),
               t.col([text(t, '<p>Il telaio è d’acciaio perché si ripara: una botta, una saldatura, e torna come prima. Pesa qualche '
                              'etto in più del carbonio e dura una vita.</p>', size=18),
                      button(t, 'fissa la misura', MISURA + '#misura', kind='blu')], wm='1-3', ws='1-1')],
              layout='custom', custom='66.66,33.33', gap=48, valign='center', style={'margin_top': 48}),
    ], bg=solid(GRIGIO), padding='large', html_id='misura'))

    lavori = t.classic('workgrid', {
        'items': [{'image': img(f), 'title': n, 'meta': m, 'description': d, 'tall': False, 'link_url': ''} for f, n, m, d in RESTAURI],
        'columns': 3, 'items_gap': 28, 'media_aspect': '1/1', 'media_tall_aspect': '4/5', 'media_bg': GRIGIO, 'hover_zoom': True,
        'title_font_family': 'heading', 'title_color': INK, 'title_size': 26, 'title_weight': '800', 'meta_color': ROSSO,
        'meta_size': 12, 'show_desc': True, 'desc_color': MUTED, 'desc_size': 15, 'mono_font_family': 'body',
        'card_style': 'none', 'card_radius': SQ, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, 'restauri', ['le bici del nonno', 'tornano a girare.'], size=60, colors=[INK, ROSSO],
                       tagline='Portateci la bici dalla cantina: vi diciamo cosa si salva, cosa si cambia e quanto costa, prima di cominciare.')]),
        t.one([lavori]),
    ], padding='large', html_id='restauri'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  GIRI E NOLEGGIO
# ══════════════════════════════════════════════════════════════════════════════

PROFILO = [('0', 9), ('5', 7), ('10', 4), ('15', 3), ('20', 2), ('25', 1), ('30', 0), ('35', 1), ('40', 2), ('45', 4), ('50', 5),
           ('55', 7), ('62', 9)]

USCITE = [
    ('rg-u1', 'Il giro delle mura', 'nove chilometri sulla cinta, con la sosta alla Montagnola', 'facile', '2026-11-08', '09:30', '9 km'),
    ('rg-u2', 'Ferrara–Delta in gravel', 'gli argini del Po fino a Goro, ritorno in treno', 'lunga', '2026-11-22', '08:30', '62 km'),
    ('rg-u3', 'Le luci di Natale', 'in bici per il centro, con la cioccolata in piazza', 'facile', '2026-12-13', '18:00', '12 km'),
    ('rg-u4', 'Impara a cambiare la camera', 'un’ora in officina, la camera d’aria la portiamo noi', 'corso', '2027-01-17', '10:00', '1 ora'),
    ('rg-u5', 'La prima lunga dell’anno', 'Ferrara–Comacchio per le valli, pranzo al porto canale', 'lunga', '2027-03-07', '08:30', '58 km'),
]


def build_giri():
    t = Tpl(ID, 'giri')
    out = []
    testa = t.classic('pagetitlebar', {
        'media_bg': video_bg(V_STRADA, overlay=0), 'bg': {'type': 'none'}, 'title_tag': 'h1', 'title_color': BIANCO,
        'title_size': 112, 'title_weight': '800', 'title_align': 'left',
        'subtitle': 'Le uscite della domenica e le bici a noleggio: da città, gravel e cargo, a giornata o per il fine settimana.',
        'subtitle_color': ON_SOFT, 'subtitle_size': 19, 'show_breadcrumbs': True, 'breadcrumb_color': ON_DIM, 'breadcrumb_separator': '/',
        'bg_overlay': 46, 'bg_overlay_color': NERO, 'min_height': 520, 'tile_padding': {'top': 96, 'right': 32, 'bottom': 64, 'left': 32},
        'content_width': 1200, 'padding_y': '40',
    })
    out.append(t.section([t.one([testa], anim=None)], width='fullbleed', padding='remove-vertical'))

    profilo = t.classic('chart', {
        'chart_type': 'line', 'items': [{'label': k + ' km', 'value': str(v), 'color': ROSSO} for k, v in PROFILO],
        'chart_height': 320, 'show_legend': False, 'tooltip_bg': NERO, 'tooltip_text_color': BIANCO, 'tooltip_suffix': ' m',
        'bg_color': 'transparent', 'border_width': 4, 'border_color_override': ROSSO, 'fill_area': True, 'point_radius': '0',
        'point_hover_radius': '6', 'tension': '0.45', 'grid_color': mix(INK, 10), 'text_color': MUTED, 'show_x_grid': False,
        'show_y_grid': True, 'begin_at_zero': True, 'y_max': '40', 'y_step_size': '10', 'x_label': '', 'y_label': 'metri',
        'dataset_label': 'quota', 'number_format': False,
    }, style={'bg': solid(CHIARO), 'padding_top': 28, 'padding_right': 28, 'padding_bottom': 20, 'padding_left': 20, 'border_radius': 0,
              'border': {'top': 3, 'right': 3, 'bottom': 3, 'left': 3, 'style': 'solid', 'color': HEX['text']}})
    out.append(t.section([
        t.row([t.col([sechead(t, 'la lunga del delta', ['62 chilometri,', '9 metri di salita.'], size=56, layout='stack'),
                      text(t, '<p>Il profilo della Ferrara–Goro: si scende verso il mare di nove metri e si risale di nove. '
                              'In pianura il nemico è il vento, non la salita: si va in fila, a turno davanti.</p>', size=17),
                      t.classic('desclist', {
                          'items': [{'term': 'partenza', 'definition': 'officina, ore 8:30', 'icon': ''},
                                    {'term': 'fondo', 'definition': 'argini sterrati e asfalto, 70% bianco', 'icon': ''},
                                    {'term': 'ritorno', 'definition': 'treno da Codigoro, bici al seguito', 'icon': ''}],
                          'layout': 'inline', 'term_color': BLU, 'term_font_size': '14', 'term_font_weight': '700',
                          'definition_color': INK, 'definition_font_size': '16', 'separator': True, 'border_color': LINE},
                          style={'margin_top': 8})], wm='2-5', ws='1-1'),
               t.col([profilo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', html_id='profilo'))

    cartellone = t.classic('cartellone', {
        'fonte': 'voci', 'vista': 'lista', 'raggruppa': False, 'ordina': True, 'limite': 0, 'passati': 'nascondi',
        'filtri_generi': True, 'filtri_mesi': False, 'ricerca': False, 'immagini': False, 'testo_tutti': 'tutte',
        'testo_info': 'iscriviti', 'calendario': True, 'testo_calendario': 'aggiungi al calendario', 'conto': False,
        'occhiello_prossimo': 'la prossima uscita', 'vuoto_testo': 'Le uscite della primavera arrivano presto.',
        'nessun_risultato': 'Nessuna uscita di questo tipo, per ora.',
        'accento': ROSSO, 'sfondo_voce': '', 'linea_colore': INK,
        'generi': [{'id': 'rg-g1', 'nome': 'facile', 'colore': NERO}, {'id': 'rg-g2', 'nome': 'lunga', 'colore': ROSSO},
                   {'id': 'rg-g3', 'nome': 'corso', 'colore': BLU}],
        'voci': [{'id': i, 'titolo': ti, 'sottotitolo': st, 'genere': g, 'data': d, 'ora': o, 'data_fine': '', 'orari': '',
                  'luogo': 'Ferrara, officina', 'durata': dur, 'prezzo': 'gratis', 'stato': '', 'etichette': '', 'descrizione': '',
                  'incontro': '', 'immagine': '', 'link_biglietti': '', 'link_info': GIRI + '#iscriviti'}
                 for i, ti, st, g, d, o, dur in USCITE],
    })
    out.append(t.section([
        t.one([sechead(t, 'le uscite', ['si parte', 'dall’officina.'], size=60, colors=[HEX['dark'], ROSSO],
                       tagline='Sono gratis e aperte a tutti: serve una bici a posto e il casco. Se non avete la bici, ve la prestiamo.')]),
        t.one([cartellone]),
    ], bg=solid(GIALLO), padding='large', html_id='uscite'))

    def noleggio(nome, prezzo, periodo, cosa, colore, popolare=False):
        return t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'currency_size': '26', 'period': periodo,
            'features': cosa, 'check_style': 'circle-check', 'cta_text': 'prenotala', 'cta_url': GIRI + '#iscriviti',
            'cta_bg_color': NERO, 'cta_text_color': BIANCO, 'cta_radius': '0', 'cta_hover_bg_color': colore,
            'cta_hover_text_color': BIANCO if colore != GIALLO else HEX['dark'], 'is_popular': popolare,
            'badge_text': 'la più chiesta', 'badge_bg_color': GIALLO, 'badge_text_color': HEX['dark'], 'badge_radius': '0',
            'bg_color': CHIARO, 'accent_color': colore, 'text_color': INK, 'border_radius': '0', 'shadow': 'none',
            'border': {'top': 10, 'right': 3, 'bottom': 3, 'left': 3, 'linked': False, 'style': 'solid', 'color': colore},
            'additional_info': 'casco e lucchetto compresi',
        }, style={'margin_top': 0, 'margin_right': 8, 'margin_bottom': 0, 'margin_left': 8})
    out.append(t.section([
        t.one([sechead(t, 'noleggio', ['una bici a posto,', 'per un giorno o tre.'], size=60, colors=[INK, BLU])]),
        t.row([t.col([noleggio('da città', '12', '/giorno', 'Cestino e portapacchi\nTre velocità e i parafanghi\n30 € il fine settimana', ROSSO)], wm='1-3', ws='1-1'),
               t.col([noleggio('gravel', '30', '/giorno', 'Taglia dalla S alla XL\nBorse da telaio a richiesta\nLa traccia del giro sul telefono', BLU, True)], wm='1-3', ws='1-1'),
               t.col([noleggio('cargo', '35', '/giorno', 'Fino a due bambini\nPedalata assistita\nCinture e telo per la pioggia', GIALLO)], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=16),
    ], bg=solid(GRIGIO), padding='large', html_id='noleggio'))

    iscriviti = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': 'Marco', 'required': True, 'width': '1-2', 'options': '', 'icon': 'user'},
            {'field_type': 'tel', 'label': 'Telefono', 'name': 'telefono', 'placeholder': '333 123 4567', 'required': True, 'width': '1-2', 'options': '', 'icon': 'phone'},
            {'field_type': 'select', 'label': 'Cosa vi serve', 'name': 'cosa', 'placeholder': 'Scegliete', 'required': True, 'width': '1-2',
             'options': 'Iscrivermi a un’uscita\nNoleggiare una bici da città\nNoleggiare una gravel\nNoleggiare una cargo', 'icon': ''},
            {'field_type': 'date', 'label': 'Dal giorno', 'name': 'dal', 'placeholder': '', 'required': True, 'width': '1-4', 'options': '', 'icon': ''},
            {'field_type': 'date', 'label': 'Al giorno', 'name': 'al', 'placeholder': '', 'required': False, 'width': '1-4', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Altezza', 'name': 'altezza', 'placeholder': 'Per la taglia', 'required': False, 'width': '1-2',
             'options': 'Meno di 1,60 m\nFra 1,60 e 1,75 m\nFra 1,75 e 1,90 m\nPiù di 1,90 m', 'icon': ''},
            {'field_type': 'textarea', 'label': 'Note', 'name': 'note', 'placeholder': 'Seggiolino, borse, a che ora passate…',
             'required': False, 'width': '1-2', 'options': '', 'icon': ''},
        ],
        'email_subject': 'Uscita o noleggio dal sito', 'success_message': 'Fatto! Vi scriviamo per confermare la bici o l’uscita.',
        'submit_text': 'manda la richiesta', 'submit_icon': 'send', 'submit_full_width': True, 'label_color': INK,
        'input_bg': CARTA, 'input_radius': 0, 'input_focus_border': BLU, 'submit_bg': BLU, 'submit_color': BIANCO,
        'submit_radius': 0, 'submit_hover_bg': NERO, 'submit_text_transform': 'lowercase', 'privacy_checkbox': True,
    }, style={'bg': solid(CHIARO), 'padding_top': 36, 'padding_right': 40, 'padding_bottom': 36, 'padding_left': 40, 'border_radius': 0,
              'border': {'top': 3, 'right': 3, 'bottom': 3, 'left': 3, 'style': 'solid', 'color': HEX['text']}})
    out.append(t.section([
        t.row([t.col([sechead(t, 'iscriviti', ['o prenota', 'la bici.'], size=56, layout='stack', colors=[INK, ROSSO]),
                      image(t, 'pexels-2131791.jpg', 'Un viale alberato con due ciclisti in fondo', ratio='1/1', r=TONDO)], wm='2-5', ws='1-1'),
               t.col([iscriviti], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', html_id='iscriviti'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    testa = t.classic('section-header', {
        'eyebrow_show': True, 'eyebrow_text': 'errore 404', 'eyebrow_color': GIALLO, 'eyebrow_dot_color': ROSSO,
        'eyebrow_separator': '', 'eyebrow_font_family': 'body', 'eyebrow_font_size': 12, 'eyebrow_font_weight': '700',
        'headline_lines': [{'text': 'abbiamo forato.', 'color': BIANCO, 'italic': False},
                           {'text': 'questa pagina non c’è.', 'color': GIALLO, 'italic': False}],
        'headline_font_family': 'heading', 'headline_font_size': 104, 'headline_line_height': 0.95, 'headline_font_weight': '800',
        'headline_align': 'left', 'headline_font_size_tablet': 76, 'headline_font_size_mobile': 50,
        'tagline_show': True, 'tagline_text': 'La camera d’aria la cambiamo in dieci minuti. Intanto, tornate alla home.',
        'tagline_text_color': ON_SOFT, 'tagline_text_size': 19, 'tagline_font_family': 'body', 'layout': 'stack', 'gap': 18,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 18, 'padding_left': 0})
    return [t.section([t.one([testa, button(t, 'torna alla home', HOME, kind='giallo')], anim='fade')],
                      bg=video_bg(V_STRADA, overlay=55, color=NERO), padding='xlarge')]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': 'Raggiera', 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Sport e benessere', 'zone': 'A che punto è la mia bici?',
    'tags': ['ciclofficina', 'biciclette', 'bauhaus', 'noleggio', 'officina'],
    'description': 'Ciclofficina a Ferrara in stile Bauhaus: titoli in minuscolo, rosso blu e giallo a campiture piene, il video dei '
                   'raggi dentro un tondo sugli anelli concentrici, la ruota da 36 raggi che si disegna, il cartellino per sapere '
                   'a che punto è la bici, la bici da comporre pezzo per pezzo e il profilo della lunga del Delta.',
    'menu': {'name': 'Raggiera — menu', 'items': [
        {'title': 'officina', 'url': OFFICINA}, {'title': 'su misura', 'url': MISURA}, {'title': 'giri e noleggio', 'url': GIRI},
        {'title': 'dove siamo', 'url': HOME + '#dove'}, {'title': 'porta la bici', 'url': OFFICINA + '#prenota'}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Raggiera — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Raggiera — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Raggiera — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Raggiera — Home'},
        'officina': {'file': 'officina.json', 'type': 'page', 'title': 'Raggiera — Officina'},
        'su-misura': {'file': 'su-misura.json', 'type': 'page', 'title': 'Raggiera — Su misura e restauri'},
        'giri-e-noleggio': {'file': 'giri-e-noleggio.json', 'type': 'page', 'title': 'Raggiera — Giri e noleggio'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'officina': {'title': 'officina', 'template': 'officina', 'slug': 'officina'},
        'su-misura': {'title': 'su misura e restauri', 'template': 'su-misura', 'slug': 'su-misura'},
        'giri-e-noleggio': {'title': 'giri e noleggio', 'template': 'giri-e-noleggio', 'slug': 'giri-e-noleggio'},
    },
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Poppins\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'League Spartan\', \'Futura\', system-ui, sans-serif',
            'font_family_mono': '\'Poppins\', system-ui, sans-serif',
            'font_size_base': '17px', 'line_height': '1.65', 'font_weight_body': '400', 'font_weight_heading': '800',
            'heading_line_height': '0.98', 'heading_letter_spacing': '-0.02em', 'heading_text_transform': 'lowercase',
            'letter_spacing': '0', 'font_size_h1': '5.6rem', 'font_size_h2': '3.8rem', 'font_size_h3': '1.9rem',
            'font_size_h4': '1.4rem', 'font_size_h5': '1.15rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3.1rem',
            'font_size_h2_mobile': '2.4rem', 'font_size_h3_mobile': '1.5rem',
        },
        'google_fonts': ['League Spartan', 'Poppins'],
        'google_fonts_weights': '400;500;600;700;800',
        'google_fonts_italic': [],
        'buttons': {'font_size': '16px', 'font_weight': '700', 'padding_x': '26px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'lowercase', 'letter_spacing': '0', 'hover_brightness': '92'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1240px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': HEX['light'], 'field_border_color': HEX['text'], 'field_border_width': '2',
                  'field_border_radius': '0px', 'field_padding': '12px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': '0 0 0 3px ' + rgba(HEX['accent'], 0.6),
                  'label_font_size': '14px', 'label_font_weight': '700', 'label_margin_bottom': '6px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': False, 'opacity': 0, 'scale': 160},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'officina.json': build_officina(), 'su-misura.json': build_misura(), 'giri-e-noleggio.json': build_giri(),
              '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
