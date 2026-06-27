// OLObuild Try — living Goo/Aurora background (mirror of the real `goo` tile)
// SVG metaball filter (feGaussianBlur + feColorMatrix) fuses blurred color blobs;
// one blob chases the cursor with easing. Aurora mode = soft drifting halos, no fuse.
// Honors prefers-reduced-motion (static) and coarse pointers (no cursor blob).

function GooBackground({
  mode = "goo",
  palette = ["#e1474f", "#7c6cff", "#ff8a3d"],
  base = "#0b0d12",
  count = 5,
  sizeMin = 220,
  sizeMax = 420,
  drift = 0.5,
  gooStrength = 16,
  followCursor = true,
  auroraBlur = 64,
  opacity = 0.9,
  className = "",
  style,
}) {
  const uid = React.useMemo(() => "goo" + Math.random().toString(36).slice(2, 8), []);
  const hostRef = React.useRef(null);
  const layerRef = React.useRef(null);
  const blobsRef = React.useRef([]);

  React.useEffect(() => {
    const host = hostRef.current;
    const layer = layerRef.current;
    if (!host || !layer) return;
    const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const coarse = window.matchMedia("(hover: none), (pointer: coarse)").matches;
    const useCursor = followCursor && !coarse && mode === "goo";

    let w = host.clientWidth, h = host.clientHeight;
    const els = blobsRef.current;
    const seeds = els.map((_, i) => ({
      baseX: (Math.random() * 0.8 + 0.1) * w,
      baseY: (Math.random() * 0.8 + 0.1) * h,
      ampX: (Math.random() * 0.22 + 0.08) * w,
      ampY: (Math.random() * 0.22 + 0.08) * h,
      spd: (Math.random() * 0.5 + 0.5) * drift,
      ph: Math.random() * Math.PI * 2,
    }));
    let cur = { x: w / 2, y: h / 2, tx: w / 2, ty: h / 2 };

    function onResize() { w = host.clientWidth; h = host.clientHeight; }
    function onMove(e) {
      const r = host.getBoundingClientRect();
      cur.tx = e.clientX - r.left; cur.ty = e.clientY - r.top;
    }
    window.addEventListener("resize", onResize);
    if (useCursor) host.addEventListener("pointermove", onMove);

    let raf, t = 0, running = true;
    const io = new IntersectionObserver(([en]) => {
      running = en.isIntersecting;
      if (running && !reduce) tick();
    }, { threshold: 0 });
    io.observe(host);

    function place(i) {
      const s = seeds[i], el = els[i];
      if (!el) return;
      const x = s.baseX + Math.sin(t * s.spd + s.ph) * s.ampX;
      const y = s.baseY + Math.cos(t * s.spd * 0.9 + s.ph) * s.ampY;
      el.style.transform = `translate(-50%,-50%) translate(${x}px,${y}px)`;
    }
    function tick() {
      if (!running) return;
      t += 0.01;
      for (let i = 0; i < seeds.length; i++) place(i);
      if (useCursor) {
        cur.x += (cur.tx - cur.x) * 0.12;
        cur.y += (cur.ty - cur.y) * 0.12;
        const cel = els[els.length - 1];
        if (cel) cel.style.transform = `translate(-50%,-50%) translate(${cur.x}px,${cur.y}px)`;
      }
      raf = requestAnimationFrame(tick);
    }
    // initial static placement always
    for (let i = 0; i < seeds.length; i++) place(i);
    if (!reduce) tick();

    return () => {
      cancelAnimationFrame(raf); io.disconnect();
      window.removeEventListener("resize", onResize);
      if (useCursor) host.removeEventListener("pointermove", onMove);
    };
  }, [mode, drift, followCursor]);

  const total = count + (followCursor && mode === "goo" ? 1 : 0);
  const blobs = Array.from({ length: total }, (_, i) => {
    const isCursor = followCursor && mode === "goo" && i === total - 1;
    const size = isCursor ? sizeMax * 0.8 : sizeMin + ((sizeMax - sizeMin) * ((i % count) / Math.max(1, count - 1)));
    const color = palette[i % palette.length];
    return (
      <span
        key={i}
        ref={(el) => (blobsRef.current[i] = el)}
        className="goo-blob"
        style={{
          width: size, height: size,
          background: `radial-gradient(circle at 35% 35%, ${color}, ${color} 42%, transparent 70%)`,
        }}
      />
    );
  });

  return (
    <div ref={hostRef} className={`goo-host ${className}`} style={{ background: base, ...style }} aria-hidden="true">
      {mode === "goo" && (
        <svg width="0" height="0" style={{ position: "absolute" }}>
          <defs>
            <filter id={uid}>
              <feGaussianBlur in="SourceGraphic" stdDeviation="14" result="b" />
              <feColorMatrix in="b" mode="matrix"
                values={`1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 ${gooStrength} -7`} result="g" />
              <feBlend in="SourceGraphic" in2="g" />
            </filter>
          </defs>
        </svg>
      )}
      <div
        ref={layerRef}
        className="goo-layer"
        style={{
          opacity,
          filter: mode === "goo" ? `url(#${uid})` : `blur(${auroraBlur}px)`,
          mixBlendMode: mode === "aurora" ? "screen" : "normal",
        }}
      >
        {blobs}
      </div>
      <div className="goo-grain" />
    </div>
  );
}

window.GooBackground = GooBackground;
