<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faq = array(
	array(
		'q' => __( 'Ви працюєте тільки з BYD?', 'leaderauto' ),
		'a' => __( 'Ні. BYD — наш профільний бренд, але ми обслуговуємо Tesla, Zeekr, Volkswagen ID, Nissan Leaf та інші електромобілі.', 'leaderauto' ),
	),
	array(
		'q' => __( 'Чи можна замовити запчастини у вас?', 'leaderauto' ),
		'a' => __( 'Так. Замовляємо оригінальні деталі та перевірені аналоги, допомагаємо з підбором і доставкою.', 'leaderauto' ),
	),
	array(
		'q' => __( 'Ви займаєтесь програмним ремонтом?', 'leaderauto' ),
		'a' => __( 'Так: діагностика, оновлення ПЗ, кодування та адаптація блоків, усунення помилок.', 'leaderauto' ),
	),
	array(
		'q' => __( 'Чи є гарантія?', 'leaderauto' ),
		'a' => __( 'На виконані роботи та встановлені запчастини надаємо гарантію; умови узгоджуємо перед ремонтом.', 'leaderauto' ),
	),
	array(
		'q' => __( 'Скільки часу займає діагностика?', 'leaderauto' ),
		'a' => __( 'Базова діагностика — зазвичай протягом дня. Складніші випадки узгоджуємо окремо.', 'leaderauto' ),
	),
);
?>
<section class="section section--dark" id="faq">
	<div class="section__inner">
		<?php /* Reference stacks oversized red F A Q behind the heading; aria-hidden
		         because it is decoration, not content a screen reader should read. */ ?>
		<div class="faq__head">
			<span class="faq__mark" aria-hidden="true">FAQ</span>
			<h2 class="section__title faq__title"><?php esc_html_e( 'Поширені запитання', 'leaderauto' ); ?></h2>
		</div>

		<div class="faq">
			<?php foreach ( $faq as $row ) : ?>
				<details class="faq__item">
					<summary class="faq__q"><?php echo esc_html( $row['q'] ); ?></summary>
					<div class="faq__a"><p><?php echo esc_html( $row['a'] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>

		<p class="faq__contacts">
			<?php printf(
				esc_html__( 'Швидке питання? Пишіть у Viber або Telegram: %s', 'leaderauto' ),
				esc_html( leaderauto_messenger() )
			); ?>
		</p>
	</div>
</section>
