// Livelli del Pro Slider con parole che si accavallano, slide per slide, a più larghezze della finestra.
// Uso in una pagina del sottosito: await accavallati(['luce-rossa','anima'], [1440,1280,1180,1100,1024,960,900,820,768,600,390])
async function accavallati(temi, larghezze) {
  const dorme = (ms) => new Promise((r) => setTimeout(r, ms));
  const out = {};
  for (const tema of temi) {
    for (const w of larghezze) {
      const f = document.createElement('iframe');
      f.style.cssText = `position:fixed;left:0;top:0;width:${w}px;height:900px;opacity:0.01;z-index:-1;pointer-events:none;border:0`;
      f.src = '/olobuild/demo/' + tema + '/?misura=' + Date.now();
      document.body.appendChild(f);
      await new Promise((r) => { f.onload = r; setTimeout(r, 20000); });
      await dorme(3500);
      const d = f.contentDocument;
      const righe = [];
      const sliders = [...d.querySelectorAll('.olo-proslider')];
      for (let si = 0; si < sliders.length; si++) {
        const sl = sliders[si];
        sl.scrollIntoView({ block: 'center' });
        await dorme(400);
        const slides = [...sl.querySelectorAll('.olo-proslider-slide')];
        for (let k = 0; k < slides.length; k++) {
          if (k > 0) {
            const dots = sl.querySelectorAll('.olo-proslider-dot');
            if (dots[k]) dots[k].click(); else sl.querySelector('.olo-proslider-next')?.click();
            await dorme(3200);
          }
          const attiva = sl.querySelector('.olo-proslider-slide.mps-active') || slides[k];
          const ls = [];
          [...attiva.querySelectorAll('.olo-proslider-layer'), ...sl.querySelectorAll('.olo-proslider-global-layers .olo-proslider-layer')].forEach((l) => {
            const cs = d.defaultView.getComputedStyle(l);
            if (cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity) < 0.05) return;
            const testo = (l.innerText || '').trim();
            if (!testo) return;
            const rng = d.createRange(); rng.selectNodeContents(l);
            const rr = [...rng.getClientRects()].filter((q) => q.width > 1 && q.height > 1);
            if (rr.length) ls.push({ id: (l.className.match(/mps-layer-([\w-]+)/) || [])[1], t: testo.replace(/\s+/g, ' ').slice(0, 26), rr });
          });
          for (let i = 0; i < ls.length; i++) for (let j = i + 1; j < ls.length; j++) {
            let max = 0;
            for (const a of ls[i].rr) for (const b of ls[j].rr) {
              const ox = Math.min(a.right, b.right) - Math.max(a.left, b.left);
              const oy = Math.min(a.bottom, b.bottom) - Math.max(a.top, b.top);
              if (ox > 2 && oy > 2) max = Math.max(max, Math.round(Math.min(ox, oy)));
            }
            if (max > 2) righe.push(`slider ${si + 1} slide ${k + 1}: «${ls[i].t}» (${ls[i].id}) ⟷ «${ls[j].t}» (${ls[j].id}) ${max}px`);
          }
        }
      }
      if (righe.length) out[tema + ' @' + w] = righe;
      f.remove();
    }
  }
  return out;
}
