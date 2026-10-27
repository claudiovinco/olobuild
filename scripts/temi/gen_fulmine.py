# -*- coding: utf-8 -*-
"""Genera il tema «Fulmine» (id fulmine): un negozio online di elettronica con WooCommerce.

Bianco e grigio di magazzino, il blu notte della testata, il giallo del fulmine sui pulsanti e il rosso delle offerte.
Titoli in Archivo (stretto e deciso, da cartellone), testo in Inter, i codici in JetBrains Mono. Card fitte, angoli
morbidi, nessuna decorazione che rubi spazio ai prodotti.

Le tile di WooCommerce fanno il negozio: la ricerca dal vivo fra i prodotti col prezzo, il carrello e i preferiti in
testata, le categorie, le card con la seconda foto, il voto e le recensioni contate, la marca, il risparmio, la
consegna e la scorta che finisce, il cuore dei preferiti e l'anteprima veloce; le offerte lampo col conto alla
rovescia che ricomincia ogni giorno, il filtro per prezzo, categoria e marca, il confronto, la scheda prodotto
(galleria, prezzo, disponibilità, carrello, schede, correlati, visti di recente), il carrello e la cassa a passi.
I prodotti di prova li crea prodotti-fulmine.php (wp eval-file) sul sito della demo (meta _olo_demo = fulmine).
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from base import Tpl, write_theme, page, lib, mix, radius, solid  # noqa: E402

ID = 'fulmine'
NAME = 'Fulmine'

HEX = {
    'primary': '#FFC72C', 'primary_contrast': '#101828', 'secondary': '#2F5BFF', 'secondary_contrast': '#FFFFFF',
    'accent': '#D42A36', 'dark': '#0B1226', 'light': '#FFFFFF', 'muted': '#EEF1F6', 'muted_contrast': '#101828',
    'text': '#101828', 'text_muted': '#5B6578', 'background': '#F6F7FA', 'border': '#DDE2EA', 'link': '#2F5BFF',
}

GIALLO = 'var(--olo-color-primary)'
ON_GIALLO = 'var(--olo-color-primary-contrast)'
BLU = 'var(--olo-color-secondary)'
ROSSO = 'var(--olo-color-accent)'
NOTTE = 'var(--olo-color-dark)'
BIANCO = 'var(--olo-color-light)'
TESTO = 'var(--olo-color-text)'
TENUE = 'var(--olo-color-text-muted)'
FONDO = 'var(--olo-color-background)'
GRIGIO = 'var(--olo-color-muted)'
FILO = 'var(--olo-color-border)'
NOTTE2 = 'color-mix(in srgb, var(--olo-color-dark) 82%, var(--olo-color-secondary))'
BIANCO_T = mix(BIANCO, 78)
MONO = 'var(--olo-font-family-mono)'
R = radius(10)

HOME = '{{PAGINA:home}}'
NEGOZIO = '{{PAGINA:negozio}}'
OFFERTE = '{{PAGINA:offerte}}'
ASSISTENZA = '{{PAGINA:assistenza}}'
CONFRONTA = '{{PAGINA:confronta}}'
CARRELLO = '{{PAGINA:carrello}}'
ACCOUNT = '{{PAGINA:account}}'
PHONE = '0523 000 247'
PHONE_TEL = 'tel:+390523000247'
MAIL = 'aiuto@fulmine.example'

V_CUFFIE = 'pexels-video-3946077'    # due mani che reggono cuffie bianche
V_STAND = 'pexels-video-8004701'     # cuffie su un supporto, fondo pastello
V_CORRIERE = 'pexels-video-6868099'  # il furgone e il corriere col pacco
V_SCATOLA = 'pexels-video-10431047'  # una scatola nera su fondo rosso

PHOTOS = ['pexels-4107249.jpg', 'pexels-6958455.jpg', 'pexels-13456097.jpg', 'pexels-33796884.jpg', 'pexels-6969971.jpg',
          'pexels-8939569.jpg', 'pexels-3184451.jpg', 'pexels-28779689.jpg', 'pexels-7561900.jpg', 'pexels-12725450.jpg']
VIDEOS = [V_CUFFIE, V_STAND, V_CORRIERE, V_SCATOLA]
MEDIA = set(PHOTOS) | {v + '.mp4' for v in VIDEOS} | {v + '.jpg' for v in VIDEOS}

CATEGORIE = [('Audio', 'audio'), ('Smartphone e tablet', 'smartphone-tablet'), ('Computer', 'computer'),
             ('Casa smart', 'casa-smart'), ('Foto e droni', 'foto-droni'), ('Gaming', 'gaming'),
             ('Indossabili', 'indossabili'), ('TV', 'tv')]


def img(name):
    assert name in MEDIA, name
    return lib(name)


def cat_url(slug):
    return NEGOZIO + '?product_cat=' + slug


def video_bg(name, overlay=45, color=NOTTE, position='center center'):
    return {'type': 'video', 'video_url': img(name + '.mp4'), 'video_poster': img(name + '.jpg'), 'video_fit': 'cover',
            'image_position': position, 'overlay_color': color, 'overlay_opacity': overlay}


def photo_bg(name, overlay=45, color=NOTTE, position='center center'):
    return {'type': 'image', 'image_url': img(name), 'image_size': 'cover', 'image_position': position,
            'overlay_color': color, 'overlay_opacity': overlay}


# ── Pezzi ricorrenti ──────────────────────────────────────────────────────────

def titolo(t, testo, size=30, color=TESTO, tag='h2', pad_bottom=6, align='left', sizes=None, weight='800'):
    o = {'heading': testo, 'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': False, 'subtitle': '',
         'heading_font_size': size, 'heading_weight': weight, 'heading_color': color, 'heading_font': 'heading',
         'heading_letter_spacing': '-0.5'}
    if sizes:
        o.update({'heading_font_size_tablet': str(sizes[0]), 'heading_font_size_mobile': str(sizes[1])})
    elif size >= 34:
        o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(24, int(size * 0.6)))})
    return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def label(t, s, color=BLU, pad_bottom=8, align='left'):
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'heading_uppercase': True, 'heading_font_size': 12, 'heading_weight': '700',
                                  'heading_color': color, 'heading_font': 'body', 'heading_letter_spacing': '1'},
                     style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def text(t, html, size=15, color=TENUE, pad=(0, 0, 0, 0), align='left'):
    return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.65, 'text_color': color, 'text_align': align,
                                    'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})


def button(t, label_, url, kind='giallo', align='left', pad_top=10, icon=''):
    if kind == 'giallo':
        o = {'bg_color': GIALLO, 'text_color': ON_GIALLO, 'hover_bg_color': BIANCO, 'hover_text_color': NOTTE}
    elif kind == 'blu':
        o = {'bg_color': BLU, 'text_color': BIANCO, 'hover_bg_color': NOTTE, 'hover_text_color': BIANCO}
    else:
        col = BIANCO if kind == 'filo-chiaro' else TESTO
        o = {'bg_color': 'transparent', 'text_color': col, 'hover_bg_color': col, 'hover_text_color': NOTTE if kind == 'filo-chiaro' else BIANCO,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
             'shadow_color': mix(col, 45), 'shadow_inset': True}
    o.update({'text': label_, 'url': url, 'alignment': align, 'border_radius': 10, 'font_size': 15, 'font_weight': '700',
              'text_transform': 'none', 'letter_spacing': '0'})
    if icon:
        o.update({'icon': icon, 'icon_position': 'after', 'icon_spacing': 8, 'icon_size': 16})
    return t.classic('button', o, style={'padding_top': pad_top})


def link(t, label_, url, align='right'):
    """Un collegamento di testo blu, come «Vedi tutte»."""
    return t.classic('button', {'text': label_, 'url': url, 'alignment': align, 'bg_color': 'transparent', 'text_color': BLU,
                                'hover_bg_color': 'transparent', 'hover_text_color': NOTTE, 'border_radius': 0,
                                'font_size': 15, 'font_weight': '600', 'text_transform': 'none', 'letter_spacing': '0',
                                'padding_x': 0, 'padding_y': 4, 'tile_padding': {'top': 4, 'right': 0, 'bottom': 4, 'left': 0},
                                'icon': 'arrow-right', 'icon_position': 'after',
                                'icon_spacing': 6, 'icon_size': 15},
                     style={'padding_top': 0})


def testata_sezione(t, eyebrow, testo, url='', link_testo='Vedi tutti', extra=None):
    sinistra = ([label(t, eyebrow)] if eyebrow else []) + [titolo(t, testo, size=30, sizes=(28, 24))] + (extra or [])
    destra = [link(t, link_testo, url)] if url else []
    return t.row([t.col(sinistra, wm='2-3', ws='2-3', width_default='2-3'), t.col(destra, wm='1-3', ws='1-3', width_default='1-3')],
                 layout='custom', custom='70,30', valign='end', anim=None, stack=False, style={'margin_bottom': 18})


def prodotti(t, **kw):
    """La griglia dei prodotti di WooCommerce con tutte le parti della card di un negozio grande."""
    o = {
        'posts_per_page': 8, 'columns': 4, 'columns_tablet': 3, 'columns_mobile': 2, 'orderby': 'date', 'order': 'DESC',
        'category': '', 'tag': '', 'on_sale': False, 'featured': False,
        'show_image': True, 'show_title': True, 'show_price': True, 'show_rating': True, 'show_rating_count': True,
        'show_add_to_cart': True, 'show_badge': True, 'show_compare': False, 'show_wishlist': True,
        'mostra_risparmio': True, 'mostra_scorta': True, 'marca_attributo': 'marca',
        'consegna_testo': 'Consegna gratuita domani', 'testo_evidenza': 'Scelto da Fulmine', 'titolo_righe': 2,
        'image_ratio': '1-1', 'gap': 16, 'card_style': 'border', 'hover_effect': 'swap', 'card_bg': BIANCO,
        'card_radius': {'tl': 12, 'tr': 12, 'br': 12, 'bl': 12}, 'card_padding': {'top': 14, 'right': 14, 'bottom': 16, 'left': 14},
        'title_color': TESTO, 'price_color': TESTO, 'sale_color': ROSSO, 'button_bg': GIALLO, 'button_color': ON_GIALLO,
        'badge_bg': ROSSO, 'pagination': False, 'layout': 'grid', 'preset': 'custom',
        'carousel_autoplay': False, 'carousel_loop': False, 'carousel_arrows': True, 'carousel_dots': False, 'carousel_speed': 4000,
        'shadow': 'none',
    }
    o.update(kw)
    return t.bare('woo_products', o)


def anteprima(t):
    """La Quick View: aggiunge «Anteprima veloce» sulle foto delle card della pagina."""
    return t.bare('woo_quickview', {
        'button_text': 'Anteprima veloce', 'button_style': 'filled', 'show_gallery': True, 'show_add_to_cart': True,
        'show_price': True, 'show_rating': True, 'show_excerpt': True, 'show_meta': False, 'modal_width': 920,
        'image_width': 50, 'accent_color': BLU, 'text_color': TESTO,
    })


def conto_alla_rovescia(t, size=40, color=BIANCO, chiaro=True):
    return t.classic('countdown', {
        'preset': 'custom', 'countdown_style': 'boxes', 'countdown_type': 'evergreen', 'evergreen_hours': 23,
        'evergreen_minutes': 59, 'evergreen_loop': True, 'expire_action': 'none', 'display_mode': 'inline', 'alignment': 'left',
        'show_days': False, 'show_hours': True, 'show_minutes': True, 'show_seconds': True,
        'label_hours': 'ore', 'label_minutes': 'min', 'label_seconds': 'sec', 'separator': ':',
        'bg_color': 'transparent', 'text_color': color, 'accent_color': GIALLO, 'separator_color': mix(color, 50),
        'number_font_size': size, 'number_font_size_tablet': int(size * 0.9), 'number_font_size_mobile': int(size * 0.8),
        'number_font_weight': '800', 'label_font_size': 11, 'label_font_weight': '600', 'separator_font_size': int(size * 0.7),
        'item_min_width': int(size * 1.7), 'item_min_width_tablet': int(size * 1.5), 'item_min_width_mobile': int(size * 1.4),
        'item_bg_color': mix(BIANCO, 10) if chiaro else BIANCO, 'item_radius': 10, 'item_padding': 10,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def promesse(t, chiaro=False):
    col = BIANCO if chiaro else TESTO
    return t.classic('trust-strip', {
        'items': [{'icon': i, 'icon_color': GIALLO if chiaro else BLU, 'text': s, 'badge': ''} for i, s in [
            ('truck', 'Consegna gratuita da 29 €, domani se ordini entro le 18'), ('rotate-ccw', 'Reso gratuito in 30 giorni'),
            ('credit-card', 'Paghi anche in 3 rate senza interessi'), ('headphones', 'Assistenza in italiano, 7 giorni su 7')]],
        'text_color': col, 'text_size': 14, 'font_family': 'body', 'align': 'center', 'flow': 'wrap', 'gap': 28,
        'variant': 'inline', 'icon_size': 18, 'separator_char': '', 'separator_color': FILO,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})


def faq(t, pairs, prefix):
    panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
              for i, (q, a) in enumerate(pairs)]
    return t.classic('accordion', {
        'panels': panels, 'default_open': 'first', 'icon_style': 'chevron', 'icon_position': 'after',
        'header_bg': BIANCO, 'header_bg_active': BIANCO, 'content_bg': BIANCO,
        'header_text_color': TESTO, 'header_text_color_active': BLU, 'header_font_size': 17, 'header_font_weight': '700',
        'header_font_family': 'var(--olo-font-family-body)', 'content_font_size': 15, 'text_color': TENUE,
        'icon_color': BLU, 'faq_schema': True,
        'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': FILO},
        'header_padding': {'top': 18, 'right': 20, 'bottom': 18, 'left': 20},
        'content_padding': {'top': 0, 'right': 48, 'bottom': 20, 'left': 20},
    }, style={})


def campo(tipo, etichetta, nome, larghezza='1-2', obbligatorio=False, opzioni='', segnaposto=''):
    return {'field_type': tipo, 'label': etichetta, 'name': nome, 'placeholder': segnaposto, 'required': obbligatorio,
            'width': larghezza, 'options': opzioni, 'icon': ''}


def modulo(t, campi, oggetto, invio, grazie, scuro=False):
    return t.classic('form', {
        'fields': campi, 'email_subject': oggetto, 'submit_text': invio, 'submit_icon': '', 'success_message': grazie,
        'submit_full_width': False, 'submit_alignment': 'left', 'label_color': BIANCO if scuro else TESTO, 'label_size': '13',
        'label_weight': '600', 'label_transform': 'none', 'label_letter_spacing': '0', 'input_bg': BIANCO, 'input_color': TESTO,
        'input_border_color': FILO, 'input_border_width': '1', 'input_border_style': 'solid', 'input_radius': 10,
        'input_focus_border': BLU, 'input_focus_shadow': False, 'gap': '14', 'submit_bg': GIALLO, 'submit_color': ON_GIALLO,
        'submit_radius': 10, 'submit_padding_x': '26', 'submit_padding_y': '14', 'submit_font_size': '15',
        'submit_font_weight': '700', 'submit_text_transform': 'none', 'submit_letter_spacing': '0',
        'submit_hover_bg': BIANCO if scuro else NOTTE, 'privacy_checkbox': True,
    }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
              'border_radius': 0})


def banner(t, eyebrow, titolo_, accento, sotto, cta, url, bg, radius_=18, pad=64):
    return t.classic('cta-banner', {
        'headline': titolo_, 'headline_accent': accento, 'headline_accent_italic': False, 'subtitle': sotto,
        'cta_text': cta, 'cta_url': url, 'cta_target': '_self', 'cta2_text': '', 'cta2_url': '',
        'bg': bg, 'text_color': BIANCO, 'accent_color': GIALLO, 'subtitle_color': BIANCO_T,
        'cta_bg': GIALLO, 'cta_bg_hover': BIANCO, 'cta_color': ON_GIALLO, 'cta_color_hover': NOTTE,
        'cta_radius': {'tl': 10, 'tr': 10, 'br': 10, 'bl': 10}, 'cta_radius_hover': {'tl': 10, 'tr': 10, 'br': 10, 'bl': 10},
        'cta_size': 15, 'cta_padding': {'top': 14, 'right': 24, 'bottom': 14, 'left': 24},
        'headline_font_family': 'heading', 'headline_size': 38, 'headline_weight': '800', 'subtitle_size': 16,
        'layout': 'stack', 'gap': 18, 'vertical_align': 'end',
        'banner_radius': {'tl': radius_, 'tr': radius_, 'br': radius_, 'bl': radius_},
        'banner_radius_hover': {'tl': radius_, 'tr': radius_, 'br': radius_, 'bl': radius_},
        'banner_padding': {'top': pad + 120, 'right': 36, 'bottom': pad - 20, 'left': 36},
    })


# ══════════════════════════════════════════════════════════════════════════════
#  HEADER · FOOTER
# ══════════════════════════════════════════════════════════════════════════════

def build_header():
    t = Tpl(ID, 'header')
    # Due fasce alte: dal tablet orizzontale in su logo, ricerca, preferiti e carrello in una riga; sotto i 960 px
    # logo, preferiti e carrello in una riga e la ricerca a tutta larghezza sotto. Le colonne non si riordinano per
    # dispositivo: ogni tile c'è due volte e si nasconde dove non serve.
    SOLO_GRANDE = {'visible_tablet': False, 'visible_mobile_landscape': False, 'visible_mobile': False}
    SOLO_PICCOLO = {'visible_desktop': False, 'visible_tablet_landscape': False}

    def logo(adv, size=30):
        return t.classic('button', {'text': 'fulmine', 'url': HOME, 'alignment': 'left', 'bg_color': 'transparent',
                                    'text_color': BIANCO, 'hover_bg_color': 'transparent', 'hover_text_color': GIALLO,
                                    'border_radius': 0, 'font_size': size, 'font_weight': '800', 'font_family': 'heading',
                                    'letter_spacing': '-1', 'text_transform': 'none', 'padding_x': 0, 'padding_y': 2, 'icon': 'zap',
                                    'icon_position': 'left', 'icon_spacing': 4, 'icon_size': int(size * 0.85), 'icon_color': GIALLO},
                         style={'padding_top': 0}, advanced=dict(adv))

    def ricerca(adv):
        return t.bare('livesearch', {
            'preset': 'custom', 'mode': 'expanded', 'placeholder': 'Cerca cuffie, telefoni, droni…', 'min_chars': 2,
            'debounce_ms': 200, 'max_results': 6, 'results_columns': 1, 'show_all_url': '', 'show_thumbnail': True,
            'show_excerpt': False, 'title_only': False, 'no_results_text': 'Nessun prodotto: prova con la marca o la categoria.',
            'post_types': 'product', 'input_bg': BIANCO, 'input_color': TESTO, 'icon_color': NOTTE, 'input_font_size': 15,
            'input_height': 46, 'input_border_color': GIALLO, 'input_border_radius': 10, 'focus_border_color': GIALLO,
            'results_bg': BIANCO, 'results_border_color': FILO, 'results_border_radius': 12, 'item_hover_bg': GRIGIO,
            'title_color': TESTO, 'excerpt_color': TENUE, 'results_max_height': 460, 'thumb_width': 48, 'thumb_height': 48,
            'thumb_radius': 8, 'animated_placeholder': True,
            'placeholder_words': 'cuffie con cancellazione del rumore\nun drone sotto i 250 g\nuno smartwatch col GPS\nun portatile leggero',
        }, advanced=dict(adv))

    def icone(adv):
        conto = t.classic('icon', {'icon': 'user', 'link_url': ACCOUNT, 'link_target': '_self', 'link_label': 'Il mio account',
                                   'size': 26, 'color': BIANCO, 'hover_animation': 'none', 'view': 'default', 'alignment': 'center'},
                          style={'margin_bottom': 0, 'padding_top': 6}, advanced=dict(adv))
        cuore = t.bare('woo_wishlist', {'icon': 'heart', 'style': 'icon', 'show_count': True, 'show_grid': False, 'icon_size': 24,
                                        'icon_color': BIANCO, 'badge_bg': GIALLO, 'badge_color': ON_GIALLO}, advanced=dict(adv))
        carrello = t.bare('woo_minicart', {'style': 'icon', 'icon': 'cart', 'show_count': True, 'show_total': True, 'dropdown': True,
                                           'icon_size': 26, 'text_color': BIANCO, 'icon_color': BIANCO, 'badge_bg': GIALLO,
                                           'badge_color': ON_GIALLO, 'dropdown_width': 360}, advanced=dict(adv))
        return t.row([t.col([conto], wm='1-3', ws='1-3', width_default='1-3'),
                      t.col([cuore], wm='1-3', ws='1-3', width_default='1-3'),
                      t.col([carrello], wm='1-3', ws='1-3', width_default='1-3')],
                     layout='33-33-33', valign='center', anim=None, stack=False, gap=8)

    alto = t.row([
        t.col([logo(SOLO_GRANDE)], wm='1-5', ws='1-5'),
        t.col([ricerca(SOLO_GRANDE)], wm='3-5', ws='3-5'),
        t.col([icone(SOLO_GRANDE)], wm='1-5', ws='1-5'),
    ], layout='custom', custom='18,62,20', gap=24, valign='center', anim=None, stack=False)
    piccolo = t.row([
        t.col([logo(SOLO_PICCOLO, size=26)], wm='1-2', ws='1-2', width_default='1-2'),
        t.col([t.row([t.col([], wm='1-3', ws='1-3', width_default='1-3'), t.col([icone(SOLO_PICCOLO)], wm='2-3', ws='2-3', width_default='2-3')],
                     layout='custom', custom='33.33,66.67', anim=None, stack=False, valign='center')],
              wm='1-2', ws='1-2', width_default='1-2'),
    ], layout='50-50', gap=12, valign='center', anim=None, stack=False)
    sotto = t.one([ricerca(SOLO_PICCOLO)], anim=None, style={'margin_top': 10})
    menu = t.classic('megamenu', {
        'menu_id': 'auto', 'logo_text': '', 'logo_mark': '', 'logo_dot': False, 'logo_position': 'left',
        'layout': 'left', 'header_mode': 'classic', 'sticky': False,
        'nav_bg': 'transparent', 'text_color': BIANCO, 'hover_color': GIALLO, 'active_color': GIALLO,
        'font_size': '14', 'font_weight': '600', 'text_transform': 'none', 'letter_spacing': '0', 'item_gap': '22',
        'bar_width': 'wide', 'nav_height': '46', 'bar_padding': {'top': 0, 'right': 24, 'bottom': 0, 'left': 24},
        'hover_effect': 'underline', 'hover_effect_color': GIALLO, 'button_mode': 'none',
        'social_in_navbar': False, 'social_in_mobile': False, 'mobile_style': 'fullscreen', 'mobile_bg': NOTTE,
        'mobile_text_color': BIANCO, 'mobile_accent_color': GIALLO, 'hamburger_style': 'minimal', 'hamburger_color': BIANCO,
        'mobile_font_size': '26', 'mobile_link_size': '26', 'mobile_link_font': 'heading', 'mobile_link_weight': '700',
        'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''},
        'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
    })
    return [t.section([alto, piccolo, sotto], bg=solid(NOTTE), width='large', padding='remove-vertical',
                      style={'padding_top': 14, 'padding_bottom': 14}),
            t.section([t.one([menu], anim=None)], bg=solid(NOTTE2), width='fullbleed', padding='remove-vertical')]


def build_footer():
    t = Tpl(ID, 'footer')

    def nav(items):
        return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in items], 'style': 'default',
                                 'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                 'link_color': BIANCO_T, 'link_hover_color': GIALLO, 'active_color': GIALLO,
                                 'font_size': '14', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
    marchio = t.classic('button', {'text': 'fulmine', 'url': HOME, 'alignment': 'left', 'bg_color': 'transparent', 'text_color': BIANCO,
                                   'hover_bg_color': 'transparent', 'hover_text_color': GIALLO, 'border_radius': 0,
                                   'font_size': 34, 'font_weight': '800', 'font_family': 'heading', 'letter_spacing': '-1',
                                   'text_transform': 'none', 'padding_x': 0, 'padding_y': 0, 'icon': 'zap', 'icon_position': 'left',
                                   'icon_spacing': 4, 'icon_size': 28, 'icon_color': GIALLO}, style={'padding_top': 0})
    claim = text(t, '<p>Elettronica scelta e provata, spedita da Piacenza. Ordini entro le 18, arriva domani.</p>', size=14,
                 color=BIANCO_T, pad=(10, 0, 14, 0))
    contatti = t.classic('iconlist', {
        'items': [{'icon': 'phone', 'text': PHONE + ' · tutti i giorni 8 — 22', 'link': PHONE_TEL, 'color': ''},
                  {'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''}],
        'icon_color': GIALLO, 'icon_size': '16', 'text_color': BIANCO, 'text_size': '14', 'gap': '10', 'icon_shape': 'none',
        'icon_bg_color': '', 'divider': False, 'layout': 'vertical'}, style={})
    cols = t.row([
        t.col([marchio, claim, contatti], wm='1-3', ws='1-1'),
        t.col([label(t, 'Comprare', color=GIALLO), nav([('Tutti i prodotti', NEGOZIO), ('Offerte lampo', OFFERTE),
                                                         ('Confronta', CONFRONTA), ('Il carrello', CARRELLO)])],
              wm='2-9', ws='1-2', width_default='1-2'),
        t.col([label(t, 'Aiuto', color=GIALLO), nav([('Spedizioni', ASSISTENZA + '#spedizioni'), ('Resi e rimborsi', ASSISTENZA + '#resi'),
                                                      ('Traccia l’ordine', ASSISTENZA + '#traccia'), ('Scrivici', ASSISTENZA + '#scrivici')])],
              wm='2-9', ws='1-2', width_default='1-2'),
        t.col([label(t, 'Categorie', color=GIALLO), nav([(n, cat_url(s)) for n, s in CATEGORIE[:6]])], wm='2-9', ws='1-1'),
    ], layout='custom', custom='34,22,22,22', gap=40, valign='start', anim=None)
    legal = text(t, '<p>© 2026 Fulmine s.r.l. · Piacenza · P. IVA 00000000000 · <a href="/privacy-policy/">Privacy</a> · '
                    'Prezzi IVA inclusa</p>', size=12, color=BIANCO_T)
    pagamenti = t.classic('trust-strip', {
        'items': [{'icon': i, 'icon_color': BIANCO_T, 'text': s, 'badge': ''} for i, s in [
            ('credit-card', 'Carte'), ('wallet', 'Portafogli digitali'), ('landmark', 'Bonifico'), ('shield-check', 'Pagamenti protetti')]],
        'text_color': BIANCO_T, 'text_size': 12, 'font_family': 'body', 'align': 'right', 'flow': 'wrap', 'gap': 18,
        'variant': 'inline', 'icon_size': 15, 'separator_char': '', 'separator_color': FILO,
    }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
    totop = t.classic('totop', {'icon_color': NOTTE, 'posizione': 'fisso', 'style': 'default'})
    fondo = t.row([t.col([legal, totop], wm='1-2', ws='1-1'), t.col([pagamenti], wm='1-2', ws='1-1')],
                  layout='50-50', valign='center', anim=None,
                  style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': mix(BIANCO, 14)},
                         'padding_top': 18, 'margin_top': 36})
    return [t.section([t.one([promesse(t, chiaro=True)], anim=None)], bg=solid(NOTTE2), padding='small'),
            t.section([cols, fondo], bg=solid(NOTTE), padding='large')]


# ══════════════════════════════════════════════════════════════════════════════
#  HOME
# ══════════════════════════════════════════════════════════════════════════════

def categorie(t, columns=8):
    return t.bare('woo_categories', {
        'columns': columns, 'columns_tablet': 4, 'columns_mobile': 2, 'show_image': True, 'show_count': True,
        'show_description': False, 'hide_empty': True, 'orderby': 'name', 'parent_only': True, 'overlay': False,
        'text_color': TESTO, 'title_tag': 'h3', 'gap': 18, 'image_ratio': '1-1', 'border_radius': 999, 'hover_effect': 'zoom',
        'posizione_testo': 'sotto', 'scorri_mobile': True, 'pagina_negozio': NEGOZIO,
    })


def build_home():
    t = Tpl(ID, 'home')
    out = []
    hero = t.classic('hero', {
        'preset': 'custom', 'eyebrow_text': 'Elettronica scelta e provata · da Piacenza', 'eyebrow_dot': True,
        'title': 'Ordini entro le 18,<br>arriva <em>domani.</em>',
        'subtitle': 'Duemila prodotti con la consegna gratuita da 29 €, il reso in 30 giorni e l’assistenza in italiano. '
                    'Ogni prodotto lo proviamo prima di metterlo in vendita.',
        'media_bg': {'type': 'video', 'video_url': img(V_STAND + '.mp4'), 'video_poster': img(V_STAND + '.jpg'),
                     'video_size': 'cover', 'video_position': 'center center'},
        'overlay_color': NOTTE, 'overlay_top': 0.15, 'overlay_bottom': 0.75, 'overlay_sides': True, 'overlay_direction': 'left',
        'glow_on': False, 'frame_on': False, 'watermark_text': '',
        'accent': GIALLO, 'text_color': BIANCO, 'title_color': BIANCO, 'subtitle_color': BIANCO_T,
        'title_tag': 'h1', 'title_font_family': 'heading', 'title_font_size': 80, 'title_font_weight': '800',
        'title_line_height': '1', 'title_letter_spacing': '-2', 'title_text_transform': 'none',
        'subtitle_font_size': 17, 'subtitle_max_width': 520,
        'min_height': '72vh', 'content_max_width': 1280, 'vertical_align': 'center', 'horizontal_align': 'center',
        'text_align': 'left', 'tile_padding': {'top': 80, 'right': 32, 'bottom': 80, 'left': 32},
        'cta_text': 'Le offerte lampo', 'cta_url': OFFERTE, 'cta_bg_color': GIALLO, 'cta_text_color': ON_GIALLO,
        'cta_radius': R, 'cta_size': '16', 'cta_style': 'filled', 'cta_font_weight': '700', 'cta_text_transform': 'none',
        'cta_letter_spacing': '0',
        'cta2_text': 'Tutti i prodotti', 'cta2_url': NEGOZIO, 'cta2_text_color': BIANCO, 'cta2_style': 'ghost',
        'meta_text': '', 'scroll_hint': '', 'scene_depth': 'none', 'module': '',
    })
    out.append(t.section([t.one([hero], anim=None)], width='fullbleed', padding='remove-vertical'))

    out.append(t.section([
        t.one([titolo(t, 'Le categorie', size=26, sizes=(24, 22), pad_bottom=18)], anim=None),
        t.one([categorie(t)]),
    ], bg=solid(BIANCO), padding='default', width='large', style={'padding_top': 44, 'padding_bottom': 44}))

    out.append(t.section([
        t.row([t.col([label(t, 'Finiscono tra', color=GIALLO), titolo(t, 'Offerte lampo', size=40, color=BIANCO, sizes=(34, 30)),
                      text(t, '<p>Ogni giorno a mezzanotte nuovi sconti, finché ci sono i pezzi.</p>', color=BIANCO_T)],
                     wm='1-2', ws='1-1'),
               t.col([conto_alla_rovescia(t, size=40), link_chiaro(t, 'Tutte le offerte', OFFERTE)], wm='1-2', ws='1-1')],
              layout='50-50', gap=24, valign='end', anim=None, style={'margin_bottom': 24}),
        t.one([anteprima(t), prodotti(t, on_sale=True, posts_per_page=10, columns=5, columns_tablet=3, layout='carousel',
                                      orderby='popularity', carousel_arrows=True)]),
    ], bg=solid(NOTTE), padding='default', width='large', html_id='offerte', style={'padding_top': 48, 'padding_bottom': 52}))

    out.append(t.section([
        t.row([t.col([banner(t, '', 'La settimana', 'dell’audio.', 'Cuffie, auricolari e casse fino al −30%. Le abbiamo provate in metro, '
                             'in ufficio e sotto la doccia.', 'Scopri l’audio', cat_url('audio'), video_bg(V_CUFFIE, overlay=35))],
                     wm='1-2', ws='1-1'),
               t.col([banner(t, '', 'La casa che si pulisce', 'da sola.', 'Robot, assistenti e luci: la casa smart da 69 €, '
                             'configurata in cinque minuti.', 'Casa smart', cat_url('casa-smart'),
                             photo_bg('pexels-4107249.jpg', overlay=42, position='center 40%'))], wm='1-2', ws='1-1')],
              layout='50-50', gap=20, valign='stretch'),
    ], padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 24}))

    out.append(t.section([
        testata_sezione(t, 'I più venduti', 'Quelli che ricomprano tutti', NEGOZIO),
        t.one([prodotti(t, orderby='popularity', posts_per_page=8)]),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 40}))

    numeri = t.classic('statstrip', {
        'items': [{'value': '24 h', 'label': 'dal clic al portone'}, {'value': '98,7%', 'label': 'pacchi arrivati puntuali'},
                  {'value': '2.000', 'label': 'prodotti provati da noi'}, {'value': '4,8/5', 'label': 'il voto dei clienti'}],
        'columns': 4, 'columns_tablet_landscape': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'items_gap': 0,
        'band_padding_y': 24, 'show_dividers': True, 'divider_color': mix(BIANCO, 20), 'band_border': False,
        'value_font_family': 'heading', 'value_color': GIALLO, 'value_size': 48, 'value_weight': '800',
        'label_color': BIANCO_T, 'label_size': 13, 'label_uppercase': False, 'label_weight': '500', 'align': 'left',
        'mono_font_family': 'body',
    })
    out.append(t.section([
        t.row([t.col([label(t, 'La consegna', color=GIALLO),
                      titolo(t, 'Dal magazzino di Piacenza al tuo portone, in 24 ore.', size=48, color=BIANCO, tag='h2'),
                      text(t, '<p>Imballiamo fino alle 18 e il corriere passa alle 19. Ti scriviamo quando il pacco parte, '
                              'quando è in zona e quando lo consegnano.</p>', size=17, color=BIANCO_T)], wm='3-5', ws='1-1'),
               t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', valign='end'),
        t.one([numeri], style={'margin_top': 40}),
    ], bg=video_bg(V_CORRIERE, overlay=62), padding='large', style={'padding_top': 140, 'padding_bottom': 60}))

    out.append(t.section([
        testata_sezione(t, 'Scelti da Fulmine', 'Quelli che regaleremmo', NEGOZIO),
        t.one([prodotti(t, featured=True, posts_per_page=10, columns=5, layout='carousel', orderby='rating')]),
    ], padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 40}))

    out.append(t.section([
        t.row([t.col([t.classic('image', {'image_url': img('pexels-6958455.jpg'), 'alt_text': 'Una mano apre una scatola di cartone piena di carta',
                                          'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/3',
                                          'height': '', 'object_fit': 'cover', 'object_position': 'center center',
                                          'border_radius': radius(18), 'lightbox': False, 'shadow': 'none',
                                          'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}})],
                     wm='1-2', ws='1-1'),
               t.col([label(t, 'Provati da noi'),
                      titolo(t, 'Prima di venderlo, lo usiamo una settimana.', size=40, tag='h2'),
                      text(t, '<p>Ogni prodotto nuovo passa una settimana sulle scrivanie del magazzino. Se non ci convince, non '
                              'entra in negozio: per questo ne abbiamo duemila e non duecentomila.</p>'),
                      t.classic('iconlist', {
                          'items': [{'icon': 'check', 'text': s, 'link': '', 'color': ''} for s in [
                              'Garanzia italiana di 2 anni, anche sui prodotti in offerta', 'Imballi in cartone riciclato, senza plastica',
                              'Il ritiro del vecchio apparecchio, gratis, quando ti portiamo il nuovo']],
                          'icon_color': BLU, 'icon_size': '18', 'text_color': TESTO, 'text_size': '15', 'gap': '10',
                          'icon_shape': 'circle', 'icon_bg_color': mix(BLU, 12), 'divider': False, 'layout': 'vertical'}, style={}),
                      button(t, 'Come lavoriamo', ASSISTENZA, kind='blu', pad_top=18)], wm='1-2', ws='1-1')],
              layout='50-50', gap=56, valign='center'),
    ], bg=solid(BIANCO), padding='large', width='large'))

    out.append(t.section([
        t.row([t.col([label(t, 'Il bollettino', color=GIALLO),
                      titolo(t, 'Le offerte lampo un’ora prima degli altri.', size=34, color=BIANCO, tag='h2', sizes=(30, 26)),
                      text(t, '<p>Una mail al giorno alle 23, niente altro. Ti cancelli con un clic.</p>', color=BIANCO_T)],
                     wm='1-2', ws='1-1'),
               t.col([modulo(t, [campo('email', 'La tua email', 'email', larghezza='1-1', obbligatorio=True,
                                       segnaposto='nome@esempio.it')], 'Fulmine — bollettino', 'Iscrivimi',
                             'Fatto: la prima mail arriva stasera alle 23.', scuro=True)], wm='1-2', ws='1-1')],
              layout='50-50', gap=48, valign='center'),
    ], bg=photo_bg('pexels-28779689.jpg', overlay=78, position='center center'), padding='large', width='large',
        style={'padding_top': 70, 'padding_bottom': 70}))
    return out


def link_chiaro(t, label_, url):
    return t.classic('button', {'text': label_, 'url': url, 'alignment': 'left', 'bg_color': 'transparent', 'text_color': GIALLO,
                                'hover_bg_color': 'transparent', 'hover_text_color': BIANCO, 'border_radius': 0,
                                'font_size': 15, 'font_weight': '600', 'text_transform': 'none', 'letter_spacing': '0',
                                'padding_x': 0, 'padding_y': 4, 'tile_padding': {'top': 4, 'right': 0, 'bottom': 4, 'left': 0},
                                'icon': 'arrow-right', 'icon_position': 'after',
                                'icon_spacing': 6, 'icon_size': 15}, style={'padding_top': 14})


# ══════════════════════════════════════════════════════════════════════════════
#  NEGOZIO · OFFERTE
# ══════════════════════════════════════════════════════════════════════════════

def intestazione(t, eyebrow, testo, sotto, scuro=False, extra=None):
    col, col2 = (BIANCO, BIANCO_T) if scuro else (TESTO, TENUE)
    return t.section([
        t.row([t.col([label(t, eyebrow, color=GIALLO if scuro else BLU), titolo(t, testo, size=44, color=col, tag='h1', sizes=(36, 30)),
                      text(t, '<p>' + sotto + '</p>', size=16, color=col2)], wm='3-5', ws='1-1'),
               t.col(extra or [], wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=32, valign='end', anim=None),
    ], bg=solid(NOTTE if scuro else BIANCO), padding='default', width='large', style={'padding_top': 44, 'padding_bottom': 36})


def build_negozio():
    t = Tpl(ID, 'negozio')
    filtro = t.bare('woo_product_filter', {
        'show_price': True, 'show_categories': True, 'show_attributes': True, 'show_stock': True, 'show_active': True,
        'price_min': 0, 'price_max': 2500, 'price_step': 50, 'attributes': 'pa_marca', 'collapsed': False,
        'heading_color': TESTO, 'text_color': TESTO, 'accent_color': BLU, 'bg_color': BIANCO, 'border_color': FILO,
        'border_radius': 12, 'button_text': 'Mostra i prodotti', 'reset_text': 'Azzera i filtri',
    })
    out = [intestazione(t, 'Il negozio', 'Tutti i prodotti', 'Filtra per prezzo, categoria e marca. Il cuore li mette nei '
                                                             'preferiti, «Confronta» li mette fianco a fianco.')]
    out.append(t.section([
        t.row([t.col([filtro, button(t, 'Vai al confronto', CONFRONTA, kind='filo', pad_top=16)], wm='1-4', ws='1-1'),
               t.col([anteprima(t), prodotti(t, posts_per_page=30, columns=3, columns_tablet=2, show_compare=True)],
                     wm='3-4', ws='1-1')],
              layout='custom', custom='25,75', gap=28, valign='start', anim=None),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 64}))
    return out


def build_offerte():
    t = Tpl(ID, 'offerte')
    out = [intestazione(t, 'Finiscono tra', 'Offerte lampo', 'Ogni giorno a mezzanotte nuovi sconti, finché ci sono i pezzi. '
                                                             'Il prezzo barrato è quello che avevamo ieri.', scuro=True,
                        extra=[conto_alla_rovescia(t, size=52)])]
    out.append(t.section([
        t.one([anteprima(t), prodotti(t, on_sale=True, posts_per_page=20, orderby='popularity')]),
    ], padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 48}))
    out.append(t.section([
        t.row([t.col([banner(t, '', 'Usato ricondizionato', 'come nuovo.', 'Telefoni e portatili controllati in 40 punti, con la '
                             'garanzia di un anno. Da questo autunno.', 'Avvisami', ASSISTENZA + '#scrivici',
                             video_bg(V_SCATOLA, overlay=30))], wm='1-1', ws='1-1')]),
    ], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  SCHEDA PRODOTTO
# ══════════════════════════════════════════════════════════════════════════════

def build_prodotto():
    t = Tpl(ID, 'prodotto')
    briciole = t.bare('woo_breadcrumbs', {'separator': '›', 'text_color': TENUE, 'link_color': BLU, 'font_size': 13,
                                          'alignment': 'left'})
    galleria = t.bare('woo_product_gallery_slider', {
        'show_thumbnails': True, 'thumbnail_position': 'left', 'enable_zoom': True, 'enable_lightbox': True,
        'main_height': '560px', 'max_width': 0, 'thumbnail_size': 76, 'thumbnail_gap': 10, 'arrows': True, 'main_bg': BIANCO,
        'thumbnail_border': FILO, 'thumbnail_active_border': BLU, 'arrow_color': TESTO, 'arrow_bg': BIANCO,
        'show_badge': False, 'badge_bg': ROSSO,
    })
    navigazione = t.bare('woo_product_navigation', {'show_thumbnail': True, 'show_label': True, 'label_prev': 'Precedente',
                                                    'label_next': 'Successivo', 'separator_style': 'line', 'text_color': TENUE,
                                                    'hover_color': BLU})
    avvisi = t.bare('woo_notices', {'show_success': True, 'show_error': True, 'show_info': True, 'border_radius': 10, 'font_size': 14})
    destra = [
        t.bare('woo_product_title', {'tag': 'h1', 'text_align': 'left', 'color': TESTO, 'font_size': 28, 'font_weight': '800',
                                     'line_height': '1.2', 'link_to_product': False}),
        t.bare('woo_rating', {'show_count': True, 'show_average': True, 'star_color': GIALLO, 'empty_star_color': FILO,
                              'text_color': BLU, 'star_size': 18, 'text_size': 14}),
        t.bare('woo_sale_badge', {'badge_text': 'auto', 'badge_bg': ROSSO, 'badge_color': BIANCO, 'badge_shape': 'rounded',
                                  'position': 'top-left', 'font_size': 13, 'font_weight': '800'}, style={'margin_top': 6}),
        t.bare('woo_price', {'show_regular': True, 'show_sale': True, 'show_suffix': False, 'price_color': TESTO,
                             'sale_color': ROSSO, 'regular_color': TENUE, 'font_size': 32, 'font_weight': '800',
                             'text_align': 'left'}),
        t.bare('woo_product_description', {'content_type': 'short', 'text_color': TESTO, 'font_size': 15, 'line_height': '1.6',
                                           'text_align': 'left', 'max_lines': 0}),
        t.bare('woo_product_stock', {'show_quantity': True, 'show_icon': True, 'in_stock_color': 'var(--olo-color-success, #15803d)',
                                     'out_of_stock_color': ROSSO, 'low_stock_color': 'var(--olo-color-warning, #b45309)',
                                     'low_stock_threshold': 5, 'font_size': 14, 'font_weight': '600', 'text_align': 'left',
                                     'icon_size': 16}),
        t.bare('woo_addtocart', {'button_text': 'Aggiungi al carrello', 'show_quantity': True, 'show_icon': True, 'icon': 'cart',
                                 'style': 'filled', 'size': 'large', 'full_width': True, 'bg_color': GIALLO, 'text_color': ON_GIALLO,
                                 'hover_bg': NOTTE, 'hover_text': BIANCO, 'quantity_style': 'stepper',
                                 'border_radius': {'tl': 10, 'tr': 10, 'br': 10, 'bl': 10}}),
        t.bare('woo_wishlist', {'icon': 'heart', 'style': 'button', 'show_count': False, 'show_grid': False, 'icon_size': 18,
                                'icon_color': ROSSO, 'btn_bg': 'transparent', 'btn_color': TESTO}),
        t.classic('iconlist', {
            'items': [{'icon': i, 'text': s, 'link': '', 'color': ''} for i, s in [
                ('truck', 'Consegna gratuita domani, se ordini entro le 18'), ('rotate-ccw', 'Reso gratuito entro 30 giorni'),
                ('shield-check', 'Garanzia italiana di 2 anni'), ('credit-card', 'Paghi anche in 3 rate senza interessi')]],
            'icon_color': BLU, 'icon_size': '18', 'text_color': TESTO, 'text_size': '14', 'gap': '10', 'icon_shape': 'none',
            'icon_bg_color': '', 'divider': True, 'divider_color': FILO, 'layout': 'vertical'},
            style={'margin_top': 14, 'padding_top': 14, 'padding_right': 16, 'padding_bottom': 14, 'padding_left': 16,
                   'bg': {'type': 'solid', 'color': GRIGIO}, 'border_radius': 12}),
        t.bare('woo_product_meta', {'show_sku': True, 'show_categories': True, 'show_tags': False, 'layout': 'inline',
                                    'separator': ' · ', 'text_color': TENUE, 'label_color': TENUE, 'link_color': BLU,
                                    'font_size': 13, 'label_weight': '600'}),
    ]
    out = [t.section([t.row([t.col([briciole], wm='2-3', ws='1-1'), t.col([navigazione], wm='1-3', ws='1-1')],
                            layout='custom', custom='62,38', gap=16, valign='center', anim=None),
                      t.one([avvisi], anim=None)], padding='remove-vertical', width='large',
                     style={'padding_top': 18, 'padding_bottom': 8})]
    out.append(t.section([
        t.row([t.col([galleria], wm='7-12', ws='1-1'), t.col(destra, wm='5-12', ws='1-1')],
              layout='custom', custom='58,42', gap=40, valign='start', anim=None),
    ], padding='default', width='large', style={'padding_top': 8, 'padding_bottom': 40}))
    out.append(t.section([
        t.one([t.bare('woo_product_bundle', {
            'product_ids': '', 'bundle_title': 'Spesso comprati insieme', 'title_tag': 'h2', 'discount_percent': 10,
            'show_savings': True, 'show_images': True, 'show_prices': True, 'show_descriptions': False,
            'button_text': 'Aggiungi i tre al carrello', 'layout': 'horizontal', 'gap': 16, 'card_style': 'border',
            'title_color': TESTO, 'price_color': TESTO, 'savings_color': 'var(--olo-color-success, #15803d)',
            'bundle_bg': BIANCO, 'button_bg': GIALLO, 'button_color': ON_GIALLO, 'divider_color': FILO,
            'border_color': FILO, 'border_radius': 14})]),
    ], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 40}))
    out.append(t.section([
        t.one([t.bare('woo_product_tabs', {'show_description': True, 'show_additional': True, 'show_reviews': True,
                                           'tab_style': 'underline', 'active_color': BLU, 'text_color': TESTO})]),
    ], bg=solid(BIANCO), padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 36}))
    out.append(t.section([
        t.one([t.bare('woo_upsells', {'show_heading': True, 'title_text': 'Potrebbe interessarti anche', 'show_image': True,
                                      'show_title': True, 'show_price': True, 'gap': 16, 'card_style': 'border', 'columns': 4,
                                      'columns_tablet': 2, 'columns_mobile': 2, 'title_color': TESTO, 'price_color': TESTO})],
              style={'margin_bottom': 40}),
        t.one([t.bare('woo_related', {'posts_per_page': 4, 'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2,
                                      'show_image': True, 'show_title': True, 'show_price': True, 'card_style': 'border',
                                      'gap': 16, 'title_color': TESTO, 'price_color': TESTO,
                                      'heading_text': 'Della stessa categoria', 'show_heading': True})]),
        t.one([t.bare('woo_recently_viewed', {
            'columns': 6, 'columns_tablet': 3, 'columns_mobile': 2, 'limit': 6, 'gap': 14, 'card_style': 'border',
            'show_image': True, 'show_title': True, 'show_price': True, 'show_rating': False, 'show_add_to_cart': False,
            'hover_effect': 'zoom', 'image_ratio': '1-1', 'title_color': TESTO, 'price_color': TESTO, 'sale_color': ROSSO,
            'button_bg': GIALLO, 'button_color': ON_GIALLO, 'heading': 'Visti di recente', 'heading_size': 22,
            'heading_color': TESTO, 'empty_text': ''})], style={'margin_top': 40}),
    ], padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 56}))
    return out


# ══════════════════════════════════════════════════════════════════════════════
#  CARRELLO · CASSA · CONFRONTA · ASSISTENZA
# ══════════════════════════════════════════════════════════════════════════════

def build_carrello():
    t = Tpl(ID, 'carrello')
    out = [intestazione(t, 'Il carrello', 'Il tuo carrello', 'Consegna gratuita da 29 €. Ordina entro le 18 e arriva domani.')]
    out.append(t.section([
        t.one([t.bare('woo_cart', {'show_thumbnail': True, 'show_coupon': True, 'show_totals': True, 'show_cross_sells': False, 'button_color': ON_GIALLO,
                                   'button_bg': GIALLO, 'text_color': TESTO, 'heading_color': TESTO})]),
        t.one([t.bare('woo_cross_sells', {
            'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'gap': 16, 'card_style': 'border', 'show_image': True,
            'show_title': True, 'show_price': True, 'show_rating': True, 'show_add_to_cart': True, 'show_badge': True,
            'hover_effect': 'zoom', 'image_ratio': '1-1', 'title_color': TESTO, 'price_color': TESTO, 'sale_color': ROSSO,
            'button_bg': GIALLO, 'button_color': ON_GIALLO, 'badge_bg': ROSSO, 'heading': 'Ti potrebbero servire anche',
            'heading_size': 22, 'heading_color': TESTO, 'limit': 4})], style={'margin_top': 40}),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 64}))
    return out


def build_account():
    t = Tpl(ID, 'account')
    out = [intestazione(t, 'Il mio account', 'I tuoi ordini, in un posto solo.', 'Ordini, resi, indirizzi e preferenze. '
                                                                               'Entri con la mail e la password del primo ordine.')]
    out.append(t.section([
        t.row([t.col([t.bare('woo_myaccount', {'layout': 'sidebar', 'show_avatar': True, 'show_greeting': True, 'avatar_size': '56',
                                               'border_radius': '14', 'sidebar_bg': BIANCO, 'sidebar_color': TESTO,
                                               'sidebar_active_bg': NOTTE, 'sidebar_active_color': BIANCO, 'content_bg': BIANCO,
                                               'heading_color': TESTO, 'text_color': TESTO, 'link_color': BLU,
                                               'button_bg': GIALLO, 'button_color': ON_GIALLO, 'border_color': FILO})],
                     wm='2-3', ws='1-1'),
               t.col([label(t, 'Senza account'), titolo(t, 'Un ordine in arrivo?', size=26, tag='h2'),
                      text(t, '<p>Non serve entrare: con il numero dell’ordine e la mail lo segui dalla pagina Assistenza.</p>'),
                      button(t, 'Traccia l’ordine', ASSISTENZA + '#spedizioni', kind='blu', pad_top=14)],
                     wm='1-3', ws='1-1')],
              layout='custom', custom='66,34', gap=40, valign='start'),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 64}))
    return out


def build_cassa():
    t = Tpl(ID, 'cassa')
    out = [intestazione(t, 'La cassa', 'Ancora tre passi', 'I tuoi dati, la consegna, il pagamento. Il pagamento è protetto e '
                                                           'non salviamo la carta.')]
    out.append(t.section([
        t.one([t.bare('woo_checkout_multistep', {'step_labels': 'I tuoi dati,La consegna,Il pagamento', 'step_style': 'progress',
                                                 'accent_color': BLU, 'step_bg': BIANCO, 'active_color': BLU, 'text_color': TESTO,
                                                 'card_radius': 14, 'show_order_review': True})]),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 64}))
    return out


def build_confronta():
    t = Tpl(ID, 'confronta')
    out = [intestazione(t, 'Confronta', 'Fianco a fianco', 'I prodotti che hai messo a confronto dal negozio, riga per riga: '
                                                           'prezzo, voto, disponibilità e caratteristiche.')]
    out.append(t.section([
        t.one([t.bare('woo_comparison', {'max_products': 4, 'show_image': True, 'show_price': True, 'show_rating': True,
                                         'show_stock': True, 'show_sku': False, 'show_description': True, 'show_attributes': True,
                                         'show_add_to_cart': True, 'header_bg': NOTTE, 'header_color': BIANCO, 'border_color': FILO,
                                         'accent_color': BLU, 'btn_bg': GIALLO, 'btn_color': ON_GIALLO,
                                         'empty_text': 'Ancora niente da confrontare: nel negozio premi «Confronta» sotto due o tre prodotti.'})]),
        t.one([button(t, 'Torna al negozio', NEGOZIO, kind='blu', pad_top=24)]),
    ], padding='default', width='large', style={'padding_top': 32, 'padding_bottom': 64}))
    return out


def build_assistenza():
    t = Tpl(ID, 'assistenza')
    out = [intestazione(t, 'Assistenza', 'Siamo qui, sette giorni su sette.', 'Al telefono dalle 8 alle 22, in chat e per mail. '
                                                                               'Rispondono persone, in italiano, dal magazzino.')]
    out.append(t.section([
        t.one([t.classic('info-cards', {
            'items': [{'counter': '', 'counter_label': '', 'title': a, 'title_accent': '', 'title_accent_italic': False,
                       'description': b, 'icon': i, 'footer_dot_color': '', 'footer_text': '', 'link_url': ASSISTENZA + '#' + h,
                       'link_text': '', 'media_image': '', 'media_label': ''} for i, a, b, h in [
                ('truck', 'Spedizioni', 'Gratis da 29 €, domani se ordini entro le 18. Il sabato consegniamo nei capoluoghi.', 'spedizioni'),
                ('rotate-ccw', 'Resi', '30 giorni per ripensarci. Il corriere passa a ritirare, il rimborso arriva in tre giorni.', 'resi'),
                ('credit-card', 'Pagamenti', 'Carte, portafogli digitali, bonifico e tre rate senza interessi sopra i 99 €.', 'pagamenti'),
                ('shield-check', 'Garanzia', 'Due anni, italiana. Se si rompe lo ritiriamo noi e ti mandiamo il sostitutivo.', 'garanzia')]],
            'container_bg': {'type': 'none'}, 'container_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0}, 'container_padding': 0,
            'container_gap': 0, 'columns': 4, 'columns_tablet': 2, 'columns_mobile_landscape': 2, 'columns_mobile': 1,
            'items_gap': 16, 'card_bg': {'type': 'solid', 'color': BIANCO}, 'card_color': TENUE, 'card_accent_color': BLU,
            'card_radius': {'tl': 14, 'tr': 14, 'br': 14, 'bl': 14}, 'card_radius_hover': {'tl': 14, 'tr': 14, 'br': 14, 'bl': 14},
            'card_padding': 24, 'card_border': '', 'show_icon': True, 'show_counter': False, 'show_counter_label': False,
            'show_arrow': True, 'show_footer': False, 'show_link_text': False, 'show_divider': False, 'show_media': False,
            'title_font_family': 'heading', 'title_size': 24, 'title_weight': '800', 'title_italic': False, 'title_color': TESTO,
            'description_size': 15, 'card_hover_effect': 'lift', 'icon_color': BLU, 'icon_bg_color': mix(BLU, 12)})]),
    ], padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 24}))

    out.append(t.section([
        t.row([t.col([label(t, 'Le domande'), titolo(t, 'Spedizioni, resi e pagamenti', size=32, tag='h2'),
                      faq(t, [
                          ('Quanto costa la spedizione?', 'Niente sopra i 29 €; sotto, 4,90 €. I prodotti grandi (TV, monitor) li '
                                                         'consegniamo al piano, sempre gratis.'),
                          ('Entro quando arriva?', 'Ordini entro le 18 dal lunedì al venerdì: arriva il giorno dopo in tutta Italia, '
                                                  'isole comprese entro due giorni.'),
                          ('Come faccio un reso?', 'Dal link nella mail dell’ordine scegli il prodotto e il giorno: il corriere '
                                                  'passa a ritirarlo, gratis. Il rimborso parte appena lo riceviamo.'),
                          ('Posso pagare a rate?', 'Sì, in tre rate senza interessi per ordini sopra i 99 €, con la carta. '
                                                  'Nessun costo e nessuna pratica.'),
                          ('Ritirate il vecchio apparecchio?', 'Sì, gratis, quando ti consegniamo il nuovo dello stesso tipo: '
                                                              'basta dirlo nell’ordine.'),
                      ], 'ass')], wm='3-5', ws='1-1'),
               t.col([t.bare('woo_order_tracking', {'title': 'Traccia il tuo ordine', 'title_tag': 'h2', 'accent_color': BLU,
                                                    'text_color': TESTO, 'button_color': ON_GIALLO, 'button_bg': GIALLO,
                                                    'form_style': 'modern'})], wm='2-5', ws='1-1')],
              layout='custom', custom='60,40', gap=48, valign='start'),
    ], padding='default', width='large', html_id='spedizioni', style={'padding_top': 40, 'padding_bottom': 48}))

    campi = [campo('text', 'Nome', 'nome', obbligatorio=True), campo('email', 'Email', 'email', obbligatorio=True),
             campo('text', 'Numero d’ordine', 'ordine', segnaposto='Se ce l’hai'),
             campo('select', 'Di cosa si tratta', 'argomento', obbligatorio=True,
                   opzioni='Un ordine in arrivo\nUn reso\nUn prodotto che non funziona\nUna domanda prima di comprare\nAltro'),
             campo('textarea', 'Il messaggio', 'messaggio', larghezza='1-1', obbligatorio=True)]
    out.append(t.section([
        t.row([t.col([label(t, 'Scrivici'), titolo(t, 'Ti rispondiamo entro un’ora.', size=34, tag='h2'),
                      text(t, '<p>Dalle 8 alle 22, anche la domenica. Oppure chiama il ' + PHONE + '.</p>'),
                      t.classic('image', {'image_url': img('pexels-8939569.jpg'), 'alt_text': 'Una cliente riceve sorridendo un pacco dal corriere',
                                          'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/3',
                                          'height': '', 'object_fit': 'cover', 'object_position': 'center center',
                                          'border_radius': radius(14), 'lightbox': False, 'shadow': 'none',
                                          'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                                style={'margin_top': 16})],
                     wm='2-5', ws='1-1'),
               t.col([modulo(t, campi, 'Fulmine — assistenza', 'Manda', 'Ricevuto: ti rispondiamo entro un’ora.')], wm='3-5', ws='1-1')],
              layout='custom', custom='40,60', gap=48, valign='start'),
    ], bg=solid(BIANCO), padding='large', width='large', html_id='scrivici'))
    return out


def build_404():
    t = Tpl(ID, '404')
    return [t.section([
        t.row([t.col([label(t, 'Errore 404', color=GIALLO),
                      titolo(t, 'Questo pacco non è mai partito.', size=56, color=BIANCO, tag='h1'),
                      text(t, '<p>La pagina che cerchi non c’è più, o non c’è mai stata. Prova a cercare il prodotto qui sopra.</p>',
                           size=17, color=BIANCO_T),
                      button(t, 'Torna al negozio', HOME, pad_top=18)], wm='3-5', ws='1-1'),
               t.col([], wm='2-5', ws='1-1')], layout='custom', custom='60,40', valign='center'),
    ], bg=video_bg(V_SCATOLA, overlay=55), padding='xlarge', width='large', style={'padding_top': 160, 'padding_bottom': 160})]


THEME = {
    'id': ID, 'name': NAME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
    'category': 'Negozi online', 'zone': 'Ordini oggi, arriva domani',
    'requires_plugin': {'slug': 'woocommerce', 'label': 'WooCommerce', 'check': 'class:WooCommerce',
                        'purchase_url': 'https://wordpress.org/plugins/woocommerce/'},
    'tags': ['negozio', 'elettronica', 'woocommerce', 'offerte', 'e-commerce', 'tecnologia', 'consegna'],
    'description': 'Un negozio di elettronica con WooCommerce: la ricerca dal vivo fra i prodotti col prezzo, il carrello e i '
                   'preferiti in testata, le categorie, le card con la seconda foto, il voto, la marca, il risparmio, la '
                   'consegna e la scorta, l’anteprima veloce, le offerte lampo col conto alla rovescia, il filtro, il confronto, '
                   'la scheda prodotto, il carrello e la cassa a passi.',
    'menu': {'name': 'Fulmine — categorie', 'items': [{'title': 'Offerte lampo', 'url': OFFERTE}, {'title': 'Tutti i prodotti', 'url': NEGOZIO}]
             + [{'title': n, 'url': cat_url(s)} for n, s in CATEGORIE] + [{'title': 'Assistenza', 'url': ASSISTENZA}]},
    'templates': {
        '404': {'file': '404.json', 'type': '404', 'title': 'Fulmine — Pagina 404'},
        'header': {'file': 'header.json', 'type': 'header', 'title': 'Fulmine — Header'},
        'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'Fulmine — Footer'},
        'home': {'file': 'home.json', 'type': 'page', 'title': 'Fulmine — Home'},
        'negozio': {'file': 'negozio.json', 'type': 'page', 'title': 'Fulmine — Negozio'},
        'offerte': {'file': 'offerte.json', 'type': 'page', 'title': 'Fulmine — Offerte lampo'},
        'assistenza': {'file': 'assistenza.json', 'type': 'page', 'title': 'Fulmine — Assistenza'},
        'confronta': {'file': 'confronta.json', 'type': 'page', 'title': 'Fulmine — Confronta'},
        'carrello': {'file': 'carrello.json', 'type': 'page', 'title': 'Fulmine — Carrello'},
        'cassa': {'file': 'cassa.json', 'type': 'page', 'title': 'Fulmine — Cassa'},
        'account': {'file': 'account.json', 'type': 'page', 'title': 'Fulmine — Il mio account'},
        'prodotto': {'file': 'prodotto.json', 'type': 'single', 'title': 'Fulmine — Scheda prodotto', 'post_type': 'product'},
    },
    'singles': {'product': 'prodotto'},
    # Le pagine del negozio in Template WooCommerce (all'import: Impostazioni → Template WooCommerce), la scheda dei
    # prodotti da singles → product. Nella demo, carrello e cassa del tema al posto di quelli del sito.
    'woo': {'product_archive': 'negozio', 'product_category': 'negozio', 'cart': 'carrello', 'checkout': 'cassa',
            'myaccount': 'account'},
    'activate': {'404': '404', 'header': 'header', 'footer': 'footer'},
    'pages': {
        'home': {'title': 'Home', 'template': 'home', 'set_as_homepage': True},
        'negozio': {'title': 'Tutti i prodotti', 'template': 'negozio', 'slug': 'negozio'},
        'offerte': {'title': 'Offerte lampo', 'template': 'offerte', 'slug': 'offerte'},
        'assistenza': {'title': 'Assistenza', 'template': 'assistenza', 'slug': 'assistenza'},
        'confronta': {'title': 'Confronta', 'template': 'confronta', 'slug': 'confronta'},
        'carrello': {'title': 'Carrello', 'template': 'carrello', 'slug': 'carrello'},
        'cassa': {'title': 'Cassa', 'template': 'cassa', 'slug': 'cassa'},
        'account': {'title': 'Il mio account', 'template': 'account', 'slug': 'il-mio-account'},
    },
    'styles': {
        'colors': dict(HEX),
        'typography': {
            'font_family': '\'Inter\', system-ui, -apple-system, \'Segoe UI\', sans-serif',
            'font_family_heading': '\'Archivo\', \'Helvetica Neue\', Arial, sans-serif',
            'font_family_mono': '\'JetBrains Mono\', ui-monospace, Menlo, monospace',
            'font_size_base': '15px', 'line_height': '1.6', 'font_weight_body': '400', 'font_weight_heading': '800',
            'heading_line_height': '1.1', 'heading_letter_spacing': '-0.01em', 'heading_text_transform': 'none',
            'letter_spacing': '0', 'font_size_h1': '3.4rem', 'font_size_h2': '2.4rem', 'font_size_h3': '1.5rem',
            'font_size_h4': '1.2rem', 'font_size_h5': '1.05rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.2rem',
            'font_size_h2_mobile': '1.8rem', 'font_size_h3_mobile': '1.3rem',
        },
        'google_fonts': ['Archivo', 'Inter', 'JetBrains Mono'],
        'google_fonts_weights': '400;500;600;700;800',
        'google_fonts_italic': [],
        'buttons': {'font_size': '15px', 'font_weight': '700', 'padding_x': '22px', 'padding_y': '13px', 'border_radius': '10px',
                    'text_transform': 'none', 'letter_spacing': '0', 'hover_brightness': '100'},
        'layout': {'border_radius': '10px', 'border_radius_large': '18px', 'container_max_width': '1320px',
                   'container_narrow': '760px', 'container_wide': '1480px'},
        'forms': {'field_bg': HEX['light'], 'field_border_color': HEX['border'], 'field_border_width': '1',
                  'field_border_radius': '10px', 'field_padding': '12px 14px', 'field_font_size': '15px',
                  'focus_border_color': HEX['secondary'], 'focus_shadow': 'none',
                  'label_font_size': '13px', 'label_font_weight': '600', 'label_margin_bottom': '6px'},
        'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
    },
}


def main():
    builds = {'header.json': build_header(), 'footer.json': build_footer(), 'home.json': build_home(),
              'negozio.json': build_negozio(), 'offerte.json': build_offerte(), 'assistenza.json': build_assistenza(),
              'confronta.json': build_confronta(), 'carrello.json': build_carrello(), 'cassa.json': build_cassa(), 'account.json': build_account(),
              'prodotto.json': build_prodotto(), '404.json': build_404()}
    write_theme(THEME, builds, MEDIA)


if __name__ == '__main__':
    main()
