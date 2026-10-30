# -*- coding: utf-8 -*-
"""Genera il tema «Fuoco Lento» (id fuoco-lento): uno chef privato fra le Langhe e Torino. Scuro e caldo.

Nero di brace, crema, il color brace per gli accenti e l'oro per i prezzi. Titoli nel Fraunces (anche in corsivo),
testo in Jost, quantità e prezzi in DM Mono. Angoli appena smussati, fili sottili, grana.

Il banner è un flambé nel buio della cucina, e il puntatore lascia una scia di braci che si spengono (Effetti mouse →
Pennellata, colori caldi, fusione «screen»). Poi come funziona, la cena da comporre col prezzo a persona che finisce
nel modulo della prenotazione (Configuratore), l'impiattamento in video, i piatti, le stagioni da sfogliare
(Switcher), le cene in quattro pannelli che si svelano di lato, lo chef e la prenotazione della data.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'fuoco-lento'
NAME = 'Fuoco Lento'

HEX = {
    'primary': '#E8773F', 'primary_contrast': '#140E0A', 'secondary': '#7D8B66', 'secondary_contrast': '#140E0A',
    'accent': '#D9A65A', 'dark': '#0A0705', 'light': '#F4ECE2', 'muted': '#1A140F', 'muted_contrast': '#F2E8DC',
    'text': '#F2E8DC', 'text_muted': '#A8998A', 'background': '#100C09', 'border': '#30261E', 'link': '#E8773F',
}

CREMA = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
BRACE = 'var(--olo-color-primary)'
ON_BRACE = 'var(--olo-color-primary-contrast)'
ORO = 'var(--olo-color-accent)'
SALVIA = 'var(--olo-color-secondary)'
FONDO = 'var(--olo-color-background)'
SALA = 'var(--olo-color-muted)'
NERO = 'var(--olo-color-dark)'
FILO = 'var(--olo-color-border)'
SOFT = mix(CREMA, 82)
MONO = 'var(--olo-font-family-mono)'
R = radius(4)

CITTA = 'Alba (CN) · cucino in tutto il Piemonte'
PHONE = '347 000 4141'
PHONE_TEL = 'tel:+393470004141'
MAIL = 'elia@fuocolento.example'
HOME = '{{PAGINA:home}}'
MENU = '{{PAGINA:menu}}'
CENE = '{{PAGINA:cene}}'
CHEF = '{{PAGINA:chi-sono}}'
PRENOTA = '{{PAGINA:prenota}}'

V_FLAMBE = 'pexels-video-20131338'     # un flambé nella padella, la fiamma alta nel buio
V_FUOCO = 'pexels-video-4315396'       # fiamme vive su fondo nero
V_BRACI = 'pexels-video-12605103'      # braci che respirano nel buio
V_IMPIATTA = 'pexels-video-4555805'    # uno chef impiatta di sera, la sala dietro
V_PERA = 'pexels-video-39877520'       # la pera cotta nel vino, i fiori messi a mano
V_TARTUFO = 'pexels-video-19905874'    # il tartufo lamellato sul piatto
V_CHEF = 'pexels-video-39498949'       # lo chef in cucina, poi il piatto finito
V_VINO = 'pexels-video-19826636'       # il vino versato nel bicchiere, dall'alto

PHOTOS = [
    'pexels-24186303.jpg', 'pexels-8112966.jpg', 'pexels-29259654.jpg', 'pexels-28616665.jpg', 'pexels-32149254.jpg',
    'pexels-19396435.jpg', 'pexels-35546720.jpg', 'pexels-5317188.jpg', 'pexels-24246105.jpg', 'pexels-27582699.jpg',
    'pexels-34342501.jpg', 'pexels-5545261.jpg', 'pexels-28052915.jpg', 'pexels-3938727.jpg', 'pexels-19986452.jpg',
    'pexels-6657697.jpg', 'pexels-5473349.jpg', 'pexels-3937411.jpg', 'pexels-6232566.jpg', 'pexels-30874866.jpg',
    'pexels-20509791.jpg', 'pexels-8112385.jpg', 'pexels-8093384.jpg', 'pexels-8093910.jpg', 'pexels-34361144.jpg',
    'pexels-3027046.jpg', 'pexels-15957984.jpg', 'pexels-5701918.jpg', 'pexels-4699966.jpg', 'pexels-30521760.jpg',
    'pexels-18498382.jpg', 'pexels-34552002.jpg', 'pexels-29380188.jpg', 'pexels-34535224.jpg', 'pexels-36740867.jpg',
    'pexels-3937888.jpg', 'pexels-6288729.jpg', 'pexels-6232536.jpg', 'pexels-6232561.jpg', 'pexels-15170729.jpg',
    'pexels-31094805.jpg', 'pexels-30247097.jpg', 'pexels-7353717.jpg', 'pexels-6303015.jpg', 'pexels-7782057.jpg',
]
VIDEOS = [V_FLAMBE, V_FUOCO, V_BRACI, V_IMPIATTA, V_PERA, V_TARTUFO, V_CHEF, V_VINO]
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


def braci(size=70, life=1500, blur=14):
    """La scia di braci dietro al puntatore (Avanzate → Effetti mouse → Pennellata)."""
    return {'cursor_smear': True, 'cursor_smear_colors': 'caldi', 'cursor_smear_blend': 'screen',
            'cursor_smear_size': size, 'cursor_smear_life': life, 'cursor_smear_blur': blur}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=40):
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': BRACE, 'eyebrow_dot_color': BRACE,
        'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '400',
        'headline_lines': [{'text': l, 'color': CREMA if i % 2 == 0 else BRACE, 'italic': i % 2 == 1} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.02,
        'headline_font_weight': '300', 'headline_align': align,
        'headline_font_size_tablet': max(38, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 16, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=CREMA, tag='h3', pad_bottom=10, align='left', weight='300', spacing=None, sizes=None):
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': True, 'accent_color': BRACE})
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
    if kind == 'brace':
        o = {'bg_color': BRACE, 'text_color': ON_BRACE, 'hover_bg_color': CREMA, 'hover_text_color': NERO}
    else:
        o = {'bg_color': 'transparent', 'text_color': CREMA, 'hover_bg_color': CREMA, 'hover_text_color': NERO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(CREMA, 45), 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 4, 'font_size': 12, 'font_weight': '500',
              'text_transform': 'uppercase', 'letter_spacing': '2'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='16/9', position='center center', drift=False, lightbox=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': R, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -24, 'position': 0}, {'value': 24, 'position': 100}]}} if drift else None)


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'vertical', 'term_color': TENUE, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': CREMA, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': CITTA, 'link': PRENOTA, 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': BRACE, 'icon_size': '16', 'text_color': CREMA, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': CREMA, 'header_text_color_active': BRACE, 'header_font_size': 22, 'header_font_weight': '300',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': BRACE, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, lines, body, foto, size=100, pos='center center', overlay=68):
    sec = t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=46)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=17, color=SOFT)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='large', style={'padding_top': 210, 'padding_bottom': 76})
    sec['advanced'].update(braci(size=56, life=1200))
    return sec


def pannello(t, foto, eyebrow, titolo, testo, link_t, link_u, overlay=56, pos='center center'):
    blocco = [label(t, eyebrow, color=BRACE),
              headline(t, titolo, size=80, tag='h2', pad_bottom=16),
              text(t, '<p>' + testo + '</p>', size=16, color=SOFT)]
    if link_t:
        blocco.append(button(t, link_t, link_u, pad_top=10))
    return t.section([
        t.row([t.col(blocco, wm='1-2', ws='1-1'), t.col([], wm='1-2', ws='1-1')], layout='custom', custom='52,48',
              valign='end', anim=None),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='large', width='large', sticky_effect='reveal-h',
        sticky_h_align='bottom', style={'padding_top': 140, 'padding_bottom': 80})


def piatto(t, foto, nome, desc, stagione, pos='center center'):
    return [image(t, foto, nome, ratio='4/5', position=pos),
            label(t, stagione, color=BRACE, pad_top=16, pad_bottom=6),
            headline(t, nome, size=28, tag='h3', pad_bottom=6),
            text(t, '<p>' + desc + '</p>', size=15)]


def modulo(t, campi, oggetto, invio, grazie):
    return t.classic('form', {
        'fields': campi, 'email_subject': oggetto, 'submit_text': invio, 'submit_icon': '', 'success_message': grazie,
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': CREMA, 'label_size': '11', 'label_weight': '400',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': 'transparent', 'input_color': CREMA,
        'input_border_color': mix(CREMA, 30), 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 4,
        'input_focus_border': BRACE, 'input_focus_shadow': False, 'gap': '20', 'submit_bg': BRACE, 'submit_color': ON_BRACE,
        'submit_radius': 4, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '12',
        'submit_font_weight': '500', 'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2',
        'submit_hover_bg': CREMA, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})


def campo(tipo, etichetta, nome, obbligatorio=False, larghezza='1-2', opzioni='', segnaposto='', param=''):
    c = {'field_type': tipo, 'label': etichetta, 'name': nome, 'placeholder': segnaposto, 'required': obbligatorio,
         'width': larghezza, 'options': opzioni, 'icon': ''}
    if param:
        c['param'] = param
    return c


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER · FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': 'Fuoco Lento', 'logo_text_size': '22', 'logo_text_color': CREMA,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': FONDO, 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': CREMA, 'hover_color': BRACE, 'active_color': BRACE,
        'font_size': '12', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '32',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': BRACE,
        'button_mode': 'last', 'btn_bg': BRACE, 'btn_color': ON_BRACE, 'btn_hover_color': ON_BRACE,
        'btn_radius': R, 'btn_padding': {'top': 11, 'right': 20, 'bottom': 11, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '500', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': NERO, 'mobile_text_color': CREMA, 'mobile_accent_color': BRACE,
        'hamburger_style': 'minimal', 'hamburger_color': CREMA, 'mobile_font_size': '40', 'mobile_link_size': '40',
        'mobile_link_font': 'heading', 'mobile_link_weight': '300',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(CREMA, 10)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'Fuoco Lento', size=140, tag='p', pad_bottom=0, weight='300', spacing=-3, sizes=(90, 50))
    claim = text(t, '<p>Elia Corradi, chef privato. Cucino a casa tua per due persone o per ventiquattro, con quello che '
                    'trovo al mercato la mattina e il fuoco che serve.</p>', size=15, pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': CREMA, 'link_hover_color': BRACE, 'active_color': BRACE,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('Il menu di stagione', MENU), ('Cene ed eventi', CENE), ('Chi sono', CHEF), ('Prenota la data', PRENOTA),
                  ('Privacy', '/privacy-policy/')])
    quando = text(t, '<p>Cene dal martedì alla domenica, pranzi il sabato e la domenica. Ottobre e novembre, il mese del '
                     'tartufo: prenota con un mese d’anticipo.</p>', size=15, color=CREMA)
    legal = text(t, '<p>© 2026 Fuoco Lento di Elia Corradi · Alba · P.IVA 00000000000</p>', size=12)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': TENUE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': NERO, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim, contacts(t)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine', color=BRACE), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'Quando', color=BRACE), quando], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
                          'padding_top': 22, 'margin_top': 24})
    return [t.section([top, t.one([brand], anim='fade', style={'margin_top': 64}), bottom], bg=solid(NERO), padding='large',
                      style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}})]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def opz(gruppo, id_, nome, prezzo=0, descrizione='', predefinita=False, badge=''):
    return {'id': id_, 'gruppo': gruppo, 'nome': nome, 'descrizione': descrizione, 'prezzo': prezzo, 'immagine': '',
            'badge': badge, 'predefinita': predefinita, 'punti': '', 'min': 0, 'max': 10}


def configuratore(t):
    return t.classic('configuratore', {
        'gruppi': [
            {'id': 'menu', 'titolo': 'Il menu', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': 'I piatti cambiano con la stagione.'},
            {'id': 'vini', 'titolo': 'I vini', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': 'Da piccoli produttori di Langa e Roero.'},
            {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''},
        ],
        'opzioni': [
            opz('menu', 'm-tradizione', 'La tradizione', 75, 'Quattro portate: battuta, tajarin, brasato, bonet', True),
            opz('menu', 'm-brace', 'Sulla brace', 95, 'Cinque portate cotte sul fuoco che accendo in giardino', badge='d’estate'),
            opz('menu', 'm-degustazione', 'Degustazione', 120, 'Sette portate, il menu della stagione', badge='il più scelto'),
            opz('menu', 'm-tartufo', 'Il tartufo bianco', 190, 'Sei portate col tartufo d’Alba, ottobre e novembre'),
            opz('vini', 'v-no', 'Senza vini', 0, 'Li scegliete voi, li apro e li servo io', True),
            opz('vini', 'v-tre', 'Tre calici', 35, 'Un bianco, un rosso, un dolce'),
            opz('vini', 'v-ogni', 'Un calice per piatto', 60, 'L’abbinamento completo, Barolo compreso'),
            opz('extra', 'x-pane', 'Pane e grissini fatti da me', 6, 'Col lievito madre, la mattina stessa'),
            opz('extra', 'x-sala', 'Un cameriere in sala', 18, 'Per le cene da otto persone in su'),
            opz('extra', 'x-corso', 'Un’ora di cucina prima', 35, 'Facciamo insieme i tajarin'),
            opz('extra', 'x-tavola', 'La tavola apparecchiata', 12, 'Tovaglie di lino, candele, fiori di stagione'),
        ],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'La vostra cena', 'periodo': 'a persona',
        'nota_prezzo': 'Tutto compreso, anche la pulizia',
        'mostra_differenze': True, 'indicatori': [], 'riepilogo': True, 'titolo_riepilogo': 'La vostra cena',
        'testo_azzera': 'Ricomincia', 'testo_pulsante': 'Prenota questa cena', 'link_azione': PRENOTA + '?cena={configurazione}#modulo',
        'campo_modulo': 'messaggio', 'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': SALA,
        'colore_scelta': BRACE, 'colore_testo': CREMA, 'colore_tenue': TENUE, 'colore_prezzo': ORO, 'sfondo_riepilogo': NERO,
        'sfondo_badge': BRACE, 'colore_badge': ON_BRACE, 'raggio_scheda': R, 'ombra': 'none',
        'gruppo_font_family': 'heading', 'gruppo_font_size': 30, 'gruppo_font_weight': '300', 'gruppo_color': CREMA,
        'opzione_font_family': 'body', 'opzione_font_size': 17, 'opzione_font_weight': '500',
        'descrizione_font_family': 'body', 'descrizione_font_size': 14, 'descrizione_font_weight': '400',
        'prezzo_font_family': 'mono', 'prezzo_font_size': 14, 'prezzo_font_weight': '500',
        'totale_font_family': 'heading', 'totale_font_size': 64, 'totale_font_weight': '300', 'totale_color': ORO, 'gap': 12,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def build_home():
    t = Tpl(ID, 'home')
    out = []
    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Chef privato · Langhe, Roero, Torino', 'eyebrow_dot': True,
        'title': 'Una cena<br>a casa tua,<br><em>a fuoco lento.</em>',
        'subtitle': 'Arrivo con la spesa del mattino, cucino nella tua cucina, servo a tavola e lascio tutto in ordine. Passa '
                    'il puntatore, o il dito, sul fuoco: lascia le braci.',
        'media_bg': {'type': 'video', 'video_url': img(V_FLAMBE + '.mp4'), 'video_poster': img(V_FLAMBE + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': NERO, 'overlay_top': 0.55, 'overlay_bottom': 0.9, 'overlay_sides': False, 'overlay_direction': 'left',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': BRACE, 'text_color': CREMA, 'title_color': CREMA, 'subtitle_color': SOFT,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 116, 'title_font_weight': '300',
        'title_line_height': '0.96', 'title_letter_spacing': '-3', 'title_text_transform': 'none',
        'subtitle_font_size': 17, 'subtitle_max_width': 450,
        'min_height': '100vh', 'content_max_width': 1320, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 150, 'right': 44, 'bottom': 84, 'left': 44},
        'cta_text': 'Componi la cena', 'cta_url': '#componi', 'cta_bg_color': BRACE, 'cta_text_color': ON_BRACE,
        'cta_radius': R, 'cta_size': '12', 'cta_style': 'filled', 'cta_font_weight': '500', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2',
        'cta2_text': 'Il menu di stagione', 'cta2_url': MENU, 'cta2_text_color': CREMA, 'cta2_style': 'ghost',
        'meta_text': 'Da 2 a 24 ospiti · da 75 € a persona, tutto compreso', 'scroll_hint': '', 'scene_depth': 'soft', 'scene_depth_fade': True,
        'module': '',
    }, advanced=braci())
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Mi scrivi', 'description': 'Quanti siete, che giorno, cosa non mangiate. Ti '
                   'rispondo in giornata con il menu.'},
                  {'number': '02', 'title': 'Vado al mercato', 'description': 'La mattina della cena: verdure di Bra, carne di '
                   'Carrù, formaggi delle valli. Porto io pentole e coltelli.'},
                  {'number': '03', 'title': 'Cucino da te', 'description': 'Arrivo tre ore prima, servo a tavola e racconto i '
                   'piatti. Quando vado via, la cucina è più pulita di prima.'}],
        'columns': 3, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': BRACE, 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': CREMA, 'title_size': 30,
        'title_weight': '300', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 15, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
        'card_radius': radius(0), 'card_padding': 24, 'columns_tablet': 3, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, '01 — Come funziona', ['Tu apparecchi,', 'io accendo il fuoco.'], size=84,
                       tagline='Tre passi, e la sera della cena hai solo da sederti. Niente sala da prenotare, niente '
                               'parcheggio: il ristorante, per una sera, è casa tua.')]),
        t.one([passi]),
    ], padding='large', width='large', style={'padding_top': 150, 'padding_bottom': 110}))

    out.append(t.section([
        t.one([sechead(t, '02 — La tua cena', ['Componila qui,', 'il prezzo è subito vero.'], size=76,
                       tagline='Scegli il menu, i vini e cosa aggiungere: il prezzo a persona si aggiorna, e la cena composta '
                               'arriva già scritta nel modulo della prenotazione.')]),
        t.one([configuratore(t)]),
    ], bg=solid(SALA), padding='large', width='large', html_id='componi', style={'padding_top': 140, 'padding_bottom': 140}))

    out.append(t.section([
        t.row([t.col([label(t, '03 — Il piatto', color=BRACE),
                      headline(t, 'L’ultimo gesto <em>è a tavola.</em>', size=80, tag='h2', pad_bottom=16),
                      text(t, '<p>Impiatto davanti a voi, sul tavolo della vostra cucina. È il momento in cui gli ospiti si '
                              'alzano a guardare: lasciateli fare.</p>', size=16, color=SOFT)], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_IMPIATTA, overlay=62), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 110}))

    out.append(t.section([
        t.one([sechead(t, '04 — In questo momento', ['Tre piatti', 'd’autunno.'], size=64,
                       tagline='Il menu cambia ogni sei settimane, con quello che c’è. Questi li cucino fino a fine novembre.')]),
        t.row([t.col(piatto(t, 'pexels-34342501.jpg', 'Tajarin al nero e gamberi di Sanremo',
                            'Quaranta tuorli per chilo di farina, tagliati a mano il pomeriggio.', 'Primo · ottobre'), wm='1-3', ws='1-1'),
               t.col(piatto(t, 'pexels-19396435.jpg', 'Fassona alla brace, fonduta leggera',
                            'Cotta sulla legna di vite, tagliata al tavolo.', 'Secondo · tutto l’anno'), wm='1-3', ws='1-1'),
               t.col(piatto(t, 'pexels-34535224.jpg', 'Pera madernassa nel Nebbiolo',
                            'Cotta otto ore, con la crema al miele di castagno.', 'Dolce · ottobre e novembre'), wm='1-3', ws='1-1')],
              layout='33-33-33', gap=32, valign='start'),
        t.one([button(t, 'Tutto il menu di stagione', MENU, pad_top=34)]),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 120}))

    voce = t.classic('testimonial', {
        'quote': 'Eravamo in dodici per i sessant’anni di mia madre. Ha cucinato nella nostra cucina minuscola e <em>alle '
                 'undici la nonna voleva la ricetta dei tajarin.</em>',
        'author_name': 'Francesca B.', 'author_role': 'Torino, cena di famiglia · settembre 2026',
        'rating': '5', 'text_color': CREMA, 'star_color': ORO, 'author_color': TENUE, 'quote_accent_color': BRACE,
        'quote_font': 'heading', 'quote_size': 40, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': BRACE,
    })
    out.append(t.section([centered(t, [voce])], bg=solid(SALA), padding='large', style={'padding_top': 130, 'padding_bottom': 130}))

    cta = t.section([
        centered(t, [label(t, 'Le date di novembre vanno via per prime', color=SOFT, align='center'),
                     headline(t, 'Che sera <em>accendiamo?</em>', size=110, tag='h2', align='center', pad_bottom=18),
                     text(t, '<p>Scrivimi giorno, ospiti e città: ti rispondo in giornata con il menu e il prezzo, scritti.</p>',
                          size=16, color=SOFT, align='center'),
                     button(t, 'Prenota la data', PRENOTA, kind='brace', align='center')]),
    ], bg=video_bg(V_BRACI, overlay=30), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})
    cta['advanced'].update(braci(size=60, life=1300))
    out.append(cta)
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Fuoco Lento',
                page_intro_caption='Accendo la brace', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=1800, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  IL MENU DI STAGIONE
# ══════════════════════════════════════════════════════════════════════════════

STAGIONI = [
    ('st-autunno', 'Autunno', 'leaf', 'pexels-34361144.jpg',
     '<p><strong>Ottobre e novembre.</strong></p><p>Battuta di Fassona al coltello, nocciole e tuorlo marinato · Tajarin '
     'ai quaranta tuorli, burro di malga e tartufo nero · Zucca di Castellazzo arrostita sulla brace · Guancia brasata nel '
     'Barolo, polenta di mais otto file · Pera madernassa nel Nebbiolo.</p>'),
    ('st-inverno', 'Inverno', 'snowflake', 'pexels-15957984.jpg',
     '<p><strong>Da dicembre a febbraio.</strong></p><p>Cardo gobbo di Nizza con la fonduta · Agnolotti del plin al sugo '
     'd’arrosto · Cipolla ripiena cotta nella cenere · Bollito misto con le sue salse · Bonet al cacao e amaretti.</p>'),
    ('st-primavera', 'Primavera', 'flower', 'pexels-5701918.jpg',
     '<p><strong>Da marzo a maggio.</strong></p><p>Asparagi di Santena e uovo morbido · Risotto alle erbe dei prati · '
     'Capretto di Roccaverano alla brace · Fragole di Cuneo, panna cotta e pepe lungo.</p>'),
    ('st-estate', 'Estate', 'sun', 'pexels-7782057.jpg',
     '<p><strong>Da giugno a settembre.</strong></p><p>Peperoni di Carmagnola con bagna cauda fredda · Tajarin ai fiori '
     'di zucca · Trota di torrente sulla brace · Pesche di Canale, amaretti e moscato.</p>'),
]


def build_menu():
    t = Tpl(ID, 'menu')
    out = [intro(t, 'Il menu di stagione', ['Si cucina', 'quello che c’è.'],
                 '<p>Quattro stagioni, quattro menu. Le portate cambiano ogni sei settimane: quello che leggi qui è quello '
                 'che c’era al mercato di Bra l’ultima volta.</p>', 'pexels-24186303.jpg', overlay=72)]

    stagioni = t.classic('switcher', {
        'items': [{'id': i, 'title': ti, 'icon': ic, 'content': c, 'image': img(f), 'link_text': 'Prenota questa stagione',
                   'link_url': PRENOTA} for i, ti, ic, f, c in STAGIONI],
        'preset': 'custom', 'nav_style': 'tab', 'animation': 'fade', 'animation_duration': '300', 'vertical': False,
        'tab_padding_y': '10', 'tab_padding_x': '20', 'tab_font_size': 15, 'tab_font_weight': '500', 'tab_gap': 4,
        'tab_radius': 4, 'container_bg': SALA, 'container_padding': 6, 'container_radius': 6,
        'active_bg': BRACE, 'active_color': ON_BRACE, 'inactive_color': SOFT,
    })
    out.append(t.section([
        t.one([sechead(t, 'Le quattro stagioni', ['Sfoglia', 'l’anno.'], size=64, tagline='Tocca una stagione: cambiano '
                       'i piatti e la foto.')]),
        t.one([stagioni]),
    ], padding='large', style={'padding_top': 80}))

    listino = t.classic('pricelist', {
        'items': [{'title': 'La tradizione', 'description': 'Quattro portate del Piemonte, come le faceva mia nonna.',
                   'price': '75 € a persona', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Sulla brace', 'description': 'Cinque portate cotte sul fuoco, in giardino o sul terrazzo.',
                   'price': '95 € a persona', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Degustazione', 'description': 'Sette portate del menu di stagione, con due sorprese.',
                   'price': '120 € a persona', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Il tartufo bianco', 'description': 'Sei portate col tartufo d’Alba, da ottobre a dicembre.',
                   'price': '190 € a persona', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'I vini', 'description': 'Tre calici o un calice per piatto, da piccoli produttori.',
                   'price': 'da 35 €', 'image_url': '', 'highlighted': False, 'badge': ''}],
        'separator_style': 'solid', 'separator_color': FILO, 'title_color': CREMA, 'price_color': ORO,
        'description_color': TENUE, 'image_size': 64, 'image_border_radius': '0', 'show_image': False,
        'price_position': 'right', 'tile_padding': {'top': 18, 'right': 0, 'bottom': 18, 'left': 0}, 'card_bg': 'transparent',
        'card_border_radius': '0',
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'I prezzi', ['Tutto compreso,', 'scritto prima.'], size=52, layout='stack', size_mobile=36),
                      text(t, '<p>Spesa, cucina, servizio e pulizia. Il viaggio in Piemonte è incluso; fuori regione lo '
                              'diciamo nel preventivo.</p>'),
                      button(t, 'Componi la cena', HOME + '#componi', kind='brace')], wm='1-3', ws='1-1'),
               t.col([listino], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(SALA), padding='large'))

    out.append(t.section([
        t.row([t.col([label(t, 'Il dolce', color=BRACE),
                      headline(t, 'La pera cuoce <em>otto ore.</em>', size=76, tag='h2', pad_bottom=16),
                      text(t, '<p>Nel Nebbiolo, con la cannella e la buccia d’arancia. La metto in pentola la sera prima, a '
                              'casa mia: è l’unica cosa che arriva già pronta.</p>', size=16, color=SOFT)], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_PERA, overlay=46), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-15170729.jpg', 'Il vino rosso versato nel calice', ratio='4/5')], wm='2-5', ws='1-1'),
               t.col([label(t, 'Le cantine', color=BRACE),
                      headline(t, 'Vini di chi <em>conosco per nome.</em>', size=60, tag='h2', pad_bottom=16),
                      text(t, '<p>Dodici cantine fra Langa e Roero, quasi tutte sotto i cinquantamila bottiglie. Se avete '
                              'già i vostri, li apro e li servo io, senza costi.</p>', size=16),
                      specs(t, [('Bianchi', 'Arneis, Nascetta, Timorasso'), ('Rossi', 'Dolcetto, Barbera, Nebbiolo, Barolo'),
                                ('Dolci', 'Moscato d’Asti, Barolo Chinato')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 140}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  CENE ED EVENTI
# ══════════════════════════════════════════════════════════════════════════════

def build_cene():
    t = Tpl(ID, 'cene')
    out = [intro(t, 'Cene ed eventi', ['Da due', 'a ventiquattro.'],
                 '<p>Una cena a due per un anniversario, la famiglia la domenica, gli amici fino a tardi, i clienti '
                 'dell’azienda in cantina. Cambia il numero, non il modo.</p>', 'pexels-3938727.jpg', overlay=66)]
    for foto, ey, ti, tx, pos in [
        ('pexels-6657697.jpg', '01 / 04 — In due', 'Un anniversario, una proposta.',
         'Cinque portate, il servizio a tavola e io che sparisco in cucina fra un piatto e l’altro. Per la proposta di '
         'matrimonio, l’anello lo nascondo dove volete.', 'center center'),
        ('pexels-6232536.jpg', '02 / 04 — In famiglia', 'La domenica, come una volta.',
         'Il pranzo lungo della domenica, i compleanni dei nonni, i battesimi. Piatti da condividere al centro della tavola.',
         'center center'),
        ('pexels-3937888.jpg', '03 / 04 — Con gli amici', 'Fino a tardi, senza fretta.',
         'Fino a ventiquattro persone, anche in piedi. Con un’ora di cucina prima, i tajarin li tirate voi.', 'center center'),
        ('pexels-19986452.jpg', '04 / 04 — In azienda', 'I clienti, in cantina.',
         'Cene per clienti e squadre in cantina, in agriturismo o in ufficio. Fattura, menu bilingue e un sommelier.',
         'center center'),
    ]:
        out.append(pannello(t, foto, ey, ti, tx, 'Prenota la data', PRENOTA, pos=pos))

    galleria = t.classic('progallery', {
        'images': [{'url': img(f), 'alt': a, 'caption': '', 'subtitle': '', 'text': ''} for f, a in [
            ('pexels-5473349.jpg', 'Candele di vetro accese sulla tavola apparecchiata'),
            ('pexels-27582699.jpg', 'Capesante con verdure su un piatto scuro'),
            ('pexels-6288729.jpg', 'Amici che brindano a una cena a casa'),
            ('pexels-28052915.jpg', 'Le mani dello chef finiscono un piatto con la sac à poche'),
            ('pexels-3937411.jpg', 'Una tavola apparecchiata con le candele accese'),
            ('pexels-29259654.jpg', 'Un dolce al cioccolato su un tavolo nero'),
        ]],
        'layout': 'expand', 'layout_family': 'classic', 'columns': 3, 'gap': 6, 'img_height': '360px',
        'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 4, 'mobile_columns': 1,
        'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
        'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
    })
    out.append(t.section([
        t.one([sechead(t, 'Le ultime sere', ['Qualche tavola', 'di quest’anno.'], size=56)]),
        t.one([galleria]),
    ], padding='large', style={'padding_top': 140}))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande', ['Prima', 'di prenotare.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Se la tua non c’è, chiamami: rispondo anche la sera, fra un piatto e l’altro.</p>'),
                      contacts(t)], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('E se la mia cucina è piccola?', 'Ho cucinato in cucine da due fuochi per quattordici persone. Porto un '
                                                      'fornello in più e tutto quello che serve.'),
                   ('Allergie e intolleranze?', 'Me le dici quando prenoti: cambio i piatti, non li tolgo. Ogni cena ha un '
                                                 'menu senza glutine se serve.'),
                   ('Quanto prima devo prenotare?', 'Due settimane di solito, un mese per ottobre, novembre e dicembre.'),
                   ('Fuori dal Piemonte?', 'Sì, in Liguria, Lombardia e Valle d’Aosta: il viaggio e una notte si aggiungono al '
                                           'preventivo.'),
               ], 'cene')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(SALA), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  CHI SONO
# ══════════════════════════════════════════════════════════════════════════════

def build_chi_sono():
    t = Tpl(ID, 'chi-sono')
    out = [intro(t, 'Chi sono', ['Elia,', 'cuoco di Alba.'],
                 '<p>Quindici anni nelle cucine degli altri, due stelle e una trattoria. Poi ho capito che mi piaceva vedere '
                 'le facce di chi mangia.</p>', 'pexels-20509791.jpg', overlay=66, pos='center 30%')]
    out.append(t.section([
        t.row([t.col([image(t, 'pexels-30874866.jpg', 'Elia Corradi al lavoro in cucina, in bianco e nero', ratio='4/5',
                            position='center 30%', drift=True)], wm='2-5', ws='1-1'),
               t.col([label(t, 'La storia', color=BRACE),
                      headline(t, 'Ho imparato a cucinare <em>guardando il fuoco.</em>', size=56, tag='h2', pad_bottom=16),
                      text(t, '<p>Mio nonno aveva una trattoria a Neive: la brace si accendeva alle cinque e si spegneva a '
                              'mezzanotte. Dopo la scuola alberghiera ho lavorato a Torino, a Parigi e a Copenaghen, poi sei anni '
                              'in un ristorante stellato in Langa.</p><p>Dal 2021 cucino solo a casa delle persone. Una cena per '
                              'sera, mai due: la spesa la faccio io, la mattina, e quello che non trovo non lo cucino.</p>', size=16),
                      specs(t, [('Scuola', 'Alberghiero di Barolo, 2006'), ('Cucine', 'Torino, Parigi, Copenaghen, Langhe'),
                                ('Chef privato', 'dal 2021, oltre 600 cene')])], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 120}))

    numeri = t.classic('statstrip', {
        'items': [{'value': '600+', 'label': 'cene a casa'}, {'value': '24', 'label': 'ospiti al massimo'},
                  {'value': '12', 'label': 'cantine amiche'}, {'value': '1', 'label': 'cena per sera'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': CREMA, 'value_size': 60, 'value_weight': '300',
        'label_color': TENUE, 'label_size': 11, 'label_uppercase': True, 'label_weight': '400', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([t.one([numeri])], padding='large', style={'padding_top': 20, 'padding_bottom': 120}))

    out.append(t.section([
        t.row([t.col([label(t, 'Il tartufo', color=BRACE),
                      headline(t, 'D’autunno, <em>sopra ogni cosa.</em>', size=76, tag='h2', pad_bottom=16),
                      text(t, '<p>Lo compro il giorno della cena da un trifulau di Montà che conosco da vent’anni. Lo pesiamo '
                              'insieme a tavola: paghi quello che mangi.</p>', size=16, color=SOFT)], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_TARTUFO, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 110}))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-4699966.jpg', 'Tajarin freschi sulla farina', ratio='1/1')], wm='1-3', ws='1-1'),
               t.col([image(t, 'pexels-30521760.jpg', 'Le mani che fanno la pasta, in bianco e nero', ratio='1/1')], wm='1-3', ws='1-1'),
               t.col([image(t, 'pexels-6303015.jpg', 'Le braci accese sotto la griglia', ratio='1/1')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=12),
    ], padding='large', style={'padding_top': 120}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  PRENOTA LA DATA
# ══════════════════════════════════════════════════════════════════════════════

def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [intro(t, 'Prenota la data', ['Scegli la sera,', 'al resto penso io.'],
                 '<p>Ti rispondo in giornata con il menu e il prezzo, scritti. La data è tua quando mi confermi, senza '
                 'anticipi.</p>', 'pexels-30247097.jpg', overlay=60)]
    campi = [campo('text', 'Nome', 'nome', obbligatorio=True), campo('email', 'Email', 'email', obbligatorio=True),
             campo('tel', 'Telefono', 'telefono', obbligatorio=True), campo('date', 'La sera', 'data', obbligatorio=True),
             campo('select', 'Quanti siete', 'ospiti', obbligatorio=True,
                   opzioni='2\n3 — 4\n5 — 8\n9 — 12\n13 — 24'),
             campo('text', 'Dove', 'dove', obbligatorio=True, segnaposto='Città o paese'),
             campo('textarea', 'La cena composta', 'cena', larghezza='1-1', param='cena',
                   segnaposto='Se l’hai composta in home, arriva qui da sola'),
             campo('text', 'Allergie o cose che non mangiate', 'allergie', larghezza='1-1'),
             campo('textarea', 'Raccontami la serata', 'messaggio', larghezza='1-1',
                   segnaposto='Un compleanno, una sorpresa, un ospite speciale.')]
    out.append(t.section([
        t.row([t.col([sechead(t, 'La richiesta', ['Due righe', 'bastano.'], size=64, layout='stack', size_mobile=40),
                      text(t, '<p>Se preferisci, chiamami: dalle 10 alle 12 rispondo sempre. Dopo le 15 sono al mercato '
                              'o ai fornelli.</p>'),
                      contacts(t)], wm='2-5', ws='1-1'),
               t.col([modulo(t, campi, 'Fuoco Lento — una cena', 'Manda la richiesta',
                             'Ricevuta. Ti scrivo in giornata con il menu e il prezzo.')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(SALA), padding='large', html_id='modulo', style={'padding_top': 130, 'padding_bottom': 130}))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Come si prenota', ['Senza anticipi,', 'senza sorprese.'], size=48, layout='stack', size_mobile=34)],
                     wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quando pago?', 'A fine serata, con bonifico o carta. Per le cene oltre le dodici persone chiedo un acconto '
                                    'del 30% una settimana prima.'),
                   ('Posso cambiare il numero degli ospiti?', 'Fino a tre giorni prima, senza costi. Dopo, la spesa è già fatta.'),
                   ('E se devo disdire?', 'Fino a sette giorni prima non paghi niente. Dopo, il 50% del menu.'),
                   ('Serve qualcosa da parte mia?', 'Una cucina con un forno e un frigorifero libero a metà. Piatti e '
                                                     'bicchieri, se volete, li porto io.'),
               ], 'pren')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large'))

    out.append(t.section([
        centered(t, [headline(t, 'Ci vediamo <em>in cucina.</em>', size=96, tag='h2', align='center', pad_bottom=0)]),
    ], bg=video_bg(V_VINO, overlay=40), padding='xlarge', style={'padding_top': 200, 'padding_bottom': 200}))
    return out


def build_404():
    t = Tpl(ID, '404')
    sec = t.section([
        centered(t, [label(t, 'Errore 404', color=BRACE, align='center'),
                     headline(t, 'Questa pagina <em>è bruciata.</em>', size=110, tag='h1', align='center', pad_bottom=20, spacing=-3),
                     text(t, '<p>Capita anche ai migliori: torniamo al menu.</p>', size=17, align='center', color=SOFT),
                     button(t, 'Torna all’inizio', HOME, kind='brace', align='center')]),
    ], bg=video_bg(V_FUOCO, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})
    sec['advanced'].update(braci())
    return [sec]


THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Ristorazione', 'zone': 'Le braci le lasci tu',
    'tags': ['chef privato', 'cene a casa', 'langhe', 'tartufo', 'eventi', 'configuratore', 'scuro'],
    'description': 'Uno chef privato fra Langhe e Torino, scuro come una cucina alla sera: il flambé in apertura con le braci '
                   'che seguono il puntatore, la cena da comporre col prezzo a persona che arriva nel modulo, l’impiattamento '
                   'in video, le quattro stagioni da sfogliare, le cene che si svelano di lato e la data da prenotare.',
    'menu': {'name': 'Fuoco Lento — menu', 'items': [
        {'title': 'Il menu', 'url': MENU}, {'title': 'Cene ed eventi', 'url': CENE}, {'title': 'Chi sono', 'url': CHEF},
        {'title': 'Prenota', 'url': PRENOTA}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Fuoco Lento — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Fuoco Lento — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Fuoco Lento — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Fuoco Lento — Home'},
        'menu': {'file': 'menu.json', 'type': 'page', 'title': 'Fuoco Lento — Il menu di stagione'},
        'cene': {'file': 'cene.json', 'type': 'page', 'title': 'Fuoco Lento — Cene ed eventi'},
        'chi-sono': {'file': 'chi-sono.json', 'type': 'page', 'title': 'Fuoco Lento — Chi sono'},
        'prenota': {'file': 'prenota.json', 'type': 'page', 'title': 'Fuoco Lento — Prenota la data'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'menu': {'title': 'Il menu di stagione', 'template': 'menu', 'slug': 'menu'},
        'cene': {'title': 'Cene ed eventi', 'template': 'cene', 'slug': 'cene'},
        'chi-sono': {'title': 'Chi sono', 'template': 'chi-sono', 'slug': 'chi-sono'},
        'prenota': {'title': 'Prenota la data', 'template': 'prenota', 'slug': 'prenota'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 30, 'ring_color': HEX['light'], 'ring_width': 1,
               'dot_size': 5, 'dot_color': HEX['primary'], 'hot_scale': 2.0, 'hot_color': HEX['primary'],
               'hot_fill': 'rgba(232,119,63,0.12)', 'blend_mode': 'normal', 'magnetic_selector': 'a, button',
               'pull_strength': 0.3, 'follow_easing': 0.2},
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Jost\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Fraunces\', Georgia, \'Times New Roman\', serif',
            'font_family_mono': '\'DM Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '300',
            'heading_line_height': '1.02', 'heading_letter_spacing': '-0.02em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '5.5rem', 'font_size_h2': '3.6rem', 'font_size_h3': '2rem',
            'font_size_h4': '1.4rem', 'font_size_h5': '1.1rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.8rem',
            'font_size_h2_mobile': '2.1rem', 'font_size_h3_mobile': '1.5rem',
        },
        'google_fonts': ['Fraunces', 'Jost', 'DM Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': ['Fraunces'],
        'buttons': {'font_size': '12px', 'font_weight': '500', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '4px',
                    'text_transform': 'uppercase', 'letter_spacing': '2px', 'hover_brightness': '100'},
        'layout': {'border_radius': '4px', 'border_radius_large': '6px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': 'transparent', 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '4px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '400', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 5, 'scale': 170},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'menu.json': build_menu(), 'cene.json': build_cene(), 'chi-sono.json': build_chi_sono(),
              'prenota.json': build_prenota(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
