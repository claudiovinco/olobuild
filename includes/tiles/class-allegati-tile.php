<?php
/**
 * Tile Allegati — i file da scaricare di una notizia, di un comunicato, di una pratica: l'icona del tipo di file, il
 * nome, il formato e il peso, il link che scarica. Le voci vengono dal campo «Allegati» del contenuto che la pagina
 * mostra (Impostazioni › Tipi di contenuto) oppure si scrivono nella tile. Senza allegati sul sito non compare.
 * Stesso disegno dei documenti della Scheda tecnica. Config: src/config/elements/allegati.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Allegati_Tile extends Olobuild_Tile_Base {

    protected $type     = 'allegati';
    protected $name     = 'Allegati';
    protected $icon     = 'dashicons-paperclip';
    protected $category = 'dynamic';

    protected $defaults = [
        // contenuto
        'titolo'              => 'Allegati',
        'campo'               => '',
        'voci'                => [
            [ 'id' => 'al-1', 'nome' => 'Comunicato stampa', 'file' => '', 'formato' => 'pdf', 'peso' => '240 KB' ],
            [ 'id' => 'al-2', 'nome' => 'Foto in alta risoluzione', 'file' => '', 'formato' => 'zip', 'peso' => '18 MB' ],
            [ 'id' => 'al-3', 'nome' => 'Dati e tabelle', 'file' => '', 'formato' => 'xls', 'peso' => '96 KB' ],
        ],
        'mostra_info'         => true,
        // stile
        'accento'             => '',
        'colore_linee'        => '',
        'sfondo_voce'         => '',
        'bordo_voce'          => [],
        'titolo_tag'          => 'h3',
        'titolo_font_family'  => '',
        'titolo_font_size'    => 13,
        'titolo_font_weight'  => '600',
        'titolo_uppercase'    => true,
        'titolo_letter_spacing' => 1.5,
        'titolo_color'        => '',
        'voce_font_family'    => '',
        'voce_font_size'      => 16,
        'voce_font_weight'    => '500',
        'voce_color'          => '',
        'info_font_size'      => 13,
        'info_color'          => '',
        'raggio'              => '',
        'padding_voce'        => [],
        'colonne'             => 1,
        'gap'                 => 0,
    ];

    /** Formato → icona. Le estensioni del file si riducono a questi gruppi (formato()). */
    const ICONE = [
        'pdf'      => 'file-text',
        'doc'      => 'file-text',
        'xls'      => 'file-spreadsheet',
        'ppt'      => 'presentation',
        'zip'      => 'file-archive',
        'immagine' => 'file-image',
        'audio'    => 'music',
        'video'    => 'video',
        'altro'    => 'file',
    ];

    const ESTENSIONI = [
        'doc' => [ 'doc', 'docx', 'odt', 'rtf', 'txt', 'pages' ],
        'xls' => [ 'xls', 'xlsx', 'ods', 'csv', 'numbers' ],
        'ppt' => [ 'ppt', 'pptx', 'odp', 'key' ],
        'zip' => [ 'zip', 'rar', '7z', 'gz', 'tar' ],
        'immagine' => [ 'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'tif', 'tiff', 'heic' ],
        'audio' => [ 'mp3', 'wav', 'ogg', 'm4a', 'flac' ],
        'video' => [ 'mp4', 'mov', 'webm', 'avi', 'm4v' ],
    ];

    public function get_controls() {
        return [];
    }

    /** Il gruppo di un'estensione (pdf, doc, xls…). */
    private static function formato( $est ) {
        $est = strtolower( (string) $est );
        if ( 'pdf' === $est ) {
            return 'pdf';
        }
        foreach ( self::ESTENSIONI as $gruppo => $estensioni ) {
            if ( in_array( $est, $estensioni, true ) ) {
                return $gruppo;
            }
        }
        return 'altro';
    }

    /** CSS di un testo dal controllo tipografia (come la Scheda tecnica). */
    private function css_testo( $s, $k, $selettore ) {
        $d   = '';
        $fam = $this->resolve_font_family( $s[ $k . '_font_family' ] ?? '' );
        $fs  = (float) ( $s[ $k . '_font_size' ] ?? 0 );
        $fw  = $this->font_weight_css( $s[ $k . '_font_weight' ] ?? '' );
        $ls  = $s[ $k . '_letter_spacing' ] ?? '';
        $col = $this->safe_color_css( $s[ $k . '_color' ] ?? '' );
        if ( '' !== $fam ) {
            $d .= 'font-family:' . $fam . ';';
        }
        if ( $fs > 0 ) {
            $d .= 'font-size:' . $fs . 'px;';
        }
        if ( $fw ) {
            $d .= 'font-weight:' . $fw . ';';
        }
        if ( array_key_exists( $k . '_uppercase', $s ) ) {
            $d .= 'text-transform:' . ( ! empty( $s[ $k . '_uppercase' ] ) ? 'uppercase' : 'none' ) . ';';
        }
        if ( is_numeric( $ls ) ) {
            $d .= 'letter-spacing:' . (float) $ls . 'px;';
        }
        if ( $col ) {
            $d .= 'color:' . $col . ';';
        }
        return '' !== $d ? "{$selettore}{{$d}}" : '';
    }

    /** Le voci da mostrare: quelle del campo del contenuto, o quelle scritte nella tile. */
    private function voci( $s, $builder, &$da_campo ) {
        $campo    = sanitize_key( (string) $s['campo'] );
        $da_campo = '' !== $campo;
        $voci     = [];
        if ( $da_campo && class_exists( 'Olobuild_Tipi_Contenuto' ) ) {
            foreach ( Olobuild_Tipi_Contenuto::allegati( $campo ) as $a ) {
                $voci[] = [ 'nome' => $a['titolo'], 'file' => $a['url'], 'formato' => self::formato( $a['est'] ), 'est' => $a['est'], 'peso' => $a['peso'] ];
            }
            // Nel builder, senza un contenuto con allegati, si vedono le voci d'esempio della tile.
            if ( $voci || ! $builder ) {
                return $voci;
            }
            $da_campo = false;
        }
        foreach ( (array) $s['voci'] as $v ) {
            if ( ! is_array( $v ) ) {
                continue;
            }
            $file = is_array( $v['file'] ?? null ) ? (string) ( $v['file']['url'] ?? '' ) : trim( (string) ( $v['file'] ?? '' ) );
            $nome = trim( (string) ( $v['nome'] ?? '' ) );
            if ( ( '' === $file && ! $builder ) || ( '' === $file && '' === $nome ) ) {
                continue;
            }
            $est    = strtoupper( (string) pathinfo( (string) wp_parse_url( $file, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
            $fmt    = (string) ( $v['formato'] ?? '' );
            $fmt    = isset( self::ICONE[ $fmt ] ) ? $fmt : self::formato( $est );
            $voci[] = [ 'nome' => $nome, 'file' => $file, 'formato' => $fmt, 'est' => '' !== $est ? $est : ( 'altro' === $fmt ? '' : strtoupper( $fmt ) ), 'peso' => trim( (string) ( $v['peso'] ?? '' ) ) ];
        }
        return $voci;
    }

    public function render( $settings ) {
        $s       = wp_parse_args( $settings, $this->defaults );
        $uid     = 'oall-' . $this->chiave_stabile( $settings );
        $sel     = '#' . $uid;
        $builder = ! empty( $s['_builder_mode'] );
        $voci    = $this->voci( $s, $builder, $da_campo );
        if ( ! $voci ) {
            return $builder ? '<p class="oall-vuoto" style="margin:0;padding:1rem;opacity:.7;border:1px dashed currentColor;border-radius:.5rem">' . esc_html__( 'Aggiungi i file nel Contenuto, o scegli il campo Allegati del contenuto.', 'olobuilder' ) . '</p>' : '';
        }

        $accento = $this->safe_color_css( $s['accento'] ) ?: 'var(--olo-color-primary)';
        $linea   = $this->safe_color_css( $s['colore_linee'] ) ?: 'color-mix(in srgb, currentColor 14%, transparent)';
        $sfondo  = $this->safe_color_css( $s['sfondo_voce'] );
        $bordo   = Olobuild_Tile_Utils::border_css( $s['bordo_voce'] ?? null );
        $raggio  = Olobuild_Tile_Utils::border_radius( $s['raggio'] ) ?: '0';
        $riquadro = '' !== $sfondo || '' !== $bordo;
        $pad     = Olobuild_Tile_Utils::sides_css( Olobuild_Tile_Utils::spacing_sides( $s['padding_voce'] ?? null, [], $riquadro ? [ 14, 16, 14, 16 ] : [ 12, 0, 12, 0 ] ) );
        $colonne = max( 1, min( 3, (int) $s['colonne'] ) );
        $gap     = max( 0, min( 80, (int) $s['gap'] ) );
        $tag     = in_array( (string) $s['titolo_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' ], true ) ? (string) $s['titolo_tag'] : 'h3';
        $titolo  = trim( (string) $s['titolo'] );

        ob_start();
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da colori passati da safe_color_css() e dagli helper di Olobuild_Tile_Utils, numeri forzati e selettori generati qui
        echo '<style>';
        echo "{$sel}{--oall-acc:{$accento};--oall-linea:{$linea};--oall-r:{$raggio}}";
        echo "{$sel} .oall-tit{margin:0 0 .5rem;opacity:.75}";
        echo "{$sel} .oall-lista{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat({$colonne},minmax(0,1fr));gap:{$gap}px " . ( $colonne > 1 ? max( 16, $gap ) : 0 ) . 'px}';
        echo "{$sel} .oall-link{display:flex;align-items:center;gap:.85rem;padding:{$pad};color:inherit;text-decoration:none;" . ( $riquadro ? "border-radius:var(--oall-r);{$bordo}" . ( '' !== $sfondo ? "background:{$sfondo};" : '' ) : 'border-bottom:1px solid var(--oall-linea);' ) . 'transition:color .2s}';
        echo "{$sel} .oall-link:hover .oall-nome{color:var(--oall-acc)}";
        echo "{$sel} .oall-link:focus-visible{outline:2px solid var(--oall-acc);outline-offset:3px}";
        echo "{$sel} .oall-ico{flex:none;display:flex;color:var(--oall-acc)}";
        echo "{$sel} .oall-ico .olo-lucide-icon,{$sel} .oall-ico svg{width:1.35em;height:1.35em}";
        echo "{$sel} .oall-nome{flex:1;min-width:0;overflow-wrap:anywhere;transition:color .2s}";
        echo "{$sel} .oall-info{flex:none;opacity:.7;font-variant-numeric:tabular-nums;white-space:nowrap}";
        echo "{$sel} .oall-giu{flex:none;display:flex;opacity:.6}";
        echo "{$sel} .oall-giu .olo-lucide-icon,{$sel} .oall-giu svg{width:1.1em;height:1.1em}";
        echo "{$sel} .oall-senza{opacity:.55}";
        echo "@media (max-width:640px){{$sel} .oall-lista{grid-template-columns:minmax(0,1fr)}}";
        echo $this->css_testo( $s, 'titolo', "{$sel} .oall-tit" );
        echo $this->css_testo( $s, 'voce', "{$sel} .oall-link" );
        echo $this->css_testo( $s, 'info', "{$sel} .oall-info" );
        echo '</style>';

        echo '<div id="' . esc_attr( $uid ) . '" class="oall">';
        if ( '' !== $titolo ) {
            echo '<' . $tag . ' class="oall-tit">' . esc_html( $titolo ) . '</' . $tag . '>';
        }
        echo '<ul class="oall-lista">';
        $giu = $this->render_icon_html( 'download', 0.9, 'aria-hidden="true"' );
        foreach ( $voci as $v ) {
            $info  = ! empty( $s['mostra_info'] ) ? trim( $v['est'] . ( '' !== $v['est'] && '' !== $v['peso'] ? ' · ' : '' ) . $v['peso'] ) : '';
            $nome  = '' !== $v['nome'] ? $v['nome'] : basename( (string) wp_parse_url( $v['file'], PHP_URL_PATH ) );
            $testo = '<span class="oall-ico">' . $this->render_icon_html( self::ICONE[ $v['formato'] ], 1, 'aria-hidden="true"' ) . '</span>'
                . '<span class="oall-nome">' . esc_html( $nome ) . '</span>'
                . ( '' !== $info ? '<span class="oall-info">' . esc_html( $info ) . '</span>' : '' );
            if ( '' !== $v['file'] ) {
                echo '<li><a class="oall-link" href="' . esc_url( $v['file'] ) . '" download>' . $testo . '<span class="oall-giu">' . $giu . '</span></a></li>';
            } else {
                echo '<li class="oall-senza"><span class="oall-link">' . $testo . '<span class="oall-info">' . esc_html__( 'manca il file', 'olobuilder' ) . '</span></span></li>';
            }
        }
        echo '</ul>';
        if ( $builder && ! $da_campo && '' !== sanitize_key( (string) $s['campo'] ) ) {
            echo '<p class="oall-nota" style="margin:.5rem 0 0;font-size:.75em;opacity:.6">' . esc_html__( 'Sul sito: gli allegati del contenuto. Qui: le voci d\'esempio.', 'olobuilder' ) . '</p>';
        }
        echo '</div>';
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
