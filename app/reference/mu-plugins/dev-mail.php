<?php
/**
 * Reference-environment only: route all WordPress mail to the Mailpit container
 * so the leaderauto-core contact form is testable end to end without a real MTA.
 *
 * Mounted read-only at wp-content/mu-plugins/ by docker-compose.yml. Not shipped.
 *
 * @package LeaderAuto\Reference
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// After easy-wp-smtp (priority 10) so this wins in the local env.
add_action( 'phpmailer_init', static function ( $phpmailer ) {
	$phpmailer->isSMTP();
	$phpmailer->Host       = 'mailpit';
	$phpmailer->Port       = 1025;
	$phpmailer->SMTPAuth   = false;
	$phpmailer->SMTPSecure = '';
	$phpmailer->SMTPAutoTLS = false;
}, 99 );

add_filter( 'wp_mail_from', static fn() => 'dev@leaderauto.local', 99 );
add_filter( 'wp_mail_from_name', static fn() => 'LeaderAuto (dev)', 99 );
