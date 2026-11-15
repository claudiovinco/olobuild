# -*- coding: utf-8 -*-
"""Le sezioni più ripetute in tutti i temi (13 nov 2026): scheletro = righe, larghezze delle colonne e tipi di tile (i
testi contano come «txt»). Da rifare prima di dire che i temi sono vari: una disposizione in decine di temi, sempre
uguale, è da variare (strutture.py, varia_home.py, varia_ordine.py).
uso: python scheletri.py [quanti]"""
import json, os, sys
from collections import Counter, defaultdict
sys.stdout.reconfigure(encoding='utf-8')
T = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes'))
SKIP = {'header.json', 'footer.json', 'theme.json'}
TXT = {'headline', 'text-block', 'section-header', 'button', 'spacer', 'divider', 'icon'}
def sk(n):
    if not isinstance(n, dict): return ''
    t = n.get('type')
    s = n.get('settings') or {}
    if t == 'section':
        return 'S[' + '/'.join(sk(c) for c in n.get('children') or []) + ']'
    if t in ('row', 'inner-columns'):
        return 'R(' + ','.join(sk(c) for c in n.get('children') or []) + ')'
    if t in ('column', 'inner-column'):
        ch = [sk(c) for c in n.get('children') or []]
        ch = [x for x in ch if x]
        return (s.get('width_medium') or '') + ':' + '+'.join(ch)
    if t in TXT: return 'txt'
    if t == 'product-cards': return 'product-cards%d' % len(s.get('items') or [])
    return t or ''
cnt = Counter(); dove = defaultdict(set)
for tid in sorted(os.listdir(T)):
    for f in sorted(os.listdir(os.path.join(T, tid))):
        if not f.endswith('.json') or f in SKIP: continue
        j = json.load(open(os.path.join(T, tid, f), encoding='utf-8'))
        c = j['content'] if isinstance(j, dict) and 'content' in j else j
        if not isinstance(c, list): continue
        for s in c:
            k = sk(s)
            k = k.replace('txt+txt+txt', 'txt*').replace('txt+txt', 'txt*')
            cnt[k] += 1; dove[k].add(tid)
for k, n in cnt.most_common(int(sys.argv[1]) if len(sys.argv) > 1 else 25):
    print('%4d volte in %3d temi  %s' % (n, len(dove[k]), k[:150]))
