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
			<p><?php printf( esc_html__( 'Viber / Telegram: %s', 'leaderauto' ), esc_html( leaderauto_messenger() ) ); ?></p>
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
	</div>

	<div class="site-footer__legal">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Усі права захищено.', 'leaderauto' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
