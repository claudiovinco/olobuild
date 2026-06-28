<?php
/**
 * Olobuild Debug Bar — mostra template renderizzati nella admin toolbar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Olobuild_Debug_Bar {

	private static $rendered = [];

	public static function init() {
		if ( ! get_option( 'olo_debug_bar' ) || ! current_user_can( 'manage_options' ) || is_admin() ) {
			return;
		}

		add_action( 'olobuild_template_rendered', [ __CLASS__, 'collect' ], 10, 3 );
		add_action( 'admin_bar_menu', [ __CLASS__, 'add_bar_nodes' ], 999 );
		add_action( 'wp_head', [ __CLASS__, 'head_css' ] );
	}

	public static function collect( $id, $title, $type ) {
		self::$rendered[] = [
			'id'    => $id,
			'title' => $title,
			'type'  => $type,
		];
	}

	public static function add_bar_nodes( $wp_admin_bar ) {
		$count = count( self::$rendered );

		$wp_admin_bar->add_node( [
			'id'    => 'olo-debug',
			'title' => 'Olo Debug (' . $count . ')',
		] );

		foreach ( self::$rendered as $item ) {
			$wp_admin_bar->add_node( [
				'parent' => 'olo-debug',
				'id'     => 'olo-debug-' . $item['id'],
				'title'  => $item['title'] . ' (' . $item['type'] . ') #' . $item['id'],
				'href'   => admin_url( 'admin.php?page=olobuilder-templates&template_id=' . $item['id'] ),
			] );
		}
	}

	public static function head_css() {
		echo '<style>
#wp-admin-bar-olo-debug > .ab-item { background: #1e1e2f !important; color: #a78bfa !important; }
#wp-admin-bar-olo-debug:hover > .ab-item { background: #2d2d44 !important; }
</style>';
	}
}
