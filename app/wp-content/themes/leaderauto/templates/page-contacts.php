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
					<p><a href="<?php echo esc_attr( leaderauto_phone_alt_href() ); ?>"><?php echo esc_html( leaderauto_phone_alt() ); ?></a></p>
					<?php /* Messenger lines lead with the app's mark instead of its name; the name
					         stays for screen readers. */ ?>
					<p>
						<a class="contacts__messenger" href="<?php echo esc_attr( leaderauto_viber_href() ); ?>">
							<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5c-5.2 0-8.5 1.9-8.5 7.6 0 3.4 1.2 5.4 3.3 6.6v3.8l3.1-2.9c.7.1 1.4.1 2.1.1 5.2 0 8.5-1.9 8.5-7.6S17.2 2.5 12 2.5Z"/><path d="M13.2 5.6a3.6 3.6 0 0 1 3.4 3.4M13.3 7.4a1.8 1.8 0 0 1 1.5 1.5"/><path fill="currentColor" stroke="none" d="M9.2 7.2c.3-.3.8-.3 1 .1l.7 1.2c.2.3.1.7-.1.9l-.4.4c.4.9 1.1 1.6 2 2l.4-.4c.2-.2.6-.3.9-.1l1.2.7c.4.2.4.7.1 1l-.5.5c-.5.5-1.3.6-1.9.3a7.6 7.6 0 0 1-3.7-3.7c-.3-.6-.2-1.4.3-1.9l.5-.5Z"/></svg>
							<span class="screen-reader-text">Viber:</span>
							<?php echo esc_html( leaderauto_viber() ); ?>
						</a>
					</p>
					<p>
						<a class="contacts__messenger" href="<?php echo esc_url( leaderauto_telegram_href() ); ?>">
							<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path fill="currentColor" d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
							<span class="screen-reader-text">Telegram:</span>
							<?php echo esc_html( leaderauto_telegram() ); ?>
						</a>
					</p>
				</div>
			</aside>
		</div>
	</section>

	<?php get_template_part( 'template-parts/voice-assistant' ); ?>
</article>
<?php
get_footer();
