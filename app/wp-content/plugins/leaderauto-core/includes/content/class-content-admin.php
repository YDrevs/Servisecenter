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
		wp_enqueue_script( 'leaderauto-content', plugins_url( 'assets/admin/content.js', $main ), array( 'jquery' ), LEADERAUTO_CORE_VERSION, true );
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
	 * @param array{page:string,key:string,field:array<string,string>,label_for:string} $args
	 */
	public function field( array $args ): void {
		$field = $args['field'];
		$name  = option_name( $args['page'] ) . '[' . $args['key'] . ']';
		$value = get( $args['page'], $args['key'] );

		if ( 'image' === $field['type'] ) {
			$this->image_field( $args['label_for'], $name, $value, $field['default'] );
		} elseif ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea id="%s" name="%s" rows="4" class="large-text">%s</textarea>',
				esc_attr( $args['label_for'] ),
				esc_attr( $name ),
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input type="%s" id="%s" name="%s" value="%s" class="regular-text">',
				'url' === $field['type'] ? 'url' : 'text',
				esc_attr( $args['label_for'] ),
				esc_attr( $name ),
				esc_attr( $value )
			);
		}

		if ( ! empty( $field['help'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
		}
	}

	/**
	 * Hidden attachment-ID input plus preview; content.js opens the media library.
	 * An empty input means the theme's bundled default image.
	 */
	private function image_field( string $id, string $name, string $value, string $default ): void {
		// Defaults ship in the active theme's assets/images/.
		$default_url = get_stylesheet_directory_uri() . '/assets/images/' . $default;
		$saved       = ctype_digit( $value ) ? $value : '';
		$preview     = $saved ? wp_get_attachment_image_url( (int) $saved, 'medium' ) : $default_url;
		?>
		<div class="la-image" data-default-url="<?php echo esc_url( $default_url ); ?>">
			<img class="la-image__preview" src="<?php echo esc_url( (string) $preview ); ?>" alt="">
			<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $saved ); ?>">
			<p>
				<button type="button" class="button la-image__pick"><?php esc_html_e( 'Обрати зображення', 'leaderauto-core' ); ?></button>
				<button type="button" class="button-link la-image__reset"<?php echo $saved ? '' : ' hidden'; ?>><?php esc_html_e( 'Повернути початкове', 'leaderauto-core' ); ?></button>
			</p>
		</div>
		<?php
	}

	/**
	 * @param mixed $input
	 * @return array<string,string>
	 */
	public function sanitize( string $page, $input ): array {
		$input = is_array( $input ) ? $input : array();
		$out   = array();

		foreach ( fields( $page ) as $key => $field ) {
			$raw = isset( $input[ $key ] ) ? (string) wp_unslash( $input[ $key ] ) : '';

			switch ( $field['type'] ) {
				case 'url':
					$out[ $key ] = esc_url_raw( trim( $raw ) );
					break;
				case 'image':
					$out[ $key ] = wp_attachment_is_image( absint( $raw ) ) ? (string) absint( $raw ) : '';
					break;
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( $raw );
					break;
				default:
					$out[ $key ] = sanitize_text_field( $raw );
			}
		}

		return $out;
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
