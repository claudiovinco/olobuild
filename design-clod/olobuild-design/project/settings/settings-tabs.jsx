// OLObuild — Configurazione: tab content components
// Stesso contenuto in entrambe le varianti visive (lo styling cambia
// via [data-variant] sul root). Cards + righe label/control.

const CfgRow = ({ label, hint, required, children, noDivider }) => (
  <div className={"cfg-row" + (noDivider ? " no-divider" : "")}>
    <div className="label-col">
      <label>{label}{required && <span className="req">*</span>}</label>
      {hint && <div className="hint">{hint}</div>}
    </div>
    <div>{children}</div>
  </div>
);

const CfgInput = ({ value, prefix, suffix, mono, password, placeholder }) => (
  <div className={"cfg-input" + (mono ? " mono" : "") + (prefix ? " with-prefix" : "") + (suffix ? " with-suffix" : "") + (password ? " password" : "")}>
    {prefix && <span className="prefix">{prefix}</span>}
    <input defaultValue={value} placeholder={placeholder}/>
    {password && <span className="reveal"><CfgIcon.Eye/></span>}
    {suffix && <span className="suffix">{suffix}</span>}
  </div>
);

const CfgSelect = ({ value, options }) => (
  <div className="cfg-select">
    <select defaultValue={value}>
      {options.map(o => <option key={o.value || o} value={o.value || o}>{o.label || o}</option>)}
    </select>
    <span className="chev"><CfgIcon.ChevD/></span>
  </div>
);

const CfgSwitch = ({ on }) => <div className={"cfg-switch" + (on ? " is-on" : "")}/>;

const CfgSlider = ({ pct = 50, val = "50" }) => (
  <div className="cfg-slider">
    <div className="track">
      <div className="fill" style={{width: pct + "%"}}/>
      <div className="knob" style={{left: pct + "%"}}/>
    </div>
    <div className="val">{val}</div>
  </div>
);

// ─────────────────────────────────────────────────────────────────
// PRESETS — TAB 1
// ─────────────────────────────────────────────────────────────────
function TabPresets() {
  const presets = [
    { id:"default",   name:"Default",   tag:"Equilibrato",      colors:["#8b5cf6","#1e293b","#f8fafc","#22c55e"], active: true },
    { id:"corporate", name:"Corporate", tag:"Sobrio · B2B",     colors:["#1d4ed8","#0f172a","#f1f5f9","#3b82f6"] },
    { id:"creative",  name:"Creative",  tag:"Editoriale",       colors:["#ec4899","#0f172a","#fef3f3","#fbbf24"] },
    { id:"dark",      name:"Dark",      tag:"Modalità scura",   colors:["#fbbf24","#0f172a","#1e293b","#a78bfa"] },
    { id:"ecommerce", name:"E-commerce",tag:"Conversione alta", colors:["#dc2626","#1e293b","#fef2f2","#15803d"] },
    { id:"luxury",    name:"Luxury",    tag:"Hotel & resort",   colors:["#92400e","#1c1917","#fef3c7","#a16207"] },
    { id:"editorial", name:"Editorial", tag:"Magazine",         colors:["#e1474f","#0f172a","#fbf5e8","#4a574e"] },
    { id:"minimal",   name:"Minimal",   tag:"Bianco & nero",    colors:["#000000","#525252","#fafafa","#a3a3a3"] },
  ];
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Stili <em>& Preset</em></h1>
          <p>Applica un set predefinito di stili globali con un click. Colori, tipografia e proporzioni vengono sovrascritti — i preset sono il punto di partenza più veloce per un nuovo sito.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Download/> Esporta preset</button>
          <button className="cfg-btn cfg-btn-primary"><CfgIcon.Plus/> Crea preset</button>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Layers/></div>
          <div>
            <h3>Preset disponibili</h3>
            <p>8 preset di sistema · 0 personalizzati. Clicca una card per fare l'anteprima sul sito.</p>
          </div>
          <div className="head-actions">
            <div className="cfg-segment">
              <button className="is-on">Sistema</button>
              <button>Personalizzati</button>
              <button>Marketplace</button>
            </div>
          </div>
        </div>
        <div className="cfg-card-body">
          <div className="grid-4" style={{gap:14}}>
            {presets.map(p => (
              <div key={p.id} style={{
                background:"#fff",
                border: p.active ? "2px solid var(--c-red)" : "1px solid var(--c-line)",
                borderRadius:12,
                padding:14,
                cursor:"pointer",
                position:"relative",
              }}>
                {p.active && <span className="cfg-pill ok" style={{position:"absolute",top:10,right:10}}><CfgIcon.Check/> Attivo</span>}
                <div style={{display:"flex",gap:4,marginBottom:14}}>
                  {p.colors.map((c,i)=>(<div key={i} style={{flex:1,height:54,background:c,borderRadius:i===0?"6px 2px 2px 6px":i===p.colors.length-1?"2px 6px 6px 2px":2}}/>))}
                </div>
                <div style={{fontWeight:600,fontSize:14,color:"var(--c-navy)"}}>{p.name}</div>
                <div className="text-xs text-mute" style={{marginTop:2}}>{p.tag}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Save/></div>
          <div>
            <h3>Comportamento dei preset</h3>
            <p>Come gestire l'applicazione di un nuovo preset rispetto alle tue modifiche.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Sovrascrivi modifiche manuali" hint="Quando applichi un preset, anche le modifiche fatte a mano vengono ripristinate. Se disattivato, le tue modifiche restano e i valori del preset si fondono.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Crea snapshot prima di applicare" hint="Salva una copia degli stili attuali, così puoi tornare indietro con un click.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Modalità anteprima" hint="Visualizza il preset sul sito senza salvarlo finché non clicchi Conferma.">
            <CfgSelect value="prima" options={[{value:"prima",label:"Mostra prima/dopo affiancati"},{value:"live",label:"Applica live e annulla con Esc"},{value:"off",label:"Disattiva anteprima"}]}/>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// COLORI — TAB 2
// ─────────────────────────────────────────────────────────────────
function TabColori() {
  const palette = [
    { name:"Primary",   role:"Brand · CTA · link",       hex:"#E1474F" },
    { name:"Secondary", role:"Accenti · highlight",      hex:"#0F172A" },
    { name:"Tertiary",  role:"Decorazione · skeleton",   hex:"#F3EDE2" },
    { name:"Success",   role:"Stato positivo",           hex:"#15803D" },
    { name:"Warning",   role:"Stato attenzione",         hex:"#B45309" },
    { name:"Danger",    role:"Stato errore",             hex:"#DC2626" },
  ];
  const neutrals = ["#FAFAFA","#F4F4F5","#E4E4E7","#A1A1AA","#52525B","#27272A","#09090B"];
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Palette <em>colori</em></h1>
          <p>I colori globali del sito. Le modifiche si propagano a tutti i template, gli elementi del builder e gli stili dei post type.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Drop/> Importa da Coolors</button>
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Sparkles/> Genera con AI</button>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Palette/></div>
          <div>
            <h3>Colori del brand</h3>
            <p>Ogni colore ha un ruolo. Cambialo e ovunque sul sito si aggiorna di conseguenza.</p>
          </div>
          <div className="head-actions">
            <span className="cfg-pill ok"><span className="dot"/> AA Verificato</span>
          </div>
        </div>
        <div className="cfg-card-body">
          <div style={{display:"grid",gap:10}}>
            {palette.map(c => (
              <div key={c.name} style={{
                display:"grid",
                gridTemplateColumns:"56px 1fr 220px 36px",
                gap:14,
                alignItems:"center",
                padding:"10px 12px",
                background:"#fff",
                border:"1px solid var(--c-line-soft)",
                borderRadius:10,
              }}>
                <div style={{width:56,height:56,borderRadius:8,background:c.hex,boxShadow:"inset 0 0 0 1px rgba(0,0,0,.06)"}}/>
                <div>
                  <div style={{fontWeight:600,fontSize:14,color:"var(--c-navy)"}}>{c.name}</div>
                  <div className="text-xs text-mute" style={{marginTop:2}}>{c.role}</div>
                </div>
                <CfgInput value={c.hex} prefix="#" mono/>
                <button className="cfg-btn cfg-btn-icon cfg-btn-ghost"><CfgIcon.ChevR/></button>
              </div>
            ))}
            <button className="cfg-btn cfg-btn-secondary" style={{justifyContent:"center",borderStyle:"dashed",marginTop:4}}>
              <CfgIcon.Plus/> Aggiungi colore custom
            </button>
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Layers/></div>
          <div>
            <h3>Scala neutri</h3>
            <p>Sfumature grigie utilizzate per testi, bordi, sfondi e stati disabilitati.</p>
          </div>
          <div className="head-actions">
            <div className="cfg-segment">
              <button className="is-on">Auto</button>
              <button>Manuale</button>
            </div>
          </div>
        </div>
        <div className="cfg-card-body">
          <div style={{display:"flex",gap:6}}>
            {neutrals.map((c,i) => (
              <div key={i} style={{flex:1}}>
                <div style={{height:64,background:c,borderRadius:8,boxShadow:"inset 0 0 0 1px rgba(0,0,0,.06)"}}/>
                <div className="text-xs text-mono" style={{textAlign:"center",marginTop:6,color:"var(--c-text-mute)"}}>{i*100 || 50}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Eye/></div>
          <div>
            <h3>Modalità dark</h3>
            <p>Configura come la palette si adatta automaticamente al tema scuro.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Abilita modalità dark" hint="Mostra il selettore dark/light nell'header del sito.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Strategia di inversione" hint="Come generare i colori scuri.">
            <CfgSelect value="auto" options={[{value:"auto",label:"Automatica (consigliata)"},{value:"manual",label:"Manuale, palette separata"},{value:"none",label:"Solo aggiusta luminanza"}]}/>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// TIPOGRAFIA — TAB 3
// ─────────────────────────────────────────────────────────────────
function TabTipografia() {
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Tipografia <em>globale</em></h1>
          <p>Coppia di font, scala modulare, pesi e interlinea. Si applica a tutti i blocchi di testo del sito — sovrascrivibile a livello di pagina.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Plus/> Carica font custom</button>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Type/></div>
          <div>
            <h3>Coppia di font</h3>
            <p>Display per titoli, body per testo corrente. Tieni 2 famiglie max per buona leggibilità.</p>
          </div>
        </div>
        <div className="cfg-card-body">
          <div className="grid-2">
            <div style={{background:"#fff",border:"1px solid var(--c-line-soft)",borderRadius:12,padding:18}}>
              <div className="text-xs text-faint fw-600" style={{textTransform:"uppercase",letterSpacing:".08em",marginBottom:8}}>Display · titoli</div>
              <div style={{fontFamily:"Instrument Serif",fontSize:46,lineHeight:1,letterSpacing:"-.02em",margin:"6px 0 12px",color:"var(--c-navy)"}}>Abc · Æg</div>
              <CfgSelect value="instrument" options={[{value:"instrument",label:"Instrument Serif"},{value:"playfair",label:"Playfair Display"},{value:"fraunces",label:"Fraunces"},{value:"dmserif",label:"DM Serif Display"}]}/>
              <div className="flex gap-2 mt-3">
                <span className="cfg-pill off">italic</span>
                <span className="cfg-pill off">400</span>
                <span className="cfg-pill ok">+ Google Fonts</span>
              </div>
            </div>
            <div style={{background:"#fff",border:"1px solid var(--c-line-soft)",borderRadius:12,padding:18}}>
              <div className="text-xs text-faint fw-600" style={{textTransform:"uppercase",letterSpacing:".08em",marginBottom:8}}>Body · testo corrente</div>
              <div style={{fontFamily:"Work Sans",fontWeight:500,fontSize:32,lineHeight:1.1,margin:"6px 0 12px",color:"var(--c-navy)"}}>Abc · 123</div>
              <CfgSelect value="worksans" options={[{value:"worksans",label:"Work Sans"},{value:"inter",label:"Inter"},{value:"manrope",label:"Manrope"},{value:"system",label:"System UI stack"}]}/>
              <div className="flex gap-2 mt-3">
                <span className="cfg-pill off">400</span>
                <span className="cfg-pill off">500</span>
                <span className="cfg-pill off">600</span>
                <span className="cfg-pill off">700</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Layers/></div>
          <div>
            <h3>Scala tipografica</h3>
            <p>Ratio matematico tra le grandezze. Anteprima live a destra.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Dimensione base" hint="Punto di partenza, di solito 16px per il corpo del testo.">
            <CfgInput value="16" suffix="px" mono/>
          </CfgRow>
          <CfgRow label="Ratio scala" hint="Più alto = differenza più marcata fra body e h1.">
            <CfgSelect value="1.25" options={[{value:"1.125",label:"1.125 · Major Second"},{value:"1.2",label:"1.2 · Minor Third"},{value:"1.25",label:"1.25 · Major Third"},{value:"1.333",label:"1.333 · Perfect Fourth"},{value:"1.414",label:"1.414 · Augmented Fourth"},{value:"1.5",label:"1.5 · Perfect Fifth"}]}/>
          </CfgRow>
          <CfgRow label="Interlinea body" hint="Altezza riga del testo corrente.">
            <CfgSlider pct={65} val="1.55"/>
          </CfgRow>
          <CfgRow label="Anteprima scala" noDivider>
            <div style={{background:"var(--c-bg)",borderRadius:10,padding:18,display:"grid",gap:10}}>
              <div style={{fontFamily:"Instrument Serif",fontSize:48,lineHeight:1,color:"var(--c-navy)"}}>H1 — 48 / 1.0</div>
              <div style={{fontFamily:"Instrument Serif",fontSize:36,lineHeight:1.05,color:"var(--c-navy)"}}>H2 — 36 / 1.05</div>
              <div style={{fontFamily:"Work Sans",fontWeight:600,fontSize:24,color:"var(--c-navy)"}}>H3 — 24 / 1.2</div>
              <div style={{fontFamily:"Work Sans",fontSize:16,lineHeight:1.55,color:"var(--c-text)"}}>Body — 16 / 1.55 — Lorem ipsum dolor sit amet consectetur adipiscing elit.</div>
              <div style={{fontFamily:"Work Sans",fontSize:13,color:"var(--c-text-mute)"}}>Small — 13 / 1.5 — Caption, meta, didascalie.</div>
            </div>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// AI ASSISTANT — TAB 4
// ─────────────────────────────────────────────────────────────────
function TabAI() {
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>AI <em>Assistant</em></h1>
          <p>Generazione testi, alt-text immagini, traduzioni e suggerimenti UX all'interno del builder. Le chiamate vengono fatturate dal provider AI che scegli.</p>
        </div>
        <div className="head-actions">
          <span className="cfg-pill ok"><span className="dot"/> Provider connesso</span>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Sparkles/></div>
          <div>
            <h3>Provider</h3>
            <p>Scegli il modello che muove le funzioni AI dentro l'editor.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Provider AI" hint="Puoi cambiarlo in qualsiasi momento — la chiave API è salvata per provider.">
            <div className="cfg-segment">
              <button className="is-on">OpenAI</button>
              <button>Anthropic</button>
              <button>Mistral</button>
              <button>Self-hosted</button>
            </div>
          </CfgRow>
          <CfgRow label="API key" hint="La chiave è criptata nel database. Inseriscila una sola volta." required>
            <CfgInput value="sk-proj-•••••••••••••••••••••••••••aF3z" mono password/>
          </CfgRow>
          <CfgRow label="Modello" hint="Modelli più potenti = risposte migliori ma costo maggiore per chiamata.">
            <CfgSelect value="gpt4o" options={[{value:"gpt4o",label:"gpt-4o · qualità/prezzo equilibrato"},{value:"gpt4omini",label:"gpt-4o-mini · economico, veloce"},{value:"o1",label:"o1 · ragionamento avanzato"}]}/>
          </CfgRow>
          <CfgRow label="Budget mensile" hint="Soglia di spesa oltre la quale le funzioni AI vengono disattivate.">
            <CfgInput value="50.00" prefix="€" mono suffix="/ mese"/>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Wrench/></div>
          <div>
            <h3>Comportamento</h3>
            <p>Tono, lingua e creatività del modello.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Lingua di default" hint="Il modello risponderà in questa lingua se non specifichi altrimenti.">
            <CfgSelect value="it" options={[{value:"it",label:"Italiano"},{value:"en",label:"Inglese"},{value:"auto",label:"Auto (lingua del sito)"}]}/>
          </CfgRow>
          <CfgRow label="Temperatura" hint="0 = preciso e ripetibile · 1 = creativo e variabile.">
            <CfgSlider pct={35} val="0.35"/>
          </CfgRow>
          <CfgRow label="Tono di voce" hint="Personalità di base che il modello adotta in tutti i task.">
            <CfgSegment/>
          </CfgRow>
          <CfgRow label="Istruzioni di sistema" hint="Contesto del brand che viene incluso in ogni chiamata. Max 500 caratteri." noDivider>
            <div className="cfg-textarea">
              <textarea defaultValue={"Sei l'assistente di scrittura per un sito di hotel boutique sul lago di Como. Tono caldo, professionale, mai gergale. Lingua italiana. Quando suggerisci copy, mantieni le frasi sotto le 20 parole."}/>
            </div>
            <div className="text-xs text-faint mt-2">312 / 500 caratteri</div>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Bar/></div>
          <div>
            <h3>Utilizzo questo mese</h3>
            <p>Statistiche delle chiamate API negli ultimi 30 giorni.</p>
          </div>
        </div>
        <div className="cfg-card-body">
          <div className="grid-4">
            {[
              {l:"Chiamate",v:"1.247",t:"+18% vs scorso mese"},
              {l:"Token usati",v:"894 k",t:"out 312k · in 582k"},
              {l:"Spesa stimata",v:"€ 8,40",t:"di €50 budget"},
              {l:"Latenza media",v:"1.2 s",t:"ottima"},
            ].map(s => (
              <div key={s.l} style={{background:"var(--c-bg)",borderRadius:10,padding:14}}>
                <div className="text-xs text-mute fw-600" style={{textTransform:"uppercase",letterSpacing:".06em"}}>{s.l}</div>
                <div style={{fontFamily:"Instrument Serif",fontSize:32,lineHeight:1,marginTop:6,color:"var(--c-navy)"}}>{s.v}</div>
                <div className="text-xs text-faint" style={{marginTop:4}}>{s.t}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </>
  );
}

function CfgSegment() {
  return (
    <div className="cfg-segment">
      <button>Neutrale</button>
      <button className="is-on">Caldo</button>
      <button>Tecnico</button>
      <button>Editoriale</button>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────
// STOCK MEDIA — TAB 5  (split out from "API & Integrazioni")
// ─────────────────────────────────────────────────────────────────
function TabStockMedia() {
  const services = [
    { id:"unsplash", name:"Unsplash", desc:"3M+ foto royalty-free, alta qualità editoriale", status:"ok",   key:"client-id_•••••••••42b1" },
    { id:"pexels",   name:"Pexels",   desc:"1M+ foto e video, license CC0",                  status:"ok",   key:"563492ad6f9170000••••42af" },
    { id:"pixabay",  name:"Pixabay",  desc:"4M+ media, anche illustrazioni e vector",        status:"off",  key:"" },
    { id:"icons8",   name:"Icons8",   desc:"Icone e illustrazioni in 30+ stili",             status:"warn", key:"abc-•••••••-fz9" },
  ];
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Stock <em>media</em></h1>
          <p>Connetti i provider di immagini gratuite per cercarli e inserirli direttamente dall'editor — senza scaricare/ricaricare a mano.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Book/> Come ottenere le chiavi</button>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Image/></div>
          <div>
            <h3>Provider connessi</h3>
            <p>2 attivi · 1 con warning · 1 disconnesso. Click su una riga per inserire/aggiornare la chiave.</p>
          </div>
        </div>
        <div className="cfg-card-body">
          <div style={{display:"grid",gap:10}}>
            {services.map(s => (
              <div key={s.id} style={{
                display:"grid",
                gridTemplateColumns:"48px 1fr 280px 110px 36px",
                gap:14,
                alignItems:"center",
                padding:"14px 16px",
                background:"#fff",
                border:"1px solid var(--c-line-soft)",
                borderRadius:10,
              }}>
                <div style={{width:48,height:48,borderRadius:10,background:"var(--c-bg)",display:"grid",placeItems:"center",color:"var(--c-navy)",fontWeight:700,fontFamily:"Instrument Serif",fontSize:22}}>{s.name[0]}</div>
                <div>
                  <div style={{fontWeight:600,fontSize:14,color:"var(--c-navy)"}}>{s.name}</div>
                  <div className="text-xs text-mute" style={{marginTop:2}}>{s.desc}</div>
                </div>
                {s.key
                  ? <div className="cfg-input mono" style={{padding:"6px 10px",fontSize:11.5}}><input defaultValue={s.key} readOnly/></div>
                  : <button className="cfg-btn cfg-btn-secondary" style={{justifyContent:"center"}}><CfgIcon.Key/> Aggiungi chiave</button>
                }
                {s.status === "ok"   && <span className="cfg-pill ok"><span className="dot"/> Connesso</span>}
                {s.status === "warn" && <span className="cfg-pill warn"><span className="dot"/> Scadenza vicina</span>}
                {s.status === "off"  && <span className="cfg-pill off"><span className="dot"/> Non connesso</span>}
                <button className="cfg-btn cfg-btn-icon cfg-btn-ghost"><CfgIcon.ChevR/></button>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Wrench/></div>
          <div>
            <h3>Comportamento default</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Provider preferito" hint="Quello che si apre per primo dalla ricerca media nell'editor.">
            <CfgSelect value="unsplash" options={[{value:"unsplash",label:"Unsplash"},{value:"pexels",label:"Pexels"},{value:"pixabay",label:"Pixabay"}]}/>
          </CfgRow>
          <CfgRow label="Scarica in locale" hint="Quando inserisci un'immagine, viene scaricata nella Libreria media di WordPress. Disattivato = hotlink al provider.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Ottimizza al download" hint="Comprime e converte in WebP automaticamente. Richiede modulo Performance attivo." noDivider>
            <CfgSwitch on={false}/>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// API & INTEGRAZIONI — TAB 6
// ─────────────────────────────────────────────────────────────────
function TabAPI() {
  const ints = [
    { name:"Google Maps",      desc:"Mappe interattive nei template",          status:"ok",  cat:"Mappe" },
    { name:"Mapbox",           desc:"Mappe vettoriali ad alta densità",        status:"off", cat:"Mappe" },
    { name:"Mailchimp",        desc:"Newsletter dai form OLObuild",            status:"ok",  cat:"Email marketing" },
    { name:"Brevo (ex Sendinblue)", desc:"Email transazionali e marketing",    status:"off", cat:"Email marketing" },
    { name:"reCAPTCHA v3",     desc:"Protezione anti-spam dei form",           status:"ok",  cat:"Sicurezza" },
    { name:"Cloudinary",       desc:"CDN media + trasformazioni live",         status:"off", cat:"Media CDN" },
    { name:"Stripe",           desc:"Pagamenti per form e prenotazioni",       status:"off", cat:"Pagamenti" },
    { name:"Zapier webhook",   desc:"Esporta dati form a qualsiasi servizio",  status:"ok",  cat:"Automazione" },
  ];
  const groups = [...new Set(ints.map(i => i.cat))];
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>API <em>& Integrazioni</em></h1>
          <p>Connetti servizi esterni — mappe, newsletter, anti-spam, pagamenti, automazioni. Le chiavi sono salvate criptate nel database.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Plus/> Aggiungi webhook custom</button>
        </div>
      </div>

      {groups.map(cat => (
        <div key={cat} className="cfg-card">
          <div className="cfg-card-head">
            <div className="head-ic"><CfgIcon.Plug/></div>
            <div>
              <h3>{cat}</h3>
              <p>{ints.filter(i => i.cat === cat).length} servizi disponibili</p>
            </div>
          </div>
          <div className="cfg-card-body" style={{padding:14}}>
            <div className="grid-2" style={{gap:10}}>
              {ints.filter(i => i.cat === cat).map(s => (
                <div key={s.name} style={{
                  display:"grid",
                  gridTemplateColumns:"40px 1fr auto",
                  gap:12,
                  alignItems:"center",
                  padding:"12px 14px",
                  background:"#fff",
                  border:"1px solid var(--c-line-soft)",
                  borderRadius:10,
                }}>
                  <div style={{width:40,height:40,borderRadius:8,background:"var(--c-bg)",display:"grid",placeItems:"center",color:"var(--c-text-mute)"}}>
                    <CfgIcon.Plug/>
                  </div>
                  <div>
                    <div style={{fontWeight:600,fontSize:13.5,color:"var(--c-navy)"}}>{s.name}</div>
                    <div className="text-xs text-mute" style={{marginTop:2}}>{s.desc}</div>
                  </div>
                  {s.status === "ok"
                    ? <span className="cfg-pill ok"><span className="dot"/> Connesso</span>
                    : <button className="cfg-btn cfg-btn-secondary" style={{padding:"5px 10px"}}>Connetti</button>
                  }
                </div>
              ))}
            </div>
          </div>
        </div>
      ))}
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// RESPONSIVE — TAB 7
// ─────────────────────────────────────────────────────────────────
function TabResponsive() {
  const bps = [
    { name:"Desktop XL", min:"1440", max:"∞",    icon:"💻", active:false },
    { name:"Desktop",    min:"1200", max:"1439", icon:"🖥️", active:true },
    { name:"Laptop",     min:"992",  max:"1199", icon:"💻", active:false },
    { name:"Tablet",     min:"768",  max:"991",  icon:"📱", active:false },
    { name:"Mobile L",   min:"576",  max:"767",  icon:"📱", active:false },
    { name:"Mobile",     min:"0",    max:"575",  icon:"📱", active:false },
  ];
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Breakpoint <em>responsive</em></h1>
          <p>I punti di transizione dove il layout cambia. Sono le viewport che vedi nell'editor in alto per testare il design.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Undo/> Ripristina default</button>
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Plus/> Aggiungi breakpoint</button>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Devices/></div>
          <div>
            <h3>Visualizzazione scala</h3>
            <p>Anteprima della copertura dei tuoi breakpoint sulle larghezze reali.</p>
          </div>
        </div>
        <div className="cfg-card-body">
          <div style={{
            position:"relative",
            height:60,
            background:"var(--c-bg)",
            borderRadius:10,
            border:"1px solid var(--c-line-soft)",
            overflow:"hidden",
          }}>
            {[
              {l:"Mobile",pct:24,color:"#fde2e4"},
              {l:"Tablet",pct:18,color:"#fbd5d8"},
              {l:"Laptop",pct:20,color:"#f5959c"},
              {l:"Desktop",pct:22,color:"#ec5a62"},
              {l:"XL",pct:16,color:"#c8323a"},
            ].reduce((acc,s,i)=>{
              const left = acc.cum;
              acc.cum += s.pct;
              acc.bars.push(
                <div key={i} style={{
                  position:"absolute",
                  left:left+"%", width:s.pct+"%",
                  top:0, bottom:0,
                  background:s.color,
                  display:"flex",alignItems:"center",justifyContent:"center",
                  color: i<3 ? "#7a1d23" : "#fff",
                  fontWeight:600,fontSize:12,
                  borderRight: i<4 ? "1px solid rgba(0,0,0,.08)" : "0",
                }}>{s.l}</div>
              );
              return acc;
            }, {cum:0, bars:[]}).bars}
          </div>
          <div className="flex justify-between text-xs text-faint mt-2" style={{fontFamily:"var(--c-mono)"}}>
            <span>0</span><span>576</span><span>768</span><span>992</span><span>1200</span><span>1440</span><span>∞</span>
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Layers/></div>
          <div>
            <h3>Breakpoint configurati</h3>
            <p>Trascina per riordinare. Il primo dall'alto è il device di default in cui si apre l'editor.</p>
          </div>
        </div>
        <div className="cfg-card-body">
          <div style={{display:"grid",gap:8}}>
            <div style={{
              display:"grid",
              gridTemplateColumns:"28px 36px 1fr 100px 100px 80px 36px",
              gap:10,
              fontSize:11,
              fontWeight:700,
              letterSpacing:".06em",
              textTransform:"uppercase",
              color:"var(--c-text-faint)",
              padding:"0 12px 6px",
            }}>
              <span/><span/><span>Nome</span><span>Da (px)</span><span>A (px)</span><span>Default</span><span/>
            </div>
            {bps.map(b => (
              <div key={b.name} style={{
                display:"grid",
                gridTemplateColumns:"28px 36px 1fr 100px 100px 80px 36px",
                gap:10,
                alignItems:"center",
                padding:"10px 12px",
                background:"#fff",
                border: b.active ? "1.5px solid var(--c-red-soft-2)" : "1px solid var(--c-line-soft)",
                borderRadius:10,
              }}>
                <div style={{color:"var(--c-text-faint)",cursor:"grab",display:"grid",placeItems:"center"}}>⋮⋮</div>
                <div style={{fontSize:18}}>{b.icon}</div>
                <div style={{fontWeight:600,fontSize:14,color:"var(--c-navy)"}}>{b.name}</div>
                <div className="cfg-input mono" style={{padding:"5px 8px"}}><input defaultValue={b.min}/></div>
                <div className="cfg-input mono" style={{padding:"5px 8px"}}><input defaultValue={b.max}/></div>
                <CfgSwitch on={b.active}/>
                <button className="cfg-btn cfg-btn-icon cfg-btn-ghost"><CfgIcon.X/></button>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Wrench/></div>
          <div>
            <h3>Comportamento avanzato</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Strategia generazione CSS" hint="Mobile-first usa min-width, desktop-first usa max-width.">
            <div className="cfg-segment">
              <button className="is-on">Mobile-first (consigliata)</button>
              <button>Desktop-first</button>
            </div>
          </CfgRow>
          <CfgRow label="Container width massima" hint="Larghezza max dei contenuti, oltre la quale il layout resta centrato.">
            <CfgInput value="1200" suffix="px" mono/>
          </CfgRow>
          <CfgRow label="Gutter laterale" hint="Padding orizzontale del wrapper su tutti i breakpoint." noDivider>
            <CfgSlider pct={24} val="24 px"/>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// IMPORT / EXPORT — TAB 8
// ─────────────────────────────────────────────────────────────────
function TabImportExport() {
  const [mode, setMode] = React.useState("template"); // template | site | backup
  const recents = [
    { kind:"export", target:"Sito completo",                date:"oggi · 14:32", size:"4.2 MB", by:"luca@hotel.it" },
    { kind:"import", target:"home-luxury-v3.json",          date:"ieri · 11:08", size:"1.1 MB", by:"luca@hotel.it" },
    { kind:"export", target:"Template · Pagina contatti",   date:"22 mag · 09:15", size:"248 KB", by:"agenzia@studio.it" },
    { kind:"export", target:"Backup completo (DB + media)", date:"15 mag · 03:00", size:"312 MB", by:"sistema (schedulato)" },
    { kind:"import", target:"resort-template.json",         date:"08 mag · 16:42", size:"890 KB", by:"luca@hotel.it" },
  ];
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Importa <em>/ Esporta</em></h1>
          <p>Sposta template, interi siti e backup completi tra installazioni OLObuild. Tutto in formato <span className="text-mono text-sm">.json</span>, con o senza media inclusi.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Refresh/> Backup schedulati</button>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Download/></div>
          <div>
            <h3>Cosa vuoi spostare?</h3>
            <p>Scegli il livello: un singolo template, l'intero sito o un backup completo (database + file).</p>
          </div>
        </div>
        <div className="cfg-card-body">
          <div className="grid-3" style={{gap:12}}>
            {[
              { id:"template", icon:"Layers",  title:"Singolo template", desc:"Una pagina o un blocco riutilizzabile", meta:"5 disponibili" },
              { id:"site",     icon:"Devices", title:"Sito completo",    desc:"Tutti i template + header, footer, stili",  meta:"~ 4 MB" },
              { id:"backup",   icon:"Save",    title:"Backup completo",  desc:"Database WordPress + media + impostazioni", meta:"~ 312 MB" },
            ].map(opt => {
              const Ic = CfgIcon[opt.icon] || CfgIcon.Layers;
              const on = mode === opt.id;
              return (
                <div key={opt.id} onClick={() => setMode(opt.id)} style={{
                  cursor:"pointer",
                  padding:"16px 18px",
                  background:"#fff",
                  border: on ? "1.5px solid var(--c-red)" : "1px solid var(--c-line)",
                  borderRadius:12,
                  position:"relative",
                  boxShadow: on ? "0 0 0 4px var(--c-red-soft)" : "none",
                  transition:"box-shadow .15s, border-color .15s",
                }}>
                  <div style={{display:"flex",alignItems:"center",gap:10,marginBottom:8}}>
                    <div style={{
                      width:32,height:32,borderRadius:8,
                      background: on ? "var(--c-red-soft)" : "var(--c-bg)",
                      border:"1px solid " + (on ? "var(--c-red-soft-2)" : "var(--c-line-soft)"),
                      color: on ? "var(--c-red)" : "var(--c-navy)",
                      display:"grid",placeItems:"center",
                    }}>
                      <Ic/>
                    </div>
                    <div style={{flex:1,fontWeight:600,fontSize:14.5,color:"var(--c-navy)"}}>{opt.title}</div>
                    <div style={{
                      width:18,height:18,borderRadius:"50%",
                      border:"1.5px solid " + (on ? "var(--c-red)" : "var(--c-line)"),
                      background: on ? "var(--c-red)" : "#fff",
                      display:"grid",placeItems:"center",color:"#fff",
                    }}>
                      {on && <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12l5 5L20 7"/></svg>}
                    </div>
                  </div>
                  <div className="text-xs text-mute" style={{marginBottom:8,lineHeight:1.5}}>{opt.desc}</div>
                  <div className="text-xs text-faint text-mono">{opt.meta}</div>
                </div>
              );
            })}
          </div>
        </div>
      </div>

      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:16}}>
        {/* ESPORTA */}
        <div className="cfg-card" style={{marginBottom:0}}>
          <div className="cfg-card-head">
            <div className="head-ic" style={{background:"var(--c-red-soft)",borderColor:"var(--c-red-soft-2)",color:"var(--c-red)"}}>
              <CfgIcon.Download/>
            </div>
            <div>
              <h3>Esporta</h3>
              <p>Scarica un file <span className="text-mono text-sm">.json</span> da importare in un'altra installazione.</p>
            </div>
          </div>
          <div className="cfg-card-body tight">
            {mode === "template" && (
              <CfgRow label="Template" hint="Scegli quale template esportare. Puoi selezionarne più di uno tenendo premuto Cmd/Ctrl.">
                <CfgSelect value="home" options={[
                  {value:"home",label:"Home page"},
                  {value:"contact",label:"Pagina contatti"},
                  {value:"about",label:"Chi siamo"},
                  {value:"services",label:"Servizi"},
                  {value:"blogpost",label:"Layout post blog"},
                ]}/>
              </CfgRow>
            )}
            {mode === "site" && (
              <CfgRow label="Cosa includere" hint="Tutto è incluso di default — togli ciò che non ti serve nel sito di destinazione.">
                <div style={{display:"grid",gap:6}}>
                  {["Template e blocchi (5)","Header & Footer globali","Stili globali (colori, font, scala)","Breakpoint responsive","Preset utente"].map(s => (
                    <label key={s} style={{display:"flex",alignItems:"center",gap:10,fontSize:13,color:"var(--c-text)"}}>
                      <input type="checkbox" defaultChecked style={{accentColor:"var(--c-red)"}}/>
                      {s}
                    </label>
                  ))}
                </div>
              </CfgRow>
            )}
            {mode === "backup" && (
              <CfgRow label="Contenuti backup" hint="Backup completo che ripristina il sito esattamente com'è ora.">
                <div style={{display:"grid",gap:6}}>
                  {[
                    {l:"Database WordPress (post, pagine, opzioni)",d:"~ 18 MB"},
                    {l:"Libreria media (uploads/)",d:"~ 280 MB"},
                    {l:"File di configurazione",d:"~ 4 KB"},
                    {l:"Impostazioni OLObuild",d:"~ 120 KB"},
                  ].map(s => (
                    <label key={s.l} style={{display:"flex",alignItems:"center",gap:10,fontSize:13,color:"var(--c-text)"}}>
                      <input type="checkbox" defaultChecked style={{accentColor:"var(--c-red)"}}/>
                      <span style={{flex:1}}>{s.l}</span>
                      <span className="text-xs text-faint text-mono">{s.d}</span>
                    </label>
                  ))}
                </div>
              </CfgRow>
            )}

            <CfgRow label="Includi media" hint="Le immagini, i video e i PDF referenziati vengono impacchettati nel file. Esclusi = solo URL.">
              <CfgSwitch on={true}/>
            </CfgRow>
            <CfgRow label="Comprimi (zip)" hint="Comprime il JSON in zip — riduce la dimensione del 60-80%.">
              <CfgSwitch on={mode !== "template"}/>
            </CfgRow>
            <CfgRow label="Cripta con password" hint="Protegge il file con password AES-256. Necessaria all'import." noDivider>
              <div style={{display:"flex",gap:8}}>
                <CfgSwitch on={false}/>
                <span className="text-xs text-mute" style={{alignSelf:"center"}}>Consigliato per backup completi</span>
              </div>
            </CfgRow>

            <div style={{
              display:"flex",alignItems:"center",gap:12,
              marginTop:18,paddingTop:16,
              borderTop:"1px solid var(--c-line-soft)",
            }}>
              <div className="text-xs text-mute" style={{flex:1}}>
                Stima dimensione · <b className="text-mono" style={{color:"var(--c-navy)"}}>
                  {mode==="template" ? "~ 280 KB" : mode==="site" ? "~ 4.2 MB" : "~ 312 MB"}
                </b>
              </div>
              <button className="cfg-btn cfg-btn-primary">
                <CfgIcon.Download/> Esporta {mode === "template" ? "template" : mode === "site" ? "sito" : "backup"}
              </button>
            </div>
          </div>
        </div>

        {/* IMPORTA */}
        <div className="cfg-card" style={{marginBottom:0}}>
          <div className="cfg-card-head">
            <div className="head-ic"><CfgIcon.Plus/></div>
            <div>
              <h3>Importa</h3>
              <p>Carica un file <span className="text-mono text-sm">.json</span> esportato da OLObuild — anche da un'altra installazione.</p>
            </div>
          </div>
          <div className="cfg-card-body">
            <div style={{
              border:"2px dashed var(--c-line)",
              borderRadius:12,
              padding:"28px 20px",
              textAlign:"center",
              background:"var(--c-bg)",
              cursor:"pointer",
            }}>
              <div style={{
                width:44,height:44,
                margin:"0 auto 12px",
                borderRadius:10,
                background:"#fff",
                border:"1px solid var(--c-line)",
                display:"grid",placeItems:"center",
                color:"var(--c-navy)",
              }}><CfgIcon.Download/></div>
              <div style={{fontWeight:600,fontSize:14,color:"var(--c-navy)",marginBottom:4}}>
                Trascina qui il file <span className="text-mono">.json</span> o <span style={{color:"var(--c-red)"}}>sfoglia</span>
              </div>
              <div className="text-xs text-mute">
                Supportati: <span className="text-mono">.json</span>, <span className="text-mono">.zip</span>, <span className="text-mono">.json.enc</span> · Max 500 MB
              </div>
            </div>

            <div style={{marginTop:16,padding:"14px 16px",background:"var(--c-bg)",border:"1px solid var(--c-line-soft)",borderRadius:10}}>
              <div className="text-xs fw-600" style={{color:"var(--c-navy)",textTransform:"uppercase",letterSpacing:".06em",marginBottom:10}}>Opzioni di import</div>
              <div style={{display:"grid",gap:8}}>
                <label style={{display:"flex",alignItems:"flex-start",gap:10,fontSize:13,color:"var(--c-text)"}}>
                  <input type="checkbox" defaultChecked style={{accentColor:"var(--c-red)",marginTop:3}}/>
                  <div>
                    <div style={{fontWeight:500}}>Scarica e ricarica i media</div>
                    <div className="text-xs text-mute">Le immagini vengono salvate localmente nella Libreria media.</div>
                  </div>
                </label>
                <label style={{display:"flex",alignItems:"flex-start",gap:10,fontSize:13,color:"var(--c-text)"}}>
                  <input type="checkbox" style={{accentColor:"var(--c-red)",marginTop:3}}/>
                  <div>
                    <div style={{fontWeight:500}}>Sovrascrivi template esistenti</div>
                    <div className="text-xs text-mute">Se un template con lo stesso slug esiste già, viene sostituito.</div>
                  </div>
                </label>
                <label style={{display:"flex",alignItems:"flex-start",gap:10,fontSize:13,color:"var(--c-text)"}}>
                  <input type="checkbox" defaultChecked style={{accentColor:"var(--c-red)",marginTop:3}}/>
                  <div>
                    <div style={{fontWeight:500}}>Crea snapshot prima di importare</div>
                    <div className="text-xs text-mute">Backup automatico di sicurezza prima delle modifiche.</div>
                  </div>
                </label>
              </div>
            </div>

            <div style={{
              display:"flex",alignItems:"center",gap:12,
              marginTop:16,
            }}>
              <div className="text-xs text-mute" style={{flex:1}}>
                Nessun file selezionato
              </div>
              <button className="cfg-btn cfg-btn-secondary" disabled style={{opacity:.5}}>
                <CfgIcon.Eye/> Anteprima
              </button>
              <button className="cfg-btn cfg-btn-primary" disabled style={{opacity:.5}}>
                <CfgIcon.Check/> Importa
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* CRONOLOGIA */}
      <div className="cfg-card" style={{marginTop:16}}>
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Refresh/></div>
          <div>
            <h3>Cronologia operazioni</h3>
            <p>Ultimi 30 giorni · {recents.length} operazioni</p>
          </div>
          <div className="head-actions">
            <div className="cfg-segment">
              <button className="is-on">Tutto</button>
              <button>Export</button>
              <button>Import</button>
              <button>Schedulati</button>
            </div>
          </div>
        </div>
        <div className="cfg-card-body" style={{padding:0}}>
          <div style={{
            display:"grid",
            gridTemplateColumns:"110px 1fr 140px 100px 1fr 80px",
            gap:14,alignItems:"center",
            padding:"10px 22px",
            fontSize:11,
            fontWeight:700,
            letterSpacing:".06em",
            textTransform:"uppercase",
            color:"var(--c-text-faint)",
            borderBottom:"1px solid var(--c-line-soft)",
            background:"var(--c-bg)",
          }}>
            <span>Operazione</span>
            <span>Oggetto</span>
            <span>Data</span>
            <span>Dimensione</span>
            <span>Eseguito da</span>
            <span style={{textAlign:"right"}}>Azioni</span>
          </div>
          {recents.map((r,i) => (
            <div key={i} style={{
              display:"grid",
              gridTemplateColumns:"110px 1fr 140px 100px 1fr 80px",
              gap:14,alignItems:"center",
              padding:"12px 22px",
              borderBottom: i < recents.length-1 ? "1px solid var(--c-line-soft)" : "0",
              fontSize:13,
            }}>
              <span className={"cfg-pill " + (r.kind === "export" ? "ok" : "new")} style={{justifySelf:"start"}}>
                <span className="dot"/> {r.kind === "export" ? "EXPORT" : "IMPORT"}
              </span>
              <span style={{fontWeight:500,color:"var(--c-navy)"}}>{r.target}</span>
              <span className="text-mute text-mono text-xs">{r.date}</span>
              <span className="text-mute text-mono text-xs">{r.size}</span>
              <span className="text-mute text-xs">{r.by}</span>
              <span style={{display:"flex",gap:4,justifyContent:"flex-end"}}>
                {r.kind === "export"
                  ? <button className="cfg-btn cfg-btn-icon cfg-btn-ghost" title="Riscarica"><CfgIcon.Download/></button>
                  : <button className="cfg-btn cfg-btn-icon cfg-btn-ghost" title="Annulla import"><CfgIcon.Undo/></button>
                }
                <button className="cfg-btn cfg-btn-icon cfg-btn-ghost" title="Dettagli"><CfgIcon.ChevR/></button>
              </span>
            </div>
          ))}
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// Banner per i tab migrati dal dashboard parente
// ─────────────────────────────────────────────────────────────────
function MigratedBanner({ from = "dashboard principale" }) {
  return (
    <div style={{
      display:"flex",alignItems:"center",gap:12,
      padding:"10px 14px",
      background:"#fef3c7",
      border:"1px solid #fde68a",
      borderRadius:10,
      fontSize:12.5,
      color:"#92400e",
      marginBottom:20,
    }}>
      <div style={{
        background:"#fbbf24",color:"#fff",
        fontSize:10,fontWeight:700,letterSpacing:".05em",
        padding:"2px 7px",borderRadius:4,
      }}>MIGRATO</div>
      <div style={{flex:1,lineHeight:1.5}}>
        Questa sezione viveva nel <b>{from}</b>. È un'impostazione globale (set-once) → vive meglio qui in Configurazione. Verificare i campi rispetto alla versione attuale prima del rilascio.
      </div>
    </div>
  );
}

// ─────────────────────────────────────────────────────────────────
// SEO GLOBALE — TAB 9 (migrated)
// ─────────────────────────────────────────────────────────────────
function TabSEO() {
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>SEO <em>globale</em></h1>
          <p>Default meta-tag, Open Graph, sitemap e schema.org per tutto il sito. Sovrascrivibile pagina per pagina dall'editor.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Eye/> Test su Google</button>
        </div>
      </div>

      <MigratedBanner from="card SEO"/>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Search/></div>
          <div>
            <h3>Default site-wide</h3>
            <p>Usati quando una pagina non ha meta-tag specifici.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Title pattern" hint="Schema del title delle pagine. Variabili: {page}, {sep}, {site}.">
            <CfgInput value="{page} {sep} {site}" mono/>
          </CfgRow>
          <CfgRow label="Separatore" hint="Carattere che separa i blocchi nel title.">
            <CfgSelect value="bar" options={[{value:"bar",label:"— (em dash)"},{value:"pipe",label:"|"},{value:"dot",label:"·"},{value:"dash",label:"-"}]}/>
          </CfgRow>
          <CfgRow label="Meta description default" hint="Massimo 160 caratteri. Usato come fallback quando la pagina non ne ha una propria.">
            <div className="cfg-textarea"><textarea defaultValue={"Hotel boutique sul lago di Como. Camere fronte lago, ristorante stellato, SPA con vista. Soggiorni dal 1923."}/></div>
            <div className="text-xs text-faint mt-2">132 / 160 caratteri</div>
          </CfgRow>
          <CfgRow label="Lingua sito" hint="Attributo lang dell'HTML.">
            <CfgSelect value="it" options={[{value:"it",label:"Italiano (it_IT)"},{value:"en",label:"English (en_US)"}]}/>
          </CfgRow>
          <CfgRow label="Robots default" hint="Comportamento di default per i motori di ricerca." noDivider>
            <div className="cfg-segment">
              <button className="is-on">Index, Follow</button>
              <button>NoIndex</button>
              <button>NoFollow</button>
            </div>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Image/></div>
          <div>
            <h3>Open Graph & Twitter Card</h3>
            <p>Come appare il sito quando viene condiviso su social e messaggistica.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Immagine OG default" hint="1200×630 consigliato. Usata quando la pagina non ne ha una propria.">
            <div style={{display:"flex",gap:12,alignItems:"flex-start"}}>
              <div style={{
                width:160,height:84,background:"linear-gradient(135deg,#e1474f,#7a1d23)",
                borderRadius:8,position:"relative",overflow:"hidden",
                display:"grid",placeItems:"center",color:"#fff",fontFamily:"Instrument Serif",fontSize:22,
              }}>Hotel Como</div>
              <div style={{flex:1}}>
                <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Plus/> Sostituisci</button>
                <div className="text-xs text-faint mt-2">og-default.jpg · 1200×630 · 184 KB</div>
              </div>
            </div>
          </CfgRow>
          <CfgRow label="Twitter handle" hint="Username Twitter del sito (con @).">
            <CfgInput value="@hotelcomo" mono/>
          </CfgRow>
          <CfgRow label="Card type" hint="Formato della preview su Twitter." noDivider>
            <div className="cfg-segment">
              <button>Summary</button>
              <button className="is-on">Summary Large Image</button>
            </div>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Refresh/></div>
          <div>
            <h3>Sitemap & schema.org</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Sitemap XML" hint="Generato automaticamente a /sitemap.xml. 47 URL al momento.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Tipo organizzazione" hint="Schema.org markup iniettato in ogni pagina.">
            <CfgSelect value="hotel" options={[{value:"hotel",label:"Hotel"},{value:"restaurant",label:"Restaurant"},{value:"localbusiness",label:"LocalBusiness"},{value:"org",label:"Organization (default)"}]}/>
          </CfgRow>
          <CfgRow label="Auto-ping search engines" hint="Notifica Google e Bing ad ogni pubblicazione." noDivider>
            <CfgSwitch on={true}/>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// COOKIE CONSENT & GDPR — TAB 10 (migrated)
// ─────────────────────────────────────────────────────────────────
function TabCookie() {
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Cookie Consent <em>& GDPR</em></h1>
          <p>Banner di consenso, categorie di cookie, e gestione delle preferenze utente in conformità al GDPR.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Eye/> Anteprima banner</button>
        </div>
      </div>

      <MigratedBanner from="card Cookie Consent"/>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Key/></div>
          <div>
            <h3>Stato e modalità</h3>
            <p>Il banner viene mostrato al primo accesso e ai visitatori che non hanno ancora scelto.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Cookie banner" hint="Disattiva solo se il sito non usa cookie non-essenziali.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Modalità" hint="Opt-in è richiesto in UE. Opt-out solo dove ammesso.">
            <div className="cfg-segment">
              <button className="is-on">Opt-in (GDPR)</button>
              <button>Opt-out</button>
              <button>Solo notifica</button>
            </div>
          </CfgRow>
          <CfgRow label="Blocca script fino al consenso" hint="Google Analytics, Meta Pixel, ecc. non partono finché l'utente non accetta.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Re-richiedi consenso dopo" hint="Mesi dopo i quali il banner ricompare." noDivider>
            <CfgInput value="6" suffix="mesi" mono/>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Layers/></div>
          <div>
            <h3>Categorie di cookie</h3>
            <p>Le categorie mostrate nel pannello di preferenze.</p>
          </div>
        </div>
        <div className="cfg-card-body" style={{padding:0}}>
          {[
            {name:"Strettamente necessari", desc:"Carrello, login, lingua. Non disattivabili.", required:true, count:4},
            {name:"Funzionali",             desc:"Chat live, salvataggio form, preferenze.", required:false, count:2, active:true},
            {name:"Analytics",              desc:"Google Analytics, Hotjar.", required:false, count:3, active:true},
            {name:"Marketing & Pixel",      desc:"Meta Pixel, LinkedIn Insight, Google Ads.", required:false, count:5, active:false},
          ].map((cat,i) => (
            <div key={cat.name} style={{
              display:"grid",
              gridTemplateColumns:"24px 1fr 60px 50px",
              gap:14,alignItems:"center",
              padding:"14px 22px",
              borderBottom: i < 3 ? "1px solid var(--c-line-soft)" : 0,
            }}>
              <div style={{
                width:8,height:8,borderRadius:2,
                background: cat.required ? "var(--c-text-faint)" : (cat.active ? "var(--c-red)" : "var(--c-line)"),
              }}/>
              <div>
                <div style={{fontWeight:600,fontSize:14,color:"var(--c-navy)"}}>
                  {cat.name}
                  {cat.required && <span className="cfg-pill off" style={{marginLeft:8,fontSize:9}}><span className="dot"/> OBBLIGATORIO</span>}
                </div>
                <div className="text-xs text-mute" style={{marginTop:2}}>{cat.desc}</div>
              </div>
              <div className="text-xs text-mute text-mono">{cat.count} cookie</div>
              {cat.required
                ? <CfgSwitch on={true}/>
                : <CfgSwitch on={cat.active}/>
              }
            </div>
          ))}
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Type/></div>
          <div>
            <h3>Copy del banner</h3>
            <p>Testi mostrati al visitatore. Supporta multilingua.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Lingua attiva" hint="Hai 2 lingue configurate.">
            <div className="cfg-segment">
              <button className="is-on">🇮🇹 Italiano</button>
              <button>🇬🇧 English</button>
            </div>
          </CfgRow>
          <CfgRow label="Titolo banner">
            <CfgInput value="Utilizziamo i cookie 🍪"/>
          </CfgRow>
          <CfgRow label="Testo banner">
            <div className="cfg-textarea"><textarea defaultValue={"Per offrirti la migliore esperienza utilizziamo cookie. Puoi accettare tutti, solo gli essenziali o personalizzare le tue preferenze. Cambia idea in qualsiasi momento dalle impostazioni in fondo a ogni pagina."}/></div>
          </CfgRow>
          <CfgRow label="CTA primario">
            <div className="grid-3">
              <CfgInput value="Accetta tutti"/>
              <CfgInput value="Solo essenziali"/>
              <CfgInput value="Personalizza"/>
            </div>
          </CfgRow>
          <CfgRow label="Posizione" noDivider>
            <div className="cfg-segment">
              <button>In basso</button>
              <button className="is-on">In basso a sx</button>
              <button>In basso a dx</button>
              <button>Centro overlay</button>
            </div>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// PERFORMANCE & CACHE — TAB 11 (migrated)
// ─────────────────────────────────────────────────────────────────
function TabPerformance() {
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Performance <em>& Cache</em></h1>
          <p>Ottimizzazioni che migliorano i Core Web Vitals senza toccare il design. Cache, lazy load, minify, immagini WebP.</p>
        </div>
        <div className="head-actions">
          <span className="cfg-pill ok"><span className="dot"/> Score 96/100</span>
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Refresh/> Svuota tutto</button>
        </div>
      </div>

      <MigratedBanner from="card Performance"/>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Gauge/></div>
          <div>
            <h3>Stato cache</h3>
            <p>Ultimo svuotamento: oggi alle 12:14 · Sistema cache attivo</p>
          </div>
          <div className="head-actions">
            <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Refresh/> Rigenera ora</button>
          </div>
        </div>
        <div className="cfg-card-body">
          <div className="grid-4">
            {[
              {l:"Pagine cachate",v:"243",t:"di 247 totali"},
              {l:"Hit rate",v:"94%",t:"ultime 24h"},
              {l:"Spazio occupato",v:"58 MB",t:"di 500 MB max"},
              {l:"Risparmio banda",v:"12 GB",t:"questo mese"},
            ].map(s => (
              <div key={s.l} style={{background:"var(--c-bg)",borderRadius:10,padding:14}}>
                <div className="text-xs text-mute fw-600" style={{textTransform:"uppercase",letterSpacing:".06em"}}>{s.l}</div>
                <div style={{fontFamily:"Instrument Serif",fontSize:32,lineHeight:1,marginTop:6,color:"var(--c-navy)"}}>{s.v}</div>
                <div className="text-xs text-faint" style={{marginTop:4}}>{s.t}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Layers/></div>
          <div>
            <h3>Cache delle pagine</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Page cache" hint="Salva le pagine generate come HTML statico. Drastico boost del TTFB.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="TTL (durata cache)" hint="Tempo dopo cui una pagina viene rigenerata.">
            <CfgSelect value="24h" options={[{value:"1h",label:"1 ora"},{value:"6h",label:"6 ore"},{value:"24h",label:"24 ore"},{value:"7d",label:"7 giorni"},{value:"manual",label:"Solo manuale"}]}/>
          </CfgRow>
          <CfgRow label="Cache anche utenti loggati" hint="Off di default — gli utenti loggati possono vedere dati personalizzati.">
            <CfgSwitch on={false}/>
          </CfgRow>
          <CfgRow label="Esclusioni URL" hint="Path che non devono essere cachati. Una per riga, supporta wildcards." noDivider>
            <div className="cfg-textarea"><textarea defaultValue={"/checkout/*\n/account/*\n/cart\n/wp-admin/*"}/></div>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Image/></div>
          <div>
            <h3>Ottimizzazione media</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Conversione WebP" hint="Genera versione WebP per immagini JPG/PNG. -40% peso medio.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Lazy loading" hint="Le immagini caricano solo quando sono visibili (eccetto la prima).">
            <div className="cfg-segment">
              <button className="is-on">Native (browser)</button>
              <button>JS-based</button>
              <button>Off</button>
            </div>
          </CfgRow>
          <CfgRow label="Qualità compressione" hint="JPG quality del codec output.">
            <CfgSlider pct={75} val="75%"/>
          </CfgRow>
          <CfgRow label="Dimensioni responsive auto" hint="Genera srcset multi-risoluzione per ogni immagine." noDivider>
            <CfgSwitch on={true}/>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Code/></div>
          <div>
            <h3>Minify & combine</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Minify HTML">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Minify CSS">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Minify JavaScript">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Defer JS non critico" hint="Rinvia il caricamento degli script non essenziali per il primo render." noDivider>
            <CfgSwitch on={true}/>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// WHITE LABEL — TAB 12 (migrated)
// ─────────────────────────────────────────────────────────────────
function TabWhiteLabel() {
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>White <em>Label</em></h1>
          <p>Personalizza nome, logo e branding del plugin per consegnarlo ai clienti senza riferimenti a OLObuild.</p>
        </div>
        <div className="head-actions">
          <span className="cfg-pill ok"><span className="dot"/> Licenza Agency</span>
        </div>
      </div>

      <MigratedBanner from="sezione SISTEMA · White Label"/>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Drop/></div>
          <div>
            <h3>Identità del plugin</h3>
            <p>Come appare nel menu di WordPress e nel builder.</p>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Nome plugin" hint="Sostituisce 'OLObuild' nelle voci di menu WP, nell'editor e nei messaggi di sistema.">
            <CfgInput value="Studio Builder"/>
          </CfgRow>
          <CfgRow label="Nome agenzia" hint="Visibile in footer di alcune schermate e nei meta dei file esportati.">
            <CfgInput value="Studio Conti & Associati"/>
          </CfgRow>
          <CfgRow label="Logo (claro)" hint="32×32px. Visibile in voce di menu WordPress.">
            <div style={{display:"flex",gap:12,alignItems:"center"}}>
              <div style={{width:48,height:48,borderRadius:10,background:"var(--c-bg)",display:"grid",placeItems:"center",border:"1px solid var(--c-line)"}}>
                <span style={{fontFamily:"Instrument Serif",fontSize:24,color:"var(--c-navy)"}}>S</span>
              </div>
              <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Plus/> Sostituisci</button>
              <button className="cfg-btn cfg-btn-ghost cfg-btn-danger" style={{padding:"6px 10px"}}><CfgIcon.X/></button>
            </div>
          </CfgRow>
          <CfgRow label="Logo (scuro)" hint="Usato in barre scure dell'editor.">
            <div style={{display:"flex",gap:12,alignItems:"center"}}>
              <div style={{width:48,height:48,borderRadius:10,background:"var(--c-navy)",display:"grid",placeItems:"center"}}>
                <span style={{fontFamily:"Instrument Serif",fontSize:24,color:"#fff"}}>S</span>
              </div>
              <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Plus/> Sostituisci</button>
            </div>
          </CfgRow>
          <CfgRow label="URL sito agenzia" hint="Link 'Powered by' nei file esportati (se attivo)." noDivider>
            <CfgInput value="https://studioconti.it" mono/>
          </CfgRow>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.EyeOff/></div>
          <div>
            <h3>Visibilità</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Nascondi 'Powered by OLObuild'" hint="Toglie attribuzione nel footer dell'editor e nei file generati.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Nascondi changelog & roadmap" hint="Il cliente non vede comunicazioni del team OLObuild.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Nascondi link Documentazione" hint="Sostituisci con un link a documentazione tua." noDivider>
            <div style={{display:"flex",gap:8,alignItems:"center"}}>
              <CfgSwitch on={false}/>
              <CfgInput value="https://studioconti.it/guida"/>
            </div>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// PERMESSI & RUOLI — TAB 13 (migrated)
// ─────────────────────────────────────────────────────────────────
function TabPermessi() {
  const matrix = [
    { perm:"Aprire l'editor",                 admin:true, editor:true, author:true,  contrib:false, client:true },
    { perm:"Pubblicare pagine",               admin:true, editor:true, author:true,  contrib:false, client:false },
    { perm:"Modificare Stili globali",        admin:true, editor:true, author:false, contrib:false, client:false },
    { perm:"Modificare Header / Footer",      admin:true, editor:true, author:false, contrib:false, client:false },
    { perm:"Modificare Configurazione",       admin:true, editor:false,author:false, contrib:false, client:false },
    { perm:"Sfogliare la libreria template",  admin:true, editor:true, author:true,  contrib:true,  client:true },
    { perm:"Salvare template personalizzati", admin:true, editor:true, author:false, contrib:false, client:false },
    { perm:"Importare / esportare",           admin:true, editor:false,author:false, contrib:false, client:false },
    { perm:"Vedere Analytics",                admin:true, editor:true, author:false, contrib:false, client:true },
  ];
  const roles = [
    { id:"admin",   label:"Admin",       count:2 },
    { id:"editor",  label:"Editor",      count:5 },
    { id:"author",  label:"Author",      count:12 },
    { id:"contrib", label:"Contributor", count:3 },
    { id:"client",  label:"Cliente",     count:1, custom:true },
  ];
  return (
    <>
      <div className="cfg-page-head">
        <div>
          <h1>Permessi <em>& Ruoli</em></h1>
          <p>Chi può fare cosa nel builder. Si appoggia ai ruoli WordPress, ma li estende con permessi granulari specifici di OLObuild.</p>
        </div>
        <div className="head-actions">
          <button className="cfg-btn cfg-btn-secondary"><CfgIcon.Plus/> Crea ruolo custom</button>
        </div>
      </div>

      <MigratedBanner from="sezione SISTEMA · Permessi & Ruoli"/>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Wrench/></div>
          <div>
            <h3>Matrice permessi</h3>
            <p>Cliccare una cella per cambiare un permesso. I ruoli custom si possono creare e modificare.</p>
          </div>
        </div>
        <div className="cfg-card-body" style={{padding:0,overflow:"auto"}}>
          <table style={{width:"100%",borderCollapse:"collapse",fontSize:13}}>
            <thead>
              <tr style={{background:"var(--c-bg)"}}>
                <th style={{textAlign:"left",padding:"12px 22px",fontSize:11,fontWeight:700,letterSpacing:".06em",textTransform:"uppercase",color:"var(--c-text-faint)"}}>Permesso</th>
                {roles.map(r => (
                  <th key={r.id} style={{padding:"12px 14px",fontSize:11,fontWeight:700,letterSpacing:".06em",textTransform:"uppercase",color:"var(--c-text-faint)",textAlign:"center",minWidth:100}}>
                    <div style={{fontSize:12,color:"var(--c-navy)",marginBottom:2,display:"flex",alignItems:"center",gap:6,justifyContent:"center"}}>
                      {r.label}
                      {r.custom && <span className="cfg-pill new" style={{fontSize:8,padding:"1px 5px"}}>CUSTOM</span>}
                    </div>
                    <div style={{fontWeight:500,color:"var(--c-text-faint)",fontSize:10}}>{r.count} utenti</div>
                  </th>
                ))}
              </tr>
            </thead>
            <tbody>
              {matrix.map((row,i) => (
                <tr key={row.perm} style={{borderTop:"1px solid var(--c-line-soft)"}}>
                  <td style={{padding:"10px 22px",fontWeight:500,color:"var(--c-navy)"}}>{row.perm}</td>
                  {roles.map(r => (
                    <td key={r.id} style={{textAlign:"center",padding:"10px 14px"}}>
                      {row[r.id]
                        ? <div style={{
                            width:22,height:22,margin:"0 auto",
                            borderRadius:5,background:"var(--c-red-soft)",
                            color:"var(--c-red)",
                            display:"grid",placeItems:"center",
                          }}><CfgIcon.Check/></div>
                        : <div style={{
                            width:22,height:22,margin:"0 auto",
                            borderRadius:5,background:"var(--c-bg)",
                            color:"var(--c-text-faint)",
                            display:"grid",placeItems:"center",
                            border:"1px solid var(--c-line)",
                          }}><CfgIcon.X/></div>
                      }
                    </td>
                  ))}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <div className="cfg-card">
        <div className="cfg-card-head">
          <div className="head-ic"><CfgIcon.Key/></div>
          <div>
            <h3>Opzioni avanzate</h3>
          </div>
        </div>
        <div className="cfg-card-body tight">
          <CfgRow label="Lock dei template Header/Footer" hint="Solo Admin può modificarli. Sicurezza per agenzie che consegnano siti ai clienti.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Lock degli Stili globali" hint="Una volta consegnato il sito, il cliente non può rovinare la palette/tipografia.">
            <CfgSwitch on={true}/>
          </CfgRow>
          <CfgRow label="Sandbox per Contributors" hint="I contributor lavorano su una copia draft, niente live edit." noDivider>
            <CfgSwitch on={false}/>
          </CfgRow>
        </div>
      </div>
    </>
  );
}

// ─────────────────────────────────────────────────────────────────
// DISPATCHER
// ─────────────────────────────────────────────────────────────────
function CfgTabContent({ activeId }) {
  switch (activeId) {
    case "presets":     return <TabPresets/>;
    case "colori":      return <TabColori/>;
    case "tipografia":  return <TabTipografia/>;
    case "ai":          return <TabAI/>;
    case "stockmedia":  return <TabStockMedia/>;
    case "api":         return <TabAPI/>;
    case "responsive":  return <TabResponsive/>;
    case "importexp":   return <TabImportExport/>;
    case "seo":         return <TabSEO/>;
    case "cookie":      return <TabCookie/>;
    case "performance": return <TabPerformance/>;
    case "whitelabel":  return <TabWhiteLabel/>;
    case "permessi":    return <TabPermessi/>;
    default:            return <TabPresets/>;
  }
}

window.CfgTabContent = CfgTabContent;
