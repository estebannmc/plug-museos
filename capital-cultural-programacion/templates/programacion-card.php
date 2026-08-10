<?php
/**
 * Programming card template.
 *
 * @package CapitalCulturalProgramacion
 */

use CapitalCultural\Programacion\CCP_Date_Formatter;
use CapitalCultural\Programacion\CCP_Status;
use CapitalCultural\Programacion\CCP_Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id       = $post->ID;
$status        = CCP_Status::get_status( $post_id );
$category      = CCP_Taxonomies::get_first_term( $post_id, CCP_Taxonomies::TAX_CATEGORIA );
$date_text     = CCP_Date_Formatter::format_range(
	(string) get_post_meta( $post_id, '_ccp_fecha_inicio', true ),
	(string) get_post_meta( $post_id, '_ccp_fecha_fin', true )
);
$excerpt       = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 24 );
$category_name = $category ? $category->name : '';
$category_tag  = $category_name && function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $category_name, 'UTF-8' ) : strtoupper( $category_name );
$thumbnail     = get_the_post_thumbnail(
	$post_id,
	'medium_large',
	array(
		'class'    => 'ccp-card__image',
		'loading'  => 'lazy',
		'decoding' => 'async',
	)
);
?>
<article class="ccp-card-wrap">
	<button class="ccp-card" type="button" data-ccp-modal-target="<?php echo esc_attr( $modal_id ); ?>" aria-haspopup="dialog">
		<span class="ccp-card__media">
			<?php if ( $thumbnail ) : ?>
				<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<span class="ccp-card__placeholder" role="img" aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">
					<span><?php esc_html_e( 'Capital Cultural', 'capital-cultural-programacion' ); ?></span>
				</span>
			<?php endif; ?>
		</span>
		<span class="ccp-card__content">
			<span class="ccp-card__tags">
				<?php if ( $category ) : ?>
					<span class="ccp-tag ccp-tag--categoria"><?php echo esc_html( $category_tag ); ?></span>
				<?php endif; ?>
				<span class="ccp-tag ccp-tag--estado ccp-tag--<?php echo esc_attr( $status['key'] ); ?>"><?php echo esc_html( $status['label'] ); ?></span>
			</span>
			<span class="ccp-card__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
			<?php if ( $date_text ) : ?>
				<span class="ccp-card__date"><?php echo esc_html( $date_text ); ?></span>
			<?php endif; ?>
			<?php if ( $show_excerpt && $excerpt ) : ?>
				<span class="ccp-card__excerpt"><?php echo esc_html( $excerpt ); ?></span>
			<?php endif; ?>
			<span class="ccp-card__action"><?php esc_html_e( 'Ver propuesta', 'capital-cultural-programacion' ); ?></span>
		</span>
	</button>
</article>
