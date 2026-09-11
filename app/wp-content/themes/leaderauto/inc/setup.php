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
		add_theme_support( 'automatic-feed-links' );
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
 * The front page's tab title. WP builds it from blogname + blogdescription, and both
 * options still read "LeaderAuto" in the DB, so it renders "LeaderAuto – LeaderAuto".
 * Home only, matching the header wordmark; the rest of the site keeps the DB values.
 */
add_filter( 'document_title_parts', static function ( $parts ) {
	if ( is_front_page() ) {
		$parts = array( 'title' => 'BYD-Center' );
	}

	return $parts;
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
