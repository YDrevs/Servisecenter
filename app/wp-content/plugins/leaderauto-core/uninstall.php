<?php
/**
 * @package LeaderAuto\Core
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'leaderauto_core_settings' );

require __DIR__ . '/includes/content/schema.php';

foreach ( array_keys( LeaderAuto\Core\Content\schema() ) as $page ) {
	delete_option( 'leaderauto_content_' . $page );
}
