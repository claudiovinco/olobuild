<?php
/**
 * I prodotti di prova di un tema-negozio di OLObuild (demo sul sottosito), da un file JSON scritto dal generatore del
 * tema (scripts/temi/prodotti/<id>.json): impostazioni di WooCommerce, attributi, categorie, prodotti semplici e a
 * varianti (taglie), foto, video del prodotto, recensioni, upsell e cross-sell.
 * Si rilancia senza danni: i prodotti si ritrovano dallo SKU, le foto dal nome del file d'origine. Ogni prodotto,
 * categoria e foto porta _olo_demo = <id del tema>: la demo del tema mostra solo i suoi.
 * uso: wp eval-file prodotti-demo.php <file.json>
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce non è attivo' );
}
$D = json_decode( (string) file_get_contents( $args[0] ), true );
if ( ! is_array( $D ) || empty( $D['tema'] ) ) {
	WP_CLI::error( 'file dei prodotti non valido' );
}
$TEMA = (string) $D['tema'];
$LIB  = ABSPATH . '../olobuild-library/media/';
$URL  = Olobuild_Template_Library::library_url() . '/media/';

// ── Impostazioni (le stesse di Fulmine: euro, Italia, recensioni, AJAX) ─────────────────────────────────────────────
$opts = [
	'woocommerce_default_country' => 'IT', 'woocommerce_currency' => 'EUR', 'woocommerce_currency_pos' => 'right_space',
	'woocommerce_price_thousand_sep' => '.', 'woocommerce_price_decimal_sep' => ',', 'woocommerce_price_num_decimals' => '2',
	'woocommerce_coming_soon' => 'no', 'woocommerce_enable_reviews' => 'yes', 'woocommerce_enable_review_rating' => 'yes',
	// le recensioni le scrive solo chi ha comprato il prodotto
	'woocommerce_review_rating_verification_required' => 'yes', 'woocommerce_review_rating_verification_label' => 'yes',
	'woocommerce_manage_stock' => 'yes', 'woocommerce_notify_low_stock_amount' => '5', 'woocommerce_enable_ajax_add_to_cart' => 'yes',
	'woocommerce_cart_redirect_after_add' => 'no', 'woocommerce_enable_guest_checkout' => 'yes',
];
foreach ( $opts as $k => $v ) {
	update_option( $k, $v );
}

function olo_demo_foto( $file, $alt, $tema, $lib ) {
	$gia = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids',
		'meta_key' => '_olo_demo_src', 'meta_value' => $file ] );
	if ( $gia ) {
		return (int) $gia[0];
	}
	if ( ! is_readable( $lib . $file ) ) {
		WP_CLI::warning( 'manca ' . $file );
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $file );
	copy( $lib . $file, $tmp );
	$id = media_handle_sideload( [ 'name' => $tema . '-' . $file, 'tmp_name' => $tmp ], 0, $alt );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( $file . ': ' . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_olo_demo_src', $file );
	update_post_meta( $id, '_olo_demo', $tema );
	return (int) $id;
}

// ── Attributi globali (filtrabili): taglia, marca, formato… ──────────────────────────────────────────────────────────
foreach ( (array) ( $D['attributi'] ?? [] ) as $slug => $a ) {
	if ( ! wc_attribute_taxonomy_id_by_name( $slug ) ) {
		wc_create_attribute( [ 'name' => $a['nome'], 'slug' => $slug, 'type' => 'select', 'order_by' => 'menu_order', 'has_archives' => false ] );
	}
	$tax = 'pa_' . $slug;
	if ( ! taxonomy_exists( $tax ) ) {
		register_taxonomy( $tax, 'product', [ 'hierarchical' => false, 'show_ui' => false, 'query_var' => true, 'rewrite' => false ] );
	}
	foreach ( (array) ( $a['termini'] ?? [] ) as $ordine => $nome ) {
		$t = term_exists( $nome, $tax );
		if ( ! $t ) {
			$t = wp_insert_term( $nome, $tax );
		}
		if ( ! is_wp_error( $t ) ) {
			update_term_meta( (int) $t['term_id'], 'order', $ordine );
		}
	}
}
function olo_demo_termine( $tax, $nome ) {
	$t = term_exists( $nome, $tax );
	if ( ! $t ) {
		$t = wp_insert_term( $nome, $tax );
	}
	return is_wp_error( $t ) ? 0 : (int) $t['term_id'];
}

// ── Categorie ────────────────────────────────────────────────────────────────────────────────────────────────────────
$cat_id = [];
foreach ( (array) $D['categorie'] as $ordine => $c ) {
	$t  = get_term_by( 'slug', $c['slug'], 'product_cat' );
	$id = $t ? (int) $t->term_id : (int) wp_insert_term( $c['nome'], 'product_cat', [ 'slug' => $c['slug'], 'description' => $c['descrizione'] ] )['term_id'];
	wp_update_term( $id, 'product_cat', [ 'name' => $c['nome'], 'description' => $c['descrizione'] ] );
	update_term_meta( $id, 'thumbnail_id', olo_demo_foto( $c['foto'], $c['nome'], $TEMA, $LIB ) );
	update_term_meta( $id, '_olo_demo', $TEMA );
	update_term_meta( $id, 'order', $ordine );
	$cat_id[ $c['slug'] ] = $id;
}

// ── Prodotti ─────────────────────────────────────────────────────────────────────────────────────────────────────────
$NOMI  = (array) $D['nomi'];
$TESTI = (array) $D['recensioni'];
$fatti = 0;
foreach ( (array) $D['prodotti'] as $i => $d ) {
	$var   = $d['varianti'] ?? null;    // { "attributo": "taglia", "valori": [...], "esaurite": [...] }
	$id    = wc_get_product_id_by_sku( $d['sku'] );
	$esiste = $id ? wc_get_product( $id ) : null;
	if ( $esiste && ( $var ? ! $esiste->is_type( 'variable' ) : $esiste->is_type( 'variable' ) ) ) {
		$esiste->delete( true );
		$esiste = null;
	}
	$p = $esiste ?: ( $var ? new WC_Product_Variable() : new WC_Product_Simple() );
	$p->set_name( $d['nome'] );
	$p->set_slug( $d['slug'] );
	$p->set_sku( $d['sku'] );
	$p->set_status( 'publish' );
	$p->set_catalog_visibility( 'visible' );
	$p->set_featured( ! empty( $d['evidenza'] ) );
	$p->set_category_ids( array_values( array_filter( array_map( function ( $s ) use ( $cat_id ) { return $cat_id[ $s ] ?? 0; }, (array) $d['cat'] ) ) ) );
	$punti = (array) ( $d['punti'] ?? [] );
	$p->set_short_description( $punti ? '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', $punti ) ) . '</li></ul>' : '' );
	$p->set_description( '<p>' . esc_html( $d['testo'] ) . '</p>' . ( $punti ? '<h3>In breve</h3><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $punti ) ) . '</li></ul>' : '' )
		. ( ! empty( $d['dentro'] ) ? '<h3>' . esc_html( $D['titolo_dentro'] ?? 'Nella confezione' ) . '</h3><p>' . esc_html( $d['dentro'] ) . '</p>' : '' ) );
	$p->set_reviews_allowed( true );
	if ( ! $var ) {
		$p->set_regular_price( (string) $d['prezzo'] );
		$p->set_sale_price( ! empty( $d['sconto'] ) ? (string) $d['sconto'] : '' );
		$p->set_manage_stock( true );
		$p->set_stock_quantity( (int) $d['scorta'] );
		$p->set_low_stock_amount( 5 );
	} else {
		$p->set_manage_stock( false );
	}
	$ids = array_values( array_filter( array_map( function ( $f ) use ( $d, $TEMA, $LIB ) { return olo_demo_foto( $f, $d['nome'], $TEMA, $LIB ); }, (array) $d['foto'] ) ) );
	if ( $ids ) {
		$p->set_image_id( $ids[0] );
		$p->set_gallery_image_ids( array_slice( $ids, 1 ) );
	}
	if ( ! empty( $d['peso'] ) ) {
		$p->set_weight( (string) $d['peso'] );
	}
	// Attributi: quelli di scheda (marca, formato…) e quello delle varianti (taglia).
	$attr  = [];
	$terms = [];
	foreach ( (array) ( $d['attributi'] ?? [] ) as $slug => $valori ) {
		$tax = 'pa_' . $slug;
		$tid = array_values( array_filter( array_map( function ( $v ) use ( $tax ) { return olo_demo_termine( $tax, $v ); }, (array) $valori ) ) );
		$a   = new WC_Product_Attribute();
		$a->set_id( wc_attribute_taxonomy_id_by_name( $slug ) );
		$a->set_name( $tax );
		$a->set_options( $tid );
		$a->set_visible( true );
		$a->set_variation( false );
		$attr[]        = $a;
		$terms[ $tax ] = $tid;
	}
	if ( $var ) {
		$tax = 'pa_' . $var['attributo'];
		$tid = array_values( array_filter( array_map( function ( $v ) use ( $tax ) { return olo_demo_termine( $tax, $v ); }, (array) $var['valori'] ) ) );
		$a   = new WC_Product_Attribute();
		$a->set_id( wc_attribute_taxonomy_id_by_name( $var['attributo'] ) );
		$a->set_name( $tax );
		$a->set_options( $tid );
		$a->set_visible( true );
		$a->set_variation( true );
		$attr[]        = $a;
		$terms[ $tax ] = $tid;
	}
	$p->set_attributes( $attr );
	$pid = $p->save();
	foreach ( $terms as $tax => $tid ) {
		wp_set_object_terms( $pid, $tid, $tax );
	}
	update_post_meta( $pid, '_olo_demo', $TEMA );
	// Il video del prodotto (Gallery Prodotto WC): un file della libreria, con la sua copertina.
	if ( ! empty( $d['video'] ) ) {
		update_post_meta( $pid, '_olo_video', $URL . $d['video'] );
		update_post_meta( $pid, '_olo_video_poster', ! empty( $d['poster'] ) ? $URL . $d['poster'] : '' );
	} else {
		delete_post_meta( $pid, '_olo_video' );
		delete_post_meta( $pid, '_olo_video_poster' );
	}
	// Le varianti: una per valore, col prezzo del prodotto; quelle «esaurite» a scorta zero.
	if ( $var ) {
		$tax      = 'pa_' . $var['attributo'];
		$esaurite = (array) ( $var['esaurite'] ?? [] );
		$attuali  = [];
		foreach ( wc_get_product( $pid )->get_children() as $cid ) {
			$v = wc_get_product( $cid );
			$attuali[ $v->get_attribute( $tax ) ] = $v;
		}
		foreach ( (array) $var['valori'] as $n => $valore ) {
			$term = get_term_by( 'name', $valore, $tax );
			$v    = $attuali[ $valore ] ?? new WC_Product_Variation();
			$v->set_parent_id( $pid );
			$v->set_attributes( [ $tax => $term ? $term->slug : sanitize_title( $valore ) ] );
			$v->set_regular_price( (string) $d['prezzo'] );
			$v->set_sale_price( ! empty( $d['sconto'] ) ? (string) $d['sconto'] : '' );
			$v->set_manage_stock( true );
			$v->set_stock_quantity( in_array( $valore, $esaurite, true ) ? 0 : max( 2, (int) round( (int) $d['scorta'] / max( 1, count( $var['valori'] ) ) ) + ( $n % 3 ) ) );
			$v->set_sku( $d['sku'] . '-' . sanitize_title( $valore ) );
			$v->set_menu_order( $n );
			$v->set_status( 'publish' );
			$v->save();
		}
		WC_Product_Variable::sync( $pid );
	}

	// Tre recensioni vere, poi i numeri del negozio.
	if ( ! get_comments( [ 'post_id' => $pid, 'count' => true ] ) ) {
		for ( $k = 0; $k < 3; $k++ ) {
			$r = $TESTI[ ( $i * 3 + $k ) % count( $TESTI ) ];
			$c = wp_insert_comment( [ 'comment_post_ID' => $pid, 'comment_author' => $NOMI[ ( $i + $k * 5 ) % count( $NOMI ) ],
				'comment_author_email' => 'cliente' . ( $i * 3 + $k ) . '@' . $TEMA . '.example', 'comment_content' => $r[1],
				'comment_type' => 'review', 'comment_approved' => 1, 'comment_date' => gmdate( 'Y-m-d H:i:s', time() - ( $i * 3 + $k + 1 ) * 86400 * 4 ) ] );
			update_comment_meta( $c, 'rating', $r[0] );
			update_comment_meta( $c, 'verified', 1 );
			update_comment_meta( $c, 'verified', 1 );
		}
	}
	$p      = wc_get_product( $pid );
	$voto   = (float) $d['voto'];
	$n_rec  = (int) $d['recensioni'];
	$cinque = (int) round( $n_rec * max( 0.35, min( 0.85, ( $voto - 3.6 ) / 1.4 ) ) );
	$resto  = $n_rec - $cinque;
	$conte  = [ 5 => $cinque, 4 => (int) round( $resto * 0.6 ), 3 => (int) round( $resto * 0.22 ), 2 => (int) round( $resto * 0.1 ) ];
	$conte[1] = max( 0, $n_rec - array_sum( $conte ) );
	$p->set_rating_counts( $conte );
	$p->set_average_rating( $voto );
	$p->set_review_count( $n_rec );
	$p->set_total_sales( (int) round( $n_rec * 2.4 ) );
	$p->save();
	$fatti++;
}

// ── Upsell (la versione superiore) e cross-sell (gli abbinamenti: il set nella scheda, i consigli nel carrello) ──────
$da_sku = static function ( $skus ) {
	return array_values( array_filter( array_map( 'wc_get_product_id_by_sku', (array) $skus ) ) );
};
foreach ( (array) $D['prodotti'] as $d ) {
	$p = wc_get_product( wc_get_product_id_by_sku( $d['sku'] ) );
	if ( $p ) {
		$p->set_upsell_ids( $da_sku( $d['upsell'] ?? [] ) );
		$p->set_cross_sell_ids( $da_sku( $d['cross'] ?? [] ) );
		$p->save();
	}
}
if ( function_exists( 'wc_delete_product_transients' ) ) {
	wc_delete_product_transients();
}
WP_CLI::success( $TEMA . ': ' . $fatti . ' prodotti · ' . count( $cat_id ) . ' categorie' );
