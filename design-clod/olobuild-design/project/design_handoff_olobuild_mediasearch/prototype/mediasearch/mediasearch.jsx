// Ricerca Media — page shell, state & layout
const { useState, useMemo, useRef: msUseRef } = React;

function MsTopBar() {
  return (
    <div className="home-bar">
      <img className="logo" src="assets/olobuild-horizontal.png" alt="Olobuild"/>
      <span className="ver">v3.4</span>
      <span className="crumb">Plugin <span>›</span> <b>Ricerca Media</b></span>
      <div className="spc"></div>
      <button className="ico-btn" title="Documentazione"><MsIcon name="info" size={15}/></button>
    </div>
  );
}

function MsTabs({ tab, onTab }) {
  const icons = { photo: "image", video: "film", photo360: "globe", video360: "rotate", audio: "music" };
  return (
    <div className="ms-tabs" role="tablist">
      {MS_TABS.map(function (t) {
        return (
          <button key={t.id} role="tab" aria-selected={tab === t.id}
                  className={"ms-tab" + (tab === t.id ? " active" : "")}
                  onClick={function () { onTab(t.id); }}>
            <MsIcon name={icons[t.id]} size={15}/>{t.label}
          </button>
        );
      })}
    </div>
  );
}

function MsProviders({ tab, provider, onProvider }) {
  const list = MS_PROVIDERS[tab] || [];
  return (
    <div className="ms-providers">
      <span className="lbl">Provider</span>
      {list.map(function (p) {
        const dis = !p.key;
        return (
          <button key={p.id}
                  className={"ms-provider" + (provider === p.id ? " active" : "") + (dis ? " disabled" : "")}
                  onClick={function () { if (!dis) onProvider(p.id); }}>
            <span className={"dot " + (dis ? "no" : "ok")}></span>
            {p.label}
            {dis ? <span className="lock"><MsIcon name="lock" size={12}/></span> : null}
            {dis ? <span className="tip">API key mancante — configurala in Configurazione → Stock Media</span> : null}
          </button>
        );
      })}
    </div>
  );
}

function MsFilters({ tab, provider, filters, setF, onReset }) {
  if (tab === "photo360") return null;
  const isPhoto = tab === "photo";
  const allowed = isPhoto ? (MS_PHOTO_FILTERS[provider] || []) : [];
  const active =
    (isPhoto && (filters.orientation || filters.size || filters.minW || filters.minH)) ||
    (!isPhoto && (filters.durPreset || filters.durMin || filters.durMax));
  return (
    <div className="ms-filters">
      <MsIcon name="sliders" size={14} style={{ color: "var(--ot-text-light)" }}/>
      {isPhoto ? (
        <React.Fragment>
          {allowed.indexOf("orientation") >= 0 ? (
            <span className="ms-filter">
              <label>Orientamento</label>
              <select value={filters.orientation} onChange={function(e){ setF("orientation", e.target.value); }}>
                <option value="">Qualsiasi</option>
                <option value="landscape">Orizzontale</option>
                <option value="portrait">Verticale</option>
                {provider !== "pixabay" ? <option value="square">Quadrato</option> : null}
              </select>
            </span>
          ) : null}
          {allowed.indexOf("size") >= 0 ? (
            <span className="ms-filter">
              <label>Dimensione</label>
              <select value={filters.size} onChange={function(e){ setF("size", e.target.value); }}>
                <option value="">Qualsiasi</option>
                <option value="small">Piccola</option>
                <option value="medium">Media</option>
                <option value="large">Grande</option>
              </select>
            </span>
          ) : null}
          {allowed.indexOf("min_width") >= 0 ? (
            <span className="ms-filter">
              <label>Min L</label>
              <input type="number" placeholder="px" min="0" step="100" value={filters.minW}
                     onChange={function(e){ setF("minW", e.target.value); }}/>
            </span>
          ) : null}
          {allowed.indexOf("min_height") >= 0 ? (
            <span className="ms-filter">
              <label>Min A</label>
              <input type="number" placeholder="px" min="0" step="100" value={filters.minH}
                     onChange={function(e){ setF("minH", e.target.value); }}/>
            </span>
          ) : null}
        </React.Fragment>
      ) : (
        <React.Fragment>
          <span className="ms-filter">
            <label>Durata</label>
            <select value={filters.durPreset} onChange={function(e){ setF("durPreset", e.target.value, true); }}>
              <option value="">Qualsiasi</option>
              <option value="0,5">Brevissimo · &lt; 5s</option>
              <option value="0,15">Breve · &lt; 15s</option>
              <option value="5,30">5 — 30 secondi</option>
              <option value="10,60">10s — 1 minuto</option>
              <option value="30,120">30s — 2 minuti</option>
              <option value="60,300">1 — 5 minuti</option>
              <option value="300,">Lungo · &gt; 5 min</option>
            </select>
          </span>
          <span className="sep">oppure</span>
          <span className="ms-filter">
            <label>Da</label>
            <input type="number" placeholder="sec" min="0" value={filters.durMin}
                   onChange={function(e){ setF("durMin", e.target.value); }}/>
          </span>
          <span className="ms-filter">
            <label>A</label>
            <input type="number" placeholder="sec" min="0" value={filters.durMax}
                   onChange={function(e){ setF("durMax", e.target.value); }}/>
          </span>
        </React.Fragment>
      )}
      {active ? <button className="reset" onClick={onReset}>Azzera filtri</button> : null}
    </div>
  );
}

function MsPages({ page, total, onPage }) {
  if (total <= 1) return null;
  const items = [];
  const around = [page - 1, page, page + 1].filter(function(p){ return p > 1 && p < total; });
  const set = [1].concat(around).concat([total]);
  let prev = 0;
  set.forEach(function (p) {
    if (p - prev > 1) items.push("…");
    items.push(p); prev = p;
  });
  return (
    <nav className="ms-pages">
      <button disabled={page === 1} onClick={function(){ onPage(page - 1); }}>‹</button>
      {items.map(function (it, i) {
        return it === "…"
          ? <span key={"e" + i} className="ell">…</span>
          : <button key={it} className={it === page ? "cur" : ""} onClick={function(){ onPage(it); }}>{it}</button>;
      })}
      <button disabled={page === total} onClick={function(){ onPage(page + 1); }}>›</button>
    </nav>
  );
}

function MediaSearchPage() {
  const [tab, setTab] = useState("photo");
  const [provider, setProvider] = useState("unsplash");
  const [query, setQuery] = useState("hotel resort");
  const [committed, setCommitted] = useState("hotel resort");
  const [status, setStatus] = useState("done"); // idle | loading | done
  const [page, setPage] = useState(1);
  const [playing, setPlaying] = useState(null);
  const [imported, setImported] = useState(function(){ return new Set(); });
  const [modal, setModal] = useState(null);
  const [filters, setFilters] = useState({ orientation: "", size: "", minW: "", minH: "", durPreset: "", durMin: "", durMax: "" });
  const timer = msUseRef(null);

  const isGsv = tab === "photo360" && provider === "googlesv";
  const providersActive = Object.values(MS_KEYS).filter(Boolean).length;
  const providersTotal = Object.keys(MS_KEYS).length;

  function runSearch(q, t, pv, pg) {
    if (!q.trim()) { setStatus("idle"); return; }
    clearTimeout(timer.current);
    setStatus("loading");
    setPlaying(null);
    timer.current = setTimeout(function () {
      setCommitted(q); setPage(pg || 1); setStatus("done");
    }, 650);
  }

  function onTab(t) {
    const first = (MS_PROVIDERS[t] || []).filter(function(p){ return p.key; })[0];
    const pv = first ? first.id : (MS_PROVIDERS[t][0] || {}).id;
    setTab(t); setProvider(pv); setModal(null);
    if (query.trim() && !(t === "photo360" && pv === "googlesv")) runSearch(query, t, pv, 1);
    else setStatus("idle");
  }
  function onProvider(pv) {
    setProvider(pv); setModal(null);
    if (pv === "googlesv") { setStatus("idle"); return; }
    if (query.trim()) runSearch(query, tab, pv, 1);
  }
  function onQuick(q) { setQuery(q); runSearch(q, tab, provider, 1); }
  function setF(k, v, clearManual) {
    const nf = Object.assign({}, filters);
    nf[k] = v;
    if (clearManual && v) { nf.durMin = ""; nf.durMax = ""; }
    if ((k === "durMin" || k === "durMax") && v) nf.durPreset = "";
    setFilters(nf);
    if (committed && status === "done") runSearch(committed, tab, provider, 1);
  }
  function resetF() {
    setFilters({ orientation: "", size: "", minW: "", minH: "", durPreset: "", durMin: "", durMax: "" });
    if (committed) runSearch(committed, tab, provider, 1);
  }
  function addImported(id) {
    setImported(function (s) { const n = new Set(s); n.add(id); return n; });
  }

  const results = useMemo(function () {
    if (status !== "done" || isGsv) return [];
    if (tab === "audio") return msAudios(committed, page);
    if (tab === "photo360") return msHdris(committed, page);
    if (tab === "video" || tab === "video360") return msVideos(committed, page, provider);
    return msPhotos(committed, page, provider);
  }, [status, tab, provider, committed, page]);

  const total = status === "done" && !isGsv ? msTotal(committed, tab, provider) : 0;
  const perPage = tab === "audio" ? 10 : tab === "photo360" ? 12 : tab.indexOf("video") === 0 ? 12 : 18;
  const totalPages = Math.max(1, Math.min(40, Math.ceil(total / perPage)));
  const provLabel = (MS_PROVIDERS[tab] || []).filter(function(p){ return p.id === provider; }).map(function(p){ return p.label; })[0];

  return (
    <WPShell activeSub="Ricerca Media">
      <MsTopBar/>
      <div className="ms-content" data-screen-label="Ricerca Media">

        <header className="ms-head">
          <div>
            <h1>Ricerca Media</h1>
            <div className="sub">Foto, video, audio e panorami da provider stock, direttamente nella Media Library · <b>{providersActive}/{providersTotal}</b> provider configurati</div>
          </div>
          <div className="spc"></div>
          <button className="ms-cfg-btn"><MsIcon name="sliders" size={14}/> Configura provider</button>
        </header>

        <section className="ms-panel">
          <MsTabs tab={tab} onTab={onTab}/>
          <MsProviders tab={tab} provider={provider} onProvider={onProvider}/>
          {isGsv ? (
            <GsvPanel imported={imported} onImported={addImported}/>
          ) : (
            <React.Fragment>
              <div className="ms-search-row">
                <div className="ms-search-field">
                  <MsIcon name="search" size={17}/>
                  <input value={query} placeholder={tab === "audio" ? "Cerca suoni, musica, ambience…" : "Cerca per parola chiave…"}
                         onChange={function(e){ setQuery(e.target.value); }}
                         onKeyDown={function(e){ if (e.key === "Enter") runSearch(query, tab, provider, 1); }}/>
                  {query ? <button className="clr" title="Svuota" onClick={function(){ setQuery(""); setStatus("idle"); }}>×</button> : null}
                </div>
                <button className="ms-search-btn" onClick={function(){ runSearch(query, tab, provider, 1); }}>
                  <MsIcon name="search" size={15}/> Cerca
                </button>
              </div>
              <div className="ms-quick">
                <span className="lbl">Prova:</span>
                {MS_QUICK.map(function(q){ return <button key={q} onClick={function(){ onQuick(q); }}>{q}</button>; })}
              </div>
              <MsFilters tab={tab} provider={provider} filters={filters} setF={setF} onReset={resetF}/>
            </React.Fragment>
          )}
        </section>

        {!isGsv ? (
          <div className="ms-status">
            {status === "loading" ? <span>Ricerca di «{query}» in corso…</span> : null}
            {status === "done" ? (
              <React.Fragment>
                <span><b>{msFmtNum(total)}</b> risultati per «{committed}» · pagina {page} di {totalPages}</span>
                <span className="via"><span className="dot" style={{width:6,height:6,borderRadius:99,background:"var(--ot-success)"}}></span> via {provLabel}</span>
              </React.Fragment>
            ) : null}
          </div>
        ) : null}

        {!isGsv && status === "loading" ? (tab === "photo" ? <SkeletonMasonry/> : <SkeletonGrid rows={tab === "audio"}/>) : null}
        {!isGsv && status === "idle" ? <EmptyState tab={tab} onQuick={onQuick}/> : null}

        {!isGsv && status === "done" ? (
          tab === "audio" ? (
            <div className="ms-audio-list">
              {results.map(function (it) {
                return <AudioRow key={it.id} item={it} playing={playing} onToggle={setPlaying}
                                 imported={imported} onImported={addImported}/>;
              })}
            </div>
          ) : tab === "photo360" ? (
            <div className="ms-grid">
              {results.map(function (it) {
                return <HdriCard key={it.id} item={it} imported={imported} onImported={addImported} onPreview={setModal}/>;
              })}
            </div>
          ) : tab === "video" || tab === "video360" ? (
            <div className="ms-grid">
              {results.map(function (it) {
                return <VideoCard key={it.id} item={it} badge={tab === "video360" ? "360°" : null}
                                  imported={imported} onImported={addImported} onPreview={setModal}/>;
              })}
            </div>
          ) : (
            <div className="ms-masonry">
              {results.map(function (it) {
                return <PhotoCard key={it.id} item={it} provider={provider}
                                  imported={imported} onImported={addImported} onPreview={setModal}/>;
              })}
            </div>
          )
        ) : null}

        {!isGsv && status === "done" ? <MsPages page={page} total={totalPages} onPage={function(p){ setPage(p); }}/> : null}
      </div>

      {modal ? (
        <PreviewModal item={modal} tab={tab} provider={provider}
                      imported={imported} onImported={addImported}
                      onClose={function(){ setModal(null); }}/>
      ) : null}
    </WPShell>
  );
}

window.MediaSearchPage = MediaSearchPage;
