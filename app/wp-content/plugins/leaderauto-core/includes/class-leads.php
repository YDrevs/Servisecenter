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
	private const NONCE    = 'leaderauto_lead_save';

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

		// List screen.
		add_action( 'admin_menu', array( $this, 'menu_badge' ), 99 );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column' ), 10, 2 );
		add_filter( 'posts_search', array( $this, 'search' ), 10, 2 );
		add_filter( 'bulk_actions-edit-' . self::POST_TYPE, array( $this, 'bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-' . self::POST_TYPE, array( $this, 'handle_bulk' ), 10, 3 );
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );

		// Edit screen.
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( $this, 'meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'guard_status' ), 10, 2 );
		add_filter( 'wp_untrash_post_status', array( $this, 'untrash_status' ), 10, 3 );
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
					'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>', 'leaderauto-core' ), // phpcs:ignore WordPress.WP.I18n -- label is already translated.
					'public'                    => false,
					'protected'                 => true,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
				)
			);
		}
	}

	public function menu_badge(): void {
		global $menu;
		$count = (int) ( wp_count_posts( self::POST_TYPE )->la_new ?? 0 );
		if ( ! $count || ! is_array( $menu ) ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( 'edit.php?post_type=' . self::POST_TYPE === ( $item[2] ?? '' ) ) {
				$menu[ $i ][0] .= sprintf( ' <span class="awaiting-mod count-%1$d"><span class="pending-count">%2$s</span></span>', $count, number_format_i18n( $count ) );
				break;
			}
		}
	}

	/**
	 * @param array<string,string> $columns
	 * @return array<string,string>
	 */
	public function columns( array $columns ): array {
		return array(
			'cb'         => $columns['cb'] ?? '',
			'la_date'    => __( 'Дата', 'leaderauto-core' ),
			'title'      => __( 'Ім’я', 'leaderauto-core' ),
			'la_phone'   => __( 'Телефон', 'leaderauto-core' ),
			'la_car'     => __( 'Авто', 'leaderauto-core' ),
			'la_message' => __( 'Повідомлення', 'leaderauto-core' ),
			'la_status'  => __( 'Статус', 'leaderauto-core' ),
		);
	}

	/**
	 * @param array<string,string> $columns
	 * @return array<string,string>
	 */
	public function sortable_columns( array $columns ): array {
		$columns['la_date'] = 'date';
		return $columns;
	}

	public function column( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'la_date':
				echo esc_html( get_the_date( 'd.m.Y H:i', $post_id ) );
				break;
			case 'la_phone':
				$phone = (string) get_post_meta( $post_id, '_la_phone', true );
				printf( '<a href="tel:%s">%s</a>', esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ), esc_html( $phone ) );
				break;
			case 'la_car':
				echo esc_html( (string) get_post_meta( $post_id, '_la_car', true ) );
				break;
			case 'la_message':
				echo esc_html( mb_strimwidth( (string) get_post_field( 'post_content', $post_id, 'raw' ), 0, 80, '…' ) );
				break;
			case 'la_status':
				$status = (string) get_post_status( $post_id );
				$label  = self::statuses()[ $status ] ?? $status;
				echo 'la_new' === $status ? '<strong>' . esc_html( $label ) . '</strong>' : esc_html( $label );
				break;
		}
	}

	/**
	 * Extend the admin search to phone, car and manager note.
	 */
	public function search( string $search, \WP_Query $query ): string {
		if ( '' === $search || ! is_admin() || ! $query->is_main_query() || self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return $search;
		}
		global $wpdb;

		$term = (string) $query->get( 's' );
		$or   = $wpdb->prepare(
			"{$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ('_la_phone','_la_car','_la_note') AND meta_value LIKE %s)",
			'%' . $wpdb->esc_like( $term ) . '%'
		);
		$digits = preg_replace( '/\D+/', '', $term );
		if ( strlen( (string) $digits ) >= 3 ) {
			$or .= $wpdb->prepare(
				" OR {$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_la_phone_digits' AND meta_value LIKE %s)",
				'%' . $wpdb->esc_like( $digits ) . '%'
			);
		}

		// Core builds " AND (…) "; turn it into " AND ( <ours> OR (…) ) ".
		return ' AND ( ' . $or . ' OR ' . substr( trim( $search ), 4 ) . ' ) ';
	}

	/**
	 * @param array<string,string> $actions
	 * @return array<string,string>
	 */
	public function bulk_actions( array $actions ): array {
		unset( $actions['edit'] );
		foreach ( self::statuses() as $key => $label ) {
			/* translators: %s: status label */
			$actions[ 'la_status_' . $key ] = sprintf( __( 'Статус → %s', 'leaderauto-core' ), $label );
		}
		return $actions;
	}

	/**
	 * @param array<int,int|string> $ids
	 */
	public function handle_bulk( string $redirect, string $action, array $ids ): string {
		if ( 0 !== strpos( $action, 'la_status_' ) ) {
			return $redirect;
		}
		$status = substr( $action, strlen( 'la_status_' ) );
		if ( ! isset( self::statuses()[ $status ] ) ) {
			return $redirect;
		}

		$done = 0;
		foreach ( $ids as $id ) {
			$id = (int) $id;
			if ( current_user_can( 'edit_post', $id ) && ! is_wp_error( wp_update_post( array( 'ID' => $id, 'post_status' => $status ), true ) ) ) {
				++$done;
			}
		}
		return add_query_arg( 'updated', $done, $redirect );
	}

	/**
	 * Quick Edit only knows core statuses and would reset ours — drop it.
	 *
	 * @param array<string,string> $actions
	 * @return array<string,string>
	 */
	public function row_actions( array $actions, \WP_Post $post ): array {
		if ( self::POST_TYPE === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	public function meta_boxes(): void {
		remove_meta_box( 'submitdiv', self::POST_TYPE, 'side' );
		add_meta_box( 'la_lead_client', __( 'Клієнт', 'leaderauto-core' ), array( $this, 'box_client' ), self::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'la_lead_work', __( 'Обробка', 'leaderauto-core' ), array( $this, 'box_work' ), self::POST_TYPE, 'side', 'high' );
		add_meta_box( 'la_lead_delivery', __( 'Доставка', 'leaderauto-core' ), array( $this, 'box_delivery' ), self::POST_TYPE, 'side' );
	}

	public function box_client( \WP_Post $post ): void {
		$phone = (string) get_post_meta( $post->ID, '_la_phone', true );
		$rows  = array(
			__( 'Дата', 'leaderauto-core' )    => esc_html( get_the_date( 'd.m.Y H:i', $post ) ),
			__( 'Ім’я', 'leaderauto-core' )    => esc_html( (string) get_post_meta( $post->ID, '_la_name', true ) ),
			__( 'Телефон', 'leaderauto-core' ) => sprintf( '<a href="tel:%s">%s</a>', esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ), esc_html( $phone ) ),
			__( 'Авто', 'leaderauto-core' )    => esc_html( (string) get_post_meta( $post->ID, '_la_car', true ) ),
			__( 'Повідомлення', 'leaderauto-core' ) => nl2br( esc_html( $post->post_content ) ),
		);
		echo '<table class="form-table" role="presentation"><tbody>';
		foreach ( $rows as $label => $html ) {
			printf( '<tr><th scope="row">%s</th><td>%s</td></tr>', esc_html( $label ), $html ? $html : '—' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
		}
		echo '</tbody></table>';
	}

	public function box_work( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE . $post->ID, self::NONCE );
		?>
		<p>
			<label for="la_status"><strong><?php esc_html_e( 'Статус', 'leaderauto-core' ); ?></strong></label><br>
			<select name="post_status" id="la_status" class="widefat">
				<?php foreach ( self::statuses() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $post->post_status, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="la_note"><strong><?php esc_html_e( 'Нотатка менеджера', 'leaderauto-core' ); ?></strong></label><br>
			<textarea name="la_note" id="la_note" rows="6" class="widefat"><?php echo esc_textarea( (string) get_post_meta( $post->ID, '_la_note', true ) ); ?></textarea>
		</p>
		<p style="display:flex;justify-content:space-between;align-items:center">
			<a class="submitdelete" style="color:#b32d2e" href="<?php echo esc_url( (string) get_delete_post_link( $post->ID ) ); ?>"><?php esc_html_e( 'У кошик', 'leaderauto-core' ); ?></a>
			<button type="submit" name="save" value="1" class="button button-primary"><?php esc_html_e( 'Зберегти', 'leaderauto-core' ); ?></button>
		</p>
		<?php
	}

	public function box_delivery( \WP_Post $post ): void {
		$mark = static function ( string $key ) use ( $post ): string {
			$v = get_post_meta( $post->ID, $key, true );
			return '' === $v ? '—' : ( '1' === $v ? '✓' : '✗' );
		};
		$make_at = (int) get_post_meta( $post->ID, '_la_make_at', true );
		printf(
			'<p>E-mail: %s<br>Telegram: %s<br>Make.com: %s%s</p>',
			esc_html( $mark( '_la_delivered_mail' ) ),
			esc_html( $mark( '_la_delivered_tg' ) ),
			esc_html( $mark( '_la_make_status' ) ),
			$make_at ? ' <span class="description">(' . esc_html( wp_date( 'd.m.Y H:i', $make_at ) ) . ')</span>' : ''
		);
	}

	public function save( int $post_id ): void {
		$nonce = isset( $_POST[ self::NONCE ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE ] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE . $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, '_la_note', sanitize_textarea_field( wp_unslash( $_POST['la_note'] ?? '' ) ) );
	}

	/**
	 * Only our statuses (or trash) are allowed; anything else keeps the previous one.
	 *
	 * @param array<string,mixed> $data
	 * @param array<string,mixed> $postarr
	 * @return array<string,mixed>
	 */
	public function guard_status( array $data, array $postarr ): array {
		if ( self::POST_TYPE !== $data['post_type'] || 'trash' === $data['post_status'] || isset( self::statuses()[ $data['post_status'] ] ) ) {
			return $data;
		}
		$prev                = empty( $postarr['ID'] ) ? '' : (string) get_post_status( (int) $postarr['ID'] );
		$data['post_status'] = isset( self::statuses()[ $prev ] ) ? $prev : 'la_new';
		return $data;
	}

	public function untrash_status( string $status, int $post_id, string $previous ): string {
		if ( self::POST_TYPE !== get_post_type( $post_id ) ) {
			return $status;
		}
		return isset( self::statuses()[ $previous ] ) ? $previous : 'la_new';
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

	/**
	 * Record per-channel delivery. Null = channel not configured, nothing is stored (shown as «—»).
	 */
	public static function set_delivery( int $id, bool $mail, ?bool $tg ): void {
		update_post_meta( $id, '_la_delivered_mail', $mail ? '1' : '0' );
		if ( null !== $tg ) {
			update_post_meta( $id, '_la_delivered_tg', $tg ? '1' : '0' );
		}
	}

	/**
	 * A lead as plain data for integrations (Make.com, CRM). Empty if not a lead.
	 *
	 * @return array<string,int|string>
	 */
	public static function to_array( int $id ): array {
		$post = get_post( $id );
		if ( ! $post || self::POST_TYPE !== $post->post_type ) {
			return array();
		}
		$created  = get_post_datetime( $post );
		$modified = get_post_datetime( $post, 'modified' );
		$labels   = self::statuses() + array( 'trash' => __( 'У кошику', 'leaderauto-core' ) );
		// Stored text is entity-encoded (kses, sanitize_*); integrations get plain text.
		$text = static function ( string $v ): string {
			return html_entity_decode( $v, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		};

		return array(
			'id'           => $post->ID,
			'created_at'   => $created ? $created->format( DATE_ATOM ) : '',
			'updated_at'   => $modified ? $modified->format( DATE_ATOM ) : '',
			'name'         => $text( (string) get_post_meta( $post->ID, '_la_name', true ) ),
			'phone'        => $text( (string) get_post_meta( $post->ID, '_la_phone', true ) ),
			'phone_digits' => (string) get_post_meta( $post->ID, '_la_phone_digits', true ),
			'car'          => $text( (string) get_post_meta( $post->ID, '_la_car', true ) ),
			'message'      => $text( $post->post_content ),
			'status'       => $post->post_status,
			'status_label' => $labels[ $post->post_status ] ?? $post->post_status,
			'note'         => $text( (string) get_post_meta( $post->ID, '_la_note', true ) ),
			'admin_url'    => admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
		);
	}
}
