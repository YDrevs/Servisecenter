<?php
/**
 * Lead events → Make.com custom webhook, with WP-Cron retries.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Make_Webhook {

	private const RETRY_HOOK  = 'leaderauto_make_retry';
	private const TEST_ACTION = 'leaderauto_make_test';
	private const TIMEOUT     = 5;
	private const MAX_TRIES   = 4;

	/** Delay before attempt N+1, keyed by the attempt that just failed. */
	private const DELAYS = array( 1 => 60, 2 => 300, 3 => 1800 );

	/**
	 * Status changes seen in this request, sent once the post (and its note) is saved.
	 *
	 * @var array<int,string> lead id => previous status
	 */
	private array $pending = array();

	public function hooks(): void {
		add_action( 'transition_post_status', array( $this, 'on_transition' ), 10, 3 );
		add_action( 'wp_after_insert_post', array( $this, 'flush_pending' ) );
		add_action( self::RETRY_HOOK, array( self::class, 'deliver' ), 10, 2 );
		add_action( 'admin_post_' . self::TEST_ACTION, array( $this, 'handle_test' ) );
	}

	public static function enabled(): bool {
		return '' !== settings()['make_webhook_url'];
	}

	/**
	 * @param array<string,mixed> $extra top-level fields (e.g. previous_status)
	 */
	public static function send( string $event, int $lead_id, array $extra = array() ): void {
		$lead = Leads::to_array( $lead_id );
		if ( ! self::enabled() || ! $lead ) {
			return;
		}
		self::deliver(
			array_merge(
				array(
					'event'   => $event,
					'sent_at' => wp_date( DATE_ATOM ),
					'site'    => home_url( '/' ),
				),
				$extra,
				array( 'lead' => $lead )
			),
			1
		);
	}

	/**
	 * POST the payload. Returns the HTTP code (0 on transport error).
	 * Real events record the result on the lead and reschedule on failure.
	 *
	 * @param array<string,mixed> $payload
	 */
	public static function deliver( array $payload, int $attempt = 1 ): int {
		$s = settings();
		if ( '' === $s['make_webhook_url'] ) {
			return 0;
		}

		$headers = array( 'Content-Type' => 'application/json; charset=utf-8' );
		if ( '' !== $s['make_api_key'] ) {
			$headers['x-make-apikey'] = $s['make_api_key'];
		}

		$res  = wp_remote_post(
			$s['make_webhook_url'],
			array(
				'timeout' => self::TIMEOUT,
				'headers' => $headers,
				'body'    => wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ),
			)
		);
		$code = is_wp_error( $res ) ? 0 : (int) wp_remote_retrieve_response_code( $res );
		$ok   = $code >= 200 && $code < 300;

		if ( ! empty( $payload['test'] ) ) {
			return $code;
		}

		$lead_id = (int) ( $payload['lead']['id'] ?? 0 );
		if ( $lead_id ) {
			update_post_meta( $lead_id, '_la_make_status', $ok ? '1' : '0' );
			update_post_meta( $lead_id, '_la_make_at', time() );
		}

		if ( ! $ok ) {
			error_log(
				sprintf(
					'[leaderauto-core] make webhook failed (%s, lead %d, attempt %d/%d): %s',
					$payload['event'] ?? '?',
					$lead_id,
					$attempt,
					self::MAX_TRIES,
					is_wp_error( $res ) ? $res->get_error_message() : $code . ' ' . wp_remote_retrieve_body( $res )
				)
			);
			if ( $attempt < self::MAX_TRIES ) {
				wp_schedule_single_event( time() + self::DELAYS[ $attempt ], self::RETRY_HOOK, array( $payload, $attempt + 1 ) );
			}
		}
		return $code;
	}

	public function on_transition( string $new, string $old, \WP_Post $post ): void {
		$ours = Leads::statuses();
		if ( Leads::POST_TYPE === $post->post_type && $new !== $old && isset( $ours[ $new ], $ours[ $old ] ) ) {
			$this->pending[ $post->ID ] = $old;
		}
	}

	public function flush_pending( int $post_id ): void {
		if ( ! isset( $this->pending[ $post_id ] ) ) {
			return;
		}
		$old = $this->pending[ $post_id ];
		unset( $this->pending[ $post_id ] );
		self::send( 'lead.status_changed', $post_id, array( 'previous_status' => $old ) );
	}

	public function handle_test(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Недостатньо прав.', 'leaderauto-core' ), 403 );
		}
		check_admin_referer( self::TEST_ACTION );

		$code = self::deliver(
			array(
				'event'   => 'lead.created',
				'test'    => true,
				'sent_at' => wp_date( DATE_ATOM ),
				'site'    => home_url( '/' ),
				'lead'    => array(
					'id'           => 0,
					'created_at'   => wp_date( DATE_ATOM ),
					'name'         => 'Тестовий клієнт',
					'phone'        => '+38 (098) 089-90-90',
					'phone_digits' => '380980899090',
					'car'          => 'BYD Seal',
					'message'      => 'Тестова заявка з налаштувань сайту.',
					'status'       => 'la_new',
					'status_label' => Leads::statuses()['la_new'],
					'note'         => '',
					'admin_url'    => admin_url( 'edit.php?post_type=' . Leads::POST_TYPE ),
				),
			)
		);

		wp_safe_redirect( add_query_arg( 'la_make_test', $code, admin_url( 'options-general.php?page=leaderauto-core' ) ) );
		exit;
	}

	public static function test_form(): void {
		?>
		<h2><?php esc_html_e( 'Make.com', 'leaderauto-core' ); ?></h2>
		<p><?php esc_html_e( 'Сайт надсилає подію lead.created при новій заявці та lead.status_changed при зміні статусу. Тестова подія потрібна, щоб Make побачив структуру даних.', 'leaderauto-core' ); ?></p>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="<?php echo esc_attr( self::TEST_ACTION ); ?>">
			<?php
			wp_nonce_field( self::TEST_ACTION );
			submit_button( __( 'Надіслати тестову подію', 'leaderauto-core' ), 'secondary', 'submit', false, self::enabled() ? array() : array( 'disabled' => 'disabled' ) );
			?>
		</form>
		<?php
	}

	public static function test_notice(): void {
		if ( ! isset( $_GET['la_make_test'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- display only.
			return;
		}
		$code = (int) $_GET['la_make_test']; // phpcs:ignore WordPress.Security.NonceVerification
		$ok   = $code >= 200 && $code < 300;
		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			$ok ? 'success' : 'error',
			esc_html(
				$ok
					/* translators: %d: HTTP status code */
					? sprintf( __( 'Тестову подію надіслано, Make відповів %d.', 'leaderauto-core' ), $code )
					/* translators: %d: HTTP status code */
					: ( $code ? sprintf( __( 'Make відповів помилкою %d. Перевірте URL і API key.', 'leaderauto-core' ), $code ) : __( 'Не вдалося з’єднатися з Make. Перевірте URL.', 'leaderauto-core' ) )
			)
		);
	}
}
