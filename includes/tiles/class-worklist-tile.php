<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Work List — indice di progetti/voci a righe con hover-list (indent + shift + freccia).
 * Generica e token-first: il titolo eredita il font heading del tema; meta in monospace.
 */
class Olobuild_WorkList_Tile extends Olobuild_Tile_Base {

    protected $type     = 'worklist';
    protected $name     = 'Work List';
    protected $icon     = 'dashicons-menu-alt';
    protected $category = 'layout';
    protected $defaults = [
        'items' => [
            // In italiano come la partenza del config (16 nov 2026: erano progetti e categorie in inglese).
            [ 'number' => '01', 'title' => 'Residenza Aurora', 'category' => 'Architettura',     'year' => '2026', 'link_url' => '' ],
            [ 'number' => '02', 'title' => 'Bottega Marini',   'category' => 'Identità visiva',  'year' => '2025', 'link_url' => '' ],
            [ 'number' => '03', 'title' => 'Studio Ferri',     'category' => 'Interni · arredo', 'year' => '2025', 'link_url' => '' ],
            [ 'number' => '04', 'title' => 'Caffè Centrale',   'category' => 'Sito web',         'year' => '2024', 'link_url' => '' ],
        ],
        'divider_color'     => 'var(--olo-color-border, #d7d1c2)',
        'row_padding_y'     => 26,
        'row_hover_bg'      => 'var(--olo-color-surface-alt, #e8e3d7)',
        'hover_indent'      => 24,
        'number_color'      => 'var(--olo-color-text-soft, #8d8a82)',
        'number_size'       => 13,
        'title_font_family' => 'heading',
        'title_color'       => 'var(--olo-color-text, #18181a)',
        'title_size'        => 40,
        'title_weight'      => '500',
        'show_category'     => true,
        'category_color'    => 'var(--olo-color-text-soft, #8d8a82)',
        'category_size'     => 12,
        'show_year'         => true,
        'year_color'        => 'var(--olo-color-text, #18181a)',
        'year_size'         => 13,
        'show_arrow'        => true,
        'arrow_color'       => 'var(--olo-color-text, #18181a)',
        'mono_font_family'  => '',
    ];

    public function get_controls() { return []; }

    public function render( $settings, $style = [] ) {
        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-worklist-' . wp_rand( 10000, 99999 );

        // Il ripiego dei titoli è quello di tutte le tile (resolve_font_family('heading')): senza un carattere dei titoli
        // scelto nel sito questa tile ripiegava su DM Sans mentre i titoli accanto andavano su Georgia (16 nov 2026).
        $heading = $this->resolve_font_family( 'heading' );
        $body    = "var(--olo-font-family, 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif)";
        // Vuoto = il mono del tema (--olo-font-family-mono), come dice l'inspector; il monospazio di sistema solo se il tema non ne ha uno.
        $mono_fb = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,Consolas,monospace)";
        $mono_fam = $this->resolve_font_family( $s['mono_font_family'] ?? '' );
        // Nome font puro (legacy campo text) → wrap con lo stack mono di fallback storico.
        if ( $mono_fam !== '' && preg_match( '/^[A-Za-z0-9 \-]+$/', $mono_fam ) ) {
            $mono_fam = "'" . $mono_fam . "'," . $mono_fb;
        }
        $mono    = $mono_fam !== '' ? $mono_fam : $mono_fb;
        $tfam    = $this->resolve_font_family( $s['title_font_family'] ?? '', [ 'heading' => $heading, 'body' => $body, 'mono' => $mono ] ) ?: $heading;

        $row_pad     = Olobuild_Tile_Utils::spacing_sides(
            $s['row_padding'] ?? null,
            [ 'y' => $s['row_padding_y'] ?? null ],
            [ 26, 8, 26, 8 ]
        );
        $row_pad_css = Olobuild_Tile_Utils::sides_css( $row_pad );
        $indent      = max( 0, min( 48, absint( $s['hover_indent'] ) ) );
        $number_size = max( 10, min( 20, absint( $s['number_size'] ) ) );
        $title_size  = max( 20, min( 72, absint( $s['title_size'] ) ) );
        // Misure del titolo per tablet e telefono (selettore del dispositivo nel controllo «Titolo»). Vuote =
        // come prima: sul tablet quella del computer, sul telefono al massimo il 9% della larghezza. Con titoli
        // lunghi (un programma di eventi) il 9% andava a capo ogni due parole.
        $title_t     = absint( $s['title_size_tablet'] ?? 0 );
        $title_m     = absint( $s['title_size_mobile'] ?? 0 );
        $title_t     = $title_t ? max( 20, min( 72, $title_t ) ) : 0;
        $title_m     = $title_m ? max( 20, min( 72, $title_m ) ) : 0;
        $title_wt    = preg_match( '/^\d+$/', (string) $s['title_weight'] ) ? $s['title_weight'] : '500';
        $cat_size    = max( 10, min( 18, absint( $s['category_size'] ) ) );
        $year_size   = max( 10, min( 20, absint( $s['year_size'] ) ) );

        $line     = $this->safe_color_css( $s['divider_color'] ) ?: 'var(--olo-color-border, #d7d1c2)';
        $hover_bg = $this->safe_color_css( $s['row_hover_bg'] ) ?: 'transparent';
        $num_c    = $this->safe_color_css( $s['number_color'] ) ?: 'var(--olo-color-text-muted, #8d8a82)';
        $title_c  = $this->safe_color_css( $s['title_color'] ) ?: 'var(--olo-color-text, #18181a)';
        $cat_c    = $this->safe_color_css( $s['category_color'] ) ?: 'var(--olo-color-text-muted, #8d8a82)';
        $year_c   = $this->safe_color_css( $s['year_color'] ) ?: 'var(--olo-color-text, #18181a)';
        $arrow_c  = $this->safe_color_css( $s['arrow_color'] ) ?: 'var(--olo-color-text, #18181a)';

        $show_cat   = ! empty( $s['show_category'] );
        $show_year  = ! empty( $s['show_year'] );
        $show_arrow = ! empty( $s['show_arrow'] );

        $items = is_array( $s['items'] ) ? $s['items'] : [];

        // Colonna del numero: 48 px (32 sul telefono), ma mai più stretta della parola più lunga delle
        // etichette, uguale in tutte le righe perché i titoli restino allineati. Un'etichetta come
        // «Primavera 2027» usciva dalla colonna e sul telefono toccava il titolo.
        $parola = 0;
        foreach ( $items as $it ) {
            foreach ( preg_split( '/\s+/u', (string) ( $it['number'] ?? '' ) ) as $w ) {
                $parola = max( $parola, mb_strlen( $w ) );
            }
        }
        $num_w = (int) ceil( $parola * $number_size * 0.62 );

        // grid columns: numero + titolo + [categoria] + [anno] + [freccia]
        // minmax(0,…): con «1fr» la colonna del titolo non scendeva sotto la parola più lunga e la riga
        // usciva dallo schermo del telefono.
        $cols = max( 48, $num_w ) . 'px minmax(0,1fr)';
        if ( $show_cat )   $cols .= ' auto';
        if ( $show_year )  $cols .= ' auto';
        if ( $show_arrow ) $cols .= ' auto';
        // Telefono: la categoria si nasconde, restano numero, titolo e le colonne accese.
        $cols_m = max( 32, $num_w ) . 'px minmax(0,1fr)';
        if ( $show_year )  $cols_m .= ' auto';
        if ( $show_arrow ) $cols_m .= ' auto';
        // Colonne dell'anno e della freccia nella griglia stretta (la categoria va sotto il titolo).
        $col_anno    = 3;
        $col_freccia = $show_year ? 4 : 3;

        // Griglia e padding della riga stanno nel <style> sotto, non in linea: in linea battevano
        // sia il :hover (l'«Indentazione hover» non spostava niente) sia la regola del telefono
        // (colonne e spazi restavano quelli del desktop). In linea resta solo la linea divisoria.
        // L'indentazione si AGGIUNGE al padding della riga: 0 = la riga non si muove.
        $hover_pad_l = (int) $row_pad['left'] + $indent;
        $hover_pad_r = (int) $row_pad['right'] + $indent;

        $row_base = 'border-bottom:1px solid ' . $line . ';';

        ob_start();
        ?>
        <div class="olo-worklist <?php echo esc_attr( $uid ); ?>" style="border-top:1px solid <?php echo esc_attr( $line ); ?>;">
            <?php foreach ( $items as $idx => $it ) :
                $number   = $it['number'] ?? '';
                $title    = $it['title'] ?? '';
                $category = $it['category'] ?? '';
                $year     = $it['year'] ?? '';
                $link     = $it['link_url'] ?? '';
                $tag      = $link ? 'a' : 'div';
                $attrs    = $link ? ' href="' . esc_url( $link ) . '"' : '';
            ?>
                <<?php echo $tag . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is a fixed 'a'/'div' literal; $attrs assembled above from a fixed literal + esc_url()'d link ?> class="olo-worklist__row" style="<?php echo esc_attr( $row_base ); ?>">
                    <span class="olo-worklist__n" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $number_size; ?>px;color:<?php echo esc_attr( $num_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.number'; ?>"><?php echo esc_html( $number ); ?></span>
                    <span class="olo-worklist__t" style="font-family:<?php echo esc_attr( $tfam ); ?>;font-weight:<?php echo esc_attr( $title_wt ); ?>;font-size:<?php echo (int) $title_size; ?>px;line-height:1;letter-spacing:-0.03em;color:<?php echo esc_attr( $title_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.title'; ?>"><?php echo esc_html( $title ); ?></span>
                    <?php if ( $show_cat ) : ?>
                        <span class="olo-worklist__cat" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $cat_size; ?>px;text-transform:uppercase;letter-spacing:0.02em;color:<?php echo esc_attr( $cat_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.category'; ?>"><?php echo esc_html( $category ); ?></span>
                    <?php endif; ?>
                    <?php if ( $show_year ) : ?>
                        <span class="olo-worklist__yr" style="font-family:<?php echo esc_attr( $mono ); ?>;font-size:<?php echo (int) $year_size; ?>px;color:<?php echo esc_attr( $year_c ); ?>;" data-olo-editable="<?php echo 'items.' . intval( $idx ) . '.year'; ?>"><?php echo esc_html( $year ); ?></span>
                    <?php endif; ?>
                    <?php if ( $show_arrow ) : ?>
                        <svg class="olo-worklist__arrow" viewBox="0 0 24 24" fill="none" stroke="<?php echo esc_attr( $arrow_c ); ?>" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                    <?php endif; ?>
                </<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'a'/'div' literal from the ternary above ?>>
            <?php endforeach; ?>
        </div>
        <?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- inline CSS below is built exclusively from values sanitized above: colours via the safe_color_css() whitelist, indent and paddings via absint()/intval() with min()/max() clamps, grid columns from fixed literals plus an integer width computed from the labels, cursor a fixed-literal ternary; $uid is internally generated. ?>
        <style>
            .<?php echo $uid; ?> .olo-worklist__row { display: grid; grid-template-columns: <?php echo $cols; ?>; gap: 24px; align-items: center; padding: <?php echo $row_pad_css; ?>; cursor: <?php echo $items && ! empty( $items[0]['link_url'] ) ? 'pointer' : 'default'; ?>; transition: padding .25s ease, background .25s ease; color: inherit; text-decoration: none; }
            .<?php echo $uid; ?> .olo-worklist__row:hover { padding-left: <?php echo $hover_pad_l; ?>px; padding-right: <?php echo $hover_pad_r; ?>px; background: <?php echo $hover_bg; ?>; }
            .<?php echo $uid; ?> .olo-worklist__t { transition: transform .25s ease; }
            .<?php echo $uid; ?> .olo-worklist__row:hover .olo-worklist__t { transform: translateX(6px); }
            .<?php echo $uid; ?> .olo-worklist__arrow { width: 20px; height: 20px; opacity: 0; transform: translateX(-6px); transition: opacity .25s ease, transform .25s ease; justify-self: end; }
            .<?php echo $uid; ?> .olo-worklist__row:hover .olo-worklist__arrow { opacity: 1; transform: none; }
            .<?php echo $uid; ?> a.olo-worklist__row:focus-visible { outline: 2px solid <?php echo $arrow_c; ?>; outline-offset: -2px; }
            <?php if ( $title_t ) : ?>
            @media (max-width: 960px) {
                .<?php echo $uid; ?> .olo-worklist__t { font-size: <?php echo (int) $title_t; ?>px !important; }
            }
            <?php endif; ?>
            /* Si adatta alla larghezza della SUA colonna, non solo dello schermo (16 nov 2026): in mezza pagina i titoli
               andavano a capo e la categoria si schiacciava; sul telefono la categoria spariva. Contenitore con nome, come
               Hover List e Info Cards (olo-cell c'è solo nel builder). */
            .<?php echo $uid; ?> { container-type: inline-size; container-name: olo-worklist; }
            @container olo-worklist (max-width: 640px) {
                .<?php echo $uid; ?> .olo-worklist__row { grid-template-columns: <?php echo $cols_m; ?>; column-gap: .875rem; row-gap: .25rem; }
                .<?php echo $uid; ?> .olo-worklist__n { grid-column: 1; grid-row: 1 / span 2; }
                .<?php echo $uid; ?> .olo-worklist__t { grid-column: 2; grid-row: 1; font-size: min(<?php echo (int) ( $title_t ? $title_t : $title_size ); ?>px, 32px) !important; overflow-wrap: anywhere; }
                .<?php echo $uid; ?> .olo-worklist__cat { grid-column: 2; grid-row: 2; }
                .<?php echo $uid; ?> .olo-worklist__yr { grid-column: <?php echo (int) $col_anno; ?>; grid-row: 1 / span 2; text-align: right; }
                .<?php echo $uid; ?> .olo-worklist__arrow { grid-column: <?php echo (int) $col_freccia; ?>; grid-row: 1 / span 2; }
            }
            @container olo-worklist (max-width: 420px) {
                .<?php echo $uid; ?> .olo-worklist__t { font-size: min(<?php echo (int) ( $title_t ? $title_t : $title_size ); ?>px, 26px) !important; }
            }
            <?php if ( $title_m ) : ?>
            @media (max-width: 640px) {
                .<?php echo $uid; ?> .olo-worklist__t { font-size: <?php echo (int) $title_m; ?>px !important; }
            }
            <?php endif; ?>
        </style>
        <?php // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php
        return ob_get_clean();
    }
}
