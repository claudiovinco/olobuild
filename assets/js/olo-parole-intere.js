/*
 * Parole intere (13 nov 2026; dal 18 nov anche le parole che escono dalla colonna senza andare a capo). Una parola
 * più larga della sua colonna non va più a capo a metà («Faccia|mo»,
 * «Contrabbandie|re»): il testo che la contiene si rimpicciolisce quanto basta perché ci stia. Era successo sul
 * telefono nei titoli dell'apertura (Cromo, Casa Scogliera), nelle liste (Pista Nera, Sartoria Ferro), nelle schede
 * strette (Solco) e nei titoli dei progetti (Studio Assi): il corpo scelto per il computer non ci stava a 390 px.
 *
 * - Solo testi da 18 px in su (titoli, voci, numeri grandi): il testo corrente va a capo fra le parole da sé.
 * - Solo rimpicciolire, al massimo fino al 55%; al cambio di larghezza della finestra si riparte dal corpo originale.
 * - Le parole col trattino (o il trattino morbido) si possono spezzare lì: non contano.
 * - Le tile che compaiono scorrendo (pigre) si controllano quando la pagina si ferma.
 */
(function () {
  'use strict';
  if (!window.NodeFilter || !document.createRange) return;
  var MIN_PX = 18, MINIMO = 0.55, PASSO = 0.93;
  var PAROLA = /[A-Za-zÀ-ɏ’']{4,}/g;
  var range = document.createRange();
  var visti = new WeakMap(); // genitore → larghezza della finestra a cui è stato controllato
  var ridotti = [];          // [elemento, font-size in linea di prima, priorità di prima]
  var larghezza = window.innerWidth;

  function spezzata(n, a, b) {
    range.setStart(n, a);
    range.setEnd(n, b);
    var q = range.getClientRects(), top = null;
    for (var i = 0; i < q.length; i++) {
      if (q[i].width < 0.5 || q[i].height < 0.5) continue;
      if (top === null) top = q[i].top;
      else if (Math.abs(q[i].top - top) > 3) return true;
    }
    return false;
  }

  function haSpezzate(radice) {
    var w = document.createTreeWalker(radice, NodeFilter.SHOW_TEXT), n;
    while ((n = w.nextNode())) {
      var t = n.textContent, m;
      if (t.length < 4) continue;
      PAROLA.lastIndex = 0;
      while ((m = PAROLA.exec(t))) {
        var prima = t.charAt(m.index - 1), dopo = t.charAt(m.index + m[0].length);
        if (prima === '-' || dopo === '-' || prima === '­' || dopo === '­') continue;
        if (spezzata(n, m.index, m.index + m[0].length)) return true;
      }
    }
    return false;
  }

  // Una parola più larga dello spazio che ha, anche senza andare a capo: esce dal suo blocco, oppure il blocco si è
  // allargato per lei oltre la tile (una casella di griglia o di flex è larga almeno quanto la sua parola più lunga).
  // 18 nov 2026: «Ti richiamiamo» di Capriola, 72 px in Unbounded in una colonna del 40%, copriva il modulo accanto.
  // La misura è quella di impaginazione (divisa per la scala di eventuali trasformazioni), non quella disegnata.
  // Fuori i livelli dello Slider Pro (tela in scala, posizioni volute) e i testi su una riga sola (nastri, ticker).
  function largo(el) {
    var cs = getComputedStyle(el);
    return el.clientWidth - (parseFloat(cs.paddingLeft) || 0) - (parseFloat(cs.paddingRight) || 0);
  }

  function sborda(b) {
    if (b.closest('.mps-layers-wrap')) return false;
    var ws = getComputedStyle(b).whiteSpace;
    if (ws === 'nowrap' || ws === 'pre') return false;
    var spazio = largo(b), tile = b.closest('.olo-frontend-tile');
    if (tile && tile !== b) spazio = Math.min(spazio, largo(tile));
    if (!(spazio > 40)) return false;
    var scala = b.offsetWidth ? b.getBoundingClientRect().width / b.offsetWidth : 1;
    if (!(scala > 0)) scala = 1;
    var w = document.createTreeWalker(b, NodeFilter.SHOW_TEXT), n;
    while ((n = w.nextNode())) {
      var t = n.textContent, m;
      if (t.length < 4) continue;
      PAROLA.lastIndex = 0;
      while ((m = PAROLA.exec(t))) {
        range.setStart(n, m.index);
        range.setEnd(n, m.index + m[0].length);
        if (range.getBoundingClientRect().width / scala > spazio + 1) return true;
      }
    }
    return false;
  }

  function storta(b) {
    return haSpezzate(b) || sborda(b);
  }

  // il blocco che contiene la riga: si rimpicciolisce lui (con la parola in corsivo o in grassetto dentro)
  function blocco(el) {
    while (el && el !== document.body && getComputedStyle(el).display === 'inline') el = el.parentElement;
    return el && el !== document.body ? el : null;
  }

  function aggiusta(b) {
    var cs = getComputedStyle(b), base = parseFloat(cs.fontSize);
    if (!(base >= MIN_PX) || !storta(b)) return;
    ridotti.push([b, b.style.getPropertyValue('font-size'), b.style.getPropertyPriority('font-size')]);
    var f = 1;
    while (f * PASSO >= MINIMO) {
      f *= PASSO;
      b.style.setProperty('font-size', (base * f).toFixed(2) + 'px', 'important');
      if (!storta(b)) break;
    }
  }

  function controlla() {
    var w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
      acceptNode: function (n) {
        var p = n.parentElement;
        if (!p || n.textContent.length < 4 || visti.get(p) === larghezza) return NodeFilter.FILTER_REJECT;
        return NodeFilter.FILTER_ACCEPT;
      }
    }), n, blocchi = [];
    while ((n = w.nextNode())) {
      var p = n.parentElement;
      // Livelli dello Slider Pro: finché il suo script non ha messo la tela alla larghezza di progetto (stile in linea
      // «width») i titoli grandi non ci stanno e vanno a capo a metà; misurati lì finivano tutti al 55% e restavano
      // così (17 nov 2026: l'apertura di Scroscio al telefono con testi da 7 px). Si riprova al controllo dopo.
      var tela = p.closest('.mps-layers-wrap');
      if (tela && !tela.style.width) continue;
      visti.set(p, larghezza);
      if (p.closest('script,style,noscript,svg,textarea,input,select,[contenteditable="true"]')) continue;
      if (!(parseFloat(getComputedStyle(p).fontSize) >= MIN_PX)) continue;
      var b = blocco(p);
      if (b && blocchi.indexOf(b) < 0) blocchi.push(b);
    }
    blocchi.forEach(aggiusta);
  }

  function daCapo() {
    if (window.innerWidth === larghezza) return;
    larghezza = window.innerWidth;
    ridotti.forEach(function (r) {
      if (r[1]) r[0].style.setProperty('font-size', r[1], r[2]);
      else r[0].style.removeProperty('font-size');
    });
    ridotti = [];
    controlla();
  }

  var attesa = 0;
  function dopo(fn, ms) {
    clearTimeout(attesa);
    attesa = setTimeout(fn, ms);
  }

  // Quando arrivano i caratteri veri (e alla fine del caricamento) si rimisura TUTTO: un titolo controllato col carattere
  // di riserva, più stretto, era segnato come visto e restava largo dopo, fuori dalla colonna (18 nov 2026: Bialbero,
  // Controtempo, Ring a 100 px). Si riparte dalla misura attuale: i testi già ridotti restano ridotti.
  function riprova() {
    visti = new WeakMap();
    controlla();
  }
  controlla();
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(riprova);
  window.addEventListener('load', function () { riprova(); setTimeout(riprova, 2500); });
  window.addEventListener('resize', function () { dopo(daCapo, 250); }, { passive: true });
  window.addEventListener('scroll', function () { dopo(controlla, 500); }, { passive: true });
})();
