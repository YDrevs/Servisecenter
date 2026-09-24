<?php
/**
 * Voice AI assistant (Vapi) — a browser call for visitors outside working hours.
 *
 * Renders nothing unless leaderauto-core has both a Vapi public key and an
 * assistant ID (env LEADERAUTO_VAPI_* or Settings → LeaderAuto). The call itself
 * runs in assets/src/js/voice.js, which loads the SDK only on the first click.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'LeaderAuto\Core\settings' ) ) {
	return;
}

$la_vapi = \LeaderAuto\Core\settings();
if ( '' === $la_vapi['vapi_public_key'] || '' === $la_vapi['vapi_assistant_id'] ) {
	return;
}
?>
<section class="section section--surface voice" id="voice-assistant">
	<div class="section__inner voice__inner">
		<div class="voice__text">
			<h2 class="voice__title"><?php esc_html_e( 'Телефонуєте в неробочий час?', 'leaderauto' ); ?></h2>
			<p><?php esc_html_e( 'Наш асистент надасть всю необхідну інформацію. Вона розповість про послуги, відповість на запитання, а також забронює для Вас час до майстра на сервіс або на тест-драйв автомобіля!', 'leaderauto' ); ?></p>
			<p class="voice__note"><?php esc_html_e( 'Потрібен мікрофон — браузер попросить дозвіл. Дзвінок безкоштовний.', 'leaderauto' ); ?></p>
		</div>

		<div class="voice__call"
			data-vapi-key="<?php echo esc_attr( $la_vapi['vapi_public_key'] ); ?>"
			data-vapi-assistant="<?php echo esc_attr( $la_vapi['vapi_assistant_id'] ); ?>">
			<button type="button" class="btn voice__button" data-voice-toggle>
				<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.92V22h2v-3.08A7 7 0 0 0 19 12h-2Z"/></svg>
				<span data-voice-label><?php esc_html_e( 'Поговорити з асистентом', 'leaderauto' ); ?></span>
			</button>
			<p class="voice__status" data-voice-status role="status" aria-live="polite"></p>
		</div>
	</div>
</section>
