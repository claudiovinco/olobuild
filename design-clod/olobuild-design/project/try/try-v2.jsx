// OLObuild — Try sandbox restyle v2 — page composition (wow-effects edition)

// ── compact ASCII equalizer (nod to the asciiviz tile) ─────────────
function AsciiBar({ cols = 28 }) {
  const [bars, setBars] = React.useState(() => Array(cols).fill(0));
  React.useEffect(() => {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    let raf, t = 0;
    (function tick() {
      t += 0.08;
      setBars(Array.from({ length: cols }, (_, i) =>
        (Math.sin(t + i * 0.5) * 0.5 + 0.5) * (0.6 + 0.4 * Math.sin(t * 0.7 + i))
      ));
      raf = requestAnimationFrame(tick);
    })();
    return () => cancelAnimationFrame(raf);
  }, []);
  const ramp = " ·:-=+*o%#@";
  return (
    <span className="ascii-bar" aria-hidden="true">
      {bars.map((v, i) => ramp[Math.min(ramp.length - 1, Math.floor(v * (ramp.length - 1)))]).join("")}
    </span>
  );
}

function TrNav() {
  return (
    <nav className="tr-nav">
      <div className="tr-nav-inner">
        <img src="assets/olobuild-horizontal.png" className="logo-img" alt="OLObuild"/>
        <span className="demo-tag"><span className="live-dot"/> Sandbox dal vivo</span>
        <span className="spc"/>
        <a className="nav-link hide-sm" href="#effetti">Effetti wow</a>
        <a className="nav-link hide-sm" href="#come-funziona">Come funziona</a>
        <a className="nav-link hide-sm" href="https://olotheme.com/prodotti/olobuild/">Installa</a>
        <button className="mk-btn mk-btn-primary">Apri il builder <Ico n="arrow" s={16}/></button>
      </div>
    </nav>
  );
}

function TrHero() {
  return (
    <header className="tr-hero v2">
      <div className="tr-hero-goo">
        <GooBackground mode="goo" count={5} sizeMin={260} sizeMax={520} drift={0.55}
          palette={["#e1474f","#7c6cff","#ff8a3d"]} base="#0b0d12" opacity={0.92}/>
      </div>
      <div className="tr-hero-inner">
        <div>
          <span className="mk-chip"><span className="dot"/> Sandbox demo · 12h gratis · nessun account</span>
          <h1>Prova OLObuild <Scramble className="hero-scramble"/>.<br/>Costruisci come<br/>nel 2026.</h1>
          <p className="lead">
            Un <b>canvas vuoto</b> e una copia personale del builder. Trascina i tile, attiva
            <b> effetti wow</b> con uno slider, salva e guarda il risultato dal vivo — tutto nel browser.
          </p>
          <div className="ctas">
            <button className="mk-btn mk-btn-primary mk-btn-big">Apri il builder · inizia ora <Ico n="arrow" s={17}/></button>
            <a className="mk-btn mk-btn-ghost-light mk-btn-big" href="#effetti">Vedi gli effetti</a>
          </div>
          <div className="tr-trust">
            <span><Ico n="check" s={15}/> Niente account</span>
            <span><Ico n="check" s={15}/> Niente email</span>
            <span><Ico n="check" s={15}/> Reset dopo 12h</span>
          </div>
        </div>

        <div className="tr-hero-visual">
          <div className="tr-countdown"><Ico n="clock" s={15}/> Reset tra <b>11:58:42</b></div>
          <div className="mk-mock lift-shadow">
            <BuilderMockup variant="full" width={1000}/>
          </div>
          <div className="tr-drag-chip">
            <div className="ic"><Ico n="type" s={16}/></div>
            <div className="lbl">Titolo</div>
            <div className="cur"><Ico n="pointer" s={22}/></div>
          </div>
        </div>
      </div>
    </header>
  );
}

function TrSteps() {
  const steps = [
    { ic:"grid",    t:"Costruisci da zero",  p:"Parti da un canvas vuoto. Trascini i tile demo dalla sidebar — headline, immagini, gallery, accordion, form, hero." },
    { ic:"edit",    t:"Modifica al volo",    p:"Doppio click su un testo e lo editi inline con la toolbar floating. Niente switch tra edit e preview.", kbd:["dbl click"] },
    { ic:"sliders", t:"Attiva gli effetti",  p:"Inspector: colori, spaziature, animazioni, Text FX, effetti wow, mouse-tilt. Tutto con slider e toggle." },
    { ic:"save",    t:"Salva e visualizza",  p:"Salva e vedi il risultato sul frontend. Modifica e ripeti, senza limiti durante la sessione.", kbd:["Ctrl","S"] },
  ];
  return (
    <section id="come-funziona" className="tr-section">
      <div className="sec-head">
        <span className="sec-eyebrow"><Ico n="clock" s={14}/> In 12 ore</span>
        <h2>Quattro mosse, <em>zero attriti.</em></h2>
        <p>Nessun setup, nessun tutorial obbligatorio. Apri il builder e sei già dentro la tua sandbox personale.</p>
      </div>
      <div className="tr-steps">
        {steps.map((s, i) => (
          <article key={s.t} className="tr-step">
            <span className="num">{String(i+1).padStart(2,"0")}</span>
            <div className="ic-frame"><Ico n={s.ic} s={22}/></div>
            <h3>{s.t}</h3>
            <p>{s.p}</p>
            {s.kbd && <span className="kbd">{s.kbd.map(k => <kbd key={k}>{k}</kbd>)}</span>}
          </article>
        ))}
      </div>
    </section>
  );
}

function TrLimits() {
  const limits = [
    { ic:"clock", pill:"12h", t:"Inattività", p:"Dopo 12 ore senza modifiche il template viene cancellato. Torna prima e riparti da dove eri." },
    { ic:"user",  pill:"Privata", t:"Sandbox personale", p:"Ognuno ha il suo template. Quello che modifichi non lo vede nessun altro. Niente registrazione." },
    { ic:"grid",  pill:"28 / 135", t:"Tile della demo", p:"Selezione rappresentativa. Nella versione completa hai 135 tile: oltre 100 gratis, alcune speciali per gli abbonati." },
    { ic:"file",  pill:"Demo", t:"Solo template demo", p:"Non crei pagine nuove o header/footer: modifichi il template che ti diamo." },
  ];
  return (
    <section className="tr-section tr-limits">
      <div className="sec-head">
        <span className="sec-eyebrow"><Ico n="lock" s={14}/> In trasparenza</span>
        <h2>I limiti della <em>sandbox.</em></h2>
        <p>È un assaggio onesto, non la versione completa. Ecco esattamente cosa aspettarti.</p>
      </div>
      <div className="tr-limits-grid">
        {limits.map(l => (
          <article key={l.t} className="tr-limit">
            <div className="ic-frame"><Ico n={l.ic} s={20}/></div>
            <span className="pill">{l.pill}</span>
            <h3>{l.t}</h3>
            <p>{l.p}</p>
          </article>
        ))}
      </div>
    </section>
  );
}

function TrCta() {
  const figs = [
    { v:"135", c:"tile totali" }, { v:"100+", c:"gratis" },
    { v:"35", c:"animazioni" }, { v:"10", c:"Text FX" },
  ];
  return (
    <section className="tr-cta v2">
      <div className="tr-cta-glow"/>
      <div className="mk-grain"/>
      <div className="tr-cta-inner">
        <div className="ascii-strip"><AsciiBar cols={40}/></div>
        <span className="sec-eyebrow"><Ico n="bolt" s={14}/> Gratis per sempre</span>
        <h2>Convinto? Installa<br/>OLObuild <em>gratis.</em></h2>
        <p className="lead">
          La sandbox è un assaggio. Sul tuo WordPress hai <b>tutto</b>: oltre 100 tile gratis
          (135 in tutto, alcune speciali per gli abbonati), gli effetti wow, i Text FX, gli
          sfondi Goo/Aurora animati e il form builder multi-step.
        </p>
        <div className="ctas">
          <a className="mk-btn mk-btn-primary mk-btn-big" href="https://olotheme.com/prodotti/olobuild/">Installa OLObuild <Ico n="arrow" s={17}/></a>
          <button className="mk-btn mk-btn-ghost-light mk-btn-big">Continua nella sandbox</button>
        </div>
        <div className="tr-cta-figs">
          {figs.map(f => (
            <div key={f.c} className="tr-cta-fig"><div className="v">{f.v}</div><div className="c">{f.c}</div></div>
          ))}
        </div>
      </div>
    </section>
  );
}

function TrFooter() {
  return (
    <footer className="tr-footer">
      <div>© 2026 OLObuild · La sandbox è il tuo browser · Reset automatico dopo 12h</div>
      <div className="links">
        <a href="https://olotheme.com/prodotti/olobuild/">olotheme.com</a>
        <a href="#">Privacy</a>
        <a href="#effetti">Effetti</a>
      </div>
    </footer>
  );
}

function TryPageV2() {
  return (
    <div className="tr-shell" data-screen-label="Try sandbox v2">
      <TrNav/>
      <TrHero/>
      <WowShowcase/>
      <TrSteps/>
      <TrLimits/>
      <TrCta/>
      <TrFooter/>
    </div>
  );
}

window.TryPageV2 = TryPageV2;
