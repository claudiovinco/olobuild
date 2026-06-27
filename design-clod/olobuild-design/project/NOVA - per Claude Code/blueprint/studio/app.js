/* ═══════════════════════════════════════════════════════════════════
   NOVA — interactions. Vanilla, rAF-driven.
   Parallax (scroll + pointer) · text scramble · 3D mouse-tilt ·
   before/after compare · reveal-on-scroll · counters.
   ═══════════════════════════════════════════════════════════════════ */
(function () {
  "use strict";
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;
  const lerp = (a, b, n) => a + (b - a) * n;
  const clamp = (v, a, b) => Math.min(Math.max(v, a), b);

  /* ── NAV ───────────────────────────────────────────────────────── */
  const nav = $(".nav");
  if (nav) {
    const onS = () => nav.classList.toggle("is-stuck", window.scrollY > 30);
    onS(); addEventListener("scroll", onS, { passive: true });
  }
  const burger = $(".nav__burger"), mm = $(".mobile-menu");
  if (burger && mm) {
    const close = $(".mobile-menu__close", mm);
    const open = () => { mm.classList.add("is-open"); document.body.style.overflow = "hidden"; };
    const hide = () => { mm.classList.remove("is-open"); document.body.style.overflow = ""; };
    burger.addEventListener("click", open);
    close && close.addEventListener("click", hide);
    $$("a", mm).forEach(a => a.addEventListener("click", hide));
  }

  /* ── PARALLAX: scroll + pointer ────────────────────────────────── */
  const pErls = $$("[data-parallax]");
  const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
  if (!reduce) {
    let raf = false;
    const upd = () => {
      const y = window.scrollY;
      pointer.tx = lerp(pointer.tx, pointer.x, 0.08);
      pointer.ty = lerp(pointer.ty, pointer.y, 0.08);
      pErls.forEach(el => {
        const sp = parseFloat(el.dataset.parallax) || 0.1;
        const mo = parseFloat(el.dataset.mouse) || 0;
        const ty = -y * sp;
        const mx = pointer.tx * mo, my = pointer.ty * mo;
        el.style.transform = `translate3d(${mx.toFixed(1)}px, ${(ty + my).toFixed(1)}px, 0)`;
      });
      raf = false;
      if (Math.abs(pointer.tx - pointer.x) > 0.1 || Math.abs(pointer.ty - pointer.y) > 0.1) tick();
    };
    const tick = () => { if (!raf) { raf = true; requestAnimationFrame(upd); } };
    addEventListener("scroll", tick, { passive: true });
    addEventListener("mousemove", (e) => {
      pointer.x = (e.clientX / innerWidth - 0.5) * 2 * 30;
      pointer.y = (e.clientY / innerHeight - 0.5) * 2 * 22;
      tick();
    });
    upd();
  }

  /* ── TEXT SCRAMBLE (OLObuild Text FX) ──────────────────────────── */
  $$(".scramble").forEach(el => {
    const words = (el.dataset.words || el.textContent).split(",").map(s => s.trim()).filter(Boolean);
    if (words.length < 2 || reduce) { el.textContent = words[0] || el.textContent; return; }
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ#%&*/";
    let wi = 0;
    const setWord = (target) => {
      let frame = 0;
      const dur = 22; // frames
      const from = el.textContent.padEnd(target.length);
      const run = () => {
        let out = "";
        for (let i = 0; i < target.length; i++) {
          const start = Math.floor((i / target.length) * dur * 0.6);
          if (frame >= start + 8) out += target[i];
          else if (frame >= start) out += chars[Math.floor(Math.random() * chars.length)];
          else out += (from[i] || "").trim() ? from[i] : chars[Math.floor(Math.random() * chars.length)];
        }
        el.textContent = out;
        frame++;
        if (frame <= dur + 8) requestAnimationFrame(run); else el.textContent = target;
      };
      run();
    };
    el.textContent = words[0];
    setInterval(() => { wi = (wi + 1) % words.length; setWord(words[wi]); }, 2600);
  });

  /* ── MOUSE TILT 3D (OLObuild hover transform) ──────────────────── */
  if (!reduce && matchMedia("(pointer:fine)").matches) {
    $$("[data-tilt]").forEach(card => {
      const max = 9;
      card.addEventListener("mousemove", (e) => {
        const r = card.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width - 0.5;
        const py = (e.clientY - r.top) / r.height - 0.5;
        card.style.transform = `perspective(900px) rotateY(${(px * max).toFixed(2)}deg) rotateX(${(-py * max).toFixed(2)}deg) scale(1.02)`;
      });
      card.addEventListener("mouseleave", () => { card.style.transform = ""; });
    });
  }

  /* ── IMGCOMPARE before/after ───────────────────────────────────── */
  $$(".cmp").forEach(cmp => {
    let active = false;
    const set = (clientX) => {
      const r = cmp.getBoundingClientRect();
      const pct = clamp(((clientX - r.left) / r.width) * 100, 2, 98);
      cmp.style.setProperty("--cmp", pct + "%");
    };
    const down = (e) => { active = true; set((e.touches ? e.touches[0] : e).clientX); e.preventDefault(); };
    const move = (e) => { if (active) set((e.touches ? e.touches[0] : e).clientX); };
    const up = () => { active = false; };
    cmp.addEventListener("pointerdown", down);
    addEventListener("pointermove", move, { passive: true });
    addEventListener("pointerup", up);
    // hover-scrub on desktop
    cmp.addEventListener("mousemove", (e) => { if (!active && matchMedia("(pointer:fine)").matches) set(e.clientX); });
  });

  /* ── REVEAL ────────────────────────────────────────────────────── */
  const rev = $$("[data-reveal], [data-reveal-clip]");
  if (reduce) rev.forEach(e => e.classList.add("is-in"));
  else {
    const io = new IntersectionObserver((ents) => {
      ents.forEach(en => {
        if (en.isIntersecting) {
          const d = parseInt(en.target.dataset.reveal || en.target.dataset.revealClip || "0", 10) || 0;
          setTimeout(() => en.target.classList.add("is-in"), d);
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -6% 0px" });
    rev.forEach(e => io.observe(e));
  }

  /* ── COUNTERS ──────────────────────────────────────────────────── */
  const cs = $$("[data-count]");
  if (cs.length) {
    const fmt = (n, d) => (d ? n.toFixed(d) : Math.round(n)).toString();
    const run = (el) => {
      const t = parseFloat(el.dataset.count), d = el.dataset.dec ? +el.dataset.dec : 0;
      if (reduce) { el.textContent = fmt(t, d); return; }
      const t0 = performance.now(), dur = 1600;
      const tk = (now) => {
        const p = Math.min((now - t0) / dur, 1), e = 1 - Math.pow(1 - p, 3);
        el.textContent = fmt(t * e, d);
        if (p < 1) requestAnimationFrame(tk); else el.textContent = fmt(t, d);
      };
      requestAnimationFrame(tk);
    };
    const io2 = new IntersectionObserver((ents) => ents.forEach(en => { if (en.isIntersecting) { run(en.target); io2.unobserve(en.target); } }), { threshold: 0.6 });
    cs.forEach(c => io2.observe(c));
  }
})();
