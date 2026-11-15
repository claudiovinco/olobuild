# -*- coding: utf-8 -*-
"""Home con la stessa sequenza di sezioni (13 nov 2026, dopo varia_home.py): i negozi WooCommerce del 4 novembre erano
identici fra loro, i temi di moda del 3 novembre quasi, e altri gruppetti. Qui ogni tema di un gruppo prende un ORDINE
diverso delle sezioni (l'apertura resta prima, l'ultima sezione resta ultima), scartando gli ordini che metterebbero
vicine due sezioni con lo stesso fondo pieno, foto o video.

I gruppi si trovano da soli: home con almeno l'80% della sequenza di tile in comune. Le scelte stanno in
ordine_scelte.json; base.write_theme le riapplica. Oltre all'ordine:
- la FASCIA video con la frase (riga a tre colonne): al centro, a sinistra o a destra, a turno;
- nei NEGOZI WooCommerce (dieci home identiche del 4 novembre) anche la forma delle categorie (tondi, ritratti, archi,
  panorami, fila stretta: il nome sempre sotto, mai sopra un velo), quante colonne e quanti prodotti nelle griglie,
  carosello o griglia per le offerte a tempo.
uso: python varia_ordine.py [--scrivi]"""
import json
import os
import random
import sys

sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, QUI)
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
SCELTE = os.path.join(QUI, 'ordine_scelte.json')
IGN = {'section', 'row', 'column', 'inner-columns', 'inner-column', 'headline', 'text-block', 'button', 'image', 'section-header',
       'spacer', 'divider', 'text', 'shape', 'shapedivider', 'none', 'icon'}


def firma(sez):
    tipi = []

    def w(n):
        if isinstance(n, dict):
            ty = n.get('type')
            if ty and ty not in IGN and ty not in tipi:
                tipi.append(ty)
            for c in n.get('children') or []:
                w(c)
    w(sez)
    return '+'.join(sorted(tipi)) or 'testo'


def fondo(sez):
    b = (sez.get('settings') or {}).get('bg')
    t = (b.get('type') or '-') if isinstance(b, dict) else '-'
    return t if t in ('solid', 'video', 'image', 'gradient') else '-'


def lcs(a, b):
    m = [[0] * (len(b) + 1) for _ in range(len(a) + 1)]
    for i in range(len(a)):
        for j in range(len(b)):
            m[i + 1][j + 1] = m[i][j] + 1 if a[i] == b[j] else max(m[i][j + 1], m[i + 1][j])
    return m[-1][-1]


def buono(ordine, fondi):
    seq = [fondi[i] for i in ordine]
    return all(not (a == b and a != '-') for a, b in zip(seq, seq[1:]))


def ordini_possibili(n, fondi, quanti, seme):
    """Permutazioni con la prima e l'ultima ferme, diverse fra loro, con i fondi alternati; poche e lontane dall'originale."""
    mezzo = list(range(1, n - 1))
    rng = random.Random(seme)
    trovati = [list(range(n))]
    prove = 0
    while len(trovati) < quanti and prove < 4000:
        prove += 1
        m = mezzo[:]
        # piccoli spostamenti: due o tre scambi fra vicini o a distanza due, così il racconto resta sensato
        for _ in range(rng.randint(2, 3)):
            i = rng.randrange(len(m) - 1)
            j = min(len(m) - 1, i + rng.choice([1, 2]))
            m[i], m[j] = m[j], m[i]
        o = [0] + m + [n - 1]
        if o not in trovati and buono(o, fondi):
            trovati.append(o)
    return trovati


FASCE = ['centro', 'lato', 'destra']
CATEGORIE = {
    'tondi': {'columns': 6, 'columns_tablet': 3, 'image_ratio': '1-1', 'border_radius': 999, 'scorri_mobile': True},
    'ritratti': {'columns': 6, 'columns_tablet': 3, 'image_ratio': '3-4', 'border_radius': 6, 'scorri_mobile': True},
    'archi': {'columns': 6, 'columns_tablet': 3, 'image_ratio': '2-3', 'scorri_mobile': True,
              'border_radius': {'tl': 400, 'tr': 400, 'br': 0, 'bl': 0}},
    'panorami': {'columns': 3, 'columns_tablet': 2, 'columns_mobile': 2, 'image_ratio': '16-9', 'border_radius': 4,
                 'scorri_mobile': False},
    'provino': {'columns': 6, 'columns_tablet': 3, 'image_ratio': '1-1', 'border_radius': 0, 'gap': 4, 'scorri_mobile': True},
}
FORME_CAT = ['tondi', 'ritratti', 'archi', 'panorami', 'provino']
GRIGLIE = [(8, 4), (6, 3), (10, 5), (4, 4)]           # la griglia grande: prodotti, colonne
ULTIME = [(4, 4, 'grid'), (3, 3, 'grid'), (6, 3, 'grid'), (8, 4, 'carousel')]
OFFERTE = [(9, 4, 'carousel'), (3, 3, 'grid'), (8, 3, 'carousel')]


def tile_di(n, tipo):
    out = []

    def w(x):
        if isinstance(x, dict):
            if x.get('type') == tipo:
                out.append(x)
            for c in x.get('children') or []:
                w(c)
    w(n)
    return out


def riga_fascia(sez):
    for r in sez.get('children') or []:
        cols = r.get('children') or []
        if r.get('type') == 'row' and len(cols) == 3 and not cols[0].get('children') and not cols[2].get('children'):
            return r
    return None


def fascia(sez, lato):
    """La fascia video con la frase: al centro (i pulsanti centrati come il testo) o di lato (varia_home.fascia_di_lato)."""
    r = riga_fascia(sez)
    if not r:
        return
    if lato == 'centro':
        for x in r['children'][1].get('children') or []:
            if x.get('type') == 'button' and (x.get('settings') or {}).get('alignment') == 'left':
                x['settings']['alignment'] = 'center'
        return
    import varia_home
    varia_home.fascia_di_lato(sez, lato)


def disponibili(st, prodotti):
    """Quanti prodotti del negozio la griglia può mostrare (categoria, saldi, in evidenza)."""
    if prodotti is None:
        return 99
    cat = {x for x in (st.get('category') or '').split(',') if x}
    return sum(1 for p in prodotti if (not cat or cat & set(p.get('cat') or []))
               and (not st.get('on_sale') or (p.get('sconto') or 0) > 0) and (not st.get('featured') or p.get('evidenza')))


def griglia(tile, n, cols, layout, prodotti=None):
    """Mai un prodotto solo sull'ultima riga né posti vuoti: con meno prodotti di quelli voluti, righe piene."""
    s = tile['settings']
    a = disponibili(s, prodotti)
    if a < n:
        if a <= cols:
            cols, n, layout = a, a, 'grid'
        elif layout == 'grid':
            n = (a // cols) * cols
        else:
            n = a
    s['posts_per_page'], s['columns'], s['layout'] = n, cols, layout
    s['columns_tablet'] = min(3, cols)


def colonne_per(n, voluto):
    """Mai una categoria sola sull'ultima riga: le colonne diventano il divisore del numero di categorie più vicino."""
    if not n or voluto >= n:
        return n or voluto
    if n % voluto == 0:
        return voluto
    return min((d for d in range(2, n + 1) if n % d == 0), key=lambda d: (abs(d - voluto), -d))


def negozio(c, k, tid=''):
    """Le varianti della home di un negozio, k = posto nel gruppo (0 = com'era)."""
    p = os.path.join(QUI, 'prodotti', tid + '.json')
    D = json.load(open(p, encoding='utf-8')) if os.path.exists(p) else {}
    n = len(D.get('categorie') or [])
    prodotti = D.get('prodotti')
    # la sezione delle categorie è quella dove le categorie stanno da sole (in alcuni negozi c'è anche un elenco
    # piccolo dentro la fascia delle offerte)
    sole = [s for s in c if firma(s) == 'woo_categories']
    for t in tile_di(sole[0], 'woo_categories') if sole else []:
        v = dict(CATEGORIE[FORME_CAT[k % len(FORME_CAT)]])
        v['columns'] = colonne_per(n, v['columns'])
        t['settings'].update(v)
        t['settings']['posizione_testo'] = 'sotto'
    # le griglie: quella col quickview (la grande), quella col conto alla rovescia (offerte), l'ultima da sola
    for s in c:
        tipi = {x.get('type') for x in tile_di(s, 'woo_quickview') + tile_di(s, 'countdown')}
        pp = [x for x in tile_di(s, 'woo_products') if (x['settings'].get('columns') or 0) > 1]
        if len(pp) != 1:
            continue
        if 'woo_quickview' in tipi:
            griglia(pp[0], *GRIGLIE[(k + k // 4) % len(GRIGLIE)], 'grid', prodotti=prodotti)
        elif 'countdown' in tipi:
            griglia(pp[0], *OFFERTE[(k * 2) % len(OFFERTE)], prodotti=prodotti)
        else:
            griglia(pp[0], *ULTIME[(k * 3) % len(ULTIME)], prodotti=prodotti)


def ritocca(c, sc):
    """Prima di riordinare (gli indici sono quelli del generatore)."""
    video = [s for s in c if fondo(s) == 'video' and riga_fascia(s)]
    for i, s in enumerate(video):
        fascia(s, FASCE[(FASCE.index(sc['f']) + i) % len(FASCE)])
    if sc.get('n') is not None:
        negozio(c, sc['n'], sc.get('t', ''))
    c[:] = [c[i] for i in sc['o']]


def home_di(tid):
    th = json.load(open(os.path.join(TEMI, tid, 'theme.json'), encoding='utf-8'))
    h = th['pages'].get('home') or {}
    p = os.path.join(TEMI, tid, th['templates'][h.get('template', 'home')]['file'])
    j = json.load(open(p, encoding='utf-8'))
    c = j['content'] if isinstance(j, dict) and 'content' in j else j
    return p, j, c


def applica_tema(theme, pagina, built, media=None):
    """Per base.write_theme."""
    if pagina != 'home':
        return
    sc = (json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}).get(theme['id'])
    c = built['content'] if isinstance(built, dict) and 'content' in built else built
    if sc and len(sc['o']) == len(c):
        ritocca(c, sc)


def main():
    scrivi = '--scrivi' in sys.argv
    date = json.load(open(os.path.join(TEMI, '..', 'temi-creati.json'), encoding='utf-8'))
    gia = set(json.load(open(os.path.join(QUI, 'home_scelte.json'), encoding='utf-8'))) if os.path.exists(os.path.join(QUI, 'home_scelte.json')) else set()
    temi = sorted(os.listdir(TEMI), key=lambda x: (date.get(x, ''), x))
    sk = {}
    for t in temi:
        if t in gia:
            continue
        _, _, c = home_di(t)
        sk[t] = [firma(s) for s in c]
    adj = {t: set() for t in sk}
    lista = list(sk)
    for i, a in enumerate(lista):
        for b in lista[i + 1:]:
            if lcs(sk[a], sk[b]) / max(len(sk[a]), len(sk[b])) >= 0.8:
                adj[a].add(b)
                adj[b].add(a)
    visti, gruppi = set(), []
    for t in lista:
        if t in visti or not adj[t]:
            continue
        st, g = [t], []
        while st:
            x = st.pop()
            if x in g:
                continue
            g.append(x)
            st += [y for y in adj[x] if y not in g]
        visti |= set(g)
        gruppi.append(sorted(g, key=lambda x: (date.get(x, ''), x)))
    scelte = json.load(open(SCELTE, encoding='utf-8')) if os.path.exists(SCELTE) else {}
    for g in gruppi:
        print('gruppo di %d: %s' % (len(g), ', '.join(g)))
        for k, t in enumerate(g):
            p, j, c = home_di(t)
            if t in scelte:
                continue
            fondi = [fondo(s) for s in c]
            ordini = ordini_possibili(len(c), fondi, len(g), seme=len(c) * 31 + len(g))
            o = ordini[k % len(ordini)]
            woo = any(tile_di(s, 'woo_categories') for s in c)
            sc = {'o': o, 'f': FASCE[k % len(FASCE)], 'n': k if woo else None, 't': t}
            scelte[t] = sc
            print('   %-16s %s %s %s' % (t, o, sc['f'], ('negozio %d: %s' % (k, FORME_CAT[k % len(FORME_CAT)])) if woo else ''))
            if scrivi:
                ritocca(c, sc)
                open(p, 'w', encoding='utf-8', newline='\n').write(json.dumps(j, ensure_ascii=False, separators=(',', ':')))
    if scrivi:
        json.dump(scelte, open(SCELTE, 'w', encoding='utf-8'), ensure_ascii=False, indent=0)
    print('(scritto)' if scrivi else '(prova)')


if __name__ == '__main__':
    main()
