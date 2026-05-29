// WordPress admin shell — left side menu with nested Olobuild submenu

function WPShell({ children, activeSub = "Avvio Rapido", appMode = false }) {
  return (
    <div className={"wp-shell " + (appMode?"app-mode":"")}>
      <aside className="wp-side">
        <div className="wp-logo">
          <span className="wp-mark">W</span>
          <span>hotel-resort.it</span>
        </div>
        <a><span className="ic"><HomeIcon name="globe" size={15}/></span>Bacheca<span className="tip">Bacheca</span></a>
        <a><span className="ic"><HomeIcon name="fileText" size={15}/></span>Articoli<span className="tip">Articoli</span></a>
        <a><span className="ic"><HomeIcon name="image" size={15}/></span>Media<span className="tip">Media</span></a>
        <a><span className="ic"><HomeIcon name="fileText" size={15}/></span>Pagine<span className="tip">Pagine</span></a>
        <a><span className="ic"><HomeIcon name="form" size={15}/></span>Commenti<span className="tip">Commenti</span></a>
        <hr/>
        <a><span className="ic"><HomeIcon name="map" size={15}/></span>Locations<span className="tip">Locations</span></a>
        <a><span className="ic"><HomeIcon name="cookie" size={15}/></span>Olo Booking<span className="tip">Olo Booking</span></a>
        <a><span className="ic"><HomeIcon name="user" size={15}/></span>Olo Tutor<span className="tip">Olo Tutor</span></a>
        <a><span className="ic"><HomeIcon name="play" size={15}/></span>Olo Lottie<span className="tip">Olo Lottie</span></a>
        <a className="active olo">
          <span className="ic"><img src="assets/olobuild-square.png" alt="" style={{width:16,height:16,borderRadius:3,filter:"brightness(0) invert(1)"}}/></span>
          Olobuild
          <span className="tip">Olobuild</span>
        </a>
        <div className="sub">
          {[
            "Dashboard","Gestione Template","Configurazione","Ricerca Media","Invii Form",
            "Analytics","Cookie Consent","Permessi","SEO","Redirect & 404","Performance",
            "Popup Globali","White Label","Import/Export","Strumenti","WooCommerce"
          ].map(s => (
            <a key={s} className={s===activeSub?"active":""}>{s}</a>
          ))}
        </div>
        <a><span className="ic"><HomeIcon name="bell" size={15}/></span>Olo Calendar<span className="tip">Olo Calendar</span></a>
        <a><span className="ic"><HomeIcon name="users" size={15}/></span>Utenti<span className="tip">Utenti</span></a>
        <a><span className="ic"><HomeIcon name="wrench" size={15}/></span>Strumenti<span className="tip">Strumenti</span></a>
        <a><span className="ic"><HomeIcon name="sliders" size={15}/></span>Impostazioni<span className="tip">Impostazioni</span></a>
        <div className="grow"/>
        <div className="userblock">
          <div className="av">MA</div>
          <div>
            <div style={{color:"#fff",fontWeight:600}}>Marco A.</div>
            <div>Amministratore</div>
          </div>
        </div>
      </aside>
      <div className="wp-main" style={{display:"flex",flexDirection:"column",minWidth:0,minHeight:0}}>
        {children}
      </div>
    </div>
  );
}

window.WPShell = WPShell;
