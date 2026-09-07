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
		<?php /* Reference row 1: two stills on the left, the debut clip on the right. */ ?>
		<div class="services__intro">
			<img class="services__shot" src="<?php echo esc_url( leaderauto_img( 'detailing-06.png' ) ); ?>" alt="<?php esc_attr_e( 'Сервіс електромобілів LeaderAuto', 'leaderauto' ); ?>" loading="lazy">
			<div class="services__aside">
				<img src="<?php echo esc_url( leaderauto_img( 'detailing-07.png' ) ); ?>" alt="<?php esc_attr_e( 'Діагностика електромобіля', 'leaderauto' ); ?>" loading="lazy">
				<video class="video" controls preload="metadata" poster="<?php echo esc_url( leaderauto_img( 'byd.jpg' ) ); ?>">
					<source src="<?php echo esc_url( leaderauto_img( 'sea-lion-08-debut.mp4' ) ); ?>" type="video/mp4">
				</video>
			</div>
		</div>

		<h2 class="section__title section__title--center"><?php esc_html_e( 'Наші послуги', 'leaderauto' ); ?></h2>
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
