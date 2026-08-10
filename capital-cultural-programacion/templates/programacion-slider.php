<?php
/**
 * Active exhibitions slider template.
 *
 * @package CapitalCulturalProgramacion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slider_id = 'ccp-slider-' . absint( $instance );
?>
<section id="<?php echo esc_attr( $slider_id ); ?>" class="ccp-programacion ccp-slider" data-ccp-slider>
	<div class="ccp-slider__header">
		<?php if ( '' !== $title ) : ?>
			<h2 class="ccp-slider__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<div class="ccp-slider__controls" aria-label="<?php esc_attr_e( 'Controles del slider', 'capital-cultural-programacion' ); ?>">
			<button class="ccp-slider__button" type="button" data-ccp-slider-prev aria-label="<?php esc_attr_e( 'Ver muestra anterior', 'capital-cultural-programacion' ); ?>">
				<span aria-hidden="true">‹</span>
			</button>
			<button class="ccp-slider__button" type="button" data-ccp-slider-next aria-label="<?php esc_attr_e( 'Ver muestra siguiente', 'capital-cultural-programacion' ); ?>">
				<span aria-hidden="true">›</span>
			</button>
		</div>
	</div>
	<div class="ccp-slider__viewport" data-ccp-slider-viewport tabindex="0" aria-label="<?php esc_attr_e( 'Muestras activas', 'capital-cultural-programacion' ); ?>">
		<div class="ccp-slider__track">
			<?php foreach ( $posts as $post ) : ?>
				<?php
				$modal_id = 'ccp-slider-modal-' . absint( $instance ) . '-' . absint( $post->ID );
				?>
				<div class="ccp-slider__slide">
					<?php include CCP_PLUGIN_DIR . 'templates/programacion-card.php'; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php foreach ( $posts as $post ) : ?>
		<?php
		$modal_id = 'ccp-slider-modal-' . absint( $instance ) . '-' . absint( $post->ID );
		include CCP_PLUGIN_DIR . 'templates/programacion-modal.php';
		?>
	<?php endforeach; ?>
</section>
