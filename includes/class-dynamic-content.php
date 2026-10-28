<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olobuild_Dynamic_Content {

    /**
     * Recursion guard to prevent infinite loops when resolving content fields.
     */
    private static $resolving = false;

    /**
     * Resolve a single field binding.
     *
     * @param string $source  Source key (current_post, site, custom_field, acf, taxonomy_field, author).
     * @param string $field   Field key within the source.
     * @param int    $post_id WordPress post ID.
     * @return mixed|null Resolved value or null.
     */
    public function resolve_field( $source, $field, $post_id = 0 ) {
        if ( ! $post_id ) {
            $post_id = get_the_ID();
        }

        // Spettacolo: i campi della pagina dello spettacolo (Olobuild_Spettacoli), fuori dalla cache: nella demo di
        // un tema la voce mostrata cambia a parità di post, e la classe tiene già gli spettacoli della richiesta.
        if ( 'spettacolo' === $source ) {
            return class_exists( 'Olobuild_Spettacoli' ) ? Olobuild_Spettacoli::campo( $field, $post_id ) : null;
        }

        // Campi dei contenuti: i campi dei tipi delle Impostazioni (e degli Articoli), nel formato da mostrare.
        if ( 'campi' === $source ) {
            return class_exists( 'Olobuild_Tipi_Contenuto' ) ? Olobuild_Tipi_Contenuto::campo( $field, $post_id ) : null;
        }

        // Object cache: avoid repeated queries when multiple tiles resolve the same field.
        $cache_key = "olo_dyn_{$source}_{$field}_{$post_id}";
        $cached    = wp_cache_get( $cache_key, 'olo_dynamic' );
        if ( false !== $cached ) {
            return $cached;
        }

        switch ( $source ) {
            case 'current_post':
                $value = $this->resolve_current_post( $field, $post_id );
                break;

            case 'site':
                $value = $this->resolve_site( $field );
                break;

            case 'custom_field':
                $value = get_post_meta( $post_id, $field, true );
                $value = $this->adapt_acf_value( $value );
                break;

            case 'acf':
                if ( function_exists( 'get_field' ) ) {
                    $value = get_field( $field, $post_id );
                    $value = $this->adapt_acf_value( $value );
                } else {
                    $value = null;
                }
                break;

            case 'taxonomy_field':
                $value = $this->resolve_taxonomy_field( $field, $post_id );
                break;

            case 'author':
                $value = $this->resolve_author( $field, $post_id );
                break;

            case 'user':
                $value = $this->resolve_user( $field );
                break;

            case 'datetime':
                $value = $this->resolve_datetime( $field );
                break;

            case 'request':
                $value = $this->resolve_request( $field );
                break;

            case 'archive':
                $value = $this->resolve_archive( $field );
                break;

            case 'woocommerce':
                $value = $this->resolve_woocommerce( $field, $post_id );
                break;

            case 'acf_option':
                if ( function_exists( 'get_field' ) ) {
                    $value = get_field( $field, 'option' );
                    $value = $this->adapt_acf_value( $value );
                } else {
                    $value = null;
                }
                break;

            case 'acf_repeater':
                if ( function_exists( 'get_field' ) ) {
                    $value = $this->resolve_acf_repeater( $field, $post_id );
                } else {
                    $value = null;
                }
                break;

            case 'comments':
                $value = $this->resolve_comments( $field, $post_id );
                break;

            case 'menu':
                $value = $this->resolve_menu( $field );
                break;

            case 'media':
                $value = $this->resolve_media( $field, $post_id );
                break;

            case 'shortcode':
                $value = do_shortcode( '[' . sanitize_text_field( $field ) . ']' );
                break;

            case 'cookie':
                $key = sanitize_text_field( $field );
                $allowed_cookies = apply_filters( 'olobuild_allowed_cookies', [ 'language', 'theme_mode', 'currency', 'olo_consent' ] );
                if ( ! in_array( $key, $allowed_cookies, true ) ) {
                    $value = '';
                } else {
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only di un cookie da allowlist per visualizzazione; nessuna modifica di stato; valore sanitizzato con wp_unslash + sanitize_text_field.
                    $value = isset( $_COOKIE[ $key ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $key ] ) ) : '';
                }
                break;

            case 'post_navigation':
                $value = $this->resolve_post_navigation( $field, $post_id );
                break;

            case 'global_widget':
                $tpl_id = intval( $field );
                if ( $tpl_id > 0 ) {
                    $renderer = new Olobuild_Frontend_Renderer();
                    $value = $renderer->render_shortcode( [ 'id' => $tpl_id ] );
                } else {
                    $value = '';
                }
                break;

            default:
                $value = null;
                break;
        }

        wp_cache_set( $cache_key, $value, 'olo_dynamic' );
        return $value;
    }

    /**
     * Resolve a current_post field.
     */
    private function resolve_current_post( $field, $post_id ) {
        switch ( $field ) {
            case 'post_title':
                return get_the_title( $post_id );

            case 'post_excerpt':
                $post = get_post( $post_id );
                if ( $post ) {
                    return $post->post_excerpt ?: wp_trim_words( $post->post_content, 30, '...' );
                }
                return '';

            case 'post_content':
                $post = get_post( $post_id );
                if ( ! $post ) return '';
                // Use wpautop only — NOT apply_filters('the_content') or do_shortcode()
                // to avoid infinite recursion with auto_render_template.
                return wpautop( $post->post_content );

            case 'featured_image':
                $url = get_the_post_thumbnail_url( $post_id, 'large' );
                return $url ?: '';

            case 'post_date':
                return get_the_date( '', $post_id );

            case 'author_name':
                $post = get_post( $post_id );
                return $post ? get_the_author_meta( 'display_name', $post->post_author ) : '';

            case 'permalink':
                return get_permalink( $post_id );

            case 'first_term':
                // Il nome del primo termine, dalle tassonomie pubbliche del tipo di contenuto: prima le
                // gerarchiche (categorie), poi le altre (tag). Niente post_format: col suo filtro di WP,
                // wp_get_object_terms( …, fields => names ) restituiva nomi VUOTI per tutte le tassonomie.
                // get_the_terms legge la cache dei termini che la query degli elenchi ha già caricato.
                $post_obj = get_post( $post_id );
                if ( ! $post_obj ) return '';
                $taxonomies = array_filter( get_object_taxonomies( $post_obj->post_type, 'objects' ), function ( $tx ) {
                    return $tx->public && 'post_format' !== $tx->name;
                } );
                uasort( $taxonomies, function ( $a, $b ) { return (int) $b->hierarchical - (int) $a->hierarchical; } );
                foreach ( $taxonomies as $tx ) {
                    $terms = get_the_terms( $post_obj, $tx->name );
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                        return $terms[0]->name;
                    }
                }
                return '';

            default:
                // Try as a post field
                $post = get_post( $post_id );
                if ( $post && isset( $post->$field ) ) {
                    return $post->$field;
                }
                return null;
        }
    }

    /**
     * Resolve a site field.
     */
    private function resolve_site( $field ) {
        switch ( $field ) {
            case 'name':
                return get_bloginfo( 'name' );
            case 'description':
                return get_bloginfo( 'description' );
            case 'url':
                return home_url( '/' );
            default:
                return get_bloginfo( $field );
        }
    }

    /**
     * Resolve a taxonomy field. Format: "taxonomy_name:property" (e.g. "category:name").
     */
    private function resolve_taxonomy_field( $field, $post_id ) {
        $parts = explode( ':', $field, 2 );
        $taxonomy = $parts[0] ?? 'category';
        $property = $parts[1] ?? 'name';

        $terms = get_the_terms( $post_id, $taxonomy );
        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return '';
        }

        $first_term = $terms[0];
        switch ( $property ) {
            case 'name':
                return $first_term->name;
            case 'link':
                return get_term_link( $first_term );
            case 'description':
                return $first_term->description;
            case 'names':
                return implode( ', ', wp_list_pluck( $terms, 'name' ) );
            default:
                return $first_term->name;
        }
    }

    /**
     * Resolve an author field.
     */
    private function resolve_author( $field, $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post ) {
            return null;
        }
        $author_id = $post->post_author;

        switch ( $field ) {
            case 'author_display_name':
            case 'display_name':
                return get_the_author_meta( 'display_name', $author_id );

            case 'author_bio':
            case 'description':
                return get_the_author_meta( 'description', $author_id );

            case 'author_email':
            case 'user_email':
                return get_the_author_meta( 'user_email', $author_id );

            case 'author_avatar':
                return get_avatar_url( $author_id, [ 'size' => 96 ] ) ?: '';

            case 'author_url':
            case 'user_url':
                return get_the_author_meta( 'user_url', $author_id );

            case 'author_posts_url':
                return get_author_posts_url( $author_id );

            default:
                return get_the_author_meta( $field, $author_id );
        }
    }

    /**
     * Resolve a user (logged-in) field.
     */
    private function resolve_user( $field ) {
        if ( ! is_user_logged_in() ) {
            return '';
        }
        $user = wp_get_current_user();

        switch ( $field ) {
            case 'user_display_name':
                return $user->display_name;

            case 'user_email':
                return $user->user_email;

            case 'user_role':
                $roles = $user->roles;
                return ! empty( $roles ) ? reset( $roles ) : '';

            case 'user_avatar':
                return get_avatar_url( $user->ID, [ 'size' => 96 ] ) ?: '';

            default:
                return isset( $user->$field ) ? $user->$field : '';
        }
    }

    /**
     * Resolve a date/time field.
     */
    private function resolve_datetime( $field ) {
        switch ( $field ) {
            case 'current_date':
                return wp_date( get_option( 'date_format' ) );

            case 'current_time':
                return wp_date( get_option( 'time_format' ) );

            case 'current_year':
                return wp_date( 'Y' );

            case 'current_day_name':
                return wp_date( 'l' );

            default:
                return wp_date( $field );
        }
    }

    /**
     * Resolve a request field.
     */
    private function resolve_request( $field ) {
        switch ( $field ) {
            case 'request_url':
                $protocol = is_ssl() ? 'https://' : 'http://';
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only di $_SERVER per comporre l'URL corrente (display); nessuna modifica di stato; valori sanitizzati con wp_unslash + sanitize_text_field / esc_url_raw.
                return $protocol . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ?? '' ) ) . esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );

            case 'request_param':
                // Field config should pass the param name as the field key in format "request_param:name"
                return '';

            case 'referrer':
                return wp_get_referer() ?: '';

            default:
                // Support request_param:param_name format
                if ( str_starts_with( $field, 'request_param:' ) ) {
                    $param_name = substr( $field, 14 );
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lettura read-only di un parametro GET per visualizzazione del relativo valore; nessuna modifica di stato; valore sanitizzato con wp_unslash + sanitize_text_field.
                    return isset( $_GET[ $param_name ] ) ? sanitize_text_field( wp_unslash( $_GET[ $param_name ] ) ) : '';
                }
                return '';
        }
    }

    /**
     * Resolve an archive field.
     */
    private function resolve_archive( $field ) {
        switch ( $field ) {
            case 'archive_title':
                if ( is_category() ) {
                    return single_cat_title( '', false );
                }
                if ( is_tag() ) {
                    return single_tag_title( '', false );
                }
                if ( is_tax() ) {
                    return single_term_title( '', false );
                }
                if ( is_post_type_archive() ) {
                    return post_type_archive_title( '', false );
                }
                if ( is_author() ) {
                    return get_the_author();
                }
                if ( is_date() ) {
                    if ( is_year() ) {
                        return get_the_date( 'Y' );
                    }
                    if ( is_month() ) {
                        return get_the_date( 'F Y' );
                    }
                    return get_the_date();
                }
                return '';

            case 'archive_description':
                return term_description() ?: '';

            default:
                return '';
        }
    }

    /**
     * Resolve a WooCommerce product/cart field.
     */
    private function resolve_woocommerce( $field, $post_id ) {
        if ( ! class_exists( 'WooCommerce' ) ) return null;

        // Cart-level fields
        if ( str_starts_with( $field, 'cart_' ) ) {
            $wc = WC();
            if ( ! $wc || ! $wc->cart ) return '';
            switch ( $field ) {
                case 'cart_total':       return $wc->cart->get_cart_total();
                case 'cart_subtotal':    return $wc->cart->get_cart_subtotal();
                case 'cart_count':       return (string) $wc->cart->get_cart_contents_count();
                case 'cart_shipping':    return $wc->cart->get_cart_shipping_total();
                case 'cart_discount':    return $wc->cart->get_discount_total();
                default:                 return '';
            }
        }

        // Product-level fields
        $product = wc_get_product( $post_id );
        if ( ! $product ) return '';

        switch ( $field ) {
            case 'product_title':       return $product->get_name();
            case 'product_price':       return $product->get_price_html();
            case 'product_regular_price': return wc_price( $product->get_regular_price() );
            case 'product_sale_price':  return $product->get_sale_price() ? wc_price( $product->get_sale_price() ) : '';
            case 'product_sku':         return $product->get_sku();
            case 'product_stock':       return $product->get_stock_status() === 'instock' ? 'Disponibile' : 'Esaurito';
            case 'product_stock_qty':   return (string) $product->get_stock_quantity();
            case 'product_short_desc':  return $product->get_short_description();
            case 'product_description': return $product->get_description();
            case 'product_weight':      return $product->get_weight();
            case 'product_dimensions':  return wc_format_dimensions( $product->get_dimensions( false ) );
            case 'product_categories':
                $terms = get_the_terms( $post_id, 'product_cat' );
                return ( $terms && ! is_wp_error( $terms ) ) ? implode( ', ', wp_list_pluck( $terms, 'name' ) ) : '';
            case 'product_tags':
                $terms = get_the_terms( $post_id, 'product_tag' );
                return ( $terms && ! is_wp_error( $terms ) ) ? implode( ', ', wp_list_pluck( $terms, 'name' ) ) : '';
            case 'product_image':       return wp_get_attachment_url( $product->get_image_id() ) ?: '';
            case 'product_gallery':
                $ids = $product->get_gallery_image_ids();
                return $this->attachment_ids_to_gallery( $ids );
            case 'product_rating':      return (string) $product->get_average_rating();
            case 'product_review_count': return (string) $product->get_review_count();
            case 'product_url':         return $product->get_permalink();
            case 'product_add_to_cart_url': return $product->add_to_cart_url();
            default:
                // Un attributo del prodotto («attributo:pa_artista»): i valori, separati da virgola.
                if ( str_starts_with( $field, 'attributo:' ) ) {
                    return (string) $product->get_attribute( sanitize_title( substr( $field, 10 ) ) );
                }
                return '';
        }
    }

    /**
     * Adapt ACF field values to formats expected by Olobuilder elements.
     *
     * - Gallery (array of int IDs or array of image arrays) → [{url, alt}]
     * - OSM map (array with lat+lng keys) → "lat, lng" string
     */
    private function adapt_acf_value( $value ) {
        if ( ! is_array( $value ) || empty( $value ) ) {
            return $value;
        }

        // OSM map field: associative array with 'lat' and 'lng'
        if ( isset( $value['lat'] ) && isset( $value['lng'] ) ) {
            $lat = floatval( $value['lat'] );
            $lng = floatval( $value['lng'] );
            return "{$lat}, {$lng}";
        }

        // Gallery: array of attachment IDs (integers)
        if ( isset( $value[0] ) && is_int( $value[0] ) ) {
            return $this->attachment_ids_to_gallery( $value );
        }

        // Gallery: array of ACF image arrays (with 'ID' key)
        if ( isset( $value[0] ) && is_array( $value[0] ) && isset( $value[0]['ID'] ) ) {
            return $this->acf_image_arrays_to_gallery( $value );
        }

        return $value;
    }

    /**
     * Convert attachment IDs to gallery items [{url, alt}].
     */
    private function attachment_ids_to_gallery( $ids ) {
        $items = [];
        foreach ( $ids as $att_id ) {
            $url = wp_get_attachment_image_url( $att_id, 'large' );
            if ( ! $url ) continue;
            $alt = get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ?: '';
            $items[] = [ 'url' => $url, 'alt' => $alt ];
        }
        return $items;
    }

    /**
     * Convert ACF image arrays (with ID, url, alt keys) to gallery items [{url, alt}].
     */
    private function acf_image_arrays_to_gallery( $images ) {
        $items = [];
        foreach ( $images as $img ) {
            $url = $img['url'] ?? '';
            // Prefer 'large' size if available
            if ( ! empty( $img['sizes']['large'] ) ) {
                $url = $img['sizes']['large'];
            }
            if ( ! $url ) continue;
            $alt = $img['alt'] ?? '';
            $items[] = [ 'url' => $url, 'alt' => $alt ];
        }
        return $items;
    }

    /**
     * Resolve ACF repeater field.
     * Format: "repeater_name:sub_field_name" or just "repeater_name" (returns all rows).
     *
     * @param string $field   Repeater field key (optionally with :subfield).
     * @param int    $post_id Post ID.
     * @return mixed Array of rows or sub-field values.
     */
    private function resolve_acf_repeater( $field, $post_id ) {
        if ( ! function_exists( 'get_field' ) ) {
            return null;
        }

        $parts     = explode( ':', $field, 2 );
        $repeater  = $parts[0];
        $sub_field = $parts[1] ?? '';

        $rows = get_field( $repeater, $post_id );
        if ( ! is_array( $rows ) || empty( $rows ) ) {
            return [];
        }

        // If sub_field specified, extract just that column
        if ( $sub_field ) {
            return array_map( function( $row ) use ( $sub_field ) {
                return $row[ $sub_field ] ?? '';
            }, $rows );
        }

        return $rows;
    }

    /**
     * Resolve inline tokens like {post_title}, {current_year}, {custom_field:KEY} in a text string.
     * Tokens use the format {token_name} or {token_name:argument}.
     * Unrecognized tokens are left unchanged.
     *
     * @param string $text Text containing tokens.
     * @return string Text with tokens replaced.
     */
    public static function resolve_tokens( $text ) {
        if ( ! is_string( $text ) || ! str_contains( $text, '{' ) ) {
            return $text;
        }
        // Le tile portano quasi sempre graffe (CSS, JS): si lavora solo se c'è un segnaposto vero.
        if ( ! preg_match( '/\{(?:' . implode( '|', self::TOKENS ) . ')(?::[^}]+)?\}/', $text ) ) {
            return $text;
        }

        // Lo <span class="olo-dynamic-value"> va solo nel testo visibile. Dentro un tag (alt, title,
        // aria-label, data-fx-phrases…) spezzava l'attributo; dentro uno script rompeva il JS
        // (console.log("<span class="…">")). Lì il valore si scrive escapato per quel contesto;
        // gli <style> restano com'erano.
        $parti = preg_split( '/(<script\b.*?<\/script>|<style\b.*?<\/style>|<[^>]*>)/is', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( ! is_array( $parti ) ) {
            $parti = [ $text ];
        }
        $out = '';
        foreach ( $parti as $parte ) {
            if ( '' === $parte || ! str_contains( $parte, '{' ) ) {
                $out .= $parte;
                continue;
            }
            if ( '<' !== $parte[0] ) {
                $contesto = 'testo';
            } elseif ( preg_match( '/^<script\b/i', $parte ) ) {
                $contesto = 'script';
            } elseif ( preg_match( '/^<style\b/i', $parte ) ) {
                $out .= $parte;
                continue;
            } else {
                $contesto = 'attributo';
            }
            $out .= preg_replace_callback( '/\{([a-z_]+)(?::([^}]+))?\}/', function( $m ) use ( $contesto ) {
                $value = self::token_value( $m[1], isset( $m[2] ) ? $m[2] : '' );
                if ( null === $value ) {
                    return $m[0]; // Token non riconosciuto: lascia invariato
                }
                if ( self::campo_html( $m[1], isset( $m[2] ) ? $m[2] : '' ) ) {
                    // HTML intero già pronto (il testo lungo di uno spettacolo): nel testo così com'è, altrove il solo testo
                    if ( 'testo' === $contesto ) {
                        return $value;
                    }
                    $value = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( $value ) ) );
                }
                if ( 'attributo' === $contesto ) {
                    return esc_attr( $value );
                }
                if ( 'script' === $contesto ) {
                    // Sicuro dentro una stringa JS/JSON con apici singoli o doppi; il titolo di WP arriva
                    // con le entità (&#8220;), che in JS resterebbero lettere.
                    $value = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
                    return substr( (string) wp_json_encode( $value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ), 1, -1 );
                }
                // Wrappa il valore sostituito per styling opzionale
                return '<span class="olo-dynamic-value">' . esc_html( $value ) . '</span>';
            }, $parte );
        }
        return $out;
    }

    /** Segnaposto riconosciuti da resolve_tokens() (gli stessi casi di token_value()). */
    const TOKENS = [ 'post_title', 'post_date', 'post_excerpt', 'post_author', 'post_category', 'site_name', 'site_url',
        'current_year', 'current_date', 'featured_image', 'custom_field', 'user_name', 'post_id', 'post_url', 'post_count',
        'acf', 'acf_option', 'acf_image', 'acf_link', 'product_price', 'product_title', 'product_sku', 'product_stock',
        'product_rating', 'product_categories', 'product_short_desc', 'cart_total', 'cart_count', 'cart_subtotal', 'spettacolo' ];

    /**
     * Campi che portano HTML intero, già pronto: il testo lungo di uno spettacolo (foto, video incorporati, gallerie,
     * shortcode). Una tile legata a uno di questi ne riceve il segnaposto (segnaposto_html()) e il valore si scrive
     * dopo, a tile disegnata, come WordPress fa col contenuto degli articoli: il filtro dell'HTML delle tile
     * (wp_kses_post) toglieva iframe, <source> e lo stile delle gallerie.
     */
    const CAMPI_HTML = [ 'spettacolo' => [ 'contenuto' ] ];

    /** Il segnaposto di un campo con HTML intero ({spettacolo:contenuto}), '' per gli altri campi. */
    public static function segnaposto_html( $source, $field ) {
        return self::campo_html( $source, $field ) ? '{' . $source . ':' . $field . '}' : '';
    }

    private static function campo_html( $source, $field ) {
        return in_array( (string) $field, self::CAMPI_HTML[ (string) $source ] ?? [], true );
    }

    /**
     * Valore di un segnaposto, o null se il nome non è riconosciuto.
     *
     * @param string $token Nome del segnaposto.
     * @param string $arg   Argomento dopo i due punti ('' se assente).
     * @return string|null
     */
    private static function token_value( $token, $arg ) {
        $value = null;

        switch ( $token ) {
            case 'post_title':
                $value = get_the_title();
                break;

            case 'post_date':
                if ( $arg !== '' ) {
                    $value = get_the_date( $arg );
                } else {
                    $value = get_the_date();
                }
                break;

            case 'post_excerpt':
                $value = get_the_excerpt();
                break;

            case 'post_author':
                $value = get_the_author();
                break;

            case 'post_category':
                $cats = get_the_category();
                if ( ! empty( $cats ) ) {
                    $value = $cats[0]->name;
                } else {
                    $value = '';
                }
                break;

            case 'site_name':
                $value = get_bloginfo( 'name' );
                break;

            case 'site_url':
                $value = home_url();
                break;

            case 'current_year':
                $value = date_i18n( 'Y' );
                break;

            case 'current_date':
                $value = date_i18n( get_option( 'date_format' ) );
                break;

            case 'featured_image':
                $url = get_the_post_thumbnail_url( null, 'large' );
                $value = $url ? $url : '';
                break;

            case 'spettacolo':
                // {spettacolo:titolo}, {spettacolo:quando}…: i campi della pagina dello spettacolo
                $value = ( '' !== $arg && class_exists( 'Olobuild_Spettacoli' ) ) ? Olobuild_Spettacoli::campo( sanitize_key( $arg ) ) : '';
                break;

            case 'custom_field':
                if ( $arg !== '' ) {
                    $value = get_post_meta( get_the_ID(), sanitize_text_field( $arg ), true );
                } else {
                    $value = '';
                }
                break;

            case 'user_name':
                if ( is_user_logged_in() ) {
                    $user  = wp_get_current_user();
                    $value = $user->display_name;
                } else {
                    $value = '';
                }
                break;

            case 'post_id':
                $value = (string) get_the_ID();
                break;

            case 'post_url':
                $value = get_permalink();
                break;

            case 'post_count':
                $counts = wp_count_posts();
                $value  = (string) $counts->publish;
                break;

            case 'acf':
                // {acf:field_name} — ACF field from current post
                if ( $arg !== '' && function_exists( 'get_field' ) ) {
                    $acf_val = get_field( sanitize_text_field( $arg ) );
                    $value = is_array( $acf_val ) ? wp_json_encode( $acf_val ) : (string) $acf_val;
                } else {
                    $value = '';
                }
                break;

            case 'acf_option':
                // {acf_option:field_name} — ACF field from options page
                if ( $arg !== '' && function_exists( 'get_field' ) ) {
                    $acf_val = get_field( sanitize_text_field( $arg ), 'option' );
                    $value = is_array( $acf_val ) ? wp_json_encode( $acf_val ) : (string) $acf_val;
                } else {
                    $value = '';
                }
                break;

            case 'acf_image':
                // {acf_image:field_name} — ACF image field (returns URL)
                if ( $arg !== '' && function_exists( 'get_field' ) ) {
                    $img = get_field( sanitize_text_field( $arg ) );
                    if ( is_array( $img ) ) {
                        $value = $img['url'] ?? '';
                    } elseif ( is_numeric( $img ) ) {
                        $value = wp_get_attachment_url( $img );
                    } else {
                        $value = (string) $img;
                    }
                } else {
                    $value = '';
                }
                break;

            case 'product_price':
            case 'product_title':
            case 'product_sku':
            case 'product_stock':
            case 'product_rating':
            case 'product_categories':
            case 'product_short_desc':
                if ( class_exists( 'WooCommerce' ) ) {
                    $inst = new self();
                    $value = $inst->resolve_woocommerce( $token, get_the_ID() );
                } else {
                    $value = '';
                }
                break;

            case 'cart_total':
            case 'cart_count':
            case 'cart_subtotal':
                if ( class_exists( 'WooCommerce' ) ) {
                    $inst = new self();
                    $value = $inst->resolve_woocommerce( $token, 0 );
                } else {
                    $value = '';
                }
                break;

            case 'acf_link':
                // {acf_link:field_name} — ACF link field (returns URL)
                if ( $arg !== '' && function_exists( 'get_field' ) ) {
                    $link = get_field( sanitize_text_field( $arg ) );
                    if ( is_array( $link ) ) {
                        $value = $link['url'] ?? '';
                    } else {
                        $value = (string) $link;
                    }
                } else {
                    $value = '';
                }
                break;

            default:
                return null;
        }

        return (string) $value;
    }

    /**
     * Execute a WP_Query from a query config.
     *
     * @param array $query_config Query configuration.
     * @return WP_Post[] Array of posts.
     */
    public function resolve_query( $query_config ) {
        if ( empty( $query_config['enabled'] ) ) {
            return [];
        }

        $args = [
            'post_type'      => sanitize_text_field( $query_config['post_type'] ?? 'post' ),
            'posts_per_page' => absint( $query_config['posts_per_page'] ?? 6 ),
            'orderby'        => sanitize_text_field( $query_config['orderby'] ?? 'date' ),
            'order'          => sanitize_text_field( $query_config['order'] ?? 'DESC' ),
            'post_status'    => 'publish',
        ];

        // «Segui l'archivio aperto»: i contenuti della pagina d'archivio (Olobuild_Archive_Integration), col numero e
        // l'ordine scelti qui; tipo e tassonomia non contano. Fuori da un archivio (il builder) gli ultimi del tipo.
        if ( ! empty( $query_config['segui_archivio'] ) && class_exists( 'Olobuild_Archive_Integration' ) ) {
            $archivio = Olobuild_Archive_Integration::argomenti_dell_archivio();
            $ordine   = [ 'orderby' => $args['orderby'], 'order' => $args['order'] ];
            if ( is_array( $archivio ) && isset( $archivio['s'] ) && 'date' === $ordine['orderby'] ) {
                $ordine = []; // nella ricerca resta la pertinenza
            }
            $args = array_merge(
                is_array( $archivio ) ? $archivio : [ 'post_type' => $args['post_type'] ],
                [
                    'posts_per_page' => $args['posts_per_page'],
                    'paged'          => max( 1, absint( get_query_var( 'paged', 1 ) ) ),
                    'post_status'    => 'publish',
                ],
                $ordine
            );
            $query = new WP_Query( $args );
            update_post_thumbnail_cache( $query );
            return $query->posts;
        }

        // Taxonomy filter
        $taxonomy = $query_config['taxonomy'] ?? '';
        $terms    = $query_config['terms'] ?? [];
        if ( ! empty( $taxonomy ) && ! empty( $terms ) ) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- query dinamica per filtro contenuti per termine di tassonomia; tax_query necessaria alla funzione del modulo, volume limitato (posts_per_page).
            $args['tax_query'] = [
                [
                    'taxonomy' => sanitize_text_field( $taxonomy ),
                    'field'    => 'term_id',
                    'terms'    => array_map( 'absint', (array) $terms ),
                ],
            ];
        }

        $query = new WP_Query( $args );
        // Prime la cache delle featured image dell'intero set: evita l'N+1 (una query
        // attachment per post) quando build_items_from_query risolve 'featured_image'.
        // Stesso pattern di postgrid/relatedposts/rest-api.
        update_post_thumbnail_cache( $query );
        return $query->posts;
    }

    /**
     * Convert an array of WP_Post objects into item arrays using a field map.
     *
     * @param WP_Post[] $posts    Array of posts.
     * @param array     $item_map Map of itemField => WP field key.
     * @return array Array of items.
     */
    public function build_items_from_query( $posts, $item_map ) {
        $items = [];
        foreach ( array_values( $posts ) as $i => $post ) {
            $item = [
                'id' => 'dyn-' . $post->ID,
            ];
            foreach ( $item_map as $item_key => $wp_field ) {
                if ( '_index' === $wp_field ) {
                    // Numero progressivo della voce: 01, 02… (numeri delle liste e delle card)
                    $item[ $item_key ] = sprintf( '%02d', $i + 1 );
                } elseif ( 'post_year' === $wp_field ) {
                    $item[ $item_key ] = get_the_date( 'Y', $post );
                } else {
                    $item[ $item_key ] = $this->resolve_field( 'current_post', $wp_field, $post->ID );
                }
            }
            $items[] = $item;
        }
        return $items;
    }

    /* ── Elenco di foto da un campo («Sorgente dinamica» › Foto di un campo) ──────────────
     * Un ripetitore (scene del Tour 360, foto della Gallery Pro, slide di un carosello) si
     * riempie con le foto salvate in un campo del post: una voce per foto, coi campi della voce
     * mappati sulle parti della foto (_itemMap). Il campo può avere gli ID come array, come
     * testo separato da virgole (WooCommerce), in JSON o come galleria di ACF.
     */

    /**
     * Gli elenchi di foto proposti nel pannello: chiave del meta => etichetta e tipi di contenuto.
     * WooCommerce e i campi galleria di ACF ci sono da soli; i plugin aggiungono i loro col filtro
     * `olobuild_dynamic_image_lists` (OLObooking: la galleria e le foto 360° delle strutture).
     *
     * @return array<string,array{label:string,post_types:string[]}>
     */
    public static function image_lists() {
        $lists = [];
        if ( class_exists( 'WooCommerce' ) ) {
            // Etichette in italiano come quelle delle altre fonti: il builder le traduce con t().
            $lists['_product_image_gallery'] = [ 'label' => 'Galleria del prodotto (WooCommerce)', 'post_types' => [ 'product' ] ];
        }
        if ( function_exists( 'acf_get_field_groups' ) && function_exists( 'acf_get_fields' ) ) {
            foreach ( (array) acf_get_field_groups() as $group ) {
                foreach ( (array) acf_get_fields( $group['key'] ?? '' ) as $f ) {
                    if ( 'gallery' === ( $f['type'] ?? '' ) && ! empty( $f['name'] ) ) {
                        $lists[ $f['name'] ] = [ 'label' => ( $f['label'] ?? $f['name'] ) . ' (ACF)', 'post_types' => [] ];
                    }
                }
            }
        }
        /**
         * Elenchi di foto dei post per la «Sorgente dinamica» › Foto di un campo.
         *
         * @param array $lists chiave del meta => [ 'label' => etichetta, 'post_types' => [ tipi di contenuto ] ]
         */
        $lists = apply_filters( 'olobuild_dynamic_image_lists', $lists );
        $out   = [];
        foreach ( (array) $lists as $key => $def ) {
            $key = trim( (string) $key );
            if ( '' === $key ) {
                continue;
            }
            $out[ $key ] = [
                'label'      => (string) ( is_array( $def ) ? ( $def['label'] ?? $key ) : $def ),
                'post_types' => is_array( $def ) ? array_values( array_map( 'strval', (array) ( $def['post_types'] ?? [] ) ) ) : [],
            ];
        }
        return $out;
    }

    /**
     * Gli ID delle foto salvate in un campo del post, nell'ordine del campo, senza doppioni e senza
     * gli allegati che non esistono più o che non sono immagini.
     *
     * @param string $key     Chiave del meta.
     * @param int    $post_id Post.
     * @return int[]
     */
    public function image_list_ids( $key, $post_id ) {
        $key     = trim( (string) $key );
        $post_id = (int) $post_id;
        if ( '' === $key || $post_id <= 0 ) {
            return [];
        }
        // Un campo «Solo nelle categorie» fuori dalle sue categorie non si usa (Olobuild_Tipi_Contenuto::campo_vale()).
        if ( class_exists( 'Olobuild_Tipi_Contenuto' ) && ! Olobuild_Tipi_Contenuto::chiave_vale( $key, $post_id ) ) {
            return [];
        }
        $ids = self::ids_from_value( get_post_meta( $post_id, $key, true ) );
        /**
         * Gli ID delle foto di un elenco, per chi le salva in un altro modo.
         *
         * @param int[]  $ids
         * @param string $key
         * @param int    $post_id
         */
        $ids = (array) apply_filters( 'olobuild_dynamic_image_list_ids', $ids, $key, $post_id );
        $ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
        if ( $ids ) {
            _prime_post_caches( $ids, false, true );
        }
        return array_values( array_filter( $ids, 'wp_attachment_is_image' ) );
    }

    /** Gli ID da un valore salvato: array (anche di array o oggetti con ID), testo con virgole, JSON, numero. */
    private static function ids_from_value( $raw ) {
        if ( is_string( $raw ) ) {
            $t = trim( $raw );
            if ( '' === $t ) {
                return [];
            }
            if ( '[' === $t[0] || '{' === $t[0] ) {
                $json = json_decode( $t, true );
                if ( is_array( $json ) ) {
                    return self::ids_from_value( $json );
                }
            }
            $raw = preg_split( '/[\s,;|]+/', $t );
        }
        if ( is_numeric( $raw ) ) {
            return [ (int) $raw ];
        }
        if ( ! is_array( $raw ) ) {
            return [];
        }
        $ids = [];
        foreach ( $raw as $v ) {
            if ( is_array( $v ) ) {
                $v = $v['ID'] ?? ( $v['id'] ?? ( $v['attachment_id'] ?? 0 ) );
            } elseif ( is_object( $v ) ) {
                $v = $v->ID ?? 0;
            }
            if ( is_numeric( $v ) && (int) $v > 0 ) {
                $ids[] = (int) $v;
            }
        }
        return $ids;
    }

    /**
     * Le voci di un ripetitore dalle foto: `id` è l'ID della foto (la Gallery Pro ne ricava
     * srcset e ingrandimento), gli altri campi vengono dalla mappatura.
     *
     * @param int[] $ids      ID delle foto.
     * @param array $item_map campo della voce => parte della foto (url, url_original, title…).
     * @return array
     */
    public function build_items_from_images( $ids, $item_map ) {
        $items = [];
        foreach ( array_values( (array) $ids ) as $i => $aid ) {
            $item = [ 'id' => (int) $aid ];
            foreach ( (array) $item_map as $item_key => $part ) {
                $item[ $item_key ] = $this->image_part( (int) $aid, (string) $part, $i );
            }
            $items[] = $item;
        }
        return $items;
    }

    /** Quante parti al massimo si leggono da una riga (Parte 1…6 nella mappatura). */
    const PARTI_RIGA = 6;

    /**
     * «Righe di un campo»: le voci scritte in un campo del contenuto, una per riga, con le parti separate da «|»
     * («2019 | Pietrasanta | Bianco su bianco»). Le righe vuote e quelle che iniziano con # non contano. Vale anche
     * un campo salvato come elenco (array di testi o di array). Così una tile a voci (Timeline, Scheda tecnica,
     * Lista…) mostra i dati di ogni contenuto: le mostre di un'opera, le misure di un prodotto.
     *
     * @param string $key     Chiave del campo (meta).
     * @param int    $post_id Contenuto.
     * @return array[] Una voce per riga: le sue parti, già ripulite.
     */
    public function righe_di_un_campo( $key, $post_id ) {
        $key     = trim( (string) $key );
        $post_id = (int) $post_id;
        if ( '' === $key || $post_id <= 0 ) {
            return [];
        }
        if ( class_exists( 'Olobuild_Tipi_Contenuto' ) && ! Olobuild_Tipi_Contenuto::chiave_vale( $key, $post_id ) ) {
            return [];
        }
        $raw = get_post_meta( $post_id, $key, true );
        /**
         * Il valore del campo letto da «Righe di un campo», per chi lo salva in un altro modo.
         *
         * @param mixed  $raw
         * @param string $key
         * @param int    $post_id
         */
        $raw   = apply_filters( 'olobuild_dynamic_righe_raw', $raw, $key, $post_id );
        $righe = [];
        if ( is_array( $raw ) ) {
            foreach ( $raw as $voce ) {
                $righe[] = is_array( $voce ) ? implode( ' | ', array_map( 'strval', array_values( $voce ) ) ) : (string) $voce;
            }
        } else {
            $righe = preg_split( '/\r\n|\r|\n/', (string) $raw );
        }
        $esito = [];
        foreach ( $righe as $riga ) {
            $riga = trim( wp_strip_all_tags( (string) $riga ) );
            if ( '' === $riga || '#' === $riga[0] ) {
                continue;
            }
            $parti   = array_map( 'trim', explode( '|', $riga, self::PARTI_RIGA ) );
            $esito[] = [ 'riga' => preg_replace( '/\s*\|\s*/', ' · ', $riga ), 'parti' => $parti ];
        }
        return $esito;
    }

    /**
     * Le voci di un ripetitore dalle righe di un campo.
     *
     * @param array[] $righe    Da righe_di_un_campo().
     * @param array   $item_map campo della voce => parte_1…parte_6, riga, _index.
     * @return array
     */
    public function build_items_from_righe( $righe, $item_map ) {
        $items = [];
        foreach ( array_values( (array) $righe ) as $i => $r ) {
            $item = [ 'id' => 'riga-' . ( $i + 1 ) ];
            foreach ( (array) $item_map as $item_key => $part ) {
                $part = (string) $part;
                if ( '_index' === $part ) {
                    $item[ $item_key ] = sprintf( '%02d', $i + 1 );
                } elseif ( 'riga' === $part ) {
                    $item[ $item_key ] = (string) $r['riga'];
                } elseif ( preg_match( '/^parte_([1-6])$/', $part, $m ) ) {
                    $item[ $item_key ] = (string) ( $r['parti'][ (int) $m[1] - 1 ] ?? '' );
                }
            }
            $items[] = $item;
        }
        return $items;
    }

    /** Il lato più lungo che una foto 360° può avere come texture: oltre, molte schede grafiche non la caricano. */
    const MAX_ORIGINALE = 8192;

    /** Una parte di una foto per la mappatura della «Sorgente dinamica». */
    private function image_part( $aid, $part, $i ) {
        switch ( $part ) {
            case '_index':
                return sprintf( '%02d', $i + 1 );
            case 'url':
                return (string) wp_get_attachment_image_url( $aid, 'full' );
            case 'url_original':
                return self::original_url( $aid );
            case 'url_2048':
                return (string) ( wp_get_attachment_image_url( $aid, '2048x2048' ) ?: wp_get_attachment_image_url( $aid, 'full' ) );
            case 'url_large':
                return (string) wp_get_attachment_image_url( $aid, 'large' );
            case 'url_medium':
                return (string) wp_get_attachment_image_url( $aid, 'medium' );
            case 'url_thumb':
                return (string) wp_get_attachment_image_url( $aid, 'thumbnail' );
            case 'title':
                return wp_strip_all_tags( (string) get_the_title( $aid ) );
            case 'caption':
                return wp_strip_all_tags( (string) wp_get_attachment_caption( $aid ) );
            case 'alt':
                return wp_strip_all_tags( (string) get_post_meta( $aid, '_wp_attachment_image_alt', true ) );
            case 'description':
                $att = get_post( $aid );
                return $att ? trim( wp_strip_all_tags( (string) $att->post_content ) ) : '';
            default:
                return '';
        }
    }

    /**
     * Il file caricato, prima che WordPress lo rimpicciolisse a 2560 px: serve alle foto 360°
     * (8192 × 4096). Se è più largo di MAX_ORIGINALE, la versione rimpicciolita.
     */
    private static function original_url( $aid ) {
        $full = (string) wp_get_attachment_image_url( $aid, 'full' );
        $meta = wp_get_attachment_metadata( $aid );
        if ( is_array( $meta ) && ! empty( $meta['original_image'] ) && function_exists( 'wp_get_original_image_path' ) ) {
            $path = wp_get_original_image_path( $aid );
            $dim  = ( $path && is_readable( $path ) ) ? wp_getimagesize( $path ) : false;
            if ( is_array( $dim ) && (int) $dim[0] > 0 && (int) $dim[0] <= self::MAX_ORIGINALE ) {
                return (string) ( wp_get_original_image_url( $aid ) ?: $full );
            }
        }
        return $full;
    }

    /**
     * Resolve a comments field.
     */
    private function resolve_comments( $field, $post_id ) {
        switch ( $field ) {
            case 'comment_count':
                return (string) get_comments_number( $post_id );
            case 'latest_comment':
                $comments = get_comments( [ 'post_id' => $post_id, 'number' => 1, 'status' => 'approve', 'orderby' => 'comment_date', 'order' => 'DESC' ] );
                return ! empty( $comments ) ? wp_strip_all_tags( $comments[0]->comment_content ) : '';
            case 'latest_comment_author':
                $comments = get_comments( [ 'post_id' => $post_id, 'number' => 1, 'status' => 'approve', 'orderby' => 'comment_date', 'order' => 'DESC' ] );
                return ! empty( $comments ) ? $comments[0]->comment_author : '';
            default:
                return '';
        }
    }

    /**
     * Resolve a menu field.
     */
    private function resolve_menu( $field ) {
        // field format: "menu_name:Primary" or "menu_item_count:Primary"
        if ( ! str_contains( $field, ':' ) ) return '';
        list( $prop, $menu_slug ) = explode( ':', $field, 2 );
        $menu_slug = trim( $menu_slug );

        $menu = wp_get_nav_menu_object( $menu_slug );
        if ( ! $menu ) {
            $locations = get_nav_menu_locations();
            $menu_id   = $locations[ $menu_slug ] ?? 0;
            if ( $menu_id ) $menu = wp_get_nav_menu_object( $menu_id );
        }
        if ( ! $menu ) return '';

        switch ( $prop ) {
            case 'menu_name':
                return $menu->name;
            case 'menu_item_count':
                return (string) $menu->count;
            default:
                return '';
        }
    }

    /**
     * Resolve a media/attachment field.
     */
    private function resolve_media( $field, $post_id ) {
        // Use featured image attachment as default media
        $attachment_id = get_post_thumbnail_id( $post_id );
        if ( ! $attachment_id ) return '';

        switch ( $field ) {
            case 'media_caption':
                return wp_get_attachment_caption( $attachment_id ) ?: '';
            case 'media_alt':
                return get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ?: '';
            case 'media_description':
                $att = get_post( $attachment_id );
                return $att ? $att->post_content : '';
            case 'media_title':
                return get_the_title( $attachment_id );
            case 'media_url':
                return wp_get_attachment_url( $attachment_id ) ?: '';
            case 'media_file_size':
                $file = get_attached_file( $attachment_id );
                if ( $file && file_exists( $file ) ) {
                    return size_format( filesize( $file ) );
                }
                return '';
            default:
                return '';
        }
    }

    /**
     * Resolve post navigation fields.
     */
    private function resolve_post_navigation( $field, $post_id ) {
        switch ( $field ) {
            case 'previous_post_title':
                $prev = get_previous_post();
                return $prev ? get_the_title( $prev ) : '';
            case 'previous_post_url':
                $prev = get_previous_post();
                return $prev ? get_permalink( $prev ) : '';
            case 'next_post_title':
                $next = get_next_post();
                return $next ? get_the_title( $next ) : '';
            case 'next_post_url':
                $next = get_next_post();
                return $next ? get_permalink( $next ) : '';
            default:
                return '';
        }
    }

    /**
     * Get all available dynamic sources and their fields.
     *
     * @return array Catalog of sources.
     */
    public function get_available_sources() {
        $sources = [
            'current_post' => [
                'label'  => 'Articolo corrente',
                'fields' => [
                    [ 'key' => 'post_title',      'label' => 'Titolo',          'type' => 'text' ],
                    [ 'key' => 'post_excerpt',     'label' => 'Estratto',        'type' => 'text' ],
                    [ 'key' => 'post_content',     'label' => 'Contenuto',        'type' => 'html' ],
                    [ 'key' => 'featured_image',   'label' => 'Immagine in evidenza', 'type' => 'image' ],
                    [ 'key' => 'post_date',        'label' => 'Data',           'type' => 'text' ],
                    [ 'key' => 'author_name',      'label' => 'Autore',         'type' => 'text' ],
                    [ 'key' => 'permalink',        'label' => 'Permalink',      'type' => 'url' ],
                ],
            ],
            'site' => [
                'label'  => 'Sito',
                'fields' => [
                    [ 'key' => 'name',        'label' => 'Titolo del sito', 'type' => 'text' ],
                    [ 'key' => 'description', 'label' => 'Motto',   'type' => 'text' ],
                    [ 'key' => 'url',         'label' => 'URL',        'type' => 'url' ],
                ],
            ],
            'custom_field' => [
                'label'  => 'Campo personalizzato',
                'fields' => 'manual',
            ],
            'author' => [
                'label'  => 'Autore',
                'fields' => [
                    [ 'key' => 'author_display_name', 'label' => 'Nome visualizzato',     'type' => 'text' ],
                    [ 'key' => 'author_bio',          'label' => 'Biografia',              'type' => 'text' ],
                    [ 'key' => 'author_email',        'label' => 'Email',            'type' => 'text' ],
                    [ 'key' => 'author_avatar',       'label' => 'URL avatar',       'type' => 'image' ],
                    [ 'key' => 'author_url',          'label' => 'Sito web',          'type' => 'url' ],
                    [ 'key' => 'author_posts_url',    'label' => 'Archivio autore',   'type' => 'url' ],
                ],
            ],
            'user' => [
                'label'  => 'Utente connesso',
                'fields' => [
                    [ 'key' => 'user_display_name', 'label' => 'Nome visualizzato', 'type' => 'text' ],
                    [ 'key' => 'user_email',        'label' => 'Email',        'type' => 'text' ],
                    [ 'key' => 'user_role',         'label' => 'Ruolo',         'type' => 'text' ],
                    [ 'key' => 'user_avatar',       'label' => 'URL avatar',   'type' => 'image' ],
                ],
            ],
            'datetime' => [
                'label'  => 'Data e ora',
                'fields' => [
                    [ 'key' => 'current_date',     'label' => 'Data di oggi',     'type' => 'text' ],
                    [ 'key' => 'current_time',     'label' => 'Ora attuale',     'type' => 'text' ],
                    [ 'key' => 'current_year',     'label' => 'Anno in corso',     'type' => 'text' ],
                    [ 'key' => 'current_day_name', 'label' => 'Giorno della settimana',         'type' => 'text' ],
                ],
            ],
            'request' => [
                'label'  => 'Richiesta',
                'fields' => [
                    [ 'key' => 'request_url', 'label' => 'URL corrente',    'type' => 'url' ],
                    [ 'key' => 'referrer',    'label' => 'Pagina di provenienza',  'type' => 'url' ],
                ],
            ],
            'archive' => [
                'label'  => 'Archivio',
                'fields' => [
                    [ 'key' => 'archive_title',       'label' => 'Titolo archivio',       'type' => 'text' ],
                    [ 'key' => 'archive_description', 'label' => 'Descrizione archivio', 'type' => 'text' ],
                ],
            ],
        ];

        // I campi dei tipi di contenuto delle Impostazioni (Notizie › Sottotitolo, Articoli › Allegati…)
        if ( class_exists( 'Olobuild_Tipi_Contenuto' ) ) {
            $campi = Olobuild_Tipi_Contenuto::campi_dinamici();
            if ( $campi ) {
                $sources['campi'] = [
                    'label'  => 'Campi dei contenuti',
                    'fields' => $campi,
                ];
            }
        }

        // Spettacoli del sito: i campi della pagina di uno spettacolo (template singolo di olo_spettacolo)
        if ( class_exists( 'Olobuild_Spettacoli' ) ) {
            $sources['spettacolo'] = [
                'label'  => 'Spettacolo',
                'fields' => Olobuild_Spettacoli::campi_dinamici(),
            ];
        }

        // WooCommerce support
        $woo_available = class_exists( 'WooCommerce' );
        if ( $woo_available ) {
            $sources['woocommerce'] = [
                'label'     => 'WooCommerce',
                'available' => true,
                'fields'    => [
                    [ 'key' => 'product_title',       'label' => 'Titolo prodotto',    'type' => 'text' ],
                    [ 'key' => 'product_price',       'label' => 'Prezzo (HTML)',       'type' => 'html' ],
                    [ 'key' => 'product_regular_price','label' => 'Prezzo regolare',    'type' => 'text' ],
                    [ 'key' => 'product_sale_price',  'label' => 'Prezzo scontato',    'type' => 'text' ],
                    [ 'key' => 'product_sku',         'label' => 'SKU',                'type' => 'text' ],
                    [ 'key' => 'product_stock',       'label' => 'Disponibilità',      'type' => 'text' ],
                    [ 'key' => 'product_stock_qty',   'label' => 'Quantità stock',     'type' => 'text' ],
                    [ 'key' => 'product_short_desc',  'label' => 'Descrizione breve',  'type' => 'text' ],
                    [ 'key' => 'product_description', 'label' => 'Descrizione',        'type' => 'html' ],
                    [ 'key' => 'product_weight',      'label' => 'Peso',               'type' => 'text' ],
                    [ 'key' => 'product_dimensions',  'label' => 'Dimensioni',         'type' => 'text' ],
                    [ 'key' => 'product_categories',  'label' => 'Categorie',          'type' => 'text' ],
                    [ 'key' => 'product_tags',        'label' => 'Tag',                'type' => 'text' ],
                    [ 'key' => 'product_image',       'label' => 'Immagine prodotto',  'type' => 'image' ],
                    [ 'key' => 'product_gallery',     'label' => 'Galleria prodotto',  'type' => 'image' ],
                    [ 'key' => 'product_rating',      'label' => 'Voto medio',         'type' => 'text' ],
                    [ 'key' => 'product_review_count','label' => 'N. recensioni',      'type' => 'text' ],
                    [ 'key' => 'product_url',         'label' => 'URL prodotto',       'type' => 'url' ],
                    [ 'key' => 'cart_total',          'label' => 'Totale carrello',    'type' => 'text' ],
                    [ 'key' => 'cart_subtotal',       'label' => 'Subtotale carrello', 'type' => 'text' ],
                    [ 'key' => 'cart_count',          'label' => 'Articoli nel carrello', 'type' => 'text' ],
                ],
            ];
            // Gli attributi del negozio (Artista, Marca, Materiale…): uno per voce, letti dal prodotto della pagina.
            if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
                foreach ( (array) wc_get_attribute_taxonomies() as $att ) {
                    $sources['woocommerce']['fields'][] = [
                        'key'   => 'attributo:pa_' . $att->attribute_name,
                        'label' => 'Attributo: ' . $att->attribute_label,
                        'type'  => 'text',
                    ];
                }
            }
        }

        // ACF support
        $acf_available = function_exists( 'get_field' ) && function_exists( 'acf_get_field_groups' );
        $sources['acf'] = [
            'label'     => 'ACF',
            'available' => $acf_available,
            'fields'    => $acf_available ? $this->get_acf_fields() : [],
        ];

        // Post types
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $pt_list = [];
        foreach ( $post_types as $pt ) {
            if ( $pt->name === 'attachment' ) continue;
            $pt_list[] = [
                'value' => $pt->name,
                'label' => $pt->label,
            ];
        }
        $sources['post_types'] = $pt_list;

        // Taxonomies with terms
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $tax_list = [];
        foreach ( $taxonomies as $tax ) {
            $terms = get_terms( [
                'taxonomy'   => $tax->name,
                'hide_empty' => false,
                'number'     => 100,
            ] );
            $term_items = [];
            if ( ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    $term_items[] = [
                        'value' => $term->term_id,
                        'label' => $term->name,
                    ];
                }
            }
            $tax_list[] = [
                'value' => $tax->name,
                'label' => $tax->label,
                'terms' => $term_items,
            ];
        }
        $sources['taxonomies'] = $tax_list;

        // New sources
        $sources['comments'] = [
            'label'  => 'Commenti',
            'fields' => [
                [ 'key' => 'comment_count',         'label' => 'Numero commenti',       'type' => 'text' ],
                [ 'key' => 'latest_comment',        'label' => 'Ultimo commento',       'type' => 'text' ],
                [ 'key' => 'latest_comment_author',  'label' => 'Autore ultimo commento','type' => 'text' ],
            ],
        ];
        $sources['media'] = [
            'label'  => 'Media (immagine in evidenza)',
            'fields' => [
                [ 'key' => 'media_caption',     'label' => 'Didascalia',    'type' => 'text' ],
                [ 'key' => 'media_alt',         'label' => 'Testo alternativo', 'type' => 'text' ],
                [ 'key' => 'media_description', 'label' => 'Descrizione',   'type' => 'text' ],
                [ 'key' => 'media_title',       'label' => 'Titolo media',  'type' => 'text' ],
                [ 'key' => 'media_url',         'label' => 'URL file',      'type' => 'text' ],
                [ 'key' => 'media_file_size',   'label' => 'Dimensione file','type' => 'text' ],
            ],
        ];
        $sources['post_navigation'] = [
            'label'  => 'Navigazione post',
            'fields' => [
                [ 'key' => 'previous_post_title', 'label' => 'Titolo post precedente', 'type' => 'text' ],
                [ 'key' => 'previous_post_url',   'label' => 'URL post precedente',    'type' => 'url' ],
                [ 'key' => 'next_post_title',     'label' => 'Titolo post successivo',  'type' => 'text' ],
                [ 'key' => 'next_post_url',       'label' => 'URL post successivo',     'type' => 'url' ],
            ],
        ];
        $sources['shortcode'] = [
            'label'  => 'Shortcode',
            'fields' => [
                [ 'key' => 'shortcode_output', 'label' => 'Shortcode (es: my_shortcode param="val")', 'type' => 'text' ],
            ],
        ];
        $sources['cookie'] = [
            'label'  => 'Cookie',
            'fields' => [
                [ 'key' => 'cookie_value', 'label' => 'Nome cookie', 'type' => 'text' ],
            ],
        ];
        $sources['global_widget'] = [
            'label'  => 'Widget globale (template incorporato)',
            'fields' => [
                [ 'key' => 'template_id', 'label' => 'ID template Olobuild', 'type' => 'text' ],
            ],
        ];

        // Elenchi di foto per i ripetitori («Sorgente dinamica» › Foto di un campo).
        $sources['image_lists'] = [];
        foreach ( self::image_lists() as $key => $def ) {
            $sources['image_lists'][] = [ 'value' => $key, 'label' => $def['label'], 'post_types' => $def['post_types'] ];
        }

        return $sources;
    }

    /**
     * Discover ACF field groups and fields.
     *
     * @return array ACF fields.
     */
    public function get_acf_fields() {
        if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
            return [];
        }

        $groups = acf_get_field_groups();
        $result = [];
        foreach ( $groups as $group ) {
            $fields = acf_get_fields( $group['key'] );
            if ( empty( $fields ) ) continue;

            $group_fields = [];
            foreach ( $fields as $f ) {
                $type_map = [
                    'text'     => 'text',
                    'textarea' => 'text',
                    'number'   => 'text',
                    'email'    => 'text',
                    'url'      => 'url',
                    'image'    => 'image',
                    'wysiwyg'  => 'html',
                ];
                $group_fields[] = [
                    'key'   => $f['name'],
                    'label' => $f['label'],
                    'type'  => $type_map[ $f['type'] ] ?? 'text',
                ];
            }

            $result[] = [
                'group_label' => $group['title'],
                'fields'      => $group_fields,
            ];
        }

        return $result;
    }
}
