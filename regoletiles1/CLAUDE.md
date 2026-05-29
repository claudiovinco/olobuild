# OLObuild — regole permanenti per Claude Code

> Metti questo file nella **root del repo** così viene caricato in ogni sessione.

## Tile = belle & coerenti, sempre
Quando tocchi una qualsiasi `src/components/Tiles/*Tile.vue` o `src/config/elements/*.js`,
applica le regole in `D:\TECNICA\olobuild\regoletiles1\DESIGN_LANGUAGE.md` e la checklist
in `D:\TECNICA\olobuild\regoletiles1\TILE_AUDIT_CHECKLIST.md`. Per un lavoro completo su
tutte le tile, segui `D:\TECNICA\olobuild\regoletiles1\START_HERE.md`.

## Regole sempre attive
- **Colori solo via token** (`oloTileDefaults`: GLOBAL ruoli cliente / SYSTEM fissi) +
  `resolveColor()`. Mai hex hardcoded (`#6366F1`, `#1e87f0`, `#e8622a`…). Primario = rosso
  brand `#e1474f` via `--olo-color-primary`.
- **Box-model** via `useBoxModel` (radius/padding/margin). **Default** da fonte unica.
- **Icone** dal set SVG, mai emoji. **Focus-visible** su ogni elemento interattivo.
- **Scale condivise** SPACE (8pt) e RADIUS. Una tile = un raggio, una lingua d'ombra.
- **Chiavi salvate INVARIATE**: cambia la UI/resa, non il formato dei dati.
- Non inventare nomi `--olo-color-*` che il GlobalColorsPanel non genera (vedi TOKEN_MAPPING).

> Il pacchetto regole vive in `D:\TECNICA\olobuild\regoletiles1\`.
