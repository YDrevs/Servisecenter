<?php
/**
 * Editable site content: which fields exist on which admin screen, and their
 * defaults. Defaults are the copy the theme shipped with, so an untouched site
 * renders exactly as before.
 *
 * Shape: page slug => [ 'title', 'sections' => [ [ 'title', 'fields' => [ key => field ] ] ] ].
 * Field: [ 'type' => text|textarea|url, 'label', 'default', 'help'? ].
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string,array{title:string,sections:array<int,array{title:string,fields:array<string,array<string,string>>}>}>
 */
function schema(): array {
	return array(
		'contacts' => array(
			'title'    => __( 'Контакти', 'leaderauto-core' ),
			'sections' => array(
				array(
					'title'  => __( 'Телефони', 'leaderauto-core' ),
					'fields' => array(
						'phone'     => array(
							'type'    => 'text',
							'label'   => __( 'Основний телефон', 'leaderauto-core' ),
							'default' => '+38 (098) 089-90-90',
							'help'    => __( 'Показується в шапці, футері та на кнопках «Подзвонити». Пишіть як завгодно — посилання для дзвінка складеться з цифр.', 'leaderauto-core' ),
						),
						'phone_alt' => array(
							'type'    => 'text',
							'label'   => __( 'Другий телефон', 'leaderauto-core' ),
							'default' => '+380 (95) 066 29 21',
						),
					),
				),
				array(
					'title'  => __( 'Месенджери та соцмережі', 'leaderauto-core' ),
					'fields' => array(
						'viber'     => array(
							'type'    => 'text',
							'label'   => __( 'Viber (номер)', 'leaderauto-core' ),
							'default' => '(095) 066 29 21',
						),
						'telegram'  => array(
							'type'    => 'text',
							'label'   => __( 'Telegram', 'leaderauto-core' ),
							'default' => '@V_P_97',
							'help'    => __( 'Ім’я користувача, наприклад @V_P_97.', 'leaderauto-core' ),
						),
						'instagram' => array(
							'type'    => 'url',
							'label'   => __( 'Instagram (посилання)', 'leaderauto-core' ),
							'default' => 'https://www.instagram.com/lider_avto_/',
						),
					),
				),
				array(
					'title'  => __( 'Адреса', 'leaderauto-core' ),
					'fields' => array(
						'address' => array(
							'type'    => 'text',
							'label'   => __( 'Адреса', 'leaderauto-core' ),
							'default' => 'вул. Коломийська, 1 «З», Чернівці, 58007',
						),
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
