<?php
/**
 * @package LeaderAuto
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Each card's services are one per line in wp-admin; a line starting with "-"
 * joins a nested list under the line before it, which becomes its label.
 */
$groups = array();
foreach ( leaderauto_rows( 'home', 'services_groups' ) as $row ) {
	$items = array();
	foreach ( preg_split( '/\R/u', $row['items'] ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		if ( str_starts_with( $line, '-' ) && $items ) {
			$last = array_key_last( $items );
			if ( ! is_array( $items[ $last ] ) ) {
				$items[ $last ] = array( 'label' => $items[ $last ], 'items' => array() );
			}
			$items[ $last ]['items'][] = trim( substr( $line, 1 ) );
		} else {
			$items[] = $line;
		}
	}
	$groups[] = array(
		'title' => $row['title'],
		'image' => leaderauto_media_url( $row['image'] ),
		'items' => $items,
	);
}
?>
<section class="section" id="services">
	<div class="section__inner">
		<?php /* Reference row 1: two stills on the left, the debut clip on the right. */ ?>
		<div class="services__intro">
			<img class="services__shot" src="<?php echo esc_url( leaderauto_field_img( 'home', 'services_shot' ) ); ?>" alt="<?php esc_attr_e( 'Сервіс електромобілів LeaderAuto', 'leaderauto' ); ?>" loading="lazy">
			<span class="services__shot-over"><img src="<?php echo esc_url( leaderauto_field_img( 'home', 'services_shot_over' ) ); ?>" alt="<?php esc_attr_e( 'Електромобіль BYD', 'leaderauto' ); ?>" width="1230" height="615" loading="lazy"></span>
			<div class="services__aside">
				<?php /* Portrait 720x1280 clip — no poster, the first frame stands in. */ ?>
				<video class="video video--portrait" controls preload="metadata">
					<source src="<?php echo esc_url( leaderauto_img( 'r2d2.mp4' ) ); ?>" type="video/mp4">
				</video>
			</div>
		</div>

		<h2 class="section__title section__title--center"><?php echo esc_html( leaderauto_field( 'home', 'services_title' ) ); ?></h2>
		<div class="cards cards--4 cards--wide">
			<?php foreach ( $groups as $group ) : ?>
				<article class="card">
					<?php if ( $group['image'] ) : ?>
						<img class="card__media" src="<?php echo esc_url( $group['image'] ); ?>" alt="<?php echo esc_attr( $group['title'] ); ?>" loading="lazy">
					<?php endif; ?>
					<h3 class="card__title"><?php echo esc_html( $group['title'] ); ?></h3>
					<ul class="card__list">
						<?php foreach ( $group['items'] as $item ) : ?>
							<?php if ( is_array( $item ) ) : ?>
								<?php /* The label heads the nested list; only the services under it link. */ ?>
								<li>
									<?php echo esc_html( $item['label'] ); ?>
									<ul class="card__sublist">
										<?php foreach ( $item['items'] as $sub ) : ?>
											<li><?php leaderauto_service_link( $sub ); ?></li>
										<?php endforeach; ?>
									</ul>
								</li>
							<?php else : ?>
								<li><?php leaderauto_service_link( $item ); ?></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</article>
			<?php endforeach; ?>
		</div>
	</div>

	<?php /* Opened by assets/src/js/service-modal.js; the links work without it. */ ?>
	<dialog class="service-modal" id="service-modal" aria-labelledby="service-modal-title">
		<form method="dialog" class="service-modal__dismiss">
			<button class="service-modal__close" value="close" aria-label="<?php esc_attr_e( 'Закрити', 'leaderauto' ); ?>">&times;</button>
		</form>
		<h2 class="service-modal__title" id="service-modal-title"><?php esc_html_e( 'Залишити заявку', 'leaderauto' ); ?></h2>
		<?php get_template_part( 'template-parts/contact-form', null, array( 'id' => 'leaderauto-contact-form-modal' ) ); ?>
	</dialog>
</section>
