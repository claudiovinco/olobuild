// OLObuild homepage — main composition

function HomeBanner({ onClose }) {
  return (
    <div className="home-banner">
      <span className="ic"><HomeIcon name="warn" size={18}/></span>
      <span>
        <b>Aggiornamento disponibile · v3.34.7</b> — include fix per Safari sticky e nuovo widget Mappa interattiva.
      </span>
      <span className="spc"/>
      <button className="act">Aggiorna ora</button>
      <button className="x" onClick={onClose} title="Chiudi"><HomeIcon name="dot3" size={14}/></button>
    </div>
  );
}

function HomeHero() {
  return (
    <div className="hero">
      <div className="hero-l">
        <div className="greet">Ciao Marco, buon lavoro</div>
        <h1>Continua su <b>Home — Hotel Resort</b></h1>
        <div className="sub">
          Hai modificato questa pagina <b>2 minuti fa</b>. La bozza non è ancora pubblicata.
        </div>
        <div className="meta-row">
          <span className="item"><HomeIcon name="globe" size={14}/> hotel-resort.it</span>
          <span className="item"><HomeIcon name="fileText" size={14}/> 47 pagine</span>
          <span className="item"><HomeIcon name="rocket" size={14}/> v3.34.6</span>
        </div>
        <div className="ctas">
          <button className="pri"><HomeIcon name="edit" size={14}/> Apri editor</button>
          <button className="sec"><HomeIcon name="external" size={14}/> Vedi sito live</button>
          <button className="sec"><HomeIcon name="plus" size={14}/> Nuova pagina</button>
        </div>
      </div>
      <div className="hero-r">
        <div className="live-pill">live</div>
        <div className="browser">
          <div className="br-bar">
            <span className="dot r"/><span className="dot y"/><span className="dot g"/>
            <span className="url">hotel-resort.it</span>
          </div>
          <div className="br-body">
            <div className="nav">
              <span className="logo">RESORT</span>
              <span>Camere</span><span>Servizi</span><span>Prenota</span>
            </div>
            <h2>Benvenuto al Resort delle Ville</h2>
            <p>Una struttura immersa nel verde, a 10 minuti dal mare.</p>
            <span className="btn">Prenota ora →</span>
          </div>
        </div>
      </div>
    </div>
  );
}

function KpiStrip() {
  return (
    <div className="kpi-strip">
      {HOME_KPIS.map(k => (
        <div key={k.label} className={"kpi " + (k.trend==="up"?"up":k.trend==="warn"?"warn":"")}>
          <div className="kpi-h">
            <span className="kpi-ic"><HomeIcon name={k.icon} size={13}/></span>
            <span>{k.label}</span>
          </div>
          <div className="kpi-val">{k.value}</div>
          <div className="kpi-d">
            <HomeIcon name={k.trend==="up"?"trendUp":k.trend==="warn"?"warn":"trendFlat"} size={11}/>
            {k.delta}
          </div>
        </div>
      ))}
    </div>
  );
}

function RecentStrip() {
  return (
    <div>
      <div className="sec-h">
        <h2>Continua dove avevi lasciato</h2>
        <span className="hint">Le tue ultime modifiche</span>
        <span className="spc"/>
        <button className="more">Vedi tutto <HomeIcon name="arrow" size={11}/></button>
      </div>
      <div className="recent-strip" style={{marginTop:8}}>
        {HOME_RECENT.map(r => (
          <div key={r.id} className="recent-card">
            <div className="thumb" style={{background: r.thumb}}>
              {r.status && <span className={"pill "+r.status}>{r.status==="live"?"live":"bozza"}</span>}
            </div>
            <div className="body">
              <div className="title">{r.title}</div>
              <div className="meta"><span className="type">{r.type}</span> · {r.time}</div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function QuickRow() {
  return (
    <div>
      <div className="sec-h">
        <h2>Azioni rapide</h2>
        <span className="hint">Ciò che fai più spesso</span>
      </div>
      <div className="quick-row" style={{marginTop:8}}>
        {HOME_QUICK.map(a => (
          <div key={a.id} className={"quick-card tone-"+a.tone}>
            <div className="ic-box"><HomeIcon name={a.icon} size={20}/></div>
            <div className="lab">
              <span className="t">{a.label}</span>
              <span className="h">{a.hint}</span>
            </div>
            <span className="arr"><HomeIcon name="arrow" size={14}/></span>
          </div>
        ))}
      </div>
    </div>
  );
}

function ManageGrid() {
  const [pinned, setPinned] = useStateH(new Set(["tpl","seo","form"]));
  const togglePin = (id, e) => {
    e.stopPropagation();
    setPinned(prev => {
      const n = new Set(prev);
      if (n.has(id)) n.delete(id); else n.add(id);
      return n;
    });
  };
  // sort: pinned first
  const tiles = [...HOME_MANAGE].sort((a,b) => (pinned.has(b.id)?1:0) - (pinned.has(a.id)?1:0));
  return (
    <div>
      <div className="sec-h">
        <h2>Gestione</h2>
        <span className="hint">Configurazione e contenuti del sito</span>
        <span className="spc"/>
        <button className="more">Personalizza ordine</button>
      </div>
      <div className="manage-grid" style={{marginTop:8}}>
        {tiles.map(t => (
          <div key={t.id} className="manage-tile">
            <div className="ic-sq" style={{background: t.color}}>
              <HomeIcon name={t.icon} size={18}/>
            </div>
            <div className="lab">
              <span className="t">{t.label}</span>
              <span className="h">{t.hint}</span>
            </div>
            <button
              className={"pin " + (pinned.has(t.id)?"on":"")}
              onClick={(e)=>togglePin(t.id, e)}
              title={pinned.has(t.id)?"Rimuovi dai preferiti":"Aggiungi ai preferiti"}
            >
              <HomeIcon name={pinned.has(t.id)?"pinFill":"pin2"} size={13}/>
            </button>
            {t.badge && <span className="badge-num">{t.badge}</span>}
          </div>
        ))}
      </div>
    </div>
  );
}

function SystemRow() {
  return (
    <div>
      <div className="sec-h">
        <h2>Sistema</h2>
        <span className="hint">Configurazione tecnica · raramente</span>
      </div>
      <div className="system-row" style={{marginTop:8}}>
        {HOME_SYSTEM.map(s => (
          <button key={s.id} className="system-chip">
            <span className="ic"><HomeIcon name={s.icon} size={13}/></span>
            {s.label}
          </button>
        ))}
      </div>
    </div>
  );
}

function HomeRail({ collapsed, onToggle }) {
  if (collapsed) {
    return (
      <aside className="home-rail">
        <div className="rail-head">
          <button className="toggle" onClick={onToggle} title="Espandi pannello">
            <HomeIcon name="panelRight" size={14}/>
          </button>
        </div>
        <div className="rail-mini">
          <button title="Cosa c'è di nuovo">
            <HomeIcon name="rocket" size={18}/>
            <span className="dot-new"/>
          </button>
          <button title="Tutorial"><HomeIcon name="play" size={18}/></button>
          <button title="Documentazione"><HomeIcon name="question" size={18}/></button>
          <button title="Notifiche">
            <HomeIcon name="bell" size={18}/>
            <span className="dot-new"/>
          </button>
        </div>
      </aside>
    );
  }
  return (
    <aside className="home-rail">
      <div className="rail-head">
        <h2>Centro risorse</h2>
        <button className="toggle" onClick={onToggle} title="Comprimi pannello">
          <HomeIcon name="collapse" size={13}/>
        </button>
      </div>
      <div className="rail-body">
        <div className="rail-section">
          <h3>Cosa c'è di nuovo <span className="pill">v3.34.6</span></h3>
          {HOME_CHANGELOG.map((c,i) => (
            <div key={c.v} className={"cl-item " + (i>0?"old":"")}>
              <div className="v">
                {c.v} <span className="date">· {c.date}</span>
                <span className={"tag "+c.tag}>{c.tag}</span>
              </div>
              <ul>
                {c.items.map(it => <li key={it}>{it}</li>)}
              </ul>
            </div>
          ))}
        </div>

        <div className="rail-section">
          <h3>Impara OLObuild</h3>
          {HOME_LEARN.map(l => (
            <div key={l.id} className="learn-card">
              <div className="th" style={{background: l.thumb}}>{l.iconBg}</div>
              <div className="info">
                <span className="t">{l.title}</span>
                <span className="d"><HomeIcon name="play" size={9}/> {l.duration}</span>
              </div>
            </div>
          ))}
        </div>

        <div className="rail-section">
          <h3>Aiuto & supporto</h3>
          <div className="help-row">
            <a><span className="ic"><HomeIcon name="question" size={13}/></span> Documentazione</a>
            <a><span className="ic"><HomeIcon name="form" size={13}/></span> Apri ticket</a>
            <a><span className="ic"><HomeIcon name="users" size={13}/></span> Community</a>
            <a><span className="ic"><HomeIcon name="external" size={13}/></span> Cosa stiamo facendo</a>
          </div>
        </div>
      </div>
    </aside>
  );
}

function HomeTopBar() {
  return (
    <div className="home-bar">
      <img src="assets/olobuild-horizontal.png" alt="OLObuild" className="logo"/>
      <span className="ver">v3.34.6</span>
      <span style={{width:1,height:18,background:"var(--ot-border)",margin:"0 4px"}}/>
      <span className="crumb">Olobuild · <b>Dashboard</b></span>
      <span className="spc"/>
      <div className="search-mini">
        <HomeIcon name="search" size={14} style={{color:"var(--ot-text-muted)"}}/>
        <input placeholder="Cerca pagine, template, impostazioni…"/>
        <kbd>⌘K</kbd>
      </div>
      <button className="ico-btn" title="Notifiche">
        <HomeIcon name="bell" size={15}/>
        <span className="badge-dot"/>
      </button>
      <button className="ico-btn" title="Aiuto"><HomeIcon name="question" size={15}/></button>
      <button className="ico-btn" title="Profilo">
        <span style={{width:22,height:22,borderRadius:99,background:"linear-gradient(135deg,#4a8c2a,#3fa23f)",color:"#fff",display:"grid",placeItems:"center",fontSize:10,fontWeight:700}}>MA</span>
      </button>
    </div>
  );
}

function AppBackStrip() {
  return (
    <div className="app-back">
      <a><HomeIcon name="collapse" size={12} style={{transform:"rotate(0)"}}/> Torna a WordPress</a>
      <span style={{opacity:.4}}>|</span>
      <span>hotel-resort.it</span>
      <span className="spc"/>
      <span className="pill-app">App mode</span>
    </div>
  );
}

function HomeDashboard({ appMode = true }) {
  const [collapsed, setCollapsed] = useStateH(false);
  const [showBanner, setShowBanner] = useStateH(true);
  return (
    <WPShell activeSub="Dashboard" appMode={appMode}>
      {appMode && <AppBackStrip/>}
      <HomeTopBar/>
      <div className={"home-grid " + (collapsed?"collapsed":"")}>
        <main className="home-content">
          {showBanner && <HomeBanner onClose={()=>setShowBanner(false)}/>}
          <HomeHero/>
          <KpiStrip/>
          <RecentStrip/>
          <QuickRow/>
          <ManageGrid/>
          <SystemRow/>
        </main>
        <HomeRail collapsed={collapsed} onToggle={()=>setCollapsed(c=>!c)}/>
      </div>
    </WPShell>
  );
}

window.HomeDashboard = HomeDashboard;
