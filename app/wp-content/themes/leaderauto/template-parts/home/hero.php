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
<section class="hero" id="hero" style="--hero-bg: url('<?php echo esc_url( leaderauto_img( 'main-header-banner.webp' ) ); ?>');">
	<div class="hero__inner">
		<h1 class="hero__title">BYD-Center<br><span class="hero__title-line">Продаж<span class="hero__dot"></span> Сервіс<span class="hero__dot"></span> Запчастини</span></h1>

		<p class="hero__lead">
			<?php esc_html_e( 'Ремонт та обслуговування усіх типів електромобілів і гібридів.', 'leaderauto' ); ?>
		</p>
		<p class="hero__actions">
			<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Отримати консультацію', 'leaderauto' ); ?> <span aria-hidden="true">→</span></a>
		</p>
	</div>

	<?php /* The service line on a dark wedge cutting in from the right, its diagonal
	         edged with a brand-green stripe. From 900px up it overlays the photo's
	         bottom edge, level with the CTA. */ ?>
	<div class="hero__band">
		<p class="hero__note">
			<?php esc_html_e( 'Діагностика • Оновлення блоків • ТО • Підбір та замовлення запчастин • Офіційне обладнання', 'leaderauto' ); ?>
		</p>
	</div>
</section>
