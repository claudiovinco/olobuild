// V2 — Struttura (layers) panel, same design language as V2 elementi.
// Tree of Header / Body / Footer with semantic color accents,
// drag handles, hover actions, selected row, empty drop zones.

const V2S_TREE = [
  {
    id: "header", label: "Header", kind: "section", tone: "info",
    children: [{
      id: "h-sez", label: "Sezione", kind: "sezione", children: [{
        id: "h-row", label: "Row", kind: "row", children: [{
          id: "h-col", label: "Column", kind: "col", children: [
            { id: "h-mega", label: "Mega Menu", kind: "el", icon: "tabs" },
            { id: "h-col2", label: "Colonna",   kind: "el", icon: "cols" },
          ]
        }]
      }]
    }]
  },
  {
    id: "body", label: "Body", kind: "section", tone: "brand",
    children: [
      { id: "b-sez1", label: "Sezione", kind: "sezione", children: [
          { id: "b-row1", label: "Row", kind: "row", children: [
              { id: "b-c1", label: "Column", kind: "col", children: [
                  { id: "b-pdf", label: "PDF Viewer", kind: "el", icon: "code" },
                  { id: "b-img", label: "Immagine",   kind: "el", icon: "image", selected: true },
              ]},
              { id: "b-c2", label: "Column", kind: "col", children: [
                  { id: "b-img2", label: "Immagine",     kind: "el", icon: "image" },
                  { id: "b-tit",  label: "Titolo sezione", kind: "el", icon: "heading" },
              ]},
          ]},
      ]},
      { id: "b-sez2", label: "Sezione", kind: "sezione", children: [
          { id: "b-row2", label: "Row", kind: "row", children: [
              { id: "b-c3", label: "Column", kind: "col", children: [
                  { id: "b-img3", label: "Immagine",  kind: "el", icon: "image" },
                  { id: "b-sp",   label: "Spaziatore", kind: "el", icon: "spacer" },
              ]},
          ]},
      ]},
      { id: "b-sez3", label: "Sezione · CTA", kind: "sezione", children: [
          { id: "b-rowA", label: "Row", kind: "row", children: [
              { id: "b-cA", label: "Column", kind: "col", children: [
                  { id: "b-cta1", label: "Clicca qui", kind: "el", icon: "button" },
              ]},
              { id: "b-cB", label: "Column", kind: "col", children: [
                  { id: "b-drop", label: "Trascina qui", kind: "drop" },
              ]},
              { id: "b-cC", label: "Column", kind: "col", children: [
                  { id: "b-cta2", label: "Clicca qui", kind: "el", icon: "button" },
              ]},
              { id: "b-cD", label: "Column", kind: "col", children: [
                  { id: "b-cta3", label: "Clicca qui", kind: "el", icon: "button" },
              ]},
          ]},
      ]},
    ]
  },
  {
    id: "footer", label: "Footer", kind: "section", tone: "success",
    children: [{
      id: "f-sez", label: "Sezione", kind: "sezione", children: [
        { id: "f-row1", label: "Row", kind: "row", children: [
            { id: "f-c1", label: "Column", kind: "col" },
            { id: "f-c2", label: "Column", kind: "col" },
        ]},
        { id: "f-row2", label: "Row", kind: "row", children: [
            { id: "f-c3", label: "Column", kind: "col", children: [
                { id: "f-logo", label: "Logo sito", kind: "el", icon: "image" },
            ]},
            { id: "f-c4", label: "Column", kind: "col" },
            { id: "f-c5", label: "Column", kind: "col" },
        ]},
      ]
    }]
  },
];

const KIND_META = {
  section: { tone: "neutral" },
  sezione: { icon: "panel" },
  row:     { icon: "cols" },
  col:     { icon: "colsInner" },
};

function V2SNode({ node, depth, query }) {
  const [open, setOpen] = React.useState(true);
  const meta = KIND_META[node.kind] || {};
  const hasKids = (node.children || []).length > 0;
  const ql = (query||"").trim().toLowerCase();
  const match = !ql || node.label.toLowerCase().includes(ql);

  // Hide subtrees that don't match search
  if (ql) {
    const subMatch = (node.children || []).some(function rec(n){ return n.label.toLowerCase().includes(ql) || (n.children||[]).some(rec); });
    if (!match && !subMatch) return null;
  }

  if (node.kind === "drop") {
    return (
      <div className="v2s-drop" style={{marginLeft: depth*16 + 8}}>
        <OLOIcon name="plus" size={11}/> {node.label}
      </div>
    );
  }

  return (
    <div className={"v2s-node v2s-k-" + node.kind + (node.selected ? " selected" : "")}>
      <div className="v2s-row" style={{paddingLeft: 6 + depth*16}}>
        <span className="v2s-grip"><OLOIcon name="drag" size={11}/></span>
        {hasKids ? (
          <button className="v2s-chev" onClick={()=>setOpen(o=>!o)}>
            <OLOIcon name="chevDown" size={11} style={{transform: open?"rotate(0)":"rotate(-90deg)", transition:"transform .15s"}}/>
          </button>
        ) : <span className="v2s-chev"/>}
        <span className="v2s-ic"><OLOIcon name={node.icon || meta.icon || "square"} size={13}/></span>
        <span className="v2s-lbl">{node.label}</span>
        {node.selected && <span className="v2s-tag">selezionato</span>}
        <span className="v2s-actions">
          <button title="Mostra/nascondi"><OLOIcon name="eye" size={12}/></button>
          <button title="Blocca"><OLOIcon name="pin" size={12}/></button>
        </span>
      </div>
      {hasKids && open && (
        <div className="v2s-kids">
          {node.children.map(c => <V2SNode key={c.id} node={c} depth={depth+1} query={query}/>)}
        </div>
      )}
    </div>
  );
}

function V2SSection({ root, query }) {
  const [open, setOpen] = React.useState(true);
  return (
    <div className={"v2s-sec tone-" + root.tone}>
      <button className="v2s-sec-h" onClick={()=>setOpen(o=>!o)}>
        <span className="v2s-sec-bar"/>
        <OLOIcon name="chevDown" size={12} style={{transform: open?"rotate(0)":"rotate(-90deg)", transition:"transform .15s"}}/>
        <span className="v2s-sec-ic"><OLOIcon name="layers" size={13}/></span>
        <span className="v2s-sec-lbl">{root.label}</span>
        <span className="v2s-sec-cnt">{(root.children||[]).length} sezioni</span>
      </button>
      {open && (
        <div className="v2s-sec-body">
          {(root.children || []).map(c => <V2SNode key={c.id} node={c} depth={0} query={query}/>)}
        </div>
      )}
    </div>
  );
}

function V2Struttura() {
  const [filter, setFilter] = React.useState("all"); // all | header | body | footer | issues
  const [q, setQ] = React.useState("");
  const trees = filter === "all" ? V2S_TREE : V2S_TREE.filter(t => t.id === filter);

  return (
    <div className="v2-body">
      {/* Rail — view modes for the tree */}
      <div className="v2-rail v2s-rail">
        {[
          {id:"all",    icon:"layers",   label:"Tutto",    cnt: 3},
          {id:"header", icon:"layout",   label:"Header",   cnt: 1, tone:"info"},
          {id:"body",   icon:"square",   label:"Body",     cnt: 3, tone:"brand"},
          {id:"footer", icon:"layout",   label:"Footer",   cnt: 1, tone:"success"},
          {id:"issues", icon:"alert",    label:"Avvisi",   cnt: 2, tone:"warning"},
          {id:"global", icon:"sparks",   label:"Globali",  cnt: 4},
        ].map(v => (
          <button
            key={v.id}
            className={"v2-rail-btn " + (filter===v.id?"on":"") + (v.tone?(" tone-"+v.tone):"")}
            onClick={()=>setFilter(v.id)}
            title={v.label}
          >
            <span className="bar"/>
            <OLOIcon name={v.icon==="sparks"?"spark":v.icon} size={18}/>
            <span className="lbl">{v.label}</span>
            <span className="cnt">{v.cnt}</span>
          </button>
        ))}
        <div className="spc"/>
      </div>

      {/* Panel */}
      <div className="v2-panel v2s-panel">
        <div className="v2-panel-head">
          <span className="dot" style={{background:"var(--ot-primary)"}}/>
          <h3>Struttura pagina</h3>
          <span className="cnt">12 elementi</span>
        </div>

        <div className="v2-search">
          <OLOIcon name="search" size={14} style={{color:"var(--ot-text-muted)"}}/>
          <input value={q} onChange={e=>setQ(e.target.value)} placeholder="Cerca un blocco…"/>
          {q && <button className="ic" onClick={()=>setQ("")}><OLOIcon name="plus" size={12} style={{transform:"rotate(45deg)"}}/></button>}
        </div>

        <div className="v2s-toolbar">
          <button><OLOIcon name="chevDown" size={11} style={{transform:"rotate(-90deg)"}}/> Comprimi tutto</button>
          <button><OLOIcon name="chevDown" size={11}/> Espandi tutto</button>
          <span className="spc"/>
          <button title="Solo selezione"><OLOIcon name="eye" size={11}/></button>
        </div>

        <div className="v2s-tree scrolly">
          {trees.map(t => <V2SSection key={t.id} root={t} query={q}/>)}
        </div>

        {/* Breadcrumb of currently selected */}
        <div className="v2s-breadcrumb">
          <span className="lbl">Selezionato</span>
          <div className="path">
            <span>Body</span> <OLOIcon name="chev" size={9}/>
            <span>Sezione</span> <OLOIcon name="chev" size={9}/>
            <span>Row</span> <OLOIcon name="chev" size={9}/>
            <span>Column</span> <OLOIcon name="chev" size={9}/>
            <span className="cur"><OLOIcon name="image" size={11}/> Immagine</span>
          </div>
        </div>
      </div>
    </div>
  );
}

window.V2Struttura = V2Struttura;
