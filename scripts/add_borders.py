#!/usr/bin/env python3
"""
Aggiunge il border system a tutti i tile PHP e JS che mancano ancora.
"""

import re, os

TILES_DIR = r'D:\TECNICA\olobuild\includes\tiles'
JS_DIR    = r'D:\TECNICA\olobuild\src\config\elements'

# ---- Tile da saltare (layout/utility, nessun wrapper visivo) ----
PHP_SKIP = {
    'class-column-tile.php', 'class-row-tile.php', 'class-section-tile.php',
    'class-inner-columns-tile.php', 'class-spacer-tile.php',
    'class-menuanchor-tile.php', 'class-killnextprev-tile.php',
    'class-fragment-tile.php', 'class-templateembed-tile.php',
    'class-shortcode-tile.php', 'class-html-tile.php',
    'class-queryloop-tile.php', 'class-grid-tile.php',
    'class-darkmode-tile.php', 'class-scrollprogress-tile.php',
    'class-tile-base.php',
}
JS_SKIP = {
    'column.js', 'row.js', 'section.js', 'inner-columns.js',
    'spacer.js', 'menuanchor.js', 'killnextprev.js',
    'fragment.js', 'templateembed.js', 'shortcode.js', 'html.js',
    'queryloop.js', 'grid.js', 'darkmode.js', 'scrollprogress.js',
    '_shared.js',
}

# ---- Defaults PHP da inserire ----
PHP_BORDER_DEFAULTS = """\
        'border'                  => [],
        'border_hover'            => [],
        'border_hover_duration'   => 300,
        'border_effect'           => 'none',
        'border_effect_intensity' => 'medium',
        'border_effect_color2'    => '',
        'border_effect_angle'     => 135,
        'border_effect_speed'     => 4,
"""

# ---- Rilevamento uid variabile ----
UID_PATTERN = re.compile(
    r'\$([a-z_]*(?:uid|id))\s*=\s*[\'"][a-z0-9_\-]+[\'"]',
    re.IGNORECASE
)

def detect_uid_var(content):
    """Rileva il nome della variabile uid nel metodo render()."""
    # Cerca dentro render()
    render_match = re.search(r'public function render\s*\(.*?\{(.*)', content, re.DOTALL)
    if not render_match:
        return None
    render_body = render_match.group(1)[:15000]  # prime 15000 char del render
    m = UID_PATTERN.search(render_body)
    if m:
        return '$' + m.group(1)
    return None

def find_defaults_end(content):
    """Trova la posizione del ]; che chiude protected $defaults."""
    start = content.find('protected $defaults = [')
    if start == -1:
        return -1
    depth = 0
    i = content.find('[', start)
    while i < len(content):
        c = content[i]
        if c == '[':
            depth += 1
        elif c == ']':
            depth -= 1
            if depth == 0:
                # trova il ; subito dopo
                j = i + 1
                while j < len(content) and content[j] in ' \t':
                    j += 1
                if j < len(content) and content[j] == ';':
                    return i  # posizione del ]
        i += 1
    return -1

def php_border_block(uid_var):
    v = uid_var  # es. '$uid' oppure '$alert_uid'
    # Le f-string Python: {{ = { e }} = } letterali
    return (
        "        // Border system\n"
        "        $border_css        = $this->build_border_css( $s['border'] ?? [] );\n"
        f"        $border_hover_css  = $this->build_border_hover_css( \".{{{v}}}\", $s['border'] ?? [], $s['border_hover'] ?? [], intval( $s['border_hover_duration'] ?? 300 ) );\n"
        f"        $border_effect_css = $this->build_border_effect_css( \".{{{v}}}\", $s['border'] ?? [], $s );\n"
        "        if ( $border_css || $border_hover_css || $border_effect_css ) {\n"
        "            echo '<style>';\n"
        f"            if ( $border_css ) echo \".{{{v}}}{{{{$border_css}}}}\";\n"
        "            echo $border_hover_css . $border_effect_css . '</style>';\n"
        "        }\n"
    )

def process_php(filepath):
    filename = os.path.basename(filepath)
    if filename in PHP_SKIP:
        return 'skip'
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    if 'build_border_css' in content:
        return 'already_done'
    if 'protected $defaults' not in content:
        return 'no_defaults'
    if 'ob_get_clean' not in content and 'return ob_get_clean' not in content:
        return 'no_obgetclean'

    uid_var = detect_uid_var(content)
    if not uid_var:
        return 'no_uid'

    # 1. Aggiungi border defaults alla fine dell'array $defaults
    pos = find_defaults_end(content)
    if pos == -1:
        return 'no_defaults_end'
    # Inserisce prima del ]
    content = content[:pos] + PHP_BORDER_DEFAULTS + "    " + content[pos:]

    # 2. Aggiungi border block prima dell'ultima return ob_get_clean()
    # Cerca l'ultima occorrenza
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1:
        return 'no_return_obgetclean'
    border_block = php_border_block(uid_var)
    content = content[:last_return] + border_block + "        " + content[last_return:]

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    return f'ok:{uid_var}'

# ---- JS ----
JS_BORDER_DEFAULTS = """\
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,"""

def update_js_import(content, filepath):
    """Aggiunge/aggiorna l'import di borderFields da _shared.js."""
    needed = "borderFields, borderDefault, borderHoverDefault, borderEffectDefaults"

    # Controlla se esiste già import da _shared
    shared_import = re.search(
        r"import\s*\{([^}]*)\}\s*from\s*['\"]\./_shared(?:\.js)?['\"]",
        content
    )
    if shared_import:
        existing = shared_import.group(1)
        # Aggiungi quelli mancanti
        exports = [e.strip() for e in existing.split(',')]
        to_add = [x.strip() for x in needed.split(',') if x.strip() not in exports]
        if not to_add:
            return content  # già completo
        new_imports = ', '.join(exports + to_add)
        old_line = shared_import.group(0)
        new_line = re.sub(r'\{[^}]*\}', '{ ' + new_imports + ' }', old_line)
        return content.replace(old_line, new_line)
    else:
        # Aggiungi import in cima (dopo eventuali altri import)
        last_import = 0
        for m in re.finditer(r'^import\s+.*?;', content, re.MULTILINE):
            last_import = m.end()
        insert_line = f"\nimport {{ {needed} }} from './_shared.js';"
        if last_import:
            return content[:last_import] + insert_line + content[last_import:]
        else:
            return insert_line + '\n' + content

def process_js(filepath):
    filename = os.path.basename(filepath)
    if filename in JS_SKIP:
        return 'skip'
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    if 'borderFields' in content:
        return 'already_done'
    # Deve avere 'defaults' e 'fields'
    if 'defaults' not in content or 'fields' not in content:
        return 'no_defaults_fields'

    # 1. Aggiorna import
    content = update_js_import(content, filepath)

    # 2. Aggiungi border defaults all'oggetto defaults
    # Cerca il pattern: defaults: { ... } o export default { defaults: {
    # Strategia: trova la chiusura dell'oggetto defaults
    def_match = re.search(r'\bdefaults\s*:\s*\{', content)
    if not def_match:
        return 'no_defaults_obj'
    # Conta le parentesi per trovare la chiusura
    start = def_match.end() - 1  # posizione dell'apertura {
    depth = 0
    i = start
    while i < len(content):
        if content[i] == '{':
            depth += 1
        elif content[i] == '}':
            depth -= 1
            if depth == 0:
                # Trovata chiusura; inserisci prima di essa
                # Trova l'ultimo carattere non-whitespace prima della chiusura
                j = i - 1
                while j > 0 and content[j] in ' \t\n\r':
                    j -= 1
                # Aggiungi una virgola se l'ultimo char non è già una virgola/virgola+newline
                last_char = content[j]
                insert_pos = j + 1
                prefix = '' if last_char == ',' else ','
                insert_text = prefix + '\n' + JS_BORDER_DEFAULTS
                content = content[:insert_pos] + insert_text + '\n  ' + content[i:]
                break
        i += 1

    # 3. Aggiungi ...borderFields() alla fine dell'array fields
    # Cerca l'array fields: [ ... ]
    fields_match = re.search(r'\bfields\s*:\s*\[', content)
    if not fields_match:
        return 'no_fields_arr'
    start_f = fields_match.end() - 1
    depth = 0
    i = start_f
    while i < len(content):
        if content[i] == '[':
            depth += 1
        elif content[i] == ']':
            depth -= 1
            if depth == 0:
                j = i - 1
                while j > 0 and content[j] in ' \t\n\r':
                    j -= 1
                last_char = content[j]
                insert_pos = j + 1
                prefix = '' if last_char == ',' else ','
                insert_text = prefix + '\n    ...borderFields(),'
                content = content[:insert_pos] + insert_text + '\n  ' + content[i:]
                break
        i += 1

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    return 'ok'

# ---- Main ----
print("=== PHP Tile Files ===")
php_results = {}
for fname in sorted(os.listdir(TILES_DIR)):
    if not fname.endswith('.php') or not fname.startswith('class-'):
        continue
    fpath = os.path.join(TILES_DIR, fname)
    result = process_php(fpath)
    php_results[result] = php_results.get(result, []) + [fname]
    if result not in ('already_done', 'skip'):
        print(f"  {fname}: {result}")

print("\n=== JS Config Files ===")
js_results = {}
for fname in sorted(os.listdir(JS_DIR)):
    if not fname.endswith('.js'):
        continue
    fpath = os.path.join(JS_DIR, fname)
    result = process_js(fpath)
    js_results[result] = js_results.get(result, []) + [fname]
    if result not in ('already_done', 'skip'):
        print(f"  {fname}: {result}")

print("\n=== Riepilogo PHP ===")
for k, v in sorted(php_results.items()):
    print(f"  {k}: {len(v)} file")
print("\n=== Riepilogo JS ===")
for k, v in sorted(js_results.items()):
    print(f"  {k}: {len(v)} file")

# File con problemi (no_uid, no_obgetclean, ecc.)
problem_keys = [k for k in php_results if not k.startswith('ok') and k not in ('already_done', 'skip', 'no_defaults', 'no_obgetclean')]
if problem_keys:
    print("\n=== PHP File problematici ===")
    for k in problem_keys:
        for f in php_results[k]:
            print(f"  {f}: {k}")
