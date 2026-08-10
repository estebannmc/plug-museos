<?php
/**
 * Programming grid template.
 *
 * @package CapitalCulturalProgramacion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$grid_id = 'ccp-grid-' . absint( $instance );
?>
<div id="<?php echo esc_attr( $grid_id ); ?>" class="ccp-programacion" data-ccp-instance="<?php echo esc_attr( (string) $instance ); ?>">
	<div class="ccp-grid ccp-grid--columns-<?php echo esc_attr( (string) $columns ); ?>" style="--ccp-columns: <?php echo esc_attr( (string) $columns ); ?>;">
		<?php foreach ( $posts as $post ) : ?>
			<?php
			$modal_id = 'ccp-modal-' . absint( $instance ) . '-' . absint( $post->ID );
			include CCP_PLUGIN_DIR . 'templates/programacion-card.php';
			?>
		<?php endforeach; ?>
	</div>
	<?php foreach ( $posts as $post ) : ?>
		<?php
		$modal_id = 'ccp-modal-' . absint( $instance ) . '-' . absint( $post->ID );
		include CCP_PLUGIN_DIR . 'templates/programacion-modal.php';
		?>
	<?php endforeach; ?>
</div>
