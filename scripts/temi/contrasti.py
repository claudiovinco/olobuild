# -*- coding: utf-8 -*-
"""Contrasto di ogni testo visibile contro lo sfondo che ha davvero sotto (colori composti salendo nei genitori), e di
link e pulsanti in hover. Soglie WCAG AA: 4,5 (3 per i testi grandi). I testi sopra foto o video si segnalano a parte.
uso: python contrasti.py larghezza URL [URL …]"""
import json
import sys
sys.stdout.reconfigure(encoding='utf-8')
from playwright.sync_api import sync_playwright

JS_UTIL = r"""
window.__ctr = (function(){
  function parse(c){ c = String(c);
    let m = c.match(/color\(srgb ([^)]+)\)/);
    if(m){ const p = m[1].split(/[ \/]+/).filter(Boolean).map(Number); return [p[0] * 255, p[1] * 255, p[2] * 255, p.length > 3 ? p[3] : 1]; }
    m = c.match(/rgba?\(([^)]+)\)/); if(!m){ return null; }
    const p = m[1].split(/[ ,\/]+/).filter(Boolean).map(Number); return [p[0], p[1], p[2], p.length > 3 ? p[3] : 1]; }
  function lum(c){ const f = v => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
    return 0.2126 * f(c[0]) + 0.7152 * f(c[1]) + 0.0722 * f(c[2]); }
  function ratio(a, b){ const x = lum(a), y = lum(b); return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05); }
  function media(e){
    // Uno sfondo fatto di foto o video: nel genitore o come livello assoluto dentro un antenato.
    let n = e;
    while(n){ const cs = getComputedStyle(n);
      if(cs.backgroundImage && cs.backgroundImage !== 'none' && !/gradient/.test(cs.backgroundImage)){ return true; }
      for(const k of n.children){ if(k.contains(e)){ continue; }
        if(/^(VIDEO|IMG|PICTURE|IFRAME|CANVAS)$/.test(k.tagName)){ const ks = getComputedStyle(k); if(ks.position === 'absolute' || ks.position === 'fixed'){ return true; } }
        if(k.querySelector){ const v = k.querySelector('video'); if(v){ const ks = getComputedStyle(k); if(ks.position === 'absolute'){ return true; } } } }
      const c = parse(cs.backgroundColor); if(c){ if(c[3] >= 1){ return false; } }
      n = n.parentElement; }
    return false; }
  function bg(e){ const layers = []; let n = e;
    while(n){ const c = parse(getComputedStyle(n).backgroundColor); if(c){ if(c[3] > 0){ layers.push(c); if(c[3] >= 1){ break; } } } n = n.parentElement; }
    let res = [255, 255, 255];
    for(let i = layers.length - 1; i >= 0; i--){ const l = layers[i]; res = res.map((v, k) => l[k] * l[3] + v * (1 - l[3])); }
    return res; }
  function opac(e){ let o = 1, n = e; while(n){ o *= parseFloat(getComputedStyle(n).opacity); n = n.parentElement; } return o; }
  function misura(e){
    const cs = getComputedStyle(e); const b = bg(e); const c = parse(cs.color) || [0, 0, 0, 1];
    const fg = [0, 1, 2].map(k => c[k] * c[3] + b[k] * (1 - c[3]));
    const size = parseFloat(cs.fontSize), peso = parseInt(cs.fontWeight, 10) || 400;
    const grande = size >= 24 || (size >= 18.66 && peso >= 700);
    return { r: Math.round(ratio(fg, b) * 100) / 100, soglia: grande ? 3 : 4.5, fg: cs.color, bg: 'rgb(' + b.map(Math.round).join(',') + ')',
             size: size, media: media(e), op: Math.round(opac(e) * 100) / 100 };
  }
  function visibile(e){ const r = e.getBoundingClientRect(); if(r.width < 2 || r.height < 2){ return false; }
    const cs = getComputedStyle(e); return cs.visibility !== 'hidden' && cs.display !== 'none'; }
  function testi(){
    const out = []; const tutti = document.querySelectorAll('body *');
    for(const e of tutti){
      if(/^(SCRIPT|STYLE|NOSCRIPT|TEMPLATE|SVG|PATH|OPTION)$/i.test(e.tagName)){ continue; }
      if(e.closest('template, [aria-hidden="true"], .olo-avc, #wpadminbar')){ continue; }
      const proprio = [...e.childNodes].some(n => n.nodeType === 3 && n.textContent.trim().length > 1);
      if(!proprio || !visibile(e)){ continue; }
      const m = misura(e); m.testo = e.textContent.trim().replace(/\s+/g, ' ').slice(0, 60); m.tag = e.tagName.toLowerCase();
      m.cls = (e.className && e.className.baseVal === undefined ? e.className : '').toString().slice(0, 50);
      out.push(m);
    }
    return out; }
  return { misura, testi, visibile };
})();
"""


def scorri(pg, h):
    tot = pg.evaluate('document.documentElement.scrollHeight'); y = 0
    while y < tot:
        pg.evaluate('scrollTo(0,%d)' % y); pg.wait_for_timeout(220); y += h // 2
        tot = pg.evaluate('document.documentElement.scrollHeight')
    pg.wait_for_timeout(1500)


def main():
    w = int(sys.argv[1])
    urls = sys.argv[2:]
    h = 900 if w > 800 else 844
    rapporto = {}
    with sync_playwright() as p:
        b = p.chromium.launch()
        ctx = b.new_context(viewport={'width': w, 'height': h}, is_mobile=w < 800, has_touch=w < 800)
        pg = ctx.new_page()
        for u in urls:
            pg.goto(u, wait_until='networkidle', timeout=90000)
            pg.wait_for_timeout(2800)
            scorri(pg, h)
            pg.add_script_tag(content=JS_UTIL)
            testi = pg.evaluate('window.__ctr.testi()')
            bassi = [t for t in testi if t['r'] < t['soglia'] and not t['media'] and t['op'] > 0.6]
            su_media = [t for t in testi if t['media'] and t['op'] > 0.6]
            animati = [t for t in testi if t['op'] <= 0.6]
            # Hover di link e pulsanti visibili (testo proprio o nei figli)
            n = pg.evaluate("""(() => { let i = 0; document.querySelectorAll('a, button').forEach(e => {
                if(e.closest('template, .olo-avc, #wpadminbar')){ return; }
                if(!window.__ctr.visibile(e) || !e.textContent.trim()){ return; }
                e.setAttribute('data-ctr', String(i++)); }); return i; })()""")
            hover = []
            for i in range(min(n, 80)):
                loc = pg.locator('[data-ctr="%d"]' % i)
                try:
                    loc.scroll_into_view_if_needed(timeout=2000)
                    loc.hover(timeout=2000, force=True)
                    pg.wait_for_timeout(380)
                    m = pg.evaluate("""(i => { const e = document.querySelector('[data-ctr="' + i + '"]');
                        const t = [...e.querySelectorAll('*')].concat([e]).filter(x => [...x.childNodes].some(n => n.nodeType === 3 && n.textContent.trim()));
                        const x = t[0] || e; const m = window.__ctr.misura(x); m.testo = e.textContent.trim().replace(/\\s+/g, ' ').slice(0, 50); return m; })(%d)""" % i)
                    if m['r'] < m['soglia'] and not m['media']:
                        hover.append(m)
                except Exception:
                    pass
            pg.mouse.move(1, 1)
            rapporto[u] = {'testi': len(testi), 'bassi': bassi, 'hover': hover, 'su_media': len(su_media), 'animati': len(animati)}
            print('\n==', u.replace('https://olotheme.com/olobuild', ''), '· testi', len(testi), '· su foto/video', len(su_media),
                  '· ancora in animazione', len(animati))
            for t in bassi:
                print('   %.2f/%s  %-38s %s su %s  %spx  <%s %s>' % (t['r'], t['soglia'], t['testo'][:38], t['fg'], t['bg'], t['size'], t['tag'], t['cls'][:30]))
            for t in hover:
                print('   HOVER %.2f/%s  %-32s %s su %s' % (t['r'], t['soglia'], t['testo'][:32], t['fg'], t['bg']))
        b.close()
    json.dump(rapporto, open('contrasti-%d.json' % w, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)


main()
