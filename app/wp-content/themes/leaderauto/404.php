<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="page-shell">
	<div class="page-shell__inner page-shell__body">
		<h1 class="page-shell__title"><?php esc_html_e( 'Сторінку не знайдено', 'leaderauto' ); ?></h1>
		<p><?php esc_html_e( 'Схоже, такої сторінки немає. Повернутися на головну:', 'leaderauto' ); ?></p>
		<p><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'На головну', 'leaderauto' ); ?></a></p>
	</div>
</div>
<?php
get_footer();
