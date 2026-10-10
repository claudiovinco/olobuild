<?php
/**
 * Olobuild — pagina di una demo dei temi (Olobuild_Demo_Temi).
 *
 * Pagina intera, come il modello della 404, ma con lo scheletro di una pagina vera di hello-olobuild
 * (.wp-site-blocks > header + main > .entry-content + footer): le regole che il plugin scrive per
 * testata e contenuto valgono come sul sito. Testata, contenuto e piè di pagina si disegnano PRIMA di
 * wp_head(), come fanno i temi a blocchi: i CSS che le tile accodano mentre si disegnano escono nella
 * <head>, non in fondo alla pagina.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$olobuild_demo_testata = Olobuild_Demo_Temi::id_zona( 'header' );
$olobuild_demo_piede   = Olobuild_Demo_Temi::id_zona( 'footer' );
$olobuild_demo_html    = '';
if ( $olobuild_demo_testata ) {
    $olobuild_demo_html .= ( new Olobuild_Header_Integration() )->render_header( $olobuild_demo_testata );
}
$olobuild_demo_html .= '<main class="wp-block-group is-layout-flow wp-block-group-is-layout-flow">'
    . '<div class="entry-content wp-block-post-content is-layout-flow wp-block-post-content-is-layout-flow">'
    . ( new Olobuild_Frontend_Renderer() )->render_shortcode( [ 'id' => Olobuild_Demo_Temi::id_pagina() ] )
    . '</div></main>';
if ( $olobuild_demo_piede ) {
    $olobuild_demo_html .= ( new Olobuild_Footer_Integration() )->render_footer( $olobuild_demo_piede );
}

?><!DOCTYPE html>
<html lang="<?php echo esc_attr( Olobuild_Demo_Temi::lingua() ); ?>">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="wp-site-blocks">
<?php echo $olobuild_demo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup di testata, pagina e piè di pagina costruito dai renderer delle tile del plugin, che escapano alla fonte ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
