<?php
/**
 * Template Name: Автодилер (Dealer)
 *
 * Reference page 15 was ~90% untranslated Divi "Car Detailing" demo (Lorem ipsum,
 * "Wheel Protection", "Quick Links", …) — none of that is carried over. This is a
 * minimal real page: BYD sales / test drive + real media + a CTA to the form.
 * TODO(content): real model list, prices, availability — pending client input.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$contacts = get_page_by_path( 'contacts' );
$cta_url  = $contacts ? get_permalink( $contacts ) : home_url( '/contacts/' );

/*
 * Six cards, mirroring the reference page's grid and image order. The reference
 * titles were untranslated Divi demo ("Full Detailing", "Wheel Protection", …)
 * with Lorem ipsum bodies; these are real Ukrainian steps of the dealer flow.
 * TODO(content): confirm wording, models and terms with the client.
 */
$offers = array(
	array( 'image' => '1w.jpg',           'title' => __( 'Підбір моделі', 'leaderauto' ),              'text' => __( 'Допомагаємо обрати електромобіль під ваш бюджет, пробіг і умови експлуатації.', 'leaderauto' ) ),
	array( 'image' => 'detailing-08.jpg', 'title' => __( 'Тест-драйв', 'leaderauto' ),                 'text' => __( 'Записуємо на тест-драйв, щоб ви відчули авто до покупки, а не з опису.', 'leaderauto' ) ),
	array( 'image' => 'detailing-21.jpg', 'title' => __( 'Оформлення', 'leaderauto' ),                 'text' => __( 'Супровід документів і реєстрації — без зайвих поїздок і посередників.', 'leaderauto' ) ),
	array( 'image' => 'detailing-18.png', 'title' => __( 'Передпродажна підготовка', 'leaderauto' ),   'text' => __( 'Повна діагностика, перевірка батареї та електроніки перед видачею.', 'leaderauto' ) ),
	array( 'image' => 'detailing-10.jpg', 'title' => __( 'Видача авто', 'leaderauto' ),                'text' => __( 'Показуємо, як користуватись зарядкою та системами авто, і відповідаємо на питання.', 'leaderauto' ) ),
	array( 'image' => 'detailing-17.png', 'title' => __( 'Сервіс після покупки', 'leaderauto' ),       'text' => __( 'Подальше обслуговування у тому ж місці, де купували — з історією вашого авто.', 'leaderauto' ) ),
);
?>
<article class="page-shell">
	<header class="page-shell__head page-shell__head--plain" style="--page-bg: url('<?php echo esc_url( leaderauto_img( 'byd.jpg' ) ); ?>');">
		<div class="page-shell__inner">
			<h1 class="page-shell__title"><?php esc_html_e( 'Автодилер', 'leaderauto' ); ?></h1>
			<p class="page-shell__lead"><?php esc_html_e( 'Обери свою серед наявних — офіційний підхід до продажу та тест-драйву електромобілів BYD.', 'leaderauto' ); ?></p>
		</div>
	</header>

	<section class="section">
		<div class="section__inner media-split">
			<div class="media-split__media">
				<img src="<?php echo esc_url( leaderauto_img( 'byd-1.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Електромобіль BYD', 'leaderauto' ); ?>" loading="lazy">
			</div>
			<div class="media-split__body">
				<h2 class="section__title"><?php esc_html_e( 'BYD у наявності та під замовлення', 'leaderauto' ); ?></h2>
				<p><?php esc_html_e( 'Підбір моделі під ваш бюджет і задачі, допомога з оформленням, підготовка авто перед видачею та подальше сервісне обслуговування в одному місці.', 'leaderauto' ); ?></p>
				<p class="section__actions">
					<a class="btn" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Записатись на тест-драйв', 'leaderauto' ); ?></a>
				</p>
			</div>
		</div>
	</section>

	<section class="section section--surface">
		<div class="section__inner">
			<h2 class="section__title"><?php esc_html_e( 'Як проходить купівля', 'leaderauto' ); ?></h2>
			<div class="cards cards--3">
				<?php foreach ( $offers as $offer ) : ?>
					<article class="card">
						<img class="card__media" src="<?php echo esc_url( leaderauto_img( $offer['image'] ) ); ?>" alt="<?php echo esc_attr( $offer['title'] ); ?>" loading="lazy">
						<h3 class="card__title"><?php echo esc_html( $offer['title'] ); ?></h3>
						<p><?php echo esc_html( $offer['text'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section--guarantee" style="--sec-bg: url('<?php echo esc_url( leaderauto_img( 'detailing-02.png' ) ); ?>');">
		<div class="section__inner media-split">
			<div class="media-split__media">
				<img src="<?php echo esc_url( leaderauto_img( 'detailing-19.png' ) ); ?>" alt="<?php esc_attr_e( 'Гарантія на роботи LeaderAuto', 'leaderauto' ); ?>" loading="lazy">
			</div>
			<div class="media-split__body">
				<h2 class="section__title"><?php esc_html_e( 'Відповідаємо за результат', 'leaderauto' ); ?></h2>
				<p><?php esc_html_e( 'Ми працюємо тільки з електромобілями, тому беремось за авто, яке справді розуміємо, і супроводжуємо його після продажу.', 'leaderauto' ); ?></p>
			</div>
		</div>
	</section>

	<section class="section section--surface">
		<div class="section__inner">
			<h2 class="section__title"><?php esc_html_e( 'BYD Sea Lion 08 — дебют', 'leaderauto' ); ?></h2>
			<video class="video" controls preload="metadata" poster="<?php echo esc_url( leaderauto_img( 'byd.jpg' ) ); ?>">
				<source src="<?php echo esc_url( leaderauto_img( 'sea-lion-08-debut.mp4' ) ); ?>" type="video/mp4">
			</video>
		</div>
	</section>

	<section class="section section--dark cta-band">
		<div class="section__inner">
			<h2 class="section__title"><?php esc_html_e( 'Цікавить конкретна модель?', 'leaderauto' ); ?></h2>
			<p class="cta-band__row">
				<a class="btn btn--light" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Залишити заявку', 'leaderauto' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_attr( leaderauto_phone_href() ); ?>"><?php echo esc_html( leaderauto_phone() ); ?></a>
			</p>
		</div>
	</section>
</article>
<?php
get_footer();
