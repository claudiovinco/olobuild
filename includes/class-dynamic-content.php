<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Dynamic_Content {

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
                $allowed_cookies = apply_filters( 'olo_allowed_cookies', [ 'language', 'theme_mode', 'currency', 'olo_consent' ] );
                if ( ! in_array( $key, $allowed_cookies, true ) ) {
                    $value = '';
                } else {
                    $value = isset( $_COOKIE[ $key ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $key ] ) ) : '';
                }
                break;

            case 'post_navigation':
                $value = $this->resolve_post_navigation( $field, $post_id );
                break;

            case 'global_widget':
                $tpl_id = intval( $field );
                if ( $tpl_id > 0 ) {
                    $renderer = new Olo_Frontend_Renderer();
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
                // Returns the name of the first term from any taxonomy attached to the post.
                // Single query instead of N+1 (one per taxonomy).
                $post_obj = get_post( $post_id );
                if ( ! $post_obj ) return '';
                $taxonomies = get_object_taxonomies( $post_obj->post_type, 'names' );
                if ( empty( $taxonomies ) ) return '';
                $terms = wp_get_object_terms( $post_id, $taxonomies, [
                    'number'  => 1,
                    'orderby' => 'term_id',
                    'order'   => 'ASC',
                    'fields'  => 'names',
                ] );
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    return $terms[0];
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
                return $protocol . ( $_SERVER['HTTP_HOST'] ?? '' ) . ( $_SERVER['REQUEST_URI'] ?? '' );

            case 'request_param':
                // Field config should pass the param name as the field key in format "request_param:name"
                return '';

            case 'referrer':
                return wp_get_referer() ?: '';

            default:
                // Support request_param:param_name format
                if ( strpos( $field, 'request_param:' ) === 0 ) {
                    $param_name = substr( $field, 14 );
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
        if ( strpos( $field, 'cart_' ) === 0 ) {
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
            default:                    return '';
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
        if ( ! is_string( $text ) || strpos( $text, '{' ) === false ) {
            return $text;
        }

        return preg_replace_callback( '/\{([a-z_]+)(?::([^}]+))?\}/', function( $m ) {
            $token = $m[1];
            $arg   = isset( $m[2] ) ? $m[2] : '';
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
                    $value = date( 'Y' );
                    break;

                case 'current_date':
                    $value = date_i18n( get_option( 'date_format' ) );
                    break;

                case 'featured_image':
                    $url = get_the_post_thumbnail_url( null, 'large' );
                    $value = $url ? $url : '';
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
                    // Token non riconosciuto: lascia invariato
                    return $m[0];
            }

            // Wrappa il valore sostituito per styling opzionale
            return '<span class="olo-dynamic-value">' . esc_html( (string) $value ) . '</span>';
        }, $text );
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

        // Taxonomy filter
        $taxonomy = $query_config['taxonomy'] ?? '';
        $terms    = $query_config['terms'] ?? [];
        if ( ! empty( $taxonomy ) && ! empty( $terms ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => sanitize_text_field( $taxonomy ),
                    'field'    => 'term_id',
                    'terms'    => array_map( 'absint', (array) $terms ),
                ],
            ];
        }

        $query = new WP_Query( $args );
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
        foreach ( $posts as $post ) {
            $item = [
                'id' => 'dyn-' . $post->ID,
            ];
            foreach ( $item_map as $item_key => $wp_field ) {
                $item[ $item_key ] = $this->resolve_field( 'current_post', $wp_field, $post->ID );
            }
            $items[] = $item;
        }
        return $items;
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
        if ( strpos( $field, ':' ) === false ) return '';
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
                'label'  => 'Current Post',
                'fields' => [
                    [ 'key' => 'post_title',      'label' => 'Title',          'type' => 'text' ],
                    [ 'key' => 'post_excerpt',     'label' => 'Excerpt',        'type' => 'text' ],
                    [ 'key' => 'post_content',     'label' => 'Content',        'type' => 'html' ],
                    [ 'key' => 'featured_image',   'label' => 'Featured Image', 'type' => 'image' ],
                    [ 'key' => 'post_date',        'label' => 'Date',           'type' => 'text' ],
                    [ 'key' => 'author_name',      'label' => 'Author',         'type' => 'text' ],
                    [ 'key' => 'permalink',        'label' => 'Permalink',      'type' => 'url' ],
                ],
            ],
            'site' => [
                'label'  => 'Site',
                'fields' => [
                    [ 'key' => 'name',        'label' => 'Site Title', 'type' => 'text' ],
                    [ 'key' => 'description', 'label' => 'Tagline',   'type' => 'text' ],
                    [ 'key' => 'url',         'label' => 'URL',        'type' => 'url' ],
                ],
            ],
            'custom_field' => [
                'label'  => 'Custom Field',
                'fields' => 'manual',
            ],
            'author' => [
                'label'  => 'Author',
                'fields' => [
                    [ 'key' => 'author_display_name', 'label' => 'Display Name',     'type' => 'text' ],
                    [ 'key' => 'author_bio',          'label' => 'Bio',              'type' => 'text' ],
                    [ 'key' => 'author_email',        'label' => 'Email',            'type' => 'text' ],
                    [ 'key' => 'author_avatar',       'label' => 'Avatar URL',       'type' => 'image' ],
                    [ 'key' => 'author_url',          'label' => 'Website',          'type' => 'url' ],
                    [ 'key' => 'author_posts_url',    'label' => 'Author Archive',   'type' => 'url' ],
                ],
            ],
            'user' => [
                'label'  => 'Current User',
                'fields' => [
                    [ 'key' => 'user_display_name', 'label' => 'Display Name', 'type' => 'text' ],
                    [ 'key' => 'user_email',        'label' => 'Email',        'type' => 'text' ],
                    [ 'key' => 'user_role',         'label' => 'Role',         'type' => 'text' ],
                    [ 'key' => 'user_avatar',       'label' => 'Avatar URL',   'type' => 'image' ],
                ],
            ],
            'datetime' => [
                'label'  => 'Date/Time',
                'fields' => [
                    [ 'key' => 'current_date',     'label' => 'Current Date',     'type' => 'text' ],
                    [ 'key' => 'current_time',     'label' => 'Current Time',     'type' => 'text' ],
                    [ 'key' => 'current_year',     'label' => 'Current Year',     'type' => 'text' ],
                    [ 'key' => 'current_day_name', 'label' => 'Day Name',         'type' => 'text' ],
                ],
            ],
            'request' => [
                'label'  => 'Request',
                'fields' => [
                    [ 'key' => 'request_url', 'label' => 'Current URL',    'type' => 'url' ],
                    [ 'key' => 'referrer',    'label' => 'HTTP Referrer',  'type' => 'url' ],
                ],
            ],
            'archive' => [
                'label'  => 'Archive',
                'fields' => [
                    [ 'key' => 'archive_title',       'label' => 'Archive Title',       'type' => 'text' ],
                    [ 'key' => 'archive_description', 'label' => 'Archive Description', 'type' => 'text' ],
                ],
            ],
        ];

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
            'label'  => 'Global Widget (embed template)',
            'fields' => [
                [ 'key' => 'template_id', 'label' => 'ID template Olobuild', 'type' => 'text' ],
            ],
        ];

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
