# -*- coding: utf-8 -*-
"""Genera il tema «Tratteggio» (id tratteggio): una restauratrice di dipinti e affreschi a Firenze. Chiaro e classico.

Carta di gesso, inchiostro, il blu oltremare del lapislazzuli e la terra di Siena; l'oro solo per i filetti. Titoli nel
Cormorant Garamond (anche in corsivo), testo in Work Sans, schede e misure in Spline Sans Mono. Angoli vivi, filetti
sottili, grana leggera.

Il banner è un soffitto affrescato da pulire: la colonna destra dell'apertura (Hero Split, pannello «Prima e dopo»)
mostra il dipinto com'era e com'è, la maniglia segue il mouse o si trascina, e al primo sguardo passa da sola come una
pulitura. Poi cosa restauriamo, tre cantieri prima e dopo, l'opera che torna al museo in video, i restauri con le
schede, la marina sotto la luce ultravioletta, il metodo in sei passi, lo studio e la richiesta di valutazione.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'tratteggio'
NAME = 'Tratteggio'

HEX = {
    'primary': '#2B4C9B', 'primary_contrast': '#F7F3EC', 'secondary': '#9A4A2E', 'secondary_contrast': '#F7F3EC',
    'accent': '#A9812F', 'dark': '#1B1815', 'light': '#FBF8F2', 'muted': '#EAE3D6', 'muted_contrast': '#231F1B',
    'text': '#231F1B', 'text_muted': '#665D53', 'background': '#F4EFE6', 'border': '#D8CEBD', 'link': '#2B4C9B',
}

INK = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
LAPIS = 'var(--olo-color-primary)'
ON_LAPIS = 'var(--olo-color-primary-contrast)'
SIENA = 'var(--olo-color-secondary)'
ORO = 'var(--olo-color-accent)'
CARTA = 'var(--olo-color-background)'
GESSO = 'var(--olo-color-muted)'
CHIARO = 'var(--olo-color-light)'
NERO = 'var(--olo-color-dark)'
FILO = 'var(--olo-color-border)'
MONO = 'var(--olo-font-family-mono)'
SQ = radius(0)

ADDRESS = 'Via Maggio 12, 50125 Firenze'
PHONE = '055 000 1504'
PHONE_TEL = 'tel:+390550001504'
MAIL = 'studio@tratteggio.example'
LAT, LNG = '43.7664', '11.2489'
HOME = '{{PAGINA:home}}'
RESTAURI = '{{PAGINA:restauri}}'
METODO = '{{PAGINA:metodo}}'
STUDIO = '{{PAGINA:studio}}'

V_MUSEO = 'pexels-video-36830517'      # una galleria di dipinti al buio, le opere accese
V_ACQUERELLO = 'pexels-video-5223087'  # un pennello sottile che stende l'acquerello
V_SPATOLA = 'pexels-video-6931940'     # la spatola sul colore, da vicino
V_SCRIGNO = 'pexels-video-18366463'    # una mano che pulisce un cofanetto intagliato
V_BOTTEGA = 'pexels-video-30926127'    # un dipinto sul cavalletto in mezzo ai colori

PHOTOS = [
    'pexels-35709064-v.jpg', 'pexels-35709064-v-prima.jpg', 'pexels-35708934.jpg', 'pexels-35708934-prima.jpg',
    'pexels-30053470.jpg', 'pexels-30053470-prima.jpg', 'pexels-37167197.jpg', 'pexels-37167197-prima.jpg',
    'pexels-4588842-quadro.jpg', 'pexels-4588842-quadro-prima.jpg', 'pexels-4588842-quadro-uv.jpg',
    'pexels-35709064.jpg', 'pexels-10854927.jpg', 'pexels-30489691.jpg', 'pexels-16960921.jpg', 'pexels-19036548.jpg',
    'pexels-35431620.jpg', 'pexels-35624251.jpg', 'pexels-35431622.jpg', 'pexels-33656463.jpg', 'pexels-33656462.jpg',
    'pexels-35709057.jpg', 'pexels-40432125.jpg', 'pexels-33150977.jpg', 'pexels-39518607.jpg', 'pexels-5682071.jpg',
    'pexels-7885473.jpg', 'pexels-5682062.jpg', 'pexels-26184267.jpg', 'pexels-39264545.jpg', 'pexels-6607763.jpg',
    'pexels-21077237.jpg', 'pexels-31280796.jpg', 'pexels-29013584.jpg', 'pexels-6184426.jpg', 'pexels-19143009.jpg',
    'pexels-34779834.jpg', 'pexels-34000181.jpg', 'pexels-31397075.jpg', 'pexels-15822401.jpg', 'pexels-12270588.jpg',
    'pexels-5393550.jpg', 'pexels-20450835.jpg', 'pexels-3724836.jpg', 'pexels-30489756.jpg', 'pexels-17305980.jpg',
    'pexels-15453433.jpg', 'pexels-16438621.jpg',
]
VIDEOS = [V_MUSEO, V_ACQUERELLO, V_SPATOLA, V_SCRIGNO, V_BOTTEGA]
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


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=40, chiaro=False):
    base_c = CHIARO if chiaro else INK
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': CHIARO if chiaro else SIENA,
        'eyebrow_dot_color': ORO, 'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11,
        'eyebrow_font_weight': '400',
        'headline_lines': [{'text': l, 'color': base_c if i % 2 == 0 else (CHIARO if chiaro else LAPIS), 'italic': i % 2 == 1}
                           for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.0,
        'headline_font_weight': '500', 'headline_align': align,
        'headline_font_size_tablet': max(38, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False,
        'tagline_text_color': mix(CHIARO, 85) if chiaro else TENUE,
        'tagline_text_size': 16, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=INK, tag='h3', pad_bottom=10, align='left', weight='500', spacing=None, sizes=None,
             accento=LAPIS):
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': True, 'accent_color': accento})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if sizes:
        o.update({'heading_font_size_tablet': str(sizes[0]), 'heading_font_size_mobile': str(sizes[1])})
    elif size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(28, int(size * 0.58)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=SIENA, pad_bottom=14, align='left', pad_top=0):
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 11, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '1.5'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=16, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='filo', align='left', pad_top=14, chiaro=False):
    if kind == 'lapis':
        o = {'bg_color': LAPIS, 'text_color': ON_LAPIS, 'hover_bg_color': INK, 'hover_text_color': CHIARO}
    else:
        c = CHIARO if chiaro else INK
        o = {'bg_color': 'transparent', 'text_color': c, 'hover_bg_color': c, 'hover_text_color': NERO if chiaro else CHIARO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(c, 50), 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 12, 'font_weight': '500',
              'text_transform': 'uppercase', 'letter_spacing': '2'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='16/9', position='center center', drift=False, lightbox=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -20, 'position': 0}, {'value': 20, 'position': 100}]}} if drift else None)


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs, chiaro=False):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'vertical', 'term_color': mix(CHIARO, 78) if chiaro else TENUE, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': CHIARO if chiaro else INK, 'definition_font_size': '16', 'separator': True,
        'border_color': mix(CHIARO, 25) if chiaro else FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': STUDIO + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': LAPIS, 'icon_size': '16', 'text_color': INK, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': LAPIS, 'header_font_size': 24, 'header_font_weight': '500',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': LAPIS, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def confronto(t, prima, dopo, lab_prima, lab_dopo, ratio='3/2', start=50, auto=False):
    return t.classic('imgcompare', {
        'preset': 'custom', 'before_image': img(prima), 'after_image': img(dopo), 'before_label': lab_prima,
        'after_label': lab_dopo, 'show_labels': True, 'start_position': start, 'orientation': 'horizontal',
        'handle_color': CHIARO, 'handle_size': 46, 'handle_border': '2', 'line_width': 2, 'height': '',
        'aspect_ratio': 'custom', 'aspect_ratio_custom': ratio, 'border_radius': 0, 'object_fit': 'cover',
        'object_position': 'center center', 'card_shadow': 'none', 'autoplay': auto, 'autoplay_delay': '2',
        'autoplay_speed': '3',
    })


def restauri(t, voci, colonne=3, ratio='4/5'):
    return t.classic('beforeafter', {
        'items': [{'before_image': img(p), 'after_image': img(d), 'before_label': 'Prima', 'after_label': 'Dopo',
                   'title': ti, 'text': tx} for p, d, ti, tx in voci],
        'mode': 'slider', 'columns': colonne, 'gap': 24, 'media_bg': '', 'media_aspect': ratio, 'media_fit': 'cover',
        'object_position': 'center center', 'accent': LAPIS, 'before_label_color': CHIARO, 'after_label_color': ON_LAPIS,
        'title_color': INK, 'text_color': TENUE, 'card_bg': CHIARO, 'radius': 0, 'cap_padding': {'top': 18, 'right': 20, 'bottom': 22, 'left': 20},
        'card_radius': SQ, 'label_radius': SQ, 'shadow': 'none', 'handle_color': CHIARO, 'autoplay': False,
        'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': FILO},
    })


def intro(t, eyebrow, lines, body, size=96):
    return t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=46)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=17)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], padding='large', style={'padding_top': 200, 'padding_bottom': 64})


def fascia(t, foto, alt, ratio='21/8', pos='center center'):
    return t.section([t.one([image(t, foto, alt, ratio=ratio, position=pos)], anim='fade')], padding='remove-vertical',
                     width='large')


def modulo(t, campi, oggetto, invio, grazie):
    return t.classic('form', {
        'fields': campi, 'email_subject': oggetto, 'submit_text': invio, 'submit_icon': '', 'success_message': grazie,
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': INK, 'label_size': '11', 'label_weight': '500',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': CHIARO, 'input_color': INK,
        'input_border_color': FILO, 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 0,
        'input_focus_border': LAPIS, 'input_focus_shadow': False, 'gap': '20', 'submit_bg': LAPIS, 'submit_color': ON_LAPIS,
        'submit_radius': 0, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '12',
        'submit_font_weight': '500', 'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2',
        'submit_hover_bg': INK, 'privacy_checkbox': True,
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
        'menu_id': 'auto', 'logo_text': 'Tratteggio', 'logo_text_size': '26', 'logo_text_color': INK,
        'logo_text_font': 'heading', 'logo_text_weight': '500', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': CARTA, 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': INK, 'hover_color': LAPIS, 'active_color': LAPIS,
        'font_size': '12', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '32',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': LAPIS,
        'button_mode': 'last', 'btn_bg': LAPIS, 'btn_color': ON_LAPIS, 'btn_hover_color': ON_LAPIS,
        'btn_radius': SQ, 'btn_padding': {'top': 11, 'right': 20, 'bottom': 11, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '500', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': CARTA, 'mobile_text_color': INK, 'mobile_accent_color': LAPIS,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '42', 'mobile_link_size': '42',
        'mobile_link_font': 'heading', 'mobile_link_weight': '500',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': FILO},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'Tratteggio', size=150, tag='p', pad_bottom=0, weight='500', spacing=-3, sizes=(96, 56), color=CHIARO)
    claim = text(t, '<p>Irene Valsecchi, restauratrice di beni culturali. Dipinti su tela e su tavola, affreschi, cornici '
                    'dorate: dal 2000 in Oltrarno, per musei, parrocchie e collezioni private.</p>', size=15,
                 color=mix(CHIARO, 80), pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': CHIARO, 'link_hover_color': mix(LAPIS, 40, CHIARO), 'active_color': CHIARO,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('I restauri', RESTAURI), ('Il metodo', METODO), ('Lo studio', STUDIO), ('Una valutazione', STUDIO + '#valutazione'),
                  ('Privacy', '/privacy-policy/')])
    orari = text(t, '<p>Lo studio è aperto dal lunedì al venerdì, 9 — 18. Le opere si vedono su appuntamento, anche nei '
                    'cantieri aperti.</p><p>' + ADDRESS + '<br>' + PHONE + '<br>' + MAIL + '</p>', size=15, color=CHIARO)
    legal = text(t, '<p>© 2026 Tratteggio di Irene Valsecchi · Restauratrice di beni culturali iscritta nell’elenco del '
                    'Ministero · Firenze</p>', size=12, color=mix(CHIARO, 70))
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': mix(CHIARO, 75), 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': LAPIS, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine', color=mix(CHIARO, 70)), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'Lo studio', color=mix(CHIARO, 70)), orari], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': mix(CHIARO, 18)},
                          'padding_top': 22, 'margin_top': 24})
    return [t.section([top, t.one([brand], anim='fade', style={'margin_top': 64}), bottom], bg=solid(NERO), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def build_home():
    t = Tpl(ID, 'home')
    out = []
    hero = t.classic('hero-split', {
        'preset': 'custom', 'eyebrow_text': 'Restauro di dipinti e affreschi · Firenze', 'eyebrow_dot_color': ORO,
        'eyebrow_color': SIENA,
        'headline_lines': [{'text': 'Il colore', 'color': INK, 'italic': False},
                           {'text': 'che c’era', 'color': LAPIS, 'italic': True},
                           {'text': 'sotto.', 'color': INK, 'italic': False}],
        'headline_font_family': 'heading', 'headline_font_size': 112, 'headline_line_height': 0.95, 'headline_font_weight': 500,
        'headline_align': 'left',
        'subhead': 'Sposta la maniglia sul soffitto: a sinistra com’era, a destra dopo otto mesi di lavoro. Togliamo solo quello '
                   'che il tempo ha aggiunto, e lo scriviamo.',
        'subhead_color': TENUE, 'subhead_size': 17, 'subhead_italic': False, 'subhead_max_width': 440, 'subhead_align': 'left',
        'cta1_text': 'I restauri', 'cta1_url': RESTAURI, 'cta1_bg': LAPIS, 'cta1_bg_hover': INK, 'cta1_color': ON_LAPIS,
        'cta1_color_hover': CHIARO, 'cta1_size': 13, 'cta1_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0, 'linked': True},
        'cta1_radius_hover': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0, 'linked': True},
        'cta2_text': 'Chiedi una valutazione', 'cta2_url': STUDIO + '#valutazione', 'cta2_bg': 'transparent', 'cta2_bg_hover': INK,
        'cta2_color': INK, 'cta2_color_hover': CHIARO, 'cta2_border': INK, 'cta2_size': 13,
        'cta2_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0, 'linked': True},
        'cta2_radius_hover': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0, 'linked': True},
        'stats': [{'value': '26', 'value_color': LAPIS, 'label': 'anni di cantieri'},
                  {'value': '312', 'value_color': LAPIS, 'label': 'opere restaurate'},
                  {'value': '4', 'value_color': SIENA, 'label': 'cantieri aperti oggi'}],
        'panel': 'compare',
        'panel_media': {'type': 'image', 'image_url': img('pexels-35709064-v.jpg'), 'image_size': 'cover',
                        'image_position': 'center center'},
        'panel_before': img('pexels-35709064-v-prima.jpg'), 'panel_compare_start': 50, 'panel_compare_follow': True,
        'panel_compare_intro': True, 'panel_label_before': 'Prima', 'panel_label_after': 'Dopo', 'panel_compare_color': CHIARO,
        'panel_media_label': '', 'panel_aspect': '4/5', 'panel_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0},
        'panel_badge_number': '', 'panel_badge_label': '',
        'split_ratio': '1fr 1fr', 'gap': 72, 'tile_padding': {'top': 150, 'right': 40, 'bottom': 90, 'left': 40},
    }, style={})
    out.append(t.section([t.one([hero], anim=None)], width='large', padding='remove-vertical'))

    def cosa(foto, titolo, testo, pos='center center'):
        return [image(t, foto, titolo, ratio='3/4', position=pos),
                label(t, titolo, pad_top=16, pad_bottom=6),
                text(t, '<p>' + testo + '</p>', size=15)]
    out.append(t.section([
        t.one([sechead(t, '01 — Cosa restauriamo', ['Quattro mestieri,', 'una sola mano.'], size=72,
                       tagline='Lavoriamo su opere dal Trecento al Novecento, in studio o sul ponteggio. Ogni intervento è '
                               'reversibile e documentato, foto per foto.')]),
        t.row([t.col(cosa('pexels-16960921.jpg', 'Dipinti su tela', 'Puliture, foderature, tensionamenti, ritocchi ad '
                          'acquerello.'), wm='1-4', ws='1-2', width_default='1-2'),
               t.col(cosa('pexels-19036548.jpg', 'Dipinti su tavola', 'Supporti in legno, parchettature, sollevamenti di '
                          'colore.'), wm='1-4', ws='1-2', width_default='1-2'),
               t.col(cosa('pexels-35431620.jpg', 'Affreschi', 'Consolidamento dell’intonaco, sali, stuccature, integrazioni a '
                          'tratteggio.'), wm='1-4', ws='1-2', width_default='1-2'),
               t.col(cosa('pexels-31397075.jpg', 'Cornici e dorature', 'Doratura a guazzo e a missione, foglia d’oro zecchino.'),
                     wm='1-4', ws='1-2', width_default='1-2')], layout='25-25-25-25', gap=24, valign='start'),
    ], padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 110}))

    out.append(t.section([
        t.one([sechead(t, '02 — Tre cantieri', ['Prima e dopo,', 'da vicino.'], size=72,
                       tagline='Trascina la maniglia su ogni opera. Sono foto prese dallo stesso punto, con la stessa luce: '
                               'quello che cambia l’abbiamo tolto o aggiunto noi.')]),
        t.one([restauri(t, [
            ('pexels-35708934-prima.jpg', 'pexels-35708934.jpg', 'Volta delle Storie · 2024',
             'Sali, nerofumo delle candele e tre ridipinture dell’Ottocento. Undici mesi sul ponteggio.'),
            ('pexels-30053470-prima.jpg', 'pexels-30053470.jpg', 'Cappella del Rosario · 2025',
             'Un velo bianco di sali e due cadute d’intonaco. Consolidato e pulito a impacchi.'),
            ('pexels-37167197-prima.jpg', 'pexels-37167197.jpg', 'Lunetta del Porto · 2023',
             'Muro dipinto a secco del Settecento: fissaggio del colore e integrazione a tratteggio.'),
        ], colonne=3, ratio='4/3')]),
    ], bg=solid(GESSO), padding='large', width='large', style={'padding_top': 140, 'padding_bottom': 140}))

    out.append(t.section([
        centered(t, [label(t, 'Galleria del Palazzo · riallestimento 2026', color=mix(CHIARO, 80), align='center'),
                     headline(t, 'Il restauro migliore <em>non si vede.</em>', size=92, tag='h2', align='center', pad_bottom=18,
                              color=CHIARO, accento=mix(LAPIS, 35, CHIARO)),
                     text(t, '<p>Si vede il dipinto. Le nostre mani restano nella relazione, nelle foto prima e dopo e '
                             'nelle lacune riempite a righe sottili, che da lontano spariscono e da vicino si riconoscono.</p>',
                          size=16, color=mix(CHIARO, 86), align='center')]),
    ], bg=video_bg(V_MUSEO, overlay=50), padding='xlarge', style={'padding_top': 230, 'padding_bottom': 230}))

    out.append(t.section([
        t.row([t.col([label(t, '03 — Il tratteggio'),
                      headline(t, 'Righe sottili, <em>verticali,</em> una accanto all’altra.', size=64, tag='h2', pad_bottom=16),
                      text(t, '<p>È il modo fiorentino di riempire una lacuna: tanti tratti di acquerello che da lontano '
                              'ricompongono l’immagine e da vicino dicono la verità. Si toglie con un po’ d’acqua, quando '
                              'vorrà chi viene dopo di noi.</p>', size=16),
                      button(t, 'Il metodo, passo per passo', METODO, pad_top=18)], wm='1-2', ws='1-1'),
               t.col([image(t, 'pexels-7885473.jpg', 'Una mano col pennello sottile sul dipinto', ratio='4/5', drift=True)],
                     wm='1-2', ws='1-1')], layout='custom', custom='50,50', gap=80, valign='center'),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 140}))

    voce = t.classic('testimonial', {
        'quote': 'Pensavamo che la Madonna avesse il mantello nero. Era azzurro: <em>lo abbiamo rivisto dopo duecento anni, '
                 'tutto il paese in fila.</em>',
        'author_name': 'Don Paolo Meucci', 'author_role': 'Pieve di San Giusto, Mugello · 2025',
        'rating': '5', 'text_color': INK, 'star_color': ORO, 'author_color': TENUE, 'quote_accent_color': LAPIS,
        'quote_font': 'heading', 'quote_size': 44, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': ORO,
    })
    out.append(t.section([centered(t, [voce])], bg=solid(GESSO), padding='large', style={'padding_top': 130, 'padding_bottom': 130}))

    out.append(t.section([
        t.row([t.col([headline(t, 'Hai un’opera <em>da guardare?</em>', size=80, tag='h2', pad_bottom=20),
                      text(t, '<p>Mandaci due foto e le misure. Ti diciamo cosa vediamo, cosa serve e quanto costa, prima di '
                              'toccare qualsiasi cosa. La prima valutazione è gratuita.</p>', size=17),
                      button(t, 'Chiedi una valutazione', STUDIO + '#valutazione', kind='lapis', pad_top=12)], wm='3-5', ws='1-1'),
               t.col([image(t, 'pexels-39264545.jpg', 'Lo studio con la luce da nord, i cavalletti e un busto in gesso',
                            ratio='4/5', drift=True)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 140}))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Tratteggio',
                page_intro_caption='Pulitura in corso', page_intro_bg=HEX['background'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  I RESTAURI
# ══════════════════════════════════════════════════════════════════════════════

def build_restauri():
    t = Tpl(ID, 'restauri')
    out = [intro(t, 'I restauri', ['Trecentododici', 'opere, una per volta.'],
                 '<p>Qui alcune delle ultime, con la scheda di cantiere. Ogni maniglia si trascina: a sinistra com’erano '
                 'quando sono arrivate, a destra come sono tornate.</p>')]

    out.append(t.section([
        t.row([t.col([confronto(t, 'pexels-4588842-quadro-prima.jpg', 'pexels-4588842-quadro.jpg', 'Prima', 'Dopo',
                                ratio='1011/788', start=58)], wm='3-5', ws='1-1'),
               t.col([label(t, 'Olio su tela · seconda metà dell’Ottocento'),
                      headline(t, 'Mare in burrasca', size=56, tag='h2', pad_bottom=12),
                      text(t, '<p>La vernice ingiallita aveva fatto del mare grigio una palude verde. Tolta a tamponi con '
                              'solventi leggeri, strato dopo strato, sotto c’erano il cielo freddo e la schiuma.</p>', size=16),
                      specs(t, [('Committente', 'collezione privata, Fiesole'), ('Intervento', 'pulitura, verniciatura, cornice'),
                                ('Durata', '4 mesi'), ('Anno', '2026')])], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='center'),
    ], padding='large', width='large', style={'padding_top': 40}))

    out.append(fascia(t, 'pexels-35709064.jpg', 'Il soffitto dipinto della sala, con la cornice dorata, visto dal basso',
                      ratio='21/9'))

    out.append(t.section([
        t.row([t.col([label(t, 'Affresco e stucchi dorati · fine del Seicento'),
                      headline(t, 'Sala delle Muse', size=56, tag='h2', pad_bottom=12),
                      text(t, '<p>Un soffitto di diciotto metri, otto mesi sul ponteggio. Il riquadro centrale era annerito '
                              'dal fumo delle candele; l’oro delle cornici, ridipinto con una porporina che era diventata '
                              'verde.</p>', size=16),
                      specs(t, [('Committente', 'Soprintendenza, palazzo privato'), ('Intervento', 'pulitura, consolidamento, '
                                'doratura'), ('Durata', '8 mesi'), ('Anno', '2025')])], wm='2-5', ws='1-1'),
               t.col([confronto(t, 'pexels-35709064-v-prima.jpg', 'pexels-35709064-v.jpg', 'Prima', 'Dopo', ratio='4/5',
                                start=45)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], padding='large', width='large'))

    out.append(t.section([
        t.one([sechead(t, 'Gli affreschi', ['Sul ponteggio,', 'per mesi.'], size=60)]),
        t.one([restauri(t, [
            ('pexels-35708934-prima.jpg', 'pexels-35708934.jpg', 'Volta delle Storie · 2024',
             'Pulitura a impacchi di carbonato d’ammonio, rimozione di tre ridipinture.'),
            ('pexels-30053470-prima.jpg', 'pexels-30053470.jpg', 'Cappella del Rosario · 2025',
             'Estrazione dei sali, consolidamento dell’intonaco con iniezioni di calce.'),
            ('pexels-37167197-prima.jpg', 'pexels-37167197.jpg', 'Lunetta del Porto · 2023',
             'Fissaggio del colore a secco, stuccature e integrazione a tratteggio.'),
        ], colonne=3, ratio='4/3')]),
    ], bg=solid(GESSO), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))

    clienti = t.classic('trust-strip', {
        'items': [{'icon': '', 'icon_color': '', 'text': n, 'badge': ''} for n in
                  ['Soprintendenza di Firenze', 'Opera di Santa Croce', 'Diocesi di Fiesole', 'Museo Bardini',
                   'Fondazione Horne', 'Pieve di San Giusto', 'Collezioni private']],
        'text_color': TENUE, 'text_size': 15, 'font_family': 'body', 'align': 'left', 'flow': 'wrap', 'gap': 26,
        'variant': 'inline', 'icon_size': 16, 'separator_char': '·', 'separator_color': ORO,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.row([t.col([label(t, 'Per chi lavoriamo')], wm='1-4', ws='1-1'), t.col([clienti], wm='3-4', ws='1-1')],
              layout='custom', custom='25,75', gap=32, valign='start'),
    ], padding='large', style={'padding_top': 90, 'padding_bottom': 90}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  IL METODO
# ══════════════════════════════════════════════════════════════════════════════

def build_metodo():
    t = Tpl(ID, 'metodo')
    out = [intro(t, 'Il metodo', ['Prima si guarda,', 'poi si tocca.'],
                 '<p>Sei passi, sempre gli stessi. Il più lungo è il primo: un’opera si studia per settimane prima di '
                 'decidere cosa togliere, e cosa lasciare.</p>')]
    passi = t.classic('process-steps', {
        'items': [{'number': 'I', 'title': 'La diagnosi', 'description': 'Luce radente, ultravioletti, infrarossi, '
                   'microscopio. Capire com’è fatta l’opera e cosa le è successo.'},
                  {'number': 'II', 'title': 'La documentazione', 'description': 'Foto di ogni centimetro, mappe dei danni, la '
                   'relazione per chi verrà dopo.'},
                  {'number': 'III', 'title': 'Il consolidamento', 'description': 'Prima di pulire, fermare quello che si stacca: '
                   'colore, intonaco, legno.'},
                  {'number': 'IV', 'title': 'La pulitura', 'description': 'Vernici ingiallite, fumo, ridipinture. Con solventi e '
                   'gel scelti per ogni strato.'},
                  {'number': 'V', 'title': 'Il tratteggio', 'description': 'Le lacune si riempiono a righe d’acquerello, '
                   'riconoscibili e reversibili.'},
                  {'number': 'VI', 'title': 'La protezione', 'description': 'Una vernice leggera, la cornice, i consigli per '
                   'appenderla e illuminarla.'}],
        'columns': 3, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': SIENA, 'number_bg': '',
        'number_size': 16, 'number_font': 'var(--olo-font-family-heading)', 'number_weight': '500', 'title_color': INK,
        'title_size': 30, 'title_weight': '500', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 15, 'align': 'left',
        'item_gap': 10, 'card_bg': 'transparent',
        'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
        'card_radius': radius(0), 'card_padding': 26, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([t.one([passi])], padding='large', style={'padding_top': 40}))

    out.append(t.section([
        t.row([t.col([label(t, 'La diagnosi · fluorescenza ultravioletta'),
                      headline(t, 'Sotto la luce nera <em>si vede la storia.</em>', size=60, tag='h2', pad_bottom=16),
                      text(t, '<p>Agli ultravioletti la vernice antica s’illumina di verde, i ritocchi fatti dopo restano '
                              'scuri. Trascina sulla marina: a sinistra com’è alla luce del giorno, a destra sotto la lampada '
                              'di Wood. Le macchie scure sono ridipinture del Novecento.</p>', size=16)], wm='2-5', ws='1-1'),
               t.col([confronto(t, 'pexels-4588842-quadro-prima.jpg', 'pexels-4588842-quadro-uv.jpg', 'Luce del giorno',
                                'Ultravioletti', ratio='1011/788', start=50)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=solid(GESSO), padding='large', width='large', style={'padding_top': 130, 'padding_bottom': 130}))

    out.append(t.section([
        t.row([t.col([label(t, 'Il ritocco', color=mix(CHIARO, 80)),
                      headline(t, 'Acquerello, <em>un pelo di martora,</em> pazienza.', size=64, tag='h2', pad_bottom=16,
                               color=CHIARO, accento=mix(LAPIS, 35, CHIARO)),
                      text(t, '<p>Un centimetro quadrato di tratteggio è un’ora di lavoro. Nessun colore a olio, niente che '
                              'non si possa togliere: è la regola dal 1966, dall’alluvione.</p>', size=16, color=mix(CHIARO, 86))],
                     wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_ACQUERELLO, overlay=58), padding='xlarge', style={'padding_top': 230, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-26184267.jpg', 'Una restauratrice al microscopio', ratio='4/3'),
                      label(t, 'Microscopio stereo · fino a 80 ingrandimenti', color=TENUE, pad_top=10, pad_bottom=0)],
                     wm='1-3', ws='1-1'),
               t.col([image(t, 'pexels-33656463.jpg', 'Mani coi guanti che puliscono un’opera su vetro', ratio='4/3'),
                      label(t, 'Pulitura a tampone · un centimetro alla volta', color=TENUE, pad_top=10, pad_bottom=0)],
                     wm='1-3', ws='1-1'),
               t.col([image(t, 'pexels-5682071.jpg', 'Le mani di una restauratrice su un rilievo in stucco', ratio='4/3'),
                      label(t, 'Stucchi · integrazione delle parti mancanti', color=TENUE, pad_top=10, pad_bottom=0)],
                     wm='1-3', ws='1-1')], layout='33-33-33', gap=24, valign='start'),
    ], padding='large', style={'padding_top': 130}))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande', ['Prima', 'di affidarci un’opera.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Se la tua non c’è, scrivici: rispondiamo entro due giorni.</p>'),
                      button(t, 'Scrivici', STUDIO + '#valutazione')], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quanto costa un restauro?', 'Dipende dall’opera e da cosa le serve: una pulitura di un quadro da salotto '
                                                 'parte da 600 €, un affresco si valuta al metro quadro. Il preventivo è '
                                                 'scritto e gratuito.'),
                   ('Serve l’autorizzazione?', 'Per le opere vincolate e per quelle delle chiese sì: prepariamo noi il progetto '
                                               'per la Soprintendenza e seguiamo la pratica.'),
                   ('Venite a prendere l’opera?', 'Sì, con un trasportatore specializzato e l’assicurazione da chiodo a '
                                                  'chiodo. Gli affreschi, ovviamente, li restauriamo dove sono.'),
                   ('Quanto tempo ci vuole?', 'Da un mese per una tela piccola a un anno per una volta affrescata. I tempi '
                                              'sono scritti nel preventivo.'),
               ], 'met')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(GESSO), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LO STUDIO
# ══════════════════════════════════════════════════════════════════════════════

def build_studio():
    t = Tpl(ID, 'studio')
    out = [intro(t, 'Lo studio', ['In via Maggio,', 'luce da nord.'],
                 '<p>Un ex laboratorio di doratori in Oltrarno: tre stanze, finestre a nord, un soffitto alto per le pale '
                 'd’altare. Qui arrivano le tele e le tavole; gli affreschi li raggiungiamo noi.</p>')]

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-5393550.jpg', 'Irene Valsecchi nello studio fra i calchi in gesso', ratio='4/5',
                            position='center 30%')], wm='2-5', ws='1-1'),
               t.col([label(t, 'Irene Valsecchi'),
                      headline(t, 'Restauratrice, <em>dal 2000.</em>', size=60, tag='h2', pad_bottom=16),
                      text(t, '<p>Diplomata alla scuola di alta formazione di Firenze, ha lavorato dieci anni nei cantieri '
                              'delle chiese del centro prima di aprire il suo studio. Insegna restauro dei dipinti murali e '
                              'ogni estate apre un cantiere scuola in Mugello.</p><p>Con lei lavorano Tommaso, che segue le '
                              'tavole e le cornici, e Livia, che fa la diagnostica e le foto.</p>', size=16),
                      specs(t, [('Qualifica', 'restauratrice di beni culturali, elenco del Ministero'),
                                ('Settori', 'dipinti su tela e tavola, dipinti murali, dorature'),
                                ('Cantieri', 'Toscana e Umbria')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 60, 'padding_bottom': 120}))

    out.append(t.section([
        t.row([t.col([label(t, 'Il banco', color=mix(CHIARO, 80)),
                      headline(t, 'Ogni opera <em>ha il suo tempo.</em>', size=72, tag='h2', pad_bottom=16, color=CHIARO,
                               accento=mix(LAPIS, 35, CHIARO)),
                      text(t, '<p>Sul cavalletto ce n’è una sola alla volta. Le altre aspettano coperte, al buio, alla '
                              'temperatura e all’umidità giuste.</p>', size=16, color=mix(CHIARO, 86))], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_BOTTEGA, overlay=66), padding='xlarge', style={'padding_top': 230, 'padding_bottom': 110}))

    campi = [campo('text', 'Nome', 'nome', obbligatorio=True), campo('email', 'Email', 'email', obbligatorio=True),
             campo('tel', 'Telefono', 'telefono'),
             campo('select', 'L’opera', 'opera', obbligatorio=True,
                   opzioni='Un dipinto su tela\nUn dipinto su tavola\nUn affresco o un muro dipinto\nUna cornice dorata\n'
                           'Una scultura o uno stucco\nNon lo so'),
             campo('text', 'Misure', 'misure', segnaposto='Anche a occhio'), campo('text', 'Di che epoca', 'epoca',
                                                                                    segnaposto='Se lo sai'),
             campo('text', 'Dove si trova', 'dove', larghezza='1-1', segnaposto='Città, e se è in una casa, in una chiesa…'),
             campo('textarea', 'Cosa vedi', 'messaggio', obbligatorio=True, larghezza='1-1',
                   segnaposto='Macchie, crepe, colore che si stacca… Le foto le mandi poi per email.')]
    out.append(t.section([
        t.row([t.col([sechead(t, 'Una valutazione', ['Due foto', 'e le misure.'], size=72, layout='stack', size_mobile=42),
                      text(t, '<p>La prima valutazione è gratuita. Ti rispondiamo entro due giorni; se serve, veniamo a '
                              'vedere l’opera dove si trova.</p>'),
                      contacts(t)], wm='2-5', ws='1-1'),
               t.col([modulo(t, campi, 'Tratteggio — una valutazione', 'Manda la richiesta',
                             'Ricevuta. Entro due giorni ti scriviamo: rispondi a quella mail con le foto dell’opera.')],
                     wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(GESSO), padding='large', html_id='valutazione', style={'padding_top': 130, 'padding_bottom': 130}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '16', 'height': '440', 'marker': True,
                                 'marker_popup': 'Tratteggio · via Maggio 12', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'light', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Dove', ['In Oltrarno,', 'fra Pitti e Santo Spirito.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Il portone verde accanto alla bottega del corniciaio. Suona «Tratteggio», secondo piano '
                              'senza ascensore.</p>')], wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], padding='large', html_id='dove'))
    out.append(fascia(t, 'pexels-17305980.jpg', 'Firenze dall’alto con la cupola del Duomo', ratio='21/7'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404 · lacuna', align='center'),
                     headline(t, 'Qui manca <em>un pezzo.</em>', size=120, tag='h1', align='center', pad_bottom=20, spacing=-2),
                     text(t, '<p>Questa pagina non c’è più, e non la integriamo a tratteggio.</p>', size=17, align='center'),
                     button(t, 'Torna all’inizio', HOME, kind='lapis', align='center')]),
    ], padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Creativi e portfolio', 'zone': 'Pulisci il soffitto',
    'tags': ['restauro', 'affreschi', 'dipinti', 'arte', 'firenze', 'prima e dopo', 'classico'],
    'description': 'Una restauratrice di dipinti e affreschi a Firenze, su carta di gesso col blu del lapislazzuli: nell’apertura '
                   'il soffitto affrescato si pulisce passando il mouse, tre cantieri prima e dopo, l’opera che torna al '
                   'museo, la marina sotto gli ultravioletti, il metodo in sei passi e la richiesta di valutazione.',
    'menu': {'name': 'Tratteggio — menu', 'items': [
        {'title': 'Restauri', 'url': RESTAURI}, {'title': 'Il metodo', 'url': METODO}, {'title': 'Lo studio', 'url': STUDIO},
        {'title': 'Valutazione', 'url': STUDIO + '#valutazione'}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Tratteggio — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Tratteggio — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Tratteggio — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Tratteggio — Home'},
        'restauri': {'file': 'restauri.json', 'type': 'page', 'title': 'Tratteggio — I restauri'},
        'metodo': {'file': 'metodo.json', 'type': 'page', 'title': 'Tratteggio — Il metodo'},
        'studio': {'file': 'studio.json', 'type': 'page', 'title': 'Tratteggio — Lo studio'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'restauri': {'title': 'I restauri', 'template': 'restauri', 'slug': 'restauri'},
        'metodo': {'title': 'Il metodo', 'template': 'metodo', 'slug': 'metodo'},
        'studio': {'title': 'Lo studio', 'template': 'studio', 'slug': 'studio'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 30, 'ring_color': HEX['text'], 'ring_width': 1,
               'dot_size': 5, 'dot_color': HEX['primary'], 'hot_scale': 2.0, 'hot_color': HEX['primary'],
               'hot_fill': 'rgba(43,76,155,0.10)', 'blend_mode': 'normal', 'magnetic_selector': 'a, button',
               'pull_strength': 0.25, 'follow_easing': 0.18},
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Cormorant Garamond\', Garamond, Georgia, serif',
            'font_family_mono': '\'Spline Sans Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '500',
            'heading_line_height': '1.0', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '5.5rem', 'font_size_h2': '3.8rem', 'font_size_h3': '2.1rem',
            'font_size_h4': '1.5rem', 'font_size_h5': '1.15rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3rem',
            'font_size_h2_mobile': '2.3rem', 'font_size_h3_mobile': '1.6rem',
        },
        'google_fonts': ['Cormorant Garamond', 'Work Sans', 'Spline Sans Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': ['Cormorant Garamond'],
        'buttons': {'font_size': '12px', 'font_weight': '500', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '2px', 'hover_brightness': '100'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': HEX['light'], 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '0px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '500', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 4, 'scale': 180},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'restauri.json': build_restauri(), 'metodo.json': build_metodo(), 'studio.json': build_studio(),
              '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
