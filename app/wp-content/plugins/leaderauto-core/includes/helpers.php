<?php
/**
 * Settings resolution. Env constants win over the options screen so a host
 * (Railway) can inject secrets without touching the database.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OPTION_KEY = 'leaderauto_core_settings';

/**
 * @return array{recipient_email:string,telegram_token:string,telegram_chat_id:string}
 */
function settings(): array {
	$saved = wp_parse_args(
		(array) get_option( OPTION_KEY, array() ),
		array(
			'recipient_email'  => '',
			'telegram_token'   => '',
			'telegram_chat_id' => '',
		)
	);

	$env = static function ( string $name ): string {
		if ( defined( $name ) ) {
			return (string) constant( $name );
		}
		$val = getenv( $name );
		return false === $val ? '' : (string) $val;
	};

	$recipient = $env( 'LEADERAUTO_CONTACT_EMAIL' ) ?: $saved['recipient_email'];

	return array(
		'recipient_email'  => $recipient ?: get_option( 'admin_email' ),
		'telegram_token'   => $env( 'LEADERAUTO_TELEGRAM_TOKEN' ) ?: $saved['telegram_token'],
		'telegram_chat_id' => $env( 'LEADERAUTO_TELEGRAM_CHAT_ID' ) ?: $saved['telegram_chat_id'],
	);
}
