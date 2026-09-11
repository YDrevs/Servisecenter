<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Перейти до вмісту', 'leaderauto' ); ?></a>

<header class="site-header">
	<div class="site-header__inner">
		<div class="site-header__brand">
			<?php if ( is_front_page() ) : ?>
				<?php /* Placeholder standing in for the ЛІДЕР-АВТО mark until the client's BYD-Center logo lands. Home only for now. */ ?>
				<a class="site-header__home site-header__wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">BYD-Center</a>
			<?php elseif ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<?php /* Ships with the theme, so a fresh install shows the real mark without a Customizer step. */ ?>
				<a class="site-header__home" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img src="<?php echo esc_url( leaderauto_img( 'logo.png' ) ); ?>"
					     alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					     width="1096" height="357">
				</a>
			<?php endif; ?>
		</div>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Головне меню', 'leaderauto' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 1,
				) );
			} else {
				leaderauto_primary_fallback();
			}
			?>
		</nav>

		<a class="site-header__phone" href="<?php echo esc_attr( leaderauto_phone_href() ); ?>">
			<?php echo esc_html( leaderauto_phone() ); ?>
		</a>

		<button class="site-nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav-mobile">
			<span class="site-nav-toggle__bar"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Меню', 'leaderauto' ); ?></span>
		</button>
	</div>
</header>

<main id="main" class="site-main">
