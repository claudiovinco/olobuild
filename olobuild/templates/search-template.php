<?php
/**
 * Olobuild Search Results Template
 *
 * Standalone template — renders Olobuild header, search results, and footer
 * without relying on the active theme's template parts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tpl_id    = $GLOBALS['olo_search_template_id'] ?? 0;
$header_id = (int) get_option( 'olo_active_header', 0 );
$footer_id = (int) get_option( 'olo_active_footer', 0 );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
if ( $header_id ) {
    $header_int = new Olo_Header_Integration();
    echo $header_int->render_header( $header_id );
}
?>

<main class="olo-search-content" role="main">
<?php
if ( $tpl_id ) {
    echo do_shortcode( '[olo_template id="' . (int) $tpl_id . '"]' );
}
?>
</main>

<?php
if ( $footer_id ) {
    $footer_int = new Olo_Footer_Integration();
    echo $footer_int->render_footer( $footer_id );
}
?>

<?php wp_footer(); ?>
</body>
</html>
