<?php
/**
 * Fulmine (demo di OLObuild sul sottosito): impostazioni di WooCommerce e i prodotti di prova.
 * Si rilancia senza danni: i prodotti si ritrovano dallo SKU, le foto dal nome del file d'origine.
 * Ogni prodotto, categoria e foto porta _olo_demo = fulmine.
 * uso: wp eval-file prodotti-fulmine.php
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce non è attivo' );
}
define( 'FUL_LIB', ABSPATH . '../olobuild-library/media/' );
define( 'FUL_TEMA', 'fulmine' );
$TEMA = FUL_TEMA;

// ── Impostazioni ─────────────────────────────────────────────────────────────
$opts = [
	'woocommerce_default_country' => 'IT', 'woocommerce_currency' => 'EUR', 'woocommerce_currency_pos' => 'right_space',
	'woocommerce_price_thousand_sep' => '.', 'woocommerce_price_decimal_sep' => ',', 'woocommerce_price_num_decimals' => '2',
	'woocommerce_coming_soon' => 'no', 'woocommerce_store_pages_only' => 'no', 'woocommerce_allow_tracking' => 'no',
	'woocommerce_show_marketplace_suggestions' => 'no', 'woocommerce_task_list_hidden' => 'yes',
	'woocommerce_extended_task_list_hidden' => 'yes', 'woocommerce_enable_reviews' => 'yes',
	'woocommerce_review_rating_verification_required' => 'no', 'woocommerce_review_rating_verification_label' => 'no',
	'woocommerce_enable_review_rating' => 'yes', 'woocommerce_review_rating_required' => 'yes', 'woocommerce_calc_taxes' => 'no',
	'woocommerce_weight_unit' => 'kg', 'woocommerce_dimension_unit' => 'cm', 'woocommerce_manage_stock' => 'yes',
	'woocommerce_notify_low_stock_amount' => '5', 'woocommerce_enable_ajax_add_to_cart' => 'yes',
	'woocommerce_cart_redirect_after_add' => 'no', 'woocommerce_analytics_enabled' => 'no',
	'woocommerce_feature_order_attribution_enabled' => 'no', 'woocommerce_allowed_countries' => 'specific',
	'woocommerce_specific_allowed_countries' => [ 'IT' ], 'woocommerce_ship_to_countries' => 'specific',
	'woocommerce_specific_ship_to_countries' => [ 'IT' ], 'woocommerce_enable_guest_checkout' => 'yes',
];
foreach ( $opts as $k => $v ) {
	update_option( $k, $v );
}
update_option( 'woocommerce_onboarding_profile', [ 'skipped' => true, 'completed' => true ] );

// ── Foto ─────────────────────────────────────────────────────────────────────
function fulmine_foto( $file, $alt ) {
	$gia = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids',
		'meta_key' => '_olo_demo_src', 'meta_value' => $file ] );
	if ( $gia ) {
		return (int) $gia[0];
	}
	if ( ! is_readable( FUL_LIB . $file ) ) {
		WP_CLI::warning( 'manca ' . $file );
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $file );
	copy( FUL_LIB . $file, $tmp );
	$id = media_handle_sideload( [ 'name' => 'fulmine-' . $file, 'tmp_name' => $tmp ], 0, $alt );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( $file . ': ' . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_olo_demo_src', $file );
	update_post_meta( $id, '_olo_demo', FUL_TEMA );
	return (int) $id;
}

// ── Categorie ────────────────────────────────────────────────────────────────
$CAT = [
	'audio'              => [ 'Audio', 'Cuffie, auricolari e casse che suonano bene davvero.', 'pexels-34075324.jpg' ],
	'smartphone-tablet'  => [ 'Smartphone e tablet', 'I telefoni e i tablet di quest’anno, sbloccati.', 'pexels-32912373.jpg' ],
	'computer'           => [ 'Computer', 'Portatili, monitor e tastiere per lavorare e giocare.', 'pexels-8533587.jpg' ],
	'casa-smart'         => [ 'Casa smart', 'Assistenti vocali, robot e luci che si comandano dal telefono.', 'pexels-35147280.jpg' ],
	'foto-droni'         => [ 'Foto e droni', 'Mirrorless, obiettivi e droni pieghevoli.', 'pexels-39342.jpg' ],
	'gaming'             => [ 'Gaming', 'Controller, visori e accessori per giocare.', 'pexels-18910365.jpg' ],
	'indossabili'        => [ 'Indossabili', 'Smartwatch e braccialetti per lo sport.', 'pexels-13007642.jpg' ],
	'tv'                 => [ 'TV e home cinema', 'OLED e QLED, soundbar e supporti.', 'pexels-7587783.jpg' ],
];
$cat_id = [];
foreach ( $CAT as $slug => $c ) {
	$t = get_term_by( 'slug', $slug, 'product_cat' );
	$id = $t ? (int) $t->term_id : (int) wp_insert_term( $c[0], 'product_cat', [ 'slug' => $slug, 'description' => $c[1] ] )['term_id'];
	wp_update_term( $id, 'product_cat', [ 'name' => $c[0], 'description' => $c[1] ] );
	update_term_meta( $id, 'thumbnail_id', fulmine_foto( $c[2], $c[0] ) );
	update_term_meta( $id, '_olo_demo', $TEMA );
	$cat_id[ $slug ] = $id;
}

// ── La marca, attributo globale (filtrabile) ────────────────────────────────
if ( ! wc_attribute_taxonomy_id_by_name( 'marca' ) ) {
	wc_create_attribute( [ 'name' => 'Marca', 'slug' => 'marca', 'type' => 'select', 'order_by' => 'name', 'has_archives' => false ] );
}
if ( ! taxonomy_exists( 'pa_marca' ) ) {
	register_taxonomy( 'pa_marca', 'product', [ 'hierarchical' => false, 'show_ui' => false, 'query_var' => true, 'rewrite' => false ] );
}

// ── I prodotti ───────────────────────────────────────────────────────────────
// sku, nome, slug, categoria, marca, prezzo, sconto, scorta, voto, recensioni, in evidenza, foto[], punti[], descrizione
$P = [
	[ 'FUL-001', 'Volta Pods Pro, auricolari con cancellazione del rumore', 'volta-pods-pro', 'audio', 'Volta', 179, 129, 34, 4.6, 3812, true,
	  [ 'pexels-16703772.jpg', 'pexels-16703782.jpg', 'pexels-22940565.jpg' ],
	  [ 'Cancellazione attiva del rumore fino a 45 dB', '30 ore con la custodia, 10 minuti di ricarica = 2 ore', 'Resistenti a sudore e pioggia (IPX5)', 'Audio spaziale con i movimenti della testa' ],
	  'Gli auricolari che usi tutto il giorno: in metro spengono il mondo, al telefono si sente solo la tua voce. La custodia si ricarica anche senza fili.' ],
	[ 'FUL-002', 'Volta Pods Lite, auricolari true wireless', 'volta-pods-lite', 'audio', 'Volta', 59.90, 0, 120, 4.3, 1240, false,
	  [ 'pexels-3585797.jpg', 'pexels-9610817.jpg' ],
	  [ 'Bluetooth 5.3, connessione a due dispositivi', '24 ore con la custodia', 'Comandi a sfioramento', 'Custodia tascabile da 38 g' ],
	  'Leggeri e senza pensieri: si collegano al telefono appena apri la custodia.' ],
	[ 'FUL-003', 'Arcus Studio ANC, cuffie wireless over-ear', 'arcus-studio-anc', 'audio', 'Arcus', 299, 219, 18, 4.7, 2104, true,
	  [ 'pexels-34075324.jpg', 'pexels-20385192.jpg' ],
	  [ 'Cancellazione del rumore adattiva', '40 ore di musica, ricarica USB-C', 'Driver da 40 mm, audio ad alta risoluzione', 'Si piegano in una custodia rigida' ],
	  'Morbide come un cuscino, silenziose come una biblioteca. Per i viaggi lunghi e le giornate in ufficio.' ],
	[ 'FUL-004', 'Arcus Monitor 7, cuffie da studio chiuse', 'arcus-monitor-7', 'audio', 'Arcus', 149, 0, 22, 4.5, 612, false,
	  [ 'pexels-8356854.jpg', 'pexels-8132514.jpg', 'pexels-8133287.jpg' ],
	  [ 'Risposta in frequenza piatta per il mixaggio', 'Cavo staccabile da 3 m', 'Padiglioni girevoli', 'Impedenza 32 Ω: vanno anche col telefono' ],
	  'Le cuffie di chi registra e monta: suonano come il disco, non come lo vorresti.' ],
	[ 'FUL-005', 'Kiri Go, cassa Bluetooth impermeabile', 'kiri-go', 'audio', 'Kiri', 79, 54.90, 3, 4.4, 5120, true,
	  [ 'pexels-12021852.jpg', 'pexels-31341733.jpg' ],
	  [ 'Impermeabile IP67: galleggia', '12 ore di musica', 'Due casse in stereo con un tocco', 'Cinghietta in silicone' ],
	  'La cassa da spiaggia, da doccia e da gita: ci si siede sopra la sabbia e continua a suonare.' ],
	[ 'FUL-006', 'Kiri Home, cassa wi-fi multiroom', 'kiri-home', 'audio', 'Kiri', 129, 0, 15, 4.2, 318, false,
	  [ 'pexels-32003907.jpg' ],
	  [ 'Wi-fi e Bluetooth', 'Multiroom con le altre Kiri', 'Assistente vocale integrato', 'Tessuto lavabile' ],
	  'Una cassa per stanza, la stessa musica in tutta la casa.' ],
	[ 'FUL-007', 'Nido Mini, smart speaker con orologio', 'nido-mini', 'casa-smart', 'Nido', 89, 69, 44, 4.1, 2870, false,
	  [ 'pexels-4790267.jpg', 'pexels-1279107.jpg' ],
	  [ 'Display con orologio e sveglia', 'Comanda luci e prese compatibili', 'Tasto fisico per spegnere i microfoni', 'Audio a 360°' ],
	  'Sul comodino: sveglia, meteo, la radio del mattino e le luci da spegnere senza alzarsi.' ],
	[ 'FUL-008', 'Nido Wood, smart speaker in legno', 'nido-wood', 'casa-smart', 'Nido', 119, 0, 9, 4.4, 506, false,
	  [ 'pexels-4790273.jpg' ],
	  [ 'Scocca in noce', 'Woofer da 3 pollici', 'Assistente vocale e AirPlay', 'Si accorda alla stanza da solo' ],
	  'L’assistente di casa che sembra un soprammobile.' ],
	[ 'FUL-009', 'Pixa 9, smartphone 5G 256 GB', 'pixa-9', 'smartphone-tablet', 'Pixa', 799, 699, 27, 4.6, 980, true,
	  [ 'pexels-7438754.jpg', 'pexels-32912373.jpg', 'pexels-33796884.jpg' ],
	  [ 'Schermo OLED 6,3″ a 120 Hz', 'Tripla fotocamera da 50 MP con zoom 5×', 'Sette anni di aggiornamenti', 'Batteria da 4.700 mAh, ricarica a 45 W' ],
	  'Il telefono che fa le foto migliori della sua fascia, anche di notte. Sbloccato, con doppia SIM.' ],
	[ 'FUL-010', 'Pixa 9a, smartphone 128 GB', 'pixa-9a', 'smartphone-tablet', 'Pixa', 449, 0, 61, 4.4, 2310, false,
	  [ 'pexels-8490071.jpg', 'pexels-8148644.jpg' ],
	  [ 'Schermo OLED 6,1″', 'Fotocamera da 48 MP', 'Due giorni di batteria', 'Resistente all’acqua IP67' ],
	  'Tutto quello che serve, niente di quello che non serve.' ],
	[ 'FUL-011', 'Pixa Tab 11, tablet 128 GB con penna', 'pixa-tab-11', 'smartphone-tablet', 'Pixa', 529, 479, 12, 4.5, 744, false,
	  [ 'pexels-8534235.jpg', 'pexels-6913315.jpg', 'pexels-11391944.jpg' ],
	  [ 'Schermo 11″ a 120 Hz', 'Penna inclusa, magnetica', 'Tastiera opzionale', '12 ore di video' ],
	  'Per leggere, disegnare e prendere appunti: la penna si attacca di lato e si ricarica da sola.' ],
	[ 'FUL-012', 'Monte Air 14, portatile ultraleggero 16 GB / 512 GB', 'monte-air-14', 'computer', 'Monte', 1299, 1099, 14, 4.7, 1530, true,
	  [ 'pexels-8533587.jpg', 'pexels-8534377.jpg', 'pexels-8534153.jpg' ],
	  [ 'Peso 1,1 kg, scocca in alluminio', 'Schermo 14″ 2,8K', '18 ore di batteria', 'Senza ventola: silenzioso' ],
	  'Il portatile che porti ovunque e ti dimentichi di caricare.' ],
	[ 'FUL-013', 'Monte Pro 16, portatile per creativi 32 GB / 1 TB', 'monte-pro-16', 'computer', 'Monte', 2199, 0, 6, 4.8, 402, false,
	  [ 'pexels-12882908.jpg', 'pexels-12882905.jpg' ],
	  [ 'Schermo 16″ mini-LED 1.600 nit', 'Scheda grafica dedicata', '32 GB di memoria, SSD da 1 TB', 'Thunderbolt, HDMI e lettore SD' ],
	  'Per montare in 8K, fare rendering e tenere aperte cento schede senza un sospiro.' ],
	[ 'FUL-014', 'Arcus View 27, monitor 4K IPS', 'arcus-view-27', 'computer', 'Arcus', 399, 329, 21, 4.5, 866, false,
	  [ 'pexels-18024473.jpg', 'pexels-3042508.jpg' ],
	  [ '27″ 4K, 98% DCI-P3', 'USB-C con ricarica a 90 W', 'Altezza e rotazione regolabili', 'Calibrato in fabbrica' ],
	  'Un cavo solo per immagine, rete e ricarica del portatile.' ],
	[ 'FUL-015', 'Arcus Wide 34, monitor curvo ultrawide', 'arcus-wide-34', 'computer', 'Arcus', 549, 0, 8, 4.6, 390, false,
	  [ 'pexels-7899231.jpg', 'pexels-8353774.jpg', 'pexels-19554899.jpg' ],
	  [ '34″ 3.440 × 1.440, 144 Hz', 'Curvatura 1.800R', 'KVM: due computer, una tastiera', 'Supporto a braccio incluso' ],
	  'Due monitor in uno, senza la riga nera in mezzo.' ],
	[ 'FUL-016', 'Volta K75, tastiera meccanica wireless', 'volta-k75', 'computer', 'Volta', 129, 99, 40, 4.6, 1784, false,
	  [ 'pexels-5152261.jpg', 'pexels-3829230.jpg' ],
	  [ 'Formato 75%, tasti hot-swap', 'Bluetooth, 2,4 GHz e cavo', 'Retroilluminazione RGB', 'Guarnizioni per un suono ovattato' ],
	  'Scrive come una macchina da scrivere silenziosa. Si collega a tre dispositivi.' ],
	[ 'FUL-017', 'Volta Arena, kit tastiera e mouse gaming RGB', 'volta-arena', 'gaming', 'Volta', 89, 0, 55, 4.3, 2290, false,
	  [ 'pexels-28779689.jpg', 'pexels-9072292.jpg' ],
	  [ 'Tastiera full-size anti-ghosting', 'Mouse da 16.000 DPI', 'Luci sincronizzate', 'Tappetino XL incluso' ],
	  'Il kit per cominciare: tutto in una scatola, luci comprese.' ],
	[ 'FUL-018', 'Kiri Watch 3, smartwatch con GPS', 'kiri-watch-3', 'indossabili', 'Kiri', 249, 199, 26, 4.3, 1930, true,
	  [ 'pexels-13007642.jpg', 'pexels-30385640.jpg' ],
	  [ 'GPS e cardiofrequenzimetro', 'Pagamenti contactless', '7 giorni di batteria', 'Impermeabile 5 ATM' ],
	  'Conta i passi, i battiti e le vasche. Ti avvisa delle chiamate senza tirare fuori il telefono.' ],
	[ 'FUL-019', 'Kiri Watch Sport, smartwatch da corsa', 'kiri-watch-sport', 'indossabili', 'Kiri', 179, 0, 4, 4.4, 655, false,
	  [ 'pexels-3646165.jpg', 'pexels-11322934.jpg' ],
	  [ 'GPS multibanda', 'Allenamenti guidati', '14 giorni di batteria', 'Cinturino traspirante' ],
	  'Per chi corre: il ritmo giusto, al chilometro.' ],
	[ 'FUL-020', 'Lumo X-T, fotocamera mirrorless con 18-55 mm', 'lumo-x-t', 'foto-droni', 'Lumo', 1199, 999, 7, 4.8, 512, true,
	  [ 'pexels-39342.jpg', 'pexels-12630464.jpg' ],
	  [ 'Sensore APS-C da 26 MP', 'Video 4K a 60 fps', 'Stabilizzatore sul sensore', 'Simulazioni pellicola' ],
	  'Piccola, bianca, con le ghiere vere: fa venir voglia di uscire a fotografare.' ],
	[ 'FUL-021', 'Lumo Zf, mirrorless full frame solo corpo', 'lumo-zf', 'foto-droni', 'Lumo', 1899, 0, 3, 4.9, 288, false,
	  [ 'pexels-19969447.jpg', 'pexels-19969462.jpg', 'pexels-17345669.jpg' ],
	  [ 'Full frame 24 MP', 'Autofocus che segue occhi e animali', 'Doppio slot SD', 'Corpo in lega di magnesio' ],
	  'Il corpo classico con il cuore di oggi.' ],
	[ 'FUL-022', 'Ala Mini 4, drone pieghevole 4K sotto i 250 g', 'ala-mini-4', 'foto-droni', 'Ala', 699, 599, 11, 4.7, 1460, true,
	  [ 'pexels-3945684.jpg', 'pexels-3945687.jpg', 'pexels-28861949.jpg' ],
	  [ 'Meno di 249 g: niente patentino A2', 'Video 4K HDR', '34 minuti di volo', 'Sensori anti-ostacolo' ],
	  'Sta in una tasca della giacca e torna da solo quando la batteria è bassa.' ],
	[ 'FUL-023', 'Pad wireless per console e PC', 'pad-wireless', 'gaming', 'Orbita', 69.90, 54.90, 80, 4.6, 6120, false,
	  [ 'pexels-18910365.jpg', 'pexels-1367000.jpg', 'pexels-7870815.jpg' ],
	  [ 'Vibrazione e grilletti adattivi', '40 ore di batteria', 'Base di ricarica inclusa', 'Funziona con console, PC e telefono' ],
	  'Il controller che regge le serate lunghe.' ],
	[ 'FUL-024', 'Orbita 2, visore di realtà virtuale 128 GB', 'orbita-2', 'gaming', 'Orbita', 449, 0, 13, 4.2, 974, false,
	  [ 'pexels-7864434.jpg', 'pexels-3761162.jpg', 'pexels-7561900.jpg' ],
	  [ 'Senza fili, senza computer', 'Realtà mista a colori', 'Controller con feedback', 'Oltre 500 giochi e app' ],
	  'Ti metti il visore e il salotto diventa una pista da sci, un ring o un museo.' ],
	[ 'FUL-025', 'Kiri Robo S8, robot aspirapolvere e lavapavimenti', 'kiri-robo-s8', 'casa-smart', 'Kiri', 499, 349, 19, 4.4, 3340, true,
	  [ 'pexels-35147280.jpg', 'pexels-35147242.jpg', 'pexels-4107249.jpg' ],
	  [ 'Aspira e lava in un passaggio', 'Mappa laser della casa', 'Base che si svuota da sola', 'Evita cavi e calzini' ],
	  'Pulisce mentre sei fuori, torna alla base e ti manda la mappa delle stanze fatte.' ],
	[ 'FUL-026', 'Arcus Vista 55, TV OLED 4K', 'arcus-vista-55', 'tv', 'Arcus', 1499, 1199, 5, 4.7, 820, true,
	  [ 'pexels-7587783.jpg', 'pexels-6782360.jpg', 'pexels-7546557.jpg' ],
	  [ 'OLED 55″ 4K a 120 Hz', 'Dolby Vision e Atmos', 'Quattro HDMI 2.1', 'Profilo da 2,4 cm' ],
	  'I neri veri, per i film la sera e le partite la domenica.' ],
	[ 'FUL-027', 'Set due pad wireless, bianco e nero', 'set-due-pad', 'gaming', 'Orbita', 129, 109, 30, 4.5, 410, false,
	  [ 'pexels-3945657.jpg', 'pexels-5208827.jpg' ],
	  [ 'Due controller', 'Un caricatore doppio', 'Ideale per giocare in due', 'Garanzia 2 anni' ],
	  'Per le sfide sul divano.' ],
];

$NOMI = [ 'Giulia R.', 'Marco T.', 'Sara B.', 'Luca F.', 'Elena M.', 'Davide C.', 'Chiara P.', 'Paolo G.', 'Francesca L.', 'Andrea S.', 'Marta V.', 'Riccardo N.' ];
$TESTI = [
	[ 5, 'Arrivato il giorno dopo, imballato bene. Fa esattamente quello che promette.' ],
	[ 5, 'Qualità sopra le aspettative per il prezzo. Lo ricomprerei.' ],
	[ 4, 'Ottimo, unico neo le istruzioni un po’ scarne. Per il resto nessun problema.' ],
	[ 5, 'Lo uso tutti i giorni da un mese: batteria che dura davvero.' ],
	[ 4, 'Buono, la confezione era un po’ ammaccata ma il prodotto perfetto.' ],
	[ 5, 'Configurato in cinque minuti col telefono. Consigliato.' ],
	[ 3, 'Fa il suo lavoro, ma mi aspettavo qualcosa in più dall’app.' ],
	[ 5, 'Regalo per mio padre, contentissimo. Assistenza gentile al telefono.' ],
];

$fatti = 0;
foreach ( $P as $i => $d ) {
	list( $sku, $nome, $slug, $cat, $marca, $prezzo, $sconto, $scorta, $voto, $n_rec, $evid, $foto, $punti, $testo ) = $d;
	$id = wc_get_product_id_by_sku( $sku );
	$p  = $id ? wc_get_product( $id ) : new WC_Product_Simple();
	$p->set_name( $nome );
	$p->set_slug( $slug );
	$p->set_sku( $sku );
	$p->set_status( 'publish' );
	$p->set_catalog_visibility( 'visible' );
	$p->set_regular_price( (string) $prezzo );
	$p->set_sale_price( $sconto ? (string) $sconto : '' );
	$p->set_manage_stock( true );
	$p->set_stock_quantity( $scorta );
	$p->set_low_stock_amount( 5 );
	$p->set_featured( $evid );
	$p->set_category_ids( [ $cat_id[ $cat ] ] );
	$p->set_short_description( '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', $punti ) ) . '</li></ul>' );
	$p->set_description( '<p>' . esc_html( $testo ) . '</p><h3>In breve</h3><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $punti ) )
		. '</li></ul><h3>Nella scatola</h3><p>Il prodotto, il cavo di ricarica USB-C, la guida rapida. Garanzia italiana di 2 anni, reso gratuito entro 30 giorni.</p>' );
	$p->set_weight( '' );
	$p->set_reviews_allowed( true );
	$ids = array_values( array_filter( array_map( function ( $f ) use ( $nome ) { return fulmine_foto( $f, $nome ); }, $foto ) ) );
	if ( $ids ) {
		$p->set_image_id( $ids[0] );
		$p->set_gallery_image_ids( array_slice( $ids, 1 ) );
	}
	$t = term_exists( $marca, 'pa_marca' );
	if ( ! $t ) {
		$t = wp_insert_term( $marca, 'pa_marca' );
	}
	$a = new WC_Product_Attribute();
	$a->set_id( wc_attribute_taxonomy_id_by_name( 'marca' ) );
	$a->set_name( 'pa_marca' );
	$a->set_options( [ (int) $t['term_id'] ] );
	$a->set_visible( true );
	$a->set_variation( false );
	$p->set_attributes( [ $a ] );
	$pid = $p->save();
	wp_set_object_terms( $pid, [ (int) $t['term_id'] ], 'pa_marca' );
	update_post_meta( $pid, '_olo_demo', $TEMA );

	// Tre recensioni vere, poi i numeri del negozio (le recensioni scritte ricalcolano media e conteggi).
	if ( ! get_comments( [ 'post_id' => $pid, 'count' => true ] ) ) {
		for ( $k = 0; $k < 3; $k++ ) {
			$r = $TESTI[ ( $i * 3 + $k ) % count( $TESTI ) ];
			$c = wp_insert_comment( [ 'comment_post_ID' => $pid, 'comment_author' => $NOMI[ ( $i + $k * 5 ) % count( $NOMI ) ],
				'comment_author_email' => 'cliente' . ( $i * 3 + $k ) . '@fulmine.example', 'comment_content' => $r[1],
				'comment_type' => 'review', 'comment_approved' => 1, 'comment_date' => gmdate( 'Y-m-d H:i:s', time() - ( $i * 3 + $k + 1 ) * 86400 * 4 ) ] );
			update_comment_meta( $c, 'rating', $r[0] );
			update_comment_meta( $c, 'verified', 1 );
		}
	}
	$p = wc_get_product( $pid );
	$cinque = (int) round( $n_rec * max( 0.35, min( 0.85, ( $voto - 3.6 ) / 1.4 ) ) );
	$resto  = $n_rec - $cinque;
	$conte  = [ 5 => $cinque, 4 => (int) round( $resto * 0.6 ), 3 => (int) round( $resto * 0.22 ), 2 => (int) round( $resto * 0.1 ) ];
	$conte[1] = max( 0, $n_rec - array_sum( $conte ) );
	$p->set_rating_counts( $conte );
	$p->set_average_rating( $voto );
	$p->set_review_count( $n_rec );
	$p->set_total_sales( (int) round( $n_rec * 2.7 ) );
	$p->save();
	$fatti++;
}

// ── Versione superiore e accessori ───────────────────────────────────────────
// Upsell (la tile «Ti potrebbe interessare» della scheda) e cross-sell (gli accessori: «Spesso comprati insieme»
// nella scheda e «Ti potrebbero servire anche» nel carrello). sku → [ upsell ], [ cross-sell ]
$LEGAMI = [
	'FUL-001' => [ [ 'FUL-003' ], [ 'FUL-005', 'FUL-018' ] ], 'FUL-002' => [ [ 'FUL-001' ], [ 'FUL-005', 'FUL-007' ] ],
	'FUL-003' => [ [], [ 'FUL-005', 'FUL-012' ] ],            'FUL-004' => [ [ 'FUL-003' ], [ 'FUL-016', 'FUL-014' ] ],
	'FUL-005' => [ [ 'FUL-006' ], [ 'FUL-001', 'FUL-018' ] ], 'FUL-006' => [ [], [ 'FUL-007', 'FUL-008' ] ],
	'FUL-007' => [ [ 'FUL-008' ], [ 'FUL-025', 'FUL-006' ] ], 'FUL-008' => [ [], [ 'FUL-007', 'FUL-025' ] ],
	'FUL-009' => [ [], [ 'FUL-001', 'FUL-018' ] ],            'FUL-010' => [ [ 'FUL-009' ], [ 'FUL-002', 'FUL-019' ] ],
	'FUL-011' => [ [ 'FUL-012' ], [ 'FUL-016', 'FUL-003' ] ], 'FUL-012' => [ [ 'FUL-013' ], [ 'FUL-014', 'FUL-016' ] ],
	'FUL-013' => [ [], [ 'FUL-015', 'FUL-016' ] ],            'FUL-014' => [ [ 'FUL-015' ], [ 'FUL-016', 'FUL-012' ] ],
	'FUL-015' => [ [], [ 'FUL-016', 'FUL-017' ] ],            'FUL-016' => [ [], [ 'FUL-014', 'FUL-017' ] ],
	'FUL-017' => [ [ 'FUL-016' ], [ 'FUL-023', 'FUL-015' ] ], 'FUL-018' => [ [], [ 'FUL-001', 'FUL-009' ] ],
	'FUL-019' => [ [ 'FUL-018' ], [ 'FUL-002', 'FUL-010' ] ], 'FUL-020' => [ [ 'FUL-021' ], [ 'FUL-022', 'FUL-012' ] ],
	'FUL-021' => [ [], [ 'FUL-022', 'FUL-013' ] ],            'FUL-022' => [ [], [ 'FUL-020', 'FUL-011' ] ],
	'FUL-023' => [ [ 'FUL-027' ], [ 'FUL-024', 'FUL-017' ] ], 'FUL-024' => [ [], [ 'FUL-023', 'FUL-003' ] ],
	'FUL-025' => [ [], [ 'FUL-007', 'FUL-008' ] ],            'FUL-026' => [ [], [ 'FUL-006', 'FUL-024' ] ],
	'FUL-027' => [ [], [ 'FUL-024', 'FUL-017' ] ],
];
$da_sku = static function ( $skus ) {
	return array_values( array_filter( array_map( 'wc_get_product_id_by_sku', $skus ) ) );
};
foreach ( $LEGAMI as $sku => $l ) {
	$p = wc_get_product( wc_get_product_id_by_sku( $sku ) );
	if ( $p ) {
		$p->set_upsell_ids( $da_sku( $l[0] ) );
		$p->set_cross_sell_ids( $da_sku( $l[1] ) );
		$p->save();
	}
}
WP_CLI::success( $fatti . ' prodotti · ' . count( $cat_id ) . ' categorie' );
