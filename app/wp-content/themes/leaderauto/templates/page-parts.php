<?php
/**
 * Template Name: Запчастини (Parts)
 *
 * Built from the client's prototype in "Planned work/LeaderAuto_Parts_Green_Integration",
 * restyled onto the theme's own tokens and components. No shop: no prices, cart or
 * stock — every control on the page just prefills the request form (assets/src/js/parts.js).
 *
 * The form posts to the same endpoint as Contacts (POST /wp-json/leaderauto/v1/contact).
 * Fields the endpoint has no param for (VIN, category, part number, what's needed) carry
 * data-message-label and are folded into `message` by form.js.
 * TODO(form): photo upload from the prototype needs its own multipart endpoint — not wired yet.
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$categories = array(
	array( 'icon' => '◈', 'title' => __( 'Кузов та оптика', 'leaderauto' ),   'text' => __( 'Фари, бампери, крила, дзеркала', 'leaderauto' ) ),
	array( 'icon' => '⌁', 'title' => __( 'Ходова частина', 'leaderauto' ),    'text' => __( 'Важелі, амортизатори, ступиці', 'leaderauto' ) ),
	array( 'icon' => '◎', 'title' => __( 'Гальмівна система', 'leaderauto' ), 'text' => __( 'Диски, колодки, супорти', 'leaderauto' ) ),
	array( 'icon' => '⌘', 'title' => __( 'Електроніка', 'leaderauto' ),       'text' => __( 'Блоки, датчики, модулі', 'leaderauto' ) ),
	array( 'icon' => '▣', 'title' => __( 'Батарея та HV', 'leaderauto' ),     'text' => __( 'Високовольтні компоненти', 'leaderauto' ) ),
	array( 'icon' => 'ϟ', 'title' => __( 'Зарядна система', 'leaderauto' ),   'text' => __( 'Порти, модулі, кабелі', 'leaderauto' ) ),
	array( 'icon' => '◇', 'title' => __( 'Салон', 'leaderauto' ),             'text' => __( 'Дисплеї, пластик, кнопки', 'leaderauto' ) ),
	array( 'icon' => '✦', 'title' => __( 'ТО та витратники', 'leaderauto' ),  'text' => __( 'Фільтри, рідини, щітки', 'leaderauto' ) ),
);

// Label => value written into the form's vehicle field.
$models = array(
	'Song Plus' => 'BYD Song Plus',
	'Seal'      => 'BYD Seal',
	'Sealion 7' => 'BYD Sealion 7',
	'Seagull'   => 'BYD Seagull',
	'Han'       => 'BYD Han',
	'Tang'      => 'BYD Tang',
	'Leopard 5' => 'Leopard 5',
);

// TODO(content): real photos per part once the client supplies them.
$popular = array(
	array( 'icon' => '◫', 'kicker' => __( 'Кузов та оптика', 'leaderauto' ), 'title' => __( 'Передня оптика BYD', 'leaderauto' ),   'part' => __( 'Передня оптика BYD', 'leaderauto' ) ),
	array( 'icon' => '◎', 'kicker' => __( 'Гальма', 'leaderauto' ),          'title' => __( 'Гальмівні колодки', 'leaderauto' ),    'part' => __( 'Гальмівні колодки', 'leaderauto' ) ),
	array( 'icon' => 'ϟ', 'kicker' => __( 'Зарядна система', 'leaderauto' ), 'title' => __( 'Порти та модулі', 'leaderauto' ),      'part' => __( 'Порт або зарядний модуль', 'leaderauto' ) ),
	array( 'icon' => '⌁', 'kicker' => __( 'Ходова', 'leaderauto' ),          'title' => __( 'Підвіска та ступиці', 'leaderauto' ), 'part' => __( 'Запчастини ходової частини', 'leaderauto' ) ),
);

$steps = array(
	__( 'Підбір за VIN', 'leaderauto' ),
	__( 'Перевірка сумісності', 'leaderauto' ),
	__( 'Встановлення', 'leaderauto' ),
	__( 'Діагностика', 'leaderauto' ),
);

$faq = array(
	array(
		'q' => __( 'Чи обов’язково знати номер запчастини?', 'leaderauto' ),
		'a' => __( 'Ні. Для підбору можна використати VIN, модель авто, опис або фото.', 'leaderauto' ),
	),
	array(
		'q' => __( 'Чи можна одразу встановити деталь?', 'leaderauto' ),
		'a' => __( 'Так, встановлення та діагностику можна погодити із сервісним центром.', 'leaderauto' ),
	),
);

$form_id = 'leaderauto-parts-form';
?>
<article class="page-shell page-shell--parts">
	<header class="page-shell__head" style="--page-bg: url('<?php echo esc_url( leaderauto_img( 'tech-service.webp' ) ); ?>');">
		<div class="page-shell__inner">
			<p class="section__kicker"><?php esc_html_e( 'Запчастини для EV', 'leaderauto' ); ?></p>
			<h1 class="page-shell__title"><?php esc_html_e( 'Запчастини для BYD та електромобілів', 'leaderauto' ); ?></h1>
			<p class="page-shell__lead"><?php esc_html_e( 'Оригінальні та перевірені запчастини для BYD, Tesla, Zeekr та інших EV. Підберемо за VIN, перевіримо сумісність та допоможемо з установкою.', 'leaderauto' ); ?></p>
			<p class="cta-band__row">
				<a class="btn btn--light" href="#request"><?php esc_html_e( 'Підібрати запчастину', 'leaderauto' ); ?></a>
				<a class="btn btn--ghost" href="#request" data-parts-vin><?php esc_html_e( 'Знайти за VIN', 'leaderauto' ); ?></a>
			</p>
		</div>
	</header>

	<section class="section" id="categories">
		<div class="section__inner">
			<p class="section__kicker parts__kicker"><?php esc_html_e( 'Категорії', 'leaderauto' ); ?></p>
			<h2 class="section__title"><?php esc_html_e( 'Що вам потрібно?', 'leaderauto' ); ?></h2>
			<p class="parts__lead"><?php esc_html_e( 'Оберіть категорію — вона автоматично підставиться у заявку.', 'leaderauto' ); ?></p>
			<div class="cards cards--4">
				<?php foreach ( $categories as $cat ) : ?>
					<button class="parts-cat" type="button" data-parts-category="<?php echo esc_attr( $cat['title'] ); ?>">
						<span class="parts-cat__icon" aria-hidden="true"><?php echo esc_html( $cat['icon'] ); ?></span>
						<span class="parts-cat__title"><?php echo esc_html( $cat['title'] ); ?></span>
						<span class="parts-cat__text"><?php echo esc_html( $cat['text'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section--surface" id="models">
		<div class="section__inner">
			<p class="section__kicker parts__kicker"><?php esc_html_e( 'Підбір за авто', 'leaderauto' ); ?></p>
			<h2 class="section__title"><?php esc_html_e( 'Оберіть автомобіль', 'leaderauto' ); ?></h2>
			<div class="parts-models">
				<?php foreach ( $models as $label => $value ) : ?>
					<button class="parts-models__chip" type="button" data-parts-model="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section" id="popular">
		<div class="section__inner">
			<p class="section__kicker parts__kicker"><?php esc_html_e( 'Популярні запити', 'leaderauto' ); ?></p>
			<h2 class="section__title"><?php esc_html_e( 'Часто шукають', 'leaderauto' ); ?></h2>
			<div class="cards cards--4">
				<?php foreach ( $popular as $item ) : ?>
					<article class="card parts-item">
						<div class="parts-item__pic" aria-hidden="true"><?php echo esc_html( $item['icon'] ); ?></div>
						<p class="parts-item__kicker"><?php echo esc_html( $item['kicker'] ); ?></p>
						<h3 class="parts-item__title"><?php echo esc_html( $item['title'] ); ?></h3>
						<button class="parts-item__ask" type="button" data-parts-part="<?php echo esc_attr( $item['part'] ); ?>"><?php esc_html_e( 'Запитати наявність →', 'leaderauto' ); ?></button>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section section--dark" id="install">
		<div class="section__inner media-split">
			<div>
				<p class="section__kicker"><?php esc_html_e( 'Запчастини + сервіс', 'leaderauto' ); ?></p>
				<h2 class="section__title"><?php esc_html_e( 'Не просто продаємо — можемо одразу встановити', 'leaderauto' ); ?></h2>
				<p><?php esc_html_e( 'Підберемо сумісну запчастину, встановимо її у сервісному центрі та перевіримо роботу систем автомобіля.', 'leaderauto' ); ?></p>
				<p class="section__actions"><a class="btn btn--light" href="#request"><?php esc_html_e( 'Підібрати запчастину', 'leaderauto' ); ?></a></p>
			</div>
			<ol class="steps parts-steps">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="steps__item"><span class="steps__n"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><?php echo esc_html( $step ); ?></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section class="section section--surface" id="request">
		<div class="section__inner parts-request">
			<div>
				<p class="section__kicker parts__kicker"><?php esc_html_e( 'Заявка', 'leaderauto' ); ?></p>
				<h2 class="section__title"><?php esc_html_e( 'Знайдемо потрібну деталь', 'leaderauto' ); ?></h2>
				<p class="parts__lead"><?php esc_html_e( 'Вкажіть автомобіль та VIN, опишіть деталь. Менеджер перевірить сумісність, наявність і вартість.', 'leaderauto' ); ?></p>
				<ul class="parts-types">
					<li><strong>Original</strong><?php esc_html_e( 'Оригінальні компоненти', 'leaderauto' ); ?></li>
					<li><strong>OEM</strong><?php esc_html_e( 'Перевірені постачальники', 'leaderauto' ); ?></li>
					<li><strong><?php esc_html_e( 'Аналог', 'leaderauto' ); ?></strong><?php esc_html_e( 'Перевірені альтернативи', 'leaderauto' ); ?></li>
				</ul>
			</div>

			<form class="contact-form parts-form" id="<?php echo esc_attr( $form_id ); ?>" novalidate>
				<div class="parts-form__grid">
					<p class="contact-form__field">
						<label for="<?php echo esc_attr( $form_id ); ?>-car"><?php esc_html_e( 'Автомобіль', 'leaderauto' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $form_id ); ?>-car" name="car" placeholder="BYD Sealion 7">
					</p>
					<p class="contact-form__field">
						<label for="<?php echo esc_attr( $form_id ); ?>-vin">VIN</label>
						<input type="text" id="<?php echo esc_attr( $form_id ); ?>-vin" data-message-label="VIN" placeholder="<?php esc_attr_e( 'VIN-код', 'leaderauto' ); ?>" autocapitalize="characters" spellcheck="false">
					</p>
					<p class="contact-form__field">
						<label for="<?php echo esc_attr( $form_id ); ?>-category"><?php esc_html_e( 'Категорія', 'leaderauto' ); ?></label>
						<select id="<?php echo esc_attr( $form_id ); ?>-category" data-message-label="<?php esc_attr_e( 'Категорія', 'leaderauto' ); ?>">
							<option value=""><?php esc_html_e( 'Оберіть', 'leaderauto' ); ?></option>
							<?php foreach ( $categories as $cat ) : ?>
								<option><?php echo esc_html( $cat['title'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p class="contact-form__field">
						<label for="<?php echo esc_attr( $form_id ); ?>-number"><?php esc_html_e( 'Номер запчастини', 'leaderauto' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $form_id ); ?>-number" data-message-label="<?php esc_attr_e( 'Номер запчастини', 'leaderauto' ); ?>" placeholder="<?php esc_attr_e( 'Необов’язково', 'leaderauto' ); ?>">
					</p>
					<p class="contact-form__field parts-form__full">
						<label for="<?php echo esc_attr( $form_id ); ?>-message"><?php esc_html_e( 'Що потрібно?', 'leaderauto' ); ?></label>
						<textarea id="<?php echo esc_attr( $form_id ); ?>-message" name="message" rows="4"></textarea>
					</p>
					<p class="contact-form__field">
						<label for="<?php echo esc_attr( $form_id ); ?>-name"><?php esc_html_e( 'Ваше ім’я', 'leaderauto' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $form_id ); ?>-name" name="name" autocomplete="name" required>
					</p>
					<p class="contact-form__field">
						<label for="<?php echo esc_attr( $form_id ); ?>-phone"><?php esc_html_e( 'Ваш телефон', 'leaderauto' ); ?></label>
						<input type="tel" id="<?php echo esc_attr( $form_id ); ?>-phone" name="phone" autocomplete="tel" placeholder="+380" required>
					</p>
				</div>
				<p class="contact-form__field contact-form__hp" aria-hidden="true">
					<label for="<?php echo esc_attr( $form_id ); ?>-website"><?php esc_html_e( 'Не заповнюйте це поле', 'leaderauto' ); ?></label>
					<input type="text" id="<?php echo esc_attr( $form_id ); ?>-website" name="website" tabindex="-1" autocomplete="off">
				</p>
				<p class="contact-form__actions">
					<button type="submit" class="btn parts-form__submit"><?php esc_html_e( 'Надіслати запит', 'leaderauto' ); ?></button>
				</p>
				<p class="contact-form__status" role="status" aria-live="polite" hidden></p>
			</form>
		</div>
	</section>

	<section class="section" id="faq">
		<div class="section__inner">
			<p class="section__kicker parts__kicker">FAQ</p>
			<h2 class="section__title"><?php esc_html_e( 'Часті запитання', 'leaderauto' ); ?></h2>
			<div class="faq">
				<?php foreach ( $faq as $row ) : ?>
					<details class="faq__item">
						<summary class="faq__q"><?php echo esc_html( $row['q'] ); ?></summary>
						<div class="faq__a"><p><?php echo esc_html( $row['a'] ); ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</article>
<?php
get_footer();
