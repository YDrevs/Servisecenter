<?php
/**
 * Editable site content: which fields exist on which admin screen, and their
 * defaults. Defaults are the copy the theme shipped with, so an untouched site
 * renders exactly as before.
 *
 * Shape: page slug => [ 'title', 'sections' => [ [ 'title', 'fields' => [ key => field ] ] ] ].
 * Field: [ 'type' => text|textarea|url|image, 'label', 'default', 'help'? ].
 * An image saves an attachment ID; its default is a path under the theme's assets/images/.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string,string>
 */
function field( string $type, string $label, string $default, string $help = '' ): array {
	return array_filter( compact( 'type', 'label', 'default', 'help' ), 'strlen' ) + array( 'default' => '' );
}

/**
 * @return array<string,array{title:string,sections:array<int,array{title:string,fields:array<string,array<string,string>>}>}>
 */
function schema(): array {
	static $schema = null;
	if ( null === $schema ) {
		$schema = build_schema();
	}
	return $schema;
}

/**
 * @return array<string,array{title:string,sections:array<int,array{title:string,fields:array<string,array<string,string>>}>}>
 */
function build_schema(): array {
	$bg     = __( 'Фонове зображення', 'leaderauto-core' );
	$title  = __( 'Заголовок', 'leaderauto-core' );
	$lead   = __( 'Підзаголовок', 'leaderauto-core' );
	$text   = __( 'Текст', 'leaderauto-core' );
	$kicker = __( 'Надзаголовок', 'leaderauto-core' );
	$button = __( 'Текст кнопки', 'leaderauto-core' );

	return array(
		'contacts'      => array(
			'title'    => __( 'Контакти', 'leaderauto-core' ),
			'sections' => array(
				array(
					'title'  => __( 'Телефони', 'leaderauto-core' ),
					'fields' => array(
						'phone'     => field( 'text', __( 'Основний телефон', 'leaderauto-core' ), '+38 (098) 089-90-90', __( 'Показується в шапці, футері та на кнопках «Подзвонити». Пишіть як завгодно — посилання для дзвінка складеться з цифр.', 'leaderauto-core' ) ),
						'phone_alt' => field( 'text', __( 'Другий телефон', 'leaderauto-core' ), '+380 (95) 066 29 21' ),
					),
				),
				array(
					'title'  => __( 'Месенджери та соцмережі', 'leaderauto-core' ),
					'fields' => array(
						'viber'     => field( 'text', __( 'Viber (номер)', 'leaderauto-core' ), '(095) 066 29 21' ),
						'telegram'  => field( 'text', __( 'Telegram', 'leaderauto-core' ), '@V_P_97', __( 'Ім’я користувача, наприклад @V_P_97.', 'leaderauto-core' ) ),
						'instagram' => field( 'url', __( 'Instagram (посилання)', 'leaderauto-core' ), 'https://www.instagram.com/lider_avto_/' ),
					),
				),
				array(
					'title'  => __( 'Адреса', 'leaderauto-core' ),
					'fields' => array(
						'address' => field( 'text', __( 'Адреса', 'leaderauto-core' ), 'вул. Коломийська, 1 «З», Чернівці, 58007' ),
					),
				),
			),
		),

		'home'          => array(
			'title'    => __( 'Головна', 'leaderauto-core' ),
			'sections' => array(
				array(
					'title'  => __( 'Головний банер', 'leaderauto-core' ),
					'fields' => array(
						'hero_bg'      => field( 'image', $bg, 'main-header-banner.webp' ),
						'hero_title'   => field( 'text', $title, 'BYD-Center' ),
						'hero_tagline' => field( 'text', __( 'Рядок під заголовком', 'leaderauto-core' ), 'Продаж • Сервіс • Запчастини', __( 'Пункти розділяйте знаком • — між ними з’являться крапки.', 'leaderauto-core' ) ),
						'hero_lead'    => field( 'textarea', $lead, 'Ремонт та обслуговування усіх типів електромобілів і гібридів.' ),
						'hero_button'  => field( 'text', $button, 'Отримати консультацію' ),
						'hero_note'    => field( 'textarea', __( 'Смуга з послугами', 'leaderauto-core' ), 'Діагностика • Оновлення блоків • ТО • Підбір та замовлення запчастин • Офіційне обладнання' ),
					),
				),
				array(
					'title'  => __( 'Наші послуги', 'leaderauto-core' ),
					'fields' => array(
						'services_shot'      => field( 'image', __( 'Фото зліва', 'leaderauto-core' ), 'detailing-19.webp' ),
						'services_shot_over' => field( 'image', __( 'Фото авто поверх нього', 'leaderauto-core' ), 'services-byd.webp' ),
						'services_title'     => field( 'text', $title, 'Наші послуги' ),
					),
				),
				array(
					'title'  => __( 'Чому обирають нас', 'leaderauto-core' ),
					'fields' => array(
						'why_bg'     => field( 'image', $bg, 'Golovna/faq.webp' ),
						'why_kicker' => field( 'text', $kicker, '100% вирішення проблем' ),
						'why_title'  => field( 'text', $title, 'Чому власники EV звертаються до нас' ),
						'why_button' => field( 'text', $button, 'Записатися' ),
					),
				),
				array(
					'title'  => __( 'Програма лояльності', 'leaderauto-core' ),
					'fields' => array(
						'loyalty_figure' => field( 'text', __( 'Велика цифра', 'leaderauto-core' ), '10%' ),
						'loyalty_title'  => field( 'text', $title, 'Програма лояльності' ),
						'loyalty_text'   => field( 'textarea', $text, 'Знижка 10% на замовлення будь-якої деталі.' ),
						'loyalty_qr'     => field( 'image', __( 'QR-код', 'leaderauto-core' ), 'qr.png' ),
					),
				),
				array(
					'title'  => __( 'Автопілот', 'leaderauto-core' ),
					'fields' => array(
						'autopilot_bg'     => field( 'image', $bg, 'Golovna/avtopilot.webp' ),
						'autopilot_title'  => field( 'text', $title, 'Налаштування автопілоту та автопаркування' ),
						'autopilot_text'   => field( 'textarea', $text, 'Активація та калібрування асистентів водіння на підтримуваних електромобілях.' ),
						'autopilot_button' => field( 'text', $button, 'Записатися' ),
					),
				),
				array(
					'title'  => __( 'Поширені запитання', 'leaderauto-core' ),
					'fields' => array(
						'faq_bg'    => field( 'image', $bg, 'Golovna/faq2.webp' ),
						'faq_title' => field( 'text', $title, 'Поширені запитання' ),
					),
				),
				array(
					'title'  => __( 'Заклик унизу сторінки', 'leaderauto-core' ),
					'fields' => array(
						'cta_bg'     => field( 'image', $bg, 'Golovna/cta-charging.webp' ),
						'cta_title'  => field( 'text', $title, 'Потрібна діагностика або ремонт електромобіля?' ),
						'cta_button' => field( 'text', $button, 'Залишити заявку' ),
					),
				),
			),
		),

		'parts'         => array(
			'title'    => __( 'Запчастини', 'leaderauto-core' ),
			'sections' => array(
				array(
					'title'  => __( 'Шапка сторінки (мобільна версія)', 'leaderauto-core' ),
					'fields' => array(
						'hero_bg'          => field( 'image', $bg, 'tech-service.webp', __( 'На комп’ютері замість цього блоку показується банер із намальованими кнопками — його змінює розробник.', 'leaderauto-core' ) ),
						'hero_kicker'      => field( 'text', $kicker, 'Запчастини для EV' ),
						'hero_title'       => field( 'text', $title, 'Запчастини для BYD та електромобілів' ),
						'hero_lead'        => field( 'textarea', $lead, 'Оригінальні та перевірені запчастини для BYD, Tesla, Zeekr та інших EV. Підберемо за VIN, перевіримо сумісність та допоможемо з установкою.' ),
						'hero_button_pick' => field( 'text', __( 'Кнопка 1', 'leaderauto-core' ), 'Підібрати запчастину' ),
						'hero_button_vin'  => field( 'text', __( 'Кнопка 2', 'leaderauto-core' ), 'Знайти за VIN' ),
					),
				),
				array(
					'title'  => __( 'Категорії', 'leaderauto-core' ),
					'fields' => array(
						'categories_kicker' => field( 'text', $kicker, 'Категорії' ),
						'categories_title'  => field( 'text', $title, 'Що саме потрібно?' ),
						'categories_lead'   => field( 'textarea', $lead, 'Оберіть категорію — вона автоматично підставиться у заявку.' ),
					),
				),
				array(
					'title'  => __( 'Підбір за авто', 'leaderauto-core' ),
					'fields' => array(
						'models_bg'     => field( 'image', $bg, 'Parts/baner3.webp' ),
						'models_kicker' => field( 'text', $kicker, 'Підбір за авто' ),
						'models_title'  => field( 'text', $title, 'Оберіть автомобіль' ),
					),
				),
				array(
					'title'  => __( 'Часто шукають', 'leaderauto-core' ),
					'fields' => array(
						'popular_kicker' => field( 'text', $kicker, 'Популярні запити' ),
						'popular_title'  => field( 'text', $title, 'Часто шукають' ),
					),
				),
				array(
					'title'  => __( 'Запчастини + сервіс', 'leaderauto-core' ),
					'fields' => array(
						'install_bg'     => field( 'image', $bg, 'Parts/servise_bg.webp' ),
						'install_kicker' => field( 'text', $kicker, 'Запчастини + сервіс' ),
						'install_title'  => field( 'text', $title, 'Не просто продаємо — можемо одразу встановити' ),
						'install_text'   => field( 'textarea', $text, 'Підберемо сумісну запчастину, встановимо її у сервісному центрі та перевіримо роботу систем автомобіля.' ),
						'install_button' => field( 'text', $button, 'Підібрати запчастину' ),
					),
				),
				array(
					'title'  => __( 'Заявка', 'leaderauto-core' ),
					'fields' => array(
						'request_kicker' => field( 'text', $kicker, 'Заявка' ),
						'request_title'  => field( 'text', $title, 'Знайдемо потрібну деталь' ),
						'request_lead'   => field( 'textarea', $lead, 'Вкажіть автомобіль та VIN, опишіть деталь. Менеджер перевірить сумісність, наявність і вартість.' ),
					),
				),
				array(
					'title'  => __( 'Часті запитання', 'leaderauto-core' ),
					'fields' => array(
						'faq_title' => field( 'text', $title, 'Часті запитання' ),
					),
				),
			),
		),

		'dealer'        => array(
			'title'    => __( 'Автодилер', 'leaderauto-core' ),
			'sections' => array(
				array(
					'title'  => __( 'Шапка сторінки', 'leaderauto-core' ),
					'fields' => array(
						'head_bg'    => field( 'image', $bg, 'byd.jpg' ),
						'head_title' => field( 'text', $title, 'Автодилер' ),
						'head_lead'  => field( 'textarea', $lead, 'Обери свою серед наявних — офіційний підхід до продажу та тест-драйву електромобілів BYD.' ),
					),
				),
				array(
					'title'  => __( 'Авто у наявності', 'leaderauto-core' ),
					'fields' => array(
						'showroom_title' => field( 'text', $title, 'Авто у наявності та під замовлення' ),
						'showroom_lead'  => field( 'textarea', $lead, 'Підбір моделі під ваш бюджет і задачі, допомога з оформленням, підготовка авто перед видачею та подальше сервісне обслуговування в одному місці.' ),
					),
				),
				array(
					'title'  => __( 'Як проходить купівля', 'leaderauto-core' ),
					'fields' => array(
						'offers_title' => field( 'text', $title, 'Як проходить купівля' ),
					),
				),
				array(
					'title'  => __( 'Гарантія', 'leaderauto-core' ),
					'fields' => array(
						'guarantee_bg'    => field( 'image', $bg, 'detailing-02.png' ),
						'guarantee_image' => field( 'image', __( 'Фото', 'leaderauto-core' ), 'detailing-19.webp' ),
						'guarantee_title' => field( 'text', $title, 'Відповідаємо за результат' ),
						'guarantee_text'  => field( 'textarea', $text, 'Ми працюємо тільки з електромобілями, тому беремось за авто, яке справді розуміємо, і супроводжуємо його після продажу.' ),
					),
				),
				array(
					'title'  => __( 'Відео', 'leaderauto-core' ),
					'fields' => array(
						'video_title' => field( 'text', $title, 'BYD Sea Lion 08 — дебют' ),
					),
				),
				array(
					'title'  => __( 'Заклик унизу сторінки', 'leaderauto-core' ),
					'fields' => array(
						'cta_title'  => field( 'text', $title, 'Цікавить конкретна модель?' ),
						'cta_button' => field( 'text', $button, 'Залишити заявку' ),
					),
				),
			),
		),

		'about'         => array(
			'title'    => __( 'Про нас', 'leaderauto-core' ),
			'sections' => array(
				array(
					'title'  => __( 'Шапка сторінки', 'leaderauto-core' ),
					'fields' => array(
						'head_bg'    => field( 'image', $bg, 'detailing-13.jpg' ),
						'head_title' => field( 'text', $title, 'Про нас' ),
						'head_lead'  => field( 'textarea', $lead, 'Експертний сервіс для сучасних електромобілів.' ),
					),
				),
				array(
					'title'  => __( 'Про компанію', 'leaderauto-core' ),
					'fields' => array(
						'intro' => field( 'textarea', $text, 'Ми спеціалізуємось на обслуговуванні та ремонті електромобілів, з акцентом на BYD та інші сучасні EV-платформи. Працюємо як з технічною частиною, так і з програмною — від діагностики до кодування блоків.' ),
					),
				),
				array(
					'title'  => __( 'Як ми працюємо', 'leaderauto-core' ),
					'fields' => array(
						'steps_title' => field( 'text', $title, 'Як ми працюємо' ),
					),
				),
				array(
					'title'  => __( 'Фото', 'leaderauto-core' ),
					'fields' => array(
						'image_1' => field( 'image', __( 'Фото 1', 'leaderauto-core' ), 'detailing-14.png' ),
						'image_2' => field( 'image', __( 'Фото 2', 'leaderauto-core' ), 'detailing-15.png' ),
					),
				),
				array(
					'title'  => __( 'Заклик унизу сторінки', 'leaderauto-core' ),
					'fields' => array(
						'cta_title'  => field( 'text', $title, 'Потрібна діагностика або ремонт електромобіля?' ),
						'cta_button' => field( 'text', $button, 'Залишити заявку' ),
					),
				),
			),
		),

		'contacts_page' => array(
			'title'    => __( 'Сторінка «Контакти»', 'leaderauto-core' ),
			'sections' => array(
				array(
					'title'  => __( 'Шапка сторінки', 'leaderauto-core' ),
					'fields' => array(
						'head_bg'    => field( 'image', $bg, 'detailing-12.jpg' ),
						'head_title' => field( 'text', $title, 'Залишити заявку' ),
						'head_lead'  => field( 'textarea', $lead, 'Опишіть коротко задачу — і ми зв’яжемось для діагностики та розрахунку.' ),
					),
				),
				array(
					'title'  => __( 'Блок контактів', 'leaderauto-core' ),
					'fields' => array(
						'visit_title' => field( 'text', __( 'Заголовок над адресою', 'leaderauto-core' ), 'Завітайте' ),
						'call_title'  => field( 'text', __( 'Заголовок над телефонами', 'leaderauto-core' ), 'Дзвоніть нам', __( 'Самі телефони й адреса — на екрані «Контакти».', 'leaderauto-core' ) ),
					),
				),
			),
		),
	);
}

/**
 * Flat key => field map for one page.
 *
 * @return array<string,array<string,string>>
 */
function fields( string $page ): array {
	$out = array();
	foreach ( schema()[ $page ]['sections'] ?? array() as $section ) {
		$out += $section['fields'];
	}
	return $out;
}

function option_name( string $page ): string {
	return 'leaderauto_content_' . $page;
}

/**
 * Saved value, or the schema default when nothing (or an empty string) is saved.
 */
function get( string $page, string $key ): string {
	static $cache = array();
	if ( ! isset( $cache[ $page ] ) ) {
		$cache[ $page ] = (array) get_option( option_name( $page ), array() );
	}
	$value = $cache[ $page ][ $key ] ?? '';
	if ( '' === $value ) {
		$value = fields( $page )[ $key ]['default'] ?? '';
	}
	return (string) $value;
}
