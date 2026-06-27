// Marketing-grade simplified mockup of the OLObuild editor.
// Built fresh (not the actual app) so it stays pixel-crisp at any size
// and reads well even when zoomed for ad detail shots.

function BuilderMockup({ variant = "full", width = 1100, scale = 1, accent = "#e1474f" }) {
  // variant: "full" | "rail-zoom" | "right-zoom" | "templates" | "struttura"
  const baseStyle = {
    width,
    transform: `scale(${scale})`,
    transformOrigin: "top left",
    fontFamily: "var(--mk-sans)",
  };

  if (variant === "rail-zoom") {
    return <RailZoom width={width} accent={accent} scale={scale}/>;
  }
  if (variant === "right-zoom") {
    return <RightPanelZoom width={width} accent={accent} scale={scale}/>;
  }
  if (variant === "templates") {
    return <TemplatesZoom width={width} accent={accent} scale={scale}/>;
  }
  if (variant === "struttura") {
    return <StrutturaZoom width={width} accent={accent} scale={scale}/>;
  }

  return (
    <div style={baseStyle} className="bm-frame">
      <BrowserBar/>
      <div className="bm-body">
        <Sidebar accent={accent}/>
        <Canvas accent={accent}/>
        <RightPanel accent={accent}/>
      </div>
      <style>{`
        .bm-frame {
          background: #fff;
          border-radius: 14px;
          overflow: hidden;
          box-shadow: 0 0 0 1px rgba(255,255,255,.05) inset;
        }
        .bm-body {
          display: grid;
          grid-template-columns: 320px 1fr 280px;
          height: 540px;
        }
      `}</style>
    </div>
  );
}

function BrowserBar() {
  return (
    <div style={{
      display: "flex", alignItems: "center", gap: 8,
      padding: "10px 14px",
      borderBottom: "1px solid #e9ecef",
      background: "#fafbfc",
    }}>
      <span style={{width:10,height:10,borderRadius:99,background:"#ff5f57"}}/>
      <span style={{width:10,height:10,borderRadius:99,background:"#febc2e"}}/>
      <span style={{width:10,height:10,borderRadius:99,background:"#28c840"}}/>
      <div style={{
        flex: 1, marginLeft: 12, marginRight: 12,
        background: "#fff", border: "1px solid #e9ecef",
        borderRadius: 6, padding: "5px 10px",
        font: "11px/1 ui-monospace, Menlo, monospace",
        color: "#6b7280", display: "flex", alignItems: "center", gap: 8,
      }}>
        <span style={{color:"#10b981"}}>●</span>
        olobuild.it/editor
      </div>
    </div>
  );
}

function Sidebar({ accent }) {
  const cats = [
    { i: "▦", label: "Tutti",        n: 97, on: false },
    { i: "★", label: "Preferiti",    n: 8,  on: false, dot: "#f59e0b" },
    { i: "■", label: "Essenziale",   n: 9,  on: true,  dot: "#ef4444" },
    { i: "▤", label: "Layout",       n: 7,  on: false, dot: "#3b82f6" },
    { i: "T", label: "Testo",        n: 10, on: false, dot: "#22c55e" },
    { i: "◑", label: "Media",        n: 20, on: false, dot: "#a855f7" },
    { i: "≡", label: "Form",         n: 8,  on: false, dot: "#0ea5e9" },
    { i: "♥", label: "Marketing",    n: 19, on: false, dot: "#ec4899" },
  ];
  const items = [
    { lbl: "Contenuto", ic: "▤" },
    { lbl: "Immagine",  ic: "◖" },
    { lbl: "Pulsante",  ic: "◉" },
    { lbl: "Titolo",    ic: "T" },
    { lbl: "Video",     ic: "▶" },
    { lbl: "Divisore",  ic: "—" },
  ];
  return (
    <div style={{display:"grid",gridTemplateColumns:"64px 1fr",borderRight:"1px solid #e9ecef",background:"#fff"}}>
      {/* Rail */}
      <div style={{background:"#f8f9fa",borderRight:"1px solid #eef0f3",padding:"6px 0"}}>
        {cats.map((c, i) => (
          <div key={i} style={{
            height: 56, display:"flex", flexDirection:"column",
            alignItems:"center", justifyContent:"center", gap:3,
            position:"relative",
            background: c.on ? "#fff" : "transparent",
            color: c.on ? "#1e293b" : "#64748b",
          }}>
            {c.on && <div style={{position:"absolute",left:0,top:8,bottom:8,width:2,background:accent,borderRadius:"0 2px 2px 0"}}/>}
            <div style={{
              width:24,height:24,borderRadius:6,display:"grid",placeItems:"center",
              background: c.on ? "rgba(225,71,79,.08)" : "transparent",
              fontSize:13,fontWeight:600,
            }}>{c.i}</div>
            <div style={{fontSize:9,fontWeight:500,lineHeight:1}}>{c.label}</div>
            <div style={{
              position:"absolute",top:6,right:8,fontSize:8,fontWeight:700,
              padding:"1px 4px",borderRadius:99,
              background: c.on ? "rgba(225,71,79,.15)" : "#e9ecef",
              color: c.on ? "#b8323a" : "#64748b",
              minWidth:12, textAlign:"center",
            }}>{c.n}</div>
          </div>
        ))}
      </div>
      {/* Card grid */}
      <div style={{padding:14,display:"flex",flexDirection:"column",gap:10}}>
        <div style={{display:"flex",alignItems:"center",gap:6,marginBottom:4}}>
          <span style={{width:8,height:8,borderRadius:99,background:"#ef4444"}}/>
          <span style={{fontSize:13,fontWeight:700,color:"#1e293b"}}>Essenziale</span>
          <span style={{fontSize:10,fontWeight:600,padding:"1px 6px",borderRadius:99,background:"#f1f5f9",color:"#64748b",marginLeft:"auto"}}>9</span>
        </div>
        <div style={{display:"flex",alignItems:"center",gap:6,padding:"5px 8px",background:"#f8f9fa",border:"1px solid #eef0f3",borderRadius:8}}>
          <span style={{fontSize:12,color:"#94a3b8"}}>⌕</span>
          <span style={{fontSize:11,color:"#94a3b8"}}>Cerca…</span>
        </div>
        <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:6}}>
          {items.map((it,i)=>(
            <div key={i} style={{
              padding:"10px 10px",
              border:"1px solid #e9ecef",
              borderRadius:8,background:"#fff",
              display:"flex",flexDirection:"column",gap:6,
            }}>
              <div style={{
                width:30,height:30,borderRadius:6,background:"#f1f5f9",
                display:"grid",placeItems:"center",fontSize:14,color:"#475569"
              }}>{it.ic}</div>
              <div style={{fontSize:11,fontWeight:500,color:"#1e293b"}}>{it.lbl}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function Canvas({ accent }) {
  return (
    <div style={{background:"#f3f4f6",padding:18,position:"relative"}}>
      <div style={{
        background:"#fff",borderRadius:8,height:"100%",
        boxShadow:"0 4px 12px rgba(0,0,0,.04)",overflow:"hidden",
        border:"1px solid #e9ecef",
      }}>
        {/* Hero band */}
        <div style={{
          height:160,
          background: `linear-gradient(135deg, ${accent} 0%, #7a1d23 100%)`,
          padding:20,color:"#fff",position:"relative",
        }}>
          <div style={{fontSize:10,fontWeight:600,opacity:.7,textTransform:"uppercase",letterSpacing:".1em",marginBottom:6}}>Hero section</div>
          <div style={{fontSize:22,fontWeight:700,marginBottom:4,maxWidth:"60%"}}>Benvenuto al Resort delle Ville</div>
          <div style={{fontSize:11,opacity:.85,maxWidth:"50%"}}>Una struttura immersa nel verde, a 10 minuti dal mare.</div>
          {/* Selection dashes */}
          <div style={{position:"absolute",inset:6,border:`1.5px dashed ${accent}`,borderRadius:6,opacity:.5,pointerEvents:"none"}}/>
          <div style={{position:"absolute",top:-7,left:14,background:accent,color:"#fff",fontSize:9,fontWeight:700,padding:"2px 7px",borderRadius:4,letterSpacing:".06em"}}>HERO</div>
        </div>
        <div style={{padding:18,display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:10}}>
          {[0,1,2].map(i=>(
            <div key={i} style={{
              border:"1px dashed #d1d5db",borderRadius:8,padding:14,minHeight:90,
              display:"flex",flexDirection:"column",gap:6,
            }}>
              <div style={{height:8,width:"55%",background:"#f1f5f9",borderRadius:3}}/>
              <div style={{height:5,background:"#f1f5f9",borderRadius:2}}/>
              <div style={{height:5,width:"75%",background:"#f1f5f9",borderRadius:2}}/>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function RightPanel({ accent }) {
  const sections = [
    { ic:"T", lbl:"Titolo",     on:true },
    { ic:"✦", lbl:"Effetti",    on:false },
    { ic:"◧", lbl:"Aspetto",    on:false },
    { ic:"◌", lbl:"Decorazione",on:false },
    { ic:"≡", lbl:"Sottotitolo",on:false },
    { ic:"⌬", lbl:"Link",       on:false },
  ];
  return (
    <div style={{display:"grid",gridTemplateColumns:"1fr 56px",borderLeft:"1px solid #e9ecef",background:"#fff"}}>
      <div style={{padding:14,display:"flex",flexDirection:"column",gap:12,overflow:"hidden"}}>
        <div style={{display:"flex",alignItems:"center",gap:6,fontSize:10,color:"#64748b"}}>
          <span style={{background:"#faf5ff",color:"#7e22ce",padding:"2px 6px",borderRadius:4,fontSize:9,fontWeight:700}}>BODY</span>
          <span>Sezione › Row › Column ›</span>
          <span style={{color:"#1e293b",fontWeight:600}}>Titolo</span>
        </div>
        <div style={{fontSize:14,fontWeight:700,color:"#1e293b"}}>Impostazioni Titolo</div>
        <div style={{display:"flex",background:"#fff",border:"1px solid #e9ecef",borderRadius:8,padding:2}}>
          <div style={{flex:1,padding:"6px 0",textAlign:"center",fontSize:11,fontWeight:600,background:accent,color:"#fff",borderRadius:6}}>Contenuto</div>
          <div style={{flex:1,padding:"6px 0",textAlign:"center",fontSize:11,color:"#64748b"}}>Stile</div>
          <div style={{flex:1,padding:"6px 0",textAlign:"center",fontSize:11,color:"#64748b"}}>Avanzate</div>
        </div>
        <div>
          <div style={{fontSize:10,color:"#64748b",marginBottom:4}}>Titolo</div>
          <div style={{padding:8,border:"1px solid #e9ecef",borderRadius:6,fontSize:11,color:"#1e293b"}}>Benvenuto al Resort delle Ville</div>
        </div>
        <div style={{background:"#f8f9fa",borderRadius:8,padding:12,display:"flex",flexDirection:"column",gap:8}}>
          <div style={{fontSize:9,fontWeight:700,color:"#64748b",letterSpacing:".06em",textTransform:"uppercase"}}>Decorazione</div>
          <div style={{display:"flex",gap:4}}>
            {["#ef4444","#1f2937","#fff","#f59e0b","#22c55e","#0ea5e9","#a855f7"].map(c=>(
              <div key={c} style={{width:18,height:18,borderRadius:5,background:c,border:c==="#fff"?"1px solid #e9ecef":"none",boxShadow:c===accent?`0 0 0 2px ${accent}`:"none"}}/>
            ))}
          </div>
          <div style={{display:"flex",alignItems:"center",gap:8}}>
            <div style={{flex:1,height:5,background:"#e9ecef",borderRadius:99,position:"relative"}}>
              <div style={{position:"absolute",left:0,top:0,bottom:0,width:"70%",background:`linear-gradient(90deg, ${accent}33, ${accent})`,borderRadius:99}}/>
            </div>
            <span style={{fontSize:10,color:"#64748b",fontVariantNumeric:"tabular-nums"}}>70%</span>
          </div>
        </div>
      </div>
      {/* Right rail mirror */}
      <div style={{background:"#f8f9fa",borderLeft:"1px solid #eef0f3",padding:"6px 0"}}>
        {sections.map((s,i)=>(
          <div key={i} style={{
            height: 56, display:"flex", flexDirection:"column",
            alignItems:"center", justifyContent:"center", gap:3,
            position:"relative",
            background: s.on ? "#fff" : "transparent",
            color: s.on ? "#1e293b" : "#64748b",
          }}>
            {s.on && <div style={{position:"absolute",right:0,top:8,bottom:8,width:2,background:accent,borderRadius:"2px 0 0 2px"}}/>}
            <div style={{
              width:24,height:24,borderRadius:6,display:"grid",placeItems:"center",
              fontSize:13,fontWeight:600,
            }}>{s.ic}</div>
            <div style={{fontSize:9,fontWeight:500,lineHeight:1}}>{s.lbl}</div>
          </div>
        ))}
      </div>
    </div>
  );
}

/* ──── Detail-zoom variants for ads ──── */

function RailZoom({ width, accent, scale }) {
  const cats = [
    { i:"★", label:"Preferiti",  n:8,  on:false },
    { i:"■", label:"Essenziale", n:9,  on:true },
    { i:"▤", label:"Layout",     n:7,  on:false },
    { i:"T", label:"Testo",      n:10, on:false },
    { i:"◑", label:"Media",      n:20, on:false },
    { i:"♥", label:"Marketing",  n:19, on:false },
  ];
  return (
    <div style={{width,transform:`scale(${scale})`,transformOrigin:"top left",
      background:"#fff",borderRadius:18,overflow:"hidden",
      display:"grid",gridTemplateColumns:"96px 1fr",
      boxShadow:"0 30px 80px rgba(0,0,0,.4)",height:540}}>
      <div style={{background:"#f8f9fa",borderRight:"1px solid #eef0f3",padding:"10px 0"}}>
        {cats.map((c,i)=>(
          <div key={i} style={{
            height:88,display:"flex",flexDirection:"column",alignItems:"center",justifyContent:"center",gap:6,
            position:"relative",background:c.on?"#fff":"transparent",
            color:c.on?"#1e293b":"#64748b",
          }}>
            {c.on && <div style={{position:"absolute",left:0,top:12,bottom:12,width:3,background:accent,borderRadius:"0 3px 3px 0"}}/>}
            <div style={{
              width:36,height:36,borderRadius:9,display:"grid",placeItems:"center",
              background:c.on?"rgba(225,71,79,.1)":"transparent",
              fontSize:18,fontWeight:600,
            }}>{c.i}</div>
            <div style={{fontSize:11,fontWeight:500}}>{c.label}</div>
            <div style={{
              position:"absolute",top:10,right:14,fontSize:10,fontWeight:700,
              padding:"2px 6px",borderRadius:99,
              background:c.on?"rgba(225,71,79,.15)":"#e9ecef",
              color:c.on?"#b8323a":"#64748b",
            }}>{c.n}</div>
          </div>
        ))}
      </div>
      <div style={{padding:24,display:"flex",flexDirection:"column",gap:14}}>
        <div style={{display:"flex",alignItems:"center",gap:8}}>
          <span style={{width:10,height:10,borderRadius:99,background:"#ef4444"}}/>
          <span style={{fontSize:18,fontWeight:700,color:"#1e293b"}}>Essenziale</span>
          <span style={{fontSize:11,fontWeight:600,padding:"2px 8px",borderRadius:99,background:"#f1f5f9",color:"#64748b",marginLeft:"auto"}}>9 elementi</span>
        </div>
        <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:10}}>
          {[
            {ic:"▤",l:"Contenuto"},{ic:"◖",l:"Immagine"},
            {ic:"▶",l:"Video"},{ic:"—",l:"Spaziatore"},
            {ic:"◉",l:"Pulsante"},{ic:"T",l:"Titolo"},
            {ic:"≡",l:"Testo"},{ic:"⌅",l:"Divisore"},
          ].map((e,i)=>(
            <div key={i} style={{
              padding:14,border:i===4?`1.5px solid ${accent}`:"1px solid #e9ecef",
              borderRadius:10,background:i===4?"rgba(225,71,79,.04)":"#fff",
              display:"flex",flexDirection:"column",gap:8,
              boxShadow:i===4?"0 4px 12px rgba(225,71,79,.12)":"none",
              transform:i===4?"translateY(-2px)":"none",
            }}>
              <div style={{
                width:38,height:38,borderRadius:8,display:"grid",placeItems:"center",
                background:i===4?"#fff":"#f1f5f9",
                color:i===4?accent:"#475569",
                fontSize:18,fontWeight:600,
                boxShadow:i===4?"0 1px 2px rgba(0,0,0,.05)":"none",
              }}>{e.ic}</div>
              <div style={{fontSize:12,fontWeight:500,color:"#1e293b"}}>{e.l}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function RightPanelZoom({ width, accent, scale }) {
  return (
    <div style={{width,transform:`scale(${scale})`,transformOrigin:"top left",
      background:"#fff",borderRadius:18,overflow:"hidden",
      boxShadow:"0 30px 80px rgba(0,0,0,.4)",height:540,
      display:"grid",gridTemplateColumns:"1fr 64px"}}>
      <div style={{padding:22,display:"flex",flexDirection:"column",gap:16}}>
        <div style={{display:"flex",alignItems:"center",gap:8,fontSize:11,color:"#64748b"}}>
          <span style={{background:"#faf5ff",color:"#7e22ce",padding:"3px 8px",borderRadius:5,fontSize:10,fontWeight:700}}>BODY</span>
          Sezione › Row › Column › <span style={{color:"#1e293b",fontWeight:600}}>Titolo</span>
        </div>
        <div style={{fontSize:20,fontWeight:700,color:"#1e293b"}}>Impostazioni Titolo</div>
        <div style={{display:"flex",background:"#fff",border:"1px solid #e9ecef",borderRadius:10,padding:3}}>
          <div style={{flex:1,padding:"9px 0",textAlign:"center",fontSize:13,fontWeight:600,background:accent,color:"#fff",borderRadius:8}}>Contenuto</div>
          <div style={{flex:1,padding:"9px 0",textAlign:"center",fontSize:13,color:"#64748b"}}>Stile</div>
          <div style={{flex:1,padding:"9px 0",textAlign:"center",fontSize:13,color:"#64748b"}}>Avanzate</div>
        </div>
        <div>
          <div style={{fontSize:11,color:"#64748b",marginBottom:6}}>Titolo</div>
          <div style={{padding:12,border:"1px solid #e9ecef",borderRadius:8,fontSize:13,color:"#1e293b"}}>Benvenuto al Resort delle Ville</div>
        </div>
        <div style={{background:"#f8f9fa",borderRadius:10,padding:16,display:"flex",flexDirection:"column",gap:12}}>
          <div style={{fontSize:10,fontWeight:700,color:"#64748b",letterSpacing:".08em",textTransform:"uppercase"}}>Decorazione</div>
          <div>
            <div style={{fontSize:11,color:"#64748b",marginBottom:6}}>Colore</div>
            <div style={{display:"flex",gap:6}}>
              {["#ef4444","#1f2937","#fff","#f59e0b","#22c55e","#0ea5e9","#a855f7","#ec4899"].map(c=>(
                <div key={c} style={{width:24,height:24,borderRadius:6,background:c,border:c==="#fff"?"1px solid #e9ecef":"none",boxShadow:c===accent?`0 0 0 2px ${accent}`:"none"}}/>
              ))}
            </div>
          </div>
          <div>
            <div style={{fontSize:11,color:"#64748b",marginBottom:6}}>Trasparenza</div>
            <div style={{display:"flex",alignItems:"center",gap:10}}>
              <div style={{flex:1,height:6,background:"#e9ecef",borderRadius:99,position:"relative"}}>
                <div style={{position:"absolute",left:0,top:0,bottom:0,width:"70%",background:`linear-gradient(90deg, ${accent}44, ${accent})`,borderRadius:99}}/>
                <div style={{position:"absolute",left:"70%",top:-4,width:14,height:14,borderRadius:99,background:"#fff",boxShadow:"0 1px 4px rgba(0,0,0,.2)",transform:"translateX(-50%)"}}/>
              </div>
              <span style={{fontSize:12,color:"#1e293b",fontVariantNumeric:"tabular-nums",fontWeight:600}}>70%</span>
            </div>
          </div>
        </div>
      </div>
      <div style={{background:"#f8f9fa",borderLeft:"1px solid #eef0f3",padding:"8px 0"}}>
        {[
          {ic:"T",l:"Titolo",on:true},
          {ic:"✦",l:"Effetti"},
          {ic:"◧",l:"Aspetto"},
          {ic:"◌",l:"Decoraz."},
          {ic:"≡",l:"Sotto."},
          {ic:"⌬",l:"Link"},
        ].map((s,i)=>(
          <div key={i} style={{
            height:68,display:"flex",flexDirection:"column",alignItems:"center",justifyContent:"center",gap:4,
            position:"relative",background:s.on?"#fff":"transparent",
            color:s.on?"#1e293b":"#64748b",
          }}>
            {s.on && <div style={{position:"absolute",right:0,top:10,bottom:10,width:3,background:accent,borderRadius:"3px 0 0 3px"}}/>}
            <div style={{width:28,height:28,borderRadius:7,display:"grid",placeItems:"center",fontSize:14,fontWeight:600,background:s.on?"rgba(225,71,79,.08)":"transparent"}}>{s.ic}</div>
            <div style={{fontSize:10,fontWeight:500}}>{s.l}</div>
          </div>
        ))}
      </div>
    </div>
  );
}

function TemplatesZoom({ width, accent, scale }) {
  const tpls = [
    { t:"Header — Hotel Resort", type:"HEADER", color:"#3b82f6", soft:"#dbeafe", attivo:true,
      preview:<div style={{padding:0,height:"100%"}}>
        <div style={{height:"36%",background:"#1d2327",display:"flex",alignItems:"center",padding:"0 14px",gap:10}}>
          <div style={{width:32,height:10,background:"rgba(255,255,255,.5)",borderRadius:2}}/>
          <div style={{flex:1}}/>
          {[1,2,3].map(i=><div key={i} style={{width:40,height:7,background:"rgba(255,255,255,.3)",borderRadius:2}}/>)}
        </div>
        <div style={{padding:14,display:"flex",flexDirection:"column",gap:8}}>
          <div style={{height:8,width:"55%",background:"rgba(15,17,21,.1)",borderRadius:3}}/>
          <div style={{height:5,width:"40%",background:"rgba(15,17,21,.08)",borderRadius:2}}/>
        </div>
      </div>
    },
    { t:"Footer — Standard", type:"FOOTER", color:"#64748b", soft:"#e2e8f0", attivo:true,
      preview:<div style={{padding:0,height:"100%",display:"flex",flexDirection:"column"}}>
        <div style={{flex:1,padding:14,display:"flex",flexDirection:"column",gap:8}}>
          <div style={{height:7,width:"50%",background:"rgba(15,17,21,.1)",borderRadius:3}}/>
          <div style={{height:5,width:"35%",background:"rgba(15,17,21,.08)",borderRadius:2}}/>
        </div>
        <div style={{height:"40%",background:"#1d2327",padding:"12px 14px",display:"grid",gridTemplateColumns:"repeat(4,1fr)",gap:8,alignContent:"center"}}>
          {[0,1,2,3].map(i => (
            <div key={i} style={{display:"flex",flexDirection:"column",gap:4}}>
              <div style={{height:6,background:"rgba(255,255,255,.5)",borderRadius:2,width:"65%"}}/>
              <div style={{height:3,background:"rgba(255,255,255,.25)",borderRadius:2}}/>
              <div style={{height:3,background:"rgba(255,255,255,.25)",borderRadius:2,width:"70%"}}/>
            </div>
          ))}
        </div>
      </div>
    },
    { t:"Pagina servizi", type:"PAGINA", color:accent, soft:"#fde2e4",
      preview:<div style={{padding:14,height:"100%",display:"flex",flexDirection:"column",gap:8}}>
        <div style={{flex:"1.5",borderRadius:6,background:`linear-gradient(135deg, ${accent}44, ${accent}11)`,padding:10,display:"flex",flexDirection:"column",justifyContent:"flex-end",gap:4}}>
          <div style={{height:8,width:"60%",background:"rgba(15,17,21,.5)",borderRadius:3}}/>
          <div style={{height:5,width:"40%",background:"rgba(15,17,21,.2)",borderRadius:2}}/>
        </div>
        <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:6}}>
          {[0,1,2].map(i=><div key={i} style={{height:32,background:"rgba(15,17,21,.06)",borderRadius:4}}/>)}
        </div>
      </div>
    },
    { t:"Mega Menu", type:"MEGA", color:"#f59e0b", soft:"#fef3c7",
      preview:<div style={{padding:14,height:"100%"}}>
        <div style={{display:"grid",gridTemplateColumns:"repeat(4,1fr)",gap:6,height:"100%"}}>
          {Array.from({length:8}).map((_,i)=><div key={i} style={{background:"rgba(15,17,21,.07)",borderRadius:4}}/>)}
        </div>
      </div>
    },
  ];
  return (
    <div style={{width,transform:`scale(${scale})`,transformOrigin:"top left",
      background:"#fff",borderRadius:18,overflow:"hidden",
      boxShadow:"0 30px 80px rgba(0,0,0,.4)",padding:24,height:540}}>
      <div style={{display:"flex",alignItems:"baseline",gap:12,marginBottom:14}}>
        <div style={{fontSize:22,fontWeight:700,color:"#1e293b"}}>Gestione Template</div>
        <div style={{fontSize:13,color:"#64748b"}}>128 totali · 3 attivi</div>
        <div style={{flex:1}}/>
        <div style={{display:"inline-flex",alignItems:"center",gap:8,padding:"8px 16px",borderRadius:8,background:accent,color:"#fff",fontSize:13,fontWeight:600}}>+ Nuovo Template</div>
      </div>
      <div style={{display:"flex",gap:6,marginBottom:16}}>
        {[
          {l:"Tutti",n:128,on:true},
          {l:"Pagine",n:47},{l:"Header",n:5},{l:"Footer",n:4},{l:"Mega",n:6},
        ].map((c,i)=>(
          <div key={i} style={{padding:"5px 11px",borderRadius:7,fontSize:12,fontWeight:500,
            background:c.on?"#1e293b":"transparent",color:c.on?"#fff":"#64748b",
            display:"inline-flex",alignItems:"center",gap:6,
          }}>{c.l} <span style={{fontSize:10,padding:"1px 5px",borderRadius:99,background:c.on?"rgba(255,255,255,.18)":"rgba(0,0,0,.06)"}}>{c.n}</span></div>
        ))}
      </div>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr 1fr",gap:14}}>
        {tpls.map((t,i)=>(
          <div key={i} style={{
            background:"#fff",border:"1px solid #eef0f3",borderRadius:12,overflow:"hidden",
            boxShadow:i===2?"0 12px 30px rgba(225,71,79,.18)":"0 2px 6px rgba(0,0,0,.04)",
            transform:i===2?"translateY(-4px)":"none",
            borderColor:i===2?accent:"#eef0f3",
          }}>
            <div style={{aspectRatio:"16/10",position:"relative",background:`linear-gradient(135deg, ${t.soft}, #fff 70%)`,borderBottom:"1px solid #eef0f3"}}>
              {t.preview}
              <div style={{position:"absolute",top:10,left:10,fontSize:9,fontWeight:700,padding:"3px 8px",borderRadius:99,background:t.color,color:"#fff",letterSpacing:".05em"}}>{t.type}</div>
              {t.attivo && <div style={{position:"absolute",top:10,right:10,fontSize:9,fontWeight:700,padding:"3px 8px",borderRadius:99,background:"#22c55e",color:"#fff",letterSpacing:".05em"}}>ATTIVO</div>}
            </div>
            <div style={{padding:"12px 14px"}}>
              <div style={{fontSize:13,fontWeight:600,color:"#1e293b",whiteSpace:"nowrap",overflow:"hidden",textOverflow:"ellipsis"}}>{t.t}</div>
              <div style={{fontSize:10,color:"#64748b",marginTop:4,fontFamily:"ui-monospace,monospace"}}>[olo_template id="{179-i}"]</div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function StrutturaZoom({ width, accent, scale }) {
  const Row = ({ depth, lbl, ic, sel, type }) => (
    <div style={{display:"flex",alignItems:"center",gap:8,padding:"7px 8px",paddingLeft:8+depth*18,
      borderRadius:6,margin:"2px 6px",background:sel?"rgba(225,71,79,.08)":"transparent",
      boxShadow:sel?`inset 3px 0 0 ${accent}`:"none",
      color:sel?"#b8323a":"#1e293b",fontWeight:sel?600:500,fontSize:12,
    }}>
      <span style={{color:"#cbd5e1",fontSize:10}}>⋮⋮</span>
      <span style={{color:sel?accent:"#94a3b8",fontSize:10}}>▾</span>
      <span style={{color:sel?accent:"#64748b",fontSize:13}}>{ic}</span>
      <span style={{flex:1}}>{lbl}</span>
      {sel && <span style={{fontSize:8,fontWeight:700,padding:"2px 6px",borderRadius:99,background:"rgba(225,71,79,.15)",color:"#b8323a"}}>SEL.</span>}
    </div>
  );
  const SectionHeader = ({ tone, label, n }) => {
    const tones = {
      header: { bg:"#eff6ff", c:"#1d4ed8", bar:"#3b82f6" },
      body:   { bg:"#faf5ff", c:"#7e22ce", bar:"#a855f7" },
      footer: { bg:"#f0fdf4", c:"#15803d", bar:"#22c55e" },
    }[tone];
    return (
      <div style={{background:tones.bg,color:tones.c,padding:"10px 12px 10px 16px",
        display:"flex",alignItems:"center",gap:8,fontSize:12,fontWeight:700,
        position:"relative",borderRadius:8,margin:"6px 0",
      }}>
        <div style={{position:"absolute",left:0,top:4,bottom:4,width:3,background:tones.bar,borderRadius:"0 3px 3px 0"}}/>
        <span style={{fontSize:10}}>▾</span>
        <span style={{fontSize:13}}>⌬</span>
        <span>{label}</span>
        <span style={{marginLeft:"auto",fontSize:10,fontWeight:500,padding:"2px 7px",borderRadius:99,background:"rgba(255,255,255,.6)"}}>{n} sezioni</span>
      </div>
    );
  };
  return (
    <div style={{width,transform:`scale(${scale})`,transformOrigin:"top left",
      background:"#fff",borderRadius:18,overflow:"hidden",
      boxShadow:"0 30px 80px rgba(0,0,0,.4)",height:540,display:"grid",gridTemplateColumns:"96px 1fr"}}>
      <div style={{background:"#f8f9fa",borderRight:"1px solid #eef0f3",padding:"10px 0"}}>
        {[
          {ic:"◫",l:"Tutto"},
          {ic:"▣",l:"Header",tone:"#3b82f6"},
          {ic:"■",l:"Body",tone:accent,on:true},
          {ic:"▤",l:"Footer",tone:"#22c55e"},
          {ic:"⚠",l:"Avvisi",tone:"#f59e0b"},
        ].map((v,i)=>(
          <div key={i} style={{
            height:80,display:"flex",flexDirection:"column",alignItems:"center",justifyContent:"center",gap:5,
            position:"relative",background:v.on?"#fff":"transparent",
          }}>
            {v.on && <div style={{position:"absolute",left:0,top:10,bottom:10,width:3,background:accent,borderRadius:"0 3px 3px 0"}}/>}
            <div style={{width:34,height:34,borderRadius:8,display:"grid",placeItems:"center",fontSize:16,background:v.on?"rgba(225,71,79,.08)":"transparent",color:v.on?accent:"#64748b"}}>{v.ic}</div>
            <div style={{fontSize:10,fontWeight:500,color:v.on?"#1e293b":"#64748b"}}>{v.l}</div>
          </div>
        ))}
      </div>
      <div style={{padding:14,overflow:"hidden"}}>
        <div style={{display:"flex",alignItems:"center",gap:8,marginBottom:8}}>
          <span style={{width:8,height:8,borderRadius:99,background:accent}}/>
          <span style={{fontSize:14,fontWeight:700,color:"#1e293b"}}>Struttura pagina</span>
          <span style={{marginLeft:"auto",fontSize:11,fontWeight:600,padding:"2px 8px",borderRadius:99,background:"#f1f5f9",color:"#64748b"}}>12 elementi</span>
        </div>
        <SectionHeader tone="header" label="Header" n={1}/>
        <Row depth={0} lbl="Sezione" ic="▦"/>
        <Row depth={1} lbl="Row" ic="▤"/>
        <Row depth={2} lbl="Column" ic="▥"/>
        <Row depth={3} lbl="Mega Menu" ic="≡"/>
        <SectionHeader tone="body" label="Body" n={3}/>
        <Row depth={0} lbl="Sezione" ic="▦"/>
        <Row depth={1} lbl="Row" ic="▤"/>
        <Row depth={2} lbl="Column" ic="▥"/>
        <Row depth={3} lbl="Immagine" ic="◖" sel/>
        <Row depth={3} lbl="Titolo sezione" ic="T"/>
        <SectionHeader tone="footer" label="Footer" n={1}/>
        <Row depth={0} lbl="Sezione" ic="▦"/>
      </div>
    </div>
  );
}

window.BuilderMockup = BuilderMockup;
