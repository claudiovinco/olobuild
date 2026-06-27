// OLObuild — Ad campaign components.
// 6 ads in 3 formats (banner / square / story) presented in a design canvas.

function ScaleWrap({ width, height, children }) {
  // Wrap natural-size ad in a container that scales to fit the artboard
  return (
    <div className="ad-scale-wrap">
      <div style={{
        width, height,
        transform: "scale(var(--ad-scale, 1))",
        transformOrigin: "center center",
        // Compute scale via inline style: use container width
      }} ref={el => {
        if (!el) return;
        const parent = el.parentElement;
        if (!parent) return;
        const fit = () => {
          const sx = parent.clientWidth / width;
          const sy = parent.clientHeight / height;
          el.style.setProperty("--ad-scale", Math.min(sx, sy).toString());
        };
        fit();
        // Re-fit when the canvas resizes
        const ro = new ResizeObserver(fit);
        ro.observe(parent);
      }}>
        {children}
      </div>
    </div>
  );
}

/* ════════════════════════════════════════════════════
   1 · Banner 1200×628 — Hero with tilted mockup
   ════════════════════════════════════════════════════ */
function Ad1() {
  return (
    <ScaleWrap width={1200} height={628}>
      <div className="ad-canvas ad-banner ad-pad-xl">
        <div className="ad-glow-tr"/>
        <div className="ad-glow-bl"/>
        <div className="ad-grain"/>

        <div style={{position:"relative",zIndex:2,display:"grid",gridTemplateColumns:"1fr 1fr",gap:48,height:"100%",alignItems:"center"}}>
          <div>
            <img src="assets/olobuild-horizontal.png" className="ad-logo" alt="OLObuild" style={{marginBottom:32}}/>
            <h2 className="ad-display" style={{fontSize:72,margin:0}}>
              WordPress,<br/>
              <em>finalmente</em><br/>
              sotto controllo.
            </h2>
            <p style={{fontSize:16,color:"var(--mk-text-dim)",margin:"24px 0 32px",lineHeight:1.5,maxWidth:"36ch"}}>
              Il page builder italiano che ridisegna l'esperienza WordPress.
            </p>
            <button className="ad-btn ad-btn-big">Prova gratis 14 giorni →</button>
          </div>

          <div style={{position:"relative",height:"100%"}}>
            <div style={{position:"absolute",top:"50%",left:0,right:-100,
              transform:"translateY(-50%) perspective(2000px) rotateY(-18deg) rotateX(6deg)",
              transformOrigin:"center"}}>
              <div className="mk-mock lift-shadow" style={{borderRadius:14}}>
                <BuilderMockup variant="templates" width={760}/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </ScaleWrap>
  );
}

/* ════════════════════════════════════════════════════
   2 · Banner 1200×628 — Feature zoom (rail elementi)
   ════════════════════════════════════════════════════ */
function Ad2() {
  return (
    <ScaleWrap width={1200} height={628}>
      <div className="ad-canvas ad-banner ad-cream ad-pad-xl">
        <div style={{position:"absolute",top:-200,right:-200,width:600,height:600,borderRadius:"50%",
          background:"radial-gradient(circle, rgba(225,71,79,.18) 0%, transparent 70%)",filter:"blur(40px)",zIndex:0}}/>

        <div style={{position:"relative",zIndex:2,display:"grid",gridTemplateColumns:"1.1fr 1fr",gap:48,height:"100%",alignItems:"center"}}>
          <div style={{position:"relative",height:"100%"}}>
            <div style={{position:"absolute",top:"50%",left:-40,
              transform:"translateY(-50%) perspective(2200px) rotateY(12deg) rotateX(4deg)",
              transformOrigin:"center"}}>
              <div className="mk-mock lift-shadow" style={{borderRadius:14}}>
                <BuilderMockup variant="rail-zoom" width={620}/>
              </div>
            </div>
          </div>
          <div>
            <div className="ad-eyebrow" style={{marginBottom:18}}>RAIL ELEMENTI</div>
            <h2 className="ad-display" style={{fontSize:62,margin:0,color:"var(--mk-navy)"}}>
              <em className="cream">97 elementi.</em><br/>
              Una sola colonna<br/>
              che funziona.
            </h2>
            <p style={{fontSize:15,color:"#6b5b54",margin:"24px 0 32px",lineHeight:1.55,maxWidth:"34ch"}}>
              Categorie sempre visibili, ricerca cross-categoria, preferiti pinnati.
            </p>
            <button className="ad-btn ad-btn-big">Scopri di più →</button>
            <div style={{marginTop:24,fontSize:13,color:"#a89084"}}>
              <span className="ad-url" style={{color:"#b8323a"}}>olobuild.it</span>
            </div>
          </div>
        </div>
      </div>
    </ScaleWrap>
  );
}

/* ════════════════════════════════════════════════════
   3 · Square 1080 — Bold poster (97)
   ════════════════════════════════════════════════════ */
function Ad3() {
  return (
    <ScaleWrap width={1080} height={1080}>
      <div className="ad-canvas ad-square ad-pad-l">
        <div className="ad-glow-tr"/>
        <div className="ad-grain"/>

        <div style={{position:"relative",zIndex:2,display:"flex",flexDirection:"column",height:"100%"}}>
          <div style={{display:"flex",alignItems:"center",gap:12,marginBottom:40}}>
            <img src="assets/olobuild-horizontal.png" className="ad-logo" alt="OLObuild"/>
            <span className="mk-chip" style={{marginLeft:"auto"}}>
              <span className="dot"/>
              v3.34.6
            </span>
          </div>

          <div style={{flex:1,display:"flex",flexDirection:"column",justifyContent:"center"}}>
            <div className="ad-display" style={{fontSize:380,lineHeight:.85,color:"rgb(var(--mk-glow-r))",letterSpacing:"-.05em",marginBottom:8}}>
              97
            </div>
            <div className="ad-display" style={{fontSize:54,maxWidth:"15ch"}}>
              elementi che cambiano <em>tutto.</em>
            </div>
            <p style={{fontSize:18,color:"var(--mk-text-dim)",marginTop:20,maxWidth:"34ch",lineHeight:1.5}}>
              Una colonna pulita, ricerca istantanea, drag&amp;drop fluido. WordPress, ridisegnato.
            </p>
          </div>

          <div style={{display:"flex",alignItems:"center",justifyContent:"space-between",marginTop:40}}>
            <button className="ad-btn ad-btn-big">Prova gratis →</button>
            <span className="ad-url">olobuild.it</span>
          </div>
        </div>
      </div>
    </ScaleWrap>
  );
}

/* ════════════════════════════════════════════════════
   4 · Square 1080 — Templates stack
   ════════════════════════════════════════════════════ */
function Ad4() {
  return (
    <ScaleWrap width={1080} height={1080}>
      <div className="ad-canvas ad-square ad-cream ad-pad-l">
        <div style={{position:"absolute",bottom:-150,right:-150,width:500,height:500,borderRadius:"50%",
          background:"radial-gradient(circle, rgba(225,71,79,.2) 0%, transparent 70%)",filter:"blur(40px)",zIndex:0}}/>

        <div style={{position:"relative",zIndex:2,display:"flex",flexDirection:"column",height:"100%"}}>
          <img src="assets/olobuild-horizontal.png" className="ad-logo" alt="OLObuild" style={{marginBottom:36}}/>

          <div className="ad-display" style={{fontSize:90,margin:0,color:"var(--mk-navy)"}}>
            <em className="cream">128</em><br/>
            template<br/>
            italiani.
          </div>
          <p style={{fontSize:18,color:"#6b5b54",margin:"20px 0 0",maxWidth:"30ch",lineHeight:1.5}}>
            Hotel, ristoranti, studi, e-commerce. Parti da uno dei nostri.
          </p>

          {/* Stack of overlapping template cards */}
          <div style={{flex:1,position:"relative",marginTop:48}}>
            {[
              { top:0, left:40, rot:-8, t:"Hotel Resort", cat:"PAGINA", c:"#e1474f", soft:"#fde2e4",
                preview:<div style={{padding:14,display:"flex",flexDirection:"column",gap:8,height:"100%"}}>
                  <div style={{flex:"1.5",borderRadius:6,background:"linear-gradient(135deg,#e1474f55,#e1474f11)",padding:10,display:"flex",flexDirection:"column",justifyContent:"flex-end",gap:4}}>
                    <div style={{height:9,width:"60%",background:"rgba(15,17,21,.5)",borderRadius:3}}/>
                    <div style={{height:5,width:"40%",background:"rgba(15,17,21,.25)",borderRadius:2}}/>
                  </div>
                  <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:6}}>
                    {[0,1,2].map(i=><div key={i} style={{height:34,background:"rgba(15,17,21,.06)",borderRadius:4}}/>)}
                  </div>
                </div>},
              { top:80, left:240, rot:4, t:"Trattoria del Borgo", cat:"PAGINA", c:"#f59e0b", soft:"#fef3c7",
                preview:<div style={{padding:14,display:"flex",flexDirection:"column",gap:8,height:"100%"}}>
                  <div style={{flex:"1.2",borderRadius:6,background:"linear-gradient(135deg,#f59e0b55,#f59e0b11)"}}/>
                  <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:6}}>
                    {[0,1].map(i=><div key={i} style={{height:30,background:"rgba(15,17,21,.06)",borderRadius:4}}/>)}
                  </div>
                </div>},
              { top:170, left:480, rot:10, t:"Studio Tomasi", cat:"PAGINA", c:"#22c55e", soft:"#dcfce7",
                preview:<div style={{padding:14,display:"flex",flexDirection:"column",gap:8,height:"100%"}}>
                  <div style={{height:9,width:"55%",background:"rgba(15,17,21,.4)",borderRadius:3,marginBottom:4}}/>
                  <div style={{height:5,width:"80%",background:"rgba(15,17,21,.15)",borderRadius:2}}/>
                  <div style={{height:5,width:"60%",background:"rgba(15,17,21,.15)",borderRadius:2}}/>
                  <div style={{flex:1,marginTop:4,background:"linear-gradient(135deg,#22c55e44,#22c55e11)",borderRadius:6}}/>
                </div>},
            ].map((card,i)=>(
              <div key={i} style={{
                position:"absolute",top:card.top,left:card.left,
                width:340,
                transform:`rotate(${card.rot}deg) perspective(1200px) rotateY(${card.rot*-0.4}deg) rotateX(4deg)`,
                transformOrigin:"center",
              }}>
                <div className="mk-mock" style={{borderRadius:14,boxShadow:"0 30px 60px -10px rgba(0,0,0,.25), 0 1px 0 rgba(255,255,255,.5) inset"}}>
                  <div style={{aspectRatio:"16/10",position:"relative",background:`linear-gradient(135deg, ${card.soft}, #fff 70%)`,borderRadius:"14px 14px 0 0"}}>
                    {card.preview}
                    <div style={{position:"absolute",top:10,left:10,fontSize:9,fontWeight:700,padding:"3px 8px",borderRadius:99,background:card.c,color:"#fff",letterSpacing:".06em"}}>{card.cat}</div>
                  </div>
                  <div style={{padding:"12px 14px",background:"#fff",borderRadius:"0 0 14px 14px"}}>
                    <div style={{fontSize:13,fontWeight:600,color:"var(--mk-navy)"}}>{card.t}</div>
                    <div style={{fontSize:10,color:"#94a3b8",fontFamily:"ui-monospace,monospace",marginTop:2}}>[olo_template id="{179-i}"]</div>
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div style={{display:"flex",alignItems:"center",justifyContent:"space-between",marginTop:24,position:"relative",zIndex:5}}>
            <button className="ad-btn ad-btn-big">Sfoglia i template →</button>
            <span className="ad-url" style={{color:"#b8323a"}}>olobuild.it</span>
          </div>
        </div>
      </div>
    </ScaleWrap>
  );
}

/* ════════════════════════════════════════════════════
   5 · Story 1080×1920 — Vertical hero
   ════════════════════════════════════════════════════ */
function Ad5() {
  return (
    <ScaleWrap width={1080} height={1920}>
      <div className="ad-canvas ad-story ad-pad-st">
        <div className="ad-glow-tr"/>
        <div style={{position:"absolute",bottom:-300,left:-200,width:700,height:700,borderRadius:"50%",
          background:"radial-gradient(circle, rgba(225,71,79,.4) 0%, transparent 70%)",filter:"blur(70px)",zIndex:0}}/>
        <div className="ad-grain"/>

        <div style={{position:"relative",zIndex:2,display:"flex",flexDirection:"column",height:"100%"}}>
          <div style={{display:"flex",alignItems:"center",gap:12}}>
            <img src="assets/olobuild-horizontal.png" className="ad-logo" alt="OLObuild" style={{height:28}}/>
            <span className="mk-chip" style={{marginLeft:"auto"}}>
              <span className="dot"/>
              Novità
            </span>
          </div>

          <div style={{marginTop:80}}>
            <h2 className="ad-display" style={{fontSize:130,margin:0}}>
              WordPress,<br/>
              <em>finalmente</em><br/>
              sotto<br/>
              controllo.
            </h2>
            <p style={{fontSize:24,color:"var(--mk-text-dim)",marginTop:32,maxWidth:"24ch",lineHeight:1.4}}>
              Il page builder italiano che mette ordine.
            </p>
          </div>

          {/* Mockup floating in middle */}
          <div style={{flex:1,position:"relative",marginTop:60,marginBottom:40}}>
            <div style={{position:"absolute",top:"50%",left:"50%",
              transform:"translate(-50%,-50%) perspective(2400px) rotateY(-8deg) rotateX(6deg)"}}>
              <div className="mk-mock lift-shadow" style={{borderRadius:18}}>
                <BuilderMockup variant="full" width={1000}/>
              </div>
            </div>
          </div>

          <div style={{display:"flex",gap:24,marginBottom:48}}>
            <div>
              <div className="ad-display" style={{fontSize:64,color:"rgb(var(--mk-glow-r))",lineHeight:1}}>97</div>
              <div style={{fontSize:13,color:"var(--mk-text-dim)",marginTop:4,letterSpacing:".08em",textTransform:"uppercase",fontWeight:500}}>elementi</div>
            </div>
            <div>
              <div className="ad-display" style={{fontSize:64,color:"rgb(var(--mk-glow-r))",lineHeight:1}}>128</div>
              <div style={{fontSize:13,color:"var(--mk-text-dim)",marginTop:4,letterSpacing:".08em",textTransform:"uppercase",fontWeight:500}}>template</div>
            </div>
            <div>
              <div className="ad-display" style={{fontSize:64,color:"rgb(var(--mk-glow-r))",lineHeight:1}}>1.2k</div>
              <div style={{fontSize:13,color:"var(--mk-text-dim)",marginTop:4,letterSpacing:".08em",textTransform:"uppercase",fontWeight:500}}>siti live</div>
            </div>
          </div>

          <button className="ad-btn ad-btn-big" style={{width:"100%",justifyContent:"center",padding:"24px",fontSize:20}}>
            Prova gratis 14 giorni →
          </button>
          <div style={{textAlign:"center",marginTop:16,color:"var(--mk-text-faint)",fontSize:13}}>
            <span className="ad-url">olobuild.it</span>
          </div>
        </div>
      </div>
    </ScaleWrap>
  );
}

/* ════════════════════════════════════════════════════
   6 · Story 1080×1920 — Before/After vertical split
   ════════════════════════════════════════════════════ */
function Ad6() {
  return (
    <ScaleWrap width={1080} height={1920}>
      <div className="ad-canvas ad-story" style={{padding:0}}>
        {/* Top half — BEFORE */}
        <div style={{height:"50%",background:"#14171f",position:"relative",padding:"64px",overflow:"hidden"}}>
          <div style={{position:"absolute",inset:0,opacity:.3,background:"repeating-linear-gradient(180deg, transparent 0 40px, rgba(255,255,255,.04) 40px 41px)"}}/>
          <div style={{position:"relative",zIndex:2,height:"100%",display:"flex",flexDirection:"column"}}>
            <div style={{display:"flex",alignItems:"center",justifyContent:"space-between"}}>
              <span style={{fontSize:14,fontWeight:600,letterSpacing:".18em",textTransform:"uppercase",color:"var(--mk-text-faint)"}}>PRIMA</span>
              <span style={{fontSize:12,color:"var(--mk-text-faint)"}}>Altri builder</span>
            </div>
            <h2 className="ad-display" style={{fontSize:80,margin:"40px 0 0",maxWidth:"12ch",color:"#fff"}}>
              Caos in una<br/>colonna.
            </h2>

            <div style={{flex:1,marginTop:40,position:"relative",overflow:"hidden"}}>
              <div style={{position:"absolute",inset:0,display:"flex",flexDirection:"column",gap:8}}>
                {Array.from({length:12}).map((_,i)=>(
                  <div key={i} style={{display:"flex",alignItems:"center",gap:12,padding:"10px 14px",background:"rgba(255,255,255,.04)",borderRadius:6,border:"1px solid rgba(255,255,255,.06)"}}>
                    <div style={{width:28,height:28,background:"rgba(255,255,255,.1)",borderRadius:4}}/>
                    <div style={{height:8,background:"rgba(255,255,255,.12)",borderRadius:3,width:`${45+(i*13)%50}%`}}/>
                    <div style={{marginLeft:"auto",width:18,height:18,background:"rgba(255,255,255,.06)",borderRadius:99}}/>
                  </div>
                ))}
                <div style={{position:"absolute",bottom:0,left:0,right:0,height:120,background:"linear-gradient(180deg,transparent,#14171f)"}}/>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom half — AFTER */}
        <div style={{height:"50%",background:"linear-gradient(180deg,#1c0a0b,#0b0d12)",position:"relative",padding:"64px",overflow:"hidden"}}>
          <div className="ad-glow-tr" style={{width:600,height:600,top:-100,right:-200}}/>
          <div className="ad-grain"/>

          <div style={{position:"relative",zIndex:2,height:"100%",display:"flex",flexDirection:"column"}}>
            <div style={{display:"flex",alignItems:"center",justifyContent:"space-between"}}>
              <span style={{fontSize:14,fontWeight:600,letterSpacing:".18em",textTransform:"uppercase",color:"rgb(var(--mk-glow-r))"}}>DOPO</span>
              <img src="assets/olobuild-horizontal.png" className="ad-logo" alt="OLObuild" style={{height:20}}/>
            </div>
            <h2 className="ad-display" style={{fontSize:80,margin:"40px 0 0",maxWidth:"12ch",color:"#fff"}}>
              <em>Una</em> rail.<br/>Sempre a fuoco.
            </h2>

            <div style={{flex:1,marginTop:40,position:"relative"}}>
              <div style={{position:"absolute",left:"50%",top:0,transform:"translateX(-50%) perspective(1800px) rotateX(8deg)"}}>
                <div className="mk-mock lift-shadow" style={{borderRadius:14}}>
                  <BuilderMockup variant="rail-zoom" width={840}/>
                </div>
              </div>
            </div>

            <div style={{marginTop:"auto",display:"flex",alignItems:"center",justifyContent:"space-between",paddingTop:24}}>
              <button className="ad-btn ad-btn-big" style={{fontSize:18,padding:"20px 32px"}}>
                Prova OLObuild →
              </button>
              <span className="ad-url" style={{fontSize:16}}>olobuild.it</span>
            </div>
          </div>
        </div>
      </div>
    </ScaleWrap>
  );
}

function AdSet() {
  return (
    <DesignCanvas initialZoom={0.5}>
      <DCSection id="banners" title="Banner web 1200×628"
        subtitle="Per landing, retargeting, Google Display, LinkedIn.">
        <DCArtboard id="banner-1" label="Banner · Hero mockup" width={1200} height={628}>
          <Ad1/>
        </DCArtboard>
        <DCArtboard id="banner-2" label="Banner · Feature zoom" width={1200} height={628}>
          <Ad2/>
        </DCArtboard>
      </DCSection>

      <DCSection id="squares" title="Post quadrati 1080×1080"
        subtitle="Instagram feed, LinkedIn, Facebook.">
        <DCArtboard id="sq-1" label="Square · Tipografia 97" width={1080} height={1080}>
          <Ad3/>
        </DCArtboard>
        <DCArtboard id="sq-2" label="Square · Template stack" width={1080} height={1080}>
          <Ad4/>
        </DCArtboard>
      </DCSection>

      <DCSection id="stories" title="Story verticali 1080×1920"
        subtitle="Instagram & Facebook stories, TikTok, Snap.">
        <DCArtboard id="st-1" label="Story · Hero verticale" width={1080} height={1920}>
          <Ad5/>
        </DCArtboard>
        <DCArtboard id="st-2" label="Story · Prima / Dopo" width={1080} height={1920}>
          <Ad6/>
        </DCArtboard>
      </DCSection>
    </DesignCanvas>
  );
}

window.AdSet = AdSet;
