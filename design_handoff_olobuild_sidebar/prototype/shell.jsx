// Shared builder shell — wraps each variant so the sidebar sits in real context.
function BuilderShell({ children, sidebarWidth = 320, theme = "light", label }) {
  const [dragOver, setDragOver] = React.useState(false);
  return (
    <div
      className={"bui-frame theme-" + theme}
      style={{ "--bui-sidebar-w": sidebarWidth + "px" }}
    >
      <div className="bui-topbar">
        <img src="assets/olobuild-horizontal.png" alt="OLObuild" className="logo-img"/>
        <div className="crumb">Pagine / <b>Home — Hotel Resort</b></div>
        <div className="spacer" />
        <div className="device-pills">
          <button className="active" title="Desktop"><OLOIcon name="desktop" size={14}/></button>
          <button title="Tablet"><OLOIcon name="tablet" size={14}/></button>
          <button title="Mobile"><OLOIcon name="device" size={14}/></button>
        </div>
        <button className="ghost" title="Annulla"><OLOIcon name="undo" size={14}/></button>
        <button className="ghost" title="Anteprima"><OLOIcon name="eye" size={14}/></button>
        <button>Bozza</button>
        <button className="primary">Pubblica</button>
      </div>

      {children /* sidebar */}

      <div className="bui-canvas">
        <div
          className={"bui-page" + (dragOver ? " drag-over" : "")}
          onDragOver={e => { e.preventDefault(); setDragOver(true); }}
          onDragLeave={() => setDragOver(false)}
          onDrop={() => setDragOver(false)}
        >
          <div className="hero">
            <div>
              <h1>Benvenuto al Resort delle Ville</h1>
              <p>Una struttura immersa nel verde, a 10 minuti dal mare.</p>
            </div>
          </div>
          <div className="row">
            <div className="card"><div className="ph-h"/><div className="ph-l"/><div className="ph-l" style={{width:"75%"}}/></div>
            <div className="card"><div className="ph-h"/><div className="ph-l"/><div className="ph-l" style={{width:"60%"}}/></div>
            <div className="card"><div className="ph-h"/><div className="ph-l"/><div className="ph-l" style={{width:"82%"}}/></div>
          </div>
          <div className="drop-hint"><OLOIcon name="plus" size={12}/> Rilascia per inserire</div>
          {label ? <div style={{position:"absolute", left:14, bottom:14, fontSize:10, color:"var(--ot-text-muted)", textTransform:"uppercase", letterSpacing:".08em", fontWeight:600}}>{label}</div> : null}
        </div>
      </div>

      <div className="bui-right scrolly">
        <h4>Modifica · Sezione Hero</h4>
        <div className="field"><label>Sfondo</label>
          <div className="swatches">
            <span style={{background:"#0f172a"}}/>
            <span style={{background:"linear-gradient(135deg,#6a2810,#e8622a)", boxShadow:"0 0 0 2px var(--ot-primary)"}}/>
            <span style={{background:"#fff", border:"1px solid var(--ot-border)"}}/>
            <span style={{background:"#f59e0b"}}/>
          </div>
        </div>
        <div className="field"><label>Padding verticale</label>
          <input defaultValue="80px"/>
        </div>
        <div className="field"><label>Allineamento</label>
          <select defaultValue="left">
            <option value="left">A sinistra</option>
            <option value="center">Centrato</option>
            <option value="right">A destra</option>
          </select>
        </div>
        <div className="field"><label>Animazione ingresso</label>
          <select defaultValue="fade"><option>Fade up</option><option>Slide</option><option>Zoom</option></select>
        </div>
        <h4 style={{marginTop:18}}>Avanzate</h4>
        <div className="field"><label>ID CSS</label><input defaultValue="hero-home"/></div>
        <div className="field"><label>Classi</label><input defaultValue="dark gradient"/></div>
      </div>
    </div>
  );
}

// Drag-from-sidebar util: sets dataTransfer + a tiny image
function startElementDrag(e, label) {
  try { e.dataTransfer.setData("text/plain", label); } catch {}
  const img = document.createElement("div");
  img.textContent = label;
  img.style.cssText = "position:absolute;top:-1000px;padding:6px 10px;background:#fff;border:1px solid #e8622a;border-radius:8px;font:500 12px Work Sans;color:#1e293b;box-shadow:0 8px 20px rgba(232,98,42,.25)";
  document.body.appendChild(img);
  e.dataTransfer.setDragImage(img, 10, 10);
  setTimeout(() => img.remove(), 0);
}

window.BuilderShell = BuilderShell;
window.startElementDrag = startElementDrag;
