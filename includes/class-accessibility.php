<?php
/**
 * Olobuild_Accessibility — Skip navigation, ARIA enhancements, focus styles,
 * reduced-motion, heading checker, alt-text warnings.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Accessibility {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // Skip navigation link (very early in body)
        add_action( 'wp_body_open', [ $this, 'output_skip_nav' ], 1 );

        // Accessibility CSS inline
        add_action( 'wp_head', [ $this, 'output_a11y_css' ], 20 );

        // Filter tile output to add ARIA enhancements
        add_filter( 'olobuild_tile_output', [ $this, 'enhance_tile_aria' ], 10, 3 );

        // Heading structure check (admin notice in builder)
        add_action( 'wp_footer', [ $this, 'output_a11y_scripts' ], 99 );

        // REST API for contrast checker
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /* ─────────────────────────────────────────────
     * Skip Navigation
     * ───────────────────────────────────────────── */

    public function output_skip_nav() {
        echo '<a class="olo-skip-nav" href="#olo-main-content">' . esc_html__( 'Vai al contenuto principale', 'olobuild' ) . '</a>' . "\n";
    }

    /* ─────────────────────────────────────────────
     * Accessibility CSS
     * ───────────────────────────────────────────── */

    public function output_a11y_css() {
        ?>
<style id="olo-accessibility-css">
/* Skip navigation */
.olo-skip-nav{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;z-index:100001;background:#000;color:#fff;padding:8px 16px;font-size:14px;text-decoration:none;border-radius:0 0 4px 0}
.olo-skip-nav:focus{position:fixed;left:0;top:0;width:auto;height:auto;overflow:visible;outline:3px solid #4f46e5}
/* Focus visible — all interactive elements */
.olo-template a:focus-visible,
.olo-template button:focus-visible,
.olo-template input:focus-visible,
.olo-template select:focus-visible,
.olo-template textarea:focus-visible,
.olo-template [tabindex]:focus-visible,
.olo-template summary:focus-visible{outline:2px solid var(--olo-color-primary,#4f46e5);outline-offset:2px;border-radius:2px}
/* Remove default outline for mouse users */
.olo-template a:focus:not(:focus-visible),
.olo-template button:focus:not(:focus-visible),
.olo-template input:focus:not(:focus-visible){outline:none}
/* Reduced motion */
@media(prefers-reduced-motion:reduce){.olo-template *,.olo-template *::before,.olo-template *::after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important;scroll-behavior:auto!important}}
/* Screen reader only */
.olo-sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
/* High contrast mode support */
@media(forced-colors:active){.olo-template a,.olo-template button{forced-color-adjust:none}}
/* Alt text warning (builder only) */
.olo-alt-warning{position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;font-size:10px;padding:2px 6px;border-radius:3px;z-index:10;pointer-events:none}
</style>
        <?php
    }

    /* ─────────────────────────────────────────────
     * ARIA enhancements on tile output
     * ───────────────────────────────────────────── */

    /**
     * Post-process tile HTML to ensure correct ARIA attributes.
     *
     * @param string $html     Tile rendered HTML.
     * @param string $type     Tile type slug.
     * @param array  $settings Tile settings.
     * @return string Enhanced HTML.
     */
    public function enhance_tile_aria( $html, $type, $settings ) {
        if ( empty( $html ) ) {
            return $html;
        }

        switch ( $type ) {
            case 'accordion':
                // Ensure aria-expanded on triggers, aria-controls on panels
                $html = $this->aria_accordion( $html );
                break;

            case 'switcher':
            case 'switcherpanel':
                // role="tablist", role="tab", role="tabpanel"
                $html = $this->aria_tabs( $html );
                break;

            case 'popup':
            case 'popover':
                // role="dialog", aria-modal
                $html = $this->aria_dialog( $html );
                break;

            case 'nav':
            case 'navmenu':
            case 'megamenu':
                // role="navigation", aria-label
                $html = $this->aria_nav( $html, $settings );
                break;

            case 'slideshow':
            case 'proslider':
            case 'overlayslider':
            case 'panelslider':
                // role="region", aria-roledescription="carousel", aria-label
                $html = $this->aria_carousel( $html );
                break;

            case 'image':
                // Warn on missing alt text
                $html = $this->check_alt_text( $html, $settings );
                break;
        }

        return $html;
    }

    private function aria_accordion( $html ) {
        // Add aria-expanded to accordion toggle buttons if missing
        if ( ! str_contains( $html, 'aria-expanded' ) ) {
            $html = preg_replace(
                '/(<a[^>]*class="[^"]*uk-accordion-title[^"]*")/i',
                '$1 role="button" aria-expanded="false"',
                $html
            );
        }
        return $html;
    }

    private function aria_tabs( $html ) {
        // Add role="tablist" if missing
        if ( ! str_contains( $html, 'role="tablist"' ) ) {
            $html = preg_replace(
                '/(<ul[^>]*class="[^"]*uk-tab[^"]*")/i',
                '$1 role="tablist"',
                $html
            );
        }
        return $html;
    }

    private function aria_dialog( $html ) {
        // Add role="dialog" and aria-modal if missing
        if ( ! str_contains( $html, 'role="dialog"' ) ) {
            $html = preg_replace(
                '/(<div[^>]*class="[^"]*uk-modal-dialog[^"]*")/i',
                '$1 role="dialog" aria-modal="true"',
                $html
            );
        }
        return $html;
    }

    private function aria_nav( $html, $settings ) {
        // Ensure <nav> has aria-label
        if ( ! str_contains( $html, 'aria-label' ) ) {
            $label = ! empty( $settings['aria_label'] ) ? $settings['aria_label'] : 'Navigazione';
            $html = preg_replace(
                '/(<nav[^>]*)>/i',
                '$1 aria-label="' . esc_attr( $label ) . '">',
                $html,
                1
            );
        }
        return $html;
    }

    private function aria_carousel( $html ) {
        // Add carousel ARIA to wrapper
        if ( ! str_contains( $html, 'aria-roledescription' ) ) {
            $html = preg_replace(
                '/(<div[^>]*class="[^"]*olo-(?:slideshow|proslider|overlayslider|panelslider)[^"]*")/i',
                '$1 role="region" aria-roledescription="carousel" aria-label="Slideshow"',
                $html,
                1
            );
        }
        return $html;
    }

    private function check_alt_text( $html, $settings ) {
        // If alt text is empty, add sr-only warning for builder context
        $alt = $settings['alt_text'] ?? '';
        if ( empty( $alt ) ) {
            // Add empty alt="" for decorative images (WCAG compliant)
            if ( ! str_contains( $html, 'alt=""' ) ) {
                $html = preg_replace(
                    '/<img(?![^>]*alt=)/i',
                    '<img alt=""',
                    $html
                );
            }
        }
        return $html;
    }

    /* ─────────────────────────────────────────────
     * REST API — Contrast Checker
     * ───────────────────────────────────────────── */

    public function register_routes() {
        // Contrast check for template
        register_rest_route( 'olo/v1', '/contrast-check/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'check_contrast' ],
            'permission_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
        ] );

        // Single color pair check
        register_rest_route( 'olo/v1', '/contrast-ratio', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_contrast_ratio' ],
            'permission_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
            'args' => [
                'fg' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
                'bg' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );
    }

    /**
     * Check contrast ratios throughout a template.
     */
    public function check_contrast( $request ) {
        $template_id = intval( $request['id'] );
        $db          = new Olobuild_Database();
        $template    = $db->get_template( $template_id );

        if ( ! $template ) {
            return new WP_Error( 'not_found', 'Template non trovato', [ 'status' => 404 ] );
        }

        // get_template() ritorna un ARRAY con 'content' GIÀ decodificato (l'albero tile).
        // (Il vecchio codice leggeva $template->data: campo+accesso errati → 0 coppie sempre.)
        $data = is_array( $template['content'] ?? null ) ? $template['content'] : [];
        if ( empty( $data ) ) {
            return rest_ensure_response( [ 'issues' => [], 'pairs' => 0, 'score' => 100 ] );
        }

        $pairs  = [];
        $this->collect_color_pairs( $data, $pairs );

        $issues = [];
        foreach ( $pairs as $pair ) {
            $ratio = $this->calculate_contrast_ratio( $pair['fg'], $pair['bg'] );
            if ( null === $ratio ) {
                continue; // coppia non valutabile: non la conteggiamo (prima era assunta OK con 21)
            }
            $pair['ratio'] = round( $ratio, 2 );

            $is_large = ( $pair['size'] ?? 16 ) >= 18 || ( ( $pair['size'] ?? 16 ) >= 14 && ( $pair['weight'] ?? 400 ) >= 700 );
            $threshold = $is_large ? 3.0 : 4.5;

            if ( $ratio < $threshold ) {
                $level = $ratio < 3.0 ? 'error' : 'warning';
                $issues[] = [
                    'type'      => $level,
                    'message'   => sprintf(
                        '%s: contrasto %s su %s = %.1f:1 (minimo %.1f:1 per %s)',
                        $pair['element'],
                        $pair['fg'],
                        $pair['bg'],
                        $ratio,
                        $threshold,
                        $is_large ? 'testo grande' : 'testo normale'
                    ),
                    'id'        => $pair['id'],
                    'fg'        => $pair['fg'],
                    'bg'        => $pair['bg'],
                    'ratio'     => $pair['ratio'],
                    'threshold' => $threshold,
                ];
            }
        }

        // Immagini senza testo alternativo (WCAG 1.1.1)
        $this->collect_alt_issues( $data, $issues );

        $score = empty( $issues ) ? 100 : max( 0, 100 - count( $issues ) * 10 );

        return rest_ensure_response( [
            'issues' => $issues,
            'pairs'  => count( $pairs ),
            'score'  => $score,
        ] );
    }

    /**
     * Raccoglie le immagini con testo alternativo mancante (1.1.1).
     * Cammina lo stesso albero di collect_color_pairs cercando le coppie
     * immagine+alt note. Segnala come WARNING (l'utente decide se decorativa).
     */
    private function collect_alt_issues( $nodes, &$issues ) {
        if ( ! is_array( $nodes ) ) {
            return;
        }
        // Chiavi di immagine-CONTENUTO (NON gli sfondi decorativi bg_*) e chiavi alt note.
        $img_keys = [ 'image_url', 'image', 'src' ];
        $alt_keys = [ 'alt_text', 'alt', 'image_alt' ];
        foreach ( $nodes as $node ) {
            if ( ! is_array( $node ) ) {
                continue;
            }
            $settings = $node['settings'] ?? [];

            $has_image = false;
            foreach ( $img_keys as $ik ) {
                $v = $settings[ $ik ] ?? '';
                if ( is_string( $v ) && $v !== '' ) { $has_image = true; break; }
            }
            if ( $has_image ) {
                $has_alt = false;
                foreach ( $alt_keys as $ak ) {
                    $a = $settings[ $ak ] ?? '';
                    if ( is_string( $a ) && trim( $a ) !== '' ) { $has_alt = true; break; }
                }
                if ( ! $has_alt ) {
                    $issues[] = [
                        'type'    => 'warning',
                        'kind'    => 'alt',
                        'message' => sprintf(
                            /* translators: %s = tipo di tile */
                            __( 'Immagine senza testo alternativo nella tile "%s" (se decorativa, ignora; altrimenti aggiungi un alt descrittivo).', 'olobuild' ),
                            $node['type'] ?? ''
                        ),
                        'id'      => $node['id'] ?? '',
                    ];
                }
            }
            if ( ! empty( $node['children'] ) ) {
                $this->collect_alt_issues( $node['children'], $issues );
            }
        }
    }

    /**
     * Single color pair contrast ratio check.
     */
    public function get_contrast_ratio( $request ) {
        $fg    = $request['fg'];
        $bg    = $request['bg'];
        $ratio = $this->calculate_contrast_ratio( $fg, $bg );

        if ( null === $ratio ) {
            // Colore non risolvibile: segnalalo come non valutabile invece di fingere OK.
            return rest_ensure_response( [
                'fg'        => $fg,
                'bg'        => $bg,
                'evaluable' => false,
            ] );
        }

        return rest_ensure_response( [
            'fg'       => $fg,
            'bg'       => $bg,
            'ratio'    => round( $ratio, 2 ),
            'evaluable' => true,
            'aa'       => $ratio >= 4.5,
            'aa_large' => $ratio >= 3.0,
            'aaa'      => $ratio >= 7.0,
        ] );
    }

    /**
     * Recursively collect text/background color pairs from template.
     */
    private function collect_color_pairs( $nodes, &$pairs, $parent_bg = '#ffffff' ) {
        if ( ! is_array( $nodes ) ) {
            return;
        }

        foreach ( $nodes as $node ) {
            if ( ! is_array( $node ) ) {
                continue;
            }

            $type     = $node['type'] ?? '';
            $settings = $node['settings'] ?? [];
            $style    = $node['style'] ?? [];
            $id       = $node['id'] ?? '';

            // Determine background color for this node
            $bg = $parent_bg;
            if ( ! empty( $style['background_color'] ) ) {
                $bg = $style['background_color'];
            }
            if ( ! empty( $settings['bg_color'] ) ) {
                $bg = $settings['bg_color'];
            }
            if ( ! empty( $settings['background_color'] ) ) {
                $bg = $settings['background_color'];
            }

            // Headline tile
            if ( $type === 'headline' ) {
                $fg = $settings['color'] ?? $style['color'] ?? '';
                if ( $fg ) {
                    $pairs[] = [
                        'element' => 'Headline',
                        'fg'      => $fg,
                        'bg'      => $bg,
                        'size'    => intval( $settings['font_size'] ?? 24 ),
                        'weight'  => intval( $settings['font_weight'] ?? 700 ),
                        'id'      => $id,
                    ];
                }
            }

            // Content tile — use text color
            if ( $type === 'content' ) {
                $fg = $settings['text_color'] ?? $style['color'] ?? '';
                if ( $fg ) {
                    $pairs[] = [
                        'element' => 'Content',
                        'fg'      => $fg,
                        'bg'      => $bg,
                        'size'    => intval( $settings['font_size'] ?? 16 ),
                        'weight'  => 400,
                        'id'      => $id,
                    ];
                }
            }

            // Button tile
            if ( $type === 'button' ) {
                $fg     = $settings['text_color'] ?? $settings['color'] ?? '';
                $btn_bg = $settings['bg_color'] ?? $settings['background_color'] ?? '';
                if ( $fg ) {
                    $pairs[] = [
                        'element' => 'Button',
                        'fg'      => $fg,
                        'bg'      => $btn_bg ?: $bg,
                        'size'    => intval( $settings['font_size'] ?? 16 ),
                        'weight'  => 600,
                        'id'      => $id,
                    ];
                }
            }

            // Alert tile
            if ( $type === 'alert' ) {
                $fg = $settings['text_color'] ?? '';
                $alert_bg = $settings['bg_color'] ?? '';
                if ( $fg ) {
                    $pairs[] = [
                        'element' => 'Alert',
                        'fg'      => $fg,
                        'bg'      => $alert_bg ?: $bg,
                        'size'    => 16,
                        'weight'  => 400,
                        'id'      => $id,
                    ];
                }
            }

            // Hero tile
            if ( $type === 'hero' ) {
                $fg = $settings['title_color'] ?? '';
                if ( $fg ) {
                    $pairs[] = [
                        'element' => 'Hero Title',
                        'fg'      => $fg,
                        'bg'      => $settings['bg_color'] ?? $bg,
                        'size'    => intval( $settings['title_size'] ?? 48 ),
                        'weight'  => 700,
                        'id'      => $id,
                    ];
                }
            }

            // Recurse with this node's bg color
            if ( ! empty( $node['children'] ) ) {
                $this->collect_color_pairs( $node['children'], $pairs, $bg );
            }
        }
    }

    /**
     * Calculate WCAG contrast ratio between two colors.
     * Returns ratio as float (e.g. 4.5 for 4.5:1).
     */
    /**
     * Risolve un token colore var(--olo-color-X) nel suo valore esadecimale corrente,
     * leggendo olo_global_colors (vince, come in generate_css) poi olo_styles['colors'].
     * Lascia invariati i valori già esadecimali/rgb.
     */
    private function resolve_color_token( $color ) {
        $c = trim( (string) $color );
        if ( ! preg_match( '/var\(\s*--olo-color-([a-z0-9_-]+)\s*(?:,\s*([^)]+))?\)/i', $c, $m ) ) {
            return $c;
        }
        $id       = sanitize_key( $m[1] );
        $fallback = isset( $m[2] ) ? trim( $m[2] ) : '';

        $gc = get_option( 'olo_global_colors', [] );
        if ( is_array( $gc ) ) {
            foreach ( $gc as $g ) {
                if ( isset( $g['id'], $g['value'] ) && $g['id'] === $id && $g['value'] !== '' ) {
                    return $g['value'];
                }
            }
        }
        $styles = get_option( 'olo_styles', [] );
        if ( ! empty( $styles['colors'][ $id ] ) ) {
            return $styles['colors'][ $id ];
        }
        return $fallback; // può essere un hex o un altro var: il chiamante ritenta hex_to_rgb
    }

    private function calculate_contrast_ratio( $fg, $bg ) {
        // Risolve prima i token (var(--olo-color-*)) → altrimenti il checker era cieco ai colori del cliente.
        $fg_rgb = $this->hex_to_rgb( $this->resolve_color_token( $fg ) );
        $bg_rgb = $this->hex_to_rgb( $this->resolve_color_token( $bg ) );

        if ( ! $fg_rgb || ! $bg_rgb ) {
            return null; // Non risolvibile (token non mappato, gradiente, ecc.): NON valutabile (prima: 21 = falso OK).
        }

        $l1 = $this->relative_luminance( $fg_rgb );
        $l2 = $this->relative_luminance( $bg_rgb );

        $lighter = max( $l1, $l2 );
        $darker  = min( $l1, $l2 );

        return ( $lighter + 0.05 ) / ( $darker + 0.05 );
    }

    /**
     * Parse hex color to RGB array. Handles #RGB and #RRGGBB.
     */
    private function hex_to_rgb( $hex ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if ( strlen( $hex ) !== 6 ) {
            return null;
        }
        if ( ! ctype_xdigit( $hex ) ) {
            return null;
        }
        return [
            'r' => hexdec( substr( $hex, 0, 2 ) ),
            'g' => hexdec( substr( $hex, 2, 2 ) ),
            'b' => hexdec( substr( $hex, 4, 2 ) ),
        ];
    }

    /**
     * Calculate relative luminance per WCAG 2.1.
     */
    private function relative_luminance( $rgb ) {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $r = $r <= 0.03928 ? $r / 12.92 : pow( ( $r + 0.055 ) / 1.055, 2.4 );
        $g = $g <= 0.03928 ? $g / 12.92 : pow( ( $g + 0.055 ) / 1.055, 2.4 );
        $b = $b <= 0.03928 ? $b / 12.92 : pow( ( $b + 0.055 ) / 1.055, 2.4 );

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /* ─────────────────────────────────────────────
     * Frontend JS for keyboard helpers
     * ───────────────────────────────────────────── */

    public function output_a11y_scripts() {
        // Only output on pages with Olobuild content
        if ( ! did_action( 'olobuild_frontend_rendered' ) ) {
            return;
        }
        ?>
<script id="olo-a11y-js">
(function(){
/* Escape key closes modals/popups */
document.addEventListener('keydown',function(e){
if(e.key==='Escape'){
var m=document.querySelector('.uk-modal.uk-open');
if(m){if(typeof UIkit!=='undefined'){UIkit.modal(m).hide()}}
}
});
/* Focus trap for open modals */
document.addEventListener('focusin',function(e){
var m=document.querySelector('.uk-modal.uk-open .uk-modal-dialog');
if(!m)return;
if(!m.contains(e.target)){
var first=m.querySelector('a[href],button:not([disabled]),input,select,textarea,[tabindex]:not([tabindex="-1"])');
if(first){first.focus()}
}
});
/* Keep ARIA state in sync with UIkit interactive widgets.
   UIkit toggles .uk-open / .uk-active classes but never updates ARIA,
   so we mirror those into aria-expanded (accordion) and aria-selected (tabs). */
function oloSyncAria(){
var accs=document.querySelectorAll('.uk-accordion > li');
accs.forEach(function(li){
var t=li.querySelector(':scope > .uk-accordion-title');
if(!t||t.__oloAria){return}
t.__oloAria=1;
var upd=function(){t.setAttribute('aria-expanded',li.classList.contains('uk-open')?'true':'false')};
upd();
new MutationObserver(upd).observe(li,{attributes:true,attributeFilter:['class']});
});
document.querySelectorAll('.uk-tab').forEach(function(tl){
tl.setAttribute('role','tablist');
tl.querySelectorAll(':scope > li').forEach(function(li){
var a=li.querySelector('a');
if(!a||a.__oloAria){return}
a.__oloAria=1;
a.setAttribute('role','tab');
var upd=function(){a.setAttribute('aria-selected',li.classList.contains('uk-active')?'true':'false')};
upd();
new MutationObserver(upd).observe(li,{attributes:true,attributeFilter:['class']});
});
});
document.querySelectorAll('.uk-switcher > li').forEach(function(li){
if(!li.__oloAria){li.__oloAria=1;li.setAttribute('role','tabpanel')}
});
}
oloSyncAria();
document.addEventListener('DOMContentLoaded',oloSyncAria);
})();
</script>
        <?php
    }
}
