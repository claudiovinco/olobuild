// Segnaposto delle tile appena inserite dalla palette (createTileFromType in useDragDrop.js).
//
// v3.55.36 — i field immagine vuoti ricevono un'immagine grigia e i testi lunghi un Lorem ipsum,
// per dare contesto visivo senza dover scrivere o caricare nulla prima di vedere la tile.
//
// 1.4.511 — il Lorem ipsum va SOLO nei testi lunghi da leggere (introduzione, sottotitolo,
// descrizione…). Prima andava in ogni campo `text` vuoto non escluso da un elenco, cioè anche in
// 124 campi a una riga che non sono testi da leggere: ID, chiavi API, tassonomie, prefissi, posizioni
// in px, la «provenienza» del Popup (che dalla 1.4.508 agisce: il popup appena inserito non compariva
// a nessuno), il «Prefisso» del Counter Circle (il cerchio mostrava il Lorem al posto del numero).
// Un campo a una riga è corto per natura o tecnico, e vuoto vuol dire «parte spenta»: dove serve un
// testo di partenza lo danno i default della tile.

export const PLACEHOLDER_LOREM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

// Rettangolo grigio 800×450 PNG — file statico servito dal plugin.
// PNG (non SVG) perché molti server WP hanno upload SVG disabilitato per sicurezza
// e alcuni filtri sanitize_url/wp_check_filetype potrebbero rifiutare data: URI o
// SVG inline. Il PNG è il formato universale più sicuro.
export function placeholderImageUrl() {
  const base = (typeof window !== 'undefined' && window.oloData && window.oloData.pluginUrl)
    ? window.oloData.pluginUrl
    : '/wp-content/plugins/olobuild/';
  return base.replace(/\/$/, '') + '/assets/img/placeholder-image.png';
}

// Testi lunghi: gli unici che possono ricevere il Lorem ipsum.
const LONG_TEXT_TYPES = new Set(['textarea', 'rich-text', 'wysiwyg']);

// …e solo se il nome dice che è un testo da leggere.
const READABLE_LONG_RE = /(^|_)(intro|introduction|subtitle|subhead|description|desc|body|content|bio|excerpt|lead|summary|message|quote|answer|note|text)$/i;

// Anche con un nome da testo, questi sono valori tecnici o testi che solo il proprietario può
// scrivere: codice, elenchi di valori uno per riga, mappe di campi, il testo della privacy.
const TECHNICAL_TEXT_RE = /(svg|code|html|css|json|script|map$|merge|fields?$|labels$|items$|_ids?$|ids$|api|key|guid|list|terms|privacy|legal|consent|shortcode|embed)/i;

// Field text che NON ricevono Lorem ipsum anche quando sono lunghi (URL, ID, target, alt-text,
// didascalia, link, codice, numeri, date…). Storico dalla v1.0.58, resta come rete di sicurezza.
const TEXT_PROTECTED_RE = /(_url$|^url$|_href|^href$|_id$|_target|^target$|_class|email|phone|^slug$|^icon|_icon|font_family|font_weight|color|align|width|height|size|radius|padding|margin|border|shadow|effect|preset|opacity|^tag|layout|columns|gap|speed|duration|delay|enabled|visible|show|hide|type$|kind|mode|style$|^alt$|alt_text|caption|^link|_link|^heading$|^title$|^name$|^label$|^cta_text$|^button_text$|_time$|^time$|_seconds$|_overlay_text$|^overlay_text$|^overlay$|_overlay$|^code$|_code$|^html$|_html$|^css$|_css$|^js$|_js$|^expression$|^script$|_format$|count|^index$|^value$|^min$|^max$|^step$|font_size|placeholder|^search$|_search$|_from$|_to$|_after$|_before$|^path$|_path$|custom_path|^date$|_date$|target_date|_message$|expired_message)/i;

// Field image SECONDARI (hover, fallback, alt) che restano vuoti — il placeholder va
// solo sul campo principale, non su quelli "opzionali". Senza questo: trascini un'immagine
// e vedi il placeholder al passaggio del mouse invece che nello stato base.
// Anche il POSTER di un video (poster_image, video_poster) è secondario: la miniatura la dà il video
// (YouTube, Vimeo) e un riquadro grigio lì la copriva appena si incollava l'URL (1.4.535).
// E nemmeno un LOGO: un riquadro grigio al posto del logo è peggio del ripiego della tile (il nome del
// sito, il logo della Personalizzazione). Un campo immagine può rinunciare al segnaposto anche da sé
// con `segnaposto: false` (le varianti del Logo del sito: retina, versione chiara).
const IMAGE_SECONDARY_RE = /(hover|secondary|alternate|fallback|backup|poster|logo|brand|^alt_image|_alt$)/i;

/** Il campo (lungo) riceve il Lorem ipsum quando è vuoto? */
export function riceveLorem(field) {
  if (!field || !field.key || !LONG_TEXT_TYPES.has(field.type)) return false;
  return READABLE_LONG_RE.test(field.key) && !TECHNICAL_TEXT_RE.test(field.key) && !TEXT_PROTECTED_RE.test(field.key);
}

/**
 * Applica i segnaposto ai field vuoti del Contenuto di una tile appena creata.
 * - testo lungo da leggere (riceveLorem) → Lorem ipsum
 * - immagine PRINCIPALE vuota → PNG segnaposto grigio (non hover_image, alt_image, ecc.)
 * I default già configurati dalla tile restano intatti (es. cta_text = 'Inizia ora').
 */
export function applyContentPlaceholders(settings, fields) {
  if (!Array.isArray(fields) || !settings) return;
  const immagine = placeholderImageUrl();
  for (const f of fields) {
    if (!f || !f.key || !f.type) continue;
    const cur = settings[f.key];
    // Voci d'esempio di un ripetitore che senza immagine non si vedono (`segnapostoVoci: true`
    // sul campo, es. le slide del carousel, che il renderer salta): l'immagine PRINCIPALE vuota
    // prende il segnaposto. Solo su richiesta: in accordion, timeline, listini… l'immagine
    // della voce è facoltativa e un riquadro grigio in ogni voce sarebbe sbagliato.
    if (Array.isArray(cur) && Array.isArray(f.itemFields)) {
      if (!f.segnapostoVoci) continue;
      const img = f.itemFields.find((x) => x && x.type === 'image' && x.key && !IMAGE_SECONDARY_RE.test(x.key));
      if (img) {
        for (const voce of cur) {
          if (voce && typeof voce === 'object' && (voce[img.key] === '' || voce[img.key] == null)) voce[img.key] = immagine;
        }
      }
      continue;
    }
    const isEmpty = cur === '' || cur === undefined || cur === null;
    if (!isEmpty) continue;
    if (riceveLorem(f)) {
      settings[f.key] = PLACEHOLDER_LOREM;
    } else if (f.type === 'image' && f.segnaposto !== false && !IMAGE_SECONDARY_RE.test(f.key)) {
      settings[f.key] = immagine;
    }
  }
}
