<?php
/**
 * Programming modal template.
 *
 * @package CapitalCulturalProgramacion
 */

use CapitalCultural\Programacion\CCP_Date_Formatter;
use CapitalCultural\Programacion\CCP_Meta_Boxes;
use CapitalCultural\Programacion\CCP_Status;
use CapitalCultural\Programacion\CCP_Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id      = $post->ID;
$status       = CCP_Status::get_status( $post_id );
$category     = CCP_Taxonomies::get_first_term( $post_id, CCP_Taxonomies::TAX_CATEGORIA );
$date_text    = CCP_Date_Formatter::format_range(
	(string) get_post_meta( $post_id, '_ccp_fecha_inicio', true ),
	(string) get_post_meta( $post_id, '_ccp_fecha_fin', true )
);
$horarios     = (string) get_post_meta( $post_id, '_ccp_horarios', true );
$ubicacion    = (string) get_post_meta( $post_id, '_ccp_ubicacion', true );
$external_url = (string) get_post_meta( $post_id, '_ccp_enlace', true );
$button_text  = (string) get_post_meta( $post_id, '_ccp_texto_boton', true );
$title_id     = $modal_id . '-title';
$content      = apply_filters( 'the_content', $post->post_content );
$category_name = $category ? $category->name : '';
$category_tag = $category_name && function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $category_name, 'UTF-8' ) : strtoupper( $category_name );
$image_style   = CCP_Meta_Boxes::image_position_style( $post_id );
$image        = get_the_post_thumbnail(
	$post_id,
	'full',
	array(
		'class'    => 'ccp-modal__image',
		'style'    => $image_style,
		'sizes'    => '(max-width: 900px) calc(100vw - 24px), 44vw',
		'loading'  => 'lazy',
		'decoding' => 'async',
	)
);
?>
<div id="<?php echo esc_attr( $modal_id ); ?>" class="ccp-modal" hidden>
	<div class="ccp-modal__overlay" data-ccp-close></div>
	<div class="ccp-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr( $title_id ); ?>" tabindex="-1">
		<button class="ccp-modal__close" type="button" aria-label="<?php esc_attr_e( 'Cerrar', 'capital-cultural-programacion' ); ?>" data-ccp-close>
			<span aria-hidden="true">&times;</span>
		</button>
		<div class="ccp-modal__media">
			<?php if ( $image ) : ?>
				<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<div class="ccp-modal__placeholder" role="img" aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" style="<?php echo esc_attr( $image_style ); ?>">
					<span><?php esc_html_e( 'Capital Cultural', 'capital-cultural-programacion' ); ?></span>
				</div>
			<?php endif; ?>
		</div>
		<div class="ccp-modal__body">
			<div class="ccp-modal__tags">
				<?php if ( $category ) : ?>
					<span class="ccp-tag ccp-tag--categoria"><?php echo esc_html( $category_tag ); ?></span>
				<?php endif; ?>
				<span class="ccp-tag ccp-tag--estado ccp-tag--<?php echo esc_attr( $status['key'] ); ?>"><?php echo esc_html( $status['label'] ); ?></span>
			</div>
			<h2 id="<?php echo esc_attr( $title_id ); ?>" class="ccp-modal__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
			<?php if ( $date_text ) : ?>
				<p class="ccp-modal__date"><?php echo esc_html( $date_text ); ?></p>
			<?php endif; ?>
			<?php if ( $horarios || $ubicacion ) : ?>
				<dl class="ccp-modal__details">
					<?php if ( $horarios ) : ?>
						<div>
							<dt><?php esc_html_e( 'Horarios', 'capital-cultural-programacion' ); ?></dt>
							<dd><?php echo esc_html( $horarios ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $ubicacion ) : ?>
						<div>
							<dt><?php esc_html_e( 'Sala o ubicación', 'capital-cultural-programacion' ); ?></dt>
							<dd><?php echo esc_html( $ubicacion ); ?></dd>
						</div>
					<?php endif; ?>
				</dl>
			<?php endif; ?>
			<div class="ccp-modal__content">
				<?php echo wp_kses_post( $content ); ?>
			</div>
			<?php if ( $external_url ) : ?>
				<a class="ccp-modal__button" href="<?php echo esc_url( $external_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html( $button_text ? $button_text : __( 'Más información', 'capital-cultural-programacion' ) ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
