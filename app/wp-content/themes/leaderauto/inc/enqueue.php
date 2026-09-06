<?php
/**
 * Asset loading.
 *
 * Two modes:
 *  - dev: `npm run dev` in app/build, then create app/wp-content/themes/leaderauto/assets/dist/.dev
 *    (or define LEADERAUTO_DEV in wp-config). Assets are served by the Vite dev server with HMR.
 *  - prod: reads assets/dist/.vite/manifest.json produced by `npm run build`.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LEADERAUTO_DEV_ORIGIN = 'http://localhost:5173';
const LEADERAUTO_ENTRY       = 'js/main.js';

/**
 * Whether to load from the Vite dev server.
 */
function leaderauto_is_dev(): bool {
	if ( defined( 'LEADERAUTO_DEV' ) && LEADERAUTO_DEV ) {
		return true;
	}
	return file_exists( LEADERAUTO_DIR . '/assets/dist/.dev' );
}

add_action( 'wp_enqueue_scripts', static function () {
	// Fonts (see app/reference/NOTES.md — Kanit for headings, Open Sans for body).
	wp_enqueue_style(
		'leaderauto-fonts',
		'https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Open+Sans:wght@400;600;700&display=swap',
		array(),
		null
	);

	if ( leaderauto_is_dev() ) {
		leaderauto_enqueue_dev();
		return;
	}
	leaderauto_enqueue_prod();
} );

/**
 * Dev: inject the Vite client + entry as ES modules.
 */
function leaderauto_enqueue_dev(): void {
	$tags = array(
		LEADERAUTO_DEV_ORIGIN . '/@vite/client',
		LEADERAUTO_DEV_ORIGIN . '/' . LEADERAUTO_ENTRY,
	);
	add_action( 'wp_head', static function () use ( $tags ) {
		foreach ( $tags as $src ) {
			printf( '<script type="module" src="%s"></script>' . "\n", esc_url( $src ) );
		}
	}, 1 );
}

/**
 * Prod: enqueue hashed files from the build manifest.
 */
function leaderauto_enqueue_prod(): void {
	$manifest_path = LEADERAUTO_DIR . '/assets/dist/.vite/manifest.json';
	if ( ! is_readable( $manifest_path ) ) {
		$manifest_path = LEADERAUTO_DIR . '/assets/dist/manifest.json'; // older Vite
	}
	if ( ! is_readable( $manifest_path ) ) {
		return;
	}

	$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );
	if ( ! isset( $manifest[ LEADERAUTO_ENTRY ] ) ) {
		return;
	}

	$entry   = $manifest[ LEADERAUTO_ENTRY ];
	$dist_ur = LEADERAUTO_URI . '/assets/dist/';

	foreach ( (array) ( $entry['css'] ?? array() ) as $i => $css ) {
		wp_enqueue_style( 'leaderauto-' . $i, $dist_ur . $css, array(), LEADERAUTO_VERSION );
	}

	if ( ! empty( $entry['file'] ) ) {
		wp_enqueue_script( 'leaderauto', $dist_ur . $entry['file'], array(), LEADERAUTO_VERSION, true );
	}
}

/**
 * Load the main script as a module.
 */
add_filter( 'script_loader_tag', static function ( $tag, $handle, $src ) {
	if ( 'leaderauto' !== $handle ) {
		return $tag;
	}
	return sprintf( '<script type="module" src="%s" id="%s-js"></script>' . "\n", esc_url( $src ), esc_attr( $handle ) );
}, 10, 3 );

/**
 * Expose the REST endpoint + nonce to the contact form script.
 */
add_action( 'wp_enqueue_scripts', static function () {
	$data = array(
		'restUrl' => esc_url_raw( rest_url( 'leaderauto/v1/contact' ) ),
		'nonce'   => wp_create_nonce( 'wp_rest' ),
	);
	wp_register_script( 'leaderauto-data', '', array(), LEADERAUTO_VERSION, true );
	wp_enqueue_script( 'leaderauto-data' );
	wp_add_inline_script( 'leaderauto-data', 'window.LEADERAUTO = ' . wp_json_encode( $data ) . ';', 'before' );
}, 5 );
