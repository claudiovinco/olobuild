# -*- coding: utf-8 -*-
"""Genera il tema «Scia» (id scia): un motoscafo elettrico costruito a Clusane, sul lago d'Iseo. La pagina di un lancio.

Scuro come l'acqua di notte, con un solo colore di segnale, un verde lime da strumento di bordo (come i due toni dei
siti premiati del 2026: Floema, Why Zero). Titoli nel Michroma largo e tecnico, testo in Hanken Grotesk, i dati in Geist
Mono. Angoli vivi, fili sottili, grana leggera da pellicola, l'intestazione che galleggia sopra le immagini.

Dettagli: l'apertura «contatore» che si carica da 0 a 100 come la batteria, il cursore con l'anello in differenza, la
scritta SCIA piena d'acqua che si ingrandisce mentre si scende (Testo maschera con lo scorrimento), il nastro delle cifre.

Moduli: il motoscafo visto dall'alto in apertura, la plancia da toccare (Hotspot), l'acceleratore in video, «Quando esci in
barca?» che consiglia la batteria (Availability), i giorni alle prime consegne sul video del lago (Countdown), la scheda
tecnica, l'autonomia secondo la velocità (Grafico a linea), la barca da comporre col prezzo (Configuratore), quanto si
risparmia di benzina (Projector), la prova sul lago (Form con la configurazione), dove si prova in Europa (Mappa del
mondo) e come nasce uno scafo in sei settimane (Step Timeline).
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid, rgba  # noqa: E402

ID = 'scia'
NAME = 'Scia'

HEX = {
    'primary': '#D7F25C', 'primary_contrast': '#0C0F0E', 'secondary': '#5F7E78', 'secondary_contrast': '#ECEEE6',
    'accent': '#D7F25C', 'dark': '#070908', 'light': '#ECEEE6', 'muted': '#141817', 'muted_contrast': '#ECEEE6',
    'text': '#ECEEE6', 'text_muted': '#8C9490', 'background': '#0C0F0E', 'border': '#252B29', 'link': '#D7F25C',
}

OSSO = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
LIME = 'var(--olo-color-primary)'
ON_LIME = 'var(--olo-color-primary-contrast)'
LAGO = 'var(--olo-color-secondary)'
FONDO = 'var(--olo-color-background)'
SCURO = 'var(--olo-color-muted)'
NERO = 'var(--olo-color-dark)'
FILO = 'var(--olo-color-border)'
FILO_OSSO = mix(OSSO, 40)
SOFT = mix(OSSO, 80)
MONO = 'var(--olo-font-family-mono)'
SQ = radius(0)

ADDRESS = 'Via Risorgimento 18, 25049 Clusane d’Iseo (BS)'
PHONE = '030 000 6868'
PHONE_TEL = 'tel:+390300006868'
MAIL = 'prova@scia.example'
LAT, LNG = '45.6585', '10.0045'
HOME = '{{PAGINA:home}}'
MODELLO = '{{PAGINA:modello}}'
CONFIGURA = '{{PAGINA:configura}}'
PROVA = '{{PAGINA:prova}}'
CANTIERE = '{{PAGINA:cantiere}}'

V_DALLALTO = 'pexels-video-10391550'   # la barca vista dall'alto sull'acqua scura, ferma al centro
V_RUNABOUT = 'pexels-video-30016531'   # un motoscafo in mogano sul lago, i monti e i paesi
V_SCIA = 'pexels-video-29917254'       # la barca esce di campo e resta la scia sull'acqua nera
V_LEVA = 'pexels-video-8822534'        # la mano sulla leva dell'acceleratore
V_ACQUA = 'pexels-video-4563075'       # l'acqua scura che increspa
V_V = 'pexels-video-9168375'           # la scia a V sull'acqua d'argento

PHOTOS = [
    'pexels-31262564.jpg', 'pexels-31262565.jpg', 'pexels-31262569.jpg', 'pexels-9220747.jpg', 'pexels-7519145.jpg',
    'pexels-11652226.jpg', 'pexels-9398211.jpg', 'pexels-36167205.jpg', 'pexels-9398213.jpg', 'pexels-29715845.jpg',
    'pexels-33577215.jpg', 'pexels-40352634.jpg', 'pexels-16620689.jpg', 'pexels-16125986.jpg', 'pexels-16125979.jpg',
    'pexels-32262612.jpg', 'pexels-28794208.jpg', 'pexels-18875968.jpg', 'pexels-33646496.jpg', 'pexels-12420333.jpg',
    'pexels-21370750.jpg', 'pexels-38041792.jpg', 'pexels-8356413.jpg', 'pexels-11150527.jpg', 'pexels-32472665.jpg',
    'pexels-18328715.jpg', 'pexels-8669061.jpg', 'pexels-34212752.jpg', 'pexels-3823225.jpg', 'pexels-3823418.jpg',
    'pexels-37943368.jpg', 'pexels-13757273.jpg', 'pexels-5711772.jpg', 'pexels-6654759.jpg', 'pexels-7480729.jpg',
    'pexels-3392035.jpg', 'pexels-8319419.jpg', 'pexels-18275579.jpg', 'pexels-27505330.jpg', 'pexels-29685322.jpg',
    'pexels-12694077.jpg', 'pexels-7480249.jpg', 'pexels-7565175.jpg', 'pexels-38040016.jpg', 'pexels-3639037.jpg',
    'pexels-5718436.jpg', 'pexels-28348805.jpg',
]
VIDEOS = [V_DALLALTO, V_RUNABOUT, V_SCIA, V_LEVA, V_ACQUA, V_V]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=45, color=NERO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=40, color=NERO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=52, align='left', layout='split', size_mobile=30, color=OSSO):
    """Il dato in mono e lime sopra, il titolo largo sotto: la seconda riga più spenta."""
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': LIME, 'eyebrow_dot_color': LIME,
        'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '400',
        'headline_lines': [{'text': l, 'color': color if i % 2 == 0 else TENUE, 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.12,
        'headline_font_weight': '400', 'headline_align': align,
        'headline_font_size_tablet': max(30, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 15, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=36, color=OSSO, tag='h3', pad_bottom=10, align='left', upper=False, spacing=None):
    """La parte fra <em> diventa il «testo accento», in lime (il Michroma non ha corsivo)."""
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': upper, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': '400', 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': False, 'accent_color': LIME})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if size >= 36:
        o.update({'heading_font_size_tablet': str(int(size * 0.78)), 'heading_font_size_mobile': str(max(24, int(size * 0.52)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=TENUE, pad_bottom=14, align='left', pad_top=0):
    """I dati di bordo: monospazio, maiuscolo, spaziato."""
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 11, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '2'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=16, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='filo', align='left', pad_top=14):
    if kind == 'lime':
        o = {'bg_color': LIME, 'text_color': ON_LIME, 'hover_bg_color': OSSO, 'hover_text_color': ON_LIME}
    else:
        o = {'bg_color': 'transparent', 'text_color': OSSO, 'hover_bg_color': OSSO, 'hover_text_color': ON_LIME,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': FILO_OSSO, 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 11, 'font_weight': '600',
              'text_transform': 'uppercase', 'letter_spacing': '2.5'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', drift=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': False, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -30, 'position': 0}, {'value': 30, 'position': 100}]}} if drift else None)


def video(t, name, ratio='4:5'):
    return t.bare('video', {'source_type': 'file', 'video_url': '', 'file_url': img(name + '.mp4'),
                            'poster_image': img(name + '.jpg'), 'display_mode': ratio, 'aspect_ratio': ratio, 'cover_mode': False,
                            'object_position': 'center center', 'border_radius': 0, 'autoplay': True, 'muted': True,
                            'loop': True, 'controls': False, 'show_play_icon': False, 'facade': False})


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'vertical', 'term_color': TENUE, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': OSSO, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': PROVA + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': LIME, 'icon_size': '16', 'text_color': OSSO, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': OSSO, 'header_text_color_active': LIME, 'header_font_size': 17, 'header_font_weight': '400',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': LIME, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, lines, body, size=64):
    return t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=32)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=16, color=TENUE)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], padding='large', style={'padding_top': 190, 'padding_bottom': 64})


def nastro(t, items, color=LIME, bg='transparent'):
    return t.classic('marquee', {
        'content_type': 'text', 'separator': '   /   ', 'text_items': ' | '.join(items),
        'speed': '50', 'direction': 'left', 'pause_hover': False, 'gap': 30, 'velocity_skew': True,
        'bg_color': bg, 'text_color': color, 'font_size': 13, 'font_weight': '400', 'letter_spacing': 2,
        'text_transform': 'uppercase', 'font_family': MONO, 'font_style': 'normal', 'separator_color': TENUE, 'height': 56,
    })


CIFRE = ['6,80 m fuori tutto', '8 posti', 'motore 40 kW', '34 nodi in punta', '0 g di CO₂', 'ricarica in 3 ore',
         'scafo in mogano lamellare', 'fatto a Clusane d’Iseo']


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': 'SCIA', 'logo_text_size': '20', 'logo_text_color': OSSO,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 8, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': mix(FONDO, 86), 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': OSSO, 'hover_color': LIME, 'active_color': LIME,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2.5', 'item_gap': '34',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': LIME,
        'button_mode': 'last', 'btn_bg': LIME, 'btn_color': ON_LIME, 'btn_hover_color': ON_LIME,
        'btn_radius': SQ, 'btn_padding': {'top': 12, 'right': 20, 'bottom': 12, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '600', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2.5',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '#', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': NERO, 'mobile_text_color': OSSO, 'mobile_accent_color': LIME,
        'hamburger_style': 'minimal', 'hamburger_color': OSSO, 'mobile_font_size': '28', 'mobile_link_size': '28',
        'mobile_link_font': 'heading', 'mobile_link_weight': '400',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(OSSO, 12)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'SCIA', size=150, color=OSSO, tag='p', pad_bottom=0, spacing=24)
    claim = text(t, '<p>Motoscafi elettrici costruiti a mano a Clusane d’Iseo, sul cantiere dove dal 1958 si fanno barche per il '
                    'lago. Scia 6.8, prime consegne nell’aprile del 2027.</p>', size=15, color=TENUE, pad=(18, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': OSSO, 'link_hover_color': LIME, 'active_color': LIME,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('Il modello', MODELLO), ('Configura', CONFIGURA), ('Prova sul lago', PROVA), ('Il cantiere', CANTIERE),
                  ('Privacy', '/privacy-policy/')])
    orari = text(t, '<p>Cantiere: lunedì — venerdì 8 — 18<br>Prove sul lago: da marzo a ottobre, tutti i giorni</p><p>Dati '
                    'misurati sul lago d’Iseo a pieno carico, quattro persone a bordo.</p>', size=15, color=OSSO)
    legal = text(t, '<p>© 2026 Scia Cantiere Nautico s.r.l. · Clusane d’Iseo · Prezzi IVA inclusa, consegna al cantiere.</p>',
                 size=12, color=TENUE)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'youtube', 'url': 'https://youtube.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': TENUE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': LIME, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([label(t, 'Le pagine', color=LIME), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'Il cantiere', color=LIME), orari], wm='1-4', ws='1-2'),
        t.col([contacts(t), claim], wm='1-2', ws='1-1'),
    ], layout='custom', custom='25,25,50', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
                          'padding_top': 22, 'margin_top': 40})
    return [t.section([t.one([nastro(t, CIFRE, color=TENUE)], anim=None, style={'margin_bottom': 72}), top,
                       t.one([brand], anim='fade', style={'margin_top': 56}), bottom],
                      bg=solid(NERO), padding='large',
                      style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}})]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

PLANCIA = [
    ('40', '40', 'Il volante', 'Frassino curvato a vapore su un’anima d’acciaio: lo stesso disegno dei motoscafi del 1960.'),
    ('14', '52', 'La plancia', 'Mogano lamellare, dodici mani di vernice. Dietro lo sportello, lo schermo dell’autonomia.'),
    ('72', '74', 'I sedili', 'Pelle marina conciata a Arzignano, cuciture a mano: si asciuga in mezz’ora al sole.'),
    ('85', '88', 'Il ponte', 'Rovere oliato o teak certificato. Sotto, la batteria da 40, 60 o 80 kWh.'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = []

    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Scia 6.8 · motoscafo elettrico · lago d’Iseo', 'eyebrow_dot': True,
        'title': 'Silenziosa<br>come la sua scia.',
        'subtitle': 'Sei metri e ottanta di mogano lamellare, un motore elettrico da 40 kW e niente rumore: al minimo si sente '
                    'l’acqua contro lo scafo. Costruita a mano a Clusane.',
        'media_bg': {'type': 'video', 'video_url': img(V_DALLALTO + '.mp4'), 'video_poster': img(V_DALLALTO + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': NERO, 'overlay_top': 0.2, 'overlay_bottom': 0.85, 'overlay_sides': True, 'overlay_direction': '',
        'glow_on': False, 'frame_on': False, 'watermark_text': 'SCIA', 'watermark_color': rgba(HEX['light'], 0.05),
        'accent': LIME, 'text_color': OSSO, 'title_color': OSSO, 'subtitle_color': SOFT,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 76, 'title_font_weight': '400',
        'title_line_height': '1.08', 'title_letter_spacing': '-1', 'title_text_transform': 'none',
        'subtitle_font_size': 16, 'subtitle_max_width': 470,
        'min_height': '100vh', 'content_max_width': 1320, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 150, 'right': 48, 'bottom': 72, 'left': 48},
        'cta_text': 'Configura la tua', 'cta_url': CONFIGURA, 'cta_bg_color': LIME, 'cta_text_color': ON_LIME,
        'cta_radius': SQ, 'cta_size': '11', 'cta_style': 'filled', 'cta_font_weight': '600', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2.5',
        'cta2_text': 'Prova sul lago', 'cta2_url': PROVA, 'cta2_text_color': OSSO, 'cta2_style': 'ghost',
        'meta_text': '0 dB al minimo · 34 nodi · 6 ore di autonomia', 'scroll_hint': 'Scorri', 'scene_depth': 'none', 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))
    out.append(t.section([t.one([nastro(t, CIFRE)], anim=None)], width='fullbleed', padding='remove-vertical',
                         bg=solid(NERO),
                         style={'border': {'top': 1, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': FILO}}))

    out.append(t.section([
        t.row([t.col([label(t, '01 / Il silenzio'),
                      headline(t, 'Al minimo si sente <em>l’acqua</em>, non il motore.', size=46, tag='h2', pad_bottom=18),
                      text(t, '<p>Il motore elettrico gira sotto il ponte, chiuso in una cassa di sughero. A otto nodi fa '
                              'meno rumore di una conversazione; a trenta, meno di un’auto in città. Si parla senza alzare la '
                              'voce, si sentono gli uccelli di Monte Isola.</p>'),
                      specs(t, [('Al minimo', '0 dB sopra il fondo del lago'), ('A 20 nodi', '58 dB, come in un ufficio'),
                                ('Vibrazioni', 'nessuna: niente cardano, niente pistoni')])],
                     wm='1-2', ws='1-1'),
               t.col([video(t, V_LEVA, ratio='4:5'), label(t, 'La leva, da vicino', pad_top=14, pad_bottom=0)], wm='1-2', ws='1-1')],
              layout='50-50', gap=88, valign='center'),
    ], padding='large', html_id='silenzio', style={'padding_top': 140, 'padding_bottom': 120}))

    maschera = t.classic('textmask', {
        'text': 'SCIA', 'multiline': False, 'font_size': '300', 'font_size_tablet': '200', 'font_size_mobile': '96',
        'font_weight': '400', 'font_family': 'heading', 'text_transform': 'uppercase', 'letter_spacing': '0',
        'line_height': '1', 'text_align': 'center', 'video_url': img(V_SCIA + '.mp4'), 'video_poster': img(V_SCIA + '.jpg'),
        'video_opacity': '100', 'object_position': 'center center', 'min_height': 'min(560px, 62vw)',
        'tile_padding': {'top': 0, 'right': 20, 'bottom': 0, 'left': 20}, 'vertical_align': 'center',
        'bg_color': FONDO, 'mask_mode': 'video_behind_text', 'blend_mode': 'screen', 'text_fill': OSSO,
        'scroll_animate': True, 'scroll_start': '0', 'scroll_end': '100', 'scroll_scale': True, 'scroll_scale_from': '70',
        'scroll_scale_to': '150', 'scroll_opacity': False, 'scroll_blur': False, 'overlay_color': NERO, 'overlay_opacity': '0',
    })
    out.append(t.section([
        t.one([maschera], anim=None),
        centered(t, [text(t, '<p>A venti nodi la scia di una Scia è alta trenta centimetri: le barche ormeggiate non '
                             'ballano e le rive non si consumano. Su un lago conta.</p>', size=16, align='center')]),
    ], padding='remove-vertical', width='fullbleed', html_id='nome', style={'padding_top': 40, 'padding_bottom': 90}))

    numeri = t.classic('statstrip', {
        # spazi indivisibili: il Michroma è largo e sul telefono «0 dB» andava a capo
        'items': [{'value': '0\u00a0dB', 'label': 'al minimo'}, {'value': '34\u00a0kn', 'label': 'in punta, 63 km/h'},
                  {'value': '6\u00a0h', 'label': 'a 8 nodi, batteria da 60'}, {'value': '50′', 'label': 'per ricaricare in DC'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 36, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': OSSO, 'value_size': 44, 'value_weight': '400',
        'label_color': TENUE, 'label_size': 11, 'label_uppercase': True, 'label_weight': '400', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([t.one([numeri])], padding='default', style={'padding_top': 20, 'padding_bottom': 100}))

    plancia = t.classic('hotspot', {
        'image': img('pexels-33646496.jpg'), 'image_alt': 'La plancia in mogano di un motoscafo col volante e il sedile rosso',
        'image_height': '', 'aspect_ratio': '2/3', 'aspect_ratio_custom': '2/3', 'object_fit': 'cover',
        'object_position': 'center center',
        'markers': [{'pos_x': x, 'pos_y': y, 'title': ti, 'description': d, 'icon': 'plus',
                     'tooltip_position': 'left' if float(x) > 55 else 'right', 'link': '', 'link_label': ''}
                    for x, y, ti, d in PLANCIA],
        'border_radius': 0, 'marker_style': 'dot', 'marker_color': LIME, 'marker_text_color': ON_LIME, 'marker_size': 22,
        'pulse_animation': True, 'tooltip_bg': NERO, 'tooltip_color': OSSO, 'tooltip_width': 260,
    })
    out.append(t.section([
        t.row([t.col([plancia], wm='2-5', ws='1-1'),
               t.col([sechead(t, '03 / A bordo', ['Il posto', 'di guida.'], size=52, layout='stack'),
                      text(t, '<p>Toccate i punti verdi sulla plancia. Dentro è un motoscafo da lago come quelli degli anni '
                              'Sessanta: mogano, pelle, un volante sottile. La differenza è sotto il ponte.</p>'),
                      button(t, 'La scheda tecnica', MODELLO)], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=88, valign='center'),
    ], bg=solid(SCURO), padding='large', html_id='bordo'))

    quando = t.bare('availability', {
        'eyebrow': '04 / La batteria', 'heading': 'Quando esci in barca?',
        'intro': 'Toccate i momenti della settimana in cui uscite di solito: vi diciamo quale batteria vi basta.',
        'days': 'lun, mar, mer, gio, ven, sab, dom', 'bands': 'all’alba, di giorno, al tramonto',
        'count_label': 'Uscite a settimana', 'verdict_label': 'La batteria giusta',
        'tiers': [{'min': 0, 'label': 'Scia 40 · 40 kWh', 'text': 'Qualche giro alla settimana: ricarichi la domenica sera e basta.'},
                  {'min': 5, 'label': 'Scia 60 · 60 kWh', 'text': 'Esci spesso: con 60 kWh ricarichi una volta ogni tre o quattro giri.'},
                  {'min': 10, 'label': 'Scia 80 · 80 kWh', 'text': 'Vivi sul lago: 80 kWh e la ricarica rapida al pontile di casa.'}],
        'zone_accent': LIME, 'zone_on': ON_LIME, 'cell_bg': SCURO,
        'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': FILO}, 'align': 'left',
    })
    out.append(t.section([
        t.row([t.col([quando], wm='3-5', ws='1-1'),
               t.col([image(t, 'pexels-38040016.jpg', 'Celle di batteria allineate in una scatola, su fondo blu', ratio='4/5'),
                      label(t, 'Celle LFP, 4.000 cicli', pad_top=14, pad_bottom=0)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], padding='large', html_id='batteria', style={'padding_top': 130, 'padding_bottom': 130}))

    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2027-04-24T10:00', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': True, 'expired_message': 'Le prime Scia sono in acqua: venite a vederle a Clusane.',
        'label_days': 'giorni', 'label_hours': 'ore', 'label_minutes': 'minuti', 'label_seconds': 'secondi', 'separator': '',
        'text_color': OSSO, 'accent_color': LIME, 'number_font_size': 72, 'number_font_weight': '400',
        'label_font_size': '11', 'label_font_weight': '400', 'item_min_width': 140, 'item_bg_color': rgba('#070908', 0.55),
        'item_radius': SQ, 'item_padding': {'top': 26, 'right': 12, 'bottom': 22, 'left': 12},
        'tile_padding': {'top': 6, 'right': 0, 'bottom': 6, 'left': 0},
    }, style={})
    out.append(t.section([
        centered(t, [label(t, '05 / Le prime consegne · 24 aprile 2027', color=LIME, align='center'),
                     headline(t, 'Dodici barche nel 2027. <em>Sette sono già di qualcuno.</em>', size=44, tag='h2',
                              align='center', pad_bottom=30),
                     conto,
                     button(t, 'Prenota la prova', PROVA, kind='lime', align='center', pad_top=30)], side=12.5),
    ], bg=video_bg(V_RUNABOUT, overlay=55), padding='xlarge', html_id='consegne',
        style={'padding_top': 180, 'padding_bottom': 180}))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-7519145.jpg', 'Dall’alto: un motoscafo e la sua scia bianca sull’acqua scura',
                            ratio='16/9', drift=True)], wm='3-5', ws='1-1'),
               t.col([label(t, '06 / Da 118.000 €'),
                      headline(t, 'Componi la tua, <em>poi provala.</em>', size=44, tag='h2', pad_bottom=18),
                      text(t, '<p>Colore dello scafo, legno del ponte, pelle, batteria. Il prezzo cambia mentre scegli; la '
                              'configurazione arriva con te alla prova.</p>'),
                      button(t, 'Configura', CONFIGURA, kind='lime', pad_top=18)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 120, 'padding_bottom': 140}))
    return page(out, page_intro_enabled=True, page_intro_style='contatore', page_intro_text='SCIA',
                page_intro_caption='Carica · motore elettrico 40 kW', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=2200, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  IL MODELLO
# ══════════════════════════════════════════════════════════════════════════════

def build_modello():
    t = Tpl(ID, 'modello')
    out = [intro(t, 'Il modello', ['Scia 6.8.', 'Un motoscafo da lago.'],
                 '<p>Otto posti, un prendisole a prua e uno a poppa, il motore sotto il ponte. Pensata per l’Iseo, il Garda e il '
                 'Como: laghi dove il motore a scoppio è sempre più spesso vietato, e dove il silenzio vale.</p>')]
    out.append(t.section([t.one([image(t, 'pexels-21370750.jpg', 'La prua di legno di una barca con la cima, sull’acqua',
                                       ratio='21/9', position='center 55%', drift=True)], anim='fade')],
                         padding='remove-vertical', width='wide'))

    spec = [('Dimensioni', 'Lunghezza fuori tutto', '6,80', 'm'), ('Dimensioni', 'Larghezza', '2,30', 'm'),
            ('Dimensioni', 'Pescaggio', '0,45', 'm'), ('Dimensioni', 'Peso a vuoto', '1.480', 'kg'),
            ('Propulsione', 'Motore', '40 (60 di picco)', 'kW'), ('Propulsione', 'Velocità massima', '34', 'nodi'),
            ('Propulsione', 'Velocità di crociera', '20', 'nodi'), ('Energia', 'Batteria', '40 · 60 · 80', 'kWh'),
            ('Energia', 'Ricarica AC 22 kW', '3', 'ore'), ('Energia', 'Ricarica DC 50 kW', '50', 'minuti'),
            ('A bordo', 'Posti', '8', 'persone'), ('A bordo', 'Omologazione', 'CE categoria C', '')]
    scheda = t.classic('schedatecnica', {
        'codice': 'S-68', 'nome': 'Scia 6.8 · motoscafo elettrico', 'etichetta': 'Prime consegne aprile 2027',
        'descrizione': 'Scafo in mogano lamellare su ordinate di frassino, motore elettrico a magneti permanenti, batteria LFP '
                       'sotto il ponte. Collaudata sul lago d’Iseo con quattro persone a bordo.',
        'immagine': img('pexels-38041792.jpg'), 'titolo_specifiche': 'I dati',
        'specifiche': [{'id': 's68-%d' % i, 'gruppo': g, 'nome': n, 'valore': v, 'unita': u} for i, (g, n, v, u) in enumerate(spec)],
        'titolo_documenti': '', 'documenti': [], 'certificazioni': 'CE categoria C · ISO 12215 · IP67 per la batteria',
        'cta_testo': 'Configura la tua', 'cta_url': CONFIGURA, 'cta_codice': False, 'cta_parametro': 'modello',
        'dati_strutturati': True, 'accento': LIME, 'sfondo_scheda': SCURO, 'bordo_scheda': {}, 'shadow': 'none',
        'colore_linee': FILO, 'righe_alternate': False, 'nome_font_size': 30, 'nome_font_weight': '400',
        'nome_color': OSSO, 'codice_color': LIME, 'descrizione_color': SOFT, 'gruppo_color': LIME, 'voce_color': TENUE,
        'valore_color': OSSO, 'unita_color': TENUE,
        'raggio': radius(0), 'raggio_pulsante': radius(0), 'padding_scheda': {'top': 44, 'right': 44, 'bottom': 44, 'left': 44},
        'img_ratio': '4/5', 'img_ratio_custom': '4/5', 'img_fit': 'cover', 'img_object_position': 'center center',
        'foto_posizione': 'sinistra', 'larghezza_foto': 40, 'gap': 48, 'colonne_specifiche': 2,
    })
    out.append(t.section([t.one([scheda])], padding='large', html_id='scheda'))

    curva = t.classic('chart', {
        'chart_type': 'line', 'index_axis': 'x',
        'items': [{'label': '5 kn', 'value': '11', 'color': LIME}, {'label': '8 kn', 'value': '6.2', 'color': LIME},
                  {'label': '12 kn', 'value': '4.1', 'color': LIME}, {'label': '20 kn', 'value': '2.6', 'color': LIME},
                  {'label': '26 kn', 'value': '1.8', 'color': LIME}, {'label': '34 kn', 'value': '1.1', 'color': LIME}],
        'chart_height': '340', 'show_legend': False, 'tooltip_enabled': True, 'tooltip_bg': NERO, 'tooltip_text_color': OSSO,
        'tooltip_suffix': ' ore', 'tooltip_prefix': '', 'animate': True, 'bar_radius': '0', 'border_width': 2,
        'border_color_override': LIME, 'fill_area': True, 'point_radius': 4, 'point_hover_radius': 7, 'point_style': 'circle',
        'tension': 0.35, 'grid_color': FILO, 'text_color': TENUE, 'tick_font_size': 11, 'show_x_grid': False,
        'show_y_grid': True, 'show_x_border': True, 'show_y_border': False, 'begin_at_zero': True,
        'dataset_label': 'Autonomia con la batteria da 60 kWh', 'x_label': 'velocità', 'y_label': 'ore',
    })
    out.append(t.section([
        t.row([t.col([sechead(t, 'L’autonomia', ['Più piano,', 'più lontano.'], size=44, layout='stack', size_mobile=28),
                      text(t, '<p>Con la batteria da 60 kWh: undici ore a passo d’uomo, sei a otto nodi per un giro dell’Iseo '
                              'con tre soste, un’ora abbondante tutta manetta. I numeri sono misurati, non calcolati.</p>')],
                     wm='1-3', ws='1-1'),
               t.col([curva], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='center'),
    ], bg=solid(SCURO), padding='large'))

    out.append(t.section([
        t.row([t.col([image(t, f, a, ratio='3/4')], wm='1-3', ws='1-1') for f, a in [
            ('pexels-11150527.jpg', 'Il corrimano di legno di una barca e i suoi pomoli, sul lago'),
            ('pexels-8356413.jpg', 'Cime annodate su un ponte bianco con le doghe scure'),
            ('pexels-32472665.jpg', 'Un cavo di ricarica collegato alla colonnina')]],
              layout='33-33-33', gap=16),
    ], padding='large', style={'padding_bottom': 40}))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande tecniche', ['Batteria,', 'ricarica, inverno.'], size=40, layout='stack', size_mobile=28)],
                     wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quanto dura la batteria?', 'Le celle LFP reggono quattromila cicli completi: con cento uscite all’anno sono più di '
                                                'trent’anni. La garanzia è di dieci.'),
                   ('Dove si ricarica?', 'A una presa industriale da 22 kW al pontile, in tre ore; ai porti con colonnina DC in '
                                         'cinquanta minuti. Sull’Iseo ce ne sono già sei.'),
                   ('E d’inverno?', 'La barca va a riposo al cantiere: la batteria resta al 50% e la controlliamo noi una volta al mese.'),
                   ('Serve la patente nautica?', 'Sì, oltre i 40 cavalli: la Scia 6.8 ne ha 54 di picco. C’è una versione da 30 kW per '
                                                 'chi non ce l’ha.'),
               ], 'tec')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  CONFIGURA
# ══════════════════════════════════════════════════════════════════════════════

def build_configura():
    t = Tpl(ID, 'configura')
    out = [intro(t, 'Configura · da 118.000 €', ['La tua Scia,', 'pezzo per pezzo.'],
                 '<p>Scegliete scafo, ponte, pelle e batteria: il prezzo si aggiorna e la barra dice quanta autonomia avete. Alla '
                 'fine la configurazione arriva con voi alla prova sul lago.</p>')]

    def o(i, g, n, d, p, pred=False, badge='', punti=''):
        return {'id': i, 'gruppo': g, 'nome': n, 'descrizione': d, 'prezzo': p, 'immagine': '', 'badge': badge,
                'predefinita': pred, 'punti': punti, 'min': 0, 'max': 10}
    barca = t.classic('configuratore', {
        'gruppi': [{'id': 'scafo', 'titolo': 'Lo scafo', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'ponte', 'titolo': 'Il ponte', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'pelle', 'titolo': 'I sedili', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'batteria', 'titolo': 'La batteria', 'tipo': 'uno', 'obbligatorio': True,
                    'aiuto': 'Non sapete quale? In home c’è «Quando esci in barca?»: ve lo dice lui.'},
                   {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': [
            o('mogano', 'scafo', 'Mogano naturale', 'Dodici mani di vernice, si vede la vena del legno.', 0, True),
            o('notte', 'scafo', 'Mogano e blu notte', 'Opera morta laccata, coperta in mogano.', 3800),
            o('isola', 'scafo', 'Grigio Monte Isola', 'Laccato opaco, il colore della pietra del lago.', 3800),
            o('rovere', 'ponte', 'Rovere oliato', 'Doghe da 8 cm, si scurisce al sole.', 0, True),
            o('teak', 'ponte', 'Teak certificato', 'Da piantagioni FSC, non serve oliarlo.', 2400),
            o('sughero', 'ponte', 'Sughero nero', 'Morbido sotto i piedi nudi, non scotta.', 1200, badge='nuovo'),
            o('naturale', 'pelle', 'Pelle naturale', 'Color cuoio, prende la patina.', 0, True),
            o('blu', 'pelle', 'Pelle marina blu notte', 'Trattata contro sale e sole.', 1600),
            o('tecnico', 'pelle', 'Tessuto tecnico grigio', 'Il più pratico: si lava col tubo.', -800),
            o('b40', 'batteria', '40 kWh', 'Quattro ore a 8 nodi.', 0, True, punti='autonomia:40'),
            o('b60', 'batteria', '60 kWh', 'Sei ore a 8 nodi. La più scelta.', 9800, badge='consigliata', punti='autonomia:62'),
            o('b80', 'batteria', '80 kWh', 'Otto ore e mezza a 8 nodi.', 18500, punti='autonomia:85'),
            o('dc', 'extra', 'Ricarica rapida DC', '50 minuti dallo 0 all’80%.', 3200),
            o('tenda', 'extra', 'Tendalino', 'In tela grigia, si apre in un minuto.', 1900),
            o('carrello', 'extra', 'Carrello stradale', 'Omologato, per cambiare lago.', 4600),
            o('inverno', 'extra', 'Rimessaggio del primo inverno', 'Al cantiere, con la batteria controllata.', 900),
        ],
        'prezzo_base': 118000, 'valuta': '€', 'testo_totale': 'La tua Scia', 'periodo': '',
        'nota_prezzo': 'IVA inclusa · consegna al cantiere di Clusane · acconto del 10%', 'mostra_differenze': True,
        'indicatori': [{'nome': 'Autonomia', 'id': 'autonomia', 'massimo': 100}],
        'riepilogo': True, 'titolo_riepilogo': 'La configurazione', 'testo_azzera': 'Ricomincia',
        'testo_pulsante': 'Prova questa Scia', 'link_azione': PROVA + '?configurazione={configurazione}#modulo',
        'campo_modulo': 'configurazione', 'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': SCURO,
        'colore_scelta': LIME, 'colore_testo': OSSO, 'colore_tenue': TENUE, 'colore_prezzo': LIME, 'sfondo_riepilogo': NERO,
        'sfondo_badge': LIME, 'colore_badge': ON_LIME, 'raggio_scheda': radius(0), 'ombra': 'none',
        'gruppo_font_family': 'heading', 'gruppo_font_size': 20, 'gruppo_font_weight': '400', 'gruppo_color': OSSO,
        'opzione_font_family': 'sans-serif', 'opzione_font_size': 16, 'opzione_font_weight': '500',
        'descrizione_font_family': 'sans-serif', 'descrizione_font_size': 13, 'descrizione_font_weight': '400',
        'prezzo_font_family': MONO, 'prezzo_font_size': 13, 'prezzo_font_weight': '400',
        'totale_font_family': MONO, 'totale_font_size': 40, 'totale_font_weight': '400', 'totale_color': LIME, 'gap': 12,
    })
    out.append(t.section([t.one([barca])], padding='large', html_id='configuratore', style={'padding_top': 20}))

    risparmio = t.classic('projector', {
        'eyebrow': 'Il conto della benzina', 'heading': 'Quanto <em>non</em> spendi in un anno?',
        'intro': 'Un motoscafo a benzina da 150 cavalli beve 25 litri l’ora a crociera; una Scia, 40 kWh. La differenza, '
                 'ora per ora, è di circa 36 €.',
        'input_label': 'Ore in barca all’anno', 'min': '20', 'max': '300', 'step': '10', 'value': '80', 'input_unit': 'ore',
        'show_contrib': True, 'years': '36', 'rate': '0', 'out_caption': 'Risparmiati in un anno, rispetto alla benzina',
        'currency': '€ ', 'result_suffix': '', 'decimals': 0,
        'note': 'Benzina a 1,95 € al litro, elettricità a 0,30 € al kWh. La manutenzione del motore elettrico costa un decimo.',
        'list_items': [], 'list_base': 1, 'zone_accent': LIME, 'card_bg': SCURO, 'align': 'left',
        'tile_padding': {'top': 48, 'right': 48, 'bottom': 48, 'left': 48}, 'border_radius': 0,
        'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': FILO}, 'shadow': 'none',
    })
    out.append(t.section([
        t.row([t.col([risparmio], wm='3-5', ws='1-1'),
               t.col([video(t, V_V, ratio='4:5'), label(t, 'Venti nodi, zero litri', pad_top=14, pad_bottom=0)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], bg=solid(SCURO), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  PROVA SUL LAGO
# ══════════════════════════════════════════════════════════════════════════════

def build_prova():
    t = Tpl(ID, 'prova')
    out = []
    modulo = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome e cognome', 'name': 'nome', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'tel', 'label': 'Telefono', 'name': 'telefono', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Il pontile', 'name': 'pontile', 'placeholder': '', 'required': True, 'width': '1-2',
             'options': 'Clusane, al cantiere\nIseo, porto turistico\nLovere, darsena\nSale Marasino, di fronte a Monte Isola', 'icon': ''},
            {'field_type': 'date', 'label': 'Il giorno', 'name': 'giorno', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'L’ora', 'name': 'ora', 'placeholder': '', 'required': True, 'width': '1-2',
             'options': 'All’alba, 7:00\nIn mattinata, 10:00\nNel pomeriggio, 15:00\nAl tramonto, 19:00', 'icon': ''},
            {'field_type': 'textarea', 'label': 'La configurazione', 'name': 'configurazione', 'param': 'configurazione',
             'placeholder': 'Arriva da sola se l’avete composta nella pagina Configura', 'required': False, 'width': '1-1',
             'options': '', 'icon': ''},
        ],
        'email_subject': 'Prova sul lago — Scia 6.8', 'submit_text': 'Prenota la prova', 'submit_icon': '',
        'success_message': 'Fatto. Vi confermiamo il pontile e l’ora entro domani: portate una giacca, sul lago fa fresco.',
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': OSSO, 'label_size': '11', 'label_weight': '400',
        'label_transform': 'uppercase', 'label_letter_spacing': '2', 'input_bg': 'transparent', 'input_color': OSSO,
        'input_border_color': FILO_OSSO, 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 0,
        'input_focus_border': LIME, 'input_focus_shadow': False, 'gap': '20', 'submit_bg': LIME, 'submit_color': ON_LIME,
        'submit_radius': 0, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '11',
        'submit_font_weight': '600', 'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2.5',
        'submit_hover_bg': OSSO, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Prova sul lago · gratis', ['Un’ora', 'al timone.'], size=64, layout='stack', size_mobile=34),
                      text(t, '<p>Da marzo a ottobre, tutti i giorni, da quattro pontili dell’Iseo. Guidate voi, con uno di noi '
                              'accanto: si esce dal porto in silenzio e si torna quando volete, entro un’ora.</p>', pad=(0, 0, 36, 0)),
                      modulo], wm='3-5', ws='1-1'),
               t.col([image(t, 'pexels-12694077.jpg', 'Una persona cammina su un pontile di legno nella nebbia del lago',
                            ratio='3/4'), label(t, 'Clusane, alle sette del mattino', pad_top=14, pad_bottom=0)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='start'),
    ], padding='large', html_id='modulo', style={'padding_top': 190}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '12', 'height': '480', 'marker': True,
                                 'marker_popup': 'Scia · il cantiere di Clusane', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'dark', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Dove', ['Quattro pontili,', 'un lago.'], size=44, layout='stack', size_mobile=28),
                      contacts(t),
                      text(t, '<p>Il cantiere è a Clusane, sulla sponda sud: dall’uscita Rovato dell’A4 sono venti minuti. Per '
                              'gli altri pontili vi aspettiamo con la barca già in acqua.</p>', size=15, pad=(16, 0, 0, 0))],
                     wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], bg=solid(SCURO), padding='large', html_id='dove'))

    mondo = t.classic('worldmap', {
        'stati': {'IT': 2, 'CH': 2, 'FR': 1, 'AT': 1, 'DE': 1, 'SI': 1},
        'mostra_legenda': True, 'mostra_numeri': True, 'etichetta_1': 'Prove dal 2027',
        'etichetta_2': 'Prove e assistenza oggi', 'colore_1': mix(LIME, 35, 'transparent'), 'colore_2': LIME,
        'colore_vuoto': mix(OSSO, 8), 'colore_confini': FONDO, 'spessore_confini': 0.6, 'legenda_posizione': 'sotto',
        'legenda_allineamento': 'left', 'legenda_font_family': MONO, 'legenda_font_size': '12', 'legenda_font_weight': '400',
        'legenda_color': TENUE,
    })
    out.append(t.section([
        t.one([sechead(t, 'Fuori dall’Iseo', ['Sui laghi', 'd’Europa.'], size=44,
                       tagline='Como, Garda, Lugano e Zurigo hanno già un pontile con la Scia di prova; Annecy, il Wolfgangsee, lo '
                               'Starnberger See e Bled dal 2027.')]),
        t.one([mondo]),
    ], padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  IL CANTIERE
# ══════════════════════════════════════════════════════════════════════════════

STORIA = [
    ('1958', 'Il nonno Battista', 'Costruisce a Clusane barche di legno per i pescatori di tinca, una ogni due mesi.'),
    ('1981', 'I motoscafi', 'Il figlio Giulio passa al mogano lamellare: quaranta motoscafi in vent’anni, ne navigano ancora trenta.'),
    ('2019', 'Il prototipo', 'Matteo, il nipote, e l’ingegnere Luca Ferri montano un motore elettrico su uno scafo del 1987. '
     'Funziona.'),
    ('2024', 'La Scia 6.8', 'Disegnata da capo intorno alla batteria, collaudata due estati sull’Iseo.'),
    ('2027', 'Le prime dodici', 'Consegna il 24 aprile, al cantiere, con la banda di Clusane.'),
]

FASI = [
    ('01', 'Settimane 1–2', 'Le ordinate', 'Il frassino curvato a vapore, una costola alla volta, sullo scalo.', 'pexels-5711772.jpg'),
    ('02', 'Settimane 3–4', 'Il fasciame', 'Tre strati di mogano incollati a fibre incrociate: leggero e rigido come una vela.', 'pexels-7480729.jpg'),
    ('03', 'Settimana 5', 'La batteria', 'Le celle entrano sotto il ponte, in una vasca stagna raffreddata con l’acqua del lago.', 'pexels-3823225.jpg'),
    ('04', 'Settimana 5', 'I sedili', 'La pelle tagliata e cucita a mano, i bordi lisciati con la carta fine.', 'pexels-6654759.jpg'),
    ('05', 'Settimana 6', 'Il collaudo', 'Dodici mani di vernice, poi cento chilometri sull’Iseo: di notte, col vento, a pieno carico.', 'pexels-18275579.jpg'),
    ('06', 'Il giorno dopo', 'La consegna', 'Al pontile del cantiere, con la batteria carica e il nome di chi l’ha costruita.', 'pexels-29685322.jpg'),
]


def build_cantiere():
    t = Tpl(ID, 'cantiere')
    out = [intro(t, 'Il cantiere · Clusane d’Iseo', ['Tre generazioni', 'di barche da lago.'],
                 '<p>Dal 1958 nello stesso capannone in riva al lago, a Clusane. Prima barche da pesca, poi motoscafi di mogano, '
                 'oggi una barca elettrica che sembra uno di quelli.</p>')]

    def persona(foto, nome, ruolo, bio, pos):
        return [image(t, foto, nome + ', ' + ruolo.lower(), ratio='4/5', position=pos),
                headline(t, nome, size=22, tag='h3', pad_bottom=2),
                label(t, ruolo, color=LIME, pad_bottom=8, pad_top=6),
                text(t, '<p>' + bio + '</p>', size=15)]
    out.append(t.section([
        t.row([t.col(persona('pexels-7480249.jpg', 'Matteo Bettoni', 'Maestro d’ascia',
                             'Terza generazione: il mestiere l’ha imparato dal padre Giulio. Il fasciame di ogni Scia lo controlla '
                             'lui, con la mano.',
                             'center 30%'), wm='1-3', ws='1-1'),
               t.col(persona('pexels-7565175.jpg', 'Luca Ferri', 'Ingegnere, il motore',
                             'Ha studiato al Politecnico di Milano e lavorato sei anni sulle batterie delle auto. È venuto sul lago '
                             'per questo.',
                             'center 35%'), wm='1-3', ws='1-1'),
               t.col([label(t, 'Il cantiere oggi'),
                      text(t, '<p>Undici persone: quattro falegnami, due elettricisti, un verniciatore, chi collauda e chi vi '
                              'accompagna alla prova. Dodici barche all’anno, non di più.</p><p>Il capannone si visita il primo '
                              'sabato del mese, alle dieci.</p>', size=16),
                      button(t, 'Prenota la visita', PROVA, pad_top=10)], wm='1-3', ws='1-1')],
              layout='33-33-33', gap=40, valign='start'),
    ], padding='large'))

    fasi = t.classic('step-timeline', {
        'items': [{'counter': n, 'tag_text': quando, 'tag_dot_color': LIME, 'media_type': 'image', 'media_image': img(f),
                   'media_label': '', 'media_content': '', 'media_bg': NERO, 'media_color': LIME, 'pre_title': '',
                   'title': titolo, 'title_accent': '', 'title_accent_italic': False, 'title_after': '', 'title_after_italic': False,
                   'description': d, 'footer_value': '', 'footer_label': '', 'separator_text': ''} for n, quando, titolo, d, f in FASI],
        'show_timeline': True, 'timeline_line_color': FILO, 'timeline_dot_color': LIME, 'timeline_dot_size': 10,
        'timeline_height': 1, 'timeline_margin_bottom': 44, 'counter_font_family': 'heading', 'counter_size': 40,
        'counter_color': LIME, 'counter_italic': False, 'counter_weight': '400', 'tag_size': 11, 'tag_color': TENUE,
        'media_aspect_ratio': '4/3', 'media_object_position': 'center center', 'media_radius': radius(0),
        'media_radius_hover': radius(0), 'media_shadow': 'none', 'show_media_label': False, 'pre_title_size': 11,
        'pre_title_color': LIME, 'title_font_family': 'heading', 'title_size': 18, 'title_weight': '400', 'title_color': OSSO,
        'description_color': TENUE,
    })
    out.append(t.section([
        t.one([sechead(t, 'Come nasce', ['Sei settimane', 'per uno scafo.'], size=48,
                       tagline='Ogni Scia ha un numero inciso sullo specchio di poppa e il nome di chi l’ha costruita.')]),
        t.one([fasi]),
    ], bg=solid(SCURO), padding='large'))

    filo = {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}
    righe = [t.row([t.col([label(t, anno, color=LIME, pad_bottom=0, pad_top=6)], wm='1-6', ws='1-1'),
                    t.col([headline(t, titolo, size=20, tag='h3', pad_bottom=0)], wm='1-3', ws='1-1'),
                    t.col([text(t, '<p>' + frase + '</p>', size=15)], wm='1-2', ws='1-1')],
                   layout='custom', custom='16,34,50', gap=24, valign='start',
                   style={'border': filo, 'padding_top': 22, 'padding_bottom': 18})
             for anno, titolo, frase in STORIA]
    out.append(t.section([
        t.one([sechead(t, 'La storia', ['Dal 1958,', 'sullo stesso scalo.'], size=48,
                       tagline='Tre generazioni, tre materiali, un solo lago.')]),
    ] + righe + [t.one([], anim=None, style={'border': filo})], padding='large'))

    out.append(t.section([
        centered(t, [label(t, 'Il collaudo', color=LIME, align='center'),
                     headline(t, 'Quaranta controlli, <em>tutti sul lago.</em>', size=48, tag='h2', align='center', pad_bottom=18),
                     text(t, '<p>Prima di consegnarla, ogni Scia fa cento chilometri sull’Iseo: di notte, col vento, a pieno '
                             'carico. Se una cosa non ci convince, si torna allo scalo.</p>', size=16, align='center', color=SOFT)]),
    ], bg=video_bg(V_ACQUA, overlay=35), padding='xlarge', style={'padding_top': 200, 'padding_bottom': 200}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404 · posizione sconosciuta', color=LIME, align='center'),
                     headline(t, 'Fuori rotta.', size=96, tag='h1', align='center', pad_bottom=20),
                     text(t, '<p>Questa pagina non c’è. Il lago, invece, è sempre lì.</p>', size=16, align='center', color=SOFT),
                     button(t, 'Torna in porto', HOME, kind='lime', align='center')]),
    ], bg=video_bg(V_ACQUA, overlay=50), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Tecnologia', 'zone': 'Quando esci in barca?',
    'tags': ['barche', 'elettrico', 'lancio prodotto', 'configuratore', 'lago d’iseo'],
    'description': 'Il lancio di un motoscafo elettrico costruito sul lago d’Iseo, scuro come l’acqua di notte con un solo verde '
                   'da strumento di bordo: l’apertura che si carica come la batteria, la scritta piena d’acqua che cresce '
                   'scorrendo, la plancia da toccare, «quando esci in barca?» che sceglie la batteria, il configuratore col prezzo e '
                   'la prova sul lago.',
    'menu': {'name': 'Scia — menu', 'items': [
        {'title': 'Il modello', 'url': MODELLO}, {'title': 'Configura', 'url': CONFIGURA}, {'title': 'Il cantiere', 'url': CANTIERE},
        {'title': 'Prova sul lago', 'url': PROVA}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Scia — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Scia — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Scia — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Scia — Home'},
        'modello': {'file': 'modello.json', 'type': 'page', 'title': 'Scia — Il modello'},
        'configura': {'file': 'configura.json', 'type': 'page', 'title': 'Scia — Configura'},
        'prova': {'file': 'prova.json', 'type': 'page', 'title': 'Scia — Prova sul lago'},
        'cantiere': {'file': 'cantiere.json', 'type': 'page', 'title': 'Scia — Il cantiere'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'modello': {'title': 'Il modello', 'template': 'modello', 'slug': 'modello'},
        'configura': {'title': 'Configura', 'template': 'configura', 'slug': 'configura'},
        'prova': {'title': 'Prova sul lago', 'template': 'prova', 'slug': 'prova'},
        'cantiere': {'title': 'Il cantiere', 'template': 'cantiere', 'slug': 'cantiere'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 34, 'ring_color': HEX['light'], 'ring_width': 1,
               'dot_size': 5, 'dot_color': HEX['primary'], 'hot_scale': 2.0, 'hot_color': HEX['primary'],
               'hot_fill': rgba(HEX['primary'], 0.12), 'blend_mode': 'difference', 'magnetic_selector': 'a, button',
               'pull_strength': 0.3, 'follow_easing': 0.2},
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Hanken Grotesk\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Michroma\', \'Eurostile\', \'Arial\', sans-serif',
            'font_family_mono': '\'Geist Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '400',
            'heading_line_height': '1.12', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '4.5rem', 'font_size_h2': '3rem', 'font_size_h3': '1.8rem',
            'font_size_h4': '1.3rem', 'font_size_h5': '1.1rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.4rem',
            'font_size_h2_mobile': '1.9rem', 'font_size_h3_mobile': '1.4rem',
        },
        'google_fonts': ['Michroma', 'Hanken Grotesk', 'Geist Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': [],
        'buttons': {'font_size': '11px', 'font_weight': '600', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '2.5px', 'hover_brightness': '100'},
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
              'modello.json': build_modello(), 'configura.json': build_configura(), 'prova.json': build_prova(),
              'cantiere.json': build_cantiere(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
