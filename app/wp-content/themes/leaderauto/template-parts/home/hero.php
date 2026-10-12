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
<section class="hero" id="hero" style="--hero-bg: url('<?php echo esc_url( leaderauto_field_img( 'home', 'hero_bg' ) ); ?>');">
	<div class="hero__inner">
		<h1 class="hero__title"><?php echo esc_html( leaderauto_field( 'home', 'hero_title' ) ); ?><br><span class="hero__title-line"><?php echo implode( '<span class="hero__dot"></span> ', array_map( 'esc_html', array_map( 'trim', explode( '•', leaderauto_field( 'home', 'hero_tagline' ) ) ) ) ); ?></span></h1>

		<p class="hero__lead">
			<?php echo esc_html( leaderauto_field( 'home', 'hero_lead' ) ); ?>
		</p>
		<p class="hero__actions">
			<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( leaderauto_field( 'home', 'hero_button' ) ); ?> <span aria-hidden="true">→</span></a>
		</p>
	</div>

	<?php /* The service line on a dark wedge cutting in from the right, its diagonal
	         edged with a brand-green stripe. From 900px up it overlays the photo's
	         bottom edge, level with the CTA. */ ?>
	<div class="hero__band">
		<p class="hero__note">
			<?php echo esc_html( leaderauto_field( 'home', 'hero_note' ) ); ?>
		</p>
	</div>
</section>
