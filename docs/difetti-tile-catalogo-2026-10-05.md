# Difetti delle tile trovati col catalogo (5 ottobre 2026)

Provando le 192 tile della palette nel catalogo di mosaic (https://mosaic.clod.eu/catalogo-tile/, 1023 esempi)
sono emersi i difetti qui sotto. **Nessuno è stato corretto**: è un elenco di lavoro. Le righe di codice sono
quelle della 1.4.515; dove non c'è «verificato» il difetto viene dalla lettura del codice.

Gravità: **G** = rotto o illeggibile, si vede subito · **M** = un controllo che non fa niente o una resa sbagliata
in un caso comune · **L** = dettaglio, colore fuori tema, testo in inglese.

---

## A. Difetti trasversali (toccano molte tile: valgono di più)

| # | Gravità | Cosa | Dove | Tile colpite |
|---|---|---|---|---|
| A1 | ✅ 1.4.516 | I **motivi SVG** (waves, triangles, diamonds, hexagons, zigzag, chevrons, herringbone, scales, circles, concentric-circles, brick, stars, crosses) con un **colore del tema** diventano una `mask-image` sull'elemento INTERO: in una sezione o in un contenitore il contenuto si vede solo dentro la sagoma del motivo. Andrebbe disegnato su un livello (pseudo-elemento), non sull'elemento | `class-css-builder.php:880-904` | ogni sezione, contenitore e campo sfondo; verificato con la scena «onde» su icone, titoli, immagini, prodotti |
| A2 | ✅ 1.4.517 | **Caricamento differito**: le tile sotto la piega nascono da un `<template>` e l'idratazione non lancia nessun evento; gli script che partono solo al `DOMContentLoaded` non le vedono. L'elenco `$no_lazy` è incompleto e il contatore delle tile «subito visibili» conta anche l'header | `class-frontend-renderer.php:682-736`, `trait-olobuild-renderer-page.php:971-1022` | Pro Slider (livelli invisibili, niente navigazione), Post Grid (filtri e pagine), Viewer 360 («Caricamento…» per sempre), PDF Viewer, PDF Pro (riquadro vuoto), Facebook Page, X Timeline, Instagram |
| A3 | ✅ 1.4.518 (nuove scelte di preset; le tile già salvate non cambiano) | I **preset del menu Stile** non portano lo sfondo in pagina: scrivono `style.bg_color`, ma vince il default `settings.bg = {type:'none'}` | `class-frontend-renderer.php:955-956` + `class-css-builder.php:41` | text-block («Retro Terminal» verde su crema), form («Underline Dark» illeggibile), content, headline, carousel («Glass Frosted»), overlay e tutte le tile coi preset di base |
| A4 | ✅ 1.4.519 | **Icone Lucide vuote**: il selettore offre ~1700 icone Lucide ma molte tile stampano solo `uk-icon` (131 icone UIkit) | iconlist:107, desclist:258, alert:101, accordion:472, popup:232, flipcard:464, counter:167, form, progresstracker, nav, pdfpro, divider:118 | 13+ tile |
| A5 | ✅ 1.4.520 | **Bordo fantasma**: la regola CSS del Bordo punta a `.{uid}` ma nel markup l'uid è un `id` (o un'altra classe) → il controllo Bordo non fa niente | social:167, sharebuttons:183, linkinbio:186, countercircle:93/217, textmask:257, blendtext, code, slideshow:185, proslider:384, osmmap, viewer360, shatteredimage:657, lightbox:147, soundcloud:75, myaccount:106, comparison:151, nav, subnav, livesearch, navmenu, progress | ~20 tile; in pdfviewer:125 e pdfpro:135 un `border:0px` inline vince sulla regola |
| A6 | ✅ 1.4.521 | **Colori del tema trattati come esadecimali**: si attacca un suffisso alfa (`var(--…)40` → CSS non valido) o si converte con `hexdec` | divider:144 (ombra), button:326 (bagliore), progresstracker:97 (pulse), pricing:332, info-cards:201/231/252, lookbookmixer:98, animatedheading:75, mappa (cluster), workgrid:83; `color_to_rgb()` → grigio 128 (`class-tile-base.php:307`, effetti «wow»); `hex_to_rgba` del neon → rosso brand fisso (`class-tile-base.php:726`); progallery:290/582; showcasegrid:62; textmask:136 | 15+ tile |
| A7 | ✅ 1.4.522 | **Token che la Palette non genera** (`dark`/`light` sul tema Contour, `surface`, `surface-alt`, `text-soft`, `text-faint`, `on-primary`, `error`, `heading`): ricadono su blu notte, grigi o quasi-bianco | preset e default di hero-split, linkinbio, testimonial:255, bottombar, themedemos, tile Woo (`class-woo-*.php`) | |
| A8 | ✅ 1.4.523-524 (tile vuote; restano 164 conflitti di valore PHP≠config in ~55 tile, da decidere) | **Default doppi** (PHP ≠ config): un array vuoto nel PHP scavalca le voci d'esempio del config → la tile appena inserita è vuota | stackscroll:69, scrollscrub:120, schedule:20, portfolio:81, northquoteslider:20; colore `#ffffff` di shapedivider:20 (banda bianca sopra la sezione dopo); chart, hoverlist, button (raggio 6 contro 10), accordion, lookbookmixer (6 voci contro 10), hero-split, matchfixtures | |
| A9 | ✅ 1.4.525 | Il **bordo del contenitore** rifiuta `color-mix()` e ripiega su `#374151` (i campi colore delle tile lo accettano) | `trait-olobuild-renderer-css.php:26-36` | tutte |
| A10 | ✅ 1.4.526-528 | **Stili globali o UIkit che scavalcano la tile** | `.olo-template input[type=…]` (`class-style-system.php:1784`) sui campi di form e loginform · `em{color:#f0506e}` di UIkit: ogni corsivo esce rosa · `.olo-template .uk-button` sul pulsante del popup · `ins` giallo sui prezzi Woo · `<pre>` di UIkit (asciiviz:168, step-timeline:242) · pillola attiva blu UIkit nel filtro della Griglia, bordo blu nello switcher, «Info» blu nell'avviso | |
| A11 | ✅ 1.4.529 | Elementi **portati nel body** che perdono i token del template | popup (primario blu `#1e87f0`, testo `#333`, font di sistema) | la 1.4.514 l'ha fatto per altre tile, non per il popup |
| A12 | ✅ 1.4.530 | Il **velo** degli sfondi fotografici INTERNI alle tile non si disegna (`get_bg_inline_css` non lo emette) | `class-css-builder.php:103-113` | cta-banner, info-cards, hero-split (vetrina), iconbox, «Sfondo box/card» di varie tile |
| A13 | ✅ 1.4.531 | **Colonne per dispositivo** offerte nell'inspector ma il PHP legge solo il desktop | workgrid:60, statstrip, process-steps, hoursstrip, showcasegrid (breakpoint fisso 880 px), beforeafter | |
| A14 | ✅ 1.4.532 | **Rotte REST registrate solo durante il render** → 404 alle chiamate AJAX | woo_quickview:58→447, woo_wishlist:384, woo_minicart:152 (frammenti) | Quick View e Wishlist non funzionano |
| A15 | ✅ 1.4.533 (scritte fisse ed emoji; i contenuti d'esempio in inglese nella revisione dei default) | **Testi di default in inglese o emoji** | process-steps, matchfixtures, workgrid, worklist, showcasegrid, categoryrail, beforeafter, filmreel, schedule (Mon, Tue…), lookbookmixer, announcementbar, code («Copy»), hoverlist («● STILL»), navmenu e megamenu («Select a menu in the Inspector panel»), productgrid sorgente Woo («SALE»); emoji: trust-strip 🇮🇹, oloxlessons (lucchetto), newsletter (opzione «Emoji»), finder e floatingpanel (opzioni) | |
| A16 | ✅ 1.4.534 | **Preset con valori inesistenti o colori fuori tema** (neon, giallo brutalista, `#6366F1`, `#e8622a`) | `tilePresets.js` (BASE_THEME_PRESETS e preset per tile: social `icon-label`, search `rounded`, progallery `frame:'border'`, button `hover_effect:'scale'`, image `filter_saturation`, newsletter `icon_type:'icon'`, lightbox `animation:'zoom'`) | |

---

## B. Difetti per tile

### Essenziale
- **icon** — G ✅ 1.4.536: padding scritto `16pxpx` (viste «Con sfondo» e «Cornice» senza padding) `:70, :91-93` · M: con un link il colore è quello dei link del tema `:135`.
- **video** — G ✅ 1.4.537: bordo e raggio anche attorno alla didascalia (classe condivisa) `:142, :480` · M: proporzioni solo 16:9/4:3/1:1/cover (il preset «Cinema Wide» chiede un 21:9 che non c'è) `:458`; testo sovrapposto sempre al centro; opzioni del play inerti con un file · L: `#1F2937` e bianco fissi.
- **image** — G ✅ 1.4.538: con proporzione e raggio la didascalia sparisce (`overflow:hidden`) · M: `uk-border-rounded` sempre sull'`<img>` (5 px anche con raggio 0) `:544`; colore e misura della didascalia fissi.
- **button** — M: con «Gradient animato» l'icona sparisce `:398`; glitch con magenta/ciano fissi `class-text-effects.php:144`.
- **divider** — M: due divisori a diamanti/puntini nella pagina condividono l'ID del motivo `:181, :189`; puntini e diamanti deformati (`preserveAspectRatio="none"`); «Stile tipografico» inerte.
- **text-block** — M: «Larghezza max» non centra il blocco `:130-132`; cursore della macchina da scrivere su una riga sua `class-text-effects.php:210`; «Reveal parola» e «Macchina da scrivere» trasformano i paragrafi in `<br>`.
- **content** — M: ombra personalizzata dell'immagine ignorata `:156`; `alt` sempre vuoto (legge `title`) `:560`; manca l'allineamento del testo; gli elenchi perdono i pallini (`frontend.css:3122` solo per `.olo-text-block`); secondo motore degli effetti testo con la stessa guardia `__oloTextFxInit` `:447`.
- **spacer** — M: «Secondo livello» spostato sotto la forma `:135, :153`; «Inverti direzione» della forma sotto inerte `:113`; riga chiara fra fascia e forme.

### Layout
- **cta-banner** — G ✅ 1.4.539: lo sfondo della tile (`bg`) è ridipinto anche sul contenitore ad angoli vivi: il raggio non si vede mai `class-frontend-renderer.php:955-956` · L: testo di default fuori luogo («una sigaretta a testa di pausa»).
- **info-cards** — G ✅ 1.4.541: una card con link ha due attributi `style` e perde fondo, bordo, padding e raggio `:193-195` · M: `title_color`, `counter_color`, `icon_color`, `icon_bg_color`, `counter_shape`, `counter_bg` letti dal PHP ma senza controllo; `media_position` mai letto; freccia come carattere di testo.
- **hoursstrip** — G ✅ 1.4.542: sul telefono restano 4 colonne schiacciate (regola mobile senza `!important`) `:103-108` · doppione di statstrip.
- **hero** — G ✅ 1.4.543: «Bordo inferiore ad arco» non taglia mai (soglia 0,87 contro 0,866) `:233`, quindi il preset «Masked Arch» non ha l'arco · M: esempio 1 con testo bianco su crema `:140`; pillole e campo della ricerca bianchi fissi `:319, :327`; famiglia del titolo `serif` dei preset = serif generico `:491`.
- **hero-split** — M: etichette delle statistiche grigie e filo scuro fissi (invisibili su scuro) `:309, :317`; lettore audio illeggibile col preset scuro `:340`; corsivo forzato solo se il valore è «Gratis» `:316`; colore del badge vetrina senza controllo `:107`.
- **section-header** — M: in colonna allineata a destra il sottotitolo non segue `:191-192`; colonna destra sempre allineata a destra `:213`; `headline_inline` senza controllo · L: seconda riga di default `#b3261e`.
- **step-timeline** — M: alone `#fff` fisso attorno ai pallini `:182`; il `<pre>` del terminale diventa un riquadro bianco `:242`; la scritta fra due step si sovrappone al numero successivo `:288`.
- **statstrip** — M: con più voci che colonne niente spazio fra le righe e divisore a inizio riga `:114`.
- **product-cards** — L: default arcobaleno in esadecimale e testi di marketing OLOtheme; manca un campo prezzo.
- **trust-strip** — L: niente misura delle icone; badge lime `#D8FF4A` di riserva.
- **inner-columns** — G ✅ 1.4.544: con le impostazioni di fabbrica le sotto-colonne non stanno mai affiancate (larghezze 100% + gap con `flex-wrap`) `trait-olobuild-renderer-structure.php:1918, :1991` · M: ignorati direzione/giustificazione Flex, «Impila su tablet», sfondi immagine e video, ombra del contenitore (`render_inner_columns_node` da :1899).
- **templateembed** — G ✅ 1.4.545: il template incluso si allarga a tutta la finestra (`.olo-template` in `assets/css/frontend.css:198-206`): in una colonna esce dai bordi, in un contenitore arrotondato viene tagliato.
- **shapedivider** — G ✅ 1.4.546: colore di fabbrica `#ffffff` (banda bianca sulla sezione dopo, z-index 1); in «Basso» senza «Specchia» la forma è capovolta; manca `left:0` a larghezza 100% (gradino a sinistra) `:99-100` · L: offre «Bordo».
- **row** — M: il «Gap» non è in px ma diventa una classe UIkit (16 ≈ 30-40 px) `:488`.
- **worklist** — M: «Indentazione hover» inerte (padding inline batte `:hover`) `:93, :125`.
- **workgrid** — M: strisce ed etichetta del segnaposto opache coi colori del tema `:83-84` · L: niente raggio né stile delle card.
- **lookbookmixer** — M: prezzi con la virgola diventano interi (`4,50` → `450`) `:93`; totale `€10` senza spazio, somma in virgola mobile `:127` · L: frecce come caratteri ‹ ›.
- **hoverlist** — M: di fabbrica il nome è chiaro su tema chiaro (invisibile); riserve `#16263d` e lime `#C6F24E` `:68, :127`.
- **schedule** — M: il titolo non ha colore (illeggibile su scuro) `:77`.
- **stackscroll** — L: «Rimpicciolisci» scala anche le card non impilate `:330`; padding per dispositivo con `absint` su un oggetto `:284`.
- **fragment** — da ritirare: disegna un div vuoto e fa ciò che fanno già le Avanzate e l'Ancora menu.
- **grid** — M: con «Testo su immagine» il raggio resta solo sugli angoli alti (gli angoli bassi della foto sono vivi); la pillola attiva del filtro è blu UIkit; etichetta «Tutti» di default «All».

### Testo
- **variablespecimen** — G ✅ 1.4.547: il font scelto non si applica mai (`font-family: Inter, inherit` scartato) `:127`; il filtro della riga 126 toglie le parentesi ai `var(--…)`.
- **textmask** — G ✅ 1.4.548: «Video dietro al testo» mostra il video intero e il testo resta invisibile `:149`; «Testo rivela il video» calcola la luminosità solo da esadecimali `:136`; senza video è un rettangolo nero alto 100vh.
- **textpath** — G ✅ 1.4.549-550: «Scorrimento una volta» finisce al 100% e il testo sparisce `:165`; «Continuo» lo fa uscire e rientrare `:161`; la spirale esce dal riquadro `:62`; un testo più lungo del tracciato viene troncato senza avviso.
- **list** — G: se tutte le voci sono «Numero» i numeri spariscono (`<ol class="uk-list">`) `:96`; preset con icone sconosciute; icona verde «successo» di default.
- **quotation** — M: niente controlli colore (illeggibile su scuro); la barra a sinistra di `frontend.css:350` resta anche centrata o a destra; 12 preset che cambiano due chiavi.
- **scrubtext (Manifesto)** — M: misura fluida fissa a 4,2vw `:123` (il massimo di 96 px arriva a 2280 px di schermo); niente allineamento.
- **animatedheading** — M: evidenziazione «Sfondo» solo con esadecimali `:75`; stile «Cerchio» senza CSS; in «Clip» la parola si alza.
- **blendtext** — M: font salvati tra apici rovinati da `esc_attr` `:61`.
- **desclist** — M: «Righe alternate» senza colore non disegna niente `:93`; in linea e a griglia le definizioni non stanno in colonna.
- **alert** — L: icona a sinistra col testo centrato; margine UIkit di 20 px sotto; non si arrotonda; il preset «Gradient Soft» scrive un gradiente in `background-color`.
- **table** — L: hover rosso fisso `rgba(225,71,79,…)` `:89`.
- **code** — L: niente evidenziazione della sintassi; raggio fisso.
- **html** — L: in sandbox l'iframe è alto 150 px fisso e non ha font e colori del tema.
- **list, desclist, iconlist** — L: gli effetti testo usano un selettore senza id, valido per tutte le liste della pagina.

### Media
- **progallery** — G: «Nastro automatico», «Nastro doppio» e Coverflow rotti: ai selettori si aggiunge la classe del preset invece dell'uid `:941, :1010, :1141, :1180, :1818`; «Sollevamento», shimmer e cornici mettono `position:relative` e fanno esplodere gli schemi assoluti (lo «Sparso» diventa alto 5000 px) `:574, :670, :702`.
- **proslider** — G: vedi A2 (sotto la piega non parte) · M: titolo di partenza `var(--olo-color-dark, #ffffff)` `:53`; pulsante senza colore blu `#2563eb` `:703`; 12 preset senza effetti visivi.
- **overlaygrid / overlayslider** — G: la tipografia del titolo non arriva sulle card con foto (CSS su `.uk-overlay h1…h4`, il markup ha `uk-overlay-primary`) `overlaygrid:271-285`, `overlayslider:303-322` · L: gap «Predefinito» di overlayslider = spazio zero.
- **slideshow** — M: radice `position:static` (frecce e pausa ai bordi della sezione) `:135`; pulsante pausa «⏸» su nero fisso; «Glow sul titolo» cerca una classe che non c'è; velo fisso a 0,45.
- **overlay** — M: l'opacità del velo si applica anche al testo `:122`; comportamento del velo diverso fra Fade/Zoom e Slide Up.
- **filmreel** — M: testo fisso su `--olo-color-text` (illeggibile sui fotogrammi scuri) `:92`; «Colore linee» salvato come oggetto diventa `solid Array` `:90`.
- **gallery** — M: la barra filtro usa `category`, che il campo galleria non permette di impostare `:361`.
- **map (Mappa Pro)** — G: accenti ed € diventano «â‚¬» (`atob` senza UTF-8) `:2521`; i tre stili CARTO (Positron, Voyager, Dark Matter) disegnano solo «API KEY REQUIRED» `:53, :907` · M: «Sopra/Sotto» porta nella fascia anche elenco e paginazione; pannello con grigi e font di sistema fissi `:1919`.
- **osmmap** — G: stili CARTO come sopra `:63` · doppione di Mappa Pro in «Indirizzo singolo».
- **pdfpro** — G: un apostrofo in un testo d'hotspot blocca tutta la tile (JSON in attributo fra apici singoli) `:144`.
- **asciiviz** — G: riquadro dei caratteri bianco (il `<pre>` di UIkit) `:168`; titolo e testi quasi illeggibili sul fondo scuro di default `:210`; «Simulato» resta piatto senza player `:350`.
- **soundcloud** — M: con l'oEmbed colore, autoplay, copertina, autore e «Player visuale» sono ignorati, iframe fisso a 500 px; `$uid` non definito `:75` (avviso PHP).
- **audio** — M: «Mostra controlli» solo nel predefinito; il minimale ignora titolo e artista; copertina solo nel personalizzato.
- **viewer360** — M: nel builder gli «Oggetto girevole» diventano neri dopo ogni render `olo-viewer360.js:280`.
- **themedemos** — L: nome nel piede con colore fisso; non carica il font scritto.
- **videoplaylist** — L: video di default Rick Astley, Gangnam Style, Despacito; tinte di hover bianche (invisibili su chiaro).
- **productgrid** — L: con la sorgente WooCommerce il tag è «SALE» in inglese.

### Marketing
- **form** — G: il «Campo calcolato» resta a 0 con errore JS se «Abilita condizioni» è spento (`getFieldValue` dentro `if (hasConditions)`) `:1050, :1147` · M: le opzioni si dividono anche sulle virgole `:1443`; il font «Titoli (tema)» delle etichette perde le parentesi `:153`; pulsante e campi in Arial; «Padding» sotto «Stile pulsante» imbottisce tutto il modulo; bordo sottolineato non sceglibile.
- **newsletter** — G: nei layout verticale e minimale i campi si schiacciano a 18 px `:181` · M: integrazioni, chiavi API, reCAPTCHA e redirect non fanno nulla (il JS manda solo email e nome); «Content Lock» disegna un riquadro sfocato vuoto `:357`.
- **loginform** — M: con le schede «Pillola» la scheda inattiva sparisce su form scuro; il social login sono solo link.
- **social** — M: etichette «Tiktok», «Youtube», «Whatsapp»; X col blu del vecchio Twitter `:82`; icona piena sempre bianca.
- **linkinbio** — M: «Mostra icone social» inerte; il raggio non ritaglia lo sfondo.
- **facebookpage** — M: con «Adatta al contenitore» il riquadro misura 0 px `:72`.
- **twitterfeed** — M: le timeline non si vedono più senza accesso a X: funziona solo il tweet singolo.
- **instagram** — L: banner dei cookie col pulsante `#2563eb` fisso (`class-cookie-consent.php:1582`).
- **countdown** — M: colore del separatore solo nello stile UIkit.
- **progresstracker** — M: «Gap aggiuntivo» inerte; icone visibili solo coi numeri spenti.
- **matchfixtures** — L: appena inserita una sola partita, in inglese, card verde scuro con stemmi in esadecimale.
- **buildermock** — M: a 0° resta inclinato (`rotateX(7deg)` fisso) `:82`; colonne interne fisse 300+268 px `:87`.
- **olox (lessons, quiz, sticky)** — M: testo chiaro su fondo trasparente (illeggibile su tema chiaro); «OLOtour (ambra)» esce verde acqua (`olox.css:15` contro `_oloxShared.js`) · L: colori solo fra i 7 dei prodotti.
- **flipcard** — G: il «Flip diagonale» mostra il retro capovolto a riposo `:502, :516`; velo perso quando la foto passa nel pannello Sfondo `:436`; ombra tagliata da `overflow:hidden` `:166` · M: in Tipografia «Titolo» colora il fronte e «Descrizione» il retro.
- **starrating** — G: le mezze stelle non si disegnano mai (`$i === ceil($rating)` fra intero e decimale) `:61` · M: stile «Arrotondato» inerte; il cursore arriva a 5 anche con 10 stelle; «4.5 / 5» col punto e non nascondibile; preset cuori e diamanti disegnano stelle.
- **progress** — G: senza colori le barre sono invisibili `:214`; con «Mostra percentuale» il numero compare due volte `:217`; «Animata» non è letto.
- **counter** — G: il numero non conta (nessuna animazione); il colore del suffisso legge `number_color`, che non esiste `:133`.
- **team** — G: «Padding contenitore» inerte (vince `tile_padding`) `:143`; «Margine dal tile» = `intval(array)` = 1 `:145`; gap negativo azzerato `:91`.
- **team, testimonial** — M: i «Filtri CSS» non li legge nessuno.
- **northquoteslider** — G: «North · enterprise AI» fisso nel codice `:184`, titolo su Cohere in inglese; puntini accanto alla citazione `:125`; frecce e puntini scuri fissi `:120, :133`.
- **leaderboard** — G: righe di default viola scuro `#1A1233` con testo scuro: l'esempio 1 è illeggibile `:110` · M: «Ruolo» colora l'unità `:236`; «Query (in arrivo)» inerte.
- **testimonial** — M: misura, font e maiuscolo della citazione agiscono solo nell'Editoriale; colore di riserva rosa `:255`.
- **pricing** — M: il badge «popolare» si allarga a tutta la card `:206, :237`; «Colore prezzo» solo con l'interruttore `:377`; separatori e binario bianchi fissi `:277, :366`; colori del countdown solo esadecimali `:481`; il periodo non cambia passando all'annuale; i preset scrivono `card_radius`, che il PHP non legge.
- **pricelist** — M: «In evidenza» sostituisce il bordo con lo sfondo `:96, :129`; hover grigio fisso `:124`; misure dei testi fisse (badge 9 px).
- **iconbox** — M: i preset «Neon», «Glass», «Brutalist» cambiano solo allineamento e misura; sfondo dipinto due volte.
- **panel** — L: «Raggio card» a 0 non toglie il raggio del tema `:239`.
- **announcementbar** — L: testi e aria-label in inglese; salvia su marrone poco contrastato.

### Interattivo
- **finder** — G: titolo, voci e risultato hanno colore fisso `--olo-color-text` `:175, :179, :197`: coi preset Neon e Retro Terminal il testo è illeggibile.
- **popup** — G: vedi A11 · M: raggio, maiuscolo, spaziatura e peso del pulsante inerti (`.olo-template .uk-button` vince) `:474`; `alt` dell'immagine da una chiave `title` inesistente `:888`.
- **accordion** — M: colore delle icone fisso sul primario `:284`; due controlli «Bordo» sulla stessa chiave (doppia riga); bordo della voce aperta forzato sul primario `:414`; prompt del terminale annullato `:263`.
- **timeline** — M: le famiglie `var(--…)` perdono le parentesi `:188`; «Colore filo» inerte col filo «solid» (vince `timeline-super.css`); polaroid alta 148 px fissa.
- **chart** — M: appena inserito quattro barre arancio identiche `:16-19`; area polare con etichette in riquadri bianchi; Chart.js col suo font; il radar ignora min/max.
- **togglebtn** — M: lati a 0 non azzerano il bordo: ricompare «2px outset» del browser (`class-tile-base.php:574`); il pulsante non eredita il font (Arial).
- **switcher** — M: bordo blu UIkit sulla scheda attiva.
- **builder (Preventivo)** — M: il contatore va su tre righe (`.obd-tot span{display:block}`) `:210` · L: «1 voci».
- **floatingpanel** — M: una sezione che contiene solo il pannello non viene resa (ID, condizioni e sfondo della sezione persi) `trait-olobuild-renderer-structure.php:31, :2125`.
- **revealbox** — M: i titoli del contenuto ignorano `top_text_color`.
- **scratchfx** — M: «Colore testo» colora anche il pulsante «Scopri» esterno `:305`.
- **projector, scorequiz, timezone** — L: sfondo e testi a colore fisso, niente versioni scure.

### Navigazione e Atmosfera
- **sitelogo** — G: «Centro» e «Destra» non fanno niente (`frontend.css:477` allinea a sinistra).
- **megamenu** — G: appena inserito il builder mette il segnaposto grigio in `logo_image`, `logo_sticky`, `mobile_logo` · M: messo nel corpo cambia la classe dell'header del sito e lo rende sticky; il pannello aperto finisce sotto le sezioni dopo.
- **navmenu** — M: come megamenu, e `olo-header-overlay` nasconde il primo blocco nei temi a blocchi; verticale senza «Sottovoci espandibili» = sottomenu irraggiungibili `:1083`.
- **oloheader** — M: CTA blu, Manrope e icone fissi fuori tema; `brand_logo_white` mai letto; pannello sotto le sezioni dopo.
- **particlefx** — M: contenuto, padding, larghezza, allineamenti, sfondo, ombra e bordo calcolati e mai stampati `:124-153`; `resolveVarColor()` ignora la riserva di `var()` (particelle nere).
- **search** — M: «Larghezza massima» mai applicata `:126-132`; la chiave `border` vale per campo e contenitore insieme.
- **livesearch** — M: compatta con `float:right` azzera l'altezza della tile (`olo-livesearch.css:16`).
- **postnavigation** — M: etichetta `#F3F4F6` su card chiara (illeggibile) `:92`.
- **breadcrumbs** — L: «Separatore» mai stampato; 12 preset senza CSS; voce corrente `#666`.
- **toc** — L: indicizza anche i titoli del footer.
- **nav** — L: hover del colore dei bordi (voci che spariscono).
- **totop** — L: «Stile» non letto; freccia minuscola senza misura né colore.
- **bottombar** — L: link su `--olo-color-heading` (quasi bianco su crema).
- **langswitcher** — L: «Mostra etichetta sotto» la mette accanto.
- **mobilebar** — L: font di sistema fisso; col padding il pannello è più largo della barra.

### Dinamico
- **queryloop** — G: «CPT slug» non funziona mai («Tipo di contenuto "custom" non trovato») `:614` · M: «Sfondo card» colora anche il blocco `:484, :679` · L: raggio 0 diventa 6 px `:810`.
- **postgrid** — G: vedi A2 (filtri e pagine) · M: velo con bordo netto a metà card (`inset:0` contro `height:N%`) `:456, :839`.
- **pagetitlebar** — G: lo sfondo media rompe lo stile (CSS senza `;` finale, si perdono `position:relative` e l'ultima dichiarazione) `class-tile-base.php:559-567`, `:104, :118`.
- **sitemap** — G: padding del contenitore perso coi tipi personalizzati (variabile sovrascritta dal ciclo) `:197, :351, :386` · M: con la ricerca nelle griglie il campo diventa una cella.
- **newsticker** — M: in slide e fade l'icona esce come testo («bolt») `:576`; titoli troncati nel verticale.
- **presencegrid** — M: ruolo su `--olo-color-muted` (colore di superficie, invisibile) `:197`.
- **portfolio** — M: buchi nella griglia (bento senza `dense`) `:118`; «caption-corner» lascia una striscia bianca `:215`; numeri sovrapposti nell'indice laterale `:245`.
- **readingtime, postmeta** — M: contano solo `post_content`, vuoto sulle pagine del builder: sempre «1 min»; «1 minuti» · L: postmeta con le voci attaccate (gap 0).
- **shortcode** — L: appena inserito `[gallery]` non disegna nulla e nessun messaggio lo dice.

### WooCommerce
- **woo_quickview** — G: vedi A14; decora solo le card già presenti all'avvio `:400`; lo stile `.olo-qv-trigger` è globale (l'ultima istanza decide per tutte) `:245`; «Stile pulsante» con valori diversi da quelli del PHP; quasi tutti i colori non letti.
- **woo_wishlist** — G: vedi A14; `show_grid` senza controllo; icona, stile e 7 colori non letti.
- **woo_recently_viewed** — G: legge un cookie che WooCommerce scrive solo col suo vecchio widget `:63`: senza widget è sempre vuota.
- **woo_product_filter** — G: molte chiavi del config diverse da quelle del PHP; `filter_style`, `apply_button`, `show_count` e i colori non letti; scrive i filtri nell'indirizzo ma Prodotti WC non li legge `:538`; attributi fissi `pa_color,pa_size`.
- **woo_product_bundle** — G: la tendina Layout dà sempre la lista verticale `:121`; `show_images/prices/descriptions` contro `show_image/price/description` del PHP.
- **woo_product_gallery_slider** — G: niente campo prodotto; transizione, autoplay, velocità e pallini non letti; «Miniature a destra» ricade in basso `:96`.
- **woo_products** — G: modalità «Carosello» e le sue 5 opzioni non lette · M: badge e pulsante senza colore di partenza (`background: ;`) `:242, :292`; attributo `class` doppio sul pulsante: l'aggiunta AJAX non parte `:400`; il Bordo incornicia la griglia e non le card; mancano raggio, padding e sfondo della card.
- **woo_order_tracking** — G: il modulo invia a My Account, che non traccia nulla `:131`.
- **woo_minicart** — M: con `icon_color` vuoto l'icona sparisce (`stroke=""`).
- **woo_categories** — M: «Mostra immagine» non letto; default poco leggibile.
- **woo_myaccount** — M: «Con sidebar» spezza il modulo d'accesso; per i visitatori nessun colore agisce.
- **woo_notices** — M: il riquadro tratteggiato «Le notifiche… appariranno qui» lo vedono anche i visitatori `:94`.
- **woo_comparison** — L: testo «vuoto» e colori solo nel PHP; etichette fisse.
- **14 tile della scheda prodotto** (titolo, prezzo, immagine, galleria, schede, meta, scorte, valutazione, descrizione, aggiungi al carrello, badge saldo, navigazione, correlati, upsell) — M: mostrano «Nessun prodotto disponibile in questo contesto» anche ai visitatori; manca un campo «Prodotto» per usarle in pagine di lancio.

---

## D. Trovati scrivendo le impostazioni di partenza (5 ottobre, sera; righe della 1.4.535)
Solo ciò che non è già nella sezione B. Fra parentesi la gravità.
- **Essenziale/Testo** — table (M): l'intestazione ricade su `on-primary` mentre lo sfondo è il secondario `:70`, «No table data» `:61` · headline (L): le linee decorative spariscono quando il titolo va a capo · textmask (L): default «WELCOME…», `bg_color #000000` `:14, :32` · spacer (L): forme `#ffffff`/`#000000` fisse.
- **Layout** — grid (M): le etichette del filtro, ricavate dallo slug, perdono gli accenti `:119-131`; l'icona di una voce non disegna niente `:211`; card con `#fff`/`#e5e7eb` fissi `:284-312` · info-cards (M): `card_bg #0f172a` e `#10b981` nei default PHP `:32-36`; nessun ripiego delle colonne sul telefono · workgrid (L): il default alto/normale/normale/alto lascia buchi `:94`.
- **Media** — map, osmmap (M): attribuzione sempre «OpenStreetMap» anche con Esri o CARTO `osmmap:107, map:390` · themedemos (M): «Saffron» scuro su scuro `:23` · showcasegrid (M): `hex_rgb()` legge la riserva del token, il velo non segue la palette `:102`; default inglesi · svganimator (M): il segnaposto PNG finisce in `svg_url` e il PHP lo scarica; «Replay» `:38, :152` · videoplaylist (L): testo `primary-contrast` su fondo `secondary` `:69` · viewer360 (L): fondo `#111` fisso; con una sola foto a 180° si vede a specchio · marquee (L): testo col contrasto del secondario su fondo scuro `:108` · imgcompare, osmmap, audio (L): `#1F2937`, `#e74c3c`, play bianco fissi; «Seleziona un file audio» non tradotto.
- **Marketing** — loginform (M): nel canvas l'amministratore vede «Bentornato… Esci» al posto del modulo `:461` · countdown (M): la chiave del localStorage cambia a ogni caricamento, il conto riparte `:225` · pricelist (L): con «Mostra immagine» e voci senza foto restano quadrati grigi `:143` · buildermock (L): sottotitolo di default tagliato.
- **Interattivo** — switcher (L): di serie il preset «Pill Sliding» con `indicator_type none`.
- **Navigazione/Dinamico** — mobilebar (M): il nome del sito di ripiego è `#fff` fisso `:484` (invisibile sulla barra chiara) · readingtime (M): `icon` non letto `:124`, `font_size` senza unità `:106` · wpcomments (M): `get_comments_number() === 0` stringa contro intero `:68`; `#888`, `#fff` · pagination (M): la chiave `border` vale per due controlli `:248` · toc (L): `text_color` calcolato e mai usato `:42` · newsticker (L): `#dc2626` e «Breaking» nei default `:21-33` · queryloop (L): tempo di lettura `rgba(0,0,0,.55)` fisso `:266` · portfolio (L): **emoji** 🖼 per l'immagine mancante `:829`; postnavigation/relatedposts `#1F2937` `:153, :179` · presencegrid (L): `@` fissa `:418`, hover `#8B5CF6` · authorbox (L): il segnaposto legge `padding` invece di `tile_padding` `:85`.
- **WooCommerce** — colori vuoti senza riserva (G) anche in sale_badge `:65`, categories `:79`, checkout `:56-61`, related/upsells · sale_badge (M): «Posizione» non agisce `:154` · checkout_multistep (M): «Stile step» e «Mostra riepilogo» inerti, `active_color` mai usato `:62` · gallery_slider (M): larghezza massima fissa 600 px `:30` · cross_sells, recently_viewed (L): `heading_tag` inerte · rating (L): «(1 recensioni)» `:83` · myaccount, comparison (L): «Ciao, » e «WooCommerce non attivo» senza traduzione.

---

## C. Doppioni e tile da ripensare
- **Frammento**: da ritirare (div vuoto; ID e classe li danno già le Avanzate e l'Ancora menu).
- **Hours Strip ≈ Statstrip**: unibili in una Statstrip con «nota».
- **Galleria ⊂ Pro Gallery**; **Overlay ≈ una card di Overlay Grid**; **Carousel, Slideshow e Overlay Slider** si sovrappongono molto.
- **Mappa OSM = Mappa Pro in «Indirizzo singolo»** (stesso codice).
- **X Timeline**: il nome promette una timeline, funziona solo il tweet singolo.
- **Kill Next/Prev** e **Paginazione**: nel catalogo non mostrano nulla per natura (servizio, archivi).
