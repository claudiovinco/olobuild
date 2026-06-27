/* ═══════════════════════════════════════════════════════════════════
   IOTfarm — shared behavior for OLObuild-composed demo site.
   Mirrors OLObuild runtime: IntersectionObserver-driven animations,
   text-effects (AnimatedHeading), Counter, parallax scroll-link, accordion.
   ═══════════════════════════════════════════════════════════════════ */
(function () {
  "use strict";
  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ── NAV: transparency-on-top + sticky shrink ──────────────────── */
  const nav = $(".nav");
  if (nav) {
    const overDark = nav.classList.contains("is-over-dark");
    const onScroll = () => {
      const stuck = window.scrollY > 24;
      nav.classList.toggle("is-stuck", stuck);
      if (overDark) nav.classList.toggle("is-over-dark", !stuck);
    };
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

  /* ── AnimatedHeading (OLObuild Text FX → word rotator) ─────────── */
  $$(".anim-words").forEach(el => {
    const words = (el.dataset.words || "").split(",").map(s => s.trim()).filter(Boolean);
    if (!words.length) return;
    el.innerHTML = "";
    const spans = words.map((w, i) => {
      const s = document.createElement("span");
      s.textContent = w;
      if (i === 0) s.classList.add("is-in");
      el.appendChild(s);
      return s;
    });
    if (reduce || spans.length < 2) { spans.forEach((s,i)=>{ if(i) s.style.display="none"; }); return; }
    let i = 0;
    setInterval(() => {
      const cur = spans[i];
      i = (i + 1) % spans.length;
      const next = spans[i];
      cur.classList.remove("is-in"); cur.classList.add("is-out");
      next.classList.remove("is-out"); next.classList.add("is-in");
      setTimeout(() => cur.classList.remove("is-out"), 600);
    }, 2400);
  });

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
      }, { threshold: 0.16, rootMargin: "0px 0px -8% 0px" });
      revealEls.forEach(e => io.observe(e));
    }
  }

  /* ── Counter (OLObuild Counter tile) ───────────────────────────── */
  const counters = $$("[data-count]");
  if (counters.length) {
    const run = (el) => {
      const target = parseFloat(el.dataset.count);
      const dec = (el.dataset.dec ? parseInt(el.dataset.dec, 10) : 0);
      const dur = 1500;
      if (reduce) { el.textContent = format(target, dec); return; }
      const t0 = performance.now();
      const tick = (t) => {
        const p = Math.min((t - t0) / dur, 1);
        const e = 1 - Math.pow(1 - p, 3);
        el.textContent = format(target * e, dec);
        if (p < 1) requestAnimationFrame(tick);
        else el.textContent = format(target, dec);
      };
      requestAnimationFrame(tick);
    };
    const format = (n, dec) => {
      const v = dec ? n.toFixed(dec) : Math.round(n);
      return String(v).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };
    const io2 = new IntersectionObserver((entries) => {
      entries.forEach(en => { if (en.isIntersecting) { run(en.target); io2.unobserve(en.target); } });
    }, { threshold: 0.5 });
    counters.forEach(c => io2.observe(c));
  }

  /* ── Parallax scroll-link (hero grid + visual + decor) ─────────── */
  const px = $$("[data-parallax]");
  if (px.length && !reduce) {
    let ticking = false;
    const upd = () => {
      const y = window.scrollY;
      px.forEach(el => {
        const sp = parseFloat(el.dataset.parallax) || 0.15;
        el.style.transform = `translate3d(0, ${(-y * sp).toFixed(1)}px, 0)`;
      });
      ticking = false;
    };
    window.addEventListener("scroll", () => { if (!ticking) { requestAnimationFrame(upd); ticking = true; } }, { passive: true });
    upd();
  }

  /* ── Accordion (OLObuild Accordion tile) ───────────────────────── */
  $$(".acc-item").forEach(item => {
    const q = $(".acc-q", item);
    const a = $(".acc-a", item);
    if (!q || !a) return;
    q.addEventListener("click", () => {
      const open = item.classList.contains("is-open");
      // close siblings within same accordion
      const acc = item.closest(".accordion");
      if (acc) $$(".acc-item.is-open", acc).forEach(o => { if (o !== item) { o.classList.remove("is-open"); $(".acc-a", o).style.maxHeight = null; } });
      item.classList.toggle("is-open", !open);
      a.style.maxHeight = open ? null : a.scrollHeight + "px";
    });
  });

  /* ── Pricing billing toggle ────────────────────────────────────── */
  const bt = $(".billing-toggle");
  if (bt) {
    bt.addEventListener("click", () => {
      const year = bt.classList.toggle("is-year");
      $$("[data-month]").forEach(el => {
        el.textContent = year ? el.dataset.year : el.dataset.month;
      });
    });
  }
})();
