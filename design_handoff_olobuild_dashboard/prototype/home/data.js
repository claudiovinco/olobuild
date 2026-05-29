// OLObuild — WordPress dashboard homepage redesign
// Modern cockpit layout: WP shell + hero + KPI + recent + grid + collapsible right panel.

const { useState: useStateH } = React;

const HOME_RECENT = [
  { id: "r1", title: "Home — Hotel Resort", type: "Pagina", time: "2 min fa", thumb: "linear-gradient(135deg,#a7d7f9,#79b8e8)", status: "draft" },
  { id: "r2", title: "Servizi & Prezzi",     type: "Pagina", time: "1 ora fa", thumb: "linear-gradient(135deg,#fde68a,#f59e0b)" },
  { id: "r3", title: "Header globale v2",    type: "Template", time: "3 ore fa", thumb: "linear-gradient(135deg,#bbf7d0,#4a8c2a)" },
  { id: "r4", title: "Landing Black Friday", type: "Pagina", time: "Ieri", thumb: "linear-gradient(135deg,#fecaca,#ef4444)", status: "live" },
  { id: "r5", title: "Footer aziendale",     type: "Template", time: "2 giorni fa", thumb: "linear-gradient(135deg,#e9d5ff,#a855f7)" },
  { id: "r6", title: "Privacy e cookie",     type: "Pagina", time: "3 giorni fa", thumb: "linear-gradient(135deg,#cffafe,#06b6d4)" },
];

const HOME_KPIS = [
  { label: "Pagine pubblicate",  value: "47",  delta: "+3 questa settimana", trend: "up",   icon: "fileText" },
  { label: "Template attivi",    value: "12",  delta: "1 in bozza",          trend: "flat", icon: "template" },
  { label: "Invii form (7gg)",   value: "128", delta: "+24% vs scorsa",      trend: "up",   icon: "form" },
  { label: "Avvisi da risolvere", value: "5",   delta: "2 SEO · 3 404",       trend: "warn", icon: "alert" },
];

const HOME_QUICK = [
  { id: "new",      label: "Crea pagina",     hint: "Da template o vuota",       icon: "plus",     tone: "primary" },
  { id: "open",     label: "Apri editor",     hint: "Riprendi Home — Hotel",     icon: "edit",     tone: "info" },
  { id: "tpl",      label: "Sfoglia template", hint: "120+ pronti all'uso",      icon: "template", tone: "purple" },
  { id: "import",   label: "Importa",         hint: "Pagina, sito, JSON",        icon: "upload",   tone: "neutral" },
];

const HOME_MANAGE = [
  { id: "tpl",   label: "Gestione Template",  hint: "Crea e modifica i tuoi template", icon: "template", color: "#f97316" },
  { id: "cfg",   label: "Configurazione",     hint: "Stili, colori, tipografia e API", icon: "sliders",  color: "#1f2937" },
  { id: "media", label: "Ricerca Media",      hint: "Foto, video e audio stock",       icon: "image",    color: "#a855f7" },
  { id: "form",  label: "Invii Form",         hint: "128 nuovi messaggi",              icon: "form",     color: "#10b981", badge: 128 },
  { id: "an",    label: "Analytics",          hint: "Tracking e statistiche",          icon: "chart",    color: "#3b82f6" },
  { id: "cc",    label: "Cookie Consent",     hint: "Banner GDPR e consenso",          icon: "cookie",   color: "#f59e0b" },
  { id: "seo",   label: "SEO",                hint: "Meta tag, OG, sitemap",           icon: "search",   color: "#06b6d4" },
  { id: "404",   label: "Redirect & 404",     hint: "3 errori da gestire",             icon: "redirect", color: "#ef4444", badge: 3 },
  { id: "perf",  label: "Performance",        hint: "Cache, lazy load, ottimizzazione", icon: "zap",     color: "#eab308" },
  { id: "tools", label: "Strumenti",          hint: "Cache, manutenzione, URL",        icon: "wrench",   color: "#64748b" },
  { id: "woo",   label: "WooCommerce",        hint: "Template prodotti e shop",        icon: "cart",     color: "#7e22ce" },
  { id: "pop",   label: "Popup Globali",      hint: "4 attivi",                        icon: "modal",    color: "#0ea5e9" },
];

const HOME_SYSTEM = [
  { id: "wl",   label: "White Label",  icon: "tag" },
  { id: "imp",  label: "Import/Export", icon: "upload" },
  { id: "perm", label: "Permessi & Ruoli", icon: "users" },
  { id: "subs", label: "Submissions", icon: "inbox" },
  { id: "log",  label: "Log e diagnostica", icon: "history" },
  { id: "lic",  label: "Licenza", icon: "key" },
];

const HOME_CHANGELOG = [
  { v: "v3.34.6", date: "9 mag", tag: "novità", items: ["Mega menu drag&drop", "Performance: lazy load video", "Fix Safari sticky header"] },
  { v: "v3.34.5", date: "2 mag", tag: "fix",    items: ["Cookie consent IT/EN", "Meta tag duplicati"] },
  { v: "v3.34.4", date: "26 apr", tag: "novità", items: ["Modulo PDF Viewer", "Nuovi 12 template"] },
];

const HOME_LEARN = [
  { id: "l1", title: "Onboarding 60 secondi",    duration: "1:02", thumb: "linear-gradient(135deg,#4a8c2a,#3fa23f)", iconBg: "🚀" },
  { id: "l2", title: "Template come pro",        duration: "4:18", thumb: "linear-gradient(135deg,#f97316,#ef4444)", iconBg: "🎨" },
  { id: "l3", title: "SEO e Open Graph",         duration: "3:45", thumb: "linear-gradient(135deg,#3b82f6,#1d4ed8)", iconBg: "🔍" },
  { id: "l4", title: "Performance: punteggio 100", duration: "5:30", thumb: "linear-gradient(135deg,#eab308,#ca8a04)", iconBg: "⚡" },
];

window.HOME_RECENT = HOME_RECENT;
window.HOME_KPIS = HOME_KPIS;
window.HOME_QUICK = HOME_QUICK;
window.HOME_MANAGE = HOME_MANAGE;
window.HOME_SYSTEM = HOME_SYSTEM;
window.HOME_CHANGELOG = HOME_CHANGELOG;
window.HOME_LEARN = HOME_LEARN;
