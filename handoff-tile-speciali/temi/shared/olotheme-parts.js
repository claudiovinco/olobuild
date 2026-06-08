/* Shared React parts: Olo logo, Nav, Footer, Crumbs — globals via window */

const Olo = ({c='currentColor',size}) => (
  React.createElement('span',{className:`olo ${size||''}`,style:{color:c}},
    React.createElement('b'),React.createElement('i'),React.createElement('b'))
);

const Nav = ({current='booking'}) => React.createElement('nav',{className:'nav'},
  React.createElement('div',{className:'nav-row'},
    React.createElement('a',{className:'nav-logo'},
      React.createElement(Olo,{c:'var(--navy)'}),
      React.createElement('span',null,'OLOtheme')),
    React.createElement('div',{className:'nav-links'},
      React.createElement('span',null,'Prodotti ▾'),
      React.createElement('span',null,'Soluzioni ▾'),
      React.createElement('span',null,'Pricing'),
      React.createElement('span',null,'Risorse ▾'),
      React.createElement('span',null,'Chi siamo')),
    React.createElement('div',{className:'nav-right'},
      React.createElement('span',{className:'mono',style:{fontSize:11,color:'var(--ink-3)'}},'IT · EN · DE'),
      React.createElement('a',{style:{fontSize:13,color:'var(--ink-2)',fontWeight:500}},'Accedi'),
      React.createElement('button',{className:'btn primary sm'},'Prenota demo →'))));

const Crumbs = ({items}) => React.createElement('div',{className:'crumbs'},
  items.map((it,i) => React.createElement(React.Fragment,{key:i},
    i>0 && React.createElement('span',{className:'sep'},'/'),
    React.createElement('span',{className:i===items.length-1?'now':''},it))));

const Footer = () => React.createElement('footer',null,
  React.createElement('div',{className:'container'},
    React.createElement('div',{className:'f-grid'},
      React.createElement('div',null,
        React.createElement('div',{className:'brand'},React.createElement(Olo,{c:'#fff',size:'md'}),React.createElement('span',null,'OLOtheme')),
        React.createElement('p',{className:'bio'},'Suite WordPress · un telaio, sei prodotti. Costruita in Italia dal 2014.')),
      React.createElement('div',null,React.createElement('h5',null,'Prodotti'),
        React.createElement('ul',null,['OLObooking','OLObuild','OLOlang','OLOcalendar','OLOtour','OLOtutor'].map((x,i)=>React.createElement('li',{key:i},x)))),
      React.createElement('div',null,React.createElement('h5',null,'Soluzioni'),
        React.createElement('ul',null,['Hotel & ospitalità','Real-estate','Academy & scuole','Agenzie WP','Ristorazione'].map((x,i)=>React.createElement('li',{key:i},x)))),
      React.createElement('div',null,React.createElement('h5',null,'Risorse'),
        React.createElement('ul',null,['Docs','Changelog','Blog','Roadmap','Community'].map((x,i)=>React.createElement('li',{key:i},x)))),
      React.createElement('div',null,React.createElement('h5',null,'Azienda'),
        React.createElement('ul',null,['Chi siamo','Pricing','Affiliate','Contatti','Supporto'].map((x,i)=>React.createElement('li',{key:i},x))))),
    React.createElement('div',{className:'f-bot'},
      React.createElement('span',null,'© 2026 OLOtheme Srl · P.IVA 0123456789'),
      React.createElement('span',null,'Privacy · Termini · Cookie · GPL-v3'))));

Object.assign(window,{Olo,Nav,Crumbs,Footer});
