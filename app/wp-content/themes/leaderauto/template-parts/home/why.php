<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reasons = leaderauto_lines( 'home', 'why_list' );

$contacts = get_page_by_path( 'contacts' );
$cta_url  = $contacts ? get_permalink( $contacts ) : home_url( '/contacts/' );
?>
<section class="section section--why" id="why" style="--sec-bg: url('<?php echo esc_url( leaderauto_field_img( 'home', 'why_bg' ) ); ?>');">
	<div class="section__inner">
		<p class="section__kicker"><span class="why__arrow" aria-hidden="true"></span><?php echo esc_html( leaderauto_field( 'home', 'why_kicker' ) ); ?></p>
		<h2 class="section__title"><?php echo esc_html( leaderauto_field( 'home', 'why_title' ) ); ?></h2>
		<ul class="ticks">
			<?php foreach ( $reasons as $reason ) : ?>
				<li><?php echo esc_html( $reason ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p class="section__actions">
			<a class="btn" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( leaderauto_field( 'home', 'why_button' ) ); ?> <span aria-hidden="true">→</span></a>
		</p>
	</div>
</section>
