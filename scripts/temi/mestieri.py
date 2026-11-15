# -*- coding: utf-8 -*-
"""Pezzi comuni dei dieci temi dei mestieri (10 nov 2026, categoria «Artigiani e mestieri»): elettricista, idraulico,
imbianchino, muratore, falegname, fabbro, meccanico, spazzacamino, giardiniere, vetraio. Siti di artigiani veri: chi
sono, cosa fanno, dove arrivano, quanto costa (il preventivo da comporre), le urgenze, i lavori prima e dopo, chi li
ha chiamati. Ogni pezzo è una tile della libreria (Tpl.classic: parte dal campione completo di un tema pubblicato).

Uso in un generatore:
    import mestieri as X
    X.testata(K, t, ID, 'Morsetto', 220, scuro=True, telefono=PHONE, tel=PHONE_TEL)
"""
from base import mix, radius, solid  # noqa: F401
import moda as M

CATEGORIA = 'Artigiani e mestieri'


def testata(K, t, tid, nome, larghezza, scuro, telefono='', tel='', **extra):
    """Il logo del marchio, il menu e il telefono in vista (chi chiama un artigiano lo cerca lì). Nei temi scuri la
    testata sta sopra l'apertura; in quelli chiari è classica, chiara, col logo scuro."""
    logo = '%s-logo-%s.png' % (tid, 'chiaro' if scuro else 'scuro')
    sez = M.testata(K, t, nome, logo, larghezza, overlay=scuro, scuro=scuro, **extra)
    s = sez[0]['children'][0]['children'][0]['children'][0]['settings']
    if telefono:
        s.update({'nav_phone': telefono, 'nav_phone_url': tel})
    return sez


def piede(K, t, tid, proporzione, claim, pagine, titolo_terza, terza_html, voci, legale, social, scuro, larghezza=360):
    """Il piede col logo vero: nei temi scuri sul nero col logo chiaro, in quelli chiari sulla «sala» col logo scuro."""
    logo = '%s-logo-%s.png' % (tid, 'chiaro' if scuro else 'scuro')
    return M.piede(K, t, logo, proporzione, claim, pagine, titolo_terza, terza_html, voci, legale, social,
                   larghezza=larghezza, fondo=None if scuro else K.c['sala'])


def fiducia(K, t, voci, colore=None):
    """La fila delle garanzie (trust-strip): voci = [(icona, html)]."""
    C = K.c
    return t.classic('trust-strip', {
        'items': [{'icon': i, 'icon_color': colore or C['acc'], 'text': s, 'badge': ''} for i, s in voci],
        'separator_char': '·', 'separator_color': C['filo'], 'text_color': C['testo'], 'text_size': 15, 'font_family': 'sans-serif',
        'align': 'center', 'flow': 'wrap', 'gap': 22, 'variant': 'inline', 'icon_size': 18}, style={})


def urgenze(K, t, titolo, accento, testo, telefono, tel, invito2, url2, foto, pos='center center'):
    """La fascia delle urgenze (CTA banner): il telefono grande, una seconda strada (il modulo), sopra una foto piena
    con la sfumatura scura solo dal lato del testo."""
    C = K.c
    return t.section([t.one([t.classic('cta-banner', {
        'headline': titolo, 'headline_accent': accento, 'headline_accent_italic': K.corsivo, 'subtitle': testo,
        'cta_text': telefono, 'cta_url': tel, 'cta_target': '_self', 'cta2_text': invito2, 'cta2_url': url2, 'cta2_target': '_self',
        'cta2_bg': 'transparent', 'cta2_color': C['chiaro'], 'cta2_border': mix(C['chiaro'], 55),
        'bg': {'type': 'image', 'image_url': K.img(foto), 'image_size': 'cover', 'image_position': pos,
               'overlay_color': C['nero'], 'overlay_opacity': 82, 'overlay_fade': 'left', 'overlay_fade_reach': 75},
        'text_color': C['chiaro'], 'accent_color': K.acc_media, 'subtitle_color': mix(C['chiaro'], 88),
        'cta_bg': K.acc_media, 'cta_bg_hover': C['chiaro'], 'cta_color': C['nero'], 'cta_color_hover': C['nero'],
        'cta_radius': radius(K.r), 'cta_radius_hover': radius(K.r), 'cta_size': 18,
        'cta_padding': {'top': 18, 'right': 30, 'bottom': 18, 'left': 30}, 'headline_font_family': 'heading', 'headline_size': 60,
        'headline_weight': K.peso, 'subtitle_size': 18, 'layout': 'stack', 'gap': 24, 'vertical_align': 'center',
        'banner_radius': radius(K.r), 'banner_radius_hover': radius(K.r)}, style={})], anim='fade')],
        padding='large', width='large', html_id='urgenze', style={'padding_top': 100, 'padding_bottom': 100})


def confronto(K, t, a, b, prima, dopo, alt='', ratio='4/3', pos='center center'):
    """Prima e dopo da trascinare (Confronto immagini)."""
    C = K.c
    return t.classic('imgcompare', {
        'before_image': K.img(a), 'after_image': K.img(b), 'before_label': prima, 'after_label': dopo, 'show_labels': True,
        'start_position': 50, 'orientation': 'horizontal', 'handle_color': C['chiaro'], 'handle_size': 44, 'handle_border': '2',
        'line_width': 2, 'height': '420', 'aspect_ratio': 'custom', 'aspect_ratio_custom': ratio, 'border_radius': K.r,
        'object_fit': 'cover', 'object_position': pos, 'autoplay': True, 'autoplay_delay': '2', 'autoplay_speed': '2'}, style={})


def prima_dopo(K, t, coppie, titolo, righe, testo=''):
    """Due (o tre) lavori prima e dopo, con la didascalia: coppie = [(prima, dopo, etichetta prima, etichetta dopo, titolo, testo)]."""
    n = len(coppie)
    w = {2: '1-2', 3: '1-3'}[n]
    cols = []
    for a, b, ep, ed, ti, tx in coppie:
        cols.append(t.col([confronto(K, t, a, b, ep, ed), K.headline(t, ti, size=24, tag='h3', pad_bottom=4),
                           K.text(t, tx, size=15)], wm=w, ws='1-1'))
    testa = [K.sechead(t, titolo, righe, tagline=testo, size=60)]
    return t.section([t.one(testa), t.row(cols, layout={2: '50-50', 3: '33-33-33'}[n], gap=28, valign='start')],
                     padding='large', width='large', style={'padding_top': 120, 'padding_bottom': 110})


def recensioni(K, t, voci, titolo, righe, fondo=True):
    """Tre persone che li hanno chiamati: voci = [(testo, nome, dove)]."""
    import pezzi_nov as P
    cols = [t.col([P.testimonianza(K, t, tx, nome, dove, size=26)], wm='1-3', ws='1-1') for tx, nome, dove in voci]
    return t.section([t.one([K.sechead(t, titolo, righe, size=56)]), t.row(cols, layout='33-33-33', gap=40, valign='start')],
                     bg=solid(K.c['sala']) if fondo else None, padding='large', width='large',
                     style={'padding_top': 120, 'padding_bottom': 110})


def zone(K, t, titolo, righe, testo, comuni, lat, lng, popup, scuro, hex_marker, html_id='zone'):
    """Dove arrivano: la mappa e i comuni a due colonne coi tempi."""
    met = (len(comuni) + 1) // 2
    return t.section([
        t.row([t.col([K.sechead(t, titolo, righe, size=54, layout='stack', size_mobile=36), K.text(t, testo),
                      K.specs(t, comuni[:met])], wm='1-3', ws='1-1'),
               t.col([K.specs(t, comuni[met:])], wm='1-6', ws='1-1'),
               t.col([K.mappa(t, lat, lng, popup, scuro=scuro, hex_marker=hex_marker)], wm='1-2', ws='1-1')],
              layout='custom', custom='30,20,50', gap=40, valign='start'),
    ], padding='large', width='large', html_id=html_id, style={'padding_top': 120, 'padding_bottom': 120})


def orari(K, t, voci, chiaro=False):
    """Gli orari in una fascia (Hours strip): voci = [(giorni, ore, nota)]."""
    C = K.c
    base = C['chiaro'] if chiaro else C['testo']
    return t.classic('hoursstrip', {
        'items': [{'day': d, 'time': h, 'note': n} for d, h, n in voci], 'columns': len(voci), 'band_padding_y': 30,
        'show_dividers': True, 'divider_color': mix(base, 18), 'band_border': False, 'day_color': K.acc_media if chiaro else C['acc'],
        'day_size': 13, 'time_font_family': 'heading', 'time_color': base, 'time_size': 44, 'time_weight': K.peso,
        'note_color': mix(base, 70), 'note_size': 14, 'mono_font_family': ''}, style={})


def preventivo(K, t, gruppi, opzioni, titolo, link, nota, periodo='', base=0, testo_pulsante='Chiedi il preventivo',
               vista='righe'):
    """Il preventivo da comporre (Configuratore): gruppi = [(id, titolo, 'uno'|'piu', obbligatorio, aiuto)],
    opzioni = [(id, gruppo, nome, descrizione, prezzo, badge, predefinita)]."""
    C = K.c
    return t.classic('configuratore', {
        'gruppi': [{'id': i, 'titolo': ti, 'tipo': tp, 'obbligatorio': ob, 'aiuto': ai} for i, ti, tp, ob, ai in gruppi],
        'opzioni': [{'id': i, 'gruppo': g, 'nome': n, 'descrizione': d, 'prezzo': p, 'immagine': '', 'badge': b, 'predefinita': pr,
                     'punti': '', 'min': 0, 'max': 10} for i, g, n, d, p, b, pr in opzioni],
        'prezzo_base': base, 'valuta': '€', 'testo_totale': 'Indicativo', 'nota_prezzo': nota, 'periodo': periodo,
        'mostra_differenze': True, 'indicatori': [], 'riepilogo': True, 'titolo_riepilogo': titolo, 'testo_azzera': 'Ricomincia',
        'testo_pulsante': testo_pulsante, 'link_azione': link, 'campo_modulo': 'lavoro', 'vista_opzioni': vista, 'disposizione': 'lato',
        'sfondo_scheda': C['sala'], 'colore_scelta': C['acc'], 'colore_testo': C['testo'], 'colore_tenue': C['tenue'],
        # nei temi chiari il prezzo nell'accento, sulla scelta tinta d'accento, restava sotto 4,5 : 1
        'colore_prezzo': C['testo'] if K.tema_chiaro else C['acc'], 'sfondo_riepilogo': C['sala'], 'sfondo_badge': C['acc'], 'colore_badge': C['on_acc'],
        'raggio_scheda': radius(K.r), 'ombra': 'none', 'gruppo_font_family': 'heading', 'gruppo_font_size': 28,
        'gruppo_font_weight': K.peso, 'gruppo_color': C['testo'], 'opzione_font_family': 'sans-serif', 'opzione_font_size': 17,
        'opzione_font_weight': '600', 'descrizione_font_family': 'sans-serif', 'descrizione_font_size': 14,
        'descrizione_font_weight': '400', 'prezzo_font_family': 'heading', 'prezzo_font_size': 20, 'prezzo_font_weight': K.peso,
        'totale_font_family': 'heading', 'totale_font_size': 60, 'totale_font_weight': K.peso, 'totale_color': C['acc'], 'gap': 16},
        style={})


def sezione_preventivo(K, t, occhiello, righe, testo, conf, html_id='preventivo'):
    return t.section([
        t.one([K.sechead(t, occhiello, righe, tagline=testo, size=60)]),
        t.one([conf]),
    ], padding='large', width='large', html_id=html_id, style={'padding_top': 120, 'padding_bottom': 120})


def certificati(K, t, voci):
    """Le abilitazioni e le norme (Certificazioni, a fascia): voci = [(norma, titolo, ambito, simbolo)]."""
    C = K.c
    return t.classic('certificazioni', {
        'voci': [{'id': '%s-c%d' % (K.id, k), 'norma': n, 'titolo': ti, 'ente': '', 'numero': '', 'ambito': a, 'rilascio': '',
                  'scadenza': '', 'simbolo': s, 'logo': '', 'documento': ''} for k, (n, ti, a, s) in enumerate(voci)],
        'vista': 'fascia', 'scadute': 'nascondi', 'mostra_documento': False, 'accento': C['acc'], 'norma_font_family': 'heading',
        'norma_font_size': 24, 'norma_font_weight': K.peso, 'norma_color': C['testo']}, style={})


def lettera(K, t, occhiello, righe, testo, bg=None):
    return M.lettera(K, t, occhiello, righe, testo, invito='Iscriviti', grazie='Fatto: ti scriviamo solo quando serve.', bg=bg)
