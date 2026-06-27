// OLObuild — "Try" sandbox landing — restyle composition

function TrNav() {
  return (
    <nav className="tr-nav">
      <div className="tr-nav-inner">
        <img src="assets/olobuild-horizontal.png" className="logo-img" alt="OLObuild"/>
        <span className="demo-tag"><span className="live-dot"/> Sandbox dal vivo</span>
        <span className="spc"/>
        <a className="nav-link hide-sm" href="#come-funziona">Come funziona</a>
        <a className="nav-link hide-sm" href="#prova">Cosa puoi provare</a>
        <a className="nav-link hide-sm" href="https://olotheme.com/prodotti/olobuild/">Installa</a>
        <button className="mk-btn mk-btn-primary">Apri il builder <Ico n="arrow" s={16}/></button>
      </div>
    </nav>
  );
}

function TrHero() {
  return (
    <header className="tr-hero">
      <div className="mk-glow-red-strong tr-glow-1"/>
      <div className="mk-glow-red tr-glow-2"/>
      <div className="mk-grain"/>

      <div className="tr-hero-inner">
        <div>
          <span className="mk-chip"><span className="dot"/> Sandbox demo · 12h gratis · nessun account</span>
          <h1>Prova OLObuild <em>ora</em>.<br/>Niente registrazione,<br/>niente carta.</h1>
          <p className="lead">
            Hai un <b>canvas vuoto</b> e una copia personale del builder. Trascina i tile,
            costruisci, salva e vedi il risultato dal vivo — tutto nel tuo browser.
          </p>
          <div className="ctas">
            <button className="mk-btn mk-btn-primary mk-btn-big">Apri il builder · inizia ora <Ico n="arrow" s={17}/></button>
            <a className="mk-btn mk-btn-ghost-light mk-btn-big" href="#come-funziona">Cosa puoi fare?</a>
          </div>
          <div className="tr-trust">
            <span><Ico n="check" s={15}/> Niente account</span>
            <span><Ico n="check" s={15}/> Niente email</span>
            <span><Ico n="check" s={15}/> Reset dopo 12h</span>
            <span><Ico n="check" s={15}/> Tutto sul tuo browser</span>
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
    { ic:"grid",    t:"Costruisci da zero",  p:"Parti da un canvas vuoto. Trascini 28 tile demo dalla sidebar — headline, immagini, gallery, accordion, button, hero, form." },
    { ic:"edit",    t:"Modifica al volo",    p:"Doppio click su un testo e lo editi inline con la toolbar floating. Niente switch tra edit mode e preview.", kbd:["dbl click"] },
    { ic:"sliders", t:"Personalizza tutto",  p:"Inspector laterale: colori, padding, margini, animazioni, hover, responsive. Anteprima fedele al frontend." },
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
            {s.kbd && (
              <span className="kbd">{s.kbd.map(k => <kbd key={k}>{k}</kbd>)}</span>
            )}
          </article>
        ))}
      </div>
    </section>
  );
}

function TrCaps() {
  const caps = [
    "28 tile selezionati (su 90 free)",
    "Drag & drop sulla griglia",
    "Inline editing con doppio click",
    "Inspector laterale completo",
    "Anteprima responsive",
    "Animazioni d'ingresso e hover",
    "Effetti testo (typewriter, glitch…)",
    "Reset al template originale",
  ];
  return (
    <section id="prova" className="tr-section">
      <div className="tr-caps">
        <div>
          <div className="sec-head" style={{marginBottom:28}}>
            <span className="sec-eyebrow"><Ico n="sparkle" s={14}/> Nell'assaggio demo</span>
            <h2>Tutto quello che puoi <em>provare.</em></h2>
          </div>
          <div className="tr-caps-list">
            {caps.map(c => (
              <div key={c} className="tr-cap">
                <span className="check"><Ico n="check" s={14} sw={2.2}/></span>
                {c}
              </div>
            ))}
            <div className="tr-caps-note">
              <Ico n="refresh" s={18}/>
              Sbagliato qualcosa? Un click su <b style={{color:"#fff",fontWeight:600,margin:"0 4px"}}>Reset</b> e torni al template originale.
            </div>
          </div>
        </div>

        <div className="tr-caps-visual">
          <div className="mk-mock">
            <BuilderMockup variant="rail-zoom" width={560}/>
          </div>
        </div>
      </div>
    </section>
  );
}

function TrLimits() {
  const limits = [
    { ic:"clock", pill:"12h", t:"Inattività", p:"Dopo 12 ore senza modifiche il tuo template viene cancellato. Torna prima e riparti da dove avevi lasciato." },
    { ic:"user",  pill:"Privata", t:"Sandbox personale", p:"Ognuno ha il suo template. Quello che modifichi tu non lo vede nessun altro. Niente registrazione." },
    { ic:"grid",  pill:"28 / 90", t:"Tile della demo", p:"Selezione rappresentativa. Nella versione completa hai 90 tile free più 22 Pro." },
    { ic:"file",  pill:"Demo", t:"Solo template demo", p:"Non puoi creare pagine nuove o cambiare header/footer. Modifichi il template che ti diamo." },
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
    { v:"90", c:"tile free" },
    { v:"22", c:"tile Pro" },
    { v:"11", c:"effetti testo" },
    { v:"36", c:"animazioni d'ingresso" },
  ];
  return (
    <section className="tr-cta">
      <div className="mk-grain"/>
      <div className="tr-cta-inner">
        <span className="sec-eyebrow"><Ico n="bolt" s={14}/> Gratis per sempre</span>
        <h2>Convinto? Installa<br/>OLObuild <em>gratis.</em></h2>
        <p className="lead">
          La sandbox ti dà un assaggio. Sul tuo WordPress hai <b>tutto</b>: 90 tile free,
          11 effetti testo, 36 animazioni d'ingresso e il form builder multi-step.
        </p>
        <div className="ctas">
          <a className="mk-btn mk-btn-primary mk-btn-big" href="https://olotheme.com/prodotti/olobuild/">Installa OLObuild <Ico n="arrow" s={17}/></a>
          <button className="mk-btn mk-btn-ghost-light mk-btn-big">Continua nella sandbox</button>
        </div>
        <div className="tr-cta-figs">
          {figs.map(f => (
            <div key={f.c} className="tr-cta-fig">
              <div className="v">{f.v}</div>
              <div className="c">{f.c}</div>
            </div>
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
        <a href="#">Come funziona</a>
      </div>
    </footer>
  );
}

function TryPage() {
  return (
    <div className="tr-shell" data-screen-label="Try sandbox">
      <TrNav/>
      <TrHero/>
      <TrSteps/>
      <TrCaps/>
      <TrLimits/>
      <TrCta/>
      <TrFooter/>
    </div>
  );
}

window.TryPage = TryPage;
