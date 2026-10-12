<?php
/**
 * Small presentation helpers. Contact details come from leaderauto-core
 * (wp-admin → Контент сайту → Контакти) and are read only through these functions.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editable content from leaderauto-core (Контент сайту in wp-admin). Empty if the
 * plugin is inactive — the plugin owns the defaults.
 */
function leaderauto_field( string $page, string $key ): string {
	return function_exists( 'leaderauto_content' ) ? leaderauto_content( $page, $key ) : '';
}

/**
 * Rows of an editable list from leaderauto-core. Empty if the plugin is inactive.
 *
 * @return array<int,array<string,string>>
 */
function leaderauto_rows( string $page, string $key ): array {
	return function_exists( 'leaderauto_content_rows' ) ? leaderauto_content_rows( $page, $key ) : array();
}

/**
 * An editable one-item-per-line field, as a list of non-empty lines.
 *
 * @return string[]
 */
function leaderauto_lines( string $page, string $key ): array {
	return array_values( array_filter( array_map( 'trim', explode( "\n", leaderauto_field( $page, $key ) ) ), 'strlen' ) );
}

/**
 * URL for an editable image value: an attachment ID picked in wp-admin, or a
 * path under assets/images/. '' stays ''.
 */
function leaderauto_media_url( string $value ): string {
	if ( ctype_digit( $value ) ) {
		return (string) wp_get_attachment_url( (int) $value );
	}
	return '' === $value ? '' : leaderauto_img( $value );
}

/**
 * URL of an editable image field: the attachment picked in wp-admin, else the bundled default.
 */
function leaderauto_field_img( string $page, string $key ): string {
	return leaderauto_media_url( leaderauto_field( $page, $key ) );
}

/**
 * Digits of a phone number as written on the site, in international form.
 * A local 0XX… number gets the 38 country prefix.
 */
function leaderauto_phone_digits( string $phone ): string {
	$digits = preg_replace( '/\D+/', '', $phone );
	if ( 10 === strlen( $digits ) && '0' === $digits[0] ) {
		$digits = '38' . $digits;
	}
	return $digits;
}

function leaderauto_phone(): string {
	return leaderauto_field( 'contacts', 'phone' );
}

function leaderauto_phone_href(): string {
	return 'tel:+' . leaderauto_phone_digits( leaderauto_phone() );
}

/** Second line, listed after the main number where both are shown. */
function leaderauto_phone_alt(): string {
	return leaderauto_field( 'contacts', 'phone_alt' );
}

function leaderauto_phone_alt_href(): string {
	return 'tel:+' . leaderauto_phone_digits( leaderauto_phone_alt() );
}

function leaderauto_viber(): string {
	return leaderauto_field( 'contacts', 'viber' );
}

function leaderauto_viber_href(): string {
	return 'viber://chat?number=%2B' . leaderauto_phone_digits( leaderauto_viber() );
}

function leaderauto_telegram(): string {
	return leaderauto_field( 'contacts', 'telegram' );
}

function leaderauto_telegram_href(): string {
	return 'https://t.me/' . ltrim( leaderauto_telegram(), '@' );
}

function leaderauto_instagram_href(): string {
	return leaderauto_field( 'contacts', 'instagram' );
}

function leaderauto_address(): string {
	return leaderauto_field( 'contacts', 'address' );
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
 * Permalink of the Contacts page — the fallback target for every service link.
 * Cached per request: the services section calls this once per list item.
 */
function leaderauto_contacts_url(): string {
	static $url = null;
	if ( null === $url ) {
		$page = get_page_by_path( 'contacts' );
		$url  = $page ? get_permalink( $page ) : home_url( '/contacts/' );
	}
	return $url;
}

/**
 * A service list item as a link to the contact form.
 *
 * The href is a real link to Contacts with the service name in the query string;
 * service-modal.js upgrades the click into an on-page dialog. Without JS, or on
 * open-in-new-tab, the Contacts page picks the name up from the URL.
 */
function leaderauto_service_link( string $name ): void {
	printf(
		'<a class="card__link" href="%1$s" data-service="%2$s">%3$s</a>',
		esc_url( add_query_arg( 'service', rawurlencode( $name ), leaderauto_contacts_url() ) ),
		esc_attr( $name ),
		esc_html( $name )
	);
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
	foreach ( array( 'parts' => 'Запчастини', 'dealer' => 'Автодилер', 'about' => 'Про нас', 'contacts' => 'Контакти' ) as $slug => $label ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $page ) ), esc_html( $label ) );
		}
	}
	echo '</ul>';
}
