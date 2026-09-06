<?php
/**
 * On theme activation: make sure the four pages, the front page and the primary
 * menu exist so the site is coherent immediately. Idempotent — safe to re-run.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', 'leaderauto_scaffold_site' );

function leaderauto_scaffold_site(): void {
	$pages = array(
		'home'     => array( 'title' => 'Головна',   'template' => '' ),
		'dealer'   => array( 'title' => 'Автодилер', 'template' => 'templates/page-dealer.php' ),
		'about'    => array( 'title' => 'Про нас',   'template' => 'templates/page-about.php' ),
		'contacts' => array( 'title' => 'Контакти',  'template' => 'templates/page-contacts.php' ),
	);

	$ids = array();
	foreach ( $pages as $slug => $conf ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = (int) $existing->ID;
		} else {
			$ids[ $slug ] = (int) wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $conf['title'],
				'post_content' => '',
			) );
		}
		if ( $ids[ $slug ] && $conf['template'] ) {
			update_post_meta( $ids[ $slug ], '_wp_page_template', $conf['template'] );
		}
	}

	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}

	// This build expects pretty permalinks. Only set a structure if none is configured.
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		update_option( 'permalink_structure', '/%postname%/' );
		if ( $wp_rewrite instanceof \WP_Rewrite ) {
			$wp_rewrite->init();
		}
	}

	leaderauto_build_primary_menu( $ids );
	flush_rewrite_rules();
}

/**
 * @param array<string,int> $ids slug => page ID
 */
function leaderauto_build_primary_menu( array $ids ): void {
	$menu_name = 'LeaderAuto — головне';
	$menu      = wp_get_nav_menu_object( $menu_name );
	$menu_id   = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $menu_name );

	if ( is_wp_error( $menu_id ) || ! $menu_id ) {
		return;
	}

	// Only populate an empty menu — don't stomp manual edits.
	if ( ! wp_get_nav_menu_items( $menu_id ) ) {
		$order = array( 'home' => 'Головна', 'dealer' => 'Автодилер', 'about' => 'Про нас', 'contacts' => 'Контакти' );
		$i     = 0;
		foreach ( $order as $slug => $label ) {
			if ( empty( $ids[ $slug ] ) ) {
				continue;
			}
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'     => $label,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $ids[ $slug ],
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => ++$i,
			) );
		}
	}

	$locations            = (array) get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
