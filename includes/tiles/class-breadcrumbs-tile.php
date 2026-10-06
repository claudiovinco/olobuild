<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Breadcrumbs_Tile extends Olobuild_Tile_Base {

    protected $type     = 'breadcrumbs';
    protected $name     = 'Breadcrumbs';
    protected $icon     = 'dashicons-arrow-right-alt2';
    protected $category = 'navigation';
    protected $defaults = [
        'preset' => 'custom',
        'separator'    => '/',
        'home_label'   => 'Home',
        'show_home'    => true,
        'show_current' => true,
    ];

    public function get_controls() {
        return [
            [ 'key' => 'separator',    'type' => 'text',   'label' => 'Separatore' ],
            [ 'key' => 'home_label',   'type' => 'text',   'label' => 'Etichetta Home' ],
            [ 'key' => 'show_home',    'type' => 'toggle', 'label' => 'Mostra Home' ],
            [ 'key' => 'show_current', 'type' => 'toggle', 'label' => 'Mostra pagina corrente' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;

        // «Separatore»: UIkit disegna il suo «/» fisso con un ::before e il campo non arrivava mai
        // in pagina (neanche i separatori dei preset: · | › → >). Va nel content di quel ::before.
        $separator  = $this->css_content_string( trim( wp_strip_all_tags( (string) $s['separator'] ) ) ?: '/' );
        $home_label = esc_html( $s['home_label'] ?: 'Home' );
        $show_home  = ! empty( $s['show_home'] );
        $show_curr  = ! empty( $s['show_current'] );

        $items = [];

        // Home link
        if ( $show_home ) {
            $items[] = '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . $home_label . '</a></li>';
        }

        if ( $post && ! is_front_page() ) {
            // Get ancestors
            $ancestors = get_post_ancestors( $post );
            $ancestors = array_reverse( $ancestors );

            foreach ( $ancestors as $ancestor_id ) {
                $items[] = '<li><a href="' . esc_url( get_permalink( $ancestor_id ) ) . '">' . esc_html( get_the_title( $ancestor_id ) ) . '</a></li>';
            }

            // Current page
            if ( $show_curr ) {
                $items[] = '<li><span aria-current="page">' . esc_html( get_the_title( $post->ID ) ) . '</span></li>';
            }
        } elseif ( is_category() ) {
            $items[] = '<li><span aria-current="page">' . esc_html( single_cat_title( '', false ) ) . '</span></li>';
        } elseif ( is_tag() ) {
            $items[] = '<li><span aria-current="page">' . esc_html( single_tag_title( '', false ) ) . '</span></li>';
        } elseif ( is_search() ) {
            $items[] = '<li><span aria-current="page">' . esc_html__( 'Risultati ricerca', 'olobuild' ) . '</span></li>';
        } elseif ( is_404() ) {
            $items[] = '<li><span aria-current="page">' . esc_html__( 'Pagina non trovata', 'olobuild' ) . '</span></li>';
        }

        if ( empty( $items ) ) {
            return '';
        }

        $uid = 'olo-bc-' . wp_unique_id();

        ob_start();
        ?>
        <nav class="olo-breadcrumbs <?php echo esc_attr( $uid ); ?> olo-bc-preset-<?php echo esc_attr( sanitize_key( $s['preset'] ?? 'custom' ) ); ?>" aria-label="<?php echo esc_attr( olobuild_t( 'Breadcrumb' ) ); ?>">
            <ul class="uk-breadcrumb">
                <?php echo implode( "\n", $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each list item is assembled above exclusively from esc_url()/esc_html()/esc_html__() output and literal markup. ?>
            </ul>
        </nav>
        <style>
            /* a11y tastiera: anello di focus visibile sui link del breadcrumb */
            .olo-breadcrumbs a:focus-visible {
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
                border-radius: 3px;
            }
            <?php
            // Separatore scelto, e i colori di UIkit fuori tema (#666 sulla voce corrente, #999 sul
            // separatore): prendono il colore del testo dove sta la tile, il separatore attenuato.
            // Così restano leggibili anche su una sezione scura o in .uk-light.
            echo '.' . esc_attr( $uid ) . ' .uk-breadcrumb>:nth-child(n+2):not(.uk-first-column)::before{content:' . $separator . ';color:inherit;opacity:.5}'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $separator da css_content_string(): solo escape esadecimali fra virgolette
            echo '.' . esc_attr( $uid ) . ' .uk-breadcrumb>:last-child>span,.' . esc_attr( $uid ) . ' .uk-breadcrumb>:last-child>a:not([href]){color:inherit}';
            ?>
        </style>
        <?php
        echo $this->stile_bordo( $s, '.' . $uid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS da Olobuild_Tile_Base::stile_bordo() (impostazioni sanificate, uid interno)
        return ob_get_clean();
    }

    /**
     * Una stringa come valore CSS di `content`, fra virgolette: ogni carattere in escape
     * esadecimale («\2f »), così nessun carattere scritto dall'utente entra nel CSS com'è
     * (virgolette, barre rovesciate, </style>). Massimo 8 caratteri: è un separatore.
     */
    private function css_content_string( $txt ) {
        $chars = preg_split( '//u', (string) $txt, -1, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $chars ) || ! $chars ) {
            $chars = [ '/' ];
        }
        $out = '';
        foreach ( array_slice( $chars, 0, 8 ) as $ch ) {
            $cp   = function_exists( 'mb_ord' ) ? mb_ord( $ch, 'UTF-8' ) : ord( $ch );
            $out .= '\\' . dechex( (int) $cp ) . ' ';
        }
        return '"' . $out . '"';
    }
}
