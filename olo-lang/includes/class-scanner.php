<?php
/**
 * Olo Lang — Scanner: estrae stringhe traducibili dai template Olobuild.
 *
 * Analizza l'albero JSON dei tile e identifica automaticamente
 * quali campi contengono testo traducibile (titoli, descrizioni, contenuti)
 * distinguendoli da campi tecnici (colori, dimensioni, URL, CSS).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Scanner {

    /**
     * Nomi di campo noti come traducibili.
     */
    private static $translatable_keys = [
        'text', 'title', 'heading', 'subtitle', 'subheading', 'content',
        'description', 'label', 'quote', 'name', 'role', 'bio', 'caption',
        'placeholder', 'alt', 'button_text', 'prefix', 'suffix', 'period',
        'price', 'author', 'html', 'date', 'front_title', 'front_text',
        'back_title', 'back_text', 'back_button_text', 'code',
        'footer_text', 'header_text', 'link_text', 'more_text',
        'badge', 'message', 'question', 'answer',
    ];

    /**
     * Nomi di campo esplicitamente NON traducibili.
     */
    private static $non_translatable_keys = [
        'type', 'id', 'tag', 'alignment', 'decoration', 'style', 'size',
        'mode', 'layout', 'gap', 'width', 'height', 'target', 'rel',
        'icon', 'animation', 'effect', 'variant', 'position', 'columns',
        'custom_widths', 'stack_mobile', 'vertical_align', 'full_width',
        'font_family', 'font_weight', 'font_size', 'line_height',
        'letter_spacing', 'text_transform', 'text_align',
    ];

    /**
     * Chiavi che contengono array di sotto-elementi (items, panels, slides...).
     */
    private static $items_keys = [
        'items', 'panels', 'slides', 'markers', 'cells', 'features',
        'steps', 'members', 'tabs', 'options', 'links', 'buttons',
    ];

    /**
     * Scansiona un intero template e restituisce le stringhe traducibili.
     *
     * @param array $content  Albero JSON dei tile.
     * @return array  [ { tile_id, tile_type, field_path, value, context }, ... ]
     */
    public static function scan_template( $content ) {
        $strings = [];
        if ( ! is_array( $content ) ) {
            return $strings;
        }

        foreach ( $content as $node ) {
            self::scan_node( $node, $strings );
        }

        return $strings;
    }

    /**
     * Scansiona un singolo nodo (tile) ricorsivamente.
     */
    private static function scan_node( $node, &$strings ) {
        if ( ! is_array( $node ) ) {
            return;
        }

        $type     = $node['type'] ?? '';
        $id       = $node['id'] ?? '';
        $settings = $node['settings'] ?? [];

        if ( ! empty( $settings ) && is_array( $settings ) ) {
            self::scan_settings( $settings, $id, $type, '', $strings );
        }

        // Ricorsione nei figli (section > row > column > elemento)
        if ( ! empty( $node['children'] ) && is_array( $node['children'] ) ) {
            foreach ( $node['children'] as $child ) {
                self::scan_node( $child, $strings );
            }
        }
    }

    /**
     * Scansiona l'oggetto settings di un tile per trovare stringhe traducibili.
     */
    private static function scan_settings( $settings, $tile_id, $tile_type, $prefix, &$strings ) {
        foreach ( $settings as $key => $value ) {
            $field_path = $prefix ? $prefix . '.' . $key : $key;

            // Salta chiavi non traducibili
            if ( self::is_non_translatable_key( $key ) ) {
                continue;
            }

            // Array di sotto-elementi (items, panels, slides...)
            if ( in_array( $key, self::$items_keys, true ) && is_array( $value ) ) {
                foreach ( $value as $idx => $item ) {
                    if ( is_array( $item ) ) {
                        self::scan_settings( $item, $tile_id, $tile_type, $field_path . '[' . $idx . ']', $strings );
                    }
                }
                continue;
            }

            // Verifica se e' una stringa traducibile
            if ( is_string( $value ) && self::is_translatable_value( $key, $value ) ) {
                $strings[] = [
                    'tile_id'    => $tile_id,
                    'tile_type'  => $tile_type,
                    'field_path' => $field_path,
                    'value'      => $value,
                    'context'    => self::get_field_context( $tile_type, $field_path ),
                ];
            }
        }
    }

    /**
     * Verifica se una chiave e' nota come non traducibile.
     */
    private static function is_non_translatable_key( $key ) {
        if ( in_array( $key, self::$non_translatable_keys, true ) ) {
            return true;
        }

        // Campi colore
        if ( strpos( $key, 'color' ) !== false || strpos( $key, '_clr' ) !== false ) {
            return true;
        }

        // Campi dimensione/layout
        if ( preg_match( '/(width|height|size|radius|spacing|gap|margin|padding|weight|opacity|scale|rotate|blur|delay|duration|offset|zindex)/', $key ) ) {
            return true;
        }

        // Campi URL (ma non url_text o link_text)
        if ( in_array( $key, [ 'url', 'href', 'src', 'video_url', 'image_url', 'poster', 'background_image' ], true ) ) {
            return true;
        }
        if ( preg_match( '/_url$/', $key ) && strpos( $key, 'text' ) === false ) {
            return true;
        }

        // Campi immagine
        if ( in_array( $key, [ 'image', 'thumbnail', 'hover_image', 'hover_video', 'front_image', 'back_image', 'bg_image' ], true ) ) {
            return true;
        }

        // Campi booleani/toggle
        if ( preg_match( '/^(enable|show_|hide_|is_|has_)/', $key ) ) {
            return true;
        }

        // Campi CSS/transform
        if ( strpos( $key, 'transform' ) !== false || strpos( $key, 'parallax' ) !== false || $key === 'custom_css' ) {
            return true;
        }

        // Campi hover (stili, non testo)
        if ( preg_match( '/^hover_(bg|border|shadow|effect|opacity|scale)/', $key ) ) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se un valore e' effettivamente testo traducibile.
     */
    private static function is_translatable_value( $key, $value ) {
        $v = trim( $value );

        if ( $v === '' ) {
            return false;
        }

        // Chiave nota come traducibile: fidati
        if ( in_array( $key, self::$translatable_keys, true ) ) {
            return true;
        }

        // Valori CSS
        if ( preg_match( '/^[0-9.]+(px|em|rem|%|vh|vw|s|ms)?$/', $v ) ) {
            return false;
        }

        // Colori hex
        if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $v ) ) {
            return false;
        }

        // URL
        if ( preg_match( '#^https?://#', $v ) ) {
            return false;
        }

        // Numeri puri
        if ( is_numeric( $v ) ) {
            return false;
        }

        // Classi CSS (stringhe corte senza spazi)
        if ( preg_match( '/^[a-z0-9_-]+$/i', $v ) && strlen( $v ) < 30 ) {
            return false;
        }

        // Chiave con nome che suggerisce testo
        if ( preg_match( '/(text|title|heading|label|name|desc|content|caption|quote|bio|role|placeholder|message|badge)/i', $key ) ) {
            return true;
        }

        // Contenuto HTML (probabilmente traducibile)
        if ( preg_match( '/<[a-z][\s\S]*>/i', $v ) ) {
            return true;
        }

        // Testo con spazi (probabilmente leggibile da umano)
        if ( strpos( $v, ' ' ) !== false && strlen( $v ) > 3 ) {
            return true;
        }

        return false;
    }

    /**
     * Contesto leggibile per un campo.
     */
    public static function get_field_context( $tile_type, $field_path ) {
        $type_names = [
            'headline'      => 'Titolo',
            'button'        => 'Pulsante',
            'content'       => 'Contenuto',
            'alert'         => 'Avviso',
            'accordion'     => 'Accordion',
            'hero'          => 'Hero',
            'panel'         => 'Pannello',
            'image'         => 'Immagine',
            'overlay'       => 'Overlay',
            'testimonial'   => 'Testimonial',
            'team'          => 'Team',
            'pricing'       => 'Pricing',
            'quotation'     => 'Citazione',
            'list'          => 'Lista',
            'timeline'      => 'Timeline',
            'slideshow'     => 'Slideshow',
            'gallery'       => 'Galleria',
            'popup'         => 'Popup',
            'flipcard'      => 'FlipCard',
            'counter'       => 'Contatore',
            'progress'      => 'Barra progresso',
            'table'         => 'Tabella',
            'video'         => 'Video',
            'search'        => 'Ricerca',
            'iconbox'       => 'Icon Box',
            'icon'          => 'Icona',
            'code'          => 'Codice',
            'html'          => 'HTML',
            'desclist'      => 'Lista descrittiva',
            'marquee'       => 'Marquee',
            'nav'           => 'Navigazione',
            'navmenu'       => 'Menu navigazione',
            'overlaygrid'   => 'Griglia overlay',
            'overlayslider' => 'Slider overlay',
            'panelslider'   => 'Slider pannelli',
            'popover'       => 'Popover',
            'social'        => 'Social',
            'subnav'        => 'Sotto-navigazione',
            'switcher'      => 'Switcher',
            'switcherpanel' => 'Pannello switcher',
            'togglebtn'     => 'Pulsante toggle',
            'section'       => 'Sezione',
            'row'           => 'Riga',
            'column'        => 'Colonna',
        ];

        $type_label = $type_names[ $tile_type ] ?? ucfirst( $tile_type );

        $field = preg_replace( '/\[\d+\]/', '[]', $field_path );
        $field = str_replace( '.', ' > ', $field );

        return $type_label . ' / ' . $field;
    }
}
