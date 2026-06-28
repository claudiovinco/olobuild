<?php
/**
 * Builder iframe page — renders the live preview canvas.
 * Loaded via ?olo_builder_iframe=1 (served by Olobuild_Builder::serve_builder_iframe).
 * Receives template HTML via postMessage from the parent builder app.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
// Tutti gli stili dell'iframe sono registrati via wp_enqueue_style in
// Olobuild_Builder::enqueue_builder_iframe_assets() ed emessi qui in ordine di dipendenza.
wp_print_styles( $this->iframe_style_handles );
?>
<?php
// Style system CSS
if ( class_exists( 'Olobuild_Style_System' ) ) {
    echo '<style id="olo-style-system">' . Olobuild_Style_System::instance()->generate_css() . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS generated internally by Olobuild_Style_System from sanitized style options; escaping would corrupt valid CSS.
}
?>
<style>
/* Il bg pagina è gestito dinamicamente da olo:set-page-bg / olo:render — qui solo reset */
html, body { margin: 0; padding: 0; }
body { min-height: 100vh; background: #fff; }
body[data-olo-pagebg], html[data-olo-pagebg], body[data-olo-pagebg] #olo-iframe-root { background: transparent !important; }
#olo-iframe-root { min-height: 100vh; }
.olo-iframe-empty { display: flex; align-items: center; justify-content: center; min-height: 60vh; color: #9CA3AF; font-family: system-ui, sans-serif; font-size: 14px; }

/* Builder mode: disable sticky headers/sections so content is never hidden behind a fixed header */
.olo-site-header.olo-header-sticky,
.olo-site-header.olo-header-classic.olo-header-sticky,
.olo-sticky-cover,
.olo-sticky-reveal {
  position: relative !important;
  top: auto !important;
  z-index: auto !important;
}
/* Builder mode: .olo-template is used (in REST render) to apply container max-width rules from frontend.css.
   Disable its break-out trick (width:100vw; transform) which is meant for theme escape, not iframe. */
.olo-template {
  width: 100% !important;
  position: static !important;
  left: auto !important;
  transform: none !important;
}
.olo-floatingpanel,
.olo-fp-wrapper {
  scroll-margin-top: 80px;
}
</style>
</head>
<body>
<div id="olo-iframe-root">
    <?php echo Olobuild_Builder::get_iframe_empty_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup built internally with esc_html__() labels ?>
</div>
<?php
// Tutti gli script dell'iframe sono registrati via wp_enqueue_script in
// Olobuild_Builder::enqueue_builder_iframe_assets() (uikit→runtimes→bridge per ultimo,
// type="module" applicato via filtro). Il worker pdf.js è inline-script su olo-ifr-pdfjs.
wp_print_scripts( $this->iframe_script_handles );
?>
</body>
</html>
