# -*- coding: utf-8 -*-
"""Genera il tema «Venatura» (id venatura): una falegnameria di mobili su misura a Predazzo, in Val di Fiemme.

Crema di truciolo, noce scuro, il verde del bosco e il miele della cera. Titoli nel Petrona (graziato caldo, con il
corsivo), testo in Work Sans, i dati in IBM Plex Mono come le matite sulle tavole. Angoli morbidi ma non tondi.

Dettagli: le aperture di pagina su foto del bosco e della bottega (la testata resta chiara su tutte), le tavole numerate
«come il loro albero», il calendario del legno in luna calante.

Moduli: dal tronco al mobile in quattro pannelli che si svelano di lato (Reveal orizzontale), le sei finiture provate su
una tavola di rovere (Pellicole con ricette personalizzate: luminosità, contrasto, saturazione, tinta), il tavolo da
comporre col prezzo vero (Configuratore) che scrive la configurazione nel modulo del preventivo, le cinque essenze con
la lente (Prova materiale), il metodo in cinque passi, i corsi del sabato, le persone, la mappa e il modulo.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'venatura'
NAME = 'Venatura'

HEX = {
    'primary': '#8C4A26', 'primary_contrast': '#FBF7F0', 'secondary': '#2E4636', 'secondary_contrast': '#F3EDE3',
    'accent': '#C98A3A', 'dark': '#1F1813', 'light': '#FBF7F0', 'muted': '#EAE2D4', 'muted_contrast': '#2A1F17',
    'text': '#2A1F17', 'text_muted': '#6B5C4E', 'background': '#F5F0E7', 'border': '#DCCFBC', 'link': '#8C4A26',
}

NOCE = 'var(--olo-color-primary)'
ON_NOCE = 'var(--olo-color-primary-contrast)'
BOSCO = 'var(--olo-color-secondary)'
MIELE = 'var(--olo-color-accent)'
TESTO = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
CREMA = 'var(--olo-color-background)'
CARTA = 'var(--olo-color-light)'
SEGATURA = 'var(--olo-color-muted)'
SCURO = 'var(--olo-color-dark)'
FILO = 'var(--olo-color-border)'
CHIARO = CARTA
CHIARO_T = mix(CARTA, 78)
MONO = 'var(--olo-font-family-mono)'
R = radius(6)

ADDRESS = 'Via dei Larici 18, 38037 Predazzo (TN)'
PHONE = '0462 000 962'
PHONE_TEL = 'tel:+390462000962'
MAIL = 'bottega@venatura.example'
LAT, LNG = '46.3117', '11.6006'
HOME = '{{PAGINA:home}}'
MISURA = '{{PAGINA:su-misura}}'
LEGNI = '{{PAGINA:legni}}'
BOTTEGA = '{{PAGINA:bottega}}'

V_PIALLA = 'pexels-video-20663034'    # i trucioli che escono dalla pialla, da vicino
V_BOSCO = 'pexels-video-34061738'     # il bosco di abeti nella nebbia
V_TAVOLA = 'pexels-video-4480591'     # la luce che scorre su una tavola
V_OLIO = 'pexels-video-3933597'       # il pennello che stende l'olio
V_SEGHERIA = 'pexels-video-7165750'   # la segheria
V_PIALLETTO = 'pexels-video-5973233'  # una pialla a mano, da vicino

PHOTOS = [
    'pexels-12278589.jpg', 'pexels-12278568.jpg', 'pexels-12278590.jpg', 'pexels-12278587.jpg', 'pexels-14773879.jpg',
    'pexels-5015412.jpg', 'pexels-15746197.jpg', 'pexels-27131971.jpg', 'pexels-31612086.jpg', 'pexels-8447895.jpg',
    'pexels-15060421.jpg', 'pexels-7484154.jpg', 'pexels-5711775.jpg', 'pexels-4491918.jpg', 'pexels-6790033.jpg',
    'pexels-7180747.jpg', 'pexels-15016526.jpg', 'pexels-5974275.jpg', 'pexels-7195739.jpg', 'pexels-6076850.jpg',
    'pexels-6284230.jpg', 'pexels-8089079.jpg', 'pexels-7492581.jpg', 'pexels-7480453.jpg', 'pexels-14335436.jpg',
    'pexels-5711882.jpg', 'pexels-10111297.jpg', 'pexels-9996436.jpg', 'pexels-11555847.jpg', 'pexels-36340078.jpg',
    'pexels-37098388.jpg', 'pexels-7109996.jpg', 'pexels-5974245.jpg', 'pexels-30907892.jpg', 'pexels-5974285.jpg',
    'pexels-5974239.jpg', 'pexels-37358115.jpg', 'pexels-30907888.jpg', 'pexels-5974403.jpg', 'pexels-11643074.jpg',
    'pexels-15209821.jpg', 'pexels-13425273.jpg', 'pexels-17905813.jpg', 'pexels-15758635.jpg', 'pexels-9501493.jpg',
    'pexels-14668290.jpg', 'pexels-15209791.jpg', 'pexels-6787924.jpg', 'pexels-7480720.jpg', 'pexels-6788401.jpg',
    'pexels-7303782.jpg', 'pexels-15253068.jpg', 'pexels-39854855.jpg', 'pexels-13630280.jpg', 'pexels-6394655.jpg',
    'pexels-5710754.jpg', 'pexels-17679557.jpg', 'pexels-27408998.jpg', 'pexels-36993598.jpg', 'pexels-37692150.jpg',
    'pexels-6380752.jpg', 'pexels-37127923.jpg', 'pexels-17346097.jpg', 'pexels-227577.jpg', 'pexels-6791510.jpg',
    'pexels-4403500.jpg',
]
VIDEOS = [V_PIALLA, V_BOSCO, V_TAVOLA, V_OLIO, V_SEGHERIA, V_PIALLETTO]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}


def img(name):
    assert name in MEDIA, name
    return lib(name)


def video_bg(name, overlay=45, color=SCURO, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=45, color=SCURO, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def sechead(t, eyebrow, lines, tagline='', size=64, align='left', layout='split', size_mobile=38, dark=False):
    c1, c2, ct = (CHIARO, MIELE, CHIARO_T) if dark else (TESTO, NOCE, TENUE)
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': MIELE if dark else NOCE,
        'eyebrow_dot_color': MIELE if dark else NOCE, 'eyebrow_separator': '', 'eyebrow_font_family': 'mono',
        'eyebrow_font_size': 12, 'eyebrow_font_weight': '500',
        'headline_lines': [{'text': l, 'color': c1 if i % 2 == 0 else c2, 'italic': i % 2 == 1} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.05,
        'headline_font_weight': '400', 'headline_align': align,
        'headline_font_size_tablet': max(36, int(size * 0.8)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': ct,
        'tagline_text_size': 17, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.2fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 44, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=TESTO, tag='h3', pad_bottom=10, align='left', weight='400', spacing=None, sizes=None,
             accent=NOCE):
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': True, 'accent_color': accent})
    o['heading'] = label_.replace('<em>', '').replace('</em>', '')
    if sizes:
        o.update({'heading_font_size_tablet': str(sizes[0]), 'heading_font_size_mobile': str(sizes[1])})
    elif size >= 40:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(28, int(size * 0.58)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=NOCE, pad_bottom=14, align='left', pad_top=0):
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 12, 'heading_weight': '500',
                                  'heading_color': color, 'heading_font': MONO, 'heading_letter_spacing': '1.2'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=17, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='noce', align='left', pad_top=14):
    if kind == 'noce':
        o = {'bg_color': NOCE, 'text_color': ON_NOCE, 'hover_bg_color': BOSCO, 'hover_text_color': CARTA}
    elif kind == 'miele':
        o = {'bg_color': MIELE, 'text_color': SCURO, 'hover_bg_color': CARTA, 'hover_text_color': SCURO}
    else:
        col = CHIARO if kind == 'filo-chiaro' else TESTO
        o = {'bg_color': 'transparent', 'text_color': col, 'hover_bg_color': col, 'hover_text_color': SCURO if kind == 'filo-chiaro' else CARTA,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(col, 55), 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 6, 'font_size': 15, 'font_weight': '500',
              'text_transform': 'none', 'letter_spacing': '0'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='4/5', position='center center', drift=False, lightbox=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': R, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -24, 'position': 0}, {'value': 24, 'position': 100}]}} if drift else None)


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs, dark=False):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'vertical', 'term_color': MIELE if dark else NOCE, 'term_font_size': '12', 'term_font_weight': '500',
        'definition_color': CHIARO if dark else TESTO, 'definition_font_size': '17',
        'separator': True, 'border_color': mix(CARTA, 22) if dark else FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t, dark=False):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': BOTTEGA + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': MIELE if dark else NOCE, 'icon_size': '18', 'text_color': CHIARO if dark else TESTO, 'text_size': '16',
        'gap': '12', 'icon_shape': 'none', 'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': TESTO, 'header_text_color_active': NOCE, 'header_font_size': 22, 'header_font_weight': '400',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 16, 'text_color': TENUE,
        'icon_color': NOCE, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, lines, body, foto, pos='center center', overlay=55):
    """Apertura delle pagine interne: su una foto scura, così la testata chiara si legge sempre."""
    return t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=92, size_mobile=46, dark=True)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=18, color=CHIARO_T)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='large', style={'padding_top': 230, 'padding_bottom': 80})


def pannello(t, foto, eyebrow, titolo, testo, pos='center center', overlay=48):
    return t.section([
        t.row([t.col([label(t, eyebrow, color=MIELE),
                      headline(t, titolo, size=80, tag='h2', pad_bottom=16, color=CHIARO, accent=MIELE),
                      text(t, '<p>' + testo + '</p>', size=18, color=CHIARO_T)], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='custom', custom='52,48', valign='end', anim=None),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='large', width='large', sticky_effect='reveal-h',
        sticky_h_align='bottom', style={'padding_top': 140, 'padding_bottom': 84})


def modulo(t, campi, oggetto, invio, grazie):
    return t.classic('form', {
        'fields': campi, 'email_subject': oggetto, 'submit_text': invio, 'submit_icon': '', 'success_message': grazie,
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': TESTO, 'label_size': '13', 'label_weight': '500',
        'label_transform': 'none', 'label_letter_spacing': '0', 'input_bg': CARTA, 'input_color': TESTO,
        'input_border_color': FILO, 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 6,
        'input_focus_border': NOCE, 'input_focus_shadow': False, 'gap': '18', 'submit_bg': NOCE, 'submit_color': ON_NOCE,
        'submit_radius': 6, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '15',
        'submit_font_weight': '500', 'submit_text_transform': 'none', 'submit_letter_spacing': '0',
        'submit_hover_bg': BOSCO, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})


def campo(tipo, etichetta, nome, larghezza='1-2', obbligatorio=False, opzioni='', segnaposto=''):
    return {'field_type': tipo, 'label': etichetta, 'name': nome, 'placeholder': segnaposto, 'required': obbligatorio,
            'width': larghezza, 'options': opzioni, 'icon': ''}


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER · FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': 'Venatura', 'logo_text_size': '26', 'logo_text_color': CARTA,
        'logo_text_font': 'heading', 'logo_text_weight': '400', 'logo_text_spacing': 0, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': SCURO, 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': CARTA, 'hover_color': MIELE, 'active_color': MIELE,
        'font_size': '15', 'font_weight': '500', 'text_transform': 'none', 'letter_spacing': '0', 'item_gap': '32',
        'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': MIELE,
        'button_mode': 'last', 'btn_bg': MIELE, 'btn_color': SCURO, 'btn_hover_color': SCURO,
        'btn_radius': R, 'btn_padding': {'top': 12, 'right': 20, 'bottom': 12, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '600', 'btn_transform': 'none', 'btn_letter_spacing': '0',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '#', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': SCURO, 'mobile_text_color': CARTA, 'mobile_accent_color': MIELE,
        'hamburger_style': 'minimal', 'hamburger_color': CARTA, 'mobile_font_size': '38', 'mobile_link_size': '38',
        'mobile_link_font': 'heading', 'mobile_link_weight': '400',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(CARTA, 14)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'Venatura', size=200, tag='p', pad_bottom=0, weight='300', color=CARTA, spacing=-4, sizes=(130, 68))
    claim = text(t, '<p>Falegnameria a Predazzo dal 1962: cucine, tavoli, scale e armadi su misura in legno massello dei boschi '
                    'della Val di Fiemme.</p>', size=16, color=CHIARO_T, pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': CARTA, 'link_hover_color': MIELE, 'active_color': MIELE,
                                 'font_size': '16', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('Su misura', MISURA), ('Il tuo tavolo', MISURA + '#tavolo'), ('I legni', LEGNI), ('La bottega', BOTTEGA),
                  ('I corsi del sabato', BOTTEGA + '#corsi'), ('Privacy', '/privacy-policy/')])
    orari = text(t, '<p>Lunedì — venerdì, 8–12 e 14–18<br>Sabato mattina su appuntamento</p><p>Sopralluoghi in Trentino e in '
                    'Alto Adige, posa anche fuori regione.</p>', size=16, color=CARTA)
    legal = text(t, '<p>© 2026 Venatura di Deflorian & figli s.n.c. · Predazzo (TN) · Legno certificato PEFC</p>', size=13,
                 color=CHIARO_T)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'facebook', 'url': 'https://facebook.com'}],
                                  'size': '32', 'alignment': 'right', 'icon_color': CHIARO_T, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': SCURO, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim, contacts(t, dark=True)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine', color=MIELE), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'La bottega', color=MIELE), orari], wm='1-4', ws='1-2'),
    ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
    bottom = t.row([t.col([legal, totop], wm='2-3', ws='1-1'), t.col([social], wm='1-3', ws='1-1')],
                   layout='custom', custom='66.66,33.33', valign='center', anim=None,
                   style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': mix(CARTA, 16)},
                          'padding_top': 22, 'margin_top': 24})
    return [t.section([top, t.one([brand], anim='fade', style={'margin_top': 56}), bottom], bg=solid(SCURO), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

FINITURE = [
    ('Cera neutra', 'Il legno com’è, appena più vivo', '#D8C3A0',
     {'luminosita': 103, 'contrasto': 98, 'saturazione': 92, 'seppia': 0, 'tonalita': 0, 'tinta': '', 'tinta_opacita': 0, 'vignetta': 0}),
    ('Olio di lino', 'Caldo e ambrato, la venatura in rilievo', '#C27A2C',
     {'luminosita': 93, 'contrasto': 110, 'saturazione': 138, 'seppia': 16, 'tonalita': -4, 'tinta': '#C77A2E', 'tinta_opacita': 26, 'vignetta': 8}),
    ('Cera d’api', 'Miele chiaro, al tatto di seta', '#DDAA45',
     {'luminosita': 101, 'contrasto': 100, 'saturazione': 118, 'seppia': 22, 'tonalita': 0, 'tinta': '#E2B04A', 'tinta_opacita': 20, 'vignetta': 0}),
    ('Mordente noce', 'Il rovere che sembra noce antico', '#5A321A',
     {'luminosita': 68, 'contrasto': 114, 'saturazione': 112, 'seppia': 38, 'tonalita': -6, 'tinta': '#5A321A', 'tinta_opacita': 46, 'vignetta': 14}),
    ('Fumé', 'Affumicato all’ammoniaca, grigio bruno', '#3B2E25',
     {'luminosita': 64, 'contrasto': 118, 'saturazione': 62, 'seppia': 28, 'tonalita': 0, 'tinta': '#3B2A20', 'tinta_opacita': 40, 'vignetta': 18}),
    ('Sbiancato', 'Scandinavo, la venatura quasi bianca', '#EDE6D9',
     {'luminosita': 124, 'contrasto': 86, 'saturazione': 42, 'seppia': 0, 'tonalita': 0, 'tinta': '#F2EDE4', 'tinta_opacita': 36, 'vignetta': 0}),
]


def finiture(t):
    voci = []
    for nome, nota, colore, ricetta in FINITURE:
        v = {'nome': nome, 'nota': nota, 'look': 'personalizzata', 'colore': colore}
        v.update(ricetta)
        voci.append(v)
    return t.classic('pellicole', {
        'immagine': img('pexels-10111297.jpg'), 'alt': 'Una tavola di rovere appena piallata, la venatura dritta e chiara',
        'pellicole': voci, 'partenza': 2, 'confronto': True, 'divisore': 50, 'etichetta_grezzo': 'Rovere grezzo',
        'mostra_nota': True, 'mostra_etichette': True, 'data_digicam': '', 'grana': 0,
        'colore_accento': NOCE, 'colore_divisore': CARTA, 'sfondo_rullino': CARTA,
        'nome_font_family': 'heading', 'nome_font_size': 19, 'nome_font_weight': '400', 'nome_color': TESTO,
        'nota_font_size': 13, 'nota_color': TENUE, 'proporzione': '16/9',
        'raggio': {'tl': 8, 'tr': 8, 'br': 8, 'bl': 8}, 'raggio_rullino': {'tl': 6, 'tr': 6, 'br': 6, 'bl': 6},
        'posizione_rullini': 'sotto', 'gap': 18,
    })


def build_home():
    t = Tpl(ID, 'home')
    out = []
    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Falegnameria · Predazzo, Val di Fiemme · dal 1962', 'eyebrow_dot': False,
        'title': 'Il legno<br>ha una <em>memoria.</em>',
        'subtitle': 'Mobili su misura in legno massello dei nostri boschi. Li disegniamo con te e li facciamo a mano, '
                    'un incastro alla volta.',
        'media_bg': {'type': 'video', 'video_url': img(V_PIALLA + '.mp4'), 'video_poster': img(V_PIALLA + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': SCURO, 'overlay_top': 0.35, 'overlay_bottom': 0.78, 'overlay_sides': False, 'overlay_direction': 'left',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': MIELE, 'text_color': CARTA, 'title_color': CARTA, 'subtitle_color': CHIARO_T,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 118, 'title_font_weight': '300',
        'title_line_height': '0.98', 'title_letter_spacing': '-2', 'title_text_transform': 'none',
        'subtitle_font_size': 19, 'subtitle_max_width': 520,
        'min_height': '100vh', 'content_max_width': 1320, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 150, 'right': 40, 'bottom': 90, 'left': 40},
        'cta_text': 'Componi il tuo tavolo', 'cta_url': MISURA + '#tavolo', 'cta_bg_color': MIELE, 'cta_text_color': SCURO,
        'cta_radius': R, 'cta_size': '16', 'cta_style': 'filled', 'cta_font_weight': '600', 'cta_text_transform': 'none',
        'cta_letter_spacing': '0',
        'cta2_text': 'La bottega', 'cta2_url': BOTTEGA, 'cta2_text_color': CARTA, 'cta2_style': 'ghost',
        'meta_text': '46°18′ N · 11°36′ E · 1018 m', 'scroll_hint': '', 'scene_depth': 'soft', 'scene_depth_fade': True, 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    numeri = t.classic('statstrip', {
        'items': [{'value': '1962', 'label': 'in bottega dal'}, {'value': '3', 'label': 'generazioni di Deflorian'},
                  {'value': '100%', 'label': 'legno certificato PEFC'}, {'value': '2', 'label': 'anni di stagionatura all’aria'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 28, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': NOCE, 'value_size': 54, 'value_weight': '400',
        'label_color': TENUE, 'label_size': 12, 'label_uppercase': True, 'label_weight': '500', 'align': 'left',
        'mono_font_family': 'mono',
    })
    out.append(t.section([
        t.row([t.col([label(t, 'Venatura, s.f.')], wm='1-4', ws='1-1'),
               t.col([headline(t, 'Il disegno che gli anelli lasciano nel legno. In ogni tavola ci sono <em>sessant’anni di '
                                  'bosco</em>: noi gli diamo il tempo che serve.', size=46, tag='h2', pad_bottom=0)], wm='3-4', ws='1-1')],
              layout='custom', custom='25,75', gap=32, valign='start'),
        t.one([numeri], style={'margin_top': 72}),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 120}))

    for foto, ey, ti, tx, pos in [
        ('pexels-14773879.jpg', '01 / 04 — Il bosco', 'Lo scegliamo <em>in piedi.</em>',
         'Con la guardia forestale, fra Paneveggio e Bellamonte: solo alberi maturi, tagliati d’inverno in luna calante, '
         'quando la linfa è ferma.', 'center 60%'),
        ('pexels-37127923.jpg', '02 / 04 — La segheria', 'Tagliato <em>a quarti.</em>',
         'Il tronco si apre in tavole alla segheria di Ziano, a sei chilometri. Ogni tavola porta scritto il numero del suo albero.',
         'center center'),
        ('pexels-12278587.jpg', '03 / 04 — La stagionatura', 'Due anni <em>all’aria.</em>',
         'Le tavole riposano sotto la tettoia, un anno per ogni centimetro di spessore. Niente forno: il legno resta vivo e poi '
         'non si muove più.', 'center center'),
        ('pexels-5974239.jpg', '04 / 04 — La bottega', 'Poi, <em>la pialla.</em>',
         'Incastri a coda di rondine, colla a caldo, olio di lino. Le macchine tagliano, le mani finiscono.', 'center 40%'),
    ]:
        out.append(pannello(t, foto, ey, ti, tx, pos=pos))

    def lavoro(foto, nome, testo, url):
        return {'counter': '', 'counter_label': '', 'title': nome, 'title_accent': '', 'title_accent_italic': False,
                'description': testo, 'icon': '', 'footer_dot_color': '', 'footer_text': '', 'link_url': url,
                'link_text': 'Scopri', 'media_image': img(foto), 'media_label': ''}
    schede = t.classic('info-cards', {
        'items': [lavoro('pexels-7195739.jpg', 'Cucine', 'In larice o rovere, con le ante a telaio e i piani in massello.',
                         MISURA + '#lavori'),
                  lavoro('pexels-36993598.jpg', 'Tavoli', 'Da quattro a quattordici posti, con le allunghe che non si vedono.',
                         MISURA + '#tavolo'),
                  lavoro('pexels-17905813.jpg', 'Scale', 'A giorno o a chiocciola, coi gradini da un’unica tavola.',
                         MISURA + '#lavori'),
                  lavoro('pexels-11643074.jpg', 'Madie e armadi', 'Cassetti a coda di rondine che scorrono da soli.',
                         MISURA + '#lavori')],
        'container_bg': {'type': 'none'}, 'container_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0},
        'container_radius_hover': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0}, 'container_padding': 0, 'container_gap': 0,
        'columns': 4, 'columns_tablet': 2, 'columns_mobile_landscape': 2, 'columns_mobile': 2, 'items_gap': 24,
        'card_bg': {'type': 'none'}, 'card_color': TENUE, 'card_accent_color': NOCE, 'card_padding': 0, 'card_border': '',
        'card_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0}, 'card_radius_hover': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0},
        'show_icon': False, 'show_counter': False, 'show_counter_label': False, 'show_arrow': True, 'show_footer': False,
        'show_link_text': True, 'show_divider': False, 'show_media': True, 'media_aspect_ratio': '4/5', 'object_fit': 'cover',
        'object_position': 'center center', 'media_radius': {'tl': 6, 'tr': 6, 'br': 6, 'bl': 6, 'linked': True},
        'media_radius_hover': {'tl': 6, 'tr': 6, 'br': 6, 'bl': 6, 'linked': True}, 'media_position': 'top',
        'title_font_family': 'heading', 'title_size': 30, 'title_weight': '400', 'title_italic': False, 'title_color': TESTO,
        'description_size': 15, 'card_hover_effect': 'none',
    })
    out.append(t.section([
        t.one([sechead(t, 'Cosa facciamo', ['Su misura,', 'mai a catalogo.'],
                       tagline='Una cucina, un tavolo, una scala, un armadio a muro: li disegniamo sul posto e li montiamo noi. '
                               'Niente catalogo, niente pannelli.')]),
        t.one([schede]),
    ], padding='large', style={'padding_top': 150}))

    out.append(t.section([
        t.one([sechead(t, 'Le finiture', ['Stesso rovere,', 'sei finiture.'],
                       tagline='Trascina il divisore: a sinistra la tavola appena piallata, a destra la finitura. Sono le nostre '
                               'sei ricette, solo oli e cere naturali. Il campione vero te lo mandiamo a casa.')]),
        t.one([finiture(t)]),
    ], bg=solid(SEGATURA), padding='large', html_id='finiture', style={'padding_top': 140, 'padding_bottom': 130}))

    out.append(t.section([
        centered(t, [label(t, 'Matteo Deflorian · terza generazione', color=MIELE, align='center'),
                     headline(t, '«Un tavolo così lo erediteranno i tuoi nipoti. <em>Noi ci firmiamo sotto.</em>»', size=60,
                              tag='p', align='center', pad_bottom=0, color=CARTA, accent=MIELE, weight='300')]),
    ], bg=video_bg(V_OLIO, overlay=55), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 220}))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-7180747.jpg', 'In bottega, un allievo guarda un uccello di legno appena finito',
                            ratio='5/4', drift=True)], wm='1-2', ws='1-1'),
               t.col([label(t, 'I corsi del sabato'),
                      headline(t, 'Il primo giorno un tagliere, <em>alla fine uno sgabello.</em>', size=52, tag='h2', pad_bottom=16),
                      text(t, '<p>Corsi di falegnameria per chi non ha mai preso in mano una pialla: sei persone al banco, i nostri '
                              'attrezzi, il legno della valle. Quello che fai lo porti a casa.</p>'),
                      button(t, 'I corsi', BOTTEGA + '#corsi')], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 140}))

    out.append(t.section([
        centered(t, [headline(t, 'Hai uno spazio <em>da riempire?</em>', size=88, tag='h2', align='center', pad_bottom=16,
                              color=CARTA, accent=MIELE),
                     text(t, '<p>Mandaci una foto e due misure. Veniamo a vedere, disegniamo, e ti diamo un prezzo che non cambia.</p>',
                          size=18, color=CHIARO_T, align='center'),
                     button(t, 'Chiedi un sopralluogo', MISURA + '#preventivo', kind='miele', align='center')]),
    ], bg=video_bg(V_BOSCO, overlay=50), padding='xlarge', style={'padding_top': 200, 'padding_bottom': 200}))
    return page(out, page_intro_enabled=True, page_intro_style='linea', page_intro_text='Venatura',
                page_intro_caption='Predazzo · Val di Fiemme · 1962', page_intro_bg=HEX['dark'], page_intro_color=HEX['accent'],
                page_intro_duration=1800, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  SU MISURA
# ══════════════════════════════════════════════════════════════════════════════

def opz(gruppo, id_, nome, prezzo=0, descrizione='', predefinita=False, badge=''):
    return {'id': id_, 'gruppo': gruppo, 'nome': nome, 'descrizione': descrizione, 'prezzo': prezzo, 'immagine': '',
            'badge': badge, 'predefinita': predefinita, 'punti': '', 'min': 0, 'max': 10}


def configuratore(t):
    return t.classic('configuratore', {
        'gruppi': [
            {'id': 'legno', 'titolo': 'Il legno', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': 'Tutti massello, stagionati due anni.'},
            {'id': 'misura', 'titolo': 'La misura', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
            {'id': 'gambe', 'titolo': 'Le gambe', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': ''},
            {'id': 'finitura', 'titolo': 'La finitura', 'tipo': 'uno', 'obbligatorio': True, 'aiuto': 'Le trovi provate sulla tavola in home.'},
            {'id': 'extra', 'titolo': 'In più', 'tipo': 'piu', 'obbligatorio': False, 'aiuto': ''},
        ],
        'opzioni': [
            opz('legno', 'l-abete', 'Abete rosso', 0, 'Chiaro e leggero, profuma di resina', True),
            opz('legno', 'l-larice', 'Larice', 180, 'Rosato, venatura fitta, non teme l’acqua'),
            opz('legno', 'l-cirmolo', 'Cirmolo', 320, 'Coi nodi, profuma per anni', badge='Della valle'),
            opz('legno', 'l-rovere', 'Rovere', 420, 'Duro, dorato, per sempre'),
            opz('legno', 'l-noce', 'Noce nazionale', 780, 'Scuro e vellutato, dal Veneto'),
            opz('misura', 'm-160', '160 × 90 cm', 0, '6 posti', True),
            opz('misura', 'm-200', '200 × 100 cm', 350, '8 posti'),
            opz('misura', 'm-240', '240 × 100 cm', 700, '10 posti'),
            opz('misura', 'm-280', '280 × 110 cm', 1100, '12 posti, piano in due tavole'),
            opz('gambe', 'g-cav', 'Cavalletto in legno', 0, 'Coi tiranti a vista', True),
            opz('gambe', 'g-trap', 'Gambe a trapezio', 120, 'Leggere, alla scandinava'),
            opz('gambe', 'g-ferro', 'Ferro nero', 240, 'Battuto dal fabbro di Tesero'),
            opz('gambe', 'g-centro', 'Gamba centrale', 380, 'Nessuna gamba fra i piedi'),
            opz('finitura', 'f-cera', 'Cera neutra', 0, '', True),
            opz('finitura', 'f-lino', 'Olio di lino', 0),
            opz('finitura', 'f-api', 'Cera d’api', 40),
            opz('finitura', 'f-noce', 'Mordente noce', 120),
            opz('finitura', 'f-fume', 'Fumé', 160),
            opz('finitura', 'f-bianco', 'Sbiancato', 90),
            opz('extra', 'x-bordo', 'Bordo naturale', 260, 'I lati del tronco, con la corteccia levigata'),
            opz('extra', 'x-allunghe', 'Due allunghe da 50 cm', 480, 'Nascoste sotto il piano'),
            opz('extra', 'x-panca', 'Panca coordinata', 690, 'Lunga quanto il tavolo'),
            opz('extra', 'x-nome', 'Il vostro nome inciso', 40, 'Sotto il piano, a fuoco'),
        ],
        'prezzo_base': 1450, 'valuta': '€', 'testo_totale': 'Il tuo tavolo',
        'nota_prezzo': 'IVA compresa · consegna e montaggio in Trentino inclusi · pronto in sei settimane',
        'periodo': '', 'mostra_differenze': True, 'indicatori': [], 'riepilogo': True, 'titolo_riepilogo': 'Il tuo tavolo',
        'testo_azzera': 'Ricomincia', 'testo_pulsante': 'Chiedi il preventivo', 'link_azione': '', 'campo_modulo': 'messaggio',
        'vista_opzioni': 'schede', 'disposizione': 'lato', 'sfondo_scheda': CARTA, 'colore_scelta': NOCE,
        'colore_testo': TESTO, 'colore_tenue': TENUE, 'colore_prezzo': NOCE, 'sfondo_riepilogo': SCURO,
        'sfondo_badge': BOSCO, 'colore_badge': CARTA, 'raggio_scheda': {'tl': 8, 'tr': 8, 'br': 8, 'bl': 8}, 'ombra': 'none',
        'gruppo_font_family': 'heading', 'gruppo_font_size': 28, 'gruppo_font_weight': '400', 'gruppo_color': TESTO,
        'opzione_font_family': 'body', 'opzione_font_size': 16, 'opzione_font_weight': '600',
        'descrizione_font_family': 'body', 'descrizione_font_size': 14, 'descrizione_font_weight': '400',
        'prezzo_font_family': 'mono', 'prezzo_font_size': 15, 'prezzo_font_weight': '500',
        'totale_font_family': 'heading', 'totale_font_size': 64, 'totale_font_weight': '300', 'totale_color': MIELE, 'gap': 12,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def build_su_misura():
    t = Tpl(ID, 'su-misura')
    out = [intro(t, 'Su misura', ['Lo disegniamo', 'insieme.'],
                 '<p>Veniamo a vedere lo spazio, prendiamo le misure, ti portiamo il disegno e i campioni. Poi il legno lo '
                 'scegli tu, tavola per tavola, in bottega.</p>', 'pexels-4491918.jpg')]

    out.append(t.section([
        t.one([sechead(t, 'Il tavolo', ['Componi il tuo tavolo,', 'il prezzo è subito vero.'],
                       tagline='Scegli legno, misura, gambe e finitura: il totale si aggiorna, e il riepilogo finisce da solo nel '
                               'modulo qui sotto. È il prezzo che paghi, montaggio compreso.')]),
        t.one([configuratore(t)]),
    ], padding='large', html_id='tavolo', style={'padding_top': 120}))

    campi = [campo('text', 'Nome', 'nome', obbligatorio=True), campo('email', 'Email', 'email', obbligatorio=True),
             campo('tel', 'Telefono', 'telefono'), campo('text', 'Comune', 'comune', segnaposto='Dove va il mobile'),
             campo('select', 'Cosa ti serve', 'cosa', obbligatorio=True,
                   opzioni='Un tavolo\nUna cucina\nUna scala\nUn armadio o una madia\nUn’altra cosa'),
             campo('date', 'Un giorno per il sopralluogo', 'sopralluogo'),
             campo('textarea', 'Raccontaci', 'messaggio', larghezza='1-1', obbligatorio=True,
                   segnaposto='Misure, legno, un’idea: anche due righe. Se hai composto il tavolo qui sopra, c’è già.')]
    out.append(t.section([
        t.row([t.col([sechead(t, 'Il preventivo', ['Una foto,', 'due misure.'], size=64, layout='stack', size_mobile=40),
                      text(t, '<p>Rispondiamo entro due giorni con un prezzo scritto. Il sopralluogo in Val di Fiemme, Fassa e Cembra '
                              'è gratuito.</p>'),
                      contacts(t)], wm='2-5', ws='1-1'),
               t.col([modulo(t, campi, 'Venatura — un preventivo', 'Manda la richiesta',
                             'Ricevuta. Ti scriviamo entro due giorni, con un prezzo e una data per vederci.')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='start'),
    ], bg=solid(SEGATURA), padding='large', html_id='preventivo', style={'padding_top': 130, 'padding_bottom': 130}))

    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'Il sopralluogo', 'description': 'Misure, luce, uso: un’ora da te. Gratis in valle.'},
                  {'number': '02', 'title': 'Il disegno', 'description': 'In una settimana, a mano e in 3D, coi campioni di legno.'},
                  {'number': '03', 'title': 'Il legno', 'description': 'Vieni a sceglierti le tavole: hanno il numero del loro albero.'},
                  {'number': '04', 'title': 'La bottega', 'description': 'Da quattro a otto settimane. Ti mandiamo le foto dal banco.'},
                  {'number': '05', 'title': 'La posa', 'description': 'Montiamo noi, e torniamo dopo un anno a ripassare l’olio.'}],
        'columns': 5, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': NOCE, 'number_bg': '',
        'number_size': 14, 'number_font': MONO, 'number_weight': '500', 'title_color': TESTO, 'title_size': 24,
        'title_weight': '400', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 15, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
        'card_radius': radius(0), 'card_padding': 22, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([
        t.one([sechead(t, 'Come lavoriamo', ['Cinque passi,', 'una sola persona.'],
                       tagline='Dal sopralluogo alla posa ti segue uno di noi tre: lo stesso che disegna è quello che monta.')]),
        t.one([passi]),
    ], padding='large'))

    galleria = t.classic('progallery', {
        'images': [{'url': img(f), 'alt': a, 'caption': c, 'subtitle': '', 'text': ''} for f, a, c in [
            ('pexels-6076850.jpg', 'Una cucina in rovere con l’isola e gli sgabelli', 'Cucina in rovere · Cavalese'),
            ('pexels-15758635.jpg', 'Una scala a giorno in larice vista dall’alto', 'Scala in larice · Tesero'),
            ('pexels-15253068.jpg', 'Un tavolo con le sedie in una stube', 'Tavolo e sedie · Moena'),
            ('pexels-6284230.jpg', 'Una cucina in noce con la parete a listelli', 'Cucina in noce · Trento'),
            ('pexels-13630280.jpg', 'Una sedia in legno accanto alla finestra', 'Sedia in frassino'),
            ('pexels-9501493.jpg', 'Una scala a chiocciola in legno chiaro', 'Scala a chiocciola · Ziano'),
            ('pexels-8089079.jpg', 'Una cucina con i pensili in larice', 'Cucina in larice · Predazzo'),
            ('pexels-39854855.jpg', 'Uno sgabello rotondo in legno chiaro', 'Sgabello in cirmolo'),
            ('pexels-14668290.jpg', 'Una scala in legno rosso vista dal basso', 'Scala in ciliegio · Bolzano'),
        ]],
        'layout': 'masonry', 'layout_family': 'classic', 'columns': 3, 'gap': 12, 'img_height': '',
        'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 6, 'mobile_columns': 2,
        'hover_effect': 'zoom', 'hover_caption': 'slide', 'lightbox': True, 'show_caption': True, 'entrance': 'none', 'filter': 'none',
    })
    out.append(t.section([
        t.one([sechead(t, 'I lavori', ['Da Moena a Trento,', 'le ultime case.'], size=56, layout='stack', size_mobile=36)]),
        t.one([galleria]),
    ], padding='large', html_id='lavori', style={'padding_top': 40}))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande', ['Quelle che', 'ci fanno tutti.'], size=48, layout='stack', size_mobile=34)],
                     wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quanto costa una cucina su misura?', 'Da 9.000 a 22.000 € per una cucina di tre metri e mezzo, elettrodomestici '
                                                         'esclusi. Il prezzo scritto dopo il sopralluogo non cambia.'),
                   ('Il legno massello non si muove?', 'Si muove se non è stagionato. Le nostre tavole riposano due anni all’aria e '
                                                     'gli incastri lasciano al legno lo spazio per respirare.'),
                   ('Lavorate fuori dal Trentino?', 'Sì: in Alto Adige e in Veneto con il nostro furgone, altrove spediamo il mobile '
                                                   'smontato e veniamo a montarlo.'),
                   ('Come si cura un mobile a olio?', 'Un panno umido tutti i giorni, una mano d’olio una volta l’anno. Il primo '
                                                     'anno la ripassiamo noi.'),
               ], 'mis')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(SEGATURA), padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  I LEGNI
# ══════════════════════════════════════════════════════════════════════════════

def build_legni():
    t = Tpl(ID, 'legni')
    out = [intro(t, 'I legni', ['Cinque essenze,', 'tre della valle.'],
                 '<p>Abete, cirmolo e larice vengono dai boschi qui sopra; il rovere e il noce da segherie che conosciamo da '
                 'trent’anni. Passa sulla foto: la lente mostra la fibra.</p>', 'pexels-31612086.jpg', overlay=40)]
    essenze = t.classic('provamateriale', {
        'voci': [
            {'id': 'vn-1', 'nome': 'Abete rosso', 'codice': 'Picea abies · Paneveggio',
             'testo': 'Chiaro, leggero, profuma di resina. Dallo stesso bosco escono le tavole di risonanza per i violini: quelle '
                      'che non suonano abbastanza diventano i nostri tavoli.',
             'immagine': img('pexels-10111297.jpg'), 'miniatura': '', 'colore': '', 'alt': 'Abete rosso: tavola chiara a venatura dritta'},
            {'id': 'vn-2', 'nome': 'Cirmolo', 'codice': 'Pinus cembra · sopra i 1.800 m',
             'testo': 'Il pino cembro dei crinali, coi nodi rossi e un profumo che dura vent’anni. Per le stanze da letto e le stube.',
             'immagine': img('pexels-9996436.jpg'), 'miniatura': '', 'colore': '', 'alt': 'Cirmolo: legno chiaro con un nodo scuro'},
            {'id': 'vn-3', 'nome': 'Larice di recupero', 'codice': 'Larix decidua · vecchi fienili',
             'testo': 'Le tavole dei fienili smontati in valle, cotte da cento inverni. Grigie fuori, rosate dentro.',
             'immagine': img('pexels-37098388.jpg'), 'miniatura': '', 'colore': '', 'alt': 'Larice vecchio: legno scuro segnato dal tempo con un nodo'},
            {'id': 'vn-4', 'nome': 'Rovere spazzolato', 'codice': 'Quercus robur · Friuli',
             'testo': 'La spazzola toglie il legno tenero e lascia in rilievo gli anelli: si sente la venatura sotto le dita.',
             'immagine': img('pexels-36340078.jpg'), 'miniatura': '', 'colore': '', 'alt': 'Rovere spazzolato: gli anelli in rilievo, a onde'},
            {'id': 'vn-5', 'nome': 'Noce nazionale', 'codice': 'Juglans regia · Veneto',
             'testo': 'Scuro, vellutato, con le vene viola. Raro: ne lavoriamo pochi metri cubi all’anno, per i pezzi da tramandare.',
             'immagine': img('pexels-11555847.jpg'), 'miniatura': '', 'colore': '', 'alt': 'Noce: legno bruno scuro a venatura fitta'},
        ],
        'iniziale': 0, 'lente': True, 'lente_zoom': 2.6, 'lente_misura': 200, 'lente_forma': 'cerchio',
        'mostra_suggerimento': True, 'testo_suggerimento': 'Passa sopra la foto per vedere la fibra',
        'testo_suggerimento_touch': 'Tocca la foto per accendere la lente', 'mostra_testi': True, 'etichetta_campioni': 'Essenza',
        'accento': NOCE, 'colore_testo': TESTO, 'colore_tenue': TENUE, 'sfondo_pannello': '', 'ombra': 'none',
        'etichetta_font_family': 'mono', 'etichetta_font_size': 12, 'etichetta_font_weight': '500', 'etichetta_color': TENUE,
        'nome_font_family': 'heading', 'nome_font_size': 48, 'nome_font_size_tablet': 40, 'nome_font_size_mobile': 34,
        'nome_font_weight': '400', 'nome_color': TESTO, 'codice_font_family': 'mono', 'codice_font_size': 12,
        'codice_font_weight': '500', 'codice_color': NOCE, 'testo_font_family': 'body', 'testo_font_size': 17,
        'testo_font_weight': '400', 'testo_color': TENUE, 'raggio_immagine': {'tl': 8, 'tr': 8, 'br': 8, 'bl': 8},
        'proporzione': '3/2', 'campione_forma': 'quadretto', 'campione_misura': 58, 'disposizione': 'lato', 'lato': 'sinistra',
        'larghezza_immagine': 60, 'gap': 56, 'allineamento': 'left',
    })
    out.append(t.section([t.one([essenze])], padding='large', style={'padding_top': 120}))

    out.append(t.section([
        t.row([t.col([label(t, 'Il bosco che suona · Paneveggio', color=MIELE),
                      headline(t, 'Si taglia d’inverno, <em>in luna calante.</em>', size=68, tag='h2', pad_bottom=16, color=CARTA,
                               accent=MIELE),
                      text(t, '<p>È la regola dei boscaioli della valle: con la linfa ferma il legno si asciuga senza fendersi e '
                              'non lo attaccano i tarli. Noi la seguiamo ancora, e scriviamo la data del taglio su ogni tavola.</p>',
                           size=18, color=CHIARO_T)], wm='1-2', ws='1-1'),
               t.col([specs(t, [('Novembre — gennaio', 'il taglio, in luna calante'), ('Primavera', 'la segheria di Ziano, a quarti'),
                                ('Due estati', 'la stagionatura sotto la tettoia'), ('Il terzo anno', 'la bottega')], dark=True)],
                     wm='1-2', ws='1-1')], layout='50-50', gap=72, valign='end'),
    ], bg=video_bg(V_BOSCO, overlay=58), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 120}))

    out.append(t.section([
        t.row([t.col([image(t, 'pexels-6380752.jpg', 'Tronchi d’abete accatastati sotto la neve', ratio='4/5', drift=True)],
                     wm='1-2', ws='1-1'),
               t.col([label(t, 'Il legno giusto'),
                      headline(t, 'Ogni tavola ha <em>il numero del suo albero.</em>', size=52, tag='h2', pad_bottom=16),
                      text(t, '<p>Dal bosco al tuo tavolo, la stessa tavola porta lo stesso numero: sai da quale versante viene, '
                              'quando è stata tagliata e da quanto riposa.</p>'),
                      specs(t, [('Certificazione', 'PEFC, gestione sostenibile dei boschi'),
                                ('Colle', 'a caldo, senza formaldeide'), ('Finiture', 'oli e cere naturali, senza solventi'),
                                ('Scarti', 'trucioli alla stalla dei vicini, ritagli in stufa')])], wm='1-2', ws='1-1')],
              layout='50-50', gap=72, valign='center'),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 140}))

    out.append(t.section([
        centered(t, [headline(t, 'Vieni a sceglierti <em>le tavole.</em>', size=72, tag='h2', align='center', pad_bottom=14),
                     text(t, '<p>In magazzino ci sono duecento metri cubi di legno che riposa. Prendi un appuntamento: te lo facciamo '
                             'toccare.</p>', align='center'),
                     button(t, 'Prendi un appuntamento', BOTTEGA + '#scrivici', align='center')]),
    ], bg=solid(SEGATURA), padding='large', style={'padding_top': 130, 'padding_bottom': 130}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LA BOTTEGA
# ══════════════════════════════════════════════════════════════════════════════

def build_bottega():
    t = Tpl(ID, 'bottega')
    out = [intro(t, 'La bottega', ['Tre generazioni,', 'una pialla.'],
                 '<p>Bruno ha aperto la bottega nel 1962 sotto casa, in via dei Larici. Oggi siamo in sette, e la pialla con cui '
                 'ha cominciato è ancora appesa sopra il banco.</p>', 'pexels-5711775.jpg', overlay=35)]

    def persona(foto, nome, ruolo, bio, pos):
        return [image(t, foto, nome + ', ' + ruolo.lower(), ratio='4/5', position=pos),
                headline(t, nome, size=28, tag='h3', pad_bottom=2, sizes=(26, 24)),
                label(t, ruolo, pad_bottom=8, pad_top=6),
                text(t, '<p>' + bio + '</p>', size=16)]
    out.append(t.section([
        t.row([t.col(persona('pexels-14335436.jpg', 'Bruno Deflorian', 'Il fondatore',
                             'Ottantasei anni, sessanta di bottega. Passa tutte le mattine a controllare gli incastri.', 'center 35%'),
                     wm='1-3', ws='1-1'),
               t.col(persona('pexels-7492581.jpg', 'Matteo Deflorian', 'Disegno e cucine',
                             'Il nipote: ha studiato design a Bolzano ed è tornato. Disegna a mano, poi in 3D.', 'center 25%'),
                     wm='1-3', ws='1-1'),
               t.col(persona('pexels-5711882.jpg', 'Andrea Vinante', 'Capo bottega',
                             'Scale e incastri a coda di rondine. In bottega dal 1998, insegna nei corsi del sabato.', 'center 30%'),
                     wm='1-3', ws='1-1')],
              layout='33-33-33', gap=32, valign='start'),
    ], padding='large', style={'padding_top': 120}))

    galleria = t.classic('progallery', {
        'images': [{'url': img(f), 'alt': a, 'caption': '', 'subtitle': '', 'text': ''} for f, a in [
            ('pexels-15060421.jpg', 'La parete degli attrezzi della bottega'),
            ('pexels-6787924.jpg', 'Scalpelli in fila su fondo scuro'),
            ('pexels-227577.jpg', 'Riccioli di truciolo sul banco'),
            ('pexels-7109996.jpg', 'Un incastro stretto nel morsetto'),
        ]],
        'layout': 'expand', 'layout_family': 'classic', 'columns': 4, 'gap': 8, 'img_height': '420px',
        'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 6, 'mobile_columns': 1,
        'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
        'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
    })
    out.append(t.section([t.one([galleria])], padding='large', width='large', style={'padding_top': 0}))

    corsi = t.classic('pricelist', {
        'items': [{'title': 'Primo incontro col legno', 'description': 'Un sabato · pialla, sega, scalpello · un tagliere',
                   'price': '95 €', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Incastri a mano', 'description': 'Due sabati · coda di rondine e tenone · una scatola',
                   'price': '190 €', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Intaglio', 'description': 'Tre sabati · sgorbie e coltelli · una ciotola o un cucchiaio',
                   'price': '260 €', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Lo sgabello', 'description': 'Quattro sabati · dal tronco al pezzo finito, portato a casa',
                   'price': '340 €', 'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Il tuo tavolo', 'description': 'Otto sabati · lo fai tu, ti aiutiamo noi · legno a parte',
                   'price': '980 €', 'image_url': '', 'highlighted': False, 'badge': ''}],
        'separator_style': 'solid', 'separator_color': FILO, 'title_color': TESTO, 'price_color': NOCE,
        'description_color': TENUE, 'image_size': 64, 'image_border_radius': '0', 'show_image': False,
        'price_position': 'right', 'tile_padding': {'top': 18, 'right': 0, 'bottom': 18, 'left': 0}, 'card_bg': 'transparent',
        'card_border_radius': '0',
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'I corsi del sabato', ['Sei al banco,', 'nove — diciassette.'], size=52, layout='stack',
                              size_mobile=36),
                      text(t, '<p>Da ottobre a maggio, al massimo sei persone. Attrezzi, legno e pranzo alla trattoria qui accanto '
                              'compresi. Si parte da zero.</p>'),
                      image(t, 'pexels-7484154.jpg', 'Due allievi al banco della bottega', ratio='3/2')],
                     wm='1-3', ws='1-1'),
               t.col([corsi, button(t, 'Iscriviti a un corso', '#scrivici', pad_top=24)], wm='2-3', ws='1-1')],
              layout='custom', custom='38,62', gap=64, valign='start'),
    ], bg=solid(SEGATURA), padding='large', html_id='corsi', style={'padding_top': 130, 'padding_bottom': 130}))

    campi = [campo('text', 'Nome', 'nome', obbligatorio=True), campo('email', 'Email', 'email', obbligatorio=True),
             campo('select', 'Per cosa', 'per', obbligatorio=True,
                   opzioni='Un mobile su misura\nScegliere il legno in magazzino\nPrimo incontro col legno\nIncastri a mano\n'
                           'Intaglio\nLo sgabello\nIl tuo tavolo\nAltro'),
             campo('date', 'Da quando', 'quando'),
             campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1', obbligatorio=True)]
    out.append(t.section([
        t.row([t.col([sechead(t, 'Scrivici', ['Passa', 'in bottega.'], size=64, layout='stack', size_mobile=40),
                      text(t, '<p>Per un mobile, per un corso o per vedere il legno che riposa: rispondiamo entro un giorno.</p>'),
                      contacts(t)], wm='2-5', ws='1-1'),
               t.col([modulo(t, campi, 'Venatura — dalla bottega', 'Manda',
                             'Ricevuta. Ti rispondiamo entro un giorno, di solito con una data per vederci.')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=64, valign='start'),
    ], padding='large', html_id='scrivici', style={'padding_top': 130}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '15', 'height': '440', 'marker': True,
                                 'marker_popup': 'Venatura · via dei Larici 18, Predazzo', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'default', 'border_radius': R, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Dove', ['A Predazzo,', 'sotto il Latemar.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>All’ingresso del paese venendo da Cavalese, dietro la segheria vecchia. Il parcheggio è nel '
                              'piazzale della bottega.</p>')], wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], bg=solid(SEGATURA), padding='large', html_id='dove'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404', color=MIELE, align='center'),
                     headline(t, 'Qui c’era <em>un nodo.</em>', size=110, tag='h1', align='center', pad_bottom=20, color=CARTA,
                              accent=MIELE, weight='300'),
                     text(t, '<p>La pagina che cerchi è finita nei trucioli. Torna al banco e ricominciamo.</p>', size=18,
                          align='center', color=CHIARO_T),
                     button(t, 'Torna all’inizio', HOME, kind='miele', align='center')]),
    ], bg=video_bg(V_PIALLETTO, overlay=58), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Casa e design', 'zone': 'Il legno ha una memoria',
    'tags': ['falegnameria', 'mobili su misura', 'legno', 'artigiani', 'trentino', 'cucine', 'corsi', 'configuratore'],
    'description': 'Una falegnameria di mobili su misura in Val di Fiemme: dal tronco al mobile in quattro pannelli che si svelano '
                   'di lato, le sei finiture provate su una tavola di rovere col divisore, il tavolo da comporre col prezzo vero '
                   'che finisce nel preventivo, le cinque essenze con la lente, i corsi del sabato e la bottega.',
    'menu': {'name': 'Venatura — menu', 'items': [
        {'title': 'Su misura', 'url': MISURA}, {'title': 'I legni', 'url': LEGNI}, {'title': 'La bottega', 'url': BOTTEGA},
        {'title': 'Il tuo tavolo', 'url': MISURA + '#tavolo'}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Venatura — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Venatura — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Venatura — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Venatura — Home'},
        'su-misura': {'file': 'su-misura.json', 'type': 'page', 'title': 'Venatura — Su misura'},
        'legni': {'file': 'legni.json', 'type': 'page', 'title': 'Venatura — I legni'},
        'bottega': {'file': 'bottega.json', 'type': 'page', 'title': 'Venatura — La bottega'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'su-misura': {'title': 'Su misura', 'template': 'su-misura', 'slug': 'su-misura'},
        'legni': {'title': 'I legni', 'template': 'legni', 'slug': 'legni'},
        'bottega': {'title': 'La bottega', 'template': 'bottega', 'slug': 'bottega'},
    },
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Work Sans\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Petrona\', Georgia, \'Times New Roman\', serif',
            'font_family_mono': '\'IBM Plex Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '17px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '400',
            'heading_line_height': '1.05', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '5.5rem', 'font_size_h2': '3.5rem', 'font_size_h3': '2rem',
            'font_size_h4': '1.4rem', 'font_size_h5': '1.15rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.8rem',
            'font_size_h2_mobile': '2.1rem', 'font_size_h3_mobile': '1.5rem',
        },
        'google_fonts': ['Petrona', 'Work Sans', 'IBM Plex Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': ['Petrona'],
        'buttons': {'font_size': '15px', 'font_weight': '500', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '6px',
                    'text_transform': 'none', 'letter_spacing': '0', 'hover_brightness': '100'},
        'layout': {'border_radius': '6px', 'border_radius_large': '10px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': HEX['light'], 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '6px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '13px', 'label_font_weight': '500', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 4, 'scale': 150},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'su-misura.json': build_su_misura(), 'legni.json': build_legni(), 'bottega.json': build_bottega(),
              '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
