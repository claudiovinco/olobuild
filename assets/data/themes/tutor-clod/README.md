# Pacchetto template OLOtutor per tutor.clod.eu

Tema importabile OLObuild, composto solo con tile native.

- `theme.json` + 7 template JSON, `logo.png`, `logo-light.png`, `screenshot.jpg`
  vanno in `assets/data/themes/tutor-clod/` del plugin.
- `SPEC-TECNICA.md` e' la specifica: token, tipografia, ritmo, mappa blocco ->
  tile, deviazioni, lista di controllo a 1280.

Testi presi verbatim da `uploads/vetrina-testi.md`.

## Cosa e' cambiato dopo la validazione e il primo collaudo

Il pacchetto e' stato vagliato contro `src/config/elements/` e guardato a 1280.
Le correzioni applicate, e il perche':

| Dove | Cosa | Perche' |
|---|---|---|
| 70 colonne | tolta la chiave `width` | la colonna non ce l'ha: le sue sono `width_default/small/medium/large`, e la larghezza desktop era gia' in `width_medium` |
| header | `alignment` tolto, `layout: horizontal` -> `left` | l'allineamento del menu si chiama `layout` e vale left/center/right |
| header | `social_icons: false` -> `social_in_navbar` e `social_in_mobile` a false | la chiave non esisteva, e le icone sono accese di serie con quattro link segnaposto |
| header | `nav_height: 64` | senza, la barra e' alta quanto il contenuto (38px) e la CTA ci sbatte contro |
| 18 hex | `#2d722d` -> `--olo-color-link`, `#ffffff` -> `--olo-color-background`, `#348634` -> `color-mix` su primary | nei template niente hex |
| home | l'onda passa da `position: bottom` a `top` | il tracciato si chiude sul bordo basso del riquadro: appesa sotto l'hero il pieno galleggia nel bianco, sopra la sezione seguente mangia il fondo chiaro. Il fondo dell'hero passa da 40 a 120 per farle posto |
| 5 righe | `gap: 20` -> `24` | il gap e' quantizzato su classi UIkit: a 1280 valgono 0, 15, 30, 70. Il 20 cadeva su «nessuna classe», cioe' ZERO, e le carte si toccavano |
| 8 occhielli | maiuscoletto, spaziatura .09em e peso 600 nel contenuto | `text-block` non ha ne' `text_transform` ne' `letter_spacing` ne' `font_weight` |
| piede | i due titoli in maiuscoletto e in Work Sans | erano minuscoli e in Playfair |
| come si prova | la fascia navy passa a `style: secondary` | `quotation` non ha nessuna chiave di colore: ereditava #333 su #0f172a, cioe' 1,5:1 |
| lo studio | le due carte col fronte vuoto ricevono il verdetto anche davanti | ferme erano due rettangoli bianchi, e si leggevano come un guasto |
| ovunque | 15 trattini lunghi -> punto mediano | |
| theme.json | tolto `"spacing": {}` | `wp_parse_args` fonde al primo livello: una chiave vuota SOSTITUISCE la scala di chi importa |

## Quello che resta fuori

- **La card del modale «Importa Temi»** arriva dalla libreria remota
  (`olotheme.com/olobuild-library/themes/<id>/screenshot.jpg`), non dal file
  locale. `screenshot.jpg` c'e' ed e' giusto, ma si vede solo quando e'
  pubblicato la' oppure quando `olobuild_library_url` viene filtrata a ''.
- **Le due immagini della home** (schermata del portale docente, foto d'aula)
  sono ancora segnaposto.
- **I mockup della `step-timeline`** non si spengono: `show_media_label: false`
  nasconde solo la barra del titolo, il riquadro si disegna sempre.

## Dopo l'import

Restano da caricare le **due immagini della home**: schermata del portale
docente (340px di altezza) e foto d'aula. Finche' mancano si vedono due
segnaposto scritti.

Lo slug delle pagine invece lo dichiara `theme.json.pages[].slug` e non va piu'
corretto a mano: senza, WordPress lo ricava dal titolo e
`sanitize_title("L'ora di lezione")` da' `lora-di-lezione`, mentre il menu
punta a `/l-ora-di-lezione/`. La chiave e' stata aggiunta all'importer il 1
settembre 2026; chi dichiara uno slug ottiene anche che un secondo import
**riusi** quella pagina invece di farne una col `-2`.
