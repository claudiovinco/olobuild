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
            if isinstance(data, dict):  # formato esteso {settings, content}
                data = data.get('content') or []

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
        # Regola del 31 ott 2026 (mai media velati): un velo scuro piatto su una foto o un video non va oltre il 50%.
        if isinstance(bg, dict) and bg.get('type') in ('image', 'video') and not bg.get('overlay_fade')                 and bg.get('overlay_color', 'var(--olo-color-dark)') in ('var(--olo-color-dark)', '#000000', '#000')                 and int(bg.get('overlay_opacity') or 0) > 50:
            bg = dict(bg, overlay_opacity=50)
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


def page(content, **settings):
    """Un template nel formato esteso {settings, content}: le impostazioni di pagina viaggiano col file
    (effetti di pagina come l'apertura page_intro_*, la grana page_grain_*)."""
    return {'settings': settings, 'content': content}


SCURI = ('var(--olo-color-dark)', '#000000', '#000')


def niente_velati(o, chiave=''):
    """Regola del 31 ott 2026, mai media velati: un velo scuro piatto oltre il 50% su una foto o un video diventa una
    sfumatura dal basso sulle facce delle carte (il titolo sta in fondo) e scende al 50% altrove."""
    if isinstance(o, dict):
        if o.get('type') in ('image', 'video') and (o.get('image_url') or o.get('video_url')) and not o.get('overlay_fade')                 and o.get('overlay_color', SCURI[0]) in SCURI and int(o.get('overlay_opacity') or 0) > 50:
            if chiave in ('front_media', 'back_media'):
                o.update(overlay_opacity=88, overlay_fade='bottom', overlay_fade_reach=80)
            else:
                o['overlay_opacity'] = 50
        for k, v in o.items():
            niente_velati(v, k)
    elif isinstance(o, list):
        for v in o:
            niente_velati(v, chiave)


def nodo_timeline(o):
    """Timeline col nodo «Anno»: la data è già scritta di lato, nel cerchio era un doppione (e una data lunga non ci
    stava). Il nodo prende l'icona della tappa, o un pallino se le tappe non hanno icona (utente, 31 ott 2026)."""
    if isinstance(o, dict):
        if o.get('type') == 'timeline' and (o.get('settings') or {}).get('tl_node') == 'year':
            voci = o['settings'].get('items') or []
            o['settings']['tl_node'] = 'icon' if voci and all(v.get('icon') for v in voci) else 'dot'
        for v in o.values():
            nodo_timeline(v)
    elif isinstance(o, list):
        for v in o:
            nodo_timeline(v)


RINFORZI = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'rinforzi.json')


def rinforza(tema, o, r):
    """Le correzioni misurate (scripts/temi/rinforzi.json, scritte dopo media_contrasti.py): le colonne «schede» diventano
    un riquadro scuro (testo chiaro sopra un media), le «chiare» un riquadro chiaro (testo scuro), e le sezioni «leggeri»
    tengono il media quasi pieno (velo al 15%)."""
    if isinstance(o, dict):
        nid = o.get('id')
        if nid in r.get('schede', []) or nid in r.get('chiare', []):
            st = o.setdefault('style', {})
            st.update({'padding_top': 40, 'padding_right': 44, 'padding_bottom': 36, 'padding_left': 44,
                       'border_radius': {'tl': 6, 'tr': 6, 'br': 6, 'bl': 6}})
            colore = 'var(--olo-color-background)' if nid in r.get('chiare', []) else 'var(--olo-color-dark)'
            st['bg'] = {'type': 'solid', 'color': 'color-mix(in srgb, %s 86%%, transparent)' % colore}
        if nid in r.get('leggeri', []) and isinstance((o.get('settings') or {}).get('bg'), dict):
            bg = o['settings']['bg']
            bg.pop('overlay_fade', None)
            bg.pop('overlay_fade_reach', None)
            bg.update(overlay_color='var(--olo-color-dark)', overlay_opacity=15)
        for v in o.values():
            rinforza(tema, v, r)
    elif isinstance(o, list):
        for v in o:
            rinforza(tema, v, r)


def write_theme(theme, builds, media):
    """theme: il dizionario di theme.json; builds: {file.json: nodi | page(nodi, …)}; media: nomi dei file della libreria usati."""
    out = os.path.join(THEMES, theme['id'])
    r = json.load(open(RINFORZI, encoding='utf-8')).get(theme['id'], {}) if os.path.exists(RINFORZI) else {}
    for built in builds.values():
        niente_velati(built)
        nodo_timeline(built)
        if r:
            rinforza(theme['id'], built, r)
    os.makedirs(out, exist_ok=True)
    used = {}
    ids = set()
    for name, built in builds.items():
        nodes = built['content'] if isinstance(built, dict) else built
        check(nodes, media, name)

        def walk(n):
            assert n['id'] not in ids, 'id doppio ' + n['id']
            ids.add(n['id'])
            for c in n.get('children') or []:
                walk(c)
        for n in nodes:
            walk(n)
        with open(os.path.join(out, name), 'w', encoding='utf-8', newline='\n') as fh:
            fh.write(json.dumps(built, ensure_ascii=False, separators=(',', ':')))
        used[name] = sorted(tiles_used(nodes, set()))
    with open(os.path.join(out, 'theme.json'), 'w', encoding='utf-8', newline='\n') as fh:
        fh.write(json.dumps(theme, ensure_ascii=False, separators=(',', ':')))
    sys.stdout.reconfigure(encoding='utf-8')
    tutti = sorted(set(t for v in used.values() for t in v))
    print(theme['id'], '·', len(builds), 'file ·', len(tutti), 'tipi di tile:', ', '.join(tutti))
    return used
