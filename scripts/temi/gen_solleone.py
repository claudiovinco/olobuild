# -*- coding: utf-8 -*-
"""Genera il tema «Solleone» (id solleone): uno studio fotografico a Lecce, due fotografi sempre sulle mode, tanti
servizi (matrimoni, ritratti, brand, cibo, case, eventi, famiglie, animali, reel).

Caldo come il nome: latte e cacao, la terracotta del Barocco leccese al tramonto, il giallo del sole di luglio. Titoli
nel Calistoga, tondo e un po' retrò; testo in Reddit Sans, i dati in Reddit Mono. Angoli morbidi, pulsanti a pillola,
grana leggera.

Dettagli: l'apertura a sipario col nome, la scritta gigante dietro il video, il nastro dei servizi che si piega
correndo, la luce della controra in video.

Moduli: la ghiera dei modi di una fotocamera da girare per scegliere il servizio (Ghiera, M · P · B · F · C · E · K · A ·
V); le mode del 2026 che si svelano di lato una sopra l'altra (Reveal orizzontale), una col video; le pellicole da
provare sulla stessa foto col divisore (Pellicole); il dietro le quinte; il feed; il quiz «che servizio fa per te?»; il
listino; la giornata del matrimonio che scorre di lato (Cover orizzontale); il pacchetto contenuti col prezzo
(Configuratore); il modulo per prenotare.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'solleone'
NAME = 'Solleone'

HEX = {
    'primary': '#E5582A', 'primary_contrast': '#22120A', 'secondary': '#F2B33D', 'secondary_contrast': '#22120A',
    'accent': '#F2B33D', 'dark': '#1F110A', 'light': '#FFF8EF', 'muted': '#F1DFCB', 'muted_contrast': '#2A170E',
    'text': '#2A170E', 'text_muted': '#7A5B49', 'background': '#F8EEE2', 'border': '#E6CDB4', 'link': '#C2471F',
}

INK = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
TERRA = 'var(--olo-color-primary)'
ON_TERRA = 'var(--olo-color-primary-contrast)'
SOLE = 'var(--olo-color-secondary)'
FONDO = 'var(--olo-color-background)'
SABBIA = 'var(--olo-color-muted)'
CACAO = 'var(--olo-color-dark)'
LATTE = 'var(--olo-color-light)'
FILO = 'var(--olo-color-border)'
SOFT_LATTE = mix(LATTE, 84)
MONO = 'var(--olo-font-family-mono)'
R = radius(20)

ADDRESS = 'Via Giuseppe Libertini 31, 73100 Lecce'
PHONE = '0832 000 416'
PHONE_TEL = 'tel:+390832000416'
MAIL = 'ciao@solleone.example'
LAT, LNG = '40.3536', '18.1680'
HOME = '{{PAGINA:home}}'
SERVIZI = '{{PAGINA:servizi}}'
MATRIMONI = '{{PAGINA:matrimoni}}'
BRAND = '{{PAGINA:brand}}'
PRENOTA = '{{PAGINA:prenota}}'

V_BALLO = 'pexels-video-31948671'     # le sagome che ballano davanti al tramonto rosso sul mare
V_LAGO = 'pexels-video-2035509'       # lui la solleva controluce, il sole sull'acqua
V_ORO = 'pexels-video-3403330'        # una ragazza nel vestito d'oro, la luce rossa, i palloncini
V_INSTAX = 'pexels-video-6132039'     # la Instax rosa: scatta, la foto esce, ride
V_OLIO = 'pexels-video-5897983'       # l'olio versato sulla pizza
V_SET = 'pexels-video-5481284'        # un fotografo seduto per terra che scatta
V_MARE = 'pexels-video-10494881'      # gli amici che corrono nel mare al tramonto
V_CONTRORA = 'pexels-video-5061816'   # la luce arancio della controra dalle persiane, l'ombra della palma

PHOTOS = [
    'pexels-8279018.jpg', 'pexels-6620958.jpg', 'pexels-33867520.jpg', 'pexels-16537980.jpg', 'pexels-9854107.jpg',
    'pexels-16564809.jpg', 'pexels-28302549.jpg', 'pexels-39468582.jpg', 'pexels-2424890.jpg', 'pexels-32498141.jpg',
    'pexels-17975862.jpg', 'pexels-34777037.jpg', 'pexels-16753394.jpg', 'pexels-39555533.jpg', 'pexels-14330674.jpg',
    'pexels-38841199.jpg', 'pexels-17174026.jpg', 'pexels-3945339.jpg', 'pexels-39284070.jpg', 'pexels-15631397.jpg',
    'pexels-34120065.jpg', 'pexels-10834798.jpg', 'pexels-33543047.jpg', 'pexels-8104206.jpg', 'pexels-8104203.jpg',
    'pexels-7142988.jpg', 'pexels-39998188.jpg', 'pexels-16374146.jpg', 'pexels-12446438.jpg', 'pexels-30640575.jpg',
    'pexels-4306963.jpg', 'pexels-30730340.jpg', 'pexels-31134069.jpg', 'pexels-34372538.jpg', 'pexels-30882828.jpg',
    'pexels-4087615.jpg', 'pexels-3184191.jpg', 'pexels-1537635.jpg', 'pexels-39551172.jpg', 'pexels-15545298.jpg',
    'pexels-3587285.jpg', 'pexels-39166707.jpg', 'pexels-36232024.jpg', 'pexels-18178648.jpg', 'pexels-37952366.jpg',
    'pexels-24431339.jpg', 'pexels-29794339.jpg', 'pexels-30433307.jpg', 'pexels-33250566.jpg', 'pexels-11479537.jpg',
    'pexels-29808541.jpg', 'pexels-4186547.jpg', 'pexels-15861733.jpg', 'pexels-15861611.jpg', 'pexels-20831571.jpg',
    'pexels-15861734.jpg', 'pexels-34677251.jpg', 'pexels-35193542.jpg', 'pexels-26526257.jpg', 'pexels-5610113.jpg',
    'pexels-9005501.jpg', 'pexels-7038151.jpg', 'pexels-9218700.jpg', 'pexels-9748520.jpg', 'pexels-8128065.jpg',
    'pexels-13929970.jpg', 'pexels-8357681.jpg', 'pexels-8371393.jpg', 'pexels-36073022.jpg', 'pexels-8093836.jpg',
    'pexels-35096909.jpg', 'pexels-33277052.jpg', 'pexels-5456333.jpg', 'pexels-31019868.jpg', 'pexels-28638693.jpg',
    'pexels-14416558.jpg', 'pexels-31019864.jpg', 'pexels-37628928.jpg', 'pexels-20005294.jpg', 'pexels-40130306.jpg',
    'pexels-39974656.jpg', 'pexels-15817165.jpg', 'pexels-34304012.jpg', 'pexels-8528764.jpg', 'pexels-16668564.jpg',
    'pexels-4883696.jpg', 'pexels-35826038.jpg', 'pexels-13023099.jpg', 'pexels-12181421.jpg',
]
VIDEOS = [V_BALLO, V_LAGO, V_ORO, V_INSTAX, V_OLIO, V_SET, V_MARE, V_CONTRORA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=40, color=CACAO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=36, color=CACAO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=42, color=INK, accent=TERRA):
    """Il dato in mono sopra, il titolo tondo sotto: la seconda riga in terracotta."""
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': TENUE if color == INK else SOFT_LATTE,
        'eyebrow_dot_color': TERRA, 'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 12,
        'eyebrow_font_weight': '500',
        'headline_lines': [{'text': l, 'color': color if i % 2 == 0 else accent, 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.0,
        'headline_font_weight': '400', 'headline_align': align,
        'headline_font_size_tablet': max(40, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False,
        'tagline_text_color': TENUE if color == INK else SOFT_LATTE,
        'tagline_text_size': 17, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.2fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=INK, tag='h3', pad_bottom=10, align='left', accent=TERRA, spacing=None):
    """La parte fra <em> diventa il «testo accento», nel colore d'accento (il Calistoga non ha corsivo)."""
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': '400', 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': False, 'accent_color': accent})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(30, int(size * 0.56)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=TENUE, pad_bottom=14, align='left', pad_top=0):
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 12, 'heading_weight': '500',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '1.5'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=17, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.7, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='terra', align='left', pad_top=14):
    """Pillole: terracotta col testo cacao, cacao col testo latte, latte sopra le foto."""
    if kind == 'terra':
        o = {'bg_color': TERRA, 'text_color': ON_TERRA, 'hover_bg_color': CACAO, 'hover_text_color': LATTE}
    elif kind == 'cacao':
        o = {'bg_color': CACAO, 'text_color': LATTE, 'hover_bg_color': TERRA, 'hover_text_color': ON_TERRA}
    elif kind == 'latte':
        o = {'bg_color': LATTE, 'text_color': CACAO, 'hover_bg_color': SOLE, 'hover_text_color': CACAO}
    else:
        o = {'bg_color': 'transparent', 'text_color': INK, 'hover_bg_color': CACAO, 'hover_text_color': LATTE,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1.5,
             'shadow_color': mix(INK, 40), 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 999, 'font_size': 15, 'font_weight': '600',
              'text_transform': 'none', 'letter_spacing': '0'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', drift=False, lightbox=False, rr=R):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': rr, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -30, 'position': 0}, {'value': 30, 'position': 100}]}} if drift else None)


def video(t, name, ratio='4:5'):
    return t.bare('video', {'source_type': 'file', 'video_url': '', 'file_url': img(name + '.mp4'),
                            'poster_image': img(name + '.jpg'), 'display_mode': ratio, 'aspect_ratio': ratio, 'cover_mode': False,
                            'object_position': 'center center', 'border_radius': 20, 'autoplay': True, 'muted': True,
                            'loop': True, 'controls': False, 'show_play_icon': False, 'facade': False})


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def contacts(t, color=INK):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': PRENOTA + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': TERRA, 'icon_size': '18', 'text_color': color, 'text_size': '16', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': TERRA, 'header_font_size': 22, 'header_font_weight': '400',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 16, 'text_color': TENUE,
        'icon_color': TERRA, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, lines, body, size=110, bg=None):
    """L'apertura delle pagine interne, scura: la testata trasparente ha le scritte chiare."""
    return t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=50, color=LATTE, accent=SOLE)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=18, color=SOFT_LATTE)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], bg=bg or solid(CACAO), padding='large', style={'padding_top': 200, 'padding_bottom': 90})


def nastro(t, items, color=CACAO, size=44):
    return t.classic('marquee', {
        'content_type': 'text', 'separator': '  ✺  ', 'text_items': ' | '.join(items),
        'speed': '45', 'direction': 'left', 'pause_hover': False, 'gap': 34, 'velocity_skew': True,
        'bg_color': 'transparent', 'text_color': color, 'font_size': size, 'font_weight': '400', 'letter_spacing': 0,
        'text_transform': 'none', 'font_family': 'heading', 'font_style': 'normal', 'separator_color': SOLE, 'height': int(size * 2.1),
    })


def pannello(t, bg, eyebrow, titolo, testo, link_t, link_u, effetto='reveal-h', html_id=None):
    """Una sezione a tutto schermo con la foto (o il video): entra di lato nel gruppo orizzontale. Pochi righi, così sta
    in una schermata anche sul telefono e il gruppo resta orizzontale."""
    blocco = [label(t, eyebrow, color=SOLE),
              headline(t, titolo, size=88, color=LATTE, tag='h2', pad_bottom=16, accent=SOLE),
              text(t, '<p>' + testo + '</p>', size=17, color=SOFT_LATTE)]
    if link_t:
        blocco.append(button(t, link_t, link_u, kind='latte', pad_top=10))
    return t.section([
        t.row([t.col(blocco, wm='1-2', ws='1-1'), t.col([], wm='1-2', ws='1-1')], layout='custom', custom='55,45',
              valign='end', anim=None),
    ], bg=bg, padding='large', width='large', sticky_effect=effetto, sticky_h_align='bottom', html_id=html_id,
        style={'padding_top': 140, 'padding_bottom': 80})


SERVIZI_NOMI = ['Matrimoni', 'Ritratti', 'Brand', 'Food', 'Case', 'Eventi', 'Famiglie', 'Animali', 'Reel e video']


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': NAME, 'logo_text_size': '28', 'logo_text_color': LATTE,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': mix(CACAO, 88), 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': LATTE, 'hover_color': SOLE, 'active_color': SOLE,
        'font_size': '15', 'font_weight': '600', 'text_transform': 'none', 'letter_spacing': '0', 'item_gap': '30',
        'bar_width': 'wide', 'nav_height': '88', 'bar_padding': {'top': 10, 'right': 36, 'bottom': 10, 'left': 36},
        'hover_effect': 'underline', 'hover_effect_color': SOLE,
        'button_mode': 'last', 'btn_bg': TERRA, 'btn_color': ON_TERRA, 'btn_hover_color': ON_TERRA,
        'btn_radius': radius(999), 'btn_padding': {'top': 12, 'right': 22, 'bottom': 12, 'left': 22}, 'btn_hover_effect': 'none',
        'btn_font_weight': '700', 'btn_transform': 'none', 'btn_letter_spacing': '0',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '#', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': CACAO, 'mobile_text_color': LATTE, 'mobile_accent_color': SOLE,
        'hamburger_style': 'minimal', 'hamburger_color': LATTE, 'mobile_font_size': '44', 'mobile_link_size': '44',
        'mobile_link_font': 'heading', 'mobile_link_weight': '400',
        'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'Solleone', size=210, color=SOLE, tag='p', pad_bottom=0, spacing=-6)
    claim = text(t, '<p>Gaia Rizzo e Nico Perrone, fotografi a Lecce dal 2016. Nove servizi, un solo modo di guardare: '
                    'caldo, vero, un po’ mosso.</p>', size=16, color=SOFT_LATTE, pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': LATTE, 'link_hover_color': SOLE, 'active_color': SOLE,
                                 'font_size': '16', 'font_weight': '500', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('I servizi', SERVIZI), ('Matrimoni', MATRIMONI), ('Brand e contenuti', BRAND), ('Prenota', PRENOTA),
                  ('Privacy', '/privacy-policy/')])
    orari = text(t, '<p>Studio: lunedì — sabato 10 — 13 e 17 — 20<br>D’estate la controra è sacra: dalle 13 alle 17 si '
                    'scatta solo fuori.</p>', size=16, color=LATTE)
    legal = text(t, '<p>© 2026 Solleone Studio · Lecce · Prezzi IVA inclusa · Le foto di questo sito sono nostre o dei '
                    'nostri amici.</p>', size=13, color=mix(LATTE, 55))
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'tiktok', 'url': 'https://tiktok.com'}],
                                  'size': '32', 'alignment': 'right', 'icon_color': SOLE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': CACAO, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim, contacts(t, color=LATTE)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine', color=SOLE), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'Lo studio', color=SOLE), orari], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': mix(LATTE, 16)},
                          'padding_top': 22, 'margin_top': 24})
    return [t.section([t.one([nastro(t, SERVIZI_NOMI, color=mix(LATTE, 70), size=30)], anim=None, style={'margin_bottom': 64}),
                       top, t.one([brand], anim='fade', style={'margin_top': 40}), bottom],
                      bg=solid(CACAO), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

# La ghiera: sigla, titolo, testo, prezzo, foto, video (facoltativo), collegamento.
GHIERA = [
    ('M', 'Matrimoni', 'Dai preparativi all’ultimo ballo, con la luce delle sette sul mare. Due fotografi, un drone, venti '
     'foto il giorno dopo.', 'da 1.900 €', 'pexels-15861733.jpg', V_LAGO, MATRIMONI),
    ('P', 'Ritratti', 'Un’ora in studio o fuori, alla luce che preferite. Venti foto scelte insieme, ritoccate poco.',
     'da 180 €', 'pexels-33277052.jpg', '', PRENOTA + '?servizio=Ritratti#modulo'),
    ('B', 'Brand', 'La vostra attività come la vedono i clienti: il bancone, le mani, le persone. Per il sito e i social.',
     'da 450 €', 'pexels-8279018.jpg', '', BRAND),
    ('F', 'Food', 'Piatti, tavole e cucine con la luce vera della sala. Per menù, delivery e campagne.',
     'da 380 €', 'pexels-30882828.jpg', '', BRAND + '#contenuti'),
    ('C', 'Case', 'Masserie, case vacanza e appartamenti in centro: foto per Airbnb e agenzie, anche dal drone.',
     'da 320 €', 'pexels-16753394.jpg', '', PRENOTA + '?servizio=Case#modulo'),
    ('E', 'Eventi', 'Feste, inaugurazioni, compleanni e cene aziendali. Col flash diretto, se volete l’effetto 2004.',
     'da 90 € l’ora', 'pexels-7142988.jpg', '', PRENOTA + '?servizio=Eventi#modulo'),
    ('K', 'Famiglie', 'Al mare, al tramonto, coi bambini che corrono: noi li seguiamo, voi giocate.',
     'da 220 €', 'pexels-34120065.jpg', '', PRENOTA + '?servizio=Famiglie#modulo'),
    ('A', 'Animali', 'Cani, gatti, cavalli. Con pazienza, biscotti in tasca e la luce bassa del pomeriggio.',
     'da 150 €', 'pexels-32498141.jpg', '', PRENOTA + '?servizio=Animali#modulo'),
    ('V', 'Reel e video', 'Video verticali da quindici secondi, montati la sera stessa, con la musica giusta.',
     'da 290 €', V_ORO + '.jpg', V_ORO, BRAND + '#pacchetto'),
]

MODE = [
    ('Flash diretto', 'Il lampo in faccia e gli angoli scuri: sembra una festa del 2004, ed è il ritratto più chiesto di '
     'quest’anno.', photo_bg('pexels-30640575.jpg', overlay=48, position='center 30%')),
    ('Mosso voluto', 'Tempi lunghi e la fotocamera che si muove con chi balla: le feste tornano a sembrare feste.',
     photo_bg('pexels-35193542.jpg', overlay=40)),
    ('Istantanee', 'La compatta digitale della zia e la Instax rosa: le portiamo ai matrimoni e le foto le stampiamo lì.',
     video_bg(V_INSTAX, overlay=40)),
    ('Toni burro', 'Gialli morbidi, contrasto basso, pelle calda: il colore del 2026, anche sulle foto.',
     photo_bg('pexels-16564809.jpg', overlay=34)),
]

PELLICOLE = [
    ('Portra 400', 'La pelle morbida, i verdi caldi', 'portra'),
    ('Cinestill 800T', 'Le luci della sera, l’alone rosso', 'cinestill'),
    ('Toni burro', 'Il colore del 2026', 'burro'),
    ('Flash diretto', 'Il lampo e gli angoli scuri', 'flash'),
    ('Digicam 2004', 'Con la data arancione', 'digicam'),
    ('HP5', 'Bianco e nero di grana', 'bn'),
]


def ghiera(t):
    return t.bare('ghiera', {
        'voci': [{'sigla': s, 'titolo': ti, 'testo': tx, 'prezzo': pr, 'immagine': img(f), 'video': img(v + '.mp4') if v else '',
                  'link': u, 'testo_link': 'Prenota ' + ti.lower() if s not in ('B', 'F', 'V', 'M') else 'Scopri ' + ti.lower()}
                 for s, ti, tx, pr, f, v, u in GHIERA],
        'partenza': 1, 'titolo_centro': 'Servizi', 'mostra_numero': True, 'etichetta': 'Gira la ghiera e scegli il servizio',
        'stile': 'metallo', 'colore_ghiera': CACAO, 'colore_sigle': LATTE, 'colore_indice': TERRA, 'sfondo_pannello': '',
        'colore_pulsante': TERRA, 'colore_testo_pulsante': ON_TERRA, 'ombra': 'none',
        'sigla_font_family': 'heading', 'sigla_font_size': 22, 'sigla_font_weight': '400',
        'titolo_font_family': 'heading', 'titolo_font_size': 56, 'titolo_font_size_mobile': 40, 'titolo_font_weight': '400',
        'titolo_line_height': 1.0, 'titolo_color': INK, 'testo_font_size': 17, 'testo_color': TENUE,
        'prezzo_font_family': MONO, 'prezzo_font_size': 15, 'prezzo_font_weight': '500', 'prezzo_color': TERRA,
        'misura': 440, 'raggio_media': radius(24), 'raggio_pulsante': radius(999), 'proporzione': '4/5',
        'disposizione': 'lato', 'gap': 64,
    })


def pellicole(t, foto, alt):
    return t.bare('pellicole', {
        'immagine': img(foto), 'alt': alt,
        'pellicole': [{'nome': n, 'nota': no, 'look': lk, 'colore': ''} for n, no, lk in PELLICOLE],
        'partenza': 3, 'confronto': True, 'divisore': 50, 'etichetta_grezzo': 'Scatto grezzo', 'mostra_nota': True,
        'mostra_etichette': True, 'data_digicam': '\'26 07 18', 'grana': 35,
        'colore_accento': TERRA, 'colore_divisore': LATTE, 'sfondo_rullino': mix(INK, 6),
        'nome_font_family': 'heading', 'nome_font_size': 18, 'nome_font_weight': '400', 'nome_color': INK,
        'nota_font_size': 13, 'nota_color': TENUE, 'proporzione': '16/9', 'raggio': radius(24), 'raggio_rullino': radius(16),
        'posizione_rullini': 'sotto', 'gap': 18,
    })


def build_home():
    t = Tpl(ID, 'home')
    out = []

    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Solleone · studio fotografico · Lecce', 'eyebrow_dot': True,
        'title': 'Foto che<br><em>scottano.</em>',
        'subtitle': 'Matrimoni, ritratti, brand, cibo, case, eventi, famiglie, animali e reel: nove servizi, un solo modo di '
                    'guardare. Caldo, vero, un po’ mosso.',
        'media_bg': {'type': 'video', 'video_url': img(V_BALLO + '.mp4'), 'video_poster': img(V_BALLO + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center 60%'},
        'overlay_color': CACAO, 'overlay_top': 0.35, 'overlay_bottom': 0.5, 'overlay_sides': True, 'overlay_direction': '',
        'glow_on': True, 'glow_color': mix(SOLE, 30), 'glow_w': 900, 'glow_h': 600, 'glow_blur': 120, 'glow_x': 30, 'glow_y': 10,
        'frame_on': False, 'watermark_text': 'SOLE', 'watermark_color': mix(LATTE, 7),
        'accent': SOLE, 'text_color': LATTE, 'title_color': LATTE, 'subtitle_color': SOFT_LATTE,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 150, 'title_font_weight': '400',
        'title_line_height': '0.92', 'title_letter_spacing': '-2', 'title_text_transform': 'none',
        'subtitle_font_size': 18, 'subtitle_max_width': 520,
        'min_height': '100vh', 'content_max_width': 1320, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 150, 'right': 48, 'bottom': 80, 'left': 48},
        'cta_text': 'Gira la ghiera', 'cta_url': '#servizi', 'cta_bg_color': TERRA, 'cta_text_color': ON_TERRA,
        'cta_radius': radius(999), 'cta_size': '16', 'cta_style': 'filled', 'cta_font_weight': '700',
        'cta_text_transform': 'none', 'cta_letter_spacing': '0',
        'cta2_text': 'Prenota una data', 'cta2_url': PRENOTA, 'cta2_text_color': LATTE, 'cta2_style': 'outline',
        'meta_text': 'Lecce · dal 2016 · 180 matrimoni e qualche migliaio di risate', 'scroll_hint': '',
        'scene_depth': 'medium', 'scene_depth_fade': True, 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([t.one([nastro(t, SERVIZI_NOMI)], anim=None)], width='fullbleed', padding='remove-vertical',
                         bg=solid(TERRA)))

    out.append(t.section([
        t.one([sechead(t, '01 — I servizi', ['Gira la ghiera.', 'Scegli cosa ti serve.'], size=84,
                       tagline='Come sulla ghiera di una fotocamera: M per i matrimoni, P per i ritratti, B per il brand e '
                               'così via. Trascinatela, usate la rotella o le frecce.')]),
        t.one([ghiera(t)]),
    ], padding='large', html_id='servizi', style={'padding_top': 150, 'padding_bottom': 150}))

    # Le mode del 2026: quattro sezioni che si svelano di lato, una sopra l'altra.
    for i, (nome, frase, bg) in enumerate(MODE):
        out.append(pannello(t, bg, 'Tendenza %02d / 04 · 2026' % (i + 1), nome + '.', frase, 'Provalo sulle foto', '#look',
                            html_id='mode' if i == 0 else None))

    out.append(t.section([
        t.one([sechead(t, '02 — Il look', ['Scegli il tuo look,', 'poi trascina.'], size=84,
                       tagline='Ogni servizio esce in due versioni: naturale e col look che scegliete voi. Toccate un rullino e '
                               'tirate il divisore: a sinistra lo scatto com’è uscito, a destra lo sviluppo.')]),
        t.one([pellicole(t, 'pexels-15817165.jpg', 'Una coppia abbracciata su una collina arancione al tramonto')]),
    ], bg=solid(SABBIA), padding='large', width='large', html_id='look', style={'padding_top': 150, 'padding_bottom': 150}))

    out.append(t.section([
        # sul video arancio il testo sta in un riquadro scuro (rinforzi.json): testo chiaro, accento del sole (31 ott 2026)
        t.row([t.col([label(t, 'Lecce · luglio · ore 15:20', color=SOLE),
                      headline(t, 'Scattiamo nella controra, <em>quando gli altri dormono.</em>', size=96, color=LATTE,
                               tag='h2', pad_bottom=18, accent=SOLE),
                      text(t, '<p>Dalle una alle cinque Lecce si ferma e la luce entra dalle persiane in strisce arancioni. È '
                              'la nostra ora preferita: i ritratti più belli li abbiamo fatti lì.</p>', size=18,
                           color=SOFT_LATTE)], wm='3-5', ws='1-1'), t.col([], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', valign='center'),
    ], bg=video_bg(V_CONTRORA, overlay=8, color=SOLE), padding='xlarge', html_id='controra',
        style={'padding_top': 220, 'padding_bottom': 220}))

    numeri = t.classic('statstrip', {
        'items': [{'value': '180', 'label': 'matrimoni dal 2016'}, {'value': '9', 'label': 'servizi, un solo caldo'},
                  {'value': '24 h', 'label': 'le prime venti foto'}, {'value': '4,9', 'label': 'su 5, 312 recensioni'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 26, 'show_dividers': True, 'divider_color': FILO, 'band_border': False,
        'value_font_family': 'heading', 'value_color': TERRA, 'value_size': 52, 'value_weight': '400',
        'label_color': TENUE, 'label_size': 12, 'label_uppercase': True, 'label_weight': '500', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([
        t.row([t.col([video(t, V_SET, ratio='4:5')], wm='2-5', ws='1-1'),
               t.col([sechead(t, '03 — Dietro le quinte', ['Siamo in due,', 'sempre in movimento.'], size=64, layout='stack',
                              size_mobile=40),
                      text(t, '<p>Gaia segue le persone, Nico la luce. Ci sdraiamo per terra, saliamo sulle sedie, chiediamo '
                              'a tutti di ridere di nuovo. Dopo dieci minuti nessuno si ricorda più che ci siamo.</p>'),
                      numeri,
                      button(t, 'Prenota una data', PRENOTA, pad_top=24)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 150, 'padding_bottom': 120}))

    def persona(foto, nome, ruolo, bio, pos):
        return [image(t, foto, nome + ', ' + ruolo.lower(), ratio='4/5', position=pos),
                headline(t, nome, size=34, tag='h3', pad_bottom=2, spacing=-0.5),
                label(t, ruolo, color=TERRA, pad_bottom=8, pad_top=6),
                text(t, '<p>' + bio + '</p>', size=16)]
    voce = t.classic('testimonial', {
        'quote': 'Volevamo foto che non sembrassero un matrimonio. Ci hanno fatto ballare in spiaggia alle sette di sera: <em>la '
                 'nonna ha appeso quella col flash in salotto.</em>',
        'author_name': 'Chiara e Davide', 'author_role': 'sposati a Torre dell’Orso, giugno 2026',
        'rating': '5', 'text_color': INK, 'star_color': TERRA, 'author_color': TENUE, 'quote_accent_color': TERRA,
        'quote_font': 'heading', 'quote_size': 38, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': SOLE,
    })
    out.append(t.section([
        t.row([t.col(persona('pexels-15545298.jpg', 'Gaia Rizzo', 'Le persone',
                             'Lecce, 1991. Ha cominciato a vent’anni ai matrimoni dei cugini. Fa ridere chiunque, anche i testimoni '
                             'più rigidi.', 'center 30%'), wm='1-3', ws='1-1'),
               t.col(persona('pexels-39166707.jpg', 'Nico Perrone', 'La luce',
                             'Gallipoli, 1988. Viene dal cinema: si porta dietro i pannelli, il fumo e la pazienza di aspettare il '
                             'sole giusto.', 'center 35%'), wm='1-3', ws='1-1'),
               t.col([voce], wm='1-3', ws='1-1')], layout='33-33-33', gap=40, valign='center'),
    ], bg=solid(SABBIA), padding='large', html_id='noi'))

    feed = [('pexels-7142988.jpg', 'Due ragazze che saltano su un divano giallo col flash'),
            ('pexels-30882828.jpg', 'Una burrata coi pomodorini e il basilico'),
            ('pexels-12446438.jpg', 'Tre amici che ridono attorno al fuoco, di notte'),
            ('pexels-16753394.jpg', 'Un balcone con gli archi e il sole sulla pietra'),
            ('pexels-28638693.jpg', 'Un ritratto all’ora d’oro, la luce sul viso'),
            ('pexels-17975862.jpg', 'Un cane color miele al sole')]
    out.append(t.section([
        t.row([t.col([sechead(t, '04 — Il feed', ['Quello che', 'abbiamo fatto ieri.'], size=56, layout='stack', size_mobile=38)],
                     wm='1-2', ws='1-1'),
               t.col([button(t, '@solleone.studio', 'https://instagram.com', kind='filo', align='right', pad_top=0)], wm='1-2',
                     ws='1-1')], layout='50-50', valign='end'),
        t.row([t.col([image(t, f, a, ratio='1/1', lightbox=True)], wm='1-3', ws='1-2') for f, a in feed[:3]], layout='33-33-33', gap=16),
        t.row([t.col([image(t, f, a, ratio='1/1', lightbox=True)], wm='1-3', ws='1-2') for f, a in feed[3:]], layout='33-33-33', gap=16,
              style={'margin_top': 16}),
    ], padding='large'))

    out.append(t.section([
        centered(t, [label(t, 'Santa Croce · sette di sera · luglio', color=SOLE, align='center'),
                     headline(t, 'Prenota una data. <em>Il sole lo portiamo noi.</em>', size=104, color=LATTE, tag='h2',
                              align='center', pad_bottom=20, accent=SOLE),
                     text(t, '<p>Rispondiamo entro la giornata, anche su WhatsApp. Per i sabati di giugno e settembre muovetevi '
                             'presto.</p>', size=18, color=SOFT_LATTE, align='center'),
                     button(t, 'Prenota', PRENOTA, kind='terra', align='center', pad_top=12)], side=12.5),
    ], bg=photo_bg('pexels-37952366.jpg', overlay=55, position='center 30%'), padding='xlarge',
        style={'padding_top': 210, 'padding_bottom': 210}))
    return page(out, page_intro_enabled=True, page_intro_style='sipario', page_intro_text='Solleone',
                page_intro_caption='Studio fotografico · Lecce', page_intro_bg=HEX['primary'], page_intro_color=HEX['dark'],
                page_intro_duration=2200, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  I SERVIZI
# ══════════════════════════════════════════════════════════════════════════════

LISTINO = [
    ('Matrimoni', 'Tutta la giornata, due fotografi, venti foto il giorno dopo.', 'da 1.900 €', 'pexels-15861611.jpg'),
    ('Ritratti', 'Un’ora in studio o fuori, venti foto scelte insieme.', 'da 180 €', 'pexels-31019868.jpg'),
    ('Brand', 'Mezza giornata nella vostra attività, quaranta foto.', 'da 450 €', 'pexels-6620958.jpg'),
    ('Food', 'Fino a quindici piatti, nella vostra sala.', 'da 380 €', 'pexels-4087615.jpg'),
    ('Case', 'Interni ed esterni, anche dal drone, consegna in 48 ore.', 'da 320 €', 'pexels-14330674.jpg'),
    ('Eventi', 'A ore, venti foto prima di mezzanotte.', 'da 90 € l’ora', 'pexels-16374146.jpg'),
    ('Famiglie', 'Al mare o in campagna, un’ora al tramonto.', 'da 220 €', 'pexels-10834798.jpg'),
    ('Animali', 'Un’ora col vostro animale, con pazienza.', 'da 150 €', 'pexels-34777037.jpg'),
    ('Reel e video', 'Tre video verticali da quindici secondi.', 'da 290 €', 'pexels-13929970.jpg'),
]

QUIZ = [
    ('Ci sposiamo', 'M · Matrimoni', 'Matrimoni', 'Due fotografi tutto il giorno e la mezz’ora al tramonto solo per voi due.',
     'da 1.900 €', MATRIMONI, 'pexels-15861734.jpg'),
    ('Ho un’attività', 'B · Brand', 'Brand e contenuti', 'Foto e reel della vostra attività, per il sito e per i social.',
     'da 450 €', BRAND, 'pexels-8279018.jpg'),
    ('Ho un ristorante', 'F · Food', 'Food', 'I piatti, la sala, la cucina che lavora: con la luce vera, niente fondali.',
     'da 380 €', BRAND + '#contenuti', 'pexels-36073022.jpg'),
    ('Voglio un ritratto', 'P · Ritratti', 'Ritratti', 'Per il profilo, per un regalo, per ricordarvi come siete adesso.',
     'da 180 €', PRENOTA + '?servizio=Ritratti#modulo', 'pexels-5456333.jpg'),
    ('Affitto una casa', 'C · Case', 'Case', 'Le foto che fanno scegliere la vostra casa su Airbnb, al primo colpo d’occhio.',
     'da 320 €', PRENOTA + '?servizio=Case#modulo', 'pexels-39555533.jpg'),
    ('Facciamo una festa', 'E · Eventi', 'Eventi', 'Compleanni, lauree, inaugurazioni: col flash diretto, se vi piace.',
     'da 90 € l’ora', PRENOTA + '?servizio=Eventi#modulo', 'pexels-34372538.jpg'),
    ('Abbiamo un cane', 'A · Animali', 'Animali', 'Un’ora al parco o al mare: il cane si diverte, voi avete le foto.',
     'da 150 €', PRENOTA + '?servizio=Animali#modulo', 'pexels-17975862.jpg'),
]


def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [intro(t, 'I servizi', ['Nove servizi,', 'un solo caldo.'],
                 '<p>Dai matrimoni alle foto del menù, dai ritratti ai reel. Non sapete quale vi serve? Il quiz qui sotto ve lo '
                 'dice in un tocco.</p>')]
    quiz = t.classic('finder', {
        'preset': 'custom', 'eyebrow': 'In un tocco', 'heading': 'Che servizio <em>fa per te</em>?',
        'intro': 'Scegli la frase che ti somiglia: ti diciamo da dove partire e quanto costa.',
        'items': [{'option': o, 'icon': '', 'kicker': k, 'title': ti, 'text': tx, 'meta': pr, 'cta_text': 'Andiamo',
                   'cta_url': u, 'image': img(f), 'media_label': ''} for o, k, ti, tx, pr, u, f in QUIZ],
        'default_index': '1', 'zone_accent': TERRA, 'zone_on': ON_TERRA, 'align': 'center',
        'chip_bg': LATTE, 'chip_radius': radius(999), 'card_bg': LATTE, 'card_border': '', 'media_bg': SABBIA,
        'media_ratio': '4/5', 'media_ratio_custom': '4/5', 'object_position': 'center center', 'card_max_width': 860,
        'card_radius': radius(28), 'card_padding': {'top': 36, 'right': 40, 'bottom': 36, 'left': 36}, 'shadow': 'none',
        'tile_padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0},
        'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''},
    })
    out.append(t.section([t.one([quiz])], bg=solid(SABBIA), padding='large', html_id='quiz',
                         style={'padding_top': 120, 'padding_bottom': 120}))

    listino = t.classic('pricelist', {
        'items': [{'title': n, 'description': d, 'price': p, 'image_url': img(f), 'highlighted': False, 'badge': ''}
                  for n, d, p, f in LISTINO],
        'separator_style': 'dotted', 'separator_color': FILO, 'title_color': INK, 'price_color': TERRA,
        'description_color': TENUE, 'image_size': 72, 'image_border_radius': '16', 'show_image': True,
        'price_position': 'right', 'tile_padding': {'top': 16, 'right': 0, 'bottom': 16, 'left': 0}, 'card_bg': 'transparent',
        'card_border_radius': '0',
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Il listino', ['Prezzi chiari,', 'tutto compreso.'], size=56, layout='stack', size_mobile=38),
                      text(t, '<p>Nel prezzo ci sono il viaggio in tutto il Salento, la scelta delle foto insieme e la galleria '
                              'online per un anno. Lo sconto lo facciamo a chi torna.</p>'),
                      button(t, 'Chiedi un preventivo', PRENOTA)], wm='1-3', ws='1-1'),
               t.col([listino], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large', html_id='listino'))

    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Ci scrivete', 'description': 'Cosa, dove, quando. Rispondiamo in giornata, anche su '
                                                                         'WhatsApp.'},
                  {'number': '02', 'title': 'Ci vediamo', 'description': 'Un caffè in studio o una chiamata: decidiamo il posto, '
                                                                         'l’ora e il look.'},
                  {'number': '03', 'title': 'Scattiamo', 'description': 'Puntuali, col sole giusto. Se piove si rimanda senza '
                                                                        'costi.'},
                  {'number': '04', 'title': 'Vi arrivano', 'description': 'Venti foto in ventiquattro ore, tutte le altre in '
                                                                          'due settimane, in due versioni.'}],
        'columns': 4, 'gap': 16, 'auto_number': False, 'number_style': 'circle', 'number_color': ON_TERRA, 'number_bg': TERRA,
        'number_size': 15, 'number_font': MONO, 'number_weight': '600', 'title_color': INK, 'title_size': 26,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 15, 'align': 'left', 'item_gap': 12,
        'card_bg': LATTE, 'card_border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''},
        'card_radius': radius(24), 'card_padding': 28, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, 'Come funziona', ['Quattro passi,', 'zero pensieri.'], size=64,
                       tagline='Vale per tutti i servizi: cambia solo quanto dura la giornata.')]),
        t.one([passi]),
    ], bg=solid(SABBIA), padding='large'))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande', ['Quelle che', 'ci fanno tutti.'], size=48, layout='stack', size_mobile=34)],
                     wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Fate anche servizi fuori dal Salento?', 'Sì, in tutta Italia e all’estero: aggiungiamo il viaggio e le notti, '
                                                           'al prezzo che paghiamo.'),
                   ('Cos’è il «look» che si sceglie?', 'È lo sviluppo delle foto: Portra, toni burro, flash, bianco e nero… Ogni '
                                                     'servizio esce in due versioni, naturale e col vostro look.'),
                   ('Se piove?', 'Si rimanda, senza costi. Per i matrimoni abbiamo sempre un piano col coperto, e le foto sotto '
                                 'la pioggia di solito sono le più belle.'),
                   ('Le foto sono nostre?', 'Sì, per sempre. Noi le usiamo nel portfolio solo se ci dite di sì.'),
                   ('Fate fattura?', 'Sì, per privati e aziende. Per le aziende il preventivo arriva in giornata.'),
               ], 'serv')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  MATRIMONI
# ══════════════════════════════════════════════════════════════════════════════

GIORNO = [
    ('11:00', 'I preparativi', 'La luce dalla finestra, le mani della mamma, il vestito appeso alla porta.', 'pexels-37628928.jpg'),
    ('16:30', 'Il sì', 'In chiesa senza flash, in masseria col sole basso. Nico in fondo, Gaia vicino.', 'pexels-20005294.jpg'),
    ('19:10', 'La mezz’ora d’oro', 'Voi due in spiaggia, il sole che scende sul mare: venti minuti, le foto più belle.',
     'pexels-15861611.jpg'),
    ('23:40', 'La festa', 'Flash diretto, mosso voluto, gli zii che ballano: qui comincia il 2004.', 'pexels-26526257.jpg'),
]


def build_matrimoni():
    t = Tpl(ID, 'matrimoni')
    out = []
    out.append(t.section([
        t.row([t.col([label(t, 'Matrimoni · da aprile a ottobre · in tutto il Salento', color=SOLE),
                      headline(t, 'Con la luce <em>delle sette.</em>', size=140, color=LATTE, tag='h1', pad_bottom=18,
                               accent=SOLE, spacing=-4),
                      text(t, '<p>Due fotografi, un drone, la mezz’ora al tramonto solo per voi. Venti foto il giorno dopo, '
                              'tutte le altre in due settimane: naturali e col vostro look.</p>', size=18, color=SOFT_LATTE),
                      button(t, 'Chiedi la data', PRENOTA + '?servizio=Matrimoni#modulo', kind='terra', pad_top=8)],
                     wm='3-5', ws='1-1'), t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', valign='end'),
    ], bg=video_bg(V_LAGO, overlay=38), padding='xlarge', style={'padding_top': 280, 'padding_bottom': 100}))

    out.append(t.section([
        t.one([sechead(t, '01 — La giornata', ['Dalle undici', 'a dopo mezzanotte.'], size=72,
                       tagline='Scendete: la giornata arriva di lato, una scena dopo l’altra.')]),
    ], padding='large', style={'padding_bottom': 40}))
    for i, (ora, titolo, frase, f) in enumerate(GIORNO):
        out.append(pannello(t, photo_bg(f, overlay=(42, 46, 30, 34)[i], position='center 35%'),
                            '%s — %02d / 04' % (ora, i + 1), titolo + '.', frase, '', '', effetto='cover-h'))

    def piano(nome, prezzo, cosa, periodo, popolare=False):
        return t.classic('pricing', {
            'plan_name': nome, 'price': prezzo, 'currency': '€', 'currency_position': 'after', 'period': periodo,
            'currency_size': '24', 'features': cosa, 'check_style': 'circle-check', 'cta_text': 'Chiedi la data',
            'cta_url': PRENOTA + '?servizio=Matrimoni#modulo', 'cta_bg_color': TERRA if popolare else CACAO,
            'cta_text_color': ON_TERRA if popolare else LATTE, 'cta_radius': '999', 'cta_hover_bg_color': SOLE,
            'cta_hover_text_color': CACAO, 'is_popular': popolare, 'badge_text': 'La più scelta', 'badge_bg_color': SOLE,
            'badge_text_color': CACAO, 'bg_color': LATTE, 'accent_color': TERRA, 'text_color': INK, 'border_radius': '28',
            'shadow': 'none', 'additional_info': '',
        }, style={'margin_top': 0, 'margin_right': 0, 'margin_bottom': 0, 'margin_left': 0})
    out.append(t.section([
        t.one([sechead(t, '02 — Quanto costa', ['Tre pacchetti,', 'nessuna sorpresa.'], size=72,
                       tagline='Viaggio in tutto il Salento compreso. La galleria online resta aperta per un anno, i file sono vostri.')]),
        t.row([t.col([piano('Solo noi', '1.900', 'Un fotografo\nCerimonia, ritratti, torta\n400 foto in due versioni\nVenti foto '
                                                  'il giorno dopo', '6 ore')], wm='1-3', ws='1-1'),
               t.col([piano('Tutto il giorno', '2.900', 'Due fotografi\nDai preparativi all’ultimo ballo\n800 foto in due '
                                                        'versioni\nLa mezz’ora d’oro in spiaggia', '12 ore', True)],
                     wm='1-3', ws='1-1'),
               t.col([piano('Con il film', '4.200', 'Tutto il giorno, più\nUn videomaker e il drone\nIl film di tre minuti\n'
                                                    'Tre reel per i social', '12 ore')], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=20),
    ], bg=solid(SABBIA), padding='large', html_id='prezzi'))

    out.append(t.section([
        t.row([t.col([pellicole(t, 'pexels-15861733.jpg', 'Gli sposi abbracciati sulla spiaggia al tramonto')], wm='3-5', ws='1-1'),
               t.col([sechead(t, '03 — Due versioni', ['Naturali', 'e col vostro look.'], size=52, layout='stack', size_mobile=38),
                      text(t, '<p>Ogni foto del matrimonio ve la consegniamo due volte: com’è uscita, ritoccata appena, e col '
                              'look che avete scelto. Provatelo qui: toccate un rullino e tirate il divisore.</p>')],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='center'),
    ], padding='large'))

    out.append(t.section([
        t.row([t.col([sechead(t, '04 — Le domande', ['Quelle degli', 'sposi.'], size=48, layout='stack', size_mobile=34)],
                     wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quanto prima dobbiamo prenotare?', 'Per i sabati di giugno e settembre un anno prima. Per i venerdì e i '
                                                       'mesi di maggio e ottobre bastano sei mesi.'),
                   ('Fate le foto di gruppo?', 'Sì, ma velocemente: dieci minuti dopo la cerimonia, con una lista che ci date '
                                               'prima. Poi si torna a fare festa.'),
                   ('Cosa sono le venti foto del giorno dopo?', 'Le prime venti, già sviluppate, per mandarle ai parenti e '
                                                               'pubblicarle. Arrivano entro le sei di sera del giorno dopo.'),
                   ('Venite anche fuori Puglia?', 'Sì: Matera, la Costiera, le Cicladi. Il viaggio e le notti li paghiamo noi e '
                                                  've li mettiamo in conto al prezzo che costano.'),
               ], 'mat')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(SABBIA), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  BRAND E CONTENUTI
# ══════════════════════════════════════════════════════════════════════════════

def build_brand():
    t = Tpl(ID, 'brand')
    out = []
    out.append(t.section([
        t.row([t.col([label(t, 'Brand e contenuti · per chi vende qualcosa', color=SOLE),
                      headline(t, 'Contenuti <em>che si fermano a guardare.</em>', size=100, color=LATTE, tag='h1',
                               pad_bottom=18, accent=SOLE, spacing=-3),
                      text(t, '<p>Foto e video per negozi, ristoranti, marchi e case vacanza. Una giornata con noi e avete '
                              'contenuti per tre mesi di social.</p>', size=18, color=SOFT_LATTE),
                      button(t, 'Componi il pacchetto', '#pacchetto', kind='terra', pad_top=8)], wm='3-5', ws='1-1'),
               t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', valign='end'),
    ], bg=video_bg(V_ORO, overlay=44), padding='xlarge', style={'padding_top': 270, 'padding_bottom': 100}))

    def o(i, g, n, d, p, pred=False, badge='', punti=''):
        return {'id': i, 'gruppo': g, 'nome': n, 'descrizione': d, 'prezzo': p, 'immagine': '', 'badge': badge,
                'predefinita': pred, 'punti': punti, 'min': 0, 'max': 10}
    pacchetto = t.classic('configuratore', {
        'gruppi': [{'id': 'tempo', 'titolo': 'Quanto tempo', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'foto', 'titolo': 'Le foto', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'video', 'titolo': 'I video', 'tipo': 'uno', 'obbligatorio': False,
                    'aiuto': 'Verticali, da quindici secondi, montati con la musica.'},
                   {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': [
            o('mezza', 'tempo', 'Mezza giornata', 'Quattro ore, una location.', 450, True, punti='mesi:1'),
            o('intera', 'tempo', 'Giornata intera', 'Otto ore, fino a tre location.', 790, badge='la più scelta', punti='mesi:2'),
            o('due', 'tempo', 'Due giornate', 'Una per stagione: estate e inverno.', 1450, punti='mesi:4'),
            o('f40', 'foto', '40 foto', 'Prodotto, ambiente e persone.', 0, True, punti='mesi:1'),
            o('f80', 'foto', '80 foto', 'Abbastanza per un sito nuovo.', 220, punti='mesi:1'),
            o('f150', 'foto', '150 foto', 'Per un anno di post.', 420, punti='mesi:2'),
            o('r3', 'video', '3 reel', 'Uno a settimana per tre settimane.', 290, punti='mesi:1'),
            o('r6', 'video', '6 reel', 'Due al mese per tre mesi.', 520, badge='consigliato', punti='mesi:2'),
            o('r12', 'video', '12 reel', 'Uno a settimana per tre mesi.', 940, punti='mesi:3'),
            o('drone', 'extra', 'Il drone', 'Per masserie, case e cantine.', 180),
            o('modelli', 'extra', 'Due modelli', 'Ragazzi del posto, veri, non da catalogo.', 260),
            o('stile', 'extra', 'Styling', 'Oggetti, fiori e tessuti per il set.', 150),
            o('piano', 'extra', 'Il piano editoriale', 'Cosa pubblicare e quando, per tre mesi.', 240),
        ],
        'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'Il vostro pacchetto', 'periodo': '',
        'nota_prezzo': 'IVA inclusa · viaggio in tutto il Salento compreso · preventivo scritto in giornata',
        'mostra_differenze': True,
        'indicatori': [{'nome': 'Mesi di social coperti', 'id': 'mesi', 'massimo': 9}],
        'riepilogo': True, 'titolo_riepilogo': 'Il pacchetto', 'testo_azzera': 'Ricomincia',
        'testo_pulsante': 'Chiedi il preventivo', 'link_azione': PRENOTA + '?servizio=Brand&pacchetto={configurazione}#modulo',
        'campo_modulo': 'messaggio', 'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': LATTE,
        'colore_scelta': TERRA, 'colore_testo': INK, 'colore_tenue': TENUE, 'colore_prezzo': TERRA, 'sfondo_riepilogo': CACAO,
        'sfondo_badge': SOLE, 'colore_badge': CACAO, 'raggio_scheda': radius(24), 'ombra': 'none',
        'gruppo_font_family': 'heading', 'gruppo_font_size': 26, 'gruppo_font_weight': '400', 'gruppo_color': INK,
        'opzione_font_family': 'sans-serif', 'opzione_font_size': 17, 'opzione_font_weight': '600',
        'descrizione_font_family': 'sans-serif', 'descrizione_font_size': 14, 'descrizione_font_weight': '400',
        'prezzo_font_family': MONO, 'prezzo_font_size': 14, 'prezzo_font_weight': '500',
        'totale_font_family': 'heading', 'totale_font_size': 52, 'totale_font_weight': '400', 'totale_color': SOLE, 'gap': 12,
    })
    out.append(t.section([
        t.one([sechead(t, '01 — Il pacchetto', ['Componetelo voi,', 'il prezzo segue.'], size=72,
                       tagline='Scegliete tempo, foto e video: la barra dice quanti mesi di social avete coperto.')]),
        t.one([pacchetto]),
    ], bg=solid(SABBIA), padding='large', html_id='pacchetto', style={'padding_top': 140}))

    galleria = t.classic('progallery', {
        'images': [{'url': img(f), 'alt': a, 'caption': '', 'subtitle': '', 'text': ''} for f, a in [
            ('pexels-7038151.jpg', 'Prodotti per la pelle su un fondo arancione con l’ombra di una foglia'),
            ('pexels-36073022.jpg', 'Un cuoco al lavoro nella cucina calda e buia'),
            ('pexels-8279018.jpg', 'Una fioraia sorridente fra i suoi fiori'),
            ('pexels-9218700.jpg', 'Una mano che sistema blocchi colorati e un rossetto'),
            ('pexels-3184191.jpg', 'Una tavola apparecchiata vista dall’alto, con la pasta e il vino'),
            ('pexels-16537980.jpg', 'Un barista sorridente davanti al suo locale'),
            ('pexels-9748520.jpg', 'Prodotti di bellezza sulla sabbia'),
            ('pexels-38841199.jpg', 'Il cortile di pietra di una masseria con gli archi')]],
        'layout': 'expand', 'layout_family': 'classic', 'columns': 4, 'gap': 12, 'img_height': '420px',
        'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 20, 'mobile_columns': 2,
        'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
        'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
    })
    out.append(t.section([
        t.one([sechead(t, '02 — I contenuti', ['Fiorai, cucine,', 'creme e masserie.'], size=64,
                       tagline='Passate sopra le foto: si allargano. Le aziende del Salento con cui lavoriamo da anni.')]),
        t.one([galleria]),
    ], padding='large', html_id='contenuti'))

    out.append(t.section([
        t.row([t.col([label(t, 'Food · olio di Ugento · 1/250', color=SOLE),
                      headline(t, 'Anche l’olio <em>ha il suo momento.</em>', size=88, color=LATTE, tag='h2', pad_bottom=16,
                               accent=SOLE),
                      text(t, '<p>Per i ristoranti giriamo un video per ogni piatto del menù: dieci secondi, la mano che '
                              'serve, il vapore. Funziona più di qualunque foto.</p>', size=18, color=SOFT_LATTE)],
                     wm='1-2', ws='1-1'), t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='center'),
    ], bg=video_bg(V_OLIO, overlay=46, position='center center'), padding='xlarge',
        style={'padding_top': 200, 'padding_bottom': 200}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  PRENOTA
# ══════════════════════════════════════════════════════════════════════════════

def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [intro(t, 'Prenota', ['Una data, un posto,', 'un’idea.'],
                 '<p>Rispondiamo entro la giornata, anche su WhatsApp. D’estate lo studio si sposta: famiglie e coppie le '
                 'fotografiamo al tramonto, da Torre dell’Orso a Porto Cesareo.</p>', size=104,
                 bg=video_bg(V_MARE, overlay=46))]
    modulo = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'tel', 'label': 'Telefono o WhatsApp', 'name': 'telefono', 'placeholder': '', 'required': True,
             'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Il servizio', 'name': 'servizio', 'param': 'servizio', 'placeholder': '',
             'required': True, 'width': '1-2', 'options': '\n'.join(SERVIZI_NOMI), 'icon': ''},
            {'field_type': 'date', 'label': 'La data', 'name': 'data', 'placeholder': '', 'required': False, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'text', 'label': 'Dove', 'name': 'luogo', 'placeholder': 'Una masseria, una spiaggia, il vostro negozio',
             'required': False, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'textarea', 'label': 'Raccontateci', 'name': 'messaggio', 'param': 'pacchetto',
             'placeholder': 'Cosa avete in mente? Se avete composto un pacchetto, arriva da solo.', 'required': False,
             'width': '1-1', 'options': '', 'icon': ''},
        ],
        'email_subject': 'Solleone — una richiesta', 'submit_text': 'Manda', 'submit_icon': '',
        'success_message': 'Arrivata! Vi rispondiamo entro oggi, anche su WhatsApp.',
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': INK, 'label_size': '13', 'label_weight': '600',
        'label_transform': 'none', 'label_letter_spacing': '0', 'input_bg': LATTE, 'input_color': INK,
        'input_border_color': FILO, 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 14,
        'input_focus_border': TERRA, 'input_focus_shadow': False, 'gap': '18', 'submit_bg': TERRA, 'submit_color': ON_TERRA,
        'submit_radius': 999, 'submit_padding_x': '34', 'submit_padding_y': '16', 'submit_font_size': '16',
        'submit_font_weight': '700', 'submit_text_transform': 'none', 'submit_letter_spacing': '0',
        'submit_hover_bg': CACAO, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Il modulo', ['Due righe', 'e ci sentiamo.'], size=64, layout='stack', size_mobile=42),
                      text(t, '<p>Rispondiamo entro la giornata. Per i matrimoni teniamo la data per due settimane, senza '
                              'acconto, mentre ci pensate.</p>'),
                      contacts(t),
                      image(t, 'pexels-24431339.jpg', 'Il cortile di pietra con l’arco, nel centro di Lecce', ratio='4/3')],
                     wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1', style={'bg': solid(SABBIA), 'border_radius': radius(28), 'padding_top': 40,
                                                         'padding_right': 40, 'padding_bottom': 40, 'padding_left': 40})],
              layout='custom', custom='40,60', gap=64, valign='start'),
    ], padding='large', html_id='modulo', style={'padding_top': 120}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '16', 'height': '460', 'marker': True,
                                 'marker_popup': 'Solleone · via Libertini 31', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'voyager', 'border_radius': radius(28), 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Lo studio', ['Fra Porta Rudiae', 'e il Duomo.'], size=52, layout='stack', size_mobile=38),
                      text(t, '<p>Un piano terra con la volta a stella, in pietra leccese. Si parcheggia fuori dalle mura, in '
                              'viale dell’Università, e si arriva a piedi in cinque minuti.</p>'),
                      t.classic('desclist', {
                          'items': [{'term': 'Lunedì — sabato', 'definition': '10 — 13 e 17 — 20', 'icon': ''},
                                    {'term': 'La controra', 'definition': 'dalle 13 alle 17, solo su appuntamento', 'icon': ''},
                                    {'term': 'Domenica', 'definition': 'siamo a un matrimonio', 'icon': ''}],
                          'layout': 'vertical', 'term_color': TENUE, 'term_font_size': '13', 'term_font_weight': '600',
                          'definition_color': INK, 'definition_font_size': '17', 'separator': True, 'border_color': FILO},
                          style={'margin_top': 8})], wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], bg=solid(SABBIA), padding='large', html_id='dove'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404 · tutto bianco', color=SOLE, align='center'),
                     headline(t, 'Sovraesposta.', size=150, color=LATTE, tag='h1', align='center', pad_bottom=20, spacing=-5),
                     text(t, '<p>Troppa luce: questa pagina si è bruciata. Torniamo all’ombra.</p>', size=18, align='center',
                          color=SOFT_LATTE),
                     button(t, 'Torna alla home', HOME, kind='terra', align='center')]),
    ], bg=video_bg(V_CONTRORA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Creativi e portfolio', 'zone': 'Gira la ghiera',
    'tags': ['fotografia', 'matrimoni', 'brand', 'food', 'reel', 'tendenze', 'lecce', 'caldo'],
    'description': 'Uno studio fotografico a Lecce con nove servizi e l’occhio sulle mode: latte, cacao e terracotta, la '
                   'ghiera della fotocamera da girare per scegliere il servizio, le mode del 2026 che si svelano di lato, le '
                   'pellicole da provare sulla stessa foto col divisore, la controra in video, il quiz, il listino e il pacchetto '
                   'contenuti col prezzo.',
    'menu': {'name': 'Solleone — menu', 'items': [
        {'title': 'Servizi', 'url': SERVIZI}, {'title': 'Matrimoni', 'url': MATRIMONI}, {'title': 'Brand', 'url': BRAND},
        {'title': 'Prenota', 'url': PRENOTA}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Solleone — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Solleone — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Solleone — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Solleone — Home'},
        'servizi': {'file': 'servizi.json', 'type': 'page', 'title': 'Solleone — I servizi'},
        'matrimoni': {'file': 'matrimoni.json', 'type': 'page', 'title': 'Solleone — Matrimoni'},
        'brand': {'file': 'brand.json', 'type': 'page', 'title': 'Solleone — Brand e contenuti'},
        'prenota': {'file': 'prenota.json', 'type': 'page', 'title': 'Solleone — Prenota'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'servizi': {'title': 'I servizi', 'template': 'servizi', 'slug': 'servizi'},
        'matrimoni': {'title': 'Matrimoni', 'template': 'matrimoni', 'slug': 'matrimoni'},
        'brand': {'title': 'Brand e contenuti', 'template': 'brand', 'slug': 'brand'},
        'prenota': {'title': 'Prenota', 'template': 'prenota', 'slug': 'prenota'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 38, 'ring_color': HEX['primary'], 'ring_width': 1.5,
               'dot_size': 6, 'dot_color': HEX['secondary'], 'hot_scale': 2.0, 'hot_color': HEX['secondary'],
               'hot_fill': 'rgba(242,179,61,0.18)', 'blend_mode': 'normal', 'magnetic_selector': 'a, button',
               'pull_strength': 0.3, 'follow_easing': 0.2},
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Reddit Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Calistoga\', \'Cooper Black\', Georgia, serif',
            'font_family_mono': '\'Reddit Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '17px', 'line_height': '1.65', 'font_weight_body': '400', 'font_weight_heading': '400',
            'heading_line_height': '1.0', 'heading_letter_spacing': '-0.02em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '6rem', 'font_size_h2': '4rem', 'font_size_h3': '2.2rem',
            'font_size_h4': '1.5rem', 'font_size_h5': '1.2rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3rem',
            'font_size_h2_mobile': '2.3rem', 'font_size_h3_mobile': '1.6rem',
        },
        'google_fonts': ['Calistoga', 'Reddit Sans', 'Reddit Mono'],
        'google_fonts_weights': '400;500;600;700',
        'google_fonts_italic': [],
        'buttons': {'font_size': '16px', 'font_weight': '700', 'padding_x': '26px', 'padding_y': '15px', 'border_radius': '999px',
                    'text_transform': 'none', 'letter_spacing': '0', 'hover_brightness': '100'},
        'layout': {'border_radius': '20px', 'border_radius_large': '28px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': HEX['light'], 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '14px', 'field_padding': '14px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '13px', 'label_font_weight': '600', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 4, 'scale': 170},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'servizi.json': build_servizi(), 'matrimoni.json': build_matrimoni(), 'brand.json': build_brand(),
              'prenota.json': build_prenota(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
