<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faq = leaderauto_rows( 'home', 'faq_items' );
?>
<section class="section section--faq" id="faq" style="--sec-bg: url('<?php echo esc_url( leaderauto_field_img( 'home', 'faq_bg' ) ); ?>');">
	<div class="section__inner">
		<h2 class="section__title"><?php echo esc_html( leaderauto_field( 'home', 'faq_title' ) ); ?></h2>

		<div class="faq">
			<?php foreach ( $faq as $row ) : ?>
				<details class="faq__item">
					<summary class="faq__q"><?php echo esc_html( $row['q'] ); ?></summary>
					<div class="faq__a"><p><?php echo esc_html( $row['a'] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>

		<p class="faq__contacts">
			<?php esc_html_e( 'Швидке питання?', 'leaderauto' ); ?>
			<?php printf( esc_html__( 'Viber: %s', 'leaderauto' ), esc_html( leaderauto_viber() ) ); ?>
			<span aria-hidden="true">·</span>
			<?php printf(
				'Telegram: <a href="%1$s">%2$s</a>',
				esc_url( leaderauto_telegram_href() ),
				esc_html( leaderauto_telegram() )
			); ?>
		</p>
	</div>
</section>
