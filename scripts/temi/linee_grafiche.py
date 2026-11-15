# -*- coding: utf-8 -*-
"""Linee grafiche dei temi (13 nov 2026). L'utente: «troppo comodo rifare tutto il sito uguale, dovresti sforzarti per dare
linee grafiche diverse tra loro, strutture creative». Le tile che tornano in tanti temi erano configurate quasi sempre
allo stesso modo (cta-banner e trust-strip 96%, desclist 91%, pricelist 90%, testimonial 86%, statstrip e info-cards
oltre l'80% a quattro colonne, team 77%).

Ogni tema riceve un PROFILO (una linea grafica coerente: editoriale, morbida, geometrica, essenziale, rivista, tecnica)
che decide come si presentano quelle tile: forma delle foto e allineamento del team, disposizione delle testimonianze,
colonne dei numeri e delle schede, stile dei numeri dei passaggi, banner d'invito, elenchi, listini, pillole di fiducia,
verso delle carte, segni dei prezzi, verso dei nastri, elenchi dei lavori, carte impilate, citazioni. Solo opzioni
di struttura: i colori restano i token del tema (e dove una forma ne vuole uno, è un token).
Si cambia solo ciò che era rimasto al valore di sempre; testata e piè di pagina non si toccano.

I profili vanno a turno nell'ordine di creazione dei temi (temi vicini, profili diversi). La scelta di ogni tema si scrive in
linee_grafiche.json, così chi rigenera un tema ritrova la sua linea (base.write_theme la applica).
uso: python linee_grafiche.py [--scrivi] [tema …]"""
import json
import os
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
SCELTE = os.path.join(QUI, 'linee_grafiche.json')
PRIMARIO_14 = 'color-mix(in srgb, var(--olo-color-primary) 14%, transparent)'
TESTO_6 = 'color-mix(in srgb, var(--olo-color-text) 6%, transparent)'
TESTO_16 = 'color-mix(in srgb, var(--olo-color-text) 16%, transparent)'

PROFILI = {
    'editoriale': {'testimonial': 'editorial', 'avatar': 'circle', 'team': ('square', 'left'), 'stat': (4, 'left'),
                   'cards': (4, 'plain'), 'passi': (4, 'plain', 'left'), 'cta': 'split-2', 'desc': 'stacked', 'icone': 'none',
                   'listino': 'solid', 'fiducia': ('inline', 'left'), 'carte': ('vertical', 'bottom'), 'prodotti': 4, 'prezzi': ('checkmark', 'minimal'), 'nastro': 'left', 'lavori': (40, True, True), 'pila': ('right', 420), 'citazione': 'left'},
    'morbida': {'testimonial': 'single', 'avatar': 'circle', 'team': ('circle', 'center'), 'stat': (2, 'center'),
                'cards': (2, 'circle'), 'passi': (2, 'circle', 'center'), 'cta': 'stack', 'desc': 'grid', 'icone': 'circle',
                'listino': 'dotted', 'fiducia': ('pill', 'center'), 'carte': ('zoom-flip', 'center'), 'prodotti': 3, 'prezzi': ('dot', 'pill'), 'nastro': 'right', 'lavori': (32, True, True), 'pila': ('left', 400), 'citazione': 'center'},
    'geometrica': {'testimonial': 'single', 'avatar': 'square', 'team': ('square', 'center'), 'stat': (4, 'center'),
                   'cards': (4, 'circle'), 'passi': (4, 'outline', 'left'), 'cta': 'split-3', 'desc': 'inline', 'icone': 'square',
                   'listino': 'dashed', 'fiducia': ('pill', 'space-between'), 'carte': ('cube', 'bottom'), 'prodotti': 4, 'prezzi': ('arrow', 'classic'), 'nastro': 'left', 'lavori': (36, True, True), 'pila': ('left', 440), 'citazione': 'left'},
    'essenziale': {'testimonial': 'single', 'avatar': 'circle', 'team': ('rounded', 'left'), 'stat': (2, 'left'),
                   'cards': (2, 'plain'), 'passi': (2, 'plain', 'left'), 'cta': 'split-2', 'desc': 'inline', 'icone': 'none',
                   'listino': 'none', 'fiducia': ('inline', 'left'), 'carte': ('slide-flip', 'top'), 'prodotti': 3, 'prezzi': ('dot', 'minimal'), 'nastro': 'right', 'lavori': (28, False, False), 'pila': ('right', 360), 'citazione': 'left'},
    'rivista': {'testimonial': 'editorial', 'avatar': 'square', 'team': ('rounded', 'center'), 'stat': (4, 'center'),
                'cards': (4, 'plain'), 'passi': (4, 'outline', 'center'), 'cta': 'stack', 'desc': 'grid', 'icone': 'rounded',
                'listino': 'dotted', 'fiducia': ('inline', 'center'), 'carte': ('horizontal', 'bottom'), 'prodotti': 4, 'prezzi': ('star', 'classic'), 'nastro': 'left', 'lavori': (48, False, True), 'pila': ('right', 460), 'citazione': 'center'},
    'tecnica': {'testimonial': 'single', 'avatar': 'square', 'team': ('square', 'left'), 'stat': (4, 'left'),
                'cards': (2, 'circle'), 'passi': (2, 'outline', 'left'), 'cta': 'split-3', 'desc': 'stacked', 'icone': 'square',
                'listino': 'dashed', 'fiducia': ('pill', 'left'), 'carte': ('vertical', 'top'), 'prodotti': 3, 'prezzi': ('checkmark', 'classic'), 'nastro': 'right', 'lavori': (30, True, True), 'pila': ('left', 380), 'citazione': 'left'},
}
GIRO = ['editoriale', 'morbida', 'geometrica', 'essenziale', 'rivista', 'tecnica']


def n_voci(s, *chiavi):
    for k in chiavi:
        if isinstance(s.get(k), list):
            return len(s[k])
    return 0


def applica_profilo(nodo, P, cambi):
    """Cambia le tile di un albero secondo il profilo P; cambi raccoglie (tipo, chiave, prima, dopo)."""
    def metti(s, k, v, tipo):
        if s.get(k) != v:
            cambi.append((tipo, k, s.get(k), v))
            s[k] = v

    def larghezze(row):
        # la frazione di riga di ogni colonna: dalla larghezza della colonna, se no dalla disposizione della riga
        cols = row.get('children') or []
        rs = row.get('settings') or {}
        lay = str(rs.get('custom_widths') or rs.get('layout') or '100')
        parti = [float(x) for x in re.findall(r'\d+', lay)] if re.search(r'\d', lay) else []
        out = []
        for i, c in enumerate(cols):
            wm = str((c.get('settings') or {}).get('width_medium') or '')
            m = re.match(r'^(\d+)-(\d+)$', wm)
            if m:
                out.append(int(m.group(1)) / float(m.group(2)))
            elif len(parti) == len(cols) and sum(parti) > 0:
                out.append(parti[i] / sum(parti))
            else:
                out.append(1.0 / max(1, len(cols)))
        return out

    def visita(n, largo=1.0):
        if isinstance(n, list):
            for x in n:
                visita(x, largo)
            return
        if not isinstance(n, dict):
            return
        if n.get('type') in ('row', 'inner-columns') and isinstance(n.get('children'), list):
            for c, f in zip(n['children'], larghezze(n)):
                visita(c, largo * f)
            return
        tipo, s = n.get('type'), n.get('settings')
        if isinstance(s, dict):
            if tipo == 'testimonial' and s.get('layout') in ('editorial', 'single', None, '') and n_voci(s, 'testimonials', 'items') <= 1:
                metti(s, 'layout', P['testimonial'], tipo)
                metti(s, 'avatar_shape', P['avatar'], tipo)
                if P['testimonial'] == 'single' and not s.get('bg_color'):
                    # la disposizione singola disegna un riquadro: senza colore era un grigio chiaro fisso, e il testo
                    # chiaro (sopra le foto) non si leggeva più
                    chiaro = any(k in str(s.get('text_color', '')) for k in ('olo-color-light', '#fff', '#FFF', 'rgb(255'))
                    metti(s, 'bg_color', 'color-mix(in srgb, var(--olo-color-dark) 52%, transparent)' if chiaro else TESTO_6, tipo)
            elif tipo == 'team' and s.get('photo_shape', 'rounded') in ('rounded', 'circle', ''):
                metti(s, 'photo_shape', P['team'][0], tipo)
                metti(s, 'info_align', P['team'][1], tipo)
            elif tipo == 'statstrip':
                k = n_voci(s, 'items', 'stats')
                col, al = P['stat']
                if col == 2 and k % 2:
                    col = k if k <= 4 else 3
                if str(s.get('columns', 4)) in ('4', str(k)):
                    metti(s, 'columns', col if k >= col else k, tipo)
                metti(s, 'align', al, tipo)
            elif tipo == 'info-cards':
                k = n_voci(s, 'items')
                col, forma = P['cards']
                if str(s.get('columns', 4)) == '4' and k == 4:
                    metti(s, 'columns', col, tipo)
                if forma != s.get('counter_shape', 'plain'):
                    metti(s, 'counter_shape', forma, tipo)
                    if forma == 'circle' and not s.get('counter_bg'):
                        metti(s, 'counter_bg', PRIMARIO_14, tipo)
                # sul cerchio tinto il numero nel colore del testo: il tenue restava a 3,8:1 (misura del 14 nov 2026)
                if s.get('counter_shape') == 'circle' and 'text-muted' in str(s.get('counter_color', '')):
                    metti(s, 'counter_color', 'var(--olo-color-text)', tipo)
            elif tipo == 'process-steps':
                k = n_voci(s, 'items')
                col, stile, al = P['passi']
                if col == 2 and k % 2:
                    col = min(4, k)
                if str(s.get('columns', 4)) == '4' and k >= 4:
                    metti(s, 'columns', col, tipo)
                    metti(s, 'columns_tablet', 2, tipo)
                # un'etichetta più lunga di un numero («T−90», «1 · 2 ore») nel tondo non ci sta: resta scritta (15 nov 2026)
                if max([len(str(it.get('number') or '')) for it in (s.get('items') or [])] or [0]) > 3:
                    stile = 'plain'
                metti(s, 'number_style', stile, tipo)
                if stile == 'circle' and not s.get('number_bg'):
                    metti(s, 'number_bg', PRIMARIO_14, tipo)
                # misura del 14 nov 2026: nell'anello vuoto il numero «di contrasto» (pensato sul tondo pieno) spariva sul
                # fondo; nei tondi il carattere è il 60% della grandezza, e 13 dava numeri di 8 px
                if stile == 'outline' and 'contrast' in str(s.get('number_color', '')):
                    metti(s, 'number_color', s.get('number_bg') or 'var(--olo-color-primary)', tipo)
                if stile in ('circle', 'outline') and int(s.get('number_size') or 40) < 22:
                    metti(s, 'number_size', 22, tipo)
                # sul tondo tinto del primario, il numero nel primario restava sotto 4,5:1: nel colore del testo
                if stile == 'circle' and s.get('number_bg') == PRIMARIO_14 and 'text)' not in str(s.get('number_color', '')):
                    metti(s, 'number_color', 'var(--olo-color-text)', tipo)
                metti(s, 'align', al, tipo)
            elif tipo == 'cta-banner' and s.get('layout', 'stack') == 'stack':
                metti(s, 'layout', P['cta'], tipo)
            elif tipo == 'desclist' and s.get('layout', 'vertical') in ('vertical', 'stacked', 'inline', 'grid'):
                # in linea e a griglia solo in colonne larghe: in una colonna stretta il valore andava a capo a ogni
                # parola (Morsetto, «entro / 2 / ore»)
                metti(s, 'layout', P['desc'] if (largo >= 0.6 or P['desc'] == 'stacked') else 'vertical', tipo)
            elif tipo == 'iconlist' and s.get('icon_shape', 'none') == 'none' and s.get('layout', 'vertical') == 'vertical':
                if P['icone'] != 'none':
                    metti(s, 'icon_shape', P['icone'], tipo)
                    if not s.get('icon_bg_color'):
                        metti(s, 'icon_bg_color', PRIMARIO_14, tipo)
            elif tipo == 'pricelist' and s.get('separator_style', 'dotted') == 'dotted':
                metti(s, 'separator_style', P['listino'], tipo)
            elif tipo == 'trust-strip' and s.get('variant', 'inline') == 'inline':
                v, al = P['fiducia']
                metti(s, 'variant', v, tipo)
                metti(s, 'align', al, tipo)
                if v == 'pill':
                    metti(s, 'pill_bg', TESTO_6, tipo)
                    metti(s, 'pill_border', TESTO_16, tipo)
            elif tipo == 'flipcard' and s.get('flip_direction', 'horizontal') == 'horizontal':
                d, va = P['carte']
                metti(s, 'flip_direction', d, tipo)
                if s.get('front_valign', 'bottom') == 'bottom':
                    metti(s, 'front_valign', va, tipo)
            elif tipo == 'pricing' and s.get('check_style', 'circle-check') == 'circle-check' and s.get('badge_style', 'pill') == 'pill':
                metti(s, 'check_style', P['prezzi'][0], tipo)
                metti(s, 'badge_style', P['prezzi'][1], tipo)
            elif tipo == 'marquee' and s.get('direction', 'left') == 'left':
                metti(s, 'direction', P['nastro'], tipo)
            elif tipo == 'worklist' and str(s.get('title_size', 40)) in ('36', '40'):
                ts, freccia, cat = P['lavori']
                metti(s, 'title_size', ts, tipo)
                metti(s, 'show_arrow', freccia, tipo)
                metti(s, 'show_category', cat, tipo)
            elif tipo == 'stackscroll' and s.get('media_position', 'right') == 'right':
                lato, alto = P['pila']
                metti(s, 'media_position', lato, tipo)
                metti(s, 'card_min_height', alto, tipo)
            elif tipo == 'quotation' and s.get('alignment', 'left') in ('left', ''):
                metti(s, 'alignment', P['citazione'], tipo)
            elif tipo == 'product-cards' and str(s.get('columns', 4)) == '4':
                k = n_voci(s, 'items', 'products')
                if P['prodotti'] == 3 and k in (3, 6):
                    metti(s, 'columns', 3, tipo)
        for v in n.values():
            visita(v, largo)
    visita(nodo)


def applica_tema(tid, nome_file, built):
    """Per base.write_theme: un tema rigenerato ritrova la sua linea grafica (linee_grafiche.json)."""
    if nome_file in ('header.json', 'footer.json'):
        return
    p = profilo_di(tid)
    if p:
        applica_profilo(built, PROFILI[p], [])


def profilo_di(tid):
    scelte = json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}
    return scelte.get(tid)


def main():
    scrivi = '--scrivi' in sys.argv
    scelti = [a for a in sys.argv[1:] if not a.startswith('--')]
    date = json.load(open(os.path.join(TEMI, '..', 'temi-creati.json'), encoding='utf-8'))
    temi = sorted(os.listdir(TEMI), key=lambda x: (date.get(x, ''), x))
    scelte = {t: GIRO[i % len(GIRO)] for i, t in enumerate(temi)}
    from collections import Counter
    tot = Counter()
    for tid in temi:
        if scelti and tid not in scelti:
            continue
        P = PROFILI[scelte[tid]]
        d = os.path.join(TEMI, tid)
        for f in sorted(os.listdir(d)):
            if not f.endswith('.json') or f in ('theme.json', 'header.json', 'footer.json'):
                continue
            p = os.path.join(d, f)
            j = json.load(open(p, encoding='utf-8'))
            cambi = []
            applica_profilo(j, P, cambi)
            for c in cambi:
                tot[(c[0], c[1], str(c[3]))] += 1
            if cambi and scrivi:
                open(p, 'w', encoding='utf-8', newline='\n').write(json.dumps(j, ensure_ascii=False, separators=(',', ':')))
    if scrivi:
        json.dump(scelte, open(SCELTE, 'w', encoding='utf-8'), ensure_ascii=False, indent=0)
    print('profili:', Counter(scelte.values()))
    for (ty, k, v), n in sorted(tot.items()):
        print('  %-14s %-16s → %-24s %d' % (ty, k, v[:24], n))
    print('(scritto)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
