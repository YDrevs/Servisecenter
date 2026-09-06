<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reasons = array(
	__( 'Спеціалізація на електромобілях', 'leaderauto' ),
	__( 'Досвід роботи з BYD', 'leaderauto' ),
	__( 'Сучасна діагностика', 'leaderauto' ),
	__( 'Прозорий підхід', 'leaderauto' ),
	__( 'Допомога із запчастинами', 'leaderauto' ),
);

$contacts = get_page_by_path( 'contacts' );
$cta_url  = $contacts ? get_permalink( $contacts ) : home_url( '/contacts/' );
?>
<section class="section section--dark" id="why">
	<div class="section__inner">
		<p class="section__kicker"><?php esc_html_e( '100% вирішення проблем', 'leaderauto' ); ?></p>
		<h2 class="section__title"><?php esc_html_e( 'Чому власники EV звертаються до нас', 'leaderauto' ); ?></h2>
		<ul class="ticks">
			<?php foreach ( $reasons as $reason ) : ?>
				<li><?php echo esc_html( $reason ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p class="section__actions">
			<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Записатися', 'leaderauto' ); ?></a>
		</p>
	</div>
</section>
