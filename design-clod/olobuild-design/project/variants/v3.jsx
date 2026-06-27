// VARIANT 3 — Smart Library: search-first with quick-add cards & live preview popover
const { useState: useState3, useRef: useRef3 } = React;

function PreviewPop({ el }) {
  // mini visual mock per element type
  const mock = {
    button:    <div style={{padding:"6px 14px", background:"var(--ot-primary)", color:"#fff", borderRadius:6, fontSize:11, fontWeight:600}}>Scopri</div>,
    image:     <div style={{width:"100%", height:60, background:"linear-gradient(135deg,#94a3b8,#475569)", borderRadius:4, position:"relative"}}><div style={{position:"absolute",inset:0,display:"grid",placeItems:"center",color:"#fff",opacity:.7}}><OLOIcon name="image" size={20}/></div></div>,
    video:     <div style={{width:"100%", height:60, background:"#0f172a", borderRadius:4, display:"grid", placeItems:"center"}}><OLOIcon name="video" size={20} color="#fff"/></div>,
    spacer:    <div style={{height:60, display:"flex", alignItems:"center", justifyContent:"center"}}><div style={{width:"100%", height:1, background:"var(--ot-border)", position:"relative"}}><span style={{position:"absolute",left:0,top:-4,width:1,height:9,background:"var(--ot-border-strong)"}}/><span style={{position:"absolute",right:0,top:-4,width:1,height:9,background:"var(--ot-border-strong)"}}/></div></div>,
    floatpanel:<div style={{position:"relative", height:60}}><div style={{position:"absolute",left:0,top:0,right:14,bottom:14,background:"var(--ot-bg-muted)",border:"1px solid var(--ot-border)",borderRadius:4}}/><div style={{position:"absolute",right:0,bottom:0,left:14,top:14,background:"#fff",boxShadow:"var(--ot-shadow)",borderRadius:4}}/></div>,
    panel:     <div style={{height:60,background:"#fff",border:"1px solid var(--ot-border)",borderRadius:4,padding:6,display:"flex",flexDirection:"column",gap:3}}><div style={{height:6,background:"var(--ot-bg-muted)",borderRadius:2,width:"40%"}}/><div style={{height:4,background:"var(--ot-bg-muted)",borderRadius:2}}/><div style={{height:4,background:"var(--ot-bg-muted)",borderRadius:2,width:"70%"}}/></div>,
    hero:      <div style={{height:60,background:"linear-gradient(135deg,#1e3a12,#4a8c2a)",borderRadius:4,padding:8,color:"#fff",display:"flex",flexDirection:"column",justifyContent:"center"}}><div style={{height:5,background:"#fff",opacity:.9,borderRadius:2,width:"60%",marginBottom:3}}/><div style={{height:3,background:"#fff",opacity:.5,borderRadius:2,width:"80%"}}/></div>,
    cols:      <div style={{height:60,display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:3}}>{[0,1,2].map(i=><div key={i} style={{background:"var(--ot-bg-muted)",borderRadius:3}}/>)}</div>,
    grid:      <div style={{height:60,display:"grid",gridTemplateColumns:"1fr 1fr",gridTemplateRows:"1fr 1fr",gap:3}}>{[0,1,2,3].map(i=><div key={i} style={{background:"var(--ot-bg-muted)",borderRadius:3}}/>)}</div>,
    heading:   <div style={{padding:6}}><div style={{height:9,background:"var(--ot-text)",borderRadius:2,width:"55%"}}/></div>,
    text:      <div style={{padding:6,display:"flex",flexDirection:"column",gap:3}}><div style={{height:4,background:"var(--ot-text-muted)",borderRadius:2}}/><div style={{height:4,background:"var(--ot-text-muted)",borderRadius:2,width:"85%"}}/><div style={{height:4,background:"var(--ot-text-muted)",borderRadius:2,width:"60%"}}/></div>,
    map:       <div style={{height:60,background:"#dbeafe",borderRadius:4,position:"relative",overflow:"hidden"}}><div style={{position:"absolute",inset:0,backgroundImage:"linear-gradient(45deg,#bfdbfe 25%,transparent 25%),linear-gradient(-45deg,#bfdbfe 25%,transparent 25%)",backgroundSize:"12px 12px"}}/><div style={{position:"absolute",left:"50%",top:"50%",transform:"translate(-50%,-50%)",width:8,height:8,borderRadius:99,background:"#ef4444",boxShadow:"0 0 0 4px rgba(239,68,68,.3)"}}/></div>,
  }[el.icon] || <div style={{height:60, background:"var(--ot-bg-muted)", borderRadius:4, display:"grid", placeItems:"center", color:"var(--ot-text-muted)"}}><OLOIcon name={el.icon} size={22}/></div>;
  return (
    <div className="v3-pop">
      <div className="v3-pop-mock">{mock}</div>
      <div className="v3-pop-meta">
        <div className="t">{el.label}</div>
        <div className="d">Trascina per inserire · clic per aggiungere in fondo</div>
      </div>
    </div>
  );
}

function Variant3() {
  const [tab, setTab] = useState3("elementi");
  const [q, setQ] = useState3("");
  const [hover, setHover] = useState3(null);
  const ql = q.trim().toLowerCase();

  // Top quick-add: 8 most relevant favorites/recents
  const quick = [
    ...window.OLO_ELEMENTS.recenti.slice(0,4),
    ...window.OLO_ELEMENTS.preferiti.slice(0,4),
  ].slice(0,8);

  const filterCats = ql
    ? window.OLO_CATEGORIES.map(c => ({...c, _items: (window.OLO_ELEMENTS[c.id]||[]).filter(e => e.label.toLowerCase().includes(ql))}))
        .filter(c => c._items.length > 0)
    : window.OLO_CATEGORIES.map(c => ({...c, _items: window.OLO_ELEMENTS[c.id]||[]}));

  return (
    <div className="v3-side">
      <div className="bui-side-tabs">
        <button className={tab==="elementi"?"active":""} onClick={()=>setTab("elementi")}>
          <OLOIcon name="grid" size={14}/> Elementi
        </button>
        <button className={tab==="struttura"?"active":""} onClick={()=>setTab("struttura")}>
          <OLOIcon name="layers" size={14}/> Struttura
        </button>
      </div>

      <div className="v3-search">
        <OLOIcon name="search" size={16} style={{color:"var(--ot-text-muted)"}}/>
        <input value={q} onChange={e=>setQ(e.target.value)} placeholder="Cerca un elemento, incolla URL…"/>
        <span className="kbd">⌘K</span>
      </div>

      {!ql && (
        <div className="v3-quick">
          <div className="v3-quick-head">
            <OLOIcon name="zap" size={12} style={{color:"var(--ot-primary)"}}/>
            <span>Aggiungi rapido</span>
            <button className="lnk">Personalizza</button>
          </div>
          <div className="v3-quick-grid">
            {quick.map(e => (
              <button
                key={e.id}
                className="v3-quick-card"
                draggable
                onDragStart={ev=>startElementDrag(ev,e.label)}
                onMouseEnter={()=>setHover(e)}
                onMouseLeave={()=>setHover(null)}
              >
                <div className="ic"><OLOIcon name={e.icon} size={18}/></div>
                <div className="lbl">{e.label}</div>
              </button>
            ))}
          </div>
        </div>
      )}

      <div className="v3-list scrolly">
        {filterCats.map(c => (
          <div key={c.id} className="v3-cat">
            <div className="v3-cat-h">
              <span className="dot" style={{background:c.dot}}/>
              <span className="lbl">{c.label}</span>
              <span className="cnt">{c._items.length}</span>
              <button className="lnk-more">Tutti <OLOIcon name="chev" size={11}/></button>
            </div>
            <div className="v3-cat-strip">
              {c._items.slice(0, 6).map(e => (
                <button
                  key={e.id}
                  className="v3-row"
                  draggable
                  onDragStart={ev=>startElementDrag(ev,e.label)}
                  onMouseEnter={()=>setHover(e)}
                  onMouseLeave={()=>setHover(null)}
                >
                  <div className="ic"><OLOIcon name={e.icon} size={14}/></div>
                  <span className="lbl">{e.label}</span>
                  {e.fav && <OLOIcon name="star" size={10} style={{color:"#f59e0b"}}/>}
                  <OLOIcon name="drag" size={12} className="grip"/>
                </button>
              ))}
              {c._items.length > 6 && (
                <button className="v3-row more">+ {c._items.length-6} altri in {c.label}</button>
              )}
            </div>
          </div>
        ))}
      </div>

      {hover && <PreviewPop el={hover}/>}
    </div>
  );
}

window.Variant3 = Variant3;
