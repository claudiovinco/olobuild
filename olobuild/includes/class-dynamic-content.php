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

        switch ( $source ) {
            case 'current_post':
                return $this->resolve_current_post( $field, $post_id );

            case 'site':
                return $this->resolve_site( $field );

            case 'custom_field':
                $value = get_post_meta( $post_id, $field, true );
                return $this->adapt_acf_value( $value );

            case 'acf':
                if ( function_exists( 'get_field' ) ) {
                    $value = get_field( $field, $post_id );
                    return $this->adapt_acf_value( $value );
                }
                return null;

            case 'taxonomy_field':
                return $this->resolve_taxonomy_field( $field, $post_id );

            case 'author':
                $post = get_post( $post_id );
                if ( $post ) {
                    return get_the_author_meta( $field, $post->post_author );
                }
                return null;

            default:
                return null;
        }
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
                // Returns the name of the first term from any taxonomy attached to the post
                $post_obj = get_post( $post_id );
                if ( ! $post_obj ) return '';
                $taxonomies = get_object_taxonomies( $post_obj->post_type, 'names' );
                foreach ( $taxonomies as $tax ) {
                    $terms = get_the_terms( $post_id, $tax );
                    if ( $terms && ! is_wp_error( $terms ) ) {
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
                    [ 'key' => 'display_name',    'label' => 'Display Name', 'type' => 'text' ],
                    [ 'key' => 'user_email',      'label' => 'Email',        'type' => 'text' ],
                    [ 'key' => 'user_url',         'label' => 'Website',      'type' => 'url' ],
                    [ 'key' => 'description',      'label' => 'Bio',          'type' => 'text' ],
                ],
            ],
        ];

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
