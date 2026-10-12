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
<section class="section section--photo" id="autopilot" style="--sec-bg: url('<?php echo esc_url( leaderauto_field_img( 'home', 'autopilot_bg' ) ); ?>');">
	<div class="section__inner">
		<h2 class="section__title section__title--display"><?php echo esc_html( leaderauto_field( 'home', 'autopilot_title' ) ); ?></h2>
		<p><?php echo esc_html( leaderauto_field( 'home', 'autopilot_text' ) ); ?></p>
		<p class="section__actions">
			<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( leaderauto_field( 'home', 'autopilot_button' ) ); ?> <span aria-hidden="true">→</span></a>
		</p>
	</div>
</section>
