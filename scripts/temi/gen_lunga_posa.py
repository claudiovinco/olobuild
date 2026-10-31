# -*- coding: utf-8 -*-
"""Genera il tema «Lunga Posa» (id lunga-posa): lo studio di una fotografa di architettura e oggetti a Torino, che fa
anche matrimoni (pochi) e le feste della città.

Minimale come i portfolio premiati del 2026 (Almira Kho, Brandon Yasin): carta grigia e grafite, un solo colore di
segnale, un rosa lampone (dal 28 ottobre 2026; prima l'arancio del fuoco). Titoli nel Funnel Display sottile, testo in Funnel Sans, i dati di
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
    'primary': '#E3457C', 'primary_contrast': '#141414', 'secondary': '#8A8F86', 'secondary_contrast': '#141414',
    'accent': '#E3457C', 'dark': '#121212', 'light': '#F7F6F2', 'muted': '#E3E0D9', 'muted_contrast': '#161616',
    'text': '#161616', 'text_muted': '#64625C', 'background': '#EEECE7', 'border': '#D3D0C8', 'link': '#161616',
}

INK = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
FUOCO = 'var(--olo-color-primary)'
# Il primario è un rosa lampone (#E3457C, dal 28 ottobre 2026). Per i testi piccoli (hover, numeri, dati di scatto) il rosa
# scurito con la grafite regge 4,5:1 sulla carta e sulle fasce grigie; per gli accenti dei titoli grandi e le icone un rosa
# appena più scuro regge 3:1 anche sulle fasce grigie (il pieno lì sta a 2,95). Il pieno resta per fondi, punti e linee,
# e sopra il rosa si scrive in NERO.
FUOCO_TESTO = 'color-mix(in srgb, var(--olo-color-primary) 70%, var(--olo-color-text))'
FUOCO_TITOLI = 'color-mix(in srgb, var(--olo-color-primary) 90%, var(--olo-color-text))'
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
AZIENDE = '{{PAGINA:aziende}}'
RITRATTI = '{{PAGINA:ritratti}}'
FAMIGLIA = '{{PAGINA:famiglia}}'
BOUDOIR = '{{PAGINA:boudoir}}'

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
    # i servizi del 28 ottobre: aziende, ritratti, famiglia, boudoir, cerimonie e provini dei matrimoni
    'pexels-7061335.jpg',
    'pexels-8143672.jpg',
    'pexels-8146158.jpg',
    'pexels-7303782.jpg',
    'pexels-7303775.jpg',
    'pexels-6962847.jpg',
    'pexels-35747340.jpg',
    'pexels-36603625.jpg',
    'pexels-36486477.jpg',
    'pexels-39836703.jpg',
    'pexels-33259929.jpg',
    'pexels-39836722.jpg',
    'pexels-37098141.jpg',
    'pexels-12181160.jpg',
    'pexels-29419658.jpg',
    'pexels-17384783.jpg',
    'pexels-19216762.jpg',
    'pexels-20856131.jpg',
    'pexels-7652128.jpg',
    'pexels-3860617.jpg',
    'pexels-7652130.jpg',
    'pexels-8297155.jpg',
    'pexels-36499768.jpg',
    'pexels-8124401.jpg',
    'pexels-30362729.jpg',
    'pexels-6039107.jpg',
    'pexels-25470069.jpg',
    'pexels-7244030.jpg',
    'pexels-15523449.jpg',
    'pexels-15929707.jpg',
    'pexels-7910793.jpg',
    'pexels-7911306.jpg',
    'pexels-35663243.jpg',
    'pexels-34069062.jpg',
    'pexels-23875387.jpg',
    'pexels-8252016.jpg',
    'pexels-30010820.jpg',
    'pexels-30010818.jpg',
    'pexels-8841432.jpg',
    'pexels-5638732.jpg',
    'pexels-5425036.jpg',
    'pexels-9267508.jpg',
    'pexels-12594181.jpg',
    'pexels-40446142.jpg',
    'pexels-38604997.jpg',
    'pexels-4609048.jpg',
    'pexels-8120950.jpg',
    'pexels-13965216.jpg',
    'pexels-10283404.jpg',
    'pexels-34651408.jpg',
    'pexels-10600032.jpg',
    'pexels-3455996.jpg',
    'pexels-12613919.jpg',
    'pexels-29002731.jpg',
    'pexels-34384314.jpg',
    'pexels-28588970.jpg',
    'pexels-1372177.jpg',
    'pexels-6860127.jpg',
    'pexels-15148504.jpg',
    'pexels-9256869.jpg',
    'pexels-29977336.jpg',
    'pexels-37512797.jpg',
    'pexels-6487425.jpg',
    'pexels-9242466.jpg',
    'pexels-27366104.jpg',
    'pexels-18821003.jpg',
    'pexels-17166034.jpg',
    'pexels-40436533.jpg',
    'pexels-14468108.jpg',
    'pexels-14175330.jpg',
    'pexels-4534869.jpg',
    'pexels-27255537.jpg',
    'pexels-8284464.jpg',
    'pexels-31951839.jpg',
    'pexels-12793812.jpg',
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
        o.update({'accent_text': m.group(1), 'accent_italic': False, 'accent_color': FUOCO_TITOLI})
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
        o = {'bg_color': INK, 'text_color': FONDO, 'hover_bg_color': FUOCO, 'hover_text_color': NERO}
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
        'icon_color': FUOCO_TITOLI, 'icon_size': '16', 'text_color': color, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': FUOCO_TESTO, 'header_font_size': 22, 'header_font_weight': '300',
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
    # Il velo non scende sotto il 60%: con meno, sulle foto chiare (finestre, muri bianchi) le scritte restavano fra 2,8 e
    # 4,4:1, misurate sui pixel il 28 ottobre 2026.
    ], bg=photo_bg(foto, overlay=max(overlay, 60), position=pos), padding='large', width='large', sticky_effect=effetto,
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
        'nav_bg': 'transparent', 'text_color': INK, 'hover_color': FUOCO_TESTO, 'active_color': FUOCO_TESTO,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '34',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': FUOCO,
        'button_mode': 'last', 'btn_bg': INK, 'btn_color': FONDO, 'btn_hover_bg': FUOCO, 'btn_hover_color': NERO,
        'btn_radius': SQ, 'btn_padding': {'top': 12, 'right': 20, 'bottom': 12, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '500', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': FONDO, 'mobile_text_color': INK, 'mobile_accent_color': FUOCO,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '40', 'mobile_link_size': '40',
        'mobile_link_font': 'heading', 'mobile_link_weight': '300',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(INK, 12)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
        # La tendina dei Servizi: carta chiara e angoli vivi, come il resto del tema.
        'panel_bg': CHIARO, 'panel_radius': SQ, 'panel_shadow': 'sm',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'LUNGA POSA', size=150, color=INK, tag='p', pad_bottom=0, weight='300', spacing=-4)
    claim = text(t, '<p>Adele Rivoira, fotografa. Architettura, interni e oggetti per chi progetta; hotel e aziende; dodici '
                    'matrimoni l’anno, ritratti e famiglie; le feste della città quando chiamano.</p>', size=15, color=TENUE,
                 pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': INK, 'link_hover_color': FUOCO_TESTO, 'active_color': FUOCO_TESTO,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    servizi = nav([('Per le aziende', AZIENDE), ('Matrimoni e cerimonie', MATRIMONI), ('Ritratti', RITRATTI),
                   ('Famiglia', FAMIGLIA), ('Boudoir', BOUDOIR), ('Eventi in città', EVENTI)])
    pagine = nav([('I lavori', LAVORI), ('Lo studio', STUDIO), ('Scrivimi', STUDIO + '#scrivimi'), ('Privacy', '/privacy-policy/')])
    borsa = text(t, '<p>Hasselblad 503 CW · rullino 120<br>Fujifilm GFX 100 · medio formato<br>Linhof 4×5 · per l’architettura</p>'
                    '<p>Studio aperto martedì — sabato, 10 — 18.</p>', size=15, color=INK)
    legal = text(t, '<p>© 2026 Lunga Posa · Adele Rivoira fotografa · P. IVA 00000000000 · Le stampe fine art sono firmate e '
                    'numerate.</p>', size=12, color=TENUE)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'linkedin', 'url': 'https://linkedin.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': TENUE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': INK, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim, contacts(t)], wm='2-5', ws='1-1'),
        t.col([label(t, 'I servizi'), servizi], wm='1-5', ws='1-2'),
        t.col([label(t, 'Le pagine'), pagine], wm='1-5', ws='1-2'),
        t.col([label(t, 'In borsa'), borsa], wm='1-5', ws='1-1'),
    ], layout='custom', custom='34,22,18,26', gap=40, valign='start', anim=None)
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
        'preset': 'custom', 'eyebrow_text': 'Adele Rivoira · fotografa · Torino', 'eyebrow_dot': True, 'eyebrow_color': INK,
        'title': 'Guardo a lungo.<br>Scatto una volta.',
        'subtitle': 'Architettura, interni e oggetti per chi progetta, hotel e aziende. E con lo stesso sguardo lento matrimoni, '
                    'ritratti e famiglie. Muovete il mouse: il fuoco lo decidete voi.',
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
    out.append(pannello(t, 'pexels-30705517.jpg', '03 / 03 — Le persone',
                        'Matrimoni, ritratti, famiglie.',
                        'Senza pose e senza scalette: guardo, aspetto, scatto. Dalle Langhe ai cortili di Torino, dal primo giorno '
                        'di un figlio all’ultimo ballo di un matrimonio.',
                        'I ritratti', RITRATTI, overlay=34, html_id='persone'))

    scelti = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': c, 'desc': c, 'number': '%02d' % (i + 1), 'image': img(f),
                   'link_url': (LAVORI + a) if a else (MATRIMONI if 'Matrimonio' in c else EVENTI), 'color': ''}
                  for i, (n, c, f, a) in enumerate(LAVORI_SCELTI)],
        'lead_mode': 'number', 'number_color': TENUE, 'number_hover_color': FUOCO_TESTO,
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
    ], bg=video_bg(V_BICCHIERE, overlay=0, color=FONDO), padding='xlarge', html_id='luce',
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
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': FUOCO_TESTO, 'number_bg': '',
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

    servizi = t.classic('worklist', {
        'items': [{'number': n, 'title': a, 'category': b, 'year': c, 'link_url': u} for n, a, b, c, u in [
            ('01', 'Per le aziende', 'Hotel, studi tecnici, agenzie immobiliari, comunicazione', 'da 190 €', AZIENDE),
            ('02', 'Matrimoni e cerimonie', 'Dodici matrimoni l’anno, battesimi, comunioni, anniversari', 'da 380 €', MATRIMONI),
            ('03', 'Ritratti', 'Da soli, in coppia, in attesa, book, beauty e glamour', 'da 180 €', RITRATTI),
            ('04', 'Famiglia', 'Newborn, il primo anno, tutti insieme', 'da 220 €', FAMIGLIA),
            ('05', 'Boudoir', 'Ritratti intimi e glamour, con la massima riservatezza', 'da 380 €', BOUDOIR),
            ('06', 'Eventi in città', 'Feste, concerti, inaugurazioni', 'da 120 €', EVENTI)]],
        'divider_color': FILO, 'row_padding_y': 22, 'row_hover_bg': mix(INK, 4), 'hover_indent': 16,
        'number_color': FUOCO_TESTO, 'number_size': 12, 'title_font_family': 'heading', 'title_color': INK, 'title_size': 36,
        'title_weight': '300', 'show_category': True, 'category_color': TENUE, 'category_size': 14, 'show_year': True,
        'year_color': INK, 'year_size': 12, 'show_arrow': True, 'arrow_color': FUOCO, 'mono_font_family': 'mono',
        'row_padding': {'top': 20, 'right': 6, 'bottom': 20, 'left': 6}, 'title_size_tablet': 28, 'title_size_mobile': 22,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.one([sechead(t, '06 — I servizi', ['Sei modi', 'di farvi fotografare.'], size=64,
                       tagline='Ogni servizio ha la sua pagina, coi prezzi chiusi e le domande che mi fanno tutti.')]),
        t.one([servizi]),
    ], padding='large', html_id='servizi'))

    voce = t.classic('testimonial', {
        'quote': 'Adele è l’unica che aspetta. Ci ha chiesto di tornare alle sette di sera, con la casa vuota: <em>aveva '
                 'ragione, e la rivista ha messo quella foto in copertina.</em>',
        'author_name': 'Giulia Ferro', 'author_role': 'Ferro Vallino Architetti, Torino',
        'rating': '5', 'text_color': INK, 'star_color': FUOCO, 'author_color': TENUE, 'quote_accent_color': FUOCO_TITOLI,
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
                          label(t, s['exif'], color=FUOCO_TESTO, pad_top=6, pad_bottom=0)], wm='1-3', ws='1-1'),
                   t.col([galleria], wm='2-3', ws='1-1')], layout='custom', custom='30,70', gap=56, valign='center'),
        ], bg=solid(CARTA) if i % 2 else None, padding='large', html_id=s['id']))

    elenco = t.classic('worklist', {
        'items': [{'number': a, 'title': c, 'category': cosa, 'year': tipo} for a, c, cosa, tipo in CLIENTI],
        'divider_color': FILO, 'row_padding_y': 22, 'row_hover_bg': mix(INK, 4), 'hover_indent': 16,
        'number_color': TENUE, 'number_size': 12, 'title_font_family': 'heading', 'title_color': INK, 'title_size': 32,
        'title_weight': '300', 'show_category': True, 'category_color': TENUE, 'category_size': 14, 'show_year': True,
        'year_color': FUOCO_TESTO, 'year_size': 12, 'show_arrow': False, 'arrow_color': INK, 'mono_font_family': 'mono',
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
    ], bg=video_bg(V_VELO, overlay=0, color=FONDO), padding='xlarge', style={'padding_top': 260, 'padding_bottom': 100}))

    come = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Ci conosciamo', 'description': 'Un caffè in studio o una telefonata lunga: chi siete, '
                                                                            'dove, chi ci sarà.'},
                  {'number': '02', 'title': 'Il sopralluogo', 'description': 'Vengo a vedere la chiesa, il comune, il posto della '
                                                                            'festa, all’ora giusta.'},
                  {'number': '03', 'title': 'Il giorno', 'description': 'Arrivo coi preparativi, me ne vado dopo l’ultimo ballo. '
                                                                       'Due fotocamere, una è a pellicola.'},
                  {'number': '04', 'title': 'Dopo', 'description': 'Venti foto in tre giorni, tutte le altre in sei settimane, '
                                                                   'l’album quando l’avete scelto.'}],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': FUOCO_TESTO, 'number_bg': '',
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

    out.append(t.section([
        t.one([sechead(t, '03 — Dai matrimoni', ['Sei matrimoni,', 'sei luci diverse.'], size=56, layout='stack',
                       tagline='Quasi tutte in bianco e nero, come le vedo mentre succedono.')]),
        t.one([galleria(t, [('pexels-3455996.jpg', 'Il padre della sposa che le accarezza il braccio, commosso'),
                            ('pexels-29002731.jpg', 'La sposa col bouquet in piedi in una stanza, la porta aperta'),
                            ('pexels-12613919.jpg', 'Un abbraccio felice fra gli invitati, in bianco e nero'),
                            ('pexels-34384314.jpg', 'Il viso di una sposa che si prepara, gli occhi bassi'),
                            ('pexels-28588970.jpg', 'Una testimone che legge il discorso, la sposa di spalle'),
                            ('pexels-1372177.jpg', 'Gli sposi abbracciati in mezzo a una strada piena di gente')], cols=3,
                        altezza='440px')]),
    ], padding='large', html_id='provini'))

    out.append(t.section([
        t.row([t.col([sechead(t, '04 — Cerimonie in famiglia', ['Battesimi, comunioni,', 'anniversari.'], size=56,
                              layout='stack', size_mobile=36),
                      text(t, '<p>Le stesse regole dei matrimoni, in poche ore: il rito senza flash, i nonni, il pranzo. Per i '
                              'cinquant’anni di matrimonio porto anche un fondale e faccio un ritratto a ogni coppia di '
                              'figli.</p>', size=16),
                      listino(t, [('Il rito', 'Due ore, cento immagini', '380 €'),
                                  ('Il rito e il pranzo', 'Cinque ore, trecento immagini', '690 €'),
                                  ('Un ritratto per famiglia', 'Al pranzo, col fondale', '+ 120 €')]),
                      button(t, 'Chiedi la data', chiedi('Cerimonia in famiglia'), kind='pieno', pad_top=22)], wm='2-5', ws='1-1'),
               t.col([galleria(t, [('pexels-35663243.jpg', 'Una mamma sorridente col bambino in braccio al battesimo'),
                                   ('pexels-34069062.jpg', 'Il sacerdote che versa l’acqua sulla testa del bambino'),
                                   ('pexels-8252016.jpg', 'Una bambina con la mamma il giorno della prima comunione'),
                                   ('pexels-30010818.jpg', 'Il cero e i fiori bianchi della comunione'),
                                   ('pexels-7910793.jpg', 'Due anziani che si baciano con un mazzo di tulipani'),
                                   ('pexels-8841432.jpg', 'Una famiglia a pranzo all’aperto, la tavola apparecchiata')],
                                cols=3, altezza='300px', mobile=2)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='start'),
    ], bg=solid(CARTA), padding='large', html_id='cerimonie'))

    def piano(nome, prezzo, cosa, extra, popolare=False):
        return t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': extra,
            'currency_size': '22', 'features': cosa, 'check_style': 'arrow', 'cta_text': 'Chiedi la data',
            'cta_url': chiedi('Matrimonio'), 'cta_bg_color': INK if popolare else 'transparent',
            'cta_text_color': FONDO if popolare else INK, 'cta_radius': '0', 'cta_hover_bg_color': FUOCO,
            'cta_hover_text_color': NERO, 'is_popular': popolare, 'badge_text': 'La più scelta', 'badge_bg_color': FUOCO,
            'badge_text_color': NERO, 'bg_color': CHIARO if popolare else 'transparent', 'accent_color': FUOCO, 'text_color': INK,
            'border_radius': '0', 'shadow': 'none', 'additional_info': '',
        }, style={'margin_top': 0, 'margin_right': 0, 'margin_bottom': 0, 'margin_left': 0,
                  'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': FILO}})
    out.append(t.section([
        t.one([sechead(t, '05 — Quanto costa', ['Tre modi,', 'prezzi chiusi.'], size=64,
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
        t.row([t.col([sechead(t, '06 — Le domande', ['Quelle che', 'mi fanno tutti.'], size=48, layout='stack', size_mobile=34)],
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
                      button(t, 'Chiedi la data', chiedi('Matrimonio'), kind='pieno', pad_top=10)],
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
                      headline(t, 'La festa finisce, le foto sono già arrivate.', size=72, color=CHIARO, tag='h2', pad_bottom=16),
                      text(t, '<p>Scatto, scelgo e correggo durante la serata: prima di mezzanotte avete una cartella con venti '
                              'immagini pronte da pubblicare. Le altre, tutte, in quarantotto ore.</p>', size=16, color=SOFT_CHIARO)],
                     wm='3-5', ws='1-1'), t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', valign='end'),
    ], bg=video_bg(V_LUCI, overlay=62), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 90}))

    lista = t.classic('worklist', {
        'items': [{'number': d, 'title': n, 'category': c, 'year': k} for d, n, c, k in FESTE],
        'divider_color': FILO, 'row_padding_y': 22, 'row_hover_bg': mix(INK, 4), 'hover_indent': 16,
        'number_color': FUOCO_TESTO, 'number_size': 12, 'title_font_family': 'heading', 'title_color': INK, 'title_size': 32,
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
                      button(t, 'Chiedi la serata', chiedi('Evento'), kind='pieno')], wm='1-3', ws='1-1'),
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
                     headline(t, 'Una stanza, una finestra, una tenda.', size=76, color=CHIARO, tag='h2', align='center',
                              pad_bottom=16),
                     text(t, '<p>Lo studio è aperto dal martedì al sabato. Si può passare a vedere le stampe: c’è sempre il caffè.</p>',
                          size=16, color=SOFT_CHIARO, align='center')]),
    ], bg=video_bg(V_TENDA, overlay=64), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220}))

    modulo = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Cosa', 'name': 'servizio', 'param': 'servizio', 'placeholder': '', 'required': True,
             'width': '1-2', 'options': 'Architettura e interni\nHotel e immobili\nComunicazione aziendale\nOggetti e design\n'
                                        'Ritratto\nFamiglia\nBoudoir\nMatrimonio\nCerimonia in famiglia\nEvento', 'icon': ''},
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
        'submit_hover_bg': FUOCO_TESTO, 'privacy_checkbox': True,
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
#  PEZZI DEI SERVIZI (aziende, ritratti, famiglia, boudoir)
# ══════════════════════════════════════════════════════════════════════════════

def chiedi(servizio):
    """Il link al modulo con «Cosa» già scelto (il campo legge ?servizio= dall'indirizzo)."""
    return STUDIO + '?servizio=' + servizio.replace(' ', '%20') + '#scrivimi'


def galleria(t, foto, cols=4, altezza='420px', mobile=2):
    return t.classic('progallery', {
        'images': [{'url': img(f), 'alt': a, 'caption': '', 'subtitle': '', 'text': ''} for f, a in foto],
        'layout': 'expand', 'layout_family': 'classic', 'columns': cols, 'gap': 8, 'img_height': altezza,
        'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 0, 'mobile_columns': mobile,
        'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
        'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
    })


def listino(t, voci):
    return t.classic('pricelist', {
        'items': [{'title': a, 'description': b, 'price': c, 'image_url': '', 'highlighted': False, 'badge': ''} for a, b, c in voci],
        'separator_style': 'solid', 'separator_color': FILO, 'title_color': INK, 'price_color': INK,
        'description_color': TENUE, 'image_size': 64, 'image_border_radius': '0', 'show_image': False,
        'price_position': 'right', 'tile_padding': {'top': 18, 'right': 0, 'bottom': 18, 'left': 0}, 'card_bg': 'transparent',
        'card_border_radius': '0',
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})


def passi(t, voci):
    return t.classic('process-steps', {
        'items': [{'number': '%02d' % (i + 1), 'title': a, 'description': b} for i, (a, b) in enumerate(voci)],
        'columns': 4, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': FUOCO_TESTO, 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': INK, 'title_size': 24,
        'title_weight': '300', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 14, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO_INK},
        'card_radius': radius(0), 'card_padding': 22, 'columns_tablet': 2, 'columns_mobile': 1,
    })


def servizio(t, eyebrow, titolo, testo, dettagli, foto, alt, exif, giro=False, html_id=None, carta=False, link=None):
    """Un servizio: il testo coi dettagli (durata, cosa si riceve, da quanto) e la foto col suo cartellino."""
    parole = [label(t, eyebrow), headline(t, titolo, size=56, tag='h2', pad_bottom=14),
              text(t, '<p>' + testo + '</p>', size=16), specs(t, dettagli)]
    if link:
        parole.append(button(t, link[0], link[1], pad_top=18))
    a = t.col(parole, wm='1-2', ws='1-1')
    b = t.col(scatto(t, foto, alt, exif, ratio='4/5'), wm='1-2', ws='1-1')
    return t.section([t.row([b, a] if giro else [a, b], layout='custom', custom='46,54' if giro else '54,46', gap=72,
                            valign='center')], bg=solid(CARTA) if carta else None, padding='large', html_id=html_id)


def domande(t, eyebrow, righe, coppie, prefisso):
    return t.section([
        t.row([t.col([sechead(t, eyebrow, righe, size=48, layout='stack', size_mobile=34)], wm='1-3', ws='1-1'),
               t.col([faq(t, coppie, prefisso)], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large')


def invito(t, titolo, testo, etichetta, url):
    return t.section([
        centered(t, [headline(t, titolo, size=60, tag='h2', align='center', pad_bottom=16),
                     text(t, '<p>' + testo + '</p>', size=17, align='center'),
                     button(t, etichetta, url, kind='pieno', align='center')]),
    ], bg=solid(CARTA), padding='large', style={'padding_top': 130, 'padding_bottom': 130})


def fascia_scura(t, foto, eyebrow, titolo, testo, overlay=60, pos='center center'):
    """Una fascia a tutta larghezza con la foto velata e le scritte chiare al centro. Mai in apertura: la testata
    trasparente ha le scritte grafite e le pagine si aprono sulla carta."""
    return t.section([
        centered(t, [label(t, eyebrow, color=SOFT_CHIARO, align='center'),
                     headline(t, titolo, size=76, color=CHIARO, tag='h2', align='center', pad_bottom=16),
                     text(t, '<p>' + testo + '</p>', size=16, color=SOFT_CHIARO, align='center')]),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})


# ══════════════════════════════════════════════════════════════════════════════
#  PER LE AZIENDE
# ══════════════════════════════════════════════════════════════════════════════

def build_aziende():
    t = Tpl(ID, 'aziende')
    out = [intro(t, 'Per le aziende', ['Spazi da vendere,', 'spazi da abitare.'],
                 '<p>Hotel, studi di architettura e d’ingegneria, agenzie immobiliari, aziende che vogliono farsi vedere come sono. '
                 'Fotografo i loro spazi come per una rivista: all’ora giusta, con la luce che c’è, e consegno immagini che si '
                 'usano ovunque, dal sito ai portali alla stampa.</p>')]
    out.append(pannello(t, 'pexels-39836703.jpg', '01 / 04 — Hotel', 'Le camere come le vedrà chi arriva.',
                        'Camere, hall, colazione, il giardino: una giornata dal mattino alla sera, per il sito, i portali di '
                        'prenotazione e le riviste di viaggio.', 'Per gli hotel', '#hotel', overlay=48))
    out.append(pannello(t, 'pexels-17384783.jpg', '02 / 04 — Studi di architettura e ingegneria',
                        'I progetti finiti, prima che li abitino.',
                        'Per i concorsi, le pubblicazioni e il portfolio dello studio: la luce di tutto il giorno, i dettagli '
                        'costruttivi, le strutture. E i cantieri, una visita al mese.', 'Per gli studi', '#studi', overlay=44))
    out.append(pannello(t, 'pexels-8143672.jpg', '03 / 04 — Agenzie immobiliari', 'Una casa in vendita, dodici foto giuste.',
                        'Un’ora di sopralluogo e scatti, le foto pronte in quarantotto ore. Linee dritte, stanze mai deformate, '
                        'i colori com’erano.', 'Per le agenzie', '#immobili', overlay=50))
    out.append(pannello(t, 'pexels-3860617.jpg', '04 / 04 — Comunicazione aziendale', 'Le persone e i luoghi di un’azienda.',
                        'I ritratti del gruppo, il laboratorio, l’ufficio, il lavoro mentre si fa: per il sito, LinkedIn, la '
                        'stampa e il bilancio di fine anno.', 'Per le aziende', '#comunicazione', overlay=66))

    out.append(servizio(t, '01 — Hotel', 'Una giornata, <em>tutte le luci</em>.',
                        'Arrivo per la colazione e me ne vado con l’ultima luce sulla facciata. Le camere le fotografo rifatte e '
                        'col sole che entra, la hall quando è vuota e quando si riempie.',
                        [('Una giornata', 'dalla colazione alla luce della sera'), ('Consegna', 'quaranta immagini in cinque giorni'),
                         ('Formati', 'orizzontali per il sito, verticali per i social, quadrate per i portali'), ('Da', '850 € la giornata')],
                        'pexels-35747340.jpg', 'La hall di un hotel, bianca e vuota, col banco della reception',
                        'Hotel Barolo Alto · GFX 100 · 1/30 · f/8', html_id='hotel'))
    out.append(servizio(t, '02 — Studi di architettura e ingegneria', 'Linee dritte, <em>sempre</em>.',
                        'Lavoro col banco ottico, come si è sempre fatto per l’architettura: le verticali restano verticali. Per '
                        'gli ingegneri fotografo anche le strutture, i nodi, i cantieri dalle fondazioni al collaudo.',
                        [('Per', 'concorsi, pubblicazioni, portfolio'), ('Cantieri', 'una visita al mese, finché serve'),
                         ('Strumento', 'banco ottico 4×5 e medio formato'), ('Da', '520 € la mezza giornata')],
                        'pexels-12181160.jpg', 'Il pilone bianco di un ponte strallato contro il cielo chiaro',
                        'Ponte sul Sangone · Linhof 4×5 · 1/60 · f/22', giro=True, html_id='studi', carta=True))
    out.append(servizio(t, '03 — Agenzie immobiliari', 'Un’ora, <em>dodici fotografie</em>.',
                        'Le case si vendono dalla prima foto. Arrivo con la luce migliore della giornata, apro le finestre, tolgo '
                        'quello che distrae: non aggiungo cieli finti e non allargo le stanze.',
                        [('In un’ora', 'sopralluogo e scatti'), ('Consegna', 'dodici immagini in quarantotto ore'),
                         ('Compresi', 'la planimetria fotografata e una foto della via'), ('Da', '190 € a immobile')],
                        'pexels-8146158.jpg', 'Un appartamento vuoto con le pareti bianche e il parquet, la luce dalle finestre',
                        'Via Nizza · GFX 100 · 1/15 · f/9', html_id='immobili'))
    out.append(servizio(t, '04 — Comunicazione aziendale', 'Chi lavora, <em>dove lavora</em>.',
                        'Una giornata in azienda: i ritratti alla finestra, il reparto, le mani sul lavoro. Niente strette di mano '
                        'finte: chiedo solo cinque minuti a testa e poi aspetto.',
                        [('Ritratti', 'del gruppo e di ognuno, nello stesso stile'), ('Reportage', 'una giornata di lavoro vera'),
                         ('Uso', 'sito, LinkedIn, stampa, bilancio'), ('Da', '640 € la giornata')],
                        'pexels-36499768.jpg', 'Una donna sorridente seduta in poltrona vicino alla finestra dell’ufficio',
                        'Officina Ruà · Hasselblad 503 · 1/125 · f/4', giro=True, html_id='comunicazione', carta=True))

    out.append(t.section([
        t.one([sechead(t, '05 — Dai lavori recenti', ['Camere, strutture,', 'persone al lavoro.'], size=56, layout='stack')]),
        t.one([galleria(t, [('pexels-39836722.jpg', 'Il letto di una camera d’albergo con la parete color terracotta'),
                            ('pexels-36603625.jpg', 'La scala a spirale bianca nella hall di un hotel'),
                            ('pexels-29419658.jpg', 'Gli archi d’acciaio di un ponte visti da sotto'),
                            ('pexels-20856131.jpg', 'L’angolo di un palazzo moderno, in bianco e nero'),
                            ('pexels-7303782.jpg', 'Una cucina chiara con una sedia di legno'),
                            ('pexels-37098141.jpg', 'Una camera minimale col letto bianco e le lampade'),
                            ('pexels-8297155.jpg', 'Una donna in piedi in un ufficio con la città dietro'),
                            ('pexels-7652130.jpg', 'Tre colleghi a una riunione intorno a un tavolo bianco')])]),
    ], padding='large'))

    out.append(domande(t, '06 — Le domande', ['Quelle delle', 'aziende.'], [
        ('Le foto restano nostre?', 'Sì: la licenza d’uso non scade e vale per sito, social, portali e stampa. Io chiedo solo di '
                                    'poterle mostrare nel portfolio, se siete d’accordo.'),
        ('Usi il drone?', 'No: lavoro da terra o da un terrazzo, con le ottiche decentrabili. Per le viste dall’alto lavoro con un '
                          'pilota abilitato, a parte.'),
        ('Quanto ritocchi?', 'Raddrizzo le linee, pulisco i fili e correggo la luce. Non tolgo muri e non metto cieli che non c’erano.'),
        ('Venite anche fuori Torino?', 'In tutto il Piemonte e in Valle d’Aosta il viaggio è compreso; altrove lo aggiungo al '
                                       'preventivo, al costo.'),
    ], 'az'))
    out.append(invito(t, 'Avete uno spazio <em>da far vedere?</em>',
                      'Scrivetemi cos’è, dove si trova e per cosa vi servono le foto: vi mando un preventivo entro sera.',
                      'Chiedi un preventivo', chiedi('Hotel e immobili')))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  RITRATTI
# ══════════════════════════════════════════════════════════════════════════════

def build_ritratti():
    t = Tpl(ID, 'ritratti')
    out = [intro(t, 'Ritratti · in studio o a casa vostra', ['Fermi,', 'solo un momento.'],
                 '<p>Un ritratto non è una foto in cui venite bene: è una foto in cui vi riconoscete. In studio ci sono una '
                 'finestra a nord, una sedia e il tempo che serve. Si parla, si beve un caffè; quando smettete di guardare '
                 'l’obiettivo, scatto.</p>')]
    out.append(t.section([
        t.one([sechead(t, '01 — I ritratti', ['Facce che ho', 'guardato a lungo.'], size=56, layout='stack',
                       tagline='Passate sopra le foto per allargarle, cliccate per vederle intere.')]),
        t.one([galleria(t, [('pexels-4534869.jpg', 'Una donna seduta sul davanzale di una grande finestra'),
                            ('pexels-8284464.jpg', 'Il ritratto in bianco e nero di un uomo anziano'),
                            ('pexels-27255537.jpg', 'La sagoma di una ragazza dietro un vetro, controluce'),
                            ('pexels-31951839.jpg', 'Un uomo appoggiato al muro di mattoni, in bianco e nero'),
                            ('pexels-6860127.jpg', 'Il viso di una donna con gli occhi chiusi, da vicino'),
                            ('pexels-12793812.jpg', 'Un uomo seduto sul divano, pensieroso, in bianco e nero'),
                            ('pexels-15148504.jpg', 'Una donna coi capelli sul viso nella luce della finestra'),
                            ('pexels-37512797.jpg', 'Un uomo contro il muro grigio in studio, in bianco e nero')], altezza='460px')]),
    ], padding='large', style={'padding_top': 20}))

    out.append(servizio(t, '02 — Il ritratto', 'Una persona, <em>una luce</em>.',
                        'Per voi, per il profilo di lavoro, per un compleanno che conta. Un’ora in studio con la luce della finestra, '
                        'poi i provini su carta: le dieci foto le scegliamo insieme.',
                        [('Durata', 'un’ora, in studio'), ('Ricevete', 'dieci immagini e una stampa fine art'), ('Da', '180 €')],
                        'pexels-8284464.jpg', 'Il ritratto in bianco e nero di un uomo anziano dallo sguardo profondo',
                        'Hasselblad 503 · 1/60 · f/4 · Ilford HP5', carta=True, html_id='ritratto'))
    out.append(servizio(t, '03 — Di coppia', 'Prima del sì, <em>o senza nessun sì</em>.',
                        'Una passeggiata al tramonto in collina o lungo il Po: per l’invito del matrimonio, per un anniversario, o '
                        'solo per avere una foto in due che vi somigli.',
                        [('Durata', 'un’ora e mezza, all’aperto'), ('Ricevete', 'trenta immagini'), ('Da', '260 €')],
                        'pexels-9242466.jpg', 'Due innamorati abbracciati nella luce dorata del tramonto',
                        'Superga · GFX 100 · 1/500 · f/2.8', giro=True, html_id='coppia'))
    out.append(servizio(t, '04 — In attesa', 'Nove mesi, <em>una luce sola</em>.',
                        'Fra il settimo e l’ottavo mese, in studio o a casa vostra, da sola o con chi aspetta con voi. Un abito '
                        'semplice, la tenda, nessuna fretta.',
                        [('Quando', 'fra la 28ª e la 34ª settimana'), ('Ricevete', 'venti immagini'), ('Da', '220 €')],
                        'pexels-17166034.jpg', 'Una donna incinta in abito nero accanto alla tenda chiara, le mani sul ventre',
                        'Studio · Hasselblad 503 · 1/30 · f/5.6', carta=True, html_id='attesa',
                        link=('Poi il primo anno', FAMIGLIA + '#primo-anno')))
    out.append(servizio(t, '05 — Book fotografici', 'Per chi lavora <em>con la propria faccia</em>.',
                        'Attori, modelle, musicisti, chi parla in pubblico: fondale grigio e luce di finestra, tre cambi d’abito, '
                        'un trucco leggero se serve. Le foto escono ritoccate quanto basta, non di più.',
                        [('Durata', 'mezza giornata'), ('Ricevete', 'venti immagini ritoccate'), ('Da', '390 €')],
                        'pexels-29977336.jpg', 'Una donna vestita di nero col cappuccio in studio, in bianco e nero',
                        'Studio · GFX 100 · 1/125 · f/8', giro=True, html_id='book'))
    out.append(servizio(t, '06 — Beauty e glamour', 'Un abito, un trucco, <em>una sera</em>.',
                        'Con una truccatrice in studio e l’abito che vi piace di più: per un regalo a voi stesse, o per ricordarvi '
                        'come eravate a quarant’anni. Per i ritratti più intimi c’è il boudoir.',
                        [('Durata', 'tre ore, con trucco e capelli'), ('Ricevete', 'venticinque immagini'), ('Da', '340 €')],
                        'pexels-15523449.jpg', 'Una donna in abito nero di paillettes seduta sul divano, la parete chiara dietro',
                        'Studio · Hasselblad 503 · 1/60 · f/4', carta=True, html_id='glamour', link=('Il boudoir', BOUDOIR)))

    out.append(domande(t, '07 — Le domande', ['Quelle di chi', 'non si piace.'], [
        ('Non sono fotogenico.', 'Lo dicono quasi tutti. Dopo venti minuti nessuno se lo ricorda più, e lì comincio a scattare.'),
        ('Cosa mi metto?', 'Quello che mettete quando state bene. Tinte unite, niente scritte, e portate un secondo cambio.'),
        ('Posso portare qualcuno?', 'Sì. Ma di solito si viene meglio da soli: chi accompagna guarda, e chi è guardato se ne accorge.'),
        ('Si può regalare?', 'Sì: il buono regalo vale un anno, e lo stampo su carta cotone dentro una busta chiusa.'),
    ], 'rit'))
    out.append(invito(t, 'Venite <em>con un’ora libera</em>.', 'Il resto lo facciamo insieme. Ditemi che ritratto avete in mente.',
                      'Prenota un ritratto', chiedi('Ritratto')))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  FAMIGLIA
# ══════════════════════════════════════════════════════════════════════════════

def build_famiglia():
    t = Tpl(ID, 'famiglia')
    out = [intro(t, 'Famiglia', ['In casa vostra,', 'con la luce di casa.'],
                 '<p>Niente sfondi, niente cappellini, niente «guardate qui». Vengo da voi la mattina, quando il sole entra in '
                 'cucina, e fotografo quello che succede: la colazione, il bagnetto, la lotta sul letto. Dopo due ore nessuno si '
                 'ricorda più di me.</p>')]
    out.append(t.section([
        t.one([galleria(t, [('pexels-4609048.jpg', 'Una famiglia abbracciata davanti a una grande finestra luminosa'),
                            ('pexels-12594181.jpg', 'Un bambino in piedi accanto a una sedia, in bianco e nero'),
                            ('pexels-10283404.jpg', 'Un neonato che dorme, in bianco e nero'),
                            ('pexels-13965216.jpg', 'Una bambina bionda col mento sulle mani, sul divano'),
                            ('pexels-8120950.jpg', 'Una famiglia seduta sul pavimento di legno vicino alla finestra'),
                            ('pexels-40446142.jpg', 'Una bambina coi riccioli e gli occhi azzurri, in camicetta bianca'),
                            ('pexels-34651408.jpg', 'Una neonata che dorme su una coperta bianca'),
                            ('pexels-38604997.jpg', 'Un bimbo sdraiato sul letto che guarda curioso')], altezza='440px')]),
    ], padding='large', style={'padding_top': 20}))

    out.append(t.section([
        t.one([sechead(t, '01 — Il primo anno', ['Quattro mattine', 'in un anno.'], size=64,
                       tagline='Dall’attesa al primo compleanno, sempre a casa vostra. Insieme costano 720 € invece di 900.')]),
        t.one([passi(t, [('In attesa', 'Al settimo mese, voi due e la pancia, in studio o in casa.'),
                         ('I primi giorni', 'Entro le prime due settimane, quando dorme quasi sempre.'),
                         ('Sei mesi', 'Sta seduto, ride, vi tira i capelli: è il momento migliore.'),
                         ('Il primo compleanno', 'La torta, gli invitati, i primi passi se ci sono.')])]),
    ], bg=solid(CARTA), padding='large', html_id='primo-anno'))

    out.append(servizio(t, '02 — Ritratti di famiglia', 'Tutti insieme, <em>almeno una volta l’anno</em>.',
                        'Genitori, figli, nonni, il cane. A casa vostra o in un parco che conoscete: due ore, qualche foto tutti '
                        'insieme e molte di quello che fate quando nessuno vi dice di stare fermi.',
                        [('Durata', 'due ore, a casa o all’aperto'), ('Ricevete', 'quaranta immagini'), ('Da', '280 €')],
                        'pexels-8120950.jpg', 'Una famiglia seduta sul pavimento di legno vicino a una grande finestra',
                        'A casa · GFX 100 · 1/125 · f/4', html_id='insieme'))
    out.append(servizio(t, '03 — Newborn', 'I primi <em>dieci giorni</em>.',
                        'Entro le prime due settimane, quando dormono quasi sempre. A casa vostra, col riscaldamento acceso e senza '
                        'fretta: tre ore, con tutte le pause per la poppata. Niente ceste e niente costumi, solo lui e voi.',
                        [('Quando', 'fra il quinto e il quattordicesimo giorno'), ('Durata', 'tre ore, a casa vostra'), ('Da', '320 €')],
                        'pexels-10283404.jpg', 'Un neonato che dorme sereno, in bianco e nero',
                        'A casa · Hasselblad 503 · 1/30 · f/2.8 · Ilford HP5', giro=True, carta=True, html_id='newborn'))
    out.append(servizio(t, '04 — I primi anni', 'Quando cominciano <em>a guardarvi</em>.',
                        'Dai sei mesi ai tre anni cambiano ogni settimana. Una mattina a casa, giocando per terra con loro: le foto '
                        'migliori vengono quando dimenticano la macchina fotografica.',
                        [('Durata', 'un’ora e mezza'), ('Ricevete', 'trenta immagini'), ('Da', '220 €')],
                        'pexels-40446142.jpg', 'Una bambina coi riccioli biondi e gli occhi azzurri in camicetta bianca',
                        'A casa · GFX 100 · 1/250 · f/2.8', html_id='bambini'))
    out.append(servizio(t, '05 — In attesa', 'Voi due, <em>e chi arriva</em>.',
                        'Le mani sulla pancia, la luce della finestra, una camicia bianca. Si può fare da sola o in coppia, e si '
                        'può tenere da parte per la prima pagina dell’album del primo anno.',
                        [('Quando', 'al settimo mese'), ('Durata', 'un’ora'), ('Da', '220 €')],
                        'pexels-5425036.jpg', 'Le mani di lui sulla pancia di lei, nella luce chiara di una stanza',
                        'Studio · GFX 100 · 1/60 · f/4', giro=True, carta=True, html_id='attesa'))

    out.append(domande(t, '06 — Le domande', ['Quelle dei', 'genitori.'], [
        ('E se piange?', 'Ci fermiamo. Nelle sedute coi bambini le pause sono metà del tempo, e sono già nel prezzo.'),
        ('Usi il flash?', 'No, mai coi neonati: solo la luce delle finestre. Per questo vengo la mattina.'),
        ('Dobbiamo preparare la casa?', 'No. Apriamo le tende e spostiamo una sedia, il resto va bene così com’è: è casa vostra.'),
    ], 'fam'))
    out.append(invito(t, 'Una mattina <em>a casa vostra</em>.', 'Ditemi chi c’è e quando nasce o è nato: troviamo il giorno giusto.',
                      'Fissiamo una mattina', chiedi('Famiglia')))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  BOUDOIR
# ══════════════════════════════════════════════════════════════════════════════

def build_boudoir():
    t = Tpl(ID, 'boudoir')
    out = [intro(t, 'Boudoir · solo su appuntamento', ['Per voi,', 'prima che per altri.'],
                 '<p>Un ritratto intimo, in camicia, in lingerie, in un lenzuolo: per vedervi come non vi siete mai viste. Nessuna '
                 'foto esce dallo studio senza il vostro sì.</p>')]
    out.append(fascia_scura(t, 'pexels-30362729.jpg', 'Via Mazzini 31 · la stanza con la tenda',
                            'Solo noi due, e la tenda chiusa.',
                            'Lo studio è vostro per tre ore: la porta chiusa, la musica che scegliete, un tè fra una posa e l’altra.',
                            pos='center 35%'))
    out.append(servizio(t, '01 — Boudoir', 'La luce di una finestra, <em>e basta</em>.',
                        'Il bianco e nero, il lino, una luce che accarezza invece di mostrare. Vi dico io come mettervi, un passo '
                        'alla volta: non serve aver mai posato.',
                        [('Durata', 'tre ore, in studio'), ('Ricevete', 'venticinque immagini'), ('Da', '420 €')],
                        'pexels-6039107.jpg', 'Una donna sdraiata sul tessuto bianco, vista dall’alto, in bianco e nero',
                        'Studio · Hasselblad 503 · 1/30 · f/4 · Ilford HP5', html_id='boudoir'))
    out.append(servizio(t, '02 — Ritratti glamour', 'Un abito lungo, <em>una sera</em>.',
                        'L’abito da sera che non mettete mai, il trucco di una truccatrice, la luce calda dell’ultima ora: una '
                        'serata per voi, da tenere in un album.',
                        [('Durata', 'tre ore, con trucco e capelli'), ('Ricevete', 'venti immagini'), ('Da', '380 €')],
                        'pexels-7244030.jpg', 'Una donna in abito lungo grigio seduta nella luce calda di una finestra',
                        'Studio · GFX 100 · 1/60 · f/2.8', giro=True, carta=True, html_id='glamour'))

    out.append(t.section([
        t.row([t.col([sechead(t, '03 — La riservatezza', ['Quello che succede', 'in studio, resta lì.'], size=56, layout='stack',
                              size_mobile=36),
                      text(t, '<p>Lo scrivo nel contratto, prima di cominciare.</p>', size=16)], wm='2-5', ws='1-1'),
               t.col([specs(t, [('In studio', 'solo noi due; la truccatrice se la volete'),
                                ('Le foto', 'in una galleria privata con la password, per trenta giorni'),
                                ('Pubblicate', 'mai, senza un vostro sì scritto'),
                                ('L’archivio', 'cancellato dopo la consegna, se lo chiedete'),
                                ('L’album', 'in una scatola chiusa, consegnato a mano')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='start'),
    ], padding='large'))

    out.append(t.section([
        t.one([sechead(t, '04 — Come si svolge', ['Una telefonata,', 'poi tre ore per voi.'], size=56, layout='stack')]),
        t.one([passi(t, [('Una telefonata', 'Cosa vi piacerebbe, cosa no. Nessun impegno.'),
                         ('Prepariamo', 'Gli abiti, il trucco, la musica: decidete voi.'),
                         ('In studio', 'Tre ore, con le pause e un tè. Si comincia vestite.'),
                         ('La scelta', 'Una settimana dopo, sui provini, solo voi e io.')])]),
    ], bg=solid(CARTA), padding='large'))

    out.append(t.section([
        t.row([t.col([galleria(t, [('pexels-25470069.jpg', 'Una donna avvolta in un tessuto scuro, in bianco e nero'),
                                   ('pexels-15929707.jpg', 'Una donna elegante in abito nero seduta sul divano'),
                                   ('pexels-30362729.jpg', 'Una donna seduta sul letto vicino alla finestra, in bianco e nero')],
                                cols=3, altezza='520px', mobile=1)], wm='3-5', ws='1-1'),
               t.col([sechead(t, '05 — Quanto costa', ['Tre ore,', 'un album.'], size=48, layout='stack', size_mobile=34),
                      listino(t, [('Boudoir', 'Tre ore in studio, venticinque immagini', '420 €'),
                                  ('Ritratti glamour', 'Con truccatrice e parrucchiera', '380 €'),
                                  ('L’album', 'Venti stampe in una scatola di tela', '+ 290 €'),
                                  ('Il buono regalo', 'Vale un anno, in una busta chiusa', 'a scelta')])], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=64, valign='center'),
    ], padding='large'))

    out.append(domande(t, '06 — Le domande', ['Quelle che non', 'si fanno ad alta voce.'], [
        ('Non sono una modella.', 'Nessuna lo è. Vi dico io dove mettere le mani e dove guardare, e possiamo fermarci quando volete.'),
        ('Chi vede le foto?', 'Voi e io. Non le mostro a nessuno e, se lo chiedete, le cancello dal mio archivio dopo la consegna.'),
        ('Devo spogliarmi?', 'Solo quanto volete voi. Molte delle foto più belle sono in camicia o in maglione.'),
        ('È adatto come regalo?', 'Sì, per voi o per chi amate: il buono vale un anno e lo consegno in una busta chiusa.'),
    ], 'bou'))
    out.append(invito(t, 'Ne parliamo <em>prima, al telefono</em>.', 'Scrivetemi solo nome e recapito: vi chiamo io, quando vi va bene.',
                      'Scrivimi', chiedi('Boudoir')))
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
    ], bg=video_bg(V_FOGLIE, overlay=0, color=FONDO), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.2.0', 'preview_v': '3', 'preview_mobile_v': '3',
    'category': 'Creativi e portfolio', 'zone': 'Il fuoco lo decidi tu',
    'tags': ['fotografia', 'architettura', 'still life', 'hotel', 'immobili', 'matrimoni', 'ritratti', 'famiglia', 'newborn',
             'boudoir', 'eventi', 'minimale', 'torino'],
    'description': 'Lo studio di una fotografa di Torino: architettura, interni e oggetti, hotel, studi tecnici, agenzie '
                   'immobiliari e aziende; dodici matrimoni l’anno e le cerimonie in famiglia; ritratti, famiglie, newborn e '
                   'boudoir; le feste della città. Carta grigia e grafite con un rosa lampone, il mirino sopra il video '
                   'd’apertura (il fuoco segue il mouse, il clic scatta e fa un provino), le sezioni che si svelano di lato, i '
                   'listini a prezzi chiusi e il modulo che sa già cosa chiedete.',
    'menu': {'name': 'Lunga Posa — menu', 'items': [
        {'title': 'I lavori', 'url': LAVORI},
        {'title': 'Servizi', 'url': HOME + '#servizi', 'children': [
            {'title': 'Per le aziende', 'url': AZIENDE}, {'title': 'Matrimoni e cerimonie', 'url': MATRIMONI},
            {'title': 'Ritratti', 'url': RITRATTI}, {'title': 'Famiglia', 'url': FAMIGLIA}, {'title': 'Boudoir', 'url': BOUDOIR},
            {'title': 'Eventi in città', 'url': EVENTI}]},
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
        'aziende': {'file': 'aziende.json', 'type': 'page', 'title': 'Lunga Posa — Per le aziende'},
        'ritratti': {'file': 'ritratti.json', 'type': 'page', 'title': 'Lunga Posa — Ritratti'},
        'famiglia': {'file': 'famiglia.json', 'type': 'page', 'title': 'Lunga Posa — Famiglia'},
        'boudoir': {'file': 'boudoir.json', 'type': 'page', 'title': 'Lunga Posa — Boudoir'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'lavori': {'title': 'I lavori', 'template': 'lavori', 'slug': 'lavori'},
        'matrimoni': {'title': 'Matrimoni', 'template': 'matrimoni', 'slug': 'matrimoni'},
        'eventi': {'title': 'Eventi in città', 'template': 'eventi', 'slug': 'eventi'},
        'studio': {'title': 'Lo studio', 'template': 'studio', 'slug': 'studio'},
        'aziende': {'title': 'Per le aziende', 'template': 'aziende', 'slug': 'aziende'},
        'ritratti': {'title': 'Ritratti', 'template': 'ritratti', 'slug': 'ritratti'},
        'famiglia': {'title': 'Famiglia', 'template': 'famiglia', 'slug': 'famiglia'},
        'boudoir': {'title': 'Boudoir', 'template': 'boudoir', 'slug': 'boudoir'},
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
              'studio.json': build_studio(), 'aziende.json': build_aziende(), 'ritratti.json': build_ritratti(),
              'famiglia.json': build_famiglia(), 'boudoir.json': build_boudoir(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
