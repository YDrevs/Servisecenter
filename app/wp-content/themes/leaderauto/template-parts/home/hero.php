<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contacts = get_page_by_path( 'contacts' );
$cta_url  = $contacts ? get_permalink( $contacts ) : home_url( '/contacts/' );
?>
<section class="hero" id="hero">
	<div class="hero__inner">
		<p class="hero__eyebrow"><?php esc_html_e( 'Ми не «звичайне СТО». Ми працюємо саме з електромобілями.', 'leaderauto' ); ?></p>
		<h1 class="hero__title">LeaderAuto — Service &amp; Dealer</h1>
		<p class="hero__lead">
			<?php esc_html_e( 'Програмний та технічний ремонт електромобілів BYD, Tesla, Zeekr, Volkswagen, Nissan та інших марок.', 'leaderauto' ); ?>
		</p>
		<p class="hero__sub">
			<?php esc_html_e( 'Діагностика, оновлення, кодування, заміна мастил, підбір та замовлення запчастин.', 'leaderauto' ); ?>
		</p>
		<p class="hero__actions">
			<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Отримати консультацію', 'leaderauto' ); ?></a>
			<a class="btn btn--ghost" href="#services"><?php esc_html_e( 'Наші послуги', 'leaderauto' ); ?></a>
		</p>
	</div>
	<div class="hero__media">
		<img src="<?php echo esc_url( leaderauto_img( 'detailing-06.png' ) ); ?>" alt="<?php esc_attr_e( 'Сервіс електромобілів LeaderAuto', 'leaderauto' ); ?>" loading="eager">
		<img src="<?php echo esc_url( leaderauto_img( 'detailing-07.png' ) ); ?>" alt="<?php esc_attr_e( 'Діагностика електромобіля', 'leaderauto' ); ?>" loading="lazy">
	</div>
</section>
