// V2 — Impostazioni elemento (right panel) with rail-menu pattern.
// Mirrors the left sidebar: vertical icon rail on the RIGHT edge,
// content panel on the left. Eliminates the "wall of accordions"
// of the source by promoting sections to first-class rail entries.

const V2I_PRIMARY = ["contenuto", "stile", "avanzate"];

const V2I_SECTIONS = {
  contenuto: [
    { id: "titolo",    label: "Titolo",      icon: "heading",  active: true },
    { id: "effetti",   label: "Effetti testo", icon: "spark" },
    { id: "aspetto",   label: "Aspetto",     icon: "sliders" },
    { id: "decoraz",   label: "Decorazione", icon: "shape" },
    { id: "sotto",     label: "Sottotitolo", icon: "text" },
    { id: "link",      label: "Link",        icon: "code" },
  ],
  stile: [
    { id: "tipo",      label: "Tipografia",  icon: "heading" },
    { id: "colori",    label: "Colori",      icon: "icon" },
    { id: "bordo",     label: "Bordo",       icon: "square" },
    { id: "ombra",     label: "Ombra",       icon: "layers" },
    { id: "sfondo",    label: "Sfondo",      icon: "image" },
    { id: "spazio",    label: "Spaziature",  icon: "spacer" },
  ],
  avanzate: [
    { id: "id",        label: "ID & Classi", icon: "code" },
    { id: "anim",      label: "Animazioni",  icon: "spark" },
    { id: "resp",      label: "Responsive",  icon: "device" },
    { id: "vis",       label: "Visibilità",  icon: "eye" },
    { id: "custom",    label: "CSS custom",  icon: "codeTag" },
  ],
};

function V2ISwatches() {
  const cols = ["#ef4444","#b91c1c","#f59e0b","#1f2937","#fff","#475569","#a3e635","#65a30d","#15803d","#0f766e","#f97316"];
  return (
    <div className="v2i-swatches">
      {cols.map((c,i)=>(
        <button key={i} className={i===3?"on":""} style={{background:c, borderColor: c==="#fff"?"#e2e8f0":"transparent"}}/>
      ))}
      <button className="add"><OLOIcon name="plus" size={11}/></button>
    </div>
  );
}

function V2ImpostazioniPanel({ section }) {
  // mostra controlli per la sezione attiva (mock per "titolo")
  return (
    <div className="v2i-content scrolly">
      <div className="v2i-block">
        <label>Titolo <OLOIcon name="zap" size={11} style={{color:"#f59e0b"}}/></label>
        <textarea defaultValue="Titolo sezione" rows={2}/>
      </div>
      <div className="v2i-block">
        <label>Sottotitolo <OLOIcon name="zap" size={11} style={{color:"#f59e0b"}}/></label>
        <textarea defaultValue="" rows={2} placeholder="Aggiungi sottotitolo…"/>
      </div>

      <div className="v2i-group">
        <div className="v2i-group-h">
          <span>Effetti testo</span>
          <button className="v2i-pill">Hover</button>
        </div>
        <div className="v2i-block">
          <label>Effetto</label>
          <div className="v2i-select"><span>Nessuno</span><OLOIcon name="chevDown" size={12}/></div>
        </div>
        <div className="v2i-block">
          <label>Carattere cursore <OLOIcon name="zap" size={11} style={{color:"#f59e0b"}}/></label>
          <input placeholder="es. |"/>
        </div>
      </div>

      <div className="v2i-group">
        <div className="v2i-group-h">
          <span>Decorazione</span>
        </div>
        <div className="v2i-block">
          <label>Tipo</label>
          <div className="v2i-select"><span>Linea</span><OLOIcon name="chevDown" size={12}/></div>
        </div>
        <div className="v2i-block">
          <label>Colore decorazione</label>
          <V2ISwatches/>
          <div className="v2i-row" style={{marginTop:8}}>
            <span className="v2i-color-prev"/>
            <input className="v2i-mono" defaultValue="#000000"/>
          </div>
          <div className="v2i-row" style={{marginTop:8, gap:10}}>
            <span className="v2i-cap">Alfa</span>
            <div className="v2i-slider"><span style={{width:"100%"}}/></div>
            <span className="v2i-num">100%</span>
          </div>
        </div>
      </div>

      <div className="v2i-group">
        <div className="v2i-group-h"><span>Bordo</span></div>
        <div className="v2i-block">
          <label>Stile</label>
          <div className="v2i-segment">
            <button className="on">Solid</button>
            <button>Dashed</button>
            <button>Dotted</button>
            <button>None</button>
          </div>
        </div>
        <div className="v2i-block">
          <label>Spessore</label>
          <div className="v2i-row" style={{gap:10}}>
            <div className="v2i-slider"><span style={{width:"30%"}}/></div>
            <input className="v2i-num-i" defaultValue="2" type="text"/>
            <span className="v2i-cap">px</span>
          </div>
        </div>
      </div>
    </div>
  );
}

function V2Impostazioni() {
  const [tab, setTab] = React.useState("contenuto");
  const [sec, setSec] = React.useState("titolo");
  const sections = V2I_SECTIONS[tab];

  return (
    <div className="v2i-side">
      {/* Top: breadcrumb + title bar */}
      <div className="v2i-head">
        <div className="v2i-crumb">
          <span className="v2i-chip body">BODY</span>
          <span>Sezione</span><OLOIcon name="chev" size={9}/>
          <span>Riga / Colonne</span><OLOIcon name="chev" size={9}/>
          <span>Colonna</span><OLOIcon name="chev" size={9}/>
          <span className="cur">Titolo</span>
        </div>
        <div className="v2i-title-row">
          <h3>Impostazioni Titolo</h3>
          <div className="v2i-actions">
            <button title="Duplica"><OLOIcon name="content" size={13}/></button>
            <button title="Incolla stile"><OLOIcon name="codeTag" size={13}/></button>
            <button title="Salva preset"><OLOIcon name="star" size={13}/></button>
            <button title="Chiudi"><OLOIcon name="plus" size={13} style={{transform:"rotate(45deg)"}}/></button>
          </div>
        </div>
        <div className="v2i-tabs">
          {V2I_PRIMARY.map(t => (
            <button key={t} className={tab===t?"on":""} onClick={()=>{setTab(t); setSec(V2I_SECTIONS[t][0].id);}}>
              {t==="contenuto"?"Contenuto":t==="stile"?"Stile":"Avanzate"}
            </button>
          ))}
        </div>
        <div className="v2i-search">
          <OLOIcon name="search" size={13} style={{color:"var(--ot-text-muted)"}}/>
          <input placeholder="Cerca impostazione…"/>
          <button title="Stato hover" className="hover-toggle"><OLOIcon name="reveal" size={13}/></button>
        </div>
      </div>

      {/* Body: panel + rail (rail on right edge, mirrors left sidebar) */}
      <div className="v2i-body">
        <V2ImpostazioniPanel section={sec}/>
        <div className="v2-rail v2i-rail">
          {sections.map(s => (
            <button
              key={s.id}
              className={"v2-rail-btn " + (sec===s.id?"on":"")}
              onClick={()=>setSec(s.id)}
              title={s.label}
            >
              <span className="bar"/>
              <OLOIcon name={s.icon} size={18}/>
              <span className="lbl">{s.label}</span>
              {s.active && <span className="cnt dot-only"/>}
            </button>
          ))}
          <div className="spc"/>
          <button className="v2-rail-btn add" title="Aggiungi sezione personalizzata">
            <span className="bar"/>
            <OLOIcon name="plus" size={16}/>
            <span className="lbl">Custom</span>
          </button>
        </div>
      </div>
    </div>
  );
}

window.V2Impostazioni = V2Impostazioni;
