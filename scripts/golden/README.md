# Banco di verifica comune — sistema compatto, passo 0.1 (`compatto-1`)

Un solo banco per i tre piani del sistema compatto (chrome dell'inspector, CSS di resa,
densità del contenuto — `audit_results/MIGLIORIE_OLOBUILD_2026-09.md`). Serve a dire, dopo
ogni passo, **che cosa è cambiato nella resa e che cosa no**, con i numeri.

| Livello | Strumento | Dove gira | Cosa confronta |
|---|---|---|---|
| L1 — HTML | `render-golden.php` + `golden-stub.php` (con `banco.sh`) | mosaic | md5 dell'HTML di ogni template del DB e di ogni pagina dei temi |
| L2 — resa | `golden-shots.mjs` | in locale | screenshot a 1440/1199/959/639/390 px, per tema, pixel per pixel |
| Dati | `censimento.php` (+ `esporta-default-js.mjs`) | mosaic (+ locale) | le sei voci del censimento su template, widget globali, pagine dei temi e libreria (blocchi e pagine), sola lettura |
| Codice | `scripts/audit-ui-standard.mjs` | in locale | 13 regole nuove `chrome-*`, `css-*`, `densita-*` |

Niente di questa cartella entra nel pacchetto del plugin (`scripts/` è escluso dallo ZIP e da
`git archive`) e niente va copiato in `wp-content`: sul server vive in `/root/olo-golden`.

## File

| File | Cosa fa |
|---|---|
| `golden-stub.php` | caricato con `wp --require=…` PRIMA di WordPress: `wp_rand()` e `wp_create_nonce()` fissi durante il render, identici al core fuori |
| `render-golden.php` | `wp eval-file`: rende i template (`src=db`), le pagine dei temi (`src=themes`) o entrambi (`src=all`) con l'ingresso del sito, **un processo per template** |
| `normalizza.json` | le regole applicate all'HTML prima dell'md5 (stessa sintassi in PCRE e JS) |
| `confronta.sh` | confronta due snapshot: uscita 0 = identici, 1 = diversi o chiavi non rese (elenco), 2 = uso o snapshot incompleto |
| `banco.sh` | il comando unico su mosaic: render + riepilogo, e confronto se si passa un riferimento; anche `censimento`, `elenco`, `confronta` |
| `censimento.php` | censimento di sola lettura del DB, delle pagine dei temi e della libreria (`lib-*`: `template-library.json` e `page-templates/`, che il builder inserisce senza fondere i default) (JSON + riepilogo leggibile) |
| `esporta-default-js.mjs` | in locale: i default JS delle tile (Vite SSR) per le divergenze PHP↔JS del censimento |
| `golden-shots.mjs` | in locale: screenshot con Playwright (non è una dipendenza del repo) e confronto pixel |
| `tema-prova/` | tema classico minimo: `a{color:red} h2{margin:2em 0} .entry-content p{margin-bottom:1.5em}` |
| `prova-banco.mjs` | prova in locale normalizzazione e `confronta.sh` (senza WordPress) |

## Comandi

Host e chiave di mosaic sono quelli della memoria di progetto. In PowerShell (l'`ssh` di Git
Bash non va: si usa quello di Windows):

```powershell
$M = 'root@<ip di mosaic>'
$K = "$HOME\.ssh\<chiave di mosaic>"
$SSH = 'C:\Windows\System32\OpenSSH\ssh.exe'; $SCP = 'C:\Windows\System32\OpenSSH\scp.exe'
```

### 1. Copiare il banco su mosaic (dalla radice del repo)

Un solo archivio (lo scp di più file è inaffidabile; `--no-same-owner` perché l'archivio
arriva da Windows):

```powershell
tar -cf "$env:TEMP\olo-golden.tar" -C scripts golden
& $SCP -i $K "$env:TEMP\olo-golden.tar" "${M}:/root/"
& $SSH -i $K $M "mkdir -p /root/olo-golden && tar -xf /root/olo-golden.tar -C /root/olo-golden --strip-components=1 --no-same-owner && rm /root/olo-golden.tar"
```

Per le divergenze PHP↔JS del censimento, prima esportare i default JS (in locale) e copiarli:

```powershell
node scripts/golden/esporta-default-js.mjs --out "$env:TEMP\default-js.json"
& $SCP -i $K "$env:TEMP\default-js.json" "${M}:/root/olo-golden/"
```

### 2. Su mosaic

```bash
cd /root/olo-golden
# lo stub si carica prima di WordPress: deve stampare /root/olo-golden/golden-stub.php
php -d memory_limit=512M /usr/local/bin/wp --allow-root --path=/var/www/wordpress \
  --require=/root/olo-golden/golden-stub.php \
  eval 'echo (new ReflectionFunction("wp_rand"))->getFileName(), "\n";'

bash banco.sh baseline-1.4.480                              # L1 di riferimento
bash banco.sh prova-B baseline-1.4.480                      # stesso codice: IDENTICI, uscita 0
ORDINE=inverso bash banco.sh prova-C baseline-1.4.480       # ordine inverso: IDENTICI (isolamento)
JS=/root/olo-golden/default-js.json bash banco.sh censimento   # SRC=db|themes|all (predefinito all)
```

Dopo un passo: `bash banco.sh dopo-0.2 baseline-1.4.480` → elenco dei template cambiati, con
`(dati)` se nel frattempo qualcuno ha modificato il template, `(resa)` se a dati uguali è
cambiato l'HTML. Solo alcune chiavi: `SOLO=db-7,tema-atelier--homepage bash banco.sh prova-D
baseline-1.4.480`. Lo snapshot parziale ha `_solo.txt` con le chiavi rese: `confronta.sh`
confronta solo quelle e conta le altre chiavi della baseline come «non confrontate», non
come differenze.

Una chiave **non resa** in B non è mai «identica», anche se l'md5 coincide: un fatale ha l'md5
vuoto in entrambi gli snapshot, e un banco i cui figli muoiono tutti passerebbe il confronto.
`confronta.sh` la elenca come `non reso` ed esce con 1. Per le pagine dei temi (`tema-*`) conta
come non resa anche `vuoto`: nessuna delle 208 pagine JSON è vuota, quindi il renderer non ha letto
la riga finta nel gruppo di cache `olo` (il meccanismo da provare per primo). È ammesso (con
`attenzione`) solo lo stesso `errore` con lo stesso messaggio in A e in B, o un fatale (o una pagina
vuota) noto elencato a mano con `ACCETTA=db-12,db-40 bash banco.sh …` purché in A fosse nello stesso
stato. Se in B nessuna chiave è `ok`, il confronto non vale; senza `_style-system.css` l'uscita è 2
(V4 non verificato).

`banco.sh` equivale a:

```bash
php -d memory_limit=512M /usr/local/bin/wp --allow-root --path=/var/www/wordpress \
  --require=/root/olo-golden/golden-stub.php \
  eval-file /root/olo-golden/render-golden.php src=all out=/root/olo-golden/snap/<fase> mem=512M
bash /root/olo-golden/confronta.sh /root/olo-golden/snap/<riferimento> /root/olo-golden/snap/<fase>
```

Senza `--require` il render si ferma subito con un messaggio (niente uid casuali in silenzio).

### 3. Riportare i risultati in locale (fuori dal repo)

```powershell
& $SSH -i $K $M "tar -cf /root/olo-golden/snap.tar -C /root/olo-golden snap"
& $SCP -i $K "${M}:/root/olo-golden/snap.tar" "D:\TECNICA\olo-golden-snap.tar"
& $SSH -i $K $M "rm /root/olo-golden/snap.tar"
New-Item -ItemType Directory -Force D:\TECNICA\olo-golden | Out-Null
tar -xf D:\TECNICA\olo-golden-snap.tar -C D:\TECNICA\olo-golden
```

La baseline resta su mosaic in `snap/baseline-<versione>` e in locale fuori dal repo: non si
committa (il contenuto di mosaic cambia).

### 4. L2 in locale

Playwright non è nel repo. Si usa un `playwright-core` già presente (per esempio quello del
driver Python) o uno installato in una cartella esterna:

```powershell
$env:OLO_PLAYWRIGHT = 'C:\Program Files\Python311\Lib\site-packages\playwright\driver\package'
# oppure: & 'C:\Program Files\nodejs\npm.cmd' install --prefix D:\TECNICA\pw playwright-core ; $env:OLO_PLAYWRIGHT = 'D:\TECNICA\pw'
$env:OLO_GOLDEN_RESOLVE = 'MAP mosaic.clod.eu <ip di mosaic>'   # scavalca il CDN
# una volta: l'utente accede a mano, lo script salva solo la sessione (fuori dal repo)
node scripts/golden/golden-shots.mjs --salva-accesso D:\TECNICA\olo-golden\mosaic-state.json --site https://mosaic.clod.eu
$env:OLO_GOLDEN_STATE = 'D:\TECNICA\olo-golden\mosaic-state.json'
node scripts/golden/golden-shots.mjs --urls D:\TECNICA\olo-golden\snap\baseline-1.4.480\_urls.tsv `
  --widths 1440,1199,959,639,390 --theme attivo,olo-tema-prova,hello-olobuild --out D:\TECNICA\olo-golden\shots\A
# … dopo il passo, stesso comando con --out …\shots\B, poi:
node scripts/golden/golden-shots.mjs --compare D:\TECNICA\olo-golden\shots\A D:\TECNICA\olo-golden\shots\B
```

- `--theme <slug>` aggiunge `?wp_theme_preview=<slug>` (core ≥ 6.3, utente con
  `switch_themes`): il tema deve essere **installato** su mosaic. `tema-prova/` si installa
  copiandolo in `wp-content/themes/olo-tema-prova` — operazione da far decidere all'utente,
  il banco non lo fa. Hello Olobuild è `includes/theme-bundle/` (lo installa il setup wizard).
  Senza utente o senza tema WordPress torna **in silenzio** al tema attivo: per questo un tema
  diverso da `attivo` vuole `--state`, e ogni scatto controlla che la pagina carichi un foglio
  di stile da `/themes/<slug>/` (lo fanno sia `olo-tema-prova` sia Hello Olobuild). Se no, niente
  scatto e un errore nel manifest (uscita 1). Un ottimizzatore che unisce i CSS farebbe fallire
  il controllo: dalla parte sicura.
- Ogni scatto controlla anche la risposta: stato HTTP ≥ 400, o un rimando a `wp-login.php`
  (sessione scaduta), è un errore nel manifest, non uno scatto.
- `--compare` con A senza scatti dà uscita 1 («il confronto non vale»), come `confronta.sh`.
- Ogni indirizzo riceve `?olo_golden=1`, che salta la cache a pagina intera.
- Si fotografano solo i template con un post collegato (`_urls.tsv`); gli altri (widget,
  popup, header e footer non collegati) restano solo L1. Il numero è in `_statistiche.txt`.
- Stabilità: `reducedMotion: reduce`, animazioni ferme, ora fissa (`--no-clock` per
  toglierla), scorrimento fino in fondo per idratare le tile lazy, `fonts.ready`, maschera su
  video, iframe, canvas e mappe. Le pagine che cambiano comunque vanno elencate e mascherate.

### 5. Prove locali (senza WordPress)

```bash
node scripts/golden/prova-banco.mjs          # normalizzazione + confronta.sh su snapshot finti
node scripts/audit-ui-standard.mjs --list    # le regole nuove con le occorrenze
```

## Che cosa rende il banco, e come lo rende ripetibile

**Ingresso**: `(new Olobuild_Frontend_Renderer())->render_shortcode(['id' => N])`, lo stesso di
`[olobuild_template]` e di `auto_render_template()` sul sito — non `render_tiles_array()`
(anteprima REST e iframe), che non ha `.olo-template` né `index_tiles` e usa l'id 0. Le pagine
dei temi (208, di cui una nel formato `{settings, content}`) entrano dallo stesso ingresso con
una riga finta nel gruppo di cache `olo` reso non persistente: `Olobuild_Database::get_template()`
legge prima la cache. L'id finto (900000 + crc32 della chiave) è fisso per chiave.

**Un processo per template.** Il renderer tiene stato statico per processo: solo le prime 3 tile
del processo sono eager (`static $element_counter` in `maybe_lazy_wrap`), gli script «stampati una
volta», i registri di `index_tiles`, il contatore di `wp_unique_id()`. Il processo principale
lancia un `wp eval-file` per chiave (`WP_CLI::runcommand`, che ripete `--path`, `--require`,
`--allow-root`); la prova con `ORDINE=inverso` deve dare gli stessi md5.

**Neutralizzato (e perché)**

| Fonte | Rimedio |
|---|---|
| `wp_rand(10000, 99999)` (168 uid di tile) e ogni altro `wp_rand()` | stub: md5(chiave · file chiamante · n-esima chiamata da quel file); fuori dal render `random_int()` come il core |
| nonce (`wp_create_nonce`, `wp_nonce_field`: login, queryloop, woo, REST) | stub: valore fisso per azione nel render (i nonce veri cambiano ogni 12 ore) |
| `shuffle()` (gallery, proslider), `wp_generate_uuid4()` (migrazione legacy) | `mt_srand(crc32(chiave))` |
| `ORDER BY RAND()` (relatedposts, woo-related, productgrid) | la query diventa `RAND(1)` |
| fine riga CRLF di PHP salvati su Windows (`\r` nell'HTML e nello Style System) | `normalizza.json` «a-capo» → LF, come fa il browser; `_style-system.css` scritto con LF. Uno snapshot preso prima di una regola nuova si allinea con `php rinormalizza.php <snapshot>` (senza WordPress) |
| pari merito di `ORDER BY` (post con la stessa data: la mappa dei servizi) | `posts_orderby` + `ID DESC` come ultimo criterio: stesse righe, ordine fisso |
| id e credenziali a orologio dei plugin fratelli (`oa-…-<md5(uniqid())>` di olo-booking, `otfaq-<md5(microtime())>` di olo-tutor, credenziali TURN di olotour) | `normalizza.json` → `UID`, `TS`, `CRED` |
| rete (miniature YouTube/Vimeo della video tile, feed) | `pre_http_request` → errore: sempre il ripiego (miniatura hq) |
| transient | solo in memoria (`wp_using_ext_object_cache(true)`, gruppi non persistenti) |
| cache CSS su file (`uploads/olobuild-cache`, `<link …?v=VERSIONE>`) | `css_cache_files=false` solo nel processo del banco: il CSS di hover e dispositivo resta inline |
| token dei moduli `v2:<time()>:<hmac>` | `normalizza.json` → `v2:0:TOKEN` |
| `?ver=` / `?v=` con `OLOBUILD_VERSION` (cambia a ogni passo) | `normalizza.json` → `VERSIONE` |
| sali di WordPress | `wp_salt()` chiamato PRIMA della finestra deterministica: se mancassero, nascerebbero casuali davvero |

L'HTML normalizzato è `<chiave>.html`; quello grezzo, se diverso, sta in `_grezzo/`.

**Non neutralizzato, ma elencato** nella colonna `volatili` di `_summary.tsv`: condizioni per
data e ora (`cond_type` date/day/time, e `cond_2_type` quando `cond_logic` è `or`;
`cond_show_from/until`), popup e hiddenpop programmati, postmeta con la data di oggi,
presencegrid, il badge «nuovo» di queryloop, i campi dinamici `datetime`, i tag
`{{current_date}}`, `{{current_time}}`, `{{current_year}}` nei testi e i token a graffa singola
`{current_year}`, `{current_date[:formato]}`, `{post_count}` che `Olobuild_Dynamic_Content::resolve_tokens`
risolve nell'HTML di ogni tile. A cavallo di mezzanotte (o di una data, o di un minuto per
`current_time`, o di un post pubblicato per `post_count`) questi template possono cambiare da
soli: se il confronto li segnala, si guarda prima la colonna `volatili`. La colonna guarda anche
dentro ciò che il template rende al suo interno, con gli stessi archi della voce d) del
censimento: template incorporati (`template_id`, `widget_id`, `*_template_id`, `panel_templates`,
shortcode nei testi; ognuno una volta sola, contro i cicli) e widget globali (`global_id`).

**Sola lettura.** Ogni `INSERT/UPDATE/DELETE/REPLACE/CREATE/ALTER/DROP/TRUNCATE/RENAME` durante il
render è bloccato e contato (colonna `scritture`, dettaglio in `_avvisi/`); il processo principale
confronta `uploads/olobuild-cache` prima e dopo. Le stesse guardie valgono per il **processo
principale**, che chiama `generate_css()` per `_style-system.css` e `_ambiente.txt`: senza, lo Style
System passerebbe da `Olobuild_Font_Host` (richiesta a Google, woff2 in `uploads/olo-fonts`,
transient salvato) e cambierebbe con lo stato del transient e della rete. Con le guardie
`_style-system.css` esce senza le `@font-face` self-hosted, uguale a ogni giro; la riga `pilota:` di
`_statistiche.txt` dice scritture (attese 0) e richieste di rete bloccate (`fonts.googleapis.com`
se lo Style System ha dei Google Fonts), dettaglio in `_avvisi/_pilota.txt`. `object_cache_esterno`
e `impronta_olobuild_performance` di `_ambiente.txt` si leggono prima delle guardie. Restano fuori
dal controllo i file scritti da altri plugin e l'eventuale cartella del mese creata da
`wp_upload_dir()`, come in ogni vista.

**Fuori da L1**: quello che il render accoda (`wp_enqueue_*`, elencato nella colonna
`accodati`) e gli script stampati in `wp_footer`; lo Style System sta in `_style-system.css`.

## Uscite di uno snapshot

| File | Contenuto |
|---|---|
| `<chiave>.html` | HTML normalizzato; chiave `db-<id>` o `tema-<tema>--<pagina>` |
| `_summary.tsv` | per chiave: `fonte id tipo stato md5 md5_grezzo byte wrapper wrapper_style_vuoto style_vuoti decl_vuote pxpx css_blocchi css_byte css_dup_byte rand accodati volatili scritture rete avvisi errore` |
| `_md5.txt` | `md5  chiave` |
| `_style-system.css` | `generate_css()`: fra due fasi solo righe aggiunte (V4) |
| `_dati.tsv` | impronta dei dati di ogni template (MD5 di content e settings) e di ogni file di tema |
| `_urls.tsv` | `chiave post_id url` dei post collegati, per L2 |
| `_ambiente.txt` | versioni, tema, plugin, impronte di `olobuild_styles`, colori, performance, Style System |
| `_statistiche.txt` | totali per db / tema / tutti, errori, pagine di tema vuote, scritture e rete bloccate al pilota, olobuild-cache prima e dopo |
| `_solo.txt` | solo negli snapshot resi con `SOLO=`: le chiavi rese (per `confronta.sh`, snapshot parziale) |

`stato`: `ok`, `vuoto` (il renderer ha risposto con un commento: template vuoto, non trovato,
non disponibile), `errore` (eccezione, messaggio in `errore`), `fatale` (il processo è morto:
md5 vuoto). `errore` e `fatale` in B contano sempre nel confronto, e `vuoto` per le pagine dei
temi (vedi sopra).

## Protocollo V1–V6 (aggiornato al banco)

- **V1 — dati**: 11 template (section, headline, spacer, iconbox, text-block, button, image,
  badge, cta-banner, megamenu, accordion); selezionare ogni tile senza modificarla, salvare,
  esportare; ripetere dopo il passo → diff JSON = 0. `normalizeNodes` sta in
  `src/stores/treeUtils.js`: si confrontano due salvataggi.
- **V2 — HTML (L1)**: `bash banco.sh <fase> <riferimento>` → `IDENTICI` (uscita 0: nessuna chiave
  non resa, almeno una `ok`, `_style-system.css` presente). In `_statistiche.txt`: `[tema] 208
  chiavi: 208 ok, 0 vuote` e `pilota: scritture bloccate 0`. La controprova via REST
  (`/templates/{id}/render`) non vale come prova: passa da `render_tiles_array`, HTML diverso dal
  sito, e chiede una application password.
- **V3 — resa (L2)**: `golden-shots.mjs` a 5 larghezze, tema attivo + `olo-tema-prova` +
  `hello-olobuild`, poi `--compare` → `IDENTICI`.
- **V4 — CSS globale**: `confronta.sh` controlla `_style-system.css` (nessuna riga tolta o
  cambiata). Il blocco `<style id="olo-style-system">` esiste solo nell'iframe del builder: sul
  sito lo Style System è `wp_add_inline_style('olo-frontend-css')`.
- **V5 — audit**: `node scripts/audit-ui-standard.mjs && node scripts/audit-ui-standard.mjs --list`.
- **V6 — chrome**: DevTools su 320/384/600 px d'inspector, Comoda e Compatta.

## Regole d'audit del sistema compatto

Entrate col passo 0.1, soglia = conteggio del 24 set 2026 (quelle a 0 restano 0, le altre
possono solo scendere). Commenti `/* … */` esclusi; dettaglio con `--list`.

| Regola | Soglia | Che cosa conta |
|---|---|---|
| `chrome-nelle-tile` | 0 | `--olo-ui-(ctl\|fs\|lh\|s1-6\|row\|inline\|section\|panel\|rail\|toolbar\|sb\|switch\|swatch\|label\|status)` in tile PHP, tile Vue, config, frontend.css |
| `chrome-descrizione-invisibile` | 240 | campi con `description` resi in linea da InspectorField (tipi `INLINE_COMPACT`/`INLINE_FILL` letti dal componente, esclusi `layout:'block'`, hoverable, `aiGenerate:'alt'`, geocode). Nelle voci dei ripetitori (`itemFields`) contano solo i tipi che ContentItemsEditor delega a InspectorField: quelli di `CIE_NATIVE` (letto dal componente) la description la mostrano. → 0 con A3 |
| `fantasma-durata` | 79 | `withHover()` la cui durata (`{key}_hover_duration` o `hoverDurationKey`) il PHP non legge né per nome né via `build_hover_css()`/`radius_hover*()`. Il tipo della tile è il `type` di primo livello di `export default { … }` (il primo `type:` del file può essere quello di un campo: proslider, revealbox, spacer) |
| `css-layer-tile` | 0 | `@layer` nel CSS di tile e di resa |
| `css-container-senza-nome` | 0 | `@container (` senza nome |
| `css-cq-fuori-container` | 0 | unità `cqi/cqw/cqh/cqb/cqmin/cqmax` fuori da `@container olo-cell` |
| `css-uid-casuale` | 168 | `wp_rand(10000, 99999)` in `includes/tiles` |
| `css-pxpx` | 0 | in `includes/` un helper che restituisce già l'unità (`spacing_css`, `sides_css`, `border_radius`, `build_border_radius_css`, `radius_force_css`, `css_len`) seguito da `. 'px'` o da `}px`; la chiamata si chiude sulle sue parentesi. Controprova sull'HTML: colonna `pxpx` di `_summary.tsv` (vede anche i valori passati da una variabile) |
| `css-soglie-fuori-contratto` | 93 | `min-/max-width` in px nelle `@media` di tile PHP e CSS di resa fuori da 1400/1200/960/640/480 e −1 |
| `densita-doppia` | 0 | `calc()` con `var(--olo-density\|ds\|dr)` E `var(--olo-space-*\|radius-*)` |
| `densita-ds-senza-ripiego` | 0 | `var(--olo-ds\|dr\|density)` senza `, 1` |
| `densita-token-in-tile-esistenti` | 0 | `var(--olo-space-*\|radius-*)` nelle tile (le nate dopo si elencano in `TILE_NATE_DOPO_0_1`) |
| `densita-letterali` | 1926 | dichiarazioni padding/margin/gap/border-radius (e lati) con un intero ≥ 3px in `includes/tiles` (regex fissata nell'audit: il report diceva 1.896 con una regex non scritta) |

**Rinviate** — la regola nasce col passo che crea ciò che controlla:

| Regola | Passo | Motivo |
|---|---|---|
| `chrome-token`, `chrome-accento` | A1 | i token `--olo-ui-*` e le esclusioni dell'accento non esistono ancora: oggi il conteggio (7 ridichiarazioni, o 152–268 accenti a seconda di cosa si conta) non ha una definizione unica |
| `chrome-segmentato-unico` | A1 / D1 | oggi è un elenco scelto a mano («3»), non una regola |
| `chrome-popover-motore` | A1 / D3 | l'euristica Teleport + getBoundingClientRect dà 7 file, il report 5: si fissa con l'elenco di D3 |
| `css-important-tile` | dopo B1 | B1 aggiunge la sua costante: fissata prima (448 = 420 + 28), B1 dovrebbe alzarla |
| `densita-default-gemelli` | 0.3 | oggi non è 0 e l'insieme da confrontare lo calcola il censimento (voce b) |
| `densita-scala-unica`, `densita-gemelli`, `densita-token-consumati` | C1 | le costanti SPACE/RADIUS/DENSITY gemelle nascono in C1 (oggi SPACE JS 12/16/24 contro token 16/24/32) |
| `densita-fisse-elenco` | C2 / E3 | l'elenco delle tile a densità fissa nasce in C2 |
| `densita-auto-gemelli`, `densita-auto-renderer` | E1 | il valore `auto` nei campi nasce in E1 |

CLAUDE.md dice che le soglie sono a 0 salvo le `fantasma-*`: le nuove soglie di debito
(`chrome-descrizione-invisibile`, `css-uid-casuale`, `css-soglie-fuori-contratto`,
`densita-letterali`) sono la stessa idea — possono solo scendere — ma la riga va aggiornata
dall'utente. Lì anche «~206 descrizioni invisibili»: il conteggio di `chrome-descrizione-invisibile`
è 240.

## I numeri «non verificati» del report

| Report | Qui |
|---|---|
| 168 `wp_rand(10000, 99999)` | riprodotto: regola `css-uid-casuale` = 168 |
| 1.896 letterali | 1926 con la regex scritta in `densita-letterali` |
| 1546 `style=""` su 1809 wrapper | `wrapper_style_vuoto` / `wrapper` in `_statistiche.txt`, gruppo `[tema]` |
| 23,1% di `<style>` duplicati | «blocchi <style> duplicati … dei byte» in `_statistiche.txt` |
| 339 KB (e 3,0 / 29,3 KB) | «CSS nel body per pagina: mediana, p90, max»: il report non dice di cosa sia il 339, il banco misura il CSS dentro l'HTML reso |

## Censimento (`censimento.php`)

Sola lettura: legge `{prefix}olobuild_templates` (uno alla volta), `{prefix}olobuild_global_widgets`,
le opzioni `olobuild_active_*` e `olobuild_global_popups` e le 208 pagine di `assets/data/themes`
(le stesse chiavi `tema-<tema>--<pagina>` di render-golden, nei due formati); ogni query che scrive
è bloccata. `src=db|themes|all` (predefinito `all`; `SRC=` con banco.sh).

Le pagine dei temi contano quanto i template: `import_theme()` le salva così come sono
(`create_template`, senza fondere i default), quindi su ogni sito che importa un tema una chiave
che lì manca prende il `$defaults` PHP — anche se su mosaic quel tema non è importato. Per questo
a), b) e c) danno i conteggi anche per fonte (`db`, `gw`, `tema`): 0.3 cambia un `$defaults` solo se
nessun nodo, di nessuna fonte, manca della chiave; E1 guarda `auto` in tutte le fonti.

- **a** `section.padding` per parola; `non_riconosciute` = fuori da remove-vertical, small,
  default, large, xlarge, custom (nelle pagine dei temi, fonte `tema`: `none` 22, `medium` 2, che
  rendono il predefinito).
- **b** per ogni (tipo, chiave dei default PHP — e JS con `js=`) i nodi e i template **senza** la
  chiave: lì il renderer usa il `$defaults` PHP, e cambiarlo cambia la resa. Con `js=`: tutte le
  divergenze PHP↔JS (valore, solo PHP, solo JS). Sempre: i candidati noti di 0.3 (button,
  hero, alert, headline, icon). Le «14 divergenze» del report non sono elencate da nessuna
  parte: qui si calcolano. Esempio dalle pagine dei temi: i 34 nodi `button` sono tutti senza
  `padding_x` e `padding_y`.
- **c** chiavi di spazio e raggio (`padding|margin|radius|gap|spacing`, anche nei lati, in
  `style.hover` e nelle voci dei ripetitori: `settings.<ripetitore>[].<chiave>`) a `''` o `'auto'`;
  `auto_su_campo_spazio_raggio` non vuoto = cambiare sentinella prima di E1.
- **d** annidati: `templateembed.template_id`, `*_template_id` (widget_template_id,
  loop_template_id), `widget_id`, `panel_templates` del megamenu, `global_id`, shortcode
  `[olo_template|olobuild_template|mosaic_template id=…]` nei testi; archi, profondità, cicli,
  destinazioni mancanti, radici (template attivi, popup globali).
- **e** `tl_density` per valore.
- **f** nodi e template con chiavi `_tablet|_tablet_landscape|_mobile|_mobile_landscape|_widescreen`
  non vuote (settings e style, anche nelle voci dei ripetitori), `style.hover` e settings `*_hover`
  non vuoti (per B1).
- **g** uso dei tipi.

## Limiti noti

- `WP_CLI::runcommand` non ripete `-d memory_limit`: il figlio lo reimposta con `mem=`.
- Una tile che scrive su file (non su DB) nel render non è bloccata: la vede solo il controllo di
  `uploads/olobuild-cache` e, per il resto, il confronto dei file del sito.
- `hello-olobuild` in L2 è il tema a blocchi vero, se installato; senza, `wp_theme_preview` lo ignora:
  lo scatto non si fa e il manifest ha l'errore «tema non applicato» (uscita 1).
