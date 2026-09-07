<?php
/**
 * Small presentation helpers. Single source of truth for on-site contact details.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function leaderauto_phone(): string {
	return '+380 (95) 066 29 21';
}

function leaderauto_phone_href(): string {
	return 'tel:+380950662921';
}

function leaderauto_viber(): string {
	return '(095) 066 29 21';
}

function leaderauto_telegram(): string {
	return '@V_P_97';
}

function leaderauto_telegram_href(): string {
	return 'https://t.me/V_P_97';
}

function leaderauto_address(): string {
	return '60313, с. Магала, вул. Гр. Нандріша, 6';
}

/**
 * Brands in order of prominence (CLAUDE.md).
 *
 * @return string[]
 */
function leaderauto_brands(): array {
	return array( 'BYD', 'Tesla', 'Zeekr', 'Volkswagen ID', 'Nissan Leaf' );
}

/**
 * URL for a file in assets/images/.
 */
function leaderauto_img( string $rel ): string {
	return LEADERAUTO_URI . '/assets/images/' . ltrim( $rel, '/' );
}

/**
 * Section wrapper open/close — keeps markup consistent across template-parts.
 */
function leaderauto_section_open( string $id, string $modifier = '' ): void {
	printf(
		'<section id="%1$s" class="section%2$s"><div class="section__inner">',
		esc_attr( $id ),
		$modifier ? ' section--' . esc_attr( $modifier ) : ''
	);
}

function leaderauto_section_close(): void {
	echo '</div></section>';
}

/**
 * Menu fallback when no `primary` menu is assigned.
 */
function leaderauto_primary_fallback(): void {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Головна</a></li>';
	foreach ( array( 'dealer' => 'Автодилер', 'about' => 'Про нас', 'contacts' => 'Контакти' ) as $slug => $label ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $page ) ), esc_html( $label ) );
		}
	}
	echo '</ul>';
}
