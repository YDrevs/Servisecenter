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
<section class="section section--dark cta-band" id="contact">
	<div class="section__inner">
		<h2 class="section__title"><?php esc_html_e( 'Потрібна діагностика або ремонт електромобіля?', 'leaderauto' ); ?></h2>
		<p class="cta-band__row">
			<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Залишити заявку', 'leaderauto' ); ?></a>
			<a class="btn btn--ghost" href="<?php echo esc_attr( leaderauto_phone_href() ); ?>"><?php echo esc_html( leaderauto_phone() ); ?></a>
		</p>
	</div>
</section>
