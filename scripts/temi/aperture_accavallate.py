# -*- coding: utf-8 -*-
"""Controlla i livelli dell'apertura Slider Pro della home di ogni demo mentre la pagina scorre (la timeline a
scorrimento fa comparire testi e pulsanti a metà strada) e in ogni diapositiva: pulsanti che si toccano o si coprono,
testi sopra un pulsante, testi sopra altri testi. A differenza di aperture_sovrapposte.py guarda anche il riquadro dei
pulsanti (sfondo e bordo), non solo le lettere: due pulsanti affiancati con le scritte staccate ma i riquadri uno
dentro l'altro (Fuori Registro fra 1025 e 1240 px, 3 nov 2026) là non risultavano.
uso: python aperture_accavallate.py uscita.json 1100,1240,1366,1440,1024,820,390 [tema …] [--ultimi N]
     python aperture_accavallate.py uscita.json 1440,390 --sito https://sito10.clod.eu/   (la home di un sito)"""
import asyncio
import glob
import json
import os
import sys

from playwright.async_api import async_playwright

BASE = 'https://olotheme.com/olobuild/demo/'
ROOT = os.path.normpath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', '..'))
PRIVATI = {'clod-evoluzione', 'tutor-clod'}
args = sys.argv[1:]
ULTIMI = 0
SITO = ''
if '--sito' in args:
    i = args.index('--sito')
    SITO = args[i + 1]
    del args[i:i + 2]
if '--ultimi' in args:
    i = args.index('--ultimi')
    ULTIMI = int(args[i + 1])
    del args[i:i + 2]
OUT, LARGHE = args[0], [int(x) for x in args[1].split(',')]
SOLO = set(args[2:])
CARTELLA = os.path.splitext(OUT)[0]
os.makedirs(CARTELLA, exist_ok=True)
SPAZIO_MIN = 8  # px fra due pulsanti affiancati: sotto è un errore anche se non si toccano

TEMI = []
if ULTIMI:
    date = json.load(open(os.path.join(ROOT, 'assets', 'data', 'temi-creati.json'), encoding='utf-8'))
    TEMI = [t for t, _ in sorted(date.items(), key=lambda x: x[1], reverse=True) if t not in PRIVATI][:ULTIMI]
else:
    for radice in [ROOT + '/assets/data/themes', ROOT + '/../olo-booking/themes']:
        for f in sorted(glob.glob(radice + '/*/theme.json')):
            d = json.load(open(f, encoding='utf-8'))
            tid = d.get('id') or os.path.basename(os.path.dirname(f))
            if tid not in PRIVATI and tid not in TEMI:
                TEMI.append(tid)
if SOLO:
    TEMI = [t for t in TEMI if t in SOLO]
if SITO:
    TEMI = ['sito']

INFO = r"""() => {
  const c = document.querySelector('.olo-proslider');
  if (!c) return null;
  // in cima alla pagina, o visibile appena aperta (Rovescio parte dal fondo: l'apertura sta in basso)
  const rc = c.getBoundingClientRect();
  if (rc.top + scrollY > 400 && !(rc.top < innerHeight && rc.bottom > 0)) return null;
  let cfg = {}; try { cfg = JSON.parse(c.getAttribute('data-proslider')) || {}; } catch (e) {}
  const w = c.closest('.mps-scroll-fixed-wrapper');
  return { n: c.querySelectorAll('.olo-proslider-slide').length, dur: cfg.transDuration || 800,
           corsa: w ? Math.max(0, w.offsetHeight - c.offsetHeight) : 0, cima: w ? w.getBoundingClientRect().top + scrollY : 0 };
}"""

VAI = r"""(k) => {
  const c = document.querySelector('.olo-proslider');
  const t = document.querySelectorAll('.olo-proslider-tab'), d = c.querySelectorAll('.olo-proslider-dot');
  if (t[k]) { t[k].click(); return true; }
  if (d[k]) { d[k].click(); return true; }
  return false;
}"""

JS = r"""(SPAZIO_MIN) => {
  const c = document.querySelector('.olo-proslider');
  const R = c.getBoundingClientRect();
  const visibile = el => {
    for (let e = el; e && e !== c; e = e.parentElement) {
      const cs = getComputedStyle(e);
      if (cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity) < 0.15) return false;
      const fo = /opacity\(([\d.]+)(%?)\)/.exec(cs.filter || ''); // lo scorrimento mette l'opacità su un filtro
      if (fo && parseFloat(fo[1]) / (fo[2] ? 100 : 1) < 0.15) return false;
      if (e.classList.contains('olo-proslider-slide') && !e.classList.contains('mps-active')) return false;
    }
    return true;
  };
  const trasparente = v => !v || v === 'transparent' || /rgba\([^)]*,\s*0\)$/.test(v);
  const tela = document.createElement('canvas').getContext('2d');
  const L = [];
  for (const el of c.querySelectorAll('.olo-proslider-layer')) {
    if (!visibile(el)) continue;
    const id = ([...el.classList].find(k => k.startsWith('mps-layer-')) || '').slice(10);
    const pulsante = el.querySelector(':scope > a, :scope > button, :scope > .mps-mp > a');
    let box = null;
    if (pulsante) {
      const cs = getComputedStyle(pulsante), ce = getComputedStyle(el);
      const pieno = !trasparente(cs.backgroundColor) || !trasparente(ce.backgroundColor) ||
                    parseFloat(cs.borderTopWidth) > 0 || parseFloat(ce.borderTopWidth) > 0;
      const r = (pieno && !trasparente(ce.backgroundColor)) || parseFloat(ce.borderTopWidth) > 0 ? el.getBoundingClientRect() : pulsante.getBoundingClientRect();
      if (pieno && r.width > 4 && r.height > 4) box = r;
    }
    const testi = [];
    const w = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, { acceptNode: n => n.nodeValue.trim() ? 1 : 3 });
    let n, t = '';
    while ((n = w.nextNode())) {
      const p = n.parentElement;
      if (!visibile(p)) continue;
      const col = getComputedStyle(p).color;
      if (trasparente(col)) continue;
      // il riquadro di una riga è alto quanto il carattere (ascendente + discendente), molto più delle lettere nei
      // titoli col corpo stretto (Anton): si tiene solo la fascia dove stanno davvero le lettere di questo testo
      const pcs = getComputedStyle(p);
      let s = n.nodeValue;
      if (pcs.textTransform === 'uppercase') s = s.toUpperCase();
      tela.font = pcs.fontStyle + ' ' + pcs.fontWeight + ' ' + pcs.fontSize + ' ' + pcs.fontFamily;
      const m = tela.measureText(s.trim());
      const fa = m.fontBoundingBoxAscent, fd = m.fontBoundingBoxDescent;
      const rg = document.createRange(); rg.selectNodeContents(n);
      for (const q of rg.getClientRects()) {
        if (q.width <= 2 || q.height <= 5) continue;
        if (!(fa + fd > 0)) { testi.push(q); continue; }
        const k = q.height / (fa + fd);
        const top = q.top + Math.max(0, fa - m.actualBoundingBoxAscent) * k, bottom = q.top + (fa + m.actualBoundingBoxDescent) * k;
        testi.push({ left: q.left, right: q.right, width: q.width, top, bottom, height: Math.max(1, bottom - top) });
      }
      if (t.length < 36) t += (t ? ' ' : '') + n.nodeValue.trim().replace(/\s+/g, ' ');
    }
    if (!box && !testi.length) continue;
    L.push({ id, el, box, testi, t: t.slice(0, 36), fusione: el.classList.contains('mps-blend-layer') });
  }
  const inter = (p, q) => [Math.min(p.right, q.right) - Math.max(p.left, q.left), Math.min(p.bottom, q.bottom) - Math.max(p.top, q.top)];
  const out = [];
  for (let i = 0; i < L.length; i++) for (let j = i + 1; j < L.length; j++) {
    const a = L[i], b = L[j];
    if (a.t && a.t === b.t) continue; // copie dello stesso testo (le tre lastre di Fuori Registro) stanno sovrapposte apposta
    if (a.fusione && b.fusione) continue; // due livelli in fusione si mescolano apposta (lettere sfalsate, colori che si sommano)
    if (a.box && b.box) {
      const [ix, iy] = inter(a.box, b.box);
      if (iy > 0.5 * Math.min(a.box.height, b.box.height) && ix > -SPAZIO_MIN) {
        out.push({ tipo: ix > 0 ? 'pulsanti accavallati' : 'pulsanti attaccati', a: a.id + ' «' + a.t + '»', b: b.id + ' «' + b.t + '»', px: Math.round(ix) });
        continue;
      }
    }
    let best = 0;
    const scatole = (x, y) => {
      for (const p of x.testi) for (const q of (y.box ? [y.box] : y.testi)) {
        const [ix, iy] = inter(p, q);
        if (ix <= 2 || iy <= 2) continue;
        const f = (ix / Math.min(p.width, q.width)) * (iy / Math.min(p.height, q.height));
        if (f > best) best = f;
      }
    };
    scatole(a, b); scatole(b, a);
    if (best > 0.15) out.push({ tipo: (a.box || b.box) ? 'testo sul pulsante' : 'testi sovrapposti', a: a.id + ' «' + a.t + '»', b: b.id + ' «' + b.t + '»', px: Math.round(best * 100) });
  }
  for (const a of L) {
    const r = a.box || (a.testi[0] ? a.testi.reduce((m, q) => ({ left: Math.min(m.left, q.left), right: Math.max(m.right, q.right), bottom: Math.max(m.bottom, q.bottom) }), { left: 1e9, right: -1e9, bottom: -1e9 }) : null);
    if (r && (r.right > R.right + 3 || r.left < R.left - 3)) out.push({ tipo: 'fuori dallo slider', a: a.id + ' «' + a.t + '»', b: '', px: Math.round(Math.max(r.right - R.right, R.left - r.left)) });
  }
  return { casi: out, box: { y: R.top + scrollY, h: R.height } };
}"""


async def uno(browser, sem, tid, w, risultati):
    async with sem:
        h = {390: 844, 820: 1180, 1024: 768}.get(w, 900)
        ctx = await browser.new_context(viewport={'width': w, 'height': h}, is_mobile=w < 700, has_touch=w < 700)
        pg = await ctx.new_page()
        visti = set()
        try:
            await pg.goto(SITO if tid == 'sito' else BASE + tid + '/', wait_until='load', timeout=120000)
            await pg.wait_for_timeout(4000)
            info = await pg.evaluate(INFO)
            if not info:
                risultati.append({'tema': tid, 'w': w, 'senza_slider': True})
                return

            async def controlla(dove):
                r = await pg.evaluate(JS, SPAZIO_MIN)
                nuovi = [c for c in r['casi'] if (c['tipo'], c['a'], c['b']) not in visti]
                for c in nuovi:
                    visti.add((c['tipo'], c['a'], c['b']))
                if nuovi:
                    nome = '%s-%d-%s.jpg' % (tid, w, dove)
                    try:
                        await pg.screenshot(path=os.path.join(CARTELLA, nome), type='jpeg', quality=70)
                    except Exception:  # noqa: BLE001
                        nome = ''
                    risultati.append({'tema': tid, 'w': w, 'dove': dove, 'casi': nuovi, 'img': nome})

            if info['corsa'] > 0:
                # la timeline a scorrimento: si scende a piccoli passi (gli effetti seguono lo scorrimento) e si guarda
                # a ogni decimo del tratto, fino in fondo
                passi = 10
                for k in range(passi + 1):
                    meta = info['cima'] + info['corsa'] * k / passi
                    y0 = await pg.evaluate('scrollY')
                    for y in range(int(y0), int(meta) + 1, 120):
                        await pg.evaluate('(y) => scrollTo(0, y)', y)
                        await pg.wait_for_timeout(25)
                    await pg.evaluate('(y) => scrollTo(0, y)', meta)
                    await pg.wait_for_timeout(700)
                    await controlla('p%d' % (k * 10))
            else:
                for k in range(info['n']):
                    if k > 0:
                        if not await pg.evaluate(VAI, k):
                            break
                        await pg.wait_for_timeout(info['dur'] + 3400)
                    await controlla('s%d' % (k + 1))
            risultati.append({'tema': tid, 'w': w, 'fatto': True})
        except Exception as e:  # noqa: BLE001
            risultati.append({'tema': tid, 'w': w, 'errore': str(e)[:200]})
        finally:
            await ctx.close()


async def main():
    risultati = []
    sem = asyncio.Semaphore(6)
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        await asyncio.gather(*[uno(browser, sem, tid, w, risultati) for tid in TEMI for w in LARGHE])
        await browser.close()
    json.dump(risultati, open(OUT, 'w', encoding='utf-8'), ensure_ascii=False, indent=1)
    con = [r for r in risultati if r.get('casi')]
    print(len(TEMI), 'temi ·', sum(1 for r in risultati if r.get('senza_slider')) // max(1, len(LARGHE)), 'senza slider ·',
          sum(len(r['casi']) for r in con), 'casi ·', sum(1 for r in risultati if 'errore' in r), 'errori')
    for r in risultati:
        if 'errore' in r:
            print('ERRORE', r['tema'], r['w'], r['errore'])
    for r in sorted(con, key=lambda r: (r['tema'], r['w'], r['dove'])):
        for c in r['casi']:
            print('%-14s %4d %-4s %-21s %4d  %s  ×  %s' % (r['tema'], r['w'], r['dove'], c['tipo'], c['px'], c['a'], c['b']))


asyncio.run(main())
