# BRIEF — Campagna hardening EscapeOutput (wordpress.org compliance)

Sei incaricato di sistemare gli errori PHPCS `WordPress.Security.EscapeOutput.OutputNotEscaped`
nei file PHP assegnati del plugin WordPress Olobuild (page builder, `D:\TECNICA\olobuild`).

## OBIETTIVO
Ogni `echo`/`print` di variabile deve risultare: (a) escapato con una funzione `esc_*`/cast
riconosciuta da PHPCS, oppure (b) coperto da annotazione `phpcs:ignore`/`phpcs:disable`
documentata e VERITIERA. Lo scopo è duplice: zero errori nel report Plugin Check E zero
modifiche all'output renderizzato.

## REGOLA SUPREMA — L'OUTPUT NON DEVE CAMBIARE DI UN BYTE
Qualsiasi `esc_*` che POTREBBE alterare il valore stampato è VIETATO. In dubbio usa
l'annotazione phpcs, non l'escape. Esempi di alterazioni vietate:
- `esc_attr()`/`esc_html()` dentro blocchi `<style>` su stringhe che possono contenere
  apici o virgolette (es. `font-family: Georgia, 'Times New Roman', serif` →
  diventerebbe `&#039;` = CSS ROTTO).
- `esc_html()` su stringhe che contengono HTML legittimo (richtext, SVG, markup assemblato).
- `esc_url()` su valori che possono essere `var(--x)`, data-URI consentiti, o stringhe
  composte (`url(...)` CSS).
- Aggiungere/inferire sanitizzazione che cambia il valore (trim, default diversi, ecc.).

## COSA FARE, per contesto

### 1. Interi / numeri
Se la variabile è già prodotta da `intval()`, `(int)`, `absint()`, `max()/min()` su interi,
`floatval()`: aggiungi il cast inline nell'echo — innocuo e PHPCS lo riconosce:
`<?php echo (int) $header_pad_y; ?>` · `<?php echo (float) $opacity; ?>`
Se la variabile è USATA come numero ma a monte NON è forzata numerica, forza il cast
inline comunque (`(int)`/`(float)`) — questo è un hardening reale e sicuro.
ATTENZIONE: solo se il valore è SEMPRE numerico. Se può essere stringa CSS ('auto',
'50%', '10px'), NON castare → vai di annotazione (o `esc_attr` se in attributo HTML).

### 2. Blocchi `<style>` e `<script>` inline generati
Pattern dominante nelle tile: blocco `<style>` con decine di `<?php echo $var; ?>` dove
le variabili sono sanitizzate A MONTE (`safe_color_css()`, `intval`, whitelist
`in_array`/`preg_match`, ternari a valori fissi, helper `Olo_Tile_Utils::*`,
`build_*_css()` della base class).
Procedura:
a. VERIFICA una per una che OGNI variabile echata nel blocco sia davvero sanitizzata a
   monte. Se qualcuna non lo è, sanitizzala a monte nello stile del file (es. colore →
   `$x = $this->safe_color_css( $s['x'] )`; numero → `intval`; enum → whitelist
   `in_array(..., true)`). NON cambiare i default né il comportamento per valori validi.
b. Per gli interi nel blocco usa il cast inline `(int)` (punto 1) dove non rompe nulla.
c. Racchiudi il blocco con la COPPIA di annotazioni (riga propria, subito dentro/fuori il tag):
   `<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above (safe_color_css/intval/whitelist). ?>`
   ... blocco ...
   `<?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>`
   MAI dimenticare il `phpcs:enable` di chiusura. MAI disable per l'intero file.

### 3. Markup HTML
- Testo piano in element content → `esc_html()` se manca E se il valore è certamente
  testo puro (titoli, label). Se può contenere markup voluto → annotazione.
- Valore in attributo HTML (`class="<?php echo $x ?>"`) → `esc_attr()` se il valore è
  un token/id/classe semplice. Se è CSS inline composto (style="...") con possibili
  apici → annotazione.
- `href`/`src` con URL veri → `esc_url()`. Se il valore può essere un anchor `#id`,
  `tel:`, `mailto:` ecc. esc_url li gestisce — ok. Se può essere altro (es. stringa
  template, var CSS) → annotazione.
- HTML già filtrato (`wp_kses_post`, `sanitize_richtext`, `safe_richtext_content`,
  `wp_kses` con set custom) o generato da helper interni (`render_icon_html`,
  `build_border_css`, `tfx_*`, `render_hover_wrap`, output di altri `render()`):
  → annotazione puntuale:
  `// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized via wp_kses_post() above` (motivo SPECIFICO e VERO).
- `wp_json_encode(...)` in attributo → avvolgi con `esc_attr()` SOLO se l'attributo è
  delimitato da apici doppi nel markup; in `<script>` JSON inline → annotazione.

### 4. Annotazioni phpcs — sintassi
- Inline (stessa riga): `<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- reason ?>`
- Una riga con PIÙ echo: una sola annotazione inline copre l'intera riga fisica.
- Blocco: coppia `phpcs:disable` + `phpcs:enable` SOLO della regola specifica
  `WordPress.Security.EscapeOutput.OutputNotEscaped`, mai generico, mai file-level.
- Motivi in INGLESE, specifici, veritieri (li leggeranno i reviewer di wordpress.org).

## COSA NON FARE
- NON toccare logica, default, struttura HTML, classi CSS, testi.
- NON aggiungere escaping "tanto per": ogni esc_* aggiunto deve essere PROVATAMENTE
  innocuo per i valori possibili di quella variabile (leggi come viene costruita!).
- NON occuparti di altri warning PHPCS (DB, nonce, unslash...): SOLO EscapeOutput.
- NON usare `// phpcs:ignoreFile`.
- NON modificare file diversi da quelli assegnati.
- Niente build, commit, deploy: solo gli edit.

## METODO
1. Leggi il file INTERO prima di toccarlo (capisci dove le variabili sono sanitizzate).
2. Trova tutti gli echo/print di variabili (grep `echo \$|echo esc|print` nel file aiuta,
   ma poi ragiona sul contesto di ognuno).
3. Applica le regole sopra. Preferisci il disable-a-blocco per i grossi blocchi <style>
   (1 coppia di annotazioni invece di 50 ignore singoli), DOPO la verifica a monte.
4. Rileggi il diff mentale: l'output renderizzato resta identico byte-per-byte per
   qualunque input valido? Se no, correggi.
5. Verifica bilanciamento dei tag PHP e delle coppie disable/enable.

## OUTPUT RICHIESTO (testo finale del tuo report)
Per ogni file: `path — N echo trattati: X esc reali, Y cast int, Z annotazioni (W blocchi). Note: ...`
Segnala SEMPRE: variabili che hai dovuto sanitizzare a monte; punti ambigui dove hai
scelto l'annotazione; qualsiasi sospetto di vulnerabilità REALE trovata (valore utente
che arriva all'output senza alcuna sanitizzazione).
