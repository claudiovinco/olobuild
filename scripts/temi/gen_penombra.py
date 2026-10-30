# -*- coding: utf-8 -*-
"""Genera il tema «Penombra» (id penombra): uno studio di lighting design a Milano. Scuro e caldo.

Nero di sala con un filo di bruno, bianco carta e un solo colore, quello della luce a 2700 K. Titoli
nell'Instrument Serif (anche in corsivo), testo in Manrope, i dati della luce (kelvin, resa, abbagliamento) in IBM Plex
Mono. Angoli vivi, fili sottili, grana.

Il banner è una sala da pranzo al buio dove la luce la porta chi guarda: la torcia calda segue il puntatore (Effetti
mouse → Torcia, fusione «color-dodge»). Poi lo stesso tavolo spento e acceso da trascinare (Confronto immagini), i
quattro mondi che si svelano di lato, la lampadina che si accende davvero nel video, i progetti col monitor che segue il
cursore, il metodo in cinque passi, i numeri della luce, lo studio, il modulo per il sopralluogo.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'penombra'
NAME = 'Penombra'

HEX = {
    'primary': '#F0B25A', 'primary_contrast': '#14110D', 'secondary': '#7C8A96', 'secondary_contrast': '#14110D',
    'accent': '#F0B25A', 'dark': '#0A0907', 'light': '#F1EBE1', 'muted': '#17140F', 'muted_contrast': '#F1EBE1',
    'text': '#EDE6DA', 'text_muted': '#9C9385', 'background': '#0F0D0B', 'border': '#2C2721', 'link': '#F0B25A',
}

CARTA = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
LUCE = 'var(--olo-color-primary)'
ON_LUCE = 'var(--olo-color-primary-contrast)'
FREDDA = 'var(--olo-color-secondary)'
FONDO = 'var(--olo-color-background)'
SALA = 'var(--olo-color-muted)'
NERO = 'var(--olo-color-dark)'
FILO = 'var(--olo-color-border)'
SOFT = mix(CARTA, 82)
MONO = 'var(--olo-font-family-mono)'
SQ = radius(0)

ADDRESS = 'Via Tortona 31, 20144 Milano'
PHONE = '02 0000 2700'
PHONE_TEL = 'tel:+390200002700'
MAIL = 'studio@penombra.example'
LAT, LNG = '45.4523', '9.1640'
HOME = '{{PAGINA:home}}'
PROGETTI = '{{PAGINA:progetti}}'
METODO = '{{PAGINA:metodo}}'
STUDIO = '{{PAGINA:studio}}'

V_ACCENDE = 'pexels-video-853772'     # una lampadina a filamento si accende nel buio
V_FILAMENTI = 'pexels-video-7934360'  # lampadine a filamento appese, calde
V_RATTAN = 'pexels-video-35993895'    # due sospensioni in rattan accese in una stanza buia
V_POLVERE = 'pexels-video-36637658'   # un fascio di luce nella polvere
V_FACCIATA = 'pexels-video-7628839'   # una facciata che cambia colore di notte
V_FASCI = 'pexels-video-29471049'     # fasci di luce dal basso nel buio

PHOTOS = [
    'pexels-28059309.jpg', 'pexels-31132843.jpg', 'pexels-31132843-spenta.jpg', 'pexels-28818507.jpg',
    'pexels-28818507-spenta.jpg', 'pexels-36650038.jpg', 'pexels-33824477.jpg', 'pexels-6896368.jpg', 'pexels-30619805.jpg',
    'pexels-9617374.jpg', 'pexels-9617889.jpg', 'pexels-9618104.jpg', 'pexels-30120522.jpg', 'pexels-30743729.jpg',
    'pexels-37679997.jpg', 'pexels-31748049.jpg', 'pexels-518973.jpg', 'pexels-10995844.jpg', 'pexels-30120703.jpg',
    'pexels-35649559.jpg', 'pexels-39007190.jpg', 'pexels-34455132.jpg', 'pexels-36363110.jpg', 'pexels-13806246.jpg',
    'pexels-19343312.jpg', 'pexels-30420679.jpg', 'pexels-8551429.jpg', 'pexels-16120131.jpg', 'pexels-29649734.jpg',
    'pexels-10874935.jpg', 'pexels-35870240.jpg', 'pexels-14648910.jpg', 'pexels-10220276.jpg', 'pexels-34922354.jpg',
    'pexels-16698956.jpg', 'pexels-31875658.jpg', 'pexels-2416875.jpg', 'pexels-15258325.jpg', 'pexels-33357683.jpg',
    'pexels-10294781.jpg', 'pexels-35431024.jpg', 'pexels-517985.jpg', 'pexels-698317.jpg', 'pexels-15725444.jpg',
    'pexels-36680074.jpg', 'pexels-20632309.jpg', 'pexels-31074126.jpg',
]
VIDEOS = [V_ACCENDE, V_FILAMENTI, V_RATTAN, V_POLVERE, V_FACCIATA, V_FASCI]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=40, color=NERO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=45, color=NERO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


def torcia(size=560, soft=78, blend='color-dodge', scope='element', color=None):
    """La luce che segue il puntatore (Avanzate → Effetti mouse → Torcia)."""
    return {'cursor_spotlight': True, 'cursor_spotlight_scope': scope, 'cursor_spotlight_size': size,
            'cursor_spotlight_softness': soft, 'cursor_spotlight_blend': blend,
            'cursor_spotlight_color': color or mix(LUCE, 55, CARTA), 'cursor_spotlight_easing': 14}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=40):
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': LUCE, 'eyebrow_dot_color': LUCE,
        'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '400',
        'headline_lines': [{'text': l, 'color': CARTA if i % 2 == 0 else LUCE, 'italic': i % 2 == 1} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.0,
        'headline_font_weight': '400', 'headline_align': align,
        'headline_font_size_tablet': max(38, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 16, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=CARTA, tag='h3', pad_bottom=10, align='left', weight='400', spacing=None, sizes=None):
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': True, 'accent_color': LUCE})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if sizes:
        o.update({'heading_font_size_tablet': str(sizes[0]), 'heading_font_size_mobile': str(sizes[1])})
    elif size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(28, int(size * 0.55)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=TENUE, pad_bottom=14, align='left', pad_top=0):
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 11, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '1.5'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=16, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='filo', align='left', pad_top=14):
    if kind == 'luce':
        o = {'bg_color': LUCE, 'text_color': ON_LUCE, 'hover_bg_color': CARTA, 'hover_text_color': NERO}
    else:
        o = {'bg_color': 'transparent', 'text_color': CARTA, 'hover_bg_color': CARTA, 'hover_text_color': NERO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(CARTA, 45), 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 11, 'font_weight': '600',
              'text_transform': 'uppercase', 'letter_spacing': '2'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='16/9', position='center center', drift=False, lightbox=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -24, 'position': 0}, {'value': 24, 'position': 100}]}} if drift else None)


def still(t, name, alt, dato, ratio='16/9', position='center center', drift=False):
    """Una foto col suo dato di luce sotto, in mono."""
    return [image(t, name, alt, ratio=ratio, position=position, drift=drift), label(t, dato, pad_top=10, pad_bottom=0)]


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'vertical', 'term_color': TENUE, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': CARTA, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': STUDIO + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': LUCE, 'icon_size': '16', 'text_color': CARTA, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': CARTA, 'header_text_color_active': LUCE, 'header_font_size': 24, 'header_font_weight': '400',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': LUCE, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def confronto(t, prima, dopo, lab_prima, lab_dopo, ratio='3/2', start=50):
    return t.classic('imgcompare', {
        'preset': 'custom', 'before_image': img(prima), 'after_image': img(dopo), 'before_label': lab_prima,
        'after_label': lab_dopo, 'show_labels': True, 'start_position': start, 'orientation': 'horizontal',
        'handle_color': LUCE, 'handle_size': 46, 'handle_border': '2', 'line_width': 2, 'height': '',
        'aspect_ratio': 'custom', 'aspect_ratio_custom': ratio, 'border_radius': 0, 'object_fit': 'cover',
        'object_position': 'center center', 'card_shadow': 'none', 'autoplay': False,
    })


def intro(t, eyebrow, lines, body, foto=None, size=104, pos='center center'):
    sec = t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=46)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=17, color=SOFT if foto else TENUE)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], bg=photo_bg(foto, overlay=72, position=pos) if foto else None, padding='large',
        style={'padding_top': 200, 'padding_bottom': 72})
    if foto:
        sec['advanced'].update(torcia(size=380, scope='section'))
    return sec


def pannello(t, foto, eyebrow, titolo, testo, link_t, link_u, overlay=56, pos='center center'):
    blocco = [label(t, eyebrow, color=LUCE),
              headline(t, titolo, size=80, tag='h2', pad_bottom=16),
              text(t, '<p>' + testo + '</p>', size=16, color=SOFT)]
    if link_t:
        blocco.append(button(t, link_t, link_u, pad_top=10))
    return t.section([
        t.row([t.col(blocco, wm='1-2', ws='1-1'), t.col([], wm='1-2', ws='1-1')], layout='custom', custom='52,48',
              valign='end', anim=None),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='large', width='large', sticky_effect='reveal-h',
        sticky_h_align='bottom', style={'padding_top': 140, 'padding_bottom': 80})


def modulo(t, campi, oggetto, invio, grazie):
    return t.classic('form', {
        'fields': campi, 'email_subject': oggetto, 'submit_text': invio, 'submit_icon': '', 'success_message': grazie,
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': CARTA, 'label_size': '11', 'label_weight': '400',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': 'transparent', 'input_color': CARTA,
        'input_border_color': mix(CARTA, 30), 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 0,
        'input_focus_border': LUCE, 'input_focus_shadow': False, 'gap': '20', 'submit_bg': LUCE, 'submit_color': ON_LUCE,
        'submit_radius': 0, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '11',
        'submit_font_weight': '600', 'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2',
        'submit_hover_bg': CARTA, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})


def campo(tipo, etichetta, nome, obbligatorio=False, larghezza='1-2', opzioni='', segnaposto=''):
    return {'field_type': tipo, 'label': etichetta, 'name': nome, 'placeholder': segnaposto, 'required': obbligatorio,
            'width': larghezza, 'options': opzioni, 'icon': ''}


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER · FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': 'Penombra', 'logo_text_size': '24', 'logo_text_color': CARTA,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': FONDO, 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': CARTA, 'hover_color': LUCE, 'active_color': LUCE,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '34',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': LUCE,
        'button_mode': 'last', 'btn_bg': LUCE, 'btn_color': ON_LUCE, 'btn_hover_color': ON_LUCE,
        'btn_radius': SQ, 'btn_padding': {'top': 11, 'right': 20, 'bottom': 11, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '600', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '#', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': NERO, 'mobile_text_color': CARTA, 'mobile_accent_color': LUCE,
        'hamburger_style': 'minimal', 'hamburger_color': CARTA, 'mobile_font_size': '44', 'mobile_link_size': '44',
        'mobile_link_font': 'heading', 'mobile_link_weight': '400',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(CARTA, 10)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'Penombra', size=150, tag='p', pad_bottom=0, weight='400', spacing=-3, sizes=(96, 56))
    claim = text(t, '<p>Studio di lighting design a Milano dal 2009: case, ristoranti, hotel, musei e facciate. Progettiamo '
                    'la luce prima delle lampade, e il buio prima della luce.</p>', size=15, pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': CARTA, 'link_hover_color': LUCE, 'active_color': LUCE,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('I progetti', PROGETTI), ('Il metodo', METODO), ('Lo studio', STUDIO), ('Un sopralluogo', STUDIO + '#sopralluogo'),
                  ('Privacy', '/privacy-policy/')])
    sala = text(t, '<p>La sala delle prove è in fondo al cortile: dodici metri di buio, un soffitto a binari e le lampade '
                   'di sessanta produttori.</p><p>Su appuntamento, anche dopo le 19.</p>', size=15, color=CARTA)
    legal = text(t, '<p>© 2026 Penombra Lighting Design s.r.l. · Milano · Le foto dei progetti appartengono ai committenti.</p>',
                 size=12)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'linkedin', 'url': 'https://linkedin.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': TENUE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': NERO, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim, contacts(t)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine', color=LUCE), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'La sala delle prove', color=LUCE), sala], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
                          'padding_top': 22, 'margin_top': 24})
    sec = t.section([top, t.one([brand], anim='fade', style={'margin_top': 64}), bottom], bg=solid(NERO), padding='large',
                    style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}})
    sec['advanced'].update(torcia(size=520, soft=80, scope='section', color=mix(LUCE, 45)))
    return [sec]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def build_home():
    t = Tpl(ID, 'home')
    out = []
    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Lighting design · Milano · dal 2009', 'eyebrow_dot': True,
        'title': 'La luce<br>è il primo<br><em>materiale.</em>',
        'subtitle': 'Progettiamo la luce di case, ristoranti, hotel e musei: quella che si vede e quella che non si nota. '
                    'Muovi il puntatore, o il dito, sulla sala: la luce la porti tu.',
        'media_bg': {'type': 'image', 'image_url': img('pexels-28059309.jpg'), 'image_size': 'cover',
                     'image_position': 'center 60%'},
        'overlay_color': NERO, 'overlay_top': 0.55, 'overlay_bottom': 0.82, 'overlay_sides': False, 'overlay_direction': 'left',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': LUCE, 'text_color': CARTA, 'title_color': CARTA, 'subtitle_color': SOFT,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 128, 'title_font_weight': '400',
        'title_line_height': '0.92', 'title_letter_spacing': '-3', 'title_text_transform': 'none',
        'subtitle_font_size': 17, 'subtitle_max_width': 460,
        'min_height': '100vh', 'content_max_width': 1320, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 150, 'right': 44, 'bottom': 84, 'left': 44},
        'cta_text': 'I progetti', 'cta_url': PROGETTI, 'cta_bg_color': LUCE, 'cta_text_color': ON_LUCE,
        'cta_radius': SQ, 'cta_size': '11', 'cta_style': 'filled', 'cta_font_weight': '600', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2',
        'cta2_text': 'Un sopralluogo, di sera', 'cta2_url': STUDIO + '#sopralluogo', 'cta2_text_color': CARTA, 'cta2_style': 'ghost',
        'meta_text': '2700 K · CRI 97 · UGR < 16', 'scroll_hint': '', 'scene_depth': 'soft', 'scene_depth_fade': True, 'module': '',
    }, advanced=torcia(size=520, soft=74))
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    out.append(t.section([
        t.one([sechead(t, '01 — Spenta, accesa', ['Lo stesso tavolo,', 'un’altra sera.'], size=84,
                       tagline='Trascina la maniglia: a sinistra la sala com’era, a destra con tre punti luce a 2700 K e il '
                               'resto spento. Nessuna lampada in più, solo quelle giuste al posto giusto.')]),
        t.row([t.col([confronto(t, 'pexels-31132843-spenta.jpg', 'pexels-31132843.jpg', 'Spenta', 'Accesa'),
                      label(t, 'Osteria in Brera · una lampada da tavolo per coperto · 2200 K', pad_top=12, pad_bottom=0)],
                     wm='1-2', ws='1-1'),
               t.col([confronto(t, 'pexels-28818507-spenta.jpg', 'pexels-28818507.jpg', 'Spenta', 'Accesa', start=40),
                      label(t, 'Hotel sul lago · la conca dei fiori, luce radente dal pavimento · 3000 K', pad_top=12,
                            pad_bottom=0)], wm='1-2', ws='1-1')], layout='50-50', gap=28),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 130}))

    for foto, ey, ti, tx, pos in [
        ('pexels-36650038.jpg', '01 / 04 — Case', 'La sera, a casa.',
         'Scenari per cenare, leggere, ricevere e dormire, da un interruttore o dal telefono. La luce di giorno la portano '
         'le finestre: noi pensiamo a quella dopo.', 'center center'),
        ('pexels-33824477.jpg', '02 / 04 — Ristoranti e hotel', 'Il piatto prima della sala.',
         'Una lampada per coperto, la sala in penombra, l’ingresso che si vede dalla strada. Tariamo la luce a locale aperto, '
         'con i clienti seduti.', 'center center'),
        ('pexels-6896368.jpg', '03 / 04 — Musei e gallerie', 'L’opera e nient’altro.',
         'Proiettori sagomati, filtri UV e lux misurati opera per opera. Il visitatore vede la scultura, non la lampada.',
         'center 35%'),
        ('pexels-30619805.jpg', '04 / 04 — Facciate e giardini', 'La città, di notte.',
         'Facciate storiche, cortili e giardini: luce che disegna senza abbagliare, e che si spegne a mezzanotte da sola.',
         'center center'),
    ]:
        out.append(pannello(t, foto, ey, ti, tx, 'I progetti', PROGETTI, pos=pos))

    numeri = t.classic('statstrip', {
        'items': [{'value': '17', 'label': 'anni di progetti'}, {'value': '240', 'label': 'spazi accesi'},
                  {'value': '−58%', 'label': 'consumi medi dopo di noi'}, {'value': '1', 'label': 'sopralluogo, sempre di sera'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': CARTA, 'value_size': 64, 'value_weight': '400',
        'label_color': TENUE, 'label_size': 11, 'label_uppercase': True, 'label_weight': '400', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([
        t.row([t.col([label(t, '02 — Come lavoriamo', color=LUCE)], wm='1-4', ws='1-1'),
               t.col([headline(t, 'Prima di disegnare una lampada, <em>spegniamo tutte le altre.</em> Poi accendiamo solo quello '
                                  'che serve, dove serve, quando serve.', size=48, tag='h2', pad_bottom=0)], wm='3-4', ws='1-1')],
              layout='custom', custom='25,75', gap=32, valign='start'),
        t.one([numeri], style={'margin_top': 72}),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 120}))

    out.append(t.section([
        centered(t, [label(t, '00:00:02 — l’attimo prima', color=SOFT, align='center'),
                     headline(t, 'Il buio è <em>metà del progetto.</em>', size=104, tag='h2', align='center', pad_bottom=18),
                     text(t, '<p>Una lampadina che si accende in una stanza buia fa più effetto di dieci in una stanza già '
                             'chiara. Per questo il primo sopralluogo lo facciamo sempre di sera.</p>', size=16, color=SOFT,
                          align='center')]),
    ], bg=video_bg(V_ACCENDE, overlay=30), padding='xlarge', style={'padding_top': 230, 'padding_bottom': 230}))

    voce = t.classic('testimonial', {
        'quote': 'Abbiamo tolto metà delle lampade e la sala sembra più grande. <em>I clienti adesso fotografano i piatti, '
                 'non il soffitto.</em>',
        'author_name': 'Giulia Ferrante', 'author_role': 'Osteria del Carmine, Brera · 2025',
        'rating': '5', 'text_color': CARTA, 'star_color': LUCE, 'author_color': TENUE, 'quote_accent_color': LUCE,
        'quote_font': 'heading', 'quote_size': 44, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': LUCE,
    })
    out.append(t.section([centered(t, [voce])], bg=solid(SALA), padding='large', style={'padding_top': 130, 'padding_bottom': 130}))

    cta = t.section([
        t.row([t.col([headline(t, 'Hai uno spazio <em>da accendere?</em>', size=84, tag='h2', pad_bottom=20),
                      text(t, '<p>Raccontacelo: una casa, un locale, una facciata. Veniamo a vederlo di sera, con un '
                              'luxmetro e due lampade di prova, e in una settimana ti mandiamo la prima idea.</p>', size=17),
                      button(t, 'Prenota un sopralluogo', STUDIO + '#sopralluogo', kind='luce', pad_top=12)], wm='3-5', ws='1-1'),
               t.col(still(t, 'pexels-9617374.jpg', 'Un progettista disegna la pianta delle luci alla lampada da tavolo',
                           'Tavola 04 · scenario «cena» · 120 lux sul piano', ratio='4/5', drift=True), wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 140})
    cta['advanced'].update(torcia(size=420, scope='section', color=mix(LUCE, 40)))
    out.append(cta)
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Penombra',
                page_intro_caption='Accendo la sala · 2700 K', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  I PROGETTI
# ══════════════════════════════════════════════════════════════════════════════

ELENCO = [
    ('Osteria del Carmine', 'Ristorante · Milano, Brera · 2025', 'pexels-31132843.jpg'),
    ('Villa sul lago', 'Hotel · Lago di Como · 2025', 'pexels-28818507.jpg'),
    ('Casa Fioravanti', 'Abitazione · Milano, Porta Romana · 2026', 'pexels-36650038.jpg'),
    ('Sala dei Gessi', 'Museo · Bergamo · 2024', 'pexels-6896368.jpg'),
    ('Palazzo Dugnani', 'Facciata storica · Milano · 2024', 'pexels-30619805.jpg'),
    ('Bar Notturno', 'Cocktail bar · Torino · 2023', 'pexels-10995844.jpg'),
    ('Giardino Arese', 'Giardino privato · Brianza · 2023', 'pexels-517985.jpg'),
    ('Galleria Ottanta', 'Galleria d’arte · Milano · 2022', 'pexels-10220276.jpg'),
]

PROGETTI_SCHEDE = [
    ('osteria-del-carmine', 'Osteria del Carmine', 'Ristorante · Brera · 2025',
     'Quaranta coperti sotto una volta bassa. Una lampada a batteria per tavolo, la volta lasciata nel buio, il bancone '
     'acceso dal basso. Consumi giù del 64%.', ['pexels-31132843.jpg', 'pexels-30420679.jpg', 'pexels-8551429.jpg'],
     [('Temperatura', '2200 K sui tavoli, 2700 K al bancone'), ('Punti luce', 'da 86 a 31'), ('Controllo', 'tre scenari, DALI')]),
    ('villa-sul-lago', 'Villa sul lago', 'Hotel · Lago di Como · 2025',
     'La hall, le scale e ventidue camere. Di giorno la luce la fa il lago; di sera la conca dei fiori, la scala e i '
     'corridoi bassi, come in una casa.', ['pexels-28818507.jpg', 'pexels-33824477.jpg', 'pexels-29649734.jpg'],
     [('Temperatura', '3000 K negli spazi comuni, 2700 K in camera'), ('Scenari', 'giorno, sera, notte'), ('Durata', '7 mesi')]),
    ('casa-fioravanti', 'Casa Fioravanti', 'Abitazione · Porta Romana · 2026',
     'Un appartamento degli anni Trenta. Niente faretti nel soffitto: lampade da terra e da tavolo, una gola sopra la '
     'libreria e un solo pulsante vicino alla porta: «sera».', ['pexels-36650038.jpg', 'pexels-39007190.jpg', 'pexels-36363110.jpg'],
     [('Temperatura', '2700 K, 2200 K dopo le 22'), ('Lampade', '14, tutte orientabili'), ('Controllo', 'Casambi, dal telefono')]),
    ('sala-dei-gessi', 'Sala dei Gessi', 'Museo · Bergamo · 2024',
     'Trentadue gessi dell’Ottocento in una sala con le finestre a nord. Proiettori sagomati, un fondo scuro e ogni volto '
     'acceso da un lato solo, come li avrebbe disegnati lo scultore.', ['pexels-6896368.jpg', 'pexels-10874935.jpg', 'pexels-35870240.jpg'],
     [('Illuminamento', '150 lux sulle opere'), ('Filtri', 'UV e infrarosso'), ('Resa cromatica', 'CRI 98')]),
]


def build_progetti():
    t = Tpl(ID, 'progetti')
    out = [intro(t, 'I progetti', ['Duecentoquaranta', 'spazi accesi.'],
                 '<p>Passa sopra i nomi: il monitor mostra il progetto. Muovi il puntatore qui sopra: la luce resta la '
                 'nostra firma.</p>', foto='pexels-10995844.jpg')]
    elenco = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': c, 'desc': c, 'number': '%02d' % (i + 1), 'image': img(f),
                   'link_url': '#' + re.sub(r'[^a-z]+', '-', n.lower()).strip('-')
                   if re.sub(r'[^a-z]+', '-', n.lower()).strip('-') in {p[0] for p in PROGETTI_SCHEDE} else '', 'color': ''}
                  for i, (n, c, f) in enumerate(ELENCO)],
        'lead_mode': 'number', 'number_color': TENUE, 'number_hover_color': LUCE,
        'name_font_family': 'heading', 'name_weight': '400', 'name_color': CARTA, 'name_hover_color': LUCE, 'name_size': 60,
        'name_uppercase': False, 'desc_color': TENUE, 'desc_hover_color': CARTA, 'desc_size': 13, 'sub_uppercase': False,
        'row_padding_y': 24, 'hover_indent': 28, 'hover_bg': 'transparent', 'line_color': FILO,
        'peek': True, 'peek_mode': 'monitor', 'peek_width': 320,
        'row_padding': {'top': 24, 'right': 0, 'bottom': 24, 'left': 0},
    })
    out.append(t.section([t.one([elenco])], padding='large', style={'padding_top': 60}))

    for i, (aid, nome, chi, testo, foto, dati) in enumerate(PROGETTI_SCHEDE):
        galleria = t.classic('progallery', {
            'images': [{'url': img(f), 'alt': nome + ': un dettaglio della luce', 'caption': '', 'subtitle': '', 'text': ''} for f in foto],
            'layout': 'expand', 'layout_family': 'classic', 'columns': 3, 'gap': 6, 'img_height': '380px',
            'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 0, 'mobile_columns': 1,
            'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
            'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
        })
        out.append(t.section([
            t.row([t.col([label(t, chi, color=LUCE), headline(t, nome, size=60, tag='h2', pad_bottom=14),
                          text(t, '<p>' + testo + '</p>', size=16), specs(t, dati)], wm='1-3', ws='1-1'),
                   t.col([galleria], wm='2-3', ws='1-1')], layout='custom', custom='32,68', gap=56, valign='center'),
        ], bg=solid(SALA) if i % 2 == 0 else None, padding='large', html_id=aid))

    out.append(t.section([
        centered(t, [label(t, 'Il prossimo', color=LUCE, align='center'),
                     headline(t, 'Il prossimo progetto <em>può essere il tuo.</em>', size=72, tag='h2', align='center', pad_bottom=18),
                     button(t, 'Prenota un sopralluogo', STUDIO + '#sopralluogo', kind='luce', align='center')]),
    ], bg=video_bg(V_FILAMENTI, overlay=55), padding='xlarge', style={'padding_top': 200, 'padding_bottom': 200}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  IL METODO
# ══════════════════════════════════════════════════════════════════════════════

def build_metodo():
    t = Tpl(ID, 'metodo')
    out = [intro(t, 'Il metodo', ['Dal buio', 'alla taratura.'],
                 '<p>Cinque passi, sempre gli stessi, per una casa come per un museo. Il progetto lo firma una persona sola, '
                 'dal sopralluogo all’ultima lampada puntata.</p>', foto='pexels-36680074.jpg')]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Il sopralluogo, di sera', 'description': 'Misuriamo la luce che c’è, '
                   'fotografiamo le ombre e proviamo due lampade sul posto. Gratis a Milano.'},
                  {'number': '02', 'title': 'Gli scenari', 'description': 'Cosa succede nello spazio e a che ora: cena, lavoro, '
                   'pulizie, notte. Ogni scenario ha la sua luce.'},
                  {'number': '03', 'title': 'Il prototipo', 'description': 'Nella sala delle prove ricostruiamo un angolo in '
                   'scala vera e lo guardiamo insieme, al buio.'},
                  {'number': '04', 'title': 'Il cantiere', 'description': 'Disegni per l’elettricista, lampade ordinate da noi, '
                   'una visita a settimana in cantiere.'},
                  {'number': '05', 'title': 'La taratura', 'description': 'L’ultima sera puntiamo ogni lampada e regoliamo ogni '
                   'scenario, a spazio finito e abitato.'}],
        'columns': 5, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': LUCE, 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': CARTA, 'title_size': 26,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 14, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
        'card_radius': radius(0), 'card_padding': 22, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([t.one([passi])], padding='large', style={'padding_top': 60}))

    out.append(t.section([
        t.row([t.col([label(t, 'I numeri della luce', color=LUCE),
                      headline(t, 'Quattro numeri <em>che controlliamo sempre.</em>', size=64, tag='h2', pad_bottom=16),
                      text(t, '<p>Una lampada bella che abbaglia è una lampada sbagliata. Prima di sceglierne una guardiamo '
                              'questi dati, poi la proviamo accesa.</p>', size=16, color=SOFT),
                      specs(t, [('Temperatura di colore', '2200 — 3000 K: calda la sera, mai fredda in casa'),
                                ('Resa cromatica', 'CRI 95 o più: i colori del legno, della pelle, del cibo'),
                                ('Abbagliamento', 'UGR sotto 16: la sorgente non si vede'),
                                ('Controllo', 'DALI o Casambi: gli scenari dal muro o dal telefono')])], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_POLVERE, overlay=40), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col(still(t, 'pexels-9617889.jpg', 'Le mani di un progettista con il compasso sulla pianta delle luci',
                           'Tavola 02 · i punti luce, in pianta'), wm='1-2', ws='1-1'),
               t.col(still(t, 'pexels-34922354.jpg', 'Sospensioni di prova appese nella sala delle prove',
                           'Sala delle prove · sei sospensioni a confronto'), wm='1-2', ws='1-1')], layout='50-50', gap=24),
    ], padding='large', style={'padding_top': 120}))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande', ['Quelle che', 'ci fanno tutti.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Se la tua non c’è, scrivici: rispondiamo in giornata.</p>'),
                      button(t, 'Scrivici', STUDIO + '#sopralluogo')], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quanto costa un progetto della luce?', 'Per una casa da 2.500 €, per un locale da 4.800 €, IVA esclusa. Il '
                                                            'primo sopralluogo a Milano è gratuito.'),
                   ('Vendete anche le lampade?', 'Le scegliamo e le ordiniamo noi, al prezzo che pagheresti tu: niente ricarichi. '
                                                 'Ci paghi solo il progetto.'),
                   ('Lavorate col mio architetto?', 'Quasi sempre. Entriamo nel progetto quando le pareti sono ancora sulla carta: '
                                                    'costa meno a tutti.'),
                   ('Si può fare senza rompere i muri?', 'Spesso sì: lampade da terra e da tavolo, binari, batterie, comandi '
                                                        'senza fili. Lo diciamo al sopralluogo.'),
               ], 'met')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(SALA), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LO STUDIO
# ══════════════════════════════════════════════════════════════════════════════

def build_studio():
    t = Tpl(ID, 'studio')
    out = [intro(t, 'Lo studio', ['Tre persone', 'e una sala buia.'],
                 '<p>In via Tortona, in un ex laboratorio di cornici. Davanti lo studio, in fondo al cortile la sala delle '
                 'prove: dodici metri senza finestre.</p>', foto='pexels-34455132.jpg')]

    def persona(foto, nome, ruolo, bio, pos):
        return [image(t, foto, nome + ', ' + ruolo.lower(), ratio='4/5', position=pos),
                headline(t, nome, size=30, tag='h3', pad_bottom=2),
                label(t, ruolo, color=LUCE, pad_bottom=8, pad_top=6),
                text(t, '<p>' + bio + '</p>', size=15)]
    out.append(t.section([
        t.row([t.col(persona('pexels-30120522.jpg', 'Marta Ceriani', 'Lighting designer, fondatrice',
                             'Architetta, quindici anni nei teatri prima delle case. Firma ogni progetto e fa ogni taratura.',
                             'center 30%'), wm='1-3', ws='1-1'),
               t.col(persona('pexels-30743729.jpg', 'Luca Bassani', 'Progettista',
                             'Ingegnere elettrico, conosce a memoria seicento lampade e i loro difetti. Segue i cantieri.',
                             'center 30%'), wm='1-3', ws='1-1'),
               t.col(persona('pexels-37679997.jpg', 'Sara Oldani', 'Sala delle prove',
                             'Costruisce i prototipi e prova le lampade nuove prima che entrino in un progetto.',
                             'center 30%'), wm='1-3', ws='1-1')],
              layout='33-33-33', gap=32, valign='start'),
    ], padding='large', style={'padding_top': 40}))

    out.append(t.section([
        t.row([t.col([label(t, 'La sala delle prove · in fondo al cortile', color=LUCE),
                      headline(t, 'Dodici metri <em>di buio.</em>', size=72, tag='h2', pad_bottom=16),
                      text(t, '<p>Un soffitto a binari, pareti nere e grigie, campioni di legno, pietra e tessuto. Ci '
                              'ricostruiamo un angolo del tuo spazio e lo accendiamo davanti a te.</p>', size=16, color=SOFT),
                      specs(t, [('Lampade in prova', 'di sessanta produttori'), ('Su appuntamento', 'anche dopo le 19'),
                                ('Visita', 'gratuita, un’ora')])], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_RATTAN, overlay=50), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 110}))

    campi = [campo('text', 'Nome', 'nome', obbligatorio=True), campo('email', 'Email', 'email', obbligatorio=True),
             campo('tel', 'Telefono', 'telefono'),
             campo('select', 'Lo spazio', 'spazio', obbligatorio=True,
                   opzioni='Una casa\nUn ristorante o un bar\nUn hotel\nUn negozio\nUn museo o una galleria\nUna facciata o un giardino'),
             campo('text', 'Dove', 'dove', segnaposto='Città o quartiere'), campo('text', 'Metri quadri', 'metri'),
             campo('date', 'Una sera per il sopralluogo', 'sera'),
             campo('select', 'A che punto sei', 'punto', opzioni='Sto progettando\nIl cantiere è aperto\nÈ finito ma la luce non va'),
             campo('textarea', 'Raccontaci', 'messaggio', obbligatorio=True, larghezza='1-1',
                   segnaposto='Cosa non ti piace della luce di adesso, o cosa sogni.')]
    out.append(t.section([
        t.row([t.col([sechead(t, 'Il sopralluogo', ['Veniamo a vederlo', 'di sera.'], size=72, layout='stack', size_mobile=42),
                      text(t, '<p>Ti rispondiamo entro due giorni con una data. A Milano il sopralluogo è gratuito, fuori '
                              'Milano costa il viaggio.</p>'),
                      contacts(t)], wm='2-5', ws='1-1'),
               t.col([modulo(t, campi, 'Penombra — un sopralluogo', 'Manda la richiesta',
                             'Ricevuta. Entro due giorni ti scriviamo con una sera per il sopralluogo.')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(SALA), padding='large', html_id='sopralluogo', style={'padding_top': 130, 'padding_bottom': 130}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '15', 'height': '440', 'marker': True,
                                 'marker_popup': 'Penombra · via Tortona 31', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'dark', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Dove', ['In via Tortona,', 'in fondo al cortile.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Dieci minuti a piedi dalla stazione di Porta Genova. Suona «Penombra»: la sala delle prove '
                              'è l’ultima porta a destra.</p>')], wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], padding='large', html_id='dove'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404 · 0 lux', color=LUCE, align='center'),
                     headline(t, 'Qui è <em>tutto spento.</em>', size=120, tag='h1', align='center', pad_bottom=20, spacing=-3),
                     text(t, '<p>Questa pagina non c’è, o qualcuno ha staccato la corrente.</p>', size=17, align='center',
                          color=SOFT),
                     button(t, 'Riaccendi', HOME, kind='luce', align='center')]),
    ], bg=video_bg(V_FASCI, overlay=45), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Casa e design', 'zone': 'La luce la porti tu',
    'tags': ['lighting design', 'luce', 'interni', 'ristoranti', 'hotel', 'musei', 'milano', 'scuro'],
    'description': 'Uno studio di lighting design a Milano, scuro e caldo come una sala alla sera: nell’apertura la luce la porta '
                   'chi guarda e segue il puntatore, lo stesso tavolo spento e acceso da trascinare, i quattro mondi che si '
                   'svelano di lato, la lampadina che si accende davvero, i progetti col monitor, il metodo e il sopralluogo.',
    'menu': {'name': 'Penombra — menu', 'items': [
        {'title': 'Progetti', 'url': PROGETTI}, {'title': 'Il metodo', 'url': METODO}, {'title': 'Lo studio', 'url': STUDIO},
        {'title': 'Sopralluogo', 'url': STUDIO + '#sopralluogo'}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Penombra — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Penombra — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Penombra — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Penombra — Home'},
        'progetti': {'file': 'progetti.json', 'type': 'page', 'title': 'Penombra — I progetti'},
        'metodo': {'file': 'metodo.json', 'type': 'page', 'title': 'Penombra — Il metodo'},
        'studio': {'file': 'studio.json', 'type': 'page', 'title': 'Penombra — Lo studio'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'progetti': {'title': 'I progetti', 'template': 'progetti', 'slug': 'progetti'},
        'metodo': {'title': 'Il metodo', 'template': 'metodo', 'slug': 'metodo'},
        'studio': {'title': 'Lo studio', 'template': 'studio', 'slug': 'studio'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 30, 'ring_color': HEX['light'], 'ring_width': 1,
               'dot_size': 5, 'dot_color': HEX['primary'], 'hot_scale': 2.0, 'hot_color': HEX['primary'],
               'hot_fill': 'rgba(240,178,90,0.12)', 'blend_mode': 'normal', 'magnetic_selector': 'a, button',
               'pull_strength': 0.3, 'follow_easing': 0.2},
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Instrument Serif\', Georgia, \'Times New Roman\', serif',
            'font_family_mono': '\'IBM Plex Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '400',
            'heading_line_height': '1.0', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '5.5rem', 'font_size_h2': '3.8rem', 'font_size_h3': '2.1rem',
            'font_size_h4': '1.5rem', 'font_size_h5': '1.15rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3rem',
            'font_size_h2_mobile': '2.3rem', 'font_size_h3_mobile': '1.6rem',
        },
        'google_fonts': ['Instrument Serif', 'Manrope', 'IBM Plex Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': ['Instrument Serif'],
        'buttons': {'font_size': '11px', 'font_weight': '600', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '2px', 'hover_brightness': '100'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': 'transparent', 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '0px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '400', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 5, 'scale': 170},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'progetti.json': build_progetti(), 'metodo.json': build_metodo(), 'studio.json': build_studio(),
              '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
