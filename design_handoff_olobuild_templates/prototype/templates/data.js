// Mock data for Gestione Template page

const TPL_TYPES = [
  { id: "all",    label: "Tutti",      count: 128 },
  { id: "page",   label: "Pagine",     count: 47, color: "primary" },
  { id: "header", label: "Header",     count: 5,  color: "blue" },
  { id: "footer", label: "Footer",     count: 4,  color: "slate" },
  { id: "single", label: "Single",     count: 38, color: "purple" },
  { id: "mega",   label: "Mega Panel", count: 6,  color: "amber" },
  { id: "widget", label: "Widget",     count: 22, color: "violet" },
  { id: "404",    label: "404",        count: 1,  color: "red" },
];

const TPL_TYPE_META = {
  page:   { label: "PAGINA",       color: "primary" },
  header: { label: "HEADER",       color: "blue" },
  footer: { label: "FOOTER",       color: "slate" },
  single: { label: "SINGLE",       color: "purple" },
  mega:   { label: "MEGA PANEL",   color: "amber" },
  widget: { label: "WIDGET",       color: "violet" },
  "404":  { label: "404",          color: "red" },
};

const TPL_LIST = [
  { id:179, title:"Che bel nome",         type:"page",   status:"published", elements:12,  date:"09 mag 2026", thumb:"linear-gradient(135deg,#dcefd2,#fff 70%)", preview:"hero+grid" },
  { id:176, title:"Test Tile 2",          type:"page",   status:"published", elements:8,   date:"09 mag 2026", thumb:"linear-gradient(135deg,#fff7ed,#fff 70%)", preview:"split" },
  { id:178, title:"test tile 5",          type:"page",   status:"published", elements:0,   date:"09 mag 2026", thumb:"linear-gradient(135deg,#f3f4f6,#fff 80%)", preview:"empty" },
  { id:177, title:"test tile 3",          type:"page",   status:"published", elements:0,   date:"09 mag 2026", thumb:"linear-gradient(135deg,#f3f4f6,#fff 80%)", preview:"empty" },
  { id:174, title:"Pagina Test Tile",     type:"page",   status:"published", elements:135, date:"09 mag 2026", thumb:"linear-gradient(135deg,#dcefd2,#fff 60%)", preview:"long" },
  { id:175, title:"Template senza titolo",type:"page",   status:"draft",     elements:0,   date:"09 mag 2026", thumb:"linear-gradient(135deg,#f3f4f6,#fff 80%)", preview:"empty" },

  { id: 98, title:"Business — Homepage",  type:"page",   status:"published", elements:22,  date:"08 mag 2026", thumb:"linear-gradient(135deg,#e0f2fe,#fff 70%)", preview:"hero+grid", attivo:true },
  { id: 40, title:"Home Baite",           type:"page",   status:"published", elements:9,   date:"07 mag 2026", thumb:"linear-gradient(135deg,#fef3c7,#fff 70%)", preview:"split" },
  { id:173, title:"Template senza titolo",type:"page",   status:"draft",     elements:12,  date:"07 mag 2026", thumb:"linear-gradient(135deg,#f3f4f6,#fff 80%)", preview:"long" },

  { id:181, title:"Header — Hotel Resort",type:"header", status:"published", elements:5,   date:"09 mag 2026", thumb:"linear-gradient(180deg,#1d2327,#1d2327 30%,#fff 32%,#fff 100%)", preview:"header", attivo:true },
  { id:201, title:"widget-test",          type:"widget", status:"published", elements:4,   date:"09 mag 2026", thumb:"linear-gradient(135deg,#f5f3ff,#fff 70%)", preview:"widget" },
  { id:140, title:"Template Baita Singola",type:"single",status:"published", elements:38,  date:"06 mag 2026", thumb:"linear-gradient(135deg,#faf5ff,#fff 70%)", preview:"long", attivo:true, singleType:"olo_service" },

  { id:160, title:"Footer — Standard",     type:"footer", status:"published", elements:6,   date:"05 mag 2026", thumb:"linear-gradient(0deg,#1d2327,#1d2327 25%,#fff 27%,#fff 100%)", preview:"footer", attivo:true },
  { id:165, title:"Mega Menu — Servizi",   type:"mega",   status:"published", elements:14,  date:"04 mag 2026", thumb:"linear-gradient(135deg,#fef3c7,#fff 70%)", preview:"grid" },
  { id:170, title:"Single — Articolo Blog",type:"single", status:"published", elements:18,  date:"03 mag 2026", thumb:"linear-gradient(135deg,#faf5ff,#fff 70%)", preview:"long", attivo:true, singleType:"post" },
  { id:172, title:"404 — Personalizzata",  type:"404",    status:"published", elements:3,   date:"01 mag 2026", thumb:"linear-gradient(135deg,#fee2e2,#fff 70%)", preview:"empty" },
];

const TPL_NEW_OPTIONS = [
  { group:"Standard", items:[
    { id:"page",   label:"Nuova Pagina",     icon:"fileText", color:"primary" },
    { id:"header", label:"Nuovo Header",     icon:"panelRight", color:"blue" },
    { id:"footer", label:"Nuovo Footer",     icon:"panelRight", color:"slate" },
    { id:"mega",   label:"Nuovo Mega Panel", icon:"grid", color:"amber" },
    { id:"widget", label:"Nuovo Widget",     icon:"tag", color:"violet" },
    { id:"404",    label:"Nuova 404",        icon:"warn", color:"red" },
  ]},
  { group:"Template Single", items:[
    { id:"s_post",     label:"Single: Articoli",  icon:"fileText", color:"purple" },
    { id:"s_product",  label:"Single: Prodotti",  icon:"tag",      color:"purple" },
    { id:"s_location", label:"Single: Locations", icon:"map",      color:"purple" },
    { id:"s_struct",   label:"Single: Strutture", icon:"globe",    color:"purple" },
    { id:"s_course",   label:"Single: Corsi",     icon:"play",     color:"purple" },
    { id:"s_event",    label:"Single: Eventi",    icon:"bell",     color:"purple" },
  ]},
];

window.TPL_TYPES = TPL_TYPES;
window.TPL_TYPE_META = TPL_TYPE_META;
window.TPL_LIST = TPL_LIST;
window.TPL_NEW_OPTIONS = TPL_NEW_OPTIONS;
