/**
 * OLOX — OLOtheme Experience behaviors (replica pixel-perfect olotheme.com).
 * Attivazione per data-attribute: [data-olox="module ..."] sugli elementi emessi
 * dalle tile olox*. Nessuna dipendenza. Fedele al JS del sito sorgente.
 * HAND-AUTHORED (non buildato da Vite). Versione = OLOBUILD_VERSION.
 */
(function () {
  'use strict';
  if (window.__oloxInit) { return; }
  window.__oloxInit = true;

  const REDUCE = window.matchMedia ? matchMedia('(prefers-reduced-motion: reduce)').matches : false;
  const isMobile = () => matchMedia('(max-width:900px)').matches;

  function ready(fn) {
    if (document.readyState !== 'loading') { fn(); }
    else { document.addEventListener('DOMContentLoaded', fn); }
  }
  function gfmt(s) { return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0'); }
  function cfg(el, fallback) {
    const n = el.querySelector(':scope > script[type="application/json"]');
    if (!n) { return fallback || {}; }
    try { return Object.assign({}, fallback || {}, JSON.parse(n.textContent)); }
    catch (e) { return fallback || {}; }
  }
  function visible(el) {
    const r = el.getBoundingClientRect();
    return r.width > 0 && r.right > 0 && r.left < innerWidth && r.bottom > 0 && r.top < innerHeight;
  }
  const io = new IntersectionObserver((es) => es.forEach((e) => {
    if (e.isIntersecting) { e.target.classList.add('in', 'go'); io.unobserve(e.target); }
  }), { threshold: 0.15 });
  const io0 = new IntersectionObserver((es) => es.forEach((e) => {
    if (e.isIntersecting) { e.target.classList.add('in'); io0.unobserve(e.target); }
  }), { threshold: 0 });

  /* ========================================================================
     MODULI PAGINE PRODOTTO
     ======================================================================== */
  const MODS = {};

  /* reveal generico: .rv dentro il container (o il container stesso) */
  MODS.reveal = (el) => {
    const list = el.classList.contains('rv') ? [el] : el.querySelectorAll('.rv');
    list.forEach((x) => io.observe(x));
  };

  /* brickcard: direzioni alternate */
  MODS.bricks = (el) => {
    el.querySelectorAll('.brickcard').forEach((b, i) => {
      b.style.setProperty('--fx', (i % 2 ? '70px' : '-70px'));
      b.style.setProperty('--fr', (i % 2 ? '2.5deg' : '-2.5deg'));
      b.style.setProperty('--d', ((i % 4) * 0.08) + 's');
      io.observe(b);
    });
  };

  /* biglietti booking */
  MODS.tickets = (el) => {
    el.querySelectorAll('.ticket').forEach((t, i) => {
      t.style.setProperty('--tr', (i % 2 ? '2.5deg' : '-2.5deg'));
      t.style.setProperty('--d', ((i % 3) * 0.1) + 's');
      io.observe(t);
    });
  };

  /* flipboard lang */
  MODS.flips = (el) => {
    el.querySelectorAll('.fliprow').forEach((f, i) => { f.style.setProperty('--d', (i * 0.14) + 's'); io.observe(f); });
  };

  /* url stream lang */
  MODS.urls = (el) => {
    el.querySelectorAll('.url').forEach((u, i) => {
      u.style.setProperty('--fx', i % 2 ? '80px' : '-80px');
      u.style.setProperty('--d', (i * 0.1) + 's');
      io.observe(u);
    });
  };

  /* redacted security (soglia più alta, come il sorgente) */
  MODS.redgrid = (el) => {
    const ior = new IntersectionObserver((es) => es.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('in'); ior.unobserve(e.target); }
    }), { threshold: 0.3 });
    el.querySelectorAll('.redcard').forEach((c) => ior.observe(c));
  };

  /* stanze + corridoi tour */
  MODS.rooms = (el) => {
    const kids = el.querySelectorAll('.rooms > *');
    kids.forEach((x, i) => { x.style.setProperty('--d', (i * 0.16) + 's'); io0.observe(x); });
  };

  /* console translator lang (barre al reveal) */
  MODS.consolego = (el) => { io.observe(el); };

  /* slab pro con gru */
  MODS.proslab = (el) => {
    const pro = el.querySelector('.slab.pro');
    if (pro) { io.observe(pro); }
  };

  /* muro hero build: 84 mattoni + counter */
  MODS.hwall = (el) => {
    const wall = el.querySelector('.ox-hwall');
    if (!wall) { return; }
    const cells = parseInt(wall.getAttribute('data-cells') || '84', 10);
    for (let i = 0; i < cells; i++) {
      const b = document.createElement('i');
      const r = Math.random();
      if (r < 0.18) { b.className = 'k1'; } else if (r < 0.42) { b.className = 'k2'; }
      b.style.setProperty('--d', (Math.random() * 0.9).toFixed(2) + 's');
      wall.insertBefore(b, wall.firstChild);
    }
    requestAnimationFrame(() => wall.classList.add('go'));
    const tEl = wall.querySelector('.count b');
    if (tEl) {
      const to = parseInt(tEl.getAttribute('data-count') || '187', 10);
      let tc = 0;
      const ti = setInterval(() => {
        tc += Math.ceil((to - tc) / 14) || 1;
        if (tc >= to) { tc = to; clearInterval(ti); }
        tEl.textContent = tc;
      }, 50);
    }
  };

  /* assembler sticky build */
  MODS.assembler = (el) => {
    const blks = [...el.querySelectorAll('.blk')];
    const stepNo = el.querySelector('.ox-stepno');
    const stepName = el.querySelector('.stepname');
    let names = [];
    try { names = JSON.parse(el.getAttribute('data-steps') || '[]'); } catch (e) { names = []; }
    let last = -1;
    function onScroll() {
      const r = el.getBoundingClientRect();
      const total = r.height - innerHeight;
      const t = Math.min(Math.max(-r.top / total, 0), 1);
      const n = Math.min(Math.floor(t * (blks.length + 1)), blks.length);
      blks.forEach((b, i) => b.classList.toggle('set', i < n));
      if (n !== last) {
        last = n;
        if (stepNo) { stepNo.textContent = n; }
        if (stepName && names[n]) { stepName.innerHTML = names[n]; }
      }
    }
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
  };

  /* giornata sticky booking (+ orologio hero se presente nella pagina) */
  MODS.dayrail = (el) => {
    let DAY = [];
    try { DAY = JSON.parse(el.getAttribute('data-slots') || '[]'); } catch (e) { DAY = []; }
    const slotsEl = el.querySelector('.slots');
    const slotEls = DAY.map((row) => {
      const d = document.createElement('div'); d.className = 'slot';
      const hh = document.createElement('span'); hh.className = 'hh'; hh.textContent = row[0];
      const what = document.createElement('span'); what.className = 'what'; what.textContent = row[1];
      const who = document.createElement('span'); who.className = 'who'; who.textContent = row[2];
      const st = document.createElement('span'); st.className = 'stamp'; st.textContent = el.getAttribute('data-stamp') || 'Confermato';
      d.appendChild(hh); d.appendChild(what); d.appendChild(who); d.appendChild(st);
      slotsEl.appendChild(d); return d;
    });
    const dayTime = el.querySelector('.daytime');
    const dayOcc = el.querySelector('.ox-dayocc');
    const heroTime = document.querySelector('.oloxp .clocklbl b');
    const handM = document.querySelector('.oloxp .hand.hm');
    const handH = document.querySelector('.oloxp .hand.h2');
    function fmt(mins) { const h = Math.floor(mins / 60), m = Math.round(mins % 60); return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0'); }
    let lastN = -1;
    function onScroll() {
      const doc = document.documentElement;
      const tAll = Math.min(Math.max(scrollY / (doc.scrollHeight - innerHeight), 0), 1);
      const minsAll = 8 * 60 + tAll * 15 * 60;
      if (heroTime) { heroTime.textContent = fmt(minsAll); }
      if (handM) { handM.style.setProperty('--rot', (minsAll % 60) / 60 * 360 + 'deg'); }
      if (handH) { handH.style.setProperty('--rot', (minsAll / 60 % 12) / 12 * 360 + 'deg'); }
      const r = el.getBoundingClientRect();
      const total = r.height - innerHeight;
      const t = Math.min(Math.max(-r.top / total, 0), 1);
      const mins = 8 * 60 + t * 13 * 60;
      const parts = fmt(mins).split(':');
      if (dayTime) { dayTime.innerHTML = parts[0] + ':<em>' + parts[1] + '</em>'; }
      const n = Math.floor(t * 1.08 * DAY.length);
      if (n !== lastN) {
        lastN = n;
        slotEls.forEach((s, i) => s.classList.toggle('b', i < n));
        if (dayOcc) { dayOcc.textContent = Math.min(Math.round(n / DAY.length * 100), 100) + '%'; }
      }
    }
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
  };

  /* scramble hero lang */
  MODS.scramble = (el) => {
    let WORDS = [];
    try { WORDS = JSON.parse(el.getAttribute('data-words') || '[]'); } catch (e) { WORDS = []; }
    if (!WORDS.length) { return; }
    const GL = 'abcdefghijklmnopqrstuvwxyzàéèöü#§*';
    let wi = 0;
    function scrambleTo(word) {
      let frame = 0; const from = el.textContent;
      const max = Math.max(from.length, word.length);
      const t = setInterval(() => {
        frame++;
        let out = '';
        for (let i = 0; i < max; i++) {
          const reveal = frame > i * 2 + 4;
          if (reveal) { out += word[i] || ''; }
          else { out += GL[Math.floor(Math.random() * GL.length)]; }
        }
        el.textContent = out;
        if (frame > max * 2 + 6) { clearInterval(t); el.textContent = word; }
      }, 34);
    }
    setInterval(() => {
      wi = (wi + 1) % WORDS.length;
      if (REDUCE) { el.textContent = WORDS[wi]; } else { scrambleTo(WORDS[wi]); }
    }, 2600);
  };

  /* terminale boot security */
  MODS.term = (el) => {
    let LINES = [];
    try { LINES = JSON.parse(el.getAttribute('data-lines') || '[]'); } catch (e) { LINES = []; }
    const boot = el.querySelector('pre');
    if (!boot || !LINES.length) { return; }
    if (REDUCE) {
      boot.innerHTML = LINES.map((l) => '<span class="' + l[0] + '">' + l[1] + '</span>').join('\n');
      return;
    }
    let li = 0, ch = 0, out = '';
    const cur = '<span class="cursor"></span>';
    const t = setInterval(() => {
      const cls = LINES[li][0], txt = LINES[li][1];
      ch += 2;
      const partial = txt.slice(0, ch);
      boot.innerHTML = out + '<span class="' + cls + '">' + partial + '</span>' + cur;
      if (ch >= txt.length) {
        out += '<span class="' + cls + '">' + txt + '</span>\n'; li++; ch = 0;
        if (li >= LINES.length) { clearInterval(t); boot.innerHTML = out + cur; }
      }
    }, 24);
  };

  /* contatore attacchi security */
  MODS.counter = (el) => {
    const atk = el.querySelector('.ox-atk');
    if (!atk) { return; }
    const to = parseInt(atk.getAttribute('data-to') || '47', 10);
    let started = false;
    const ioC = new IntersectionObserver((es) => es.forEach((e) => {
      if (e.isIntersecting && !started) {
        started = true;
        let n = 0;
        const t = setInterval(() => { n += Math.random() < 0.3 ? 2 : 1; atk.textContent = n; if (n >= to) { clearInterval(t); } }, 90);
        ioC.unobserve(atk);
      }
    }), { threshold: 0.4 });
    ioC.observe(atk);
  };

  /* scanline security */
  MODS.scan = (el) => {
    function onScroll() {
      const t = Math.min(Math.max(scrollY / (document.documentElement.scrollHeight - innerHeight), 0), 1);
      el.style.top = (t * 100) + 'vh';
    }
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
  };

  /* panorama 360 tour (bg fisso + bussola + gradi + oblò hero) */
  MODS.pano = (el) => {
    // Come nel sito sorgente (body transparent): le tile lasciano vedere il panorama.
    document.querySelectorAll('.oloxp').forEach((x) => x.classList.add('oloxp-clear'));
    const seq = 'N · · · NE · · · E · · · SE · · · S · · · SO · · · O · · · NO · · · ';
    const cbar = el.querySelector('.cin');
    if (cbar) { cbar.textContent = seq.repeat(10); }
    const strip = el.querySelector('.strip');
    const deg = el.querySelector('.ox-deg');
    const panoHero = document.querySelector('.oloxp .ox-porthole .pano');
    function onScroll() {
      const t = Math.min(Math.max(scrollY / (document.documentElement.scrollHeight - innerHeight), 0), 1);
      if (deg) { deg.textContent = Math.round(t * 360) + '°'; }
      if (strip) { strip.style.transform = 'translateX(' + (-t * 64) + '%)'; }
      if (panoHero) { panoHero.style.transform = 'translateX(' + (-t * 62) + '%)'; }
      if (cbar) { cbar.style.transform = 'translateX(' + (-t * 1400) + 'px)'; }
    }
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
  };

  /* XP tutor: barra fissa + toast + livello + sblocco lezioni + bonus quiz */
  MODS.xp = (el) => {
    const xpbar = el.querySelector('.bar i');
    const xpval = el.querySelector('.ox-xpval');
    const toast = document.querySelector('.oloxp .lvltoast');
    const lvlEl = document.querySelector('.oloxp .medal .inner b');
    const total = parseInt(el.getAttribute('data-total') || '540', 10);
    const cap = parseInt(el.getAttribute('data-cap') || '630', 10);
    const step = parseInt(el.getAttribute('data-step') || '180', 10);
    let bonus = 0, lastLvl = 1, toastT;
    function levelFor(xp) { return 1 + Math.floor(xp / step); }
    function showToast(msg) {
      if (!toast) { return; }
      toast.textContent = msg; toast.classList.add('pop');
      clearTimeout(toastT); toastT = setTimeout(() => toast.classList.remove('pop'), 1800);
    }
    function onScroll() {
      const t = Math.min(Math.max(scrollY / (document.documentElement.scrollHeight - innerHeight), 0), 1);
      const xp = Math.round(t * total) + bonus;
      if (xpbar) { xpbar.style.width = (Math.min(xp / cap, 1) * 100) + '%'; }
      if (xpval) { xpval.textContent = xp; }
      const lvl = levelFor(xp);
      if (lvl !== lastLvl) {
        if (lvl > lastLvl) { showToast('★ Level up! → ' + lvl); }
        lastLvl = lvl;
        if (lvlEl) { lvlEl.textContent = lvl; }
      }
      document.querySelectorAll('.oloxp .lez').forEach((l) => {
        const r = l.getBoundingClientRect();
        if (r.top < innerHeight * 0.72) { l.classList.add('unlocked'); }
      });
    }
    document.addEventListener('olox:xp', (e) => {
      bonus += (e.detail && e.detail.bonus) ? e.detail.bonus : 0;
      if (e.detail && e.detail.toast) { showToast(e.detail.toast); }
      onScroll();
    });
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
  };

  /* quiz tutor */
  MODS.quiz = (el) => {
    const verdict = el.querySelector('.verdict');
    const okMsg = el.getAttribute('data-ok') || 'esatto · <b>+90 xp</b> · badge sbloccato';
    const koMsg = el.getAttribute('data-ko') || 'mmh, riprova: la risposta è nel nome della suite…';
    const bonus = parseInt(el.getAttribute('data-bonus') || '90', 10);
    let answered = false;
    function confetti(from) {
      const r = from.getBoundingClientRect();
      const cols = ['#38C172', '#F5A623', '#3D8BFF', '#E8409A', '#FAF7F2'];
      for (let i = 0; i < 26; i++) {
        const c = document.createElement('i'); c.className = 'olox-confetti';
        c.style.background = cols[i % cols.length];
        c.style.left = (r.left + r.width / 2) + 'px'; c.style.top = r.top + 'px';
        document.body.appendChild(c);
        const ang = Math.random() * Math.PI - Math.PI / 2, v = 120 + Math.random() * 220;
        c.animate([
          { transform: 'translate(0,0) rotate(0deg)', opacity: 1 },
          { transform: 'translate(' + (Math.cos(ang) * v) + 'px,' + (Math.sin(ang) * v - 140) + 'px) rotate(' + (Math.random() * 540 - 270) + 'deg)', opacity: 0 }
        ], { duration: 900 + Math.random() * 500, easing: 'cubic-bezier(.2,.8,.4,1)' }).onfinish = () => c.remove();
      }
    }
    el.querySelectorAll('.ans button').forEach((b) => {
      b.addEventListener('click', () => {
        if (answered) { return; }
        if (b.getAttribute('data-ok') === '1') {
          answered = true; b.classList.add('right');
          if (verdict) { verdict.innerHTML = okMsg; }
          document.dispatchEvent(new CustomEvent('olox:xp', { detail: { bonus: bonus, toast: '★ Quiz superato +' + bonus + ' XP' } }));
          confetti(b);
        } else {
          b.classList.add('wrong');
          if (verdict) { verdict.textContent = koMsg; }
        }
      });
    });
  };

  /* TOC manuale: scrollspy */
  MODS.toc = (el) => {
    const links = [...el.querySelectorAll('.toc a')];
    const targets = links.map((a) => {
      const href = a.getAttribute('href') || '';
      return href.startsWith('#') ? document.getElementById(href.slice(1)) : null;
    });
    function onScroll() {
      let best = 0;
      targets.forEach((t, i) => { if (t && t.getBoundingClientRect().top < innerHeight * 0.4) { best = i; } });
      links.forEach((a, i) => a.classList.toggle('on', i === best));
    }
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
  };

  /* lang switcher (dropdown mobile) */
  MODS.langsw = (el) => {
    const t = el.querySelector('.lsw-t');
    if (!t) { return; }
    t.addEventListener('click', (e) => { e.stopPropagation(); el.classList.toggle('open'); });
    document.addEventListener('click', () => el.classList.remove('open'));
  };

  /* ========================================================================
     HOME EXPERIENCE (rail + 6 minigiochi + madlib)
     ======================================================================== */
  MODS.home = (root) => {
    const track = root.querySelector('.ox-track');
    const rail = root.querySelector('.ox-rail');
    if (!track || !rail) { return; }
    const panels = [...root.querySelectorAll('.panel')];
    const pbar = root.querySelector('.progress i');
    const hint = root.querySelector('.hint');
    const halo = root.querySelector('.ox-halo');
    const C = cfg(root, {});
    root.style.setProperty('--panels', panels.length);

    const haloCols = panels.map((p) => {
      const h = getComputedStyle(p).getPropertyValue('--c').trim().replace('#', '');
      if (/^[0-9a-f]{6}$/i.test(h)) { return [parseInt(h.slice(0, 2), 16), parseInt(h.slice(2, 4), 16), parseInt(h.slice(4, 6), 16)]; }
      return [232, 69, 61];
    });

    /* ---- muro: forza-4 da cantiere (tu vs computer) ---- */
    const wall = root.querySelector('.wall');
    if (wall) {
      const COLS = 12, ROWS = 7;
      const cells = [];
      for (let i = 0; i < COLS * ROWS; i++) { const el = document.createElement('i'); wall.appendChild(el); cells.push(el); }
      const wstat = root.querySelector('.wstat');
      const START = (C.wall_start || '<b>Costruisci il sito perfetto</b> · posiziona le tile · 4 in fila vince');
      let board = new Array(COLS * ROWS).fill(0), gameDone = false, aiBusy = false;
      const at = (r, c) => board[r * COLS + c];
      function dropRow(c) { for (let r = ROWS - 1; r >= 0; r--) { if (!at(r, c)) { return r; } } return -1; }
      function place(c, p) {
        const r = dropRow(c); if (r < 0) { return false; }
        board[r * COLS + c] = p;
        const el = cells[r * COLS + c];
        el.classList.add(p === 1 ? 'p1' : 'p2');
        el.classList.remove('pop'); void el.offsetWidth; el.classList.add('pop');
        return true;
      }
      function winLine(p) {
        const dirs = [[0, 1], [1, 0], [1, 1], [1, -1]];
        for (let r = 0; r < ROWS; r++) {
          for (let c = 0; c < COLS; c++) {
            for (const dc of dirs) {
              const line = [];
              for (let k = 0; k < 4; k++) {
                const rr = r + dc[0] * k, cc = c + dc[1] * k;
                if (rr < 0 || rr >= ROWS || cc < 0 || cc >= COLS || at(rr, cc) !== p) { line.length = 0; break; }
                line.push(rr * COLS + cc);
              }
              if (line.length === 4) { return line; }
            }
          }
        }
        return null;
      }
      function wouldWin(c, p) {
        const r = dropRow(c); if (r < 0) { return false; }
        board[r * COLS + c] = p; const w = !!winLine(p); board[r * COLS + c] = 0;
        return w;
      }
      function finish(line, msg) {
        gameDone = true;
        if (line) { line.forEach((i) => cells[i].classList.add('winc')); }
        wstat.innerHTML = msg;
      }
      function aiMove() {
        const open = [];
        for (let c = 0; c < COLS; c++) { if (dropRow(c) >= 0) { open.push(c); } }
        if (!open.length) { return finish(null, 'muro pieno · <b>pareggio</b>'); }
        let pick = open.find((c) => wouldWin(c, 2));
        if (pick === undefined) { pick = open.find((c) => wouldWin(c, 1)); }
        if (pick === undefined) {
          const weighted = open.flatMap((c) => new Array(Math.max(1, 6 - Math.floor(Math.abs(c - 5.5)))).fill(c));
          pick = weighted[Math.floor(Math.random() * weighted.length)];
        }
        place(pick, 2);
        const w = winLine(2);
        if (w) { return finish(w, 'ha vinto il <b>computer</b> · riprova'); }
        wstat.innerHTML = '<b>Costruisci il sito perfetto</b> · posiziona le tile';
      }
      wall.addEventListener('click', (e) => {
        if (gameDone || aiBusy) { return; }
        const i = cells.indexOf(e.target); if (i < 0) { return; }
        const c = i % COLS;
        if (!place(c, 1)) { return; }
        const w = winLine(1);
        if (w) { return finish(w, '<b>hai vinto!</b> il cantiere è tuo'); }
        if (board.every((v) => v)) { return finish(null, 'muro pieno · <b>pareggio</b>'); }
        aiBusy = true; wstat.innerHTML = 'posa il <b>computer</b>…';
        setTimeout(() => { aiMove(); aiBusy = false; }, 450);
      });
      const wreset = root.querySelector('.wreset');
      if (wreset) {
        wreset.addEventListener('click', () => {
          board.fill(0); gameDone = false; aiBusy = false;
          cells.forEach((el) => { el.className = ''; });
          wstat.innerHTML = START;
        });
      }
    }

    /* ---- minigioco imprevisti ---- */
    const arena = root.querySelector('.arena');
    if (arena) {
      const IMPS = C.imps || ['no-show', 'overbooking', 'pagamento KO', 'doppio slot', 'cliente in ritardo', 'mail nello spam', 'caparra mancante', 'chiavi smarrite', 'tavolo conteso', 'maltempo improvviso'];
      let impI = 0, impHits = 0, impMiss = 0, gameLeft = 120, gameOn = false;
      const hitEl = root.querySelector('.ox-hit'), missEl = root.querySelector('.ox-miss');
      const gtimer = root.querySelector('.ox-gtimer');
      function showStart(msg) {
        arena.querySelectorAll('.imp, .final').forEach((x) => x.remove());
        const f = document.createElement('div');
        f.className = 'final';
        f.innerHTML = (msg || '') + '<button class="cta impstart" type="button" style="--c:var(--booking); margin-top:6px;">Cattura gli imprevisti</button>';
        f.querySelector('.impstart').addEventListener('click', startGame);
        arena.appendChild(f);
      }
      function startGame() {
        arena.querySelectorAll('.imp, .final').forEach((x) => x.remove());
        impHits = 0; impMiss = 0; hitEl.textContent = '0'; missEl.textContent = '0';
        gameLeft = 120; gtimer.textContent = gfmt(gameLeft);
        gameOn = true;
      }
      function endGame() {
        gameOn = false;
        showStart('<span>tempo scaduto</span><b>' + impHits + ' gestiti · ' + impMiss + ' sfuggiti</b><span>nel mondo reale li gestisce il motore</span>');
      }
      showStart('<span>turno di prova · 2 minuti</span>');
      setInterval(() => {
        if (!gameOn || !visible(arena)) { return; }
        gameLeft--;
        if (gameLeft <= 0) { gameLeft = 0; gtimer.textContent = gfmt(0); return endGame(); }
        gtimer.textContent = gfmt(gameLeft);
      }, 1000);
      setInterval(() => {
        if (!gameOn || !visible(arena)) { return; }
        if (arena.querySelectorAll('.imp:not(.hit):not(.gone)').length >= 3) { return; }
        const el = document.createElement('span');
        el.className = 'imp';
        el.textContent = IMPS[impI++ % IMPS.length];
        el.style.left = (4 + Math.random() * 52) + '%';
        el.style.top = (8 + Math.random() * 72) + '%';
        el.addEventListener('click', () => {
          if (el.classList.contains('hit')) { return; }
          el.classList.add('hit'); el.textContent = 'gestito ✓';
          impHits++; hitEl.textContent = impHits;
          setTimeout(() => el.remove(), 1100);
        });
        arena.appendChild(el);
        setTimeout(() => {
          if (!el.isConnected || el.classList.contains('hit')) { return; }
          el.classList.add('gone'); impMiss++; missEl.textContent = impMiss;
          setTimeout(() => el.remove(), 260);
        }, 650);
      }, 900);
    }

    /* ---- quiz: che lingua è? · 1 minuto · +2/-1 ---- */
    const helloEl = root.querySelector('.ox-hello');
    const picksEl = root.querySelector('.langpicks');
    if (helloEl && picksEl) {
      const QLANGS = C.qlangs || {
        'Tedesco': ['Willkommen', 'Danke', 'Schmetterling', 'Gemütlichkeit', 'Bahnhof', 'Zeitgeist', 'Wanderlust', 'Kindergarten'],
        'Francese': ['Bienvenue', 'Merci', 'Papillon', 'Croissant', 'Bonheur', 'Étoile', 'Fromage', 'Rendez-vous'],
        'Spagnolo': ['Bienvenido', 'Gracias', 'Mariposa', 'Corazón', 'Sobremesa', 'Estrella', 'Queso', 'Siesta'],
        'Portoghese': ['Bem-vindo', 'Obrigado', 'Saudade', 'Borboleta', 'Praia', 'Estrela', 'Queijo', 'Coração'],
        'Olandese': ['Welkom', 'Dankjewel', 'Vlinder', 'Gezellig', 'Fiets', 'Gracht', 'Kaas', 'Sterren'],
        'Svedese': ['Välkommen', 'Tack', 'Fjäril', 'Lagom', 'Smörgåsbord', 'Stjärna', 'Fika', 'Midsommar'],
        'Greco': ['Καλώς ήρθες', 'Ευχαριστώ', 'Πεταλούδα', 'Θάλασσα', 'Φιλοξενία', 'Αστέρι', 'Ψωμί', 'Καρδιά'],
        'Russo': ['Добро пожаловать', 'Спасибо', 'Бабочка', 'Сердце', 'Звезда', 'Хлеб', 'Море', 'Друг'],
        'Giapponese': ['ようこそ', 'ありがとう', '蝶', '心', '星', '海', '友達', '侗寂'],
        'Cinese': ['欢迎', '谢谢', '蝴蝶', '星星', '大海', '朋友', '面包', '缘分'],
        'Turco': ['Hoş geldin', 'Teşekkürler', 'Kelebek', 'Yıldız', 'Deniz', 'Ekmek', 'Arkadaş', 'Kalp'],
        'Polacco': ['Witamy', 'Dziękuję', 'Motyl', 'Serce', 'Gwiazda', 'Chleb', 'Przyjaciel', 'Morze'],
        'Ungherese': ['Üdvözöljük', 'Köszönöm', 'Pillangó', 'Szív', 'Csillag', 'Kenyér', 'Barát', 'Tenger'],
        'Finlandese': ['Tervetuloa', 'Kiitos', 'Perhonen', 'Sydän', 'Tähti', 'Leipä', 'Ystävä', 'Sisu'],
        'Danese': ['Velkommen', 'Tak', 'Sommerfugl', 'Hygge', 'Stjerne', 'Brød', 'Ven', 'Hav'],
        'Croato': ['Dobrodošli', 'Hvala', 'Leptir', 'Srce', 'Zvijezda', 'Kruh', 'Prijatelj', 'More'],
        'Rumeno': ['Bun venit', 'Mulțumesc', 'Fluture', 'Inimă', 'Stea', 'Pâine', 'Prieten', 'Mare'],
        'Coreano': ['환영합니다', '감사합니다', '나비', '마음', '별', '바다', '친구', '정'],
        'Arabo': ['مرحبا', 'شكرا', 'فراشة', 'قلب', 'نجمة', 'بحر', 'صديق', 'خبز']
      };
      const QUIZ = [];
      const QNAMES = Object.keys(QLANGS);
      QNAMES.forEach((lang) => QLANGS[lang].forEach((w) => QUIZ.push([w, lang])));
      const scoreEl = root.querySelector('.ox-lscore');
      const ltimeEl = root.querySelector('.ox-ltime');
      let quizCur = -1, qScore = 0, quizLock = false, qLeft = 60, qTick = null, qOver = false;
      function qHud() { scoreEl.textContent = qScore; ltimeEl.textContent = gfmt(qLeft); }
      function qEnd() {
        clearInterval(qTick); qTick = null; qOver = true;
        helloEl.textContent = 'tempo scaduto · ' + qScore + ' punti';
        picksEl.innerHTML = '';
        const b = document.createElement('button');
        b.type = 'button'; b.textContent = '↺ rigioca';
        b.addEventListener('click', () => { qScore = 0; qLeft = 60; qOver = false; qHud(); newRound(); });
        picksEl.appendChild(b);
      }
      function qStartTimer() {
        if (qTick || qOver) { return; }
        qTick = setInterval(() => {
          qLeft--;
          if (qLeft <= 0) { qLeft = 0; qHud(); return qEnd(); }
          qHud();
        }, 1000);
      }
      function newRound() {
        quizLock = false;
        let i; do { i = Math.floor(Math.random() * QUIZ.length); } while (i === quizCur);
        quizCur = i;
        helloEl.textContent = QUIZ[i][0];
        helloEl.classList.remove('flash'); void helloEl.offsetWidth; helloEl.classList.add('flash');
        const opts = new Set([QUIZ[i][1]]);
        while (opts.size < 3) { opts.add(QNAMES[Math.floor(Math.random() * QNAMES.length)]); }
        const arr = [...opts].sort(() => Math.random() - 0.5);
        picksEl.innerHTML = '';
        arr.forEach((lang) => {
          const b = document.createElement('button');
          b.type = 'button'; b.textContent = lang;
          b.addEventListener('click', () => {
            if (quizLock || qOver) { return; }
            qStartTimer();
            quizLock = true;
            if (lang === QUIZ[quizCur][1]) { b.classList.add('ok'); qScore += 2; }
            else { b.classList.add('ko'); qScore -= 1; }
            qHud();
            setTimeout(newRound, 550);
          });
          picksEl.appendChild(b);
        });
      }
      qHud();
      newRound();
    }

    /* ---- radar: difendi il perimetro (2 minuti) ---- */
    const radarEl = root.querySelector('.radar');
    if (radarEl) {
      const seclogEl = root.querySelector('.seclog');
      const radhud = root.querySelector('.radhud');
      function rlog(html) {
        if (!seclogEl) { return; }
        const line = document.createElement('div');
        line.style.cssText = 'opacity:1; transform:none;';
        line.innerHTML = html;
        seclogEl.appendChild(line);
        while (seclogEl.children.length > 9) { seclogEl.removeChild(seclogEl.firstChild); }
      }
      let rAtks = [], rOn = false, rLeft = 120, rKills = 0, rBreach = 0, rSpawnAcc = 0, rTimeAcc = 0, rPrevT = 0;
      function rHud() { radhud.innerHTML = gfmt(rLeft) + ' · intercettati <b>' + rKills + '</b> · violazioni <b>' + rBreach + '</b>'; }
      function rClear() { rAtks.forEach((a) => a.el.remove()); rAtks = []; }
      function rOverlay(msg) {
        radarEl.querySelectorAll('.rovl').forEach((x) => x.remove());
        const o = document.createElement('div');
        o.className = 'rovl';
        o.innerHTML = msg + '<button class="cta" type="button" style="--c:var(--secur); margin-top:8px;">Difendi il perimetro</button>';
        o.querySelector('button').addEventListener('click', (e) => { e.stopPropagation(); rStart(); });
        radarEl.appendChild(o);
      }
      function rStart() {
        radarEl.querySelectorAll('.rovl').forEach((x) => x.remove());
        rClear(); rKills = 0; rBreach = 0; rLeft = 120; rSpawnAcc = 0; rTimeAcc = 0; rOn = true; rHud();
      }
      function rEnd() {
        rOn = false; rClear();
        rlog('<span class="cy">[sentinel]</span> turno finito · ' + rKills + ' giù, ' + rBreach + ' violazioni');
        rOverlay('<span>tempo scaduto</span><b>' + rKills + ' intercettati · ' + rBreach + ' violazioni</b>');
      }
      rOverlay('<span>attacchi in arrivo dal perimetro<br>clicca per sparare dal centro</span>');
      function rSpawn() {
        const el = document.createElement('span');
        el.className = 'atk';
        const a = { el: el, ang: Math.random() * Math.PI * 2, r: 48 };
        radarEl.appendChild(el);
        rAtks.push(a); rPlace(a);
      }
      function rPlace(a) {
        a.el.style.left = (50 + Math.cos(a.ang) * a.r) + '%';
        a.el.style.top = (50 + Math.sin(a.ang) * a.r) + '%';
      }
      function rFrame(t) {
        requestAnimationFrame(rFrame);
        const dt = Math.min((t - rPrevT) / 1000, 0.1); rPrevT = t;
        if (!rOn || !visible(radarEl)) { return; }
        rTimeAcc += dt;
        if (rTimeAcc >= 1) {
          rTimeAcc -= 1; rLeft--;
          if (rLeft <= 0) { rLeft = 0; rHud(); return rEnd(); }
          rHud();
        }
        rSpawnAcc += dt;
        const every = Math.max(0.5, 1.15 - (120 - rLeft) * 0.004);
        if (rSpawnAcc >= every) { rSpawnAcc = 0; rSpawn(); }
        const speed = 6.5 + (120 - rLeft) * 0.05;
        for (const a of [...rAtks]) {
          a.r -= speed * dt; rPlace(a);
          if (a.r <= 9) {
            rAtks.splice(rAtks.indexOf(a), 1); a.el.remove();
            rBreach++; rHud();
            radarEl.classList.remove('breach'); void radarEl.offsetWidth; radarEl.classList.add('breach');
            rlog('<span class="cy">[core]</span> perimetro violato <span class="bad">ALLARME</span>');
          }
        }
      }
      requestAnimationFrame(rFrame);
      radarEl.addEventListener('click', (e) => {
        if (!rOn) { return; }
        const rc = radarEl.getBoundingClientRect();
        const dx = e.clientX - (rc.left + rc.width / 2);
        const dy = e.clientY - (rc.top + rc.height / 2);
        const ang = Math.atan2(dy, dx);
        const beam = document.createElement('span');
        beam.className = 'beam';
        beam.style.transform = 'rotate(' + (ang * 180 / Math.PI) + 'deg)';
        radarEl.appendChild(beam);
        setTimeout(() => beam.remove(), 240);
        const hitset = rAtks.filter((a) => {
          let d = Math.abs(a.ang - ang) % (Math.PI * 2);
          if (d > Math.PI) { d = Math.PI * 2 - d; }
          return d < 0.24;
        }).sort((a, b) => a.r - b.r);
        if (hitset.length) {
          const a = hitset[0];
          rAtks.splice(rAtks.indexOf(a), 1);
          a.el.classList.add('boom');
          setTimeout(() => a.el.remove(), 320);
          rKills++; rHud();
          rlog('<span class="cy">[waf]</span> bersaglio a ' + Math.round(a.r * 2) + 'm <span class="ok">GIÙ</span>');
        }
      });
    }

    /* ---- oblò: drag per ruotare + indovinelli + lucchetto ---- */
    const port = root.querySelector('.porthole');
    if (port) {
      let dragOn = false, dragX = 0, dragBase = 0;
      port.addEventListener('pointerdown', (e) => {
        if (e.target.closest('.spot, .tg-card, .tg-win')) { return; }
        dragOn = true; dragX = e.clientX; port.classList.add('grabbing'); port.setPointerCapture(e.pointerId);
      });
      port.addEventListener('pointermove', (e) => {
        if (!dragOn) { return; }
        dragBase = Math.max(-260, Math.min(260, dragBase + (e.clientX - dragX) * 0.7));
        dragX = e.clientX;
        port.style.setProperty('--drag', dragBase.toFixed(1) + 'px');
      });
      ['pointerup', 'pointercancel'].forEach((ev) => port.addEventListener(ev, () => { dragOn = false; port.classList.remove('grabbing'); }));

      const TG_RIDDLES = C.riddles || [
        { r: 'Tre caravelle e un equivoco grandioso: cercava le Indie, trovò un mondo nuovo. In che anno?', a: '1492' },
        { r: 'Un muro cadde a pezzi e le due metà di una città tornarono a guardarsi. In che anno?', a: '1989' },
        { r: 'Un piccolo passo nella polvere grigia, un balzo enorme per chi guardava da quaggiù. In che anno?', a: '1969' },
        { r: 'Una prigione presa d’assalto a Parigi, e nulla in Europa fu più come prima. In che anno?', a: '1789' },
        { r: 'Lo stivale smise di essere un mosaico e si cucì in un solo regno. In che anno?', a: '1861' },
        { r: 'La chiamavano inaffondabile, ma nella notte incontrò una montagna di ghiaccio. In che anno?', a: '1912' },
        { r: 'Con una matita in mano, gli italiani scelsero di non avere più un re. In che anno?', a: '1946' },
        { r: 'In una piana fangosa del Belgio, l’imperatore giocò la sua ultima carta. In che anno?', a: '1815' },
        { r: 'Dodici secondi d’aria sopra una spiaggia ventosa: l’uomo imparò a volare. In che anno?', a: '1903' },
        { r: 'Un orafo di Magonza insegnò ai libri a moltiplicarsi da soli. In che anno (la sua Bibbia)?', a: '1455' },
        { r: 'Un bip metallico dall’orbita fece alzare il naso a tutto il pianeta. In che anno?', a: '1957' },
        { r: 'Un fisico di Ginevra regalò al mondo una ragnatela che oggi copre la Terra. In che anno?', a: '1991' },
        { r: 'Tredici colonie firmarono il loro addio al re d’Inghilterra. In che anno?', a: '1776' },
        { r: 'Una signora di ferro si alzò su Parigi fra mille critiche, e non se n’è più andata. In che anno?', a: '1889' },
        { r: 'Le vecchie lire lasciarono il posto nelle tasche a una moneta con ponti e finestre. In che anno?', a: '2002' },
        { r: 'Due scienziati trovarono la scala a chiocciola su cui è scritta la vita. In che anno?', a: '1953' }
      ];
      const TG_SPOTS = [
        { s1: { top: '38%', left: '30%' }, s2: { top: '58%', left: '66%' } },
        { s1: { top: '30%', left: '62%' }, s2: { top: '64%', left: '28%' } },
        { s1: { top: '52%', left: '22%' }, s2: { top: '32%', left: '72%' } }
      ];
      let TG = [];
      function tgShuffle() {
        TG = [...TG_RIDDLES].sort(() => Math.random() - 0.5).slice(0, 3)
          .map((q, i) => Object.assign({}, q, TG_SPOTS[i]));
      }
      tgShuffle();
      const spotA = port.querySelector('.ox-spot-a');
      const spotB = port.querySelector('.ox-spot-b');
      let tgRound = 0, tgDigits = [1, 9, 0, 0], tgCard = null;
      function tgCloseCard() { if (tgCard) { tgCard.remove(); tgCard = null; } }
      function tgOpen(html) {
        tgCloseCard();
        tgCard = document.createElement('div');
        tgCard.className = 'tg-card';
        tgCard.innerHTML = '<button class="tg-close" type="button">✕</button>' + html;
        tgCard.addEventListener('pointerdown', (e) => e.stopPropagation());
        tgCard.querySelector('.tg-close').addEventListener('click', tgCloseCard);
        port.appendChild(tgCard);
        return tgCard;
      }
      function tgPlaceSpots() {
        const R = TG[tgRound];
        spotA.style.top = R.s1.top; spotA.style.left = R.s1.left;
        spotB.style.top = R.s2.top; spotB.style.left = R.s2.left;
      }
      if (spotA && spotB) {
        spotA.addEventListener('pointerdown', (e) => e.stopPropagation());
        spotB.addEventListener('pointerdown', (e) => e.stopPropagation());
        spotA.addEventListener('click', (e) => {
          e.stopPropagation();
          tgOpen('<span class="tg-k">tappa ' + (tgRound + 1) + ' di 3 · indovinello</span><div class="tg-r">' + TG[tgRound].r + '</div><span class="tg-ok">la risposta apre il lucchetto sull’altro punto</span>');
        });
        spotB.addEventListener('click', (e) => {
          e.stopPropagation();
          const c = tgOpen('<span class="tg-k">tappa ' + (tgRound + 1) + ' di 3 · lucchetto a combinazione</span><div class="tg-digits"></div><button class="cta" type="button" style="--c:var(--tour);">Apri</button><span class="tg-ok">inserisci l’anno dell’indovinello</span>');
          const dg = c.querySelector('.tg-digits');
          tgDigits = [1, 9, 0, 0];
          tgDigits.forEach((v, i) => {
            const w = document.createElement('div'); w.className = 'tg-dig';
            w.innerHTML = '<button type="button">▲</button><div class="dv">' + v + '</div><button type="button">▼</button>';
            const btns = w.querySelectorAll('button');
            const dv = w.querySelector('.dv');
            btns[0].addEventListener('click', () => { tgDigits[i] = (tgDigits[i] + 1) % 10; dv.textContent = tgDigits[i]; });
            btns[1].addEventListener('click', () => { tgDigits[i] = (tgDigits[i] + 9) % 10; dv.textContent = tgDigits[i]; });
            dg.appendChild(w);
          });
          c.querySelector('.cta').addEventListener('click', () => {
            if (tgDigits.join('') === TG[tgRound].a) {
              tgRound++;
              tgCloseCard();
              if (tgRound >= TG.length) {
                const w = document.createElement('div');
                w.className = 'tg-win';
                w.innerHTML = '<b>Sbloccato tutto · hai vinto</b><span>ogni hot-spot può aprire quello che vuoi tu</span><button class="cta" type="button" style="--c:var(--tour); margin-top:6px;">↺ rigioca</button>';
                w.addEventListener('pointerdown', (e2) => e2.stopPropagation());
                w.querySelector('.cta').addEventListener('click', () => { w.remove(); tgRound = 0; tgShuffle(); tgPlaceSpots(); });
                port.appendChild(w);
              } else {
                tgPlaceSpots();
              }
            } else {
              c.classList.remove('wrong'); void c.offsetWidth; c.classList.add('wrong');
            }
          });
        });
        tgPlaceSpots();
      }
    }

    /* ---- tutor: quiz a trascinamento ---- */
    let tutorBonus = 0;
    const tqChips = root.querySelector('.tq-chips');
    if (tqChips) {
      const PRODOTTI = ['OLObuild', 'OLObooking', 'OLOlang', 'OLOsecurity', 'OLOtour', 'OLOtutor'];
      const TQ = C.tq || [
        { q: 'Per tradurre il sito in 28 lingue uso', a: 'OLOlang' },
        { q: 'I 187 tile del page builder vivono in', a: 'OLObuild' },
        { q: 'Tavoli, camere e appuntamenti si prenotano con', a: 'OLObooking' },
        { q: 'Il firewall 100% locale col pannello Sentinel è', a: 'OLOsecurity' },
        { q: 'I tour virtuali a 360° arriveranno con', a: 'OLOtour' },
        { q: 'Corsi, quiz e certificati sono il mestiere di', a: 'OLOtutor' },
        { q: 'La caparra anti no-show è una funzione di', a: 'OLObooking' },
        { q: 'OLObuild Pro include a vita', a: 'OLOlang' },
        { q: 'Il 2FA con codici di recupero lo trovi in', a: 'OLOsecurity' },
        { q: 'Il tile Viewer 360° è l’antipasto di', a: 'OLOtour' },
        { q: 'Il builder di OLObuild è scritto in', a: 'Vue 3 + Pinia', opts: ['React + Redux', 'jQuery'] },
        { q: 'Le revisioni di OLObuild vivono in', a: 'tabelle DB dedicate', opts: ['postmeta', 'file .json'] },
        { q: 'Lo scanner legge ~11.000 file PHP in', a: '2–3 secondi', opts: ['2–3 minuti', 'mezz’ora'] },
        { q: 'OLOsecurity al WP Plugin Check totalizza', a: '0 errori / 0 warning', opts: ['3 warning', '12 errori'] },
        { q: 'Il requisito PHP minimo della suite è', a: 'PHP 7.4+', opts: ['PHP 5.6', 'solo PHP 8.3'] },
        { q: 'Le regole del mini-WAF seguono le famiglie', a: 'OWASP', opts: ['ISO 9001', 'RFC 2616'] },
        { q: 'OLOlang traduce contenuti e stringhe passando dal', a: 'database', opts: ['frontend al volo', 'file .po'] },
        { q: 'La SEO multilingua usa URL localizzati e', a: 'hreflang', opts: ['redirect 302', 'cookie'] },
        { q: 'I motori di OLOlang sono il traduttore IA e', a: 'DeepL', opts: ['Google Translate', 'un dizionario'] },
        { q: 'Il drag & drop del builder pesa circa', a: '5 kb', opts: ['80 kb', '1 MB'] },
        { q: 'I verticali di OLObooking sono', a: '6', opts: ['2', '12'] },
        { q: 'Il 2FA di OLOsecurity usa codici', a: 'TOTP', opts: ['solo SMS', 'via fax'] }
      ];
      const TQ_PER_GAME = 6;
      const tqQ = root.querySelector('.tq-q');
      const tqStat = root.querySelector('.tq-stat');
      const tqLvl = root.querySelector('.ox-tqlvl');
      let tqOrder = [...TQ].sort(() => Math.random() - 0.5).slice(0, TQ_PER_GAME), tqI = -1, tqDone = 0;
      function tqEnd() {
        tqQ.innerHTML = 'Corso completato · <span class="tq-slot okk">' + tqDone + ' su ' + TQ_PER_GAME + '</span>';
        tqChips.innerHTML = '';
        const c = document.createElement('span');
        c.className = 'tq-chip'; c.textContent = '↺ ricomincia'; c.style.cursor = 'pointer';
        c.addEventListener('click', () => {
          tqOrder = [...TQ].sort(() => Math.random() - 0.5).slice(0, TQ_PER_GAME);
          tqI = -1; tqDone = 0;
          if (tqLvl) { tqLvl.textContent = '01'; }
          tqStat.innerHTML = 'trascina la risposta giusta nello spazio · +60 xp';
          tqNext();
        });
        tqChips.appendChild(c);
        tqStat.innerHTML = 'altre domande ti aspettano · ricomincia quando vuoi';
      }
      function tqNext() {
        tqI++;
        if (tqI >= tqOrder.length) { return tqEnd(); }
        const cur = tqOrder[tqI];
        tqQ.innerHTML = cur.q + ' <span class="tq-slot">trascina qui</span>';
        let arr;
        if (cur.opts) { arr = [cur.a].concat(cur.opts); }
        else {
          const s = new Set([cur.a]);
          while (s.size < 3) { s.add(PRODOTTI[Math.floor(Math.random() * PRODOTTI.length)]); }
          arr = [...s];
        }
        tqChips.innerHTML = '';
        arr.sort(() => Math.random() - 0.5).forEach((name) => {
          const c = document.createElement('span');
          c.className = 'tq-chip'; c.innerHTML = name;
          tqChips.appendChild(c);
        });
        tqStat.innerHTML = 'domanda <b>' + (tqI + 1) + '</b> / ' + TQ_PER_GAME + ' · giuste: <b>' + tqDone + '</b>';
      }
      tqNext();
      let tqDrag = null;
      tqChips.addEventListener('pointerdown', (e) => {
        const chip = e.target.closest('.tq-chip');
        if (!chip) { return; }
        tqDrag = chip;
        chip.setPointerCapture(e.pointerId);
        chip.classList.add('drag');
        chip.style.left = e.clientX + 'px'; chip.style.top = e.clientY + 'px';
      });
      tqChips.addEventListener('pointermove', (e) => {
        if (!tqDrag) { return; }
        tqDrag.style.left = e.clientX + 'px'; tqDrag.style.top = e.clientY + 'px';
        const slot = root.querySelector('.tq-q .tq-slot');
        if (!slot) { return; }
        const r = slot.getBoundingClientRect();
        slot.classList.toggle('over', e.clientX > r.left && e.clientX < r.right && e.clientY > r.top && e.clientY < r.bottom);
      });
      tqChips.addEventListener('pointerup', (e) => {
        if (!tqDrag) { return; }
        const chip = tqDrag; tqDrag = null;
        chip.classList.remove('drag'); chip.style.left = ''; chip.style.top = '';
        const slot = root.querySelector('.tq-q .tq-slot');
        if (!slot) { return; }
        const r = slot.getBoundingClientRect();
        const inSlot = e.clientX > r.left && e.clientX < r.right && e.clientY > r.top && e.clientY < r.bottom;
        slot.classList.remove('over');
        if (!inSlot) { return; }
        if (chip.textContent === tqOrder[tqI].a) {
          slot.textContent = chip.textContent; slot.classList.add('okk');
          chip.remove();
          tqDone++; tutorBonus += 60; xpShown = -2;
          if (tqLvl) { tqLvl.textContent = String(1 + tqDone).padStart(2, '0'); }
          tqStat.innerHTML = 'esatto · <b>+60 xp</b> · giuste: <b>' + tqDone + '</b>';
          setTimeout(tqNext, 900);
        } else {
          slot.classList.remove('kko'); void slot.offsetWidth; slot.classList.add('kko');
          tqStat.innerHTML = 'mmh, non è quello · riprova';
        }
      });
    }

    /* ---- transform orizzontale + progressi ---- */
    let trackW = 0, railH = 0, vh = 0, vw = 0;
    function measure() {
      vw = innerWidth; vh = innerHeight;
      trackW = track.scrollWidth; railH = rail.offsetHeight;
    }
    measure(); addEventListener('resize', measure);

    let xpShown = -1;
    const xpEl = root.querySelector('.ox-xp');
    function frame() {
      requestAnimationFrame(frame);
      const maxScroll = railH - vh;
      const t = maxScroll > 0 ? Math.min(Math.max(scrollY / maxScroll, 0), 1) : 0;
      if (!isMobile()) {
        track.style.transform = 'translateX(' + (-t * (trackW - vw)).toFixed(1) + 'px)';
      }
      if (pbar) { pbar.style.width = (t * 100).toFixed(2) + '%'; }
      if (hint) { hint.classList.toggle('gone', t > 0.06); }

      let hr = 0, hg = 0, hb = 0, hw = 0;
      panels.forEach((p, pi) => {
        const r = p.getBoundingClientRect();
        let pp;
        if (isMobile()) { pp = Math.min(Math.max((vh - r.top) / (vh * 0.9), 0), 1); }
        else { pp = Math.min(Math.max((vw - r.left) / r.width, 0), 1); }
        p.style.setProperty('--pp', pp.toFixed(4));
        const dist = isMobile()
          ? Math.abs((r.top + r.height / 2) - vh / 2) / Math.max(vh, 1)
          : Math.abs((r.left + r.width / 2) - vw / 2) / Math.max(vw, 1);
        const w = Math.max(0, 1 - dist);
        const hc = haloCols[pi];
        hr += hc[0] * w; hg += hc[1] * w; hb += hc[2] * w; hw += w;
        const sc = p.querySelector('.scene');
        if (sc) { sc.classList.toggle('go', pp > 0.2); }
        const fx = sc ? sc.getAttribute('data-fx') : '';
        if (fx === 'course' && xpEl) {
          const n = Math.round(Math.min(pp * 1.6, 1) * 390) + tutorBonus;
          if (n !== xpShown) { xpShown = n; xpEl.textContent = n; }
        }
      });
      if (hw > 0 && halo) {
        halo.style.setProperty('--halo', 'rgba(' + Math.round(hr / hw) + ',' + Math.round(hg / hw) + ',' + Math.round(hb / hw) + ',.5)');
      }
    }
    requestAnimationFrame(frame);

    /* ---- "olonica": spiegazione ---- */
    const opb = root.querySelector('.opb');
    const olw = root.querySelector('.olw');
    if (opb && olw) {
      olw.addEventListener('click', () => opb.classList.add('open'));
      const opclose = opb.querySelector('.opclose');
      if (opclose) { opclose.addEventListener('click', () => opb.classList.remove('open')); }
      opb.addEventListener('click', (e) => { if (e.target === opb) { opb.classList.remove('open'); } });
    }

    /* ---- pallini di salto ---- */
    const jump = root.querySelector('.jump');
    let dots = [];
    function goTo(i) {
      if (isMobile()) { window.scrollTo({ top: panels[i].offsetTop + rail.getBoundingClientRect().top + scrollY, behavior: 'smooth' }); return; }
      const maxScroll = railH - vh;
      const x = panels[i].offsetLeft;
      const target = (x / (trackW - vw)) * maxScroll;
      window.scrollTo({ top: Math.min(target, maxScroll), behavior: 'smooth' });
    }
    if (jump) {
      dots = panels.map((p, i) => {
        const b = document.createElement('button');
        b.style.setProperty('--jc', getComputedStyle(p).getPropertyValue('--c') || '#fff');
        b.title = p.getAttribute('data-screen-label') || ('Sezione ' + i);
        b.innerHTML = '<span></span>';
        b.addEventListener('click', () => goTo(i));
        jump.appendChild(b); return b;
      });
      setInterval(() => {
        let best = 0, bd = 1e9;
        panels.forEach((p, i) => {
          const r = p.getBoundingClientRect();
          const d = Math.abs(r.left + r.width / 2 - vw / 2) + (isMobile() ? Math.abs(r.top) : 0);
          if (d < bd) { bd = d; best = i; }
        });
        dots.forEach((d, i) => d.classList.toggle('on', i === best));
      }, 300);
    }
    root.querySelectorAll('[data-go]').forEach((a) => {
      a.addEventListener('click', (e) => { e.preventDefault(); goTo(+a.getAttribute('data-go')); });
    });
    /* deep-link a una fermata: #go-N (usato anche per QA/screenshot) */
    const goMatch = /^#go-(\d+)$/.exec(location.hash || '');
    if (goMatch) {
      const gi = Math.min(panels.length - 1, Math.max(0, +goMatch[1]));
      setTimeout(() => {
        if (isMobile()) { window.scrollTo({ top: panels[gi].getBoundingClientRect().top + scrollY, behavior: 'instant' }); return; }
        const maxScroll = railH - vh;
        const x = panels[gi].offsetLeft;
        window.scrollTo({ top: Math.min((x / (trackW - vw)) * maxScroll, maxScroll), behavior: 'instant' });
      }, 300);
    }
    const logoTop = root.querySelector('.chrome .logo');
    if (logoTop) { logoTop.addEventListener('click', (e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); }); }

    /* ---- form folle: mad-lib ---- */
    const stampBtn = root.querySelector('.ox-stamp');
    if (stampBtn) {
      let sogno = '';
      root.querySelectorAll('.pick button').forEach((b) => {
        b.addEventListener('click', () => {
          root.querySelectorAll('.pick button').forEach((x) => x.classList.remove('sel'));
          b.classList.add('sel'); sogno = b.getAttribute('data-v');
        });
      });
      stampBtn.addEventListener('click', () => {
        const card = root.querySelector('.madcard');
        const nome = (root.querySelector('.ox-f-nome') || {}).value || '';
        const mail = (root.querySelector('.ox-f-mail') || {}).value || '';
        const note = root.querySelector('.madnote');
        const mailto = root.querySelector('.madcard').getAttribute('data-mailto') || 'info@olotheme.com';
        if (!nome.trim() || !mail.trim() || mail.indexOf('@') < 0 || !sogno) {
          card.classList.remove('shake'); void card.offsetWidth; card.classList.add('shake');
          note.textContent = 'compila nome, sogno e una mail vera, poi timbra';
          return;
        }
        card.classList.add('stamped');
        note.textContent = 'timbrato · apro la mail…';
        const body = encodeURIComponent('Ciao, mi chiamo ' + nome.trim() + ' e il mio sito sogna di diventare ' + sogno + '.\nScrivetemi a ' + mail.trim() + ', promesso, niente catene.');
        setTimeout(() => { location.href = 'mailto:' + mailto + '?subject=' + encodeURIComponent('Il mio sito sogna · ' + nome.trim()) + '&body=' + body; }, 750);
        setTimeout(() => { card.classList.remove('stamped'); note.textContent = 'il timbro apre la tua mail già compilata'; }, 5200);
      });
    }

    /* ---- posizione persistente ---- */
    try {
      const saved = localStorage.getItem('olo-exp-scroll');
      if (saved && !goMatch) { requestAnimationFrame(() => scrollTo({ top: +saved, behavior: 'instant' })); }
      let st;
      addEventListener('scroll', () => {
        clearTimeout(st);
        st = setTimeout(() => localStorage.setItem('olo-exp-scroll', String(scrollY)), 200);
      });
    } catch (e) { /* storage non disponibile */ }
  };

  /* ======================================================================== */
  ready(() => {
    document.querySelectorAll('[data-olox]').forEach((el) => {
      const names = (el.getAttribute('data-olox') || '').split(/\s+/);
      names.forEach((n) => {
        if (!MODS[n]) { return; }
        const flag = '__olox_' + n;
        if (el[flag]) { return; }
        el[flag] = true;
        MODS[n](el);
      });
    });
  });
})();
