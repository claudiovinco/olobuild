<?php
/**
 * Olo Lang — Traduzione post/CPT tramite copie per lingua.
 *
 * Approccio "stile WPML": per ogni post (baita, articolo, pagina) si crea
 * una copia per ogni lingua target. Il traduttore traduce direttamente
 * la copia nell'editor WordPress.
 *
 * Meta utilizzati:
 *   - _olo_lang_translations  (sull'originale)  { "en": 42, "de": 43 }
 *   - _olo_lang_source        (sulla copia)      ID del post originale
 *   - _olo_lang_lang          (sulla copia)      codice lingua ("en", "de")
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Lang_Post_Translation {

    /** CPT per i quali abilitare il sistema di copie */
    private $enabled_post_types = [ 'olo_service', 'post', 'page' ];

    public function init() {
        // Meta box nell'editor WP
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );

        // AJAX: duplica post
        add_action( 'wp_ajax_olo_lang_duplicate_post', [ $this, 'ajax_duplicate_post' ] );

        // Colonna nella lista admin per olo_service
        add_filter( 'manage_olo_service_posts_columns', [ $this, 'add_columns' ] );
        add_action( 'manage_olo_service_posts_custom_column', [ $this, 'render_column' ], 10, 2 );

        // Frontend: nascondi copie tradotte dalle query (default lang) / prepara swap (target lang)
        add_action( 'pre_get_posts', [ $this, 'filter_query_by_lang' ] );

        // Frontend: su singolo post, swap alla copia tradotta se disponibile
        add_filter( 'the_posts', [ $this, 'swap_single_post' ], 10, 2 );

        // Frontend: su archivi/liste/secondary queries, swap alla copia tradotta
        add_filter( 'the_posts', [ $this, 'swap_archive_posts' ], 11, 2 );
    }

    // =========================================================================
    // Meta Box — bottoni "Crea traduzione" / "Modifica traduzione"
    // =========================================================================

    public function add_meta_box() {
        $config    = Olo_Lang_Language::get_config();
        $languages = Olo_Lang_Language::get_active_languages();

        if ( count( $languages ) < 2 ) {
            return;
        }

        foreach ( $this->enabled_post_types as $pt ) {
            add_meta_box(
                'olo-lang-translations',
                'Traduzioni',
                [ $this, 'render_meta_box' ],
                $pt,
                'side',
                'high'
            );
        }
    }

    public function render_meta_box( $post ) {
        $config    = Olo_Lang_Language::get_config();
        $default   = $config['default_lang'];
        $languages = Olo_Lang_Language::get_active_languages();

        // Determina se questo post e' un originale o una copia
        $source_id = (int) get_post_meta( $post->ID, '_olo_lang_source', true );
        $post_lang = get_post_meta( $post->ID, '_olo_lang_lang', true );

        if ( $source_id ) {
            // Questa e' una COPIA tradotta
            $source = get_post( $source_id );
            echo '<p style="margin:0 0 8px">';
            echo '<span class="dashicons dashicons-translation" style="color:#6366F1"></span> ';
            echo 'Traduzione <strong>' . esc_html( strtoupper( $post_lang ) ) . '</strong> di: ';
            echo '<a href="' . esc_url( get_edit_post_link( $source_id ) ) . '">';
            echo esc_html( $source ? $source->post_title : '#' . $source_id );
            echo '</a></p>';

            wp_nonce_field( 'olo_lang_translation_meta', '_olo_lang_nonce' );
            return;
        }

        // Questo e' un ORIGINALE — mostra stato traduzioni per ogni lingua target
        $translations = get_post_meta( $post->ID, '_olo_lang_translations', true );
        $translations = is_array( $translations ) ? $translations : [];

        $target_langs = array_filter( $languages, function ( $l ) use ( $default ) {
            return $l['code'] !== $default;
        } );

        if ( empty( $target_langs ) ) {
            echo '<p>Nessuna lingua target configurata.</p>';
            return;
        }

        echo '<div class="olo-lang-pt-meta">';

        foreach ( $target_langs as $lang ) {
            $code    = $lang['code'];
            $copy_id = $translations[ $code ] ?? 0;
            $copy    = $copy_id ? get_post( $copy_id ) : null;

            echo '<div class="olo-lang-pt-row" style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #eee">';
            echo '<strong style="min-width:28px">' . esc_html( strtoupper( $code ) ) . '</strong>';

            if ( $copy && $copy->post_status !== 'trash' ) {
                $status_label = $copy->post_status === 'publish' ? 'Pubblicata' : 'Bozza';
                $status_color = $copy->post_status === 'publish' ? '#16a34a' : '#f59e0b';
                echo '<span style="flex:1;font-size:12px;color:' . $status_color . '">' . esc_html( $status_label ) . '</span>';
                echo '<a href="' . esc_url( get_edit_post_link( $copy_id ) ) . '" class="button button-small">Modifica</a>';
            } else {
                echo '<span style="flex:1;font-size:12px;color:#9ca3af">Non tradotto</span>';
                echo '<button type="button" class="button button-small button-primary olo-lang-pt-create" '
                    . 'data-post-id="' . esc_attr( $post->ID ) . '" '
                    . 'data-lang="' . esc_attr( $code ) . '">'
                    . 'Crea</button>';
            }

            echo '</div>';
        }

        echo '</div>';

        // JS inline per il bottone Crea
        echo '<script>
        jQuery(function($){
            $(".olo-lang-pt-create").on("click", function(){
                var $btn = $(this).prop("disabled",true).text("Creazione...");
                $.post(ajaxurl, {
                    action: "olo_lang_duplicate_post",
                    post_id: $btn.data("post-id"),
                    lang: $btn.data("lang"),
                    _wpnonce: "' . wp_create_nonce( 'olo_lang_duplicate' ) . '"
                }, function(res){
                    if(res.success && res.data.edit_url){
                        window.location.href = res.data.edit_url;
                    } else {
                        alert(res.data || "Errore");
                        $btn.prop("disabled",false).text("Crea");
                    }
                });
            });
        });
        </script>';

        wp_nonce_field( 'olo_lang_translation_meta', '_olo_lang_nonce' );
    }

    // =========================================================================
    // AJAX — Duplica post per lingua
    // =========================================================================

    public function ajax_duplicate_post() {
        check_ajax_referer( 'olo_lang_duplicate', '_wpnonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( 'Permesso negato.' );
        }

        $post_id = absint( $_POST['post_id'] ?? 0 );
        $lang    = sanitize_text_field( $_POST['lang'] ?? '' );

        if ( ! $post_id || ! $lang ) {
            wp_send_json_error( 'Dati mancanti.' );
        }

        $copy_id = $this->duplicate_post( $post_id, $lang );

        if ( is_wp_error( $copy_id ) ) {
            wp_send_json_error( $copy_id->get_error_message() );
        }

        wp_send_json_success( [
            'copy_id'  => $copy_id,
            'edit_url' => get_edit_post_link( $copy_id, 'raw' ),
        ] );
    }

    /**
     * Duplica un post e lo collega come traduzione.
     */
    public function duplicate_post( $post_id, $lang ) {
        $post = get_post( $post_id );
        if ( ! $post ) {
            return new WP_Error( 'not_found', 'Post non trovato.' );
        }

        // Controlla se esiste gia' una copia per questa lingua
        $existing = get_post_meta( $post_id, '_olo_lang_translations', true );
        $existing = is_array( $existing ) ? $existing : [];

        if ( ! empty( $existing[ $lang ] ) ) {
            $existing_post = get_post( $existing[ $lang ] );
            if ( $existing_post && $existing_post->post_status !== 'trash' ) {
                return new WP_Error( 'exists', 'Traduzione gia\' esistente.' );
            }
        }

        // Crea la copia come bozza
        $copy_data = [
            'post_type'    => $post->post_type,
            'post_title'   => $post->post_title . ' [' . strtoupper( $lang ) . ']',
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_status'  => 'draft',
            'post_author'  => get_current_user_id(),
        ];

        $copy_id = wp_insert_post( $copy_data, true );
        if ( is_wp_error( $copy_id ) ) {
            return $copy_id;
        }

        // Copia tutti i meta (eccetto quelli interni olo_lang)
        $all_meta = get_post_meta( $post_id );
        foreach ( $all_meta as $key => $values ) {
            if ( strpos( $key, '_olo_lang_' ) === 0 ) {
                continue;
            }
            foreach ( $values as $value ) {
                add_post_meta( $copy_id, $key, maybe_unserialize( $value ) );
            }
        }

        // Copia la featured image
        $thumb_id = get_post_thumbnail_id( $post_id );
        if ( $thumb_id ) {
            set_post_thumbnail( $copy_id, $thumb_id );
        }

        // Copia le tassonomie
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $tax ) {
            $terms = wp_get_object_terms( $post_id, $tax, [ 'fields' => 'ids' ] );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                wp_set_object_terms( $copy_id, $terms, $tax );
            }
        }

        // Segna la copia
        update_post_meta( $copy_id, '_olo_lang_source', $post_id );
        update_post_meta( $copy_id, '_olo_lang_lang', $lang );

        // Aggiorna la mappa traduzioni nell'originale
        $existing[ $lang ] = $copy_id;
        update_post_meta( $post_id, '_olo_lang_translations', $existing );

        return $copy_id;
    }

    // =========================================================================
    // Colonna admin — stato traduzioni nella lista olo_service
    // =========================================================================

    public function add_columns( $columns ) {
        $config    = Olo_Lang_Language::get_config();
        $languages = Olo_Lang_Language::get_active_languages();

        if ( count( $languages ) < 2 ) {
            return $columns;
        }

        $new = [];
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( $key === 'title' ) {
                $new['olo_lang'] = 'Traduzioni';
            }
        }
        return $new;
    }

    public function render_column( $column, $post_id ) {
        if ( $column !== 'olo_lang' ) {
            return;
        }

        // Non mostrare per le copie
        $source_id = get_post_meta( $post_id, '_olo_lang_source', true );
        if ( $source_id ) {
            $lang = get_post_meta( $post_id, '_olo_lang_lang', true );
            echo '<span style="color:#9ca3af;font-size:11px">copia ' . esc_html( strtoupper( $lang ) ) . '</span>';
            return;
        }

        $config    = Olo_Lang_Language::get_config();
        $default   = $config['default_lang'];
        $languages = Olo_Lang_Language::get_active_languages();
        $translations = get_post_meta( $post_id, '_olo_lang_translations', true );
        $translations = is_array( $translations ) ? $translations : [];

        foreach ( $languages as $lang ) {
            if ( $lang['code'] === $default ) {
                continue;
            }
            $copy_id = $translations[ $lang['code'] ] ?? 0;
            $copy    = $copy_id ? get_post( $copy_id ) : null;

            if ( $copy && $copy->post_status !== 'trash' ) {
                $color = $copy->post_status === 'publish' ? '#16a34a' : '#f59e0b';
                $icon  = $copy->post_status === 'publish' ? '&#10003;' : '&#9998;';
                echo '<a href="' . esc_url( get_edit_post_link( $copy_id ) ) . '" '
                   . 'style="color:' . $color . ';font-weight:600;text-decoration:none;margin-right:6px" '
                   . 'title="' . esc_attr( $lang['name'] . ': ' . $copy->post_title ) . '">'
                   . esc_html( strtoupper( $lang['code'] ) ) . ' ' . $icon
                   . '</a>';
            } else {
                echo '<span style="color:#d1d5db;margin-right:6px" title="' . esc_attr( $lang['name'] . ': non tradotto' ) . '">'
                   . esc_html( strtoupper( $lang['code'] ) )
                   . '</span>';
            }
        }
    }

    // =========================================================================
    // Frontend — Query filter: mostra copie nella lingua corrente
    // =========================================================================

    public function filter_query_by_lang( $query ) {
        if ( is_admin() ) {
            return;
        }

        // Applica solo ai post type con traduzioni abilitate
        $queried_type = $query->get( 'post_type' );
        if ( $queried_type ) {
            // Normalizza a stringa singola se array di uno
            $types = (array) $queried_type;
            // Se nessuno dei tipi richiesti ha traduzioni, esci
            $has_translatable = false;
            foreach ( $types as $t ) {
                if ( in_array( $t, $this->enabled_post_types, true ) ) {
                    $has_translatable = true;
                    break;
                }
            }
            if ( ! $has_translatable ) {
                return;
            }
        }

        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            // Lingua default: nascondi le copie tradotte nelle query dei post type abilitati
            // Eccezione: non filtrare la main query singolare (il post specifico richiesto)
            if ( $query->is_main_query() && $query->is_singular() ) {
                return;
            }

            $meta_query = $query->get( 'meta_query' ) ?: [];
            $meta_query[] = [
                'relation' => 'OR',
                [
                    'key'     => '_olo_lang_source',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'   => '_olo_lang_source',
                    'value' => '',
                ],
            ];
            $query->set( 'meta_query', $meta_query );
        }
        // Lingua target: lo swap viene gestito da swap_archive_posts (registrato in init)
    }

    /**
     * Per singoli: se esiste una copia nella lingua corrente, sostituisci il post.
     */
    public function swap_single_post( $posts, $query ) {
        if ( is_admin() || ! $query->is_main_query() || ! $query->is_singular() ) {
            return $posts;
        }

        if ( empty( $posts ) ) {
            return $posts;
        }

        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            return $posts;
        }

        $post = $posts[0];

        // Se sto gia' visualizzando una copia nella lingua corretta, OK
        $post_lang = get_post_meta( $post->ID, '_olo_lang_lang', true );
        if ( $post_lang === $lang ) {
            return $posts;
        }

        // Se e' un originale con una copia nella lingua target, swappa
        $translations = get_post_meta( $post->ID, '_olo_lang_translations', true );
        $translations = is_array( $translations ) ? $translations : [];

        if ( ! empty( $translations[ $lang ] ) ) {
            $copy = get_post( $translations[ $lang ] );
            if ( $copy && $copy->post_status === 'publish' ) {
                return [ $copy ];
            }
        }

        return $posts;
    }

    /**
     * Per archivi e query secondarie (PostGrid, widget, ecc.):
     * sostituisci gli originali con le copie nella lingua corrente.
     * Registrato permanentemente in init() — NON si auto-rimuove.
     */
    public function swap_archive_posts( $posts, $query ) {
        // Solo frontend, non per query singolari della main query (gestite da swap_single_post)
        if ( is_admin() ) {
            return $posts;
        }

        if ( $query->is_main_query() && $query->is_singular() ) {
            return $posts;
        }

        if ( empty( $posts ) ) {
            return $posts;
        }

        $lang    = Olo_Lang_Language::detect_current_lang();
        $default = Olo_Lang_Language::get_default_lang();

        if ( $lang === $default ) {
            return $posts;
        }

        $result = [];
        $seen_sources = [];

        foreach ( $posts as $post ) {
            $source_id = (int) get_post_meta( $post->ID, '_olo_lang_source', true );

            if ( $source_id ) {
                // E' una copia — mostra solo se nella lingua corrente
                $post_lang = get_post_meta( $post->ID, '_olo_lang_lang', true );
                if ( $post_lang === $lang ) {
                    $result[] = $post;
                    $seen_sources[ $source_id ] = true;
                }
                continue;
            }

            // E' un originale — cerca se ha una copia nella lingua corrente
            $translations = get_post_meta( $post->ID, '_olo_lang_translations', true );
            $translations = is_array( $translations ) ? $translations : [];

            if ( ! empty( $translations[ $lang ] ) ) {
                $copy = get_post( $translations[ $lang ] );
                if ( $copy && $copy->post_status === 'publish' ) {
                    if ( empty( $seen_sources[ $post->ID ] ) ) {
                        $result[] = $copy;
                        $seen_sources[ $post->ID ] = true;
                    }
                    continue;
                }
            }

            // Nessuna copia: mostra l'originale
            if ( empty( $seen_sources[ $post->ID ] ) ) {
                $result[] = $post;
            }
        }

        return $result;
    }

    // =========================================================================
    // Utility
    // =========================================================================

    /**
     * Ottieni l'ID dell'originale di un post (se copia, ritorna source; se originale, ritorna se stesso).
     */
    public static function get_source_id( $post_id ) {
        $source = (int) get_post_meta( $post_id, '_olo_lang_source', true );
        return $source ?: $post_id;
    }

    /**
     * Ottieni la copia di un post per una lingua specifica.
     */
    public static function get_translation( $post_id, $lang ) {
        $source_id = self::get_source_id( $post_id );
        $translations = get_post_meta( $source_id, '_olo_lang_translations', true );
        $translations = is_array( $translations ) ? $translations : [];
        return $translations[ $lang ] ?? 0;
    }
}
