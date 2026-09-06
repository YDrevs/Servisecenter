<?php
/**
 * @package LeaderAuto\Core
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'leaderauto_core_settings' );
