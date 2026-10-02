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
<section class="section section--photo" id="autopilot" style="--sec-bg: url('<?php echo esc_url( leaderauto_img( 'Golovna/avtopilot.webp' ) ); ?>');">
	<div class="section__inner">
		<h2 class="section__title section__title--display"><?php esc_html_e( 'Налаштування автопілоту та автопаркування', 'leaderauto' ); ?></h2>
		<p><?php esc_html_e( 'Активація та калібрування асистентів водіння на підтримуваних електромобілях.', 'leaderauto' ); ?></p>
		<p class="section__actions">
			<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Записатися', 'leaderauto' ); ?> <span aria-hidden="true">→</span></a>
		</p>
	</div>
</section>
