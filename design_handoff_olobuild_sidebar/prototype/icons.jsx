// Lucide-style stroke icons used across all variants.
// Pure JSX renderers exposed on window.OLOIcon(name).

const _IP = { width: 16, height: 16, viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", strokeWidth: 1.6, strokeLinecap: "round", strokeLinejoin: "round" };

const ICONS = {
  // Categories
  clock:    <><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></>,
  star:     <path d="M12 3l2.7 5.5 6 .9-4.4 4.3 1 6L12 17l-5.4 2.8 1-6L3.3 9.4l6-.9z"/>,
  square:   <rect x="4" y="4" width="16" height="16" rx="3"/>,
  layout:   <><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></>,
  text:     <path d="M5 6h14M5 12h10M5 18h14"/>,
  image:    <><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="1.5"/><path d="M21 15l-5-5L5 21"/></>,
  megaphone:<><path d="M3 11v2a1 1 0 001 1h2l5 4V6L6 10H4a1 1 0 00-1 1z"/><path d="M16 8a4 4 0 010 8M19 5a8 8 0 010 14"/></>,
  spark:    <path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/>,
  form:     <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h6M7 17h4"/></>,
  cart:     <><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/><path d="M3 4h2l2.4 11a2 2 0 002 1.6h7.2a2 2 0 002-1.5L20 8H6"/></>,

  // Element-level
  button:   <rect x="3" y="8" width="18" height="8" rx="4"/>,
  map:      <path d="M9 4l-6 2v14l6-2 6 2 6-2V4l-6 2z M9 4v14M15 6v14"/>,
  hero:     <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 12h18M8 8h6"/></>,
  spacer:   <path d="M5 6h14M5 18h14M12 9v6"/>,
  floatpanel:<><rect x="3" y="4" width="14" height="14" rx="2"/><rect x="9" y="10" width="12" height="10" rx="2"/></>,
  panel:    <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></>,
  content:  <path d="M5 7h14M5 12h14M5 17h9"/>,
  video:    <><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M10 9l5 3-5 3z" fill="currentColor" stroke="none"/></>,
  slider:   <><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 6v12M17 6v12"/></>,
  overlay:  <><rect x="4" y="4" width="14" height="14" rx="2"/><rect x="8" y="8" width="12" height="12" rx="2"/></>,
  heading:  <path d="M6 5v14M18 5v14M6 12h12"/>,
  divider:  <path d="M3 12h18"/>,
  icon:     <path d="M12 3l2.7 5.5 6 .9-4.4 4.3 1 6L12 17l-5.4 2.8 1-6L3.3 9.4l6-.9z"/>,
  cols:     <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16M15 4v16"/></>,
  fragment: <path d="M9 4H5a2 2 0 00-2 2v4M9 20H5a2 2 0 01-2-2v-4M15 4h4a2 2 0 012 2v4M15 20h4a2 2 0 002-2v-4"/>,
  grid:     <><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></>,
  colsInner:<><rect x="3" y="4" width="18" height="16" rx="2"/><rect x="6" y="7" width="5" height="10" rx="1"/><rect x="13" y="7" width="5" height="10" rx="1"/></>,
  shape:    <path d="M3 16c3-4 6-4 9 0s6 4 9 0M3 8h18"/>,
  template: <><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/><path d="M14 14l2 2 4-4"/></>,
  alert:    <><path d="M12 4l9 16H3z"/><path d="M12 11v4M12 18h.01"/></>,
  code:     <path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/>,
  list:     <path d="M4 6h.01M9 6h11M4 12h.01M9 12h11M4 18h.01M9 18h11"/>,
  table:    <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M3 16h18M11 4v16"/></>,
  listDesc: <path d="M4 6h6M14 6h6M4 12h6M14 12h6M4 18h6M14 18h6"/>,
  quote:    <path d="M7 7h4v4a4 4 0 01-4 4M15 7h4v4a4 4 0 01-4 4"/>,
  codeTag:  <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 10l-2 2 2 2M15 10l2 2-2 2"/></>,
  listIcon: <><circle cx="5" cy="6" r="1.5"/><circle cx="5" cy="12" r="1.5"/><circle cx="5" cy="18" r="1.5"/><path d="M10 6h10M10 12h10M10 18h10"/></>,
  headingAnim:<path d="M6 5v14M18 5v14M6 12h12M3 21l3-2 3 2"/>,
  textPath: <path d="M3 17c5-10 13-10 18 0M8 13l-1 4M16 13l1 4"/>,
  gallery:  <><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></>,
  carousel: <><rect x="6" y="6" width="12" height="12" rx="2"/><path d="M3 9v6M21 9v6"/></>,
  lightbox: <><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6v6H9z"/></>,
  audio:    <path d="M9 8a3 3 0 016 0v8a3 3 0 01-6 0V8z M5 11v2M19 11v2"/>,
  youtube:  <><rect x="3" y="6" width="18" height="12" rx="3"/><path d="M11 9l4 3-4 3z" fill="currentColor" stroke="none"/></>,
  vimeo:    <path d="M4 8c2-2 6 0 7 4s4 6 6 4 4-10-3-10"/>,
  sphere:   <><circle cx="12" cy="12" r="9"/><path d="M3 12c5-3 13-3 18 0M3 12c5 3 13 3 18 0M12 3c-3 5-3 13 0 18M12 3c3 5 3 13 0 18"/></>,
  videoBg:  <><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="12" cy="12" r="3"/></>,
  cta:      <><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M14 12l3-2v4z" fill="currentColor" stroke="none"/></>,
  price:    <><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10h8M9 14h6M10 7v10"/></>,
  testimonial:<><circle cx="9" cy="10" r="3"/><path d="M3 20c1-3 4-5 6-5s5 2 6 5M16 4l4 4-4 4"/></>,
  timer:    <><circle cx="12" cy="13" r="7"/><path d="M9 3h6M12 9v4l2 1"/></>,
  team:     <><circle cx="9" cy="9" r="3"/><circle cx="17" cy="10" r="2.5"/><path d="M3 19c1-3 4-4 6-4s5 1 6 4M14 18c1-2 3-3 4-3s3 1 4 3"/></>,
  mail:     <><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></>,
  stats:    <path d="M5 19V11M11 19V5M17 19v-7M3 19h18"/>,
  accordion:<><rect x="3" y="4" width="18" height="5" rx="1"/><rect x="3" y="11" width="18" height="9" rx="1"/></>,
  tabs:     <><path d="M3 9h6V4h6v5h6"/><path d="M3 9v11h18V9"/></>,
  tooltip:  <><rect x="3" y="4" width="18" height="11" rx="2"/><path d="M11 15l1 3 2-3"/></>,
  modal:    <><rect x="2" y="4" width="20" height="16" rx="2" opacity=".4"/><rect x="6" y="8" width="12" height="9" rx="1.5"/></>,
  flip:     <><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 12c4-4 12-4 16 0"/></>,
  reveal:   <path d="M3 12c3-5 7-7 9-7s6 2 9 7c-3 5-7 7-9 7s-6-2-9-7z M9 12a3 3 0 106 0 3 3 0 00-6 0"/>,
  input:    <><rect x="3" y="9" width="18" height="6" rx="2"/><path d="M7 12h2"/></>,
  select:   <><rect x="3" y="9" width="18" height="6" rx="2"/><path d="M16 11l2 2 2-2"/></>,
  check:    <><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 12l3 3 5-6"/></>,
  radio:    <><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3" fill="currentColor" stroke="none"/></>,
  textarea: <><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h7"/></>,
  // ui
  search:   <><circle cx="11" cy="11" r="6"/><path d="M20 20l-4-4"/></>,
  sliders:  <path d="M4 7h7M14 7h6M14 7a2 2 0 100 4 2 2 0 000-4M4 17h2M9 17h11M6 17a2 2 0 100 4 2 2 0 000-4"/>,
  chev:     <path d="M9 6l6 6-6 6"/>,
  chevDown: <path d="M6 9l6 6 6-6"/>,
  plus:     <path d="M12 5v14M5 12h14"/>,
  pin:      <path d="M12 3v6l-4 4h8l-4-4M12 13v8"/>,
  drag:     <><circle cx="9" cy="6" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="18" r="1"/></>,
  cmd:      <path d="M9 6V4a2 2 0 10-2 2h2zM9 6h6V4a2 2 0 112 2h-2zM9 6v6m6-6v6M9 12h6M9 12v2a2 2 0 11-2-2h2zm6 0h2a2 2 0 11-2 2v-2z"/>,
  arrow:    <path d="M5 12h14M13 6l6 6-6 6"/>,
  device:   <><rect x="6" y="3" width="12" height="18" rx="2"/><path d="M11 18h2"/></>,
  desktop:  <><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M9 20h6M12 16v4"/></>,
  tablet:   <><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M11 18h2"/></>,
  undo:     <path d="M9 14l-4-4 4-4 M5 10h9a5 5 0 010 10h-2"/>,
  eye:      <path d="M3 12c3-5 7-7 9-7s6 2 9 7c-3 5-7 7-9 7s-6-2-9-7z M9 12a3 3 0 106 0 3 3 0 00-6 0"/>,
  layers:   <path d="M12 3l9 5-9 5-9-5z M3 12l9 5 9-5 M3 17l9 5 9-5"/>,
  zap:      <path d="M13 3L4 14h7l-1 7 9-11h-7z"/>,
  history:  <path d="M3 12a9 9 0 109-9 9 9 0 00-7 4M3 4v4h4M12 7v5l3 2"/>,
};

window.OLOIcon = function OLOIcon({ name, size = 16, color, style, className }) {
  const node = ICONS[name] || ICONS.square;
  return (
    <svg
      width={size} height={size} viewBox="0 0 24 24"
      fill="none" stroke={color || "currentColor"} strokeWidth="1.6"
      strokeLinecap="round" strokeLinejoin="round"
      style={style} className={className}
    >{node}</svg>
  );
};
