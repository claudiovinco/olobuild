# -*- coding: utf-8 -*-
"""Pezzi comuni del tema «OLOtutor — Il racconto»: colori (solo token della Palette del sito OLOtutor), link alle
pagine, e i mattoncini (occhiello, titolo, testo, nota a mano, pulsante, foto, schermata) che le pagine compongono.

Il tema usa la Palette che il sottosito olotheme.com/olotutor ha già (verde #2E7D32, blu #1D2B44, giallo #FFD84A,
lavagna #14203A, carta #FBFBF7) e i suoi caratteri (Lexend nei titoli, Atkinson Hyperlegible nel testo, Caveat per
le note a mano): i template importati lì si vedono come nella demo.
"""
import os
import sys

QUI = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, os.path.normpath(os.path.join(QUI, '..', '..', 'temi')))

from base import Tpl, lib, mix, radius, solid  # noqa: E402,F401
from kit import Kit  # noqa: E402
import media as MD  # noqa: E402

ID = 'olotutor-racconto'
NOME = 'OLOtutor — Il racconto'
# i media del tema: quelli di olotutor.com (otr-*) e il video di sottofondo della home, già nella libreria di OLObuild
MEDIA = set(MD.TUTTI) | {'pexels-video-8108842.mp4', 'pexels-video-8108842-copertina.jpg'}
K = Kit(ID, MEDIA, raggio=14, peso_titoli='700', corsivo=False, maiuscole_pulsanti=False)


def tok(n):
    return 'var(--olo-color-%s)' % n


CARTA, INK, TENUE, LAV, BLU = tok('background'), tok('text'), tok('text-muted'), tok('dark'), tok('secondary')
VERDE, ONV, GIALLO, GESSO, CIELO, FILO = tok('primary'), tok('primary-contrast'), tok('accent'), tok('light'), tok('muted'), tok('border')
ROSSO, ARANCIO, AZZURRO = tok('danger'), tok('warning'), tok('info')
VERDE_G = mix(VERDE, 55, GESSO)        # il verde che si legge sulla lavagna (6:1)
GESSO_T = mix(GESSO, 80)               # il gesso dei testi lunghi sulla lavagna
GESSO_F = mix(GESSO, 14)               # i fili sulla lavagna
LAV2 = mix(LAV, 88, BLU)               # la lavagna un po' più chiara, per le schede sulla lavagna
CARTA2 = mix(INK, 5, CARTA)            # la carta un po' più grigia, per le sezioni «quaderno»
MANO = "'Caveat', cursive"

HOME, STU, DOC, SCU, MET, PRO = ['{{PAGINA:%s}}' % k for k in ('home', 'studente', 'docente', 'scuola', 'metodo', 'prova')]
NOTA = lib('otr-nota-di-sintesi.pdf')
MAIL = 'info@olotutor.com'

# La variante per olotutor.com (SITO=com, 7 nov 2026): nella testata «Entra» (la piattaforma) e il selettore delle
# lingue, la nota di sintesi dal sito stesso, e in «Come si prova» niente modulo (la pagina privacy del sito è in
# bozza): «Scrivici» con la mail, come il sito aveva già.
SITO = os.environ.get('SITO', '')
if SITO == 'com':
    NOTA = 'https://olotutor.com/wp-content/uploads/2026/09/nota-di-sintesi.pdf'


def img(nome):
    return K.img(nome)


# ── testi ──
def occ(t, s, col=None, align='left', pad_bottom=14, pad_top=0):
    """L'occhiello: maiuscolo piccolo, in Lexend."""
    return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                  'accent_text': '', 'heading_uppercase': True, 'heading_font_size': 13,
                                  'heading_weight': '700', 'heading_color': col or VERDE, 'heading_font': 'heading',
                                  'heading_letter_spacing': '1.6'},
                     style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})


def tit(t, s, size=56, col=None, acc=None, tag='h2', align='left', pad_bottom=18, sizes=None):
    """Il titolo; la parte fra <em> e </em> prende il colore d'accento (non corsivo)."""
    h = K.headline(t, s, size=size, color=col or INK, tag=tag, pad_bottom=pad_bottom, align=align, weight='700',
                   spacing='-1' if size >= 40 else None, sizes=sizes, accento=acc or VERDE)
    if '<em>' not in s:
        h['settings']['accent_text'] = ''
    h['settings']['heading_line_height'] = 1.06
    return h


def txt(t, html, size=18, col=None, align='left', pad=(0, 0, 0, 0)):
    b = K.text(t, html, size=size, color=col or TENUE, pad=pad, align=align)
    b['settings']['line_height'] = 1.62
    return b


def mano(t, s, col=None, size=30, align='left', pad_bottom=8):
    """Una nota scritta a mano (Caveat), come sul quaderno."""
    h = t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                               'accent_text': '', 'heading_uppercase': False, 'heading_font_size': size,
                               'heading_font_size_tablet': str(size), 'heading_font_size_mobile': str(max(22, int(size * 0.85))),
                               'heading_weight': '700', 'heading_color': col or VERDE, 'heading_font': MANO},
                  style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})
    h['settings']['heading_line_height'] = 1.1
    return h


def btn(t, s, url, tipo='verde', align='left', pad_top=18, blank=False):
    """tipo: verde (pieno), giallo (pieno, testo inchiostro), filo (su carta), filo_g (su lavagna)."""
    if tipo == 'verde':
        o = {'bg_color': VERDE, 'text_color': ONV, 'hover_bg_color': INK, 'hover_text_color': CARTA}
    elif tipo == 'giallo':
        o = {'bg_color': GIALLO, 'text_color': INK, 'hover_bg_color': GESSO, 'hover_text_color': INK}
    else:
        base = GESSO if tipo == 'filo_g' else INK
        o = {'bg_color': 'transparent', 'text_color': base, 'hover_bg_color': base, 'hover_text_color': LAV if tipo == 'filo_g' else CARTA,
             'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 2,
             'shadow_color': mix(base, 40), 'shadow_inset': True}
    o.update({'text': s, 'url': url, 'alignment': align, 'border_radius': 12, 'font_size': 16, 'font_weight': '700',
              'text_transform': 'none', 'letter_spacing': '0', 'target': '_blank' if blank else '_self'})
    return t.classic('button', o, style={'padding_top': pad_top})


def bottoni(t, voci, pad_top=22, align='left'):
    """Più pulsanti affiancati alla loro larghezza (riga a CSS Grid, colonne «max-content»); sotto i 960 px uno
    sotto l'altro. voci: [(testo, link, tipo)]"""
    extra = {'grid_justify_content': 'center'} if align == 'center' else {}
    return t.row([t.col([btn(t, s, u, k, pad_top=0, align=align, blank=u.endswith('.pdf'))]) for s, u, k in voci],
                 layout='100', anim=None, layout_mode='grid', grid_columns=' '.join(['max-content'] * len(voci)),
                 grid_column_gap=12, grid_row_gap=12, style={'margin_top': pad_top}, **extra)


def badge(t, s, fondo=None, col=None, align='left'):
    return t.classic('badge', {'text': s, 'variant': 'solid', 'bg_color': fondo or GIALLO, 'text_color': col or INK,
                               'alignment': align})


# ── immagini ──
OMBRA = {'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 26, 'shadow_blur': 60, 'shadow_spread': -22,
         'shadow_color': mix(LAV, 45), 'shadow_inset': False}


def foto(t, nome, alt, ratio='4/3', pos='center center', r=18, ombra=True, tilt=False, drift=False, bordo=False):
    i = K.image(t, nome, alt, ratio=ratio, position=pos, drift=drift, tilt=tilt)
    s = i['settings']
    s['border_radius'] = radius(r)
    if ombra:
        s.update(OMBRA)
    if bordo:
        s['border'] = {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': FILO}
    return i


def schermata(t, nome, alt, ratio='16/10', pos='center top', tilt=True, r=16):
    """Una schermata vera della piattaforma: bordo sottile, ombra lunga, si inclina col mouse."""
    return foto(t, nome, alt, ratio=ratio, pos=pos, r=r, ombra=True, tilt=tilt, bordo=True)


# ── sezioni ──
def sez(t, figli, fondo=None, pad=(110, 110), html_id=None, width='default', **extra):
    bg = solid(fondo) if isinstance(fondo, str) else fondo
    return t.section(figli, bg=bg, padding='large', width=width, html_id=html_id,
                     style={'padding_top': pad[0], 'padding_bottom': pad[1]}, **extra)


def due(t, sx, dx, peso=(50, 50), gap=72, valign='center', inverti_tel=False, anim='slide-up'):
    """Due colonne (sul telefono una sotto l'altra)."""
    a, b = peso
    return t.row([t.col(sx, wm=None, ws='1-1'), t.col(dx, wm=None, ws='1-1')], layout='custom', custom='%s,%s' % (a, b),
                 gap=gap, valign=valign, anim=anim)


def griglia(t, colonne, n=3, gap=24, stili=None, anim='slide-up'):
    """Colonne vere in una riga a CSS Grid: il gap è reale e lo sfondo della colonna non lo invade
    (feedback no-custom-css-templates); sotto i 960 px una sotto l'altra. stili: uno stile per colonna (o uno per tutte)."""
    if isinstance(stili, dict):
        stili = [stili] * len(colonne)
    cols = [t.col(c, style=dict(stili[i]) if stili else None) for i, c in enumerate(colonne)]
    return t.row(cols, layout='100', gap=None, anim=anim, layout_mode='grid', grid_columns='repeat(%d, 1fr)' % n,
                 grid_column_gap=gap, grid_row_gap=gap)


def scheda(fondo, r=22, pad=(36, 34, 34, 34), bordo=None, ombra=False):
    """Lo stile di una colonna-scheda."""
    st = {'bg': solid(fondo), 'padding_top': pad[0], 'padding_right': pad[1], 'padding_bottom': pad[2],
          'padding_left': pad[3], 'border_radius': radius(r)}
    if bordo:
        st['border'] = {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'style': 'solid', 'color': bordo}
    if ombra:
        st.update({'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 18, 'shadow_blur': 44, 'shadow_spread': -24,
                   'shadow_color': mix(LAV, 30), 'shadow_inset': False})
    return st


# ── tile speciali usate in più pagine ──
def marquee(t, testo, fondo=None, col=None, size=34):
    return t.classic('marquee', {'content_type': 'text', 'text_items': testo, 'separator': '  ✶  ', 'speed': '30',
                                 'direction': 'left', 'pause_hover': False, 'gap': 30, 'velocity_skew': True,
                                 'bg_color': fondo or GIALLO, 'text_color': col or INK, 'font_size': size,
                                 'font_weight': '700', 'letter_spacing': 0, 'text_transform': 'none',
                                 'font_family': 'heading', 'separator_color': col or INK, 'height': int(size * 2.2),
                                 'full_width': True})


def chiusura(t, titolo='Come si comincia.', testo='Si comincia da una classe e da un corso già in calendario, per il '
             'tempo di un\'unità di apprendimento, senza migrare un registro e senza aprire un progetto.',
             cta=('Come si prova', PRO), cta2=('Scarica la nota di sintesi', NOTA), html_id=None):
    """La chiusura di ogni pagina: il banner con i due pulsanti, sulla lavagna con le righe del quaderno."""
    b = t.classic('cta-banner', {
        'headline': titolo, 'headline_accent': '', 'headline_accent_italic': False, 'subtitle': testo,
        'cta_text': cta[0], 'cta_url': cta[1], 'cta_target': '_self', 'cta2_text': cta2[0], 'cta2_url': cta2[1],
        'cta2_target': '_blank' if cta2[1].endswith('.pdf') else '_self', 'cta2_bg': 'transparent', 'cta2_color': GESSO,
        'cta2_border': mix(GESSO, 55),
        'bg': {'type': 'image', 'image_url': img('otr-std-navy-bg.svg'), 'image_size': 'cover', 'image_position': 'right top',
               'overlay_color': LAV, 'overlay_opacity': 0},
        'text_color': GESSO, 'accent_color': GIALLO, 'subtitle_color': GESSO_T,
        'cta_bg': GIALLO, 'cta_bg_hover': GESSO, 'cta_color': INK, 'cta_color_hover': INK,
        'cta_radius': radius(12), 'cta_radius_hover': radius(12), 'cta_size': 16,
        'cta_padding': {'top': 17, 'right': 28, 'bottom': 17, 'left': 28}, 'headline_font_family': 'heading',
        'headline_size': 60, 'headline_weight': '700', 'subtitle_size': 19, 'layout': 'split-2', 'ratio': '1.4fr 1fr auto',
        'gap': 40, 'vertical_align': 'center', 'banner_radius': radius(28), 'banner_radius_hover': radius(28),
        'banner_padding': {'top': 72, 'right': 64, 'bottom': 72, 'left': 64}})
    return sez(t, [t.one([b])], fondo=CARTA, pad=(40, 96), html_id=html_id)


def viste(t, titolo, voci):
    """«Le stesse funzioni, viste dalle altre due figure»: un interruttore a schede, una per figura."""
    items = []
    for i, (nome, righe, link_t, link_u) in enumerate(voci):
        corpo = ''.join('<p><strong>%s</strong> %s</p>' % (a, b) for a, b in righe)
        items.append({'id': '%s-vista-%d' % (t.key, i + 1), 'title': nome, 'icon': '', 'content': corpo, 'image': '',
                      'link_text': link_t, 'link_url': link_u})
    sw = t.classic('switcher', {
        'items': items, 'preset': 'custom', 'nav_style': 'tab', 'animation': 'fade', 'animation_duration': '300',
        'vertical': False, 'tab_padding_y': '12', 'tab_padding_x': '22', 'tab_font_size': 17, 'tab_font_weight': '700',
        'tab_gap': 6, 'tab_radius': 999, 'container_bg': mix(GESSO, 8), 'container_padding': 6, 'container_radius': 999,
        'active_bg': GIALLO, 'active_color': INK, 'inactive_color': GESSO_T, 'hover_bg': mix(GIALLO, 80),
        'indicator_type': 'pill', 'content_bg': '', 'content_color': GESSO, 'content_padding_y': '28',
        'content_padding_x': '0'})
    return sez(t, [
        due(t, [occ(t, 'Gli stessi dati, da un\'altra sedia', col=GIALLO), tit(t, titolo, size=48, col=GESSO),
                mano(t, 'cinque funzioni, tre punti di vista →', col=VERDE_G, size=30)],
            [sw], peso=(40, 60), valign='start'),
    ], fondo=LAV, pad=(110, 110))


# le «stesse funzioni» viste da ogni figura (testi di olotutor.com)
VISTE = {
    'studente': ('Per lo studente', [
        ('Ripasso dagli errori:', 'sa che cosa ripassare ogni giorno, con un carico uguale per tutti.'),
        ('Certezza:', 'vede quali risposte giuste erano fortuna e quali errori erano convinzioni.'),
        ('Allenamento e prova valutata:', 'si esercita senza che l\'esercizio conti.'),
        ('Presenze:', 'si segna presente da sé con il QR o la targa.'),
        ('Famiglie:', 'vede nel profilo chi lo segue da casa.')], 'Che cosa trova lo studente', STU),
    'docente': ('Per il docente', [
        ('Ripasso dagli errori:', 'gli errori di quiz e partite tornano agli studenti senza preparare niente.'),
        ('Certezza:', 'vede le idee sbagliate diffuse, domanda per domanda.'),
        ('Allenamento e prova valutata:', 'separa l\'esercizio dalla verifica nello stesso strumento.'),
        ('Presenze:', 'fa l\'appello a ore, anche con il QR, la targa o lo scatto ai cartoncini.'),
        ('Famiglie:', 'riceve le giustificazioni da accogliere.')], 'Che cosa trova il docente', DOC),
    'scuola': ('Per la scuola', [
        ('Ripasso dagli errori:', 'un metodo documentato, il ripasso distanziato, applicato allo stesso modo nei corsi in cui il '
                                  'docente lo accende.'),
        ('Certezza:', 'un dato sulla classe che non entra nel voto di nessuno.'),
        ('Allenamento e prova valutata:', 'statistiche, famiglie e tutoraggio contano solo le prove valutate.'),
        ('Presenze:', 'ore della giornata salvate con la presenza, registro coerente, giustificazioni online.'),
        ('Famiglie:', 'accesso in sola lettura, legami tracciati, registro degli accessi.')], 'Che cosa trova la scuola', SCU),
}


def altre_funzioni(t, voci, titolo='Le altre funzioni', occhiello='E poi', fondo=None):
    """La griglia delle altre funzioni: voci = [(icona, titolo, testo)], tre per riga."""
    def box(icona, nome, testo):
        return t.classic('iconbox', {
            'media_bg': {'type': 'solid', 'color': GESSO}, 'preset': 'custom', 'icon_emoji': icona, 'title': nome,
            'description': testo, 'link_url': '', 'link_text': '', 'alignment': 'left', 'text_color': TENUE,
            'icon_size': '1.6', 'icon_position': 'top', 'icon_bg_color': mix(VERDE, 14), 'icon_bg_shape': 'circle',
            'icon_color': VERDE, 'title_font_size': 22, 'title_font_weight': '700', 'title_color': INK, 'shadow': 'none',
            'icon_gap': '18', 'title_gap': '8', 'desc_gap': '0', 'tile_padding': {'top': 30, 'right': 28, 'bottom': 30, 'left': 28},
            'border_radius': radius(22),
            'border': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': FILO},
            'border_hover': {'top': 1, 'right': 1, 'bottom': 1, 'left': 1, 'linked': True, 'style': 'solid', 'color': VERDE},
            'border_hover_duration': 300})
    return sez(t, [
        t.one([occ(t, occhiello), tit(t, titolo, size=48)]),
        griglia(t, [[box(*v)] for v in voci], n=3, gap=20),
    ], fondo=fondo or CARTA2, pad=(110, 110), html_id='altre-funzioni')


def numeri_banda(t, voci, chiaro=True, size=72):
    s = K.numeri(t, voci, colonne=4, size=size, chiaro=chiaro)
    s['settings'].update({'forma': 'banda', 'columns': len(voci) if len(voci) <= 4 else 4, 'columns_tablet_landscape': 4,
                          'columns_tablet': 2, 'columns_mobile': 2, 'value_color': GIALLO if chiaro else VERDE,
                          'label_color': GESSO_T if chiaro else TENUE, 'divider_color': GESSO_F if chiaro else FILO,
                          'label_uppercase': False, 'label_size': 16, 'label_weight': '400',
                          'mono_font_family': "'Atkinson Hyperlegible', sans-serif", 'band_padding_y': 30})
    return s


def hotspot(t, nome, alt, punti, altezza=620, ratio='1/1', fit='cover'):
    """punti: [(x, y, titolo, testo, lato del fumetto)]"""
    return t.classic('hotspot', {
        'image': img(nome), 'image_alt': alt, 'image_height': str(altezza), 'aspect_ratio': ratio,
        'aspect_ratio_custom': ratio, 'object_fit': fit, 'object_position': 'center center',
        'markers': [{'id': '%s-hs-%d' % (t.key, i + 1), 'pos_x': str(x), 'pos_y': str(y), 'title': a, 'description': b,
                     'icon': '', 'tooltip_position': lato, 'link': '', 'link_label': ''}
                    for i, (x, y, a, b, lato) in enumerate(punti)]})


def flip_carta(t, davanti, sotto, dietro, dietro_sotto, f_bg, f_col, b_bg, b_col, alto=420, size=24, cta=None):
    """Una carta da girare senza foto: davanti e dietro di un colore."""
    o = {'front_media': {'type': 'none'}, 'back_media': {'type': 'none'}, 'front_image': '', 'front_video': '',
         'front_icon': '', 'front_title': davanti, 'front_description': sotto, 'front_bg': f_bg, 'front_text_color': f_col,
         'front_text_align': 'left', 'front_valign': 'center', 'back_image': '', 'back_video': '', 'back_icon': '',
         'back_title': dietro, 'back_description': dietro_sotto, 'back_bg': b_bg, 'back_text_color': b_col,
         'back_text_align': 'left', 'back_valign': 'center', 'back_cta_text': '', 'back_cta_url': '',
         'flip_direction': 'horizontal', 'flip_duration': '800', 'flip_trigger': 'hover', 'card_height': str(alto),
         'card_border_radius': '24', 'card_shadow': 'lg', 'title_size': str(size), 'title_weight': '700', 'desc_size': '16',
         'text_effect': 'none', 'tile_padding': {'top': 34, 'right': 34, 'bottom': 34, 'left': 34}}
    if cta:
        o.update({'back_cta_text': cta[0], 'back_cta_url': cta[1], 'back_cta_bg': cta[2], 'back_cta_color': cta[3],
                  'back_cta_radius': '12', 'back_cta_target': False})
    return t.classic('flipcard', o)
