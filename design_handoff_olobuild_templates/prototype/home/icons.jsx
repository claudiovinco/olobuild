// Extra icons for home dashboard. Adds to window.OLOIcon registry by patching ICONS.
// (We render via a local copy here to avoid touching the editor icons set.)
const HOME_ICONS = {
  fileText:  <><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></>,
  edit:      <path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/>,
  upload:    <path d="M12 4v12M7 9l5-5 5 5M5 20h14"/>,
  chart:     <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>,
  cookie:    <><circle cx="12" cy="12" r="9"/><circle cx="9" cy="9" r="1"/><circle cx="15" cy="11" r="1"/><circle cx="11" cy="15" r="1"/></>,
  redirect:  <path d="M3 9l4-4 4 4M7 5v9a4 4 0 004 4h7M21 15l-4 4-4-4"/>,
  wrench:    <path d="M14 7a4 4 0 105.7 3.7l-7 7L9 14l8-8a4 4 0 00-3 1z"/>,
  tag:       <><path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="7.5" r="1"/></>,
  users:     <><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><circle cx="17" cy="9" r="2.5"/><path d="M14 20a5 5 0 017-4.5"/></>,
  inbox:     <path d="M3 12l3-7h12l3 7v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6zM3 12h5l1 2h6l1-2h5"/>,
  key:       <><circle cx="8" cy="15" r="4"/><path d="M11 12l9-9 2 2-2 2 2 2-2 2-3-3"/></>,
  bell:      <path d="M6 16V11a6 6 0 0112 0v5l2 2H4l2-2zM10 20a2 2 0 004 0"/>,
  rocket:    <path d="M5 19l3-3a4 4 0 015.7 0l.3.3 4-9-9 4 .3.3a4 4 0 010 5.7L5 19zM4 14a3 3 0 00-1 6 3 3 0 006-1"/>,
  trendUp:   <path d="M3 17l6-6 4 4 8-8M14 7h7v7"/>,
  trendFlat: <path d="M3 12h18"/>,
  warn:      <><path d="M12 4l9 16H3z"/><path d="M12 11v4M12 18h.01"/></>,
  pin2:      <path d="M14 3l7 7-3 3 1 5-5-1-7 7v-4l-3-3 7-7-1-5z"/>,
  external:  <path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/>,
  globe:     <><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></>,
  arrow:     <path d="M5 12h14M13 6l6 6-6 6"/>,
  panelRight:<><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M15 4v16"/></>,
  collapse:  <path d="M9 6l-6 6 6 6M15 6l6 6-6 6"/>,
  dot3:      <><circle cx="6" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="18" cy="12" r="1.5"/></>,
  question:  <><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/></>,
  play:      <path d="M7 5l12 7-12 7z"/>,
  user:      <><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></>,
  pinFill:   <path d="M14 3l7 7-3 3 1 5-5-1-7 7v-4l-3-3 7-7-1-5z" fill="currentColor"/>,
  copy:      <><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></>,
  trash:     <path d="M4 7h16M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2M6 7l1 13a2 2 0 002 2h6a2 2 0 002-2l1-13"/>,
  chevronDown: <path d="M6 9l6 6 6-6"/>,
  grid:      <><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></>,
  list:      <><path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></>,
};

function HomeIcon({ name, size = 18, ...rest }) {
  const path = HOME_ICONS[name];
  if (!path) {
    return <OLOIcon name={name} size={size} {...rest}/>;
  }
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none"
         stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round" {...rest}>
      {path}
    </svg>
  );
}

window.HomeIcon = HomeIcon;
