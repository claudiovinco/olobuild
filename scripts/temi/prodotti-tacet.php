<?php
/**
 * Tacet (demo di OLObuild sul sottosito): le opere della galleria come prodotti di WooCommerce.
 * Si rilancia senza danni: le opere si ritrovano dallo SKU, le foto dal nome del file d'origine.
 * Ogni opera, categoria e foto porta _olo_demo = tacet (la demo mostra solo le sue: Olobuild_Demo_Temi).
 * Campi delle opere letti dalle tile: anno, riga_opera (anno · materiale), viaggio (il riassunto del pedigree),
 * scheda_tecnica e pedigree (una voce per riga, parti divise da «|»: «Righe di un campo» della Sorgente dinamica).
 * Peso e misure anche nei campi di WooCommerce. Le opere senza prezzo non si comprano: si chiede la visione.
 * uso: wp eval-file prodotti-tacet.php
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce non è attivo' );
}
define( 'TAC_LIB', ABSPATH . '../olobuild-library/media/' );
define( 'TAC_TEMA', 'tacet' );

function tacet_foto( $file, $alt ) {
	$gia = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids',
		'meta_key' => '_olo_demo_src', 'meta_value' => 'tacet:' . $file ] );
	if ( $gia ) {
		return (int) $gia[0];
	}
	if ( ! is_readable( TAC_LIB . $file ) ) {
		WP_CLI::warning( 'manca ' . $file );
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $file );
	copy( TAC_LIB . $file, $tmp );
	$id = media_handle_sideload( [ 'name' => 'tacet-' . $file, 'tmp_name' => $tmp ], 0, $alt );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( $file . ': ' . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_olo_demo_src', 'tacet:' . $file );
	update_post_meta( $id, '_olo_demo', TAC_TEMA );
	return (int) $id;
}

// ── I materiali (categorie) ──────────────────────────────────────────────────
$CAT = [
	'marmo'      => [ 'Marmo', 'Statuario, Lasa, Marquinia. Il bianco che ricorda.', 'pexels-7828709.jpg' ],
	'pietra'     => [ 'Pietra', 'Pietra serena, basalto, lava. Il grigio che resta.', 'pexels-10431040.jpg' ],
	'gesso'      => [ 'Gesso', 'Calchi, prove, originali. Il bianco che si rompe.', 'pexels-5682147.jpg' ],
	'bronzo'     => [ 'Bronzo', 'Cera persa, patine a fuoco.', 'pexels-35279463.jpg' ],
	'ceramica'   => [ 'Ceramica', 'Gres e porcellana, cotture a legna.', 'pexels-9733007.jpg' ],
	'terracotta' => [ 'Terracotta', 'Terra rossa, ingobbi, mani.', 'pexels-11016739.jpg' ],
	'vetro-metallo' => [ 'Vetro e metallo', 'Acciaio a specchio, vetro soffiato.', 'pexels-29833481.jpg' ],
	'legno'      => [ 'Legno', 'Cirmolo, noce, quercia di fiume.', 'pexels-37258072.jpg' ],
];
$cat_id = [];
foreach ( $CAT as $slug => $c ) {
	$t  = get_term_by( 'slug', $slug, 'product_cat' );
	$id = $t ? (int) $t->term_id : (int) wp_insert_term( $c[0], 'product_cat', [ 'slug' => $slug, 'description' => $c[1] ] )['term_id'];
	wp_update_term( $id, 'product_cat', [ 'name' => $c[0], 'description' => $c[1] ] );
	update_term_meta( $id, 'thumbnail_id', tacet_foto( $c[2], $c[0] ) );
	update_term_meta( $id, '_olo_demo', TAC_TEMA );
	$cat_id[ $slug ] = $id;
}

// ── L'artista, attributo globale (filtrabile, sopra il titolo delle card) ────
if ( ! wc_attribute_taxonomy_id_by_name( 'artista' ) ) {
	wc_create_attribute( [ 'name' => 'Artista', 'slug' => 'artista', 'type' => 'select', 'order_by' => 'name', 'has_archives' => false ] );
}
if ( ! taxonomy_exists( 'pa_artista' ) ) {
	register_taxonomy( 'pa_artista', 'product', [ 'hierarchical' => false, 'show_ui' => false, 'query_var' => true, 'rewrite' => false ] );
}

// ── Le opere ─────────────────────────────────────────────────────────────────
// sku, titolo, slug, materiale (categoria), artista, anno, prezzo (0 = riservato), in evidenza, foto[],
// materiale per esteso, esemplare, firma, [altezza, larghezza, profondità] cm, peso kg,
// appoggio, fissaggio, luce, ambiente, [cassa a×l×p cm, peso cassa kg], movimentazione,
// breve, nota, pedigree [ [anno, città, evento] … ]
$O = [
	[ 'TCT-001', 'Equilibrio, I', 'equilibrio-i', 'marmo', 'E.R.', 2021, 0, true,
	  [ 'pexels-7828709.jpg', 'pexels-7828701.jpg', 'pexels-7828706.jpg' ],
	  'Marmo statuario di Carrara, basalto, acciaio inox', 'Unico', 'Incisa sotto la base: «E.R. 21»', [ 58, 74, 22 ], 96,
	  'Piano orizzontale di almeno 80 × 30 cm', 'Perno inox nella base (fornito)', 'Radente da sinistra, 2700 K', 'Solo interno',
	  [ 92, 52, 46, 148 ], 'Due persone e un carrello a ruote piene',
	  'Un blocco di statuario con la sua vena d’oro, una sfera di basalto che non poggia dove dovrebbe, un’asta che tiene tutto. Ventidue giorni di lucidatura a mano.',
	  'La sfera non è incollata: sta dove l’asta la lascia. Spostarla di un centimetro cambia il peso di tutta la stanza. E.R. dice che l’opera è finita quando nessuno ha più voglia di toccarla.',
	  [ [ 2021, 'Pietrasanta', 'Prima uscita, «Bianco su nero», Galleria Tacet' ], [ 2022, 'Basilea', '«Peso specifico», Kunstraum Nebel' ],
	    [ 2023, 'Ginevra', 'In prestito alla Fondazione Lanterna' ], [ 2025, 'Seul', '«Stone Silence», Hall Nove' ],
	    [ 2026, 'Pietrasanta', 'Rientrata in galleria. Visibile su appuntamento' ] ] ],
	[ 'TCT-002', 'Equilibrio, notturno', 'equilibrio-notturno', 'marmo', 'E.R.', 2022, 38000, true,
	  [ 'pexels-7828698.jpg', 'pexels-7828700.jpg' ],
	  'Marmo statuario, basalto nero, acciaio brunito', 'Unico', 'Incisa sotto la base: «E.R. 22 n»', [ 52, 60, 20 ], 81,
	  'Piano orizzontale di almeno 70 × 30 cm', 'Perno inox nella base (fornito)', 'Un solo punto luce dall’alto, 2400 K', 'Solo interno',
	  [ 84, 46, 44, 126 ], 'Due persone',
	  'La sorella di «Equilibrio, I», pensata per il buio: la vena d’oro sparisce, resta la crepa.',
	  'Fotografata solo di notte, per volere dell’artista. Di giorno è un’altra opera.',
	  [ [ 2022, 'Pietrasanta', '«Notturni», Galleria Tacet' ], [ 2023, 'Torino', '«Sala delle Ombre»' ], [ 2024, 'Rotterdam', 'Fiera d’arte contemporanea, stand 14' ] ] ],
	[ 'TCT-003', 'Undici', 'undici', 'marmo', 'Halvik', 2019, 0, true,
	  [ 'pexels-15535441.jpg' ],
	  'Undici pietre di marmo bianco, perno centrale in acciaio', 'Unico', 'Nessuna: numero «11» inciso sulla pietra più bassa', [ 168, 44, 44 ], 214,
	  'Pavimento in cemento o pietra; sul parquet serve una piastra di ripartizione', 'Perno passante in acciaio, montaggio in loco', 'Diffusa, dall’alto', 'Interno o chiostro coperto',
	  [ 120, 70, 60, 290 ], 'Tre persone; le pietre viaggiano separate e numerate',
	  'Undici pietre raccolte in undici anni sulla stessa spiaggia, tagliate e impilate in ordine di arrivo.',
	  'Halvik non dice quale spiaggia. Sa solo che la dodicesima pietra non è ancora arrivata.',
	  [ [ 2019, 'Tórshavn', 'Mostrata una sola sera, in un capannone del porto' ], [ 2020, 'Copenaghen', 'Collezione privata' ],
	    [ 2022, 'Basilea', '«Peso specifico», Kunstraum Nebel' ], [ 2024, 'Venezia', 'Padiglione temporaneo, giardino privato' ],
	    [ 2025, 'Ginevra', 'Fondazione Lanterna, sala grande' ], [ 2026, 'Pietrasanta', 'Galleria Tacet, su appuntamento' ] ] ],
	[ 'TCT-004', 'Anello', 'anello', 'ceramica', 'I.M.', 2020, 6800, false,
	  [ 'pexels-39363466.jpg' ],
	  'Gres bianco, smalto opaco, base in noce nazionale', 'Unico', 'A matita, sotto la base: «I.M. 20»', [ 46, 40, 12 ], 14,
	  'Mensola o tavolo, profondità almeno 20 cm', 'Nessuno', 'Controluce morbido', 'Solo interno',
	  [ 62, 56, 30, 22 ], 'Una persona',
	  'Un anello di gres alto mezzo metro, con un vuoto al centro che non è rotondo. Cotto due volte.',
	  'La prima cottura l’ha crepato; I.M. ha tenuto la crepa e l’ha chiusa con un secondo fuoco.',
	  [ [ 2020, 'Cagliari', 'Studio aperto, una settimana' ], [ 2023, 'Milano', '«Spazio Grigio»' ] ] ],
	[ 'TCT-005', 'Due, che si guardano', 'due-che-si-guardano', 'marmo', 'I.M.', 2023, 14500, true,
	  [ 'pexels-36382214.jpg' ],
	  'Marmo bianco di Lasa', 'Unico', 'Incisa sul retro: «IM»', [ 52, 30, 24 ], 38,
	  'Piano stabile, almeno 40 × 30 cm', 'Feltrini sotto la base (forniti)', 'Laterale, 3000 K', 'Solo interno',
	  [ 70, 46, 42, 64 ], 'Due persone',
	  'Due profili ricavati dallo stesso blocco, che non arrivano mai a toccarsi. Lasa, che di sera diventa rosa.',
	  'Ordinata da un collezionista che l’ha rifiutata alla consegna: troppo vicini, disse. Da allora è rimasta in galleria.',
	  [ [ 2023, 'Pietrasanta', 'Galleria Tacet' ], [ 2024, 'Londra', 'Asta di arte contemporanea, lotto ritirato' ], [ 2025, 'Milano', '«Spazio Grigio»' ] ] ],
	[ 'TCT-006', 'Busto con crepe d’acqua', 'busto-con-crepe-dacqua', 'ceramica', 'Nove Lune', 2022, 9200, false,
	  [ 'pexels-9733007.jpg' ],
	  'Porcellana, smalto celadon, cottura a legna', 'Unico', 'Timbro a secco «NL» sulla spalla sinistra', [ 61, 28, 22 ], 19,
	  'Piedistallo alto 100–110 cm', 'Nastro biadesivo museale (fornito)', 'Frontale e diffusa', 'Solo interno',
	  [ 80, 44, 40, 34 ], 'Una persona',
	  'Il celadon si è aperto in cottura come la superficie di un lago che gela. Nessuna crepa è dipinta.',
	  'Nove Lune ha fatto undici busti; ne ha tenuti tre. Questo è il secondo.',
	  [ [ 2022, 'Faenza', 'Rassegna di ceramica contemporanea' ], [ 2024, 'Kyoto', 'Galleria privata, sala del tè' ] ] ],
	[ 'TCT-007', 'Nicchia', 'nicchia', 'bronzo', 'Nove Lune', 2021, 11000, false,
	  [ 'pexels-20515535.jpg' ],
	  'Bronzo a cera persa, patina a fuoco; nicchia in gesso', 'Unico', 'Punzone «NL» sul bronzo', [ 40, 30, 18 ], 12,
	  'A parete: due tasselli da 8 mm', 'Staffa nascosta (fornita)', 'Dall’alto, stretta', 'Solo interno',
	  [ 56, 46, 34, 24 ], 'Una persona',
	  'Un piccolo bronzo che vive nella sua nicchia di gesso, appesa alla parete come una finestra.',
	  'Il bronzo si può togliere. Nove Lune chiede di non farlo.',
	  [ [ 2021, 'Pietrasanta', 'Galleria Tacet' ], [ 2023, 'Torino', '«Sala delle Ombre»' ] ] ],
	[ 'TCT-008', 'Le mani di prima', 'le-mani-di-prima', 'bronzo', 'il Muto', 1987, 0, true,
	  [ 'pexels-35279463.jpg' ],
	  'Bronzo a cera persa, patina bruna', 'Unico', 'Punzone della fonderia e «A.V. 87»', [ 34, 28, 26 ], 22,
	  'Piano stabile o piedistallo', 'Nessuno', 'Radente, calda', 'Interno',
	  [ 50, 44, 42, 38 ], 'Una persona',
	  'Due mani che tengono qualcosa che non si vede. Fuse nella fonderia dove l’artista ha lavorato cinquant’anni.',
	  'Dall’archivio del Muto, che non ha mai venduto un’opera in vita. Esce dalla galleria solo con una lettera di chi la riceve.',
	  [ [ 1987, 'Pietrasanta', 'Fonderia, mai esposta' ], [ 2019, 'Pietrasanta', 'Archivio dell’artista' ], [ 2021, 'Lucca', 'Mostra postuma, chiesa sconsacrata' ],
	    [ 2023, 'Parigi', 'Studio privato, in prestito' ], [ 2026, 'Pietrasanta', 'Galleria Tacet' ] ] ],
	[ 'TCT-009', 'Sezione, n. 4', 'sezione-n-4', 'marmo', 'K.O.', 2024, 18000, true,
	  [ 'pexels-35805444.jpg' ],
	  'Resina e polvere di marmo, base in travertino', 'Esemplare 1 di 3', 'Placca d’acciaio sotto la base', [ 48, 22, 26 ], 17,
	  'Piedistallo o mensola profonda', 'Nessuno', 'Controluce caldo, per vedere le sezioni', 'Solo interno',
	  [ 64, 40, 40, 30 ], 'Una persona',
	  'Una testa tagliata in quaranta fette e ricomposta con uno scarto di due millimetri. Da lontano è intera.',
	  'K.O. dice che è un ritratto. Non dice di chi.',
	  [ [ 2024, 'Kanazawa', 'Studio dell’artista' ], [ 2025, 'Seul', '«Stone Silence», Hall Nove' ], [ 2026, 'Pietrasanta', 'Galleria Tacet' ] ] ],
	[ 'TCT-010', 'Specchio cieco', 'specchio-cieco', 'vetro-metallo', 'K.O.', 2020, 0, false,
	  [ 'pexels-29833481.jpg' ],
	  'Acciaio inox lucidato a specchio, vetro', 'Unico', 'Incisa a laser sotto la base', [ 70, 40, 40 ], 31,
	  'Piano stabile, lontano da finestre a sud', 'Tre piedini regolabili', 'Diffusa: una luce puntata la fa sparire', 'Solo interno',
	  [ 90, 56, 56, 58 ], 'Due persone, con guanti',
	  'Una forma che riflette tutto tranne chi la guarda. Lucidata per tre mesi.',
	  'Si pulisce solo con acqua. Ogni impronta resta.',
	  [ [ 2020, 'Tokyo', 'Spazio indipendente, una notte' ], [ 2022, 'Rotterdam', 'Fiera d’arte contemporanea' ], [ 2024, 'Ginevra', 'Fondazione Lanterna' ] ] ],
	[ 'TCT-011', 'Busto per una stanza vuota', 'busto-per-una-stanza-vuota', 'gesso', 'Halvik', 2016, 7400, false,
	  [ 'pexels-28845599.jpg' ],
	  'Gesso alabastrino patinato, ferro', 'Unico', 'Numero «7» sul retro', [ 64, 42, 30 ], 21,
	  'Piedistallo alto almeno 90 cm', 'Nessuno', 'Una finestra, se c’è', 'Solo interno',
	  [ 84, 60, 48, 40 ], 'Due persone',
	  'Un busto fatto per stare da solo in una stanza. Halvik chiede che non abbia nulla accanto.',
	  'Il gesso è patinato con la cera e il fumo di una candela, strato su strato.',
	  [ [ 2016, 'Tórshavn', 'Casa dell’artista' ], [ 2018, 'Oslo', 'Mostra collettiva «Rom»' ], [ 2021, 'Pietrasanta', 'Galleria Tacet' ] ] ],
	[ 'TCT-012', 'Terra rossa', 'terra-rossa', 'terracotta', 'I.M.', 2019, 5900, false,
	  [ 'pexels-11016739.jpg' ],
	  'Terracotta, ingobbio, cera', 'Unico', 'Impronta del pollice sotto la base', [ 38, 30, 24 ], 9,
	  'Mensola o tavolo', 'Nessuno', 'Bassa e calda', 'Interno',
	  [ 52, 44, 38, 16 ], 'Una persona',
	  'Una testa di terra rossa, modellata in un giorno e cotta in una buca nel terreno.',
	  'La cottura in buca non si controlla: il nero sulla guancia l’ha deciso il fuoco.',
	  [ [ 2019, 'Cagliari', 'Studio aperto' ], [ 2021, 'Napoli', 'Collezione privata' ] ] ],
	[ 'TCT-013', 'Il volto sotto', 'il-volto-sotto', 'pietra', 'E.R.', 2015, 16000, false,
	  [ 'pexels-10431040.jpg' ],
	  'Pietra serena', 'Unico', 'Incisa sul fianco: «E.R. 15»', [ 44, 52, 36 ], 64,
	  'Piano stabile o pavimento', 'Nessuno', 'Radente dall’alto', 'Interno o esterno coperto',
	  [ 66, 70, 56, 104 ], 'Due persone e un carrello',
	  'Un volto che affiora dalla pietra e non decide se uscire. Lasciato grezzo sul retro.',
	  'La prima opera di E.R. in pietra grigia, e l’ultima.',
	  [ [ 2015, 'Firenze', 'Mostra di diploma' ], [ 2017, 'Pietrasanta', 'Galleria Tacet, prima mostra' ], [ 2020, 'Basilea', 'Kunstraum Nebel' ],
	    [ 2023, 'Lisbona', 'Museo del Calco, sala 2' ] ] ],
	[ 'TCT-014', 'Calco, mano sinistra', 'calco-mano-sinistra', 'gesso', 'Halvik', 2014, 3900, false,
	  [ 'pexels-5682147.jpg' ],
	  'Gesso da formatore', 'Esemplare 2 di 5', 'Numero «2/5» al polso', [ 34, 14, 12 ], 3,
	  'Mensola', 'Nessuno', 'Laterale', 'Solo interno',
	  [ 46, 28, 24, 7 ], 'Una persona',
	  'Il calco della mano sinistra dell’artista, preso il giorno in cui ha smesso di usarla per scolpire.',
	  'Halvik scolpisce con la destra da allora. Non dice perché.',
	  [ [ 2014, 'Tórshavn', 'Studio' ], [ 2019, 'Copenaghen', 'Mostra «Hands»' ] ] ],
	[ 'TCT-015', 'Torso, stoffa', 'torso-stoffa', 'gesso', 'I.M.', 2021, 4800, false,
	  [ 'pexels-8496566.jpg' ],
	  'Gesso alabastrino, lino', 'Unico', 'A matita, sotto: «I.M. 21»', [ 40, 26, 18 ], 6,
	  'Tavolo; il lino fa parte dell’opera', 'Nessuno', 'Calda, laterale', 'Solo interno',
	  [ 56, 40, 32, 12 ], 'Una persona',
	  'Un torso senza testa né braccia, appoggiato su un lino che non va tolto.',
	  'Il lino era della nonna di I.M. Viaggia sempre con l’opera.',
	  [ [ 2021, 'Cagliari', 'Studio aperto' ], [ 2024, 'Milano', '«Spazio Grigio»' ] ] ],
	[ 'TCT-016', 'Cave', 'cave', 'ceramica', 'Nove Lune', 2018, 12500, false,
	  [ 'pexels-40547007.jpg' ],
	  'Gres porcellanato, 212 elementi su pannello', 'Unico', 'Sul retro del pannello', [ 120, 80, 9 ], 26,
	  'A parete: quattro tasselli da 8 mm', 'Binario nascosto (fornito)', 'Radente dall’alto: le cavità si riempiono d’ombra', 'Solo interno',
	  [ 136, 96, 22, 48 ], 'Due persone',
	  'Duecentododici cavità modellate a mano, una per ogni giorno di lavoro.',
	  'Il pannello si appende in un solo verso. Il retro lo dice.',
	  [ [ 2018, 'Faenza', 'Rassegna di ceramica contemporanea' ], [ 2020, 'Kyoto', 'Galleria privata' ], [ 2022, 'Pietrasanta', 'Galleria Tacet' ] ] ],
	[ 'TCT-017', 'Pesi', 'pesi', 'pietra', 'Halvik', 2020, 9600, false,
	  [ 'pexels-7828179.jpg', 'pexels-7827881.jpg' ],
	  'Basalto, travertino, granito, sfere in marmo', 'Unico', 'Nessuna', [ 36, 28, 28 ], 29,
	  'Piano orizzontale perfettamente in bolla', 'Nessuno: si regge da sé', 'Diffusa', 'Solo interno',
	  [ 50, 44, 44, 44 ], 'Una persona; gli elementi viaggiano separati',
	  'Cinque pietre impilate in un equilibrio che non usa colla né perni.',
	  'Si monta seguendo una sola fotografia, allegata al certificato.',
	  [ [ 2020, 'Copenaghen', 'Collezione privata' ], [ 2023, 'Pietrasanta', 'Galleria Tacet' ] ] ],
	[ 'TCT-018', 'Teste di cirmolo', 'teste-di-cirmolo', 'legno', 'il Muto', 1992, 0, false,
	  [ 'pexels-37258072.jpg' ],
	  'Cirmolo intagliato, cera d’api', 'Unico', 'Inciso: «A.V. 92»', [ 88, 30, 24 ], 18,
	  'Piedistallo o base a terra', 'Nessuno', 'Calda', 'Solo interno, lontano dai termosifoni',
	  [ 104, 46, 40, 36 ], 'Due persone',
	  'L’unico lavoro in legno del Muto: sette teste in un tronco solo, intagliate in un inverno.',
	  'Dall’archivio dell’artista. Esce dalla galleria solo con una lettera di chi la riceve.',
	  [ [ 1992, 'Pietrasanta', 'Laboratorio, mai esposta' ], [ 2021, 'Lucca', 'Mostra postuma' ], [ 2026, 'Pietrasanta', 'Galleria Tacet' ] ] ],
	[ 'TCT-019', 'Tre colonne a righe', 'tre-colonne-a-righe', 'ceramica', 'Nove Lune', 2023, 8400, false,
	  [ 'pexels-30654319.jpg' ],
	  'Gres smaltato, tre elementi', 'Unico', 'Timbro «NL» su ogni elemento', [ 74, 60, 24 ], 22,
	  'Piano lungo almeno 80 cm', 'Nessuno', 'Frontale', 'Solo interno',
	  [ 90, 76, 40, 40 ], 'Una persona',
	  'Tre colonne tornite e smaltate a righe, da disporre come si vuole. L’ordine lo sceglie chi le ospita.',
	  'Nove Lune ne fa una serie ogni nove lune.',
	  [ [ 2023, 'Faenza', 'Rassegna di ceramica contemporanea' ], [ 2025, 'Milano', '«Spazio Grigio»' ] ] ],
	[ 'TCT-020', 'Ritratto in gesso', 'ritratto-in-gesso', 'gesso', 'il Muto', 1974, 0, false,
	  [ 'pexels-20364955.jpg' ],
	  'Gesso patinato su base modanata', 'Unico (modello per un bronzo mai fuso)', 'A matita sotto la base: «A.V. 74»', [ 72, 48, 30 ], 26,
	  'Piedistallo alto almeno 100 cm', 'Nessuno', 'Laterale da sinistra', 'Solo interno',
	  [ 92, 66, 50, 54 ], 'Due persone',
	  'Il modello in gesso di un ritratto che doveva diventare bronzo. Il bronzo non è mai stato fuso.',
	  'Non si sa chi sia la persona ritratta. Il Muto, naturalmente, non l’ha detto.',
	  [ [ 1974, 'Pietrasanta', 'Laboratorio' ], [ 2021, 'Lucca', 'Mostra postuma' ], [ 2024, 'Parigi', 'Studio privato, in prestito' ],
	    [ 2026, 'Pietrasanta', 'Galleria Tacet' ] ] ],
];

$NUMERI = [ 1 => 'uno', 2 => 'due', 3 => 'tre', 4 => 'quattro', 5 => 'cinque', 6 => 'sei', 7 => 'sette', 8 => 'otto', 9 => 'nove', 10 => 'dieci' ];
$fatte  = 0;
foreach ( $O as $d ) {
	list( $sku, $titolo, $slug, $cat, $artista, $anno, $prezzo, $evid, $foto, $mat, $esempl, $firma, $dim, $peso,
		$appoggio, $fissaggio, $luce, $ambiente, $cassa, $movim, $breve, $nota, $ped ) = $d;
	$id = wc_get_product_id_by_sku( $sku );
	$p  = $id ? wc_get_product( $id ) : new WC_Product_Simple();
	$p->set_name( $titolo );
	$p->set_slug( $slug );
	$p->set_sku( $sku );
	$p->set_status( 'publish' );
	$p->set_catalog_visibility( 'visible' );
	$p->set_regular_price( $prezzo ? (string) $prezzo : '' );
	$p->set_sale_price( '' );
	$p->set_manage_stock( true );
	$p->set_stock_quantity( 1 );
	$p->set_sold_individually( true );
	$p->set_featured( $evid );
	$p->set_category_ids( [ $cat_id[ $cat ] ] );
	$p->set_short_description( '<p>' . esc_html( $breve ) . '</p>' );
	$p->set_description( '<p>' . esc_html( $nota ) . '</p>' );
	$p->set_weight( (string) $peso );
	$p->set_height( (string) $dim[0] );
	$p->set_width( (string) $dim[1] );
	$p->set_length( (string) $dim[2] );
	$p->set_reviews_allowed( false );
	$ids = array_values( array_filter( array_map( function ( $f ) use ( $titolo, $artista ) { return tacet_foto( $f, $titolo . ', ' . $artista ); }, $foto ) ) );
	if ( $ids ) {
		$p->set_image_id( $ids[0] );
		$p->set_gallery_image_ids( array_slice( $ids, 1 ) );
	}
	$t = term_exists( $artista, 'pa_artista' );
	if ( ! $t ) {
		$t = wp_insert_term( $artista, 'pa_artista' );
	}
	$a = new WC_Product_Attribute();
	$a->set_id( wc_attribute_taxonomy_id_by_name( 'artista' ) );
	$a->set_name( 'pa_artista' );
	$a->set_options( [ (int) $t['term_id'] ] );
	$a->set_visible( true );
	$a->set_variation( false );
	$p->set_attributes( [ $a ] );
	$pid = $p->save();
	wp_set_object_terms( $pid, [ (int) $t['term_id'] ], 'pa_artista' );
	update_post_meta( $pid, '_olo_demo', TAC_TEMA );

	// I campi che le tile della scheda leggono.
	$scheda = [
		'Opera | Materiale | ' . $mat,
		'Opera | Anno | ' . $anno,
		'Opera | Esemplare | ' . $esempl,
		'Opera | Firma | ' . $firma,
		'Misure | Altezza | ' . $dim[0] . ' | cm',
		'Misure | Larghezza | ' . $dim[1] . ' | cm',
		'Misure | Profondità | ' . $dim[2] . ' | cm',
		'Misure | Peso | ' . $peso . ' | kg',
		'Installazione | Appoggio | ' . $appoggio,
		'Installazione | Carico sul piano | ' . (int) ceil( $peso / max( 0.01, ( $dim[1] * $dim[2] ) / 10000 ) ) . ' | kg/m²',
		'Installazione | Fissaggio | ' . $fissaggio,
		'Installazione | Luce | ' . $luce,
		'Installazione | Ambiente | ' . $ambiente,
		'Trasporto | Cassa | ' . $cassa[0] . ' × ' . $cassa[1] . ' × ' . $cassa[2] . ' | cm',
		'Trasporto | Peso con la cassa | ' . $cassa[3] . ' | kg',
		'Trasporto | Movimentazione | ' . $movim,
		'Documenti | Certificato | Autenticità, firmato dall’artista e dalla galleria',
		'Documenti | Stato di conservazione | Rapporto fotografico, ottobre 2026',
	];
	update_post_meta( $pid, 'scheda_tecnica', implode( "\n", $scheda ) );
	update_post_meta( $pid, 'pedigree', implode( "\n", array_map( function ( $r ) { return implode( ' | ', $r ); }, $ped ) ) );
	update_post_meta( $pid, 'anno', (string) $anno );
	update_post_meta( $pid, 'riga_opera', $anno . ' · ' . $mat );
	$citta = count( array_unique( array_column( $ped, 1 ) ) );
	$anni  = 2026 - (int) $ped[0][0];
	$n     = function ( $x ) use ( $NUMERI ) { return $NUMERI[ $x ] ?? (string) $x; };
	update_post_meta( $pid, 'viaggio', $anni > 0
		? sprintf( 'In viaggio da %s %s, %s %s.', $n( $anni ), 1 === $anni ? 'anno' : 'anni', $n( $citta ), 1 === $citta ? 'città' : 'città' )
		: 'Appena uscita dallo studio.' );
	$fatte++;
}
WP_CLI::success( $fatte . ' opere · ' . count( $cat_id ) . ' materiali' );
