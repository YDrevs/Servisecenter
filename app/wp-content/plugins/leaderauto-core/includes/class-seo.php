<?php
/**
 * Search and share metadata: per-page title/description (Контент сайту → SEO),
 * Open Graph, and keeping WordPress' blog leftovers (authors, categories, feeds)
 * out of the index — the site is five pages, not a blog.
 *
 * @package LeaderAuto\Core
 */

namespace LeaderAuto\Core;

use function LeaderAuto\Core\Content\get as content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Seo {

	/** Pages with their own SEO fields, by slug (see the theme's inc/activation.php). */
	private const PAGES = array( 'parts', 'dealer', 'about', 'contacts' );

	/** The old Divi site's plain `?page_id=` URLs → today's slugs. */
	private const LEGACY_IDS = array( 10 => '', 15 => 'dealer', 17 => 'about', 13 => 'contacts' );

	public function hooks(): void {
		add_filter( 'pre_get_document_title', array( $this, 'title' ) );
		add_action( 'wp_head', array( $this, 'meta' ), 1 );
		add_filter( 'wp_robots', array( $this, 'robots' ) );
		add_action( 'template_redirect', array( $this, 'redirects' ) );

		add_filter( 'wp_sitemaps_add_provider', static function ( $provider, string $name ) {
			return in_array( $name, array( 'users', 'taxonomies' ), true ) ? false : $provider;
		}, 10, 2 );
		add_filter( 'wp_sitemaps_post_types', static function ( array $types ): array {
			return array_intersect_key( $types, array( 'page' => true ) );
		} );

		add_filter( 'rest_endpoints', array( $this, 'hide_users' ) );

		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
	}

	/**
	 * SEO key of the current request: 'home', a slug from PAGES, or '' for anything else.
	 */
	private function page_key(): string {
		if ( is_front_page() ) {
			return 'home';
		}
		$post = is_page() ? get_queried_object() : null;
		return $post instanceof \WP_Post && in_array( $post->post_name, self::PAGES, true ) ? $post->post_name : '';
	}

	public function title( string $title ): string {
		$key = $this->page_key();
		return '' === $key ? $title : content( 'seo', $key . '_title' );
	}

	public function meta(): void {
		$key = $this->page_key();
		if ( '' === $key ) {
			return;
		}

		$description = content( 'seo', $key . '_description' );
		$url         = 'home' === $key ? home_url( '/' ) : (string) wp_get_canonical_url();
		$image       = $this->og_image();

		$tags = array(
			'og:type'        => 'website',
			'og:locale'      => 'uk_UA',
			'og:site_name'   => get_bloginfo( 'name' ),
			'og:title'       => content( 'seo', $key . '_title' ),
			'og:description' => $description,
			'og:url'         => $url,
			'og:image'       => $image['url'],
		);
		if ( $image['width'] ) {
			$tags['og:image:width']  = (string) $image['width'];
			$tags['og:image:height'] = (string) $image['height'];
		}

		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		foreach ( $tags as $property => $value ) {
			if ( '' !== $value ) {
				printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $value ) );
			}
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}

	/**
	 * The share image: an attachment picked in wp-admin, or the theme's bundled default.
	 *
	 * @return array{url:string,width:int,height:int}
	 */
	private function og_image(): array {
		$value = content( 'seo', 'og_image' );
		if ( ctype_digit( $value ) ) {
			$src = wp_get_attachment_image_src( (int) $value, 'full' );
			if ( $src ) {
				return array( 'url' => $src[0], 'width' => (int) $src[1], 'height' => (int) $src[2] );
			}
			$value = '';
		}
		if ( '' === $value ) {
			return array( 'url' => '', 'width' => 0, 'height' => 0 );
		}

		$url  = get_theme_file_uri( 'assets/images/' . ltrim( $value, '/' ) );
		$size = wp_getimagesize( get_theme_file_path( 'assets/images/' . ltrim( $value, '/' ) ) );
		return array( 'url' => $url, 'width' => $size ? (int) $size[0] : 0, 'height' => $size ? (int) $size[1] : 0 );
	}

	/**
	 * Archives (author, category, date…), attachments and 404s are not content here.
	 *
	 * @param array<string,bool|string> $robots
	 * @return array<string,bool|string>
	 */
	public function robots( array $robots ): array {
		if ( is_archive() || is_attachment() || is_404() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}

	public function redirects(): void {
		// Author archives only expose the admin login.
		if ( is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}

		// Old inbound links to the Divi site's ?page_id= URLs. Only when nothing answers
		// that ID here — locally the reference DB still holds those old pages.
		$id = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( is_404() && isset( self::LEGACY_IDS[ $id ] ) ) {
			$slug = self::LEGACY_IDS[ $id ];
			wp_safe_redirect( home_url( '' === $slug ? '/' : '/' . $slug . '/' ), 301 );
			exit;
		}
	}

	/**
	 * Drop the public user list (it leaks the admin login). Editors keep it — the
	 * block editor's author picker reads these routes.
	 *
	 * @param array<string,mixed> $endpoints
	 * @return array<string,mixed>
	 */
	public function hide_users( array $endpoints ): array {
		if ( current_user_can( 'list_users' ) ) {
			return $endpoints;
		}
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		return $endpoints;
	}
}
