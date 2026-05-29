#!/bin/bash
# Esporta .po/.mo di olobuild + olo-lang da wp_olo_lang_* su mosaic
set -e
OUT=/tmp/olo-po
rm -rf "$OUT" && mkdir -p "$OUT/olobuild" "$OUT/olo-lang"

WP="php -d memory_limit=512M /usr/local/bin/wp"
PATH_WP=/var/www/wordpress

# Mappatura codice lingua → locale WP
pairs="cs:cs_CZ de:de_DE en:en_US es:es_ES fr:fr_FR hu:hu_HU ja:ja nl:nl_NL pl:pl_PL pt:pt_BR ru:ru_RU"

for pair in $pairs; do
  lang="${pair%%:*}"
  loc="${pair##*:}"
  for dom in olobuild olo-lang; do
    $WP olo-lang export-po --domain="$dom" --lang="$lang" \
      --out="$OUT/$dom/$dom-$loc.po" --path="$PATH_WP" --allow-root --quiet 2>/dev/null || true
    $WP olo-lang export-mo --domain="$dom" --lang="$lang" \
      --out="$OUT/$dom/$dom-$loc.mo" --path="$PATH_WP" --allow-root --quiet 2>/dev/null || true
  done
done

echo "---olobuild---"
ls -la "$OUT/olobuild"
echo "---olo-lang---"
ls -la "$OUT/olo-lang"
