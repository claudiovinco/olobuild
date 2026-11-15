# -*- coding: utf-8 -*-
"""La regia delle gallerie (14 nov 2026). L'utente: «usa anche tutte le funzioni di gallery pro, non le vedo da nessuna
parte… crea delle variazioni tra i vari temi… anche la stessa visualizzazione a pieno schermo ha tante opzioni, usane
qualcuna ogni tanto». Delle 78 Galleria pro dei temi, 68 erano «Espandi» o «Coverflow 3D», nessuna aveva un'entrata, sei
un effetto al passaggio, e lo schermo intero era sempre lo scorrimento senza miniature.

Ogni galleria prende una REGIA: schema (giustificato, masonry, mosaico, collage, sparso con le polaroid, cascata, metro,
esagoni, diagonale, puzzle, deriva, nastro automatico, nastro doppio, griglia in bianco e nero, espandi, coverflow),
entrata, effetto al passaggio, didascalia (solo se le foto ne hanno), a volte filtro, cornice o bordo animato, e lo
schermo intero (dissolvenza, scala o scorrimento; miniature sotto, a destra o a sinistra, su una o due file).
La scelta segue il profilo del tema (linee_grafiche.json), il numero di foto e la larghezza della colonna; due gallerie
vicine non hanno mai la stessa regia. Le scelte stanno in gallerie_scelte.json; base.write_theme le riapplica.
uso: python gallerie.py [--scrivi]"""
import json
import math
import os
import re
import sys

QUI = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, QUI)
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
SCELTE = os.path.join(QUI, 'gallerie_scelte.json')
PROFILI = os.path.join(QUI, 'linee_grafiche.json')
PRIMARIO = 'var(--olo-color-primary)'

PREFERENZE = {
    'editoriale': ['giustificato', 'masonry', 'mosaico', 'griglia_bn', 'espandi', 'nastro'],
    'morbida': ['sparso', 'collage', 'deriva', 'masonry', 'diagonale', 'coverflow'],
    'geometrica': ['metro', 'esagoni', 'diagonale', 'puzzle', 'mosaico', 'coverflow'],
    'essenziale': ['nastro', 'griglia_bn', 'giustificato', 'espandi', 'coverflow'],
    'rivista': ['mosaico', 'collage', 'nastro_doppio', 'deriva', 'giustificato', 'coverflow'],
    'tecnica': ['coverflow', 'metro', 'esagoni', 'nastro', 'diagonale', 'espandi'],
}
# lo schermo intero, due modi per profilo, a turno
SCHERMO = {
    'editoriale': [{'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1'}, {'lightbox_thumbs': 'none', 'lightbox_animation': 'fade'}],
    'morbida': [{'lightbox_thumbs': 'none', 'lightbox_animation': 'scale'}, {'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '1'}],
    'geometrica': [{'lightbox_thumbs': 'right', 'lightbox_thumbs_rows': '2'}, {'lightbox_thumbs': 'none', 'lightbox_animation': 'slide'}],
    'essenziale': [{'lightbox_thumbs': 'none', 'lightbox_animation': 'fade'}, {'lightbox_thumbs': 'right', 'lightbox_thumbs_rows': '1'}],
    'rivista': [{'lightbox_thumbs': 'left', 'lightbox_thumbs_rows': '1'}, {'lightbox_thumbs': 'none', 'lightbox_animation': 'scale'}],
    'tecnica': [{'lightbox_thumbs': 'bottom', 'lightbox_thumbs_rows': '2'}, {'lightbox_thumbs': 'left', 'lightbox_thumbs_rows': '2'}],
}
CLASSICA, NASTRO = 'classic', 'strip'


def ok(nome, n, largo):
    """Le regie che reggono quel numero di foto in quella larghezza."""
    largo_ok = largo >= 0.6
    return {
        'giustificato': n >= 6,  # con meno foto l'ultima si allarga da sola a tutta riga
        'masonry': n >= 5,
        'mosaico': n in (4, 9) and largo_ok,  # il disegno 2×2 + due + una larga chiude le righe solo così
        # la prima foto prende 2×2: in 3 colonne le righe si chiudono con 3, 6, 9 foto; in 2 colonne con 3, 5, 7
        'collage': n in ((3, 5, 7) if largo < 0.6 else (3, 6, 9)),
        'sparso': 4 <= n <= 8 and largo_ok,
        'cascata': False,  # sul telefono le carte uscivano dal bordo (14 nov 2026)
        'metro': n == 4 and largo_ok,  # il disegno metro (2×2, due, una larga) chiude le righe solo con 4 foto
        'esagoni': n >= 5 and largo_ok,
        'diagonale': 3 <= n <= 6 and n != 5,
        'puzzle': n in (6, 8, 9, 12) and largo_ok,
        'deriva': n >= 6 and largo_ok,
        'nastro': n >= 4,
        'nastro_doppio': n >= 6 and largo_ok,
        'griglia_bn': 3 <= n <= 8 and n not in (5, 7),
        'espandi': 3 <= n <= 6,
        'coverflow': n >= 4,
    }[nome]


def regia(nome, n, largo, didascalie):
    """Le impostazioni della regia (solo chiavi della Galleria pro)."""
    cap = lambda tipo: tipo if didascalie else 'none'
    stretto = largo < 0.6
    base = {'entrance_stagger': 110, 'entrance_duration': 700, 'continuous': '', 'filter': 'none', 'filter_hover_restore': False,
            'frame': 'none', 'anim_border': 'none', 'rows': 0}
    R = {
        'giustificato': {'layout': 'justified', 'layout_family': CLASSICA, 'img_height': '180px' if stretto else '250px', 'gap': 10,
                         'aspect_ratio': 'auto', 'entrance': 'fade-up', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.05,
                         'hover_caption': cap('fade')},
        'masonry': {'layout': 'masonry', 'layout_family': CLASSICA, 'columns': 2 if stretto else 3, 'gap': 12, 'aspect_ratio': 'auto',
                    'img_height': '', 'entrance': 'blur-in', 'hover_effect': 'lift', 'hover_caption': cap('slide-up')},
        'mosaico': {'layout': 'mosaic', 'layout_family': CLASSICA, 'columns': 4, 'img_height': '190px', 'gap': 10,
                    'entrance': 'zoom-center', 'hover_effect': 'blur-peers', 'hover_caption': cap('centered')},
        'collage': {'layout': 'collage', 'layout_family': CLASSICA, 'columns': 2 if stretto else 3, 'img_height': '190px', 'gap': 12,
                    'entrance': 'split-sides', 'hover_effect': 'tilt3d', 'hover_tilt_angle': 8, 'hover_caption': cap('frame')},
        'sparso': {'layout': 'scattered', 'layout_family': CLASSICA, 'columns': 4, 'gap': 16, 'frame': 'polaroid',
                   'entrance': 'fall', 'hover_effect': 'lift', 'hover_caption': 'none'},
        'cascata': {'layout': 'cascade', 'layout_family': CLASSICA, 'cascade_spread': 70, 'cascade_overlap': 35, 'cascade_rotation': 6,
                    'entrance': 'none', 'hover_effect': 'lift', 'hover_caption': cap('slide-up')},
        'metro': {'layout': 'metro', 'layout_family': CLASSICA, 'columns': 4, 'metro_cell_height': 150, 'gap': 10,
                  'entrance': 'land', 'hover_effect': 'zoom', 'hover_zoom_scale': 1.06, 'hover_caption': cap('slide-up'),
                  'anim_border': 'corners', 'anim_border_color': PRIMARIO, 'anim_border_thickness': 2, 'anim_border_inset': 14},
        'esagoni': {'layout': 'hexgrid', 'layout_family': CLASSICA, 'columns': 4, 'gap': 8, 'entrance': 'zoom-center',
                    'hover_effect': 'zoom', 'hover_zoom_scale': 1.08, 'hover_caption': 'none'},
        'diagonale': {'layout': 'diagonal', 'layout_family': CLASSICA, 'columns': n if n <= 4 else 3, 'aspect_ratio': 'custom',
                      'aspect_ratio_custom': '3/4', 'gap': 12, 'entrance': 'wind', 'hover_effect': 'magnetic',
                      'hover_magnetic_strength': 16, 'hover_caption': cap('slide-up')},
        'puzzle': {'layout': 'puzzle', 'layout_family': CLASSICA, 'columns': 4 if n in (8, 12) else 3, 'img_height': '220px',
                   'puzzle_style': 'wave', 'entrance': 'flip', 'hover_effect': 'tilt3d', 'hover_tilt_angle': 6, 'hover_caption': 'none'},
        'deriva': {'layout': 'drift', 'layout_family': CLASSICA, 'drift_height': 900, 'drift_intensity': 35, 'drift_rotation': 6,
                   'entrance': 'none', 'hover_effect': 'lift', 'hover_caption': cap('fade')},
        'nastro': {'layout': 'strip_marquee', 'layout_family': NASTRO, 'strip_height': 220 if stretto else 290,
                   'strip_item_width': max(220, int(math.ceil(1500 / n)) - 12), 'gap': 12, 'strip_speed': max(28, n * 7),
                   'strip_fade_edges': True, 'strip_pause_hover': True, 'strip_direction': 'left', 'entrance': 'none',
                   'hover_effect': 'zoom', 'hover_zoom_scale': 1.05, 'hover_caption': cap('slide-up')},
        'nastro_doppio': {'layout': 'strip_split', 'layout_family': NASTRO, 'strip_height': 440,
                          'strip_item_width': max(220, int(math.ceil(3000 / n)) - 12), 'gap': 12, 'strip_speed': max(36, n * 6),
                          'strip_fade_edges': True, 'strip_pause_hover': True, 'entrance': 'none', 'hover_effect': 'zoom',
                          'hover_zoom_scale': 1.05, 'hover_caption': cap('slide-up')},
        'griglia_bn': {'layout': 'grid', 'layout_family': CLASSICA, 'columns': n if n <= 4 else (4 if n % 4 == 0 else 3),
                       'aspect_ratio': 'custom', 'aspect_ratio_custom': '4/5', 'gap': 14, 'entrance': 'fade-scale',
                       'hover_effect': 'none', 'filter': 'grayscale', 'filter_hover_restore': True, 'frame': 'inset',
                       'frame_color': 'var(--olo-color-light)', 'hover_caption': cap('fade')},
        'espandi': {'layout': 'expand', 'layout_family': CLASSICA, 'columns': n, 'img_height': '300px' if not stretto else '220px',
                    'expand_ratio': 2.6, 'expand_shrink': 0.6, 'entrance': 'fade-scale', 'hover_effect': 'none',
                    'hover_caption': cap('slide-up'), 'continuous': 'kenburns', 'continuous_speed': 24},
        'coverflow': {'layout': 'strip_coverflow', 'layout_family': NASTRO, 'filmstrip_autoplay': True, 'filmstrip_speed': 4.5,
                      'filmstrip_side_tilt': 32, 'entrance': 'none', 'hover_effect': 'none', 'hover_caption': cap('slide-up')},
    }[nome]
    out = dict(base)
    out.update(R)
    return out


def indicatore(k):
    return ['progress', 'fraction', 'lines', 'dots'][k % 4]


def gallerie_di(built):
    """Le Galleria pro di una pagina, in ordine, con la frazione di riga della colonna che le contiene."""
    out = []

    def larghezze(row):
        cols = row.get('children') or []
        rs = row.get('settings') or {}
        lay = str(rs.get('custom_widths') or rs.get('layout') or '100')
        parti = [float(x) for x in re.findall(r'\d+(?:\.\d+)?', lay)] if re.search(r'\d', lay) else []
        res = []
        for i, c in enumerate(cols):
            m = re.match(r'^(\d+)-(\d+)$', str((c.get('settings') or {}).get('width_medium') or ''))
            if m:
                res.append(int(m.group(1)) / float(m.group(2)))
            elif len(parti) == len(cols) and sum(parti) > 0:
                res.append(parti[i] / sum(parti))
            else:
                res.append(1.0 / max(1, len(cols)))
        return res

    def w(n, largo=1.0):
        if isinstance(n, list):
            for x in n:
                w(x, largo)
            return
        if not isinstance(n, dict):
            return
        if n.get('type') == 'progallery':
            out.append((n, largo))
            return
        if n.get('type') in ('row', 'inner-columns') and isinstance(n.get('children'), list):
            for c, f in zip(n['children'], larghezze(n)):
                w(c, largo * f)
            return
        for v in n.values():
            if isinstance(v, (dict, list)):
                w(v, largo)
    c = built['content'] if isinstance(built, dict) and 'content' in built else built
    w(c)
    return out


def applica(tile, scelta):
    s = tile.setdefault('settings', {})
    imgs = s.get('images') or []
    did = any((i.get('caption') or '').strip() for i in imgs if isinstance(i, dict))
    s.update(regia(scelta['regia'], len(imgs), scelta.get('largo', 1.0), did))
    if scelta['regia'] == 'coverflow':
        s['filmstrip_dots'] = scelta.get('indicatore', 'dots')
    s['lightbox'] = True
    s['lightbox_animation'] = 'slide'
    s['lightbox_thumbs'] = 'none'
    s['lightbox_thumbs_rows'] = '1'
    s.update(scelta.get('schermo') or {})
    s['show_caption'] = did


def applica_tema(tid, nome_file, built):
    """Per base.write_theme."""
    sc = json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}
    for k, (tile, largo) in enumerate(gallerie_di(built)):
        v = sc.get('%s/%s#%d' % (tid, nome_file, k))
        if v and len(tile.get('settings', {}).get('images') or []) == v.get('n'):
            applica(tile, v)


def main():
    sys.stdout.reconfigure(encoding='utf-8')
    scrivi = '--scrivi' in sys.argv
    profili = json.load(open(PROFILI, encoding='utf-8'))
    date = json.load(open(os.path.join(TEMI, '..', 'temi-creati.json'), encoding='utf-8'))
    scelte = {}
    conta, prima, giro = {}, None, {}
    for tid in sorted(os.listdir(TEMI), key=lambda x: (date.get(x, ''), x)):
        pr = profili.get(tid, 'editoriale')
        nel_tema = []
        for f in sorted(os.listdir(os.path.join(TEMI, tid))):
            if not f.endswith('.json') or f in ('theme.json', 'header.json', 'footer.json'):
                continue
            p = os.path.join(TEMI, tid, f)
            testo = open(p, encoding='utf-8').read()
            if '"progallery"' not in testo:
                continue
            j = json.loads(testo)
            for k, (tile, largo) in enumerate(gallerie_di(j)):
                n = len((tile.get('settings') or {}).get('images') or [])
                pref = PREFERENZE[pr]
                inizio = giro.get(pr, 0)
                scelta = None
                for d in range(len(pref)):
                    nome = pref[(inizio + d) % len(pref)]
                    if ok(nome, n, largo) and nome != prima and nome not in nel_tema:
                        scelta = nome
                        break
                if not scelta:
                    # ripiego a turno fra le regie che reggono poche foto, perché non vincano sempre le stesse due
                    rip = ['griglia_bn', 'diagonale', 'nastro', 'mosaico', 'sparso', 'masonry', 'collage', 'espandi', 'coverflow']
                    t0 = giro.get('ripiego', 0)
                    for d in range(len(rip)):
                        nome = rip[(t0 + d) % len(rip)]
                        if ok(nome, n, largo) and nome != prima and nome not in nel_tema:
                            scelta = nome
                            giro['ripiego'] = t0 + d + 1
                            break
                if not scelta:
                    continue
                giro[pr] = inizio + 1
                v = {'regia': scelta, 'n': n, 'largo': round(largo, 2), 'indicatore': indicatore(len(scelte)),
                     'schermo': SCHERMO[pr][len(scelte) % 2]}
                scelte['%s/%s#%d' % (tid, f, k)] = v
                conta[scelta] = conta.get(scelta, 0) + 1
                prima = scelta
                nel_tema.append(scelta)
                applica(tile, v)
            if scrivi:
                compatto = not testo.lstrip().startswith('{\n') and not testo.lstrip().startswith('[\n')
                open(p, 'w', encoding='utf-8', newline='\n').write(
                    json.dumps(j, ensure_ascii=False, separators=(',', ':')) if compatto else json.dumps(j, ensure_ascii=False, indent=1))
    for k in sorted(conta, key=lambda x: -conta[x]):
        print('%-14s %d' % (k, conta[k]))
    print(len(scelte), 'gallerie')
    if scrivi:
        json.dump(scelte, open(SCELTE, 'w', encoding='utf-8'), ensure_ascii=False, indent=0)
    print('(scritto)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
