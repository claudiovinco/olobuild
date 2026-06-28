<?php
/**
 * Olobuild Archive Template
 *
 * Standalone template — renders Olobuild header, archive content, and footer
 * without relying on the active theme's template parts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- variabile locale del template (inclusa nello scope del render, non un global del plugin)
$tpl_id    = $GLOBALS['olobuild_archive_template_id'] ?? 0;
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- variabile locale del template (inclusa nello scope del render, non un global del plugin)
$header_id = (int) get_option( 'olo_active_header', 0 );
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- variabile locale del template (inclusa nello scope del render, non un global del plugin)
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
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- variabile locale del template (inclusa nello scope del render, non un global del plugin)
    $header_int = new Olobuild_Header_Integration();
    echo $header_int->render_header( $header_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- header markup built by the plugin's own tile renderer; tile output is escaped at the source.
}
?>

<main class="olo-archive-content" role="main">
<?php
if ( $tpl_id ) {
    echo do_shortcode( '[olo_template id="' . (int) $tpl_id . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- renders the plugin's own [olo_template] shortcode (id hard-cast to int); template markup is escaped at the source by each tile renderer.
}
?>
</main>

<?php
if ( $footer_id ) {
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- variabile locale del template (inclusa nello scope del render, non un global del plugin)
    $footer_int = new Olobuild_Footer_Integration();
    echo $footer_int->render_footer( $footer_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- footer markup built by the plugin's own tile renderer; tile output is escaped at the source.
}
?>

<?php wp_footer(); ?>
</body>
</html>
