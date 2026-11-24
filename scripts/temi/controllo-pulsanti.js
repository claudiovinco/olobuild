/*
 * Controllo dei pulsanti dello Slider Pro nelle demo dei temi (24 nov 2026): due pulsanti o link della stessa slide che
 * si accavallano o restano a meno di 12 px. Nato da Torchio, dove il riquadro rosso di «La stamperia» copriva il bordo di
 * «Sfoglia il campionario». Dal 1.4.1415 il motore dello Slider Pro (distanziaPulsanti in olo-proslider.js) li distanzia
 * da sé sulla pagina; questo controllo misura le POSIZIONI DEL PROGETTO (spegne quella regola), così un tema nuovo nasce
 * giusto anche nell'editor dello Slider Pro.
 *
 * Uso: aprire una pagina del sottosito (https://olotheme.com/olobuild/…) nel browser, incollare questo file nella console,
 * poi:   await controllaPulsanti(['torchio', 'strappo'])            → computer (1440)
 *        await controllaPulsanti(['torchio'], 820, 1180)             → tablet
 *        await controllaPulsanti(['torchio'], 390, 844)              → telefono
 * Deve rispondere {} (nessun problema). Ogni voce: «id↔id» in fila, «id↕id» in pila, con lo spazio o l'accavallamento.
 * La cornice sta dentro lo schermo (invisibile): le tile pigre si caricano solo se visibili.
 */
async function misuraPulsanti(tema, larg, alt) {
  const f = document.createElement('iframe');
  f.style.cssText = `position:fixed;left:0;top:0;width:${larg}px;height:${alt}px;opacity:0.01;z-index:-1;pointer-events:none;border:0`;
  f.src = '/olobuild/demo/' + tema + '/?controllo-pulsanti=1';
  document.body.appendChild(f);
  await new Promise((r) => { f.onload = r; setTimeout(r, 20000); });
  await new Promise((r) => setTimeout(r, 2200));
  const d = f.contentDocument;
  const out = [];
  // Le posizioni del progetto: via gli spostamenti della regola del motore.
  d.querySelectorAll('[data-mps-sp]').forEach((el) => { el.style.marginLeft = ''; el.style.marginTop = ''; });
  d.querySelectorAll('.olo-proslider-slide, .olo-proslider-global-layers').forEach((slide) => {
    const ls = [];
    slide.querySelectorAll('.olo-proslider-layer').forEach((l) => {
      if (!l.querySelector(':scope > a, :scope > .mps-mp > a')) return;
      const cs = getComputedStyle(l);
      if (cs.display === 'none' || cs.visibility === 'hidden' && !slide.classList.contains('olo-proslider-slide')) return;
      const r = l.getBoundingClientRect();
      if (r.width < 2 || r.height < 2) return;
      ls.push({ id: (l.className.match(/mps-layer-([\w-]+)/) || [])[1], r });
    });
    for (let i = 0; i < ls.length; i++) {
      for (let j = i + 1; j < ls.length; j++) {
        const A = ls[i].r, B = ls[j].r;
        const vy = Math.min(A.bottom, B.bottom) - Math.max(A.top, B.top);
        const vx = Math.min(A.right, B.right) - Math.max(A.left, B.left);
        const testo = (v) => (v > 0 ? 'accavallati ' + Math.round(v) : 'spazio ' + Math.round(-v)) + 'px';
        if (vy > 0 && vx > -12) out.push(`${ls[i].id}↔${ls[j].id} ${testo(vx)}`);
        else if (vx > 0 && vy > -12) out.push(`${ls[i].id}↕${ls[j].id} ${testo(vy)}`);
      }
    }
  });
  f.remove();
  return out;
}

async function controllaPulsanti(temi, larg = 1440, alt = 900) {
  const problemi = {};
  for (const t of temi) {
    const r = await misuraPulsanti(t, larg, alt);
    if (r.length) problemi[t] = r;
  }
  return problemi;
}
