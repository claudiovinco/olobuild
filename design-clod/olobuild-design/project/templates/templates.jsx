// Gestione Template page — composition

function TplPreview({ kind }) {
  // Inline mini-preview shapes per template kind
  if (kind === "empty") return <div className="pv-empty">0 elementi</div>;
  if (kind === "header") return (
    <div className="preview" style={{padding:0}}>
      <div style={{height:"30%", background:"#1d2327", display:"flex", alignItems:"center", padding:"0 12px", gap:8}}>
        <div style={{width:24,height:8,background:"rgba(255,255,255,.5)",borderRadius:2}}/>
        <div style={{flex:1}}/>
        <div style={{width:30,height:5,background:"rgba(255,255,255,.3)",borderRadius:2}}/>
        <div style={{width:30,height:5,background:"rgba(255,255,255,.3)",borderRadius:2}}/>
        <div style={{width:30,height:5,background:"rgba(255,255,255,.3)",borderRadius:2}}/>
      </div>
      <div style={{padding:"12px 14px"}}>
        <div className="pv-bar med"/>
        <div className="pv-bar short" style={{marginTop:6}}/>
      </div>
    </div>
  );
  if (kind === "footer") return (
    <div className="preview" style={{padding:0}}>
      <div style={{flex:1, padding:"12px 14px"}}>
        <div className="pv-bar med"/>
        <div className="pv-bar short" style={{marginTop:6}}/>
      </div>
      <div style={{height:"35%", background:"#1d2327", padding:"10px 12px", display:"grid", gridTemplateColumns:"repeat(4,1fr)", gap:6, alignContent:"center"}}>
        {[0,1,2,3].map(i => (
          <div key={i} style={{display:"flex",flexDirection:"column",gap:4}}>
            <div style={{height:5,background:"rgba(255,255,255,.5)",borderRadius:2,width:"60%"}}/>
            <div style={{height:3,background:"rgba(255,255,255,.25)",borderRadius:2}}/>
            <div style={{height:3,background:"rgba(255,255,255,.25)",borderRadius:2,width:"70%"}}/>
          </div>
        ))}
      </div>
    </div>
  );
  if (kind === "widget") return (
    <div className="preview">
      <div className="pv-hero" style={{flex:1, background:"linear-gradient(135deg, var(--ot-primary-100), var(--ot-primary-50))", display:"flex", alignItems:"center", justifyContent:"center"}}>
        <div style={{display:"flex",gap:6,alignItems:"center",background:"#fff",padding:"6px 10px",borderRadius:6,border:"1px solid rgba(0,0,0,.06)"}}>
          <div style={{width:20,height:20,borderRadius:4,background:"var(--ot-primary)"}}/>
          <div style={{display:"flex",flexDirection:"column",gap:3}}>
            <div className="pv-bar" style={{width:50}}/>
            <div className="pv-bar short" style={{width:30}}/>
          </div>
        </div>
      </div>
    </div>
  );
  if (kind === "split") return (
    <div className="preview" style={{flexDirection:"row"}}>
      <div style={{flex:1, background:"rgba(15,17,21,.07)", borderRadius:4, display:"flex",alignItems:"center",justifyContent:"center"}}>
        <div style={{width:24,height:24,borderRadius:4,background:"var(--ot-primary)",opacity:.6}}/>
      </div>
      <div style={{flex:1.2,display:"flex",flexDirection:"column",justifyContent:"center",gap:5,paddingLeft:8}}>
        <div className="pv-bar"/>
        <div className="pv-bar med"/>
        <div className="pv-bar short"/>
        <div className="pv-bar brand short" style={{marginTop:4,height:5,width:"40%"}}/>
      </div>
    </div>
  );
  if (kind === "long") return (
    <div className="preview">
      <div className="pv-bar med dark"/>
      <div className="pv-bar short"/>
      <div className="pv-row">
        <div className="cell"/><div className="cell"/><div className="cell"/>
      </div>
      <div className="pv-row" style={{gridTemplateColumns:"1fr 1fr"}}>
        <div className="cell" style={{height:24}}/><div className="cell" style={{height:24}}/>
      </div>
    </div>
  );
  if (kind === "grid") return (
    <div className="preview">
      <div className="pv-row" style={{gridTemplateColumns:"repeat(4,1fr)", gap:5}}>
        {[0,1,2,3,4,5,6,7].map(i => <div key={i} className="cell" style={{height:24}}/>)}
      </div>
    </div>
  );
  // hero+grid (default)
  return (
    <div className="preview">
      <div className="pv-hero" style={{flex:"1.2", background:"linear-gradient(135deg, var(--ot-primary-100) 0%, var(--ot-primary-50) 100%)"}}>
        <div style={{display:"flex",flexDirection:"column",gap:4,padding:8,width:"60%"}}>
          <div className="pv-bar dark"/>
          <div className="pv-bar short"/>
        </div>
      </div>
      <div className="pv-row">
        <div className="cell"/><div className="cell"/><div className="cell"/>
      </div>
    </div>
  );
}

function TplCard({ t }) {
  const meta = TPL_TYPE_META[t.type] || { label: t.type.toUpperCase(), color: "slate" };
  const isEmpty = t.elements === 0;
  return (
    <div className={"tpl-card " + (isEmpty?"is-empty":"")}>
      <div className="thumb" style={{background: t.thumb}}>
        {!isEmpty && <TplPreview kind={t.preview}/>}
        {isEmpty && <div className="pv-empty"/>}
        <div className="badges">
          <span className={"badge t-"+t.type}>{meta.label}{t.singleType?`: ${t.singleType}`:""}</span>
        </div>
        <div className="badge-r">
          {t.attivo && <span className="badge attivo">Attivo</span>}
          {t.status === "draft" && <span className="badge draft">Bozza</span>}
        </div>
        {!isEmpty && <span className="pv-elements">{t.elements} elementi</span>}
      </div>
      <div className="body">
        <div className="title-row">
          <span className="title">{t.title}</span>
        </div>
        <div className="meta">
          <span className={"dot-status "+(t.status==="draft"?"draft":"")}/>
          <span>{t.status === "draft" ? "Bozza" : "Pubblicato"}</span>
          <span style={{opacity:.5}}>·</span>
          <span>{t.date}</span>
          <span style={{opacity:.5}}>·</span>
          <span>ID {t.id}</span>
        </div>
        <span className="shortcode" title="Clicca per copiare">
          <span className="cp"><HomeIcon name="copy" size={11}/></span>
          [olo_template id="{t.id}"]
        </span>
      </div>
      <div className="actions">
        <button title="Modifica"><HomeIcon name="edit" size={13}/></button>
        <button title="Duplica"><HomeIcon name="copy" size={13}/></button>
        <button title="Elimina" className="danger"><HomeIcon name="trash" size={13}/></button>
      </div>
    </div>
  );
}

function TplListRow({ t }) {
  const meta = TPL_TYPE_META[t.type] || { label: t.type.toUpperCase() };
  return (
    <div className="row">
      <div className="mini-thumb" style={{background: t.thumb}}/>
      <div>
        <div className="ttl">{t.title}</div>
        <div style={{fontSize:11, color:"var(--ot-text-muted)", marginTop:2, fontFamily:"ui-monospace, monospace"}}>[olo_template id="{t.id}"]</div>
      </div>
      <span className={"badge t-"+t.type}>{meta.label}</span>
      <div style={{display:"flex",alignItems:"center",gap:6,fontSize:12,color:"var(--ot-text-muted)"}}>
        <span className={"dot-status "+(t.status==="draft"?"draft":"")} style={{width:6,height:6,borderRadius:99,background:t.status==="draft"?"#f59e0b":"#22c55e"}}/>
        {t.status === "draft" ? "Bozza" : "Pubblicato"}
        {t.attivo && <span style={{fontSize:9.5,fontWeight:700,padding:"2px 6px",background:"var(--ot-primary)",color:"#fff",borderRadius:99,letterSpacing:".05em"}}>ATTIVO</span>}
      </div>
      <div style={{color:"var(--ot-text-muted)",fontSize:12}}>{t.date} · {t.elements} elementi</div>
      <div className="acts">
        <button title="Modifica"><HomeIcon name="edit" size={12}/></button>
        <button title="Duplica"><HomeIcon name="copy" size={12}/></button>
        <button title="Elimina"><HomeIcon name="trash" size={12}/></button>
      </div>
    </div>
  );
}

const TONE_BG = {
  primary: "linear-gradient(135deg,#e1474f,#ec5a62)",
  blue:    "linear-gradient(135deg,#3b82f6,#1d4ed8)",
  slate:   "linear-gradient(135deg,#475569,#1e293b)",
  purple:  "linear-gradient(135deg,#a855f7,#7e22ce)",
  amber:   "linear-gradient(135deg,#f59e0b,#d97706)",
  violet:  "linear-gradient(135deg,#8b5cf6,#5b21b6)",
  red:     "linear-gradient(135deg,#ef4444,#b91c1c)",
};

function NewTemplateMenu({ onClose }) {
  React.useEffect(() => {
    const h = (e) => { if (!e.target.closest(".split")) onClose(); };
    setTimeout(() => document.addEventListener("click", h), 0);
    return () => document.removeEventListener("click", h);
  }, []);
  return (
    <div className="tpl-new-menu">
      {TPL_NEW_OPTIONS.map((g, gi) => (
        <React.Fragment key={g.group}>
          {gi > 0 && <hr/>}
          <div className="grp-head">{g.group}</div>
          {g.items.map(it => (
            <div key={it.id} className="item" onClick={onClose}>
              <span className="ic-box" style={{background: TONE_BG[it.color] || TONE_BG.slate}}>
                <HomeIcon name={it.icon} size={13}/>
              </span>
              {it.label}
            </div>
          ))}
        </React.Fragment>
      ))}
    </div>
  );
}

function TplPage({ appMode = true }) {
  const [activeType, setActiveType] = React.useState("all");
  const [activeSub,  setActiveSub]  = React.useState("salvati");
  const [view,       setView]       = React.useState("grid");
  const [menuOpen,   setMenuOpen]   = React.useState(false);
  const [query,      setQuery]      = React.useState("");

  const filtered = TPL_LIST.filter(t => {
    if (activeType !== "all" && t.type !== activeType) return false;
    if (query && !t.title.toLowerCase().includes(query.toLowerCase())) return false;
    return true;
  });

  const subTabs = [
    { id:"salvati", label:"Template Salvati", n: TPL_LIST.length },
    { id:"website", label:"Template Website", n: 8 },
    { id:"popups",  label:"Popups",           n: 3 },
  ];

  const crumb = (
    <span>Olobuild · <a style={{color:"inherit",textDecoration:"none",cursor:"pointer"}}>Template</a> · <b>Salvati</b></span>
  );

  return (
    <WPShell activeSub="Gestione Template" appMode={appMode}>
      {appMode && <AppBackStrip/>}
      <HomeTopBar crumb={crumb}/>
      <main className="tpl-page">
        <div className="tpl-head">
          <div className="titles">
            <h1>Gestione Template</h1>
            <div className="sub">
              <b>{TPL_LIST.length}</b> template totali · <b>3</b> attivi · ultima modifica 2 minuti fa
            </div>
          </div>
          <div className="spc"/>
          <button className="btn-sec">
            <HomeIcon name="upload" size={13}/> Importa
          </button>
          <div className="split">
            <button className="btn-pri main">
              <HomeIcon name="plus" size={13}/> Nuovo Template
            </button>
            <button className="caret" onClick={(e)=>{ e.stopPropagation(); setMenuOpen(o=>!o); }}>
              <HomeIcon name="chevronDown" size={12}/>
            </button>
            {menuOpen && <NewTemplateMenu onClose={()=>setMenuOpen(false)}/>}
          </div>
        </div>

        <div className="tpl-subnav">
          {subTabs.map(s => (
            <a key={s.id} className={s.id===activeSub?"active":""} onClick={()=>setActiveSub(s.id)}>
              {s.label} <span className="num">{s.n}</span>
            </a>
          ))}
        </div>

        <div className="tpl-toolbar">
          <div className="filters">
            {TPL_TYPES.map(t => (
              <button key={t.id} className={"chip "+(t.id===activeType?"on":"")} onClick={()=>setActiveType(t.id)}>
                {t.label} <span className="num">{t.count}</span>
              </button>
            ))}
          </div>
          <div className="div"/>
          <div className="search">
            <HomeIcon name="search" size={12} style={{color:"var(--ot-text-muted)"}}/>
            <input placeholder="Cerca per nome o ID…" value={query} onChange={e=>setQuery(e.target.value)}/>
          </div>
          <div className="spc"/>
          <button className="sort">
            <HomeIcon name="sliders" size={12}/> Ultime modifiche
            <HomeIcon name="chevronDown" size={11}/>
          </button>
          <div className="view-tog">
            <button className={view==="grid"?"on":""} onClick={()=>setView("grid")} title="Griglia">
              <HomeIcon name="grid" size={13}/>
            </button>
            <button className={view==="list"?"on":""} onClick={()=>setView("list")} title="Lista">
              <HomeIcon name="list" size={13}/>
            </button>
          </div>
        </div>

        {view === "grid" ? (
          <div className="tpl-grid">
            {filtered.map(t => <TplCard key={t.id} t={t}/>)}
          </div>
        ) : (
          <div className="tpl-list">
            <div className="row h">
              <span/>
              <span>Template</span>
              <span>Tipo</span>
              <span>Stato</span>
              <span>Modificato</span>
              <span/>
            </div>
            {filtered.map(t => <TplListRow key={t.id} t={t}/>)}
          </div>
        )}

        {filtered.length === 0 && (
          <div style={{padding:"60px 20px",textAlign:"center",color:"var(--ot-text-muted)"}}>
            <div style={{fontSize:14,fontWeight:600,color:"var(--ot-text)"}}>Nessun template trovato</div>
            <div style={{fontSize:12,marginTop:4}}>Prova a cambiare filtro o crea un nuovo template.</div>
          </div>
        )}
      </main>
    </WPShell>
  );
}

window.TplPage = TplPage;
