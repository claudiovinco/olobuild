/*
 * Controllo dei negozi dei temi (24 nov 2026): cerca gli elementi rimasti con la grafica del browser o di WooCommerce.
 *
 * Uso: aprire una pagina qualsiasi del sottosito (https://olotheme.com/olobuild/…), incollare questo file nella console
 * del browser (o eseguirlo con lo strumento JavaScript del browser) e poi:
 *     await controllaNegozi(['spiedo', 'ristretto'])      // alcuni temi
 *     await controllaNegozi()                             // tutti quelli di MAPPA (circa 10 s a tema)
 * Per ogni tema carica in una cornice nascosta (1316 × 900) negozio, una scheda prodotto, carrello, cassa e account,
 * con il mini carrello aperto, e riporta: pulsanti col fondo grigio o il carattere del browser, link che dovrebbero
 * essere pulsanti, campi senza padding, avvisi coi colori di WooCommerce, foto enormi nel mini carrello, paginazione
 * senza grafica, pagine non trovate. Per avere carrello e cassa pieni, prima aggiungere un prodotto (?add-to-cart=ID).
 * MAPPA: tema → [pagina del negozio, carrello, cassa, account] (slug di theme.json → pages, dalle chiavi di woo).
 * Con strumenti che fermano lo script dopo 45 s: lanciarlo senza await, salvando in window.__risultati, e rileggerlo.
 */
const MAPPA = {
  calice: ['cantina', 'carrello', 'cassa', 'il-mio-account'], carambola: ['prenota', 'carrello', 'cassa', 'il-mio-account'],
  crocevia: ['negozio', 'carrello', 'cassa', 'il-mio-account'], davanzale: ['oggetti', 'carrello', 'cassa', 'il-mio-account'],
  falda: ['negozio', 'carrello', 'cassa', 'il-mio-account'], fulmine: ['negozio', 'carrello', 'cassa', 'il-mio-account'],
  madia: ['dispensa', 'carrello', 'cassa', 'il-mio-account'], orlo: ['collezione', 'carrello', 'cassa', 'il-mio-account'],
  pennino: ['negozio', 'carrello', 'cassa', 'il-mio-account'], ristretto: ['caffe', 'carrello', 'cassa', 'il-mio-account'],
  siero: ['negozio', 'carrello', 'cassa', 'il-mio-account'], spiedo: ['menu', 'carrello', 'cassa', 'il-mio-account'],
  suola: ['scarpe', 'carrello', 'cassa', 'il-mio-account'], tacet: ['opere', 'carrello', 'cassa'],
  tracolla: ['borse', 'carrello', 'cassa', 'il-mio-account'], trottola: ['negozio', 'carrello', 'cassa', 'il-mio-account'],
  velluto: ['collezioni', 'carrello', 'cassa', 'il-mio-account'],
};

function controllaPagina(w) {
  const d = w.document;
  d.querySelectorAll('.olo-mc-dropdown').forEach(x => { x.style.display = 'block'; });
  const corpo = w.getComputedStyle(d.body).fontFamily;
  const vis = el => { const r = el.getBoundingClientRect(); const cs = w.getComputedStyle(el); return r.width > 0 && r.height > 0 && cs.visibility !== 'hidden' && cs.display !== 'none'; };
  const testo = el => (el.value || el.textContent || el.getAttribute('aria-label') || el.placeholder || '').trim().replace(/\s+/g, ' ').slice(0, 34);
  const out = [];
  d.querySelectorAll('button, input[type=submit], input[type=button], a.button, .button, .wc-forward').forEach(el => {
    if (!vis(el)) return;
    const cs = w.getComputedStyle(el); const f = [];
    if (/^rgb\(2(39|40), 2(39|40), 2(39|40)\)$/.test(cs.backgroundColor)) f.push('fondo grigio del browser');
    if (cs.borderTopStyle === 'outset') f.push('bordo outset');
    if (cs.fontFamily !== corpo && /^(Arial|system-ui|sans-serif|"?Segoe UI"?)/.test(cs.fontFamily)) f.push('carattere del browser');
    if (el.tagName === 'A' && parseFloat(cs.paddingLeft) < 4 && cs.backgroundColor === 'rgba(0, 0, 0, 0)' && cs.borderTopStyle === 'none') f.push('link senza forma di pulsante');
    if (f.length) out.push('PULSANTE «' + testo(el) + '»: ' + f.join(', '));
  });
  d.querySelectorAll('input[type=text],input[type=email],input[type=tel],input[type=password],input[type=number],input[type=search],select,textarea').forEach(el => {
    // Il paese unico della cassa (select.country_to_state--single) WooCommerce lo mostra come testo: non è un campo.
    if (!vis(el) || el.offsetWidth < 20 || el.classList.contains('country_to_state--single')) return;
    const cs = w.getComputedStyle(el); const f = [];
    if (cs.borderTopStyle === 'inset') f.push('bordo inset');
    if (parseFloat(cs.paddingLeft) < 3) f.push('senza padding');
    if (cs.fontFamily !== corpo && /^(Arial|monospace|system-ui)/.test(cs.fontFamily)) f.push('carattere del browser');
    if (f.length) out.push('CAMPO «' + testo(el) + '»: ' + f.join(', '));
  });
  // Fondi degli avvisi di WooCommerce (blocchi: info, successo, errore; classici)
  const base = ['rgb(244, 248, 255)', 'rgb(244, 255, 247)', 'rgb(255, 240, 240)', 'rgb(246, 245, 248)', 'rgb(255, 248, 225)'];
  d.querySelectorAll('.woocommerce-info,.woocommerce-message,.woocommerce-error,.wc-block-components-notice-banner').forEach(el => {
    if (vis(el) && base.includes(w.getComputedStyle(el).backgroundColor)) out.push('AVVISO coi colori di WooCommerce «' + testo(el) + '»');
  });
  d.querySelectorAll('.woocommerce-mini-cart img').forEach(el => { const l = el.getBoundingClientRect().width; if (l > 120) out.push('FOTO del mini carrello larga ' + Math.round(l) + ' px'); });
  d.querySelectorAll('.page-numbers').forEach(el => { if (vis(el) && w.getComputedStyle(el).borderTopStyle === 'none' && !el.classList.contains('dots')) out.push('PAGINAZIONE senza grafica «' + testo(el) + '»'); });
  if (/non trovata|not found/i.test(d.title)) out.push('PAGINA NON TROVATA');
  return out;
}

// La cornice sta DENTRO lo schermo, invisibile: fuori (left: -20000px) le tile che si caricano quando entrano in vista
// (IntersectionObserver guarda lo schermo della pagina che la contiene) non partivano, e griglie e link mancavano.
function caricaInCornice(url) {
  return new Promise(res => {
    const f = document.createElement('iframe');
    f.style.cssText = 'position:fixed;left:0;top:0;width:1316px;height:900px;border:0;opacity:0.01;pointer-events:none;z-index:-1';
    let fatto = false;
    const fine = () => {
      if (fatto) return;
      fatto = true;
      setTimeout(() => { try { f.contentWindow.scrollTo(0, 400); } catch (e) { /* altra origine */ } setTimeout(() => res(f), 1200); }, 1800);
    };
    f.onload = fine; f.src = url; document.body.appendChild(f); setTimeout(fine, 15000);
  });
}

async function controllaNegozi(temi) {
  const out = {};
  for (const t of (temi || Object.keys(MAPPA))) {
    for (const [i, slug] of MAPPA[t].entries()) {
      const f = await caricaInCornice('/olobuild/demo/' + t + '/' + slug + '/');
      try {
        const r = controllaPagina(f.contentWindow);
        out[t + '/' + slug] = r.length ? r : 'ok';
        if (i === 0) { // la scheda del primo prodotto della griglia
          const a = [...f.contentDocument.querySelectorAll('.olo-woo-card a[href]')].find(x => !/add-to-cart|^#/.test(x.getAttribute('href')));
          if (a) { const f2 = await caricaInCornice(a.getAttribute('href')); const r2 = controllaPagina(f2.contentWindow); out[t + '/prodotto'] = r2.length ? r2 : 'ok'; f2.remove(); }
          else out[t + '/prodotto'] = ['nessun prodotto nella griglia'];
        }
      } catch (e) { out[t + '/' + slug] = ['ERRORE ' + e.message]; }
      f.remove();
    }
  }
  return out;
}
