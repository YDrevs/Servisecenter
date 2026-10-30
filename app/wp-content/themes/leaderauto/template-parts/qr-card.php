<?php
/**
 * A QR code for a link, in the black-framed card. The code is drawn by
 * assets/src/js/qr.js; the card is also a plain link, for phones that can't
 * scan their own screen.
 *
 * Args: `url` (required), `class` (extra classes), `label` (false drops the «скануй» pill).
 *
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $args['url'] ) ) {
	return;
}
?>
<a class="qr-card <?php echo esc_attr( $args['class'] ?? '' ); ?>" href="<?php echo esc_url( $args['url'] ); ?>" target="_blank" rel="noopener"
   aria-label="<?php esc_attr_e( 'QR-код: скануй або натисни, щоб перейти', 'leaderauto' ); ?>">
	<span class="qr-card__code" data-qr="<?php echo esc_url( $args['url'] ); ?>"></span>
	<?php if ( $args['label'] ?? true ) : ?>
		<span class="qr-card__label">
			<svg class="qr-card__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="12" fill="#fff"/><rect x="8" y="4.5" width="8" height="15" rx="1.2" fill="#000"/><rect x="9" y="7" width="6" height="9.5" fill="#fff"/><rect x="10.5" y="5.4" width="3" height="0.8" rx="0.4" fill="#fff"/><circle cx="12" cy="18" r="0.75" fill="#fff"/></svg>
			<?php esc_html_e( 'скануй', 'leaderauto' ); ?>
		</span>
	<?php endif; ?>
</a>
