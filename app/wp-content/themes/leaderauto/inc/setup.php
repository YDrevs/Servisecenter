<?php
/**
 * Theme supports, menus, image sizes.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'leaderauto', LEADERAUTO_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'custom-logo', array(
			'height'      => 96,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		) );

		register_nav_menus( array(
			'primary' => __( 'Головне меню', 'leaderauto' ),
		) );

		add_image_size( 'leaderauto-card', 720, 480, true );
		add_image_size( 'leaderauto-wide', 1600, 900, true );
	}
);

/**
 * Favicon and home-screen icons. A Site Icon set in the Customizer wins — WP prints
 * its own tags then. /favicon.ico is answered from the same file so crawlers that
 * ask for it directly don't get redirected to the WordPress logo.
 */
add_action( 'wp_head', static function () {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" sizes="48x48">' . "\n", esc_url( leaderauto_img( 'icons/favicon.ico' ) ) );
	printf( '<link rel="icon" href="%s" type="image/png" sizes="192x192">' . "\n", esc_url( leaderauto_img( 'icons/icon-192.png' ) ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( leaderauto_img( 'icons/apple-touch-icon.png' ) ) );
} );

add_action( 'do_faviconico', static function () {
	if ( has_site_icon() ) {
		return;
	}
	header( 'Content-Type: image/x-icon' );
	header( 'Cache-Control: public, max-age=2592000' );
	readfile( LEADERAUTO_DIR . '/assets/images/icons/favicon.ico' );
	exit;
} );

/**
 * Keep the front page tidy: no default block-editor widget areas needed for this build.
 */
add_action( 'widgets_init', static function () {
	register_sidebar( array(
		'name'          => __( 'Підвал', 'leaderauto' ),
		'id'            => 'footer',
		'description'   => __( 'Опціональний блок у підвалі.', 'leaderauto' ),
		'before_widget' => '<div class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="footer-widget__title">',
		'after_title'   => '</h3>',
	) );
} );
