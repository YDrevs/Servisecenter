<?php
/**
 * Plugin Name:       LeaderAuto Core
 * Description:        Business logic for the LeaderAuto site — contact-form REST endpoint and delivery (e-mail + Telegram). Kept separate from the theme so the design can change without losing functionality.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Text Domain:       leaderauto-core
 *
 * @package LeaderAuto\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LEADERAUTO_CORE_VERSION', '0.1.0' );
define( 'LEADERAUTO_CORE_DIR', plugin_dir_path( __FILE__ ) );

require LEADERAUTO_CORE_DIR . 'includes/helpers.php';
require LEADERAUTO_CORE_DIR . 'includes/class-settings.php';
require LEADERAUTO_CORE_DIR . 'includes/class-rest-contact.php';

add_action( 'plugins_loaded', static function () {
	( new \LeaderAuto\Core\Settings() )->hooks();
	( new \LeaderAuto\Core\Rest_Contact() )->hooks();
} );
