#!/usr/bin/env python3
"""
Gestisce i tile PHP speciali rimasti senza border system.
"""
import re, os

TILES_DIR = r'D:\TECNICA\olobuild\includes\tiles'

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

def php_border_block(uid_var):
    v = uid_var
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

def find_defaults_end(content):
    start = content.find('protected $defaults = [')
    if start == -1: return -1
    depth = 0
    i = content.find('[', start)
    while i < len(content):
        c = content[i]
        if c == '[': depth += 1
        elif c == ']':
            depth -= 1
            if depth == 0: return i
        i += 1
    return -1

def add_border_defaults(content):
    pos = find_defaults_end(content)
    if pos == -1: return content
    return content[:pos] + PHP_BORDER_DEFAULTS + "    " + content[pos:]

# ---------------------------------------------------------------
# 1. NEWSLETTER — uid esiste già, return usa $html
# ---------------------------------------------------------------
def fix_newsletter():
    path = os.path.join(TILES_DIR, 'class-newsletter-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    content = add_border_defaults(content)

    # Inserisci border block PRIMA di "return $html;"
    block = php_border_block('$uid')
    content = content.replace(
        '        return $html;\n    }\n}',
        block + '        return $html;\n    }\n}'
    )
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# Helper: aggiungi uid a tile che usano ob_start+ob_get_clean
# ---------------------------------------------------------------
def add_uid_to_tile(filename, uid_prefix, uid_var, wrapper_class, wp_func='wp_rand( 10000, 99999 )'):
    """
    Aggiunge:
    1. $uid_var = 'prefix-' . wp_func; dopo la wp_parse_args
    2. uid class al wrapper
    3. border defaults + border block
    """
    path = os.path.join(TILES_DIR, filename)
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # 1. Aggiungi uid dopo wp_parse_args oppure all'inizio di render()
    parse_args = content.find('$s   = wp_parse_args')
    if parse_args == -1:
        parse_args = content.find('$s = wp_parse_args')
    if parse_args == -1:
        # cerca inizio render
        parse_args = content.find('public function render')
    # Trova fine riga
    eol = content.find('\n', parse_args)
    uid_line = f"\n        {uid_var} = '{uid_prefix}' . {wp_func};"
    content = content[:eol] + uid_line + content[eol:]

    # 2. Aggiungi uid class al wrapper HTML
    content = content.replace(
        f'class="{wrapper_class}"',
        f'class="{wrapper_class} <?php echo esc_attr( {uid_var} ); ?>"',
        1  # solo prima occorrenza
    )
    # Prova anche con la variante in PHP inline
    content = content.replace(
        f'class="olo-{wrapper_class.replace("olo-","")}"',
        f'class="olo-{wrapper_class.replace("olo-","")}" <?php echo esc_attr( {uid_var} ); ?>',
        1
    )

    # 3. Aggiungi border defaults
    content = add_border_defaults(content)

    # 4. Aggiungi border block prima di return ob_get_clean()
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block(uid_var)
    content = content[:last_return] + block + "        " + content[last_return:]

    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 2. ICONLIST
# ---------------------------------------------------------------
def fix_iconlist():
    path = os.path.join(TILES_DIR, 'class-iconlist-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # Trova inizio render e aggiungi uid dopo $s
    parse_args_pos = content.find('$s = wp_parse_args')
    if parse_args_pos == -1: parse_args_pos = content.find('public function render')
    eol = content.find('\n', parse_args_pos)
    uid_line = "\n        $uid = 'olo-il-' . wp_rand( 10000, 99999 );"
    content = content[:eol] + uid_line + content[eol:]

    # Aggiungi uid class alla prima div wrapper
    content = content.replace(
        '<div class="olo-iconlist"',
        '<div class="olo-iconlist <?php echo esc_attr( $uid ); ?>"',
        1
    )
    content = add_border_defaults(content)
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 3. VIEWSCOUNTER
# ---------------------------------------------------------------
def fix_viewscounter():
    path = os.path.join(TILES_DIR, 'class-viewscounter-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # Aggiungi uid dopo wp_parse_args
    pa_pos = content.find('wp_parse_args')
    eol = content.find('\n', pa_pos)
    uid_line = "\n        $uid = 'olo-vc-' . wp_rand( 10000, 99999 );"
    content = content[:eol] + uid_line + content[eol:]

    content = content.replace(
        '<div class="olo-viewscounter"',
        '<div class="olo-viewscounter <?php echo esc_attr( $uid ); ?>"',
        1
    )
    content = add_border_defaults(content)
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 4. FACEBOOKPAGE
# ---------------------------------------------------------------
def fix_facebookpage():
    path = os.path.join(TILES_DIR, 'class-facebookpage-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    pa_pos = content.find('wp_parse_args')
    eol = content.find('\n', pa_pos)
    uid_line = "\n        $uid = 'olo-fb-' . wp_rand( 10000, 99999 );"
    content = content[:eol] + uid_line + content[eol:]

    content = content.replace(
        '<div class="olo-facebookpage"',
        '<div class="olo-facebookpage <?php echo esc_attr( $uid ); ?>"',
        1
    )
    content = add_border_defaults(content)
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 5. TOTOP
# ---------------------------------------------------------------
def fix_totop():
    path = os.path.join(TILES_DIR, 'class-totop-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    pa_pos = content.find('wp_parse_args')
    eol = content.find('\n', pa_pos)
    uid_line = "\n        $uid = 'olo-tt-' . wp_rand( 10000, 99999 );"
    content = content[:eol] + uid_line + content[eol:]

    content = content.replace(
        '<div class="olo-totop',
        '<div class="olo-totop <?php echo esc_attr( $uid ); ?>',
        1
    )
    content = add_border_defaults(content)
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 6. POSTMETA
# ---------------------------------------------------------------
def fix_postmeta():
    path = os.path.join(TILES_DIR, 'class-postmeta-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    pa_pos = content.find('wp_parse_args')
    eol = content.find('\n', pa_pos)
    uid_line = "\n        $uid = 'olo-pm-' . wp_rand( 10000, 99999 );"
    content = content[:eol] + uid_line + content[eol:]

    content = content.replace(
        '<div class="olo-postmeta"',
        '<div class="olo-postmeta <?php echo esc_attr( $uid ); ?>"',
        1
    )
    content = add_border_defaults(content)
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 7. SWITCHER (multiple render paths, tutte chiuse da ob_get_clean)
# ---------------------------------------------------------------
def fix_switcher():
    path = os.path.join(TILES_DIR, 'class-switcher-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # Aggiungi uid dopo $count check
    # Trova la prima riga dopo "if ( $count === 0 )"
    zero_pos = content.find('if ( $count === 0 )')
    if zero_pos == -1: zero_pos = content.find('ob_start()')
    eol = content.find('\n', zero_pos)
    # Cerca la riga successiva che non sia la early return
    eol2 = content.find('\n', eol + 1)
    while content[eol2+1:eol2+13].strip() in ('return', ''):
        eol2 = content.find('\n', eol2 + 1)
        if eol2 > zero_pos + 500: break
    # Aggiungi uid prima di ob_start
    ob_pos = content.find('ob_start()')
    uid_line = "        $uid = 'olo-sw-' . wp_rand( 10000, 99999 );\n"
    content = content[:ob_pos] + uid_line + content[ob_pos:]

    # Aggiungi uid class a ENTRAMBE le div wrapper (verticale + orizzontale)
    content = content.replace(
        '<div class="olo-switcher"',
        '<div class="olo-switcher <?php echo esc_attr( $uid ); ?>"'
    )  # replace_all - entrambe le occorrenze
    content = add_border_defaults(content)
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 8. DIVIDER (usa echo direttamente, non ob_start)
# ---------------------------------------------------------------
def fix_divider():
    path = os.path.join(TILES_DIR, 'class-divider-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # Aggiungi uid prima di ob_start
    ob_pos = content.find('ob_start()')
    uid_line = "        $uid = 'olo-divider-' . wp_rand( 10000, 99999 );\n"
    content = content[:ob_pos] + uid_line + content[ob_pos:]

    # Aggiungi uid class alla prima div wrapper
    content = content.replace(
        "echo '<div class=\"olo-divider\"",
        "echo '<div class=\"olo-divider ' . esc_attr( $uid ) . '\"",
        1
    )
    content = add_border_defaults(content)
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 9. SOUNDCLOUD (ha più percorsi render)
# ---------------------------------------------------------------
def fix_soundcloud():
    path = os.path.join(TILES_DIR, 'class-soundcloud-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # Aggiungi uid prima del primo ob_start
    first_ob = content.find('ob_start()')
    uid_line = "        $uid = 'olo-sc-' . wp_rand( 10000, 99999 );\n"
    content = content[:first_ob] + uid_line + content[first_ob:]

    # Aggiungi uid class a tutte le div wrapper olo-soundcloud
    content = content.replace(
        '<div class="olo-soundcloud"',
        '<div class="olo-soundcloud <?php echo esc_attr( $uid ); ?>"'
    )
    # Wrapper con classi extra (es olo-soundcloud olo-sc-hr-xxx)
    content = content.replace(
        '<div class="olo-soundcloud<?php echo',
        '<div class="olo-soundcloud <?php echo esc_attr( $uid ); ?><?php echo',
    )

    content = add_border_defaults(content)
    # Aggiungi border block prima dell'ULTIMA return ob_get_clean()
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 10. LIST (usa <ul>, avvolgo in wrapper div)
# ---------------------------------------------------------------
def fix_list():
    path = os.path.join(TILES_DIR, 'class-list-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # Aggiungi uid prima di ob_start
    ob_pos = content.find('ob_start()')
    if ob_pos == -1: return 'no_ob_start'
    uid_line = "        $uid = 'olo-list-' . wp_rand( 10000, 99999 );\n"
    content = content[:ob_pos] + uid_line + content[ob_pos:]

    content = add_border_defaults(content)
    # La list non ha un wrapper div, quindi aggiungiamo il border CSS con un selettore
    # che usi la classe generica — ma per istanza-specifica usiamo uid sulla ul
    content = content.replace(
        '<ul class="olo-list',
        '<ul class="olo-list <?php echo esc_attr( $uid ); ?>',
        1
    )
    last_return = content.rfind('return ob_get_clean()')
    if last_return == -1: return 'no_return'
    block = php_border_block('$uid')
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 11. LIGHTBOX (nessun $defaults, usa $html string building)
# ---------------------------------------------------------------
def fix_lightbox():
    path = os.path.join(TILES_DIR, 'class-lightbox-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    # Aggiungi uid e aggiungilo al wrapper
    # $html = '<div class="olo-lightbox-grid"' ...
    render_start = content.find('public function render')
    items_line = content.find('$items   =', render_start)
    if items_line == -1: items_line = content.find('$items =', render_start)
    eol = content.find('\n', items_line)
    uid_line = "\n        $uid = 'olo-lb-' . wp_rand( 10000, 99999 );"
    content = content[:eol] + uid_line + content[eol:]

    content = content.replace(
        "'<div class=\"olo-lightbox-grid\"",
        "'<div class=\"olo-lightbox-grid ' . esc_attr( $uid ) . '\"",
        1
    )
    # Aggiungi border block prima di return $html;
    block = (
        "        $border_css        = $this->build_border_css( [] );\n"
        "        $border_hover_css  = $this->build_border_hover_css( '.$uid', [], [], 300 );\n"
        "        $border_effect_css = $this->build_border_effect_css( '.$uid', [], $settings );\n"
        "        if ( $border_css || $border_hover_css || $border_effect_css ) {\n"
        "            $html .= '<style>';\n"
        "            if ( $border_css ) $html .= '.' . $uid . '{' . $border_css . '}';\n"
        "            $html .= $border_hover_css . $border_effect_css . '</style>';\n"
        "        }\n"
    )
    content = content.replace(
        '        return $html;\n    }\n}',
        block + '        return $html;\n    }\n}',
        1
    )
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# 12. PAYMENTBUTTONS (nessun $defaults, usa $html string building)
# ---------------------------------------------------------------
def fix_paymentbuttons():
    path = os.path.join(TILES_DIR, 'class-paymentbuttons-tile.php')
    with open(path, 'r', encoding='utf-8') as f: content = f.read()
    if 'build_border_css' in content: return 'already_done'

    render_start = content.find('public function render')
    provider_line = content.find("$provider    =", render_start)
    if provider_line == -1: provider_line = content.find("$provider =", render_start)
    eol = content.find('\n', provider_line)
    uid_line = "\n        $uid = 'olo-pb-' . wp_rand( 10000, 99999 );"
    content = content[:eol] + uid_line + content[eol:]

    content = content.replace(
        "'<div class=\"olo-paymentbuttons\"",
        "'<div class=\"olo-paymentbuttons ' . esc_attr( $uid ) . '\"",
        1
    )
    # cerca return $html nella funzione render (non nel resto della classe)
    render_section = content[render_start:]
    last_return_rel = render_section.rfind('return $html;')
    if last_return_rel == -1: return 'no_return'
    last_return = render_start + last_return_rel
    block = (
        "        $border_css        = $this->build_border_css( $settings['border'] ?? [] );\n"
        "        $border_hover_css  = $this->build_border_hover_css( '.' . $uid, $settings['border'] ?? [], $settings['border_hover'] ?? [], intval( $settings['border_hover_duration'] ?? 300 ) );\n"
        "        $border_effect_css = $this->build_border_effect_css( '.' . $uid, $settings['border'] ?? [], $settings );\n"
        "        if ( $border_css || $border_hover_css || $border_effect_css ) {\n"
        "            $html .= '<style>';\n"
        "            if ( $border_css ) $html .= '.' . $uid . '{' . $border_css . '}';\n"
        "            $html .= $border_hover_css . $border_effect_css . '</style>';\n"
        "        }\n"
    )
    content = content[:last_return] + block + "        " + content[last_return:]
    with open(path, 'w', encoding='utf-8') as f: f.write(content)
    return 'ok'

# ---------------------------------------------------------------
# MAIN
# ---------------------------------------------------------------
if __name__ == '__main__':
    results = {
        'newsletter':     fix_newsletter(),
        'iconlist':       fix_iconlist(),
        'viewscounter':   fix_viewscounter(),
        'facebookpage':   fix_facebookpage(),
        'totop':          fix_totop(),
        'postmeta':       fix_postmeta(),
        'switcher':       fix_switcher(),
        'divider':        fix_divider(),
        'soundcloud':     fix_soundcloud(),
        'list':           fix_list(),
        'lightbox':       fix_lightbox(),
        'paymentbuttons': fix_paymentbuttons(),
    }
    for k, v in results.items():
        print(f"  {k}: {v}")
