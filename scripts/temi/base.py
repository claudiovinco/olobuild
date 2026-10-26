# -*- coding: utf-8 -*-
"""Pezzi comuni dei generatori dei temi di OLObuild (assets/data/themes/<id>/).

Le tile partono dalle impostazioni complete di una tile vera di un tema già pubblicato (così l'editor trova ogni
campo e cambiano solo i valori del tema); le tile che nessun tema usa ancora portano solo le impostazioni scelte
(il renderer PHP completa coi suoi default). Solo controlli nativi: nessun custom_css.
"""
import copy
import json
import os
import sys

ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
THEMES = os.path.join(ROOT, 'assets', 'data', 'themes')
LIB = '{{OLOBUILD_LIBRARY}}/media/'

# Temi da cui prendere il campione di una tile, in ordine di preferenza (il primo che la usa).
PREFERITI = ['acqua-viva', 'baita-stua', 'cantina-vallerose', 'carico-libero', 'circo-pappagallo', 'casa-scogliera',
             'giroscopio', 'sorriso-clinic', 'argilla', 'giulia-marchi', 'cantiere-aperto', 'fenice-calcio',
             'conto-chiaro', 'nido-coworking', 'orto-vivo', 'denim-district', 'il-gufo', 'cromo', 'kemet',
             'linea-reno', 'marmi-lizza', 'moneta', 'onde-corte', 'inchiostro-riso', 'rewind-vintage']
SAMPLES = {}


def _collect():
    ordine = PREFERITI + [t for t in sorted(os.listdir(THEMES)) if t not in PREFERITI]
    for t in ordine:
        d = os.path.join(THEMES, t)
        if not os.path.isdir(d):
            continue
        for f in sorted(os.listdir(d)):
            if not f.endswith('.json') or f in ('theme.json', 'tiles-manifest.json'):
                continue
            with open(os.path.join(d, f), encoding='utf-8') as fh:
                data = json.load(fh)

            def walk(n):
                if n['type'] not in ('section', 'row', 'column') and n['type'] not in SAMPLES:
                    SAMPLES[n['type']] = n
                for c in n.get('children') or []:
                    walk(c)
            for n in data:
                walk(n)


_collect()


def rgba(hx, a):
    hx = hx.lstrip('#')
    return 'rgba(%d,%d,%d,%s)' % (int(hx[0:2], 16), int(hx[2:4], 16), int(hx[4:6], 16), a)


def mix(color, pct, other='transparent'):
    return 'color-mix(in srgb, %s %d%%, %s)' % (color, pct, other)


def tok(name):
    return 'var(--olo-color-%s)' % name


def radius(r):
    return {'tl': r, 'tr': r, 'br': r, 'bl': r}


def corners(tl, tr, br, bl):
    return {'tl': tl, 'tr': tr, 'br': br, 'bl': bl}


def solid(color):
    return {'type': 'solid', 'color': color}


def lib(name):
    return LIB + name


class Tpl:
    """Un template: contatore degli id e costruttori dei nodi."""

    def __init__(self, theme_id, key):
        self.theme_id = theme_id
        self.key = key
        self.n = 0

    def nid(self):
        self.n += 1
        return '%s-%s-%d' % (self.theme_id, self.key, self.n)

    def node(self, type_, settings, style=None, advanced=None, children=None):
        n = {'id': self.nid(), 'type': type_, 'settings': settings, 'style': style or {}, 'advanced': advanced or {}}
        if children is not None:
            n['children'] = children
        return n

    def section(self, children, bg=None, padding='default', width='default', html_id=None, style=None, **extra):
        s = {'width': width, 'padding': padding}
        if bg is not None:
            s['bg'] = bg
            s['bg_scope'] = 'section'
        s.update(extra)
        adv = {'html_id': html_id} if html_id else {}
        return self.node('section', s, style or {}, adv, children)

    def row(self, cols, layout='100', gap=None, valign=None, custom=None, style=None, anim='slide-up', stack=True, **extra):
        s = {'layout': layout, 'stack_mobile': stack}
        if gap is not None:
            s['gap'] = str(gap)
        if valign:
            s['vertical_align'] = valign
        if custom:
            s['custom_widths'] = custom
            s['layout'] = 'custom'
        if anim:
            s['entrance_animation'] = anim
            s['entrance_duration'] = 800
            s['entrance_easing'] = 'cubic-bezier(.22,1,.36,1)'
            s['entrance_stagger'] = True
            s['entrance_stagger_delay'] = 90
        s.update(extra)
        return self.node('row', s, style or {}, {}, cols)

    def col(self, children, wm=None, ws=None, style=None, **extra):
        s = {}
        if wm:
            s['width_medium'] = wm
        if ws:
            s['width_small'] = ws
        s.update(extra)
        return self.node('column', s, style or {}, {}, children)

    def one(self, tiles, **kw):
        """Una riga a una colonna."""
        return self.row([self.col(tiles if isinstance(tiles, list) else [tiles])], **kw)

    def classic(self, type_, overrides, style=None, advanced=None):
        """Tile dal campione completo di un tema pubblicato, coi valori del tema sopra."""
        base = SAMPLES[type_]
        s = copy.deepcopy(base['settings'])
        s.update(overrides)
        st = copy.deepcopy(base.get('style') or {}) if style is None else style
        return self.node(type_, s, st, advanced or {})

    def bare(self, type_, settings, style=None, advanced=None):
        """Tile che nessun tema usa ancora: solo le impostazioni scelte (il resto dai default del renderer)."""
        return self.node(type_, settings, style or {}, advanced or {})


def tiles_used(nodes, acc):
    for n in nodes:
        if n['type'] not in ('section', 'row', 'column'):
            acc.add(n['type'])
        tiles_used(n.get('children') or [], acc)
    return acc


def check(nodes, media, path=''):
    """Solo controlli nativi (niente custom_css) e solo file della libreria che esistono fra i media del tema."""
    txt = json.dumps(nodes, ensure_ascii=False)
    for n in nodes:
        assert 'custom_css' not in n.get('settings', {}) and 'custom_css' not in n.get('advanced', {}), n['id']
    import re
    for m in re.findall(r'\{\{OLOBUILD_LIBRARY\}\}/media/([^"\\?#]+)', txt):
        assert m in media, '%s: manca il file %s' % (path, m)


def write_theme(theme, builds, media):
    """theme: il dizionario di theme.json; builds: {file.json: nodi}; media: nomi dei file della libreria usati."""
    out = os.path.join(THEMES, theme['id'])
    os.makedirs(out, exist_ok=True)
    used = {}
    ids = set()
    for name, nodes in builds.items():
        check(nodes, media, name)

        def walk(n):
            assert n['id'] not in ids, 'id doppio ' + n['id']
            ids.add(n['id'])
            for c in n.get('children') or []:
                walk(c)
        for n in nodes:
            walk(n)
        with open(os.path.join(out, name), 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(json.dumps(nodes, ensure_ascii=False, separators=(',', ':')))
        used[name] = sorted(tiles_used(nodes, set()))
    with open(os.path.join(out, 'theme.json'), 'w', encoding='utf-8', newline='\n') as fh:
        fh.write(json.dumps(theme, ensure_ascii=False, separators=(',', ':')))
    sys.stdout.reconfigure(encoding='utf-8')
    tutti = sorted(set(t for v in used.values() for t in v))
    print(theme['id'], '·', len(builds), 'file ·', len(tutti), 'tipi di tile:', ', '.join(tutti))
    return used
