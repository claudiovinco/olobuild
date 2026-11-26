# -*- coding: utf-8 -*-
"""
I disegni SVG dei Pro Slider dei temi coi colori della palette (1.4.1433, comando «Colori del disegno»).

Per ogni livello immagine con un disegno della libreria ({{OLOBUILD_LIBRARY}}/media/*.svg) i colori del file uguali (o
quasi, distanza RGB < 12) a un colore della palette del tema — ruoli e colori extra (theme.json → colori_extra) — vanno in
`coloriDisegno` come token: sul tema il disegno resta identico, e cambiando la palette cambia anche lui. Gli altri colori
restano quelli del file, e si cambiano dal pannello.

    python scripts/temi/colori_disegni.py            → prova
    python scripts/temi/colori_disegni.py scrivi     → scrive i file
I disegni si scaricano una volta in %TEMP%/olo-disegni.
"""
import io, json, os, re, sys, tempfile, urllib.request
sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
LIB = 'https://olotheme.com/olobuild-library/media/'
CACHE = os.path.join(tempfile.gettempdir(), 'olo-disegni')
SCRIVI = len(sys.argv) > 1 and sys.argv[1] == 'scrivi'
HEX = re.compile(r'#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})(?![0-9a-zA-Z])')
PREFERENZA = ['primary', 'secondary', 'accent', 'light', 'dark', 'text', 'background', 'muted', 'border', 'text_muted',
              'link', 'primary_contrast', 'secondary_contrast', 'muted_contrast']


def norm(h):
    h = h.lstrip('#').upper()
    return '#' + (''.join(c * 2 for c in h) if len(h) == 3 else h[:6])


def dist(a, b): return sum((int(a[i:i + 2], 16) - int(b[i:i + 2], 16)) ** 2 for i in (1, 3, 5)) ** .5


def svg(nome):
    os.makedirs(CACHE, exist_ok=True)
    p = os.path.join(CACHE, nome)
    if not os.path.exists(p):
        try:
            with urllib.request.urlopen(LIB + nome, timeout=20) as r:
                io.open(p, 'wb').write(r.read())
        except Exception:
            io.open(p, 'wb').write(b'')
    return io.open(p, encoding='utf-8', errors='ignore').read()


def colori(testo):
    out = {}
    for m in HEX.finditer(testo):
        out[norm(m.group(0))] = out.get(norm(m.group(0)), 0) + 1
    return out


def main():
    tot_liv = tot_col = file_c = 0
    for nome in sorted(os.listdir(TEMI)):
        dp = os.path.join(TEMI, nome)
        tp = os.path.join(dp, 'theme.json')
        if not os.path.isfile(tp): continue
        th = json.load(io.open(tp, encoding='utf-8'))
        pal = [(r, norm(v)) for r, v in ((th.get('styles') or {}).get('colors') or {}).items() if isinstance(v, str) and re.fullmatch(r'#[0-9a-fA-F]{3,8}', v)]
        pal.sort(key=lambda x: PREFERENZA.index(x[0]) if x[0] in PREFERENZA else 99)
        ruoli = [('var(--olo-color-%s)' % r.replace('_', '-'), v) for r, v in pal]
        extra = [('var(--olo-color-%s, %s)' % (c['id'], norm(c['value'])), norm(c['value'])) for c in th.get('colori_extra') or [] if isinstance(c, dict)]
        candidati = ruoli + extra
        for f in sorted(os.listdir(dp)):
            if not f.endswith('.json') or f == 'theme.json': continue
            p = os.path.join(dp, f)
            raw = io.open(p, encoding='utf-8').read()
            if 'OLOBUILD_LIBRARY}}/media/' not in raw or '.svg' not in raw: continue
            d = json.loads(raw)
            if json.dumps(d, ensure_ascii=False, separators=(',', ':')) != raw.rstrip('\n'):
                print('  (salto, non si rilegge identico)', nome, f); continue
            n = 0
            def visita(o, in_slider=False):
                nonlocal n
                if isinstance(o, list):
                    for x in o: visita(x, in_slider)
                elif isinstance(o, dict):
                    if o.get('type') == 'proslider' and isinstance(o.get('settings'), dict):
                        visita(o['settings'], True); return
                    src = o.get('imageSrc') if in_slider and o.get('type') == 'image' else None
                    m = re.search(r'\{\{OLOBUILD_LIBRARY\}\}/media/([a-z0-9._-]+\.svg)$', src or '', re.I)
                    if m:
                        mappa = dict(o.get('coloriDisegno') or {})
                        for c in colori(svg(m.group(1))):
                            if c in mappa: continue
                            vicini = sorted(((dist(c, v), i, tok) for i, (tok, v) in enumerate(candidati)))
                            if vicini and vicini[0][0] < 12:
                                mappa[c] = vicini[0][2]
                        if mappa != (o.get('coloriDisegno') or {}):
                            n += len(mappa) - len(o.get('coloriDisegno') or {})
                            o['coloriDisegno'] = mappa
                            nonlocal_liv[0] += 1
                    for v in o.values(): visita(v, in_slider)
            nonlocal_liv = [0]
            visita(d)
            if n:
                print('%-16s %-14s livelli %2d · colori collegati %3d' % (nome, f, nonlocal_liv[0], n))
                tot_liv += nonlocal_liv[0]; tot_col += n; file_c += 1
                if SCRIVI:
                    io.open(p, 'w', encoding='utf-8', newline='').write(json.dumps(d, ensure_ascii=False, separators=(',', ':')))
    print('\nlivelli', tot_liv, '· colori collegati', tot_col, '· file', file_c, '· scritto' if SCRIVI else '· PROVA')


if __name__ == '__main__':
    main()
