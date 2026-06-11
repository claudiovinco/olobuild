<?php
/**
 * Render di tutte le pagine dei 50 temi su file HTML, per diff pre/post campagna escaping.
 * Uso: php -d memory_limit=512M /usr/local/bin/wp eval-file /tmp/olo_esc_render.php <outdir> --allow-root --path=/var/www/wordpress
 */
$outdir = isset( $args[0] ) ? rtrim( $args[0], '/' ) : '/root/esc_baseline/A';
if ( ! is_dir( $outdir ) ) {
	mkdir( $outdir, 0755, true );
}
$themes_dir = '/var/www/wordpress/wp-content/plugins/olobuild/assets/data/themes';
$renderer   = new Olo_Frontend_Renderer();
$summary    = [];
$fatals     = 0;

foreach ( glob( $themes_dir . '/*', GLOB_ONLYDIR ) as $tdir ) {
	$slug = basename( $tdir );
	foreach ( glob( $tdir . '/*.json' ) as $jf ) {
		$page = basename( $jf, '.json' );
		if ( $page === 'theme' ) {
			continue;
		}
		$tiles = json_decode( file_get_contents( $jf ), true );
		if ( ! is_array( $tiles ) || empty( $tiles ) ) {
			continue;
		}
		$err = '';
		ob_start();
		try {
			$renderer->render_tiles_array( $tiles, [] );
			$html = ob_get_clean();
		} catch ( Throwable $e ) {
			ob_end_clean();
			$html = '';
			$err  = get_class( $e ) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine();
			$fatals++;
		}
		$key = $slug . '__' . $page;
		file_put_contents( $outdir . '/' . $key . '.html', $html );
		$summary[] = $key . "\t" . md5( $html ) . "\t" . strlen( $html ) . "\t" . $err;
	}
}
file_put_contents( $outdir . '/_summary.tsv', implode( "\n", $summary ) . "\n" );
echo 'DONE pages=' . count( $summary ) . ' fatals=' . $fatals . ' -> ' . $outdir . "\n";
