// OLObuild — Border Radius: analisi controllo attuale vs redesign compatto

const RIcon = {
  ChevUp: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m6 15 6-6 6 6"/></svg>,
  ChevDown: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m6 9 6 6 6-6"/></svg>,
  Eye: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>,
  EyeOff: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10.8 10.8 0 0 1 12 6c6.5 0 10 6 10 6a17.6 17.6 0 0 1-3.36 3.96M6.6 6.6A17.6 17.6 0 0 0 2 12s3.5 6 10 6a10.8 10.8 0 0 0 3.4-.55"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>,
  Link: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1.5-1.5"/></svg>,
  Unlink: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m18.84 12.25 1.72-1.71a5 5 0 0 0-7.07-7.07l-1.71 1.71"/><path d="m5.17 11.75-1.71 1.71a5 5 0 0 0 7.07 7.07l1.71-1.71"/><path d="M8 2v3M2 8h3M16 22v-3M22 16h-3"/></svg>,
  Corners: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 9V6a2 2 0 0 1 2-2h3"/><path d="M15 4h3a2 2 0 0 1 2 2v3"/><path d="M20 15v3a2 2 0 0 1-2 2h-3"/><path d="M9 20H6a2 2 0 0 1-2-2v-3"/></svg>,
  X: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>,
  Check: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12l5 5L20 7"/></svg>,
  CornerTL: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 20v-7a9 9 0 0 0-9-9H4"/></svg>,
};

// ════════════════════════════════════════════════════════════════
// CURRENT — faithful recreation
// ════════════════════════════════════════════════════════════════
function CurrentControl() {
  return (
    <div className="rad-panel" style={{position:"relative"}}>
      <div className="rad-panel-head">
        <span className="ttl">Border Radius</span>
        <span className="chev"><RIcon.ChevUp/></span>
      </div>
      <div className="rad-panel-body">
        <div className="rad-field-label">
          <span className="lab">Border radius (px)</span>
          <span className="eye"><RIcon.Eye/></span>
        </div>

        <div style={{position:"relative"}}>
          <div className="rad-grid">
            <div className="cell"><input className="rad-num" defaultValue="20"/></div>
            <div className="cell"><input className="rad-num" defaultValue="20"/></div>
            <div className="cell"><input className="rad-num" defaultValue="20"/></div>
            <div className="cell"><input className="rad-num" defaultValue="20"/></div>
            <div className="link-btn"><RIcon.Link/></div>
          </div>
          <div className="rad-measure"><div className="bar"><span className="val">~190px</span></div></div>
        </div>

        <div className="rad-hover-section">
          <div className="hv-label">Hover</div>
          <div style={{position:"relative"}}>
            <div className="rad-grid hover-grid">
              <div className="cell"><input className="rad-num" defaultValue="0"/></div>
              <div className="cell"><input className="rad-num" defaultValue="0"/></div>
              <div className="cell"><input className="rad-num" defaultValue="0"/></div>
              <div className="cell"><input className="rad-num" defaultValue="0"/></div>
              <div className="link-btn"><RIcon.Link/></div>
            </div>
          </div>
          <div className="rad-duration">
            <span className="lab">Durata</span>
            <input className="rad-num" defaultValue="300"/>
            <span className="unit">ms</span>
          </div>
        </div>
      </div>
    </div>
  );
}

// ════════════════════════════════════════════════════════════════
// REDESIGN — compact, stateful
// ════════════════════════════════════════════════════════════════
function CompactControl() {
  const [state, setState] = React.useState("normal");      // normal | hover
  const [linked, setLinked] = React.useState(true);
  const [vals, setVals] = React.useState({
    normal: { all: 20, tl: 20, tr: 20, br: 20, bl: 20 },
    hover:  { all: 0,  tl: 0,  tr: 0,  br: 0,  bl: 0 },
  });
  const [duration, setDuration] = React.useState(300);
  const [peek, setPeek] = React.useState(true);

  const cur = vals[state];
  const isHover = state === "hover";
  const hoverDiffers = ["all","tl","tr","br","bl"].some(k => vals.hover[k] !== vals.normal[k]);

  const setAll = (v) => {
    v = Math.max(0, Math.min(100, +v || 0));
    setVals(s => ({ ...s, [state]: { all: v, tl: v, tr: v, br: v, bl: v } }));
  };
  const setCorner = (k, v) => {
    v = Math.max(0, Math.min(100, +v || 0));
    setVals(s => ({ ...s, [state]: { ...s[state], [k]: v } }));
  };

  // preview radius string (use normal for the resting chip)
  const pv = vals.normal;
  const radiusStr = `${pv.tl}px ${pv.tr}px ${pv.br}px ${pv.bl}px`;

  const sliderPct = Math.min(100, (cur.all / 60) * 100);
  const onTrack = (e) => {
    const rect = e.currentTarget.getBoundingClientRect();
    const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
    setAll(Math.round(pct * 60));
  };

  return (
    <div className="rad-panel">
      <div className="rad-panel-head">
        <span className="ttl">Border Radius</span>
        <span className="chev"><RIcon.ChevUp/></span>
      </div>
      <div className="rad-panel-body">
        {/* state toggle + peek eye */}
        <div className="rc-states">
          <div className="rc-seg">
            <button className={!isHover ? "on" : ""} onClick={() => setState("normal")}>Normale</button>
            <button className={isHover ? "on hover-on" : ""} onClick={() => setState("hover")}>
              Hover {hoverDiffers && <span className="dot"/>}
            </button>
          </div>
          <span className="spacer"/>
          <button
            className={"rc-eye" + (peek ? " on" : "")}
            onClick={() => setPeek(p => !p)}
            title={peek ? "Nascondi anteprima" : "Sbircia l'anteprima"}
          >
            {peek ? <RIcon.Eye/> : <RIcon.EyeOff/>}
          </button>
        </div>

        {/* main row */}
        <div className="rc-row">
          <button
            className={"rc-cornerbtn" + (linked ? " linked" : "")}
            onClick={() => setLinked(l => !l)}
            title={linked ? "Angoli collegati — clicca per separare" : "Angoli separati — clicca per collegare"}
          >
            {linked ? <RIcon.Link/> : <RIcon.Corners/>}
          </button>

          <div className={"rc-slider" + (isHover ? " hover" : "")} onMouseDown={onTrack}>
            <div className="track">
              <div className="fill" style={{width: sliderPct + "%"}}/>
              <div className="knob" style={{left: sliderPct + "%"}}/>
            </div>
          </div>

          <div className="rc-value">
            <input
              value={linked ? cur.all : "—"}
              onChange={(e) => setAll(e.target.value)}
              disabled={!linked}
            />
            <span className="unit">px <RIcon.ChevDown/></span>
          </div>
        </div>

        {/* per-corner expanded */}
        {!linked && (
          <div className="rc-corners">
            {[
              { k:"tl", r:"0deg" }, { k:"tr", r:"90deg" }, { k:"br", r:"180deg" }, { k:"bl", r:"270deg" },
            ].map(c => (
              <div key={c.k} className="rc-corner">
                <span className="cnr-ic" style={{transform:`rotate(${c.r})`}}><RIcon.CornerTL/></span>
                <input className="mini" value={cur[c.k]} onChange={(e) => setCorner(c.k, e.target.value)}/>
              </div>
            ))}
          </div>
        )}

        {/* duration — only when editing hover (it's the only state with a transition) */}
        {isHover && (
          <div className="rc-duration">
            <span>Transizione</span>
            <div className="rc-value">
              <input value={duration} onChange={(e) => setDuration(Math.max(0, +e.target.value || 0))}/>
              <span className="unit">ms</span>
            </div>
          </div>
        )}

        {/* live preview */}
        {peek && (
          <div className="rc-preview">
            <div className="chip" style={{borderRadius: radiusStr}}/>
          </div>
        )}
      </div>
    </div>
  );
}

// ════════════════════════════════════════════════════════════════
// States gallery — shows the compact control in its key states
// ════════════════════════════════════════════════════════════════
function StatesGallery() {
  return (
    <div className="rad" style={{display:"grid",gap:18}}>
      <div className="rc-block">
        <div className="rc-state-cap">Stato base · angoli collegati · ~150px totali</div>
        <MiniCompact linked={true} state="normal"/>
      </div>
      <div className="rc-block">
        <div className="rc-state-cap">Angoli separati · si espande solo quando serve</div>
        <MiniCompact linked={false} state="normal"/>
      </div>
      <div className="rc-block">
        <div className="rc-state-cap">Stato Hover · durata transizione inline</div>
        <MiniCompact linked={true} state="hover"/>
      </div>
    </div>
  );
}

// Static, non-interactive mini versions for the gallery
function MiniCompact({ linked, state }) {
  const isHover = state === "hover";
  const v = isHover ? 0 : 20;
  const pct = (v / 60) * 100;
  return (
    <div className="rad-panel" style={{width:308}}>
      <div className="rad-panel-body" style={{padding:"14px 14px"}}>
        <div className="rc-states">
          <div className="rc-seg">
            <button className={!isHover ? "on" : ""}>Normale</button>
            <button className={isHover ? "on hover-on" : ""}>Hover <span className="dot"/></button>
          </div>
          <span className="spacer"/>
          <span className="rc-eye on"><RIcon.Eye/></span>
        </div>
        <div className="rc-row">
          <button className={"rc-cornerbtn" + (linked ? " linked" : "")}>
            {linked ? <RIcon.Link/> : <RIcon.Corners/>}
          </button>
          <div className={"rc-slider" + (isHover ? " hover" : "")}>
            <div className="track"><div className="fill" style={{width: pct + "%"}}/><div className="knob" style={{left: pct + "%"}}/></div>
          </div>
          <div className="rc-value">
            <input value={linked ? v : "—"} readOnly disabled={!linked}/>
            <span className="unit">px <RIcon.ChevDown/></span>
          </div>
        </div>
        {!linked && (
          <div className="rc-corners">
            {["0deg","90deg","180deg","270deg"].map((r,i) => (
              <div key={i} className="rc-corner">
                <span className="cnr-ic" style={{transform:`rotate(${r})`}}><RIcon.CornerTL/></span>
                <input className="mini" value={[20,20,8,8][i]} readOnly/>
              </div>
            ))}
          </div>
        )}
        {isHover && (
          <div className="rc-duration">
            <span>Transizione</span>
            <div className="rc-value"><input value="300" readOnly/><span className="unit">ms</span></div>
          </div>
        )}
      </div>
    </div>
  );
}

// ════════════════════════════════════════════════════════════════
// Annotation panels
// ════════════════════════════════════════════════════════════════
function CurrentNotes() {
  const items = [
    { t:"Griglia 2×2 spaziale", d:"4 input disposti agli angoli con link al centro: ~190px di altezza per 4 numeri quasi sempre identici." },
    { t:"HOVER duplica tutto", d:"L'intero blocco è ripetuto per lo stato hover → l'altezza raddoppia per una variante di stato." },
    { t:"Nessuna gerarchia", d:"Il caso comune (raggio uniforme) costa quanto il caso raro (4 angoli diversi)." },
    { t:"Durata sempre visibile", d:"Il campo transizione occupa spazio anche quando hover = normale (nessuna transizione utile)." },
  ];
  return (
    <div className="rad" style={{width:340}}>
      <div className="rad-h">Diagnosi · controllo attuale</div>
      <div style={{marginBottom:16}}>
        <span className="rad-tag bad">≈ 500px di altezza · 9 campi</span>
      </div>
      <div className="rad-anno-list">
        {items.map(it => (
          <div key={it.t} className="rad-anno">
            <span className="ic x"><RIcon.X/></span>
            <div><b>{it.t}.</b> {it.d}</div>
          </div>
        ))}
      </div>
    </div>
  );
}

function CompactNotes() {
  const items = [
    { t:"Slider + valore unico", d:"Il 95% dei casi è un raggio uniforme: uno slider con drag rapido + campo numerico. Nessuna griglia." },
    { t:"Angoli on-demand", d:"Il pulsante collega/separa espande i 4 input solo quando servono davvero — altrimenti restano nascosti." },
    { t:"Stato come toggle", d:"Normale / Hover condividono lo stesso controllo. Un puntino segnala che l'hover ha valori diversi." },
    { t:"Durata contestuale", d:"Il campo transizione appare solo in modalità Hover, dove ha senso." },
    { t:"Anteprima live", d:"Una chip mostra il raggio risultante in tempo reale — feedback immediato, niente tentativi al buio." },
  ];
  return (
    <div className="rad" style={{width:340}}>
      <div className="rad-h">Redesign · controllo compatto</div>
      <div style={{marginBottom:16,display:"flex",gap:8,flexWrap:"wrap"}}>
        <span className="rad-tag good">≈ 150px stato base</span>
        <span className="rad-tag save">−70% altezza</span>
      </div>
      <div className="rad-anno-list">
        {items.map(it => (
          <div key={it.t} className="rad-anno">
            <span className="ic v"><RIcon.Check/></span>
            <div><b>{it.t}.</b> {it.d}</div>
          </div>
        ))}
      </div>
      <div style={{marginTop:18,padding:"14px 16px",background:"var(--blue-soft)",border:"1px solid var(--blue-line)",borderRadius:10}}>
        <div className="rad-note">
          <b>Pattern riusabile.</b> Lo stesso schema (slider + valore + toggle collega/separa + Normale/Hover) si applica a <b>margini, padding, bordi, ombre</b> — tutte le proprietà "4 lati / 4 angoli" del pannello. Standardizzarlo riduce l'ingombro di decine di sezioni.
        </div>
      </div>
    </div>
  );
}

window.CurrentControl = CurrentControl;
window.CompactControl = CompactControl;
window.StatesGallery = StatesGallery;
window.CurrentNotes = CurrentNotes;
window.CompactNotes = CompactNotes;
