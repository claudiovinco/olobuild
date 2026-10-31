/**
 * OLObuild — cursori che seguono il mouse (31 ott 2026). Gli stili del cursore globale oltre all'anello classico
 * (che resta in Olobuild_Magnetic_Cursor): scia, mirino, goccia, inverti, etichetta, cometa, aggancio, bussola,
 * scintille, alone; e dal 1 nov 2026 orbita, onde, neve, cucitura, pennello, testo, lente, coriandoli. La configurazione arriva da window.OLO_CURSORE (stampata da Olobuild_Magnetic_Cursor).
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

  // Orbita: un punto insegue il puntatore e un satellite gli gira attorno; sugli elementi interattivi l'orbita si allarga.
  STILI.orbita = function () {
    var c = nodo('punto'), sat = nodo('punto'), giro = nodo('anello');
    var S = R * 1.1;
    giro.style.cssText += ';width:' + S + 'px;height:' + S + 'px;margin:' + (-S / 2) + 'px 0 0 ' + (-S / 2) + 'px;opacity:.7;border-style:dashed';
    sat.style.background = 'var(--oc-ring)';
    var x = mx, y = my, a = 0, r = S / 2, prima = 0;
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        x += (mx - x) * (meno ? 1 : 0.3); y += (my - y) * (meno ? 1 : 0.3);
        var rT = (hot ? S * 0.85 : S / 2);
        r += (rT - r) * 0.15;
        a += meno ? 0 : dt * 0.0045;
        sposta(c, mx, my);
        sposta(giro, x, y, ' scale(' + (r / (S / 2)).toFixed(3) + ')');
        sposta(sat, x + Math.cos(a) * r, y + Math.sin(a) * r);
        return !meno && performance.now() - ultimo < 4000; // gira finché il puntatore si muove (poi riparte al primo movimento)
      }
    };
  };

  // Onde: un anello leggero segue il puntatore; ogni clic apre tre onde che si allargano e svaniscono.
  STILI.onde = function () {
    var a = nodo('anello'), p = nodo('punto');
    a.style.margin = (-R / 2) + 'px 0 0 ' + (-R / 2) + 'px';
    var onde = [];
    document.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch' || meno) return;
      for (var k = 0; k < 3; k++) {
        var o = nodo('anello');
        o.style.margin = (-R / 2) + 'px 0 0 ' + (-R / 2) + 'px';
        o.style.transition = 'none';
        onde.push({ el: o, x: mx, y: my, t0: performance.now() + k * 140 });
      }
      chiedi();
    });
    var x = mx, y = my;
    return {
      frame: function (t) {
        x += (mx - x) * (meno ? 1 : 0.25); y += (my - y) * (meno ? 1 : 0.25);
        sposta(p, mx, my);
        a.classList.toggle('is-hot', !!hot);
        sposta(a, x, y, hot ? ' scale(' + (+CFG.hotScale || 1.6) + ')' : '');
        for (var i = onde.length - 1; i >= 0; i--) {
          var o = onde[i], k = (t - o.t0) / 900;
          if (k < 0) { o.el.style.opacity = '0'; continue; }
          if (k >= 1) { o.el.remove(); onde.splice(i, 1); continue; }
          o.el.style.opacity = String(1 - k);
          sposta(o.el, o.x, o.y, ' scale(' + (1 + k * 3.5).toFixed(3) + ')');
        }
        return onde.length > 0 || Math.abs(mx - x) + Math.abs(my - y) > 0.2;
      }
    };
  };

  // Neve: muovendosi il puntatore lascia cadere fiocchi che scendono ondeggiando e si sciolgono.
  STILI.neve = function () {
    var T = tela(), p = nodo('punto'), f = [];
    var col = risolvi(CFG.ringColor || '#fff');
    var prima = 0;
    return {
      punto: function () {
        if (meno) return;
        var n = Math.min(2, 1 + Math.floor(Math.sqrt(vx * vx + vy * vy) / 16));
        for (var i = 0; i < n; i++) {
          f.push({ x: mx + (Math.random() - 0.5) * 18, y: my + (Math.random() - 0.5) * 10, vy: 0.4 + Math.random() * 0.7,
                   fase: Math.random() * 6.28, r: 1.2 + Math.random() * 2.4, t: 0, vita: 1400 + Math.random() * 900 });
        }
        if (f.length > 260) f.splice(0, f.length - 260);
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my);
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.fillStyle = col;
        for (var i = f.length - 1; i >= 0; i--) {
          var s = f[i];
          s.t += dt;
          if (s.t > s.vita) { f.splice(i, 1); continue; }
          s.y += s.vy * dt / 16;
          s.x += Math.sin(s.fase + s.t / 380) * 0.45;
          g.globalAlpha = Math.min(1, (1 - s.t / s.vita) * 1.4) * 0.9;
          g.beginPath(); g.arc(s.x, s.y, s.r, 0, 6.283); g.fill();
        }
        g.globalAlpha = 1;
        if (!f.length) prima = 0;
        return f.length > 0;
      }
    };
  };

  // Cucitura: il puntatore cuce: dietro resta una fila di punti di filo tratteggiati che si sfilano in poco tempo.
  STILI.cucitura = function () {
    var T = tela(), ago = nodo('punto'), pts = [];
    var col = risolvi(CFG.dotColor || '#fff');
    var VITA = 700;
    return {
      punto: function (t) { if (!meno) pts.push({ x: mx, y: my, t: t }); },
      frame: function (t) {
        sposta(ago, mx, my);
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        while (pts.length && t - pts[0].t > VITA) pts.shift();
        if (pts.length > 1) {
          g.strokeStyle = col;
          g.lineWidth = Math.max(1.5, D * 0.35);
          g.lineCap = 'round';
          g.setLineDash([7, 6]);
          g.globalAlpha = 0.9;
          g.beginPath();
          g.moveTo(pts[0].x, pts[0].y);
          for (var i = 1; i < pts.length; i++) g.lineTo(pts[i].x, pts[i].y);
          g.stroke();
          g.setLineDash([]);
          g.globalAlpha = 1;
        }
        return pts.length > 1;
      }
    };
  };

  // Pennello: una pennellata morbida che segue il puntatore, più larga se si va piano, e svanisce in meno di un secondo.
  STILI.pennello = function () {
    var T = tela(), p = nodo('punto'), pts = [];
    var col = risolvi(CFG.dotColor || '#fff');
    var VITA = 800;
    return {
      punto: function (t) {
        if (meno) return;
        var v = Math.sqrt(vx * vx + vy * vy);
        pts.push({ x: mx, y: my, t: t, w: Math.max(6, D * 4.2 - v * 0.3) });
      },
      frame: function (t) {
        sposta(p, mx, my);
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        while (pts.length && t - pts[0].t > VITA) pts.shift();
        g.strokeStyle = col;
        g.lineCap = 'round';
        g.lineJoin = 'round';
        for (var i = 1; i < pts.length; i++) {
          var k = 1 - (t - pts[i].t) / VITA;
          g.globalAlpha = Math.max(0, k) * 0.8;
          g.lineWidth = pts[i].w * (0.5 + 0.7 * k);
          g.beginPath(); g.moveTo(pts[i - 1].x, pts[i - 1].y); g.lineTo(pts[i].x, pts[i].y); g.stroke();
        }
        g.globalAlpha = 1;
        return pts.length > 1;
      }
    };
  };

  // Testo: una scritta in cerchio gira piano attorno al puntatore (la parola della configurazione); sugli elementi
  // interattivi si allarga e gira più in fretta.
  STILI.testo = function () {
    var c = nodo('testo-giro'), p = nodo('punto');
    var S = Math.max(84, R * 2.6);
    var parola = (CFG.label || 'Scorri · scopri').toUpperCase();
    var giro = (parola + ' · ').repeat(Math.max(1, Math.round(26 / (parola.length + 3))));
    c.style.cssText += ';width:' + S + 'px;height:' + S + 'px;margin:' + (-S / 2) + 'px 0 0 ' + (-S / 2) + 'px';
    c.innerHTML = '<svg viewBox="0 0 100 100" width="100%" height="100%"><defs><path id="' + P + '-cerchio" d="M50,50 m-38,0 a38,38 0 1,1 76,0 a38,38 0 1,1 -76,0"/></defs>'
      + '<text font-size="9.5" letter-spacing="2.4" font-family="ui-monospace,Menlo,monospace" fill="var(--oc-ring)"><textPath href="#' + P + '-cerchio">'
      + giro.replace(/[<>&]/g, '') + '</textPath></text></svg>';
    var x = mx, y = my, a = 0, sc = 1, prima = 0;
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        x += (mx - x) * (meno ? 1 : 0.18); y += (my - y) * (meno ? 1 : 0.18);
        a += meno ? 0 : dt * (hot ? 0.09 : 0.03);
        var scT = hot ? 1.35 : 1;
        sc += (scT - sc) * 0.15;
        sposta(p, mx, my);
        sposta(c, x, y, ' rotate(' + (a % 360).toFixed(1) + 'deg) scale(' + sc.toFixed(3) + ')');
        return !meno && performance.now() - ultimo < 4000;
      }
    };
  };

  // Lente: un cerchio di vetro che ravviva quello che ha sotto (colori più pieni, più contrasto); sugli elementi
  // interattivi si allarga.
  STILI.lente = function () {
    var l = nodo('lente');
    var S = R * 2.2;
    l.style.cssText += ';width:' + S + 'px;height:' + S + 'px;margin:' + (-S / 2) + 'px 0 0 ' + (-S / 2) + 'px;border-radius:50%;'
      + 'border:var(--oc-w,1.5px) solid color-mix(in srgb,var(--oc-ring) 70%,transparent);'
      + 'backdrop-filter:saturate(1.8) contrast(1.15) brightness(1.08);-webkit-backdrop-filter:saturate(1.8) contrast(1.15) brightness(1.08);'
      + 'box-shadow:inset 0 0 0 6px color-mix(in srgb,var(--oc-ring) 10%,transparent),0 10px 30px -12px rgba(0,0,0,.45)';
    var x = mx, y = my, sc = 1;
    return {
      frame: function () {
        x += (mx - x) * (meno ? 1 : 0.22); y += (my - y) * (meno ? 1 : 0.22);
        var scT = hot ? 1.5 : 1;
        sc += (scT - sc) * 0.16;
        sposta(l, x, y, ' scale(' + sc.toFixed(3) + ')');
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || Math.abs(scT - sc) > 0.005;
      }
    };
  };

  // Coriandoli: un punto sul puntatore; ogni clic fa uno scoppio di coriandoli colorati che girano e cadono.
  STILI.coriandoli = function () {
    var T = tela(), p = nodo('punto'), cc = [];
    var cols = [risolvi(CFG.dotColor || '#f55'), risolvi(CFG.hotColor || '#5af'), risolvi(CFG.ringColor || '#fd5'),
                risolvi(CFG.labelColor || '#fff')];
    document.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch' || meno) return;
      for (var i = 0; i < 34; i++) {
        var a = -Math.PI / 2 + (Math.random() - 0.5) * 2.4, v = 3 + Math.random() * 4.5;
        cc.push({ x: mx, y: my, vx: Math.cos(a) * v, vy: Math.sin(a) * v, r: Math.random() * 6.28, vr: (Math.random() - 0.5) * 0.35,
                  w: 4 + Math.random() * 5, h: 2 + Math.random() * 3, c: cols[i % cols.length], t: 0, vita: 1100 + Math.random() * 700 });
      }
      chiedi();
    });
    var prima = 0;
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my);
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = cc.length - 1; i >= 0; i--) {
          var s = cc[i];
          s.t += dt;
          if (s.t > s.vita) { cc.splice(i, 1); continue; }
          s.vy += 0.16 * dt / 16; s.vx *= 0.985; s.x += s.vx * dt / 16; s.y += s.vy * dt / 16; s.r += s.vr;
          g.globalAlpha = Math.min(1, (1 - s.t / s.vita) * 2);
          g.fillStyle = s.c;
          g.setTransform(T.dpr * Math.cos(s.r), T.dpr * Math.sin(s.r), -T.dpr * Math.sin(s.r), T.dpr * Math.cos(s.r), s.x * T.dpr, s.y * T.dpr);
          g.fillRect(-s.w / 2, -s.h / 2, s.w, s.h);
        }
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.globalAlpha = 1;
        if (!cc.length) prima = 0;
        return cc.length > 0;
      }
    };
  };

  // Faro: la pagina resta in penombra e il puntatore la illumina come una torcia; sui link la luce si allarga.
  STILI.faro = function () {
    var v = nodo('faro');
    var buio = Math.max(0.2, Math.min(0.7, +CFG.shade || 0.45));
    v.style.cssText += ';inset:0;width:100vw;height:100vh;transform:none!important;transition:opacity .4s;'
      + 'background:radial-gradient(circle var(--of-r,170px) at var(--of-x,-400px) var(--of-y,-400px),transparent 0,'
      + 'transparent 35%,rgba(0,0,0,' + buio + ') 100%)';
    var p = nodo('punto'), x = mx, y = my, r = 170;
    return {
      frame: function () {
        x += (mx - x) * (meno ? 1 : 0.22);
        y += (my - y) * (meno ? 1 : 0.22);
        var rT = hot ? 240 : 170;
        r += (rT - r) * (meno ? 1 : 0.12);
        v.style.setProperty('--of-x', x.toFixed(1) + 'px');
        v.style.setProperty('--of-y', y.toFixed(1) + 'px');
        v.style.setProperty('--of-r', r.toFixed(1) + 'px');
        sposta(p, mx, my);
        return Math.abs(mx - x) + Math.abs(my - y) > 0.3 || Math.abs(rT - r) > 0.5;
      }
    };
  };

  // Brace: dal puntatore salgono scintille calde che tremano, si spengono e cambiano colore mentre salgono.
  STILI.brace = function () {
    var T = tela(), p = nodo('punto'), f = [];
    var c1 = risolvi(CFG.dotColor || '#ffb347'), c2 = risolvi(CFG.hotColor || CFG.ringColor || '#ff5a36');
    var prima = 0;
    return {
      punto: function () {
        if (meno) return;
        var n = Math.min(3, 1 + Math.floor(Math.sqrt(vx * vx + vy * vy) / 12));
        for (var i = 0; i < n; i++) {
          f.push({ x: mx + (Math.random() - 0.5) * 10, y: my, vx: (Math.random() - 0.5) * 0.8, vy: -0.6 - Math.random() * 1.4,
                   r: 1 + Math.random() * 2.2, t: 0, vita: 700 + Math.random() * 900, c: Math.random() < 0.5 ? c1 : c2 });
        }
        if (f.length > 220) f.splice(0, f.length - 220);
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2.2)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.globalCompositeOperation = 'lighter';
        for (var i = f.length - 1; i >= 0; i--) {
          var s = f[i];
          s.t += dt;
          if (s.t > s.vita) { f.splice(i, 1); continue; }
          s.vx += (Math.random() - 0.5) * 0.12;
          s.x += s.vx * dt / 16; s.y += s.vy * dt / 16;
          var k = 1 - s.t / s.vita;
          g.globalAlpha = Math.min(1, k * 1.5);
          g.fillStyle = s.c;
          g.shadowColor = s.c; g.shadowBlur = 8;
          g.beginPath(); g.arc(s.x, s.y, s.r * (0.4 + 0.6 * k), 0, 6.283); g.fill();
        }
        g.shadowBlur = 0; g.globalAlpha = 1; g.globalCompositeOperation = 'source-over';
        if (!f.length) prima = 0;
        return f.length > 0;
      }
    };
  };

  // Inchiostro: dove passa il puntatore cadono gocce che si allargano e sfumano, come inchiostro nell'acqua.
  STILI.inchiostro = function () {
    var T = tela(), p = nodo('punto'), f = [], ultimoP = 0;
    var col = risolvi(CFG.dotColor || '#1a1a1a');
    var prima = 0;
    return {
      punto: function (t) {
        if (meno || t - ultimoP < 45) return;
        ultimoP = t;
        var v = Math.sqrt(vx * vx + vy * vy);
        f.push({ x: mx, y: my, r0: 3 + Math.min(10, v * 0.5), r1: 22 + Math.random() * 26 + Math.min(30, v * 1.4), t: 0,
                 vita: 1500 + Math.random() * 700 });
        if (f.length > 80) f.splice(0, f.length - 80);
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2.4)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.fillStyle = col;
        for (var i = f.length - 1; i >= 0; i--) {
          var s = f[i];
          s.t += dt;
          if (s.t > s.vita) { f.splice(i, 1); continue; }
          var k = s.t / s.vita, e = 1 - Math.pow(1 - k, 3);
          g.globalAlpha = 0.16 * (1 - k);
          g.beginPath(); g.arc(s.x, s.y, s.r0 + (s.r1 - s.r0) * e, 0, 6.283); g.fill();
          g.globalAlpha = 0.28 * (1 - k);
          g.beginPath(); g.arc(s.x, s.y, (s.r0 + (s.r1 - s.r0) * e) * 0.45, 0, 6.283); g.fill();
        }
        g.globalAlpha = 1;
        if (!f.length) prima = 0;
        return f.length > 0;
      }
    };
  };

  // Bolle: dal puntatore partono bolle trasparenti che salgono ondeggiando e scoppiano in un anello.
  STILI.bolle = function () {
    var T = tela(), p = nodo('punto'), f = [], ultimoP = 0;
    var col = risolvi(CFG.ringColor || '#ffffff');
    var prima = 0;
    return {
      punto: function (t) {
        if (meno || t - ultimoP < 70) return;
        ultimoP = t;
        f.push({ x: mx, y: my, r: 3 + Math.random() * 7, vy: -0.5 - Math.random() * 0.8, fase: Math.random() * 6.28, t: 0,
                 vita: 1300 + Math.random() * 1100 });
        if (f.length > 90) f.splice(0, f.length - 90);
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2.4)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.strokeStyle = col;
        for (var i = f.length - 1; i >= 0; i--) {
          var s = f[i];
          s.t += dt;
          if (s.t > s.vita + 220) { f.splice(i, 1); continue; }
          if (s.t <= s.vita) {
            s.y += s.vy * dt / 16;
            s.x += Math.sin(s.fase + s.t / 260) * 0.6;
            g.globalAlpha = 0.75; g.lineWidth = 1.4;
            g.beginPath(); g.arc(s.x, s.y, s.r, 0, 6.283); g.stroke();
            g.globalAlpha = 0.6;
            g.beginPath(); g.arc(s.x - s.r * 0.35, s.y - s.r * 0.35, s.r * 0.22, 0, 6.283); g.stroke();
          } else {
            var k = (s.t - s.vita) / 220;
            g.globalAlpha = 0.7 * (1 - k); g.lineWidth = 1;
            g.beginPath(); g.arc(s.x, s.y, s.r * (1 + k * 1.6), 0, 6.283); g.stroke();
          }
        }
        g.globalAlpha = 1;
        if (!f.length) prima = 0;
        return f.length > 0;
      }
    };
  };

  // Foglie: muovendo il puntatore si staccano foglioline che cadono girando su se stesse e oscillando.
  STILI.foglie = function () {
    var T = tela(), p = nodo('punto'), f = [], ultimoP = 0;
    var cols = [risolvi(CFG.dotColor || '#3f7a3a'), risolvi(CFG.ringColor || '#7fb069'), risolvi(CFG.hotColor || '#c9a227')];
    var prima = 0;
    return {
      punto: function (t) {
        if (meno || t - ultimoP < 90) return;
        ultimoP = t;
        f.push({ x: mx, y: my, vx: vx * 0.08, vy: 0.4 + Math.random() * 0.5, a: Math.random() * 6.28, va: (Math.random() - 0.5) * 0.08,
                 fase: Math.random() * 6.28, l: 7 + Math.random() * 6, c: cols[Math.floor(Math.random() * cols.length)], t: 0,
                 vita: 2000 + Math.random() * 1200 });
        if (f.length > 70) f.splice(0, f.length - 70);
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2.4)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = f.length - 1; i >= 0; i--) {
          var s = f[i];
          s.t += dt;
          if (s.t > s.vita) { f.splice(i, 1); continue; }
          s.vx *= 0.98;
          s.x += (s.vx + Math.sin(s.fase + s.t / 420) * 0.9) * dt / 16;
          s.y += s.vy * dt / 16;
          s.a += s.va * dt / 16;
          var k = 1 - s.t / s.vita;
          g.globalAlpha = Math.min(1, k * 1.6) * 0.9;
          g.fillStyle = s.c;
          g.setTransform(T.dpr * Math.cos(s.a), T.dpr * Math.sin(s.a), -T.dpr * Math.sin(s.a), T.dpr * Math.cos(s.a), s.x * T.dpr, s.y * T.dpr);
          g.beginPath();
          g.moveTo(-s.l, 0);
          g.quadraticCurveTo(0, -s.l * 0.55, s.l, 0);
          g.quadraticCurveTo(0, s.l * 0.55, -s.l, 0);
          g.fill();
        }
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.globalAlpha = 1;
        if (!f.length) prima = 0;
        return f.length > 0;
      }
    };
  };

  // Vapore: dal puntatore salgono sbuffi morbidi che si allargano e svaniscono, come il vapore da una tazza.
  STILI.vapore = function () {
    var T = tela(), p = nodo('punto'), f = [], ultimoP = 0;
    var col = risolvi(CFG.ringColor || '#ffffff');
    var prima = 0;
    return {
      punto: function (t) {
        if (meno || t - ultimoP < 60) return;
        ultimoP = t;
        f.push({ x: mx + (Math.random() - 0.5) * 8, y: my, r: 6 + Math.random() * 6, vy: -0.45 - Math.random() * 0.4,
                 fase: Math.random() * 6.28, t: 0, vita: 1600 + Math.random() * 900 });
        if (f.length > 90) f.splice(0, f.length - 90);
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2.4)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = f.length - 1; i >= 0; i--) {
          var s = f[i];
          s.t += dt;
          if (s.t > s.vita) { f.splice(i, 1); continue; }
          var k = s.t / s.vita;
          s.y += s.vy * dt / 16;
          s.x += Math.sin(s.fase + s.t / 500) * 0.35;
          var r = s.r * (1 + k * 2.6);
          var gr = g.createRadialGradient(s.x, s.y, 0, s.x, s.y, r);
          gr.addColorStop(0, col);
          gr.addColorStop(1, 'rgba(255,255,255,0)');
          g.globalAlpha = 0.22 * Math.sin(Math.PI * Math.min(1, k * 1.2));
          g.fillStyle = gr;
          g.beginPath(); g.arc(s.x, s.y, r, 0, 6.283); g.fill();
        }
        g.globalAlpha = 1;
        if (!f.length) prima = 0;
        return f.length > 0;
      }
    };
  };

  // Calamita: un anello col punto; avvicinandosi a un pulsante o a un link, l'elemento si sposta verso il puntatore
  // (fino a un terzo della distanza) e torna al suo posto quando lo si lascia.
  STILI.calamita = function () {
    var a = nodo('anello'), p = nodo('punto'), ax = mx, ay = my, preso = null;
    function lascia() {
      if (preso) { preso.style.translate = ''; preso.style.transition = 'translate .45s cubic-bezier(.2,.8,.2,1)'; preso = null; }
    }
    return {
      frame: function () {
        var bersaglio = hot && hot.closest ? hot.closest('a, button, [role="button"]') : null;
        if (bersaglio !== preso) { lascia(); preso = bersaglio; if (preso) preso.style.transition = 'translate .12s linear'; }
        var tx = mx, ty = my;
        if (preso && !meno) {
          var r = preso.getBoundingClientRect();
          var cx = r.left + r.width / 2, cy = r.top + r.height / 2;
          preso.style.translate = ((mx - cx) * 0.3).toFixed(1) + 'px ' + ((my - cy) * 0.3).toFixed(1) + 'px';
          tx = cx + (mx - cx) * 0.3; ty = cy + (my - cy) * 0.3;
        }
        ax += (tx - ax) * (meno ? 1 : 0.25);
        ay += (ty - ay) * (meno ? 1 : 0.25);
        a.classList.toggle('is-hot', !!preso);
        sposta(a, ax - R / 2, ay - R / 2, preso ? ' scale(1.9)' : '');
        sposta(p, mx, my);
        return Math.abs(tx - ax) + Math.abs(ty - ay) > 0.3 || !!preso;
      }
    };
  };

  // Impronte: dietro il puntatore restano ferri di cavallo, uno a sinistra e uno a destra del cammino, che sbiadiscono.
  STILI.impronte = function () {
    var T = tela(), p = nodo('punto'), f = [], lato = 1, ux = mx, uy = my;
    var col = risolvi(CFG.dotColor || '#7a3e1d');
    var prima = 0;
    return {
      punto: function () {
        if (meno) return;
        var dx = mx - ux, dy = my - uy, d = Math.sqrt(dx * dx + dy * dy);
        if (d < 34) return;
        var a = Math.atan2(dy, dx);
        lato = -lato;
        f.push({ x: mx + Math.cos(a + Math.PI / 2) * 9 * lato, y: my + Math.sin(a + Math.PI / 2) * 9 * lato, a: a, t: 0, vita: 1800 });
        ux = mx; uy = my;
        if (f.length > 40) f.splice(0, f.length - 40);
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2.4)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.strokeStyle = col; g.lineWidth = 2.6; g.lineCap = 'round';
        for (var i = f.length - 1; i >= 0; i--) {
          var s = f[i];
          s.t += dt;
          if (s.t > s.vita) { f.splice(i, 1); continue; }
          g.globalAlpha = 0.75 * (1 - s.t / s.vita);
          g.setTransform(T.dpr * Math.cos(s.a), T.dpr * Math.sin(s.a), -T.dpr * Math.sin(s.a), T.dpr * Math.cos(s.a), s.x * T.dpr, s.y * T.dpr);
          g.beginPath(); g.arc(0, 0, 6, Math.PI * 0.62, Math.PI * 1.38, true); g.stroke();
        }
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.globalAlpha = 1;
        if (!f.length) prima = 0;
        return f.length > 0;
      }
    };
  };

  // Filo: un ago vero, inclinato, con la punta sul puntatore e la cruna forata; dalla cruna esce un filo morbido che
  // pende, ondeggia piano e segue il movimento (con un codino dall'altra parte della cruna e il nodo in fondo). L'ago
  // si inclina con la velocità; sui link si raddrizza e il filo si accorcia.
  STILI.filo = function () {
    var T = tela(), N = 16, L = 6, pts = [];
    var col = risolvi(CFG.dotColor || '#c0392b');
    var acciaio = risolvi(CFG.ringColor || '#e6e6e6');
    for (var i = 0; i < N; i++) pts.push({ x: mx, y: my + i * L, px: mx, py: my + i * L });
    var ang = 38, sc = 1, prima = null, scossa = 0;
    function disegnaAgo(g, x, y, a, s) {
      var len = 30 * s, w = 1.8 * s;
      g.save();
      g.translate(x, y);
      g.rotate(a * Math.PI / 180);
      var gr = g.createLinearGradient(-w, 0, w, 0);
      gr.addColorStop(0, 'rgba(0,0,0,0.35)');
      gr.addColorStop(0.35, acciaio);
      gr.addColorStop(1, acciaio);
      g.beginPath();
      g.moveTo(0, 0);
      g.quadraticCurveTo(w * 0.9, -len * 0.45, w, -len + w);
      g.arc(0, -len + w, w, 0, Math.PI, true);
      g.quadraticCurveTo(-w * 0.9, -len * 0.45, 0, 0);
      g.closePath();
      g.fillStyle = acciaio;
      g.fill();
      g.fillStyle = gr;
      g.globalAlpha = 0.6;
      g.fill();
      g.globalAlpha = 1;
      g.lineWidth = 0.6;
      g.strokeStyle = 'rgba(0,0,0,0.35)';
      g.stroke();
      // la cruna: una fessura allungata vicino alla testa, forata (si vede quello che c'è sotto)
      g.globalCompositeOperation = 'destination-out';
      g.beginPath();
      g.ellipse(0, -len + 5.2 * s, 0.75 * s, 2.7 * s, 0, 0, 6.283);
      g.fill();
      g.globalCompositeOperation = 'source-over';
      g.restore();
    }
    return {
      frame: function (t) {
        if (hot !== prima) { prima = hot; scossa = t; }
        var aT = hot ? 12 : 38 + Math.max(-22, Math.min(22, vx * 1.2));
        ang += (aT - ang) * (meno ? 1 : 0.16);
        sc += ((hot ? 1.18 : 1) - sc) * 0.2;
        // la cruna, nel mondo
        var r = ang * Math.PI / 180, len = 30 * sc;
        var ex = mx + Math.sin(r) * (len - 5.2 * sc), ey = my - Math.cos(r) * (len - 5.2 * sc);
        pts[0].x = ex; pts[0].y = ey;
        var lj = hot ? L * 0.4 : L;
        for (var i = 1; i < N; i++) {
          var p = pts[i];
          var vxs = (p.x - p.px) * 0.94, vys = (p.y - p.py) * 0.94;
          p.px = p.x; p.py = p.y;
          var brezza = meno ? 0 : Math.sin(t / 650 + i * 0.45) * 0.05 * (i / N);
          p.x += vxs + brezza;
          p.y += vys + 0.32;
        }
        // il filo esce dalla cruna di traverso all'ago, poi pende: si tira piano il primo tratto in quella direzione
        var ux = Math.cos(r), uy = Math.sin(r);
        for (var it = 0; it < 5; it++) {
          pts[1].x += (ex + ux * lj - pts[1].x) * 0.5; pts[1].y += (ey + uy * lj - pts[1].y) * 0.5;
          pts[2].x += (ex + ux * lj * 1.7 - pts[2].x) * 0.15; pts[2].y += (ey + uy * lj * 1.7 + lj * 0.6 - pts[2].y) * 0.15;
          for (var j = 1; j < N; j++) {
            var a = pts[j - 1], b = pts[j];
            var dx = b.x - a.x, dy = b.y - a.y, d = Math.sqrt(dx * dx + dy * dy) || 1;
            var k = (d - lj) / d;
            if (j > 1) { a.x += dx * k * 0.5; a.y += dy * k * 0.5; b.x -= dx * k * 0.5; b.y -= dy * k * 0.5; } else { b.x -= dx * k; b.y -= dy * k; }
          }
        }
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        disegnaAgo(g, mx, my, ang, sc);
        // il filo: dalla cruna, assottigliandosi verso il fondo, con le curve morbide fra i punti
        g.strokeStyle = col; g.lineCap = 'round'; g.lineJoin = 'round';
        for (var m = 1; m < N; m++) {
          var p0 = pts[m - 1], p1 = pts[m], p2 = pts[Math.min(N - 1, m + 1)];
          g.lineWidth = 2.1 - 1.1 * m / N;
          g.beginPath();
          g.moveTo(m === 1 ? p0.x : (p0.x + p1.x) / 2, m === 1 ? p0.y : (p0.y + p1.y) / 2);
          g.quadraticCurveTo(p1.x, p1.y, (p1.x + p2.x) / 2, (p1.y + p2.y) / 2);
          g.stroke();
        }
        // il codino dall'altra parte della cruna, e il nodo in fondo
        g.lineWidth = 1.6;
        g.beginPath();
        g.moveTo(ex, ey);
        g.quadraticCurveTo(ex - ux * 7, ey - uy * 7 + 2, ex - ux * 8, ey - uy * 8 + 9);
        g.stroke();
        var fondo = pts[N - 1];
        g.fillStyle = col;
        g.beginPath(); g.arc(fondo.x, fondo.y, 2.1, 0, 6.283); g.fill();
        // continua finché il filo si assesta dopo l'ultimo movimento o l'ultimo link; poi resta fermo com'è
        return t - Math.max(ultimo, scossa) < 3500;
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
