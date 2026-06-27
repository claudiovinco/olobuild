// VARIANT 4 — Audacious: dark glass / futurist
// Dark sidebar with subtle green glow, horizontal scrolling category pills,
// element grid with thumbnail-style cards.
const { useState: useState4 } = React;

function Variant4() {
  const [tab, setTab] = useState4("elementi");
  const [active, setActive] = useState4("essenziale");
  const [q, setQ] = useState4("");
  const [view, setView] = useState4("grid"); // grid | list
  const ql = q.trim().toLowerCase();
  const cat = window.OLO_CATEGORIES.find(c => c.id === active);
  const items = (window.OLO_ELEMENTS[active] || []).filter(e => !ql || e.label.toLowerCase().includes(ql));
  const searchHits = ql ? window.OLO_ELEMENTS_FLAT.filter(e => e.label.toLowerCase().includes(ql)) : null;

  return (
    <div className="v4-side">
      <div className="v4-tabs">
        <button className={tab==="elementi"?"active":""} onClick={()=>setTab("elementi")}>
          <OLOIcon name="grid" size={13}/> Elementi
        </button>
        <button className={tab==="struttura"?"active":""} onClick={()=>setTab("struttura")}>
          <OLOIcon name="layers" size={13}/> Struttura
        </button>
        <div className="ind" style={{transform: tab==="elementi"?"translateX(0)":"translateX(100%)"}}/>
      </div>

      <div className="v4-search">
        <OLOIcon name="search" size={14}/>
        <input value={q} onChange={e=>setQ(e.target.value)} placeholder="Cerca tra 97 elementi…"/>
        <span className="kbd">⌘K</span>
      </div>

      {/* Horizontal scrolling category pills */}
      <div className="v4-pills scrolly-x">
        {window.OLO_CATEGORIES.map(c => (
          <button
            key={c.id}
            className={"v4-pill " + (active===c.id ? "on" : "")}
            onClick={()=>{ setActive(c.id); setQ(""); }}
            style={active===c.id ? {"--glow": c.dot} : null}
          >
            <span className="ic"><OLOIcon name={c.icon} size={13}/></span>
            <span>{c.label}</span>
            <span className="cnt">{c.count}</span>
          </button>
        ))}
      </div>

      <div className="v4-toolbar">
        <div className="title">
          {ql ? `${(searchHits||[]).length} risultati` : cat.label}
          <span className="dot" style={{background:cat.dot, boxShadow:`0 0 8px ${cat.dot}`}}/>
        </div>
        <div className="seg">
          <button className={view==="grid"?"on":""} onClick={()=>setView("grid")} title="Griglia"><OLOIcon name="grid" size={12}/></button>
          <button className={view==="list"?"on":""} onClick={()=>setView("list")} title="Lista"><OLOIcon name="list" size={12}/></button>
        </div>
      </div>

      <div className={"v4-content scrolly " + (view==="grid"?"as-grid":"as-list")}>
        {(searchHits || items).map(e => (
          <button
            key={(e.cat||active)+"-"+e.id}
            className="v4-card"
            draggable
            onDragStart={ev=>startElementDrag(ev,e.label)}
          >
            <div className="ic">
              <OLOIcon name={e.icon} size={view==="grid"?22:16}/>
            </div>
            <div className="lbl">{e.label}</div>
            {searchHits && (
              <div className="bdg">{window.OLO_CATEGORIES.find(c=>c.id===e.cat)?.label}</div>
            )}
            {e.fav && <span className="fav"/>}
          </button>
        ))}
        {searchHits && searchHits.length === 0 && (
          <div className="v4-empty">Nessun elemento per "<b>{q}</b>"</div>
        )}
      </div>

      <div className="v4-foot">
        <button className="add" title="Aggiungi sezione"><OLOIcon name="plus" size={14}/></button>
        <span className="hint">Trascina o premi <kbd>⏎</kbd> per inserire</span>
      </div>
    </div>
  );
}

window.Variant4 = Variant4;
