<?php
/**
 * Contact form, shared by the Contacts page and the services modal.
 *
 * Posts to the leaderauto-core REST endpoint (POST /wp-json/leaderauto/v1/contact)
 * via assets/src/js/form.js, which binds every .contact-form on the page — hence
 * the id prefix: two copies can coexist without colliding ids.
 *
 * @param string $args['id'] Unique id prefix for this instance.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$la_id = isset( $args['id'] ) ? $args['id'] : 'leaderauto-contact-form';
?>
<form class="contact-form" id="<?php echo esc_attr( $la_id ); ?>" novalidate>
	<p class="contact-form__field">
		<label for="<?php echo esc_attr( $la_id ); ?>-name"><?php esc_html_e( 'Ваше ім’я', 'leaderauto' ); ?></label>
		<input type="text" id="<?php echo esc_attr( $la_id ); ?>-name" name="name" autocomplete="name" required>
	</p>
	<p class="contact-form__field">
		<label for="<?php echo esc_attr( $la_id ); ?>-phone"><?php esc_html_e( 'Ваш телефон', 'leaderauto' ); ?></label>
		<input type="tel" id="<?php echo esc_attr( $la_id ); ?>-phone" name="phone" autocomplete="tel" required>
	</p>
	<p class="contact-form__field">
		<label for="<?php echo esc_attr( $la_id ); ?>-car"><?php esc_html_e( 'Ваш автомобіль', 'leaderauto' ); ?></label>
		<input type="text" id="<?php echo esc_attr( $la_id ); ?>-car" name="car" placeholder="BYD, Tesla, Nissan Leaf…">
	</p>
	<p class="contact-form__field">
		<label for="<?php echo esc_attr( $la_id ); ?>-message"><?php esc_html_e( 'Напишіть повідомлення', 'leaderauto' ); ?></label>
		<textarea id="<?php echo esc_attr( $la_id ); ?>-message" name="message" rows="4"></textarea>
	</p>
	<p class="contact-form__field contact-form__hp" aria-hidden="true">
		<label for="<?php echo esc_attr( $la_id ); ?>-website"><?php esc_html_e( 'Не заповнюйте це поле', 'leaderauto' ); ?></label>
		<input type="text" id="<?php echo esc_attr( $la_id ); ?>-website" name="website" tabindex="-1" autocomplete="off">
	</p>
	<p class="contact-form__actions">
		<button type="submit" class="btn"><?php esc_html_e( 'Відправити', 'leaderauto' ); ?></button>
	</p>
	<p class="contact-form__status" role="status" aria-live="polite" hidden></p>
</form>
