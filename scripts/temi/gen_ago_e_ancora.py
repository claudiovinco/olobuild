# -*- coding: utf-8 -*-
"""Genera il tema «Ago e Ancora» (id ago-e-ancora): studio di tatuaggi a Livorno, nel quartiere Venezia Nuova.

Scuro come l'inchiostro, col colore del flash tradizionale: vermiglio, senape e verde, osso al posto del bianco.
Titoli in un gotico leggibile (Grenze Gotisch), testo in Bitter, le firme scritte a mano (Mr Dafoe). Riquadri ad
angoli vivi col filo d'osso come un foglio di flash, pennellate d'inchiostro fra le sezioni (Shape Divider
«Pennellata»), grana sulla carta.

Moduli: il manifesto che si accende parola per parola (Scrub testo), i lavori filtrati per stile (Portfolio), chi
c'è in studio oggi (Presence Grid), la mappa del dolore sulla schiena (Hotspot), i giorni al venerdì 13 del flash
day (Countdown sull'inchiostro in video), lo studio in quattro video (Video Playlist), il flash del mese da
grattare col codice (Scratch FX), il prezzo dalle ore di seduta (Projector), la guarigione giorno per giorno
(Switcher), la richiesta in due passi con la foto di riferimento (Form) e il percorso della prenotazione
(Progress Tracker); i lavori di ogni artista che si allargano al passaggio (Pro Gallery «Espandi»).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, lib, mix, radius, corners, solid, rgba  # noqa: E402

ID = 'ago-e-ancora'
NAME = 'Ago e Ancora'

HEX = {
    'primary': '#E04A2F', 'primary_contrast': '#140F0E', 'secondary': '#2E8C78', 'secondary_contrast': '#F1E7D6',
    'accent': '#E9B13F', 'dark': '#0B0909', 'light': '#F1E7D6', 'muted': '#1D1817', 'muted_contrast': '#F1E7D6',
    'text': '#F1E7D6', 'text_muted': '#A99E8F', 'background': '#141010', 'border': '#3A302C', 'link': '#E9B13F',
}

OSSO = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
ROSSO = 'var(--olo-color-primary)'
ON_ROSSO = 'var(--olo-color-primary-contrast)'
VERDE = 'var(--olo-color-secondary)'
SENAPE = 'var(--olo-color-accent)'
NERO = 'var(--olo-color-dark)'
FONDO = 'var(--olo-color-background)'
SCURO = 'var(--olo-color-muted)'
FILO = mix(OSSO, 22)
FILO_FORTE = mix(OSSO, 55)
SOFT = mix(OSSO, 82)
DIM = mix(OSSO, 60)
MANO = "'Mr Dafoe', cursive"   # apici singoli: le doppie chiuderebbero l'attributo style
SQ = radius(0)
TONDO = radius(999)

ADDRESS = 'Scali delle Cantine 31, 57123 Livorno'
PHONE = '0586 000 000'
PHONE_TEL = 'tel:+390586000000'
MAIL = 'studio@agoeancora.example'
LAT, LNG = '43.5530', '10.3086'
HOME = '{{PAGINA:home}}'
ARTISTI = '{{PAGINA:artisti}}'
FLASH = '{{PAGINA:flash}}'
CURA = '{{PAGINA:la-cura}}'
PRENOTA = '{{PAGINA:prenota}}'

V_STENCIL = 'pexels-video-4343487'     # l'ago che segue lo stencil viola
V_MACCHINA = 'pexels-video-4343476'    # la macchinetta a bobine da vicino
V_TATUATORE = 'pexels-video-5095664'   # il tatuatore chino sulla gamba
V_INCHIOSTRO = 'pexels-video-6071645'  # l'inchiostro nell'acqua
V_PORTO = 'pexels-video-4365128'       # il porto di notte
V_CAPPELLO = 'pexels-video-7030905'    # Bruno col cappello, al lavoro su una spalla

PHOTOS = [
    'pexels-17672691.jpg', 'pexels-7005675.jpg', 'pexels-20531496.jpg', 'pexels-10681099.jpg', 'pexels-15575963.jpg',
    'pexels-7005738.jpg', 'pexels-19312615.jpg', 'pexels-28991540.jpg', 'pexels-28991531.jpg', 'pexels-7005658.jpg',
    'pexels-7147750.jpg', 'pexels-26076870.jpg', 'pexels-13059119.jpg', 'pexels-20519322.jpg', 'pexels-2126124.jpg',
    'pexels-8183952.jpg', 'pexels-2183132.jpg', 'pexels-2186981.jpg', 'pexels-2134085.jpg', 'pexels-9356536.jpg',
    'pexels-34155039.jpg', 'pexels-12802253.jpg', 'pexels-7664155.jpg', 'pexels-3851370.jpg', 'pexels-37023013.jpg',
    'pexels-33679740.jpg', 'pexels-18875533.jpg', 'pexels-37757623.jpg', 'pexels-7147745.jpg', 'pexels-13765699.jpg',
    'pexels-5560567.jpg', 'pexels-14011941.jpg', 'pexels-39636796.jpg', 'pexels-8360451.jpg', 'pexels-2183131.jpg',
    'pexels-3295586.jpg', 'pexels-29004935.jpg', 'pexels-7005739.jpg', 'pexels-4925891.jpg',
]
VIDEOS = [V_STENCIL, V_MACCHINA, V_TATUATORE, V_INCHIOSTRO, V_PORTO, V_CAPPELLO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=50, color=NERO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=50, color=NERO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=44, colors=None):
    """Occhiello senape, titolo gotico in osso con la seconda riga in vermiglio."""
    colors = colors or [OSSO, ROSSO]
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': SENAPE, 'eyebrow_dot_color': ROSSO,
        'eyebrow_separator': '✦ ', 'eyebrow_font_family': 'body', 'eyebrow_font_size': 12, 'eyebrow_font_weight': '700',
        'headline_lines': [{'text': l, 'color': colors[i % len(colors)], 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 0.98,
        'headline_font_weight': '700', 'headline_align': align,
        'headline_font_size_tablet': max(40, int(size * 0.8)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 17, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.3fr 1fr', 'gap': 20,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 30, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def firma(t, label, size=48, color=SENAPE, align='left', pad_bottom=0, tag='p'):
    """Le scritte a mano: firme, dediche, i nomi d'arte."""
    return t.classic('headline', {'heading': label, 'tag': tag, 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': False, 'heading_font_size': size, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': MANO,
                                  'heading_font_size_mobile': str(max(32, int(size * 0.75)))},
                     style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def headline(t, label, size=40, color=OSSO, tag='h3', pad_bottom=10, align='left'):
    o = {'heading': label, 'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': '700', 'heading_color': color, 'heading_font': 'heading'}
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.82)), 'heading_font_size_mobile': str(max(30, int(size * 0.62)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=17, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label, url, kind='rosso', align='left', pad_top=10):
    if kind == 'rosso':
        o = {'bg_color': ROSSO, 'text_color': ON_ROSSO, 'hover_bg_color': SENAPE, 'hover_text_color': ON_ROSSO}
    elif kind == 'senape':
        o = {'bg_color': SENAPE, 'text_color': ON_ROSSO, 'hover_bg_color': OSSO, 'hover_text_color': ON_ROSSO}
    else:  # filo d'osso
        o = {'bg_color': 'transparent', 'text_color': OSSO, 'hover_bg_color': OSSO, 'hover_text_color': ON_ROSSO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 2,
             'shadow_color': FILO_FORTE, 'shadow_inset': True}
    o.update({'text': label, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 15, 'font_weight': '700',
              'text_transform': 'uppercase', 'letter_spacing': '1.5'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', r=None, tilt=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': r if r is not None else SQ, 'lightbox': False, 'shadow': 'none',
                               'border': {'top': 2, 'right': 2, 'bottom': 2, 'left': 2, 'linked': True, 'style': 'solid', 'color': FILO_FORTE}},
                     advanced={'mouse_tilt': True, 'mouse_tilt_intensity': 5} if tilt else None)


def pennellata(t, color, position='bottom', flip=False, height=60):
    """L'inchiostro che sbava nella sezione accanto: la forma ha il colore della SUA sezione."""
    return t.one([t.classic('shapedivider', {'shape': 'brush', 'position': position, 'flip_vertical': False,
                                             'flip_horizontal': flip, 'color': color, 'height': height, 'width': '100',
                                             'z_index': '3', 'responsive_height_tablet': str(int(height * 0.8)),
                                             'responsive_height_mobile': str(int(height * 0.55))})], anim=None)


def titlebar(t, sub, image_name, position='center center', overlay=62):
    return t.classic('pagetitlebar', {
        'media_bg': {'type': 'image', 'image_url': img(image_name), 'image_size': 'cover', 'image_position': position},
        'bg': {'type': 'none'}, 'title_tag': 'h1', 'title_color': OSSO, 'title_size': 112, 'title_weight': '700',
        'title_align': 'left', 'subtitle': sub, 'subtitle_color': SOFT, 'subtitle_size': 19,
        'show_breadcrumbs': True, 'breadcrumb_color': DIM, 'breadcrumb_separator': '✦', 'bg_color': NERO,
        'bg_overlay': overlay, 'bg_overlay_color': NERO, 'min_height': 500,
        'tile_padding': {'top': 110, 'right': 32, 'bottom': 70, 'left': 32}, 'content_width': 1200, 'padding_y': '40',
    })


def contacts(t):
    return t.classic('iconlist', {
        'items': [{'icon': 'anchor', 'text': ADDRESS, 'link': HOME + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': ON_ROSSO, 'icon_size': '18', 'text_color': OSSO, 'text_size': '16', 'gap': '14', 'icon_shape': 'square',
        'icon_bg_color': SENAPE, 'divider': False, 'layout': 'vertical',
    }, style={})


def hours(t):
    return t.classic('hoursstrip', {
        'items': [{'day': 'martedì — giovedì', 'time': '11 — 20', 'note': 'su appuntamento'},
                  {'day': 'venerdì', 'time': '11 — 22', 'note': 'walk-in dalle 17, senza appuntamento'},
                  {'day': 'sabato', 'time': '10 — 19', 'note': 'consulenze gratuite la mattina'}],
        'columns': 3, 'band_padding_y': 26, 'show_dividers': True, 'divider_color': FILO, 'band_border': False,
        'day_color': SENAPE, 'day_size': 13, 'time_font_family': 'heading', 'time_color': OSSO, 'time_size': 56,
        'time_weight': '700', 'note_color': TENUE, 'note_size': 14, 'mono_font_family': '',
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'first', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': OSSO, 'header_text_color_active': SENAPE, 'header_font_size': 26, 'header_font_weight': '700',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 16, 'text_color': TENUE,
        'icon_color': ROSSO, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 20, 'right': 0, 'bottom': 20, 'left': 0},
        'content_padding': {'top': 0, 'right': 40, 'bottom': 22, 'left': 0},
    }, style={})


def form_style():
    return {'bg': solid(SCURO), 'padding_top': 36, 'padding_right': 40, 'padding_bottom': 36, 'padding_left': 40, 'border_radius': 0,
            'border': {'top': 2, 'right': 2, 'bottom': 2, 'left': 2, 'style': 'solid', 'color': FILO_FORTE}}


def form_common(**extra):
    o = {'label_color': OSSO, 'input_bg': FONDO, 'input_color': OSSO, 'input_border_color': FILO, 'input_border_width': '1',
         'input_border_style': 'solid', 'input_radius': 0, 'input_focus_border': SENAPE, 'submit_bg': ROSSO,
         'submit_color': ON_ROSSO, 'submit_radius': 0, 'submit_hover_bg': SENAPE, 'submit_text_transform': 'uppercase',
         'submit_letter_spacing': '1.5', 'privacy_checkbox': True, 'submit_full_width': True}
    o.update(extra)
    return o


# ══════════════════════════════════════════════════════════════════════════════
#  ARTISTI
# ══════════════════════════════════════════════════════════════════════════════

ART = [
    {'slug': 'teo', 'nome': 'Teo Falleni', 'firma': 'Teo', 'ruolo': 'blackwork · geometrico', 'foto': 'pexels-20519322.jpg',
     'pos': 'center 30%', 'colore': ROSSO, 'online': True,
     'bio': 'Ha aperto lo studio nel 2009, dopo sei anni da apprendista a Rotterdam. Linee spesse, neri pieni, mandala e '
            'geometrie che seguono il muscolo: disegna sul corpo col pennarello prima dello stencil.',
     'attesa': 'tre mesi', 'minimo': '120 €', 'lavori': ['pexels-12802253.jpg', 'pexels-7664155.jpg', 'pexels-3851370.jpg', 'pexels-4925891.jpg']},
    {'slug': 'nadia', 'nome': 'Nadia Bracci', 'firma': 'Nadia', 'ruolo': 'tradizionale · giapponese', 'foto': 'pexels-26076870.jpg',
     'pos': 'center 25%', 'colore': SENAPE, 'online': True,
     'bio': 'Rose, rondini, carpe e peonie col contorno nero e i colori pieni: quelli che restano belli anche fra trent’anni. '
            'Fa le maniche giapponesi in sedute da tre ore, una al mese.',
     'attesa': 'due mesi', 'minimo': '100 €', 'lavori': ['pexels-2126124.jpg', 'pexels-2186981.jpg', 'pexels-3295586.jpg', 'pexels-2183132.jpg']},
    {'slug': 'iris', 'nome': 'Iris Conti', 'firma': 'Iris', 'ruolo': 'fineline · lettering', 'foto': 'pexels-13059119.jpg',
     'pos': 'center 30%', 'colore': VERDE, 'online': False,
     'bio': 'Linee sottili come un capello, fiori, ritratti di profilo e scritte a mano libera. Ogni lettera la disegna lei, '
            'mai un carattere preso dal computer.',
     'attesa': 'un mese', 'minimo': '80 €', 'lavori': ['pexels-9356536.jpg', 'pexels-34155039.jpg', 'pexels-29004935.jpg', 'pexels-8183952.jpg']},
    {'slug': 'bruno', 'nome': 'Bruno Gori', 'firma': 'il Capitano', 'ruolo': 'old school · cover-up', 'foto': 'pexels-7005658.jpg',
     'pos': 'center 35%', 'colore': OSSO, 'online': True,
     'bio': 'Tatua da quarant’anni, prima sulle navi e poi in via Grande. Lo chiamano il Capitano. Copre i tatuaggi '
            'sbagliati con quelli giusti, e i flash del venerdì li disegna lui.',
     'attesa': 'tre settimane', 'minimo': '80 €', 'lavori': ['pexels-2134085.jpg', 'pexels-2183131.jpg', 'pexels-19312615.jpg', 'pexels-5560567.jpg']},
]


def presenza(t):
    return t.classic('presencegrid', {
        'source': 'manual',
        'members': [{'name': a['nome'], 'avatar': img(a['foto']), 'role': a['ruolo'], 'online': a['online']} for a in ART],
        'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'gap': 18, 'show_ranks': False,
        'online_label': 'In studio oggi', 'offline_label': 'In convention a Londra', 'name_prefix': '',
        'show_ticker': True, 'ticker_text': 'Venerdì walk-in dalle 17 · Iris torna martedì · consulenze gratuite il sabato mattina',
        'ticker_speed': 30, 'ticker_bg': SCURO, 'ticker_color': SENAPE,
        'card_bg': SCURO, 'card_color': OSSO, 'role_color': SENAPE, 'avatar_size': 96, 'avatar_shape': 'circle',
        'online_color': VERDE, 'offline_color': TENUE, 'dot_size': 14, 'name_size': 19, 'name_weight': '700', 'role_size': 12,
        'card_radius': SQ, 'card_hover_effect': 'scale',
        'border': {'top': 2, 'right': 2, 'bottom': 2, 'left': 2, 'style': 'solid', 'color': FILO},
    })


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': NAME, 'logo_text_size': '34', 'logo_text_color': OSSO,
        'logo_text_font': 'heading', 'logo_text_weight': '700', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': 'anchor', 'logo_mark_size': 18, 'logo_mark_stroke': 2.25, 'logo_mark_color': ON_ROSSO,
        'logo_mark_bg': ROSSO, 'logo_mark_shape': 'circle', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'classic', 'sticky': True,
        'sticky_bg': mix(NERO, 94), 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': False,
        'nav_bg': NERO, 'text_color': OSSO, 'hover_color': SENAPE, 'active_color': SENAPE,
        'font_size': '13', 'font_weight': '700', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '30',
        'bar_width': 'wide', 'nav_height': '78', 'bar_padding': {'top': 10, 'right': 32, 'bottom': 10, 'left': 32},
        'hover_effect': 'underline', 'hover_effect_color': ROSSO,
        'button_mode': 'last', 'btn_bg': ROSSO, 'btn_color': ON_ROSSO, 'btn_hover_color': ON_ROSSO,
        'btn_radius': SQ, 'btn_padding': {'top': 12, 'right': 22, 'bottom': 12, 'left': 22}, 'btn_hover_effect': 'none',
        'btn_font_weight': '700', 'btn_transform': 'uppercase', 'btn_letter_spacing': '1.5',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '#', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': NERO, 'mobile_text_color': OSSO, 'mobile_accent_color': SENAPE,
        'hamburger_style': 'minimal', 'hamburger_color': OSSO, 'mobile_font_size': '44', 'mobile_link_size': '44',
        'mobile_link_font': 'heading', 'mobile_link_weight': '700',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': HEX['border']},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, NAME, size=92, color=OSSO, tag='p', pad_bottom=0)
    sotto = firma(t, 'mano ferma, inchiostro buono', size=42, color=SENAPE, pad_bottom=10)
    claim = text(t, '<p>Studio di tatuaggi sugli Scali delle Cantine, a Livorno, dal 2009. Quattro artisti, un ago nuovo per '
                    'ogni persona e la sterilizzazione con l’autoclave, registrata ogni sera.</p>', size=15, color=TENUE)

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'uppercase', 'letter_spacing': '1.5',
                                 'link_color': SOFT, 'link_hover_color': SENAPE, 'active_color': OSSO,
                                 'font_size': '13', 'font_weight': '700', 'tile_padding': {'top': 4, 'right': 0, 'bottom': 4, 'left': 0}})
    titolo = lambda s: headline(t, s, size=28, color=ROSSO, tag='h4', pad_bottom=8)  # noqa: E731
    pagine = nav([('Gli artisti', ARTISTI), ('Flash day', FLASH), ('La cura', CURA), ('Prenota', PRENOTA),
                  ('Dove siamo', HOME + '#dove'), ('Privacy', '/privacy-policy/')])
    orari = text(t, '<p>mar–gio 11–20<br>ven 11–22, walk-in dalle 17<br>sab 10–19</p><p>Solo maggiorenni, col documento.</p>',
                 size=15, color=SOFT)
    legal = text(t, '<p>© 2026 Ago e Ancora · Studio di tatuaggi, Livorno. Autorizzazione sanitaria ASL Toscana nord ovest. '
                    'Le foto dei lavori sono pubblicate col permesso di chi li porta.</p>', size=13, color=mix(OSSO, 45))
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'facebook', 'url': 'https://facebook.com'}],
                                  'size': '34', 'alignment': 'right', 'icon_color': DIM, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': SENAPE, 'posizione': 'fisso', 'style': 'default'})
    line = {'style': 'solid', 'color': HEX['border']}
    top = t.row([
        t.col([brand, sotto, claim, contacts(t)], wm='1-2', ws='1-1'),
        t.col([titolo('Le pagine'), pagine], wm='1-4', ws='1-2'),
        t.col([titolo('Gli orari'), orari], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': dict(top=1, right=0, bottom=0, left=0, **line), 'padding_top': 20, 'margin_top': 44})
    return [t.section([pennellata(t, NERO, position='top', height=50), top, bottom], bg=solid(NERO), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

LAVORI = [
    ('pexels-2126124.jpg', 'Carpa fra le onde', 'Giapponese', 'Nadia · manica, sei sedute'),
    ('pexels-12802253.jpg', 'Mandala sull’avambraccio', 'Blackwork', 'Teo · una seduta da quattro ore'),
    ('pexels-9356536.jpg', 'Peonie sulla spalla', 'Fineline', 'Iris · due sedute'),
    ('pexels-3295586.jpg', 'Rosa sul gomito', 'Tradizionale', 'Nadia · il punto che fa più male'),
    ('pexels-3851370.jpg', 'Alveare sulla schiena', 'Blackwork', 'Teo · nove mesi di lavoro'),
    ('pexels-34155039.jpg', 'Profilo a una linea', 'Fineline', 'Iris · quaranta minuti'),
    ('pexels-2186981.jpg', 'Petto e maniche', 'Giapponese', 'Nadia · due anni, una volta al mese'),
    ('pexels-8183952.jpg', 'Rosa e stelle', 'Tradizionale', 'Bruno · flash del venerdì'),
    ('pexels-7664155.jpg', 'Sole nero', 'Blackwork', 'Teo · copre una scritta del 2004'),
]

DOLORE = [
    ('38', '28', 'Avambraccio', '3 su 10 · il posto giusto per il primo tatuaggio: pelle tesa, poco nervo.'),
    ('33', '45', 'Gomito', '8 su 10 · l’osso è sotto la pelle e la macchinetta si sente fino ai denti.'),
    ('66', '56', 'Braccio, lato interno', '6 su 10 · pelle sottile: si sopporta, ma si sente.'),
    ('50', '66', 'Nuca', '5 su 10 · fastidiosa più che dolorosa, come il solletico.'),
    ('29', '79', 'Scapola', '4 su 10 · c’è il muscolo: si sta comodi anche tre ore.'),
    ('50', '88', 'Colonna', '7 su 10 · le vertebre vibrano con l’ago. Le pause aiutano.'),
    ('69', '91', 'Costole', '9 su 10 · il più temuto: respiro lento e sedute corte.'),
]

PLAYLIST = [
    (V_STENCIL, 'Lo stencil · l’ago segue il disegno viola'),
    (V_TATUATORE, 'Teo al lavoro · un sole nero sulla caviglia'),
    (V_MACCHINA, 'La macchinetta a bobine · come la usava il Capitano'),
    (V_CAPPELLO, 'Il Capitano · una rondine sulla spalla'),
]


def build_home():
    t = Tpl(ID, 'home')
    out = []

    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Studio di tatuaggi · Livorno, Venezia Nuova', 'eyebrow_dot': True,
        'title': 'Inchiostro, ago<br>e <em>mano ferma</em>.',
        'subtitle': 'Quattro artisti sugli Scali delle Cantine: blackwork, tradizionale, giapponese e fineline. Prima la '
                    'consulenza, poi il disegno, poi la pelle. Il venerdì sera si entra senza appuntamento.',
        'media_bg': {'type': 'video', 'video_url': img(V_STENCIL + '.mp4'), 'video_poster': img(V_STENCIL + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': NERO, 'overlay_top': 0.35, 'overlay_bottom': 0.92, 'overlay_sides': True, 'overlay_direction': '',
        'glow_on': True, 'glow_color': rgba(HEX['primary'], 0.22), 'glow_w': 900, 'glow_h': 520, 'glow_blur': 140,
        'glow_x': 20, 'glow_y': 80,
        'frame_on': True, 'frame_inset': 22, 'watermark_text': 'ANCORA', 'watermark_color': rgba(HEX['light'], 0.05),
        'accent': ROSSO, 'text_color': OSSO, 'title_color': OSSO, 'subtitle_color': SOFT,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 128, 'title_font_weight': '700',
        'title_line_height': '0.92', 'title_letter_spacing': '0', 'title_text_transform': 'none',
        'subtitle_font_size': 19, 'subtitle_max_width': 600,
        'min_height': '94vh', 'content_max_width': 1240, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 140, 'right': 48, 'bottom': 96, 'left': 48},
        'cta_text': 'Chiedi una consulenza', 'cta_url': PRENOTA, 'cta_bg_color': ROSSO, 'cta_text_color': ON_ROSSO,
        'cta_radius': SQ, 'cta_size': '15', 'cta_style': 'filled', 'cta_font_weight': '700', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '1.5',
        'cta2_text': 'Gli artisti', 'cta2_url': ARTISTI, 'cta2_text_color': OSSO, 'cta2_style': 'outline',
        'meta_text': 'Su appuntamento · mar–sab · walk-in il venerdì dalle 17',
        'scroll_hint': '', 'scene_depth': 'medium', 'scene_depth_fade': True, 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    nastro = t.classic('marquee', {
        'content_type': 'text', 'separator': '  ✦  ',
        'text_items': 'Blackwork | Tradizionale | Giapponese | Fineline | Lettering | Cover-up | Flash del venerdì | Un ago nuovo per ogni persona',
        'speed': '36', 'direction': 'left', 'pause_hover': False, 'gap': 30, 'velocity_skew': True,
        'bg_color': ROSSO, 'text_color': ON_ROSSO, 'font_size': 40, 'font_weight': '700', 'letter_spacing': 0,
        'text_transform': 'none', 'font_family': 'heading', 'font_style': 'normal', 'separator_color': ON_ROSSO, 'height': 78,
    })
    out.append(t.section([t.one([nastro], anim=None)], width='fullbleed', padding='remove-vertical'))

    manifesto = t.classic('scrubtext', {
        'text': 'Un tatuaggio <em>non si cancella</em>. Per questo lo disegniamo con calma, <em>insieme a te</em>.',
        'show_lead': True,
        'lead': 'La prima volta ci sediamo al tavolo con un caffè: ci racconti l’idea, guardiamo dove metterla, ti diciamo '
                'quanto costa e quante sedute servono. Se non siamo le persone giuste, ti diciamo chi lo è.',
        'scroll_reveal': True, 'dim_opacity': 22, 'accent': ROSSO, 'text_color': OSSO, 'lead_color': TENUE,
        'size_min': 52, 'size_max': 96, 'max_width_ch': 18, 'lead_size': 18, 'lead_max_width_ch': 52,
        'text_font_family': 'heading', 'text_font_weight': '700', 'text_align': '',
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([t.one([manifesto], anim=None), pennellata(t, FONDO, height=56)], padding='large', html_id='manifesto'))

    lavori = t.classic('portfolio', {
        'source': 'manual', 'layout': 'grid', 'columns': 3, 'gap': 18, 'image_ratio': '4:5', 'image_fit': 'cover',
        'filter_bar': True, 'filter_style': 'pills', 'filter_all_label': 'Tutti', 'filter_color': TENUE,
        'filter_active_color': SENAPE, 'hover_effect': 'zoom', 'animation': 'fade', 'caption_position': 'overlay',
        'caption_corner': 'bottom-left', 'stagger_entrance': True, 'stagger_delay': 70, 'dim_others': True,
        'show_title': True, 'show_category': True, 'show_excerpt': True, 'font_family': 'heading', 'font_weight': '700',
        'title_color': OSSO, 'text_color': SOFT, 'accent_color': SENAPE, 'overlay_color': NERO, 'overlay_opacity': 45,
        'border_radius': 0, 'cursor_label_enabled': True, 'cursor_label_text': 'Guarda',
        'items': [{'title': ti, 'image_url': img(f), 'category': c, 'description': d, 'year': '', 'link_url': ARTISTI}
                  for f, ti, c, d in LAVORI],
        'columns_tablet_landscape': '3', 'columns_tablet': '2', 'columns_mobile_landscape': '2',
    })
    out.append(t.section([
        t.one([sechead(t, 'i lavori', ['Quattro mani,', 'quattro stili.'], size=80,
                       tagline='Scegli lo stile: ti mostriamo i lavori di chi lo fa meglio. Ogni foto è di una persona vera, '
                               'che ci ha dato il permesso di pubblicarla.')]),
        t.one([lavori]),
    ], bg=solid(SCURO), padding='large', html_id='lavori'))

    out.append(t.section([
        t.row([t.col([sechead(t, 'in studio', ['Chi c’è', 'oggi.'], size=72, layout='stack'),
                      text(t, '<p>Il pallino verde vuol dire che l’artista è al banco: potete passare a salutare o a chiedere '
                              'un parere. Il venerdì dalle 17 c’è sempre almeno uno di noi per il walk-in.</p>'),
                      button(t, 'Conosci gli artisti', ARTISTI, kind='filo')], wm='1-3', ws='1-1'),
               t.col([presenza(t)], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=56, valign='center'),
    ], padding='large', html_id='in-studio'))

    mappa = t.classic('hotspot', {
        'image': img('pexels-3851370.jpg'), 'image_alt': 'Una schiena tatuata con le braccia alzate',
        'image_height': '760', 'aspect_ratio': '2/3', 'aspect_ratio_custom': '2/3', 'object_fit': 'cover',
        'object_position': 'center center',
        'markers': [{'pos_x': x, 'pos_y': y, 'title': ti, 'description': d, 'icon': 'pin',
                     'tooltip_position': 'left' if float(x) > 55 else 'right', 'link': '', 'link_label': ''} for x, y, ti, d in DOLORE],
        'border_radius': 0, 'marker_style': 'number', 'marker_color': ROSSO, 'marker_text_color': ON_ROSSO, 'marker_size': 34,
        'pulse_animation': True, 'tooltip_bg': OSSO, 'tooltip_color': HEX['dark'], 'tooltip_width': 250,
    })
    out.append(t.section([
        t.row([t.col([mappa], wm='1-2', ws='1-1'),
               t.col([sechead(t, 'la domanda di tutti', ['Dove fa', 'più male?'], size=80, layout='stack'),
                      text(t, '<p>Toccate i numeri sulla schiena: è la scala che usiamo in studio, da uno a dieci, raccolta in '
                              'quindici anni di sedute. Dove c’è muscolo si sente poco, dove l’osso è vicino alla pelle si sente '
                              'di più. Nessuno se ne è mai andato a metà.</p>', size=18),
                      t.classic('desclist', {
                          'items': [{'term': 'Il primo', 'definition': 'avambraccio o polpaccio', 'icon': ''},
                                    {'term': 'Il coraggioso', 'definition': 'costole, gomito, caviglia', 'icon': ''},
                                    {'term': 'Il consiglio', 'definition': 'dormi, mangia e non bere la sera prima', 'icon': ''}],
                          'layout': 'inline', 'term_color': SENAPE, 'term_font_size': '13', 'term_font_weight': '700',
                          'definition_color': OSSO, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
                          style={'margin_top': 6, 'margin_bottom': 18}),
                      button(t, 'Come si cura dopo', CURA, kind='filo')], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='center'),
    ], bg=solid(SCURO), padding='large', html_id='dolore'))

    conto = t.classic('countdown', {
        'countdown_type': 'date', 'target_date': '2026-11-13T17:00', 'show_days': True, 'show_hours': True,
        'show_minutes': True, 'show_seconds': True, 'expired_message': 'Il flash day è cominciato: venite, siamo aperti fino alle 22.',
        'label_days': 'giorni', 'label_hours': 'ore', 'label_minutes': 'minuti', 'label_seconds': 'secondi', 'separator': '',
        'text_color': OSSO, 'accent_color': SENAPE, 'number_font_size': 92, 'number_font_weight': '700',
        'label_font_size': '12', 'label_font_weight': '700', 'item_min_width': 150, 'item_bg_color': rgba('#0B0909', 0.55),
        'item_radius': SQ, 'item_padding': {'top': 26, 'right': 14, 'bottom': 22, 'left': 14},
        'tile_padding': {'top': 6, 'right': 0, 'bottom': 6, 'left': 0},
    }, style={})
    out.append(t.section([
        t.row([t.col([firma(t, 'venerdì 13', size=64, color=SENAPE),
                      headline(t, 'Flash day: piccoli tatuaggi a 50 €, senza appuntamento.', size=52, tag='h2', pad_bottom=14),
                      text(t, '<p>Il 13 novembre dalle 17 alle 22. Il Capitano disegna un foglio di trenta flash: si sceglie, si fa la '
                              'fila, si entra. Uno a testa, finché ce n’è.</p>', size=17, color=SOFT),
                      button(t, 'Il foglio dei flash', FLASH, kind='senape')], wm='2-5', ws='1-1'),
               t.col([conto], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
    ], bg=video_bg(V_INCHIOSTRO, overlay=48), padding='xlarge', html_id='venerdi-13'))

    playlist = t.classic('videoplaylist', {
        'videos': [{'url': img(v + '.mp4'), 'title': ti, 'duration': '', 'thumbnail': img(v + '.jpg')} for v, ti in PLAYLIST],
        'layout': 'sidebar-right', 'player_height': 460, 'sidebar_width': 320, 'sidebar_bg': SCURO, 'text_color': OSSO,
        'active_color': SENAPE, 'show_duration': False, 'autoplay_next': True, 'thumbnail_ratio': '1/1',
    })
    out.append(t.section([
        t.one([sechead(t, 'in studio', ['L’ago,', 'da vicino.'], size=72,
                       tagline='Quattro video girati in studio, con il permesso di chi stava sul lettino. Si sente solo il ronzio.')]),
        t.one([playlist]),
    ], padding='large', html_id='video'))

    voce = t.classic('testimonial', {
        'quote': 'Avevo una scritta del 2004 che non sopportavo più. Teo l’ha fatta sparire dentro un sole nero: <em>ora è la '
                 'cosa che mi guardo di più.</em>', 'author_name': 'Marco R.', 'author_role': 'cover-up, quattro ore',
        'rating': '5', 'text_color': OSSO, 'star_color': SENAPE, 'author_color': TENUE, 'quote_accent_color': ROSSO,
        'quote_font': 'heading', 'quote_size': 44, 'author_uppercase': True, 'quote_uppercase': False, 'layout': 'editorial',
        'show_line': True, 'line_color': ROSSO,
    })
    out.append(t.section([t.one([voce], anim='fade')], bg=solid(SCURO), padding='large'))

    mappa_osm = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '16', 'height': '420', 'marker': True,
                                     'marker_popup': 'Ago e Ancora · Scali delle Cantine 31', 'scroll_wheel_zoom': False,
                                     'tile_layer': 'dark', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'dove siamo', ['Sui fossi,', 'a Venezia Nuova.'], size=64, layout='stack'),
                      contacts(t),
                      text(t, '<p>Fra il Fosso Reale e il porto, sopra le cantine dove i battelli scaricavano il vino. Si arriva a '
                              'piedi dalla Fortezza Nuova; le barche passano sotto la finestra.</p>', size=16, color=SOFT,
                           pad=(18, 0, 0, 0))], wm='2-5', ws='1-1'),
               t.col([mappa_osm], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
        t.one([hours(t)], style={'margin_top': 48}),
    ], bg=video_bg(V_PORTO, overlay=72), padding='large', html_id='dove'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  GLI ARTISTI
# ══════════════════════════════════════════════════════════════════════════════

def build_artisti():
    t = Tpl(ID, 'artisti')
    out = [t.section([t.one([titlebar(t, 'Quattro mani, quattro stili. Scegli chi ti somiglia: la consulenza è gratuita, con chiunque.',
                                      'pexels-7005675.jpg', position='center 40%')], anim=None)],
                     width='fullbleed', padding='remove-vertical')]
    out.append(t.section([t.one([presenza(t)])], padding='default'))
    for i, a in enumerate(ART):
        foto = image(t, a['foto'], a['nome'], ratio='4/5', position=a['pos'], tilt=True)
        dati = t.classic('desclist', {
            'items': [{'term': 'Stile', 'definition': a['ruolo'], 'icon': ''},
                      {'term': 'Attesa', 'definition': a['attesa'], 'icon': ''},
                      {'term': 'Minimo', 'definition': a['minimo'], 'icon': ''}],
            'layout': 'inline', 'term_color': SENAPE, 'term_font_size': '13', 'term_font_weight': '700',
            'definition_color': OSSO, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
            style={'margin_top': 8, 'margin_bottom': 18})
        galleria = t.classic('progallery', {
            'images': [{'url': img(f), 'alt': 'Un lavoro di ' + a['nome'], 'caption': '', 'subtitle': '', 'text': ''} for f in a['lavori']],
            'layout': 'expand', 'layout_family': 'classic', 'columns': 4, 'gap': 8, 'img_height': '300px',
            'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 0, 'mobile_columns': 2,
            'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 500, 'hover_effect': 'none', 'hover_caption': 'none',
            'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
        })
        testo = [firma(t, a['firma'], size=56, color=a['colore'] if a['colore'] != OSSO else SENAPE),
                 headline(t, a['nome'], size=64, tag='h2', pad_bottom=12),
                 text(t, '<p>' + a['bio'] + '</p>', size=18, color=SOFT), dati,
                 button(t, 'Prenota con ' + a['nome'].split()[0], PRENOTA + '?artista=' + a['nome'].split()[0] + '#richiesta', kind='rosso')]
        cols = [t.col([foto], wm='2-5', ws='1-1'), t.col(testo, wm='3-5', ws='1-1')]
        if i % 2:
            cols = [t.col(testo, wm='3-5', ws='1-1'), t.col([foto], wm='2-5', ws='1-1')]
        out.append(t.section([
            t.row(cols, layout='custom', custom='40,60' if not i % 2 else '60,40', gap=64, valign='center'),
            t.one([galleria], style={'margin_top': 40}),
        ], bg=solid(SCURO) if i % 2 else None, padding='large', html_id=a['slug']))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  FLASH
# ══════════════════════════════════════════════════════════════════════════════

FOGLIO = [
    ('Rondine', 'Torna sempre a casa. La regalavano ai marinai dopo cinquemila miglia.', '80 €', '6 cm'),
    ('Ancora', 'Il nostro nome. Tiene fermi quando c’è burrasca.', '90 €', '7 cm'),
    ('Rosa e pugnale', 'L’amore che punge: il flash più chiesto dal 1940.', '140 €', '10 cm'),
    ('Faro della Meloria', 'Il nostro faro, a sei miglia dal porto, con le onde a riccio.', '160 €', '12 cm'),
    ('Pantera', 'Nera, a bocca aperta, da mettere sull’avambraccio.', '150 €', '12 cm'),
    ('Nave a vele piene', 'Il viaggio lungo. Si fa in una seduta da tre ore.', '260 €', '18 cm'),
    ('Scritta a mano', 'Una parola, un nome o una data, disegnata da Iris lettera per lettera.', '80 €', 'fino a 10 lettere'),
]

FAQ_FLASH = [
    ('Il flash si può modificare?', 'Un po’: il colore, la misura, una lettera nel cartiglio. Se cambia molto, diventa un disegno su misura e lo prenoti come consulenza.'),
    ('Un flash si ripete?', 'Quelli del foglio sì. Quelli del venerdì 13 no: il Capitano li disegna per quella sera e poi strappa il foglio.'),
    ('Serve la caparra?', 'Per i flash no. Per i lavori su misura sì, 50 €, che si scalano dal prezzo finale.'),
    ('Fate tatuaggi ai minorenni?', 'No, nemmeno col permesso dei genitori. Chiediamo un documento a tutti.'),
]


def build_flash():
    t = Tpl(ID, 'flash')
    out = [t.section([t.one([titlebar(t, 'I disegni pronti da scegliere sul foglio, il flash del mese da grattare e il venerdì 13, '
                                         'quando si entra senza appuntamento.', 'pexels-37023013.jpg', position='center 35%')], anim=None)],
                     width='fullbleed', padding='remove-vertical')]

    gratta = t.classic('scratchfx', {
        'image': img('pexels-33679740.jpg'), 'object_fit': 'cover', 'object_position': 'center center',
        'prize_eyebrow': 'Il flash di novembre', 'prize_title': 'Il drago rosso a 120 €',
        'prize_text': 'Invece di 160: lo disegna Nadia, sulla spalla o sul polpaccio. Mostrate il codice alla consulenza.',
        'coupon_code': 'DRAGO13', 'copy_label': 'Copia il codice', 'remember': True, 'text_color': OSSO, 'under_bg': SCURO,
        'hint': 'Gratta con il dito o col mouse', 'show_button': True, 'reveal_label': 'Scopri senza grattare',
        'cover_type': 'solid', 'cover_color': ROSSO, 'cover_text': 'GRATTA QUI', 'cover_text_color': ON_ROSSO,
        'brush_size': 36, 'reveal_threshold': 55, 'reset_on_leave': False, 'aspect': '4/3', 'height_mode': 'aspect',
        'align': 'left', 'max_width': 620, 'border_radius': SQ, 'shadow': 'none',
        'border': {'top': 2, 'right': 2, 'bottom': 2, 'left': 2, 'linked': True, 'style': 'solid', 'color': FILO_FORTE},
    })
    out.append(t.section([
        t.row([t.col([sechead(t, 'il flash del mese', ['Gratta', 'e scopri.'], size=80, layout='stack'),
                      text(t, '<p>Ogni mese un artista sceglie un disegno e lo mette a un prezzo più basso. Sotto la vernice c’è '
                              'quello di novembre, con il codice da mostrare alla consulenza.</p>', size=18)], wm='2-5', ws='1-1'),
               t.col([gratta], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='center'),
        pennellata(t, FONDO, height=56),
    ], padding='large', html_id='flash-del-mese'))

    foglio = t.classic('worklist', {
        'items': [{'number': '%02d' % (i + 1), 'title': n, 'category': d, 'year': p + ' · ' + m} for i, (n, d, p, m) in enumerate(FOGLIO)],
        'divider_color': FILO, 'row_padding_y': 24, 'row_hover_bg': mix(OSSO, 5), 'hover_indent': 20, 'number_color': ROSSO,
        'number_size': 13, 'title_font_family': 'heading', 'title_color': OSSO, 'title_size': 44, 'title_weight': '700',
        'show_category': True, 'category_color': TENUE, 'category_size': 15, 'show_year': True, 'year_color': SENAPE,
        'year_size': 15, 'show_arrow': False, 'arrow_color': SENAPE, 'mono_font_family': 'body',
        'row_padding': {'top': 22, 'right': 8, 'bottom': 22, 'left': 8},
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.one([sechead(t, 'il foglio', ['Sette flash,', 'sempre pronti.'], size=72,
                       tagline='Disegnati dal Capitano e da Nadia, si fanno in una seduta. Il prezzo è tutto compreso: stencil, '
                               'inchiostro, pellicola e il ritocco del mese dopo.')]),
        t.one([foglio]),
    ], bg=solid(SCURO), padding='large', html_id='foglio'))

    conto = t.classic('projector', {
        'eyebrow': 'su misura', 'heading': 'Quante ore <em>ci vogliono</em>?',
        'intro': 'Un disegno su misura si paga a ore di seduta. Spostate il cursore: il totale è indicativo, quello vero ve lo '
                 'diciamo alla consulenza.',
        'min': '1', 'max': '6', 'step': '1', 'value': '2', 'rate': '0', 'years': '120', 'currency': '€ ',
        'input_label': 'Ore di seduta', 'input_unit': 'ore', 'out_caption': 'Il prezzo indicativo', 'result_suffix': '',
        'decimals': 0, 'note': 'Minimo 80 €. La giornata intera (sei ore) costa 650 €, il ritocco del mese dopo è gratuito.',
        'show_contrib': False, 'list_base': 1, 'list_items': [], 'zone_accent': SENAPE, 'card_bg': SCURO, 'align': 'left',
        'tile_padding': {'top': 44, 'right': 44, 'bottom': 44, 'left': 44}, 'border_radius': 0, 'shadow': 'none',
        'border': {'top': 2, 'right': 2, 'bottom': 2, 'left': 2, 'linked': True, 'style': 'solid', 'color': FILO_FORTE},
    })
    out.append(t.section([
        t.row([t.col([conto], wm='3-5', ws='1-1'),
               t.col([image(t, 'pexels-18875533.jpg', 'Un’artista disegna i bozzetti sul tavolo', ratio='4/5', tilt=True)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='center'),
    ], padding='large', html_id='quanto-costa'))

    out.append(t.section([
        t.row([t.col([sechead(t, 'domande', ['Sui flash.'], size=64, layout='stack')], wm='1-3', ws='1-1'),
               t.col([faq(t, FAQ_FLASH, 'aa-flash')], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=56),
    ], bg=solid(SCURO), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LA CURA
# ══════════════════════════════════════════════════════════════════════════════

FASI = [
    ('Le prime tre ore', 'clock', '<p><strong>La pellicola resta su.</strong> L’abbiamo messa noi in studio: protegge la pelle aperta dalla '
     'polvere. Dopo tre ore la togli sotto l’acqua tiepida, lavi con sapone neutro e asciughi tamponando con la carta, '
     'mai con l’asciugamano.</p>'),
    ('Giorni 1–4', 'droplets', '<p><strong>Lava e crema, tre volte al giorno.</strong> Un velo sottile della crema che ti abbiamo dato: '
     'la pelle deve respirare, non annegare. È normale che il tatuaggio sia gonfio, caldo e un po’ lucido.</p>'),
    ('Giorni 5–14', 'hand', '<p><strong>Prude, si sfoglia: non grattare.</strong> Si formano le crosticine, come dopo una scottatura. '
     'Lasciale cadere da sole, anche se il colore sembra opaco. Niente mare, piscina e sole diretto: lo sappiamo, siamo a '
     'Livorno, ma sono solo due settimane.</p>'),
    ('Dopo un mese', 'sparkles', '<p><strong>Torna per il ritocco, è gratis.</strong> A un mese la pelle è guarita e si vede se qualche '
     'linea ha bisogno di una ripassata. Poi, per sempre, la crema solare sul tatuaggio d’estate.</p>'),
]

SERVE = [
    ('droplets', 'Sapone neutro, senza profumo'), ('hand', 'Le mani lavate prima di toccarlo'),
    ('shirt', 'Vestiti larghi di cotone sopra il tatuaggio'), ('sun', 'Crema solare 50 per sempre, d’estate'),
]
EVITA = [
    ('sailboat', 'Mare e piscina per due settimane'), ('flame', 'Sauna, bagno turco e lampade'),
    ('ban', 'Grattare o staccare le crosticine'), ('wine', 'Alcol la sera prima della seduta'),
]

FAQ_CURA = [
    ('È normale che perda un po’ di inchiostro?', 'Sì, nei primi due giorni: è il plasma con un po’ di colore in eccesso. Se dopo il terzo giorno è ancora molto rosso e caldo, chiamaci.'),
    ('Che crema devo usare?', 'Quella che ti diamo in studio, oppure una crema neutra per la pelle secca senza profumo. Niente creme cicatrizzanti grasse.'),
    ('Posso andare in palestra?', 'Dopo tre giorni sì, se il tatuaggio non sfrega sui vestiti o sugli attrezzi. Puliscilo subito dopo.'),
    ('Quando posso fare il secondo?', 'Su un’altra parte del corpo anche la settimana dopo; vicino al primo, dopo un mese.'),
]


def build_cura():
    t = Tpl(ID, 'cura')
    out = [t.section([t.one([titlebar(t, 'Un tatuaggio guarisce in un mese. Quello che fai nelle prime due settimane decide come sarà '
                                         'fra vent’anni.', 'pexels-8360451.jpg', position='center 40%')], anim=None)],
                     width='fullbleed', padding='remove-vertical')]

    schede = t.classic('switcher', {
        'items': [{'id': 'aa-g%d' % (i + 1), 'title': ti, 'icon': ic, 'content': c, 'image': '', 'link_text': '', 'link_url': ''}
                  for i, (ti, ic, c) in enumerate(FASI)],
        'nav_style': 'tab', 'animation': 'fade', 'vertical': False, 'tab_padding_y': '12', 'tab_padding_x': '18',
        'tab_font_size': 14, 'tab_font_weight': '700', 'tab_gap': 0, 'tab_radius': 0, 'container_bg': SCURO,
        'container_padding': 4, 'container_radius': 0, 'active_bg': ROSSO, 'active_color': ON_ROSSO, 'inactive_color': SOFT,
        'indicator_type': 'pill', 'content_color': SOFT, 'content_padding_y': '26', 'content_padding_x': '0',
        'content_font_size': '18',
    })
    out.append(t.section([
        t.one([sechead(t, 'giorno per giorno', ['La guarigione,', 'in quattro tempi.'], size=72,
                       tagline='Scegli il giorno in cui sei. Se qualcosa non ti torna, mandaci una foto: rispondiamo entro sera.')]),
        t.row([t.col([schede], wm='3-5', ws='1-1'),
               t.col([image(t, 'pexels-2183131.jpg', 'Un braccio tatuato guarito, col pugno chiuso', ratio='4/5', position='center center', tilt=True)],
                     wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=56, valign='center'),
    ], padding='large', html_id='giorni'))

    def lista(items, color):
        return t.classic('iconlist', {
            'items': [{'icon': i, 'text': tx, 'link': '', 'color': ''} for i, tx in items],
            'icon_color': color, 'icon_size': '20', 'text_color': OSSO, 'text_size': '17', 'gap': '16', 'icon_shape': 'none',
            'icon_bg_color': '', 'divider': True, 'divider_color': FILO, 'layout': 'vertical',
        }, style={})
    out.append(t.section([
        t.row([t.col([headline(t, 'Sì', size=88, color=VERDE, tag='h3', pad_bottom=6), lista(SERVE, VERDE)], wm='1-2', ws='1-1'),
               t.col([headline(t, 'No', size=88, color=ROSSO, tag='h3', pad_bottom=6), lista(EVITA, ROSSO)], wm='1-2', ws='1-1')],
              layout='50-50', gap=64, valign='start'),
    ], bg=solid(SCURO), padding='large', html_id='si-e-no'))

    out.append(t.section([
        t.row([t.col([sechead(t, 'domande', ['Dopo', 'la seduta.'], size=64, layout='stack'),
                      button(t, 'Mandaci una foto', 'mailto:' + MAIL, kind='filo')], wm='1-3', ws='1-1'),
               t.col([faq(t, FAQ_CURA, 'aa-cura')], wm='2-3', ws='1-1')], layout='custom', custom='34,66', gap=56),
    ], padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  PRENOTA
# ══════════════════════════════════════════════════════════════════════════════

PERCORSO = [
    ('Ci scrivi', 'L’idea, dove la vuoi, una foto di riferimento. Rispondiamo entro due giorni.', 'send', 'active'),
    ('La consulenza', 'Mezz’ora al tavolo, gratuita: misura, posto, prezzo e sedute.', 'coffee', 'pending'),
    ('Il disegno', 'L’artista lo prepara e te lo mostra il giorno prima. Si cambia finché non è tuo.', 'pen-tool', 'pending'),
    ('La seduta', 'Ago nuovo, stencil, e si comincia. Pausa quando vuoi.', 'anchor', 'pending'),
    ('Il ritocco', 'Dopo un mese, gratuito: ripassiamo ciò che serve.', 'sparkles', 'pending'),
]


def build_prenota():
    t = Tpl(ID, 'prenota')
    out = [t.section([t.one([titlebar(t, 'Raccontaci l’idea: ti rispondiamo entro due giorni con la data della consulenza. È gratuita, '
                                         'e non obbliga a niente.', 'pexels-7147745.jpg', position='center 45%')], anim=None)],
                     width='fullbleed', padding='remove-vertical')]
    percorso = t.classic('progresstracker', {
        'items': [{'title': ti, 'description': d, 'icon': ic, 'status': st} for ti, d, ic, st in PERCORSO],
        'layout': 'horizontal', 'connector_style': 'dashed', 'connector_color': FILO_FORTE, 'completed_color': VERDE,
        'active_color': ROSSO, 'pending_color': mix(OSSO, 35), 'text_color': OSSO, 'show_description': True, 'show_numbers': True,
        'circle_size': '52', 'font_size': '17', 'gap': '0',
    })
    out.append(t.section([t.one([sechead(t, 'come funziona', ['Cinque passi,', 'nessuna fretta.'], size=72)]), t.one([percorso])],
                         padding='large', html_id='percorso'))

    form = t.classic('form', dict(form_common(), **{
        'fields': [
            {'field_type': 'textarea', 'label': 'L’idea', 'name': 'idea', 'placeholder': 'Una rondine con la data di nascita di mia figlia, in stile tradizionale…',
             'required': True, 'width': '1-1', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Lo stile', 'name': 'stile', 'placeholder': 'Scegli', 'required': True, 'width': '1-2',
             'options': 'Blackwork\nTradizionale\nGiapponese\nFineline\nLettering\nCover-up\nNon lo so ancora', 'icon': ''},
            {'field_type': 'select', 'label': 'Dove', 'name': 'dove', 'placeholder': 'Scegli', 'required': True, 'width': '1-2',
             'options': 'Avambraccio\nBraccio\nSpalla\nSchiena\nPetto\nCostole\nGamba\nCaviglia o piede\nMano o dita\nAltro', 'icon': ''},
            {'field_type': 'select', 'label': 'Misura', 'name': 'misura', 'placeholder': 'Più o meno', 'required': False, 'width': '1-2',
             'options': 'Piccolo, fino a 7 cm\nMedio, fino a 15 cm\nGrande, fino a 25 cm\nManica o schiena', 'icon': ''},
            {'field_type': 'select', 'label': 'Con chi', 'name': 'artista', 'placeholder': 'Chi è libero', 'required': False, 'width': '1-2',
             'options': 'Teo\nNadia\nIris\nBruno\nChi è libero prima', 'icon': '',
             'param': 'artista'},
            {'field_type': 'file', 'label': 'Una foto di riferimento', 'name': 'riferimento', 'placeholder': '', 'required': False,
             'width': '1-1', 'options': '', 'icon': '', 'file_allowed_types': '.jpg,.jpeg,.png,.webp,.pdf', 'file_max_size': 8,
             'file_max_files': 3, 'file_button_text': 'Scegli le foto'},
            {'field_type': 'step', 'label': 'Chi sei', 'name': 'passo-2', 'placeholder': '', 'required': False, 'width': '1-1', 'options': '', 'icon': ''},
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': 'Giulia', 'required': True, 'width': '1-2', 'options': '', 'icon': 'user'},
            {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': 'giulia@esempio.it', 'required': True, 'width': '1-2', 'options': '', 'icon': 'mail'},
            {'field_type': 'tel', 'label': 'Telefono', 'name': 'telefono', 'placeholder': '333 123 4567', 'required': False, 'width': '1-2', 'options': '', 'icon': 'phone'},
            {'field_type': 'select', 'label': 'Quando sei libero', 'name': 'quando', 'placeholder': 'Scegli', 'required': False, 'width': '1-2',
             'options': 'Mattina\nPomeriggio\nSera\nSolo il sabato', 'icon': ''},
            {'field_type': 'checkbox', 'label': 'Maggiore età', 'name': 'maggiorenne', 'placeholder': '', 'required': True, 'width': '1-1',
             'options': 'Ho almeno 18 anni e porterò un documento', 'icon': ''},
        ],
        'enable_multistep': True, 'step_style': 'numbers', 'step_labels': 'L’idea\nChi sei',
        'file_types': '.jpg,.jpeg,.png,.webp,.pdf', 'file_max_size': '8',
        'email_subject': 'Una richiesta di consulenza dal sito', 'success_message': 'Ricevuta. Ti rispondiamo entro due giorni con la data della consulenza.',
        'submit_text': 'Manda la richiesta', 'submit_icon': 'send',
    }), style=form_style())
    out.append(t.section([
        t.row([t.col([sechead(t, 'la richiesta', ['Raccontaci', 'l’idea.'], size=72, layout='stack'),
                      text(t, '<p>Due passi: prima l’idea, poi chi sei. Se arrivi dalla pagina di un artista, il suo nome è già scelto.</p>'
                              '<p>La caparra di 50 € si lascia alla consulenza, solo per i lavori su misura, e si scala dal prezzo.</p>',
                           size=17),
                      image(t, 'pexels-7005739.jpg', 'Le macchinette sul tavolo dello studio', ratio='4/3')], wm='2-5', ws='1-1'),
               t.col([form], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=56, valign='start'),
    ], bg=solid(SCURO), padding='large', html_id='richiesta'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    testa = t.classic('section-header', {
        'eyebrow_show': True, 'eyebrow_text': 'Errore 404', 'eyebrow_color': SENAPE, 'eyebrow_dot_color': ROSSO,
        'eyebrow_separator': '✦ ', 'eyebrow_font_family': 'body', 'eyebrow_font_size': 12, 'eyebrow_font_weight': '700',
        'headline_lines': [{'text': 'Questa pagina', 'color': OSSO, 'italic': False},
                           {'text': 'è sbavata.', 'color': ROSSO, 'italic': False}],
        'headline_font_family': 'heading', 'headline_font_size': 120, 'headline_line_height': 0.95, 'headline_font_weight': '700',
        'headline_align': 'center', 'headline_font_size_tablet': 88, 'headline_font_size_mobile': 56,
        'tagline_show': True, 'tagline_text': 'Succede anche ai migliori: per fortuna non è sulla pelle. Torna allo studio.',
        'tagline_text_color': SOFT, 'tagline_text_size': 19, 'tagline_font_family': 'body', 'layout': 'center', 'gap': 18,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 18, 'padding_left': 0})
    return [t.section([t.one([testa, button(t, 'Torna allo studio', HOME, kind='rosso', align='center')], anim='fade')],
                      bg=video_bg(V_INCHIOSTRO, overlay=55), padding='xlarge')]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Moda e bellezza', 'zone': 'Dove fa più male?',
    'tags': ['tatuaggi', 'tattoo', 'studio', 'flash', 'livorno'],
    'description': 'Studio di tatuaggi a Livorno, scuro come l’inchiostro col colore del flash tradizionale: l’ago che segue lo '
                   'stencil in apertura, il manifesto che si accende parola per parola, la mappa del dolore sulla schiena, chi c’è '
                   'in studio oggi, i giorni al venerdì 13, il flash del mese da grattare e la richiesta in due passi con la foto.',
    'menu': {'name': 'Ago e Ancora — menu', 'items': [
        {'title': 'Gli artisti', 'url': ARTISTI}, {'title': 'Flash day', 'url': FLASH}, {'title': 'La cura', 'url': CURA},
        {'title': 'Dove siamo', 'url': HOME + '#dove'}, {'title': 'Prenota', 'url': PRENOTA}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Ago e Ancora — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Ago e Ancora — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Ago e Ancora — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Ago e Ancora — Home'},
        'artisti': {'file': 'artisti.json', 'type': 'page', 'title': 'Ago e Ancora — Gli artisti'},
        'flash': {'file': 'flash.json', 'type': 'page', 'title': 'Ago e Ancora — Flash day'},
        'la-cura': {'file': 'la-cura.json', 'type': 'page', 'title': 'Ago e Ancora — La cura'},
        'prenota': {'file': 'prenota.json', 'type': 'page', 'title': 'Ago e Ancora — Prenota'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'artisti': {'title': 'Gli artisti', 'template': 'artisti', 'slug': 'artisti'},
        'flash': {'title': 'Flash day', 'template': 'flash', 'slug': 'flash'},
        'la-cura': {'title': 'La cura', 'template': 'la-cura', 'slug': 'la-cura'},
        'prenota': {'title': 'Prenota', 'template': 'prenota', 'slug': 'prenota'},
    },
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Bitter\', Georgia, \'Times New Roman\', serif',
            'font_family_heading': '\'Grenze Gotisch\', \'Old English Text MT\', Georgia, serif',
            'font_family_mono': '\'Bitter\', Georgia, serif',
            'font_size_base': '17px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '700',
            'heading_line_height': '1.0', 'heading_letter_spacing': '0', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '6rem', 'font_size_h2': '4rem', 'font_size_h3': '2.2rem',
            'font_size_h4': '1.6rem', 'font_size_h5': '1.25rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3.4rem',
            'font_size_h2_mobile': '2.6rem', 'font_size_h3_mobile': '1.7rem',
        },
        'google_fonts': ['Grenze Gotisch', 'Bitter', 'Mr Dafoe'],
        'google_fonts_weights': '400;500;600;700;800',
        'google_fonts_italic': ['Bitter'],
        'buttons': {'font_size': '15px', 'font_weight': '700', 'padding_x': '26px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '1.5px', 'hover_brightness': '108'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1240px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': HEX['background'], 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '0px', 'field_padding': '12px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['accent'], 'focus_shadow': '0 0 0 3px ' + rgba(HEX['accent'], 0.25),
                  'label_font_size': '14px', 'label_font_weight': '700', 'label_margin_bottom': '6px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 7, 'scale': 160},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'artisti.json': build_artisti(), 'flash.json': build_flash(), 'la-cura.json': build_cura(),
              'prenota.json': build_prenota(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
