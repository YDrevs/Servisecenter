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
		'image' => 'diagnostic-byd.webp',
		'items' => array(
			__( 'Комп’ютерна діагностика', 'leaderauto' ),
			__( 'Оновлення програмного забезпечення', 'leaderauto' ),
			__( 'Оновлення та адаптація блоків', 'leaderauto' ),
			__( 'Усунення помилок та програмних збоїв', 'leaderauto' ),
			__( 'Налаштування електронних систем безпеки', 'leaderauto' ),
			__( 'Послуги автоелектрика', 'leaderauto' ),
			__( 'Ремонт інверторів', 'leaderauto' ),
			__( 'Обслуговування та ремонт високовольтних батарей', 'leaderauto' ),
		),
	),
	array(
		'title' => __( 'Технічне обслуговування', 'leaderauto' ),
		'image' => 'tech-service.webp',
		'items' => array(
			__( 'Розвал-сходження для EV та HYBRID', 'leaderauto' ),
			__( 'Вібростенд та ремонт ходової', 'leaderauto' ),
			__( 'Заміна мастил та технічних рідин', 'leaderauto' ),
			__( 'Шиномонтаж', 'leaderauto' ),
			__( 'Заправка та ремонт кондиціонерів', 'leaderauto' ),
			__( 'Перевірка усіх систем автомобіля', 'leaderauto' ),
		),
	),
	array(
		'title' => __( 'Запчастини та комплектуючі', 'leaderauto' ),
		'image' => 'detailing-10.jpg',
		'items' => array(
			__( 'Підбір та продаж запчастин', 'leaderauto' ),
			__( 'Замовлення запчастин з Китаю', 'leaderauto' ),
			__( 'Доставка по Україні', 'leaderauto' ),
			__( 'Діючі програми лояльності', 'leaderauto' ),
		),
	),
	array(
		'title' => __( 'Додаткові послуги', 'leaderauto' ),
		'image' => 'detailing-08.jpg',
		'items' => array(
			__( 'Забір і доставка автомобіля від клієнта до СТО і назад', 'leaderauto' ),
			__( 'Евакуатор', 'leaderauto' ),
			__( 'Оренда підйомника або робочого місця для самостійного обслуговування', 'leaderauto' ),
			__( 'Зберігання коліс', 'leaderauto' ),
			__( 'Передпродажна перевірка', 'leaderauto' ),
			// An item may carry its own nested list; the template renders it one level deep.
			array(
				'label' => __( 'Післяпродажний сервіс:', 'leaderauto' ),
				'items' => array(
					__( 'Антикорозійна обробка', 'leaderauto' ),
					__( 'Тонування', 'leaderauto' ),
					__( 'Антихром обробка', 'leaderauto' ),
					__( 'Шумовіброізоляція', 'leaderauto' ),
				),
			),
		),
	),
);
?>
<section class="section" id="services">
	<div class="section__inner">
		<?php /* Reference row 1: two stills on the left, the debut clip on the right. */ ?>
		<div class="services__intro">
			<img class="services__shot" src="<?php echo esc_url( leaderauto_img( 'detailing-19.webp' ) ); ?>" alt="<?php esc_attr_e( 'Сервіс електромобілів LeaderAuto', 'leaderauto' ); ?>" loading="lazy">
			<img class="services__shot-over" src="<?php echo esc_url( leaderauto_img( 'detailing-07.png' ) ); ?>" alt="<?php esc_attr_e( 'Діагностика електромобіля', 'leaderauto' ); ?>" loading="lazy">
			<div class="services__aside">
				<?php /* Portrait 720x1280 clip — no poster, the first frame stands in. */ ?>
				<video class="video video--portrait" controls preload="metadata">
					<source src="<?php echo esc_url( leaderauto_img( 'r2d2.mp4' ) ); ?>" type="video/mp4">
				</video>
			</div>
		</div>

		<h2 class="section__title section__title--center"><?php esc_html_e( 'Наші послуги', 'leaderauto' ); ?></h2>
		<div class="cards cards--4 cards--wide">
			<?php foreach ( $groups as $group ) : ?>
				<article class="card">
					<img class="card__media" src="<?php echo esc_url( leaderauto_img( $group['image'] ) ); ?>" alt="<?php echo esc_attr( $group['title'] ); ?>" loading="lazy">
					<h3 class="card__title"><?php echo esc_html( $group['title'] ); ?></h3>
					<ul class="card__list">
						<?php foreach ( $group['items'] as $item ) : ?>
							<?php if ( is_array( $item ) ) : ?>
								<?php /* The label heads the nested list; only the services under it link. */ ?>
								<li>
									<?php echo esc_html( $item['label'] ); ?>
									<ul class="card__sublist">
										<?php foreach ( $item['items'] as $sub ) : ?>
											<li><?php leaderauto_service_link( $sub ); ?></li>
										<?php endforeach; ?>
									</ul>
								</li>
							<?php else : ?>
								<li><?php leaderauto_service_link( $item ); ?></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</article>
			<?php endforeach; ?>
		</div>
	</div>

	<?php /* Opened by assets/src/js/service-modal.js; the links work without it. */ ?>
	<dialog class="service-modal" id="service-modal" aria-labelledby="service-modal-title">
		<form method="dialog" class="service-modal__dismiss">
			<button class="service-modal__close" value="close" aria-label="<?php esc_attr_e( 'Закрити', 'leaderauto' ); ?>">&times;</button>
		</form>
		<h2 class="service-modal__title" id="service-modal-title"><?php esc_html_e( 'Залишити заявку', 'leaderauto' ); ?></h2>
		<?php get_template_part( 'template-parts/contact-form', null, array( 'id' => 'leaderauto-contact-form-modal' ) ); ?>
	</dialog>
</section>
