# -*- coding: utf-8 -*-
"""Cerca le PAROLE SPEZZATE a metà (una parola più larga della sua colonna: il browser la manda a capo dentro, «Faccia|mo»),
nelle pagine dei temi alla larghezza data. Per ogni parola di almeno 4 lettere guarda le righe che occupa: se sono due
o più, è spezzata (le parole col trattino sono escluse: lì andare a capo è giusto). Scorre la pagina per far comparire
tutto, e misura anche le diapositive non attive del Pro Slider (le rende visibili per un attimo).
uso: python parole_spezzate.py larghezza [processi] tema[/pagina] …    (senza temi: le home di tutti)
Esempio del 13 nov 2026: Cromo e Casa Scogliera, titolo dell'apertura a 390 px."""
import json
import os
import sys
from concurrent.futures import ThreadPoolExecutor

from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')
TEMI = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..', 'assets', 'data', 'themes'))
BASE = 'https://olotheme.com/olobuild/demo/'

MISURA = r"""()=>{const out=[];const w=document.createTreeWalker(document.body,NodeFilter.SHOW_TEXT);let n;
 const r=document.createRange();
 while((n=w.nextNode())){const p=n.parentElement;if(!p||p.closest('script,style,noscript,svg,[aria-hidden="true"]'))continue;
  const cs=getComputedStyle(p);if(cs.visibility==='hidden'||cs.display==='none')continue;
  const t=n.textContent;const re=/[A-Za-zÀ-ÿ’']{4,}/g;let m;
  while((m=re.exec(t))){r.setStart(n,m.index);r.setEnd(n,m.index+m[0].length);const q=[...r.getClientRects()].filter(x=>x.width>0.5&&x.height>0.5);
   if(q.length<2)continue;const tops=new Set(q.map(x=>Math.round(x.top/3)));if(tops.size<2)continue;
   const s=m.index>0?t[m.index-1]:'',e=t[m.index+m[0].length]||'';if(s==='-'||e==='-'||s==='­'||e==='­')continue;
   const fs=parseFloat(cs.fontSize);out.push(m[0]+' · '+p.tagName.toLowerCase()+' '+Math.round(fs)+'px · «'+t.trim().slice(0,60)+'»')}}
 return [...new Set(out)]}"""


def una(args):
    url, w = args
    with sync_playwright() as pw:
        b = pw.chromium.launch(channel='chrome', headless=True)
        tel = w < 800
        ctx = b.new_context(viewport={'width': w, 'height': 844 if tel else 900}, is_mobile=tel, has_touch=tel)
        pg = ctx.new_page()
        try:
            pg.goto(url, wait_until='networkidle', timeout=90000)
            pg.add_style_tag(content='[class^="olo-cc"],[id^="olo-cc"]{display:none!important}')
            pg.wait_for_timeout(2500)
            y, tot = 0, pg.evaluate('document.documentElement.scrollHeight')
            while y < tot:
                y += 700
                pg.evaluate('(y)=>scrollTo(0,y)', y)
                pg.wait_for_timeout(120)
                tot = pg.evaluate('document.documentElement.scrollHeight')
            pg.evaluate('scrollTo(0,0)')
            pg.wait_for_timeout(1500)
            trov = pg.evaluate(MISURA)
            # le diapositive non attive: visibili per la misura
            pg.add_style_tag(content='.mps-slide{opacity:1!important;visibility:visible!important;display:block!important}'
                                     '.mps-layer{opacity:1!important;visibility:visible!important;transform:none!important;'
                                     'clip-path:none!important;filter:none!important}')
            pg.wait_for_timeout(400)
            trov = sorted(set(trov) | set(pg.evaluate(MISURA)))
        except Exception as e:  # noqa
            trov = ['ERRORE ' + str(e)[:120]]
        b.close()
    return url, trov


def main():
    w = int(sys.argv[1])
    n = int(sys.argv[2]) if len(sys.argv) > 2 else 2
    temi = sys.argv[3:] or sorted(os.listdir(TEMI))
    urls = [(BASE + t.strip('/') + '/', w) for t in temi]
    with ThreadPoolExecutor(n) as ex:
        for url, trov in ex.map(una, urls):
            if trov:
                print('==', url.replace(BASE, ''), flush=True)
                for x in trov:
                    print('   ', x, flush=True)
    print('fatto:', len(urls), 'pagine a', w, 'px')


if __name__ == '__main__':
    main()
