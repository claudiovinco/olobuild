# -*- coding: utf-8 -*-
"""L'elenco delle scene del Pro Slider (1.4.1422): ogni Pro Slider di ogni tema diventa una scena da cui partire
nell'editor (pulsante «Scene dei temi»). Utente, 24 nov 2026: «riprogettare la tile pro slider in modo che permetta di
costruire tutti i pro slider che hai inserito in tutti i temi… anche l'utente poco addestrato deve capire». Fase 1:
si parte da una scena vera e si cambiano testi e foto.

Scrive assets/data/scene-slider.json: solo i dati per le card (tipo, numero di slide, titolo, sfondo). Il contenuto
si legge dalla pagina del tema quando la scena si usa (Olobuild_Scene_Slider): nello zip della Free le pagine dei temi
del Pro non ci sono, l'elenco sì (col lucchetto).

Da rifare dopo aver aggiunto un tema o cambiato un suo Pro Slider:   python scripts/indice-scene-slider.py
"""
import glob
import html
import json
import os
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.join(QUI, '..', 'assets', 'data', 'themes')
USCITA = os.path.join(QUI, '..', 'assets', 'data', 'scene-slider.json')
CONTENITORI = {'section', 'row', 'column', 'container', 'grid', 'columns'}


def livelli_di(s):
    out = [l for sl in s.get('slides') or [] for l in sl.get('layers') or []]
    return out + list(s.get('globalLayers') or [])


def tipo_di(s):
    """Il tipo di scena, come nel censimento del 24 nov: scelte (cambia con un clic), animata (3D, pieghe, tende,
    linea del tempo), scorrimento (si muove scorrendo la pagina), classica (entra e resta)."""
    ls = livelli_di(s)
    if any(l.get('stato') or l.get('pulisci') or (l.get('action') or {}).get('type') in ('setState', 'cycleState', 'toggleLayer')
           for l in ls):
        return 'scelte'
    css = ' '.join(str(l.get('customCSS') or '') for l in ls)
    if ('backface' in css or 'transform-origin' in css or 'background:url' in css
            or any(len((l.get('timeline') or {}).get('keyframes') or []) >= 2 for l in ls)):
        return 'animata'
    if any(l.get('scrollMove') for l in ls) or s.get('scrollTimeline') or s.get('scrollScrub'):
        return 'scorrimento'
    return 'classica'


def testo(v):
    return re.sub(r'\s+', ' ', html.unescape(re.sub(r'<[^>]+>', ' ', str(v or '')))).strip()


def titolo_di(s):
    """La frase più grande della prima slide (il titolo dell'apertura)."""
    sl = (s.get('slides') or [{}])[0]
    testi = [l for l in sl.get('layers') or [] if l.get('type') == 'text' and testo(l.get('content'))]
    if not testi:
        return ''
    h1 = [l for l in testi if l.get('tag') == 'h1']
    l = h1[0] if h1 else max(testi, key=lambda x: float(x.get('fontSize') or 0))
    return testo(l.get('content'))[:90]


def sfondo_di(s):
    bg = ((s.get('slides') or [{}])[0].get('background') or {})
    if bg.get('type') in ('image', 'video') and bg.get('image'):
        return bg['image'], bg.get('type') == 'video'
    g = s.get('globalBackground') or {}
    if g.get('type') in ('image', 'video') and g.get('image'):
        return g['image'], g.get('type') == 'video'
    return '', False


def main():
    scene = []
    for cartella in sorted(glob.glob(os.path.join(TEMI, '*'))):
        tj = os.path.join(cartella, 'theme.json')
        if not os.path.isfile(tj):
            continue
        tema = os.path.basename(cartella)
        meta = json.load(open(tj, encoding='utf-8'))
        if meta.get('in_lavorazione'):
            continue
        pagine = ['home'] + sorted(k for k in (meta.get('pages') or {}) if k != 'home')
        for pagina in pagine:
            f = os.path.join(cartella, (meta.get('pages') or {}).get(pagina, {}).get('template', pagina) + '.json')
            if not os.path.isfile(f):
                continue
            nodi = json.load(open(f, encoding='utf-8'))
            visti = []  # le tile in ordine, per sapere se lo slider è la prima della home

            def giro(o):
                if isinstance(o, dict):
                    if o.get('type') and o.get('type') not in CONTENITORI:
                        visti.append(o)
                    for v in o.values():
                        giro(v)
                elif isinstance(o, list):
                    for x in o:
                        giro(x)
            giro(nodi)
            for i, n in enumerate(visti):
                if n.get('type') != 'proslider' or not isinstance(n.get('settings'), dict) or not n.get('id'):
                    continue
                s = n['settings']
                if not s.get('slides'):
                    continue
                sfondo, video = sfondo_di(s)
                scene.append({
                    'id': '%s/%s/%s' % (tema, pagina, n['id']), 'tema': tema, 'pagina': pagina,
                    'tipo': tipo_di(s), 'schede': bool(s.get('showTabs')), 'slide': len(s.get('slides') or []),
                    'livelli': len(livelli_di(s)), 'prima': pagina == 'home' and i == 0,
                    'titolo': titolo_di(s), 'sfondo': sfondo, 'video': video,
                })
    json.dump({'v': 1, 'scene': scene}, open(USCITA, 'w', encoding='utf-8', newline='\n'), ensure_ascii=False,
              separators=(',', ':'))
    conti = {}
    for x in scene:
        conti[x['tipo']] = conti.get(x['tipo'], 0) + 1
    print(len(scene), 'scene da', len({x['tema'] for x in scene}), 'temi:', conti,
          '| prime della home', sum(1 for x in scene if x['prima']), '| con le schede', sum(1 for x in scene if x['schede']))


if __name__ == '__main__':
    main()
