// OLObuild — Landing demo page composition

function LdNav() {
  return (
    <nav className="ld-nav">
      <div className="ld-nav-inner">
        <img src="assets/olobuild-horizontal.png" className="logo-img" alt="OLObuild"/>
        <a href="#features">Funzionalità</a>
        <a href="#showcase">Showcase</a>
        <a href="#prezzi">Prezzi</a>
        <a href="#docs">Documentazione</a>
        <span className="spc"/>
        <span className="lang">IT</span>
        <a href="#" style={{color:"#fff"}}>Accedi</a>
        <button className="mk-btn mk-btn-primary">Prova gratis</button>
      </div>
    </nav>
  );
}

function LdHero() {
  return (
    <section className="ld-hero">
      <div className="mk-glow-red-strong hero-glow-1"/>
      <div className="mk-glow-red hero-glow-2"/>
      <div className="mk-grain"/>

      <div className="ld-hero-inner">
        <div>
          <div className="mk-chip" style={{marginBottom:24}}>
            <span className="dot"/>
            v3.34.6 · Nuova rail elementi
          </div>
          <h1>
            WordPress,<br/>
            <em>finalmente</em><br/>
            sotto controllo.
          </h1>
          <p className="lead">
            Il page builder italiano che ridisegna l'esperienza di costruire siti.
            <b style={{color:"#fff"}}> 97 elementi, 128 template, un'interfaccia che non ti rallenta.</b>
          </p>
          <div className="ctas">
            <button className="mk-btn mk-btn-primary mk-btn-big">Prova gratis 14 giorni →</button>
            <button className="mk-btn mk-btn-ghost-light mk-btn-big">Guarda la demo</button>
          </div>
          <div className="meta">
            <span>Compatibile WP 6.0+</span>
            <span>Senza carta di credito</span>
            <span>Supporto in italiano</span>
          </div>
        </div>
      </div>

      <div className="ld-hero-mock">
        <div className="ld-hero-mock-inner">
          <div className="mk-mock lift-shadow">
            <BuilderMockup variant="full" width={1180}/>
          </div>
        </div>
      </div>
    </section>
  );
}

function LdBento() {
  return (
    <section id="features" className="ld-section">
      <div className="sec-head">
        <span className="sec-eyebrow">Funzionalità</span>
        <h2>Ogni dettaglio, <em>al suo posto.</em></h2>
        <p>
          Niente più muri di accordion. Niente più liste infinite. Un'interfaccia
          pensata per chi WordPress lo usa ogni giorno.
        </p>
      </div>

      <div className="bento-grid">
        {/* b1: Rail elementi grande */}
        <div className="mk-bento b1" style={{padding:0,overflow:"hidden"}}>
          <div style={{position:"absolute",inset:0,padding:"36px 36px 0",zIndex:2,maxWidth:"55%"}}>
            <div className="mk-eyebrow" style={{color:"rgb(var(--mk-glow-r))",marginBottom:14}}>RAIL ELEMENTI</div>
            <h3 style={{maxWidth:"16ch"}}>97 elementi. <em className="mk-italic" style={{fontFamily:"var(--mk-display)",color:"rgb(var(--mk-glow-r))"}}>Una sola colonna</em> che funziona.</h3>
            <p style={{maxWidth:"40ch",marginTop:10}}>
              Categorie cliccabili, ricerca cross-categoria, preferiti pinnati, drag&amp;drop fluido.
            </p>
          </div>
          <div style={{position:"absolute",right:-180,bottom:-60,top:60,width:620,
              transform:"perspective(1800px) rotateY(-22deg) rotateX(10deg)",
              transformOrigin:"center center"}}>
            <div className="mk-mock">
              <BuilderMockup variant="rail-zoom" width={620}/>
            </div>
          </div>
        </div>

        {/* b2: Stat */}
        <div className="mk-bento b2" style={{justifyContent:"flex-end"}}>
          <div className="mk-stat" style={{color:"rgb(var(--mk-glow-r))"}}>2×</div>
          <div className="mk-stat-cap">più veloci<br/>nel trovare ogni elemento</div>
        </div>

        {/* b3: Right panel */}
        <div className="mk-bento b3" style={{padding:0,overflow:"hidden"}}>
          <div style={{position:"absolute",inset:0,padding:"24px",zIndex:3}}>
            <div className="mk-eyebrow" style={{color:"rgb(var(--mk-glow-r))",fontSize:10,marginBottom:8}}>IMPOSTAZIONI</div>
            <h3 style={{fontSize:22,maxWidth:"14ch",margin:0}}>Pannello con due rail.</h3>
          </div>
          <div style={{position:"absolute",right:-90,bottom:-20,width:340,
              transform:"perspective(1500px) rotateY(-14deg) rotateX(8deg)",
              transformOrigin:"center",zIndex:1}}>
            <div className="mk-mock">
              <BuilderMockup variant="right-zoom" width={340}/>
            </div>
          </div>
        </div>

        {/* b4: Templates */}
        <div className="mk-bento b4 mk-bento-cream" style={{padding:0,overflow:"hidden"}}>
          <div style={{position:"absolute",inset:0,padding:24,zIndex:3}}>
            <div className="mk-eyebrow" style={{color:"rgb(var(--mk-glow-r))",fontSize:10,marginBottom:8}}>TEMPLATE</div>
            <h3 style={{fontSize:22,maxWidth:"14ch",margin:0,color:"var(--mk-navy)"}}>128 template <em className="mk-italic" style={{color:"rgb(var(--mk-glow-r))"}}>italiani</em>.</h3>
          </div>
          <div style={{position:"absolute",right:-140,bottom:-30,width:440,transform:"perspective(1600px) rotateY(-14deg) rotateX(8deg)",transformOrigin:"center",zIndex:1}}>
            <div className="mk-mock">
              <BuilderMockup variant="templates" width={440}/>
            </div>
          </div>
        </div>

        {/* b5: Struttura */}
        <div className="mk-bento b5" style={{padding:0,overflow:"hidden"}}>
          <div style={{position:"absolute",inset:0,padding:24,zIndex:3}}>
            <div className="mk-eyebrow" style={{color:"rgb(var(--mk-glow-r))",fontSize:10,marginBottom:8}}>STRUTTURA</div>
            <h3 style={{fontSize:22,maxWidth:"15ch",margin:0}}>L'albero a colpo d'occhio.</h3>
          </div>
          <div style={{position:"absolute",right:-100,bottom:-30,width:360,transform:"perspective(1600px) rotateY(-14deg) rotateX(8deg)",transformOrigin:"center",zIndex:1}}>
            <div className="mk-mock">
              <BuilderMockup variant="struttura" width={360}/>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function LdShowcase() {
  const sites = [
    { cat:"Hotel",      t:"Resort delle Ville",   s:"hotel-resort.it",   grad:"linear-gradient(135deg,#e1474f,#7a1d23)", emo:"🌿"  },
    { cat:"Ristorante", t:"Trattoria del Borgo",  s:"trattoria-borgo.it",grad:"linear-gradient(135deg,#f59e0b,#7a3e1d)", emo:"🍝" },
    { cat:"Studio",     t:"Studio Legale Conti",  s:"studioconti.it",    grad:"linear-gradient(135deg,#1e293b,#475569)", emo:"⚖️" },
    { cat:"Yoga",       t:"Centro Mandala",       s:"mandala-yoga.it",   grad:"linear-gradient(135deg,#a855f7,#581c87)", emo:"🧘" },
    { cat:"Officina",   t:"Carrozzeria Romano",   s:"romano-auto.it",    grad:"linear-gradient(135deg,#0ea5e9,#0c4a6e)", emo:"🔧" },
    { cat:"Architettura",t:"Studio Tomasi",       s:"tomasiarch.it",     grad:"linear-gradient(135deg,#22c55e,#14532d)", emo:"🏛" },
  ];
  return (
    <section id="showcase" className="ld-section">
      <div className="sec-head">
        <span className="sec-eyebrow">Showcase</span>
        <h2>1.200 siti italiani, <em>in produzione.</em></h2>
        <p>Hotel, ristoranti, studi, e-commerce. Ogni settore, lo stesso builder.</p>
      </div>

      <div className="showcase-filters">
        <button className="on">Tutti</button>
        <button>Hotel</button>
        <button>Ristoranti</button>
        <button>Studi</button>
        <button>E-commerce</button>
        <button>Servizi</button>
      </div>

      <div className="showcase-grid">
        {sites.map(s => (
          <div key={s.t} className="showcase-card" style={{background: s.grad}}>
            <div style={{position:"absolute",inset:0,background:"linear-gradient(180deg, transparent 40%, rgba(0,0,0,.7) 100%)"}}/>
            <div style={{position:"absolute",top:"50%",left:"50%",transform:"translate(-50%,-50%)",fontSize:64,opacity:.4}}>{s.emo}</div>
            <div className="label">{s.cat}</div>
            <div className="meta">
              <div className="t">{s.t}</div>
              <div className="s">{s.s}</div>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

function LdStats() {
  const stats = [
    { v:"97",     c:"elementi nativi" },
    { v:"128",    c:"template italiani" },
    { v:"1.2k",   c:"siti pubblicati" },
    { v:"9.4/10", c:"valutazione supporto" },
  ];
  return (
    <section className="ld-section tight">
      <div className="stats">
        {stats.map(s => (
          <div key={s.c}>
            <div className="mk-stat">{s.v}</div>
            <div className="mk-stat-cap">{s.c}</div>
          </div>
        ))}
      </div>
    </section>
  );
}

function LdBeforeAfter() {
  return (
    <section className="ld-section">
      <div className="sec-head">
        <span className="sec-eyebrow">Confronto</span>
        <h2>Una colonna piena di <em>caos</em>.<br/>Oppure <em>questa</em>.</h2>
        <p>Il dolore di tutti i page builder: la sidebar elementi a perdita d'occhio. La nostra risposta.</p>
      </div>

      <div className="ba-grid">
        <div className="ba-card before">
          <div className="ba-eyebrow">Prima — Altri builder</div>
          <h3>Una lista lunga, accordion impilati.</h3>
          <ul className="ba-list">
            <li>Tile placeholder tutte uguali</li>
            <li>Scroll infinito per trovare un widget</li>
            <li>Ricerca sepolta o assente</li>
            <li>Categorie indistinguibili</li>
            <li>Drag&amp;drop pesante</li>
          </ul>
          <div style={{flex:1,marginTop:24,
            background:"repeating-linear-gradient(180deg, transparent 0 28px, rgba(255,255,255,.04) 28px 29px)",
            borderRadius:8,position:"relative",overflow:"hidden"}}>
            <div style={{position:"absolute",inset:14,display:"flex",flexDirection:"column",gap:6}}>
              {Array.from({length:8}).map((_,i)=>(
                <div key={i} style={{display:"flex",alignItems:"center",gap:8,padding:"6px 8px",background:"rgba(255,255,255,.04)",borderRadius:4}}>
                  <div style={{width:20,height:20,background:"rgba(255,255,255,.1)",borderRadius:3}}/>
                  <div style={{height:6,background:"rgba(255,255,255,.1)",borderRadius:3,width:`${50+(i*7)%40}%`}}/>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="ba-card after">
          <div className="ba-eyebrow">Dopo — OLObuild</div>
          <h3>Una rail, sempre a fuoco.</h3>
          <ul className="ba-list">
            <li>Categorie colorate, sempre visibili</li>
            <li>Anteprima inline tipizzata per ogni elemento</li>
            <li>Ricerca cross-categoria istantanea</li>
            <li>Preferiti pinnati in cima</li>
            <li>Drag fluido con drop hint sul canvas</li>
          </ul>
          <div style={{flex:1,marginTop:24,
            position:"relative",overflow:"hidden",
            transform:"perspective(1200px) rotateX(-2deg)",transformOrigin:"top"}}>
            <div className="mk-mock" style={{position:"absolute",left:0,right:0,top:0}}>
              <BuilderMockup variant="rail-zoom" width={580} scale={0.7}/>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function LdWhy() {
  const items = [
    { i:"🇮🇹", t:"Pensato in italiano", p:"Interfaccia, template e supporto — tutto in italiano. Nessuna traduzione automatica, niente compromessi linguistici." },
    { i:"⚡",  t:"WordPress nativo", p:"Non sostituisce WordPress, lo potenzia. Compatibile con tutti i temi, ACF, WooCommerce, Polylang." },
    { i:"♥",  t:"Supporto vero", p:"Team italiano, risposte in 24h. Onboarding 1-a-1 incluso. Sessione di setup gratuita." },
  ];
  return (
    <section className="ld-section">
      <div className="sec-head">
        <span className="sec-eyebrow">Perché OLObuild</span>
        <h2>Una scelta <em>chiara.</em></h2>
        <p>Tre cose che ci distinguono dagli altri.</p>
      </div>
      <div className="why-grid">
        {items.map(it => (
          <div key={it.t} className="why-card">
            <div className="icon-frame">{it.i}</div>
            <h3>{it.t}</h3>
            <p>{it.p}</p>
          </div>
        ))}
      </div>
    </section>
  );
}

function LdFinalCta() {
  return (
    <section id="prezzi" className="cta-section">
      <div className="mk-grain"/>
      <h2>
        Inizia a costruire<br/>
        <em>oggi stesso.</em>
      </h2>
      <p className="lead">
        14 giorni di prova gratuita, senza carta di credito.
        Importa un sito esistente o parti da uno dei nostri template.
      </p>
      <div style={{display:"flex",gap:12,justifyContent:"center",flexWrap:"wrap"}}>
        <button className="mk-btn mk-btn-primary mk-btn-big">Prova gratis 14 giorni →</button>
        <button className="mk-btn mk-btn-ghost-light mk-btn-big">Parla con un esperto</button>
      </div>
      <div className="price">
        Piani da <b>€9/mese</b> · Annuale <b>€89/anno</b> · Agenzia <b>€199/anno</b>
      </div>
    </section>
  );
}

function LdFooter() {
  return (
    <footer className="ld-footer">
      <div>© 2026 OLObuild · Costruito a Roma</div>
      <div style={{display:"flex",gap:16}}>
        <a style={{color:"inherit"}}>Privacy</a>
        <a style={{color:"inherit"}}>Termini</a>
        <a style={{color:"inherit"}}>Status</a>
      </div>
    </footer>
  );
}

function Landing() {
  return (
    <div className="ld-shell">
      <LdNav/>
      <LdHero/>
      <LdBento/>
      <LdShowcase/>
      <LdStats/>
      <LdBeforeAfter/>
      <LdWhy/>
      <LdFinalCta/>
      <LdFooter/>
    </div>
  );
}

window.Landing = Landing;
