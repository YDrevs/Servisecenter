<?php
/**
 * «Контент сайту» admin menu: one screen per schema page, saved via the Settings API.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {

	const CAP  = 'edit_theme_options';
	const SLUG = 'leaderauto-content';

	public function hooks(): void {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
	}

	public function assets( string $hook ): void {
		if ( false === strpos( $hook, self::SLUG ) ) {
			return;
		}
		$main = LEADERAUTO_CORE_DIR . 'leaderauto-core.php';
		wp_enqueue_media();
		wp_enqueue_style( 'leaderauto-content', plugins_url( 'assets/admin/content.css', $main ), array(), LEADERAUTO_CORE_VERSION );
		wp_enqueue_script( 'leaderauto-content', plugins_url( 'assets/admin/content.js', $main ), array( 'jquery', 'jquery-ui-sortable' ), LEADERAUTO_CORE_VERSION, true );
	}

	public function menu(): void {
		$pages = schema();
		$first = (string) array_key_first( $pages );

		add_menu_page(
			__( 'Контент сайту', 'leaderauto-core' ),
			__( 'Контент сайту', 'leaderauto-core' ),
			self::CAP,
			self::SLUG . '-' . $first,
			'__return_null',
			'dashicons-edit-page',
			3
		);

		foreach ( $pages as $page => $def ) {
			add_submenu_page(
				self::SLUG . '-' . $first,
				$def['title'],
				$def['title'],
				self::CAP,
				self::SLUG . '-' . $page,
				function () use ( $page ) {
					$this->render( $page );
				}
			);
		}
	}

	public function register(): void {
		foreach ( schema() as $page => $def ) {
			$screen = self::SLUG . '-' . $page;

			register_setting(
				$screen,
				option_name( $page ),
				array(
					'type'              => 'array',
					'sanitize_callback' => function ( $input ) use ( $page ) {
						return $this->sanitize( $page, $input );
					},
					'default'           => array(),
				)
			);

			foreach ( $def['sections'] as $i => $section ) {
				$section_id = $screen . '-' . $i;
				add_settings_section( $section_id, $section['title'], '__return_false', $screen );

				foreach ( $section['fields'] as $key => $field ) {
					add_settings_field(
						$key,
						$field['label'],
						array( $this, 'field' ),
						$screen,
						$section_id,
						array(
							'page'      => $page,
							'key'       => $key,
							'field'     => $field,
							'label_for' => option_name( $page ) . '-' . $key,
						)
					);
				}
			}
		}
	}

	/**
	 * @param array{page:string,key:string,field:array<string,mixed>,label_for:string} $args
	 */
	public function field( array $args ): void {
		$field = $args['field'];
		$name  = option_name( $args['page'] ) . '[' . $args['key'] . ']';

		if ( 'repeater' === $field['type'] ) {
			$this->repeater( $name, $field, get_rows( $args['page'], $args['key'] ) );
		} else {
			$this->control( $field, $args['label_for'], $name, get( $args['page'], $args['key'] ), $field['default'] );
		}

		if ( ! empty( $field['help'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
		}
	}

	/**
	 * One input. For an image, $default is the bundled file shown while nothing is
	 * picked; inside a repeater it is '' (the row's own value carries any default path).
	 *
	 * @param array<string,mixed> $field
	 */
	private function control( array $field, string $id, string $name, string $value, string $default ): void {
		switch ( $field['type'] ) {
			case 'image':
			case 'video':
				$this->media_field( $field['type'], $id, $name, $value, $default );
				break;
			case 'checkbox':
				printf(
					'<input type="hidden" name="%2$s" value=""><input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s>',
					esc_attr( $id ),
					esc_attr( $name ),
					checked( '1', $value, false )
				);
				break;
			case 'textarea':
			case 'lines':
				printf(
					'<textarea id="%s" name="%s" rows="%d" class="large-text">%s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					'lines' === $field['type'] ? max( 4, substr_count( $value, "\n" ) + 2 ) : 4,
					esc_textarea( $value )
				);
				break;
			default:
				printf(
					'<input type="%s" id="%s" name="%s" value="%s" class="regular-text">',
					'url' === $field['type'] ? 'url' : 'text',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
		}
	}

	/**
	 * Hidden value input plus preview; content.js opens the media library.
	 * The value is an attachment ID, a path under the theme's assets/images/, or ''.
	 * With a $default, '' means that bundled file; without one, '' means no file.
	 *
	 * @param string $kind image|video — the library filter and the preview element.
	 */
	private function media_field( string $kind, string $id, string $name, string $value, string $default ): void {
		// Bundled images ship in the active theme's assets/images/.
		$base        = get_stylesheet_directory_uri() . '/assets/images/';
		$default_url = '' === $default ? '' : $base . $default;
		if ( ctype_digit( $value ) ) {
			$preview = (string) ( 'video' === $kind ? wp_get_attachment_url( (int) $value ) : wp_get_attachment_image_url( (int) $value, 'medium' ) );
		} else {
			$preview = '' === $value || ( $default && $value === $default ) ? $default_url : $base . $value;
		}
		$is_default = '' === $value || $value === $default;
		?>
		<div class="la-image" data-kind="<?php echo esc_attr( $kind ); ?>" data-default-url="<?php echo esc_url( $default_url ); ?>">
			<?php if ( 'video' === $kind ) : ?>
				<video class="la-image__preview" src="<?php echo esc_url( $preview ); ?>" controls preload="metadata"></video>
			<?php else : ?>
				<img class="la-image__preview" src="<?php echo esc_url( $preview ); ?>" alt="">
			<?php endif; ?>
			<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value === $default ? '' : $value ); ?>">
			<p>
				<button type="button" class="button la-image__pick"><?php 'video' === $kind ? esc_html_e( 'Обрати відео', 'leaderauto-core' ) : esc_html_e( 'Обрати зображення', 'leaderauto-core' ); ?></button>
				<button type="button" class="button-link la-image__reset"<?php echo $is_default ? ' hidden' : ''; ?>><?php '' === $default ? esc_html_e( 'Прибрати', 'leaderauto-core' ) : esc_html_e( 'Повернути початкове', 'leaderauto-core' ); ?></button>
			</p>
		</div>
		<?php
	}

	/**
	 * Rows as collapsible <details>, plus a blank row template for «Додати».
	 * Row indexes only group the inputs; sanitize() renumbers them in display order.
	 *
	 * @param array<string,mixed>             $field
	 * @param array<int,array<string,string>> $rows
	 */
	private function repeater( string $name, array $field, array $rows ): void {
		?>
		<div class="la-rows">
			<div class="la-rows__list">
				<?php
				foreach ( array_values( $rows ) as $i => $row ) {
					$this->row( $name . '[' . $i . ']', $field, $row );
				}
				?>
			</div>
			<script type="text/html" class="la-rows__tpl"><?php $this->row( $name . '[__i__]', $field, array(), true ); ?></script>
			<p><button type="button" class="button la-rows__add"><?php esc_html_e( '+ Додати', 'leaderauto-core' ); ?></button></p>
		</div>
		<?php
	}

	/**
	 * @param array<string,mixed>  $field
	 * @param array<string,string> $row
	 */
	private function row( string $name, array $field, array $row, bool $open = false ): void {
		$title = $row[ $field['title'] ] ?? '';
		?>
		<details class="la-row"<?php echo $open ? ' open' : ''; ?>>
			<summary class="la-row__head">
				<span class="la-row__handle dashicons dashicons-move" title="<?php esc_attr_e( 'Перетягніть, щоб змінити порядок', 'leaderauto-core' ); ?>"></span>
				<span class="la-row__title" data-placeholder="<?php esc_attr_e( 'Новий елемент', 'leaderauto-core' ); ?>"><?php echo esc_html( '' === $title ? __( 'Новий елемент', 'leaderauto-core' ) : $title ); ?></span>
				<button type="button" class="button-link button-link-delete la-row__remove"><?php esc_html_e( 'Видалити', 'leaderauto-core' ); ?></button>
			</summary>
			<div class="la-row__body">
				<?php foreach ( $field['subfields'] as $sub_key => $sub ) : ?>
					<?php $id = sanitize_key( str_replace( array( '[', ']' ), '-', $name ) ) . $sub_key; ?>
					<div class="la-row__field<?php echo $sub_key === $field['title'] ? ' la-row__field--title' : ''; ?>">
						<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $sub['label'] ); ?></label>
						<?php $this->control( $sub, $id, $name . '[' . $sub_key . ']', (string) ( $row[ $sub_key ] ?? '' ), '' ); ?>
						<?php if ( ! empty( $sub['help'] ) ) : ?>
							<p class="description"><?php echo esc_html( $sub['help'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</details>
		<?php
	}

	/**
	 * @param mixed $input
	 * @return array<string,mixed>
	 */
	public function sanitize( string $page, $input ): array {
		$input = is_array( $input ) ? $input : array();
		$out   = array();

		foreach ( fields( $page ) as $key => $field ) {
			if ( 'repeater' !== $field['type'] ) {
				$out[ $key ] = $this->clean( $field['type'], $input[ $key ] ?? '' );
				continue;
			}

			$out[ $key ] = array();
			foreach ( is_array( $input[ $key ] ?? null ) ? $input[ $key ] : array() as $raw_row ) {
				$row = array();
				foreach ( $field['subfields'] as $sub_key => $sub ) {
					$row[ $sub_key ] = $this->clean( $sub['type'], is_array( $raw_row ) ? ( $raw_row[ $sub_key ] ?? '' ) : '' );
				}
				// A row left completely blank is dropped rather than rendered empty.
				if ( implode( '', $row ) !== '' ) {
					$out[ $key ][] = $row;
				}
			}
		}

		return $out;
	}

	/**
	 * @param mixed $raw
	 */
	private function clean( string $type, $raw ): string {
		$raw = is_scalar( $raw ) ? (string) wp_unslash( $raw ) : '';

		switch ( $type ) {
			case 'url':
				return esc_url_raw( trim( $raw ) );
			case 'image':
			case 'video':
				if ( ctype_digit( $raw ) ) {
					$ok = 'image' === $type
						? wp_attachment_is_image( (int) $raw )
						: str_starts_with( (string) get_post_mime_type( (int) $raw ), 'video/' );
					return $ok ? $raw : '';
				}
				// A bundled default carried over from the shipped rows: a plain relative path
				// (spaces allowed — the about page's photos live in "About page images/").
				return preg_match( '#^[A-Za-z0-9_-][A-Za-z0-9_./ -]*$#', $raw ) && false === strpos( $raw, '..' ) ? $raw : '';
			case 'checkbox':
				return '1' === $raw ? '1' : '';
			case 'textarea':
			case 'lines':
				return sanitize_textarea_field( $raw );
			default:
				return sanitize_text_field( $raw );
		}
	}

	public function render( string $page ): void {
		$screen = self::SLUG . '-' . $page;
		?>
		<div class="wrap">
			<h1><?php echo esc_html( __( 'Контент сайту', 'leaderauto-core' ) . ' — ' . schema()[ $page ]['title'] ); ?></h1>
			<p><?php esc_html_e( 'Зміни з’являються на сайті одразу після збереження. Якщо поле залишити порожнім, повернеться початкове значення.', 'leaderauto-core' ); ?></p>
			<form action="options.php" method="post">
				<?php
				settings_fields( $screen );
				do_settings_sections( $screen );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
