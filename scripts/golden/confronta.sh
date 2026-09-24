#!/usr/bin/env bash
# confronta.sh — confronta due snapshot di render-golden.php (banco compatto, passo 0.1).
#
#   bash confronta.sh <snapshot A> <snapshot B>
#
# Confronta gli md5 dell'HTML normalizzato (_summary.tsv) chiave per chiave e le righe
# dello Style System (_style-system.css, V4: fra due fasi sono ammesse solo aggiunte).
# Per ogni template cambiato dice anche se sono cambiati i DATI (_dati.tsv): «dati» =
# qualcuno ha modificato il template, «resa» = a dati uguali è cambiato l'HTML.
#
# Una chiave NON resa in B (stato «fatale», md5 vuoto, o «errore» con un messaggio diverso
# da quello di A) non conta mai come identica: due fatali hanno lo stesso md5 vuoto, e un
# banco che non rende niente passerebbe il confronto. Per le pagine dei temi (tema-*) anche
# «vuoto» è non reso: nessuna delle pagine JSON è vuota, quindi il renderer non ha letto la
# riga finta nel gruppo di cache 'olo'. Lo stesso errore, con lo stesso messaggio in A e in
# B, è ammesso (resta segnalato). ACCETTA=chiave1,chiave2 ammette anche i fatali (e le
# pagine vuote) noti, purché in A fossero nello stesso stato. Se in B nessuna chiave è
# «ok», il confronto non vale.
#
# B parziale (reso con SOLO=…: render-golden.php scrive _solo.txt): si confrontano solo le
# chiavi di B, e quelle di A che mancano in B sono contate come «non confrontate», non come
# differenze.
#
# Uscita: 0 = identici · 1 = differenze o chiavi non rese (elencate) · 2 = uso sbagliato o
# snapshot incompleto (manca _summary.tsv, o _style-system.css e V4 non si verifica).
# Gira con bash + awk (anche in Git Bash su Windows).
set -u
export LC_ALL=C

A=${1:-}
B=${2:-}
if [ -z "$A" ] || [ -z "$B" ] || [ ! -f "$A/_summary.tsv" ] || [ ! -f "$B/_summary.tsv" ]; then
  echo "Uso: confronta.sh <snapshot A> <snapshot B>  (cartelle con _summary.tsv di render-golden.php)" >&2
  exit 2
fi

# chiave<TAB>md5<TAB>stato<TAB>volatili<TAB>errore da _summary.tsv, cercando le colonne per nome.
estrai() {
  awk -F'\t' 'NR == 1 { for (i = 1; i <= NF; i++) c[$i] = i; next }
    { print $(c["chiave"]) "\t" $(c["md5"]) "\t" (("stato" in c) ? $(c["stato"]) : "") "\t" (("volatili" in c) ? $(c["volatili"]) : "") "\t" (("errore" in c) ? $(c["errore"]) : "") }' "$1/_summary.tsv"
}
# chiave<TAB>impronta dei dati da _dati.tsv (se c'è).
dati() {
  if [ -f "$1/_dati.tsv" ]; then
    awk -F'\t' 'NR == 1 { for (i = 1; i <= NF; i++) c[$i] = i; next } { print $(c["chiave"]) "\t" $(c["impronta"]) }' "$1/_dati.tsv"
  fi
}

TMP=$(mktemp -d 2>/dev/null || mktemp -d -t olo-golden)
trap 'rm -rf "$TMP"' EXIT
estrai "$A" > "$TMP/a"
estrai "$B" > "$TMP/b"
dati "$A" > "$TMP/da"
dati "$B" > "$TMP/db"

PARZIALE=0
[ -f "$B/_solo.txt" ] && PARZIALE=1

awk -F'\t' -v DA="$TMP/da" -v DB="$TMP/db" -v ACCETTA="${ACCETTA:-}" -v PARZIALE="$PARZIALE" '
  BEGIN {
    while ((getline l < DA) > 0) { split(l, x, "\t"); da[x[1]] = x[2] }
    while ((getline l < DB) > 0) { split(l, x, "\t"); db[x[1]] = x[2] }
    na = split(ACCETTA, x, ","); for (i = 1; i <= na; i++) if (x[i] != "") accetta[x[i]] = 1
  }
  FNR == NR { ma[$1] = $2; sa[$1] = $3; ea[$1] = $5; ordine[++n] = $1; next }
  { mb[$1] = $2; sb[$1] = $3; vb[$1] = $4; eb[$1] = $5; if (!($1 in ma)) solob[++nb] = $1 }
  END {
    cambiati = 0; soloa = 0; rotti = 0; noti = 0; resi = 0; nonconf = 0
    for (i = 1; i <= n; i++) {
      k = ordine[i]
      if (!(k in mb) && PARZIALE == 1) { nonconf++; continue }
      if (!(k in mb)) { print "solo in A      " k; soloa++; continue }
      if (ma[k] != mb[k]) {
        perche = "resa"
        if ((k in da) && (k in db) && da[k] != db[k]) perche = "dati"
        else if (!(k in da) || !(k in db)) perche = "?"
        printf "cambiato (%s)  %s%s\n", perche, k, (vb[k] != "" ? "   [volatile: " vb[k] "]" : "")
        cambiati++
      }
    }
    for (i = 1; i <= nb; i++) print "solo in B      " solob[i]
    for (i = 1; i <= n + nb; i++) {
      k = (i <= n) ? ordine[i] : solob[i - n]
      if (!(k in sb)) continue
      if (sb[k] == "ok") resi++
      # una pagina di tema «vuota» non è legittima: la riga finta nella cache non è stata letta
      tema_vuota = (sb[k] == "vuoto" && k ~ /^tema-/)
      if (sb[k] != "errore" && sb[k] != "fatale" && mb[k] != "" && !tema_vuota) continue
      stato = (sb[k] != "") ? sb[k] : "senza md5"
      # stesso errore (stesso messaggio) in A, oppure fatale noto elencato in ACCETTA
      if ((k in sa) && sa[k] == sb[k] && ((sb[k] == "errore" && ea[k] == eb[k]) || (k in accetta))) {
        print "attenzione     " k " è " stato " in B, come in A" (eb[k] != "" ? ": " eb[k] : ""); noti++
      } else {
        print "non reso       " k " è " stato " in B" (eb[k] != "" ? ": " eb[k] : ""); rotti++
      }
    }
    printf "\nchiavi: %d in A, %d in B · cambiate %d · solo in A %d · solo in B %d · non rese in B %d · errori ammessi %d · ok in B %d\n", n, n - soloa - nonconf + nb, cambiati, soloa, nb, rotti, noti, resi
    if (PARZIALE == 1) printf "B è parziale (_solo.txt, reso con SOLO=…): confrontate solo le sue chiavi, %d chiavi di A non confrontate\n", nonconf
    if (resi == 0) print "nessuna chiave resa («ok») in B: il confronto non vale"
    esito = (cambiati + soloa + nb + rotti > 0 || resi == 0) ? 1 : 0
    exit esito
  }' "$TMP/a" "$TMP/b"
esito=$?

# V4 — Style System: una riga di A che manca in B è una riga tolta o cambiata.
if [ -f "$A/_style-system.css" ] && [ -f "$B/_style-system.css" ]; then
  # Multinsiemi, non insiemi: una riga presente due volte in A (lo stesso token nel blocco chiaro
  # e in html.olo-dark-mode) va ritrovata due volte in B, altrimenti una delle due è cambiata.
  tolte=$(awk 'FNR == NR { b[$0]++; next } { if (b[$0] > 0) b[$0]--; else print }' "$B/_style-system.css" "$A/_style-system.css")
  aggiunte=$(awk 'FNR == NR { a[$0]++; next } { if (a[$0] > 0) a[$0]--; else print }' "$A/_style-system.css" "$B/_style-system.css" | grep -c . || true)
  if [ -n "$tolte" ]; then
    echo
    echo "Style System: righe di A tolte o cambiate in B (V4 vuole solo aggiunte):"
    printf '%s\n' "$tolte" | head -40 | sed 's/^/  - /'
    esito=1
  fi
  echo "Style System: $aggiunte righe aggiunte in B"
else
  echo "Style System: _style-system.css mancante in uno dei due snapshot (V4 non verificato: snapshot incompleto)"
  esito=2
fi

if [ "$esito" -eq 0 ]; then
  echo "IDENTICI"
elif [ "$esito" -eq 2 ]; then
  echo "NON VERIFICATO"
else
  echo "DIVERSI"
fi
exit "$esito"
