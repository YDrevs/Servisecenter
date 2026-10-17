<?php
/**
 * Заявки з контактної форми: тип запису la_lead, статуси, екран у wp-admin.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Leads {

	public const POST_TYPE = 'la_lead';

	/**
	 * @return array<string,string> status key => label
	 */
	public static function statuses(): array {
		return array(
			'la_new'      => __( 'Нова', 'leaderauto-core' ),
			'la_progress' => __( 'В роботі', 'leaderauto-core' ),
			'la_done'     => __( 'Виконана', 'leaderauto-core' ),
			'la_cancel'   => __( 'Скасована', 'leaderauto-core' ),
		);
	}

	public function hooks(): void {
		add_action( 'init', array( $this, 'register' ) );
	}

	public function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'               => __( 'Заявки', 'leaderauto-core' ),
					'singular_name'      => __( 'Заявка', 'leaderauto-core' ),
					'menu_name'          => __( 'Заявки', 'leaderauto-core' ),
					'all_items'          => __( 'Усі заявки', 'leaderauto-core' ),
					'edit_item'          => __( 'Заявка', 'leaderauto-core' ),
					'search_items'       => __( 'Шукати заявки', 'leaderauto-core' ),
					'not_found'          => __( 'Заявок немає.', 'leaderauto-core' ),
					'not_found_in_trash' => __( 'У кошику заявок немає.', 'leaderauto-core' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'menu_position'       => 3,
				'menu_icon'           => 'dashicons-email-alt',
				'supports'            => false,
				'map_meta_cap'        => true,
				'capabilities'        => array(
					'create_posts' => 'do_not_allow',
					'edit_posts'   => 'edit_others_posts',
				),
			)
		);

		foreach ( self::statuses() as $key => $label ) {
			register_post_status(
				$key,
				array(
					'label'                     => $label,
					/* translators: %s: number of leads */
					'label_count'               => array( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>', 'leaderauto-core' ),
					'public'                    => false,
					'protected'                 => true,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
				)
			);
		}
	}

	/**
	 * Store a validated submission. Returns the lead id, or 0 on failure.
	 *
	 * @param array{name:string,phone:string,car:string,message:string} $p
	 */
	public static function create( array $p ): int {
		$id = wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_status'  => 'la_new',
				'post_title'   => $p['name'],
				'post_content' => $p['message'],
				'meta_input'   => array(
					'_la_name'         => $p['name'],
					'_la_phone'        => $p['phone'],
					'_la_phone_digits' => preg_replace( '/\D+/', '', $p['phone'] ),
					'_la_car'          => $p['car'],
				),
			),
			true
		);

		if ( is_wp_error( $id ) ) {
			error_log( '[leaderauto-core] lead not saved: ' . $id->get_error_message() . '; payload: ' . wp_json_encode( $p ) );
			return 0;
		}
		return (int) $id;
	}

	public static function set_delivery( int $id, bool $mail, bool $tg ): void {
		update_post_meta( $id, '_la_delivered_mail', $mail ? '1' : '0' );
		update_post_meta( $id, '_la_delivered_tg', $tg ? '1' : '0' );
	}
}
