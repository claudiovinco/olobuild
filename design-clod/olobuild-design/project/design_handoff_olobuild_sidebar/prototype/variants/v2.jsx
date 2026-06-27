// VARIANT 2 — Vertical Tabs (Webflow/Figma library pattern)
// 56px icon rail + 304px element panel. Active category highlights both.
const { useState: useState2 } = React;

function Variant2({ defaultTab = "elementi" }) {
  const [tab, setTab] = useState2(defaultTab);
  const [active, setActive] = useState2("essenziale");
  const [q, setQ] = useState2("");
  if (tab === "struttura") {
    return (
      <div className="v2-side">
        <div className="bui-side-tabs">
          <button className={tab==="elementi"?"active":""} onClick={()=>setTab("elementi")}>
            <OLOIcon name="grid" size={14}/> Elementi
          </button>
          <button className={tab==="struttura"?"active":""} onClick={()=>setTab("struttura")}>
            <OLOIcon name="layers" size={14}/> Struttura
          </button>
        </div>
        <V2Struttura/>
      </div>
    );
  }
  const ql = q.trim().toLowerCase();
  const cat = window.OLO_CATEGORIES.find(c => c.id === active);
  const items = (window.OLO_ELEMENTS[active] || []).filter(e => !ql || e.label.toLowerCase().includes(ql));
  const searchHits = ql ? window.OLO_ELEMENTS_FLAT.filter(e => e.label.toLowerCase().includes(ql)) : null;

  return (
    <div className="v2-side">
      <div className="bui-side-tabs">
        <button className={tab==="elementi"?"active":""} onClick={()=>setTab("elementi")}>
          <OLOIcon name="grid" size={14}/> Elementi
        </button>
        <button className={tab==="struttura"?"active":""} onClick={()=>setTab("struttura")}>
          <OLOIcon name="layers" size={14}/> Struttura
        </button>
      </div>

      <div className="v2-body">
        {/* Rail */}
        <div className="v2-rail">
          {window.OLO_CATEGORIES.map(c => (
            <button
              key={c.id}
              className={"v2-rail-btn " + (active===c.id?"on":"")}
              onClick={()=>{ setActive(c.id); setQ(""); }}
              title={c.label}
            >
              <span className="bar"/>
              <OLOIcon name={c.icon} size={18}/>
              <span className="lbl">{c.label}</span>
              <span className="cnt">{c.count}</span>
            </button>
          ))}
          <div className="spc"/>
          <button className="v2-rail-btn add" title="Gestisci categorie">
            <span className="bar"/>
            <OLOIcon name="plus" size={16}/>
            <span className="lbl">Personalizza</span>
          </button>
        </div>

        {/* Panel */}
        <div className="v2-panel">
          <div className="v2-panel-head">
            <span className="dot" style={{background: cat.dot}}/>
            <h3>{ql ? "Risultati" : cat.label}</h3>
            <span className="cnt">{ql ? (searchHits||[]).length : cat.count}</span>
          </div>

          <div className="v2-search">
            <OLOIcon name="search" size={14} style={{color:"var(--ot-text-muted)"}}/>
            <input value={q} onChange={e=>setQ(e.target.value)} placeholder={ql ? "" : `Cerca in ${cat.label}…`}/>
            {q && <button className="ic" onClick={()=>setQ("")}><OLOIcon name="plus" size={12} style={{transform:"rotate(45deg)"}}/></button>}
          </div>

          <div className="v2-grid scrolly">
            {(searchHits || items).map(e => (
              <button
                key={(e.cat||active)+"-"+e.id}
                className="v2-card"
                draggable
                onDragStart={ev=>startElementDrag(ev,e.label)}
              >
                <div className="ic"><OLOIcon name={e.icon} size={20}/></div>
                <div className="lbl">{e.label}</div>
                {searchHits && (
                  <div className="bdg" style={{background: (window.OLO_CATEGORIES.find(c=>c.id===e.cat)||{}).dot+"22", color:(window.OLO_CATEGORIES.find(c=>c.id===e.cat)||{}).dot}}>
                    {window.OLO_CATEGORIES.find(c=>c.id===e.cat)?.label}
                  </div>
                )}
                {e.fav && !searchHits && <OLOIcon name="star" size={11} className="fav"/>}
                <div className="grip"><OLOIcon name="drag" size={12}/></div>
              </button>
            ))}
            {searchHits && searchHits.length === 0 && (
              <div className="v2-empty">Nessun elemento corrisponde a "<b>{q}</b>"</div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

window.Variant2 = Variant2;
