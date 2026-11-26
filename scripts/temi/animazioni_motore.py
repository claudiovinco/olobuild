# -*- coding: utf-8 -*-
"""
Le animazioni dei Pro Slider dei temi coi nomi che il motore conosce (1.4.1435). Rilanciabile.

- animazioni continue scritte coi nomi sbagliati (non partivano mai): float → floatLoop, pulse → pulseLoop,
  breathe → breathLoop, flicker → flickerLoop, wander → wanderLoop, drift → driftLoop (le ultime tre nuove nel motore);
- entrata «typewriter» (il motore non la conosceva): dissolvenza veloce + lettere una alla volta (charAnim), comandi
  che l'editor ha già; la durata scritta diventa il passo fra le lettere.

    python scripts/temi/animazioni_motore.py [scrivi]
"""
import io, json, os, re, sys
sys.stdout.reconfigure(encoding='utf-8')
QUI = os.path.dirname(os.path.abspath(__file__))
TEMI = os.path.normpath(os.path.join(QUI, '..', '..', 'assets', 'data', 'themes'))
SCRIVI = len(sys.argv) > 1 and sys.argv[1] == 'scrivi'
LOOP = {'float': 'floatLoop', 'pulse': 'pulseLoop', 'breathe': 'breathLoop', 'flicker': 'flickerLoop',
        'wander': 'wanderLoop', 'drift': 'driftLoop'}


def lettere(html):
    return len(re.sub(r'\s+', ' ', re.sub(r'<[^>]*>', '', str(html or ''))).strip()) or 1


tot = {'loop': 0, 'typewriter': 0}
for nome in sorted(os.listdir(TEMI)):
    dp = os.path.join(TEMI, nome)
    if not os.path.isdir(dp): continue
    for f in sorted(os.listdir(dp)):
        if not f.endswith('.json') or f == 'theme.json': continue
        p = os.path.join(dp, f)
        raw = io.open(p, encoding='utf-8').read()
        if '"animLoop"' not in raw and 'typewriter' not in raw: continue
        d = json.loads(raw)
        if json.dumps(d, ensure_ascii=False, separators=(',', ':')) != raw.rstrip('\n'):
            print('  (salto, non si rilegge identico)', nome, f); continue
        n = 0
        def v(o, dentro=False):
            global n
            if isinstance(o, list):
                for x in o: v(x, dentro)
            elif isinstance(o, dict):
                if o.get('type') == 'proslider' and isinstance(o.get('settings'), dict):
                    v(o['settings'], True); return
                if dentro and isinstance(o.get('type'), str):
                    if o.get('animLoop') in LOOP:
                        o['animLoop'] = LOOP[o['animLoop']]; n += 1; tot['loop'] += 1
                    if o.get('animIn') == 'typewriter':
                        dur = int(o.get('animInDuration') or 1200)
                        o['animIn'] = 'fadeIn'
                        o['animInDuration'] = 200
                        o['charAnim'] = {'split': 'chars', 'direction': 'forward', 'stagger': max(15, min(120, round(dur / lettere(o.get('content')))))}
                        n += 1; tot['typewriter'] += 1
                for x in o.values(): v(x, dentro)
        v(d)
        if n:
            print('%-16s %-14s %d' % (nome, f, n))
            if SCRIVI:
                io.open(p, 'w', encoding='utf-8', newline='').write(json.dumps(d, ensure_ascii=False, separators=(',', ':')))
print(tot, '· scritto' if SCRIVI else '· PROVA')
