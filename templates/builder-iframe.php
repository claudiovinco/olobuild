<?php
/**
 * Builder iframe page — renders the live preview canvas.
 * Loaded via ?olo_builder_iframe=1 (served by Olo_Builder::serve_builder_iframe).
 * Receives template HTML via postMessage from the parent builder app.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php echo esc_url( OLO_URL . 'assets/vendor/uikit/css/uikit.min.css' ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( OLO_URL . 'assets/css/frontend.css?v=' . OLO_VERSION ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( OLO_URL . 'assets/css/olo-proslider.css?v=' . OLO_VERSION ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( OLO_URL . 'assets/css/olo-svganimator.css?v=' . OLO_VERSION ); ?>">
<?php if ( file_exists( OLO_PATH . 'assets/css/olo-livesearch.css' ) ) : ?>
<link rel="stylesheet" href="<?php echo esc_url( OLO_URL . 'assets/css/olo-livesearch.css?v=' . OLO_VERSION ); ?>">
<?php endif; ?>
<?php $leaflet_url = OLO_URL . 'assets/vendor/leaflet/'; ?>
<link rel="stylesheet" href="<?php echo esc_url( $leaflet_url . 'leaflet.css' ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( $leaflet_url . 'leaflet.markercluster.css' ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( $leaflet_url . 'leaflet.markercluster-default.css' ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( OLO_URL . 'assets/css/iframe-builder.css?v=' . OLO_VERSION ); ?>">
<?php
// Olo Booking CSS/JS (if plugin active)
$booking_path = WP_PLUGIN_DIR . '/olo-booking/';
$booking_url  = plugins_url( 'olo-booking/' );
if ( file_exists( $booking_path . 'assets/css/booking-front.css' ) ) :
?>
<link rel="stylesheet" href="<?php echo esc_url( $booking_url . 'assets/css/booking-front.css?v=' . OLO_VERSION ); ?>">
<?php endif; ?>
<?php
// Olo Virtual Tour CSS (if plugin active)
$vtour_path = WP_PLUGIN_DIR . '/olo-vtour/';
$vtour_url  = plugins_url( 'olo-vtour/' );
if ( file_exists( $vtour_path . 'assets/vendor/psv/psv-bundle.css' ) ) :
?>
<link rel="stylesheet" href="<?php echo esc_url( $vtour_url . 'assets/vendor/psv/psv-bundle.css' ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( $vtour_url . 'assets/css/olo-vtour-viewer.css' ); ?>">
<?php endif; ?>
<?php
// Style system CSS
if ( class_exists( 'Olo_Style_System' ) ) {
    echo '<style id="olo-style-system">' . Olo_Style_System::instance()->generate_css() . '</style>';
}
?>
<style>
html, body { margin: 0; padding: 0; background: #fff; }
body { min-height: 100vh; }
#olo-iframe-root { min-height: 100vh; }
.olo-iframe-empty { display: flex; align-items: center; justify-content: center; min-height: 60vh; color: #9CA3AF; font-family: system-ui, sans-serif; font-size: 14px; }
</style>
</head>
<body>
<div id="olo-iframe-root">
    <div class="olo-iframe-empty">Caricamento preview...</div>
</div>
<!-- Core JS -->
<script src="<?php echo esc_url( OLO_URL . 'assets/vendor/uikit/js/uikit.min.js' ); ?>"></script>
<script src="<?php echo esc_url( OLO_URL . 'assets/vendor/uikit/js/uikit-icons.min.js' ); ?>"></script>
<!-- Tile JS (ProSlider, PostGrid, etc.) -->
<?php if ( file_exists( OLO_PATH . 'assets/js/olo-proslider.js' ) ) : ?>
<script type="module" src="<?php echo esc_url( OLO_URL . 'assets/js/olo-proslider.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<?php if ( file_exists( OLO_PATH . 'assets/js/olo-postgrid.js' ) ) : ?>
<script type="module" src="<?php echo esc_url( OLO_URL . 'assets/js/olo-postgrid.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<!-- Leaflet (maps) -->
<script src="<?php echo esc_url( $leaflet_url . 'leaflet.js' ); ?>"></script>
<script src="<?php echo esc_url( $leaflet_url . 'leaflet.markercluster.js' ); ?>"></script>
<?php if ( file_exists( OLO_PATH . 'assets/js/olo-map.js' ) ) : ?>
<script src="<?php echo esc_url( OLO_URL . 'assets/js/olo-map.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<!-- SVG Animator -->
<?php if ( file_exists( OLO_PATH . 'assets/js/olo-svganimator.js' ) ) : ?>
<script type="module" src="<?php echo esc_url( OLO_URL . 'assets/js/olo-svganimator.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<!-- Viewer 360 -->
<?php if ( file_exists( OLO_PATH . 'assets/js/olo-viewer360.js' ) ) : ?>
<script src="<?php echo esc_url( OLO_URL . 'assets/js/olo-viewer360.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<!-- Frontend utils (entrance animations, scroll effects, etc.) -->
<?php if ( file_exists( OLO_PATH . 'assets/js/olo-utils.js' ) ) : ?>
<script type="module" src="<?php echo esc_url( OLO_URL . 'assets/js/olo-utils.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<!-- Olo Booking JS -->
<?php if ( file_exists( $booking_path . 'assets/js/booking-front.js' ) ) : ?>
<script src="<?php echo esc_url( $booking_url . 'assets/js/booking-front.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<?php if ( file_exists( $booking_path . 'assets/js/olo-restaurant-booking.js' ) ) : ?>
<script src="<?php echo esc_url( $booking_url . 'assets/js/olo-restaurant-booking.js?v=' . OLO_VERSION ); ?>"></script>
<?php endif; ?>
<!-- Olo Virtual Tour JS -->
<?php if ( file_exists( $vtour_path . 'assets/vendor/psv/psv-bundle.js' ) ) : ?>
<script src="<?php echo esc_url( $vtour_url . 'assets/vendor/psv/psv-bundle.js' ); ?>"></script>
<script src="<?php echo esc_url( $vtour_url . 'assets/js/olo-vtour-viewer.js' ); ?>"></script>
<?php endif; ?>
<!-- Bridge (must be last) -->
<script src="<?php echo esc_url( OLO_URL . 'assets/js/iframe-bridge.js?v=' . OLO_VERSION ); ?>"></script>
</body>
</html>
