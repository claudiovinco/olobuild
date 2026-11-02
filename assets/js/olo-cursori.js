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
  var nodiNuovi = null; // i nodi dello stile che si sta creando (crea): si nascondono quando lo stile non è attivo
  function nodo(cls, tag) {
    var e = document.createElement(tag || 'div');
    e.className = P + ' ' + P + '-' + cls;
    e.setAttribute('aria-hidden', 'true');
    document.body.appendChild(e);
    if (nodiNuovi) nodiNuovi.push(e);
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
    if (!stile) return;
    var ancora = stile.frame(t || performance.now());
    if (ancora) giro = requestAnimationFrame(ciclo);
  }
  function chiedi() { if (!giro) giro = requestAnimationFrame(ciclo); }

  /* ── gli stili ── */
  var STILI = {};

  // Anello e punto. Quello del sito lo disegna Olobuild_Magnetic_Cursor (con l'attrazione dei pulsanti); questo serve
  // alle sezioni che lo scelgono mentre la pagina ne ha un altro.
  STILI.anello = function () {
    var a = nodo('anello'), p = nodo('punto');
    var ax = mx, ay = my;
    return {
      frame: function () {
        ax += (mx - ax) * (meno ? 1 : 0.2);
        ay += (my - ay) * (meno ? 1 : 0.2);
        a.classList.toggle('is-hot', !!hot);
        sposta(a, ax, ay, hot ? ' scale(' + (+CFG.hotScale || 1.6) + ')' : '');
        sposta(p, mx, my);
        return Math.abs(mx - ax) + Math.abs(my - ay) > 0.2;
      }
    };
  };

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

  // Metro: il nastro del metro da sarta si srotola dietro il puntatore, con le tacche ogni centimetro e i numeri ogni
  // cinque; da fermo si riavvolge. In testa la linguetta di metallo; sui link il nastro si ritira in fretta.
  STILI.metro = function () {
    var T = tela(), pts = [], prima = 0;
    var nastro = risolvi(CFG.ringColor || '#f3e7c9'), tacche = risolvi(CFG.dotColor || '#1b1b1b');
    var MAX = 260, PASSO = 5;
    return {
      punto: function () {
        if (meno) return;
        var u = pts[pts.length - 1];
        if (!u || Math.abs(mx - u.x) + Math.abs(my - u.y) > PASSO) pts.push({ x: mx, y: my });
        var l = 0;
        for (var i = pts.length - 1; i > 0; i--) {
          l += Math.hypot(pts[i].x - pts[i - 1].x, pts[i].y - pts[i - 1].y);
          if (l > MAX) { pts.splice(0, i); break; }
        }
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        // da fermo il nastro si riavvolge verso la linguetta
        if (t - ultimo > 120 && pts.length > 1) pts.splice(0, Math.max(1, Math.round(dt / (hot ? 6 : 16))));
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        var L = pts.concat([{ x: mx, y: my }]);
        if (L.length > 1) {
          // il nastro: una striscia larga 13 px lungo il percorso
          g.lineJoin = 'round'; g.lineCap = 'butt';
          g.strokeStyle = tacche; g.lineWidth = 15;
          g.beginPath(); g.moveTo(L[0].x, L[0].y);
          for (var a = 1; a < L.length; a++) g.lineTo(L[a].x, L[a].y);
          g.stroke();
          g.strokeStyle = nastro; g.lineWidth = 13;
          g.stroke();
          // le tacche, misurate dalla linguetta verso la coda
          var dist = 0, cm = 0;
          g.fillStyle = tacche; g.strokeStyle = tacche; g.lineWidth = 1;
          g.font = '600 8px ui-monospace, Menlo, monospace'; g.textAlign = 'center'; g.textBaseline = 'middle';
          for (var k = L.length - 1; k > 0; k--) {
            var p = L[k], q = L[k - 1];
            var seg = Math.hypot(p.x - q.x, p.y - q.y);
            if (!seg) continue;
            var ux = (q.x - p.x) / seg, uy = (q.y - p.y) / seg, nx = -uy, ny = ux;
            while (dist + seg >= (cm + 1) * 8) {
              cm++;
              var f = (cm * 8 - dist) / seg, x = p.x + (q.x - p.x) * f, y = p.y + (q.y - p.y) * f;
              var lungo = cm % 5 === 0 ? 6 : 3;
              g.beginPath(); g.moveTo(x + nx * 6.5, y + ny * 6.5); g.lineTo(x + nx * (6.5 - lungo), y + ny * (6.5 - lungo)); g.stroke();
              if (cm % 5 === 0) g.fillText(String(cm), x - nx * 2.5, y - ny * 2.5);
            }
            dist += seg;
          }
        }
        // la linguetta di metallo in testa al nastro
        var ang = L.length > 1 ? Math.atan2(my - L[L.length - 2].y, mx - L[L.length - 2].x) : 0;
        g.setTransform(T.dpr * Math.cos(ang), T.dpr * Math.sin(ang), -T.dpr * Math.sin(ang), T.dpr * Math.cos(ang), mx * T.dpr, my * T.dpr);
        g.fillStyle = tacche;
        g.fillRect(-2, -8, 5, 16);
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        return pts.length > 1 || t - ultimo < 200;
      }
    };
  };

  // Perla: una perla iridescente segue il puntatore con un poco di ritardo; i riflessi cambiano colore mentre si muove.
  // Sui link si gonfia in una bolla trasparente che lascia vedere il testo sotto.
  STILI.perla = function () {
    var T = tela(), x = mx, y = my, r = 9, giro = 0;
    var base = risolvi(CFG.ringColor || '#f6efe9'), alone = risolvi(CFG.dotColor || '#9b4a3c');
    return {
      frame: function () {
        var k = meno ? 1 : 0.22;
        x += (mx - x) * k; y += (my - y) * k;
        var rT = hot ? 24 : 9;
        r += (rT - r) * 0.2;
        giro += (Math.abs(mx - x) + Math.abs(my - y)) * 0.02 + 0.004;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        // l'ombra morbida sotto la perla
        g.globalAlpha = hot ? 0 : 0.18;
        g.fillStyle = '#000';
        g.beginPath(); g.ellipse(x + 2, y + r + 4, r * 0.9, r * 0.32, 0, 0, 6.283); g.fill();
        g.globalAlpha = 1;
        var corpo = g.createRadialGradient(x - r * 0.35, y - r * 0.4, r * 0.1, x, y, r);
        corpo.addColorStop(0, '#ffffff');
        corpo.addColorStop(0.45, base);
        corpo.addColorStop(1, alone);
        g.globalAlpha = hot ? 0.28 : 1;
        g.fillStyle = corpo;
        g.beginPath(); g.arc(x, y, r, 0, 6.283); g.fill();
        // l'iridescenza: un giro di colori che ruota col movimento
        if (g.createConicGradient) {
          var iri = g.createConicGradient(giro, x, y);
          ['rgba(255,170,200,.55)', 'rgba(170,220,255,.55)', 'rgba(200,255,210,.5)', 'rgba(255,230,170,.55)', 'rgba(255,170,200,.55)']
            .forEach(function (c, i) { iri.addColorStop(i / 4, c); });
          g.globalAlpha = hot ? 0.5 : 0.45;
          g.globalCompositeOperation = 'soft-light';
          g.fillStyle = iri;
          g.beginPath(); g.arc(x, y, r, 0, 6.283); g.fill();
          g.globalCompositeOperation = 'source-over';
        }
        g.globalAlpha = 1;
        g.lineWidth = hot ? 1.5 : 0.8;
        g.strokeStyle = hot ? alone : 'rgba(0,0,0,.18)';
        g.beginPath(); g.arc(x, y, r, 0, 6.283); g.stroke();
        // il punto di luce
        g.fillStyle = 'rgba(255,255,255,.9)';
        g.beginPath(); g.ellipse(x - r * 0.38, y - r * 0.42, r * 0.22, r * 0.14, -0.6, 0, 6.283); g.fill();
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || Math.abs(rT - r) > 0.05;
      }
    };
  };

  // Farina: il puntatore lascia cadere un velo di farina che scende piano e si posa; al clic, uno sbuffo.
  STILI.farina = function () {
    var T = tela(), p = nodo('punto'), grani = [], prima = 0;
    // la farina è chiara ma si deve vedere anche sui fondi chiari: un beige caldo col bordo appena più scuro
    var col = risolvi('color-mix(in srgb, ' + (CFG.ringColor || '#f4ead8') + ' 62%, #9a8463)'), bordo = 'rgba(70,52,30,.35)';
    function soffia(x, y, n, forza) {
      for (var i = 0; i < n; i++) {
        var a = Math.random() * 6.283, v = (0.3 + Math.random()) * forza;
        grani.push({ x: x, y: y, vx: Math.cos(a) * v, vy: Math.sin(a) * v - forza * 0.3, r: 1.2 + Math.random() * 2.6, t: 0,
                     vita: 900 + Math.random() * 900 });
      }
      if (grani.length > 420) grani.splice(0, grani.length - 420);
      chiedi();
    }
    window.addEventListener('pointerdown', function (e) { if (!meno) soffia(e.clientX, e.clientY, 46, 2.6); }, { passive: true });
    return {
      punto: function () { if (!meno) soffia(mx, my, 3, 0.45); },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2.2)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.fillStyle = col; g.strokeStyle = bordo; g.lineWidth = 0.6;
        for (var i = grani.length - 1; i >= 0; i--) {
          var s = grani[i];
          s.t += dt;
          if (s.t > s.vita) { grani.splice(i, 1); continue; }
          s.vx *= 0.94; s.vy = s.vy * 0.94 + 0.035;
          s.x += s.vx * dt / 16 + Math.sin((s.t + i * 40) / 260) * 0.12; s.y += s.vy * dt / 16;
          g.globalAlpha = 0.9 * (1 - s.t / s.vita);
          g.beginPath(); g.arc(s.x, s.y, s.r, 0, 6.283); g.fill(); g.stroke();
        }
        g.globalAlpha = 1;
        if (!grani.length) prima = 0;
        return grani.length > 0;
      }
    };
  };

  // Ombra: il puntatore è uno gnomone, un'asticella dritta; la luce sta in alto al centro della finestra e l'ombra si
  // allunga quanto più ci si allontana, come il sole che scende. Sui link l'asticella si piega verso il link.
  STILI.ombra = function () {
    var T = tela(), x = mx, y = my, pieg = 0;
    var col = risolvi(CFG.dotColor || '#2f4a4c'), asta = risolvi(CFG.ringColor || '#ffffff');
    return {
      frame: function () {
        var k = meno ? 1 : 0.35;
        x += (mx - x) * k; y += (my - y) * k;
        pieg += ((hot ? 1 : 0) - pieg) * 0.2;
        var sx = innerWidth / 2, sy = -innerHeight * 0.25;
        var dx = x - sx, dy = y - sy, d = Math.hypot(dx, dy) || 1;
        var ang = Math.atan2(dy, dx), lung = 26 + d * 0.16 * (1 - pieg * 0.6);
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        // l'ombra: un fuso che si allarga e sfuma
        g.save();
        g.translate(x, y);
        g.rotate(ang);
        var sf = g.createLinearGradient(0, 0, lung, 0);
        sf.addColorStop(0, col);
        sf.addColorStop(1, 'rgba(0,0,0,0)');
        g.globalAlpha = 0.6;
        g.fillStyle = sf;
        g.beginPath(); g.moveTo(0, -2); g.lineTo(lung, -7); g.lineTo(lung, 7); g.lineTo(0, 2); g.closePath(); g.fill();
        g.restore();
        // l'asticella
        g.globalAlpha = 1;
        g.save();
        g.translate(x, y);
        g.rotate(-0.5 * pieg);
        g.fillStyle = col;
        g.fillRect(-1.5, -15, 3, 15);
        g.fillStyle = asta;
        g.fillRect(-0.6, -14, 1.2, 12);
        g.beginPath(); g.arc(0, -15, 2.6, 0, 6.283); g.fillStyle = col; g.fill();
        g.restore();
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || Math.abs((hot ? 1 : 0) - pieg) > 0.01;
      }
    };
  };

  // Cartellino: un cartellino del prezzo appeso al puntatore con lo spago, che dondola quando ci si muove e si ferma
  // piano; dice la parola del cursore (–30%) e sui link si gira e dice «Vedi».
  STILI.cartellino = function () {
    var T = tela(), ang = 0, vel = 0, gira = 0, prima = 0;
    var fondo = risolvi(CFG.dotColor || '#5b2eff'), scritta = risolvi(CFG.labelColor || '#ffffff');
    var parola = String(CFG.label || '−30%').slice(0, 8);
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var s = dt / 16;
        // un pendolo: la spinta viene dalla velocità orizzontale del puntatore
        var spinta = meno ? 0 : -vx * 0.006;
        vel += (spinta - ang * 0.06) * s;
        vel *= Math.pow(0.9, s);
        ang += vel * s;
        if (meno) ang = 0;
        vx *= 0.8;
        gira += ((hot ? 1 : 0) - gira) * 0.18 * s;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.save();
        g.translate(mx, my);
        g.rotate(ang);
        // lo spago
        g.strokeStyle = fondo; g.lineWidth = 1.2;
        g.beginPath(); g.moveTo(0, 0); g.quadraticCurveTo(3, 10, 0, 20); g.stroke();
        // il cartellino, girato in larghezza sui link
        g.translate(0, 20);
        var sc = Math.cos(gira * Math.PI);
        g.scale(Math.abs(sc) < 0.04 ? 0.04 : Math.abs(sc), 1);
        var w = 38, h = 22;
        g.fillStyle = fondo;
        g.beginPath();
        g.moveTo(-w / 2, 6); g.lineTo(-6, 0); g.lineTo(6, 0); g.lineTo(w / 2, 6); g.lineTo(w / 2, h + 6); g.lineTo(-w / 2, h + 6);
        g.closePath(); g.fill();
        g.globalCompositeOperation = 'destination-out';
        g.beginPath(); g.arc(0, 4, 2, 0, 6.283); g.fill();
        g.globalCompositeOperation = 'source-over';
        g.fillStyle = scritta;
        g.font = '700 10px ui-monospace, Menlo, monospace'; g.textAlign = 'center'; g.textBaseline = 'middle';
        g.fillText(gira > 0.5 ? 'Vedi' : parola, 0, 6 + h / 2 + 1);
        g.restore();
        return Math.abs(ang) > 0.002 || Math.abs(vel) > 0.002 || Math.abs((hot ? 1 : 0) - gira) > 0.01 || t - ultimo < 120;
      }
    };
  };

  // Fibbia: una fibbia d'ottone col suo pezzo di cinturino dietro, girata nella direzione in cui si va; sui link
  // l'ardiglione (la punta che entra nel buco) si abbassa e la chiude.
  STILI.fibbia = function () {
    var T = tela(), x = mx, y = my, ang = 0, chiusa = 0, prima = 0;
    var ottone = risolvi(CFG.ringColor || '#d9a441'), cuoio = risolvi(CFG.dotColor || '#8a3b1f');
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var s = dt / 16, k = meno ? 1 : 0.3;
        x += (mx - x) * k; y += (my - y) * k;
        var v = Math.sqrt(vx * vx + vy * vy);
        if (v > 1.5 && !meno) {
          var aT = Math.atan2(vy, vx), d = aT - ang;
          while (d > Math.PI) d -= 6.283;
          while (d < -Math.PI) d += 6.283;
          ang += d * 0.12 * s;
        }
        vx *= 0.85; vy *= 0.85;
        chiusa += ((hot ? 1 : 0) - chiusa) * 0.22 * s;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.save();
        g.translate(x, y);
        g.rotate(ang);
        // il cinturino dietro, col bordo cucito
        g.fillStyle = cuoio;
        g.beginPath(); g.roundRect ? g.roundRect(-44, -6, 38, 12, 3) : g.rect(-44, -6, 38, 12); g.fill();
        g.strokeStyle = 'rgba(255,255,255,.45)'; g.lineWidth = 0.8; g.setLineDash([2.5, 2]);
        g.beginPath(); g.moveTo(-42, -3.5); g.lineTo(-8, -3.5); g.moveTo(-42, 3.5); g.lineTo(-8, 3.5); g.stroke();
        g.setLineDash([]);
        // il telaio della fibbia
        g.strokeStyle = 'rgba(0,0,0,.28)'; g.lineWidth = 4.6;
        g.beginPath(); g.roundRect ? g.roundRect(-9, -11, 22, 22, 6) : g.rect(-9, -11, 22, 22); g.stroke();
        g.strokeStyle = ottone; g.lineWidth = 3;
        g.beginPath(); g.roundRect ? g.roundRect(-9, -11, 22, 22, 6) : g.rect(-9, -11, 22, 22); g.stroke();
        // l'ardiglione: alzato quando si va, abbassato sopra un link
        g.save();
        g.translate(-9, 0);
        g.rotate(-0.95 * (1 - chiusa));
        g.strokeStyle = ottone; g.lineWidth = 2.4; g.lineCap = 'round';
        g.beginPath(); g.moveTo(0, 0); g.lineTo(24, 0); g.stroke();
        g.restore();
        // il riflesso sul telaio
        g.strokeStyle = 'rgba(255,255,255,.6)'; g.lineWidth = 1;
        g.beginPath(); g.moveTo(-5, -9); g.lineTo(6, -9); g.stroke();
        g.restore();
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || Math.abs((hot ? 1 : 0) - chiusa) > 0.01 || t - ultimo < 600;
      }
    };
  };

  // Paillettes: dal puntatore cadono paillettes che girano su sé stesse (si vede il dritto lucido e il rovescio) e
  // luccicano; al clic ne esplode una manciata. Colori dal cursore (oro, il colore del punto) e l'avorio.
  STILI.paillettes = function () {
    var T = tela(), p = nodo('punto'), pp = [], prima = 0, cx = mx, cy = my;
    var tinte = [risolvi(CFG.ringColor || '#c8a45c'), risolvi(CFG.dotColor || '#5e0f23'), '#f3e9d6'];
    function getta(x, y, n, forza) {
      for (var i = 0; i < n; i++) {
        var a = Math.random() * 6.283, v = (0.2 + Math.random()) * forza;
        pp.push({ x: x, y: y, vx: Math.cos(a) * v, vy: Math.sin(a) * v - forza * 0.4, r: 2.6 + Math.random() * 2.2,
                  giro: Math.random() * 6.283, vg: 0.08 + Math.random() * 0.16, t: 0, vita: 1100 + Math.random() * 900,
                  c: tinte[(Math.random() * tinte.length) | 0] });
      }
      if (pp.length > 260) pp.splice(0, pp.length - 260);
      chiedi();
    }
    window.addEventListener('pointerdown', function (e) { if (!meno) getta(e.clientX, e.clientY, 26, 3.2); }, { passive: true });
    return {
      punto: function () {
        if (meno) return;
        var d = Math.abs(mx - cx) + Math.abs(my - cy);
        if (d > 14) { cx = mx; cy = my; getta(mx, my, 1, 0.6); }
      },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        sposta(p, mx, my, hot ? ' scale(2)' : '');
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = pp.length - 1; i >= 0; i--) {
          var s = pp[i];
          s.t += dt;
          if (s.t > s.vita) { pp.splice(i, 1); continue; }
          s.vx *= 0.95; s.vy = s.vy * 0.95 + 0.03;
          s.x += s.vx * dt / 16; s.y += s.vy * dt / 16;
          s.giro += s.vg * dt / 16;
          var lato = Math.cos(s.giro), luce = Math.max(0, Math.sin(s.giro * 2 + i));
          g.globalAlpha = 0.95 * (1 - s.t / s.vita);
          g.save();
          g.translate(s.x, s.y);
          g.scale(Math.max(0.12, Math.abs(lato)), 1);
          g.fillStyle = lato > 0 ? s.c : 'rgba(0,0,0,.35)';
          g.beginPath(); g.arc(0, 0, s.r, 0, 6.283); g.fill();
          // il forellino al centro e il lampo di luce sul dritto
          g.globalCompositeOperation = 'destination-out';
          g.beginPath(); g.arc(0, 0, s.r * 0.22, 0, 6.283); g.fill();
          g.globalCompositeOperation = 'source-over';
          if (lato > 0 && luce > 0.7) {
            g.fillStyle = 'rgba(255,255,255,' + ((luce - 0.7) * 3).toFixed(2) + ')';
            g.beginPath(); g.arc(-s.r * 0.3, -s.r * 0.3, s.r * 0.5, 0, 6.283); g.fill();
          }
          g.restore();
        }
        g.globalAlpha = 1;
        if (!pp.length) prima = 0;
        return pp.length > 0;
      }
    };
  };

  // Laccio: un laccio da scarpa appeso al puntatore, con l'occhiello in cima e il puntale in fondo; pende, ondeggia
  // con il movimento e sopra un link si accorcia, tirato come quando si allaccia la scarpa.
  STILI.laccio = function () {
    var T = tela(), N = 18, L = 5.5, pts = [], prima = null, scossa = 0;
    var col = risolvi(CFG.ringColor || '#f25c3a'), puntale = risolvi(CFG.dotColor || '#10142e');
    for (var i = 0; i < N; i++) pts.push({ x: mx, y: my + i * L, px: mx, py: my + i * L });
    return {
      frame: function (t) {
        if (hot !== prima) { prima = hot; scossa = t; }
        pts[0].x = mx; pts[0].y = my;
        var lj = hot ? L * 0.45 : L;
        for (var i = 1; i < N; i++) {
          var p = pts[i];
          var vxs = (p.x - p.px) * 0.95, vys = (p.y - p.py) * 0.95;
          p.px = p.x; p.py = p.y;
          p.x += vxs + (meno ? 0 : Math.sin(t / 700 + i * 0.4) * 0.04 * (i / N));
          p.y += vys + 0.36;
        }
        for (var it = 0; it < 6; it++) {
          for (var j = 1; j < N; j++) {
            var a = pts[j - 1], b = pts[j];
            var dx = b.x - a.x, dy = b.y - a.y, d = Math.sqrt(dx * dx + dy * dy) || 1, k = (d - lj) / d;
            if (j > 1) { a.x += dx * k * 0.5; a.y += dy * k * 0.5; b.x -= dx * k * 0.5; b.y -= dy * k * 0.5; } else { b.x -= dx * k; b.y -= dy * k; }
          }
        }
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.lineCap = 'round'; g.lineJoin = 'round';
        // il laccio: un nastro piatto col bordo scuro e la trama a spina di pesce
        function tratto(w, c, trat) {
          g.lineWidth = w; g.strokeStyle = c; g.setLineDash(trat || []);
          g.beginPath();
          g.moveTo(pts[0].x, pts[0].y);
          for (var m = 1; m < N - 1; m++) g.quadraticCurveTo(pts[m].x, pts[m].y, (pts[m].x + pts[m + 1].x) / 2, (pts[m].y + pts[m + 1].y) / 2);
          g.lineTo(pts[N - 1].x, pts[N - 1].y);
          g.stroke();
        }
        tratto(5.2, 'rgba(0,0,0,.35)');
        tratto(4, col);
        tratto(1.2, 'rgba(255,255,255,.45)', [2, 3]);
        g.setLineDash([]);
        // l'occhiello in cima, sul puntatore
        g.lineWidth = 2; g.strokeStyle = puntale; g.fillStyle = 'rgba(255,255,255,.9)';
        g.beginPath(); g.arc(mx, my, 4.2, 0, 6.283); g.fill(); g.stroke();
        // il puntale in fondo, nella direzione dell'ultimo tratto
        var u = pts[N - 1], w = pts[N - 2], an = Math.atan2(u.y - w.y, u.x - w.x);
        g.save(); g.translate(u.x, u.y); g.rotate(an);
        g.fillStyle = puntale;
        g.beginPath(); g.roundRect ? g.roundRect(-2, -2.6, 12, 5.2, 2) : g.rect(-2, -2.6, 12, 5.2); g.fill();
        g.restore();
        return t - Math.max(ultimo, scossa) < 3500;
      }
    };
  };

  // Chicco: un chicco di caffè col suo solco, che gira mentre si muove; al clic se ne spargono alcuni che rotolano e
  // si fermano. Il colore dal punto del cursore (il marrone dell'espresso), il riflesso caldo dall'anello.
  STILI.chicco = function () {
    var T = tela(), x = mx, y = my, ang = 0, prima = 0, sparsi = [];
    var bruno = risolvi(CFG.dotColor || '#4a2c1d'), crema = risolvi(CFG.ringColor || '#d9a066');
    function chicco(g, cx, cy, a, s, alfa) {
      g.save(); g.translate(cx, cy); g.rotate(a); g.scale(s, s); g.globalAlpha = alfa;
      var gr = g.createRadialGradient(-3, -4, 1, 0, 0, 11);
      gr.addColorStop(0, crema); gr.addColorStop(0.35, bruno); gr.addColorStop(1, 'rgba(0,0,0,.85)');
      g.fillStyle = gr;
      g.beginPath(); g.ellipse(0, 0, 7.5, 10.5, 0, 0, 6.283); g.fill();
      // il solco: una S appena accennata
      g.strokeStyle = 'rgba(20,10,5,.75)'; g.lineWidth = 1.6; g.lineCap = 'round';
      g.beginPath(); g.moveTo(0, -9); g.bezierCurveTo(-3.5, -3, 3.5, 3, 0, 9); g.stroke();
      g.restore(); g.globalAlpha = 1;
    }
    window.addEventListener('pointerdown', function (e) {
      if (meno) return;
      for (var i = 0; i < 7; i++) {
        var a = Math.random() * 6.283, v = 1.5 + Math.random() * 2.5;
        sparsi.push({ x: e.clientX, y: e.clientY, vx: Math.cos(a) * v, vy: Math.sin(a) * v, a: Math.random() * 6, va: (Math.random() - 0.5) * 0.4, t: 0 });
      }
      if (sparsi.length > 60) sparsi.splice(0, sparsi.length - 60);
      chiedi();
    }, { passive: true });
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var k = meno ? 1 : 0.28;
        var dx = (mx - x) * k, dy = (my - y) * k;
        x += dx; y += dy;
        ang += (dx + dy) * 0.06;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = sparsi.length - 1; i >= 0; i--) {
          var s = sparsi[i];
          s.t += dt;
          if (s.t > 1600) { sparsi.splice(i, 1); continue; }
          s.vx *= 0.9; s.vy *= 0.9; s.x += s.vx * dt / 16; s.y += s.vy * dt / 16; s.a += s.va * s.vx;
          chicco(g, s.x, s.y, s.a, 0.7, 1 - Math.max(0, (s.t - 1100) / 500));
        }
        chicco(g, x, y, ang, hot ? 1.5 : 1, 1);
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || sparsi.length > 0;
      }
    };
  };

  // Stecca: una stecca da biliardo che punta al puntatore dal basso a destra, col puntale blu di gesso; si inclina
  // con il movimento, sui link si accorcia (prende la mira) e al clic carica e colpisce.
  STILI.stecca = function () {
    var T = tela(), ang = -2.35, tiro = 0, carica = 0, prima = 0;
    var legno = risolvi(CFG.ringColor || '#e8b931'), punta = '#3b6fd6';
    window.addEventListener('pointerdown', function () { if (!meno) { carica = 1; chiedi(); } }, { passive: true });
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var s = dt / 16;
        var aT = -2.35 + Math.max(-0.5, Math.min(0.5, vx * 0.02));
        ang += (aT - ang) * (meno ? 1 : 0.14 * s);
        vx *= 0.85;
        // il tiro: indietro mentre si carica, poi avanti di scatto
        if (carica > 0) { carica -= 0.06 * s; tiro = carica > 0.3 ? -18 * (1 - carica) / 0.7 : 14; }
        else { tiro += (0 - tiro) * 0.25 * s; }
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        g.save();
        g.translate(mx, my);
        g.rotate(ang + Math.PI);
        var lontano = (hot ? 10 : 16) - tiro;
        var lung = hot ? 90 : 120;
        // il fusto: chiaro in punta, scuro al calcio
        var gr = g.createLinearGradient(lontano, 0, lontano + lung, 0);
        gr.addColorStop(0, '#f3e6c8'); gr.addColorStop(0.55, legno); gr.addColorStop(1, '#3a2312');
        g.fillStyle = gr;
        g.beginPath();
        g.moveTo(lontano, -1.8); g.lineTo(lontano + lung, -3.6); g.lineTo(lontano + lung, 3.6); g.lineTo(lontano, 1.8);
        g.closePath(); g.fill();
        // il puntale blu
        g.fillStyle = punta;
        g.beginPath(); g.roundRect ? g.roundRect(lontano - 4, -1.9, 5, 3.8, 1.2) : g.rect(lontano - 4, -1.9, 5, 3.8); g.fill();
        g.restore();
        // la mira: un punto sotto il puntatore
        g.fillStyle = hot ? punta : 'rgba(255,255,255,.9)';
        g.strokeStyle = 'rgba(0,0,0,.35)'; g.lineWidth = 1;
        g.beginPath(); g.arc(mx, my, hot ? 4 : 3, 0, 6.283); g.fill(); g.stroke();
        return Math.abs(aT - ang) > 0.002 || Math.abs(tiro) > 0.2 || carica > 0 || t - ultimo < 400;
      }
    };
  };

  // Peperoncino: un peperoncino rosso col picciolo verde, che si piega nella direzione in cui si va; al clic sparge i
  // fiocchi che cadono girando.
  STILI.peperoncino = function () {
    var T = tela(), x = mx, y = my, pieg = 0, prima = 0, fiocchi = [];
    var rosso = risolvi(CFG.ringColor || '#b3261e'), seme = risolvi(CFG.dotColor || '#f2b705');
    window.addEventListener('pointerdown', function (e) {
      if (meno) return;
      for (var i = 0; i < 18; i++) {
        var a = -Math.PI / 2 + (Math.random() - 0.5) * 2.4, v = 1.5 + Math.random() * 3;
        fiocchi.push({ x: e.clientX, y: e.clientY, vx: Math.cos(a) * v, vy: Math.sin(a) * v, r: 1.5 + Math.random() * 2,
                       a: Math.random() * 6, t: 0, c: Math.random() < 0.25 ? seme : rosso });
      }
      if (fiocchi.length > 160) fiocchi.splice(0, fiocchi.length - 160);
      chiedi();
    }, { passive: true });
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var k = meno ? 1 : 0.3;
        x += (mx - x) * k; y += (my - y) * k;
        pieg += ((meno ? 0 : Math.max(-0.6, Math.min(0.6, vx * 0.03))) - pieg) * 0.15;
        vx *= 0.85;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = fiocchi.length - 1; i >= 0; i--) {
          var f = fiocchi[i];
          f.t += dt;
          if (f.t > 1400) { fiocchi.splice(i, 1); continue; }
          f.vx *= 0.97; f.vy = f.vy * 0.97 + 0.12; f.x += f.vx * dt / 16; f.y += f.vy * dt / 16; f.a += 0.2;
          g.globalAlpha = 1 - f.t / 1400; g.fillStyle = f.c;
          g.save(); g.translate(f.x, f.y); g.rotate(f.a);
          g.beginPath(); g.ellipse(0, 0, f.r * 1.5, f.r * 0.7, 0, 0, 6.283); g.fill(); g.restore();
        }
        g.globalAlpha = 1;
        g.save();
        g.translate(x, y);
        g.rotate(-0.5 + pieg);
        var sc = hot ? 1.25 : 1;
        g.scale(sc, sc);
        // il corpo: largo in alto, sottile e ricurvo in punta (verso il puntatore)
        var gr = g.createLinearGradient(-6, 0, 6, 0);
        gr.addColorStop(0, rosso); gr.addColorStop(0.5, '#ff6a5a'); gr.addColorStop(1, rosso);
        g.fillStyle = gr;
        g.beginPath();
        g.moveTo(-5.5, -26);
        g.bezierCurveTo(-6.5, -10, -3, -2, 0, 0);
        g.bezierCurveTo(2.5, -4, 6.5, -12, 5.5, -26);
        g.quadraticCurveTo(0, -29, -5.5, -26);
        g.fill();
        g.fillStyle = 'rgba(255,255,255,.45)';
        g.beginPath(); g.ellipse(-2, -18, 1.1, 5, 0.1, 0, 6.283); g.fill();
        // il picciolo verde
        g.strokeStyle = '#2f7a2f'; g.lineWidth = 2.6; g.lineCap = 'round';
        g.beginPath(); g.moveTo(-4.5, -27); g.quadraticCurveTo(0, -31, 2, -36); g.stroke();
        g.fillStyle = '#2f7a2f';
        g.beginPath(); g.ellipse(0, -27.5, 6.5, 2.4, 0, 0, 6.283); g.fill();
        g.restore();
        return Math.abs(mx - x) + Math.abs(my - y) > 0.2 || fiocchi.length > 0 || Math.abs(pieg) > 0.01;
      }
    };
  };

  // Pennino: il pennino d'una stilografica con la punta sul puntatore, il taglio e il foro di sfiato; scrive un tratto
  // da calligrafo (sottile in un verso, pieno nell'altro, come la punta tronca) che si asciuga e svanisce; sui link
  // il pennino si drizza, al clic lascia una macchia d'inchiostro con due schizzi.
  STILI.pennino = function () {
    var T = tela(), pts = [], macchie = [], ang = -0.75, prima = 0;
    var inchiostro = risolvi(CFG.dotColor || '#233a8b'), metallo = risolvi(CFG.ringColor || '#c9a227');
    window.addEventListener('pointerdown', function (e) {
      if (meno) return;
      var gocce = [];
      for (var i = 0; i < 3; i++) {
        var a = Math.random() * 6.283, d = 9 + Math.random() * 9;
        gocce.push({ dx: Math.cos(a) * d, dy: Math.sin(a) * d, r: 1.2 + Math.random() * 1.8 });
      }
      macchie.push({ x: e.clientX, y: e.clientY, t: 0, g: gocce, f: Math.random() * 6 });
      if (macchie.length > 12) macchie.shift();
      chiedi();
    }, { passive: true });
    return {
      punto: function (t) { if (!meno) pts.push({ x: mx, y: my, t: t }); if (pts.length > 140) pts.shift(); },
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        // il tratto: la larghezza dipende dalla direzione rispetto alla punta tronca (inclinata di 40°)
        while (pts.length && t - pts[0].t > 1100) pts.shift();
        g.lineCap = 'round';
        for (var i = 1; i < pts.length; i++) {
          var a = pts[i - 1], b = pts[i];
          var dir = Math.atan2(b.y - a.y, b.x - a.x);
          var w = 0.8 + 4.2 * Math.abs(Math.sin(dir - 0.7));
          var vita = 1 - (t - b.t) / 1100;
          g.globalAlpha = Math.max(0, vita) * 0.9;
          g.strokeStyle = inchiostro; g.lineWidth = w;
          g.beginPath(); g.moveTo(a.x, a.y); g.lineTo(b.x, b.y); g.stroke();
        }
        g.globalAlpha = 1;
        for (var m = macchie.length - 1; m >= 0; m--) {
          var mc = macchie[m];
          mc.t += dt;
          if (mc.t > 2200) { macchie.splice(m, 1); continue; }
          var cresce = Math.min(1, mc.t / 180), sv = mc.t > 1500 ? 1 - (mc.t - 1500) / 700 : 1;
          g.globalAlpha = sv * 0.85; g.fillStyle = inchiostro;
          g.beginPath();
          for (var k = 0; k <= 12; k++) {
            var aa = k / 12 * 6.283, rr = (6 + 2.2 * Math.sin(aa * 3 + mc.f)) * cresce;
            if (k === 0) g.moveTo(mc.x + Math.cos(aa) * rr, mc.y + Math.sin(aa) * rr);
            else g.lineTo(mc.x + Math.cos(aa) * rr, mc.y + Math.sin(aa) * rr);
          }
          g.closePath(); g.fill();
          mc.g.forEach(function (q) { g.beginPath(); g.arc(mc.x + q.dx * cresce, mc.y + q.dy * cresce, q.r, 0, 6.283); g.fill(); });
        }
        g.globalAlpha = 1;
        // il pennino: si inclina appena col movimento, sui link si drizza
        var aT = (hot ? -0.25 : -0.75) + (meno ? 0 : Math.max(-0.35, Math.min(0.35, vx * 0.02)));
        ang += (aT - ang) * 0.18;
        vx *= 0.85;
        g.save();
        g.translate(mx, my);
        g.rotate(ang);
        // la sagoma: dalla punta (0,0) si allarga verso l'alto, spalle arrotondate
        var gr = g.createLinearGradient(-9, 0, 9, 0);
        gr.addColorStop(0, metallo); gr.addColorStop(0.45, '#fff7d6'); gr.addColorStop(1, metallo);
        g.fillStyle = gr;
        g.strokeStyle = 'rgba(0,0,0,.35)'; g.lineWidth = 0.8;
        g.beginPath();
        g.moveTo(0, 0);
        g.bezierCurveTo(-3, -6, -8.5, -16, -8.5, -26);
        g.lineTo(-8.5, -34); g.quadraticCurveTo(0, -38, 8.5, -34); g.lineTo(8.5, -26);
        g.bezierCurveTo(8.5, -16, 3, -6, 0, 0);
        g.closePath(); g.fill(); g.stroke();
        // il taglio e il foro di sfiato
        g.strokeStyle = 'rgba(20,20,30,.75)'; g.lineWidth = 0.9;
        g.beginPath(); g.moveTo(0, -1); g.lineTo(0, -18); g.stroke();
        g.fillStyle = 'rgba(20,20,30,.8)';
        g.beginPath(); g.ellipse(0, -20.5, 1.8, 2.4, 0, 0, 6.283); g.fill();
        // la goccia d'inchiostro sulla punta
        g.fillStyle = inchiostro;
        g.beginPath(); g.arc(0, -0.5, 1.4, 0, 6.283); g.fill();
        g.restore();
        return pts.length > 0 || macchie.length > 0 || Math.abs(aT - ang) > 0.002 || t - ultimo < 400;
      }
    };
  };

  // Ombrello: un ombrello aperto col manico a uncino sul puntatore; il vento del movimento lo inclina e lo fa
  // ondeggiare, dalle stecche cadono gocce; sui link si chiude a metà (come quando si entra in un negozio), al clic
  // scrolla una manciata di gocce.
  STILI.ombrello = function () {
    var T = tela(), ang = 0, va = 0, apre = 1, gocce = [], prima = 0, tic = 0;
    var tela_c = risolvi(CFG.ringColor || '#0e5a6b'), manico = risolvi(CFG.dotColor || '#7a2e2e');
    function goccia(x, y, vx0, vy0) {
      gocce.push({ x: x, y: y, vx: vx0, vy: vy0, t: 0 });
      if (gocce.length > 80) gocce.shift();
    }
    window.addEventListener('pointerdown', function () {
      if (meno) return;
      for (var i = 0; i < 14; i++) goccia(mx + (Math.random() - 0.5) * 50, my - 34, (Math.random() - 0.5) * 3, -1 - Math.random() * 2);
      chiedi();
    }, { passive: true });
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var s = dt / 16;
        // una molla: il vento (la velocità) spinge, la molla riporta dritto
        var spinta = meno ? 0 : Math.max(-0.7, Math.min(0.7, -vx * 0.035));
        va += ((spinta - ang) * 0.08 - va * 0.16) * s;
        ang += va * s;
        vx *= 0.88;
        apre += ((hot ? 0.45 : 1) - apre) * 0.18 * s;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        // le gocce che scendono dalle punte
        tic += dt;
        if (!meno && apre > 0.8 && tic > 260 && t - ultimo < 1200) {
          tic = 0;
          var lato = Math.random() < 0.5 ? -1 : 1, lx = lato * 30, ly = -34;
          goccia(mx + Math.cos(ang) * lx - Math.sin(ang) * ly, my + Math.sin(ang) * lx + Math.cos(ang) * ly, lato * 0.4, 0.5);
        }
        for (var i = gocce.length - 1; i >= 0; i--) {
          var q = gocce[i];
          q.t += dt;
          if (q.t > 1100 || q.y > innerHeight + 10) { gocce.splice(i, 1); continue; }
          q.vy += 0.22 * s; q.x += q.vx * s; q.y += q.vy * s;
          g.globalAlpha = 0.75 * (1 - q.t / 1100);
          g.fillStyle = tela_c;
          g.beginPath(); g.moveTo(q.x, q.y - 4); g.quadraticCurveTo(q.x + 2.4, q.y + 1, q.x, q.y + 2); g.quadraticCurveTo(q.x - 2.4, q.y + 1, q.x, q.y - 4); g.fill();
        }
        g.globalAlpha = 1;
        g.save();
        g.translate(mx, my);
        g.rotate(ang);
        // il manico a uncino (il puntatore sta nella curva) e l'asta
        g.strokeStyle = manico; g.lineWidth = 3; g.lineCap = 'round';
        g.beginPath(); g.arc(-4, 0, 4, 0, Math.PI, false); g.stroke();
        g.strokeStyle = 'rgba(40,40,40,.85)'; g.lineWidth = 1.6;
        g.beginPath(); g.moveTo(0, 0); g.lineTo(0, -40); g.stroke();
        // la cupola: quattro spicchi, larga quanto è aperta
        var L = 30 * apre, Hc = 15 + 8 * (1 - apre), base = -34 + 6 * (1 - apre);
        g.fillStyle = tela_c;
        g.beginPath();
        g.moveTo(-L, base);
        g.quadraticCurveTo(-L * 0.9, base - Hc * 1.25, 0, base - Hc);
        g.quadraticCurveTo(L * 0.9, base - Hc * 1.25, L, base);
        // il bordo a festone tra le stecche
        for (var k = 3; k >= 0; k--) {
          var x1 = -L + k * L / 2;
          g.quadraticCurveTo(x1 + L / 4, base - 5 * apre, x1, base);
        }
        g.closePath(); g.fill();
        g.globalAlpha = 0.28; g.fillStyle = '#ffffff';
        g.beginPath(); g.moveTo(-L * 0.5, base - 1); g.quadraticCurveTo(-L * 0.45, base - Hc * 1.05, 0, base - Hc); g.quadraticCurveTo(-L * 0.2, base - Hc * 0.6, -L * 0.5, base - 1); g.fill();
        g.globalAlpha = 1;
        g.strokeStyle = 'rgba(0,0,0,.25)'; g.lineWidth = 0.8;
        g.beginPath(); g.moveTo(0, base - Hc); g.lineTo(-L * 0.5, base); g.moveTo(0, base - Hc); g.lineTo(L * 0.5, base); g.stroke();
        // il puntale
        g.strokeStyle = 'rgba(40,40,40,.85)'; g.lineWidth = 1.6;
        g.beginPath(); g.moveTo(0, base - Hc); g.lineTo(0, base - Hc - 6); g.stroke();
        g.restore();
        return Math.abs(ang) > 0.002 || Math.abs(va) > 0.002 || gocce.length > 0 || Math.abs((hot ? 0.45 : 1) - apre) > 0.01 || t - ultimo < 1300;
      }
    };
  };

  // Trottola: una trottola di legno a righe colorate con la punta sul puntatore; gira sempre, più svelta quanto più
  // svelto si va, e quando rallenta ondeggia sull'asse; sui link accelera e mostra il cerchio del giro, al clic fa un
  // saltello.
  STILI.trottola = function () {
    var T = tela(), rot = 0, vel = 0.08, inclina = 0, fase = 0, salto = 0, prima = 0;
    var c1 = risolvi(CFG.ringColor || '#2a54c8'), c2 = risolvi(CFG.dotColor || '#ffc83d');
    window.addEventListener('pointerdown', function () { if (!meno) { salto = 1; chiedi(); } }, { passive: true });
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var s = dt / 16;
        var v = Math.sqrt(vx * vx + vy * vy);
        var fermo = t - ultimo > 2600 && !hot;
        var vT = meno || fermo ? 0 : 0.08 + Math.min(0.5, v * 0.02) + (hot ? 0.3 : 0);
        vel += (vT - vel) * 0.06 * s;
        vx *= 0.9; vy *= 0.9;
        rot += vel * s;
        fase += 0.05 * s;
        // l'ondeggiare: forte quando gira piano, quasi niente quando va forte
        inclina = meno ? 0 : Math.sin(fase * 2.2) * Math.max(0, 0.32 - vel) * 0.9 * Math.min(1, vel * 14);
        salto = Math.max(0, salto - 0.05 * s);
        var su = Math.sin(salto * Math.PI) * 12;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        // l'ombra
        g.fillStyle = 'rgba(0,0,0,.18)';
        g.beginPath(); g.ellipse(mx, my + 1, 9 - su * 0.3, 2.6, 0, 0, 6.283); g.fill();
        if (hot && !meno) {
          g.strokeStyle = c2; g.lineWidth = 1.2; g.globalAlpha = 0.7;
          g.beginPath(); g.ellipse(mx, my - 18 - su, 22, 6, 0, 0, 6.283); g.stroke();
          g.globalAlpha = 1;
        }
        g.save();
        g.translate(mx, my - su);
        g.rotate(inclina);
        // il corpo: una goccia rovesciata (punta in basso), con le righe che scorrono per dare il giro
        g.beginPath();
        g.moveTo(0, 0);
        g.bezierCurveTo(-4, -6, -16, -14, -15, -22);
        g.quadraticCurveTo(-13, -29, 0, -29);
        g.quadraticCurveTo(13, -29, 15, -22);
        g.bezierCurveTo(16, -14, 4, -6, 0, 0);
        g.closePath();
        g.save();
        g.clip();
        g.fillStyle = '#e8c48c';
        g.fillRect(-17, -31, 34, 32);
        // le righe: bande verticali che si spostano come su un corpo che gira (più strette ai bordi)
        for (var k = 0; k < 6; k++) {
          var a = rot + k * 1.047;
          var cx = Math.sin(a) * 15, vis = Math.cos(a);
          if (vis < 0) continue;
          g.fillStyle = k % 2 ? c1 : c2;
          g.fillRect(cx - 3.2 * vis, -31, 6.4 * vis, 32);
        }
        // la luce e l'ombra del volume
        var gr = g.createLinearGradient(-15, 0, 15, 0);
        gr.addColorStop(0, 'rgba(0,0,0,.28)'); gr.addColorStop(0.35, 'rgba(255,255,255,.25)'); gr.addColorStop(1, 'rgba(0,0,0,.35)');
        g.fillStyle = gr; g.fillRect(-17, -31, 34, 32);
        g.restore();
        g.strokeStyle = 'rgba(60,35,10,.55)'; g.lineWidth = 0.8; g.stroke();
        // il pomolo in cima
        g.fillStyle = '#b07a3e';
        g.beginPath(); g.roundRect ? g.roundRect(-2.4, -36, 4.8, 8, 2) : g.rect(-2.4, -36, 4.8, 8); g.fill();
        g.restore();
        return vel > 0.002 || salto > 0 || !!hot;
      }
    };
  };

  // Calice: un calice di vino col gambo che punta al puntatore; il vino ondeggia quando ci si muove (una molla con
  // l'attrito) e si calma da fermo; sui link il calice si inclina per il brindisi e si riempie un po' di più, al clic
  // salta qualche goccia.
  STILI.calice = function () {
    var T = tela(), onda = 0, vo = 0, liv = 0.45, brind = 0, gocce = [], prima = 0;
    var vino = risolvi(CFG.dotColor || '#7b1e2b'), vetro = risolvi(CFG.ringColor || '#d8c7a3');
    window.addEventListener('pointerdown', function () {
      if (meno) return;
      for (var i = 0; i < 6; i++) {
        gocce.push({ x: mx + (Math.random() - 0.5) * 10, y: my - 38, vx: (Math.random() - 0.5) * 3, vy: -2 - Math.random() * 2.5, t: 0 });
      }
      if (gocce.length > 40) gocce.splice(0, gocce.length - 40);
      chiedi();
    }, { passive: true });
    return {
      frame: function (t) {
        var dt = prima ? Math.min(40, t - prima) : 16; prima = t;
        var s = dt / 16;
        // la superficie del vino: spinta dall'accelerazione orizzontale, riportata piana da una molla smorzata
        var spinta = meno ? 0 : Math.max(-0.5, Math.min(0.5, -vx * 0.03));
        vo += ((spinta - onda) * 0.12 - vo * 0.1) * s;
        onda += vo * s;
        vx *= 0.85;
        brind += ((hot ? 1 : 0) - brind) * 0.15 * s;
        liv += ((hot ? 0.62 : 0.45) - liv) * 0.08 * s;
        var g = T.g;
        g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0);
        g.clearRect(0, 0, innerWidth, innerHeight);
        for (var i = gocce.length - 1; i >= 0; i--) {
          var q = gocce[i];
          q.t += dt;
          if (q.t > 1000) { gocce.splice(i, 1); continue; }
          q.vy += 0.25 * s; q.x += q.vx * s; q.y += q.vy * s;
          g.globalAlpha = 1 - q.t / 1000; g.fillStyle = vino;
          g.beginPath(); g.arc(q.x, q.y, 2, 0, 6.283); g.fill();
        }
        g.globalAlpha = 1;
        g.save();
        g.translate(mx, my);
        g.rotate(-0.35 * brind);
        // il piede e il gambo
        g.strokeStyle = vetro; g.lineWidth = 1.6; g.lineCap = 'round';
        g.beginPath(); g.ellipse(0, -1.5, 8, 2, 0, 0, 6.283); g.stroke();
        g.beginPath(); g.moveTo(0, -2); g.lineTo(0, -18); g.stroke();
        // la coppa: tulipano
        var top = -46, fondo = -18;
        g.beginPath();
        g.moveTo(-10, top);
        g.bezierCurveTo(-13, -30, -8, fondo, 0, fondo);
        g.bezierCurveTo(8, fondo, 13, -30, 10, top);
        g.save();
        g.clip();
        // il vino: un rettangolo largo ruotato della superficie (tenuta orizzontale anche se il calice si inclina)
        var yl = fondo - (fondo - top) * liv;
        g.translate(0, yl);
        g.rotate(onda + 0.35 * brind);
        var gr = g.createLinearGradient(0, 0, 0, 30);
        gr.addColorStop(0, vino); gr.addColorStop(1, 'rgba(30,5,10,.95)');
        g.fillStyle = gr;
        g.fillRect(-30, 0, 60, 40);
        g.fillStyle = 'rgba(255,255,255,.28)';
        g.fillRect(-30, -0.6, 60, 1.4);
        g.restore();
        g.strokeStyle = vetro; g.lineWidth = 1.6; g.stroke();
        // il riflesso sul vetro
        g.strokeStyle = 'rgba(255,255,255,.7)'; g.lineWidth = 1.2;
        g.beginPath(); g.moveTo(-7, top + 4); g.quadraticCurveTo(-9, -32, -5, -24); g.stroke();
        g.restore();
        return Math.abs(onda) > 0.002 || Math.abs(vo) > 0.002 || gocce.length > 0 || Math.abs((hot ? 1 : 0) - brind) > 0.01 || t - ultimo < 500;
      }
    };
  };

  /* ── Quindici cursori dei temi del 1° novembre 2026 (seconda serie) ── */

  // il dt del fotogramma, in sedicesimi di secondo (1 = 60 fps), per molle e attriti che non dipendono dagli fps
  function passo(st, t) { var dt = st.prima ? Math.min(40, t - st.prima) : 16; st.prima = t; return dt / 16; }
  function pulisci(T) { T.g.setTransform(T.dpr, 0, 0, T.dpr, 0, 0); T.g.clearRect(0, 0, innerWidth, innerHeight); return T.g; }

  // Elastico: il punto sta sul puntatore, un elastico lo lega a un capo che lo rincorre con una molla. Teso si
  // assottiglia, lento si ingrossa; sui link l'elastico si ritira e il punto si allarga.
  STILI.elastico = function () {
    var T = tela(), st = {}, ax = mx, ay = my, avx = 0, avy = 0, r = 4;
    var banda = risolvi(CFG.ringColor || '#2f8f83'), punto = risolvi(CFG.dotColor || '#ff6b4a');
    return {
      frame: function (t) {
        var s = passo(st, t);
        var k = hot ? 0.3 : 0.07;
        avx = (avx + (mx - ax) * k * s) * Math.pow(0.78, s); avy = (avy + (my - ay) * k * s) * Math.pow(0.78, s);
        if (meno) { ax = mx; ay = my; avx = avy = 0; }
        ax += avx * s; ay += avy * s;
        r += ((hot ? 9 : 4) - r) * 0.2 * s;
        var g = pulisci(T), d = Math.hypot(mx - ax, my - ay);
        if (d > 1) {
          g.strokeStyle = banda; g.lineCap = 'round';
          g.lineWidth = Math.max(1.2, 6 - d / 22);
          // un poco curvo: il capo è indietro e la corda si piega verso la velocità
          var cx = (mx + ax) / 2 - avy * 0.8, cy = (my + ay) / 2 + avx * 0.8;
          g.beginPath(); g.moveTo(ax, ay); g.quadraticCurveTo(cx, cy, mx, my); g.stroke();
          g.fillStyle = banda; g.beginPath(); g.arc(ax, ay, 3, 0, 6.283); g.fill();
        }
        g.fillStyle = punto; g.beginPath(); g.arc(mx, my, r, 0, 6.283); g.fill();
        return d > 0.4 || Math.abs(avx) + Math.abs(avy) > 0.05 || Math.abs((hot ? 9 : 4) - r) > 0.05;
      }
    };
  };

  // Respiro: un cerchio che respira piano (otto secondi a respiro) e segue il puntatore con calma; quando ci si muove
  // svelti si stringe, sui link trattiene il respiro e si riempie appena.
  STILI.respiro = function () {
    var T = tela(), st = {}, cx = mx, cy = my, stretto = 0, pieno = 0;
    var anello = risolvi(CFG.ringColor || '#c9c3ff'), centro = risolvi(CFG.dotColor || '#ffffff');
    return {
      frame: function (t) {
        var s = passo(st, t);
        var v = Math.hypot(vx, vy); vx *= 0.9; vy *= 0.9;
        cx += (mx - cx) * (meno ? 1 : 0.12) * s; cy += (my - cy) * (meno ? 1 : 0.12) * s;
        stretto += (Math.min(1, v / 18) - stretto) * 0.1 * s;
        pieno += ((hot ? 1 : 0) - pieno) * 0.08 * s;
        var fase = meno ? 0.5 : (Math.sin(t / 8000 * 6.283 - 1.57) + 1) / 2;
        var R = (14 + 10 * fase * (1 - pieno)) * (1 - 0.35 * stretto) + pieno * 6;
        var g = pulisci(T);
        g.globalAlpha = 0.12 + 0.12 * pieno; g.fillStyle = anello;
        g.beginPath(); g.arc(cx, cy, R, 0, 6.283); g.fill();
        g.globalAlpha = 0.9; g.strokeStyle = anello; g.lineWidth = 1.4;
        g.beginPath(); g.arc(cx, cy, R, 0, 6.283); g.stroke();
        // l'anello esterno, più tenue, va in controfase
        g.globalAlpha = 0.35 * (1 - pieno); g.lineWidth = 1;
        g.beginPath(); g.arc(cx, cy, R + 7 + 5 * (1 - fase), 0, 6.283); g.stroke();
        g.globalAlpha = 1; g.fillStyle = centro;
        g.beginPath(); g.arc(mx, my, 2.5, 0, 6.283); g.fill();
        // respira finché ci si muove e per quindici secondi dopo l'ultimo movimento
        return (!meno && (t - ultimo < 15000 || !!hot)) || Math.abs(mx - cx) + Math.abs(my - cy) > 0.3;
      }
    };
  };

  // Luna: una piccola luna segue il puntatore e cambia fase con la strada fatta (un ciclo ogni 1800 px); fermandosi
  // resta nella sua fase, sui link è piena con l'alone. Muovendosi lascia qualche stella che si spegne.
  STILI.luna = function () {
    var T = tela(), st = {}, strada = 0, piena = 0, stelle = [], lx = mx, ly = my;
    var chiara = risolvi(CFG.dotColor || '#f4ead2'), cielo = risolvi(CFG.ringColor || '#1a2140');
    return {
      punto: function (t) {
        var d = Math.hypot(mx - lx, my - ly); lx = mx; ly = my;
        strada += d;
        if (!meno && d > 6 && Math.random() < 0.35) {
          stelle.push({ x: mx + (Math.random() - 0.5) * 50, y: my + (Math.random() - 0.5) * 50, t: t, r: 0.8 + Math.random() * 1.2 });
          if (stelle.length > 30) stelle.shift();
        }
      },
      frame: function (t) {
        var s = passo(st, t);
        piena += ((hot ? 1 : 0) - piena) * 0.1 * s;
        var fase = ((strada / 1800) % 1);
        fase = fase + (0.5 - fase) * piena; // 0,5 = piena
        var g = pulisci(T), R = 9 + 3 * piena;
        for (var i = stelle.length - 1; i >= 0; i--) {
          var e = (t - stelle[i].t) / 900;
          if (e >= 1) { stelle.splice(i, 1); continue; }
          g.globalAlpha = Math.sin(e * Math.PI); g.fillStyle = chiara;
          g.beginPath(); g.arc(stelle[i].x, stelle[i].y, stelle[i].r, 0, 6.283); g.fill();
        }
        g.globalAlpha = 1;
        if (piena > 0.02) {
          var al = g.createRadialGradient(mx, my, R, mx, my, R * 2.6);
          al.addColorStop(0, 'rgba(255,248,225,' + (0.35 * piena) + ')'); al.addColorStop(1, 'rgba(255,248,225,0)');
          g.fillStyle = al; g.beginPath(); g.arc(mx, my, R * 2.6, 0, 6.283); g.fill();
        }
        // il disco chiaro, poi l'ombra: un disco del colore del cielo spostato di lato (fase 0 nuova, 0,5 piena)
        g.fillStyle = chiara; g.beginPath(); g.arc(mx, my, R, 0, 6.283); g.fill();
        var off = Math.cos(fase * 6.283) * 2 * R * (fase < 0.5 ? 1 : -1);
        if (Math.abs(off) < 2 * R - 0.3) {
          g.save(); g.beginPath(); g.arc(mx, my, R + 0.3, 0, 6.283); g.clip();
          g.globalAlpha = 0.92; g.fillStyle = cielo;
          g.beginPath(); g.arc(mx + off, my, R + 0.6, 0, 6.283); g.fill();
          g.restore();
        }
        g.strokeStyle = chiara; g.globalAlpha = 0.6; g.lineWidth = 1;
        g.beginPath(); g.arc(mx, my, R, 0, 6.283); g.stroke(); g.globalAlpha = 1;
        return stelle.length > 0 || Math.abs((hot ? 1 : 0) - piena) > 0.01;
      }
    };
  };

  // Volante: un volante a tre razze sul puntatore che sterza con la velocità orizzontale e torna dritto da solo;
  // sui link il clacson: il mozzo si accende e il volante vibra un attimo.
  STILI.volante = function () {
    var T = tela(), st = {}, ang = 0, va = 0, clacson = 0;
    var corona = risolvi(CFG.ringColor || '#2b2b2b'), mozzo = risolvi(CFG.dotColor || '#e2b44b');
    var eraHot = false;
    return {
      frame: function (t) {
        var s = passo(st, t);
        if (hot && !eraHot) clacson = 1;
        eraHot = !!hot;
        var meta = meno ? 0 : Math.max(-1.6, Math.min(1.6, vx * 0.09));
        vx *= 0.88;
        va += ((meta - ang) * 0.12 - va * 0.25) * s; ang += va * s;
        clacson = Math.max(0, clacson - 0.04 * s);
        var tremo = clacson > 0 && !meno ? Math.sin(t / 18) * 1.5 * clacson : 0;
        var g = pulisci(T);
        g.save(); g.translate(mx + tremo, my); g.rotate(ang);
        g.strokeStyle = corona; g.lineWidth = 4;
        g.beginPath(); g.arc(0, 0, 15, 0, 6.283); g.stroke();
        g.lineWidth = 2.6;
        for (var k = 0; k < 3; k++) {
          var a = 1.571 + k * 2.094;
          g.beginPath(); g.moveTo(Math.cos(a) * 4, Math.sin(a) * 4); g.lineTo(Math.cos(a) * 14, Math.sin(a) * 14); g.stroke();
        }
        g.fillStyle = hot ? mozzo : corona;
        g.beginPath(); g.arc(0, 0, 4.5, 0, 6.283); g.fill();
        // il segno del centro, in alto, per vedere quanto si sterza
        g.fillStyle = mozzo; g.fillRect(-1.5, -17.5, 3, 4);
        g.restore();
        return Math.abs(ang) > 0.002 || Math.abs(va) > 0.002 || clacson > 0;
      }
    };
  };

  // Messa a fuoco: quattro angoli attorno al puntatore, larghi quando ci si muove (sfocato) e stretti da fermi; sui
  // link corrono sui bordi dell'elemento come l'autofocus di una cinepresa e si accende il puntino verde.
  STILI.fuoco = function () {
    var T = tela(), st = {}, box = { x: mx - 18, y: my - 18, w: 36, h: 36 }, spinta = 0;
    var col = risolvi(CFG.ringColor || '#ffffff'), ok = risolvi(CFG.dotColor || '#41d37e');
    return {
      frame: function (t) {
        var s = passo(st, t);
        var v = Math.hypot(vx, vy); vx *= 0.85; vy *= 0.85;
        spinta += (Math.min(1, v / 14) - spinta) * 0.15 * s;
        var m;
        if (hot && hot.getBoundingClientRect) {
          var r = hot.getBoundingClientRect();
          m = { x: r.left - 6, y: r.top - 6, w: r.width + 12, h: r.height + 12 };
          if (m.w > 520 || m.h > 260) m = null;
        }
        if (!m) { var h = 16 + 14 * spinta; m = { x: mx - h, y: my - h, w: 2 * h, h: 2 * h }; }
        var k = meno ? 1 : 0.25 * s;
        box.x += (m.x - box.x) * k; box.y += (m.y - box.y) * k; box.w += (m.w - box.w) * k; box.h += (m.h - box.h) * k;
        var g = pulisci(T), L = Math.min(10, box.w / 3, box.h / 3);
        g.strokeStyle = col; g.lineWidth = 1.6; g.lineCap = 'square';
        g.beginPath();
        var X = box.x, Y = box.y, W = box.w, H = box.h;
        g.moveTo(X, Y + L); g.lineTo(X, Y); g.lineTo(X + L, Y);
        g.moveTo(X + W - L, Y); g.lineTo(X + W, Y); g.lineTo(X + W, Y + L);
        g.moveTo(X + W, Y + H - L); g.lineTo(X + W, Y + H); g.lineTo(X + W - L, Y + H);
        g.moveTo(X + L, Y + H); g.lineTo(X, Y + H); g.lineTo(X, Y + H - L);
        g.stroke();
        g.fillStyle = hot ? ok : col;
        g.beginPath(); g.arc(mx, my, hot ? 3 : 1.6, 0, 6.283); g.fill();
        return Math.abs(m.x - box.x) + Math.abs(m.w - box.w) + Math.abs(m.y - box.y) > 0.3 || spinta > 0.01;
      }
    };
  };

  // Quota: due linee di quota dal puntatore ai bordi della finestra, con le frecce e la misura in pixel come su una
  // tavola di progetto; sui link la quota misura l'elemento (larghezza × altezza).
  STILI.quota = function () {
    var T = tela(), st = {};
    var linea = risolvi(CFG.ringColor || '#1d1d1b'), segno = risolvi(CFG.dotColor || '#d9502e');
    function freccia(g, x, y, ang) {
      g.save(); g.translate(x, y); g.rotate(ang);
      g.beginPath(); g.moveTo(0, 0); g.lineTo(6, -2.5); g.lineTo(6, 2.5); g.closePath(); g.fill(); g.restore();
    }
    return {
      frame: function (t) {
        passo(st, t);
        var g = pulisci(T);
        g.font = '600 10px ui-monospace, Menlo, monospace'; g.textBaseline = 'bottom';
        g.strokeStyle = linea; g.fillStyle = linea; g.lineWidth = 0.8; g.globalAlpha = 0.85;
        if (hot && hot.getBoundingClientRect) {
          var r = hot.getBoundingClientRect(), y = r.top - 10, x = r.right + 10;
          g.beginPath(); g.moveTo(r.left, y); g.lineTo(r.right, y); g.moveTo(x, r.top); g.lineTo(x, r.bottom);
          g.moveTo(r.left, y - 4); g.lineTo(r.left, y + 4); g.moveTo(r.right, y - 4); g.lineTo(r.right, y + 4);
          g.moveTo(x - 4, r.top); g.lineTo(x + 4, r.top); g.moveTo(x - 4, r.bottom); g.lineTo(x + 4, r.bottom); g.stroke();
          g.fillStyle = segno; g.textAlign = 'center';
          g.fillText(Math.round(r.width) + ' × ' + Math.round(r.height), (r.left + r.right) / 2, y - 3);
        } else {
          g.setLineDash([3, 3]);
          g.beginPath(); g.moveTo(0, my); g.lineTo(mx - 8, my); g.moveTo(mx, 0); g.lineTo(mx, my - 8); g.stroke();
          g.setLineDash([]);
          freccia(g, 0, my, 0); freccia(g, mx - 8, my, Math.PI); freccia(g, mx, 0, Math.PI / 2); freccia(g, mx, my - 8, -Math.PI / 2);
          g.textAlign = 'center'; g.fillText(String(Math.round(mx)), mx / 2, my - 4);
          g.save(); g.translate(mx - 4, my / 2); g.rotate(-Math.PI / 2); g.fillText(String(Math.round(my)), 0, 0); g.restore();
        }
        g.globalAlpha = 1; g.strokeStyle = segno; g.lineWidth = 1.4;
        g.beginPath(); g.moveTo(mx - 7, my); g.lineTo(mx + 7, my); g.moveTo(mx, my - 7); g.lineTo(mx, my + 7); g.stroke();
        return false;
      }
    };
  };

  // Chiave: una chiave d'ottone con la punta sul puntatore, inclinata nel verso del movimento; sui link gira di un
  // quarto come nella serratura; al clic tintinna.
  STILI.chiave = function () {
    var T = tela(), st = {}, giraK = 0, ang = -0.6, tin = 0;
    var ottone = risolvi(CFG.dotColor || '#c9a24a'), scuro = risolvi(CFG.ringColor || '#3a2c16');
    window.addEventListener('pointerdown', function () { if (!meno) { tin = 1; chiedi(); } }, { passive: true });
    return {
      frame: function (t) {
        var s = passo(st, t);
        giraK += ((hot ? 1 : 0) - giraK) * 0.18 * s;
        var meta = -0.6 + Math.max(-0.5, Math.min(0.5, vx * 0.03));
        vx *= 0.9;
        ang += (meta - ang) * 0.12 * s;
        tin = Math.max(0, tin - 0.03 * s);
        var g = pulisci(T);
        g.save(); g.translate(mx, my);
        g.rotate(ang + (meno ? 0 : Math.sin(t / 30) * 0.12 * tin));
        // la chiave va dalla punta (0,0) verso l'anello in alto a destra; girando, l'anello si schiaccia (vista di lato)
        var sc = Math.cos(giraK * 1.571) * 0.85 + 0.15;
        g.scale(1, sc);
        g.fillStyle = ottone; g.strokeStyle = scuro; g.lineWidth = 1;
        // il gambo
        g.beginPath(); g.rect(0, -2, 22, 4); g.fill(); g.stroke();
        // i denti, vicino alla punta
        g.beginPath(); g.moveTo(2, 2); g.lineTo(2, 7); g.lineTo(5, 7); g.lineTo(5, 5); g.lineTo(8, 5); g.lineTo(8, 7); g.lineTo(11, 7);
        g.lineTo(11, 2); g.closePath(); g.fill(); g.stroke();
        // l'anello
        g.beginPath(); g.arc(30, 0, 8, 0, 6.283); g.fill(); g.stroke();
        g.fillStyle = 'rgba(0,0,0,.55)'; g.beginPath(); g.arc(30, 0, 3.4, 0, 6.283); g.fill();
        g.restore();
        return Math.abs((hot ? 1 : 0) - giraK) > 0.01 || Math.abs(meta - ang) > 0.003 || tin > 0;
      }
    };
  };

  // Riflesso: il punto e, sotto, il suo riflesso nell'acqua capovolto e mosso dalle onde; muovendosi si allargano
  // cerchi schiacciati sull'acqua; sui link il riflesso si schiarisce.
  STILI.riflesso = function () {
    var T = tela(), st = {}, cerchi = [], ultimoC = 0, chiaro = 0;
    var col = risolvi(CFG.dotColor || '#f2e6c8'), acqua = risolvi(CFG.ringColor || '#9cc3d5');
    return {
      punto: function (t) {
        if (meno || t - ultimoC < 140) return;
        ultimoC = t; cerchi.push({ x: mx, y: my + 22, t: t });
        if (cerchi.length > 12) cerchi.shift();
      },
      frame: function (t) {
        var s = passo(st, t);
        chiaro += ((hot ? 1 : 0) - chiaro) * 0.12 * s;
        var g = pulisci(T);
        for (var i = cerchi.length - 1; i >= 0; i--) {
          var e = (t - cerchi[i].t) / 1300;
          if (e >= 1) { cerchi.splice(i, 1); continue; }
          g.globalAlpha = (1 - e) * 0.6; g.strokeStyle = acqua; g.lineWidth = 1;
          g.beginPath(); g.ellipse(cerchi[i].x, cerchi[i].y, 6 + e * 34, 2 + e * 8, 0, 0, 6.283); g.stroke();
        }
        g.globalAlpha = 1; g.fillStyle = col;
        g.beginPath(); g.arc(mx, my, 5 + 2 * chiaro, 0, 6.283); g.fill();
        // il riflesso: strisce orizzontali sfasate, sempre più rade verso il basso
        g.globalAlpha = 0.35 + 0.35 * chiaro;
        for (var k = 0; k < 7; k++) {
          var yy = my + 16 + k * 2.6, w = (6 - k * 0.6) * (1 + 0.3 * chiaro);
          var dx = meno ? 0 : Math.sin(t / 220 + k * 1.3) * (1.5 + k * 0.5);
          if (w > 0) g.fillRect(mx - w + dx, yy, 2 * w, 1.4);
        }
        g.globalAlpha = 1;
        return (!meno && (t - ultimo < 15000 || !!hot)) || cerchi.length > 0 || Math.abs((hot ? 1 : 0) - chiaro) > 0.01;
      }
    };
  };

  // Battito: un piccolo monitor accanto al puntatore col tracciato del cuore che scorre; i battiti al minuto salgono
  // quando ci si muove svelti e sui link, poi tornano a riposo.
  STILI.battito = function () {
    var T = tela(), st = {}, bpm = 64, traccia = [], fase = 0;
    var linea = risolvi(CFG.dotColor || '#ff3b3b'), cornice = risolvi(CFG.ringColor || '#ffffff');
    var N = 60;
    for (var i = 0; i < N; i++) traccia.push(0);
    function onda(f) {
      // la forma di un battito: P, QRS, T su una fase da 0 a 1
      if (f < 0.1) return Math.sin(f / 0.1 * Math.PI) * 0.12;
      if (f < 0.16) return 0;
      if (f < 0.19) return -0.18;
      if (f < 0.23) return 1;
      if (f < 0.27) return -0.32;
      if (f < 0.4) return 0;
      if (f < 0.55) return Math.sin((f - 0.4) / 0.15 * Math.PI) * 0.22;
      return 0;
    }
    return {
      frame: function (t) {
        var s = passo(st, t);
        var v = Math.hypot(vx, vy); vx *= 0.92; vy *= 0.92;
        var meta = hot ? 120 : 64 + Math.min(70, v * 4);
        bpm += (meta - bpm) * 0.02 * s;
        if (!meno) {
          fase += bpm / 60 / 60 * s * 1.0;
          traccia.push(onda(fase % 1)); traccia.shift();
        }
        var g = pulisci(T), X = mx + 14, Y = my + 10, W = 62, H = 26;
        g.globalAlpha = 0.85; g.fillStyle = 'rgba(8,12,20,.72)';
        g.beginPath(); g.roundRect ? g.roundRect(X, Y, W, H, 4) : g.rect(X, Y, W, H); g.fill();
        g.globalAlpha = 1; g.strokeStyle = linea; g.lineWidth = 1.4; g.lineJoin = 'round';
        g.beginPath();
        for (var k = 0; k < N; k++) {
          var px = X + 2 + k * (W - 4) / (N - 1), py = Y + H * 0.62 - traccia[k] * H * 0.5;
          if (k) g.lineTo(px, py); else g.moveTo(px, py);
        }
        g.stroke();
        g.fillStyle = cornice; g.font = '600 8px ui-monospace, Menlo, monospace'; g.textAlign = 'right'; g.textBaseline = 'top';
        g.fillText(Math.round(bpm) + ' bpm', X + W - 3, Y + 2);
        g.fillStyle = linea; g.beginPath(); g.arc(mx, my, 3, 0, 6.283); g.fill();
        return !meno && (t - ultimo < 15000 || !!hot);
      }
    };
  };

  // Scodinzolo: un punto col codino che scodinzola dietro, dalla parte opposta al movimento; più svelti si va, più
  // scodinzola; sui link è contentissimo.
  STILI.scodinzolo = function () {
    var T = tela(), st = {}, dir = 3.6, felice = 0, fase = 0;
    var pelo = risolvi(CFG.ringColor || '#8a5a2b'), naso = risolvi(CFG.dotColor || '#2a1a0e');
    return {
      frame: function (t) {
        var s = passo(st, t);
        var v = Math.hypot(vx, vy);
        if (v > 0.6) {
          var meta = Math.atan2(-vy, -vx), d = meta - dir;
          while (d > Math.PI) d -= 6.283; while (d < -Math.PI) d += 6.283;
          dir += d * 0.15 * s;
        }
        vx *= 0.9; vy *= 0.9;
        felice += (((hot ? 1 : 0) + Math.min(1, v / 12)) - felice) * 0.08 * s;
        fase += (0.12 + 0.35 * felice) * s;
        var g = pulisci(T);
        var amp = meno ? 0 : 0.35 + 0.45 * Math.min(1, felice);
        var a = dir + Math.sin(fase) * amp;
        // il codino: una curva dal punto verso dietro, con la punta più chiara
        var L = 20, bx = mx + Math.cos(a) * L, by = my + Math.sin(a) * L;
        var cx = mx + Math.cos(a - Math.sin(fase) * 0.6) * L * 0.6, cy = my + Math.sin(a - Math.sin(fase) * 0.6) * L * 0.6;
        g.strokeStyle = pelo; g.lineCap = 'round'; g.lineWidth = 5;
        g.beginPath(); g.moveTo(mx, my); g.quadraticCurveTo(cx, cy, bx, by); g.stroke();
        g.lineWidth = 3; g.strokeStyle = 'rgba(255,255,255,.35)';
        g.beginPath(); g.moveTo((cx + bx) / 2, (cy + by) / 2); g.lineTo(bx, by); g.stroke();
        g.fillStyle = naso; g.beginPath(); g.arc(mx, my, 4.5, 0, 6.283); g.fill();
        return !meno && (felice > 0.03 || t - ultimo < 1500);
      }
    };
  };

  // Pioggerella: una nuvoletta sopra il puntatore che, quando ci si muove, lascia cadere qualche goccia che si
  // apre in un cerchietto sul punto d'arrivo; sui link piove un po' di più.
  STILI.pioggerella = function () {
    var T = tela(), st = {}, gocce = [], cx = mx, cy = my;
    var nube = risolvi(CFG.ringColor || '#e9efe6'), acqua = risolvi(CFG.dotColor || '#4c9bd6');
    return {
      punto: function () {
        if (meno) return;
        var n = hot ? 2 : 1;
        for (var i = 0; i < n; i++) if (Math.random() < 0.5) {
          gocce.push({ x: cx + (Math.random() - 0.5) * 16, y: cy - 16, vy: 1.5, fine: my + 4 + Math.random() * 10, t: 0, giu: true });
        }
        if (gocce.length > 50) gocce.splice(0, gocce.length - 50);
      },
      frame: function (t) {
        var s = passo(st, t);
        cx += (mx - cx) * (meno ? 1 : 0.2) * s; cy += (my - 26 - cy) * (meno ? 1 : 0.2) * s;
        var g = pulisci(T);
        for (var i = gocce.length - 1; i >= 0; i--) {
          var q = gocce[i];
          if (q.giu) {
            q.vy += 0.35 * s; q.y += q.vy * s;
            g.strokeStyle = acqua; g.lineWidth = 1.5; g.globalAlpha = 0.85;
            g.beginPath(); g.moveTo(q.x, q.y - 4); g.lineTo(q.x, q.y); g.stroke();
            if (q.y >= q.fine) { q.giu = false; q.t = 0; }
          } else {
            q.t += 16 * s;
            var e = q.t / 420;
            if (e >= 1) { gocce.splice(i, 1); continue; }
            g.globalAlpha = 1 - e; g.strokeStyle = acqua; g.lineWidth = 1;
            g.beginPath(); g.ellipse(q.x, q.fine, 2 + e * 7, 1 + e * 2.2, 0, 0, 6.283); g.stroke();
          }
        }
        g.globalAlpha = 0.95; g.fillStyle = nube;
        g.beginPath();
        g.arc(cx - 8, cy, 6, 0, 6.283); g.arc(cx, cy - 4, 8, 0, 6.283); g.arc(cx + 8, cy, 6, 0, 6.283);
        g.rect(cx - 8, cy, 16, 6);
        g.fill();
        g.globalAlpha = 1; g.fillStyle = acqua; g.beginPath(); g.arc(mx, my, 2.2, 0, 6.283); g.fill();
        return gocce.length > 0 || Math.abs(mx - cx) + Math.abs(my - 26 - cy) > 0.3;
      }
    };
  };

  // Ugello: l'ugello di una stampante 3D sul puntatore che stende un filo di plastica calda: arancio vicino
  // all'ugello, si raffredda in grigio lungo la strada e sparisce in un secondo e mezzo; sui link l'ugello si scalda.
  STILI.ugello = function () {
    var T = tela(), st = {}, pts = [], caldo = 0;
    var filo = risolvi(CFG.dotColor || '#ff7a1a'), freddo = risolvi(CFG.ringColor || '#9aa0a6');
    function rgb(c) { var m = c.match(/[\d.]+/g) || [0, 0, 0]; return [+m[0], +m[1], +m[2]]; }
    var A = rgb(filo), B = rgb(freddo);
    var VITA = 1500;
    return {
      punto: function (t) { if (!meno) { pts.push({ x: mx, y: my, t: t }); if (pts.length > 240) pts.shift(); } },
      frame: function (t) {
        var s = passo(st, t);
        caldo += ((hot ? 1 : 0) - caldo) * 0.12 * s;
        while (pts.length && t - pts[0].t > VITA) pts.shift();
        var g = pulisci(T);
        g.lineCap = 'round'; g.lineWidth = 3;
        for (var i = 1; i < pts.length; i++) {
          var e = (t - pts[i].t) / VITA, f = Math.min(1, e * 2.2);
          g.strokeStyle = 'rgb(' + Math.round(A[0] + (B[0] - A[0]) * f) + ',' + Math.round(A[1] + (B[1] - A[1]) * f) + ',' +
            Math.round(A[2] + (B[2] - A[2]) * f) + ')';
          g.globalAlpha = 1 - Math.max(0, e - 0.6) / 0.4;
          g.beginPath(); g.moveTo(pts[i - 1].x, pts[i - 1].y); g.lineTo(pts[i].x, pts[i].y); g.stroke();
        }
        g.globalAlpha = 1;
        // l'ugello: un tronco di cono d'ottone con la punta sul puntatore
        g.fillStyle = caldo > 0.05 ? 'rgb(' + Math.round(200 + 55 * caldo) + ',' + Math.round(150 - 40 * caldo) + ',60)' : '#c8a24c';
        g.beginPath(); g.moveTo(mx - 2, my - 3); g.lineTo(mx + 2, my - 3); g.lineTo(mx + 7, my - 14); g.lineTo(mx - 7, my - 14); g.closePath(); g.fill();
        g.fillStyle = '#2b2b2b'; g.fillRect(mx - 9, my - 22, 18, 8);
        if (caldo > 0.05) {
          g.globalAlpha = caldo * 0.5; g.fillStyle = filo;
          g.beginPath(); g.arc(mx, my - 2, 6, 0, 6.283); g.fill(); g.globalAlpha = 1;
        }
        return pts.length > 0 || Math.abs((hot ? 1 : 0) - caldo) > 0.01;
      }
    };
  };

  // Passo passo: il cursore di un motore passo-passo: un quadratino che scatta di cella in cella su una griglia di
  // 16 px (con una piccola frenata), e attorno la griglia che si accende e si spegne; sui link la cella si riempie.
  STILI.passopasso = function () {
    var T = tela(), st = {}, P = 16, gx = Math.round(mx / P) * P, gy = Math.round(my / P) * P, luce = 0;
    var col = risolvi(CFG.ringColor || '#1f2a33'), acc = risolvi(CFG.dotColor || '#f2c200');
    return {
      frame: function (t) {
        var s = passo(st, t);
        var tx = Math.round(mx / P) * P, ty = Math.round(my / P) * P;
        var k = meno ? 1 : 0.35 * s;
        gx += (tx - gx) * k; gy += (ty - gy) * k;
        luce += ((t - ultimo < 600 ? 1 : 0) - luce) * 0.08 * s;
        var g = pulisci(T);
        if (luce > 0.02) {
          g.strokeStyle = col; g.lineWidth = 1;
          for (var i = -2; i <= 3; i++) {
            g.globalAlpha = luce * 0.25 * (1 - Math.abs(i - 0.5) / 3.5);
            g.beginPath(); g.moveTo(tx - 2 * P - P / 2, ty + i * P - P / 2); g.lineTo(tx + 3 * P - P / 2, ty + i * P - P / 2); g.stroke();
            g.beginPath(); g.moveTo(tx + i * P - P / 2, ty - 2 * P - P / 2); g.lineTo(tx + i * P - P / 2, ty + 3 * P - P / 2); g.stroke();
          }
        }
        g.globalAlpha = 1;
        if (hot) { g.fillStyle = acc; g.globalAlpha = 0.85; g.fillRect(gx - P / 2, gy - P / 2, P, P); g.globalAlpha = 1; }
        g.strokeStyle = acc; g.lineWidth = 2; g.strokeRect(gx - P / 2 + 1, gy - P / 2 + 1, P - 2, P - 2);
        g.fillStyle = col; g.fillRect(mx - 1.5, my - 1.5, 3, 3);
        return Math.abs(tx - gx) + Math.abs(ty - gy) > 0.2 || luce > 0.02;
      }
    };
  };

  // Mosaico: il puntatore posa piastrelline quadrate su una griglia: ognuna si gira entrando (come una tessera
  // voltata) e sbiadisce; tre colori a turno; sui link le tessere sono più grandi.
  STILI.mosaico = function () {
    var T = tela(), st = {}, tessere = [], visti = {}, n = 0;
    var c1 = risolvi(CFG.ringColor || '#1d4e89'), c2 = risolvi(CFG.dotColor || '#e0a526'), c3 = risolvi(CFG.hotColor || '#c6463a');
    var COL = [c1, c2, c3];
    return {
      punto: function (t) {
        if (meno) return;
        var P = hot ? 14 : 10, x = Math.floor(mx / P) * P, y = Math.floor(my / P) * P, key = P + ':' + x + ':' + y;
        if (visti[key] && t - visti[key] < 900) return;
        visti[key] = t;
        tessere.push({ x: x, y: y, P: P, t: t, c: COL[n++ % 3] });
        if (tessere.length > 80) tessere.shift();
      },
      frame: function (t) {
        passo(st, t);
        var g = pulisci(T);
        for (var i = tessere.length - 1; i >= 0; i--) {
          var q = tessere[i], e = (t - q.t) / 1100;
          if (e >= 1) { tessere.splice(i, 1); continue; }
          var gira = Math.min(1, e * 5), sx = Math.abs(Math.cos((1 - gira) * Math.PI / 2));
          g.globalAlpha = 1 - Math.max(0, e - 0.5) * 2; g.fillStyle = q.c;
          g.fillRect(q.x + q.P / 2 - (q.P - 1) * sx / 2, q.y, (q.P - 1) * sx, q.P - 1);
        }
        g.globalAlpha = 1; g.strokeStyle = c1; g.lineWidth = 1.5;
        g.strokeRect(mx - 5, my - 5, 10, 10);
        if (tessere.length > 200) visti = {};
        return tessere.length > 0;
      }
    };
  };

  // Nappa: una nappa di passamaneria appesa al puntatore con un cordoncino: oscilla come un pendolo quando ci si
  // muove e si ferma piano; sui link il cordoncino si accorcia e la nappa sale.
  STILI.nappa = function () {
    var T = tela(), st = {}, ang = 0, va = 0, lung = 30;
    var cordone = risolvi(CFG.ringColor || '#c8a45c'), fili = risolvi(CFG.dotColor || '#7a1f2b');
    return {
      frame: function (t) {
        var s = passo(st, t);
        // il pendolo: l'accelerazione orizzontale del punto d'appoggio spinge, la gravità riporta giù
        va += (-Math.sin(ang) * 0.012 - vx * 0.004 - va * 0.04) * s;
        vx *= 0.8; vy *= 0.8;
        if (meno) { ang = 0; va = 0; }
        ang += va * s;
        lung += ((hot ? 14 : 30) - lung) * 0.15 * s;
        var g = pulisci(T);
        var bx = mx + Math.sin(ang) * lung, by = my + Math.cos(ang) * lung;
        g.strokeStyle = cordone; g.lineWidth = 1.6;
        g.beginPath(); g.moveTo(mx, my); g.lineTo(bx, by); g.stroke();
        g.save(); g.translate(bx, by); g.rotate(-ang);
        // la testa della nappa e la frangia
        g.fillStyle = cordone; g.beginPath(); g.ellipse(0, 3, 4, 4.5, 0, 0, 6.283); g.fill();
        g.fillStyle = cordone; g.fillRect(-4.5, 7, 9, 2);
        g.strokeStyle = fili; g.lineWidth = 1.1;
        for (var k = -4; k <= 4; k++) {
          var curva = meno ? 0 : Math.sin(t / 160 + k) * va * 30;
          g.beginPath(); g.moveTo(k * 1.1, 9); g.quadraticCurveTo(k * 1.4 + curva, 15, k * 1.6 + curva * 1.4, 22); g.stroke();
        }
        g.restore();
        g.fillStyle = cordone; g.beginPath(); g.arc(mx, my, 2.2, 0, 6.283); g.fill();
        return Math.abs(ang) > 0.002 || Math.abs(va) > 0.0005 || Math.abs((hot ? 14 : 30) - lung) > 0.1;
      }
    };
  };

  // Nota (2 nov 2026, per il Coro Paganella): il puntatore è una nota col gambo, del colore principale e col bordo
  // chiaro (si vede sul chiaro e sullo scuro). Muovendosi lascia dietro qualche croma piccola che sale ondeggiando e
  // sbiadisce, come un canto che parte; sui link la nota diventa una croma con la cediglia che sventola.
  STILI.nota = function () {
    var T = tela(), st = {}, note = [], strada = 0, px = mx, py = my, n = 0, cresce = 0;
    var corpo = risolvi(CFG.ringColor || '#8a2523'), bordo = risolvi(CFG.dotColor || '#fbfaf6');
    function disegna(g, x, y, k, croma, onda, colore) {
      // la testa ovale inclinata, il gambo a destra verso l'alto e, per la croma, la cediglia
      g.save(); g.translate(x, y);
      g.lineWidth = 1.6 * k; g.strokeStyle = bordo; g.fillStyle = colore;
      g.save(); g.rotate(-0.35); g.beginPath(); g.ellipse(0, 0, 6.2 * k, 4.4 * k, 0, 0, 6.283); g.fill(); g.stroke(); g.restore();
      var gx = 5.4 * k;
      g.beginPath(); g.moveTo(gx, -1.5 * k); g.lineTo(gx, -21 * k);
      if (croma) g.quadraticCurveTo(gx + (8 + onda) * k, -15 * k, gx + (5 + onda * 0.6) * k, -7 * k);
      g.lineWidth = 3.4 * k; g.strokeStyle = bordo; g.stroke();
      g.lineWidth = 1.8 * k; g.strokeStyle = colore; g.stroke();
      g.restore();
    }
    return {
      punto: function (t) {
        if (meno) return;
        strada += Math.hypot(mx - px, my - py); px = mx; py = my;
        if (strada < 90) return;
        strada = 0;
        note.push({ x: mx, y: my, t: t, dx: (n % 2 ? 1 : -1) * (6 + Math.random() * 10), k: 0.55 + Math.random() * 0.25,
                    chiara: n % 3 === 2 });
        n++;
        if (note.length > 14) note.shift();
      },
      frame: function (t) {
        var s = passo(st, t);
        cresce += ((hot ? 1 : 0) - cresce) * 0.2 * s;
        var g = pulisci(T);
        for (var i = note.length - 1; i >= 0; i--) {
          var q = note[i], e = (t - q.t) / 1400;
          if (e >= 1) { note.splice(i, 1); continue; }
          g.globalAlpha = (1 - e) * 0.9;
          disegna(g, q.x + q.dx * e + Math.sin(e * 9 + i) * 6, q.y - 10 - e * 70, q.k, true, Math.sin(t / 120 + i) * 2,
                  q.chiara ? bordo : corpo);
        }
        g.globalAlpha = 1;
        disegna(g, mx, my, 1 + cresce * 0.25, cresce > 0.5, meno ? 0 : Math.sin(t / 110) * 2.5, corpo);
        return note.length > 0 || Math.abs((hot ? 1 : 0) - cresce) > 0.01 || cresce > 0.5;
      }
    };
  };

  /* ── cursore della sezione (2 nov 2026) ──
     Una sezione con data-olo-cursore ha un cursore suo: entrandoci il cursore cambia, uscendo torna quello della
     pagina. Ogni stile si crea la prima volta che serve e poi resta, coi suoi nodi nascosti quando non è attivo; i suoi
     ascoltatori del puntatore (i clic dei coriandoli…) agiscono solo mentre è lui il cursore. «nessuno» = la freccia di
     sistema. L'anello del sito lo disegna Olobuild_Magnetic_Cursor (CFG.anelloSito): si nasconde nelle sezioni. */
  var istanze = {};
  var attivo = null;       // lo stile attivo (null = nessuno)
  var attivoChiave = '';   // 'b:' fuori dalle sezioni, 's:' dentro una sezione col suo cursore
  function crea(nome) {
    if (istanze[nome]) return istanze[nome];
    var wAdd = window.addEventListener, dAdd = document.addEventListener;
    var filtra = function (orig, bersaglio) {
      return function (tipo, fn, opz) {
        var avvolta = (typeof fn === 'function' && /^(pointer|mouse|click|touch)/.test(tipo))
          ? function (e) { if (attivo === nome) return fn.call(this, e); } : fn;
        return orig.call(bersaglio, tipo, avvolta, opz);
      };
    };
    nodiNuovi = [];
    window.addEventListener = filtra(wAdd, window);
    document.addEventListener = filtra(dAdd, document);
    try {
      istanze[nome] = { stile: STILI[nome](), nodi: nodiNuovi };
    } finally {
      window.addEventListener = wAdd;
      document.addEventListener = dAdd;
      nodiNuovi = null;
    }
    return istanze[nome];
  }
  function anelloSito(visibile) {
    var m = window.__oloMagCur;
    if (!m || !m.uid) return;
    var nodi = document.querySelectorAll('.' + m.uid + '-ring, .' + m.uid + '-dot');
    for (var i = 0; i < nodi.length; i++) nodi[i].style.visibility = visibile ? '' : 'hidden';
    html.classList.toggle(m.uid + '-on', visibile);
  }
  function attiva(nome, base) {
    if (!STILI[nome]) nome = 'nessuno';
    var chiave = (base ? 'b:' : 's:') + nome;
    if (chiave === attivoChiave) return;
    attivoChiave = chiave;
    if (attivo && istanze[attivo] && attivo !== nome) {
      istanze[attivo].nodi.forEach(function (e) { e.style.display = 'none'; });
    }
    var ist = nome === 'nessuno' ? null : crea(nome);
    attivo = ist ? nome : null;
    stile = ist ? ist.stile : null;
    if (ist) ist.nodi.forEach(function (e) { e.style.display = ''; });
    html.classList.toggle(P + '-on', !!(ist && CFG.hideSys));
    if (CFG.anelloSito) anelloSito(!!base);
    chiedi();
  }
  function sezioneDi(t) {
    var s = t && t.closest ? t.closest('[data-olo-cursore]') : null;
    attiva(s ? s.getAttribute('data-olo-cursore') : STILE, !s);
  }

  /* ── avvio ── */
  var avviato = false;
  function avvia() {
    if (!document.body || avviato) return;
    avviato = true;
    attiva(STILE, true);
    html.classList.add(P + '-fuori');
    window.addEventListener('pointermove', function (e) {
      if (e.pointerType === 'touch') return;
      var t = performance.now();
      var dt = Math.max(8, t - ultimo);
      vx = (e.clientX - mx) / dt * 16; vy = (e.clientY - my) / dt * 16;
      if (html.classList.contains(P + '-fuori')) { vx = vy = 0; }
      mx = e.clientX; my = e.clientY; ultimo = t;
      html.classList.remove(P + '-fuori');
      if (stile && stile.punto) stile.punto(t);
      chiedi();
    }, { passive: true });
    document.addEventListener('pointerover', function (e) {
      if (e.pointerType !== 'touch') sezioneDi(e.target);
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
