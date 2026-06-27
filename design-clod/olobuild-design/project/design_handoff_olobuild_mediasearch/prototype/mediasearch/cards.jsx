// Ricerca Media — card & list renderers
const { useState, useEffect, useRef } = React;

// local icon set (keeps the page self-contained)
const MS_ICONS = {
  search:   <><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></>,
  image:    <><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.5"/><path d="M21 15l-5-5L5 21"/></>,
  film:     <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 4v16M17 4v16M3 9h4M3 15h4M17 9h4M17 15h4"/></>,
  globe:    <><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></>,
  rotate:   <><path d="M3 12a9 9 0 109-9"/><path d="M3 4v5h5"/><circle cx="12" cy="12" r="2.5"/></>,
  music:    <><path d="M9 18V6l10-2v12"/><circle cx="6.5" cy="18" r="2.5"/><circle cx="16.5" cy="16" r="2.5"/></>,
  play:     <path d="M8 5l11 7-11 7z" fill="currentColor" stroke="none"/>,
  pause:    <path d="M8 5v14M16 5v14" strokeWidth="2.6"/>,
  download: <path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>,
  eye:      <><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z"/><circle cx="12" cy="12" r="2.6"/></>,
  check:    <path d="M4 12.5l5 5L20 6.5"/>,
  external: <path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/>,
  lock:     <><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></>,
  sliders:  <path d="M4 7h10M18 7h2M4 17h2M10 17h10M14 4v6M8 14v6"/>,
  info:     <><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></>,
  pin:      <><path d="M12 21s-7-6.1-7-11a7 7 0 0114 0c0 4.9-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></>,
  x:        <path d="M6 6l12 12M18 6L6 18"/>,
};

function MsIcon({ name, size = 16, ...rest }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor"
         strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" {...rest}>
      {MS_ICONS[name]}
    </svg>
  );
}

function msImg(seed, w, h) {
  return "https://picsum.photos/seed/" + encodeURIComponent(seed) + "/" + w + "/" + h;
}

// ── import button with busy/done states ──
function ImportBtn({ id, imported, onImported, small, label }) {
  const [busy, setBusy] = useState(false);
  const done = imported.has(id);
  const t = useRef(null);
  useEffect(function () { return function () { clearTimeout(t.current); }; }, []);
  function go(e) {
    e.stopPropagation();
    if (busy || done) return;
    setBusy(true);
    t.current = setTimeout(function () { setBusy(false); onImported(id); }, 900);
  }
  return (
    <button className={"ms-import" + (busy ? " busy" : "") + (done ? " done" : "")} onClick={go}>
      {busy ? <span className="spin"></span> : <MsIcon name={done ? "check" : "download"} size={13}/>}
      {done ? "Importato" : busy ? "Importazione…" : (label || (small ? "Importa" : "Importa nella Media Library"))}
    </button>
  );
}

// ── photo card (masonry) ──
function PhotoCard({ item, provider, imported, onImported, onPreview }) {
  const thW = 480, thH = Math.round(480 * item.arh / item.arw);
  return (
    <figure className="ms-card" style={{ aspectRatio: item.arw + "/" + item.arh }} onClick={function(){ onPreview(item); }}>
      <img src={msImg(item.seed, thW, thH)} alt={item.photographer} loading="lazy"/>
      <div className="veil">
        <div className="top">
          <button className="ms-iconbtn" title="Anteprima" onClick={function(e){ e.stopPropagation(); onPreview(item); }}>
            <MsIcon name="eye" size={15}/>
          </button>
        </div>
        <div className="bottom">
          <div className="who">
            <div className="nm">{item.photographer}</div>
            <div className="dim">{item.w} × {item.h}</div>
          </div>
          <ImportBtn id={item.id} imported={imported} onImported={onImported} small={true}/>
        </div>
      </div>
    </figure>
  );
}

// ── video card ──
function VideoCard({ item, badge, imported, onImported, onPreview }) {
  return (
    <figure className="ms-vcard" onClick={function(){ onPreview(item); }}>
      <img src={msImg(item.seed, 480, 270)} alt="" loading="lazy"/>
      {badge ? <span className="ms-badge">{badge}</span> : null}
      <div className="veil">
        <button className="playbtn" title="Anteprima" onClick={function(e){ e.stopPropagation(); onPreview(item); }}>
          <MsIcon name="play" size={18}/>
        </button>
      </div>
      <span className="who">{item.photographer}</span>
      <span className="dur">{msFmtDur(item.duration)}</span>
    </figure>
  );
}

// ── HDRI card ──
function HdriCard({ item, imported, onImported, onPreview }) {
  return (
    <figure className="ms-vcard" onClick={function(){ onPreview(item); }}>
      <img src={msImg(item.seed, 480, 270)} alt={item.name} loading="lazy"/>
      <span className="ms-badge"><MsIcon name="globe" size={11}/> HDRI 360</span>
      <div className="veil">
        <button className="playbtn" title="Anteprima" onClick={function(e){ e.stopPropagation(); onPreview(item); }}>
          <MsIcon name="eye" size={17}/>
        </button>
      </div>
      <div className="ms-tagrow">
        {item.tags.map(function(t){ return <span key={t} className="ms-tag">{t}</span>; })}
      </div>
      <span className="dur">{item.name}</span>
    </figure>
  );
}

// ── audio row ──
function AudioRow({ item, playing, onToggle, imported, onImported }) {
  const isOn = playing === item.id;
  const [prog, setProg] = useState(0);
  useEffect(function () {
    if (!isOn) { setProg(0); return; }
    const t0 = performance.now();
    let raf;
    function tick(now) {
      const p = Math.min(1, (now - t0) / (item.duration * 1000) * 14); // accelerated demo playback
      setProg(p);
      if (p < 1) raf = requestAnimationFrame(tick);
      else onToggle(null);
    }
    raf = requestAnimationFrame(tick);
    return function () { cancelAnimationFrame(raf); };
  }, [isOn]);
  const litCount = Math.round(prog * item.bars.length);
  return (
    <div className={"ms-arow" + (isOn ? " playing" : "")}>
      <button className="ms-aplay" onClick={function(){ onToggle(isOn ? null : item.id); }} title={isOn ? "Pausa" : "Riproduci"}>
        <MsIcon name={isOn ? "pause" : "play"} size={15}/>
      </button>
      <div className="ms-ainfo">
        <div className="nm">{item.name}</div>
        <div className="meta">
          <span className="lic">{item.license}</span>
          <span>{item.kind}</span>
          <span>di {item.author}</span>
          <span>{msFmtNum(item.downloads)} download</span>
        </div>
      </div>
      <div className="ms-wave" aria-hidden="true">
        {item.bars.map(function(b, i){
          return <i key={i} className={i < litCount ? "lit" : ""} style={{ height: Math.round(b * 100) + "%" }}></i>;
        })}
      </div>
      <span className="dur">{msFmtDur(item.duration)}</span>
      <ImportBtn id={item.id} imported={imported} onImported={onImported} small={true}/>
    </div>
  );
}

// ── Google Street View panel ──
function GsvPanel({ imported, onImported }) {
  const [val, setVal] = useState("45.4642, 9.1900");
  const [zoom, setZoom] = useState("3");
  const [res, setRes] = useState(null);
  const [busy, setBusy] = useState(false);
  const [date, setDate] = useState("2024-06");
  const dates = ["2024-06", "2022-09", "2019-04", "2016-11"];
  function resolve() {
    if (!val.trim() || busy) return;
    setBusy(true);
    setTimeout(function () {
      setBusy(false);
      setRes({ pano: "CIHM0ogKEICAgID" + msHash(val).toString(36).slice(0, 6), lat: "45.46420", lng: "9.19000", addr: "Piazza del Duomo, Milano MI" });
    }, 700);
  }
  return (
    <div className="ms-gsv">
      <div className="note">
        <MsIcon name="info" size={16} style={{ flexShrink: 0, marginTop: 1 }}/>
        <span>Incolla un URL Google Maps, coordinate <b>lat,lng</b> o un <b>pano_id</b> per scaricare un panorama Street View equirettangolare.</span>
      </div>
      <div className="ms-search-row">
        <div className="ms-search-field">
          <MsIcon name="pin" size={17}/>
          <input value={val} onChange={function(e){ setVal(e.target.value); }}
                 onKeyDown={function(e){ if (e.key === "Enter") resolve(); }}
                 placeholder="https://www.google.com/maps/@45.4642,9.1900,3a… oppure 45.4642,9.1900"/>
        </div>
        <div className="ms-filter" style={{ borderRadius: 10 }}>
          <label>Zoom</label>
          <select value={zoom} onChange={function(e){ setZoom(e.target.value); }}>
            <option value="2">2 · bassa</option>
            <option value="3">3 · media</option>
            <option value="4">4 · alta</option>
          </select>
        </div>
        <button className="ms-search-btn" onClick={resolve}>{busy ? "Ricerca…" : "Cerca panorama"}</button>
      </div>
      {res ? (
        <div className="ms-gsv-result">
          <img src={msImg("pano-" + res.pano + "-" + date, 840, 420)} alt="Anteprima Street View"/>
          <div className="meta">
            <p><strong>{res.addr}</strong></p>
            <p>Pano ID: <strong style={{ fontFamily: "var(--ot-font-mono)", fontSize: 12 }}>{res.pano}</strong></p>
            <p>Coordinate: <strong>{res.lat}, {res.lng}</strong> · Zoom {zoom}</p>
            <p>Timeline:</p>
            <div className="hist">
              {dates.map(function(d){
                return <button key={d} className={d === date ? "cur" : ""} onClick={function(){ setDate(d); }}>{d}</button>;
              })}
            </div>
            <ImportBtn id={"gsv-" + res.pano + date} imported={imported} onImported={onImported} label="Importa panorama JPG"/>
          </div>
        </div>
      ) : null}
    </div>
  );
}

// ── skeletons / empty ──
function SkeletonMasonry() {
  const hs = [180, 260, 210, 320, 190, 240, 300, 200, 250, 170, 280, 220];
  return (
    <div className="ms-masonry">
      {hs.map(function(h, i){ return <div key={i} className="ms-skel" style={{ height: h }}></div>; })}
    </div>
  );
}
function SkeletonGrid({ rows }) {
  if (rows) {
    return (
      <div className="ms-audio-list">
        {[0,1,2,3,4,5].map(function(i){ return <div key={i} className="ms-skel" style={{ height: 62, margin: 0 }}></div>; })}
      </div>
    );
  }
  return (
    <div className="ms-grid">
      {[0,1,2,3,4,5,6,7].map(function(i){ return <div key={i} className="ms-skel" style={{ aspectRatio: "16/9", margin: 0 }}></div>; })}
    </div>
  );
}
function EmptyState({ tab, onQuick }) {
  const icons = { photo: "image", video: "film", photo360: "globe", video360: "rotate", audio: "music" };
  return (
    <div className="ms-empty">
      <span className="ico"><MsIcon name={icons[tab]} size={26}/></span>
      <h3>Cerca nei provider stock</h3>
      <p>Scrivi una parola chiave e premi Invio: i risultati arrivano direttamente nella Media Library, con licenza libera.</p>
      <div className="ms-quick" style={{ justifyContent: "center" }}>
        {MS_QUICK.map(function(q){ return <button key={q} onClick={function(){ onQuick(q); }}>{q}</button>; })}
      </div>
    </div>
  );
}

// ── preview modal ──
function PreviewModal({ item, tab, provider, imported, onImported, onClose }) {
  useEffect(function () {
    function onKey(e) { if (e.key === "Escape") onClose(); }
    addEventListener("keydown", onKey);
    return function () { removeEventListener("keydown", onKey); };
  }, []);
  if (!item) return null;
  const isVideo = tab === "video" || tab === "video360";
  const big = msImg(item.seed, 1200, isVideo ? 675 : Math.round(1200 * (item.arh || 9) / (item.arw || 16)));
  const provLabel = (MS_PROVIDERS[tab] || []).filter(function(p){ return p.id === provider; }).map(function(p){ return p.label; })[0] || provider;
  return (
    <div className="ms-modal" role="dialog" aria-modal="true">
      <div className="ovl" onClick={onClose}></div>
      <div className="box">
        <button className="x" onClick={onClose} title="Chiudi"><MsIcon name="x" size={15}/></button>
        <img className="big" src={big} alt={item.name || item.photographer || ""}/>
        <div className="row">
          <dl className="facts">
            <dt>{item.name ? "Nome" : "Autore"}</dt><dd>{item.name || item.photographer}</dd>
            {item.name && item.author ? <React.Fragment><dt>Autore</dt><dd>{item.author}</dd></React.Fragment> : null}
            <dt>Provider</dt><dd>{provLabel}</dd>
            {item.w ? <React.Fragment><dt>Dimensioni</dt><dd>{item.w} × {item.h} px</dd></React.Fragment> : null}
            {item.duration ? <React.Fragment><dt>Durata</dt><dd>{msFmtDur(item.duration)}</dd></React.Fragment> : null}
            <dt>Licenza</dt><dd>{tab === "photo360" ? "CC0 · dominio pubblico" : "Licenza libera, attribuzione consigliata"}</dd>
          </dl>
          <div className="acts">
            <a className="ghost" href="#" onClick={function(e){ e.preventDefault(); }}>
              <MsIcon name="external" size={13}/> Apri originale
            </a>
            <ImportBtn id={item.id} imported={imported} onImported={onImported}/>
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, {
  MsIcon, msImg, ImportBtn, PhotoCard, VideoCard, HdriCard, AudioRow,
  GsvPanel, SkeletonMasonry, SkeletonGrid, EmptyState, PreviewModal,
});
