# -*- coding: utf-8 -*-
"""Il CSS su misura dei livelli del Pro Slider diventa comandi dell'editor (1.4.1430).

Utente, 24 nov 2026: «devi fare in modo che il pro slider possa gestirle tutte in modo facile… vai OLTRE». Nei temi
456 dichiarazioni stavano nel «CSS su misura» dei livelli (Avanzate), che un cliente non sa leggere. Qui quelle che hanno
un comando (426 su 456) ci vanno:

  transform-origin            → origine        («Punto di rotazione», Posizione)
  backface-visibility: hidden → retroNascosto  («Retro nascosto», per le carte che girano)
  background: url(…) pos / dim→ shapeImage, shapeImagePos, shapeImageSize, shapeImageRepeat («Immagine nella forma»)
  white-space: nowrap         → unaRiga        («Su una riga sola»)
  rotate: Ndeg                → rotazione      («Rotazione»)
  box-shadow (una sola)       → boxShadow      (il comando «Ombra» che c'era già)
  text-shadow (una sola)      → textShadow     («Ombra del testo» che c'era già)
  filter: brightness(n)       → filterBrightness (foto e video)

Il resto resta nel CSS su misura. Lo usano: scripts/temi/kit.py (Kit.livello, così un tema rigenerato nasce coi
comandi) e scripts/temi/migra_css_comandi.py (i JSON dei temi già fatti).
"""
import re


def _dichiarazioni(css):
    out, buf, prof = [], '', 0
    for ch in str(css or ''):
        if ch == '(':
            prof += 1
        elif ch == ')':
            prof = max(0, prof - 1)
        if ch == ';' and prof == 0:
            out.append(buf)
            buf = ''
        else:
            buf += ch
    out.append(buf)
    res = []
    for d in out:
        if ':' in d and d.strip():
            k, v = d.split(':', 1)
            res.append((k.strip().lower(), v.strip()))
    return res


def _fuori_parentesi(v, sep):
    """Divide v su sep solo fuori dalle parentesi."""
    out, buf, prof = [], '', 0
    for ch in v:
        if ch == '(':
            prof += 1
        elif ch == ')':
            prof = max(0, prof - 1)
        if ch == sep and prof == 0:
            out.append(buf)
            buf = ''
        else:
            buf += ch
    out.append(buf)
    return [x.strip() for x in out if x.strip()]


def _px(s):
    m = re.fullmatch(r'(-?\d+(?:\.\d+)?)(px)?', s.strip())
    return float(m.group(1)) if m else None


def _ombra(v, testo=False):
    if len(_fuori_parentesi(v, ',')) != 1 or 'inset' in v:
        return None
    parti = _fuori_parentesi(v, ' ')
    colore = [p for p in parti if _px(p) is None]
    numeri = [_px(p) for p in parti if _px(p) is not None]
    if len(colore) != 1 or not (2 <= len(numeri) <= (3 if testo else 4)):
        return None
    numeri += [0] * (4 - len(numeri))
    o = {'x': int(round(numeri[0])), 'y': int(round(numeri[1])), 'blur': int(round(numeri[2])), 'color': colore[0]}
    if not testo:
        o['spread'] = int(round(numeri[3]))
    return o


FUNZIONI_SFUMATURA = ('repeating-linear-gradient', 'repeating-radial-gradient', 'linear-gradient', 'radial-gradient')
DIREZIONI = {'to top': 0, 'to right': 90, 'to bottom': 180, 'to left': 270}
MISURA = re.compile(r'^-?\d*\.?\d+(px|%|em|rem|vw|vh)?$')


def _sfumatura(testo):
    """«radial-gradient(circle at 0 0,var(--c) 0 34%,transparent 35%)» → {tipo, ripeti, angolo | forma, dimensione,
    posizione, tappe: [{colore, da, a}]}. None se non si legge in modo sicuro."""
    testo = testo.strip()
    for f in FUNZIONI_SFUMATURA:
        if testo.startswith(f + '(') and testo.endswith(')'):
            break
    else:
        return None
    g = {'tipo': 'radiale' if 'radial' in f else 'lineare', 'ripeti': f.startswith('repeating-')}
    args = _fuori_parentesi(testo[len(f) + 1:-1], ',')
    if not args:
        return None
    testa = args[0]
    if g['tipo'] == 'lineare':
        if re.fullmatch(r'-?\d+(\.\d+)?deg', testa):
            g['angolo'] = float(testa[:-3]) if '.' in testa else int(testa[:-3])
            args = args[1:]
        elif testa in DIREZIONI:
            g['angolo'] = DIREZIONI[testa]
            args = args[1:]
    else:
        parole = _fuori_parentesi(testa, ' ')
        if parole and (parole[0] in ('circle', 'ellipse', 'at', 'closest-side', 'closest-corner', 'farthest-side', 'farthest-corner') or ' at ' in ' ' + testa + ' '):
            prima, _, dopo = (' ' + testa + ' ').partition(' at ')
            p = prima.split()
            if p and p[0] in ('circle', 'ellipse'):
                g['forma'] = p[0]
                p = p[1:]
            if p:
                g['dimensione'] = ' '.join(p)
            if dopo.strip():
                g['posizione'] = ' '.join(dopo.split())
            args = args[1:]
    tappe = []
    for a in args:
        parti = _fuori_parentesi(a, ' ')
        if not parti or MISURA.match(parti[0]):
            return None
        pos = parti[1:]
        if len(pos) > 2 or any(not MISURA.match(x) for x in pos):
            return None
        t = {'colore': parti[0]}
        if pos:
            t['da'] = pos[0]
        if len(pos) == 2:
            t['a'] = pos[1]
        tappe.append(t)
    if len(tappe) < 2:
        return None
    g['tappe'] = tappe
    return g


def sfumatura_css(g):
    """Il contrario di _sfumatura: lo stesso CSS che scrive il PHP (Olobuild_ProSlider_Tile::sfumatura_css)."""
    f = ('repeating-' if g.get('ripeti') else '') + ('radial' if g.get('tipo') == 'radiale' else 'linear') + '-gradient'
    testa = ''
    if g.get('tipo') == 'radiale':
        testa = ' '.join(x for x in (g.get('forma', ''), g.get('dimensione', '')) if x)
        if g.get('posizione'):
            testa = (testa + ' at ' + g['posizione']).strip()
    elif g.get('angolo') is not None:
        testa = ('%g' % g['angolo']) + 'deg'
    tappe = [' '.join(x for x in (t.get('colore', ''), t.get('da', ''), t.get('a', '')) if x) for t in g.get('tappe', [])]
    return f + '(' + ','.join(([testa] if testa else []) + tappe) + ')'


def _sfondo(v):
    """Uno sfondo fatto di sfumature (e al massimo un colore in fondo): [sfumature], colore. None se non si legge."""
    parti = _fuori_parentesi(v, ',')
    sf, colore = [], ''
    for i, p in enumerate(parti):
        g = _sfumatura(p)
        if g:
            sf.append(g)
        elif i == len(parti) - 1 and re.fullmatch(r'(#[0-9a-fA-F]{3,8}|var\([^()]*\)|rgba?\([^()]*\)|transparent|[a-z]+)', p.strip()):
            colore = p.strip()
        else:
            return None
    return (sf, colore) if sf else None


def _ombre(v):
    """Più ombre, anche interne: [{x, y, blur, spread, color, inset}]. None se non si legge."""
    out = []
    for o in _fuori_parentesi(v, ','):
        parti = _fuori_parentesi(o, ' ')
        inset = 'inset' in parti
        parti = [p for p in parti if p != 'inset']
        colore = [p for p in parti if _px(p) is None]
        numeri = [_px(p) for p in parti if _px(p) is not None]
        if len(colore) != 1 or not (2 <= len(numeri) <= 4):
            return None
        numeri += [0] * (4 - len(numeri))
        out.append({'x': int(round(numeri[0])), 'y': int(round(numeri[1])), 'blur': int(round(numeri[2])), 'spread': int(round(numeri[3])), 'color': colore[0], 'inset': inset})
    return out or None


def _norm(s):
    return re.sub(r'\s*([,()/])\s*', r'\1', re.sub(r'\s+', ' ', s.strip()))


def converti(livello):
    """Sposta nei comandi il CSS su misura che ne ha uno. Cambia il dizionario e lo restituisce."""
    css = str(livello.get('customCSS') or '').strip()
    if not css:
        return livello
    resto = []
    flex = []
    retro = False
    for k, v in _dichiarazioni(css):
        if k == 'transform-origin' and re.fullmatch(r'[\w.%\s-]+', v):
            livello['origine'] = v
        elif k in ('backface-visibility', '-webkit-backface-visibility') and v == 'hidden':
            retro = True
        elif k == 'white-space' and v == 'nowrap' and livello.get('type') in ('text', 'button'):
            livello['unaRiga'] = True
        elif k == 'rotate' and re.fullmatch(r'-?\d+(\.\d+)?deg', v):
            livello['rotazione'] = float(v[:-3]) if '.' in v else int(v[:-3])
        elif k == 'background' and livello.get('type') == 'shape' and v.startswith('url(') and 'shapeImage' not in livello:
            m = re.fullmatch(r'url\(\s*([^)\s]+)\s*\)\s*(.*)', v)
            if not m:
                resto.append((k, v))
                continue
            dopo = m.group(2)
            ripeti = ''
            for r in ('no-repeat', 'repeat-x', 'repeat-y', 'repeat'):
                if re.search(r'\b' + r + r'\b', dopo):
                    ripeti = r
                    dopo = re.sub(r'\b' + r + r'\b', '', dopo).strip()
                    break
            pos, _, dim = dopo.partition('/')
            livello['shapeImage'] = m.group(1).strip('\'"')
            if pos.strip():
                livello['shapeImagePos'] = re.sub(r'\s+', ' ', pos.strip())
            if dim.strip():
                livello['shapeImageSize'] = re.sub(r'\s+', ' ', dim.strip())
            if ripeti:
                livello['shapeImageRepeat'] = ripeti
        elif (k == 'background' and livello.get('type') == 'shape' and not livello.get('shapeGradient')
              and re.fullmatch(r'linear-gradient\(\s*-?\d+(\.\d+)?deg\s*,[^,()]+(\([^()]*\))?\s*,[^,()]+(\([^()]*\))?\s*\)', v)):
            # una sfumatura a due colori senza tappe: il comando «Sfumatura» della forma (Cementina, 16 piastrelle)
            ang, a, b = _fuori_parentesi(v[len('linear-gradient('):-1], ',')
            livello['shapeGradient'] = {'angle': int(float(ang[:-3])), 'from': a, 'to': b, 'fromStop': 0}
        elif k == 'box-shadow' and not livello.get('boxShadow') and _ombra(v):
            livello['boxShadow'] = _ombra(v)
        elif k == 'text-shadow' and not livello.get('textShadow') and _ombra(v, testo=True):
            livello['textShadow'] = _ombra(v, testo=True)
        elif (k == 'filter' and livello.get('type') in ('image', 'video') and re.fullmatch(r'brightness\(\s*\d*\.?\d+\s*\)', v)
              and livello.get('filterBrightness') in (None, 100)):
            n = float(re.search(r'\d*\.?\d+', v).group(0))
            livello['filterBrightness'] = int(round(n * 100 if n <= 3 else n))
        # ── 1.4.1431: anche il resto, perché nei temi non resti CSS su misura (utente: «non devono esserci css
        # personalizzati o hardcoded nelle cose fatte con pro slider ma comandi usabili da chiunque») ──
        elif k in ('background', 'background-image') and livello.get('type') == 'shape' and 'sfondoForma' not in livello and _sfondo(v):
            sf, colore = _sfondo(v)
            livello['sfondoForma'] = sf
            if k == 'background-image':
                livello['sfondoFormaSulColore'] = True   # il colore della forma resta sotto le sfumature
            elif colore:
                livello['sfondoFormaColore'] = colore
        elif k == 'box-shadow' and 'ombre' not in livello and _ombre(v):
            livello['ombre'] = _ombre(v)
        elif k == 'filter' and 'ombraForma' not in livello and re.fullmatch(r'drop-shadow\(([^()]*(\([^()]*\))?[^()]*)\)', v):
            o = _ombra(v[len('drop-shadow('):-1], testo=True)
            if o:
                livello['ombraForma'] = o
            else:
                resto.append((k, v))
        elif k == 'transition' and re.fullmatch(r'background-color\s+\d*\.?\d+m?s(\s+ease)?', v):
            n = float(re.search(r'\d*\.?\d+', v).group(0))
            livello['sfumaColori'] = int(n if 'ms' in v else n * 1000)
        elif k == 'clip-path' and re.fullmatch(r'polygon\(([^()]*)\)', v):
            punti = []
            for coppia in v[len('polygon('):-1].split(','):
                xy = coppia.split()
                if len(xy) != 2 or not all(MISURA.match(x) for x in xy):
                    punti = None
                    break
                punti.append(xy)
            if punti and len(punti) >= 3:
                livello['ritaglio'] = punti
            else:
                resto.append((k, v))
        elif k == 'clip-path' and re.fullmatch(r'inset\(\s*(-?\d*\.?\d+%?\s*){4}\)', v):
            # inset(alto destra basso sinistra) = un rettangolo di quattro punti (Truciolo, la lama a metà)
            n = [float(x.rstrip('%')) for x in v[len('inset('):-1].split()]
            f = lambda x: ('%g' % x) + '%' if x else '0'
            livello['ritaglio'] = [[f(n[3]), f(n[0])], [f(100 - n[1]), f(n[0])], [f(100 - n[1]), f(100 - n[2])], [f(n[3]), f(100 - n[2])]]
        elif k == 'translate' and re.fullmatch(r'var\(--mps-clic-x-([\w-]+),\s*0px\)\s+var\(--mps-clic-y-\1,\s*0px\)', v):
            livello['seguiClic'] = re.match(r'var\(--mps-clic-x-([\w-]+)', v).group(1)
        elif k == 'pointer-events' and v == 'none':
            livello['clicPassanti'] = True
        elif k == 'overflow' and v == 'hidden':
            livello['ritaglia'] = True
        elif k == 'font-variation-settings' and re.fullmatch(r'("(wdth|wght)"\s+\d+(\.\d+)?\s*,?\s*){1,2}', v):
            for asse, n in re.findall(r'"(wdth|wght)"\s+(\d+(?:\.\d+)?)', v):
                if asse == 'wdth':
                    livello['fontWdth'] = float(n) if '.' in n else int(n)
                else:
                    livello['fontWeight'] = str(int(float(n)))
        elif k == 'mix-blend-mode' and livello.get('blendMode') in (None, '', 'normal') and re.fullmatch(r'[a-z-]+', v):
            livello['blendMode'] = v
        elif k in ('mask-image', '-webkit-mask-image') and re.fullmatch(r'radial-gradient\(closest-side,\s*#000\s+\d+%,\s*transparent\s+100%\)', v):
            livello['bordiSfumati'] = int(re.search(r'#000\s+(\d+)%', v).group(1))
        elif (re.fullmatch(r'border-(top|right|bottom|left)', k) and not livello.get('borderWidth')
              and re.fullmatch(r'\d+px\s+(solid|dashed|dotted|double)\s+\S.*', v)):
            w, stile, colore = v.split(None, 2)
            livello['borderWidthLinked'] = False
            livello['borderWidth' + k.split('-')[1].capitalize()] = int(w[:-2])
            livello['borderStyle'] = stile
            livello['borderColor'] = colore
        elif k in ('display', 'flex-direction', 'justify-content'):
            flex.append((k, v))
        else:
            resto.append((k, v))
    # «display:flex; flex-direction:column; justify-content:flex-end» su un testo = «Allineamento verticale: in basso»
    if flex:
        f = dict(flex)
        if (livello.get('type') == 'text' and f.get('display') == 'flex' and f.get('flex-direction') == 'column'
                and f.get('justify-content') in ('flex-end', 'center') and len(f) == 3):
            livello['textValign'] = 'bottom' if f['justify-content'] == 'flex-end' else 'center'
        else:
            resto.extend(flex)
    if retro:
        livello['retroNascosto'] = True
    nuovo = ';'.join(k + ':' + v for k, v in resto)
    if nuovo:
        livello['customCSS'] = nuovo + ';'
    else:
        livello.pop('customCSS', None)
    return livello
