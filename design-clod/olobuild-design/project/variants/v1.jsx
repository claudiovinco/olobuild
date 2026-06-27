// VARIANT 1 — On-brand: dense, professional, fixes the source pain point
// (categorie collassabili dense, pinned strip, density toggle)
const { useState: useState1, useMemo: useMemo1 } = React;

function ElementChip({ el, density="cozy" }) {
  const big = density === "comfy";
  return (
    <div
      className="v1-chip"
      draggable
      onDragStart={e => startElementDrag(e, el.label)}
      style={{padding: big ? "10px 10px" : "7px 10px"}}
    >
      <div className="v1-chip-ic"><OLOIcon name={el.icon} size={big?16:14}/></div>
      <span className="v1-chip-lbl" title={el.label}>{el.label}</span>
      {el.fav && <OLOIcon name="star" size={11} style={{color:"#f59e0b", flexShrink:0}}/>}
    </div>
  );
}

function V1Section({ cat, items, density, open, onToggle, query }) {
  const filtered = items.filter(e => !query || e.label.toLowerCase().includes(query));
  if (query && filtered.length === 0) return null;
  return (
    <div className="v1-sec">
      <button className="v1-sec-h" onClick={onToggle}>
        <span className="dot" style={{background:cat.dot}}/>
        <span className="lbl">{cat.label}</span>
        <span className="cnt">{cat.count}</span>
        <OLOIcon name="chevDown" size={14} style={{marginLeft:"auto", transform: open?"rotate(0)":"rotate(-90deg)", transition:"transform .2s", color:"var(--ot-text-muted)"}}/>
      </button>
      {open && (
        <div className="v1-sec-body" style={{gridTemplateColumns: density==="comfy"?"1fr 1fr":density==="dense"?"1fr 1fr 1fr":"1fr 1fr"}}>
          {filtered.map(e => <ElementChip key={e.id} el={e} density={density}/>)}
        </div>
      )}
    </div>
  );
}

function Variant1() {
  const [tab, setTab] = useState1("elementi");
  const [q, setQ] = useState1("");
  const [density, setDensity] = useState1("cozy"); // cozy | dense | comfy
  const [open, setOpen] = useState1(() => Object.fromEntries(window.OLO_CATEGORIES.map(c => [c.id, ["recenti","preferiti","essenziale","layout"].includes(c.id)])));

  // Pinned strip = first 7 favorites flattened
  const pinned = window.OLO_ELEMENTS_FLAT.filter(e => e.fav).slice(0, 7);
  const ql = q.trim().toLowerCase();

  return (
    <div className="v1-side">
      <div className="bui-side-tabs">
        <button className={tab==="elementi"?"active":""} onClick={()=>setTab("elementi")}>
          <OLOIcon name="grid" size={14}/> Elementi
        </button>
        <button className={tab==="struttura"?"active":""} onClick={()=>setTab("struttura")}>
          <OLOIcon name="layers" size={14}/> Struttura
        </button>
      </div>

      <div className="v1-search">
        <OLOIcon name="search" size={14} style={{color:"var(--ot-text-muted)"}}/>
        <input value={q} onChange={e=>setQ(e.target.value)} placeholder="Cerca tra 97 elementi…"/>
        {q && <button className="ic" onClick={()=>setQ("")}><OLOIcon name="plus" size={12} style={{transform:"rotate(45deg)"}}/></button>}
        <span className="kbd">⌘K</span>
      </div>

      <div className="v1-toolbar">
        <span className="muted">{ql ? `${window.OLO_ELEMENTS_FLAT.filter(e=>e.label.toLowerCase().includes(ql)).length} risultati` : "10 categorie"}</span>
        <div className="seg">
          <button className={density==="dense"?"on":""} onClick={()=>setDensity("dense")} title="Densità alta"><OLOIcon name="grid" size={12}/></button>
          <button className={density==="cozy"?"on":""} onClick={()=>setDensity("cozy")} title="Standard"><OLOIcon name="square" size={12}/></button>
          <button className={density==="comfy"?"on":""} onClick={()=>setDensity("comfy")} title="Spaziato"><OLOIcon name="sliders" size={12}/></button>
        </div>
      </div>

      {!ql && (
        <div className="v1-pinned">
          <div className="v1-pinned-head">
            <OLOIcon name="pin" size={11}/> <span>Sempre a portata</span>
            <button className="lnk">Modifica</button>
          </div>
          <div className="v1-pinned-grid">
            {pinned.map(e => (
              <button key={e.id} className="v1-pin" draggable onDragStart={ev=>startElementDrag(ev,e.label)} title={e.label}>
                <OLOIcon name={e.icon} size={16}/>
              </button>
            ))}
          </div>
        </div>
      )}

      <div className="v1-list scrolly">
        {window.OLO_CATEGORIES.map(cat => (
          <V1Section
            key={cat.id} cat={cat}
            items={window.OLO_ELEMENTS[cat.id] || []}
            density={density}
            open={open[cat.id] || !!ql}
            onToggle={()=>setOpen(o=>({...o, [cat.id]: !o[cat.id]}))}
            query={ql}
          />
        ))}
      </div>
    </div>
  );
}

window.Variant1 = Variant1;
