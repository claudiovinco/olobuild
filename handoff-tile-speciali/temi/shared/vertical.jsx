/* Vertical page shared — import shared/vertical.js with:
   window.renderVertical({slug, num, title, sector, accent, hero, engine, features, useCases, testimonial})
*/

const CrumbsV = window.Crumbs;
const NavV = window.Nav;
const FooterV = window.Footer;
const OloV = window.Olo;

function VerticalHero({v}){
  return (
    <section className="p-hero hero-booking">
      <div className="container hero-grid">
        <div>
          <div className="eyebrow"><span className="dot"></span><span>{v.eyebrow}</span></div>
          <h1 dangerouslySetInnerHTML={{__html:v.headline}}/>
          <p className="sub">{v.sub}</p>
          <div className="ctas">
            <button className="btn royal lg">Prenota demo →</button>
            <button className="btn secondary lg">Vedi live demo</button>
          </div>
          <div className="trust">
            {v.trust.map((t,i)=><div key={i}><b>{t.n}</b><small>{t.l}</small></div>)}
          </div>
        </div>
        <div className="engine">
          <div style={{fontSize:11,color:'var(--ink-3)',fontFamily:'"JetBrains Mono",monospace',textTransform:'uppercase',letterSpacing:'.08em',marginBottom:14}}>
            ↓ motore configurato per {v.sector.toLowerCase()}
          </div>
          <div className="fields">
            {v.engineFields.map((f,i)=>(
              <div key={i} className="fld" style={i===0&&v.engineFields.length%2===1?{gridColumn:'span 2'}:{}}>
                <label>{f.label}</label>
                <div className="v">{f.value}</div>
              </div>
            ))}
          </div>
          <button className="btn royal cta" style={{width:'100%',marginTop:14,justifyContent:'center'}}>{v.engineCTA}</button>
          <div style={{marginTop:12,padding:'10px 12px',background:'var(--royal-soft)',borderRadius:10,fontSize:12,color:'var(--royal-ink)'}}>
            <span className="mono" style={{fontSize:10,background:'var(--royal)',color:'#fff',padding:'2px 6px',borderRadius:3,marginRight:6}}>CONFIG</span>
            {v.engineNote}
          </div>
        </div>
      </div>
    </section>
  );
}

function VerticalFeatures({v}){
  return (
    <section style={{padding:'100px 0'}}>
      <div className="container">
        <div className="sec-head">
          <div className="micro k">§ 02 · cosa include</div>
          <h2>{v.featHead}</h2>
          <p>{v.featSub}</p>
        </div>
        <div className="feature-grid">
          {v.features.map((f,i)=>(
            <div key={i} className="feature">
              <div className="num">0{i+1}</div>
              <h3>{f.t}</h3>
              <p>{f.p}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function VerticalUseCase({v}){
  return (
    <section style={{padding:'100px 0',background:'#fff',borderTop:'1px solid var(--line)',borderBottom:'1px solid var(--line)'}}>
      <div className="container">
        <div style={{display:'grid',gridTemplateColumns:'1fr 1fr',gap:60,alignItems:'center'}}>
          <div>
            <div className="micro" style={{marginBottom:20}}>§ 03 · caso d'uso reale</div>
            <p style={{fontSize:28,lineHeight:1.35,letterSpacing:'-.015em',margin:'0 0 28px',fontWeight:500}}>
              <span style={{color:'var(--royal)',fontFamily:'Georgia,serif',fontSize:72,lineHeight:0,verticalAlign:'-.35em',marginRight:4}}>"</span>
              {v.quote}
            </p>
            <div style={{display:'flex',alignItems:'center',gap:12}}>
              <div style={{width:44,height:44,borderRadius:'50%',background:v.avatarBg}}></div>
              <div>
                <div style={{fontWeight:800,fontSize:14}}>{v.quoteAuthor}</div>
                <div style={{fontSize:12,color:'var(--ink-3)',fontFamily:'"JetBrains Mono",monospace'}}>{v.quoteRole}</div>
              </div>
            </div>
          </div>
          <div style={{display:'grid',gridTemplateColumns:'1fr 1fr',gap:14}}>
            {v.kpis.map((k,i)=>(
              <div key={i} style={{padding:24,border:'1px solid var(--line)',borderRadius:16,background:'var(--paper)'}}>
                <div style={{fontSize:44,fontWeight:800,letterSpacing:'-.03em',lineHeight:1,color:'var(--royal-ink)'}}>{k.n}</div>
                <div style={{fontSize:12,color:'var(--ink-3)',marginTop:8,fontFamily:'"JetBrains Mono",monospace',textTransform:'uppercase',letterSpacing:'.08em'}}>{k.l}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

function VerticalNav({v}){
  const all=[
    {s:'accommodation',t:'Accommodation'},{s:'appointments',t:'Appointments'},{s:'events',t:'Events'},
    {s:'real-estate',t:'Real-estate'},{s:'rentals',t:'Rentals'},{s:'restaurants',t:'Restaurants'}
  ];
  return (
    <section style={{padding:'60px 0',background:'var(--paper)'}}>
      <div className="container">
        <div className="micro" style={{marginBottom:14}}>altre verticali di OLObooking</div>
        <div style={{display:'grid',gridTemplateColumns:'repeat(6,1fr)',gap:8}}>
          {all.map((a,i)=>(
            <a key={i} href={a.s===v.slug?null:`12-olobooking-${a.s}.html`} style={{
              padding:'14px 12px',background:a.s===v.slug?'var(--royal)':'#fff',color:a.s===v.slug?'#fff':'var(--ink)',
              border:'1px solid '+(a.s===v.slug?'var(--royal)':'var(--line)'),borderRadius:12,textAlign:'center',
              fontSize:12,fontWeight:700,cursor:a.s===v.slug?'default':'pointer',transition:'all .15s'
            }}>{a.t}</a>
          ))}
        </div>
      </div>
    </section>
  );
}

function VerticalCTA({v}){
  return (
    <section className="finalcta">
      <div className="container">
        <h2>{v.ctaTitle}</h2>
        <p>{v.ctaSub}</p>
        <div className="ctas">
          <button className="btn primary lg">Prenota demo →</button>
          <button className="btn secondary lg">Acquista €249 LTD</button>
        </div>
      </div>
    </section>
  );
}

window.renderVertical = function(v){
  const App = ()=>(
    <>
      <NavV current="booking"/>
      <CrumbsV items={['Home','Prodotti','OLObooking',v.sector]}/>
      <VerticalHero v={v}/>
      <VerticalFeatures v={v}/>
      <VerticalUseCase v={v}/>
      <VerticalNav v={v}/>
      <VerticalCTA v={v}/>
      <FooterV/>
    </>
  );
  ReactDOM.createRoot(document.getElementById('root')).render(<App/>);
};
