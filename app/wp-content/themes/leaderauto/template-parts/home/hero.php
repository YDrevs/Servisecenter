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
<section class="hero" id="hero" style="--hero-bg: url('<?php echo esc_url( leaderauto_img( 'detailing-05.jpg' ) ); ?>');">
	<div class="hero__inner">
		<h1 class="hero__title">BYD-Center<br>Service &amp; Dealer</h1>
	</div>

	<?php /* Reference row 2: the pitch sits on the blue overlay at the left, the
	         service line on a black wedge cutting in from the right. */ ?>
	<div class="hero__band">
		<div class="hero__band-inner">
			<div class="hero__pitch">
				<p class="hero__lead">
					<?php esc_html_e( 'Програмний та технічний ремонт електромобілів BYD, Tesla, Zeekr, Volkswagen, Nissan та інших марок.', 'leaderauto' ); ?>
				</p>
				<p class="hero__actions">
					<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Отримати консультацію', 'leaderauto' ); ?></a>
				</p>
			</div>

			<p class="hero__note">
				<?php esc_html_e( 'Діагностика, оновлення, кодування, заміна мастил, підбір та замовлення запчастин', 'leaderauto' ); ?>
			</p>
		</div>
	</div>
</section>
