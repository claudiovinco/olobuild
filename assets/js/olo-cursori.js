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
  // Su telefoni e tablet niente cursore, tranne la Torcia (18 nov 2026): lì la pagina al buio è il tema, non un ornamento
  // del puntatore, e la luce sta ferma al centro dello schermo mentre la pagina le scorre sotto.
  var touchSolo = mm('(hover: none)') || mm('(pointer: coarse)');
  if (touchSolo && CFG.style !== 'torcia') return;
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
  // opz.mondo: lo stile lascia cose nella pagina (scie, particelle, tagli, gocce) che devono restare dove sono state
  // disegnate. Lì niente rimpicciolimento di tutta la tela verso il puntatore (14 nov 2026, l'utente: «parte
  // dell'effetto si trascina quando trascino il puntatore»: scalando attorno al puntatore che si muove, ogni punto
  // fermo scivolava col 40% del movimento); il disegno sul puntatore si rimpicciolisce da solo con T.glifo(g, x, y)
  // al posto di g.translate(x, y).
  function tela(opz) {
    var mondo = !!(opz && opz.mondo);
    var c = nodo('tela', 'canvas');
    var dpr = Math.min(2, window.devicePixelRatio || 1);
    function misura() {
      c.width = innerWidth * dpr; c.height = innerHeight * dpr;
      c.style.width = innerWidth + 'px'; c.style.height = innerHeight + 'px';
    }
    misura();
    window.addEventListener('resize', misura);
    var g = c.getContext('2d');
    // Disegni piccoli (13 nov 2026, l'utente: «quando il puntatore ha una parte grafica, non esagerare… spesso la vedo
    // gigante che copre l'animazione»): tutto ciò che uno stile disegna si rimpicciolisce di SCALA verso l'angolo in
    // basso a destra della freccia di sistema (la punta resta libera e il disegno le sta accanto, come prima ma più
    // piccolo). Le cancellature e le dissolvenze a tutta tela restano a tutta tela, se no lascerebbero aloni.
    var SCALA = +CFG.scalaDisegno || 0.6, AX = 10, AY = 14;
    var setT = g.setTransform.bind(g), pulisci = g.clearRect.bind(g), riempi = g.fillRect.bind(g);
    if (!mondo) g.setTransform = function (a, b, cc, d, e, f) {
      if (a && typeof a === 'object') return setT(a);
      var px = (mx + AX) * dpr, py = (my + AY) * dpr;
      setT(a * SCALA, b * SCALA, cc * SCALA, d * SCALA, e * SCALA + px * (1 - SCALA), f * SCALA + py * (1 - SCALA));
    };
    // Il disegno sul puntatore negli stili «mondo»: stesso rimpicciolimento verso l'angolo della freccia, solo per lui.
    function glifo(gg, x, y) {
      var ax = mx + AX, ay = my + AY;
      gg.translate(ax + (x - ax) * SCALA, ay + (y - ay) * SCALA);
      gg.scale(SCALA, SCALA);
    }
    function tutta(x, y, w, h) { return x <= 0 && y <= 0 && w >= innerWidth - 1 && h >= innerHeight - 1; }
    g.clearRect = function (x, y, w, h) {
      if (!tutta(x, y, w, h)) return pulisci(x, y, w, h);
      g.save(); setT(1, 0, 0, 1, 0, 0); pulisci(0, 0, c.width, c.height); g.restore();
    };
    g.fillRect = function (x, y, w, h) {
      if (!tutta(x, y, w, h)) return riempi(x, y, w, h);
      g.save(); setT(1, 0, 0, 1, 0, 0); riempi(0, 0, c.width, c.height); g.restore();
    };
    return { c: c, g: g, dpr: dpr, glifo: glifo };
  }

  // Cometa: il puntatore lascia una scia di luce che si assottiglia e svanisce in mezzo secondo.
  STILI.cometa = function () {
    var T = tela({ mondo: true }), testa = nodo('punto'), pts = [];
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
    var T = tela({ mondo: true }), p = nodo('punto'), sc = [];
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
    var T = tela({ mondo: true }), p = nodo('punto'), f = [];
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
    var T = tela({ mondo: true }), ago = nodo('punto'), pts = [];
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
    var T = tela({ mondo: true }), p = nodo('punto'), pts = [];
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
    var T = tela({ mondo: true }), p = nodo('punto'), cc = [];
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
    var T = tela({ mondo: true }), p = nodo('punto'), f = [];
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
    var T = tela({ mondo: true }), p = nodo('punto'), f = [], ultimoP = 0;
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
    var T = tela({ mondo: true }), p = nodo('punto'), f = [], ultimoP = 0;
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
    var T = tela({ mondo: true }), p = nodo('punto'), f = [], ultimoP = 0;
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
    var T = tela({ mondo: true }), p = nodo('punto'), f = [], ultimoP = 0;
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
    var T = tela({ mondo: true }), p = nodo('punto'), f = [], lato = 1, ux = mx, uy = my;
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
    var T = tela({ mondo: true }), N = 16, L = 6, pts = [];
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
    var T = tela({ mondo: true }), pts = [], prima = 0;
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
    var T = tela({ mondo: true }), p = nodo('punto'), grani = [], prima = 0;
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
    var T = tela({ mondo: true }), p = nodo('punto'), pp = [], prima = 0, cx = mx, cy = my;
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
    var T = tela({ mondo: true }), N = 18, L = 5.5, pts = [], prima = null, scossa = 0;
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
    var T = tela({ mondo: true }), x = mx, y = my, ang = 0, prima = 0, sparsi = [];
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
    var T = tela({ mondo: true }), x = mx, y = my, pieg = 0, prima = 0, fiocchi = [];
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

  // Pennino: il pennino d'una stilografica con la punta sul puntatore e girata come la freccia del mouse (in alto a
  // sinistra, 8 nov 2026: prima guardava in basso a destra), il taglio e il foro di sfiato; scrive un tratto
  // da calligrafo (sottile in un verso, pieno nell'altro, come la punta tronca) che si asciuga e svanisce; sui link
  // il pennino si drizza, al clic lascia una macchia d'inchiostro con due schizzi.
  STILI.pennino = function () {
    var T = tela({ mondo: true }), pts = [], macchie = [], ang = 2.39, prima = 0;
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
        // 2.39 = la punta verso l'alto a sinistra, il corpo in basso a destra come la freccia; sui link 2.89, quasi dritto
        var aT = (hot ? 2.89 : 2.39) + (meno ? 0 : Math.max(-0.35, Math.min(0.35, vx * 0.02)));
        ang += (aT - ang) * 0.18;
        vx *= 0.85;
        g.save();
        T.glifo(g, mx, my);
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
    var T = tela({ mondo: true }), ang = 0, va = 0, apre = 1, gocce = [], prima = 0, tic = 0;
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
        T.glifo(g, mx, my);
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
    var T = tela({ mondo: true }), onda = 0, vo = 0, liv = 0.45, brind = 0, gocce = [], prima = 0;
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
        T.glifo(g, mx, my);
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
    var T = tela({ mondo: true }), st = {}, strada = 0, piena = 0, stelle = [], lx = mx, ly = my;
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
    var T = tela({ mondo: true }), st = {}, cerchi = [], ultimoC = 0, chiaro = 0;
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
    var T = tela({ mondo: true }), st = {}, gocce = [], cx = mx, cy = my;
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
    var T = tela({ mondo: true }), st = {}, pts = [], caldo = 0;
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
    var T = tela({ mondo: true }), st = {}, tessere = [], visti = {}, n = 0;
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
  // Con la freccia di sistema visibile (hide_system spento, 3 nov 2026: «si fatica a capire dove premere») la nota
  // accompagna la freccia in basso a destra e la punta resta libera; senza freccia la testa della nota è il puntatore.
  STILI.nota = function () {
    var T = tela({ mondo: true }), st = {}, note = [], strada = 0, px = mx, py = my, n = 0, cresce = 0;
    var corpo = risolvi(CFG.ringColor || '#8a2523'), bordo = risolvi(CFG.dotColor || '#fbfaf6');
    var accanto = !CFG.hideSys, ox = accanto ? 20 : 0, oy = accanto ? 27 : 0;
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
        note.push({ x: mx + ox, y: my + oy, t: t, dx: (n % 2 ? 1 : -1) * (6 + Math.random() * 10), k: 0.55 + Math.random() * 0.25,
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
        // sui link la manina di sistema è più larga della freccia: la nota si scosta un po' di più
        disegna(g, mx + ox + cresce * (accanto ? 4 : 0), my + oy + cresce * (accanto ? 2 : 0), 1 + cresce * 0.25, cresce > 0.5,
                meno ? 0 : Math.sin(t / 110) * 2.5, corpo);
        return note.length > 0 || Math.abs((hot ? 1 : 0) - cresce) > 0.01 || cresce > 0.5;
      }
    };
  };

  /* ── i dieci cursori dei marchi del 3 nov 2026 ── Con la freccia di sistema visibile (hide_system spento) il disegno
     sta accanto alla freccia, in basso a destra, e la punta resta libera; senza freccia il disegno è il puntatore. */
  function scosta() { return CFG.hideSys ? [0, 0] : [18, 22]; }
  function frecciaPath(g) {
    g.beginPath(); g.moveTo(0, 0); g.lineTo(0, 22); g.lineTo(5.5, 17); g.lineTo(9.5, 26); g.lineTo(13, 24.5);
    g.lineTo(9, 15.8); g.lineTo(16, 15.8); g.closePath();
  }

  // Specchio (per Rovescio): dalla parte opposta dello schermo, ribaltato rispetto al centro, corre un secondo puntatore
  // rovesciato che fa ogni gesto al contrario; un filo tratteggiato li lega passando dal centro. Sui link si riempie.
  STILI.specchio = function () {
    var T = tela(), st = {}, gx = innerWidth - mx, gy = innerHeight - my, pieno = 0;
    var filo = risolvi(CFG.ringColor || '#111111'), tinta = risolvi(CFG.dotColor || '#ff5a1f');
    function freccia(g, x, y, ang, fill) {
      g.save(); g.translate(x, y); g.rotate(ang); g.scale(1.15, 1.15); frecciaPath(g);
      if (fill > 0.02) { g.globalAlpha = fill; g.fillStyle = tinta; g.fill(); g.globalAlpha = 1; }
      g.lineWidth = 1.6; g.strokeStyle = tinta; g.stroke(); g.restore();
    }
    return {
      frame: function (t) {
        var s = passo(st, t), tx = innerWidth - mx, ty = innerHeight - my, f = meno ? 1 : Math.min(1, 0.25 * s);
        gx += (tx - gx) * f; gy += (ty - gy) * f;
        pieno += ((hot ? 1 : 0) - pieno) * 0.2 * s;
        var g = pulisci(T);
        g.setLineDash([2, 7]); g.lineWidth = 1; g.strokeStyle = filo; g.globalAlpha = 0.22;
        g.beginPath(); g.moveTo(mx, my); g.lineTo(gx, gy); g.stroke(); g.setLineDash([]); g.globalAlpha = 1;
        g.fillStyle = tinta; g.beginPath(); g.arc(innerWidth / 2, innerHeight / 2, 2.5, 0, 6.283); g.fill();
        if (CFG.hideSys) freccia(g, mx, my, 0, 1);
        freccia(g, gx, gy, Math.PI, pieno);
        return Math.abs(tx - gx) + Math.abs(ty - gy) > 0.3 || Math.abs((hot ? 1 : 0) - pieno) > 0.01;
      }
    };
  };

  // Prisma (per Cangiante): un piccolo prisma che scompone la luce: muovendosi lascia dietro un ventaglio di raggi
  // dell'arcobaleno lungo quanto la velocità e una scia di puntini colorati; sui link il ventaglio si apre.
  STILI.prisma = function () {
    var T = tela({ mondo: true }), st = {}, scie = [], lung = 0, dir = Math.PI * 0.75, giro = 0, n = 0;
    var SPETTRO = ['#ff3b3b', '#ff9a1f', '#ffd60a', '#2fd36b', '#1fb6ff', '#5a5cff', '#b44dff'];
    var bordo = risolvi(CFG.ringColor || '#ffffff');
    return {
      punto: function (t) {
        if (meno) return;
        scie.push({ x: mx, y: my, t: t, c: SPETTRO[n++ % 7] });
        if (scie.length > 40) scie.shift();
      },
      frame: function (t) {
        var s = passo(st, t), v = Math.hypot(vx, vy), o = scosta();
        if (v > 0.4) dir = Math.atan2(-vy, -vx);
        vx *= 0.86; vy *= 0.86;
        lung += (Math.min(80, v * 5) + (hot ? 34 : 0) - lung) * 0.2 * s;
        if (!meno) giro += (0.01 + v * 0.004) * s;
        var cx = mx + o[0], cy = my + o[1];
        var g = pulisci(T);
        g.lineCap = 'round';
        for (var i = 0; i < 7; i++) {
          var a = dir + (i - 3) * (hot ? 0.12 : 0.055);
          g.strokeStyle = SPETTRO[i]; g.globalAlpha = 0.75; g.lineWidth = 2.4;
          g.beginPath(); g.moveTo(cx, cy); g.lineTo(cx + Math.cos(a) * lung, cy + Math.sin(a) * lung); g.stroke();
        }
        for (var j = scie.length - 1; j >= 0; j--) {
          var q = scie[j], e = (t - q.t) / 650;
          if (e >= 1) { scie.splice(j, 1); continue; }
          g.globalAlpha = (1 - e) * 0.8; g.fillStyle = q.c;
          g.beginPath(); g.arc(q.x + o[0], q.y + o[1], 2.4 * (1 - e) + 0.6, 0, 6.283); g.fill();
        }
        g.globalAlpha = 1;
        g.save(); T.glifo(g, cx, cy); g.rotate(giro);
        g.beginPath(); g.moveTo(0, -9); g.lineTo(8, 5.5); g.lineTo(-8, 5.5); g.closePath();
        g.fillStyle = 'rgba(255,255,255,.35)'; g.fill(); g.lineWidth = 1.6; g.strokeStyle = bordo; g.stroke(); g.restore();
        return lung > 0.5 || scie.length > 0 || v > 0.05;
      }
    };
  };

  // Strati (per A Cipolla): cinque anelli uno dentro l'altro, come gli strati di una cipolla o di chi si veste a strati:
  // ognuno insegue il puntatore con un po' più di ritardo, così muovendosi si sfogliano; sui link si stringono in uno.
  STILI.strati = function () {
    var N = 5, anelli = [], pos = [];
    for (var i = 0; i < N; i++) {
      var e = nodo('anello'), r = 10 + i * 9;
      e.style.width = e.style.height = r + 'px';
      e.style.margin = (-r / 2) + 'px 0 0 ' + (-r / 2) + 'px';
      e.style.borderColor = i % 2 ? 'var(--oc-dot)' : 'var(--oc-ring)';
      e.style.opacity = String(1 - i * 0.15);
      anelli.push(e); pos.push({ x: mx, y: my, k: 1 });
    }
    return {
      frame: function () {
        var mosso = false;
        for (var i = 0; i < N; i++) {
          var f = meno ? 1 : 0.36 - i * 0.06, p = pos[i];
          p.x += (mx - p.x) * f; p.y += (my - p.y) * f;
          var kk = hot ? (i === 0 ? 1.5 : 0.3 + 0.04 * i) : 1;
          p.k += (kk - p.k) * 0.2;
          sposta(anelli[i], p.x, p.y, ' scale(' + p.k.toFixed(3) + ')');
          if (Math.abs(mx - p.x) + Math.abs(my - p.y) > 0.2 || Math.abs(kk - p.k) > 0.01) mosso = true;
        }
        return mosso;
      }
    };
  };

  // Zip (per Cerniera): il cursore di una cerniera lampo con la sua linguetta: dove passa lascia i denti della zip chiusi,
  // che dopo un attimo si riaprono e svaniscono; la linguetta dondola col moto e sui link si alza.
  STILI.zip = function () {
    var T = tela({ mondo: true }), st = {}, denti = [], strada = 0, px = mx, py = my, dir = Math.PI / 2, ang = 0, va = 0, n = 0, su = 0;
    var metallo = risolvi(CFG.ringColor || '#c9ccd1'), nastro = risolvi(CFG.dotColor || '#ff5a1f');
    return {
      punto: function (t) {
        var d = Math.hypot(mx - px, my - py);
        if (d > 0.5) dir = Math.atan2(my - py, mx - px);
        strada += d; px = mx; py = my;
        if (meno) return;
        while (strada > 5) { strada -= 5; denti.push({ x: mx, y: my, a: dir, t: t, l: n++ % 2 }); }
        if (denti.length > 140) denti.splice(0, denti.length - 140);
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        va += (-Math.sin(ang) * 0.02 - vx * 0.006 - va * 0.06) * s; ang += va * s; vx *= 0.8; vy *= 0.8;
        su += ((hot ? 1 : 0) - su) * 0.2 * s;
        if (meno) { ang = 0; va = 0; }
        var g = pulisci(T);
        for (var i = denti.length - 1; i >= 0; i--) {
          var q = denti[i], e = (t - q.t) / 1100;
          if (e >= 1) { denti.splice(i, 1); continue; }
          var apri = Math.max(0, e - 0.35) * 12, nx = -Math.sin(q.a), ny = Math.cos(q.a), lato = q.l ? 1 : -1;
          var cx = q.x + o[0] + nx * lato * (2 + apri), cy = q.y + o[1] + ny * lato * (2 + apri);
          g.globalAlpha = 1 - e;
          g.save(); g.translate(cx, cy); g.rotate(q.a);
          g.fillStyle = nastro; g.fillRect(-2.6, lato * 1.5 - 2.5, 5.2, 5);
          g.fillStyle = metallo; g.fillRect(-1.4, -2.4, 2.8, 4.8);
          g.restore();
        }
        g.globalAlpha = 1;
        g.save(); T.glifo(g, mx + o[0], my + o[1]); g.rotate(dir - Math.PI / 2);
        g.fillStyle = metallo; g.strokeStyle = 'rgba(0,0,0,.4)'; g.lineWidth = 1;
        g.beginPath(); g.moveTo(-6.5, -8); g.lineTo(6.5, -8); g.lineTo(4.5, 7); g.lineTo(-4.5, 7); g.closePath(); g.fill(); g.stroke();
        g.rotate(ang - su * 2.6);
        g.beginPath(); g.moveTo(-3, 6); g.lineTo(3, 6); g.lineTo(3.8, 21); g.lineTo(-3.8, 21); g.closePath(); g.fill(); g.stroke();
        g.fillStyle = 'rgba(0,0,0,.35)'; g.fillRect(-1.6, 13, 3.2, 5);
        g.restore();
        return denti.length > 0 || Math.abs(ang) > 0.002 || Math.abs(va) > 0.0005 || Math.abs((hot ? 1 : 0) - su) > 0.01;
      }
    };
  };

  // Spaiato (per Spaiato): due compagni che non si somigliano, un tondo e un quadrato di due colori: uno corre dietro al
  // puntatore svelto, l'altro arriva tardi e un po' storto, e a ogni scatto veloce si scambiano il passo. Sui link il
  // tondo si gonfia e il quadrato si mette di traverso.
  STILI.spaiato = function () {
    var a = nodo('punto'), b = nodo('quadro');
    a.style.cssText += ';width:14px;height:14px;margin:-7px 0 0 -7px;background:var(--oc-dot)';
    b.style.cssText += ';width:13px;height:13px;margin:-6.5px 0 0 -6.5px;border:2.5px solid var(--oc-ring);box-sizing:border-box';
    var ax = mx, ay = my, bx = mx, by = my, rot = 12, scambio = 0, quando = 0;
    return {
      frame: function (t) {
        var o = scosta();
        if (!meno && t - quando > 2600 && Math.hypot(vx, vy) > 9) { scambio = 1 - scambio; quando = t; }
        vx *= 0.9; vy *= 0.9;
        var fa = meno ? 1 : (scambio ? 0.09 : 0.34), fb = meno ? 1 : (scambio ? 0.34 : 0.09);
        ax += (mx + o[0] - ax) * fa; ay += (my + o[1] - ay) * fa;
        bx += (mx + o[0] + 15 - bx) * fb; by += (my + o[1] + 7 - by) * fb;
        rot += ((hot ? 45 : 12) - rot) * 0.15;
        sposta(a, ax, ay, hot ? ' scale(1.8)' : '');
        sposta(b, bx, by, ' rotate(' + rot.toFixed(1) + 'deg)');
        return Math.abs(mx + o[0] - ax) + Math.abs(my + o[1] - ay) + Math.abs(mx + o[0] + 15 - bx) + Math.abs(my + o[1] + 7 - by) > 0.4
          || Math.abs((hot ? 45 : 12) - rot) > 0.1;
      }
    };
  };

  // Centrifuga (per Trentagradi): l'oblò di una lavatrice con un calzino dentro: il cestello gira più forte quanto più
  // veloce corre il puntatore e rallenta piano fino a fermarsi; sui link lo sportello si apre.
  STILI.centrifuga = function () {
    var T = tela(), st = {}, giro = 0, vel = 0, apre = 0;
    var bordo = risolvi(CFG.ringColor || '#d8dde3'), panno = risolvi(CFG.dotColor || '#3a7bd5');
    return {
      frame: function (t) {
        var s = passo(st, t), v = Math.hypot(vx, vy), o = scosta(), R = 15;
        vx *= 0.85; vy *= 0.85;
        vel += (Math.min(0.7, v * 0.035) - vel) * 0.06 * s;
        if (meno) vel = 0;
        giro += vel * s;
        apre += ((hot ? 1 : 0) - apre) * 0.18 * s;
        var cx = mx + o[0] + 4, cy = my + o[1] + 4;
        var g = pulisci(T);
        g.fillStyle = 'rgba(170,205,235,.28)'; g.beginPath(); g.arc(cx, cy, R, 0, 6.283); g.fill();
        g.save(); g.beginPath(); g.arc(cx, cy, R - 1, 0, 6.283); g.clip();
        g.fillStyle = 'rgba(110,170,225,.45)';
        g.beginPath(); g.arc(cx, cy + 22 - vel * 6, 20, 0, 6.283); g.fill();
        g.translate(cx, cy); g.rotate(giro);
        g.fillStyle = panno; g.fillRect(-3, -10, 6, 11);
        g.beginPath(); g.ellipse(2, 2, 6, 3.4, 0, 0, 6.283); g.fill();
        g.fillStyle = 'rgba(255,255,255,.7)'; g.fillRect(-3, -10, 6, 2.2);
        g.restore();
        g.lineWidth = 3.2; g.strokeStyle = bordo; g.beginPath(); g.arc(cx, cy, R + 1.6, 0, 6.283); g.stroke();
        if (apre > 0.02) {
          g.save(); g.translate(cx - R - 1.6, cy); g.scale(1 - apre * 0.7, 1);
          g.lineWidth = 2.4; g.strokeStyle = bordo; g.fillStyle = 'rgba(170,205,235,.2)';
          g.beginPath(); g.arc(-(R + 3), 0, R + 3, 0, 6.283); g.fill(); g.stroke(); g.restore();
        }
        return vel > 0.002 || v > 0.05 || Math.abs((hot ? 1 : 0) - apre) > 0.01;
      }
    };
  };

  // Sonno (per Sonnambulo): una falce di luna che lascia qualche stellina quando si muove; ferma per due secondi
  // sbadiglia e fa salire le Z. Sui link si sveglia: la luna diventa piena.
  STILI.sonno = function () {
    var T = tela({ mondo: true }), st = {}, stelle = [], strada = 0, px = mx, py = my, fermo = performance.now(), pieno = 0, attesa = 0;
    var luna = risolvi(CFG.ringColor || '#f3e7b3');
    function stella(g, x, y, r) {
      g.beginPath();
      for (var k = 0; k < 8; k++) { var a = k * Math.PI / 4, rr = k % 2 ? r * 0.38 : r; g.lineTo(x + Math.cos(a) * rr, y + Math.sin(a) * rr); }
      g.closePath(); g.fill();
    }
    return {
      punto: function (t) {
        fermo = t;
        clearTimeout(attesa); attesa = setTimeout(chiedi, 2050);
        if (meno) return;
        strada += Math.hypot(mx - px, my - py); px = mx; py = my;
        if (strada > 55) { strada = 0; stelle.push({ x: mx, y: my, t: t, r: 2 + Math.random() * 2.2 }); if (stelle.length > 16) stelle.shift(); }
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta(), cx = mx + o[0], cy = my + o[1];
        pieno += ((hot ? 1 : 0) - pieno) * 0.18 * s;
        var g = pulisci(T);
        g.fillStyle = luna;
        for (var i = stelle.length - 1; i >= 0; i--) {
          var q = stelle[i], e = (t - q.t) / 1200;
          if (e >= 1) { stelle.splice(i, 1); continue; }
          g.globalAlpha = 1 - e; stella(g, q.x + o[0], q.y + o[1] + e * 10, q.r * (1 - e * 0.4));
        }
        g.globalAlpha = 1;
        g.save();
        g.beginPath(); g.arc(cx, cy, 9, 0, 6.283); g.fill();
        if (pieno < 0.98) {
          g.globalCompositeOperation = 'destination-out';
          g.beginPath(); g.arc(cx + 5 + pieno * 14, cy - 3 - pieno * 6, 8, 0, 6.283); g.fill();
        }
        g.restore();
        var dorme = !meno && !hot && t - fermo > 2000;
        if (dorme) {
          var k = ((t - fermo - 2000) % 2400) / 2400;
          for (var z = 0; z < 3; z++) {
            var e2 = (k + z / 3) % 1;
            g.globalAlpha = Math.sin(e2 * Math.PI); g.font = '700 ' + (9 + z * 3) + 'px system-ui,sans-serif';
            g.fillText('z', cx + 9 + e2 * 14, cy - 10 - e2 * 26);
          }
          g.globalAlpha = 1;
        }
        return dorme || stelle.length > 0 || Math.abs((hot ? 1 : 0) - pieno) > 0.01;
      }
    };
  };

  // Cupola (per Scroscio): un ombrello trasparente a cupola sopra il puntatore, col manico a uncino: muovendosi si
  // inclina e dal bordo cadono le gocce; sui link si chiude un po'. Fa coppia con la Pioggia delle Particelle: la cupola
  // ha il raggio dell'ombrello della pioggia (48), sotto non piove.
  STILI.cupola = function () {
    var T = tela({ mondo: true }), st = {}, gocce = [], chiude = 0, incl = 0;
    var telo = risolvi(CFG.ringColor || '#ffffff'), manico = risolvi(CFG.dotColor || '#ffcc00');
    return {
      punto: function (t) {
        if (meno || Math.random() > 0.4) return;
        var lato = Math.random() < 0.5 ? -1 : 1;
        gocce.push({ x: mx + lato * 47 * (1 - chiude * 0.45), y: my, vy: 0.5, t: t });
        if (gocce.length > 30) gocce.shift();
      },
      frame: function (t) {
        var s = passo(st, t);
        chiude += ((hot ? 1 : 0) - chiude) * 0.2 * s;
        incl += (Math.max(-0.3, Math.min(0.3, -vx * 0.02)) - incl) * 0.12 * s; vx *= 0.85; vy *= 0.85;
        if (meno) incl = 0;
        var R = 48 * (1 - chiude * 0.45), g = pulisci(T);
        g.strokeStyle = telo; g.lineWidth = 1.4; g.lineCap = 'round';
        for (var i = gocce.length - 1; i >= 0; i--) {
          var q = gocce[i]; q.vy += 0.35 * s; q.y += q.vy * s;
          if (q.y > innerHeight + 10 || t - q.t > 1400) { gocce.splice(i, 1); continue; }
          g.globalAlpha = 0.75; g.beginPath(); g.moveTo(q.x, q.y); g.lineTo(q.x, q.y - 3 - q.vy * 1.2); g.stroke();
        }
        g.globalAlpha = 1;
        g.save(); T.glifo(g, mx, my); g.rotate(incl);
        g.beginPath(); g.ellipse(0, 0, R, R * 0.62, 0, Math.PI, 2 * Math.PI); g.closePath();
        g.fillStyle = 'rgba(255,255,255,.18)'; g.fill(); g.lineWidth = 1.6; g.strokeStyle = telo; g.stroke();
        g.globalAlpha = 0.45; g.lineWidth = 1;
        for (var k = -2; k <= 2; k++) {
          if (!k) continue;
          g.beginPath(); g.moveTo(0, -R * 0.62); g.quadraticCurveTo(k * R * 0.22, -R * 0.42, k * R * 0.45, 0); g.stroke();
        }
        g.globalAlpha = 1;
        g.strokeStyle = manico; g.lineWidth = 2.6;
        g.beginPath(); g.moveTo(0, -R * 0.62); g.lineTo(0, 24); g.arc(-5, 24, 5, 0, Math.PI); g.stroke();
        g.restore();
        return gocce.length > 0 || Math.abs((hot ? 1 : 0) - chiude) > 0.01 || Math.abs(incl) > 0.002;
      }
    };
  };

  // Gomitolo (per Gomitolo): un gomitolo di lana che rotola mentre ci si muove e srotola dietro di sé il filo, morbido e
  // ondulato, che poi si riavvolge; sui link il gomitolo si gonfia.
  STILI.gomitolo = function () {
    var T = tela({ mondo: true }), st = {}, filo = [], giro = 0, px = mx, py = my, gonfio = 0;
    var lana = risolvi(CFG.ringColor || '#e06c4f'), segno = risolvi(CFG.dotColor || '#ffffff');
    return {
      punto: function (t) {
        giro += Math.hypot(mx - px, my - py) / 10; px = mx; py = my;
        if (meno) return;
        filo.push({ x: mx, y: my, t: t });
        if (filo.length > 90) filo.shift();
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta(), R = 9 + gonfio * 4;
        gonfio += ((hot ? 1 : 0) - gonfio) * 0.18 * s;
        while (filo.length && t - filo[0].t > 1600) filo.shift();
        var g = pulisci(T);
        if (filo.length > 1) {
          g.lineCap = 'round'; g.lineJoin = 'round'; g.strokeStyle = lana; g.lineWidth = 2.2; g.globalAlpha = 0.85;
          g.beginPath();
          for (var i = 0; i < filo.length; i++) {
            var q = filo[i], w = Math.sin(i * 0.7 + t / 300) * 2.5;
            if (i) g.lineTo(q.x + o[0] + w, q.y + o[1] + w); else g.moveTo(q.x + o[0] + w, q.y + o[1] + w);
          }
          g.lineTo(mx + o[0], my + o[1]); g.stroke(); g.globalAlpha = 1;
        }
        var cx = mx + o[0], cy = my + o[1];
        g.fillStyle = lana; g.beginPath(); g.arc(cx, cy, R, 0, 6.283); g.fill();
        g.save(); g.beginPath(); g.arc(cx, cy, R, 0, 6.283); g.clip(); T.glifo(g, cx, cy); g.rotate(giro);
        g.strokeStyle = segno; g.globalAlpha = 0.5; g.lineWidth = 1;
        for (var k = -3; k <= 3; k++) { g.beginPath(); g.ellipse(0, k * 3, R * 1.15, R * 0.34, 0.5, 0, 6.283); g.stroke(); }
        g.restore(); g.globalAlpha = 1;
        return filo.length > 0 || Math.abs((hot ? 1 : 0) - gonfio) > 0.01;
      }
    };
  };

  // XXL (per Fuorimisura): un puntatore enorme, quattro volte la freccia di sistema, con la taglia scritta dentro; sui
  // link si stringe alla misura normale e la taglia diventa «S». La punta resta dove punta il mouse.
  STILI.xxl = function () {
    var T = tela(), st = {}, k = 4, ax = mx, ay = my;
    var pieno = risolvi(CFG.ringColor || '#ff3d00'), scritta = risolvi(CFG.dotColor || '#111111');
    return {
      frame: function (t) {
        var s = passo(st, t), meta = hot ? 1.15 : 4;
        k += (meta - k) * 0.2 * s;
        ax += (mx - ax) * (meno ? 1 : 0.5); ay += (my - ay) * (meno ? 1 : 0.5);
        var g = pulisci(T);
        g.save(); g.translate(ax, ay); g.scale(k, k); frecciaPath(g);
        g.fillStyle = pieno; g.fill(); g.lineWidth = 1.6 / k; g.strokeStyle = scritta; g.stroke(); g.restore();
        g.fillStyle = scritta;
        if (k > 2) { g.font = '800 ' + Math.round(k * 4) + 'px system-ui,sans-serif'; g.fillText('XXL', ax + k * 1.3, ay + k * 13.5); }
        else { g.font = '800 ' + Math.round(k * 6.5) + 'px system-ui,sans-serif'; g.fillText('S', ax + k * 2.2, ay + k * 14); }
        return Math.abs(meta - k) > 0.01 || Math.abs(mx - ax) + Math.abs(my - ay) > 0.3;
      }
    };
  };

  // Raspa (per Raspa): la raspa che graffia il denim: una lima a righe incrociate che, muovendosi, lascia dietro di sé
  // graffi chiari che sbiadiscono in un attimo; sui link si inclina come per dare l'ultima passata.
  STILI.raspa = function () {
    var T = tela({ mondo: true }), st = {}, graffi = [], strada = 0, px = mx, py = my, incl = 0;
    var lima = risolvi(CFG.ringColor || '#9aa7bd'), segno = risolvi(CFG.dotColor || '#e8f0fb');
    return {
      punto: function (t) {
        var d = Math.hypot(mx - px, my - py), a = Math.atan2(my - py, mx - px);
        strada += d; px = mx; py = my;
        if (meno) return;
        while (strada > 7) {
          strada -= 7;
          graffi.push({ x: mx + (Math.random() - 0.5) * 10, y: my + (Math.random() - 0.5) * 10, a: a + (Math.random() - 0.5) * 0.5,
                        l: 6 + Math.random() * 12, t: t });
        }
        if (graffi.length > 120) graffi.splice(0, graffi.length - 120);
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        incl += ((hot ? -0.55 : 0) - incl) * 0.18 * s;
        var g = pulisci(T);
        g.lineCap = 'round';
        for (var i = graffi.length - 1; i >= 0; i--) {
          var q = graffi[i], e = (t - q.t) / 900;
          if (e >= 1) { graffi.splice(i, 1); continue; }
          g.globalAlpha = (1 - e) * 0.85; g.strokeStyle = segno; g.lineWidth = 1.4;
          g.beginPath(); g.moveTo(q.x - Math.cos(q.a) * q.l / 2, q.y - Math.sin(q.a) * q.l / 2);
          g.lineTo(q.x + Math.cos(q.a) * q.l / 2, q.y + Math.sin(q.a) * q.l / 2); g.stroke();
        }
        g.globalAlpha = 1;
        g.save(); T.glifo(g, mx + o[0], my + o[1]); g.rotate(-0.6 + incl);
        g.fillStyle = lima; g.fillRect(-4, -15, 8, 22);
        g.strokeStyle = 'rgba(0,0,0,.35)'; g.lineWidth = 1;
        for (var k = -13; k < 6; k += 3) { g.beginPath(); g.moveTo(-4, k); g.lineTo(4, k + 3); g.moveTo(4, k); g.lineTo(-4, k + 3); g.stroke(); }
        g.fillStyle = 'rgba(0,0,0,.55)'; g.fillRect(-2.5, 7, 5, 9);
        g.restore();
        return graffi.length > 0 || Math.abs((hot ? -0.55 : 0) - incl) > 0.005;
      }
    };
  };

  // Gruccia (per Stender): una gruccia appesa sotto il puntatore, che dondola come un pendolo quando si corre di lato e
  // torna ferma piano; sui link si gira di tre quarti, come quando la si prende dalla stender.
  STILI.gruccia = function () {
    var T = tela(), st = {}, ang = 0, va = 0, giro = 0;
    var filo = risolvi(CFG.ringColor || '#1f3fa0'), gancio = risolvi(CFG.dotColor || '#c8553d');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        va += (-Math.sin(ang) * 0.025 - vx * 0.004 - va * 0.05) * s; ang += va * s; vx *= 0.82; vy *= 0.82;
        giro += ((hot ? 1 : 0) - giro) * 0.16 * s;
        if (meno) { ang = 0; va = 0; }
        var g = pulisci(T);
        g.save(); g.translate(mx + o[0] + 4, my + o[1] + 2); g.rotate(ang);
        g.lineWidth = 2.2; g.lineJoin = 'round'; g.lineCap = 'round';
        g.strokeStyle = gancio; g.beginPath(); g.moveTo(0, 13); g.bezierCurveTo(0, 8, 5, 8, 5, 3); g.bezierCurveTo(5, -2, -3, -2, -3, 2); g.stroke();
        var w = 17 * (1 - giro * 0.55);
        g.strokeStyle = filo; g.beginPath(); g.moveTo(0, 13); g.lineTo(w, 26); g.lineTo(-w, 26); g.closePath(); g.stroke();
        g.restore();
        return Math.abs(ang) > 0.002 || Math.abs(va) > 0.0005 || Math.abs((hot ? 1 : 0) - giro) > 0.01;
      }
    };
  };

  // Registro (per Fuori Registro): il segno di registro della stampa, e dietro tre copie ciano, magenta e giallo che lo
  // inseguono ognuna col suo ritardo: correndo vanno fuori registro, da fermi tornano una sopra l'altra. Sui link si pieno.
  STILI.registro = function () {
    var T = tela(), st = {}, copie = [{ c: '#00a3d9', k: 0.32, x: mx, y: my }, { c: '#e0007a', k: 0.18, x: mx, y: my },
                                     { c: '#f2c300', k: 0.1, x: mx, y: my }], pieno = 0;
    var ink = risolvi(CFG.ringColor || '#161616');
    function segno(g, x, y, col, r) {
      g.strokeStyle = col; g.lineWidth = 2; g.beginPath(); g.arc(x, y, r, 0, 6.283); g.stroke();
      g.beginPath(); g.moveTo(x - r - 6, y); g.lineTo(x + r + 6, y); g.moveTo(x, y - r - 6); g.lineTo(x, y + r + 6); g.stroke();
    }
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta(), cx = mx + o[0], cy = my + o[1], fermo = true;
        pieno += ((hot ? 1 : 0) - pieno) * 0.2 * s;
        var g = pulisci(T);
        g.globalCompositeOperation = 'multiply';
        copie.forEach(function (c) {
          var k = meno ? 1 : Math.min(1, c.k * s);
          c.x += (cx - c.x) * k; c.y += (cy - c.y) * k;
          if (Math.abs(cx - c.x) + Math.abs(cy - c.y) > 0.3) fermo = false;
          segno(g, c.x, c.y, c.c, 8);
        });
        g.globalCompositeOperation = 'source-over';
        segno(g, cx, cy, ink, 8);
        if (pieno > 0.02) { g.globalAlpha = pieno; g.fillStyle = ink; g.beginPath(); g.arc(cx, cy, 8, 0, 6.283); g.fill(); g.globalAlpha = 1; }
        return !fermo || Math.abs((hot ? 1 : 0) - pieno) > 0.01;
      }
    };
  };

  // Lanterna (per Luce Rossa): la luce rossa della camera oscura che segue il puntatore: un alone morbido che respira
  // appena, con la lampadina al centro; sui link la luce si allarga e si scalda.
  STILI.lanterna = function () {
    var T = tela(), st = {}, ax = mx, ay = my, r = 90;
    var rosso = risolvi(CFG.dotColor || '#e5483d');
    function rgba(c, a) { var m = c.match(/\d+(\.\d+)?/g) || [229, 72, 61]; return 'rgba(' + m[0] + ',' + m[1] + ',' + m[2] + ',' + a + ')'; }
    return {
      frame: function (t) {
        var s = passo(st, t);
        ax += (mx - ax) * (meno ? 1 : 0.22 * s); ay += (my - ay) * (meno ? 1 : 0.22 * s);
        r += ((hot ? 150 : 95) - r) * 0.12 * s;
        var resp = meno ? 1 : 1 + Math.sin(t / 700) * 0.05, R = r * resp;
        var g = pulisci(T);
        var gr = g.createRadialGradient(ax, ay, 0, ax, ay, R);
        gr.addColorStop(0, rgba(rosso, 0.34)); gr.addColorStop(0.45, rgba(rosso, 0.14)); gr.addColorStop(1, rgba(rosso, 0));
        g.fillStyle = gr; g.beginPath(); g.arc(ax, ay, R, 0, 6.283); g.fill();
        g.fillStyle = rosso; g.beginPath(); g.arc(mx, my, 4, 0, 6.283); g.fill();
        return true;
      }
    };
  };

  // Scanner (per Lastra): il riquadro del metal detector dell'aeroporto: quattro angoli e una riga di luce che sale e
  // scende dentro; sui link il riquadro si stringe sul bersaglio e la riga si ferma a metà.
  STILI.scanner = function () {
    var T = tela(), st = {}, lato = 34, fase = 0;
    var luce = risolvi(CFG.dotColor || '#45d1ff'), angolo = risolvi(CFG.ringColor || '#e2f2fa');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        lato += ((hot ? 22 : 34) - lato) * 0.2 * s;
        fase += meno ? 0 : 0.06 * s;
        var cx = mx + o[0], cy = my + o[1], h = lato / 2, a = h * 0.45;
        var g = pulisci(T);
        g.strokeStyle = angolo; g.lineWidth = 2;
        [[-1, -1], [1, -1], [1, 1], [-1, 1]].forEach(function (q) {
          g.beginPath(); g.moveTo(cx + q[0] * h, cy + q[1] * (h - a)); g.lineTo(cx + q[0] * h, cy + q[1] * h); g.lineTo(cx + q[0] * (h - a), cy + q[1] * h); g.stroke();
        });
        var y = hot ? cy : cy + Math.sin(fase) * (h - 3);
        g.shadowColor = luce; g.shadowBlur = 8; g.strokeStyle = luce; g.lineWidth = 2;
        g.beginPath(); g.moveTo(cx - h + 3, y); g.lineTo(cx + h - 3, y); g.stroke(); g.shadowBlur = 0;
        return !meno || Math.abs((hot ? 22 : 34) - lato) > 0.1;
      }
    };
  };

  // Forbici (per Sbieco): un paio di forbici che si aprono e si chiudono mentre avanzano, girate verso dove si va, e dietro
  // la riga tratteggiata del taglio che svanisce; sui link si chiudono di colpo.
  STILI.forbici = function () {
    var T = tela({ mondo: true }), st = {}, strada = 0, dir = 0, px = mx, py = my, taglio = [];
    var lama = risolvi(CFG.ringColor || '#2a2018'), manico = risolvi(CFG.dotColor || '#a3322b');
    return {
      punto: function (t) {
        var d = Math.hypot(mx - px, my - py);
        if (d > 0.5) dir = Math.atan2(my - py, mx - px);
        strada += d; px = mx; py = my;
        if (!meno) { taglio.push({ x: mx, y: my, t: t }); if (taglio.length > 90) taglio.shift(); }
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        var g = pulisci(T);
        g.setLineDash([5, 5]); g.lineWidth = 1.4; g.strokeStyle = lama;
        for (var i = 1; i < taglio.length; i++) {
          var e = (t - taglio[i].t) / 1000;
          if (e >= 1) continue;
          g.globalAlpha = 1 - e; g.beginPath(); g.moveTo(taglio[i - 1].x, taglio[i - 1].y); g.lineTo(taglio[i].x, taglio[i].y); g.stroke();
        }
        while (taglio.length && t - taglio[0].t > 1000) taglio.shift();
        g.setLineDash([]); g.globalAlpha = 1;
        var ap = hot || meno ? 0.05 : 0.12 + Math.abs(Math.sin(strada / 18)) * 0.42;
        g.save(); T.glifo(g, mx + o[0], my + o[1]); g.rotate(dir);
        [-1, 1].forEach(function (k) {
          g.save(); g.rotate(k * ap);
          g.fillStyle = lama; g.beginPath(); g.moveTo(0, 0); g.lineTo(17, k * -1.5); g.lineTo(0, k * 2.6); g.closePath(); g.fill();
          g.strokeStyle = manico; g.lineWidth = 2.2; g.beginPath(); g.arc(-8, k * 3.5, 4, 0, 6.283); g.stroke();
          g.restore();
        });
        g.fillStyle = lama; g.beginPath(); g.arc(0, 0, 1.6, 0, 6.283); g.fill();
        g.restore();
        return taglio.length > 0;
      }
    };
  };

  // Flash (per Falcata): il mirino del fotografo a bordo passerella: quattro angoli e un punto; a ogni clic scatta un
  // lampo bianco che si allarga e svanisce, e sui link il mirino mette a fuoco (gli angoli stringono).
  STILI.flash = function () {
    var T = tela({ mondo: true }), st = {}, lampi = [], fuoco = 0;
    var oro = risolvi(CFG.dotColor || '#e9c46a'), bianco = risolvi(CFG.ringColor || '#f3efe8');
    document.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch' || meno) return;
      lampi.push({ x: mx, y: my, t: performance.now() });
      chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        fuoco += ((hot ? 1 : 0) - fuoco) * 0.2 * s;
        var g = pulisci(T);
        for (var i = lampi.length - 1; i >= 0; i--) {
          var l = lampi[i], e = (t - l.t) / 520;
          if (e >= 1) { lampi.splice(i, 1); continue; }
          var gr = g.createRadialGradient(l.x, l.y, 0, l.x, l.y, 40 + e * 220);
          gr.addColorStop(0, 'rgba(255,255,255,' + (0.9 * (1 - e)) + ')'); gr.addColorStop(1, 'rgba(255,255,255,0)');
          g.fillStyle = gr; g.beginPath(); g.arc(l.x, l.y, 40 + e * 220, 0, 6.283); g.fill();
        }
        var cx = mx + o[0], cy = my + o[1], h = 14 - fuoco * 5, a = 5;
        g.strokeStyle = fuoco > 0.5 ? oro : bianco; g.lineWidth = 1.6;
        [[-1, -1], [1, -1], [1, 1], [-1, 1]].forEach(function (q) {
          g.beginPath(); g.moveTo(cx + q[0] * h, cy + q[1] * (h - a)); g.lineTo(cx + q[0] * h, cy + q[1] * h); g.lineTo(cx + q[0] * (h - a), cy + q[1] * h); g.stroke();
        });
        g.fillStyle = oro; g.beginPath(); g.arc(cx, cy, 2.2, 0, 6.283); g.fill();
        return lampi.length > 0 || Math.abs((hot ? 1 : 0) - fuoco) > 0.01;
      }
    };
  };

  // Quadrante (per Combinazione): la manopola di una cassaforte con le tacche: gira a scatti quando il puntatore corre
  // a destra o a sinistra, come chi cerca la combinazione; sui link fa l'ultimo scatto e l'indice si accende.
  STILI.quadrante = function () {
    var T = tela(), st = {}, ang = 0, meta = 0, acceso = 0;
    var ottone = risolvi(CFG.ringColor || '#cfa54c'), indice = risolvi(CFG.dotColor || '#c25555');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        if (!meno) meta += vx * 0.012;
        vx *= 0.8; vy *= 0.8;
        var scatto = Math.round(meta / 0.157) * 0.157;
        ang += (scatto - ang) * 0.3 * s;
        acceso += ((hot ? 1 : 0) - acceso) * 0.2 * s;
        var cx = mx + o[0] + 2, cy = my + o[1] + 2, R = 12;
        var g = pulisci(T);
        g.strokeStyle = ottone; g.lineWidth = 2; g.beginPath(); g.arc(cx, cy, R, 0, 6.283); g.stroke();
        g.save(); g.translate(cx, cy); g.rotate(ang);
        for (var k = 0; k < 20; k++) {
          g.rotate(0.314); g.lineWidth = k % 5 ? 1 : 1.8; g.beginPath(); g.moveTo(0, -R + 1); g.lineTo(0, -R + (k % 5 ? 4 : 6)); g.stroke();
        }
        g.restore();
        g.fillStyle = acceso > 0.5 ? indice : ottone; g.beginPath(); g.moveTo(cx, cy - R - 2); g.lineTo(cx - 3.5, cy - R - 8); g.lineTo(cx + 3.5, cy - R - 8);
        g.closePath(); g.fill();
        g.beginPath(); g.arc(cx, cy, 2.4, 0, 6.283); g.fill();
        return Math.abs(scatto - ang) > 0.002 || Math.abs(vx) > 0.05 || Math.abs((hot ? 1 : 0) - acceso) > 0.01;
      }
    };
  };

  // Ventaglio (per Ventaglio): un ventaglio piccolo, chiuso finché si va in giro; sui link si apre a mezzaluna e corre
  // veloce fa vento (le stecche tremano appena).
  STILI.ventaglio = function () {
    var T = tela(), st = {}, apre = 0, vento = 0;
    var c1 = risolvi(CFG.dotColor || '#b8402b'), c2 = risolvi(CFG.ringColor || '#2f7a6a');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        apre += ((hot ? 1 : 0) - apre) * 0.16 * s;
        vento += (Math.min(1, Math.hypot(vx, vy) / 25) - vento) * 0.2 * s; vx *= 0.85; vy *= 0.85;
        if (meno) vento = 0;
        var g = pulisci(T), cx = mx + o[0], cy = my + o[1] + 18, N = 9, ampio = 0.25 + apre * 2.35;
        for (var k = 0; k < N; k++) {
          var a = -Math.PI / 2 - ampio / 2 + ampio * k / (N - 1) + Math.sin(t / 60 + k) * vento * 0.05;
          g.save(); g.translate(cx, cy); g.rotate(a + Math.PI / 2);
          g.fillStyle = k % 2 ? c2 : c1; g.beginPath(); g.moveTo(0, 0); g.lineTo(-3.2, -20); g.quadraticCurveTo(0, -23, 3.2, -20); g.closePath(); g.fill();
          g.restore();
        }
        g.fillStyle = c1; g.beginPath(); g.arc(cx, cy, 2.4, 0, 6.283); g.fill();
        return Math.abs((hot ? 1 : 0) - apre) > 0.005 || vento > 0.01;
      }
    };
  };

  // Molla (per Elastan): il punto sta sul puntatore e un anello lo segue legato da una molla a zig-zag: correndo la molla
  // si allunga e le spire si diradano, fermi si richiude; sui link l'anello scatta addosso al punto.
  STILI.molla = function () {
    var T = tela(), st = {}, ax = mx, ay = my, avx = 0, avy = 0;
    var rosa = risolvi(CFG.dotColor || '#ff4f8b'), acqua = risolvi(CFG.ringColor || '#3df5c8');
    return {
      frame: function (t) {
        var s = passo(st, t), k = hot ? 0.3 : 0.06;
        if (meno) { ax = mx; ay = my; avx = avy = 0; }
        avx += ((mx - ax) * k - avx * 0.22) * s; avy += ((my - ay) * k - avy * 0.22) * s;
        ax += avx * s; ay += avy * s;
        var g = pulisci(T), dx = mx - ax, dy = my - ay, L = Math.hypot(dx, dy);
        if (L > 2) {
          var nx = -dy / L, ny = dx / L, spire = 10, w = Math.max(1.5, 6 - L / 30);
          g.strokeStyle = acqua; g.lineWidth = 1.6; g.lineJoin = 'round'; g.beginPath(); g.moveTo(ax, ay);
          for (var i = 1; i < spire * 2; i++) {
            var f = i / (spire * 2), sg = i % 2 ? 1 : -1;
            g.lineTo(ax + dx * f + nx * w * sg, ay + dy * f + ny * w * sg);
          }
          g.lineTo(mx, my); g.stroke();
        }
        g.strokeStyle = acqua; g.lineWidth = 2; g.beginPath(); g.arc(ax, ay, 9, 0, 6.283); g.stroke();
        g.fillStyle = rosa; g.beginPath(); g.arc(mx, my, 3.5, 0, 6.283); g.fill();
        return L > 0.3 || Math.abs(avx) + Math.abs(avy) > 0.05;
      }
    };
  };

  /* ── I dieci cursori dei mestieri (10 nov 2026) ── */

  // Arco (per Morsetto): dalla punta del puntatore parte una scarica a zig-zag verso il link o il pulsante più vicino,
  // se è a meno di 170 px; lontano da tutto restano due scintille che crepitano sulla punta. La saetta si ridisegna
  // ogni 50–90 ms, con l'alone. Con «meno movimento» la scarica è ferma e non crepita.
  STILI.arco = function () {
    var T = tela(), bersagli = [], cercato = -1e9, saetta = [], rami = [], rifai = 0;
    var luce = risolvi(CFG.ringColor || '#bfe3ff'), nucleo = risolvi(CFG.dotColor || '#ffd23f');
    function cerca(t) {
      if (t - cercato < 260) return;
      cercato = t; bersagli = [];
      var els = document.querySelectorAll('a[href], button, [role="button"]');
      for (var i = 0; i < els.length && bersagli.length < 400; i++) {
        var r = els[i].getBoundingClientRect();
        if (r.width > 2 && r.height > 2 && r.bottom > 0 && r.top < innerHeight && r.right > 0 && r.left < innerWidth) bersagli.push(r);
      }
    }
    function vicino() {
      var best = null, bd = 170;
      for (var i = 0; i < bersagli.length; i++) {
        var r = bersagli[i], px = Math.max(r.left, Math.min(mx, r.right)), py = Math.max(r.top, Math.min(my, r.bottom));
        var d = Math.hypot(px - mx, py - my);
        if (d < bd) { bd = d; best = [px, py, d]; }
      }
      return best;
    }
    function zigzag(x0, y0, x1, y1, n, amp) {
      var p = [[x0, y0]], dx = x1 - x0, dy = y1 - y0, L = Math.hypot(dx, dy) || 1, nx = -dy / L, ny = dx / L;
      for (var i = 1; i < n; i++) {
        var f = i / n, a = (Math.random() - 0.5) * amp * Math.sin(Math.PI * f);
        p.push([x0 + dx * f + nx * a, y0 + dy * f + ny * a]);
      }
      p.push([x1, y1]);
      return p;
    }
    function linea(g, p, w, c) {
      g.strokeStyle = c; g.lineWidth = w; g.beginPath(); g.moveTo(p[0][0], p[0][1]);
      for (var i = 1; i < p.length; i++) g.lineTo(p[i][0], p[i][1]);
      g.stroke();
    }
    return {
      frame: function (t) {
        cerca(t);
        var g = pulisci(T), v = vicino(), vivo = t - ultimo < 1600;
        if (!vivo && !v) return false;
        if (t > rifai || meno) {
          rifai = t + 50 + Math.random() * 40;
          if (v && v[2] > 6) {
            var n = Math.max(4, Math.round(v[2] / 14));
            saetta = zigzag(mx, my, v[0], v[1], n, Math.min(34, 8 + v[2] * 0.18));
            rami = [];
            if (!meno && saetta.length > 3) {
              var k = 1 + Math.floor(Math.random() * (saetta.length - 2)), q = saetta[k];
              rami.push(zigzag(q[0], q[1], q[0] + (Math.random() - 0.5) * 40, q[1] + (Math.random() - 0.5) * 40, 3, 10));
            }
          } else {
            saetta = [];
            rami = [0, 1].map(function () {
              var a = Math.random() * 6.283, l = 7 + Math.random() * 7;
              return zigzag(mx, my, mx + Math.cos(a) * l, my + Math.sin(a) * l, 3, 5);
            });
          }
        }
        g.lineCap = 'round'; g.lineJoin = 'round';
        g.shadowColor = luce; g.shadowBlur = 14;
        if (saetta.length) { linea(g, saetta, 3.2, luce); }
        rami.forEach(function (r) { linea(g, r, 1.6, luce); });
        g.shadowBlur = 0;
        if (saetta.length) { linea(g, saetta, 1.1, '#ffffff'); }
        g.fillStyle = nucleo; g.beginPath(); g.arc(mx, my, 2.6, 0, 6.283); g.fill();
        if (v && saetta.length) { g.fillStyle = '#ffffff'; g.beginPath(); g.arc(v[0], v[1], 2.2, 0, 6.283); g.fill(); }
        return !meno;
      }
    };
  };

  // Gocciolio (per Sifone): accanto al puntatore si gonfia una goccia; quando è piena si stacca, cade e schizza in
  // goccioline che svaniscono. Correndo gocciola più spesso, sui link la goccia si gonfia in fretta; al clic uno spruzzo.
  STILI.gocciolio = function () {
    var T = tela({ mondo: true }), st = {}, gonfia = 0, gocce = [], schizzi = [];
    var acqua = risolvi(CFG.ringColor || '#4fb3e8');
    function spruzza(x, y, n, forza) {
      for (var i = 0; i < n; i++) {
        var a = -Math.PI * (0.1 + 0.8 * Math.random());
        schizzi.push({ x: x, y: y, vx: Math.cos(a) * forza * (0.5 + Math.random()), vy: Math.sin(a) * forza * (0.6 + Math.random()), v: 1 });
      }
    }
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      var o = scosta(); spruzza(mx + o[0], my + o[1], 10, 3.2); chiedi();
    });
    function goccia(g, x, y, r, lunga) {
      g.beginPath(); g.moveTo(x, y - r * (2 + lunga));
      g.quadraticCurveTo(x + r * 1.15, y - r * 0.7, x + r, y);
      g.arc(x, y, r, 0, Math.PI);
      g.quadraticCurveTo(x - r * 1.15, y - r * 0.7, x, y - r * (2 + lunga));
      g.fill();
    }
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta(), hx = mx + o[0], hy = my + o[1];
        var corsa = Math.min(1, Math.hypot(vx, vy) / 20);
        vx *= 0.85; vy *= 0.85;
        if (!meno) gonfia += (0.008 + corsa * 0.03 + (hot ? 0.02 : 0)) * s;
        if (gonfia >= 1) { gonfia = 0; gocce.push({ x: hx, y: hy + 4, y0: hy + 4, vy: 0.6 }); }
        var g = pulisci(T);
        g.fillStyle = acqua;
        gocce = gocce.filter(function (d) {
          d.vy += 0.38 * s; d.y += d.vy * s;
          if (d.y - d.y0 > 160) { spruzza(d.x, d.y, 6, 2.2); return false; }
          g.globalAlpha = 0.95; goccia(g, d.x, d.y, 3.2, Math.min(1.4, d.vy / 6));
          return true;
        });
        schizzi = schizzi.filter(function (p) {
          p.vy += 0.22 * s; p.x += p.vx * s; p.y += p.vy * s; p.v -= 0.035 * s;
          if (p.v <= 0) return false;
          g.globalAlpha = p.v; g.beginPath(); g.arc(p.x, p.y, 1.6, 0, 6.283); g.fill();
          return true;
        });
        g.globalAlpha = 1;
        var r = 1.6 + gonfia * 2.4;
        goccia(g, hx, hy + r, r, gonfia * 0.6);
        g.fillStyle = 'rgba(255,255,255,.7)'; g.beginPath(); g.arc(hx - r * 0.35, hy + r * 0.7, Math.max(0.6, r * 0.28), 0, 6.283); g.fill();
        return gocce.length > 0 || schizzi.length > 0 || t - ultimo < 3000;
      }
    };
  };

  // Rullo (per Due Mani): un rullo da imbianchino accanto al puntatore; dove passa lascia una pennellata larga, a righe
  // come la vernice stesa, che si asciuga in un secondo e mezzo. Sui link il rullo si inclina, come per caricarsi.
  STILI.rullo = function () {
    var T = tela({ mondo: true }), st = {}, tratti = [], incl = 0;
    var vernice = risolvi(CFG.dotColor || '#c8553d'), manico = risolvi(CFG.ringColor || '#2b2b2b');
    var VITA = 1500;
    return {
      punto: function (t) {
        if (meno) return;
        var o = scosta();
        tratti.push({ x: mx + o[0], y: my + o[1] + 6, t: t });
        if (tratti.length > 160) tratti.shift();
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        incl += ((hot ? -0.45 : 0) - incl) * 0.18 * s;
        while (tratti.length && t - tratti[0].t > VITA) tratti.shift();
        var g = pulisci(T);
        g.lineCap = 'butt'; g.lineJoin = 'round';
        for (var i = 1; i < tratti.length; i++) {
          var a = tratti[i - 1], b = tratti[i], k = 1 - (t - b.t) / VITA;
          if (Math.hypot(b.x - a.x, b.y - a.y) > 140) continue;
          g.globalAlpha = Math.max(0, k) * 0.5; g.strokeStyle = vernice; g.lineWidth = 22;
          g.beginPath(); g.moveTo(a.x, a.y); g.lineTo(b.x, b.y); g.stroke();
          g.globalAlpha = Math.max(0, k) * 0.22; g.strokeStyle = '#ffffff'; g.lineWidth = 1.2;
          [-7, -2, 4, 8].forEach(function (d) { g.beginPath(); g.moveTo(a.x, a.y + d); g.lineTo(b.x, b.y + d); g.stroke(); });
        }
        g.globalAlpha = 1;
        g.save(); T.glifo(g, mx + o[0], my + o[1]); g.rotate(incl);
        // il manico e la staffa, poi il cilindro
        g.strokeStyle = manico; g.lineWidth = 2; g.lineCap = 'round';
        g.beginPath(); g.moveTo(14, 2); g.lineTo(18, -4); g.lineTo(6, -9); g.lineTo(6, -14); g.stroke();
        g.lineWidth = 4.5; g.beginPath(); g.moveTo(6, -14); g.lineTo(4, -26); g.stroke();
        g.fillStyle = vernice; g.beginPath();
        if (g.roundRect) g.roundRect(-14, 0, 28, 11, 3); else g.rect(-14, 0, 28, 11);
        g.fill();
        g.fillStyle = 'rgba(255,255,255,.28)'; g.fillRect(-12, 2, 24, 2.5);
        g.restore();
        return tratti.length > 0 || Math.abs((hot ? -0.45 : 0) - incl) > 0.005;
      }
    };
  };

  // Livella (per Filo a Piombo): una livella da muratore accanto al puntatore. Correndo la bolla scappa dalla parte
  // opposta e la livella si inclina appena; ferma, la bolla torna in mezzo alle tacche e la fiala si fa verde. Sui link
  // la bolla va subito in bolla.
  STILI.livella = function () {
    var T = tela(), st = {}, bolla = 0, bv = 0, incl = 0, verde = 0;
    var corpo = risolvi(CFG.ringColor || '#e8b923'), fiala = risolvi(CFG.dotColor || '#8fd16a');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        var spinta = meno ? 0 : Math.max(-1, Math.min(1, -vx / 18));
        vx *= 0.82; vy *= 0.82;
        var meta = hot ? 0 : spinta;
        bv += ((meta - bolla) * 0.12 - bv * 0.25) * s; bolla += bv * s;
        incl += ((meno || hot ? 0 : Math.max(-0.18, Math.min(0.18, spinta * 0.18))) - incl) * 0.15 * s;
        var ferma = Math.abs(bolla) < 0.06 && Math.abs(bv) < 0.01;
        verde += ((ferma ? 1 : 0) - verde) * 0.12 * s;
        var g = pulisci(T);
        g.save(); g.translate(mx + o[0] + 8, my + o[1]); g.rotate(incl);
        g.fillStyle = corpo; g.beginPath();
        if (g.roundRect) g.roundRect(-26, -6, 52, 12, 2.5); else g.rect(-26, -6, 52, 12);
        g.fill();
        g.fillStyle = 'rgba(0,0,0,.22)'; g.fillRect(-26, 3, 52, 3);
        // la fiala: si colora di verde quando la bolla è in mezzo
        g.fillStyle = 'rgba(255,255,255,.85)'; g.beginPath();
        if (g.roundRect) g.roundRect(-11, -3.5, 22, 7, 3.5); else g.rect(-11, -3.5, 22, 7);
        g.fill();
        g.globalAlpha = verde; g.fillStyle = fiala; g.beginPath();
        if (g.roundRect) g.roundRect(-11, -3.5, 22, 7, 3.5); else g.rect(-11, -3.5, 22, 7);
        g.fill(); g.globalAlpha = 1;
        g.strokeStyle = 'rgba(0,0,0,.55)'; g.lineWidth = 1;
        g.beginPath(); g.moveTo(-3.5, -3.5); g.lineTo(-3.5, 3.5); g.moveTo(3.5, -3.5); g.lineTo(3.5, 3.5); g.stroke();
        g.fillStyle = 'rgba(255,255,255,.95)'; g.strokeStyle = 'rgba(0,0,0,.35)';
        g.beginPath(); g.ellipse(bolla * 7.5, 0, 3.2, 2.4, 0, 0, 6.283); g.fill(); g.stroke();
        g.restore();
        return !ferma || Math.abs(incl) > 0.002 || Math.abs((ferma ? 1 : 0) - verde) > 0.01;
      }
    };
  };

  // Trucioli (per Truciolo): una piccola pialla accanto al puntatore; correndo stacca trucioli di legno arricciati che
  // girano, cadono e svaniscono. Sui link la pialla si alza come per dare l'ultima passata.
  STILI.trucioli = function () {
    var T = tela({ mondo: true }), st = {}, ricci = [], strada = 0, alza = 0;
    var legno = risolvi(CFG.ringColor || '#d9a66b'), scuro = risolvi(CFG.dotColor || '#8a5a2b');
    return {
      punto: function () {
        if (meno) return;
        strada += Math.hypot(vx, vy);
        if (strada > 70) {
          strada = 0;
          var o = scosta();
          ricci.push({ x: mx + o[0] - 8, y: my + o[1] + 4, vx: -vx * 0.08 + (Math.random() - 0.5) * 1.2, vy: -1.4 - Math.random(),
            r: Math.random() * 6.283, vr: (Math.random() - 0.5) * 0.25, v: 1, k: 4 + Math.random() * 3 });
          if (ricci.length > 26) ricci.shift();
        }
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        alza += ((hot ? 1 : 0) - alza) * 0.18 * s;
        var g = pulisci(T);
        ricci = ricci.filter(function (c) {
          c.vy += 0.12 * s; c.x += c.vx * s; c.y += c.vy * s; c.r += c.vr * s; c.v -= 0.012 * s;
          if (c.v <= 0) return false;
          g.save(); g.translate(c.x, c.y); g.rotate(c.r); g.globalAlpha = Math.min(1, c.v * 1.4);
          g.strokeStyle = legno; g.lineWidth = 2.6; g.lineCap = 'round'; g.beginPath();
          for (var a = 0; a < 9.4; a += 0.3) { var rr = c.k * (1 - a / 12); var x = Math.cos(a) * rr, y = Math.sin(a) * rr * 0.75; if (a === 0) g.moveTo(x, y); else g.lineTo(x, y); }
          g.stroke(); g.strokeStyle = scuro; g.lineWidth = 0.8; g.stroke();
          g.restore();
          return true;
        });
        g.globalAlpha = 1;
        g.save(); T.glifo(g, mx + o[0], my + o[1] - alza * 6); g.rotate(-0.12 - alza * 0.25);
        g.fillStyle = scuro; g.beginPath();
        if (g.roundRect) g.roundRect(-14, -5, 28, 10, 2); else g.rect(-14, -5, 28, 10);
        g.fill();
        g.fillStyle = legno; g.beginPath(); g.ellipse(6, -7, 6, 4, 0, 0, 6.283); g.fill();
        g.fillStyle = '#c9ced6'; g.fillRect(-3, 3, 7, 3);
        g.restore();
        return ricci.length > 0 || Math.abs((hot ? 1 : 0) - alza) > 0.005;
      }
    };
  };

  // Cerchio aereo (per Capriola): il cerchio dell'acrobatica appeso a un tratto di corda accanto al puntatore; quando ci
  // si muove dondola come un pendolo e si assesta; sui link fa una capriola intera, una per ogni link.
  STILI.cerchio = function () {
    var T = tela(), st = {}, ang = 0, vang = 0, capo = 0, meta = 0, eraSu = false;
    var anello = risolvi(CFG.ringColor || '#ff5a3c'), corpo = risolvi(CFG.dotColor || '#ffffff');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        if (hot && !eraSu) meta += 6.283;
        eraSu = !!hot;
        if (meno) { ang = 0; vang = 0; capo = meta; } else {
          vang = (vang + (-vx * 0.012 - ang * 0.06) * s) * Math.pow(0.92, s);
          ang = Math.max(-1.1, Math.min(1.1, ang + vang * s));
          capo += (meta - capo) * 0.09 * s;
        }
        vx *= 0.8; vy *= 0.8;
        var g = pulisci(T);
        g.save(); g.translate(mx + o[0] + 4, my + o[1] - 6); g.rotate(ang);
        g.globalAlpha = 0.7; g.strokeStyle = corpo; g.lineWidth = 1.2;
        g.beginPath(); g.moveTo(0, 0); g.lineTo(0, 10); g.stroke(); g.globalAlpha = 1;
        g.translate(0, 21); g.rotate(capo);
        g.strokeStyle = anello; g.lineWidth = 2.6; g.beginPath(); g.arc(0, 0, 11, 0, 6.283); g.stroke();
        g.strokeStyle = corpo; g.lineWidth = 1.6; g.lineCap = 'round'; g.beginPath(); g.moveTo(0, 3); g.lineTo(0, -7); g.stroke();
        g.fillStyle = corpo; g.beginPath(); g.arc(0, 5.6, 2.4, 0, 6.283); g.fill();
        g.restore();
        return Math.abs(vang) > 0.002 || Math.abs(ang) > 0.002 || Math.abs(meta - capo) > 0.01 || Math.abs(vx) > 0.05;
      }
    };
  };

  // Ipnosi (per Abbaglio): una piccola spirale op art, bianca e nera, che gira accanto al puntatore e sembra risucchiare;
  // più veloce quando ci si muove; sui link si allarga e accelera, come un'illusione che prende.
  STILI.ipnosi = function () {
    var T = tela(), st = {}, ang = 0, gonfia = 0;
    var chiaro = risolvi(CFG.ringColor || '#ffffff'), scuro = risolvi(CFG.dotColor || '#111111');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta(), corsa = Math.min(1, Math.hypot(vx, vy) / 12);
        vx *= 0.82; vy *= 0.82;
        gonfia += ((hot ? 1 : 0) - gonfia) * 0.12 * s;
        if (!meno) ang += (0.05 + corsa * 0.18 + gonfia * 0.2) * s;
        var g = pulisci(T), R = 12 + gonfia * 6;
        g.save(); g.translate(mx + o[0] + 4, my + o[1] + 4); g.rotate(ang);
        g.fillStyle = scuro; g.beginPath(); g.arc(0, 0, R, 0, 6.283); g.fill();
        g.strokeStyle = chiaro; g.lineWidth = R / 7; g.lineCap = 'round'; g.beginPath();
        for (var k = 0; k <= 60; k++) {
          var a = k / 60 * 6.283 * 2.6, r = R * 0.9 * k / 60;
          g[k ? 'lineTo' : 'moveTo'](Math.cos(a) * r, Math.sin(a) * r);
        }
        g.stroke();
        g.strokeStyle = chiaro; g.lineWidth = 1; g.globalAlpha = 0.5; g.beginPath(); g.arc(0, 0, R, 0, 6.283); g.stroke();
        g.restore();
        return true;
      }
    };
  };

  // Miccia (per Girandola): la scintilla di una miccia accesa accanto al puntatore, che lascia cadere faville mentre ci
  // si muove; al clic scoppia un piccolo fuoco d'artificio nei colori del tema; sui link la scintilla si fa più grande.
  STILI.miccia = function () {
    var T = tela({ mondo: true }), st = {}, faville = [], scoppi = [], vivo = 0;
    var oro = risolvi(CFG.ringColor || '#ffc94a'), rosa = risolvi(CFG.dotColor || '#ff3d7f');
    var COLORI = [oro, rosa, '#3ddcff', '#ffffff'];
    window.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return;
      if (meno) return;
      var c = COLORI[Math.floor(Math.random() * COLORI.length)];
      for (var i = 0; i < 26; i++) {
        var a = i / 26 * 6.283 + Math.random() * 0.2, f = 2.2 + Math.random() * 2.6;
        scoppi.push({ x: mx, y: my, vx: Math.cos(a) * f, vy: Math.sin(a) * f, v: 1, c: i % 4 ? c : '#ffffff' });
      }
      chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta(), corsa = Math.hypot(vx, vy);
        vx *= 0.85; vy *= 0.85;
        vivo += ((hot ? 1 : 0) - vivo) * 0.15 * s;
        var cx = mx + o[0] + 2, cy = my + o[1] + 2;
        if (!meno) {
          var n = corsa > 2 ? 2 : (Math.random() < 0.35 ? 1 : 0);
          for (var i = 0; i < n; i++) {
            var a = Math.random() * 6.283, f = 0.6 + Math.random() * 1.6;
            faville.push({ x: cx, y: cy, vx: Math.cos(a) * f, vy: Math.sin(a) * f - 0.6, v: 1 });
          }
          if (faville.length > 90) faville.splice(0, faville.length - 90);
        }
        var g = pulisci(T);
        faville = faville.filter(function (p) {
          p.vy += 0.06 * s; p.x += p.vx * s; p.y += p.vy * s; p.v -= 0.035 * s;
          if (p.v <= 0) return false;
          g.globalAlpha = p.v; g.fillStyle = p.v > 0.6 ? '#fff6c8' : oro; g.fillRect(p.x - 0.8, p.y - 0.8, 1.6, 1.6);
          return true;
        });
        scoppi = scoppi.filter(function (p) {
          p.vx *= Math.pow(0.96, s); p.vy = p.vy * Math.pow(0.96, s) + 0.05 * s; p.x += p.vx * s; p.y += p.vy * s; p.v -= 0.02 * s;
          if (p.v <= 0) return false;
          g.globalAlpha = p.v; g.strokeStyle = p.c; g.lineWidth = 1.8; g.lineCap = 'round';
          g.beginPath(); g.moveTo(p.x, p.y); g.lineTo(p.x - p.vx * 3, p.y - p.vy * 3); g.stroke();
          return true;
        });
        g.globalAlpha = 1;
        var R = 3 + vivo * 2.5 + Math.random() * 1.2;
        var al = g.createRadialGradient(cx, cy, 0, cx, cy, R * 3.2);
        al.addColorStop(0, 'rgba(255,246,200,.95)'); al.addColorStop(0.35, 'rgba(255,170,60,.55)'); al.addColorStop(1, 'rgba(255,120,40,0)');
        g.fillStyle = al; g.beginPath(); g.arc(cx, cy, R * 3.2, 0, 6.283); g.fill();
        g.fillStyle = '#fffbe8'; g.beginPath(); g.arc(cx, cy, R * 0.55, 0, 6.283); g.fill();
        return true;
      }
    };
  };

  // Martello (per Incudine): un martello da fabbro accanto al puntatore, alzato; al clic cala di colpo e dalla punta
  // escono le scintille del ferro battuto, che cadono e passano dal giallo al rosso. Sui link il martello si alza di più.
  STILI.martello = function () {
    var T = tela({ mondo: true }), st = {}, colpo = 0, scintille = [];
    var testa = risolvi(CFG.ringColor || '#3a3a3a'), fuoco = risolvi(CFG.dotColor || '#ff8a1f');
    window.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return;
      colpo = 1;
      if (!meno) {
        for (var i = 0; i < 16; i++) {
          var a = -Math.PI * Math.random(), f = 2 + Math.random() * 4.5;
          scintille.push({ x: mx, y: my, vx: Math.cos(a) * f, vy: Math.sin(a) * f, v: 1 });
        }
      }
      chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        colpo = Math.max(0, colpo - 0.09 * s);
        var g = pulisci(T);
        scintille = scintille.filter(function (p) {
          p.vy += 0.2 * s; p.x += p.vx * s; p.y += p.vy * s; p.v -= 0.03 * s;
          if (p.v <= 0) return false;
          g.globalAlpha = p.v; g.strokeStyle = p.v > 0.6 ? '#fff3b0' : fuoco; g.lineWidth = 1.6;
          g.beginPath(); g.moveTo(p.x, p.y); g.lineTo(p.x - p.vx * 2, p.y - p.vy * 2); g.stroke();
          return true;
        });
        g.globalAlpha = 1;
        // alzato di 50°, o di più sui link; al colpo cala fino a terra
        var alto = hot ? -1.15 : -0.85, ang = alto + (0.3 - alto) * Math.pow(colpo, 0.6);
        g.save(); T.glifo(g, mx + o[0] + 16, my + o[1] + 10); g.rotate(ang);
        g.strokeStyle = '#8a5a2b'; g.lineWidth = 3.4; g.lineCap = 'round'; g.beginPath(); g.moveTo(0, 0); g.lineTo(-22, 0); g.stroke();
        g.fillStyle = testa; g.fillRect(-28, -7, 9, 14);
        g.fillStyle = 'rgba(255,255,255,.25)'; g.fillRect(-28, -7, 9, 3);
        g.restore();
        return colpo > 0 || scintille.length > 0;
      }
    };
  };

  // Bullone (per Cric): la testa esagonale di un bullone accanto al puntatore, che gira con la strada fatta (avanti
  // andando a destra, indietro a sinistra); sui link si avvita veloce e rimpicciolisce, come stretto a fondo.
  STILI.bullone = function () {
    var T = tela(), st = {}, ang = 0, stretto = 0;
    var acciaio = risolvi(CFG.ringColor || '#c3c9d1'), segno = risolvi(CFG.dotColor || '#f2b134');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        if (!meno) ang += vx * 0.03 + (hot ? 0.22 * s : 0);
        vx *= 0.8; vy *= 0.8;
        stretto += ((hot ? 1 : 0) - stretto) * 0.15 * s;
        var g = pulisci(T), R = 11 - stretto * 3;
        g.save(); g.translate(mx + o[0] + 2, my + o[1] + 2); g.rotate(ang);
        g.fillStyle = acciaio; g.strokeStyle = 'rgba(0,0,0,.45)'; g.lineWidth = 1.2; g.beginPath();
        for (var k = 0; k < 6; k++) { var a = k * Math.PI / 3; g[k ? 'lineTo' : 'moveTo'](Math.cos(a) * R, Math.sin(a) * R); }
        g.closePath(); g.fill(); g.stroke();
        g.beginPath(); g.arc(0, 0, R * 0.55, 0, 6.283); g.strokeStyle = 'rgba(0,0,0,.35)'; g.stroke();
        g.fillStyle = segno; g.fillRect(-R * 0.55, -1, R * 1.1, 2);
        g.restore();
        return Math.abs(vx) > 0.05 || hot || Math.abs((hot ? 1 : 0) - stretto) > 0.005;
      }
    };
  };

  // Scovolo (per Fuliggine): lo scovolo tondo dello spazzacamino accanto al puntatore, con le setole a raggiera che
  // girano col movimento; correndo lascia cadere granelli di fuliggine. Sui link gira da solo, come dentro la canna.
  STILI.scovolo = function () {
    var T = tela({ mondo: true }), st = {}, ang = 0, grani = [];
    var setole = risolvi(CFG.ringColor || '#2a2624'), manico = risolvi(CFG.dotColor || '#c0782f');
    return {
      punto: function () {
        if (meno) return;
        var corsa = Math.hypot(vx, vy);
        if (corsa > 4 && Math.random() < 0.6) {
          var o = scosta();
          grani.push({ x: mx + o[0] + (Math.random() - 0.5) * 18, y: my + o[1] + (Math.random() - 0.5) * 18, vx: (Math.random() - 0.5) * 0.6,
            vy: 0.4 + Math.random() * 0.8, v: 1, r: 0.8 + Math.random() * 1.6 });
          if (grani.length > 80) grani.shift();
        }
      },
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        if (!meno) ang += (vx * 0.02 + vy * 0.02) * s + (hot ? 0.12 * s : 0);
        vx *= 0.85; vy *= 0.85;
        var g = pulisci(T);
        g.fillStyle = '#3b3632';
        grani = grani.filter(function (p) {
          p.vy += 0.05 * s; p.x += p.vx * s; p.y += p.vy * s; p.v -= 0.016 * s;
          if (p.v <= 0) return false;
          g.globalAlpha = p.v * 0.8; g.beginPath(); g.arc(p.x, p.y, p.r, 0, 6.283); g.fill();
          return true;
        });
        g.globalAlpha = 1;
        var cx = mx + o[0] + 4, cy = my + o[1] + 4;
        g.strokeStyle = manico; g.lineWidth = 2.4; g.lineCap = 'round'; g.beginPath(); g.moveTo(cx, cy); g.lineTo(cx + 16, cy + 16); g.stroke();
        g.save(); T.glifo(g, cx, cy); g.rotate(ang);
        g.strokeStyle = setole; g.lineWidth = 1.3;
        for (var k = 0; k < 22; k++) { var a = k * 6.283 / 22, l = 10 + (k % 3); g.beginPath(); g.moveTo(Math.cos(a) * 2.5, Math.sin(a) * 2.5); g.lineTo(Math.cos(a) * l, Math.sin(a) * l); g.stroke(); }
        g.fillStyle = manico; g.beginPath(); g.arc(0, 0, 2.6, 0, 6.283); g.fill();
        g.restore();
        return grani.length > 0 || Math.abs(vx) + Math.abs(vy) > 0.1 || !!hot;
      }
    };
  };

  // Irrigatore (per Rastrello): la testina di un irrigatore oscillante accanto al puntatore; quando ci si muove spruzza a
  // ventaglio, avanti e indietro, goccioline che fanno l'arco e cadono. Sui link il ventaglio si apre del tutto.
  STILI.irrigatore = function () {
    var T = tela({ mondo: true }), st = {}, fase = 0, gocce = [], acceso = 0;
    var acqua = risolvi(CFG.ringColor || '#7cc6f2'), verde = risolvi(CFG.dotColor || '#2f7d4f');
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta(), corsa = Math.min(1, Math.hypot(vx, vy) / 14);
        vx *= 0.86; vy *= 0.86;
        acceso += ((corsa > 0.08 || hot ? 1 : 0) - acceso) * 0.15 * s;
        fase += 0.06 * s;
        var cx = mx + o[0] + 6, cy = my + o[1] + 8, apertura = hot ? 1.2 : 0.7;
        var dir = -Math.PI / 2 + Math.sin(fase) * apertura;
        if (!meno && acceso > 0.3) {
          for (var i = 0; i < 2; i++) {
            var a = dir + (Math.random() - 0.5) * 0.25, f = 3 + Math.random() * 1.6;
            gocce.push({ x: cx + Math.cos(dir) * 6, y: cy + Math.sin(dir) * 6, vx: Math.cos(a) * f, vy: Math.sin(a) * f, v: 1 });
          }
          if (gocce.length > 120) gocce.splice(0, gocce.length - 120);
        }
        var g = pulisci(T);
        g.fillStyle = acqua;
        gocce = gocce.filter(function (p) {
          p.vy += 0.16 * s; p.x += p.vx * s; p.y += p.vy * s; p.v -= 0.022 * s;
          if (p.v <= 0) return false;
          g.globalAlpha = p.v * 0.85; g.beginPath(); g.arc(p.x, p.y, 1.4, 0, 6.283); g.fill();
          return true;
        });
        g.globalAlpha = 1;
        g.fillStyle = verde; g.beginPath();
        if (g.roundRect) g.roundRect(cx - 7, cy, 14, 6, 2); else g.rect(cx - 7, cy, 14, 6);
        g.fill();
        g.save(); T.glifo(g, cx, cy); g.rotate(dir + Math.PI / 2);
        g.fillStyle = '#c9ced6'; g.fillRect(-1.6, -8, 3.2, 8);
        g.restore();
        return gocce.length > 0 || acceso > 0.01;
      }
    };
  };

  // Diamante (per Scheggia): il tagliavetro, col manico e la punta di diamante sul puntatore; dove passa resta un graffio
  // sottile e chiaro con qualche luccichio, che svanisce. Al clic una piccola stella di crepe, che si richiude.
  STILI.diamante = function () {
    var T = tela({ mondo: true }), st = {}, segno = [], stelle = [];
    var vetro = risolvi(CFG.ringColor || '#d8f3ff'), manico = risolvi(CFG.dotColor || '#2c7a7b');
    var VITA = 1600;
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      var raggi = [];
      for (var i = 0; i < 7; i++) {
        var a = i * 6.283 / 7 + Math.random() * 0.5, l = 14 + Math.random() * 22, p = [[0, 0]];
        for (var k = 1; k <= 3; k++) p.push([Math.cos(a) * l * k / 3 + (Math.random() - 0.5) * 4, Math.sin(a) * l * k / 3 + (Math.random() - 0.5) * 4]);
        raggi.push(p);
      }
      var o = scosta();
      stelle.push({ x: mx + o[0], y: my + o[1], t: performance.now(), r: raggi });
      chiedi();
    });
    return {
      punto: function (t) { if (!meno) { var o = scosta(); segno.push({ x: mx + o[0], y: my + o[1], t: t }); if (segno.length > 140) segno.shift(); } },
      frame: function (t) {
        var g = pulisci(T);
        while (segno.length && t - segno[0].t > VITA) segno.shift();
        stelle = stelle.filter(function (s) { return t - s.t < 1300; });
        g.lineCap = 'round';
        for (var i = 1; i < segno.length; i++) {
          var a = segno[i - 1], b = segno[i], k = 1 - (t - b.t) / VITA;
          if (Math.hypot(b.x - a.x, b.y - a.y) > 120) continue;
          g.globalAlpha = Math.max(0, k) * 0.9; g.strokeStyle = vetro; g.lineWidth = 1.1;
          g.beginPath(); g.moveTo(a.x, a.y); g.lineTo(b.x, b.y); g.stroke();
          if (i % 9 === 0) { g.fillStyle = '#ffffff'; g.beginPath(); g.arc(b.x, b.y, 1.6 * k, 0, 6.283); g.fill(); }
        }
        stelle.forEach(function (s) {
          var e = (t - s.t) / 1300;
          g.globalAlpha = 1 - e; g.strokeStyle = vetro; g.lineWidth = 1;
          s.r.forEach(function (p) {
            g.beginPath(); g.moveTo(s.x, s.y);
            for (var k = 1; k < p.length; k++) g.lineTo(s.x + p[k][0], s.y + p[k][1]);
            g.stroke();
          });
        });
        g.globalAlpha = 1;
        // il tagliavetro: la punta sul puntatore, il manico in basso a destra come la freccia
        var oo = scosta();
        g.save(); T.glifo(g, mx + oo[0], my + oo[1]); g.rotate(0.62);
        g.fillStyle = '#c9ced6'; g.fillRect(-1.5, 2, 3, 8);
        g.fillStyle = vetro; g.beginPath(); g.moveTo(0, 0); g.lineTo(2.4, 3); g.lineTo(-2.4, 3); g.closePath(); g.fill();
        g.fillStyle = manico; g.beginPath();
        if (g.roundRect) g.roundRect(-3, 10, 6, 22, 3); else g.rect(-3, 10, 6, 22);
        g.fill();
        g.restore();
        return segno.length > 0 || stelle.length > 0;
      }
    };
  };

  // Atomo (per Ti Consiglia, 10 nov 2026): sulla punta del puntatore un piccolo atomo, il nucleo e tre orbite. Muovendosi
  // gli elettroni girano, più in fretta quanto più si corre, e da fermi rallentano fino a fermarsi; sui link l'atomo si
  // eccita (le orbite si allargano e gli elettroni non si fermano) e al clic emette un fotone, un'onda che si allontana.
  // 11 nov 2026 (utente: «deve stare sulla punta e il mouse è decisamente troppo grande»): centrato sulla punta anche con
  // la freccia di sistema visibile (prima stava 18 px a destra e 22 sotto) e disegnato a SCALA (circa 16 px, 23 sui link).
  STILI.atomo = function () {
    var T = tela({ mondo: true }), st = {}, fase = 0, giri = 0.02, eccita = 0, fotoni = [], SCALA = 0.62;
    var orbita = risolvi(CFG.ringColor || '#e6b057'), carica = risolvi(CFG.dotColor || '#f6e09b');
    function scosta() { return [0, 0]; }
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      var o = scosta();
      fotoni.push({ x: mx + o[0], y: my + o[1], t: performance.now(), a: Math.random() * 6.283 });
      if (fotoni.length > 6) fotoni.shift();
      chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t), o = scosta();
        var corsa = meno ? 0 : Math.min(1, Math.hypot(vx, vy) / 24);
        vx *= 0.8; vy *= 0.8;
        giri += ((meno ? 0 : corsa * 0.32 + (hot ? 0.06 : 0)) - giri) * 0.08 * s;
        fase += giri * s;
        eccita += ((hot ? 1 : 0) - eccita) * 0.14 * s;
        var g = pulisci(T), cx = mx + o[0], cy = my + o[1];
        var rx = 13 + eccita * 6, ry = 4.6 + eccita * 1.8;
        // i fotoni: un'onda corta che parte dal nucleo e svanisce
        fotoni = fotoni.filter(function (f) { return t - f.t < 900; });
        fotoni.forEach(function (f) {
          var e = (t - f.t) / 900, d = 10 + e * 70;
          g.save(); g.translate(f.x, f.y); g.rotate(f.a); g.scale(SCALA, SCALA);
          g.globalAlpha = 1 - e; g.strokeStyle = carica; g.lineWidth = 1.4 / SCALA; g.beginPath();
          for (var k = 0; k <= 22; k++) { var x = d + k * 1.2, y = Math.sin(k * 0.9 - e * 12) * 3.2; if (k === 0) g.moveTo(x, y); else g.lineTo(x, y); }
          g.stroke(); g.restore();
        });
        g.globalAlpha = 1;
        // lo spessore delle orbite resta quello di prima (0,9 px) anche rimpicciolito: si leggono ancora sul fondo scuro
        g.save(); g.translate(cx, cy); g.scale(SCALA, SCALA);
        for (var i = 0; i < 3; i++) {
          var r = i * 1.0472;
          g.save(); g.rotate(r);
          g.globalAlpha = 0.5 + eccita * 0.3; g.strokeStyle = orbita; g.lineWidth = 0.9 / SCALA;
          g.beginPath(); g.ellipse(0, 0, rx, ry, 0, 0, 6.283); g.stroke();
          var a = fase * (1 + i * 0.18) + i * 2.1;
          g.globalAlpha = 1; g.fillStyle = carica;
          g.beginPath(); g.arc(Math.cos(a) * rx, Math.sin(a) * ry, 1.9, 0, 6.283); g.fill();
          g.restore();
        }
        // il nucleo: tre nucleoni stretti
        g.fillStyle = orbita;
        g.beginPath(); g.arc(-1.3, -0.8, 2.1, 0, 6.283); g.fill();
        g.beginPath(); g.arc(1.4, -0.5, 2.1, 0, 6.283); g.fill();
        g.fillStyle = carica;
        g.beginPath(); g.arc(0, 1.3, 2.1, 0, 6.283); g.fill();
        g.restore();
        return giri > 0.004 || Math.abs((hot ? 1 : 0) - eccita) > 0.01 || fotoni.length > 0 || !!hot;
      }
    };
  };

  // Tornante (per Tornanti, 18 nov 2026): un paracarro bianco e rosso sulla punta, che lascia dietro di sé la strada
  // fatta (un tratteggio che sbiadisce); a ogni clic sale un cartello col numero del tornante, da 48 in giù. Sui link il
  // paracarro si alza.
  STILI.tornante = function () {
    var T = tela({ mondo: true }), st = {}, scia = [], cartelli = [], numero = 48, alza = 0, ux = null, uy = null;
    var rosso = risolvi(CFG.ringColor || '#e5322d'), giallo = risolvi(CFG.dotColor || '#ffc400');
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      cartelli.push({ x: mx, y: my, t: performance.now(), n: numero });
      numero = numero > 1 ? numero - 1 : 48;
      chiedi();
    });
    function rett(g, x, y, w, h, r) {
      g.beginPath();
      if (g.roundRect) g.roundRect(x, y, w, h, r); else g.rect(x, y, w, h);
    }
    return {
      frame: function (t) {
        passo(st, t);
        if (!meno) {
          if (ux === null || Math.hypot(mx - ux, my - uy) > 2) { scia.push({ x: mx, y: my, t: t }); ux = mx; uy = my; }
          if (scia.length > 90) scia.shift();
        }
        alza += ((hot ? 1 : 0) - alza) * 0.18;
        scia = scia.filter(function (q) { return t - q.t < 1100; });
        var g = pulisci(T), i;
        g.lineCap = 'round'; g.lineJoin = 'round';
        for (i = 1; i < scia.length; i++) {
          g.globalAlpha = (1 - (t - scia[i].t) / 1100) * 0.5; g.strokeStyle = '#1c2226'; g.lineWidth = 7;
          g.beginPath(); g.moveTo(scia[i - 1].x, scia[i - 1].y); g.lineTo(scia[i].x, scia[i].y); g.stroke();
        }
        g.setLineDash([5, 5]);
        for (i = 1; i < scia.length; i++) {
          g.globalAlpha = (1 - (t - scia[i].t) / 1100) * 0.9; g.strokeStyle = '#ffffff'; g.lineWidth = 1.6;
          g.beginPath(); g.moveTo(scia[i - 1].x, scia[i - 1].y); g.lineTo(scia[i].x, scia[i].y); g.stroke();
        }
        g.setLineDash([]);
        cartelli = cartelli.filter(function (c) { return t - c.t < 1300; });
        cartelli.forEach(function (c) {
          var e = (t - c.t) / 1300, y = c.y - 18 - e * 46;
          g.globalAlpha = 1 - e; g.fillStyle = giallo; g.strokeStyle = '#1c2226'; g.lineWidth = 1.5;
          rett(g, c.x - 15, y - 11, 30, 20, 4); g.fill(); g.stroke();
          g.fillStyle = '#1c2226'; g.font = '700 12px system-ui, sans-serif'; g.textAlign = 'center'; g.textBaseline = 'middle';
          g.fillText(String(c.n), c.x, y - 1);
        });
        g.globalAlpha = 1;
        var h = 15 + alza * 6, w = 7;
        g.fillStyle = '#ffffff'; g.strokeStyle = '#1c2226'; g.lineWidth = 1.2;
        rett(g, mx - w / 2, my - h, w, h, 3); g.fill(); g.stroke();
        g.fillStyle = rosso; g.fillRect(mx - w / 2 + 0.6, my - h + 2.5, w - 1.2, 3.2);
        return scia.length > 0 || cartelli.length > 0 || Math.abs((hot ? 1 : 0) - alza) > 0.01;
      }
    };
  };

  // Persiana (per Persiana, 18 nov 2026): una piccola persiana verde sulla punta, con le stecche chiuse; sui link le
  // stecche si aprono e lasciano passare una lama di luce calda; al clic la persiana sbatte, si apre tutta e si richiude.
  STILI.persiana = function () {
    var T = tela({ mondo: true }), st = {}, apri = 0, colpo = 0;
    var verde = risolvi(CFG.ringColor || '#2f5d50'), luce = risolvi(CFG.dotColor || '#e0b04a');
    window.addEventListener('pointerdown', function (e) { if (meno || e.pointerType === 'touch') return; colpo = 1; chiedi(); });
    return {
      frame: function (t) {
        var s = passo(st, t);
        colpo = Math.max(0, colpo - 0.035 * s);
        var voglia = Math.max(hot ? 0.7 : 0, Math.sin(colpo * Math.PI));
        apri += (voglia - apri) * 0.2 * s;
        var g = pulisci(T), W = 16, H = 20, x = mx - W / 2, y = my - H / 2;
        if (apri > 0.02) {
          var al = g.createRadialGradient(mx, my, 1, mx, my, 26);
          al.addColorStop(0, luce); al.addColorStop(1, 'rgba(255,200,80,0)');
          g.globalAlpha = apri * 0.55; g.fillStyle = al; g.beginPath(); g.arc(mx, my, 26, 0, 6.283); g.fill(); g.globalAlpha = 1;
        }
        g.fillStyle = 'rgba(255,255,255,.9)'; g.fillRect(x - 1.5, y - 1.5, W + 3, H + 3);
        g.strokeStyle = verde; g.lineWidth = 1.6; g.strokeRect(x, y, W, H);
        for (var k = 0; k < 6; k++) {
          var yy = y + 2.5 + k * 3.1, alto = 1.8 * (1 - apri) + 0.3;
          g.fillStyle = verde; g.fillRect(x + 2, yy, W - 4, alto);
          if (apri > 0.05) { g.globalAlpha = apri; g.fillStyle = luce; g.fillRect(x + 2, yy + alto, W - 4, 0.9 * apri); g.globalAlpha = 1; }
        }
        return colpo > 0 || Math.abs((hot ? 0.7 : 0) - apri) > 0.01;
      }
    };
  };

  // Raggi (per Ruota Libera, 18 nov 2026): una ruota di bici sulla punta, che gira quanto ci si muove (come se rotolasse
  // sotto il puntatore) e piano piano si ferma; sui link la ruota si fa più grande; al clic suona il campanello: due archi
  // che vibrano e la scritta «drin».
  STILI.raggi = function () {
    var T = tela({ mondo: true }), st = {}, ang = 0, vang = 0, gonfia = 0, drin = [], px = null;
    var gomma = risolvi(CFG.ringColor || '#ff6a1a'), mozzo = risolvi(CFG.dotColor || '#2cd3a8');
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      drin.push({ x: mx, y: my, t: performance.now() }); if (drin.length > 4) drin.shift(); vang += 0.6; chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t);
        if (px !== null) { if (!meno) vang += (mx - px) / 11 * 0.5; }
        px = mx;
        vang *= Math.pow(0.9, s); ang += vang * s;
        gonfia += ((hot ? 1 : 0) - gonfia) * 0.16 * s;
        var g = pulisci(T), R = 10 + gonfia * 4, k, q;
        g.save(); g.translate(mx, my); g.rotate(ang);
        g.strokeStyle = gomma; g.lineWidth = 2.6; g.beginPath(); g.arc(0, 0, R, 0, 6.283); g.stroke();
        g.strokeStyle = 'rgba(255,255,255,.85)'; g.lineWidth = 0.8;
        for (k = 0; k < 10; k++) { var a = k / 10 * 6.283; g.beginPath(); g.moveTo(0, 0); g.lineTo(Math.cos(a) * (R - 1.4), Math.sin(a) * (R - 1.4)); g.stroke(); }
        g.fillStyle = mozzo; g.beginPath(); g.arc(0, 0, 2.4, 0, 6.283); g.fill();
        g.restore();
        drin = drin.filter(function (d) { return t - d.t < 900; });
        drin.forEach(function (d) {
          var e = (t - d.t) / 900, v = Math.sin(e * 40) * 2 * (1 - e);
          g.globalAlpha = 1 - e; g.strokeStyle = mozzo; g.lineWidth = 1.6;
          for (q = 0; q < 2; q++) {
            g.beginPath(); g.arc(d.x + v, d.y, 16 + q * 6 + e * 10, -0.9, 0.9); g.stroke();
            g.beginPath(); g.arc(d.x - v, d.y, 16 + q * 6 + e * 10, Math.PI - 0.9, Math.PI + 0.9); g.stroke();
          }
          g.fillStyle = gomma; g.font = '800 12px system-ui, sans-serif'; g.textAlign = 'center';
          g.fillText('drin', d.x, d.y - 26 - e * 18);
        });
        g.globalAlpha = 1;
        return Math.abs(vang) > 0.002 || drin.length > 0 || Math.abs((hot ? 1 : 0) - gonfia) > 0.01;
      }
    };
  };

  // Oculare (per Vetrino, 18 nov 2026): l'oculare del microscopio sulla punta, un cerchio col reticolo e la scala; mentre
  // ci si muove è sfocato (sdoppiato), quando ci si ferma va a fuoco; sui link ingrandisce; al clic, per un attimo, dentro
  // l'oculare compare una cellula colorata come in un vetrino.
  STILI.oculare = function () {
    var T = tela({ mondo: true }), st = {}, fuoco = 1, zoom = 0, cellule = [], ux = null, uy = null;
    var viola = risolvi(CFG.ringColor || '#7b3fe4'), rosa = risolvi(CFG.dotColor || '#e8467c');
    window.addEventListener('pointerdown', function (e) { if (meno || e.pointerType === 'touch') return; cellule.push({ t: performance.now() }); chiedi(); });
    function disegna(g, x, y, R, a) {
      g.globalAlpha = a; g.strokeStyle = viola; g.lineWidth = 1.6; g.beginPath(); g.arc(x, y, R, 0, 6.283); g.stroke();
      g.lineWidth = 0.8; g.beginPath(); g.moveTo(x - R + 3, y); g.lineTo(x + R - 3, y); g.moveTo(x, y - R + 3); g.lineTo(x, y + R - 3); g.stroke();
      for (var k = -4; k <= 4; k++) {
        if (!k) continue;
        var l = k % 2 ? 1.6 : 3; g.beginPath(); g.moveTo(x + k * R / 5.5, y - l); g.lineTo(x + k * R / 5.5, y + l); g.stroke();
      }
    }
    return {
      frame: function (t) {
        var s = passo(st, t), corsa = 0;
        if (ux !== null) { if (!meno) corsa = Math.min(1, Math.hypot(mx - ux, my - uy) / 9); }
        ux = mx; uy = my;
        fuoco += ((1 - corsa) - fuoco) * 0.12 * s;
        zoom += ((hot ? 1 : 0) - zoom) * 0.16 * s;
        var g = pulisci(T), R = 12 + zoom * 6, d = (1 - fuoco) * 4;
        cellule = cellule.filter(function (c) { return t - c.t < 1200; });
        cellule.forEach(function (c) {
          var e = (t - c.t) / 1200, a = e < 0.2 ? e / 0.2 : 1 - (e - 0.2) / 0.8;
          g.globalAlpha = a * 0.85; g.fillStyle = rosa; g.beginPath(); g.ellipse(mx + 2, my - 1, R * 0.55, R * 0.42, 0.5, 0, 6.283); g.fill();
          g.fillStyle = viola; g.beginPath(); g.arc(mx + 3, my - 2, R * 0.2, 0, 6.283); g.fill();
        });
        if (d > 0.3) { disegna(g, mx - d, my, R, 0.35); disegna(g, mx + d, my, R, 0.35); }
        disegna(g, mx, my, R, d > 0.3 ? 0.55 : 1);
        g.globalAlpha = 1;
        return fuoco < 0.99 || cellule.length > 0 || Math.abs((hot ? 1 : 0) - zoom) > 0.01;
      }
    };
  };

  // Doppione (per Doppio Senso, 18 nov 2026): il puntatore ha un gemello allo specchio, dall'altra parte dello schermo,
  // una freccia girata che fa le stesse cose al contrario; un filo sottile li unisce passando per il centro. Sui link si
  // accendono tutti e due; al clic da ciascuno parte un'onda.
  STILI.doppione = function () {
    var T = tela({ mondo: true }), st = {}, luce = 0, onde = [];
    var oro = risolvi(CFG.ringColor || '#e8b04b'), carta = risolvi(CFG.dotColor || '#eee8dc');
    window.addEventListener('pointerdown', function (e) { if (meno || e.pointerType === 'touch') return; onde.push({ x: mx, y: my, t: performance.now() }); chiedi(); });
    function freccia(g, x, y, verso, col) {
      g.save(); g.translate(x, y); g.scale(verso, 1);
      g.fillStyle = col; g.strokeStyle = 'rgba(0,0,0,.55)'; g.lineWidth = 1;
      g.beginPath(); g.moveTo(0, 0); g.lineTo(0, 16); g.lineTo(4.2, 12.2); g.lineTo(7, 18.5); g.lineTo(9.4, 17.4); g.lineTo(6.6, 11.3); g.lineTo(12, 11.3); g.closePath();
      g.fill(); g.stroke(); g.restore();
    }
    return {
      frame: function (t) {
        var s = passo(st, t), W = window.innerWidth, gx = W - mx;
        luce += ((hot ? 1 : 0) - luce) * 0.16 * s;
        var g = pulisci(T);
        g.globalAlpha = 0.22 + luce * 0.3; g.strokeStyle = oro; g.lineWidth = 1; g.setLineDash([3, 6]);
        g.beginPath(); g.moveTo(mx, my); g.quadraticCurveTo(W / 2, my - 40, gx, my); g.stroke(); g.setLineDash([]);
        onde = onde.filter(function (o) { return t - o.t < 800; });
        onde.forEach(function (o) {
          var e = (t - o.t) / 800; g.globalAlpha = 1 - e; g.strokeStyle = oro; g.lineWidth = 1.4;
          g.beginPath(); g.arc(o.x, o.y, 6 + e * 26, 0, 6.283); g.stroke();
          g.beginPath(); g.arc(W - o.x, o.y, 6 + e * 26, 0, 6.283); g.stroke();
        });
        g.globalAlpha = 0.55 + luce * 0.45;
        freccia(g, gx, my, -1, luce > 0.5 ? oro : carta);
        g.globalAlpha = 1;
        return onde.length > 0 || Math.abs((hot ? 1 : 0) - luce) > 0.01;
      }
    };
  };

  // Un rettangolo con gli angoli tondi (per i disegni dei cursori qui sotto).
  function tondo(g, x, y, w, h, r) {
    g.beginPath(); g.moveTo(x + r, y); g.arcTo(x + w, y, x + w, y + h, r); g.arcTo(x + w, y + h, x, y + h, r);
    g.arcTo(x, y + h, x, y, r); g.arcTo(x, y, x + w, y, r); g.closePath();
  }

  // Segnalibro (per Dorso, 18 nov 2026): un nastro di raso appeso alla punta, con la coda a coda di rondine, che dondola
  // quando ti muovi e si ferma piano; sui link si allunga; al clic, lì dove hai cliccato, si piega l'angolo di una pagina.
  STILI.segnalibro = function () {
    var T = tela({ mondo: true }), st = {}, ang = 0, vang = 0, lung = 0, orecchie = [], ux = null;
    var raso = risolvi(CFG.ringColor || '#8c1c2b'), oro = risolvi(CFG.dotColor || '#c9a24a');
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      orecchie.push({ x: mx, y: my, t: performance.now() }); if (orecchie.length > 5) orecchie.shift(); chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t), dx = ux === null ? 0 : mx - ux;
        ux = mx;
        if (meno) { ang = 0; vang = 0; } else {
          vang += (-dx * 0.006 - ang * 0.07) * s; vang *= Math.pow(0.9, s); ang += vang * s;
        }
        lung += ((hot ? 1 : 0) - lung) * 0.15 * s;
        var g = pulisci(T), L = 30 + lung * 16, W = 9;
        g.save(); T.glifo(g, mx, my); g.rotate(ang);
        g.fillStyle = raso; g.beginPath(); g.moveTo(-W / 2, 0); g.lineTo(W / 2, 0); g.lineTo(W / 2, L);
        g.lineTo(0, L - 6); g.lineTo(-W / 2, L); g.closePath(); g.fill();
        g.globalAlpha = 0.22; g.fillStyle = '#ffffff'; g.fillRect(-W / 2 + 1.6, 1, 1.3, L - 5); g.globalAlpha = 1;
        g.fillStyle = oro; g.beginPath(); g.arc(0, 0, 3.2, 0, 6.283); g.fill();
        g.restore();
        orecchie = orecchie.filter(function (o) { return t - o.t < 1500; });
        orecchie.forEach(function (o) {
          var e = (t - o.t) / 1500, k = Math.min(1, e / 0.18), a = e < 0.18 ? 1 : 1 - (e - 0.18) / 0.82, q = 26 * k;
          g.globalAlpha = a * 0.25; g.fillStyle = '#000000';
          g.beginPath(); g.moveTo(o.x, o.y); g.lineTo(o.x + q + 3, o.y); g.lineTo(o.x, o.y + q + 3); g.closePath(); g.fill();
          g.globalAlpha = a; g.fillStyle = oro;
          g.beginPath(); g.moveTo(o.x + q, o.y); g.lineTo(o.x + q, o.y + q); g.lineTo(o.x, o.y + q); g.closePath(); g.fill();
          g.strokeStyle = raso; g.lineWidth = 1; g.stroke();
        });
        g.globalAlpha = 1;
        return Math.abs(vang) > 0.0008 || Math.abs(ang) > 0.002 || orecchie.length > 0 || Math.abs((hot ? 1 : 0) - lung) > 0.01;
      }
    };
  };

  // Torcia (per Lanterna, 18 nov 2026): la pagina al buio; si vede solo dove punti la torcia, nell'alone caldo del
  // fascio, e sui link il fascio si allarga. Unico cursore che resta su telefoni e tablet: lì la torcia sta al centro
  // dello schermo e la pagina le scorre sotto. Col tasto Tab la luce va sull'elemento col focus. L'interruttore in basso
  // a sinistra (la «Parola del cursore», di serie «Luce») accende la luce in tutta la pagina e la tiene accesa per la
  // visita. Con meno movimento la luce non insegue: sta sul puntatore.
  STILI.torcia = function () {
    var v = nodo('torcia');
    v.classList.remove(P);
    v.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:' + (Z - 2) + ';transition:opacity .7s;'
      + 'background:radial-gradient(circle var(--ot-r,190px) at var(--ot-x,50vw) var(--ot-y,45vh),'
      + 'color-mix(in srgb,var(--oc-ring) 10%,transparent) 0,transparent 55%,rgba(4,4,7,.94) 100%)';
    var p = touchSolo ? null : nodo('punto');
    var cssT = document.createElement('style');
    cssT.textContent = '.olo-cur-interruttore{position:fixed;left:16px;bottom:16px;z-index:' + (Z - 1) + ';display:inline-flex;'
      + 'align-items:center;gap:8px;padding:9px 15px 9px 11px;border-radius:999px;cursor:pointer;'
      + 'border:1px solid color-mix(in srgb,var(--oc-ring) 50%,transparent);'
      + 'background:color-mix(in srgb,var(--olo-color-dark,#0b0b10) 84%,transparent);color:var(--olo-color-light,#f4efe4);'
      + 'font:600 13px/1 var(--olo-font-family,system-ui),system-ui,sans-serif;letter-spacing:.04em}'
      + '.olo-cur-interruttore:focus-visible{outline:2px solid var(--oc-ring);outline-offset:3px}'
      + '.olo-cur-interruttore svg{width:18px;height:18px;flex:none;color:var(--oc-ring)}'
      + '.olo-cur-interruttore[aria-pressed="true"] svg{filter:drop-shadow(0 0 6px var(--oc-ring))}';
    document.head.appendChild(cssT);
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'olo-tokens olo-cur-interruttore';
    b.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" '
      + 'stroke-linejoin="round" aria-hidden="true"><path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.6 10.8c.7.5 1.1 1.3 1.1 2.2h5'
      + 'c0-.9.4-1.7 1.1-2.2A6 6 0 0 0 12 3z"/></svg><span></span>';
    b.lastChild.textContent = (CFG.label && CFG.label !== 'Apri') ? CFG.label : 'Luce';
    document.body.appendChild(b);
    if (nodiNuovi) nodiNuovi.push(b);
    var acceso = false;
    try { acceso = window.sessionStorage.getItem('olo-torcia') === '1'; } catch (e) { acceso = false; }
    function metti(on) {
      acceso = on;
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
      v.style.opacity = on ? '0' : '1';
      try { window.sessionStorage.setItem('olo-torcia', on ? '1' : '0'); } catch (e) { /* senza memoria resta per la pagina */ }
    }
    b.addEventListener('click', function () { metti(!acceso); });
    metti(acceso);
    var fx = null, fy = null, fr = 0, umx = mx, umy = my;
    document.addEventListener('focusin', function (e) {
      var el = e.target, ok = false;
      if (!el || !el.getBoundingClientRect || el === b) return;
      try { ok = el.matches(':focus-visible'); } catch (er) { ok = false; }
      if (!ok) return;
      var r = el.getBoundingClientRect();
      fx = r.left + r.width / 2; fy = r.top + r.height / 2; fr = Math.max(150, Math.hypot(r.width, r.height) * 0.8);
      chiedi();
    });
    window.addEventListener('resize', chiedi);
    var x = innerWidth / 2, y = innerHeight * 0.45, r = 190;
    return {
      frame: function () {
        if (mx !== umx || my !== umy) { fx = null; umx = mx; umy = my; }
        var tx, ty, tr;
        if (fx !== null) { tx = fx; ty = fy; tr = fr; }
        else if (touchSolo || mx < -100) { tx = innerWidth / 2; ty = innerHeight * 0.45; tr = Math.min(innerWidth * 0.46, 230); }
        else { tx = mx; ty = my; tr = hot ? 260 : 190; }
        var k = meno ? 1 : 0.2;
        x += (tx - x) * k; y += (ty - y) * k; r += (tr - r) * (meno ? 1 : 0.12);
        v.style.setProperty('--ot-x', x.toFixed(1) + 'px');
        v.style.setProperty('--ot-y', y.toFixed(1) + 'px');
        v.style.setProperty('--ot-r', r.toFixed(1) + 'px');
        if (p) sposta(p, mx, my);
        return Math.abs(tx - x) + Math.abs(ty - y) > 0.3 || Math.abs(tr - r) > 0.5;
      }
    };
  };

  // Telecomando (per Zapping, 18 nov 2026): un piccolo telecomando sulla punta, col led rosso che si accende sui link;
  // al clic parte il lampo dell'infrarosso e accanto compare il numero del canale, che sale a ogni clic.
  STILI.telecomando = function () {
    var T = tela(), st = {}, led = 0, lampi = [], canale = 0;
    var corpo = risolvi(CFG.ringColor || '#1d1d22'), rosso = risolvi(CFG.dotColor || '#ff3b30');
    window.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return;
      canale = canale % 99 + 1;
      lampi.push({ x: mx, y: my, t: performance.now(), n: canale }); if (lampi.length > 4) lampi.shift(); chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t), q, c;
        led += ((hot ? 1 : 0) - led) * 0.2 * s;
        var g = pulisci(T);
        g.save(); g.translate(mx, my); g.rotate(-0.55);
        g.fillStyle = corpo; tondo(g, -7, 2, 14, 36, 5); g.fill();
        g.strokeStyle = 'rgba(255,255,255,.28)'; g.lineWidth = 0.8; tondo(g, -7, 2, 14, 36, 5); g.stroke();
        g.fillStyle = 'rgba(255,255,255,.6)';
        for (q = 0; q < 3; q++) for (c = 0; c < 2; c++) { g.beginPath(); g.arc(-3 + c * 6, 17 + q * 6, 1.4, 0, 6.283); g.fill(); }
        g.globalAlpha = 0.35 + led * 0.65; g.fillStyle = rosso; g.beginPath(); g.arc(0, 7, 2.2, 0, 6.283); g.fill();
        g.globalAlpha = 1; g.restore();
        lampi = lampi.filter(function (l) { return t - l.t < 1100; });
        lampi.forEach(function (l) {
          var e = (t - l.t) / 1100;
          g.globalAlpha = 1 - e; g.strokeStyle = rosso; g.lineWidth = 1.5;
          for (q = 0; q < 3; q++) { g.beginPath(); g.arc(l.x, l.y, 7 + q * 7 + e * 16, -2.5, -1.3); g.stroke(); }
          g.fillStyle = rosso; g.font = '700 16px ui-monospace, monospace'; g.textAlign = 'left';
          g.fillText('CH ' + (l.n < 10 ? '0' : '') + l.n, l.x + 16, l.y - 18 - e * 12);
        });
        g.globalAlpha = 1;
        return lampi.length > 0 || Math.abs((hot ? 1 : 0) - led) > 0.01;
      }
    };
  };

  // Pulsante (per Torre, 18 nov 2026): il pulsante di chiamata dell'ascensore sulla punta, un anello d'ottone con la
  // freccia in su; sui link si accende; al clic si illumina del tutto, fa «din» e da lì si allarga un anello di luce.
  STILI.pulsante = function () {
    var T = tela(), st = {}, luce = 0, botta = 0, din = [];
    var ottone = risolvi(CFG.ringColor || '#c9a45c'), lume = risolvi(CFG.dotColor || '#ffd66b');
    window.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return;
      botta = 1; din.push({ x: mx, y: my, t: performance.now() }); if (din.length > 4) din.shift(); chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t);
        luce += ((hot ? 1 : 0) - luce) * 0.18 * s; botta *= Math.pow(0.93, s);
        var g = pulisci(T), acceso = Math.min(1, luce * 0.7 + botta);
        g.save(); g.translate(mx, my);
        g.fillStyle = '#26211a'; g.beginPath(); g.arc(0, 0, 12, 0, 6.283); g.fill();
        g.strokeStyle = ottone; g.lineWidth = 3; g.beginPath(); g.arc(0, 0, 12, 0, 6.283); g.stroke();
        if (acceso > 0.02) {
          g.globalAlpha = acceso; g.fillStyle = lume; g.shadowColor = lume; g.shadowBlur = 10 * acceso;
          g.beginPath(); g.arc(0, 0, 8.5, 0, 6.283); g.fill(); g.shadowBlur = 0; g.globalAlpha = 1;
        }
        g.fillStyle = acceso > 0.5 ? '#26211a' : ottone;
        g.beginPath(); g.moveTo(0, -5); g.lineTo(4.6, 2.6); g.lineTo(-4.6, 2.6); g.closePath(); g.fill();
        g.restore();
        din = din.filter(function (d) { return t - d.t < 1000; });
        din.forEach(function (d) {
          var e = (t - d.t) / 1000;
          g.globalAlpha = 1 - e; g.strokeStyle = lume; g.lineWidth = 2 * (1 - e) + 0.5;
          g.beginPath(); g.arc(d.x, d.y, 14 + e * 30, 0, 6.283); g.stroke();
          g.fillStyle = lume; g.font = 'italic 700 14px Georgia, serif'; g.textAlign = 'center';
          g.fillText('din', d.x, d.y - 24 - e * 20);
        });
        g.globalAlpha = 1;
        return botta > 0.01 || din.length > 0 || Math.abs((hot ? 1 : 0) - luce) > 0.01;
      }
    };
  };

  // Bilancia (per Scontrino, 18 nov 2026): il quadrante di una bilancia da banco sulla punta; la lancetta segna quanto
  // ti muovi (fino a un chilo) e torna a zero quando ti fermi; sui link segna «€»; al clic esce un pezzo di scontrino
  // col peso, che cade girando.
  STILI.bilancia = function () {
    var T = tela({ mondo: true }), st = {}, peso = 0, euro = 0, strappi = [], ux = null, uy = null;
    var smalto = risolvi(CFG.ringColor || '#f2efe6'), rosso = risolvi(CFG.dotColor || '#d8342b'), china = '#1f1c18';
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      strappi.push({ x: mx, y: my, t: performance.now(), g: Math.max(50, Math.round(peso / 10) * 10), r: (Math.random() - 0.5) * 0.6 });
      if (strappi.length > 5) strappi.shift(); chiedi();
    });
    function kg(gr) { return (gr / 1000).toFixed(3).replace('.', ',') + ' kg'; }
    return {
      frame: function (t) {
        var s = passo(st, t), corsa = 0;
        if (ux !== null) { if (!meno) corsa = Math.hypot(mx - ux, my - uy); }
        ux = mx; uy = my;
        peso += (Math.min(1000, corsa * 45) - peso) * 0.12 * s;
        euro += ((hot ? 1 : 0) - euro) * 0.16 * s;
        var g = pulisci(T), a = -2.3 + 4.6 * Math.min(1, peso / 1000), k;
        g.save(); T.glifo(g, mx, my);
        g.fillStyle = smalto; g.beginPath(); g.arc(0, 0, 15, 0, 6.283); g.fill();
        g.strokeStyle = china; g.lineWidth = 1.6; g.stroke();
        g.lineWidth = 0.8;
        for (k = 0; k <= 10; k++) {
          var b = -2.3 + 4.6 * k / 10, l = k % 5 ? 2.5 : 4.5;
          g.beginPath(); g.moveTo(Math.sin(b) * (13 - l), -Math.cos(b) * (13 - l)); g.lineTo(Math.sin(b) * 13, -Math.cos(b) * 13); g.stroke();
        }
        g.strokeStyle = rosso; g.lineWidth = 1.5; g.beginPath(); g.moveTo(0, 0); g.lineTo(Math.sin(a) * 11, -Math.cos(a) * 11); g.stroke();
        g.fillStyle = china; g.beginPath(); g.arc(0, 0, 1.8, 0, 6.283); g.fill();
        g.font = '700 9px ui-monospace, monospace'; g.textAlign = 'center';
        g.fillText(euro > 0.5 ? '€' : Math.round(peso / 10) * 10 + ' g', 0, 26);
        g.restore();
        strappi = strappi.filter(function (q) { return t - q.t < 1600; });
        strappi.forEach(function (q) {
          var e = (t - q.t) / 1600, y = q.y + 16 + e * e * 160, w = 58, h = 26, z;
          g.save(); g.globalAlpha = e < 0.7 ? 1 : 1 - (e - 0.7) / 0.3;
          g.translate(q.x, y); g.rotate(q.r + e * q.r * 3);
          g.fillStyle = '#fbfaf5'; g.beginPath(); g.moveTo(-w / 2, -h / 2); g.lineTo(w / 2, -h / 2); g.lineTo(w / 2, h / 2);
          for (z = 0; z < 8; z++) g.lineTo(w / 2 - (z + 0.5) * w / 8, h / 2 + (z % 2 ? 0 : 3));
          g.lineTo(-w / 2, h / 2); g.closePath(); g.fill();
          g.strokeStyle = 'rgba(0,0,0,.12)'; g.lineWidth = 0.6; g.stroke();
          g.fillStyle = china; g.font = '600 9px ui-monospace, monospace'; g.textAlign = 'center';
          g.fillText('1 x ' + kg(q.g), 0, 3);
          g.restore();
        });
        g.globalAlpha = 1;
        return peso > 1 || strappi.length > 0 || Math.abs((hot ? 1 : 0) - euro) > 0.01;
      }
    };
  };

  // Colla (per Affisso, 20 nov 2026): la pennellessa dell'attacchino; muovendosi stende una striscia di colla lucida che
  // asciuga in un secondo e mezzo; sui link le setole premono; al clic una pennellata più carica.
  STILI.colla = function () {
    var T = tela({ mondo: true }), st = {}, scie = [], ux = null, uy = null, premi = 0;
    var legno = risolvi(CFG.ringColor || '#c8a165'), setole = risolvi(CFG.dotColor || '#2b2520');
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      scie.push({ x0: mx - 26, y0: my + 4, x1: mx + 26, y1: my + 10, t: performance.now(), w: 26 }); chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t);
        if (ux !== null) {
          if (!meno) {
            if (Math.hypot(mx - ux, my - uy) > 2) scie.push({ x0: ux, y0: uy, x1: mx, y1: my, t: t, w: 16 + premi * 6 });
          }
        }
        ux = mx; uy = my;
        premi += ((hot ? 1 : 0) - premi) * 0.2 * s;
        while (scie.length > 160) scie.shift();
        scie = scie.filter(function (q) { return t - q.t < 1500; });
        var g = pulisci(T);
        g.lineCap = 'round';
        scie.forEach(function (q) {
          var e = 1 - (t - q.t) / 1500;
          g.strokeStyle = 'rgba(250,246,232,' + (0.34 * e).toFixed(3) + ')'; g.lineWidth = q.w;
          g.beginPath(); g.moveTo(q.x0, q.y0); g.lineTo(q.x1, q.y1); g.stroke();
          g.strokeStyle = 'rgba(255,255,255,' + (0.45 * e * e).toFixed(3) + ')'; g.lineWidth = 2;
          g.beginPath(); g.moveTo(q.x0, q.y0 - q.w * 0.25); g.lineTo(q.x1, q.y1 - q.w * 0.25); g.stroke();
        });
        g.save(); T.glifo(g, mx, my); g.rotate(-0.5);
        g.fillStyle = legno; g.fillRect(-3, -34, 6, 20);
        g.fillStyle = '#9aa0a6'; g.fillRect(-11, -15, 22, 6);
        g.fillStyle = setole; g.beginPath(); g.moveTo(-11, -9); g.lineTo(11, -9); g.lineTo(11 + premi * 2, 2); g.lineTo(-11 - premi * 2, 2); g.closePath(); g.fill();
        g.restore();
        return scie.length > 0 || Math.abs((hot ? 1 : 0) - premi) > 0.01;
      }
    };
  };

  // Ciak (per Diaframma, 20 nov 2026): la claquette accanto alla freccia, con l'asta aperta; sui link si apre di più,
  // pronta; al clic batte e il numero della ripresa sale (SC 1 · TK 2, 3…), con un lampo breve.
  STILI.ciak = function () {
    var T = tela(), st = {}, apre = 0, batti = -1, ripresa = 1;
    var lavagna = risolvi(CFG.ringColor || '#1b1b1d'), segno = risolvi(CFG.dotColor || '#f5f1e8');
    window.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return;
      batti = performance.now(); ripresa = ripresa % 99 + 1; chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t);
        apre += ((hot ? 1 : 0) - apre) * 0.18 * s;
        var colpo = batti > 0 ? (t - batti) / 260 : 2, ang;
        if (colpo < 1) ang = 0.62 * (1 - Math.min(1, colpo * 3)); else ang = 0.28 + apre * 0.34;
        var g = pulisci(T), x = mx + 26, y = my + 24;
        g.save(); g.translate(x, y);
        g.fillStyle = lavagna; g.fillRect(-16, -2, 32, 22);
        g.fillStyle = segno; g.font = '700 6.5px ui-monospace, monospace'; g.textAlign = 'center';
        g.fillText('SC 1 · TK ' + ripresa, 0, 9); g.fillRect(-12, 13, 24, 0.8);
        g.save(); g.translate(-16, -2); g.rotate(-ang);
        for (var k = 0; k < 6; k++) { g.fillStyle = k % 2 ? segno : lavagna; g.beginPath(); g.moveTo(k * 32 / 6, 0); g.lineTo((k + 1) * 32 / 6, 0); g.lineTo((k + 1) * 32 / 6 + 3, -6); g.lineTo(k * 32 / 6 + 3, -6); g.closePath(); g.fill(); }
        g.restore();
        if (colpo < 1) {
          g.strokeStyle = segno; g.globalAlpha = 1 - colpo; g.lineWidth = 1.2;
          for (var r = 0; r < 3; r++) { g.beginPath(); g.moveTo(18 + r * 2, -6 - r * 4); g.lineTo(24 + r * 3, -9 - r * 6); g.stroke(); }
          g.globalAlpha = 1;
        }
        g.restore();
        return colpo < 1 || Math.abs((hot ? 1 : 0) - apre) > 0.01;
      }
    };
  };

  // Cuore (per Pollice, 20 nov 2026): un cuore piccolo accanto alla freccia che sui link si riempie; al clic ne salgono
  // altri ondeggiando, come i «mi piace» di una diretta.
  STILI.cuore = function () {
    var T = tela({ mondo: true }), st = {}, pieno = 0, salgono = [];
    var rosa = risolvi(CFG.dotColor || '#ff3d6e'), bordo = risolvi(CFG.ringColor || '#ffffff');
    function disegna(g, r, riempi, linea) {
      g.beginPath(); g.moveTo(0, r * 0.9);
      g.bezierCurveTo(-r * 1.6, -r * 0.2, -r * 0.7, -r * 1.4, 0, -r * 0.5);
      g.bezierCurveTo(r * 0.7, -r * 1.4, r * 1.6, -r * 0.2, 0, r * 0.9); g.closePath();
      if (riempi) { g.fillStyle = riempi; g.fill(); }
      if (linea) { g.strokeStyle = linea; g.lineWidth = 1.6; g.stroke(); }
    }
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      for (var k = 0; k < 8; k++) salgono.push({ x: mx, y: my, t: performance.now() + k * 70, f: Math.random() * 6.28, r: 5 + Math.random() * 5, d: (Math.random() - 0.5) * 50 });
      chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t);
        pieno += ((hot ? 1 : 0) - pieno) * 0.2 * s;
        var g = pulisci(T);
        salgono = salgono.filter(function (q) { return t - q.t < 1500; });
        salgono.forEach(function (q) {
          var e = (t - q.t) / 1500; if (e < 0) return;
          g.save(); g.globalAlpha = e < 0.75 ? 1 : 1 - (e - 0.75) / 0.25;
          g.translate(q.x + Math.sin(q.f + e * 9) * 10 + q.d * e, q.y - e * 150); g.scale(0.6 + e * 0.6, 0.6 + e * 0.6);
          disegna(g, q.r, rosa, null); g.restore();
        });
        g.globalAlpha = 1;
        g.save(); T.glifo(g, mx + 22, my + 22); g.scale(1 + pieno * 0.15, 1 + pieno * 0.15);
        disegna(g, 9, pieno > 0.05 ? 'rgba(' + rosa.replace(/[^\d,]/g, '').split(',').slice(0, 3).join(',') + ',' + pieno.toFixed(2) + ')' : null, pieno > 0.5 ? rosa : bordo);
        g.restore();
        return salgono.length > 0 || Math.abs((hot ? 1 : 0) - pieno) > 0.01;
      }
    };
  };

  // Taglierino (per Strappo, 20 nov 2026): la lama accanto alla freccia; tenendo premuto si taglia la pagina, un taglio
  // sottile col bordo chiaro che si richiude in due secondi; sui link la lama esce di più.
  STILI.taglierino = function () {
    var T = tela({ mondo: true }), st = {}, tagli = [], giu = false, ux = null, uy = null, lama = 0;
    var manico = risolvi(CFG.dotColor || '#f2c230'), acciaio = '#c9ced4';
    // Mentre si taglia il browser non deve selezionare il testo sotto la lama (20 nov 2026: tenendo premuto si
    // evidenziava il titolo). Fuori da campi, pulsanti e link: lì la pressione resta quella di sempre.
    if (!document.getElementById('olo-taglia-css')) {
      var cssT = document.createElement('style');
      cssT.id = 'olo-taglia-css';
      cssT.textContent = 'html.olo-taglia,html.olo-taglia *{-webkit-user-select:none!important;user-select:none!important}';
      document.head.appendChild(cssT);
    }
    var smetti = function () { giu = false; document.documentElement.classList.remove('olo-taglia'); };
    window.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return;
      giu = true; ux = mx; uy = my;
      if (!(e.target && e.target.closest && e.target.closest('a,button,input,textarea,select,label,[contenteditable],[role="slider"]'))) {
        document.documentElement.classList.add('olo-taglia');
        var sel = window.getSelection && window.getSelection();
        if (sel && sel.removeAllRanges) sel.removeAllRanges();
      }
    });
    window.addEventListener('pointerup', smetti);
    window.addEventListener('pointercancel', smetti);
    return {
      frame: function (t) {
        var s = passo(st, t);
        lama += ((hot ? 1 : 0) - lama) * 0.2 * s;
        if (giu) { if (!meno) { if (Math.hypot(mx - ux, my - uy) > 1.5) { tagli.push({ x0: ux, y0: uy, x1: mx, y1: my, t: t }); ux = mx; uy = my; } } }
        while (tagli.length > 400) tagli.shift();
        tagli = tagli.filter(function (q) { return t - q.t < 2000; });
        var g = pulisci(T);
        g.lineCap = 'round';
        tagli.forEach(function (q) {
          var e = 1 - (t - q.t) / 2000;
          g.strokeStyle = 'rgba(255,255,255,' + (0.7 * e).toFixed(3) + ')'; g.lineWidth = 2.4 * e + 0.4;
          g.beginPath(); g.moveTo(q.x0 + 0.8, q.y0 + 1); g.lineTo(q.x1 + 0.8, q.y1 + 1); g.stroke();
          g.strokeStyle = 'rgba(10,10,10,' + (0.85 * e).toFixed(3) + ')'; g.lineWidth = 1.2 * e + 0.3;
          g.beginPath(); g.moveTo(q.x0, q.y0); g.lineTo(q.x1, q.y1); g.stroke();
        });
        g.save(); T.glifo(g, mx, my); g.translate(20, 20); g.rotate(0.78);
        g.fillStyle = acciaio; g.beginPath(); g.moveTo(0, -3); g.lineTo(-10 - lama * 5, -3); g.lineTo(-14 - lama * 5, 3); g.lineTo(0, 3); g.closePath(); g.fill();
        g.fillStyle = manico; g.fillRect(0, -5, 26, 10);
        g.fillStyle = 'rgba(0,0,0,.35)'; for (var k = 0; k < 4; k++) g.fillRect(6 + k * 5, -5, 1.5, 10);
        g.restore();
        return tagli.length > 0 || giu || Math.abs((hot ? 1 : 0) - lama) > 0.01;
      }
    };
  };

  // Timbro (per Torchio, 20 nov 2026): un carattere da stampa in piombo accanto alla freccia; sui link scende come per
  // premere; al clic lascia sulla pagina la sua lettera inchiostrata, un po' storta, che sbiadisce.
  STILI.timbro = function () {
    var T = tela({ mondo: true }), st = {}, impronte = [], preme = 0, lettere = 'AEGKMQRSTWÆ&';
    var inchiostro = risolvi(CFG.dotColor || '#c0262d'), piombo = '#8c9096';
    window.addEventListener('pointerdown', function (e) {
      if (meno || e.pointerType === 'touch') return;
      impronte.push({ x: mx, y: my, t: performance.now(), c: lettere.charAt(Math.floor(Math.random() * lettere.length)), r: (Math.random() - 0.5) * 0.25 });
      if (impronte.length > 12) impronte.shift(); chiedi();
    });
    return {
      frame: function (t) {
        var s = passo(st, t);
        preme += ((hot ? 1 : 0) - preme) * 0.2 * s;
        var g = pulisci(T);
        impronte = impronte.filter(function (q) { return t - q.t < 2600; });
        impronte.forEach(function (q) {
          var e = (t - q.t) / 2600;
          g.save(); g.globalAlpha = e < 0.6 ? 0.9 : 0.9 * (1 - (e - 0.6) / 0.4);
          g.translate(q.x, q.y); g.rotate(q.r);
          g.fillStyle = inchiostro; g.font = '700 46px Georgia, \'Times New Roman\', serif'; g.textAlign = 'center'; g.textBaseline = 'middle';
          g.fillText(q.c, 0, 0);
          g.globalCompositeOperation = 'destination-out';
          for (var k = 0; k < 18; k++) { g.fillRect((rumoreC(k + q.t) - 0.5) * 40, (rumoreC(k * 3 + q.t) - 0.5) * 44, 2, 1.5); }
          g.restore();
        });
        g.globalAlpha = 1;
        g.save(); T.glifo(g, mx + 20, my + 18 + preme * 4);
        g.fillStyle = piombo; g.fillRect(-7, -12, 14, 24);
        g.fillStyle = 'rgba(0,0,0,.28)'; g.fillRect(-7, 6, 14, 2.5);
        g.fillStyle = inchiostro; g.fillRect(-7, 10, 14, 2);
        g.fillStyle = '#2a2d31'; g.font = '700 10px Georgia, serif'; g.textAlign = 'center'; g.fillText('A', 0, 2);
        g.restore();
        return impronte.length > 0 || Math.abs((hot ? 1 : 0) - preme) > 0.01;
      }
    };
  };
  function rumoreC(k) { var x = Math.sin(k * 12.9898) * 43758.5453; return x - Math.floor(x); }

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
