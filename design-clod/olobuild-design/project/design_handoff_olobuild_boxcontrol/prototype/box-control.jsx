// OLObuild — BoxField: controllo compatto generalizzato
// Gestisce proprietà "4 angoli" (raggio) e "4 lati" (margine, padding, bordo)
// con: slider + valore, selettore unità, collega/separa on-demand,
// toggle Normale/Hover opzionale, durata contestuale, anteprima opzionale.

const BIcon = {
  ChevDown: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m6 9 6 6 6-6"/></svg>,
  ChevUp: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m6 15 6-6 6 6"/></svg>,
  Eye: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>,
  EyeOff: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10.8 10.8 0 0 1 12 6c6.5 0 10 6 10 6a17.6 17.6 0 0 1-3.36 3.96M6.6 6.6A17.6 17.6 0 0 0 2 12s3.5 6 10 6a10.8 10.8 0 0 0 3.4-.55"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>,
  Link: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1.5-1.5"/></svg>,
  Separate: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 9V6a2 2 0 0 1 2-2h3"/><path d="M15 4h3a2 2 0 0 1 2 2v3"/><path d="M20 15v3a2 2 0 0 1-2 2h-3"/><path d="M9 20H6a2 2 0 0 1-2-2v-3"/></svg>,
  Check: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12l5 5L20 7"/></svg>,
  CornerTL: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 20v-7a9 9 0 0 0-9-9H4"/></svg>,
  Edge: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"><rect x="4" y="7" width="16" height="13" rx="2" strokeWidth="1.5" opacity="0.32"/><line x1="4" y1="4" x2="20" y2="4" strokeWidth="2.6"/></svg>,
  Radius: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 21v-6a8 8 0 0 0-8-8H3"/></svg>,
  Margin: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="1"/><rect x="8" y="8" width="8" height="8" rx="1" opacity=".5"/></svg>,
  Padding: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="1" opacity=".5"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>,
  Border: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>,
  Desktop: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="4" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg>,
  Tablet: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M11 18h2"/></svg>,
  Mobile: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>,
  Reset: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 3-6.7L3 9"/></svg>,
};

// unit config: slider max + step per unit
const UNIT_CFG = {
  px:  { max: 100, step: 1 },
  "%": { max: 100, step: 1 },
  em:  { max: 10,  step: 0.1 },
  rem: { max: 10,  step: 0.1 },
  vw:  { max: 100, step: 1 },
  vh:  { max: 100, step: 1 },
};

function UnitSelect({ unit, units, onChange }) {
  const [open, setOpen] = React.useState(false);
  const ref = React.useRef(null);
  React.useEffect(() => {
    if (!open) return;
    const h = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false); };
    document.addEventListener("mousedown", h);
    return () => document.removeEventListener("mousedown", h);
  }, [open]);
  return (
    <span className="rc-units" ref={ref}>
      <button className={"rc-unit-btn" + (open ? " open" : "")} onClick={() => setOpen(o => !o)}>
        {unit} <BIcon.ChevDown/>
      </button>
      {open && (
        <div className="rc-unit-pop">
          {units.map(u => (
            <button key={u} className={u === unit ? "on" : ""} onClick={() => { onChange(u); setOpen(false); }}>
              {u} {u === unit && <BIcon.Check/>}
            </button>
          ))}
        </div>
      )}
    </span>
  );
}

// keys + icon rotation per mode
const KEYS = {
  corners: [
    { k: "tl", r: "0deg" }, { k: "tr", r: "90deg" }, { k: "br", r: "180deg" }, { k: "bl", r: "270deg" },
  ],
  sides: [
    { k: "t", r: "0deg" }, { k: "r", r: "90deg" }, { k: "b", r: "180deg" }, { k: "l", r: "270deg" },
  ],
};

const BPS = ["desktop", "tablet", "mobile"];
const BP_LABEL = { desktop: "Desktop", tablet: "Tablet", mobile: "Mobile" };

function DeviceSwitch({ bp, setBp, overrides = {} }) {
  return (
    <div className="rc-devices">
      {BPS.map(id => {
        const Ic = BIcon[BP_LABEL[id]];
        return (
          <button key={id} className={bp === id ? "on" : ""} onClick={() => setBp(id)} title={BP_LABEL[id]}>
            <Ic/>
            {overrides[id] && <span className="od"/>}
          </button>
        );
      })}
    </div>
  );
}

function BoxField({
  mode = "corners",         // corners | sides
  units = ["px"],
  defaultUnit = "px",
  hasHover = false,
  initial = 0,
  preview = "auto",         // auto | radius | border | spacing | none
  defaultPeek = false,
  variant = "standalone",   // standalone | stacked
  name = "",
  icon = "Radius",
  responsive = true,        // show device breakpoints
  bp: controlledBp = null,  // when provided, breakpoint is driven globally
  onBp = null,
}) {
  const keys = KEYS[mode];
  const allKeys = keys.map(x => x.k);
  const mk = (v) => Object.fromEntries(allKeys.map(k => [k, v]));
  const Ic = BIcon[icon] || BIcon.Radius;
  const pvKind = preview === "auto" ? (mode === "corners" ? "radius" : "spacing") : preview;

  const blank = () => ({ normal: { all: initial, ...mk(initial) }, hover: { all: initial, ...mk(initial) } });

  const [state, setState] = React.useState("normal");
  const [linked, setLinked] = React.useState(true);
  const [unit, setUnit] = React.useState(defaultUnit);
  const [duration, setDuration] = React.useState(300);
  const [peek, setPeek] = React.useState(defaultPeek);
  const [internalBp, setInternalBp] = React.useState("desktop");
  const [vals, setVals] = React.useState({ desktop: blank(), tablet: blank(), mobile: blank() });

  const bp = controlledBp || internalBp;
  const setBp = onBp || setInternalBp;
  const isHover = state === "hover";
  const cur = vals[bp][state];
  const cfg = UNIT_CFG[unit] || UNIT_CFG.px;
  const hoverDiffers = hasHover && (["all", ...allKeys].some(k => vals[bp].hover[k] !== vals[bp].normal[k]));

  // breakpoint override detection (tablet/mobile differ from desktop)
  const eq = (a, b) => JSON.stringify(a) === JSON.stringify(b);
  const overrides = { tablet: !eq(vals.tablet, vals.desktop), mobile: !eq(vals.mobile, vals.desktop) };
  const overridden = bp !== "desktop" && overrides[bp];

  const clamp = (v) => Math.max(0, Math.min(cfg.max, +v || 0));
  const setAll = (v) => { v = clamp(v); setVals(s => ({ ...s, [bp]: { ...s[bp], [state]: { all: v, ...mk(v) } } })); };
  const setKey = (k, v) => { v = clamp(v); setVals(s => ({ ...s, [bp]: { ...s[bp], [state]: { ...s[bp][state], [k]: v } } })); };
  const resetBp = () => setVals(s => ({ ...s, [bp]: JSON.parse(JSON.stringify(s.desktop)) }));

  const sliderPct = Math.min(100, (cur.all / cfg.max) * 100);
  const onTrack = (e) => {
    const move = (ev) => {
      const rect = track.getBoundingClientRect();
      const pct = Math.max(0, Math.min(1, (ev.clientX - rect.left) / rect.width));
      const raw = pct * cfg.max;
      setAll(cfg.step < 1 ? Math.round(raw * 10) / 10 : Math.round(raw));
    };
    const track = e.currentTarget.querySelector(".track");
    move(e);
    const up = () => { document.removeEventListener("mousemove", move); document.removeEventListener("mouseup", up); };
    document.addEventListener("mousemove", move);
    document.addEventListener("mouseup", up);
  };

  const EdgeOrCorner = mode === "corners" ? BIcon.CornerTL : BIcon.Edge;

  const EyeBtn = (
    <button
      className={"rc-eye" + (peek ? " on" : "")}
      onClick={() => setPeek(p => !p)}
      title={peek ? "Nascondi anteprima" : "Sbircia l'anteprima"}
    >
      {peek ? <BIcon.Eye/> : <BIcon.EyeOff/>}
    </button>
  );

  const ResetChip = overridden ? (
    <button className="rc-reset" onClick={resetBp} title={`Reimposta a Desktop`}>
      <BIcon.Reset/> {BP_LABEL[bp]}
    </button>
  ) : null;

  // per-field device switch only when standalone (not globally controlled)
  const showDevices = responsive && !controlledBp;

  return (
    <>
      {/* header */}
      {variant === "stacked" ? (
        <div className="rc-stack-head">
          <span className="picon"><Ic/></span>
          <span className="pname">{name}</span>
          <span className="spacer"/>
          {ResetChip}
          {EyeBtn}
        </div>
      ) : (
        <div className="rc-ctxbar">
          {showDevices && <DeviceSwitch bp={bp} setBp={setBp} overrides={overrides}/>}
          {name ? <span className="nm">{name}</span> : null}
          <span className="spacer"/>
          {ResetChip}
          {EyeBtn}
        </div>
      )}

      {hasHover && (
        <div className="rc-states">
          <div className="rc-seg">
            <button className={!isHover ? "on" : ""} onClick={() => setState("normal")}>Normale</button>
            <button className={isHover ? "on hover-on" : ""} onClick={() => setState("hover")}>
              Hover {hoverDiffers && <span className="dot"/>}
            </button>
          </div>
        </div>
      )}

      <div className="rc-row">
        <button
          className={"rc-cornerbtn" + (linked ? " linked" : "")}
          onClick={() => setLinked(l => !l)}
          title={linked ? "Collegati — clicca per separare" : "Separati — clicca per collegare"}
        >
          {linked ? <BIcon.Link/> : <BIcon.Separate/>}
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
          {units.length > 1
            ? <UnitSelect unit={unit} units={units} onChange={setUnit}/>
            : <span className="unit">{unit}</span>}
        </div>
      </div>

      {!linked && (
        <div className="rc-corners">
          {keys.map(c => (
            <div key={c.k} className="rc-corner">
              <span className={mode === "corners" ? "cnr-ic" : "edge-ic"} style={{transform:`rotate(${c.r})`}}>
                <EdgeOrCorner/>
              </span>
              <input className="mini" value={cur[c.k]} onChange={(e) => setKey(c.k, e.target.value)}/>
            </div>
          ))}
        </div>
      )}

      {hasHover && isHover && (
        <div className="rc-duration">
          <span>Transizione</span>
          <div className="rc-value">
            <input value={duration} onChange={(e) => setDuration(Math.max(0, +e.target.value || 0))}/>
            <span className="unit">ms</span>
          </div>
        </div>
      )}

      {/* inherited hint when on a non-desktop bp with no override */}
      {bp !== "desktop" && !overridden && (
        <div className="rc-inherit">Eredita da Desktop — modifica per creare un valore {BP_LABEL[bp]}</div>
      )}

      {peek && pvKind !== "none" && (
        <div className="rc-preview">
          {pvKind === "spacing" ? (
            <div className="pv-spacing">
              <div className="pv-outer">
                <div className="pv-inner" style={{
                  margin: `${Math.min(cur.t,26)}px ${Math.min(cur.r,40)}px ${Math.min(cur.b,26)}px ${Math.min(cur.l,40)}px`,
                }}/>
              </div>
            </div>
          ) : pvKind === "border" ? (
            <div className="chip" style={{
              borderStyle: "solid", borderColor: "rgba(255,255,255,.92)",
              borderWidth: `${Math.min(cur.t,12)}px ${Math.min(cur.r,12)}px ${Math.min(cur.b,12)}px ${Math.min(cur.l,12)}px`,
              borderRadius: 8,
            }}/>
          ) : (
            <div className="chip" style={{
              borderRadius: `${cur.tl}${unit==="%"?"%":"px"} ${cur.tr}${unit==="%"?"%":"px"} ${cur.br}${unit==="%"?"%":"px"} ${cur.bl}${unit==="%"?"%":"px"}`,
            }}/>
          )}
        </div>
      )}
    </>
  );
}

// Panel chrome wrapper
function BPanel({ title, width = 340, children }) {
  return (
    <div className="rad-panel" style={{width}}>
      <div className="rad-panel-head">
        <span className="ttl">{title}</span>
        <span className="chev"><BIcon.ChevUp/></span>
      </div>
      <div className="rad-panel-body">{children}</div>
    </div>
  );
}

// A single standalone field — BoxField now renders its own header (name + eye)
function StandaloneField({ name, icon, ...props }) {
  return <BoxField variant="standalone" name={name} icon={icon} {...props}/>;
}

// ════════════════════════════════════════════════════════════════
// Stacked panel — "Spazi & Bordi": many controls, one frame
// ════════════════════════════════════════════════════════════════
function StackedField({ name, icon, ...props }) {
  return (
    <div className="rc-stack-field">
      <BoxField variant="stacked" name={name} icon={icon} {...props}/>
    </div>
  );
}

function StackedPanel() {
  const [bp, setBp] = React.useState("desktop");
  return (
    <div className="rad">
      <BPanel title="Spazi & Bordi" width={344}>
        <div className="rc-panel-bar">
          <span className="lbl">Modifica per</span>
          <DeviceSwitch bp={bp} setBp={setBp}/>
          <span className="spacer"/>
        </div>
        <StackedField bp={bp} onBp={setBp} icon="Margin"  name="Margine" mode="sides"   units={["px","%","em","rem","vw"]} defaultUnit="px" initial={0}  preview="spacing"/>
        <StackedField bp={bp} onBp={setBp} icon="Padding" name="Padding" mode="sides"   units={["px","%","em","rem"]}      defaultUnit="px" initial={16} preview="spacing"/>
        <StackedField bp={bp} onBp={setBp} icon="Radius"  name="Raggio"  mode="corners" units={["px","%","em","rem"]}      defaultUnit="px" initial={20} hasHover={true} preview="radius"/>
        <StackedField bp={bp} onBp={setBp} icon="Border"  name="Spessore bordo" mode="sides" units={["px","em","rem"]}      defaultUnit="px" initial={1}  hasHover={true} preview="border"/>
      </BPanel>
    </div>
  );
}

// Standalone demo artboards
function MarginDemo() {
  return (
    <div className="rad">
      <BPanel title="Margine" width={340}>
        <StandaloneField icon="Margin" name="Margine" mode="sides" units={["px","%","em","rem","vw","vh"]} defaultUnit="px" initial={0} preview="spacing" defaultPeek={true}/>
      </BPanel>
    </div>
  );
}
function PaddingDemo() {
  return (
    <div className="rad">
      <BPanel title="Padding" width={340}>
        <StandaloneField icon="Padding" name="Padding" mode="sides" units={["px","%","em","rem"]} defaultUnit="px" initial={16} preview="spacing" defaultPeek={true}/>
      </BPanel>
    </div>
  );
}
function BorderRadiusDemo() {
  return (
    <div className="rad">
      <BPanel title="Border Radius" width={340}>
        <StandaloneField icon="Radius" name="Raggio angoli" mode="corners" units={["px","%","em","rem"]} defaultUnit="px" initial={20} hasHover={true} preview="radius" defaultPeek={true}/>
      </BPanel>
    </div>
  );
}
function BorderWidthDemo() {
  return (
    <div className="rad">
      <BPanel title="Bordo" width={340}>
        <StandaloneField icon="Border" name="Spessore bordo" mode="sides" units={["px","em","rem"]} defaultUnit="px" initial={1} hasHover={true} preview="border" defaultPeek={true}/>
      </BPanel>
    </div>
  );
}

// Notes for the unit selector + generalization
function ExtendNotes() {
  const items = [
    { t:"Un solo componente", d:"Margine, Padding, Raggio, Spessore bordo usano lo stesso BoxField: cambia solo se mostra lati (T/R/B/L) o angoli." },
    { t:"Selettore unità integrato", d:"px / % / em / rem / vw / vh nel chip a destra del valore. Lo slider adatta automaticamente il range (px→0-100, em→0-10)." },
    { t:"Breakpoint per device", d:"Uno switch desktop/tablet/mobile (globale sul pannello) cambia il valore mostrato. Tablet e mobile ereditano da Desktop finché non li sovrascrivi — un puntino segnala gli override." },
    { t:"Hover solo dove serve", d:"Raggio e bordo hanno il toggle Normale/Hover; margine e padding no — il controllo si accorcia da solo." },
    { t:"Risparmio cumulativo", d:"Un pannello 'Spazi & Bordi' con 4 proprietà, ognuna con stati e breakpoint, sta in un solo frame invece di srotolarsi per migliaia di pixel." },
  ];
  return (
    <div className="rad" style={{width:340}}>
      <div className="rad-h">Estensione · pattern unificato</div>
      <div style={{marginBottom:16,display:"flex",gap:8,flexWrap:"wrap"}}>
        <span className="rad-tag good">4 proprietà · 1 componente</span>
        <span className="rad-tag save">px % em rem vw vh</span>
        <span className="rad-tag save">desktop / tablet / mobile</span>
      </div>
      <div className="rad-anno-list">
        {items.map(it => (
          <div key={it.t} className="rad-anno">
            <span className="ic v"><BIcon.Check/></span>
            <div><b>{it.t}.</b> {it.d}</div>
          </div>
        ))}
      </div>
    </div>
  );
}

window.BoxField = BoxField;
window.StackedPanel = StackedPanel;
window.MarginDemo = MarginDemo;
window.PaddingDemo = PaddingDemo;
window.BorderRadiusDemo = BorderRadiusDemo;
window.BorderWidthDemo = BorderWidthDemo;
window.ExtendNotes = ExtendNotes;
