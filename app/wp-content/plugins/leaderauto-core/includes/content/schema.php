<?php
/**
 * Editable site content: which fields exist on which admin screen, and their
 * defaults. Defaults are the copy the theme shipped with, so an untouched site
 * renders exactly as before.
 *
 * Shape: page slug => [ 'title', 'sections' => [ [ 'title', 'fields' => [ key => field ] ] ] ].
 * Field: [ 'type' => text|textarea|url|image|video|lines, 'label', 'default', 'help'? ].
 * An image or video saves an attachment ID; its default is a path under the theme's assets/images/.
 * `lines` is a textarea read as one item per line.
 * A repeater (see repeater()) holds rows of sub-fields; its default is the shipped rows.
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
 * A list of cards the client can add to, remove from and reorder.
 * Sub-field types: text, textarea, image, checkbox. `title` names the sub-field
 * shown as each row's heading in wp-admin.
 *
 * @param array<string,array<string,string>> $subfields
 * @param array<int,array<string,string>>    $rows
 * @return array<string,mixed>
 */
function repeater( string $label, string $title, array $subfields, array $rows, string $help = '' ): array {
	return array(
		'type'      => 'repeater',
		'label'     => $label,
		'title'     => $title,
		'subfields' => $subfields,
		'default'   => $rows,
		'help'      => $help,
	);
}

/**
 * @return array<string,array{title:string,sections:array<int,array{title:string,fields:array<string,array<string,mixed>>}>}>
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
	$lines  = __( 'Кожен пункт — з нового рядка.', 'leaderauto-core' );
	$faq    = array(
		'q' => field( 'text', __( 'Питання', 'leaderauto-core' ), '' ),
		'a' => field( 'textarea', __( 'Відповідь', 'leaderauto-core' ), '' ),
	);

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
					'title'  => __( 'Пошта', 'leaderauto-core' ),
					'fields' => array(
						'email' => field( 'text', __( 'E-mail', 'leaderauto-core' ), 'bydcenterinfo@gmail.com', __( 'Показується на сторінці «Контакти». Заявки з форми сюди не надходять — їхня адреса в Налаштуваннях плагіна.', 'leaderauto-core' ) ),
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
						'map_url' => field( 'url', __( 'Посилання на карту', 'leaderauto-core' ), 'https://www.google.com/maps/place/BYD-Center/@48.3267303,25.9155928,17z/data=!4m15!1m8!3m7!1s0x473407b903c9fc75:0xfa75ab3f2d7195da!2z0YPQuy4g0JrQvtC70L7QvNGL0LnRgdC60LDRjywgMdC3LCDQp9C10YDQvdC-0LLRhtGLLCDQp9C10YDQvdC-0LLQuNGG0LrQsNGPINC-0LHQu9Cw0YHRgtGMLCA1ODAwNw!3b1!8m2!3d48.3267303!4d25.9155928!16s%2Fg%2F11c4zv56b_!3m5!1s0x47340986aad843bd:0x208fd44277903a9b!8m2!3d48.3258792!4d25.9149778!16s%2Fg%2F11lnmzhr02?entry=ttu&g_ep=EgoyMDI2MTAxNC4wIKXMDSoASAFQAw%3D%3D', __( 'Google Maps → «Поділитися» → «Копіювати посилання». Адреса на сайті стане клікабельною. Порожнє — адреса просто текстом.', 'leaderauto-core' ) ),
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
						'services_video'     => field( 'video', __( 'Відео праворуч', 'leaderauto-core' ), 'r2d2.mp4', __( 'Вертикальне відео (9:16), MP4. До 64 МБ.', 'leaderauto-core' ) ),
						'services_title'     => field( 'text', $title, 'Наші послуги' ),
						'services_groups'    => repeater(
							__( 'Картки послуг', 'leaderauto-core' ),
							'title',
							array(
								'title' => field( 'text', __( 'Назва', 'leaderauto-core' ), '' ),
								'image' => field( 'image', __( 'Фото', 'leaderauto-core' ), '' ),
								'items' => field( 'textarea', __( 'Послуги', 'leaderauto-core' ), '', __( 'Кожна послуга — з нового рядка. Рядок, що починається з «-», стає підпунктом попереднього рядка.', 'leaderauto-core' ) ),
							),
							array(
								array(
									'title' => 'Програмний ремонт та діагностика',
									'image' => 'diagnostic-byd.webp',
									'items' => "Комп’ютерна діагностика\nОновлення програмного забезпечення\nОновлення та адаптація блоків\nУсунення помилок та програмних збоїв\nНалаштування електронних систем безпеки\nПослуги автоелектрика\nРемонт інверторів\nОбслуговування та ремонт високовольтних батарей",
								),
								array(
									'title' => 'Технічне обслуговування',
									'image' => 'tech-service.webp',
									'items' => "Розвал-сходження для EV та HYBRID\nВібростенд та ремонт ходової\nЗаміна мастил та технічних рідин\nШиномонтаж\nЗаправка та ремонт кондиціонерів\nПеревірка усіх систем автомобіля",
								),
								array(
									'title' => 'Запчастини та комплектуючі',
									'image' => 'detailing-10.jpg',
									'items' => "Підбір та продаж запчастин\nЗамовлення запчастин з Китаю\nДоставка по Україні\nДіючі програми лояльності",
								),
								array(
									'title' => 'Додаткові послуги',
									'image' => 'services-extra.webp',
									'items' => "Забір і доставка автомобіля від клієнта до СТО і назад\nЕвакуатор\nОренда підйомника або робочого місця для самостійного обслуговування\nЗберігання коліс\nПередпродажна перевірка\nПісляпродажний сервіс:\n- Антикорозійна обробка\n- Тонування\n- Антихром обробка\n- Шумовіброізоляція",
								),
							),
							__( 'Кожна послуга на сайті веде на форму заявки з її назвою.', 'leaderauto-core' )
						),
					),
				),
				array(
					'title'  => __( 'Чому обирають нас', 'leaderauto-core' ),
					'fields' => array(
						'why_bg'     => field( 'image', $bg, 'Golovna/faq.webp' ),
						'why_kicker' => field( 'text', $kicker, '100% вирішення проблем' ),
						'why_title'  => field( 'text', $title, 'Чому власники EV звертаються до нас' ),
						'why_button' => field( 'text', $button, 'Записатися' ),
						'why_list'   => field( 'lines', __( 'Пункти', 'leaderauto-core' ), "Спеціалізація на електромобілях\nДосвід роботи з BYD\nСучасна діагностика\nПрозорий підхід\nДопомога із запчастинами", $lines ),
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
						'faq_items' => repeater(
							__( 'Питання', 'leaderauto-core' ),
							'q',
							$faq,
							array(
								array( 'q' => 'Ви працюєте тільки з BYD?', 'a' => 'Ні. BYD — наш профільний бренд, але ми обслуговуємо Honda, Zeekr, Volkswagen ID, Nissan Leaf та інші електромобілі.' ),
								array( 'q' => 'Чи можна замовити запчастини у вас?', 'a' => 'Так. Замовляємо оригінальні деталі та перевірені аналоги, допомагаємо з підбором і доставкою.' ),
								array( 'q' => 'Ви займаєтесь програмним ремонтом?', 'a' => 'Так: діагностика, оновлення ПЗ, кодування та адаптація блоків, усунення помилок.' ),
								array( 'q' => 'Чи є гарантія?', 'a' => 'На виконані роботи та встановлені запчастини надаємо гарантію; умови узгоджуємо перед ремонтом.' ),
								array( 'q' => 'Скільки часу займає діагностика?', 'a' => 'Базова діагностика — зазвичай протягом дня. Складніші випадки узгоджуємо окремо.' ),
							)
						),
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
						'categories'        => repeater(
							__( 'Категорії', 'leaderauto-core' ),
							'title',
							array(
								'title' => field( 'text', __( 'Назва', 'leaderauto-core' ), '' ),
								'text'  => field( 'text', __( 'Опис', 'leaderauto-core' ), '' ),
								'icon'  => field( 'text', __( 'Значок (символ)', 'leaderauto-core' ), '', __( 'Показується, коли немає фото.', 'leaderauto-core' ) ),
								'img'   => field( 'image', __( 'Фото', 'leaderauto-core' ), '' ),
							),
							array(
								array( 'icon' => '◈', 'title' => 'Кузов та оптика', 'text' => 'Фари, бампери, крила, дзеркала', 'img' => 'Parts/byd_avtomobil.webp' ),
								array( 'icon' => '⌁', 'title' => 'Ходова частина', 'text' => 'Важелі, амортизатори, ступиці', 'img' => 'Parts/hodova2.webp' ),
								array( 'icon' => '◎', 'title' => 'Гальмівна система', 'text' => 'Диски, колодки, супорти', 'img' => 'Parts/galma_disk.webp' ),
								array( 'icon' => '⌘', 'title' => 'Електроніка', 'text' => 'Блоки, датчики, модулі', 'img' => 'Parts/EBU.webp' ),
								array( 'icon' => '▣', 'title' => 'Батарея та HV', 'text' => 'Високовольтні компоненти', 'img' => 'Parts/baterry.webp' ),
								array( 'icon' => 'ϟ', 'title' => 'Зарядна система', 'text' => 'Порти, модулі, кабелі', 'img' => 'Parts/zaryad2_foto.webp' ),
								array( 'icon' => '◇', 'title' => 'Салон', 'text' => 'Дисплеї, пластик, кнопки', 'img' => 'Parts/salon.webp' ),
								array( 'icon' => '✦', 'title' => 'ТО та витратники', 'text' => 'Фільтри, рідини, щітки', 'img' => 'Parts/servise.webp' ),
							),
							__( 'Назви категорій також є варіантами у полі «Категорія» форми заявки.', 'leaderauto-core' )
						),
					),
				),
				array(
					'title'  => __( 'Підбір за авто', 'leaderauto-core' ),
					'fields' => array(
						'models_bg'     => field( 'image', $bg, 'Parts/baner3.webp' ),
						'models_kicker' => field( 'text', $kicker, 'Підбір за авто' ),
						'models_title'  => field( 'text', $title, 'Оберіть автомобіль' ),
						'brands'        => repeater(
							__( 'Марки', 'leaderauto-core' ),
							'brand',
							array(
								'brand' => field( 'text', __( 'Марка', 'leaderauto-core' ), '', __( 'Підставляється в поле «Автомобіль» форми заявки.', 'leaderauto-core' ) ),
								'logo'  => field( 'image', __( 'Логотип', 'leaderauto-core' ), '' ),
							),
							array(
								array( 'brand' => 'Changan', 'logo' => 'logo_cars/changan.webp' ),
								array( 'brand' => 'BYD', 'logo' => 'logo_cars/byd.webp' ),
								array( 'brand' => 'Audi', 'logo' => 'logo_cars/audi.webp' ),
								array( 'brand' => 'Chery', 'logo' => 'logo_cars/chery.webp' ),
								array( 'brand' => 'Denza', 'logo' => 'logo_cars/denza.webp' ),
								array( 'brand' => 'Fangchengbao', 'logo' => 'logo_cars/fangchengbao.webp' ),
								array( 'brand' => 'Honda', 'logo' => 'logo_cars/honda.webp' ),
								array( 'brand' => 'DFSK', 'logo' => 'logo_cars/dfsk.webp' ),
								array( 'brand' => 'Hyundai', 'logo' => 'logo_cars/hyundai.webp' ),
								array( 'brand' => 'Kia', 'logo' => 'logo_cars/kia.webp' ),
								array( 'brand' => 'MG', 'logo' => 'logo_cars/mg.webp' ),
								array( 'brand' => 'Nissan', 'logo' => 'logo_cars/nissan.webp' ),
								array( 'brand' => 'Toyota', 'logo' => 'logo_cars/toyota.webp' ),
								array( 'brand' => 'Volkswagen', 'logo' => 'logo_cars/volkswagen.webp' ),
								array( 'brand' => 'Zeekr', 'logo' => 'logo_cars/zeekr.webp' ),
							)
						),
					),
				),
				array(
					'title'  => __( 'Часто шукають', 'leaderauto-core' ),
					'fields' => array(
						'popular_kicker' => field( 'text', $kicker, 'Популярні запити' ),
						'popular_title'  => field( 'text', $title, 'Часто шукають' ),
						'popular'        => repeater(
							__( 'Картки', 'leaderauto-core' ),
							'title',
							array(
								'title' => field( 'text', __( 'Назва', 'leaderauto-core' ), '' ),
								'part'  => field( 'text', __( 'Що підставити в заявку', 'leaderauto-core' ), '' ),
								'icon'  => field( 'text', __( 'Значок (символ)', 'leaderauto-core' ), '', __( 'Показується, коли немає фото.', 'leaderauto-core' ) ),
								'img'   => field( 'image', __( 'Фото', 'leaderauto-core' ), '' ),
								'badge' => field( 'image', __( 'Маленька іконка на фото', 'leaderauto-core' ), '' ),
							),
							array(
								array( 'icon' => '◫', 'title' => 'Передня оптика BYD', 'part' => 'Передня оптика BYD', 'img' => 'Parts/fara.webp', 'badge' => 'Parts/fara_icon.webp' ),
								array( 'icon' => '◎', 'title' => 'Гальмівні колодки', 'part' => 'Гальмівні колодки', 'img' => 'Parts/galmdisk_foto.webp', 'badge' => 'Parts/galmo_icon.webp' ),
								array( 'icon' => 'ϟ', 'title' => 'Порти та модулі', 'part' => 'Порт або зарядний модуль', 'img' => 'Parts/zaryad_foto.webp', 'badge' => 'Parts/zaryadka_icon.webp' ),
								array( 'icon' => '⌁', 'title' => 'Підвіска та ступиці', 'part' => 'Запчастини ходової частини', 'img' => 'Parts/Hodova_foto.webp', 'badge' => 'Parts/servis_icon.webp' ),
							)
						),
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
						'install_steps'  => field( 'lines', __( 'Кроки', 'leaderauto-core' ), "Підбір за VIN\nПеревірка сумісності\nВстановлення\nДіагностика", $lines ),
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
						'faq_items' => repeater(
							__( 'Питання', 'leaderauto-core' ),
							'q',
							$faq,
							array(
								array( 'q' => 'Чи обов’язково знати номер запчастини?', 'a' => 'Ні. Для підбору можна використати VIN, модель авто, опис або фото.' ),
								array( 'q' => 'Чи можна одразу встановити деталь?', 'a' => 'Так, встановлення та діагностику можна погодити із сервісним центром.' ),
							)
						),
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
						'models'         => repeater(
							__( 'Моделі', 'leaderauto-core' ),
							'name',
							array(
								'name'     => field( 'text', __( 'Модель (без «BYD»)', 'leaderauto-core' ), '' ),
								'type'     => field( 'text', __( 'Тип кузова', 'leaderauto-core' ), '' ),
								'in_stock' => field( 'checkbox', __( 'В наявності', 'leaderauto-core' ), '', __( 'Без позначки показується «Під замовлення».', 'leaderauto-core' ) ),
								'image'    => field( 'image', __( 'Фото', 'leaderauto-core' ), '' ),
								'text'     => field( 'textarea', __( 'Опис', 'leaderauto-core' ), '' ),
							),
							array(
								array( 'name' => 'Seal', 'type' => 'Седан', 'in_stock' => '1', 'image' => 'vehicles/seal.webp', 'text' => 'Електричний седан для щоденних поїздок і траси.' ),
								array( 'name' => 'Sealion 7', 'type' => 'Кросовер', 'in_stock' => '', 'image' => 'vehicles/sealion-7.webp', 'text' => 'Просторий електричний кросовер для сім\'ї та далеких поїздок.' ),
								array( 'name' => 'Leopard', 'type' => 'Позашляховик', 'in_stock' => '', 'image' => 'vehicles/leopard.webp', 'text' => 'Рамний позашляховик для тих, кому потрібне авто поза асфальтом.' ),
								array( 'name' => 'Song +', 'type' => 'Кросовер, гібрид', 'in_stock' => '1', 'image' => 'vehicles/song-plus.webp', 'text' => 'Гібридний кросовер: електротяга в місті й запас ходу для поїздок.' ),
								array( 'name' => 'Seagull', 'type' => 'Хетчбек', 'in_stock' => '1', 'image' => 'vehicles/seagull.webp', 'text' => 'Компактний міський хетчбек, зручний для парковки й коротких маршрутів.' ),
								array( 'name' => 'Dolphin', 'type' => 'Хетчбек', 'in_stock' => '', 'image' => 'vehicles/dolphin.webp', 'text' => 'Практичний хетчбек з місткою кабіною для міста та області.' ),
							),
							__( 'Без фото показується сіра заглушка «ФОТО МОДЕЛІ».', 'leaderauto-core' )
						),
					),
				),
				array(
					'title'  => __( 'Як проходить купівля', 'leaderauto-core' ),
					'fields' => array(
						'offers_title' => field( 'text', $title, 'Як проходить купівля' ),
						'offers'       => repeater(
							__( 'Картки', 'leaderauto-core' ),
							'title',
							array(
								'title' => field( 'text', __( 'Назва', 'leaderauto-core' ), '' ),
								'image' => field( 'image', __( 'Фото', 'leaderauto-core' ), '' ),
								'text'  => field( 'textarea', $text, '' ),
							),
							array(
								array( 'image' => '1w.jpg', 'title' => 'Підбір моделі', 'text' => 'Допомагаємо обрати електромобіль під ваш бюджет, пробіг і умови експлуатації.' ),
								array( 'image' => 'detailing-08.jpg', 'title' => 'Тест-драйв', 'text' => 'Записуємо на тест-драйв, щоб ви відчули авто до покупки, а не з опису.' ),
								array( 'image' => 'detailing-21.jpg', 'title' => 'Оформлення', 'text' => 'Супровід документів і реєстрації — без зайвих поїздок і посередників.' ),
								array( 'image' => 'detailing-18.png', 'title' => 'Передпродажна підготовка', 'text' => 'Повна діагностика, перевірка батареї та електроніки перед видачею.' ),
								array( 'image' => 'detailing-10.jpg', 'title' => 'Видача авто', 'text' => 'Показуємо, як користуватись зарядкою та системами авто, і відповідаємо на питання.' ),
								array( 'image' => 'detailing-17.png', 'title' => 'Сервіс після покупки', 'text' => 'Подальше обслуговування у тому ж місці, де купували — з історією вашого авто.' ),
							)
						),
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
						'video_title'  => field( 'text', $title, 'BYD Sea Lion 08 — дебют' ),
						'video'        => field( 'video', __( 'Відео', 'leaderauto-core' ), 'sea-lion-08-debut.mp4', __( 'MP4, до 64 МБ.', 'leaderauto-core' ) ),
						'video_poster' => field( 'image', __( 'Обкладинка відео', 'leaderauto-core' ), 'byd.jpg', __( 'Картинка, яку видно до натискання «Відтворити».', 'leaderauto-core' ) ),
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
						'stats' => repeater(
							__( 'Цифри', 'leaderauto-core' ),
							'n',
							array(
								'n'     => field( 'text', __( 'Значення', 'leaderauto-core' ), '', __( 'Наприклад 275+ або 100%.', 'leaderauto-core' ) ),
								'label' => field( 'text', __( 'Підпис', 'leaderauto-core' ), '' ),
								'from'  => field( 'text', __( 'Анімувати від', 'leaderauto-core' ), '', __( 'Число, з якого лічильник рахує при прокрутці. Порожньо — без анімації.', 'leaderauto-core' ) ),
							),
							array(
								array( 'n' => '100%', 'from' => '55', 'label' => 'Задоволених клієнтів' ),
								array( 'n' => '24/7', 'from' => '', 'label' => 'На зв’язку' ),
								array( 'n' => '275+', 'from' => '199', 'label' => 'Виконаних сервісів' ),
							)
						),
					),
				),
				array(
					'title'  => __( 'Як ми працюємо', 'leaderauto-core' ),
					'fields' => array(
						'steps_title' => field( 'text', $title, 'Як ми працюємо' ),
						'steps'       => field( 'lines', __( 'Кроки', 'leaderauto-core' ), "Заявка або дзвінок\nДіагностика\nУзгодження\nРемонт\nВидача авто", $lines ),
					),
				),
				array(
					'title'  => __( 'Наша команда', 'leaderauto-core' ),
					'fields' => array(
						'team_title' => field( 'text', $title, 'Наша Команда' ),
						'team'       => repeater(
							__( 'Картки', 'leaderauto-core' ),
							'name',
							array(
								'name'  => field( 'text', __( 'Ім’я', 'leaderauto-core' ), '' ),
								'image' => field( 'image', __( 'Фото', 'leaderauto-core' ), '' ),
								'text'  => field( 'textarea', $text, '' ),
							),
							array(
								array( 'image' => 'About page images/Mehanik1.webp', 'name' => 'Ім’я Прізвище', 'text' => 'Посада, досвід і спеціалізація.' ),
								array( 'image' => 'About page images/Mehanik2.webp', 'name' => 'Ім’я Прізвище', 'text' => 'Посада, досвід і спеціалізація.' ),
								array( 'image' => 'About page images/Mehanik3.webp', 'name' => 'Ім’я Прізвище', 'text' => 'Посада, досвід і спеціалізація.' ),
								array( 'image' => 'About page images/Mehanik4.webp', 'name' => 'Ім’я Прізвище', 'text' => 'Посада, досвід і спеціалізація.' ),
							)
						),
					),
				),
				array(
					'title'  => __( 'Наші робочі будні', 'leaderauto-core' ),
					'fields' => array(
						'work_title'   => field( 'text', $title, 'Наші робочі будні' ),
						'work_video_1' => field( 'video', __( 'Відео 1', 'leaderauto-core' ), '', __( 'Вертикальне відео з iPhone (9:16), MP4 або MOV. До 64 МБ. Порожнє поле — на сайті показується заглушка.', 'leaderauto-core' ) ),
						'work_video_2' => field( 'video', __( 'Відео 2', 'leaderauto-core' ), '' ),
						'work_video_3' => field( 'video', __( 'Відео 3', 'leaderauto-core' ), '' ),
						'work_video_4' => field( 'video', __( 'Відео 4', 'leaderauto-core' ), '' ),
						'work_video_5' => field( 'video', __( 'Відео 5', 'leaderauto-core' ), '' ),
						'work_video_6' => field( 'video', __( 'Відео 6', 'leaderauto-core' ), '' ),
						'work_video_7' => field( 'video', __( 'Відео 7', 'leaderauto-core' ), '' ),
						'work_video_8' => field( 'video', __( 'Відео 8', 'leaderauto-core' ), '' ),
					),
				),
				array(
					'title'  => __( 'Наші задоволені клієнти', 'leaderauto-core' ),
					'fields' => array(
						'clients_title' => field( 'text', $title, 'Наші задоволені клієнти' ),
						'clients'       => repeater(
							__( 'Картки', 'leaderauto-core' ),
							'caption',
							array(
								'caption' => field( 'text', __( 'Підпис', 'leaderauto-core' ), '', __( 'Необов’язково: ім’я клієнта чи авто. Показується під карткою.', 'leaderauto-core' ) ),
								'video'   => field( 'video', __( 'Відео', 'leaderauto-core' ), '', __( 'Reels з Instagram (9:16), MP4. До 64 МБ. Якщо обрано і відео, і фото, показується відео.', 'leaderauto-core' ) ),
								'image'   => field( 'image', __( 'Фото', 'leaderauto-core' ), '', __( 'Допис з Instagram, у своїй пропорції (4:5, 3:4 чи 1:1).', 'leaderauto-core' ) ),
							),
							array(
								array( 'caption' => '', 'video' => '', 'image' => 'About page images/Vidguk/Seagull1_sold.webp' ),
								array( 'caption' => '', 'video' => 'About page images/Vidguk/seagull2_sold.mp4', 'image' => '' ),
								array( 'caption' => '', 'video' => '', 'image' => 'About page images/Vidguk/Audi_sold.webp' ),
								array( 'caption' => '', 'video' => 'About page images/Vidguk/Ivan_Yuan.mp4', 'image' => '' ),
								array( 'caption' => '', 'video' => '', 'image' => 'About page images/Vidguk/Seagull3_sold.webp' ),
								array( 'caption' => '', 'video' => 'About page images/Vidguk/song_sold.mp4', 'image' => '' ),
								array( 'caption' => '', 'video' => '', 'image' => 'About page images/Vidguk/Honda_sold.webp' ),
								array( 'caption' => '', 'video' => 'About page images/Vidguk/Uahn_sold.mp4', 'image' => '' ),
								array( 'caption' => '', 'video' => '', 'image' => 'About page images/Vidguk/Seagull4_sold.webp' ),
							)
						),
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
						'mail_title'  => field( 'text', __( 'Заголовок над поштою', 'leaderauto-core' ), 'Пишіть нам' ),
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
	return is_array( $value ) ? '' : (string) $value;
}

/**
 * Rows of a repeater. Saved rows win even when empty — the client removed them all.
 *
 * @return array<int,array<string,string>>
 */
function get_rows( string $page, string $key ): array {
	$saved = (array) get_option( option_name( $page ), array() );
	if ( isset( $saved[ $key ] ) && is_array( $saved[ $key ] ) ) {
		return $saved[ $key ];
	}
	return fields( $page )[ $key ]['default'] ?? array();
}
