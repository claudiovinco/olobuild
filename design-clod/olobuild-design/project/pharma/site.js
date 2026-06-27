/* ═══════════════════════════════════════════════════════════════════
   Farmacie Comunali Borgoverde — shared behavior.
   Nav sticky · mobile menu · Counter · reveal-on-scroll · accordion.
   ═══════════════════════════════════════════════════════════════════ */
(function () {
  "use strict";
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ── NAV sticky shadow ─────────────────────────────────────────── */
  const nav = $(".nav");
  if (nav) {
    const onScroll = () => nav.classList.toggle("is-stuck", window.scrollY > 16);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ── Mobile menu ───────────────────────────────────────────────── */
  const burger = $(".nav__burger");
  const mm = $(".mobile-menu");
  if (burger && mm) {
    const close = $(".mobile-menu__close", mm);
    const open = () => { mm.classList.add("is-open"); document.body.style.overflow = "hidden"; };
    const hide = () => { mm.classList.remove("is-open"); document.body.style.overflow = ""; };
    burger.addEventListener("click", open);
    close && close.addEventListener("click", hide);
    $$("a", mm).forEach(a => a.addEventListener("click", hide));
  }

  /* ── Reveal on scroll ──────────────────────────────────────────── */
  const revealEls = $$("[data-reveal]");
  if (revealEls.length) {
    if (reduce) revealEls.forEach(e => e.classList.add("is-in"));
    else {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(en => {
          if (en.isIntersecting) {
            const d = parseInt(en.target.dataset.reveal || "0", 10) || 0;
            setTimeout(() => en.target.classList.add("is-in"), d);
            io.unobserve(en.target);
          }
        });
      }, { threshold: 0.14, rootMargin: "0px 0px -8% 0px" });
      revealEls.forEach(e => io.observe(e));
    }
  }

  /* ── Counter (OLObuild Counter tile) ───────────────────────────── */
  const counters = $$("[data-count]");
  if (counters.length) {
    const format = (n, dec) => {
      const v = dec ? n.toFixed(dec) : Math.round(n);
      return String(v).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    };
    const run = (el) => {
      const target = parseFloat(el.dataset.count);
      const dec = el.dataset.dec ? parseInt(el.dataset.dec, 10) : 0;
      if (reduce) { el.textContent = format(target, dec); return; }
      const dur = 1500, t0 = performance.now();
      const tick = (t) => {
        const p = Math.min((t - t0) / dur, 1);
        const e = 1 - Math.pow(1 - p, 3);
        el.textContent = format(target * e, dec);
        if (p < 1) requestAnimationFrame(tick); else el.textContent = format(target, dec);
      };
      requestAnimationFrame(tick);
    };
    const io2 = new IntersectionObserver((entries) => {
      entries.forEach(en => { if (en.isIntersecting) { run(en.target); io2.unobserve(en.target); } });
    }, { threshold: 0.5 });
    counters.forEach(c => io2.observe(c));
  }

  /* ── Accordion (OLObuild Accordion tile) ───────────────────────── */
  $$(".acc-item").forEach(item => {
    const q = $(".acc-q", item), a = $(".acc-a", item);
    if (!q || !a) return;
    if (item.classList.contains("is-open")) a.style.maxHeight = a.scrollHeight + "px";
    q.addEventListener("click", () => {
      const open = item.classList.contains("is-open");
      const acc = item.closest(".accordion");
      if (acc) $$(".acc-item.is-open", acc).forEach(o => { if (o !== item) { o.classList.remove("is-open"); $(".acc-a", o).style.maxHeight = null; } });
      item.classList.toggle("is-open", !open);
      a.style.maxHeight = open ? null : a.scrollHeight + "px";
    });
  });
})();
