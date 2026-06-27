// OLObuild — IA Map: Configurazione come "hub di tutte le impostazioni globali"
// 5 voci migrate dal dashboard genitore + 7 originali, 4 gruppi semantici

function IAMap() {
  const oldTabs = [
    { label: "Stili",              to: "Stili & Preset" },
    { label: "Colori",             to: "Palette colori" },
    { label: "Tipografia",         to: "Tipografia" },
    { label: "AI Assistant",       to: "AI Assistant" },
    { label: "API & Integrazioni", to: "Stock media", renamed: true },
    { label: "Responsive",         to: "Breakpoint responsive" },
  ];

  const migrated = [
    { label: "SEO globale",            from: "card SEO",         where: "GESTIONE" },
    { label: "Cookie Consent & GDPR",  from: "card Cookie",      where: "GESTIONE" },
    { label: "Performance & Cache",    from: "card Performance", where: "GESTIONE" },
    { label: "White Label",            from: "btn White Label",  where: "SISTEMA" },
    { label: "Permessi & Ruoli",       from: "btn Permessi",     where: "SISTEMA" },
  ];

  const newGroups = [
    {
      title: "Design",
      color: "#e1474f",
      items: [
        { label: "Stili & Preset",          src: "stili" },
        { label: "Palette colori",          src: "colori" },
        { label: "Tipografia",              src: "tipografia" },
        { label: "Spaziature & layout",     isNew: true },
        { label: "Breakpoint responsive",   src: "responsive" },
      ],
    },
    {
      title: "SEO & Privacy",
      color: "#0ea5e9",
      items: [
        { label: "SEO globale",            migrated: true, src: "GESTIONE" },
        { label: "Cookie Consent & GDPR",  migrated: true, src: "GESTIONE" },
      ],
    },
    {
      title: "Prestazioni & Servizi",
      color: "#7e22ce",
      items: [
        { label: "Performance & Cache",  migrated: true, src: "GESTIONE" },
        { label: "AI Assistant",         src: "AI Assistant" },
        { label: "Stock media",          src: "API & Integ.", renamed: true },
      ],
    },
    {
      title: "Team & Brand",
      color: "#15803d",
      items: [
        { label: "White Label",         migrated: true, src: "SISTEMA" },
        { label: "Permessi & Ruoli",    migrated: true, src: "SISTEMA" },
      ],
    },
  ];

  // What STAYS in the parent dashboard (action/data oriented)
  const stays = [
    { label: "Gestione Template", kind: "action" },
    { label: "Ricerca Media",     kind: "action" },
    { label: "Invii Form",        kind: "data" },
    { label: "Analytics",         kind: "data" },
    { label: "Redirect & 404",    kind: "action" },
    { label: "Strumenti",         kind: "action" },
    { label: "Popup Globali",     kind: "action" },
    { label: "WooCommerce",       kind: "action" },
    { label: "Import / Export",   kind: "action" },
    { label: "Submissions",       kind: "data" },
    { label: "Diagnostica",       kind: "action" },
    { label: "Licenza",           kind: "data" },
  ];

  return (
    <div style={{
      background: "#fffdf8",
      padding: "32px 36px",
      fontFamily: "var(--c-sans)",
      color: "var(--c-text)",
      height: "100%",
      overflow: "auto",
      boxSizing: "border-box",
    }}>
      <div style={{display:"flex",alignItems:"baseline",gap:20,marginBottom:8,flexWrap:"wrap"}}>
        <span style={{
          fontSize:11,fontWeight:700,letterSpacing:".12em",textTransform:"uppercase",
          color:"var(--c-red)",
        }}>Proposta di riorganizzazione</span>
        <span style={{
          fontSize:12,color:"var(--c-text-mute)",
          background:"var(--c-bg)",
          padding:"3px 10px",borderRadius:999,fontWeight:500,
        }}>Configurazione = "hub impostazioni" · Dashboard = "hub azioni e dati"</span>
      </div>
      <h1 style={{
        fontFamily:"var(--c-display)",
        fontWeight:400,
        fontSize:48,
        lineHeight:.98,
        letterSpacing:"-.02em",
        margin:"0 0 12px",
        color:"var(--c-navy)",
      }}>
        Mappa <em style={{fontStyle:"italic",color:"var(--c-red)"}}>informativa</em>
      </h1>
      <p style={{fontSize:14.5,lineHeight:1.55,color:"var(--c-text-mute)",margin:"0 0 28px",maxWidth:"82ch"}}>
        Regola guida: <b style={{color:"var(--c-navy)"}}>tutto ciò che è "set-once, applies-everywhere"</b> vive in Configurazione. Tutto ciò che è "do-something / look-at-data" resta nel dashboard genitore. Da qui la migrazione di SEO, Cookie Consent, Performance, White Label e Permessi — sono impostazioni globali travestite da card del dashboard.
      </p>

      <div style={{
        display:"grid",
        gridTemplateColumns:"1.1fr 50px 1.6fr",
        gap:20,
        alignItems:"start",
        marginBottom: 24,
      }}>
        {/* LEFT — current state */}
        <div>
          <div style={{
            fontSize:11,fontWeight:700,letterSpacing:".08em",textTransform:"uppercase",
            color:"var(--c-text-mute)",marginBottom:14,
          }}>Oggi · 6 tab piatti + dashboard sovraccarico</div>

          <div style={{
            background:"#fff",
            border:"1px solid var(--c-line)",
            borderRadius:14,
            padding:8,
            marginBottom:14,
          }}>
            <div style={{
              fontSize:10,fontWeight:700,letterSpacing:".06em",textTransform:"uppercase",
              color:"var(--c-red)",padding:"6px 10px 4px",
            }}>Configurazione attuale</div>
            {oldTabs.map(t => (
              <div key={t.label} style={{
                display:"flex",alignItems:"center",justifyContent:"space-between",
                padding:"9px 14px",
                fontSize:13,
                fontWeight:500,
                color:"var(--c-navy)",
                borderTop:"1px solid var(--c-line-soft)",
              }}>
                <span>{t.label}</span>
                {t.renamed && <span style={{
                  fontSize:9,fontWeight:700,letterSpacing:".04em",
                  background:"#fef3c7",color:"#92400e",
                  padding:"1px 5px",borderRadius:3,
                }}>RINOMINATO</span>}
              </div>
            ))}
          </div>

          <div style={{
            background:"#fff",
            border:"1px solid var(--c-line)",
            borderRadius:14,
            padding:8,
          }}>
            <div style={{
              fontSize:10,fontWeight:700,letterSpacing:".06em",textTransform:"uppercase",
              color:"#0ea5e9",padding:"6px 10px 4px",
            }}>Da dashboard genitore (migrate qui)</div>
            {migrated.map(t => (
              <div key={t.label} style={{
                display:"flex",alignItems:"center",justifyContent:"space-between",
                padding:"9px 14px",
                fontSize:13,
                fontWeight:500,
                color:"var(--c-navy)",
                borderTop:"1px solid var(--c-line-soft)",
              }}>
                <span style={{display:"flex",alignItems:"center",gap:6}}>
                  {t.label}
                  <span style={{
                    fontSize:9.5,color:"var(--c-text-faint)",fontStyle:"italic",
                  }}>{t.from}</span>
                </span>
                <span style={{
                  fontSize:9,fontWeight:700,letterSpacing:".04em",
                  background:"rgba(245,158,11,.15)",color:"#92400e",
                  padding:"1px 5px",borderRadius:3,
                }}>↓ {t.where}</span>
              </div>
            ))}
          </div>
        </div>

        {/* MIDDLE — arrow */}
        <div style={{
          display:"flex",alignItems:"center",justifyContent:"center",
          paddingTop:140,
        }}>
          <svg width="50" height="120" viewBox="0 0 50 120" fill="none">
            <path d="M5 60 L40 60" stroke="#e1474f" strokeWidth="2" strokeLinecap="round" strokeDasharray="2 5"/>
            <path d="M32 50 L42 60 L32 70" stroke="#e1474f" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" fill="none"/>
          </svg>
        </div>

        {/* RIGHT — new grouped */}
        <div>
          <div style={{
            fontSize:11,fontWeight:700,letterSpacing:".08em",textTransform:"uppercase",
            color:"var(--c-text-mute)",marginBottom:14,
            display:"flex",alignItems:"center",gap:10,
          }}>
            Proposta · 4 gruppi, 12 voci
            <span style={{
              background:"var(--c-red)",color:"#fff",
              padding:"2px 8px",borderRadius:4,fontSize:9,letterSpacing:".04em",
            }}>HUB IMPOSTAZIONI</span>
          </div>
          <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:12}}>
            {newGroups.map(g => (
              <div key={g.title} style={{
                background:"#fff",
                border:"1px solid var(--c-line)",
                borderRadius:14,
                padding:14,
              }}>
                <div style={{
                  display:"flex",alignItems:"center",gap:8,marginBottom:10,
                  paddingBottom:8,borderBottom:"1px dashed var(--c-line)",
                }}>
                  <div style={{
                    width:8,height:8,borderRadius:2,background:g.color,
                  }}/>
                  <div style={{
                    fontFamily:"var(--c-display)",
                    fontStyle:"italic",fontWeight:400,fontSize:18,
                    color:"var(--c-navy)",
                    letterSpacing:"-.005em",
                  }}>{g.title}</div>
                  <div style={{
                    marginLeft:"auto",
                    fontSize:10,color:"var(--c-text-faint)",fontWeight:600,
                  }}>{g.items.length}</div>
                </div>
                {g.items.map(it => (
                  <div key={it.label} style={{
                    display:"flex",alignItems:"center",justifyContent:"space-between",
                    padding:"5px 0",
                    fontSize:12.5,
                    fontWeight: it.isNew ? 400 : 500,
                    color: it.isNew ? "var(--c-text-faint)" : "var(--c-navy)",
                  }}>
                    <span style={{display:"flex",alignItems:"center",gap:6}}>
                      {it.label}
                      {it.src && !it.migrated && !it.renamed && (
                        <span style={{
                          fontSize:9.5,color:"var(--c-text-faint)",fontStyle:"italic",
                        }}>era: {it.src}</span>
                      )}
                      {it.renamed && (
                        <span style={{
                          fontSize:9.5,color:"#92400e",fontStyle:"italic",
                        }}>era: {it.src}</span>
                      )}
                    </span>
                    {it.isNew && (
                      <span style={{
                        fontSize:9,fontWeight:700,letterSpacing:".04em",
                        background:"#fef3c7",color:"#92400e",
                        padding:"1px 5px",borderRadius:3,
                      }}>SOON</span>
                    )}
                    {it.migrated && (
                      <span style={{
                        fontSize:9,fontWeight:700,letterSpacing:".04em",
                        background:"rgba(245,158,11,.15)",color:"#92400e",
                        padding:"1px 5px",borderRadius:3,
                      }}>↓ {it.src}</span>
                    )}
                  </div>
                ))}
              </div>
            ))}
          </div>
          <div style={{
            marginTop:14,padding:"12px 14px",
            background:"#dcfce7",
            border:"1px dashed #86efac",
            borderRadius:10,
            fontSize:12.5,
            color:"#166534",
            lineHeight:1.5,
          }}>
            <b>Regola euristica:</b> "se la guardo una volta e dimentico" → Configurazione. "se la apro per fare qualcosa o controllare" → Dashboard. Risultato: l'utente cerca le impostazioni in <b>un solo posto</b>, e il dashboard diventa un vero centro operativo.
          </div>
        </div>
      </div>

      {/* What stays in the parent dashboard */}
      <div style={{
        padding:"20px 22px",
        background:"#fff",
        border:"1px solid var(--c-line)",
        borderRadius:14,
      }}>
        <div style={{display:"flex",alignItems:"baseline",gap:12,marginBottom:14}}>
          <div style={{
            fontFamily:"var(--c-display)",
            fontStyle:"italic",fontWeight:400,fontSize:22,
            color:"var(--c-navy)",
            letterSpacing:"-.005em",
          }}>Dashboard genitore — resta come centro operativo</div>
          <span style={{fontSize:12.5,color:"var(--c-text-mute)"}}>
            azioni + dati · 12 card · <span className="text-mono">?page=olobuild</span>
          </span>
        </div>
        <div style={{display:"grid",gridTemplateColumns:"repeat(4, 1fr)",gap:8}}>
          {stays.map(e => (
            <div key={e.label} style={{
              display:"flex",alignItems:"center",gap:10,
              padding:"9px 12px",
              background:"var(--c-bg)",
              borderRadius:8,
              fontSize:12.5,
            }}>
              <div style={{
                width:5,height:5,borderRadius:"50%",
                background: e.kind === "action" ? "#15803d" : "#0ea5e9",
              }}/>
              <div style={{flex:1,fontWeight:500,color:"var(--c-navy)"}}>{e.label}</div>
              <div style={{
                fontSize:9.5,fontWeight:700,letterSpacing:".04em",
                color: e.kind === "action" ? "#15803d" : "#0ea5e9",
                textTransform:"uppercase",
              }}>{e.kind}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

window.IAMap = IAMap;
