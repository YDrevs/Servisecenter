<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main><!-- #main -->

<footer class="site-footer">
	<div class="site-footer__inner">
		<div class="site-footer__col">
			<p class="site-footer__brand"><?php bloginfo( 'name' ); ?></p>
			<p class="site-footer__tagline">
				<?php esc_html_e( 'Сервіс і діагностика електромобілів у Чернівецькому регіоні. Ми працюємо саме з електромобілями.', 'leaderauto' ); ?>
			</p>
		</div>

		<div class="site-footer__col">
			<h3 class="site-footer__title"><?php esc_html_e( 'Контакти', 'leaderauto' ); ?></h3>
			<p>
				<a href="<?php echo esc_attr( leaderauto_phone_href() ); ?>"><?php echo esc_html( leaderauto_phone() ); ?></a><br>
				<?php echo esc_html( leaderauto_address() ); ?>
			</p>
			<p><?php printf( esc_html__( 'Viber: %s', 'leaderauto' ), esc_html( leaderauto_viber() ) ); ?></p>
			<p><?php printf(
				'Telegram: <a href="%1$s">%2$s</a>',
				esc_url( leaderauto_telegram_href() ),
				esc_html( leaderauto_telegram() )
			); ?></p>
		</div>

		<div class="site-footer__col">
			<h3 class="site-footer__title"><?php esc_html_e( 'Розділи', 'leaderauto' ); ?></h3>
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu menu--footer',
					'depth'          => 1,
				) );
			} else {
				leaderauto_primary_fallback();
			}
			?>
		</div>

		<div class="site-footer__col site-footer__social">
			<a class="site-footer__icon" href="<?php echo esc_url( leaderauto_instagram_href() ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
				<svg viewBox="0 0 24 24" width="28" height="28" focusable="false" aria-hidden="true"><path fill="currentColor" d="M12 2.2c3.2 0 3.58 0 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s0 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58 0-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.21 15.58 2.2 15.2 2.2 12s0-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.21 8.8 2.2 12 2.2Zm0 1.8c-3.15 0-3.5 0-4.74.07-.98.04-1.5.2-1.86.34-.47.18-.8.4-1.15.75-.35.35-.57.68-.75 1.15-.14.35-.3.88-.34 1.86C3.1 8.5 3.1 8.85 3.1 12s0 3.5.07 4.74c.04.98.2 1.5.34 1.86.18.47.4.8.75 1.15.35.35.68.57 1.15.75.35.14.88.3 1.86.34 1.24.06 1.59.07 4.74.07s3.5 0 4.74-.07c.98-.04 1.5-.2 1.86-.34.47-.18.8-.4 1.15-.75.35-.35.57-.68.75-1.15.14-.35.3-.88.34-1.86.06-1.24.07-1.59.07-4.74s0-3.5-.07-4.74c-.04-.98-.2-1.5-.34-1.86a3.1 3.1 0 0 0-.75-1.15 3.1 3.1 0 0 0-1.15-.75c-.35-.14-.88-.3-1.86-.34C15.5 4 15.15 4 12 4Zm0 3.07a4.93 4.93 0 1 1 0 9.86 4.93 4.93 0 0 1 0-9.86Zm0 8.13a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4Zm6.28-8.33a1.15 1.15 0 1 1-2.3 0 1.15 1.15 0 0 1 2.3 0Z"/></svg>
			</a>
			<?php /* Facebook: no page yet — add an href once the client has one. */ ?>
			<span class="site-footer__icon" role="img" aria-label="Facebook">
				<svg viewBox="0 0 24 24" width="28" height="28" focusable="false" aria-hidden="true"><path fill="currentColor" d="M13.5 21.9v-7.4h2.5l.38-2.9H13.5V9.76c0-.84.23-1.41 1.44-1.41h1.54V5.76a20.6 20.6 0 0 0-2.24-.12c-2.22 0-3.74 1.36-3.74 3.85v2.14H8v2.9h2.5v7.4H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v15.9a2 2 0 0 1-2 2h-6.5Z"/></svg>
			</span>
		</div>
	</div>

	<div class="site-footer__legal">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Усі права захищено.', 'leaderauto' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
