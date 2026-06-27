// OLObuild Try — wow-effects showcase. Each card live-demos a real OLObuild capability.

// ── 3D tilt hook (mirrors mouse_tilt) ───────────────────────────────
function useTilt(max = 14) {
  const ref = React.useRef(null);
  React.useEffect(() => {
    const el = ref.current; if (!el) return;
    if (window.matchMedia("(hover: none),(pointer: coarse)").matches) return;
    let raf;
    function move(e) {
      const r = el.getBoundingClientRect();
      const px = (e.clientX - r.left) / r.width - 0.5;
      const py = (e.clientY - r.top) / r.height - 0.5;
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(() => {
        el.style.transform = `perspective(800px) rotateY(${px * max}deg) rotateX(${-py * max}deg) translateZ(6px)`;
      });
    }
    function leave() { cancelAnimationFrame(raf); el.style.transform = ""; }
    el.addEventListener("pointermove", move);
    el.addEventListener("pointerleave", leave);
    return () => { el.removeEventListener("pointermove", move); el.removeEventListener("pointerleave", leave); cancelAnimationFrame(raf); };
  }, [max]);
  return ref;
}

// ── Scramble text (mirrors text_effect: scramble) ───────────────────
function Scramble({ words = ["ORA", "DAL VIVO", "GRATIS", "SUBITO"], className }) {
  const [txt, setTxt] = React.useState(words[0]);
  React.useEffect(() => {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    const glyphs = "ABCDEFGHIJKLMNOPQRSTUVWXYZ#%@&▚▞░▒";
    let wi = 0, raf, to;
    function scrambleTo(next) {
      const from = txt; const len = Math.max(from.length, next.length);
      let frame = 0; const total = 22;
      cancelAnimationFrame(raf);
      (function run() {
        let out = "";
        for (let i = 0; i < len; i++) {
          const p = frame / total - i / len;
          if (p > 0.6) out += next[i] || "";
          else if (p > 0) out += glyphs[(Math.random() * glyphs.length) | 0];
          else out += from[i] || "";
        }
        setTxt(out);
        frame++;
        if (frame <= total) raf = requestAnimationFrame(run);
        else { setTxt(next); to = setTimeout(() => { wi = (wi + 1) % words.length; scrambleTo(words[wi]); }, 1600); }
      })();
    }
    to = setTimeout(() => { wi = (wi + 1) % words.length; scrambleTo(words[wi]); }, 1600);
    return () => { cancelAnimationFrame(raf); clearTimeout(to); };
  }, []);
  return <span className={className}>{txt}</span>;
}

// ── Typewriter loop (mirrors text_effect: typewriter-loop) ──────────
function Typeloop({ phrases = ["headline", "gallery", "form", "hero", "accordion"], className }) {
  const [s, setS] = React.useState("");
  React.useEffect(() => {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) { setS(phrases[0]); return; }
    let pi = 0, ci = 0, del = false, to;
    (function step() {
      const word = phrases[pi];
      ci += del ? -1 : 1;
      setS(word.slice(0, ci));
      let d = del ? 45 : 95;
      if (!del && ci === word.length) { d = 1300; del = true; }
      else if (del && ci === 0) { del = false; pi = (pi + 1) % phrases.length; d = 250; }
      to = setTimeout(step, d);
    })();
    return () => clearTimeout(to);
  }, []);
  return <span className={className}>{s}<span className="tw-caret">▋</span></span>;
}

// ── The showcase grid ───────────────────────────────────────────────
function WowShowcase() {
  const tiltA = useTilt(16);
  const tiltB = useTilt(10);
  return (
    <section id="effetti" className="tr-section">
      <div className="sec-head">
        <span className="sec-eyebrow"><Ico n="sparkle" s={14}/> Novità · effetti wow</span>
        <h2>Gli effetti che <em>non</em> ti aspetti da un page builder.</h2>
        <p>Niente plugin, niente codice. Sono controlli nell'inspector: scegli, regoli gli slider, salvi. Ogni card qui sotto è un effetto vero che puoi attivare nella sandbox.</p>
      </div>

      <div className="wow-grid">
        {/* Goo / metaball */}
        <article className="wow-card span-2 wow-goo">
          <div className="wow-goo-bg">
            <GooBackground mode="goo" count={4} sizeMin={120} sizeMax={240} opacity={0.95}
              palette={["#e1474f","#7c6cff","#ff8a3d"]} base="#0e1017"/>
          </div>
          <div className="wow-body">
            <span className="wow-tag">Sfondo Goo</span>
            <h3>Metaball che inseguono il mouse</h3>
            <p>Gocce di colore che si fondono e seguono il cursore. Muovi il puntatore qui sopra.</p>
          </div>
        </article>

        {/* Liquid glass */}
        <article className="wow-card wow-liquid">
          <div className="wow-liquid-orbs"><span/><span/></div>
          <div className="wow-glass">
            <span className="wow-tag light">Liquid glass</span>
            <h3>Vetro liquido</h3>
            <p>Backdrop-blur + saturazione, bordo luce.</p>
          </div>
        </article>

        {/* Neon cyber */}
        <article className="wow-card wow-neon">
          <div className="wow-body">
            <span className="wow-tag neon">Neon · bordo pulsante</span>
            <h3 className="neon-title">NEON CYBER</h3>
            <p>Bordo <code>neon-pulse</code> + glow sul titolo.</p>
          </div>
        </article>

        {/* Glitch */}
        <article className="wow-card wow-glitch">
          <div className="wow-body">
            <span className="wow-tag">Text FX · Glitch RGB</span>
            <h3 className="glitch" data-txt="ERRORE?">ERRORE?</h3>
            <p>Scostamento canali rosso/ciano animato.</p>
          </div>
        </article>

        {/* Retro terminal */}
        <article className="wow-card wow-term">
          <div className="wow-scan"/>
          <div className="wow-body">
            <span className="wow-tag term">Retro terminal</span>
            <pre className="term-pre">{`> olobuild --demo
caricamento tile…`}<span className="term-cur">█</span></pre>
            <p className="term-note">Scanlines CRT + prompt + cursore.</p>
          </div>
        </article>

        {/* 3D tilt */}
        <article ref={tiltA} className="wow-card wow-tilt">
          <div className="wow-body">
            <span className="wow-tag">Mouse · tilt 3D</span>
            <h3>Inclina al passaggio</h3>
            <p>Passa il mouse: la card segue in prospettiva.</p>
          </div>
        </article>

        {/* Sticker */}
        <article ref={tiltB} className="wow-card wow-sticker">
          <div className="wow-body">
            <span className="wow-tag dark">Sticker</span>
            <h3>Adesivo</h3>
            <p>Rotazione + bordo bianco + ombra.</p>
          </div>
        </article>

        {/* Scramble + typewriter combo */}
        <article className="wow-card span-2 wow-fx">
          <div className="wow-body">
            <span className="wow-tag">Text FX dal vivo</span>
            <h3 className="fx-line">Prova OLObuild <Scramble className="fx-scramble" /></h3>
            <p className="fx-sub">Trascini il tile <Typeloop className="fx-type" /> sul canvas.</p>
          </div>
        </article>
      </div>
    </section>
  );
}

window.WowShowcase = WowShowcase;
window.GooUseTilt = useTilt;
window.Scramble = Scramble;
