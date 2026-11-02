# -*- coding: utf-8 -*-
"""Kit dei negozi online (WooCommerce) dei temi di novembre 2026: le parti che ogni negozio fatto bene ha — la barra
delle offerte, la testata con la ricerca dal vivo fra i prodotti, l'account, i preferiti e il mini carrello, il piede
con la newsletter e i pagamenti — e le pagine del negozio gestite da OLObuild (Template WooCommerce): il catalogo coi
filtri, le offerte col conto alla rovescia, la scheda prodotto (galleria col video del prodotto, taglie e varianti,
schede, set, correlati, visti di recente), il carrello, la cassa a passi, l'account, i preferiti e l'assistenza.

Tutto con tile vere e colori del tema (token): ogni generatore passa il suo Kit e sceglie raggio, proporzioni delle
foto delle card, testata chiara o scura. Uso:
    N = Negozio(K, 'Orlo', P, raggio=2, ratio='3-4', card='none', hover='swap')
    N.testata(Tpl(ID, 'header'), 'orlo', icona='scissors', ...)
"""
from base import mix, radius, solid
import testate


class Negozio:
    def __init__(self, K, nome, P, raggio=8, ratio='1-1', card='border', hover='swap', testata_scura=False,
                 consegna='', evidenza='', marca='', peso_titoli='600', valuta='€'):
        self.K = K
        self.c = K.c
        self.nome = nome
        self.P = P                    # pagine: home, negozio, offerte, carrello, cassa, account, preferiti, assistenza…
        self.r = raggio
        self.ratio = ratio
        self.card = card
        self.hover = hover
        self.scura = testata_scura
        self.consegna = consegna
        self.evidenza = evidenza
        self.marca = marca
        self.peso = peso_titoli
        self.valuta = valuta
        self.cta_media = None         # (fondo, testo) del pulsante delle diapositive, se non il primario

    # ── mattoncini ────────────────────────────────────────────────────────────────────────────────────────────────
    def cat_url(self, slug):
        return self.P['negozio'] + '?product_cat=' + slug

    def titolo(self, t, testo, size=32, color=None, tag='h2', align='left', pad_bottom=8, sizes=None):
        accento = self.c['sec'] if color == self.c['chiaro'] else None
        return self.K.headline(t, testo, size=size, color=color, tag=tag, align=align, pad_bottom=pad_bottom,
                               weight=self.peso, sizes=sizes, accento=accento)

    def occhiello(self, t, testo, color=None, align='left', pad_bottom=10):
        return self.K.label(t, testo, color=color or self.c['acc'], align=align, pad_bottom=pad_bottom)

    def testo(self, t, html, size=16, color=None, align='left', pad=(0, 0, 0, 0)):
        return self.K.text(t, html, size=size, color=color, align=align, pad=pad)

    def pulsante(self, t, testo, url, pieno=True, chiaro=False, align='left', pad_top=14, icona=''):
        c = self.c
        if pieno:
            o = {'bg_color': c['acc'], 'text_color': c['on_acc'], 'hover_bg_color': c['chiaro'] if chiaro else c['testo'],
                 'hover_text_color': c['nero'] if chiaro else c['fondo']}
        else:
            base = c['chiaro'] if chiaro else c['testo']
            o = {'bg_color': 'transparent', 'text_color': base, 'hover_bg_color': base,
                 'hover_text_color': c['nero'] if chiaro else c['fondo'], 'shadow': 'custom', 'shadow_h': 0, 'shadow_v': 0,
                 'shadow_blur': 0, 'shadow_spread': 1, 'shadow_color': mix(base, 50), 'shadow_inset': True}
        o.update({'text': testo, 'url': url, 'alignment': align, 'border_radius': self.r, 'font_size': 14,
                  'font_weight': '600', 'text_transform': 'none', 'letter_spacing': '0'})
        if icona:
            o.update({'icon': icona, 'icon_position': 'after', 'icon_spacing': 8, 'icon_size': 16})
        return t.classic('button', o, style={'padding_top': pad_top})

    def link(self, t, testo, url, chiaro=False, align='right', pad_top=4):
        c = self.c
        return t.classic('button', {'text': testo, 'url': url, 'alignment': align, 'bg_color': 'transparent',
                                    'text_color': c['chiaro'] if chiaro else c['testo'], 'hover_bg_color': 'transparent',
                                    'hover_text_color': c['sec'] if chiaro else c['acc'], 'border_radius': 0, 'font_size': 14,
                                    'font_weight': '600',
                                    'text_transform': 'none', 'letter_spacing': '0', 'padding_x': 0, 'padding_y': 4,
                                    'tile_padding': {'top': 4, 'right': 0, 'bottom': 4, 'left': 0}, 'icon': 'arrow-right',
                                    'icon_position': 'after', 'icon_spacing': 6, 'icon_size': 15}, style={'padding_top': pad_top})

    def testata_sezione(self, t, eyebrow, testo, url='', link_testo='Vedi tutti', chiaro=False, size=34):
        c = self.c
        sinistra = ([self.occhiello(t, eyebrow, color=mix(c['chiaro'], 80) if chiaro else None)] if eyebrow else []) + \
            [self.titolo(t, testo, size=size, color=c['chiaro'] if chiaro else None, sizes=(30, 26))]
        destra = [self.link(t, link_testo, url, chiaro=chiaro)] if url else []
        return t.row([t.col(sinistra, wm='2-3', ws='2-3', width_default='2-3'), t.col(destra, wm='1-3', ws='1-3', width_default='1-3')],
                     layout='custom', custom='70,30', valign='end', anim=None, stack=False, style={'margin_bottom': 20})

    def prodotti(self, t, **kw):
        """La griglia (o il carosello) dei prodotti di WooCommerce con le parti della card di un negozio grande."""
        c = self.c
        o = {
            'posts_per_page': 8, 'columns': 4, 'columns_tablet': 3, 'columns_mobile': 2, 'orderby': 'date', 'order': 'DESC',
            'category': '', 'tag': '', 'on_sale': False, 'featured': False,
            'show_image': True, 'show_title': True, 'show_price': True, 'show_rating': True, 'show_rating_count': True,
            'show_add_to_cart': True, 'show_badge': True, 'show_compare': False, 'show_wishlist': True,
            'mostra_risparmio': True, 'mostra_scorta': True, 'marca_attributo': self.marca,
            'consegna_testo': self.consegna, 'testo_evidenza': self.evidenza, 'titolo_righe': 2,
            'image_ratio': self.ratio, 'gap': 20, 'card_style': self.card, 'hover_effect': self.hover,
            'card_bg': c['chiaro'] if self.card != 'none' else 'transparent',
            'card_radius': radius(self.r), 'card_padding': {'top': 12, 'right': 12, 'bottom': 16, 'left': 12} if self.card != 'none'
            else {'top': 0, 'right': 0, 'bottom': 8, 'left': 0},
            'title_color': c['testo'], 'price_color': c['testo'], 'sale_color': c['acc2'], 'button_bg': c['acc'],
            'button_color': c['on_acc'], 'badge_bg': c['acc2'], 'pagination': False, 'layout': 'grid', 'preset': 'custom',
            'carousel_autoplay': False, 'carousel_loop': False, 'carousel_arrows': True, 'carousel_dots': False,
            'carousel_speed': 4000, 'shadow': 'none',
        }
        o.update(kw)
        return t.bare('woo_products', o)

    def anteprima(self, t, testo='Anteprima veloce'):
        """Quick View: «Anteprima veloce» sulle foto delle card della pagina."""
        c = self.c
        return t.bare('woo_quickview', {
            'button_text': testo, 'button_style': 'filled', 'show_gallery': True, 'show_add_to_cart': True, 'show_price': True,
            'show_rating': True, 'show_excerpt': True, 'show_meta': False, 'modal_width': 940, 'image_width': 50,
            'accent_color': c['acc'], 'text_color': c['testo']})

    def categorie(self, t, colonne=6, raggio_foto=None, sotto=True, ratio='1-1', scorri=True, overlay=False, colonne_telefono=2):
        c = self.c
        return t.bare('woo_categories', {
            'columns': colonne, 'columns_tablet': min(colonne, 4), 'columns_mobile': colonne_telefono, 'show_image': True, 'show_count': True,
            'show_description': False, 'hide_empty': True, 'orderby': 'name', 'parent_only': True, 'overlay': overlay,
            'text_color': c['chiaro'] if overlay else c['testo'], 'title_tag': 'h3', 'gap': 18, 'image_ratio': ratio,
            'border_radius': self.r if raggio_foto is None else raggio_foto, 'hover_effect': 'zoom',
            'posizione_testo': 'sotto' if sotto else 'sopra', 'scorri_mobile': scorri, 'pagina_negozio': self.P['negozio']})

    def conto(self, t, size=40, chiaro=True, align='left', giorni=False, secondi=True):
        """Il conto alla rovescia delle offerte: ricomincia ogni giorno (evergreen)."""
        c = self.c
        col = c['chiaro'] if chiaro else c['testo']
        return t.classic('countdown', {
            'preset': 'custom', 'countdown_style': 'boxes', 'countdown_type': 'evergreen', 'evergreen_hours': 23,
            'evergreen_minutes': 59, 'evergreen_loop': True, 'expire_action': 'none', 'display_mode': 'inline', 'alignment': align,
            'show_days': giorni, 'show_hours': True, 'show_minutes': True, 'show_seconds': secondi, 'label_days': 'giorni',
            'label_hours': 'ore', 'label_minutes': 'min', 'label_seconds': 'sec', 'separator': ':',
            'bg_color': 'transparent', 'text_color': col, 'accent_color': c['sec'] if chiaro else c['acc'],
            'separator_color': mix(col, 50),
            'number_font_size': size, 'number_font_size_tablet': int(size * 0.9), 'number_font_size_mobile': int(size * 0.8),
            'number_font_weight': '700', 'label_font_size': 11, 'label_font_weight': '600', 'separator_font_size': int(size * 0.7),
            'item_min_width': int(size * 1.7), 'item_min_width_tablet': int(size * 1.5), 'item_min_width_mobile': int(size * 1.4),
            'item_bg_color': mix(col, 10), 'item_radius': self.r, 'item_padding': 10,
        }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})

    def promesse(self, t, voci, chiaro=False, align='center'):
        """La fascia delle promesse: [(icona, testo)]."""
        c = self.c
        col = c['chiaro'] if chiaro else c['testo']
        return t.classic('trust-strip', {
            'items': [{'icon': i, 'icon_color': c['acc'] if not chiaro else mix(c['chiaro'], 90), 'text': s, 'badge': ''} for i, s in voci],
            'text_color': col, 'text_size': 14, 'font_family': 'body', 'align': align, 'flow': 'wrap', 'gap': 28,
            'variant': 'inline', 'icon_size': 18, 'separator_char': '', 'separator_color': c['filo'],
        }, style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})

    def banner(self, t, titolo, accento, sotto, cta, url, bg, pad=64, size=40, cta2=('', '')):
        """Un banner promozionale sopra una foto o un video: il testo in basso, sulla sfumatura che sale da lì."""
        c = self.c
        if isinstance(bg, dict) and bg.get('type') in ('image', 'video'):
            bg = dict(bg, overlay_color=c['nero'], overlay_opacity=86, overlay_fade='bottom', overlay_fade_reach=70)
        r = self.r * 2
        return t.classic('cta-banner', {
            'headline': titolo, 'headline_accent': accento, 'headline_accent_italic': self.K.corsivo, 'subtitle': sotto,
            'cta_text': cta, 'cta_url': url, 'cta_target': '_self', 'cta2_text': cta2[0], 'cta2_url': cta2[1],
            'bg': bg, 'text_color': c['chiaro'], 'accent_color': mix(c['sec'], 40, c['chiaro']), 'subtitle_color': mix(c['chiaro'], 88),
            'cta_bg': c['acc'], 'cta_bg_hover': c['chiaro'], 'cta_color': c['on_acc'], 'cta_color_hover': c['nero'],
            'cta_radius': radius(self.r), 'cta_radius_hover': radius(self.r), 'cta_size': 15,
            'cta_padding': {'top': 14, 'right': 24, 'bottom': 14, 'left': 24},
            'headline_font_family': 'heading', 'headline_size': size, 'headline_weight': self.peso, 'subtitle_size': 16,
            'layout': 'stack', 'gap': 18, 'vertical_align': 'end', 'banner_radius': radius(r), 'banner_radius_hover': radius(r),
            'banner_padding': {'top': pad + 150, 'right': 36, 'bottom': pad - 20, 'left': 36},
        })

    def intestazione(self, t, eyebrow, testo, sotto, scuro=False, extra=None, bg=None, size=48):
        c = self.c
        col, col2 = (c['chiaro'], mix(c['chiaro'], 84)) if scuro else (c['testo'], c['tenue'])
        return t.section([
            t.row([t.col([self.occhiello(t, eyebrow, color=mix(c['chiaro'], 80) if scuro else None),
                          self.titolo(t, testo, size=size, color=col, tag='h1', sizes=(38, 30)),
                          self.testo(t, sotto, size=16, color=col2)], wm='3-5', ws='1-1'),
                   t.col(extra or [], wm='2-5', ws='1-1')], layout='custom', custom='60,40', gap=32, valign='end', anim=None),
        ], bg=bg if bg is not None else solid(c['nero'] if scuro else c['sala']), padding='default', width='large',
            style={'padding_top': 56, 'padding_bottom': 44})

    def annuncio(self, t, testo, accento, url):
        c = self.c
        return t.section([t.one([t.bare('announcementbar', {
            'text': testo, 'accent_text': accento, 'link_url': url, 'dismissible': False, 'bg_color': c['acc'],
            'accent_color': c['on_acc'], 'text_color': c['on_acc'], 'alignment': 'center', 'border_bottom': 0, 'font_size': 13})],
            anim=None)], width='fullbleed', padding='remove-vertical')

    # ── la vetrina delle offerte: l'apertura di un negozio ──────────────────────────────────────────────────────────
    # Come nei negozi grandi (Amazon, Zalando): sotto la testata una fascia bassa di offerte che scorrono, non
    # un'apertura a tutto schermo; subito sotto le quattro schede con le foto dei prodotti (offerte, novità…).
    # Tele dello slider: computer 1440 × 420, tablet 1024 × 500, telefono 640 × 640 (sul telefono è un quadrato).
    VETRINA_H, VETRINA_HM = 420.0, 640.0

    def offerta(self, sid, sfondo, occhiello, titolo, testo, cta, url, bollino='', nota='', lato='sinistra', primo=False,
                titoli=None, mono=None, bollino_fondo=None, bollino_testo=None, bollino_giro=0, entra='fadeInUp', largo=0.55,
                bollino_x=None, bollino_font=None):
        """Una diapositiva della vetrina: l'offerta sul suo media, pieno; il testo da un lato sul velo sfumato da quel
        lato, il bollino dello sconto accanto e la nota (il codice, la scadenza) sotto il pulsante. Sul telefono il
        titolo e il pulsante stanno in basso (testo e nota no: la fascia resta bassa) e il bollino in alto a destra.
        largo: la larghezza media di una lettera del carattere dei titoli, in frazioni del corpo (per contare le righe e
        mettere il blocco al centro dell'altezza senza buchi né sovrapposizioni). bollino_x: dove sta il bollino (in %
        della larghezza) quando accanto al testo coprirebbe il prodotto. bollino_font: il carattere del bollino, se quello
        dei titoli è troppo sottile per un disco piccolo (di solito quello dei titoli)."""
        # il prezzo resta attaccato al simbolo: «21 €» non va a capo fra il numero e l'euro
        titolo, testo = titolo.replace(' €', '\u00a0€'), testo.replace(' €', '\u00a0€')
        K, c, m = self.K, self.c, self.K.mob
        H, HM = self.VETRINA_H, self.VETRINA_HM
        x0 = 6 if lato == 'sinistra' else 52
        fam = {'fontFamily': titoli} if titoli else {}
        famm = {'fontFamily': mono} if mono else {}

        def righe(testo_, corpo, largh, k):
            return max(1, -(-int(len(testo_) * corpo * k) // int(largh)))
        # computer: occhiello, titolo, testo, pulsante e nota uno sotto l'altro, il blocco al centro dell'altezza
        TS = 52
        alt = [16, righe(titolo, TS, 0.44 * 1440, largo) * TS * 1.04, righe(testo, 17, 0.40 * 1440, 0.46) * 17 * 1.5, 50,
               16 if nota else 0]
        spazi = [12, 16, 22, 14 if nota else 0]
        y = (H - sum(alt) - sum(spazi)) / 2
        ys = []
        for a_, s_ in zip(alt, spazi + [0]):
            ys.append(round(y / H * 100, 2))
            y += a_ + s_
        # telefono: occhiello, titolo e pulsante in basso (sopra le frecce), il velo dal basso fin sopra l'occhiello
        TM = m(26)
        altm = [m(11) * 1.3, righe(titolo, TM, 0.88 * 640, largo) * TM * 1.06, m(13) * 1.25 + 32]
        spazim = [10, 18]
        ym = HM * 0.85 - sum(altm) - sum(spazim)   # sotto restano le frecce e i punti
        yms = []
        for a_, s_ in zip(altm, spazim + [0]):
            yms.append(round(ym / HM * 100, 2))
            ym += a_ + s_
        L = K.veli(sid + '-v', lato, 30, 66, colore=c['nero'], forza=90, mobile_da=max(30, yms[0] - 4))
        # il pulsante sul velo scuro: il primario, se si vede; un tema col primario quasi nero lo mette chiaro (cta_media)
        cta_fondo, cta_testo = self.cta_media or (c['acc'], c['on_acc'])
        cta_hover = ({'bgColor': c['sec'], 'color': c['chiaro'], 'duration': 250} if cta_fondo == c['chiaro']
                     else {'bgColor': c['chiaro'], 'color': c['nero'], 'duration': 250})
        NO = {'visible': False}
        L += [
            K.livello(sid + '-o', contenuto=occhiello, x=x0, y=ys[0], w=42, size=13, peso='700', colore=mix(c['chiaro'], 90),
                      letterSpacing=2, textTransform='uppercase', entra='fadeIn', ritardo=150, **famm,
                      responsive={'mobile': {'fontSize': m(11), 'y': yms[0], 'x': 6, 'width': 88}}),
            K.livello(sid + '-t', contenuto=titolo, tag='h1' if primo else 'h2', x=x0, y=ys[1], w=44, size=TS,
                      peso=self.peso, colore=c['chiaro'], lineHeight=1.02, letterSpacing=-1, entra=entra, ritardo=250,
                      durata=900, **fam, responsive={'mobile': {'fontSize': TM, 'y': yms[1], 'x': 6, 'width': 88}}),
            K.livello(sid + '-s', contenuto=testo, x=x0, y=ys[2], w=40, size=17, colore=mix(c['chiaro'], 90), lineHeight=1.5,
                      ritardo=450, responsive={'mobile': NO}),
            K.livello(sid + '-b', tipo='button', contenuto=cta, x=x0, y=ys[3], w='auto', size=15, peso='700', colore=cta_testo,
                      bgColor=cta_fondo, padding=16, borderRadius=self.r, ritardo=600, buttonUrl=url, hover=cta_hover,
                      responsive={'mobile': {'y': yms[2], 'x': 6, 'fontSize': m(13)}}),
        ]
        if nota:
            L.append(K.livello(sid + '-n', contenuto=nota, x=x0, y=ys[4], w=42, size=13, peso='500', colore=mix(c['chiaro'], 82),
                               letterSpacing=1, ritardo=750, **famm, responsive={'mobile': NO}))
        if bollino:
            lato_b = bollino_x if bollino_x is not None else (x0 + 45 if lato == 'sinistra' else x0 - 11.5)
            L.append(K.livello(sid + '-z', contenuto=bollino, x=lato_b, y=round((H / 2 - 75) / H * 100, 2), w=10.42,
                               h=round(150 / H * 100, 2), size=38, peso='700', colore=bollino_testo or c['nero'],
                               bgColor=bollino_fondo or c['sec'], borderRadius=999, align='center', textValign='center',
                               lineHeight=1, letterSpacing=-1, entra='zoomIn', ritardo=800, durata=700,
                               **({'fontFamily': bollino_font} if bollino_font else fam),
                               # inclinato: lo Slider Pro non ha la rotazione fissa, la dà il movimento tenuto fermo
                               **({'scrollMove': {'inizio': 0, 'fine': 100, 'da': {'r': bollino_giro}, 'a': {'r': bollino_giro}}}
                                  if bollino_giro else {}),
                               responsive={'mobile': {'x': 72, 'y': 5, 'width': 22, 'height': 22, 'fontSize': m(24)}}))
        return K.diapositiva(sid, sfondo, L, durata=6500)

    def vetrina(self, t, slides, transizione='fade', **opz):
        """La fascia delle offerte: lo Slider Pro basso, largo quanto la finestra, che scorre da solo."""
        o = dict(altezza={'mode': 'px', 'value': int(self.VETRINA_H)}, transizione=transizione, durata_trans=1000,
                 heightTablet={'mode': 'px', 'value': 500}, heightMobile={'mode': 'px', 'value': int(self.VETRINA_HM)},
                 autoplay=True, autoplaySpeed=6500, pauseOnHover=True, showArrows=True, arrowStyle='minimal', showDots=True,
                 dotStyle='dash', showProgressBar=True, parallax=False, scrollTimeline=False)
        o.update(opz)
        sl = self.K.proslider(t, slides, **o)
        return t.section([t.one([sl], anim=None)], width='large', padding='remove-vertical',
                         style={'padding_top': 0, 'padding_bottom': 0})

    # Le schede sotto la vetrina hanno più forme, una per negozio (utente, 1 nov 2026: «son tutti uguali» al primo colpo
    # d'occhio): cambiano il numero e la larghezza delle schede, quante foto, se ci sono nomi e prezzi, e cosa le
    # accompagna (un banner, i reparti, il conto alla rovescia, il fondo scuro).
    STILI_QUADRI = ('griglia', 'prezzi', 'promo', 'striscia', 'tre', 'scuro', 'reparti', 'tempo')

    def _foto_quadro(self, t, filtro, **kw):
        """I prodotti di una scheda: di base le sole foto, due per due; kw cambia quante e cosa si vede."""
        base = dict(posts_per_page=4, columns=2, columns_tablet=2, columns_mobile=2, show_title=False, show_price=False,
                    show_rating=False, show_rating_count=False, show_add_to_cart=False, show_badge=False, show_wishlist=False,
                    show_compare=False, mostra_risparmio=False, mostra_scorta=False, consegna_testo='', testo_evidenza='',
                    marca_attributo='', gap=10, card_style='none', card_bg='transparent', hover_effect='zoom',
                    card_padding={'top': 0, 'right': 0, 'bottom': 0, 'left': 0}, titolo_righe=2, vista_rapida=False)
        # sulle foto piccole il pulsante «Anteprima veloce» coprirebbe la foto: torna solo dove i prodotti sono grandi
        base.update(kw)
        return self.prodotti(t, **dict(base, **filtro))

    def _scheda(self, t, figli, larghe=('1-2', '1-2', '1-4'), fondo=None, pad=(16, 14, 10, 14)):
        """Una scheda: larghe = (telefono, tablet, computer) in classi di UIkit, la riga va a capo da sé."""
        return t.col(figli, width_default=larghe[0], ws=larghe[1], wm=larghe[2],
                     style={'bg': solid(fondo or self.c['chiaro']), 'border_radius': self.r, 'padding_top': pad[0],
                            'padding_right': pad[1], 'padding_bottom': pad[2], 'padding_left': pad[3]})

    def quadri(self, t, voci, fondo=None, stile='griglia', promo=None, reparti='I reparti', tempo=('Offerta a tempo',
               'Finisce a mezzanotte')):
        """Le schede sotto la vetrina, come quelle dei negozi grandi: le offerte, le novità, i prezzi piccoli… voci:
        [(titolo, filtro di Prodotti WC, testo del link, indirizzo)]. stile:
        griglia  quattro schede, quattro foto ciascuna (solo foto);
        prezzi   quattro schede con UN prodotto grande, nome, prezzo e risparmio;
        promo    un banner su foto largo metà (promo = titolo, accento, sotto, pulsante, indirizzo, foto) e due schede;
        striscia due fasce larghe, sei prodotti in fila col nome e il prezzo (carosello);
        tre      tre schede con due prodotti ciascuna, nome e prezzo;
        scuro    quattro schede chiare su una fascia scura, le foto col prezzo sotto;
        reparti  la scheda dei reparti (le categorie in tondo) e tre schede;
        tempo    tre schede e, in fondo, la scheda scura dell'offerta a tempo col conto alla rovescia."""
        c = self.c
        tit_ = lambda tit, col=None: self.titolo(t, tit, size=21, tag='h2', sizes=(19, 16), pad_bottom=12, color=col)
        link_ = lambda testo, url, chiaro=False: self.link(t, testo, url, chiaro=chiaro, align='left', pad_top=8)
        cols, sezione = [], {}
        if stile == 'griglia':
            for tit, filtro, lt, url in voci:
                cols.append(self._scheda(t, [tit_(tit), self._foto_quadro(t, filtro), link_(lt, url)], fondo=fondo))
        elif stile == 'prezzi':
            for tit, filtro, lt, url in voci:
                uno = self._foto_quadro(t, filtro, posts_per_page=1, columns=1, columns_tablet=1, columns_mobile=1,
                                        show_title=True, show_price=True, show_badge=True, mostra_risparmio=True,
                                        image_ratio='4-5', titolo_righe=2, vista_rapida=True)
                cols.append(self._scheda(t, [tit_(tit), uno, link_(lt, url)], fondo=fondo))
        elif stile == 'promo':
            ti, acc, sotto, cta, url_p, foto = promo
            # il banner non ha l'altezza: più spazio sopra il testo, così arriva all'altezza delle schede accanto
            cols.append(t.col([self.banner(t, ti, acc, sotto, cta, url_p, self.K.photo_bg(foto), pad=100, size=36)],
                              width_default='1-1', ws='1-1', wm='1-2'))
            for tit, filtro, lt, url in voci[:2]:
                cols.append(self._scheda(t, [tit_(tit), self._foto_quadro(t, filtro), link_(lt, url)], fondo=fondo))
        elif stile == 'striscia':
            for tit, filtro, lt, url in voci[:2]:
                fila = self._foto_quadro(t, filtro, posts_per_page=8, columns=6, columns_tablet=3, columns_mobile=2,
                                         show_title=True, show_price=True, show_badge=True, titolo_righe=1, gap=16,
                                         layout='carousel', carousel_arrows=True, vista_rapida=True)
                cols.append(self._scheda(t, [tit_(tit), fila, link_(lt, url)], larghe=('1-1', '1-1', '1-1'), fondo=fondo,
                                         pad=(18, 20, 12, 20)))
        elif stile == 'tre':
            for tit, filtro, lt, url in voci[:3]:
                due = self._foto_quadro(t, filtro, posts_per_page=2, show_title=True, show_price=True, show_badge=True,
                                        gap=14, vista_rapida=True)
                cols.append(self._scheda(t, [tit_(tit), due, link_(lt, url)], larghe=('1-1', '1-2', '1-3'), fondo=fondo))
        elif stile == 'scuro':
            for tit, filtro, lt, url in voci:
                foto = self._foto_quadro(t, filtro, show_price=True, show_badge=True)
                cols.append(self._scheda(t, [tit_(tit), foto, link_(lt, url)], fondo=fondo))
            sezione = {'bg': solid(c['nero']), 'padding': 'small', 'style': {'padding_top': 28, 'padding_bottom': 28}}
        elif stile == 'reparti':
            # sul telefono i reparti stanno su tutta la riga, tre per fila; la terza scheda pure, con le quattro foto in fila
            cat = self.categorie(t, colonne=3, raggio_foto=999, ratio='1-1', scorri=False, colonne_telefono=3)
            cols.append(self._scheda(t, [tit_(reparti), cat, link_('Tutto il negozio', self.P['negozio'])], fondo=fondo,
                                     larghe=('1-1', '1-2', '1-4')))
            for k, (tit, filtro, lt, url) in enumerate(voci[:3]):
                ultima = k == 2
                foto = self._foto_quadro(t, filtro, columns_mobile=4) if ultima else self._foto_quadro(t, filtro)
                cols.append(self._scheda(t, [tit_(tit), foto, link_(lt, url)], fondo=fondo,
                                         larghe=('1-1', '1-2', '1-4') if ultima else ('1-2', '1-2', '1-4')))
        elif stile == 'tempo':
            for tit, filtro, lt, url in voci[:3]:
                cols.append(self._scheda(t, [tit_(tit), self._foto_quadro(t, filtro), link_(lt, url)], fondo=fondo))
            uno = self._foto_quadro(t, {'on_sale': True, 'orderby': 'popularity'}, posts_per_page=1, columns=1, columns_tablet=1,
                                    columns_mobile=1, show_title=True, show_price=True, show_badge=True, mostra_risparmio=True,
                                    image_ratio='4-3', titolo_righe=1, title_color=c['chiaro'], price_color=c['chiaro'],
                                    sale_color=c['sec'])
            cols.append(self._scheda(t, [self.occhiello(t, tempo[0], color=c['sec'], pad_bottom=6),
                                         self.testo(t, tempo[1], size=14, color=mix(c['chiaro'], 80), pad=(0, 0, 10, 0)),
                                         self.conto(t, size=22, chiaro=True, secondi=False), uno,
                                         link_('Tutte le offerte', self.P['offerte'],
                                                                                        chiaro=True)],
                                     fondo=c['nero'], pad=(18, 16, 10, 16)))
        else:
            raise ValueError(stile)
        bg = sezione.pop('bg', None)
        return t.section([t.row(cols, layout='25-25-25-25', gap=14, valign='stretch', anim=None)], bg=bg, width='large',
                         padding=sezione.get('padding', 'remove-vertical'),
                         style=sezione.get('style', {'padding_top': 20, 'padding_bottom': 8}))

    # ── i pannelli del Mega Menu (template «megapanel» del tema: la voce del menu col suo «pannello») ──────────────
    def _pannello(self, t, sinistra, centro, destra, larghezze='24,54,22'):
        c = self.c
        return [t.section([t.row([t.col(sinistra, wm='1-3', ws='1-1'), t.col(centro, wm='2-3', ws='1-1'),
                                  t.col(destra, wm='1-3', ws='1-1')], layout='custom', custom=larghezze, gap=36,
                                 valign='start', anim=None)],
                          bg=solid(c['fondo']), width='large', padding='remove-vertical',
                          style={'padding_top': 28, 'padding_bottom': 32})]

    def _mini_prodotti(self, t, **kw):
        return self.prodotti(t, **dict(dict(
            posts_per_page=4, columns=4, columns_tablet=4, columns_mobile=2, show_rating=False, show_rating_count=False,
            show_add_to_cart=False, show_wishlist=False, show_compare=False, mostra_scorta=False, consegna_testo='',
            testo_evidenza='', titolo_righe=1, gap=16), **kw))

    def _promo(self, t, promo):
        """La scheda promozionale a destra nei pannelli: (titolo, accento, sotto, pulsante, indirizzo, foto)."""
        tit, acc, sotto, cta, url, foto = promo
        return self.banner(t, tit, acc, sotto, cta, url, self.K.photo_bg(foto), pad=26, size=24)

    def pannello_offerte(self, t, occhiello, titolo, testo, promo, tutte='Tutte le offerte'):
        return self._pannello(t, [self.occhiello(t, occhiello), self.titolo(t, titolo, size=26, tag='p', pad_bottom=6),
                                  self.testo(t, testo, size=14, color=self.c['tenue'], pad=(0, 0, 14, 0)),
                                  self.conto(t, size=24, chiaro=False),
                                  self.link(t, tutte, self.P['offerte'], align='left', pad_top=16)],
                              [self._mini_prodotti(t, on_sale=True, orderby='popularity')], [self._promo(t, promo)])

    def pannello_novita(self, t, occhiello, titolo, voci, promo, tutte='Tutte le novità'):
        """voci: [(testo, indirizzo)], le novità per reparto."""
        c = self.c
        elenco = t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in voci], 'style': 'default',
                                   'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                   'link_color': c['testo'], 'link_hover_color': c['acc'], 'active_color': c['acc'],
                                   'font_size': '15', 'font_weight': '500',
                                   'tile_padding': {'top': 4, 'right': 0, 'bottom': 4, 'left': 0}})
        return self._pannello(t, [self.occhiello(t, occhiello), self.titolo(t, titolo, size=26, tag='p', pad_bottom=12), elenco,
                                  self.link(t, tutte, self.P['negozio'], align='left', pad_top=14)],
                              [self._mini_prodotti(t, orderby='date')], [self._promo(t, promo)])

    def pannello_reparti(self, t, occhiello, titolo, promo, colonne=6, tutto='Tutto il negozio'):
        return self._pannello(t, [self.occhiello(t, occhiello), self.titolo(t, titolo, size=26, tag='p', pad_bottom=12),
                                  self.link(t, tutto, self.P['negozio'], align='left', pad_top=6)],
                              [self.categorie(t, colonne=colonne, ratio='1-1', scorri=False)], [self._promo(t, promo)])

    # ── testata e piede ───────────────────────────────────────────────────────────────────────────────────────────
    def testata(self, t, logo, icona='', ricerca='Cerca nel negozio…', parole='', annuncio=None, logo_size=30):
        """La testata del negozio in una delle otto disposizioni di testate.NEGOZI (una per negozio, perché non siano
        tutte uguali): classica (logo · ricerca · icone, il menu sotto), in linea (logo e menu sulla stessa riga),
        logo al centro, menu diviso attorno al logo, ricerca in alto su una fascia, scura compatta, due piani coi social,
        menu su una fascia colorata. Sul telefono una riga sola: il Mega Menu col suo logo (simbolo e nome) e
        l'hamburger, le icone di account, preferiti e carrello; la ricerca sotto o nell'icona della lente.
        Ogni tile c'è due volte (computer / telefono) e si nasconde dove non serve."""
        c, K = self.c, self.K
        disp = testate.NEGOZI.get(K.id, 'classica')
        scura = self.scura or disp == 'scura'
        fondo = c['nero'] if scura else c['fondo']
        col = c['chiaro'] if scura else c['testo']
        hov = c['sec'] if scura else c['acc']
        icona = icona or testate.LOGHI.get(K.id, '')
        GRANDE = {'visible_tablet': False, 'visible_mobile_landscape': False, 'visible_mobile': False}
        PICCOLO = {'visible_desktop': False, 'visible_tablet_landscape': False}
        lente = disp in ('menu-diviso', 'scura', 'due-piani')   # la ricerca nell'icona (popup), non nella barra
        SENZA = {'top': 0, 'right': 0, 'bottom': 0, 'left': 0}

        def marchio(adv, size, align='left'):
            o = {'text': logo, 'url': self.P['home'], 'alignment': align, 'bg_color': 'transparent', 'text_color': col,
                 'hover_bg_color': 'transparent', 'hover_text_color': hov, 'border_radius': 0, 'font_size': size,
                 'font_weight': self.peso, 'font_family': 'heading', 'letter_spacing': '-0.5', 'text_transform': 'none',
                 'padding_x': 0, 'padding_y': 2}
            if icona:
                o.update({'icon': icona, 'icon_position': 'left', 'icon_spacing': 8, 'icon_size': int(size * 0.8), 'icon_color': c['acc']})
            return t.classic('button', o, style={'padding_top': 0}, advanced=dict(adv))

        def cerca(adv, mode='expanded', alta=46, chiara=None):
            chiara = scura if chiara is None else chiara
            o = {
                'preset': 'custom', 'mode': mode, 'placeholder': ricerca, 'min_chars': 2, 'debounce_ms': 200, 'max_results': 6,
                'results_columns': 1, 'show_all_url': '', 'show_thumbnail': True, 'show_excerpt': False, 'title_only': False,
                'no_results_text': 'Nessun prodotto: prova con un’altra parola.', 'post_types': 'product',
                'input_bg': c['chiaro'] if chiara else c['sala'], 'input_color': c['testo'] if not chiara else c['nero'],
                'icon_color': (c['tenue'] if not chiara else c['nero']) if mode in ('expanded', 'inline') else col,
                'input_font_size': 15, 'input_height': alta,
                'input_border_color': 'transparent', 'input_border_radius': max(self.r, 4), 'focus_border_color': c['acc'],
                'results_bg': c['chiaro'], 'results_border_color': c['filo'], 'results_border_radius': max(self.r, 4),
                'item_hover_bg': c['sala'], 'title_color': c['testo'], 'excerpt_color': c['tenue'], 'results_max_height': 460,
                'thumb_width': 48, 'thumb_height': 48, 'thumb_radius': self.r, 'animated_placeholder': bool(parole),
                'placeholder_words': parole}
            if mode in ('modal', 'compact'):
                # La lente nella barra: senza disco attorno, del colore delle altre icone; il campo del popup resta chiaro.
                o.update({'input_height': 40, 'icon_color': c['tenue'], 'input_bg': c['chiaro'], 'input_color': c['nero'],
                          'trigger_bg': 'transparent', 'trigger_border_color': 'transparent', 'trigger_color': col})
            return t.bare('livesearch', o, advanced=dict(adv))

        def icone(adv, con_lente=False, colore=None):
            colore = colore or col
            conto = t.classic('icon', {'icon': 'user', 'link_url': self.P['account'], 'link_target': '_self',
                                       'link_label': 'Il mio account', 'size': 24, 'color': colore, 'hover_animation': 'none',
                                       'view': 'default', 'alignment': 'center'}, style={'margin_bottom': 0, 'padding_top': 6},
                              advanced=dict(adv))
            cuore = t.bare('woo_wishlist', {'icon': 'heart', 'style': 'icon', 'show_count': True, 'show_grid': False, 'icon_size': 22,
                                            'icon_color': colore, 'icon_color_active': c['acc2'], 'badge_bg': c['acc'],
                                            'badge_color': c['on_acc']}, advanced=dict(adv))
            carrello = t.bare('woo_minicart', {'style': 'icon', 'icon': 'bag', 'show_count': True, 'show_total': True, 'dropdown': True,
                                               'icon_size': 24, 'text_color': colore, 'icon_color': colore, 'badge_bg': c['acc'],
                                               'badge_color': c['on_acc'], 'dropdown_width': 360}, advanced=dict(adv))
            pezzi = ([cerca(adv, mode='modal')] if con_lente else []) + [conto, cuore, carrello]
            q = len(pezzi)
            w = {3: ('1-3', '33-33-33'), 4: ('1-4', '25-25-25-25')}[q]
            return t.row([t.col([p], wm=w[0], ws=w[0], width_default=w[0]) for p in pezzi], layout=w[1], valign='center',
                         anim=None, stack=False, gap=6)

        def menu(adv, con_logo=False, layout='left', hover='underline', posizione='left', altezza='46', testo=None,
                 sopra=None, lato='left'):
            testo = testo or col
            sopra = sopra or hov
            s = {
                'menu_id': 'auto', 'logo_text': logo if con_logo else '', 'logo_mark': '', 'logo_dot': False,
                'logo_text_size': str(max(20, min(28, logo_size - 4))), 'logo_text_color': col, 'logo_text_font': 'heading',
                'logo_text_weight': self.peso, 'logo_text_spacing': 0, 'logo_text_transform': '',
                'logo_position': posizione, 'layout': layout, 'mobile_bar_logo': True, 'hamburger_side': lato,
                'header_mode': 'classic', 'sticky': False, 'nav_bg': 'transparent', 'text_color': testo,
                'hover_color': sopra, 'active_color': sopra, 'font_size': '14', 'font_weight': '600', 'text_transform': 'none',
                'letter_spacing': '0', 'item_gap': '24', 'bar_width': 'wide', 'nav_height': altezza, 'bar_padding': SENZA,
                'hover_effect': hover, 'hover_effect_color': sopra, 'hover_effect_height': 2, 'button_mode': 'none',
                'social_in_navbar': False, 'mobile_bg': c['nero'], 'mobile_text_color': c['chiaro'],
                'mobile_accent_color': c['acc'], 'hamburger_color': col, 'mobile_link_font': 'heading',
                'mobile_link_weight': self.peso, 'border': dict(SENZA, style='solid', color=''),
                'topbar_enabled': False, 'nav_phone': '', 'nav_phone_url': '',
                # i pannelli delle voci (offerte, novità, reparti): template del tema su tutta la larghezza sotto il menu
                'panel_bg': c['fondo'], 'panel_border_top': '1', 'panel_border_color': mix(c['testo'], 14), 'panel_shadow': 'lg',
                'panel_radius': {'tl': 0, 'tr': 0, 'br': 0, 'bl': 0}, 'panel_open_animation': 'fade', 'panel_origin': 'nav'}
            testate.mobile(K.id, s)
            if con_logo:
                testate.simbolo(K.id, s)
                s['logo_text_color'] = col
                if scura and s.get('logo_mark'):
                    # sulla barra scura il primario (spesso scuro) spariva: il simbolo prende l'accento chiaro
                    pieno = bool(s.get('logo_mark_bg')) and s.get('logo_mark_shape') != 'ring'
                    s.update({'logo_mark_color': c['nero'] if pieno else c['sec']})
                    if s.get('logo_mark_bg'):
                        s['logo_mark_bg'] = c['sec']
            return t.classic('megamenu', s, advanced=dict(adv))

        def social(adv, align='left', colore=None):
            reti = [('instagram', '#'), ('facebook', '#'), (testate.TERZO.get('Negozi online', 'pinterest'), '#')]
            return t.classic('social', {'links': [{'platform': p, 'url': u} for p, u in reti], 'style': 'minimal', 'size': '36',
                                        'gap': '16', 'icon_color': colore or col, 'hover_effect': 'none', 'alignment': align},
                             advanced=dict(adv))

        def riga(cols, custom, gap=24, valign='center'):
            return t.row([t.col(x, wm='1-3', ws='1-3') for x in cols], layout='custom', custom=custom, gap=gap,
                         valign=valign, anim=None, stack=False)

        def destra(pezzo, vuoto=30):
            """Un gruppo di icone spinto a destra della sua colonna."""
            return t.row([t.col([], wm='1-3', ws='1-3', width_default='1-3'),
                          t.col([pezzo], wm='2-3', ws='2-3', width_default='2-3')],
                         layout='custom', custom='%d,%d' % (vuoto, 100 - vuoto), anim=None, stack=False, valign='center')

        # Una sola sezione per tutta la testata: le sezioni non si nascondono per dispositivo, le tile sì. Le fasce
        # colorate (il menu su fondo pieno, la ricerca su una fascia) sono lo sfondo delle tile stesse.
        def fascia(colore, pad=(12, 14)):
            return {'bg': solid(colore), 'padding_top': pad[0], 'padding_bottom': pad[0], 'padding_left': pad[1],
                    'padding_right': pad[1], 'border_radius': max(self.r, 0)}
        filo_sopra = {'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': mix(col, 14)},
                      'padding_top': 0}

        def menu_sotto(**kw):
            m = menu(GRANDE, **kw)
            m['style'] = dict(m.get('style') or {}, **filo_sopra)
            return t.one([m], anim=None, style={'margin_top': 12})

        # ── computer ──
        if disp == 'in-linea':
            # logo e menu sulla stessa riga: la ricerca è la lente accanto alle icone (le voci hanno tutto lo spazio)
            pc = [riga([[menu(GRANDE, con_logo=True, altezza='64', hover='fill-up')], [destra(icone(GRANDE, con_lente=True), vuoto=10)]],
                       '80,20')]
        elif disp == 'logo-al-centro':
            pc = [riga([[cerca(GRANDE, alta=42)], [marchio(GRANDE, logo_size + 4, align='center')], [destra(icone(GRANDE))]],
                       '30,40,30'),
                  menu_sotto(layout='center', hover='overline')]
        elif disp == 'menu-diviso':
            pc = [riga([[menu(GRANDE, con_logo=True, layout='center', posizione='split', hover='dot', altezza='72')],
                        [destra(icone(GRANDE, con_lente=True))]], '78,22')]
        elif disp == 'ricerca-in-alto':
            larga = cerca(GRANDE, alta=52, chiara=True)
            larga['style'] = fascia(c['sala'], pad=(14, 220))
            pc = [riga([[menu(GRANDE, con_logo=True, layout='right', hover='highlight', altezza='68')], [destra(icone(GRANDE))]],
                       '80,20'),
                  t.one([larga], anim=None, style={'margin_top': 10})]
        elif disp == 'scura':
            pc = [riga([[menu(GRANDE, con_logo=True, layout='center', hover='double-line', altezza='70')],
                        [destra(icone(GRANDE, con_lente=True))]], '76,24')]
        elif disp == 'due-piani':
            pc = [riga([[social(GRANDE)], [marchio(GRANDE, logo_size + 2, align='center')], [destra(icone(GRANDE, con_lente=True))]],
                       '30,40,30'),
                  menu_sotto(layout='center', hover='double-line')]
        elif disp == 'barra-colorata':
            barra = menu(GRANDE, testo=c['on_acc'], sopra=c['on_acc'], hover='highlight', altezza='50')
            barra['settings'].update({'nav_bg': c['acc'], 'bar_padding': {'top': 0, 'right': 18, 'bottom': 0, 'left': 18},
                                     'bar_padding_4lati': True})
            barra['style'] = dict(barra.get('style') or {}, border_radius=max(self.r, 0))
            pc = [riga([[marchio(GRANDE, logo_size)], [cerca(GRANDE)], [icone(GRANDE)]], '22,58,20'),
                  t.one([barra], anim=None, style={'margin_top': 14})]
        else:  # classica
            pc = [riga([[marchio(GRANDE, logo_size)], [cerca(GRANDE)], [icone(GRANDE)]], '22,58,20'), menu_sotto()]

        # ── tablet e telefono: il menu col logo e l'hamburger, le icone; la ricerca sotto o nella lente ──
        lato = 'right' if disp in ('logo-al-centro', 'due-piani', 'scura') else 'left'
        tel = [riga([[menu(PICCOLO, con_logo=True, altezza='56', lato=lato)],
                     [destra(icone(PICCOLO, con_lente=lente), vuoto=4 if lente else 30)]],
                    '48,52' if lente else '56,44', gap=8)]
        if not lente:
            tel.append(t.one([cerca(PICCOLO, alta=44)], anim=None, style={'margin_top': 8}))

        out = []
        if annuncio and disp != 'scura':
            out.append(self.annuncio(t, *annuncio))
        out.append(t.section(pc + tel, bg=solid(fondo), width='large', padding='remove-vertical',
                             style={'padding_top': 12, 'padding_bottom': 12,
                                    'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid',
                                               'color': mix(col, 14)}}))
        if annuncio and disp == 'scura':
            out.append(self.annuncio(t, *annuncio))
        return out

    def piede(self, t, logo, icona, claim, colonne, contatti, legale, promesse, newsletter, social=None):
        """promesse: [(icona, testo)] · colonne: [(titolo, [(voce, link)])] · contatti: [(icona, testo, link)] ·
        newsletter: (occhiello, titolo, testo, grazie)."""
        c, K = self.c, self.K
        sfondo, col, col2 = c['nero'], c['chiaro'], mix(c['chiaro'], 76)

        def nav(voci):
            return t.classic('nav', {'items': [{'title': a, 'content': b, 'tag': ''} for a, b in voci], 'style': 'default',
                                     'direction': 'vertical', 'alignment': 'left', 'text_transform': 'none', 'letter_spacing': '0',
                                     'link_color': col2, 'link_hover_color': c['sec'], 'active_color': c['sec'], 'font_size': '14',
                                     'font_weight': '400', 'tile_padding': {'top': 3, 'right': 0, 'bottom': 3, 'left': 0}})
        o = {'text': logo, 'url': self.P['home'], 'alignment': 'left', 'bg_color': 'transparent', 'text_color': col,
             'hover_bg_color': 'transparent', 'hover_text_color': c['sec'], 'border_radius': 0, 'font_size': 34,
             'font_weight': self.peso, 'font_family': 'heading', 'letter_spacing': '-1', 'text_transform': 'none',
             'padding_x': 0, 'padding_y': 0}
        if icona:
            o.update({'icon': icona, 'icon_position': 'left', 'icon_spacing': 6, 'icon_size': 28, 'icon_color': c['acc']})
        marchio = t.classic('button', o, style={'padding_top': 0})
        lista = t.classic('iconlist', {
            'items': [{'icon': i, 'text': s, 'link': l, 'color': ''} for i, s, l in contatti], 'icon_color': c['sec'],
            'icon_size': '16', 'text_color': col, 'text_size': '14', 'gap': '10', 'icon_shape': 'none', 'icon_bg_color': '',
            'divider': False, 'layout': 'vertical'}, style={})
        prima = [marchio, self.testo(t, claim, size=14, color=col2, pad=(10, 0, 14, 0)), lista]
        if social:
            prima.append(t.classic('social', {'links': [{'platform': n, 'url': u} for n, u in social], 'style': 'minimal',
                                              'size': '20', 'gap': '14', 'icon_color': col2, 'hover_effect': 'glow',
                                              'alignment': 'left'}, style={'margin_top': 14}))
        largh = max(1, len(colonne))
        cols = [t.col(prima, wm='1-3', ws='1-1')] + \
            [t.col([K.label(t, tit, color=mix(c['chiaro'], 72), pad_bottom=12), nav(voci)], wm='2-9', ws='1-2',
                   width_default='1-2')
             for tit, voci in colonne]
        resto = round(66 / largh, 2)
        righe = t.row(cols, layout='custom', custom=','.join(['34'] + [str(resto)] * largh), gap=40, valign='start', anim=None)
        pagamenti = t.classic('trust-strip', {
            'items': [{'icon': i, 'icon_color': col2, 'text': s, 'badge': ''} for i, s in [
                ('credit-card', 'Carte'), ('wallet', 'Portafogli digitali'), ('landmark', 'Bonifico'),
                ('shield-check', 'Pagamenti protetti')]],
            'text_color': col2, 'text_size': 12, 'font_family': 'body', 'align': 'right', 'flow': 'wrap', 'gap': 18,
            'variant': 'inline', 'icon_size': 15, 'separator_char': '', 'separator_color': c['filo']},
            style={'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0})
        totop = t.classic('totop', {'icon_color': c['on_acc'], 'posizione': 'fisso', 'style': 'default'})
        riga_legale = t.classic('text-block', {'content': '<p>' + legale + '</p>', 'font_size': 12, 'line_height': 1.7,
                                               'text_color': col2, 'link_color': c['sec'], 'text_align': 'left',
                                               'tile_padding': {'top': 0, 'right': 0, 'bottom': 0, 'left': 0}})
        fondo = t.row([t.col([riga_legale, totop], wm='1-2', ws='1-1'),
                       t.col([pagamenti], wm='1-2', ws='1-1')], layout='50-50', valign='center', anim=None,
                      style={'border': {'top': 1, 'right': 0, 'bottom': 0, 'left': 0, 'style': 'solid', 'color': mix(col, 14)},
                             'padding_top': 18, 'margin_top': 40})
        occh, tit, txt, grazie = newsletter
        bollettino = t.section([
            t.row([t.col([self.occhiello(t, occh),
                          self.titolo(t, tit, size=34, sizes=(30, 26)),
                          self.testo(t, txt)], wm='1-2', ws='1-1'),
                   t.col([self.modulo_email(t, grazie)], wm='1-2', ws='1-1')], layout='50-50', gap=48, valign='center'),
        ], bg=solid(c['sala']), padding='default', width='large', style={'padding_top': 56, 'padding_bottom': 56})
        return [bollettino,
                t.section([t.one([self.promesse(t, promesse, chiaro=True)], anim=None)], bg=solid(c['nero']), padding='small',
                          style={'border': {'top': 0, 'right': 0, 'bottom': 1, 'left': 0, 'style': 'solid', 'color': mix(col, 12)}}),
                t.section([righe, fondo], bg=solid(sfondo), padding='large', width='large')]

    def modulo_email(self, t, grazie):
        c = self.c
        return t.classic('form', {
            'fields': [{'field_type': 'email', 'label': 'La tua email', 'name': 'email', 'placeholder': 'nome@esempio.it',
                        'required': True, 'width': '1-1', 'options': '', 'icon': ''}],
            'email_subject': self.nome + ' — newsletter', 'submit_text': 'Iscrivimi', 'submit_icon': '', 'success_message': grazie,
            'submit_full_width': False, 'submit_alignment': 'left', 'label_color': c['testo'], 'label_size': '13',
            'label_weight': '600', 'label_transform': 'none', 'label_letter_spacing': '0', 'input_bg': c['chiaro'],
            'input_color': c['nero'], 'input_border_color': c['filo'], 'input_border_width': '1', 'input_border_style': 'solid',
            'input_radius': self.r, 'input_focus_border': c['acc'], 'input_focus_shadow': False, 'gap': '12',
            'submit_bg': c['acc'], 'submit_color': c['on_acc'], 'submit_radius': self.r, 'submit_padding_x': '26',
            'submit_padding_y': '14', 'submit_font_size': '15', 'submit_font_weight': '600', 'submit_text_transform': 'none',
            'submit_letter_spacing': '0', 'submit_hover_bg': c['testo'], 'privacy_checkbox': True},
            style={'bg': {'type': 'none'}, 'padding_top': 0, 'padding_right': 0, 'padding_bottom': 0, 'padding_left': 0,
                   'border_radius': 0})

    # ── pagine del negozio ────────────────────────────────────────────────────────────────────────────────────────
    def filtro(self, t, attributi=''):
        c = self.c
        return t.bare('woo_product_filter', {
            'show_price': True, 'show_price_range': True, 'show_categories': True, 'show_attributes': bool(attributi),
            'show_stock': True, 'show_active': True, 'show_count': True, 'price_min': 0, 'price_max': 500, 'price_step': 10,
            'attributes': attributi, 'collapsed': False, 'filter_style': 'sidebar', 'collapsible': True,
            'heading_color': c['testo'], 'text_color': c['testo'], 'accent_color': c['acc'], 'active_color': c['acc'],
            'bg_color': c['chiaro'], 'border_color': c['filo'], 'border_radius': max(self.r, 4), 'apply_button': True,
            'button_text': 'Mostra i prodotti', 'reset_text': 'Azzera i filtri', 'button_bg': c['acc'], 'button_color': c['on_acc']})

    def pagina_negozio(self, t, eyebrow, titolo, sotto, attributi='', colonne=3, banner=None, bg=None):
        out = [self.intestazione(t, eyebrow, titolo, sotto, bg=bg)]
        destra = [self.anteprima(t), self.prodotti(t, posts_per_page=24, columns=colonne, columns_tablet=2, pagination=True)]
        out.append(t.section([
            t.row([t.col([self.filtro(t, attributi)], wm='1-4', ws='1-1'), t.col(destra, wm='3-4', ws='1-1')],
                  layout='custom', custom='25,75', gap=32, valign='start', anim=None),
        ], padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 64}))
        if banner:
            out.append(t.section([t.one([banner])], padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 64}))
        return out

    def pagina_offerte(self, t, eyebrow, titolo, sotto, extra=None, bg=None):
        c = self.c
        out = [self.intestazione(t, eyebrow, titolo, sotto, scuro=True, extra=[self.conto(t, size=48)], bg=bg)]
        out.append(t.section([t.one([self.anteprima(t), self.prodotti(t, on_sale=True, posts_per_page=20, orderby='popularity')])],
                             padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 48}))
        out += extra or []
        return out

    def pagina_prodotto(self, t, usp, bundle='Spesso comprati insieme', miniature='left', altezza='600px',
                        bottone='Aggiungi al carrello', schede_stile='underline', fondo_galleria=None):
        """La scheda prodotto: briciole e navigazione, galleria col video del prodotto, titolo, voto, sconto, prezzo,
        descrizione breve, scorta, varianti e carrello, preferiti, promesse, dati; poi il set, le schede, i correlati,
        gli upsell e i visti di recente. usp: [(icona, testo)]."""
        c = self.c
        briciole = t.bare('woo_breadcrumbs', {'separator': '/', 'text_color': c['tenue'], 'link_color': c['testo'],
                                              'font_size': 13, 'alignment': 'left'})
        nav = t.bare('woo_product_navigation', {'show_thumbnail': True, 'show_label': True, 'label_prev': 'Precedente',
                                                'label_next': 'Successivo', 'separator_style': 'line', 'text_color': c['tenue'],
                                                'hover_color': c['acc']})
        avvisi = t.bare('woo_notices', {'show_success': True, 'show_error': True, 'show_info': True, 'border_radius': self.r,
                                        'font_size': 14})
        galleria = t.bare('woo_product_gallery_slider', {
            'show_thumbnails': True, 'thumbnail_position': miniature, 'enable_zoom': True, 'enable_lightbox': True,
            'show_video': True, 'video_position': 'second', 'main_height': altezza, 'max_width': 0, 'thumbnail_size': 78,
            'thumbnail_gap': 10, 'arrows': True, 'main_bg': fondo_galleria or c['sala'], 'thumbnail_border': c['filo'],
            'thumbnail_active_border': c['testo'], 'arrow_color': c['testo'], 'arrow_bg': c['chiaro'], 'show_badge': True,
            'badge_bg': c['acc2'], 'border_radius': self.r})
        destra = [
            t.bare('woo_product_title', {'tag': 'h1', 'text_align': 'left', 'color': c['testo'], 'font_size': 34,
                                         'font_weight': self.peso, 'line_height': '1.15', 'link_to_product': False}),
            t.bare('woo_rating', {'show_count': True, 'show_average': True, 'star_color': c['acc'], 'empty_star_color': c['filo'],
                                  'text_color': c['tenue'], 'star_size': 16, 'text_size': 13}),
            t.bare('woo_price', {'show_regular': True, 'show_sale': True, 'show_suffix': False, 'price_color': c['testo'],
                                 'sale_color': c['acc2'], 'regular_color': c['tenue'], 'font_size': 28, 'font_weight': '600',
                                 'text_align': 'left'}, style={'margin_top': 8}),
            t.bare('woo_product_description', {'content_type': 'short', 'text_color': c['testo'], 'font_size': 15,
                                               'line_height': '1.65', 'text_align': 'left', 'max_lines': 0}),
            t.bare('woo_product_stock', {'show_quantity': True, 'show_icon': True,
                                         'in_stock_color': 'var(--olo-color-success, #15803d)', 'out_of_stock_color': c['acc2'],
                                         'low_stock_color': 'var(--olo-color-warning, #b45309)', 'low_stock_threshold': 5,
                                         'font_size': 14, 'font_weight': '600', 'text_align': 'left', 'icon_size': 16}),
            t.bare('woo_addtocart', {'button_text': bottone, 'show_quantity': True, 'show_icon': True, 'icon': 'bag',
                                     'style': 'filled', 'size': 'large', 'full_width': True, 'bg_color': c['acc'],
                                     'text_color': c['on_acc'], 'hover_bg': c['testo'], 'hover_text': c['fondo'],
                                     'quantity_style': 'stepper', 'border_radius': radius(self.r)}),
            t.bare('woo_wishlist', {'icon': 'heart', 'style': 'button', 'show_count': False, 'show_grid': False, 'icon_size': 18,
                                    'icon_color': c['acc2'], 'btn_bg': 'transparent', 'btn_color': c['testo']}),
            t.classic('iconlist', {
                'items': [{'icon': i, 'text': s, 'link': '', 'color': ''} for i, s in usp], 'icon_color': c['acc'], 'icon_size': '18',
                'text_color': c['testo'], 'text_size': '14', 'gap': '10', 'icon_shape': 'none', 'icon_bg_color': '',
                'divider': True, 'divider_color': c['filo'], 'layout': 'vertical'},
                style={'margin_top': 14, 'padding_top': 14, 'padding_right': 16, 'padding_bottom': 14, 'padding_left': 16,
                       'bg': {'type': 'solid', 'color': c['sala']}, 'border_radius': self.r}),
            t.bare('woo_product_meta', {'show_sku': True, 'show_categories': True, 'show_tags': False, 'layout': 'inline',
                                        'separator': ' · ', 'text_color': c['tenue'], 'label_color': c['tenue'],
                                        'link_color': c['testo'], 'font_size': 13, 'label_weight': '600'}),
        ]
        out = [t.section([t.row([t.col([briciole], wm='2-3', ws='1-1'), t.col([nav], wm='1-3', ws='1-1')],
                                layout='custom', custom='62,38', gap=16, valign='center', anim=None),
                          t.one([avvisi], anim=None)], padding='remove-vertical', width='large',
                         style={'padding_top': 20, 'padding_bottom': 8})]
        out.append(t.section([t.row([t.col([galleria], wm='7-12', ws='1-1'), t.col(destra, wm='5-12', ws='1-1')],
                                    layout='custom', custom='58,42', gap=44, valign='start', anim=None)],
                             padding='default', width='large', style={'padding_top': 8, 'padding_bottom': 48}))
        if bundle:
            out.append(t.section([t.one([t.bare('woo_product_bundle', {
                'product_ids': '', 'bundle_title': bundle, 'title_tag': 'h2', 'discount_percent': 10, 'show_savings': True,
                'show_images': True, 'show_prices': True, 'show_descriptions': False, 'button_text': 'Aggiungi il set al carrello',
                'layout': 'horizontal', 'gap': 16, 'card_style': 'border', 'title_color': c['testo'], 'price_color': c['testo'],
                'savings_color': 'var(--olo-color-success, #15803d)', 'bundle_bg': c['chiaro'], 'button_bg': c['acc'],
                'button_color': c['on_acc'], 'divider_color': c['filo'], 'border_color': c['filo'], 'border_radius': self.r * 2})])],
                padding='default', width='large', style={'padding_top': 0, 'padding_bottom': 48}))
        out.append(t.section([t.one([t.bare('woo_product_tabs', {'show_description': True, 'show_additional': True,
                                                                 'show_reviews': True, 'tab_style': schede_stile,
                                                                 'active_color': c['acc'], 'text_color': c['testo']})])],
                             bg=solid(c['sala']), padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 40}))
        out.append(t.section([
            t.one([t.bare('woo_upsells', {'show_heading': True, 'title_text': 'Potrebbe piacerti anche', 'show_image': True,
                                          'show_title': True, 'show_price': True, 'gap': 18, 'card_style': self.card, 'columns': 4,
                                          'columns_tablet': 2, 'columns_mobile': 2, 'title_color': c['testo'],
                                          'price_color': c['testo']})], style={'margin_bottom': 44}),
            t.one([t.bare('woo_related', {'posts_per_page': 4, 'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2,
                                          'show_image': True, 'show_title': True, 'show_price': True, 'card_style': self.card,
                                          'gap': 18, 'title_color': c['testo'], 'price_color': c['testo'],
                                          'heading_text': 'Dalla stessa categoria', 'show_heading': True})]),
            t.one([t.bare('woo_recently_viewed', {
                'columns': 6, 'columns_tablet': 3, 'columns_mobile': 2, 'limit': 6, 'gap': 14, 'card_style': self.card,
                'show_image': True, 'show_title': True, 'show_price': True, 'show_rating': False, 'show_add_to_cart': False,
                'hover_effect': 'zoom', 'image_ratio': self.ratio, 'title_color': c['testo'], 'price_color': c['testo'],
                'sale_color': c['acc2'], 'button_bg': c['acc'], 'button_color': c['on_acc'], 'heading': 'Visti di recente',
                'heading_size': 22, 'heading_color': c['testo'], 'empty_text': ''})], style={'margin_top': 44}),
        ], padding='default', width='large', style={'padding_top': 48, 'padding_bottom': 64}))
        return out

    def pagina_carrello(self, t, sotto, promesse):
        c = self.c
        out = [self.intestazione(t, 'Il carrello', 'Il tuo carrello', sotto)]
        out.append(t.section([
            t.one([t.bare('woo_cart', {'show_thumbnail': True, 'show_coupon': True, 'show_totals': True, 'show_cross_sells': False,
                                       'button_color': c['on_acc'], 'button_bg': c['acc'], 'text_color': c['testo'],
                                       'heading_color': c['testo']})]),
            t.one([self.promesse(t, promesse)], style={'margin_top': 28}),
            t.one([t.bare('woo_cross_sells', {
                'columns': 4, 'columns_tablet': 2, 'columns_mobile': 2, 'gap': 18, 'card_style': self.card, 'show_image': True,
                'show_title': True, 'show_price': True, 'show_rating': True, 'show_add_to_cart': True, 'show_badge': True,
                'hover_effect': 'zoom', 'image_ratio': self.ratio, 'title_color': c['testo'], 'price_color': c['testo'],
                'sale_color': c['acc2'], 'button_bg': c['acc'], 'button_color': c['on_acc'], 'badge_bg': c['acc2'],
                'heading': 'Ti potrebbero servire anche', 'heading_size': 24, 'heading_color': c['testo'], 'limit': 4})],
                style={'margin_top': 44}),
        ], padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 64}))
        return out

    def pagina_cassa(self, t, sotto, passi='I tuoi dati,La consegna,Il pagamento'):
        c = self.c
        out = [self.intestazione(t, 'La cassa', 'Ancora tre passi', sotto)]
        out.append(t.section([t.one([t.bare('woo_checkout_multistep', {
            'step_labels': passi, 'step_style': 'progress', 'accent_color': c['acc'], 'step_bg': c['chiaro'],
            'active_color': c['acc'], 'text_color': c['testo'], 'card_radius': radius(self.r * 2), 'show_order_review': True})])],
            padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 64}))
        return out

    def pagina_account(self, t, sotto, laterale):
        """laterale: (titolo, testo, pulsante, link) per chi ha un ordine in arrivo senza account."""
        c = self.c
        tit, txt, cta, url = laterale
        out = [self.intestazione(t, 'Il mio account', 'Ordini, resi e preferenze', sotto)]
        out.append(t.section([
            t.row([t.col([t.bare('woo_myaccount', {'layout': 'sidebar', 'show_avatar': True, 'show_greeting': True,
                                                   'avatar_size': '56', 'border_radius': str(self.r * 2), 'sidebar_bg': c['chiaro'],
                                                   'sidebar_color': c['testo'], 'sidebar_active_bg': c['nero'],
                                                   'sidebar_active_color': c['chiaro'], 'content_bg': c['chiaro'],
                                                   'heading_color': c['testo'], 'text_color': c['testo'], 'link_color': c['testo'],
                                                   'button_bg': c['acc'], 'button_color': c['on_acc'], 'border_color': c['filo']})],
                         wm='2-3', ws='1-1'),
                   t.col([self.occhiello(t, 'Senza account'), self.titolo(t, tit, size=26), self.testo(t, txt),
                          self.pulsante(t, cta, url, pad_top=14)], wm='1-3', ws='1-1')],
                  layout='custom', custom='66,34', gap=44, valign='start'),
        ], padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 64}))
        return out

    def pagina_preferiti(self, t, sotto, vuota):
        c = self.c
        out = [self.intestazione(t, 'I preferiti', 'Quello che ti è piaciuto', sotto)]
        out.append(t.section([
            t.one([t.bare('woo_wishlist', {'icon': 'heart', 'style': 'icon', 'show_count': False, 'show_grid': True, 'columns': 4,
                                           'show_price': True, 'show_add_to_cart': True, 'show_remove': True, 'empty_text': vuota,
                                           'gap': 18, 'card_style': self.card, 'icon_color': c['acc2'], 'title_color': c['testo'],
                                           'price_color': c['testo'], 'empty_color': c['tenue'], 'button_bg': c['acc'],
                                           'button_color': c['on_acc']})]),
            t.one([self.titolo(t, 'Intanto, i più amati', size=26)], style={'margin_top': 56}),
            t.one([self.anteprima(t), self.prodotti(t, orderby='popularity', posts_per_page=4)]),
        ], padding='default', width='large', style={'padding_top': 36, 'padding_bottom': 64}))
        return out

    def pagina_assistenza(self, t, sotto, schede, faq, contatti, campi_extra='', foto=None):
        """schede: [(icona, titolo, testo, ancora)] · faq: [(domanda, risposta)] · contatti: [(icona, testo, link)]."""
        c, K = self.c, self.K
        out = [self.intestazione(t, 'Assistenza', 'Siamo qui per te', sotto)]
        out.append(t.section([t.one([t.classic('info-cards', {
            'items': [{'counter': '', 'counter_label': '', 'title': a, 'title_accent': '', 'title_accent_italic': False,
                       'description': b, 'icon': i, 'footer_dot_color': '', 'footer_text': '',
                       'link_url': self.P['assistenza'] + '#' + h, 'link_text': '', 'media_image': '', 'media_label': ''}
                      for i, a, b, h in schede],
            'container_bg': {'type': 'none'}, 'container_radius': radius(0), 'container_padding': 0, 'container_gap': 0,
            'columns': 4, 'columns_tablet': 2, 'columns_mobile_landscape': 2, 'columns_mobile': 1, 'items_gap': 16,
            'card_bg': {'type': 'solid', 'color': c['chiaro']}, 'card_color': c['tenue'], 'card_accent_color': c['acc'],
            'card_radius': radius(self.r * 2), 'card_radius_hover': radius(self.r * 2), 'card_padding': 24, 'card_border': '',
            'show_icon': True, 'show_counter': False, 'show_counter_label': False, 'show_arrow': True, 'show_footer': False,
            'show_link_text': False, 'show_divider': False, 'show_media': False, 'title_font_family': 'heading', 'title_size': 22,
            'title_weight': self.peso, 'title_italic': False, 'title_color': c['testo'], 'description_size': 15,
            'card_hover_effect': 'lift', 'icon_color': c['acc'], 'icon_bg_color': mix(c['acc'], 12)})])],
            padding='default', width='large', style={'padding_top': 40, 'padding_bottom': 24}))
        out.append(t.section([
            t.row([t.col([self.occhiello(t, 'Le domande'), self.titolo(t, 'Spedizioni, resi e pagamenti', size=32),
                          K.faq(t, faq, 'ass')], wm='3-5', ws='1-1'),
                   t.col([t.bare('woo_order_tracking', {'title': 'Traccia il tuo ordine', 'title_tag': 'h2',
                                                        'accent_color': c['acc'], 'text_color': c['testo'],
                                                        'button_color': c['on_acc'], 'button_bg': c['acc'],
                                                        'form_style': 'modern'})], wm='2-5', ws='1-1')],
                  layout='custom', custom='60,40', gap=48, valign='start'),
        ], padding='default', width='large', html_id='spedizioni', style={'padding_top': 40, 'padding_bottom': 48}))
        campi = [K.campo('text', 'Nome', 'nome', True), K.campo('email', 'Email', 'email', True),
                 K.campo('text', 'Numero d’ordine', 'ordine', segnaposto='Se ce l’hai'),
                 K.campo('select', 'Di cosa si tratta', 'argomento', True,
                         opzioni='Un ordine in arrivo\nUn reso o un cambio\nUna domanda su un prodotto\n' + campi_extra + 'Altro'),
                 K.campo('textarea', 'Il messaggio', 'messaggio', True, larghezza='1-1')]
        sinistra = [self.occhiello(t, 'Scrivici'), self.titolo(t, 'Ti rispondiamo in giornata.', size=32), K.contacts(t, contatti)]
        if foto:
            sinistra.append(K.image(t, foto[0], foto[1], ratio='4/3'))
        out.append(t.section([
            t.row([t.col(sinistra, wm='2-5', ws='1-1'),
                   t.col([K.modulo(t, campi, self.nome + ' — assistenza', 'Invia', 'Ricevuto: ti rispondiamo in giornata.')],
                         wm='3-5', ws='1-1')], layout='custom', custom='40,60', gap=48, valign='start'),
        ], bg=solid(c['sala']), padding='large', width='large', html_id='scrivici'))
        return out

    def pagina_404(self, t, titolo, sotto, bg):
        c, K = self.c, self.K
        return [t.section([
            K.centered(t, [K.label(t, 'Errore 404', color=mix(c['chiaro'], 88), align='center'),
                           K.headline(t, titolo, size=72, tag='h1', align='center', pad_bottom=18, color=c['chiaro'],
                                      accento=K.acc_media),
                           K.text(t, sotto, size=17, align='center', color=mix(c['chiaro'], 88)),
                           self.pulsante(t, 'Torna al negozio', self.P['negozio'], align='center', chiaro=True)]),
        ], bg=bg, padding='xlarge', style={'padding_top': 220, 'padding_bottom': 200})]


def pannelli(theme, nome, voci):
    """Le voci del menu che aprono un pannello del Mega Menu. voci: {titolo della voce: (chiave del template, titolo del
    pannello)}; il template (tipo megapanel) si scrive come le pagine, nel file <chiave>.json."""
    for it in theme['menu']['items']:
        if it['title'] in voci:
            it['pannello'] = voci[it['title']][0]
    for chiave, titolo in voci.values():
        theme['templates'][chiave] = {'file': chiave + '.json', 'type': 'megapanel', 'title': '%s — %s' % (nome, titolo)}


def tema_negozio(theme, nome):
    """Aggiunge a un tema (kit.tema) quello che serve a un negozio: WooCommerce richiesto, la scheda prodotto come
    template dinamico, le pagine del negozio in Template WooCommerce, le recensioni solo da chi ha comprato."""
    theme['templates']['prodotto'] = {'file': 'prodotto.json', 'type': 'single', 'title': '%s — Scheda prodotto' % nome,
                                      'post_type': 'product'}
    theme['singles'] = {'product': 'prodotto'}
    theme['woo'] = {'product_archive': 'negozio', 'product_category': 'negozio', 'cart': 'carrello', 'checkout': 'cassa',
                    'myaccount': 'account', 'recensioni': 'acquirenti'}
    theme['requires_plugin'] = {'slug': 'woocommerce', 'label': 'WooCommerce', 'check': 'class:WooCommerce',
                                'purchase_url': 'https://wordpress.org/plugins/woocommerce/'}
    return theme


def prodotto(sku, nome, slug, cat, prezzo, sconto, scorta, voto, recensioni, evidenza, foto, punti, testo, varianti=None,
             video=None, attributi=None, upsell=None, cross=None):
    """Un prodotto di prova: varianti = (attributo, [valori], [esauriti]) · video = nome del video della libreria (senza
    estensione) · attributi = {slug: [valori]} di scheda (marca, tipo di pelle…)."""
    d = {'sku': sku, 'nome': nome, 'slug': slug, 'cat': [cat] if isinstance(cat, str) else list(cat), 'prezzo': prezzo,
         'sconto': sconto, 'scorta': scorta, 'voto': voto, 'recensioni': recensioni, 'evidenza': evidenza, 'foto': foto,
         'punti': punti, 'testo': testo, 'upsell': upsell or [], 'cross': cross or []}
    if varianti:
        d['varianti'] = {'attributo': varianti[0], 'valori': varianti[1], 'esaurite': varianti[2]}
    if video:
        d['video'], d['poster'] = video + '.mp4', video + '.jpg'
    if attributi:
        d['attributi'] = attributi
    return d


def scrivi_prodotti(tema_id, media, categorie, prodotti, recensioni, nomi, attributi=None, dentro='',
                    titolo_dentro='Nella confezione'):
    """scripts/temi/prodotti/<id>.json per prodotti-demo.php; controlla che ogni foto e video sia fra i media del tema."""
    import json
    import os
    for d in prodotti:
        if dentro and 'dentro' not in d:
            d['dentro'] = dentro
        for f in d['foto'] + [d.get('video', ''), d.get('poster', '')]:
            assert not f or f in media, f
    for s, n, ds, f in categorie:
        assert f in media, f
    dati = {'tema': tema_id, 'titolo_dentro': titolo_dentro, 'attributi': attributi or {},
            'categorie': [{'slug': s, 'nome': n, 'descrizione': ds, 'foto': f} for s, n, ds, f in categorie],
            'prodotti': prodotti, 'recensioni': recensioni, 'nomi': nomi}
    out = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'prodotti')
    os.makedirs(out, exist_ok=True)
    with open(os.path.join(out, tema_id + '.json'), 'w', encoding='utf-8', newline='\n') as fh:
        json.dump(dati, fh, ensure_ascii=False, indent=1)
    print(tema_id, '·', len(prodotti), 'prodotti ·', len(categorie), 'categorie')
