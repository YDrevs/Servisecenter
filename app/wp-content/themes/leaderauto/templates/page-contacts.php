<?php
/**
 * Template Name: Контакти (Contacts)
 *
 * Reference page 13. Form posts to the leaderauto-core REST endpoint
 * (POST /wp-json/leaderauto/v1/contact). The demo e-mail address on the original
 * page (hello@divicardetailing.com) is intentionally omitted.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<article class="page-shell page-shell--contacts">
	<header class="page-shell__head" style="--page-bg: url('<?php echo esc_url( leaderauto_img( 'detailing-12.jpg' ) ); ?>');">
		<div class="page-shell__inner">
			<h1 class="page-shell__title"><?php esc_html_e( 'Залишити заявку', 'leaderauto' ); ?></h1>
			<p class="page-shell__lead"><?php esc_html_e( 'Опишіть коротко задачу — і ми зв’яжемось для діагностики та розрахунку.', 'leaderauto' ); ?></p>
		</div>
	</header>

	<section class="section">
		<div class="section__inner contacts">
			<form class="contact-form" id="leaderauto-contact-form" novalidate>
				<p class="contact-form__field">
					<label for="lf-name"><?php esc_html_e( 'Ваше ім’я', 'leaderauto' ); ?></label>
					<input type="text" id="lf-name" name="name" autocomplete="name" required>
				</p>
				<p class="contact-form__field">
					<label for="lf-phone"><?php esc_html_e( 'Ваш телефон', 'leaderauto' ); ?></label>
					<input type="tel" id="lf-phone" name="phone" autocomplete="tel" required>
				</p>
				<p class="contact-form__field">
					<label for="lf-car"><?php esc_html_e( 'Ваш автомобіль', 'leaderauto' ); ?></label>
					<input type="text" id="lf-car" name="car" placeholder="BYD, Tesla, Nissan Leaf…">
				</p>
				<p class="contact-form__field">
					<label for="lf-message"><?php esc_html_e( 'Напишіть повідомлення', 'leaderauto' ); ?></label>
					<textarea id="lf-message" name="message" rows="4"></textarea>
				</p>
				<p class="contact-form__field contact-form__hp" aria-hidden="true">
					<label for="lf-website"><?php esc_html_e( 'Не заповнюйте це поле', 'leaderauto' ); ?></label>
					<input type="text" id="lf-website" name="website" tabindex="-1" autocomplete="off">
				</p>
				<p class="contact-form__actions">
					<button type="submit" class="btn"><?php esc_html_e( 'Відправити', 'leaderauto' ); ?></button>
				</p>
				<p class="contact-form__status" role="status" aria-live="polite" hidden></p>
			</form>

			<?php /* Reference: each block leads with a white-disc icon over a Kanit 24px
			         heading, the whole column a blue panel cut diagonally bottom-right. */ ?>
			<aside class="contacts__info">
				<div class="contacts__block">
					<span class="contacts__badge" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="20" height="20" focusable="false"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5Z"/></svg>
					</span>
					<h2 class="contacts__title"><?php esc_html_e( 'Завітайте', 'leaderauto' ); ?></h2>
					<p><?php echo esc_html( leaderauto_address() ); ?></p>
				</div>
				<div class="contacts__block">
					<span class="contacts__badge" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="20" height="20" focusable="false"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24c1.15.38 2.36.57 3.6.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.24.2 2.45.57 3.6a1 1 0 0 1-.25 1l-2.22 2.2Z"/></svg>
					</span>
					<h2 class="contacts__title"><?php esc_html_e( 'Дзвоніть нам', 'leaderauto' ); ?></h2>
					<p><a href="<?php echo esc_attr( leaderauto_phone_href() ); ?>"><?php echo esc_html( leaderauto_phone() ); ?></a></p>
					<p><?php printf( esc_html__( 'Viber: %s', 'leaderauto' ), esc_html( leaderauto_viber() ) ); ?></p>
					<p><?php printf(
						'Telegram: <a href="%1$s">%2$s</a>',
						esc_url( leaderauto_telegram_href() ),
						esc_html( leaderauto_telegram() )
					); ?></p>
				</div>
			</aside>
		</div>
	</section>
</article>
<?php
get_footer();
