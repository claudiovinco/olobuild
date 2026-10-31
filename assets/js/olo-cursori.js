/**
 * OLObuild — cursori che seguono il mouse (31 ott 2026). Gli stili del cursore globale oltre all'anello classico
 * (che resta in Olobuild_Magnetic_Cursor): scia, mirino, goccia, inverti, etichetta, cometa, aggancio, bussola,
 * scintille, alone. La configurazione arriva da window.OLO_CURSORE (stampata da Olobuild_Magnetic_Cursor).
 *
 * Regole comuni: niente su touch o puntatore grossolano; con «meno movimento» niente scie, particelle né rincorse
 * (il cursore sta sul puntatore); il focus da tastiera non si nasconde mai; i colori passano come proprietà CSS
 * (--oc-*), non scritti nel CSS; un solo ciclo d'animazione, fermo quando tutto è arrivato.
 */
(function () {
  var CFG = window.OLO_CURSORE;
  if (!CFG || window.__oloCursore) return;
  var mm = window.matchMedia ? function (q) { return window.matchMedia(q).matches; } : function () { return false; };
  if (mm('(hover: none)') || mm('(pointer: coarse)')) return;
  window.__oloCursore = true;
  var meno = mm('(prefers-reduced-motion: reduce)');
  var html = document.documentElement;
  var P = 'olo-cur';
  var STILE = CFG.style || 'scia';

  /* ── CSS (le misure dai numeri della configurazione, i colori dalle proprietà) ── */
  var R = +CFG.ring || 34, D = +CFG.dot || 6, Z = +CFG.z || 100000;
  var css = '.' + P + '{position:fixed;top:0;left:0;pointer-events:none;z-index:' + Z + ';will-change:transform;contain:layout style}'
    + 'html.' + P + '-fuori .' + P + '{opacity:0!important}'
    + '.' + P + '-punto{width:' + D + 'px;height:' + D + 'px;margin:' + (-D / 2) + 'px 0 0 ' + (-D / 2) + 'px;border-radius:50%;background:var(--oc-dot);transition:opacity .2s}'
    + '.' + P + '-anello{width:' + R + 'px;height:' + R + 'px;border:var(--oc-w,1.5px) solid var(--oc-ring);border-radius:50%;box-sizing:border-box;'
    + 'transition:border-color .2s,background .2s,border-radius .25s}'
    + '.' + P + '-anello.is-hot{border-color:var(--oc-hot);background:var(--oc-fill)}';
  if (CFG.hideSys) {
    css += 'html.' + P + '-on,html.' + P + '-on a,html.' + P + '-on button,html.' + P + '-on [role="button"]{cursor:none}';
  }
  var st = document.createElement('style');
  st.textContent = css;
  document.head.appendChild(st);
  html.style.setProperty('--oc-ring', CFG.ringColor || '#ffffff');
  html.style.setProperty('--oc-dot', CFG.dotColor || '#ff5a36');
  html.style.setProperty('--oc-hot', CFG.hotColor || CFG.ringColor || '#ffffff');
  html.style.setProperty('--oc-fill', CFG.hotFill || 'transparent');
  html.style.setProperty('--oc-label', CFG.labelColor || '#ffffff');
  html.style.setProperty('--oc-w', (+CFG.ringW || 1.5) + 'px');

  /* ── utilità ── */
  function nodo(cls, tag) {
    var e = document.createElement(tag || 'div');
    e.className = P + ' ' + P + '-' + cls;
    e.setAttribute('aria-hidden', 'true');
    document.body.appendChild(e);
    return e;
  }
  function sposta(e, x, y, dopo) { e.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)' + (dopo || ''); }
  // un colore della configurazione (anche var() o color-mix) come colore che il canvas capisce
  function risolvi(c) {
    var t = document.createElement('span');
    t.style.color = c;
    document.body.appendChild(t);
    var v = getComputedStyle(t).color;
    t.remove();
    return v;
  }
  var HOT = 'a, button, [role="button"], input, select, textarea, label, summary' + (CFG.sel ? ', ' + CFG.sel : '');

  /* ── stato del puntatore ── */
  var mx = -200, my = -200, vx = 0, vy = 0, hot = null, ultimo = 0;
  var giro = null;
  var stile = null;

  function ciclo(t) {
    giro = null;
    var ancora = stile.frame(t || performance.now());
    if (ancora) giro = requestAnimationFrame(ciclo);
  }
  function chiedi() { if (!giro) giro = requestAnimationFrame(ciclo); }

  /* ── gli stili ── */
  var STILI = {};

  // Scia: una catena di punti, ognuno insegue quello davanti; il primo cresce sugli elementi interattivi.
  STILI.scia = function () {
    var N = 12, punti = [], pos = [];
    for (var i = 0; i < N; i++) {
      var e = nodo('punto');
      var k = 1 - i / N;
      e.style.opacity = String(0.15 + 0.85 * k);
      e.style.width = e.style.height = Math.max(2, D * (0.5 + 0.9 * k)) + 'px';
      e.style.margin = (-Math.max(2, D * (0.5 + 0.9 * k)) / 2) + 'px 0 0 ' + (-Math.max(2, D * (0.5 + 0.9 * k)) / 2) + 'px';
      e.style.background = 'var(--oc-ring)';
      punti.push(e);
      pos.push({ x: mx, y: my });
    }
    punti[0].style.background = 'var(--oc-dot)';
    punti[0].style.transition = 'transform .05s';
    return {
      frame: function () {
        var mosso = false;
        for (var i = 0; i < N; i++) {
          var tx = i ? pos[i - 1].x : mx, ty = i ? pos[i - 1].y : my;
          var f = meno ? 1 : (i ? 0.42 : 0.6);
          pos[i].x += (tx - pos[i].x) * f;
          pos[i].y += (ty - pos[i].y) * f;
          if (Math.abs(tx - pos[i].x) + Math.abs(ty - pos[i].y) > 0.2) mosso = true;
          sposta(punti[i], pos[i].x, pos[i].y, i === 0 && hot ? ' scale(2.6)' : '');
          if (meno && i) punti[i].style.opacity = '0';
        }
        return mosso;
      }
    };
  };

  // Mirino: due fili che attraversano lo schermo, un quadrato al centro e le coordinate.
  STILI.mirino = function () {
    var oriz = nodo('filo'), vert = nodo('filo'), q = nodo('quadro'), coord = nodo('coord');
    oriz.style.cssText += ';width:100vw;height:1px;background:color-mix(in srgb,var(--oc-ring) 32%,transparent)';
    vert.style.cssText += ';width:1px;height:100vh;background:color-mix(in srgb,var(--oc-ring) 32%,transparent)';
    q.style.cssText += ';width:14px;height:14px;margin:-7px 0 0 -7px;border:1px solid var(--oc-ring);transition:border-color .2s';
    coord.style.cssText += ';font:500 10px/1 ui-monospace,Menlo,monospace;letter-spacing:.08em;color:color-mix(in srgb,var(--oc-ring) 75%,transparent);white-space:nowrap';
    var qx = mx, qy = my, ang = 0, scala = 1;
    return {
      frame: function () {
        sposta(oriz, 0, my);
        sposta(vert, mx, 0);
        qx += (mx - qx) * (meno ? 1 : 0.35);
        qy += (my - qy) * (meno ? 1 : 0.35);
        var aT = hot ? 45 : 0, sT = hot ? 1.8 : 1;
        ang += (aT - ang) * (meno ? 1 : 0.2);
        scala += (sT - scala) * (meno ? 1 : 0.2);
        q.style.borderColor = hot ? 'var(--oc-hot)' : 'var(--oc-ring)';
        sposta(q, qx, qy, ' rotate(' + ang.toFixed(1) + 'deg) scale(' + scala.toFixed(3) + ')');
        coord.textContent = 'X ' + ('000' + Math.round(mx)).slice(-4) + '  ·  Y ' + ('000' + Math.round(my)).slice(-4);
        sposta(coord, mx + 14, my + 14);
        return Math.abs(mx - qx) + Math.abs(my - qy) > 0.2 || Math.abs(aT - ang) > 0.2 || Math.abs(sT - scala) > 0.005;
      }
    };
  };

  // Goccia: un tondo pieno che si allunga nella direzione del movimento, come una goccia che corre.
  STILI.goccia = function () {
    var g = nodo('goccia');
    var S = R * 0.8;
    g.style.cssText += ';width:' + S + 'px;height:' + S + 'px;margin:' + (-S / 2) + 'px 0 0 ' + (-S / 2) + 'px;border-radius:50%;'
      + 'background:var(--oc-dot);mix-blend-mode:' + (CFG.blend || 'normal') + ';transition:background .25s,opacity .25s';
    var x = mx, y = my, sc = 1, st = 0, ang = 0;
    return {
      frame: function () {
        var dx = (mx - x) * (meno ? 1 : 0.22), dy = (my - y) * (meno ? 1 : 0.22);
        x += dx; y += dy;
        var v = Math.sqrt(dx * dx + dy * dy);
        var sT = meno ? 0 : Math.min(v / 18, 0.75);
        st += (sT - st) * 0.3;
        if (v > 0.4) ang = Math.atan2(dy, dx) * 180 / Math.PI;
        var scT = hot ? 1.9 : 1;
        sc += (scT - sc) * 0.18;
        g.style.background = hot ? 'var(--oc-hot)' : 'var(--oc-dot)';
        g.style.opacity = hot ? '0.85' : '1';
        sposta(g, x, y, ' rotate(' + ang.toFixed(1) + 'deg) scale(' + (sc * (1 + st)).toFixed(3) + ',' + (sc * (1 - st * 0.55)).toFixed(3) + ')');
        return v > 0.15 || st > 0.01 || Math.abs(scT - sc) > 0.005;
      }
    };
  };

  // Inverti: un disco che rovescia i colori di quello che ha sotto; si allarga sugli elementi interattivi.
  STILI.inverti = function () {
    var d = nodo('disco');
    var S = R * 1.4;
    d.style.cssText += ';width:' + S + 'px;height:' + S + 'px;margin:' + (-S / 2) + 'px 0 0 ' + (-S / 2) + 'px;border-radius:50%;background:#fff;mix-blend-mode:difference';
    var x = mx, y = my, sc = 1;
    return {
      frame: function () {
        x += (mx - x) * (meno ? 1 : 0.24);
        y += (my - y) * (meno ? 1 : 0.24);
        var scT = hot ? 2.2 : 1;
        sc += (scT - sc) * (meno ? 1 : 0.16);
        sposta(d, x, y, ' scale(' + sc.toFixed(3) + ')');
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || Math.abs(scT - sc) > 0.005;
      }
    };
  };

  // Etichetta: un punto sul puntatore e, sugli elementi interattivi, una pillola con una parola (data-cursor
  // dell'elemento, o quella della configurazione) che lo insegue.
  STILI.etichetta = function () {
    var p = nodo('punto'), e = nodo('etichetta');
    e.style.cssText += ';padding:9px 14px;border-radius:999px;background:var(--oc-dot);color:var(--oc-label);'
      + 'font:600 11px/1 system-ui,-apple-system,"Segoe UI",sans-serif;letter-spacing:.14em;text-transform:uppercase;white-space:nowrap;'
      + 'transform-origin:0 0;transition:opacity .2s';
    var x = mx, y = my, sc = 0;
    return {
      frame: function () {
        sposta(p, mx, my);
        x += (mx - x) * (meno ? 1 : 0.2);
        y += (my - y) * (meno ? 1 : 0.2);
        var scT = hot ? 1 : 0;
        sc += (scT - sc) * (meno ? 1 : 0.22);
        if (hot) e.textContent = (hot.getAttribute('data-cursor') || CFG.label || 'Apri').slice(0, 24);
        e.style.opacity = sc > 0.05 ? '1' : '0';
        p.style.opacity = hot ? '0' : '1';
        sposta(e, x + 16, y + 16, ' scale(' + sc.toFixed(3) + ')');
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || Math.abs(scT - sc) > 0.005;
      }
    };
  };

  // Una tela a tutto schermo per gli stili che disegnano (cometa, scintille).
  function tela() {
    var c = nodo('tela', 'canvas');
    var dpr = Math.min(2, window.devicePixelRatio || 1);
    function misura() {
      c.width = innerWidth * dpr; c.height = innerHeight * dpr;
      c.style.width = innerWidth + 'px'; c.style.height = innerHeight + 'px';
    }
    misura();
    window.addEventListener('resize', misura);
    var g = c.getContext('2d');
    return { c: c, g: g, dpr: dpr };
  }

  // Cometa: il puntatore lascia una scia di luce che si assottiglia e svanisce in mezzo secondo.
  STILI.cometa = function () {
    var T = tela(), testa = nodo('punto'), pts = [];
    var col = risolvi(CFG.dotColor || '#fff'), colHot = risolvi(CFG.hotColor || CFG.dotColor || '#fff');
    var VITA = 420;
    return {
      punto: function (t) { if (!meno) pts.push({ x: mx, y: my, t: t }); },
      frame: function (t) {
        sposta(testa, mx, my);
        var g = T.g, dpr = T.dpr;
        g.setTransform(dpr, 0, 0, dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        while (pts.length && t - pts[0].t > VITA) pts.shift();
        g.lineCap = 'round';
        g.strokeStyle = hot ? colHot : col;
        for (var i = 1; i < pts.length; i++) {
          var k = 1 - (t - pts[i].t) / VITA;
          g.globalAlpha = Math.max(0, k * 0.9);
          g.lineWidth = Math.max(0.5, D * 1.3 * k * (hot ? 1.5 : 1));
          g.beginPath();
          g.moveTo(pts[i - 1].x, pts[i - 1].y);
          g.lineTo(pts[i].x, pts[i].y);
          g.stroke();
        }
        g.globalAlpha = 1;
        return pts.length > 1;
      }
    };
  };

  // Aggancio: l'anello segue il puntatore e, sopra un pulsante o un link, lo abbraccia prendendone forma e misura.
  STILI.aggancio = function () {
    var a = nodo('anello'), p = nodo('punto');
    a.style.margin = '0';
    a.style.transition = 'border-color .2s,background .2s';
    var x = mx, y = my, w = R, h = R, rad = R / 2;
    return {
      frame: function () {
        sposta(p, mx, my);
        var tx = mx, ty = my, tw = R, th = R, tr = R / 2;
        if (hot) {
          var r = hot.getBoundingClientRect();
          if (r.width < 640 && r.height < 220) {
            tx = r.left + r.width / 2; ty = r.top + r.height / 2; tw = r.width + 14; th = r.height + 14;
            tr = Math.min(th / 2, (parseFloat(getComputedStyle(hot).borderTopLeftRadius) || 6) + 7);
          }
        }
        var f = meno ? 1 : 0.22;
        x += (tx - x) * f; y += (ty - y) * f; w += (tw - w) * f; h += (th - h) * f; rad += (tr - rad) * f;
        a.style.width = w + 'px'; a.style.height = h + 'px'; a.style.borderRadius = rad + 'px';
        // sul pulsante l'anello resta del suo colore: quello «hot» è spesso il colore del pulsante e sparirebbe
        p.style.opacity = hot ? '0' : '1';
        sposta(a, x - w / 2, y - h / 2);
        return Math.abs(tx - x) + Math.abs(ty - y) + Math.abs(tw - w) + Math.abs(th - h) > 0.3;
      }
    };
  };

  // Bussola: un anello con una freccia che punta dove va il mouse, e torna lenta verso l'alto quando si ferma.
  STILI.bussola = function () {
    var a = nodo('anello'), f = nodo('freccia');
    a.style.margin = (-R / 2) + 'px 0 0 ' + (-R / 2) + 'px';
    var S = R * 0.55;
    f.style.cssText += ';width:' + S + 'px;height:' + S + 'px;margin:' + (-S / 2) + 'px 0 0 ' + (-S / 2) + 'px';
    f.innerHTML = '<svg viewBox="0 0 24 24" width="100%" height="100%"><path d="M12 2 L18 20 L12 15.5 L6 20 Z" fill="var(--oc-dot)"/></svg>';
    var x = mx, y = my, ang = 0, angT = 0, sc = 1, fermo = 0;
    return {
      frame: function (t) {
        var dx = (mx - x) * (meno ? 1 : 0.2), dy = (my - y) * (meno ? 1 : 0.2);
        x += dx; y += dy;
        if (Math.sqrt(dx * dx + dy * dy) > 0.6) { angT = Math.atan2(dy, dx) * 180 / Math.PI + 90; fermo = t; }
        else if (t - fermo > 900) { angT = 0; }
        var d = ((angT - ang + 540) % 360) - 180;
        ang += d * (meno ? 1 : 0.15);
        var scT = hot ? (+CFG.hotScale || 1.6) : 1;
        sc += (scT - sc) * 0.2;
        a.classList.toggle('is-hot', !!hot);
        sposta(a, x, y, ' scale(' + sc.toFixed(3) + ')');
        sposta(f, x, y, ' rotate(' + ang.toFixed(1) + 'deg)');
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || Math.abs(d) > 0.3 || Math.abs(scT - sc) > 0.005 || t - fermo < 1000;
      }
    };
  };

  // Scintille: muovendosi il puntatore sparge scintille che cadono e si spengono; un clic ne fa uno scoppio.
  STILI.scintille = function () {
    var T = tela(), p = nodo('punto'), sc = [];
    var cols = [risolvi(CFG.dotColor || '#fff'), risolvi(CFG.hotColor || '#fff'), risolvi(CFG.ringColor || '#fff')];
    function sparge(n, forza) {
      if (meno) return;
      for (var i = 0; i < n; i++) {
        var a = Math.random() * Math.PI * 2, v = (0.5 + Math.random()) * forza;
        sc.push({ x: mx, y: my, vx: Math.cos(a) * v + vx * 0.08, vy: Math.sin(a) * v + vy * 0.08 - 0.6, t: 0,
                  vita: 500 + Math.random() * 450, r: 1 + Math.random() * 2.2, c: cols[i % 3] });
      }
      if (sc.length > 240) sc.splice(0, sc.length - 240);
    }
    document.addEventListener('pointerdown', function (e) { if (e.pointerType !== 'touch') { sparge(26, 3.2); chiedi(); } });
    var prima = 0;
    return {
      punto: function () { sparge(Math.min(3, 1 + Math.floor(Math.sqrt(vx * vx + vy * vy) / 14)), 1.1); },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my);
        var g = T.g, dpr = T.dpr;
        g.setTransform(dpr, 0, 0, dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = sc.length - 1; i >= 0; i--) {
          var s = sc[i];
          s.t += dt;
          if (s.t > s.vita) { sc.splice(i, 1); continue; }
          s.vy += 0.045 * dt / 16; s.x += s.vx * dt / 16; s.y += s.vy * dt / 16;
          g.globalAlpha = 1 - s.t / s.vita;
          g.fillStyle = s.c;
          g.beginPath(); g.arc(s.x, s.y, s.r, 0, 6.283); g.fill();
        }
        g.globalAlpha = 1;
        if (!sc.length) prima = 0;
        return sc.length > 0;
      }
    };
  };

  // Alone: una luce morbida e grande che insegue piano il puntatore, più viva sugli elementi interattivi.
  STILI.alone = function () {
    var a = nodo('alone'), p = nodo('punto');
    var S = R * 7;
    a.style.cssText += ';width:' + S + 'px;height:' + S + 'px;margin:' + (-S / 2) + 'px 0 0 ' + (-S / 2) + 'px;border-radius:50%;'
      + 'background:radial-gradient(circle,color-mix(in srgb,var(--oc-ring) 30%,transparent) 0%,transparent 62%);'
      + 'mix-blend-mode:' + (CFG.blend === 'normal' ? 'screen' : (CFG.blend || 'screen')) + ';transition:opacity .4s';
    var x = mx, y = my, sc = 1;
    return {
      frame: function () {
        sposta(p, mx, my);
        x += (mx - x) * (meno ? 1 : 0.07);
        y += (my - y) * (meno ? 1 : 0.07);
        var scT = hot ? 1.25 : 1;
        sc += (scT - sc) * 0.08;
        a.style.opacity = hot ? '1' : '0.75';
        sposta(a, x, y, ' scale(' + sc.toFixed(3) + ')');
        return Math.abs(mx - x) + Math.abs(my - y) > 0.3 || Math.abs(scT - sc) > 0.005;
      }
    };
  };

  /* ── avvio ── */
  function avvia() {
    if (!document.body || stile) return;
    stile = (STILI[STILE] || STILI.scia)();
    if (CFG.hideSys) html.classList.add(P + '-on');
    html.classList.add(P + '-fuori');
    window.addEventListener('pointermove', function (e) {
      if (e.pointerType === 'touch') return;
      var t = performance.now();
      var dt = Math.max(8, t - ultimo);
      vx = (e.clientX - mx) / dt * 16; vy = (e.clientY - my) / dt * 16;
      if (html.classList.contains(P + '-fuori')) { vx = vy = 0; }
      mx = e.clientX; my = e.clientY; ultimo = t;
      html.classList.remove(P + '-fuori');
      if (stile.punto) stile.punto(t);
      chiedi();
    }, { passive: true });
    document.addEventListener('pointerover', function (e) {
      var h = e.target && e.target.closest ? e.target.closest(HOT) : null;
      if (h !== hot) { hot = h; chiedi(); }
    }, { passive: true });
    document.addEventListener('pointerout', function (e) {
      if (!e.relatedTarget) { hot = null; chiedi(); }
    }, { passive: true });
    document.addEventListener('mouseleave', function () { html.classList.add(P + '-fuori'); });
    document.addEventListener('mouseenter', function () { html.classList.remove(P + '-fuori'); });
  }
  if (document.body) avvia(); else document.addEventListener('DOMContentLoaded', avvia);
})();
