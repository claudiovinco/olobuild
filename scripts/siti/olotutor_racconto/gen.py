# -*- coding: utf-8 -*-
"""Genera il tema «OLOtutor — Il racconto» (id olotutor-racconto), 7 nov 2026.

Richiesta dell'utente: un sito che prenda tutte le informazioni di olotutor.com, divise per figura (studente, docente,
scuola), e racconti il progetto in modo più umano, moderno e pieno di soluzioni grafiche; tutte le immagini del sito
riusate; il lavoro va come template nuovi nella zona dei template (Blocchi & Pagine).

Il tema è del sito dell'utente, quindi NON sta in assets/data/themes (feedback_temi_privati_mai_nel_selettore): la
cartella uscita da qui va sul sottosito olotheme.com/olotutor in wp-content/olotutor-temi/ (demo a /olotutor/demo/
olotutor-racconto/), e importa.php la porta fra i template di quel sito senza toccare quelli attivi.

Il racconto segue persone: Elena della 2A (le schermate vere della piattaforma, classe di prova con nomi inventati), la
sua prof, la segreteria, la famiglia. Ogni pagina ha un'apertura di logica diversa (il voto che viaggia, il telefono di
Elena, i tre atti della lezione, il registro che si alza in 3D, la carta di ripasso che si gira, una classe su quaranta).
Solo tile di OLObuild e controlli nativi: nessun custom_css, nessun HTML in linea.

Uso: python gen.py   (scrive ./olotutor-racconto/)
"""
import copy
import json
import os
import sys

from comune import (K, ID, NOME, MEDIA, Tpl, mix, radius, solid, img, occ, tit, txt, mano, btn, bottoni, sez, due,
                    CARTA, INK, LAV, VERDE, GIALLO, GESSO, GESSO_T, GESSO_F, VERDE_G, LAV2, HOME, STU, DOC, SCU, MET,
                    PRO, NOTA, MAIL, SITO)
from base import check, niente_velati, tiles_used, page  # noqa: E402
import p_home

QUI = os.path.dirname(os.path.abspath(__file__))
# SITO=com scrive la variante per olotutor.com in una cartella a parte (la demo resta quella di sempre)
OUT = os.path.join(QUI, ID + ('-com' if SITO == 'com' else ''))

# la Palette e i caratteri del sottosito OLOtutor (olobuild_styles di olotheme.com/olotutor, 7 nov 2026)
HEX = {'primary': '#2E7D32', 'primary_contrast': '#FFFFFF', 'secondary': '#1D2B44', 'secondary_contrast': '#FBFBF7',
       'accent': '#FFD84A', 'dark': '#14203A', 'light': '#FBFBF7', 'muted': '#EAF1F8', 'muted_contrast': '#1D2B44',
       'text': '#1D2B44', 'text_muted': '#55627A', 'background': '#FBFBF7', 'border': '#D6E1EC', 'link': '#2E7D32',
       'success': '#2E7D32', 'warning': '#E0A100', 'danger': '#C8412F', 'info': '#2F6DB5'}
STILI = {
    'colors': HEX,
    'typography': {'font_family': '"Atkinson Hyperlegible", system-ui, -apple-system, "Segoe UI", sans-serif',
                   'font_family_heading': '"Lexend", system-ui, -apple-system, "Segoe UI", sans-serif',
                   'font_family_mono': '', 'font_size_base': '18px', 'line_height': '1.62', 'font_weight_body': '400',
                   'font_weight_heading': '700', 'heading_line_height': '1.08', 'heading_letter_spacing': '-0.025em',
                   'heading_text_transform': 'none', 'letter_spacing': '0', 'font_size_h1': '4.4rem',
                   'font_size_h2': '3.2rem', 'font_size_h3': '1.6rem', 'font_size_h4': '1.3rem', 'font_size_h5': '1.1rem',
                   'font_size_h6': '1rem', 'font_size_h1_mobile': '2.5rem', 'font_size_h2_mobile': '2rem',
                   'font_size_h3_mobile': '1.35rem'},
    'google_fonts': ['Lexend', 'Atkinson Hyperlegible', 'Caveat'], 'google_fonts_weights': '400;600;700;800',
    'google_fonts_italic': [],
    'buttons': {'font_size': '17px', 'font_weight': '700', 'padding_x': '26px', 'padding_y': '15px', 'border_radius': '12px',
                'text_transform': 'none', 'letter_spacing': '0', 'hover_brightness': '92'},
    'layout': {'border_radius': '14px', 'border_radius_large': '22px', 'container_max_width': '1240px',
               'container_narrow': '760px', 'container_wide': '1440px'},
    'forms': {'field_bg': '#FFFFFF', 'field_border_color': '#D6E1EC', 'field_border_width': '1', 'field_border_radius': '12px',
              'field_padding': '12px 16px', 'field_font_size': '16px', 'focus_border_color': '#2E7D32',
              'focus_shadow': '0 0 0 3px rgba(46,125,50,0.18)', 'label_font_size': '14px', 'label_font_weight': '600',
              'label_margin_bottom': '6px'},
    'links': {'color': 'currentColor', 'hover_color': '', 'decoration': 'underline', 'hover_decoration': 'underline'},
    'grain': {'enabled': False, 'opacity': 6, 'scale': 180},
}
PAGINE = [  # chiave, titolo, slug (None = home)
    ('home', 'Il racconto', None), ('studente', 'Lo studente', 'lo-studente'), ('docente', 'Il docente', 'il-docente'),
    ('scuola', 'La scuola', 'la-scuola'), ('metodo', 'Il metodo', 'il-metodo'), ('prova', 'Come si prova', 'come-si-prova'),
]
MENU = [('Lo studente', STU), ('Il docente', DOC), ('La scuola', SCU), ('Il metodo', MET), ('Come si prova', PRO)]


def tema():
    templates = {'404': {'file': '404.json', 'type': '404', 'title': 'OLOtutor Racconto — Pagina 404'},
                 'header': {'file': 'header.json', 'type': 'header', 'title': 'OLOtutor Racconto — Header'},
                 'footer': {'file': 'footer.json', 'type': 'footer', 'title': 'OLOtutor Racconto — Footer'}}
    pg = {}
    for k, titolo, slug in PAGINE:
        templates[k] = {'file': k + '.json', 'type': 'page', 'title': 'OLOtutor Racconto — %s' % titolo}
        pg[k] = {'title': titolo, 'template': k, 'set_as_homepage': True} if slug is None else {'title': titolo, 'template': k, 'slug': slug}
    return {
        'id': ID, 'name': NOME, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
        'category': 'Istruzione', 'zone': 'Un voto, quattro sguardi: lo studente, il docente, la scuola, la famiglia',
        'tags': ['scuola', 'e-learning', 'olotutor', 'lavagna', 'quaderno'],
        'description': 'OLOtutor raccontato dalle persone che lo usano: il voto che passa dal registro della prof al telefono '
                       'di Elena, alla media della classe, alla famiglia; la lavagna, il quaderno e l\'evidenziatore giallo.',
        'inLavorazione': True,
        'menu': {'name': 'OLOtutor Racconto — menu', 'items': [{'title': a, 'url': b} for a, b in MENU]},
        'templates': templates, 'activate': {'404': '404', 'header': 'header', 'footer': 'footer'}, 'pages': pg,
        'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 30, 'ring_color': HEX['dark'], 'ring_width': 1,
                   'dot_size': 5, 'dot_color': HEX['primary'], 'hot_scale': 2.0, 'hot_color': HEX['primary'],
                   'hot_fill': 'rgba(46,125,50,0.12)', 'blend_mode': 'normal', 'magnetic_selector': 'a, button',
                   'pull_strength': 0.3, 'follow_easing': 0.2, 'style': 'pennino', 'label_text': 'Apri',
                   'label_color': HEX['primary_contrast']},
        'styles': STILI,
    }


# ── testata ──
def build_header():
    t = Tpl('otr', 'header')
    base = json.load(open(os.path.join(QUI, 'testata-olotutor.json'), encoding='utf-8'))
    s = copy.deepcopy(base)
    s.update({
        'logo_image': img('otr-logo-w.png'), 'logo_sticky': img('otr-logo-w.png'), 'mobile_logo': img('otr-logo-w.png'),
        'logo_width': '150', 'logo_link': HOME, 'logo_mark': '',
        'nav_bg': LAV, 'sticky_bg': mix(LAV, 96), 'text_color': mix(GESSO, 88), 'hover_color': GIALLO,
        'active_color': GIALLO, 'hover_effect': 'underline-grow', 'hover_effect_color': GIALLO,
        'btn_bg': GIALLO, 'btn_color': INK, 'btn_hover_color': INK, 'btn_hover_bg': GESSO, 'btn_hover_effect': 'lift',
        'btn_font_weight': '700', 'mobile_accent_color': VERDE,
        'extra_link_1_label': '← OLOtheme', 'extra_link_1_url': 'https://olotheme.com/', 'extra_link_1_blank': False,
        'extra_link_1_button': False, 'extra_link_2_label': '', 'extra_link_2_url': '', 'extra_links_right': True,
        'lang_tile_id': '',
        'topbar_enabled': True, 'topbar_bg': GIALLO, 'topbar_text_color': INK, 'topbar_link_color': INK,
        'topbar_height': '34', 'topbar_font_size': '13', 'topbar_hide_mobile': True, 'topbar_hide_sticky': True,
        'topbar_left_content': 'ticker', 'topbar_left_text': '', 'topbar_left_menu_id': 0,
        'topbar_ticker_items': 'Ottimizzata la gestione del calendario studenti\nTest 100 accessi simultanei concluso con '
                               'successo\nTest 200 accessi in corso',
        'topbar_ticker_label': 'Aggiornamenti: ', 'topbar_ticker_speed': '5', 'topbar_right_social': False,
        'topbar_right_search': False, 'topbar_right_cart': False, 'topbar_right_text': 'Piattaforma e-learning per la scuola', 'topbar_right_cta_label': '', 'topbar_border_bottom': False,
        'mobile_footer_text': 'Piattaforma e-learning per la scuola',
        'mobile_footer_cta_text': 'Prova con una classe', 'mobile_footer_cta_url': PRO,
    })
    tiles = []
    if SITO == 'com':
        # olotutor.com: «Entra» porta alla piattaforma (come la testata di prima), e il selettore delle lingue
        # (il sito è in quattro lingue con OLOlang) va nella barra con lang_tile_id, come sul sottosito OLOtutor
        s.update({'extra_link_1_label': 'Entra', 'extra_link_1_url': '/entra', 'extra_link_1_blank': True,
                  'extra_link_1_button': False, 'lang_tile_id': 'otr-header-lingue'})
        tiles.append(t.node('langswitcher', {
            'preset': 'custom', 'typography_preset': '', 'lang_source': 'auto', 'style': 'codes', 'layout': 'dropdown',
            'compact': True, 'item_box': 'box', 'show_dropdown_arrow': True, 'gap': 8, 'bg': mix(GESSO, 8),
            'color': GESSO, 'border_color': mix(GESSO, 24), 'active_bg': mix(GIALLO, 22), 'active_color': GIALLO,
            'border_radius': 10}))
        tiles[-1]['id'] = 'otr-header-lingue'
    mm = t.node('megamenu', s)
    return [t.section([t.one([mm] + tiles, anim=None)], width='fullbleed', padding='remove-vertical')]


# ── piè di pagina ──
def build_footer():
    t = Tpl('otr', 'footer')

    def nav(voci):
        n = t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in voci], 'style': 'default',
                              'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                              'link_color': GESSO, 'link_hover_color': GIALLO, 'active_color': GIALLO, 'font_size': '17',
                              'font_weight': '400', 'tile_padding': {'top': 4, 'right': 0, 'bottom': 4, 'left': 0}})
        return n
    logo = t.classic('image', {'image_url': img('otr-logo-w.png'), 'alt_text': 'OLOtutor', 'media_bg': {'type': 'none'},
                               'aspect_ratio': 'custom', 'aspect_ratio_custom': '1000/272', 'height': '', 'object_fit': 'contain',
                               'object_position': 'left center', 'image_width': '220px', 'image_alignment': 'left',
                               'border_radius': radius(0), 'shadow': 'none', 'lightbox': False, 'link_url': HOME})
    contatti = t.classic('iconlist', {'items': [{'icon': 'mail', 'text': MAIL, 'link': 'mailto:' + MAIL, 'color': ''},
                                                {'icon': 'file-text', 'text': 'La nota di sintesi (PDF)', 'link': NOTA, 'color': ''}],
                                      'icon_color': INK, 'icon_size': '16', 'text_color': GESSO, 'text_size': '16', 'gap': '12',
                                      'icon_shape': 'circle', 'icon_bg_color': GIALLO, 'divider': False, 'layout': 'vertical'})
    totop = t.classic('totop', {'icon_color': INK, 'posizione': 'fisso', 'style': 'default'})
    top = t.row([
        t.col([logo, txt(t, 'I corsi restano tuoi e si esportano in qualsiasi momento, con le loro domande e il loro '
                            'glossario.', size=17, col=GESSO_T, pad=(18, 0, 10, 0)),
               mano(t, 'si comincia da una classe.', col=GIALLO, size=30)], wm='2-5', ws='1-1'),
        t.col([occ(t, 'Il prodotto', col=GIALLO), nav([('Per lo studente', STU), ('Per il docente', DOC),
                                                        ('Per la scuola', SCU)])], wm='1-5', ws='1-2', width_default='1-2'),
        t.col([occ(t, 'Documenti', col=GIALLO), nav([('Nota di sintesi', NOTA), ('Come si prova', PRO), ('Il metodo', MET),
                                                      ('Accessibilità', SCU + '#altre-funzioni')])], wm='1-5', ws='1-2',
              width_default='1-2'),
        t.col([occ(t, 'Scrivici', col=GIALLO), contatti], wm='1-5', ws='1-1'),
    ], layout='custom', custom='40,20,20,20', gap=40, valign='start', anim=None)
    fondo = t.row([t.col([txt(t, 'OLOtutor by clod.eu · ©2026 OLOtheme team · criteri WCAG 2.1 AA · Open Badges 2.0', size=14,
                              col=mix(GESSO, 62)), totop])], layout='100', anim=None,
                  style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': GESSO_F},
                         'padding_top': 22, 'margin_top': 56})
    return [t.section([top, fondo], bg={'type': 'image', 'image_url': img('otr-tesi-cerchi.svg'), 'image_size': 'cover',
                                        'image_position': 'right top', 'overlay_color': LAV, 'overlay_opacity': 0},
                      padding='large', style={'padding_top': 96, 'padding_bottom': 40})]


# ── 404 ──
def build_404():
    t = Tpl('otr', '404')
    return [t.section([
        t.row([t.col([
            occ(t, 'Errore 404', col=GIALLO),
            tit(t, 'Questa pagina <em>non è nel programma</em>.', size=72, col=GESSO, acc=GIALLO, tag='h1'),
            txt(t, 'Capita anche alle carte migliori di finire nel mazzo sbagliato. Si riparte da qui:', col=GESSO_T),
            bottoni(t, [('Torna all\'inizio', HOME, 'giallo'), ('Lo studente', STU, 'filo_g'), ('Il docente', DOC, 'filo_g'),
                        ('La scuola', SCU, 'filo_g')]),
            mano(t, 'di nuovo · difficile · bene · facile', col=VERDE_G, size=30, pad_bottom=0),
        ], wm='2-3', ws='1-1'), t.col([
            t.classic('textpath', {'text': 'Di nuovo · Difficile · Bene · Facile · Di nuovo · Difficile · Bene · Facile · ',
                                   'path_preset': 'circle', 'font_size': '22', 'text_color': GIALLO, 'letter_spacing': '3',
                                   'animation': 'none', 'spin': True, 'spin_speed': '18', 'spin_direction': 'cw'})],
            wm='1-3', ws='1-1')], layout='custom', custom='64,36', gap=48, valign='center'),
    ], bg={'type': 'image', 'image_url': img('otr-hero-navy-bg.svg'), 'image_size': 'cover', 'image_position': 'right top',
           'overlay_color': LAV, 'overlay_opacity': 0}, padding='large', style={'padding_top': 150, 'padding_bottom': 150})]


def costruisci(solo=None):
    import importlib
    builds = {'header.json': build_header(), 'footer.json': build_footer(), '404.json': build_404()}
    for k, _, _ in PAGINE:
        nome = 'p_' + k
        if solo and k not in solo:
            continue
        try:
            mod = importlib.import_module(nome)
        except ModuleNotFoundError:
            print('  (pagina %s non ancora scritta)' % k)
            continue
        builds[k + '.json'] = page(mod.build())
    return builds


def scrivi(builds):
    os.makedirs(OUT, exist_ok=True)
    tutti = set()
    for name, built in builds.items():
        nodes = built['content'] if isinstance(built, dict) else built
        niente_velati(nodes)
        check(nodes, MEDIA, name)
        ids = set()

        def walk(n):
            assert n['id'] not in ids, 'id doppio ' + n['id']
            ids.add(n['id'])
            for c in n.get('children') or []:
                walk(c)
        for n in nodes:
            walk(n)
        with open(os.path.join(OUT, name), 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(json.dumps(built, ensure_ascii=False, separators=(',', ':')))
        u = tiles_used(nodes, set())
        tutti |= u
        print('  %-14s %3d nodi · %s' % (name, len(ids), ', '.join(sorted(u))))
    th = tema()
    th['templates'] = {k: v for k, v in th['templates'].items() if v['file'] in builds}
    th['pages'] = {k: v for k, v in th['pages'].items() if v['template'] + '.json' in builds}
    with open(os.path.join(OUT, 'theme.json'), 'w', encoding='utf-8', newline='\n') as fh:
        fh.write(json.dumps(th, ensure_ascii=False, separators=(',', ':')))
    for f in ('otr-logo.png', 'otr-logo-w.png'):   # i loghi del tema (logo.png e logo-light.png per il selettore)
        import shutil
        shutil.copyfile(os.path.join(QUI, 'media', f), os.path.join(OUT, 'logo.png' if f == 'otr-logo.png' else 'logo-light.png'))
    print(ID, '·', len(builds), 'file ·', len(tutti), 'tipi di tile')


if __name__ == '__main__':
    sys.stdout.reconfigure(encoding='utf-8')
    scrivi(costruisci(sys.argv[1:] or None))
