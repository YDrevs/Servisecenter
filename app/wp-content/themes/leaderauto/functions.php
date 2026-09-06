<?php
/**
 * LeaderAuto theme bootstrap.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LEADERAUTO_VERSION', '0.1.0' );
define( 'LEADERAUTO_DIR', get_stylesheet_directory() );
define( 'LEADERAUTO_URI', get_stylesheet_directory_uri() );

require LEADERAUTO_DIR . '/inc/setup.php';
require LEADERAUTO_DIR . '/inc/enqueue.php';
require LEADERAUTO_DIR . '/inc/template-tags.php';
require LEADERAUTO_DIR . '/inc/activation.php';
