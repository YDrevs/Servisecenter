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
			<?php get_template_part( 'template-parts/contact-form', null, array( 'id' => 'leaderauto-contact-form' ) ); ?>

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

	<?php get_template_part( 'template-parts/voice-assistant' ); ?>
</article>
<?php
get_footer();
