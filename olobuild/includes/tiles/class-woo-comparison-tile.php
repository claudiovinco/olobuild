<?php
/**
 * WooCommerce Product Comparison tile — compare up to 4 products side by side.
 *
 * Renders a comparison table with images, prices, attributes, ratings,
 * and add-to-cart buttons. Products are added via AJAX "Compare" buttons.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Woo_Comparison_Tile extends Olo_Tile_Base {

    protected $type     = 'woo_comparison';
    protected $name     = 'WC Confronto Prodotti';
    protected $icon     = 'dashicons-columns';
    protected $category = 'woocommerce';
    protected $defaults = [
        'max_products'    => 4,
        'show_image'      => true,
        'show_price'      => true,
        'show_rating'     => true,
        'show_stock'      => true,
        'show_sku'        => true,
        'show_description' => true,
        'show_attributes' => true,
        'show_add_to_cart' => true,
        'header_bg'       => '#F9FAFB',
        'header_color'    => '',
        'border_color'    => '#E5E7EB',
        'accent_color'    => '#4f46e5',
        'btn_bg'          => '#4f46e5',
        'btn_color'       => '#FFFFFF',
        'empty_text'      => 'Aggiungi prodotti da confrontare usando il pulsante "Confronta".',
    ];

    public function get_controls() {
        return [];
    }

    /**
     * Register REST endpoint for fetching comparison data.
     */
    public static function register_rest_routes() {
        register_rest_route( 'olo/v1', '/woo-compare', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'api_compare' ],
            'permission_callback' => '__return_true',
            'args' => [
                'ids' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );
    }

    public static function api_compare( $request ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return rest_ensure_response( [] );
        }

        $raw = $request->get_param( 'ids' );
        $ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
        $ids = array_slice( $ids, 0, 6 );

        $results = [];
        foreach ( $ids as $pid ) {
            $product = wc_get_product( $pid );
            if ( ! $product ) {
                continue;
            }

            $image = get_the_post_thumbnail_url( $pid, 'woocommerce_thumbnail' );
            $attrs = [];
            foreach ( $product->get_attributes() as $attr ) {
                $name = wc_attribute_label( $attr->get_name() );
                if ( $attr->is_taxonomy() ) {
                    $vals = wc_get_product_terms( $pid, $attr->get_name(), [ 'fields' => 'names' ] );
                    $attrs[ $name ] = implode( ', ', $vals );
                } else {
                    $attrs[ $name ] = implode( ', ', $attr->get_options() );
                }
            }

            $results[] = [
                'id'                => $pid,
                'name'              => $product->get_name(),
                'image'             => $image ?: '',
                'price_html'        => $product->get_price_html(),
                'price'             => $product->get_price(),
                'rating'            => floatval( $product->get_average_rating() ),
                'review_count'      => intval( $product->get_review_count() ),
                'in_stock'          => $product->is_in_stock(),
                'sku'               => $product->get_sku(),
                'short_description' => wp_strip_all_tags( $product->get_short_description() ),
                'attributes'        => $attrs,
                'add_to_cart_url'   => $product->add_to_cart_url(),
                'add_to_cart_text'  => $product->add_to_cart_text(),
                'permalink'         => get_permalink( $pid ),
            ];
        }

        return rest_ensure_response( $results );
    }

    public function render( $settings ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return '<p style="color:var(--olo-color-text-muted, #9CA3AF);text-align:center;padding:40px">WooCommerce non attivo</p>';
        }

        $s   = wp_parse_args( $settings, $this->defaults );
        $uid = 'olo-cmp-' . wp_rand( 10000, 99999 );

        $max        = max( 2, min( 6, intval( $s['max_products'] ) ) );
        $hdr_bg     = $this->safe_color_css( $s['header_bg'] ) ?: '#F9FAFB';
        $hdr_c      = $this->safe_color_css( $s['header_color'] ) ?: 'var(--olo-color-text, #374151)';
        $bdr_c      = $this->safe_color_css( $s['border_color'] ) ?: '#E5E7EB';
        $accent     = $this->safe_color_css( $s['accent_color'] ) ?: '#4f46e5';
        $btn_bg     = $this->safe_color_css( $s['btn_bg'] ) ?: '#4f46e5';
        $btn_c      = $this->safe_color_css( $s['btn_color'] ) ?: '#FFFFFF';
        $empty_text = esc_html( $s['empty_text'] );

        ob_start();
        ?>
        <style>
        #<?php echo $uid; ?>{width:100%;overflow-x:auto}
        #<?php echo $uid; ?> table{width:100%;border-collapse:collapse;min-width:600px}
        #<?php echo $uid; ?> th{background:<?php echo $hdr_bg; ?>;color:<?php echo $hdr_c; ?>;padding:12px 16px;text-align:left;font-weight:600;border:1px solid <?php echo $bdr_c; ?>;white-space:nowrap}
        #<?php echo $uid; ?> td{padding:12px 16px;border:1px solid <?php echo $bdr_c; ?>;vertical-align:middle;text-align:center}
        #<?php echo $uid; ?> td:first-child{text-align:left;font-weight:500;background:<?php echo $hdr_bg; ?>}
        #<?php echo $uid; ?> .cmp-img{max-width:120px;height:auto;border-radius:6px}
        #<?php echo $uid; ?> .cmp-price{font-size:18px;font-weight:700;color:<?php echo $accent; ?>}
        #<?php echo $uid; ?> .cmp-btn{display:inline-block;padding:8px 20px;background:<?php echo $btn_bg; ?>;color:<?php echo $btn_c; ?>;border:none;border-radius:6px;cursor:pointer;text-decoration:none;font-size:14px;transition:opacity .2s}
        #<?php echo $uid; ?> .cmp-btn:hover{opacity:.85}
        #<?php echo $uid; ?> .cmp-remove{display:inline-block;width:24px;height:24px;line-height:24px;text-align:center;background:var(--olo-color-danger, #EF4444);color:#fff;border-radius:50%;cursor:pointer;font-size:14px;text-decoration:none;margin-top:8px}
        #<?php echo $uid; ?> .cmp-stars{color:var(--olo-color-warning, #F59E0B);letter-spacing:1px}
        #<?php echo $uid; ?> .cmp-stock-in{color:var(--olo-color-success, #10B981);font-weight:500}
        #<?php echo $uid; ?> .cmp-stock-out{color:var(--olo-color-danger, #EF4444);font-weight:500}
        #<?php echo $uid; ?> .cmp-empty{text-align:center;padding:40px 20px;color:var(--olo-color-text-muted, #9CA3AF)}
        </style>

        <div id="<?php echo esc_attr( $uid ); ?>" class="olo-woo-comparison" data-max="<?php echo $max; ?>">
            <div class="cmp-empty" id="<?php echo $uid; ?>-empty"><?php echo $empty_text; ?></div>
            <div id="<?php echo $uid; ?>-table" style="display:none"></div>
        </div>

        <script>
        (function(){
            var uid = '<?php echo $uid; ?>';
            var max = <?php echo $max; ?>;
            var showImage = <?php echo ! empty( $s['show_image'] ) ? 'true' : 'false'; ?>;
            var showPrice = <?php echo ! empty( $s['show_price'] ) ? 'true' : 'false'; ?>;
            var showRating = <?php echo ! empty( $s['show_rating'] ) ? 'true' : 'false'; ?>;
            var showStock = <?php echo ! empty( $s['show_stock'] ) ? 'true' : 'false'; ?>;
            var showSku = <?php echo ! empty( $s['show_sku'] ) ? 'true' : 'false'; ?>;
            var showDesc = <?php echo ! empty( $s['show_description'] ) ? 'true' : 'false'; ?>;
            var showAttrs = <?php echo ! empty( $s['show_attributes'] ) ? 'true' : 'false'; ?>;
            var showCart = <?php echo ! empty( $s['show_add_to_cart'] ) ? 'true' : 'false'; ?>;

            var KEY = 'olo_compare_ids';

            function getIds() {
                try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch(e) { return []; }
            }
            function setIds(ids) {
                localStorage.setItem(KEY, JSON.stringify(ids.slice(0, max)));
                render();
            }

            function render() {
                var ids = getIds();
                var emptyEl = document.getElementById(uid + '-empty');
                var tableEl = document.getElementById(uid + '-table');

                if (!ids.length) {
                    emptyEl.style.display = 'block';
                    tableEl.style.display = 'none';
                    return;
                }

                emptyEl.style.display = 'none';
                tableEl.style.display = 'block';
                tableEl.innerHTML = '<p style="text-align:center;color:var(--olo-color-text-muted, #9CA3AF)">Caricamento confronto...</p>';

                fetch('<?php echo esc_js( rest_url( 'olo/v1/woo-compare' ) ); ?>?ids=' + ids.join(','), {
                    headers: { 'X-WP-Nonce': '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>' }
                })
                .then(function(r) { return r.json(); })
                .then(function(products) {
                    if (!products.length) {
                        emptyEl.style.display = 'block';
                        tableEl.style.display = 'none';
                        return;
                    }
                    buildTable(products);
                })
                .catch(function() {
                    tableEl.innerHTML = '<p style="color:var(--olo-color-danger, #EF4444);text-align:center">Errore caricamento</p>';
                });
            }

            function buildTable(products) {
                var tableEl = document.getElementById(uid + '-table');
                var html = '<table>';

                // Header row
                html += '<tr><th></th>';
                products.forEach(function(p) {
                    html += '<th style="text-align:center">' + esc(p.name) + '</th>';
                });
                html += '</tr>';

                // Image row
                if (showImage) {
                    html += '<tr><td>Immagine</td>';
                    products.forEach(function(p) {
                        html += '<td>';
                        if (p.image) { html += '<img src="' + esc(p.image) + '" class="cmp-img" alt="' + esc(p.name) + '" />'; }
                        html += '<br/><a href="#" class="cmp-remove" data-id="' + p.id + '" title="Rimuovi">&times;</a>';
                        html += '</td>';
                    });
                    html += '</tr>';
                }

                // Price
                if (showPrice) {
                    html += '<tr><td>Prezzo</td>';
                    products.forEach(function(p) { html += '<td class="cmp-price">' + p.price_html + '</td>'; });
                    html += '</tr>';
                }

                // Rating
                if (showRating) {
                    html += '<tr><td>Valutazione</td>';
                    products.forEach(function(p) {
                        var stars = '';
                        for (var i = 1; i <= 5; i++) { stars += i <= Math.round(p.rating) ? '&#9733;' : '&#9734;'; }
                        html += '<td class="cmp-stars">' + stars + ' (' + p.review_count + ')</td>';
                    });
                    html += '</tr>';
                }

                // Stock
                if (showStock) {
                    html += '<tr><td>Disponibilità</td>';
                    products.forEach(function(p) {
                        var cls = p.in_stock ? 'cmp-stock-in' : 'cmp-stock-out';
                        html += '<td class="' + cls + '">' + (p.in_stock ? 'Disponibile' : 'Non disponibile') + '</td>';
                    });
                    html += '</tr>';
                }

                // SKU
                if (showSku) {
                    html += '<tr><td>SKU</td>';
                    products.forEach(function(p) { html += '<td>' + esc(p.sku || '—') + '</td>'; });
                    html += '</tr>';
                }

                // Description
                if (showDesc) {
                    html += '<tr><td>Descrizione</td>';
                    products.forEach(function(p) { html += '<td style="font-size:13px;text-align:left">' + (p.short_description || '—') + '</td>'; });
                    html += '</tr>';
                }

                // Attributes
                if (showAttrs) {
                    var allAttrs = {};
                    products.forEach(function(p) {
                        if (p.attributes) {
                            Object.keys(p.attributes).forEach(function(k) { allAttrs[k] = true; });
                        }
                    });
                    Object.keys(allAttrs).forEach(function(attr) {
                        html += '<tr><td>' + esc(attr) + '</td>';
                        products.forEach(function(p) {
                            html += '<td>' + esc((p.attributes || {})[attr] || '—') + '</td>';
                        });
                        html += '</tr>';
                    });
                }

                // Add to cart
                if (showCart) {
                    html += '<tr><td></td>';
                    products.forEach(function(p) {
                        html += '<td><a href="' + esc(p.add_to_cart_url) + '" class="cmp-btn">' + esc(p.add_to_cart_text) + '</a></td>';
                    });
                    html += '</tr>';
                }

                html += '</table>';
                tableEl.innerHTML = html;

                // Remove buttons
                tableEl.querySelectorAll('.cmp-remove').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var rid = parseInt(this.getAttribute('data-id'));
                        var ids = getIds().filter(function(id) { return id !== rid; });
                        setIds(ids);
                    });
                });
            }

            function esc(s) {
                if (!s) return '';
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            // Global function to add product to comparison
            window.oloCompareAdd = function(id) {
                var ids = getIds();
                id = parseInt(id);
                if (ids.indexOf(id) === -1) {
                    if (ids.length >= max) { ids.shift(); }
                    ids.push(id);
                    setIds(ids);
                }
            };

            window.oloCompareRemove = function(id) {
                var ids = getIds().filter(function(i) { return i !== parseInt(id); });
                setIds(ids);
            };

            render();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
