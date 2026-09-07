<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$groups = array(
	array(
		'title' => __( 'Програмний ремонт та діагностика', 'leaderauto' ),
		'image' => 'detailing-21.jpg',
		'items' => array(
			__( 'Комп’ютерна діагностика', 'leaderauto' ),
			__( 'Оновлення програмного забезпечення', 'leaderauto' ),
			__( 'Кодування та адаптація блоків', 'leaderauto' ),
			__( 'Усунення помилок та збоїв', 'leaderauto' ),
			__( 'Налаштування електронних систем', 'leaderauto' ),
			__( 'Робота з батарейними системами', 'leaderauto' ),
		),
	),
	array(
		'title' => __( 'Технічне обслуговування', 'leaderauto' ),
		'image' => 'detailing-08.jpg',
		'items' => array(
			__( 'Заміна мастил та технічних рідин', 'leaderauto' ),
			__( 'Обслуговування редуктора', 'leaderauto' ),
			__( 'Заміна фільтрів', 'leaderauto' ),
			__( 'Перевірка ходової частини', 'leaderauto' ),
			__( 'Гальмівна система', 'leaderauto' ),
			__( 'Обслуговування систем охолодження', 'leaderauto' ),
		),
	),
	array(
		'title' => __( 'Запчастини та комплектуючі', 'leaderauto' ),
		'image' => 'detailing-10.jpg',
		'items' => array(
			__( 'Замовлення оригінальних запчастин', 'leaderauto' ),
			__( 'Аналоги перевірених брендів', 'leaderauto' ),
			__( 'Пошук рідкісних деталей', 'leaderauto' ),
			__( 'Допомога з підбором', 'leaderauto' ),
			__( 'Швидка доставка', 'leaderauto' ),
		),
	),
);
?>
<section class="section" id="services">
	<div class="section__inner">
		<h2 class="section__title"><?php esc_html_e( 'Наші послуги', 'leaderauto' ); ?></h2>
		<div class="cards cards--3">
			<?php foreach ( $groups as $group ) : ?>
				<article class="card">
					<img class="card__media" src="<?php echo esc_url( leaderauto_img( $group['image'] ) ); ?>" alt="<?php echo esc_attr( $group['title'] ); ?>" loading="lazy">
					<h3 class="card__title"><?php echo esc_html( $group['title'] ); ?></h3>
					<ul class="card__list">
						<?php foreach ( $group['items'] as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
