<?php
/**
 * Template Name: Про нас (About)
 *
 * Mirrors reference page 17. The originals' stat labels ("Happy Clients / Towing
 * Services / Projects Done") were Divi demo text.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$steps = leaderauto_lines( 'about', 'steps' );

/*
 * Defaults are the values as they settle on the reference (100% / 24/7 / 275+);
 * the written spec's 78 / 19 / 213 were read mid-animation. 'from' opts a tile
 * into the count-up. Editable in wp-admin → Контент сайту → Про нас.
 */
$stats = leaderauto_rows( 'about', 'stats' );

$contacts = get_page_by_path( 'contacts' );
$cta_url  = $contacts ? get_permalink( $contacts ) : home_url( '/contacts/' );
?>
<article class="page-shell">
	<header class="page-shell__head" style="--page-bg: url('<?php echo esc_url( leaderauto_field_img( 'about', 'head_bg' ) ); ?>');">
		<div class="page-shell__inner">
			<h1 class="page-shell__title"><?php echo esc_html( leaderauto_field( 'about', 'head_title' ) ); ?></h1>
			<p class="page-shell__lead"><?php echo esc_html( leaderauto_field( 'about', 'head_lead' ) ); ?></p>
		</div>
	</header>

	<section class="section section--wedge-tr">
		<div class="section__inner">
			<p>
				<?php echo esc_html( leaderauto_field( 'about', 'intro' ) ); ?>
			</p>
			<div class="stats">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="stats__item">
						<?php /* 'from' opts the tile into the count-up; 24/7 is not a quantity. */ ?>
						<span class="stats__n"<?php echo '' !== ( $stat['from'] ?? '' ) ? ' data-count-from="' . esc_attr( (string) $stat['from'] ) . '"' : ''; ?>><?php echo esc_html( $stat['n'] ); ?></span>
						<span class="stats__label"><?php echo esc_html( $stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section--dark section--divided">
		<div class="section__inner">
			<h2 class="section__title"><?php echo esc_html( leaderauto_field( 'about', 'steps_title' ) ); ?></h2>
			<ol class="steps">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="steps__item">
						<span class="steps__n"><?php echo esc_html( $i + 1 ); ?></span>
						<span class="steps__label"><?php echo esc_html( $step ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
		<?php /* The reference's one real Divi shape divider: a 140px white triangle
		         closing the section into the white block below. Decorative. */ ?>
		<div class="section__divider" aria-hidden="true">
			<svg viewBox="0 0 1280 140" preserveAspectRatio="none" focusable="false">
				<path d="M0 0v140h1280L0 0z"></path>
			</svg>
		</div>
	</section>

	<section class="section">
		<div class="section__inner media-pair">
			<img src="<?php echo esc_url( leaderauto_field_img( 'about', 'image_1' ) ); ?>" alt="<?php esc_attr_e( 'Робота з електромобілем у сервісі', 'leaderauto' ); ?>" loading="lazy">
			<img src="<?php echo esc_url( leaderauto_field_img( 'about', 'image_2' ) ); ?>" alt="<?php esc_attr_e( 'Обладнання сервісу LeaderAuto', 'leaderauto' ); ?>" loading="lazy">
		</div>
	</section>

	<section class="section section--dark cta-band cta-band--photo" style="--sec-bg: url('<?php echo esc_url( leaderauto_field_img( 'about', 'cta_bg' ) ); ?>');">
		<div class="section__inner">
			<h2 class="section__title"><?php echo esc_html( leaderauto_field( 'about', 'cta_title' ) ); ?></h2>
			<p class="cta-band__row">
				<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( leaderauto_field( 'about', 'cta_button' ) ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_attr( leaderauto_phone_href() ); ?>"><?php echo esc_html( leaderauto_phone() ); ?></a>
			</p>
		</div>
	</section>
</article>
<?php
get_footer();
