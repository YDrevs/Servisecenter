<?php
/**
 * POST /wp-json/leaderauto/v1/contact
 *
 * Validates the contact form, then delivers by e-mail and (if configured) Telegram.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rest_Contact {

	private const NAMESPACE = 'leaderauto/v1';
	private const ROUTE     = '/contact';
	private const WINDOW    = 120; // seconds between submissions per IP

	public function hooks(): void {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	public function register(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'handle' ),
				'args'                => array(
					'name'    => array( 'type' => 'string', 'required' => true ),
					'phone'   => array( 'type' => 'string', 'required' => true ),
					'car'     => array( 'type' => 'string' ),
					'message' => array( 'type' => 'string' ),
					'website' => array( 'type' => 'string' ), // honeypot
				),
			)
		);
	}

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle( WP_REST_Request $request ) {
		// Honeypot: silently accept, don't deliver.
		if ( '' !== trim( (string) $request->get_param( 'website' ) ) ) {
			return new WP_REST_Response( array( 'ok' => true, 'message' => $this->thanks() ), 200 );
		}

		$ip  = $this->client_ip();
		$key = 'leaderauto_contact_' . md5( $ip );
		if ( $ip && get_transient( $key ) ) {
			return new WP_Error(
				'leaderauto_rate_limited',
				__( 'Ви щойно надіслали заявку. Зачекайте пару хвилин.', 'leaderauto-core' ),
				array( 'status' => 429 )
			);
		}

		$name    = sanitize_text_field( (string) $request->get_param( 'name' ) );
		$phone   = sanitize_text_field( (string) $request->get_param( 'phone' ) );
		$car     = sanitize_text_field( (string) $request->get_param( 'car' ) );
		$message = sanitize_textarea_field( (string) $request->get_param( 'message' ) );

		$errors = array();
		if ( mb_strlen( $name ) < 2 || mb_strlen( $name ) > 100 ) {
			$errors['name'] = __( 'Вкажіть ім’я.', 'leaderauto-core' );
		}
		$digits = preg_replace( '/\D+/', '', $phone );
		if ( strlen( (string) $digits ) < 7 || strlen( (string) $digits ) > 15 ) {
			$errors['phone'] = __( 'Вкажіть коректний номер телефону.', 'leaderauto-core' );
		}
		if ( mb_strlen( $message ) > 2000 ) {
			$errors['message'] = __( 'Повідомлення задовге.', 'leaderauto-core' );
		}
		if ( $errors ) {
			return new WP_Error(
				'leaderauto_invalid',
				__( 'Перевірте заповнені поля.', 'leaderauto-core' ),
				array( 'status' => 422, 'fields' => $errors )
			);
		}

		$payload = compact( 'name', 'phone', 'car', 'message' );
		$sent_mail     = $this->send_email( $payload );
		$sent_telegram = $this->send_telegram( $payload );

		if ( ! $sent_mail && ! $sent_telegram ) {
			return new WP_Error(
				'leaderauto_delivery_failed',
				__( 'Не вдалося надіслати заявку. Спробуйте пізніше або зателефонуйте.', 'leaderauto-core' ),
				array( 'status' => 502 )
			);
		}

		if ( $ip ) {
			set_transient( $key, 1, self::WINDOW );
		}

		return new WP_REST_Response( array( 'ok' => true, 'message' => $this->thanks() ), 200 );
	}

	private function thanks(): string {
		return __( 'Дякуємо! Ми зв’яжемось найближчим часом.', 'leaderauto-core' );
	}

	/**
	 * @param array<string,string> $p
	 */
	private function send_email( array $p ): bool {
		$to = settings()['recipient_email'];
		if ( ! is_email( $to ) ) {
			return false;
		}

		$lines = array(
			sprintf( /* translators: %s: name */ __( 'Ім’я: %s', 'leaderauto-core' ), $p['name'] ),
			sprintf( /* translators: %s: phone */ __( 'Телефон: %s', 'leaderauto-core' ), $p['phone'] ),
		);
		if ( '' !== $p['car'] ) {
			$lines[] = sprintf( /* translators: %s: car */ __( 'Автомобіль: %s', 'leaderauto-core' ), $p['car'] );
		}
		if ( '' !== $p['message'] ) {
			$lines[] = '';
			$lines[] = $p['message'];
		}
		$lines[] = '';
		$lines[] = sprintf( /* translators: %s: url */ __( 'Надіслано з %s', 'leaderauto-core' ), home_url( '/' ) );

		$subject = sprintf( /* translators: %s: name */ __( 'Нова заявка з сайту — %s', 'leaderauto-core' ), $p['name'] );

		$ok = wp_mail( $to, $subject, implode( "\n", $lines ) );
		if ( ! $ok ) {
			error_log( '[leaderauto-core] wp_mail failed; payload: ' . wp_json_encode( $p ) );
		}
		return (bool) $ok;
	}

	/**
	 * @param array<string,string> $p
	 */
	private function send_telegram( array $p ): bool {
		$s = settings();
		if ( '' === $s['telegram_token'] || '' === $s['telegram_chat_id'] ) {
			return false;
		}

		$text = sprintf(
			"🚗 *%s*\n%s: %s\n%s: %s%s%s",
			__( 'Нова заявка з сайту', 'leaderauto-core' ),
			__( 'Ім’я', 'leaderauto-core' ),
			$p['name'],
			__( 'Телефон', 'leaderauto-core' ),
			$p['phone'],
			'' !== $p['car'] ? "\n" . __( 'Авто', 'leaderauto-core' ) . ': ' . $p['car'] : '',
			'' !== $p['message'] ? "\n\n" . $p['message'] : ''
		);

		$res = wp_remote_post(
			sprintf( 'https://api.telegram.org/bot%s/sendMessage', $s['telegram_token'] ),
			array(
				'timeout' => 8,
				'body'    => array(
					'chat_id'    => $s['telegram_chat_id'],
					'text'       => $text,
					'parse_mode' => 'Markdown',
				),
			)
		);

		if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
			error_log( '[leaderauto-core] telegram send failed: ' . ( is_wp_error( $res ) ? $res->get_error_message() : wp_remote_retrieve_body( $res ) ) );
			return false;
		}
		return true;
	}

	private function client_ip(): string {
		$raw = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
		$ip  = filter_var( $raw, FILTER_VALIDATE_IP );
		return $ip ?: '';
	}
}
