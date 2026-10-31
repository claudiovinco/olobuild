# -*- coding: utf-8 -*-
"""Kit comune dei generatori dei temi «scuri e creativi» (ottobre 2026): i pezzi ricorrenti (titoli di sezione, testi,
pulsanti, immagini, schede, domande, moduli, testata, piede) con i colori del tema passati come token, e i costruttori
delle diapositive e dei livelli dello Slider Pro (proslider) per le aperture.

Uso in un generatore:
    K = Kit(ID, colori={'testo': 'var(--olo-color-text)', ...}, raggio=0, media=MEDIA)
    t = Tpl(ID, 'home'); K.headline(t, 'Titolo <em>accento</em>')
Tutte le tile partono dai campioni dei temi pubblicati (Tpl.classic): l'editor trova ogni campo.
"""
import re

from base import lib, mix, radius, solid  # noqa: F401

TOK = {
    'testo': 'var(--olo-color-text)', 'tenue': 'var(--olo-color-text-muted)', 'acc': 'var(--olo-color-primary)',
    'on_acc': 'var(--olo-color-primary-contrast)', 'acc2': 'var(--olo-color-accent)', 'sec': 'var(--olo-color-secondary)',
    'fondo': 'var(--olo-color-background)', 'sala': 'var(--olo-color-muted)', 'nero': 'var(--olo-color-dark)',
    'chiaro': 'var(--olo-color-light)', 'filo': 'var(--olo-color-border)', 'mono': 'var(--olo-font-family-mono)',
}


class Kit:
    def __init__(self, theme_id, media, raggio=0, peso_titoli='400', corsivo=True, maiuscole_pulsanti=True,
                 tema_chiaro=False, accento_su_media=None):
        self.id = theme_id
        # Temi chiari: sopra foto e video (intro, pannelli, frasi) i testi vanno chiari, non nel colore del testo
        # del tema (che lì è scuro). Nei temi scuri restano quelli di prima.
        self.tema_chiaro = tema_chiaro
        self.media = media
        self.r = raggio
        self.peso = peso_titoli
        self.corsivo = corsivo
        self.maiuscole = maiuscole_pulsanti
        self.c = dict(TOK)
        self.c['soft'] = mix(TOK['testo'], 82)
        self.su_media = TOK['chiaro'] if tema_chiaro else TOK['testo']
        self.soft_media = mix(TOK['chiaro'], 88) if tema_chiaro else self.c['soft']
        self.acc_media = accento_su_media or TOK['acc']

    # ── media ──
    def img(self, name):
        assert name in self.media, name
        return lib(name)

    def video_bg(self, name, overlay=40, color=None, position='center center'):
        return {'type': 'video', 'video_url': self.img(name + '.mp4'), 'video_poster': self.img(name + '.jpg'),
                'video_fit': 'cover', 'image_position': position, 'overlay_color': color or self.c['nero'],
                'overlay_opacity': overlay}

    def photo_bg(self, name, overlay=45, color=None, position='center center'):
        return {'type': 'image', 'image_url': self.img(name), 'image_size': 'cover', 'image_position': position,
                'overlay_color': color or self.c['nero'], 'overlay_opacity': overlay}

    # ── testi ──
    def sechead(self, t, eyebrow, lines, tagline='', size=72, align='left', layout='split', size_mobile=40, chiaro=False):
        c = self.c
        base = c['chiaro'] if chiaro else c['testo']
        alt = self.acc_media if chiaro else c['acc']
        return t.classic('section-header', {
            'eyebrow_show': bool(eyebrow), 'eyebrow_text': eyebrow, 'eyebrow_color': alt, 'eyebrow_dot_color': alt,
            'eyebrow_separator': '', 'eyebrow_font_family': 'mono', 'eyebrow_font_size': 11, 'eyebrow_font_weight': '400',
            'headline_lines': [{'text': l, 'color': base if i % 2 == 0 else alt, 'italic': self.corsivo and i % 2 == 1}
                               for i, l in enumerate(lines)],
            'headline_font_family': 'heading', 'headline_font_size': size, 'headline_line_height': 1.0,
            'headline_font_weight': self.peso, 'headline_align': align,
            'headline_font_size_tablet': max(38, int(size * 0.78)), 'headline_font_size_mobile': size_mobile,
            'tagline_show': bool(tagline), 'tagline_text': tagline, 'tagline_text_italic': False,
            'tagline_text_color': self.soft_media if chiaro else c['tenue'], 'tagline_text_size': 16, 'tagline_font_family': 'body',
            'tagline_font_weight': '', 'tagline_caption': '',
            'layout': (layout if tagline else 'stack') if align == 'left' else 'center', 'split_ratio': '1.25fr 1fr', 'gap': 24,
            'vertical_align': 'end', 'headline_inline': False,
        }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 40, 'padding_left': 0,
                  'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': ''}, 'border_radius': 0})

    def headline(self, t, testo, size=40, color=None, tag='h3', pad_bottom=10, align='left', weight=None, spacing=None,
                 sizes=None, maiuscolo=False, accento=None):
        o = {'tag': tag, 'alignment': align, 'decoration': 'none', 'heading_uppercase': maiuscolo, 'subtitle': '',
             'heading_font_size': size, 'heading_weight': weight or self.peso, 'heading_color': color or self.c['testo'],
             'heading_font': 'heading'}
        if spacing is not None:
            o['heading_letter_spacing'] = spacing
        m = re.search(r'<em>(.*?)</em>', testo)
        if m:
            o.update({'accent_text': m.group(1), 'accent_italic': self.corsivo, 'accent_color': accento or self.c['acc']})
        o['heading'] = testo.replace('<em>', '').replace('</em>', '')
        if sizes:
            o.update({'heading_font_size_tablet': str(sizes[0]), 'heading_font_size_mobile': str(sizes[1])})
        elif size >= 40:
            o.update({'heading_font_size_tablet': str(int(size * 0.8)), 'heading_font_size_mobile': str(max(28, int(size * 0.55)))})
        return t.classic('headline', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})

    def label(self, t, s, color=None, pad_bottom=14, align='left', pad_top=0):
        return t.classic('headline', {'heading': s, 'tag': 'p', 'alignment': align, 'decoration': 'none', 'subtitle': '',
                                      'heading_uppercase': True, 'heading_font_size': 11, 'heading_weight': '400',
                                      'heading_color': color or self.c['tenue'], 'heading_font': self.c['mono'],
                                      'heading_letter_spacing': '1.5'},
                         style={'padding_top': pad_top, 'padding_right': 0, 'padding_bottom': pad_bottom, 'padding_left': 0})

    def text(self, t, html, size=16, color=None, pad=(0, 0, 0, 0), align='left'):
        if not html.startswith('<'):
            html = '<p>' + html + '</p>'
        return t.classic('text-block', {'content': html, 'font_size': size, 'line_height': 1.75, 'text_color': color or self.c['tenue'],
                                        'text_align': align,
                                        'tile_padding': {'top': pad[0], 'right': pad[1], 'bottom': pad[2], 'left': pad[3]}})

    def button(self, t, testo, url, kind='filo', align='left', pad_top=14, chiaro=False):
        c = self.c
        if kind == 'pieno':
            o = {'bg_color': c['acc'], 'text_color': c['on_acc'], 'hover_bg_color': c['testo'], 'hover_text_color': c['fondo']}
        else:
            base = c['chiaro'] if chiaro else c['testo']
            o = {'bg_color': 'transparent', 'text_color': base, 'hover_bg_color': base, 'hover_text_color': c['nero'] if chiaro else c['fondo'],
                 'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0, 'shadow_blur': 0, 'shadow_spread': 1,
                 'shadow_color': mix(base, 45), 'shadow_inset': True}
        o.update({'text': testo, 'url': url, 'alignment': align, 'border_radius': self.r, 'font_size': 12, 'font_weight': '600',
                  'text_transform': 'uppercase' if self.maiuscole else 'none', 'letter_spacing': '2' if self.maiuscole else '0'})
        return t.classic('button', o, style={'padding_top': pad_top})

    def image(self, t, name, alt, ratio='16/9', position='center center', drift=False, lightbox=False, tilt=False):
        adv = {}
        if drift:
            adv['parallax'] = {'y': [{'value': -24, 'position': 0}, {'value': 24, 'position': 100}]}
        if tilt:
            adv.update({'mouse_tilt': True, 'mouse_tilt_intensity': 8})
        return t.classic('image', {'image_url': self.img(name), 'alt_text': alt, 'media_bg': {'type': 'none'}, 'aspect_ratio': 'custom',
                                   'aspect_ratio_custom': ratio, 'height': '', 'object_fit': 'cover', 'object_position': position,
                                   'border_radius': radius(self.r), 'lightbox': lightbox, 'shadow': 'none',
                                   'border': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0, 'linked': True, 'style': 'solid', 'color': ''}},
                         advanced=adv or None)

    def centered(self, t, tiles, side=16.66):
        mid = round(100 - 2 * side, 2)
        return t.row([t.col([], wm='1-6', ws='1-1'), t.col(tiles, wm='2-3', ws='1-1'), t.col([], wm='1-6', ws='1-1')],
                     layout='custom', custom='%s,%s,%s' % (side, mid, side), valign='center')

    def specs(self, t, pairs, chiaro=False):
        c = self.c
        return t.classic('desclist', {
            'items': [{'term': a, 'definition': b, 'icon': ''} for a, b in pairs],
            'layout': 'vertical', 'term_color': c['soft'] if chiaro else c['tenue'], 'term_font_size': '11', 'term_font_weight': '400',
            'definition_color': c['chiaro'] if chiaro else c['testo'], 'definition_font_size': '16', 'separator': True,
            'border_color': mix(c['chiaro'], 22) if chiaro else c['filo']},
            style={'margin_top': 8, 'margin_bottom': 6})

    def contacts(self, t, voci):
        """voci: [(icona, testo, link)]"""
        return t.classic('iconlist', {
            'items': [{'icon': i, 'text': s, 'link': l, 'color': ''} for i, s, l in voci],
            'icon_color': self.c['acc'], 'icon_size': '16', 'text_color': self.c['testo'], 'text_size': '15', 'gap': '12',
            'icon_shape': 'none', 'icon_bg_color': '', 'divider': False, 'layout': 'vertical',
        }, style={})

    def faq(self, t, pairs, prefix):
        c = self.c
        panels = [{'id': '%s-%d' % (prefix, i + 1), 'title': q, 'content': a, 'image': '', 'video': '', 'icon': ''}
                  for i, (q, a) in enumerate(pairs)]
        return t.classic('accordion', {
            'panels': panels, 'default_open': 'none', 'icon_style': 'plus', 'icon_position': 'right',
            'header_bg': '', 'header_bg_active': '', 'content_bg': '',
            'header_text_color': c['testo'], 'header_text_color_active': c['acc'], 'header_font_size': 22,
            'header_font_weight': self.peso, 'header_font_family': 'var(--olo-font-family-heading)', 'content_font_size': 15,
            'text_color': c['tenue'], 'icon_color': c['acc'], 'faq_schema': True,
            'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'linked': False, 'style': 'solid', 'color': c['filo']},
            'header_padding': {'top': 22, 'right': 0, 'bottom': 22, 'left': 0},
            'content_padding': {'top': 0, 'right': 48, 'bottom': 24, 'left': 0},
        }, style={})

    def domande(self, t, titolo, righe, pairs, prefix, bg=True, nota=''):
        sinistra = [self.sechead(t, 'Le domande', [titolo, righe], size=48, layout='stack', size_mobile=34)]
        if nota:
            sinistra.append(self.text(t, nota))
        return t.section([
            t.row([t.col(sinistra, wm='1-3', ws='1-1'), t.col([self.faq(t, pairs, prefix)], wm='2-3', ws='1-1')],
                  layout='custom', custom='33,67', gap=64, valign='start'),
        ], bg=solid(self.c['sala']) if bg else None, padding='large')

    # ── blocchi ──
    def intro(self, t, eyebrow, lines, body, bg=None, size=100, overlay_text=False, pad_top=200, advanced=None):
        sec = t.section([
            t.row([t.col([self.sechead(t, eyebrow, lines, size=size, size_mobile=46,
                                       chiaro=self.tema_chiaro and overlay_text)], wm='3-5', ws='1-1'),
                   t.col([self.text(t, body, size=17, color=self.soft_media if overlay_text else None)], wm='2-5', ws='1-1')],
                  layout='custom', custom='60,40', gap=56, valign='end'),
        ], bg=bg, padding='large', style={'padding_top': pad_top, 'padding_bottom': 72})
        if advanced:
            sec['advanced'].update(advanced)
        return sec

    def pannello(self, t, foto, eyebrow, titolo, testo, link_t='', link_u='', overlay=56, pos='center center', effetto='reveal-h',
                 video=False):
        blocco = [self.label(t, eyebrow, color=self.acc_media),
                  self.headline(t, titolo, size=80, tag='h2', pad_bottom=16, color=self.su_media, accento=self.acc_media),
                  self.text(t, testo, size=16, color=self.soft_media)]
        if link_t:
            blocco.append(self.button(t, link_t, link_u, pad_top=10, chiaro=self.tema_chiaro))
        bg = self.video_bg(foto, overlay=overlay, position=pos) if video else self.photo_bg(foto, overlay=overlay, position=pos)
        return t.section([
            t.row([t.col(blocco, wm='1-2', ws='1-1'), t.col([], wm='1-2', ws='1-1')], layout='custom', custom='52,48',
                  valign='end', anim=None),
        ], bg=bg, padding='large', width='large', sticky_effect=effetto, sticky_h_align='bottom',
            style={'padding_top': 140, 'padding_bottom': 80})

    def video_frase(self, t, video, eyebrow, titolo, testo, overlay=45, pad=230, extra=None):
        tiles = [self.label(t, eyebrow, color=self.soft_media, align='center'),
                 self.headline(t, titolo, size=96, tag='h2', align='center', pad_bottom=18, color=self.su_media,
                               accento=self.acc_media),
                 self.text(t, testo, size=16, color=self.soft_media, align='center')]
        if extra:
            tiles += extra
        return t.section([self.centered(t, tiles)], bg=self.video_bg(video, overlay=overlay), padding='xlarge',
                         style={'padding_top': pad, 'padding_bottom': pad})

    def modulo(self, t, campi, oggetto, invio, grazie):
        c = self.c
        return t.classic('form', {
            'fields': campi, 'email_subject': oggetto, 'submit_text': invio, 'submit_icon': '', 'success_message': grazie,
            'submit_full_width': False, 'submit_alignment': 'left', 'label_color': c['testo'], 'label_size': '11',
            'label_weight': '500', 'label_transform': 'uppercase', 'label_letter_spacing': '1.5', 'input_bg': 'transparent',
            'input_color': c['testo'], 'input_border_color': mix(c['testo'], 30), 'input_border_width': '1',
            'input_border_style': 'solid', 'input_radius': self.r, 'input_focus_border': c['acc'], 'input_focus_shadow': False,
            'gap': '20', 'submit_bg': c['acc'], 'submit_color': c['on_acc'], 'submit_radius': self.r, 'submit_padding_x': '30',
            'submit_padding_y': '16', 'submit_font_size': '12', 'submit_font_weight': '600',
            'submit_text_transform': 'uppercase' if self.maiuscole else 'none', 'submit_letter_spacing': '2' if self.maiuscole else '0',
            'submit_hover_bg': c['testo'], 'privacy_checkbox': True,
        }, style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
                  'border_radius': 0})

    @staticmethod
    def campo(tipo, etichetta, nome, obbligatorio=False, larghezza='1-2', opzioni='', segnaposto='', param=''):
        c = {'field_type': tipo, 'label': etichetta, 'name': nome, 'placeholder': segnaposto, 'required': obbligatorio,
             'width': larghezza, 'options': opzioni, 'icon': ''}
        if param:
            c['param'] = param
        return c

    def sezione_modulo(self, t, html_id, eyebrow, righe, testo, voci, campi, oggetto, invio, grazie):
        return t.section([
            t.row([t.col([self.sechead(t, eyebrow, righe, size=72, layout='stack', size_mobile=42), self.text(t, testo),
                          self.contacts(t, voci)], wm='2-5', ws='1-1'),
                   t.col([self.modulo(t, campi, oggetto, invio, grazie)], wm='3-5', ws='1-1')],
                  layout='custom', custom='40,60', gap=72, valign='start'),
        ], bg=solid(self.c['sala']), padding='large', html_id=html_id, style={'padding_top': 130, 'padding_bottom': 130})

    def mappa(self, t, lat, lng, popup, scuro=True, zoom='15', hex_marker='#ffffff'):
        return t.classic('osmmap', {'latitude': lat, 'longitude': lng, 'zoom': zoom, 'height': '440', 'marker': True,
                                    'marker_popup': popup, 'scroll_wheel_zoom': False, 'tile_layer': 'dark' if scuro else 'light',
                                    'border_radius': radius(self.r), 'marker_color': hex_marker}, style={})

    # ── testata e piede ──
    def testata(self, t, logo, logo_size='22', overlay=True, scuro=True):
        c = self.c
        mm = t.classic('megamenu', {
            'menu_id': 'auto', 'logo_text': logo, 'logo_text_size': logo_size, 'logo_text_color': c['testo'],
            'logo_text_font': 'heading', 'logo_text_weight': self.peso, 'logo_text_spacing': 0, 'logo_text_transform': '',
            'logo_mark': '', 'logo_mark_bg': '', 'logo_dot': True, 'logo_position': 'left',
            'layout': 'right', 'header_mode': 'overlay' if overlay else 'classic', 'sticky': True,
            'sticky_bg': c['fondo'], 'sticky_shadow': False, 'sticky_shrink': True, 'sticky_show_on_up': True,
            'nav_bg': 'transparent', 'text_color': c['testo'], 'hover_color': c['acc'], 'active_color': c['acc'],
            'font_size': '12', 'font_weight': '500', 'text_transform': 'uppercase', 'letter_spacing': '2', 'item_gap': '32',
            'bar_width': 'wide', 'nav_height': '84', 'bar_padding': {'top': 10, 'right': 40, 'bottom': 10, 'left': 40},
            'hover_effect': 'underline', 'hover_effect_color': c['acc'],
            'button_mode': 'last', 'btn_bg': c['acc'], 'btn_color': c['on_acc'], 'btn_hover_color': c['on_acc'],
            'btn_radius': radius(self.r), 'btn_padding': {'top': 11, 'right': 20, 'bottom': 11, 'left': 20}, 'btn_hover_effect': 'none',
            'btn_font_weight': '600', 'btn_transform': 'uppercase', 'btn_letter_spacing': '2',
            'social_in_navbar': False, 'social_in_mobile': True, 'social_facebook': '', 'social_instagram': '#',
            'social_linkedin': '', 'social_youtube': '', 'social_tiktok': '', 'social_x': '',
            'mobile_style': 'fullscreen', 'mobile_bg': c['nero'] if scuro else c['fondo'], 'mobile_text_color': c['testo'],
            'mobile_accent_color': c['acc'], 'hamburger_style': 'minimal', 'hamburger_color': c['testo'],
            'mobile_font_size': '40', 'mobile_link_size': '40', 'mobile_link_font': 'heading', 'mobile_link_weight': self.peso,
            'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(c['testo'], 10)},
            'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
        })
        return [t.section([t.one([mm], anim=None)], width='fullbleed', padding='remove-vertical')]

    def piede(self, t, marchio, claim, pagine, titolo_terza, terza_html, voci, legale, social, marchio_size=140, fondo=None):
        c = self.c
        brand = self.headline(t, marchio, size=marchio_size, tag='p', pad_bottom=0, spacing=-3,
                              sizes=(int(marchio_size * 0.64), int(marchio_size * 0.38)))
        nav = t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in pagine], 'style': 'default',
                                'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                'link_color': c['testo'], 'link_hover_color': c['acc'], 'active_color': c['acc'],
                                'font_size': '15', 'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
        soc = t.classic('social', {'links': [{'platform': p, 'url': u} for p, u in social],
                                   'size': '30', 'alignment': 'right', 'icon_color': c['tenue'], 'hover_effect': 'none'})
        totop = t.classic('totop', {'icon_color': c['nero'], 'posizione': 'fisso', 'style': 'default'})
        top = t.row([
            t.col([self.text(t, claim, size=15, pad=(0, 0, 18, 0)), self.contacts(t, voci)], wm='1-2', ws='1-1'),
            t.col([self.label(t, 'Le pagine', color=c['acc']), nav], wm='1-4', ws='1-2', width_default='1-2'),
            t.col([self.label(t, titolo_terza, color=c['acc']), self.text(t, terza_html, size=15, color=c['testo'])],
                  wm='1-4', ws='1-2', width_default='1-2'),
        ], layout='custom', custom='50,25,25', gap=48, valign='start', anim=None)
        bottom = t.row([t.col([self.text(t, legale, size=12), totop], wm='2-3', ws='1-1'), t.col([soc], wm='1-3', ws='1-1')],
                       layout='custom', custom='66.66,33.33', valign='center', anim=None,
                       style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': c['filo']},
                              'padding_top': 22, 'margin_top': 24})
        return [t.section([top, t.one([brand], anim='fade', style={'margin_top': 64}), bottom], bg=solid(fondo or c['nero']), padding='large',
                          style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': c['filo']}})]

    # ── Slider Pro ──
    @staticmethod
    def mob(px):
        """Dimensione di un livello sul telefono: sotto i 640 px la tela dello slider è larga 640 e si scala sulla
        larghezza del telefono, quindi 40 px veri su un telefono da 390 sono 66 unità."""
        return int(round(px * 640 / 390))

    @staticmethod
    def livello(lid, tipo='text', contenuto='', x=6, y=40, w=60, h='auto', size=48, peso='400', colore='var(--olo-color-light)',
                tag='p', align='left', entra='fadeInUp', ritardo=200, durata=800, esce='fadeOutDown', **extra):
        L = {'id': lid, 'type': tipo, 'content': contenuto, 'tag': tag, 'x': x, 'y': y, 'width': w, 'height': h,
             'fontSize': size, 'fontWeight': peso, 'fontStyle': 'normal', 'color': colore, 'textAlign': align, 'bgColor': '',
             'borderRadius': 0, 'padding': 0, 'opacity': 100, 'lineHeight': 1.05, 'letterSpacing': 0, 'textTransform': 'none',
             'animIn': entra, 'animInDuration': durata, 'animInDelay': ritardo, 'animOut': esce, 'animOutDuration': 500,
             'animOutDelay': 0, 'animEasing': 'cubic-bezier(.22,1,.36,1)'}
        L.update(extra)
        return L

    @staticmethod
    def diapositiva(sid, sfondo, livelli, durata=6000, tab=''):
        s = {'id': sid, 'background': sfondo, 'duration': durata, 'layers': livelli}
        if tab:
            s['tabLabel'] = tab
        return s

    def sfondo_foto(self, name, overlay=0.4, kb=True, scala=1.15, durata=9000, verso='in', colore=None):
        return {'type': 'image', 'image': self.img(name), 'video': '', 'color': '', 'overlay': colore or self.c['nero'],
                'overlayOpacity': overlay, 'kenBurns': kb, 'kenBurnsScale': scala, 'kenBurnsDuration': durata,
                'kenBurnsDirection': verso}

    def sfondo_video(self, name, overlay=0.4, colore=None):
        return {'type': 'video', 'image': self.img(name + '.jpg'), 'video': self.img(name + '.mp4'), 'color': '',
                'overlay': colore or self.c['nero'], 'overlayOpacity': overlay, 'kenBurns': False}

    def proslider(self, t, slides, globali=None, altezza=None, transizione='fade', durata_trans=900, **opz):
        o = {'preset': 'custom', 'globalBackground': {'type': 'color', 'color': self.c['nero']},
             'height': altezza or {'mode': 'vh', 'value': 100}, 'heightNotebook': None, 'heightTablet': None,
             'heightMobile': {'mode': 'px', 'value': 1400}, 'autoplay': True, 'autoplaySpeed': 6500, 'pauseOnHover': True,
             'loop': True, 'transition': transizione, 'transitionDuration': durata_trans, 'showArrows': True,
             'arrowStyle': 'minimal', 'showDots': True, 'dotStyle': 'dash', 'showProgressBar': True,
             'progressBarColor': self.c['acc'], 'progressBarHeight': 2, 'keyboard': True, 'swipe': True, 'mouseWheel': False,
             'sizingMode': 'fullwidth', 'parallax': True, 'parallaxType': 'mouse', 'parallaxIntensity': 6, 'scrollEffect': 'none',
             'carousel': False, 'carouselWidth': 80, 'carouselGap': 10, 'carouselSideScale': 0.85, 'carousel3D': False,
             'showThumbs': False, 'thumbPosition': 'bottom', 'showTabs': False, 'tabPosition': 'bottom',
             'scrollTimeline': False, 'scrollTimelineDistance': 2000, 'slides': slides, 'globalLayers': globali or []}
        o.update(opz)
        return t.classic('proslider', o, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})

    def apertura(self, t, slider):
        return t.section([t.one([slider], anim=None)], width='fullbleed', padding='remove-vertical')

    @staticmethod
    def tl(x, y, punti, durata, loop=True, ritardo=0, curva='linear', fine_con_slide=True):
        """La timeline di un livello: punti = [(ms, {proprietà})]. x e y vanno in ogni fotogramma (sono left/top):
        si mettono quelli del livello dove il punto non li dà. Con rotationX/Y nei fotogrammi la prospettiva c'è
        solo dove la rotazione 3D non è zero: un valore minimo (0.01) negli altri tiene la prospettiva costante."""
        kfs = []
        for punto in punti:  # (ms, proprietà) o (ms, proprietà, curva fino al fotogramma dopo)
            ms, p = punto[0], punto[1]
            q = {'x': x, 'y': y}
            q.update(p)
            kfs.append({'time': ms, 'props': q, 'easing': punto[2] if len(punto) > 2 else curva})
        o = {'duration': durata, 'delay': ritardo, 'loop': loop, 'keyframes': kfs}
        if not fine_con_slide:
            o['endWithSlide'] = False
        return o

    def numeri(self, t, voci, colonne=4, size=54, chiaro=False):
        c = self.c
        return t.classic('statstrip', {
            'items': [{'value': v, 'label': l} for v, l in voci], 'columns': colonne, 'columns_tablet': 2,
            'columns_mobile': 2, 'band_padding_y': 28, 'show_dividers': True,
            'divider_color': mix(c['chiaro'], 22) if chiaro else c['filo'], 'value_font_family': 'heading',
            'value_color': c['chiaro'] if chiaro else c['testo'], 'value_size': size, 'value_weight': self.peso,
            'label_color': self.soft_media if chiaro else c['tenue'], 'label_size': 12, 'label_uppercase': True,
            'align': 'left', 'mono_font_family': ''}, style={})

    def squadra(self, t, persone, ratio='4/5', size='300', sfondo=None, testo=None):
        """persone: [(foto, nome, ruolo, bio, punto focale)] → una riga di schede della squadra."""
        c = self.c
        cols = []
        for foto, nome, ruolo, bio, pos in persone:
            cols.append(t.col([t.classic('team', {
                'photo': self.img(foto), 'hover_image': '', 'hover_video': '', 'name': nome, 'role': ruolo, 'bio': bio,
                'link_text': '', 'link_url': '', 'photo_size': size, 'photo_ratio': ratio, 'photo_fit': 'cover',
                'photo_object_position': pos, 'photo_shape': 'rounded', 'photo_radius': str(self.r),
                'photo_border_width': '0', 'photo_shadow': 'none', 'info_bg_color': sfondo or c['sala'],
                'info_text_color': testo or c['testo'], 'info_padding': '24', 'info_radius': str(self.r),
                'info_align': 'left', 'name_size': '24', 'name_weight': self.peso, 'role_size': '13',
                'role_color': c['acc'], 'bio_size': '15', 'bg_color': 'transparent'}, style={})],
                wm='1-%d' % len(persone), ws='1-1'))
        return t.row(cols, layout='-'.join([str(100 // len(persone))] * len(persone)), gap=24, valign='start')

    def piani(self, t, piani, cta, link, nota='', sfondo=None, valuta='€'):
        """piani: [(nome, prezzo, periodo, 'voce\\nvoce', popolare, badge)] → una riga di listini."""
        c = self.c
        cols = []
        for nome, prezzo, periodo, voci, pop, badge in piani:
            cols.append(t.col([t.classic('pricing', {
                'plan_name': nome, 'price': prezzo, 'currency': valuta, 'currency_position': 'after', 'period': periodo,
                'features': voci, 'check_style': 'circle-check', 'cta_text': cta, 'cta_url': link,
                'cta_bg_color': c['acc'] if pop else 'transparent', 'cta_text_color': c['on_acc'] if pop else c['testo'],
                'cta_border_width': '0' if pop else '1', 'cta_border_color': c['testo'], 'cta_radius': str(self.r),
                'cta_hover_bg_color': c['testo'], 'additional_info': nota, 'is_popular': pop, 'badge_text': badge,
                'badge_bg_color': c['acc2'], 'badge_text_color': c['nero'], 'bg_color': sfondo or c['sala'],
                'accent_color': c['acc'], 'text_color': c['testo'], 'price_color': c['testo'],
                'border_radius': str(self.r), 'shadow': 'md' if pop else 'none'}, style={})],
                wm='1-%d' % len(piani), ws='1-1'))
        return t.row(cols, layout='-'.join([str(100 // len(piani))] * len(piani)), gap=20, valign='start')

    def coverflow(self, t, voci, altezza=440, larghezza=360):
        """La galleria a coverflow 3D (Galleria pro, nastro): voci = [(foto, alt, titolo, sottotitolo)]."""
        return t.classic('progallery', {
            'images': [{'url': self.img(f), 'alt': a, 'caption': ti, 'subtitle': s, 'text': ''} for f, a, ti, s in voci],
            'layout_family': 'strip', 'layout': 'strip_coverflow', 'columns': 4, 'gap': 12, 'object_fit': 'cover',
            'thumb_radius': self.r, 'mobile_columns': 1, 'filmstrip_autoplay': True, 'filmstrip_speed': 3.5,
            'filmstrip_dots': 'lines', 'filmstrip_dots_color': self.c['acc'], 'filmstrip_item_width': larghezza,
            'filmstrip_center_zoom': 1.12, 'filmstrip_side_tilt': 38, 'img_height': '%dpx' % altezza, 'entrance': 'none',
            'hover_effect': 'none', 'lightbox': True, 'show_caption': True}, style={})


def tema(theme_id, nome, categoria, zona, tags, descrizione, menu, pagine, hexes, font_corpo, font_titoli, font_mono,
         gfonts, corsivo_di=None, peso_titoli='400', raggio=0, grana=5, titoli_line='1.0', titoli_spazio='-0.01em'):
    """Il dizionario di theme.json. pagine: [(chiave, titolo, slug|None per la home)]"""
    templates = {'404': {'file': '404.json', 'type': '404', 'title': '%s — Pagina 404' % nome},
                 'header': {'file': 'header.json', 'type': 'header', 'title': '%s — Header' % nome},
                 'footer': {'file': 'footer.json', 'type': 'footer', 'title': '%s — Footer' % nome}}
    pg = {}
    for k, titolo, slug in pagine:
        templates[k] = {'file': k + '.json', 'type': 'page', 'title': '%s — %s' % (nome, titolo)}
        pg[k] = {'title': titolo, 'template': k, 'set_as_homepage': True} if slug is None else {'title': titolo, 'template': k, 'slug': slug}
    r = '%dpx' % raggio
    return {
        'id': theme_id, 'name': nome, 'author': 'Olobuild', 'version': '1.0.0', 'preview_v': '1', 'preview_mobile_v': '1',
        'category': categoria, 'zone': zona, 'tags': tags, 'description': descrizione,
        'menu': {'name': '%s — menu' % nome, 'items': [{'title': a, 'url': b} for a, b in menu]},
        'templates': templates, 'activate': {'404': '404', 'header': 'header', 'footer': 'footer'}, 'pages': pg,
        'cursor': {'enabled': True, 'hide_system': False, 'ring_size': 30, 'ring_color': hexes['light'], 'ring_width': 1,
                   'dot_size': 5, 'dot_color': hexes['primary'], 'hot_scale': 2.0, 'hot_color': hexes['primary'],
                   'hot_fill': 'rgba(%d,%d,%d,0.12)' % tuple(int(hexes['primary'][i:i + 2], 16) for i in (1, 3, 5)),
                   'blend_mode': 'normal', 'magnetic_selector': 'a, button', 'pull_strength': 0.3, 'follow_easing': 0.2},
        'styles': {
            'colors': dict(hexes),
            'typography': {
                'font_family': font_corpo, 'font_family_heading': font_titoli, 'font_family_mono': font_mono,
                'font_size_base': '16px', 'line_height': '1.7', 'font_weight_body': '400', 'font_weight_heading': peso_titoli,
                'heading_line_height': titoli_line, 'heading_letter_spacing': titoli_spazio, 'heading_text_transform': 'none',
                'letter_spacing': '0', 'font_size_h1': '5.5rem', 'font_size_h2': '3.6rem', 'font_size_h3': '2rem',
                'font_size_h4': '1.4rem', 'font_size_h5': '1.1rem', 'font_size_h6': '1rem', 'font_size_h1_mobile': '2.8rem',
                'font_size_h2_mobile': '2.1rem', 'font_size_h3_mobile': '1.5rem',
            },
            'google_fonts': gfonts, 'google_fonts_weights': '300;400;500;600;700', 'google_fonts_italic': corsivo_di or [],
            'buttons': {'font_size': '12px', 'font_weight': '600', 'padding_x': '24px', 'padding_y': '15px', 'border_radius': r,
                        'text_transform': 'uppercase', 'letter_spacing': '2px', 'hover_brightness': '100'},
            'layout': {'border_radius': r, 'border_radius_large': '%dpx' % (raggio * 2), 'container_max_width': '1280px',
                       'container_narrow': '760px', 'container_wide': '1440px'},
            'forms': {'field_bg': 'transparent', 'field_border_color': hexes['border'], 'field_border_width': '1',
                      'field_border_radius': r, 'field_padding': '13px 16px', 'field_font_size': '16px',
                      'focus_border_color': hexes['primary'], 'focus_shadow': 'none',
                      'label_font_size': '11px', 'label_font_weight': '500', 'label_margin_bottom': '8px'},
            'links': {'color': '', 'hover_color': '', 'decoration': 'none', 'hover_decoration': 'underline'},
            'grain': {'enabled': grana > 0, 'opacity': grana, 'scale': 170},
        },
    }
