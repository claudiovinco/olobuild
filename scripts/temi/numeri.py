# -*- coding: utf-8 -*-
"""Le strisce di numeri delle home dei temi (statstrip, counter), decise in un posto solo.

Utente, 2 nov 2026: «le tile dei numeri sono ovunque, in ogni tema, sempre molto simili e sempre all'inizio a tutta
larghezza… puoi anche non inserirle sempre, ma ogni tanto, dove sono indispensabili e, comunque, anche in modo più
discreto». Quindi: dove i numeri sono colore e non servono si tolgono; dove servono (industria, studi, cliniche,
negozi, associazioni…) cambiano forma (la tile statstrip ha banda, riga, elenco, schede, evidenza) e posto.

NUMERI[tema] = 'togli'            → via dalla home (la tile; la colonna, la riga e la sezione se restano vuote)
             | (forma, dove)       → per ogni striscia della home:
                 forma: banda · riga · elenco · schede · evidenza
                 dove:  'qui'   resta dov'è
                        'testo' entra nella colonna del testo della stessa sezione, sotto il paragrafo (la riga che la
                                portava da sola sparisce); se la sezione non ha un testo resta dov'è
                        'giu'   la sua sezione, se contiene solo i numeri e sta fra le prime tre, scende prima delle
                                ultime due
             | [azione, azione…]   una per striscia, nell'ordine della pagina
I contatori (counter) si toccano solo con 'togli': gli altri sono numeri «dal vivo» che fanno parte dell'idea del tema.
Solo la home: nelle altre pagine (casi, misure, lavori) i numeri sono il contenuto. applica() si può ripassare: la
seconda volta non cambia niente. La usano patch_numeri.py (sui JSON) e base.write_theme (rigenerando un tema).
"""

TOGLI = 'togli'

NUMERI = {
    # ── tolti: numeri di colore, non indispensabili ──
    'aerostato': TOGLI, 'arnia': TOGLI, 'bagnasciuga': TOGLI, 'bolina': TOGLI, 'cantina-vallerose': TOGLI,
    'capitonne': TOGLI, 'cementina': TOGLI, 'ciak': TOGLI, 'clorofilla': TOGLI, 'concia': TOGLI, 'foglia-lenta': TOGLI,
    'fuorigiri': TOGLI, 'osteria-fumo': TOGLI, 'orto-vivo': TOGLI, 'pista-nera': TOGLI, 'radice': TOGLI,
    'sartoria-ferro': TOGLI, 'sincope': TOGLI, 'soglia': TOGLI, 'staffa': TOGLI, 'tavola-errante': TOGLI,
    'telaio': TOGLI, 'tepidario': TOGLI, 'tostatura-nove': TOGLI,
    # ── industria: dati tecnici, servono; le bande sole in cima scendono ──
    'fonderia-mella': ('schede', 'giu'), 'lamiere-astico': ('evidenza', 'giu'), 'linea-reno': ('banda', 'giu'),
    'marmi-lizza': ('schede', 'giu'), 'officine-quota': ('evidenza', 'giu'), 'pastificio-sorgiva': ('banda', 'giu'),
    'tessiture-navetta': [TOGLI, ('elenco', 'qui')],
    'varco-nove': ('riga', 'testo'), 'settimo-asse': ('elenco', 'testo'), 'meriggio-energia': ('riga', 'testo'),
    # ── studi, salute, immobiliare, associazioni: fiducia, dentro l'introduzione ──
    'insegna': ('riga', 'testo'), 'leva-consulenza': ('elenco', 'testo'), 'nido-coworking': ('schede', 'qui'),
    'sorriso-clinic': ('evidenza', 'qui'), 'vertebra': ('riga', 'testo'), 'respiro': ('elenco', 'testo'),
    'chiavistello': ('riga', 'testo'), 'cuccia': ('elenco', 'testo'),
    # ── tecnologia e creativi ──
    'moneta': ('schede', 'qui'), 'synthetica': ('evidenza', 'qui'), 'strato': ('riga', 'testo'), 'giroscopio': ('schede', 'qui'),
    # ── casa ──
    'cantiere-aperto': ('evidenza', 'giu'), 'studio-assi': ('banda', 'qui'),
    # ── negozi: già in fondo, forme diverse ──
    'crocevia': ('riga', 'qui'), 'fulmine': ('schede', 'qui'), 'orlo': ('riga', 'qui'), 'siero': ('riga', 'qui'),
    'davanzale': ('riga', 'qui'), 'pagine-sparse': ('evidenza', 'qui'), 'scheda-madre': ('schede', 'qui'),
    # ── sport, ospitalità, cultura ed eventi ──
    'fenice-calcio': ('banda', 'qui'), 'carico-libero': ('riga', 'testo'),
    'baita-stua': ('elenco', 'testo'), 'rifugio-larice': ('elenco', 'testo'),
    'teatro-seta': ('evidenza', 'qui'), 'kemet': ('riga', 'testo'), 'parla-mondo': ('riga', 'qui'),
    'doppia-mandata': ('elenco', 'testo'),
}

TIPI = ('statstrip', 'counter')

# Le misure di ogni forma: i colori e i font restano quelli del tema.
FORME = {
    'banda':    {},
    'riga':     {'value_size': 24, 'band_border': False, 'show_dividers': False,
                 'band_padding': {'top': 22, 'right': 0, 'bottom': 6, 'left': 0}},
    'elenco':   {'value_size': 30, 'band_border': False, 'show_dividers': True,
                 'band_padding': {'top': 14, 'right': 0, 'bottom': 0, 'left': 0}},
    'schede':   {'value_size': 38, 'band_border': False, 'show_dividers': False,
                 'band_padding': {'top': 8, 'right': 0, 'bottom': 8, 'left': 0}},
    'evidenza': {'value_size': 60, 'band_border': False, 'show_dividers': False,
                 'band_padding': {'top': 12, 'right': 0, 'bottom': 12, 'left': 0}},
}


def _figli(n):
    return n.get('children') or []


def _strisce(sezioni):
    """[(sezione, riga, colonna, tile)] nell'ordine della pagina; riga/colonna None se la tile sta altrove."""
    out = []
    for sez in sezioni:
        def cerca(n, riga, col):
            t = n.get('type')
            if t in TIPI:
                out.append((sez, riga, col, n))
                return
            for c in _figli(n):
                cerca(c, n if t == 'row' else riga, n if t == 'column' else col)
        cerca(sez, None, None)
    return out


def _solo_numeri(n):
    """La sezione (o la riga) contiene soltanto strisce di numeri."""
    tile = []

    def w(x):
        if x.get('type') not in ('section', 'row', 'column', 'inner-columns'):
            tile.append(x.get('type'))
        for c in _figli(x):
            w(c)
    w(n)
    return bool(tile) and all(t in TIPI for t in tile)


def _togli(sezioni, sez, riga, col, tile):
    if col is not None and tile in _figli(col):
        col['children'].remove(tile)
        if not col['children'] and riga is not None:
            riga['children'].remove(col)
            if not riga['children'] and riga in _figli(sez):
                sez['children'].remove(riga)
    if not _figli(sez) and sez in sezioni:
        sezioni.remove(sez)


def _nel_testo(sez, riga, col, tile):
    """La striscia entra nella colonna del testo della sezione, dopo l'ultimo paragrafo. False se non c'è."""
    if riga is None or col is None or len(_figli(riga)) != 1 or _figli(col) != [tile]:
        return False
    for r in _figli(sez):
        if r is riga:
            continue
        for c in _figli(r):
            tipi = [x.get('type') for x in _figli(c)]
            if 'text-block' in tipi:
                ultimo = max(i for i, x in enumerate(tipi) if x == 'text-block')
                c['children'].insert(ultimo + 1, tile)
                sez['children'].remove(riga)
                return True
    return False


def applica(tid, sezioni):
    """Applica la decisione del tema alla lista di sezioni della home (modificata sul posto). Ritorna quante strisce."""
    azione = NUMERI.get(tid)
    if azione is None or not isinstance(sezioni, list):
        return 0
    trovate = _strisce(sezioni)
    azioni = azione if isinstance(azione, list) else [azione] * len(trovate)
    n = 0
    for (sez, riga, col, tile), a in zip(trovate, azioni):
        if a == TOGLI:
            _togli(sezioni, sez, riga, col, tile)
            n += 1
            continue
        if tile.get('type') != 'statstrip':
            continue
        forma, dove = a
        st = tile.setdefault('settings', {})
        st.update(FORME[forma])
        st['forma'] = forma
        if forma == 'schede':
            st['columns'] = min(4, max(2, len(st.get('items') or []))) if len(st.get('items') or []) != 3 else 3
        if dove == 'testo':
            _nel_testo(sez, riga, col, tile)
        elif dove == 'giu' and _solo_numeri(sez) and sezioni.index(sez) <= 3 and len(sezioni) > 4:
            sezioni.remove(sez)
            sezioni.insert(len(sezioni) - 2, sez)
        n += 1
    return n
