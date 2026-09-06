<?php
/**
 * Settings → LeaderAuto: contact-form delivery targets.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	public function hooks(): void {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	public function menu(): void {
		add_options_page(
			__( 'LeaderAuto', 'leaderauto-core' ),
			__( 'LeaderAuto', 'leaderauto-core' ),
			'manage_options',
			'leaderauto-core',
			array( $this, 'render' )
		);
	}

	public function register(): void {
		register_setting(
			'leaderauto_core',
			OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(),
			)
		);

		add_settings_section( 'leaderauto_core_main', '', '__return_false', 'leaderauto-core' );

		$fields = array(
			'recipient_email'  => __( 'E-mail для заявок', 'leaderauto-core' ),
			'telegram_token'   => __( 'Telegram bot token', 'leaderauto-core' ),
			'telegram_chat_id' => __( 'Telegram chat ID', 'leaderauto-core' ),
		);
		foreach ( $fields as $key => $label ) {
			add_settings_field(
				$key,
				$label,
				array( $this, 'field' ),
				'leaderauto-core',
				'leaderauto_core_main',
				array( 'key' => $key )
			);
		}
	}

	/**
	 * @param array<string,mixed> $args
	 */
	public function field( array $args ): void {
		$key   = $args['key'];
		$saved = (array) get_option( OPTION_KEY, array() );
		$value = isset( $saved[ $key ] ) ? (string) $saved[ $key ] : '';

		$const = strtoupper( 'LEADERAUTO_' . ( 'recipient_email' === $key ? 'CONTACT_EMAIL' : $key ) );
		$overridden = defined( $const ) || false !== getenv( $const );

		printf(
			'<input type="%s" name="%s[%s]" value="%s" class="regular-text" autocomplete="off" %s>',
			'recipient_email' === $key ? 'email' : 'text',
			esc_attr( OPTION_KEY ),
			esc_attr( $key ),
			esc_attr( $value ),
			$overridden ? 'disabled' : ''
		);
		if ( $overridden ) {
			printf( ' <span class="description">%s <code>%s</code></span>', esc_html__( 'задано через середовище:', 'leaderauto-core' ), esc_html( $const ) );
		}
	}

	/**
	 * @param mixed $input
	 * @return array<string,string>
	 */
	public function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : array();
		return array(
			'recipient_email'  => sanitize_email( $input['recipient_email'] ?? '' ),
			'telegram_token'   => sanitize_text_field( $input['telegram_token'] ?? '' ),
			'telegram_chat_id' => sanitize_text_field( $input['telegram_chat_id'] ?? '' ),
		);
	}

	public function render(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'LeaderAuto — заявки з сайту', 'leaderauto-core' ); ?></h1>
			<p><?php esc_html_e( 'Куди надсилати повідомлення з контактної форми. Порожній e-mail = адреса адміністратора. Telegram працює, лише якщо заповнені токен і chat ID.', 'leaderauto-core' ); ?></p>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'leaderauto_core' );
				do_settings_sections( 'leaderauto-core' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
