<?php
/**
 * Canvas full-page per /north-demo/ — niente header/footer del tema, solo il contenuto
 * Olobuild + i font del blueprint North. Caricato da north-demo.php via template_include.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<?php wp_head(); ?>
<style id="north-font-tokens">
  html, body { margin: 0; padding: 0; background: #ffffff; }
  /* Mantieni lo scroll sulla WINDOW (non sul body): un body con overflow-x:clip
     forzerebbe overflow-y:auto rendendo il body lo scroller e disattivando gli
     effetti scroll-linked (reveal). Clip in orizzontale sull'html. */
  html { overflow-x: clip; }
  body { overflow-x: visible !important; overflow-y: visible !important; }
  :root, body, .olo-template, .olo-frontend, [class*="olo-tpl"] {
    --olo-font-family-heading: 'Space Grotesk', -apple-system, BlinkMacSystemFont, sans-serif !important;
    --olo-font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
    --olo-font-family-mono: 'Space Mono', ui-monospace, 'SF Mono', Menlo, monospace !important;
  }
</style>
</head>
<body <?php body_class( 'north-demo-canvas' ); ?>>
<?php
while ( have_posts() ) {
    the_post();
    the_content();
}
wp_footer();
?>
</body>
</html>
