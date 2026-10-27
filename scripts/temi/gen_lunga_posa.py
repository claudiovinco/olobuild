# -*- coding: utf-8 -*-
"""Genera il tema «Lunga Posa» (id lunga-posa): lo studio di una fotografa di architettura e oggetti a Torino, che fa
anche matrimoni (pochi) e le feste della città.

Minimale come i portfolio premiati del 2026 (Almira Kho, Brandon Yasin): carta grigia e grafite, un solo colore di
segnale, l'arancio del punto di messa a fuoco. Titoli nel Funnel Display sottile, testo in Funnel Sans, i dati di
scatto in Fragment Mono come si leggono nel mirino. Angoli vivi, fili da un pixel, grana leggera.

Dettagli: l'apertura «linea» col nome, il mirino della fotocamera sopra il video d'apertura (il fuoco segue il mouse,
il clic scatta e il fotogramma diventa un provino; dodici pose come un rullino 120), i dati di scatto sotto ogni foto.

Moduli: le tre cose che fa, una sezione che si svela di lato sopra l'altra (Reveal orizzontale); i lavori con la foto
che segue il cursore (Hover List); la luce nel bicchiere in video; il metodo in quattro passi; le quattro serie che
scorrono di lato (Cover orizzontale) e le loro gallerie; la giornata di un matrimonio in quattro fotografie, svelate di
lato; i listini; le feste in città; lo studio, la borsa e il modulo per scrivere.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'lunga-posa'
NAME = 'Lunga Posa'

HEX = {
    'primary': '#E04A21', 'primary_contrast': '#141414', 'secondary': '#8A8F86', 'secondary_contrast': '#141414',
    'accent': '#E04A21', 'dark': '#121212', 'light': '#F7F6F2', 'muted': '#E3E0D9', 'muted_contrast': '#161616',
    'text': '#161616', 'text_muted': '#6E6C66', 'background': '#EEECE7', 'border': '#D3D0C8', 'link': '#161616',
}

INK = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
FUOCO = 'var(--olo-color-primary)'
FONDO = 'var(--olo-color-background)'
CARTA = 'var(--olo-color-muted)'
NERO = 'var(--olo-color-dark)'
CHIARO = 'var(--olo-color-light)'
FILO = 'var(--olo-color-border)'
FILO_INK = mix(INK, 70)
SOFT_CHIARO = mix(CHIARO, 84)
MONO = 'var(--olo-font-family-mono)'
SQ = radius(0)

ADDRESS = 'Via Mazzini 31, 10123 Torino'
PHONE = '011 000 3170'
PHONE_TEL = 'tel:+390110003170'
MAIL = 'studio@lungaposa.example'
LAT, LNG = '45.0627', '7.6862'
HOME = '{{PAGINA:home}}'
LAVORI = '{{PAGINA:lavori}}'
MATRIMONI = '{{PAGINA:matrimoni}}'
EVENTI = '{{PAGINA:eventi}}'
STUDIO = '{{PAGINA:studio}}'

V_MURO = 'pexels-video-6963729'      # l'ombra di un ramo e della finestra sul muro grigio, il vaso sulla mensola
V_BICCHIERE = 'pexels-video-4513055'  # la luce che attraversa un bicchiere d'acqua sul pavimento
V_VELO = 'pexels-video-7289093'       # la sposa dietro il velo che si muove
V_TENDA = 'pexels-video-35161057'     # la tenda bianca con l'ombra delle foglie
V_FOGLIE = 'pexels-video-7876536'     # le ombre delle foglie sul lino
V_LUCI = 'pexels-video-13514070'      # una via piena di gente sotto gli ombrelli colorati illuminati

PHOTOS = [
    'pexels-30380818.jpg', 'pexels-18891774.jpg', 'pexels-29032809.jpg', 'pexels-34492970.jpg', 'pexels-34193139.jpg',
    'pexels-19063818.jpg', 'pexels-21038762.jpg', 'pexels-25643330.jpg', 'pexels-16512513.jpg', 'pexels-2191622.jpg',
    'pexels-13094315.jpg', 'pexels-36901388.jpg', 'pexels-39259349.jpg', 'pexels-10324965.jpg', 'pexels-20874685.jpg',
    'pexels-26585250.jpg', 'pexels-167712.jpg', 'pexels-7206230.jpg', 'pexels-8694375.jpg', 'pexels-2990835.jpg',
    'pexels-20532119.jpg', 'pexels-15658307.jpg', 'pexels-29689915.jpg', 'pexels-30539407.jpg', 'pexels-18529574.jpg',
    'pexels-24374988.jpg', 'pexels-18999568.jpg', 'pexels-17801359.jpg', 'pexels-6762296.jpg', 'pexels-12172057.jpg',
    'pexels-13745224.jpg', 'pexels-36580004.jpg', 'pexels-16391246.jpg', 'pexels-37206195.jpg', 'pexels-8123128.jpg',
    'pexels-7031577.jpg', 'pexels-28005518.jpg', 'pexels-5764081.jpg', 'pexels-28449815.jpg', 'pexels-19562958.jpg',
    'pexels-32796436.jpg', 'pexels-27363812.jpg', 'pexels-34543843.jpg', 'pexels-18535623.jpg', 'pexels-36339454.jpg',
    'pexels-3094345.jpg', 'pexels-30705517.jpg', 'pexels-14429585.jpg', 'pexels-11779495.jpg', 'pexels-34241576.jpg',
    'pexels-12289213.jpg', 'pexels-36227358.jpg', 'pexels-39988496.jpg', 'pexels-31965857.jpg', 'pexels-36237383.jpg',
    'pexels-12312412.jpg', 'pexels-36372741.jpg', 'pexels-20889964.jpg', 'pexels-12476804.jpg', 'pexels-8532943.jpg',
    'pexels-17904312.jpg', 'pexels-17904295.jpg', 'pexels-11134290.jpg', 'pexels-6825571.jpg', 'pexels-4252522.jpg',
    'pexels-39915418.jpg', 'pexels-6702631.jpg', 'pexels-26093496.jpg', 'pexels-30688132.jpg', 'pexels-39325084.jpg',
    'pexels-11474966.jpg', 'pexels-30747177.jpg', 'pexels-11693638.jpg', 'pexels-29221491.jpg', 'pexels-8767270.jpg',
    'pexels-8533745.jpg', 'pexels-38816868.jpg', 'pexels-7119223.jpg', 'pexels-27584189.jpg', 'pexels-9566058.jpg',
    'pexels-8143699.jpg', 'pexels-7383644.jpg', 'pexels-37468396.jpg', 'pexels-14925658.jpg', 'pexels-32031528.jpg',
    'pexels-10141648.jpg', 'pexels-37034969.jpg',
]
VIDEOS = [V_MURO, V_BICCHIERE, V_VELO, V_TENDA, V_FOGLIE, V_LUCI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=40, color=NERO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=36, color=NERO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=40, color=INK):
    """Il dato in mono sopra («02 — Matrimoni»), il titolo sottile sotto, la seconda riga più spenta."""
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': TENUE, 'eyebrow_dot_color': FUOCO,
        'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '400',
        'headline_lines': [{'text': l, 'color': color if i % 2 == 0 else TENUE, 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.02,
        'headline_font_weight': '300', 'headline_align': align,
        'headline_font_size_tablet': max(38, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 16, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=INK, tag='h3', pad_bottom=10, align='left', weight='300', spacing=None):
    """La parte fra <em> diventa il «testo accento»: stesso peso, nel colore del fuoco (il Funnel non ha corsivo)."""
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': False, 'accent_color': FUOCO})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(28, int(size * 0.56)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=TENUE, pad_bottom=14, align='left', pad_top=0):
    """I dati di scatto: monospazio, maiuscolo, spaziato."""
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 11, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '1.5'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=16, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='filo', align='left', pad_top=14):
    """Pulsanti piatti ad angoli vivi: grafite pieno, col filo, o chiaro sopra le foto."""
    if kind == 'pieno':
        o = {'bg_color': INK, 'text_color': FONDO, 'hover_bg_color': FUOCO, 'hover_text_color': INK}
    elif kind == 'chiaro':
        o = {'bg_color': 'transparent', 'text_color': CHIARO, 'hover_bg_color': CHIARO, 'hover_text_color': NERO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(CHIARO, 70), 'shadow_inset': True}
    else:
        o = {'bg_color': 'transparent', 'text_color': INK, 'hover_bg_color': INK, 'hover_text_color': FONDO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': FILO_INK, 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 11, 'font_weight': '500',
              'text_transform': 'uppercase', 'letter_spacing': '2'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', drift=False, lightbox=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -26, 'position': 0}, {'value': 26, 'position': 100}]}} if drift else None)


def scatto(t, name, alt, exif, ratio='4/5', position='center center', drift=False):
    """Una foto col suo cartellino: i dati di scatto in mono sotto."""
    return [image(t, name, alt, ratio=ratio, position=position, drift=drift), label(t, exif, pad_top=12, pad_bottom=0)]


def video(t, name, ratio='4:5'):
    return t.bare('video', {'source_type': 'file', 'video_url': '', 'file_url': img(name + '.mp4'),
                            'poster_image': img(name + '.jpg'), 'display_mode': ratio, 'aspect_ratio': ratio, 'cover_mode': False,
                            'object_position': 'center center', 'border_radius': 0, 'autoplay': True, 'muted': True,
                            'loop': True, 'controls': False, 'show_play_icon': False, 'facade': False})


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs, layout='vertical', color=INK):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': layout, 'term_color': TENUE, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': color, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t, color=INK):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': STUDIO + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': FUOCO, 'icon_size': '16', 'text_color': color, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': FUOCO, 'header_font_size': 22, 'header_font_weight': '300',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': INK, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, lines, body, size=104):
    """L'apertura delle pagine interne: il titolo grande sulla carta, il testo a destra in basso."""
    return t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=46)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=17, color=TENUE)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], padding='large', style={'padding_top': 190, 'padding_bottom': 64})


def pannello(t, foto, eyebrow, titolo, testo, link_t, link_u, effetto='reveal-h', pos='center center', overlay=40,
             html_id=None):
    """Una sezione a tutto schermo con la foto: entra di lato nel gruppo orizzontale (Reveal o Cover orizzontale).
    Pochi righi, così sta in una schermata anche sul telefono e il gruppo resta orizzontale."""
    blocco = [label(t, eyebrow, color=SOFT_CHIARO),
              headline(t, titolo, size=72, color=CHIARO, tag='h2', pad_bottom=16, weight='300'),
              text(t, '<p>' + testo + '</p>', size=16, color=SOFT_CHIARO)]
    if link_t:
        blocco.append(button(t, link_t, link_u, kind='chiaro', pad_top=10))
    return t.section([
        t.row([t.col(blocco, wm='1-2', ws='1-1'), t.col([], wm='1-2', ws='1-1')], layout='custom', custom='52,48',
              valign='end', anim=None),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='large', width='large', sticky_effect=effetto,
        sticky_h_align='bottom', html_id=html_id, style={'padding_top': 140, 'padding_bottom': 80})


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': NAME, 'logo_text_size': '22', 'logo_text_color': INK,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': mix(FONDO, 90), 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': INK, 'hover_color': FUOCO, 'active_color': FUOCO,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '34',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': FUOCO,
        'button_mode': 'last', 'btn_bg': INK, 'btn_color': FONDO, 'btn_hover_color': INK,
        'btn_radius': SQ, 'btn_padding': {'top': 12, 'right': 20, 'bottom': 12, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '500', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': FONDO, 'mobile_text_color': INK, 'mobile_accent_color': FUOCO,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '40', 'mobile_link_size': '40',
        'mobile_link_font': 'heading', 'mobile_link_weight': '300',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(INK, 12)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'LUNGA POSA', size=150, color=INK, tag='p', pad_bottom=0, weight='300', spacing=-4)
    claim = text(t, '<p>Adele Rivoira, fotografa. Architettura, interni e oggetti per chi progetta; dodici matrimoni l’anno; '
                    'le feste della città quando chiamano.</p>', size=15, color=TENUE, pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': INK, 'link_hover_color': FUOCO, 'active_color': FUOCO,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('I lavori', LAVORI), ('Matrimoni', MATRIMONI), ('Eventi in città', EVENTI), ('Lo studio', STUDIO),
                  ('Privacy', '/privacy-policy/')])
    borsa = text(t, '<p>Hasselblad 503 CW · rullino 120<br>Fujifilm GFX 100 · medio formato<br>Linhof 4×5 · per l’architettura</p>'
                    '<p>Studio aperto martedì — sabato, 10 — 18.</p>', size=15, color=INK)
    legal = text(t, '<p>© 2026 Lunga Posa · Adele Rivoira fotografa · P. IVA 00000000000 · Le stampe fine art sono firmate e '
                    'numerate.</p>', size=12, color=TENUE)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'linkedin', 'url': 'https://linkedin.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': TENUE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': INK, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim, contacts(t)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine'), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'In borsa'), borsa], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
                          'padding_top': 22, 'margin_top': 24})
    return [t.section([top, t.one([brand], anim='fade', style={'margin_top': 64}), bottom], bg=solid(FONDO), padding='large',
                      style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}})]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

LAVORI_SCELTI = [
    ('Casa sul Po', 'Architettura · Torino · 2026', 'pexels-34193139.jpg', '#casa-sul-po'),
    ('Frassino', 'Sedie per Officina Ruà · 2025', 'pexels-11474966.jpg', '#frassino'),
    ('Cemento e luce', 'Una fabbrica restaurata · Ivrea · 2025', 'pexels-25643330.jpg', '#cemento'),
    ('Sette vasi', 'Ceramiche di Castellamonte · 2026', 'pexels-27584189.jpg', '#sette-vasi'),
    ('Silvia e Tommaso', 'Matrimonio · Langhe · 2025', 'pexels-12312412.jpg', ''),
    ('Notte bianca in Vanchiglia', 'Festa di quartiere · Torino · 2026', 'pexels-30539407.jpg', ''),
]


def build_home():
    t = Tpl(ID, 'home')
    out = []

    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Adele Rivoira · fotografa · Torino', 'eyebrow_dot': True,
        'title': 'Guardo a lungo.<br>Scatto una volta.',
        'subtitle': 'Architettura, interni e oggetti per chi progetta. E quando serve matrimoni ed eventi in città, con lo stesso '
                    'sguardo lento. Muovete il mouse: il fuoco lo decidete voi.',
        'media_bg': {'type': 'video', 'video_url': img(V_MURO + '.mp4'), 'video_poster': img(V_MURO + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': FONDO, 'overlay_top': 0.0, 'overlay_bottom': 0.18, 'overlay_sides': False, 'overlay_direction': '',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': FUOCO, 'text_color': INK, 'title_color': INK, 'subtitle_color': mix(INK, 78),
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 84, 'title_font_weight': '300',
        'title_line_height': '0.98', 'title_letter_spacing': '-3', 'title_text_transform': 'none',
        'subtitle_font_size': 16, 'subtitle_max_width': 440,
        'min_height': '100vh', 'content_max_width': 640, 'vertical_align': 'bottom', 'horizontal_align': 'right',
        'text_align': 'left', 'tile_padding': {'top': 150, 'right': 56, 'bottom': 176, 'left': 48},
        'cta_text': 'I lavori', 'cta_url': LAVORI, 'cta_bg_color': INK, 'cta_text_color': FONDO,
        'cta_radius': SQ, 'cta_size': '11', 'cta_style': 'filled', 'cta_font_weight': '500', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2',
        'cta2_text': 'Scrivimi', 'cta2_url': STUDIO + '#scrivimi', 'cta2_text_color': INK, 'cta2_style': 'ghost',
        'meta_text': '', 'scroll_hint': '', 'scene_depth': 'none', 'module': '',
        # il mirino: dodici pose come un rullino 120, il fuoco parte dal vaso sulla mensola
        'vf_on': True, 'vf_readout': 'A · 1/30 · f/11 · ISO 64', 'vf_frames': 12, 'vf_hint': 'Clicca per scattare',
        'vf_shutter_label': 'Scatta una foto', 'vf_focus': True, 'vf_shutter': True, 'vf_strip': True, 'vf_grid': True,
        'vf_meter': True, 'vf_x': 17, 'vf_y': 50, 'vf_color': INK, 'vf_accent': FUOCO, 'vf_blur': 7, 'vf_focus_size': 24,
        'vf_font_family': MONO,
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    numeri = t.classic('statstrip', {
        'items': [{'value': '12', 'label': 'pose in un rullino 120'}, {'value': '1', 'label': 'luce: quella che c’è'},
                  {'value': '48 h', 'label': 'per i provini'}, {'value': '2014', 'label': 'il primo lavoro su commissione'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': INK, 'value_size': 52, 'value_weight': '300',
        'label_color': TENUE, 'label_size': 11, 'label_uppercase': True, 'label_weight': '400', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([
        t.row([t.col([label(t, '00 — Il metodo')], wm='1-4', ws='1-1'),
               t.col([headline(t, 'Una casa, una sedia, due persone che si sposano: le fotografo nello stesso modo. Con calma, '
                                  'con <em>la luce che c’è</em>.', size=46, tag='h2', pad_bottom=0)], wm='3-4', ws='1-1')],
              layout='custom', custom='25,75', gap=32, valign='start'),
        t.one([numeri], style={'margin_top': 72}),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 120}))

    # Tre modi di guardare: le tre sezioni si svelano di lato, una sopra l'altra, mentre si scende.
    out.append(pannello(t, 'pexels-19562958.jpg', '01 / 03 — Architettura e interni',
                        'Le case prima che arrivino i mobili.',
                        'Per studi di architettura e riviste: il sopralluogo all’alba, una giornata intera di luce, venti immagini '
                        'scelte insieme. Per gli archivi le stampo e le firmo.',
                        'Gli interni', LAVORI + '#casa-sul-po', overlay=34, html_id='architettura'))
    out.append(pannello(t, 'pexels-8767270.jpg', '02 / 03 — Oggetti e design',
                        'Una sedia ha tre lati buoni. Li trovo tutti.',
                        'Cataloghi, lanci e schede prodotto per aziende di arredo e designer: sul fondale in studio o nella casa '
                        'vera, con la luce di una finestra.',
                        'Gli oggetti', LAVORI + '#frassino', overlay=38, html_id='oggetti'))
    out.append(pannello(t, 'pexels-30705517.jpg', '03 / 03 — Matrimoni ed eventi',
                        'Dodici matrimoni l’anno. E le feste della città.',
                        'Senza pose e senza scalette: guardo, aspetto, scatto. Dalle Langhe ai cortili di Torino, dai concerti in '
                        'piazza alle inaugurazioni di quartiere.',
                        'I matrimoni', MATRIMONI, overlay=34, html_id='persone'))

    scelti = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': c, 'desc': c, 'number': '%02d' % (i + 1), 'image': img(f),
                   'link_url': (LAVORI + a) if a else (MATRIMONI if 'Matrimonio' in c else EVENTI), 'color': ''}
                  for i, (n, c, f, a) in enumerate(LAVORI_SCELTI)],
        'lead_mode': 'number', 'number_color': TENUE, 'number_hover_color': FUOCO,
        'name_font_family': 'heading', 'name_weight': '300', 'name_color': INK, 'name_hover_color': INK, 'name_size': 48,
        'name_uppercase': False,
        'desc_color': TENUE, 'desc_hover_color': INK, 'desc_size': 13, 'sub_uppercase': False,
        'row_padding_y': 24, 'hover_indent': 28, 'hover_bg': 'transparent', 'line_color': FILO,
        'peek': True, 'peek_mode': 'image', 'peek_width': 320, 'peek_ratio': '4/5',
        'row_padding': {'top': 24, 'right': 0, 'bottom': 24, 'left': 0},
    })
    out.append(t.section([
        t.row([t.col([sechead(t, '04 — I lavori scelti', ['Sei lavori,', 'sei luci diverse.'], size=56, layout='stack',
                              size_mobile=38),
                      text(t, '<p>Passate sopra i nomi: la foto segue il cursore. Ogni serie ha la sua pagina, coi provini e i '
                              'dati di scatto.</p>', size=16),
                      button(t, 'Tutti i lavori', LAVORI)], wm='1-3', ws='1-1'),
               t.col([scelti], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large', html_id='lavori', style={'padding_top': 150}))

    out.append(t.section([
        centered(t, [label(t, 'Studio · via Mazzini · 16:40 · ottobre', align='center', color=INK),
                     headline(t, 'La luce arriva sempre <em>da una parte sola</em>.', size=80, tag='h2', align='center',
                              pad_bottom=18),
                     text(t, '<p>In studio c’è una finestra a nord e nient’altro. Il flash lo porto, ma resta quasi sempre spento.</p>',
                          size=16, color=mix(INK, 80), align='center')]),
    ], bg=video_bg(V_BICCHIERE, overlay=14, color=FONDO), padding='xlarge', html_id='luce',
        style={'padding_top': 230, 'padding_bottom': 230}))

    metodo = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Il sopralluogo', 'description': 'Vengo a vedere, di solito all’ora in cui scatteremo. '
                                                                              'Gratis, entro trenta chilometri da Torino.'},
                  {'number': '02', 'title': 'La giornata', 'description': 'Una luce sola, quella che c’è. Si scatta finché è giusta, '
                                                                         'poi ci si ferma.'},
                  {'number': '03', 'title': 'I provini', 'description': 'In quarantotto ore, su un foglio come quelli della camera '
                                                                       'oscura: si sceglie insieme.'},
                  {'number': '04', 'title': 'Le stampe', 'description': 'Le immagini finite in alta risoluzione e, per chi le vuole, '
                                                                       'le stampe fine art firmate.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': FUOCO, 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': INK, 'title_size': 24,
        'title_weight': '300', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 14, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO_INK},
        'card_radius': radius(0), 'card_padding': 22, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, '05 — Come lavoro', ['Quattro passi,', 'nessuna fretta.'], size=64,
                       tagline='Vale per una casa, per un catalogo e anche per un matrimonio: cambia solo quanto dura la giornata.')]),
        t.one([metodo]),
    ], bg=solid(CARTA), padding='large'))

    voce = t.classic('testimonial', {
        'quote': 'Adele è l’unica che aspetta. Ci ha chiesto di tornare alle sette di sera, con la casa vuota: <em>aveva '
                 'ragione, e la rivista ha messo quella foto in copertina.</em>',
        'author_name': 'Giulia Ferro', 'author_role': 'Ferro Vallino Architetti, Torino',
        'rating': '5', 'text_color': INK, 'star_color': FUOCO, 'author_color': TENUE, 'quote_accent_color': FUOCO,
        'quote_font': 'heading', 'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': FUOCO,
    })
    clienti = t.classic('trust-strip', {
        'items': [{'icon': '', 'icon_color': '', 'text': n, 'badge': ''} for n in
                  ['Ferro Vallino Architetti', 'Officina Ruà', 'Ceramiche Gallo Castellamonte', 'Rivista Soglie',
                   'Atelier Doranova', 'Falegnameria Bosio']],
        'text_color': TENUE, 'text_size': 15, 'font_family': 'body', 'align': 'left', 'flow': 'wrap', 'gap': 26,
        'variant': 'inline', 'icon_size': 16, 'separator_char': '·', 'separator_color': FUOCO,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.one([voce], anim='fade'),
        t.row([t.col([label(t, 'Hanno lavorato con me', pad_bottom=10), clienti], wm='1-1', ws='1-1')], anim='fade',
              style={'margin_top': 64, 'padding_top': 28,
                     'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}}),
    ], padding='large', style={'padding_top': 130, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col([headline(t, 'Un progetto, un matrimonio, <em>una festa in piazza?</em>', size=76, tag='h2', pad_bottom=20),
                      text(t, '<p>Scrivetemi due righe: cosa, dove, quando. Rispondo entro sera, anche il sabato.</p>', size=17),
                      button(t, 'Scrivimi', STUDIO + '#scrivimi', kind='pieno', pad_top=12)], wm='3-5', ws='1-1'),
               t.col(scatto(t, 'pexels-18529574.jpg', 'Adele Rivoira, in bianco e nero, con la fotocamera davanti al viso',
                            'Autoritratto · Hasselblad · 1/60 · f/5.6', ratio='4/5', drift=True), wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], bg=solid(CARTA), padding='large', style={'padding_top': 140, 'padding_bottom': 140}))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='LUNGA POSA',
                page_intro_caption='Esposizione di due secondi', page_intro_bg=HEX['background'],
                page_intro_color=HEX['text'], page_intro_duration=2400, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  I LAVORI
# ══════════════════════════════════════════════════════════════════════════════

SERIE = [
    {'id': 'casa-sul-po', 'nome': 'Casa sul Po', 'per': 'Ferro Vallino Architetti · 2026', 'cover': 'pexels-34193139.jpg',
     'testo': 'Una casa degli anni Trenta sulla collina, svuotata e rifatta in calce e pietra di Luserna. Tre giorni, dall’alba al '
              'tramonto, con la casa ancora vuota.',
     'exif': 'Linhof 4×5 · 1/8 · f/22 · ISO 100',
     'foto': [('pexels-12172057.jpg', 'Una sedia sola nella stanza, con le ombre della finestra sul pavimento'),
              ('pexels-7031577.jpg', 'Il corridoio bianco con le finestre alte'),
              ('pexels-21038762.jpg', 'La scala interna con le ombre delle piante, in bianco e nero'),
              ('pexels-28005518.jpg', 'Le ombre della finestra sul muro bianco')]},
    {'id': 'frassino', 'nome': 'Frassino', 'per': 'Officina Ruà · catalogo 2025', 'cover': 'pexels-11474966.jpg',
     'testo': 'Dodici sedie di frassino curvato a vapore, fotografate in studio sul fondale grigio e poi nelle case dei clienti, '
              'dove si erano già consumate un po’.',
     'exif': 'GFX 100 · 1/125 · f/8 · ISO 100',
     'foto': [('pexels-29221491.jpg', 'Una sedia di legno e la sua ombra sul muro, in bianco e nero'),
              ('pexels-30747177.jpg', 'Una sedia di legno vicino a una finestra vecchia, nel sole'),
              ('pexels-11693638.jpg', 'Una sedia azzurra contro il muro bianco al sole'),
              ('pexels-13745224.jpg', 'Una sedia di legno in una stanza vuota con la luce che entra')]},
    {'id': 'cemento', 'nome': 'Cemento e luce', 'per': 'Una fabbrica restaurata · Ivrea · 2025', 'cover': 'pexels-25643330.jpg',
     'testo': 'Una fabbrica di macchine per scrivere diventata uffici. Ho seguito il sole per un anno, una giornata per stagione, '
              'sempre dallo stesso punto.',
     'exif': 'Linhof 4×5 · 1/30 · f/16 · ISO 100',
     'foto': [('pexels-39259349.jpg', 'La luce che passa da una finestra a griglia di cemento, in bianco e nero'),
              ('pexels-2191622.jpg', 'Strutture di cemento minimali contro il cielo'),
              ('pexels-13094315.jpg', 'Un muro di cemento con le ombre geometriche'),
              ('pexels-36901388.jpg', 'L’ombra di un lampione sul muro grezzo')]},
    {'id': 'sette-vasi', 'nome': 'Sette vasi', 'per': 'Ceramiche Gallo Castellamonte · 2026', 'cover': 'pexels-27584189.jpg',
     'testo': 'Sette vasi in terra rossa di Castellamonte, ognuno con la luce dell’ora in cui era uscito dal forno.',
     'exif': 'Hasselblad 503 · 1/15 · f/11 · ISO 160',
     'foto': [('pexels-8767270.jpg', 'Due vasi gialli di ceramica con le loro ombre'),
              ('pexels-8533745.jpg', 'Un vaso di ceramica ad anello nella luce naturale'),
              ('pexels-38816868.jpg', 'Un vaso di terracotta con le foglie e la sua ombra'),
              ('pexels-9566058.jpg', 'Un vaso con un ramo fiorito sul davanzale')]},
]

CLIENTI = [
    ('2026', 'Ferro Vallino Architetti', 'Casa sul Po · tre giorni', 'architettura'),
    ('2026', 'Ceramiche Gallo Castellamonte', 'Catalogo e sito', 'oggetti'),
    ('2026', 'Rivista Soglie', 'Copertina del numero di primavera', 'architettura'),
    ('2025', 'Officina Ruà', 'Catalogo Frassino', 'oggetti'),
    ('2025', 'Studio Arco', 'Una fabbrica restaurata a Ivrea', 'architettura'),
    ('2025', 'Atelier Doranova', 'Ritratti dei designer', 'ritratto'),
    ('2024', 'Falegnameria Bosio', 'Schede prodotto, 140 pezzi', 'oggetti'),
]


def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [intro(t, 'I lavori', ['Quattro serie,', 'una luce sola.'],
                 '<p>Scorrete: le serie arrivano di lato, una dopo l’altra. Sotto, per ognuna, i provini scelti con chi me le ha '
                 'chieste e i dati di scatto.</p>')]
    for i, s in enumerate(SERIE):
        out.append(pannello(t, s['cover'], '%02d / %02d — %s' % (i + 1, len(SERIE), s['per']), s['nome'] + '.', s['testo'],
                            'I provini', '#' + s['id'], effetto='cover-h', overlay=52 if i == 0 else 44))
    for i, s in enumerate(SERIE):
        galleria = t.classic('progallery', {
            'images': [{'url': img(f), 'alt': a, 'caption': '', 'subtitle': '', 'text': ''} for f, a in s['foto']],
            'layout': 'expand', 'layout_family': 'classic', 'columns': 4, 'gap': 8, 'img_height': '420px',
            'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 0, 'mobile_columns': 2,
            'expand_ratio': 2.6, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
            'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
        })
        out.append(t.section([
            t.row([t.col([label(t, '%02d — %s' % (i + 1, s['per'])),
                          headline(t, s['nome'], size=56, tag='h2', pad_bottom=14),
                          text(t, '<p>' + s['testo'] + '</p>', size=16),
                          label(t, s['exif'], color=FUOCO, pad_top=6, pad_bottom=0)], wm='1-3', ws='1-1'),
                   t.col([galleria], wm='2-3', ws='1-1')], layout='custom', custom='30,70', gap=56, valign='center'),
        ], bg=solid(CARTA) if i % 2 else None, padding='large', html_id=s['id']))

    elenco = t.classic('worklist', {
        'items': [{'number': a, 'title': c, 'category': cosa, 'year': tipo} for a, c, cosa, tipo in CLIENTI],
        'divider_color': FILO, 'row_padding_y': 22, 'row_hover_bg': mix(INK, 4), 'hover_indent': 16,
        'number_color': TENUE, 'number_size': 12, 'title_font_family': 'heading', 'title_color': INK, 'title_size': 32,
        'title_weight': '300', 'show_category': True, 'category_color': TENUE, 'category_size': 14, 'show_year': True,
        'year_color': FUOCO, 'year_size': 12, 'show_arrow': False, 'arrow_color': INK, 'mono_font_family': 'mono',
        'row_padding': {'top': 20, 'right': 6, 'bottom': 20, 'left': 6}, 'title_size_tablet': 26, 'title_size_mobile': 21,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.one([sechead(t, 'Per chi', ['Studi, aziende,', 'qualche rivista.'], size=64,
                       tagline='Lavoro soprattutto per chi progetta: architetti, designer, falegnami, ceramisti. Le immagini restano '
                               'loro; io tengo i negativi.')]),
        t.one([elenco]),
    ], padding='large'))
    out.append(t.section([
        centered(t, [headline(t, 'Avete una casa finita <em>e nessuno l’ha ancora vista?</em>', size=60, tag='h2', align='center',
                              pad_bottom=16),
                     button(t, 'Raccontatemela', STUDIO + '#scrivimi', kind='pieno', align='center')]),
    ], bg=solid(CARTA), padding='large', style={'padding_top': 130, 'padding_bottom': 130}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  MATRIMONI
# ══════════════════════════════════════════════════════════════════════════════

GIORNATA = [
    ('11:20', 'I preparativi', 'Le mani, la luce della finestra, nessuno che guarda in macchina.', 'pexels-34543843.jpg'),
    ('15:00', 'Il sì', 'Sto in fondo, col teleobiettivo e senza flash. Il prete se ne accorge solo alla fine.', 'pexels-31965857.jpg'),
    ('18:40', 'Mezz’ora per voi due', 'Una strada di campagna, il sole basso: venti minuti, non di più.', 'pexels-14429585.jpg'),
    ('23:10', 'L’ultimo ballo', 'In bianco e nero, su pellicola: due rullini a matrimonio, sempre.', 'pexels-39988496.jpg'),
]


def build_matrimoni():
    t = Tpl(ID, 'matrimoni')
    out = []
    out.append(t.section([
        t.row([t.col([label(t, 'Matrimoni · dodici l’anno, da aprile a ottobre', color=INK),
                      headline(t, 'Pochi e lenti.', size=132, tag='h1', pad_bottom=18, spacing=-4),
                      text(t, '<p>Niente scaletta di foto di gruppo, niente «guardate qui». Ci sono tutto il giorno, guardo, aspetto: '
                              'dopo dieci minuti si dimenticano di me, ed è lì che si comincia.</p>', size=17, color=mix(INK, 82))],
                     wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_VELO, overlay=30, color=FONDO), padding='xlarge', style={'padding_top': 260, 'padding_bottom': 100}))

    come = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Ci conosciamo', 'description': 'Un caffè in studio o una telefonata lunga: chi siete, '
                                                                            'dove, chi ci sarà.'},
                  {'number': '02', 'title': 'Il sopralluogo', 'description': 'Vengo a vedere la chiesa, il comune, il posto della '
                                                                            'festa, all’ora giusta.'},
                  {'number': '03', 'title': 'Il giorno', 'description': 'Arrivo coi preparativi, me ne vado dopo l’ultimo ballo. '
                                                                       'Due fotocamere, una è a pellicola.'},
                  {'number': '04', 'title': 'Dopo', 'description': 'Venti foto in tre giorni, tutte le altre in sei settimane, '
                                                                   'l’album quando l’avete scelto.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': FUOCO, 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': INK, 'title_size': 24,
        'title_weight': '300', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 14, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO_INK},
        'card_radius': radius(0), 'card_padding': 22, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, '01 — Come funziona', ['Dal primo caffè', 'all’album.'], size=64,
                       tagline='Fotografo un solo matrimonio a settimana: il resto del tempo è per chi si è sposato prima di voi.')]),
        t.one([come]),
    ], padding='large', style={'padding_top': 130}))

    out.append(t.section([
        t.one([sechead(t, '02 — La giornata', ['Dalle undici', 'all’ultimo ballo.'], size=64,
                       tagline='Scendete: la giornata arriva di lato, una fotografia alla volta, come la stampa da un negativo.')]),
    ], padding='large', style={'padding_bottom': 40}))
    for i, (ora, titolo, frase, f) in enumerate(GIORNATA):
        out.append(pannello(t, f, '%s — %02d / 04' % (ora, i + 1), titolo + '.', frase, '', '', overlay=(54, 46, 40, 30)[i],
                            pos='center 40%'))

    def piano(nome, prezzo, cosa, extra, popolare=False):
        return t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': extra,
            'currency_size': '22', 'features': cosa, 'check_style': 'arrow', 'cta_text': 'Chiedi la data',
            'cta_url': STUDIO + '?servizio=matrimonio#scrivimi', 'cta_bg_color': INK if popolare else 'transparent',
            'cta_text_color': FONDO if popolare else INK, 'cta_radius': '0', 'cta_hover_bg_color': FUOCO,
            'cta_hover_text_color': INK, 'is_popular': popolare, 'badge_text': 'La più scelta', 'badge_bg_color': FUOCO,
            'badge_text_color': INK, 'bg_color': CHIARO if popolare else 'transparent', 'accent_color': FUOCO, 'text_color': INK,
            'border_radius': '0', 'shadow': 'none', 'additional_info': '',
        }, style={'margin_top': 0, 'margin_right': 0, 'margin_bottom': 0, 'margin_left': 0,
                  'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': FILO}})
    out.append(t.section([
        t.one([sechead(t, '03 — Quanto costa', ['Tre modi,', 'prezzi chiusi.'], size=64,
                       tagline='Tutto compreso entro cento chilometri da Torino: viaggio, assistente, provini stampati.')]),
        t.row([t.col([piano('Il rito', '1.600', 'Cerimonia e ritratti\nQuattro ore\n250 fotografie\nProvini stampati', 'fino a 4 ore')],
                     wm='1-3', ws='1-1'),
               t.col([piano('Tutta la giornata', '2.900', 'Dai preparativi all’ultimo ballo\nDue fotocamere, una a pellicola\n'
                                                         '600 fotografie\nVenti foto in tre giorni', 'fino a 11 ore', True)],
                     wm='1-3', ws='1-1'),
               t.col([piano('L’album', '480', 'Trenta pagine in carta cotone\nStampa fine art\nScatola in tela\nUna copia per i '
                                              'genitori a metà prezzo', 'in più')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=24, valign='stretch'),
    ], bg=solid(CARTA), padding='large', html_id='prezzi'))

    out.append(t.section([
        t.row([t.col([sechead(t, '04 — Le domande', ['Quelle che', 'mi fanno tutti.'], size=48, layout='stack', size_mobile=34)],
                     wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Scatti davvero in pellicola?', 'Sì: due rullini di bianco e nero a ogni matrimonio, di solito per la sera. Li '
                                                   'sviluppo io, in studio. Il resto è digitale.'),
                   ('Quanto dobbiamo aspettare le foto?', 'Venti foto scelte arrivano in tre giorni, per chi le vuole mandare ai '
                                                          'parenti. Tutte le altre in sei settimane.'),
                   ('Fai anche le foto di gruppo?', 'Una, con tutti, sui gradini della chiesa o del comune. Le altre le faccio '
                                                    'senza che ve ne accorgiate.'),
                   ('Vieni anche fuori dal Piemonte?', 'Sì, in tutta Italia: fuori dai cento chilometri aggiungo il viaggio e '
                                                       'una notte, al prezzo che pago.'),
                   ('Quanto prima dobbiamo prenotare?', 'Per i sabati di giugno e settembre, un anno. Per gli altri giorni bastano '
                                                       'spesso tre mesi.'),
               ], 'mat')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large'))

    out.append(t.section([
        t.row([t.col(scatto(t, 'pexels-12289213.jpg', 'Due mani intrecciate su un pavimento bianco',
                            'Silvia e Tommaso · Langhe · 1/250 · f/2.8', ratio='4/5'), wm='2-5', ws='1-1'),
               t.col([headline(t, 'Ditemi la data: <em>se è libera, è vostra.</em>', size=64, tag='h2', pad_bottom=18),
                      text(t, '<p>Tengo la data per due settimane, senza acconto, mentre ci pensate.</p>', size=17),
                      button(t, 'Chiedi la data', STUDIO + '?servizio=matrimonio#scrivimi', kind='pieno', pad_top=10)],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='42,58', gap=72, valign='center'),
    ], bg=solid(CARTA), padding='large', style={'padding_top': 130, 'padding_bottom': 130}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  EVENTI IN CITTÀ
# ══════════════════════════════════════════════════════════════════════════════

FESTE = [
    ('14.09', 'Notte bianca in Vanchiglia', 'Festa di quartiere · dalle 19 alle 2', 'reportage'),
    ('21.06', 'La festa della musica ai Murazzi', 'Sei palchi lungo il Po', 'concerti'),
    ('03.05', 'Una libreria che apre in Borgo Po', 'Inaugurazione · centoventi persone', 'inaugurazione'),
    ('12.04', 'Il mercato all’alba', 'Reportage per il Comune · dalle 5 alle 9', 'reportage'),
    ('08.03', 'Officina Ruà apre le porte', 'Laboratorio aperto e cena', 'aziende'),
    ('15.12', 'La cena di Natale di uno studio di architetti', 'Quaranta persone e un brindisi', 'aziende'),
]


def build_eventi():
    t = Tpl(ID, 'eventi')
    out = [intro(t, 'Eventi in città', ['Le feste di Torino,', 'da vicino.'],
                 '<p>Concerti in piazza, inaugurazioni, la notte bianca del quartiere, la cena di fine anno dell’ufficio. Arrivo '
                 'prima, me ne vado dopo, e la sera stessa avete venti foto per i social.</p>')]

    out.append(t.section([
        t.row([t.col([label(t, 'Venti foto la sera stessa', color=SOFT_CHIARO),
                      headline(t, 'La festa finisce, <em>le foto sono già arrivate.</em>', size=72, color=CHIARO, tag='h2', pad_bottom=16),
                      text(t, '<p>Scatto, scelgo e correggo durante la serata: prima di mezzanotte avete una cartella con venti '
                              'immagini pronte da pubblicare. Le altre, tutte, in quarantotto ore.</p>', size=16, color=SOFT_CHIARO)],
                     wm='3-5', ws='1-1'), t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', valign='end'),
    ], bg=video_bg(V_LUCI, overlay=48), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 90}))

    lista = t.classic('worklist', {
        'items': [{'number': d, 'title': n, 'category': c, 'year': k} for d, n, c, k in FESTE],
        'divider_color': FILO, 'row_padding_y': 22, 'row_hover_bg': mix(INK, 4), 'hover_indent': 16,
        'number_color': FUOCO, 'number_size': 12, 'title_font_family': 'heading', 'title_color': INK, 'title_size': 32,
        'title_weight': '300', 'show_category': True, 'category_color': TENUE, 'category_size': 14, 'show_year': True,
        'year_color': TENUE, 'year_size': 12, 'show_arrow': False, 'arrow_color': INK, 'mono_font_family': 'mono',
        'row_padding': {'top': 20, 'right': 6, 'bottom': 20, 'left': 6}, 'title_size_tablet': 26, 'title_size_mobile': 21,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.one([sechead(t, '01 — Quest’anno', ['Sei serate,', 'sei quartieri.'], size=64,
                       tagline='Le feste che ho fotografato nel 2026. Per alcune ero lì per il Comune, per altre per chi le organizzava.')]),
        t.one([lista]),
    ], padding='large'))

    galleria = t.classic('progallery', {
        'images': [{'url': img(f), 'alt': a, 'caption': '', 'subtitle': '', 'text': ''} for f, a in [
            ('pexels-15658307.jpg', 'Una strada piena di gente in festa, coi lampioni accesi'),
            ('pexels-8694375.jpg', 'La folla davanti a un palco all’aperto, di sera'),
            ('pexels-36372741.jpg', 'Un mercato sotto gli archi, all’alba'),
            ('pexels-18999568.jpg', 'Persone che ridono con un bicchiere in mano a un’inaugurazione'),
            ('pexels-29689915.jpg', 'Una via del centro con le luci appese per le feste'),
            ('pexels-12476804.jpg', 'Due uomini che contrattano al banco del mercato'),
            ('pexels-17801359.jpg', 'Una festa su una terrazza al tramonto'),
            ('pexels-2990835.jpg', 'Un concerto con le luci del palco e le mani alzate')]],
        'layout': 'expand', 'layout_family': 'classic', 'columns': 4, 'gap': 8, 'img_height': '360px',
        'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 0, 'mobile_columns': 2,
        'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
        'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
    })
    out.append(t.section([
        t.one([sechead(t, '02 — I provini', ['Otto foto,', 'otto serate.'], size=56, layout='stack')]),
        t.one([galleria]),
    ], bg=solid(CARTA), padding='large'))

    tariffe = t.classic('pricelist', {
        'items': [{'title': 'Un’ora', 'description': 'Un brindisi, un taglio del nastro, una premiazione.', 'price': '120 €',
                   'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Mezza serata · 4 ore', 'description': 'Un’inaugurazione intera, dall’arrivo degli ospiti ai saluti.',
                   'price': '380 €', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Tutta la sera · 8 ore', 'description': 'Una festa di quartiere o un concerto, con la folla e i '
                                                                    'dettagli.', 'price': '690 €', 'image_url': '',
                   'highlighted': False, 'badge': ''},
                  {'title': 'Venti foto prima di mezzanotte', 'description': 'Già corrette, pronte da pubblicare.',
                   'price': 'compreso', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Un video verticale di trenta secondi', 'description': 'Per le storie, montato la sera stessa.',
                   'price': '+ 150 €', 'image_url': '', 'highlighted': False, 'badge': ''}],
        'separator_style': 'solid', 'separator_color': FILO, 'title_color': INK, 'price_color': INK,
        'description_color': TENUE, 'image_size': 64, 'image_border_radius': '0', 'show_image': False,
        'price_position': 'right', 'tile_padding': {'top': 18, 'right': 0, 'bottom': 18, 'left': 0}, 'card_bg': 'transparent',
        'card_border_radius': '0',
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, '03 — Le tariffe', ['A ore,', 'senza sorprese.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>In tutta Torino e prima cintura il viaggio è compreso. Associazioni di quartiere e feste di '
                              'beneficenza: metà prezzo.</p>'),
                      button(t, 'Chiedi la serata', STUDIO + '?servizio=evento#scrivimi', kind='pieno')], wm='1-3', ws='1-1'),
               t.col([tariffe], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large', html_id='tariffe'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LO STUDIO
# ══════════════════════════════════════════════════════════════════════════════

def build_studio():
    t = Tpl(ID, 'studio')
    out = []
    out.append(t.section([
        t.row([t.col(scatto(t, 'pexels-18529574.jpg', 'Adele Rivoira, in bianco e nero, con la fotocamera davanti al viso',
                            'Autoritratto · Hasselblad 503 · 1/60 · f/5.6'), wm='2-5', ws='1-1'),
               t.col([sechead(t, 'Lo studio', ['Adele Rivoira,', 'fotografa.'], size=96, layout='stack', size_mobile=46),
                      text(t, '<p>Classe 1987, ha studiato architettura al Politecnico e non ha mai disegnato una casa: le '
                              'fotografa. Dal 2014 lavora per studi, aziende di arredo e qualche rivista, quasi sempre con una '
                              'luce sola.</p><p>Il sabato, a volte, sposa qualcuno. Con la fotocamera.</p>', size=17),
                      specs(t, [('Hasselblad 503 CW', 'pellicola 120, per i ritratti e i matrimoni'),
                                ('Fujifilm GFX 100', 'medio formato digitale, per tutto il resto'),
                                ('Linhof 4×5', 'a banco ottico, per l’architettura'),
                                ('Un flash', 'che resta quasi sempre spento')])],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='end'),
    ], padding='large', style={'padding_top': 180}))

    out.append(t.section([
        centered(t, [label(t, 'Via Mazzini 31 · secondo piano · finestra a nord', color=SOFT_CHIARO, align='center'),
                     headline(t, 'Una stanza, una finestra, <em>una tenda.</em>', size=76, color=CHIARO, tag='h2', align='center',
                              pad_bottom=16),
                     text(t, '<p>Lo studio è aperto dal martedì al sabato. Si può passare a vedere le stampe: c’è sempre il caffè.</p>',
                          size=16, color=SOFT_CHIARO, align='center')]),
    ], bg=video_bg(V_TENDA, overlay=46), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220}))

    modulo = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Cosa', 'name': 'servizio', 'param': 'servizio', 'placeholder': '', 'required': True,
             'width': '1-2', 'options': 'Architettura e interni\nOggetti e design\nRitratto\nMatrimonio\nEvento', 'icon': ''},
            {'field_type': 'date', 'label': 'Quando', 'name': 'data', 'placeholder': '', 'required': False, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'text', 'label': 'Dove', 'name': 'luogo', 'placeholder': 'Una via, un paese, una chiesa', 'required': False,
             'width': '1-1', 'options': '', 'icon': ''},
            {'field_type': 'textarea', 'label': 'Due righe', 'name': 'messaggio', 'placeholder': 'Cosa avete in mente?',
             'required': True, 'width': '1-1', 'options': '', 'icon': ''},
        ],
        'email_subject': 'Lunga Posa — una richiesta', 'submit_text': 'Manda', 'submit_icon': '',
        'success_message': 'Arrivato. Vi rispondo entro sera, anche il sabato.',
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': INK, 'label_size': '11', 'label_weight': '400',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': 'transparent', 'input_color': INK,
        'input_border_color': FILO_INK, 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 0,
        'input_focus_border': FUOCO, 'input_focus_shadow': False, 'gap': '20', 'submit_bg': INK, 'submit_color': FONDO,
        'submit_radius': 0, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '11',
        'submit_font_weight': '500', 'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2',
        'submit_hover_bg': FUOCO, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Scrivimi', ['Cosa, dove,', 'quando.'], size=72, layout='stack', size_mobile=42),
                      text(t, '<p>Rispondo entro sera. Per i matrimoni ditemi la data: se è libera la tengo per voi due settimane.</p>'),
                      contacts(t)], wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(CARTA), padding='large', html_id='scrivimi', style={'padding_top': 130, 'padding_bottom': 130}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '15', 'height': '460', 'marker': True,
                                 'marker_popup': 'Lunga Posa · via Mazzini 31', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'positron', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Dove', ['A due passi', 'da Porta Nuova.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Fra San Salvario e il Po, in un palazzo del 1890 col cortile. Citofono «Lunga Posa», secondo '
                              'piano senza ascensore: le stampe le portiamo giù noi.</p>', size=16),
                      specs(t, [('Studio', 'martedì — sabato, 10 — 18'), ('Sopralluoghi', 'anche all’alba, su appuntamento'),
                                ('Risposte', 'entro sera')])], wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], padding='large', html_id='dove'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404 · 1/8 · f/1.4 · mosso', color=INK, align='center'),
                     headline(t, 'Fuori fuoco.', size=150, tag='h1', align='center', pad_bottom=20, spacing=-5),
                     text(t, '<p>Questa pagina non è venuta. Capita anche alle foto: si ricomincia dalla prima.</p>', size=17,
                          align='center', color=mix(INK, 80)),
                     button(t, 'Torna alla home', HOME, kind='pieno', align='center')]),
    ], bg=video_bg(V_FOGLIE, overlay=24, color=FONDO), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Creativi e portfolio', 'zone': 'Il fuoco lo decidi tu',
    'tags': ['fotografia', 'architettura', 'still life', 'matrimoni', 'eventi', 'minimale', 'torino'],
    'description': 'Lo studio di una fotografa di architettura e oggetti a Torino, che fa anche dodici matrimoni l’anno e le feste '
                   'della città: carta grigia e grafite con l’arancio del fuoco, il mirino sopra il video d’apertura (il fuoco '
                   'segue il mouse, il clic scatta e fa un provino), le sezioni che si svelano di lato, le serie che scorrono, i '
                   'listini e il modulo per scrivere.',
    'menu': {'name': 'Lunga Posa — menu', 'items': [
        {'title': 'I lavori', 'url': LAVORI}, {'title': 'Matrimoni', 'url': MATRIMONI}, {'title': 'Eventi', 'url': EVENTI},
        {'title': 'Lo studio', 'url': STUDIO}, {'title': 'Scrivimi', 'url': STUDIO + '#scrivimi'}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Lunga Posa — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Lunga Posa — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Lunga Posa — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Lunga Posa — Home'},
        'lavori': {'file': 'lavori.json', 'type': 'page', 'title': 'Lunga Posa — I lavori'},
        'matrimoni': {'file': 'matrimoni.json', 'type': 'page', 'title': 'Lunga Posa — Matrimoni'},
        'eventi': {'file': 'eventi.json', 'type': 'page', 'title': 'Lunga Posa — Eventi in città'},
        'studio': {'file': 'studio.json', 'type': 'page', 'title': 'Lunga Posa — Lo studio'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'lavori': {'title': 'I lavori', 'template': 'lavori', 'slug': 'lavori'},
        'matrimoni': {'title': 'Matrimoni', 'template': 'matrimoni', 'slug': 'matrimoni'},
        'eventi': {'title': 'Eventi in città', 'template': 'eventi', 'slug': 'eventi'},
        'studio': {'title': 'Lo studio', 'template': 'studio', 'slug': 'studio'},
    },
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Funnel Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Funnel Display\', \'Helvetica Neue\', Arial, sans-serif',
            'font_family_mono': '\'Fragment Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '300',
            'heading_line_height': '1.02', 'heading_letter_spacing': '-0.02em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '5.5rem', 'font_size_h2': '3.6rem', 'font_size_h3': '2rem',
            'font_size_h4': '1.4rem', 'font_size_h5': '1.1rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.8rem',
            'font_size_h2_mobile': '2.1rem', 'font_size_h3_mobile': '1.5rem',
        },
        'google_fonts': ['Funnel Display', 'Funnel Sans', 'Fragment Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': [],
        'buttons': {'font_size': '11px', 'font_weight': '500', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '2px', 'hover_brightness': '100'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': 'transparent', 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '0px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '400', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 3, 'scale': 160},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'lavori.json': build_lavori(), 'matrimoni.json': build_matrimoni(), 'eventi.json': build_eventi(),
              'studio.json': build_studio(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
