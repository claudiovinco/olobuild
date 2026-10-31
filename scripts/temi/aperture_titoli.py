# -*- coding: utf-8 -*-
"""Per ogni titolo dell'apertura Slider Pro (testi dai 30 px), in tutte le diapositive: quanto è largo su una riga sola
rispetto alla larghezza del suo livello. Oltre 1,0 il titolo va a capo (e di solito copre la riga sotto).
Stampa, per famiglia di livello (l'id senza il numero della diapositiva), il rapporto peggiore e la diapositiva.
uso: python aperture_titoli.py larghezza tema [tema …]"""
import re
import sys

from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')
larg, temi = int(sys.argv[1]), sys.argv[2:]
JS = r"""() => {
  const out = [];
  document.querySelectorAll('.olo-proslider .olo-proslider-layer').forEach(L => {
    const id = ([...L.classList].find(c => c.startsWith('mps-layer-')) || '').slice(10);
    const el = L.querySelector('h1,h2,h3,h4,h5,h6,p,span,div') || L;
    const fs = parseFloat(getComputedStyle(el).fontSize);
    const t = (L.textContent || '').trim();
    if (!t || fs < 30 || getComputedStyle(L).display === 'none') return;
    const c = el.cloneNode(true);
    c.style.cssText += ';position:absolute;visibility:hidden;white-space:nowrap;width:auto;max-width:none;left:0;top:0;';
    L.parentElement.appendChild(c);
    const nat = c.getBoundingClientRect().width;
    c.remove();
    const disp = L.getBoundingClientRect().width;
    const scala = L.getBoundingClientRect().width / L.offsetWidth || 1;
    out.push({ id, t: t.replace(/\s+/g, ' ').slice(0, 30), r: Math.round(nat / disp * 100) / 100, fs: Math.round(fs) });
  });
  return out;
}"""
with sync_playwright() as p:
    b = p.chromium.launch()
    for tema in temi:
        pg = b.new_page(viewport={'width': larg, 'height': {390: 844, 820: 1180, 1024: 768}.get(larg, 900)},
                        is_mobile=larg < 700, has_touch=larg < 700)
        pg.goto('https://olotheme.com/olobuild/demo/%s/' % tema, wait_until='load', timeout=120000)
        pg.wait_for_timeout(2500)
        peggio = {}
        for L in pg.evaluate(JS):
            fam = re.sub(r'\d+$', '', L['id'])
            if fam not in peggio or L['r'] > peggio[fam]['r']:
                peggio[fam] = L
        righe = sorted(peggio.items(), key=lambda kv: -kv[1]['r'])
        print('%-15s %s' % (tema, '  '.join('%s %.2f (%dpx «%s»)' % (f, L['r'], L['fs'], L['t'][:22]) for f, L in righe
                                             if L['r'] > 0.9) or 'tutti su una riga'))
        pg.close()
    b.close()
