<?php
// Olobuild WooCommerce wrapper template
if ( ! defined( 'ABSPATH' ) ) exit;

$tpl_id = get_query_var( 'olo_woo_tpl_id', 0 );

// Resolve Olobuild header/footer IDs
$header_id = (int) get_option( 'olo_active_header', 0 );
$footer_id = (int) get_option( 'olo_active_footer', 0 );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
// Render Olobuild header
if ( $header_id ) {
    $header_integration = new Olo_Header_Integration();
    echo $header_integration->render_header( $header_id );
}
?>

<main>
<?php
if ( $tpl_id ) {
    echo do_shortcode( '[olo_template id="' . intval( $tpl_id ) . '"]' );
}
?>
</main>

<?php
// Render Olobuild footer
if ( $footer_id ) {
    $footer_integration = new Olo_Footer_Integration();
    echo $footer_integration->render_footer( $footer_id );
}
?>

<?php wp_footer(); ?>
</body>
</html>
