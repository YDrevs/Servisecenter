<?php
/**
 * Template Name: Про нас (About)
 *
 * Mirrors reference page 17. Stat labels/numbers below are placeholders — the
 * originals ("Happy Clients / Towing Services / Projects Done") were Divi demo text.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$steps = array(
	__( 'Заявка або дзвінок', 'leaderauto' ),
	__( 'Діагностика', 'leaderauto' ),
	__( 'Узгодження', 'leaderauto' ),
	__( 'Ремонт', 'leaderauto' ),
	__( 'Видача авто', 'leaderauto' ),
);

$stats = array(
	array( 'n' => '500+', 'label' => __( 'Обслугованих електромобілів', 'leaderauto' ) ),
	array( 'n' => '5',    'label' => __( 'Років у сервісі EV', 'leaderauto' ) ),
	array( 'n' => 'BYD',  'label' => __( 'Профільний бренд', 'leaderauto' ) ),
);

$contacts = get_page_by_path( 'contacts' );
$cta_url  = $contacts ? get_permalink( $contacts ) : home_url( '/contacts/' );
?>
<article class="page-shell">
	<header class="page-shell__head">
		<div class="page-shell__inner">
			<h1 class="page-shell__title"><?php esc_html_e( 'Про нас', 'leaderauto' ); ?></h1>
			<p class="page-shell__lead"><?php esc_html_e( 'Експертний сервіс для сучасних електромобілів.', 'leaderauto' ); ?></p>
		</div>
	</header>

	<section class="section">
		<div class="section__inner">
			<p>
				<?php esc_html_e( 'Ми спеціалізуємось на обслуговуванні та ремонті електромобілів, з акцентом на BYD та інші сучасні EV-платформи. Працюємо як з технічною частиною, так і з програмною — від діагностики до кодування блоків.', 'leaderauto' ); ?>
			</p>
			<div class="stats">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="stats__item">
						<span class="stats__n"><?php echo esc_html( $stat['n'] ); ?></span>
						<span class="stats__label"><?php echo esc_html( $stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section--surface">
		<div class="section__inner">
			<h2 class="section__title"><?php esc_html_e( 'Як ми працюємо', 'leaderauto' ); ?></h2>
			<ol class="steps">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="steps__item">
						<span class="steps__n"><?php echo esc_html( $i + 1 ); ?></span>
						<span class="steps__label"><?php echo esc_html( $step ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="section">
		<div class="section__inner media-pair">
			<img src="<?php echo esc_url( leaderauto_img( 'detailing-14.png' ) ); ?>" alt="<?php esc_attr_e( 'Робота з електромобілем у сервісі', 'leaderauto' ); ?>" loading="lazy">
			<img src="<?php echo esc_url( leaderauto_img( 'detailing-15.png' ) ); ?>" alt="<?php esc_attr_e( 'Обладнання сервісу LeaderAuto', 'leaderauto' ); ?>" loading="lazy">
		</div>
	</section>

	<section class="section section--dark cta-band">
		<div class="section__inner">
			<h2 class="section__title"><?php esc_html_e( 'Потрібна діагностика або ремонт електромобіля?', 'leaderauto' ); ?></h2>
			<p class="cta-band__row">
				<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Залишити заявку', 'leaderauto' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_attr( leaderauto_phone_href() ); ?>"><?php echo esc_html( leaderauto_phone() ); ?></a>
			</p>
		</div>
	</section>
</article>
<?php
get_footer();
