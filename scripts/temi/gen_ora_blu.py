# -*- coding: utf-8 -*-
"""Genera il tema «Ora Blu» (id ora-blu): profumeria d'autore a Venezia, sei profumi per sei ore della giornata.

Il colore è quello dell'ora blu sulla laguna: una foschia lilla al posto del bianco, l'inchiostro indaco al posto del
nero, una sola nota d'ambra per i prezzi. Titoli in Tenor Sans (proporzioni classiche, niente grazie), testo in
Manrope, le ore e le didascalie in Spline Sans Mono, come i numeri di un orario dei vaporetti. Angoli vivi, fili sottili,
tanta aria, come le pagine di Cartier e di A24 premiate nel 2026.

Dettagli: l'apertura «sipario» color inchiostro col nome e l'ora (20:31), il cursore ad anello sottile, il cielo
dell'ora blu che si muove piano dietro la frase (Aurora), le ore scritte come su un orario.

Moduli: le sei ore con la foto che segue il cursore (Hover List), «A che ora ti senti?» (Finder), il flacone in video,
le carte dei profumi che si girano (FlipCard), formati e prezzi (Lista prezzi), il naso e il metodo (Process Steps),
la cantina in video, la scatola dei campioni da comporre col profilo olfattivo che si riempie (Configuratore con gli
indicatori) e che ore sono dove spediamo (Timezone).
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid, rgba  # noqa: E402

ID = 'ora-blu'
NAME = 'Ora Blu'

HEX = {
    'primary': '#2C2F6E', 'primary_contrast': '#F4F3F8', 'secondary': '#8E8FBF', 'secondary_contrast': '#1B1D33',
    'accent': '#A97E52', 'dark': '#14152A', 'light': '#F4F3F8', 'muted': '#D9D8E6', 'muted_contrast': '#1B1D33',
    'text': '#1B1D33', 'text_muted': '#5C5E78', 'background': '#E6E5EF', 'border': '#C6C5D8', 'link': '#2C2F6E',
}

INK = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
INDACO = 'var(--olo-color-primary)'
ON_INDACO = 'var(--olo-color-primary-contrast)'
LAVANDA = 'var(--olo-color-secondary)'
AMBRA = 'var(--olo-color-accent)'
FONDO = 'var(--olo-color-background)'
NEBBIA = 'var(--olo-color-muted)'
NOTTE = 'var(--olo-color-dark)'
CHIARO = 'var(--olo-color-light)'
FILO = 'var(--olo-color-border)'
FILO_INK = mix(INK, 60)
SOFT_CHIARO = mix(CHIARO, 78)
FILO_CHIARO = mix(CHIARO, 22)
MONO = 'var(--olo-font-family-mono)'
SQ = radius(0)

ADDRESS = 'Calle de la Mandola 3712, 30124 Venezia'
PHONE = '041 000 2031'
PHONE_TEL = 'tel:+390410002031'
MAIL = 'bottega@orablu.example'
LAT, LNG = '45.4350', '12.3322'
HOME = '{{PAGINA:home}}'
COLLEZIONE = '{{PAGINA:collezione}}'
ATELIER = '{{PAGINA:atelier}}'
CAMPIONI = '{{PAGINA:campioni}}'

V_INCHIOSTRO = 'pexels-video-7234988'   # l'inchiostro viola che si apre nell'acqua
V_IRIS = 'pexels-video-8447663'         # gli iris su fondo grigio, mossi dall'aria
V_GONDOLE = 'pexels-video-33793071'     # le gondole all'alba, San Giorgio sul fondo
V_FLACONE = 'pexels-video-6802240'      # una mano che porge il flacone, camicia bianca
V_FUMO = 'pexels-video-10004412'        # il fumo blu nel buio

PHOTOS = [
    'pexels-8361481.jpg', 'pexels-27357173.jpg', 'pexels-8361490.jpg', 'pexels-8361539.jpg', 'pexels-8361491.jpg',
    'pexels-36779953.jpg', 'pexels-36779951.jpg', 'pexels-18490314.jpg', 'pexels-36615511.jpg', 'pexels-36422209.jpg',
    'pexels-16537109.jpg', 'pexels-36422211.jpg', 'pexels-14089085.jpg', 'pexels-13675701.jpg', 'pexels-12007864.jpg',
    'pexels-29487576.jpg', 'pexels-29487521.jpg', 'pexels-36589849.jpg', 'pexels-27393225.jpg', 'pexels-36406238.jpg',
    'pexels-8046199.jpg', 'pexels-30127959.jpg', 'pexels-12507443.jpg', 'pexels-6938742.jpg', 'pexels-36740886.jpg',
    'pexels-20321033.jpg', 'pexels-33491333.jpg', 'pexels-33924345.jpg', 'pexels-27898368.jpg', 'pexels-38674155.jpg',
    'pexels-32709882.jpg', 'pexels-14991564.jpg', 'pexels-36638414.jpg', 'pexels-8450116.jpg', 'pexels-8450510.jpg',
    'pexels-8450117.jpg', 'pexels-10536619.jpg', 'pexels-8450134.jpg', 'pexels-12236651.jpg', 'pexels-5908368.jpg',
    'pexels-8450252.jpg', 'pexels-20805367.jpg', 'pexels-11567050.jpg', 'pexels-6069729.jpg', 'pexels-8145863.jpg',
    'pexels-6387841.jpg', 'pexels-3845162.jpg', 'pexels-32248357.jpg', 'pexels-8942905.jpg', 'pexels-19815864.jpg',
    'pexels-17695484.jpg', 'pexels-3693296.jpg', 'pexels-33015766.jpg', 'pexels-17837242.jpg', 'pexels-39070285.jpg',
    'pexels-8450504.jpg', 'pexels-5872188.jpg', 'pexels-5872179.jpg',
]
VIDEOS = [V_INCHIOSTRO, V_IRIS, V_GONDOLE, V_FLACONE, V_FUMO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=40, color=NOTTE, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


# ══════════════════════════════════════════════════════════════════════════════
#  LE SEI ORE
# ══════════════════════════════════════════════════════════════════════════════

ORE = [
    {'ora': '05:40', 'nome': 'Fondamenta', 'chip': 'All’alba', 'foto': 'pexels-36589849.jpg', 'pos': 'center center',
     'note': 'iris pallido · aldeidi · muschio bianco', 'famiglia': 'polveroso, fresco',
     'storia': 'La nebbia sulle Fondamenta Nuove prima del primo vaporetto: si vede appena l’acqua, si sente il bucato '
               'steso nelle corti.', 'punti': 'fresco:30,fiori:40,legno:10,fumo:0'},
    {'ora': '09:15', 'nome': 'Rialto', 'chip': 'Di mattina', 'foto': 'pexels-20321033.jpg', 'pos': 'center center',
     'note': 'foglia di fico · bergamotto · caffè', 'famiglia': 'verde, vivace',
     'storia': 'Le casse di fichi al mercato, il bergamotto delle bancarelle, l’espresso al banco dietro la Pescheria.',
     'punti': 'fresco:40,fiori:10,legno:20,fumo:10'},
    {'ora': '12:30', 'nome': 'Zattere', 'chip': 'A mezzogiorno', 'foto': 'pexels-17695484.jpg', 'pos': 'center center',
     'note': 'sale · neroli · pietra d’Istria calda', 'famiglia': 'salato, luminoso',
     'storia': 'Il sole sulla pietra bianca delle Zattere, il vento dalla Giudecca, le mani che sanno di sale.',
     'punti': 'fresco:40,fiori:30,legno:10,fumo:0'},
    {'ora': '17:50', 'nome': 'Lido', 'chip': 'Al tramonto', 'foto': 'pexels-8361490.jpg', 'pos': 'center 60%',
     'note': 'fiore d’arancio · ambra · pelle salata', 'famiglia': 'caldo, solare',
     'storia': 'Le cabine che si chiudono, la sabbia ancora calda, l’ultimo vaporetto per San Marco col sole basso.',
     'punti': 'fresco:10,fiori:30,legno:30,fumo:10'},
    {'ora': '20:31', 'nome': 'Ora blu', 'chip': 'All’ora blu', 'foto': 'pexels-32709882.jpg', 'pos': 'center center',
     'note': 'violetta · incenso · acqua di laguna', 'famiglia': 'il nostro primo, dal 1931',
     'storia': 'Quel minuto, dopo il tramonto, in cui l’acqua e il cielo hanno lo stesso colore. Il profumo con cui '
               'abbiamo cominciato.', 'punti': 'fresco:10,fiori:40,legno:20,fumo:30'},
    {'ora': '23:45', 'nome': 'Laguna', 'chip': 'A mezzanotte', 'foto': 'pexels-32248357.jpg', 'pos': 'center center',
     'note': 'vetiver · cuoio · candela spenta', 'famiglia': 'scuro, legnoso',
     'storia': 'Le calli vuote, una finestra accesa sul rio, il fumo di una candela appena spenta.',
     'punti': 'fresco:0,fiori:10,legno:40,fumo:40'},
]
PREZZO_50 = '148 €'


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=64, align='left', layout='split', size_mobile=36, color=INK):
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': TENUE, 'eyebrow_dot_color': INDACO,
        'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '400',
        'headline_lines': [{'text': l, 'color': color if i % 2 == 0 else LAVANDA, 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.06,
        'headline_font_weight': '400', 'headline_align': align,
        'headline_font_size_tablet': max(36, int(size * 0.8)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 15, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.2fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=INK, tag='h3', pad_bottom=10, align='left', upper=False, spacing=None):
    """Il Titolo toglie i tag: la parte fra <em> diventa il «testo accento», color lavanda (Tenor Sans non ha corsivo)."""
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': upper, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': '400', 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': False, 'accent_color': LAVANDA if color == INK else SOFT_CHIARO})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(30, int(size * 0.58)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=TENUE, pad_bottom=14, align='left', pad_top=0):
    """Le ore e le didascalie in monospazio, maiuscolo e spaziato: l'orario dei vaporetti."""
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 11, 'heading_weight': '400',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '2'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=16, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.8, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='filo', align='left', pad_top=14):
    if kind == 'pieno':
        o = {'bg_color': INDACO, 'text_color': ON_INDACO, 'hover_bg_color': INK, 'hover_text_color': CHIARO}
    elif kind == 'chiaro':
        o = {'bg_color': 'transparent', 'text_color': CHIARO, 'hover_bg_color': CHIARO, 'hover_text_color': NOTTE,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(CHIARO, 70), 'shadow_inset': True}
    else:
        o = {'bg_color': 'transparent', 'text_color': INK, 'hover_bg_color': INK, 'hover_text_color': FONDO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': FILO_INK, 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 0, 'font_size': 11, 'font_weight': '500',
              'text_transform': 'uppercase', 'letter_spacing': '2.5'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', drift=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': False, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -24, 'position': 0}, {'value': 24, 'position': 100}]}} if drift else None)


def video(t, name, ratio='4:5'):
    return t.bare('video', {'source_type': 'file', 'video_url': '', 'file_url': img(name + '.mp4'),
                            'poster_image': img(name + '.jpg'), 'display_mode': ratio, 'aspect_ratio': ratio, 'cover_mode': False,
                            'object_position': 'center center', 'border_radius': 0, 'autoplay': True, 'muted': True,
                            'loop': True, 'controls': False, 'show_play_icon': False, 'facade': False})


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs, color=INK):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'vertical', 'term_color': TENUE, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': color, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t, color=INK, icon=INDACO):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': ATELIER + '#bottega', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': icon, 'icon_size': '16', 'text_color': color, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': INK, 'header_text_color_active': INDACO, 'header_font_size': 21, 'header_font_weight': '400',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': INK, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, lines, body, size=88):
    return t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=42)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=16, color=TENUE)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], padding='large', style={'padding_top': 150, 'padding_bottom': 64})


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': 'ORA BLU', 'logo_text_size': '20', 'logo_text_color': INK,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 6, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'center', 'header_mode': 'classic', 'sticky': True,
        'sticky_bg': mix(FONDO, 90), 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': FONDO, 'text_color': INK, 'hover_color': INDACO, 'active_color': INDACO,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2.5', 'item_gap': '36',
        'bar_width': 'wide', 'nav_height': '80', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': INK,
        'button_mode': 'last', 'btn_bg': INK, 'btn_color': CHIARO, 'btn_hover_color': CHIARO,
        'btn_radius': SQ, 'btn_padding': {'top': 12, 'right': 20, 'bottom': 12, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '500', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2.5',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': NOTTE, 'mobile_text_color': CHIARO, 'mobile_accent_color': LAVANDA,
        'hamburger_style': 'minimal', 'hamburger_color': INK, 'mobile_font_size': '34', 'mobile_link_size': '34',
        'mobile_link_font': 'heading', 'mobile_link_weight': '400',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': FILO},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


# ══════════════════════════════════════════════════════════════════════════════
#  FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'ORA BLU', size=96, color=CHIARO, tag='p', pad_bottom=4, spacing=14)
    ore = t.classic('marquee', {
        'content_type': 'text', 'separator': '   ·   ',
        'text_items': ' | '.join('%s %s' % (o['ora'], o['nome']) for o in ORE),
        'speed': '60', 'direction': 'left', 'pause_hover': True, 'gap': 30, 'velocity_skew': False,
        'bg_color': 'transparent', 'text_color': SOFT_CHIARO, 'font_size': 13, 'font_weight': '400', 'letter_spacing': 2,
        'text_transform': 'uppercase', 'font_family': MONO, 'font_style': 'normal', 'separator_color': LAVANDA, 'height': 48,
    })
    claim = text(t, '<p>Profumeria d’autore in calle de la Mandola, dal 1931. Sei profumi, uno per ogni ora della giornata '
                    'veneziana, macerati sei settimane in una cantina sotto il livello del mare.</p>', size=15,
                 color=SOFT_CHIARO, pad=(10, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': CHIARO, 'link_hover_color': LAVANDA, 'active_color': LAVANDA,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('La collezione', COLLEZIONE), ('L’atelier', ATELIER), ('I campioni', CAMPIONI),
                  ('Spedizioni e resi', CAMPIONI + '#domande'), ('Privacy', '/privacy-policy/')])
    orari = text(t, '<p>Lunedì — sabato 10 — 19<br>domenica 11 — 18<br>chiuso il martedì</p><p>L’ultimo giovedì del mese '
                    'restiamo aperti fino alle 21:30, all’ora blu.</p>', size=15, color=CHIARO)
    legal = text(t, '<p>© 2026 Ora Blu · Profumeria d’autore, Venezia · Extrait de parfum, 22% di essenze · Spedizioni in '
                    'tutta Europa in tre giorni.</p>', size=12, color=mix(CHIARO, 55))
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': SOFT_CHIARO, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': INK, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([brand, claim, contacts(t, color=CHIARO, icon=LAVANDA)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine', color=LAVANDA), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'La bottega', color=LAVANDA), orari], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO_CHIARO},
                          'padding_top': 22, 'margin_top': 56})
    return [t.section([t.one([ore], anim=None, style={'margin_bottom': 64}), top, bottom], bg=solid(NOTTE), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def build_home():
    t = Tpl(ID, 'home')
    out = []

    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Profumeria d’autore · Venezia, dal 1931', 'eyebrow_dot': False,
        'title': 'Sei ore<br>di Venezia.',
        'subtitle': 'Sei profumi, uno per ogni ora della giornata in laguna: dalla nebbia delle cinque e quaranta alla candela '
                    'spenta di mezzanotte.',
        'media_bg': {'type': 'video', 'video_url': img(V_INCHIOSTRO + '.mp4'), 'video_poster': img(V_INCHIOSTRO + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': FONDO, 'overlay_top': 0.25, 'overlay_bottom': 0.62, 'overlay_sides': True, 'overlay_direction': '',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': INDACO, 'text_color': INK, 'title_color': INK, 'subtitle_color': INK,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 128, 'title_font_weight': '400',
        'title_line_height': '0.98', 'title_letter_spacing': '-1', 'title_text_transform': 'none',
        'subtitle_font_size': 16, 'subtitle_max_width': 440,
        'min_height': '100vh', 'content_max_width': 980, 'vertical_align': 'center', 'horizontal_align': 'center',
        'text_align': 'center', 'tile_padding': {'top': 140, 'right': 40, 'bottom': 120, 'left': 40},
        'cta_text': 'A che ora ti senti?', 'cta_url': '#ora', 'cta_bg_color': INK, 'cta_text_color': CHIARO,
        'cta_radius': SQ, 'cta_size': '11', 'cta_style': 'filled', 'cta_font_weight': '500', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2.5',
        'cta2_text': 'La collezione', 'cta2_url': COLLEZIONE, 'cta2_text_color': INK, 'cta2_style': 'ghost',
        'meta_text': '45°26′ N · 12°20′ E · ore 20:31', 'scroll_hint': 'Scorri', 'scene_depth': 'none', 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    lista = t.classic('hoverlist', {
        'items': [{'name': o['nome'], 'sub': o['note'], 'desc': o['note'], 'number': o['ora'],
                   'image': img(o['foto']), 'link_url': COLLEZIONE + '#' + o['nome'].lower().replace(' ', '-'), 'color': ''}
                  for o in ORE],
        'lead_mode': 'number', 'number_color': TENUE, 'number_hover_color': INDACO, 'mono_font_family': MONO,
        'name_font_family': 'heading', 'name_weight': '400', 'name_color': INK, 'name_hover_color': INDACO, 'name_size': 56,
        'name_uppercase': True, 'desc_color': TENUE, 'desc_hover_color': INK, 'desc_size': 13, 'sub_uppercase': False,
        'row_padding_y': 24, 'hover_indent': 28, 'hover_bg': 'transparent', 'line_color': FILO,
        'peek': True, 'peek_mode': 'image', 'peek_width': 260, 'peek_ratio': '3/4',
        'row_padding': {'top': 24, 'right': 0, 'bottom': 24, 'left': 0},
    })
    out.append(t.section([
        t.one([sechead(t, 'La collezione · extrait 50 ml · 148 €', ['Un profumo', 'per ogni ora.'], size=64,
                       tagline='Passate sopra le ore: compare la Venezia da cui viene ognuno. Si portano insieme, uno di giorno '
                               'e uno di sera, o da soli per tutta la vita.')]),
        t.one([lista]),
    ], padding='large', html_id='ore', style={'padding_top': 140}))

    cielo = t.classic('goo', {
        'scope': 'section', 'mode': 'aurora', 'color_1': '#B9BBE3', 'color_2': '#2C2F6E', 'color_3': '#D8B9A4',
        'color_4': '#8E8FBF', 'color_5': '', 'blob_count': 6, 'blob_size_min': 160, 'blob_size_max': 320,
        'drift_speed': 0.35, 'drift_amplitude': 1, 'goo_strength': 26, 'follow_cursor': False, 'cursor_blob_size': 140,
        'aurora_blur': 90, 'blend_mode': 'normal', 'layer_opacity': 85, 'content': '', 'min_height': 0,
        'base_color': '#14152A', 'full_width': True,
    })
    out.append(t.section([
        t.one([cielo], anim=None),
        centered(t, [label(t, '20:31 · l’ora blu', color=SOFT_CHIARO, align='center'),
                     headline(t, 'C’è un minuto, dopo il tramonto, in cui l’acqua e il cielo <em>hanno lo stesso colore.</em>',
                              size=60, color=CHIARO, tag='h2', align='center', pad_bottom=20),
                     text(t, '<p>I veneziani lo chiamano l’ora blu. Ottavio Zen, nel 1931, ci fece il suo primo profumo: '
                             'violetta, incenso e l’odore dell’acqua quando si ferma. Lo facciamo ancora così.</p>',
                          size=16, color=SOFT_CHIARO, align='center')]),
    ], bg=solid(NOTTE), padding='xlarge', html_id='ora-blu', style={'padding_top': 200, 'padding_bottom': 200}))

    finder = t.classic('finder', {
        'preset': 'custom', 'eyebrow': 'Il profumo giusto', 'heading': 'A che ora <em>ti senti</em>?',
        'intro': 'Non a che ora lo metti: a che ora del giorno ti riconosci. Scegli, e ti diciamo quale portare.',
        'items': [{'option': o['chip'], 'icon': '', 'kicker': o['ora'] + ' · ' + o['famiglia'], 'title': o['nome'],
                   'text': o['storia'] + ' ' + o['note'].capitalize() + '.', 'meta': 'Extrait 50 ml · ' + PREZZO_50,
                   'cta_text': 'Scopri ' + o['nome'], 'cta_url': COLLEZIONE + '#' + o['nome'].lower().replace(' ', '-'),
                   'image': img(o['foto']), 'media_label': ''} for o in ORE],
        'default_index': '4', 'zone_accent': INDACO, 'zone_on': CHIARO, 'align': 'center',
        'chip_bg': 'transparent', 'chip_radius': radius(0), 'card_bg': CHIARO, 'card_border': '', 'media_bg': NEBBIA,
        'media_ratio': '3/4', 'media_ratio_custom': '3/4', 'object_position': 'center center', 'card_max_width': 820,
        'card_radius': radius(0), 'card_padding': {'top': 36, 'right': 40, 'bottom': 36, 'left': 36}, 'shadow': 'none',
        'tile_padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0},
        'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''},
    })
    out.append(t.section([t.one([finder])], bg=solid(NEBBIA), padding='large', html_id='ora',
                         style={'padding_top': 130, 'padding_bottom': 130}))

    out.append(t.section([
        t.row([t.col([video(t, V_FLACONE, ratio='4:5'), label(t, 'Il flacone, in mano', pad_top=14, pad_bottom=0)], wm='1-2', ws='1-1'),
               t.col([label(t, 'L’oggetto'),
                      headline(t, 'Un flacone <em>che si tiene in mano</em>, non sulla mensola.', size=52, tag='h2', pad_bottom=18),
                      text(t, '<p>Vetro soffiato a Murano, tappo di ottone brunito, etichetta scritta a mano con l’ora e il '
                              'numero del lotto. Si ricarica in bottega o per posta, senza buttare niente.</p>'),
                      specs(t, [('Vetro', 'soffiato a Murano, 50 o 100 ml'), ('Tappo', 'ottone brunito, magnetico'),
                                ('Concentrazione', 'extrait de parfum, 22%'), ('Dura sulla pelle', 'dalle otto alle dodici ore')]),
                      button(t, 'Formati e prezzi', COLLEZIONE + '#formati', pad_top=20)], wm='1-2', ws='1-1')],
              layout='50-50', gap=88, valign='center'),
    ], padding='large', html_id='flacone', style={'padding_top': 140, 'padding_bottom': 140}))

    out.append(t.section([
        centered(t, [label(t, 'L’atelier · San Marco', color=SOFT_CHIARO, align='center'),
                     headline(t, 'Ogni profumo matura sei settimane <em>sotto il livello del mare.</em>', size=60,
                              color=CHIARO, tag='h2', align='center', pad_bottom=18),
                     text(t, '<p>La cantina della bottega ha sempre quattordici gradi e l’umidità della laguna. È lì che le '
                             'essenze si sposano, in damigiane di vetro, prima di essere filtrate a mano.</p>', size=16,
                          color=SOFT_CHIARO, align='center'),
                     button(t, 'L’atelier', ATELIER, kind='chiaro', align='center')]),
    ], bg=video_bg(V_GONDOLE, overlay=46, color=NOTTE), padding='xlarge', html_id='atelier',
        style={'padding_top': 220, 'padding_bottom': 220}))

    out.append(t.section([
        t.row([t.col([label(t, 'I campioni'),
                      headline(t, 'Sei ore in sei fiale, <em>38 €.</em>', size=60, tag='h2', pad_bottom=18),
                      text(t, '<p>Una scatola con le sei ore da due millilitri: una settimana per capire quale è la tua. I 38 € '
                              'si scalano dal primo flacone.</p>'),
                      button(t, 'Componi la scatola', CAMPIONI, kind='pieno', pad_top=20)], wm='1-2', ws='1-1'),
               t.col([image(t, 'pexels-5872179.jpg', 'Due mani che chiudono una scatola bianca con un nastro nero',
                            ratio='5/4', drift=True)], wm='1-2', ws='1-1')],
              layout='50-50', gap=88, valign='center'),
    ], bg=solid(NEBBIA), padding='large', html_id='campioni', style={'padding_top': 130, 'padding_bottom': 130}))

    lettera = t.classic('newsletter', {
        'layout': 'vertical', 'preset': 'custom', 'eyebrow': 'Una lettera alla luna nuova', 'eyebrow_color': TENUE,
        'title_accent_color': LAVANDA, 'title': 'Le ore nuove, <em>prima</em> degli altri.',
        'subtitle': 'Una lettera ogni luna nuova: un profumo in arrivo, una storia della bottega, l’invito al giovedì dell’ora blu.',
        'icon_type': 'none', 'show_name': False, 'email_placeholder': 'La tua email', 'button_text': 'Iscrivimi',
        'button_icon': False, 'max_width': '640', 'alignment': 'center', 'bg_color': 'transparent', 'box_border': 'transparent',
        'border_radius': 0, 'tile_padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0}, 'title_size': '40',
        'title_weight': '400', 'title_color': INK, 'subtitle_size': '15', 'subtitle_color': TENUE,
        'input_bg': 'transparent', 'input_color': INK, 'input_radius': 0, 'btn_radius': 0,
    })
    out.append(t.section([centered(t, [lettera])], padding='large', style={'padding_top': 130, 'padding_bottom': 130}))
    return page(out, page_intro_enabled=True, page_intro_style='sipario', page_intro_text=NAME,
                page_intro_caption='Venezia · ore 20:31', page_intro_bg=HEX['dark'], page_intro_color=HEX['light'],
                page_intro_duration=2300, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  LA COLLEZIONE
# ══════════════════════════════════════════════════════════════════════════════

def build_collezione():
    t = Tpl(ID, 'collezione')
    out = [intro(t, 'La collezione', ['Sei ore,', 'sei profumi.'],
                 '<p>Si scelgono dall’ora, non dalla famiglia olfattiva. Passate sopra le carte per girarle: dietro ci sono le '
                 'note, la storia e il prezzo.</p>')]

    def carta(o):
        return t.classic('flipcard', {
            'front_media': {'type': 'image', 'image_url': img(o['foto']), 'image_size': 'cover', 'image_position': o['pos'],
                            'overlay_color': NOTTE, 'overlay_opacity': 30},
            'back_media': {'type': 'none'}, 'bg': {'type': 'none'}, 'preset': 'custom',
            'front_title': o['ora'] + '  ' + o['nome'].upper(), 'front_description': o['note'],
            'front_text_color': CHIARO, 'front_text_align': 'left', 'front_valign': 'bottom',
            'back_title': o['nome'], 'back_description': o['storia'] + '<br><br>' + o['note'].capitalize() + '.<br>Extrait 50 ml · '
                                                         + PREZZO_50,
            'back_bg': CHIARO, 'back_text_color': INK, 'back_text_align': 'left', 'back_valign': 'center',
            'back_cta_text': 'Lo voglio', 'back_cta_url': CAMPIONI + '?profumo=' + o['nome'].replace(' ', '%20'),
            'back_cta_bg': INK, 'back_cta_color': CHIARO, 'back_cta_radius': '0',
            'flip_direction': 'horizontal', 'flip_duration': '900', 'flip_trigger': 'hover', 'flip_easing': 'ease-in-out',
            'card_height': '520', 'card_border_radius': '0', 'card_shadow': 'none',
            'card_border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': FILO},
            'tile_padding': {'top': 28, 'right': 28, 'bottom': 28, 'left': 28}, 'title_size': '22', 'title_weight': '400',
            'desc_size': '13',
        }, advanced={'html_id': o['nome'].lower().replace(' ', '-')})
    for i in range(0, 6, 3):
        out.append(t.section([t.row([t.col([carta(o)], wm='1-3', ws='1-1') for o in ORE[i:i + 3]], layout='33-33-33', gap=24)],
                             padding='remove-vertical', style={'padding_bottom': 24}))

    formati = t.classic('pricelist', {
        'items': [{'title': 'Extrait 50 ml', 'description': 'Il flacone di Murano col tappo di ottone. Dura dalle otto alle dodici ore.',
                   'price': '148 €', 'image_url': img('pexels-36779953.jpg'), 'highlighted': False, 'badge': ''},
                  {'title': 'Extrait 100 ml', 'description': 'Lo stesso flacone, più alto di quattro centimetri.',
                   'price': '210 €', 'image_url': img('pexels-36779951.jpg'), 'highlighted': False, 'badge': ''},
                  {'title': 'Da viaggio, 10 ml', 'description': 'Vetro e ottone in tasca: passa i controlli in aeroporto.',
                   'price': '42 €', 'image_url': img('pexels-12236651.jpg'), 'highlighted': False, 'badge': ''},
                  {'title': 'Ricarica 100 ml', 'description': 'In bottega o per posta, nel flacone che hai già.',
                   'price': '160 €', 'image_url': img('pexels-8361481.jpg'), 'highlighted': False, 'badge': ''}],
        'separator_style': 'solid', 'separator_color': FILO, 'title_color': INK, 'price_color': AMBRA,
        'description_color': TENUE, 'image_size': 64, 'image_border_radius': '0', 'show_image': True,
        'price_position': 'right', 'tile_padding': {'top': 18, 'right': 0, 'bottom': 18, 'left': 0}, 'card_bg': 'transparent',
        'card_border_radius': '0',
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Formati', ['Quattro misure,', 'un solo vetro.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Tutti i profumi costano uguale: le essenze dell’ora blu e quelle di mezzogiorno pesano lo '
                              'stesso nel flacone.</p>')], wm='1-3', ws='1-1'),
               t.col([formati], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large', html_id='formati', style={'padding_top': 140}))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Come si porta', ['Un profumo d’ora', 'si mette a tempo.'], size=38, layout='stack',
                              size_mobile=34)], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Se ne può portare più di uno?', 'Sì, uno sopra l’altro: Rialto la mattina e Laguna la sera stanno bene insieme, '
                                                    'perché il primo se ne va quando arriva il secondo.'),
                   ('Dove si mette?', 'Sui polsi senza strofinare, dietro le orecchie, sulla sciarpa. Sulla lana dura il doppio.'),
                   ('Quanto dura un flacone?', 'Due spruzzi al giorno: un 50 ml dura circa otto mesi.'),
                   ('Perché non c’è l’eau de toilette?', 'Perché l’extrait è più lento: si apre in un’ora e resta tutto il giorno, '
                                                       'come l’ora da cui viene.'),
               ], 'porta')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(NEBBIA), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  L'ATELIER
# ══════════════════════════════════════════════════════════════════════════════

STORIA = [
    ('1931', 'Ottavio Zen apre in calle de la Mandola', 'Un banco di legno, sei damigiane e il primo profumo: Ora blu.'),
    ('1966', 'L’acqua alta', 'Il 4 novembre la cantina va sott’acqua. Le damigiane, tappate, si salvano tutte.'),
    ('1989', 'Le altre ore', 'Lucia Zen aggiunge Fondamenta e Laguna: l’alba e la mezzanotte.'),
    ('2014', 'Rialto, Zattere e Lido', 'Le tre ore del giorno, con le essenze di tre piccole distillerie del Veneto.'),
    ('2026', 'La bottega oggi', 'Tre persone, un naso, sei profumi. Nessuna linea nuova in arrivo: le ore sono ventiquattro, ma '
     'ce ne bastano sei.'),
]


def build_atelier():
    t = Tpl(ID, 'atelier')
    out = [intro(t, 'L’atelier', ['Una bottega,', 'un naso.'],
                 '<p>In calle de la Mandola, fra Campo Sant’Angelo e Campo Manin. Al piano terra si provano i profumi, nella '
                 'cantina maturano, al primo piano li compone Marco Zen, nipote di Ottavio.</p>')]

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-8450117.jpg', 'Il naso della bottega mentre versa un’essenza in un imbuto di vetro',
                            ratio='4/5', position='center 30%')], wm='1-2', ws='1-1'),
               t.col([label(t, 'Il naso'),
                      headline(t, 'Marco Zen, <em>terza generazione.</em>', size=52, tag='h2', pad_bottom=18),
                      text(t, '<p>Ha imparato dal nonno a riconoscere sessanta essenze a occhi chiusi, poi ha studiato a '
                              'Grasse. Compone un profumo in due anni e lo cambia di un grammo alla volta.</p><p>«Un’ora si '
                              'riconosce dall’odore prima che dalla luce. Alle cinque e quaranta Venezia sa di bucato e di '
                              'nebbia: ci ho messo sei anni a metterlo in un flacone.»</p>'),
                      specs(t, [('In bottega', 'dal lunedì al sabato, il mattino'), ('Consulenze', 'gratuite, su appuntamento, 40 minuti')])],
                     wm='1-2', ws='1-1')], layout='50-50', gap=88, valign='center'),
    ], padding='large'))

    metodo = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Le essenze', 'description': 'Iris di Firenze, bergamotto di Reggio, vetiver di Haiti, '
                                                                          'violetta di Tourrettes.'},
                  {'number': '02', 'title': 'La formula', 'description': 'Pesate a mano al decimo di grammo, sul quaderno di Ottavio.'},
                  {'number': '03', 'title': 'La cantina', 'description': 'Sei settimane in damigiana, a quattordici gradi, sotto il '
                                                                         'livello del mare.'},
                  {'number': '04', 'title': 'Il filtro', 'description': 'Carta e freddo, una notte intera: niente coloranti.'},
                  {'number': '05', 'title': 'Il flacone', 'description': 'Riempito, tappato e scritto a mano, con l’ora e il lotto.'}],
        'columns': 5, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': LAVANDA, 'number_bg': '',
        'number_size': 15, 'number_font': MONO, 'number_weight': '400', 'title_color': INK, 'title_size': 22,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 14, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO_INK},
        'card_radius': radius(0), 'card_padding': 22, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, 'Il metodo', ['Cinque passaggi,', 'otto settimane.'], size=56,
                       tagline='Un profumo d’ora si fa lentamente: un lotto ogni due mesi, trecento flaconi, numerati.')]),
        t.one([metodo]),
    ], bg=solid(NEBBIA), padding='large'))

    out.append(t.section([
        centered(t, [label(t, 'La cantina · −1,20 m', color=SOFT_CHIARO, align='center'),
                     headline(t, 'Quattordici gradi, <em>il buio, la laguna intorno.</em>', size=60, color=CHIARO, tag='h2',
                              align='center', pad_bottom=18),
                     text(t, '<p>Le damigiane stanno su un pavimento di mattoni che d’inverno, con l’acqua alta, si bagna. '
                             'Le essenze lo sentono: i nostri profumi hanno un fondo salato che non si riesce a copiare altrove.</p>',
                          size=16, color=SOFT_CHIARO, align='center')]),
    ], bg=video_bg(V_FUMO, overlay=30), padding='xlarge', style={'padding_top': 200, 'padding_bottom': 200}))

    filo = {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO}
    righe = [t.row([t.col([label(t, anno, color=INDACO, pad_bottom=0, pad_top=8)], wm='1-6', ws='1-1'),
                    t.col([headline(t, titolo, size=26, tag='h3', pad_bottom=0)], wm='1-3', ws='1-1'),
                    t.col([text(t, '<p>' + frase + '</p>', size=15)], wm='1-2', ws='1-1')],
                   layout='custom', custom='16,34,50', gap=24, valign='start',
                   style={'border': filo, 'padding_top': 22, 'padding_bottom': 18})
             for anno, titolo, frase in STORIA]
    out.append(t.section([
        t.one([sechead(t, 'La storia', ['Novantacinque anni', 'in calle de la Mandola.'], size=56,
                       tagline='La bottega ha cambiato tre volte insegna e mai indirizzo.')]),
    ] + righe + [t.one([], anim=None, style={'border': filo})], padding='large'))

    out.append(t.section([
        t.row([t.col([image(t, f, a, ratio='4/5')], wm='1-3', ws='1-1') for f, a in [
            ('pexels-6069729.jpg', 'Bottiglie e alambicchi di vetro trasparente sul banco, con le loro ombre'),
            ('pexels-8450134.jpg', 'Dall’alto: il banco del naso con le essenze, le cartine e un mortaio'),
            ('pexels-8145863.jpg', 'Bicchieri e caraffe di cristallo su un tavolo bianco, nella luce del pomeriggio')]],
              layout='33-33-33', gap=20),
    ], padding='remove-vertical', style={'padding_bottom': 40}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '16', 'height': '460', 'marker': True,
                                 'marker_popup': 'Ora Blu · calle de la Mandola 3712', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'positron', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    orari = t.classic('hoursstrip', {
        'items': [{'day': 'lunedì — sabato', 'time': '10 — 19', 'note': 'il martedì chiuso'},
                  {'day': 'domenica', 'time': '11 — 18', 'note': 'solo la bottega, la cantina è chiusa'},
                  {'day': 'ultimo giovedì', 'time': 'fino alle 21:30', 'note': 'aperti all’ora blu, con un bicchiere'}],
        'columns': 3, 'band_padding_y': 26, 'show_dividers': True, 'divider_color': FILO, 'band_border': False,
        'day_color': TENUE, 'day_size': 11, 'time_font_family': 'heading', 'time_color': INK, 'time_size': 32,
        'time_weight': '400', 'note_color': TENUE, 'note_size': 14, 'mono_font_family': MONO,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'La bottega', ['Calle de la Mandola,', 'numero 3712.'], size=44, layout='stack',
                              size_mobile=32),
                      contacts(t),
                      text(t, '<p>Dalla fermata Sant’Angelo della linea 1 sono quattro minuti a piedi. Dal ponte di Rialto, '
                              'dieci.</p>', size=15, pad=(16, 0, 0, 0))], wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
        t.one([orari], style={'margin_top': 48}),
    ], bg=solid(NEBBIA), padding='large', html_id='bottega'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  I CAMPIONI
# ══════════════════════════════════════════════════════════════════════════════

def build_campioni():
    t = Tpl(ID, 'campioni')
    out = [intro(t, 'I campioni', ['Una settimana', 'per trovare la tua ora.'],
                 '<p>Scegli tre ore o tutte e sei, la misura delle fiale e la scatola. Le barre a destra dicono che profumo avrà la '
                 'tua settimana: più fresco, più fiorito, più legno o più fumo.</p>')]

    opzioni = []
    for o in ORE:
        opzioni.append({'id': 'ora-' + o['ora'].replace(':', ''), 'gruppo': 'ore', 'nome': o['ora'] + ' · ' + o['nome'],
                        'descrizione': o['note'], 'prezzo': 6, 'immagine': '', 'badge': 'il primo' if o['nome'] == 'Ora blu' else '',
                        'predefinita': o['nome'] in ('Fondamenta', 'Ora blu', 'Laguna'), 'punti': o['punti'], 'min': 0, 'max': 10})
    opzioni += [
        {'id': 'f2', 'gruppo': 'misura', 'nome': 'Fiale da 2 ml', 'descrizione': 'Una settimana per ogni ora.', 'prezzo': 0,
         'immagine': '', 'badge': '', 'predefinita': True, 'punti': '', 'min': 0, 'max': 10},
        {'id': 'f5', 'gruppo': 'misura', 'nome': 'Fiale da 5 ml', 'descrizione': 'Un mese: per chi vuole esserne sicuro.', 'prezzo': 12,
         'immagine': '', 'badge': '', 'predefinita': False, 'punti': '', 'min': 0, 'max': 10},
        {'id': 'busta', 'gruppo': 'scatola', 'nome': 'Busta di carta', 'descrizione': 'Carta di Amalfi, sigillo di ceralacca.',
         'prezzo': 2, 'immagine': '', 'badge': '', 'predefinita': True, 'punti': '', 'min': 0, 'max': 10},
        {'id': 'lacca', 'gruppo': 'scatola', 'nome': 'Scatola laccata', 'descrizione': 'Blu notte, per un regalo. Dentro, il biglietto.',
         'prezzo': 14, 'immagine': '', 'badge': 'regalo', 'predefinita': False, 'punti': '', 'min': 0, 'max': 10},
        {'id': 'nome', 'gruppo': 'extra', 'nome': 'Il nome sulle fiale', 'descrizione': 'Scritto a mano, come le etichette dei flaconi.',
         'prezzo': 5, 'immagine': '', 'badge': '', 'predefinita': False, 'punti': '', 'min': 0, 'max': 10},
    ]
    scatola = t.classic('configuratore', {
        'gruppi': [{'id': 'ore', 'titolo': 'Le ore', 'tipo': 'piu', 'obbligatorio': True,
                    'aiuto': 'Tre ore per cominciare, sei per non sbagliare. Ogni fiala costa 6 €.'},
                   {'id': 'misura', 'titolo': 'Le fiale', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'scatola', 'titolo': 'La scatola', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
                   {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''}],
        'opzioni': opzioni, 'prezzo_base': 0, 'valuta': '€', 'testo_totale': 'La tua scatola',
        'nota_prezzo': 'Spedizione gratuita in Italia · il prezzo si scala dal primo flacone', 'periodo': '',
        'mostra_differenze': True,
        'indicatori': [{'nome': 'Fresco', 'id': 'fresco', 'massimo': 120}, {'nome': 'Fiori', 'id': 'fiori', 'massimo': 120},
                       {'nome': 'Legno', 'id': 'legno', 'massimo': 120}, {'nome': 'Fumo', 'id': 'fumo', 'massimo': 120}],
        'riepilogo': True, 'titolo_riepilogo': 'Nella scatola', 'testo_azzera': 'Ricomincia', 'testo_pulsante': 'Ordina la scatola',
        'link_azione': 'mailto:' + MAIL + '?subject=Scatola%20dei%20campioni&body={configurazione}', 'campo_modulo': '',
        'vista_opzioni': 'righe', 'disposizione': 'lato', 'sfondo_scheda': CHIARO, 'colore_scelta': INDACO, 'colore_testo': INK,
        'colore_tenue': TENUE, 'colore_prezzo': AMBRA, 'sfondo_riepilogo': NOTTE, 'sfondo_badge': LAVANDA, 'colore_badge': NOTTE,
        'raggio_scheda': radius(0), 'ombra': 'none', 'gruppo_font_family': 'heading', 'gruppo_font_size': 24,
        'gruppo_font_weight': '400', 'gruppo_color': INK, 'opzione_font_family': 'sans-serif', 'opzione_font_size': 16,
        'opzione_font_weight': '500', 'descrizione_font_family': 'sans-serif', 'descrizione_font_size': 13,
        'descrizione_font_weight': '400', 'prezzo_font_family': MONO, 'prezzo_font_size': 14, 'prezzo_font_weight': '400',
        'totale_font_family': 'heading', 'totale_font_size': 56, 'totale_font_weight': '400', 'totale_color': CHIARO, 'gap': 14,
    })
    out.append(t.section([t.one([scatola])], padding='large', html_id='scatola', style={'padding_top': 20}))

    fusi = t.classic('timezone', {
        'eyebrow': 'Spediamo in tre giorni', 'heading': 'Quando qui è l’ora blu, da te che ore sono?',
        'intro': 'Spostate l’ora di Venezia: vedete l’ora nelle città dove arrivano più scatole.',
        'base_label': 'A Venezia', 'input_value': 20, 'work_start': 9, 'work_end': 19,
        'items': [{'city': 'Venezia', 'tz': 'Europe/Rome', 'offset': 1, 'label': ''},
                  {'city': 'Londra', 'tz': 'Europe/London', 'offset': 0, 'label': ''},
                  {'city': 'New York', 'tz': 'America/New_York', 'offset': -5, 'label': ''},
                  {'city': 'Tokyo', 'tz': 'Asia/Tokyo', 'offset': 9, 'label': ''}],
        'zone_accent': INDACO, 'work_color': '', 'ok_color': LAVANDA, 'sleep_color': '', 'card_bg': CHIARO, 'card_border': '',
        'align': 'left',
    })
    out.append(t.section([
        t.row([t.col([fusi], wm='3-5', ws='1-1'),
               t.col([image(t, 'pexels-5872188.jpg', 'Una scatola bianca chiusa con un nastro di raso nero', ratio='4/5'),
                      label(t, 'La scatola laccata · 14 €', pad_top=14, pad_bottom=0)], wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], bg=solid(NEBBIA), padding='large'))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande', ['Spedizioni,', 'resi, regali.'], size=48, layout='stack', size_mobile=34),
                      contacts(t)], wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quanto ci mette?', 'Tre giorni lavorativi in Italia e in Europa, cinque negli Stati Uniti e in Giappone. Partiamo '
                                        'ogni giorno alle 12, col vaporetto delle Poste.'),
                   ('Si può rendere un profumo?', 'Entro trenta giorni, anche aperto: ci basta che tu ci dica perché non era la tua ora.'),
                   ('Come funziona lo sconto dei campioni?', 'Nella scatola trovi un biglietto con un codice: i 38 € si scalano dal '
                                                            'primo flacone, entro sei mesi.'),
                   ('Fate pacchi regalo?', 'Sempre, gratis: carta blu notte e un biglietto scritto a mano col messaggio che ci mandi.'),
               ], 'domande')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large', html_id='domande'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  404
# ══════════════════════════════════════════════════════════════════════════════

def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404 · ore 00:00', align='center', color=SOFT_CHIARO),
                     headline(t, 'Questa pagina <em>ha passato la sua ora.</em>', size=88, tag='h1', align='center',
                              color=CHIARO, pad_bottom=20),
                     text(t, '<p>Le altre sei sono ancora qui.</p>', size=16, align='center', color=SOFT_CHIARO),
                     button(t, 'Torna alla bottega', HOME, kind='chiaro', align='center')]),
    ], bg=video_bg(V_INCHIOSTRO, overlay=78), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220})]


# ══════════════════════════════════════════════════════════════════════════════
#  THEME.JSON
# ══════════════════════════════════════════════════════════════════════════════

THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Negozi online', 'zone': 'A che ora ti senti?',
    'tags': ['profumeria', 'profumi', 'venezia', 'artigianato', 'minimale'],
    'description': 'Profumeria d’autore a Venezia con sei profumi per sei ore, nel colore dell’ora blu: foschia lilla e '
                   'inchiostro indaco, l’apertura a sipario, le ore che mostrano la loro Venezia sotto il cursore, il cielo che si '
                   'muove dietro la frase, «a che ora ti senti?», le carte da girare e la scatola dei campioni col profilo che si '
                   'riempie.',
    'menu': {'name': 'Ora Blu — menu', 'items': [
        {'title': 'La collezione', 'url': COLLEZIONE}, {'title': 'L’atelier', 'url': ATELIER},
        {'title': 'Le ore', 'url': HOME + '#ore'}, {'title': 'I campioni', 'url': CAMPIONI}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Ora Blu — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Ora Blu — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Ora Blu — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Ora Blu — Home'},
        'collezione': {'file': 'collezione.json', 'type': 'page', 'title': 'Ora Blu — La collezione'},
        'atelier': {'file': 'atelier.json', 'type': 'page', 'title': 'Ora Blu — L’atelier'},
        'campioni': {'file': 'campioni.json', 'type': 'page', 'title': 'Ora Blu — I campioni'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'collezione': {'title': 'La collezione', 'template': 'collezione', 'slug': 'collezione'},
        'atelier': {'title': 'L’atelier', 'template': 'atelier', 'slug': 'atelier'},
        'campioni': {'title': 'I campioni', 'template': 'campioni', 'slug': 'campioni'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 26, 'ring_color': HEX['primary'], 'ring_width': 1,
               'dot_size': 3, 'dot_color': HEX['primary'], 'hot_scale': 2.4, 'hot_color': HEX['primary'],
               'hot_fill': rgba(HEX['secondary'], 0.18), 'blend_mode': 'normal', 'magnetic_selector': 'a, button',
               'pull_strength': 0.2, 'follow_easing': 0.14},
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Manrope\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Tenor Sans\', \'Gill Sans\', \'Helvetica Neue\', sans-serif',
            'font_family_mono': '\'Spline Sans Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.75', 'font_weight_body': '400', 'font_weight_heading': '400',
            'heading_line_height': '1.06', 'heading_letter_spacing': '0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '6rem', 'font_size_h2': '4rem', 'font_size_h3': '2.2rem',
            'font_size_h4': '1.5rem', 'font_size_h5': '1.2rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '3rem',
            'font_size_h2_mobile': '2.3rem', 'font_size_h3_mobile': '1.7rem',
        },
        'google_fonts': ['Tenor Sans', 'Manrope', 'Spline Sans Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': [],
        'buttons': {'font_size': '11px', 'font_weight': '500', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '0px',
                    'text_transform': 'uppercase', 'letter_spacing': '2.5px', 'hover_brightness': '100'},
        'layout': {'border_radius': '0px', 'border_radius_large': '0px', 'container_max_width': '1240px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': 'transparent', 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '0px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '500', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': False, 'opacity': 0, 'scale': 160},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'collezione.json': build_collezione(), 'atelier.json': build_atelier(), 'campioni.json': build_campioni(),
              '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
