// OLObuild — Configurazione: app shell (shared topbar/sidebar/savebar)
// ───────────────────────────────────────────────────────────────────
// Icon set (Lucide inline, stroke 2)
const CfgIcon = {
  Search: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>,
  ChevR: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 18 6-6-6-6"/></svg>,
  ChevD: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m6 9 6 6 6-6"/></svg>,
  Palette: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.5 1.5-1.5 0-.4-.2-.8-.5-1.2a1.5 1.5 0 0 1 1.2-2.3h2C18.5 17 20.5 15 20.5 12.4 20.5 6.5 16.7 2 12 2z"/></svg>,
  Layers: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>,
  Type: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 7V5h16v2"/><path d="M9 20h6"/><path d="M12 5v15"/></svg>,
  Devices: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="5" width="14" height="11" rx="1.5"/><rect x="14" y="9" width="8" height="11" rx="1.5"/><path d="M5 20h6"/></svg>,
  Sparkles: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/><path d="M19 14l.7 2.3L22 17l-2.3.7L19 20l-.7-2.3L16 17l2.3-.7L19 14z"/></svg>,
  Plug: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 2v6M15 2v6"/><path d="M5 8h14v3a5 5 0 0 1-5 5h-4a5 5 0 0 1-5-5V8z"/><path d="M12 16v6"/></svg>,
  Image: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>,
  Mail: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>,
  Bar: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 21h18"/><path d="M7 17V8M12 17V4M17 17v-6"/></svg>,
  Key: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="8" cy="14" r="4"/><path d="m11 12 9-9 3 3-3 3-2-2-2 2-2-2-3 3"/></svg>,
  Refresh: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7l3 2.7"/><path d="M21 3v6h-6"/></svg>,
  Gauge: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 14 8 10"/><circle cx="12" cy="14" r="9"/><path d="M3 14a9 9 0 0 1 18 0"/></svg>,
  Download: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 3v13M5 12l7 7 7-7M5 21h14"/></svg>,
  Code: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m16 18 6-6-6-6M8 6l-6 6 6 6"/></svg>,
  Bug: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="8" y="6" width="8" height="14" rx="4"/><path d="M12 6V4M8 8l-3-3M16 8l3-3M5 13H2M22 13h-3M8 18l-3 3M16 18l3 3"/></svg>,
  Wrench: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg>,
  Save: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>,
  Book: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 4h6a4 4 0 0 1 4 4v12a3 3 0 0 0-3-3H2zM22 4h-6a4 4 0 0 0-4 4v12a3 3 0 0 1 3-3h7z"/></svg>,
  Eye: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>,
  EyeOff: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 3l18 18M10.6 6.1A10.5 10.5 0 0 1 12 6c6.5 0 10 6 10 6a17.3 17.3 0 0 1-4 4.5M6.6 6.6A17.3 17.3 0 0 0 2 12s3.5 6 10 6c1.3 0 2.5-.3 3.6-.7"/><path d="M9.9 4.2A11.4 11.4 0 0 1 12 4"/></svg>,
  Plus: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M12 5v14M5 12h14"/></svg>,
  Check: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12l5 5L20 7"/></svg>,
  X: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>,
  Undo: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 3-6.7L3 9"/></svg>,
  Drop: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 3s7 8 7 13a7 7 0 0 1-14 0c0-5 7-13 7-13z"/></svg>,
  Logo: () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2.5"/></svg>,
};

// ───────────────────────────────────────────────────────────────────
// Information Architecture — Configurazione = "il posto delle impostazioni
// globali del sito". Migrato qui da dashboard parent ?page=olobuild:
//   SEO, Cookie Consent, Performance & Cache, White Label, Permessi & Ruoli
// (tutte cose set-once che applicano ovunque — non azioni)
// Il dashboard parent resta puramente azioni/dati:
//   Gestione Template, Ricerca Media, Invii Form, Analytics, Redirect,
//   Strumenti, Popup, WooCommerce, Import/Export, Submissions, Diagnostica,
//   Licenza.
const IA_GROUPS = [
  {
    id: "design",
    title: "Design",
    items: [
      { id: "presets",    label: "Stili & Preset",          ic: "Layers",  active: true, badge: null },
      { id: "colori",     label: "Palette colori",          ic: "Palette", badge: null },
      { id: "tipografia", label: "Tipografia",              ic: "Type",    badge: null },
      { id: "spaziature", label: "Spaziature & layout",     ic: "Layers",  soon: true },
      { id: "responsive", label: "Breakpoint responsive",   ic: "Devices", badge: null },
    ],
  },
  {
    id: "seoprivacy",
    title: "SEO & Privacy",
    items: [
      { id: "seo",        label: "SEO globale",             ic: "Search",  badge: "MIGRATO" },
      { id: "cookie",     label: "Cookie Consent & GDPR",   ic: "Key",     badge: "MIGRATO" },
    ],
  },
  {
    id: "prestazioni",
    title: "Prestazioni & Servizi",
    items: [
      { id: "performance",label: "Performance & Cache",     ic: "Gauge",   badge: "MIGRATO" },
      { id: "ai",         label: "AI Assistant",            ic: "Sparkles",badge: "NEW" },
      { id: "stockmedia", label: "Stock media",             ic: "Image",   badge: null },
    ],
  },
  {
    id: "team",
    title: "Team & Brand",
    items: [
      { id: "whitelabel", label: "White Label",             ic: "Drop",    badge: "MIGRATO" },
      { id: "permessi",   label: "Permessi & Ruoli",        ic: "Wrench",  badge: "MIGRATO" },
    ],
  },
];

// ───────────────────────────────────────────────────────────────────
// Shared shell components
function CfgTopbar({ tabLabel, variant }) {
  return (
    <div className="cfg-topbar">
      <div className="brand">
        <div className="dot"><CfgIcon.Logo/></div>
        <div>olo<span>build</span></div>
      </div>
      <div className="crumb">
        <a style={{color:"var(--c-text-mute)",textDecoration:"none",cursor:"pointer"}}>Dashboard</a>
        <span className="sep">/</span>
        <a style={{color:"var(--c-text-mute)",textDecoration:"none",cursor:"pointer"}}>Configurazione</a>
        <span className="sep">/</span>
        <b>{tabLabel}</b>
      </div>
      <div className="spacer"/>
      <div className="top-search">
        <CfgIcon.Search/>
        <span>Cerca un'impostazione…</span>
        <span className="kbd">⌘ K</span>
      </div>
      <div className="top-actions">
        <a className="doc-link"><CfgIcon.Book/> Documentazione</a>
        <a className="doc-link"><CfgIcon.Eye/> Anteprima sito</a>
      </div>
    </div>
  );
}

function CfgSidebar({ activeId, onPick }) {
  return (
    <aside className="cfg-sidebar">
      <div className="cfg-side-search">
        <div className="cfg-side-search-input">
          <CfgIcon.Search/>
          <span>Filtra impostazioni…</span>
        </div>
      </div>
      <div className="cfg-sidegroups">
        {IA_GROUPS.map(g => (
          <div key={g.id} className="cfg-group">
            <div className="cfg-group-head">
              {g.title}
              <span className="count">{g.items.length}</span>
            </div>
            {g.items.map(it => {
              const Ic = CfgIcon[it.ic] || CfgIcon.Layers;
              const isActive = activeId === it.id;
              const cn = "cfg-side-item" + (isActive ? " is-active" : "") + (it.soon ? " is-soon" : "");
              return (
                <div
                  key={it.id}
                  className={cn}
                  onClick={() => { if (!it.soon) onPick(it.id); }}
                  title={it.soon ? "In arrivo nella prossima release" : ""}
                >
                  <div className="ic"><Ic/></div>
                  <div>{it.label}</div>
                  {it.soon
                    ? <span className="pill-soon">Soon</span>
                    : it.badge === "MIGRATO"
                      ? <span className="badge-new" style={{background:"rgba(245,158,11,.15)",color:"#fbbf24"}}>↓</span>
                      : it.badge
                        ? <span className="badge-new">{it.badge}</span>
                        : <span className="chev"><CfgIcon.ChevR/></span>
                  }
                </div>
              );
            })}
          </div>
        ))}
      </div>
      <div className="cfg-side-footer">
        <div className="lic">
          <span className="ok"/>
          <span>Licenza <b>Agency</b> attiva</span>
        </div>
        <div className="ver">v3.34.6 · build 2026.05</div>
      </div>
    </aside>
  );
}

function CfgSavebar({ dirty = true }) {
  return (
    <div className="cfg-savebar">
      <div className="meta">
        {dirty && <span className="dirty"><span className="dot"/> Modifiche non salvate</span>}
        <span>Ultimo salvataggio <b>oggi alle 14:32</b> · <span className="text-mono">v3.34.6</span></span>
      </div>
      <div className="grow"/>
      <div className="save-actions">
        <button className="cfg-btn cfg-btn-ghost"><CfgIcon.Undo/> Annulla modifiche</button>
        <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Eye/> Anteprima</button>
        <button className="cfg-btn cfg-btn-primary"><CfgIcon.Save/> Salva impostazioni</button>
      </div>
    </div>
  );
}

window.CfgIcon = CfgIcon;
window.IA_GROUPS = IA_GROUPS;
window.CfgTopbar = CfgTopbar;
window.CfgSidebar = CfgSidebar;
window.CfgSavebar = CfgSavebar;
