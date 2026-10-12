<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="section section--dark section--asphalt" id="loyalty">
	<div class="section__inner band">
		<div class="band__figure"><?php echo esc_html( leaderauto_field( 'home', 'loyalty_figure' ) ); ?></div>
		<div class="band__body">
			<h2 class="section__title"><?php echo esc_html( leaderauto_field( 'home', 'loyalty_title' ) ); ?></h2>
			<p><?php echo esc_html( leaderauto_field( 'home', 'loyalty_text' ) ); ?></p>
		</div>
		<img class="band__qr" src="<?php echo esc_url( leaderauto_field_img( 'home', 'loyalty_qr' ) ); ?>"
		     alt="<?php esc_attr_e( 'QR-код: скануй, щоб перейти на сайт', 'leaderauto' ); ?>"
		     width="3000" height="3889" loading="lazy">
	</div>
</section>
