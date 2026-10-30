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
		<?php get_template_part( 'template-parts/qr-card', null, array( 'url' => leaderauto_field( 'home', 'loyalty_qr_url' ), 'class' => 'band__qr' ) ); ?>
	</div>
</section>
