<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tile Vetrina temi — i temi inclusi nel plugin come siti da visitare: una scheda per tema con
 * l'anteprima che scorre al passaggio del mouse (come nel selettore dei temi), i filtri per
 * categoria e un visore a tutto schermo con la demo dal vivo (Olobuild_Demo_Temi) in vista
 * computer, tablet o telefono, col tema precedente e il successivo.
 *
 * Si registra solo dove le demo sono accese: altrove non avrebbe niente da mostrare. Elenco,
 * nomi, categorie ed effetti vengono dai temi stessi (Olobuild_Theme_Importer::get_themes()),
 * così la vetrina segue da sola i temi del plugin installato.
 *
 * Le schede sono collegamenti veri alla demo: senza JavaScript, o con Ctrl/Cmd+clic, la demo si
 * apre come pagina. Il visore è un <dialog> modale: sta sopra la pagina anche dentro il template
 * (che ha un transform), eredita i token del sito e tiene da sé il focus. Ricorda il tema
 * nell'indirizzo (#demo-<tema>): si può condividere, e il tasto Indietro lo chiude.
 * Spazi e raggi dai token del sito (--olo-space-*, raggi e pulsanti della Palette).
 * Runtime inline senza '&&' né '<'/'>' (WordPress li altera).
 */
class Olobuild_ThemeShowcase_Tile extends Olobuild_Tile_Base {

    protected $type     = 'themeshowcase';
    protected $name     = 'Vetrina temi';
    protected $icon     = 'dashicons-admin-appearance';
    protected $category = 'dynamic';
    protected $defaults = [
        'filter_bar'          => true,
        'filter_all_label'    => 'Tutti',
        'show_category'       => true,
        'show_zone'           => true,
        'card_label'          => 'Guarda la demo',
        'cta_text'            => 'Usa questo tema',
        'cta_url'             => '',
        // Stile
        'accent'              => '',
        'accent_on'           => '',
        'card_bg'             => '',
        'card_border'         => '',
        'card_radius'         => [ 'tl' => 16, 'tr' => 16, 'br' => 16, 'bl' => 16 ],
        'preview_ratio'       => '4:3',
        'name_font_family'    => '',
        'name_font_size'      => '20',
        'name_font_weight'    => '600',
        'name_text_transform' => 'none',
        'name_letter_spacing' => '0',
        'name_color'          => '',
        'meta_font_family'    => '',
        'meta_font_size'      => '12',
        'meta_font_weight'    => '600',
        'meta_text_transform' => 'uppercase',
        'meta_letter_spacing' => '1',
        'meta_color'          => '',
        'columns'             => 3,
        'gap'                 => 24,
        'filter_align'        => 'center',
    ];

    /** Icone dei comandi del visore (fisse: sono interfaccia, non contenuto). */
    private const ICONE = [
        'computer' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="12" rx="1.5"/><path d="M8 20h8M12 16v4"/></svg>',
        'tablet'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="2.5" width="14" height="19" rx="2"/><path d="M11 18.5h2"/></svg>',
        'telefono' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="2.5" width="10" height="19" rx="2"/><path d="M11 18.5h2"/></svg>',
        'prec'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>',
        'succ'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>',
        'esterno'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/></svg>',
        'chiudi'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>',
        'freccia'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
    ];

    public function get_controls() { return []; }

    /** I temi con la demo accesa, ordinati per categoria e nome. */
    private function temi() {
        if ( ! class_exists( 'Olobuild_Theme_Importer' ) ) {
            require_once OLOBUILD_PATH . 'includes/class-theme-importer.php';
        }
        $temi = [];
        foreach ( Olobuild_Theme_Importer::get_themes() as $t ) {
            $id = (string) ( $t['id'] ?? '' );
            if ( '' === $id || ! Olobuild_Demo_Temi::acceso( $id ) ) {
                continue;
            }
            $temi[] = [
                'id'        => $id,
                'nome'      => (string) ( $t['name'] ?? $id ),
                'categoria' => (string) ( $t['category'] ?? '' ),
                'effetto'   => (string) ( $t['zone'] ?? '' ),
                'anteprima' => (string) ( $t['screenshot'] ?? '' ),
                'demo'      => Olobuild_Demo_Temi::indirizzo_tema( $id ),
                'colore'    => (string) sanitize_hex_color( (string) ( $t['accent'] ?? '' ) ),
            ];
        }
        usort( $temi, function ( $a, $b ) {
            $c = strnatcasecmp( $a['categoria'], $b['categoria'] );
            return 0 !== $c ? $c : strnatcasecmp( $a['nome'], $b['nome'] );
        } );
        return $temi;
    }

    /** «4:3», «4/3» o «4-3» (i formati storici delle proporzioni) → «4/3» per aspect-ratio. */
    private function proporzione( $v ) {
        if ( preg_match( '/^(\d{1,2})\s*[:\/-]\s*(\d{1,2})$/', trim( (string) $v ), $m ) && (int) $m[1] > 0 && (int) $m[2] > 0 ) {
            return (int) $m[1] . '/' . (int) $m[2];
        }
        return '4/3';
    }

    /** Dichiarazioni CSS di un controllo tipografia (chiavi piatte con prefisso). */
    private function tipografia( $s, $p ) {
        $css = '';
        $ff  = $this->resolve_font_family( (string) ( $s[ $p . '_font_family' ] ?? '' ) );
        if ( '' !== $ff ) {
            $css .= 'font-family:' . $ff . ';';
        }
        $fs = intval( $s[ $p . '_font_size' ] ?? 0 );
        if ( $fs > 0 ) {
            $css .= 'font-size:' . max( 8, min( 72, $fs ) ) . 'px;';
        }
        $fw = $this->font_weight_css( $s[ $p . '_font_weight' ] ?? '' );
        if ( '' !== $fw ) {
            $css .= 'font-weight:' . $fw . ';';
        }
        $tt = (string) ( $s[ $p . '_text_transform' ] ?? '' );
        if ( in_array( $tt, [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ) {
            $css .= 'text-transform:' . $tt . ';';
        }
        $ls = (float) ( $s[ $p . '_letter_spacing' ] ?? 0 );
        if ( 0.0 !== $ls ) {
            $css .= 'letter-spacing:' . max( -5, min( 20, $ls ) ) . 'px;';
        }
        $col = $this->safe_color_css( $s[ $p . '_color' ] ?? '' );
        if ( '' !== $col ) {
            $css .= 'color:' . $col . ';';
        }
        return $css;
    }

    public function render( $settings, $style = [] ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $builder = ! empty( $s['_builder_mode'] );
        $temi    = $this->temi();
        if ( empty( $temi ) ) {
            return $builder ? '<p>' . esc_html( olobuild_t( 'Nessun tema con la demo accesa su questo sito.' ) ) . '</p>' : '';
        }
        $uid = 'ovt-' . $this->chiave_stabile( $settings );

        $categorie = [];
        foreach ( $temi as $t ) {
            if ( '' !== $t['categoria'] ) {
                $categorie[ $t['categoria'] ] = ( $categorie[ $t['categoria'] ] ?? 0 ) + 1;
            }
        }

        $acc    = $this->safe_color_css( $s['accent'] ) ?: 'var(--olo-color-primary, #e1474f)';
        $on     = $this->safe_color_css( $s['accent_on'] ) ?: 'var(--olo-color-primary-contrast, #ffffff)';
        $card   = $this->safe_color_css( $s['card_bg'] ) ?: 'var(--olo-color-surface, #ffffff)';
        $bordo  = Olobuild_Tile_Utils::border_css( $s['card_border'] ?? null, [ 'width' => 1, 'color' => 'var(--olo-color-border, #e5e7eb)' ] );
        $raggio = $this->build_border_radius_css( $s['card_radius'] ?? null ) ?: 'var(--olo-border-radius-large)';
        $ratio  = $this->proporzione( $s['preview_ratio'] );
        $cols   = max( 1, min( 4, intval( $s['columns'] ) ) );
        $gap    = max( 0, min( 64, intval( $s['gap'] ) ) );
        $giust  = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ][ (string) $s['filter_align'] ] ?? 'center';
        $cta    = trim( (string) $s['cta_url'] );
        $ico    = self::ICONE;
        $meta   = ( '' === $this->safe_color_css( $s['meta_color'] ) ? 'opacity:.65;' : '' ) . $this->tipografia( $s, 'meta' );
        $nome   = $this->tipografia( $s, 'name' );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS costruito solo da valori ripuliti qui sopra: colori dalla whitelist safe_color_css() con ripieghi var() fissi, bordo da Olobuild_Tile_Utils::border_css(), raggio da build_border_radius_css() (interi) o token, proporzione da proporzione() (interi), allineamento da mappa fissa, colonne e gap interi limitati, tipografia da tipografia() (whitelist e interi); $uid interno. Icone: SVG fissi della classe.
        ?>
        <style>
            .<?php echo $uid; ?>{--ovt-acc:<?php echo $acc; ?>;--ovt-on:<?php echo $on; ?>;--ovt-card:<?php echo $card; ?>;--ovt-r:<?php echo $raggio; ?>;}
            .<?php echo $uid; ?> .ovt-filtri{display:flex;flex-wrap:wrap;gap:var(--olo-space-sm);justify-content:<?php echo $giust; ?>;margin:0 0 var(--olo-space-lg);}
            .<?php echo $uid; ?> .ovt-filtro{font:inherit;font-size:14px;font-weight:600;line-height:1.2;display:inline-flex;align-items:center;gap:var(--olo-space-xs);padding:var(--olo-space-sm) var(--olo-space-md);border-radius:var(--olo-btn-border-radius);border:1px solid color-mix(in srgb,currentColor 20%,transparent);background:transparent;color:inherit;cursor:pointer;transition:background-color .2s ease,border-color .2s ease,color .2s ease;}
            .<?php echo $uid; ?> .ovt-filtro:hover{border-color:var(--ovt-acc);}
            .<?php echo $uid; ?> .ovt-filtro .ovt-n{font-size:12px;font-weight:500;opacity:.6;}
            .<?php echo $uid; ?> .ovt-filtro.is-on{background:var(--ovt-acc);border-color:var(--ovt-acc);color:var(--ovt-on);}
            .<?php echo $uid; ?> .ovt-filtro.is-on .ovt-n{opacity:.8;}
            .<?php echo $uid; ?> .ovt-griglia{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(<?php echo $cols; ?>,minmax(0,1fr));gap:<?php echo $gap; ?>px;}
            .<?php echo $uid; ?> .ovt-voce{margin:0;padding:0;}
            .<?php echo $uid; ?> .ovt-voce[hidden]{display:none;}
            .<?php echo $uid; ?> .ovt-card{display:flex;flex-direction:column;height:100%;text-decoration:none;color:inherit;background:var(--ovt-card);<?php echo $bordo; ?>border-radius:var(--ovt-r);overflow:hidden;transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;}
            .<?php echo $uid; ?> .ovt-card:hover{transform:translateY(-4px);box-shadow:var(--olo-shadow-large);border-color:var(--ovt-acc);}
            .<?php echo $uid; ?> .ovt-card:focus-visible,.<?php echo $uid; ?> .ovt-filtro:focus-visible{outline:2px solid var(--ovt-acc);outline-offset:3px;}
            .<?php echo $uid; ?> .ovt-anteprima{position:relative;display:block;aspect-ratio:<?php echo $ratio; ?>;overflow:hidden;background:color-mix(in srgb,currentColor 6%,transparent);}
            .<?php echo $uid; ?> .ovt-anteprima img{display:block;width:100%;height:100%;object-fit:cover;object-position:top center;transition:object-position .6s ease;}
            .<?php echo $uid; ?> .ovt-card:hover .ovt-anteprima img,.<?php echo $uid; ?> .ovt-card:focus-visible .ovt-anteprima img{object-position:bottom center;transition:object-position 9s linear;}
            .<?php echo $uid; ?> .ovt-effetto{position:absolute;left:var(--olo-space-sm);bottom:var(--olo-space-sm);max-width:calc(100% - 2 * var(--olo-space-sm));display:inline-flex;align-items:center;gap:var(--olo-space-xs);padding:var(--olo-space-xs) var(--olo-space-sm);border-radius:var(--olo-btn-border-radius);font-size:12px;font-weight:600;line-height:1.3;background:color-mix(in srgb,var(--olo-color-dark) 74%,transparent);color:var(--olo-color-light);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px);}
            .<?php echo $uid; ?> .ovt-effetto i{flex:none;width:7px;height:7px;border-radius:50%;background:var(--ovt-tema, var(--ovt-acc));}
            .<?php echo $uid; ?> .ovt-corpo{flex:1;display:flex;flex-direction:column;gap:var(--olo-space-xs);padding:var(--olo-space-md);}
            .<?php echo $uid; ?> .ovt-cat{<?php echo $meta; ?>}
            .<?php echo $uid; ?> .ovt-nome{line-height:1.2;font-family:var(--olo-font-family-heading, inherit);<?php echo $nome; ?>}
            .<?php echo $uid; ?> .ovt-azione{margin-top:auto;padding-top:var(--olo-space-sm);display:inline-flex;align-items:center;gap:var(--olo-space-xs);font-size:14px;font-weight:600;color:var(--ovt-acc);}
            .<?php echo $uid; ?> .ovt-azione svg{width:16px;height:16px;transition:transform .2s ease;}
            .<?php echo $uid; ?> .ovt-card:hover .ovt-azione svg{transform:translateX(3px);}
            @media (max-width:959px){.<?php echo $uid; ?> .ovt-griglia{grid-template-columns:repeat(<?php echo min( 2, $cols ); ?>,minmax(0,1fr));}}
            @media (max-width:639px){.<?php echo $uid; ?> .ovt-griglia{grid-template-columns:minmax(0,1fr);}
                .<?php echo $uid; ?> .ovt-filtri{flex-wrap:nowrap;justify-content:flex-start;overflow-x:auto;scroll-snap-type:x proximity;margin-left:calc(-1 * var(--olo-space-md));margin-right:calc(-1 * var(--olo-space-md));padding:0 var(--olo-space-md) var(--olo-space-xs);scrollbar-width:none;}
                .<?php echo $uid; ?> .ovt-filtri::-webkit-scrollbar{display:none;}
                .<?php echo $uid; ?> .ovt-filtro{flex:none;scroll-snap-align:start;}}
            <?php echo $this->css_per_dispositivo( $s, 'columns', '.' . $uid . ' .ovt-griglia', $this->decl_colonne( 4 ) ); ?>
            .<?php echo $uid; ?> .ovt-visore{position:fixed;inset:0;width:100%;height:100%;max-width:none;max-height:none;margin:0;padding:0;border:0;display:none;flex-direction:column;overflow:hidden;background:var(--olo-color-dark);color:var(--olo-color-light);font-family:var(--olo-font-family, inherit);}
            .<?php echo $uid; ?> .ovt-visore[open]{display:flex;}
            .<?php echo $uid; ?> .ovt-visore::backdrop{background:transparent;}
            .<?php echo $uid; ?> .ovt-barra{flex:none;display:flex;align-items:center;gap:var(--olo-space-sm);min-height:58px;padding:var(--olo-space-sm) var(--olo-space-md);border-bottom:1px solid color-mix(in srgb,currentColor 14%,transparent);}
            .<?php echo $uid; ?> .ovt-titolo{flex:1;min-width:0;display:flex;flex-direction:column;line-height:1.25;}
            .<?php echo $uid; ?> .ovt-titolo strong{font-size:16px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
            .<?php echo $uid; ?> .ovt-titolo span{font-size:12px;opacity:.65;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
            .<?php echo $uid; ?> .ovt-gruppo{display:flex;align-items:center;gap:var(--olo-space-xs);}
            .<?php echo $uid; ?> .ovt-dispositivi{padding:var(--olo-space-xs);border-radius:var(--olo-border-radius-large);background:color-mix(in srgb,currentColor 9%,transparent);}
            .<?php echo $uid; ?> .ovt-btn{font:inherit;display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;padding:0;border:0;border-radius:var(--olo-border-radius);background:transparent;color:inherit;cursor:pointer;text-decoration:none;transition:background-color .15s ease,color .15s ease;}
            .<?php echo $uid; ?> .ovt-btn svg{width:20px;height:20px;}
            .<?php echo $uid; ?> .ovt-btn:hover{background:color-mix(in srgb,currentColor 12%,transparent);}
            .<?php echo $uid; ?> .ovt-btn[aria-pressed="true"]{background:var(--ovt-acc);color:var(--ovt-on);}
            .<?php echo $uid; ?> .ovt-btn:focus-visible,.<?php echo $uid; ?> .ovt-cta:focus-visible{outline:2px solid var(--ovt-acc);outline-offset:2px;}
            .<?php echo $uid; ?> .ovt-cta{display:inline-flex;align-items:center;height:40px;padding:0 var(--olo-space-md);border-radius:var(--olo-btn-border-radius);background:var(--ovt-acc);color:var(--ovt-on);font-size:14px;font-weight:700;text-decoration:none;white-space:nowrap;}
            .<?php echo $uid; ?> .ovt-palco{flex:1;min-height:0;display:flex;justify-content:center;align-items:stretch;overflow:hidden;}
            .<?php echo $uid; ?> .ovt-schermo{position:relative;width:100%;height:100%;transition:width .35s ease,margin .35s ease,border-radius .35s ease;}
            .<?php echo $uid; ?> .ovt-schermo[data-vista="tablet"],.<?php echo $uid; ?> .ovt-schermo[data-vista="telefono"]{width:834px;max-width:calc(100% - 2 * var(--olo-space-md));height:calc(100% - 2 * var(--olo-space-md));margin:var(--olo-space-md) 0;border-radius:var(--olo-space-lg);overflow:hidden;box-shadow:0 0 0 10px color-mix(in srgb,currentColor 10%,transparent),var(--olo-shadow-large);}
            .<?php echo $uid; ?> .ovt-schermo[data-vista="telefono"]{width:390px;border-radius:var(--olo-space-xl);}
            .<?php echo $uid; ?> .ovt-schermo iframe{display:block;width:100%;height:100%;border:0;background:var(--olo-color-light);}
            .<?php echo $uid; ?> .ovt-attesa{position:absolute;inset:0;display:none;align-items:center;justify-content:center;flex-direction:column;gap:var(--olo-space-sm);font-size:14px;background:var(--olo-color-dark);}
            .<?php echo $uid; ?> .ovt-schermo.is-attesa .ovt-attesa{display:flex;}
            .<?php echo $uid; ?> .ovt-ruota{width:34px;height:34px;border-radius:50%;border:3px solid color-mix(in srgb,currentColor 20%,transparent);border-top-color:var(--ovt-acc);animation:<?php echo $uid; ?>-ruota .8s linear infinite;}
            @keyframes <?php echo $uid; ?>-ruota{to{transform:rotate(360deg);}}
            @media (max-width:639px){.<?php echo $uid; ?> .ovt-dispositivi,.<?php echo $uid; ?> .ovt-cta{display:none;}
                .<?php echo $uid; ?> .ovt-schermo[data-vista]{width:100%;max-width:none;height:100%;margin:0;border-radius:0;box-shadow:none;}
                .<?php echo $uid; ?> .ovt-barra{gap:var(--olo-space-xs);padding:var(--olo-space-xs) var(--olo-space-sm);}}
            html.ovt-aperto,html.ovt-aperto body{overflow:hidden;}
            @media (prefers-reduced-motion:reduce){.<?php echo $uid; ?> .ovt-card,.<?php echo $uid; ?> .ovt-anteprima img,.<?php echo $uid; ?> .ovt-schermo{transition:none;}
                .<?php echo $uid; ?> .ovt-card:hover .ovt-anteprima img{object-position:top center;}}
        </style>
        <div class="olo-themeshowcase <?php echo esc_attr( $uid ); ?>" data-ovt="<?php echo esc_attr( $uid ); ?>">
            <?php if ( ! empty( $s['filter_bar'] ) && count( $categorie ) > 1 ) : ?>
            <div class="ovt-filtri" role="group" aria-label="<?php echo esc_attr( olobuild_t( 'Categorie' ) ); ?>" data-olo-interactive>
                <button type="button" class="ovt-filtro is-on" aria-pressed="true" data-cat=""><?php echo esc_html( (string) $s['filter_all_label'] ); ?><span class="ovt-n"><?php echo (int) count( $temi ); ?></span></button>
                <?php foreach ( $categorie as $nome_cat => $quanti ) : ?>
                <button type="button" class="ovt-filtro" aria-pressed="false" data-cat="<?php echo esc_attr( $nome_cat ); ?>"><?php echo esc_html( $nome_cat ); ?><span class="ovt-n"><?php echo (int) $quanti; ?></span></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <ul class="ovt-griglia" role="list">
                <?php foreach ( $temi as $t ) : ?>
                <li class="ovt-voce" data-cat="<?php echo esc_attr( $t['categoria'] ); ?>">
                    <a class="ovt-card" href="<?php echo esc_url( $t['demo'] ); ?>" data-tema="<?php echo esc_attr( $t['id'] ); ?>" data-nome="<?php echo esc_attr( $t['nome'] ); ?>" data-categoria="<?php echo esc_attr( $t['categoria'] ); ?>"<?php echo '' !== $t['colore'] ? ' style="--ovt-tema:' . esc_attr( $t['colore'] ) . '"' : ''; ?>>
                        <span class="ovt-anteprima">
                            <?php if ( '' !== $t['anteprima'] ) : ?><img src="<?php echo esc_url( $t['anteprima'] ); ?>" alt="" loading="lazy" decoding="async" /><?php endif; ?>
                            <?php if ( ! empty( $s['show_zone'] ) && '' !== $t['effetto'] ) : ?><span class="ovt-effetto"><i></i><?php echo esc_html( $t['effetto'] ); ?></span><?php endif; ?>
                        </span>
                        <span class="ovt-corpo">
                            <?php if ( ! empty( $s['show_category'] ) && '' !== $t['categoria'] ) : ?><span class="ovt-cat"><?php echo esc_html( $t['categoria'] ); ?></span><?php endif; ?>
                            <span class="ovt-nome"><?php echo esc_html( $t['nome'] ); ?></span>
                            <?php if ( '' !== trim( (string) $s['card_label'] ) ) : ?><span class="ovt-azione"><?php echo esc_html( (string) $s['card_label'] ); ?><?php echo $ico['freccia']; ?></span><?php endif; ?>
                        </span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php if ( ! $builder ) : ?>
            <dialog class="ovt-visore" aria-labelledby="<?php echo esc_attr( $uid ); ?>-nome">
                <div class="ovt-barra">
                    <div class="ovt-titolo"><strong id="<?php echo esc_attr( $uid ); ?>-nome" data-nome></strong><span data-categoria></span></div>
                    <div class="ovt-gruppo ovt-dispositivi" role="group" aria-label="<?php echo esc_attr( olobuild_t( 'Vista' ) ); ?>">
                        <button type="button" class="ovt-btn" data-vista="computer" aria-pressed="true" aria-label="<?php echo esc_attr( olobuild_t( 'Computer' ) ); ?>" title="<?php echo esc_attr( olobuild_t( 'Computer' ) ); ?>"><?php echo $ico['computer']; ?></button>
                        <button type="button" class="ovt-btn" data-vista="tablet" aria-pressed="false" aria-label="<?php echo esc_attr( olobuild_t( 'Tablet' ) ); ?>" title="<?php echo esc_attr( olobuild_t( 'Tablet' ) ); ?>"><?php echo $ico['tablet']; ?></button>
                        <button type="button" class="ovt-btn" data-vista="telefono" aria-pressed="false" aria-label="<?php echo esc_attr( olobuild_t( 'Telefono' ) ); ?>" title="<?php echo esc_attr( olobuild_t( 'Telefono' ) ); ?>"><?php echo $ico['telefono']; ?></button>
                    </div>
                    <div class="ovt-gruppo">
                        <button type="button" class="ovt-btn" data-passo="-1" aria-label="<?php echo esc_attr( olobuild_t( 'Tema precedente' ) ); ?>" title="<?php echo esc_attr( olobuild_t( 'Tema precedente' ) ); ?>"><?php echo $ico['prec']; ?></button>
                        <button type="button" class="ovt-btn" data-passo="1" aria-label="<?php echo esc_attr( olobuild_t( 'Tema successivo' ) ); ?>" title="<?php echo esc_attr( olobuild_t( 'Tema successivo' ) ); ?>"><?php echo $ico['succ']; ?></button>
                        <a class="ovt-btn" data-nuova href="#" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( olobuild_t( 'Apri in una nuova scheda' ) ); ?>" title="<?php echo esc_attr( olobuild_t( 'Apri in una nuova scheda' ) ); ?>"><?php echo $ico['esterno']; ?></a>
                        <?php if ( '' !== $cta && '' !== trim( (string) $s['cta_text'] ) ) : ?><a class="ovt-cta" href="<?php echo esc_url( $cta ); ?>"><?php echo esc_html( (string) $s['cta_text'] ); ?></a><?php endif; ?>
                        <button type="button" class="ovt-btn" data-chiudi aria-label="<?php echo esc_attr( olobuild_t( 'Chiudi' ) ); ?>" title="<?php echo esc_attr( olobuild_t( 'Chiudi' ) ); ?>"><?php echo $ico['chiudi']; ?></button>
                    </div>
                </div>
                <div class="ovt-palco">
                    <div class="ovt-schermo" data-vista="computer">
                        <iframe title="" src="about:blank" data-titolo="<?php echo esc_attr( olobuild_t( 'Demo del tema %s' ) ); ?>"></iframe>
                        <div class="ovt-attesa" aria-hidden="true"><span class="ovt-ruota"></span><span><?php echo esc_html( olobuild_t( 'Caricamento della demo…' ) ); ?></span></div>
                    </div>
                </div>
            </dialog>
            <?php endif; ?>
        </div>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        $this->runtime( $uid, $builder );
        return ob_get_clean();
    }

    /** Filtri (anche nel builder) e visore (solo sul sito). Senza '&&' né '<'/'>'. */
    private function runtime( $uid, $builder ) {
        ?>
        <script>
        (function () {
            var root = document.querySelector('[data-ovt="<?php echo esc_js( $uid ); ?>"]');
            if (!root) { return; }
            var voci = [].slice.call(root.querySelectorAll('.ovt-voce'));
            var filtri = [].slice.call(root.querySelectorAll('.ovt-filtro'));
            function filtra(cat) {
                filtri.forEach(function (b) {
                    var on = b.getAttribute('data-cat') === cat;
                    b.classList.toggle('is-on', on);
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
                voci.forEach(function (v) { v.hidden = cat ? v.getAttribute('data-cat') !== cat : false; });
            }
            filtri.forEach(function (b) { b.addEventListener('click', function () { filtra(b.getAttribute('data-cat')); }); });
            <?php if ( $builder ) : ?>
        })();
        </script>
            <?php
            return;
        endif;
        ?>
            var visore = root.querySelector('.ovt-visore');
            if (!visore) { return; }
            if (typeof visore.showModal !== 'function') { return; }
            var schermo = visore.querySelector('.ovt-schermo'), frame = visore.querySelector('iframe');
            var nuova = visore.querySelector('[data-nuova]'), chiudiBtn = visore.querySelector('[data-chiudi]');
            var attuale = null, ritorno = null, spinto = false, atteso = '', dentro = false;
            // Cambiare la pagina del riquadro con src aggiungerebbe una voce alla cronologia della
            // finestra: «Indietro» tornerebbe al tema di prima invece di chiudere il visore.
            function vai(url) { try { frame.contentWindow.location.replace(url); } catch (err) { frame.src = url; } }
            function lista() { return voci.filter(function (v) { return !v.hidden; }).map(function (v) { return v.querySelector('.ovt-card'); }); }
            function card(id) { var c = null; voci.forEach(function (v) { var a = v.querySelector('.ovt-card'); if (a.getAttribute('data-tema') === id) { c = a; } }); return c; }
            function mostra(a, storia) {
                attuale = a;
                visore.querySelector('[data-nome]').textContent = a.getAttribute('data-nome');
                visore.querySelector('[data-categoria]').textContent = a.getAttribute('data-categoria');
                frame.title = frame.getAttribute('data-titolo').replace('%s', a.getAttribute('data-nome'));
                schermo.classList.add('is-attesa');
                atteso = a.href;
                dentro = false;
                vai(atteso);
                nuova.href = atteso;
                if (!visore.open) {
                    ritorno = document.activeElement;
                    visore.showModal();
                    document.documentElement.classList.add('ovt-aperto');
                    chiudiBtn.focus();
                }
                var segno = '#demo-' + a.getAttribute('data-tema');
                if (storia === 'nuova') { history.pushState({ ovt: 1 }, '', segno); spinto = true; }
                else if (storia === 'sostituisci') { history.replaceState(history.state, '', segno); }
            }
            function chiudi(daStoria) {
                if (!visore.open) { return; }
                visore.close();
                atteso = '';
                vai('about:blank');
                document.documentElement.classList.remove('ovt-aperto');
                attuale = null;
                if (ritorno) { ritorno.focus(); }
                if (!daStoria) {
                    // Indietro toglie la voce aggiunta all'apertura; se dentro la demo si è navigato,
                    // le voci sono di più: si toglie solo il tema dall'indirizzo, senza muoversi.
                    if (spinto ? !dentro : false) { spinto = false; history.back(); }
                    else { spinto = false; history.replaceState(history.state, '', location.pathname + location.search); }
                }
            }
            function passo(n) {
                var l = lista(), i = l.indexOf(attuale);
                if (!l.length) { return; }
                mostra(l[(i + n + l.length) % l.length], 'sostituisci');
            }
            frame.addEventListener('load', function () {
                var dove = '';
                try { dove = frame.contentWindow.location.href; } catch (err) { dove = ''; }
                if (!atteso) { return; }
                schermo.classList.remove('is-attesa');
                // Una pagina della demo aperta dal suo menu: «Apri in una nuova scheda» porta lì.
                if (dove) {
                    if (dove.split('#')[0] !== atteso.split('#')[0]) { dentro = true; }
                    nuova.href = dove;
                }
            });
            voci.forEach(function (v) {
                var a = v.querySelector('.ovt-card');
                a.addEventListener('click', function (e) {
                    if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button) { return; }
                    e.preventDefault();
                    mostra(a, visore.open ? 'sostituisci' : 'nuova');
                });
            });
            [].slice.call(visore.querySelectorAll('button[data-vista]')).forEach(function (b) {
                b.addEventListener('click', function () {
                    schermo.setAttribute('data-vista', b.getAttribute('data-vista'));
                    [].slice.call(visore.querySelectorAll('button[data-vista]')).forEach(function (x) { x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
                });
            });
            [].slice.call(visore.querySelectorAll('[data-passo]')).forEach(function (b) {
                b.addEventListener('click', function () { passo(parseInt(b.getAttribute('data-passo'), 10)); });
            });
            chiudiBtn.addEventListener('click', function () { chiudi(false); });
            // Esc: il dialogo lo chiuderebbe da sé, senza togliere il tema dall'indirizzo.
            visore.addEventListener('cancel', function (e) { e.preventDefault(); chiudi(false); });
            visore.addEventListener('keydown', function (e) {
                if (e.key !== 'ArrowLeft' ? e.key !== 'ArrowRight' : false) { return; }
                if (e.target === frame) { return; }
                passo(e.key === 'ArrowLeft' ? -1 : 1);
            });
            function dallIndirizzo() {
                var h = location.hash;
                if (h.indexOf('#demo-') !== 0) { chiudi(true); return; }
                var a = card(h.slice(6));
                if (!a) { return; }
                if (a.closest('.ovt-voce').hidden) { filtra(''); }
                mostra(a, '');
            }
            window.addEventListener('popstate', function () { spinto = false; dallIndirizzo(); });
            if (location.hash.indexOf('#demo-') === 0) { dallIndirizzo(); }
        })();
        </script>
        <?php
    }
}
