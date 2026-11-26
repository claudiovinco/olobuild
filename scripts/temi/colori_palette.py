# -*- coding: utf-8 -*-
"""
I colori dei Pro Slider dei temi presi dalla palette (24 nov 2026). Utente: «nessun CSS o cose hardcoded».

Ogni colore fisso di un livello (#hex, rgb, rgba, anche dentro color-mix) diventa un colore della palette del tema:
- uguale (o quasi, distanza RGB < 12) a un ruolo del tema → var(--olo-color-<ruolo>);
- nero per le ombre → il ruolo «dark», bianco → «light», se il tema li ha quasi neri e quasi bianchi;
- altrimenti un COLORE EXTRA del tema, con un nome («Argento», «Ruggine»), scritto in theme.json → `colori_extra`
  e importato con il tema fra i «Colori globali extra» della Palette: il cliente lo ritrova e lo cambia da lì.
La trasparenza resta: color-mix(in srgb, var(--olo-color-x) 35%, transparent).

    python scripts/temi/colori_palette.py            → prova (conta e basta)
    python scripts/temi/colori_palette.py scrivi     → scrive i file
"""
import io, json, os, re, sys
sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
SCRIVI = len(sys.argv) > 1 and sys.argv[1] == 'scrivi'

PREFERENZA = ['primary', 'secondary', 'accent', 'light', 'dark', 'text', 'background', 'muted', 'border', 'text_muted',
              'link', 'primary_contrast', 'secondary_contrast', 'muted_contrast']
NOMI = [  # nome italiano → tinta di riferimento, per dare un nome ai colori extra
    ('Nero', '#000000'), ('Ebano', '#2A1A0E'), ('Antracite', '#2B2B2B'), ('Grafite', '#3A3D42'), ('Ardesia', '#4A4E55'),
    ('Fumo', '#8A837A'), ('Grigio', '#808080'), ('Cemento', '#9C9890'), ('Tortora', '#ABA79F'), ('Grigio perla', '#B9BEC5'),
    ('Argento', '#C9CCD1'), ('Nebbia', '#D2D6DA'), ('Ghiaccio', '#ECEFF2'), ('Bianco', '#FFFFFF'), ('Panna', '#F6F1EA'),
    ('Avorio', '#FFF8E7'), ('Crema', '#F6E3A1'), ('Sabbia', '#E8D2A6'), ('Beige', '#D4B68E'), ('Cammello', '#B08D57'),
    ('Cuoio', '#8E6E2A'), ('Caffè', '#5A3A1C'), ('Marrone', '#6B3A1F'), ('Cioccolato', '#3E2C0C'), ('Ocra', '#D9B45A'),
    ('Oro', '#E3C96A'), ('Ambra', '#F2B544'), ('Giallo', '#FFD84A'), ('Giallo lime', '#D7FF3A'), ('Albicocca', '#F6C77A'),
    ('Arancio', '#E8762D'), ('Mandarino', '#FF8A57'), ('Pesca', '#F0A27C'), ('Salmone', '#E8907F'), ('Corallo', '#FF5436'),
    ('Rosso', '#D2302C'), ('Vermiglio', '#FF4D2E'), ('Bordeaux', '#8E1B2E'), ('Vinaccia', '#561020'), ('Prugna', '#3B1F35'),
    ('Viola', '#6A2C91'), ('Lilla', '#C8A2C8'), ('Rosa antico', '#C04A6A'), ('Rosa', '#E07BA5'), ('Cipria', '#F0B8A8'),
    ('Fucsia', '#FF4FB3'), ('Magenta', '#FF2E98'), ('Blu', '#1E4FC2'), ('Blu notte', '#3A4064'), ('Petrolio', '#2B4560'),
    ('Carta da zucchero', '#4C8FB0'), ('Azzurro', '#9FD8FF'), ('Grigio azzurro', '#C9D7EA'), ('Polvere', '#9FB6CC'),
    ('Celeste', '#9EE3E6'), ('Ciano', '#00B7F0'), ('Acquamarina', '#2CE5C0'), ('Smeraldo', '#2E8B5E'), ('Verde', '#0E7A3E'),
    ('Verde prato', '#5E9E3A'), ('Verde salvia', '#8FB596'), ('Pistacchio', '#D9E7B8'), ('Verde bosco', '#2F3B2F'),
    ('Terracotta', '#A94F25'), ('Ruggine', '#8A3B1E'), ('Rame', '#B88A4E'), ('Bronzo', '#8F6A39'), ('Mattone', '#7A1F2B'),
]
HEX = re.compile(r'#(?:[0-9a-fA-F]{8}|[0-9a-fA-F]{6}|[0-9a-fA-F]{3})(?![0-9a-zA-Z])')
RGB = re.compile(r'rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*([\d.]+%?)\s*)?\)')
# dove stanno i colori in un livello, in una slide e nelle impostazioni dello slider
CHIAVE_COLORE = re.compile(r'(colou?r|colore|^bgColor$|Color$|^from$|^to$|^segno$)', re.I)
SALTA = {'content', 'name', 'id', 'label', 'tabLabel', 'iconName', 'imageSrc', 'videoSrc', 'shapeImage', 'poster',
         'buttonUrl', 'url', 'customCSS', 'customClass', 'customId', 'audioSrc'}
# impostazioni comuni della tile (contenitore, sfondo della tile, tipografia): non sono il Pro Slider
SALTA_SET = {'border', 'border_hover', 'border_effect_color2', 'bg', 'typography_preset', 'slides', 'globalLayers'}


def norm(h):
    h = h.lstrip('#').upper()
    if len(h) == 3: h = ''.join(c * 2 for c in h)
    if len(h) == 4: h = ''.join(c * 2 for c in h)
    return '#' + h


def rgb(h): return tuple(int(h[i:i + 2], 16) for i in (1, 3, 5))
def dist(a, b): return sum((x - y) ** 2 for x, y in zip(rgb(a), rgb(b))) ** .5
def slug(s): return re.sub(r'[^a-z0-9]+', '-', s.lower().replace('à', 'a').replace('è', 'e').replace('é', 'e').replace('ò', 'o').replace('ù', 'u').replace('ì', 'i')).strip('-')


class Tema:
    def __init__(self, cartella, theme):
        self.slug = os.path.basename(cartella)
        cols = (theme.get('styles') or {}).get('colors') or {}
        self.ruoli = {k: norm(v) for k, v in cols.items() if isinstance(v, str) and re.fullmatch(r'#[0-9a-fA-F]{3,8}', v.strip())}
        self.extra = {norm(c['value']): c for c in theme.get('colori_extra') or [] if isinstance(c, dict) and c.get('value')}
        self.nuovi = 0
        self.uso = {'ruolo': 0, 'quasi': 0, 'nero-bianco': 0, 'extra': 0}

    def token(self, h):
        """Il token della palette per un #RRGGBB."""
        esatti = [r for r in PREFERENZA if self.ruoli.get(r) == h] + [r for r, v in self.ruoli.items() if v == h and r not in PREFERENZA]
        if esatti:
            self.uso['ruolo'] += 1
            return 'var(--olo-color-%s)' % esatti[0].replace('_', '-')
        vicini = sorted((dist(h, v), PREFERENZA.index(r) if r in PREFERENZA else 99, r) for r, v in self.ruoli.items())
        if vicini and vicini[0][0] < 12:
            self.uso['quasi'] += 1
            return 'var(--olo-color-%s)' % vicini[0][2].replace('_', '-')
        if h == '#000000' and 'dark' in self.ruoli and dist(self.ruoli['dark'], h) <= 60:
            self.uso['nero-bianco'] += 1
            return 'var(--olo-color-dark)'
        if h == '#FFFFFF' and 'light' in self.ruoli and dist(self.ruoli['light'], h) <= 40:
            self.uso['nero-bianco'] += 1
            return 'var(--olo-color-light)'
        self.uso['extra'] += 1
        if h not in self.extra:
            nome = min(NOMI, key=lambda n: dist(n[1], h))[0]
            base = '%s-%s' % (self.slug, slug(nome))
            ids = {c['id'] for c in self.extra.values()}
            cid, i = base, 2
            while cid in ids:
                cid, i = '%s-%d' % (base, i), i + 1
            etichetta = nome if cid == base else '%s %d' % (nome, i - 1)
            self.extra[h] = {'id': cid, 'label': '%s · %s' % (TITOLI.get(self.slug, self.slug.title()), etichetta), 'value': h}
            self.nuovi += 1
        # col colore di riserva: dove l'extra non è stato importato (una scena copiata a mano) il colore resta
        return 'var(--olo-color-%s, %s)' % (self.extra[h]['id'], h)

    def con_alfa(self, h, alfa):
        t = self.token(h)
        if alfa is None or alfa >= 0.999:
            return t
        pct = round(alfa * 100, 1)
        pct = int(pct) if pct == int(pct) else pct
        return 'color-mix(in srgb, %s %s%%, transparent)' % (t, pct)

    def converti(self, valore):
        """Una stringa con dentro colori fissi → la stessa coi colori della palette."""
        def da_rgb(m):
            r, g, b = (max(0, min(255, int(x))) for x in m.group(1, 2, 3))
            a = m.group(4)
            alfa = None if a is None else (float(a[:-1]) / 100 if a.endswith('%') else float(a))
            return self.con_alfa('#%02X%02X%02X' % (r, g, b), alfa)

        def da_hex(m):
            h = norm(m.group(0))
            alfa = int(h[7:9], 16) / 255 if len(h) == 9 else None
            return self.con_alfa(h[:7], alfa)
        # il colore di riserva di un token già fatto (var(--olo-color-x, #C9CCD1)) non si tocca: rilanciare non fa danni
        riserve = []
        def metti_da_parte(m):
            riserve.append(m.group(0)); return '\x00%d\x00' % (len(riserve) - 1)
        RISERVA = r'var\(--olo-color-[a-z0-9_-]+,\s*[^()]*?\)'
        out = re.sub(RISERVA, metti_da_parte, valore)
        out = RGB.sub(da_rgb, out)
        out = re.sub(RISERVA, metti_da_parte, out)  # anche le riserve appena scritte da un rgba()
        out = HEX.sub(da_hex, out)
        out = re.sub(r'\x00(\d+)\x00', lambda m: riserve[int(m.group(1))], out)
        # color-mix(in srgb, color-mix(in srgb, X a%, transparent) b%, transparent) → una sola trasparenza
        def piatto(m):
            a, b = float(m.group(2)), float(m.group(3))
            p = round(a * b / 100, 1)
            p = int(p) if p == int(p) else p
            return 'color-mix(in srgb, %s %s%%, transparent)' % (m.group(1), p)
        out = re.sub(r'color-mix\(in srgb, color-mix\(in srgb, (var\(--olo-color-[a-z0-9_-]+\)) ([\d.]+)%, transparent\) ([\d.]+)%, transparent\)', piatto, out)
        return out


TITOLI = {}


def visita(o, tema, dentro_set=False, chiave=''):
    """Converte in place; restituisce quanti valori ha cambiato."""
    n = 0
    if isinstance(o, list):
        for i, x in enumerate(o):
            if isinstance(x, str):
                if CHIAVE_COLORE.search(chiave):
                    nv = tema.converti(x)
                    if nv != x: o[i] = nv; n += 1
            else:
                n += visita(x, tema, False, chiave)
        return n
    if not isinstance(o, dict):
        return 0
    for k, v in list(o.items()):
        if k in SALTA or (dentro_set and k in SALTA_SET):
            continue
        if isinstance(v, str):
            if CHIAVE_COLORE.search(k) and (HEX.search(v) or RGB.search(v)):
                nv = tema.converti(v)
                if nv != v: o[k] = nv; n += 1
        elif isinstance(v, (dict, list)):
            n += visita(v, tema, False, k)
    return n


def tile_slider(o, acc):
    if isinstance(o, list):
        for x in o: tile_slider(x, acc)
    elif isinstance(o, dict):
        if o.get('type') == 'proslider' and isinstance(o.get('settings'), dict):
            acc.append(o['settings']); return
        for v in o.values(): tile_slider(v, acc)


def main():
    tot = {'ruolo': 0, 'quasi': 0, 'nero-bianco': 0, 'extra': 0}
    file_cambiati = temi_cambiati = extra_nuovi = 0
    for nome in sorted(os.listdir(TEMI)):
        dp = os.path.join(TEMI, nome)
        tp = os.path.join(dp, 'theme.json')
        if not os.path.isfile(tp): continue
        traw = io.open(tp, encoding='utf-8').read()
        theme = json.loads(traw)
        TITOLI[nome] = theme.get('name') or nome.title()
        tema = Tema(dp, theme)
        cambiati = []
        for f in sorted(os.listdir(dp)):
            if not f.endswith('.json') or f == 'theme.json': continue
            p = os.path.join(dp, f)
            raw = io.open(p, encoding='utf-8').read()
            try: d = json.loads(raw)
            except Exception: continue
            if json.dumps(d, ensure_ascii=False, separators=(',', ':')) != raw.rstrip('\n'):
                print('  (salto, non si rilegge identico)', nome, f); continue
            tiles = []; tile_slider(d, tiles)
            n = 0
            for s in tiles:
                for k, v in s.items():
                    if k in SALTA_SET: continue
                    if isinstance(v, str) and CHIAVE_COLORE.search(k) and (HEX.search(v) or RGB.search(v)):
                        nv = tema.converti(v)
                        if nv != v: s[k] = nv; n += 1
                    elif isinstance(v, (dict, list)):
                        n += visita(v, tema, False, k)
                for sl in s.get('slides') or []:
                    for k, v in sl.items():
                        if k == 'layers': n += visita(v, tema, False, 'layers')
                        elif isinstance(v, (dict, list)): n += visita(v, tema, False, k)
                        elif isinstance(v, str) and CHIAVE_COLORE.search(k) and (HEX.search(v) or RGB.search(v)):
                            nv = tema.converti(v)
                            if nv != v: sl[k] = nv; n += 1
                n += visita(s.get('globalLayers') or [], tema, False, 'globalLayers')
            if n:
                cambiati.append((p, d, n))
        if not cambiati and not tema.nuovi: continue
        for k in tot: tot[k] += tema.uso[k]
        print('%-16s %3d valori · ruoli %d, quasi %d, nero/bianco %d, extra %d (colori extra nuovi: %d)' % (
            nome, sum(c[2] for c in cambiati), tema.uso['ruolo'], tema.uso['quasi'], tema.uso['nero-bianco'], tema.uso['extra'], tema.nuovi))
        temi_cambiati += 1; file_cambiati += len(cambiati); extra_nuovi += tema.nuovi
        if SCRIVI:
            for p, d, _ in cambiati:
                io.open(p, 'w', encoding='utf-8', newline='').write(json.dumps(d, ensure_ascii=False, separators=(',', ':')))
            if tema.nuovi:
                theme['colori_extra'] = sorted(tema.extra.values(), key=lambda c: c['id'])
                if json.dumps(json.loads(traw), ensure_ascii=False, separators=(',', ':')) == traw.rstrip('\n'):
                    io.open(tp, 'w', encoding='utf-8', newline='').write(json.dumps(theme, ensure_ascii=False, separators=(',', ':')))
                else:
                    print('  ⚠ theme.json non si rilegge identico:', nome)
    print('\nusi:', tot, '· temi', temi_cambiati, '· file', file_cambiati, '· colori extra nuovi', extra_nuovi, '· scritto' if SCRIVI else '· PROVA')


if __name__ == '__main__':
    main()
