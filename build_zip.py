"""
Build installable ZIP for a WordPress plugin.
Creates a ZIP with Linux-style forward-slash paths, safe for WordPress install.

Usage: python build_zip.py [plugin_name] [--root=PATH]
If plugin_name is omitted, defaults to 'olobuild'.
--root overrides the source folder (e.g. a clean staging export of git HEAD)
while keeping plugin_name as the ZIP root folder and output name.
"""
import os
import sys
import zipfile
from pathlib import Path

_positional = [a for a in sys.argv[1:] if not a.startswith("--")]
_root_args = [a.split("=", 1)[1] for a in sys.argv[1:] if a.startswith("--root=")]
PLUGIN = _positional[0] if _positional else "olobuild"
PROJECT_ROOT = Path(_root_args[0]) if _root_args else Path(f"D:/TECNICA/{PLUGIN}")
OUTPUT_DIR = Path("D:/TECNICA/olobuild/dist")

# Read version from main PHP file
main_php = PROJECT_ROOT / f"{PLUGIN}.php"
version = "0.0.0"
if main_php.exists():
    with open(main_php, "r", encoding="utf-8") as f:
        for line in f:
            if line.strip().startswith("* Version:"):
                version = line.split(":", 1)[1].strip()
                break
OUTPUT_ZIP = OUTPUT_DIR / f"{PLUGIN}-{version}.zip"

# Exclusions
EXCLUDE_DIRS = {
    "node_modules", ".git", ".claude", "tmp_deploy", "dist",
    "docs", ".vscode", ".idea", "__pycache__",
    "tmp_handoff", "scripts",
    # cartelle di lavoro che NON devono finire nel plugin distribuito
    "regoletiles1", "audit_results", "handoff-tile-speciali",
    "iotfarm-demo", "bordo",
    # scratch: contiene solo un residuo build/olosecurity/ (vecchia copia con
    # header "Plugin Name:" → farebbe abortire il safety-check secondo-header)
    "build",
}
# Qualsiasi cartella che inizia con questi prefissi viene esclusa.
# "tmp_" → tmp_try_build/tmp_try_ref/ecc.; "_" → _legacy/_backup; più le cartelle di lavoro note.
EXCLUDE_DIR_PREFIXES = (
    "design_handoff_", "design-", "languages._backup_",
    "tmp_", "_", "handoff", "regoletiles", "audit_", "iotfarm",
    "NOVA", "OLObuild design",
)
EXCLUDE_FILES = {
    "CLAUDE.md", ".gitignore", ".gitattributes", ".editorconfig",
    "package.json", "package-lock.json", "yarn.lock",
    "vite.config.js", "vite.config.admin.js", "vite.picker.config.js",
    "tailwind.config.js", "postcss.config.js",
    "composer.json", "composer.lock",
    "_build.cjs", "check_config.py", "build_zip.py",
    ".DS_Store", "Thumbs.db",
    "ROADMAP.md", "OLOBUILD-SCHEDA.md", "TILE_PROGRESS.md",
    "add_borders.py", "add_borders_special.py",
}
# .md non serve nel plugin distribuito (il riferimento è readme.txt); idem file di lavoro
EXCLUDE_EXT = {".log", ".po~", ".bak", ".tmp", ".swp", ".tsv", ".cjs", ".zip", ".md"}
# test-*.php, BRIEF_*.md, AUDIT-*.md, HERO_AUDIT_*.json, tmp_*, _tmp*, _b.txt, HANDOFF*
EXCLUDE_PATTERN_PREFIXES = (
    "test-", "BRIEF_", "AUDIT-", "AUDIT_", "HERO_AUDIT", "HANDOFF",
    "tmp_", "_tmp", "_",
)
# Exclude files with .bak somewhere in the name
def is_backup_file(name: str) -> bool:
    return ".bak" in name

# Include src folder: required by wordpress.org so the minified builder.js bundle
# has its readable, non-compiled sources available for review.
INCLUDE_SRC = True

OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

if not PROJECT_ROOT.exists():
    print(f"ERROR: project folder not found: {PROJECT_ROOT}")
    sys.exit(1)

print(f"Building {PLUGIN}-{version}.zip...")
print(f"Source: {PROJECT_ROOT}")
print(f"Output: {OUTPUT_ZIP}")

# Build file list
print("\n[1/2] Collecting files...")
files_to_zip = []
total_size = 0

for root, dirs, files in os.walk(PROJECT_ROOT):
    root_path = Path(root)
    # Prune excluded dirs in-place
    dirs[:] = [
        d for d in dirs
        if d not in EXCLUDE_DIRS
        and not d.startswith(".")
        and not any(d.startswith(p) for p in EXCLUDE_DIR_PREFIXES)
    ]
    if not INCLUDE_SRC and root_path == PROJECT_ROOT:
        dirs[:] = [d for d in dirs if d != "src"]

    for filename in files:
        if filename in EXCLUDE_FILES:
            continue
        if any(filename.startswith(p) for p in EXCLUDE_PATTERN_PREFIXES):
            continue
        if Path(filename).suffix in EXCLUDE_EXT:
            continue
        if filename.startswith(".") and filename not in (".htaccess",):
            continue
        if is_backup_file(filename):
            continue

        abs_path = root_path / filename
        rel_path = abs_path.relative_to(PROJECT_ROOT)

        # Use forward slashes (Linux style)
        arc_name = f"{PLUGIN}/" + rel_path.as_posix()

        files_to_zip.append((abs_path, arc_name))
        total_size += abs_path.stat().st_size

print(f"  Files to archive: {len(files_to_zip)}")
print(f"  Total source size: {total_size / 1024 / 1024:.1f} MB")

# Sanity check
main_in_zip = f"{PLUGIN}/{PLUGIN}.php"
if not any(arc == main_in_zip for _, arc in files_to_zip):
    print(f"ERROR: main file {main_in_zip} not included!")
    sys.exit(1)

# Safety: nessun SECONDO header di plugin oltre al main — un altro file con
# "Plugin Name:" nell'header fa fallire l'installazione con
# "Il plugin ha un'intestazione non valida".
offenders = []
for abs_path, arc in files_to_zip:
    if arc.endswith(".php") and arc != main_in_zip:
        try:
            with open(abs_path, "r", encoding="utf-8", errors="ignore") as fh:
                head = fh.read(4096)
            if "Plugin Name:" in head:
                offenders.append(arc)
        except OSError:
            pass
if offenders:
    print("ERROR: trovato un secondo header di plugin (causa 'intestazione non valida'):")
    for o in offenders:
        print(f"   - {o}")
    print("Aggiungi la cartella/file alle esclusioni e riprova.")
    sys.exit(1)

# Create ZIP
print(f"\n[2/2] Creating ZIP...")
if OUTPUT_ZIP.exists():
    OUTPUT_ZIP.unlink()

with zipfile.ZipFile(OUTPUT_ZIP, "w", zipfile.ZIP_DEFLATED, compresslevel=6) as zf:
    for abs_path, arc_name in files_to_zip:
        zf.write(abs_path, arc_name)

zip_size = OUTPUT_ZIP.stat().st_size
print(f"  Output: {OUTPUT_ZIP}")
print(f"  ZIP size: {zip_size / 1024 / 1024:.2f} MB")
print(f"  Compression: {100 - (zip_size * 100 / total_size):.1f}%")

# Verification
print("\n[VERIFY] Structure:")
with zipfile.ZipFile(OUTPUT_ZIP, "r") as zf:
    names = zf.namelist()
    top_dirs = sorted(set(n[len(PLUGIN)+1:].split("/")[0] for n in names if "/" in n[len(PLUGIN)+1:]))
    top_files = sorted([n[len(PLUGIN)+1:] for n in names if "/" not in n[len(PLUGIN)+1:] and n != f"{PLUGIN}/"])
    print(f"  Main PHP: {main_in_zip in names}")
    print(f"  Top-level dirs: {', '.join(top_dirs)}")
    print(f"  Total entries: {len(names)}")

print("\nDone!")
