/* ═══════════════════════════════════════════════════════════════════
   SALDO — interactions. Vanilla, rAF-driven.
   Sticky nav · subtle parallax · reveal-on-scroll · counters ·
   FAQ accordion · pricing billing toggle · hero doc bar fills.
   ═══════════════════════════════════════════════════════════════════ */
(function () {
  "use strict";
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;
  const lerp = (a, b, n) => a + (b - a) * n;
  const clamp = (v, a, b) => Math.min(Math.max(v, a), b);

  /* ── NAV sticky ────────────────────────────────────────────────── */
  const nav = $(".nav");
  if (nav) {
    const onS = () => nav.classList.toggle("is-stuck", window.scrollY > 24);
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

  /* ── PARALLAX: subtle scroll + pointer ─────────────────────────── */
  const pErls = $$("[data-parallax]");
  const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
  if (!reduce && pErls.length) {
    let raf = false;
    const upd = () => {
      const y = window.scrollY;
      pointer.tx = lerp(pointer.tx, pointer.x, 0.08);
      pointer.ty = lerp(pointer.ty, pointer.y, 0.08);
      pErls.forEach(el => {
        const sp = parseFloat(el.dataset.parallax) || 0.08;
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
      pointer.x = (e.clientX / innerWidth - 0.5) * 2 * 16;
      pointer.y = (e.clientY / innerHeight - 0.5) * 2 * 12;
      tick();
    });
    upd();
  }

  /* ── REVEAL ────────────────────────────────────────────────────── */
  const rev = $$("[data-reveal]");
  if (reduce) rev.forEach(e => e.classList.add("is-in"));
  else {
    const io = new IntersectionObserver((ents) => {
      ents.forEach(en => {
        if (en.isIntersecting) {
          const d = parseInt(en.target.dataset.reveal || "0", 10) || 0;
          setTimeout(() => en.target.classList.add("is-in"), d);
          io.unobserve(en.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -6% 0px" });
    rev.forEach(e => io.observe(e));
  }

  /* ── HERO doc bar fills (animate when visible) ─────────────────── */
  const doc = $(".hero__doc");
  if (doc) {
    const io = new IntersectionObserver((ents) => ents.forEach(en => { if (en.isIntersecting) { doc.classList.add("is-in"); io.disconnect(); } }), { threshold: 0.3 });
    io.observe(doc);
  }

  /* ── COUNTERS ──────────────────────────────────────────────────── */
  const cs = $$("[data-count]");
  if (cs.length) {
    const fmt = (n, d) => {
      const v = d ? n.toFixed(d) : Math.round(n).toString();
      return v.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };
    const run = (el) => {
      const t = parseFloat(el.dataset.count), d = el.dataset.dec ? +el.dataset.dec : 0;
      if (reduce) { el.textContent = fmt(t, d); return; }
      const t0 = performance.now(), dur = 1700;
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

  /* ── FAQ accordion ─────────────────────────────────────────────── */
  $$(".acc").forEach(item => {
    const q = $(".acc__q", item), a = $(".acc__a", item);
    if (!q || !a) return;
    q.addEventListener("click", () => {
      const open = item.classList.contains("is-open");
      $$(".acc.is-open").forEach(o => { if (o !== item) { o.classList.remove("is-open"); $(".acc__a", o).style.maxHeight = "0px"; } });
      item.classList.toggle("is-open", !open);
      a.style.maxHeight = open ? "0px" : a.scrollHeight + "px";
    });
  });
  // open the first by default
  const first = $(".acc");
  if (first) { first.classList.add("is-open"); const a = $(".acc__a", first); if (a) a.style.maxHeight = a.scrollHeight + "px"; }
  addEventListener("resize", () => { const o = $(".acc.is-open .acc__a"); if (o) o.style.maxHeight = o.scrollHeight + "px"; });

  /* ── PRICING billing toggle ────────────────────────────────────── */
  const billing = $(".billing");
  if (billing) {
    const sw = $(".sw", billing), labs = $$(".lab", billing);
    const set = (year) => {
      billing.classList.toggle("is-year", year);
      labs[0] && labs[0].classList.toggle("is-on", !year);
      labs[1] && labs[1].classList.toggle("is-on", year);
    };
    billing.addEventListener("click", () => set(!billing.classList.contains("is-year")));
    set(false);
  }
})();
