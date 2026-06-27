// OLObuild — Post-install default home (variants)
// Pagina generata al termine dell'installazione del plugin.
// Visualizzata sul front-end finché l'utente non sostituisce il contenuto
// dal builder Olobuild.

// ───────── Icone (Lucide inline, stroke 2) ─────────
const Icon = {
  Search:    () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>,
  ChevronDown:() => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m6 9 6 6 6-6"/></svg>,
  ChevronL:  () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m15 18-6-6 6-6"/></svg>,
  ChevronR:  () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9 18 6-6-6-6"/></svg>,
  ChevronU:  () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m18 15-6-6-6 6"/></svg>,
  Settings:  () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h0a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h0a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v0a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>,
  Copy:      () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>,
  Trash:     () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>,
  Plus:      () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="M12 5v14M5 12h14"/></svg>,
  Cols:      () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="7" height="16" rx="1.5"/><rect x="14" y="4" width="7" height="16" rx="1.5"/></svg>,
  ArrowR:    () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>,
  Brush:     () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m9.06 11.9 8.07-8.06a2.85 2.85 0 1 1 4.03 4.03l-8.06 8.08"/><path d="M7.07 14.94c-1.66 0-3 1.35-3 3.02 0 1.33-2.5 1.52-2 2.02 1.08 1.1 2.49 2.02 4 2.02 2.2 0 4-1.8 4-4.04a3.01 3.01 0 0 0-3-3.02z"/></svg>,
  Header:    () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18"/><circle cx="7" cy="7" r=".5" fill="currentColor"/><path d="M11 7h6"/></svg>,
  Footer:    () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 14h18"/><path d="M7 17h4"/><path d="M14 17h3"/></svg>,
  Sparkles:  () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/></svg>,
  Check:     () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12l5 5L20 7"/></svg>,
  Info:      () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v5h1"/></svg>,
  Palette:   () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="13.5" cy="6.5" r="1.5"/><circle cx="17.5" cy="10.5" r="1.5"/><circle cx="8.5" cy="7.5" r="1.5"/><circle cx="6.5" cy="12.5" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.5 1.5-1.5 0-.4-.18-.83-.5-1.17a1.4 1.4 0 0 1-.34-1.04 1.5 1.5 0 0 1 1.5-1.29h2.04a4.3 4.3 0 0 0 4.3-4.3C20.5 6.69 16.7 2 12 2z"/></svg>,
  Layout:    () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>,
  BookOpen:  () => <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M2 4h6a4 4 0 0 1 4 4v12a3 3 0 0 0-3-3H2zM22 4h-6a4 4 0 0 0-4 4v12a3 3 0 0 1 3-3h7z"/></svg>,
};

// ───────── Shared pieces ─────────
function PiNav() {
  return (
    <nav className="pi-nav">
      <div className="pi-nav-links">
        <a href="#" className="is-current">Home</a>
        <a href="#">Privacy</a>
      </div>
      <div className="pi-nav-right">
        <div className="pi-lang"><span className="iso">IT</span> Italiano <Icon.ChevronDown/></div>
        <button className="pi-search-btn" aria-label="Cerca"><Icon.Search/></button>
      </div>
    </nav>
  );
}

function PiEditorBar() {
  return (
    <div className="pi-editor-bar" aria-hidden="true">
      <span className="grip"><i/><i/><i/><i/><i/><i/></span>
      <span className="lbl">Sezione</span>
      <span className="btn"><Icon.ChevronL/></span>
      <span className="btn"><Icon.ChevronU/></span>
      <span className="btn" style={{transform:"rotate(180deg)"}}><Icon.ChevronU/></span>
      <span className="btn"><Icon.ChevronR/></span>
      <span className="sep"/>
      <span className="btn"><Icon.Settings/></span>
      <span className="btn"><Icon.Copy/></span>
      <span className="btn"><Icon.Trash/></span>
      <span className="sep"/>
      <span className="btn add"><Icon.Plus/></span>
      <span className="btn cols"><Icon.Cols/></span>
    </div>
  );
}

function PiFooter() {
  return (
    <footer className="pi-footer">
      <div className="pi-footer-brand">
        <div className="logo">olo<span>build</span></div>
        <p>Sostituisci questo testo con la descrizione della tua attività. Apri <b style={{color:"#fff"}}>Olobuild → Modifica Footer</b> per cambiare colonne, link e identità.</p>
        <div className="copy">© 2026 olobuild. Tutti i diritti riservati.</div>
      </div>
      <div className="pi-footer-col">
        <h4>Naviga</h4>
        <ul>
          <li><Icon.Check/><a href="#">Home</a></li>
          <li><Icon.Check/><a href="#">Privacy</a></li>
          <li><Icon.Check/><a href="#">Cookie</a></li>
          <li><Icon.Check/><a href="#">Contatti</a></li>
        </ul>
      </div>
      <div className="pi-footer-col">
        <h4>Risorse</h4>
        <ul>
          <li><a href="#">Documentazione</a></li>
          <li><a href="#">Template</a></li>
          <li><a href="#">Changelog</a></li>
          <li><a href="#">Supporto</a></li>
        </ul>
      </div>
      <div className="pi-footer-col">
        <h4>Contatti</h4>
        <ul>
          <li><a href="#">info@olobuild.it</a></li>
          <li><a href="#">+39 06 1234 5678</a></li>
          <li><a href="#">Via Roma, 1 — 00100</a></li>
        </ul>
      </div>
    </footer>
  );
}

// ───────── VARIANT A — Hero + Tip Cards ─────────
function PostInstallA() {
  return (
    <div className="pi-page" data-screen-label="A · Suggerimenti">
      <PiNav/>
      <PiEditorBar/>

      <section className="pi-hero">
        <span className="pi-hero-chip">
          <span className="dot"/> Installazione completata · v3.34.6
        </span>

        <h1>
          Benvenuto.<br/>
          <em>Costruiamo</em> la tua home.
        </h1>

        <p className="pi-lead">
          Questa è la pagina temporanea generata da <b>OLObuild</b> al termine dell'installazione.
          Usa i pulsanti qui sotto per personalizzare le tre aree principali del sito — al primo salvataggio, questi contenuti spariranno.
        </p>

        <div className="pi-cta-row">
          <a href="#" className="pi-btn pi-btn-primary">
            <Icon.Brush/> Personalizza la home
          </a>
          <a href="#" className="pi-btn pi-btn-secondary">
            <Icon.Header/> Modifica Header
          </a>
          <a href="#" className="pi-btn pi-btn-secondary">
            <Icon.Footer/> Modifica Footer
          </a>
          <a href="#" className="pi-btn pi-btn-link">
            Apri la documentazione →
          </a>
        </div>

        <div className="pi-cta-hint">
          <Icon.Info/>
          Tutti gli strumenti sono raggiungibili anche dal menu <b style={{color:"var(--pi-navy)",fontWeight:600,marginLeft:4}}>Olobuild</b> nella sidebar di WordPress.
        </div>
      </section>

      <section className="pi-tips">
        <div className="pi-tips-head">
          <h2>Per iniziare, <em>tre mosse.</em></h2>
          <span className="note"><Icon.Sparkles/> Questi suggerimenti scompariranno al primo salvataggio</span>
        </div>

        <div className="pi-tip-grid">
          <div className="pi-tip">
            <div className="pi-tip-num">01</div>
            <h3>Parti da un template</h3>
            <p>Scegli tra 128 layout italiani pensati per hotel, ristoranti, studi, e-commerce — pronti da personalizzare.</p>
            <a href="#" className="pi-tip-link">Sfoglia i template <Icon.ArrowR/></a>
          </div>
          <div className="pi-tip">
            <div className="pi-tip-num">02</div>
            <h3>Adatta colori e font</h3>
            <p>Imposta la palette del brand e le tipografie globali una sola volta. Si applicano a header, footer e a tutte le pagine.</p>
            <a href="#" className="pi-tip-link">Apri lo Style Manager <Icon.ArrowR/></a>
          </div>
          <div className="pi-tip">
            <div className="pi-tip-num">03</div>
            <h3>Sostituisci questo contenuto</h3>
            <p>Aggiungi la tua storia, i tuoi servizi e i tuoi contatti. Clicca <b>Personalizza la home</b> per aprire la sezione nel builder.</p>
            <a href="#" className="pi-tip-link">Apri il builder <Icon.ArrowR/></a>
          </div>
        </div>
      </section>

      <PiFooter/>
    </div>
  );
}

// ───────── VARIANT B — Checklist + Content Placeholders ─────────
function PostInstallB() {
  return (
    <div className="pi-page" data-screen-label="B · Checklist + segnaposto">
      <PiNav/>
      <PiEditorBar/>

      <section className="pi-hero-split">
        <div>
          <span className="pi-hero-chip">
            <span className="dot"/> Setup pronto · 4 passaggi
          </span>
          <h1 style={{marginTop:24}}>
            La tua home,<br/>
            in <em>quattro mosse</em>.
          </h1>
          <p className="pi-lead" style={{marginTop:24}}>
            Hai appena installato OLObuild. Personalizza le tre aree fondamentali del sito direttamente da qui — gli inviti a fare scompariranno al primo salvataggio.
          </p>

          <div className="pi-cta-row" style={{marginTop:32}}>
            <a href="#" className="pi-btn pi-btn-primary">
              <Icon.Brush/> Personalizza la home
            </a>
            <a href="#" className="pi-btn pi-btn-secondary">
              <Icon.Header/> Modifica Header
            </a>
            <a href="#" className="pi-btn pi-btn-secondary">
              <Icon.Footer/> Modifica Footer
            </a>
          </div>
        </div>

        <div className="pi-checklist">
          <div className="pi-check-item">
            <span className="pi-check-num">1</span>
            <div className="pi-check-body">
              <strong>Personalizza la home</strong>
              <span>Sostituisci questo contenuto temporaneo</span>
            </div>
            <span className="pi-check-action">Apri builder</span>
          </div>
          <div className="pi-check-item">
            <span className="pi-check-num">2</span>
            <div className="pi-check-body">
              <strong>Modifica l'Header</strong>
              <span>Logo, menu di navigazione, selettore lingua</span>
            </div>
            <span className="pi-check-action">Modifica</span>
          </div>
          <div className="pi-check-item">
            <span className="pi-check-num">3</span>
            <div className="pi-check-body">
              <strong>Modifica il Footer</strong>
              <span>Colonne, link legali, recapiti</span>
            </div>
            <span className="pi-check-action">Modifica</span>
          </div>
          <div className="pi-check-item">
            <span className="pi-check-num">4</span>
            <div className="pi-check-body">
              <strong>Imposta colori e font</strong>
              <span>Style Manager globale del sito</span>
            </div>
            <span className="pi-check-action done">Più tardi</span>
          </div>
        </div>
      </section>

      <section className="pi-placeholders">
        <div className="pi-ph tall">
          <div className="pi-ph-art"/>
          <span className="pi-ph-tag">Sezione suggerita</span>
          <h3>Chi siamo</h3>
          <p>Racconta in poche righe la tua storia, i valori del brand e cosa rende unico ciò che offri.</p>
          <div className="skeleton"><i/><i/><i/></div>
        </div>
        <div className="pi-ph">
          <span className="pi-ph-tag">Sezione suggerita</span>
          <h3>I tuoi servizi</h3>
          <p>3–6 card con i servizi principali, un'icona, un titolo e una breve descrizione.</p>
          <div className="skeleton"><i/><i/></div>
        </div>
        <div className="pi-ph">
          <span className="pi-ph-tag">Sezione suggerita</span>
          <h3>Recensioni clienti</h3>
          <p>Testimonianze reali con foto e nome del cliente. Aumentano la fiducia di chi visita il sito.</p>
          <div className="skeleton"><i/><i/></div>
        </div>
      </section>

      <PiFooter/>
    </div>
  );
}

window.PostInstallA = PostInstallA;
window.PostInstallB = PostInstallB;
