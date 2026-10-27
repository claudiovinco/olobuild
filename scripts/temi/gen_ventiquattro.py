# -*- coding: utf-8 -*-
"""Genera il tema «Ventiquattro» (id ventiquattro): una casa di produzione video a Bologna. Minimale e scuro.

Nero di sala, bianco carta e un solo rosso, quello del REC. Titoli nello Space Grotesk sottile, testo in Inter, i
dati in DM Mono come il timecode. Angoli quasi vivi, fili sottili, grana di pellicola.

Dettagli: l'apertura «contatore» come il conto alla rovescia di un rullo, il fotogramma 2,39:1 attorno al video
d'apertura, il timecode sotto le immagini, il monitor di regia che segue il cursore nell'elenco dei lavori.

Moduli: lo showreel montato davanti a chi guarda (Montaggio: monitor, timecode, righello, traccia video e audio,
testina da trascinare, J K L), i quattro mestieri che si svelano di lato (Reveal orizzontale), i lavori col monitor
di regia (Hover List «monitor»), la pellicola del vino in video, il metodo in cinque passi, i prezzi da cui partire,
le persone, il teatro di posa e il modulo per il preventivo.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'ventiquattro'
NAME = 'Ventiquattro'

HEX = {
    'primary': '#FF4127', 'primary_contrast': '#0D0D0D', 'secondary': '#2E2D2B', 'secondary_contrast': '#F2F0EB',
    'accent': '#FF4127', 'dark': '#070707', 'light': '#F2F0EB', 'muted': '#161615', 'muted_contrast': '#F2F0EB',
    'text': '#F2F0EB', 'text_muted': '#8E8C86', 'background': '#0D0D0D', 'border': '#2A2927', 'link': '#F2F0EB',
}

BIANCO = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
REC = 'var(--olo-color-primary)'
ON_REC = 'var(--olo-color-primary-contrast)'
FONDO = 'var(--olo-color-background)'
SALA = 'var(--olo-color-muted)'
NERO = 'var(--olo-color-dark)'
FILO = 'var(--olo-color-border)'
SOFT = mix(BIANCO, 80)
MONO = 'var(--olo-font-family-mono)'
SQ = radius(2)

ADDRESS = 'Via del Pratello 46, 40122 Bologna'
TEATRO = 'Teatro di posa: via Stalingrado 112, Bologna'
PHONE = '051 000 2424'
PHONE_TEL = 'tel:+390510002424'
MAIL = 'produzione@ventiquattro.example'
LAT, LNG = '44.4950', '11.3332'
HOME = '{{PAGINA:home}}'
LAVORI = '{{PAGINA:lavori}}'
SERVIZI = '{{PAGINA:servizi}}'
STUDIO = '{{PAGINA:studio}}'

V_CAPELLI = 'pexels-video-8328576'    # una ragazza scuote i capelli su fondo grigio, al rallentatore
V_BIANCO = 'pexels-video-6154040'     # il vestito bianco che si muove sul blu scuro
V_STRADA = 'pexels-video-5558025'     # un'auto su una strada dritta fra i campi
V_ORO = 'pexels-video-3325983'        # una salsa dorata che cola da un cucchiaio
V_ROSSO = 'pexels-video-30618930'     # una band nella luce rossa
V_CORSA = 'pexels-video-8533122'      # un corridore in pista, i piedi e il viso
V_CIME = 'pexels-video-40295220'      # le montagne dall'alto fra le nuvole
V_VINO = 'pexels-video-19826636'      # il vino versato nel bicchiere, dall'alto
V_OPERATORE = 'pexels-video-26898433'  # un operatore dietro la cinepresa, luce blu
V_CITTA = 'pexels-video-33811927'     # la città di notte dall'alto

PHOTOS = [
    'pexels-8088389.jpg', 'pexels-6899777.jpg', 'pexels-7319477.jpg', 'pexels-8089675.jpg', 'pexels-9227679.jpg',
    'pexels-3945325.jpg', 'pexels-8088380.jpg', 'pexels-29508640.jpg', 'pexels-40399698.jpg', 'pexels-40399664.jpg',
    'pexels-6664785.jpg', 'pexels-39579116.jpg', 'pexels-39587241.jpg', 'pexels-7991486.jpg', 'pexels-7991491.jpg',
    'pexels-3709370.jpg', 'pexels-8100058.jpg', 'pexels-8100059.jpg', 'pexels-8102676.jpg', 'pexels-28712136.jpg',
    'pexels-18660493.jpg', 'pexels-6794933.jpg', 'pexels-6202743.jpg', 'pexels-30396798.jpg', 'pexels-40399674.jpg',
    'pexels-8089650.jpg', 'pexels-19224452.jpg', 'pexels-10321163.jpg', 'pexels-695730.jpg', 'pexels-6253568.jpg',
    'pexels-32774292.jpg', 'pexels-30736254.jpg', 'pexels-7239913.jpg', 'pexels-36799210.jpg', 'pexels-30680185.jpg',
    'pexels-19224453.jpg', 'pexels-30697922.jpg', 'pexels-34007217.jpg', 'pexels-30681572.jpg', 'pexels-3692625.jpg',
    'pexels-27202111.jpg', 'pexels-13991779.jpg', 'pexels-23384400.jpg', 'pexels-13811051.jpg', 'pexels-32610376.jpg',
    'pexels-11043433.jpg',
]
VIDEOS = [V_CAPELLI, V_BIANCO, V_STRADA, V_ORO, V_ROSSO, V_CORSA, V_CIME, V_VINO, V_OPERATORE, V_CITTA]
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

def sechead(t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=40):
    return t.classic('section-header', {
        'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': REC, 'eyebrow_dot_color': REC,
        'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '400',
        'headline_lines': [{'text': l, 'color': BIANCO if i % 2 == 0 else TENUE, 'italic': False} for i, l in enumerate(lines)],
        'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.0,
        'headline_font_weight': '300', 'headline_align': align,
        'headline_font_size_tablet': max(38, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
        'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False, 'tagline_text_color': TENUE,
        'tagline_text_size': 16, 'tagline_font_family': 'body', 'tagline_font_weight': '', 'tagline_caption': '',
        'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
        'vertical_align': 'end', 'headline_inline': False,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
              'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})


def headline(t, label_, size=40, color=BIANCO, tag='h3', pad_bottom=10, align='left', weight='300', spacing=None, sizes=None):
    o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading'}
    if spacing is not None:
        o['heading_letter_spacing'] = spacing
    m = re.search(r'<em>(.*?)</em>', label_)
    if m:
        o.update({'accent_text': m.group(1), 'accent_italic': False, 'accent_color': REC})
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
    if kind == 'rec':
        o = {'bg_color': REC, 'text_color': ON_REC, 'hover_bg_color': BIANCO, 'hover_text_color': NERO}
    else:
        o = {'bg_color': 'transparent', 'text_color': BIANCO, 'hover_bg_color': BIANCO, 'hover_text_color': NERO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(BIANCO, 45), 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 2, 'font_size': 11, 'font_weight': '500',
              'text_transform': 'uppercase', 'letter_spacing': '2'})
    return t.classic('button', o, style={'padding_top': pad_top})


def image(t, name, alt, ratio='16/9', position='center center', drift=False, lightbox=False):
    return t.classic('image', {'image_url': img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                               'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                               'border_radius': SQ, 'lightbox': lightbox, 'shadow': 'none',
                               'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                     advanced={'parallax': {'y': [{'value': -24, 'position': 0}, {'value': 24, 'position': 100}]}} if drift else None)


def still(t, name, alt, tc, ratio='16/9', position='center center', drift=False):
    """Un fotogramma col suo timecode sotto, in mono."""
    return [image(t, name, alt, ratio=ratio, position=position, drift=drift), label(t, tc, pad_top=10, pad_bottom=0)]


def centered(t, tiles, side=16.66):
    mid = round(100 - 2 * side, 2)
    return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                 layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')


def specs(t, pairs):
    return t.classic('desclist', {
        'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
        'layout': 'vertical', 'term_color': TENUE, 'term_font_size': '11', 'term_font_weight': '400',
        'definition_color': BIANCO, 'definition_font_size': '16', 'separator': True, 'border_color': FILO},
        style={'margin_top': 8, 'margin_bottom': 6})


def contacts(t):
    return t.classic('iconlist', {
        'items': [{'icon': 'map-pin', 'text': ADDRESS, 'link': STUDIO + '#dove', 'color': ''},
                  {'icon': 'phone', 'text': PHONE, 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': REC, 'icon_size': '16', 'text_color': BIANCO, 'text_size': '15', 'gap': '12', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
    }, style={})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
        'header_bg': '', 'header_bg_active': '', 'content_bg': '',
        'header_text_color': BIANCO, 'header_text_color_active': REC, 'header_font_size': 20, 'header_font_weight': '300',
        'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': REC, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
    }, style={})


def intro(t, eyebrow, lines, body, size=104):
    return t.section([
        t.row([t.col([sechead(t, eyebrow, lines, size=size, size_mobile=46)], wm='3-5', ws='1-1'),
               t.col([text(t, body, size=17)], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=56, valign='end'),
    ], padding='large', style={'padding_top': 190, 'padding_bottom': 64})


def pannello(t, foto, eyebrow, titolo, testo, link_t, link_u, effetto='reveal-h', overlay=50, pos='center center'):
    blocco = [label(t, eyebrow, color=REC),
              headline(t, titolo, size=76, tag='h2', pad_bottom=16),
              text(t, '<p>' + testo + '</p>', size=16, color=SOFT)]
    if link_t:
        blocco.append(button(t, link_t, link_u, pad_top=10))
    return t.section([
        t.row([t.col(blocco, wm='1-2', ws='1-1'), t.col([], wm='1-2', ws='1-1')], layout='custom', custom='52,48',
              valign='end', anim=None),
    ], bg=photo_bg(foto, overlay=overlay, position=pos), padding='large', width='large', sticky_effect=effetto,
        sticky_h_align='bottom', style={'padding_top': 140, 'padding_bottom': 80})


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER · FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    mm = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': 'VENTIQUATTRO', 'logo_text_size': '15', 'logo_text_color': BIANCO,
        'logo_text_font': 'heading', 'logo_text_weight': '500', 'logo_text_spacing': 5, 'logo_text_transform': '',
        'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
        'layout': 'right', 'header_mode': 'overlay', 'sticky': True,
        'sticky_bg': FONDO, 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
        'nav_bg': 'transparent', 'text_color': BIANCO, 'hover_color': REC, 'active_color': REC,
        'font_size': '11', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '34',
        'bar_width': 'wide', 'nav_height': '80', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
        'hover_effect': 'underline', 'hover_effect_color': REC,
        'button_mode': 'last', 'btn_bg': REC, 'btn_color': ON_REC, 'btn_hover_color': ON_REC,
        'btn_radius': SQ, 'btn_padding': {'top': 11, 'right': 20, 'bottom': 11, 'left': 20}, 'btn_hover_effect': 'none',
        'btn_font_weight': '600', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2',
        'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
        'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
        'mobile_style': 'fullscreen', 'mobile_bg': NERO, 'mobile_text_color': BIANCO, 'mobile_accent_color': REC,
        'hamburger_style': 'minimal', 'hamburger_color': BIANCO, 'mobile_font_size': '40', 'mobile_link_size': '40',
        'mobile_link_font': 'heading', 'mobile_link_weight': '300',
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(BIANCO, 10)},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]


def build_footer():
    t = Tpl(ID, 'footer')
    brand = headline(t, 'VENTIQUATTRO', size=130, tag='p', pad_bottom=0, weight='300', spacing=-2, sizes=(84, 42))
    claim = text(t, '<p>Casa di produzione a Bologna dal 2012: spot, brand film, videoclip e documentari. Ventiquattro '
                    'fotogrammi al secondo, uno alla volta.</p>', size=15, pad=(0, 0, 18, 0))

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': BIANCO, 'link_hover_color': REC, 'active_color': REC,
                                 'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    pagine = nav([('I lavori', LAVORI), ('Cosa facciamo', SERVIZI), ('Lo studio', STUDIO), ('Il preventivo', STUDIO + '#scrivici'),
                  ('Privacy', '/privacy-policy/')])
    posa = text(t, '<p>' + TEATRO + '<br>240 m², cyclorama bianco e verde, 6 m di altezza</p><p>Lunedì — venerdì 9 — 19, '
                   'le riprese quando servono.</p>', size=15, color=BIANCO)
    legal = text(t, '<p>© 2026 Ventiquattro Produzioni s.r.l. · Bologna · I lavori mostrati appartengono ai loro committenti.</p>',
                 size=12)
    social = t.classic('social', {'links': [{'platform': 'instagram', 'url': 'https://instagram.com'},
                                            {'platform': 'vimeo', 'url': 'https://vimeo.com'}],
                                  'size': '30', 'alignment': 'right', 'icon_color': TENUE, 'hover_effect': 'none'})
    totop = t.classic('totop', {'icon_color': NERO, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([claim, contacts(t)], wm='1-2', ws='1-1'),
        t.col([label(t, 'Le pagine', color=REC), pagine], wm='1-4', ws='1-2'),
        t.col([label(t, 'Il teatro di posa', color=REC), posa], wm='1-4', ws='1-2'),
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

CLIP = [
    ('Bianco', 'Fashion film', 'Atelier Neve', '2026', V_BIANCO, 12),
    ('Strada bianca', 'Spot', 'Ferri Automobili', '2026', V_STRADA, 10),
    ('Oro', 'Spot food', 'Pastificio Barbieri', '2025', V_ORO, 10),
    ('Rosso', 'Videoclip', 'I Fari', '2025', V_ROSSO, 10),
    ('Ultimi cento metri', 'Brand film', 'Corri Bologna', '2026', V_CORSA, 10),
    ('Sopra le nuvole', 'Documentario', 'Montagne Aperte', '2024', V_CIME, 10),
]


def montaggio(t):
    return t.bare('montaggio', {
        'clip': [{'titolo': ti, 'tipo': tp, 'cliente': cl, 'anno': an, 'video': img(v + '.mp4'), 'immagine': img(v + '.jpg'),
                  'durata': d, 'link': LAVORI + '#' + re.sub(r'[^a-z]+', '-', ti.lower()).strip('-'), 'testo_link': 'Il progetto',
                  'colore': ''} for ti, tp, cl, an, v, d in CLIP],
        'nome_sequenza': 'SHOWREEL_2026_v12', 'fps': 24, 'parti_da_sola': True, 'mostra_audio': True, 'mostra_info': True,
        'colore_fondo': SALA, 'colore_testo': BIANCO, 'colore_testina': REC, 'colore_clip': '#2E2D2B', 'colore_audio': mix(REC, 70),
        'titolo_font_family': 'heading', 'titolo_font_size': 40, 'titolo_font_size_mobile': 28, 'titolo_font_weight': '300',
        'titolo_color': BIANCO, 'dati_font_family': MONO, 'raggio': radius(4), 'proporzione': '21/9', 'altezza_traccia': 58,
        'posizione_info': 'lato',
    })


def build_home():
    t = Tpl(ID, 'home')
    out = []
    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Casa di produzione · Bologna · dal 2012', 'eyebrow_dot': True,
        'title': 'Ventiquattro<br>fotogrammi<br><em>al secondo.</em>',
        'subtitle': 'Spot, brand film, videoclip e documentari. Li scriviamo, li giriamo e li montiamo qui, uno alla volta.',
        'media_bg': {'type': 'video', 'video_url': img(V_CAPELLI + '.mp4'), 'video_poster': img(V_CAPELLI + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': NERO, 'overlay_top': 0.25, 'overlay_bottom': 0.7, 'overlay_sides': False, 'overlay_direction': 'left',
        'glow_on': False, 'frame_on': True, 'frame_inset': 28, 'watermark_text': '',
        'accent': REC, 'text_color': BIANCO, 'title_color': BIANCO, 'subtitle_color': SOFT,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 112, 'title_font_weight': '300',
        'title_line_height': '0.95', 'title_letter_spacing': '-3', 'title_text_transform': 'none',
        'subtitle_font_size': 16, 'subtitle_max_width': 420,
        'min_height': '100vh', 'content_max_width': 1320, 'vertical_align': 'bottom', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 150, 'right': 44, 'bottom': 84, 'left': 44},
        'cta_text': 'Lo showreel', 'cta_url': '#showreel', 'cta_bg_color': REC, 'cta_text_color': ON_REC,
        'cta_radius': SQ, 'cta_size': '11', 'cta_style': 'filled', 'cta_font_weight': '600', 'cta_text_transform': 'uppercase',
        'cta_letter_spacing': '2',
        'cta2_text': 'Il preventivo', 'cta2_url': STUDIO + '#scrivici', 'cta2_text_color': BIANCO, 'cta2_style': 'ghost',
        'meta_text': '● REC · 4K · 2,39:1 · 24 fps', 'scroll_hint': '', 'scene_depth': 'soft', 'scene_depth_fade': True, 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    out.append(t.section([
        t.one([sechead(t, '01 — Showreel 2026', ['Lo showreel,', 'montato davanti a te.'], size=80,
                       tagline='Sei lavori in fila sulla timeline. Trascina la testina, tocca una clip, o usa J K L come in sala di '
                               'montaggio: il monitor segue.')]),
        t.one([montaggio(t)]),
    ], padding='large', width='large', html_id='showreel', style={'padding_top': 150, 'padding_bottom': 130}))

    for i, (foto, ey, ti, tx, pos) in enumerate([
        ('pexels-23384400.jpg', '01 / 04 — Spot e brand film', 'Trenta secondi che restano.',
         'Per aziende e agenzie: dall’idea al montaggio finale, con la troupe giusta e un solo responsabile. Anche in quattro lingue.',
         'center 40%'),
        ('pexels-8089675.jpg', '02 / 04 — Videoclip', 'La musica la porti tu.',
         'Per band ed etichette: un concetto, una giornata di riprese, il colore. Il budget lo diciamo prima, non dopo.',
         'center center'),
        ('pexels-34007217.jpg', '03 / 04 — Documentari', 'Storie vere, tempi lunghi.',
         'Seguiamo persone e luoghi per mesi. Tre dei nostri documentari sono passati in festival in Italia e all’estero.',
         'center center'),
        ('pexels-7319477.jpg', '04 / 04 — Moda e contenuti', 'Il vestito si muove.',
         'Fashion film, campagne e contenuti verticali per i social, girati nello stesso giorno con la stessa luce.',
         'center 30%'),
    ]):
        out.append(pannello(t, foto, ey, ti, tx, 'I lavori', LAVORI, overlay=52, pos=pos))

    numeri = t.classic('statstrip', {
        'items': [{'value': '14', 'label': 'anni di set'}, {'value': '340', 'label': 'produzioni consegnate'},
                  {'value': '9', 'label': 'premi e selezioni ufficiali'}, {'value': '1', 'label': 'responsabile per progetto'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 30, 'show_dividers': True, 'divider_color': FILO, 'band_border': True,
        'value_font_family': 'heading', 'value_color': BIANCO, 'value_size': 56, 'value_weight': '300',
        'label_color': TENUE, 'label_size': 11, 'label_uppercase': True, 'label_weight': '400', 'align': 'left',
        'mono_font_family': 'mono',
    })
    clienti = t.classic('trust-strip', {
        'items': [{'icon': '', 'icon_color': '', 'text': n, 'badge': ''} for n in
                  ['Atelier Neve', 'Ferri Automobili', 'Pastificio Barbieri', 'I Fari', 'Corri Bologna', 'Montagne Aperte',
                   'Teatro Nuovo', 'Cooperativa Lune']],
        'text_color': TENUE, 'text_size': 15, 'font_family': 'body', 'align': 'left', 'flow': 'wrap', 'gap': 26,
        'variant': 'inline', 'icon_size': 16, 'separator_char': '·', 'separator_color': REC,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    out.append(t.section([
        t.row([t.col([label(t, '02 — Il metodo', color=REC)], wm='1-4', ws='1-1'),
               t.col([headline(t, 'Una storia, una troupe, <em>un responsabile</em>. Dall’idea alla copia finale non cambia '
                                  'chi ti risponde al telefono.', size=46, tag='h2', pad_bottom=0)], wm='3-4', ws='1-1')],
              layout='custom', custom='25,75', gap=32, valign='start'),
        t.one([numeri], style={'margin_top': 72}),
        t.row([t.col([label(t, 'Hanno girato con noi', pad_bottom=10), clienti], wm='1-1', ws='1-1')],
              style={'margin_top': 56}),
    ], padding='large', style={'padding_top': 140, 'padding_bottom': 120}))

    out.append(t.section([
        centered(t, [label(t, 'Spot · Cantina Pradalupo · 00:00:07:12', color=SOFT, align='center'),
                     headline(t, 'Ogni fotogramma <em>è una scelta.</em>', size=96, tag='h2', align='center', pad_bottom=18),
                     text(t, '<p>Il vino versato in questo spot è lo stesso bicchiere, ripreso trentuno volte. Abbiamo tenuto la '
                             'ventiquattresima.</p>', size=16, color=SOFT, align='center')]),
    ], bg=video_bg(V_VINO, overlay=45), padding='xlarge', style={'padding_top': 230, 'padding_bottom': 230}))

    out.append(t.section([
        t.row([t.col([headline(t, 'Hai una storia? <em>Ci serve un caffè e un’ora.</em>', size=76, tag='h2', pad_bottom=20),
                      text(t, '<p>Raccontacela, anche solo a voce. Entro due giorni ti mandiamo l’idea, i tempi e il budget, '
                              'scritti su una pagina.</p>', size=17),
                      button(t, 'Il preventivo', STUDIO + '#scrivici', kind='rec', pad_top=12)], wm='3-5', ws='1-1'),
               t.col(still(t, 'pexels-6664785.jpg', 'La cinepresa montata col monitor, pronta a girare', 'A CAM · 00:00:00:01', ratio='4/5',
                           drift=True), wm='2-5', ws='1-1')],
              layout='custom', custom='58,42', gap=72, valign='center'),
    ], bg=solid(SALA), padding='large', style={'padding_top': 140, 'padding_bottom': 140}))
    return page(out, page_intro_enabled=True, page_intro_style='contatore', page_intro_text='VENTIQUATTRO',
                page_intro_caption='Carico il rullo · 24 fps', page_intro_bg=HEX['dark'], page_intro_color=HEX['primary'],
                page_intro_duration=2000, page_intro_once=True)


# ══════════════════════════════════════════════════════════════════════════════
#  I LAVORI
# ══════════════════════════════════════════════════════════════════════════════

LAVORI_ELENCO = [
    ('Bianco', 'Fashion film · Atelier Neve · 2026', 'pexels-7319477.jpg'),
    ('Strada bianca', 'Spot · Ferri Automobili · 2026', 'pexels-13811051.jpg'),
    ('Oro', 'Spot food · Pastificio Barbieri · 2025', 'pexels-40399674.jpg'),
    ('Rosso', 'Videoclip · I Fari · 2025', 'pexels-8089675.jpg'),
    ('Ultimi cento metri', 'Brand film · Corri Bologna · 2026', 'pexels-30680185.jpg'),
    ('Sopra le nuvole', 'Documentario · Montagne Aperte · 2024', 'pexels-28712136.jpg'),
    ('La notte del teatro', 'Documentario · Teatro Nuovo · 2023', 'pexels-7991491.jpg'),
    ('Lune', 'Brand film · Cooperativa Lune · 2023', 'pexels-30736254.jpg'),
]

PROGETTI = [
    ('bianco', 'Bianco', 'Atelier Neve · fashion film · 2026', 'Un vestito, un fondale blu, una ballerina. Tre minuti senza dialoghi, '
     'girati in un giorno nel nostro teatro di posa.', ['pexels-video-6154040.jpg', 'pexels-6899777.jpg', 'pexels-8089650.jpg']),
    ('strada-bianca', 'Strada bianca', 'Ferri Automobili · spot 30″ · 2026', 'Una strada fra i campi della Bassa all’alba, '
     'un’auto elettrica e nessun rumore. Girato col drone e col braccio sulla macchina.', ['pexels-video-5558025.jpg', 'pexels-13811051.jpg', 'pexels-18660493.jpg']),
    ('oro', 'Oro', 'Pastificio Barbieri · spot food 20″ · 2025', 'Una salsa che cola al rallentatore, ripresa a mille fotogrammi '
     'al secondo sotto una sola luce calda. Due giorni per otto secondi.', ['pexels-video-3325983.jpg', 'pexels-40399674.jpg', 'pexels-30396798.jpg']),
    ('rosso', 'Rosso', 'I Fari · videoclip · 2025', 'Una sola luce rossa, quattro musicisti, una notte. Il pezzo è entrato '
     'nelle radio, il video in due festival.', ['pexels-video-30618930.jpg', 'pexels-8088389.jpg', 'pexels-36799210.jpg']),
    ('ultimi-cento-metri', 'Ultimi cento metri', 'Corri Bologna · brand film · 2026', 'Un passo dopo l’altro, l’ultima curva '
     'della pista e il fiato. Girato con la cinepresa in spalla, correndo accanto.', ['pexels-video-8533122.jpg', 'pexels-32610376.jpg', 'pexels-11043433.jpg']),
    ('sopra-le-nuvole', 'Sopra le nuvole', 'Montagne Aperte · documentario · 2024', 'Un anno con i gestori di tre rifugi '
     'dell’Appennino. Cinquantadue minuti, selezione ufficiale al Trento Film Festival.', ['pexels-video-40295220.jpg', 'pexels-19224452.jpg', 'pexels-30697922.jpg']),
]
SCHEDE = {p[0] for p in PROGETTI}


def ancora(nome):
    return re.sub(r'[^a-z]+', '-', nome.lower()).strip('-')


def build_lavori():
    t = Tpl(ID, 'lavori')
    out = [intro(t, 'I lavori', ['Otto lavori,', 'quattordici anni.'],
                 '<p>Passa sopra i titoli: il monitor di regia mostra un fotogramma. Ogni progetto ha avuto un solo responsabile, '
                 'dall’idea alla copia finale.</p>')]
    elenco = t.classic('hoverlist', {
        'items': [{'name': n, 'sub': c, 'desc': c, 'number': '%02d' % (i + 1), 'image': img(f),
                   'link_url': ('#' + ancora(n)) if ancora(n) in SCHEDE else '', 'color': ''}
                  for i, (n, c, f) in enumerate(LAVORI_ELENCO)],
        'lead_mode': 'number', 'number_color': TENUE, 'number_hover_color': REC,
        'name_font_family': 'heading', 'name_weight': '300', 'name_color': BIANCO, 'name_hover_color': BIANCO, 'name_size': 56,
        'name_uppercase': False, 'desc_color': TENUE, 'desc_hover_color': BIANCO, 'desc_size': 13, 'sub_uppercase': False,
        'row_padding_y': 24, 'hover_indent': 28, 'hover_bg': 'transparent', 'line_color': FILO,
        'peek': True, 'peek_mode': 'monitor', 'peek_width': 320,
        'row_padding': {'top': 24, 'right': 0, 'bottom': 24, 'left': 0},
    })
    out.append(t.section([t.one([elenco])], padding='large', style={'padding_top': 40}))

    for i, (aid, nome, chi, testo, foto) in enumerate(PROGETTI):
        galleria = t.classic('progallery', {
            'images': [{'url': img(f), 'alt': nome + ': un fotogramma', 'caption': '', 'subtitle': '', 'text': ''} for f in foto],
            'layout': 'expand', 'layout_family': 'classic', 'columns': 3, 'gap': 6, 'img_height': '360px',
            'object_fit': 'cover', 'object_position': 'center center', 'thumb_radius': 2, 'mobile_columns': 1,
            'expand_ratio': 2.4, 'expand_shrink': 0.6, 'expand_speed': 600, 'hover_effect': 'none', 'hover_caption': 'none',
            'lightbox': True, 'show_caption': False, 'entrance': 'none', 'filter': 'none',
        })
        out.append(t.section([
            t.row([t.col([label(t, chi, color=REC), headline(t, nome, size=56, tag='h2', pad_bottom=14),
                          text(t, '<p>' + testo + '</p>', size=16)], wm='1-3', ws='1-1'),
                   t.col([galleria], wm='2-3', ws='1-1')], layout='custom', custom='30,70', gap=56, valign='center'),
        ], bg=solid(SALA) if i % 2 == 0 else None, padding='large', html_id=aid))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  COSA FACCIAMO
# ══════════════════════════════════════════════════════════════════════════════

def build_servizi():
    t = Tpl(ID, 'servizi')
    out = [intro(t, 'Cosa facciamo', ['Dall’idea', 'alla copia finale.'],
                 '<p>Scrittura, pre-produzione, riprese, montaggio, colore e suono, sempre con la stessa squadra. Se un pezzo '
                 'lo fa qualcun altro, te lo diciamo prima.</p>')]
    passi = t.classic('process-steps', {
        'items': [{'number': '01', 'title': 'L’idea', 'description': 'Un incontro, poi una pagina: concetto, riferimenti, tempi e '
                                                                       'budget. Gratis.'},
                  {'number': '02', 'title': 'La preparazione', 'description': 'Sceneggiatura, storyboard, location, casting, permessi. '
                                                                                'Due o tre settimane.'},
                  {'number': '03', 'title': 'Le riprese', 'description': 'Uno o più giorni, con la troupe che serve: da tre a trenta '
                                                                          'persone.'},
                  {'number': '04', 'title': 'Il montaggio', 'description': 'Il primo montato in una settimana, poi due giri di '
                                                                            'correzioni compresi.'},
                  {'number': '05', 'title': 'Colore e suono', 'description': 'Correzione colore, mix e musica. Le copie per cinema, '
                                                                              'web e social.'}],
        'columns': 5, 'gap': 0, 'auto_number': False, 'number_style': 'plain', 'number_color': REC, 'number_bg': '',
        'number_size': 13, 'number_font': MONO, 'number_weight': '400', 'title_color': BIANCO, 'title_size': 22,
        'title_weight': '300', 'title_font': 'heading', 'desc_color': TENUE, 'desc_size': 14, 'align': 'left', 'item_gap': 10,
        'card_bg': 'transparent', 'card_border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': FILO},
        'card_radius': radius(0), 'card_padding': 22, 'columns_tablet': 2, 'columns_mobile': 1,
    })
    out.append(t.section([t.one([passi])], padding='large', style={'padding_top': 20}))

    out.append(t.section([
        t.row([t.col(still(t, 'pexels-8100058.jpg', 'Il colorista davanti ai due monitor della sala colore', 'SALA COLORE · DCI-P3'),
                     wm='1-2', ws='1-1'),
               t.col(still(t, 'pexels-10321163.jpg', 'Un microfono da studio in bianco e nero', 'SALA MIX · 5.1 e stereo'),
                     wm='1-2', ws='1-1')], layout='50-50', gap=24),
    ], padding='large', style={'padding_top': 20}))

    listino = t.classic('pricelist', {
        'items': [{'title': 'Contenuti social', 'description': 'Una giornata, dieci video verticali montati.', 'price': 'da 3.500 €',
                   'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Videoclip', 'description': 'Concetto, una giornata di riprese, montaggio e colore.', 'price': 'da 6.000 €',
                   'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Spot 30″', 'description': 'Scrittura, casting, due giornate, montaggio, colore, suono.', 'price': 'da 18.000 €',
                   'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Brand film', 'description': 'Da tre a cinque minuti, fino a cinque giornate.', 'price': 'da 25.000 €',
                   'image_url': '', 'highlighted': False, 'badge': ''},
                  {'title': 'Documentario', 'description': 'Lungo o corto: il budget si fa insieme, anche con i bandi.', 'price': 'su misura',
                   'image_url': '', 'highlighted': False, 'badge': ''}],
        'separator_style': 'solid', 'separator_color': FILO, 'title_color': BIANCO, 'price_color': REC,
        'description_color': TENUE, 'image_size': 64, 'image_border_radius': '0', 'show_image': False,
        'price_position': 'right', 'tile_padding': {'top': 18, 'right': 0, 'bottom': 18, 'left': 0}, 'card_bg': 'transparent',
        'card_border_radius': '0',
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Da quanto si parte', ['Prezzi veri,', 'non «dipende».'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Sono le cifre da cui partono i nostri progetti, IVA esclusa. Il preventivo vero arriva entro due '
                              'giorni, su una pagina.</p>'),
                      button(t, 'Il preventivo', STUDIO + '#scrivici', kind='rec')], wm='1-3', ws='1-1'),
               t.col([listino], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], bg=solid(SALA), padding='large'))

    out.append(t.section([
        t.row([t.col([sechead(t, 'Le domande', ['Quelle che', 'ci fanno tutti.'], size=44, layout='stack', size_mobile=32),
                      specs(t, [('Cinepresa', 'ARRI Alexa Mini LF, RED Komodo'), ('Ottiche', 'anamorfiche e sferiche'),
                                ('Drone', 'pilota con patentino, in città e in montagna'), ('Teatro di posa', '240 m², cyclorama')])],
                     wm='1-3', ws='1-1'),
               t.col([faq(t, [
                   ('Quanto tempo serve per uno spot?', 'Da sei a otto settimane dall’idea alla consegna. Se c’è fretta si fa in tre, '
                                                       'ma te lo diciamo con il preventivo.'),
                   ('Scrivete anche la storia?', 'Sì: nel nostro gruppo ci sono due autori. Se hai già un’agenzia, lavoriamo col suo '
                                                 'testo.'),
                   ('Girate fuori dall’Italia?', 'Sì, in Europa con la nostra troupe; altrove con una squadra del posto e il nostro '
                                                 'direttore della fotografia.'),
                   ('A chi restano i diritti?', 'Al committente, per l’uso concordato. Noi chiediamo solo di poterlo mostrare nello '
                                                'showreel.'),
               ], 'serv')], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=64, valign='start'),
    ], padding='large'))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  LO STUDIO
# ══════════════════════════════════════════════════════════════════════════════

def build_studio():
    t = Tpl(ID, 'studio')
    out = [intro(t, 'Lo studio', ['Undici persone', 'e un teatro di posa.'],
                 '<p>Al Pratello gli uffici e le sale di montaggio, in via Stalingrado il teatro di posa. Ci trovi tutti i giorni, '
                 'tranne quando giriamo.</p>')]

    def persona(foto, nome, ruolo, bio, pos):
        return [image(t, foto, nome + ', ' + ruolo.lower(), ratio='4/5', position=pos),
                headline(t, nome, size=24, tag='h3', pad_bottom=2),
                label(t, ruolo, color=REC, pad_bottom=8, pad_top=6),
                text(t, '<p>' + bio + '</p>', size=15)]
    out.append(t.section([
        t.row([t.col(persona('pexels-3692625.jpg', 'Marco Zanotti', 'Regista e fondatore',
                             'Ha cominciato coi videoclip delle band di Bologna. Oggi firma gli spot e i documentari.', 'center 30%'),
                     wm='1-3', ws='1-1'),
               t.col(persona('pexels-27202111.jpg', 'Davide Gherardi', 'Direttore della fotografia',
                             'Vent’anni di set, due Alexa e una passione per le luci basse. Ha girato «Sopra le nuvole».', 'center 35%'),
                     wm='1-3', ws='1-1'),
               t.col(persona('pexels-30681572.jpg', 'Lia Ferraresi', 'Montaggio e colore',
                             'Monta tutto quello che esce da qui. Il colore lo fa di notte, quando la sala è sua.', 'center 35%'),
                     wm='1-3', ws='1-1')],
              layout='33-33-33', gap=32, valign='start'),
    ], padding='large', style={'padding_top': 20}))

    out.append(t.section([
        t.row([t.col([label(t, 'Il teatro di posa · via Stalingrado 112', color=REC),
                      headline(t, '240 metri quadri, <em>sei di altezza.</em>', size=64, tag='h2', pad_bottom=16),
                      text(t, '<p>Cyclorama bianco e verde, graticcia motorizzata, cucina e camerini. Lo affittiamo anche a chi '
                              'gira con la sua troupe: mezza giornata o giornata intera.</p>', size=16, color=SOFT),
                      specs(t, [('Mezza giornata', '650 €, con un tecnico'), ('Giornata intera', '1.100 €, con un tecnico'),
                                ('Luci', 'kit LED e tungsteno compresi')])], wm='1-2', ws='1-1'),
               t.col([], wm='1-2', ws='1-1')], layout='50-50', valign='end'),
    ], bg=video_bg(V_OPERATORE, overlay=58), padding='xlarge', style={'padding_top': 220, 'padding_bottom': 110}))

    modulo = t.classic('form', {
        'fields': [
            {'field_type': 'text', 'label': 'Nome', 'name': 'nome', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'email', 'label': 'Email', 'name': 'email', 'placeholder': '', 'required': True, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'select', 'label': 'Il progetto', 'name': 'progetto', 'placeholder': '', 'required': True, 'width': '1-2',
             'options': 'Spot\nBrand film\nVideoclip\nDocumentario\nContenuti social\nAffitto del teatro di posa', 'icon': ''},
            {'field_type': 'select', 'label': 'Il budget', 'name': 'budget', 'placeholder': '', 'required': False, 'width': '1-2',
             'options': 'Fino a 5.000 €\n5.000 — 15.000 €\n15.000 — 40.000 €\nOltre 40.000 €\nNon lo so ancora', 'icon': ''},
            {'field_type': 'date', 'label': 'Quando deve uscire', 'name': 'uscita', 'placeholder': '', 'required': False, 'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'text', 'label': 'Dove', 'name': 'dove', 'placeholder': 'Una città, un posto, un teatro', 'required': False,
             'width': '1-2', 'options': '', 'icon': ''},
            {'field_type': 'textarea', 'label': 'La storia', 'name': 'storia', 'placeholder': 'Anche solo tre righe', 'required': True,
             'width': '1-1', 'options': '', 'icon': ''},
        ],
        'email_subject': 'Ventiquattro — un progetto', 'submit_text': 'Manda', 'submit_icon': '',
        'success_message': 'Arrivata. Entro due giorni ti mandiamo l’idea, i tempi e il budget su una pagina.',
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': BIANCO, 'label_size': '11', 'label_weight': '400',
        'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': 'transparent', 'input_color': BIANCO,
        'input_border_color': mix(BIANCO, 30), 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 2,
        'input_focus_border': REC, 'input_focus_shadow': False, 'gap': '20', 'submit_bg': REC, 'submit_color': ON_REC,
        'submit_radius': 2, 'submit_padding_x': '30', 'submit_padding_y': '16', 'submit_font_size': '11',
        'submit_font_weight': '600', 'submit_text_transform': 'uppercase', 'submit_letter_spacing': '2',
        'submit_hover_bg': BIANCO, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Il preventivo', ['Raccontaci', 'la storia.'], size=72, layout='stack', size_mobile=42),
                      text(t, '<p>Entro due giorni l’idea, i tempi e il budget, scritti su una pagina. Se è un videoclip, '
                              'mandaci il pezzo.</p>'),
                      contacts(t)], wm='2-5', ws='1-1'),
               t.col([modulo], wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=72, valign='start'),
    ], bg=solid(SALA), padding='large', html_id='scrivici', style={'padding_top': 130, 'padding_bottom': 130}))

    mappa = t.classic('osmmap', {'latitude': LAT, 'longitude': LNG, 'zoom': '15', 'height': '440', 'marker': True,
                                 'marker_popup': 'Ventiquattro · via del Pratello 46', 'scroll_wheel_zoom': False,
                                 'tile_layer': 'dark', 'border_radius': SQ, 'marker_color': HEX['primary']}, style={})
    out.append(t.section([
        t.row([t.col([sechead(t, 'Dove', ['Al Pratello,', 'dentro le mura.'], size=48, layout='stack', size_mobile=34),
                      text(t, '<p>Gli uffici e le sale sono al primo piano, sopra un’osteria. Il teatro di posa è fuori porta, '
                              'in via Stalingrado: dieci minuti in bici.</p>')], wm='1-3', ws='1-1'),
               t.col([mappa], wm='2-3', ws='1-1')], layout='custom', custom='33,67', gap=56, valign='center'),
    ], padding='large', html_id='dove'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        centered(t, [label(t, 'Errore 404 · 00:00:00:00', color=REC, align='center'),
                     headline(t, 'Fotogramma mancante.', size=110, tag='h1', align='center', pad_bottom=20, spacing=-3),
                     text(t, '<p>Questa pagina è finita sul pavimento della sala di montaggio.</p>', size=17, align='center',
                          color=SOFT),
                     button(t, 'Torna all’inizio', HOME, kind='rec', align='center')]),
    ], bg=video_bg(V_CITTA, overlay=60), padding='xlarge', style={'padding_top': 240, 'padding_bottom': 220})]


THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Creativi e portfolio', 'zone': 'La testina la muovi tu',
    'tags': ['video', 'produzione', 'showreel', 'spot', 'videoclip', 'documentari', 'bologna', 'minimale'],
    'description': 'Una casa di produzione video a Bologna, minimale e scura con un solo rosso da REC: lo showreel su una timeline '
                   'di montaggio vera (monitor, timecode, tracce, testina da trascinare, J K L), i quattro mestieri che si '
                   'svelano di lato, i lavori col monitor di regia, i prezzi da cui partire, il teatro di posa e il preventivo.',
    'menu': {'name': 'Ventiquattro — menu', 'items': [
        {'title': 'Lavori', 'url': LAVORI}, {'title': 'Cosa facciamo', 'url': SERVIZI}, {'title': 'Lo studio', 'url': STUDIO},
        {'title': 'Preventivo', 'url': STUDIO + '#scrivici'}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Ventiquattro — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Ventiquattro — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Ventiquattro — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Ventiquattro — Home'},
        'lavori': {'file': 'lavori.json', 'type': 'page', 'title': 'Ventiquattro — I lavori'},
        'servizi': {'file': 'servizi.json', 'type': 'page', 'title': 'Ventiquattro — Cosa facciamo'},
        'studio': {'file': 'studio.json', 'type': 'page', 'title': 'Ventiquattro — Lo studio'},
    },
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'lavori': {'title': 'I lavori', 'template': 'lavori', 'slug': 'lavori'},
        'servizi': {'title': 'Cosa facciamo', 'template': 'servizi', 'slug': 'servizi'},
        'studio': {'title': 'Lo studio', 'template': 'studio', 'slug': 'studio'},
    },
    'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 30, 'ring_color': HEX['light'], 'ring_width': 1,
               'dot_size': 5, 'dot_color': HEX['primary'], 'hot_scale': 2.0, 'hot_color': HEX['primary'],
               'hot_fill': 'rgba(255,65,39,0.12)', 'blend_mode': 'difference', 'magnetic_selector': 'a, button',
               'pull_strength': 0.3, 'follow_easing': 0.2},
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Inter\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Space Grotesk\', \'Helvetica Neue\', Arial, sans-serif',
            'font_family_mono': '\'DM Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': '300',
            'heading_line_height': '1.0', 'heading_letter_spacing': '-0.02em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '5.5rem', 'font_size_h2': '3.6rem', 'font_size_h3': '2rem',
            'font_size_h4': '1.4rem', 'font_size_h5': '1.1rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.8rem',
            'font_size_h2_mobile': '2.1rem', 'font_size_h3_mobile': '1.5rem',
        },
        'google_fonts': ['Space Grotesk', 'Inter', 'DM Mono'],
        'google_fonts_weights': '300;400;500;600',
        'google_fonts_italic': [],
        'buttons': {'font_size': '11px', 'font_weight': '600', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': '2px',
                    'text_transform': 'uppercase', 'letter_spacing': '2px', 'hover_brightness': '100'},
        'layout': {'border_radius': '2px', 'border_radius_large': '4px', 'container_max_width': '1280px',
                   'container_narrow': '760px', 'container_wide': '1440px'},
        'forms': {'field_bg': 'transparent', 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '2px', 'field_padding': '13px 16px', 'field_font_size': '16px',
                  'focus_border_color': HEX['primary'], 'focus_shadow': 'none',
                  'label_font_size': '11px', 'label_font_weight': '400', 'label_margin_bottom': '8px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
        'grain': {'enabled': True, 'opacity': 5, 'scale': 170},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'lavori.json': build_lavori(), 'servizi.json': build_servizi(), 'studio.json': build_studio(),
              '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
